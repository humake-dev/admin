$(function () {
  $('#facility_manager_buttons a.disabled').click(function(event){
    event.preventDefault();
    alert('먼저 이 메뉴가 가능한 락커를 선택해주세요');
  });
  
  $('#rents .list article').css('cursor','pointer');
  $('#rents .list article a').click(function() {
    $(this).closest('article').trigger('click');

    return false;
  });

  $('#rents .list article').click(function(){
    location.href=$(this).find('.card-header a').attr('href');
  });

  window.scrollTo(0,0);

  $("#go_top").click(function(){
    $("#rents .list").animate({scrollTop: 0}, 500, 'linear');
    window.scrollTo(0,0);
  });
});
