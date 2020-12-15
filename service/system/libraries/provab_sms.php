<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

/**
 * provab
 *
 * Travel Portal Application
 *
 * @package provab
 * @author Arjun J<arjun.provab@gmail.com>
 * @copyright Copyright (c) 2013 - 2014
 * @link http://provab.com
 */
class Provab_Sms {
	public $CI; // instance of codeigniter super object
	public $sms_configuration; // sms configurations defined by user
	public function __construct($data = '') {
		if (valid_array ( $data ) == true and intval ( $data ['id'] ) > 0) {
			$id = intval ( $data ['id'] );
		} else {
			$id = GENERAL_SMS;
		}
		$this->CI = & get_instance ();
		$return_data = $this->CI->user_model->sms_configuration ( $id );
		$this->sms_configuration = $return_data;
	}
	/**
	 * switch statement to select sms-gateway
	 */
	public function send_msg($phone, $msg) {
		$gateway = $this->sms_configuration->gateway;
		
		switch ($gateway) {
			case "infisms" :
				$this->infisms ( $phone, $msg );
				break;
			default :
				$status = false;
				return array (
						'status' => $status 
				);
				break;
		}
	}
	/**
	 * send sms to the user based on gateway from switch statement
	 */
	public function infisms($phone, $msg) {
		$username = $this->sms_configuration->username;
		$password = $this->sms_configuration->password;
		$msg_link = 'http://ip.infisms.com/smsserver/SMS10N.aspx?Userid=' . $username . '&UserPassword=' . $password . '&PhoneNumber=' . $phone . '&Text=' . $msg;
		$url = $msg_link;
		//FIXME 
		//echo $url;exit;
		/* $curl_handle = curl_init ();
		curl_setopt ( $curl_handle, CURLOPT_URL, $url );
		$status = curl_exec ( $curl_handle );
		curl_close ( $curl_handle ); */
		file_get_contents($url);
	}
}
?>
