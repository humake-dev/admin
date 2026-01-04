function getList(current_page, jq) {
  if(!current_page)
    current_page = 0;

  var perPage =10;
  var pageID=current_page+1;

  $.getJSON('/message-prepares/select',{'format':'json','per_page':perPage,'page':pageID},function(data) {
      if(data.result=='success') {
        $("#message_prepare_list tbody").empty();
        $('#message_prepare_list_count').val(data.total);

        if(data.total) {
          $.each(data.list,function(index,value){
            $("#message_prepare_list tbody").append('<tr><td><input name="id" value="'+value.id+'" type="radio"></td><td class="title">'+value.title+'</td><td class="content" style="display:none">'+value.content+'</td><td class="text-right">'+value.created_at+'</td></tr>');
          });

          $('#message_prepare_list tbody td').click(mp_td_click);
          $('#message_prepare_list tbody tr td input').click(function(e) {
            e.stopPropagation();
          }).change(mp_input_change);
        } else {
          $("#message_prepare_list tbody").append('<tr><td colspan="4" style="text-align:center">해당 데이터가 없습니다.</td></tr>');
        }
        $(".sl_pagination").removeData("load").empty();
        initPagination(data.total,10,current_page);
      } else {
      }
  });
}

function mp_td_click() {
  var i_checkbox = $(this).parent().find('input:first').trigger('click');
}

function mp_input_change() {
  $(this).closest('tbody').find('tr').removeClass('table-primary');
  if ($(this).prop('checked')) {
    $(this).closest('tr').addClass('table-primary');
  }
}

$(function () {
  // 자바스크립트가 지원될때 Tr 커서 선택
  $("#message_prepare_list tbody tr").css('cursor','pointer');

  initPagination(Number($('#message_prepare_list_count').val()),10,0);

  $('#select_prepare_message').click(function (){
    var tr=$('#message_prepare_list tbody input:checked').closest('tr');
    var title=tr.find('td.title').text();
    var content=tr.find('td.content').text();

    $("#m_title").val(title);
    $("#m_content").val(content);

    $("#myModal").modal('hide');
  });

  $('#message_prepare_list tbody td').click(mp_td_click);
  $('#message_prepare_list tbody tr td input').click(function(e) {
    e.stopPropagation();
  }).change(mp_input_change);


});
