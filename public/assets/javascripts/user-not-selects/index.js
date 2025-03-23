var multi=true;

function getQueryStringObject(querystring) {
  var a = querystring.split('&');
  if (a == "") return {};
  var b = {};
  for (var i = 0; i < a.length; ++i) {
      var p = a[i].split('=', 2);
      if (p.length == 1)
          b[p[0]] = "";
      else
          b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
  }
  return b;
}

function getList(current_page, jq) {
  if(!current_page)
    current_page = 0;

  var perPage =10;
  var pageID=current_page+1;

  var searchType=null;
  var searchField=null;
  var searchWord=null;

  if($.trim($("#s_search_word").val())!='') {
    searchType='field';
    searchField=$("#s_search_field").val();
    searchWord=$.trim($("#s_search_word").val());
  }

  var s_param={'format':'json','search_type':searchType,'search_field':searchField,'search_word':searchWord,'per_page':perPage,'page':pageID}


  if($("#send_message_type").length) {
    s_param.message_type=$("#send_message_type").val();
  }

  if($('#send_message_type').val()=='push') {
    s_param.push=true;
  }

  if($("#search_params").length) {
    var qq='?'+$("#search_params").val();
  } else {
    var qq='';
  }

  $.getJSON('/user-not-selects'+qq,s_param,function(data) {
      if(data.result=='success') {
        $("#user_select_list tbody").empty();
        $('#user_select_list_count,#list_count').val(data.total);
        $('.summary .mark').text(data.total+'명');

        if(data.total) {
          $.each(data.list,function(index,value){

              if(value.birthday) {
                var birthday=value.birthday;
              } else {
                var birthday='입력안됨';
              }

              var input='<td><input name="id[]" value="'+value.id+'" type="checkbox"></td>';

            if(value.phone) {
              var phone=add_hyphen(value.phone)
            } else {
              var phone='입력안됨';
            }

            $("#user_select_list tbody").append('<tr>'+input+'<td class="name">'+value.name+'</td><td>'+value.card_no+'</td><td>'+birthday+'</td><td>'+display_gender(value['gender'])+'</td><td class="phone">'+phone+'</td></tr>');
          });

          $('#user_select_list tbody td').click(m_td_click);
          $('#user_select_list tbody tr td input').click(function(e) {
            e.stopPropagation();
          }).change(m_input_change);

          check_checked('user_select_list',true);
        } else {
          $("#user_select_list tbody").append('<tr><td colspan="4" style="text-align:center">해당 데이터가 없습니다.</td></tr>');
        }

        if($("#user_select_list tbody input:checkbox:not(:checked)").length) {
          $('#user_select_check_all').prop('checked',false);
        } else {
          $('#user_select_check_all').prop('checked',true);
        }        
        $(".sl_pagination").removeData("load").empty();
        initPagination(data.total,10,current_page);
      } else {
      }
  });
}

function m_td_click() {
  $(this).parent().find('input:first').trigger('click');
}

function m_input_change() {
  if(multi) {
    if ($(this).prop('checked')) {
      $(this).closest('tr').addClass('table-primary');
    } else {
      $(this).closest('tr').removeClass('table-primary');
    }

    sync_selct_user($(this).val());

    var tbody = $(this).closest('tbody');
    if (tbody.find('input:checkbox').length == tbody.find('input:checked').length) {
      $("#user_select_check_all").prop('checked', true);
    } else {
      $("#user_select_check_all").prop('checked', false);
    }
  } else {
    $(this).closest('tbody').find('tr').removeClass('table-primary');

    if ($(this).prop('checked')) {
      $(this).closest('tr').addClass('table-primary');
    }

  }
}

function sync_selct_user(user_id) {
  var i_input=$('#user_select_list input[value="'+user_id+'"]');
  var tr=$('#user_select_list input[value="'+user_id+'"]').parent().parent();

  if($('.not_users_input input[value="'+user_id+'"]').length) {
    $('.not_users_input input[value="'+user_id+'"]').parent().remove();
    i_input.prop('checked',false);
    tr.removeClass('table-primary');
    return false;
  }

  var name=tr.find('td.name').text();

  var span=$('<span class="select_user text-success">'+name+'<input type="hidden" name="not_user[]" value="'+user_id+'" /> <span class="text-danger">X<span></span>');
  span.find('.text-danger').click(deleteSelectedUser);
  $('.not_users_input').append(span);
}

$(function () {
  // 자바스크립트가 지원될때 Tr 커서 선택
  $("#user_select_list tbody tr").css('cursor','pointer');

  initPagination(Number($('#user_select_list_count').val()),10,0);

  check_checked('user_select_list',true);

  $("#user_select_check_all").click(function(){
    var tbody=$(this).closest('table').find('tbody');

    if($(this).is(':checked')) {
      tbody.find('input').prop('checked',true).change();
    } else {
      tbody.find('input').prop('checked',false).change();
    }
  });

  if($("#user_select_list tbody input:checkbox:not(:checked)").length) {
    $('#user_select_check_all').prop('checked',false);
  } else {
    $('#user_select_check_all').prop('checked',true);
  }  

  $('#user_select_search').submit(function(){
    getList();
    return false;
  });

  $('.card-header').click(function(){
    if($(this).find('.buttons').length) {
      if($(this).find('.buttons i').text()=='keyboard_arrow_down') {
        $(this).find('.buttons i').text('keyboard_arrow_up');
        $(this).closest('.card').find('.card-body').slideDown();
      } else {
        $(this).find('.buttons i').text('keyboard_arrow_down');
        $(this).closest('.card').find('.card-body').slideUp();
      }
    }
  });

  $('#user_select_list tbody td').click(m_td_click);
  $('#user_select_list tbody tr td input').click(function(e) {
    e.stopPropagation();
  }).change(m_input_change);

  $('#select').click(function (){
    var user_id=$('#user_select_list tbody input:checked').val();
    var tr=$('#user_select_list tbody input:checked').closest('tr');
    var phone=tr.find('td.phone').text();
    var name=tr.find('td.name').text();

    $("#c_user_id").val(user_id);
    $("#c_phone").val($.trim(phone));
    $("#c_name").val($.trim(name));

    $("#myModal").modal('hide');
  });
});
