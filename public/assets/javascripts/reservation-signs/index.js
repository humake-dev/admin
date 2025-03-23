$(function () {
  $("#signature").jSignature();

  $("#reservation_signs form").submit(function(){
    $(this).find('input:first').val($("#signature").jSignature("getData"));
  });
});
