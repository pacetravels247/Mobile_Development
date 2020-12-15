<?php
$data['result'] = $bus_search_params;
$template_images = $GLOBALS['CI']->template->template_images();
$mini_loading_image = '<div class="text-center loader-image"><img src="'.$template_images.'loader_v3.gif" alt="Loading........"/></div>';
$loading_image = '<div class="text-center loader-image"><img src="'.$template_images.'loader_v1.gif" alt="Loading........"/></div>';
$flight_o_direction_icon = '<img src="'.$template_images.'icons/flight-search-result-up-icon.png" alt="Flight Search Result Up Icon">';
echo $GLOBALS['CI']->template->isolated_view('bus/search_panel_summary');
?>
<section class="search-result">
	<div class="container">
	<?php echo $GLOBALS['CI']->template->isolated_view('share/loader/bus_result_pre_loader',$data);?>
		<div class="row">
			<div class="col-lg-2 nopadMob mobHide">
				<h2 class="h4">Refine your search</h2>
				<div class="panel panel-default b-r-0">
					<div class="panel-body refine">
						<div class="row">
							<div class="col-lg-12 col-md-4 col-sm-6">
								<h3 class="h4 m-t-0 refine-header" data-toggle="collapse" data-target=".price-refine"><img src="<?=$template_images?>icons/price-tag-icon.png" alt="Price Tag Icon"> Price</h3>
								<div class="collapse in price-refine">
									<p id="amount"></p>
									<div id="slider-range-1" class="" aria-disabled="false"></div>
									<?=$mini_loading_image;?>
								</div>
							</div>
							<div class="clearfix visible-sm-block"></div>
							<div class="col-lg-12 col-md-4 col-sm-6 PT15mob">
								<hr class="visible-lg-block hr-10">
								<h3 class="h4 m-t-0 refine-header" data-toggle="collapse" data-target=".bustype-refine"><img src="<?=$template_images?>icons/bus-search-result-small-icon.png" alt="Bus Icon"> Bus Type</h3>
								<div class="collapse in bustype-refine">
									<?=$mini_loading_image;?>
								</div>
							</div>
							<div class="clearfix visible-md-block"></div>
							<div class="col-lg-12 col-md-4 col-sm-6 hide">
								<hr class="visible-lg-block hr-10">
								<h3 class="h4 m-t-0 refine-header" data-toggle="collapse" data-target=".departure-refine"><img src="<?=$template_images?>icons/bus-boarding-point-icon.png" alt="Bus Boarding Point Icon"> Departure</h3>
								<div class="collapse in departure-refine">
									<p id="departure"></p>
									<div id="slider-range-2" class="" aria-disabled="false"></div>
									<?=$mini_loading_image;?>
								</div>
							</div>
							<div class="clearfix visible-sm-block"></div>
							<div class="col-lg-12 col-md-4 col-sm-6 hide">
								<hr class="visible-lg-block hr-10">
								<h3 class="h4 m-t-0 refine-header" data-toggle="collapse" data-target=".arrival-refine"><img src="<?=$template_images?>icons/bus-dropping-point-icon.png" alt="Bus Dropping Point Icon"> Arrival</h3>
								<div class="collapse in arrival-refine">
									<p id="arrival"></p>
									<div id="slider-range-3" class="" aria-disabled="false"></div>
									<?=$mini_loading_image;?>
								</div>
							</div>
							<div class="clearfix visible-sm-block"></div>
							<div class="col-lg-12 col-md-4 col-sm-6">
								<hr class="hr-10 visible-lg-block">
								<h3 class="h4 m-t-0 refine-header" data-toggle="collapse" data-target=".travel-refine"><img src="<?=$template_images?>icons/bus-star-icon.png" alt="Bus Star Icon"> Operators</h3>
								<div class="collapse in travel-refine">
									<?=$mini_loading_image;?>
								</div>
							</div>
							<div class="col-lg-12">
								<button type="button" class="btn btn-t btn-block b-r-0 reset-page-loader">Reset Filters</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-10">
				<h2 class="h4 text-p"><?php echo $mini_loading_image?>
					<span id="total_records">Loading....</span> Busses Found
				</h2>
				<h2 class="h4 text-p"><?=ucfirst(strtolower($bus_search_params['bus_station_from']))?> to <?=ucfirst(strtolower($bus_search_params['bus_station_to']))?> Bus | <?=$bus_search_params['bus_date_1']?></h2>
				<hr class="hr-10">
				<div class="row text-center bus_srh_head">
					<div class="col-md-3 col-sm-6 sortMob">
						<h6 class="m-0 travel-l-2-h hand-cursor loader"><i class="fa fa-sort-alpha-asc"></i> Operators</h6>
						<h6 class="m-0 travel-h-2-l hide hand-cursor loader"><i class="fa fa-sort-alpha-desc"></i>  Operators</h6>
					</div>
					<div class="clearfix visible-xs-block"></div>
					<hr class="hr-10 visible-xs-block">
					<div class="col-md-2 col-sm-6 sortMob">
						<h6 class="m-0 departure-l-2-h hand-cursor loader"><i class="fa fa-clock-o"></i> Departure</h6>
						<h6 class="m-0 departure-h-2-l hide hand-cursor loader"><i class="fa fa-clock-o"></i> Departure</h6>
					</div>
					<hr class="hr-10 visible-xs-block">
					<div class="col-md-2 col-sm-6 sortMob">
						<h6 class="m-0 arrival-l-2-h hand-cursor loader"><i class="fa fa-clock-o"></i> Arrival</h6>
						<h6 class="m-0 arrival-h-2-l hide hand-cursor loader"><i class="fa fa-clock-o"></i> Arrival</h6>
					</div>
					<div class="clearfix visible-xs-block visible-sm-block"></div>
					<hr class="hr-10 visible-xs-block visible-sm-block">
					<div class="col-md-2 col-sm-6 sortMob">
						<h6 class="m-0 seat-l-2-h hide hand-cursor loader"><i class="fa fa fa-sort-numeric-asc"></i> Seats</h6>
						<h6 class="m-0 seat-h-2-l hand-cursor loader"><i class="fa fa fa-sort-numeric-desc"></i> Seats</h6>
					</div>
					<div class="clearfix visible-xs-block"></div>
					<hr class="hr-10 visible-xs-block">
					<div class="col-md-1 col-sm-6 sortMob">
						<h6 class="m-0 bus-type-l-2-h hide hand-cursor loader"><i class="fa fa fa-bus"></i> Bus</h6>
						<h6 class="m-0 bus-type-h-2-l hand-cursor loader"><i class="fa fa fa-bus"></i> Bus</h6>
					</div>
					<div class="clearfix visible-xs-block visible-sm-block"></div>
					<hr class="hr-10 visible-xs-block visible-sm-block">
					<div class="col-md-2 col-sm-6 sortMob">
						<h6 class="m-0 price-l-2-h hand-cursor loader"><i class="fa fa-sort-amount-asc"></i> Price</h6>
						<h6 class="m-0 price-h-2-l hide hand-cursor loader"><i class="fa fa-sort-amount-desc"></i> Price</h6>
					</div>
				</div>
				<hr class="hr-10">
				<?php echo $loading_image;?>
				<div id="bus_search_result" class="panel xs-text-center bus-search-result-panel panel-default b-r-0 m-b-10">
				</div>

				<div id="empty_bus_search_result" style="display:none">
					No Busses Found !!!!!!!
				</div>
				<hr class="hr-10">
				<div class="clearfix">
					<div class="pull-right">
						<span class="h5">Showing: <?php echo $mini_loading_image?><span class="visible-row-record-count">...</span> of <span class="total-row-record-count">...</span> Busses</span>
					</div>
				</div>
				<hr class="hr-10 invisible">
			</div>
		</div>
	</div>
	<div class="modal fade bs-example-modal-lg" id="bus-info-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
	  <div class="modal-dialog modal-lg">
	  <div class="modal-content">
		  <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel">Bus Details</h4>
	      </div>
	      <div class="modal-body">
	      	<?=$mini_loading_image?>
	      	<div id="bus-info-modal-content">
	      	</div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      </div>
	    </div>
	  </div>
	</div>
	
	<div class="modal fade bs-example-modal-lg" id="bus-boarding-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
	  <div class="modal-dialog modal-lg">
	  <div class="modal-content">
		  <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel">Bus Boarding Details</h4>
	      </div>
	      <div class="modal-body">
	      	<?=$mini_loading_image?>
	      	<div id="bus-boarding-modal-content">
	      	</div>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      </div>
	    </div>
	  </div>
	</div>
</section>
<?php
foreach ($active_booking_source as $t_k => $t_v) {
	$active_source[] = $t_v['source_id'];
}
$active_source = json_encode($active_source);
?>
<script	src="<?php echo JAVASCRIPT_LIBRARY_DIR?>jquery.jsort.0.4.min.js" defer></script>
<script>
$(document).ready(function() {
	/**
	*Load buss - Source Trigger
	*/
	function load_busses() {
		pre_load_audio();
		var _active_booking_source = <?=$active_source?>;
		$.each(_active_booking_source, function(k, booking_source_name) {
			core_bus_loader(booking_source_name);
		});
	}

	/**
	*Load buss Core Ajax
	*/
	function core_bus_loader(booking_source_id)
	{
		$('.loader-image').show();
		var params = {booking_source:booking_source_id, search_id:<?php echo $bus_search_params['search_id']?>, op:'load'};
		$.ajax({
			type: 'GET',
			url: app_base_url+'index.php/ajax/bus_list',
			async:true,
			cache: true,
			data: $.param(params),
			dataType: 'json',
			success: function(result) {
				$('.loader-image').hide();
				hide_result_pre_loader();
				if (result.hasOwnProperty('status') == true && result.status == true) {
					$('#bus_search_result').html(result.data);
					post_load_audio();
					//update total bus count
					//update_bus_summary_counter(result.total_result_count);
					update_filter();
					//filter_row_origin_marker();
					update_total_count_summary();
					//$('.trigger-collapse-view ').trigger('click');
				} else {
					update_total_count_summary();
				}
			}
		});
	}

	/**
	*Update Count Details
	*/
	function update_total_count_summary()
	{
		$('#bus_search_result').show();
		var _visible_records = parseInt($('.r-r-i:visible').length);
		var _total_records = $('.r-r-i').length;
		if (isNaN(_visible_records) == true || _visible_records == 0) {
			_visible_records = 0;
			//display warning
			$('#bus_search_result').hide();
			$('#empty_bus_search_result').show();
		} else {
			$('#bus_search_result').show();
			$('#empty_bus_search_result').hide();
		}
		$('#total_records').text(_visible_records);
		$('.visible-row-record-count').text(_visible_records);
		$('.total-row-record-count').text(_total_records);
	}

	function update_filter()
	{
		//update filters
		var travelList = {};
		var busTypeList = [];
		var minPrice = 99999999;
		var maxPrice = 0;
		var minDepartureDatetime = 9999999999999999999;
		var maxDepartureDatetime = 0;
		var minArrivalDatetime = 9999999999999999999;
		var maxArrivalDatetime = 0;
		var price = 0;
		var dep_time = 0;
		var arr_time = 0;
		var temp_travel = '' 
		var temp_type = [];
		var busTypeCount = {};
		$('.r-r-i').each(function(key, value) {
			price = parseFloat($('.bus-price:first', this).text());
			dep_time = parseFloat($('.departure-time:first', this).text());
			arr_time = parseFloat($('.arrival-time:first', this).text());
			temp_travel = $('.travel-name:first', this).text();

			if (travelList.hasOwnProperty(temp_travel) == false) {travelList[temp_travel] = temp_travel;}
			//if (busTypeList.hasOwnProperty(temp_type) == false) {busTypeList[temp_type] = temp_type;}
			if (price < minPrice) {minPrice = price;}
			if (price > maxPrice) {maxPrice = price;}
			if (dep_time < minDepartureDatetime) {minDepartureDatetime = dep_time;}
			if (dep_time > maxDepartureDatetime) {maxDepartureDatetime = dep_time;}
			if (arr_time < minArrivalDatetime) {minArrivalDatetime = arr_time;}
			if (arr_time > maxArrivalDatetime) {maxArrivalDatetime = arr_time;}
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
			if (temp_type.length > 0) {
				busTypeList = busTypeList.concat(temp_type);
			}
		});
		travelList = getSortedObject(travelList);
		busTypeList = getSortedObject(busTypeList);
		//price-refine
		enable_price_range_slider(minPrice, maxPrice);
		//travel-refine
		enable_travel_refine(travelList);
		//bustype-refine
		enable_bus_type_refine(busTypeList, busTypeCount);
		//departure-refine
		enable_departure_range_slider(minDepartureDatetime, maxDepartureDatetime);
		//arrival-refine
		enable_arrival_range_slider(minArrivalDatetime, maxArrivalDatetime);
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

	var sliderCurrency = "<?php echo $this->currency->get_currency_symbol(get_application_currency_preference()); ?>";
	function enable_price_range_slider(minPrice, maxPrice)
	{
		var slide_left = minPrice;
		var slide_right = maxPrice;
		$( "#slider-range-1" ).slider({
			range: true,
			min: minPrice,
			max: maxPrice,
			animate: "slow",
			values: [ minPrice, maxPrice ],
			slide: function(event, ui) {
				$( "#amount" ).text( sliderCurrency + ui.values[ 0 ] + " - "+sliderCurrency + ui.values[ 1 ] );
			},
			change: function(event, ui) {
				if (parseFloat(ui.values[0]) == slide_left) {
					if (parseFloat(ui.values[1]) > slide_right) {
						visibility = ':hidden';
					} else {
						visibility = ':visible';
					}
				} else {
					if (parseFloat(ui.values[0]) < slide_left) {
						visibility = ':hidden';
					} else {
						visibility = ':visible';
					}
				}
				slide_left = parseFloat(ui.values[0]);
				slide_right = parseFloat(ui.values[1]);
				filter_row_origin_marker(visibility);
			}
		});
		$( "#amount" ).text( sliderCurrency + minPrice + " - "+ sliderCurrency + maxPrice);
	}

	function enable_travel_refine(core_list)
	{
		var list = '';
		//travel-name
		if ($.isEmptyObject(core_list) == false) {
			$.each(core_list, function(k, v) {
				list += '<div class="checkbox"><label><input type="checkbox" class="travel-box" value="'+v+'"> '+v+'</label></div>';
			});
		}
		$('.travel-refine:first').append(list);
	}

	function enable_bus_type_refine(core_list, core_count_list)
	{
		var list = '';
		//bus-type
		if ($.isEmptyObject(core_list) == false) {
			$.each(core_list, function(k, v) {
				list += '<div class="checkbox"><label><input type="checkbox" class="bus-type-box" value="'+v+'"> '+v+'('+(parseInt(core_count_list[v])+1)+')</label></div>';
			});
		}
		$('.bustype-refine:first').append(list);
	}

	function enable_departure_range_slider(minDep, maxDep)
	{
		var slide_left = minDep;
		var slide_right = maxDep;
		$( "#slider-range-2" ).slider({
			range: true,
			min: minDep,
			max: maxDep,
			animate: "slow",
			values: [ minDep, maxDep ],
			slide: function(event, ui) {
			minDep = timeConverter(ui.values[ 0 ]);
			maxDep = timeConverter(ui.values[ 1 ]);
				$( "#departure" ).text( minDep + " - "+ maxDep );
			},
			change: function(event, ui) {
				if (parseFloat(ui.values[0]) == slide_left) {
					if (parseFloat(ui.values[1]) > slide_right) {
						visibility = ':hidden';
					} else {
						visibility = ':visible';
					}
				} else {
					if (parseFloat(ui.values[0]) < slide_left) {
						visibility = ':hidden';
					} else {
						visibility = ':visible';
					}
				}
				slide_left = parseFloat(ui.values[0]);
				slide_right = parseFloat(ui.values[1]);
				filter_row_origin_marker(visibility);
			}
		});
		minDep = timeConverter(minDep);
		maxDep = timeConverter(maxDep);
		$( "#departure" ).text( minDep + " - "+ maxDep);
	}

	function enable_arrival_range_slider(minArr, maxArr)
	{
		var slide_left = minArr;
		var slide_right = maxArr;
		$( "#slider-range-3" ).slider({
			range: true,
			min: minArr,
			max: maxArr,
			animate: "slow",
			values: [ minArr, maxArr ],
			slide: function(event, ui) {
			minArr = timeConverter(ui.values[ 0 ]);
			maxArr = timeConverter(ui.values[ 1 ]);
				$( "#arrival" ).text( minArr + " - "+ maxArr );
			},
			change: function(event, ui) {
				if (parseFloat(ui.values[0]) == slide_left) {
					if (parseFloat(ui.values[1]) > slide_right) {
						visibility = ':hidden';
					} else {
						visibility = ':visible';
					}
				} else {
					if (parseFloat(ui.values[0]) < slide_left) {
						visibility = ':hidden';
					} else {
						visibility = ':visible';
					}
				}
				slide_left = parseFloat(ui.values[0]);
				slide_right = parseFloat(ui.values[1]);
				filter_row_origin_marker(visibility);
			}
		});
		minArr = timeConverter(minArr);
		maxArr = timeConverter(maxArr);
		$( "#arrival" ).text( minArr + " - "+ maxArr);
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
	function filter_row_origin_marker(visibility)
	{
		loader();
		visibility = visibility || '';
		//get all the search criteria
		var bus_type_refine = $('.bus-type-box:checked', '.bustype-refine').map(function() {
			return this.value;
		}).get()
		var travel_refine = $('.travel-box:checked', '.travel-refine').map(function() {
			return this.value;
		}).get();
		var min_price = parseFloat($("#slider-range-1").slider("values")[0]);
		var max_price = parseFloat($("#slider-range-1").slider("values")[1]);
		var min_dep_duration = parseFloat($("#slider-range-2").slider("values")[0]);
		var max_dep_duration = parseFloat($("#slider-range-2").slider("values")[1]);
		var min_arr_duration = parseFloat($("#slider-range-3").slider("values")[0]);
		var max_arr_duration = parseFloat($("#slider-range-3").slider("values")[1]);
		$('.r-r-i'+visibility).each(function(key, value) {
			//$.inArray($('.a-n:first', this).data('airline-code'), airlineList) != -1
			if (
				(travel_refine.length == 0 || $.inArray($('.travel-name', this).text(), travel_refine) != -1) &&
				(bus_type_refine.length == 0 || has_bus_type_attribute(bus_type_refine, this)) &&
				(parseFloat($('.bus-price:first', this).text()) >= min_price && parseFloat($('.bus-price:first', this).text()) <= max_price) &&
				(parseFloat($('.departure-time:first', this).text()) >= min_dep_duration && parseFloat($('.departure-time:first', this).text()) <= max_dep_duration) &&
				(parseFloat($('.arrival-time:first', this).text()) >= min_arr_duration && parseFloat($('.arrival-time:first', this).text()) <= max_arr_duration)
			) {
				$(this).show();
		} else {
			$(this).hide();
		}
	});
		update_total_count_summary();
	}

	function has_bus_type_attribute(bus_type_refine, record_origin)
	{
		var status = false;
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
		$("#bus_search_result .r-w-g").jSort({sort_by: '.available-seats:first', item: '.r-r-i', order: 'asc', is_num: true});
	});

	$(".seat-h-2-l").click(function(){
		$(this).addClass('hide');
		$('.seat-l-2-h').removeClass('hide');
		$("#bus_search_result .r-w-g").jSort({sort_by: '.available-seats:first', item: '.r-r-i', order: 'desc', is_num: true});
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

	$('.loader').on('click', function(e) {
		e.preventDefault();
		loader();
	});
	$(document).on('click', '.reset-page-loader', function(e) {
		e.preventDefault();
		loader();
		location.reload();
	});
	function loader(selector)
	{
		selector = selector || '#bus_search_result';
		$(selector).animate({'opacity':'.1'});
		setTimeout(function() {$(selector).animate({'opacity':'1'}, 'slow');}, 1000);
	}
	//Load buss from active source
	load_busses();
	show_result_pre_loader();

	//activate bus booking
	$(document).on('click', '.inner-summary-btn', function(e) {
		e.preventDefault();
		var _inner_summary_toggle = $('.inner-summary-toggle', $(this).closest('.r-r-i'));
		_inner_summary_toggle.toggle();
		//update data if visible
		if (_inner_summary_toggle.is(':visible')) {
			//load data
			var _booking_data = get_inner_bus_details($(this).closest('form').serializeArray());
			$('.room-summ', _inner_summary_toggle).html(_booking_data).show();
			$('.loader-image', _inner_summary_toggle).hide();
		} else {
			$('.room-summ', _inner_summary_toggle).html('').hide();
			$('.loader-image', _inner_summary_toggle).show();
		}
	});

	function get_inner_bus_details(params)
	{
		alert();
		var res = '';
		$.ajax({
			type: 'POST',
			url: app_base_url+'index.php/ajax/get_bus_details',
			async: false,
			cache: false,
			data: params,
			success: function(result) {
				if (result.status) {
					res = result.data;
				}
			}
		});
		return res;
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


	//Boarding point info
	$(document).on('click', '.bus-boarding-info-btn', function(e) {
		e.preventDefault();
		//update data if visible
		//load data
		clean_up_boarding_modal();
		var _bus_info_data = get_board_bus_information($('.book-form', $(this).closest('.r-r-i')).serializeArray());
	});

	function clean_up_boarding_modal()
	{
		$('#bus-boarding-modal-content ').empty();
		$('#bus-boarding-modal .loader-image').show();
		$('#bus-boarding-modal').modal();
	}
	function get_board_bus_information(params)
	{
		$.ajax({
			type: 'POST',
			url: app_base_url+'index.php/ajax/get_bus_details/true',
			async: true,
			cache: true,
			data: params,
			success: function(result) {
				$('#bus-boarding-modal .loader-image').hide();
				if (result.status) {
					$('#bus-boarding-modal-content').html(result.data);
				} else {
					$('#bus-boarding-modal-content').html('NA');
				}
			}
		});
	}
});
</script>
<?php
echo $this->template->isolated_view('share/media/bus_search');
?>