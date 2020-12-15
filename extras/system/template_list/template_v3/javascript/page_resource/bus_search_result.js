$(document).ready(function() {
show_result_pre_loader();
//pre_load_audio();
var api_counter = 0;
window.process_result_update = function(result)
{
	$('.bus_preloader').hide();
	$('.loader-image').hide();
	api_counter++;
				
				if (result.hasOwnProperty('status') == true && result.status == true) {
					// alert(result.api_count);
					//$('#bus_search_result').html(result.data);
					$.each(result, function(k, v) {
						var bus_rows = $('<div>').append($(v['data']).find(".busrows").clone()).html();
						if(bus_rows!=""){
							$('#bus_search_result .allbusresult').append(bus_rows);
						}
						//$('#bus_search_result #'+k).append(v);
					});
					//post_load_audio();
					//update total bus count
					update_filter();
					update_total_count_summary(result.api_count);
				} else if(num_of_act_bs == api_counter){
					update_total_count_summary();
					check_empty_search_result();
				}
};

	function check_empty_search_result()
	{
		if ($('.allbusresult').children(".busrows").index() == -1) {
			$('#empty-search-result').show();
			$('#page-parent').hide();
		}
	}

	/**
	*Update Count Details
	*/
	function update_total_count_summary(api_count)
	{
		$('#bus_search_result').show();
		var total_api_result_count = $('#total_api_result_count').val();
		// alert(total_api_result_count);
		var _visible_records = parseInt($('.r-r-i:visible').length);
		var _total_records = $('.r-r-i').length;
		if (isNaN(_visible_records) == true || _visible_records == 0) {
			_visible_records = 0;
			//display warning
			$('#bus_search_result').hide();
			if(api_count == total_api_result_count){
				hide_result_pre_loader();
				$('#empty_bus_search_result').show();

			}
			
		} else {
			hide_result_pre_loader();
			$('#bus_search_result').show();
			$('#empty_bus_search_result').hide();
		}
		if (_visible_records > 1) {
			_visible_records = _visible_records+' buses ';
		} else {
			_visible_records = _visible_records+' bus ';
		}
		$('#total_records').text(_visible_records+' found');
		$('.visible-row-record-count').text(_visible_records);
		$('.total-row-record-count').text(_total_records);
	}

	var ac_sleeper = "AC SLEEPER";
	var non_ac_sleeper = "NON_AC SLEEPER";
	function update_filter()
	{
		//update filters
		var travelList = {};
		var busTypeList = [];
		var departureCategoryList = {};
		var arrivalCategoryList = {};


		var minPrice = 99999999;
		var maxPrice = 0;
		var price = 0;
		var dep_time = 0;
		var arr_time = 0;
		var temp_travel = '' 
		var temp_type = [];
		var busTypeCount = {};
		var bus_type_arr = [];
		//Extras
		busTypeCount[ac_sleeper] = 0;
		busTypeCount[non_ac_sleeper] = 0;
		//Extras End
		$('.r-r-i').each(function(key, value) {
			price = parseFloat($('.bus-price:first', this).text());
			depCat = parseInt($('.departure_datetime:first', this).data('departure-category'));
			arrCat = parseInt($('.arrival_datetime:first', this).data('arrival-category'));
			temp_travel = $('.travel-name:first', this).text();
			
			if (departureCategoryList.hasOwnProperty(depCat) == false) {departureCategoryList[depCat] = depCat;}
			if (arrivalCategoryList.hasOwnProperty(arrCat) == false) {arrivalCategoryList[arrCat] = arrCat;}
			if (travelList.hasOwnProperty(temp_travel) == false) {travelList[temp_travel] = temp_travel;}
			//if (busTypeList.hasOwnProperty(temp_type) == false) {busTypeList[temp_type] = temp_type;}
			if (price < minPrice) {minPrice = price;}
			if (price > maxPrice) {maxPrice = price;}
			//bus-type
			temp_type = $('.bus-type', this).map(function() {
							var temp_text = $(this).text();
							if ((temp_text in busTypeCount) == false) {
								busTypeCount[temp_text] = 0;
							} else {
								busTypeCount[temp_text]++;
							}
							if (busTypeList.indexOf(temp_text) == -1) {
								return temp_text;
							}
						}).get();
			
			//Extra Filters Ac And NON AC Sleepera added here
			var temp_text_arr = []; var ttc = 0;
			$(this).find('.bus-type').each(function(){
				temp_text = $(this).text();
				temp_text_arr[ttc] = temp_text;
				ttc++;
			});
			var bus_type = temp_text_arr.join(" ");
			bus_type_arr.push(bus_type);
			if(bus_type == ac_sleeper)
			{
				var type_count = temp_type.length;
				if(busTypeCount[ac_sleeper] == 0)
					temp_type[type_count] = ac_sleeper;
				busTypeCount[ac_sleeper]++;
			}
			
			if(bus_type == non_ac_sleeper)
			{
				var type_count = temp_type.length;
				if(busTypeCount[non_ac_sleeper] == 0)
					temp_type[type_count] = non_ac_sleeper;

				busTypeCount[non_ac_sleeper]++;
			}
			//Extra Filters Ac And NON AC Sleeper added here END

			if (temp_type.length > 0) {
				busTypeList = busTypeList.concat(temp_type);
			}
		});
		busTypeCount[ac_sleeper]--;
		busTypeCount[non_ac_sleeper]--;

		$('#core_minimum_range_value', '#core_min_max_slider_values').val(minPrice);
		$('#core_maximum_range_value', '#core_min_max_slider_values').val(maxPrice);
		travelList = getSortedObject(travelList);
		busTypeList = getSortedObject(busTypeList);
		//price-refine
		enable_price_range_slider(minPrice, maxPrice);
		//travel-refine
		enable_travel_refine(travelList);
		//bustype-refine
		enable_bus_type_refine(busTypeList, busTypeCount);
		//departure-refine, //arrival-refine
		loadTimeRangeSelector(departureCategoryList, arrivalCategoryList);
		
	}
	//Enable/Disable-Time Range Selector
	function loadTimeRangeSelector(depCatList, arrCatList)
	{
		//Works for both onward and return
		if ($.isPlainObject(depCatList)) {
			var depCatListArray = getArray(depCatList);
			var stopCat = 0;
			if(depCatListArray != ''){
				$('#departureTimeWrapper .time-category').each(function(key, value) {
					depCat = parseInt($(this).val());
					if (depCatListArray.indexOf(depCat) == -1) {
						//disabled
						if((bus_api_result_counter == 1) || (bus_api_result_counter > 1 && !$(this).closest('.time-wrapper').hasClass("enabled")))
						{
							$(this).attr('disabled', 'disabled');
							$(this).closest('.time-wrapper').addClass('disabled');
						}
					} else {
						$(this).removeAttr('disabled');
						$(this).closest('.time-wrapper').removeClass('disabled');
						$(this).closest('.time-wrapper').addClass('enabled');
					}
				});
			}
		}

		if ($.isPlainObject(arrCatList)) {
			var arrCatListArray = getArray(arrCatList);
			var arrCat = 0;
			if(arrCatListArray !=''){
				$('#arrivalTimeWrapper .time-category').each(function(key, value) {
					arrCat = parseInt($(this).val());
					if (arrCatListArray.indexOf(arrCat) == -1) {
						//disabled
						if((bus_api_result_counter == 1) || (bus_api_result_counter > 1 && !$(this).closest('.time-wrapper').hasClass("enabled")))
						{
							$(this).attr('disabled', 'disabled');
							$(this).closest('.time-wrapper').addClass('disabled');
						}
					} else {
						$(this).removeAttr('disabled');
						$(this).closest('.time-wrapper').removeClass('disabled');
						$(this).closest('.time-wrapper').addClass('enabled');
					}
				});
			}
		}
	}

	function getArray(objectWrap)
	{
		var objectWrapValueArr = [];
		$.each(objectWrap, function(key, value) {
			objectWrapValueArr.push(value);
		});
		return objectWrapValueArr;
	}

	function getSortedObject(obj)
	{
		var objValArray = getArray(obj);
		var sortObj = {};
		objValArray.sort();
		$.each(objValArray, function(obj_key, obj_val) {
			$.each(obj, function(i_k, i_v) {
				if (i_v == obj_val) {
					sortObj[i_k] = i_v;
				}
			});
		});
		return sortObj;
	}

	var sliderCurrency = document.getElementById('pri_slider_currency').value;
	var min_amt = 0;
	var max_amt = 0;
	function enable_price_range_slider(minPrice, maxPrice)
	{
		min_amt = minPrice;
		max_amt = maxPrice;
		$( "#slider-range-1" ).slider({
			range: true,
			min: minPrice,
			max: maxPrice,
			animate: "slow",
			values: [ minPrice, maxPrice ],
			slide: function(event, ui) {
				set_slider_label(ui.values[ 0 ], ui.values[ 1 ]);
			},
			change: function(event, ui) {
				if (parseFloat(ui.values[0]) == min_amt) {
					if (parseFloat(ui.values[1]) > max_amt) {
						visibility = ':hidden';
					} else {
						visibility = ':visible';
					}
				} else {
					if (parseFloat(ui.values[0]) < min_amt) {
						visibility = ':hidden';
					} else {
						visibility = ':visible';
					}
				}
				filter_row_origin_marker(visibility);
			}
		});
		set_slider_label(minPrice, maxPrice);
	}

	function set_slider_label(val1, val2)
	{
		$( "#amount" ).text( sliderCurrency + val1 + " - "+ sliderCurrency + val2);
	}

	function enable_travel_refine(core_list)
	{
		var list = '<ul class="locationul">';
		//travel-name
		if ($.isEmptyObject(core_list) == false) {
			$.each(core_list, function(k, v) {
				list += '<li><div class="squaredThree">';
				list += '<input type="checkbox" id="airline-filter-'+k+'" class="airlinecheckbox travel-box" value="'+v+'"><label for="airline-filter-'+k+'"></label>';
				list += '</div>';
				list += '<label class="lbllbl" for="airline-filter-'+k+'"> '+v+'</label></li>';
			});
		}
		list += '</ul>';
		//$('.travel-refine:first').append(list);
		$('.travel-refine:first').html(list);
	}

	function enable_bus_type_refine(core_list, core_count_list)
	{
		var list = '<ul class="locationul">';
		//bus-type
		if ($.isEmptyObject(core_list) == false) {
			$.each(core_list, function(k, v) {
				list += '<li><div class="squaredThree">';
				list += '<input type="checkbox" id="airline-filter-'+k+'" class="airlinecheckbox bus-type-box" value="'+v+'"><label for="airline-filter-'+k+'"></label>';
				list += '</div>';
				list += '<label class="lbllbl" for="airline-filter-'+k+'"> '+v+'('+(parseInt(core_count_list[v])+1)+') </label></li>';
			});
		}
		list += '</ul>';
		//$('.bustype-refine:first').append(list);
		$('.bustype-refine:first').html(list);
	}

	function timeConverter(UNIX_timestamp){
		var a = new Date(parseFloat(UNIX_timestamp));
		var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
		var month = months[a.getMonth()];
		var year = a.getFullYear();
		var date = a.getDate();
		var hour = a.getHours();
		var min = a.getMinutes();
		var time = date+' '+month+' '+hour+':'+min;
		return time;
	}
	//Refine data result
	$(document).on('change', '.bus-type-box, .travel-box', function() {
		filter_row_origin_marker();
	});
	$(document).on('change', '#departureTimeWrapper .time-wrapper.enabled .time-category, #arrivalTimeWrapper .time-wrapper.enabled .time-category', function() {
		filter_row_origin_marker();
	});
	function filter_row_origin_marker()
	{
		loader();
		visibility = '';
		//get all the search criteria
		var bus_type_refine = $('.bus-type-box:checked:not(:disabled)', '.bustype-refine').map(function() {
			return this.value;
		}).get()
		var travel_refine = $('.travel-box:checked:not(:disabled)', '.travel-refine').map(function() {
			return this.value;
		}).get();
		var deptimeList = $('.time-category:checked:not(:disabled)', '#departureTimeWrapper').map(function() {
			return parseInt(this.value);
		}).get();
		var arrtimeList = $('.time-category:checked:not(:disabled)', '#arrivalTimeWrapper').map(function() {
			return parseInt(this.value);
		}).get();
		var min_price = parseFloat($("#slider-range-1").slider("values")[0]);
		var max_price = parseFloat($("#slider-range-1").slider("values")[1]);
		$('.r-r-i'+visibility).each(function(key, value) {
			//
			if (
				(travel_refine.length == 0 || $.inArray($('.travel-name', this).text(), travel_refine) != -1) &&
				(bus_type_refine.length == 0 || has_bus_type_attribute(bus_type_refine, this)) &&

				((deptimeList.length == 0) || ($.inArray(parseInt($('.departure_datetime:first', this).data('departure-category')), deptimeList) != -1)) &&
				((arrtimeList.length == 0) || ($.inArray(parseInt($('.arrival_datetime:first', this).data('arrival-category')), arrtimeList) != -1)) &&

				(parseFloat($('.bus-price:first', this).text()) >= min_price && parseFloat($('.bus-price:first', this).text()) <= max_price) 
			) {
				$(this).show();
		} else {
			$(this).hide();
		}
	});
		var api_count_val = 0;
		update_total_count_summary(api_count_val);
	}

	function has_bus_type_attribute(bus_type_refine, record_origin)
	{
		var status = false; var firstval = ""; var secondval = "";
		if(bus_type_refine.indexOf(ac_sleeper) > -1)
		{
			var filter_value  = bus_type_refine[bus_type_refine.indexOf(ac_sleeper)];
			var multivals = filter_value.split(" ");
			firstval = multivals[0]; secondval = multivals[1];
			if(bus_type_refine.indexOf(firstval) == -1)
			{
				bus_type_refine.push(firstval);
			}
			if(bus_type_refine.indexOf(secondval) == -1)
			{
				bus_type_refine.push(secondval);
			}
			bus_type_refine = bus_type_refine.splice(bus_type_refine.indexOf(ac_sleeper), 1);
		}
		if(bus_type_refine.indexOf(non_ac_sleeper) > -1)
		{
			var filter_value = bus_type_refine[bus_type_refine.indexOf(non_ac_sleeper)];
			var multivals = filter_value.split(" ");
			firstval = multivals[0]; secondval = multivals[1];
			if(bus_type_refine.indexOf(firstval) == -1)
			{
				bus_type_refine.push(firstval);
			}
			if(bus_type_refine.indexOf(secondval) == -1)
			{
				bus_type_refine.push(secondval);
			}
			bus_type_refine = bus_type_refine.splice(bus_type_refine.indexOf(non_ac_sleeper), 1);
		}
		if (bus_type_refine.length == 1) {
			$('.bus-type', record_origin).each(function(k, v) {
				//check anyone occurance
				if ($.inArray($(this).text(), bus_type_refine) != -1) {
					status = true;
				}
			});
		} else if (bus_type_refine.length > 1) {
			//check exact match
			status = true;
			var _bus_type = $('.bus-type', record_origin).map(function() {
				return $(this).text();
			}).get();
			$.each(bus_type_refine, function(k, v) {
				if ($.inArray(v, _bus_type) == -1) {
					status = false;
				}
			});
		}
		return status;
	}

	$(document).on('click', ".more-bus-content-btn", function(){
		$(".more-bus-content-container", $(this).closest('.r-r-i')).toggle();
	});

	//price sort
	$(".price-l-2-h").click(function(){
		loader();
		$(this).addClass('hide');
		$('.price-h-2-l').removeClass('hide');
		$("#bus_search_result .r-w-g").jSort({sort_by: '.bus-price:first', item: '.r-r-i', order: 'asc', is_num: true });
	});

	$(".price-h-2-l").click(function(){
		$(this).addClass('hide');
		$('.price-l-2-h').removeClass('hide');
		$("#bus_search_result .r-w-g").jSort({sort_by: '.bus-price:first', item: '.r-r-i', order: 'desc', is_num: true});
	});

	//name sort
	$(".travel-l-2-h").click(function(){
		$(this).addClass('hide');
		$('.travel-h-2-l').removeClass('hide');
		$("#bus_search_result .r-w-g").jSort({sort_by: '.travel-name:first', item: '.r-r-i', order: 'asc', is_num: false});
	});

	$(".travel-h-2-l").click(function(){
		$(this).addClass('hide');
		$('.travel-l-2-h').removeClass('hide');
		$("#bus_search_result .r-w-g").jSort({sort_by: '.travel-name:first', item: '.r-r-i', order: 'desc', is_num: false});
	});


	//departure sort
	$(".departure-l-2-h").click(function(){
		$(this).addClass('hide');
		$('.departure-h-2-l').removeClass('hide');
		$("#bus_search_result .r-w-g").jSort({sort_by: '.departure-time:first', item: '.r-r-i', order: 'asc', is_num: true});
	});

	$(".departure-h-2-l").click(function(){
		$(this).addClass('hide');
		$('.departure-l-2-h').removeClass('hide');
		$("#bus_search_result .r-w-g").jSort({sort_by: '.departure-time:first', item: '.r-r-i', order: 'desc', is_num: true});
	});

	//arrival name sort
	$(".arrival-l-2-h").click(function(){
		$(this).addClass('hide');
		$('.arrival-h-2-l').removeClass('hide');
		$("#bus_search_result .r-w-g").jSort({sort_by: '.arrival-time:first', item: '.r-r-i', order: 'asc', is_num: true});
	});

	$(".arrival-h-2-l").click(function(){
		$(this).addClass('hide');
		$('.arrival-l-2-h').removeClass('hide');
		$("#bus_search_result .r-w-g").jSort({sort_by: '.arrival-time:first', item: '.r-r-i', order: 'desc', is_num: true});
	});


	//seats sort
	$(".seat-l-2-h").click(function(){
		$(this).addClass('hide');
		$('.seat-h-2-l').removeClass('hide');
		$("#bus_search_result .r-w-g").jSort({sort_by: '.travel-duration:first', item: '.r-r-i', order: 'asc', is_num: true});
	});

	$(".seat-h-2-l").click(function(){
		$(this).addClass('hide');
		$('.seat-l-2-h').removeClass('hide');
		$("#bus_search_result .r-w-g").jSort({sort_by: '.travel-duration:first', item: '.r-r-i', order: 'desc', is_num: true});
	});

	//seats sort
	$(".bus-type-l-2-h").click(function(){
		$(this).addClass('hide');
		$('.bus-type-h-2-l').removeClass('hide');
		$("#bus_search_result .r-w-g").jSort({sort_by: '.bus-type-count:first', item: '.r-r-i', order: 'asc', is_num: true});
	});

	$(".bus-type-h-2-l").click(function(){
		$(this).addClass('hide');
		$('.bus-type-l-2-h').removeClass('hide');
		$("#bus_search_result .r-w-g").jSort({sort_by: '.bus-type-count:first', item: '.r-r-i', order: 'desc', is_num: true});
	});

	//available seats sort
	$(".seatli-l-2-h").click(function(){
		$(this).addClass('hide');
		$('.seatli-h-2-l').removeClass('hide');
		$("#bus_search_result .r-w-g").jSort({sort_by: '.available-seats:first', item: '.r-r-i', order: 'asc', is_num: true});
	});

	$(".seatli-h-2-l").click(function(){
		$(this).addClass('hide');
		$('.seatli-l-2-h').removeClass('hide');
		$("#bus_search_result .r-w-g").jSort({sort_by: '.available-seats:first', item: '.r-r-i', order: 'desc', is_num: true});
	});

	$('.loader').on('click', function(e) {
		e.preventDefault();
		loader();
	});
	$(document).on('click', '.reset-page-loader', function(e) {
		e.preventDefault();
		loader();
		location.reload();
	});
	//Reset the filters -- Balu A
	$(document).on('click', '#reset_filters', function() {
		loader();
		var minPrice = $('#core_minimum_range_value', '#core_min_max_slider_values').val();
		var maxPrice = $('#core_maximum_range_value', '#core_min_max_slider_values').val();

		$("#slider-range-1").slider("option", "values", [minPrice, maxPrice]);
		//Reset the Bus type, and operator
		$('input.bus-type-box, input.time-category, input.travel-box').prop('checked', false);
		//remove active classes
		$('.enabled','#departureTimeWrapper').removeClass('active');
		$('.enabled','#arrivalTimeWrapper').removeClass('active');
		set_slider_label(min_amt, max_amt);
		filter_row_origin_marker();
	});
	function loader(selector)
	{
		selector = selector || '#bus_search_result';
		$(selector).animate({'opacity':'.1'});
		setTimeout(function() {$(selector).animate({'opacity':'1'}, 'slow');}, 1000);
	}

	//activate bus booking
	$(document).on('click', '.inner-summary-btn', function(e) {
		e.preventDefault();
		var _inner_summary_toggle = $('.inner-summary-toggle', $(this).closest('.r-r-i'));
		_inner_summary_toggle.toggle();
		//update data if visible
		if (_inner_summary_toggle.is(':visible')) {
			//load data
			get_inner_bus_details($(this).closest('form').serializeArray(), _inner_summary_toggle);
		} else {
			$('.room-summ', _inner_summary_toggle).html('').hide();
			$('.loader-image', _inner_summary_toggle).show();
		}
	});
	// if ($(window).width() < 550) {
	// 	$(document).on('click', '.busrows', function(e) {
		
	// 		e.preventDefault();
	// 		var _inner_summary_toggle = $('.inner-summary-toggle', $(this).closest('.r-r-i'));
	// 		_inner_summary_toggle.toggle();
	// 		//update data if visible
	// 		if (_inner_summary_toggle.is(':visible')) {
	// 			//load data
	// 			get_inner_bus_details($('.book-form').serializeArray(), _inner_summary_toggle);
	// 		} else {
	// 			$('.room-summ', _inner_summary_toggle).html('').hide();
	// 			$('.loader-image', _inner_summary_toggle).show();
	// 		}
	// 	});
	// }
	function get_inner_bus_details(params, result_row_index)
	{
		// alert(result_row_index);
		$.ajax({
			type: 'POST',
			url: app_base_url+'index.php/ajax/get_bus_details',
			async: true,
			cache: false,
			data: params,
			success: function(result) { 
				console.log(result.data);
					$('.room-summ', result_row_index).html(result.data);
					$('.room-summ', result_row_index).show();
					$('.loader-image', result_row_index).hide();
			}
		});
	}

	//activate bus booking
	$(document).on('click', '.bus-info-btn', function(e) {
		e.preventDefault();
		//update data if visible
		//load data
		clean_up_info_modal();
		var _bus_info_data = get_inner_bus_information($('.book-form', $(this).closest('.r-r-i')).serializeArray());
	});

	function clean_up_info_modal()
	{
		$('#bus-info-modal-content ').empty();
		$('#bus-info-modal .loader-image').show();
		$('#bus-info-modal').modal();
	}
	function get_inner_bus_information(params)
	{
		$.ajax({
			type: 'POST',
			url: app_base_url+'index.php/ajax/get_bus_information',
			async: true,
			cache: true,
			data: params,
			success: function(result) {
				$('#bus-info-modal .loader-image').hide();
				if (result.status) {
					$('#bus-info-modal-content').html(result.data);
				} else {
					$('#bus-info-modal-content').html('NA');
				}
			}
		});
	}
	
		/**
	* Toggle active class to highlight current applied sorting
	**/
	$(document).on('click', '.sorta', function(e) {
		e.preventDefault();
		$(this).closest('.sortul').find('.active').removeClass('active');
		//Add to sibling
		$(this).siblings().addClass('active');
	});
	
	//Filter toggle
	$('.toglefil').click(function() {
			$(this).toggleClass('active');
		});

	//Boarding point info
	$(document).on('click', '.bus-boarding-info-btn', function(e) {
		e.preventDefault();
		//update data if visible
		//load data
		clean_up_boarding_modal();
		var _target_view = $(this).data('target');
		var _bus_info_data = get_board_bus_information($('.book-form', $(this).closest('.r-r-i')).serializeArray(), _target_view);
	});

	function clean_up_boarding_modal()
	{
		$('#bus-boarding-modal-content ').empty();
		$('#bus-boarding-modal .loader-image').show();
		$('#bus-boarding-modal').modal();
	}
	function get_board_bus_information(params, _target_view)
	{
		$.ajax({
			type: 'POST',
			url: app_base_url+'index.php/ajax/get_bus_details/true',
			async: true,
			cache: true,
			data: params,
			success: function(result) {
				if (result.status) {
					$('#bus-boarding-modal-content').html(result.data);
					$('#bus-boarding-modal-content').children().hide();
					$(_target_view).show();
				} else {
					$('#bus-boarding-modal-content').html('NA');
				}
				$('#bus-boarding-modal .loader-image').hide();
			}
		});
	}
	
	/*	Mobile Filter	*/
	$('.filter_tab').click(function() {
		$('.resultalls').stop( true, true ).toggleClass('open');
		$('.coleft').stop( true, true ).slideToggle(500);
	});
	
	var widowwidth = $(window).width();
	if(widowwidth < 991)
	{
	$('.resultalls.open #bus_search_result').on('click',function() {
		$('.resultalls').removeClass('open');
		$('.coleft').slideUp(500);
	});
	}
});
