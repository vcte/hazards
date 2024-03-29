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
var svg = d3.select("#bar-chart"),
    margin = {top: 20, right: 20, bottom: 30, left: 40},
    width = +svg.attr("width") - margin.left - margin.right,
    height = +svg.attr("height") - margin.top - margin.bottom,
    g = svg.append("g").attr("transform", "translate(" + margin.left + "," + margin.top + ")");

   var divTooltip = d3.select("body").append("div").attr("class", "toolTip");

   var legend_size = 180;
   var x0 = d3.scaleBand()
     .rangeRound([0, width - legend_size])
     .paddingInner(0.1);

   var x1 = d3.scaleBand()
     .padding(0.05);

   var y = d3.scaleLinear()
     .rangeRound([height, 0]);
  
   data = [];

<?php
  // print out javascript declarations for issue data
  foreach ($issues_by_dept as $row) {
     $dept = $row[0];
     $issues = $row[1];
     if (count($issues) > 0) {
        $str = '{';
        $str .= '"Dept":' . '"' . $dept . '", ';
        foreach ($issues as $issue) {
           $str .= '"' . $issue[0] . '":' . $issue[1] . ', ';
        }
        $str .= '}';
        echo '  data.push(' . $str . ');';
        echo PHP_EOL;
     }
  }
  
  echo_keys_and_colors($conn);
?>

  x0.domain(data.map(function(d) { return d.Dept; }));
  x1.domain(keys).rangeRound([0, x0.bandwidth()]);
  y.domain([0, d3.max(data, function(d) { return d3.max(keys, function(key) { return d[key]; }); })]).nice();

  // draw bars
  g.append("g")
    .selectAll("g")
    .data(data)
    .enter().append("g")
      .attr("transform", function(d) { return "translate(" + x0(d.Dept) + ",0)"; })
    .selectAll("rect")
    .data(function(d) { dkeys = Object.keys(d).filter(function(k) { return keys.indexOf(k) != -1; }).sort(function(a, b) { return d[b] - d[a]; });  
                        return dkeys.map(function(key) { return {dept:d.Dept, dkeys:dkeys, key:key, value:d[key]}; }); })
    .enter().append("rect")
      .attr("x", function(d) { return x1.domain(d.dkeys)(d.key); })
      .attr("y", function(d) { return y(d.value); })
      .attr("width", x1.bandwidth())
      .attr("height", function(d) { return height - y(d.value); })
      .attr("fill", function(d) { return z(d.key); })
      
    // display tooltip whenever user hovers over bar
    .on("mousemove", function(d){
      divTooltip.style("left", d3.event.pageX+10+"px");
      divTooltip.style("top", d3.event.pageY-25+"px");
      divTooltip.style("display", "inline-block");
      var x = d3.event.pageX, y = d3.event.pageY;
      divTooltip.html((d.dept) + "<br>" + (d.key) + "<br>" + (d.value));
    })
    .on("mouseout", function(d){
      divTooltip.style("display", "none");
    })
    
    // open new window for zoomed-in view of hazard data whenever user clicks on bar
    .on("click", function(d) {
      <?php
      $url = '"/~fsdatabase/' . $type_url_param . '?observ_code=" + decodeURI(d.key.replace(/ /g, "+")) + "&department=" + (d.dept) + "' . $date_url_params . '"';
      echo 'window.open(' . $url . ', "_blank");';
      ?>
    });

  /*g.append("text")
      .attr("x", (width / 2))             
      .attr("y", 0 - (margin.top / 2))
      .attr("text-anchor", "middle")
      .style("font-weight", "bold")
      .text("Top 3 issues for each department");*/

  // draw x axis
  g.append("g")
      .attr("class", "axis")
      .attr("transform", "translate(0," + height + ")")
      .call(d3.axisBottom(x0))
    .append("text")
      .attr("x", (width - legend_size) / 2)
      .attr("dy", 30)
      .attr("fill", "#000")
      .attr("font-weight", "bold")
      .text("Department");

  // draw y axis
  g.append("g")
      .attr("class", "axis")
      .call(d3.axisLeft(y).ticks(null, "s"))
    .append("text")
      .attr("x", 2)
      .attr("y", y(y.ticks().pop()) + 0.5)
      .attr("dy", "0.32em")
      .attr("fill", "#000")
      .attr("font-weight", "bold")
      .attr("text-anchor", "start")
      .text("Number of hazards");

  // draw legend on right, indicates color associated with each hazard
  var legend = g.append("g")
      .attr("font-family", "sans-serif")
      .attr("font-size", 10)
      .attr("text-anchor", "end")
    .selectAll("g")
    .data(keys)
    .enter().append("g")
      .attr("transform", function(d, i) { return "translate(0," + i * 20 + ")"; });

  legend.append("rect")
      .attr("x", width - 19)
      .attr("width", 19)
      .attr("height", 19)
      .attr("fill", z);

  legend.append("text")
      .attr("x", width - 24)
      .attr("y", 9.5)
      .attr("dy", "0.32em")
      .text(function(d) { return d; });

</script>
