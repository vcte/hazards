<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Laser Hazards - My.ENGR Portal - U of I</title>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
		<meta name="keywords" content="" />

		<link rel="SHORTCUT ICON" href="/~fsdatabase/favicon.ico" />
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
		        .table-wrapper {
		            overflow: auto;
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
<div id="portal_app_info" style="float: right; margin-right: 10px;"></div>Laser Hazards</h2>

<div class="clearfix" style="width: 100%;">
<div class="module_content">
<form method="get">

<div class="menu">
<ul>
<li><a href="dashboard.php">Dashboard</a></li>
<li><a href="lab.php">Lab Hazards</a></li>
<li><a href="facility.php">Facility Hazards</a></li>
<li><a href="radiation.php">Radiation Hazards</a></li>
<li class="on"><a href="laser.php">Laser Hazards</a></li>
<li><a href="fume.php">Fume Hoods</a></li>
<li><a href="report.php">Print Report</a></li>
</ul>
</div> <!-- menu close -->
<?php
require 'database.php';
require 'helper.php';
require 'dropdown.php';

$conn = db_connect($role);
?>
<div class="clearfix">

<!-- Supervisor drop down menu -->
<?php $selected_sups = echo_supervisor_menu($conn, "laser_header", $role, $dept, $user_netid); ?>

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
$sup_restriction = $selected_sups;

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

$headers = get_laser_headers($conn, $sup_restriction, $start_restriction, $end_restriction);

// helper functions
/**
 * Return html for table entries containing checkboxes 
 * @param $admin associative array
 * @param string $key key
 * @retval string html for table data element. 
 */
function checkboxes($admin, $key) {
   $opts = array('S', 'U', 'NA', 'NC');
   $str = '';
   foreach ($opts as $opt) {
      $str .= '<td>' . ($admin[$key] === $opt ? '<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>' : '') . '</td>';
   }
   return $str;
}

function rel_query($params) {
   return '"/~fsdatabase/laser.php?' . http_build_query($params) . '"';
}

function anything_in_report_matches_search($row, $laser_labs, $systems, $search_restriction) {
   $header_col_names = array("supervisor", "office_building", "office_room", "permit_number", "rss_representative", "lab_personnel", "comments");
   foreach ($header_col_names as $col_name) {
      if (preg_match('/' . $search_restriction . '/i', $row[$col_name])) {
         return True;
      }
   }
   
   $labs_col_names = array("building", "room");
   foreach ($laser_labs as $lab) {
      foreach ($labs_col_names as $col_name) {
         if (preg_match('/' . $search_restriction . '/i', $lab[$col_name])) {
            return True;
         }
      }
   }
   
   $system_col_names = array("iema_ref_num", "uiuc_inv_num", "building", "room", "laser_manufacturer", "model", "serial_num",
                             "type", "lasing_medium", "wavelengths", "max_power", "pulse_duration", "pulse_frequency", 
                             "emerging_beam_divergence", "beam_diameter", "status", "comments", "pi_name", "fabricated_at_uiuc");
   foreach ($systems as $sys) {
      foreach ($system_col_names as $col_name) {
         if (preg_match('/' . $search_restriction . '/i', $sys[$col_name])) {
            return True;
         }
      }
   }
   
   return False;// TODO - deal w/ whitespace
}

// laser audit items - procedure and admin controls
$operation_procedure_item = "Operation procedure available";
$alignment_procedure_item = "Alignment procedure available";
$min_power_item = "Does procedure call for minimum power/energy necessary";
$annual_training_item = "Personnel have had annual training";
$area_posted_item = "Area appropriately posted";
$eye_protection_item = "Eye protection appropriately protects the eye";
$eyewear_condition_item = "Eyewear in good condition";
$eyewear_wavelengths_item = "Eyewear suitable for specific wavelength(s)";
$eyewear_density_item = "Eyewear optical density is adequate";
$eyewear_labeled_item = "Eyewear ODs & wavelengths are labeled";
$eyewear_nhz_item = "Eyewear worn inside the NHZ";
$eyewear_inspection_item = "Semi-annual eyewear inspection documented";
$clothing_available_item = "Gloves, clothing, or shields available (Class 4 only)";

// laser audit items - engineering controls
$protective_housing_item = "Protective housing in place";
$safety_interlocks_item = "Safety interlocks perform as intended";
$laser_mounted_item = "Laser mounted on optical bench or other stabile platform";
$beam_eye_level_item = "Beam not at eye level";
$warning_system_item = "Warning system (visual or aural)";
$access_restricted_item = "Access is restricted";
$rapid_exit_item = "Rapid exit pathway available";
$disconnect_switch_item = "Emergency disconnect switch available";
$contact_list_item = "Emergency contact list available";
$laser_terminus_item = "Lasers > 5mW have fire-resistant terminus";

$url_params = remove_empty($_GET);

$iterated = False;
foreach ($headers as $row) {
   $laser_labs = get_laser_labs($conn, $row['laser_report_id']);
   $systems = get_laser_system($conn, $row['laser_report_id']);
   
   // skip this laser report if none of the lab buildings match the query building
   if (!any_building_matches_restriction($laser_labs, $bldg_restriction)) {
      continue;
   }
   
   // skip this laser report if nothing in the report matches the search query
   if ($search_restriction != NULL && !anything_in_report_matches_search($row, $laser_labs, $systems, $search_restriction)) {
      continue;
   }
   $iterated = True;
   
   echo '<h3>Laser Safety Report</h3>';
   echo '<table class="table table-responsive table-bordered">';
   echo '<tr><td class="col-md-4">Supervisor: <a href=' . rel_query(with_param($url_params, "supervisor", $row['supervisor'])) . 
        '>' . $row['supervisor'] . '</a></td>';
   echo     '<td class="col-md-2">Permit No: ' . $row['permit_number'] . '</td>';
   echo     '<td class="col-md-2">Date: ' . rearrange_sql_date($row['date']) . '</td></tr>';
   echo '<tr><td class="col-md-4">Office: ' . $row['office_room'] . ' ' . $row['office_building'] . '</td>';
   echo     '<td class="col-md-3" colspan="2">RSS Representative: ' . $row['rss_representative'] . '</td></tr>';
   echo '<tr><td class="col-md-4">Lab: ' . formatted_labs($laser_labs) . '</td>';
   echo     '<td class="col-md-3" colspan="2">Lab Personnel: ' . $row['lab_personnel'] . '</td></tr>';
   echo '</table>';
   
   $admin = get_laser_admin($conn, $row['laser_report_id']);
   $engr = get_laser_engr($conn, $row['laser_report_id']);
   if ($admin != NULL || engr != NULL) {
      echo '<table class="table table-responsive table-bordered">';
      echo '<thead><tr><th class="col-md-8">Audit item</th>';
      echo        '<th class="col-md-1">S</th><th class="col-md-1">U</th><th class="col-md-1">NA</th><th class="col-md-1">NC</th></tr></thead><tbody>';
      if ($admin != NULL) {
         echo '<tr><td colspan="5" style="text-align:center"><b>Procedure and Administrative Controls</b></td></tr>';
         echo '<tr><td>' . $operation_procedure_item . '</td>' . checkboxes($admin, 'operator_procedure_available') . '</tr>';
         echo '<tr><td>' . $alignment_procedure_item . '</td>' . checkboxes($admin, 'alignment_procedure_available') . '</tr>';
         echo '<tr><td>' . $min_power_item . '</td>' . checkboxes($admin, 'does_call_for_min_energy') . '</tr>';
         echo '<tr><td>' . $annual_training_item . '</td>' . checkboxes($admin, 'personnel_had_annual_training') . '</tr>';
         echo '<tr><td>' . $area_posted_item . '</td>' . checkboxes($admin, 'area_appropriately_posted') . '</tr>';
         echo '<tr><td>' . $eye_protection_item . '</td>' . checkboxes($admin, 'eye_protection_appropriate') . '</tr>';
         echo '<tr><td>' . $eyewear_condition_item . '</td>' . checkboxes($admin, 'eyewear_good_condition') . '</tr>';
         echo '<tr><td>' . $eyewear_wavelengths_item . '</td>' . checkboxes($admin, 'eyewear_specific_wavelengths') . '</tr>';
         echo '<tr><td>' . $eyewear_density_item . '</td>' . checkboxes($admin, 'eyewear_optical_density') . '</tr>';
         echo '<tr><td>' . $eyewear_labeled_item . '</td>' . checkboxes($admin, 'eyewear_od_wavelength_labeled') . '</tr>';
         echo '<tr><td>' . $eyewear_nhz_item . '</td>' . checkboxes($admin, 'eyewear_worn_in_nhz') . '</tr>';
         echo '<tr><td>' . $eyewear_inspection_item . '</td>' . checkboxes($admin, 'eyewear_inspection_documented') . '</tr>';
         echo '<tr><td>' . $clothing_available_item . '</td>' . checkboxes($admin, 'gloves_clothing_shields') . '</tr>';
      }
      if ($engr != NULL) {
         echo '<tr><td colspan="5" style="text-align:center"><b>Engineering Controls</b></td></tr>';
         echo '<tr><td>' . $protective_housing_item . '</td>' . checkboxes($engr, 'protective_housing') . '</tr>';
         echo '<tr><td>' . $safety_interlocks_item . '</td>' . checkboxes($engr, 'safety_interlocks') . '</tr>';
         echo '<tr><td>' . $laser_mounted_item . '</td>' . checkboxes($engr, 'laser_mounted_stable') . '</tr>';
         echo '<tr><td>' . $beam_eye_level_item . '</td>' . checkboxes($engr, 'beam_not_at_eye_level') . '</tr>';
         echo '<tr><td>' . $warning_system_item . '</td>' . checkboxes($engr, 'warning_system') . '</tr>';
         echo '<tr><td>' . $access_restricted_item . '</td>' . checkboxes($engr, 'access_restricted') . '</tr>';
         echo '<tr><td>' . $rapid_exit_item . '</td>' . checkboxes($engr, 'rapid_exit_pathway') . '</tr>';
         echo '<tr><td>' . $disconnect_switch_item . '</td>' . checkboxes($engr, 'emergency_disconnect_switch') . '</tr>';
         echo '<tr><td>' . $contact_list_item . '</td>' . checkboxes($engr, 'emergency_contact_list') . '</tr>';
         echo '<tr><td>' . $laser_terminus_item . '</td>' . checkboxes($engr, 'laser_fire_resistant_terminus') . '</tr>';
      }
      echo '</tbody></table>';
   }
   
   echo '<div><p>Comments: ' . str_replace('\n', '<br>', $row['comments']) . '</p></div>';
   
   if (count($systems) > 0) {
      // determine which headers to display
      $keys = array();
      foreach (array_keys($systems[0]) as $k) {
         if (is_numeric($k) || $k === "laser_report_id" || $k === "laser_id") {
            continue;
         }
         
         foreach ($systems as $sys) {
            if ($sys[$k] != NULL) {
               $keys[] = $k;
               break;
            }
         }
      }
      echo '<h3>Laser System Information</h3>';
      echo '<div class="table-wrapper">';
      echo '<table class="table table-responsive table-bordered">';
      echo '<thead><tr>';
      foreach ($keys as $k) {
         echo '<th>' . $k . '</th>';
      }
      echo '</tr></thead>';
      foreach ($systems as $sys) {
         echo '<tr>';
         foreach ($keys as $k) {
            echo '<td>' . trim(str_replace('\n', '<br>', $sys[$k]), '"') . '</td>';
         }
         echo '</tr>';
      }
      echo '</table></div>';
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
