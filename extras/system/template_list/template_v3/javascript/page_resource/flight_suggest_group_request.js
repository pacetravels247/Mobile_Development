var flight_adult_child_max_count = 9;
$(document).ready(function() {
	function set_flight_cookie_data() {
		var s_params = $('#flight_form').serialize().trim();
		setCookie('flight_search', s_params, 100)
	}
	

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
	
	$('#flight-form-submit').on('click', function(e) {
	
    	var trip_type = $('[name="trip_type"]:checked').val();
    	
    	if(trip_type == 'oneway' || trip_type == 'circle' || trip_type == 'round_trip_special') {//Oneway/roundWay
	        var _from_loc = $('#flight_form #from').val();
	        var _to_loc = $('#flight_form #to').val();
	        if (_from_loc == _to_loc) {
	            show_alert_content('From location and To location can not be same.', '#flight-alert-box');
	            e.preventDefault();
	            return ''
	        }
	        //Disable MultiCity Fieldset
	        $('input,checkbox,radio,select', "#multi_way_fieldset").attr('disabled', 'disabled');
        } else{//MultiWay
        	$('input,checkbox,radio,select', "#onw_rndw_fieldset").attr('disabled', 'disabled');
        	//Disable Oneway/RoundWay Fieldset
        }
        var _adult = parseInt($('#OWT_adult').val());
        var _child = parseInt($('#OWT_child').val());
        var _infant = parseInt($('#OWT_infant').val());
        var _content = '';
        if (_infant > 0 && _adult < _infant) {
            e.preventDefault();
            _content = '1 Infant Per Adult Allowed';
        }
        if((_adult+_child) > 9) {
        	e.preventDefault();
			_content = '9 Passengers Max';
        }
        show_alert_content(_content)
    });
	
	/*$('.remove_city').click(function(e){
		e.preventDefault();
		var segment_count = parseInt($('#multicity_segment_count').val());
		console.log(segment_count);
		toggle_add_remove_segments((segment_count - 1));
	    if (segment_count > min_multicity_segments) {
	        //$('#multi_city_container_' + segment_count).hide();
	    	var seg_object = $(this).closest('.multi_city_container');
	    	seg_object.hide();
	    	$('input, select', seg_object).val('').attr('disabled', 'disabled').addClass('inactive_segment');
	    	//$('input, select', seg_object).attr('disabled', 'disabled').addClass('inactive_segment');
	    	seg_object.insertAfter($('.multi_city_container', '#multi_way_fieldset').last());
	        segment_count = segment_count - 1;
	        console.log('seg cnt = ' + segment_count);
	        $('#multicity_segment_count').val(segment_count);
	        validate_multicity_segments(segment_count);
	        validate_segment_dates();
	    }
		
	});*/
	
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
	
	
	/*$('#flight-form-submit').on('click', function(e) {
		var _from_loc = $('#flight_form #from').val();
		var _to_loc = $('#flight_form #to').val();
		if (_from_loc == _to_loc) {
			show_alert_content('From location and To location can not be same.', '#flight-alert-box');
			e.preventDefault();
			return ''
		}
		var _adult = parseInt($('#OWT_adult').val());
		var _child = parseInt($('#OWT_child').val());
		var _infant = parseInt($('#OWT_infant').val());
		var _content = '';
		if ((_adult+_child) > 9) {
			e.preventDefault();
			_content = '9 Passengers Max'
		}
		
		if (_infant > 0 && _adult < _infant) {
			e.preventDefault();
			_content = '1 Infant Per Adult Allowed'
		}
		show_alert_content(_content, '#flight-alert-box')
	});*/
	
	
	$('[name="trip_type"]').on('change', function() {
		handle_active_trip_type(this.value)
	});
	$('[name="sector_type"]').on('change', function() {
		handle_active_sector_type(this.value);
		handle_airline_list(this.value);
		console.log(this.value);
		if(this.value == 'domestic') {
			$('.choosen_airline_class_international').addClass('hide');
			$('.choosen_airline_class_domestic').removeClass('hide');
		}else {
			$('.choosen_airline_class_domestic').addClass('hide');
			$('.choosen_airline_class_international').removeClass('hide');
		}

	});
	function handle_active_sector_type(_active_sector_type) {
		//alert(_active_sector_type);
		if (_active_sector_type == 'domestic') {

			if ($('#d_flight').parent('label.wament1').hasClass('active') == false) {
				$('#d_flight').parent('label.wament1').addClass('active')
			}
			if ($('#i_flight').parent('label.wament1').hasClass('active') == true) {
				$('#i_flight').parent('label.wament1').removeClass('active')
			}
		} else if (_active_sector_type == 'international') {
			if ($('#i_flight').parent('label.wament1').hasClass('active') == false) {
				$('#i_flight').parent('label.wament1').addClass('active')
			}
			if ($('#d_flight').parent('label.wament1').hasClass('active') == true) {
				$('#d_flight').parent('label.wament1').removeClass('active')
			}
		}
	}
	function handle_airline_list(_active_sector_type){
		console.log(_active_sector_type);
		
		if (_active_sector_type == 'domestic') {
			$('.choose_cls_a').not('.domestic').hide();
		}
		else{console.log($('.choose_cls_a').not('.domestic'));
			$('.choose_cls_a').not('.domestic').show();
		}
		$('#carrier').val('');
		$('#choosen_preferred_airline').text('Preferred Airline');
	}
	
	function handle_active_trip_type(_active_trip_type) {
		if (_active_trip_type == 'oneway') {
			$('input,checkbox,radio,select', "#onw_rndw_fieldset").attr('enabled', 'enabled');
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
			if ($('#round_trip_special').parent('label.wament').hasClass('active') == true) {
				$('#round_trip_special').parent('label.wament').removeClass('active')
			}
			$('#onw_rndw_fieldset').show();
			$('#multi_way_fieldset').hide();
                        $('#RTAirlineList').hide();
		} else if (_active_trip_type == 'circle') {
			$('input,checkbox,radio,select', "#onw_rndw_fieldset").attr('enabled', 'enabled');
			if ($('#rnd-trp').parent('label.wament').hasClass('active') == false) {
				$('#rnd-trp').parent('label.wament').addClass('active')
			}
			if ($('#onew-trp').parent('label.wament').hasClass('active') == true) {
				$('#onew-trp').parent('label.wament').removeClass('active')
			}
			if ($('#multi-trp').parent('label.wament').hasClass('active') == true) {
				$('#multi-trp').parent('label.wament').removeClass('active')
			}
			if ($('#round_trip_special').parent('label.wament').hasClass('active') == true) {
				$('#round_trip_special').parent('label.wament').removeClass('active')
			}
			$('#flight_datepicker2').removeAttr('disabled').attr('required','required').closest('.date-wrapper').animate({
				'opacity': '1'
			}).focus();
			$('#onw_rndw_fieldset').show();
			$('#multi_way_fieldset').hide();
                        $('#RTAirlineList').hide();
		} else if (_active_trip_type == 'multicity') {
			$('input,checkbox,radio,select', "#multi_way_fieldset").attr('enabled', 'enabled');
			if ($('#multi-trp').parent('label.wament').hasClass('active') == false) {
				$('#multi-trp').parent('label.wament').addClass('active')
			}
			if ($('#onew-trp').parent('label.wament').hasClass('active') == true) {
				$('#onew-trp').parent('label.wament').removeClass('active')
			}
			if ($('#rnd-trp').parent('label.wament').hasClass('active') == true) {
				$('#rnd-trp').parent('label.wament').removeClass('active')
			}
			if ($('#round_trip_special').parent('label.wament').hasClass('active') == true) {
				$('#round_trip_special').parent('label.wament').removeClass('active')
			}
			$('#onw_rndw_fieldset').hide();
			$('#multi_way_fieldset').show();
                          $('#RTAirlineList').hide();
			
		} else if (_active_trip_type == 'round_trip_special') {
                      $('#RTAirlineList').show();
			$('input,checkbox,radio,select', "#onw_rndw_fieldset").attr('enabled', 'enabled');
			if ($('#round_trip_special').parent('label.wament').hasClass('active') == false) {
				$('#round_trip_special').parent('label.wament').addClass('active')
			}
			if ($('#rnd-trp').parent('label.wament').hasClass('active') == true) {
				$('#rnd-trp').parent('label.wament').removeClass('active')
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
                      
		}
		var segment_count = parseInt($('#multicity_segment_count').val())
		toggle_add_remove_segments(segment_count);
	}
	
	handle_active_trip_type($('[name="trip_type"]:checked').val());
	var cache = {};
	var from_airport = $('#from').val();
	var to_airport = $('#to').val();
	/*$(".fromflight, .departflight").catcomplete({
		source: function(request, response) { 
			request['type'] = $('[name="sector_type"]:checked').val();
			
			var term = request.term;
			var search_key = term + request.type;
			if (search_key in cache) {
				response(cache[search_key]);
				return;
			} else {
			
				$.getJSON(app_base_url + "index.php/ajax/get_airport_code_list", request, function(data, status, xhr) {
					if ($.isEmptyObject(data) == true && $.isEmptyObject(cache[""]) == false) {
						data = cache[""]
					} else {
						cache[search_key] = data;
						response(cache[search_key])
					}
				})
			}
		},
		minLength: 0,
		autoFocus: true,
		select: function(event, ui) {
			var label = ui.item.label;
			var category = ui.item.category;
			if (this.id == 'to') {
				to_airport = ui.item.value
			} else if (this.id == 'from') {
				from_airport = ui.item.value
			}
			$(this).siblings('.loc_id_holder').val(ui.item.id);
			//auto_focus_input(this.id);
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
		var auto_suggest_value = highlight_search_text(this.term.trim(), item.value, item.label);
		var top = 'Top Searches';
		return $("<li class='custom-auto-complete'>").append('<a>' + auto_suggest_value + '</a>').appendTo(ul)
	};
	$(".departflight").catcomplete("instance")._renderItem = function(ul, item) {
		
		var auto_suggest_value = highlight_search_text(this.term.trim(), item.value, item.label);
		return $("<li class='custom-auto-complete'>").append('<a>' + auto_suggest_value + '</a>').appendTo(ul)
	};*/
	$(".fromflight, .departflight").catcomplete({
		source: function(request, response) { 
			request['type'] = $('[name="sector_type"]:checked').val();
			
			var term = request.term;
			var search_key = term + request.type;
			if (search_key in cache) {
				response(cache[search_key]);
				return;
			} else {
			
				$.getJSON(app_base_url + "index.php/ajax/get_airport_code_list", request, function(data, status, xhr) {
					if ($.isEmptyObject(data) == true && $.isEmptyObject(cache[""]) == false) {
						data = cache[""]
					} else {
						cache[search_key] = data;
						response(cache[search_key])
					}
				})
			}
		},
		minLength: 0,
		autoFocus: true,
		select: function(event, ui) {
			var label = ui.item.label;
			var category = ui.item.category;
			if (this.id == 'to') {
				to_airport = ui.item.value
			} else if (this.id == 'from') {
				from_airport = ui.item.value
			}
			$(this).siblings('.loc_id_holder').val(ui.item.id);
			auto_focus_input(this.id);
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
		var auto_suggest_value = highlight_search_text(this.term.trim(), item.value, item.label);
		var top = 'Top Searches';
		return $("<li class='custom-auto-complete'>").append('<a>' + auto_suggest_value + '</a>').appendTo(ul)
	};
	
	$(".departflight").catcomplete("instance")._renderItem = function(ul, item) {
		
		var auto_suggest_value = highlight_search_text(this.term.trim(), item.value, item.label);
		return $("<li class='custom-auto-complete'>").append('<a>' + auto_suggest_value + '</a>').appendTo(ul)
	};
	//$('#flight_datepicker2, #OWT_adult, #OWT_child, #OWT_infant, #class, #carrier').change(function() {
		//auto_focus_input(this.id)
	//});
	if ($.isFunction('total_pax_count') == true) {
		total_pax_count('flight_form');
	}
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
	$('.choosen_airline_class_dom').click(function() {
//		var airline_class_name = $(this).val();
//		alert(airline_class_name);
		var airline_class = $(this).val();
		$('#class_i').val(airline_class);
		if (airline_class == '') {
			airline_class_name = 'Class'
		}
//		$('#choosen_airline_class_dom').empty().text(airline_class_name);
		if ($('.class_advance_div_dom').hasClass('fadeinn') == true) {
			$('.class_advance_div_dom').removeClass('fadeinn')
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

});

function show_alert_content(content, container) {
	if (container == '' || container == undefined || container == null) {
		container = '.alert-danger'
	}
	$(container).text(content);
	if (content.length > 0) {
		alert(content);
		$(container).removeClass('hide')
	} else {
		$(container).addClass('hide')
	}
}

/*function manage_infant_count(pax_type) {
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
	show_alert_content(_content, '#flight-alert-box');
}*/

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
                _content = 'Max 9 Passenger(Adult+Child) Allowed'
            } else {
            	$('#OWT_adult').parent('.adult_count_div').find('button[data-type=plus]').removeAttr('disabled');
            	$('#OWT_child').parent('.child_count_div').find('button[data-type=plus]').removeAttr('disabled');
            }
    	//Infant
        if (infant_count > 0 && infant_count > adult_count) {
            $('#OWT_infant').val(0)
        }
        $('#OWT_infant').parent('.infant_count_div').find('button[data-type=plus]').removeAttr('disabled');
    }else if (pax_type == 'child') {//Child
    	var temp_adult_child_count = (total_adult_child_count	 - 1);
		if (temp_adult_child_count >= flight_adult_child_max_count) {
            $('#OWT_child').val(child_count-1);
            $('#OWT_child').parent('.child_count_div').find('button[data-type=plus]').attr('disabled', 'disabled');
            _content = 'Max 9 Passenger(Adult+Child) Allowed'
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
        		$('#m_from' + i).prop('required',true);
        		$('#m_to' + i).prop('required',true);
        		$('#m_flight_datepicker' + i).prop('required',true);
        		//make field requeired
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
	segment_count = segment_count - 1;
	toggle_add_remove_segments((segment_count));
    if (segment_count >= min_multicity_segments) {
        //$('#multi_city_container_' + segment_count).hide();
    	var seg_object = $(this).closest('.multi_city_container');
    	seg_object.hide();
    	$('input, select', seg_object).val('').attr('disabled', 'disabled').addClass('inactive_segment');
    	//$('input, select', seg_object).attr('disabled', 'disabled').addClass('inactive_segment');
    	seg_object.insertAfter($('.multi_city_container', '#multi_way_fieldset').last());
        
        $('#multicity_segment_count').val(segment_count);
        validate_multicity_segments(segment_count);
        validate_segment_dates();
    }
	
});
//Validaing MultiCity Departure Date
$('.m_depature_date').change(function(){
	validate_segment_dates();
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
	//alert($('[name="trip_type"]:checked').val());
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

//Validaing Roundway Departure Date
$('#flight_datepicker1, #flight_datepicker2').change(function(){
	validate_roundway_dates(this);
});
//Round Way Dates
function validate_roundway_dates(thisRef)
{
	auto_set_dates($("#flight_datepicker1").datepicker('getDate'), "flight_datepicker2", 'minDate', 0);
	if ($(thisRef).attr('id') == 'flight_datepicker1') {
		window.setTimeout(function() {$('#flight_datepicker2').focus();}, 1);
	}
}
