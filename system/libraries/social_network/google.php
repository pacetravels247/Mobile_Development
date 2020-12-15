<?php if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' );
/**
 *
 * @package		Provab
 * @subpackage	GOOGLE
 * @author		Balu A <balu.provab@gmail.com>
 * @version		V1
 */
class Google {
	//Google Client ID Needed For Application
	public $CI;
	//const CLIENT_ID = '1007217831534-84d9ndnrbireli03ainmnar8acb0kjuh.apps.googleusercontent.com';
	public $google_client_id;
	
	public function __construct($data = '') {
		if (valid_array ( $data ) == true and intval ( $data ['id'] ) > 0) {
			$id = intval ( $data ['id'] );
		} else {
			$id = GENERAL_SOCIAL;
		}
		$this->CI = & get_instance ();
		$this->CI->load->model('user_model');
		$rtn_data = $this->CI->user_model->google_network_configuration ( $id,'googleplus' );
	
		$this->social_configuration = $rtn_data;
		if ($this->social_configuration !=''){
		$this->google_client_id = $this->social_configuration;
		}else {
			$this->google_client_id ='';
		}
	}

	/**
	 * Load all the necessary files for integration
	 */
	function load_library()
	{
		return $this->CI->template->isolated_view('social_network/google/library', array('client_id' => $this->google_client_id));
	}

	/**
	 * Load Google Login Button
	 */
	function login_button()
	{
		return $this->CI->template->isolated_view('social_network/google/login');
	}
	
	/**
	 * Load Google Logout Button
	 */
	function logout_button()
	{
		return $this->CI->template->isolated_view('social_network/google/logout');
	}
	
	/**
	 * Register or update user details
	 */
	function register_user($email, $first_name, $last_name)
	{
		
	}
}