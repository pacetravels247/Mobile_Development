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
		$this->CI->lang->load ( 'utility', $this->CI->language_preference );
		$this->CI->lang->load ( 'application', $this->CI->language_preference );
		// $this->CI->session->set_userdata(array(AUTH_USER_POINTER => 10, LOGIN_POINTER => intval(100)));
	}

	/**
	 * We need to initialize all the domain key details here
	 * Written only for provab
	 */
	function initialize_domain_key() {
		$this->CI->application_default_template = 'template_v1';
		// Balu A
		$domain_auth_id = $GLOBALS ['CI']->session->userdata ( DOMAIN_AUTH_ID );
		$domain_key = $GLOBALS ['CI']->session->userdata ( DOMAIN_KEY );
		$domain_details = $GLOBALS ['CI']->custom_db->single_table_records ( 'domain_list', '*', array (
				'domain_key' => CURRENT_DOMAIN_KEY 
		) );
		
		if (valid_array ( $domain_details ) == true and count ( $domain_details ) >= 1) {
			$domain_details = $domain_details ['data'] [0];
			$this->CI->entity_domain_name = $domain_details ['domain_name'];
			$this->CI->application_domain_logo = $domain_details['domain_logo'];
			$this->CI->entity_domain_website = $domain_details ['domain_webiste'];
			if (intval ( $domain_auth_id ) == 0 && empty ( $domain_key ) == true and strlen ( trim ( $domain_details ['domain_name'] ) ) > 0) {
				// IF DOMAIN KEY IS NOT SET, THEN SET THE DOMANIN DETAILS
				$domain_session_data = array ();
				// SETTING DOMAIN KEY
				$domain_session_data [DOMAIN_AUTH_ID] = intval ( $domain_details ['origin'] );
				// SETTING DOMAIN CONFIGURATION
				$domain_session_data [DOMAIN_KEY] = base64_encode ( trim ( $domain_details ['domain_key'] ) );
				$this->CI->session->set_userdata ( $domain_session_data );
			}
		}
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
		//define('HEADER_DOMAIN_WEBSITE', $this->CI->entity_domain_website);
		//define('HEADER_DOMAIN_NAME', $this->CI->entity_domain_name);
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
		$domain_key = base64_decode ( $this->CI->session->userdata ( DOMAIN_KEY ) );
		$domain_auth_id = $this->CI->session->userdata ( DOMAIN_AUTH_ID );
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
				'forgot_password','update_pnr_details','send_otp','change_admin_password','auto_lock_agents'
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
			} elseif (intval ( $auth_login_id ) > 0) {
				$condition ['uuid'] = $auth_login_id;
			}
			if (isset ( $condition ) == true and is_array ( $condition ) == true and count ( $condition ) > 0) {
				$condition ['status'] = ACTIVE;
				$user_details = $this->CI->db->get_where ( 'user', $condition )->row_array ();
				if (valid_array ( $user_details ) == true) {
					$this->CI->entity_uuid = $user_details ['uuid'];
					$this->CI->entity_user_type = $user_details ['user_type'];
					if (empty ( $email_id ) == false and empty ( $password ) == false) {
						$this->CI->session->set_userdata ( array (
						AUTH_USER_POINTER => $user_details ['uuid'],
						LOGIN_POINTER => intval ( $this->update_login_manager () )
						) );
					}
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

			$login_cookie = $this->CI->input->cookie ( 'login_cookie', TRUE );  


			$email = isset ( $_POST ['email'] ) ? $_POST ['email'] : '';
			$password = isset ( $_POST ['password'] ) ? $_POST ['password'] : '';
			$OTP = $this->CI->session->userdata('OTP');
                   
			if(!empty($_POST ['opt_number'])){
				if($OTP == $_POST ['opt_number']){
					$otp_verity = true;
				}
				else{
					$otp_verity = false;
				}
			}
                        
			else{
                if(empty($login_cookie)==false)
                {
                   $otp_verity = true;
                } else {
           		$otp_verity = false;
                }
			}
			$otp_verity = true;


			// check session when the user is not in the login page
			$user_id = $this->CI->session->userdata ( AUTH_USER_POINTER );
			// segments
			$segment1 = $this->CI->uri->segment ( 1 );
			$segment2 = $this->CI->uri->segment ( 2 );
			if (isset ( $user_id ) == true and empty ( $user_id ) == false) {
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
				// debug($user_details);exit;
			} elseif (($segment1 == 'general' and $segment2 == 'index') || (empty ( $segment1 ) == true || ($segment2) == true) and empty ( $email ) == false and empty ( $password ) == false and ($otp_verity== true)) {

				// USER Logging in with credentials
				$this->CI->form_validation->set_rules ( 'email', 'Email', 'trim|required|valid_email|min_length[4]|max_length[45]|xss_clean' );
				$this->CI->form_validation->set_rules ( 'password', 'Password', 'required|min_length[5]|max_length[45]|xss_clean' );
				if ($this->CI->form_validation->run ()) {
					// $condition ['password'] = md5 ( $this->CI->db->escape_str ( $password ) );
					// $condition ['user_name'] = $email;
					// $condition ['status'] = ACTIVE;
					#echo $email;
					$data['password'] =  $this->CI->db->escape_str (provab_encrypt(md5(trim($password )))) ;
					$data['user_name'] = provab_encrypt(trim($email));
					$data['status'] = ACTIVE;
					// debug($condition);exit;
					$domain_auth_id = get_domain_auth_id ();
					$domain_key = get_domain_key ();
					if (intval ( $domain_auth_id ) > 0 && empty ( $domain_key ) == false) { // IF DOMAIN KEY EXISTS
						// $condition['domain_list_fk'] = intval(get_domain_auth_id());
						// $this->CI->db->where_in ( 'domain_list_fk', array (
						// intval ( get_domain_auth_id () ),
						// 0
						// ) );
					}
					/**
					 * USER TYPES *
					 */
					$user_types = array(
					ADMIN, SUB_ADMIN ,Executive, CALL_CENTER_USER, DEVELOPER, SALES_MANAGER, SUPPORT_EXECUTIVE, ACCOUNTANT, PACKAGE_MANAGER, FRONT_DESK
					);
					$ut_cond = "";
					foreach($user_types AS $utk => $ut) {
						$ut_cond .= " U.user_type = ".$ut." OR";
					}
					$ut_cond = rtrim($ut_cond, "OR");
					//debug($user_types); exit;
					#debug($data);

					// Merge condition with super admin also
					$user_details = $this->CI->user_model->get_admin_details($data, $ut_cond);
					//print_r($user_details);die();
					#echo $this->CI->db->last_query();
					#debug($user_details);exit;
					// $user_details = $this->CI->db->where_in ( 'user_type', $user_types )->get_where ( 'user', $condition );
					if ($user_details) {
						$user_details = $user_details;
					}
				}
			} else {
				$this->CI->session->unset_userdata ( array (
				AUTH_USER_POINTER => '',
				LOGIN_POINTER => ''
				) );
				if (($this->CI->uri->segment ( 1 ) != 'general' || $this->CI->uri->segment ( 2 ) != 'index')) {
					/* if(empty($this->CI->uri->segment(3)) || $this->CI->uri->segment (3)!="cron")
						redirect ('general/index');
						exit(); */
						redirect ('general/index');
				}
			}
			// set the details when the user details is present
			if (isset ( $user_details ) == true and valid_array ( $user_details ) == true and count ( $user_details ) > 0) {
				$this->CI->entity_user_id = $user_details ['user_id'];
				$this->CI->entity_domain_id = $user_details ['domain_list_fk'];
				$this->CI->entity_uuid = provab_decrypt($user_details ['uuid']);
				$this->CI->entity_user_type = $user_details ['user_type'];
				$this->CI->entity_email = provab_decrypt($user_details ['email']);
				$this->CI->entity_voucher_email = $user_details ['voucher_email'];
				$this->CI->entity_name = get_enum_list ( 'title', $user_details ['title'] ) . ' ' . ucfirst ( $user_details ['first_name'] ) . ' ' . ucfirst ( $user_details ['last_name'] );
				$this->CI->entity_address = $user_details ['address'];
				$this->CI->entity_phone = $user_details ['phone'];
				$this->CI->entity_country_code = $user_details ['country_code'];
				$this->CI->entity_status = $user_details ['status'];
				$this->CI->entity_date_of_birth = $user_details ['date_of_birth'];
				$this->CI->entity_image = $user_details ['image'];
				$this->CI->entity_creation = $user_details ['created_datetime'];
                                $this->CI->entity_gst_number =  $user_details ['gst_number'];
				if (empty ( $email ) == false and empty ( $password ) == false) {
					// SETTING SESSION DATA
					$user_session_data = array ();

					$user_session_data [AUTH_USER_POINTER] = $user_details ['uuid'];
					$user_session_data [LOGIN_POINTER] = intval ( $this->update_login_manager () );
					$this->CI->session->set_userdata ( $user_session_data );


					$cookie = array (
							'name' => 'login_cookie',
							'value' => $user_details ['uuid'],
							'expire' => PROJECT_COOKIE_TIME,
							'path' => PROJECT_COOKIE_PATH 
					);

					$sparam = @$this->CI->input->cookie ( 'login_cookie', TRUE );
					if(empty($sparam)){
						$this->CI->input->set_cookie ( $cookie );
					}
				


					// $this->CI->session->set_userdata(array(AUTH_USER_POINTER => $user_details['user_id'], LOGIN_POINTER => intval($this->update_login_manager())));
				}
			}
		}
	}

	/**
	 * function to update login time and logout time details of user when user
	 * login or logout.
	 */
	function update_login_manager() {
		return $this->CI->user_model->create_login_auth_record (provab_encrypt($this->CI->entity_uuid), $this->CI->entity_user_type, $this->CI->entity_user_id, $this->CI->entity_name );
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

		$custom_menu_search1 = $this->CI->custom_db->single_table_records ( 'custom_menu_search', '*',array ('status' => 1));
		$this->CI->custom_menu_search=$custom_menu_search1; 
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
			$this->CI->bus_engine_system = $system; //test/live
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
	        //$urls['flight_url'] = 'http://192.168.0.87/travelomatix_live_services/webservices/flight/service/';
	        unset($urls['flight_url']);
	        $urls['flight_url'] = 'http://pacetravels.org/service/webservices/index.php/flight/service/';
			
			//api urls
			$this->CI->flight_url = $urls['flight_url'];
			$this->CI->hotel_url = $urls['hotel_url'];
			$this->CI->bus_url = $urls['bus_url'];
			$this->CI->transferv1_url = $urls['transfer_url'];
			$this->CI->sightseeing_url =  $urls['activity_url'];
			$this->CI->car_url = $urls['car_url'];
			$this->CI->external_service = $urls['external_service'];
			$this->CI->domain_key = $domain_list['data'][0]['domain_key'];
			// debug($config);exit;
			//passwords
			
		}
		
	}
	/**
	 * Set all the active User previleges for doamin
	 */
	function initialize_domain_previleges() {
		
		
		// set global modules data
		$user_id = $this->CI->session->userdata ( AUTH_USER_POINTER );
		
		if (! empty ( $user_id )) {
			
			
			$user_previleges = $this->CI->module_model->get_user_privillages ();
			// debug($user_previleges);exit;
			if(valid_array($user_previleges)){
				$active_user_previleges = array_values(magical_converter(array('k' => 'privilege_key', 'v' => 'privilege_key'), array('data' => $user_previleges)));
				$active_user_previleges_url = array_values(magical_converter(array('k' => 'privilege_key', 'v' => 'url'), array('data' => $user_previleges)));
				$this->CI->active_user_previleges = $active_user_previleges;
			}
			
			$controller = $segment = $this->CI->uri->segment(1);
			$function = $segment = $this->CI->uri->segment(2);
			
			if(empty($function) == false && $controller!='menu' && $controller!='general' && $function!='not_access' && ($this->CI->entity_user_type!=ADMIN)){
				$url = $controller.'/'.$function;
				
				$array_search = in_array($url, $active_user_previleges_url);
				
				if(empty($array_search) == true){
					$allowed_controllers = array();
					foreach($active_user_previleges_url AS $key=>$allowed_url)
					{
						$allowed_controllers[] = explode("/", $allowed_url)[0];
					}
					if(empty(in_array($controller, $allowed_controllers)) == true)
						redirect(base_url().'index.php/utilities/not_access');
				}
			}
			
		}
	}

}
