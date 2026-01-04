$(function () {
  $("#s_reference_date").data('datepicker').setStartDate('-10y').setEndDate('+10y');

  $('#s_search_status').change(function(){
    if($(this).val()=='' && ($('#course_id').val()!='' || $('#facility_id').val()!='')) {
      $("#fg_reference_date").show().find('input').removeAttr('disabled');
      $("#default_period_form").hide();
    } else {
      $("#fg_reference_date").hide().find('input').attr('disabled','disabled');
      $("#default_period_form").show();
    }

    if($(this).val()=='') {
      $("#default_period_form").hide();
    } else {

      if($(this).val()=='status7' || $(this).val()=='status8') {
        if($('select[name="er_type"]').length) {
          $('select[name="er_type"]').val('enroll').change();
        }
      }

      if($(this).val()=='status9' || $(this).val()=='status10') {
        if($('select[name="er_type"]').length) {                
          $('select[name="er_type"]').val('rent').change();
        }
      }

      $("#default_period_form").show();
    }
  });

  $("#future_search").val(1);
  $('#a_start_date').data('datepicker').setStartDate('-100y').setEndDate('+50y');
  $('#a_end_date').data('datepicker').setStartDate('-100y').setEndDate('+50y');


  $('.birthday-datepicker').datepicker( { 
    language: "ko",
    changeYear: true, 
    viewMode: "months", 
    maxViewMode: "years"
  });

  $('select[name="user_type"]').change(function(){
    var reference_no_display=true;

    switch($(this).val()) {
      case 'default' :
        $('.not_available_search').hide();
        $('.available_search').show();
        $("#fg_payment_status").show().find('input').removeAttr('disabled');
        break;
      case 'free' :
        $('.not_available_search').hide();
        $('.available_search').show();
        $('#s_payment_id').val('');
        $("#fg_payment_status").hide();
        break;
      default :
        $('.not_available_search').show();
        $('.available_search').hide();
        $("#fg_payment_status").show();  
    }

    if($("#s_search_status").val()=='') {
      if($("#course_id").val()!='' || $("#facility_id").val()!='') {
        reference_no_display = false;
      }
    }

    if(pt_list.includes($("#course_id").val())) {
      reference_no_display = true;
    }

    if(reference_no_display) {
      $("#fg_reference_date").hide().find('input').attr('disabled','disabled');
    } else {
      $("#fg_reference_date").show().find('input').removeAttr('disabled');
    }
  });

  $('select[name="er_type"]').change(function(){
    var card_block=$(this).closest('article.card-block');
    
    switch($(this).val()) {
      case 'enroll' :
        card_block.find('.facility_select_layer').hide();
        card_block.find('.course_select_layer').show();  
        break;
      case 'rent' :
        card_block.find('.facility_select_layer').show();
        card_block.find('.course_select_layer').hide();
        break;
      default :
        card_block.find('.facility_select_layer').hide();
        card_block.find('.course_select_layer').hide();      
    }
  });

  $("#s_type").change(function(){
    $(this).closest('form').find('input[name="search_word"]').focus();

    if($(this).val()=='birthday') {
      $("#field_period_form").show();
      $(this).closest('form').find('.input-group:first').hide().find('input[type="search"]').val('');
     
      $("#birthday_search").show();
    } else {
      $("#field_period_form").hide();
      $(this).closest('form').find('.input-group:first').show();
      $("#birthday_search").hide();
    }
  });

  $('#search_form .card-header').click(function(){
    if($(this).find('.buttons i').text()=='keyboard_arrow_down') {
      $.post('/searches/index-oc',{'format':'json'},function(){

      },'json');
    } else {
      var card=$(this).closest('.card');
      var index=card.find('.card-block').index(card.find('.card-block:visible'));
      
      if(index) {
        $.post('/searches/index-oc/field',{'format':'json'},function(){
  
        },'json');
      } else {
          $.post('/searches/index-oc/default',{'format':'json'},function(){
  
          },'json');
      }
    }
  });

  $("#clear_employee").click(function(){
    $("#s_employee").val('');
    $("#e_employee_fc_id").val('');
    $("#e_employee_trainer_id").val('');
    $("#clear_employee").hide();

    return false;
  });

  $("#search_form .card-header .nav-item .nav-link").click(function(event){
    event.preventDefault();
    event.stopPropagation();

    var card=$(this).closest('.card');
    var index=card.find('.card-header .nav-item .nav-link').index($(this));

    if(index) {
      $.post('/searches/index-oc/field',{'format':'json'},function(){

      },'json');
    } else {
        $.post('/searches/index-oc/default',{'format':'json'},function(){

        },'json');
    }
  });

  $('input[name="enable_reference_date"]').change(function(){
    if($(this).is(":checked")) {
      $('#sb_reference_date').removeAttr('disabled');
    } else {
      $('#sb_reference_date').attr('disabled',true);
    }
  });

  $('#facility_id').change(function(){
    if($(this).val()=='') {
      $('#rent_option').hide().val('');
    } else {
      $('#rent_option').show();
    }
  });

  $("#course_id").change(function(){    
    var reference_no_display=true;

    if($("#s_search_status").val()=='') {
      if($("#course_id").val()!='' || $("#facility_id").val()!='') {
        reference_no_display = false;
      }
    }

    if(pt_list.includes($(this).val())) {
      $("#s_user_type option:eq(1)").text('유효회원');
      $("#s_user_type option:eq(2)").text('종료회원');
      reference_no_display=true;
    } else {
      $("#s_user_type option:eq(1)").text('유료회원');
      $("#s_user_type option:eq(2)").text('무료회원');
    }

    if(reference_no_display) {
      $("#fg_reference_date").hide().find('input').attr('disabled','disabled');
    } else {
      $("#fg_reference_date").show().find('input').removeAttr('disabled');
    }

    $("#s_user_type").effect('highlight');
  });

  var currentYear = new Date().getFullYear();
  var startYear = currentYear - 100;
  
  $('#yearpicker').datepicker({
    format: "yyyy",
    viewMode: "years",
    minViewMode: "years",
    language: "ko",
    autoclose: true,
    startDate: new Date(startYear, 0, 1),
    endDate: new Date(currentYear, 0, 1)
});


    $('#schMonth').monthpicker({
				monthNamesShort: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
				dateFormat: 'yy-mm',
			});


  $('input[name="birthday_search_type"]').change(function(){
    switch($(this).val()) {
      case 'birthday_month':
        $('#birthday_custom_serach_input').hide();
        $('#birthday_month_serach_input').show();   
        $('#birthday_year_serach_input').hide();        
        break;
      case 'custom_period_search':
        $('#birthday_custom_serach_input').show();
        $('#birthday_month_serach_input').hide();   
        $('#birthday_year_serach_input').hide();        
        break;
      default : 
        $('#birthday_custom_serach_input').hide();
        $('#birthday_month_serach_input').hide();   
        $('#birthday_year_serach_input').show();
        break;
    }
  });

  $('input[name="birthday_search_type"]:checked').trigger('change');
});
