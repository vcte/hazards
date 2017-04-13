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
    		<script type="text/javascript" src="scripts/bootstrap.min.js"></script>
    		
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
<div id="portal_app_info" style="float: right; margin-right: 10px;"></div>Dashboard</h2>

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
<?php
require 'database.php';
require 'helper.php';

$conn = db_connect();

$role = "safety_manager_or_dean";
$dept = NULL;

if ($role == "lab_pi") {
	include("pi_dashboard.php");
} else if ($role == "facility_manager") {
	include("fac_dashboard.php");
} else if ($role == "safety_manager_or_dean") {
	include("dean_dashboard.php");
}
?>

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
