$(function () {
  $('input.calc_outstanding').change(function(){

    var e_cash=0;
    var e_credit=0;

    if($("#o_cash").val()) {
      e_cash=$("#o_cash").val();
    }
    if($("#o_credit").val()) {
      e_credit=$("#o_credit").val();
    }

    var payment=Number(e_cash)+Number(e_credit);    

    switch($('input[name="refund_type"]:checked').val()) {
      case 'etc' :
      var total=$("#o_total").val();
      var result=total-payment;
      if(result<0) {
        $("#o_cash,#o_credit").val('0');
      } else {
        $("#o_cash,#o_credit").effect('highlight');
      }

      $("#o_d_refund").text(payment.toLocaleString()+'원').effect('highlight');
      $("#o_refund").val(payment);      
        break;
      default :
        var total=$("#o_refund").val();
        var result=total-payment;

        if(result<0) {
          if(e_cash>e_credit) {
          } else {
          }
        }
    }
    

  });

  $("#insert_refund").change(function(){
    if($(this).is(':checked')) {
      $('#refund_layer').slideDown();
    } else {
      $('#refund_layer').slideUp();
    }
  });

  $("#r_apt_cancel1").change(function(){ 
    if($(this).is(':checked')) {
      $('#refund_layer').slideUp();
    }
  });

  $('.enroll_datepicker').datepicker({language: "ko",todayHighlight: true});  
  $("#is_today").change(function(){
    if($(this).prop('checked')) {
      $("#o_transaction_date_layer").hide();
      $("#today_display").show();
    } else {
      $("#o_transaction_date_layer").show();
      $("#today_display").hide();
    }
  });


  function refund_method_change() {
      switch($('input[name="refund_type"]:checked').val()) {            
        case 'all':
          var total=$("#o_total").val();
          $("#calculator_info").hide();
          break;
        case 'calculate':
          var total=$("#o_calculate").val();
          $("#calculator_info").show();          
          break;
        case 'etc' :
          $('.payment_label').show();
          var total=0;          
          $('.calc_outstanding').val(0);
          $("#calculator_info").hide();              
          break;           
    }
    $('#o_refund').val(total);
    $('#o_d_refund').text(Number(total).toLocaleString()+'원');
    payment_change();
  }

  $('input[name="refund_type"]').change(refund_method_change);

  function payment_change(){
    var etc=false;
    switch($('input[name="refund_type"]:checked').val()) {            
      case 'all':   
        var total=$("#o_total").val();
        break;
      case 'calculate': 
        var total=$("#o_calculate").val();
        break;
      default :
        var total=0;
        var etc=true;         
        $('.calc_outstanding').val(0);        
    }

    $('.payment_label').hide();
    $('.calc_outstanding').attr('type','hidden');
    switch($("#select_payment").val()) {
      case '1' :
        $('.calc_outstanding').val(0);
        $("#o_cash").val(total);

        if(etc) {
          $("#o_cash").attr('type','number').closest('.form-group').find('.payment_label').show();
        }
        break;
      case '2' :
        $('.calc_outstanding').val(0);
        $("#o_credit").val(total);
        if(etc) {
          $("#o_credit").attr('type','number').closest('.form-group').find('.payment_label').show();
        }        
        break;
      case '4' :
        $('.payment_label').show();
        $('.calc_outstanding').each(function(){ 
          $(this).attr('type','number');
          if($(this).val()=='0') {
            $(this).val('');
          }
        });
        break;
      default :        
    }
  }
  
  $("#select_payment").change(payment_change);

  $('#order_delete_form').submit(function(event) {
    if(!$("#insert_refund").is(':checked')) {
      return true;
    }

    if(!$('#o_cash').val()) {
      $('#o_cash').val(0);
    }

    if(!$('#o_credit').val()) {
      $('#o_credit').val(0);
    }
    
    var refund=$('#o_cash').val()+$('#o_credit').val();

    if(refund==0) {
      $("#insert_refund").prop('checked',false);
      return true;
    }

    if(!confirm("환불금 처리가 맞게 되었습니까?\n주문이 삭제되므로 앞으로는 더 이상 수정할 수 없습니다.")) {
      return false;
    }
  });
});
