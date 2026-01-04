$(function () {
    $("#b_use_ac_controller").change(function(){
        if($(this).val()==1) {
            $(this).closest('.card-block').find('.ac_contoller').show();
        } else {
            $(this).closest('.card-block').find('.ac_contoller').hide().find('select').val('0');
        }
    });

    $('.branch_content_section .card-header').click(function(){
        if($(this).find('.buttons i').text()=='keyboard_arrow_down') {
          $.post('/branches/index-oc',{'format':'json'},function(){
    
          },'json');
        } else {
          var card=$(this).closest('.card');
          var index=card.find('.card-block').index(card.find('.card-block:visible'));
          
          if(index) {
            $.post('/branches/index-oc/access',{'format':'json'},function(){},'json');
            } else {
            $.post('/branches/index-oc/default',{'format':'json'},function(){},'json');
            }
        }
      });
    
      $(".branch_content_section .card-header .nav-item .nav-link").click(function(event){
        event.preventDefault();
        event.stopPropagation();
    
        var card=$(this).closest('.card');
        var index=card.find('.card-header .nav-item .nav-link').index($(this));
    
        if(index) {
            $.post('/branches/index-oc/access',{'format':'json'},function(){},'json');
        } else {
            $.post('/branches/index-oc/default',{'format':'json'},function(){},'json');
        }
      });     
});