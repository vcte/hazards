<?php
require 'database.php';
require 'helper.php';
require 'dropdown.php';

$conn = db_connect($role);
?>

<form method="get">
<div class="clearfix">

<!-- Department drop down menu -->
<?php $selected_dept = echo_dept_menu($conn, $role, $dept); ?>

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

<!-- PI name drop down menu -->
<div class="fleft">
<label for="pi_name">PI name</label><br />
<select id="pi_name" name="pi_name" onchange="this.form.submit();" autocomplete="off">
<?php
$selected_pi = "";
$pi_param = isset($_GET['pi_name']) ? $_GET['pi_name'] : '';
echo '<option value=""' . ($pi_param == '' ? ' selected' : '') . '>All</option>';
if ($conn) {
	try {
		$stmt = $conn->prepare("SELECT DISTINCT(pi_name) FROM hazard ORDER BY pi_name");
		$stmt->execute();
		$pi_names = $stmt->fetchAll();
		foreach ($pi_names as $index => $pi_name) {
			echo '<option value="' . $pi_name["pi_name"] . '"';
			if ($pi_param == $pi_name["pi_name"]) {
				echo " selected";
				$selected_pi = $pi_param;
			}
			echo ">" . $pi_name["pi_name"] . "</option>";
		}
	} catch (PDOException $e) {
		echo "<div>Could not access hazard table: " . $e->getMessage() . "</div>";
		$conn = NULL;
	}
}
?>
</select>
</div> <!-- End of PI name menu -->

<!-- Observation code drop down menu -->
<div class="fleft">
<label for="observ_code">Observation Code</label><br />
<select id="observ_code" name="observ_code" onchange="this.form.submit();" autocomplete="off">
<?php
$selected_observ = "";
$observ_param = isset($_GET['observ_code']) ? $_GET['observ_code'] : '';
echo '<option value=""' . ($observ_param == '' ? ' selected' : '') . '>All</option>';
if ($conn) {
	try {
		$stmt = $conn->prepare("SELECT DISTINCT(observ_code) FROM hazard ORDER BY observ_code");
		$stmt->execute();
		$observ_codes = $stmt->fetchAll();
		foreach ($observ_codes as $index => $code) {
			echo '<option value="' . $code["observ_code"] . '"';
			if ($observ_param == $code["observ_code"]) {
				echo " selected";
				$selected_observ = $observ_param;
			}
			echo ">" . $code["observ_code"] . "</option>";
		}
	} catch (PDOException $e) {
		echo "<div>Could not access hazards table: " . $e->getMessage() . "</div>";
		$conn = NULL;
	}
}
?>
</select>
</div> <!-- End of observation code menu -->

<!-- Hazard rank drop down menu -->
<div class="fleft">
<label for="hazard_rank">Hazard Rank</label><br />
<select id="hazard_rank" name="hazard_rank" onchange="this.form.submit();" autocomplete="off" style="min-width: 75px;">
<?php
$selected_rank = "";
$rank_param = isset($_GET['hazard_rank']) ? $_GET['hazard_rank'] : '';
echo '<option value=""' . ($rank_param == '' ? ' selected' : '') . '>All</option>';
if ($conn) {
	try {
		$stmt = $conn->prepare("SELECT rank_id FROM hazard_rank");
		$stmt->execute();
		$ranks = $stmt->fetchAll();
		foreach ($ranks as $index => $rank) {
			echo '<option value="' . $rank["rank_id"] . '"';
			if ($rank_param == $rank["rank_id"]) {
				echo " selected";
				$selected_rank = $rank_param;
			}
			echo ">" . $rank["rank_id"] . "</option>";
		}
	} catch (PDOException $e) {
		echo "<div>Could not access hazard ranks table: " . $e->getMessage() . "</div>";
		$conn = NULL;
	}
}
?>
</select>
</div> <!-- End of hazard rank menu -->

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

<!-- Responsible party drop down menu -->
<div class="fleft">
<label for="party">Responsible party</label><br />
<select id="party" name="party" onchange="this.form.submit();" autocomplete="off" style="min-width: 100px;">
<?php
$selected_party = "";
$party_param = isset($_GET['party']) ? $_GET['party'] : '';
echo '<option value=""' . ($party_param == '' ? ' selected' : '') . '>All</option>';
if ($conn) {
	try {
		$stmt = $conn->prepare("SELECT DISTINCT(responsible_party) FROM hazard ORDER BY responsible_party");
		$stmt->execute();
		$responsible_parties = $stmt->fetchAll();
		$responsible_parties = array_unique(array_map(function($p) { return preg_replace('/^[^A-Za-z]|[^A-Za-z]$/', '', $p[0]); }, $responsible_parties));
		foreach ($responsible_parties as $party) {
			echo '<option value="' . $party . '"';
			if ($party_param == $party) {
				echo " selected";
				$selected_party = $party_param;
			}
			echo ">" . $party . "</option>";
		}
	} catch (PDOException $e) {
		echo "<div>Could not access hazards table: " . $e->getMessage() . "</div>";
		$conn = NULL;
	}
}
?>
</select>
</div> <!-- End of responsible party menu -->

<!-- Mitigated drop down menu -->
<div class="fleft">
<label for="mitigated">Mitigated</label><br />
<select id="mitigated" name="mitigated" onchange="this.form.submit();" autocomplete="off" style="min-width: 75px;">
<?php
$selected_mitig = "";
$mitig_param = isset($_GET['mitigated']) ? $_GET['mitigated'] : '';
echo '<option value=""' . ($mitig_param == '' ? ' selected' : '') . '>All</option>';
$mitig_options = ["Yes", "No"];
foreach ($mitig_options as $mitig) {
	echo '<option value="' . $mitig . '"';
	if ($mitig_param == $mitig) {
		echo " selected";
		$selected_mitig = $mitig_param;
	}
	echo ">" . $mitig . "</option>";
}
?>
</select>
</div> <!-- End of responsible party menu -->

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

<!-- Hazards table -->
<table cellspacing="1" cellpadding="0" border="0" width="100%" class="gentable sortable" summary="" id="tablekit-table-1">
<tr>
<th  class="left sortcol">Date</th>
<th  class="left sortcol">Department</th>
<th  class="left sortcol">PI name</th>
<th  class="left sortcol">IRU</th>
<th  class="left sortcol">Building</th>
<th  class="left sortcol">Room</th>
<th  class="left sortcol">Audit Type</th>
<th  class="left sortcol">Issue</th>
<th  class="left sortcol">Observation code</th>
<th  class="left sortcol">Hazard Rank</th>
<th  class="left sortcol">Resonsible Party</th>
<th  class="left sortcol">Mitigated</th>
<th  class="left sortcol">Date Mitigated</th>
</tr>
<?php
// helper functions
function rel_query($params) {
   global $selected_audit_type;
   return '"/~fsdatabase/' . ($selected_audit_type == 'Lab' ? 'lab.php' : 'facility.php') . '?' . http_build_query($params) . '"';
}

$offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
$url_params = remove_empty($_GET);
if ($conn) {
   try {
      // determine attribute filters
      $filter_list = array();
      if (isset($selected_audit_type) && $selected_audit_type != '') {
         array_push($filter_list, 'audit_type="' . $selected_audit_type . '"');
      }
      
      if ($role != "safety_manager_or_dean") {
         array_push($filter_list, 'dept="' . $dept . '"');
      } else if ($selected_dept != '') {
         array_push($filter_list, 'dept="' . $selected_dept . '"');
      }
      
      if (!empty($selected_bldg)) {
         array_push($filter_list, 'building="' . $selected_bldg . '"');
      }
      
      if (!empty($selected_pi)) {
         array_push($filter_list, 'pi_name="' . $selected_pi . '"');
      }
      
      if (!empty($selected_observ)) {
         array_push($filter_list, 'observ_code="' . $selected_observ . '"');
      }
      
      // NOTE: there should NOT be a rank "0", or else empty will behave unexpectedly 
      if (!empty($selected_rank)) {
         array_push($filter_list, '(hazard_rank="' . $selected_rank . '" OR hazard_rank LIKE "_' . $selected_rank . '")');
      }
      
      if (!empty($selected_start)) {
         array_push($filter_list, 'date >= "' . rearrange_gui_date($selected_start) . '"');
      }
      
      if (!empty($selected_end)) {
         array_push($filter_list, 'date <= "' . rearrange_gui_date($selected_end) . '"');
      }
      
      if (!empty($selected_party)) {
         array_push($filter_list, 'responsible_party LIKE "%' . $selected_party . '%"');
      }
      
      if (!empty($selected_mitig)) {
         array_push($filter_list, ($selected_mitig == "Yes" ? "mitigated = 1" : "(mitigated IS NULL OR mitigated = 0)"));
      }
      
      if (isset($_GET['txtSearch']) && $_GET['txtSearch'] != '') {
         $search_filters = array();
         foreach (array('dept', 'pi_name', 'iru', 'building', 'room', 'audit_type', 'issue', 'observ_code', 'hazard_rank', 'responsible_party') as $col_name) {
            array_push($search_filters, $col_name . ' LIKE "%' . addslashes($_GET['txtSearch']) . '%"');
         }
         array_push($filter_list, '(' . join(' || ', $search_filters) . ')');
      }
      
      // calculate total number of entries
      $filter = count($filter_list) > 0 ? " WHERE " . join(" && ", $filter_list) : "";
      $query_tot = "SELECT COUNT(*) FROM hazard " . $filter;
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
      $query = "SELECT * FROM hazard " . $filter . " LIMIT 100" . " OFFSET " . $offset;
      $stmt = $conn->prepare($query);
      $stmt->execute();
      $hazards = $stmt->fetchAll();
      foreach ($hazards as $hazard_row) {
         echo "<tr>";
         echo '<td class="left">' . rearrange_sql_date($hazard_row["date"]) . '</td>';
         echo '<td class="left"><a href=' . rel_query(with_param($url_params, "department", $hazard_row["dept"])) . '>' . $hazard_row["dept"] . '</a></td>';
         echo '<td class="left"><a href=' . rel_query(with_param($url_params, "pi_name", $hazard_row["pi_name"])) . '>' . $hazard_row["pi_name"] . '</a></td>';
         echo '<td class="left">' . $hazard_row["iru"] . '</td>';
         echo '<td class="left"><a href=' . rel_query(with_param($url_params, "building", $hazard_row["building"])) . '>' . $hazard_row["building"] . '</a></td>';
         echo '<td class="left"><a href=' . rel_query(with_param(with_param($url_params, "building", $hazard_row["building"]), "txtSearch", $hazard_row["room"])) .
              '>' . $hazard_row["room"] . '</a></td>';
         echo '<td class="left">' . $hazard_row["audit_type"] . '</td>';
         echo '<td class="left">' . htmlspecialchars($hazard_row["issue"]) . '</td>';
         echo '<td class="left"><a href=' . rel_query(with_param($url_params, "observ_code", $hazard_row["observ_code"])) . 
              '>' . $hazard_row["observ_code"] . '</a></td>';
         echo '<td class="left"><a href=' . rel_query(with_param($url_params, "hazard_rank", $hazard_row["hazard_rank"])) . 
              '>' . $hazard_row["hazard_rank"] . '</a></td>';
         echo '<td class="left"><a href=' . rel_query(with_param($url_params, "party", $hazard_row["responsible_party"])) . 
              '>' . $hazard_row["responsible_party"] . '</td>';
              
         // temporary icons
        if ($hazard_row["mitigated"]) {
             echo '<td class="left"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></td>';
         } else {
             echo '<td class="left"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></td>';
         }
         echo '<td class="left">' . rearrange_sql_date($hazard_row["date_mitigated"]) . '</td>';         
         
         // TODO - real time updates
         //echo '<td class="left">' . /*<input type="checkbox" name="is_mitigated_' . $hazard_row["hazard_id"] . 
         //     '" value="yes"' . ($hazard_row["mitigated"] ? ' checked=true' : '') . '>*/ '</td>';
         //echo '<div class="input-group date" data-provide="datepicker">' .
            //'<input type="text" class="form-control">' +
               //'<div class="input-group-addon">' +
                  //'<span class="glyphicon glyphicon-th"></span>' +
               //'</div>' +
            //'</div>'
         //echo '<td class="left input-group date"></td>'; /*<input type="text" class="dp form-control" style="display: block !important;" ';
         //echo 'value="' . rearrange_date($hazard_row["date_mitigated"], "y-m-d", "m/d/y") . '">*/
         echo "</tr>";
      }
   } catch (PDOException $e) {
      echo "<div>Could not access hazards table: " . $e->getMessage() . "</div>";
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
if ($hazards) {
	echo "Showing results " . $offset . " to " . min($offset + count($hazards), $total) . " (" . $total . " total)";
} else {
	echo "No results found";
}
echo "</div>";
?>
<script type="text/javascript">
    TableKit.Sortable.detectors = $w('date date-us time datasize number casesensitivetext text');

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