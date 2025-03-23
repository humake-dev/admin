var current_page=0;
var gender=null;

$(function () {
  $('#myModal').removeData("modal");
  // 자바스크립트가 지원될때 Tr 커서 선택
  $("#enroll_list tbody tr").css('cursor','pointer');
  $("#enroll_list tbody td").click(function(){
    var href=$(this).closest('tr').find('a').attr('href');
    if(href.indexOf('?')=='-1') {
      var url=href+'?popup=true&rent=true';
    } else {
      var url=href+'&popup=true&rent=true';
    }
  	$('#myModal').load(url,function(){
  		$('#myModal').modal();
  	});
  });

  $("#enroll_list tbody td a").click(function(event){
	  event.preventDefault();
  });

  if($('.enroll_after_datepicker').length) {
  $('.enroll_after_datepicker').datepicker({language: "ko",todayHighlight: true, maxViewMode : 'decades', startDate:'-0d', endDate:'+100y'});  
  }
  $('.rent_datepicker,.enroll_datepicker').datepicker({language: "ko",todayHighlight: true});
  $('.input-daterange input').each(function() {
      $(this).datepicker({language: "ko",todayHighlight: true});
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
        $("#payment_complete").show();
        $("#payment_complete_label").show();        
        $(".mix").hide();

        $("#o_cash").val($("#o_payment").val());
        break;      
      case '4' :
        $("#payment_complete").prop('checked',false).hide();
        $("#payment_complete_label").hide();

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
        $("#payment_complete").show();
        $("#payment_complete_label").show();      
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
      $("#sell_price_layer,#price_text").hide();
      $("#display_sell_price_layer").show();
      $("#hidden_sell_price").val($("#o_original_price").val())
      $("#custom_price").val(addCommas($("#hidden_sell_price").val()));     
    } else {
      $("#sell_price_layer,#price_text").show();
      $("#display_sell_price_layer").hide();
    }

    paymentComplete();
  });

  function paymentComplete(){
    $("#payment_layer").hide();
    $("#display_payment_layer").show();
    $("#o_payment").val($("#hidden_sell_price").val());
    $("#display_payment").text(Number($("#hidden_sell_price").val()).toLocaleString()).effect('highlight');
    $("#price_text").text(fn_change_hangul_money(stripComma($('#hidden_sell_price').val()))).effect('highlight');
    
    calculatorPayment();
  };

  paymentComplete();


  $("#custom_price").change(function(){
    $("#hidden_sell_price").val(stripComma($(this).val()));
    $("#o_sell_price").val($(this).val());

    paymentComplete();
  });

  $("#custom_price").on("keyup", function() {
    $(this).val(addCommas($(this).val().replace(/[^0-9]/g,"")));
    $("#price_text").text(fn_change_hangul_money(stripComma($(this).val()))).effect('highlight');
  });

  $("#custom_price").val(addCommas($("#hidden_sell_price").val()));


  var price=$("#r_product_price").val();
  var quantity=1;

  $('.datepicker').datepicker({language: "ko",todayHighlight: true});
  $('.input-daterange input').each(function() {
      $(this).datepicker({language: "ko",todayHighlight: true});
  });

  function period_change(){
    if($(this).val()=='') {
      $('#o_end_date').val('');
      OrderCalculator();
    } else {
      var eX=add_month($('#o_start_date').val(), $('#r_rent_month').val());
      quantity = Number($('#r_rent_month').val());
      $('#o_end_date').val(eX).effect('highlight');        
      OrderCalculator();
    }
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

    $("#hidden_sell_price").val(sell_price);
    $("#o_sell_price").text(Number(sell_price).toLocaleString()).effect('highlight');
    $('#o_original_price').val(cost)
    $('#custom_price').val(addCommas(cost));
    $('#display_original_price').text(Number(cost).toLocaleString());

    paymentComplete();
    calculatorPayment();
  }

  $("#select_type input").change(function(){
    var select_value=$('#select_type input[name="type"]:checked').val();
    if(select_value=='day' /* || select_value=='time'*/) {
      price=$("#r_facility_sub_price").val();
      $("#f_original_price").val(price);
      $("#display_original_price").text(Number(price).toLocaleString()); 
      $("#select_day").show();
      $("#select_month").hide();      
    } else {
      price=$("#r_facility_price").val();
      $("#f_original_price").val(price);
      $("#display_original_price").text(Number(price).toLocaleString());
      $("#select_day").hide();
      $("#select_month").show();      
    }
  });

  $('input[name="user_search_type"]').change(function(){
    $("#search_label").text($(this).parent().find('label').text());
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

    var content={'id':u_id,'name':u_name,'phone':u_phone,'address_detail':address_detail};
    select_user(content);    
  });
  initPagination(Number($('#user_select_list_count').val()),10,0);


  $("#r_rent_month,#o_start_date").change(period_change);
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

  $(".payment_label").each(function(){
    $(this).removeAttr('for').html($(this).html()+' <input type="radio" name="payment_type" class="payment_type" />');
    $('.payment_type').click(function(){
      $('.payment_type').closest('.form-group').find('input.calc_payment').val(0);
      $(this).closest('.form-group').find('input.calc_payment').val($("#o_sell_price").val()).change();
    });
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
  });
});


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
      search_param['enroll_info']=1;
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

          if(data.enroll_info) {
            $("#enroll_list").show().find('table tbody').empty();
            
            if(data.enroll_list.total) {
              $.each(data.enroll_list.list,function(index,value){
                var status='';
                $("#enroll_list table tbody").append('<tr><td>'+(10-Number(index))+'</td><td><a href="/enrolls/view/'+value.id+'">'+value.product_name+'</a></td><td>'+status+'</td></tr>');
              });
              
              $("#enroll_list h3 span").text(content.name);
              $("#enroll_list tbody tr").css('cursor','pointer');
              $("#enroll_list tbody td").click(function(){
                var href=$(this).closest('tr').find('a').attr('href');
                if(href.indexOf('?')=='-1') {
                  var url=href+'?popup=true&rent-sw=true';
                } else {
                  var url=href+'&popup=true&rent-sw=true';
                }
                $('#myModal').load(url,function(){
                  $('#myModal').modal();
                });
              });
            
              $("#enroll_list tbody td a").click(function(event){
                event.preventDefault();
              });
            } else {
              $("#enroll_list table tbody").append('<tr><td colspan="3" class="text-center">대여중인 시설물이 없습니다.</td></tr>');
            }
          }

          $("#rent_user_find_form h3 span:first").hide();
          $("#rent_user_find_form h3 span:eq(1)").show();
        } else {
          if(data.total) {
            $("#user_select_list_layer").show();
            $("#user_select_list tbody").empty();
            $('#user_select_list_count').val(data.total);
          
            if(data.total) {
              $.each(data.list,function(index,value){
                if($("#dongho_c").length) {
                  if(value['address_detail']) {
                    var birthday=value['address_detail'];
                  } else {
                    var birthday='입력안됨';
                  }
                } else {
                if(value['birthday']) {
                  var birthday=value['birthday'];
                } else {
                  var birthday='입력안됨';
                }
                }
          
                var input='<td class="text-center"><input name="id" value="'+value['id']+'" type="radio"></td>';
                $("#user_select_list tbody").append('<tr>'+input+'<td class="name">'+value['name']+'</td><td>'+value['card_no']+'</td><td>'+birthday+'</td><td>'+display_gender(value['gender'])+'<input type="hidden" name="gender[]" value="'+value['gender']+'" /></td><td class="phone">'+add_hyphen(value['phone'])+'</td></tr>');
              });
          
              $('#user_select_list tbody td').click(m_td_click);
              $('#user_select_list tbody tr td input').click(function(e) {
                e.stopPropagation();
              }).change(function(){
                var tr=$(this).closest('tr');
                var u_id=tr.find('td:first input').val();
                var u_name=tr.find('td:eq(1)').text();
                var u_phone=tr.find('td:eq(5)').text();
                var address_detail=tr.find('td:eq(3)').text();
                var gender=tr.find('td:eq(4) input').val();
                var content={'id':u_id,'name':u_name,'phone':u_phone,'address_detail':address_detail,'gender':gender};
                select_user(content);    
              });
            } else {
              $("#user_select_list tbody").append('<tr><td colspan="4" style="text-align:center">해당 데이터가 없습니다.</td></tr>');
            }
            $(".sl_pagination").removeData("load").empty();
            initPagination(data.total,10,current_page);  
          } else {
            $("#user_select_list_layer").hide();
            alert('해당 회원이 없습니다.');
            $('#rent_add_form input[type="submit"]').attr('disabled','disabled');
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
  
  $("#user_name").text(name);
  if(content.phone) {
    var phone=content.phone;
  } else {
    var phone='미입력';
  }
  $("#user_phone").text(phone);

  if(content.address_detail) {
    var address=content.address_detail;
  } else {
    var address='미입력';
  }
  $("#user_address").text(address);
  $('#rent_add_form input[type="submit"]').removeAttr('disabled');
}
