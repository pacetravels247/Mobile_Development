<?php 
	
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// ------------------------------------------------------------------------
/**
 * Controller for all ajax activities
 *
 * @package    Provab
 * @subpackage ajax loaders
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V1
 */
// ------------------------------------------------------------------------

class Static_data extends CI_Controller {

	private $current_module;
    public function __construct() {
        parent::__construct();
        if (is_ajax() == false) {
            //$this->index();
        }
        ob_start();
       
        $this->current_module = $this->config->item('current_module');
    }
    public function get_viator_destination_id(){
    	$domain_key = 897934934531871;
    	$url = "http://prelive.viatorapi.viator.com/service/taxonomy/destinations?apiKey=897934934531871";
    	$response = $this->api_json_response($url,array(),$domain_key);
    	debug($response);
    	exit;
    	foreach($response['data'] as $key=>$value){
    		$insert_arr = array();
    		$insert_arr['destination_name']=$value['destinationName'];
    		$insert_arr['destination_id'] =$value['destinationId'];
    		$insert_arr['destination_type']=$value['destinationType'];
    		$insert_arr['timeZone']=$value['timeZone'];
    		$insert_arr['iataCode']=$value['iataCode'];
    		$insert_arr['lat']=$value['latitude'];
    		$insert_arr['lng']=$value['longitude'];
    		$this->custom_db->insert_record('api_sightseeing_destination_list',$insert_arr);

    	}
    	echo "success";
    	exit;

    }
    public function api_json_response($url,$json_data=array(),$domain_key,$method=''){
		$header=array(
			'api-key:'.$domain_key,			      
			'Content-Type:application/json',
			'Accept:application/json'			
		);		
		//echo $header_details['DomainKey'];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
		if($method == 'post'){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$json_data);
		}elseif($method =="delete"){
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
			curl_setopt($ch, CURLOPT_POSTFIELDS,$json_data);
		}   
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response  = curl_exec($ch);
		$headers = curl_getinfo($ch); 
		//echo $response;exit;
		//debug($headers);exit;
		if ($headers['http_code'] != '200') {
			// echo "<pre>";
			// print_r(curl_error($ch));
			exit;
			return false;
		} else {
		 $response = json_decode($response, true);
		//echo "<pre/>";
		 //print_r($response);exit;
		 return $response;

		}
		curl_close($ch);
	}

}

?>