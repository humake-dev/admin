$(function () {
  var s_data={};

  $("#c_name").click(function(){
    $(this).parent().find('.t-select-user').click();
  }).css('cursor','pointer');

	$(".t-select-user").click(function(event){
    event.preventDefault();
    
    if(!$("#o_other_branch").is(":checked")) {
      if($("#cb_branch").val()=='') {
        alert('먼저 지점을 선택해주세요');
        return false;        
      }

      s_data.branch_id=$("#cb_branch").val();
    }

		$('#myModal').removeData("modal");
		$('#myModal').load('/users/select/single?popup=no',s_data,function(){
			$('#myModal').modal();
		});
  }).css('cursor','pointer');

  $('.trans-schedule-datepicker').datepicker({language: "ko",todayHighlight: true, endDate:'0'});

  $("#order_transfer").change(function(){
    if($(this).prop('checked')) {
      $(this).closest('h3').find('span').text('양도할 락커정보');
    } else {
      $(this).closest('h3').find('span').text('락커정보');
    }
  });

  $("#o_no_schedule").change(function(){
    if($(this).prop('checked')) {
      $("#o_schedule_date").hide().find('input').removeAttr('required');
    } else {
      $("#o_schedule_date").show().find('input').attr('required','required');
    }
  });
  
  $("#o_other_branch").change(function(){
    if($(this).prop('checked')) {
      $(".to_other_branch").hide().find('select').removeAttr('required');
    } else {
      $(".to_other_branch").show().find('select').attr('required','required');
    }
  });

  $("#cb_branch").change(function(){
    var l_branch_id=$(this).val();

    $("#new_product option").remove();

    if(l_branch_id=='') {
        $("#new_product").attr('disabled',true).append('<option value="0">'+$('#text_sbf').val()+'</option>').effect('highlight');                     
    } else {
      var params={'branch_id':l_branch_id,'format':'json'}
      $("#new_product").removeAttr('disabled'); 

      switch($("#product_type").val()) {
        case 'rent' :
          var requestURL='/facilities';
          break;
        case 'rent_sw':
          var requestURL='/sports-wears';
          break;
        default :
          var requestURL='/courses';
          params['lesson_type']=$('#transfer_lesson_type').val();
      }

      $.getJSON(requestURL,params,function(data){
        if(data.total<1) {
          $("#new_product").empty();
          return false;
        }

        branch_id=l_branch_id;
        
        $("#new_product").each(function(i,select_e){
          $.each(data.list,function(index,value){
            $(select_e).append('<option value="'+value.product_id+'">'+value.title+'</option>');
          });
          $(select_e).effect('highlight');
        });
      });
    }

    if($('#same-rent').length) {
      if($('#same-rent input[name="same_rent"]').length) {
        $("#new_product_rent").removeAttr('disabled');
        $("#new_product_rent option").remove();

        $.getJSON('/facilities',params,function(data){
          if(data.total<1) {
            $("#new_product_rent").empty();
            return false;
          }
          
          $("#new_product_rent").each(function(i,select_e){
            $.each(data.list,function(index,value){
              $(select_e).append('<option value="'+value.product_id+'">'+value.title+'</option>');
            });
            $(select_e).effect('highlight');
          });
        });
      }

      if($('#same-rent input[name="same_rent_sw"]').length) {
        $("#new_product_rent_sw").removeAttr('disabled');
        $("#new_product_rent_sw option").remove();
        
        $.getJSON('/sports-wears',params,function(data){
          if(data.total<1) {
            $("#new_product_rent_sw").empty();
            return false;
          }
          
          $("#new_product_rent_sw").each(function(i,select_e){
            $.each(data.list,function(index,value){
              $(select_e).append('<option value="'+value.product_id+'">'+value.title+'</option>');
            });
            $(select_e).effect('highlight');
          });
        });
      }
    }
  });

  $("#o_payment_method").change(function(){
    $("#o_cash").val(0);
    $("#o_credit").val(0);

    switch($(this).val()) {
      case '1' :     
        $(".mix").hide();
        
        $("#o_cash").val($("#trans_commmission").val());
        break;
      case '4' :
        if($("#o_mix_credit").val()==0 && $("#o_mix_credit").val()==0) {
          $("#o_mix_credit").val($("#hidden_commission").val());        
          $("#o_mix_cash").val(0);
        }

        $(".mix").show();

        $("#payment_layer").hide();
        $("#display_payment_layer").show();
        break;
      default :   
        $(".mix").hide();
        $("#o_credit").val($("#trans_commmission").val());        
        break;
    }

    if($(this).val()!=4) {
      $(".select_payment").val($(this).val()).effect('highlight').change();
    }
  });

  $('.calc_payment').change(calculatorPayment);

  $('.enroll_datepicker').datepicker({language: "ko",todayHighlight: true,autoclose:true});

  $('#trans_commmission').on("keyup", function() {
    $(this).val(addCommas($(this).val().replace(/[^0-9]/g,"")));

  });

  $('#order_transfer_form').submit(function(){
      if($("#o_payment_method").val()==4) {
        $('#hidden_commission').val(stripComma($('#trans_commmission').val()));

        var hidden_commission=Number($('#hidden_commission').val());

        if(hidden_commission>0) {
          if(hidden_commission!=(Number($('#o_mix_cash').val())+Number($('#o_mix_credit').val()))) {
            alert('수수료와 현금+카드 입력합이 일치하지 않습니다.');
            return false;
          }
        }
      } else {
        $('#trans_commmission').val(stripComma($('#trans_commmission').val()));
      }
  });
});
