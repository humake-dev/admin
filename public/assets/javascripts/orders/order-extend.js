$(function () {
  $('#o_ae_select_month input[name="select_month"]').change(function() {
    if($(this).val()=='current') {
      $('#o_start_date').val($("#order_today").val()).effect('highlight');                      
      $('#o_end_date').val($("#order_ae_current_end_date").val()).effect('highlight');
      $("#is_today").prop('checked',true).change();
      $("#today_display").effect('highlight');
    } else {
      $('#o_start_date').val($("#order_ae_next_start_date").val()).effect('highlight');                   
      $('#o_end_date').val($("#order_ae_next_end_date").val()).effect('highlight');
      $("#is_today").prop('checked',false).change();
      $("#o_transaction_date").val($("#order_ae_next_start_date").val()).effect('highlight');
    }
  });
});
