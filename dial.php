<style type="text/css">

    #dial-0 .needle path {
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

    #dial-1 text.label {
      font-size: 16px;      
    } 

    #dial-2 text.label {
      font-size: 14px;      
    } 

</style>

<script>

      var w = 960,
          h = 500;

      // NOTE: changing x distorts the position of the numbers on perimeter of dial, so change dx to shift the dial
      var dx = 50;
      var dy = 0;
      var x = 150,
	  y = 200,
	  r = 160,
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

      dials.each(function(d, i) { d3.select(this).data([<?php echo $perc_hazards_mitigated; ?>]).call(charts[i]); });

</script>

