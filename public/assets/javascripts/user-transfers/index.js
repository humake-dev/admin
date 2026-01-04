$(function () {
    $("#cb_branch").change(function(){
        var branch_id=$(this).val();


        $("#new_enroll select option").remove();
        $("#new_rent select option").remove();
        $("#new_rent_sw select option").remove();

        if(branch_id=='') {
            $("#new_enroll select").attr('disabled',true).append('<option value="0">'+$('#text_sbf').val()+'</option>').effect('highlight');
            $("#new_rent select").attr('disabled',true).append('<option value="0">'+$('#text_sbf').val()+'</option>').effect('highlight');
            $("#new_rent_sw select").attr('disabled',true).append('<option value="0">'+$('#text_sbf').val()+'</option>').effect('highlight');                       
        } else {
            $("#new_enroll select").removeAttr('disabled').append('<option value="0">'+$('#text_nt').val()+'</option>');
            $("#new_rent select").removeAttr('disabled').append('<option value="0">'+$('#text_nt').val()+'</option>');
            $("#new_rent_sw select").removeAttr('disabled').append('<option value="0">'+$('#text_nt').val()+'</option>');

            if($("#new_enroll").length) {
                $.getJSON('/courses',{'branch_id':branch_id,'format':'json'},function(data){
                    if(data.total<1) {
                        return false;
                    }

                    $("#new_enroll select").each(function(i,select_e){
                        $.each(data.list,function(index,value){
                            $(select_e).append('<option value="'+value.id+'">'+value.title+'</option>');
                        });
                        $(select_e).effect('highlight');
                    });
                });
            }
            
            if($("#new_rent").length) {     
                $.getJSON('/facilities',{'branch_id':branch_id,'format':'json'},function(data){
                    if(data.total<1) {
                        return false;
                    }

                    $("#new_rent select").each(function(i,select_e){
                        $.each(data.list,function(index,value){
                            $(select_e).append('<option value="'+value.id+'">'+value.title+'</option>');
                        });
                        $(select_e).effect('highlight');
                    });
                });
            }
            
            if($("#new_rent_sw").length) {
                $.getJSON('/sports-wears',{'branch_id':branch_id,'format':'json'},function(data){
                    if(data.total<1) {
                        return false;
                    }

                    $("#new_rent_sw select").each(function(i,select_e){
                        $.each(data.list,function(index,value){
                            $(select_e).append('<option value="'+value.id+'">'+value.title+'</option>');
                        });
                        $(select_e).effect('highlight');
                    });
                });
            }       
        }
    });
});