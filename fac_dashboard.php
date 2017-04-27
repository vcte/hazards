<div class="container">
   <div class="row">
      <div class="col-lg-4"><h3>Percentage of lab hazards mitigated</h3></div>
      <div class="col-lg-4"><h3>PIs with the most lab hazards</h3></div>
      <div class="col-lg-4"><h3>Average time to mitigation</h3></div>
   </div>
   <div class="row">
      <div class="col-lg-4" id="chart"></div>
      <div class="col-lg-4">
         <div class="table-responsive">
            <table class="table table-striped"> 
               <tr><th>PI name</th><th>Number of lab hazards</th></tr>
               <?php
               $pis_most_hazards = get_pi_most_lab_hazards($conn, $dept, 3);
               foreach ($pis_most_hazards as $row) {
                  echo '<tr>';
                  echo '<td><a href="/~fsdatabase/lab.php?pi_name=' . urlencode($row[0][0]) . '">' . $row[0][0] . '</a></td>';
                  echo '<td>' . $row[1] . '</td>';
                  echo '</tr>';
               }
               ?>
            </table>
         </div>
      </div>
      <div class="col-lg-4">
         <div id="delay-gauge"> <?php if ($days_to_mitigation == NULL) echo "No mitigation dates have been reported!"; ?> </div>
      </div>
   </div>
</div>	<!-- close container -->

<?php include("dial.php"); ?>

<script>
<?php
if ($days_to_mitigation != NULL) {
 echo 'var gauge = new Gauge("delay-gauge");';
 echo 'gauge.render(' . round($days_to_mitigation) . ');';
}
?>
</script>
