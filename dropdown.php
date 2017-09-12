<?php
/**
 * Output html code for a department drop-down menu. 
 * @param $conn database connection object
 * @param string $role access level of the user, can be safety_manager_or_dean | facility_manager | lab_pi
 * @param string $dept Abbreviated name of department that the user belongs to, or NULL to output all departments in the drop-down menu. 
 * @retval string Abbreviated name of department selected by user, or "" if "all" option is selected. 
 */
function echo_dept_menu($conn, $role, $dept) {
    echo '<div class="fleft">';
    echo '<label for="department">Select Department</label><br />';
    echo '<select id="department" name="department" onchange="this.form.submit();" autocomplete="off">';
    
    // restrict visible departments if user is not safety manager / dean
    $dept_param = isset($_GET['department']) ? $_GET['department'] : '';
    if ($role != "safety_manager_or_dean") {
        // in case one department is visible, default selection is that dept. 
        $selected_dept = $dept;
        $restrict_visible_dept_to = $dept;
    } else {
        // in case multiple departments visible, default selection is "all"
        $selected_dept = "";
        $restrict_visible_dept_to = NULL;
    }
    
    // only emit "all" option if there is more than one dept to choose from
    if ($restrict_visible_dept_to == NULL) {
        echo '<option value=""' . ($dept_param == '' ? ' selected' : '') . '>All</OPTION>';
    }
    
    if ($conn) {
    	try {
    		$depts = get_all_dept($conn);
    		foreach ($depts as $index => $dept) {
    			$abbrev = $dept["department_abbrev"];
    			if ($restrict_visible_dept_to == NULL || $restrict_visible_dept_to == $abbrev) {
        			$dept_name = str_replace("Engineering", "Engr.", $dept["department_fullname"]);
        			echo '<option value="' . $abbrev . '"';
    	    		if ($dept_param == $abbrev || $restrict_visible_dept_to == $abbrev) {
    		    		echo " selected";
    			    	$selected_dept = $abbrev;
    			    }
    			    echo ">" . $dept_name . "</option>";
    			}
    		}
    	} catch (PDOException $e) {
    		echo "<div>Could not access departments table: " . $e->getMessage() . "</div>";
    	}
    }
    echo '</select>';
    echo '</div> <!-- End of dept menu -->';
    return $selected_dept;
}

/** 
 * Retrieve a list of pi names who are associated w/ a particular department.
 * @param string $dept Abbreviated name of department, must be non-NULL
 * @retval string[] list of PI's in access.txt who belong to the given department
 */
function get_pi_list_for_department($dept) {
    $dept_pi_list = array();
    $access = file_get_contents("access.txt");
    foreach (explode("\n", $access) as $line) {
        $items = explode("\t", $line);
        $pi_name = trim($items[1]);
        $auth_level = strtolower(trim($items[2]));
        $pi_dept = trim($items[3]);
        if ($auth_level === "pi" && $pi_dept === $dept) {
            $dept_pi_list[] = $pi_name;
        }
    }
    return $dept_pi_list;
}

/**
 * Retrieve the name of the user w/ a particular netID. 
 * @param string $user_netid netID
 * @retval string name of PI / manager in access.txt with given netID, or NULL if no user with given netID found
 */
function get_user_with_netid($user_netid) {
    $access = file_get_contents("access.txt");
    foreach (explode("\n", $access) as $line) {
        $items = explode("\t", $line);
        $netid = strtolower(trim($items[0]));
        $name = trim($items[1]);
        if ($netid === $user_netid) {
            return $name;
        }
    }
    return NULL;
}

/**
 * Convert name to canonical form (not supposed to be human-readable).
 * @param string $name First and/or last name
 * @retval string name in [first last] form, lowercase, no special chars
 */
function normalize_name($name) {
    // convert to lowercase
    $name = strtolower($name);
    
    // if name is in "last, first" format, convert to "first last"
    if (strpos($name, ',') !== False) {
        $name = join(" ", array_reverse(explode(", ", $name)));
    }
    
    // remove non alphanumerical characters from string
    $name = preg_replace("/[^a-z0-9 ]/", '', $name);
    
    return $name;
}

/**
 * Returns true if and only if names contain the same first and last names. 
 * @param string $name_1 name to compare
 * @param string $name_2 name to compare
 * @retval boolean whether names match. 
 */
function names_match($name_1, $name_2) {
    return normalize_name($name_1) === normalize_name($name_2);
}

/**
 * Returns true if and only if user is somewhere in the given list of users. Uses fuzzy matching, so strings do not need to be identical or have the first and last name in the same order. 
 * @param string $pi user name
 * @param $pi_list list of user names
 * @retval boolean whether name is in list
 */
function pi_in_list($pi, $pi_list) {
    foreach ($pi_list as $pi_member) {
        if (names_match($pi, $pi_member)) {
            return True;
        }
    }
    return False;
}

/**
 * Output html code for a supervisors drop-down menu.
 * @param $conn database connection object
 * @param string $table name of a table with the `supervisor` attribute, can be "rad_header" or "laser_header"
 * @param string $role access level of the user, can be safety_manager_or_dean | facility_manager | lab_pi
 * @param string $dept Abbreviated name of department, must be non-NULL for facility manager role
 * @param string $user_netid netID, must be non-empty for lab PI role
 * @retval string[] list of selected supervisors, empty if no supevisors selected, contains all supervisors in drop-down menu if "all" selected
 */
function echo_supervisor_menu($conn, $table, $role, $dept, $user_netid = "") {
    // facility managers are restricted to pi's in their department
    $supervisor_list = NULL;
    if ($role === "facility_manager") {
        $supervisor_list = get_pi_list_for_department($dept);
    }
    
    // pi's are restricted to their own data
    else if ($role === "lab_pi") {
        $supervisor_list = array(get_user_with_netid($user_netid));
    }

    echo '<div class="fleft">';
    echo '<label for="supervisor">Supervisor</label><br />';
    echo '<select id="supervisor" name="supervisor" onchange="this.form.submit();" autocomplete="off">';
    $selected_sups = array();
    $sup_param = isset($_GET['supervisor']) ? $_GET['supervisor'] : '';
    
    // get array of supervisor names
    $sups = get_all_supervisors($conn, $table);
    $sups = array_map(function($x) { return $x["supervisor"]; }, $sups);
    
    // filter out supervisors not in list, if list is not null
    if ($supervisor_list !== NULL) {
        foreach ($sups as $key => $val) {
            if (!pi_in_list($val, $supervisor_list)) {
                unset($sups[$key]);
            }
        }
    }

    // 'All' option is only available if user has dean-level access or there are more than 0 options
    if (count($sups) > 0) {
        $selected_str = '';
        if ($sup_param === '') {
            $selected_sups = $sups;
            $selected_str = ' selected';
        }
        echo '<option value=""' . $selected_str . '>All</OPTION>';
    }
    
	foreach ($sups as $index => $sup) {
		echo '<option value="' . $sup . '"';
		if ($sup_param == $sup) {
			echo " selected";
    		$selected_sups = array($sup_param);
	    }
	    echo ">" . $sup . "</option>";
    }
    
    echo '</select>';
    echo '</div> <!-- End of supervisor menu -->';
    return $selected_sups;
}

?>