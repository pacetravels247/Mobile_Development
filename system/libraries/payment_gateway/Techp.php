<?php if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' );
/**
 *
 * @package Provab
 * @subpackage Techp
 * @author Shashikumar Misal <shashikumar.misal@provabmail.com>
 * @version V1.0.0
 */
date_default_timezone_set('Asia/Calcutta');
require_once 'techp_deps/TransactionRequestBean.php';
require_once 'techp_deps/TransactionResponseBean.php';
class Techp {
	/*
	 * Client Live credentials -_-
	 * ------------------------------
	 * Merchant Code	:	L44492
	 * ------------------------------
	 * Merchant Key		:	4054827749UTVFUA
	 * Merchant Salt	:	1112634858JLKTFH
	 * URL  : https://www.tpsl-india.in/PaymentGateway/TransactionDetailsNew.wsdl
	 * ______________________________
	 */

	/**
	 * Client Test credentials -_-
	 * ------------------------------
	 * Merchant Code			:	T6482
	 * ------------------------------
	 * Merchant Iv 				:	1302233340IDAEVU
	 * Merchant Key 			:	7664678267ERXWIS
	 * URL : https://www.tekprocess.co.in/PaymentGateway/TransactionDetailsNew.wsdl
	 * ______________________________
	 */
	private $iv;
	private $key;
	private $rurl;

	public function __construct() {
		$this->CI = &get_instance ();
		$this->active_payment_system = $this->CI->config->item('active_payment_system');
		$this->iv = '1112634858JLKTFH';
		$this->key = '4054827749UTVFUA';
		$this->rurl = base_url().'index.php/payment_gateway/techprocess_response';

	}

	function initialize($data) 
	{
		$transactionRequestBean = new TransactionRequestBean();

		$transactionRequestBean->setMerchantCode("L44492");
		if ($this->active_payment_system == 'test') {
			//test
			$transactionRequestBean->setKey($this->key);
	    	$transactionRequestBean->setIv($this->iv);
			$url = 'https://www.tekprocess.co.in/PaymentGateway/TransactionDetailsNew.wsdl';
			$transactionRequestBean->setWebServiceLocator($url);
		} else {
			//live
			$transactionRequestBean->setKey($this->key);
	    	$transactionRequestBean->setIv($this->iv);
			$url = 'https://www.tpsl-india.in/PaymentGateway/TransactionDetailsNew.wsdl';
			$transactionRequestBean->setWebServiceLocator($url);
		}
    	//Setting all values here
    	$transactionRequestBean->setMobileNumber($data['phone']);	        
	    $transactionRequestBean->setCustomerName($data['firstname']);
	    $transactionRequestBean->setAmount($data['pgi_amount']); //$data['pgi_amount']
	    $transactionRequestBean->setRequestType($data['req_type']);
	    $transactionRequestBean->setMerchantTxnRefNumber($data['txnid']);
	    $transactionRequestBean->setCurrencyCode('INR');
	    $transactionRequestBean->setShoppingCartDetails("Test_1.0_0.0");
	    $transactionRequestBean->setReturnURL($this->rurl);
	    $transactionRequestBean->setS2SReturnURL("");
	    $transactionRequestBean->setAccountNo('');
	    $transactionRequestBean->setITC($data['productinfo']);
	    $transactionRequestBean->setTxnDate(date("d-m-Y"));
	    $transactionRequestBean->setBankCode("");
	    $transactionRequestBean->setTPSLTxnID($data['pg_txnid']);
	    $transactionRequestBean->setCustId("");
	    $transactionRequestBean->setCardId("");
	    $transactionRequestBean->setMMID("");
	    $transactionRequestBean->setOTP("");
	    $transactionRequestBean->setCardName("");
	    $transactionRequestBean->setCardNo("");
	    $transactionRequestBean->setCardCVV("");
	    $transactionRequestBean->setCardExpMM("");
	    $transactionRequestBean->setCardExpYY("");
	    $transactionRequestBean->setTimeOut("");
		//$this->email = $data['email'];
		return $transactionRequestBean;
	}
	function process_payment($transactionRequestBean){
		$url = $transactionRequestBean->getTransactionToken();
	    $responseDetails = $transactionRequestBean->getTransactionToken();
	    $responseDetails = (array)$responseDetails;
	    $post_data['response'] = $responseDetails[0];
	    //debug($post_data); exit;
		return $post_data;
	}
	function read_response($response, $do="pay")
	{
		if($do=="pay")
	    {
	    if(is_array($response)){
	        $str = $response['msg'];
	    }else if(is_string($response) && strstr($response, 'msg=')){
	        $outputStr = str_replace('msg=', '', $response);
	        $outputArr = explode('&', $outputStr);
	        $str = $outputArr[0];
	    }else {
	        $str = $response;
	    }
	    $transactionResponseBean = new TransactionResponseBean();
	    $transactionResponseBean->setResponsePayload($str);
	    $transactionResponseBean->setKey($this->key);
	    $transactionResponseBean->setIv($this->iv);
	    $response = $transactionResponseBean->getResponsePayload();
	    
	    //Need to parse the decrypted response string to make a readable array
	    $temp_resp_array=explode("|", $response);
	    //debug($response); exit;
		    $resp_array["status_code"]=explode("=", $temp_resp_array[0])[1]; //For Status Code
		    $resp_array["status"]=explode("=", $temp_resp_array[1])[1]; //For Status Message
		    $resp_array["txnid"]=explode("=", $temp_resp_array[3])[1]; //For txnid
		    $resp_array["pg_txn_id"]=explode("=", $temp_resp_array[5])[1]; //For txnid
		    //Same status assigning to one more index just to balance in reporting
		    $resp_array["txn_status"] = $resp_array["status"];
		    $resp_array["bank_txn_id"] = "NA";
		    $resp_array["bank_name"] = "NA";
		    $resp_array["pg_name"] = "TECHP";
		    /*Below snippet is to get other submitted details like Name, Mobile Num, Product Info 
		    etc.*/
		    $client_rq_meta=array();
		    $clt_rq_mt_raw=explode("=", $temp_resp_array[7]);
		    $clt_rq_mt_raw=trim($clt_rq_mt_raw[1], '{}');
		    $clt_rq_mt_raw_array=explode("}{", $clt_rq_mt_raw);
		    foreach($clt_rq_mt_raw_array AS $key=>$value)
		    {
		    	$req_param = explode(':', $value);
		    	$client_rq_meta[$req_param[0]]=$req_param[1];
		    }

		    $resp_array["productinfo"]=$client_rq_meta["itc"];
		}
		if($do=="refund")
		{
			$temp_resp_array=explode("|", $response);
			//debug($temp_resp_array); exit;
			$resp_array["txn_status"]=explode("=", $temp_resp_array[0])[1]; //For Status Code
		    $resp_array["txn_msg"]=explode("=", $temp_resp_array[1])[1]; //For txn Message
		    $resp_array["txn_err_msg"]=explode("=", $temp_resp_array[2])[1]; //For txn err msg
		    $resp_array["clnt_txn_ref"]=explode("=", $temp_resp_array[3])[1]; //For booking id
		    $resp_array["pg_txn_id"]=explode("=", $temp_resp_array[5])[1]; //For booking id
		    $resp_array["pg_name"] = "TECHP";
		}
	    return $resp_array;
	}
}
