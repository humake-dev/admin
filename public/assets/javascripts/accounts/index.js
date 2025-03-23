$(function () {
  // 자바스크립트가 지원될때 Tr 커서 선택
  $("#employee_list tr").css('cursor','pointer');

  $("#employee_list td").click(function(){
    location.href=$(this).parent().find('a').attr('href');
  });

  $(".search_user_form select").change(function(){
      $(".search_user_form").submit();
  });
});
