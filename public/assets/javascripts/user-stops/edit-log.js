$(function () {
  $('.datepicker').datepicker({language: "ko",todayHighlight: true, maxViewMode : 'decades', startDate:'-100y', endDate:'+100y',
  beforeShowDay: function(date){
      if (typeof active_dates === 'undefined') {
        return true;
      }

      var formattedDate = convertDate(date);
      if ($.inArray(formattedDate, active_dates) != -1){
        return {
           classes: 'active-check'
        };
      }
   return;
}
});

  var stop_day=$("#u_stop_start_date").val();
  $("#u_stop_end_date").datepicker({language: "ko",todayHighlight: true, startDate:$("#u_stop_start_date").val()});

  if($("#u_stop_end_date_not_set").is(":checked")) {
    $("#u_stop_start_date").datepicker({language: "ko",todayHighlight: true});
  } else {
    $("#u_stop_start_date").datepicker({language: "ko",todayHighlight: true, endDate:$("#u_stop_end_date").val()});
  }
  
  $('#u_stop_start_date').change(function() {
    if($("#u_stop_end_date").val()=='') {
      if($("#u_stop_end_date_not_set").is(":checked")) {
        return false;
      }
    }

    var startDate = moment($('#u_stop_start_date').val(), "YYYY-MM-DD");
    var endDate = moment($('#u_stop_end_date').val(), "YYYY-MM-DD");

    var diff_day=endDate.diff(startDate, 'days');
    change_date(diff_day+1);
  });

  $('#u_stop_end_date').change(function() {
    if($("#u_stop_end_date").val()=='') {
      if($("#u_stop_end_date_not_set").is(":checked")) {
        return false;
      }
    } else {
      $("#u_stop_end_date_not_set").prop("checked",false);
    }

    var startDate = moment($('#u_stop_start_date').val(), "YYYY-MM-DD");
    var endDate = moment($('#u_stop_end_date').val(), "YYYY-MM-DD");

    var diff_day=endDate.diff(startDate, 'days');    

    change_date(diff_day+1);
    
    $("#u_stop_end_date_not_set").prop('checked',false);

    if($("#u_stop_end_date_not_set").is(":checked")) {
      $("#u_stop_start_date").datepicker({language: "ko",todayHighlight: true});
    } else {
      $("#u_stop_start_date").datepicker({language: "ko",todayHighlight: true, endDate:$("#u_stop_end_date").val()});
    }
  });

  function change_date(day) {
    $("#o_stop_day,.o_stop_day").text(day).effect('highlight');

    $('#order_stop_table tbody tr').each(function(){
      var origin_start_date=$(this).find('.origin_start_date').val();
      var startDate = moment(origin_start_date, "YYYY-MM-DD");
      var change_end_date = startDate.add(day,'days');
  
      $(this).find('.change_end_date').text(change_end_date.format('YYYY년 M월 D일')).effect('highlight');
    });
  }
});
