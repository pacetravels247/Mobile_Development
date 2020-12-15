<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// ------------------------------------------------------------------------
/**
 * Controller for all ajax activities
 *
 * @package    Provab
 * @subpackage ajax loaders
 * @author     Balu A J<balu.provab@gmail.com>
 * @version    V1
 */
// ------------------------------------------------------------------------

class Auth extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('user_model');
		$this->load->library('provab_sms');
		
		$this->load->library('social_network/facebook');
	}

	/**
	 * index page of application will be loaded here
	 */
	function index()
	{

	}

	function register_on_light_box()
	{
		if (is_logged_in_user() == false) {
			$op_data = $this->input->post();
			//debug($op_data);exit;
			$status = false;
			$data = '';
			//data posted
			if (valid_array($op_data) == true) {
				//validate
				$this->load->library('form_validation');
				$this->form_validation->set_rules('email', 'Email', 'valid_email|required|max_length[80]|callback_username_check');//Username to be unique
				$this->form_validation->set_rules('password', 'Password', 'matches[confirm_password]|min_length[5]|max_length[45]|required|callback_valid_password');
				$this->form_validation->set_rules('confirm_password', 'Confirm');
				$this->form_validation->set_rules('first_name', 'Name', 'xss_clean|required|min_length[2]|max_length[45]');
				//$this->form_validation->set_rules('phone', 'Phone', 'numeric|required|max_length[10]');
				if ($this->form_validation->run()) {
					//Create New User
					
					$creation = $this->user_model->create_user($op_data['email'], $op_data['password'], $op_data['first_name'], $op_data['country_code'],$op_data['phone']);
					
					//Sms config & Checkpoint
					/* if(active_sms_checkpoint('registration'))
					{
					$msg = "Dear ".$op_data['first_name']." Thank you for registering with us.Verification link has been sent to your email id";
					$msg = urlencode($msg);
					$this->provab_sms->send_msg($op_data['phone'],$msg);
					} */
					//Sms will be sent
					if ($creation['status'] == true and $creation['data'][0] == true) {
						//send activation mail
						$original = $creation['data'][0]['user_id'];
						$encoded_data = rand(100,999).base64_encode($original);
						$url = base_url().'index.php/general/activate_account_status?origin='.$encoded_data;
						$creation['data'][0]['activation_link'] = $url;

						$creation['data'][0]['email']  = provab_decrypt($creation['data'][0]['email']);

						$mail_template = $this->template->isolated_view('user/user_registration_template', $creation['data'][0]);
						$email = $creation['data'][0]['email'];

						$this->load->library('provab_mailer');
						$mail_status = $this->provab_mailer->send_mail($email, 'New-User Account Activation', $mail_template);
						//debug($mail_status);exit;
						$status = true;
						$data = get_app_message('AL002');
					} else {
						$data = get_app_message('AL003');
					}
				} else {
					$data = validation_errors();
				}
			}
			header('content-type:application/json');
			echo json_encode(array('status' => $status, 'data' => $data));
			exit;
		} else {
			redirect(base_url());
		}
	}

	/**
	 * Balu A
	 */
	function register()
	{
		if (is_logged_in_user() == false) {
			$op_data = $this->input->post();
			//data posted
			if (valid_array($op_data) == true) {
				//validate

				$this->load->library('form_validation');
				$this->form_validation->set_rules('email', 'Email', 'valid_email|required|max_length[80]|callback_username_check');//Username to be unique
				$this->form_validation->set_rules('password', 'Password', 'matches[confirm_password]|min_length[5]|max_length[45]|required');
				$this->form_validation->set_rules('confirm_password', 'Confirm');
				$this->form_validation->set_rules('first_name', 'Name', 'xss_clean|required|min_length[2]|max_length[45]');
				//$this->form_validation->set_rules('phone', 'Phone', 'numeric|required|max_length[10]');
				if ($this->form_validation->run()) {
					//Create New User
					$creation = $this->user_model->create_user($op_data['email'], $op_data['password'], $op_data['first_name'], $op_data['phone']);
						
					//Sms config & Checkpoint
					/* if(active_sms_checkpoint('registration'))
					{
						$msg = "Dear ".$op_data['first_name']." Thank you for registering with us.Verification link has been sent to your email id";
						$msg = urlencode($msg);
						$this->provab_sms->send_msg($op_data['phone'],$msg);
					} */
					//Sms will be sent
					if ($creation['status'] == true and $creation['data'][0] == true) {

						//send activation mail
						$original = $creation['data'][0]['user_id'];
						$encoded_data = rand(100,999).base64_encode($original);
						$url = base_url().'index.php/general/activate_account_status?origin='.$encoded_data;
						$creation['data'][0]['activation_link'] = $url;
						$mail_template = $this->template->isolated_view('user/user_registration_template', $creation['data'][0]);
						echo $mail_template;exit;
						$email = $creation['data'][0]['email'];
						$this->load->library('provab_mailer');
						//echo $mail_template;exit;
						$this->provab_mailer->send_mail($email, 'New-User Account Activation', $mail_template);


						$this->session->set_flashdata(array('message' => 'AL002', 'type' => SUCCESS_MESSAGE));
						redirect(base_url().'index.php/auth/register');
					} else {
						$this->session->set_flashdata(array('message' => 'AL003', 'type' => ERROR_MESSAGE));
					}
				}
			}
			$this->template->view('user/register', array('form' => $op_data));
		} else {
			redirect(base_url());
		}
	}
	/*
	 * Jaganath
	 * Add guest User details
	 */
	function register_guest_user()
	{
		$post_data = $this->input->post();
		$status = false;
		$data = '';
		if (is_logged_in_user() == false && empty($post_data['username']) == false && empty($post_data['mobile_number']) == false) {
			$user_name = trim($post_data['username']);
			$mobile_number = trim($post_data['mobile_number']);
			$user_exists = $this->username_check($user_name);

			$status = true;
			if($user_exists == false) {//Check User Exists based on Username
				$data = 'User Exists';
			} else {//If not exists add the guest user details
				$password = 'test';
				$first_name = 'user';
				$country_code = trim($post_data['country_code']);
				$creation_source = 'guest';
				$user_type = 0;
				$creation = $this->user_model->create_user($user_name, $password, $first_name, 
					$country_code, $mobile_number, $creation_source, $user_type);
				$data = 'Added guest User';
			}
		}
		header('content-type:application/json');
		echo json_encode(array('status' => $status, 'data' => $data));
		exit;
	}
	/**
	 * Call back function to check username availability
	 * @param string $name
	 */
	public function username_check($name)
	{
		$condition['email'] = provab_encrypt($name);
		$condition['user_type'] = B2C_USER;
		$condition['domain_list_fk'] = intval(get_domain_auth_id());
		$data = $this->custom_db->single_table_records('user', 'user_id', $condition);
		if ($data['status'] == SUCCESS_STATUS and valid_array($data['data']) == true) {
			$this->form_validation->set_message('username_check', $name.' Already Registered!!!');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	/**
	 * Balu A
	 */
	function forgot_password()
	{

		$post_data = $this->input->post();
		extract($post_data);
		//email, phone
		$condition['email'] = provab_encrypt($email);
		//$condition['phone'] = $phone;
		$condition['status'] = ACTIVE;
		$condition['user_type'] = B2C_USER;
		$user_record = $this->custom_db->single_table_records('user', 'email, password, user_id, first_name, last_name', $condition);
		//echo $this->db->last_query();exit;
		if ($user_record['status'] == true and valid_array($user_record['data']) == true) {

			//Sms config & Checkpoint
			/* if(active_sms_checkpoint('forget_password'))
			{
			$msg = "Dear ".$user_record['data'][0]['first_name']." Your Password details has been sent to your email id";
			//print($msg); exit;
			$msg = urlencode($msg);
			$this->provab_sms->send_msg($phone,$msg);
			} */
			//sms will be sent

			$user_record['data'][0]['password'] = time();
			$user_record['data'][0]['email'] = provab_decrypt($user_record['data'][0]['email']);
			//send email
			$mail_template = $this->template->isolated_view('user/forgot_password_template', $user_record['data'][0]);
			$user_record['data'][0]['password'] = provab_encrypt(md5(trim($user_record['data'][0]['password'])));
			$user_record['data'][0]['email'] = provab_encrypt($user_record['data'][0]['email']);
			$this->custom_db->update_record('user', $user_record['data'][0], array('user_id' => intval($user_record['data'][0]['user_id'])));
			$this->load->library('provab_mailer');
			//echo $mail_template;exit;
			//$this->provab_mailer->send_mail($email, 'Password Reset', $mail_template);
			$this->provab_mailer->send_mail($email, 'Password Reset', $mail_template);
			$data = 'Password Has Been Reset Successfully and Sent To Your Email ID';
			$status = true;
		} else {
			$data = 'Please Provide Correct Data To Identify Your Account';
			$status = false;
		}
		header('content-type:application/json');
		echo json_encode(array('status' => $status, 'data' => $data));
		exit;
	}

	/**
	 * Balu A
	 */
	function login()
	{
		$post_data = $this->input->post();
		
		extract($post_data);
		

		$status = false;
		if (is_logged_in_user() == false) {
			//email, phone
			
			
			$user_record = $this->user_model->active_b2c_user($username, $password);
			//debug($user_record);exit;
			
			if ($user_record != '' and valid_array($user_record) == true) {
				if($user_record[0]['status'] != 0){
					//send email
					$data = 'Login Successful';
					$status = true;
					//create login pointer
					
					$user_type = $user_record[0]['user_type'];
					$auth_user_pointer = $user_record[0]['uuid'];
					$user_id = $user_record[0]['user_id'];
					$first_name = $user_record[0]['first_name'];
					$this->create_login_session($auth_user_pointer, $user_type, $user_id, $first_name);
				}
				else{
					$data = 'Username is Inactive Please Contact Admin!!!';
					$status = false;
				}
				
			} else {
				$data = 'Username And Password Does Not Match!!!';
				$status = false;
			}
		}

		header('content-type:application/json');
		echo json_encode(array('status' => $status, 'data' => $data));
		exit;
	}
	/**
	 *
	 * @param string $auth_user_pointer	Unique user id
	 * @param string $user_type			User type
	 * @param number $user_id			Unique id of user - origin
	 * @param string $first_name		First name of the user
	 */
	private function create_login_session($auth_user_pointer, $user_type, $user_id, $first_name)
	{
		
		$login_pointer = $this->user_model->create_login_auth_record($auth_user_pointer, $user_type, $user_id, $first_name);
		$this->session->set_userdata(array(AUTH_USER_POINTER => $auth_user_pointer, LOGIN_POINTER => $login_pointer));
	}

	/**
	 * Network Source
	 */
	function social_network_login_auth($domain_name)
	{
		$response['status'] = FAILURE_STATUS;
		$response['message'] = 'Remote IO Error!!!';
		if (is_logged_in_user() == false) {
			$params = $this->input->post();
			switch ((string)strtolower($domain_name)) {
				case 'google' :
					$email1 = provab_encrypt($params['email']);
					$email = $params['email'];
					$first_name = $params['name'];
					$cond[] = array('U.email', '=', $this->db->escape($email1));
					$cond[] = array('U.user_type', '=', B2C_USER);
					$existing_user = $this->user_model->get_user_details($cond);
					//new user
					if (valid_array($existing_user) == false) {
						$this->user_model->create_user($email, 'password', $first_name, '', 'google');
						$existing_user = $this->user_model->get_user_details($cond);
					}
					break;
				case 'facebook' :
					$url_params = $this->input->get();
					//debug($params);debug($url_params);exit;
					$email1 = provab_encrypt($params['email']);
					$email = $params['email'];
					$first_name = $params['name'];
					$cond[] = array('U.email', '=', $this->db->escape($email1));
					$cond[] = array('U.user_type', '=', B2C_USER);
					$existing_user = $this->user_model->get_user_details($cond);
					//new user
					if (valid_array($existing_user) == false) {
						$this->user_model->create_user($email, 'password', $first_name, '', 'facebook');
						$existing_user = $this->user_model->get_user_details($cond);
					}
					break;
				default:
					break;
			}

			if (valid_array($existing_user) == true) {
				//create session
				$response['status'] = SUCCESS_STATUS;
				$response['message'] = 'Login Successfull!!!';

				$user_type = 4;
				$auth_user_pointer = $existing_user[0]['uuid'];
				$user_id = $existing_user[0]['user_id'];
				$first_name = $existing_user[0]['first_name'];
				$this->create_login_session($auth_user_pointer, $user_type, $user_id, $first_name);
			}
		}

		header('content-type:application/json');
		echo json_encode($response);
		exit;
	}

	function change_password()
	{
		validate_user_login();
		$data=array();
		$page_data['form_data'] = $this->input->post();
		//debug($page_data);exit;
		if(valid_array($page_data['form_data'])==TRUE) {
			//$this->current_page->set_auto_validator();
			$this->load->library('form_validation');
			$this->form_validation->set_rules('current_password', 'Current Password', 'required|min_length[5]|max_length[45]|callback_password_check');
			$this->form_validation->set_rules('new_password', 'New Password', 'matches[confirm_password]|min_length[5]|max_length[45]|required|callback_valid_password');
			$this->form_validation->set_rules('confirm_password', 'Confirm', 'callback_check_new_password');
			if ($this->form_validation->run()) {
			
				$table_name="user";
				/** Checking New Password and Old Password Are Same OR Not **/
				$condition['password'] = provab_encrypt(md5(trim($this->input->post('new_password'))));

				$condition['user_id'] = $this->entity_user_id;
				$check_pwd = $this->custom_db->single_table_records($table_name,'password',$condition);
				//debug($check_pwd);exit;
				if($check_pwd['status'] == false) {//If New Password is not same as Current Password
					$condition['password'] = provab_encrypt(md5(trim($this->input->post('current_password'))));

					$condition['user_id'] = $this->entity_user_id;
					
					$data['password'] = provab_encrypt(md5(trim($this->input->post('new_password'))));

					$update_res = $this->custom_db->update_record($table_name, $data, $condition);
					if($update_res)	{
						$this->application_logger->change_password($this->entity_name);
						$this->session->set_flashdata(array('message' => 'UL0010', 'type' => SUCCESS_MESSAGE));
						redirect("index.php/auth/change_password");
					} else {
						$this->session->set_flashdata(array('message' => 'UL0011', 'type' => ERROR_MESSAGE));
						redirect("index.php/auth/change_password");
						/*$data['msg'] = 'UL0011';
						 $data['type'] = ERROR_MESSAGE;*/
					}
				} else {
					$this->session->set_flashdata(array('message' => 'UL0012', 'type'=>WARNING_MESSAGE));
					//debug($this->session);exit;
					redirect("index.php/auth/change_password");
					//redirect('general/change_password?uid='.urlencode($get_data['uid']));
				}
			}
		}
		$user_details = $this->user_model->get_current_user_details();
		$data['form_data'] = $user_details[0];
		$this->template->view('user/change_password', $data);
	}

	/**
	 *  user has already logged in or not
	 */
	function invalid_request()
	{

	}

	/**
	 * Logout function for logout from account and unset all the session variables
	 */
	function initilize_logout() {
		if (is_logged_in_user()) {
			$user_id = $this->session->userdata(AUTH_USER_POINTER);
			$login_id = $this->session->userdata(LOGIN_POINTER);
			$this->user_model->update_login_manager($user_id, $login_id);
			$this->session->unset_userdata(array(AUTH_USER_POINTER => '',LOGIN_POINTER => ''));
		} else {
			$user_id = $this->session->userdata(AUTH_USER_POINTER);
			$login_id = $this->session->userdata(LOGIN_POINTER);
			$this->session->unset_userdata(array(AUTH_USER_POINTER => '',LOGIN_POINTER => ''));
		}
		redirect(base_url());
	}
	/**
	 * Ajax Logout
	 * Logout function for logout from account and unset all the session variables
	 */
	function ajax_logout() 
	{
		$data = '';
		$status = false;
		if (is_logged_in_user()) {
			$user_id = $this->session->userdata(AUTH_USER_POINTER);
			$login_id = $this->session->userdata(LOGIN_POINTER);
			$this->user_model->update_login_manager($user_id, $login_id);
			$this->session->unset_userdata(array(AUTH_USER_POINTER => '',LOGIN_POINTER => ''));
			$status = true;
			$data = 'Logout Successfull';
		} else {
			$user_id = $this->session->userdata(AUTH_USER_POINTER);
			$login_id = $this->session->userdata(LOGIN_POINTER);
			$this->session->unset_userdata(array(AUTH_USER_POINTER => '',LOGIN_POINTER => ''));
			$status = false;
			$data = 'User Not Logged In!!!';
		}
		header('content-type:application/json');
		echo json_encode(array('status' => $status, 'data' => $data));
		exit;
	}
	/**
	 * Validate the password
	 *
	 * @param string $password
	 *
	 * @return bool
	 */
	public function valid_password($password)
	{
		$password = trim($password);
		$regex_lowercase = '/[a-z]/';
		$regex_uppercase = '/[A-Z]/';
		$regex_number = '/[0-9]/';
		$regex_special = '/[!@#$%^&*()\-_=+{};:,<.>§~]/';
		if (empty($password))
		{
			$this->form_validation->set_message('valid_password', 'The Password field is required.');
			return FALSE;
		}
		if (preg_match_all($regex_lowercase, $password) < 1 || preg_match_all($regex_uppercase, $password) < 1 || preg_match_all($regex_number, $password) < 1 || preg_match_all($regex_special, $password) < 1)
		{
			$this->form_validation->set_message('valid_password', 'The Password field must be at least one lowercase letter, one uppercase letter, one number, one special character.');
			return FALSE;
		}
		if (strlen($password) < 5)
		{
			$this->form_validation->set_message('valid_password', 'The Password field must be at least 5 characters in length.');
			return FALSE;
		}
		if (strlen($password) > 32)
		{
			$this->form_validation->set_message('valid_password', 'The Password field cannot exceed 32 characters in length.');
			return FALSE;
		}
		return TRUE;
	}
}
