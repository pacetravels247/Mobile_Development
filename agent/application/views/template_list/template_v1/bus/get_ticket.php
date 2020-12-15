<?php
$gemoratti_booking_id = $booking_details['data']['booking_details']['app_refernce'];
//$booked_date = $booking_details['booking_made_on'];
$booking_made_on = $booking_details['data']['booking_details']['created_datetime'];

$booking_status = $booking_details['status'];
if($booking_status == BOOKING_CONFIRMED || $booking_status == BOOKING_CANCELLED) { 
	$ticket_details = get_ticket_details($booking_details,$domain_details);
} else {
	$booking_status = get_booking_status($booking_status);
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
            <h5 style="font-size:13px;color: #666666; margin:0px 0px 5px;"><span style="color: #666666;">Booking ID :</span> <?php echo $gemoratti_booking_id;?></h5>
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
	<?php echo $ticket_details; ?>
</div>
</div>
<?php 
function get_ticket_details($booking_details,$domain_details)
{
	$ticket_details = '';
	$ticket_details .= '
	<div class="table-responsive">
	<table style="width: 94%;margin: 0 auto; font-family:Arial, Helvetica, sans-serif; background:#FFFFFF;">
    <tr>
        <td>
            <h2 style="margin: 0 0 15px 0; color:black;">Booking Details</h2></td>
    </tr>
	</table>
	 
	<table class="table" style="width: 94%;margin: 0 auto;box-shadow: 2px 2px 1px #d9d9d9;-o-box-shadow: 2px 2px 1px #d9d9d9;-ms-box-shadow: 2px 2px 1px #d9d9d9;-moz-box-shadow: 2px 2px 1px #d9d9d9;-webkit-box-shadow: 2px 2px 1px #d9d9d9;border: 1px solid #CCC;border-radius: 4px;-o-border-radius: 4px;-ms-border-radius: 4px;-moz-border-radius: 4px;-webkit-border-radius: 4px; background:#FFFFFF; font-family:Arial, Helvetica, sans-serif;">';
	
	$ticket_details .= get_booking_details($booking_details);
	$ticket_details .= get_bus_pax_details($booking_details);
	$ticket_details .= '</table></div>';
	$ticket_details .= '<div class="table-responsive">'.get_bus_eticket_content_block($domain_details).'</div>';
	return $ticket_details;
}
function get_booking_details($booking_details)
{
	//Booking Details
	$details = '';
	$choosen_parameters = $booking_details['data']['booking_itinerary_details'][0];
	$travel_name = $choosen_parameters['operator'];
	$bus_type = $choosen_parameters['bus_type'];
	$boarding_location = $choosen_parameters['departure_from'];
	//$boarding_time = @$choosen_parameters['boarding_time'];
	$boarding_time = $choosen_parameters['departure_datetime'];
	//$dropping_location = @$choosen_parameters['dropping_location'];
	$dropping_time = $choosen_parameters['arrival_datetime'];
	$source_name = $choosen_parameters['departure_from'];
	$destination_name = $choosen_parameters['arrival_to'];
	$total_duration = get_total_duration($dropping_time, $boarding_time);
	//$total_duration = "";
	$details .= '<tr bgcolor="#cccccc">
	<th width="30%" height="40">Travel</th>
	<th width="20%">Source</th>
	<th width="5%">&nbsp;</th>
	<th width="20%">Destination</th>
	<th>Duration</th></tr>';
	$details .= '<tr>
        <td style="border-bottom:1px solid #CCC; padding: 10px;width:30%;  font-size: 14px;">
            <div style="font-weight: 700;color: #333 !important;font-size: 16px;"><i class="fa fa-bus"></i>'.$travel_name.'</div>
        </td>
        <td valign="top" style="border-bottom:1px solid #CCC; padding: 10px;width: 20%;">
            <div style="font-weight: 700;color: #333 !important;font-size: 16px; margin:0; padding:0;">'.$source_name.'</div>
            <div style="color:#999; font-size: 11px; margin:0; padding:0;">'.$boarding_location.'</div>
            <div style="color:#999; font-size: 11px; margin:0; padding:0;">'.$boarding_time.'</div>
        </td>
        <td style="border-bottom:1px solid #CCC; padding: 10px;" width="5%">
            <i style="font-size: 16px;margin-top: 0px;" class="fa fa-long-arrow-right arrow_right"></i>
        </td>
        <td style="border-bottom:1px solid #CCC; padding: 10px;width: 20%;">
            <div style="font-weight: 700;color: #333 !important;font-size: 16px; margin:0; padding:0;">'.$destination_name.'</div>
            <div style="color:#999; font-size: 11px; margin:0; padding:0;">'.$destination_name.'</div>
            <div style="color:#999; font-size: 11px; margin:0; padding:0;">'.$dropping_time.'</div>
        </td>
        <td style="border-bottom:1px solid #CCC; padding: 10px;width: 20%;">
            <div style="font-weight: 700;color: #333 !important;font-size: 11px;">'.$total_duration['hours'].'hr '.$total_duration['minutes'].'m</div>
        </td>
    </tr>';
	return $details;
}
/**
 * Pax Details
 * @param unknown_type $booking_details
 * @return string
 */
function get_bus_pax_details($booking_details)
{
	$pax_details = '';
		$pax_details .= '
		<tr>
        <td colspan="5">
            <table style="width:100%;">
                <tr>
                    <td style="background-color: #e8e8e8; font-weight: bold; border-bottom:1px solid #CCC; padding:10px; font-size: 14px;">PASSENGER NAME</td>
                    <td style="background-color: #e8e8e8; font-weight: bold; border-bottom:1px solid #CCC; padding:10px; font-size: 14px;">PNR</td>
                    <td style="background-color: #e8e8e8; font-weight: bold; border-bottom:1px solid #CCC; padding:10px; font-size: 14px;">SEAT NUMBER</td>
                </tr>';
		//$bus_pnr = trim($booking_details['bus_pnr']);
		$bus_pnr = $booking_details['data']['booking_details']['pnr'];
		//$booking_status = get_enum_list('booking_status', $booking_details['status']);
		//$choosen_parameters = unserialize($booking_details['choosen_parameters']);
		//$seat_numbers = explode(',', $choosen_parameters['seat_numbers']);
		//$core_pax_name = explode(DB_SAFE_SEPARATOR, $booking_details['pax_name']);
		//$core_pax_title = explode(DB_SAFE_SEPARATOR, $booking_details['pax_title']);
		$i = 0; 
		if(count($booking_details['data']['booking_customer_details']) > 0){
			foreach($booking_details['data']['booking_customer_details'] as $pax_data){
			//$pax_title  = get_enum_list('title', $core_pax_title[$i]);
			$pax_name = $pax_data['name'];
			$seat_numbers = $pax_data['seat_no'];
			$paid_amount = '';
			$pax_details .= '<tr>
                    <td style="background-color: white; font-weight: bold; border-bottom:1px solid #CCC; padding:10px; font-size: 13px;">'.$pax_name.'</td>
                    <td style="background-color: white; font-weight: bold; border-bottom:1px solid #CCC; padding:10px; font-size: 13px;">'.$bus_pnr.'</td>
                    <td style="background-color: white; border-bottom:1px solid #CCC; padding:10px; font-size: 13px;">'.$seat_numbers.'</td>
                	</tr>';
	
			}
		}
		$pax_details .= '</table>
        </td>
    </tr>';
	return $pax_details;
}

function get_bus_eticket_content_block($domain_details)
{
	$message = '<table class="eticket-table" style="width: 94%;margin: 10px auto; background:#FFFFFF; font-family:Arial, Helvetica, sans-serif;>
<tr>
<td colspan="2"><strong>Terms and Conditions</strong>
<div style="border-bottom:#666 1px dashed"></div>
</td>
</tr>
<tr>
<td width="47%" valign="top" style="font-size:12px; font-family:Arial, Helvetica, sans-serif; line-height:18px; text-align:justify; padding:10px;">

<ol>
<li>'.$domain_details['domain_name'].' is ONLY a bus ticket agent and does not operate bus services of its own. We provide information in good faith to help passengers to make an informed decision such as comprehensive choice of bus operators, departure times, prices and other required information to customers. We advice our customers to choose those bus service they feel comfortable with.</li>
<li>Our responsibilities includes
<ol>
<li type="a">Issuing a valid ticket (a ticket that will be accepted by the bus operator) for its network of bus operators.</li>
<li type="a">Providing refund and support in the event of cancellation</li>
<li type="a">Providing customer support and information in case of any delays / inconvenience</li>
</ol>
</li>

<li>Our responsibilities does not includes, however we do our best to provide best available services.
<ol>
<li type="a">The bus operator s bus not departing / reaching on time.</li>
<li type="a">The bus operator s employees being rude</li>
<li type="a">The bus operator s bus seats etc not being up to the customer s expectation.</li>

<li type="a">The bus operator canceling the trip due to unavoidable reasons.</li>
<li type="a">The baggage of the customer getting lost / stolen / damaged.</li>
<li type="a">The bus operator changing a customer s seat at the last minute to accommodate a lady / child.</li>
<li type="a">The customer waiting at the wrong boarding point (please call the bus operator to find out the exact boarding point if you are not a regular traveler on that particular bus).</li>
<li type="a">The bus operator changing the boarding point and/or using a pick-up vehicle at the boarding point to take customers to the bus departure point.</li>

</ol>
</li>

<li>The departure time and duration mentioned on the ticket is only tentative timings. However the bus will not leave the source before the time that is mentioned on the ticket.</li>
<li>Passengers are required to furnish the following at the time of boarding the bus:
<ol>
<li type="a">A Valid copy of ticket (M-Ticket OR print out of the ticket).</li>
<li>A valid identity proof Failing to do so, they may not be allowed to board the bus.</li>
</ol>

</li>

<li>Change of bus: In case the bus operator changes the type of bus due to some reason, '.$domain_details['domain_name'].' will refund the differential amount to the customer upon being intimated by the customers in 24 hours of the journey.</li>
</ol>

<ol start="7">

<li>Cancellation
<ol>
<li type="a">If you wish to cancel
<ol type="i">
<li>Please visit 24x7 Customer Support on our website OR write us at support@'.$domain_details['domain_name'].' OR call us at PHONE_NUMBER</li>
</ol>
</li>

<li type="a">In the event of bus cancellation
<ol type="i">
<li>Please call us at PHONE_NUMBER for alternate arrangements (If available) or for refunds</li>
</ol>
</li>
</ol>
</li>
<li>A ticket will be considered booked as long as the ticket shows up on the confirmation page of www.'.$domain_details['domain_name'].' even in case the case of booking confirmation e-mail and sms gets delayed or fails because of technical reasons or as a result of incorrect e-mail ID / phone number provided by the user etc.</li>
<li>Grievances and claims related to the bus journey should be reported to '.$domain_details['domain_name'].' support team within 10 days of your travel date.</li>
<li>Cancellation policy mentioned on website OR on ticket is of travels and does not decided by '.$domain_details['domain_name'].'. '.$domain_details['domain_name'].' does not levy any cancellation charges.</li>

<li>Cancellation Policy</li>
<table width="100%" cellpadding="2" cellspacing="0" border="0" style="font-family:arial;border-collapse:collapse">
<tbody><tr>
<th align="left" style="font-size:11px;font-weight:bold;border:1px solid #999999">Time Before Departure</th>
<th align="left" style="font-size:11px;font-weight:bold;border:1px solid #999999">Cancellation charges</th>
</tr>
<tr>
<td>';
if (isset($cancel_data) == true and valid_array($cancel_data)) {
	for ($i = 0; $i < count($cancel_data); $i++)
	{
		if ($cancel_data[$i]['ChargePercentage']['_v'] == 100)
		{
			if ($cancel_data[$i]['MinsBeforeDeparture']['_v'] < 1440)
			{
				$Deptime = ($cancel_data[$i]['MinsBeforeDeparture']['_v'] / 60) . " Hr.";
			} else
			{
				$Deptime = ($cancel_data[$i]['MinsBeforeDeparture']['_v'] / (60 * 24)) . " Days.";
			}
			$message.='<tr class="smalltext-heading-blue textPadding" align="left" style="height:25px;">
    <td>
        Less than ' . $Deptime . ' before departure time and at/after the departure time
    </td><td align="left">
        <span>&nbsp;</span><span class="bodytext" style="font-weight:normal;">No refund will be given</span>
    </td>
</tr>';
		} else if ($i == (count($cancel_data) - 1))
		{
			// For Before
			if ($cancel_data[$i - 1]['MinsBeforeDeparture']['_v'] < 1440)
			{
				$Deptime = ($cancel_data[$i - 1]['MinsBeforeDeparture']['_v'] / 60) . " Hr.";
			} else
			{
				$Deptime = ($cancel_data[$i - 1]['MinsBeforeDeparture']['_v'] / (60 * 24)) . " Days.";
			}

			// For After
			if ($cancel_data[$i]['MinsBeforeDeparture']['_v'] < 1440)
			{
				$Deptime2 = ($cancel_data[$i]['MinsBeforeDeparture']['_v'] / 60) . " Hr.";
			} else
			{
				$Deptime2 = ($cancel_data[$i]['MinsBeforeDeparture']['_v'] / (60 * 24)) . " Days.";
			}
			$message.='<tr class="smalltext-heading-blue textPadding" align="left" style="height:25px;">
    <td>
        Up to ' . $Deptime . ' before departure time
    </td><td align="left">
        <span>&nbsp;</span><span class="bodytext" style="font-weight:normal;">' . $cancel_data[$i]['ChargePercentage']['_v'] . '% </span>
    </td>
</tr>';
		} else
		{
			// For Before
			if ($cancel_data[$i - 1]['MinsBeforeDeparture']['_v'] < 1440)
			{
				$Deptime = ($cancel_data[$i - 1]['MinsBeforeDeparture']['_v'] / 60) . " Hr.";
			} else
			{
				$Deptime = ($cancel_data[$i - 1]['MinsBeforeDeparture']['_v'] / (60 * 24)) . " Days.";
			}

			// For After
			if ($cancel_data[$i]['MinsBeforeDeparture']['_v'] < 1440)
			{
				$Deptime2 = ($cancel_data[$i]['MinsBeforeDeparture']['_v'] / 60) . " Hr.";
			} else
			{
				$Deptime2 = ($cancel_data[$i]['MinsBeforeDeparture']['_v'] / (60 * 24)) . " Days.";
			}
			$message.='<tr class="smalltext-heading-blue textPadding" align="left" style="height:25px;">
    <td>
        Between ' . $Deptime2 . ' and up to ' . $Deptime . ' before departure time
    </td><td align="left">
        <span>&nbsp;</span><span class="bodytext" style="font-weight:normal;">' . $cancel_data[$i]['ChargePercentage']['_v'] . '% </span>
    </td>
</tr>';
		}
	}
}

$message.='</td>
</tr><tr>
<td colspan="2" style="color:red;font-size:11px;border:1px solid #999999">Note: The above mentioned policy is bus operator s cancellation policy. '.$domain_details['domain_name'].' does not levy any cancellation charges on its own. .</td>
</tr>
</tbody></table>
</ol>
</td>
</tr>
</table>';

return $message;

}
?>