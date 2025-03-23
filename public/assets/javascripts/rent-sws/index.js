$(function () {
    $('#user_rent_sw_list tr td').click(function() {
        var tr=$(this).parent();
        /* if(tr.hasClass('table-primary')) {
            return false;
        } */

        if(!tr.find('td:first input').length) {
            return false;
        }

    var rent_id=tr.find('td:first input:eq(0)').val();
    $('.rent_sw_transfer').attr('href','/rent-sws/transfer/'+rent_id);

    if($('#user_rent_sw_edit').length) {
      $('#user_rent_sw_edit').attr('href','/rent-sws/edit/'+rent_id);
    }

    if($('#user_rent_sw_delete').length) {
      $('#user_rent_sw_delete').attr('href','/rent-sws/delete/'+rent_id);
    }
 
    
    tr.parent().find('tr').removeClass('table-primary');
    tr.addClass('table-primary');
  }).css('cursor','pointer');

  $('#rent_sws_search .card-header').click(function(){
    if($(this).find('.buttons i').text()=='keyboard_arrow_down') {
      $.post('/rent-sws/index-oc',{'format':'json'},function(){

      },'json');
    } else {
      var card=$(this).closest('.card');
      var index=card.find('.card-block').index(card.find('.card-block:visible'));
      
      if(index) {
        $.post('/rent-sws/index-oc/field',{'format':'json'},function(){
  
        },'json');
      } else {
          $.post('/rent-sws/index-oc/default',{'format':'json'},function(){
  
          },'json');
      }
    }
  });

  $("#rent_sws_search .card-header .nav-item .nav-link").click(function(event){
    event.preventDefault();
    event.stopPropagation();

    var card=$(this).closest('.card');
    var index=card.find('.card-header .nav-item .nav-link').index($(this));

    if(index) {
      $.post('/rent-sws/index-oc/field',{'format':'json'},function(){

      },'json');
    } else {
        $.post('/rent-sws/index-oc/default',{'format':'json'},function(){

        },'json');
    }
  });
});