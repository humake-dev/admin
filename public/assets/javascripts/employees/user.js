function getList(current_page, jq) {
  if(!current_page)
    current_page = 0;

  var perPage =10;
  var pageID=current_page+1;

  $.getJSON('',{'format':'json','per_page':perPage,'page':pageID},function(data) {
      if(data.result=='success') {
        $("#employee_user_table tbody").empty();
        $('#employee_user_count').val(data.total);

        if(data.total) {
          $.each(data.list,function(index,value){
            var number=data.total-(perPage*(pageID-1))-index;

            var transaction_date=value.transaction_date;
            
            var period_month='-';
            var e_status='<span class="text-warning">만료</span>';
            employee_id=$("#form_photo").attr('action').split('update-photo/')[1];

            if(value.period) {
              period_month=value.period;
            }

            if(Number(value.available_count)>0) {
              e_status = '<span class="text-success">유효</span>';
            }

            if(value.phone) {
              var phone=add_hyphen(value.phone);
            } else {
              var phone='입력안됨';
            }

            var odd_even_select=false;
            var odd_even_match=false;

            if($('#select-oe').val()!='') {
              odd_even_select=true;
              var result = (pageID % 2  == 0) ? "even" : "odd";
              if($('#select-oe').val()==result) {
                odd_even_match=true;
              }
            }
            

            if(odd_even_select) {
              if(odd_even_match) {
                var checked=' checked="checked"';
                if($('#eu_unselect input[value="'+value.id+'"]').length) {
                  checked='';                
                } 
              } else {
                var checked=''; 
                if($('#eu_select input[value="'+value.id+'"]').length) {
                  checked=' checked="checked"';
                }
              }              
            } else {
            if($('#check_real_all').is(':checked')) {
              var checked=' checked="checked"';
              if($('#eu_unselect input[value="'+value.id+'"]').length) {
                checked='';                
              } 
            } else {
                var checked=''; 
                if($('#eu_select input[value="'+value.id+'"]').length) {
                  checked=' checked="checked"';
                }
            }
          }

            $("#employee_user_table tbody").append('<tr><td>'+number+'</td><td><a href="/view/'+value['id']+'" target="_blank">'+value['name']+' / '+phone+'</a></td><td class="text-right">'+e_status+'</td><td class="text-right">'+period_month+'</td><td>'+transaction_date+'</td><td class="text-right"><label style="display:block"><input type="checkbox" name="user_id[]" value="'+value['id']+'"'+checked+'></label></td></tr>');
          });

          $('#employee_user_table input[name="user_id[]"]').change(eu_select_change);
          $("#employee_user_table tr").css('cursor','pointer');

          if($("#employee_user_table tbody input:checkbox:not(:checked)").length) {
            $('#check_all').prop('checked',false);
          } else {
            $('#check_all').prop('checked',true);
          }  

        } else {
          $("#employee_user_table tbody").append('<tr><td colspan="2" style="text-align:center">해당 데이터가 없습니다.</td></tr>');
        }
        
        $(".sl_pagination").removeData("load").empty();
        initPagination(data.total,10,current_page);
      } else {
      }
  });
}

function eu_select_change(){
  var eu_total=$("#eu_search_form strong.mark").text();    
  n_eu_total=Number(eu_total.replace('명',''));

  if($('#select-oe').val()=='') {
    if($('#check_real_all').is(':checked')) {
      if($(this).is(':checked')) {
        $('#eu_unselect input[value="'+$(this).val()+'"]').remove();
      } else {
        var c_input=$(this).clone(false);
        c_input.attr('type','hidden');
        c_input.attr('name','unselect_user_id[]');
        c_input.removeAttr('checked');
        $("#eu_unselect").append(c_input);    
      }
  
      var eu_unselect_total=$('#eu_unselect input').length;
  
      if(eu_unselect_total==n_eu_total) {
        $("#select_label").hide();
        $("#no_select_label").show();
      }
  
      $("#eu_total").text(n_eu_total-eu_unselect_total);
    } else {
      if($(this).is(':checked')) {
        var c_input=$(this).clone(false);
        c_input.attr('type','hidden');
        c_input.attr('name','select_user_id[]');
        c_input.removeAttr('checked');
        $("#eu_select").append(c_input);
      } else {
        $('#eu_select input[value="'+$(this).val()+'"]').remove();
      }
  
      var eu_select_total=$('#eu_select input').length;
      
      if(eu_select_total==1) {
        $("#select_label").show();
        $("#no_select_label").hide();
      } else {
        if(!eu_select_total) {
          $("#select_label").hide();
          $("#no_select_label").show();
        }
      }
      $("#eu_total").text(eu_select_total);
    }
  } else {
    if($('.sl_pagination .active').length) {
      var cp=Number($('.sl_pagination span.active:first').text());
      var result = (cp % 2  == 0) ? "even" : "odd";


      if($('#select-oe').val()==result) {
        if($(this).is(':checked')) {
          $('#eu_unselect input[value="'+$(this).val()+'"]').remove();
        } else {
          var c_input=$(this).clone(false);
          c_input.attr('type','hidden');
          c_input.attr('name','unselect_user_id[]');
          c_input.removeAttr('checked');
          $("#eu_unselect").append(c_input);    
        }
    
        var eu_unselect_total=$('#eu_unselect input').length;
    
        if(eu_unselect_total==n_eu_total) {
          $("#select_label").hide();
          $("#no_select_label").show();
        }
      } else {

        if($(this).is(':checked')) {
          var c_input=$(this).clone(false);
          c_input.attr('type','hidden');
          c_input.attr('name','select_user_id[]');
          c_input.removeAttr('checked');
          $("#eu_select").append(c_input);
        } else {
          $('#eu_select input[value="'+$(this).val()+'"]').remove();
        }
    
        var eu_select_total=$('#eu_select input').length;
        
        if(eu_select_total==1) {
          $("#select_label").show();
          $("#no_select_label").hide();
        } else {
          if(!eu_select_total) {
            $("#select_label").hide();
            $("#no_select_label").show();
          }
      }


      }
      change_oe_count();
    }
  }

  if($("#employee_user_table tbody input:checkbox:not(:checked)").length) {
    $('#check_all').prop('checked',false);
  } else {
    $('#check_all').prop('checked',true);
  }
}


function change_oe_count() {
  if(!$('.sl_pagination .active').length) {
    return false;
  }
  
  var cp=Number($('.sl_pagination span.active:first').text());
  var result = (cp % 2  == 0) ? "even" : "odd";
  var select_value=$('#select-oe').val();

          var eu_total=$("#eu_search_form strong.mark").text();
          var n_eu_total=Number(eu_total.replace('명',''));
          var aa=Math.floor((n_eu_total/10));
          var bb=Math.floor((aa/2));
          
          var poe=(aa % 2  == 0) ? "even" : "odd";

          if(poe=='odd' && poe==select_value) {
            var oe_total=(bb*10)+10;
          } else {
              var oe_total=(bb*10);
          }

          var md=Number(n_eu_total%10);
          if(md!=0) {
            if(poe!=select_value) {
              oe_total+=md;
            }
          }

          if($('#eu_select input').length) {
            oe_total+=$('#eu_select input').length
          }

          if($('#eu_unselect input').length) {
            oe_total-=$('#eu_unselect input').length
          }

          $("#eu_total").text(oe_total);

          $("#select_label").show();
          $("#no_select_label").hide();

    return result;

}


$(function () {
  $('#select-oe').change(function() {
    $('#eu_unselect,#eu_select').empty();

      if($(this).val()=='') {
        $("#check_all").prop('checked',false);
        $('#employee_user_table tbody input[type="checkbox"]').prop('checked',false);
        
        $("#select_label").hide();
        $("#no_select_label").show();
      } else {
        var result=change_oe_count();

        if($('#select-oe').val()==result) {
          $('#employee_user_table tbody input[type="checkbox"]').prop('checked',true);
            $("#check_all").prop('checked',true);
        } else {
          $('#employee_user_table tbody input[type="checkbox"]').prop('checked',false);
          if($("#check_all").prop('checked')) {
            $("#check_all").prop('checked',false);
          }
        }
  
        if($("#check_real_all").prop('checked')) {
            $("#check_real_all").prop('checked',false);
        }
      }
    });

  $("#check_real_all").click(function(){
    var tbody=$(this).closest('form').find('tbody');

    $('#eu_unselect,#eu_select').empty();

    if($(this).prop('checked')) {
      $('#select-oe').val('').change();

      var eu_total=$("#eu_search_form strong.mark").text();
      n_eu_total=Number(eu_total.replace('명',''));
      $("#eu_total").text(n_eu_total);

      $("#select_label").show();
      $("#no_select_label").hide();

      tbody.find('input:not(:checked)').prop('checked',true).change();
      $(this).prop('checked',true);
      $("#check_all").prop('checked',true);
    } else {
      $("#select_label").hide();
      $("#no_select_label").show();

      tbody.find('input:checked').prop('checked',false).change();
      $(this).prop('checked',false);
      $("#check_all").prop('checked',false);
    }
  });

  $("#check_all").click(function(){
    var tbody=$(this).closest('table').find('tbody');

    if($(this).is(':checked')) {
      tbody.find('input').prop('checked',true).change();
    } else {
      tbody.find('input').prop('checked',false).change();
    }


    eu_select_change();
  });

  $('#employee_user_table input[name="user_id[]"]').change(eu_select_change);

  check_checked('employee_user_table');  


  // 자바스크립트가 지원될때 Tr 커서 선택
  $("#employee_list tr").css('cursor','pointer');

  $("#employee_list td").click(function(){
    location.href=$(this).parent().find('a').attr('href');
  });

  $('#photo_load').click(function () {
  	$(this).parent().find('input:file').prop('capture', '');
  	$(this).parent().find('input:file').trigger('click');
  });

  if($("#employee_user_table").length) {
    initPagination(Number($('#employee_user_count').val()),10);
  }

  $('#form_photo input:file').change(function () {
  		var formData = new FormData();
  		formData.append('photo[]', $('input:file')[0].files[0]);
  		formData.append('format','json');

      $.ajax({
          url :$('#form_photo').attr('action'),
          type: "POST",
          data : formData,
          processData: false,
          contentType: false,
          success:function(data, textStatus, jqXHR){
            var json = $.parseJSON(data);

            if(json.id!=true) {
              var form=$('<form action="/employee-pictures/delete/'+json.id+'" method="post" accept-charset="utf-8" id="delete-photo-form">');
              form.append('<input value="'+$("#delete-photo-layer span").text()+'" class="btn btn-sm btn-outline-secondary btn-block" type="submit">');
              $("#delete-photo-layer").empty().append(form);
              
              form.submit(delete_employee_photo);
            }

            showPhoto(urldecode(json.photo));
          },
          error: function(jqXHR, textStatus, errorThrown){
              //if fails
          }
    });
  });

  $('#employee_user_search .card-header').click(function(){
    if($(this).find('.buttons i').text()=='keyboard_arrow_down') {
      $.post('/employees/user-search-oc',{'format':'json'},function(){
      
      },'json');
    } else {
      $.post('/employees/user-search-oc/open',{'format':'json'},function(){
      
      },'json');
    }
  });
  

  $('.employee_content_section .card-header').click(function(){
    if($(this).find('.buttons i').text()=='keyboard_arrow_down') {
      $.post('/employees/index-oc',{'format':'json'},function(){

      },'json');
    } else {
      var card=$(this).closest('.card');
      var index=card.find('.card-block').index(card.find('.card-block:visible'));
      
      if(index) {
        if(index==1) {
          $.post('/employees/index-oc/permission',{'format':'json'},function(){

          },'json');
        } else {
          $.post('/employees/index-oc/access-control',{'format':'json'},function(){

          },'json');
        }              
      } else {
        $.post('/employees/index-oc/default',{'format':'json'},function(){

        },'json');
      }
    }
  });

  $(".employee_content_section .card-header .nav-item .nav-link").click(function(event){
    event.preventDefault();
    event.stopPropagation();

    var card=$(this).closest('.card');
    var index=card.find('.card-header .nav-item .nav-link').index($(this));

    if(index) {
      if(index==1) {
        $.post('/employees/index-oc/permission',{'format':'json'},function(){

        },'json');
      } else {
        $.post('/employees/index-oc/access-control',{'format':'json'},function(){

        },'json');
      } 
    } else {
      $.post('/employees/index-oc/default',{'format':'json'},function(){

      },'json');
    }
  });

  $('#delete-photo-form').submit(delete_employee_photo);

  function delete_employee_photo() {
    if(!confirm('정말로 삭제합니까?')) {
      return false;
    }
  }
});
