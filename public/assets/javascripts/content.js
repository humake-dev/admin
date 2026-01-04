$(document).ready(function(){
  $(".content-edit").click(function(e){
    e.preventDefault();
    var modal_box=$(this).closest('.modal');
    modal_box.find('.modal-footer').empty().append('<input type="submit" value="메모 수정" class="btn btn-block btn-primary" />');
    modal_box.find('.content-text').hide();
    modal_box.find('.form-group').show();
    return false;
  });

  $(".content-delete").click(function(e){
    e.preventDefault();
    if(!confirm('정말로 메모를 삭제합니까?')) {
      return false;
    }

    var url=$(this).attr('href').replace('/delete-confirm/','/delete/');
    $.post(url,{'format':'json'},function(data){
      if(data.result=='success') {
        location.reload();
      } else {
        alert(data.message);
      }
    },'json');
  });
});
