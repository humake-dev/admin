$(function () {

  $("#f_facility_id").change(function(){
    var facility_id=$(this).val();
    var rent_id=$("#rent_move_form").attr('action').split('/move/')[1];
    $.getJSON('/rents/get-available-no/'+facility_id,{'move_current_rent_id':rent_id,'format':'json'},function(data){
      if(data.result=='success') {
        $("#f_no").empty();

        if(data.total) {
          $("#f_no").append('<option value="0">미정</option>');
          $.each(data.list,function(index,value){
            if(value.enable) {
              $("#f_no").append('<option value="'+value.no+'">'+value.no+'</option>');
            } else {
              $("#f_no").append('<option value="'+value.no+'" disabled="disabled">'+value.no+'('+value.disable+')</option>');
            }
          });
          $("#f_no").effect('highlight');
        } else {
          $("#f_no").append('<option value="0">미정</option>');
        }

      } else {
        alert(data.message);
      }
    });
  });

  $("#f_facility_id").change();
});
