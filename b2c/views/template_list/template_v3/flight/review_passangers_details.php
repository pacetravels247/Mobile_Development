<?php 
$current_month = date('M');
$current_month_value = date('m');
$current_day = date('d');
$current_year = date('Y');
//error_reporting(E_ALL);
$booking_details = $data ['booking_details'] [0];
$itinerary_details = $booking_details ['booking_itinerary_details'];
  // debug($itinerary_details);exit;
$attributes = $booking_details ['attributes'];
$customer_details = $booking_details ['booking_transaction_details'][0]['booking_customer_details'];
$domain_details = $booking_details;
$lead_pax_details = $customer_details;
$booking_transaction_details = $booking_details ['booking_transaction_details'];
$adult_count = 0;
$infant_count = 0;

foreach ( $customer_details as $k => $v ) {
  if (strtolower ( $v ['passenger_type'] ) == 'infant') {
    $infant_count ++;
  } else {
    $adult_count ++;
  }
}

$Onward = '';
$return = '';
if (count ( $booking_transaction_details ) == 2) {
  $Onward = 'Onward ';
  $Return = 'Return ';
}

// generate onword and return
if ($booking_details ['is_domestic'] == true && count ( $booking_transaction_details ) == 2) {
  $onward_segment_details = array ();
  $return_segment_details = array ();
  $segment_indicator_arr = array ();
  $segment_indicator_sort = array ();
  
  foreach ( $itinerary_details as $key => $key_sort_data ) {
    $segment_indicator_sort [$key] = $key_sort_data ['origin'];
  }
  array_multisort ( $segment_indicator_sort, SORT_ASC, $itinerary_details );

  foreach ( $itinerary_details as $k => $sub_details ) {
    $segment_indicator_arr [] = $sub_details ['segment_indicator'];
    $count_value = array_count_values ( $segment_indicator_arr );
    
    if ($count_value [1] == 1) {
      $onward_segment_details [] = $sub_details;
    } else {
      $return_segment_details [] = $sub_details;
    }
  }
}
// debug($onward_segment_details);exit;
if(isset($onward_segment_details[0]['airline_pnr']) && !empty($itinerary_details[0]['airline_pnr'])){
  $airline_pnr = $itinerary_details[0]['airline_pnr'];
  $gds_pnr = $booking_transaction_details[0]['pnr'];
}
else if(!empty($booking_transaction_details[0]['pnr'])){
  $airline_pnr = $booking_transaction_details[0]['pnr'];
  $gds_pnr = $booking_transaction_details[0]['pnr'];
}
else{
  $airline_pnr = $booking_transaction_details[0]['book_id'];
  $gds_pnr = $booking_transaction_details[0]['book_id'];
}
if(isset($return_segment_details)){
  // debug($booking_transaction_details);exit;
  if(isset($return_segment_details[0]['airline_pnr']) && !empty($return_segment_details[0]['airline_pnr'])){
    $return_airline_pnr = $return_segment_details[0]['airline_pnr'];
    $return_gds_pnr = $booking_transaction_details[1]['pnr'];
  }
  elseif(!empty($booking_transaction_details[1]['pnr'])){
    $return_airline_pnr = $booking_transaction_details[1]['pnr'];
    $return_gds_pnr = $booking_transaction_details[1]['pnr'];
  }
  else{
    $return_airline_pnr = $booking_transaction_details[1]['book_id'];
    $return_gds_pnr = $booking_transaction_details[1]['book_id'];
  }
  $retur_fare_details = json_decode($booking_transaction_details[1]['attributes'],True);

}

$fare_details = json_decode($booking_transaction_details[0]['attributes'],True);

$BaseFare = $fare_details['Fare']['BaseFare']+@$retur_fare_details['Fare']['BaseFare'];
$Tax = $fare_details['Fare']['Tax']+@$retur_fare_details['Fare']['Tax'];
$GST = $booking_transaction_details[0]['gst']+@$booking_transaction_details[1]['gst'];

$booking_transaction_details_value = $booking_transaction_details [0];
$baggage_price = 0;
$meal_price = 0;
$seat_price = 0;
// debug($booking_transaction_details_value);exit;
if(isset($booking_transaction_details_value['extra_service_details']['baggage_details']) == true && valid_array($booking_transaction_details_value['extra_service_details']['baggage_details']) == true){
  $baggage_details = $booking_transaction_details_value['extra_service_details']['baggage_details'];
  foreach ($baggage_details['details'] as $bag_k => $bag_v){
    foreach ($bag_v as $bd_k => $bd_v){ 
      $baggage_price += $bd_v['price'];
    }
  }
}
if(isset($booking_transaction_details_value['extra_service_details']['meal_details']) == true && valid_array($booking_transaction_details_value['extra_service_details']['meal_details']) == true){
  // debug($booking_transaction_details_value);exit;
  $meal_details = $booking_transaction_details_value['extra_service_details']['meal_details'];
  foreach ($meal_details['details'] as $meal_k => $meal_v){
    foreach ($meal_v as $md_k => $md_v){
      $meal_price += $md_v['price'];
    }
  }
}
if(isset($booking_transaction_details_value['extra_service_details']['seat_details']) == true && valid_array($booking_transaction_details_value['extra_service_details']['seat_details']) == true){
  $seat_details = $booking_transaction_details_value['extra_service_details']['seat_details'];
  foreach ($seat_details['details'] as $seat_k => $seat_v){
    foreach ($seat_v as $sd_k => $sd_v){
      // debug($seat_v);exit;
      $seat_price += $sd_v['price'];
    }
  }
}
if ($booking_details ['is_domestic'] == true && count ( $booking_transaction_details ) == 2) {
$booking_transaction_details_value = $booking_transaction_details [1];
if(isset($booking_transaction_details_value['extra_service_details']['baggage_details']) == true && valid_array($booking_transaction_details_value['extra_service_details']['baggage_details']) == true){
  $baggage_details = $booking_transaction_details_value['extra_service_details']['baggage_details'];
  foreach ($baggage_details['details'] as $bag_k => $bag_v){
    foreach ($bag_v as $bd_k => $bd_v){ 
      $baggage_price += $bd_v['price'];
    }
  }
}
if(isset($booking_transaction_details_value['extra_service_details']['meal_details']) == true && valid_array($booking_transaction_details_value['extra_service_details']['meal_details']) == true){
  // debug($booking_transaction_details_value);exit;
  $meal_details = $booking_transaction_details_value['extra_service_details']['meal_details'];
  foreach ($meal_details['details'] as $meal_k => $meal_v){
    foreach ($meal_v as $md_k => $md_v){
      $meal_price += $md_v['price'];
    }
  }
}
if(isset($booking_transaction_details_value['extra_service_details']['seat_details']) == true && valid_array($booking_transaction_details_value['extra_service_details']['seat_details']) == true){
  $seat_details = $booking_transaction_details_value['extra_service_details']['seat_details'];
  foreach ($seat_details['details'] as $seat_k => $seat_v){
    foreach ($seat_v as $sd_k => $sd_v){
      // debug($seat_v);exit;
      $seat_price += $sd_v['price'];
    }
  }
}
}

$airline_contact_no = $this->custom_db->single_table_records ( 'airline_contact_numbers','*',array('airline_code'=>$itinerary_details[0]['airline_code']));
// debug($airline_contact_no);exit;
if(isset($airline_contact_no['data'][0])){
  $airline_number = '<span><img style="vertical-align:middle" src='.SYSTEM_IMAGE_DIR.'phone.png /><span style="font-size:16px;color:#00a9d6;vertical-align:middle;font-weight: 600;"> &nbsp;'.$airline_contact_no['data'][0]['phone_number'].'</span></span> ';
}
else{
  $airline_number ='';
}
if($booking_transaction_details[0] ['status'] == 'BOOKING_CONFIRMED'){
  $border = 'border-top:2px solid #808080;';
}
else if($booking_transaction_details[0]
  ['status'] == 'BOOKING_HOLD'){
  $border = '';
}
$trip_type = $booking_details['trip_type'];
$currency_conversion_rate = $booking_details['currency_conversion_rate'];

$days = get_day_numbers();
$months = get_month_names();
$current_day_key = (array_search((int)$current_day, $days));
$current_month_key = array_search(strtolower($current_month), array_map('strtolower', $months));

// echo $meal_price;exit;
// /debug($fare_details);exit;
?>
<style type="text/css">
  td, th {padding: 5px;}
  @media print { 
    header, footer, #show_log { display: none; }
    .pag_brk { page-break-before: always; }
  }
  .form-group {
    overflow: auto;
  }
  .cancel_btn {
    padding: 11px 0;
    border-radius: 0;
  }
  .custm-btn {
    width: initial;
    line-height: initial;
    padding: 15px;
    margin: 0 5px;
  }
  .modal-header button.close {
    border: 1px solid red;
    padding: 0 6px;
    color: #fff;
    background: red;
    opacity: initial;
  }
  .required {
    color: red;
  }
</style>
<script type="text/javascript">
  $(document).on("change", "#expiry-year", function() {
    var curr_year = new Date().getFullYear();
    var sel_year = $(this).val();
    var sel_day = $("#expiry_day").val();
    var sel_month = $("#expiry_month").val();
    var days = <?= json_encode(get_day_numbers()) ?>;
    var months = <?= json_encode(get_month_names()) ?>;
    var d_html = "";
    var m_html = "";
    if (curr_year != sel_year) {
      var current_day_key = 0;
      var current_month_key = 0;
    } else {
      var current_day_key = <?= $current_day_key ?>;
      var current_month_key = <?= $current_month_key ?>;
    }
    $.each(days, function( i, l ){
      if (current_day_key < i)
        d_html += `<option value = '${l}' > ${l} </option>`
    });
    $.each( months, function( i_m, l_m ){
      if (current_month_key <= i_m)
        m_html += `<option value = '${i_m + 1}' > ${l_m} </option>`
    });

    $("#expiry_day").html(d_html);
    $("#expiry_month").html(m_html);

    if (sel_day) {
      $("#expiry_day").val(sel_day);
    }
    if (sel_month != "") {
      $("#expiry_month").val(sel_month);
    }
  })
</script>
<div id="editPax" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit</h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="<?= base_url() ?>index.php/flight/edit_pax" class="form-horizontal">
        </form>
      </div>
    </div>
  </div>
</div>
<div style="background:#fff; position:relative;" class="container table-responsive">
<table cellpadding="0" border-collapse cellspacing="0" width="100%" style="font-size:12px; font-family: 'Open Sans', sans-serif; margin:0px auto;background-color:#fff; padding:15px 15px;border-collapse:separate; color: #000; border: 1px solid #ad9d9d;
    margin: 10px 0;">
<tbody>
  <tr>
   <!--  <td colspan="2" style="padding-bottom:10px"><img style="width: 100px;background: #073c7d;padding: 10px;" src="<?='http://'.$_SERVER['HTTP_HOST'].$GLOBALS['CI']->template->domain_images($booking_details['domain_logo'])?>" /></td> -->
    <!-- <td colspan="2" style="padding-bottom:10px" align="right"><span>Booking ID : <?=@$booking_details['app_reference']?></span><br><span>Booked on : <?=app_friendly_absolute_date(@$booking_details['booked_date'])?></span></td> -->
  </tr>
  <tr>
  <!-- <td align="right" colspan="4" style="line-height:26px; border-top:1px solid #00a9d6; border-bottom:1px solid #00a9d6;"><span style="font-size:12px;">Status: </span><strong class="<?php echo booking_status_label($booking_transaction_details[0]['status'])?>" style=" font-size:14px;">
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
  
  ?></strong></td> -->
  </tr>
  <?php if ($booking_details ['is_domestic'] == true && count ( $booking_transaction_details ) == 2) { ?>
  <tr>
    <td colspan="4" style="font-size: 18px;font-weight: 600;text-align: center;padding: 10px 0 0;">E-Ticket <?php echo $Onward;?></td>
  </tr>
  <?php } ?>
  <tr>
    <!-- <td style="padding:10px 0"><img style="width:60px;" src="<?='http://'.$_SERVER['HTTP_HOST'].SYSTEM_IMAGE_DIR.'airline_logo/'.$itinerary_details[0]['airline_code'].'.gif'?>" /></td> --><!-- 
    <td style="padding:10px 0; line-height:25px;"><span style="display: block;border-right: 1px solid #999;"><span style="font-size:14px;"><?=@$itinerary_details[0]['airline_name']?></span><br><?php echo $airline_number ?></span></td> -->

    <!-- <td style="padding:10px 0; padding-left: 10%; line-height:25px;"><span style="font-size:14px;">Agency</span><br><span><img style="vertical-align:middle" src="<?='http://'.$_SERVER['HTTP_HOST'].SYSTEM_IMAGE_DIR.'phone.png'?>" /> <span style="font-size:16px;color:#00a9d6;vertical-align:middle;font-weight: 600;"> &nbsp;<?php echo $data['phone']?></span></span></td> -->
    <?php if((!empty($gds_pnr)) || (!empty($airline_pnr))){?>
    <td style="padding:10px 0;text-align: center;">
    <span style="font-size:14px; border:2px solid #808080; display:block">
    <!-- <?php if($booking_transaction_details[0] ['status'] == 'BOOKING_CONFIRMED') { ?>
    <span style="color:#00a9d6;padding:5px; display:block">AIRLINE PNR</span>
    <span style="font-size:26px;line-height:35px;padding-bottom: 5px;display:block;font-weight: 600;"><?=@$airline_pnr?></span>
    <?php } ?> -->
  <!--   <?php if(($booking_transaction_details[0] ['status'] == 'BOOKING_CONFIRMED' || $booking_transaction_details[0] ['status'] == 'BOOKING_HOLD') && !empty($gds_pnr)) { ?>
    <span style="<?php echo $border;?>display:block; padding:5px;">GDS PNR:  <?=@$gds_pnr?></span>
    <?php } ?> -->
    </span></td>
    <?php } ?>
  </tr>
  <tr>
  <tr>
    <td colspan="4" style="border:1px solid #003f6a; padding:0;">
      <table cellspacing="0" cellpadding="5" width="100%" style="font-size:14px; padding:0;">
        <tbody>
          <tr>
            <td colspan="2" style="background-color:#003f6a; color:#fff"><img style="vertical-align:middle" src="<?='http://'.$_SERVER['HTTP_HOST'].SYSTEM_IMAGE_DIR.'flight.png'?>"> &nbsp;<span style="vertical-align:middle;font-size:15px">Onward Flight Details</span></td>
            <td align="right" colspan="2" style="background-color:#003f6a; color:#fff"><span style="font-size:10px"></span></td>
          </tr>
          <?php 

          if (isset ( $booking_transaction_details ) && $booking_transaction_details != "") {
              if ($booking_details ['is_domestic'] == true && count ( $booking_transaction_details ) == 2) {
                $itinerary_details = array ();
                $itinerary_details = $onward_segment_details;
              }
              // debug($itinerary_details);exit;
              $checkin_baggage = 0;
              $cabin_baggage = 0;
              $seg_count = count($itinerary_details);
              if($seg_count == 1){
                $non_stop = 'Non Stop';
              }
              else{
                $non_stop ='';
              }
              $seg_in_array = array();
              $seg_array = array();
              $seg_counts = 0;
              foreach ( $itinerary_details as $segment_details_k => $segment_details_v ) { 
                // echo $trip_type;
                $seg_array [] = $segment_details_v['segment_indicator'];
                if(in_array($segment_details_v['segment_indicator'], $seg_in_array) && !empty($seg_in_array) && ($seg_counts == 0) && ($trip_type != 'multicity')){ 
                  // echo $seg_count;
                  $seg_counts = $seg_counts+1;

                    // debug($seg_in_array);
                  ?>
                <tr>
                  <td colspan="2" style="background-color:#003f6a; color:#fff"><img style="vertical-align:middle" src="<?='http://'.$_SERVER['HTTP_HOST'].SYSTEM_IMAGE_DIR.'flight.png'?>"> &nbsp;<span style="vertical-align:middle;font-size:14px">Retun Flight Details</span></td>
                  <td align="right" colspan="2" style="background-color:#003f6a; color:#fff"><span style="font-size:10px">*Please verify flight times with the airlines prior to departure</span></td>
                </tr>
          
              <?php }
                
                $itinerary_details_attributes = json_decode ( $segment_details_v ['attributes'], true);
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
                $checkin_baggage += (int) $segment_details_v['checkin_baggage'];
                $cabin_baggage += (int) $segment_details_v['cabin_baggage'];  
                if($seg_count != 1 && $trip_type != 'multicity'){
                  
                  if (count(array_unique($seg_array)) == 1 && end($seg_array) == 1 && $seg_count == 2 && $trip_type !='oneway') {
                    
                    $non_stop = 'Non Stop';
                  }
                  else{
                    $non_stop = $segment_details_v['segment_indicator'].' Stop';
                  }
                }
                else if($seg_count != 1){
                  $non_stop = ($segment_details_k+1).' Stop';
                }
                if($trip_type == 'multicity'){
                  $fight_count = ($segment_details_k+1);
                }
                else{
                  $fight_count = $segment_details_v['segment_indicator'];
                }
                $seg_in_array[] = $segment_details_v['segment_indicator'];
              if (valid_array ( $segment_details_v ) == true) {
            ?>
          <tr>
            <td style="background-color:#d9d9d9; color:#555555"><span style="color:#003f6a">Flight <?php echo $fight_count; ?></span></td>
            <td style="background-color:#d9d9d9; color:#555555"><img style="vertical-align:middle" src="<?='http://'.$_SERVER['HTTP_HOST'].SYSTEM_IMAGE_DIR.'flight_up.png'?>">&nbsp;<span style="vertical-align:middle">Departing</span></td>
            <td style="background-color:#d9d9d9; color:#555555"><img style="vertical-align:middle" src="<?='http://'.$_SERVER['HTTP_HOST'].SYSTEM_IMAGE_DIR.'flight_down.png'?>">&nbsp;<span style="vertical-align:middle">Arriving</span></td>
            <td style="background-color:#d9d9d9; color:#555555">&nbsp;</td>
          </tr>
          <tr>
            <?php
            $passport_minimum_expiry_date = date('Y-m-d', strtotime($segment_details_v['departure_datetime']));
            $cutoff = date('Y', strtotime('+20 years', strtotime($passport_minimum_expiry_date)));
            // echo $cutoff;exit;
            //current year
            ?>
            <td><span><?=@$segment_details_v['airline_name']?><br><?php echo $segment_details_v['airline_code'].' - '.$segment_details_v['flight_number'];?><br>Cabin: <?php echo $booking_details['cabin_class']; ?></span></td>
            <td><span><strong><?=@$segment_details_v['from_airport_name'] ?>(<?=@$segment_details_v['from_airport_code']?>)</strong><br><?php echo date("D, d M Y",strtotime($segment_details_v['departure_datetime'])).", ".date("h:i A",strtotime($segment_details_v['departure_datetime']));?><br><?php echo $origin_terminal;?></span></td>
            <td><span><strong><?=@$segment_details_v['to_airport_name']?>(<?=@$segment_details_v['to_airport_code']?>)</strong><br><?php echo date("D, d M Y",strtotime($segment_details_v['arrival_datetime'])).", ".date("h:i A",strtotime($segment_details_v['arrival_datetime']));?><br><?php echo $destination_terminal;?></span></td>
            <td><span style="display:block;border-left: 1px solid #999; padding-left:10%"><?php echo $non_stop; ?><br><?php echo $segment_details_v['total_duration'];?><br><?php echo $segment_details_v['is_refundable']?></span></td>
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
  <?php if(count($booking_transaction_details) == 2) { ?>
    <tr>
      <td colspan="4" style="border:1px solid #003f6a; padding:0;">
        <table cellspacing="0" cellpadding="5" width="100%" style="font-size:14px; padding:0;">
          <tbody>
            <tr>
              <td colspan="2" style="background-color:#003f6a; color:#fff"><img style="vertical-align:middle" src="<?='http://'.$_SERVER['HTTP_HOST'].SYSTEM_IMAGE_DIR.'flight.png'?>"> &nbsp;<span style="vertical-align:middle;font-size:15px">Return Flight Details</span></td>
              <td align="right" colspan="2" style="background-color:#003f6a; color:#fff"><span style="font-size:10px"></span></td>
            </tr>
            <?php

            if (isset ( $booking_transaction_details ) && $booking_transaction_details != "") {
                if ($booking_details ['is_domestic'] == true && count ( $booking_transaction_details ) == 2) {
                  $itinerary_details = array ();
                  $itinerary_details = $onward_segment_details;
                }
                $checkin_baggage = 0;
                $cabin_baggage = 0;
                $seg_count = count($itinerary_details);
                if($seg_count == 1){
                  $non_stop = 'Non Stop';
                }
                else{
                  $non_stop ='';
                }
                $seg_in_array = array();
                $seg_array = array();
                $seg_counts = 0;
                foreach ( $return_segment_details as $segment_details_k => $segment_details_v ) { 
                  // echo $trip_type;
                  $seg_array [] = $segment_details_v['segment_indicator'];
                  if(in_array($segment_details_v['segment_indicator'], $seg_in_array) && !empty($seg_in_array) && ($seg_counts == 0) && ($trip_type != 'multicity')){ 
                    // echo $seg_count;
                    $seg_counts = $seg_counts+1;

                      // debug($seg_in_array);
                    ?>
                  <tr>
                    <td colspan="2" style="background-color:#003f6a; color:#fff"><img style="vertical-align:middle" src="<?='http://'.$_SERVER['HTTP_HOST'].SYSTEM_IMAGE_DIR.'flight.png'?>"> &nbsp;<span style="vertical-align:middle;font-size:14px">Retun Flight Details</span></td>
                    <td align="right" colspan="2" style="background-color:#003f6a; color:#fff"><span style="font-size:10px">*Please verify flight times with the airlines prior to departure</span></td>
                  </tr>
            
                <?php }
                  
                  $itinerary_details_attributes = json_decode ( $segment_details_v ['attributes'], true);
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
                  $checkin_baggage += (int) $segment_details_v['checkin_baggage'];
                  $cabin_baggage += (int) $segment_details_v['cabin_baggage'];  
                  if($seg_count != 1 && $trip_type != 'multicity'){
                    
                    if (count(array_unique($seg_array)) == 1 && end($seg_array) == 1 && $seg_count == 2 && $trip_type !='oneway') {
                      
                      $non_stop = 'Non Stop';
                    }
                    else{
                      $non_stop = $segment_details_v['segment_indicator'].' Stop';
                    }
                  }
                  else if($seg_count != 1){
                    $non_stop = ($segment_details_k+1).' Stop';
                  }
                  if($trip_type == 'multicity'){
                    $fight_count = ($segment_details_k+1);
                  }
                  else{
                    $fight_count = $segment_details_v['segment_indicator'];
                  }
                  $seg_in_array[] = $segment_details_v['segment_indicator'];
                if (valid_array ( $segment_details_v ) == true) {
              ?>
            <tr>
              <td style="background-color:#d9d9d9; color:#555555"><span style="color:#003f6a">Flight <?php echo $fight_count; ?></span></td>
              <td style="background-color:#d9d9d9; color:#555555"><img style="vertical-align:middle" src="<?='http://'.$_SERVER['HTTP_HOST'].SYSTEM_IMAGE_DIR.'flight_up.png'?>">&nbsp;<span style="vertical-align:middle">Departing</span></td>
              <td style="background-color:#d9d9d9; color:#555555"><img style="vertical-align:middle" src="<?='http://'.$_SERVER['HTTP_HOST'].SYSTEM_IMAGE_DIR.'flight_down.png'?>">&nbsp;<span style="vertical-align:middle">Arriving</span></td>
              <td style="background-color:#d9d9d9; color:#555555">&nbsp;</td>
            </tr>
            <tr>
              <?php
              $passport_minimum_expiry_date = date('Y-m-d', strtotime($segment_details_v['departure_datetime']));
              $cutoff = date('Y', strtotime('+20 years', strtotime($passport_minimum_expiry_date)));
              // echo $cutoff;exit;
              //current year
              ?>
              <td><span><?=@$segment_details_v['airline_name']?><br><?php echo $segment_details_v['airline_code'].' - '.$segment_details_v['flight_number'];?><br>Cabin: <?php echo $booking_details['cabin_class']; ?></span></td>
              <td><span><strong><?=@$segment_details_v['from_airport_name'] ?>(<?=@$segment_details_v['from_airport_code']?>)</strong><br><?php echo date("D, d M Y",strtotime($segment_details_v['departure_datetime'])).", ".date("h:i A",strtotime($segment_details_v['departure_datetime']));?><br><?php echo $origin_terminal;?></span></td>
              <td><span><strong><?=@$segment_details_v['to_airport_name']?>(<?=@$segment_details_v['to_airport_code']?>)</strong><br><?php echo date("D, d M Y",strtotime($segment_details_v['arrival_datetime'])).", ".date("h:i A",strtotime($segment_details_v['arrival_datetime']));?><br><?php echo $destination_terminal;?></span></td>
              <td><span style="display:block;border-left: 1px solid #999; padding-left:10%"><?php echo $non_stop; ?><br><?php echo $segment_details_v['total_duration'];?><br><?php echo $segment_details_v['is_refundable']?></span></td>
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
  <?php } ?>
  <tr><td style="line-height:15px;padding:0;">&nbsp;</td></tr>
  <tr>
    <td colspan="4" style="border:1px solid #003f6a; padding:0;">
      <table cellspacing="0" cellpadding="5" width="100%" style="font-size:14px; padding:0;">
        <tbody>
          <tr>
            <td colspan="10" style="background-color:#003f6a; color:#fff"><img style="vertical-align:middle" src="<?='http://'.$_SERVER['HTTP_HOST'].SYSTEM_IMAGE_DIR.'people_group.png'?>"> &nbsp;<span style="vertical-align:middle;font-size:15px">Passenger(s) Details</span></td>
          </tr>

          <tr>
            <td style="background-color:#d9d9d9; color:#555555"><span>Sr No.</span></td>
            <td style="background-color:#d9d9d9; color:#555555"><span>Passenger(s) Name</span></td>
            <?php if (!$booking_details["is_domestic"]) {?>
              <td style="background-color:#d9d9d9; color:#555555"><span>Passport Number</span></td>
            <?php } ?>
            <td style="background-color:#d9d9d9; color:#555555"><span>Type</span></td>
            <td style="background-color:#d9d9d9; color:#555555"><span>Edit</span></td>
          </tr>
          <?php
          $booking_transaction_details_value = $booking_transaction_details [0];
          // debug($booking_transaction_details);exit;
          if (isset ( $booking_transaction_details_value ['booking_customer_details'] )) {
            foreach ( $booking_transaction_details_value ['booking_customer_details'] as $cus_k => $cus_v ) {
              $pax_type = $cus_v['passenger_type'];
              if (strtolower($cus_v['passenger_type']) == 'infant') { 
                $pass_name = $cus_v['first_name'].'  '.$cus_v['last_name'];
              } else {
                $pass_name = $cus_v['first_name'].'  '.$cus_v['last_name'];
              }
            ?>
          <tr>
            <td><span><?php echo ($cus_k+1);?></span></td>
            <td><span><strong><?php echo $pass_name;?></strong></span></td>
            <?php if (!$booking_details["is_domestic"]) {?>
              <td><span> <?= $cus_v['passport_number'] ?></span></td>
            <?php } ?>
            <td><span><?php echo ucfirst($cus_v['passenger_type'])?></span></td>
            <td><button type="button" class="edit_pax" data-id="<?= $cus_v['origin'] ?>" data-toggle="modal" data-target="#editPax"><i class="fa fa-pencil"></i></button>
              <div class="hidden edit_pax_html">
                <input type="hidden" name="is_domestic" value="<?= $booking_details ['is_domestic'] ?>">
                <input type="hidden" name="origin" value="<?= $cus_v['origin'] ?>">
                <input type="hidden" name="app_reference" value="<?= $app_reference ?>">
                <div class="form-group">
                  <label class="control-label col-md-4" for="first_name">Passenger First Name<span class="required"> *</span></label>
                  <div class="col-md-5">
                    <input class="code form-control" required="required" type="text" name="first_name" value="<?= $cus_v['first_name'] ?>" />
                  </div>
                </div>
                <div class="form-group">
                  <label class="control-label col-md-4" for="last_name">Passenger Last Name<span class="required"> *</span></label>
                  <div class="col-md-5">
                    <input class="code form-control" required="required" type="text" name="last_name" value="<?= $cus_v['last_name'] ?>" />
                  </div>
                </div>
                <div class="form-group">
                  <label class="control-label col-md-4" for="first_name">Dob<span class="required"> *</span></label>
                  <div class="col-md-5">
                    <?php
                      $dob_id = "";
                      switch (strtolower($pax_type)) {
                        case 'adult':
                          $dob_id = "adult-date-picker-1";
                          break;
                        case 'child':
                          $dob_id = "child-date-picker-1";
                          break;
                        case 'infant':
                          $dob_id = "infant-date-picker-1";
                          break;
                       } ?>
                    <input class="code form-control" required="required" type="text" name="date_of_birth" value="<?= $cus_v['date_of_birth'] ?>" id = "<?= $dob_id ?>"/>
                  </div>
                </div>
                <?php
                  $issue_country_origin = array_search($cus_v['passport_issuing_country'], $country_list);
                  $expiry_date = explode('-', $cus_v['passport_expiry_date']);
                  $passport_issuing_country_options = generate_options($country_list, $issue_country_origin);
                  $now = date('Y', strtotime($passport_minimum_expiry_date));
                  if ($booking_details ['is_domestic'] != true) { ?>
                    <div class="form-group">
                      <label class="control-label col-md-4" for="last_name">Passport Number<span class="required"> *</span></label>
                      <div class="col-md-5">
                        <input class="code form-control" required="required" type="text" name="passport_number" value="<?= $cus_v['passport_number'] ?>" />
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="control-label col-md-4" for="last_name">Passport Issuing Country<span class="required"> *</span></label>
                      <div class="col-md-5">
                        <select required="required" name="passenger_passport_issuing_country" class="form-control">
                          <option value="INVALIDIP">Please Select</option>
                          <?php
                          foreach ($country_list as $key => $country) { ?>
                            <option value="<?= $key ?>" <?= ($key == $issue_country_origin) ? "selected='selected'" : "" ?>><?= $country ?></option>
                          <?php  }
                          ?>
                        </select>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="control-label col-md-4" for="last_name">Passport expiry<span class="required"> *</span></label>
                      <div class="col-md-2">
                        <select required="required" name="date[2]" id = "expiry_day" class="form-control">
                          <option value="">DD</option>
                          <?php

                            foreach (get_day_numbers() as $key => $value) {
                            if ($current_year == $expiry_date[0]) {
                              if ($current_month_value == $expiry_date[1]) {
                                if ($current_day_key < $key) { ?>
                                  <option value="<?= $key ?>" <?= ($key == $expiry_date[2]) ? "selected='selected'" : "" ?>><?= $value ?></option>
                                  
                            <?php } } else { ?>
                              <option value="<?= $key ?>" <?= ($key == $expiry_date[2]) ? "selected='selected'" : "" ?>><?= $value ?></option>
                            <?php } } else { ?>
                                <option value="<?= $key ?>" <?= ($key == $expiry_date[2]) ? "selected='selected'" : "" ?>><?= $value ?></option>
                            <?php } } ?>
                        </select>
                      </div>
                      <div class="col-md-2" style="padding: 0 5px">
                        <select required="required" name="date[1]" id = "expiry_month" class="form-control">
                          <option value="">MM</option>
                            <?php
                            foreach ($months as $key => $value) {
                                if ($current_year == $expiry_date[0]) {
                                  if ($current_month_key <= $key) { ?>
                                <option value="<?= $key + 1 ?>" <?= ($key + 1 == $expiry_date[1]) ? "selected='selected'" : "" ?>><?= $value ?></option>
                            <?php } } else { ?>
                              <option value="<?= $key + 1 ?>" <?= ($key + 1 == $expiry_date[1]) ? "selected='selected'" : "" ?>><?= $value ?></option>
                            <?php } } ?>
                        </select>
                      </div>
                      <div class="col-md-3">
                        <select required="required" name="date[0]" id="expiry-year" class="form-control">
                          <option value="">YYYY</option>
                          <?php
                            foreach (get_years($now, $cutoff) as $key => $value) {?>
                              <option value="<?= $key ?>" <?= ($key == $expiry_date[0]) ? "selected='selected'" : "" ?>><?= $value ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>
                <?php } ?>
                <div class="form-group">
                  <label class="control-label col-md-5"></label>
                  <div class="col-md-5">
                    <input class="btn btn-primary" type="submit"/>
                  </div>
                </div>
              </div>
            </td>
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
      <table cellspacing="0" cellpadding="5" width="100%" style="font-size:14px; padding:0;">
        <tbody>
          <tr>
            <td width="100%" style="padding:0;">
              <table cellspacing="0" cellpadding="5" width="100%" style="font-size:14px; padding:0;border:1px solid #003f6a;">
                <tbody>
                  <tr>
                    <td style="background:#003f6a;border-bottom:1px solid #ccc"><span style="font-size:14px;font-size:15px;color: #fff;">Payment Details</span></td>
                    <td style="background:#003f6a;border-bottom:1px solid #ccc"><span style="font-size:14px;font-size:15px;color: #fff;">Amount ( <?php echo $booking_details['currency']?>
                    )</span></td>
                  </tr>
                  <?php 
                  // debug($booking_details);exit;
                  ?>
                  <tr>
                    <td><span>Air Fare</span></td>
                    <td><span> <?php echo number_format($currency_conversion_rate*$BaseFare, 2)?></span></td>
                  </tr>
                  
                  <tr>
                    <td><span>Taxes &amp; Fees</span></td>
                    <td><span> <?php echo number_format((($currency_conversion_rate*$Tax)+($booking_details['admin_markup'])+($currency_conversion_rate*$booking_details['convinence_amount'])-($booking_details['admin_commission'])+$booking_details['admin_tds']), 2)?></span></td>
                  </tr>
                  <?php if($GST > 0){?>
                  <tr>
                    <td><span>GST</span></td>
                    <td><span> <?php echo number_format(($currency_conversion_rate*$GST), 2)?></span></td>
                  </tr>
                  <?php } ?>
                  <?php if(isset($baggage_price) && $baggage_price !=0){?>
                  <tr>
                    <td><span>Cabin Baggage</span></td>
                    <td><span> <?php echo number_format($baggage_price, 2)?></span></td>
                  </tr>
                  <?php } ?>
                  <?php if(isset($meal_price) && $meal_price!=0){?>
                  <tr>
                    <td><span>Meals</span></td>
                    <td><span> <?php echo number_format($meal_price,2)?></span></td>
                  </tr>
                  <?php } ?>
                  <?php if(isset($seat_price) && $seat_price!=0){?>
                  <tr>
                    <td><span>Seat</span></td>
                    <td><span> <?php echo number_format($seat_price,2)?></span></td>
                  </tr>
                  <?php } if($booking_details['discount']!=0){ ?>
                  <tr>
                    <td><span>Discount (-)</span></td>
                    <td><span> <?php echo number_format($currency_conversion_rate*$booking_details['discount'],2)?></span></td>
                  </tr>
                  <?php } ?>  
                  <tr>
                    <td style="border-top:1px solid #ccc;background: #d9d9d9;font-weight: bold;"><span style="font-size:15px;color: #003f6a;">Grand Total</span></td>
                    <td style="border-top:1px solid #ccc;background: #d9d9d9;font-weight: bold;"><span style="font-size:15px"> <?=number_format(@$booking_details['grand_total']+$seat_price+$meal_price+$baggage_price,2)?></span></td>
                  </tr>
                </tbody>
              </table>
            </td>
            <!-- <td width="50%" style="padding:0;padding-left:14px; vertical-align:top">
              <table cellspacing="0" cellpadding="5" width="100%" style="border:1px solid #9a9a9a;font-size:12px; padding:0;">
                <tbody>
                    <tr>
                      <td colspan="4" style="border-bottom:1px solid #ccc"><span style="font-size:13px">Flight Inclusions</span></td>
                   </tr>
                    <tr>
                      <td style="background-color:#d9d9d9; color:#555555"><span>Baggage</span></td>
                      <td style="background-color:#d9d9d9; color:#555555"><span>Adult</span></td>
                      <td style="background-color:#d9d9d9; color:#555555"><span>Child</span></td>
                      <td style="background-color:#d9d9d9; color:#555555"><span>Infant</span></td>
                   </tr>
                   <tr>
                      <td><span>Cabin Baggage</span></td>
                      <td><span><?php echo $cabin_baggage; ?> Kg</span></td>
                      <td><span><?php echo $cabin_baggage; ?> Kg</span></td>
                      <td><span>0 Kg</span></td>
                   </tr>
                   <tr>
                      <td><span>Check-in Baggage</span></td>
                      <td><span><?php echo $checkin_baggage; ?> Kg</span></td>
                      <td><span><?php echo $checkin_baggage; ?> Kg</span></td>
                      <td><span>0 Kg</span></td>
                   </tr>
                   <tr>
                      <td colspan="4" style="border-top:1px solid #ccc"><span style="font-size:10px; color:#666; line-height:20px;">* Flight inclusions are subject to change with Airlines.</span></td>
                   </tr>
                </tbody>
              </table>
            </td> -->
            </tr>           
            <tr><td style="line-height:15px;padding:0;">&nbsp;</td></tr>
            <?php 
              if(isset($booking_transaction_details_value['extra_service_details']) == true && valid_array($booking_transaction_details_value['extra_service_details']) == true){ ?>
            <tr>
            <td colspan="2" style="padding-top:10px; vertical-align:top; padding:0;">
              <table cellspacing="0" cellpadding="5" width="100%" style="border:1px solid #666666;font-size:12px; padding:0;">
                <tbody>
                  <tr>
                    <td colspan="4" style="background-color:#666666; color:#fff;"><span style="font-size:13px">Flight Extra Information</span></td>
                  </tr>
                
                  <?php 
                  if(isset($booking_transaction_details_value['extra_service_details']['baggage_details']) == true && valid_array($booking_transaction_details_value['extra_service_details']['baggage_details']) == true){ 
                    $baggage_details = $booking_transaction_details_value['extra_service_details']['baggage_details'];
                    foreach ($baggage_details['baggage_source_destination_label'] as $bag_lk => $bag_lv){
                  ?>
                  <tr><td colspan="4" style="background-color:#d9d9d9; color:#555555"><span><?php echo 'Extra Baggage Information'; ?> ( <?=$bag_lv?> )</span></td></tr>
                  <?php 
                  foreach ($baggage_details['details'] as $bag_k => $bag_v){
                    foreach ($bag_v as $bd_k => $bd_v){
                      // debug($bag_lv);exit;
                      if($bd_v['from_airport_code'].'-'.$bd_v['to_airport_code'] == $bag_lv){
                  ?>
                  <tr><td colspan="4"><?=$bd_v['description']?> ( <?=$bag_v[0]['pax_name']?> )</td></tr>
                  <?php } } } } }
                  if(isset($booking_transaction_details_value['extra_service_details']['meal_details']) == true && valid_array($booking_transaction_details_value['extra_service_details']['meal_details']) == true){ 
                    $meal_details = $booking_transaction_details_value['extra_service_details']['meal_details'];
                    $meal_type = end($meal_details['details']);
                    
                    $meal_type = $meal_type[0]['type'];
                    if($meal_type == 'static'){
                      $meal_type_label = 'Meal Preference';
                    } else{
                      $meal_type_label = 'Meal Information';
                    }
                    foreach ($meal_details['meal_source_destination_label'] as $meal_lk => $meal_lv){
                  ?>
                  <tr><td colspan="4" style="background-color:#d9d9d9; color:#555555"><span><?php echo $meal_type_label; ?> ( <?=$meal_lv?> )</span></td></tr>
                  <?php 
                  foreach ($meal_details['details'] as $meal_k => $meal_v){
                    foreach ($meal_v as $md_k => $md_v){

                      if($md_v['from_airport_code'].'-'.$md_v['to_airport_code'] == $meal_lv){
                  ?>
                  <tr><td colspan="4"><?=$md_v['description']?> ( <?=$meal_v[0]['pax_name']?> )</td></tr>
                  <?php } } } } }
                   if(isset($booking_transaction_details_value['extra_service_details']['seat_details']) == true && valid_array($booking_transaction_details_value['extra_service_details']['seat_details']) == true){
                    $seat_details = $booking_transaction_details_value['extra_service_details']['seat_details'];
                    $seat_type = end($seat_details['details']);
                    $seat_type =  $seat_type[0]['type'];
                    if($seat_type == 'static'){
                      $seat_type_label = 'Seat Preference';
                    } else{
                      $seat_type_label = 'Seat Information';
                    }
                   foreach ($seat_details['seat_source_destination_label'] as $seat_lk => $seat_lv){
                  ?>
                  <tr><td colspan="4" style="background-color:#d9d9d9; color:#555555"><span><?php echo $seat_type_label; ?> ( <?=$seat_lv?> )</span></td></tr>
                  <?php 
                  foreach ($seat_details['details'] as $seat_k => $seat_v){
                    // debug($seat_lv);exit;
                    foreach ($seat_v as $sd_k => $sd_v){
                      if($sd_v['from_airport_code'].'-'.$sd_v['to_airport_code'] == $seat_lv){
                      $seat_description = trim($sd_v['description']);
                      if(empty($seat_description) == true){
                        $seat_description = trim($sd_v['code']);
                      }
                  ?>
                  <tr><td colspan="4"><?=$seat_description?> ( <?=$seat_v[0]['pax_name']?> )</td></tr>
                  <?php } } } } } ?>
                  
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
    <td colspan="4" style="border-bottom:1px solid #999999;padding-bottom:15px">
      <span style="font-size:14px; color:#555;"><p style="color:#212121;font-size:16px;font-weight: bold;">Customer Contact Details :<span style="display: block; float: right;">
        <button type="button"data-toggle="modal" data-target="#editBookingDetails" class="contact_info_edit"><i class="fa fa-pencil"></i></button></span></p>
        <p>E-mail : <?=$booking_details['email']?></p>
        <p>Contact No : <?=$booking_details['phone']?></p>
      </span>
    </td>
  </tr>
  <tr><td style="line-height:10px;">&nbsp;</td></tr>
   <tr>
      <td>
        <div style="text-align: center;">
            <a href="<?php echo base_url().'index.php/payment_gateway/payment/'.$app_reference.'/'.$book_origin ?>" class="no-pad" style="padding-left: 0">
              <input type="submit" class="custm-btn searchsbmt b-proceed-pay" value="Proceed to pay">
            </a>
            <a style="margin-top: -6px; padding: 15px 56px;" href="<?php echo base_url() ?>" class="custm-btn cancel_btn no-pad btn btn-danger">
              CANCEL
            </a>
        </div>
      </td>
   </tr>
  <!-- <tr><td style="line-height:15px;padding:0;">&nbsp;</td></tr> 
  <tr>
  <td colspan="4"><span style="line-height:20px; font-size:13px;">Important Information</span></td></tr>
  <tr>
    <td colspan="4" style="border-bottom:1px solid #999999; line-height:20px; font-size:12px; color:#555">
      <ul style="padding-bottom:10px">
        
      </ul>
    </td>
  </tr> -->
  <!-- <tr>
    <td colspan="4" align="right" style="padding-top:10px"><?php echo strtoupper($data['domainname'])?><br>ContactNo : <?php echo $data['phone']?><br><?php echo $data['address']?></td>
  </tr> -->
</tbody>
</table>
</div>




<!-- Return Ticket -->

<div id="editBookingDetails" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit</h4>
      </div>
      <div class="modal-body">
        <form method="POST" id="booking_details" action="<?= base_url() ?>index.php/flight/edit_booking_details" class="form-horizontal">
          <input type="hidden" name="origin" value="<?= $booking_details['origin'] ?>">
          <input type="hidden" name="app_reference" value="<?= $app_reference ?>">
          <div class="form-group">
            <label class="control-label col-md-3" for="first_name">Email<span class="required"> *</span></label>
            <div class="col-md-5">
              <input class="code form-control" type="text" required="required" name="email" value="<?= $booking_details['email'] ?>" />
            </div>
          </div>
          <div class="form-group">
             <label class="control-label col-md-3">Phone<span class="required"> *</span></label>
            <div class="col-md-5">
              <input id="booking_user_mobile" maxlength="10" required="required" class="code form-control newslterinput _numeric_only _guest_validate" type="text" name="phone" value="<?= $booking_details['phone'] ?>" minlength="10"/>
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-md-3" for="last_name"></label>
            <div class="col-md-5">
              <input type="submit" class="btn btn-primary" />
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  $(".edit_pax").on("click", function() {
    let html = $(this).parent("td").find(".edit_pax_html").html();
    $("#editPax .modal-body form").html(html);
    $("#editPax input[name=date_of_birth]").addClass("dp_init").attr("id","static_dp");
    var reinit_date = $("#editPax input[name=date_of_birth]").val().split("-");
    var reinit_set_date = new Date(reinit_date[0],parseInt(reinit_date[1])-1,reinit_date[2]);
    $('#static_dp').datepicker({
      dateFormat: 'yy-mm-dd',
      onSelect: function(dateText, inst) {
            //alert(dateText);
          }
    });
    $('#static_dp').datepicker('setDate', reinit_set_date);
  })

  $(document).on('change', "#expiry_month", function() {
    let val = $(this).val();
    let curr_month = <?= $current_month_value ?>;
    let curr_year = <?= $current_year ?>;
    var sel_year = $("#expiry-year").val();
    var curr_day = $("#expiry_day").val();
    var days = <?= json_encode(get_day_numbers()) ?>;
    var current_day_key = 0;
    var d_html = "";

    if (sel_year == curr_year) {
      if (val == curr_month) {
        current_day_key = <?= $current_day_key ?>;
      }
    }

    $.each(days, function( i, l ){
      if (current_day_key < i)
        d_html += `<option value = '${l}' > ${l} </option>`
    });

    $("#expiry_day").html(d_html);

    $("#expiry_day").val(curr_day);
  })
</script>