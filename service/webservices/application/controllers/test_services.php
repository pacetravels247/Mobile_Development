<?php if (! defined ( 'BASEPATH' ))	exit ( 'No direct script access allowed' );
class Test_Services extends CI_Controller 
{
	
	function __construct()
	{
		parent::__construct();
	}
	
	public function mystifly()
	{
		$this->load->library('flight/mystifly/mystifly');
		//$this->mystifly->create_session();
		$this->mystifly->get_flight_list();
	}
	
	public function get_json_response($url='http://192.168.0.24/travelomatix_services/webservices/index.php/flight_v10/provab_api/Authenticate', 
	$request=array(), $header_details=''){
		//echo "Url:";debug($url); echo "<br/>Request:";debug($request); echo "<br/>Header:";debug($header_details);exit;
		
		$header=array(
			'Content-Type:application/json',
			'WWW-Authenticate: Basic realm="My Realm"',
			'Accept-Encoding:gzip, deflate',
			'x-Username:Test',
			'x-DomainKey:Test',
			'x-system:Test',
			'x-Password:Test'			 
		);

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_ENCODING, "gzip");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		//curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		//curl_setopt($ch, CURLOPT_USERPWD, "username:password"); //Your credentials goes here
		
		//curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		//curl_setopt($ch, CURLOPT_USERPWD, $header_details['UserName'].':'.$header_details['Password']); //Your credentials goes here
				   
		//curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "Digestusername:password");
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
        //curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        //curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_GSSNEGOTIATE);
         
		
	
		$res = curl_exec($ch);
		echo 'resposnse';
		echo $res;exit;
		$res = json_decode($res, true);
		curl_close($ch);
		return $res;
	}
	function test_digest_auth()
	{
		error_reporting(E_ALL); 
		ini_set( 'display_errors','1');
		$myValue = 'myvalue';
		$url = "http://192.168.0.24/travelomatix_services/webservices/index.php/flight_v10/provab_api/Authenticate";
		$ch = curl_init($url);
		//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
		curl_setopt($ch, CURLOPT_USERPWD,'TestUser:TestPwd');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_NOBODY, false);
		curl_setopt($ch, CURLOPT_URL, $url);
		$result = curl_exec($ch);
	}
}