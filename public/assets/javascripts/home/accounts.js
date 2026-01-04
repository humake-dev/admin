function getList(current_page, jq) {
  if(!current_page)
    current_page = 0;

  var perPage =10;
  var pageID=current_page+1;
  var user_id=$('#home_user_id').val();
  var currency=$('#home_currency').val();

  $.getJSON('/accounts',{'user_id':user_id,'format':'json','no_commission':true,'no_branch_transfer':true,'no_period_search':true,'per_page':perPage,'page':pageID},function(data) {
      if(data.result=='success') {
        $("#user_account_list tbody").empty();
        $('#list_count').val(data.total);

        if(data.total) {
          $.each(data.list,function(index,value){
            if(value['type']=='I') {
               var type='<span class="text-success">수입</span>';
            } else {
              var type='<span class="text-danger">지출</span>';
            }

            var ta=value['transaction_date'].split('-');
            var transaction_date=Number(ta[0])+'년 '+Number(ta[1])+'월 '+Number(ta[2])+'일';
            var category_name='';

            if (value.account_category_id == ADD_OTHER) {
              category_name=value.other_title;
            } else {
                if (value.account_category_id == ADD_ORDER) {
                  category_name=value.product_title;
                } else{
                  category_name=value.category_name;
                }
              }

            var product_name='';

            if(value.product_name!==null) {
              product_name=value.product_name;
            }

            var append='<tr><td>'+type+'</td><td>'+transaction_date+'</td><td>'+category_name+'</td><td>'+product_name+'</td><td class="text-right">'+Number(Number(value['cash'])+Number(value['credit'])).toLocaleString()+currency+'</td>';
            append+='<td class="text-right">'+Number(value['cash']).toLocaleString()+currency+'</td><td class="text-right">'+Number(value['credit']).toLocaleString()+currency+'</td>';

            var tth=$('table th.manage');
            if(tth.length) {
              append+='<td class="text-center">';

              if(tth.hasClass('a-write')) {
                append+='<a href="/accounts/edit/'+value['id']+'" class="btn btn-default">수정</a>';
              }

              if(tth.hasClass('a-delete')) {
                append+='<a href="/accounts/delete-confirm/'+value['id']+'" class="btn btn-danger btn-delete-confirm">삭제</a>';
              }
              append+='</td>';
            }

            append+='</tr>';
            $("#user_account_list tbody").append(append);
          });
          $("#user_account_list tr").css('cursor','pointer');
          $('#user_account_list tbody td').click(function(e){
            var i_checkbox=$(this).parent().find('input:first');
            var user_idx = i_checkbox.val();
          });
        } else {
          $("#user_account_list tbody").append('<tr><td colspan="2" style="text-align:center">해당 데이터가 없습니다.</td></tr>');
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
