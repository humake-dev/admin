$(function () {
  $("#user_find_form").submit(function(){
    getList();
    return false;
  });
  
  $("#user_select_cancel").click(user_select_cancel);
  
  $('input[name="user_search_type"]').change(function() {
    $("#search_label").text($(this).parent().find('label').text());
  });
  
  function getList(current_page, jq) {
        if(!current_page)
          current_page = 0;
      
        var perPage =10;
        var pageID=current_page+1;
      
        var searchType=null;
        var searchField=null;
        var searchWord=null;
      
        if($.trim($("#u_search_word").val())!='') {
          searchType='field';
          searchField=$('input[name="user_search_type"]:checked').val();
          searchWord=$.trim($("#u_search_word").val());
        }
      
          var search_param={'search_type':'field','search_field' : searchField,'search_word' : searchWord ,'format': 'json','per_page':perPage,'page':pageID};
      
          if($('aside input[name="rent_info"]').length) {
            search_param.rent_info=1;
          }
      
          if($('aside input[name="enroll_info"]').length) {
            search_param.enroll_info=1;
          }
      
          $.getJSON('/users/select',search_param,function(data){
            if(data.result=='success') {
              if(data.total==1) {
                $("#user_select_list_layer").hide();
                var content=data.content;
      
                if(!$("#dongho_c").length) {
                  content.address_detail=content.birthday;
                }
      
                select_user(content);
      
                if(data.rent_info) {
                  $("#rent_list h3 span").text(content.name);              
                  rentInfo(data.rent_list);
                }
      
                if(data.enroll_info) {
                  $("#enroll_list h3 span").text(content.name);              
                  enrollInfo(data.enroll_list);
                }
              
                $("#rent_user_find_form h3 span:first").hide();
                $("#rent_user_find_form h3 span:eq(1)").show();
      
                $("#r_facility_id").change();
              } else {
                if(data.total) {
                  $("#user_select_list_layer").show();
                  $("#user_select_list tbody").empty();
                  $('#user_select_list_count').val(data.total);
                
                  if(data.total) {
                    $.each(data.list,function(index,value){
                      if($("#dongho_c").length) {
                        if(value.address_detail) {
                          var birthday=value.address_detail;
                        } else {
                          var birthday='입력안됨';
                        }
                      } else {
                        if(value.birthday) {
                          var birthday=value.birthday;
                        } else {
                          var birthday='입력안됨';
                        }
                      }
                
                      var input='<td class="text-center"><input name="id" value="'+value['id']+'" type="radio"></td>';
      
      
                      var tr='<tr>'+input+'<td class="name">'+value['name']+'</td>';
                      
                      if($("#th_access_card_no").length) {
                        tr+='<td>'+value['card_no']+'</td>';
                      }
      
                      tr+='<td>'+birthday+'</td><td>'+display_gender(value['gender'])+'<input type="hidden" name="gender[]" value="'+value['gender']+'" /></td><td class="phone">'+add_hyphen(value['phone'])+'</td></tr>';
                      $("#user_select_list tbody").append(tr);
                    });
                
                    $('#user_select_list tbody td').click(m_td_click);
                    $('#user_select_list tbody tr td input').click(function(e) {
                      e.stopPropagation();
                    }).change(function(){
                      var search_param={'user_id' : $(this).val() ,'format': 'json'};
          
                      if($('aside input[name="rent_info"]').length) {
                        search_param.rent_info=1;
                      }                
      
                      $.getJSON('/users/select',search_param,function(data){
                        if(data.result=='success') {
                          if(data.total==1) {
                            $("#user_select_list_layer").hide();
                            var content=data.list[0];
                  
                            if(!$("#dongho_c").length) {
                              content.address_detail=content.birthday;
                            }
                  
                            select_user(content);
                  
                            if(data.rent_info) {
                              $("#rent_list h3 span").text(content.name);                            
                              rentInfo(data.rent_list);
                            }
      
                            if(data.enroll_info) {
                              $("#enroll_list h3 span").text(content.name);                            
                              enrollInfo(data.enroll_list);
                            }                      
                  
                            $("#rent_user_find_form h3 span:first").hide();
                            $("#rent_user_find_form h3 span:eq(1)").show();
                  
                            $("#r_facility_id").change();
                          } else {
      
                          }
                        }
                      });

                    });
                  } else {
                    $("#user_select_list tbody").append('<tr><td colspan="4" style="text-align:center">해당 데이터가 없습니다.</td></tr>');
                  }
                  $(".sl_pagination").removeData("load").empty();
                  initPagination(data.total,10,current_page);  
                } else {
                  $("#user_select_list_layer").hide();
                  alert('해당 회원이 없습니다.');
                  $('.user_select_rel_form input[type="submit"]').attr('disabled','disabled');
                }
              }
            } else {
              alert(data.message);
            }
          });
      
          return false;
      }
      
      function m_td_click() {
        $(this).parent().find('input:first').prop('checked',true).change();
      }
      
      function select_user(content) {
        $("#o_user_id").val(content.id);
        $("#user_info").show();
        $("#user_search").hide();
        $("aside").removeAttr('class').attr('class','col-12 col-lg-4 col-xxl-3');
        $("#right_data_form").removeAttr('class').attr('class','col-12 col-lg-8 col-xxl-9');
      
        var name=content.name;
        
      
        $("#user_name").html(name);
        
        var phone='미입력';
        var address='미입력';
      
        if(content.phone) {
          phone=add_hyphen(content.phone);
        }
      
        $("#user_phone").text(phone);
      
        if(content.address_detail) {
          address=content.address_detail;
        }
      
        $("#user_address").text(address);
      
        if(content.picture_url) {
          $("#profile_photo").attr('src',content.picture_url);
        } else {
          $("#profile_photo").attr('src','/assets/images/common/bg_photo_none.gif');
        }
        
        $('.user_select_rel_form input[type="submit"]').removeAttr('disabled');
    }          
});