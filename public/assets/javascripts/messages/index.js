$(function () {
  var user_select_link=$("#user_select").attr('href');

  $('input[name="type"]').change(function(){
    switch($(this).val()) {
      case 'wapos' :
        $(this).closest('form').find('button i').text('mail');
        $("#show_byte_layer").show();
        $("#sms_available_quantity_layer").show();
        $("#temp_user_select").show();      
        if($("#m_sender_layer").length) {
          $("#m_sender_layer").show();
        }
        $("#user_select").text('회원선택').attr('href',user_select_link+'?message_type=wapos');         
        break;
      case 'push' :
        $(this).closest('form').find('button i').text('stay_current_portrait');
        $("#show_byte_layer").hide();
        $("#sms_available_quantity_layer").hide();
        $("#temp_user_select").hide();      
        if($("#m_sender_layer").length) {
          $("#m_sender_layer").hide();
        }
        $("#user_select").text('회원선택(푸시 가능회원만)').attr('href',user_select_link+'?message_type=push');
        break;
      default :
        $(this).closest('form').find('button i').text('mail');
        $("#show_byte_layer").show();
        $("#sms_available_quantity_layer").show();
        $("#temp_user_select").show();      
        if($("#m_sender_layer").length) {
          $("#m_sender_layer").show();
        }
        $("#user_select").text('회원선택').attr('href',user_select_link);          
    }
  });

  $('.users_input .text-danger').click(deleteSelectedUser);

  $('#m_picture').change(function(){
    $("#sms_type").text('MMS').addClass('text-warning').removeClass('text-success').effect('highlight');
    $("#m_type").val('mms');    
  });

  if($("#m_type").val()!='push') {  
  $('#m_content').keyup(function(){

    var totalByte = 0;
    var message = $(this).val();

    for(var i =0; i < message.length; i++) {
            var currentByte = message.charCodeAt(i);
            if(currentByte > 128) totalByte += 2;
            else totalByte++;
    }

    $("#show_byte").text(totalByte);
    var effect=false;
    if(totalByte<80) {
      if($("#sms_type").text()=='LMS') {
        var effect=true;
      }

      if($("#sms_type").text()!='MMS') {
        $("#sms_type").text('SMS').removeClass('text-warning').addClass('text-success');
        $("#m_type").val('sms');
      }
    } else {
      if($("#sms_type").text()=='SMS') {
        var effect=true;
      }

      if($("#sms_type").text()!='MMS') {
        $("#sms_type").text('LMS').addClass('text-warning').removeClass('text-success');
        $("#m_type").val('lms');
      }
    }

    if(effect) {
      $("#sms_type").effect('highlight');
    }

    if(totalByte > limitByte) {
      alert( limitByte+"바이트까지 전송가능합니다.");
    }
  });
  }

  if($("#sms_message_left").length) {
    $.getJSON("/messages/get-check-remain?format=json",function(data){
     // if(data.result_code==1) {
          $("#sms_message_left").find('dd:first span').text(data.SMS_CNT).effect('highlight');
          $("#sms_message_left").find('dd:eq(1) span').text(data.LMS_CNT).effect('highlight');
          $("#sms_message_left").find('dd:eq(2) span').text(data.MMS_CNT).effect('highlight');                    
     // } else {
     //   alert(data.message);
     // }
    });
  }
});
