$(document).ready(function () {

    function set_flight_cookie_data() {
        var s_params = $('#flight_form').serialize().trim();
        setCookie('flight_search', s_params, 100)
    }
    $('#transfer_from, #transfer_to, [name="transfer_type"]').on('change, blur', function () {
        is_domestic_oneway_search()
    });

    function is_domestic_oneway_search() {}


    $('[name="transfer_type"]').on('change', function () {
        handle_active_transfer_type(this.value)
    });

    function handle_active_transfer_type(_active_transfer_type) {
        if (_active_transfer_type == 'oneway') {
            $('#transfer_datepicker2').attr('disabled', true).removeAttr('required').closest('.date-wrapper').animate({
                'opacity': '.3'
            });
            $('#transfer_datepicker2').val('');
            if ($('#onew-trip').parent('label.wament').hasClass('active') == false) {
                $('#onew-trip').parent('label.wament').addClass('active')
            }
            if ($('#rnd-trip').parent('label.wament').hasClass('active') == true) {
                $('#rnd-trip').parent('label.wament').removeClass('active')
            }
        } else if (_active_transfer_type == 'circle') {
            if ($('#rnd-trip').parent('label.wament').hasClass('active') == false) {
                $('#rnd-trip').parent('label.wament').addClass('active')
            }
            if ($('#onew-trip').parent('label.wament').hasClass('active') == true) {
                $('#onew-trip').parent('label.wament').removeClass('active')
            }
            $('#transfer_datepicker2').removeAttr('disabled').attr('required', 'required').closest('.date-wrapper').animate({
                'opacity': '1'

            }).focus()
        } else if (_active_transfer_type == 'multicity') {
        }
    }
    handle_active_transfer_type($('[name="transfer_type"]:checked').val());
    var cache = {};
    var transfer_from = $('#transfer_from').val();
    var transfer_to = $('#transfer_to').val();
    $(".fromtransfer, .departtransfer").catcomplete({
        source: function (request, response) {
            var term = request.term;
            if (term != ' ' && term != '')//remove autocomplete
            {
                if (term in cache) {
                    response(cache[term]);
                    return
                }
                $.getJSON(app_base_url + "index.php/ajax/get_airport_transfer_code_list", request, function (data, status, xhr) {

                    cache[term] = data;
                    response(cache[term])

                })
            }
        },
        minLength: 0,
        autoFocus: true,
        select: function (event, ui) {
            var label = ui.item.label;
            var category = ui.item.category;
            if (this.id == 'transfer_to') {
                to_airport = ui.item.value
            } else if (this.id == 'transfer_from') {
                from_airport = ui.item.value
            }
            $(this).siblings('.loc_id_holder').val(ui.item.id);
            $(this).siblings('.transfer_type').val(ui.item.transfer_type);
            auto_focus_input(this.id)
            auto_focus_input(this.transfer_type)
        },
        change: function (ev, ui) {
            if (!ui.item) {
                $(this).val("")
            }
        }
    }).bind('focus', function () {
        $(this).catcomplete("search")
    }).catcomplete("instance")._renderItem = function (ul, item) {
        var auto_suggest_value = highlight_search_text(this.term.trim(), item.value, item.label);
        var top = 'Top Searches';
        return $("<li class='custom-auto-complete'>").append('<a>' + auto_suggest_value + '</a>').appendTo(ul)
    };
    $(".departtransfer").catcomplete("instance")._renderItem = function (ul, item) {
        var auto_suggest_value = highlight_search_text(this.term.trim(), item.value, item.label);
        return $("<li class='custom-auto-complete'>").append('<a>' + auto_suggest_value + '</a>').appendTo(ul)
    };
    $('#flight_datepicker2, #OWT_adult, #OWT_child, #OWT_infant, #class, #carrier').change(function () {
        auto_focus_input(this.id)
    });

    total_pax_count('trasfer');
    

    /*show age drodown on click of plus*/
    $('.plusValue').click(function () {
        var id = $(this).siblings("input").attr('id');
        if (id == "OWT_transfer_child") {
            var idval = '#transfer_child_ageId';
            var classVal = '.transfer_child_ageId';
        }
        if (id == "OWT_transfer_adult") {
            var idval = '#transfer_adult_ageId';
            var classVal = '.transfer_adult_ageId';
        }

        var value = $(this).siblings("input").val();
        // added 
        $(idval + value).removeClass('hide');
        $(classVal + value).removeClass('hide');
        $(idval + value).prop("disabled", false);
        var value = parseInt(value) + 1; // not useful
    });

    /*hide age drodown on click of minus*/
    $('.minusValue').click(function () {
        var value = $(this).siblings("input").val();
        value = parseInt(value) + 1; // useful
        var id = $(this).siblings("input").attr('id');
        if (id == "OWT_transfer_child") {
            var idval = '#transfer_child_ageId';
            var classVal = '.transfer_child_ageId';
        }
        if (id == "OWT_transfer_adult") {
            var idval = '#transfer_adult_ageId';
            var classVal = '.transfer_adult_ageId';
        }
        var prevVal = parseInt(value);
        if (id == "OWT_transfer_adult") {
            if (prevVal != 1 && id == "OWT_transfer_adult")
            {
                prevVal = prevVal;
            } else {
                prevVal = prevVal + 1;
            }
        }
        if (id == "OWT_transfer_child") {
            if (prevVal != 1 && id == "OWT_transfer_child")
            {
                prevVal = prevVal;
            } else {
                prevVal = prevVal;
            }
        }

        $(idval + prevVal).addClass('hide');
        $(classVal + prevVal).addClass('hide');

        $(idval + prevVal).prop("disabled", true);

    });

    //datetime picker
    // $("#transfer_datepicker1").change(function () {
    //     //manage date validation
    //     auto_set_date_time($("#transfer_datepicker1").val(), "transfer_datepicker2", 'minDate');
    // });
    // //if second date is already set then dont run
    // if ($("#transfer_datepicker2").val() == '') {
    //     auto_set_date_time($("#transfer_datepicker1").val(), "transfer_datepicker2", 'minDate');
    // }

});


function show_alert_content(content, container) {
    if (container == '') {
        container = '.alert-danger'
    }
    $(container).text(content);
    if (content.length > 0) {
        $(container).removeClass('hide')
    } else {
        $(container).addClass('hide')
    }
}

function manage_infant_count(pax_type) {
    var _content = '';
    var adult_count = parseInt($('#OWT_adult').val().trim());
    var infant_count = parseInt($('#OWT_infant').val().trim());
    if (pax_type == 'infant' && infant_count > 0) {
        var temp_infant_count = (infant_count - 1);
        if (temp_infant_count >= adult_count) {
            $('#OWT_infant').val(temp_infant_count);
            $('#OWT_infant').parent('.infant_count_div').find('button[data-type=plus]').attr('disabled', 'disabled');
            _content = '1 Infant Per Adult Allowed'
        }
    }
    if (pax_type == 'adult' && infant_count > 0) {
        if (infant_count > adult_count) {
            $('#OWT_infant').val(0)
        }
        $('#OWT_infant').parent('.infant_count_div').find('button[data-type=plus]').removeAttr('disabled')
    }
    show_alert_content(_content)



}