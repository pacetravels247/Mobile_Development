<?php
ob_start();
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
/**
 * Output data with header set to json
 *
 * @param array $data
 *        	Data to be output with json header
 */
function get_client_ip() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

function echo_json($data) {
	header ( 'Content-type:application/json' );
	echo json_encode ( $data );
	exit ();
}
function validate_user_login() {
	if (is_logged_in_user () == false) {
		redirect ( base_url () );
	}
}
function index_month_number($data)
{
	$imn_data = array();
	if (valid_array($data)) {
		foreach ($data as $k => $v) {
			$imn_data[($v['month_number']-1)] = $v;//Dont Change--Jaganath
			//$imn_data[$k] = $v;//In HighChart, Month starts with 0;
		}
	}
	return $imn_data;
}
// ---------------------------------------------------------- Active Module Start
/**
 * Check if current module is enabled
 */
function is_active_airline_module() {
	$status = false;
	if (is_domain_user () == false || in_array ( META_AIRLINE_COURSE, $GLOBALS ['CI']->active_domain_modules )) {
		$status = true;
	}
	return $status;
}

/**
 * Check if current module is enabled
 */
function is_active_hotel_module() {
	$status = false;
	if (is_domain_user () == false || in_array ( META_ACCOMODATION_COURSE, $GLOBALS ['CI']->active_domain_modules )) {
		$status = true;
	}
	return $status;
}




/**
 * Check if current module is enabled
 */
function is_active_bus_module() {
	$status = false;
	if (is_domain_user () == false || in_array ( META_BUS_COURSE, $GLOBALS ['CI']->active_domain_modules )) {
		$status = true;
	}
	return $status;
}
/**
 * Check if current module is enabled
 */
function is_active_sightseeing_module() {
	$status = false;
	if (is_domain_user () == false || in_array ( META_SIGHTSEEING_COURSE, $GLOBALS ['CI']->active_domain_modules )) {
		$status = true;
	}
	return $status;
}

/**
 * Check if current module is enabled
 */
function is_active_transferv1_module() {
	$status = false;
	if (is_domain_user () == false || in_array ( META_VIATOR_TRANSFER_COURSE, $GLOBALS ['CI']->active_domain_modules )) {
		$status = true;
	}
	return $status;
}


function roundoff_number($number)
{
	return round($number, 3);
	//return $number;
}

/**
 * Check if current module is enabled
 */
function is_active_package_module() {
	$status = false;
	if (is_domain_user () == false || in_array ( META_PACKAGE_COURSE, $GLOBALS ['CI']->active_domain_modules )) {
		$status = true;
	}
	return $status;
}
// ---------------------------------------------------------- Active Module End
// ---------------------------------------------------------- Load Library Start
/**
 * Load Hotel Lib based on hotel source
 *
 * @param string $source        	
 */
/*
function load_hotel_lib($source) {
	$CI = &get_instance ();
	switch ($source) {
		case TBO_HOTEL_BOOKING_SOURCE :
			$CI->load->library ( 'hotel/tbo_private', '', 'hotel_lib' );
			break;
		default :
			redirect ( base_url () );
	}
} */
function load_hotel_lib($source, $obj = '', $object_name_as_class = false) 
{

	$lib_name = '';
	$params = '';
	$CI = &get_instance ();

	$class_name = master_class_name ( $source );

	$folder_name = $class_name;
	$obj_name = ($object_name_as_class == true) ? $class_name : (empty ( $obj ) == false ? $obj : 'hotel_lib');
        
	$data = $CI->load->library ( 'hotel/' . $folder_name . '/' . $class_name, $params, $obj_name );
        
	return $obj_name;
}


function load_hotel_lib_v3($source, $obj = '', $object_name_as_class = false) 
{
  
	$lib_name = '';
	$params = '';
	$CI = &get_instance ();

	$class_name = master_class_name ( $source );
	$folder_name = $class_name;
	$obj_name = ($object_name_as_class == true) ? $class_name : (empty ( $obj ) == false ? $obj : 'hotel_lib');
       
	$data = $CI->load->library ( 'hotel/GRN/' . $folder_name . '/' . $class_name, $params, $obj_name );
      
	return $obj_name;
}
/**
*Load Sight seeing Library Viator
*/
function load_sightseen_lib($source, $obj = '', $object_name_as_class = false) 
{
   
	$lib_name = '';
	$params = '';
	$CI = &get_instance ();
	$class_name = master_class_name ( $source );

	$folder_name = $class_name;
        
	$obj_name = ($object_name_as_class == true) ? $class_name : (empty ( $obj ) == false ? $obj : 'sightseen_lib');
   
   
	$data = $CI->load->library ( 'sightseeing/' . $folder_name . '/' . $class_name, $params, $obj_name );
       
	return $obj_name;
}
/**
*Load Transfer Library Viator
*/
function load_viatortransfer_lib($source, $obj = '', $object_name_as_class = false) 
{
   
	$lib_name = '';
	$params = '';
	$CI = &get_instance ();
	$class_name = master_class_name ( $source );
        
	$folder_name = $class_name;
        
	$obj_name = ($object_name_as_class == true) ? $class_name : (empty ( $obj ) == false ? $obj : 'viatortransfer_lib');
   
   
	$data = $CI->load->library ( 'viatortransfer/' . $folder_name . '/' . $class_name, $params, $obj_name );
       
	return $obj_name;
}

/**
 * Load Hotel Lib based on hotel source
 *
 * @param string $source        	
 */
/*
function load_flight_lib($source) {
	$CI = &get_instance ();
	switch ($source) {
		case TBO_FLIGHT_BOOKING_SOURCE :
			$GLOBALS ['CI']->load->library ( 'flight/tbo_private', '', 'flight_lib' );
			break;
		default :
			redirect ( base_url () );
	}
}*/
function load_flight_lib($source, $obj = '', $object_name_as_class = false,$config_table_origin='') 
{
	$lib_name = '';
	$params[] = $config_table_origin;
	$CI = &get_instance ();
	$class_name = master_class_name ( $source );

	$folder_name = $class_name;
	$obj_name = ($object_name_as_class == true) ? $class_name : (empty ( $obj ) == false ? $obj : 'flight_lib');
	$data = $CI->load->library ( 'flight/' . $folder_name . '/' . $class_name, $params, $obj_name );
	return $obj_name;
}

/**
 * Load Bus Lib based on bus source
 *
 * @param string $source        	
 */
function load_bus_lib($source) {
	$CI = &get_instance ();
	switch ($source) {
		case TRAVELYAARI_BUS_BOOKING_SOURCE :
			$CI->load->library ( 'bus/travelyaari_private', '', 'bus_lib' );
			break;
		default :
			redirect ( base_url () );
	}
}
function load_bus_lib_new($source, $obj = '', $object_name_as_class = false) 
{

	$lib_name = '';
	$params = '';
	$CI = &get_instance ();
	$class_name = master_class_name ( $source );
	$folder_name = $class_name;
	$obj_name = ($object_name_as_class == true) ? $class_name : (empty ( $obj ) == false ? $obj : 'bus_lib');
	
	$data = $CI->load->library ( 'bus/' . $folder_name . '/' . $class_name, $params, $obj_name );
        
	return $obj_name;
}
// ---------------------------------------------------------- Load Library END
/**
 * * Arjun J Gowda
 * Get unserialized
 */
function unserialized_data($data, $data_key = false) {
	if (empty ( $data_key ) == true || md5 ( $data ) == $data_key) {
		$data = @unserialize ( base64_decode ( $data ) );
		if (valid_array ( $data ) != true) {
			$data = false;
		}
	} else {
		$data = false;
	}
	return $data;
}

/**
 * * Arjun J Gowda
 * Serialized
 */
function serialized_data($data) {
	return base64_encode ( serialize ( $data ) );
}

/**
 * Check if multiple or force to be multiple
 *
 * @param array $data
 *        	array/json object
 */
function force_multple_data_format($data) {
	if (is_object ( $data ) == true) {
		$mul_data = '';
		if (isset ( $data->{0} ) == false) {
			$mul_data->{0} = $data;
		} else {
			$mul_data = $data;
		}
	} elseif (is_array ( $data ) == true) {
		$mul_data = array();
		if (isset ( $data [0] ) == false) {
			$mul_data [0] = $data;
		} else {
			$mul_data = $data;
		}
	}
	return $mul_data;
}

/**
 * show label based on balance request status
 *
 * @param string $status        	
 */
function balance_status_label($status) {
	switch ($status) {
		case 'PENDING' :
			$status_label = 'label-info';
			break;
		case 'ACCEPTED' :
			$status_label = 'label-success';
			break;
		case 'REJECTED' :
			$status_label = 'label-danger';
			break;
	}
	return $status_label;
}

/**
 * show label based on balance request status
 *
 * @param string $status        	
 */
function booking_status_label($status) {
	switch ($status) {
		case 'BOOKING_PENDING' :
		case 'BOOKING_HOLD' :
			$status_label = 'label label-info';
			break;
		case 'BOOKING_CONFIRMED' :
			$status_label = 'label label-success';
			break;
		case 'BOOKING_ERROR' :
		case 'BOOKING_INCOMPLETE' :
		case 'BOOKING_CANCELLED' :
			$status_label = 'label label-danger';
			break;
		default :
			$status_label = 'label label-primary';
	}
	return $status_label;
}
/**
 * Refund Status Label
 * @param $status
 */
function refund_status_label($status)
{
	switch ($status) {
		case 'INPROGRESS':
			$status_label = 'label label-info';
			break;
		case 'PROCESSED':
			$status_label = 'label label-success';
			break;
		case 'REJECTED':
			$status_label = 'label label-danger';
			break;
		break;
		default : $status_label = 'label label-primary';
	}
	return $status_label;
}

/**
 * * Arjun J Gowda
 * check if the current user has privilege to view the web page
 *
 * @param $privilege_key unique
 *        	key which identifies privilege to access each page
 * @param $auto_redirect boolean
 *        	used to prevent auto redirection while checking web page access privilege
 */
function web_page_access_privilege($privilege_key, $auto_redirect = true) {
	return true;
}

/**
 * * Arjun J Gowda
 */
function get_default_image_loader() {
	return '<div class="data-utility-loader" style="display:none">
			Please Wait <img src="' . $GLOBALS ['CI']->template->template_images ( 'tiny_loader_v1.gif' ) . '" class="img-responsive center-block"></img>
		</div>';
}

/**
 * * Arjun J Gowda
 * check if the user is logged in or not
 */
function is_logged_in_user() {
	// echo "sadasd";
	// echo $GLOBALS ['CI']->entity_user_id;exit;
	if (isset ( $GLOBALS ['CI']->entity_user_id ) == true and intval ( $GLOBALS ['CI']->entity_user_id ) > 0) {
		return true;
	} else {
		return false;
	}
}
function is_app_user() {
	if (isset ( $GLOBALS ['CI']->entity_user_type ) == true and in_array ( intval ( $GLOBALS ['CI']->entity_user_type ), array (
			B2C_USER,
			B2B_USER 
	) )) {
		return true;
	} else {
		return false;
	}
}
/**
 * reuturn domain balance
 */
function get_domain_balance() {
	
	// All Fields are required
	// method is used to check the production status available values for system(test, live)
	$request = array (
			'domain_key' => '192.168.0.25',
			'username' => 'test',
			'password' => 'password',
			'system' => 'test' 
	);
	
	$ch = curl_init ( 'http://192.168.0.63/provab/webservices/rest/domain_balance' );
	curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
	curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
	curl_setopt ( $ch, CURLOPT_POST, 1 );
	curl_setopt ( $ch, CURLOPT_ENCODING, "gzip" );
	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $request );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
	
	$res = curl_exec ( $ch );
	
	curl_close ( $ch );
	
	echo $res;
}

/**
 * * Arjun J Gowda
 * return domain name
 */
function domain_name() {
	return @$GLOBALS ['CI']->entity_domain_name;
}

/**
 * check user is Provab Admin or Domain Admin
 * Jaganath (22-05-2015) - 22-05-2015
 */
function is_domain_user() {
	// check this based on domain id when the user login
	$domain_id = @$GLOBALS ['CI']->entity_domain_id;
	if (intval ( $domain_id ) > 0) {
		return true;
	} else {
		return false;
	}
}

/*
 * Separating Two Admin User Who can't see Admin Markup and Transaction Markup information 
 * Balu A (01-09-2017) - 01-09-2017
 *    
 */

function is_domain_user_master_admin() {
	// check this based on domain id when the user login
	//$domain_id = @$GLOBALS ['CI']->entity_domain_id;
        $entity_domain_id= @$GLOBALS ['CI']->entity_uuid;
     
        if($entity_domain_id=="VH32773379") {
        	return true;
	} else {
		return false;
	}
        
	
}


/**
 * Get Domain Key
 * Jaganath (22-05-2015) - 22-05-2015
 */
function get_domain_auth_id() {
	$CI = & get_instance ();
	$domain_auth_id = $CI->session->userdata ( DOMAIN_AUTH_ID );
	if (intval ( $domain_auth_id ) > 0) {
		return intval ( $domain_auth_id );
	} else {
		return 0;
	}
}


    /*
     *
     * get airport city based on airport code
     */

    function get_airport_city($airport_code,$type='CN',&$airport_list) {
        $CI = & get_instance();
        if(isset($airport_list[$airport_code])){
            if($type=='CN'){
                return $airport_list[$airport_code]['airport_city'];
            }else{
                return $airport_list[$airport_code]['airport_name'];
            }
        }else{
            $airport_data = $CI->db_cache_api->get_airport_city_name(array(
            'airport_code' => $airport_code
            ));
            $airport_list[$airport_code]['airport_city'] = $airport_data['airport_city'];
            $airport_list[$airport_code]['airport_name'] = $airport_data['airport_name'];
            if($type=='CN'){
                return $airport_list[$airport_code]['airport_city'];
            }else{
                return $airport_list[$airport_code]['airport_name'];
            }
        }
    }

    /*
     *
     * get airport city based on airport code
     */
    function get_airline_name($airline_code,&$airline_list) {
        $CI = & get_instance();
        if(isset($airline_list[$airline_code])){
            return $airline_list[$airline_code];
        }else{
             $airline_name = $CI->db_cache_api->get_airline_code_list(array(
            'code' => $airline_code
            ));
            return $airline_list[$airline_code] = @$airline_name [$airline_code];
        }
    }



/**
 * Get Domain Config Key
 * Jaganath (26-05-2015) - 26-05-2015
 */
function get_domain_key() {
	$CI = & get_instance ();
	$domain_key = $CI->session->userdata ( DOMAIN_KEY );
	if (empty ( $domain_key ) == false) {
		return base64_decode ( $domain_key );
	} else {
		return '';
	}
}
function active_sms_checkpoint($name) {
	$status = $GLOBALS ['CI']->user_model->sms_checkpoint ( $name );
	if ($status == ACTIVE) {
		return true;
	} else {
		return false;
	}
}
function current_application_balance() {
	$domain_id = intval ( get_domain_auth_id () );
	$balance_details = $GLOBALS ['CI']->db_cache_api->get_current_balance ();
	return $balance_details;
}
function user_type_access_control($user_type_heap) {
	return check_user_type ( $GLOBALS ['CI']->entity_user_type, $user_type_heap );
}
function get_status_strip($status) {
	$strip = '';
	switch ($status) {
		case ACTIVE :
			$strip = 'alert-success';
			break;
		case INACTIVE :
			$strip = 'alert-danger';
			break;
	}
	return $strip;
}
function get_target_status_strip($target_limit, $user_target) {
	// badge red-shade-3
}
/**
 */
function check_user_type($user_type = '', $user_type_heap = '') {
	if (intval ( $user_type ) > 0) {
		// get user type related list
		$heap_data = array ();
		if (is_string ( $user_type_heap ) == true) {
			if (in_array ( $user_type, array_keys ( get_enum_list ( $user_type_heap ) ) )) {
				return true;
			}
		} elseif (valid_array ( $user_type_heap ) == true) {
			foreach ( $user_type_heap as $k => $v ) {
				if (is_string ( $v )) {
					if (in_array ( $user_type, array_keys ( get_enum_list ( $v ) ) )) {
						return true;
					}
				}
			}
		}
	}
}
function refresh() {
	redirect ( uri_string () . '?' . (empty ( $_SERVER ['REDIRECT_QUERY_STRING'] ) == false ? $_SERVER ['REDIRECT_QUERY_STRING'] : $_SERVER ['QUERY_STRING']), 'Refresh' );
}
/**
 * to check if the request from client is ajax or not
 */
function is_ajax() {
	if (isset ( $_SERVER ['HTTP_X_REQUESTED_WITH'] ) == true && strtolower ( $_SERVER ['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest') {
		$status = true;
	} else {
		$status = false;
	}
	return $status;
}
function check_default_edit_privilege($user_id = 0) {
	if ($user_id == $GLOBALS ['CI']->entity_user_id) {
		return true;
	} else {
		return false;
	}
}
function super_privilege() {
	return false;
}

/**
 * compress output before sending to browser
 *
 * @param unknown_type $input        	
 */
function get_compressed_output($data) {
	$search = array (
			'/\>[^\S ]+/s',
			'/[^\S ]+\</s',
			'#(?://)?<!\[CDATA\[(.*?)(?://)?\]\]>#s' 
	); // leave CDATA alone

	$replace = array (
			'>',
			'<',
			"//&lt;![CDATA[\n" . '\1' . "\n//]]>" 
	);
	
	return preg_replace ( $search, $replace, $data );
}
/**
 * decode json and returns valid json
 *
 * @param object $json
 *        	Json which has to be decoded
 * @param boolean $assoc
 *        	boolean which indicates if array should be returned
 */
function json_validate($json, $assoc = TRUE) {
	// decode the JSON data
	$result = json_decode ( $json, $assoc );
	
	// switch and check possible JSON errors
	switch (json_last_error ()) {
		case JSON_ERROR_NONE :
			$error = ''; // JSON is valid
			break;
		case JSON_ERROR_DEPTH :
			$error = 'Maximum stack depth exceeded.';
			break;
		case JSON_ERROR_STATE_MISMATCH :
			$error = 'Underflow or the modes mismatch.';
			break;
		case JSON_ERROR_CTRL_CHAR :
			$error = 'Unexpected control character found.';
			break;
		case JSON_ERROR_SYNTAX :
			$error = 'Syntax error, malformed JSON.';
			break;
		// only PHP 5.3+
		case JSON_ERROR_UTF8 :
			$error = 'Malformed UTF-8 characters, possibly incorrectly encoded.';
			break;
		default :
			$error = 'Unknown JSON error occured.';
			break;
	}
	
	if ($error !== '') {
		// throw the Exception or exit
		// $status = false;
		// $message = 'Please check you data. Contact System Administrator.';
		// $data = '';
		redirect ( 'general/index' );
	} else {
		$status = true;
		$message = '';
		$data = $result;
	}
	
	return array (
			'status' => $status,
			'message' => $message,
			'data' => $data 
	);
}
/**
 * 
 * Returns the data in Json Format
 */
function output_service_json_data($data)
{
	while (ob_get_level() > 0) { ob_end_clean() ; }
	ob_start("ob_gzhandler");
	header('Content-type:application/json');
	echo json_encode($data);
	ob_end_flush();
	exit;
}
/**
 * get message returns a message with appropriate html
 *
 * @param int $message_type
 *        	message boxes to be displayed
 * @param str $message
 *        	to be displayed inside the box
 */
function get_message($message = "UL003", $message_type = ERROR_MESSAGE, $button_required = false, $override_app_msg = false) {
	switch ($message_type) {
		case SUCCESS_MESSAGE :
			$alert_class = 'alert-success';
			break;
		
		case WARNING_MESSAGE :
			$alert_class = 'alert-warning';
			break;
		
		case INFO_MESSAGE :
			$alert_class = 'alert-info';
			break;
		
		default :
			$alert_class = 'alert-danger';
	}
	$content = '<div class="alert ' . $alert_class . ' clearfix" role="alert">';
	if ($button_required) {
		$content .= '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>';
	}
	if ($override_app_msg == false) {
		$content .= $GLOBALS ['CI']->lang->line ( $message ) . '</div>';
	} else {
		$content .= $message . '</div>';
	}
	
	return $content;
}

/**
 *
 * @param string $message        	
 * @param string $override_app_msg        	
 */
function extract_message($message = "UL003", $override_app_msg = false) {
	$content = '';
	if (empty ( $override_app_msg ) == true) {
		$content .= $GLOBALS ['CI']->lang->line ( $message );
	} else {
		$content .= $message;
	}
	if (empty ( $content ) == true) {
		$content = "Remote IO Error";
	}
	return $content;
}
function get_toastr_index($message_type) {
	switch ($message_type) {
		case SUCCESS_MESSAGE :
			$toastr = 'success';
			break;
		
		case WARNING_MESSAGE :
			$toastr = 'warning';
			break;
		
		case INFO_MESSAGE :
			$toastr = 'info';
			break;
		
		default :
			$toastr = 'error';
	}
	return $toastr;
}

/**
 * returns string containing message for message id
 *
 * @param $msg_id index
 *        	of message whose message has to be fetched
 */
function get_label($msg_id) {
	$msg = $GLOBALS ['CI']->lang->line ( 'FL00' . $msg_id );
	return (empty ( $msg ) == false ? $msg : $msg_id);
}

/**
 * returns string containing message for message id
 *
 * @param $msg_id index
 *        	of message whose message has to be fetched
 */
function get_placeholder($msg_id) {
	return $GLOBALS ['CI']->lang->line ( 'FL000' . $msg_id );
}

/**
 * returns string containing message for message id
 *
 * @param $msg_id index
 *        	of message whose message has to be fetched
 */
function get_help_text($msg_id) {
	return $GLOBALS ['CI']->lang->line ( 'FL0000' . $msg_id );
}
/**
 * returns string containing message for message id
 *
 * @param $msg_id index
 *        	of message whose message has to be fetched
 */
function generate_help_text($ip_help_text) {
	return ' data-container="body" data-toggle="popover" data-original-title="' . get_utility_message ( 'UL004' ) . '" data-placement="bottom" data-trigger="hover focus" data-content="' . $ip_help_text . '"';
}

/**
 * returns string containing message for message id
 *
 * @param $msg_id index
 *        	of message whose message has to be fetched
 */
function provab_help_text($help_text) {
	return '<span data-toggle="popover" data-title="' . get_utility_message ( 'UL004' ) . '" data-placement="bottom" data-html=true  class="glyphicon glyphicon-question-sign handCursor provabHelpText" data-content="' . $help_text . '">';
}
/**
 * returns string containing message for message id
 *
 * @param $msg_id index
 *        	of message whose message has to be fetched
 */
function get_utility_message($msg_id) {
	return $GLOBALS ['CI']->lang->line ( $msg_id );
}

/**
 * returns string containing message for message id
 *
 * @param $msg_id index
 *        	of message whose message has to be fetched
 */
function get_app_message($msg_id) {
	return $GLOBALS ['CI']->lang->line ( $msg_id );
}

/**
 * returns string containing message for message id
 *
 * @param $msg_id index
 *        	of message whose message has to be fetched
 */
function get_legend($msg_id) {
	return $GLOBALS ['CI']->lang->line ( $msg_id );
}

/**
 * * returns string containing options
 *
 * @param $option_list list
 *        	of option values
 * @param $override_order should
 *        	the order be changed by sorting values
 */
function generate_options($option_list = array(), $default_value = false, $override_order = false) {
	$options = '';
	if (valid_array ( $option_list ) == true) {
		$array_values = array_values ( $option_list );
		if (valid_array ( $array_values [0] ) == true) {
			if ($default_value) {
				foreach ( $option_list as $k => $v ) {
					if (in_array ( $v ['k'], $default_value )) {
						$selected = ' selected="selected" ';
					} else {
						$selected = '';
					}
					$options .= '<option value="' . $v ['k'] . '" ' . $selected . '>' . $v ['v'] . '</option>';
				}
			} else {
				foreach ( $option_list as $k => $v ) {
					$options .= '<option value="' . $v ['k'] . '">' . $v ['v'] . '</option>';
				}
			}
		} else {
			if ($default_value) {
				foreach ( $option_list as $k => $v ) {
					if (in_array ( $k, $default_value )) {
						$selected = ' selected="selected" ';
					} else {
						$selected = '';
					}
					$options .= '<option value="' . $k . '" ' . $selected . '>' . $v . '</option>';
				}
			} else {
				foreach ( $option_list as $k => $v ) {
					$options .= '<option value="' . $k . '">' . $v . '</option>';
				}
			}
		}
	} else {
		$options .= '<option value="INVALIDIP">---</option>';
	}
	return $options;
}

/**
 * returns enumerated list of values
 *
 * @param $enum name
 *        	of enumeration datatype
 */
function get_enum_list($enum, $default_value = -1) {
	if ($GLOBALS ['CI']->load->is_loaded ( 'enumeration' ) == false) {
		$GLOBALS ['CI']->load->library ( 'enumeration' );
	}
	$enumeration_list = $GLOBALS ['CI']->enumeration->getEnumerationList ( $enum );
	if (intval ( $default_value ) > - 1) {
		return (isset ( $enumeration_list [$default_value] ) ? $enumeration_list [$default_value] : '');
	} else {
		return $enumeration_list;
	}
}

/**
 * returns enumerated list of values
 *
 * @param $enum name
 *        	of enumeration datatype
 */
function provab_solid_regexp($data_key) {
	if ($GLOBALS ['CI']->load->is_loaded ( 'provab_solid' ) == false) {
		$GLOBALS ['CI']->load->library ( 'provab_solid' );
	}
	return $GLOBALS ['CI']->provab_solid->provab_solid_regexp ( $data_key );
}

/**
 * returns image
 *
 * @param unknown_type $image_name        	
 */
function get_profile_image($image_name = '') {
	return (empty ( $image_name ) ? 'face.png' : $image_name);
}
function debug($ele = array()) {
	echo '<pre>';
	print_r ( $ele );
}

/**
 * Generates Numeric Drop-Down with
 * the given size
 */
function numeric_dropdown($size = array('size' => 10)) {
	$options = "";
	if (valid_array ( $size )) {
		$arr_size = $size ['size'];
		if(isset($size['divider']) == true && $size['divider'] > 0) {
			$divider = floatval($size['divider']);
		} else {
			$divider = 1;
		}
		for($i = 1; $i <= $size ['size']; $i ++) {
			$options [$i] = array (
					'k' => ($i/$divider),
					'v' => ($i/$divider) 
			);
		}
	}
	return $options;
}

function custom_numeric_dropdown($max, $start = 0, $step = 0.5) {
	$options = array ();
	if (isset ( $max )) {
		if ($start == $max) {
			$options [] = array (
					'k' => $start,
					'v' => $start 
			);
		}
		for($i = intval ( $start ); $i < $max; $i += $step) {
			$options [] = array (
					'k' => $i,
					'v' => $i 
			);
		}
		if (end ( $options ) ['k'] < $max) {
			$options [] = array (
					'k' => $max,
					'v' => $max 
			);
		}
	}
	return $options;
}
/**
 * Jaganath
 * Set Insert meassge
 *
 * @param
 *        	$msg
 * @param
 *        	$type
 * @param
 *        	$attributes
 */
function set_insert_message($msg = 'UL0014', $type = SUCCESS_MESSAGE, $attributes = array()) {
	$error_message = array (
			'message' => $msg,
			'type' => $type 
	);
	if (valid_array ( $attributes )) {
		$error_message = array_merge ( $error_message, $attributes );
	}
	$GLOBALS ['CI']->session->set_flashdata ( $error_message );
}
/**
 * Jaganath
 * Set Update meassge
 *
 * @param
 *        	$msg
 * @param
 *        	$type
 * @param
 *        	$attributes
 */
function set_update_message($msg = 'UL0013', $type = SUCCESS_MESSAGE, $attributes = array()) {
	$error_message = array (
			'message' => $msg,
			'type' => $type 
	);
	if (valid_array ( $attributes )) {
		$error_message = array_merge ( $error_message, $attributes );
	}
	$GLOBALS ['CI']->session->set_flashdata ( $error_message );
}
/**
 * Jaganath
 * Set Error meassge
 *
 * @param
 *        	$msg
 * @param
 *        	$type
 * @param
 *        	$attributes
 */
function set_error_message($msg = 'UL0049', $type = ERROR_MESSAGE, $attributes = array()) {
	$error_message = array (
			'message' => $msg,
			'type' => $type 
	);
	if (valid_array ( $attributes )) {
		$error_message = array_merge ( $error_message, $attributes );
	}
	$GLOBALS ['CI']->session->set_flashdata ( $error_message );
}
function get_arrangement_icon($arrangement = '') {
	$arrangement_icon = '';
	switch ($arrangement) {
		/* Hotel */
		case META_ACCOMODATION_COURSE :
			$arrangement_icon = 'fa fa-bed';
			break;
		/* Transfers */
		case META_TRANSFERS_COURSE :
			$arrangement_icon = 'fa fa-car';
			break;
		/* Airline */
		case META_AIRLINE_COURSE :
			$arrangement_icon = 'fa fa-plane';
			break;
		  /* Transfers */
        case META_VIATOR_TRANSFER_COURSE: 
        	$arrangement_icon = 'fa fa-taxi';
            break;
       /* Sightseeing */
        case META_SIGHTSEEING_COURSE: $arrangement_icon = 'fa fa-binoculars';
            break;
		/* Meals */
		case 'VHCID1420973863' :
			$arrangement_icon = 'fa fa-cutlery';
			break;
		
		/* Activities */
		case 'VHCID1420973899' :
			$arrangement_icon = 'fa fa-camera';
			break;
		
		/* Guide */
		case 'VHCID1420973949' :
			$arrangement_icon = 'fa fa-user';
			break;
		
		/* Visa */
		case 'VHCID1420973967' :
			$arrangement_icon = 'fa fa-credit-card';
			break;
		
		/* Insurance */
		case 'VHCID1420973976' :
			$arrangement_icon = 'fa fa-credit-card';
			break;
		
		/* Misc */
		case 'VHCID1420973998' :
			$arrangement_icon = 'fa fa-random';
			break;
		
		/* HANDOVER */
		case 'VHCID1430114195' :
			$arrangement_icon = 'fa fa-gift';
			break;
		
		/* BUS */
		case META_BUS_COURSE :
			$arrangement_icon = 'fa fa-bus';
			break;
		
		/* Package */
		case META_PACKAGE_COURSE :
			$arrangement_icon = 'fa fa-suitcase';
			break;
		
		default :
			$arrangement_icon = 'fa fa-th';
			break;
	}
	return $arrangement_icon;
}
function get_arrangement_color($arrangement = '') {
	$arrangement_color = '';
	switch ($arrangement) {
		/* Hotel */
		case META_ACCOMODATION_COURSE :
			$arrangement_color = 'hotel-l-bg';
			break;
		/* Transfers */
		case META_TRANSFERS_COURSE :
			$arrangement_icon = '';
			break;
		/* Airline */
		case META_AIRLINE_COURSE :
			$arrangement_color = 'flight-l-bg';
			break;
		/* BUS */
		case META_BUS_COURSE :
			$arrangement_color = 'bus-l-bg';
			break;
		
		/* Package */
		case META_PACKAGE_COURSE :
			$arrangement_color = 'package-l-bg';
			break;
		
		default :
			$arrangement_color = '';
			break;
	}
	return $arrangement_color;
}

/**
 *
 * @param unknown_type $name        	
 */
function module_name_to_id($name) {
	$id = '';
	switch ($name) {
		case 'flight' :
			$id = META_AIRLINE_COURSE;
			break;
		case 'hotel' :
			$id = META_ACCOMODATION_COURSE;
			break;
		case 'bus' :
			$id = META_BUS_COURSE;
			break;
		case 'package' :
			$id = META_PACKAGE_COURSE;
			break;
	}
	return $id;
}

/**
 * Show lazy loading gif to user
 */
function get_lazy_loading_icon() {
	return '<img src="' . $GLOBALS ['CI']->template->template_images ( 'loader.gif' ) . '" class="center-align-image lazy-loader-image">';
}

/**
 * Show lazy loading gif to user
 */
function get_circle_ball_loading_icon() {
	return '<img src="' . $GLOBALS ['CI']->template->template_images ( 'circle-ball-ajax-loader.gif' ) . '" class="">';
}

/**
 * generate color code for string
 *
 * @param
 *        	$string
 */
function string_color_code($string) {
	$string = md5 ( $string );
	$string = array (
			"R" => hexdec ( substr ( $string, 0, 2 ) ),
			"G" => hexdec ( substr ( $string, 2, 2 ) ),
			"B" => hexdec ( substr ( $string, 4, 2 ) ) 
	);
	return 'rgb(' . implode ( ',', $string ) . ')';
}
function get_current_url() {
	$url = current_base_url ();
	return $_SERVER ['QUERY_STRING'] ? $url . '?' . $_SERVER ['QUERY_STRING'] : $url;
}
function current_base_url() {
	$CI = & get_instance ();
	return $CI->config->site_url ( $CI->uri->uri_string () );
}

/**
 * default form should be visible if data is posted or if eid is present or if op is defined as add
 */
function form_visible_operation() {
	if (valid_array ( $_POST ) == true || isset ( $_GET ['eid'] ) == true || isset ( $_GET ['origin_id'] ) == true || (isset ( $_GET ['op'] ) == true && $_GET ['op'] == 'add')) {
		return true;
	} else {
		return false;
	}
}
function login_status($time) {
	return ((empty ( $time ) == false and strtotime ( $time ) == 0) ? '<i class="fa fa-circle text-success"></i>' : '<i class="fa fa-circle text-warning"></i>');
}
function last_login($time) {
	return (empty ( $time ) == false ? get_duration_label ( time () - strtotime ( $time ) ) . ' ago(' . app_friendly_absolute_date ( $time ) . ')' : '');
}
function member_since($time) {
	return (empty ( $time ) == false ? '(Since:' . date ( 'M, Y', strtotime ( $time ) ) . ')' : '');
}
function data_to_log_file($data) {
	if (is_array ( $data )) {
		$data = json_encode ( $data );
	}
	error_log ( $data, 3, '/opt/lampp/logs/php_error_log' );
}
/**
 * Admin Base Currency
 * @return string
 */
function domain_base_currency()
{
	return $GLOBALS['CI']->db_cache_api->get_domain_base_currency();
}
/**
 * Groups the clumns
 * @param $input
 * @param $column_key
 * @param $index_key
 */

function group_array_column( array $input, $column_key, $index_key = null ) 
{
	$result = array();
	foreach( $input as $k => $v ) {
		$result[$k] = $v[$column_key];
	}
	return $result;
 }

function admin_base_currency()
{
	return $GLOBALS['CI']->db_cache_api->get_admin_base_currency();
}
/**
 * Returns Class name for specified booking source
 * @param unknown_type $source
 */
function master_class_name($source) 
{
	switch ($source) {
		case TBO_FLIGHT_BOOKING_SOURCE :
			$class_name = 'tbo';
			break;
		case MYSTIFLY_FLIGHT_BOOKING_SOURCE :
			$class_name = 'mystifly';
			break;
        case TBO_HOTEL_BOOKING_SOURCE :
			$class_name = 'tbo';
			break;
		case TRAVELYAARI_BUS_BOOKING_SOURCE :
			$class_name = 'travelyaari';
			break;
        case TRAVELPORT_FLIGHT_BOOKING_SOURCE :
			$class_name = 'travelport';
			break;
		case TRAVELPORT_LCC_FLIGHT_BOOKING_SOURCE :
			$class_name = 'travelport_lcc';
			break;     
        case GRN_CONNECT_HOTEL_BOOKING_SOURCE :
			$class_name = 'GRN';
			break;  
		case AGODA_HOTEL_BOOKING_SOURCE :
			$class_name = 'AGODA';
			break;     
		case ETRAVELSMART_BUS_BOOKING_SOURCE :
				$class_name = 'etravelsmart';

			break; 
		case SIGHTSEEING_BOOKING_SOURCE:
			$class_name ='viator';
			break;    
		case VIATOR_TRANSFER_BOOKING_SOURCE:
			$class_name = 'viator';
			break;
		case SABARE_FLIGHT_BOOKING_SOURCE:
			$class_name = 'sabare';
			break;

		case OYO_HOTEL_BOOKING_SOURCE :
			$class_name = 'OYO';
			break;
		case SPICEJET_FLIGHT_BOOKING_SOURCE:
			$class_name = 'spicejet';  
			break;
		default :
			redirect ( base_url () );
	}
   
	return $class_name;
}
/*
	Sachin
	Format pdf table data 
*/
function format_pdf_data($data=array(), $col=array()){
	
	$pdf_data = array();
	$count = 0;			
	if(valid_array($col)){
		foreach($col as $v){
			if(is_array($v)){
				$v = $v['title'];
			}
					
			$pdf_data['head'][] = $v;
		}				
				
		foreach($data as $v){
			foreach($col as $k=>$cv){						
				if(is_array($cv)){
					$val = '';
					$sep = isset($cv['sep'])?$cv['sep']:' ';
					foreach($cv['cols'] as $cv1){
						$val .= $v[$cv1].$sep;
					}
					$v[$k] = rtrim($val,$sep);
				}
				$pdf_data['body'][$count][] = $v[$k];
			}
			$count++;
		}
	} else {
		$pdf_data['body'] = $data;
	}

	return $pdf_data;
}
/**
 * Compress and output data
 * @param array $data
 */
function output_compressed_data($data)
{
	while (ob_get_level() > 0) { ob_end_clean() ; }
	ob_start("ob_gzhandler");
	header('Content-type:application/json');
	echo json_encode($data);
	ob_end_flush();
	exit;
}
/**
 *
 * Phaneesh Hegde
 *
 * @param unknown $mobile
 * @param string $msg
 * @param string $ref
 * @return string
 */
function send_sms($msg, $user_id=0, $ref='', $mobile='')
{
	$CI = & get_instance ();
	/*$user_info = $CI->user_model->get_user_info($user_id);
	if(empty($mobile)){
		if(valid_array($user_info)){
			$msg = 'TMX ID: '.$user_info['uuid'].', '.$msg;
			$mobile = $user_info['phone'];
		}
	} else if(valid_array($user_info)) {
		$msg = $user_info['agency_name'].', '.$msg;
	}*/

	if(!preg_match('/[0-9]{10}/', $mobile)){
		return false;
	}
	$CI->load->library('sms');
	$response = $CI->sms->send_sms($mobile,$msg,$ref);
	//send_alert_sms($msg,$ref);
	return $response;
}

/**
 *
 * Phaneesh Hegde
 *
 * @param unknown $mobile
 * @param string $msg
 * @param string $ref
 * @return string
 */
function send_alert_sms($msg, $ref='') {
	$response = '';
	if(ENVIRONMENT == 'production'){
		$CI = & get_instance ();
		$CI->load->library('sms');
		$alert_mobile_number = $CI->config->item('alert_mobile_number');
		foreach ($alert_mobile_number as $alm_k => $alm_v){
			$mobile_number = trim($alm_v);
			$response = $CI->sms->send_sms($mobile_number,$msg,$ref);
		}
	}
	return $response;
}



 /* Sending Mail to Travelport Ticketing Team
  * Need to remove when weare running our direct travelport API
 * @param unknown $mobile
 * @param string $msg
 * @param string $ref
 * @return string
 */

function send_alert_sms_only_travelport($msg, $ref='') {
	$response = '';
	if(ENVIRONMENT == 'production'){
		$CI = & get_instance ();
		$CI->load->library('sms');
		$alert_mobile_number = $CI->config->item('alert_mobile_number_only_travelport');
		foreach ($alert_mobile_number as $alm_k => $alm_v){
			$mobile_number = trim($alm_v);
			$response = $CI->sms->send_sms($mobile_number,$msg,$ref);
		}
	}
	return $response;
}

/**
 * 
 * Enter description here ...
 * @param unknown_type $msg
 * @param unknown_type $ref
 */
function send_alert_mail($subject, $mail_template) {
	$response = '';
	$CI = & get_instance ();
	$CI->load->library('provab_mailer');
	$alert_email = $CI->config->item('alert_email_id');
	
	$alert_email = trim($alert_email);
	$response = $CI->provab_mailer->send_mail($alert_email, $subject, $mail_template);
	return $response;
}

function booking_status_label_text($status)
{
	$booking_status = '';
	switch ($status) {
		case 'BOOKING_CONFIRMED' :
			$booking_status = 'CONFIRMED';
			break;
		case 'BOOKING_CANCELLED' :
			$booking_status = 'CANCELLED';
			break;
		case 'BOOKING_FAILED' :
			$booking_status = 'FAILED';
			break;
		case 'BOOKING_INPROGRESS' :
			$booking_status = 'INPROGRESS';
			break;
		case 'BOOKING_INCOMPLETE' :
			$booking_status = 'INCOMPLETE';
			break;
		case 'BOOKING_HOLD' :
			$booking_status = 'HOLD';
			break;
		case 'BOOKING_PENDING' :
			$booking_status = 'PENDING';
			break;
		case 'BOOKING_ERROR' :
			$booking_status = 'ERROR';
			break;
			default:
			$booking_status = $status;
	}
	return $booking_status;
}
function generate_app_transaction_reference($mod_prefix = '', $add_project_prefix = true) {
	if (empty ( $mod_prefix )) {
		$mod_prefix = 'REF';
	}
	$ref = '';
	if ($add_project_prefix) {
		$ref .= PROJECT_PREFIX . '-';
	}
	return $mod_prefix .  date ( 'dmYHi' ) .  rand ( 1000, 9999 );
	//return $ref . $mod_prefix . '-' . date ( 'dmY-Hi' ) . '-' . rand ( 1000, 9999 );
}
function randomPassword_sms() {
        $alphabet = 'okmqazwsxnji852edcbhurfvbgtyhq741wertyuiopl369mnbvcxzas789dfghjkledc5231ijntgvla8137';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 15; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

	function tax_breakup($tax_details){
        $display_tax_list = array('YQ','YR');
        $return_tax_list = array();
        $return_tax_list['Other_Tax'] = 0;
        if(valid_array($tax_details)){
            foreach($tax_details as $t_k => $t_v){
                if(in_array($t_k, $display_tax_list)){
                    $return_tax_list[$t_k] = $t_v;
                }else{
                    $return_tax_list['Other_Tax'] += $t_v;
                }
            }
        }
        return $return_tax_list;
    }
    function flight_commission_calculation($check_com=true,$carrier, $is_domestic, $booking_source,$CabinClass,&$flight_journey,$approx_base_price,$tax_details){
        // echo $approx_base_price;exit;
        $CI = & get_instance();
        $yqvalue = $tax_details['YQ'];
        $YR_value = $tax_details['YR'];
        if($check_com==true){
            $check_comm = $CI->flight_model->check_commision($carrier, $is_domestic, $booking_source);
        	
            #Checking Booking Classes for adding Commission
            $ExceptClass = array();
            if (!empty($check_comm->except_classes)) {
                $ExceptClass = explode(',', $check_comm->except_classes);
            }
            $YQ_value = 0;
            $flight_commission =0;
            if (trim($CabinClass) == "Economy") {

                if ($check_comm->e_basic_plus_yq_type == "YQ") {
                    $YQ_value = $yqvalue;
                }
                if ($check_comm->e_basic_plus_yq_type == "YR") {
                    $YQ_value = $YR_value;
                }
                $flight_commission = $check_comm->e_basic_plus_yq_value;

            }else if (trim($CabinClass) == "Business") {
                if ($check_comm->b_basic_plus_yq_type == "YQ") {
                    $YQ_value = $yqvalue;
                }
                if ($check_comm->b_basic_plus_yq_type == "YR") {
                    $YQ_value = $YR_value;
                }
                $flight_commission = $check_comm->b_basic_plus_yq_value;
            }
            $iata_commssion=0;
            $PLB_commssion=0;
            #Add IATA Commission with Basic Fare
            $value_type="percentage";
            if ($check_comm->value_type == 'percentage') {
                $iata_commssion = ($approx_base_price * $check_comm->iata) / 100;
            } else if ($check_comm->value_type == "plus") {
                $iata_commssion = $check_comm->iata;
            } else {
                $iata_commssion = 0;
            }

            #Add PLB Commission With Basic+ YQ/YR   
            $available_class = '';
            $available_class = array_search($BookingCode, $ExceptClass); // $key = 2;

            if (empty($available_class)) {
                if ($check_comm->value_type == 'percentage') {
                    $basic_plus_yq = (($approx_base_price - $iata_commssion) + $YQ_value);
                    $PLB_commssion = ($basic_plus_yq * $flight_commission) / 100;
                } else if ($check_comm->value_type == 'plus') {
                    $PLB_commssion = $flight_commission;
                }
            }
            #Cash Back Option
            $CashBackAmount=0;
            $CashBack=$check_comm->cashback;
            if($CashBack > 0){
                $CashBackAmount=$CashBack;
            }

            $tds_commission = ($agent_commssion + $yq_value) * 5 / 100;
           
            $flight_journey ['Price'] ['PriceBreakup'] ['CommissionEarned'] = @$iata_commssion;
            $flight_journey ['Price'] ['PriceBreakup'] ['PLBEarned'] = (@$PLB_commssion + $CashBackAmount);
            $flight_journey ['Price'] ['PriceBreakup'] ['TdsOnCommission'] = ((@$iata_commssion * 5)/100);
            $flight_journey ['Price'] ['PriceBreakup'] ['TdsOnPLB'] = (((@$PLB_commssion+$CashBackAmount) * 5)/100);
            
            $flight_journey ['Price'] ['PriceBreakup'] ['AgentCommission'] = @$iata_commssion + @$PLB_commssion + $CashBackAmount;
            $flight_journey ['Price'] ['PriceBreakup'] ['AgentTdsOnCommision'] = ((@$iata_commssion * 5)/100)+((@$PLB_commssion * 5)/100) + (($CashBackAmount*5)/100);
       		
       }else{
            $flight_journey ['Price'] ['PriceBreakup'] ['CommissionEarned'] = 0;
            $flight_journey ['Price'] ['PriceBreakup'] ['PLBEarned'] = 0;
            $flight_journey ['Price'] ['PriceBreakup'] ['TdsOnCommission'] = 0;
            $flight_journey ['Price'] ['PriceBreakup'] ['TdsOnPLB'] = 0;
            $flight_journey ['Price'] ['PriceBreakup'] ['AgentCommission'] = 0;
            $flight_journey ['Price'] ['PriceBreakup'] ['AgentTdsOnCommision'] = 0;
        }
    }
        //checking fare type active or not
	function check_active_fare_type($booking_source,$domain_origin){
       	$CI = & get_instance();
       	$check_fare_type = $CI->custom_db->single_table_records('api_fare_type_details', '*', array('domain_origin' => $domain_origin,'booking_source' => $booking_source));
       	$fare_type_list = array();
        if($check_fare_type['status'] == true){
           	foreach($check_fare_type['data'] as $fare_type){
           		array_push($fare_type_list, $fare_type['fare_type']);
           	}
           	return $fare_type_list;	
        }
        else{
            return false;
        }
    }   
    
