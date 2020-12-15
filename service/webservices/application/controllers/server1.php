<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Server1 extends CI_Controller {
	
	public static $a;  
		
	public function __construct() {
		parent::__construct();
		$this->a=10;
        error_reporting(0);
	}
	
	public function hello(){
		
		$options = array('location' => 'http://192.168.0.25/proapp_ng/webservice', 
                  'uri' => 'http://localhost/');
		//create an instante of the SOAPClient (the API will be available)
		$api = new SoapClient(NULL, $options);
		//call an API method
		echo $api->__soapCall('balance', array());
		
	}
	
	public function pri(){
		echo $this->a;
	}


}
