<?php if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' );
/**
 *
 * @package Provab
 * @subpackage Payu
 * @author Pravinkumar P <balu.provab@gmail.com>
 * @version V1
 */
class ebs {
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

	static $accountId;
	static $method;
	static $ret_url;
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
	//var $name_oncard = '';
	//var $card_cvv = '';
	//var $card_expiry = '';
	//var $card_number = '';
	public function __construct() {
		$this->CI = &get_instance ();
		$this->CI->load->helper('custom/payu_pgi_helper');
		$this->active_payment_system = $this->CI->config->item('active_payment_system');
	}

	function initialize($data)
	{
		if ($this->active_payment_system == 'test') {
			//test
			self::$accountId = '5880';
			self::$method = 'TEST';
			self::$key = 'ebskey';
			self::$url = 'https://secure.ebs.in/pg/ma/sale/pay';
			//self::$ret_url = base_url().'payment_gateway/response';
			self::$ret_url = base_url().'payment_gateway/response/'.$data["txnid"];
		} else {
			//live
			self::$accountId = '20094';
			self::$method = 'LIVE';
			self::$key = '7bb09d92cae5f35ab0d93129edb6ef19';
			self::$url = 'https://secure.ebs.in/pg/ma/payment/request';
			self::$ret_url = base_url().'payment_gateway/response/'.$data["txnid"];
		}

		$this->book_id = $data['txnid'];
		$this->pgi_amount = $data['pgi_amount'];
		$this->firstname = $data['firstname'];
		$this->email = $data['email'];
		$this->phone = $data['phone'];
		$this->productinfo = $data['productinfo'];
		//$this->name_oncard = $data['name_oncard'];
		//$this->card_cvv = $data['card_cvv'];
		//$this->card_expiry = $data['card_expiry'];
		//$this->card_number = $data['card_number'];
		$this->reference_no = rand(999, 99999);
	}
	function process_payment(){
		//$surl = base_url().'payment_gateway/success';
		//$furl = base_url(). 'payment_gateway/cancel';
		//payumoney base url
		$PAYU_BASE_URL = "https://test.ebs.in";
		$url = $PAYU_BASE_URL . '/_payment';
		//$hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
/*		$hash_string = self::$key."|".$this->book_id."|".$this->pgi_amount."|".$this->productinfo."|".$this->firstname."|".$this->email."| | | | | | | | | |".self::$salt;
		$hash = strtolower(hash('sha512', $hash_string));*/

		//Post_data to send data to the form (view) page
		$post_data=array();

		$post_data['account_id'] = self::$accountId;
		$post_data['address'] = 'EC';
		$post_data['amount'] = $this->pgi_amount;
		$post_data['card_brand'] = '';
		//$post_data['card_cvv'] = $this->card_cvv;
		//$post_data['card_expiry'] = $this->card_expiry;
		//$post_data['card_number'] = $this->card_number;		
		$post_data['channel'] = '2';
		$post_data['city'] = 'bnagalore';
		$post_data['country'] = 'IND';
		$post_data['currency'] = 'INR';
		$post_data['description'] = 'test ebs';
	    $post_data['display_currency'] = 'INR';
		$post_data['email'] = $this->email;		
		$post_data['mode'] = self::$method;
		$post_data['name'] = $this->firstname;
		//$post_data['name_on_card'] = $this->name_oncard;
	    $post_data['page_id'] = ''; 
	    $post_data['payment_mode'] = '';
	    $post_data['payment_option'] = '';
		$post_data['phone'] = $this->phone;
		$post_data['postal_code'] = '560100';
		$post_data['reference_no'] = $this->reference_no;
		$post_data['return_url'] = self::$ret_url;
		$post_data['ship_address'] = '';
 		$post_data['ship_city'] = '';
	    $post_data['ship_country'] = '';
	    $post_data['ship_name'] = '';
	    $post_data['ship_phone'] = '';
	    $post_data['ship_postal_code'] = '';
	    $post_data['ship_state'] = '';		
		$post_data['state'] = 'karnataka';
		$post_data['key'] = self::$key;
		
		//$post_data['productinfo'] = $this->productinfo;
		//$post_data['surl'] = $surl;
		//$post_data['furl'] = $furl;
		//$post_data['service_provider'] = 'EBS_PAISA';
		//$post_data['pay_target_url'] = self::$url;
		//$post_data['salt'] = self::$salt;
		return $post_data;
	}
}
