<?php
// return a 6 character code that uniquely identifies the string
function hash6($str) {
	return substr(hash("md5", $str), -6);
}

// change the format by which a date is represented
function rearrange_date($date, $fmt1, $fmt2) {
	// convert current format to regex
	$fmt1 = preg_quote($fmt1, "/");
	$fmt1 = '/' . str_replace("m", "(?P<m>\d{1,2})", str_replace("y", "(?P<y>\d{1,4})", str_replace("d", "(?P<d>\d{1,2})", $fmt1))) . '/';
	if (preg_match($fmt1, $date, $matches)) {
		return str_replace("d", $matches["d"], str_replace("m", $matches["m"], str_replace("y", $matches["y"], $fmt2)));
	} else {
		return $date;
	}
}

// convert date format from mysql format to gui format
function rearrange_sql_date($date) {
	if ($date != NULL) {
		return rearrange_date($date, "y-m-d", "m/d/y");
	} else {
		return "Unknown";
	}
}

// convert date format from gui format to mysql format
function rearrange_gui_date($date) {
	if ($date != NULL) {
		return rearrange_date($date, "m/d/y", "y-m-d");
	} else {
		return NULL;
	}
}

// format a list of labs
function formatted_labs($labs) {
	$lab_strs = array();
	foreach ($labs as $row) {
		$lab_strs[] = $row['room'] . ' ' . $row['building'];
	}
	return join(', ', $lab_strs);
}

// check if any labs are located in the query building
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

// replace parameter in the associative array
function with_param($a, $k, $v) {
	$a2 = $a;
	$a2[$k] = $v;
	return $a2;
}

// remove keys whose values is equal to empty string in associative array
function remove_empty($a, $token = "") {
	$a2 = $a;
	foreach ($a as $k => $v) {
		if ($v == $token) {
			unset($a2[$k]);
		}
	}
	return $a2;
}
?>