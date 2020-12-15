<?php
$cust_contact_td_width = "50%";
$with_fare = 1;
//debug($data);exit;
$booking_details = $data ['booking_details'] [0];
// debug($booking_details);exit;
$itinerary_details = $booking_details ['booking_itinerary_details'];
// debug($itinerary_details);exit;
$attributes = $booking_details ['attributes'];
$customer_details = $booking_details ['booking_transaction_details'] [0] ['booking_customer_details'];
$domain_details = $booking_details;
$lead_pax_details = $customer_details;
$booking_transaction_details = $booking_details ['booking_transaction_details'];

$adult_count = 0;
$infant_count = 0;

foreach ($customer_details as $k => $v) {
    if (strtolower($v ['passenger_type']) == 'infant') {
        $infant_count ++;
    } else {
        $adult_count ++;
    }
}

$Onward = '';
$return = '';
if (count($booking_transaction_details) == 2) {
    $Onward = 'Onward ';
    $Return = 'Return ';
}
$segment_details = array();
// debug($itinerary_details);exit;
foreach ($itinerary_details as $k => $sub_details) {
    $segment_details [$sub_details['airline_code']][] = $sub_details;
    
}
$segment_details = array_values($segment_details);
$onward_segment_details = $segment_details[0];
$return_segment_details = $segment_details[1];
// generate onword and return
if ($booking_details ['is_domestic'] == true && count($booking_transaction_details) == 2) {
    $onward_segment_details = array();
    $return_segment_details = array();
    $segment_indicator_arr = array();
    $segment_indicator_sort = array();

    foreach ($itinerary_details as $key => $key_sort_data) {
        $segment_indicator_sort [$key] = $key_sort_data ['origin'];
    }
    array_multisort($segment_indicator_sort, SORT_ASC, $itinerary_details);

    foreach ($itinerary_details as $k => $sub_details) {
        $segment_indicator_arr [] = $sub_details ['segment_indicator'];
        $count_value = array_count_values($segment_indicator_arr);

        if ($count_value [1] == 1) {
            $onward_segment_details [] = $sub_details;
        } else {
            $return_segment_details [] = $sub_details;
        }
    }
}
// debug($onward_segment_details);exit;
if (!empty($itinerary_details[0]['airline_pnr'])) {
    $airline_pnr_arr = array();
    foreach ($itinerary_details as $key => $value) {
        if(!in_array($value['airline_pnr'], $airline_pnr_arr)){
            if(empty($value['airline_pnr']) == false){
                $airline_pnr .= $value['airline_pnr'].' / ';
                $airline_pnr_arr[] = $value['airline_pnr'];
            }
        }

    }
$airline_pnr = substr($airline_pnr, 0, -2);
    $gds_pnr = $booking_transaction_details[0]['gds_pnr'];
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
        $return_gds_pnr = $booking_transaction_details[0]['gds_pnr'];
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

$airline_contact_no = $this->custom_db->single_table_records('airline_contact_numbers', '*', array('airline_code' => $itinerary_details[0]['airline_code']));
// debug($airline_contact_no);exit;
if (isset($airline_contact_no['data'][0])) {
    $airline_number = '<span><img style="vertical-align:middle" src=' . FULL_SYSTEM_IMAGE_DIR . 'phone.png /><span style="font-size:12pt;color:#00a9d6;vertical-align:middle;font-weight: 600;">' . $airline_contact_no['data'][0]['phone_number'] . '</span></span> ';
} else {
    $airline_number = '';
}

$trip_type = $booking_details['trip_type'];
// echo $meal_price;exit;
// debug($fare_details);exit;
?>
<style type="text/css">
    td, th {padding: 5px;}
    table {
        border-collapse: collapse;
        border-spacing: 0;
    }
    @media print { 
        header, footer, #show_log { display: none; }
        .pag_brk { page-break-before: always; }
    }
</style>
<div style="background:#ccc; width:100%; position:relative">
    <table cellpadding="0" border-collapse cellspacing="0" width="100%" style="font-size:9pt;font-family: 'Open Sans', sans-serif; max-width:850px; margin:0px auto;background-color:#fff; padding:45px;border-collapse:separate; color: #000;">
        <tbody>
            <tr>
                <td colspan="2" style="padding-bottom:10px" class="voucher_logo"><img src="<?= $GLOBALS['CI']->template->domain_full_images($data['logo']) ?>" style="height: 50px;"/></td>
                <td colspan="2" style="padding-bottom:10px" align="right">
                <span><?=$data['domainname']?></span><br>
				<span><?=$data['phone']?></span><br>
				<span><?=$data['address']?></span><br>
                <span>Booked on : <?= app_friendly_absolute_date(@$booking_details['booked_date']) ?></span><br/>
                    <?php if(!empty($data['gst_name'])){
?>
<span>GST Name : <?=$data['gst_name']?></span><br>
<?php }?>
<?php if(!empty($data['gst_number'])){
?>
<span>GST No : <?=$data['gst_number']?></span><br>
<?php }?>
                </td>
            </tr>
            <tr><td colspan="4" style="line-height:7px;padding:0;">&nbsp;</td></tr>
            <tr>
            <td align="left" colspan="2" style="line-height:26px; border-top:1px solid #00a9d6; border-bottom:1px solid #00a9d6;"> <span>Booking ID : <?= @$booking_details['app_reference'] ?></span></td>
<td align="right" colspan="2" style="line-height:26px; border-top:1px solid #00a9d6; border-bottom:1px solid #00a9d6;"><span style="font-size:12px;">Status: </span><strong class="<?php echo booking_status_label($booking_transaction_details[0]['status']) ?>" style=" font-size:12px;">
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
                    <td colspan="4" style="font-size: 15pt;font-weight: 600;text-align: center;padding: 10px 0 0; line-height:35px;">E-Ticket <?php echo $Onward; ?></td>
                </tr>
<?php } ?>
            <tr><td style="line-height:6px;padding:0;">&nbsp;</td></tr>
            <tr>
                <td style="padding:10px 0; vertical-align:middle;"><img style="width:40px;" src="<?= FULL_SYSTEM_IMAGE_DIR . 'airline_logo/' . $itinerary_details[0]['airline_code'] . '.gif' ?>" /></td>

                <td style="padding:10px 0; vertical-align:middle; line-height:normal;"><span style="display: block;border-right: 1px solid #999;"><span style="font-size:11pt;"><?= @$itinerary_details[0]['airline_name'] ?></span><br><br><?php echo $airline_number ?></span></td>
                <td style="padding:10px 0; vertical-align:middle; line-height:normal;"><span style="font-size:11pt;">Agency</span><br><br><span style="font-size:12pt;color:#00a9d6;vertical-align:middle;font-weight: 600;"><img valign="bottom" style="vertical-align:middle;width:12px;line-height:normal;" src="<?= FULL_SYSTEM_IMAGE_DIR . 'phone.png' ?>" />&nbsp; <span style="line-height:normal;"><?php echo $data['phone'] ?></span></span></td>

<?php if ((!empty($gds_pnr)) || (!empty($airline_pnr))) { ?>
                    <td style="padding:5px 0;text-align: center;border:2px solid #808080;">
                        <span style="font-size:12px; display:block;border:2px solid #808080;">
    <?php if ($booking_transaction_details[0] ['status'] == 'BOOKING_CONFIRMED' || $booking_transaction_details[0] ['status'] == 'BOOKING_HOLD') { ?>
                                <span style="color:#00a9d6;padding:5px; display:block;font-size: 10px;">AIRLINE PNR</span><br>
                                <span style="font-size:14px;padding-bottom: 5px;display:block;font-weight: 600;"><?= @$airline_pnr ?></span><br>
                    <?php } ?>
                    <?php if (($booking_transaction_details[0] ['status'] == 'BOOKING_CONFIRMED' || $booking_transaction_details[0] ['status'] == 'BOOKING_HOLD') && !empty($gds_pnr)) { ?>
                                <span style="<?php echo $border; ?> color:#00a9d6;padding:5px; display:block;font-size: 10px;">GDS PNR</span><br><span style="font-size:14px;padding-bottom: 5px;display:block;font-weight: 600;"><?= @$gds_pnr ?></span>
                            <?php } ?>
                        </span></td>
<?php } ?>
            </tr>
            <tr><td colspan="4" style="line-height:8px;padding:0;">&nbsp;</td></tr>
            <tr>
                <td colspan="4" style="padding:0;">
<table cellspacing="0" cellpadding="5" width="100%" style="border:1px solid #666666; font-size:9pt; padding:0;">
                            <tbody>
                                <tr>
                                    <td colspan="5" style="background-color:#666666; color:#fff"><img width="12" style="vertical-align:middle" src="<?= FULL_SYSTEM_IMAGE_DIR . 'people_group.png' ?>"> &nbsp;<span style="vertical-align:middle;font-size:10pt">Passenger(s) Details</span></td>
                                </tr>
                                <tr>
                                    <td style="background-color:#d9d9d9;border-left:1px solid #666666; color:#555555"><span>Sr No.</span></td>
                                    <td style="background-color:#d9d9d9; color:#555555"><span>Passenger(s) Name</span></td>
                                    <td style="background-color:#d9d9d9; color:#555555"><span>Type</span></td>
                                    <td style="background-color:#d9d9d9; color:#555555"><span>E-ticket No</span></td>
									<td style="background-color:#d9d9d9;border-right:1px solid #666666; color:#555555"><span>Status</span></td>
                                </tr>
                                <?php
                                $booking_transaction_details_value = $booking_transaction_details [0];
                                // debug($booking_transaction_details_value);exit;
                                if (isset($booking_transaction_details_value ['booking_customer_details'])) {
                                    foreach ($booking_transaction_details_value ['booking_customer_details'] as $cus_k => $cus_v) {
                                        if (strtolower($cus_v['passenger_type']) == 'infant') {
                                            $pass_name = $cus_v['first_name'] . '  ' . $cus_v['last_name'];
                                        } else {
                                            $pass_name = $cus_v['title'] . '.' . $cus_v['first_name'] . '  ' . $cus_v['last_name'];
                                        }
                                        ?>
                                        <tr>
                                            <td style="line-height: 13px;"><span><?php echo ($cus_k + 1); ?></span></td>
                                            <td style="line-height: 13px;"><span><strong><?php echo $pass_name; ?></strong></span></td>
                                            <td style="line-height: 13px;"><span><?php echo ucfirst($cus_v['passenger_type']) ?></span></td>
                                            <?php 
                                            if(@$cus_v['TicketNumber'] != @$cus_v['TicketId']){
                                            ?> <td style="line-height: 13px;"><span><?= @$cus_v['TicketNumber']; ?></span></td> <?php  
                                            }else{
                                            ?> <td style="line-height: 13px; padding-left: 5%;"><span>-</span></td> <?php    
                                            }
                                            ?> 
											<td style="line-height: 13px;"><span><?= (@$cus_v['status'])?'CONFIRMED':'CANCELLED'; ?></span></td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                </td>
            </tr>
			<tr>
                    <td style="line-height:8px;padding:0;">&nbsp;</td>
                </tr>
            <tr>
                <td colspan="4" style="padding:0;"><table cellspacing="0" cellpadding="5" width="100%" style="border:1px solid #00a9d6;font-size:9pt; padding:0;">
                        <tbody><tr><td colspan="2" style="background-color:#00a9d6; color:#fff"><img width="9" src="<?= FULL_SYSTEM_IMAGE_DIR . 'flight.png' ?>"> &nbsp;<span style="vertical-align:middle;font-size:10pt">Onward Flight Details</span></td>     <td align="right" colspan="2" style="background-color:#00a9d6; color:#fff;line-height:normal;vertical-align:middle;"><span style="font-size:8pt;">*Please verify flight times with the airlines prior to departure</span></td>
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
    $seg_in_array = array();
    $seg_array = array();
    $seg_counts = 0;
	$BaggageSegment_ccheckin = "";
	$BaggageSegment_cabin = "";
    foreach ($onward_segment_details as $segment_details_k => $segment_details_v) {
        // echo $trip_type;
        $seg_array [] = $segment_details_v['segment_indicator'];
        if (in_array($segment_details_v['segment_indicator'], $seg_in_array) && !empty($seg_in_array) && ($seg_counts == 0) && ($trip_type != 'multicity')) {
            // echo $seg_count;
            $seg_counts = $seg_counts + 1;

            // debug($seg_in_array);
            ?>
                                        <tr>
                                            <td colspan="2" style="background-color:#00a9d6; color:#fff"><img width="9" src="<?= FULL_SYSTEM_IMAGE_DIR . 'flight.png' ?>"> &nbsp;<span style="vertical-align:middle;font-size:10pt">Return Flight Details</span></td> 
                                            <td align="right" colspan="2" style="background-color:#00a9d6; color:#fff;line-height:normal;vertical-align:middle;"><span style="font-size:8pt;">*Please verify flight times with the airlines prior to departure</span></td>
                                        </tr>
                                    <?php
                                    }
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
                                    if ($seg_count != 1 && $trip_type != 'multicity') {

                                        if (count(array_unique($seg_array)) == 1 && end($seg_array) == 1 && $seg_count == 2 && $trip_type != 'oneway') {

                                            $non_stop = 'Non Stop';
                                        } else {
                                            $non_stop = $segment_details_v['segment_indicator'] . ' Stop';
                                        }
                                    } else if ($seg_count != 1) {
                                        $non_stop = ($segment_details_k + 1) . ' Stop';
                                    }
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
                <td style="background-color:#d9d9d9; color:#555555"><img style="vertical-align:middle" src="<?= FULL_SYSTEM_IMAGE_DIR . 'flight_up.png' ?>">&nbsp;<span style="vertical-align:middle">Departing</span></td>
                <td style="background-color:#d9d9d9; color:#555555"><img style="vertical-align:middle" src="<?= FULL_SYSTEM_IMAGE_DIR . 'flight_down.png' ?>">&nbsp;<span style="vertical-align:middle">Arriving</span></td>
                <td style="background-color:#d9d9d9; color:#555555">&nbsp;</td>
            </tr>
            <tr>
                <td><span><?= @$segment_details_v['airline_name'] ?><br><?php echo $segment_details_v['airline_code'] . ' - ' . $segment_details_v['flight_number']; ?><br>Cabin: <?php echo $booking_details['cabin_class']; ?></span></td>
                <td><span><strong><?= @$segment_details_v['from_airport_name'] ?>(<?= @$segment_details_v['from_airport_code'] ?>)</strong><br><?php echo date("D, d M Y", strtotime($segment_details_v['departure_datetime'])) . ", " . date("H:i", strtotime($segment_details_v['departure_datetime'])); ?><br><?php echo $origin_terminal; ?></span></td>
                <td><span><strong><?= @$segment_details_v['to_airport_name'] ?>(<?= @$segment_details_v['to_airport_code'] ?>)</strong><br><?php echo date("D, d M Y", strtotime($segment_details_v['arrival_datetime'])) . ", " . date("H:i", strtotime($segment_details_v['arrival_datetime'])); ?><br><?php echo $destination_terminal; ?></span></td>
                <td><span style="display:block;border-left: 1px solid #999;"><?php echo $non_stop; echo " | ".$segment_details_v['total_duration']; ?></span></td>
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
            <tr>
                <td style="line-height:4px;padding:0;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="4"><table cellspacing="0" cellpadding="5" width="100%" style="font-size:9pt; padding:0;"><tbody><tr>
<td width="<?php echo $cust_contact_td_width; ?>" style="padding:0;padding-right:14px; vertical-align:top"><table cellspacing="0" cellpadding="5" width="100%" style="border:1px solid #9a9a9a;font-size:10px; padding:0;">
            <tbody><tr><td colspan="4" style="border-bottom:1px solid #ccc"><span style="font-size:12px">Customer Contact Details</span></td>
                </tr>
               
                <tr>
                    <td colspan="4">
                       Name : <?=$booking_details['lead_pax_name']?><br>
                       E-mail : <?= $booking_details['email'] ?> <br>
					   Phone :<?= $booking_details['phone_code'] ?> <?= $booking_details['phone'] ?> <br>
					   GSTIN : <?= $booking_details['gst_number'] ?>
                    </td>
                </tr>
                 <tr>
                    <td colspan="4" style="border-top:1px solid #ccc"><span style="font-size:10px; color:#666; line-height:20px;">* Flight inclusions are subject to change with Airlines.</span></td>
                </tr>
            </tbody>
        </table>
    </td>
<?php if($with_fare) { ?>
    <td width="50%" style="padding:0;">
        <table cellspacing="0" cellpadding="5" width="100%" style="font-size:10px; padding:0;border:1px solid #9a9a9a;">
            <tbody>
                <tr>
                    <td style="border-bottom:1px solid #ccc"><span style="font-size:12px">Payment Details</span></td>
                    <td style="border-bottom:1px solid #ccc"><span style="font-size:12px">Amount ( <?php echo $booking_details['currency'] ?>
                            )</span></td>
                </tr>
                <tr>
                    <td><span>Base Fare</span></td>
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
                <tr>
                    <td style="border-top:1px solid #ccc"><span style="font-size:11px">Grand Total</span></td>
                    <td style="border-top:1px solid #ccc"><span style="font-size:11px"> <?= number_format(@$booking_details['grand_total'] + $seat_price + $meal_price + $baggage_price, 2) ?></span></td>
                </tr>
            </tbody>
        </table>
    </td>
<?php } ?>
</tr>
                                                <?php if (isset($booking_transaction_details_value['extra_service_details']) == true && valid_array($booking_transaction_details_value['extra_service_details']) == true) { ?>
                                <tr>
                                    <td colspan="2" style="padding-top:10px; vertical-align:top; padding:0;font-size: 10px;">
                                        <table cellspacing="0" cellpadding="5" width="100%" style="border:1px solid #666666;font-size:9pt; padding:0;">
                                            <tbody>
                                                <tr>
                                                    <td colspan="4" style="background-color:#666666; color:#fff;"><span style="font-size:10pt">Flight Extra Information</span></td>
                                                </tr>
    <?php
    if (isset($booking_transaction_details_value['extra_service_details']['baggage_details']) == true && valid_array($booking_transaction_details_value['extra_service_details']['baggage_details']) == true) {
        $baggage_details = $booking_transaction_details_value['extra_service_details']['baggage_details'];
        foreach ($baggage_details['baggage_source_destination_label'] as $bag_lk => $bag_lv) {
            ?>
                                                        <tr>
                                                            <td colspan="4" style="background-color:#d9d9d9; color:#555555"><span><?php echo 'Extra Baggage Information'; ?> ( <?= $bag_lv ?> )</span></td>
                                                        </tr>
            <?php
            foreach ($baggage_details['details'] as $bag_k => $bag_v) {
                foreach ($bag_v as $bd_k => $bd_v) {
                    // debug($bag_lv);exit;
                    if ($bd_v['from_airport_code'] . '-' . $bd_v['to_airport_code'] == $bag_lv) {
                        ?>
                                                                    <tr>
                                                                        <td colspan="4" style="border-left:none;border-right:1px solid #fff"><?= $bd_v['description'] ?> ( <?= $bag_v[0]['pax_name'] ?> )</td>
                                                                    </tr>
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
                                                        <tr>
                                                            <td colspan="4" style="background-color:#d9d9d9; color:#555555"><span><?php echo $meal_type_label; ?> ( <?= $meal_lv ?> )</span></td>
                                                        </tr>
                                                        <?php
                                                        foreach ($meal_details['details'] as $meal_k => $meal_v) {
                                                            foreach ($meal_v as $md_k => $md_v) {

                                                                if ($md_v['from_airport_code'] . '-' . $md_v['to_airport_code'] == $meal_lv) {
                                                                    ?>
                                                                    <tr>
                                                                        <td colspan="4" style="border-left:none;border-right:1px solid #fff"><?= $md_v['description'] ?> ( <?= $meal_v[0]['pax_name'] ?> )</td>
                                                                    </tr>
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
                                                        <tr>
                                                            <td colspan="4" style="background-color:#d9d9d9; color:#555555"><span><?php echo $seat_type_label; ?> ( <?= $seat_lv ?> )</span></td>
                                                        </tr>
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
                                                                    <tr>
                                                                        <td colspan="4"  style="border-left:none;border-right:1px solid #fff; border-bottom:none"><?= $seat_description ?> ( <?= $seat_v[0]['pax_name'] ?> )</td>
                                                                    </tr>
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
            <tr>
                <td style="line-height:3px;padding:0;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="4" ><span style="line-height:23px; font-size:10pt;">Important Information</span></td>
            </tr>
            <tr>
                <td colspan="4" style="font-size:9pt; color:#555">
                <?php echo $data['terms_conditions']; ?>
                </td>
            </tr>
            <?php
if(isset($adv_data[0]['image'])){
?>
<tr>
<td colspan="4" style="width:100%;text-align:center";>
<img style="padding-top:5%;width:700px; height: 100px; text-align:center" src="<?=$this->template->domain_images().'offer_images/'.$adv_data[0]['image']?>">
</td>
</tr>
<?php } ?>
        </tbody>
    </table>
</div>
<!-- Return Ticket -->
<?php
if (valid_array($return_segment_details)) {
    $airline_contact_no = $this->custom_db->single_table_records('airline_contact_numbers', '*', array('airline_code' => $return_segment_details[0]['airline_code']));
// debug($airline_contact_no);exit;
    if (isset($airline_contact_no['data'][0])) {
        $airline_number = '<span><img style="vertical-align:middle" src=' . FULL_SYSTEM_IMAGE_DIR . 'phone.png /><span style="font-size:16px;color:#00a9d6;vertical-align:middle;font-weight: 600;"> &nbsp;' . $airline_contact_no['data'][0]['phone_number'] . '</span></span> ';
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
        <table class="pag_brk" cellpadding="0" border-collapse cellspacing="0" width="100%" style="font-size:9pt; font-family: 'Open Sans', sans-serif; max-width:850px; margin:0px auto;background-color:#fff; padding:45px;border-collapse:separate; color: #000;">
            <tbody>
                <tr>
                    <td colspan="4" style="font-size: 15pt;font-weight: 600;text-align: center;padding: 10px 0 0; line-height:20px;">E-Ticket <?php echo $Return; ?></td>
                </tr>
                <tr><td colspan="4" style="line-height:6px;padding:0;">&nbsp;</td></tr>
                <tr>
                    <td align="right" colspan="4" style="line-height:25px;font-size:12px; border-top:2px solid #00a9d6; border-bottom:1px solid #00a9d6;"><span style="font-size:9pt;">Status: </span><strong class="<?php echo booking_status_label($booking_transaction_details[0]['status']) ?>" style=" font-size:12px;">
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
                            ?></strong>
                    </td>
                </tr>
                <tr><td style="line-height:14px;padding:0;">&nbsp;</td></tr>
                <tr>
                    <td style="padding:10px 0; vertical-align:middle;"><img style="width:40px;" src="<?= FULL_SYSTEM_IMAGE_DIR . 'airline_logo/' . $return_segment_details[0]['airline_code'] . '.gif' ?>" /></td>
                    <td style="padding:10px 0; vertical-align:middle; line-height:normal;"><span style="display: block;border-right: 1px solid #999;"><span style="font-size:11pt;"><?= @$return_segment_details[0]['airline_name'] ?></span><br><br><?php echo $airline_number ?></span></td>
                    <td style="padding:10px 0; vertical-align:middle; padding-left: 10%; line-height:normal;"><span style="font-size:11pt;">Agency</span><br><br><span style="font-size:12pt;color:#00a9d6;vertical-align:middle;font-weight: 600;"><img valign="bottom" style="vertical-align:middle;width:12pt;line-height:normal;" src="<?= FULL_SYSTEM_IMAGE_DIR . 'phone.png' ?>" />&nbsp; <span style="line-height:normal;"><?php echo $data['phone'] ?></span></span></td>
<?php if ((!empty($return_gds_pnr)) || (!empty($return_airline_pnr))) { ?>
                    <td style="padding:5px 0;text-align: center;border:2px solid #808080;">
                        <span style="font-size:12px; display:block;border:2px solid #808080;">
    <?php if ($booking_transaction_details[0] ['status'] == 'BOOKING_CONFIRMED' || $booking_transaction_details[0] ['status'] == 'BOOKING_HOLD') { ?>
                                <span style="color:#00a9d6;padding:5px; display:block;font-size: 10px;">AIRLINE PNR</span><br>
                                <span style="font-size:14px;padding-bottom: 5px;display:block;font-weight: 600;"><?= @$return_airline_pnr ?></span><br>
                    <?php } ?>
                    <?php if (($booking_transaction_details[0] ['status'] == 'BOOKING_CONFIRMED' || $booking_transaction_details[0] ['status'] == 'BOOKING_HOLD') && !empty($gds_pnr)) { ?>
                                <span style="<?php echo $border; ?> color:#00a9d6;padding:5px; display:block;font-size: 10px;">GDS PNR</span><br><span style="font-size:14px;padding-bottom: 5px;display:block;font-weight: 600;"><?= @$return_gds_pnr ?></span>
                            <?php } ?>
                        </span></td>
<?php } ?>
                </tr>
                <tr><td colspan="4" style="line-height:6px;padding:0;">&nbsp;</td></tr>
                <tr>
                    <td colspan="4" style="padding:0;">
                        <table border="0" cellspacing="0" cellpadding="5" width="100%" style="border:1px solid #00a9d6;font-size:9pt; padding:0;">
                            <tbody>
                                <tr>
                                    <td colspan="2" style="background-color:#00a9d6; color:#fff"><img width="9" src="<?= FULL_SYSTEM_IMAGE_DIR . 'flight.png' ?>"> &nbsp;<span style="vertical-align:middle;font-size:10pt"><?php echo $Return; ?>Flight Details</span></td>
                                    <td align="right" colspan="2" style="background-color:#00a9d6; color:#fff;line-height:normal;vertical-align:middle;"><span style="font-size:8pt;">*Please verify flight times with the airlines prior to departure</span></td>
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
									$BaggageSegment_ccheckin = "";
									$BaggageSegment_cabin = "";
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
                                        if ($seg_count != 1) {
                                            $non_stop = ($segment_details_k + 1) . ' Stop';
                                        }
                                        if (valid_array($segment_details_v) == true) {
                                            ?>
                                            <tr>
                                                <td style="background-color:#d9d9d9;border-left:1px solid #00a9d6; color:#555555"><span style="color:#006f8e">Flight <?php echo $segment_details_v['segment_indicator']; ?></span></td>
                                                <td style="background-color:#d9d9d9;color:#555555"><img width="15" style="vertical-align:middle" src="<?= FULL_SYSTEM_IMAGE_DIR . 'flight_up.png' ?>">&nbsp;<span style="vertical-align:middle">Departing</span></td>
                                                <td style="background-color:#d9d9d9;color:#555555"><img width="15" style="vertical-align:middle" src="<?= FULL_SYSTEM_IMAGE_DIR . 'flight_down.png' ?>">&nbsp;<span style="vertical-align:middle">Arriving</span></td>
                                                <td style="background-color:#d9d9d9;border-right:1px solid #00a9d6; color:#555555">&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td style="line-height: 13px;"><span><?= @$segment_details_v['airline_name'] ?><br><?php echo $segment_details_v['airline_code'] . ' - ' . $segment_details_v['flight_number']; ?><br>Cabin: <?php echo $booking_details['cabin_class']; ?></span></td>
                                                <td style="line-height: 13px;"><span><strong><?= @$segment_details_v['from_airport_name'] ?>(<?= @$segment_details_v['from_airport_code'] ?>)</strong><br><?php echo date("D, d M Y", strtotime($segment_details_v['departure_datetime'])) . ", " . date("h:i A", strtotime($segment_details_v['departure_datetime'])); ?><br><?php echo $origin_terminal; ?></span></td>
                                                <td style="line-height: 13px;"><span><strong><?= @$segment_details_v['to_airport_name'] ?>(<?= @$segment_details_v['to_airport_code'] ?>)</strong><br><?php echo date("D, d M Y", strtotime($segment_details_v['arrival_datetime'])) . ", " . date("h:i A", strtotime($segment_details_v['arrival_datetime'])); ?><br><?php echo $destination_terminal; ?></span></td>
                                                <td style="line-height: 13px;"><span style="display:block;border-left: 1px solid #999; padding-left:10%"><?php echo $non_stop; ?><br><?php echo $segment_details_v['total_duration']; ?></span></td>
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
                <tr>
                    <td style="line-height:8px;padding:0;">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="4" style="padding:0;">
                        <table cellspacing="0" cellpadding="5" width="100%" style="border:1px solid #666666; font-size:9pt; padding:0;">
                            <tbody>
                                <tr>
                                    <td colspan="5" style="background-color:#666666; color:#fff"><img width="12" style="vertical-align:middle" src="<?= FULL_SYSTEM_IMAGE_DIR . 'people_group.png' ?>"> &nbsp;<span style="vertical-align:middle;font-size:10pt">Passenger(s) Details</span></td>
                                </tr>
                                <tr>
                                    <td style="background-color:#d9d9d9;border-left:1px solid #666666; color:#555555"><span>Sr No.</span></td>
                                    <td style="background-color:#d9d9d9; color:#555555"><span>Passenger(s) Name</span></td>
                                    <td style="background-color:#d9d9d9; color:#555555"><span>Type</span></td>
                                    <td style="background-color:#d9d9d9; color:#555555"><span>E-ticket No</span></td>
									<td style="background-color:#d9d9d9;border-right:1px solid #666666; color:#555555"><span>Status</span></td>
                                </tr>
                                <?php
                                $booking_transaction_details_value = $booking_transaction_details [1];
                                // debug($booking_transaction_details_value);exit;
                                if(isset($booking_transaction_details_value ['booking_customer_details'])) {
                                    foreach ($booking_transaction_details_value ['booking_customer_details'] as $cus_k => $cus_v) {
                                        if (strtolower($cus_v['passenger_type']) == 'infant') {
                                            $pass_name = $cus_v['first_name'] . '  ' . $cus_v['last_name'];
                                        } else {
                                            $pass_name = $cus_v['title'] . '.' . $cus_v['first_name'] . '  ' . $cus_v['last_name'];
                                        }
                                        ?>
                                        <tr>
                                            <td style="line-height: 13px;"><span><?php echo ($cus_k + 1); ?></span></td>
                                            <td style="line-height: 13px;"><span><strong><?php echo $pass_name; ?></strong></span></td>
                                            <td style="line-height: 13px;"><span><?php echo ucfirst($cus_v['passenger_type']) ?></span></td>
                                            <?php 
                                            if(@$cus_v['TicketNumber'] != @$cus_v['TicketId']){
                                            ?> <td style="line-height: 13px;"><span><?= @$cus_v['TicketNumber']; ?></span></td> <?php  
                                            }else{
                                            ?> <td style="line-height: 13px; padding-left: 5%;"><span>-</span></td> <?php    
                                            }
                                            ?>  
											<td style="line-height: 13px;"><span><?= (@$cus_v['status'])?'CONFIRMED':'CANCELLED'; ?></span></td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="line-height:4px;padding:0;">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="4" style="padding:0;">
                        <table cellspacing="0" cellpadding="5" width="100%" style="font-size:9pt; padding:0;">
                            <tbody>
                                <?php if (isset($booking_transaction_details_value['extra_service_details']) == true && valid_array($booking_transaction_details_value['extra_service_details']) == true) { ?>
                                    <tr>
                                        <td style="padding:10px 0 0 0;vertical-align:top">
                                            <table cellspacing="0" cellpadding="5" width="100%" style="border:1px solid #9a9a9a;font-size:9pt; padding:0;">
                                                <tbody>
                                                    <tr>
                                                        <td colspan="4" style="background-color:#666666; color:#fff;"><span style="font-size:10pt">Flight Extra Information</span></td>
                                                    </tr>
        <?php
        if (isset($booking_transaction_details_value['extra_service_details']['baggage_details']) == true && valid_array($booking_transaction_details_value['extra_service_details']['baggage_details']) == true) {
            $baggage_details = $booking_transaction_details_value['extra_service_details']['baggage_details'];
            foreach ($baggage_details['baggage_source_destination_label'] as $bag_lk => $bag_lv) {
                ?>
                                                            <tr>
                                                                <td colspan="4" style="background-color:#d9d9d9; color:#555555"><span><?php echo 'Extra Baggage Information'; ?> ( <?= $bag_lv ?> )</span></td>
                                                            </tr>
                <?php
                foreach ($baggage_details['details'] as $bag_k => $bag_v) {
                    foreach ($bag_v as $bd_k => $bd_v) {
                        // debug($bag_lv);exit;
                        if ($bd_v['from_airport_code'] . '-' . $bd_v['to_airport_code'] == $bag_lv) {
                            ?>
                                                                        <tr>
                                                                            <td colspan="4" style="border-left:none;border-right:1px solid #fff"><?= $bd_v['description'] ?> ( <?= $bag_v[0]['pax_name'] ?> )</td>
                                                                        </tr>
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
                                                            <tr>
                                                                <td colspan="4" style="background-color:#d9d9d9; color:#555555"><span><?php echo $meal_type_label; ?> ( <?= $meal_lv ?> )</span></td>
                                                            </tr>
                                                            <?php
                                                            foreach ($meal_details['details'] as $meal_k => $meal_v) {
                                                                foreach ($meal_v as $md_k => $md_v) {

                                                                    if ($md_v['from_airport_code'] . '-' . $md_v['to_airport_code'] == $meal_lv) {
                                                                        ?>
                                                                        <tr>
                                                                            <td colspan="4" style="border-left:none;border-right:1px solid #fff"><?= $md_v['description'] ?> ( <?= $meal_v[0]['pax_name'] ?> )</td>
                                                                        </tr>
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
                                                            <tr>
                                                                <td colspan="4" style="background-color:#d9d9d9; color:#555555"><span><?php echo $seat_type_label; ?> ( <?= $seat_lv ?> )</span></td>
                                                            </tr>
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
                                                                        <tr>
                                                                            <td colspan="4"  style="border-left:none;border-right:1px solid #fff; border-bottom:none"><?= $seat_description ?> ( <?= $seat_v[0]['pax_name'] ?> )</td>
                                                                        </tr>
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
                <tr>
                    <td style="line-height:3px;padding:0;">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="4" style="padding-bottom:15px;line-height:30px;border-bottom:1px solid #999999;text-align:left"><span style="font-size:10pt; color:#555;">Customer Contact Details | E-mail : <?= $booking_details['email'] ?> | Contact No : <?= $booking_details['phone'] ?></span></td>
                </tr>
                <tr>
                    <td colspan="4" ><span style="line-height:23px; font-size:10pt;">Important Information</span></td>
                </tr>
                <tr>
                    <td colspan="4" style="font-size:9pt; color:#555">
                    <?php echo $data['terms_conditions']; ?>
                    </td>
                </tr>
                <tr><td colspan="4" style="line-height:8px;padding:0;border-bottom:1px solid #999999;">&nbsp;</td></tr>
                <tr><td colspan="4" style="line-height:8px;padding:0;">&nbsp;</td></tr>
            </tbody>
        </table>
    </div>
<?php } ?>
