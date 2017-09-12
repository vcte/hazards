<!-- grouped bar chart that displays top hazards for each department -->

<style>
.axis .domain {
  display: none;
}
.toolTip {
    font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
    position: absolute;
    display: none;
    width: auto;
    height: auto;
    background: none repeat scroll 0 0 white;
    border: 0 none;
    border-radius: 8px 8px 8px 8px;
    box-shadow: -3px 3px 15px #888888;
    color: black;
    font: 12px sans-serif;
    padding: 5px;
    text-align: center;
}
</style>

<script>
var svg = d3.select("#horiz-chart"),
    margin = {top: 20, right: 20, bottom: 30, left: 50},
    width = +svg.attr("width") - margin.left - margin.right,
    height = +svg.attr("height") - margin.top - margin.bottom,
    g = svg.append("g").attr("transform", "translate(" + margin.left + "," + margin.top + ")");

   var horizDivTooltip = d3.select("body").append("div").attr("class", "toolTip");

   var x = d3.scaleLinear()
     .rangeRound([0, width]);
   var y0 = d3.scaleBand()
     .rangeRound([0, height])
     .paddingInner(0.1);
     
   var y1 = d3.scaleBand()
     .padding(0.05);

   data = [];

<?php
  // print out javascript declarations for issue data
  $max_num_pi_per_dept = 0;
  foreach ($dept_and_pis_with_most_hazards as $dept_row) {
     foreach ($dept_row as $i => $row) {
         $dept = $row[0];
         $pi_name = $row[1];
         $n_hazards = $row[2];
         if ($n_hazards > 0) {
            $max_num_pi_per_dept = max($max_num_pi_per_dept, $i);
            $str = '{';
            $str .= '"Dept":"' . $dept . '", ';
            $str .= '"PI":"' . $pi_name . '", ';
            $str .= '"Hazards":"' . $n_hazards . '", ';
            $str .= '"Index":"' . $i . '", ';
            $str .= '}';
            echo '  data.push(' . $str . ');';
            echo PHP_EOL;
         }
     }
  }
  
?>

  x.domain([0, d3.max(data, function(d) { return +d.Hazards; }) * 5/4]).nice();
  y0.domain(data.map(function(d) { return d.Dept; }));
  y1.domain([<?php for ($i = 0; $i <= $max_num_pi_per_dept; $i++) echo $i . ', '; ?>]).rangeRound([0, y0.bandwidth()]);
  
  var z = d3.scaleLinear().domain([0, height])
      .range([d3.rgb("#72b7c4"), d3.rgb('#00008a')])
      .interpolate(d3.interpolateHcl);

  // draw bars
  var g2 = g.append("g")
    .selectAll("g")
    .data(data)
    .enter().append("g")
      .attr("transform", function(d) { return "translate(0, " + y0(d.Dept) + ")"; });
  g2.append("rect")
      .attr("x", 0)
      .attr("y", function(d) { return y1(d.Index); })
      .attr("width", function(d) { return x(+d.Hazards); })
      .attr("height", y1.bandwidth())
      .attr("fill", function(d) { return z(y0(d.Dept) + y1(d.Index)); })
      
    // display tooltip whenever user hovers over bar
    .on("mousemove", function(d){
      horizDivTooltip.style("left", d3.event.pageX+10+"px");
      horizDivTooltip.style("top", d3.event.pageY-25+"px");
      horizDivTooltip.style("display", "inline-block");
      var x = d3.event.pageX, y = d3.event.pageY;
      horizDivTooltip.html((d.Dept) + "<br>" + (d.PI) + "<br>" + (d.Hazards));
    })
    .on("mouseout", function(d){
      horizDivTooltip.style("display", "none");
    })
    
    // open new window for zoomed-in view of hazard data whenever user clicks on bar
    .on("click", function(d) {
      <?php
      $url = '"/~fsdatabase/' . $type_url_param . '?pi_name=" + decodeURI(d.PI.replace(/ /g, "+")) + "&department=" + (d.Dept) + "' . $date_url_params . '"';
      echo 'window.open(' . $url . ', "_blank");';
      ?>
    });
    
  g2.append("text")
    .attr("font-family", "sans-serif")
    .attr("font-size", 14)
    .attr("x", function(d) { return x(+d.Hazards) + 10; })
    .attr("y", function(d) { return y1(d.Index) + 12; })
    .text(function(d) { return d.PI.split(" ")[0]; });
    
  g2.append("text")
    .attr("font-family", "sans-serif")
    .attr("font-size", 14)
    .attr("x", function(d) { return x(+d.Hazards) + 10; })
    .attr("y", function(d) { return y1(d.Index) + 26; })
    .text(function(d) { return d.PI.split(" ").slice(1).join(" "); });
    
// TODO: label for pi names, fix space at end of pi name

  // draw x axis
  g.append("g")
      .attr("class", "axis")
      .attr("transform", "translate(0," + height + ")")
      .call(d3.axisBottom(x).ticks(null, "s"))
    .append("text")
      .attr("x", width / 2)
      .attr("dy", 30)
      .attr("fill", "#000")
      .attr("font-weight", "bold")
      .text("Number of hazards");

  // draw y axis
  g.append("g")
      .attr("class", "axis")
      .call(d3.axisLeft(y0).ticks(null, "s"))
    .append("text")
      .attr("x", -36)
      .attr("y", y0(data[0].Dept) - 12)
      .attr("dy", "0.32em")
      .attr("fill", "#000")
      .attr("font-weight", "bold")
      .attr("text-anchor", "start")
      .text("Department");

</script>
