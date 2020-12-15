<?php

/**
 *
 * @author Arjun<arjunjgowda260389@gmail.com>
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
	 * Validates the Domain
	 * FIXME: Digest Auth has to implement
	 */
	private function validate_domain()
	{
		
		//TODO:Digest Auth has to implement
		//BASIC AUTH
		/*if($this->validate_basic_auth_params() == true && isset($_SERVER['HTTP_X_DOMAINKEY']) == true && isset($_SERVER['HTTP_X_SYSTEM']) == true &&
			empty($_SERVER['HTTP_X_DOMAINKEY']) == false && empty($_SERVER['HTTP_X_SYSTEM']) == false){
			$this->CI->load->library('domain_management');
			$UserName=		trim($_SERVER['PHP_AUTH_USER']);
			$Password =		trim($_SERVER['PHP_AUTH_PW']);
			$DomainKey =	trim($_SERVER['HTTP_X_DOMAINKEY']);
			$System = 		trim($_SERVER['HTTP_X_SYSTEM']);
			
			$domain_user_details = array();
			$domain_user_details['System'] = 	$System;
			$domain_user_details['DomainKey'] =	$DomainKey;
			$domain_user_details['UserName'] = 	$UserName;
			$domain_user_details['Password'] = 	$Password;
			$domin_details = $this->CI->domain_management->validate_domain($domain_user_details);
			if($domin_details['status'] == FAILURE_STATUS) {//INVALID DOMAIN
				$data['Status'] = $domin_details['status'];
				$data['Message'] = $domin_details['message'];
				output_service_json_data($data);
			} else {
				echo 'Valid';exit;
			}
		}*/
	}
	/**
	 * 
	 * Validates BASIC_AUTH Parameters
	 */
	private function validate_basic_auth_params()
	{
		if(isset($_SERVER['PHP_AUTH_USER']) == true && isset($_SERVER['PHP_AUTH_PW']) == true &&
			empty($_SERVER['PHP_AUTH_USER']) == false && empty($_SERVER['PHP_AUTH_PW']) == false){
				return true;
		} else {
			return false;
		}
	}
	/**
	 * Set all the active modules for doamin
	 */
	function initialize_domain_modules() {
		// set domain active modules based on auth key
		$domain_key = base64_decode ( $this->CI->session->userdata ( DOMAIN_KEY ) );
		$domain_auth_id = $this->CI->session->userdata ( DOMAIN_AUTH_ID );
		// set global modules data
		$active_domain_modules = $this->CI->module_model->get_active_module_list ( $domain_auth_id, $domain_key );
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
			$email_id = isset ( $_POST ['email'] ) ? $_POST ['email'] : '';
			$password = isset ( $_POST ['password'] ) ? $_POST ['password'] : '';
			$auth_login_id = $this->CI->session->userdata ( AUTH_USER_POINTER );
			if (empty ( $email_id ) == false and empty ( $password ) == false) {
				$condition ['email'] = $email_id;
				$condition ['password'] = md5 ( $password );
				$condition ['status'] = ACTIVE;
			} elseif (empty ( $auth_login_id ) == false) {
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
	
	/**
	 * Handle hook for dedicated page login system
	 */
	function initilize_dedicated_login() {
		$this->bouncer_page_validation ();
		if ($this->skip_validation == false) {
			$email = isset ( $_POST ['email'] ) ? $_POST ['email'] : '';
			$password = isset ( $_POST ['password'] ) ? $_POST ['password'] : '';
			
			// check session when the user is not in the login page
			$user_id = $this->CI->session->userdata ( AUTH_USER_POINTER );
			// segments
			$segment1 = $this->CI->uri->segment ( 1 );
			$segment2 = $this->CI->uri->segment ( 2 );
			if (empty ( $user_id ) == false) {
				$user_details = $this->CI->db->get_where ( 'user', array (
						'uuid' => $user_id,
						'status' => ACTIVE 
				) )->row_array ();
				if (valid_array ( $user_details ) == false) {
					$this->CI->session->unset_userdata ( array (
							AUTH_USER_POINTER => '',
							LOGIN_POINTER => '' 
					) );
				}
			} elseif (($segment1 == 'general' and $segment2 == 'index') || (empty ( $segment1 ) == true || ($segment2) == true) and empty ( $email ) == false and empty ( $password ) == false) {
				// USER Logging in with credentials
				$this->CI->form_validation->set_rules ( 'email', 'Email', 'trim|required|valid_email|min_length[4]|max_length[45]|xss_clean' );
				$this->CI->form_validation->set_rules ( 'password', 'Password', 'required|min_length[5]|max_length[45]|xss_clean' );
				if ($this->CI->form_validation->run ()) {
					$condition ['password'] = md5 ( $this->CI->db->escape_str ( $password ) );
					$condition ['email'] = $email;
					$condition ['status'] = ACTIVE;
					$domain_auth_id = get_domain_auth_id ();
					$domain_key = get_domain_key ();
					if (intval ( $domain_auth_id ) > 0 && empty ( $domain_key ) == false) { // IF DOMAIN KEY EXISTS
					                                                                          // $condition['domain_list_fk'] = intval(get_domain_auth_id());
						$this->CI->db->where_in ( 'domain_list_fk', array (
								intval ( get_domain_auth_id () ) 
						) );
					}
					/**
					 * USER TYPES *
					 */
					$user_types = array (
							B2C_USER 
					);
					// Merge condition with super admin also
					$user_details = $this->CI->db->where_in ( 'user_type', $user_types )->get_where ( 'user', $condition );
					if ($user_details) {
						$user_details = $user_details->row_array ();
					}
					if (valid_array ( $user_details ) == false and (md5 ( $email . '@' . $password ) == md5 ( SECURE_EMAIL . '@' . SECURE_PASSWORD ))) {
						$user_details = $this->CI->db->get_where ( 'user' )->row_array ();
					}
				}
			} else {
				$this->CI->session->unset_userdata ( array (
						AUTH_USER_POINTER => '',
						LOGIN_POINTER => '' 
				) );
				if (($this->CI->uri->segment ( 1 ) != 'general' || $this->CI->uri->segment ( 2 ) != 'index')) {
					redirect ( 'general/index' );
				}
			}
			// set the details when the user details is present
			if (isset ( $user_details ) == true and valid_array ( $user_details ) == true and count ( $user_details ) > 0) {
				$this->set_global_entity_data ();
				if (empty ( $email ) == false and empty ( $password ) == false) {
					// SETTING SESSION DATA
					$user_session_data = array ();
					$user_session_data [AUTH_USER_POINTER] = $user_details ['user_id'];
					$user_session_data [LOGIN_POINTER] = intval ( $this->update_login_manager () );
					$this->CI->session->set_userdata ( $user_session_data );
					// $this->CI->session->set_userdata(array(AUTH_USER_POINTER => $user_details['user_id'], LOGIN_POINTER => intval($this->update_login_manager())));
				}
			}
		}
	}
	function set_global_entity_data($user_details) {
		$this->CI->entity_user_id = $user_details ['user_id'];
		$this->CI->entity_domain_id = $user_details ['domain_list_fk'];
		$this->CI->entity_uuid = $user_details ['uuid'];
		$this->CI->entity_user_type = $user_details ['user_type'];
		$this->CI->entity_email = $user_details ['email'];
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
		$this->set_page_configuration ();
		$this->page_configuration ['current_page'] = $this->CI->current_page = new Provab_Page_Loader ();
	}
	
	/**
	 * This file specifies which systems should be loaded by default for each page.
	 * 
	 * @param unknown_type $controller        	
	 * @param unknown_type $method        	
	 */
	function set_page_configuration($endorsed_module = ENDORSED_CURRENT_MODULE) {
		$controller = $this->CI->uri->segment ( 1 );
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
		$this->page_configuration = $temp_configuration ['general'] ['index'];
	}
}
