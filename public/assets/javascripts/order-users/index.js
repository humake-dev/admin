$(function () {
	$('.btn-ou-delete-confirm').click(function(e){
		e.preventDefault();
		var tr=$(this).closest('tr');
		
		var product_name=tr.find('.ou_product').text();
		var user_name=tr.find('.ou_user').text();
		
		confirm_message=user_name+'님의 주문('+product_name+')을 정말로 삭제합니까?';


		if(confirm(confirm_message)) {
			var url=$(this).attr('href').replace('/delete-confirm/','/delete/');
			$.post(url,{'format':'json'},function(data){
				if(data.result=='success') {
					tr.effect('highlight',function() {
						location.reload();
					});
				} else {
					alert(data.message);
				}
			},'json');
		}
	});
});