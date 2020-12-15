<?php
$sms_message = '';
$sms_message .= 'Ticket Cancellation Request By: '.$domain_name;
//$sms_message .= ', BookingAPI: '.$booking_transaction_details['booking_api_name'];
$sms_message .= ', BookingID: '.$booking_transaction_details['book_id'];
if(isset($booking_transaction_details['pnr']) == true && empty($booking_transaction_details['pnr']) == false){
	$sms_message .= ', PNR: '.$booking_transaction_details['pnr'];
}
//$sms_message .= ' AppReference: '.$booking_transaction_details['app_reference'];
$sms_message .= ' ,check mail for more details.';
echo $sms_message;
?>