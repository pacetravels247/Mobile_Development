<?php if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' );
/**
 *
 * @package Provab
 * @subpackage Payu
 * @author Pravinkumar P <pravinkumar.provab@gmail.com>
 * @version V1
 */
class Paypal {

	// function test(){
	// 	echo "paypal here !";
	// 	die();
	// }
	private $merchant_id;
	private $client_ID;
	private $secret;
	private $url;
	private $merchant_email;

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
		$this->active_payment_system = $this->CI->config->item('active_payment_system');
		
		$this->merchant_id='B96R8SJ7VZCHG';
		if ($this->active_payment_system == 'test') {
			//test
			$this->client_ID = 'ATDuwsaIXHBRLIe4ECk_IbWfSDnROLM_p0xMoLairB3IQRNeFBg6G8c_ypu17arz1gwquQ-DxTA24hWV';
			$this->secret = 'EE-6-DnjIPOweLRHGQvz9r4CfqWDP48odgk0TuFhTqONw0BcDEstOUOoAAQcvrdeMSRJ9QMrrmltheb_';
			$this->merchant_email='cmd-facilitator@pacetravels.in';
			$this->url = 'https://api.sandbox.paypal.com/';
		} else {
			//live
			
		}
	}

	function initialize($data)
	{
		$this->book_id = $data['txnid'];
		$this->pgi_amount = $data['pgi_amount'];
		//$this->pgi_amount = '1';
		$this->firstname = $data['firstname'];
		$this->email = $data['email'];
		$this->phone = $data['phone'];
		$this->productinfo = $data['productinfo'];
		//debug($data);die('123');
	}

	function authorization(){

		$header = array(
            'Accept: application/json',
            'Accept-Language: en_US',
        );
		$request = "grant_type=client_credentials";
        $url= $this->url.'v1/oauth2/token';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_USERPWD,$this->client_ID .":". $this->secret);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $res = curl_exec($ch);
        $res = json_decode($res, true);
        curl_close($ch);
        return $res;
	}

	function process_payment(){


		$auth_data=$this->authorization();
		//debug($auth_data);die('here');

		if(isset($auth_data['access_token'])){

			$surl = base_url().'index.php/payment_gateway/paypal_response';
			$curl = base_url(). 'index.php/payment_gateway/paypal_response/'.$this->book_id.'/'.$this->productinfo.'/cancelled';
			
			$post_data=array();
			$post_data['paypal_username'] = $this->merchant_email;
			$post_data['tran_id'] = $this->book_id;
			$post_data['total_amount'] =  $this->pgi_amount;
			$post_data['cus_name'] = $this->firstname;
			$post_data['cus_email'] = $this->email;
			$post_data['cus_phone'] = $this->phone;
			$post_data['productinfo'] = $this->productinfo;
			$post_data['currency_code'] = "INR";

			$post_data['surl'] = $surl;
			$post_data['curl'] = $curl;

			//Sandbox
			$post_data['process_url'] = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
			//Live
			//$post_data['process_url'] = 'https://www.paypal.com/cgi-bin/webscr';



			return $post_data;

		}else{
			echo "Some error occured..!";
		}
	}

	function verifyTransaction($validate_data){
		//debug($validate_data);die('lib');

		$paypalUrl ='https://www.sandbox.paypal.com/cgi-bin/webscr';

	    $req = 'cmd=_notify-validate';
	    foreach ($validate_data as $key => $value) {
	        $value = urlencode(stripslashes($value));
	        $value = preg_replace('/(.*[^%^0^D])(%0A)(.*)/i', '${1}%0D%0A${3}', $value); // IPN fix
	        $req .= "&$key=$value";
	    }

	    $ch = curl_init($paypalUrl);
	    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
	    curl_setopt($ch, CURLOPT_SSLVERSION, 6);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	    curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
	    $res = curl_exec($ch);

	    if (!$res) {
	        $errno = curl_errno($ch);
	        $errstr = curl_error($ch);
	        curl_close($ch);
	        throw new Exception("cURL error: [$errno] $errstr");
    	}
    	$info = curl_getinfo($ch);

	    // Check the http response
	    $httpCode = $info['http_code'];
	    if ($httpCode != 200) {
	        throw new Exception("PayPal responded with http code $httpCode");
	    }

    	curl_close($ch);

    	return $res;
	}

	function read_response($response)
	{
		$response_map["txnid"]=$response["item_number"];
		$response_map["productinfo"]=$response["item_name"];

		$validation = $this->verifyTransaction($response);
		if($validation!="VERIFIED")
		{
			$response_map["status"]="cancelled";
			return $response_map;
		}

		if($response["payment_status"] == 'Completed')
		{
			$response_map["status"]="success";
			return $response_map;
		}
		else{
			$response_map["status"]="failure";
			return $response_map;
		}
	}
}