<style type="text/css">

    #dial .needle path {
      fill: beige;
    }
          
    circle.label {
      fill: white;
    }       
    
    line.label {
      stroke: white; 
      stroke-width: 1px;
    }
    
    text.label {
      font-family: Arial;
      font-size: 12px;
      fill: white; 
    }

</style>

<script>

      var w = 960,
          h = 500;

      // NOTE: changing x distorts the position of the numbers on perimeter of dial, so change dx to shift the dial
      var dx = 50;
      var dy = 0;
      var r = <?php echo isset($dial_radius) ? $dial_radius : 160 ?>,
      x = r,
	  y = r + 40,
	  m = 100,
	  ticks = 0,
	  mark = 'line';
      var layout = [ 
        { x: x, y: y, r: r, m: m, ticks: ticks, mark: mark },  
      ];
      var charts = [NBXDialChart()
          .width(r * 2)
          .height(r * 2)
          .domain([0, m])
          .range([-x, x])
          .minorTicks(ticks)
          .minorMark(mark)
      ];      
      
      var svg = d3.select('#dial')
        .append('svg:svg')
          .attr('width', w) 
          .attr('height', h);
      
      var dials = svg.selectAll('g.dial')
          .data(layout)
        .enter().append('svg:g')
          .attr('class', 'dial')
          .attr('id', 'dial-0')
          .attr('transform', 'translate(' + (x - r + dx) + ',' + (y - r + dy) + ')');

      dials.each(function(d, i) { d3.select(this).data([<?php echo $dial_perc; ?>]).call(charts[i]); });

</script>

