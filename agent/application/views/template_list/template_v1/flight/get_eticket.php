<?php 
$proapp_booking_id = trim($booking_details['data']['booking_details']['booking_source']);
$booking_made_on = $booking_details['data']['booking_details']['created_datetime'];
$booking_status = $booking_details['status'];

if($booking_status == BOOKING_CONFIRMED || $booking_status == BOOKING_CANCELLED) {
	$ticket_details = get_ticket_details($booking_details);
} else {
	$booking_status = $booking_details['data']['booking_details']['status'];
	$ticket_details = '<div class="row">';
	$ticket_details .= '<div class="col-md-12"><h4>Bookng Status: '.$booking_status.'</h4></div>';
	$ticket_details .= '<div class="col-md-12"><h4>Message: Ticket Not Available</h4></div>';
	$ticket_details .= '</div>';
}
?>
<div class="container-fluid pad0">
<div class="row">
	<table style="width: 94%;margin: 0 auto; background:#FFFFFF; font-family:Arial, Helvetica, sans-serif;">
    <tr>
        <td>
            <h3 style="color: #337ab7;margin-bottom: 10px;">Ticket</h3>
            <h5 style="font-size:13px;color: #666666; margin:0;"><span style="color: #666666;">Booking ID :</span> <?php echo $proapp_booking_id;?></h5>
            <h5 style="font-size:13px;color: #666666; margin:0;">Booking Date : <?php echo $booking_made_on;?> Hrs</h5>
        </td>
        <td align="right">
            <img src="<?php echo $GLOBALS['CI']->template->domain_images($GLOBALS['CI']->template->get_domain_logo()); ?>">
        </td>
    </tr>
	</table>
	<table>
    <tr>
        <td>
            <hr style="border-top: 1px solid #CCC;padding: 0;margin: 20px 0 !important;width: 100%;clear: both;">
        </td>
    </tr>
	</table>
	<?php echo $ticket_details;?>
</div>
</div>
<?php 
function get_ticket_details($booking_details)
{
	$ticket_details = '';
	$ticket_details .= '
	<table style="width: 94%;margin: 0 auto; background:#FFFFFF; font-family:Arial, Helvetica, sans-serif;">
    <tr>
        <td>
            <h2 style="margin: 0 0 15px 0; color:black;">Itinerary & Reservation Details</h2></td>
    </tr>
	</table>
	<table style="width: 94%;margin: 0 auto;box-shadow: 2px 2px 1px #d9d9d9;-o-box-shadow: 2px 2px 1px #d9d9d9;-ms-box-shadow: 2px 2px 1px #d9d9d9;-moz-box-shadow: 2px 2px 1px #d9d9d9;-webkit-box-shadow: 2px 2px 1px #d9d9d9;border: 1px solid #CCC;border-radius: 4px;-o-border-radius: 4px;-ms-border-radius: 4px;-moz-border-radius: 4px;-webkit-border-radius: 4px; background:#FFFFFF; font-family:Arial, Helvetica, sans-serif;">';
	$ticket_details .= ge_segment_details($booking_details);
	$ticket_details .= get_flight_pax_details($booking_details);
	$ticket_details .= '</table>';
	/* $ticket_details .= '<table class="eticket-table" style="width: 94%;margin: 10px auto; background:#FFFFFF; font-family:Arial, Helvetica, sans-serif;">
						<tr>
							<td >'.get_flight_eticket_content_block().'</td>
							</tr>
						</table>'; */ 
	return $ticket_details;
}
//Segment Details
function ge_segment_details($booking_details)
{
	load_flight_lib(PROVAB_FLIGHT_BOOKING_SOURCE);
	$details = '<table style="width: 94%;margin: 0 auto 20px  auto;box-shadow: 2px 2px 1px #d9d9d9;-o-box-shadow: 2px 2px 1px #d9d9d9;-ms-box-shadow: 2px 2px 1px #d9d9d9;
											-moz-box-shadow: 2px 2px 1px #d9d9d9;-webkit-box-shadow: 2px 2px 1px #d9d9d9;border: 1px solid #CCC;border:1px solid #CCC; padding: 10px;">';
	
	$is_onward = array();
	$is_return = array();
	$onward_flight = "";
	$return_flight = "";
	$onward_header = "";
	$return_header = "";
	foreach($booking_details['data']['booking_itinerary_details'] as $v){
		$total_duration = get_total_duration($v['arrival_datetime'], $v['departure_datetime']);
		
		$fare_class = $GLOBALS['CI']->flight_lib->get_fare_class($v['fare_class']);
		if(trim($v['segment_indicator']) == '1'){
			$is_onward[] = 1;
			$onward_flight .='<tr>  
								<td style="border-bottom:1px solid #CCC; padding: 10px;width: 90px;">
									<img src="'.SYSTEM_IMAGE_DIR.'airline_logo/'.$v['airline_code'].'.gif">
									<span style="color:#999;">'.$v['airline_name'].'</span>
									<div style="color:#999;margin-left: 25px;">'.$v['flight_number'].'</div>
								</td>
								<td style="border-bottom:1px solid #CCC; padding: 10px;width: 150px;">
									<div style="font-weight: 700;color: #333 !important;font-size: 16px;">'.$v['from_airport_code'].'</div>
									<div style="color:#999;">'.$v['from_airport_name'].'</div>
									<div style="color:#999;">'.$v['departure_datetime'].'</div>
								</td>
								<td style="border-bottom:1px solid #CCC; padding: 10px;">
									<i style="font-size: 16px;margin-top: 0px;" class="fa fa-long-arrow-right arrow_right"></i>
								</td>
								<td style="border-bottom:1px solid #CCC; padding: 10px;width: 150px;">
									<div style="font-weight: 700;color: #333 !important;font-size: 16px;">'.$v['to_airport_code'].'</div>
									<div style="color:#999;">'.$v['to_airport_name'].'</div>
									<div style="color:#999;">'.$v['arrival_datetime'].'</div>
								</td>
								<td style="border-bottom:1px solid #CCC; padding: 10px;width: 96px;">
									<div style="font-weight: 700;color: #333 !important;font-size: 16px;">'.$total_duration['hours'].'hr '.$total_duration['minutes'].'m</div>
									<div style="color:#999;">'.$fare_class.'</div>
								</td>
							</tr>';		
		}else{
			$is_return[] = 2;
			$return_flight .='<tr>
								<td style="border-bottom:1px solid #CCC; padding: 10px;width: 90px;">
									<img src="'.SYSTEM_IMAGE_DIR.'airline_logo/'.$v['airline_code'].'.gif">
									<span style="color:#999;">'.$v['airline_name'].'</span>
									<div style="color:#999;margin-left: 25px;">'.$v['flight_number'].'</div>
								</td>
								<td style="border-bottom:1px solid #CCC; padding: 10px;width: 150px;">
									<div style="font-weight: 700;color: #333 !important;font-size: 16px;">'.$v['from_airport_code'].'</div>
									<div style="color:#999;">'.$v['from_airport_name'].'</div>
									<div style="color:#999;">'.$v['departure_datetime'].'</div>
								</td>
								<td style="border-bottom:1px solid #CCC; padding: 10px;">
									<i style="font-size: 16px;margin-top: 0px;" class="fa fa-long-arrow-right arrow_right"></i>
								</td>
								<td style="border-bottom:1px solid #CCC; padding: 10px;width: 150px;">
									<div style="font-weight: 700;color: #333 !important;font-size: 16px;">'.$v['to_airport_code'].'</div>
									<div style="color:#999;">'.$v['to_airport_name'].'</div>
									<div style="color:#999;">'.$v['arrival_datetime'].'</div>
								</td>
								<td style="border-bottom:1px solid #CCC; padding: 10px;width: 96px;">
									<div style="font-weight: 700;color: #333 !important;font-size: 16px;">'.$total_duration['hours'].'hr '.$total_duration['minutes'].'m</div>
									<div style="color:#999;">'.$fare_class.'</div>
								</td>
							</tr>';
		}
	}
	
	if(count($is_onward) > 0 && count($is_return) > 0){
		if(count($is_onward) > 0){
			$onward_header .= '<tr><td colspan="5"  style="border-bottom:1px solid #CCC; padding: 10px;width: 90px;font-weight: bold;color: #337ab7 !important;"> ONWARD AIRLINE DETAILS</td></tr>';
		}
		
		if(count($is_return) > 0){
			$return_header .= '<tr><td colspan="5"  style="border-bottom:1px solid #CCC; padding: 10px;width: 90px;font-weight: bold;color: #337ab7 !important;"> RETURN AIRLINE DETAILS</td></tr>';
		}
	}else{
		$onward_header .='<tr><td colspan="5"  style="border-bottom:1px solid #CCC; padding: 10px;width: 90px;font-weight: bold;color: #337ab7 !important;"> AIRLINE DETAILS</td></tr>';
	}
	
	$details .= $onward_header.$onward_flight.$return_header.$return_flight.'</table>';
	
	return $details;
}
/**
 * Pax Details
 * @param unknown_type $booking_details
 * @return string
 */
function get_flight_pax_details($booking_details)
{
	$pax_details = '';
	$booking_transaction_details = $booking_details['data']['booking_transaction_details'];
	foreach($booking_transaction_details as $k => $v) {
		if(count($booking_transaction_details) > 1 && $k == 0 ) {
			$pax_details .= '<tr><td colspan="5"  style="border-bottom:1px solid #CCC; padding: 10px;width: 100%;font-weight: bold;color: #337ab7 !important;"> ONWARD PAX DETAILS</td></tr>';
		}
		if($k > 0) {
			$pax_details .= '<tr><td colspan="5"  style="border-bottom:1px solid #CCC; padding: 10px;width: 100%;font-weight: bold;color: #337ab7 !important;"> RETURN  PAX DETAILS</td></tr>';
		}
		$pax_details .= '
		<tr>
        <td colspan="5">
            <table width="100%" style="width:100%;">
                <tr>
                    <td valign="middle"  width="20%" height="40" style="background-color: #e8e8e8; font-weight: bold; border-bottom:1px solid #CCC; padding:10px; font-size:14px;">PASSENGER NAME</td>
                    <td valign="middle"  width="20%"  style="background-color: #e8e8e8; font-weight: bold; border-bottom:1px solid #CCC; padding:10px; font-size:14px;">TYPE</td>
                    <td valign="middle"  width="20%" style="background-color: #e8e8e8; font-weight: bold; border-bottom:1px solid #CCC; padding:10px; font-size:14px;">TICKET ID</td>
                    <td valign="middle"  width="20%" style="background-color: #e8e8e8; font-weight: bold; border-bottom:1px solid #CCC; padding:10px; font-size:14px;">PNR</td>
                    <td valign="middle"  width="20%" style="background-color: #e8e8e8; font-weight: bold; border-bottom:1px solid #CCC; padding:10px; font-size:14px;">STATUS</td>
                </tr>';

		$pax_ticket_details = $booking_details['data']['pax_ticket_details'];
		$booking_pax_details = $booking_details['data']['booking_pax_details'];
		$ticket_status = $v['status'];
		
		$onwards_passenger = "";
		$return_passenger = "";
		for($i = 0; $i<count($pax_ticket_details); $i++) {
			
			if(($k == 0) && ($pax_ticket_details[$i]['passenger_fk'] == $booking_pax_details[$i]['origin']) &&
			($v['origin'] == $booking_pax_details[$i]['flight_booking_transaction_details_fk'])){
				
				$pax_name = $booking_pax_details[$i]['title'].' '.$booking_pax_details[$i]['first_name'].' '.$booking_pax_details[$i]['middle_name'].' '.$booking_pax_details[$i]['last_name'];
				$pax_type = $booking_pax_details[$i]['passenger_type'];
				$ticket_id = $pax_ticket_details[$i]['TicketId'];
				$pnr = $pax_ticket_details[$i]['TicketNumber'];
				
				$onwards_passenger .= '<tr>
	                    <td valign="middle" height="40" style="background-color: white; font-weight: bold; border-bottom:1px solid #CCC; padding:10px; font-size:13px;">'.$pax_name.'</td>
	                    <td  valign="middle" style="background-color: white; border-bottom:1px solid #CCC; padding:10px; font-size:13px;">'.$pax_type.'</td>
	                    <td  valign="middle" style="background-color: white; font-weight: bold; border-bottom:1px solid #CCC; padding:10px; font-size:13px;">'.$ticket_id.'</td>
	                    <td  valign="middle" style="background-color: white; border-bottom:1px solid #CCC; padding:10px; font-size:13px;">'.$pnr.'</td>
	                    <td  valign="middle" style="background-color: white; border-bottom:1px solid #CCC; padding:10px; font-size:13px;">'.$ticket_status.'</td>
	                	</tr>';

			}elseif(($k > 0) && ($pax_ticket_details[$i]['passenger_fk'] == $booking_pax_details[$i]['origin']) &&
			($v['origin'] == $booking_pax_details[$i]['flight_booking_transaction_details_fk'])){
				
				$pax_name = $booking_pax_details[$i]['title'].' '.$booking_pax_details[$i]['first_name'].' '.$booking_pax_details[$i]['middle_name'].' '.$booking_pax_details[$i]['last_name'];
				$pax_type = $booking_pax_details[$i]['passenger_type'];
				$ticket_id = $pax_ticket_details[$i]['TicketId'];
				$pnr = $pax_ticket_details[$i]['TicketNumber'];
			
				$return_passenger .= '<tr>
	                    <td valign="middle" height="40" style="background-color: white; font-weight: bold; border-bottom:1px solid #CCC; padding:10px; font-size:13px;">'.$pax_name.'</td>
	                    <td  valign="middle" style="background-color: white; border-bottom:1px solid #CCC; padding:10px; font-size:13px;">'.$pax_type.'</td>
	                    <td  valign="middle" style="background-color: white; font-weight: bold; border-bottom:1px solid #CCC; padding:10px; font-size:13px;">'.$ticket_id.'</td>
	                    <td  valign="middle" style="background-color: white; border-bottom:1px solid #CCC; padding:10px; font-size:13px;">'.$pnr.'</td>
	                    <td  valign="middle" style="background-color: white; border-bottom:1px solid #CCC; padding:10px; font-size:13px;">'.$ticket_status.'</td>
	                	</tr>';
			} 
		}
		$pax_details .= $onwards_passenger.$return_passenger.'</table>
					        </td>
					    </tr>';
	}
	return $pax_details;
}
?>