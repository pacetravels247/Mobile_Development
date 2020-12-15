var owl;
$(document).ready(function() {
	// alert('test');
	$city = 'London';
	 
	// $url = "http://maps.googleapis.com/maps/api/geocode/json?address=$city";
	// $json_data = file_get_contents($url);
	// $result = json_decode($json_data, TRUE);
	// $latitude = $result['results'][0]['geometry']['location']['lat'];
	// $longitude = $result['results'][0]['geometry']['location']['lng'];
	// alert($latitude);
	enable_sort();
	var bounds = new google.maps.LatLngBounds();
	$('.loader-image').show();
	pre_load_audio();
	var _search_id = document.getElementById('pri_search_id').value;
	var api_url = document.getElementById('api_base_url').value;
	var active_booking_source = document.getElementById('api_booking_source').value;
	var pagination_loader = document.getElementById('pagination_loader').value;
	//console.log("api_url"+api_url);
	var _fltr_r_cnt = 0; //Filter Result count
	var _total_r_cnt = 0;//Total Result count
	var _offset = 0;//Offset to load results
	var dynamic_page_data = true;
	//var default_loader = document.getElementById('default_loader').value;
	var default_loader = api_url+'image_loader.gif';

	
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
					load_hotels(process_result_update, _offset, _ini_fil);
					reset_ini_map_view();
					google.maps.event.trigger(map, 'resize');
					resizeMap();
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
		console.log("offset"+_offset+"fltr_count"+_fltr_r_cnt+"total_count"+_total_r_cnt);
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
			$(window).unbind('scroll', window, processLazyLoad);
			$('#npl_img').remove();
			dynamic_page_data = false;
			
			//enable sorting via javascript
			enable_sort();
		} else if (_offset < _fltr_r_cnt) {
			_next_page = true;
			
			if(parseInt($("#npl_img").length)==0){				
				var html ='';
				html +='<div id="npl_img" class="text-center" loaded="true">';
				html+='<img src="'+pagination_loader+'" alt="please wait"/>';
				html +='</div>';				
				$(".allresult").append(html);
			}			
			//console.log('More Records are available to load');
			$('#npl_img').show();
		}else{
			$(window).unbind('scroll', window, processLazyLoad);
			$('#npl_img').remove();
			dynamic_page_data = false;
			enable_sort();
		}
	}

	window.process_result_update = function(result) {
		var ___data_r = Date.now();
		$('.loader-image').hide();
		hide_result_pre_loader();
		//console.log("pre_loader");
		if (result.hasOwnProperty('status') == true && result.status == true) {
			set_total_summary(result.total_result_count, result.filter_result_count, result.offset);
			update_total_count_summary();
			$('#npl_img').removeAttr('loaded');
			if (_offset == 0) {
				//console.log('Data loaded with offset 0');
				$('#hotel_search_result').html(result.data);
				 $("#npl_img").hide();
				//No Result Found
			} else {

				if(_offset == 20){
                        $('#hotel_search_result').html(result.data);
                }else{
                    $('#hotel_search_result').append(result.data);
                }

				//console.log('Pagination data loaded with offset');
				//$('#hotel_search_result').append(result.data);
			}
			ini_pagination();
			//Set time out to lazy load images
			lazy_img();
		}
	}
	
	window.ini_result_update = function(result) {
		post_load_audio();
		//console.log(result);
		if (result.hasOwnProperty('status') == true && result.status == true) {
			update_range_slider(parseInt(result.filters.p.min), parseInt(result.filters.p.max));
			enable_location_selector(result.filters.loc);
			enable_star_wrapper(result.filters.star);
			inif();
			$(window).on('scroll', processLazyLoad);
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
		e.preventDefault();
		var _cur_row_index = $(this).closest('.r-r-i');
		var _hotel_room_list = $(".room-list", _cur_row_index);
		var _result_index = $('[name="ResultIndex"]:first', _cur_row_index).val();
		if (_hotel_room_list.is(':visible') == false) {
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
				$(".room-summ", _cur_row_index).show();
			}
		} else {
			_hotel_room_list.hide();
		}
	});
 	//getting hotel images dynamically
	$(document).on('click','.view-photo-btn',function(e){
		var id = $(this).data('id');	
		//alert(id);	
		var Hotel_code = $("#map_id_"+id).data('hotel-code');
		//alert( Hotel_code);
		var Booking_source = $("#map_id_"+id).data('booking-source');
		var hotel_name = $("#map_id_"+id).data('hotel-name');

		//alert(hotel_name);
		var hotel_star =parseInt($("#map_id_"+id).data('star-rating'));
		var price = $("#map_id_"+id).data('price');
		var result_token = $("#map_id_"+id).data('result-token');
		var trip_url = $("#map_id_"+id).data('trip-url');
		var trip_rating= $("#map_id_"+id).data('trip-rating');
		$("#hotel-images").html('');
		$("#modal-price-symbol").html('');
		$("#modal-price").html();
		
		if(trip_url!=''){
			$(".imghtltrpadv").removeClass('hide');
		}else{
			$(".imghtltrpadv").addClass('hide');
		}
		$("#trip_adv_img").attr('src','');
		$("#myModalLabel").html(hotel_name);
		var price_text = $('#pri_app_pref_currency').val();
		$("#modal-price-symbol").html(price_text);
		$("#modal-price").html(price);
		$("#trip_adv_img").attr('src',trip_url);
		$(".spinner").show();
		$(".hotel-images").hide();
		var star_str='';
		for (var i = 0; i < hotel_star; i++) {
			star_str +='<li><i class="fa fa-star" aria-hidden="true"></i></li>';
		}
		$(".htmimgstr").html(star_str);
		var booking_url =app_base_url+'index.php/hotel/hotel_details/'+
        _search_id+'?ResultIndex='+result_token+'&booking_source='+Booking_source
        +'&op=get_details';
		$("#modal-submit").attr('href',booking_url);
		$.post(app_base_url + "index.php/ajax/get_hotel_images",{hotel_code:Hotel_code,booking_source:Booking_source,search_id:_search_id,Hotel_name:hotel_name}, function(response) {
			//console.log("dasjkjdasd");
			//$("#myModalLabel").html(hotel_name);
			$(".spinner").hide();
			$(".hotel-images").show();
		//	console.log(response);
			if(response){
				$("#hotel-images").html(response.data);	
			}
			
		});

	});
	//Load hotels from active source
	show_result_pre_loader();
	$(document).on('click', '.location-map', function() {
		var data_key = $(this).data('key');
		var hotel_code = $(this).data('hotel-code');
		var star_rating = $("#location_"+hotel_code+"_"+data_key).data('star-rating');
		var hotel_name = $("#location_"+hotel_code+"_"+data_key).data('hotel-name');
		$(".htmimgstr").html("");
		$("#myModalLabelMap").html("");
		var star_str='';
		for (var i = 0; i < star_rating; i++) {
			star_str +='<li><i class="fa fa-star" aria-hidden="true"></i></li>';
		}
		$(".htmimgstr").html(star_str);
		$("#myModalLabelMap").html(hotel_name);
		$('#map-box-modal').modal();
	});
	$(document).on('click','.hotel-image-gal',function(){
		$("#hotel-img-gal-box-modal").modal();
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
	// $('.filter_tab').click(function() {
	// 	$('.resultalls').stop(true, true).toggleClass('open');
	// 	$('.coleft').stop(true, true).slideToggle(500);
	// });

	var widowwidth = $(window).width();
	if (widowwidth < 991) {
		$('.coleft').hide();
		$('.resultalls.open #hotel_search_result').on('click', function() {
			$('.resultalls').removeClass('open');
			$('.coleft').slideUp(500);
		});
	}


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
			$("#hotel_search_result").jSort({
				sort_by: '.h-p',
				item: '.r-r-i',
				order: 'asc',
				is_num: true
			});
		});

		$(".price-h-2-l").click(function() {
			$(this).addClass('hide');
			$('.price-l-2-h').removeClass('hide');
			$("#hotel_search_result").jSort({
				sort_by: '.h-p',
				item: '.r-r-i',
				order: 'desc',
				is_num: true
			});
		});*/
		$(".price-l-2-h").click(function(){
			//console.log("price low to high");
			//console.log("dynamic_page_data l to hi"+dynamic_page_data);
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
			//console.log("dynamic_page_data hi to l"+dynamic_page_data);
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
			//console.log("ascending name");
			unset_sorters_array();
			_ini_fil['sort_item'] = 'name';
			_ini_fil['sort_type'] = 'asc';
			dynamic_sorter_rom();
			if($('.name-l-2-h').hasClass('hide') == false) {
				console.log("addhide");
				$('.name-l-2-h').addClass('hide');
			}
			if($('.name-h-2-l').hasClass('hide') == true) {
				console.log("removehide");
				$('.name-h-2-l').removeClass('hide');
			}
		});

		$(".name-h-2-l").click(function() {
			//console.log("desscending name");
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
		loader();
		$(this).closest('.sortul').find('.active').removeClass('active');
		//Add to sibling
		$(this).siblings().addClass('active');
	});

	$('.loader').on('click', function(e) {
		e.preventDefault();
		loader();
	});

	$('#hotel-name-search-btn').on('click', function(e) {
		e.preventDefault();
		ini_hotel_namef();
		filter_rom();
	});

	$(document).on('change', 'input.hotel-location', function(e) {
		loader();
		ini_hotel_locf();
		filter_rom();
	});

	$('.deal-status-filter').on('change', function(e) {

		loader();
		ini_dealf();
		filter_rom();
	});
	$('.freecancel-hotels-view').on('change',function(e){
		loader();
		ini_free_cancel();
		filter_rom();
		
	});
	$('.wifi-hotels-view').on('change',function(e){
		loader();
		ini_wifi();
		filter_rom();
		
	});
	$('.break-hotels-view').on('change',function(e){
		loader();
		ini_breakfast();
		filter_rom();
		
	});
	$('.parking-hotels-view').on('change',function(e){
		loader();
		ini_parking();
		filter_rom();
		
	});
	$('.pool-hotels-view').on('change',function(e){
		loader();
		ini_swim_pool();
		filter_rom();
		
	});
	$('.refundable-hotels-view').on('change',function(e){	
		loader();	
		ini_refundable();	
		filter_rom();	
			
	});	
	$('.nonrefundable-hotels-view').on('change',function(e){	
		loader();	
		ini_nonrefundable();	
		filter_rom();	
			
	});	
	$('.ro-hotels-view').on('change',function(e){	
		loader();	
		//alert("fasdf");	
		ini_roomonly();	
		filter_rom();	
			
	});	
	$('.hb-hotels-view').on('change',function(e){	
		loader();	
		ini_halfboard();	
		filter_rom();	
			
	});	
	$('.bb-hotels-view').on('change',function(e){	
		loader();	
		ini_break_fast();	
		filter_rom();	
			
	});	
	$('.fb-hotels-view').on('change',function(e){	
		loader();	
		ini_fullboard();	
		filter_rom();	
			
	});
	$(document).on('change', '.star-filter', function(e) {
		loader();
		var thisEle = this;
		var _filter = '';
		var attr = {};
		attr['checked'] = $(thisEle).is(':checked');
		ini_starf();
		filter_rom('star-filter', _filter, attr);
	});



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
		//Reset the Star,and Location Filters
		$('#starCountWrapper .enabled').each(function() {
			$(this).removeClass('active');
			$('.star-filter', this).prop('checked', false);
		});
		$('input.hotel-location').prop('checked', false);
		$(".freecancel-hotels-view").prop('checked',false);
		$('.wifi-hotels-view').prop('checked',false);
		$('.pool-hotels-view').prop('checked',false);
		$('.parking-hotels-view').prop('checked',false);
		$('.break-hotels-view').prop('checked',false);
		//HotelName
		$('#hotel-name').val(''); //Hotel Name
		set_slider_label(min_amt, max_amt);
		var minPrice = $('#core_minimum_range_value', '#core_min_max_slider_values').val();
		var maxPrice = $('#core_maximum_range_value', '#core_min_max_slider_values').val();
		$("#price-range").slider("option", "values", [minPrice, maxPrice]);
		inif();
		//alert("hiii");		
		$('.deal-status-filter:checked').val('all');
		$('.freecancel-hotels-view:checked').val('all');
		$('.wifi-hotels-view').val('all');
		$('.pool-hotels-view').val('all');
		$('.parking-hotels-view').val('all');
		$('.break-hotels-view').val('all');

		filter_rom();
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
	
	_ini_fil['hl'] = [];
	function ini_hotel_locf()
	{
		_ini_fil['hl'] = $('.hotel-location:checked', '#hotel-location-wrapper').map(function() {
			return this.value;
		}).get();
		//alert(_ini_fil['hl']);
	}
	
	_ini_fil['min_price'] = 0;
	_ini_fil['max_price'] = 0;
	function ini_pricef()
	{
		_ini_fil['min_price'] = parseFloat($("#price-range").slider("values")[0]);
		_ini_fil['max_price'] = parseFloat($("#price-range").slider("values")[1]);
	}
	
	_ini_fil['hn_val'] = '';
	function ini_hotel_namef()
	{
		_ini_fil['hn_val'] = $('#hotel-name').val().trim().toLowerCase();
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
	_ini_fil['free_cancel'] = false;
	function ini_free_cancel()
	{	
		if($('.freecancel-hotels-view:checked').val()=='filter'){
			_ini_fil['free_cancel'] = true;
		}else{
			_ini_fil['free_cancel'] = false;
		}		
	}

	_ini_fil['wifi'] = false;
	function ini_wifi()
	{	
		if($('.wifi-hotels-view:checked').val()=='filter'){
			_ini_fil['wifi'] = true;
		}else{
			_ini_fil['wifi'] = false;
		}		
	}
	_ini_fil['breakfast'] = false;
	function ini_breakfast()
	{	
		if($('.break-hotels-view:checked').val()=='filter'){
			_ini_fil['breakfast'] = true;
		}else{
			_ini_fil['breakfast'] = false;
		}		
	}
	_ini_fil['parking'] = false;
	function ini_parking()
	{	
		if($('.parking-hotels-view:checked').val()=='filter'){
			_ini_fil['parking'] = true;
		}else{
			_ini_fil['parking'] = false;
		}		
	}
	_ini_fil['swim_pool'] = false;
	function ini_swim_pool()
	{	
		if($('.pool-hotels-view:checked').val()=='filter'){
			_ini_fil['swim_pool'] = true;
		}else{
			_ini_fil['swim_pool'] = false;
		}		
	}
	_ini_fil['refundable'] = false;	
	function ini_refundable()	
	{		
		if($('.refundable-hotels-view:checked').val()=='filter'){	
			_ini_fil['refundable'] = true;	
		}else{	
			_ini_fil['refundable'] = false;	
		}			
	}	
	_ini_fil['nonrefundable'] = false;	
	function ini_nonrefundable()	
	{		
		if($('.nonrefundable-hotels-view').val()=='filter'){	
			_ini_fil['nonrefundable'] = true;	
		}else{	
			_ini_fil['nonrefundable'] = false;	
		}			
	}	
	_ini_fil['roomonly'] = false;	
	function ini_roomonly()	
	{		
		if($('.ro-hotels-view').val()=='filter'){	
			_ini_fil['roomonly'] = true;	
		}else{	
			_ini_fil['roomonly'] = false;	
		}			
	}	
	_ini_fil['break_fast'] = false;	
	function ini_break_fast()	
	{		
		if($('.bb-hotels-view').val()=='filter'){	
			_ini_fil['break_fast'] = true;	
		}else{	
			_ini_fil['break_fast'] = false;	
		}			
	}	
	_ini_fil['halfboard'] = false;	
	function ini_halfboard()	
	{		
		if($('.hb-hotels-view').val()=='filter'){	
			console.log("hi");	
			_ini_fil['halfboard'] = true;	
		}else{	
			_ini_fil['halfboard'] = false;	
		}			
	}	
	_ini_fil['fullboard'] = false;	
	function ini_fullboard()	
	{		
		if($('.fb-hotels-view').val()=='filter'){	
			_ini_fil['fullboard'] = true;	
		}else{	
			_ini_fil['fullboard'] = false;	
		}			
	}
	function inif()
	{
		ini_starf();
		ini_hotel_locf();
		ini_pricef();
		ini_hotel_namef();
		ini_dealf();
		ini_free_cancel();
		ini_wifi();
		ini_breakfast();
		ini_parking();
		ini_swim_pool();

	}
	//------ INI F

	/**
	 * _filter_trigger	==> element which caused fliter to be triggered
	 * _filter			==> default filter settings received from filter trigger
	 */
	function filter_rom(_filter_trigger, _filter, attr) {
		inif();
		//if (_fltr_r_cnt == _total_r_cnt && _offset >= _total_r_cnt) {
		
		if (dynamic_page_data == false) {

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
			// console.log(_ini_fil['min_price']);
			// console.log('----------');
			// console.log(_ini_fil['max_price']);
			
			//$('.r-r-i' + _filter) : FIXME
			$('.r-r-i').each(function(key, value) {
				
				var _rmp = parseInt($('.h-p', this).text());
				var _rhn = $('.h-name', this).text().trim().toLowerCase();
				var _rhl = $('.h-loc', this).text();
				var _rde = $('.deal-status', this).data('deal');
				var _free = $('.free_cancel',this).data('free-cancel');
				var _wifi = $('.wifi',this).data('wifi');
				var _breakfast = $('.breakfast',this).data('breakfast');
				var _parking = $('.parking',this).data('parking');
				var _swim_pool = $('.pool',this).data('pool');
				var _roomonly = $('.roomonly',this).data('roomonly');	
				var _break_fast = $('.break_fast',this).data('break_fast');	
				var _halfboard = $('.halfboard',this).data('halfboard');	
				var _fullboard = $('.fullboard',this).data('fullboard');	
				//console.log(_roomonly);	
				//console.log("-----------");	
				//console.log(_ini_fil['roomonly']);
				if (
					((_ini_fil['_sf'].length == 0) || ($.inArray(($('.h-sr', this).text()), _ini_fil['_sf']) != -1)) &&
					(_rmp >= _ini_fil['min_price'] && _rmp <= _ini_fil['max_price']) &&
					((_rhn == "" || _ini_fil['hn_val'] == "") || (_rhn.search(_ini_fil['hn_val']) > -1)) &&
					((_rhl == "" || _ini_fil['hl'].length == 0) || ($.inArray((_rhl), _ini_fil['hl']) != -1)) &&
					(_ini_fil['dealf'] == false || (_ini_fil['dealf'] == true && _rde == true)) && 
					(_ini_fil['free_cancel'] == 0 || (_ini_fil['free_cancel'] == 1 && _free == 1))&&
					(_ini_fil['wifi'] == 0 || (_ini_fil['wifi'] == 1 && _wifi == 1))&&
					(_ini_fil['breakfast'] == 0 || (_ini_fil['breakfast'] == 1 && _breakfast == 1))&&
					(_ini_fil['parking'] == 0 || (_ini_fil['parking'] == 1 && _parking == 1))&&
					(_ini_fil['swim_pool'] == 0 || (_ini_fil['swim_pool'] == 1 && _swim_pool == 1))&&	
					(_ini_fil['roomonly'] == 0 || (_ini_fil['roomonly'] == 1 && _roomonly == 1))&&	
					(_ini_fil['halfboard'] == 0 || (_ini_fil['halfboard'] == 1 && _halfboard == 1))&&	
					(_ini_fil['fullboard'] == 0 || (_ini_fil['fullboard'] == 1 && _fullboard == 1))&&	
					(_ini_fil['break_fast'] == 0 || (_ini_fil['break_fast'] == 1 && _break_fast == 1))
					) {
					++_fltr_r_cnt;
					$(this).removeClass('hide');
				} else {
					$(this).addClass('hide');
				}
			});
			
		} else {
			
			//filter from backend
			dynamic_filter_rom();
		}
		update_total_count_summary();
	}
	
	function dynamic_filter_rom()
	{
		//console.log("Elavarasi");
		//-- empty results and show loader
		show_result_pre_loader();
		$('#hotel_search_result').empty();
		_offset = 0;
		//_ini_fil['image_order'] =true; 
		load_hotels(process_result_update, _offset, _ini_fil);
	}
	//Balu A
	function dynamic_sorter_rom()
	{
		//-- empty results and show loader
		show_result_pre_loader();
		$('#hotel_search_result').empty();
		_offset = 0;
		
		load_hotels(process_result_update, _offset, _ini_fil);
	}
	/**
	 *Update Hotel Count Details
	 */
	function update_total_count_summary() {
		$('#hotel_search_result').show();
		
		if (isNaN(_fltr_r_cnt) == true || _fltr_r_cnt < 1) {
			_fltr_r_cnt = 0;
			//display warning
			$('#hotel_search_result').hide();
			$('#empty_hotel_search_result').show();
		} else {
			$('#hotel_search_result').show();
			$('#empty_hotel_search_result').hide();
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
					filter_rom();
				}
			}
		});
		set_slider_label(minPrice, maxPrice);
		/**** PRICE SLIDER END ****/
	}

	function set_slider_label(val1, val2) {
		$("#hotel-price").text(sliderCurrency + val1 + " - " + sliderCurrency + val2);
	}

	function enable_location_selector(locs) {
		var _location_option_list = '';
		var i = 0;
		$.each(locs, function(k, v) {
			_location_option_list += '<li>';
			_location_option_list += '<div class="squaredThree">';
			_location_option_list += '<input id="locSquaredThree' + i + '" class="hotel-location" type="checkbox" name="check" value="' + v['v'] + '">';
			_location_option_list += '<label for="locSquaredThree' + i + '"></label>';
			_location_option_list += '</div>';
			_location_option_list += '<label class="lbllbl" for="locSquaredThree' + i + '">' + v['v'] + '('+v['c']+')</label>';
			_location_option_list += '</li>';
			i++;
		});
		$('#hotel-location-wrapper').html(_location_option_list);
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

	//MAP
    /*Map view click function*/
    $('.map_click').click(function() {
		resizeMap();
		$('.rowresult.r-r-i').removeClass('marker_highlight');
        reset_ini_map_view();//set up master map markers
        //window.location.reload();
      //  console.log("_offset"+_offset);
        //load_hotels(process_result_update,120, _ini_fil);
        //reset_ini_map_view();//set up master map markers
        $('.allresult').addClass('map_open');
        $('.view_type').removeClass('active');
        $(this).addClass('active');
        $(".resultalls").addClass("fulview");
         $('.rowresult').removeClass("col-xs-4");
         $('#hotel_search_result .item').removeClass('grid-group-item');
        $('.hotel_map').show();
        $(".coleft").hide();       

        $(".allresult").niceScroll({
            styler: "fb",
            cursorcolor: "#4ECDC4",
            cursorwidth: '3',
            cursorborderradius: '10px',
            background: '#404040',
            spacebarenabled: false,
            cursorborder: ''
        });      
		google.maps.event.trigger(map, 'resize');
		resizeMap();
		
		/* $(document).on("mouseover",".madgrid",function(){
			alert("hii");
		});
		$(document).on("mouseout",".madgrid",function(){
			alert("hello");
		});*/
    //reset_ini_map_view();
    });
	
	
	
    function map_point_load(){
    	 resizeMap();
        reset_ini_map_view();//set up master map markers
        //window.location.reload();
        //reset_ini_map_view();//set up master map markers
        $('.allresult').addClass('map_open');
        $('.view_type').removeClass('active');
        $(this).addClass('active');
        $(".allresult").niceScroll({
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
	
	
    $('.list_click').click(function() {
        // $('.allresult').removeClass('map_open');
        // $('.view_type').removeClass('active');
        // $(this).addClass('active');
        $(".resultalls").removeClass("fulview");
        $('#hotel_search_result .item').removeClass('grid-group-item');
        $('.hotel_map').hide();
        $('.rowresult').removeClass("col-xs-4");
        $(".coleft").show();
        $(this).addClass("active");
        $('.allresult').removeClass('map_open');
         $('.map_click').removeClass('active');
    });

    // DEFINE YOUR MAP AND MARKER 
    var map;
    var lat = $('.hotel_location_city').data('lat');
    var lon = $('.hotel_location_city').data('lon');
    // alert(lat);
    // alert(lon);
    lat = 53;
    lon =-1.33;

    var styles = [
    {
     featureType: "landscape",
      "elementType": "labels.text.stroke",
     stylers: [
      { color: '#cad6db' }
     ]
     
    },
     {
        "featureType": "all",
        "elementType": "geometry",
        "stylers": [
            {
                "color": "#cad6db"
            }   
        ],
   "stylers1": [
            {
                "color": "#cad6db"
            }   
        ]
    },
   
    
    {
        "featureType": "landscape",
        "elementType": "geometry",
        "stylers1": [
            {
               
    "color": "#cad6db"
            },
            
        ]
    },
    {
        "featureType": "poi",
        "elementType": "geometry",
        "stylers": [
            {
                
    "color": "#cad6db"
            }
        ]
    },
    {
        "featureType": "poi.park",
        "elementType": "geometry",
        "stylers": [
            {
                
    "color": "#cad6db"
            },
            
        ]
    },
    {
        "featureType": "road",
        "elementType": "geometry",
        "stylers": [
            {
               
    "color": "#cad6db"
            },
           
        ]
    },
    {
        "featureType": "road",
        "elementType": "geometry.stroke",
        "stylers": [
            
            {
                
    "color": "#ffffff"
            }
        ]
    },
    {
        "featureType": "water",
        "elementType": "all",
        "stylers": [
            {
                "invert_lightness": true
            },
            {
                "saturation": 60
            },
            {
                "lightness": 60
            },
            {
                "gamma": 1
            },
            {
                "hue": "#11b6df"
            }
   
        ]
    }
    
   ];
   
  	var myCenter=new google.maps.LatLng(lat, lon);
	var marker=new google.maps.Marker({
	    position:myCenter
	});
	var infowindow;
    (function() {
        google.maps.Map.prototype.markers = new Array();
        google.maps.Map.prototype.addMarker = function(marker) {
            this.markers[this.markers.length] = marker;
        };
        google.maps.Map.prototype.getMarkers = function() {
            return this.markers
        };
        google.maps.Map.prototype.clearMarkers = function() {
            if (infowindow) {
                infowindow.close();
            }
            for (var i = 0; i < this.markers.length; i++) {
                this.markers[i].set_map(null);
            }
        };
    })();
    function initialize() {

        var mapProp = {
            center: myCenter, 
            zoom: 10,
		    draggable: true,
		    scrollwheel: false,
		    styles: styles,
		    gestureHandling: 'none',
          	//zoomControl: false,
		   
		    mapTypeId:google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(document.getElementById("map"), mapProp);
        marker.setMap(map);
       // console.log("elaasjajsdjsdsdf");
        infoWindow = new google.maps.InfoWindow();
        google.maps.event.addListener(marker, 'click', function() {
            //infowindow.setContent(infowindow);
            infowindow.open(map, marker);
        });
    };
    google.maps.event.addDomListener(window, 'load', initialize);

    google.maps.event.addDomListener(window, "resize", resizingMap());

    $('#map_view_hotel').on('show.bs.modal', function() {
        //Must wait until the render of the modal appear, thats why we use the resizeMap and NOT resizingMap!! ;-)alert("xx");
        resizeMap();
    })

    function resizeMap() {
        if (typeof map == "undefined") return;
        setTimeout(function() {
            resizingMap();
        }, 600);
    }

    function resizingMap() {
        if (typeof map == "undefined") return;
        var center = map.getCenter();
        google.maps.event.trigger(map, "resize");
        map.setCenter(center);
    }
	
    /**
	 * Set value are data on map
	 */
    var marker;
    //var markers = [];
    var min_lat = 0;
    var max_lat = 0;
    var min_lon = 0;
    var max_lon = 0;
    //initialize master map object
    function reset_ini_map_view()
    {
		
        ini_map_view();
    }
	
    function ini_map_view() {		
     	var counter=0;
    	if (max_lat == 0) {
			max_lat = min_lat = 0;
		}

		if (max_lon == 0) {
			max_lon = min_lon = 0;
		} 
		
        $('#hotel_search_result .r-r-i').each(function() {        	
            set_marker_visibility(this);           
        	var lat = parseFloat($(this).find('.hotel_location').data('lat'));
			var lon = parseFloat($(this).find('.hotel_location').data('lon'));
			if((typeof(lat)!='undefined') && (lat!='') && !isNaN(lat) && (counter<1)){
				max_lat = min_lat = lat;
			}
			if((typeof(lon)!='undefined') && (lon!='') && !isNaN(lon)){
				counter++;					
				max_lon = min_lon = lon;
			}
        });
        $(".spinner").hide();
    	$(".map_hotel").removeClass('hide');
		 //max_lat = $('.hotel_location').data('lat')
		 //max_lon = $('.hotel_location').data('lon')
		
		if(typeof map=='object'){
			map.setCenter(new google.maps.LatLng(max_lat, max_lon));	
		}
      	

    }
	
    //function show_on_map()
	
    function get_map_attr_obj(thisRef, lat, lon, name, star)
    {
        var object = {};
        object['lat'] = parseFloat(lat) || parseFloat($('.hotel_location', thisRef).data('lat'));
        object['lon'] = parseFloat(lon) || parseFloat($('.hotel_location', thisRef).data('lon'));
        object['name'] = name || $('.h-name', thisRef).text();
        object['star'] = star || $('.h-sr', thisRef).text();
       // console.log("star"+object['star']);
 	var result_token = $(".result_token,thisRef").text();
	 var booking_url = app_base_url+'index.php/hotel/hotel_details/'+
        _search_id+'?ResultIndex='+result_token+'&booking_source='+active_booking_source
        +'&op=get_details';
        object['details_url'] = booking_url;

        object['img'] = $('.h-img', thisRef).data('src');
        object['acc_key'] = '1_'+marker_access_key(object['lat'], object['lon']);
        if (object['img'] == '') {
            $('.h-img', thisRef).attr('src');
        }
        var price_text = $('#pri_app_pref_currency').val();

        //object['curr'] = $('.currency_symbol', thisRef).text();
        object['curr'] =  price_text;
        object['price'] = $('.h-p', thisRef).text();
        return object;
    }
	function get_map_attr_obj_new(thisRef)
    {
        var object = {};
        object['lat'] = parseFloat(thisRef.Latitude);
        object['lon'] = parseFloat(thisRef.Longitude);
        object['name'] = thisRef.HotelName;
        object['star'] = thisRef.StarRating;    	
        var booking_url = '';
        booking_url = app_base_url+'index.php/hotel/hotel_details/'+
        _search_id+'?ResultIndex='+thisRef.MResultToken+'&booking_source='+active_booking_source
        +'&op=get_details';
        object['details_url'] = booking_url;
        if(thisRef.HotelPicture){
        	object['img'] = thisRef.HotelPicture;	
        }else{
        	object['img'] = api_url+'default_hotel_img.jpg';
        }
        
        object['acc_key'] = marker_access_key(object['lat'], object['lon']);
        // if (object['img'] == '') {
        //     $('.h-img', thisRef).attr('src');
        // }
        object['curr'] = $('.currency_symbol').text();
         var price = 0;
        var price = thisRef.Price.RoomPrice;
        object['price'] = price;
        return object;
    }
    function marker_access_key(lat, lon)
    {
        return lat+'_'+lon;
    }
	
    function set_marker_visibility(thisRef, lat, lon, name, star)
    {
    	if(typeof thisRef !='undefined'){

	        var lat = parseFloat($('.hotel_location', thisRef).data('lat'));
	        var lon = parseFloat($('.hotel_location', thisRef).data('lon'));
	       //console.log("lat"+lat);
	       //console.log("lon"+lon);
	        var access_key = marker_access_key(lat, lon);
	        var visibility = true;
	        if ($(thisRef).is(':visible') == false) {
	            visibility = false;
	        //console.log('data is hidden');
	        }
	        if ($.isEmptyObject(markers[access_key]) == true) {
	            //console.log('Access key not found '+access_key);
	            $(thisRef).attr('access_key', access_key);
	            var object = get_map_attr_obj(thisRef, lat, lon, name, star);
	            create_marker(object, visibility);
	            
	        } else {
	            //toggle visibility
	            //console.log('already present so setting visibility');
	            markers[access_key].setVisible(visibility);
	            markers[access_key].setIcon(normalIcon());
	            console.log("called2");
	        }
	    }
        
    }
  function set_marker_visibility_old(thisRef)
    {	
    	if(typeof thisRef !='undefined'){
    		if(thisRef.Latitude){
    			var lat = parseFloat(thisRef.Latitude);	
	    	}else{
	    		var lat = 0;
	    	}
	    	if(thisRef.Longitude){
	    		var lon = parseFloat(thisRef.Longitude);	
	    	}else{
	    		var lon = 0;
	    	}        
	        
	       //console.log("lat"+lat);
	       	//console.log("lon"+lon);
	        var access_key = marker_access_key(lat, lon);
	        var visibility = true;
	        // if ($(thisRef).is(':visible') == false) {
	        //     visibility = false;
	        // //console.log('data is hidden');
	        // }

	        if ($.isEmptyObject(markers[access_key]) == true) {
	          //  console.log('Access key not found '+access_key);
	            //$(thisRef).attr('access_key', access_key);
	            var object = get_map_attr_obj(thisRef);
	            create_marker(object, visibility);
	        } else {
	            //toggle visibility
	          //  console.log('already present so setting visibility');
	            markers[access_key].setVisible(visibility);
	        }
    	}
    	
        //console.log("marker"+markers[access_key]);
    }
    /**
	 * Add marker
	 */
    function create_marker(obj, visibility)
    {
    	//console.log("visibility"+visibility);
        max_lon = (max_lon < obj['lon'] ? obj['lon'] : max_lon);
        min_lon = (min_lon < obj['lon'] ? obj['lon'] : min_lon);
        max_lat = (max_lat < obj['lat'] ? obj['lat'] : max_lat);
        min_lat = (min_lat < obj['lat'] ? obj['lat'] : min_lat);
         var star_rating = '';
        for (var i =1; i <= obj['star']; i++) {
        	star_rating +='<span class="fa fa-star"></span>';
        }

        var contentString =
        '<div class="map_box">'+
        '<div class="in_map_box">'+
        '<div class="map_image">'+
        '<img alt="" src="'+obj['img']+'">'+
        '<div class="map_htl_price"><span>'+obj['curr']+'</span> <span>'+obj['price']+'</span></div>'+
        '</div>'+
        '<div class="map_details">'+
        '<div class="col-xs-8 nopad">'+
        '<div class="map_name_dets">'+
        '<h4 class="map_name_htl">'+obj['name']+'</h4>'+
        '<div data-star="'+obj['star']+'" class="stra_hotel">'+star_rating+'</div>'+
        '</div>'+
        '</div>'+
        '<div class="col-xs-4 nopad"> <a class="book_map" href="'+obj['details_url']+'">Book</a> </div>'+
        '</div>'+
        '</div>'+
        '</div>';
        // var infowindow = new google.maps.InfoWindow({
        //     content: contentString
        // });
        //console.log("map"+map);
     
        var marker = new google.maps.Marker({
        	
            map: map,
            draggable: false,
            position: {
                lat: obj['lat'], 
                lng: obj['lon']
                },
            title: obj['name'],
            visible: visibility,
            icon:api_url+'marker/hotel_map_marker.png'
           
            
        });       
        marker.addListener('click', function() {        	
        	if(infowindow)infowindow.close();
        	infowindow = new google.maps.InfoWindow({
                content: contentString
            });
            infowindow.open(map, marker);
        });
       // console.log('Access key created for '+obj['acc_key']);
      	//markers.push(marker);
        markers[obj['acc_key']] = marker;
       

        //map.fitBounds(bounds);
        //var markerCluster = new MarkerClusterer(map, markers);
    }

	/*$(document).on("click",".madgrid",function(){
		 	var index = $('.madgrid').index(this);
		 	$('.rowresult.r-r-i').removeClass('marker_highlight');
	        var access_key = $('.rowresult.r-r-i')[index].getAttribute('access_key');
	      	
	      	$('.rowresult.r-r-i:eq('+index+')').addClass('marker_highlight');
	      	//console.log($('.rowresult.r-r-i').not($('.rowresult.r-r-i.marker_highlight')));
	      	//highlight current marker another disable
	      	$('.rowresult.r-r-i').not($('.rowresult.r-r-i.marker_highlight')).each(function(e_index,value){
	      		//console.log("called");
	      		var normal_marker = $('.rowresult.r-r-i')[e_index].getAttribute('access_key');
	      		if(normal_marker!=null){	
	      		//	console.log(normal_marker);    			
	      			markers['1_'+normal_marker].setIcon(normalIcon());
	      		}
	      	});
	      	//console.log(access_key);
	      	if(access_key!=null){
	      		var arr = access_key.split('_');	      		
	      		var lat = arr[0];
	      		var lon = arr[1];
	  			var pt = new google.maps.LatLng(lat, lon);	
	  			bounds.extend(pt);
	  			map.fitBounds(bounds);      		
	      		map.setCenter(new google.maps.LatLng(lat, lon));	      		
	      		markers['1_'+access_key].setIcon(highlightedIcon());
	      	}
	});*/
	
	
	if ($(window).width() < 550) {
            $(".result_srch_htl").removeClass("owl-carousel");

			$(".map_click").click(function(){
				$('.rowresult.r-r-i').removeClass('marker_highlight');
				$(".result_srch_htl").addClass("owl-carousel");
				 owl = $(".owl-carousel").owlCarousel({

						center: true,
						items:1,
						loop:false,
						addClassActive:true,
						margin:10,
							responsive:{
								550:{
								items:1
								}
							},
					});
				 owl.on('changed.owl.carousel', function(e) {
    				//alert("test");
    				
    				var index_div = $(".owl-item.active",this).index();
    				var index = (index_div+1);
    				var access_key = $('.rowresult.r-r-i')[index].getAttribute('access_key');
    			 	$('.rowresult.r-r-i').removeClass('marker_highlight');
			    
			    
			      $('.rowresult.r-r-i:eq('+index+')').addClass('marker_highlight');
			      	
			      	//highlight current marker another disable
			      	$('.rowresult.r-r-i').not($('.rowresult.r-r-i.marker_highlight')).each(function(e_index,value){
			      		var normal_marker = $('.rowresult.r-r-i')[e_index].getAttribute('access_key');
			      		if(normal_marker!=null){
			      			markers[normal_marker].setIcon(normalIcon());
			      		}
			      	});
			      	
			      	if(access_key!=null){
			      		var arr = access_key.split('_');
			      		var lat = arr[0];
			      		var lon = arr[1];
			  			var pt = new google.maps.LatLng(lat, lon);	
			  			bounds.extend(pt);
			  			map.fitBounds(bounds);      		
			      		map.setCenter(new google.maps.LatLng(lat, lon));
			      		markers[access_key].setIcon(highlightedIcon());
			      	}
  				});	                       
			});	
          
          $(".list_tab").click(function(){
					$(".result_srch_htl").removeClass("owl-carousel");
					$(".result_srch_htl").removeClass("owl-loaded");
					$(".result_srch_htl").removeClass("owl-drag");
					$(".owl-stage-outer").removeClass("owl-stage-outer");
					 
			});	 
			
		}

});
var markers = {};
function normalIcon() {

	var api_url = document.getElementById('api_base_url').value;
  return {
    url: api_url+'marker/hotel_map_marker.png'
  };
}
function highlightedIcon() {
	var api_url = document.getElementById('api_base_url').value;
  return {
    url: api_url+'marker/green_hotel_map_marker.png'
  };
}
