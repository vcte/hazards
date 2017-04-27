<style>
.arc text {
  font: 12px sans-serif;
  text-anchor: middle;
}

.arc path {
  stroke: #fff;
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
data = [];

<?php
  // find total number of issues
  $total_issues = 0;
  foreach ($all_issues as $row) {
      $count = $row[1];
      $total_issues += $count;
  }
  
  // print out javascript declarations for issue data
  $total_top_issues = 0;
  foreach ($top_issues as $row) {
     $issue = $row[0];
     $count = $row[1];
     if ($count > 0) {
        $str = '{';
        $str .= '"Issue":' . '"' . $issue . '", ';
        $str .= '"Count":' . '"' . $count . '", ';
        $str .= '"Perc":'  . '"' . round(100 * $count / $total_issues, 1) . '%", ';
        $str .= '}';
        echo '  data.push(' . $str . ');';
        echo PHP_EOL;
        $total_top_issues += $count;
     }
  }
  
  $other_issues = ($total_issues - $total_top_issues);
  $other_perc = round(100 * $other_issues / $total_issues, 1);
  $other_str = '{"Issue":"Other", "Count":"' . $other_issues . '", "Perc":"' . $other_perc . '%"}';
  echo '  data.push(' . $other_str . ');' . PHP_EOL;
  
  echo_keys_and_colors($conn)[0];
?>

var color = d3.scaleOrdinal()
    .range(["#98abc5", "#8a89a6", "#7b6888"]);

var pie = d3.pie()
    .sort(null)
    .value(function(d) { return d.Count; });

var svg = d3.select("#pie-chart"),
    margin = {top: 10, right: 0, bottom: 40, left: 0},
    width = +svg.attr("width"),
    height = +svg.attr("height"),
    radius = Math.min(width - margin.left - margin.right, height - margin.top - margin.bottom) / 2;
var g = svg.append("g")
    .attr("transform", "translate(" + width * 3 / 8 + "," + height / 2 + ")");
    
var arc = d3.arc()
    .outerRadius(radius - 10)
    .innerRadius(0);

var labelArc = d3.arc()
    .outerRadius(radius + 15)
    .innerRadius(radius + 15);

  var g = g.selectAll(".arc")
      .data(pie(data))
    .enter().append("g")
      .attr("class", "arc");

  g.append("path")
      .attr("d", arc)
      .style("fill", function(d) { return z(d.data.Issue); })
      
      // display tooltip whenever user hovers over pie chart
      .on("mousemove", function(d){
          divTooltip.style("left", d3.event.pageX+10+"px");
          divTooltip.style("top", d3.event.pageY-25+"px");
          divTooltip.style("display", "inline-block");
          var x = d3.event.pageX, y = d3.event.pageY;
          divTooltip.html((d.data.Issue) + "<br>" + (d.data.Count) + " (" + (d.data.Perc) + ")");
      })
      .on("mouseout", function(d){
          divTooltip.style("display", "none");
      })
      
      // open new window for zoomed-in view of hazard data whenever user clicks on bar
      .on("click", function(d) {
          if (d.data.Issue != "Other") {
             <?php
             $url = '"/~fsdatabase/' . $type_url_param . '?observ_code=" + decodeURI(d.data.Issue.replace(/ /g, "+")) + "' . $date_url_params . '"';
             echo 'window.open(' . $url . ', "_blank");';
             ?>
          }
      });

  g.append("text")
      .attr("transform", function(d) { return "translate(" + labelArc.centroid(d) + ")"; })
      .attr("dy", ".35em")
      .text(function(d) { return d.data.Perc; });
      
  // draw legend on right, indicates color associated with each hazard
  var legend = svg.append("g")
      .attr("font-family", "sans-serif")
      .attr("font-size", 10)
      .attr("text-anchor", "end")
    .selectAll("g")
    .data(pie(data))
    .enter().append("g")
      .attr("transform", function(d, i) { return "translate(0," + (100 + i * 20) + ")"; });

  legend.append("rect")
      .attr("x", width - 19)
      .attr("width", 19)
      .attr("height", 19)
      .attr("fill", function(d) { return z(d.data.Issue); });

  legend.append("text")
      .attr("x", width - 24)
      .attr("y", 9.5)
      .attr("dy", "0.32em")
      .text(function(d) { return d.data.Issue; });
</script>
