function getList(current_page, jq) {
  if(!current_page)
    current_page = 0;

  var perPage =10;
  var pageID=current_page+1;
  var employee_id=$('#employee_attendance_form input[name="employee_id"]').val();

  $.getJSON('/employee-attendances',{'employee_id':employee_id,'format':'json','per_page':perPage,'page':pageID},function(data) {
      if(data.result=='success') {
        $("#user_attendance_list tbody").empty();
        $('#user_list_count').val(data.total);

        if(data.total) {
          $.each(data.list,function(index,value){
            var ta=value['in_time'].split(' ');
            var i_date=ta[0].split('-');
            var i_time=ta[1].split(':');            
            var in_date=Number(i_date[0])+'년 '+Number(i_date[1])+'월 '+Number(i_date[2])+'일';
            var in_time=i_time[0]+'시 '+ i_time[1]+'분 ';
            $("#user_attendance_list tbody").append('<tr><td>'+in_date+' '+in_time+'</td><td><a href="/employee-attendances/delete-confirm/'+value['id']+'" class="btn btn-danger btn-delete-confirm">삭제</a></td></tr>');
          });
          $("#user_attendance_list tr").css('cursor','pointer');
          $('#user_attendance_list tbody td').click(function(e){
            var i_checkbox=$(this).parent().find('input:first');
            var user_idx = i_checkbox.val();
          });
        } else {
          $("#user_attendance_list tbody").append('<tr><td colspan="2" style="text-align:center">해당 데이터가 없습니다.</td></tr>');
        }
        $(".sl_pagination").removeData("load").empty();
        initPagination(data.total,10,current_page);
      } else {
      }
  });
}

$(function () {
  // 자바스크립트가 지원될때 Tr 커서 선택
  $("#employee_list tr").css('cursor','pointer');

  $("#employee_list td").click(function(){
    location.href=$(this).parent().find('a').attr('href');
  });

  $('#photo_load').click(function () {
  	$(this).parent().find('input:file').prop('capture', '');
  	$(this).parent().find('input:file').trigger('click');
  });

  $('#form_photo input:file').change(function () {
    var formData = new FormData();
    formData.append('photo[]', $('input:file')[0].files[0]);
    formData.append('format','json');

    $.ajax({
        url :$('#form_photo').attr('action'),
        type: "POST",
        data : formData,
        processData: false,
        contentType: false,
        success:function(data, textStatus, jqXHR){
          var json = $.parseJSON(data);

          if(json.id!=true) {
            var form=$('<form action="/employee-pictures/delete/'+json.id+'" method="post" accept-charset="utf-8">');
            form.append('<input value="'+$("#delete-photo-layer span").text()+'" class="btn btn-sm btn-outline-secondary btn-block" type="submit">');
            $("#delete-photo-layer").empty().append(form);
          }

          showPhoto(json.photo);
        },
        error: function(jqXHR, textStatus, errorThrown){
            //if fails
        }
  });
});

    initPagination(Number($('#user_attendance_count').val()),10);


    $('#attendance_calendar').datepicker({
      templates: {leftArrow: '<i class="fa fa-arrow-left"></i>',rightArrow: '<i class="fa fa-arrow-right"></i>'},
        language: "ko",
        todayHighlight: true,
        beforeShowDay: function(date){
            if (typeof active_dates === 'undefined') {
              return true;
            }
  
            var formattedDate = convertDate(date);
            if ($.inArray(formattedDate, active_dates) != -1){
              return {
                 classes: 'active-check'
              };
            }
         return;
      }}).on('changeDate', function(e) {
        $('#employee_attendance_date').val(e.format());
    });
});
