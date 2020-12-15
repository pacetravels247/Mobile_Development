$(".fromflight2").catcomplete({
        open: function(event, ui) {
        $('.ui-autocomplete').off('menufocus hover mouseover mouseenter');
    },
        source: function(request, response) {
            var term = request.term;
            if (term in cache) {
                response(cache[term]);
                return
            } else {
                $.getJSON(app_base_url + "index.php/ajax/get_airport_code_list", request, function(data, status, xhr) {
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
        autoFocus: false,
        select: function(event, ui) {
            var label = ui.item.label;
            var category = ui.item.category;
            if (this.id == 'to') {
                to_airport = ui.item.value
            } else if (this.id == 'from') {
                from_airport = ui.item.value
            }
            $(this).siblings('.loc_id_holder').val(ui.item.id);
            auto_focus_input(this.id)
            //For Multicity-To autofill the next departure city
            if($(this).hasClass('m_arrcity') == true && ui.item.value !='') {
            	var next_depcity_id = $(this).closest('.multi_city_container').next('.multi_city_container').find('.m_depcity').attr('id');
            	if($('#'+next_depcity_id).val() == '') {
	            	$('#'+next_depcity_id).val(ui.item.value);
	            	$('#'+next_depcity_id).siblings('.loc_id_holder').val(ui.item.id);
            	}
            }
        },
        change: function(ev, ui) {
            if (!ui.item) {
                $(this).val("")
            }
        }
    }).bind('focus', function() {
        $(this).catcomplete("search")
    }).catcomplete("instance")._renderItem = function(ul, item) { 
        var auto_suggest_value = highlight_search_text(this.term.trim(), item.value, item.label, item.country_code);
        var top = 'Top Searches';
        return $("<li class='custom-auto-complete'>").append('<a><img class="flag_image" src="' + item.country_code + '">' + auto_suggest_value + '</a>').appendTo(ul)
    };