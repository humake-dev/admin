$(function () {


  $('input[name="type"]').change(function(){
    if($(this).val()=='month') {
      $('.choice_period').hide();
      $('.choice_month').show();
    } else {
      $('.choice_period').show();
      $('.choice_month').hide();
    }
  });





  // 등록구분
  if($("#donutchart4").length) {
    google.charts.load("current", {packages:["corechart",'bar']});
    google.charts.setOnLoadCallback(function() {
      var data = google.visualization.arrayToDataTable(course_data);
      var options = {
        pieHole: 0.4,
        chartArea:{left:90,top:0,width:'100%',height:'100%'},
        legend: {
          maxLines: 1,
          textStyle: {
            fontSize: 15
          }
        }
      };
      var chart = new google.visualization.PieChart(document.getElementById('donutchart4'));
      chart.draw(data, options);
    });
  }


// 등록비율
if($("#donutchart2").length) {
  google.charts.load("current", {packages:["corechart","bar"]});
  google.charts.setOnLoadCallback(function() {
  var data = google.visualization.arrayToDataTable(course_new_re_ratio);
  var options = {
    pieHole: 0.4,
    chartArea:{left:90,top:0,width:'100%',height:'100%'},
    colors:['#99E000','#FFBB00'],
    legend: {
      maxLines: 1,
      textStyle: {
        fontSize: 15
      }
    }
  };
  var chart = new google.visualization.PieChart(document.getElementById('donutchart2'));
  chart.draw(data, options);
  });
}


if($('#columnchart_values1').length) {
  google.charts.load("current", {packages:['corechart','bar']});
  google.charts.setOnLoadCallback(function() {
    var data = google.visualization.arrayToDataTable(course_new_re_data);

    var view = new google.visualization.DataView(data);
    view.setColumns([0, 1,
                     { calc: "stringify",
                       sourceColumn: 1,
                       type: "string",
                       role: "annotation" },
                       2,{ calc: "stringify",
                         sourceColumn: 2,
                         type: "string",
                         role: "annotation" }
                     ]);

    var options = {
      width: 400,
      height: 160,
      bar: {groupWidth: "50%"},
      legend: { position: "top" }

    };
    var chart = new google.visualization.ColumnChart(document.getElementById("columnchart_values1"));
    chart.draw(view, options);
});
}


if($('#columnchart_values').length) {
google.charts.load("current", {packages:['corechart','bar']});
google.charts.setOnLoadCallback(function() {
  var data = google.visualization.arrayToDataTable(sales_data);

  var view = new google.visualization.DataView(data);
  view.setColumns([0, 1,
                   { calc: "stringify",
                     sourceColumn: 1,
                     type: "string",
                     role: "annotation"}
                     //color: "#99E000"}
                   ,2
                   ]);

  var options = {
    width: 400,
    height: 160,
     //chartArea:{left:0,top:0,width:'70%',height:'90%'},
    bar: {groupWidth: "60%"},
    legend: {position: 'none'},
    //colors:['#99E000']
  };
  var chart = new google.visualization.ColumnChart(document.getElementById("columnchart_values"));
  chart.draw(view, options);
});
}

  // 결제형태
  if($("#donutchart3").length) {
    google.charts.load("current", {packages:["corechart",'bar']});
    google.charts.setOnLoadCallback(function() {
      var data = google.visualization.arrayToDataTable(payment_type_data);

      var view = new google.visualization.DataView(data);
      view.setColumns([0, 1,
                       { calc: "stringify",
                         sourceColumn: 1,
                         type: "string",
                         role: "annotation" },
                       2]);
  
      var options = {
        width: 400,
        height: 160,
        bar: {groupWidth: "50%"},
        legend: { position: "none" },
      };
      var chart = new google.visualization.ColumnChart(document.getElementById("donutchart3"));
      chart.draw(view, options);
    });
  }

// 월별입장회원
if($("#columnchart_values2").length) {
  google.charts.load("current", {packages:['corechart','bar']});
  google.charts.setOnLoadCallback(function() {
    var data = google.visualization.arrayToDataTable(entrance_month_data);

    var view = new google.visualization.DataView(data);
    view.setColumns([0, 1,
                     { calc: "stringify",
                       sourceColumn: 1,
                       type: "string",
                       role: "annotation" },
                     2]);

    var options = {
      width: 400,
      height: 160,
      bar: {groupWidth: "50%"},
      legend: { position: "none" },
    };
    var chart = new google.visualization.ColumnChart(document.getElementById("columnchart_values2"));
    chart.draw(view, options);
  });
}

  // 연령비율
  if($("#donutchart1").length) {
    google.charts.load("current", {packages:['corechart','bar']});
    google.charts.setOnLoadCallback(function() {
      var data = google.visualization.arrayToDataTable(age_data);

      var options = {
        pieHole: 0.4,
        chartArea:{left:90,top:0,width:'100%',height:'100%'},
        legend: {
          maxLines: 1,
          textStyle: {
            fontSize: 15
          }
        }
      };

      var chart = new google.visualization.PieChart(document.getElementById('donutchart1'));
      chart.draw(data, options);
    });
  }

  // 회원성비
  if($("#donutchart").length) {
    google.charts.load("current", {packages:["corechart",'bar']});
    google.charts.setOnLoadCallback(function() {
      var data = google.visualization.arrayToDataTable(gender_data);

      var options = {
        pieHole: 0.4,
        chartArea:{left:90,top:0,width:'100%',height:'100%'},
        legend: {
          maxLines: 1,
          textStyle: {
            fontSize: 15
          }
        }
      };

      var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
      chart.draw(data, options);
    });
  }

  // 요일별 입장회원
  if($("#columnchart_material").length) {
    google.charts.load("current", {packages:['corechart','bar']});
    google.charts.setOnLoadCallback(function() {
      var data = google.visualization.arrayToDataTable(entrance_week_data);

      var view = new google.visualization.DataView(data);
      view.setColumns([0, 1,
                           { calc: "stringify",
                             sourceColumn: 1,
                             type: "string",
                             role: "annotation" },
                           2]);
      var options = {
            width: 400,
            height: 160,
            bar: {groupWidth: "50%"},
            legend: { position: "none" },
          };
      var chart = new google.visualization.ColumnChart(document.getElementById("columnchart_material"));
      chart.draw(view, options);
    });
  }
});
