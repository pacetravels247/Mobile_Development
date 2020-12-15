<?php
$booking_status = $booking_details['status'];
if($booking_status == BOOKING_CONFIRMED || $booking_status == BOOKING_CANCELLED) {
	$transaction_id = trim($booking_details['data']['booking_details']['booking_source']); 
	$invoice_header = '<h3 style="color: #337ab7;margin-bottom: 10px;">Invoice</h3>
						<h5 style="font-size:13px;color: #666666; margin:0;"><span style="color: #666666;">Invoice No :</span>  '.$transaction_id.'</h5>
            			<h5 style="font-size:13px;color: #666666; margin:0;"><span style="color: #666666;">Service Tax No :</span> </h5>'; 
	$invoice_details = get_invoice_dtails($booking_details);
} else {
	$booking_status = $booking_details['data']['booking_details']['status'];
	$invoice_header = '<h3 style="color: #337ab7;margin-bottom: 10px;">Invoice</h3>
						<h5 style="font-size:13px;color: #666666; margin:0;"><span style="color: #666666;">Bookng Status :</span>  '.$booking_status.'</h5>
            			<h5 style="font-size:13px;color: #666666; margin:0;"><span style="color: #666666;">Message :</span>  Invoice Not Available</h5>';
	$invoice_details = '';
}
?>
<div class="container-fluid pad0">
	<table style="width: 94%;margin: 0 auto;">
    <tr>
        <td>
            <?php echo $invoice_header;?>
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
<?php echo $invoice_details;?>
</div>
<?php
function get_invoice_dtails($booking_details)
{
	$invoice_dtails = '';
	$invoice_dtails .= get_booking_info($booking_details);
	$invoice_dtails .= ge_segment_details($booking_details);
	$invoice_dtails .= get_flight_pax_details($booking_details);
	$invoice_dtails .= get_fare_details($booking_details);
	return $invoice_dtails;
}
function get_booking_info($booking_details)
{
	$proapp_booking_id = $booking_details['data']['booking_details']['booking_source'];
	$booking_made_on = $booking_details['data']['booking_details']['created_datetime'];
	$booked_pax_name = $booking_details['data']['booking_pax_details'][0]['title'].' '.$booking_details['data']['booking_pax_details'][0]['first_name'].' '.$booking_details['data']['booking_pax_details'][0]['middle_name'].' '.$booking_details['data']['booking_pax_details'][0]['last_name'];
	$booking_info = '
				<table style="width: 94%;margin: 0 auto 20px  auto;box-shadow: 2px 2px 1px #d9d9d9;-o-box-shadow: 2px 2px 1px #d9d9d9;-ms-box-shadow: 2px 2px 1px #d9d9d9;
											-moz-box-shadow: 2px 2px 1px #d9d9d9;-webkit-box-shadow: 2px 2px 1px #d9d9d9;border: 1px solid #CCC;border:1px solid #CCC; padding: 10px;">
			    <tr style="box-shadow: 2px 2px 1px #d9d9d9;-o-box-shadow: 2px 2px 1px #d9d9d9;
																-ms-box-shadow: 2px 2px 1px #d9d9d9;-moz-box-shadow: 2px 2px 1px #d9d9d9;-webkit-box-shadow: 2px 2px 1px #d9d9d9;
																border: 1px solid #CCC;margin: 0 10px 15px 0 !important;width: 100%;background-color: #FFF;">
			        <td style="background-color: #e8e8e8;font-weight:bold;padding: 10px 15px;">BOOKED BY</td>
			        <td style="background-color: #e8e8e8;font-weight:bold;padding: 10px 15px;">BOOKING ID</td>
			        <td style="background-color: #e8e8e8;font-weight:bold;padding: 10px 15px;">BOOKING DATE</td>
			    </tr>
			    <tr style="box-shadow: 2px 2px 1px #d9d9d9;-o-box-shadow: 2px 2px 1px #d9d9d9;
															-ms-box-shadow: 2px 2px 1px #d9d9d9;-moz-box-shadow: 2px 2px 1px #d9d9d9;-webkit-box-shadow: 2px 2px 1px #d9d9d9;
															border: 1px solid #CCC;margin: 0 10px 15px 0 !important;width: 100%;background-color: #FFF;">
			        <td style="font-weight:bold;padding: 10px 15px;">'.$booked_pax_name.'</td>
			        <td style="padding: 10px 15px;">'.$proapp_booking_id.'</td>
			        <td style="padding: 10px 15px;">'.$booking_made_on.'</td>
			    </tr>
			</table>';
	return $booking_info;
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

/** Pax Details **/
 
function get_flight_pax_details($booking_details)
{
	$pax_details = '<table style="width: 94%;margin: 0px auto 8px auto;">
				    <tr>
				        <td>
				            <h3 style="color: #000;margin:0;">Passengers</h3></td>
				    </tr>
				</table>';
	$pax_details .= '<table style="width: 94%;margin: 0 auto 20px  auto;box-shadow: 2px 2px 1px #d9d9d9;-o-box-shadow: 2px 2px 1px #d9d9d9;-ms-box-shadow: 2px 2px 1px #d9d9d9;
										-moz-box-shadow: 2px 2px 1px #d9d9d9;-webkit-box-shadow: 2px 2px 1px #d9d9d9;border: 1px solid #CCC;border:1px solid #CCC; padding: 10px;">';
	$passenger_data = $booking_details['data']['booking_pax_details'];  
	for($i = 0; $i<count($passenger_data); $i++) {
		$pax_name = $passenger_data[$i]['title'].' '.$passenger_data[$i]['first_name'].' '.$passenger_data[$i]['middle_name'].' '.$passenger_data[$i]['last_name'];
		$pax_details .= '<tr style="border: 1px solid #CCC;margin: 0 10px 15px 0 !important;width: 100%;background-color: #FFF;">
						<td style="box-shadow: 2px 2px 1px #d9d9d9;-o-box-shadow: 2px 2px 1px #d9d9d9;-ms-box-shadow: 2px 2px 1px #d9d9d9;
													-moz-box-shadow: 2px 2px 1px #d9d9d9;-webkit-box-shadow: 2px 2px 1px #d9d9d9;border: 1px solid #CCC;border:1px solid #CCC; padding: 10px;">
							<p style="margin: 0;"><span>'.sprintf("%02d", ($i+1)).'. </span> '.$pax_name.'</p>
						</td>
    				</tr>';
	}
	$pax_details .= '</table>';
	return $pax_details;
}

function get_fare_details($booking_details)
{
	
	$base_fare = $booking_details['data']['booking_details']['total_fare'];
//	$tax = $payment_details['tax'];
//	$other_charges = $payment_details['other_charges'];
//	$grand_total = $payment_details['grand_total'];
//	$convenience_fee = $payment_details['convenience_fee'];
	
	$fare_details = '';
	$fare_details .= '<table style="width: 94%;margin: 0px auto 8px auto;">
					    <tr>
					        <td>
					            <h3 style="color: #000;margin:0;">Fare Details</h3></td>
					    </tr>
					</table>';
	$fare_details .= '<table style="width: 94%;margin: 0 auto 20px  auto;box-shadow: 2px 2px 1px #d9d9d9;-o-box-shadow: 2px 2px 1px #d9d9d9;-ms-box-shadow: 2px 2px 1px #d9d9d9;
											-moz-box-shadow: 2px 2px 1px #d9d9d9;-webkit-box-shadow: 2px 2px 1px #d9d9d9;border: 1px solid #CCC;border:1px solid #CCC; padding: 10px;">
    <tr>
        <td style="background-color: #e8e8e8;border-bottom:1px solid #CCC;color:black;font-weight:bold;padding: 10px 15px;">FARE DETAILS</td>
    </tr>
    <tr>
        <td colspan="2" style="padding: 5px 20px;">
            <table style="width:100%;line-height:25px;">
                <tr>
                    <td style="padding: 5px 0px;border-bottom: 1px solid #CCC;">
                        <h4 style="margin:0; color:black;">Base Fare</h4>
                    </td>
                    <td style="padding: 5px 0px;border-bottom: 1px solid #CCC;" align="right">
                        <h4 style="margin:0; color:black;"><i class="fa fa-rupee"></i> '.$base_fare.'</h4>
                    </td>
                </tr>';
               /*  <tr>
                    <td>Tax</td>
                    <td align="right"><i class="fa fa-rupee"></i> '.$tax.'</td>
                </tr>
                <tr>
                    <td>Other Charges</td>
                    <td align="right"><i class="fa fa-rupee"></i> '.$other_charges.'</td>
                </tr>
                <tr>
                    <td>Convenience Fee</td>
                    <td align="right"><i class="fa fa-rupee"></i> '.$convenience_fee.'</td>
                </tr> */ 
          $fare_details .= '<tr>
                    <td style="padding: 5px 0px;">
                        <h4 style="margin:0;margin-top:5px; color: #337ab7;">GRAND TOTAL</h4>
                    </td>
                    <td style="padding: 5px 0px;" align="right">
                        <h4 style="margin:0;margin-top:5px;color: #337ab7;"><i class="fa fa-rupee"></i> '.$base_fare.'</h4>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
	</table>';
	return $fare_details;
}