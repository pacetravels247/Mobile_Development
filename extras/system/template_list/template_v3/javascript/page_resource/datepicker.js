var dp_visi_months = 1;
if ($(window).width() > 420) {
    dp_visi_months = 2
} else {
    dp_visi_months = 1
}

function db_date(days, timestamp) {
    if (timestamp == undefined || timestamp == '' || parseInt(timestamp) == NaN || timestamp < 1) {
        timestamp = Date.now()
    }
    if (days == undefined || days == '' || parseInt(days) == NaN || days < 1) {
        days = 0
    }
    var cdate = new Date(timestamp);
    var ndate = new Date(cdate.getFullYear(), cdate.getMonth(), ((cdate.getDate()) + days));
    return zeroPad(ndate.getFullYear()) + '-' + zeroPad(ndate.getMonth() + 1, 2) + '-' + zeroPad((ndate.getDate()), 2)
}

function add_days_to_date(from_date, to_date, no_of_nights) {
    var _from_date_val = $(from_date).val();
    var _nights_count_val = parseInt($(no_of_nights).val());
    if (_from_date_val != '' && _nights_count_val != '' && _nights_count_val > 0) {
        _from_date_val = new Date(_from_date_val);
        _to_date_val = new Date(_from_date_val.getFullYear(), _from_date_val.getMonth(), (_from_date_val.getDate() + _nights_count_val));
        $(to_date).val(_to_date_val.getFullYear() + '-' + (_to_date_val.getMonth() + 1) + '-' + _to_date_val.getDate())
    }
}

function zeroPad(num, count) {
    var numZeropad = num + '';
    while (numZeropad.length < count) {
        numZeropad = "0" + numZeropad
    }
    return numZeropad
}

function dateADD(currentDate) {
    var valueofcurrentDate = currentDate.valueOf() + (24 * 60 * 60 * 1000);
    var newDate = new Date(valueofcurrentDate);
    return newDate
}

function daysInMonth(month, year) {
    return new Date(year, month, 0).getDate()
}

function get_day_difference(date1, date2) {
    if (date1 != '' && date2 != '') {
        var date1 = new Date(date1);
        var date2 = new Date(date2);
        var time_diff = date2.getTime() - date1.getTime();
        var days = time_diff / (1000 * 3600 * 24);
        return days
    }
}

function bindDatepicker(inputId) {
    $('#' + inputId).datepicker({
        numberOfMonths: dp_visi_months,
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true
    })
}

function bindMonthPicker(inputId) {
    $('#' + inputId).datepicker({
        numberOfMonths: dp_visi_months,
        dateFormat: 'mm-yy',
        changeMonth: true,
        changeYear: true
    })
}

function pastDatepicker(inputId) {
    $('#' + inputId).datepicker({
        numberOfMonths: dp_visi_months,
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        maxDate: 0,
        yearRange: "-100:-0"
    })
}

function futureDatepicker(inputId) {
    $('#' + inputId).datepicker({
        numberOfMonths: dp_visi_months,
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        minDate: 0
    })
}

function futureDatepickerSingleMonth(inputId) {
    $('#' + inputId).datepicker({
        numberOfMonths: 1,
        dateFormat: 'dd-mm-yy',
        changeMonth: true,
        changeYear: true,
        minDate: 0
    })
}

function futureDatepickerMonthDisabled(inputId) {
    $('#' + inputId).datepicker({
        numberOfMonths: dp_visi_months,
        dateFormat: 'dd-mm-yy',
        minDate: 0
    })
}

function futureDateTimepicker(inputId) {
    $('#' + inputId).datetimepicker({
        step: 30,
        format: 'd-m-Y H:i',
        minDate: 0,
        defaultTime: '09:00:am',
        formatTime: 'h:i:a'
    })
}

function monthDatepicker(inputId) {
    $('#' + inputId).datepicker({
        numberOfMonths: dp_visi_months,
        dateFormat: 'mm-yy',
        changeMonth: true,
        changeYear: true,
        maxDate: 0,
        yearRange: "-100:-0"
    })
}

function adultDatePicker(elementId) {
    $('#' + elementId).datepicker({
        yearRange: "-100:-0",
        changeYear: true,
        changeMonth: true,
        numberOfMonths: dp_visi_months,
        dateFormat: 'yy-mm-dd',
        minDate: '-100Y',
        maxDate: '-12Y',
    })
}

function childDatePicker(elementId) {
    $('#' + elementId).datepicker({
        yearRange: "-12:-2",
        changeYear: true,
        changeMonth: true,
        numberOfMonths: dp_visi_months,
        dateFormat: 'yy-mm-dd',
        minDate: '-12Y',
        maxDate: '-2Y',
    })
}

function infantDatePicker(elementId) {
    $('#' + elementId).datepicker({
        yearRange: "-2:+0",
        changeYear: true,
        changeMonth: true,
        numberOfMonths: dp_visi_months,
        dateFormat: 'yy-mm-dd',
        minDate: '-2Y',
        maxDate: '0',
    })
}

function auto_set_dates(date_1, input2, date_type, add_days) {
	var add_days = typeof add_days !== 'undefined' ?  add_days : 1;
    var date_1_ts = Date.parse(date_1);
    if (isNaN(date_1_ts) == false) {
        var ip_2 = $("#" + input2);
        ip_2.trigger("click");
        var selectedDate = date_1;
        
        //var nextdayDate = dateADD(selectedDate);
        nextdayDate = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), (selectedDate.getDate() + add_days));
        
        var nextDateStr = zeroPad(nextdayDate.getDate(), 2) + "-" + zeroPad((nextdayDate.getMonth() + 1), 2) + "-" + (nextdayDate.getFullYear());
        ip_2.datepicker('option', date_type, nextdayDate);
        var second_date = ip_2.datepicker('getDate');
        var date_diff = get_day_difference(selectedDate, second_date);
        if (date_diff < 1 && ip_2.is(':disabled') == false && ip_2.hasClass('disable-date-auto-update') == false) {
            ip_2.val(nextDateStr)
        }
    }
}