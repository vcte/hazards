<?php
// determine authorization level and department affiliation
$user = $_SERVER['REMOTE_USER']; //'rmoller@illinois.edu'; // $_SERVER['REMOTE_USER'];
$user_netid = explode("@", strtolower($user))[0];
$role = NULL;
$dept = NULL;
$access = file_get_contents("access.txt");
foreach (explode("\n", $access) as $line) {
    $items = explode("\t", $line);
    $netid = strtolower(trim($items[0]));
    if ($netid === $user_netid) {
        // determine authorization level
        $auth_level = strtolower(trim($items[2]));
        if ($auth_level === "dean") {
            $role = "safety_manager_or_dean";
        } else if ($auth_level === "facility") {
            $role = "facility_manager";
        } else if ($auth_level === "pi") {
            $role = "lab_pi";
        }
        
        // determine department affiliation
        if ($role === "safety_manager_or_dean") {
            $dept = NULL;
        } else {
            $dept = trim($items[3]);
        }
        
        break;
    }
}; 

// database.php
// list of functions to connect and log in to the database, and to query and access data in the database. 

/**
 * Create and authenticate a connection to the database. 
 * @param string $role must be either "safety_manager_or_dean" or "facility_manager" or "lab_pi"
 * @return $conn database connection object
 */
function db_connect($role) {
    // restrict access so that only users listed in the access.txt file can view data
    if ($role !== "safety_manager_or_dean" && $role !== "facility_manager") { // && $role !== "lab_pi") {
        return NULL;
    }
    
	try {
		$conn = new PDO("mysql:host=fsdatabase.web.engr.illinois.edu;dbname=fsdataba_fsdatabase;charset=latin1", "", "");
		$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
	} catch (PDOException $e) {
		echo "<div>Could not connect to database</div>";
		$conn = NULL;
	}
	return $conn;
}

/**
 * Executes a SQL query and handles exceptions. 
 * @param $conn database connection object
 * @param string $query SQL query
 * @return results as an array or NULL if query failed. 
 */
function exec_query($conn, $query) {
	if ($conn) {
		try {
			$stmt = $conn->prepare($query);
			$stmt->execute();
			return $stmt->fetchAll();
		} catch (PDOException $e) {
			echo "<div>Could not access db: " . $e->getMessage() . "</div>";
		}
	}
	return NULL;
}

/**
 * Build a SQL query for the `hazard` table with the given filters. 
 * If any of the parameters are NULL, then filter is not applied for the attribute. 
 * @param string $dept Department abbreviation (key in the `department` table)
 * @param string $date_start (year-mm-dd) format. Get hazards reported after the given date. 
 * @param string $date_end (year-mm-dd) format. Get hazards reported before the given date. 
 * @param string $type_restriction Audit type, can be either "OSHA" or "Lab"
 * @param $mitigated boolean, can be either 0 | 1 | "0" | "1" | "true" | "false"
 * @retval string SQL query with the given filters. 
 */
function get_sql_hazard_filter($dept = NULL, $date_start = NULL, $date_end = NULL, $type_restriction = NULL, $mitigated = NULL) {
    $filters = array();
    if ($dept != NULL) {
        $filters[] = "dept = '" . $dept . "'";
    }
    if ($date_start != NULL) {
        $filters[] = "date >= '" . $date_start . "'";
    }
    if ($date_end != NULL) {
        $filters[] = "date <= '" . $date_end . "'";
    }
    if ($type_restriction != NULL) {
        $filters[] = "audit_type = '" . $type_restriction . "'";
    }
    if ($mitigated != NULL) {
        $filters[] = "mitigated = " . $mitigated;
    }
    $filter_str = join(" AND ", $filters);
    return " WHERE " . ($filter_str != "" ? $filter_str : "1") . " ";
}

/**
 * Get percentage of mitigated hazards, either for a department or college-wide.
 * @param $conn database connection object
 * @param string $dept abbreheviated department name. NULL for all departments.
 * @param string $date_start in year-m-d format. Get hazards reported after the given date. NULL for no start date limits.
 * @param string $date_end in year-m-d format. Get hazards reported before the given date. NULL for no end date limits.
 * @param string $type_restriction "OSHA" or "Lab". NULL for all audit types. 
 * @retval float percentage of hazards mitigated for the subset of data specified by the parameters. 
 */
function get_percent_mitigated_hazards($conn, $dept = NULL, $date_start = NULL, $date_end = NULL, $type_restriction = NULL) {
    $filter_tot = get_sql_hazard_filter($dept, $date_start, $date_end, $type_restriction);
    $filter_mit = get_sql_hazard_filter($dept, $date_start, $date_end, $type_restriction, 1);
	$query_tot = "SELECT COUNT(*) FROM hazard" . $filter_tot;
	$query_mit = "SELECT COUNT(*) FROM hazard" . $filter_mit;
	$total = exec_query($conn, $query_tot)[0][0];
	$mitig = exec_query($conn, $query_mit)[0][0];
	return $mitig / $total * 100;
}

/**
 * Find the PIs with the most number of lab hazards, top $limit are returned.
 * @param $conn database connection object
 * @param string $dept abbreheviated department name; should satisfy foreign key constraint to `department.department_abbrev`
 * @param int $limit upper limit on number of PIs to retrieve. 
 * @param $date_start in year-m-d format. Get hazards reported after the given date. NULL for no start date limits.
 * @param string $date_end in year-m-d format. Get hazards reported before the given date. NULL for no end date limits.
 * @param string $type_restriction "OSHA" or "Lab". NULL for all audit types. 
 * @return 2D array of [pi_name, number of lab hazards] arrays
 */
function get_pi_most_lab_hazards($conn, $dept, $limit = 10, $date_start = NULL, $date_end = NULL, $type_restriction = NULL) {
    $filter = get_sql_hazard_filter($dept, $date_start, $date_end, $type_restriction);
	$query_pi = "SELECT pi_name, COUNT(*) as num_hazards " .
		    "FROM hazard" . $filter . " AND pi_name <> 'department' " .
		    "GROUP BY pi_name " .
		    "ORDER BY num_hazards DESC " .
		    "LIMIT " . $limit;
	return exec_query($conn, $query_pi);
}

/**
 * Calculate the average number of days from date of report issued to date mitigated, either for a department or college-wide
 * @param $conn database connection object
 * @param string $dept abbreheviated department name. NULL for all departments.
 * @param string $date_start in year-m-d format. Get hazards reported after the given date. NULL for no start date limits.
 * @param string $date_end in year-m-d format. Get hazards reported before the given date. NULL for no end date limits.
 * @param string $type_restriction "OSHA" or "Lab". NULL for all audit types. 
 * @retval float average number of days from report date to mitigation date for the subset of hazard data specified by parameters. 
 */
function get_avg_days_to_mitigation($conn, $dept = NULL, $date_start = NULL, $date_end = NULL, $type_restriction = NULL) {
    $filter = get_sql_hazard_filter($dept, $date_start, $date_end, $type_restriction);
	$query_days = "SELECT AVG(t.days) " .
		      "FROM (SELECT DATEDIFF(date_mitigated, date) as days " .
		            "FROM hazard " .
		            $filter . " AND date_mitigated IS NOT NULL AND date IS NOT NULL " .
		            "LIMIT 100) as t";
	return exec_query($conn, $query_days)[0][0];
}

/**
 * Get top n issues, either for a department or college-wide
 * @param $conn database connection object
 * @param string $dept abbhreviated department name. Set $dept to get the top issues for a specific department. NULL for the top issues college-wide. 
 * @param int $num upper bound on number of issues to get. 
 * @param string $date_start in year-m-d format. Get hazards reported after the given date. NULL for no start date limits.
 * @param string $date_end in year-m-d format. Get hazards reported before the given date. NULL for no end date limits.
 * @param string $type_restriction "OSHA" or "Lab". NULL for all audit types. 
 */
function get_top_issues($conn, $dept = NULL, $num = NULL, $date_start = NULL, $date_end = NULL, $type_restriction = NULL) {
    $filter = get_sql_hazard_filter($dept, $date_start, $date_end, $type_restriction);
	$query_issues = "SELECT observ_code, COUNT(*) " .
			"FROM hazard " .
			$filter . " " . 
			"GROUP BY observ_code " .
			"ORDER BY COUNT(*) DESC " .
			($num !== NULL ? "LIMIT " . $num : "");
	return exec_query($conn, $query_issues);
}

/**
 * Construct sql filter so that supervisor must be in sup_restriction list
 * @param [string] $sup_restriction list of supervisors, such that the `supervisor` attribute must equal one of the supervisors in the list. 
 * @param string $table name of the table that the supervisor attribute belongs to. Necessary for queries involving multiple tables with `supervisor` attr.
 * @retval string SQL condition that can be used in WHERE clause. 
 */
function get_sup_filter($sup_restriction, $table = '') {
    $sup_filters = array();
    foreach ($sup_restriction as $sup) {
        $sup_filters[] = $table . ($table != '' ? '.' : '') . 'supervisor = "' . $sup . '"';
    }
    
    if (count($sup_filters) > 0) {
        return '(' . join(' || ', $sup_filters) . ')';
    } else {
        return "0";
    }
}

/**
 * Get all relevant radiation audit data. 
 * @param $conn database connection object
 * @param [string] $sup_restriction list of supervisors, such that `supervisor` must equal one of the strings in the list. 
 * @param string $start_restriction in year-m-d format. Non-null if the time spanned by the rad audit must be after the date. 
 * @param string $end_restriction in year-m-d format. Non-null if the time spanned by the rad audit must be before the date. 
 * @return list of rad audits that match the given conditions. 
 */
function get_rad_audits($conn, $sup_restriction = NULL, $start_restriction = NULL, $end_restriction = NULL) {
    $sup_cond = "";
    if ($sup_restriction !== NULL) {
        $sup_cond = ' AND ' . get_sup_filter($sup_restriction, "rad_header");
    }
    $start_cond = "";
    if ($start_restriction !== NULL) {
        $start_cond = ' AND (rad_header.date_start >= "' . $start_restriction .
                '" OR rad_header.date_end >= "' . $start_restriction . '") ';
    }
    $end_cond = "";
    if ($end_restriction !== NULL) {
        $end_cond = ' AND (rad_header.date_start <= "' . $end_restriction .
            '" OR rad_header.date_end <= "' . $end_restriction . '") ';
    }
	$query_rad = "SELECT * FROM rad_audit, rad_header WHERE " .
		     "rad_audit.header_id = rad_header.header_id " . 
		     $sup_cond . $start_cond . $end_cond;
	return exec_query($conn, $query_rad);
}

/**
 * Get all labs in a rad audit. 
 * @param $conn database connection object
 * @param int $audit_id id of the rad audit
 * @return list of labs in format [audit_lab_id, audit_id, header_id, building, room] associated with the lab audit. 
 */
function get_rad_audit_labs($conn, $audit_id) {
	$query_labs = "SELECT building, room FROM rad_audit_labs WHERE audit_id = " . $audit_id;
	return exec_query($conn, $query_labs);
}

/**
 * Get all unique radiation survey identifiers, with header information.
 * @param $conn database connection object
 * @param [string] $sup_restriction list of supervisors, such that `supervisor` must equal one of the strings in the list. 
 * @param string $start_restriction in year-m-d format. Non-null if the time spanned by the rad survey must be after the date. 
 * @param string $end_restriction in year-m-d format. Non-null if the time spanned by the rad survey must be before the date. 
 * @return list of rad surveys that meet the given conditions. 
 */
function get_rad_surveys($conn, $sup_restriction = NULL, $start_restriction = NULL, $end_restriction = NULL) {
	$query_rad = "SELECT * FROM rad_survey_comment, rad_header WHERE " .
		     "rad_survey_comment.header_id = rad_header.header_id " . 
		     ($sup_restriction !== NULL ? (' AND ' . get_sup_filter($sup_restriction, "rad_header")) : "") . 
		     ($start_restriction !== NULL ? (' AND (rad_header.date_start >= "' . $start_restriction . 
		                                    '" OR rad_header.date_end >= "' . $start_restriction . '") ') : "") . 
		     ($end_restriction !== NULL ? (' AND (rad_header.date_start <= "' . $end_restriction . 
		                                    '" OR rad_header.date_end <= "' . $end_restriction . '") ') : "");
	return exec_query($conn, $query_rad);
}

/**
 * Get all labs associated with a rad survey. 
 * @param $conn database connection object
 * @param int $survey_id id of the rad survey
 * @return list of labs in format [audit_lab_id, audit_id, header_id, building, room] associated with the lab audit. 
 */
function get_rad_survey_labs($conn, $survey_id) {
	$query_labs = "SELECT building, room FROM rad_survey_labs WHERE survey_id = " . $survey_id;
	return exec_query($conn, $query_labs);
}

/**
 * Get all rad survey results. 
 * @param $conn database connection object
 * @param int $survey_id id of the rad survey
 * @return array of survey results for the given rad survey.
 */
function get_rad_survey_results($conn, $survey_id) {
	$query_results = "SELECT * FROM rad_survey_result WHERE survey_id = " . $survey_id;
	return exec_query($conn, $query_results);
}

/**
 * Get general audit associated with a header_id, or NULL if no such audit exists.
 * @param $conn database connection object
 * @param int $header_id id of rad header
 * @return array of general laboratory safety audit data, or NULL if rad audit does not have a general safety audit.
 */
function get_rad_general($conn, $header_id) {
	$query_general = "SELECT * FROM rad_general WHERE header_id = " . $header_id;
	$gen = exec_query($conn, $query_general);
	if (count($gen) > 0) {
		return $gen[0];
	} else {
		return NULL;
	}
}

/**
 * Get all laser headers that meet the given conditions.
 * @param $conn database connection object
 * @param [string] $sup_restriction list of supervisors, such that `supervisor` must equal one of the strings in the list. 
 * @param string $start_restriction in year-m-d format. Non-null if the date of the laser report must be after the start date. 
 * @param string $end_restriction in year-m-d format. Non-null if the date of the laser report must be before the end date. 
 * @return array of rows from the `laser_header` table that match the given conditions. 
 */
function get_laser_headers($conn, $sup_restriction = NULL, $start_restriction = NULL, $end_restriction = NULL) {
	$query_laser = "SELECT * FROM laser_header WHERE 1 " . 
	               ($sup_restriction !== NULL ? (' AND ' . get_sup_filter($sup_restriction, "laser_header")) : "") . 
	               ($start_restriction !== NULL ? (' AND laser_header.date >= "' . $start_restriction . '"') : "") . 
	               ($end_restriction !== NULL ? (' AND laser_header.date <= "' . $end_restriction . '"') : "");
	return exec_query($conn, $query_laser);
}

/**
 * Get all labs associated with a laser header. 
 * @param $conn database connection object
 * @param int $laser_report_id id of the laser report
 * @return array of [laser_lab_id, laser_report_id, building, room] rows in the laser_labs table. 
 */
function get_laser_labs($conn, $laser_report_id) {
	$query_labs = "SELECT building, room FROM laser_labs WHERE laser_report_id = " . $laser_report_id;
	return exec_query($conn, $query_labs);
}

/**
 * Get laser admin info associated with a laser report id, or NULL if no such info exists
 * @param $conn database connection object
 * @param int $laser_report_id id of the laser report
 * @return array of admin audit items, or NULL if laser report id does not correspond to any entries in the laser_admin table. 
 */
function get_laser_admin($conn, $laser_report_id) {
	$query_admin = "SELECT * FROM laser_admin WHERE laser_report_id = " . $laser_report_id;
	$admin = exec_query($conn, $query_admin);
	if (count($admin) > 0) {
		return $admin[0];
	} else {
		return NULL;
	}
}

/**
 * Get laser engr info associated with a laser report id, or NULL if no such info exists
 * @param $conn database connection object
 * @param int $laser_report_id id of the laser report
 * @return array of engr audit items, or NULL if laser report id does not correspond to any entries in the laser_engr audit table. 
 */
function get_laser_engr($conn, $laser_report_id) {
	$query_engr = "SELECT * FROM laser_engr WHERE laser_report_id = " . $laser_report_id;
	$engr = exec_query($conn, $query_engr);
	if (count($engr) > 0) {
		return $engr[0];
	} else {
		return NULL;
	}
}

/**
 * Get all laser system data associated with a laser report id.
 * @param $conn database connection object
 * @param int $laser_report_id id of the laser report
 * @return 2d array containing all lasers associated with the laser report. 
 */
function get_laser_system($conn, $laser_report_id) {
	$query_sys = "SELECT * FROM laser_system WHERE laser_report_id = " . $laser_report_id;
	return exec_query($conn, $query_sys);
}

/**
 * Get the fiscal year that a given date falls under.
 * Fiscal year 20XX runs from July 1 20(XX-1) to June 30 20XX. 
 * @param Date $date PHP date object
 * @retval int fiscal year of the date. 
 */
function get_corresponding_fiscal_year($date) {
    $year = $date->format('Y');
    if ($date <= DateTime::createFromFormat('Y-m-d', $year . '-6-30')) {
        return $year;
    } else {
        return $year + 1;
    }
}

/**
 * Get range of years encompassing all hazard dates.
 * @param $conn database connection object
 * @retval array that ranges from the first fiscal year in the hazards data to the last fiscal year. 
 */
function get_all_hazard_fiscal_years($conn) {
    $query_date = "SELECT MIN(date), MAX(date) FROM hazard";
    $dates = exec_query($conn, $query_date);
    $min_date = DateTime::createFromFormat('Y-m-d', $dates[0][0]);
    $max_date = DateTime::createFromFormat('Y-m-d', $dates[0][1]);
    $min_fiscal_year = get_corresponding_fiscal_year($min_date);
    $max_fiscal_year = get_corresponding_fiscal_year($max_date);
    return range($min_fiscal_year, $max_fiscal_year);
}

/**
 * Get all departments that are represented in the `departments` table. 
 * @param $conn database connection object
 * @return array of [abbreviated name, full name] pairs in ascending lexigraphical order. 
 */
function get_all_dept($conn) {
    $stmt = "SELECT department_abbrev, department_fullname FROM department ORDER BY department_abbrev";
    return exec_query($conn, $stmt);
}

/**
 * Get all supervisors in a given table. 
 * @param $conn database connection object
 * @param string $table name of a table with the `supervisor` attribute, can be "rad_header" or "laser_header"
 * @return array of singleton [supervisor] entries, each of which is unique. 
 */
function get_all_supervisors($conn, $table) {
    $stmt = "SELECT DISTINCT(supervisor) FROM " . $table . " ORDER BY supervisor";
    return exec_query($conn, $stmt);
}

?>
