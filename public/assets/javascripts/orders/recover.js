$(function () {
  $('.date .input-group-text').click(function(){
    $(this).parent().find('input').trigger('focus');
  }).css('cursor','pointer');

    $('.enroll_datepicker').datepicker({language: "ko",todayHighlight: true,startDate: $("#e_end_start_date").val()});
});