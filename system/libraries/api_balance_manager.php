<?php
ini_set('memory_limit', '-1');

class Api_balance_manager {
	public function __construct() {
		$this->CI = &get_instance();
		$this->CI->load->library("Api_Interface");
    }
    private function set_tbo_api_credentials() {
		//$this->CI->load->database("services", "service_db");
        $flight_engine_system = $this->CI->flight_engine_system;
        $this->system = $flight_engine_system;
		//$credentials = $this->CI->custom_db->single_table_records("api_config", "config", array("pace_bs_id" => PROVAB_FLIGHT_BOOKING_SOURCE, "system"=>$this->system));


        $credentials='{"ClientId":"ApiIntegrationNew",
"UserName":"pacetravels","Password":"pace@1234",

"AuthenticationUrl":"http://api.tektravels.com/SharedServices/SharedData.svc/rest/",

"EndPointUrl":"http://api.tektravels.com/BookingEngineService_Air/AirService.svc/rest/"}';
$this->details = json_decode($credentials, true);

		//$this->details = json_decode($credentials["data"][0]["config"], true);
		//debug($this->details); exit;
        $this->Url = $this->details['AuthenticationUrl'];
        $this->client_id = $this->details['ClientId'];
        $this->username = $this->details['UserName'];
        $this->password = $this->details['Password'];
        $this->token_agency_id = $this->details['TokenAgencyId'];
        $this->token_member_id = $this->details['TokenMemberId'];
        $this->end_user_ip = "54.151.201.32";
    }
    private function set_ets_api_credentials() {
        $bus_engine_system = $this->CI->bus_engine_system;
        $this->system = $bus_engine_system;
		//exit("HI");
        $credentials = $this->CI->custom_db->single_table_records("supplier_credentials", "config", array("booking_source" => ETS_BUS_BOOKING_SOURCE, "mode"=>$bus_engine_system));
        $this->details = json_decode($credentials["data"][0]["config"], true);
        $this->Url = $this->details ['api_url'];
        $this->UserName = $this->details [$this->system.'_username'];
        $this->Password = $this->details [$this->system.'_password'];
    }
    private function set_bitla_api_credentials() {
        $bus_engine_system = $this->CI->bus_engine_system;
        $this->system = $bus_engine_system;
        $credentials = $this->CI->custom_db->single_table_records("supplier_credentials", "config", array("booking_source" => BITLA_BUS_BOOKING_SOURCE, "mode"=>$bus_engine_system));
		$this->details = json_decode($credentials["data"][0]["config"], true);
		//debug($this->details); exit;
        $this->Url = $this->details ['api_url'];
        $this->UserName = $this->details [$this->system.'_username'];
        $this->Password = $this->details [$this->system.'_password'];
        $this->Api_key = $this->details ['api_key'];
    }
    public function getBitlaBalance()
    {
    	$this->set_bitla_api_credentials();
    	$api_key = $this->Api_key;
    	$url = $this->Url.'gds/api/get_balance.json';
        $request['status'] = SUCCESS_STATUS;
        $request['request_url'] = $url;
        $request['api_key'] = $api_key;
		$response = $this->CI->api_interface->get_json_response_bitla($request['request_url'], $request['api_key']);
        //debug($response); exit; 
		return $response;
    }
	
	//Bitla Direct Operator Balance functions #Start
	public function getBitlaDOBalance($travel_id)
    {
    	$this->set_bitla_api_credentials();
    	$api_key = $this->Api_key;
    	$url = $this->Url.'gds/api/get_balance.json';
        $request['status'] = SUCCESS_STATUS;
        $request['request_url'] = $url;
        $request['api_key'] = $api_key;
		$request_data = array(
			'travel_id' => $travel_id,
		);
		$response = $this->CI->api_interface->get_json_response_bitla($request['request_url'], $request['api_key'], $request_data, 1);
        return $response;
    }
	//Bitla Direct Operator Balance functions #End
	
    public function getEtsBalance()
    {
        $this->set_ets_api_credentials();
    	$url = $this->Url.'getMyPlanAndBalance';
        $request['status'] = SUCCESS_STATUS;
        $request['service_url'] = $url;
        $request['username'] = $this->UserName;
        $request['password'] = $this->Password;
        $response = $this->CI->api_interface->get_json_response_ets($request['service_url'],$request['username'],$request['password']);
        //debug($response);
        return $response;
    }
    function tbo_authenticate_request() {
        $request = array();
        $AuthenticationRequest = array();
        $AuthenticationRequest['ClientId'] = $this->client_id;
        $AuthenticationRequest['UserName'] = $this->username;
        $AuthenticationRequest['Password'] = $this->password;
        $AuthenticationRequest['EndUserIp'] = $this->end_user_ip;
        $request ['request'] = json_encode($AuthenticationRequest);
        $request ['url'] = $this->Url.'Authenticate';
        $request ['status'] = SUCCESS_STATUS;
        return $request;
    }
    public function getTboBalance()
    {
    	$this->set_tbo_api_credentials();
        $auth_req = $this->tbo_authenticate_request();
        $auth_resp = $this->CI->api_interface->process_tbo_request($auth_req["request"], $auth_req["url"]);
        $auth_resp = json_decode($auth_resp);
    	$url = $this->Url.'GetAgencyBalance';
        $request['status'] = SUCCESS_STATUS;
        $request['request_url'] = $url;
        $bal_request['ClientId'] = $this->client_id;
        $bal_request['EndUserIp'] = $this->end_user_ip;
        $bal_request['TokenAgencyId'] = $this->token_agency_id;
        $bal_request['TokenMemberId'] = $this->token_member_id;
        $bal_request['TokenId'] = $auth_resp->TokenId;
        $request["request"] = json_encode($bal_request);
		$response = $this->CI->api_interface->process_tbo_request($request['request'], $request['request_url']);
        //debug($response); exit; 
		return json_decode($response, true);
    }
    public function getRezliveBalance()
    {
        $this->set_rezlive_api_credentials();
        $url = $this->Url.'​rest/GetAgencyBalance';
        $request['status'] = SUCCESS_STATUS;
        $request['request_url'] = $url;
        $request['client_id'] = $client_id;
        $response = $this->CI->api_interface->get_json_response_bitla($request['request_url'], $request['client_id']);
    }
    function update_api_balance($source_id, $amount)
    {
        $current_balance = 0;
        $cond = array('source_id' => $source_id);
        $details = $this->CI->custom_db->single_table_records('booking_source', 'balance, minimum_balance, credit_limit, due_amount', $cond);
        if ($details['status'] == true) {
            if($amount > 0 && $details ['data'] [0] ['due_amount'] < 0)
            {
                if(abs($details ['data'] [0] ['due_amount']) >= $amount)
                {
                    $details ['data'] [0] ['due_amount'] += $amount;
                    $amount = 0;
                }
                if(abs($details ['data'] [0] ['due_amount']) < $amount)
                {
                    $amount += $details ['data'] [0] ['due_amount'];
                    $details ['data'] [0] ['due_amount'] = 0;
                }
            }
            $details ['data'] [0] ['balance'] = $current_balance = ($details ['data'] [0] ['balance'] + $amount);
                if ($details ['data'] [0] ['balance'] < 0) {
                    $details ['data'] [0] ['due_amount'] += $details ['data'] [0] ['balance'];
                    $details ['data'] [0] ['balance'] = 0;
                }
                // debug($details);exit;
            // $details['data'][0]['balance'] = $current_balance = ($details['data'][0]['balance'] + $amount);
            $details ['data'][0]['updated_datetime'] = date('Y-m-d H:i:s');
            $this->CI->custom_db->update_record('booking_source', $details['data'][0], $cond);
        }
    }
}
?>