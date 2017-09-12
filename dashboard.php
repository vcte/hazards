<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Dashboard - My.ENGR Portal - U of I</title>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
		<meta name="keywords" content="" />

		<link rel="SHORTCUT ICON" href="/~fsdatabase/favicon.ico" />
		<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700" />
		<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Open+Sans:400,700,400italic" />
		<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Oswald:400,700,300" />
		<link rel="stylesheet" type="text/css" href="style/datepicker3.min.css" />
		<link rel="stylesheet" type="text/css" href="style/gentable.css" />
		<link rel="stylesheet" type="text/css" href="style/engr.css" />
		<link rel="stylesheet" type="text/css" href="style/bootstrap.min.css" />
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<script type="text/javascript" src="https://d3js.org/d3.v4.min.js"></script>
    	<script type="text/javascript" src="scripts/dial_chart.js"></script>
    	<script type="text/javascript" src="scripts/gauge.js"></script>
    	<script type="text/javascript" src="scripts/bootstrap.min.js"></script>
    		
	</head>

<body>
<div id="page">
	<?php
	include("banner.php");
	?>
	
<div id="content">
    
<?php
require 'database.php';
require 'helper.php';

$conn = db_connect($role);
?>

<div class="module">
<div class="module_top"><span></span></div>
<h2><a href="/" style="float: right;">Return</a>
<div id="portal_app_info" style="float: right; margin-right: 10px;"></div>
<?php echo $role === "safety_manager_or_dean" ? "Dean Dashboard" : "Dashboard - " . $dept ?></h2>

<div class="clearfix" style="width: 100%;">
<div class="module_content">

<div class="menu">
<ul>
<li class="on"><a href="dashboard.php">Dashboard</a></li>
<li><a href="lab.php">Lab Hazards</a></li>
<li><a href="facility.php">Facility Hazards</a></li>
<li><a href="radiation.php">Radiation Hazards</a></li>
<li><a href="laser.php">Laser Hazards</a></li>
<li><a href="fume.php">Fume Hoods</a></li>
<li><a href="report.php">Print Report</a></li>
</ul>
</div> <!-- menu close -->

<form method="get">

<!-- Fiscal year drop down menu -->
<div class="fleft">
<label for="year">Select Fiscal Year</label><br />
<select id="year" name="year" onchange="this.form.submit();" autocomplete="off" style="min-width:100px">
<?php
$selected_year = "";
$year_param = isset($_GET['year']) ? $_GET['year'] : '';
echo '<option value=""' . ($year_param == '' ? ' selected' : '') . '>All</OPTION>';
if ($conn) {
	$years = get_all_hazard_fiscal_years($conn);
	foreach ($years as $year) {
		echo '<option value="' . $year . '"';
		if ($year_param == $year) {
			echo " selected";
			$selected_year = $year_param;
		}
		echo ">" . $year . "</option>";
	}
}

if ($selected_year != "") {
    $date_start = ($selected_year - 1) . '-7-01';
    $date_end   = ($selected_year)     . '-6-30';
} else {
    $date_start = NULL;
    $date_end   = NULL;
}
?>
</select>
</div> <!-- End of fiscal year menu -->

<!-- Audit type drop down menu -->
<div class="fleft">
<label for="type">Select Audit type</label><br />
<select id="type" name="type" onchange="this.form.submit();" autocomplete="off" style="min-width:100px">
<?php
$selected_type = "";
$type_param = isset($_GET['type']) ? $_GET['type'] : 'Lab';
if ($conn) {
    $types = array("Lab", "OSHA");
	foreach ($types as $type) {
		echo '<option value="' . $type . '"';
		if ($type_param == $type) {
			echo " selected";
			$selected_type = $type_param;
		}
		echo ">" . $type . "</option>";
	}
}
$type_restriction = $selected_type != "" ? $selected_type : NULL;
?>
</select>
</div> <!-- End of Audit type menu -->

<br><br><br><br>

<?php
$date_url_params = "";
if ($date_start != NULL) {
    $date_url_params .= "&date_start=" . urlencode(rearrange_sql_date($date_start));
}
if ($date_end != NULL) {
    $date_url_params .= "&date_end=" . urlencode(rearrange_sql_date($date_end));
}

$type_url_param = "lab.php";
if ($type_restriction == "OSHA") {
    $type_url_param = "facility.php";
}

if ($role == "lab_pi") {
	include("pi_dashboard.php");
} else if ($role == "facility_manager") {
	include("fac_dashboard.php");
} else if ($role == "safety_manager_or_dean") {
	include("dean_dashboard.php");
}
?>

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
