var multi=true;
var employee_status={H:"이용중",L:"휴식",R:"이용종료"};

function getList(current_page, jq) {
  if(!current_page)
    current_page = 0;

  var perPage =10;
  var pageID=current_page+1;

  var type=$("#e_poisiton").val();
  var status=$("#e_status").val();


  $.getJSON('/employees/select',{'format':'json','status[]':status,'position':type,'per_page':perPage,'page':pageID},function(data) {
      if(data.result=='success') {
        $("#employee_select_list tbody").empty();
        $('#employee_select_list_count').val(data.total);

        if(data.total) {
          if(multi) {
            var input_no='<td><input name="nottoo" value="0" type="checkbox"></td>';
          } else {
            var input_no='<td class="text-center"><input name="nottoo" value="0" type="radio"></td>';
          }

          $("#employee_select_list tbody").append('<tr>'+input_no+'<td class="name">담당자 없음</td><td colspan="4">&nbsp;</td></tr>');

          $.each(data.list,function(index,value){
            if(multi) {
              var input='<td><input name="id[]" value="'+value['id']+'" type="checkbox"></td>';
            } else {
              var input='<td class="text-center"><input name="id" value="'+value['id']+'" type="radio"></td>';
            }

            if(value['gender']==1) {
               var gender='남자';
             } else {
               var gender='여자';
            }

            var roles='';
            if(value['is_trainer']==1) {
              roles+=' 트레이너';
              employee_class='employee_trainer';
            }

            if(value['is_fc']==1) {
              roles+=' FC';
              employee_class='employee_fc';
            }

            $("#employee_select_list tbody").append('<tr>'+input+'<td class="name">'+value['name']+'</td><td>'+value['role_name']+'</td><td>'+employee_status[value.status]+'</td><td>'+gender+'</td><td>'+roles+'<input type="hidden" value="1" class="'+employee_class+'"></td></tr>');
          });

          $('#employee_select_list tbody td').click(m_td_click);
          $('#employee_select_list tbody tr td input').click(function(e) {
            e.stopPropagation();
          }).change(m_input_change);

          check_employee($('#e_employee_id').val());
        } else {
          $("#employee_select_list tbody").append('<tr><td colspan="4" style="text-align:center">해당 데이터가 없습니다.</td></tr>');
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

    var tbody = $(this).closest('tbody');
    if (tbody.find('input:checkbox').length == tbody.find('input:checked').length) {
      $("#employee_select_check_all").prop('checked', true);
    } else {
      $("#employee_select_check_all").prop('checked', false);
    }
  } else {
    $(this).closest('tbody').find('tr').removeClass('table-primary');

    if ($(this).prop('checked')) {
      $(this).closest('tr').addClass('table-primary');
    }
  }
}

  function check_employee(employee_id) {
  var i_input=$('#employee_select_list input[value="'+employee_id+'"]');
  var tr=$('#employee_select_list input[value="'+employee_id+'"]').parent().parent();

  tr.addClass('table-primary').find('input').prop('checked',true);
}

$(function () {
  if(!$("#employee_select_check_all").length) {
    multi=false;
  }
  // 자바스크립트가 지원될때 Tr 커서 선택
  $("#employee_select_list tbody tr").css('cursor','pointer');

  initPagination(Number($('#employee_select_list_count').val()),10,0);

  $("#e_poisiton,#e_status").change(function(){
    getList();
  });

  if($('#e_employee_id').length) {
    if($('#e_employee_id').val()) {
      check_employee($('#e_employee_id').val());
    }
  }

  $('#employee_select_list tbody td').click(m_td_click);
  $('#employee_select_list tbody tr td input').click(function(e) {
    e.stopPropagation();
  }).change(m_input_change);

  $('#select').click(function (){
    var employee_id=$('#employee_select_list tbody input:checked').val();
    var tr=$('#employee_select_list tbody input:checked').closest('tr');
    var name=tr.find('td.name').text();

    if($("#select_type_require").length) {
      var type='fc';

      if($('#e_poisiton').val()=='trainer') {
      //if(tr.find('.employee_trainer').length) {
        type='trainer';
      }

      if(type=='trainer') {
        $("#e_employee_trainer_id").val(employee_id);
        $("#e_employee_fc_id").val('');
        $("#s_employee_label").text('트레이너');
      } else {
        $("#e_employee_fc_id").val(employee_id);
        $("#e_employee_trainer_id").val('');
        $("#s_employee_label").text('FC');
      }
    } else {
      $("#e_employee_id").val(employee_id);
    }

    if($("#clear_employee").length) {
      $("#clear_employee").show();
    }

    $("#s_employee").val($.trim(name));    

    $("#myModal").modal('hide');
  });
});
