<?php
$template_images = $GLOBALS['CI']->template->template_images();
$mini_loading_image = '<div class="text-center loader-image">Please Wait <img src="'.$template_images.'loader_v3.gif" alt="Loading........"/></div>';
if (count($private_trip_indicator_group) == 1) {
	$col_division = 'col-md-12';
} elseif (count($private_trip_indicator_group) == 2) {
	$col_division = 'col-md-6';
}
//Images Url
$template_images = $GLOBALS['CI']->template->template_images();
$loc_dir_icon = '<img src="'.$template_images.'icons/flight-search-result-icon.png" alt="Flight Search Result Icon">';
$tmp_SessionId = explode(',', $SessionId);

//Change booking button based on type of flight
if ($domestic_round_way_flight) {
	$booking_button = '<button class="btn btn-p btn-sm b-r-0 mfb-btn" type="button">Select</button>';
} else {
	$booking_button = '<button class="btn btn-p b-r-0 b-btn" type="submit">Book</button>';
}

$booking_url = $GLOBALS['CI']->flight_lib->booking_url(intval($search_id));

$flights = '<div class="row rowclear">';
foreach ($private_trip_indicator_group as $__tirp_indicator => $__trip_flights) {
	$arc_session_id				= array_shift($tmp_SessionId);
	if (empty($arc_session_id) == false) {
		$SessionId					= $arc_session_id;
	}
	$flight_total_price_summary	= ''; //Total Price Summary For Each Flight
	$flights .= '<div class="'.$col_division.' r-w-g" id="t-w-i-'.$__tirp_indicator.'">';
	foreach ($__trip_flights as $__trip_flight_k => $__trip_flight_v) {
		//Reset This Everytime
		$inner_summary = $outer_summary = '';
		//$cur_TripIndicator			= $__trip_flight_v['TripIndicator'];
		//$cur_WSPTCFare				= force_multple_data_format($__trip_flight_v['FareBreakdown']['WSPTCFare']);
		//$cur_Origin					= $__trip_flight_v['Origin'];
		//$cur_Destination			= $__trip_flight_v['Destination'];
		$cur_WSSegment				= force_multple_data_format($__trip_flight_v['Segment']['WSSegment']);
		//$cur_IbDuration				= isset($__trip_flight_v['IbDuration']) ? $__trip_flight_v['IbDuration'] : 0;
		//$cur_ObDuration				= $__trip_flight_v['ObDuration'];
		//$cur_Source					= $__trip_flight_v['Source'];
		//$cur_FareRule				= $__trip_flight_v['FareRule'];
		$cur_IsLcc					= $__trip_flight_v['IsLcc'];
		//$cur_IbSegCount				= isset($__trip_flight_v['IbSegCount']) ? $__trip_flight_v['IbSegCount'] : 0;
		//$cur_ObSegCount				= $__trip_flight_v['ObSegCount'];
		$cur_PromotionalPlanType	= isset($__trip_flight_v['PromotionalPlanType']) ? $__trip_flight_v['PromotionalPlanType'] : 'N/A';
		$cur_NonRefundable			= isset($__trip_flight_v['NonRefundable']) ? $__trip_flight_v['NonRefundable'] : true;
		$Refundable_lab = ($cur_NonRefundable == true ? 'Non-Refundable' : 'Refundable');
		//$cur_SegmentKey				= $__trip_flight_v['SegmentKey'];
		//$cur_WSResult				= serialized_data($__trip_flight_v);
		$cur_WSSegment				= $GLOBALS['CI']->flight_lib->group_segment_indicator($cur_WSSegment);//Group All Flights With Segments
		$cur_Fare					= $__trip_flight_v['Fare'];
		$cur_ProvabAuthKey			= $__trip_flight_v['ProvabAuthKey'];   //For ProvabAuthKey
		$temp_price_details			= $GLOBALS['CI']->flight_lib->update_search_markup_currency($cur_Fare, $currency_obj, false, true, $attr['search_id']);
		$o_BaseFare					= ceil($temp_price_details['BaseFare']);
		$cur_Currency				= $currency_obj->get_currency_symbol($currency_obj->to_currency);
		$o_Total_Tax				= ceil($GLOBALS['CI']->flight_lib->tax_service_sum($temp_price_details, $cur_Fare));
		$o_Total_Fare				= ceil($GLOBALS['CI']->flight_lib->total_price($temp_price_details));

		//VIEW START
		//SegmentIndicator used to identifies one way or return or multi stop
		$inner_summary .= '<div class="i-i-f-s-t bg-success ' . add_special_class('xs-font', '', $domestic_round_way_flight) . '" style="display:none;">';
		$inner_summary .= '<div class="row"><div class="col-md-12"><h5 class="text-info text-center">Fare Rules <i class="fa fa-money"></i><i class="fa fa-arrow-circle-down pull-right"></i></h5></div></div>';
		$inner_summary .= $mini_loading_image;
		$inner_summary .= '<div class="i-s-s-c"></div>';
		$inner_summary .= '</div>';
		
		$inner_summary .= '<div class="i-i-s-t bg-info ' . add_special_class('xs-font', '', $domestic_round_way_flight) . '" style="display:none;">';
		$inner_summary .= '<div class="row"><div class="col-md-12"><h5 class="text-info text-center">Flight Details <i class="fa fa-plane"></i><i class="fa fa-arrow-circle-down pull-right"></i></h5></div></div>';
			$inner_summary .= '<div class="row">';//summary wrapper start
				$inner_summary .= '<div class="' . add_special_class('col-md-8', 'col-md-9', $domestic_round_way_flight) . '">';//airline summary start
				foreach ($cur_WSSegment as $__segment_k => $__segment_v) {
					$tmp_origin			= current($__segment_v);
					$tmp_destination	= end($__segment_v);
					$inner_summary .= '<div class="seg-'.$__segment_k.'">';
						//Way Summary in one line - Start
						$inner_summary .= '<div class="seg-sum">';
						$inner_summary .= '<span>'.$tmp_origin['Origin']['CityName'].'</span> <i class="fa fa-arrow-circle-right"></i> <span>'.$tmp_destination['Destination']['CityName'].'</span>';
						$inner_summary .= '</div>';
					//Way Summary in one line - End
					foreach ($__segment_v as $__stop => $__segment_flight) {
						//Summary of Way - Start
						$inner_summary .= '<div class="segment-flight row">';
							//airline
							$inner_summary .= '<div class="col-sm-6 p-tb-10 text-center ' . add_special_class('col-md-4', 'col-md-3', $domestic_round_way_flight) . '">
												<img class="airline-logo" alt="Flight Image" src="'.SYSTEM_IMAGE_DIR.'airline_logo/'.$__segment_flight['Airline']['AirlineCode'].'.gif" height="15">
												<h4 class="h6 m-b-0"><b class="a-n" data-code="'.$__segment_flight['Airline']['AirlineCode'].'">'.$__segment_flight['Airline']['AirlineName'].'</b> <strong>'.$__segment_flight['Airline']['AirlineCode'].' '.$__segment_flight['FlightNumber'].'</strong></h4>
											  </div>';
							//depart
							$inner_summary .= '<div class="col-md-3 col-sm-6 p-tb-10">
													<h4><span class="sm-hidden">'.$__segment_flight['Origin']['CityName'].'</span>('.$__segment_flight['Origin']['CityCode'].')</h4>
													<h5>'.local_time($__segment_flight['DepTIme']).', '.local_date($__segment_flight['DepTIme']).'</h5>
												</div>';
							//direction indicator
							$inner_summary .= '<div class="col-md-1 p-tb-10 ' . add_special_class('visible-xs-block', '', $domestic_round_way_flight) . '"><br>'.$loc_dir_icon.'</div>';
							//arrival
							$inner_summary .= '<div class="col-md-3 col-sm-6 p-tb-10">
													<h4><span class="sm-hidden">'.$__segment_flight['Destination']['CityName'].'</span>('.$__segment_flight['Destination']['CityCode'].')</h4>
													<h5>'.local_time($__segment_flight['ArrTime']).', '.local_date($__segment_flight['ArrTime']).'</h5>
												</div>';

							$inner_summary .= '<div class="col-sm-6 p-tb-10 ' . add_special_class('col-md-2', 'col-md-1', $domestic_round_way_flight) . '"><h4>'.get_time_duration_label(calculate_duration($__segment_flight['DepTIme'], $__segment_flight['ArrTime'])).'</h4><h5 class="stop-value" data-val="'.($__stop).'">Stop : '.($__stop).'</h5></div>';

						//Summary of Way - End
						$inner_summary .= '</div>';
						if (isset($__segment_v[$__stop+1]) == true) {
							$next_seg_info = $__segment_v[$__stop+1];
							$waiting_time = get_time_duration_label(calculate_duration($__segment_flight['ArrTime'], $next_seg_info['DepTIme']));
							$inner_summary .= '
						<div class="clearfix details-head-group">
							<hr>
							<h1 class="details-head">Plane change at '.$next_seg_info['Origin']['CityName'].' | Waiting: '.$waiting_time.'</h1>
						</div>';
						}
					}
					$inner_summary .= '</div>';
				}
				$inner_summary .= '</div>';//airline summary end
				$inner_summary .= '<div class="' . add_special_class('col-md-4', 'col-md-3', $domestic_round_way_flight) . '">';//price summary start
					$inner_summary .= '<table class="table table-responsive table-condensed m-0">';
            $inner_summary .= '<caption class="text-center">Total Fare Breakup</caption>';
            $inner_summary .= '<tbody>';
              $inner_summary .= '<tr class="info">';
                $inner_summary .= '<th>Total Base Fare</th>';
                $inner_summary .= '<td class="text-right"><span class="curr_icon">'.$cur_Currency.'</span> '.number_format($o_BaseFare).'</td>';
              $inner_summary .= '</tr>';
              $inner_summary .= '<tr class="info">';
                $inner_summary .= '<th>Taxes &amp; Fees</th>';
                $inner_summary .= '<td class="text-right"><span class="curr_icon">'.$cur_Currency.'</span> '.number_format($o_Total_Tax).'</td>';
              $inner_summary .= '</tr>';
              $inner_summary .= '<tr class="success">';
                $inner_summary .= '<th>Grand Total</th>';
                $inner_summary .= '<td class="text-right"><span class="curr_icon">'.$cur_Currency.'</span> '.number_format($o_Total_Fare).'</td>';
              $inner_summary .= '</tr>';
						$inner_summary .= '</tbody>';
  				$inner_summary .= '</table>';
				$inner_summary .= '</div>';//price summary end
			$inner_summary .= '</div>';//summary wrapper end
		$inner_summary .= '</div>';


		//Outer Summary - START
		$outer_summary .= '<div class="row rowclear madgrid ' . add_special_class('xs-font', '', $domestic_round_way_flight) . '">';
		$outer_summary .= '<div class="f-s-d-w ' . add_special_class('col-md-8', 'col-md-9', $domestic_round_way_flight) . '">';
			$total_stop_count = 0;
			foreach ($cur_WSSegment as $__segment_k => $__segment_v) {
				$tmp_origin			= current($__segment_v);
				$tmp_destination	= end($__segment_v);
				$__stop_count		= (count($__segment_v)-1);
				$total_stop_count	+= $__stop_count;

				//calculate total segment travel duration
				$total_segment_travel_duration = calculate_duration($tmp_origin['DepTIme'], $tmp_destination['ArrTime']);
				$outer_summary .= '<div class="outer-segment-'.$__segment_k.' row rowclear flightRstPad">';
					//airline
					$outer_summary .= '<div class="col-sm-6 p-tb-10 text-center col-xs-3 ' . add_special_class('col-md-4', 'col-md-3', $domestic_round_way_flight) . '">
										<img class="airline-logo" alt="Flight Image" src="'.SYSTEM_IMAGE_DIR.'airline_logo/'.$tmp_origin['Airline']['AirlineCode'].'.gif">
										<h4 class="h6 m-b-0"><b class="a-n" data-code="'.$tmp_origin['Airline']['AirlineCode'].'">'.$tmp_origin['Airline']['AirlineName'].'</b><strong> '.$tmp_origin['Airline']['AirlineCode'].' '.$tmp_origin['FlightNumber'].'</strong></h4>
									  </div>';
					//depart
					$outer_summary .= '<div class="col-md-3 col-xs-3 col-sm-6 p-tb-10 txtwrapRow">
											<h4 class="f-d-t">'.local_time($tmp_origin['DepTIme']).'<span class="mobHide hide">'.local_date($tmp_origin['DepTIme']).'</span></h4>
											<h5 class="from-loc"><span class="sm-hidden hide">'.$tmp_origin['Origin']['CityName'].'</span>'.$tmp_origin['Origin']['CityCode'].'</h5>
											<span class="dep_dt hide" data-datetime="'.(number_format((strtotime($tmp_origin['DepTIme'])*1000), 0, null, '')).'"></span>
										</div>';
					//direction indicator
					$outer_summary .= '<div class="clearfix visible-sm-block"></div>';
					$outer_summary .= '<div class="col-md-1 p-tb-10 ' . add_special_class('visible-xs-block', '', $domestic_round_way_flight) . '"><br>'.$loc_dir_icon.'</div>';
					//arrival
					$outer_summary .= '<div class="col-md-3 col-xs-3 col-sm-6 p-tb-10 noPL txtwrapRow">
											<h4 class="f-a-t">'.local_time($tmp_destination['ArrTime']).'<span class="mobHide hide">'.local_date($tmp_destination['ArrTime']).'</span></h4>
											<h5 class="to-loc"><span class="sm-hidden hide">'.$tmp_destination['Destination'] ['CityName'].'</span>'.$tmp_destination['Destination'] ['CityCode'].'</h5>
											<span class="arr_dt hide" data-datetime="'.(number_format((strtotime($tmp_origin['ArrTime'])*1000), 0, null, '')).'"></span>
										</div><div class="clearfix visible-xs-block"></div>';
					$outer_summary .= '<div class="col-sm-6 p-tb-10 col-xs-6 ' . add_special_class('col-md-2', 'col-md-1', $domestic_round_way_flight) . '"><span class="f-d hide">'.$total_segment_travel_duration.'</span><h5>'.get_time_duration_label($total_segment_travel_duration).'</h5> <h5 class="stop-value" data-val="'.($__stop_count).'">Stop:'.($__stop_count).'</h5></div>';
					$outer_summary .= '<div class="clearfix visible-sm-block"></div>';
				$outer_summary .= '</div>';
			}
		$outer_summary .= '</div><div class="clearfix visible-xs-block"></div>';
		$outer_summary .= '
					<div class="price-details-summary-wrapper mobRht ' . add_special_class('col-md-3' , 'col-xs-3', $domestic_round_way_flight) . '">
						<span class="hide stp" data-stp="'.$total_stop_count.'"></span>
						<div class="row row-no-gutter">
							<div class="col-md-9 p-tb-10 text-center">
								<h4 class="rsMob"><span class="flt_currency"> '.$cur_Currency.' </span><span class="h3 text-i f-p">'.$o_Total_Fare.'</span></h4>
								<span class="hide price" data-price="'.$o_Total_Fare.'" data-currency="'.$cur_Currency.'"></span>
								<div data-val="'.intval($cur_NonRefundable).'" class="n-r n-r-t">'.$Refundable_lab.'</div>
								<div class="form-wrapper">
								<form method="POST" action="'.$booking_url.'" class="book-form-wrapper">
									'.$GLOBALS['CI']->flight_lib->booking_form($IsDomestic, $__trip_flight_v['Token'], $__trip_flight_v['TokenKey'], $SessionId, $cur_IsLcc, $GLOBALS['CI']->flight_lib->get_booking_type($cur_IsLcc), $cur_PromotionalPlanType).'
									'.$booking_button.'
								</form>
								</div>
							</div>
							<div class="mobHide col-md-3">
								<div role="group" class="btn-group btn-group-vertical ' . add_special_class('btn-group-xs', '', $domestic_round_way_flight) . '">
									<button class="btn btn-default b-r-0 send-quotation-details" type="button"><i class="fa fa-envelope"></i></button>
									<button class="btn btn-default b-r-0 iti-fare-btn" type="button"><i class="fa fa-money"></i></button>
									<button class="btn btn-default b-r-0 iti-btn" type="button">
										<img alt="Double Arrow Down Icon" src="'.$template_images.'icons/double-arrow-down-icon.png">
									</button>
								</div>
							</div>
						</div>
					</div>';
		//Outer Summary - END
		$outer_summary .= '</div>';

		$flights .= '
			<div class="panel-body text-center p-0 r-r-i t-w-i-'.$__tirp_indicator.'">
			'.$outer_summary.'
			'.$inner_summary.'
			</div>
		';
	}
	$flights .= '</div>';
	$flight_total_price_summary = ''; //Initialize to empty so next loop gets fresh data
	
}
$flights .= '</div>';
echo $flights;

/**
 * Return class based on type of page
 */
function add_special_class($col_2x_class, $col_1x_class, $domestic_round_way_flight)
{
	if ($domestic_round_way_flight) {
		return $col_2x_class;
	} else {
		return $col_1x_class;
	}
}
