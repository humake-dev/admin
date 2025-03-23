$(function () {
    var class_time=1;
    var t_option={
      start_time: ["05", "00", "AM"],
      step_size_minutes:10,
      min_hour_value:05,
      max_hour_value:23,
      show_meridian:false,
    }
    
    var t_option2={
        start_time: ["06", "00", "AM"],
        step_size_minutes:10,
        min_hour_value:01,
        max_hour_value:23,
        show_meridian:false,
    }  

  // 자바스크립트가 지원될때 Tr 커서 선택
  $("#courses aside .card-header").css('cursor','pointer').click(function(){
    location.href=$(this).find('a').attr('href');
  });

  $("#courses aside .card-header a").click(function(e){
    e.preventDefault();
  });

  $("#add_class").click(function(e){
    e.preventDefault();
    var col=$("#clone_class");
    var col_clone=col.clone();

    col_clone.find('button').remove();

    var c_group_id=$(this).closest('.card-block').find('input[name="c_group_id"]').val();

    var new_button=$('<input type="button" value="삭제" class="btn btn-warning delete-class" style="margin-left:20px" />');
    //t_option.start_time=col_clone.find('input:first').val();
    var new_starttime=$('<input name="class['+c_group_id+']['+class_time+'][start_time]" class="form-control timepicker" />');
    var first_input=col_clone.find('input.timepicker:first');
    first_input.before(new_starttime);
    first_input.remove();

    var new_endtime=$('<input name="class['+c_group_id+']['+class_time+'][end_time]" class="form-control timepicker" />');
    var second_input=col_clone.find('input.timepicker:eq(1)');
    second_input.before(new_endtime);
    second_input.remove();

    col_clone.find('input:eq(2)').attr('name','class['+c_group_id+']['+class_time+'][quota]');
    new_button.click(function(e){
      e.preventDefault();
      $(this).closest('.col-12').remove();
    });
    col_clone.append(new_button);
    col.parent().append(col_clone);
    col_clone.find('.timepicker').timepicki(t_option);
    class_time++;
  });

  $(".edit_delete_button").click(function(e){
    e.preventDefault();
    var col=$(this).closest('.col-12');
    $(this).closest('form').append('<input type="hidden" name="delete_class[]" value="'+col.find('input:first').val()+'" />');
    col.remove();
  });

  $('.edit-time').each(function(){
    $(this).val($(this).val().substring(0,$(this).val().length-3));
  });

  $('.timepicker').timepicki(t_option);
  $('.timepicker_limit').timepicki(t_option2);

  function disableTrainerInput() {
    $('#trainer_select').val('').attr('disabled','disabled');
    $("#trainer_info").val('').attr('disabled','disabled');
    $("#trainer_select_row").hide();
  }

  function enableTrainerInput() {
    $('#trainer_select').removeAttr('disabled');
    $("#trainer_info").removeAttr('disabled');
    $("#trainer_select_row").show();
  }

  function trainer_search (trainer_idx) {
    $.getJSON("trainer/show.php",{'trainer_idx': trainer_idx},function(data){
      if(data.result=='success') {
        $("#trainer_info").val(data.gender+' / '+data.age);
      } else {

      }
    });
  }

  // 폼제출전 검사
  function check_before_submit () {
      if ($("#course_name").val() == '') {
          alert("강습명을 입력하세요.");
          return false;
      }

      if ($("#course_idx").val() != '') {
          var lesson_type = $('input:radio[name=lesson_type]:checked').val();
          if (lesson_type == '') {
              alert("수강방식을 선택하세요.");
              return false;
          }
          if (lesson_type == 1) { // 기간제
              $("#lesson_period").val($('.lesson_period').eq(1).val());
              if ($("#lesson_period").val() == '' || $("#lesson_period").val() == 0) {
                  alert("기간을 입력하세요.");
                  return false;
              }
              $("#lesson_quantity").val(0);
          }
          else { // 횟수,쿠폰제
              if ($("#lesson_quantity").val() == '') {
                  alert("횟수/갯수를 입력하세요.");
                  return false;
              }

              var lesson_time_type = $('input:radio[name=lesson_time_type]:checked').val(); //console.log(x);
              if (lesson_time_type == 2) { // 횟수,쿠폰제 기간한정인 경우
                  $("#lesson_period").val($('.lesson_period').eq(1).val());
                  if ($("#lesson_period").val() == '' || $("#lesson_period").val() == 0) {
                      alert("한정기간을 입력하세요.");
                      return false;
                  }
              }
              else {
                  $("#lesson_period").val(0);
              }
          }
          if ($("#lesson_fee").val() == '') {
              alert("단위 수강료를 입력하세요.");
              return false;
          }

          if ($("#status").val() == '') {
              alert("강습상태를 선택하세요.");
              return false;
          }

          if ($("#trainer_idx").val() == '') {
              alert("트레이너를 선택하세요.");
              return false;
          }
          if ($("#check_attendance").val() == '') {
              alert("출결관리를 선택하세요.");
              return false;
          }
      }
  }

  // 수강방식 변경에 따른 수강료 셋팅 변경
  function setPlaceHolder ($isReset=true) {
      if ($isReset == true) $("#lesson_quantity").val("");

      $('.lesson_type').hide();
      //$('.lesson_unit').hide();
      $('#cn_time,#min_time').hide();
      $('#cn_time input,#min_time input').prop('disabled', false);
      $('#cn_time select').prop('disabled', false);
      $('.lesson_type').eq(0).find('input').prop('disabled', false);
      $('.lesson_type').eq(0).find('select').prop('disabled', false);
      $("#class_setting").hide();
      var lesson_type = $('input:radio[name=lesson_type]:checked').val();

      switch (lesson_type) {
          case '1': // 기간제
              $('.lesson_type').eq(0).show();
              $('#cn_time input,#min_time input').prop('disabled', true);
              $('#lesson_apt_default').hide();
              $('#cn_time select').prop('disabled', true);
              $("#user_reservation_layer").css('visibility','hidden');
              enableTrainerInput();
              break;
          case '2': // 횟수제


          case '3': // 쿠폰제
              $('.lesson_type').eq(1).show();
              $('.lesson_unit').html('개');
              $('#cn_time,#min_time').show();
              if($('input:radio[name=lesson_time_type]:checked').val()) {
                $('input:radio[name=lesson_time_type]').trigger('change');
              }
              $('.lesson_type').eq(0).find('input').prop('disabled', true);
              $('.lesson_type').eq(0).find('select').prop('disabled', true);
              $("#user_reservation_layer").css('visibility','hidden');             
              enableTrainerInput();
              break;
        default : // PT
         /*     $('#lesson_apt_default').hide();
              $("#user_reservation_layer").show();
              $('#cn_time,#min_time').show();                      
              userReservation();              
              disableTrainerInput();
              break;
          case '5': // GX */
              $('.lesson_type').eq(1).show();
              $('.lesson_unit').html('회');
              $('#cn_time,#min_time').show();
              $('#lesson_apt_default').hide();
              $('.lesson_type').eq(0).find('input').prop('disabled', true);
              $('.lesson_type').eq(0).find('select').prop('disabled', true);
              $("#class_setting").show();
              $("#user_reservation_layer").show().css('visibility','visible');
              userReservation();                            
              enableTrainerInput();             
              break;              
      }
  }

  $('input[name="lesson_apt_default"]').change(function() {
    if($(this).is(":checked")) {
      $("#additional_fee").show();
    } else {
      $("#additional_fee").hide();
    }
  });

  $('input:radio[name=lesson_time_type]').change(function () {
      var x = $('input:radio[name=lesson_time_type]:checked').val(); //console.log(x);
      switch (x) {
          case '1': // 무제한
              $('#lesson_time_type_x').hide();
              if($('input:radio[name=lesson_type]:checked').val()==3) {
                $('#lesson_apt_default').show();
              }
              $('#lesson_time_type_x input').prop('disabled', true);
              $('#lesson_time_type_x select').prop('disabled', true);
              break;
          case '2': // 기간한정
              $('#lesson_time_type_x').show();
              if($('input:radio[name=lesson_type]:checked').val()==3) {
                $('#lesson_apt_default').hide();
              }
              $('#lesson_time_type_x input').prop('disabled', false);
              $('#lesson_time_type_x select').prop('disabled', false);
              break;
      }
  });
  $('input:radio[name=lesson_time_type]').trigger('change');

/*
  $('.lesson_manage .left_a .category .head a.show_menu').click(function(e){ e.preventDefault(); var a=$.parseParams($(this).attr('href').split('?')[1]);
  getList(a.class_id,$(this).closest('li'))});
  $('.lesson_manage .left_a .category .hide .d_regist').ajaxForm(category_add_form_option);
  $('.delete_form').ajaxForm({dataType :'json',beforeSubmit : delete_form_submit_before, success :delete_course_form });
*/
  setPlaceHolder(false);

  $('input:radio[name="user_reservation"]').change(userReservation);
  $('input:radio[name="lesson_type"]').change(setPlaceHolder);

  userReservation();


  $("#p_auto_extend").change(function(){
    if($(this).val()=='1') {
      $("#p_auto_extend_type_layer").show();
    } else {
      $("#p_auto_extend_type_layer").hide();
    }
  });  

  function userReservation() {
    var user_reservation = $('input[name="user_reservation"]:checked').val();
    if(user_reservation=='1') {
        $(".user_reservation").show();
    } else {
        $(".user_reservation").hide();
    }

  }

  // 담당강사(트레이너) 선택 및 수정시 연결동작 처리하기
  var trainer_idx = $("#trainer_select").val();
  if (trainer_idx) {
      trainer_search(trainer_idx);
  }

  $('#trainer_select').change(function () {
      var trainer_idx = $("#trainer_select").val();
      if (!trainer_idx) {
          $("#trainer_info").val("");
      }
      else {
          trainer_search(trainer_idx);
      }
  });


  $("#c_limit_start_reservation_type,#c_limit_end_reservation_type,#c_limit_cancel_type").change(function(){        
      var row=$(this).closest('.row');
      
      switch($(this).val()) {
          case 'day' :
            row.find('.select-date').show();
            row.find('.select-time').hide();
            break;
          case 'time' :
            row.find('.select-date').hide();
            row.find('.select-time').show();          
            break;
          case 'dayntime' :
            row.find('.select-date').show();
            row.find('.select-time').show();          
            break;
      }
  });
});
