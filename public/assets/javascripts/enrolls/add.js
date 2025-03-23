$(function () {
  $('#myModal').removeData("modal");
  // 자바스크립트가 지원될때 Tr 커서 선택
  $("#enroll_list tbody tr").css('cursor','pointer');
  $("#enroll_list tbody td").click(function(){
    var href=$(this).closest('tr').find('a').attr('href');
    if(href.indexOf('?')=='-1') {
      var url=href+'?popup=1&re-enroll=1';
    } else {
      var url=href+'&popup=1&re-enroll=1';
    }
  	$('#myModal').load(url,function(){
  		$('#myModal').modal();
    });
    
    $(this).closest('tbody').find('tr.table-primary').removeClass('table-primary');
    $(this).closest('tr').addClass('table-primary');
  });

  $("#rent_list tbody td").click(function(){
    var href=$(this).closest('tr').find('a').attr('href');
    if(href.indexOf('?')=='-1') {
      var url=href+'?popup=1';
    } else {
      var url=href+'&popup=1';
    }
  	$('#myModal').load(url,function(){
  		$('#myModal').modal();
    }); 
  });  

  $("#enroll_list tbody td a,#rent_list tbody td a").click(function(event){
	  event.preventDefault();
  });

  $("#commission_default").change(function(){
    if($(this).is(':checked')) {
      $("#o_commission_layer").hide();
      $("#o_commission").attr('disabled');
      $("#commission_display").show();
    } else {
      $("#o_commission_layer").show();
      $("#o_commission").removeAttr('disabled');      
      $("#commission_display").hide();
    }
  });

  $("#commission_default").change();

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
      $(".select_payment").val($(this).val()).effect('highlight').change();
    }
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

  $('.enroll_after_datepicker').datepicker({language: "ko",todayHighlight: true, maxViewMode : 'decades', startDate:'-0d', endDate:'+100y',autoclose:true});  
  $('.enroll_datepicker').datepicker({language: "ko",todayHighlight: true,autoclose:true});
  $('.input-daterange input').each(function() {
    $(this).datepicker({language: "ko",todayHighlight: true,autoclose:true});
  });

  // 요금제 정보표시
  function showLessonPaymentSystem () {
  var a_lesson_type = {
      '1': '기간제',
      '2': '횟수제',
      '3': '쿠폰제',
      '4': 'PT',
      '5': 'GX'
  };

      var lesson_type   = $('#e_lesson_type').val();
      var lesson_period = $('#e_lesson_period').val();

      var X = a_lesson_type[lesson_type];
      switch (lesson_type) {
          case '1': // 기간제
              X += " / "+ lesson_period +" "+ getLessonUnit();
              break;
          case '2': // 횟수제
          case '3': // PT
          case '4': // GX
          case '5': // 쿠폰제
              if (lesson_period > 0) { // 유한
                  X += " / "+ lesson_period + getLessonUnit();
              }
              else { // 무한
                  X += "/ 무기한";
              }
              break;
      }
      $('#lesson_payment_system').val(X);
  }

  // 요금제 단위 표시
  function getLessonUnit () {
    var lesson_type   = $('#e_lesson_type').val();
    var lesson_period_unit = $('#e_lesson_period_unit').val();
    
    var lesson_unit = {
      'M': '개월',
      'W': '주',
      'D': '일'
    };

    switch(lesson_type) {
      case '1': // 기간제
        var unit=lesson_unit[lesson_period_unit];
        break;
      case '3': // 쿠폰제
        var unit='개';
        break;
      default :
        var unit='회';
    }
    return unit;
  }

  function setQuantitySelect () {
      var lesson_type     = $('#e_lesson_type').val();
      var lesson_quantity = $('#e_lesson_quantity').val();
      var enroll_quantity = $('#e_enroll_quantity').val();
      var lesson_period = $('#e_lesson_period').val();      
      var lesson_unit     = getLessonUnit();
      
      if(lesson_type=='1') {
          // 기간제
          enroll_quantity = lesson_period;
      }


      var str = "";
      for(var i = 1; i <= 200; ++i) {
        var SL = (enroll_quantity == i) ? 'selected="selected"' : '';
        str += "<option value='"+ i +"' "+ SL +">"+ (lesson_quantity * i) +" "+ lesson_unit +"</option>";
      }

      var LESSON_COUNTER = {
        '1': '기간',
        '2': '횟수',
        '3': '수량',
        '4': '횟수',
        '5': '횟수'
      };  

      $('#e_quantity').html(str);
      $('.e_xl').text(LESSON_COUNTER[lesson_type]);
  }

  function setLessonFeeText () {
      if ($('#e_title').val() == '') {
          $('#lesson_fee_text').val('');
          return;
      }

      var lesson_type   = $('#e_lesson_type').val();
      var lesson_quantity = $('#e_lesson_quantity').val();
      if(lesson_type=='1') {
        lesson_quantity = $('#e_lesson_period').val();
      }

      var lesson_fee_unit = $('#e_lesson_fee_unit').val();

      var X = lesson_quantity +" "+ getLessonUnit() +"당 / "+ Number(lesson_fee_unit).toLocaleString()+'원';
      $('#e_lesson_fee_text').val(': '+X);
  }

  function showQuantity () {
      var lesson_type   = Number($('#e_lesson_type').val());
      if (lesson_type == 0) return;

      var lesson_period =$('#e_lesson_period').val();

      $('.quantity').show();
      switch (lesson_type) {
          case '2': // 횟수제
          case '3': // 쿠폰제
          case '4': // PT
          case '5': // GX
              if (lesson_period == 0) { // 무기한인 경우에는 종료일자를 셋팅할 필요가 없다.
                $('.quantity').eq(2).hide();
              }
              break;
      }
  }

  function calcLessonPeriod (isReset=false) {
      showLessonPaymentSystem();
      setLessonFeeText();
      showQuantity();
      if (isReset == true) setQuantitySelect(); // 수강과목을 변경했거나 처음 로드시에만 기간 셋팅을 새로 한다.

      var quantity = $('#e_quantity').val();
      var lesson_period = $('#e_lesson_period').val();
      var sDate = new Date($('#o_start_date').val());
      var eDate = sDate;

      var lesson_quantity = $("#e_lesson_quantity").val();
      var lesson_type   = getIntVal('#e_lesson_type');
      var lesson_period_unit = $('#e_lesson_period_unit').val();

      function calcEndDate (unit) {
        switch (lesson_period_unit) {
            case 'M':
                // 월간 계산은 매우 독특하다.
                var eX = add_month($('#o_start_date').val(), unit);
                $('#o_end_date').val(eX);
                eDate = new Date($('#o_end_date').val());                  
                break;
            case 'W':
                eDate.setDate(sDate.getDate() + (unit*7));
                break;
            case 'D':
                eDate.setDate(sDate.getDate() + unit -1 );
                break;
        }
    }

      var unlimit=false;
      switch (lesson_type) {
          case 1: // 기간제

              calcEndDate(lesson_quantity*quantity);
              break;
          case 2: // 횟수제
          case 3: // 쿠폰제
          case 4: // PT
            if (lesson_period > 0) { // 유한
                lesson_period_unit='D';
                calcEndDate(lesson_period*quantity*5);
            } else { // 무기한
                unlimit=true;
                eDate = new Date('2050-12-31');
            }
            break;
          case 5: // GX
              if (lesson_period > 0) { // 유한
                  lesson_period_unit='D';
                  calcEndDate(lesson_period*quantity*5);
              } else { // 무기한
                unlimit=true;
                eDate = new Date('2050-12-31');
              }
              break;
       }

        if(unlimit) {
            $("#unlimit_end_date").show();
            $("#limit_end_date").hide();
        } else {
            $("#unlimit_end_date").hide();
            $("#limit_end_date").show();
        }
      $('#o_end_date').val($.datepicker.formatDate('yy-mm-dd', eDate)).effect('highlight');
  }


/*
  $('#quantity_range').change(function(){
    var lesson_type   = $('#e_lesson_type').val();
    var lesson_period = $('#e_lesson_period').val();
    var quantity = $(this).val();

    if(lesson_type==4 && quantity>0) {
      var sDate = new Date($('#o_start_date').val());
      var eDate = sDate;
      eDate.setDate(sDate.getDate() + (lesson_period*quantity*5) -1 );
      $('#o_end_date').val($.datepicker.formatDate('yy-mm-dd', eDate)).effect('highlight');
    }
  }); */

  function OrderCalculator(isReset=false) {
      var lesson_fee_unit = Number($('#e_lesson_fee_unit').val());
      var quantity = Number($('#e_quantity').val());
      var dc_rate = Number($('#o_dc_rate').val());
      var dc_price = Number($('#o_dc_price').val());

      var cost = lesson_fee_unit * quantity;
      var dc_rate_price = cost * dc_rate / 100;
      var sell_price = cost - dc_rate_price - dc_price;

      $("#hidden_sell_price").val(sell_price);
      $("#o_sell_price").text(Number(sell_price).toLocaleString()).effect('highlight');
      $('#o_original_price').val(cost)
      $('#custom_price').val(addCommas(cost));
      $('#display_original_price').text(Number(cost).toLocaleString());

      calcLessonPeriod(isReset);
      calculatorPayment();
      paymentComplete();
  }

  $(".additional_product_select").click(function(){
    var search_param='format=json';
    var form_row=$(this).closest('.form-row');
    $.getJSON('/products/view/'+$(this).val(),search_param,function(data){
      if(data.result=='success') {
        if(data.content.price) {
          form_row.find('.p_price').val(data.content.price);
        }
      } else {
        alert(data.message);
      }
    });
  });

  $("#e_trainer").change(function(){
    if($(this).val()!='') {
    if($('#e_lesson_type').val()==4) {
      $("#commission_layer").show();
    } else {
      $("#commission_layer").hide();
    }
  } else {
    $("#commission_layer").hide();
  }
  });

  $("#e_trainer").change();

  $("#o_start_date").change(function(){
      calcLessonPeriod();
  });

  $('.calc').change(OrderCalculator);
  $('.calc_payment').change(calculatorPayment);

  $("#course_id").change(function(){
    $.getJSON('/courses/view/'+$(this).val(),{'user_id':$("#o_user_id").val(),'format':'json'},function(data){
      if(data.result=='success') {
        $("#e_category_title").val(': '+data.content.category_title).effect('highlight');
        $("#e_title").val(': '+data.content.title).effect('highlight');
        $("#item_name").val(data.content.title);
        $("#e_quota").val(': '+data.content.quota).effect('highlight');
        $('#o_original_price,#hidden_sell_price').val(data.content.price);
        $('#o_sell_price').text(Number(data.content.price).toLocaleString());
        $("#display_original_price").text(Number(data.content.price).toLocaleString());
        $('#e_lesson_type').val(data.content.lesson_type);
        $("#e_lesson_fee_unit").val(data.content.price);
        $('#e_lesson_period').val(data.content.lesson_period);
        $('#e_lesson_period_unit').val(data.content.lesson_period_unit);
        $('#e_lesson_quantity').val(data.content.lesson_quantity);

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

        if(data.content.trainer_id) {
          $('#e_trainer').val(data.content.trainer_id);
        } else {
          $('#e_trainer').val($('#default_trainer_id').val());
        }        

        if(data.content.lesson_type==4) {
          if($('#e_trainer').val()!='') {
            $("#commission_layer").show();
          } else {
            $("#commission_layer").hide();
          }
          $("#pt_serial").show();
        } else {
          $("#commission_layer").hide();
          $("#pt_serial").hide();
        }

        if(data.content.lesson_dayofweek) {
          $("#e_dayofweek").val(': '+dowtostr(data.content.lesson_dayofweek)).effect('highlight');
        } else {
          $("#e_dayofweek").val('무제한');
        }

        if(data.content.quota==0) {
          $("#e_quota").val('무제한');
        } else {
          $("#e_quota").val(data.content.quota);          
        }
    
        $("#no_discount").prop('checked',true).change();
        $("#use_default_price").prop('checked',true).change();   

        setQuantitySelect();
        if(!$("#e_quantity option:selected").length) {
          $("#e_quantity").val($("#e_quantity option:first").attr('value'));
        }
        OrderCalculator();
      } else {
        alert(data.message);
      }
    });
  });

  if($("#course_id").prop("tagName")=='SELECT') {
    $("#course_id").change();
  }

  $("#user_find_form").submit(function(){
    var search_type=$(this).find('input[name="user_search_type"]:checked').val();
    var search_word=$(this).find('input[type="search"]').val();
    var search_param={'search_type':'field','search_field' : search_type,'search_word' : search_word ,'format': 'json'};

    $.getJSON('/users/select',search_param,function(data){
      if(data.result=='success') {
        if(data.total==1) {
          $("#user_select_list_layer").hide();
          var content=data.list[0];

          if(!$("#dongho_c").length) {
            content.address_detail=content.birthday;
          }

          select_user(content);

          $("#r_facility_id").change();
        } else {
          if(data.total) {
            $("#user_select_list_layer").show();
            $("#user_select_list tbody").empty();
            $('#user_select_list_count').val(data.total);
          
            if(data.total) {
              $.each(data.list,function(index,value){
                var birthday='입력안됨';        
                if($("#dongho_c").length) {
                  if(value.address_detail) {
                    birthday=value.address_detail;
                  }
                } else {
                  if(value.birthday) {
                    birthday=value.birthday;
                  }
                }
          
                var input='<td class="text-center"><input name="id" value="'+value.id+'" type="radio"></td>';
                $("#user_select_list tbody").append('<tr>'+input+'<td class="name">'+value.name+'</td><td>'+value.card_no+'</td><td>'+birthday+'</td><td>'+display_gender(value.gender)+'<input type="hidden" name="gender[]" value="'+value.gender+'" /></td><td class="phone">'+add_hyphen(value.phone)+'</td></tr>');
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
            $('.user_select_rel_form input[type="submit"]').attr('disabled','disabled');
          }
        }
      } else {
        alert(data.message);
      }
    });

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

  $(window).keydown(function(event){
    if(event.keyCode == 13) {
      if($('textarea').is(':focus')) {
        return true;
      }
      
      event.preventDefault();
      return false;
    }
  });

  $("#have_date_is_today").change(function(){
    if($(this).prop('checked')) {
      $("#o_have_datetime_layer").hide();
      $("#have_date_is_today_display").show();
    } else {
      $("#o_have_datetime_layer").show();      
      $("#have_date_is_today_display").hide();
    }
  });

  $('#enroll_add_form,#enroll_edit_form').submit(function(){
    if($("#o_payment_method").val()==4) {
      if(Number($('#hidden_sell_price').val())!=(Number($('#o_mix_cash').val())+Number($('#o_mix_credit').val()))) {
        alert('결제값과 현금+카드 입력합이 일치하지 않습니다.');
        return false;
      }
    }
  });

  $('.p_price').on("keyup", function() {
    $(this).val(addCommas($(this).val().replace(/[^0-9]/g,"")));
  });

  $("#custom_price").on("keyup", function() {
    $(this).val(addCommas($(this).val().replace(/[^0-9]/g,"")));
    $("#price_text").text(fn_change_hangul_money(stripComma($(this).val()))).effect('highlight');
  });

  $("#custom_price").val(addCommas($("#hidden_sell_price").val()));
});