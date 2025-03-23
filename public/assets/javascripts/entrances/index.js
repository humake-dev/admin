$(function () {
  // 자바스크립트가 지원될때 Tr 커서 선택
  if($("#user_list tr a").length) {
    $("#user_list tr").css('cursor','pointer');
  }

  $("#user_list td").click(function(){
    var tr=$(this).parent();
    if(tr.find('a').length) {
      location.href=tr.find('a').attr('href');
    }
  });


  $("#enroll_list tr").css('cursor','pointer');
  $("#enroll_list td").click(function(){
    var tr=$(this).parent();
    $("#enroll_list tbody tr").removeClass('table-primary');
    tr.addClass('table-primary');
    $("#enroll_content p:first span").text(tr.find('td:first').text()).effect('highlight');
    $("#enroll_content p:eq(1) span").text(tr.find('td:eq(1)').text()).effect('highlight');
    $("#enroll_content p:eq(2) span").text(tr.find('td:eq(2)').text()).effect('highlight');
    $("#enroll_content p:eq(3) span").text(tr.find('td:eq(3)').text()).effect('highlight');
    $("#enroll_content p:eq(4) span").text(tr.find('td:eq(4)').text()).effect('highlight');
    $("#enroll_content p:eq(5) span").text(tr.find('td:eq(5)').text()).effect('highlight');
    $("#enroll_content p:eq(6) span").text(tr.find('td:eq(6)').text()).effect('highlight');
    $("#enroll_content p:eq(7) span").text(tr.find('td:eq(7)').text()).effect('highlight');
  });

  $('#entrances aside input.datepicker').change(function () {
    location.href='entrances?date='+$(this).val();
  });
});
