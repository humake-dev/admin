$(function () {
    $('input[name="search_type"]').change(function(){
        switch($(this).val()) {
            case 'search_s_or_g' :
                $("#search_period,#search_period_type").hide().find('input').attr('disabled','disabled');                
                $("#search_number").hide().find('input').attr('disabled','disabled');
                $("#search_range").show().find('input').removeAttr('disabled');
                $("#search_range").find('input,label').effect('highlight');
                $("pt_serial").blur();
                break;
            case 'search_serial' :
                $("#search_period,#search_period_type").hide().find('input').attr('disabled','disabled');
                $("#search_range").hide().find('input').attr('disabled','disabled');
                $("#search_number").show().find('input').removeAttr('disabled');
                $("#search_number").find('input,label').effect('highlight');
                $("#pt_serial").focus();
                break;
            default :
                $("#search_number").hide().find('input').attr('disabled','disabled');
                $("#search_range").hide().find('input').attr('disabled','disabled');
                $("#search_period,#search_period_type").show().find('input').removeAttr('disabled');
                $("#search_period,#search_period_type").find('input,label').effect('highlight');     
                $("pt_serial").blur();
        }
    });

    $('#search_period_type select').change(function(){
        $("#search_period label:first").text($(this).find('option[value="'+$(this).val()+'"]').text()).effect('highlight'); 
    });
});