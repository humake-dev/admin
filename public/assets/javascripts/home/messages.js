function getList(current_page, jq) {
  if(!current_page)
    current_page = 0;

  var perPage =10;
  var pageID=current_page+1;
  var user_id=$('#home_user_id').val();

  $.getJSON('/home/messages/'+user_id,{'format':'json','per_page':perPage,'page':pageID},function(data) {
      if(data.result=='success') {
        $("#user_message_list tbody").empty();
        $('#list_count').val(data.total);

        if(data.total) {
          $.each(data.list,function(index,value){
            var ta=value.created_at.split(' ');
            var i_date=ta[0].split('-');
            var created_at=Number(i_date[0])+'년 '+Number(i_date[1])+'월 '+Number(i_date[2])+'일';

            if(value.type=='sms') {
              var m_type='SMS';
            } else {
              var m_type='스마트폰 푸시';
            }

            var append_str='<tr><td>'+m_type+'</td><td>'+value.title+'</td><td class="text-right"><a href="/message-contents/view/'+value.id+'" class="btn btn-secondary btn-modal">내용보기</a></td><td class="text-right">'+created_at+'</td>';

            if($('#user-message-manage').length) {
              append_str+='<td><a href="/message-users/delete-confirm/'+value.mu_id+'" class="btn btn-danger btn-delete-confirm">삭제</a></td>';
            }
            
            append_str+='</tr>';

            $("#user_message_list tbody").append(append_str);


          });
          $("#user_message_list tr").css('cursor','pointer');
          $("#user_message_list tbody .btn-modal").click(btn_modal_click);
          $("#user_message_list tbody .btn-delete-confirm").click(btn_delete_confirm_click);

        } else {
          $("#user_message_list tbody").append('<tr><td colspan="2" style="text-align:center">해당 데이터가 없습니다.</td></tr>');
        }
        $(".sl_pagination").removeData("load").empty();
        initPagination(data.total,10,current_page);
      } else {
      }
  });
}

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

  $('#show_app_id').click(function () {
		alert("앱 아이디:"+$(this).parent().find('input').val());
	});

    initPagination(Number($('#list_count').text()),10);
});
