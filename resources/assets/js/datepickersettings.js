var generalSettings = {
    todayHighlight: true,
    autoclose: true,
    format: "dd.mm.yyyy"
};

var startDateSettings = $.extend({}, generalSettings, { startDate: "0d", todayBtn: "linked" });
$(".datepicker.start-date").datepicker(startDateSettings);

var endDateSettings = $.extend({}, generalSettings, { startDate: "+1d" });
$(".datepicker.end-date").datepicker(endDateSettings);

$(".datepicker").datepicker(generalSettings);
