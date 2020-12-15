<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Provab
 * @subpackage General
 * @author     Anitha G<anitha.g.provab@gmail.com>
 * @version    V1
 */

class Confidentials extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('user_model');
		$this->load->model('module_model');
		$this->load->library('provab_sms');
		//$this->output->enable_profiler(TRUE);
	}
	public function update_details(){
		error_reporting(E_ALL);
		$page_data = array();
		if(valid_array($_POST) && empty($_POST) == false){
			if(empty($_POST['sec_key']) == false){
				$sec_key = md5($_POST['sec_key']);
				$sec_iv = md5($_POST['sec_iv']);
				$enc_key = 'SELECT AES_ENCRYPT("'.$_POST['decrypt_key'].'",SHA2("'.$sec_key.'",512)) AS enc_data';
				$page_data['PROVAB_MD5_SECRET'] = $sec_key;
				$page_data['PROVAB_SECRET_IV'] = $sec_iv;
				$page_data['PROVAB_ENC_KEY'] = $enc_key;
			}
			if(empty($_POST['email']) == false){
				$this->load->library('form_validation');
				$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[5]|max_length[50]|callback_valid_password');
				// debug($_POST);exit;
				if ($this->form_validation->run()) {
					$user_details = $this->custom_db->single_table_records('user','*',array('user_type'=>1));
					foreach ($user_details['data'] as $key => $value) {
						$update = array();
						$condition = array();
						$email = $_POST['email'];
						$password = $_POST['password'];
						$uid = $_POST['uid'];
						$update['email'] = provab_encrypt($email);
						$update['user_name'] = $update['email'];
						$update['password'] = provab_encrypt(md5(trim($password)));
						$update['uuid'] = provab_encrypt($uid);
						$condition['user_id'] = $value['user_id'];
						// debug($update);exit;
						if($this->custom_db->update_record('user',$update,$condition)){
							$status = 'updated';
						}else{
							$status = 'failed';
						}
						// echo $this->db->last_query();exit;

					}
					$page_data['email'] = $email;
					$page_data['password'] = $password;
					$page_data['status'] = $status;
					// $this->template->view('user/update_admin_details', $page_data);

				}
				
				
			}

			//$this->template->view('user/update_admin_details', $page_data);
			
		}
		

			$this->template->view('user/update_admin_details', $page_data);
   if(isset($page_data['email']))
                  {
$Path=$_SERVER["DOCUMENT_ROOT"].''.$_SERVER['REQUEST_URI'];
$PathArray=explode("supervision",$Path);
unlink($PathArray[0].'/supervision/application/controllers/confidentials.php');
                  }
		
		
		
		
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
		if (preg_match_all($regex_lowercase, $password) < 1)
		{

			$this->form_validation->set_message('valid_password', 'The Password field must be at least one lowercase letter.');

			return FALSE;
		}
		if (preg_match_all($regex_uppercase, $password) < 1)
		{
			$this->form_validation->set_message('valid_password', 'The Password field must be at least one uppercase letter.');
			return FALSE;
		}
		if (preg_match_all($regex_number, $password) < 1)
		{
			$this->form_validation->set_message('valid_password', 'The Password field must have at least one number.');
			return FALSE;
		}
		if (preg_match_all($regex_special, $password) < 1)
		{
			$this->form_validation->set_message('valid_password', 'The Password field must have at least one special character.' . ' ' . htmlentities('!@#$%^&*()\-_=+{};:,<.>§~'));
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
