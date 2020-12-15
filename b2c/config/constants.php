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
define('FILE_READ_MODE',  0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE',   0755);
define('DIR_WRITE_MODE',  0777);
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


define('PROJECT_PREFIX', 'TM');//Prefix Used In Project - User ID, Voucher ID
define('PROJECT_NAME','Pace Travels');
define('CURRENT_DOMAIN_KEY', 'TMX1512291534825461'); // FIXME//Vc8Z7XLsyys9TEQdFPkV
define('PROJECT_COOKIE_NAME', 'travels');//FIXME

define('DEFAULT_TEMPLATE', 'template_v1');
define('LOGIN_POINTER', 'LID');
define('AUTH_USER_POINTER', 'AID');
define('DOMAIN_AUTH_ID', 'domain_auth_id');
define('DOMAIN_KEY', 'domain_key');
define('PHONE', 'phone');
define('EMAIL', 'email');
/*
 |--------------------------------------------------------------------------
 | Folder Names
 |--------------------------------------------------------------------------
 |--------------------------------------------------------------------------
 | Application Folder Names
 |--------------------------------------------------------------------------
 */
if (ENVIRONMENT == 'development') {
	$project_parent_folder = '';
        $extras_folder = 'extras';
	$extras_folder_realpath = '../'.$extras_folder;
} elseif (ENVIRONMENT == 'production') {
	$project_parent_folder = '';
} else {
	$project_parent_folder = '';
}



if (empty($project_parent_folder) == false) {
	define('APP_ROOT_DIR', '/'.$project_parent_folder); //main folder which wraps complete application
	define('PROJECT_COOKIE_PATH', '/'.$project_parent_folder.'/');
} else {
	define('APP_ROOT_DIR', ''); //main folder which wraps complete application
	define('PROJECT_COOKIE_PATH', '/');
}

define('MODULE_ROOT_DIR', ''); //main folder which holds current module
define('PROJECT_URI', APP_ROOT_DIR.MODULE_ROOT_DIR);

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
define('SYSTEM_TEMPLATE_LIST_RELATIVE_PATH', realpath('extras').'/system/template_list');

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
define('TEMPLATE_AUDIO_DIR', '/audio/');
define('TEMPLATE_JS_DIR', '/javascript/');
define('TEMPLATE_IMAGE_DIR', '/images/');
/*
 |--------------------------------------------------------------------------
 | DOMAIN SPECIFIC CONSTANTS
 |--------------------------------------------------------------------------
 */

define('DOMAIN_IMAGE_DIR', CUSTOM_RESOURCE_DIR.'/'.CURRENT_DOMAIN_KEY.'/images/');
define('DOMAIN_IMAGE_UPLOAD_DIR', realpath('extras').'/custom/'.CURRENT_DOMAIN_KEY.'/images/');
define('DOMAIN_UPLOAD_DIR', CUSTOM_RESOURCE_DIR.'/'.CURRENT_DOMAIN_KEY.'/uploads/');
define('DOMAIN_PCKG_UPLOAD_DIR', CUSTOM_RESOURCE_DIR.'/'.CURRENT_DOMAIN_KEY.'/uploads/packages/');


define('DOMAIN_TMP_DIR', CUSTOM_RESOURCE_DIR.'/'.CURRENT_DOMAIN_KEY.'/tmp/');
define('DOMAIN_TMP_UPLOAD_DIR', realpath('extras').'/custom/'.CURRENT_DOMAIN_KEY.'/tmp/');
define('DOMAIN_BAN_UPLOAD_DIR', realpath('system').'/template_list/template_v1/images/');
define('DOMAIN_TOP_AIRLINE_IMAGE_DIR', SYSTEM_RESOURCE_DIR.'/template_list/template_v3/images/top_airlines/');
define('DOMAIN_TOUR_STYLE_IMAGE_DIR', SYSTEM_RESOURCE_DIR.'/template_list/template_v3/images/tourstyles/');

// echo DOMAIN_BAN_UPLOAD_DIR;exit;
/*
 |--------------------------------------------------------------------------
 PDF
 |--------------------------------------------------------------------------
 */
//define('DOMAIN_PDF_DIR', CUSTOM_RESOURCE_DIR.'/'.CURRENT_DOMAIN_KEY.'/temp_booking_data_pdf/');
//define ('DOMAIN_PDF_DIR', '../extras/custom/'.CURRENT_DOMAIN_KEY.'/temp_booking_data_pdf/',true);
	define ('DOMAIN_PDF_DIR', $_SERVER['DOCUMENT_ROOT'].'/extras/custom/'.CURRENT_DOMAIN_KEY.'/temp_booking_data_pdf/',true);
/*
 |--------------------------------------------------------------------------
 | PAGE CONFIGURATION
 |--------------------------------------------------------------------------
 */
define('CUSTOM_FOLDER_PREFIX', '');

define('CORE_PAGE_CONFIGURATIONS', CUSTOM_FOLDER_PREFIX.'b2c/views/page_configuration/');
define('COMMON_JS', CUSTOM_FOLDER_PREFIX.'b2c/views/page_configuration/resources/common.php');
define('DATEPICKER_JS', CUSTOM_FOLDER_PREFIX.'b2c/views/page_configuration/resources/datepicker.php');
define('COMMON_UI_JS', CUSTOM_FOLDER_PREFIX.'b2c/views/page_configuration/resources/common_ui_js.php');
define('COMMON_SHARED_CSS_RESOURCE', CUSTOM_FOLDER_PREFIX.'b2c/views/page_configuration/resources/header_css_resource.php');
define('COMMON_SHARED_JS_RESOURCE', CUSTOM_FOLDER_PREFIX.'b2c/views/page_configuration/resources/header_js_resource.php');

define('COMMON_SHARED_FOOTER_JS_RESOURCE', CUSTOM_FOLDER_PREFIX.'b2c/views/page_configuration/resources/footer_js_resource.php');
define('ENUM_DATA_DIR', CUSTOM_FOLDER_PREFIX.'b2c/custom/enumeration/');
define('DATATYPE_DIR', CUSTOM_FOLDER_PREFIX.'b2c/custom/data_type/');
define('COMMON_SHARED_JS', CUSTOM_FOLDER_PREFIX.'b2c/views/page_configuration/shared_js/');
define('DOMAIN_CONFIG', CUSTOM_FOLDER_PREFIX.'b2c/custom/domain_config/');
define('HTTP_HOST_NAME', "http://".$_SERVER["HTTP_HOST"]);
define('SYSTEM_IMAGE_FULL_PATH_TO_EXTRA', HTTP_HOST_NAME.$project_parent_folder);
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

define('YOUTH_DATE_PICKER','5');//Sightseeing
define('SENIOR_DATE_PICKER','4');//Sightseeing

define('FUTURE_DATE_DISABLED_MONTH', '8');
define('FUTURE_DATE_SINGLE_MONTH', '9');
define('CARADULT_DATE_PICKER', '10');

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
define('C_MRS_TITLE', 5);
define('A_MASTER',6);

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
//define('META_TRANSFERS_COURSE', 'VHCID1420613763');
define('META_ACCOMODATION_COURSE', 'VHCID1420613748');
define('META_BUS_COURSE', 'VHCID1433498307');
define('META_PACKAGE_COURSE', 'VHCID1433498322');
define('META_TRANSFER_COURSE', 'VHCID1433496655');
define('META_SIGHTSEEING_COURSE','TMCID1524458882');
define('META_CAR_COURSE','TMCAR1433491849');
define('META_TRANSFERV1_COURSE','TMVIATID1527240212');
define('META_INSURANCE_COURSE','TTAGINS15741283692');
/*
 |--------------------------------------------------------------------------
 Booking source of Course Type
 |--------------------------------------------------------------------------
 */
define('PROVAB_HOTEL_BOOKING_SOURCE', 'PTBSID0000000001');
define('REZLIVE_HOTEL', 'PTBSID0000000034');
define('PROVAB_FLIGHT_BOOKING_SOURCE', 'PTBSID0000000002');
define('PROVAB_BUS_BOOKING_SOURCE', 'PTBSID0000000003');
define('PROVAB_SIGHTSEEN_BOOKING_SOURCE', 'PTBSID0000000006');
define('PROVAB_CAR_BOOKING_SOURCE', 'PTBSID0000000007');
define('PROVAB_TRANSFERV1_BOOKING_SOURCE','PTBSID0000000008');

define('TRAVELPORT_ACH_BOOKING_SOURCE','PTBSID0000000009');
define('TRAVELPORT_GDS_BOOKING_SOURCE','PTBSID0000000010');
define('SPICEJET_BOOKING_SOURCE','PTBSID0000000011');
define('STAR_BOOKING_SOURCE','PTBSID0000000012');
define('INDIGO_BOOKING_SOURCE','PTBSID0000000013');
define('PROVAB_PACKAGE_BOOKING_SOURCE','PTBSID0000000014');
define('BITLA_BUS_BOOKING_SOURCE', 'PTBSID0000000031');
define('VRL_BUS_BOOKING_SOURCE', 'PTBSID0000000032');
define('ETS_BUS_BOOKING_SOURCE', 'PTBSID0000000033');
define('KUKKESHREE_BUS_BOOKING_SOURCE', 'PTBSID0000000035');
define('KRL_BUS_BOOKING_SOURCE', 'PTBSID0000000036');
define('SRS_BUS_BOOKING_SOURCE', 'PTBSID0000000037');
/*
 |--------------------------------------------------------------------------
 TBO SPECIFIC CONSTANTS
 |--------------------------------------------------------------------------
 */
define('LCC_BOOKING', 'process_fare_quote');
define('NON_LCC_BOOKING', 'process_fare_quote');//In V10, FareQuote is Mandatoy For Both LCC and NON-LCC FLIGHTS

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
define('SIGHTSEEING_BOOKING','SB');
define('TRANSFER_BOOKING','TB');
define('CAR_BOOKING', 'CB');
define('MAX_BUS_SEAT_BOOKING', 6);
define('PACKAGE_BOOKING','PB');

/*
 |--------------------------------------------------------------------------
 | Email ID Constants
 |--------------------------------------------------------------------------
 */
define('GENERAL_EMAIL', 1);
/*
 |--------------------------------------------------------------------------
 | SMS Constants for SMS Integration
 |--------------------------------------------------------------------------
 */
define('GENERAL_SMS', 1);
/*
 |--------------------------------------------------------------------------
 | Social Login Constants for Social Login Integration
 |--------------------------------------------------------------------------
 */
define('GENERAL_SOCIAL', 1);

define('LAZY_IMG_ENCODED_STR', '');
/*
 |--------------------------------------------------------------------------
 | Report Filters
 |--------------------------------------------------------------------------
 */
define('FILTER_PLANNED_BOOKING', 'FILTER_PLANNED_BOOKING');
define('FILTER_COMPLETED_BOOKING', 'FILTER_COMPLETED_BOOKING');
define('FILTER_CANCELLED_BOOKING', 'FILTER_CANCELLED_BOOKING');

/*AES Encryption Key*/
define('PROVAB_ENC_KEY','0x6211e4df763ac394df2bd2a84fa7fbebfa6797f939f846de4e2cd1bf2c00f587');
define('PROVAB_MD5_SECRET','14c374552fa9b2b1d64c4799698cf0f4');
define('PROVAB_SECRET_IV','fdbe2d90bb96e6c334dc1eb308985f9e');


/* End of file constants.php */
/* Location: ./application/config/constants.php */
