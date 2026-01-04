$(function () {
    $('.birthday-datepicker').datepicker({
    startView: 'years',
    defaultViewDate : {'year':1990},
    startDate:'-100y',
    endDate:'-0y',
    maxViewMode : 'decades',
    format: 'yyyy-mm-dd',
    autoclose :true,
    language : 'ko'
    });
});