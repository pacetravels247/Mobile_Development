<?php
/**
 * APPLICATION THEME
 * Themes available in the application
 */
$enums['theme_list'] = array('template_v1' => 'Default Theme');

$enums['room_type_list'] = array('SG' => 'Single', 'TW' => 'Twin', 'DB' => 'Double', 'TR' => 'Triple', 'QD' => 'Quad');

/**
 * Type of balance request methods integrated in the system
 */
$enums['provab_balance_requests'] = array(
	'CHECK___DD' => 'CHECK/DD',
	'ETRANSFER' => 'E-TRANSFER',
	'CASH' => 'Cash'
);

$enums['priority_list'] = array(
	'high' => 'High',
	'medium' => 'Medium',
	'low' => 'Low'
);

$enums['flight_type'] = array(
	'GDS' => 'GDS',
	'LCC' => 'LCC',
	'BOTH' => 'BOTH'
);

$enums['title'] = array(
1 => 'Mr',
2 => 'Ms',
3 => 'Miss',
4 => 'Master',
5 => 'Mrs'
);

$enums['status'] = array(
0 => 'Inactive',
1 => 'Active'
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