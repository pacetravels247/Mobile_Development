<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['master_module_list']	= array(
META_AIRLINE_COURSE => 'flight',
META_TRANSFERS_COURSE => 'transferHotel',
META_BUS_COURSE => 'bus',
META_ACCOMODATION_COURSE => 'hotel',
META_TRANSFERV1_COURSE=>'transfers',
META_SIGHTSEEING_COURSE=>'activities',
META_CAR_COURSE=>'car',
META_PACKAGE_COURSE => 'package'
);
/******** Current Module ********/
$config['current_module'] = 'b2b';

$config['verify_domain_balance'] = true;

/******** PAYMENT GATEWAY START ********/
//To enable/disable PG
$config['enable_payment_gateway'] = true;
$config['active_payment_gateway'] = 'TECHP'; //PAYTM / TECHP / PAYU
$config['active_payment_system'] = 'test';//test/live 
$config['payment_gateway_currency'] = 'INR';//INR
/******** PAYMENT GATEWAY END ********/

/**
 * 
 * Enable/Disable caching for search result
 */
$config['cache_hotel_search'] = true;
$config['cache_sightseeing_search'] = true;
$config['cache_flight_search'] = false;
$config['cache_bus_search'] = true;
$config['cache_car_search'] = false;

/**
 * Number of seconds results should be cached in the system
 */
$config['cache_hotel_search_ttl'] = 600;
$config['cache_flight_search_ttl'] = 1900;
$config['cache_bus_search_ttl'] = 3600;
$config['cache_car_search_ttl'] = 300;
$config['cache_sightseeing_search_ttl'] = 300;

$config['cache_hotel_details_ttl'] = 600;



/*$config['lazy_load_hotel_search'] = true;*/
$config['hotel_per_page_limit'] = 20;
$config['car_per_page_limit'] = 200;
$config['star_api_system'] = 'Test';
/*
	search session expiry period in seconds
*/
$config['flight_search_session_expiry_period'] = 600;
$config['flight_search_session_expiry_alert_period'] = 300;


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
$config ['star_air_test'] = array (
		'starusername' => 'PACEOTA',
		'starpassword' => 'R76k98iTjwy45F9',
		'starairtarget' => 'Test',
		'starairapiversion' => '1.04',
		'starapikey' => '1008/06B9180602633B447F8ADCBAED1B618414',
		'star_api_url' => 'https://ogtest.zapways.com:4433/AirAPI/V1.04/OTAAPI.asmx',
		'star_availability_url' => 'http://zapways.com/air/ota/AirLowFareSearch',
		'star_block_url' => 'http://zapways.com/air/ota/AirBook',
		'star_confirm_url' => 'http://zapways.com/air/ota/AirDemandTicket'
	);
$config ['app_display_currency_preference'] = 'INR';



//REZLIVE_HOTEL test
$config ['rezlive_hotel_test'] = array (
		'endpoint_url' => 'http://test.xmlhub.com/testpanel.php/action',
		'user_code' => 'X31663',
		'username' => 'pacetravelsxml',
		'password' => 'ali@ahil'
	);

//These are needed for advance search to allow LCC & GDS flight search
$config ['lcc_apis'] = array ("PTBSID0000000002", "PTBSID0000000009", "PTBSID0000000012", "PTBSID0000000011");

$config ['gds_apis'] = array ("PTBSID0000000010");

//kukkeshree Live : do not book in local.
$config ['kukkeshree_bus_live'] = array (
		'api_url' => 'http://kks.kukkeshreetravels.com',
		'api_key' => 'TSLVWEAPI67585874',
	);

//krl Live : do not book in local.
$config ['krl_bus_live'] = array (
		'api_url' => 'http://krlt.krltravels.com',
		'api_key' => 'TSGCVUAPI34413325',
	);
