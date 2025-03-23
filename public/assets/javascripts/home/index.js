var memo_perpage=3;
var memo_page=2;

$(function () {
  // 자바스크립트가 지원될때 Tr 커서 선택
  $("#user_list tbody tr").css('cursor','pointer');

  $("#user_list tbody td").click(function(){
    location.href=$(this).parent().find('a').attr('href');
  });

  $('#show_app_id').click(function () {
		alert("앱 아이디:"+$(this).parent().find('input').val());
	});

  $('#photo_load').click(function () {
		$(this).parent().find('input:file').prop('capture', '');
		$(this).parent().find('input:file').trigger('click');
	});

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
          var form=$('<form action="/user-pictures/delete/'+json.id+'" method="post" accept-charset="utf-8" id="delete-photo-form">');
          form.append('<input value="'+$("#delete-photo-layer span").text()+'" class="btn btn-sm btn-outline-secondary btn-block" type="submit">');
          $("#delete-photo-layer").empty().append(form);

          form.submit(delete_user_photo);
        }
        
        showPhoto(urldecode(json.photo));
        },
        error: function(jqXHR, textStatus, errorThrown){
            //if fails
        }
    });
	});

  $('#user_enroll_list tbody tr td,#user_end_enroll_list tbody tr td').click(function() {
    var tr=$(this).parent();

    if(tr.hasClass('no-event')) {
      return false;
    }

    if(!$(this).hasClass('link')) {
      if(tr.hasClass('table-primary')) {
        return false;
      }
    }

      var enroll_id=tr.find('td.enroll_transaction_date input:first').val();
      var stopped=tr.find('td.enroll_transaction_date input:eq(1)').val();
      var order_id=tr.find('td.enroll_transaction_date input:eq(2)').val();
      var type=tr.find('td.enroll_category_name input').val();
      $('#user_enroll_edit').attr('href','/enrolls/edit/'+enroll_id);

      var is_delete=tr.find('td.enroll_transaction_date input:eq(3)').val();
      var end_text=tr.find('td.enroll_transaction_date input:eq(4)').val();
      
      if(type==1) {
        $('#user_enroll_extend').attr('href','/enrolls/extend/'+enroll_id).addClass('btn-modal').removeClass('disabled');
      } else {
        $('#user_enroll_extend').attr('href','#').addClass('disabled').removeClass('btn-modal');
      }
      
      if(stopped=='1') {
        $('#user_enroll_edit').attr('href','/enrolls/edit/'+enroll_id).addClass('btn-modal').addClass('disabled');        
        $('#user_enroll_transfer').attr('href','#').addClass('disabled').removeClass('btn-modal');
      } else {
        $('#user_enroll_edit').attr('href','/enrolls/edit/'+enroll_id).addClass('btn-modal').removeClass('disabled');
        $('#user_enroll_transfer').attr('href','/enrolls/transfer/'+enroll_id).addClass('btn-modal').removeClass('disabled');
      }

        $('#user_enroll_delete').text(end_text);
        if(is_delete) {
          if($('#user_enroll_delete').length) {
            $('#user_enroll_delete').attr('href','/enrolls/delete/'+enroll_id).show();
          } else {
            $('#user_enroll_delete').attr('href','').hide();
          }

          if($('#user_enroll_recover').length) {
            $('#user_enroll_recover').attr('href','/enrolls/recover/'+enroll_id).show();
          } else {
            $('#user_enroll_recover').attr('href','').hide();
          }
          $('#user_enroll_end').attr('href','').hide();
        } else {
          $('#user_enroll_end').attr('href','/enrolls/end/'+enroll_id).show();
          if($('#user_enroll_delete').length) {
            $('#user_enroll_delete').attr('href','').hide();
          }

          if($('#user_enroll_recover').length) {
            $('#user_enroll_recover').attr('href','').hide();
          }
        }
    

    tr.parent().find('tr').removeClass('table-primary');
    tr.addClass('table-primary');
    var enroll_category_name=tr.find('.enroll_category_name').text();
    var order_id=tr.find('input[name="order_id[]"]').val();
    $.getJSON('/accounts/get_order_list/'+order_id,{'format':'json'},function(data){
      if(data.result=='success') {
        $('#user_enroll_log_list tbody').empty();
        $("#enroll_log_title").text(enroll_category_name).effect('highlight');
        $("#export_enroll_account").attr('href','/accounts/export_enroll_account/'+order_id);
        if(data.total) {
          $.each(data.list,function(index,value) {
            var amount=Number(value.cash)+Number(value.credit);

            var fee='-';
            var dc='-';

            if(value.account_category_id==1) {
               fee=Number(value.original_price).toLocaleString()+'원';
               dc=Number(Number(value.original_price * value.dc_rate / 100)+Number(value.dc_price)).toLocaleString()+'원';
            }

            if (value['type'] == 'O' && value['cash'] != 0) {
              var price = '<span class="text-danger"> -' +Number(amount).toLocaleString()+'원 </span>';
              if(value.cash) {
                var cash = '<span class="text-danger"> -' +Number(value.cash).toLocaleString()+'원 </span>';
              } else {
                var cash = '0원';
              }

              if(value.credit) {
                var credit = '<span class="text-danger"> -' +Number(value.credit).toLocaleString()+'원 </span>';
              } else {
                var credit = '0원';
              }
            } else {
              var price = Number(amount).toLocaleString()+'원';
              var cash = Number(value.cash).toLocaleString()+'원';
              var credit =  Number(value.credit).toLocaleString()+'원';
            }
            
            $('<tr><td class="text-center">'+date_format(value.transaction_date)+'</td><td class="text-center">'+value.category_name.replace('수강','')+'</td><td class="text-right">'+fee+'</td><td class="text-right">'+dc+'</td><td class="text-right" style="background-color:powderblue;">'+price+'</td><td class="text-right">'+cash+'</td><td class="text-right">'+credit+'</td></tr>').appendTo('#user_enroll_log_list tbody').effect('highlight');
          });
        } else {
            $('#user_enroll_log_list tbody').append('<tr><td colspan="8">해당 데이터가 없습니다.</td></tr>');
        }

      } else {

      }
    });
  }).css('cursor','pointer');
  
  $('#user_rent_list tbody tr td').click(function() {
    var tr=$(this).parent();
    if(tr.hasClass('table-primary')) {
      return false;
    }

    if(!tr.find('td.rent_transaction_date input').length) {
      return false;
    }

    var rent_id=tr.find('td.rent_transaction_date input:eq(0)').val();
    var stopped=tr.find('td.rent_transaction_date input:eq(1)').val();
    var expired=tr.find('td.rent_transaction_date input:eq(3)').val();
    $('#user_rent_edit').attr('href','/rents/edit/'+rent_id+'?user-page=true');
    $('#user_rent_move').attr('href','/rents/move/'+rent_id);
    $('#user_rent_transfer').attr('href','/rents/transfer/'+rent_id);    
    $('#user_rent_extend').attr('href','/rents/extend/'+rent_id);
    $('#user_rent_delete').attr('href','/rents/delete/'+rent_id);

    if(stopped=='1') {
      $('#user_rent_stop_resume').text($("#text_resume_order").val()).attr('href','/rents/resume/'+rent_id).removeClass('disabled');
    } else {
      $('#user_rent_stop_resume').text($("#text_stop_order").val()).attr('href','/rents/stop/'+rent_id).removeClass('disabled');
    }

    if(expired=='1') {
      $("#user_rent_delete").text($("#text_delete_rent").val()).attr('href','/rents/end/'+rent_id+'?return=true');
    } else {
      $("#user_rent_delete").text($("#text_end_order").val()).attr('href','/rents/end/'+rent_id);
    }

    tr.parent().find('tr').removeClass('table-primary');
    tr.addClass('table-primary');
  }).css('cursor','pointer');


  $('#user_rent_sw_list tbody tr td').click(function() {
    var tr=$(this).parent();
    if(tr.hasClass('table-primary')) {
      return false;
    }

    if(!tr.find('td:first input').length) {
      return false;
    }

    var rent_sw_id=tr.find('td:first input:eq(0)').val();
    var stopped=tr.find('td:first input:eq(1)').val();
    $('#user_rent_sw_edit').attr('href','/rent-sws/edit/'+rent_sw_id+'?user-page=true');
    $('#user_rent_sw_move').attr('href','/rent-sws/move/'+rent_sw_id);
    $('#user_rent_sw_delete').attr('href','/rent-sws/delete/'+rent_sw_id);
    $('#user_rent_sw_transfer').attr('href','/rent-sws/transfer/'+rent_sw_id);    

    if(stopped=='1') {
      $('#user_rent_sw_stop_resume').text($("#text_rent_resume").val()).attr('href','/rent-sws/resume/'+rent_sw_id).removeClass('disabled');
    } else {
      $('#user_rent_sw_stop_resume').text($("#text_rent_stop").val()).attr('href','/rent-sws/stop/'+rent_sw_id).removeClass('disabled');
    }

    tr.parent().find('tr').removeClass('table-primary');
    tr.addClass('table-primary');
  }).css('cursor','pointer');
  
  $("#more-user-addtional").click(function(){
    if($('.additional_info').is(':visible')) {
      $('.additional_info').hide();
      $("#more-user-addtional").find('i').text('keyboard_arrow_down');
    } else {
      $('.additional_info').show().effect("highlight");
      $("#more-user-addtional").find('i').text('keyboard_arrow_up');
    }
    
    return false;
  });

  $("#more-user-memo").click(function(){
    var c_body=$(this).closest('.card').find('.card-body');
    $.getJSON('/user-contents',{'per_page':memo_perpage,'page':memo_page,'parent_id':$("#home_user_id").val(),'format':'json'},function(data){
      if(data.result=='success') {
        memo_page++;

        if(data.total) {
          var c_body_content=c_body.find('.col-12');
          $.each(data.list,function(index,value){
            var div_node=$('<div style="margin-bottom:20px"></div>');
            var a_node=$('<a href="/user-contents/view/'+value.id+'" class="btn-modal more">'+nl2br(value.content)+'</a>');
            c_body_content.append(div_node.append(a_node));

            var ta=value.updated_at.split(' ')[0].split('-');
            console.log(ta);
            var update_date=Number(ta[0])+'년 '+Number(ta[1])+'월 '+Number(ta[2])+'일';

            div_node.append($('<span>('+update_date+')</span>'));
            div_node.effect("highlight");
            a_node.click(function(){
              event.preventDefault();
              $('#myModal').removeData("modal");
              if($(this).attr('href').indexOf('?')=='-1') {
                var url=$(this).attr('href')+'?popup=true';
              } else {
                var url=$(this).attr('href')+'&popup=true';
              }
              $('#myModal').load(url,function(){
                $('#myModal').modal();
                });
              return false
            });
          });
          c_body.scrollTop(c_body.height());  
        }

      } else {

      }
    });

    return false;
  });

  $('#user_end_enroll_list tbody tr td').click(function() {
    var tr=$(this).parent();
    if(tr.hasClass('table-primary')) {
      return false;
    }

    if(!tr.find('td:first input').length) {
      return false;
    }

    var re_id=tr.find('td:first input:eq(0)').val();

    $("#user_re_enroll").attr('href',$("#user_re_enroll").attr('href').split('after=')[0]+'after='+re_id);
    $("#user_enroll_edit_expire_log").attr('href',$("#user_enroll_edit_expire_log").attr('href').split('/edit')[0]+'/edit/'+re_id);
    $("#user_enroll_delete_expire_log").attr('href',$("#user_enroll_delete_expire_log").attr('href').split('/disable')[0]+'/disable/'+re_id);

    tr.parent().find('tr').removeClass('table-primary');
    tr.addClass('table-primary');
  }).css('cursor','pointer');

  $('#user_end_rent_list tbody tr td').click(function() {
    var tr=$(this).parent();
    if(tr.hasClass('table-primary')) {
      return false;
    }

    if(!tr.find('td:first input').length) {
      return false;
    }
    
    var re_id=tr.find('td:first input:eq(0)').val();

    $("#user_re_rent").attr('href',$("#user_re_rent").attr('href').split('after=')[0]+'after='+re_id);
    $("#user_rent_edit_expire_log").attr('href',$("#user_rent_edit_expire_log").attr('href').split('/edit')[0]+'/edit/'+re_id);
    $("#user_rent_delete_expire_log").attr('href',$("#user_rent_delete_expire_log").attr('href').split('/disable')[0]+'/disable/'+re_id);

    tr.parent().find('tr').removeClass('table-primary');
    tr.addClass('table-primary');
  }).css('cursor','pointer');

  $("#user_order_stop_resume,#user_order_stop_edit").css('visibility','visible');

  $('#delete-photo-form').submit(delete_user_photo);

  function delete_user_photo() {
    if(!confirm('정말로 삭제합니까?')) {
      return false;
    }
  }
});
