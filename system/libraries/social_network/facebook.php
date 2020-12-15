<?php if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' );
/**
 *
 * @package		Provab
 * @subpackage	FACEBOOK LOGIN
 * @author		Pravinkumar P<balu.provab@gmail.com>
 * @version		V1
 */
class Facebook {
	//FAcebook Client ID Needed For Application
	var $CI;
	public $fb_app_id;//fb_config defined by user
	//const APP_ID = '1677506025831023';//sunango.in
	//const APP_ID = '607193859445574';//http://demo.travelomatix.com/index.php/auth/register
	
	public function __construct($data = '') {
		if (valid_array ( $data ) == true and intval ( $data ['id'] ) > 0) {
			$id = intval ( $data ['id'] );
		} else {
			$id = GENERAL_SOCIAL;
		}
		$this->CI = &get_instance ();
		$this->CI->load->helper('custom/payu_pgi_helper');
		$this->CI->load->model('user_model');
		$return_data = $this->CI->user_model->fb_network_configuration ( $id,'facebook' );
		$this->social_configuration = $return_data;
		if ($this->social_configuration !=''){
			$this->fb_app_id = $this->social_configuration;
		}else {
			$this->fb_app_id ='';
		}
	}
	/**
	 * Load Facebook Login Button
	 */
	function login_button()
	{
		return $this->CI->template->isolated_view('social_network/facebook/facebook_login_auth',array('app_id' => $this->fb_app_id));
	}
	function share_button()
	{
		return $this->CI->template->isolated_view('social_network/facebook/facebook_share_button');
	}
}