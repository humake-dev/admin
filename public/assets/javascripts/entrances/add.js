$(function () {
  $('#user_attendance_form .datepicker').datepicker({language: "ko",todayHighlight: true, maxViewMode : 'decades', startDate:'-100y', endDate:'+0d',
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

  $('.input-group .input-group-addon').click(function(){
    $(this).parent().find('input').trigger('focus');
  });  
});
