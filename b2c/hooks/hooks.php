<?php

/**
 *
 * @author Balu A<balu.provab@gmail.com>
 *
 */
class application {
	var $CI; // code igniter object
	var $userId; // user id to identify user
	var $page_configuration;
	var $skip_validation;

	/**
	 * constructor to initialize data
	 */
	function __construct() {
		$this->CI = &get_instance ();
		$this->CI->load->library ( 'provab_page_loader.php' );
		$this->CI->load->helper ( 'url' );
		if (! isset ( $this->CI->session )) {
			$this->CI->load->library ( 'session' );
		}
		$this->footer_needle = $this->header_needle = $this->CI->uri->segment ( 2 );
		$this->skip_validation = false;
		$this->CI->language_preference = 'english';
		$this->CI->lang->load ( 'form', $this->CI->language_preference );
		$this->CI->lang->load ( 'application', $this->CI->language_preference );
		$this->CI->lang->load ( 'utility', $this->CI->language_preference );
		// $this->CI->session->set_userdata(array(AUTH_USER_POINTER => 10, LOGIN_POINTER => intval(100)));
	}

	/**
	 * We need to initialize all the domain key details here
	 * Written only for provab
	 */
	function initialize_domain_key() {
		$domain_auth_id = 1;
		$domain_key = CURRENT_DOMAIN_KEY;
		$domain_details = $GLOBALS ['CI']->custom_db->single_table_records ( 'domain_list', '*', array (
				'domain_key' => CURRENT_DOMAIN_KEY 
		) );
		$module = $this->CI->uri->segment(1);
		if(empty($module) == false){
			if($module == 'flight'){
				$module = 'flight';
			}
			else if($module == 'hotel'){
				$module = 'hotel';
			}
			else if($module == 'bus'){
				$module = 'bus';
			}
			else if($module == 'sightseeing'){
				$module = 'activity';
			}
			else if($module == 'transferv1'){
				$module = 'transfer';
			}
			else{
				$module ='general';
			}
		}
		else{
			$module ='general';
		}

		
		$seo_details = $GLOBALS ['CI']->custom_db->single_table_records ( 'seo', '*',array('module' => $module) );

		if (valid_array ( $domain_details ) == true) {
			// IF DOMAIN KEY IS NOT SET, THEN SET THE DOMANIN DETAILS
			$domain_details = $domain_details ['data'] [0];
			//$this->CI->application_default_template = $domain_details['theme_id'];
			$this->CI->application_default_template = isset($_GET['theme']) ? $_GET['theme'] : $domain_details['theme_id'];
			$this->CI->entity_domain_name = $domain_details ['domain_name'];
			$this->CI->entity_domain_website = $domain_details ['domain_webiste'];
			$this->CI->entity_domain_phone = $domain_details['phone'];
			$this->CI->entity_domain_mail = $domain_details['email'];
			$this->CI->entity_domain_voucher_email = $domain_details['voucher_email'];
			$this->CI->application_domain_logo = $domain_details['domain_logo'];
			//if (intval ( $domain_auth_id ) == 0 && empty ( $domain_key ) == true and strlen ( trim ( $domain_details ['domain_name'] ) ) > 0) {
			if (strlen ( trim ( $domain_details ['domain_name'] ) ) > 0) {//CHECK THIS
				$domain_session_data = array ();
				// SETTING DOMAIN KEY
				$domain_session_data [DOMAIN_AUTH_ID] = intval ( $domain_details ['origin'] );
				// SETTING DOMAIN CONFIGURATION
				$domain_session_data [DOMAIN_KEY] = base64_encode ( trim ( $domain_details ['domain_key'] ) );
				$this->CI->session->set_userdata ( $domain_session_data );
			}
		}
		
		define('HEADER_DOMAIN_WEBSITE', $this->CI->entity_domain_website);
		define('HEADER_DOMAIN_NAME', $this->CI->entity_domain_name);
		if($seo_details['status'] == SUCCESS_STATUS){

			define('HEADER_TITLE_SUFFIX', $seo_details['data'][0]['title']); // Common Suffix For All Pages
			define('META_KEYWORDS', $seo_details['data'][0]['keyword']); // Common Suffix For All Pages
			define('META_DESCRIPTION', $seo_details['data'][0]['description']); // Common Suffix For All Pages
		}else{
			if (empty($this->CI->entity_domain_name) == false) {
				define('HEADER_TITLE_SUFFIX', ' - Welcome'.$this->CI->entity_domain_name); // Common Suffix For All Pages
			} else {
				define('HEADER_TITLE_SUFFIX', ' - Welcome Travels'); // Common Suffix For All Pages
			}
			define('META_KEYWORDS', HEADER_TITLE_SUFFIX. "Flights, Hotels, Busses, Packages, Low Cost Flights");
			define('META_DESCRIPTION', 'Flight Bookings, Hotel Bookings, Bus Bookings, Package bookings system.');
		}
		
	}

	/**
	 * Set all the active modules for doamin
	 */
	function initialize_domain_modules() {
		// set domain active modules based on auth key
		$domain_key = CURRENT_DOMAIN_KEY;
		$domain_auth_id = 1;
		// set global modules data
		$active_domain_modules = $this->CI->module_model->get_active_module_list ( $domain_auth_id, $domain_key );
		// debug($active_domain_modules);exit;
		$this->CI->active_domain_modules = $active_domain_modules;
	}

	/**
	 * Following pages will not have any validations
	 */
	function bouncer_page_validation() {
		$skip_validation_list = array (
				'forgot_password' 
				); // SKIP LIST
				if (in_array ( $this->header_needle, $skip_validation_list )) {
					$this->skip_validation = true;
				}
	}

	/**
	 * Handle hook for multiple page login system
	 */
	function initilize_multiple_login() {
		$this->bouncer_page_validation ();
		if ($this->skip_validation == false) {
			$auth_login_id = $this->CI->session->userdata ( AUTH_USER_POINTER );
			if (empty ( $auth_login_id ) == false) {
				$condition ['uuid'] = $auth_login_id;
				$condition ['status'] = ACTIVE;
			}
			if (isset ( $condition ) == true and is_array ( $condition ) == true and count ( $condition ) > 0) {
				$condition ['status'] = ACTIVE;
				$user_details = $this->CI->db->get_where ( 'user', $condition )->row_array ();
				if (valid_array ( $user_details ) == true) {
					$this->set_global_entity_data ( $user_details );
				}
			}
		}
	}

	function set_global_entity_data($user_details) {

		$this->CI->entity_user_id = $user_details ['user_id'];
		$this->CI->entity_domain_id = $user_details ['domain_list_fk'];
		$this->CI->entity_uuid = provab_decrypt($user_details ['uuid']);
		$this->CI->entity_user_type = $user_details ['user_type'];
		$this->CI->entity_email = provab_decrypt($user_details ['email']);
		$this->CI->entity_title = $user_details ['title'];
		$this->CI->entity_first_name = $user_details ['first_name'];
		$this->CI->entity_signature = $user_details ['signature'];
		$this->CI->entity_last_name = $user_details ['last_name'];
		$this->CI->entity_name = get_enum_list ( 'title', $user_details ['title'] ) . ' ' . ucfirst ( $user_details ['first_name'] ) . ' ' . ucfirst ( $user_details ['last_name'] );
		$this->CI->entity_address = $user_details ['address'];
		$this->CI->entity_phone = $user_details ['phone'];
		$this->CI->entity_country_code = $user_details ['country_code'];
		$this->CI->entity_status = $user_details ['status'];
		$this->CI->entity_date_of_birth = $user_details ['date_of_birth'];
		$this->CI->entity_image = $user_details ['image'];
		$this->CI->entity_created_datetime = $user_details ['created_datetime'];
		$this->CI->entity_language_preference = $user_details ['language_preference'];
	}

	/**
	 * function to update login time and logout time details of user when user
	 * login or logout.
	 */
	function update_login_manager() {
		$loginDetails ['browser'] = $_SERVER ['HTTP_USER_AGENT'];
		$remote_ip = $_SERVER ['REMOTE_ADDR'];
		$loginDetails ['info'] = file_get_contents ( "http://ipinfo.io/" . $remote_ip . "/json" );
		$checkLogin = $this->CI->custom_db->single_table_records ( 'login_manager', '*', array (
				'user_id' => $this->CI->entity_user_id,
				'login_ip !=' => $remote_ip 
		), '0', '10', '' );
		if (empty ( $checkLogin ['data'] ) == true) {
			$checkLoginSameIP = $this->CI->custom_db->single_table_records ( 'login_manager', '*', array (
					'user_id' => $this->CI->entity_uuid,
					'login_ip' => $remote_ip 
			), '0', '10', '' );
			if (empty ( $checkLoginSameIP ['data'] ) == false) {
				$loginID ['insert_id'] = isset ( $this->CI->session->userdata [LOGIN_POINTER] ) ? $this->CI->session->userdata [LOGIN_POINTER] : $this->CI->entity_user_id;
			} else {
				$loginID = $this->CI->custom_db->insert_record ( 'login_manager', array (
						'user_type' => $this->CI->entity_user_type,
						'user_id' => $this->CI->entity_uuid,
						'login_date_time' => date ( 'Y-m-d H:i:s', time () ),
						'login_ip' => $remote_ip,
						'attributes' => mysql_real_escape_string ( json_encode ( $loginDetails ) ) 
				) );
			}
		} else {
			$this->CI->custom_db->update_record ( 'login_manager', array (
					'logout_date_time' => date ( 'Y-m-d H:i:s', time () ) 
			), array (
					'user_id' => $this->CI->entity_uuid 
			) );
			$loginID = $this->CI->custom_db->insert_record ( 'login_manager', array (
					'user_type' => $this->CI->entity_user_type,
					'user_id' => $this->CI->entity_uuid,
					'login_date_time' => date ( 'Y-m-d H:i:s', time () ),
					'login_ip' => $remote_ip,
					'attributes' => mysql_real_escape_string ( json_encode ( $loginDetails ) ) 
			) );
		}
		return $loginID ['insert_id'];
	}

	/*
	 * load current page configuration
	 */
	function load_current_page_configuration() {
		//$this->set_page_configuration ();
		$this->page_configuration ['current_page'] = $this->CI->current_page = new Provab_Page_Loader ();
	}

	/**
	 * This file specifies which systems should be loaded by default for each page.
	 *
	 * @param unknown_type $controller
	 * @param unknown_type $method
	 */
	function set_page_configuration() {
		/*$controller = $this->CI->uri->segment ( 1 );
		 $method = $this->CI->uri->segment ( 2 );
		 $temp_configuration ['general'] ['index'] = array (
		 'header_title' => 'AL001',
		 'menu' => false,
		 'page_keywords' => array (
		 'meta' => '',
		 'author' => ''
		 ),
		 'page_small_icon' => ''
		 );
		 $this->page_configuration = $temp_configuration ['general'] ['index'];*/
	}
	function set_project_configuration(){
		$api_data = $this->CI->custom_db->single_table_records ( 'api_urls_new', '*',array ('status' => 1));
		$domain_list = $this->CI->custom_db->single_table_records ( 'domain_list', '*',array ('status' => 1));
		// debug($domain_list);exit;

		if($api_data['status'] == true){
			$api_data = $api_data['data'][0];

			// debug($api_data);exit;	
			if($api_data['system'] == 'Test'){
				$system = 'test';
				$this->CI->test_username = $domain_list['data'][0]['test_username'];
				$this->CI->test_password = $domain_list['data'][0]['test_password'];
			}
			else{
				$system = 'live';
				$this->CI->live_username = $domain_list['data'][0]['live_username'];
				$this->CI->live_password = $domain_list['data'][0]['live_password'];
			}
			
			$this->CI->flight_engine_system = $system; //test/live
			$this->CI->hotel_engine_system = $system;//test/live
			$this->CI->bus_engine_system = "test"; //$system; //test/live
			$this->CI->transfer_engine_system = $system; //test/live
			$this->CI->external_service_system = $system; //test/live
			$this->CI->sightseeing_engine_system = $system;
			$this->CI->car_engine_system = $system; //test/live

			$secret_iv = PROVAB_SECRET_IV;
			$md5_key = PROVAB_MD5_SECRET;
	        $encrypt_key = PROVAB_ENC_KEY;
	        $encrypt_method = "AES-256-CBC";
	        $decrypt_password = $this->CI->db->query("SELECT AES_DECRYPT($encrypt_key,SHA2('".$md5_key."',512)) AS decrypt_data");
	          
	        $db_data = $decrypt_password->row();
	         
	        $secret_key = trim($db_data->decrypt_data); 
	        $key = hash('sha256', $secret_key);
	        $iv = substr(hash('sha256', $secret_iv), 0, 16);
	        $urls = openssl_decrypt(base64_decode($api_data['urls']), $encrypt_method, $key, 0, $iv);
	       
	        $urls = json_decode($urls, true);
	        // debug($urls);exit;
	        // $urls['flight_url'] = 'http://192.168.0.87/travelomatix_live_services/webservices/flight/service/';
	  //       $urls['flight_url'] = 'http://test.services.travelomatix.com/webservices/flight/service/';
	  //       $urls['bus_url'] = 'http://test.services.travelomatix.com/webservices/bus/service/';
			// $urls['activity_url'] = 'http://test.services.travelomatix.com/webservices/sightseeing/service/';
			// $urls['transfer_url'] = 'http://test.services.travelomatix.com/webservices/transferv1/service/';
			// $urls['hotel_url'] = 'http://test.services.travelomatix.com/webservices/hotel_v3/service/';
			// $urls['external_service'] = 'http://test.services.travelomatix.com/webservices/index.php/rest/';
			//api urls
			$urls['flight_url'] = 'https://pacetravels.net/service/webservices/index.php/flight/service/';
			https://pacetravels.net/service/webservices/index.php/flight/service/Search
			$this->CI->flight_url = $urls['flight_url'];
			$this->CI->hotel_url = $urls['hotel_url'];
			$this->CI->bus_url = $urls['bus_url'];
			$this->CI->transferv1_url = $urls['transfer_url'];
			$this->CI->sightseeing_url =  $urls['activity_url'];
			$this->CI->car_url = $urls['car_url'];
			$this->CI->external_service = $urls['external_service'];
			$this->CI->domain_key = $domain_list['data'][0]['domain_key'];
			// debug($urls);exit;
			//passwords
			
		}
		
	}
}
