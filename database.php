<?php
function db_connect() {
	try {
		$conn = new PDO("mysql:host=fsdatabase.web.engr.illinois.edu;dbname=fsdataba_fsdatabase;charset=latin1", "username", "password");
		$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
	} catch (PDOException $e) {
		echo "<div>Could not connect to database</div>";
		$conn = NULL;
	}
	return $conn;
}

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

// get percentage of mitigated hazards, either for a department or college-wide
function get_percent_mitigated_hazards($conn, $dept = NULL) {
	$query_tot = "SELECT COUNT(*) FROM hazard WHERE audit_type = 'Lab'" . ($dept != NULL ? " AND dept = '" . $dept . "'" : "");
	$query_mit = "SELECT COUNT(*) FROM hazard WHERE audit_type = 'Lab' AND mitigated = 1" . ($dept != NULL ? " AND dept = '" . $dept . "'" : "");
	$total = exec_query($conn, $query_tot)[0][0];
	$mitig = exec_query($conn, $query_mit)[0][0];
	return $mitig / $total * 100;
}

// find the PIs with the most number of lab hazards, top 10 are returned
function get_pi_most_lab_hazards($conn, $dept, $limit = 10) {
	$query_pi = "SELECT pi_name, COUNT(*) as num_hazards " .
		    "FROM hazard WHERE audit_type = 'Lab' AND dept = '" . $dept . "' AND pi_name <> 'department' " .
		    "GROUP BY pi_name " .
		    "ORDER BY num_hazards DESC " .
		    "LIMIT " . $limit;
	return exec_query($conn, $query_pi);
}

// calculate the average number of days from date of report issued to date mitigated, either for a department or college-wide
function get_avg_days_to_mitigation($conn, $dept = NULL) {
	$query_days = "SELECT AVG(t.days) " .
		      "FROM (SELECT DATEDIFF(date_mitigated, date) as days " .
		            "FROM hazard " .
		            "WHERE date_mitigated IS NOT NULL AND date IS NOT NULL " . ($dept != NULL ? "AND dept = '" . $dept . "' " : "") .
		            "LIMIT 100) as t";
	return exec_query($conn, $query_days)[0][0];
}

// get top n issues, either for a department or college-wide
function get_top_issues($conn, $dept = NULL, $num = NULL) {
	$query_issues = "SELECT observ_code, COUNT(*) " .
			"FROM hazard " .
			($dept != NULL ? "WHERE dept='" . $dept . "' " : "") .
			"GROUP BY observ_code " .
			"ORDER BY COUNT(*) DESC " .
			($num != NULL ? "LIMIT " . $num : "");
	return exec_query($conn, $query_issues);
}

// get all departments
function get_all_dept_abbrev($conn) {
	$query_depts = "SELECT department_abbrev FROM department";
	return exec_query($conn, $query_depts);
}

// get all relevant radiation audit data
function get_rad_audits($conn, $sup_restriction = NULL, $start_restriction = NULL, $end_restriction = NULL) {
	$query_rad = "SELECT * FROM rad_audit, rad_header WHERE " .
		     "rad_audit.header_id = rad_header.header_id " . 
		     ($sup_restriction != NULL ? (' AND rad_header.supervisor = "' . $sup_restriction . '" ') : "") . 
		     ($start_restriction != NULL ? (' AND (rad_header.date_start >= "' . $start_restriction . 
		                                    '" OR rad_header.date_end >= "' . $start_restriction . '") ') : "") . 
		     ($end_restriction != NULL ? (' AND (rad_header.date_start <= "' . $end_restriction . 
		                                    '" OR rad_header.date_end <= "' . $end_restriction . '") ') : "");
	return exec_query($conn, $query_rad);
}

// get all labs in a rad audit
function get_rad_audit_labs($conn, $audit_id) {
	$query_labs = "SELECT building, room FROM rad_audit_labs WHERE audit_id = " . $audit_id;
	return exec_query($conn, $query_labs);
}

// get all unique radiation survey identifiers, with header information
function get_rad_surveys($conn, $sup_restriction = NULL, $start_restriction = NULL, $end_restriction = NULL) {
	$query_rad = "SELECT * FROM rad_survey_comment, rad_header WHERE " .
		     "rad_survey_comment.header_id = rad_header.header_id " . 
		     ($sup_restriction != NULL ? (' AND rad_header.supervisor = "' . $sup_restriction . '"') : "") . 
		     ($start_restriction != NULL ? (' AND (rad_header.date_start >= "' . $start_restriction . 
		                                    '" OR rad_header.date_end >= "' . $start_restriction . '") ') : "") . 
		     ($end_restriction != NULL ? (' AND (rad_header.date_start <= "' . $end_restriction . 
		                                    '" OR rad_header.date_end <= "' . $end_restriction . '") ') : "");
	return exec_query($conn, $query_rad);
}

// get all labs in a rad survey
function get_rad_survey_labs($conn, $survey_id) {
	$query_labs = "SELECT building, room FROM rad_survey_labs WHERE survey_id = " . $survey_id;
	return exec_query($conn, $query_labs);
}

// get all rad survey results
function get_rad_survey_results($conn, $survey_id) {
	$query_results = "SELECT * FROM rad_survey_result WHERE survey_id = " . $survey_id;
	return exec_query($conn, $query_results);
}

// get general audit associated with a header_id, or NULL if no such audit exists
function get_rad_general($conn, $header_id) {
	$query_general = "SELECT * FROM rad_general WHERE header_id = " . $header_id;
	$gen = exec_query($conn, $query_general);
	if (count($gen) > 0) {
		return $gen[0];
	} else {
		return NULL;
	}
}

// get all laser headers
function get_laser_headers($conn, $sup_restriction = NULL, $start_restriction = NULL, $end_restriction = NULL) {
	$query_laser = "SELECT * FROM laser_header WHERE 1 " . 
	               ($sup_restriction != NULL ? (' AND laser_header.supervisor = "' . $sup_restriction . '"') : "") . 
	               ($start_restriction != NULL ? (' AND laser_header.date >= "' . $start_restriction . '"') : "") . 
	               ($end_restriction != NULL ? (' AND laser_header.date <= "' . $end_restriction . '"') : "");
	return exec_query($conn, $query_laser);
}

// get all labs associated with a laser header
function get_laser_labs($conn, $laser_report_id) {
	$query_labs = "SELECT building, room FROM laser_labs WHERE laser_report_id = " . $laser_report_id;
	return exec_query($conn, $query_labs);
}

// get laser admin info associated with a laser report id, or NULL if no such info exists
function get_laser_admin($conn, $laser_report_id) {
	$query_admin = "SELECT * FROM laser_admin WHERE laser_report_id = " . $laser_report_id;
	$admin = exec_query($conn, $query_admin);
	if (count($admin) > 0) {
		return $admin[0];
	} else {
		return NULL;
	}
}

// get laser engr info associated with a laser report id, or NULL if no such info exists
function get_laser_engr($conn, $laser_report_id) {
	$query_engr = "SELECT * FROM laser_engr WHERE laser_report_id = " . $laser_report_id;
	$engr = exec_query($conn, $query_engr);
	if (count($engr) > 0) {
		return $engr[0];
	} else {
		return NULL;
	}
}

// get all laser system data associated with a laser report id
function get_laser_system($conn, $laser_report_id) {
	$query_sys = "SELECT * FROM laser_system WHERE laser_report_id = " . $laser_report_id;
	return exec_query($conn, $query_sys);
}
?>
