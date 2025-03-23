$(function () {
  var t_option={
      start_time: ["05", "00", "AM"],
      step_size_minutes:10,
      min_hour_value:05,
      max_hour_value:23,
      show_meridian:false,
  }

  function button_click(){
    if(!$(this).find('i').length) {
      return false;
    }

    if($(this).find('i').text()=='keyboard_arrow_down') {
      var index=0;

      $(this).find('i').text('keyboard_arrow_up');
      $(this).closest('.card').find('.card-body').slideDown();

      var card=$(this).closest('.card');
      if(card.find('.card-block:visible').length) {
        var v_card=card.find('.card-block:visible');
        index=card.find('.card-block').index(v_card);
      }

    } else {
      $(this).find('i').text('keyboard_arrow_down');
      $(this).closest('.card').find('.card-body').slideUp();
    }
  }

  $('.permission_detail .card-header .buttons').click(button_click);

  // 자바스크립트가 지원될때 Tr 커서 선택
  $("#employee_list tr").css('cursor','pointer');

  $("#employee_list td").click(function(){
    location.href=$(this).parent().find('a').attr('href');
  });

  $('#photo_load').click(function () {
  	$(this).parent().find('input:file').prop('capture', '');
  	$(this).parent().find('input:file').trigger('click');
  });

  $('.timepicker').timepicki(t_option);

  $('.birthday-datepicker').datepicker({
		startView: 'years',
		defaultViewDate : {'year':1990},
    startDate:'-100y',
    endDate:'-10y',
    maxViewMode : 'decades',
		format: 'yyyy-mm-dd',
		autoclose :true,
		language : 'ko'
	});

  $('#attendance_calendar').datepicker({
    templates: {leftArrow: '<i class="fa fa-arrow-left"></i>',rightArrow: '<i class="fa fa-arrow-right"></i>'},
      language: "ko",
      todayHighlight: true,
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
  	}}).on('changeDate', function(e) {
      $('#employee_attendance_form input[name="date"]').val(e.format());
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
