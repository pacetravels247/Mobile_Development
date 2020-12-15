<?php
//debug($data);exit;
$booking_details = $data ['booking_details'] [0];
$booking_source = $booking_details['booking_source'];
// debug($booking_source);exit;
$itinerary_details = $booking_details ['booking_itinerary_details'];
// debug($itinerary_details);exit;
$attributes = $booking_details ['attributes'];
$customer_details = $booking_details ['booking_transaction_details'] [0] ['booking_customer_details'];
$domain_details = $booking_details;
$lead_pax_details = $customer_details;
$booking_transaction_details = $booking_details ['booking_transaction_details'];
$price_attr = json_decode($booking_details["price_attr"], true);
$adult_count = 0;
$infant_count = 0;

//Multi Voucher Parameters - 1 #Starts
$infant_voucher = 0;  $cust_origin = 0; $with_fare = 1; $cust_name = 0; $cust_contact_td_width = "50%";
$get_data = $this->input->get();
if(isset($get_data["cust_origin"]))
	$cust_origin = $get_data["cust_origin"];
if(isset($get_data["cust_origin"]))
	$with_fare = $get_data["with_fare"];
if($with_fare == 0)
{
	$cust_contact_td_width = "100%";
}
//Multi Voucher Parameters - 1 #Ends
$price_data = array();
foreach ($customer_details as $k => $v) {
    if (strtolower($v ['passenger_type']) == 'infant') {
        $infant_count ++;
    } else {
        $adult_count ++;
    }
	if(strtolower($v['passenger_type']) == 'infant' && $v['origin'] == $cust_origin)
	{
		$infant_voucher = 1;
		$price_data = $price_attr["INF"];
	}
	if(strtolower($v['passenger_type']) == 'adult' && $v['origin'] == $cust_origin)
	{
		$adult_voucher = 1;
		$price_data = $price_attr["ADT"];
	}
	if(strtolower($v['passenger_type']) == 'child' && $v['origin'] == $cust_origin)
	{
		$child_voucher = 1;
		$price_data = $price_attr["CHD"];
	}
}
$total_pass_count = $adult_count+$infant_count;

$Onward = '';
$return = '';
if (count($booking_transaction_details) == 2) {
    $Onward = 'Onward ';
    $Return = 'Return ';
}
$segment_details = array();
foreach ($itinerary_details as $k => $sub_details) {
    $segment_details [$sub_details['airline_code']][] = $sub_details;
    
}
$segment_details = array_values($segment_details);
$onward_segment_details = $segment_details[0];
$return_segment_details = $segment_details[1];
//echo "<pre>booking_transaction_details"; print_r($booking_transaction_details);die;
// generate onword and return
if ($booking_details ['is_domestic'] == true && count($booking_transaction_details) == 2) {
    $onward_segment_details = array();
    $return_segment_details = array();
    $segment_indicator_arr = array();
    $segment_indicator_sort = array();
	
	if($booking_details["booking_billing_type"] != "offline"){
    foreach ($itinerary_details as $key => $key_sort_data) {
        $segment_indicator_sort [$key] = $key_sort_data ['origin'];
    }
    array_multisort($segment_indicator_sort, SORT_ASC, $itinerary_details);

    foreach ($itinerary_details as $k => $sub_details) {
        $segment_indicator_arr [] = $sub_details ['segment_indicator'];
        $count_value = array_count_values($segment_indicator_arr);

        if (isset($count_value [1]) && $count_value [1] == 1) {
            $onward_segment_details [] = $sub_details;
        } else {
            $return_segment_details [] = $sub_details;
        }
    }
	}
	else{
	//New Format Snippet - Start
	$to_loc = $booking_details["to_loc"];
	$return_start = 0;
	foreach ($itinerary_details as $k => $sub_details) {
		if (!$return_start) {
			$onward_segment_details [] = $sub_details;
		} else {
			$return_segment_details [] = $sub_details;
		}
		if(trim($sub_details["to_airport_code"]) == trim($to_loc))
		{
			$return_start = 1;
		}
	}
	//New Format Snippet - End
	}
}

// debug($booking_transaction_details);exit;
if (!empty($itinerary_details[0]['airline_pnr'])) {
   
    $airline_pnr = $onward_segment_details[0]['airline_pnr'];
    $gds_pnr = $booking_transaction_details[0]['pnr'];
} else if (!empty($booking_transaction_details[0]['pnr'])) {
    $airline_pnr = $booking_transaction_details[0]['pnr'];
    $gds_pnr = $booking_transaction_details[0]['gds_pnr'];
} else {
    $airline_pnr = $booking_transaction_details[0]['book_id'];
    $gds_pnr = $booking_transaction_details[0]['book_id'];
}
if (isset($return_segment_details)) {
    // debug($booking_transaction_details);exit;
    
    if (isset($return_segment_details[0]['airline_pnr']) && !empty($return_segment_details[0]['airline_pnr'])) {
        $return_airline_pnr = $return_segment_details[0]['airline_pnr'];
        $return_gds_pnr = $booking_transaction_details[0]['pnr'];
    } elseif (!empty($booking_transaction_details[0]['pnr'])) {
        $return_airline_pnr = $booking_transaction_details[0]['pnr'];
        $return_gds_pnr = $booking_transaction_details[0]['pnr'];
    } else {
        $return_airline_pnr = $booking_transaction_details[0]['book_id'];
        $return_gds_pnr = $booking_transaction_details[0]['book_id'];
    }
    $retur_fare_details = json_decode($booking_transaction_details[0]['attributes'], True);
}

$fare_details = json_decode($booking_transaction_details[0]['attributes'], True);

$BaseFare = $fare_details['Fare']['BaseFare'] + @$retur_fare_details['Fare']['BaseFare'];
$Tax = $fare_details['Fare']['Tax'] + @$retur_fare_details['Fare']['Tax'];
$GST = $booking_transaction_details[0]['gst'] + @$booking_transaction_details[0]['gst'];
$booking_transaction_details_value = $booking_transaction_details [0];
$baggage_price = 0;
$meal_price = 0;
$seat_price = 0;
// debug($booking_transaction_details_value);exit;
if (isset($booking_transaction_details_value['extra_service_details']['baggage_details']) == true && valid_array($booking_transaction_details_value['extra_service_details']['baggage_details']) == true) {
    $baggage_details = $booking_transaction_details_value['extra_service_details']['baggage_details'];
    foreach ($baggage_details['details'] as $bag_k => $bag_v) {
        foreach ($bag_v as $bd_k => $bd_v) {
            $baggage_price += $bd_v['price'];
        }
    }
}
if (isset($booking_transaction_details_value['extra_service_details']['meal_details']) == true && valid_array($booking_transaction_details_value['extra_service_details']['meal_details']) == true) {
    // debug($booking_transaction_details_value);exit;
    $meal_details = $booking_transaction_details_value['extra_service_details']['meal_details'];
    foreach ($meal_details['details'] as $meal_k => $meal_v) {
        foreach ($meal_v as $md_k => $md_v) {
            $meal_price += $md_v['price'];
        }
    }
}
if (isset($booking_transaction_details_value['extra_service_details']['seat_details']) == true && valid_array($booking_transaction_details_value['extra_service_details']['seat_details']) == true) {
    $seat_details = $booking_transaction_details_value['extra_service_details']['seat_details'];
    foreach ($seat_details['details'] as $seat_k => $seat_v) {
        foreach ($seat_v as $sd_k => $sd_v) {
            // debug($seat_v);exit;
            $seat_price += $sd_v['price'];
        }
    }
}
if ($booking_details ['is_domestic'] == true && count($booking_transaction_details) == 2) {
    $booking_transaction_details_value = $booking_transaction_details [1];
    if (isset($booking_transaction_details_value['extra_service_details']['baggage_details']) == true && valid_array($booking_transaction_details_value['extra_service_details']['baggage_details']) == true) {
        $baggage_details = $booking_transaction_details_value['extra_service_details']['baggage_details'];
        foreach ($baggage_details['details'] as $bag_k => $bag_v) {
            foreach ($bag_v as $bd_k => $bd_v) {
                $baggage_price += $bd_v['price'];
            }
        }
    }
    if (isset($booking_transaction_details_value['extra_service_details']['meal_details']) == true && valid_array($booking_transaction_details_value['extra_service_details']['meal_details']) == true) {
        // debug($booking_transaction_details_value);exit;
        $meal_details = $booking_transaction_details_value['extra_service_details']['meal_details'];
        foreach ($meal_details['details'] as $meal_k => $meal_v) {
            foreach ($meal_v as $md_k => $md_v) {
                $meal_price += $md_v['price'];
            }
        }
    }
    if (isset($booking_transaction_details_value['extra_service_details']['seat_details']) == true && valid_array($booking_transaction_details_value['extra_service_details']['seat_details']) == true) {
        $seat_details = $booking_transaction_details_value['extra_service_details']['seat_details'];
        foreach ($seat_details['details'] as $seat_k => $seat_v) {
            foreach ($seat_v as $sd_k => $sd_v) {
                // debug($seat_v);exit;
                $seat_price += $sd_v['price'];
            }
        }
    }
}

//Multi Voucher Parameters - 1 #Starts
if($cust_origin != 0)
{
	$multi_voucher = 1;
	$BaseFare = $price_data["BaseFare"];
	$Tax = $price_data["Tax"];
	$GST = $GST/$total_pass_count;
	$baggage_price = $baggage_price/$adult_count;
	$meal_price = $meal_price/$adult_count;
	$seat_price = $seat_price/$adult_count;
	$booking_details['admin_markup'] = $booking_details['admin_markup']/$total_pass_count;
	$booking_details['agent_markup'] = $booking_details['agent_markup']/$total_pass_count;
	$booking_details['convinence_amount'] = $booking_details['convinence_amount']/$total_pass_count;
	$booking_details['grand_total'] = $BaseFare+$Tax+$GST+$booking_details['admin_markup']+$booking_details['agent_markup']+$booking_details['convinence_amount'];
}
//Multi Voucher Parameters - 1 #End

$airline_contact_no = $this->custom_db->single_table_records('airline_contact_numbers', '*', array('airline_code' => $itinerary_details[0]['airline_code']));
// debug($airline_contact_no);exit;
if (isset($airline_contact_no['data'][0])) {
    $airline_number = '<span><img style="vertical-align:middle" src=' . SYSTEM_IMAGE_DIR . 'phone.png /><span style="font-size:16px;color:#00a9d6;vertical-align:middle;font-weight: 600;"> &nbsp;' . $airline_contact_no['data'][0]['phone_number'] . '</span></span> ';
} else {
    $airline_number = '';
}
if ($booking_transaction_details[0] ['status'] == 'BOOKING_CONFIRMED') {
    $border = 'border-top:2px solid #808080;';
} else if ($booking_transaction_details[0] ['status'] == 'BOOKING_HOLD') {
    $border = '';
}
$trip_type = $booking_details['trip_type'];
// echo $meal_price;exit;
// debug($fare_details);exit;

$extra_pax_price = 0;
if(isset($booking_transaction_details[0]['extra_pax_fare'])){
    $extra_pax_price += $booking_transaction_details[0]['extra_pax_fare'];
}
//debug($extra_pax_price);die();
?>
<style type="text/css">
    td, th {padding: 5px;}
    @media print { 
        header, footer, #show_log { display: none; }
        .pag_brk { page-break-before: always; }
    }
</style>
<div style="background:#ccc; width:100%; position:relative" id="ticket_flight">
    <table cellpadding="0" border-collapse cellspacing="0" width="100%" style="font-size:12px; font-family: 'Open Sans', sans-serif; width:850px; margin:0px auto;background-color:#fff; padding:45px;border-collapse:separate; color: #000;">
        <tbody>
			<?php if(empty(trim($data['logo']))) { $data['logo']="no_logo.png"; } ?>
            <tr>
                <td colspan="1" style="padding-bottom:10px"><img alt="No Logo" style="width:150px;" src="<?= $GLOBALS['CI']->template->domain_images($data['logo']) ?>" /></td>
                <td colspan="3" style="padding-bottom:10px" align="right">
				<?php echo strtoupper($data['domainname']) ?><br>ContactNo :<?php echo $data['phone_code'] ?> <?php echo $data['phone'] ?><br><?php echo $data['address'] ?><br>
				<br><span>Booked on : <?= app_friendly_absolute_date(@$booking_details['booked_date']) ?></span></td>
            </tr>
            <tr>
				<td align="left" colspan="2" style="line-height:26px; border-top:1px solid #00a9d6; border-bottom:1px solid #00a9d6;"> <span>Booking ID : <?= @$booking_details['app_reference'] ?></span></td>
                <td align="right" colspan="2" style="line-height:26px; border-top:1px solid #00a9d6; border-bottom:1px solid #00a9d6;"><span style="font-size:12px;">Status: </span><strong class="<?php echo booking_status_label($booking_transaction_details[0]['status']) ?>" style=" font-size:14px;">
<?php
switch (@$booking_transaction_details[0] ['status']) {
    case 'BOOKING_CONFIRMED' :
        echo 'CONFIRMED';
        break;
    case 'BOOKING_CANCELLED' :
        echo 'CANCELLED';
        break;
    case 'BOOKING_FAILED' :
        echo 'FAILED';
        break;
    case 'BOOKING_INPROGRESS' :
        echo 'INPROGRESS';
        break;
    case 'BOOKING_INCOMPLETE' :
        echo 'INCOMPLETE';
        break;
    case 'BOOKING_HOLD' :
        echo 'HOLD';
        break;
    case 'BOOKING_PENDING' :
        echo 'PENDING';
        break;
    case 'BOOKING_ERROR' :
        echo 'ERROR';
        break;
}
?></strong></td>
            </tr>
                        <?php if ($booking_details ['is_domestic'] == true && count($booking_transaction_details) == 2) { ?>
                <tr>
                    <td colspan="4" style="font-size: 18px;font-weight: 600;text-align: center;padding: 10px 0 0;">E-Ticket <?php echo $Onward; ?></td>
                </tr>
                        <?php } ?>
            <tr>
                <td><img style="width:60px;" src="<?= SYSTEM_IMAGE_DIR . 'airline_logo/' . $itinerary_details[0]['airline_code'] . '.gif' ?>" /></td>
                <td><span style="display: block;border-right: 1px solid #999;"><span style="font-size:14px;"><?= @$itinerary_details[0]['airline_name'] ?></span><br><?php echo $airline_number ?></span></td>
                <td><span style="font-size:14px;">Agency </span><span><img style="vertical-align:middle" src="<?= SYSTEM_IMAGE_DIR . 'phone.png' ?>" /> <span style="font-size:16px;color:#00a9d6;vertical-align:middle;font-weight: 600;"> &nbsp;<?php echo $data['phone'] ?></span></span></td>
<?php if ((!empty($gds_pnr)) || (!empty($airline_pnr))) { ?>
                    <td style="padding:10px 0;text-align: center;">
                        <span style="font-size:14px; border:2px solid #808080; display:block">
    <?php if ($booking_transaction_details[0] ['status'] == 'BOOKING_CONFIRMED' || $booking_transaction_details[0] ['status'] == 'BOOKING_HOLD') { ?>
                                <span style="color:#00a9d6;padding:5px; display:block">AIRLINE PNR</span>
                                <span style="font-size:26px;line-height:35px;padding-bottom: 5px;display:block;font-weight: 600;"><?= @$airline_pnr ?></span>
                    <?php } ?>
                    <?php if (($booking_transaction_details[0] ['status'] == 'BOOKING_CONFIRMED' || $booking_transaction_details[0] ['status'] == 'BOOKING_HOLD') && !empty($gds_pnr) && $booking_source!=PROVAB_FLIGHT_BOOKING_SOURCE) { ?>
                                <span style="<?php echo $border; ?>display:block; padding:5px;">GDS PNR:  <?= @$gds_pnr ?></span>
                            <?php } ?>
                        </span></td>
<?php } ?>
            </tr>
            <tr>
            <tr>
                <td colspan="4" style="border:1px solid #00a9d6; padding:0;">
                    <table cellspacing="0" cellpadding="5" width="100%" style="font-size:12px; padding:0;">
                        <tbody>
                            <tr>
                                <td colspan="2" style="background-color:#00a9d6; color:#fff"><img style="vertical-align:middle" src="<?= SYSTEM_IMAGE_DIR . 'flight.png' ?>"> &nbsp;<span style="vertical-align:middle;font-size:13px">Onward Flight Details</span></td>
                                <td align="right" colspan="2" style="background-color:#00a9d6; color:#fff"><span style="font-size:10px">*Please verify flight times with the airlines prior to departure</span></td>
                            </tr>

<?php
if (isset($booking_transaction_details) && $booking_transaction_details != "") {
    if ($booking_details ['is_domestic'] == true && count($booking_transaction_details) == 2) {
        $itinerary_details = array();
        $itinerary_details = $onward_segment_details;
    }
    // debug($itinerary_details);exit;
    $checkin_baggage = 0;
    $cabin_baggage = 0;
    $seg_count = count($itinerary_details);
    if ($seg_count == 1) {
        $non_stop = 'Non Stop';
    } else {
        $non_stop = '';
    }
	$non_stop = '';
    $seg_in_array = array();
    $seg_array = array();
    $seg_counts = 0;
    // debug($itinerary_details);exit;
    foreach ($onward_segment_details as $segment_details_k => $segment_details_v) {
        // echo $trip_type;
        $seg_array [] = $segment_details_v['segment_indicator'];
        // debug($seg_array);
        if (in_array($segment_details_v['segment_indicator'], $seg_in_array) && !empty($seg_in_array) && ($seg_counts == 0) && ($trip_type != 'multicity') && count($booking_transaction_details) != 2) {
            // echo $seg_count;
            $seg_counts = $seg_counts + 1;

            // debug($seg_in_array);
            ?>
                                        <tr>
                                            <td colspan="2" style="background-color:#00a9d6; color:#fff"><img style="vertical-align:middle" src="<?= SYSTEM_IMAGE_DIR . 'flight.png' ?>"> &nbsp;<span style="vertical-align:middle;font-size:13px">Return Flight Details</span></td>
                                            <td align="right" colspan="2" style="background-color:#00a9d6; color:#fff"><span style="font-size:10px">*Please verify flight times with the airlines prior to departure</span></td>
                                        </tr>

                                    <?php
                                    }
                                  //  $BaggageSegment_cabin = '';
                                   // $BaggageSegment_checkin = '';
                                    $itinerary_details_attributes = json_decode($segment_details_v ['attributes'], true);
                                    $airline_terminal_origin = @$itinerary_details_attributes['departure_terminal'];
                                    $airline_terminal_destination = @$itinerary_details_attributes['arrival_terminal'];
                                    $origin_terminal = '';
                                    $destination_terminal = '';
                                    // debug($itinerary_details_attributes);exit;
                                    if ($airline_terminal_origin != '') {
                                        $origin_terminal = 'Terminal ' . $airline_terminal_origin;
                                    }
                                    if ($airline_terminal_destination != '') {
                                        $destination_terminal = 'Terminal ' . $airline_terminal_destination;
                                    }


# Checking CheckIN Baggage

         if (is_int($segment_details_v['checkin_baggage'])) {
            $seg_wise_checkin_bgg = 'Checkin Baggage : Adult :' . (int) $segment_details_v['checkin_baggage'] . ' Kg & Child :'.(int) $segment_details_v['checkin_baggage'] .' Kg<br />';
            $BaggageSegment_checkin .= $seg_wise_checkin_bgg;
            $TotalBaggageCheckIN = (int) $segment_details_v['checkin_baggage'];
        } else {
            $seg_wise_checkin_bgg = 'Checkin Baggage : ' . $segment_details_v['checkin_baggage'].' | ';
            $BaggageSegment_checkin .= $seg_wise_checkin_bgg;
            $TotalBaggageCheckIN = $segment_details_v['checkin_baggage'];
        }

# Checking Cabin Baggage

        if (is_int($segment_details_v['cabin_baggage'])) {
            $seg_wise_cabin_bgg = 'Cabin Baggage : ' . (int) $segment_details_v['cabin_baggage'] . ' Kg';
            $BaggageSegment_cabin .=  $seg_wise_cabin_bgg;
            $TotalBaggageCabin = (int) $segment_details_v['cabin_baggage'];
        } else {
            $seg_wise_cabin_bgg = 'Cabin Baggage : ' . $segment_details_v['cabin_baggage'];
            $BaggageSegment_cabin .=  $seg_wise_cabin_bgg;
            $TotalBaggageCabin = $segment_details_v['cabin_baggage'];
        }



                                    /*
                                      $a_checkin_baggage= $segment_details_v['checkin_baggage'];
                                      $a_cabin_baggage= $segment_details_v['cabin_baggage'];
                                      if (strpos($a_checkin_baggage, 'PC') !== false) {
                                      $checkin_baggage=$a_checkin_baggage.' Per Adult';
                                      }
                                      else
                                      {
                                      $checkin_baggage += (int) $segment_details_v['checkin_baggage'].' Kg';  ;
                                      }
                                      if (strpos($a_cabin_baggage, 'PC') !== false) {
                                      $a_cabin_baggage=$a_cabin_baggage.' Per Adult';
                                      } else {
                                      $cabin_baggage += (int) $segment_details_v['cabin_baggage'].' Kg';
                                      } */





                                    if ($seg_count != 1 && $trip_type != 'multicity') {

                                        if (count(array_unique($seg_array)) == 1 && end($seg_array) == 1 && $seg_count == 2 && $trip_type != 'oneway') {

                                            $non_stop = 'Non Stop';
                                        } else {
                                            $non_stop = $segment_details_v['segment_indicator'] . ' Stop';
                                        }
                                    } else if ($seg_count != 1) {
                                        $non_stop = ($segment_details_k + 1) . ' Stop';
                                    }
									$non_stop = '';
                                    if ($trip_type == 'multicity') {
                                        $fight_count = ($segment_details_k + 1);
                                    } else {
                                        $fight_count = $segment_details_v['segment_indicator'];
                                    }
                                    $seg_in_array[] = $segment_details_v['segment_indicator'];
                                    if (valid_array($segment_details_v) == true) {
                                        ?>
                                        <tr>
                                            <td style="background-color:#d9d9d9; color:#555555"><span style="color:#006f8e">Flight <?php echo $fight_count; ?></span></td>
                                            <td style="background-color:#d9d9d9; color:#555555"><img style="vertical-align:middle" src="<?= SYSTEM_IMAGE_DIR . 'flight_up.png' ?>">&nbsp;<span style="vertical-align:middle">Departing</span></td>
                                            <td style="background-color:#d9d9d9; color:#555555"><img style="vertical-align:middle" src="<?= SYSTEM_IMAGE_DIR . 'flight_down.png' ?>">&nbsp;<span style="vertical-align:middle">Arriving</span></td>
                                            <td style="background-color:#d9d9d9; color:#555555">&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td><span><?= @$segment_details_v['airline_name'] ?><br><?php echo $segment_details_v['airline_code'] . ' - ' . $segment_details_v['flight_number']; ?><br>Cabin: <?php echo $booking_details['cabin_class']; ?></span></td>
                                            <td><span><strong><?= @$segment_details_v['from_airport_name'] ?>(<?= @$segment_details_v['from_airport_code'] ?>)</strong><br><?php echo date("D, d M Y", strtotime($segment_details_v['departure_datetime'])) . ", " . date("h:i A", strtotime($segment_details_v['departure_datetime'])); ?><br><?php echo $origin_terminal; ?></span></td>
                                            <td><span><strong><?= @$segment_details_v['to_airport_name'] ?>(<?= @$segment_details_v['to_airport_code'] ?>)</strong><br><?php echo date("D, d M Y", strtotime($segment_details_v['arrival_datetime'])) . ", " . date("h:i A", strtotime($segment_details_v['arrival_datetime'])); ?><br><?php echo $destination_terminal; ?></span></td>
                                            <td><span style="display:block;border-left: 1px solid #999; padding-left:10%"><?php echo $non_stop; ?><br><?php echo $segment_details_v['total_duration']; ?><br><?php echo $segment_details_v['is_refundable'] ?></span></td>
                                        </tr>
										<tr>
										<td>&nbsp;</td>
										<td colspan="3">
											<?=$seg_wise_checkin_bgg.$seg_wise_cabin_bgg?>
										</td>
									</tr>
            <?php
        }
    } 
}
?>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr><td style="line-height:15px;padding:0;">&nbsp;</td></tr>
            <tr>
                <td colspan="4" style="border:1px solid #666666; padding:0;">
                    <table cellspacing="0" cellpadding="5" width="100%" style="font-size:12px; padding:0;">
                        <tbody>
                            <tr>
                                <td colspan="4" style="background-color:#666666; color:#fff"><img style="vertical-align:middle" src="<?= SYSTEM_IMAGE_DIR . 'people_group.png' ?>"> &nbsp;<span style="vertical-align:middle;font-size:13px">Passenger(s) Details</span></td>
                            </tr>

                            <tr>
                                <td style="background-color:#d9d9d9; color:#555555"><span>Sr No.</span></td>
                                <td style="background-color:#d9d9d9; color:#555555"><span>Passenger(s) Name</span></td>
                                <td style="background-color:#d9d9d9; color:#555555"><span>Type</span></td>
                                <td style="background-color:#d9d9d9; color:#555555"><span>E-ticket No</span></td>
                            </tr>
<?php
$booking_transaction_details_value = $booking_transaction_details [0];
// debug($booking_transaction_details_value);exit;
if (isset($booking_transaction_details_value ['booking_customer_details'])) {
    foreach ($booking_transaction_details_value ['booking_customer_details'] as $cus_k => $cus_v) {
		if($cust_origin != 0 && $cus_v["origin"] != $cust_origin)
			continue;
		else
			$cust_name = $cus_v["first_name"].$cus_v["last_name"];
        if (strtolower($cus_v['passenger_type']) == 'infant') {
            $pass_name = $cus_v['title'] . '.' . $cus_v['first_name'] . '  ' . $cus_v['last_name'];
        } else {
            $pass_name = $cus_v['title'] . '.' . $cus_v['first_name'] . '  ' . $cus_v['last_name'];
        }
        ?>
                                    <tr>
                                        <td><span><?php echo ($cus_k + 1); ?></span></td>
                                        <td><span><strong><?php echo $pass_name; ?></strong</span></td>
                                        <td><span><?php echo ucfirst($cus_v['passenger_type']) ?></span></td>
                                        <?php 
                                        if(@$cus_v['TicketNumber'] != @$cus_v['TicketId']){
                                        ?> <td><span><?= @$cus_v['TicketNumber']; ?></span></td> <?php  
                                        }else{
                                        ?> <td style="padding-left: 5%;"><span>-</span></td> <?php    
                                        }
                                        ?> 
                                    </tr>

                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr><td style="line-height:15px;padding:0;">&nbsp;</td></tr>
            <tr>
                <td colspan="4" style="padding:0;">
                    <table cellspacing="0" cellpadding="5" width="100%" style="font-size:12px; padding:0;">
                        <tbody>
                            <tr>
								<td width="50%" style="padding:0;padding-right:14px; vertical-align:top">
									<table cellspacing="0" cellpadding="5" width="100%" style="border:1px solid #9a9a9a;font-size:12px; padding:0;">
										<tbody>
											<tr>
												<td colspan="4" style="border-bottom:1px solid #ccc"><span style="font-size:13px">Customer Contact Details</span></td>
											</tr>
										   
											<tr>
												<td colspan="4">
													Name : <?=$booking_details['lead_pax_name']?><br>
												   E-mail : <?= $booking_details['email'] ?> <br> Phone :<?= $booking_details['phone_code'] ?> <?= $booking_details['phone'] ?> <br>
												   GSTIN : <?= $booking_details['gst_number'] ?>
												</td>
											</tr>
											 <tr>
												<td colspan="4" style="border-top:1px solid #ccc"><span style="font-size:10px; color:#666; line-height:20px;">* Flight inclusions are subject to change with Airlines.</span></td>
											</tr>
										</tbody>
									</table>
								</td>
                                <?php if($with_fare == 1){ ?>
                                <td width="50%" style="padding:0;padding-right:0px;">
                                    <table cellspacing="0" cellpadding="5" width="100%" style="font-size:12px; padding:0;border:1px solid #9a9a9a;">
                                        <tbody>
                                            <tr>
                                                <td style="border-bottom:1px solid #ccc"><span style="font-size:13px">Payment Details</span></td>
                                                <td style="border-bottom:1px solid #ccc"><span style="font-size:11px">Amount ( <?php echo $booking_details['currency'] ?>
                                                        )</span></td>
                                            </tr>
                                            <tr>
                                                <td><span>Air Fare</span></td>
                                                <td><span> <?php echo number_format($BaseFare, 2) ?></span></td>
                                            </tr>
<?php if ($this->uri->segment(2) == 'b2c_flight_voucher'): ?>
                                                <tr>
                                                    <td><span>Taxes &amp; Fees</span></td>
                                                    <td><span> <?php echo number_format(($Tax + $booking_details['admin_markup'] + $booking_details['agent_markup'] + ($booking_details['convinence_amount'])) - $booking_details['admin_commission'] + $booking_details['admin_tds'], 2) ?></span></td>
                                                </tr>
<?php else: ?>
                                                <tr>
                                                    <td><span>Taxes &amp; Fees</span></td>
                                                    <td><span> <?php echo number_format(($Tax + $booking_details['admin_markup'] + $booking_details['agent_markup'] + ($booking_details['convinence_amount'])), 2) ?></span></td>
                                                </tr>
<?php endif; ?>
                                            <?php if ($GST > 0) { ?>
                                                <tr>
                                                    <td><span>GST</span></td>
                                                    <td><span> <?php echo number_format(($GST), 2) ?></span></td>
                                                </tr>
                                            <?php } ?>
                                            <?php if (isset($baggage_price) && $baggage_price != 0) { ?>
                                                <tr>
                                                    <td><span>Extra Baggage</span></td>
                                                    <td><span> <?php echo number_format($baggage_price, 2) ?></span></td>
                                                </tr>
                                            <?php } ?>
                                            <?php if (isset($meal_price) && $meal_price != 0) { ?>
                                                <tr>
                                                    <td><span>Meals</span></td>
                                                    <td><span> <?php echo number_format($meal_price, 2) ?></span></td>
                                                </tr>
                                            <?php } ?>
                                            <?php if (isset($seat_price) && $seat_price != 0) { ?>
                                                <tr>
                                                    <td><span>Seat</span></td>
                                                    <td><span> <?php echo number_format($seat_price, 2) ?></span></td>
                                                </tr>
                                            <?php } if ($booking_details['discount'] != 0) { ?>
                                                <tr>
                                                    <td><span>Discount (-)</span></td>
                                                    <td><span> <?php echo number_format($booking_details['discount'], 2) ?></span></td>
                                                </tr>
                                            <?php } ?>
                                            <?php if($extra_pax_price > 0){?>
                                                <tr>
                                                    <td><span>Extra Pax. Fare</span></td>
                                                    <td><span> <?php echo number_format($extra_pax_price, 2) ?></span></td>
                                                </tr>
                                            <?php } ?>
                                            <tr>
                                                <td style="border-top:1px solid #ccc"><span style="font-size:13px">Grand Total</span></td>
                                                <td style="border-top:1px solid #ccc"><span style="font-size:13px"> <?= number_format(@$booking_details['grand_total']- @$booking_details['extra_amount'], 2) ?></span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            <?php } ?>
                            </tr>						
                            <tr><td style="line-height:15px;padding:0;">&nbsp;</td></tr>
<?php if (isset($booking_transaction_details_value['extra_service_details']) == true && valid_array($booking_transaction_details_value['extra_service_details']) == true) { ?>
                                <tr>
                                    <td colspan="2" style="padding-top:10px; vertical-align:top; padding:0;">
                                        <table cellspacing="0" cellpadding="5" width="100%" style="border:1px solid #666666;font-size:12px; padding:0;">
                                            <tbody>
                                                <tr>
                                                    <td colspan="4" style="background-color:#666666; color:#fff;"><span style="font-size:13px">Flight Extra Information</span></td>
                                                </tr>

    <?php
    if (isset($booking_transaction_details_value['extra_service_details']['baggage_details']) == true && valid_array($booking_transaction_details_value['extra_service_details']['baggage_details']) == true) {
        $baggage_details = $booking_transaction_details_value['extra_service_details']['baggage_details'];
        foreach ($baggage_details['baggage_source_destination_label'] as $bag_lk => $bag_lv) {
            ?>
                                                        <tr><td colspan="4" style="background-color:#d9d9d9; color:#555555"><span><?php echo 'Extra Baggage Information'; ?> ( <?= $bag_lv ?> )</span></td></tr>
            <?php
            foreach ($baggage_details['details'] as $bag_k => $bag_v) {
                foreach ($bag_v as $bd_k => $bd_v) {
                    // debug($bag_lv);exit;
                    if ($bd_v['from_airport_code'] . '-' . $bd_v['to_airport_code'] == $bag_lv) {
                        ?>
                                                                    <tr><td colspan="4"><?= $bd_v['description'] ?> ( <?= $bag_v[0]['pax_name'] ?> )</td></tr>
                                                                <?php
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                if (isset($booking_transaction_details_value['extra_service_details']['meal_details']) == true && valid_array($booking_transaction_details_value['extra_service_details']['meal_details']) == true) {
                                                    $meal_details = $booking_transaction_details_value['extra_service_details']['meal_details'];
                                                    $meal_type = end($meal_details['details']);

                                                    $meal_type = $meal_type[0]['type'];
                                                    if ($meal_type == 'static') {
                                                        $meal_type_label = 'Meal Preference';
                                                    } else {
                                                        $meal_type_label = 'Meal Information';
                                                    }
                                                    foreach ($meal_details['meal_source_destination_label'] as $meal_lk => $meal_lv) {
                                                        ?>
                                                        <tr><td colspan="4" style="background-color:#d9d9d9; color:#555555"><span><?php echo $meal_type_label; ?> ( <?= $meal_lv ?> )</span></td></tr>
                                                        <?php
                                                        foreach ($meal_details['details'] as $meal_k => $meal_v) {
                                                            foreach ($meal_v as $md_k => $md_v) {

                                                                if ($md_v['from_airport_code'] . '-' . $md_v['to_airport_code'] == $meal_lv) {
                                                                    ?>
                                                                    <tr><td colspan="4"><?= $md_v['description'] ?> ( <?= $meal_v[0]['pax_name'] ?> )</td></tr>
                                                                <?php
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                if (isset($booking_transaction_details_value['extra_service_details']['seat_details']) == true && valid_array($booking_transaction_details_value['extra_service_details']['seat_details']) == true) {
                                                    $seat_details = $booking_transaction_details_value['extra_service_details']['seat_details'];
                                                    $seat_type = end($seat_details['details']);
                                                    $seat_type = $seat_type[0]['type'];
                                                    if ($seat_type == 'static') {
                                                        $seat_type_label = 'Seat Preference';
                                                    } else {
                                                        $seat_type_label = 'Seat Information';
                                                    }
                                                    foreach ($seat_details['seat_source_destination_label'] as $seat_lk => $seat_lv) {
                                                        ?>
                                                        <tr><td colspan="4" style="background-color:#d9d9d9; color:#555555"><span><?php echo $seat_type_label; ?> ( <?= $seat_lv ?> )</span></td></tr>
                                                        <?php
                                                        foreach ($seat_details['details'] as $seat_k => $seat_v) {
                                                            // debug($seat_lv);exit;
                                                            foreach ($seat_v as $sd_k => $sd_v) {
                                                                if ($sd_v['from_airport_code'] . '-' . $sd_v['to_airport_code'] == $seat_lv) {
                                                                    $seat_description = trim($sd_v['description']);
                                                                    if (empty($seat_description) == true) {
                                                                        $seat_description = trim($sd_v['code']);
                                                                    }
                                                                    ?>
                                                                    <tr><td colspan="4"><?= $seat_description ?> ( <?= $seat_v[0]['pax_name'] ?> )</td></tr>
                                                                <?php }
                                                            }
                                                        }
                                                    }
                                                } ?>

                                            </tbody>
                                        </table>
                                    </td>

                                </tr>
<?php } ?>
                        </tbody>
                    </table>
                </td>
            </tr>            
            <tr><td style="line-height:15px;padding:0;">&nbsp;</td></tr>
            <tr>
                <td colspan="4"><span style="line-height:20px; font-size:13px;">Important Information</span></td></tr>
            <tr>
                <td colspan="4" style="border-bottom:1px solid #999999; line-height:20px; font-size:12px; color:#555">
                    <?php echo $data['terms_conditions']; ?>
                </td>
            </tr>
        </tbody>
    </table>
<!-- Return Ticket -->
<?php
if (valid_array($return_segment_details)) {
    $airline_contact_no = $this->custom_db->single_table_records('airline_contact_numbers', '*', array('airline_code' => $return_segment_details[0]['airline_code']));
// debug($airline_contact_no);exit;
    if (isset($airline_contact_no['data'][0])) {
        $airline_number = '<span><img style="vertical-align:middle" src=' . SYSTEM_IMAGE_DIR . 'phone.png /><span style="font-size:16px;color:#00a9d6;vertical-align:middle;font-weight: 600;"> &nbsp;' . $airline_contact_no['data'][0]['phone_number'] . '</span></span> ';
    } else {
        $airline_number = '';
    }
    if ($booking_transaction_details[0] ['status'] == 'BOOKING_CONFIRMED') {
        $border = 'border-top:2px solid #808080;';
    } else if ($booking_transaction_details[0] ['status'] == 'BOOKING_HOLD') {
        $border = '';
    }
    ?>
    <div style="background:#ccc; width:100%; position:relative">
        <table class="pag_brk" cellpadding="0" border-collapse cellspacing="0" width="100%" style="font-size:12px; font-family: 'Open Sans', sans-serif; width:850px; margin:0px auto;background-color:#fff; padding:45px;border-collapse:separate;color: #000;">
            <tbody>
                <tr>
                    <td colspan="4" style="font-size: 18px;font-weight: 600;text-align: center;padding: 10px 0 12px;">E-Ticket <?php echo $Return; ?></td>
                </tr>
                <tr>
                    <td align="right" colspan="4" style="line-height:26px; border-top:1px solid #00a9d6; border-bottom:1px solid #00a9d6;"><span style="font-size:12px;">Status: </span><strong class="<?php echo booking_status_label($booking_transaction_details[0]['status']) ?>" style=" font-size:14px;">
                            <?php
                            switch (@$booking_transaction_details[0] ['status']) {
                                case 'BOOKING_CONFIRMED' :
                                    echo 'CONFIRMED';
                                    break;
                                case 'BOOKING_CANCELLED' :
                                    echo 'CANCELLED';
                                    break;
                                case 'BOOKING_FAILED' :
                                    echo 'FAILED';
                                    break;
                                case 'BOOKING_INPROGRESS' :
                                    echo 'INPROGRESS';
                                    break;
                                case 'BOOKING_INCOMPLETE' :
                                    echo 'INCOMPLETE';
                                    break;
                                case 'BOOKING_HOLD' :
                                    echo 'HOLD';
                                    break;
                                case 'BOOKING_PENDING' :
                                    echo 'PENDING';
                                    break;
                                case 'BOOKING_ERROR' :
                                    echo 'ERROR';
                                    break;
                            }
                            ?></strong></td>
                </tr>
                <tr>
                    <td style="padding:10px 0"><img style="width:60px;" src="<?= SYSTEM_IMAGE_DIR . 'airline_logo/' . $return_segment_details[0]['airline_code'] . '.gif' ?>" /></td>
                    <td style="padding:10px 0; line-height:25px;"><span style="display: block;border-right: 1px solid #999;"><span style="font-size:14px;"><?= @$return_segment_details[0]['airline_name'] ?></span><br><?php echo $airline_number ?></span></td>
                    <td style="padding:10px 0; padding-left: 10%; line-height:25px;"><span style="font-size:14px;">Agency</span><br><span><img style="vertical-align:middle" src="<?= SYSTEM_IMAGE_DIR . 'phone.png' ?>" /> <span style="font-size:16px;color:#00a9d6;vertical-align:middle;font-weight: 600;"> &nbsp;<?php echo $data['phone'] ?></span></span></td>
                    <?php if ((!empty($return_gds_pnr)) || (!empty($return_airline_pnr))) { ?>
                        <td style="padding:10px 0;text-align: center;">
                            <span style="font-size:14px; border:2px solid #808080; display:block">
                                <?php if ($booking_transaction_details[0] ['status'] == 'BOOKING_CONFIRMED' || $booking_transaction_details[0] ['status'] == 'BOOKING_HOLD' && empty($return_airline_pnr) == false) { ?>
                                    <span style="color:#00a9d6;padding:5px; display:block">AIRLINE PNR</span>
                                    <span style="font-size:26px;line-height:35px;padding-bottom: 5px;display:block;font-weight: 600;"><?= @$return_airline_pnr ?></span>
                                <?php } ?>
                                <?php if (($booking_transaction_details[0] ['status'] == 'BOOKING_CONFIRMED' || $booking_transaction_details[0] ['status'] == 'BOOKING_HOLD') && (!empty($return_gds_pnr))) { ?>
                                    <span style="<?php echo $border; ?>display:block; padding:5px;">GDS PNR:  <?= @$return_gds_pnr ?></span>
                                <?php } ?>
                            </span></td>
                    <?php } ?>
                </tr>
                <tr>
                    <td colspan="4" style="border:1px solid #00a9d6; padding:0;">
                        <table cellspacing="0" cellpadding="5" width="100%" style="font-size:12px; padding:0;">
                            <tbody>
                                <tr>
                                    <td colspan="2" style="background-color:#00a9d6; color:#fff"><img style="vertical-align:middle" src="<?= SYSTEM_IMAGE_DIR . 'flight.png' ?>"> &nbsp;<span style="vertical-align:middle;font-size:13px"><?php echo $Return; ?> Flight Details</span></td>
                                    <td align="right" colspan="2" style="background-color:#00a9d6; color:#fff"><span style="font-size:9px">*Please verify flight times with the airlines prior to departure</span></td>
                                </tr>

                                <?php
                                if (isset($booking_transaction_details) && $booking_transaction_details != "") {
                                    $checkin_baggage = 0;
                                    $cabin_baggage = 0;
                                    $seg_count = count($itinerary_details);
                                    if ($seg_count == 1) {
                                        $non_stop = 'Non Stop';
                                    } else {
                                        $non_stop = '';
                                    }
									$non_stop = '';
                                    foreach ($return_segment_details as $segment_details_k => $segment_details_v) {
                                        $itinerary_details_attributes = json_decode($segment_details_v ['attributes'], true);
                                        $airline_terminal_origin = @$itinerary_details_attributes['departure_terminal'];
                                        $airline_terminal_destination = @$itinerary_details_attributes['arrival_terminal'];
                                        $origin_terminal = '';
                                        $destination_terminal = '';
                                        if ($airline_terminal_origin != '') {
                                            $origin_terminal = 'Terminal ' . $airline_terminal_origin;
                                        }
                                        if ($airline_terminal_destination != '') {
                                            $destination_terminal = 'Terminal ' . $airline_terminal_destination;
                                        }

                                        //$checkin_baggage += (int) $segment_details_v['checkin_baggage'];
                                        //$cabin_baggage += (int) $segment_details_v['cabin_baggage'];  
# Checking CheckIN Baggage

                                        if (is_int($segment_details_v['checkin_baggage'])) {
                                            $BaggageSegment_checkin1 .= 'Checkin Baggage : ' . @$segment_details_v['from_airport_code'] . ' To ' . @$segment_details_v['to_airport_code'] . ' (Adult :' . (int) $segment_details_v['checkin_baggage'] . ' Kg & Child :' . (int) $segment_details_v['checkin_baggage'] . ' Kg)<br />';
                                            @$TotalBaggageCheckIN1 = (int) $segment_details_v['checkin_baggage'];
                                        } else {
                                            $BaggageSegment_checkin1 .= 'Checkin Baggage : ' . @$segment_details_v['from_airport_code'] . ' To ' . @$segment_details_v['to_airport_code'] . ' (Adult :' . $segment_details_v['checkin_baggage'] . ' & Child :' . $segment_details_v['checkin_baggage'] . ')<br />';
                                            @$TotalBaggageCheckIN1 = $segment_details_v['checkin_baggage'];
                                        }

# Checking Cabin Baggage

                                        if (is_int($segment_details_v['cabin_baggage'])) {
                                            @$BaggageSegment_cabin1 .= 'Cabin Baggage : ' . @$segment_details_v['from_airport_code'] . ' To ' . @$segment_details_v['to_airport_code'] . ' (Adult :' . (int) $segment_details_v['cabin_baggage'] . ' Kg & Child :' . (int) $segment_details_v['cabin_baggage'] . ')<br />';
                                            $TotalBaggageCabin1 = (int) $segment_details_v['cabin_baggage'];
                                        } else {
                                            @$BaggageSegment_cabin1 .= 'Cabin Baggage : ' . @$segment_details_v['from_airport_code'] . ' To ' . @$segment_details_v['to_airport_code'] . ' (Adult :' . $segment_details_v['cabin_baggage'] . ' & Child : ' . $segment_details_v['cabin_baggage'] . ')<br />';
                                            $TotalBaggageCabin1 = $segment_details_v['cabin_baggage'];
                                        }











                                        if ($seg_count != 1) {
                                            $non_stop = ($segment_details_k + 1) . ' Stop';
                                        }
										$non_stop = '';
                                        if (valid_array($segment_details_v) == true) {
                                            ?>
                                            <tr>
                                                <td style="background-color:#d9d9d9; color:#555555"><span style="color:#006f8e">Flight <?php echo $segment_details_v['segment_indicator']; ?></span></td>
                                                <td style="background-color:#d9d9d9; color:#555555"><img style="vertical-align:middle" src="<?= SYSTEM_IMAGE_DIR . 'flight_up.png' ?>">&nbsp;<span style="vertical-align:middle">Departing</span></td>
                                                <td style="background-color:#d9d9d9; color:#555555"><img style="vertical-align:middle" src="<?= SYSTEM_IMAGE_DIR . 'flight_down.png' ?>">&nbsp;<span style="vertical-align:middle">Arriving</span></td>
                                                <td style="background-color:#d9d9d9; color:#555555">&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td><span><?= @$segment_details_v['airline_name'] ?><br><?php echo $segment_details_v['airline_code'] . ' - ' . $segment_details_v['flight_number']; ?><br>Cabin: <?php echo $booking_details['cabin_class']; ?></span></td>
                                                <td><span><strong><?= @$segment_details_v['from_airport_name'] ?>(<?= @$segment_details_v['from_airport_code'] ?>)</strong><br><?php echo date("D, d M Y", strtotime($segment_details_v['departure_datetime'])) . ", " . date("h:i A", strtotime($segment_details_v['departure_datetime'])); ?><br><?php echo $origin_terminal; ?></span></td>
                                                <td><span><strong><?= @$segment_details_v['to_airport_name'] ?>(<?= @$segment_details_v['to_airport_code'] ?>)</strong><br><?php echo date("D, d M Y", strtotime($segment_details_v['arrival_datetime'])) . ", " . date("h:i A", strtotime($segment_details_v['arrival_datetime'])); ?><br><?php echo $destination_terminal; ?></span></td>
                                                <td><span style="display:block;border-left: 1px solid #999; padding-left:10%"><?php echo $non_stop; ?><br><?php echo $segment_details_v['total_duration']; ?><br><?php echo $segment_details_v['is_refundable'] ?></span></td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr><td style="line-height:15px;padding:0;">&nbsp;</td></tr>
                <tr>
                    <td colspan="4" style="border:1px solid #666666; padding:0;">
                        <table cellspacing="0" cellpadding="5" width="100%" style="font-size:12px; padding:0;">
                            <tbody>

                                <tr>
                                    <td colspan="4" style="background-color:#666666; color:#fff"><img style="vertical-align:middle" src="<?= SYSTEM_IMAGE_DIR . 'people_group.png' ?>"> &nbsp;<span style="vertical-align:middle;font-size:13px;">Passenger(s) Details</span></td>
                                </tr>

                                <tr>
                                    <td style="background-color:#d9d9d9; color:#555555"><span>Sr No.</span></td>
                                    <td style="background-color:#d9d9d9; color:#555555"><span>Passenger(s) Name</span></td>
                                    <td style="background-color:#d9d9d9; color:#555555"><span>Type</span></td>
                                    <td style="background-color:#d9d9d9; color:#555555"><span>E-ticket No</span></td>
                                </tr>
                                <?php
                                $booking_transaction_details_value = $booking_transaction_details [0];
                                // debug($booking_transaction_details_value);exit;
                                if (isset($booking_transaction_details_value ['booking_customer_details'])) {
                                    foreach ($booking_transaction_details_value ['booking_customer_details'] as $cus_k => $cus_v) {
											if($cust_origin != 0 && trim($cus_v["first_name"].$cus_v["last_name"]) != trim($cust_name))
												continue;
											
                                        if (strtolower($cus_v['passenger_type']) == 'infant') {
                                            $pass_name = $cus_v['title'] . '.' . $cus_v['first_name'] . '  ' . $cus_v['last_name'];
                                        } else {
                                            $pass_name = $cus_v['title'] . '.' . $cus_v['first_name'] . '  ' . $cus_v['last_name'];
                                        }
                                        ?>
                                        <tr>
                                            <td><span><?php echo ($cus_k + 1); ?></span></td>
                                            <td><span><strong><?php echo $pass_name; ?></strong</span></td>
                                            <td><span><?php echo ucfirst($cus_v['passenger_type']) ?></span></td>
                                            <?php 
                                            if(@$cus_v['TicketNumber'] != @$cus_v['TicketId']){
                                            ?> <td><span><?= @$cus_v['TicketNumber']; ?></span></td> <?php  
                                            }else{
                                            ?> <td style="padding-left: 5%;"><span>-</span></td> <?php    
                                            }
                                            ?> 
                                        </tr>

            <?php
        }
    }
    ?>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr><td style="line-height:15px;padding:0;">&nbsp;</td></tr>
                <tr>
                    <td colspan="4" style="padding:0;">
                        <table cellspacing="0" cellpadding="5" width="100%" style="font-size:12px; padding:0;">
                            <tbody>
                                <tr>

                                    <td style="padding:0; vertical-align:top">
                                        <table cellspacing="0" cellpadding="5" width="100%" style="border:1px solid #9a9a9a;font-size:12px; padding:0;">
                                            <tbody>
                                                <tr>
                                                    <td colspan="4" style="border-bottom:1px solid #ccc"><span style="font-size:13px">Flight Inclusions</span></td>
                                                </tr>



                                                <tr>

    <?php if ($TotalBaggageCheckIN1 != '' && $TotalBaggageCheckIN1 != 0) { ?>
                                                        <td colspan="4"><span><?php echo $BaggageSegment_checkin1; ?></span></td>

    <?php } else {
        ?>
                                                        <td colspan="4"><span>Cabin Baggage :  Allowed as per the Airline Policy</span></td>
                                                    <?php } ?>
                                                </tr>
                                                <tr>

    <?php if ($TotalBaggageCabin1 != '' && $TotalBaggageCabin1 != 0) { ?>
                                                        <td colspan="4"><span><?php echo $BaggageSegment_cabin1; ?></span></td>


    <?php } else {
        ?>
                                                        <td colspan="4"><span>Cabin Baggage :  Allowed as per the Airline Policy</span></td>
                                                    <?php } ?>
                                                </tr>



                                                <tr>
                                                    <td colspan="4" style="border-top:1px solid #ccc"><span style="font-size:10px; color:#666; line-height:20px;">* Flight inclusions are subject to change with Airlines.</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
    <?php if (isset($booking_transaction_details_value['extra_service_details']) == true && valid_array($booking_transaction_details_value['extra_service_details']) == true) { ?>
                                    <tr>
                                        <td style="padding:10px 0 0 0;vertical-align:top">
                                            <table cellspacing="0" cellpadding="5" width="100%" style="border:1px solid #9a9a9a;font-size:12px; padding:0;">
                                                <tbody>
                                                    <tr>
                                                        <td colspan="4" style="border-bottom:1px solid #ccc"><span style="font-size:13px">Flight Extra Information</span></td>
                                                    </tr>
        <?php
        if (isset($booking_transaction_details_value['extra_service_details']['baggage_details']) == true && valid_array($booking_transaction_details_value['extra_service_details']['baggage_details']) == true) {
            $baggage_details = $booking_transaction_details_value['extra_service_details']['baggage_details'];
            foreach ($baggage_details['baggage_source_destination_label'] as $bag_lk => $bag_lv) {
                ?>
                                                            <tr><td colspan="4" style="background-color:#d9d9d9; color:#555555"><span><?php echo 'Extra Baggage Information'; ?> ( <?= $bag_lv ?> )</span></td></tr>
                                                            <?php
                                                            foreach ($baggage_details['details'] as $bag_k => $bag_v) {
                                                                foreach ($bag_v as $bd_k => $bd_v) {
                                                                    // debug($bag_lv);exit;
                                                                    if ($bd_v['from_airport_code'] . '-' . $bd_v['to_airport_code'] == $bag_lv) {
                                                                        ?>
                                                                        <tr><td colspan="4"><?= $bd_v['description'] ?> ( <?= $bag_v[0]['pax_name'] ?> )</td></tr>
                                                                    <?php
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                    // debug($booking_transaction_details_value);exit;
                                                    if (isset($booking_transaction_details_value['extra_service_details']['meal_details']) == true && valid_array($booking_transaction_details_value['extra_service_details']['meal_details']) == true) {
                                                        $meal_details = $booking_transaction_details_value['extra_service_details']['meal_details'];
                                                        $meal_type = end($meal_details['details']);

                                                        $meal_type = $meal_type[0]['type'];
                                                        if ($meal_type == 'static') {
                                                            $meal_type_label = 'Meal Preference';
                                                        } else {
                                                            $meal_type_label = 'Meal Information';
                                                        }
                                                        foreach ($meal_details['meal_source_destination_label'] as $meal_lk => $meal_lv) {
                                                            ?>
                                                            <tr><td colspan="4" style="background-color:#d9d9d9; color:#555555"><span><?php echo $meal_type_label; ?> ( <?= $bag_lv ?> )</span></td></tr>
                                                            <?php
                                                            foreach ($meal_details['details'] as $meal_k => $meal_v) {
                                                                foreach ($meal_v as $md_k => $md_v) {

                                                                    if ($md_v['from_airport_code'] . '-' . $md_v['to_airport_code'] == $bag_lv) {
                                                                        ?>
                                                                        <tr><td colspan="4"><?= $md_v['description'] ?> ( <?= $meal_v[0]['pax_name'] ?> )</td></tr>
                                                                    <?php
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                    if (isset($booking_transaction_details_value['extra_service_details']['seat_details']) == true && valid_array($booking_transaction_details_value['extra_service_details']['seat_details']) == true) {
                                                        $seat_details = $booking_transaction_details_value['extra_service_details']['seat_details'];
                                                        $seat_type = end($seat_details['details']);
                                                        $seat_type = $seat_type[0]['type'];
                                                        if ($seat_type == 'static') {
                                                            $seat_type_label = 'Seat Preference';
                                                        } else {
                                                            $seat_type_label = 'Seat Information';
                                                        }
                                                        foreach ($seat_details['seat_source_destination_label'] as $seat_lk => $seat_lv) {
                                                            ?>
                                                            <tr><td colspan="4" style="background-color:#d9d9d9; color:#555555"><span><?php echo $seat_type_label; ?> ( <?= $seat_lv ?> )</span></td></tr>
                                                            <?php
                                                            foreach ($seat_details['details'] as $seat_k => $seat_v) {
                                                                // debug($seat_lv);exit;
                                                                foreach ($seat_v as $sd_k => $sd_v) {
                                                                    if ($sd_v['from_airport_code'] . '-' . $sd_v['to_airport_code'] == $seat_lv) {
                                                                        $seat_description = trim($sd_v['description']);
                                                                        if (empty($seat_description) == true) {
                                                                            $seat_description = trim($sd_v['code']);
                                                                        }
                                                                        ?>
                                                                        <tr><td colspan="4"><?= $seat_description ?> ( <?= $seat_v[0]['pax_name'] ?> )</td></tr>
                        <?php }
                    }
                }
            }
        } ?>
                                                </tbody>
                                            </table>
                                        </td>

                                    </tr>
    <?php } ?>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr><td style="line-height:15px;padding:0;">&nbsp;</td></tr>
                <tr><td colspan="4" style="border-bottom:1px solid #999999;padding-bottom:15px"><span style="font-size:13px; color:#555;">Customer Contact Details | E-mail : <?= $booking_details['email'] ?> | Contact No : <?= $booking_details['phone'] ?></span></td></tr>
                <tr><td style="line-height:15px;padding:0;">&nbsp;</td></tr>
                <tr>
                    <td colspan="4"><span style="line-height:20px; font-size:13px;">Important Information</span></td></tr>
                <tr>
                    <td colspan="4" style="border-bottom:1px solid #999999; line-height:20px; font-size:12px; color:#555">
                      <?php echo $data['terms_conditions']; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
<?php } ?>

<?php 
    $app_reference = $booking_details['app_reference'];
    $booking_source = $booking_details['booking_source'];
    $booking_status = $booking_details['status'];
    $current_module = $this->uri->segment(2);
    $booking_details = $data ['booking_details'] [0];
?>
</div>
<?php
if($booking_status == 'BOOKING_CONFIRMED' || $booking_status == 'BOOKING_HOLD'){
?>
	<table  style="border-collapse: collapse; font-size: 14px; margin: 10px auto; font-family: arial;"
		width="70%" cellpadding="0" cellspacing="0" border="0">
		<tbody>
			<tr>
				<td align="center">
					<a href="<?php echo base_url();?>index.php/voucher/voucher_edit/<?=$app_reference?>/<?=$booking_source;?>/<?=$booking_status;?>/show_voucher/<?=$current_module;?>">
					<input
					style="background: #418bca; height: 34px; padding: 10px; border-radius: 4px; border: none; color: #fff; margin: 0 2px;"
					type="button" value="Add Extra Passenger" /></a></td>
			</tr>
		</tbody>
	</table>
<?php } ?>
   <table id="printOption"onclick="w=window.open();w.document.write(document.getElementById('ticket_flight').innerHTML);w.print();w.close(); return true;"
      style="border-collapse: collapse;font-size: 14px; margin: 10px auto; font-family: arial;" width="70%" cellpadding="0" cellspacing="0" border="0">
      <tbody>
         <tr>
            <td align="center"><input style="background:#418bca; height:34px; padding:10px; border-radius:4px; border:none; color:#fff; margin:0 2px;" type="button" value="Print" />
         </tr>
      </tbody>
   </table>