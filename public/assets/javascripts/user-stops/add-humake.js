$(function () {
    $('#o_stop_start_date').datepicker({language: "ko",todayHighlight: true});

    //$("#o_stop_end_date").datepicker({language: "ko",todayHighlight: false, startDate:add_day($("#o_stop_start_date").val(),7), endDate:add_day($("#o_stop_start_date").val(),Number($("#max_period_day").val())+1)});       
    $("#o_stop_end_date").datepicker({language: "ko",todayHighlight: false, startDate:add_day($("#o_stop_start_date").val(),7)});   

    $('#o_stop_start_date').change(function(){
      $("#o_stop_end_date").datepicker('setStartDate', add_day($('#o_stop_start_date').val(),7));
    //  $("#o_stop_end_date").datepicker('setEndDate', add_day($("#o_stop_start_date").val(),Number($("#max_period_day").val())+1));
    });
});