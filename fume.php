<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Fume Hoods - My.ENGR Portal - U of I</title>
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
		<script src="scripts/prototype.js" language="javascript" type="text/javascript"></script>
		<script src="scripts/tablekit.js" language="javascript" type="text/javascript"></script>
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
<div id="portal_app_info" style="float: right; margin-right: 10px;"></div>Directory - Fume Hoods</h2>

<div class="clearfix" style="width: 100%;">
<div class="module_content">
<form method="get">

<div class="menu">
<ul>
<li><a href="dashboard.php">Dashboard</a></li>
<li><a href="lab.php">Lab Hazards</a></li>
<li><a href="facility.php">Facility Hazards</a></li>
<li><a href="radiation.php">Radiation Hazards</a></li>
<li><a href="laser.php">Laser Hazards</a></li>
<li class="on"><a href="fume.php">Fume Hoods</a></li>
<li><a href="report.php">Print Report</a></li>
</ul>
</div> <!-- menu close -->
<?php
require 'database.php';
require 'helper.php';

$conn = db_connect();
?>
<div class="clearfix">

<!-- Department drop down menu -->
<div class="fleft">
<label for="department">Select Department</label><br />
<select id="department" name="department" onchange="this.form.submit();" autocomplete="off">
<?php
$selected_dept = "";
$dept_param = isset($_GET['department']) ? $_GET['department'] : '';
echo '<option value=""' . ($dept_param == '' ? ' selected' : '') . '>All</OPTION>';
if ($conn) {
	try {
		$stmt = $conn->prepare("SELECT department_abbrev, department_fullname FROM department ORDER BY department_abbrev");
		$stmt->execute();
		$depts = $stmt->fetchAll();
		foreach ($depts as $index => $dept) {
			$abbrev = $dept["department_abbrev"];
			echo '<option value="' . $abbrev . '"';
			if ($dept_param == $abbrev) {
				echo " selected";
				$selected_dept = $dept_param;
			}
			echo ">" . str_replace("Engineering", "Engr.", $dept["department_fullname"]) . "</option>";
		}
	} catch (PDOException $e) {
		echo "<div>Could not access departments table: " . $e->getMessage() . "</div>";
		$conn = NULL;
	}
}
?>
</select>
</div> <!-- End of dept menu -->

<!-- Building drop down menu -->
<div class="fleft">
<label for="building">Building</label><br />
<select id="building" name="building" onchange="this.form.submit();" autocomplete="off">
<?php
$selected_bldg = "";
$bldg_param = isset($_GET['building']) ? $_GET['building'] : '';
echo '<option value=""' . ($bldg_param == '' ? ' selected' : '') . '>All</option>';
if ($conn) {
	try {
		$stmt = $conn->prepare("SELECT DISTINCT(building_name) FROM building");
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
		echo "<div>Could not access buildings table: " . $e->getMessage() . "</div>";
		$conn = NULL;
	}
}
?>
</select>
</div> <!-- End of building menu -->

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
echo '<input type="text" id="txtSearch" name="txtSearch" maxlength="50" size="25" value="' . filter_var($_GET['txtSearch'], FILTER_SANITIZE_STRING) . '" />';
?>
<input type="submit"/>
</div> <!-- End of search form -->

</div> <!-- End of clearfix -->

<!-- Fume hoods table -->
<table cellspacing="1" cellpadding="0" border="0" width="100%" class="gentable sortable" summary="" id="tablekit-table-1">
<tr>
<th  class="left sortcol">Hood ID</th>
<th  class="left sortcol">Building</th>
<th  class="left sortcol">Room</th>
<th  class="left sortcol">Location description</th>
<th  class="left sortcol">Date last surveyed</th>
<th  class="left sortcol">Department</th>
<th  class="left sortcol">Face velocity</th>
<th  class="left sortcol">Status</th>
<th  class="left sortcol">Item type</th>
<th  class="left sortcol">Path</th>
</tr>
<?php
// helper functions
function rel_query($params) {
   return '"/~fsdatabase/fume.php?' . http_build_query($params) . '"';
}

$search_restriction = NULL;
if (isset($_GET['txtSearch']) && $_GET['txtSearch'] != '') {
	$search_restriction = preg_quote(addslashes($_GET['txtSearch']));
}

$offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
$url_params = remove_empty($_GET);
if ($conn) {
	try {
		$filter_list = array();
		if ($selected_dept != '') {
			array_push($filter_list, 'department="' . $selected_dept . '" ');
		}
		
		if ($selected_bldg != '') {
			array_push($filter_list, 'building_name="' . $selected_bldg . '" ');
		}
		
		if ($selected_start != '') {
			array_push($filter_list, 'date_last_surveyed >= "' . rearrange_gui_date($selected_start) . '" ');
		}
		
		if ($selected_end != '') {
			array_push($filter_list, 'date_last_surveyed <= "' . rearrange_gui_date($selected_end) . '" ');
		}
		
		if ($search_restriction != NULL) {
            $search_filters = array();
            foreach (array("hood_id", "building_name", "room", "location_description", "department", "face_velocity", "fh_status", "item_type", "path") as $col_name) {
                array_push($search_filters, $col_name . ' LIKE "%' . $search_restriction . '%"');
            }
            array_push($filter_list, '(' . join(' || ', $search_filters) . ')');
        }
		
		// calculate total number of entries
		$filter = count($filter_list) > 0 ? " WHERE " . join(" && ", $filter_list) : "";
		$query_tot = "SELECT COUNT(*) FROM fume_hood " . $filter;
		$stmt = $conn->prepare($query_tot);
		$stmt->execute();
		$total = $stmt->fetchAll()[0][0];
		
		// calculate new offset
		if (isset($_GET['nav'])) {
			$end_offset = (int) floor($total / 100) * 100;
			if ($_GET['nav'] == '<<') {
				$offset = 0;
			} else if ($_GET['nav'] == '<') {
				$offset = max($offset - 100, 0);
			} else if ($_GET['nav'] == '>') {
				$offset = min($offset + 100, $end_offset);
			} else if ($_GET['nav'] == '>>') {
				$offset = $end_offset;
			}
		} else {
			$offset = 0;
		}
		
		// retrieve limited number of entries
		$query = "SELECT * FROM fume_hood " . $filter . " LIMIT 100" . " OFFSET " . $offset;
		$stmt = $conn->prepare($query);
		$stmt->execute();
		$fume_hoods = $stmt->fetchAll();
		foreach ($fume_hoods as $fh_row) {
			echo "<tr>";
			echo '<td class="left">' . $fh_row["hood_id"] . '</td>';
			echo '<td class="left"><a href=' . rel_query(with_param($url_params, "building", $fh_row["building_name"])) .
			     '>' . $fh_row["building_name"] . '</a></td>';
			echo '<td class="left"><a href=' . 
			     rel_query(with_param(with_param($url_params, "building", $fh_row["building_name"]), "txtSearch", $fh_row["room"])) . 
			     '>' . $fh_row["room"] . '</a></td>';
			echo '<td class="left">' . $fh_row["location_description"] . '</td>';
			echo '<td class="left">' . rearrange_sql_date($fh_row["date_last_surveyed"]) . '</td>';
			echo '<td class="left"><a href=' . rel_query(with_param($url_params, "department", $fh_row["department"])) . 
			     '>' . $fh_row["department"] . '</a></td>';
			echo '<td class="left">' . $fh_row["face_velocity"] . '</td>';
			echo '<td class="left">' . $fh_row["fh_status"] . '</td>';
			echo '<td class="left">' . $fh_row["item_type"] . '</td>';
			echo '<td class="left">' . $fh_row["path"] . '</td>';
			echo "</tr>";
		}
	} catch (PDOException $e) {
		echo "<div>Could not access fume hood table: " . $e->getMessage() . "</div>";
		$conn = NULL;
	}
} else {
	echo "<div>No connection to database</div>";
}
?>
</table>
<?php
echo '<div>';
echo '<input type="hidden" name="offset" value="' . $offset . '"/>';
if ($offset > 0) {
	echo '<input type="submit" name="nav" title = "First" value="<<"/>';
	echo '<input type="submit" name="nav" title = "Previous" value="<"/>';
}
if ($offset < $total - 100) {
	echo '<input type="submit" name="nav" title = "Next" value=">"/>';
	echo '<input type="submit" name="nav" title = "Last" value=">>"/>';
}
echo '</div>';

echo "<div>";
if ($fume_hoods) {
	echo "Showing results " . $offset . " to " . min($offset + count($fume_hoods), $total) . " (" . $total . " total)";
} else {
	echo "No results found";
}
echo "</div>";
?>

<script type="text/javascript">
    TableKit.Sortable.detectors = $w('date date-us time currency datasize number casesensitivetext text');

    TableKit.Sortable.addSortType(
	    new TableKit.Sortable.Type('number', {
	        pattern: /^[-+]?[\d]*\.?[\d]+(?:[eE][-+]?[\d]+)?/,
	        normal: function (v) {
	            // This will grab the first thing that looks like a number from a string, so you can use it to order a column of various srings containing numbers.
	            v = parseFloat(v.replace(/^.*?([-+]?[\d]*\.?[\d]+(?:[eE][-+]?[\d]+)?).*$/, "$1"));
	            return isNaN(v) ? 0 : v;
	        }
	    })
    );

    document.observe('dom:loaded', function () {
        $$('table').each(function (elem) {
            TableKit.Sortable.init(elem, { editable: false });
        });
    });
    
    $j('.dp').datepicker({ format: 'mm/dd/yyyy'});
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
