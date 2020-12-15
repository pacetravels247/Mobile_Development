
<script src="<?php echo JAVASCRIPT_LIBRARY_DIR?>jquery.jsort.0.4.min.js" defer></script>
<script>
var application_preference_currency = "<?php echo $this->currency->get_currency_symbol(get_application_currency_preference());?>";
$(document).ready(function() {
	$(".price-l-2-h").click(function(){
		$(this).addClass('hide');
		$('.price-h-2-l').removeClass('hide');
		$("#hotel_search_result").jSort({sort_by: '.h-p', item: '.r-r-i', order: 'asc', is_num: true });
	});

	$(".price-h-2-l").click(function(){
		$(this).addClass('hide');
		$('.price-l-2-h').removeClass('hide');
		$("#hotel_search_result").jSort({sort_by: '.h-p', item: '.r-r-i', order: 'desc', is_num: true});
	});

	$(".name-l-2-h").click(function(){
		$(this).addClass('hide');
		$('.name-h-2-l').removeClass('hide');
		$("#hotel_search_result").jSort({sort_by: '.h-name', item: '.r-r-i', order: 'asc', is_num: false});
	});

	$(".name-h-2-l").click(function(){
		$(this).addClass('hide');
		$('.name-l-2-h').removeClass('hide');
		$("#hotel_search_result").jSort({sort_by: '.h-name', item: '.r-r-i', order: 'desc', is_num: false});
	});


	$(".star-l-2-h").click(function(){
		$(this).addClass('hide');
		$('.star-h-2-l').removeClass('hide');
		$("#hotel_search_result").jSort({sort_by: '.h-sr', item: '.r-r-i', order: 'asc', is_num: true});
	});

	$(".star-h-2-l").click(function(){
		$(this).addClass('hide');
		$('.star-l-2-h').removeClass('hide');
		$("#hotel_search_result").jSort({sort_by: '.h-sr', item: '.r-r-i', order: 'desc', is_num: true});
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

	$('.loader').on('click', function(e) {
		e.preventDefault();
		loader();
	});

	//$('.star-filter').on('change click', function() {
	//	loader();
	//	filter_row_origin_marker();
	//});

	$('#hotel-name-search-btn').on('click', function(e) {
		e.preventDefault();
		filter_row_origin_marker();
	});

	//$('#hotel-name').on('change blur keyup', function(e) {
	//	e.preventDefault();
	//	loader();
	//	filter_row_origin_marker();
	//});

	$(document).on('change', 'input.hotel-location', function(e) {
		loader();
		filter_row_origin_marker();
	});

	$('.deal-status-filter').on('change', function(e) {
		loader();
		filter_row_origin_marker();
	});

	$(document).on('change', '.star-filter', function(e) {
		loader();
		var thisEle = this;
		//setTimeout(function() {
		var _filter = '';
		var attr = {};
		attr['checked'] = $(thisEle).is(':checked');
		filter_row_origin_marker('star-filter', _filter, attr);
		//}, 1);
	});
});
//Balu A - Setting minimum and maximum price for slider range
function update_range_slider()
{
	var minPrice = 99999999;
	var maxPrice = 0;
	var price = 0;
	$('.r-r-i').each(function(key, value) {
		price = parseFloat($('.h-p:first', this).text());
		if (price < minPrice) {minPrice = price;}
		if (price > maxPrice) {maxPrice = price;}
	});
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
	//HotelName
	$('#hotel-name').val('');//Hotel Name
	set_slider_label(min_amt, max_amt);
	var minPrice = $('#core_minimum_range_value', '#core_min_max_slider_values').val();
	var maxPrice = $('#core_maximum_range_value', '#core_min_max_slider_values').val();
	$("#price-range").slider("option", "values", [minPrice, maxPrice]);
	filter_row_origin_marker();
});
/**
 * Show loader images
 */
function loader()
{
	$('.container').css({'opacity':'.1'});
	setTimeout(function() {$('.container').css({'opacity':'1'}, 'slow');}, 1000);
}
/**
 * _filter_trigger	==> element which caused fliter to be triggered
 * _filter			==> default filter settings received from filter trigger
 */
function filter_row_origin_marker(_filter_trigger, _filter, attr)
{
	 var _star_filter = [];
	//get all the search criteria
	_star_filter = $('input.star-filter:checked').map(function() {
		return this.value;
	}).get();

	if (_filter_trigger == 'star-filter') {
		if ((attr['checked'] == false && _star_filter.length > 0) || (attr['checked'] == true && _star_filter.length == 1)) {
			_filter = ':visible';
		} else {
			_filter = ':hidden';
		}
		//_filter = '';
	} else {
		_filter = '';
	}

	
	var min_price				= parseFloat($("#price-range").slider("values")[0]);
	var max_price				= parseFloat($("#price-range").slider("values")[1]);

	var hotel_name_search_val	= $('#hotel-name').val().trim().toLowerCase();

	var hotel_location = $('.hotel-location:checked', '#hotel-location-wrapper').map(function() {
		return this.value;
	}).get();

	//check if deal status filter is available and if available then filter and otherwise you can skip the validation
	var hotel_deal_filter = $('.deal-status-filter:checked').val();
	
	var filter_with_deal = false;
	if (hotel_deal_filter == 'filter') {
		filter_with_deal = true;
	}

	//var ___end = Date.now();
//	console.log(((___end-___start)/1000)+' Seconds : Refining Result');
	//return ;

	$('.r-r-i'+_filter).each(function(key, value) {
		var _row_marker_price	= parseInt($('.h-p', this).text());
		var _row_hotel_name		= $('.h-name', this).text().trim().toLowerCase();
		var _row_hotel_location	= $('.h-loc', this).text();
		var _row_deal_enabled = $('.deal-status', this).data('deal');
		if (
				((_star_filter.length == 0) || ($.inArray(($('.h-sr', this).text()), _star_filter) != -1)) &&
				(_row_marker_price >= min_price && _row_marker_price <= max_price) &&
				((_row_hotel_name == "" || hotel_name_search_val == "") || (_row_hotel_name.search(hotel_name_search_val) > -1)) &&
				((_row_hotel_location == "" || hotel_location.length == 0) || ($.inArray((_row_hotel_location), hotel_location) != -1)) &&
				(filter_with_deal == false || (filter_with_deal == true && _row_deal_enabled == true))) {
			
			$(this).removeClass('hide');
		} else {
			$(this).addClass('hide');
		}
	});
	update_total_count_summary();
}

/**
*Update Hotel Count Details
*/
function update_total_count_summary()
{
	$('#hotel_search_result').show();
	var _visible_records = parseInt($('.r-r-i:visible').length);
	var _total_records = $('.r-r-i').length;
	if (isNaN(_visible_records) == true || _visible_records == 0) {
		_visible_records = 0;
		//display warning
		$('#hotel_search_result').hide();
		$('#empty_hotel_search_result').show();
	} else {
		$('#hotel_search_result').show();
		$('#empty_hotel_search_result').hide();
	}
	$('#total_records').text(_visible_records);
	$('.visible-row-record-count').text(_visible_records);
	$('.total-row-record-count').text(_total_records);
}
var sliderCurrency = "<?php echo $this->currency->get_currency_symbol(get_application_currency_preference()); ?>";
//application_preference_currency
var min_amt = 0;
var max_amt = 0;
function enable_price_range_slider(minPrice, maxPrice)
{
	min_amt = minPrice;
	max_amt = maxPrice;
	$("#price-range" ).empty();
	/**** PRICE SLIDER START ****/
	$("#price-range" ).slider({
		range: true,
		min: minPrice,
		max: maxPrice,
		values: [ minPrice, maxPrice ],
		slide: function( event, ui ) {
			set_slider_label(ui.values[ 0 ], ui.values[ 1 ]);
		},
		change : function(e) {
			if ('originalEvent' in e) {
				loader();
				filter_row_origin_marker();
			}
		}
	});
	set_slider_label(minPrice, maxPrice);
	/**** PRICE SLIDER END ****/
}
function set_slider_label(val1, val2)
{
	$( "#hotel-price" ).text( sliderCurrency + val1 + " - "+ sliderCurrency + val2);
}

function enable_location_selector()
{
	var _location_list = get_location_list().sort();
	_location_list = unique_array_values(_location_list);
	var _location_option_list = '';
	$.each(_location_list, function(k, v) {
		_location_option_list += '<li>';
		_location_option_list += '<div class="squaredThree">';
		_location_option_list += '<input id="locSquaredThree'+k+'" class="hotel-location" type="checkbox" name="check" value="'+v+'">';
		_location_option_list+= '<label for="locSquaredThree'+k+'"></label>';
		_location_option_list += '</div>';
		_location_option_list += '<label class="lbllbl" for="locSquaredThree'+k+'">'+v+'</label>';
		_location_option_list += '</li>';
	});
	$('#hotel-location-wrapper').html(_location_option_list);
}


function enable_star_wrapper()
{
	var star_wrapper = {};
	star_wrapper = $('.h-sr').map(function () {return parseInt($(this).text()); });
	loadStarFilter(unique_array_values(star_wrapper));
}
function loadStarFilter(star_count) {
	if ($.isPlainObject(star_count) == false) {
		var star_count_array = star_count;
		var starCat = 0;
		$('#starCountWrapper .star-filter').each(function(key, value) {
			starCat = parseInt($(this).val());
			if (star_count_array.indexOf(starCat) == -1) {
				//disabled
				$(this).attr('disabled', 'disabled');
				$(this).closest('.star-wrapper').addClass('disabled');
			} else {
				$(this).closest('.star-wrapper').addClass('enabled');
			}
		});
	}
}

function unique_array_values(array_values)
{
	var _unique_array_values = [];
	$.each(array_values, function(k, v) {
		if (_unique_array_values.indexOf(v) == -1) {
			_unique_array_values.push(v);
		}
	});
	return _unique_array_values;
}

function get_location_list()
{
	return $('.h-loc').map(function() {
		return $(this).text();
		});
}
</script>
