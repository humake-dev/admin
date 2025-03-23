$(function () {
    $("#sync-content-button").click(function(){
        $("#o_start_date").val($("#sync-start-date").val());
        $("#r_rent_month").val($("#sync-insert-quantity").val()).change();
        $("#o_end_date").val($("#sync-end-date").val());
        $('#myModal').modal('hide');
    });
    
    $("#extend-content-button").click(function(){
        $("#default_facility_id").val($("#extend-facility-id").val());
        $("#default_no").val($("#extend-no").val());
        $("#r_no").val($("#extend-no").val());
        $("#r_facility_id").val($("#extend-facility-id").val());        
        $("#o_start_date").val($("#extend-start-date").val());
        $("#r_rent_month").val($("#extend-insert-quantity").val()).change();   
        $('#myModal').modal('hide');
        $("#re-rent").val('1');
    });
});
