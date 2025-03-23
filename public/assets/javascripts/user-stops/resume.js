$(function () {
  var stop_day=$("#count_stop_day").val();
  $('.datepicker').datepicker({language: "ko",todayHighlight: true, endDate:'-1d', startDate:'-'+stop_day+'d'});
});
