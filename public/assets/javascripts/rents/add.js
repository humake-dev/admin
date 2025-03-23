var current_page=0;
var gender=null;

$(function () {
  $('#myModal').removeData("modal");
  // 자바스크립트가 지원될때 Tr 커서 선택
  $("#rent_list tbody tr,#enroll_list tbody tr").css('cursor','pointer');

  $("#rent_list tbody td,#enroll_list tbody td").click(function(){
    var href=$(this).closest('tr').find('a').attr('href');
    if(href.indexOf('?')=='-1') {
      var url=href+'?popup=1&rent=1';
    } else {
      var url=href+'&popup=1&rent=1';
    }
  	$('#myModal').load(url,function(){
  		$('#myModal').modal();
    });
    
    $('#enroll_list tbody tr.table-primary,#rent_list tbody tr.table-primary').removeClass('table-primary');
    $(this).closest('tr').addClass('table-primary');  
  });

  $("#rent_list tbody td a,#enroll_list tbody a").click(function(event){
	  event.preventDefault();
  });

  var t_option={
    step_size_minutes:10,
    min_hour_value:05,
    max_hour_value:23,
    show_meridian:false,
  }

  var price=$("#r_facility_price").val();
  var quantity=1;

  $("#o_sell_price").click(function(){
    $(this).closest('form').find('.btn-primary').focus();
  });

  $("#no_discount").change(function(){
    if($(this).is(':checked')) {
      $("#o_dc_rate,#o_dc_price").val(0);
      $(".dc_layer").hide();
    } else {
      $(".dc_layer").show();
    }
  });

  $("#no_discount").change();

  $("#o_payment_method").change(function(){
    $("#o_cash").val(0);
    $("#o_credit").val(0);

    switch($(this).val()) {
      case '1' :   
        $(".mix").hide();

        $("#o_cash").val($("#o_payment").val());
        break;       
      case '4' :
        if($("#o_mix_credit").val()==0 && $("#o_mix_credit").val()==0) {
          $("#o_mix_credit").val($("#hidden_sell_price").val());        
          $("#o_mix_cash").val(0);
        }

        $("#o_mix_cash").attr('max',$("#hidden_sell_price").val());
        $("#o_mix_credit").attr('max',$("#hidden_sell_price").val());   

        $(".mix").show();

        $("#payment_layer").hide();
        $("#display_payment_layer").show();
        break;
      default :     
        $(".mix").hide();

        $("#o_credit").val($("#o_payment").val());        
        break;
    }

    if($(this).val()!=4) {
      $(".select_payment").val($(this).val()).effect('highlight');
    } 
    calculatorPayment();    
  });

  $("#o_payment_method").change();

  $("#use_default_price").change(function(){
    if($(this).is(':checked')) {
      $("#sell_price_layer").hide();
      $("#display_sell_price_layer").show();
      $("#hidden_sell_price").val($("#o_original_price").val())
      $("#custom_price").val($("#hidden_sell_price").val());      
    } else {
      $("#sell_price_layer").show();
      $("#display_sell_price_layer").hide();
    }

    paymentComplete();
  });

  function paymentComplete() {
    $("#payment_layer").hide();
    $("#display_payment_layer").show();

    $("#o_payment").val($("#hidden_sell_price").val());
    $("#display_payment").text(Number($("#hidden_sell_price").val()).toLocaleString()).effect('highlight');

    $("#o_payment_method").change();  

    calculatorPayment();    
  };  


  if(!$(".humake_rent_edit_form").length) {
    paymentComplete();
  }


  $("#custom_price").change(function(){
    /*if($(".humake_rent_edit_form").length) {
      return false;
    } */

    $("#hidden_sell_price").val($(this).val());
    $("#o_sell_price").val($(this).val());

    paymentComplete();
  });

  $('.enroll_after_datepicker').datepicker({language: "ko",todayHighlight: true, maxViewMode : 'decades', startDate:'-0d', endDate:'+100y',autoclose:true});  
  $('.rent_datepicker,.enroll_datepicker').datepicker({language: "ko",todayHighlight: true,autoclose:true});
  $('.input-daterange input').each(function() {
      $(this).datepicker({language: "ko",todayHighlight: true,autoclose:true});
  });

  $('.datepicker').datepicker({language: "ko",todayHighlight: true,autoclose:true});
  $('.input-daterange input').each(function() {
      $(this).datepicker({language: "ko",todayHighlight: true,autoclose:true});
  });

  function period_change(){
    var facility_id=$('#r_facility_id').val();
    var params={'format':'json'};

    if($(this).val()=='') {
      $('#o_end_date').val('');
      OrderCalculator();
    } else {
          if($('#r_rent_month').val()!='') {
            var eX=add_month($('#o_start_date').val(), $('#r_rent_month').val());
            quantity = Number($('#r_rent_month').val());
          }

          if($("#o_user_id").val()) {
            params.user_id=$("#o_user_id").val();
          }

          if($("#o_start_date").val() && $("#o_end_date").val()) {
            params.start_date=$("#o_start_date").val();
            params.end_date=$("#o_end_date").val();
          }
        
        if($("#o_order_id").length) {
          params.current_order_id=$("#o_order_id").val();
        }

        $('#o_end_date').val(eX).effect('highlight');    

      if($("#o_diff_price").val()==0) {
        OrderCalculator();
      }
    }

    $.getJSON('/rents/get-available-no/'+facility_id,params,function(data){
      if(data.result=='success') {
        make_r_no(data);
      }
    });
  }

  function user_select_complete() {
    if(!$(this).val())
      return false;

    $.getJSON('/view/'+$(this).val(),{'format':'json'},function(data){
      if(data.result=='success') {
        $("#r_name").val(data.content.name).effect('highlight');
        $("#r_card_no").val(data.content.card_no).effect('highlight');

        if(data.content.phone) {
            $("#r_phone").val(add_hyphen(data.content.phone)).effect('highlight');
        } else {
            $("#r_phone").val('').effect('highlight');
        }
      } else {
        alert(data.message);
      }
    });
  }

  function OrderCalculator() {
    var dc_rate = Number($('#o_dc_rate').val());
    var dc_price = Number($('#o_dc_price').val());

    var cost = price * quantity;
    var dc_rate_price = cost * dc_rate / 100;
    var sell_price = cost - dc_rate_price - dc_price;

    $('#o_original_price').val(cost);
    $('#display_original_price').text(Number(cost).toLocaleString());
    
    if($(".humake_rent_edit_form").length) {
      return true;
    }

    $("#hidden_sell_price").val(sell_price);
    $("#o_sell_price").text(Number(sell_price).toLocaleString()).effect('highlight');


    calculatorPayment();
    paymentComplete();
  }

  $('input[name="user_search_type"]').change(function(){
    $("#search_label").text($(this).parent().find('label').text());
  });

  function make_r_no(data){
    var current_no=$("#r_no").val();

    if(data.total) {
      $("#r_no").empty();

      if(data.use_not_set=='1') {
        $("#r_no").append('<option value="0">미정</option>');
      } else {
        $("#r_no").append('<option value="">선택하세요</option>');
      }
      $.each(data.list,function(index,value){
        if(value.enable) {
          $("#r_no").append('<option value="'+value.no+'">'+value.no+'</option>');
        } else {
          $("#r_no").append('<option value="'+value.no+'" disabled="disabled">'+value.no+'('+value.disable+')</option>');
        }
      });

      if($("#default_facility_id").val()) {
        if($("#default_facility_id").val()==$('#r_facility_id').val()) {
          if($("#default_no").val()) {
            $("#r_no").val($("#default_no").val());
          }
        }
      }

      $("#r_no").effect('highlight');      
    } else {
      $("#r_no").append('<option value="0">미정</option>');
    }

    if($('#r_no option[value="'+current_no+'"]').length) {
      if($('#r_no option[value="'+current_no+'"]').attr('disabled')=='disabled') {
        alert('해당기간에 겹치는 대여항목이 있어서 다시 선택해야합니다.');
        if($('#r_no option[value="0"]').length) {
          $("#r_no").val("0");
        } else {
          $("#r_no").val("");
        }
      } else {
        $("#r_no").val(current_no);
      }
    }
  }


  $("#r_facility_id").change(function(){
    var facility_id=$(this).val();

    $.getJSON('/facilities/view/'+facility_id,{'user_id':$("#o_user_id").val(),'format':'json'},function(data){
      if(data.result=='success') {
        $('#o_original_price').val(data.content.price);
        $("#display_original_price").text(Number(data.content.price).toLocaleString());

        if(!$(".humake_rent_edit_form").length) {
          $('#o_sell_price').text(Number(data.content.price).toLocaleString());
          $('#hidden_sell_price').val(data.content.price);  
        }

        if(!data.content.price) {
          $("#o_dc_rate").val(0);
        }
        $("#o_original_price,#r_facility_price").val(data.content.price);

        if(!$(".humake_rent_edit_form").length) {
          if(data.content.re_order) {
            if($("#re-order").attr('type')=='checkbox') {
              $("#re-order").prop('checked',true);
            } else {
              $("#re-order").val(1);
            }
          } else {
            if($("#re-order").attr('type')=='checkbox') {          
              $("#re-order").prop('checked',false);
            } else {
              $("#re-order").val(0);
            }
          }
        }     

            $("#r_rent_month").val(1).effect('highlight').change();


      } else {
        alert(data.message);
      }
    });
  });

  // 자바스크립트가 지원될때 Tr 커서 선택
  $("#user_select_list tbody tr").css('cursor','pointer');  
  $('#user_select_list tbody td').click(m_td_click);

  $('#user_select_list tbody tr td input').change(function(){
    var tr=$(this).closest('tr');
    var u_id=tr.find('td:first input').val();
    var u_name=tr.find('td:eq(1)').text();
    var u_phone=tr.find('td:eq(5)').text();
    var address_detail=tr.find('td:eq(3)').text();
    var picture_url='';

    var content={'id':u_id,'name':u_name,'phone':u_phone,'address_detail':address_detail,'picture_url':picture_url};
    select_user(content);
  });
  initPagination(Number($('#user_select_list_count').val()),10,0);

  $(".r_rent_period,#o_start_date,#o_end_date").change(period_change);
  $('.calc').change(OrderCalculator);
  $('.calc_payment').change(calculatorPayment);

  $("#user_find_form").submit(function(){
    getList();
    return false;
  });

  $("#user_select_cancel").click(user_select_cancel);

  $(".select_payment").change(function(){
    var tt=$(this).closest('.form-row');

    switch($(this).val()) {
      case '1' :
        tt.find('.p_price').attr('name',tt.find('.p_price').attr('name').replace('credit','cash'));
        break;
      case '2' :
        tt.find('.p_price').attr('name',tt.find('.p_price').attr('name').replace('cash','credit'));
        break;
      default :
    }
  });

  $('#rent_add_form').submit(function(){
    if($("#o_payment_method").val()==4) {
      if(Number($('#hidden_sell_price').val())!=(Number($('#o_mix_cash').val())+Number($('#o_mix_credit').val()))) {
        alert('결제값과 현금+카드 입력합이 일치하지 않습니다.');
        return false;
      }
    }

    if(!$('#o_cash').val()) {
      $('#o_cash').val(0);
    }

    if(!$('#o_credit').val()) {
      $('#o_credit').val(0);
    }

    if($("#r_no").val()=='') {
      alert('번호를 선택하세요');
      $("#r_no").effect('shake');
      return false;
    }
  });
});

function rentInfo(rent_list) {
    $("#rent_list").show().find('table tbody').empty();
    
    if(rent_list.total) {
      $.each(rent_list.list,function(index,value){
        if(value.no=='0') {
          var no='미정';
        } else {
          var no=value.no;
        }
        $("#rent_list table tbody").append('<tr><td>'+(10-Number(index))+'</td><td><a href="/rents/view/'+value.id+'">'+value.product_name+'</a></td><td>'+no+'</td></tr>');
      });
      
      $("#rent_list tbody tr").css('cursor','pointer');
      $("#rent_list tbody td").click(function(){
        var href=$(this).closest('tr').find('a').attr('href');
        if(href.indexOf('?')=='-1') {
          var url=href+'?popup=true';
        } else {
          var url=href+'&popup=true';
        }
        $('#myModal').load(url,function(){
          $('#myModal').modal();
        });
      });
    
      $("#rent_list tbody td a").click(function(event){
        event.preventDefault();
      });
    } else {
      $("#rent_list table tbody").append('<tr><td colspan="3" class="text-center">대여중인 시설물이 없습니다.</td></tr>');
    }
}

function enrollInfo(enroll_list) {
  $("#enroll_list").show().find('table tbody').empty();

  if(enroll_list.total) {
    $.each(enroll_list.list,function(index,value){
        if (value.stopped) {
          var type='<span class="text-success">사용중</span>';
        }  else {
          var type='<span class="text-warning">중지</span>';
        }

      $("#enroll_list table tbody").append('<tr class="enroll_e"><td>'+(Number(index+1))+'</td><td><a href="/enrolls/view/'+value.id+'" title="수강정보 자세히 보기">'+value.product_name+'</a></td><td>'+type+'</td></tr>');
    });
    

    $("#enroll_list tbody tr").css('cursor','pointer');
    $("#enroll_list tbody td").click(function(){
      var href=$(this).closest('tr').find('a').attr('href');
      if(href.indexOf('?')=='-1') {
        var url=href+'?popup=true';
      } else {
        var url=href+'&popup=true';
      }
      $('#myModal').load(url,function(){
        $('#myModal').modal();
      });
    });
  
    $("#enroll_list tbody td a").click(function(event){
      event.preventDefault();
    });
  } else {
    $("#free_check").remove();
    $("#enroll_list table tbody").append('<tr><td colspan="2" class="text-center">수강중인 과목이 없습니다</td></tr>');
  }
}

function getList(current_page, jq) {
  if(!current_page)
    current_page = 0;

  var perPage =10;
  var pageID=current_page+1;

  var searchType=null;
  var searchField=null;
  var searchWord=null;

  if($.trim($("#u_search_word").val())!='') {
    searchType='field';
    searchField=$('input[name="user_search_type"]:checked').val();
    searchWord=$.trim($("#u_search_word").val());
  }

    var search_param={'search_type':'field','search_field' : searchField,'search_word' : searchWord ,'format': 'json','per_page':perPage,'page':pageID};

    if($('aside input[name="enroll_info"]').length) {
      search_param.enroll_info=1;
    }

    $.getJSON('/users/select',search_param,function(data){
      if(data.result=='success') {
        if(data.total==1) {
          $("#user_select_list_layer").hide();
          var content=data.content;

          if(!$("#dongho_c").length) {
            content.address_detail=content.birthday;
          }

          select_user(content);

          if(data.rent_info) {
            $("#rent_list h3 span").text(content.name);              
            rentInfo(data.rent_list);
          }

          if(data.enroll_info) {
            $("#enroll_list h3 span").text(content.name);              
            enrollInfo(data.enroll_list);
          }
        
          $("#rent_user_find_form h3 span:first").hide();
          $("#rent_user_find_form h3 span:eq(1)").show();

          $("#r_facility_id").change();
        } else {
          if(data.total) {
            $("#user_select_list_layer").show();
            $("#user_select_list tbody").empty();
            $('#user_select_list_count').val(data.total);
          
            if(data.total) {
              $.each(data.list,function(index,value){
                if($("#dongho_c").length) {
                  if(value.address_detail) {
                    var birthday=value.address_detail;
                  } else {
                    var birthday='입력안됨';
                  }
                } else {
                  if(value.birthday) {
                    var birthday=value.birthday;
                  } else {
                    var birthday='입력안됨';
                  }
                }
          
                var input='<td class="text-center"><input name="id" value="'+value['id']+'" type="radio"></td>';


                var tr='<tr>'+input+'<td class="name">'+value['name']+'</td>';
                
                if($("#th_access_card_no").length) {
                  tr+='<td>'+value['card_no']+'</td>';
                }

                tr+='<td>'+birthday+'</td><td>'+display_gender(value['gender'])+'<input type="hidden" name="gender[]" value="'+value['gender']+'" /></td><td class="phone">'+add_hyphen(value['phone'])+'</td></tr>';
                $("#user_select_list tbody").append(tr);
              });
          
              $('#user_select_list tbody td').click(m_td_click);
              $('#user_select_list tbody tr td input').click(function(e) {
                e.stopPropagation();
              }).change(function(){
                var search_param={'user_id' : $(this).val() ,'format': 'json'};
    
                if($('aside input[name="rent_info"]').length) {
                  search_param.rent_info=1;
                }                

                $.getJSON('/users/select',search_param,function(data){
                  if(data.result=='success') {
                    if(data.total==1) {
                      $("#user_select_list_layer").hide();
                      var content=data.list[0];
            
                      if(!$("#dongho_c").length) {
                        content.address_detail=content.birthday;
                      }
            
                      select_user(content);
            
                      if(data.rent_info) {
                        $("#rent_list h3 span").text(content.name);                            
                        rentInfo(data.rent_list);
                      }

                      if(data.enroll_info) {
                        $("#enroll_list h3 span").text(content.name);                            
                        enrollInfo(data.enroll_list);
                      }                      
            
                      $("#rent_user_find_form h3 span:first").hide();
                      $("#rent_user_find_form h3 span:eq(1)").show();
            
                      $("#r_facility_id").change();
                    } else {

                    }
                  }
                });
              });
            } else {
              $("#user_select_list tbody").append('<tr><td colspan="4" style="text-align:center">해당 데이터가 없습니다.</td></tr>');
            }
            $(".sl_pagination").removeData("load").empty();
            initPagination(data.total,10,current_page);  
          } else {
            $("#user_select_list_layer").hide();
            alert('해당 회원이 없습니다.');
            $('.user_select_rel_form input[type="submit"]').attr('disabled','disabled');
          }
        }
      } else {
        alert(data.message);
      }
    });

    return false;
}

function m_td_click() {
  $(this).parent().find('input:first').prop('checked',true).change();
}

function select_user(content) {
  $("#o_user_id").val(content.id);
  $("#user_info").show();
  $("#user_search").hide();
  $("aside").removeAttr('class').attr('class','col-12 col-lg-4 col-xxl-3');
  $("#right_data_form").removeAttr('class').attr('class','col-12 col-lg-8 col-xxl-9');

  var name=content.name;

  $("#user_name").html(name);
  
  var phone='미입력';
  var address='미입력';

  if(content.phone) {
    phone=add_hyphen(content.phone);
  }

  $("#user_phone").text(phone);

  if(content.address_detail) {
    address=content.address_detail;
  }

  $("#user_address").text(address);

  if(content.picture_url) {
    $("#profile_photo").attr('src',content.picture_url);
  } else {
    $("#profile_photo").attr('src','/assets/images/common/bg_photo_none.gif');
  }

  if(gender!=content.gender) {
    gender=content.gender;
    $.getJSON('/facilities',{'format':'json','gender':gender,'per_page':1000,'page':0},function(data) {
      if(data.result=='success') {
        if(data.total) {
          $("#r_facility_id").empty();
          var default_value=false;
          $.each(data.list,function(index,value){
            $("#r_facility_id").append('<option value="'+value.id+'">'+value.title+'</option>');

            if($("#default_facility_id").length) {
              if($("#default_facility_id").val()==value.id) {
                default_value=value.id;
              }
            }
          });       

          if(default_value) {
            $("#r_facility_id").val(default_value);
          }
          
          $("#r_facility_id").effect('highlight').change();
        } else {
        }
      } else {
      }
  });
  }
  
  $('.user_select_rel_form input[type="submit"]').removeAttr('disabled');
}
