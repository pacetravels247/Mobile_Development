<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['master_module_list']	= array(	
	META_ACCOMODATION_COURSE => 'hotel',
	META_AIRLINE_COURSE => 'flight',	
	META_BUS_COURSE => 'bus',
	META_PACKAGE_COURSE => 'package',
	META_SIGHTSEEING_COURSE=>'activities',
	META_CAR_COURSE => 'car',
	META_TRANSFERV1_COURSE=>'transfers'

);
$config['sides'] = array('b2b'=>"B2B", 'b2c'=>"B2C");
/******** Current Module ********/
$config['current_module'] = 'admin';

$config ['bitla_bus_test'] = array (
		'api_url' => 'http://apistaging.ticketsimply.com/',
		'api_key' => 'TSYLIEAPI51336037',
		'test_username' => 'api.pacetravel_dir',
		'test_password' => 'pace@123'
	);

$config ['vrl_bus_test'] = array (
		'api_url' => 'http://61.0.236.133/vrltest/vrlWebService/BookingWebService.asmx',
		'test_username' => 'PACETRAVELS',
		'test_password' => 'PACE@vrltest'
	);
$config ['ets_bus_test'] = array (
		'api_url' => 'http://test.etravelsmart.com/etsAPI/api/',
		'test_username' => 'Pacetravels',
		'test_password' => 'pace@169'
	);

//REZLIVE_HOTEL test
$config ['rezlive_hotel_test'] = array (
		'endpoint_url' => 'http://test.xmlhub.com/testpanel.php/action',
		'user_code' => 'X31663',
		'username' => 'pacetravelsxml',
		'password' => 'ali@ahil'
	);

$config ['tbo_flight_test'] = array (
		'api_url' => 'http://api.tektravels.com/SharedServices/SharedData.svc/',
		'client_id' => 'ApiIntegrationNew',
		'username' => 'pacetravels',
		'password' => 'pace@1234',
		'token_agency_id' => '556',
		'token_member_id' => '494',
		'end_user_ip' => '127.0.0.1'
	);

//kukkeshree Live : do not book.
$config ['kukkeshree_bus_live'] = array (
		'api_url' => 'http://kks.kukkeshreetravels.com',
		'api_key' => 'TSLVWEAPI67585874',
	);

//krl Live : do not book.
$config ['krl_bus_live'] = array (
		'api_url' => 'http://krlt.krltravels.com',
		'api_key' => 'TSGCVUAPI34413325',
	);