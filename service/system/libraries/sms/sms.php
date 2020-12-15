<?php
require_once BASEPATH . 'libraries/Master_Api_Config.php';
class SMS extends Master_Api_Config {
	protected $source_code = V3SMS_BOOKING_SOURCE;
	protected $country_code = '+91';
	function __construct() {
		parent::__construct ( META_SMS_GATEWAY, $this->source_code );
	}
	
	private function openurl($url) {
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		$response = trim ( curl_exec ( $ch ) );
		curl_close ( $ch );
		return $response;
	}
	
	private function insert_sms_data($data) {
		$CI = &get_instance ();
		
		$sms_data ['message'] = urldecode ( $data ['SMSText'] );
		$sms_data ['from'] = $data ['sender'];
		$sms_data ['to'] = $data ['GSM'];
		$sms_data ['status'] = $data ['status'];
		$sms_data ['response'] = $data ['response'];
		$sms_data ['reference'] = $data ['reference'];
		$sms_data ['created_by_id'] = intval(@$CI->entity_user_id);
		$sms_data ['created_datetime'] = date ( 'Y-m-d H:i:s' );
		$CI->db -> insert ( 'sms_details', $sms_data );
	}
	
	function send_sms($mobile, $msg = '', $ref = '') {
		$data ['user'] = $this->config['user'];
		$data ['password'] = $this->config['password'];
		$data ['sender'] = $this->config['sender'];
		$data ['type'] = $this->config['type'];
		
		$data ['SMSText'] = ( $msg );
		$data ['GSM'] = $this->country_code.$mobile;
		$sms_url = $this->config['api_url'].'?' . http_build_query ( $data );
		//echo $sms_url;exit;
		$data ['response'] = $this->openurl ( $sms_url );
	
		$data ['status'] = 0;
		$data ['reference'] = $ref;
		$this->insert_sms_data ( $data );
		
		return $data ['status'];
	}
         function send_sms_users($get_data) {

        $CI = &get_instance();
        $data ['user'] = $this->config['user'];
        $data ['password'] = $this->config['password'];
        if($get_data['sender']!="")
        {
             $data ['sender'] = $get_data['sender'];
        } else {
           $data ['sender'] = $this->config['sender'];
        }
        
        $data ['type'] = $this->config['type'];

        $data ['SMSText'] =$get_data['text'];
        $data ['GSM'] = $this->country_code . $get_data['mobile'];
        $sms_url = $this->config['api_url'] . '?' . http_build_query($data);

        $data ['response'] = $this->openurl ( $sms_url );

       /* $response = '<?xml version="1.0" encoding="UTF-8"?>
<results>
<result><status>0</status><messageid>218102613192841713</messageid><destination>+918123573796</destination></result>
</results>';*/
        
        
$response_xml = simplexml_load_string($data ['response']);
$__response = json_decode(json_encode($response_xml), 1);



 $save_data=array(); 
 $save_data['user_origin']=$get_data['domain_origin'];
 $save_data['user_request']=json_encode($get_data);
 $save_data['api_request']=$sms_url;
 $save_data['api_response']=json_encode($__response);
    if($__response['result']['status']==0)
    {
      $save_data['status']="Success";
    }
    else 
    {
      $save_data['status']="Failed";
    }
 $save_data['created_datetime']=date('Y-m-d H:i:s');
 
 $CI->db->insert('user_sms_tracker', $save_data);
       
        return $__response;
    }

}