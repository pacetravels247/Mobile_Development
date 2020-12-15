<?php
$raw_bus_list = force_multple_data_format($raw_bus_list);
$cur_Currency = $currency_obj->get_currency_symbol($currency_obj->to_currency);
$template_images = $GLOBALS['CI']->template->template_images();
$mini_loading_image = '<div class="text-center loader-image">Please Wait <img src="'.$template_images.'loader_v3.gif" alt="Loading........"/></div>';
$busses = '<div class="r-w-g">';
foreach($raw_bus_list as $__bk => $__bv) {
	$bus_fare_details = $currency_obj->get_currency($__bv['Fare']);
	$bus_fare = $bus_fare_details['default_value'];
	$s2s_duration = calculate_duration($__bv['DepartureTime'], $__bv['ArrivalTime']);
	$busses .= '
	<div class="panel-body p-0 r-r-i">
		<div class="row lg-flex">
			<div class="col-md-4 col-sm-6 p-tb-10">
				<div class="media">
					<div class="media-left">
						<a href="#">
						<img alt="Bus Icon" src="'.$GLOBALS['CI']->template->template_images('icons/bus-search-result-big-icon.png').'" class="media-object">
						</a>
					</div>
					<div class="media-body">
						<h4 class="media-heading travel-name">'.$__bv['CompanyName'].'</h4>
						<p class="m-0"><small class="">'.$__bv['BusTypeName'].'</small></p>
						<a href="#" class="bus-info-btn"><small>Bus Details</small></a>
					</div>
				</div>
			</div>
			<div class="col-md-3 col-sm-6 p-tb-10">
				<div class="media">
					<div class="media-left">
						<a href="#">
						<img alt="Bus Boarding Dropping Point Icon" src="'.$GLOBALS['CI']->template->template_images('icons/bus-boarding-dropping-point-icon.png').'" class="media-object">
						</a>
					</div>
					<div class="media-body">
						<span class="hide departure-time">'.(number_format((strtotime($__bv['DepartureTime'])*1000), 0, null, '')).'</span>
						<span class="hide arrival-time">'.(number_format((strtotime($__bv['ArrivalTime'])*1000), 0, null, '')).'</span>
						<span class="hide travel-duration">'.($s2s_duration).'</span>
						<span class="hide available-seats">'.(intval($__bv['AvailableSeats'])).'</span>
						<span class="hide bus-type-count">'.($GLOBALS['CI']->bus_lib->get_bus_type_count($__bv['HasAC'], $__bv['HasNAC'], $__bv['HasSeater'], $__bv['HasSleeper'], $__bv['IsVolvo'])).'</span>
						<span class="hide bus-price">'.($bus_fare).'</span>
							<h4 class="media-heading h5"><b>'.get_time($__bv['DepartureTime']).' </b>-<b> '.get_time($__bv['ArrivalTime']).'</b></h4>
						<p class="m-0 duration" data-duration="'.$s2s_duration.'"><small>Duration - '.get_time_duration_label($s2s_duration).'</small></p>
						<a href="#" class="bus-boarding-info-btn"><small>Boarding and drop Point</small></a>
					</div>
				</div>
			</div>
			<div class="clearfix visible-sm-block"></div>
			<div class="col-md-2 col-sm-6 p-tb-10">
				<div class="media">
					<div class="media-left">
						<a href="#">
						<img alt="Bus Seat Icon" src="'.$GLOBALS['CI']->template->template_images('icons/bus-search-result-seat-icon.png').'" class="media-object">
						</a>
					</div>
					<div class="media-body">
						<h4 class="media-heading h5 text-success"><b>'.intval($__bv['AvailableSeats']).' seats</b></h4>
						<p class="m-0  text-success"><small><b> Available</b></small></p>
					</div>
				</div>
			</div>
			<div class="col-md-1 col-sm-6 b-r text-center">
				<h6 class="">
				'.$GLOBALS['CI']->bus_lib->get_bus_type($__bv['HasAC'], $__bv['HasNAC'], $__bv['HasSeater'], $__bv['HasSleeper'], $__bv['IsVolvo']).'
				</h6>
			</div>
			<div class="clearfix visible-sm-block"></div>
			<div class="col-md-2 col-sm-6 text-center">
				<h6>'.$cur_Currency.' <span class="h4 text-i">'.$bus_fare.'</span></h6>
				<form action="" method="GET" class="book-form">
					<input type="hidden" name="route_schedule_id" class="route-schedule-id" value="'.$__bv['RouteScheduleId'].'">
					<input type="hidden" name="route_code" class="route-code" value="'.$__bv['RouteCode'].'">
					<input type="hidden" name="journey_date" class="journey-date" value="'.$__bv['DepartureTime'].'">
					<input type="hidden" name="search_id" class="search-id" value="'.$search_id.'">
					<input type="hidden" name="booking_source" class="booking-source" value="'.$booking_source.'">
					<button class="btn btn-xs btn-p b-r-0 inner-summary-btn" type="button" id="route'.DB_SAFE_SEPARATOR.''.$__bv['RouteScheduleId'].'">Book Now</button>
				</form>
				<!--
				<h6>
					<a href="#">Fare Details</a>
				</h6>
				-->
			</div>
		</div>
		
		<div class="inner-summary-toggle" style="display:none;">
			<div class="row">
				<div class="col-md-12">
					<h5 class="text-info text-center">Select Seat <i class="fa fa-bus"></i><i class="fa fa-arrow-circle-down pull-right"></i></h5>
				</div>
			</div>
			'.$mini_loading_image.'
			<div class="room-summ">
			</div>
		</div>
		
	</div>';
}
$busses .= '</div>';
echo $busses;
?>