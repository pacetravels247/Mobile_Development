<?php if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' );
/**
 *
 * @package Provab
 * @subpackage Techp
 * @author Shashikumar Misal <shashikumar.misal@provabmail.com>
 * @version V1.0.0
 */
date_default_timezone_set('Asia/Calcutta');
require_once 'paytm_deps/encdec_paytm.php';
class Paytm {
	/*
	 * Client Live credentials -_-
	 * ------------------------------
	 * Merchant Id		: PACETR82735402549764
	 * ------------------------------
	 * Merchant Key		: 
	 * Merchant Website : pacetravelsweb
	 * Environment		: live	
	 * URL  : https://securegw.paytm.in/theia/processTransaction
	 * ______________________________
	 */

	/**
	 * Client Test credentials -_-
	 * ------------------------------
	 * Merchant Id		: PACETR82735402549764
	 * ------------------------------
	 * Merchant Key		: !0yiTmCoghK1ne3O	
	 * Merchant Website : pacetravelsweb
	 * Environment		: test	
	 * URL  : https://securegw-stage.paytm.in/theia/processTransaction
	 * ______________________________
	 */
	private $merchant_id;
	private $key;
	private $website;
	private $ind_type;
	private $channel_id;
	private $rurl;

	var $active_payment_system;

	var $book_id;
	var $cust_id;
	var $book_origin;
	var $pgi_amount;
	var $name;
	var $email;
	var $phone;
	var $txn_type;
	var $pg_txn_id;
	var $m_refund_id;
	var $refund_amt;
	var $productinfo;
	var $refund_url;
	var $payment_mode_only;
	var $payment_type_id;

	public function __construct() {
		$this->CI = &get_instance ();
		$this->active_payment_system = $this->CI->config->item('active_payment_system');
		if($this->active_payment_system=="test"){
			$this->merchant_id = 'PACETR82735402549764';
			$this->key = '!0yiTmCoghK1ne3O';
			$this->url = 'https://securegw-stage.paytm.in/theia/processTransaction';
			$this->refund_url = "https://securegw-stage.paytm.in/refund/apply";
		}
		else{
			$this->merchant_id = 'PACETR04684467431060';
			$this->key = '_HhdDPmiZFOmmPuz';
			$this->url = 'https://securegw.paytm.in/theia/processTransaction';
			$this->refund_url = "https://securegw.paytm.in/refund/apply";
		}
		$this->website = 'PACETRWEB';
		$this->ind_type = 'Retail102';
		$this->channel_id = 'WEB';
		$this->rurl = base_url().'index.php/payment_gateway/paytm_response';

		$this->cust_id = 'SOMEBODY';
	}

	function initialize($data)
	{
    	//Setting all values here
		$this->book_id = $data['txnid'];
		$this->pgi_amount = 1.00; //$data['pgi_amount'];
		$this->firstname = $data['firstname'];
		$this->email = $data['email'];
		$this->phone = $data['phone'];
		if(isset($data['txn_type']) &&  $data['txn_type'] == 'REFUND')
		{
			$this->txn_type = $data['txn_type'];
			$this->pg_txn_id = $data['pg_txnid'];
			$this->refund_amt = $data['refund_amt'];
			$this->m_refund_id = "R-".time(); //We generate this
		}
		if(isset($data['txn_type']) &&  $data['txn_type'] == 'INSTANT_RECHARGE')
		{
			$this->txn_type = $data['txn_type'];
			$this->payment_mode_only = $data["payment_mode_only"];
			$this->payment_type_id = $data["payment_type_id"];
		}

		/*We need to store product info value in session as Paytm does have param to send/receive this as request/response respectively.*/
		$GLOBALS["CI"]->session->set_userdata("paytm_productinfo", $data['productinfo']);
	}
	function process_payment($obj=NULL){
		$checkSum = "";
		$post_data = array();

		// Create an array having all required parameters for creating checksum.
		$post_data["MID"] = $this->merchant_id;
		$post_data["ORDER_ID"] = $this->book_id;
		$post_data["CUST_ID"] = $this->cust_id;
		$post_data["INDUSTRY_TYPE_ID"] = $this->ind_type;
		$post_data["CHANNEL_ID"] = $this->channel_id;
		$post_data["TXN_AMOUNT"] = $this->pgi_amount;
		$post_data["WEBSITE"] = $this->website;

		$post_data["CALLBACK_URL"] = $this->rurl;
		
		$post_data["MSISDN"] = $this->phone; //Mobile number of customer
		$post_data["EMAIL"] = $this->email; //Email ID of customer
		if($this->txn_type == "INSTANT_RECHARGE")
		{
			$post_data["PAYMENT_MODE_ONLY"] = $this->payment_mode_only;
			$post_data["PAYMENT_TYPE_ID"] = $this->payment_type_id;
		}
		
		$url = $this->url;

		//Here checksum string will return by getChecksumFromArray() function.
		$checkSum = getChecksumFromArray($post_data, $this->key);
		$ret_data = array("post_data"=>$post_data, "checksum"=>$checkSum, "url"=>$url);

		return $ret_data;
	}

	function process_refund()
	{
		$post_data["mid"] = $this->merchant_id;
		$post_data["orderId"] = $this->book_id;
		$post_data["txnType"] = $this->txn_type;
		$post_data["txnId"] = $this->pg_txn_id;
		$post_data["refId"] = $this->m_refund_id;
		$post_data["refundAmount"] = $this->refund_amt;

		$paytmParams["body"]=$post_data;

		$checksum = getChecksumFromString(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), $this->key);

		/* head parameters */
		$paytmParams["head"] = array(
		    /* This is used when you have two different merchant keys. In case you have only one please put - C11 */
		    "clientId"	=> "C11",
		    /* put generated checksum value here */
		    "signature"	=> $checksum
		);

		/* prepare JSON string for request */
		$post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);

		$ch = curl_init($this->refund_url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json")); 
		$response = curl_exec($ch);
		return $response;
	}

	function read_refund_response($response)
	{
		$response = json_decode($response);
	    $resp_array["txn_status"] = $response->body->resultInfo->resultStatus; //For Status Code
	    $resp_array["txn_msg"] = $response->body->resultInfo->resultMsg; //For txn Message
	    $resp_array["clnt_txn_ref"] = $response->body->orderId; //For booking id
	    $resp_array["m_refund_id"] = $response->body->refId; //Merchant created ref id
	    $resp_array["pg_txn_id"] = $response->body->txnId; //For pg id
	    $resp_array["pg_refund_id"] = $response->body->refundId; //For pg id
	    
	    if($resp_array["txn_status"] == "SUCCESS")
	    	$resp_array["txn_status"] = "success";

	    $resp_array["attr"] = json_decode(json_encode($response), true);
	    $resp_array["attr"]["pg_name"] = "PAYTM";
	    //debug($resp_array); exit;
	    return $resp_array;
	}

	function read_response($response)
	{
		//debug($response); exit;
	    $response_map["txnid"]=$response["ORDERID"];
	    $response_map["productinfo"]=$GLOBALS["CI"]->session->userdata("paytm_productinfo");
	    if($response["STATUS"]=="TXN_SUCCESS")
	    	$response_map["status"]="success";
	    else
	    	$response_map["status"]="failure";
	    $response_map["pg_txn_id"] = $response["TXNID"];
	    $is_valid_checksum = verifychecksum_e($response, $this->key, $response["CHECKSUMHASH"]);
	    $response_map["is_valid_checksum"]=$is_valid_checksum;
	    $response_map["bank_txn_id"] = $response["BANKTXNID"];
	    $response_map["bank_name"] = $response["BANKNAME"];
	    $response_map["txn_status"] = $response["STATUS"];
	    $response_map["attr"] = $response;
	    $response_map["attr"]["pg_name"] = "PAYTM";
	    //$GLOBALS["CI"]->session->unset_userdata("paytm_productinfo");
	    return $response_map; 
	}
}
