<?php
$perc_hazards_mitigated = get_percent_mitigated_hazards($conn, $dept, $date_start, $date_end, $type_restriction);
$days_to_mitigation = get_avg_days_to_mitigation($conn, $dept, $date_start, $date_end, $type_restriction);

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
?>

<div class="container">
   <div class="row">
      <div class="col-lg-6"><h3>Percentage of <?php echo $type_restriction; ?> hazards mitigated</h3></div>
      <div class="col-lg-6"><h3>PI's with the most <?php echo $type_restriction; ?> hazards
      <div class="btn-group" role="group" aria-label="...">
         <button id="button-horiz-chart" type="button" class="btn btn-default active" aria-label="Display Horizontal Bar Chart">
            <span class="glyphicon glyphicon-align-left" aria-hidden="true"></span>
         </button>
         <button id="button-pi-table" type="button" class="btn btn-default" aria-label="Display Table">
            <span class="glyphicon glyphicon-th-list" aria-hidden="true"></span>
         </button>
      </div></h3></div>
   </div>
   <div class="row">
      <div class="col-lg-6"> <div id="dial"></div> </div>
      <div class="col-lg-6">
         <svg width="540" height="500" id="horiz-chart"></svg>

         <div class="table-responsive">
            <table id="pi-table" class="table table-striped" style="display: none;"> 
               <tr><th>Department</th><th>PI name</th><th># lab hazards</th></tr>
               <?php
               $dept_and_pi_with_most_hazards = array();
               $depts = get_all_dept_abbrev($conn);
               foreach ($depts as $row) {
                  $dept = $row[0];
                  $pis_most_hazards_per_dept = get_pi_most_lab_hazards($conn, $dept, 1, $date_start, $date_end, $type_restriction);
                  if (count($pis_most_hazards_per_dept) > 0) {
                     $pi_name = $pis_most_hazards_per_dept[0][0];
                     echo '<tr>';
                     echo '<td><a href="/~fsdatabase/' . $type_url_param . '?department=' . $dept . $date_url_params . '">' . $dept . '</a></td>';
                     echo '<td><a href="/~fsdatabase/' . $type_url_param . '?pi_name=' . urlencode($pi_name) . $date_url_params . '">' . $pi_name . '</a></td>';
                     echo '<td>' . $pis_most_hazards_per_dept[0][1] . '</td>';
                     echo '</tr>';
                     $dept_and_pi_with_most_hazards[] = array($dept, $pi_name, $pis_most_hazards_per_dept[0][1]);
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
      <div class="col-lg-6"><h3>Average time to mitigation (in days)</h3></div>
      <div class="col-lg-6"><h3>Top 5 issues college-wide
      <div class="btn-group" role="group" aria-label="...">
         <button id="button-pie-chart" type="button" class="btn btn-default active" aria-label="Display Pie Chart">
            <span class="glyphicon glyphicon-adjust" aria-hidden="true"></span>
         </button>
         <button id="button-issues" type="button" class="btn btn-default" aria-label="Display Table">
            <span class="glyphicon glyphicon-th-list" aria-hidden="true"></span>
         </button>
      </div>
      </h3></div>
   </div>
   <div class="row">
      <div class="col-lg-6">
          <div id="delay-gauge"> <?php if ($days_to_mitigation == NULL) echo "No mitigation dates have been reported!"; ?> </div>
      </div>
      <div class="col-lg-6">
         <svg width="540" height="320" id="pie-chart"></svg>
         <br>
         
         <div class="table-responsive">
            <table id="issues-table" class="table table-striped" style="display: none;"> 
               <tr><th>Observation code</th><th>Count</th></tr>
               <?php
               $top_issues = get_top_issues($conn, NULL, 5, $date_start, $date_end, $type_restriction);
               $all_issues = get_top_issues($conn, NULL, NULL, $date_start, $date_end, $type_restriction);
               foreach ($top_issues as $row) {
                  echo '<tr>';
                  echo '<td><a href="/~fsdatabase/' . $type_url_param . '?observ_code=' . urlencode($row[0]) . $date_url_params . '">' . $row[0] . '</a></td>';
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
                  $issues = get_top_issues($conn, $dept, 3, $date_start, $date_end, $type_restriction);
                  $issues_by_dept[] = array($dept, $issues);
                  echo '<tr>';
                  echo '<td><a href="/~fsdatabase/' . $type_url_param . '?department=' . $dept . $date_url_params . '">' . $dept . '</a></td>';
                  for ($i = 0; $i < 3; $i++) {
                     echo '<td>';
                     if (isset($issues[$i])) 
                        echo '<a href="/~fsdatabase/' . $type_url_param . '?department=' . $dept . '&observ_code=' . urlencode($issues[$i][0]) . 
                             $date_url_params . '">' . $issues[$i][0] . '</a>';
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

<?php 
$dial_perc = $perc_hazards_mitigated;
include("dial.php");
?>

<?php include ("horiz_chart.php"); ?>

<?php include ("bar_chart.php"); ?>

<?php include ("pie_chart.php"); ?>

<script>
<?php
if ($days_to_mitigation != NULL) {
 echo 'var gauge = new Gauge("delay-gauge");';
 echo 'gauge.render(' . round($days_to_mitigation) . ');';
}
?>
</script>

<script>
// toggle between horizontal bar chart and table to display pi's with most lab hazards
$('#button-horiz-chart').on('click', function (e) {
   $('#button-horiz-chart').addClass("active");
   $('#button-pi-table').removeClass("active");
   $('#horiz-chart').css('display', 'initial');
   $('#pi-table').css('display', 'none');
});

$('#button-pi-table').on('click', function (e) {
   $('#button-pi-table').addClass("active");
   $('#button-horiz-chart').removeClass("active");
   $('#pi-table').css('display', '');
   $('#horiz-chart').css('display', 'none');
});

// toggle between pie chart and table to display top hazards college-wide
$('#button-pie-chart').on('click', function (e) {
   $('#button-pie-chart').addClass("active");
   $('#button-issues').removeClass("active");
   $('#pie-chart').css('display', 'initial');
   $('#issues-table').css('display', 'none');
});

$('#button-issues').on('click', function (e) {
   $('#button-issues').addClass("active");
   $('#button-pie-chart').removeClass("active");
   $('#issues-table').css('display', '');
   $('#pie-chart').css('display', 'none');
});

// toggle between grouped bar chart and table to display top hazards for each department
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
