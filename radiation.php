<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Radiation Hazards - My.ENGR Portal - U of I</title>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
		<meta name="keywords" content="" />

		<link rel="SHORTCUT ICON" href="/favicon.ico" />
		<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700" />
		<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Open+Sans:400,700,400italic" />
		<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Oswald:400,700,300" />
		<link href="style/gentable.css" rel="stylesheet" type="text/css" />
		<link href="style/engr.css" rel="stylesheet" type="text/css" />
		<link rel="stylesheet" type="text/css" href="style/bootstrap.min.css" />
		<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<script type="text/javascript">var $j = jQuery.noConflict(); </script>
    		<script type="text/javascript" src="scripts/bootstrap.min.js"></script>
    		<script src="scripts/bootstrap-datepicker.min.js" language="javascript" type="text/javascript"></script>
		<style>
		        .sortcol {
		            background-position: right center;
		            background-repeat: no-repeat;
		            cursor: pointer;
		            padding-right: 50px;
		        }
		        .sortasc {
		            background-color: #DDFFAC;
		            background-image: url("static/arrow-090-medium.png");
		        }
		        .sortdesc {
		            background-color: #B9DDFF;
		            background-image: url("static/arrow-270-medium.png");
		        }
		        .nosort {
		            cursor: default;
		        }
		</style>
	</head>

<body>
	<div id="page">
		<?php
		include("banner.php");
		?>
	
	<div id="content">

<div class="module">
<div class="module_top"><span></span></div>
<h2><a href="/" style="float: right;">Return</a>
<div id="portal_app_info" style="float: right; margin-right: 10px;"></div>Directory - Radiation Hazards</h2>

<div class="clearfix" style="width: 100%;">
<div class="module_content">
<form method="get">

<div class="menu">
<ul>
<li><a href="dashboard.php">Dashboard</a></li>
<li><a href="lab.php">Lab Hazards</a></li>
<li><a href="facility.php">Facility Hazards</a></li>
<li class="on"><a href="radiation.php">Radiation Hazards</a></li>
<li><a href="laser.php">Laser Hazards</a></li>
<li><a href="fume.php">Fume Hoods</a></li>
<li><a href="report.php">Print Report</a></li>
</ul>
</div> <!-- menu close -->
<?php
require 'database.php';
require 'helper.php';

$conn = db_connect();
?>
<div class="clearfix">

<!-- Supervisor drop down menu -->
<div class="fleft">
<label for="supervisor">Supervisor</label><br />
<select id="supervisor" name="supervisor" onchange="this.form.submit();" autocomplete="off">
<?php
$selected_sup = "";
$sup_param = isset($_GET['supervisor']) ? $_GET['supervisor'] : '';
echo '<option value=""' . ($sup_param == '' ? ' selected' : '') . '>All</OPTION>';
if ($conn) {
	try {
		$stmt = $conn->prepare("SELECT DISTINCT(supervisor) FROM rad_header ORDER BY supervisor");
		$stmt->execute();
		$sups = $stmt->fetchAll();
		foreach ($sups as $index => $sup) {
			echo '<option value="' . $sup["supervisor"] . '"';
			if ($sup_param == $sup["supervisor"]) {
				echo " selected";
				$selected_sup = $sup_param;
			}
			echo ">" . $sup["supervisor"] . "</option>";
		}
	} catch (PDOException $e) {
		echo "<div>Could not access rad_header table: " . $e->getMessage() . "</div>";
		$conn = NULL;
	}
}
?>
</select>
</div> <!-- End of supervisor menu -->

<!-- Lab building drop down menu -->
<div class="fleft">
<label for="building">Lab Building</label><br />
<select id="building" name="building" onchange="this.form.submit();" autocomplete="off">
<?php
$selected_bldg = "";
$bldg_param = isset($_GET['building']) ? $_GET['building'] : '';
echo '<option value=""' . ($bldg_param == '' ? ' selected' : '') . '>All</OPTION>';
if ($conn) {
	try {
		$stmt = $conn->prepare("SELECT DISTINCT(building_name) FROM building ORDER BY building_name");
		$stmt->execute();
		$bldgs = $stmt->fetchAll();
		foreach ($bldgs as $index => $bldg) {
			echo '<option value="' . $bldg["building_name"] . '"';
			if ($bldg_param == $bldg["building_name"]) {
				echo " selected";
				$selected_bldg = $bldg_param;
			}
			echo ">" . $bldg["building_name"] . "</option>";
		}
	} catch (PDOException $e) {
		echo "<div>Could not access building table: " . $e->getMessage() . "</div>";
		$conn = NULL;
	}
}
?>
</select>
</div> <!-- End of lab building menu -->

<!-- Start date picker -->
<div class="fleft">
<label for="date_start">After this date</label><br />
<?php
$selected_start = isset($_GET['date_start']) ? $_GET['date_start'] : '';
echo '<div class="input-group date"><input type="text" id="date_start" name="date_start" onchange="this.form.submit();" class="dp form-control" ';
echo 'value="' . $selected_start . '"></div>';
?>
</div> <!-- End of start date picker -->

<!-- End date picker -->
<div class="fleft">
<label for="date_end">Before this date</label><br />
<?php
$selected_end = isset($_GET['date_end']) ? $_GET['date_end'] : '';
echo '<div class="input-group date"><input type="text" id="date_end" name="date_end" onchange="this.form.submit();" class="dp form-control" ';
echo 'value="' . $selected_end . '"></div>';
?>
</div> <!-- End of end date picker -->

<!-- Search form -->
<div class="fleft">
<label for="txtSearch">Search</label><br />
<?php 
if (isset($_GET['txtSearch'])) {
	$_GET['txtSearch'] = substr($_GET['txtSearch'], 0, 50);
} else {
	$_GET['txtSearch'] = '';
}
echo '<input type="text" id="txtSearch" name="txtSearch" maxlength="50" value="' . filter_var($_GET['txtSearch'], FILTER_SANITIZE_STRING) . '" />';
?>
<input type="submit" name="strSubmit" id="strSubmit" value="Search"/>
</div> <!-- End of search form -->
<br><br><br>

<?php
// determine which restrictions to apply to query
$sup_restriction = NULL;
if ($selected_sup != '') {
	$sup_restriction = $selected_sup;
}

$bldg_restriction = NULL;
if ($selected_bldg != '') {
	$bldg_restriction = $selected_bldg;
}

$start_restriction = NULL;
if ($selected_start != '') {
	$start_restriction = rearrange_gui_date($selected_start);
}

$end_restriction = NULL;
if ($selected_end != '') {
	$end_restriction = rearrange_gui_date($selected_end);
}

$search_restriction = NULL;
if (isset($_GET['txtSearch']) && $_GET['txtSearch'] != '') {
	$search_restriction = preg_quote(addslashes($_GET['txtSearch']));
}
	
$rad_audits = get_rad_audits($conn, $sup_restriction, $start_restriction, $end_restriction);
$rad_surveys = get_rad_surveys($conn, $sup_restriction, $start_restriction, $end_restriction);

// audit report items
$weekly_survey_item = "Weekly contamination surveys performed when material in use?";
$calibrated_inst_item = "Are calibrated instruments available for contamination surveys?";
$inventory_uptodate_item = "Are inventory, use and waste records up-to-date?";
$caution_signs_item = "Are there caution signs and employee notices?";
$protective_clothing_item = "Do lab workers wear protective clothing, gloves, eyewear, footwear, etc?";
$absorbent_paper_item = "Is absorbent paper used on the benches?";
$training_records_item = "Are training records available for review?";
$isotope_labeled_item = "Are isotope areas and articles labeled?";
$mouth_pipetting_item = "No mouth pipetting!";
$no_cosmetic_item = "No eating, drinking and cosmetic use!";
$food_storage_item = "No improper food storage";
$shielding_handling_item = "Are shielding and handling devices in use?";
$lab_security_item = "Is the lab locked or attended?";
$radioactive_waste_item = "Is radioactive waste stored properly and labeled?";

// general lab safety audit items
$emergency_postings_item = "Are current emergency postings at the entrance?";
$chemical_label_item = "Are Chemical containers labeled properly?";
$gas_bottle_secured_item = "Are gas bottles properly secured?";
$window_unobstructed_item = "Are lab door windows unobstructed?";
$housekeeping_item = "Is housekeeping satisfactory?";
$shower_stations_item = "Are emergency shower/eyewash stations tested?";
$stations_accessible_item = "Are emergency/eyewash stations accessible?";

// helper functions
function expand($str) {
   if ($str == "S") {
      return "Satisfactory";
   } else if ($str == "NC") {
      return "Not Checked";
   } else {
      return "Not Applicable";
   }
}

function rel_query($params) {
   return '"/~fsdatabase/radiation.php?' . http_build_query($params) . '"';
}

function anything_in_survey_matches_search($survey, $survey_labs, $survey_results, $search_restriction) {
   $header_col_names = array("supervisor", "office_building", "office_room", "permit_number", "performed_by", "lab_personnel");
   $survey_col_names = array("frisk_instrument_name", "frisk_instrument_serial", "swipe_instrument_name", "swipe_instrument_serial",
                             "exposure_instrument_name", "exposure_instrument_serial", "comments");
   foreach (array_merge($header_col_names, $survey_col_names) as $col_name) {
      if (preg_match('/' . $search_restriction . '/i', $survey[$col_name])) {
         return True;
      }
   }
   
   $labs_col_names = array("building", "room");
   foreach ($survey_labs as $lab) {
      foreach ($labs_col_names as $col_name) {
         if (preg_match('/' . $search_restriction . '/i', $lab[$col_name])) {
            return True;
         }
      }
   }
   
   $result_col_names = array("location", "comments");
   foreach ($survey_results as $result) {
      foreach ($result_col_names as $col_name) {
         if (preg_match('/' . $search_restriction . '/i', $result[$col_name])) {
            return True;
         }
      }
   }
   
   return False;
}

function anything_in_audit_matches_search($row, $labs, $general, $search_restriction) {
   $header_col_names = array("supervisor", "office_building", "office_room", "permit_number", "performed_by", "lab_personnel");
   $audit_col_names = array("survey_instruments_calibrated", "contact", "comments");
   foreach (array_merge($header_col_names, $audit_col_names) as $col_name) {
      if (preg_match('/' . $search_restriction . '/i', $row[$col_name])) {
         return True;
      }
   }
   
   $labs_col_names = array("building", "room");
   foreach ($labs as $lab) {
      foreach ($labs_col_names as $col_name) {
         if (preg_match('/' . $search_restriction . '/i', $lab[$col_name])) {
            return True;
         }
      }
   }
   
   if (preg_match('/' . $search_restriction . '/i', $general["comments"])) {
      return True;
   }
   
   return False;
}

function echo_rad_audit_header($row, $labs) {
   echo '<h3>Radiation Safety Audit Report</h3>';
   echo '<table class="table table-responsive table-bordered">';
   echo '<tr><td class="col-md-2">Supervisor: <a href=' . rel_query(with_param($url_params, "supervisor", $row['supervisor'])) . 
        '>' . $row['supervisor'] . '</a></td>';
   echo     '<td class="col-md-2">Permit No: ' . $row['permit_number'] . '</td></tr>';
   echo '<tr><td class="col-md-2">Office: ' . $row['office_room'] . ' ' . $row['office_building'] . '</td>';
   echo     '<td class="col-md-2">Performed by: ' . $row['performed_by'] . '</td></tr>';
   if ($row['lab_personnel'] != NULL && $row['lab_personnel'] != "") {
      echo '<tr><td class="col-md-2"></td><td class="col-md-2">Lab Personnel: ' . $row['lab_personnel'] . '</td></tr>';
   }
   echo '<tr><td class="col-md-2">Lab(s): ' . formatted_labs($labs) . '</td>';
   echo     '<td class="col-md-2">Audit Dates: ' . rearrange_sql_date($row['date_start']) . ' - ' . rearrange_sql_date($row['date_end']) . '</td></tr>';
   echo '</table>';
}

function echo_rad_audit($row) {
   global $weekly_survey_item, $calibrated_inst_item, $inventory_uptodate_item, $caution_signs_item, $protective_clothing_item, 
          $absorbent_paper_item, $training_records_item, $isotope_labeled_item, $mouth_pipetting_item, $no_cosmetic_item, $food_storage_item, 
          $shielding_handling_item, $lab_security_item, $radioactive_waste_item;

   echo '<table class="table table-responsive table-bordered">';
   echo '<tr><td class="col-md-6">' . $weekly_survey_item . '</td><td class="col-md-2">' . expand($row['weekly_surveys']) . '</td></tr>';
   echo '<tr><td class="col-md-6">' . $calibrated_inst_item . '</td><td class="col-md-2">' . expand($row['survey_meter_available']) . '</td></tr>';
   echo '<tr><td class="col-md-6">' . $inventory_uptodate_item . '</td><td class="col-md-2">' . expand($row['inventory_and_use_records']) . '</td></tr>';
   echo '<tr><td class="col-md-6">' . $caution_signs_item . '</td><td class="col-md-2">' . expand($row['caution_signs_and_employee_notices']) . '</td></tr>';
   echo '<tr><td class="col-md-6">' . $protective_clothing_item . '</td><td class="col-md-2">' . expand($row['protective_clothing']) . '</td></tr>';
   echo '<tr><td class="col-md-6">' . $absorbent_paper_item . '</td><td class="col-md-2">' . expand($row['use_of_absorbent_paper_on_benches']) . '</td></tr>';
   echo '<tr><td class="col-md-6">' . $training_records_item . '</td><td class="col-md-2">' . expand($row['training_records_available']) . '</td></tr>';
   echo '<tr><td class="col-md-6">' . $isotope_labeled_item . '</td><td class="col-md-2">' . expand($row['isotope_areas_labeled']) . '</td></tr>';
   echo '<tr><td class="col-md-6">' . $mouth_pipetting_item . '</td><td class="col-md-2">' . expand($row['no_mouth_pipetting']) . '</td></tr>';
   echo '<tr><td class="col-md-6">' . $no_cosmetic_item . '</td><td class="col-md-2">' . expand($row['no_eating_drinking_cosmetics']) . '</td></tr>';
   echo '<tr><td class="col-md-6">' . $food_storage_item . '</td><td class="col-md-2">' . expand($row['proper_food_storage']) . '</td></tr>';
   echo '<tr><td class="col-md-6">' . $shielding_handling_item . '</td><td class="col-md-2">' . expand($row['shielding_and_handling_devices']) . '</td></tr>';
   echo '<tr><td class="col-md-6">' . $lab_security_item . '</td><td class="col-md-2">' . expand($row['lab_security']) . '</td></tr>';
   echo '<tr><td class="col-md-6">' . $radioactive_waste_item . '</td><td class="col-md-2">' . expand($row['radioactive_waste_storage_labels']) . '</td></tr>';
   echo '</table>';
      
   echo '<div>';
   echo '<p>Survey instruments calibrated (mfr., model, SN): ' . $row['survey_instruments_calibrated'] . '</p>';
   echo '<p>Comments: ' . str_replace('\n', '<br>', $row['comments']) . '</p>';
   echo '<p>Contact: ' . $row['contact'] . '</p>';
   echo '</div>';
}

function echo_rad_general($general) {
   global $emergency_postings_item , $chemical_label_item, $gas_bottle_secured_item, $window_unobstructed_item, 
          $housekeeping_item, $shower_stations_item, $stations_accessible_item;
   
   echo '<h3>General Laboratory Safety Audit</h3>';
   echo '<table class="table table-responsive table-bordered">';
   echo '<tr><td class="col-md-6">' . $emergency_postings_item . '</td><td class="col-md-2">' . expand($general['emergency_posting']) . '</td></tr>';
   echo '<tr><td class="col-md-6">' . $chemical_label_item . '</td><td class="col-md-2">' . expand($general['chemical_containers_labeled']) . '</td></tr>';
   echo '<tr><td class="col-md-6">' . $gas_bottle_secured_item . '</td><td class="col-md-2">' . expand($general['gas_bottles_tethered']) . '</td></tr>';
   echo '<tr><td class="col-md-6">' . $window_unobstructed_item . '</td><td class="col-md-2">' . expand($general['window_unobstructed']) . '</td></tr>';
   echo '<tr><td class="col-md-6">' . $housekeeping_item . '</td><td class="col-md-2">' . expand($general['housekeeping_satisfactory']) . '</td></tr>';
   echo '<tr><td class="col-md-6">' . $shower_stations_item . '</td><td class="col-md-2">' . expand($general['shower_eyewash_testing']) . '</td></tr>';
   echo '<tr><td class="col-md-6">' . $stations_accessible_item . '</td><td class="col-md-2">' . expand($general['shower_eyewash_accessible']) . '</td></tr>';
   echo '</table>';
   
   echo '<div><p>Comments: ' . str_replace('\n', '<br>', $general['comments']) . '</p></div>';
}

function echo_rad_report_header($survey) {
   echo '<h3>Radiation Safety Survey Report</h3>';
   echo '<table class="table table-responsive table-bordered">';
   echo '<tr><td class="col-md-2">Supervisor: <a href=' . rel_query(with_param($url_params, "supervisor", $survey['supervisor'])) . 
        '>' . $survey['supervisor'] . '</a></td>';
   echo     '<td class="col-md-2">Permit No: ' . $survey['permit_number'] . '</td></tr>';
   echo '<tr><td class="col-md-2">Office: ' . $survey['office_room'] . ' ' . $survey['office_building'] . '</td>';
   echo     '<td class="col-md-2">Performed by: ' . $survey['performed_by'] . '</td></tr>';
   echo '<tr><td class="col-md-2">Survey Date: ' . rearrange_sql_date($survey['date_end']) . '</td>';
   echo     '<td class="col-md-2"></td></tr>';
   echo '</table>';
}

function echo_rad_report_labs($survey_labs) {
   echo '<div><b>Labs</b></div>';
   echo '<table class="table table-responsive table-bordered">'; //table-striped
   echo '<thead class="thead-inverse"><tr><th class="col-md-2">Building</th><th class="col-md-2">Room</th></tr></thead><tbody>';
   foreach ($survey_labs as $lab) {
      echo '<tr><td class="col-md-2"><a href=' . rel_query(with_param($url_params, "building", $lab['building'])) . '>' . $lab['building'] . '</a></td>';
      echo     '<td class="col-md-2">' . $lab['room'] . '</td></tr>';
   }
   echo '</tbody></table>';
}

function echo_rad_report($survey, $survey_results) {
   echo '<div><b>Survey Results</b></div>';
   echo '<table class="table table-responsive table-bordered"><thead class="thead-inverse">'; //table-striped
   echo '<tr><th class="col-md-2">Location</th><th class="col-md-2">Direct (cpm)</th>';
   echo     '<th class="col-md-2">Swipe DPM (per 100 cm^2)</th><th class="col-md-2">Exposure (mR/h)</th></tr></thead><tbody>';
   foreach ($survey_results as $result) {
      echo '<tr><td class="col-md-2">' . $result['location'] . '</td>';
      echo     '<td class="col-md-2">' . $result['direct'] . '</td>';
      echo     '<td class="col-md-2">' . $result['swipe_dpm'] . '</td>';
      echo     '<td class="col-md-2">' . $result['exposure'] . '</td></tr>';
      if ($result['comments'] != NULL) {
         echo '<tr><td colspan="4">' . $result['comments'] . '</td></tr>';
      }
   }
   echo '</tbody></table>';
   
   $frisk_inst = $survey['frisk_instrument_name'] != NULL ? $survey['frisk_instrument_name'] : 'None';
   $swipe_inst = $survey['swipe_instrument_name'] != NULL ? $survey['swipe_instrument_name'] : 'None';
   $exposure_inst = $survey['exposure_instrument_name'] != NULL ? $survey['exposure_instrument_name'] : 'None';
   echo '<div><p>Instruments used in this survey:</p>';
   echo '<p>Frisk Instrument: ' . $frisk_inst . ': ' . $survey['frisk_instrument_serial'] . '</p>';
   echo '<p>Swipe Instrument: ' . $swipe_inst . ': ' . $survey['swipe_instrument_serial'] . '</p>';
   echo '<p>Exposure Instrument: ' . $exposure_inst . ': ' . $survey['exposure_instrument_serial'] . '</p>';
   echo '<p>General comments: ' . str_replace('\n', '<br>', $survey['comments']) . '</p>';
   echo '</div>';
}

$url_params = remove_empty($_GET);

// sort surveys and audits by date
usort($rad_surveys, function($a, $b) { return strtotime($a['date_end']) - strtotime($b['date_end']); });
usort($rad_audits, function($a, $b) { return strtotime($a['date_end']) - strtotime($b['date_end']); });

// determine date intervals
$dates = array();
$dates[] = new DateTime('1970-01-01');
foreach ($rad_surveys as $survey) {
   if ($dates[-1] != $survey['date_end']) {
      $dates[] = $survey['date_end'];
   }
}
$dates[] = new DateTime('2020-12-28');

$iterated = False;
for ($i = 0; $i < count($dates) - 1; $i++) {
   $date_start = $dates[$i];
   $date_end = $dates[$i + 1];
   
   foreach ($rad_surveys as $survey) {
      if ($survey['date_end'] === $date_end) {
         $survey_labs = get_rad_survey_labs($conn, $survey['survey_id']);
         
         // skip this rad survey if none of the lab buildings match the query building
         if (!any_building_matches_restriction($survey_labs, $bldg_restriction)) {
            continue;
         }
         
         // skip this rad survey is nothing in the survey matches the search query
         $survey_results = get_rad_survey_results($conn, $survey['survey_id']);
         if ($search_restriction != NULL && !anything_in_survey_matches_search($survey, $survey_labs, $survey_results, $search_restriction)) {
            continue;
         }
         $iterated = True;
         
         echo_rad_report_header($survey);
         echo_rad_report_labs($survey_labs);
         echo_rad_report($survey, $survey_results);
      }
   }
   
   foreach ($rad_audits as $row) {
      $date = DateTime::createFromFormat('Y-m-d', $row['date_end']);
      if ($date_start < $date && $date <= $date_end) {
         $labs = get_rad_audit_labs($conn, $row['audit_id']);
         
         // skip this rad audit if none of the lab buildings match the query building
         if (!any_building_matches_restriction($labs, $bldg_restriction)) {
            continue;
         }
         $iterated = True;
         
         // skip this rad audit is nothing in the audit matches the search query
         $general = get_rad_general($conn, $row['header_id']);
         if ($search_restriction != NULL && !anything_in_audit_matches_search($row, $labs, $general, $search_restriction)) {
            continue;
         }
         
         echo_rad_audit_header($row, $labs);
         echo_rad_audit($row);
         
         if ($general != NULL) {
            echo_rad_general($general);
         }
      }
   }
}

if (!$iterated) {
   echo "<br><br><div>No results found!</div>";
}
?>

</div> <!-- End of clearfix -->

<script type="text/javascript">
    $j('.dp').datepicker({ format: 'mm/dd/yyyy'});
    
    // TODO - disable enter button on date pickers
</script>

</form>
</div> <!-- end of module_content -->
</div> <!-- end of clearfix -->
<div class="module_bottom"><span></span></div>
</div> <!-- end of module -->
<div id="authz"></div>
</div><!-- close content -->

	<?php
	include("footer.php");
	?>
</div><!--close page-->
	<script type="text/javascript" src="//emergency.webservices.illinois.edu/illinois.js"></script>
</body>
</html>
