<?php

$raw_bus_list = force_multple_data_format($raw_bus_list);
$cur_Currency = $currency_obj->get_currency_symbol($currency_obj->to_currency);
$template_images = $GLOBALS['CI']->template->template_images();
$mini_loading_image = '<div class="text-center loader-image">Please Wait <img src="' . $template_images . 'loader_v3.gif" alt="Loading........"/></div>';
$busses = '<div class="r-w-g allbusresult">';
foreach ($raw_bus_list as $__bk => $__bv) {
    // debug($__bv);exit;
    $filt_dept_time = str_replace('T', ' ', $__bv['DepartureTime']);
    $filt_arrv_time = str_replace('T', ' ', $__bv['ArrivalTime']);
    $route_id = $__bv['RouteScheduleId'].'*'.$search_id.'*'.$__bv['RouteCode'].'*'.$booking_source;
    // $bus_booking_data = array(
    //     'RouteScheduleId' => $__bv['RouteScheduleId'],
    //     'CompanyName' => $__bv['CompanyName'],
    //     'CompanyId' => $__bv['CompanyId'],
    //     'From' => $__bv['From'],
    //     'To' => $__bv['To'],
    //     'Form_id' => $search_data_city['from_id'],
    //     'To_id' => $search_data_city['to_id'],
    //     'DeptTime' => $__bv['DeptTime'],
    //     'DepartureTime' => $__bv['DepartureTime'],
    //     'ArrTime' => $__bv['ArrTime'],
    //     'ArrivalTime' => $__bv['ArrivalTime'],
    //     'Fare' => $__bv['Fare'],
    //     'HasAC' => $__bv['HasAC'],
    //     'HasNAC' => $__bv['HasNAC'],
    //     'HasSleeper' => @$__bv['HasSleeper'],
    //     'HasSeater' => @$__bv['HasSeater'],
    //     'BusLabel' => $__bv['BusLabel'],
    //     'BusTypeName' => $__bv['BusTypeName'],
    //     'CommAmount' => $__bv['CommAmount'],
    //     'CommPCT' => @$__bv['CommPCT']);
// debug($bus_booking_data);exit;
    // $bus_booking_data = base64_encode(json_encode($bus_booking_data));
    $bus_booking_data ='';
    $bus_fare_details = $currency_obj->get_currency($__bv['Fare']);
    $bus_fare = $bus_fare_details['default_value'];
    $s2s_duration = calculate_duration($__bv['DepartureTime'], $__bv['ArrivalTime']);
    $busses .= '
			<div class="busrows r-r-i">
				<div class="col-xs-4 nopad fiftywirdt1 dflex">
				<div class="icon_bus">
                 <img src="' . $template_images . 'location_Bus.png" alt=""/>
				</div>
				<div class="inpadbus mobile_pad_bus">
					<div class="busername  travel-name">' . $__bv['CompanyName'] . '</div>
					<div class="bustype">' . $__bv['BusTypeName'] . '</div>
					<a class="poptoup bus-boarding-info-btn" data-target=".cancellation-wrapper">Canc.Policy</a>
				</div>
			</div>
			<div class="col-xs-5 nopad fiftywirdt2">
				<div class="col-xs-4 col-md-5 nopad">
					<div class="inpadbus cenertext">
						<span class="hide departure-time">' . date('Hi', strtotime($filt_dept_time)) . '</span>
						<span class="hide arrival-time">' . date('Hi', strtotime($filt_arrv_time)) . '</span>
						<span class="hide travel-duration">' . ($s2s_duration) . '</span>
						<span class="departure_datetime hide" data-departure-category="' . time_filter_category($__bv['DepartureTime']) . '"></span>
						<span class="arrival_datetime hide" data-arrival-category="' . time_filter_category($__bv['ArrivalTime']) . '"></span>
						<span class="hide available-seats">' . (intval($__bv['AvailableSeats'])) . '</span>
						<span class="hide bus-type-count">' . ($GLOBALS['CI']->bus_lib->get_bus_type_count(@$__bv['HasAC'], @$__bv['HasNAC'], @$__bv['HasSeater'], @$__bv['HasSleeper'], @$__bv['IsVolvo'])) . '</span>
						<span class="hide bus-price">' . ($bus_fare) . '</span>
						<a class="timeicon timings ' . time_filter_category_class($__bv['DepartureTime']) . '" title="' . time_filter_category_label($__bv['DepartureTime']) . '"></a>
						<div class="timelabelf">' . get_time($__bv['DepartureTime']) . ' </div>
						<a class="poptoup bus-boarding-info-btn" data-target=".pick-up-wrapper">Pickups</a>
					</div>
				</div>
				<div class="col-xs-4 col-md-2 nopad ">
					<div class="arowwspr">
						<span class="desk_arrow fa fa-long-arrow-right"></span>
						<span class="mobile_clock fa fa-clock-o"></span>
						<span class="durtnlabls duration" data-duration="' . $s2s_duration . '">' . get_time_duration_label($s2s_duration) . '</span>
					</div>
				</div>
				<div class="col-xs-4 col-md-5 nopad">
					<div class="inpadbus cenertext">
						<a class="timeicon timings ' . time_filter_category_class($__bv['ArrivalTime']) . '" title="' . time_filter_category_label($__bv['ArrivalTime']) . '"></a>
						<div class="timelabelf">' . get_time($__bv['ArrivalTime']) . '</div>
						<a class="poptoup bus-boarding-info-btn" data-target=".drop-wrapper">Dropoffs</a>
					</div>
				</div>
			</div>
			<div class="col-xs-3 nopad full_bus_prices">
				<div class="col-xs-12 col-xs-5 nopad mobile_rt">
					<div class="inpadbus mobile_rt">
						<a class="timeicon timings icseats" title="Seats"></a>
						<div class="seatcnt">' . intval($__bv['AvailableSeats']) . ' <strong>seats</strong></div>
					</div>
				</div>
				<div class="hide">
					' . $GLOBALS['CI']->bus_lib->get_bus_type($__bv['HasAC'], $__bv['HasNAC'], @$__bv['HasSeater'], @$__bv['HasSleeper'], $__bv['IsVolvo']) . '
				</div>
				<div class="col-xs-12 col-xs-7 nopad mobile_rt">
					<div class="inpadbus">
						<div class="bustprice"><strong>' . $cur_Currency . '</strong>' . $bus_fare . '</div>
						<div class="bookbuss">
						<form action="" method="GET" class="book-form">
								<input type="hidden" name="route_schedule_id" class="route-schedule-id" value="' . $route_id . '">
							
								<button class="inner-summary-btn bookallbtn" type="button" id="route' . DB_SAFE_SEPARATOR . '' . $__bv['RouteScheduleId'] . '">Select Seats</button>
							</form>
						</div>
					</div>
				</div>
			</div>
			<div class="inner-summary-toggle" style="display:none;">
				<div class="buseatselct">
				' . $mini_loading_image . '
				<div class="room-summ">
				</div>
				</div>
			</div>
		</div>';
}
$busses .= '</div>';
echo $busses;

function time_filter_category($time_value) {
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

function time_filter_category_class($time_value) {
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

function time_filter_category_label($time_value) {
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
