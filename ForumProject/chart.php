<!DOCTYPE HTML>
     <html>
     <head>  
     <script src="https://cdn.jsdelivr.net/npm/chart.js@4.2.1/dist/chart.umd.min.js "></script>
          <script>
     $(document).ready(function () {
            showGraph();
        });


        function showGraph()
        {
                $.ajax({
            type      : 'POST', //Method type
            url       : 'ajax/chartmaker.php', //Your form processing file URL
            data       : {check:0},
            dataType  : 'json',
                success: function (data)
                {
                    console.log(data[0]);
                     var name = [];
                    
                    var marks = [];
                   
                    for (var i in data) {
                        name.push(data[i].country);
                        marks.push(data[i].num);
                        
                    }
                    var chartdata = {
                        labels: name,
                        datasets: [
                            {
                                label: '# of users',
                                data: marks
                            }
                        ]
                    };
                    var graphTarget = $("#countryContainer");
                    var barGraph = new Chart(graphTarget, {
                        type: 'pie',
                        data: chartdata,
                        options: {
    responsive: true
  }
                    });
                }
            });
                    $.ajax({
            type      : 'POST', //Method type
            url       : 'ajax/chartmaker.php', //Your form processing file URL
            data       : {check:1},
            dataType  : 'json',
                success: function (data)
                {

                    var barColors = [
  "#BF1FA4",
  "#4951F2",
  "#04BF8A",
  "#F27C38",
  "rgba(0,0,255,0.2)",
];
                    var name2=[];
                    var marks2= [];
                    for (var i in data) {
                    name2.push(data[i].page_url);
                       
                        marks2.push(data[i].timeSec);}

                    var chartdata2 = {
                        labels: name2,
                        datasets: [
                            {
                                label: 'Average Time spent (HH:MM:SS)',
                                backgroundColor: barColors,
                                data: marks2,
                            }
                        ]
                    };

                    var graphTarget = $("#pageContainer");

                    function format(val){
            let minutes = Math.floor(val / 60);
            let hours = Math.floor(minutes/60);
            minutes = minutes %60;
                        let extraSeconds = val % 60;
                        minutes = minutes < 10 ? "0" + minutes : minutes;
                        extraSeconds = extraSeconds < 10 ? "0" + extraSeconds : extraSeconds;
                        hours = hours <10 ? "0" + hours : hours;
                        return hours+":"+minutes+":"+extraSeconds;}

                    var barGraph = new Chart(graphTarget, {
                        type: 'bar',
                        data: chartdata2,
                        options: {
                            response:true,
    scales: {
        y: {
            ticks: {
                callback: function(value, index, ticks) {
                    return format(value);
                },
            }
        }
    },plugins:
        {tooltip: {
            callbacks:{
                label: function(context){
                return format(context.dataset.data[context.dataIndex]);
            }
            }
            }
        }
                        }                
    });
}
                
                });
                $.ajax({
            type      : 'POST', //Method type
            url       : 'ajax/chartmaker.php', //Your form processing file URL
            data       : {check:2},
            dataType  : 'json',
                success: function (data)
                {
                    var name3=[];
                    var marks3= [];
                    var barColors = [
  "#BF1FA4",
  "#4951F2",
  "#04BF8A",
  "#F27C38",
];
                    for (var i in data) {
                    name3.push(data[i].topic_name); 
                    marks3.push(data[i].numPosts);}
                    var chartdata3 = {
                        labels: name3,
                        datasets: [
                            {
                                label: 'Total Posts',
                                data: marks3,
                                backgroundColor: barColors
                            }
                        ]
                    };
                    var graphTarget3 = $("#boardContainer");
                    var barGraph3 = new Chart(graphTarget3, {
                        type: 'bar',
                        data: chartdata3,
                        options: {
    responsive: true
  }
                    });
                }
            });
            }
            
           
     </script>
     </head>
     <body>

     </body>
     </html>                              