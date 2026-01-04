$(function () {
  $("#clear_employee").click(function(){
    $("#s_employee").val('');
    $("#e_employee_id").val('');
    $("#clear_employee").hide();

    return false;
  });

  $("#s_no_manager").change(function(){
    if($(this).is(':checked')) {
      $("#s_employee").val('');
      $("#e_employee_id").val('');
      $("#select_manager_field").hide();
    } else {
      $("#select_manager_field").show();
    }
  });

  $(".manager-td").click(function(){
    $(".manager-td").each(function(){
      if($(this).find(".counsel_manager_form").is(':visible')) {
        $(this).find(".manager-name").show();      
        $(this).find(".counsel_manager_form").hide();
      }
    });

    if(!$(this).find(".counsel_manager_form").is(':visible')) {
      $(this).find(".manager-name").hide();      
      $(this).find(".counsel_manager_form").show().find('select').click(function(){
        return false;
      });
    }

  }).css('cursor','pointer');

  $('#counsel_search .card-header').click(function(){
    if($(this).find('.buttons i').text()=='keyboard_arrow_down') {
      $.post('/counsels/index-oc',{'format':'json'},function(){

      },'json');
    } else {
      var card=$(this).closest('.card');
      var index=card.find('.card-block').index(card.find('.card-block:visible'));
      
      if(index) {
        $.post('/counsels/index-oc/field',{'format':'json'},function(){
  
        },'json');
      } else {
          $.post('/counsels/index-oc/default',{'format':'json'},function(){
  
          },'json');
      }
    }
  });

  $("#counsel_search .card-header .nav-item .nav-link").click(function(event){
    event.preventDefault();
    event.stopPropagation();

    var card=$(this).closest('.card');
    var index=card.find('.card-header .nav-item .nav-link').index($(this));

    if(index) {
      $.post('/counsels/index-oc/field',{'format':'json'},function(){

      },'json');
    } else {
        $.post('/counsels/index-oc/default',{'format':'json'},function(){

        },'json');
    }
  }); 
});
