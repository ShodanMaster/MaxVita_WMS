$(function() {
  'use strict';

 

  // Apex Donut chart start
  var options = {
    chart: {
      height: 300,
      type: "donut"
    },
    stroke: {
      colors: ['rgba(0,0,0,0)']
    },
    colors: [ "#7ee5e5","#4d8af0","#fbbc06","#f77eb9"],
    legend: {
      position: 'top',
      horizontalAlign: 'center'
    },
    dataLabels: {
      enabled: false,
      formatter: function (val) {
        return val
      }
    },
    series: [28, 40, 32, 10],
	labels: ["0-2 Days", "3-5 Days", "6-30 Days", " Above 30 Days"]
  };
  
  var chart = new ApexCharts(document.querySelector("#apexDonut"), options);
  
  chart.render();
  // Apex Donut chart start
  
 
  
  

  

});