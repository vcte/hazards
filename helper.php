<?php
/** 
 * Return a 6 character code that uniquely identifies the string.
 * @param string $str arbitrary string, or object that can be converted to string
 * @retval string 6 character md5 hash. 
 */
function hash6($str) {
	return substr(hash("md5", $str), -6);
}

/** 
 * Change the format by which a date is represented.
 * Format is a string in which year, month and day are matched to "y", "m" and "d" respectively. Ex: "m/d/y", "y-m-d". 
 * @param string $date date in format $fmt1.
 * @param string $fmt1 date format. 
 * @param string $fmt2 date format. 
 * @retval string date in format $fmt2, or $date if does not match $fmt1. 
 */
function rearrange_date($date, $fmt1, $fmt2) {
	// convert current format to regex
	$fmt1 = preg_quote($fmt1, "/");
	$fmt1 = str_replace("d", "(?P<d>\d{1,2})", $fmt1);
	$fmt1 = str_replace("y", "(?P<y>\d{1,4})", $fmt1);
	$fmt1 = str_replace("m", "(?P<m>\d{1,2})", $fmt1);
	$fmt1 = '/' . $fmt1 . '/';
	
	// match date to format $fmt1
	if (preg_match($fmt1, $date, $matches)) {
		return str_replace("d", $matches["d"], str_replace("m", $matches["m"], str_replace("y", $matches["y"], $fmt2)));
	} else {
		return $date;
	}
}

/** Convert date format from mysql format to gui format.
 * @param string $date in "y-m-d" format.
 * @retval string date in "m/d/y" format, or "Unknown" if given date is NULL.
 */
function rearrange_sql_date($date) {
	if ($date != NULL) {
		return rearrange_date($date, "y-m-d", "m/d/y");
	} else {
		return "Unknown";
	}
}

/** Convert date format from gui format to mysql format.
 * @param string $date in "m/d/y" format.
 * @retval string date in "y-m-d" format, or NULL if given date is NULL;
 */
function rearrange_gui_date($date) {
	if ($date != NULL) {
		return rearrange_date($date, "m/d/y", "y-m-d");
	} else {
		return NULL;
	}
}

/** Format a list of labs
 * @param $labs 2D array, or list of  (lab room, lab building) pairs.
 * @retval string comma-separated sequence of lab locations. 
 */
function formatted_labs($labs) {
	$lab_strs = array();
	foreach ($labs as $row) {
		$lab_strs[] = $row['room'] . ' ' . $row['building'];
	}
	return join(', ', $lab_strs);
}

/** Check if any labs are located in the query building.
 * @param $labs 2D array, or list of  (lab room, lab building) pairs.
 * @param string $bldg_restriction building name to match against, or NULL for all buildings.
 * @retval boolean whether any labs in the list are in the building given by $bldg_restriction
 */
function any_building_matches_restriction($labs, $bldg_restriction) {
   if ($bldg_restriction != NULL) {
      foreach ($labs as $lab) {
         if ($lab['building'] == $bldg_restriction) {
            return True;
         }
      }
      return False;
   }
   return True;
}

/** Replace parameter in the associative array.
 * @param $a associative array
 * @param $k key
 * @param $v new value
 * $retun updated associative array
 */
function with_param($a, $k, $v) {
	$a2 = $a;
	$a2[$k] = $v;
	return $a2;
}

/** Remove keys whose value is equal to $token in associative array
 * @param $a associative array
 * @param string $token value to match against, equal to empty string by default
 * @return updated associative array
 */
function remove_empty($a, $token = "") {
	$a2 = $a;
	foreach ($a as $k => $v) {
		if ($v == $token) {
			unset($a2[$k]);
		}
	}
	return $a2;
}

/** Print out all issues as js array named keys, and colors associated with each issue. 
 * Determines which color depicts which issue based on frequency and hard-coded mapping for common issues. 
 * @param $conn database connection object. 
 * @return tuple w/ total number of issues, list of issues, and list of colors
 */
function echo_keys_and_colors($conn) {
    // print out javascript array literal for all issues, sorted by frequency
    $all_issues = get_top_issues($conn);
    $keys = array();
    foreach ($all_issues as $row) {
        $issue = $row[0];
        $keys[] = '"' . $issue . '"';
    }
    echo '  var keys = [' . join(', ', $keys) . '];' . PHP_EOL;
    
    // print out colors for all issues
    // use highly saturated colors for most frequent issues
    $color_for_frequent_issues = array(
        "Electrical" => "#ffd700",              // gold
        "Emergency Equipment" => "#8b0000",     // darkred
        "Machine Guarding" => "#808080",        // gray
        "High Hazard Materials" => "#ff8c00",    // darkorange
        "Lab Safety Plan" => "#00008b",         // darkblue
        "Chemical Storage" => "#006400",        // darkgreen
        "Compressed Gases" => "#fa8072",        // salmon
        "Walking-Working Surfaces" => "#8b4513",// saddlebrown
        "Ladders" => "#9400d3"
    );

    // use lighter, less saturated colors for less frequent issues
    $color_for_uncommon_issues = array(
        "PPE" => "#d3d3d3",                     // lightgrey
        "Falling Hazard" => "#afeeee",          // paleturquoise
        "Housekeeping" => "#eee8aa",            // palegoldenrod
        "Training" => "#da70d6",                // orchid
        "Waste Mgt." => "#deb887",              // burlywood
        "Cranes" => "#90ee90"                   // lightgreen
    );
    
    // use saturated or neon colors for unknown issues
    $backup_colors_for_unknown_issues = array(
        "#FF00FF",                              // fuschia
        "#00FF00",                              // lime
        "#008B8B",                              // darkcyan
        "#800000",                              // maroon
        "#7FFFD4",                              // aquamarine
        "#4B0082",                              // indigo
        "#556B2F",                              // darkolivegreen
        "#F0E68C",                              // khaki
        "#C71585",                              // mediumvioletred
        "#B0C4DE",                              // lightsteelblue
    );
    $final_backup_color = "#7FFF00";            // chartreuse
    
    // use black if issue appears less than 1% of the time in the hazards table
    $color_for_rare_issues = "#000000";         // black
    
    // determine total number of hazards
    $total_num = 0;
    foreach ($all_issues as $row) {
        $total_num += $row[1];
    }
    
    // determine which color to use for representing each issue
    $colors = array();
    for ($i = $j = 0; $i < count($all_issues); $i++) {
        $issue = $all_issues[$i][0];
        $freq  = $all_issues[$i][1];
        $color = $color_for_rare_issues;
        if (array_key_exists($issue, $color_for_frequent_issues)) {
            $color = $color_for_frequent_issues[$issue];
        } else if (array_key_exists($issue, $color_for_uncommon_issues)) {
            $color = $color_for_uncommon_issues[$issue];
        } else if (freq >= $total_num / 100) {
            if ($j < count($backup_colors_for_unknown_issues)) {
                $color = $backup_colors_for_unknown_issues[$j];
                $j++;
            } else {
                $color = $final_backup_color;
            }
        } else {
            $color = $color_for_rare_issues;
        }
        $colors[] = '"' . $color . '"';
    }
    
    echo '  var cols = [' . join(', ', $colors) . '];' . PHP_EOL;
    echo '  var z = d3.scaleOrdinal().domain(keys).range(cols);' . PHP_EOL;
    
    return array($total_num, $keys, $colors);
}

?>