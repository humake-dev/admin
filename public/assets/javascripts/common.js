;(function($){
	$.fn.datepicker.dates['ko'] = {
		days: ["일요일", "월요일", "화요일", "수요일", "목요일", "금요일", "토요일"],
		daysShort: ["일", "월", "화", "수", "목", "금", "토"],
		daysMin: ["일", "월", "화", "수", "목", "금", "토"],
		months: ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"],
		monthsShort: ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"],
		today: "오늘",
		clear: "삭제",
		format: "yyyy-mm-dd",
		maxViewMode : 'decades',
		titleFormat: "yyyy년 mm월",
		weekStart: 0
	};
}(jQuery));


$(document).ready(function() {
  $("a.simple_image").fancybox({
			'opacity'   : true,
			'overlayShow'        : true,
			'overlayColor': '#000000',
			'overlayOpacity'     : 0.9,
			'titleShow':true,
			'openEffect'  : 'elastic',
			'closeEffect' : 'elastic'
		});

		$('.input-group-prepend .input-group-text').click(function(){
			$(this).parent().find('input').trigger('focus');
    }).css('cursor','pointer');

    $('.datepicker').datepicker({language: "ko",todayHighlight: true, maxViewMode : 'decades', startDate:'-100y', endDate:'+0d',
		beforeShowDay: function(date){
				if (typeof active_dates === 'undefined') {
			  	return true;
				}

	      var formattedDate = convertDate(date);
	      if ($.inArray(formattedDate, active_dates) != -1){
	        return {
	           classes: 'active-check'
	        };
	      }
	   return;
	}
	});
    $('.input-daterange input').each(function() {
        $(this).datepicker({language: "ko",todayHighlight: true, maxViewMode : 'decades'});
    });

    $('input[name="show_only_my_user"]').change(function(){
      if($(this).is(":checked")) {
          var cval=1;
      } else {
          var cval=0;
      }

        $.post('/home/show-omu',{'show_omu':cval,'format':'json'},function(data){
            if(data.result=='success') {
                location.reload();
            } else {

            }
        },'json');
  });

  $("#is_today").change(function(){
    if($(this).prop('checked')) {
      $("#o_transaction_date_layer").hide();
      $("#today_display").show();
    } else {
      $("#o_transaction_date_layer").show();      
      $("#today_display").hide();
    }
  });

  $("#o_dc_rate,#o_dc_price,#o_cash,#o_credit").focus(function(){
    if($(this).val()==0) {
      $(this).val('');
    }
  });

  $("#o_dc_rate,#o_dc_price,#o_cash,#o_credit").blur(function(){
    if($.trim($(this).val())=='') {
      $(this).val(0);
    }
  });

	$('.sub_nav a.disabled').click(function(event){
	  	event.preventDefault();
			alert('준비중입니다.');
	});

  $('.btn-modal').click(btn_modal_click);

  // 메세지 닫기
  $('#message .m_close').click(function(){
    $(this).parent().remove();
  });

	$("#messages .alert-success").fadeOut(5000,function(){
		var as=$(this);
		$("#messages").slideUp('slow',function(){
			as.remove();
			$("#messages").slideDown();
		});
	});

  $('.btn-popup').click(btn_popup_click);

  $('.popup_close').click(function(){
    window.close();
  });

  $('.btn-delete-confirm').click(btn_delete_confirm_click);

  // 몇개씩 보기 바뀌었을때, 리스트 갱신
  $("#perpage").change(function(){
    $(".sl_pagination").empty();
    getList();
    $(this).blur();
    return true;
  });

  // 기간 선택시 날짜 채워지기
  $('input[name="date_p"]').change(function(){
		if($("#future_search").length) {
			if($("#future_search").val()==1) {
				if($(this).val()=='all') {
   				$('input[name="start_date"]').val('').effect("highlight");
   				$('input[name="end_date"]').val('').effect("highlight");
				} else {
					setDateFutureInput($(this).val());
				}
				return true;
			}
		}

		if($(this).val()=='all') {
   		$('input[name="start_date"]').val('').effect("highlight");
   		$('input[name="end_date"]').val('').effect("highlight");
		} else {
			setDateInput($(this).val());
		}
	});

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

	$(".select-user").click(function(event){
		event.preventDefault();
		$('#myModal').removeData("modal");
		$('#myModal').load('/users/select/single?popup=no',function(){
			$('#myModal').modal();
		});
	}).css('cursor','pointer');

	$(".select-employee").click(function(event){
		event.preventDefault();
    $('#myModal').removeData("modal");

    var load_url='/employees/select/single?popup=no';    
    if($(this).parent().find('input.default_position').length) {
      load_url+='&default_position='+$(this).parent().find('input.default_position').val();
    }

		$('#myModal').load(load_url,function(){
			$('#myModal').modal();
		});
  });
  
	$(".select-fc").click(function(event){
    event.preventDefault();    
    $('#myModal').removeData("modal");    
    var popup_url='/employees/select/single?popup=no&position=fc';

    if($(this).hasClass('no-search')) {
      popup_url+='&no-search=1';
    }

    if($(this).hasClass('search-branch')) {
      if($('#search_branch').val()) {
        popup_url+='&branch_id='+$('#search_branch').val();
      }
    }

		$('#myModal').load(popup_url,function(){
			$('#myModal').modal();
		});
  });
  
	$(".select-trainer").click(function(event){
		event.preventDefault();
    $('#myModal').removeData("modal");
    var popup_url='/employees/select/single?popup=no&position=trainer';

    if($(this).hasClass('no-search')) {
      popup_url+='&no-search=1';
    }

		$('#myModal').load(popup_url,function(){
			$('#myModal').modal();
		});
	});  
}); // document.ready end


// 정수형으로 받기
function getIntVal (field) {
    return ($(field).val() == '') ? 0 : parseInt(strip_number_comma($(field).val()));
}

// 콤마가 포함된 숫자에서 콤마 제거하기
function strip_number_comma (v) {
    if (v == null) return v;
    return (v.match(/[^0-9.,]/g) == null) ? v.replace(/,/g, '') : v;
}

function urldecode(url) {
  return decodeURIComponent(url.replace(/\+/g, ' '));
}

function showPhoto(src) {
  $('#profile_photo').attr('src',src);
}

function exercise_change(){
   if($(this).val()=='') {
     $("#exercise").empty().append('<option selected="selected">카테고리를 선택하세요!</option>').effect("highlight", {}, 1000);
     return false;
   }

   $.getJSON('/exercises?json=true',{'category_id':$(this).val(),'per_page':100},function(data){
     if(data.result=='success') {
       $("#exercise").empty();
       $.each(data.list,function(key,value){
         $("#exercise").append('<option value="'+value.id+'">'+value.title+'</option>');
       });
       $("#exercise").effect("highlight", {}, 1000);
     } else {
       alert(data.message);
     }
   });
 }

function btn_popup_click(e){
  e.preventDefault();

  if($(this).attr('href').indexOf('?')=='-1') {
    var url=$(this).attr('href')+'?popup=true';
  } else {
    var url=$(this).attr('href')+'&popup=true';
  }

  var win = window.open(url,$(this).attr('title'), "top=0,left=0,width=900px, height=650px");
  win.focus();
}

function btn_modal_click(event){
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
}

function btn_delete_confirm_click(event){
  event.preventDefault();  
  var tr=$(this).closest('tr');
  if(confirm('정말로 삭제합니까?')) {
    var url=$(this).attr('href').replace('/delete-confirm/','/delete/');
    $.post(url,{'format':'json'},function(data){
      if(data.result=='success') {
        display_message(data.message,'success');
        if($('#list_count').length) {
          $('#list_count').text(Number($('#list_count').text()-1));

          if($('#list_count').parent().find('.mark').length) {
            $('#list_count').parent().find('.mark').text($('#list_count').text());
          }
        }
        
        tr.effect('highlight',function() {
          $(this).remove();
        });
      } else {
        alert(data.message);
      }
    },'json');
  }
}

function nl2br (str, is_xhtml) {
  var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br ' + '/>' : '<br>'; // Adjust comment to avoid issue on phpjs.org display

  return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}

function isEmpty(value){
   if( value == ""  || value == null || value == undefined || ( value != null && typeof value == "object" && !Object.keys(value).length ) ){
     return true
   }else{
     return false
   }
}

function setDateInput(obj) {
 if (obj != undefined) {
  var datediff = -(parseInt(obj));
  var newDate = new Date();
  var now = new Date();
  newDate.setDate(now.getDate()+datediff);
  var newYear = newDate.getFullYear();
  var newMonth = newDate.getMonth()+1;
  if (newMonth.toString().length == 1) newMonth = "0" + newMonth;

  endMonth=now.getMonth() +1;
  if (endMonth.toString().length == 1) endMonth = "0" + endMonth;

  var newDay = newDate.getDate();
  if (newDay.toString().length == 1) { newDay = "0" + newDay};

  var txtSDate = newYear + "-" + newMonth +'-'+ newDay;
  endDay=now.getDate();

  if (endDay.toString().length == 1) {endDay = "0" + endDay; };
  var txtEDate = now.getFullYear() + '-' + endMonth + "-" + endDay;

  $('input[name="start_date"]').val(txtSDate).effect("highlight");
  $('input[name="end_date"]').val(txtEDate).effect("highlight");
 } else {alert("잠시 후 이용해 주시기 바랍니다."); return false;}
}

function setDateFutureInput(obj) {
 if (obj != undefined) {
  var datediff = -(parseInt(obj));
  var newDate = new Date();
  var now = new Date();
  newDate.setDate(now.getDate()-datediff);
  var newYear = newDate.getFullYear();
  var newMonth = newDate.getMonth()+1;
  if (newMonth.toString().length == 1) newMonth = "0" + newMonth;

  endMonth=now.getMonth() +1;
  if (endMonth.toString().length == 1) endMonth = "0" + endMonth;

  var newDay = newDate.getDate();
  if (newDay.toString().length == 1) newDay = "0" + newDay;
  var txtEDate  = newYear + "-" + newMonth +'-'+ newDay;
  endDay=now.getDate();
  if (endDay.toString().length == 1) endDay = "0" + endDay;
  var txtSDate = now.getFullYear() + '-' + endMonth + "-" + endDay;
  $('input[name="start_date"]').val(txtSDate).effect("highlight");
  $('input[name="end_date"]').val(txtEDate).effect("highlight");
 } else {alert("잠시 후 이용해 주시기 바랍니다."); return false;}
}

//layer popup
function commonLayerOpen(thisClass){
    $('.'+thisClass).fadeIn();
}
function commonLayerClose(thisClass){
    $('.'+thisClass).fadeOut();
}

function list_count_minus() {
  var s_count=Number($('#list_count').text())-1;

  if(s_count<0)
    s_count=0;

  $('#list_count').text(s_count).parent().effect("highlight", {}, 1000);

  return s_count;
}

// 리스트 선택후 다시 돌아왔을때 선택되어있게 하기
function check_checked(table_id,not) {
  if(not) {
    var users_input_class='not_users_input';
  } else {
    var users_input_class='users_input';
  }

  $('#'+table_id+' tbody input:checkbox').each(function(){
    var exists=false;
    var i_val=$(this).val();

    var users_input=$('.'+users_input_class);
    users_input.find('input').each(function(){
      if(i_val==$(this).val()) {
        exists=true;
      }
    });

    if(exists) {
      $(this).prop('checked',true);
      $(this).closest('tr').addClass('table-primary');
    }
  });
}

function delete_form_submit(data, statusText, xhr, $form) {
    if(data.result=='success') {
      var tr=$form.parent().parent();
      tr.effect("highlight", {}, 500,function(){
        getList();
        if(opener) {
          remove_prepare_select(data.deleted_id);
        }
      });
    } else {
      alert(data.message);
    }
}

function delete_m_form_submit(data, statusText, xhr, $form) {
  if(data.result=='success') {
    if(data.delete_parent) {
      var tr=$form.parent().parent();
      $form.remove();
      tr.effect("highlight", {}, 200,function(){
        var count=list_count_minus();
        if(count<$('#perpage').val()) {
        if(count<1) {
          var th_length=$(this).parent().parent().find('th').length;
          var new_tr=$('<tr><td colspan="'+(th_length+1)+'" style="text-align:center">해당 데이터가 없습니다.</td></tr>');
          new_tr.appendTo($(this).parent()).effect("highlight", {}, 800);
        }
      } else {
        getList();
        if(opener) {
          remove_prepare_select(data.deleted_parent_id);
        }
      }
        $(this).remove();
      });
    } else {
      $form.parent().parent().effect("highlight", {}, 1000);
      $form.fadeOut(function(){
        $(this).remove();
      });
    }
  } else {
    display_message(data.message);
  }
}

function date_format(f_date) {
	var date_a=f_date.split(' ')[0].split('-');

	return date_a[0]+'년 '+Number(date_a[1])+'월 '+Number(date_a[2])+'일';
}

function delete_f_form_submit(data, statusText, xhr, $form) {
  if(data.result=='success') {
    if(data.delete_parent) {
      var tr=$form.parent().parent();
      $form.remove();
      tr.effect("highlight", {}, 200,function(){
        var count=list_count_minus();
        if(count<$('#perpage').val()) {
        if(count<1) {
          var th_length=$(this).parent().parent().find('th').length;
          var new_tr=$('<tr><td colspan="'+(th_length+1)+'" style="text-align:center">해당 데이터가 없습니다.</td></tr>');
          new_tr.appendTo($(this).parent()).effect("highlight", {}, 800);
        }
      } else {
        getList();
        if(opener) {
          remove_prepare_select(data.deleted_parent_id);
        }
      }
        $(this).remove();
      });
    } else {
      $form.parent().parent().effect("highlight", {}, 1000);
      $form.fadeOut(function(){
        var calorie=$(this).find('input[name="calorie"]').val();
        var r_cal=parseFloat($(this).parent().parent().find('td.t_calorie').text())-parseFloat(calorie);
        $(this).parent().parent().find('td.t_calorie').text(r_cal.toFixed(2));
        $(this).remove();
      });
    }
  } else {
    display_message(data.message);
  }
}

function add_m_form_submit(data, statusText, xhr, $form) {
  if(data.result=='success') {
    var tr=$form.parent().parent();
    tr.effect("highlight", {}, 200,function(){
      /*if($('#list_count').text()<$('#perpage').val()) {
      } else {
      } */
      $("#leftMemberList tbody tr").removeClass('selected').find('input').prop('checked',false);
      $('.members_input li input[name="members[]"]').parent().remove();
      getList();
      });
  } else {
    display_message(data.message);
  }
}

function delete_form_submit_before() {
  if(!confirm('정말로 삭제합니까?')) {
    return false;
  }

  $('#message').empty();
  $(this).find("input[type='submit']").prop('disabled', true);
}

function form_submit_before() {
  $('#message').empty();
  $(this).find("input[type='submit']").prop('disabled', true);
}

function display_message(message,alert_type) {
  var alert_type = alert_type || 'danger';

	if($("#messages").length) {
		$("#messages").empty();
		$("#messages").html('<div class="alert alert-'+alert_type+'"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span><span class="sr-only">닫기</span></button>'+message+'</div>');
    $("#messages .m_close").click(function(){
      $(this).parent().remove();
    });
	}
}

function pageselectCallback(page_index, jq) {
  if ($(jq).data("load") == true)
    getList(page_index, jq);
  else
    $(jq).data("load", true);

    return false;
}

function initPagination(num_entries, items_per_page, current_page) {
  if(!current_page) {
  	var current_page=0;
	}

  if(num_entries<=items_per_page) {
    return false;
  }

  $(".sl_pagination").pagination(num_entries, {
    current_page : current_page,
    num_edge_entries : 2,
    num_display_entries : 8,
    callback : pageselectCallback,
    items_per_page : items_per_page
  });
  return false;
}

function viewKorean(num) {
	var hanA = new Array("","일","이","삼","사","오","육","칠","팔","구","십");
	var danA = new Array("","십","백","천","","십","백","천","","십","백","천","","십","백","천");
	var result = ""; for(i=0; i<num.length; i++) { str = ""; han = hanA[num.charAt(num.length-(i+1))]; if(han != "") str += han+danA[i]; if(i == 4) str += "만"; if(i == 8) str += "억"; if(i == 12) str += "조"; result = str + result; } if(num != 0) result = result + "원";
	return result;
}

function add_day(strDate, numberOfDays) {
  var startDate=new Date(strDate);
  xDate=new Date(startDate.getTime() + ((numberOfDays-1) * 24 *60 * 60 * 1000));
	return $.datepicker.formatDate('yy-mm-dd', xDate);
}

// 날짜 계산하기 - 규칙이 독특해서 따로 만듦. - 더하기에서만 정상 동작함.
// PHP도 똑같은 이름으로 동일한 결과를 얻도록 함수를 만든다.
function add_month (strDate, interval) {
    if (typeof(interval) == 'string') interval = parseInt(interval);
    if (interval <= 0) return strDate; // 빼기 및 0인 경우에는 처리하지 않는다.

    var sDate = new Date(strDate);

    return moment(sDate).add(interval, 'months').add(-1, 'days').format('YYYY-MM-DD');
}

function dowtostr(dow)
{
		if(dow.length==0 || dow.length==7) {
			return '전요일';
		}

		return dow.replace("0", "일").replace("1", "월").replace("2", "화").replace("3", "수").replace("4", "목").replace("5", "금").replace("6", "토").split('').join(',');
		//return , dow.// 유니코드는 3바이트
}

function convertDate(date) {
  var yyyy = date.getFullYear().toString();
  var mm = (date.getMonth()+1).toString();
  var dd  = date.getDate().toString();

  var mmChars = mm.split('');
  var ddChars = dd.split('');

  return yyyy + '-' + (mmChars[1]?mm:"0"+mmChars[0]) + '-' + (ddChars[1]?dd:"0"+ddChars[0]);
}

function user_select_cancel() {
    $("aside").removeAttr('class').attr('class','col-12 col-lg-6 col-xl-5');
    $("#right_data_form").removeAttr('class').attr('class','col-12 col-lg-6 col-xl-7');
    $("#user_info").hide();
    $("#user_search").show();
    $("#o_user_id").val('');

    $("#user_find_form h3 span:first").show();
    $("#user_find_form h3 span:eq(1)").hide();
    $('#rent_add_form input[type="submit"]').attr('disabled','disabled');


    $(".use_list").hide();
}

function calculatorPayment() {
    switch($("#o_payment_method").val()) {
      case '1':
        $("#o_cash").val($("#o_payment").val());
        break;
      default :
        $("#o_credit").val($("#o_payment").val());
    }
}

function display_gender(gender)
{
	if(gender==null) {
		return '미입력';
	}

	if (gender==1) {
			return '남자';
	} else {
			return '여자';
	}
}

//3자리 단위마다 콤마 생성
function addCommas(x) {
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function deleteSelectedUser() {
  $(this).closest('.select_user').remove();
}

function stripComma(str) {
    var re = /,/g;
    return str.replace(re, "");
}

function add_hyphen(v) {
		v = v.replace(/[^0-9]/g, '');
		return v.replace(/^(0(?:2|[0-9]{2}))([0-9]+)([0-9]{4}$)/, "$1-$2-$3");
}

      // 1 ~ 9 한글 표시
      var arrNumberWord = new Array("","일","이","삼","사","오","육","칠","팔","구");
      // 10, 100, 100 자리수 한글 표시
      var arrDigitWord = new  Array("","십","백","천");
      // 만단위 한글 표시
      var arrManWord = new  Array("","만","억", "조");

      // Copyright 취생몽사(http://bemeal2.tistory.com)
      // 소스는 자유롭게 사용가능합니다. Copyright 는 삭제하지 마세요.

      function fn_change_hangul_money(n_value,n_length)
      {
            var num_value = n_value;
            var num_length = num_value.length;

 

            if(isNaN(num_value) == true)
                  return;

 

            var han_value = "";
            var man_count = 0;      // 만단위 0이 아닌 금액 카운트.

 

            for(i=0; i < num_value.length; i++)
            {
                  // 1단위의 문자로 표시.. (0은 제외)
                  var strTextWord = arrNumberWord[num_value.charAt(i)];

 

                  // 0이 아닌경우만, 십/백/천 표시
                  if(strTextWord != "")
                  {
                        man_count++;
                        strTextWord += arrDigitWord[(num_length - (i+1)) % 4];
                  }

 

                  // 만단위마다 표시 (0인경우에도 만단위는 표시한다)
                  if(man_count != 0 && (num_length - (i+1)) % 4 == 0)
                  {
                        man_count = 0;
                        strTextWord = strTextWord + arrManWord[(num_length - (i+1)) / 4];
                  }

                  han_value += strTextWord;
            }

  if(num_value != 0)
    han_value = "금 " + han_value + " 원";

  return han_value;
}

