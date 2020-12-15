<?php
$cur_Currency = $currency_obj->get_currency_symbol($currency_obj->to_currency);
$template_images = $GLOBALS['CI']->template->template_images();
$mini_loading_image = '<div class="text-center loader-image">Please Wait <img src="'.$template_images.'loader_v3.gif" alt="Loading........"/></div>';
$busses = '<div class="r-w-g allbusresult">';
foreach($raw_bus_list as $__bk => $__bv) {
	$bus_fare_details			= $this->bus_lib->update_markup_currency($__bv, $currency_obj, 1);//Has all converted currency
	$nf_without_makrup			= $__bv;

	$bus_fare					= $bus_fare_details['Fare'];
	$bus_net_fare				= @$nf_without_makrup['Net']['Fare'];//If Zero seats available, we would not get this tag
	$bus_net_fare_commission	= @$nf_without_makrup['Commission']['Fare'];//If Zero seats available, we would not get this tag
	$bus_total_markup			= $bus_fare_details['Fare'] - $__bv['Fare'];
	$bus_total_earning			= $bus_net_fare_commission+$bus_total_markup;
	$b2b_comm_pct				= $nf_without_makrup['B2BCommPct'];
	$s2s_duration				= calculate_duration($__bv['DepartureTime'], $__bv['ArrivalTime']);
	$busses .= '
		<div class="busrows r-r-i">
		<div class="col-xs-4 nopad fiftywirdt">
			<div class="inpadbus">
				<div class="busername  travel-name">'.$__bv['CompanyName'].'</div>
				<div class="bustype">'.$__bv['BusTypeName'].'</div>
				<a class="poptoup bus-boarding-info-btn" data-target=".cancellation-wrapper">Canc.Policy</a>
			</div>
		</div>
		<div class="col-xs-5 nopad fiftywirdt">
			<div class="col-xs-5 nopad">
				<div class="inpadbus cenertext">
					<span class="hide departure-time">'.(number_format((strtotime($__bv['DepartureTime'])*1000), 0, null, '')).'</span>
					<span class="hide arrival-time">'.(number_format((strtotime($__bv['ArrivalTime'])*1000), 0, null, '')).'</span>
					<span class="hide travel-duration">'.($s2s_duration).'</span>
					<span class="departure_datetime hide" data-departure-category="'.time_filter_category($__bv['DepartureTime']).'"></span>
					<span class="arrival_datetime hide" data-arrival-category="'.time_filter_category($__bv['ArrivalTime']).'"></span>
					<span class="hide available-seats">'.(intval($__bv['AvailableSeats'])).'</span>
					<span class="hide bus-type-count">'.($GLOBALS['CI']->bus_lib->get_bus_type_count($__bv['HasAC'], $__bv['HasNAC'], $__bv['HasSeater'], $__bv['HasSleeper'], $__bv['IsVolvo'])).'</span>
					<span class="hide bus-price">'.($bus_fare).'</span>
					<a class="timeicon timings '.time_filter_category_class($__bv['DepartureTime']).'" title="'.time_filter_category_label($__bv['DepartureTime']).'"></a>
					<div class="timelabelf">'.get_time($__bv['DepartureTime']).' </div>
					<a class="poptoup bus-boarding-info-btn" data-target=".pick-up-wrapper">Pickups</a>
				</div>
			</div>
			<div class="col-xs-2 nopad ">
				<div class="arowwspr">
					<span class="fa fa-long-arrow-right"></span>
					<span class="durtnlabls duration" data-duration="'.$s2s_duration.'">'.get_time_duration_label($s2s_duration).'</span>
				</div>
			</div>
			<div class="col-xs-5 nopad">
				<div class="inpadbus cenertext">
					<a class="timeicon timings '.time_filter_category_class($__bv['ArrivalTime']).'" title="'.time_filter_category_label($__bv['ArrivalTime']).'"></a>
					<div class="timelabelf">'.get_time($__bv['ArrivalTime']).'</div>
					<a class="poptoup bus-boarding-info-btn" data-target=".drop-wrapper">Dropoffs</a>
				</div>
			</div>
		</div>
		<div class="col-xs-3 nopad full_bus_prices">
			<div class="col-xs-5 nopad">
				<div class="inpadbus">
					<a class="timeicon timings icseats" title="Seats"></a>
					<div class="seatcnt">'.intval($__bv['AvailableSeats']).' <strong>seats</strong></div>
				</div>
			</div>
			<div class="hide">
				'.$GLOBALS['CI']->bus_lib->get_bus_type($__bv['HasAC'], $__bv['HasNAC'], $__bv['HasSeater'], $__bv['HasSleeper'], $__bv['IsVolvo']).'
			</div>
			<div class="col-xs-7 nopad">
				<div class="inpadbus">
					<div class="bustprice"><strong>'.$cur_Currency.'</strong>'.$bus_fare.'</div>
					<div style="display:none" class="net-fare-tag snf_hnf" title="C '.$bus_net_fare_commission.'('.$b2b_comm_pct.'%)+M '.$bus_total_markup.' = '.$bus_total_earning.'">'.$cur_Currency.' <span>'.$bus_net_fare.'</div>
					<div class="bookbuss">
						<form action="" method="GET" class="book-form">
							<input type="hidden" name="route_schedule_id" class="route-schedule-id" value="'.$__bv['RouteScheduleId'].'">
							<input type="hidden" name="route_code" class="route-code" value="'.$__bv['RouteCode'].'">
							<input type="hidden" name="journey_date" class="journey-date" value="'.$__bv['DepartureTime'].'">
							<input type="hidden" name="search_id" class="search-id" value="'.$search_id.'">
							<input type="hidden" name="booking_source" class="booking-source" value="'.$booking_source.'">
							<button class="inner-summary-btn bookallbtn" type="button" id="route'.DB_SAFE_SEPARATOR.''.$__bv['RouteScheduleId'].'">Select Seats</button>
						</form>
					</div>
				</div>
			</div>
		</div>
		<div class="inner-summary-toggle" style="display:none;">
			<div class="buseatselct">
			'.$mini_loading_image.'
			<div class="room-summ">
			</div>
			</div>
		</div>
	</div>';
}
$busses .= '</div>';
echo $busses;

function time_filter_category($time_value)
{
	$category = 4;
	$time_offset = intval(date('H', strtotime($time_value)));
	if ($time_offset < 5 || $time_offset >= 21) {
		$category = 4;
	} elseif ($time_offset < 9 && $time_offset >= 5) {
		$category = 1;
	} elseif ($time_offset < 17 && $time_offset >= 9) {
		$category = 2;
	} else {
		$category = 3;
	}
	return $category;
}

function time_filter_category_class($time_value)
{
	$category = 'icnight';
	$time_offset = intval(date('H', strtotime($time_value)));
	if ($time_offset < 5 || $time_offset >= 21) {
		$category = 'icnight';
	} elseif ($time_offset < 9 && $time_offset >= 5) {
		$category = 'icmorning';
	} elseif ($time_offset < 17 && $time_offset >= 9) {
		$category = 'icafternoon';
	} else {
		$category = 'icevening';
	}
	return $category;
}
function time_filter_category_label($time_value)
{
	$category = 'Night';
	$time_offset = intval(date('H', strtotime($time_value)));
	if ($time_offset < 5 || $time_offset >= 21) {
		$category = 'Night';
	} elseif ($time_offset < 9 && $time_offset >= 5) {
		$category = 'Early Morning';
	} elseif ($time_offset < 17 && $time_offset >= 9) {
		$category = 'Morning/Afternoon';
	} else {
		$category = 'Evening';
	}
	return $category;
}
?>