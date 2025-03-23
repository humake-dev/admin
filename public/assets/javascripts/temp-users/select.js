var multi=true;
var new_submit=false;

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

  $.getJSON('/temp-users/select',{'format':'json','search_type':searchType,'search_field':searchField,'search_word':searchWord,'per_page':perPage,'page':pageID},function(data) {
      if(data.result=='success') {
        $("#user_select_list tbody").empty();
        $('#user_select_list_count').val(data.total);

        if(data.total) {
          $.each(data.list,function(index,value){
            if(multi) {
              var input='<td class="text-center"><input name="id[]" value="'+value.id+'" type="checkbox"></td>';
            } else {
              var input='<td class="text-center"><input name="id" value="'+value.id+'" type="radio"></td>';
            }

            $("#user_select_list tbody").append('<tr>'+input+'<td class="name">'+value.name+'</td><td class="phone">'+add_hyphen(value.phone)+'</td><td class="text-center manage"><a href="/temp-users/delete-confirm/'+value.id+'" class="btn btn-danger btn-delete-confirm">삭제</a></td></tr>');
          });

          $('#user_select_list tbody td').click(m_td_click);
          $('#user_select_list tbody tr td input').click(function(e) {
            e.stopPropagation();
          }).change(m_input_change);

          if(multi) {
            check_checked('user_select_list');
          }

          $("#temp_name").val('');
          $("#temp_phone").val('');

          $("#user_select_list .btn-delete-confirm").click(delete_temp_user);

          if(new_submit) {
            $("#user_select_list tbody tr:first td").effect("highlight", {}, 3500);
            new_submit=false;
          }
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

  if($('.users_input input[value="'+user_id+'"]').length) {
    $('.users_input input[value="'+user_id+'"]').parent().remove();
    i_input.prop('checked',false);
    tr.removeClass('table-primary');
    return false;
  }

  var name=tr.find('td.name').text();

  var span=$('<span class="select_user text-success">'+name+'<input type="hidden" name="temp_user[]" value="'+user_id+'" /> <span class="text-danger">X<span></span>');
  span.find('.text-danger').click(deleteSelectedUser);
  $('.users_input').append(span);
}

function delete_temp_user() {
  /* if(!confirm('정말로 삭제합니까?')) {
    return false;
  } */

  var url=$(this).attr('href').replace('/delete-confirm/','/delete/');
  $.post(url,{'format':'json'},function(data){
    if(data.result=='success') {
      alert('삭제되었습니다');
      getList();
    }
  },'json');

  return false;
}

$(function () {
  if(!$("#user_select_check_all").length) {
    multi=false;
  }

	$(".card .card-header .nav-item .nav-link").click(function(event){
		event.preventDefault();
		event.stopPropagation();

		var card=$(this).closest('.card');
		card.find(".card-header .nav-item .nav-link").removeClass('active');

		$(this).addClass('active');
		var index=card.find('.card-header .nav-item .nav-link').index($(this));
		card.find('.card-block').hide();
		card.find('.card-block:eq('+index+')').show();
		$(this).blur();

		var card_header=$(this).closest('.card-header');
		if(card_header.find('.buttons i').text()=='keyboard_arrow_down') {
			card_header.find('.buttons i').text('keyboard_arrow_up');
			card_header.closest('.card').find('.card-body:not(.no_common)').slideDown();
		}
  });

  $("#user_select_check_all").click(function(){
    var tbody=$(this).closest('table').find('tbody');

    if($(this).is(':checked')) {
      tbody.find('input').prop('checked',true).change();
    } else {
      tbody.find('input').prop('checked',false).change();
    }
  });

	$('.card-header .buttons').closest('.card').find('.card-header').css('cursor','pointer');
	$('.card-header').click(function(){
		if(!$(this).find('.buttons').length) {
			return true;
		}

		if($(this).find('.no_common').length) {
			return true;
		}

		if($(this).find('.buttons i').text()=='keyboard_arrow_down') {
			var index=0;

			$(this).find('.buttons i').text('keyboard_arrow_up');
			$(this).closest('.card').find('.card-body:not(.no_common)').slideDown();

			var card=$(this).closest('.card');
			if(card.find('.card-block:visible').length) {
				var v_card=card.find('.card-block:visible');
				index=card.find('.card-block').index(v_card);
			}
			card.find('.card-header .nav-item .nav-link:eq('+index+')').addClass('active');
		} else {
			$(this).find('.nav-item .nav-link').removeClass('active');

			$(this).find('.buttons i').text('keyboard_arrow_down');
			$(this).closest('.card').find('.card-body:not(.no_common)').slideUp();
		}
	});

  // 자바스크립트가 지원될때 Tr 커서 선택
  $("#user_select_list tbody tr").css('cursor','pointer');

  initPagination(Number($('#user_select_list_count').val()),10,0);
  check_checked('user_select_list');

  if($("#user_select_list tbody input:checkbox:not(:checked)").length) {
    $('#user_select_check_all').prop('checked',false);
  } else {
    $('#user_select_check_all').prop('checked',true);
  }
  
  $('#user_select_search').submit(function(){
    getList();
    return false;
  });

  $("#add_temp_user_form").submit(function(){
    $.post($(this).attr('action'),{'name':$(this).find('input[name="name"]').val(),'phone':$(this).find('input[name="phone"]').val(),'format':'json'},function(data){
      if(data.result=='success') {
        new_submit=true;
        getList();
      }
    },'json');

    return false;
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

  $("#user_select_list .btn-delete-confirm").click(delete_temp_user);
});
