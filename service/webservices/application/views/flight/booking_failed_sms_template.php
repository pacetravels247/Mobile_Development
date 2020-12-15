<?php
$sms_message = '';
$sms_message .= 'Booking is in "'.booking_status_label_text($booking_transaction_details['status']).'" status for the client: '.$domain_name;
$sms_message .= ', AppReference: '.$booking_transaction_details['app_reference'];
if(!empty($booking_transaction_details['gds_pnr'])==true) {
$sms_message .= ', GDS PNR: '.$booking_transaction_details['gds_pnr'];
}
$sms_message .= ' ,check mail for more details.';
echo $sms_message;
?>