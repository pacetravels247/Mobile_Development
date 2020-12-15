<?php
/**
 * APPLICATION THEME
 * Themes available in the application
 */
$enums['theme_list'] = array('template_v1' => 'Default Theme', 'template_v2' => 'X Theme', 'template_v3' => 'T Theme');

$enums['room_type_list'] = array('SG' => 'Single', 'TW' => 'Twin', 'DB' => 'Double', 'TR' => 'Triple', 'QD' => 'Quad');

/**
 * Type of balance request methods integrated in the system
 */
$enums['provab_balance_requests'] = array(
	'CHECK___DD' => 'CHEQUE/DD',
	'ETRANSFER' => 'E-TRANSFER',
	'CASH' => 'Bank Deposite', 
	'WALLET' => 'Wallet'
);
/**
 * Balance status details
 */
$enums['provab_balance_status'] = array('ACCEPTED' => 'accepted', 'REJECTED' => 'rejected', 'PENDING' => 'pending');
$enums['booking_status_options'] = array('BOOKING_CONFIRMED' => 'BOOKING_CONFIRMED','BOOKING_INPROGRESS'=>'BOOKING_INPROGRESS','BOOKING_HOLD' => 'BOOKING_HOLD','BOOKING_CANCELLED' => 'BOOKING_CANCELLED','BOOKING_ERROR' => 'BOOKING_ERROR','BOOKING_INCOMPLETE' => 'BOOKING_INCOMPLETE','BOOKING_VOUCHERED' => 'BOOKING_VOUCHERED','BOOKING_PENDING' => 'BOOKING_PENDING','BOOKING_FAILED' => 'BOOKING_FAILED');


$enums['priority_list'] = array(
	'high' => 'High',
	'medium' => 'Medium',
	'low' => 'Low'
);
$enums['title'] = array(
1 => 'Mr',
2 => 'Ms',
3 => 'Miss',
4 => 'Master'
);

$enums['status'] = array(
0 => 'Inactive',
1 => 'Active',
2 => 'Lock'
);

$enums['status_choice'] = array(
0 => 'No',
1 => 'Yes'
);

$enums['user_group'] = array('marketing_team', 'sales_team', 'zone_team', 'production_team', 'accounts_team', 'merchantize_team');

$enums['language_preference'] = array(
	'english' => 'English',
	'hindi' => 'Hindi'
);

$enums['location_type'] = array(
	'1' => 'continent',
	'2' => 'country',
	'3' => 'city'
);

$enums['payment_mode'] = array(
1 => 'Cash',
2 => 'Check',
3 => 'Deposit'
);

$enums['duration_type'] = array(
1 => 'day',
2 => 'week',
3 => 'month',
4 => 'year'
);

$enums['gender'] = array(
1 => 'Male',
2 => 'Female',
3 => 'Others'
);

$enums['sex'] = array(
1 => 'Male',
2 => 'Female'
);

$enums ['report_filter_status'] = array (
		'BOOKING_CONFIRMED' => 'Confirmed',
		'BOOKING_PENDING' => 'Pending',
		'BOOKING_CANCELLED' => 'Cancelled'
);
$curr = get_application_default_currency();
$enums['value_type'] = array(
'plus' => 'Plus(+ '.$curr.')',
'percentage' => 'Percentage(%)',
);
$enums['display_home_page'] = array(
'Yes' => 'Yes',
'No' => 'No',
);
$enums['refund_status'] = array(
'INPROGRESS' => 'INPROGRESS',
'PROCESSED' => 'PROCESSED',
'REJECTED' => 'REJECTED',
);
$enums['refund_paymode_mode'] = array(
'online' => 'Online',
'offline' => 'Offline'
);

$enums['transaction_type'] = array(
'flight' => 'Flight',
'hotel' => 'Hotel',
'bus' => 'Bus',
'transferv1' => 'Transfer',
'sightseeing' => 'Activity',
'transaction' => 'Transaction-(Deposits/Others)'
);

$enums['balance_debit_credit_reasons'] = array(
'Flight Booking' => 'Flight Booking',
'Hotel Booking' => 'Hotel Booking',
'Bus Booking' => 'Bus Booking',
'Payment Against Cancellation' => 'Payment Against Cancellation',
'Misc' => 'Misc.',
);
$enums ['month_names'] = array (
		0 => 'Jan',
		1 => 'Feb',
		2 => 'Mar',
		3 => 'Apr',
		4 => 'May',
		5 => 'Jun',
		6 => 'Jul',
		7 => 'Aug',
		8 => 'Sep',
		9 => 'Oct',
		10 => 'Nov',
		11 => 'Dec'
);

$enums['travel_type'] = array(
    'international' => 'International',
    'domestic' => 'Domestic'
);
$enums['flight_carrier'] = array(
    'lcc' => 'LCC',
    'gds' => 'GDS'
);
$enums['payment_method'] = array('pg' => 'Payment Gateway','cc' => 'Credit Card','cash' => 'Cash');
$enums['boc_request_status'] = array(
"Pending" => 'PENDING',
"Rejected" => 'REJECTED',
"Refunded" => 'REFUNDED'
);
$enums['pg_list'] = array(
'PAYTM' => 'PAYTM',
'TECHP' => 'TECHP'
);
$enums['quota_type'] = array(
'International' => 'International',
'Domestic' => 'Domestic'
);