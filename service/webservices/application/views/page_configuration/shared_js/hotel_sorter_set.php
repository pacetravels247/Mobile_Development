
<script src="<?php echo JAVASCRIPT_LIBRARY_DIR?>jquery.jsort.0.4.min.js"></script>
<script>
var application_preference_currency = "<?php echo get_application_currency_preference();?>";
$(document).ready(function() {
	//price sort
	$(".price-l-2-h").click(function(){
		$("#hotel_search_result").jSort({sort_by: '.hotel-price', item: '.result-row-index', order: 'asc', is_num: true });
	});

	$(".price-h-2-l").click(function(){
		$("#hotel_search_result").jSort({sort_by: '.hotel-price', item: '.result-row-index', order: 'desc', is_num: true});
	});

	//name sort
	$(".name-l-2-h").click(function(){
		$("#hotel_search_result").jSort({sort_by: '.hotel-name', item: '.result-row-index', order: 'asc', is_num: false});
	});

	$(".name-h-2-l").click(function(){
		$("#hotel_search_result").jSort({sort_by: '.hotel-name', item: '.result-row-index', order: 'desc', is_num: false});
	});


	//star sort
	$(".star-l-2-h").click(function(){
		$("#hotel_search_result").jSort({sort_by: '.hotel-star-rating', item: '.result-row-index', order: 'asc', is_num: true});
	});

	$(".star-h-2-l").click(function(){
		$("#hotel_search_result").jSort({sort_by: '.hotel-star-rating', item: '.result-row-index', order: 'desc', is_num: true});
	});

	$('.loader').on('click', function(e) {
		e.preventDefault();
		loader();
	});

	$('.star-filter').on('change click', function() {
		loader();
		filter_row_origin_marker();
	});

	$('#hotel-name-search-btn').on('click', function(e) {
		e.preventDefault();
		filter_row_origin_marker();
	});

	$('#hotel-name').on('change blur keyup', function(e) {
		e.preventDefault();
		filter_row_origin_marker();
	});

	$('#hotel-location').on('change', function(e) {
		loader();
		filter_row_origin_marker();
	});

	$('.deal-status-filter').on('change', function(e) {
		loader();
		filter_row_origin_marker();
	});
});

/**
 * Show loader images
 */
function loader()
{
	$('.container').animate({'opacity':'.1'});
	setTimeout(function() {$('.container').animate({'opacity':'1'}, 'slow');}, 1000);
}

function filter_row_origin_marker() 
{
	//get all the search criteria
	var _star_filter = $('input.star-filter:checked').map(function() {
		return this.value;
	}).get();
	var min_price				= parseFloat($("#price-range").slider("values")[0]);
	var max_price				= parseFloat($("#price-range").slider("values")[1]);
	var hotel_name_search_val	= $('#hotel-name').val().trim().toLowerCase();
	var hotel_location			= $('#hotel-location').val();
	//check if deal status filter is available and if available then filter and otherwise you can skip the validation
	var hotel_deal_filter = $('.deal-status-filter:checked').val();
	var filter_with_deal = false;
	if (hotel_deal_filter == 'filter') {
		filter_with_deal = true;
	}
	$('.result-row-index').each(function(key, value) {
		var _row_marker_price	= parseInt($('.hotel-price', this).text());
		var _row_hotel_name		= $('.hotel-name', this).text().trim().toLowerCase();
		var _row_hotel_location	= $('.hotel-location', this).text();
		var _row_deal_enabled = $('.deal-status', this).data('deal');
		if ($.inArray(($('.hotel-star-rating', this).text()), _star_filter) != -1 &&
				(_row_marker_price >= min_price && _row_marker_price <= max_price) &&
				((_row_hotel_name == "" || hotel_name_search_val == "") || (_row_hotel_name.search(hotel_name_search_val) > -1)) &&
				((_row_hotel_location == "" || hotel_location == "") || (hotel_location == _row_hotel_location)) &&
				(filter_with_deal == false || (filter_with_deal == true && _row_deal_enabled == true))) {
			$(this).show();
		} else {
			$(this).hide();
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
	var _visible_records = parseInt($('.result-row-index:visible').length);
	var _total_records = $('.result-row-index').length;
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

function enable_price_range_slider()
{
	var price_limit = get_price_slide_values();
	$("#price-range" ).empty();
	/**** PRICE SLIDER START ****/
	$("#price-range" ).slider({
		range: true,
		min: price_limit.min,
		max: price_limit.max,
		values: [ price_limit.min, price_limit.max ],
		slide: function( event, ui ) {
			$( "#hotel-price" ).text(application_preference_currency + ' ' + ui.values[ 0 ] + " - "+application_preference_currency+' '+ ui.values[ 1 ]);
		},
		change : function() {
			loader();
			filter_row_origin_marker();
		}
	});
	$("#hotel-price").text(application_preference_currency +' '+ $("#price-range").slider("values", 0) + " - " + application_preference_currency + ' ' + $("#price-range").slider("values", 1));
	/**** PRICE SLIDER END ****/
}

function enable_location_selector()
{
	var _location_list = get_location_list().sort();
	_location_list = unique_array_values(_location_list);
	var _location_option_list = '<option value="">Please Select</option>';
	$.each(_location_list, function(k, v) {
		_location_option_list += '<option value="'+v+'">'+v+'</option>';
	});
	$('#hotel-location').html(_location_option_list);
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
	return $('.hotel-location').map(function() {
		return $(this).text();
		});
}

function get_price_slide_values()
{
	var _row_price = 0;
	var max = 0;
	var min = 0;
	if ($('.result-row-index').length > 0) {
		max = parseInt($('.result-row-index .hotel-price:first').text());
		min = parseInt($('.result-row-index .hotel-price:first').text());
		$('.result-row-index').each(function() {
			_row_price = parseInt($('.hotel-price', this).text());
			if (_row_price < min) {
				min = _row_price;
			}

			if (_row_price > max) {
				max = _row_price;
			}
		});
	}
	return {"min":min, "max":max};
}
</script>