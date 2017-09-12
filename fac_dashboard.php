<?php
$perc_hazards_mitigated = get_percent_mitigated_hazards($conn, $dept, $date_start, $date_end, $type_restriction);
$days_to_mitigation = get_avg_days_to_mitigation($conn, $dept, $date_start, $date_end, $type_restriction);
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
      <div class="col-lg-6" id="dial"></div>
      
      <div class="col-lg-6">
         <br>
         <svg width="540" height="500" id="horiz-chart"></svg>
         <div class="table-responsive">
            <table id="pi-table" class="table table-striped" style="display: none;"> 
               <tr><th>PI name</th><th>Number of lab hazards</th></tr>
               <?php
               $pis_with_most_hazards = array();
               $pis_most_hazards = get_pi_most_lab_hazards($conn, $dept, 5, $date_start, $date_end, $type_restriction);
               foreach ($pis_most_hazards as $row) {
                  $pi_name = $row[0];
                  echo '<tr>';
                  echo '<td><a href="/~fsdatabase/' . $type_url_param . '?pi_name=' . urlencode($pi_name) . '">' . $pi_name . '</a></td>';
                  echo '<td>' . $row[1] . '</td>';
                  echo '</tr>';
                  
                  $pis_with_most_hazards[] = array($dept, $pi_name, $row[1]);
               }
               $dept_and_pis_with_most_hazards = array($pis_with_most_hazards);
               ?>
            </table>
         </div>
      </div>
   </div>
</div>	<!-- close container -->

<div class="container">
   <div class="row">
       <div class="col-lg-6"><h3>Average time to mitigation (in days)</h3></div>
   </div>
   <div class="row">
       <div class="col-lg-6">
         <div id="delay-gauge"> <?php if ($days_to_mitigation == NULL) echo "No mitigation dates have been reported!"; ?> </div>
      </div>
   </div>
</div>	<!-- close container -->

<?php
$dial_perc = $perc_hazards_mitigated;
$dial_radius = 160;
include("dial.php");
?>

<?php
if (count($pis_most_hazards) > 0) {
 include ("horiz_chart.php");
}
?>

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
</script>
