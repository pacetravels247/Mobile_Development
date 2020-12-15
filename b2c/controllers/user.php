<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Provab
 * @subpackage General
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V1
 */

class User extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('user_model');
		$this->load->model('module_model');
		//$this->output->enable_profiler(TRUE);
	}

	function create_default_domain($domain_key_name='192.168.0.26')
	{
		include_once DOMAIN_CONFIG.'default_domain_configuration.php';
	}

	/**
	 * index page of application will be loaded here
	 */
	function index()
	{
		if (is_logged_in_user()) {
			redirect('menu/index');
		}
	}

	/**
	 * User Profile Management
	 */
	function profile()
	{
		//echo $this->entity_user_id;exit;
		validate_user_login();
		$op_data = $this->input->post();
		
		$page_data = array();
		$this->load->model('transaction_model');
		$currency_obj = new Currency();
		$page_data['currency_obj'] = $currency_obj;
		if (valid_array($op_data) == true && empty($op_data['title']) == false && empty($op_data['first_name']) == false && empty($op_data['last_name']) == false &&
			empty($op_data['country_code']) == false && empty($op_data['phone']) == false && empty($op_data['address']) == false) {
			//Application Logger
			$notification_users = $this->user_model->get_admin_user_id();
			$remarks = $op_data['first_name'].' Updated Profile Details';
			$action_query_string = array();
			$action_query_string['user_id'] = $this->entity_user_id;
			$action_query_string['uuid'] = $this->entity_uuid;
			$action_query_string['user_type'] = B2C_USER;
			
			$this->application_logger->profile_update($op_data['first_name'], $remarks, $action_query_string, array(), $this->entity_user_id, $notification_users);
			
			$this->custom_db->update_record('user', $op_data, array('user_id' => $this->entity_user_id, 'uuid' => provab_encrypt($this->entity_uuid)));
			//PROFILE IMAGE UPLOAD
			if (valid_array($_FILES) == true and $_FILES['image']['error'] == 0 and $_FILES['image']['size'] > 0) {
				if( function_exists( "check_mime_image_type" ) ) {
				    if ( !check_mime_image_type( $_FILES['image']['tmp_name'] ) ) {
				    	echo "Please select the image files only (gif|jpg|png|jpeg)"; exit;
				    }
				}
                               
                      
                                
                                
				$config['upload_path'] = $this->template->domain_image_upload_path();//FIXME: Balu A get Correct Path
				
				$config['allowed_types'] = 'gif|jpg|png|jpeg';
				$config['file_name'] = $image_name = $_FILES['image']['name'];
				$config['max_size'] = '1000000';
				$config['max_width']  = '';
				$config['max_height']  = '';
				$config['remove_spaces']  = false;
				// debug($config);exit;
				//UPDATE
				$temp_record = $this->custom_db->single_table_records('user', 'image', array('user_id' => $this->entity_user_id));
				$icon = $temp_record['data'][0]['image'];
				//DELETE OLD FILES
				if (empty($icon) == false) {
					if (file_exists($config['upload_path'].$icon)) {
						unlink($config['upload_path'].$icon);
					}
				}
				$this->load->library('upload', $config);
				$this->upload->initialize($config);
				if ( ! $this->upload->do_upload('image')) {
					echo $this->upload->display_errors();
				} else {
					$image_data =  $this->upload->data();
				}
			
				$this->custom_db->update_record('user', array('image' => @$image_data['file_name']), array('user_id' => $this->entity_user_id));
			}
			$this->session->set_flashdata(array('message' => 'AL004', 'type' => SUCCESS_MESSAGE));
			if(empty($_SERVER['QUERY_STRING']) == false) {
				$query_string = '?'.$_SERVER['QUERY_STRING'];
			} else {
				$query_string = '';
			}
			redirect('user/profile'.$query_string);
		} else {
			$page_data['title'] = $this->entity_title;
			$page_data['first_name'] = $this->entity_first_name;
			$page_data['last_name'] = $this->entity_last_name;
			$page_data['full_name'] = $this->entity_name;

			$mobile_code = $this->db_cache_api->get_mobile_code($this->entity_country_code);
			// debug($mobile_code);exit;
			$page_data['mobile_code'] = $mobile_code;
			$page_data['user_country_code'] = $this->entity_country_code;
			$page_data['date_of_birth'] = date('d-m-Y', strtotime($this->entity_date_of_birth));
			$page_data['address'] = $this->entity_address;
			$page_data['phone'] = $this->entity_phone;
			$page_data['email'] = $this->entity_email;
			$page_data['profile_image'] = $this->entity_image;
			$page_data['signature'] = $this->entity_signature;
		}
		$this->load->library('booking_data_formatter');
		$booking_counts = $this->booking_data_formatter->get_booking_counts();
		$page_data['booking_counts'] = $booking_counts['data'];
		$country_code = $this->db_cache_api->get_country_code_list_profile();
		// debug($country_code);exit;
		$phone_code_array = array();
		foreach($country_code['data'] as $c_key => $c_value){
			$phone_code_array[$c_value['country_code']] = $c_value['name'].' '.$c_value['country_code'];
			
		}

		// debug($phone_code_array);exit;
		$page_data['phone_code_array'] = $phone_code_array;
		// debug($phone_code_array);exit;
		// $page_data['country_code'] = $country_code;
	
		$latest_transaction = $this->transaction_model->logs(array(), false, 0, 5);
		$latest_transaction = $this->booking_data_formatter->format_recent_transactions($latest_transaction, 'b2c');
		$page_data['latest_transaction'] = $latest_transaction['data']['transaction_details'];
		$traveller_details = $this->traveller_details();
		$page_data['user_passport_visa_details'] = $traveller_details['user_passport_visa_details'];
		$page_data['traveller_details'] = $traveller_details['traveller_details'];
		$page_data ['iso_country_list'] = $this->db_cache_api->get_iso_country_code ();
		$page_data ['country_list'] = $this->db_cache_api->get_iso_country_code ();
		$this->template->view('user/profile', $page_data);
	}

	/**
	 * Logout function for logout from account and unset all the session variables
	 */
	function initilize_logout(){
		redirect('auth/initilize_logout');
		if (is_logged_in_user()) {
			$this->general_model->update_login_manager($this->session->userdata(LOGIN_POINTER));
			$this->session->unset_userdata(array(AUTH_USER_POINTER => '',LOGIN_POINTER => '') );
			// added by nithin for unseting the email username
			$this->session->unset_userdata('mail_user');
		}
	}

	/**
	 * oops page of application will be loaded here
	 */
	public function ooops()
	{
		$this->template->view('utilities/404.php');
	}
	

	/**
	 * Function to Change the Password of a User
	 */
	public function change_password()
	{
		validate_user_login();
		$data=array();
		$get_data = $this->input->get();
		if(isset($get_data['uid'])) {
			$user_id = intval($this->encrypt->decode($get_data['uid']));
		} else {
			redirect("general/initilize_logout");
		}
		$page_data['form_data'] = $this->input->post();
		if(valid_array($page_data['form_data'])==TRUE) {
			$this->current_page->set_auto_validator();
			if ($this->form_validation->run()) {
				$table_name="user";
				/** Checking New Password and Old Password Are Same OR Not **/
				$condition['password'] = md5($this->input->post('new_password'));
				$condition['user_id'] = $user_id;
				$check_pwd = $this->custom_db->single_table_records($table_name,'password',$condition);
				if(!$check_pwd['status']) {
					$condition['password'] = md5($this->input->post('current_password'));
					$condition['user_id'] = $user_id;
					$data['password'] = md5($this->input->post('new_password'));
					$update_res=$this->custom_db->update_record($table_name, $data, $condition);
					if($update_res)	{
						$this->session->set_flashdata(array('message' => 'UL0010', 'type' => SUCCESS_MESSAGE));
						refresh();
					} else {
						$this->session->set_flashdata(array('message' => 'UL0011', 'type' => ERROR_MESSAGE));
						refresh();
						/*$data['msg'] = 'UL0011';
						 $data['type'] = ERROR_MESSAGE;*/
					}
				} else {
					$this->session->set_flashdata(array('message' => 'UL0012', 'type'=>WARNING_MESSAGE));
					refresh();
					//redirect('general/change_password?uid='.urlencode($get_data['uid']));
				}
			}
		}
		$this->template->view('user/change_password', $data);
	}
	/**
	 * Balu A
	 * Add Traveller
	 */
	function add_traveller()
	{
		//FIXME:Make Codeigniter Validations -- Balu A
		validate_user_login();
		$post_data = $this->input->post();
		if(valid_array($post_data) == true && isset($post_data['traveller_first_name']) == true && empty($post_data['traveller_first_name']) == false && isset($post_data['traveller_date_of_birth']) == true && empty($post_data['traveller_date_of_birth']) == false 
		&& isset($post_data['traveller_email']) == true && isset($post_data['traveller_last_name']) == true) {
			$user_traveller_details = array();
			$user_traveller_details['first_name'] = $first_name = trim($post_data['traveller_first_name']);
			$user_traveller_details['last_name'] = trim($post_data['traveller_last_name']);
			$user_traveller_details['date_of_birth'] = $date_of_birth = date('Y-m-d', strtotime(trim($post_data['traveller_date_of_birth'])));
			$user_traveller_details['email'] = trim($post_data['traveller_email']);
			$user_traveller_details['created_by_id'] = $this->entity_user_id;
			$user_traveller_details['created_datetime'] = date('Y-m-d H:i:s');
			
			$check_traveller_data = $this->custom_db->single_table_records('user_traveller_details','*',array('created_by_id' => $this->entity_user_id, 'first_name' => $first_name, 'date_of_birth' => $date_of_birth));
			if($check_traveller_data['status'] == FAILURE_STATUS){
				$this->custom_db->insert_record('user_traveller_details', $user_traveller_details);
			}
			
		}
		if(empty($_SERVER['QUERY_STRING']) == false) {
			$query_string = '?'.$_SERVER['QUERY_STRING'];
		} else {
			$query_string = '';
		}
		redirect('user/profile'.$query_string);
	}
	/**
	 * Balu A
	 */
	function update_traveller_details()
	{
		//FIXME:Make Codeigniter Validations -- Balu A
		$post_data = $this->input->post();
		if(valid_array($post_data) == true && isset($post_data['origin']) == true && intval($post_data['origin']) > 0 &&
		isset($post_data['traveller_first_name']) == true && empty($post_data['traveller_first_name']) == false && isset($post_data['traveller_date_of_birth']) == true && empty($post_data['traveller_date_of_birth']) == false 
		&& isset($post_data['traveller_email']) == true && isset($post_data['traveller_last_name']) == true) {
			$user_traveller_details = array();
			$user_traveller_details['first_name'] = trim($post_data['traveller_first_name']);
			$user_traveller_details['last_name'] = trim($post_data['traveller_last_name']);
			$user_traveller_details['date_of_birth'] = date('Y-m-d', strtotime(trim($post_data['traveller_date_of_birth'])));
			$user_traveller_details['email'] = trim($post_data['traveller_email']);
			
			$user_traveller_details['passport_user_name'] = trim($post_data['passport_user_name']);
			$user_traveller_details['passport_nationality'] = trim($post_data['passport_nationality']);
			$user_traveller_details['passport_expiry_day'] = trim($post_data['passport_expiry_day']);
			$user_traveller_details['passport_expiry_month'] = trim($post_data['passport_expiry_month']);
			$user_traveller_details['passport_expiry_year'] = trim($post_data['passport_expiry_year']);
			$user_traveller_details['passport_number'] = trim($post_data['passport_number']);
			$user_traveller_details['passport_issuing_country'] = trim($post_data['passport_issuing_country']);
			$user_traveller_details['updated_by_id'] = $this->entity_user_id;
			$user_traveller_details['updated_datetime'] = date('Y-m-d H:i:s');
			$this->custom_db->update_record('user_traveller_details', $user_traveller_details, array('origin' => intval($post_data['origin'])));
		}
		if(empty($_SERVER['QUERY_STRING']) == false) {
			$query_string = '?'.$_SERVER['QUERY_STRING'];
		} else {
			$query_string = '';
		}
		redirect('user/profile'.$query_string);
	}
	/**
	 * Balu A
	 */
	function traveller_details()
	{
		$data = array();
		$data['user_passport_visa_details'] = array();
		$data['traveller_details'] = array();
		//traveller details
		$traveller_details = $this->custom_db->single_table_records('user_traveller_details', '*', array('created_by_id' => $this->entity_user_id, 'user_id' => 0));
		if($traveller_details['status'] == true) {
			$data['traveller_details'] = $traveller_details['data'];
		}
		//User PassportVisa details
		$user_passport_visa_details = $this->custom_db->single_table_records('user_traveller_details', '*', array('created_by_id' => $this->entity_user_id, 'user_id' => $this->entity_user_id));
		if($user_passport_visa_details['status'] == true) {
			$data['traveller_details'] = $user_passport_visa_details['data'][0];
		}
		return $data;
	}
}
