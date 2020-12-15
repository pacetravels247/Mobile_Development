$(document).ready(function() {

	$(".pkupdt").click(function(){
		$('#car_datepicker1').datepicker('show');
	});
	$(".retdt").click(function(){
		$('#car_datepicker2').datepicker('show');
	});
	$('.loader-image').show();
	pre_load_audio();
	
	var _fltr_r_cnt = 0; //Filter Result count
	var _total_r_cnt = 0; //Total Result count
	var _offset = 0; //Offset to load results
	var dynamic_page_data = true;

	var processLazyLoad = function() {
		// load_tranfer(process_result_update, _offset, _ini_fil);
			//check if your div is visible to user
			//CODE ONLY CHECKS VISIBILITY FROM TOP OF THE PAGE
			if (_next_page == true && $('#npl_img').get(0) && $('#npl_img').get(0).scrollHeight != 0) {
				//// console.log('Lazy loaded flexible');
				if ($(window).scrollTop() + $(window).height() >= $('#npl_img').get(0).scrollHeight) {
					if (!$('#npl_img').attr('loaded')) {
						_next_page == false;
						//not in ajax.success due to multiple sroll events
						$('#npl_img').attr('loaded', true);
						//ajax goes here
						load_car(process_result_update, _offset, _ini_fil);
					}
				}
			}
		}
		/**
		 * Offset and total records needed for pagination
		 */
	var _next_page = false;
	function ini_pagination() {
		
		//fixme - here
		console.log(_offset+' offset : '+_fltr_r_cnt+ ' Filter Count '+ _total_r_cnt+ ' Total Count');
		if (_offset >= _fltr_r_cnt && _fltr_r_cnt < _total_r_cnt) {
			_next_page = false;
			//// console.log('Filters are applied and all the results are loaded');
			$('#npl_img').hide();
		} else if (_offset < _fltr_r_cnt && _fltr_r_cnt < _total_r_cnt) {
			_next_page = true;
			//// console.log('Filters are applied and all the results are not loaded');
			$('#npl_img').show();
		} else if (_offset > _total_r_cnt && _fltr_r_cnt == _total_r_cnt && dynamic_page_data == true) {
			_next_page = false;
			//all data loaded, remove scroll event handler as we no longer need lazy loading of data
			console.log('No More Records so can activate JS filter and disable pagination');
			$(window).unbind('scroll', window, processLazyLoad);
			$('#npl_img').remove();
			dynamic_page_data = false;
			//enable sorting via javascript
			enable_sort();
		} else if (_offset < _fltr_r_cnt) {
			_next_page = true;
			//// console.log('More Records are available to load');
			$('#npl_img').show();
		}
	}

	window.process_result_update = function(result) {
		// alert();
		// console.log(result.hasOwnProperty('status'));
		// alert(result.hasOwnProperty('status'));
		var ___data_r = Date.now();
		$('.loader-image').hide();
		hide_result_pre_loader();
		if (result.hasOwnProperty('status') == true && result.status == true) {
			
			set_total_summary(result.total_result_count, result.filter_result_count, result.offset);
			update_total_count_summary();
			$('#npl_img').removeAttr('loaded');
			$('#car_search_result').html(result.data);
			
			$('#npl_img').hide();
			$('.car_results').css('opacity', '1');
		/*	if (_offset == 0) {
				//// console.log('Data loaded with offset 0');
				$('#car_search_result').html(result.data);
				//No Result Found
			} else {
				//// console.log('Pagination data loaded with offset');
				$('#car_search_result').append(result.data);
			}*/
			ini_pagination();
			// $('#empty_car_search_result').hide();
			/*Bishnu*/
			/*if(result.total_result_count > 0){
				$('#empty_car_search_result').hide();
			}else{
				$('#empty_car_search_result').show();
			}*/
			
			//Set time out to lazy load images
			lazy_img();
		}else {
			$('#empty_car_search_result').show();
			$('#npl_img').hide();
			$('.car_results').css('opacity', '1');
			
		}

		
	}

	window.ini_result_update = function(result) {

		    post_load_audio();
		if (result.hasOwnProperty('status') == true && result.status == true) {
			
			if(typeof result.filters.car_type != 'undefined'){
				enable_location_selector(result.filters.car_type);
			}
			if(typeof result.filters.p.min != 'undefined' && typeof result.filters.p.max != 'undefined'){
				//update_range_slider(parseInt(result.filters.p.min), parseInt(result.filters.p.max));
				var temp_minimum_record = $('.r-r-i:first');
				var minPrice = $('.f-p:first', temp_minimum_record).data('price');
				var maxPrice = $('.f-p:first', temp_minimum_record).data('price');
				// alert(minPrice);
				// alert(maxPrice);
				var price = 0;
				$('.r-r-i').each(function(key, value) {
				price = $('.f-p:first', this).data('price');				
				if (parseFloat(price) < parseFloat(minPrice)) {
					minPrice = parseFloat(price);
				}
	
				if (parseFloat(price) > parseFloat(maxPrice)) {
					maxPrice = parseFloat(price);
				}
			});
				update_range_slider(minPrice, maxPrice);
			}
			inif();

			
			_ini_fil['min_price'] = parseInt(result.filters.p.min);
			_ini_fil['max_price'] = parseInt(result.filters.p.max);
		if(typeof result.filters.door_count != 'undefined'){
			enable_door_count_wrapper(result.filters.door_count);
		}
		if(typeof result.filters.vendor_list != 'undefined'){
			enable_vendor_list_wrapper(result.filters.vendor_list);
		}
		if(typeof result.filters.coverage_type != 'undefined'){
			enable_coverage_list_wrapper(result.filters.coverage_type);
		}
		if(typeof result.filters.passenger_quantity != 'undefined'){
			enable_passenger_quantity_wrapper(result.filters.passenger_quantity);
		}
		if(typeof result.filters.vehicle_category != 'undefined'){
			enable_vehicle_category_wrapper(result.filters.vehicle_category);
		}
		if(typeof result.filters.vehicle_size != 'undefined'){
			enable_vehicle_size_wrapper(result.filters.vehicle_size);
		}
		if(typeof result.filters.vehicle_package != 'undefined'){
			enable_vehicle_package_wrapper(result.filters.vehicle_package);
		}
		if(typeof result.filters.vehicle_ac != 'undefined'){
			enable_vehicle_ac_wrapper(result.filters.vehicle_ac);
		}
		if(typeof result.filters.vehicle_manual != 'undefined'){
			enable_vehicle_manual_wrapper(result.filters.vehicle_manual);
		}
			//// console_log(result.filters.coverage_type);

			process_result_update(result);
			//$(window).on('scroll', processLazyLoad);
		}


		check_empty_search_result();

		$(".price-l-2-h").click();
	}
	
	function post_load_audio()
	{
		pause_audio('pre-load');
		pause_audio('landing-load');
		if ($('.r-r-i').length > 0) {
			play_audio('post-load');
		} else {
			play_audio('empty-load');
		}
	}
	function pre_load_audio()
	{
		play_audio('landing-load');
		/*document.getElementById('landing-load').addEventListener("ended", function() {
			play_audio('pre-load');
		});*/
	}
	function play_audio(element_id)
	{
		//document.getElementById(element_id).play();
	}
	/**
	 * Pause Audio
	 */
	function pause_audio(element_id)
	{
		//document.getElementById(element_id).pause();
	}

	/**
	 * Set total result summary
	 */
	function set_total_summary(total_count, fltr_count, offset) {
		_fltr_r_cnt = parseInt(fltr_count); //visible 
		_total_r_cnt = parseInt(total_count); //total
		_offset = parseInt(offset);
	}

	function lazy_img() {
		$("img.lazy").lazy({
			threshold: 200
		});
	}

	function check_empty_search_result() {
		if ($('.r-r-i:first').index() == -1) {
			$('#empty-search-result').show();
			$('#page-parent').remove();
		}
	}

	//Load hotels from active source
	show_result_pre_loader();
	$(document).on('click', '.location-map', function() {
		$('#map-box-modal').modal();
	});


	/**
	 * Toggle active class to highlight current applied sorting
	 **/
	$(document).on('click', '.sorta', function(e) {
		e.preventDefault();
		$(this).closest('.sortul').find('.active').removeClass('active');
		//Add to sibling
		$(this).siblings().addClass('active');
	});

	//************************** **********/

	$('.toglefil').click(function() {
		$(this).toggleClass('active');
	});


	/*  Mobile Filter  */
	$('.filter_tab').click(function() {
		$('.resultalls').stop(true, true).toggleClass('open');
		$('.coleft').stop(true, true).slideToggle(500);
	});

	var widowwidth = $(window).width();
	if (widowwidth < 991) {
		$('.resultalls.open #car_search_result').on('click', function() {
			$('.resultalls').removeClass('open');
			$('.coleft').slideUp(500);
		});
	}


	var application_preference_currency = document.getElementById('pri_app_pref_currency').value;
	

	/** -------------------------SORT LIST DATA---------------------- **/
	
	function enable_sort() {
		$(".price-l-2-h").click(function() {
			$(this).addClass('hide');
			$('.price-h-2-l').removeClass('hide');
			$('.car_results').css('opacity', '0.1');
			$("#car_search_result").jSort({
				sort_by: '.f-p',
				item: '.r-r-i',
				order: 'asc',
				is_num: true
			});
			$('.car_results').css('opacity', '1');
		});

		$(".price-h-2-l").click(function() {
			$(this).addClass('hide');
			$('.car_results').css('opacity', '0.1');
			$('.price-l-2-h').removeClass('hide');
			$("#car_search_result").jSort({
				sort_by: '.f-p',
				item: '.r-r-i',
				order: 'desc',
				is_num: true
			});
			$('.car_results').css('opacity', '1');
		});

		$(".name-l-2-h").click(function() {
			$(this).addClass('hide');
			$('.car_results').css('opacity', '0.1');
			$('.name-h-2-l').removeClass('hide');
			$("#car_search_result").jSort({
				sort_by: '.car_name',
				item: '.r-r-i',
				order: 'asc',
				is_num: false
			});
			$('.car_results').css('opacity', '1');
		});

		$(".name-h-2-l").click(function() {
			$(this).addClass('hide');
			$('.car_results').css('opacity', '0.1');
			$('.name-l-2-h').removeClass('hide');
			$("#car_search_result").jSort({
				sort_by: '.car_name',
				item: '.r-r-i',
				order: 'desc',
				is_num: false
			});
			$('.car_results').css('opacity', '1');
		});
		
		$(".cartype-l-2-h").click(function() {
			$('.car_results').css('opacity', '0.1');
			$(this).addClass('hide');
			$('.cartype-h-2-l').removeClass('hide');
			$("#car_search_result").jSort({
				sort_by: '.car_type',
				item: '.r-r-i',
				order: 'asc',
				is_num: false
			});
			$('.car_results').css('opacity', '1');
		});

		$(".cartype-h-2-l").click(function() {
			$('.car_results').css('opacity', '0.1');
			$(this).addClass('hide');
			$('.cartype-l-2-h').removeClass('hide');
			$("#car_search_result").jSort({
				sort_by: '.car_type',
				item: '.r-r-i',
				order: 'desc',
				is_num: false
			});
			$('.car_results').css('opacity', '1');
		});

		$(".supplier-l-2-h").click(function() {
			$(this).addClass('hide');
			$('.supplier-h-2-l').removeClass('hide');
			$('.car_results').css('opacity', '0.1');
			$("#car_search_result").jSort({
				sort_by: '.supplier_name',
				item: '.r-r-i',
				order: 'asc',
				is_num: false
			});
			$('.car_results').css('opacity', '1');
		});

		$(".supplier-h-2-l").click(function() {
			$(this).addClass('hide');
			$('.car_results').css('opacity', '0.1');
			$('.supplier-l-2-h').removeClass('hide');
			$("#car_search_result").jSort({
				sort_by: '.supplier_name',
				item: '.r-r-i',
				order: 'desc',
				is_num: false
			});
			$('.car_results').css('opacity', '1');
		});
	}
	enable_sort();
	/** -------------------------SORT LIST DATA---------------------- **/

	/**
	 * Toggle active class to highlight current applied sorting
	 **/
	$(document).on('click', '.sorta', function(e) {
		e.preventDefault();
		loader();
		$(this).closest('.sortul').find('.active').removeClass('active');
		//Add to sibling
		$(this).siblings().addClass('active');
	});

	$('.loader').on('click', function(e) {
		e.preventDefault();
		loader();
	});

	$(document).on('change', '.vehicle-category-f', function(e) {
		loader();
		ini_cartypef();
		filter_rom();

	});
	$(document).on('change', '.passenger-quantity-f', function(e) {
		loader();
		ini_passengerquantityf();
		filter_rom();
		
	});
	     
	$(document).on('change', '.door-count-f', function(e) {
		loader();
		ini_doorcountf();
		filter_rom();
	
	});
	$(document).on('change', '.vehicle-size-f', function(e) {
		loader();
		ini_vehiclesizef();
		filter_rom();
		
	});
	$(document).on('change', '.vehicle-package-f', function(e) {
		loader();
		ini_vehiclepackagef();
		filter_rom();
		
	});
	$(document).on('change', '.vehicle-ac-f', function(e) {
		loader();
		ini_vehicleacf();
		filter_rom();
	});
	$(document).on('change', '.vehicle-manual-f', function(e) {

		loader();
		ini_vehiclemanualf();
		filter_rom();
		
	});

	$(document).on('change', '.vendor-list-f', function(e) {
		loader();
		ini_vendorlistf();
		filter_rom();
	
	});
	$(document).on('change', '.coverage-type-f', function(e) {
		loader();
		ini_coveragetypef();
		filter_rom('coverage-type-f');
		
	});
	
	
	//hotel-facility
	 function pre_load_audio() {
	 	play_audio('landing-load');
	 	/*document.getElementById('landing-load').addEventListener("ended", function() {
	 		play_audio('pre-load');
	 	});*/
	 }
	 /*function play_audio(element_id){
	 	document.getElementById(element_id).play();
	 }*/

	//Balu A - Setting minimum and maximum price for slider range
	function update_range_slider(minPrice, maxPrice) {

		$('#core_minimum_range_value', '#core_min_max_slider_values').val(minPrice);
		$('#core_maximum_range_value', '#core_min_max_slider_values').val(maxPrice);
		//price-refine
		enable_price_range_slider(minPrice, maxPrice);
	}
	//Reset the filters -- Balu A
	$(document).on('click', '#reset_filters', function() {
		loader();
		//Reset car type       
		$('input.car-type-f').prop('checked', false);
		$('input.door-count-f').prop('checked', false);
		$('input.vendor-list-f').prop('checked', false);
		$('input.coverage-type-f').prop('checked', false);
		$('input.passenger-quantity-f').prop('checked', false);
		$('input.vehicle-category-f').prop('checked', false);
		$('input.vehicle-manual-f').prop('checked', false);
		$('input.vehicle-ac-f').prop('checked', false);
		$('input.vehicle-package-f').prop('checked', false);
		$('input.vehicle-size-f').prop('checked', false);
		
		// console.log($('input.vehicle-size-f'));


		set_slider_label(min_amt, max_amt);
		var minPrice = parseFloat($('#core_minimum_range_value', '#core_min_max_slider_values').val());
		var maxPrice = parseFloat($('#core_maximum_range_value', '#core_min_max_slider_values').val());
		_ini_fil['min_price'] = minPrice;
		_ini_fil['max_price'] = maxPrice;
		$("#price-range").slider("option", "values", [minPrice, maxPrice]);
		inif();
		filter_rom();
	});
	/**
	 * Show loader images
	 * 
	 */
	function loader() {
		// $('.container').css({
		// 	'opacity': '.1'
		// });
		setTimeout(function() {
			$('.container').css({
				'opacity': '1'
			}, 'slow');
		}, 1000);
	}

	//------ INI F
	var _ini_fil = {};
	//car type door-count-f  vendor-list-f  coverage-type-f passenger-quantity-f
	_ini_fil['_car_Type'] = [];
	_ini_fil['_door_Count'] = [];
	_ini_fil['_vendor_List'] = [];
	_ini_fil['_coverage_Type'] = [];
	_ini_fil['_passenger_Quantity'] = [];
	_ini_fil['_vehicle_Category'] = [];
	_ini_fil['_vehicle_Size'] = [];
	_ini_fil['_vehicle_Package'] = [];
	_ini_fil['_vehicle_Ac'] = [];
	_ini_fil['_vehicle_Manual'] = [];

	function ini_cartypef() {

		_ini_fil['_car_Type'] = $('input.vehicle-category-f:checked').map(function() {
			return this.value;
		}).get();


	}
	     
	function ini_doorcountf() {
		_ini_fil['_door_Count'] = $('input.door-count-f:checked').map(function() {
			return this.value;
		}).get();
	}
	function ini_vendorlistf() {
		_ini_fil['_vendor_List'] = $('input.vendor-list-f:checked').map(function() {
			return this.value;
		}).get();
	}
	function ini_coveragetypef() {
		_ini_fil['_coverage_Type'] = $('input.coverage-type-f:checked').map(function() {
			return this.value;
		}).get();
		console.log(_ini_fil['_coverage_Type']);
	}
	function ini_passengerquantityf() {
		_ini_fil['_passenger_Quantity'] = $('input.passenger-quantity-f:checked').map(function() {
			return this.value;
		}).get();
	}
	function ini_vehiclecategoryf() {
		_ini_fil['_vehicle_Category'] = $('input.vehicle-category-f:checked').map(function() {
			return this.value;
		}).get();
		
	}
	function ini_vehiclesizef() {
		_ini_fil['_vehicle_Size'] = $('input.vehicle-size-f:checked').map(function() {
			return this.value;
		}).get();
	}
	function ini_vehiclepackagef(){
		_ini_fil['_vehicle_Package'] = $('input.vehicle-package-f:checked').map(function() {
			return this.value;
		}).get();
	}
	function ini_vehicleacf(){
		_ini_fil['_vehicle_Ac'] = $('input.vehicle-ac-f:checked').map(function() {
			return this.value;
		}).get();
	}
	function ini_vehiclemanualf(){
		_ini_fil['_vehicle_Manual'] = $('input.vehicle-manual-f:checked').map(function() {
			return this.value;
		}).get();
	}
	
	_ini_fil['min_price'] = 0;
	_ini_fil['max_price'] = 0;

	function ini_pricef() {
	
		_ini_fil['min_price'] = parseFloat($("#price-range").slider("values")[0]);
		_ini_fil['max_price'] = parseFloat($("#price-range").slider("values")[1]);
	}

	_ini_fil['hn_val'] = '';

	function ini_hotel_namef() {
		_ini_fil['hn_val'] = $('#hotel-name').val().trim().toLowerCase();
	}

	_ini_fil['dealf'] = false;

	function ini_dealf() {
		if ($('.deal-status-filter:checked').val() == 'filter') {
			_ini_fil['dealf'] = true;
		} else {
			_ini_fil['dealf'] = false;
		}
	}

	function inif() {
		//ini_starf();
		//ini_hotel_locf();
		//ini_hotel_accf();
		//ini_pricef();
		//ini_hotel_namef();
		//ini_dealf();
		// ini_cartypef();
		ini_cartypef();
		ini_doorcountf();
		ini_vendorlistf();
		ini_coveragetypef();
		ini_passengerquantityf();
		ini_vehiclecategoryf();
		ini_vehiclesizef();
		ini_vehiclepackagef();
		ini_vehicleacf();
		ini_vehiclemanualf();
		ini_pricef();
	}
	//------ INI F

	/**
	 * _filter_trigger	==> element which caused fliter to be triggered
	 * _filter			==> default filter settings received from filter trigger
	 */
	function filter_rom(_filter_trigger, _filter, attr) {
		inif();
		
		if (dynamic_page_data == false) {
			
			_fltr_r_cnt = 0;
			//$('.r-r-i' + _filter) : FIXME
			$('.r-r-i').each(function(key, value) {
				
				var _cartype = $('.car_type_id', this).text();
				var _door_count = $('.door_count', this).text();
				var _vehicle_size = $('.vehicle_size', this).text();
				var _passenger_quantity = $('.passenger_quantity', this).text();
				var _vehicle_package = $('.vehicle_package', this).text();
				var _vehicle_ac = $('.vehicle_ac', this).text();
				var _vehicle_manual = $('.vehicle_manual', this).text();
				var _vendor_list = $('.supplier_name', this).text();
				var _rmp = $('.vehicle_price', this).text();

				if (
					((_cartype == "" || _ini_fil['_car_Type'].length == 0) || ($.inArray((_cartype), _ini_fil['_car_Type']) != -1)) &&
					(_rmp >= _ini_fil['min_price'] && _rmp <= _ini_fil['max_price']) &&
					((_door_count == "" || _ini_fil['_door_Count'].length == 0) || ($.inArray((_door_count), _ini_fil['_door_Count']) != -1)) && 
					((_vehicle_size == "" || _ini_fil['_vehicle_Size'].length == 0) || ($.inArray((_vehicle_size), _ini_fil['_vehicle_Size']) != -1)) &&
					((_passenger_quantity == "" || _ini_fil['_passenger_Quantity'].length == 0) || ($.inArray((_passenger_quantity), _ini_fil['_passenger_Quantity']) != -1)) && 
					((_vehicle_package == "" || _ini_fil['_vehicle_Package'].length == 0) || ($.inArray((_vehicle_package), _ini_fil['_vehicle_Package']) != -1)) && 
					((_vehicle_ac == "" || _ini_fil['_vehicle_Ac'].length == 0) || ($.inArray((_vehicle_ac), _ini_fil['_vehicle_Ac']) != -1)) &&
					((_vehicle_manual == "" || _ini_fil['_vehicle_Manual'].length == 0) || ($.inArray((_vehicle_manual), _ini_fil['_vehicle_Manual']) != -1)) && 
					((_vendor_list == "" || _ini_fil['_vendor_List'].length == 0) || ($.inArray((_vendor_list), _ini_fil['_vendor_List']) != -1)) 					

				) {
					// alert('yes');
					++_fltr_r_cnt;
					$(this).removeClass('hide');
				} else {
					// alert('no');
					$(this).addClass('hide');
				}
				// set_marker_visibility(this, null, null, _rhn, star);
			});
		} else {			
			//filter from backend
			dynamic_filter_rom();
		}
		update_total_count_summary();
	}

	function has_any_facility(_fclty) {
		var has = false;
		$.each(_ini_fil['_fac'], function(k, v) {
			if (_fclty.search(v) > -1) {
				has = true;
				return false
			}
		});
		return has;
	}

	function dynamic_filter_rom() {
		//-- empty results and show loader
		show_result_pre_loader();
		$('#car_search_result').empty();
		_offset = 0;
		// console.log(process_result_update+', '+ _offset+',' + _ini_fil);
		console.log(_offset+',' + _ini_fil);
	
		load_car(process_result_update, _offset, _ini_fil);
	}

	/**
	 *Update Hotel Count Details
	 */
	function update_total_count_summary() {
		$('#car_search_result').show();
		if (isNaN(_fltr_r_cnt) == true || _fltr_r_cnt < 1) {
			_fltr_r_cnt = 0;
			//display warning
			$('#car_search_result').hide();
			$('#empty_car_search_result').show();
		} else {
			$('#car_search_result').show();
			$('#empty_car_search_result').hide();
		}
		$('#total_records').text(_total_r_cnt);
		$('.total-row-record-count').text(_total_r_cnt);
		$('#filter_records').text(_fltr_r_cnt);
	}
	var sliderCurrency = application_preference_currency;
	//application_preference_currency
	var min_amt = 0;
	var max_amt = 0;

	function enable_price_range_slider(minPrice, maxPrice) {
		min_amt = parseFloat(minPrice);
		max_amt = parseFloat(maxPrice);
		minPrice = parseFloat(minPrice);
		maxPrice = parseFloat(maxPrice);
		// alert(maxPrice);
		$("#price-range").empty();
		/**** PRICE SLIDER START ****/
		$("#price-range").slider({
			range: true,
			min: minPrice,
			max: maxPrice,
			values: [minPrice, maxPrice],
			slide: function(event, ui) {
				set_slider_label(ui.values[0], ui.values[1]);
			},
			change: function(e,vl) {
				//// console.log(vl.values[0]);
				//enable_price_range_slider(vl.values[0], vl.values[1]);
				if ('originalEvent' in e) {
					loader();
					ini_pricef();
					filter_rom();
				}
			}
		});
		set_slider_label(minPrice, maxPrice);
		/**** PRICE SLIDER END ****/
	}

	function set_slider_label(val1, val2) {
		// alert(sliderCurrency);
		//sliderCurrency = $('#currency').val();
		//$("#car-price").text(sliderCurrency + ' ' + val1 + " - " + sliderCurrency + ' ' + val2);
		$("#car-price").text(sliderCurrency + ' ' + parseFloat(val1).toFixed(2) + " - " + sliderCurrency + ' ' + parseFloat(val2).toFixed(2));
	}

	function enable_location_selector(car_type) {

		var _car_type_option_list = '';
		var i = 0;
		$.each(car_type, function(k, v) {
			_car_type_option_list += '<li>';
			_car_type_option_list += '<div class="squaredThree">';
			_car_type_option_list += '<input id="llocSquaredThree' + i + '" class="car-type-f" type="checkbox" name="check" value="' + v['v'] + '">';
			_car_type_option_list += '<label for="llocSquaredThree' + i + '"></label>';
			_car_type_option_list += '</div>';
			_car_type_option_list += '<label class="lbllbl" for="llocSquaredThree' + i + '">' + v['v'] + ' (' + v['c'] + ')</label>';
			_car_type_option_list += '</li>';
			i++;
		});
		$('#car-type-wrapper').html(_car_type_option_list);
	}
	
	//for door count
	function enable_door_count_wrapper(door_count) {

		var _door_count_option_list = '';
		var i = 0;
		$.each(door_count, function(k, v) {
			_door_count_option_list += '<li>';
			_door_count_option_list += '<div class="squaredThree">';
			_door_count_option_list += '<input id="flocSquaredFour' + i + '" class="door-count-f" type="checkbox" name="door_count[]" value="' + v['v'] + '">';
			_door_count_option_list += '<label for="flocSquaredFour' + i + '"></label>';
			_door_count_option_list += '</div>';
			_door_count_option_list += '<label class="lbllbl" for="flocSquaredFour' + i + '">' + v['v'] + ' (' + v['c'] + ')</label>';
			_door_count_option_list += '</li>';
			i++;
		});
		$('#car-door-count-wrapper').html(_door_count_option_list);
	}
	
	//for vendor list
	function enable_vendor_list_wrapper(vendor_list)
	{

		var _vendor_list_option_list = '';
		var i = 0;
		$.each(vendor_list, function(k, v) {
			_vendor_list_option_list += '<li>';
			_vendor_list_option_list += '<div class="squaredThree">';
			_vendor_list_option_list += '<input id="flocSquaredFive' + i + '" class="vendor-list-f" type="checkbox" name="vendor_list[]" value="' + v['v'] + '">';
			_vendor_list_option_list += '<label for="flocSquaredFive' + i + '"></label>';
			_vendor_list_option_list += '</div>';
			_vendor_list_option_list += '<label class="lbllbl" for="flocSquaredFive' + i + '">' + v['v'] + ' (' + v['c'] + ')</label>';
			_vendor_list_option_list += '</li>';
			i++;
		});
		$('#car-vendor-list-wrapper').html(_vendor_list_option_list);
	}

	//for coverage list
	function enable_coverage_list_wrapper(coverage_type)
	{   

		var _coverage_type_option_list = '';
		var i = 0;
		$.each(coverage_type, function(k, v) {
			_coverage_type_option_list += '<li>';
			_coverage_type_option_list += '<div class="squaredThree">';
			_coverage_type_option_list += '<input id="flocSquaredSix' + i + '" class="coverage-type-f" type="checkbox" name="coverage_type[]" value="' + v['v'] + '">';
			_coverage_type_option_list += '<label for="flocSquaredSix' + i + '"></label>';
			_coverage_type_option_list += '</div>';
			_coverage_type_option_list += '<label class="lbllbl" for="flocSquaredSix' + i + '">' + v['v'] + ' (' + v['c'] + ')</label>';
			_coverage_type_option_list += '</li>';
			i++;
		});
		$('#car-package-list-wrapper').html(_coverage_type_option_list);
	}
	
	//for passenger quantity
	function enable_passenger_quantity_wrapper(passenger_quantity){
		var _passenger_quantity_option_list = '';
		var i = 0;
		$.each(passenger_quantity, function(k, v) {
			_passenger_quantity_option_list += '<li>';
			_passenger_quantity_option_list += '<div class="squaredThree">';
			_passenger_quantity_option_list += '<input id="flocSquaredSeven' + i + '" class="passenger-quantity-f" type="checkbox" name="passenger_quantity[]" value="' + v['v'] + '">';
			_passenger_quantity_option_list += '<label for="flocSquaredSeven' + i + '"></label>';
			_passenger_quantity_option_list += '</div>';
			_passenger_quantity_option_list += '<label class="lbllbl" for="flocSquaredSeven' + i + '">' + v['v'] + ' (' + v['c'] + ')</label>';
			_passenger_quantity_option_list += '</li>';
			i++;
		});
		$('#car-passenger-quantity-wrapper').html(_passenger_quantity_option_list);
	}
	
	//for vehicle category
	function enable_vehicle_category_wrapper(vehicle_category){
		var _vehicle_category_option_list = '';
		var i = 0;
		$.each(vehicle_category, function(k, v) {
			_vehicle_category_option_list += '<li>';
			_vehicle_category_option_list += '<div class="squaredThree">';
			_vehicle_category_option_list += '<input id="flocSquaredEight' + i + '" class="vehicle-category-f" type="checkbox" name="vehicle_category[]" value="' + v['v'] + '">';
			_vehicle_category_option_list += '<label for="flocSquaredEight' + i + '"></label>';
			_vehicle_category_option_list += '</div>';
			_vehicle_category_option_list += '<label class="lbllbl" for="flocSquaredEight' + i + '">' + v['y'] + ' (' + v['c'] + ')</label>';
			_vehicle_category_option_list += '</li>';
			i++;
		});
		$('#car-vehicle-category-wrapper').html(_vehicle_category_option_list);
	}
	//for vehicle size
	function enable_vehicle_size_wrapper(vehicle_size){
		var _vehicle_size_option_list = '';
		var i = 0;
		$.each(vehicle_size, function(k, v) {
			_vehicle_size_option_list += '<li>';
			_vehicle_size_option_list += '<div class="squaredThree">';
			_vehicle_size_option_list += '<input id="flocSquaredNine' + i + '" class="vehicle-size-f" type="checkbox" name="vehicle_size[]" value="' + v['v'] + '">';
			_vehicle_size_option_list += '<label for="flocSquaredNine' + i + '"></label>';
			_vehicle_size_option_list += '</div>';
			_vehicle_size_option_list += '<label class="lbllbl" for="flocSquaredNine' + i + '">' + v['y'] + ' (' + v['c'] + ')</label>';
			_vehicle_size_option_list += '</li>';
			i++;
		});
		$('#car-vehicle-size-wrapper').html(_vehicle_size_option_list);
	}
	//for vehicle package
	function enable_vehicle_package_wrapper(vehicle_package){
		var _vehicle_package_option_list = '';
		var i = 0;
		$.each(vehicle_package, function(k, v) {
			_vehicle_package_option_list += '<li>';
			_vehicle_package_option_list += '<div class="squaredThree">';
			_vehicle_package_option_list += '<input id="flocSquaredTen' + i + '" class="vehicle-package-f" type="checkbox" name="vehicle_package[]" value="' + v['v'] + '">';
			_vehicle_package_option_list += '<label for="flocSquaredTen' + i + '"></label>';
			_vehicle_package_option_list += '</div>';
			_vehicle_package_option_list += '<label class="lbllbl" for="flocSquaredTen' + i + '">' + v['v'] + ' (' + v['c'] + ')</label>';
			_vehicle_package_option_list += '</li>';
			i++;
		});
		$('#car-vehicle-package-wrapper').html(_vehicle_package_option_list);
	}

	//for ac/non ac vehicle
	function enable_vehicle_ac_wrapper(vehicle_ac){
		var _vehicle_ac_option_list = '';
		var i = 0;
		$.each(vehicle_ac, function(k, v) {
			_vehicle_ac_option_list += '<li>';
			_vehicle_ac_option_list += '<div class="squaredThree">';
			_vehicle_ac_option_list += '<input id="flocSquaredEleven' + i + '" class="vehicle-ac-f" type="checkbox" name="vehicle_ac[]" value="' + v['v'] + '">';
			_vehicle_ac_option_list += '<label for="flocSquaredEleven' + i + '"></label>';
			_vehicle_ac_option_list += '</div>';
			_vehicle_ac_option_list += '<label class="lbllbl" for="flocSquaredEleven' + i + '">' + v['v'] + ' (' + v['c'] + ')</label>';
			_vehicle_ac_option_list += '</li>';
			i++;
		});
		$('#car-vehicle-ac-wrapper').html(_vehicle_ac_option_list);
	}

	//for auto/manual vehicle
	function enable_vehicle_manual_wrapper(vehicle_manual){
		var _vehicle_manual_option_list = '';
		var i = 0;
		$.each(vehicle_manual, function(k, v) {
			_vehicle_manual_option_list += '<li>';
			_vehicle_manual_option_list += '<div class="squaredThree">';
			_vehicle_manual_option_list += '<input id="flocSquaredTwelve' + i + '" class="vehicle-manual-f" type="checkbox" name="vehicle_manual[]" value="' + v['v'] + '">';
			_vehicle_manual_option_list += '<label for="flocSquaredTwelve' + i + '"></label>';
			_vehicle_manual_option_list += '</div>';
			_vehicle_manual_option_list += '<label class="lbllbl" for="flocSquaredTwelve' + i + '">' + v['v'] + ' (' + v['c'] + ')</label>';
			_vehicle_manual_option_list += '</li>';
			i++;
		});
		$('#car-vehicle-manual-wrapper').html(_vehicle_manual_option_list);
	}
	
	function unique_array_values(array_values) {
		var _unique_array_values = [];
		$.each(array_values, function(k, v) {
			if (_unique_array_values.indexOf(v) == -1) {
				_unique_array_values.push(v);
			}
		});
		return _unique_array_values;
	}

	function get_location_list() {
		return $('.h-loc').map(function() {
			return $(this).text();
		});
	}
	$('.vlulike').click(function() {
		$('.vlulike').removeClass('active');
		$(this).addClass('active');
	});
});

	
