$(function () {
    // 자바스크립트가 지원될때 Tr 커서 선택
    $("#user_list tbody tr").css('cursor','pointer');
  
    // 리더기 읽을때 제출되는것 막음
    $("#user_form").submit(function(event){
      if ($("#u_card_no").is(':focus')) {
          return false;
      }
    });
  
    $("#user_list tbody td").click(function(){
      location.href=$(this).parent().find('a').attr('href');
    });
});