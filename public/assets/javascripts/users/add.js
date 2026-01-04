$(function () {
    // 리더기 읽을때 제출되는것 막음
    $("#user_form").submit(function(event){
      if ($("#u_card_no").is(':focus')) {
          return false;
      }
    });
  
    $('.birthday-datepicker').datepicker({
        startView: 'years',
        defaultViewDate : {'year':1990},
        startDate:'-100y',
        endDate:'-0y',
        maxViewMode : 'decades',
        format: 'yyyy-mm-dd',
        autoclose :true,
        language : 'ko'
    });
});