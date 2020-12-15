$(document).ready(function() {
	enable_sort();
	//$('.loader-image').show();
	//pre_load_audio();
	var _search_id = document.getElementById('pri_search_id').value;
	var _fltr_r_cnt = 0; //Filter Result count
	var _total_r_cnt = 0;//Total Result count
	var _offset = 0;//Offset to load results
	var dynamic_page_data = true;

	var processLazyLoad = function() {
		//check if your div is visible to user
		//CODE ONLY CHECKS VISIBILITY FROM TOP OF THE PAGE
		if (_next_page == true && $('#npl_img').get(0) && $('#npl_img').get(0).scrollHeight != 0) {
			//console.log('Lazy loaded flexible');
			if ($(window).scrollTop() + $(window).height() >= $('#npl_img').get(0).scrollHeight) {
				if(!$('#npl_img').attr('loaded')) {
					_next_page == false;
					//not in ajax.success due to multiple sroll events
					$('#npl_img').attr('loaded', true);
					//ajax goes here
					load_products(process_result_update, _offset, _ini_fil);
				}
			}
		}
	}
	/**
	 * Offset and total records needed for pagination
	 */
	var _next_page = false;
	function ini_pagination()
	{
		//fixme - here
		//console.log(_offset+' offset : '+_fltr_r_cnt+ ' Filter Count '+ _total_r_cnt+ ' Total Count');
		if (_offset >= _fltr_r_cnt && _fltr_r_cnt < _total_r_cnt) {
			_next_page = false;
			//console.log('Filters are applied and all the results are loaded');
			$('#npl_img').hide();
		} else if (_offset < _fltr_r_cnt && _fltr_r_cnt < _total_r_cnt) {
			_next_page = true;
			//console.log('Filters are applied and all the results are not loaded');
			$('#npl_img').show();
		} else if (_offset > _total_r_cnt && _fltr_r_cnt == _total_r_cnt && dynamic_page_data == true) {
			_next_page = false;
			//all data loaded, remove scroll event handler as we no longer need lazy loading of data
			//console.log('No More Records so can activate JS filter and disable pagination');
			//command by ela
			//$(window).unbind('scroll', window, processLazyLoad);
			$('#npl_img').remove();
			dynamic_page_data = false;
			//enable sorting via javascript
			enable_sort();
		} else if (_offset < _fltr_r_cnt) {
			_next_page = true;
			//console.log('More Records are available to load');
			$('#npl_img').show();
		}
	}

	window.process_result_update = function(result) {
		var ___data_r = Date.now();
		$('.loader-image').hide();
		hide_result_pre_loader();
		//alert(result.hasOwnProperty('status'));
		if(result){
			if (result.hasOwnProperty('status') == true && result.status == true) {
				set_total_summary(result.total_result_count, result.filter_result_count, result.offset);
				update_total_count_summary();
				$('#npl_img').removeAttr('loaded');
				if (_offset == 0) {
					//console.log('Data loaded with offset 0');

					$('#tour_search_result').html(result.data);
					//No Result Found
				} else {
					//console.log('Pagination data loaded with offset');
					//$('#tour_search_result').append(result.data);
					$('#tour_search_result').html(result.data);
				}
				if($(".grid_click").hasClass('active')){
                   $('#tour_search_result .item').removeClass('list-group-item');
                    $('#tour_search_result .item').addClass('grid-group-item');
                    $('.rowresult').addClass("col-xs-4");
                    
                }else if($(".list_click").hasClass('active')){
                	$('#tour_search_result .item').addClass('list-group-item');
                    $('#tour_search_result .item').removeClass('grid-group-item');
                    $('.rowresult').removeClass("col-xs-4");
                }

				ini_pagination();
				//Set time out to lazy load images
				lazy_img();
			}else{
				//alert("falsessee");
			}
		}
		
	}
	
	window.ini_result_update = function(result) {
		//post_load_audio();

		if (result.hasOwnProperty('status') == true && result.status == true) {
				if(result.total_result_count >=1){
					update_range_slider(parseInt(result.filters.p.min), parseInt(result.filters.p.max));
				}
				enable_discount_selector();
				//enable_star_wrapper(result.filters.star);
				inif();

			
			//command by ela
			//$(window).on('scroll', processLazyLoad);
		}
		check_empty_search_result();
	}

	/**
	 * Set total result summary
	 */
	function set_total_summary(total_count, fltr_count, offset)
	{
		_fltr_r_cnt = parseInt(fltr_count);//visible 
		_total_r_cnt = parseInt(total_count);//total
		_offset = parseInt(offset);
	}
	
	function lazy_img()
	{
		$("img.lazy").lazy();
	}

	function check_empty_search_result() {
		if ($('.r-r-i:first').index() == -1) {
			$('#empty-search-result').show();
			$('#page-parent').remove();
		}
	}

	var room_list_cache = {};
	$(document).on('click', ".room-btn", function(e) {
		//console.log("MAthi");
		e.preventDefault();
		var _cur_row_index = $(this).closest('.r-r-i');
		console.log("_cur_row_index"+_cur_row_index);
		var _hotel_room_list = $(".room-list", _cur_row_index);
		var _result_index = $('[name="ResultIndex"]:first', _cur_row_index).val();
		if (_hotel_room_list.is(':visible') == false) {
			//console.log("Show Mathi...");
			_hotel_room_list.show();
			if ((_result_index in room_list_cache) == false) {
				var _hotel_name = $('.h-name', _cur_row_index).text();
				var _hotel_star_rating = $('.h-sr', _cur_row_index).text();
				var _hotel_image = $('.h-img', _cur_row_index).attr('src');
				var _hotel_address = $('.h-adr', _cur_row_index).text();
				$('.loader-image', _cur_row_index).show();
				$.post(app_base_url + "index.php/ajax/get_room_details", $('.room-form', _cur_row_index).serializeArray(), function(response) {
					if (response.hasOwnProperty('status') == true && response.status == true) {
						room_list_cache[_result_index] = true;
						$('.loader-image', _cur_row_index).hide();
						$(".room-summ", _cur_row_index).html(response.data);
						//update star rating and hotel name
						$('[name="HotelName"]', _cur_row_index).val(_hotel_name);
						$('[name="StarRating"]', _cur_row_index).val(_hotel_star_rating);
						$('[name="HotelImage"]').val(_hotel_image); //Balu A
						$('[name="HotelAddress"]').val(_hotel_address); //Balu A
					}
				});
			} else {
				//console.log("show else mathi");
				$(".room-summ", _cur_row_index).show();
			}
		} else {
			//console.log("Else..Mathi..");
			_hotel_room_list.hide();
		}
	});

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
		console.log("Elavarasi");
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
		$('.resultalls.open #tour_search_result').on('click', function() {
			$('.resultalls').removeClass('open');
			$('.coleft').slideUp(500);
		});
	}


	//var application_preference_currency = document.getElementById('pri_app_pref_currency').value;
	var application_preference_currency = document.getElementById('pri_app_pref_currency').value;
	
	/** -------------------------SORT LIST DATA---------------------- **/
	/**
	 * Unsetting the sorter array
	 */
	function unset_sorters_array()
	{
		_ini_fil['sort_item'] = undefined;
		_ini_fil['sort_type'] = undefined;
	}
	function enable_sort()
	{
		/*$(".price-l-2-h").click(function() {
			$(this).addClass('hide');
			$('.price-h-2-l').removeClass('hide');
			$("#tour_search_result").jSort({
				sort_by: '.h-p',
				item: '.r-r-i',
				order: 'asc',
				is_num: true
			});
		});

		$(".price-h-2-l").click(function() {
			$(this).addClass('hide');
			$('.price-l-2-h').removeClass('hide');
			$("#tour_search_result").jSort({
				sort_by: '.h-p',
				item: '.r-r-i',
				order: 'desc',
				is_num: true
			});
		});*/
		$(".price-l-2-h").click(function(){
				$('.loader-image').show();
			//if(dynamic_page_data == true) {
				unset_sorters_array();
				_ini_fil['sort_item'] = 'price';
				_ini_fil['sort_type'] = 'asc';
				dynamic_sorter_rom();
				if($('.price-l-2-h').hasClass('hide') == false) {
					$('.price-l-2-h').addClass('hide');
				}
				if($('.price-h-2-l').hasClass('hide') == true) {
					$('.price-h-2-l').removeClass('hide');
				}
			//}
		});
		$(".price-h-2-l").click(function(){
				$('.loader-image').show();
			//if(dynamic_page_data == true) {
				unset_sorters_array();
				_ini_fil['sort_item'] = 'price';
				_ini_fil['sort_type'] = 'desc';
				dynamic_sorter_rom();
				if($('.price-h-2-l').hasClass('hide') == false) {
					$('.price-h-2-l').addClass('hide');
				}
				if($('.price-l-2-h').hasClass('hide') == true) {
					$('.price-l-2-h').removeClass('hide');
				}
			//}
		});

		$(".name-l-2-h").click(function() {
				$('.loader-image').show();
			unset_sorters_array();
			_ini_fil['sort_item'] = 'name';
			_ini_fil['sort_type'] = 'asc';
			dynamic_sorter_rom();
			if($('.name-l-2-h').hasClass('hide') == false) {
				$('.name-l-2-h').addClass('hide');
			}
			if($('.name-h-2-l').hasClass('hide') == true) {
				$('.name-h-2-l').removeClass('hide');
			}
		});

		$(".name-h-2-l").click(function() {
				$('.loader-image').show();
			unset_sorters_array();
			_ini_fil['sort_item'] = 'name';
			_ini_fil['sort_type'] = 'desc';
			dynamic_sorter_rom();
			if($('.name-h-2-l').hasClass('hide') == false) {
				$('.name-h-2-l').addClass('hide');
			}
			if($('.name-l-2-h').hasClass('hide') == true) {
				$('.name-l-2-h').removeClass('hide');
			}
		});
		$(".star-l-2-h").click(function(){
				$('.loader-image').show();
			unset_sorters_array();
			_ini_fil['sort_item'] = 'star';
			_ini_fil['sort_type'] = 'asc';
			dynamic_sorter_rom();
			if($('.star-l-2-h').hasClass('hide') == false) {
				$('.star-l-2-h').addClass('hide');
			}
			if($('.star-h-2-l').hasClass('hide') == true) {
				$('.star-h-2-l').removeClass('hide');
			}
		});
		$(".star-h-2-l").click(function(){
			$('.loader-image').show();
			unset_sorters_array();
			_ini_fil['sort_item'] = 'star';
			_ini_fil['sort_type'] = 'desc';
			dynamic_sorter_rom();
			if($('.star-h-2-l').hasClass('hide') == false) {
				$('.star-h-2-l').addClass('hide');
			}
			if($('.star-l-2-h').hasClass('hide') == true) {
				$('.star-l-2-h').removeClass('hide');
			}
		});
	}
	/** -------------------------SORT LIST DATA---------------------- **/

	/**
	 * Toggle active class to highlight current applied sorting
	 **/
	$(document).on('click', '.sorta', function(e) {
		e.preventDefault();
		//console.log("mathi");
		loader();
		$(this).closest('.sortul').find('.active').removeClass('active');
		//Add to sibling
		$(this).siblings().addClass('active');
	});

	$('.loader').on('click', function(e) {
		e.preventDefault();
		loader();
	});

	$('#tour-search-btn').on('click', function(e) {
		//alert('rout');
		loader();
		e.preventDefault();
		ini_activity_namef();
		filter_tour();
	});

	$(document).on('change', 'input.discount-filter', function(e) {
		//console.log("calllleerere");
		//alert("calee");
		//$('.loader-image').show();
		loader();
		ini_discount_filt();
		filter_tour();
		//console.log("caleed");

	});

	$('.deal-status-filter').on('change', function(e) {
		loader();
		ini_dealf();
		filter_tour();
	});

	$(document).on('change', '.star-filter', function(e) {
		loader();
		var thisEle = this;
		var _filter = '';
		var attr = {};
		attr['checked'] = $(thisEle).is(':checked');
		ini_starf();
		filter_tour('star-filter', _filter, attr);
	});



	//Balu A - Setting minimum and maximum price for slider range
	function update_range_slider(minPrice, maxPrice) {
		$('#core_minimum_range_value', '#core_min_max_slider_values').val(minPrice);
		$('#core_maximum_range_value', '#core_min_max_slider_values').val(maxPrice);
		//price-refine
		enable_price_range_slider(minPrice, maxPrice);
	}
	//Reset the filters -- Elavarasi
	$(document).on('click', '#reset_filters', function() {
		//comment by ela
		//$('.loader-image').show();
		loader();	
		//Reset the Star,and Location Filters
		// $('#starCountWrapper .enabled').each(function() {
		// 	$(this).removeClass('active');
		// 	$('.star-filter', this).prop('checked', false);
		// });
		$('#tour-name').val('');
		//$('#tour_search_result').empty();
		$('input.activity-cate').prop('checked', false);
		$('input.discount-filter').prop('checked',false);
		set_slider_label(min_amt, max_amt);
		var minPrice = $('#core_minimum_range_value', '#core_min_max_slider_values').val();
		var maxPrice = $('#core_maximum_range_value', '#core_min_max_slider_values').val();
		$("#price-range").slider("option", "values", [minPrice, maxPrice]);
		//$('.discount-filter:checked').val('all');
		inif();
		
		filter_tour();

	});
	/**
	 * Show loader images
	 */
	function loader() {
		$('.container').css({
			'opacity': '.1'
		});
		setTimeout(function() {
			$('.container').css({
				'opacity': '1'
			}, 'slow');
		}, 1000);
	}

	//------ INI F
	var _ini_fil = {};
	_ini_fil['_sf'] = [];
	function ini_starf()
	{
		_ini_fil['_sf'] = $('input.star-filter:checked').map(function() {
			return this.value;
		}).get();
	}
	
	_ini_fil['dis'] = false;
	function ini_discount_filt()
	{
		if($('.discount-filter:checked').val()=='filter'){
		//	alert('checked');
			_ini_fil['dis'] = true;
		} else {
			//alert('not_checked');
			_ini_fil['dis'] = false;
		}

		// _ini_fil['dis'] = $('.discount-filter:checked', '#discount-filter-wrapper').map(function() {
		// 	return this.value;
		// }).get();

		//alert(_ini_fil);
		//console.log(_ini_fil['hl'])
		//alert(_ini_fil['hl']);
	}
	
	_ini_fil['min_price'] = 0;
	_ini_fil['max_price'] = 0;
	function ini_pricef()
	{
		_ini_fil['min_price'] = parseFloat($("#price-range").slider("values")[0]);
		_ini_fil['max_price'] = parseFloat($("#price-range").slider("values")[1]);
	}
	
	_ini_fil['an_val'] = '';
	function ini_activity_namef()
	{
		//_ini_fil['hn_val'] = $('#hotel-name').val().trim().toLowerCase();
		_ini_fil['an_val'] = $('#tour-name').val();
		
	}
	
	_ini_fil['dealf'] = false;
	function ini_dealf()
	{
		if ($('.deal-status-filter:checked').val() == 'filter') {
			_ini_fil['dealf'] = true;
		} else {
			_ini_fil['dealf'] = false;
		}
	}
	_ini_fil['cate'] = [];
	function ini_activity_cate()
	{
		_ini_fil['cate'] = $('.activity-cate:checked', '#activity-cate-wrapper').map(function() {
			return this.value;
		}).get();
		//alert(_ini_fil['cate']);
	}

	$(document).on('change', 'input.activity-cate', function(e) {
		loader();
		ini_activity_cate();
		filter_tour();
	});

	function inif()
	{
		//ini_starf();
		ini_discount_filt();
		ini_pricef();
		ini_activity_namef();
		//ini_dealf();
		ini_activity_cate();

	}
	//------ INI F

	/**
	 * _filter_trigger	==> element which caused fliter to be triggered
	 * _filter			==> default filter settings received from filter trigger
	 */
	function filter_tour(_filter_trigger, _filter, attr) {
		//console.log("called");
		inif();
		//console.log(_offset+' offset : '+_fltr_r_cnt+ ' Filter Count '+ _total_r_cnt+ ' Total Count');
		//if (_fltr_r_cnt == _total_r_cnt && _offset >= _total_r_cnt) {
		if (dynamic_page_data == false) {
			//alert("filterroom");
			if (_filter_trigger == 'star-filter') {
				if ((attr['checked'] == false && _ini_fil['_sf'].length > 0) || (attr['checked'] == true && _ini_fil['_sf'].length == 1)) {
					_filter = ':visible';
				} else {
					_filter = ':hidden';
				}
			} else {
				_filter = '';
			}

			_fltr_r_cnt = 0;
			//console.log(_ini_fil['cate']);

			//console.log(_ini_fil);
			//$('.r-r-i' + _filter) : FIXME
			$('.r-r-i').each(function(key, value) {
				var _rmp = parseInt($('.h-p', this).text());
				var _discount = parseInt($('.special-offer',this).text());	
				var _ran = $('.h-name', this).text().trim().toLowerCase();	
				var _acate = $('.activity-cate',this).text().trim();
				//console.log(_acate);
				if (					
					(_rmp >= _ini_fil['min_price'] && _rmp <= _ini_fil['max_price']) &&
					(_ini_fil['dis'] == false || (_ini_fil['dis'] == true && _discount == true)) && 
					((_ran == "" || _ini_fil['an_val'] == "") || (_ran.search(_ini_fil['an_val'].trim().toLowerCase()) > -1)) &&
					((_ini_fil['cate'].length == 0) || (has_all_cate(_acate))==true) 
					) {
					++_fltr_r_cnt;
					//console.log("removeClass");
					$(this).removeClass('hide');
				} else {
					//console.log("addClass");
					$(this).addClass('hide');
				}
				//console.log("filterscalleedssss");
				//set_marker_visibility(this, lat, lon, _rhn, star,price)
			});
		} else {
			//alert("backend filter");
			//filter from backend
			dynamic_filter_rom();
		}
		update_total_count_summary();
	}
	 function has_all_cate(cate) {
        
        var has = false;
       	var found_array =new Array();
       	for (var i = 0; i <_ini_fil['cate'] .length; i++) {
       		
       		var cat_found = parseInt($.inArray(_ini_fil['cate'][i],cate));
       		if(cat_found >-1){
       			found_array.push(1);
       		}else{
       			found_array.push(0);
       		}
       	}
       	if($.inArray(1,found_array) >-1){
       		has = true;
       	}else{
       		has =false;
       		//console.log("notfound");
       	}
       	//console.log(found_array);
        /*$.each(_ini_fil['cate'], function(k, v) {
            //console.log("v"+v);
           // var str_lnth = parseInt(cate.search(v));
          	var cat_found = parseInt($.inArray(v,cate));
          	console.log("cat_found"+cat_found);
            if (cat_found > -1) {
                //_fclty.search(set_null);
              
                has = true;
                //return false
            }else{
                
                has = false;
                return false;
            }
        });*/
        return has;
    }
	function dynamic_filter_rom()
	{
		//-- empty results and show loader
		show_result_pre_loader();
		$('#tour_search_result').empty();
		_offset = 0;
		load_products(process_result_update, _offset, _ini_fil);
	}
	//Balu A
	function dynamic_sorter_rom()
	{
		//-- empty results and show loader
		show_result_pre_loader();
		$('#tour_search_result').empty();
		_offset = 0;
		load_products(process_result_update, _offset, _ini_fil);
	}
	/**
	 *Update Hotel Count Details
	 */
	function update_total_count_summary() {
		$('#tour_search_result').show();
		if (isNaN(_fltr_r_cnt) == true || _fltr_r_cnt < 1) {
			_fltr_r_cnt = 0;
			//display warning
			$('#tour_search_result').hide();
			$('#empty_tour_search_result').show();
		} else {
			$('#tour_search_result').show();
			$('#empty_tour_search_result').hide();
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
		min_amt = minPrice;
		max_amt = maxPrice;
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
			change: function(e) {
				if ('originalEvent' in e) {
					loader();
					ini_pricef();
					filter_tour();
				}
			}
		});
		set_slider_label(minPrice, maxPrice);
		/**** PRICE SLIDER END ****/
	}

	function set_slider_label(val1, val2) {
		$("#hotel-price").text(sliderCurrency +' '+ val1 + " - " + sliderCurrency+' '+ val2);
	}

	function enable_discount_selector() {
		//alert(locs);
			
		
		var _location_option_list = '';
	
		_location_option_list += '<li>';
			_location_option_list += '<div class="squaredThree">';
			_location_option_list += '<input id="locSquaredThreedisc' + 0+ '" class="discount-filter" type="checkbox" name="check" value="filter">';
			_location_option_list += '<label for="locSquaredThreedisc' + 0 + '"></label>';
			_location_option_list += '</div>';
			_location_option_list += '<label class="lbllbl" for="locSquaredThreedisc' + 0 + '">Discount & Special Offer</label>';
			_location_option_list += '</li>';
			//i++;
		$('#discount-filter-wrapper').html(_location_option_list);
	}


	function enable_star_wrapper(star_sum) {
		loadStarFilter(star_sum);
	}
	function loadStarFilter(star_count_array) {
		var starCat = 0;
		$('#starCountWrapper .star-filter').each(function(key, value) {
			starCat = parseInt($(this).val());
			if ($.isEmptyObject(star_count_array[starCat]) == true) {
				//disabled
				$(this).attr('disabled', 'disabled');
				$(this).closest('.star-wrapper').addClass('disabled');
			} else {
				$(this).closest('.star-wrapper').addClass('enabled');
			}
		});
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
	/************MAP**********/

    //MAP
    /*Map view click function*/
    $('.map_click').click(function() { 
        reset_ini_map_view();//set up master map markers
        //window.location.reload();
        //reset_ini_map_view();//set up master map markers
        $('.allresult').addClass('map_open');
        $('.view_type').removeClass('active');
        $(this).addClass('active');
        $(".result_srch_htl").niceScroll({
            styler: "fb",
            cursorcolor: "#4ECDC4",
            cursorwidth: '3',
            cursorborderradius: '10px',
            background: '#404040',
            spacebarenabled: false,
            cursorborder: ''
        });
        setTimeout(function() {
            google.maps.event.trigger(map, 'resize');
        }, 500);
		
		
    //reset_ini_map_view();
    });
	
	
	
    function map_point_load(){
        reset_ini_map_view();//set up master map markers
        //window.location.reload();
        //reset_ini_map_view();//set up master map markers
        $('.allresult').addClass('map_open');
        $('.view_type').removeClass('active');
        $(this).addClass('active');
        $(".result_srch_htl").niceScroll({
            styler: "fb",
            cursorcolor: "#4ECDC4",
            cursorwidth: '3',
            cursorborderradius: '10px',
            background: '#404040',
            spacebarenabled: false,
            cursorborder: ''
        });
        setTimeout(function() {
            google.maps.event.trigger(map, 'resize');
        }, 500);
		
    }

	
	
/*    $('.list_click').click(function() { 
        $('.allresult').removeClass('map_open');
        $('.view_type').removeClass('active');
        $(this).addClass('active');
    });*/
        $('.list_click').click(function() {
        // $('.allresult').removeClass('map_open');
        // $('.view_type').removeClass('active');
        // $(this).addClass('active');
        //$(".resultalls").removeClass("fulview");
        $('#tour_search_result .item').removeClass('grid-group-item');
        $('.rowresult').removeClass("col-xs-4");
        $(this).addClass("active");
        $('.grid_click').removeClass('active');
    });
    $('.grid_click').click(function() {
		$('#tour_search_result .item').removeClass('list-group-item');
    	$('#tour_search_result .item').addClass('grid-group-item');
    	$('.rowresult').addClass("col-xs-4");
    	$('#tour_search_result .item').show();
    	$('.view_type').removeClass("active");
    	$('.grid_click').addClass("active");
	});

    // DEFINE YOUR MAP AND MARKER 
    //google.maps.event.addDomListener(window, 'load', initialize);

    //google.maps.event.addDomListener(window, "resize", resizingMap());
});
 