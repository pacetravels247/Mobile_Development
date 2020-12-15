<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['master_module_list']	= array(
	META_AIRLINE_COURSE => 'flight',
	META_TRANSFERS_COURSE => 'transfer',
	META_ACCOMODATION_COURSE => 'hotel',
	META_BUS_COURSE => 'bus',
	META_PACKAGE_COURSE => 'package',
	META_SIGHTSEEING_COURSE=>'sightseeing'
);

$config['verify_domain_balance'] = true;
$config['domain_user'] = true;

$config['alert_mobile_number'] = array('8050006336', '9916100864');

$config['alert_mobile_number_only_travelport'] = array('9565000044','9670000301');

$config['alert_email_id'] = 'yuvraj@travelomatix.com';
