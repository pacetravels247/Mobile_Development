<script src="<?php echo JS_DIR; ?>jquery.jsort.0.4.min.js" defer></script>
<script>
$(document).ready(function() {
	//price sort
	$(".price-l-2-h").click(function(){
		$("ul#hotel_search_result, ul#aot_hotel_search_result").jSort({sort_by: '.price', item: 'li', order: 'asc', is_num: true });
	});

	$(".price-h-2-l").click(function(){
		$("ul#hotel_search_result, ul#aot_hotel_search_result").jSort({sort_by: '.price', item: 'li', order: 'desc', is_num: true});
	});

	//name sort
	$(".name-l-2-h").click(function(){
		$("ul#hotel_search_result, ul#aot_hotel_search_result").jSort({sort_by: '.name', item: 'li', order: 'asc', is_num: false});
	});

	$(".name-h-2-l").click(function(){
		$("ul#hotel_search_result, ul#aot_hotel_search_result").jSort({sort_by: '.name', item: 'li', order: 'desc', is_num: false});
	});


	//star sort
	$(".star-l-2-h").click(function(){
		$("ul#hotel_search_result, ul#aot_hotel_search_result").jSort({sort_by: '.star', item: 'li', order: 'asc', is_num: true});
	});

	$(".star-h-2-l").click(function(){
		$("ul#hotel_search_result, ul#aot_hotel_search_result").jSort({sort_by: '.star', item: 'li', order: 'desc', is_num: true});
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
});

function loader()
{
	$('body').css('opacity', '.1');
	setTimeout(function() {$('body').css('opacity', '1');}, 1000);
}

function filter_row_origin_marker() 
{
	//get all the search criteria
	var _star_filter = $('input.star-filter:checked').map(function() {
	    return this.value;
	}).get();
	var min_price = parseFloat($("#price-range").slider("values")[0]);
	var max_price = parseFloat($("#price-range").slider("values")[1]);
	var hotel_name_search_val = $('#hotel-name').val().trim().toLowerCase();
	$('.row-origin-marker').each(function(key, value) {
	 	var _row_marker_price = parseInt($('.price', this).text());
	 	var _row_hotel_name = $('.name', this).text().trim().toLowerCase();
		if ($.inArray(($('.star', this).text()), _star_filter) != -1 &&
		 		(_row_marker_price >= min_price && _row_marker_price <= max_price) &&
		 		((_row_hotel_name == "" || hotel_name_search_val == "") || (_row_hotel_name.search(hotel_name_search_val) > -1))) {
			$(this).show();
		} else {
			$(this).hide();
		}
	});
	updateTotalCountSummary();
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
			$( "#price" ).val( "" + ui.values[ 0 ] + " - " + ui.values[ 1 ] );
		},
		change : function() {
			loader();
			filter_row_origin_marker();
		}
	});
	$( "#price" ).val( "$" + $( "#price-range" ).slider( "values", 0 ) + " - $" + $( "#price-range" ).slider( "values", 1 ) );
	/**** PRICE SLIDER END ****/
}

function get_price_slide_values()
{
	var _row_price = 0;
	var max = 0;
	var min = 0;
	if ($('.row-origin-marker').length > 0) {
		max = parseInt($('.row-origin-marker .price:first').text());
		min = parseInt($('.row-origin-marker .price:first').text());
		$('.row-origin-marker').each(function() {
			_row_price = parseInt($('.price', this).text());
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

function clearSearchResult()
{
	$('#hotel_search_result, #aot-search-result').empty();
}
//summary of data displayed
function updateTotalCountSummary()
{
	$('#totalCount').empty().text(parseInt(get_total_hotel_count()));
}

function get_total_hotel_count()
{
	return $('.result-set-container .list-group-item').length;
}
</script>