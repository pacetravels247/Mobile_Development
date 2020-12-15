var flight_adult_child_max_count = 9;
var cache = {};
$(document).ready(function() {

    var country = ["Australia", "Bangladesh", "Denmark", "Hong Kong", "Indonesia", "Netherlands", "New Zealand", "South Africa"];
    // $("#airline_select").select2({
    //   data: country
    // });

	validate_roundway_dates();
    function set_flight_cookie_data() {
        var s_params = $('#flight_form').serialize().trim();
        setCookie('flight_search', s_params, 100)
    }
    $('#from, #to, [name="trip_type"]').on('change, blur', function() {
        is_domestic_oneway_search()
    });
    $("#flight_form").attr('autocomplete', 'off'); 
    function is_domestic_oneway_search() {}
    $('#flight_fare_calendar').on('click', function(e) {
        e.preventDefault();
        var data = {};
        data['from'] = $('#from').val();
        data['to'] = $('#to').val();
        data['depature'] = $('#flight_datepicker1').val();
        data['trip_type'] = 'oneway';
        data['adult'] = $('#OWT_adult').val();
        var url = app_base_url + 'index.php/flight/pre_calendar_fare_search?' + $.param(data);
        window.open(url)
    });
    //On Submit
    $('#flight-form-submit').on('click', function(e) {
    	var trip_type = $('[name="trip_type"]:checked').val();
       
    	if(trip_type == 'oneway' || trip_type == 'circle') {//Oneway/roundWay
	        var _from_loc = $('#flight_form #from').val();
	        var _to_loc = $('#flight_form #to').val();
            var depature = $('#flight_form #flight_datepicker1').val();
            var return_date = $('#flight_form #flight_datepicker2').val();    
            if(_from_loc== ''){
                show_alert_content('From location cannot be empty.', '#flight-alert-box');
                e.preventDefault();
                return ''
            }
             if(_to_loc== ''){
                show_alert_content('To location cannot be empty.', '#flight-alert-box');
                e.preventDefault();
                return ''
            }
	        if (_from_loc!= '' && _to_loc != '' && _from_loc == _to_loc) {
	            show_alert_content('From location and To location cannot be same.', '#flight-alert-box');
	            e.preventDefault();
	            return ''
	        }
            if(depature == ''){
                show_alert_content('Departure date cannot be empty.', '#flight-alert-box');
                e.preventDefault();
                return ''
            }
            if(trip_type == 'circle'){
                if(return_date == ''){
                    show_alert_content('Return date cannot be empty.', '#flight-alert-box');
                    e.preventDefault();
                    return ''
                }
            }
	        //Disable MultiCity Fieldset
	        $('input,checkbox,radio,select', "#multi_way_fieldset").attr('disabled', 'disabled');
        } else{//MultiWay
            var _from_loc1 = $('#m_from1').val();
            var _to_loc1 = $('#m_to1').val();
            var _from_loc2 = $('#m_from2').val();
            var _to_loc2 = $('#m_to2').val();
            
            if($('#multi_city_container_3:visible').length > 0)
            {
                var _from_loc3 = $('#m_from3').val();
                var _to_loc3 = $('#m_to3').val();
                var _departure3 = $('#m_flight_datepicker3').val();
            }
            else{
                var _from_loc3 = 'val';
                var _to_loc3 = 'vals';
                var _departure3 = 'val';
            }
            if($('#multi_city_container_4:visible').length > 0)
            {
                var _from_loc4 = $('#m_from4').val();
                var _to_loc4 = $('#m_to4').val();
                var _departure4 = $('#m_flight_datepicker4').val();
            }
            else{
                var _from_loc4 = 'val';
                var _to_loc4 = 'vals';
                var _departure4 = 'val';
            }
            if($('#multi_city_container_5:visible').length > 0)
            {
                var _from_loc5 = $('#m_from4').val();
                var _to_loc5 = $('#m_to4').val();
                var _departure5 = $('#m_flight_datepicker5').val();
            }
            else{
                var _from_loc5 = 'val';
                var _to_loc5 = 'vals';
                var _departure5 = 'val';
            }


            var _departure1 = $('#m_flight_datepicker1').val();
            var _departure2 = $('#m_flight_datepicker2').val();
            
            if(_from_loc1 == '' || _from_loc2 =='' || _from_loc3 == '' || _from_loc4 == '' || _from_loc5 == ''){
                 show_alert_content('From location can not be empty.', '#flight-alert-box');
                e.preventDefault();
                return ''
            }
             if(_to_loc1 == '' || _to_loc2 =='' || _to_loc3 == '' || _to_loc4 == '' || _to_loc5 == ''){
                show_alert_content('To location can not be empty.', '#flight-alert-box');
                e.preventDefault();
                return ''
            }
            if ((_from_loc1 == _to_loc1) || (_from_loc2 == _to_loc2) || (_from_loc3 == _to_loc3) || (_from_loc4 == _to_loc4) || (_from_loc5 == _to_loc5)) {
                show_alert_content('From location and To location can not be same.', '#flight-alert-box');
                e.preventDefault();
                return ''
            }
            if(_departure1 == '' || _departure2 =='' || _departure3 == '' || _departure4 == '' || _departure5 == ''){
                show_alert_content('Departure date can not be empty.', '#flight-alert-box');
                e.preventDefault();
                return ''
            }
        	$('input,checkbox,radio,select', "#onw_rndw_fieldset").attr('disabled', 'disabled');
        	//Disable Oneway/RoundWay Fieldset
        }
        var _adult = parseInt($('#OWT_adult').val());
        var _child = parseInt($('#OWT_child').val());
        var _infant = parseInt($('#OWT_infant').val());
        var _content = '';
        if (_infant > 0 && _adult < _infant) {
            e.preventDefault();
            _content = '1 Infant Per Adult Allowed'
        }
        show_alert_content(_content)
    });
    $('[name="trip_type"]').on('change', function() {
        handle_active_trip_type(this.value)
    });

    function handle_active_trip_type(_active_trip_type) 
    {
    	toggle_add_city_button();
        if (_active_trip_type == 'oneway') {
            $('#flight_datepicker2').attr('disabled', true).removeAttr('required').closest('.date-wrapper').animate({
                'opacity': '.3'
            });
            $('#flight_datepicker2').val('');
            if ($('#onew-trp').parent('label.wament').hasClass('active') == false) {
                $('#onew-trp').parent('label.wament').addClass('active')
            }
            if ($('#rnd-trp').parent('label.wament').hasClass('active') == true) {
                $('#rnd-trp').parent('label.wament').removeClass('active')
            }
            if ($('#multi-trp').parent('label.wament').hasClass('active') == true) {
                $('#multi-trp').parent('label.wament').removeClass('active')
            }
            $('#onw_rndw_fieldset').show();
        	$('#multi_way_fieldset').hide();
        } else if (_active_trip_type == 'circle') {
            if ($('#rnd-trp').parent('label.wament').hasClass('active') == false) {
                $('#rnd-trp').parent('label.wament').addClass('active')
            }
            if ($('#onew-trp').parent('label.wament').hasClass('active') == true) {
                $('#onew-trp').parent('label.wament').removeClass('active')
            }
            if ($('#multi-trp').parent('label.wament').hasClass('active') == true) {
                $('#multi-trp').parent('label.wament').removeClass('active')
            }
            $('#flight_datepicker2').removeAttr('disabled').attr('required', 'required').closest('.date-wrapper').animate({
                'opacity': '1'
            }).focus();
            $('#onw_rndw_fieldset').show();
        	$('#multi_way_fieldset').hide();
        } else if (_active_trip_type == 'multicity') {
        	if ($('#multi-trp').parent('label.wament').hasClass('active') == false) {
                $('#multi-trp').parent('label.wament').addClass('active')
            }
            if ($('#onew-trp').parent('label.wament').hasClass('active') == true) {
                $('#onew-trp').parent('label.wament').removeClass('active')
            }
            if ($('#rnd-trp').parent('label.wament').hasClass('active') == true) {
                $('#rnd-trp').parent('label.wament').removeClass('active')
            }
            //Fieldset
            $('#onw_rndw_fieldset').hide();
        	$('#multi_way_fieldset').show();
           
        }
    }
    handle_active_trip_type($('[name="trip_type"]:checked').val());
    
    var from_airport = $('#from').val();
    var to_airport = $('#to').val();
    $(".fromflight, .departflight").catcomplete({
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
    $(".departflight").catcomplete("instance")._renderItem = function(ul, item) {
        var auto_suggest_value = highlight_search_text(this.term.trim(), item.value, item.label, item.country_code);
        return $("<li class='custom-auto-complete'>").append('<a><img class="flag_image" src="' + item.country_code + '">' + auto_suggest_value + '</a>').appendTo(ul)
    };
    $('#flight_datepicker2, #OWT_adult, #OWT_child, #OWT_infant, #class, #carrier').change(function() {
        auto_focus_input(this.id)
    });
    // total_pax_count('flight_form');
    $('.choose_airline_class').click(function() {
        var airline_class_name = $(this).text();
        var airline_class = $(this).data('airline_class');
        $('#class').val(airline_class);
        if (airline_class == '') {
            airline_class_name = 'Class'
        }
        $('#choosen_airline_class').empty().text(airline_class_name);
        if ($('.class_advance_div').hasClass('fadeinn') == true) {
            $('.class_advance_div').removeClass('fadeinn')
        }
    });
    $('.choose_preferred_airline').click(function() {
        var airline_name = $(this).text();
        var airline_code = $(this).data('airline_code');
        $('#carrier').val(airline_code);
        if (airline_name == '') {
            airline_name = 'Preferred Airline'
        }
        $('#choosen_preferred_airline').empty().text(airline_name);
        if ($('.preferred_airlines_advance_div').hasClass('fadeinn') == true) {
            $('.preferred_airlines_advance_div').removeClass('fadeinn')
        }
    })
    //After loading document ,validating the Multicity Segment dates
    validate_segment_dates();
    
});
function show_alert_content(content, container) {
	 if (typeof container == "undefined") {
	        container = '.alert-content'
	    }
	    $(container).html(content);
	    if (content.length > 0) {
	        $('.alert-wrapper').removeClass('hide')
	    } else {
	        $('.alert-wrapper').addClass('hide')
	    }
}
function manage_infant_count(pax_type) 
{
	var _content = '';
    var adult_count = parseInt($('#OWT_adult').val().trim());
    var child_count = parseInt($('#OWT_child').val().trim());
    var infant_count = parseInt($('#OWT_infant').val().trim());
    var total_adult_child_count = (adult_count+child_count);
    if (pax_type == 'infant' && infant_count > 0) {
        var temp_infant_count = (infant_count - 1);
        if (temp_infant_count >= adult_count) {
            $('#OWT_infant').val(temp_infant_count);
            $('#OWT_infant').parent('.infant_count_div').find('button[data-type=plus]').attr('disabled', 'disabled');
            _content = '1 Infant Per Adult Allowed'
        }
    }
    if (pax_type == 'adult') {//Adult
    		var temp_adult_child_count = (total_adult_child_count	 - 1);
    		if (temp_adult_child_count >= flight_adult_child_max_count) {
                $('#OWT_adult').val(adult_count-1);
                $('#OWT_adult').parent('.adult_count_div').find('button[data-type=plus]').attr('disabled', 'disabled');
                _content = '<small>Max 9 Passenger(Adult+Child) Allowed</small>'
            } else {
            	$('#OWT_adult').parent('.adult_count_div').find('button[data-type=plus]').removeAttr('disabled');
            	$('#OWT_child').parent('.child_count_div').find('button[data-type=plus]').removeAttr('disabled');
            }
    	//Infant
        if (infant_count > 0 && infant_count > adult_count) {
            $('#OWT_infant').val(0)
        }
        $('#OWT_infant').parent('.infant_count_div').find('button[data-type=plus]').removeAttr('disabled')
    }else if (pax_type == 'child') {//Child
    	var temp_adult_child_count = (total_adult_child_count	 - 1);
		if (temp_adult_child_count >= flight_adult_child_max_count) {
            $('#OWT_child').val(child_count-1);
            $('#OWT_child').parent('.child_count_div').find('button[data-type=plus]').attr('disabled', 'disabled');
            _content = '<small>Max 9 Passenger(Adult+Child) Allowed</small>'
        } else {
        	$('#OWT_adult').parent('.adult_count_div').find('button[data-type=plus]').removeAttr('disabled');
        	$('#OWT_child').parent('.child_count_div').find('button[data-type=plus]').removeAttr('disabled');
        }
    }
    show_alert_content(_content)
}
//Add cties for Multi city
var max_multicity_segments = $('#max_multicity_segments').val();
var min_multicity_segments = 2;
var pre_segment_count = parseInt($('#multicity_segment_count').val());
validate_multicity_segments(pre_segment_count);
toggle_add_remove_segments(pre_segment_count);

$('#add_city').click(function(e){
	e.preventDefault();
	var segment_count = parseInt($('#multicity_segment_count').val());
	segment_count = segment_count + 1;
	toggle_add_remove_segments(segment_count);
    if (segment_count <= max_multicity_segments) {
    	$('.inactive_segment').first().removeClass('inactive_segment');
        $('#multicity_segment_count').val(segment_count);
        for (var i = 1; i <= (segment_count); i++) {
        	if($('#multi_city_container_'+i).hasClass('inactive_segment') == false) {
        		$('#multi_city_container_' + i).show();
        	}
        }
        validate_multicity_segments(segment_count);
        validate_segment_dates();
    }
    //Auto Fill the next departure city
    $('.m_depcity').each(function(){
    	if($(this).is(":visible") == true && $(this).val() == '') {
    		var seg_obj = $(this).closest('.multi_city_container').prev('.multi_city_container');
    		var depcity = seg_obj.find('.m_arrcity').val();
    		var loc_id_holder = seg_obj.find("input[name='to_loc_id[]']").val();
    		if(depcity !='') {
    			$(this).val(depcity);
    			$(this).siblings('.loc_id_holder').val(loc_id_holder);
    		}
    	}
    });
});
$('.remove_city').click(function(e){
	e.preventDefault();
	var segment_count = parseInt($('#multicity_segment_count').val());
	toggle_add_remove_segments((segment_count - 1));
    if (segment_count > min_multicity_segments) {
        //$('#multi_city_container_' + segment_count).hide();
    	var seg_object = $(this).closest('.multi_city_container');
    	seg_object.hide();
    	$('input, select', seg_object).val('').attr('disabled', 'disabled').addClass('inactive_segment');
    	//$('input, select', seg_object).attr('disabled', 'disabled').addClass('inactive_segment');
    	seg_object.insertAfter($('.multi_city_container', '#multi_way_fieldset').last());
        segment_count = segment_count - 1;
        $('#multicity_segment_count').val(segment_count);
        validate_multicity_segments(segment_count);
        validate_segment_dates();
    }
	
});
// for($i=1; $i<10;$i++){
//         $('.datepicker_new'+$i).on('click', function(e) {
//         var item_value = $(this).attr('iditem');
//         $('#'+item_value).focus();
         
//      });
// }


//Validaing MultiCity Departure Date
$('.m_depature_date').change(function(){
	validate_segment_dates();
});
//Validaing Roundway Departure Date
$('#flight_datepicker1, #flight_datepicker2').change(function(){
	validate_roundway_dates();
});

function validate_multicity_segments(segment_count)
{
	for (var i = (parseInt(segment_count) + 1); i <= max_multicity_segments; i++) {
		
        //$('input, select', $('#multi_city_container_' + i)).attr('disabled', 'disabled');
		if($('#multi_city_container_' + i).is(":visible") == false) {
			$('input, select', $('#multi_city_container_' + i)).attr('disabled', 'disabled');
		}
    }
    for (var i = (parseInt(segment_count)); i >= min_multicity_segments; i--) {
        //$('input, select', $('#multi_city_container_' + i)).removeAttr('disabled');
    	if($('#multi_city_container_' + i).is(":visible") == true) {
    		$('input, select', $('#multi_city_container_' + i)).removeAttr('disabled');
    	}
    }
}
function toggle_add_remove_segments(current_segments) {
    if (current_segments >= max_multicity_segments) {
        $('#add_city').hide();
    } else {
    	toggle_add_city_button();
    }
}
function toggle_add_city_button()
{
	if($('[name="trip_type"]:checked').val() == 'multicity') {
		$('#add_city').show();
	} else {
		$('#add_city').hide();
	}
}
//Multiway Dates
function validate_segment_dates()
{
	/*for (var i = 1; i <= max_multicity_segments; i++) {
		auto_set_dates($("#m_flight_datepicker"+i).datepicker('getDate'), "m_flight_datepicker"+(i+1), 'minDate', 0);
    }*/
	$('.multi_city_container').each(function(){
		var current_departure_id = $('.m_depature_date', this).attr('id');
		var next_departure_id = $(this).next('.multi_city_container').find('.m_depature_date').attr('id');
		auto_set_dates($("#"+current_departure_id).datepicker('getDate'), next_departure_id, 'minDate', 0);
	});
}
//Round Way Dates
function validate_roundway_dates()
{
	auto_set_dates($("#flight_datepicker1").datepicker('getDate'), "flight_datepicker2", 'minDate', 0);
   
}

function catcomplete_new(val){
$(val).catcomplete({
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
}