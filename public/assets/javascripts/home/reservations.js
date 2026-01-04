function getList(current_page, jq) {
  if(!current_page)
    current_page = 0;

  var perPage =10;
  var pageID=current_page+1;
  var user_id=$('#home_user_id').val();
  var currency=$('#home_currency').val();
  var minute=$('#home_minute').val();

  var change_status=false;

  if($('input[name="change-status"]').val()==1) {
    change_status=true;
  }

  var return_url=$('#return-url').val();

  $.getJSON('/home/reservations/'+user_id,{'format':'json','per_page':perPage,'page':pageID},function(data) {
      if(data.result=='success') {
        $("#user_reservation_list tbody").empty();
        $('#list_count').val(data.total);

        if(data.total) {
          $.each(data.list,function(index,value){
            if(value.commission) {
              var commission=Number(value.commission).toLocaleString()+currency;
            } else {
              var commission='-';
            }

            if(value.course_name) {
              var course_name=value.course_name;
            } else {
              var course_name='-';
            }
            
            if(value.type=='PT') {              
              switch(value.complete) {
                case  3 :
                    var status='<span class="text-success">완료</span>';
                  break;
                case  4 :
                  var status='<span class="text-success">완료</span>';
                  break;
                  default :
                    if(change_status) {
                      var status='<a href="/reservations/complete/'+value['reservation_id']+'?return_url='+return_url+'">예약중</a>';
                    } else {
                      var status='예약중';
                    }
                    break;
              }
            } else {
              var status='-';
            }

            var start_time=value.start_time;
            var progress_time=value.progress_time+minute;
            

            if(value.manager_enable==1) {
              var manager_name=value.manager_name;
            } else {
              var manager_name='<span class="text-danger">(삭제된 직원)</span>';
            }

            var tr='<tr><td>'+value.type+'</td><td>'+course_name+'</td><td>'+manager_name+'</td><td>'+start_time+'</td><td>'+status+'</td><td>'+commission+'</td><td>'+progress_time+'</td>';

            if($("#change-status").val()) {
              if(value.delete_available) {
                tr+='<td><a href="/reservations/delete/'+value.reservation_id+'" class="btn btn-danger btn-delete-confirm">삭제</a></td>';
              } else {
                tr+='<td>-</td>';
              }
            }
            tr+='</tr>';
            $("#user_reservation_list tbody").append(tr);
          });
          $("#user_reservation_list tr").css('cursor','pointer');
          $('#user_reservation_list .btn-modal').click(btn_modal_click);
        } else {
          $("#user_reservation_list tbody").append('<tr><td colspan="2" style="text-align:center">해당 데이터가 없습니다.</td></tr>');
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
