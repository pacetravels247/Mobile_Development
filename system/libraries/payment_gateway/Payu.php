<?php if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' );
/**
 *
 * @package Provab
 * @subpackage Payu
 * @author Pravinkumar P <balu.provab@gmail.com>
 * @version V1
 */
class Payu {
	/*
	 * Client Live credentials -_-
	 * ------------------------------
	 * Merchant ID		:	5331200
	 * ------------------------------
	 * Merchant Key		:	sZqbYi
	 * Merchant Salt	:	eMfHb7uk
	 * URL  :https://secure.payu.in
	 * ______________________________
	 */

	/**
	 * Client Test credentials -_-
	 * ------------------------------
	 * Merchant ID		:	4933825
	 * ------------------------------
	 * Merchant Key		:	4USjgC
	 * Merchant Salt	:	SCVEtzhP
	 * URL : https://test.payu.in
	 * ______________________________
	 */

	static $key;
	static $salt;
	static $url;

	var $active_payment_system;

	var $book_id = '';
	var $book_origin = '';
	var $pgi_amount = '';
	var $name = '';
	var $email = '';
	var $phone = '';
	var $productinfo = '';
	public function __construct() {
		$this->CI = &get_instance ();
		$this->CI->load->helper('custom/payu_pgi_helper');
		$this->active_payment_system = $this->CI->config->item('active_payment_system');
	}

	function initialize($data)
	{
		if ($this->active_payment_system == 'test') {
			//test
			self::$key = '4USjgC';
			self::$salt = 'SCVEtzhP';
			self::$url = 'https://test.payu.in';
			/*self::$key = 'gtKFFx';
			self::$salt = 'eCwWELxi';
			self::$url = 'https://test.payu.in';*/
		} else {
			//live
			self::$key = 'sZqbYi';
			self::$salt = 'eMfHb7uk';
			self::$url = 'https://secure.payu.in';
		}
		$this->book_id = $data['txnid'];
		$this->pgi_amount = $data['pgi_amount'];
		$this->firstname = $data['firstname'];
		$this->email = $data['email'];
		$this->phone = $data['phone'];
		$this->productinfo = $data['productinfo'];
	}
	function process_payment($obj=''){
		$surl = base_url().'index.php/payment_gateway/success';
		$furl = base_url(). 'index.php/payment_gateway/cancel';
		//payumoney base url
		$PAYU_BASE_URL = "https://test.payu.in";
		$url = $PAYU_BASE_URL . '/_payment';
		//$hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
		$hash_string = self::$key."|".$this->book_id."|".$this->pgi_amount."|".$this->productinfo."|".$this->firstname."|".$this->email."| | | | | | | | | |".self::$salt;
		$hash = strtolower(hash('sha512', $hash_string));
		//Post_data to send data to the form (view) page
		$post_data=array();
		$post_data['key'] = self::$key;
		$post_data['txnid'] = $this->book_id;
		$post_data['amount'] = $this->pgi_amount;
		$post_data['firstname'] = $this->firstname;
		$post_data['email'] = $this->email;
		$post_data['phone'] = $this->phone;
		$post_data['productinfo'] = $this->productinfo;
		$post_data['surl'] = $surl;
		$post_data['furl'] = $furl;
		$post_data['service_provider'] = 'payu_paisa';

		$post_data['pay_target_url'] = self::$url;
		$post_data['salt'] = self::$salt;
		$post_data['key'] = self::$key;
		return $post_data;
	}
}
