$(function () {
  var s_data={};

  $("#layer_schedule_w table tbody td").click(function(event){
    event.preventDefault();
    location.href=$(this).find('a').attr('href');
  }).css('cursor','pointer');

  $("#c_name").click(function(){
    $(this).parent().find('.r-select-user').click();
  }).css('cursor','pointer');

	$(".r-select-user").click(function(event){
		event.preventDefault();
		$('#myModal').removeData("modal");
		$('#myModal').load('/reservations/select/single?popup=no',s_data,function(){
			$('#myModal').modal();
		});
  }).css('cursor','pointer');

  function set_users() {
    if($("#r_manager").length) {
      var trainer_id=$("#r_manager").val();
    } else {
      var trainer_id=$('input[name="manager"]').val();
    }
    
    if($("#r_type").val()=='PT') {
      s_data.quantity_only=true;
      
      if($("#r_course").val()) {
        s_data.course_id=$("#r_course").val();
        s_data.trainer_id=trainer_id;
      }
    } else {
      s_data.course_id=null;
    }
  }

  function type_change(){
    var manager_id=$("#r_manager").val();

    if($("#r_type").val()=='PT') {
      $("#r_course_layer").show();
      
      if($('#r_manager').length) {
        $.getJSON('/employees',{'trainer':1,'format':'json','per_page':10000},function(data){
          $("#r_manager").empty();
          if(data.result=='success') {
            if(data.total) {
              $("#r_manager").append('<option value="">선택하세요</option>');
              $.each(data.list,function(index,value){
                $("#r_manager").append('<option value="'+value.id+'">'+value.name+'</option>');
              });
              $("#r_manager").val(manager_id).effect('highlight').change();           
            } else {
            }
          } else {
            alert(data.message);
          }
        });
      }
    } else {
      $("#r_course_layer").hide();

      if($('#r_manager').length) {
        $.getJSON('/employees',{'format':'json','per_page':10000},function(data){
          if(data.result=='success') {
            if(data.total) {
              $("#r_manager").empty();
              $("#r_manager").append('<option value="">선택하세요</option>');
              
              $.each(data.list,function(index,value){
                $("#r_manager").append('<option value="'+value.id+'">'+value.name+'</option>');
              });
              
              $("#r_manager").val(manager_id).effect('highlight').change();
            } else {

            }
          } else {
            alert(data.message);
          }
        });
        
        set_users();
      }
    }
  }

  $('.datepicker').change(function () {
      var date = $(this).val();
      location.href='index.php?date='+date;
  });

  $('#r_type').change(type_change);
  type_change();


  $("#r_manager").change(function(){
      if($("#r_type").val()) {
        set_users();
      }
  });

  $('#r_course').change(function(){
    if($(this).val()=='') {
      return false;
    }

    if($(this).val()!='') {
      set_users();
    }
  });

  if($("#r_type").val()=='PT') {
    if($("#r_course option").length==2) {
      $("#r_course").val($("#r_course option:eq(1)").attr('value')).change();
    }
  }

  $("#reservation_form").submit(function(){
    if($("#r_type").val()=='PT') {
      if($("#r_course").val()=='') {
        display_message($("#message_course_empty").val(),'danger');
        return false;
      }
    }

    if($("#r_users").val()=='' || $("#r_users").val()===null) {
      display_message($("#message_user_empty").val(),'danger');
      return false;
    } 
  });
});
