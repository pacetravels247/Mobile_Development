$(document).ready(function() {
		//pre_load_audio();
		show_result_pre_loader();
		var _active_booking_source = JSON.parse(document.getElementById('pri_active_source').value);
		var _search_id = document.getElementById('pri_search_id').value;
		var _airline_lg_path = document.getElementById('pri_airline_lg_path').value;
		var template_image_path = document.getElementById('pri_template_image_path').value;
		var _search_params = JSON.parse(document.getElementById('pri_search_params').value);
		var default_currency = document.getElementById('pri_def_curr').value;
		var sliderCurrency = default_currency;
		/** PAGE FUNCTIONALITY STARTS HERE **/
		var application_preference_currency = document.getElementById('pri_preferred_currency').value;
		if (document.getElementById('pri_trip_type').value == true) {
			function fare_carousel()
			{
				var item_length = $('#farecal .item').length;
				if (item_length > 6) {
					item_length = 6;
				} else {
					item_length = item_length-1;
				}
				$("#farecal").owlCarousel({
					items : item_length,
					itemsDesktop : [1200, 4],
					itemsDesktopSmall : [991,3],
					itemsTablet: [600, 2],
					itemsMobile: [479, 1],
					navigation: true,
					pagination: false,
					autoPlay : false,
					autoplayTimeout : 3000,
					autoplayHoverPause : true
				});
			}
			/**
			 *Load Flight - Source Trigger
			 */
			function load_flight_fare_calendar(date) {
				$.each(_active_booking_source, function(k, booking_source_name) {
					if(booking_source_name == 'PTBSID0000000002'){
						core_flight_fare_loader(booking_source_name, date);
					}
					
				});
			}
			/**
			*Update fare day wise 
			*/
			function load_flight_day_fare(current_date, session_id, thisRef) {
				$.each(_active_booking_source, function(k, booking_source_name) {
					core_flight_day_fare_loader(booking_source_name, current_date, session_id, thisRef);
				});
			}
	
			/**
			 *Load Flight Fare Core Ajax
			 */
			function core_flight_fare_loader(booking_source_id) {
				var search_params = {};
				search_params['search_id'] = _search_id;
				$.ajax({
					type: 'GET',
					url: app_base_url+'index.php/ajax/puls_minus_days_fare_list/'+booking_source_id+'?'+jQuery.param(search_params),
					async: true,
					cache: true,
					dataType: 'json',
					success: function(result) {
						if (result.hasOwnProperty('status') == true && result.status == true) {
							activate_fare_carousel(result.data.day_fare_list);
						} else {
							//Fare Calendar not available
							$('#fare_calendar_wrapper').remove();
						}
					}
				});
			}
			//load carousel data for fare calendar
			function activate_fare_carousel(list)
			{
				var imgPath = _airline_lg_path;
				var data_list = '';
				$.each(list, function(k, v) {
					
					data_list += '<div class="item" title="'+v.tip+'">';
						data_list += '<a class="pricedates add_days_todate" data-journey-date="'+v.start+'">';
						data_list += '<div class="imgemtrx_plusmin"><img  alt="Flight" src="'+imgPath+v.data_id+'.gif"></div>';
						data_list += '<div class="alsmtrx">';
						data_list += '<strong>'+v.start_label+'</strong>';
						data_list += '<span class="mtrxprice">'+v.title+'</span>';
						data_list += '</div>';
						data_list += '</a>';
					data_list += '</div>';
				});
				$('#farecal').html(data_list);
				fare_carousel();
			}
			function core_flight_day_fare_loader(booking_source_id, departure, session, thisRef) {
				if (session != '') {
					$('.result-pre-loader').show();
					var search_params = _search_params;
					search_params['session_id'] = session;
					search_params['depature'] = departure || search_params['depature'];
					departure = search_params['depature'];
					$.ajax({
						type: 'POST',
						url: app_base_url+'index.php/ajax/day_fare_list/'+booking_source_id+'?'+jQuery.param(search_params),
						async: false,
						cache: true,
						dataType: 'json',
						success: function(result) {
							$('.result-pre-loader').hide();
							if (result.hasOwnProperty('status') == true && result.status == true) {
								fare_cache[result.data.departure] = result.data.day_fare_list;
								fare_session[result.data.departure] = result.next_search;
								update_fare_calendar_events(result.data.departure);
							} else {
								//No Result Found
								alert(result.msg);
							}
							hide_result_pre_loader();
						}
					});
				}
			}
	
			//Balu A
			$(document).on('click', '.add_days_todate', function(){
				var new_date = $(this).data('journey-date');
				var search_id = _search_id;
				window.location.href = app_base_url+'flight/add_days_todate?search_id='+search_id+'&new_date='+new_date;
			});
			load_flight_fare_calendar();
		}
	
		window.process_result_update = function(result,total_api_count,api_count) {
                    //alert(api_count);
			$('.loader-image').hide();
			api_result_counter++
			if (result.hasOwnProperty('status') == true && result.status == true) {
				if($('.round-domestk').length > 0){
					var t_w_one = $(result.data).find('#t-w-i-1').html();
					$('#t-w-i-1').append(t_w_one);
					var t_w_two = $(result.data).find('#t-w-i-2').html();
					$('#t-w-i-2').append(t_w_two);
				}else{
					var one_way_only = $(result.data).find('.one_way_only').html();
					if((one_way_only != "" || one_way_only != undefined) && api_result_counter > 1 && 
						($('.r-r-i:first').index() != -1))
					{
						$('.one_way_only').append(one_way_only);	
					}
					else
						$('#flight_search_result').append(result.data);	
				}
				//post_load_audio();
				updateFilters(api_result_counter);
				//filter_row_origin_marker();
				update_total_count_summary();
				default_col2x(total_api_count);
				hide_result_pre_loader();
				$('.r-r-i:first').show();
			} else {
				// alert(api_result_counter);
				// alert(total_api_count);
				//No Result Found
				if(total_api_count == api_result_counter){
					
					check_empty_search_result();
				}
			}
		};
		function check_empty_search_result()
		{
			if ($('.r-r-i:first').index() == -1) {
				$('#empty-search-result').show();
				$('#page-parent').hide();
			}
		}
	
		function toggle_more_details() {
			$(document).on('click', '.iti-btn', function(e) {
				e.preventDefault();
				var tab_id = $(this).data('id');
				$('#'+tab_id).provabPopup({
					modalClose: true,
					zIndex: 10000005,
					closeClass: 'closepopup'
				});
			});
		}
	
		var fare_details_cache = {};
	
		function toggle_fare_details() {
			//Fare Rules
			$(document).on('click', ".iti-fare-btn", function(e) {
				e.preventDefault();
				var _tmp_result_form_index = $('#'+$(this).data('form-id'));
				var _tmp_result_row_index = $(this).closest('.propopum');
				var _data_access_key = $('.data-access-key', _tmp_result_form_index).val();
				var _booking_source = $('.booking-source', _tmp_result_form_index).val();
				var _search_access_key = $('.search-access-key', _tmp_result_form_index).val();
				var _provab_auth_key = $('.provab-auth-key', _tmp_result_form_index).val(); //
	
				
				$('.loader-image', _tmp_result_row_index).show();
				//$('.i-i-f-s-t', _tmp_result_row_index).toggle();
				if ((_data_access_key in fare_details_cache) == false) {
					set_fare_details(_data_access_key, _booking_source, _search_access_key, _provab_auth_key, _tmp_result_row_index);
				} else {
					show_fare_result(_tmp_result_row_index, fare_details_cache[_data_access_key]);
				}
			});
		}
	
		function show_fare_result(result_row_index, data)
		{
			$('.i-s-s-c', result_row_index).html(data);
			$('.loader-image', result_row_index).hide();
		}
	
		//set fare details cache
		function set_fare_details(data_access_key, source, search_access_key, provab_auth_key, _tmp_result_row_index) {
			var params = {
				data_access_key: data_access_key,
				booking_source: source,
				search_access_key: search_access_key,
				provab_auth_key: provab_auth_key
			};
			$.ajax({
				type: 'POST',
				url: app_base_url+'index.php/ajax/get_fare_details',
				async: true,
				cache: false,
				data: $.param(params),
				dataType: 'json',
				success: function(result) {
					if (result.hasOwnProperty('status') == true && result.status == true) {
						fare_details_cache[data_access_key] = result.data;
					} else {
						fare_details_cache[data_access_key] = result.msg;
					}
					show_fare_result(_tmp_result_row_index, fare_details_cache[data_access_key]);
				}
			});
		}
		toggle_more_details();
		toggle_fare_details();
		function updateFilters(api_result_count) {
			// alert(api_result_count);
			//update filters
			var temp_minimum_record = $('.r-r-i:first');
			var airlineList = {};
			var stopCountList = {};
			var departureCategoryList = {};
			var arrivalCategoryList = {};
	
			var minPrice = $('.price:first', temp_minimum_record).data('price');
			var maxPrice = $('.price:first', temp_minimum_record).data('price');
			var minDepartureDatetime = $('.dep_dt:first', temp_minimum_record).data('datetime');
			var maxDepartureDatetime = $('.dep_dt:first', temp_minimum_record).data('datetime');

			var original_minPrice = $('.price:first', temp_minimum_record).data('api_offered_fare');
			var original_maxPrice = $('.price:first', temp_minimum_record).data('api_offered_fare');

			var price = 0;
			var time = 0;
			var stopcount = 0;
			var depCat = 0;
			var arrCat = 0;

			var cloneList = {};
			var airline = '';
			var cloneCriteriaList = {};
			$('.r-r-i').each(function(key, value) {
				original_price = $('.price:first', this).data('original_price');
				price = $('.price:first', this).data('price');
				time = $('.dep_dt:first', this).data('datetime');
				stopcount = parseInt($('.stp:first', this).data('category'));
				airline = $('.a-n:first', this).data('code');
				depCat = parseInt($('.dep_dt:first', this).data('category'));
				arrCat = parseInt($('.arr_dt:last', this).data('category'));
				
				// if(api_result_count == 2){
					if (airlineList.hasOwnProperty(airline) == false) {
						airlineList[airline] = $('.a-n:first', this).text();
						cloneList[airline + 'MINP'] = key;
						cloneCriteriaList[airline + 'MINP'] = price;
					} else {
						if (price < cloneCriteriaList[airline + 'MINP']) {
							cloneCriteriaList[airline + 'MINP'] = price;
							cloneList[airline + 'MINP'] = key;
						}
					}
				// }
				
				if (departureCategoryList.hasOwnProperty(depCat) == false) {
					departureCategoryList[depCat] = depCat;
				}
				if (arrivalCategoryList.hasOwnProperty(arrCat) == false) {
					arrivalCategoryList[arrCat] = arrCat;
				}
				if (stopCountList.hasOwnProperty(stopcount) == false) {
					stopCountList[stopcount] = stopcount;
				}
				if (price < minPrice) {
					minPrice = price;
				}
	
				if (price > maxPrice) {
					maxPrice = price;
				}

				if (original_price < original_minPrice) {
					original_minPrice = original_price;
				}

				if (original_price > original_maxPrice) {
					original_maxPrice = original_price;
				}

			});
			airlineList = getSortedObject(airlineList);
			loadAirlineFilter(airlineList);
			loadPriceRangeSelector(minPrice, maxPrice, original_minPrice, original_maxPrice);

			loadStopFilter(stopCountList);
			loadTimeRangeSelector(departureCategoryList, arrivalCategoryList);
			cloneSlider(cloneList);
			// console.log('anitha');
			// console.log(cloneList);
			$('#core_minimum_range_value', '#core_min_max_slider_values').val(minPrice);
			$('#core_maximum_range_value', '#core_min_max_slider_values').val(maxPrice);
			$("#flight_search_result #t-w-i-1").jSort({
				sort_by: '.f-price:first',
				item: '.r-r-i',
				order: 'asc',
				is_num: true
			});
			var len = $("#flight_search_result #t-w-i-2").length;
			if(len > 0){
				$("#flight_search_result #t-w-i-2").jSort({
					sort_by: '.f-p:first',
					item: '.r-r-i',
					order: 'asc',
					is_num: true
				});
			}
		}
	
		function getArray(objectWrap) {
			var objectWrapValueArr = [];
			$.each(objectWrap, function(key, value) {
				objectWrapValueArr.push(value);
			});
			return objectWrapValueArr;
		}
	
		function getSortedObject(obj) {
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
		
		//Enable/Disable-Time Range Selector
		function loadTimeRangeSelector(depCatList, arrCatList)
		{
			//Works for both onward and return
			if ($.isPlainObject(depCatList)) {
				var depCatListArray = getArray(depCatList);
				var stopCat = 0;
				$('#departureTimeWrapper .time-category').each(function(key, value) {
					depCat = parseInt($(this).val());
					if (depCatListArray.indexOf(depCat) == -1) {
						//disabled
						if((api_result_counter == 1) || (api_result_counter > 1 && !$(this).closest('.time-wrapper').hasClass("enabled")))
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
	
			if ($.isPlainObject(arrCatList)) {
				var arrCatListArray = getArray(arrCatList);
				var arrCat = 0;
				$('#arrivalTimeWrapper .time-category').each(function(key, value) {
					arrCat = parseInt($(this).val());
					if (arrCatListArray.indexOf(arrCat) == -1) {
						//disabled
						if((api_result_counter == 1) || (api_result_counter > 1 && !$(this).closest('.time-wrapper').hasClass("enabled")))
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
	
		//STOP FILTER
		function loadStopFilter(stopCountList) {
			//Enable / Disable stopcountcheckbox
			if ($.isPlainObject(stopCountList)) {
				var stopCountListArray = getArray(stopCountList);
				var stopCat = 0;
				$('#stopCountWrapper .stopcount').each(function(key, value) {
					stopCat = parseInt($(this).val());
					if (stopCountListArray.indexOf(stopCat) == -1) {
						//disabled
						if((api_result_counter == 1) || (api_result_counter > 1 && !$(this).closest('.stop-wrapper').hasClass('disabled')))
						{
							$(this).attr('disabled', 'disabled');
							$(this).closest('.stop-wrapper').addClass('disabled');
						}
					} else {
						$(this).removeAttr('disabled');
						$(this).closest('.stop-wrapper').removeClass('disabled');
						$(this).closest('.stop-wrapper').addClass('enabled');
					}
				});
			}
		}
		
		//AIRLINE FILTER
		function loadAirlineFilter(airlineList) {
			var imgPath = _airline_lg_path;
			var filterList = '';
			var index = 1;
			if ($('.a-n').length > 0) {
				$.each(airlineList, function(key, value) {
					filterList += '<li>';
					filterList += '<div class="squaredThree">';
					filterList += '<input type="checkbox" value="' + key + '" name="check" class="airlinecheckbox" id="squaredThree'+index+'">';
					filterList += '<label for="squaredThree'+index+'"></label>';
					filterList += '</div>';
					filterList += '<label for="squaredThree'+index+'" class="lbllbl">'+value+'</label>';
					filterList += '</li>';
					index++;
				});
			} else {
				filterList += '<li></li>';
			}
			$('#allairlines').html('<ul class="locationul">' + filterList + '</ul>');
		}

		var minDefaultPrice = maxDefaultPrice = '';
		var min_amt = 0;
		var max_amt = 0;
		function loadPriceRangeSelector(minPrice, maxPrice, original_minPrice='', original_maxPrice='') {
			min_amt = minPrice;
			max_amt = maxPrice;
			minDefaultPrice = minPrice;
			maxDefaultPrice = maxPrice;
			$("#slider-range").slider({
				range: true,
				min: minPrice,
				max: maxPrice,
				animate: "slow",
				values: [minPrice, maxPrice],
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
					filter_row_origin_marker();
				}
			});
			set_slider_label(minPrice, maxPrice, original_minPrice, original_maxPrice);
		}
		function set_slider_label(val1, val2,original_minPrice='',original_maxPrice='')
		{
			$( "#amount" ).html( "<span class='filt-currency' >" +sliderCurrency+" </span>" + "<span class='filt-min_price' data-min_price='"+original_minPrice+"' >" +val1 +" </span>"+ " - "+  "<span class='filt-currency' >" + sliderCurrency+" </span>" +"<span class='filt-max_price' data-max_price='"+original_maxPrice+"' >" + val2+" </span>");

			// $( "#amount" ).text( sliderCurrency + val1 + " - "+ sliderCurrency + val2);
		}
		function cloneSlider(slides) {
			//alert($('#flight_search_result .r-r-i:nth-child(10) .a-n').text());
			var cloneList = {};
			var list = '';
			$.each(slides, function(key, value) {
				cloneList[(value + 1)] = value + 1;
			});
			var airline_name = '';
			var airline_code = '';
			var airline_image = '';
			var airline_price = 0;
			$.each(cloneList, function(k, v) {
				airline_name = $('#flight_search_result .r-r-i:nth-child(' + k + ') .a-n:first').text();
				airline_code = $('#flight_search_result .r-r-i:nth-child(' + k + ') .a-n:first').data('code');
				//airline_price = $('#flight_search_result .r-r-i:nth-child(' + k + ') .f-p:first').text();
				airline_price = $('#flight_search_result .r-r-i:nth-child(' + k + ') .price:first').data('price');//Balu A
				airline_original_price = $('#flight_search_result .r-r-i:nth-child(' + k + ') .price:first').data('api_offered_fare');//Balu A
				airline_image = $('#flight_search_result .r-r-i:nth-child(' + k + ') .airline-logo:first').attr('src');
				if(airline_code != undefined){
				list += '<div class="item hand-cursor">';
					list += '<div class="airlinesd">';
						list += '<input type="checkbox" class="airline-slider" value="'+airline_code+'">';
						list += '<div class="imgemtrx"><img  alt="Flight" src="' + airline_image + '"></div>';
						list += '<div class="alsmtrx">';
							list += '<strong>' + airline_name + '</strong>';
							list += '<span class="mtrxprice"><span class="clone_side_org_price hide">'+airline_original_price+'</span><span class="clone_side_curr">' +sliderCurrency+'</span> <span class="clone_side_price">'+airline_price + '</span></span>';
						list += '</div>';
						//Important For Clone
						//list += '<div class="cloneForm" style="display:none;">';
							//list += $('#flight_search_result .r-r-i:nth-child(' + k + ') .form-wrapper').html();
						//list += '</div>';
					list += '</div>';
				list += '</div>';
				}
			});
			//$("#arlinemtrx.owl-carousel").html(list);
			$("#airlinematrix-wrapper").empty();
			$('#airlinematrix-wrapper').html('<div id="arlinemtrx" class="owl-carousel matrixcarsl"></div>');
			$("#arlinemtrx.owl-carousel").html(list);
			carousel();
		}
	
		//$(document).on('click', '.carousel-booking', function(e) {
			//e.preventDefault();
			//$('.book-form-wrapper', this).submit();
		//});
		function timeConverter(UNIX_timestamp) {
			var a = new Date(UNIX_timestamp);
			var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
			var month = months[a.getMonth()];
			var year = a.getFullYear();
			var date = a.getDate();
			var hour = a.getHours();
			var min = a.getMinutes();
			var time = date + ' ' + month + ' ' + hour + ':' + min;
			return time;
		}
	
		//$('.airline-slider[value="6E"]')
		$(document).on('click', 'input.airline-slider', function() {
			//Set all IP with value as checked
			//$(this).closest('.item').toggleClass('active');
                        
			$('.airline-slider[value="'+$(this).val()+'"]').closest('.item').toggleClass('active');//carousel-- Balu A
			
			toggle_airline_select_matrix($(this).val(), $(this).is(':checked'));
			filter_row_origin_marker();
		});
                function toggle_airline_select_matrix(airline_code, checked)
		{
                    
                       // $('.airlinecheckbox').prop('checked', false);
			if (checked == true) {
				$('.airline-slider[value="'+airline_code+'"], .airlinecheckbox[value="'+airline_code+'"]').prop('checked', 'checked');
			} else {
				$('.airline-slider[value="'+airline_code+'"], .airlinecheckbox[value="'+airline_code+'"]').prop('checked', false);
			}
		}
                $(document).on('click', 'input.airlinecheckbox', function() {
			//Set all IP with value as checked
			//$(this).closest('.item').toggleClass('active');
                        
			$('.airline-slider[value="'+$(this).val()+'"]').closest('.item').toggleClass('active');//carousel-- Balu A
			
			toggle_airline_select($(this).val(), $(this).is(':checked'));
			filter_row_origin_marker();
		});
                function toggle_airline_select(airline_code, checked)
		{
			if (checked == true) {
				$('.airline-slider[value="'+airline_code+'"], .airlinecheckbox[value="'+airline_code+'"]').prop('checked', 'checked');
			} else {
				$('.airline-slider[value="'+airline_code+'"], .airlinecheckbox[value="'+airline_code+'"]').prop('checked', false);
			}
		}
		
		$(document).on('change', '.ref_nref-wrapper input.ref_nref, #stopCountWrapper .stop-wrapper.enabled .stopcount, #departureTimeWrapper .time-wrapper.enabled .time-category, #arrivalTimeWrapper .time-wrapper.enabled .time-category', function() {
			filter_row_origin_marker();
		});
	
		function carousel() {
			$("#arlinemtrx.owl-carousel").owlCarousel({
				items : 5,
				itemsDesktop : [1200, 3],
				itemsDesktopSmall : [991,3],
				itemsTablet: [600, 2],
				itemsMobile: [479, 1],
				navigation: true,
				pagination: false,
				
				autoPlay : true,
				autoplayTimeout : 3000,
				autoplayHoverPause : true
			});
		}
		//Reset the filters -- Balu A
		$(document).on('click', '#reset_filters', function() {
			loader();
			var minPrice = $('#core_minimum_range_value', '#core_min_max_slider_values').val();
			var maxPrice = $('#core_maximum_range_value', '#core_min_max_slider_values').val();
	
			$("#slider-range").slider("option", "values", [minPrice, maxPrice]);
				
			//Reset the No. of stops, departure time,arrival time, and airlines
			$('input.airlinecheckbox, input.stopcount, input.time-category').prop('checked', false);
			//remove active classes
			$('.enabled','#departureTimeWrapper').removeClass('active');
			$('.enabled','#arrivalTimeWrapper').removeClass('active');
			$('.enabled','#stopCountWrapper').removeClass('active');
			//Reset the carousel
			$('.owl-item', '.owl-wrapper').each(function() {
				if ($(this).find('.item').hasClass('active') == true) {
					$(this).find('.item').removeClass('active');
					$(this).find('.airline-slider:checked').prop('checked', false);
				}
			});
			set_slider_label(min_amt, max_amt);
			filter_row_origin_marker();
		});
		$(document).on('click', '.airlineall', function() {
			$('input.airlinecheckbox, input.stopcount, input.time-category').prop('checked', false);
			$('.owl-item', '.owl-wrapper').each(function() {
				if ($(this).find('.item').hasClass('active') == true) {
					$(this).find('.item').removeClass('active');
					$(this).find('.airline-slider:checked').prop('checked', false);
				}
			});
			filter_row_origin_marker();
		});
		function loader(selector) {
			selector = selector || '#flight_search_result';
			$(selector).animate({
				'opacity': '.1'
			});
			setTimeout(function() {
				$(selector).animate({
					'opacity': '1'
				}, 'slow');
			}, 1000);
		}
	
		/**
		*Filter function for loaded Row Origin Marker
		*/
		function filter_row_origin_marker() {
			loader();
			visibility = '';
			//get all the search criteria
			var stopCountList = $('input.stopcount:checked:not(:disabled)', '#stopCountWrapper').map(function() {
				return parseInt(this.value);
			}).get();
			var airlineList = $('input.airlinecheckbox:checked:not(:disabled)', '#allairlines').map(function() {
				return this.value;
			}).get();
			
			var deptimeList = $('.time-category:checked:not(:disabled)', '#departureTimeWrapper').map(function() {
				return parseInt(this.value);
			}).get();
			var arrtimeList = $('.time-category:checked:not(:disabled)', '#arrivalTimeWrapper').map(function() {
				return parseInt(this.value);
			}).get();
			var ref_nref_list = $('.ref_nref:checked', '#ref_nref_chks').map(function() {
				return parseInt(this.value);
			}).get();

			var min_price = parseFloat($("#slider-range").slider("values")[0]);
			var max_price = parseFloat($("#slider-range").slider("values")[1]);
			i=0;
			$('.r-r-i' + visibility).each(function(key, value) {
				if (((airlineList.length == 0) || ($.inArray($('.a-n:first', this).data('code'), airlineList) != -1)) &&
					((stopCountList.length == 0) || ($.inArray(parseInt($('.stp:first', this).data('category')), stopCountList) != -1)) &&
	
					((deptimeList.length == 0) || ($.inArray(parseInt($('.dep_dt:first', this).data('category')), deptimeList) != -1)) &&
					((arrtimeList.length == 0) || ($.inArray(parseInt($('.arr_dt:last', this).data('category')), arrtimeList) != -1)) &&
	
					((ref_nref_list.length == 0) || ($.inArray(parseInt($('.refnref:first', this).data('val')), ref_nref_list) != -1)) &&
					(parseFloat($('.price:first', this).data('price')) >= min_price && parseFloat($('.price:first', this).data('price')) <= max_price)
				) {
					$(this).show();
				} else {
					$(this).hide();
				}
				i++;
			});
			update_total_count_summary();
		}
	
		/**
		 *Update Hotel Count Details
		 */
		function update_total_count_summary() {
			$('#flight_search_result').show();
			var _visible_records = parseInt($('.r-r-i:visible').length);
			var _total_records = $('.r-r-i').length;
			// alert(_total_records);
			if (isNaN(_visible_records) == true || _visible_records == 0) {
				_visible_records = 0;
				//display warning
				$('#flight_search_result').hide();
				$('#empty_flight_search_result').show();
			} else {
				$('#flight_search_result').show();
				$('#empty_flight_search_result').hide();
			}
			$('#total_records').text(_visible_records);
			if(_visible_records == 1){
				$('#flights_text').text('Flight');
			}
			$('.visible-row-record-count').text(_visible_records);
			$('.total-row-record-count').text(_total_records);
			
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
		/**
		* Toggle active class to highlight current applied sorting
		**/
		$(document).on('click', '.sorta', function(e) {
			e.preventDefault();
			$(this).closest('.sortul').find('.active').removeClass('active');
			//Add to sibling
			$(this).siblings().addClass('active');
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
	
		//Handle col2x selector
		$(document).on('click', '.mfb-btn', function(e) {
			e.preventDefault();
			loader('#multi-flight-summary-container');
			loader();
			update_col2x_flight($(this).closest('.r-r-i'), $(this).closest('.r-w-g').attr('id'));
			$('#multi-flight-summary-container').effect('bounce', 'slow');
		});
	
		/**
		 *Update Selected flight details and highlight selected flight
		 */
		function update_col2x_flight(segment, trip_way_indicator) {
			$(segment).closest('.r-w-g').find('.r-r-i.active').removeClass('active');
			$(segment).addClass('active');
			//update flight details
			var _flight_icon = $('.airline-logo:first', segment).attr('src');
			var _flight_name = $('.a-n:first', segment).text();
			var _flight_from_price = $('#flight-from-price').val();
			var _flight_from_bookingsource	= $('#flight-from-bookingsource').val();
			var _flight_to_bookingsource	= $('#flight-to-bookingsource').val();
			var _flight_from_faretype	= $('#flight-from-faretype').val();
			var _flight_to_faretype	= $('#flight-to-faretype').val();
			var _flight_to_price = $('#flight-to-price').val();
	
			var _location_details_html = '<div class="topnavi">';
			_location_details_html += '<div class="col-xs-4 padflt widftysing">';
			_location_details_html += '<span class="flitrlbl elipsetool">'+$('.from-loc:first', segment).text()+'</span>';
			_location_details_html += '</div>';
			_location_details_html += '<div class="col-xs-4 padflt nonefitysing">';
			//_location_details_html += '<span class="arofa fa fa-long-arrow-right"></span>';
			_location_details_html += '</div>';
			_location_details_html += '<div class="col-xs-4 padflt widftysing">';
			_location_details_html += '<span class="flitrlbl elipsetool text_algn_rit">'+$('.to-loc:first', segment).text()+'</span>';
			_location_details_html += '</div></div>';
			var _location_details =  _location_details_html;
	
			var _departure = $('.f-d-t:first', segment).text();
			var _arrival = $('.f-a-t:first', segment).text();
			var _duration = $('.durtntime:first', segment).text();
			var _stop_count = $('.stop-value:first', segment).text();
			var stop_image ='';
			
			var flight_stop_arr =  _stop_count.split(':');
			var flight_stop_count =parseInt(flight_stop_arr[1]);
			
			for (var i = 0; i < 5; i++) {
				if(flight_stop_count==i){
					stop_image = '<img src='+template_image_path+'stop_'+i+'.png alt="stop_image">';	
				}
				
			}
			// console.log("flight_stop_count"+flight_stop_count);
			// console.log("template_image_path"+stop_image);
			if (trip_way_indicator == 't-w-i-1') {
				$(".onward_multi_flight_data").html($(segment).html());
				var i=0;
				$(".onward_multi_flight_data .allsegments").each(function(){
					if(i>0)
					{
						$(this).find(".airline-logo").remove();
						$(this).find(".airlinename").remove();
					}
					i++;
				});
				//t-w-i-1
				// $('#multi-flight-summary-container .departure-flight-icon').attr('src', _flight_icon);
				// $('#multi-flight-summary-container .departure-flight-name').text(_flight_name);
				// $('#multi-flight-summary-container .outbound-details').html(_location_details);
				//_flight_from_price = $('.f-p:first', segment).text();
				// _flight_from_price = $('.price:first', segment).data('price');//Balu A
				// $('#multi-flight-summary-container .outbound-timing-details .departure').text(_departure);
				// $('#multi-flight-summary-container .outbound-timing-details .arrival').text(_arrival);
				
				// $('#multi-flight-summary-container .outbound-timing-details .duration').text(_duration);
				// $('#multi-flight-summary-container .outbound-details .nonefitysing').html(stop_image);
				// $('#multi-flight-summary-container .outbound-timing-details .stop-count').text(_stop_count);
				//$('#multi-flight-summary-container .inbound-timing-details .stop-count').text(10);
				_flight_from_price = $('.price:first', segment).data('price');
				_flight_from_bookingsource = $('.booking_source:first',segment ).text();
				_flight_from_faretype = $('.fare_type:first',segment ).text();
			} else if (trip_way_indicator == 't-w-i-2') {
				$(".return_multi_flight_data").html($(segment).html());
				var i = 0;
				$(".return_multi_flight_data .allsegments").each(function(){
					if(i>0)
					{
						$(this).find(".airline-logo").remove();
						$(this).find(".airlinename").remove();
					}
					i++;
				});
				//t-w-i-2
				// $('#multi-flight-summary-container .arrival-flight-icon').attr('src', _flight_icon);
				// $('#multi-flight-summary-container .arrival-flight-name').text(_flight_name);
				// $('#multi-flight-summary-container .inbound-details').html(_location_details);
				// $('#multi-flight-summary-container .inbound-timing-details .departure').text(_departure);
				// $('#multi-flight-summary-container .inbound-timing-details .arrival').text(_arrival);
	
				// $('#multi-flight-summary-container .inbound-timing-details .duration').text(_duration);
				// $('#multi-flight-summary-container .inbound-details .nonefitysing').html(stop_image);
				// $('#multi-flight-summary-container .inbound-timing-details .stop-count').text(_stop_count);
				//$('#multi-flight-summary-container .inbound-timing-details .stop-count').text(10);
				//_flight_to_price = $('.f-p:first', segment).text();
				_flight_to_price = $('.price:first', segment).data('price');//Balu A
				_flight_to_bookingsource = $('.booking_source:first',segment ).text();
				_flight_to_faretype = $('.fare_type:first',segment ).text();
			}
			$("#multi-flight-summary-container").find(".book-form-wrapper").remove();
			$("#multi-flight-summary-container").find(".hedtowr").remove();
			$("#multi-flight-summary-container").find(".smalairport").remove();
			$("#multi-flight-summary-container").find(".mrinfrmtn").remove();
			$("#multi-flight-summary-container").find(".smalairport_code").show();
			$("#multi-flight-summary-container").find(".airline_code_n_class").remove();
			$("#multi-flight-summary-container").find(".flight_terminal").remove();
			$("#multi-flight-summary-container").find(".stp").remove();
			$("#multi-flight-summary-container").find(".propopum").remove();
			$("#multi-flight-summary-container").find(".snf_hnf").remove();
			$("#multi-flight-summary-container").find(".flight_selector").remove();

			//update flight-price
			$('#flight-from-price').val(_flight_from_price);
			$('#flight-to-price').val(_flight_to_price);
			$('#flight-from-bookingsource').val(_flight_from_bookingsource);
			$('#flight-to-bookingsource').val(_flight_to_bookingsource);
			$('#flight-from-faretype').val(_flight_from_faretype);
			$('#flight-to-faretype').val(_flight_to_faretype);
			$('#multi-flight-summary-container .f-p:last').text((parseFloat(_flight_from_price) + parseFloat(_flight_to_price)).toFixed(2));
			$('#multi-flight-summary-container .currency').text(default_currency);
		}
	
		/**
		 *Get Booking Form Contents
		 */
		function get_booking_form_contents() {
			//run ajax and get update
			var _trip_way_1 = $('#t-w-i-1 .r-r-i.active:first form.book-form-wrapper').serializeArray();
			var _trip_way_2 = $('#t-w-i-2 .r-r-i.active:first form.book-form-wrapper').serializeArray();
			if (jQuery.isEmptyObject(_trip_way_1) == false && jQuery.isEmptyObject(_trip_way_2) == false) {
				return get_combined_booking_from(JSON.stringify(_trip_way_1), JSON.stringify(_trip_way_2));
			} else {
				location.reload();
			}
		}
	
		/**
		 *Combined booking form to be loaded via Ajax
		 */
		function get_combined_booking_from(trip_way_1, trip_way_2) {
			var _result = {};
			var params = {
				trip_way_1: trip_way_1,
				trip_way_2: trip_way_2,
				search_id: _search_id
			};
			$.ajax({
				type: 'POST',
				url: app_base_url+'index.php/ajax/get_combined_booking_from',
				async: false,
				cache: false,
				data: $.param(params),
				dataType: 'json',
				success: function(result) {
					if (result.status == true) {
						_result = result.data;
					} else {
						location.reload();
					}
				}
			});
			return _result;
		}
	
		/**
		 *update default first row as selected
		 */
		function default_col2x(default_col2x) {
			//var col_count = $('.r-w-g').length;
			//if (parseInt(col_count) == 2) {
			var col_count = $('.round-domestk').length;
			if (parseInt(col_count) >= 1) {
				$('#clone-list-container').addClass('hide');
				$('#multi-flight-summary-container').removeClass('hide');
				update_col2x_flight($('#t-w-i-1 .r-r-i:first'), 't-w-i-1');
				update_col2x_flight($('#t-w-i-2 .r-r-i:first'), 't-w-i-2');
			} else {
				//remove double filter
				$('#top-sort-list-wrapper #top-sort-list-2').remove();
				$('#top-sort-list-wrapper').removeClass('addtwofilter');
			}
		}
	
		/**
		 *Create Booking Form on click on book button
		 */
		$(document).on('click', '#multi-flight-booking-btn', function(e) {
			e.preventDefault();
			//update booking form
			var from_booking_source = $('#flight-from-bookingsource').val();
			var to_booking_source = $('#flight-to-bookingsource').val();
			var from_fare_type = $('#flight-from-faretype').val();
			var to_fare_type = $('#flight-to-faretype').val();
			if(from_booking_source != to_booking_source){
				// alert('Please select same airline for onward and return');
				$('#alert_message').text('Please select same airline for onward and return');
				$("#diff_fare_type").modal("show");
				return false;
			}
			else{
				if(from_booking_source == 'PTBSID0000000009'){
					if(from_fare_type != to_fare_type){
						$('#alert_message').text(' Please select same faretype for onward and return');
						$("#diff_fare_type").modal("show");
						return false;
					}
				}
				$(this).attr('disabled', true);
				loader('body');
				$('.round_trip_loader').removeClass('hide');
				
				var _form_contents = get_booking_form_contents();
				$('#trip-way-wrapper').empty().html(_form_contents['form_content']);
				$('#multi-flight-form').attr('action', _form_contents['booking_url']);
				//submit form

				$('#multi-flight-form').submit();

				// $(this).removeAttr('disabled');
				
			}
			
		});
	
		$('.toglefil').click(function() {
			$(this).toggleClass('active');
		});
		/** PAGE FUNCTIONALITY ENDS HERE **/
	
		/** TOBIN Wrote this **/
		/*selection and filter fixed- result*/
	var flterffect = $('.fixincrmnt').offset().top+60;

	$(window).scroll(function() {
			var yPos = $(window).scrollTop();
			if(yPos > flterffect) {
				$('.fixincrmnt, .addtwofilter').addClass('fixed');
			}
			else {
				$('.fixincrmnt, .addtwofilter').removeClass('fixed');
			}
		});
	/*selection fixed- result end*/

	/*  Mobile Filter  */
	$('.filter_tab').click(function() {
		$('.resultalls').stop( true, true ).toggleClass('open');
		$('.coleft').stop( true, true ).slideToggle(500);
	});
	
	var widowwidth = $(window).width();
	if(widowwidth < 651)
	{
			$('.filterforall').removeClass('addtwofilter');
	}
	if(widowwidth < 991)
	{

		$('.resultalls.open .allresult').on('click',function() {
			$('.resultalls').removeClass('open');
			$('.coleft').slideUp(500);
		});
	}
	});
