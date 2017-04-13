<?php
$perc_hazards_mitigated = get_percent_mitigated_hazards($conn, $dept);
$days_to_mitigation = get_avg_days_to_mitigation($conn, $dept);
?>

<div class="container">
   <div class="row">
      <div class="col-lg-6"><h3>Percentage of lab hazards mitigated</h3></div>
      <div class="col-lg-6"><h3>PI's with the most lab hazards</h3></div>
   </div>
   <div class="row">
      <div class="col-lg-6"> <div id="dial"></div> </div>
      <div class="col-lg-6">
         <div class="table-responsive">
            <table class="table table-striped"> 
               <tr><th>Department</th><th>PI name</th><th># lab hazards</th></tr>
               <?php
               $depts = get_all_dept_abbrev($conn);
               foreach ($depts as $row) {
                  $dept = $row[0];
                  $pis_most_hazards_per_dept = get_pi_most_lab_hazards($conn, $dept, 1);
                  if (count($pis_most_hazards_per_dept) > 0) {
                     $pi_name = $pis_most_hazards_per_dept[0][0];
                     echo '<tr>';
                     echo '<td><a href="/~fsdatabase/lab.php?department=' . $dept . '">' . $dept . '</a></td>';
                     echo '<td><a href="/~fsdatabase/lab.php?pi_name=' . urlencode($pi_name) . '">' . $pi_name . '</a></td>';
                     echo '<td>' . $pis_most_hazards_per_dept[0][1] . '</td>';
                     echo '</tr>';
                  }
               }
               ?>
            </table>
         </div>
      </div>
   </div>
</div>	<!-- close container -->

<div class="container">
   <div class="row">
      <div class="col-lg-6"><h3>Average time to mitigation</h3></div>
      <div class="col-lg-6"><h3>Top 5 issues college-wide</h3></div>
   </div>
   <div class="row">
      <div class="col-lg-6">
         <?php
         if ($days_to_mitigation != NULL) {
         	echo round($days_to_mitigation, 1);
         	echo " days.";
         } else {
         	echo "No mitigation dates have been reported!";
         }
         ?>
      </div>
      <div class="col-lg-6">
         <div class="table-responsive">
            <table class="table table-striped"> 
               <tr><th>Observation code</th><th>Count</th></tr>
               <?php
               $issues = get_top_issues($conn, NULL, 5);
               foreach ($issues as $row) {
                  echo '<tr>';
                  echo '<td><a href="/~fsdatabase/lab.php?observ_code=' . urlencode($row[0]) . '">' . $row[0] . '</a></td>';
                  echo '<td>' . $row[1] . '</td>';
                  echo '</tr>';
               }
               ?>
            </table>
         </div>
      </div>
   </div>
</div>	<!-- close container -->

<div class="container">
   <div class="row">
      <div class="col-lg-12"><h3>Top 3 issues per department
      <div class="btn-group" role="group" aria-label="...">
         <button id="button-graph" type="button" class="btn btn-default active" aria-label="Display Graph">
            <span class="glyphicon glyphicon-signal" aria-hidden="true"></span>
         </button>
         <button id="button-table" type="button" class="btn btn-default" aria-label="Display Table">
            <span class="glyphicon glyphicon-th-list" aria-hidden="true"></span>
         </button>
      </div>
      </h3>
      </div>
   </div>
   <div class="row">
      <div class="col-lg-12">
         <svg width="1200" height="600" id="bar-chart"></svg>
         <br>
         <div class="table-responsive">
            <table id="hazard-table" class="table table-striped" style="display: none;"> 
               <tr>
                  <th class="col-lg-1">Department</th>
                  <th class="col-lg-2">Issue #1</th><th class="col-lg-1">Count #1</th><th class="col-lg-1"></th>
                  <th class="col-lg-2">Issue #2</th><th class="col-lg-1">Count #2</th><th class="col-lg-1"></th>
                  <th class="col-lg-2">Issue #3</th><th class="col-lg-1">Count #3</th>
               </tr>
               <?php
               $issues_by_dept = array();
               $depts = get_all_dept_abbrev($conn);
               foreach ($depts as $row) {
                  $dept = $row[0];
                  $issues = get_top_issues($conn, $dept, 3);
                  $issues_by_dept[] = array($dept, $issues);
                  echo '<tr>';
                  echo '<td><a href="/~fsdatabase/lab.php?department=' . $dept . '">' . $dept . '</a></td>';
                  for ($i = 0; $i < 3; $i++) {
                     echo '<td>';
                     if (isset($issues[$i])) 
                        echo '<a href="/~fsdatabase/lab.php?department=' . $dept . '&observ_code=' . urlencode($issues[$i][0]) . '">' . $issues[$i][0] . '</a>';
                     echo '</td>';
                     echo '<td>' . (isset($issues[$i]) ? $issues[$i][1] : '') . '</td>';
                     
                     // insert padding to clearly separate different (issue, count) pairs
                     if ($i != 2) echo '<td></td>';
                  }
                  echo '</tr>';
               }
               ?>
            </table>
         </div>
      </div>
   </div>
</div>  <!-- close container -->

<?php include("dial.php"); ?>

<?php include ("bar_chart.php"); ?>

<script>
$('#button-graph').on('click', function (e) {
   $('#button-graph').addClass("active");
   $('#button-table').removeClass("active");
   $('#bar-chart').css('display', 'initial');
   $('#hazard-table').css('display', 'none');
});

$('#button-table').on('click', function (e) {
   $('#button-table').addClass("active");
   $('#button-graph').removeClass("active");
   $('#hazard-table').css('display', 'initial');
   $('#bar-chart').css('display', 'none');
});
</script>
