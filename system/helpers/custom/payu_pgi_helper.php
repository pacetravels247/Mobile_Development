<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 
 * @param unknown $params
 * @param unknown $salt
 * @throws Exception
 */
function pay_page ( $params, $salt )
{
	if ( count( $_POST ) && isset( $_POST['mihpayid'] ) && ! empty( $_POST['mihpayid'] ) ) {
		$_POST['surl'] = $params['surl'];
		$_POST['furl'] = $params['furl'];

		$result = response( $_POST, $salt );
		Misc::show_reponse( $result );
	} else {
		//$host = (isset( $_SERVER['https'] ) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$surl = base_url().'index.php/transaction/success';
		$furl = base_url(). 'index.php/transaction/cancel';
		if ( isset( $_SERVER['REQUEST_URI'] ) && ! empty( $_SERVER['REQUEST_URI'] ) ) $params['surl'] = $surl;
		if ( isset( $_SERVER['REQUEST_URI'] ) && ! empty( $_SERVER['REQUEST_URI'] ) ) $params['furl'] = $furl;

		$result = pay( $params, $salt );
		//debug($result);exit;
		Misc::show_page( $result );
	}
}
function initiate_payu($post_data){
	$GLOBALS['CI']->load->view('payment/pay',$post_data);
}

/**
 * Returns the response object.
 *
 * @param unknown $params
 * @param unknown $salt
 * @throws Exception
 * @return number
 */
function response ( $params, $salt )
{
	if ( ! is_array( $params ) ) throw new Exception( 'PayU response params is empty' );

	if ( empty( $salt ) ) throw new Exception( 'Salt is empty' );

	if ( empty( $params['status'] ) ) throw new Exception( 'Status is empty' );

	$response = new Response( $salt );
	$result = $response->get_response( $_POST );
	unset( $response );

	return $result;
}
class Curl {
	private $url = "";
	private $user_agent = "libCurl";
	private $return_result = false;
	private $referrer = false;
	private $cookies_on = false;
	private $proxy = array ();
	private $timeout = 30;
	private $cookies;
	private $headers;
	private $method = "POST";
	private $httpHeader = "multipart/form-data";

	public $error = 0;
	public $info = array ();
	function __construct($url = false) {
		$this->cookies = new Cookies ();
		$this->url = $url;
		$this->info ['total_time'] = time ();
	}
	function __destruct() {
	}
	private function getHost($url) {
		$url = str_replace ( array (
				"http://",
				"https://"
		), "", $url );
		$tmp = explode ( "/", $url );
		return $tmp [0];
	}
	private function getQuery($url) {
		$url = str_replace ( array (
				"http://",
				"https://"
		), "", $url );
		$tmp = explode ( "/", $url, 2 );
		return "/" . $tmp [1];
	}
	private function _parseRawData($rawData) {
		$array = explode ( "\r\n\r\n", $rawData, 2 );
		$this->header_data = $array [0];
		$this->content = $array [1];
		$this->_parseHeaders ( $array [0] );
	}
	private function _parseHeaders($rawHeaders) {
		$rawHeaders = trim ( $rawHeaders );
		$headers = explode ( "\r\n", $rawHeaders );

		foreach ( $headers as $header ) {
			if (preg_match ( "|http/1\.. (\d+)|i", $header, $match )) {
				$this->status_code = $match [1];
				continue;
			}
				
			$headerArray = explode ( ":", $header );
			$headerName = trim ( $headerArray [0] );
			$headerValue = trim ( $headerArray [1] );
				
			if (preg_match ( "|set-cookie2?|i", $headerName ))
				$this->cookies->add ( $headerValue );
			if (isset ( $headerName ))
				$this->headers [strtolower ( $headerName )] = $headerValue;
		}

		if (isset ( $this->headers ["location"] )) {
			$this->url = $this->headers ["location"];
			$this->exec ();
		}
	}
	public function setopt($name, $value = false) {
		switch ($name) {
			case CURLOPT_URL :
				$this->url = $value;
				$this->proxy ["port"] = substr ( $this->url, 0, 5 ) === 'https' ? 443 : 80;
				break;
			case CURLOPT_USERAGENT :
				$this->user_agent = $value;
				break;
			case CURLOPT_POST :
				$this->method = ($value == true) ? "POST" : "GET";
				break;
			case CURLOPT_POSTFIELDS :
				$this->post_data = $value;
				break;
			case CURLOPT_RETURNTRANSFER :
				$this->return_result = ($value == true);
				break;
			case CURLOPT_REFERER :
				$this->referrer = $value;
				break;
			case CURLOPT_HEADER :
				$this->options ["header"] = ($value == true);
				break;
			case CURLOPT_PROXY :
				list ( $this->proxy ["host"], $this->proxy ["port"] ) = explode ( ":", $value );
				break;
			case CURLOPT_CONNECTTIMEOUT : /* Fall through. */
			case CURLOPT_TIMEOUT :
				$this->timeout = ($value >= 0) ? $value : 30;
				break;
			case CURLOPT_PORT :
				$this->proxy ["port"] = $value ? $value : (substr ( $this->url, 0, 5 ) === 'https' ? 443 : 80);
				break;
			case CURLOPT_HTTPHEADER :
				$this->httpHeader = substr ( implode ( ";", $value ), 0, - 1 );
				break;
		}
	}
	public function setoptArray($options) {
		foreach ( $options as $name => $value )
			$this->setopt ( $name, $value );
	}
	public function exec() {
		$errno = false;
		$errstr = false;
		$url = $this->url;

		$host = $this->getHost ( $url );
		$query = $this->getQuery ( $url );

		$this->proxy ["host"] = $host;

		if (isset ( $this->proxy ["port"] )) {
			$this->proxy ["host"] = (443 === $this->proxy ["port"]) ? "ssl://$host" : $host;
			$fp = pfsockopen ( $this->proxy ["host"], $this->proxy ["port"], $errno, $errstr, $this->timeout );
			$request = $query;
		} else {
			$fp = pfsockopen ( $host, 80, $errno, $errstr, $this->timeout );
			$request = $query;
		}

		if (! $fp) { /* trigger_error($errstr, E_WARNING); */
			$this->error = 1;
			return;
		}

		$headers = $this->method . " $request HTTP/1.0 \r\nHost: $host \r\n";
		if ($this->user_agent)
			$headers .= "User-Agent: " . $this->user_agent . "\r\n";
		if ($this->referrer)
			$headers .= "Referrer: " . $this->referrer . "\r\n";
		if ($this->method == "POST") {
			$headers .= "Content-Type: " . $this->httpHeader . "\r\n";
			$headers .= "Content-Length: " . strlen ( $this->post_data ) . "\r\n";
		}

		if ($this->cookies_on)
			$headers .= $this->cookies->createHeader ();
		$headers .= "Connection: Close\r\n\r\n";
		if ("POST" == $this->method)
			$headers .= $this->post_data;
		$headers .= "\r\n\r\n";

		fwrite ( $fp, $headers );
		$rawData = "";
		while ( ! feof ( $fp ) )
			$rawData .= fread ( $fp, 512 );
		/* fclose($fp); /* Too lazy to read the docs. */
		$this->info ['total_time'] = time () - $this->info ['total_time'];

		$this->_parseRawData ( $rawData );
		if ($this->options ["header"])
			$this->content = $rawData;
		if ($this->return_result)
			return $this->content;
		echo $this->content;
	}
}
class Cookies {
	private $cookies;
	function __construct() {
	}
	function __destruct() {
	}
	public function add($cookie) {
		list ( $data, $etc ) = explode ( ";", $cookie, 2 );
		list ( $name, $value ) = explode ( "=", $data );
		$this->cookies [trim ( $name )] = trim ( $value );
	}
	public function createHeader() {
		if (0 == count ( $this->cookies ) || ! is_array ( $this->cookies ))
			return "";
		$output = "";
		foreach ( $this->cookies as $name => $value )
			$output .= "$name=$value; ";
		return "Cookies: $output\r\n";
	}
}
class Misc {
	const SUCCESS = 1;
	const FAILURE = 0;
	public static function get_hash($params, $salt) {
		$posted = array ();

		if (! empty ( $params ))
			foreach ( $params as $key => $value )
				$posted [$key] = htmlentities ( $value, ENT_QUOTES );

			$hash_sequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";

			$hash_vars_seq = explode ( '|', $hash_sequence );
			$hash_string = null;

			foreach ( $hash_vars_seq as $hash_var ) {
				$hash_string .= isset ( $posted [$hash_var] ) ? $posted [$hash_var] : '';
				$hash_string .= '|';
			}

			$hash_string .= $salt;
			return strtolower ( hash ( 'sha512', $hash_string ) );
	}
	public static function reverse_hash($params, $salt, $status) {
		$posted = array ();
		$hash_string = null;

		if (! empty ( $params ))
			foreach ( $params as $key => $value )
				$posted [$key] = htmlentities ( $value, ENT_QUOTES );

			$additional_hash_sequence = 'base_merchantid|base_payuid|miles|additional_charges';
			$hash_vars_seq = explode ( '|', $additional_hash_sequence );

			foreach ( $hash_vars_seq as $hash_var )
				$hash_string .= isset ( $posted [$hash_var] ) ? $posted [$hash_var] . '|' : '';

			$hash_sequence = "udf10|udf9|udf8|udf7|udf6|udf5|udf4|udf3|udf2|udf1|email|firstname|productinfo|amount|txnid|key";
			$hash_vars_seq = explode ( '|', $hash_sequence );
			$hash_string .= $salt . '|' . $status;

			foreach ( $hash_vars_seq as $hash_var ) {
				$hash_string .= '|';
				$hash_string .= isset ( $posted [$hash_var] ) ? $posted [$hash_var] : '';
			}

			return strtolower ( hash ( 'sha512', $hash_string ) );
	}
	public static function curl_call($url, $data) {
		$ch = curl_init ();

		curl_setopt_array ( $ch, array (
		CURLOPT_URL => $url,
		CURLOPT_POSTFIELDS => $data,
		CURLOPT_POST => true,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_USERAGENT => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36',
		CURLOPT_SSL_VERIFYHOST => 0,
		CURLOPT_SSL_VERIFYPEER => 0
		) );

		$o = curl_exec ( $ch );

		if (curl_errno ( $ch )) {
			$c_error = curl_error ( $ch );
				
			if (empty ( $c_error ))
				$c_error = 'Server Error';
				
			return array (
					'curl_status' => Misc::FAILURE,
					'error' => $c_error
			);
		}

		$o = trim ( $o );
		return array (
				'curl_status' => Misc::SUCCESS,
				'result' => $o
		);
	}
	public static function show_page($result) {
		if ($result ['status'] === Misc::SUCCESS){
			
			header('Location:'.$result ['data']);
		} else {
			throw new Exception ( $result ['data'] );
		}
		
		exit;
	}
	public static function show_reponse($result) {
		if ($result ['status'] === Misc::SUCCESS)
			$result ['data'] ();
		else
			return $result ['data'];
	}
}
class Payment {
	private $url;
	private $salt;
	private $params = array ();
	public function __construct($salt, $env = 'test') {
		$this->salt = $salt;

		switch ($env) {
			case 'test' :
				$this->url = 'https://test.payu.in/';
				break;
			case 'prod' :
				$this->url = 'https://secure.payu.in/';
				break;
			default :
				$this->url = 'https://test.payu.in/';
		}
	}
	public function __destruct() {
		unset ( $this->url );
		unset ( $this->salt );
		unset ( $this->params );
	}
	public function __set($key, $value) {
		$this->params [$key] = $value;
	}
	public function __get($key) {
		return $this->params [$key];
	}
	public function pay($params = null) {
		if (is_array ( $params ))
			foreach ( $params as $key => $value )
				$this->params [$key] = $value;

			$error = $this->check_params ();

			if ($error === true) {
				$this->params ['hash'] = Misc::get_hash ( $this->params, $this->salt );
				$result = Misc::curl_call ( $this->url . '_payment?type=merchant_txn', http_build_query ( $this->params ) );
				$transaction_id = ($result ['curl_status'] === Misc::SUCCESS) ? $result ['result'] : null;
					
				if (empty ( $transaction_id ))
					return array (
							'status' => Misc::FAILURE,
							'data' => $result ['error']
					);
						
					return array (
							'status' => Misc::SUCCESS,
							'data' => $this->url . '_payment_options?mihpayid=' . $transaction_id
					);
			} else {
				return array (
						'status' => Misc::FAILURE,
						'data' => $error
				);
			}
	}
	private function check_params() {
		if (empty ( $this->params ['key'] ))
			return $this->error ( 'key' );
		if (empty ( $this->params ['txnid'] ))
			return $this->error ( 'txnid' );
		if (empty ( $this->params ['amount'] ))
			return $this->error ( 'amount' );
		if (empty ( $this->params ['firstname'] ))
			return $this->error ( 'firstname' );
		if (empty ( $this->params ['email'] ))
			return $this->error ( 'email' );
		if (empty ( $this->params ['phone'] ))
			return $this->error ( 'phone' );
		if (empty ( $this->params ['productinfo'] ))
			return $this->error ( 'productinfo' );
		if (empty ( $this->params ['surl'] ))
			return $this->error ( 'surl' );
		if (empty ( $this->params ['furl'] ))
			return $this->error ( 'furl' );

		return true;
	}
	private function error($key) {
		return 'Mandatory parameter ' . $key . ' is empty';
	}
}
class Response {
	private $salt;
	private $params = array ();
	public function __construct($salt) {
		$this->salt = $salt;
	}
	public function __destruct() {
		unset ( $this->salt );
		unset ( $this->params );
	}
	public function __set($key, $value) {
		$this->params [$key] = $value;
	}
	public function __get($key) {
		return $this->params [$key];
	}
	public function get_response($params = null) {
		$this->params = (is_array ( $params ) && count ( $params )) ? $params : $_POST;

		$error = $this->check_params ();

		if ($error === true) {
			if (Misc::reverse_hash ( $this->params, $this->salt, $this->params ['status'] ) === $this->params ['hash']) {
				switch ($this->params ['status']) {
					case 'success' :
						return array (
						'status' => Misc::SUCCESS,
						'data' => $this->params ['surl']
						);
						break;
					case 'failure' :
						return array (
						'status' => Misc::SUCCESS,
						'data' => $this->params ['furl']
						);
						break;
					default :
						return array (
						'status' => Misc::FAILURE,
						'data' => 'Unmapped status'
								);
				}
			} else {
				return array (
						'status' => Misc::FAILURE,
						'data' => 'Hash Mismatch'
				);
			}
		} else {
			return array (
					'status' => Misc::FAILURE,
					'data' => $error
			);
		}
	}
	private function check_params() {
		if (empty ( $this->params ['key'] ))
			return $this->error ( 'key' );
		if (empty ( $this->params ['txnid'] ))
			return $this->error ( 'txnid' );
		if (empty ( $this->params ['amount'] ))
			return $this->error ( 'amount' );
		if (empty ( $this->params ['firstname'] ))
			return $this->error ( 'firstname' );
		if (empty ( $this->params ['email'] ))
			return $this->error ( 'email' );
		if (empty ( $this->params ['phone'] ))
			return $this->error ( 'phone' );
		if (empty ( $this->params ['productinfo'] ))
			return $this->error ( 'productinfo' );
		if (empty ( $this->params ['surl'] ))
			return $this->error ( 'surl' );
		if (empty ( $this->params ['furl'] ))
			return $this->error ( 'furl' );

		return true;
	}
	private function error($key) {
		return 'Mandatory parameter ' . $key . ' is empty';
	}
}