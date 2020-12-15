<?php
$message = '';
$message.='<div class="mobVouch"><div style="width:1024px; border:#080808 1px solid; margin:auto; font-family:Arial, Helvetica, sans-serif; padding:2px;" id="voucher">
<table width="100%" border="0" style="font-family:Arial, Helvetica, sans-serif">
<tr>';
if($button == ACTIVE){
	$message.='
<td width="21%"><img src="'.$GLOBALS['CI']->template->domain_images($domain_details['domain_logo']).'" width="200" alt="" title="" /></td>';
}
$message.='<td width="74%"><table width="100%" border="0">
<tr>
<td align="right" style="font-size:35px; padding:5px;"><strong>eTicket</strong></td>
</tr>
<tr>
<td align="right"><span style="font-size:16px; padding:5px;"> <strong>'.$booking_itinerary_details[0]['operator'].'</strong> </span></td>
</tr>
<tr>
<td align="right"><span style="font-size:14px; padding:5px;">'.$booking_itinerary_details[0]['bus_type'].'</span></td>
</tr>
</table></td>    
</tr>
</table>
<table width="100%" border="0" cellpadding="6" style="border-top:2px #cfcbcb solid;">
<tr>
<td width="23%"> '.date("l, M jS Y",strtotime($booking_itinerary_details[0]['journey_datetime'])).'</td>
<td width="36%" align="center" style="font-size:25px; font-weight:bold;">'.ucfirst($booking_itinerary_details[0]['departure_from']).'-to-'.ucfirst($booking_itinerary_details[0]['arrival_to']).'</td>
<td width="10%">'.$booking_details['status'].'</td>
<td width="31%" align="right">'.$domain_details['domain_name'].' Ref No: '.$booking_details['app_reference'].'</td>
</tr>
<tr>  
<td colspan="4">    
<div style="height:3px; width:100%; background-color:#ed1c24"></div>
<div style="margin-top:10px; float:left">';
$message.='</div>
</td>
</tr>
</table>';
$message.='<table width="100%" border="0" bordercolor="#9c9b9b" cellpadding="10" cellspacing="0" style="font-size:12px;">
<tr>
<td>Passenger Name</td>
<td>Seat Number</td>
<td>Gender(Age)</td>
</tr>';
$SeatDiaplay = '';
foreach($booking_customer_details as $key => $value)
{
	if($value!=""){
		$message.='<tr>
<td><strong>'. $value['name'].' </strong></td>
<td><strong>'.$value['seat_no'].' </strong></td>
<td><strong> '. $value['gender'].'('.$value['age'].') </strong></td>
</tr>';
		$SeatDiaplay .= $value['seat_no'].',';
	}
}
$message.='</table>
    <div style="border-bottom:#666 1px dashed"></div>'; 
$message.='<table width="100%" border="0" cellpadding="10" style="font-size:12px;">
<tr>
<td width="15%">Bus Operator</td>
<td width="15%">Bus Type</td>
<td width="10%">Seat Number </td>
<td width="15%">Departure Time</td>
<td width="25%">Boarding point</td>
</tr>
<tr>
<td><strong>'.$booking_itinerary_details[0]['bus_type'].'</strong></td>
<td><strong>'.$booking_itinerary_details[0]['operator'].'</strong></td>
<td>';
//foreach($inventoryItems as $intvItem1){
$message.='<strong>'.substr($SeatDiaplay, 0, -1).'&nbsp;&nbsp;&nbsp;&nbsp;'.'</strong>';
//}
$message.='</td>
<td><strong>'.$booking_itinerary_details[0]['departure_datetime'].'</strong></td>
<td><strong>Location: </strong>'.$booking_itinerary_details[0]['boarding_from'].'</td>
</tr>
</table>
<div style="border-bottom:#666 1px dashed"></div>  
<table width="100%" border="0" bordercolor="#9c9b9b" cellpadding="10" cellspacing="0" style="font-size:12px; margin-top:5px;">
<tr>
<td width="15%">Booking Date</td>
<td width="15%">From</td>
<td width="15%">To</td>
<td width="15%">Ticket No</td>
<td width="15%">PNR</td>
</tr>
<tr>
<td><strong>'.$booking_details['created_datetime'].'</strong></td>
<td><strong>'.$booking_itinerary_details[0]['departure_from'].'</strong></td>
<td><strong>'.$booking_itinerary_details[0]['arrival_to'].'</strong></td>
<td><strong>'.$booking_details['ticket'].'</strong></td>
<td><strong>'.$booking_details['pnr'].'</strong></td>
    
</tr>
<tr>
<td colspan="10">
<div style="width:100%; height:20px; text-align:right; border-top:#666 solid 1px; padding-top:10px; font-size:15px;"><strong>Total Fare: '.$booking_details['currency'].' '.($booking_details['total_fare']+$booking_details['domain_markup']+$booking_details['level_one_markup']).'/-</strong></div>
</td>
</tr>
</table>  
<table width="100%" border="0" cellpadding="5" cellspacing="0">

<tr>
<td>&nbsp;</td>
</tr>
</table>
<table width="100%" border="0" cellpadding="5" cellspacing="0">
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
</ol></td>
<td width="53%" valign="top" style="font-size:12px; font-family:Arial, Helvetica, sans-serif; line-height:18px; text-align:justify; padding:10px;">

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
if($button == ACTIVE){
		$message.='
<div style="margin-bottom: 10px;" colspan="3" align="center" class="button-wrapper"><input width="135" type="button" height="28" value="Print" alt="print_ticket" class="button_big blue" onClick="window.print(); return false;" style="cursor:pointer;"/>&nbsp;
<button width="135" type="button" height="28" alt="print_ticket" class="button_big blue"  style="cursor:pointer;"><a href="'.base_url().'">Close</a></button></div>
</div></div></div>';
}
echo $message;
?>