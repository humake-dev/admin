$(function () {
    $("#signature").jSignature();
  
    $("#contract_signs form").submit(function(){
      $(this).find('input:first').val($("#signature").jSignature("getData"));
    });
  });
  