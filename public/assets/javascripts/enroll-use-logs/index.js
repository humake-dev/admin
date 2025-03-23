$(function () {
    $('#s_employee,.select-employee').attr('style','cursor:pointer');
    $("#s_employee").click(function(){
        $('.select-employee').click();
    });

    $('#c_name,.select-user').attr('style','cursor:pointer');
    $("#c_name").click(function(){
        $('.select-user').click();
    });
});