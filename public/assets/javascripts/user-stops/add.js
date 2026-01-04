$(function () {
  $('.user_change_datepicker').datepicker({language: "ko",todayHighlight: true,
    startDate: available_stop_start_date,
    datesDisabled: disable_date
  });

  $("#us_stop_start_date,#us_stop_end_date").change(function(){
    if($("#us_stop_start_date").val()=='') {
      $("#stop_day_count_value").val('미정');
      return false;
    }

    if($("#us_stop_end_date").val()=='') {
      $("#stop_day_count_value").val('미정');
      return false;
    }

    var s_date=moment($("#us_stop_start_date").val());
    var e_date=moment($("#us_stop_end_date").val());

    $("#stop_day_count_value").val(moment.duration(e_date.diff(s_date)).asDays()+1+'일');
  });
  
  $("#is_today_onetime").change(function(){
    if($(this).prop('checked')) {
    } else {
      $("#us_request_date").show();      
      $("#today_display").hide();
    }
  });
});