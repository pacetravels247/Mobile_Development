<?php
$sms_message = '';
$sms_message .= 'Client '.$domain_name.' is requested to confirm the ticket';
$sms_message .= ', AppReference: '.$AppReference;
$sms_message .= ' ,check mail for more details.';
echo $sms_message;
?>