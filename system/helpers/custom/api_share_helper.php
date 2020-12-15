<?php
function bus_email_voucher($app_reference, $booking_source='', $status='')
{
	return '<a href="'.bus_voucher_url($app_reference, $booking_source, $status).'/email_voucher" title="Email Voucher" target="_blank" class="btn btn-sm btn-success email-voucher"><i class="far fa-envelope"></i> </a>';
}
function bus_voucher($app_reference, $booking_source='', $status='',$module='')
{
	return '<a href="'.bus_voucher_url($app_reference, $booking_source, $status,$module).'/show_voucher" title="Voucher" target="_blank" class="btn btn-sm btn-primary"><i class="far fa-ticket"></i></a>';
}


function bus_voucher_url($app_reference, $booking_source='', $status='',$module='')
{
	if($module == 'b2c'){
		return base_url().'index.php/voucher/b2c_bus_voucher/'.$app_reference.'/'.$booking_source.'/'.$status;
	}elseif($module =='b2b'){
		return base_url().'index.php/voucher/b2b_bus_voucher/'.$app_reference.'/'.$booking_source.'/'.$status;
	}else{
		return base_url().'index.php/voucher/bus/'.$app_reference.'/'.$booking_source.'/'.$status;
	}
	
}
function bus_pdf($app_reference, $booking_source='', $status='')
{
	return '<a href="'.bus_voucher_url($app_reference, $booking_source, $status).'/show_pdf" target="_blank" title="PDF" class="btn btn-sm btn-primary"><i class="far fa-file-pdf"></i></a>';
}


function bus_pdf_url($app_reference, $booking_source='', $status='')
{
	return base_url().'index.php/voucher/bus/'.$app_reference.'/'.$booking_source.'/'.$status;
}
function car_voucher($app_reference, $booking_source='', $status='')
{
	return '<a href="'.car_voucher_url($app_reference, $booking_source, $status).'/show_voucher" title="Voucher" target="_blank" class="btn btn-sm btn-primary flight_v"><i class="far fa-file"></i> </a>';
}
function car_voucher_url($app_reference, $booking_source='', $status='')
{
	return base_url().'voucher/car/'.$app_reference.'/'.$booking_source.'/'.$status;
}
function car_pdf($app_reference, $booking_source='', $status='')
{
	return '<a href="'.car_voucher_url($app_reference, $booking_source, $status).'/show_pdf" target="_blank" title="PDF" class="btn btn-sm btn-primary"><i class="far fa-file-pdf"></i> </a>';
}
function car_email_voucher($app_reference, $booking_source='', $status='')
{
	return '<a href="'.car_voucher_url($app_reference, $booking_source, $status).'/email_voucher" title="Email Voucher" target="_blank" class="btn btn-sm btn-success email-voucher"><i class="far fa-envelope"></i> </a>';
}
function cancel_car_booking($app_reference, $booking_source='', $status='')
{
	return '<a href="'.cancel_car_booking_url($app_reference, $booking_source, $status).'" target="_blank" title="Cancel" class="btn btn-sm btn-warning"><i class="far fa-ban"></i> </a>';
}

function bus_cancel($app_reference, $booking_source='', $status='')
{
	if($status == 'BOOKING_CONFIRMED') {
		return '<a href="'.bus_cancel_url($app_reference, $booking_source, $status).'" target="_blank" title="Cancel" class="btn btn-sm btn-danger"><i class="far fa-ban"></i></a>';
	}
}
function bus_cancel_url($app_reference, $booking_source='', $status='')
{
	return base_url().'index.php/bus/pre_cancellation/'.$app_reference.'/'.$booking_source.'/'.$status;
}
/**
 * Hotel Voucher
 */

function hotel_email_voucher($app_reference, $booking_source='', $status='')
{
	return '<a href="'.hotel_voucher_url($app_reference, $booking_source, $status).'/email_voucher" title="Email Voucher" target="_blank" class="btn btn-sm btn-success email-voucher"><i class="far fa-envelope"></i> </a>';
}
function hotel_voucher($app_reference, $booking_source='', $status='',$module='')
{
	return '<a href="'.hotel_voucher_url($app_reference, $booking_source, $status,$module).'/show_voucher" title="Voucher" target="_blank" class="btn btn-sm btn-primary"><i class="far fa-file"></i> </a>';
}
function hotel_voucher_url($app_reference, $booking_source='', $status='',$module='')
{
	if($module == 'b2c'){
		return base_url().'index.php/voucher/b2c_hotel_voucher/'.$app_reference.'/'.$booking_source.'/'.$status;
	}
	elseif($module == 'b2b'){
		return base_url().'index.php/voucher/b2b_hotel_voucher/'.$app_reference.'/'.$booking_source.'/'.$status;
	}else{
		return base_url().'index.php/voucher/hotel/'.$app_reference.'/'.$booking_source.'/'.$status;
	}
	
}
function hotel_pdf($app_reference, $booking_source='', $status='')
{
	return '<a href="'.hotel_pdf_url($app_reference, $booking_source, $status).'/show_pdf" title="PDF" target="_blank" class="btn btn-sm btn-primary"><i class="far fa-file-pdf"></i> </a>';
}
function hotel_pdf_url($app_reference, $booking_source='', $status='')
{
	return base_url().'index.php/voucher/hotel/'.$app_reference.'/'.$booking_source.'/'.$status;
}

function cancel_hotel_booking($app_reference, $booking_source='', $status='')
{
	return '<a href="'.cancel_hotel_booking_url($app_reference, $booking_source, $status).'" target="_blank" title="Cancel" class="btn btn-sm btn-warning"><i class="far fa-ban"></i> </a>';
}
function cancel_car_booking_url($app_reference, $booking_source='', $status='')
{
	return base_url().'index.php/car/pre_cancellation/'.$app_reference.'/'.$booking_source.'/'.$status;
}
function cancel_hotel_booking_url($app_reference, $booking_source='', $status='')
{
	return base_url().'index.php/hotel/pre_cancellation/'.$app_reference.'/'.$booking_source.'/'.$status;
}

/**
 * Sightseeing Voucher
 */

function sightseeing_email_voucher($app_reference, $booking_source='', $status='')
{
	return '<a href="'.sightseeing_voucher_url($app_reference, $booking_source, $status).'/email_voucher" title="Email Voucher" target="_blank" class="btn btn-sm btn-success email-voucher"><i class="far fa-envelope"></i> </a>';
}
function sightseeing_voucher($app_reference, $booking_source='', $status='',$module='')
{
	return '<a href="'.sightseeing_voucher_url($app_reference, $booking_source, $status,$module).'/show_voucher" title="Voucher" target="_blank" class="btn btn-sm btn-primary"><i class="far fa-file"></i> </a>';
}
function sightseeing_voucher_url($app_reference, $booking_source='', $status='',$module='')
{
	if($module == 'b2c'){
		return base_url().'index.php/voucher/b2c_sightseeing_voucher/'.$app_reference.'/'.$booking_source.'/'.$status;
	}
	elseif($module == 'b2b'){
		return base_url().'index.php/voucher/b2b_sightseeing_voucher/'.$app_reference.'/'.$booking_source.'/'.$status;
	}else{
		return base_url().'index.php/voucher/activities/'.$app_reference.'/'.$booking_source.'/'.$status;
	}
	
}
function sightseeing_pdf($app_reference, $booking_source='', $status='',$module='')
{


	return '<a href="'.sightseeing_pdf_url($app_reference, $booking_source, $status,$module).'/show_pdf" title="PDF" target="_blank" class="btn btn-sm btn-primary"><i class="far fa-file-pdf"></i> </a>';
}
function sightseeing_pdf_url($app_reference, $booking_source='', $status='',$module='')
{	

	if($module == 'b2c'){
		return base_url().'index.php/voucher/b2c_sightseeing_voucher/'.$app_reference.'/'.$booking_source.'/'.$status;
	}
	elseif($module == 'b2b'){
		return base_url().'index.php/voucher/b2b_sightseeing_voucher/'.$app_reference.'/'.$booking_source.'/'.$status;
	}else{
		return base_url().'index.php/voucher/activities/'.$app_reference.'/'.$booking_source.'/'.$status;
	}

	//return base_url().'index.php/voucher/activities/'.$app_reference.'/'.$booking_source.'/'.$status;
}

function cancel_sightseeing_booking($app_reference, $booking_source='', $status='',$module='b2c')
{
	return '<a href="'.cancel_sightseeing_booking_url($app_reference, $booking_source, $status,$module).'" title="Cancel" target="_blank" class="btn btn-sm btn-warning"><i class="far fa-ban"></i> </a>';
}

function cancel_sightseeing_booking_url($app_reference, $booking_source='', $status='',$module='')
{
	return base_url().'index.php/sightseeing/pre_cancellation/'.$app_reference.'/'.$booking_source.'/'.$status.'/'.$module;
}

/**
 * Transfers Voucher
 */

function transfers_email_voucher($app_reference, $booking_source='', $status='')
{
	return '<a href="'.transfers_voucher_url($app_reference, $booking_source, $status).'/email_voucher" title="Email Voucher" target="_blank" class="btn btn-sm btn-success email-voucher"><i class="far fa-envelope"></i> </a>';
}
function transfers_voucher($app_reference, $booking_source='', $status='',$module='')
{
	return '<a href="'.transfers_voucher_url($app_reference, $booking_source, $status,$module).'/show_voucher" title="Voucher" target="_blank" class="btn btn-sm btn-primary"><i class="far fa-file"></i> </a>';
}
function transfers_voucher_url($app_reference, $booking_source='', $status='',$module='')
{
	if($module == 'b2c'){
		return base_url().'index.php/voucher/b2c_transfers_voucher/'.$app_reference.'/'.$booking_source.'/'.$status;
	}
	elseif($module == 'b2b'){
		return base_url().'index.php/voucher/b2b_transfers_voucher/'.$app_reference.'/'.$booking_source.'/'.$status;
	}else{
		return base_url().'index.php/voucher/transfers/'.$app_reference.'/'.$booking_source.'/'.$status;
	}
	
}
function transfers_pdf($app_reference, $booking_source='', $status='', $module='')
{
	
	return '<a href="'.transfers_voucher_url($app_reference, $booking_source, $status, $module).'/show_pdf" title="PDF" target="_blank" class="btn btn-sm btn-primary"><i class="far fa-file-pdf"></i> </a>';
}
function transfers_pdf_url($app_reference, $booking_source='', $status='')
{
	return base_url().'index.php/voucher/transfers/'.$app_reference.'/'.$booking_source.'/'.$status;
}

function cancel_transfers_booking($app_reference, $booking_source='', $status='',$module='')
{
	return '<a href="'.cancel_transfers_booking_url($app_reference, $booking_source, $status,$module).'" title="Cancel" target="_blank" class="btn btn-sm btn-warning"><i class="far fa-ban"></i> </a>';
}

function cancel_transfers_booking_url($app_reference, $booking_source='', $status='',$module='')
{
	return base_url().'index.php/transferv1/pre_cancellation/'.$app_reference.'/'.$booking_source.'/'.$status.'/'.$module;
}
/*************End*********/
/**
 * Flight Voucher
 */
function flight_email_voucher()
{
	return '<a data-href="'.flight_voucher_url($app_reference='', $booking_source='', $status='').'/email_voucher" title="Email Voucher" target="_blank" class="btn btn-sm btn-success email-voucher"><i class="far fa-envelope"></i> </a>';
}
function flight_voucher($app_reference, $booking_source='', $status='',$module='')
{
	// echo flight_voucher_url($app_reference, $booking_source, $status,$module);exit;
	return '<a href="'.flight_voucher_url($app_reference, $booking_source, $status,$module).'/show_voucher" title="Voucher" target="_blank" class="btn btn-sm btn-primary"><i class="far fa-ticket"></i> </a>';
}
function flight_voucher_url($app_reference, $booking_source='', $status='',$module='')
{
	if($module == 'b2c'){
		// echo 'herer I am';exit;
		return base_url().'index.php/voucher/b2c_flight_voucher/'.$app_reference.'/'.$booking_source.'/'.$status;
	}elseif($module == 'b2b'){
		return base_url().'index.php/voucher/b2b_flight_voucher/'.$app_reference.'/'.$booking_source.'/'.$status;
	}else{
		return base_url().'index.php/voucher/flight/'.$app_reference.'/'.$booking_source.'/'.$status;
	}
	
}
function flight_pdf($app_reference, $booking_source='', $status='', $module='')
{
	// echo $status;exit;
	return '<a href="'.flight_voucher_url($app_reference, $booking_source, $status, $module).'/show_pdf" title="PDF" target="_blank" class="btn btn-sm btn-primary"><i class="far fa-file-pdf"></i></a>';
}
function flight_pdf_url($app_reference, $booking_source='', $status='')
{
	return base_url().'index.php/voucher/flight/'.$app_reference.'/'.$booking_source.'/'.$status;
}
/**
 * INVOICE For Flight Booing
 */
function flight_invoice_url($app_reference, $booking_source='', $status='')
{
	return base_url().'index.php/voucher/flight_invoice/'.$app_reference.'/'.$booking_source.'/'.$status;
}
function flight_GST_Invoice($app_reference, $booking_source='', $status='',$module='')
{
    return '<a href="'.base_url().'index.php/voucher/flight_invoice_GST/'.$app_reference.'/'.$booking_source.'/'.$status.'/'.$module.'" target="_blank" class="btn btn-sm btn-primary"
    	title="Invoice"><i class="fa fa-sticky-note"></i></a>';
	//return base_url().'index.php/voucher/flight_invoice/'.$app_reference.'/'.$booking_source.'/'.$status;
}

function hotel_GST_Invoice($app_reference, $booking_source='', $status='',$module='')
{
    return '<a href="'.base_url().'index.php/voucher/hotel_invoice_GST/'.$app_reference.'/'.$booking_source.'/'.$status.'/'.$module.'" target="_blank" class="btn btn-sm btn-primary"><i class="far fa-file"></i> Invoice</a>';
	
}

function bus_GST_Invoice($app_reference, $booking_source='', $status='',$module='')
{
    return '<a href="'.base_url().'index.php/voucher/bus_invoice_GST/'.$app_reference.'/'.$booking_source.'/'.$status.'/'.$module.'" target="_blank" class="btn btn-sm btn-primary"
    	title="Invoice"><i class="far fa-sticky-note"></i></a>';
	
}
function activity_GST_Invoice($app_reference, $booking_source='', $status='',$module='')
{
    return '<a href="'.base_url().'index.php/voucher/activity_invoice_GST/'.$app_reference.'/'.$booking_source.'/'.$status.'/'.$module.'" target="_blank" class="btn btn-sm btn-primary"><i class="far fa-file"></i> Invoice</a>';
	
}
function transfer_GST_Invoice($app_reference, $booking_source='', $status='',$module='')
{
    return '<a href="'.base_url().'index.php/voucher/transfer_invoice_GST/'.$app_reference.'/'.$booking_source.'/'.$status.'/'.$module.'" target="_blank" class="btn btn-sm btn-primary"><i class="far fa-file"></i> Invoice</a>';
	
}
function flight_invoice($app_reference, $booking_source='', $status='')
{
	return '<a href="'.flight_invoice_url($app_reference, $booking_source, $status).'/show_voucher" title="Voucher" target="_blank" class="btn btn-sm btn-primary"><i class="far fa-file"></i> Invoice</a>';
}
function flight_cancel($app_reference, $booking_source='', $status='')
{
	if($status == 'BOOKING_CONFIRMED') {
		return '<a href="'.flight_cancel_url($app_reference, $booking_source, $status).'" title="Cancel" target="_blank" class="btn btn-sm btn-warning"><i class="far fa-ban"></i> </a>';
	}
}
function flight_cancel_url($app_reference, $booking_source='', $status='')
{
	return base_url().'index.php/flight/pre_cancellation/'.$app_reference.'/'.$booking_source.'/'.$status;
}

function check_run_ticket_method($app_reference ,$booking_source, $status,$is_domestic,$travel_date)
{
	$today_date = date('Y-m-d H:i:s');
	if($status == 'BOOKING_HOLD' && $today_date<=$travel_date) {
		return '<a data-app-reference="'.$app_reference.'" data-booking-source="'.$booking_source.'"data-booking-status="'.$status.'" class="btn btn-sm btn-warning  issue_hold_ticket"><i class="far fa-moniter"></i> Confirm Ticket</a>';
	}
}

function flight_run_ticket_url($app_reference, $booking_source,$status)
{
	return base_url().'index.php/flight/run_ticketing_method/'.$app_reference.'/'.$booking_source;
}
//----------------------------------------------------------------------------------------------------------------------------------
/**
 * Convert numeric index to room key indexed array
 * @param array $HotelRoomsDetails
 */
function get_room_index_list($HotelRoomsDetails)
{
	$tmp_room_details = array();
	/**
	 * Forcing room list to appear in multiple list format
	 */
	if (isset($HotelRoomsDetails[0]) == false) {
		$HotelRoomsDetails[0] = $HotelRoomsDetails;
	}
	foreach ($HotelRoomsDetails as $__room) {
		$tmp_room_details[$__room['RoomIndex']] = $__room;
	}
	return $tmp_room_details;
}

function get_smoking_preference($preference)
{
	$temp_preference['label'] = '';
	$temp_preference['code'] = '';
	switch ($preference)
	{
		case 'NoPreference':
			$temp_preference['label'] = 'No Preference';
			$temp_preference['code'] = 0;
			break;
		case 'Smoking':
			$temp_preference['label'] = 'Smoking';
			$temp_preference['code'] = 1;
			break;
		case 'NonSmoking':
			$temp_preference['label'] = 'Non Smoking';
			$temp_preference['code'] = 2;
			break;
		case 'Either':
			$temp_preference['label'] = 'Either';
			$temp_preference['code'] = 3;
			break;
		default : $temp_preference['label'] = 'NA';
		$temp_preference['code'] = '';
		break;
	}
	return $temp_preference;
}

function get_booking_status($status)
{
	switch (strtoupper($status))
	{
		case 'CONFIRMED': $status = 'BOOKING_CONFIRMED';
		break;
		case 'PENDING':
			$status = 'BOOKING_PENDING';
			break;
		case 'VOUCHERED':
			$status = 'BOOKING_VOUCHERED';
			break;
		case 'FAILED':
			$status = 'BOOKING_FAILED';
			break;
	}
	return $status;
}

/**
 * custom function needed as its requiredget_dynamic_booking_parameters in multiple places
 * @param number $room_index
 * @param array  $room_details
 * @param string $application_default_currency
 */
function get_dynamic_booking_parameters($room_index, $room_details, $application_default_currency)
{
	$dynamic_params_url = '';
	$dynamic_params_url['RoomIndex']				= $room_index;
	$dynamic_params_url['RatePlanCode']				= $room_details['RatePlanCode'];
	$dynamic_params_url['RatePlanName']				= @$room_details['RatePlanName'];
	$dynamic_params_url['RoomTypeCode']				= $room_details['RoomTypeCode'];
	$dynamic_params_url['RoomTypeName']				= @$room_details['RoomTypeName'];
	$dynamic_params_url['CurrencyCode']				= $room_details['API_raw_price']['CurrencyCode'];

	$dynamic_params_url['RoomPrice']				= $room_details['API_raw_price']['RoomPrice'];
	$dynamic_params_url['PublishedPrice']			= $room_details['API_raw_price']['PublishedPrice'];
	$dynamic_params_url['PublishedPriceRoundedOff']	= $room_details['API_raw_price']['PublishedPriceRoundedOff'];
	$dynamic_params_url['OfferedPrice']				= $room_details['API_raw_price']['OfferedPrice'];
	$dynamic_params_url['OfferedPriceRoundedOff']	= $room_details['API_raw_price']['OfferedPriceRoundedOff'];

	$SmokingPreference = get_smoking_preference(@$room_details['SmokingPreference']);
	$dynamic_params_url['SmokingPreference']		= $SmokingPreference['code'];

	$dynamic_params_url['ServiceTax']				= $room_details['API_raw_price']['ServiceTax'];
	$dynamic_params_url['Tax']						= (@$room_details['API_raw_price']['Tax']);
	$dynamic_params_url['ExtraGuestCharge']			= (@$room_details['API_raw_price']['ExtraGuestCharge']);
	$dynamic_params_url['ChildCharge']				= (@$room_details['API_raw_price']['ChildCharge']);
	$dynamic_params_url['OtherCharges']				= (@$room_details['API_raw_price']['OtherCharges']);
	$dynamic_params_url['Discount']					= (@$room_details['API_raw_price']['Discount']);
	$dynamic_params_url['AgentCommission']			= (@$room_details['API_raw_price']['AgentCommission']);
	$dynamic_params_url['AgentMarkUp']				= (@$room_details['API_raw_price']['AgentMarkUp']);
	$dynamic_params_url['TDS']						= (@$room_details['API_raw_price']['TDS']);
	
	$dynamic_params_url['AccessKey']				= (@$room_details['AccessKey']);//Travelomatix Access Key
	return $dynamic_params_url;
}

/**
 * combine and return single data
 * @param array $HotelRoomsDetails
 */
function tbo_summary_room_combination($HotelRoomsDetails)
{
	$summary_combination_list = array('RoomPrice', 'PublishedPrice', 'PublishedPriceRoundedOff', 'OfferedPrice', 'OfferedPriceRoundedOff', 'ServiceTax', 'Tax',
	'ExtraGuestCharge', 'ChildCharge', 'OtherCharges', 'TDS');
	$summary = array();
	foreach ($summary_combination_list as $k => $v) {
		$summary[$v] = 0;
	}
	foreach ($HotelRoomsDetails as $__h_key => $__h_val) {
		$summary['RoomPrice']					+= $__h_val['Price']['RoomPrice'];
		$summary['PublishedPrice']				+= $__h_val['Price']['PublishedPrice'];
		$summary['PublishedPriceRoundedOff']	+= $__h_val['Price']['PublishedPriceRoundedOff'];
		$summary['OfferedPrice']				+= $__h_val['Price']['OfferedPrice'];
		$summary['OfferedPriceRoundedOff']		+= $__h_val['Price']['OfferedPriceRoundedOff'];
		$summary['ServiceTax']					+= $__h_val['Price']['ServiceTax'];
		$summary['Tax']							+= $__h_val['Price']['Tax'];
		$summary['ExtraGuestCharge']			+= $__h_val['Price']['ExtraGuestCharge'];
		$summary['ChildCharge']					+= $__h_val['Price']['ChildCharge'];
		$summary['OtherCharges']				+= $__h_val['Price']['OtherCharges'];
		$summary['TDS']							+= $__h_val['Price']['TDS'];
	}
	return $summary;
}

/**
 * combine and return single data
 * @param array $HotelRoomsDetails
 */
function viator_summary_trip_combination($TripPriceDetails)
{
	$summary_combination_list = array('TotalDisplayFare','NetFare');
	$summary = '';
	foreach ($summary_combination_list as $k => $v) {
		$summary[$v] = 0;
	}
	$summary['TotalDisplayFare'] = $TripPriceDetails['TotalDisplayFare'];
	$summary['NetFare'] = $TripPriceDetails['NetFare'];
	

	return $summary;
}

function print_star_rating($star_rating=0)
{
	$inverse_star_key = array(0 => 0, 1 => 5, 2 => 4, 3 => 3, 4 => 2, 5 => 1);
	$max_star_rate = 5;
	$min_star_rate = 0;
	$rating = '';	
	$current_rate = $inverse_star_key[intval($star_rating)];
	for ($min_star_rate = 1; $min_star_rate <= $max_star_rate; $min_star_rate++) {
		$active_star_rating = (($current_rate == $min_star_rate) == true ? 'active' : '');
		$rating .= '<span class="star '.$min_star_rate.' '.$active_star_rating.'"></span>';
	}
	return $rating;
}
function get_search_panel()
{
	$hotel_name_filter = '
	<div class="panel panel-primary contrast10 clearfix">
		<div class="panel-heading"><i class="far fa-filter"></i> <span class="bg-primary">Filter By Hotel Name</span></div>
		<div class="panel-body">
			<input type="text" class="form-control" id="hotel-name" name="hotel_name" placeholder="Hotel Name">
			<button class="btn btn-sm btn-success" id="hotel-name-search-btn">Search</button>
		</div>
	</div>';

	$price_filter = '
	<div class="panel panel-primary contrast10 clearfix">
		<div class="panel-heading"><i class="far fa-money"></i> <span class="bg-primary">Filter By Price</span></div>
		<div class="panel-body">
			<p>
			<label for="amount">Price range:</label>
			<input type="text" id="price" readonly style="border:0;">
			</p>
			<div id="price-range"></div>
		</div>
	</div>';
	$rating_filter = '
	<div class="panel panel-primary contrast10 clearfix">
		<div class="panel-heading"><i class="far fa-star"></i> <span class="bg-primary">Filter By Rating</span></div>
		<div class="panel-body">
			<ul class="">
			<li class=""><label><input type="checkbox" class="star-filter" value="0" checked="checked"> 0 <i class="far fa-star"></i></label></li>
			<li class=""><label><input type="checkbox" class="star-filter" value="1" checked="checked"> 1 <i class="far fa-star"></i></label></li>
			<li class=""><label><input type="checkbox" class="star-filter" value="2" checked="checked"> 2 <i class="far fa-star"></i></label></li>
			<li class=""><label><input type="checkbox" class="star-filter" value="3" checked="checked"> 3 <i class="far fa-star"></i></label></li>
			<li class=""><label><input type="checkbox" class="star-filter" value="4" checked="checked"> 4 <i class="far fa-star"></i></label></li>
			<li class=""><label><input type="checkbox" class="star-filter" value="5" checked="checked"> 5 <i class="far fa-star"></i></label></li>
			</ul>
		</div>
	</div>';
	return $hotel_name_filter.$price_filter.$rating_filter;
}
