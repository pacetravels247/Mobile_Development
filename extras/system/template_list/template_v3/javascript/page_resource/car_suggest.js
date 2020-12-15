$(document).on('click', '.custom-auto-complete', function() {});
$(document).ready(function() {
    function set_flight_cookie_data() {
        var s_params = $('#flight_form').serialize().trim();
        setCookie('flight_search', s_params, 100)
    }
    $('#car_from, #car_to, [name="car_type"]').on('change, blur', function() {
        is_domestic_oneway_search()
    });

    function is_domestic_oneway_search() {}
    $('[name="car_type"]').on('change', function() {
        handle_active_car_type(this.value)
    });

    function handle_active_car_type(_active_car_type) {
        if (_active_car_type == 'oneway') {
            $('#car_datepicker2').attr('disabled', true).removeAttr('required').closest('.date-wrapper').animate({
                'opacity': '.3'
            });
            $('#car_datepicker2').val('');
            if ($('#onew-car-trip').parent('label.wament').hasClass('active') == false) {
                $('#onew-car-trip').parent('label.wament').addClass('active')
            }
            if ($('#rnd-car-trip').parent('label.wament').hasClass('active') == true) {
                $('#rnd-car-trip').parent('label.wament').removeClass('active')
            }
        } else if (_active_car_type == 'circle') {
            if ($('#rnd-car-trip').parent('label.wament').hasClass('active') == false) {
                $('#rnd-car-trip').parent('label.wament').addClass('active')
            }
            if ($('#onew-car-trip').parent('label.wament').hasClass('active') == true) {
                $('#onew-car-trip').parent('label.wament').removeClass('active')
            }
            $('#car_datepicker2').removeAttr('disabled').attr('required', 'required').closest('.date-wrapper').animate({
                'opacity': '1'
            }).focus()
        } else if (_active_car_type == 'multicity') {}
    }
    handle_active_car_type($('[name="car_type"]:checked').val());
    var cache = {};
    var car_from = $('#car_from').val();
    var car_to = $('#car_to').val();
    $(".fromcar, .departcar").catcomplete({
        source: function(request, response) {
            var term = request.term;
            if (term in cache) {
                response(cache[term]);
                return
            } else {
                $.getJSON(app_base_url + "index.php/ajax/get_airport_city_list", request, function(data, status, xhr) {
                    if ($.isEmptyObject(data) == true && $.isEmptyObject(cache[""]) == false) {
                        data = cache[""]
                    } else {
                        cache[term] = data;
                        response(cache[term])
                    }
                })
            }
        },
        minLength: 0,
        autoFocus: true,
        select: function(event, ui) {
           
            var label = ui.item.label;
            var category = ui.item.category;
            if (this.id == 'car_to') {
                to_airport = ui.item.value;
            } else if (this.id == 'car_from') {
                from_airport = ui.item.value;
               
                $('#car_from_loc_id').val(ui.item.id);
                $('#car_to').val(from_airport);
                $('#car_to_loc_id').val(ui.item.id);
                $('#car_from_loc_code').val(ui.item.airport_code);
                $('#car_to_loc_code').val(ui.item.airport_code);
            }
            $(this).siblings('.loc_id_holder').val(ui.item.id);
            $(this).siblings('.loc_code_holder').val(ui.item.airport_code);
            auto_focus_input(this.id)
            auto_focus_input(this.car_type)
        },
        change: function(ev, ui) {
            if (!ui.item) {
                $(this).val("")
            }
        }
    }).catcomplete("instance")._renderItem = function(ul, item) {
        var auto_suggest_value = highlight_search_text(this.term.trim(), item.value, item.label);
        var top = 'Top Searches';
        return $("<li class='custom-auto-complete'>").append('<a>' + auto_suggest_value + '</a>').appendTo(ul)
    };
    $(".departcar").catcomplete("instance")._renderItem = function(ul, item) {
        var auto_suggest_value = highlight_search_text(this.term.trim(), item.value, item.label);
        return $("<li class='custom-auto-complete'>").append('<a>' + auto_suggest_value + '</a>').appendTo(ul)
    };
    $('#car_datepicker2').change(function() {
        auto_focus_input(this.id)
    });
    //Validaing Roundway Departure Date
    $('#car_datepicker1, #car_datepicker2').change(function(){
        validate_roundway_dates();
    });
    //Round Way Dates
    function validate_roundway_dates()
    {
        auto_set_dates($("#car_datepicker1").datepicker('getDate'), "car_datepicker2", 'minDate', 0);
    }
    

    function toValidDate(datestring) {
        return datestring.replace(/(\d{2})(-)(\d{2})/, "$3$2$1");
    }
   
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


$(function() {
    $('#trasfer').submit(function() {
        if ($('#car_datepicker1').val() == '') {
            $('#car_datepicker1').addClass('invalid-ip');
            $('#car_datepicker1').datepicker('show');
            return false;
        }
        if ($('#car_datepicker2').val() == '') {
            $('#car_datepicker2').addClass('invalid-ip');
            $('#car_datepicker2').datepicker('show');
            return false;
        }
        if ($('#car_datepicker1').val() == $('#car_datepicker2').val()) {
            if ($('#depature_time').val() >= $('#return_time').val()) {
                $('.alert').hide();
                $('#return_time').css('border-color', '#bf7070')
                $('.custom_divclass').before('<div class="alert alert-danger"> <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button> <strong>Wrong!</strong> Return time ... </div>');;
                return false;
            }
        }
    });
});