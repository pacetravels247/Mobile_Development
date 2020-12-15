$(document).ready(function() {
    var max_rooms = 3;
    var min_rooms = 1;
    var max_childs = 2;
    $('.add_rooms').on('click', function(e) {
        e.preventDefault();
        var _visible_rooms = parseInt($('#room-count').val());
        _visible_rooms = _visible_rooms + 1;
        toggle_add_remove_rooms(_visible_rooms);
        if (_visible_rooms <= max_rooms) {
            $('#room-count').val(_visible_rooms);
            for (var i = 1; i <= (_visible_rooms); i++) {
                $('#room-wrapper-' + i).show()
            }
            validate_rooms(_visible_rooms)
        }
        total_pax_summary()
    });
    $('.remove_rooms').on('click', function(e) {
        e.preventDefault();
        var _visible_rooms = parseInt($('#room-count').val());
        toggle_add_remove_rooms((_visible_rooms - 1));
        if (_visible_rooms > min_rooms) {
            $('#room-wrapper-' + _visible_rooms).hide();
            _visible_rooms = _visible_rooms - 1;
            $('#room-count').val(_visible_rooms);
            validate_rooms(_visible_rooms)
        }
        total_pax_summary()
    });

    function toggle_add_remove_rooms(current_rooms) {
        if (current_rooms >= max_rooms) {
            $('.add_rooms').hide()
        } else {
            $('.add_rooms').show()
        }
        if (current_rooms <= min_rooms) {
            $('.remove_rooms').hide()
        } else {
            $('.remove_rooms').show()
        }
    }

    function validate_rooms(room) {
        for (var i = (parseInt(room) + 1); i <= max_rooms; i++) {
            $('input, select', $('#room-wrapper-' + i)).attr('disabled', 'disabled')
        }
        for (var i = (parseInt(room)); i >= min_rooms; i--) {
            $('input, select', $('#room-wrapper-' + i)).removeAttr('disabled')
        }
    }
    var pri_visible_room = $('#pri_visible_room').val();
    toggle_add_remove_rooms(pri_visible_room);
    validate_rooms(pri_visible_room);
    total_pax_summary();
    $('#hotel_search .input-number').on('change blur', function() {
        total_pax_summary()
    });
    $('#hotel_search input[name="child[]"]').on('change', function() {
        var current_rooms = $(this).closest('.oneroom');
        var child_count = parseInt(this.value);
        if (child_count < 1) {
            $('.chilagediv', current_rooms).hide()
        } else {
            $('.chilagediv', current_rooms).show();
           // console.log(child_count);
            for (var j = 1; j <= child_count; j++) {
                $('.child-age-wrapper-' + j, current_rooms).show()
            }
            for (var j = (child_count + 1); j <= max_childs; j++) {
                $('.child-age-wrapper-' + j, current_rooms).hide()
            }
        }
    });

    function total_pax_summary() {
        var total_rooms = $('#room-count').val();
        var total_adults = 0;
        
        /*start*/
        for (var i = 0; i <parseInt(total_rooms); i++) {
            var room_adult_count = $("#adult_text_"+i).val();
            if(parseInt(room_adult_count)==0){
                $("#adult_text_"+i).val(1);
            }
        }
        /*End*/
        $('#hotel_search [name="adult[]"]').not(':disabled').each(function() {
            total_adults = total_adults + parseInt(this.value)
        });
        var total_child = 0;
        $('#hotel_search [name="child[]"]').not(':disabled').each(function() {
            total_child = total_child + parseInt(this.value)
        });
        var room_summary = '';
        room_summary += total_adults;
        if (total_adults > 1) {
            room_summary += ' Adults,'
        } else {
            room_summary += ' Adult,'
        }
        if (total_child > 0) {
            room_summary += total_child;
            if (total_child > 1) {
                room_summary += ' Children,'
            } else {
                room_summary += ' Child,'
            }
        }
        room_summary += total_rooms;
        if (total_rooms > 1) {
            room_summary += ' Rooms'
        } else {
            room_summary += ' Room'
        }
        $('#hotel-pax-summary').text(room_summary)
    }
});
$(document).ready(function() {
    function set_hotel_cookie_data() {
        var s_params = $('#hotel_search').serialize().trim();
        setCookie('hotel_search', s_params, 100)
    }
    var cache = {};
    $(".hotel_city").catcomplete({
        open: function(event, ui) {
        $('.ui-autocomplete').off('menufocus hover mouseover mouseenter');
    },

        source: function(request, response) {
            var term = request.term;
            if (term in cache) {
                response(cache[term]);
                return
            }
             console.log("hotel_city");
            $.getJSON(app_base_url + "index.php/ajax/get_hotel_city_list", request, function(data, status, xhr) {
                cache[term] = data;
                response(data)
            })
        },
        minLength: 0,
        autoFocus: false,
        select: function(event, ui) {
            var label = ui.item.label;
            var category = ui.item.category;
            $(this).siblings('.loc_id_holder').val(ui.item.id);
            $(this).siblings('.rz_city_id_holder').val(ui.item.rz_city);
            $(this).siblings('.rz_country_id_holder').val(ui.item.rz_country);
            $('#hotel_checkin').focus()
        },
        change: function(ev, ui) {
            if (!ui.item) {
                $(this).val("")
            }
        }
    }).bind('focus', function() {
        $(this).catcomplete("search")
    }).catcomplete("instance")._renderItem = function(ul, item) {
        var auto_suggest_value = highlight_search_text(this.term.trim(), item.value, item.label);
        var hotel_count = '';
        var count = parseInt(item.count);
        if (count > 0) {
            var h_lab = '';
            if (count > 1) {
                h_lab = 'Hotels'
            } else {
                h_lab = 'Hotel'
            }
            hotel_count = '<span class="hotel_cnt">(' + parseInt(item.count) + ' ' + h_lab + ')</span>'
        }
        return $("<li class='custom-auto-complete'>").append('<a> <span class="fal fa-map-marker-alt"></span> ' + auto_suggest_value + ' ' + hotel_count + '</a>').appendTo(ul)
    };
    $('#hotel_checkin, #hotel_checkout').on('change', function(e) {
        e.preventDefault();
        var from_date = $('#hotel_checkin').val();
        var to_date = $('#hotel_checkout').val();
        if (from_date != '' && to_date != '') {
            var diffDays = parseInt(get_day_difference($('#hotel_checkin').datepicker('getDate'), $('#hotel_checkout').datepicker('getDate')));
            if (parseInt(diffDays) > 10 || diffDays < -10) {
                diffDays = 10;
                $('#hotel_checkout').val(add_days_to_date(from_date, diffDays))
            } else if (diffDays < 0) {
                diffDays = diffDays * -1;
                $('#hotel_checkout').val(add_days_to_date(from_date, diffDays))
            } else if (diffDays == 0) {
                diffDays = 1;
                $('#hotel_checkout').val(add_days_to_date(from_date, diffDays))
            }
            $('#no_of_nights').val(diffDays)
        }
    });

    function add_days_to_date(from_date, number_of_days) {
        from_date = from_date.split('-');
        var to_date = new Date(from_date[2], parseInt(from_date[1]) - 1, (parseInt(from_date[0]) + number_of_days));
        month = '' + (to_date.getMonth() + 1), day = '' + to_date.getDate(), year = to_date.getFullYear();
        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;
        return [day, month, year].join('-')
    }
    $('#no_of_nights').on('change', function() {
        var from_date = $('#hotel_checkin').val();
        var number_of_nights = parseInt(this.value);
        if (from_date != '') {
            var to_date = add_days_to_date(from_date, number_of_nights);
            $('#hotel_checkout').val(to_date)
        }
    })
});