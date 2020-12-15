<?php
//----------------------------------------------------------------------------------------------------------------------------------
/**
 * get bus booking voucher
 * @param string $app_reference
 * @param string $booking_source
 * @param string $status
 */
function bus_voucher($app_reference, $booking_source='', $status='')
{
	return '<a href="'.bus_voucher_url($app_reference, $booking_source, $status).'/show_voucher" target="_blank" class="btn btn-sm btn-primary flight_v"><i class="fa fa-file-o"></i> Ticket</a>';
}

function bus_voucher_url($app_reference, $booking_source='', $status='')
{
	return base_url().'voucher/bus/'.$app_reference.'/'.$booking_source.'/'.$status;
}


function hotel_voucher($app_reference, $booking_source='', $status='')
{
	return '<a href="'.hotel_voucher_url($app_reference, $booking_source, $status).'/show_voucher" target="_blank" class="btn btn-sm btn-primary flight_v"><i class="fa fa-file-o"></i> Voucher</a>';
}

function hotel_voucher_beta($app_reference, $booking_source='', $status='')
{
	return '<a href="'.hotel_voucher_url_beta($app_reference, $booking_source, $status).'/show_voucher" target="_blank" class="btn btn-sm btn-primary flight_v"><i class="fa fa-file-o"></i> Voucher Beta</a>';
}
function hotel_voucher_url_beta($app_reference, $booking_source='', $status='')
{
	return base_url().'voucher/hotel/'.$app_reference.'/'.$booking_source.'/'.$status.'/new_voucher';
}


function hotel_voucher_url($app_reference, $booking_source='', $status='')
{
	return base_url().'voucher/hotel/'.$app_reference.'/'.$booking_source.'/'.$status;
}

function flight_voucher($app_reference, $booking_source='', $status='')
{
	return '<a href="'.flight_voucher_url($app_reference, $booking_source, $status).'/show_voucher" target="_blank" class="btn btn-sm btn-primary flight_v"><i class="fa fa-file-o"></i> Voucher</a>';
}
function flight_voucher_url($app_reference, $booking_source='', $status='')
{
	return base_url().'voucher/flight/'.$app_reference.'/'.$booking_source.'/'.$status;
}


/*Sightseeing Voucher*/
function sightseen_voucher($app_reference, $booking_source='', $status='')
{
	return '<a href="'.sightseen_voucher_url($app_reference, $booking_source, $status).'/show_voucher" target="_blank" class="btn btn-sm btn-primary flight_v"><i class="fa fa-file-o"></i> Voucher</a>';
}

function sightseen_voucher_url($app_reference, $booking_source='', $status='')
{
	return base_url().'voucher/sightseen/'.$app_reference.'/'.$booking_source.'/'.$status;
}
/*Transfer Voucher*/

function transferv1_voucher($app_reference, $booking_source='', $status='')
{
	return '<a href="'.transferv1_voucher_url($app_reference, $booking_source, $status).'/show_voucher" target="_blank" class="btn btn-sm btn-primary flight_v"><i class="fa fa-file-o"></i> Voucher</a>';
}

function transferv1_voucher_url($app_reference, $booking_source='', $status='')
{
	return base_url().'voucher/transferv1/'.$app_reference.'/'.$booking_source.'/'.$status;
}
/******Transfer End*****/

/**
 * INVOICE For Flight Booing
 */
function flight_invoice_url($app_reference, $booking_source='', $status='')
{
	return base_url().'voucher/flight_invoice/'.$app_reference.'/'.$booking_source.'/'.$status;
}
function flight_invoice($app_reference, $booking_source='', $status='')
{
	return '<a href="'.flight_invoice_url($app_reference, $booking_source, $status).'/show_voucher" target="_blank" class="btn btn-sm btn-primary"><i class="fa fa-file-o"></i> Invoice</a>';
}
//----------------------------------------------------------------------------------------------------------------------------------
/**
 * Convert numeric index to room key indexed array
 * @param array $HotelRoomsDetails
 */
function get_room_index_list($HotelRoomsDetails)
{
	$tmp_room_details = '';
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
 * custom function needed as its required in multiple places
 * @param number $room_index
 * @param array  $room_details
 * @param string $application_default_currency
 */
function get_dynamic_booking_parameters($room_index, $room_details, $application_default_currency)
{
	$dynamic_params_url = '';
	$dynamic_params_url['RoomIndex']				= $room_index;
	$dynamic_params_url['RatePlanCode']				= $room_details['RatePlanCode'];
	$dynamic_params_url['RatePlanName']				= $room_details['RatePlanName'];
	$dynamic_params_url['RoomTypeCode']				= $room_details['RoomTypeCode'];
	$dynamic_params_url['RoomTypeName']				= $room_details['RoomTypeName'];
	$dynamic_params_url['CurrencyCode']				= $application_default_currency;

	$dynamic_params_url['RoomPrice']				= $room_details['Price']['RoomPrice'];
	$dynamic_params_url['PublishedPrice']			= $room_details['Price']['PublishedPrice'];
	$dynamic_params_url['PublishedPriceRoundedOff']	= $room_details['Price']['PublishedPriceRoundedOff'];
	$dynamic_params_url['OfferedPrice']				= $room_details['Price']['OfferedPrice'];
	$dynamic_params_url['OfferedPriceRoundedOff']	= $room_details['Price']['OfferedPriceRoundedOff'];

	$SmokingPreference = get_smoking_preference(@$room_details['SmokingPreference']);
	$dynamic_params_url['SmokingPreference']		= $SmokingPreference['code'];

	$dynamic_params_url['ServiceTax']				= $room_details['Price']['ServiceTax'];
	$dynamic_params_url['Tax']						= $room_details['Price']['Tax'];
	$dynamic_params_url['ExtraGuestCharge']			= $room_details['Price']['ExtraGuestCharge'];
	$dynamic_params_url['ChildCharge']				= $room_details['Price']['ChildCharge'];
	$dynamic_params_url['OtherCharges']				= $room_details['Price']['OtherCharges'];
	$dynamic_params_url['Discount']					= $room_details['Price']['Discount'];
	$dynamic_params_url['AgentCommission']			= $room_details['Price']['AgentCommission'];
	$dynamic_params_url['AgentMarkUp']				= $room_details['Price']['AgentMarkUp'];
	$dynamic_params_url['TDS']						= $room_details['Price']['TDS'];
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
	$summary = '';
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
		<div class="panel-heading"><i class="fa fa-filter"></i> <span class="bg-primary">Filter By Hotel Name</span></div>
		<div class="panel-body">
			<input type="text" class="form-control" id="hotel-name" name="hotel_name" placeholder="Hotel Name">
			<button class="btn btn-sm btn-success" id="hotel-name-search-btn">Search</button>
		</div>
	</div>';

	$price_filter = '
	<div class="panel panel-primary contrast10 clearfix">
		<div class="panel-heading"><i class="fa fa-money"></i> <span class="bg-primary">Filter By Price</span></div>
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
		<div class="panel-heading"><i class="fa fa-star"></i> <span class="bg-primary">Filter By Rating</span></div>
		<div class="panel-body">
			<ul class="">
			<li class=""><label><input type="checkbox" class="star-filter" value="0" checked="checked"> 0 <i class="fa fa-star"></i></label></li>
			<li class=""><label><input type="checkbox" class="star-filter" value="1" checked="checked"> 1 <i class="fa fa-star"></i></label></li>
			<li class=""><label><input type="checkbox" class="star-filter" value="2" checked="checked"> 2 <i class="fa fa-star"></i></label></li>
			<li class=""><label><input type="checkbox" class="star-filter" value="3" checked="checked"> 3 <i class="fa fa-star"></i></label></li>
			<li class=""><label><input type="checkbox" class="star-filter" value="4" checked="checked"> 4 <i class="fa fa-star"></i></label></li>
			<li class=""><label><input type="checkbox" class="star-filter" value="5" checked="checked"> 5 <i class="fa fa-star"></i></label></li>
			</ul>
		</div>
	</div>';
	return $hotel_name_filter.$price_filter.$rating_filter;
}