<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 |--------------------------------------------------------------------------
 | File and Directory Modes
 |--------------------------------------------------------------------------
 |
 | These prefs are used when checking and setting modes when working
 | with the file system.  The defaults are fine on servers with proper
 | security, but you may wish (or even need) to change the values in
 | certain environments (Apache running a separate process for each
 | user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
 | always be used to set the mode correctly.
 |
 */
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);
/*
 |--------------------------------------------------------------------------
 | File Stream Modes
 |--------------------------------------------------------------------------
 |
 | These modes are used when working with fopen()/popen()
 |
 */
define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');
/*
 |--------------------------------------------------------------------------
 | Constant Used In Application
 |--------------------------------------------------------------------------
 |--------------------------------------------------------------------------
 | Active Module Name
 |--------------------------------------------------------------------------
 |
 | This is the current module which is active
 |
 */
define('PROJECT_TITLE', 'PROAPP NG');
define('PROJECT_SHORT_TITLE', 'NG');
define('ENDORSED_CURRENT_MODULE', 1);
define('LOGIN_POINTER', 'ALID');
define('AUTH_USER_POINTER', 'AUID');
define('HEADER_TITLE_SUFFIX', '- Provab Dev');
define('SECURE_EMAIL', 'arjun.provab@gmail.com');
define('SECURE_PASSWORD', 'arjun.provab@gmail.com');
define('PROJECT_PREFIX', 'VH');
define('DEFAULT_TEMPLATE', 'template_v1');
/* define('CURRENT_DOMAIN_KEY', '192.168.0.25'); */
define('DOMAIN_AUTH_ID', 'domain_auth_id');
define('DOMAIN_KEY', 'domain_key');
/*
 |--------------------------------------------------------------------------
 | Folder Names
 |--------------------------------------------------------------------------
 |--------------------------------------------------------------------------
 | Application Folder Names
 |--------------------------------------------------------------------------
 */


define('APP_ROOT_DIR', '/service'); //main folder which wraps complete application

define('MODULE_ROOT_DIR', '/webservices'); //main folder which holds current module
define('PROJECT_URI', APP_ROOT_DIR.MODULE_ROOT_DIR);
define('PROJECT_COOKIE_NAME', 'webservices');
define('PROJECT_COOKIE_PATH', '/provab/webservices/');



/*
 |--------------------------------------------------------------------------
 | RESOURCE FOLDERS
 |--------------------------------------------------------------------------
 */
define('RESOURCE_DIR', APP_ROOT_DIR.'/extras'); //main folder which holds all the resource
define('SYSTEM_RESOURCE_DIR', RESOURCE_DIR.'/system');
define('CUSTOM_RESOURCE_DIR', RESOURCE_DIR.'/custom');
define('SYSTEM_RESOURCE_LIBRARY', SYSTEM_RESOURCE_DIR.'/library'); //complete application library storage
define('SYSTEM_TEMPLATE_LIST', SYSTEM_RESOURCE_DIR.'/template_list');//complete application template storage
define('SYSTEM_TEMPLATE_LIST_RELATIVE_PATH', '../'.SYSTEM_TEMPLATE_LIST);
/*
 |--------------------------------------------------------------------------
 | EXTRAS LIBRARY FOLDERS
 |--------------------------------------------------------------------------
 */
define('BOOTSTRAP_JS_DIR', SYSTEM_RESOURCE_LIBRARY.'/bootstrap/js/');
define('BOOTSTRAP_CSS_DIR', SYSTEM_RESOURCE_LIBRARY.'/bootstrap/css/');
define('SYSTEM_IMAGE_DIR', SYSTEM_RESOURCE_LIBRARY.'/images/');
define('GRAPH_SCRIPT', SYSTEM_RESOURCE_LIBRARY.'/Highcharts/js/');
define('JAVASCRIPT_LIBRARY_DIR', SYSTEM_RESOURCE_LIBRARY.'/javascript/');
define('JQUERY_UI_LIBRARY_DIR', JAVASCRIPT_LIBRARY_DIR.'jquery-ui-1.11.2.custom/');
define('DATEPICKER_LIBRARY_DIR', SYSTEM_RESOURCE_LIBRARY.'/datetimepicker/');

/*
 |--------------------------------------------------------------------------
 | THEME TEMPLATE LIBRARY FOLDERS
 |--------------------------------------------------------------------------
 */
define('TEMPLATE_CSS_DIR', '/css/');
define('TEMPLATE_JS_DIR', '/javascript/');
define('TEMPLATE_IMAGE_DIR', '/images/');

/*
 |--------------------------------------------------------------------------
 | DOMAIN SPECIFIC CONSTANTS
 |--------------------------------------------------------------------------
 */
/* define('DOMAIN_IMAGE_DIR', CUSTOM_RESOURCE_DIR.'/'.CURRENT_DOMAIN_KEY.'/images/');
define('DOMAIN_UPLOAD_DIR', CUSTOM_RESOURCE_DIR.'/'.CURRENT_DOMAIN_KEY.'/uploads/'); */
/*
 |--------------------------------------------------------------------------
 | PAGE CONFIGURATION
 |--------------------------------------------------------------------------
 */
define('CORE_PAGE_CONFIGURATIONS', '../'.MODULE_ROOT_DIR.'/application/views/page_configuration/');
define('COMMON_JS', '../'.MODULE_ROOT_DIR.'/application/views/page_configuration/resources/common.php');
define('DATEPICKER_JS', '../'.MODULE_ROOT_DIR.'/application/views/page_configuration/resources/datepicker.php');
define('COMMON_UI_JS', '../'.MODULE_ROOT_DIR.'/application/views/page_configuration/resources/common_ui_js.php');
define('COMMON_SHARED_CSS_RESOURCE', '../'.MODULE_ROOT_DIR.'/application/views/page_configuration/resources/header_css_resource.php');
define('COMMON_SHARED_JS_RESOURCE', '../'.MODULE_ROOT_DIR.'/application/views/page_configuration/resources/header_js_resource.php');
define('ENUM_DATA_DIR', '../'.MODULE_ROOT_DIR.'/application/custom/enumeration/');
define('DATATYPE_DIR', '../'.MODULE_ROOT_DIR.'/application/custom/data_type/');
define('COMMON_SHARED_JS', '../'.MODULE_ROOT_DIR.'/application/views/page_configuration/shared_js/');
define('DOMAIN_CONFIG', '../'.MODULE_ROOT_DIR.'/application/custom/domain_config/');

/*
 |--------------------------------------------------------------------------
 | IMAGE SIZE
 |--------------------------------------------------------------------------
 */
define('PANEL_WRAPPER', 'panel-primary');

/*
 |--------------------------------------------------------------------------
 | IMAGE SIZE
 |--------------------------------------------------------------------------
 */
define('THUMBNAIL', 1);

/*
 |--------------------------------------------------------------------------
 | Status codes used in application
 |--------------------------------------------------------------------------
 */
define('INACTIVE', 0);
define('FAILURE_STATUS', 0);
define('QUERY_FAILURE', 0);
define('ACTIVE', 1);
define('SUCCESS_STATUS', 1);
define('QUERY_SUCCESS', 1);
define('PENDING', 1);
define('ACCEPTED', 2);
define('DECLINED', 3);
define('SUCCESS_MESSAGE', 0);
define('ERROR_MESSAGE', 1);
define('WARNING_MESSAGE', 2);
define('INFO_MESSAGE', 3);

define('BOOKING_CONFIRMED', 1);//Booking completed
define('BOOKING_HOLD', 2);//Booking on hold
define('BOOKING_CANCELLED', 3);//Booked and cancelled
define('BOOKING_ERROR', 4);//unable to continue booking
define('BOOKING_INCOMPLETE', 5);//left in between
define('BOOKING_VOUCHERED', 6);//left in between
define('BOOKING_PENDING', 7);//left in between
define('BOOKING_FAILED', 8);//left in between
define('BOOKING_INPROGRESS', 9);//Booking is processing


/*
 |--------------------------------------------------------------------------
 | Type Of Markup Supported in application
 |--------------------------------------------------------------------------
 */
define('GENERIC', 0);
define('SPECIFIC', 1);
define('MARKUP_VALUE_PERCENTAGE', 0);
define('MARKUP_VALUE_MONEY', 1);
define('B2C_FLIGHT', 1);
define('B2C_HOTEL', 2);
define('B2C_CAR', 3);
define('MARKUP_CURRENCY', 'INR');

/*
 |--------------------------------------------------------------------------
 | Currency
 |--------------------------------------------------------------------------
 */
define('UNIVERSAL_DEFAULT_CURRENCY', 'INR'); // INR
define('COURSE_LIST_DEFAULT_CURRENCY', 22); // INR
define('COURSE_LIST_DEFAULT_CURRENCY_VALUE', 'INR');
define('COURSE_LIST_DEFAULT_CURRENCY_SYMBOL', '&#8377;');

/*
 |--------------------------------------------------------------------------
 | Application USER LIST
 |--------------------------------------------------------------------------
 */
define('AUTO_SYSTEM', 0);
define('ADMIN', 1);
define('SUB_ADMIN', 2);
define('B2B_USER', 3);
define('B2C_USER', 4);
define('B2E_USER', 5);
define('CALL_CENTER_USER', 6);

/*
 |--------------------------------------------------------------------------
 | Application PAGINATION
 |--------------------------------------------------------------------------
 */
define('RECORDS_RANGE_1', 10);
define('RECORDS_RANGE_2', 20);
define('RECORDS_RANGE_3', 50);


/*
 |--------------------------------------------------------------------------
 | Application Booking Engine Data Source
 |--------------------------------------------------------------------------
 */
define('FLIGHT_CRS', 0);
define('HOTEL_CRS', 1);
define('TRANSFER_CRS', 2);
define('HOLIDAY_CRS', 0);
define('SIGHTSEEING_CRS', 0);
define('RECHARGE_CRS', 0);
define('ACTIVITY_CRS', 50);
define('DMC_CRS', 8);
define('OTHER_BOOKING_SOURCE', 6);

define('FLIGHT_API',00);
define('HOTEL_API', 'ESHB');
define('HOLIDAY_API', 00);
define('TRANSFER_API', 00);
define('SIGHTSEEING_API', 00);
define('RECHARGE_API', 00);

/*
 |--------------------------------------------------------------------------
 | DATE TYPES
 |--------------------------------------------------------------------------
 */
define('PAST_DATE', '0');
define('FUTURE_DATE', '1');
define('PAST_DATE_TIME', '2');
define('FUTURE_DATE_TIME', '3');
define('ENABLE_MONTH', '4');
define('ADULT_DATE_PICKER', '5');
define('CHILD_DATE_PICKER', '6');
define('INFANT_DATE_PICKER', '7');


/*
 |--------------------------------------------------------------------------
 | Location TYPES
 |--------------------------------------------------------------------------
 */
define('CONTINENT_ZLOCATION', 'continent');
define('COUNTRY_ZLOCATION', 'country');
define('CITY_ZLOCATION', 'city');
define('EVENT_TEMPLATE', 'event');
define('GENERAL_TEMPLATE', 'general');

/*
 |--------------------------------------------------------------------------
 | User Title TYPES
 |--------------------------------------------------------------------------
 */
define('MR_TITLE', 1);
define('MRS_TITLE', 2);
define('MISS_TITLE', 3);
define('MASTER_TITLE', 4);

/*
 |--------------------------------------------------------------------------
 Country AND City Code
 |--------------------------------------------------------------------------
 */
define('INDIA_CODE', 92);
define('INDIA_COUNTRY_CODE', +91);
define('ISO_INDIA', 'IN');

/*
 |--------------------------------------------------------------------------
 Meta Course Type
 |--------------------------------------------------------------------------
 */
define('META_AIRLINE_COURSE', 'VHCID1420613784');
define('META_TRANSFERS_COURSE', 'VHCID1420613763');
define('META_ACCOMODATION_COURSE', 'VHCID1420613748');
define('META_BUS_COURSE', 'VHCID1433498307');
define('META_PACKAGE_COURSE', 'VHCID1433498322');
define ( 'META_SMS_GATEWAY', 'SMS19910215100315' );
define('META_SIGHTSEEING_COURSE','SSID17042018001');
define('META_VIATOR_TRANSFER_COURSE','VTID26052018002');
/*
 |--------------------------------------------------------------------------
 Booking source of Course Type
 |--------------------------------------------------------------------------
 */
define('TBO_HOTEL_BOOKING_SOURCE', 'PTBSID0000000001');//Hotel
define('TBO_FLIGHT_BOOKING_SOURCE', 'PTBSID0000000002');//TBO Flight
define('TRAVELYAARI_BUS_BOOKING_SOURCE', 'PTBSID0000000003');//Bus
define ('MYSTIFLY_FLIGHT_BOOKING_SOURCE', 'PTBSID0000000004' ); // MYSTIFLY Flight
define ('V3SMS_BOOKING_SOURCE', 'PTBSID0000000005' ); // V3 SMS Gateway
define ('TRAVELPORT_FLIGHT_BOOKING_SOURCE', 'PTBSID0000000007' ); // TravelPort Flight
define ('TRAVELPORT_LCC_FLIGHT_BOOKING_SOURCE', 'PTBSID00000000071' ); // TravelPort Flight
define ('GRN_CONNECT_HOTEL_BOOKING_SOURCE', 'PTBSID0000000011' ); // GRN Connect Hotel API
define ('OYO_HOTEL_BOOKING_SOURCE', 'PTBSID0000000012' ); // OYO Hotel API

define('AGODA_HOTEL_BOOKING_SOURCE', 'PTBSID0000000016');//Agoda Hotel API
define('ETRAVELSMART_BUS_BOOKING_SOURCE', 'PTBSID0000000014');// etravel smart bus
define('SPICEJET_FLIGHT_BOOKING_SOURCE', 'PTBSID0000000023');//GOAIR Flight
define('SIGHTSEEING_BOOKING_SOURCE','PTBSID0000000019');//Viator Sightseeing
define('VIATOR_TRANSFER_BOOKING_SOURCE','PTBSID0000000022');//viator transfers
define('SABARE_FLIGHT_BOOKING_SOURCE', 'PTBSID0000000021');//Sabre Flight
/*
 |--------------------------------------------------------------------------
 TBO SPECIFIC CONSTANTS
 |--------------------------------------------------------------------------
 */
define('LCC_BOOKING', 'process_fare_quote');
define('NON_LCC_BOOKING', 'process_booking');

/*
 |--------------------------------------------------------------------------
 | Different Types Of Payment Methods supported in application
 |--------------------------------------------------------------------------
 */
define('PAY_NOW', 'PNHB1');
define('PAY_AT_BANK', 'PABHB2');

/*
 |--------------------------------------------------------------------------
 | Custom Provab Application Seperator used in the application
 |--------------------------------------------------------------------------
 */
define('DB_SAFE_SEPARATOR', '*_*');

define('HOTEL_BOOKING', 'HB');
define('FLIGHT_BOOKING', 'FB');
define('BUS_BOOKING', 'BB');

define('MAX_BUS_SEAT_BOOKING', 6);

/*
 |--------------------------------------------------------------------------
 | Email ID Constants
 |--------------------------------------------------------------------------
 */
define('GENERAL_EMAIL', 1);

/*
 |--------------------------------------------------------------------------
 | SMS Constants
 |--------------------------------------------------------------------------
 */
define('GENERAL_SMS', 1);
/*
 |--------------------------------------------------------------------------
 | Provab Authentication Constants
 |--------------------------------------------------------------------------
 */
define('AUTH_KEY_SEPARATOR', '___');
/*
 |--------------------------------------------------------------------------
 | COURSE VESRIONS
 |--------------------------------------------------------------------------
 */
define('FLIGHT_VERSION_1',	1);
define('HOTEL_VERSION_1', 	1);
define('BUS_VERSION_1', 	1);
define('SIGHTSEEING_VERSION_1',1);
define('FLIGHT_VERSION_2',	2);
define('HOTEL_VERSION_2', 	2);
define('HOTEL_VERSION_3', 	3);
define('BUS_VERSION_2', 	2);
define('VIATOR_TRANSFER_VERSION_1',1);
/*
 |--------------------------------------------------------------------------
 Cache Related Constants
 |--------------------------------------------------------------------------
 */

define('TBO_FLIGHT_CACHE_PATH','/flight_cache/tbo/');
define('TBO_HOTEL_CACHE_PATH','/hotel_cache/tbo/');
define('TRAVELYAARI_BUS_CACHE_PATH', '/bus_cache/travelyaari/');
/*Static image url*/
define('SERVER_IMAGE_PATH','http://demo.travelomatix.com/demo_alpha/new/cdn/');
/* End of file constants.php */
/* Location: ./application/config/constants.php */
