$(document).ready(function() {
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
    var cache = {};
    var from_station = $('#bus-station-from').val();
    var to_station = $('#bus-station-to').val();
    $(".bus-station").catcomplete({
        open: function(event, ui) {
            $('.ui-autocomplete').off('menufocus hover mouseover mouseenter');
        },
        source: function(request, response) {
            var term = request.term;
            if (term in cache) {
                response(cache[term]);
                return
            }
            $.getJSON(app_base_url + "index.php/ajax/bus_stations", request, function(data, status, xhr) {
                cache[term] = data;
                response(data)
            })
        },
        minLength: 0,
        autoFocus: false,
        select: function(event, ui) {
            var label = ui.item.label;
            var category = ui.item.category;
            if (this.id == 'bus-station-from') {
                from_station = ui.item.value
            } else if (this.id == 'bus-station-to') {
                to_station = ui.item.value
            }
            $(this).siblings('.loc_id_holder').val(ui.item.id);
            auto_focus_input(this.id)
        },
        change: function(ev, ui) {
            if (!ui.item) {
                $(this).val("")
            }
        }
    }).bind('focus', function() {
        $(this).catcomplete("search")
    }).catcomplete("instance")._renderItem = function(ul, item) {
        var auto_suggest_value = (this.term.trim(), item.value, item.label);
        return $("<li class='custom-auto-complete'>").append('<a>' + auto_suggest_value + '</a>').appendTo(ul)
    };
    $("#bus-station-to").catcomplete("instance")._renderItem = function(ul, item) {
        var auto_suggest_value = highlight_search_text(this.term.trim(), item.value, item.label);
        return $("<li class='custom-auto-complete'>").append('<a>' + auto_suggest_value + '</a>').appendTo(ul)
    };
    $('#bus-station-from, #bus-station-to, #bus-date-1').change(function() {
        auto_focus_input(this.id)
    });
    $('#bus-form-submit').click(function(e) {
        if ($('#bus-station-from').val() == $('#bus-station-to').val()) {
            e.preventDefault();
            show_alert_content('From location and To location can not be same.', '#bus-alert-box');
            return ''
        }
    })
});

function set_bus_cookie_data() {
    var s_params = $('#bus_form').serialize().trim();
    setCookie('bus_search', s_params, 100)
}