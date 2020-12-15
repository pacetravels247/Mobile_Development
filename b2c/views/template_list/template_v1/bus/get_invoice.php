<?php  
//$gemoratti_booking_id = trim($booking_details['gemoratti_booking_id']);
//$booked_date = $booking_details['booking_made_on'];
//$booking_made_on = booking_date($booked_date).' '.booking_time($booked_date);
$gemoratti_booking_id = $booking_details['data']['booking_details']['booking_source'];
$booked_date="";
$booking_made_on="";

$booking_status = $booking_details['status'];
if($booking_status == BOOKING_CONFIRMED || $booking_status == BOOKING_CANCELLED) {
	$transaction_id = $booking_details['data']['booking_details']['booking_source'];
	
	$invoice_header = '<h3 style="color: #337ab7;margin-bottom: 10px;">Invoice</h3>
						<h5 style="font-size:13px;color: #666666; margin:0;"><span style="color: #666666;">Invoice No :</span>  '.$transaction_id.'</h5>
            			<h5 style="font-size:13px;color: #666666; margin:0;"><span style="color: #666666;">Service Tax No :</span> </h5>'; 
	$invoice_details = get_invoice_dtails($booking_details);
} else {
	$booking_status = get_booking_status($booking_status);
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
	$invoice_dtails .= ge_bus_details($booking_details);
	$invoice_dtails .= get_bus_pax_details($booking_details);
	$invoice_dtails .= get_fare_details($booking_details);
	return $invoice_dtails;
}

function get_booking_info($booking_details)
{
	$gemoratti_booking_id = $booking_details['data']['booking_details']['booking_source'];
	$booked_date = $booking_details['data']['booking_details']['created_datetime'];
	$booking_made_on = $booking_details['data']['booking_details']['created_datetime'];
	$booking_made_on = explode(" ",$booking_made_on);
	$booking_made_on = $booking_made_on[0];
	$booked_pax_name = $booking_details['data']['booking_customer_details'][0]['name'];
	
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
			        <td style="padding: 10px 15px;">'.$gemoratti_booking_id.'</td>
			        <td style="padding: 10px 15px;">'.$booking_made_on.'</td>
			    </tr>
			</table>';
	return $booking_info;
}
/**
 * Pax Details
 * @param unknown_type $booking_details
 * @return string
 */
function get_bus_pax_details($booking_details)
{
	$pax_details = '<table style="width: 100%;margin: 0px auto 8px auto;">
				    <tr>
				        <td>
				            <h3 style="color: #000;margin:0;">Passengers</h3></td>
				    </tr>
				</table>';
	$pax_details .= '<table style="width: 94%;margin: 0 auto 20px  auto;box-shadow: 2px 2px 1px #d9d9d9;-o-box-shadow: 2px 2px 1px #d9d9d9;-ms-box-shadow: 2px 2px 1px #d9d9d9;
										-moz-box-shadow: 2px 2px 1px #d9d9d9;-webkit-box-shadow: 2px 2px 1px #d9d9d9;border: 1px solid #CCC;border:1px solid #CCC; padding: 10px;">';

	$booking_details['data']['booking_customer_details'];
	$i = 0; 
	if(count($booking_details['data']['booking_customer_details']) > 0){
		foreach($booking_details['data']['booking_customer_details'] as $pax_data){
		$pax_name = $pax_data['name'];
		$seat_numbers = $pax_data['seat_no'];
		if($i ==0 ) {
			$width_class= ' mt10 ';
		} else {
			$width_class= '';
		}
		$pax_details .= '<tr style="border: 1px solid #CCC;margin: 0 10px 15px 0 !important;width: 100%;background-color: #FFF;">
						<td style="box-shadow: 2px 2px 1px #d9d9d9;-o-box-shadow: 2px 2px 1px #d9d9d9;-ms-box-shadow: 2px 2px 1px #d9d9d9;
													-moz-box-shadow: 2px 2px 1px #d9d9d9;-webkit-box-shadow: 2px 2px 1px #d9d9d9;border: 1px solid #CCC;border:1px solid #CCC; padding: 10px;">
							<p style="margin: 0;"><span>'.sprintf("%02d", ($i+1)).'. </span> '.$pax_name.' (SEAT:'.$seat_numbers.')</p>
						</td>
    				</tr>';
		$i++;
		} 
	}
	$pax_details .= '</table>';
	return $pax_details;
}
//Hotel Details
function ge_bus_details($booking_details)
{
	$details = '<table style="width: 94%;margin: 0 auto 20px  auto;box-shadow: 2px 2px 1px #d9d9d9;-o-box-shadow: 2px 2px 1px #d9d9d9;-ms-box-shadow: 2px 2px 1px #d9d9d9;
											-moz-box-shadow: 2px 2px 1px #d9d9d9;-webkit-box-shadow: 2px 2px 1px #d9d9d9;border: 1px solid #CCC;border:1px solid #CCC; padding: 10px;">';
	 
	$choosen_parameters = $booking_details['data']['booking_itinerary_details'][0];
	$travel_name = $choosen_parameters['operator'];
	$bus_type = $choosen_parameters['bus_type'];
	$source_name = $choosen_parameters['departure_from'];
	$destination_name = $choosen_parameters['arrival_to'];
	
	$details .= '<tr>
        <td style="border-bottom:1px solid #CCC; padding: 10px;width: 90px;">
            <i class="fa fa-bus"></i>'.$travel_name.'
        </td>
        <td style="border-bottom:1px solid #CCC; padding: 10px;width: 150px;">
            <div style="font-weight: 700;color: #333 !important;font-size: 16px;">'.$source_name.'</div>
            <div style="color:#999;"></div>
        </td>
        <td style="border-bottom:1px solid #CCC; padding: 10px;">
            <i style="font-size: 16px;margin-top: 0px;" class="fa fa-long-arrow-right arrow_right"></i>
        </td>
        <td style="border-bottom:1px solid #CCC; padding: 10px;width: 150px;">
            <div style="font-weight: 700;color: #333 !important;font-size: 16px;">'.$destination_name.'</div>
            <div style="color:#999;"></div>
        </td>
        <td style="border-bottom:1px solid #CCC; padding: 10px;width: 96px;">
            <div style="font-weight: 700;color: #333 !important;font-size: 16px;"></div>
        </td>
    </tr>';
	$details .= '</table>';
	return $details;
}

function get_fare_details($booking_details)
{
	$base_fare = $booking_details['data']['booking_customer_details'][0]['fare'];
	$tax = '';
	$grand_total = $booking_details['data']['booking_customer_details'][0]['fare'];
	$convenience_fee = '';
	
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
               /* <tr>
                    <td>Tax</td>
                    <td align="right"><i class="fa fa-rupee"></i> '.$tax.'</td>
                </tr>
                <tr>
                    <td>Convenience Fee</td>
                    <td align="right"><i class="fa fa-rupee"></i> '.$convenience_fee.'</td>
                </tr>'
           
           $fare_details .= '<tr>
                    <td>Other Charges</td>
                    <td align="right"><i class="fa fa-rupee"></i> '.$other_charges.'</td>
                </tr>';
                */
           $fare_details .= ' <tr>
                    <td style="padding: 5px 0px;">
                        <h4 style="margin:0;margin-top:5px; color: #337ab7;">GRAND TOTAL</h4>
                    </td>
                    <td style="padding: 5px 0px;" align="right">
                        <h4 style="margin:0;margin-top:5px;color: #337ab7;"><i class="fa fa-rupee"></i> '.$grand_total.'</h4>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
	</table>';
	return $fare_details;
}
?>
