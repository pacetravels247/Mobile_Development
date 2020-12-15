<?php
/*debug($raw_room_list);
exit;*/
$booking_url = $GLOBALS['CI']->hotel_lib->booking_url($params['search_id']);
/**
 * Generate all the possible combinations among a set of nested arrays.
 *
 * @param array $data  The entrypoint array container.
 * @param array $all   The final container (used internally).
 * @param array $group The sub container (used internally).
 * @param mixed $val   The value to append (used internally).
 * @param int   $i     The key index (used internally).
 */
function generate_combinations(array $data, array &$all = array(), array $group = array(), $value = null, $i = 0)
{
	$keys = array_keys($data);
	if (isset($value) === true) {
		array_push($group, $value);
	}

	if ($i >= count($data)) {
		array_push($all, $group);
	} else {
		$currentKey     = $keys[$i];
		$currentElement = $data[$currentKey];
		foreach ($currentElement as $val) {
			generate_combinations($data, $all, $group, $val, $i + 1);
		}
	}

	return $all;
}

$clean_room_list			 = '';//HOLD DATA TO BE RETURNED
$_HotelRoomsDetails			 = get_room_index_list($raw_room_list['GetHotelRoomResult']['HotelRoomsDetails']);
$_RoomCombinations			 = $raw_room_list['GetHotelRoomResult']['RoomCombinations'];
$_TraceId					 = $raw_room_list['GetHotelRoomResult']['TraceId'];
$_IsUnderCancellationAllowed = $raw_room_list['GetHotelRoomResult']['IsUnderCancellationAllowed'];
$_InfoSource				 = $raw_room_list['GetHotelRoomResult']['RoomCombinations']['InfoSource'];

$common_params_url = '';
$common_params_url .= '<input type="hidden" name="HotelCode"		value="'.$params['HotelCode'].'">';//This URL has to be carried forward
$common_params_url .= '<input type="hidden" name="ResultIndex"		value="'.$params['ResultIndex'].'">';
$common_params_url .= '<input type="hidden" name="booking_source"	value="'.$params['booking_source'].'">';
$common_params_url .= '<input type="hidden" name="search_id"		value="'.$params['search_id'].'">';
$common_params_url .= '<input type="hidden" name="TraceId"			value="'.$_TraceId.'">';
$common_params_url .= '<input type="hidden" name="op"				value="block_room">';
$common_params_url .= '<input type="hidden" name="GuestNationality"	value="'.ISO_INDIA.'" >';
$common_params_url .= '<input type="hidden" name="HotelName"		value="" >';
$common_params_url .= '<input type="hidden" name="StarRating"		value="">';




/**
 * Forcing room combination to appear in multiple list format
 */
if (isset($_RoomCombinations['RoomCombination'][0]) == false) {
	$_RoomCombinations['RoomCombination'][0] = $_RoomCombinations['RoomCombination'];
}


//print_r($_RoomCombinations['RoomCombination']);echo "<br>test";
/**
 * FIXME
 * Room Details
 * Currently we are supporting Room Of - FixedCombination
 */
$generate_rm_cm = array();
if ($_InfoSource != 'FixedCombination') {


	//print_r($_RoomCombinations['RoomCombination']);
	foreach ($_RoomCombinations['RoomCombination'] as $key => $value) {
		$rm_com = array();
		/*echo "key "; print_r($key);
		echo "<br>value "; print_r($value['RoomIndex']);*/
		$rm_com = $value['RoomIndex'];
		$generate_rm_cm[] = $rm_com;
	}

	$_RoomComb = generate_combinations($generate_rm_cm);
	//echo "<br><pre>";print_r($_RoomComb);
	$RoomComb_fin = array();
	foreach ($_RoomComb as $key => $value) {
		$RoomComb_fin[$key]['RoomIndex']=$value;
		 /*echo"<br>key"; print_r($key);
		 echo "<bR> VALUE";
		 print_r($value);*/
		}
		$_RoomCombinations['RoomCombination'] = $RoomComb_fin;
	//echo "<pre>";	print_r($RoomComb_fin);
	/*	debug($_RoomCombinations);
	exit;*/
}

/**
 * Forcing room combination to appear in multiple list format
 */
if (isset($_RoomCombinations['RoomCombination'][0]) == false) {
	$_RoomCombinations['RoomCombination'][0] = $_RoomCombinations['RoomCombination'];
}

/**
 * Forcing Room list to appear in multiple list format
 */
if (isset($_HotelRoomsDetails[0]) == false) {
	$_HotelRoomsDetails[0] = $_HotelRoomsDetails;
}

//---------------------------------------------------------------------------Print Combination - START
foreach ($_RoomCombinations['RoomCombination'] as $__rc_key => $__rc_value) {
	/**
	 * Forcing Combination to appear in multiple format
	 */
	if (valid_array($__rc_value['RoomIndex']) == false) {
		$current_combination_wrapper = array($__rc_value['RoomIndex']);
	} else {
		$current_combination_wrapper = $__rc_value['RoomIndex'];
	}

	$temp_current_combination_count = count($current_combination_wrapper);
	$clean_room_list .= '<div class="panel panel-default">';
	$room_panel_details = $room_panel_summary = $dynamic_params_url = '';//SUPPORT DETAILS
	foreach ($current_combination_wrapper as $__room_index_key => $__room_index_value) {
		//NOTE : PRINT ROOM DETAILS OF EACH ROOM INDEX VALUE
		$temp_room_details = $_HotelRoomsDetails[$__room_index_value];

		$SmokingPreference = get_smoking_preference(@$temp_room_details['SmokingPreference']);

		$room_panel_details .= '<div class="col-md-4">';
		$room_panel_details .= '<h4><span class="room-name">'.$temp_room_details['RoomTypeName'].'</span></h4>';
		$room_panel_details .= '<p><span class="smoking-preference">Smoking Preference : '.$SmokingPreference['label'].'</span></p>';
		$room_panel_details .= '</div>';
		$room_panel_details .= '<div class="col-md-6">';
		if (isset($temp_room_details['Amenities'])) {
			$room_panel_details .= '<p><ul>';
			foreach ($temp_room_details['Amenities'] as $__amenity) {
				$room_panel_details .= '<li>'.$__amenity.'</li>';
			}
			$room_panel_details .= '</ul></p>';
		}
		$room_panel_details .= '<p>Last Cancellation Date : '.local_date($temp_room_details['LastCancellationDate']).'</p>';
		$room_panel_details .= '<p><a href="#"class="" data-toggle="tooltip" data-placement="top" title="'.$temp_room_details['CancellationPolicy'].'">Cancellation Policy</a></p>';
		$room_panel_details .= '</div>';

		$search_id = $attr['search_id'];
		$rslt_temp_room_details = $temp_room_details;
		$temp_price_details = $GLOBALS['CI']->hotel_lib->update_room_markup_currency($temp_room_details['Price'], $currency_obj, $search_id);
		$PublishedPrice				= $temp_price_details['PublishedPrice'];
		$PublishedPriceRoundedOff	= $temp_price_details['PublishedPriceRoundedOff'];
		$OfferedPrice				= $temp_price_details['OfferedPrice'];
		$OfferedPriceRoundedOff		= $temp_price_details['OfferedPriceRoundedOff'];
		$RoomPrice					= $temp_price_details['RoomPrice'];

		$room_panel_details .= '<div class="col-md-2">';
		$room_panel_details .= '<p><h4>'.$currency_obj->get_currency_symbol($currency_obj->to_currency).' <span class="h3 text-i h-p">'.$RoomPrice.'</span></h4></p>';
		$room_panel_details .= '</div>';


		if (intval($__room_index_key) == 0) {
			$temp_book_now_button = '';
			$temp_summary_room_list = array($rslt_temp_room_details['RoomTypeName']);
			$temp_summary_price_list = array($RoomPrice);
		} else {
			$temp_summary_room_list[] = $rslt_temp_room_details['RoomTypeName'];
			$temp_summary_price_list[] = $RoomPrice;
		}

		if (intval($temp_current_combination_count) == intval($__room_index_key+1)) {
			//PIN Summary
			if (valid_array($temp_summary_room_list)) {
				$temp_summary_room_list = implode(' <i class="fa fa-plus"></i> ', $temp_summary_room_list);
			}
			if (valid_array($temp_summary_price_list)) {
				$temp_summary_price_list = array_sum($temp_summary_price_list);
			}
			$room_panel_summary = '<p><span class="room-name">'.$temp_summary_room_list.'</span> : '.$currency_obj->get_currency_symbol($application_preferred_currency).' <span class="h3 text-i room-price">'.$temp_summary_price_list.'</span> <h3 class="mrdtls_drp  hand-cursor">More Details <i class="fa fa-info"></i></i></h3></p>';
		}

		$dynamic_params_url[] = get_dynamic_booking_parameters($__room_index_value, $rslt_temp_room_details, $application_default_currency);
	}//END INDIVIDUAL COMBINATION LOOPING
	$dynamic_params_url = serialized_data($dynamic_params_url);
	$temp_dynamic_params_url = '';
	$temp_dynamic_params_url .= '<input type="hidden" name="token" value="'.$dynamic_params_url.'">';
	$temp_dynamic_params_url .= '<input type="hidden" name="token_key" value="'.md5($dynamic_params_url).'">';
	$temp_book_link = '<form method="POST" action="'.$booking_url.'" class="book-now-form">
	'.$common_params_url.$temp_dynamic_params_url.'
	<button class="btn btn-p btn-xs book-now-btn" type="submit">Book Now</button>
</form>';

$clean_room_list .= '<div class="panel-heading">';
$clean_room_list .= '<div class="panel-title">'.$room_panel_summary.$temp_book_link.'</div>';
$clean_room_list .= '</div>';
$clean_room_list .= '<div class="panel-body dtls_body" style="display:none;">';
$clean_room_list .= '<div class="row">';
$clean_room_list .= $room_panel_details;
$clean_room_list .= '</div>';
$clean_room_list .= '</div>';
$clean_room_list .= '</div>';
}//END COMBINATION LOOPING

echo $clean_room_list;
//---------------------------------------------------------------------------Print Combination - END
?>
<script>
	$(function () {
		$('[data-toggle="tooltip"]').tooltip();
	});

	$(".mrdtls_drp").click(function(){

		$(".dtls_body", $(this).closest('.panel')).toggle();
	});

</script>
<?php
//---------------------------------------------------------------------------Support Functions - START
//---------------------------------------------------------------------------Support Functions - END