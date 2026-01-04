$(function () {
    $("#re-enroll-button").click(function(){
        $("#course_id").val($("#extend-course-id").val()).change();
        $("#e_trainer").val($("#extend-trainer-id").val());
        $("#o_start_date").val($("#extend-start-date").val());
        $("#e_quantity").val($("#extend-insert-quantity").val()).change();
        $('#myModal').modal('hide');
        $("#o_start_date,#e_quantity").effect('highlight');
        $("#re-enroll").prop('checked',true);

        if($("#e_content_re_order_no").length) {
            $("#re_order_no").val($("#e_content_re_order_no").val());
            $("#re_order_no").closest('.form-check').find('input[type="checkbox"]').prop('checked','checked');
        }
    });    
});