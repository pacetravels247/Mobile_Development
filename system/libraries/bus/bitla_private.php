<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once BASEPATH . 'libraries/Common_Api_Grind.php';

class Bitla_private extends Common_Api_Grind {

    private $EndUserIp = '127.0.0.1';
    private $TokenId; //	Token ID that needs to be echoed back in every subsequent request
    private $master_search_data;
    private $commission = array();
    private $ClientId;
    private $UserName;
    private $Password;
    private $system;
    private $Url;
    private $net_fare_tags = array('Fare', 'SeaterFareNAC', 'SeaterFareAC', 'SleeperFareNAC', 'SleeperFareAC');
    public $search_hash;
	public $opts_cache;

    public function __construct() {
        $this->CI = &get_instance();
        $GLOBALS['CI']->load->library('Api_Interface');
        $GLOBALS['CI']->load->library('Converter');
        $GLOBALS['CI']->load->model('bus_model');
        $this->set_api_credentials();
		$this->get_all_operators();
    }

     private function set_api_credentials() {
        $bus_engine_system = $this->CI->bus_engine_system;
        $this->system = $bus_engine_system;
        $credentials = $this->CI->custom_db->single_table_records("supplier_credentials", "config", array("booking_source" => BITLA_BUS_BOOKING_SOURCE, "mode"=>$bus_engine_system));
        $this->details = json_decode($credentials["data"][0]["config"], true);
        //$this->details = $this->CI->config->item ('bitla_bus_'.$bus_engine_system);
        $this->Url = $this->details ['api_url'];
        $this->UserName = $this->details [$this->system.'_username'];
        $this->Password = $this->details [$this->system.'_password'];
        $this->Api_key = $this->details ['api_key'];
		//debug($this); exit;
    }

    /**
     * check if auth data is refreshed or not
     */
    function is_valid_token() {
        //validate based on created datetime
        //FIXME
        return true;
    }

    /**
     * TBO auth token will be returned
     */
    public function get_authenticate_token() {
        return $GLOBALS['CI']->session->userdata('tb_auth_token');
    }

    /**
     *  Balu A
     *
     * get bus search request details
     * @param array $search_params data to be used while searching of busses
     */
    private function bus_search_request($bus_station_from_id, $bus_station_to_id, $bus_date, $number_of_seats = 1, $SearchId = 123456, $srs) {
        
        //134,506 id for test
        $bus_station_from_id = $bus_station_from_id;
        $bus_station_to_id = $bus_station_to_id;
        $bus_date = $bus_date;
        $api_key = $this->Api_key;

        if($srs)
			$api_key = "TSKHLRAPI43124815";
        
        $url = $this->Url.'gds/api/schedules/'.$bus_station_from_id.'/'.$bus_station_to_id .'/'.$bus_date.'.json';	
        $response['status'] = SUCCESS_STATUS;
        $response['request_url'] = $url;
        $response['api_key'] = $api_key;
		//debug($response); 
        return $response;
    }

    /**
     *
     */
    function GetRouteScheduleDetailsWithComm_request($route_schedule_id, $journey_date, $route_code, $ResultToken, $booking_source) {
        $response['status'] = SUCCESS_STATUS;
        $response = array();
        $api_key = $this->Api_key;
        $url = $this->Url .'gds/api/schedule/'.$route_schedule_id.'.json';
        //$request_paramas['RouteScheduleId'] = $route_schedule_id;
        
        $response['status'] = SUCCESS_STATUS;
        $response['request_url'] = $url;
        $response['api_key'] = $api_key;

        return $response;
    }

    /**
     *
     */
    function GetRouteInfo_request($route_schedule_id, $journey_date) {
        $response['status'] = SUCCESS_STATUS;
        $response['data'] = array();
        /** Request to be formed for search * */
        //$url = 'http://affapi.mantistechnologies.com/service.asmx?op=GetRouteInfo';
        //$url = $this->Url . 'GetRouteInfo';
        $url = $this->Url . 'GetRouteInfo';
        /* $request = '
          <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
          <soap:Body>
          <GetRouteInfo xmlns="http://tempuri.org/">
          <Authentication>
          <UserID>'.$this->TokenId['UserID'].'</UserID>
          <UserType>'.$this->TokenId['UserType'].'</UserType>
          <Key>'. $this->TokenId['Key'].'</Key>
          </Authentication>
          <intRouteScheduleID>'.$route_schedule_id.'</intRouteScheduleID>
          <dtJourneyDate>'.date('Y-m-d', strtotime($journey_date)).'</dtJourneyDate>
          </GetRouteInfo>
          </soap:Body>
          </soap:Envelope>';
         */
        $request = '
					<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
						<soap:Body>
							<GetRouteInfo xmlns="http://tempuri.org/">
								<Authentication>
									<UserName>' . $this->UserName . '</UserName>
									<PassWord>' . $this->Password . '</PassWord>
									<Domain_Key>' . $this->Domain_Key . '</Domain_Key>
									<System>' . $this->system . '</System>
								</Authentication>
								<intRouteScheduleID>' . $route_schedule_id . '</intRouteScheduleID>
								<dtJourneyDate>' . date('Y-m-d', strtotime($journey_date)) . '</dtJourneyDate>
							</GetRouteInfo>
						</soap:Body>
					</soap:Envelope>';

        $response['data']['request'] = $request;
        $response['data']['service_url'] = $url;
        return $response;
    }

    /**
     * Hold Seat Request
     * @param $params
     */
    function HoldSeatsForSchedule_request($params) {
        //hotel_code,hotel_name,hotel_city,cityid,country_code,country_name,rating,address,postal_code,latitude,longitude,description
        // debug($params);exit;
        $response['status'] = SUCCESS_STATUS;
        $response['data'] = array();
        /** Request to be formed for block seats * */
        //$url = 'http://affapi.mantistechnologies.com/service.asmx?op=HoldSeatsForSchedule';
        //$url = $this->Url . 'HoldSeatsForSchedule';
        $url = $this->Url . 'HoldSeatsForSchedule';
        $Passenger = '';
        $i = 0;

        // debug($params);exit;
        $passenger = array();
        foreach ($params['token']['seat_attr']['seats'] as $k => $v) {
            $passenger[$i]['Name'] = $params['contact_name'][$i];
            $passenger[$i]['Age'] = (int) $params['age'][$i];
            $gender = get_enum_list('gender', $params['gender'][$i]);
            if ($gender == 'Male') {
                $passenger[$i]['Gender'] = 'M';
            } else if ($gender == 'Female') {
                $passenger[$i]['Gender'] = 'F';
            }
            $passenger[$i]['SeatNo'] = (string) $k;
            $passenger[$i]['Fare'] = $v['Fare'];
            $passenger[$i]['seq_no'] = $v['seq_no'];
            $passenger[$i]['decks'] = $v['decks'];
            if ($v['SeatType'] == 'seat') {
                $seattypeid = 1;
            } else if ($v['SeatType'] == 'sleeper') {
                $seattypeid = 2;
            } else {
                $seattypeid = 4;
            }
            $passenger[$i]['SeatTypeId'] = $seattypeid;
            if ($v['IsAcSeat'] == '' || empty($v['IsAcSeat'])) {
                $passenger[$i]['IsAcSeat'] = false;
            } else if ($v['IsAcSeat'] == '1') {
                $passenger[$i]['IsAcSeat'] = true;
            }
            // $passenger[$i]['IsAcSeat'] = (empty($v['IsAcSeat']) == false ? $v['IsAcSeat'] : 0);

            $i = $i + 1;
        }
        // debug($params);exit;
        // $request_paramas['fromCityId'] = $params['Route']['FromCityId'];
        // $request_paramas['toCityId'] = $params['Route']['ToCityId'];
        $JourneyDate = explode(' ', $params['token']['JourneyDate']);
        $request_paramas['fromCityId'] = (int) $params['token']['Form_id'];
        $request_paramas['toCityId'] = (int) $params['token']['To_id'];
        $request_paramas['JourneyDate'] = $JourneyDate[0];
        $request_paramas['BusId'] = $params['token']['RouteScheduleId'];
        $request_paramas['PickUpID'] = $params['token']['PickUpID'];
        $request_paramas['DropOffID'] = $params['token']['DropID'];
        $request_paramas['ContactInfo'] = array('CustomerName' => $params['contact_name'][0],
            'Email' => $params['billing_email'],
            'Phone' => $params['passenger_contact'],
            'Mobile' => $params['passenger_contact']);


        $request_paramas['Passengers'] = $passenger;
        // $request_paramas['booking_source'] = $params['booking_source'];
        $request_paramas['ResultToken'] = $params['ResultToken'];
        // debug($request_paramas);exit;
        //    //echo count($params['contact_name']); exit;
        //    /* for ($i=0; $i<count($params['token']['contact_name']); $i++) {
        //      $Passenger .= '<Passenger>
        //      <Name>'.get_enum_list('title', $params['pax_title'][$i]).' '.$params['contact_name'][$i].'</Name>
        //      <Age>'.$params['age'][$i].'</Age>
        //      <Gender>'.get_enum_list('gender', $params['gender'][$i]).'</Gender>
        //      <SeatNo>'.$k.'</SeatNo>
        //      <Fare>'.$v['Fare'].'</Fare>
        //      <SeatType>'.$v['SeatType'].'</SeatType>
        //      <IsAcSeat>'.(empty($v['IsAcSeat']) == false ? $v['IsAcSeat'] : 0).'</IsAcSeat>
        //      </Passenger>';
        //      } */
        //      $request = '
        //      <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        //      <soap:Body>
        //      <HoldSeatsForSchedule xmlns="http://tempuri.org/">
        //      <Authentication>
        //      <UserID>'.$this->TokenId['UserID'].'</UserID>
        //      <UserType>'.$this->TokenId['UserType'].'</UserType>
        //      <Key>'. $this->TokenId['Key'].'</Key>
        //      </Authentication>
        //      <RouteScheduleId>'.$params['token']['RouteScheduleId'].'</RouteScheduleId>
        //      <JourneyDate>'.date('Y-m-d', strtotime($params['token']['JourneyDate'])).'</JourneyDate>
        //      <PickUpID>'.$params['token']['PickUpID'].'</PickUpID>
        //      <ContactInformation>
        //      <CustomerName>'.get_enum_list('title', $params['pax_title'][0]).' '.$params['contact_name'][0].'</CustomerName>
        //      <Email>'.$params['contact_email'].'</Email>
        //      <Phone>'.$params['alternate_contact'].'</Phone>
        //      <Mobile>'.$params['passenger_contact'].'</Mobile>
        //      </ContactInformation>
        //      <Passengers>
        //      '.$Passenger.'
        //      </Passengers>
        //      </HoldSeatsForSchedule>
        //      </soap:Body>
        //      </soap:Envelope>';
        //    $request = '
        // <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        // 	<soap:Body>
        // 		<HoldSeatsForSchedule xmlns="http://tempuri.org/">
        // 			<Authentication>
        // 				<UserName>' . $this->UserName . '</UserName>
        // 				<PassWord>' . $this->Password . '</PassWord>
        // 				<Domain_Key>' . $this->Domain_Key . '</Domain_Key>
        // 				<System>' . $this->system . '</System>
        // 			</Authentication>
        // 			<RouteScheduleId>' . $params['token']['RouteScheduleId'] . '</RouteScheduleId>
        // 			<JourneyDate>' . date('Y-m-d', strtotime($params['token']['JourneyDate'])) . '</JourneyDate>
        // 			<PickUpID>' . $params['token']['PickUpID'] . '</PickUpID>
        // 			<ContactInformation>
        // 				<CustomerName>' . get_enum_list('title', $params['pax_title'][0]) . ' ' . $params['contact_name'][0] . '</CustomerName>
        // 				<Email>' . $params['billing_email'] . '</Email>
        // 				<Phone>' . $params['alternate_contact'] . '</Phone>
        // 				<Mobile>' . $params['passenger_contact'] . '</Mobile>
        // 			</ContactInformation>
        // 			<Passengers>
        // 			' . $Passenger . '
        // 			</Passengers>
        // 		</HoldSeatsForSchedule>
        // 	</soap:Body>
        // </soap:Envelope>';


        $response['data']['request'] = json_encode($request_paramas, true);
        $response['data']['service_url'] = $url;
        // debug($response);exit;
        return $response;
    }

    /**
     * Booking params
     */
    function BookSeats_request($booking_params, $booking_source, $booking_id) {
       /* debug($booking_params);
        debug($booking_source);
        debug($booking_id);
        die('55');*/
        $response['status'] = SUCCESS_STATUS;
        $url = "";
        if(isset($booking_params['block_ticket_data']['result'])){
            $pnr_number = $booking_params['block_ticket_data']['result']['ticket_details']['pnr_number'];
            $operator_pnr_number = $booking_params['block_ticket_data']['result']['ticket_details']['operator_pnr'];

            $url = $this->Url.'gds/api/confirm_booking/'.$pnr_number.'.json';
            $data = array(
                "pnr_number" => $operator_pnr_number,
            );
            $response['data'] = json_encode($data);
        }else{
            $response['status'] = FAILURE_STATUS;
        }
        
        
        $response['request_url'] = $url;
        $response['api_key'] = $this->Api_key;

        return $response;
    }

    /**
     * Booking params
     */
    function GetBookSeat_request($booking_params, $booking_source) {

        $response['status'] = SUCCESS_STATUS;
        $response['data'] = array();
        $url = $this->Url . 'GetBookingDetails';
        $request_paramas['PNR'] = $booking_params['data']['result']['PNRNo'];
        $request_paramas['TicketNo'] = $booking_params['data']['result']['TicketNo'];
        $request_paramas['booking_source'] = $booking_source;
        $response['data']['request'] = json_encode($request_paramas, true);
        $response['data']['service_url'] = $url;

        return $response;
    }

    /*
     * Balu A
     * Format the Request For Cancellation
     */

    function cancellation_request_params($booking_details, $app_reference ,$seat_to_cancel ="") {

        $cancellation_request_params['request_data'] = array();
        $cancellation_request_params['request_data']['PNRNo'] = $booking_details['PNRNo'];
        $cancellation_request_params['request_data']['TicketNo'] = $booking_details['TicketNo'];
        //$cancellation_request_params['request_data']['SeatNos'] = $booking_details['SeatNos'];
        if(empty($seat_to_cancel)){
            $cancellation_request_params['request_data']['SeatNos'] = $booking_details['SeatNos'];
        }else{
             $cancellation_request_params['request_data']['SeatNos'] = $seat_to_cancel;
        }
        $cancellation_request_params['request_data']['booking_source'] = $booking_details['booking_source'];
        $cancellation_request_params['request_data']['AppReference'] = $app_reference;
        //debug($cancellation_request_params);die('000000');
        return $cancellation_request_params;
    }

    /*
     * Balu A
     * Checks whether ticket is elgible for Cancellation or not
     * API MethodName: IsCancellable2
     */

    function is_cancellable($cancellation_request_params) {
        $request_data = $cancellation_request_params['request_data'];
        //debug($request_data);exit('5');
    
        $api_key = $this->Api_key;
        $ticket_no = $request_data['TicketNo'];
        $seat_no = $request_data['SeatNos'];
        $request_data = array(
                'ticket_no' => $ticket_no,
                'seat_numbers' => $seat_no
            );
        $url = $this->Url.'gds/api/can_cancel.json';
        
        //?ticket_number='.$ticket_no.'&seat_numbers='.$seat_no
        
        $response['status'] = SUCCESS_STATUS;
        $response['request_url'] = $url;
        $response['api_key'] = $api_key;
        $response['request_data'] = $request_data;
        /*debug($response);
        die('here');*/
        return $response;
    }

    /*
     * Balu A
     * Cancell the Ticket(Complete Booking Cancel)
     * API MethodName: CancelTicket2
     */

    function cancel_ticket($cancellation_request_params, $app_reference) {
        #debug($cancellation_request_params);die();
        $api_key = $this->Api_key;
        $ticket_no = $cancellation_request_params['request_data']['TicketNo'];
        $seat_no = $cancellation_request_params['request_data']['SeatNos'];
        $request_data = array(
                'ticket_no' => $ticket_no,
                'seat_numbers' => $seat_no
            );
        $url = $this->Url.'gds/api/cancel_booking.json';
     
        $response['status'] = SUCCESS_STATUS;
        $response['request_url'] = $url;
        $response['api_key'] = $api_key;
        $response['request_data'] = $request_data;
        /*debug($response);
        die('here');*/
        return $response;
    }
	function get_all_operators()
	{
		$this->CI->load->driver('cache');
		$this->opts_cache = $this->CI->cache->file->get("opts_".$this->CI->bus_engine_system."_".BITLA_BUS_BOOKING_SOURCE);
		if(empty($this->opts_cache) || !isset($this->opts_cache)){
			$url = $this->Url."/gds/api/operators.json";
			$api_key = $this->Api_key;
			$res = $GLOBALS['CI']->api_interface->get_json_response_bitla($url, $api_key);
			$bitla_opts = array();
			foreach($res["result"] AS $opt_key=>$opts)
			{
				if($opt_key>0){
					$bitla_opts[$opts[0]]=$opts[1];
					$bitla_opts[$opts[0]."_cancel_policy"]=$opts[8];
				}
			}
			$this->opts_cache = $bitla_opts;
			$cache_exp = "3600";
			$this->CI->cache->file->save("opts_".$this->CI->bus_engine_system."_".BITLA_BUS_BOOKING_SOURCE, $bitla_opts, $cache_exp);
		}
	}
    /**
     *  Balu A
     * get search result from travelyaari
     * @param number $search_id unique id which identifies search details
     */
    function get_bus_list($search_id = '') {
        $this->CI->load->driver('cache');
        $response['data'] = array();
        $response['status'] = true;
        $search_data = $this->search_data($search_id);
        //debug($search_data);exit;
        //Cache
        $cache_search = $this->CI->config->item('cache_bus_search');
        $search_hash = $this->search_hash;
        $header_info = $this->get_header();

        if ($cache_search) {
            $cache_contents = $this->CI->cache->file->get($search_hash);
        }
        $cache_contents = "";
        if ($search_data['status'] == true) {

            if ($cache_search === false || ($cache_search === true && empty($cache_contents) == true)) {
                
                //debug($request);exit;
                $request['status'] = true;
                if ($request['status']) {

                    $from_id = explode(',', $search_data['data']['bus_station_from_id']);
                    $to_id = explode(',', $search_data['data']['bus_station_to_id']);
					
					$bitla_from_id = $from_id[0];
					$bitla_to_id = $to_id[0];
					
					$sr_from_id = $from_id[1];
					$sr_to_id = $to_id[1];
					
					unset($from_id[1]);
					unset($to_id[1]);
					
					$from_id = array_values($from_id);
					$to_id = array_values($to_id);
					
                    $new_array = array();
                    for($i = 0; $i < count($from_id); $i++){
                        $new_array[] = $from_id[$i].'-'.$to_id[$i];
                    }
					//debug($bitla_to_id); exit; 
                    $arr_search_unique = array_unique($new_array);
					$arr_search_unique = array_values($arr_search_unique);
					//Lets add SRS city ids at the end
					//$arr_search_unique[] = $sr_from_id."-".$sr_to_id;
                    $f_response = array();
					$city_arr_len = count($arr_search_unique);
                    for($i = 0; $i < $city_arr_len; $i++){
						$srs = 0;
						if($i==($city_arr_len-1))
							$srs = 0; 
                        $explode_data = explode('-',$arr_search_unique[$i]);
                        $id_form = $explode_data[0];
                        $id_to = $explode_data[1];
						
						if($id_form==0)
							$id_form = $bitla_from_id;
						if($id_to==0)
							$id_to = $bitla_to_id;
						
                        //if(($id_form != $bitla_from_id) || ($id_to != $bitla_to_id) || ($i==0) || ($srs==1)){
						if($i==0){
                            $request = $this->bus_search_request($id_form,$id_to, $search_data['data']['bus_date_1'], 1, $search_id, $srs);
			    $response_data = $GLOBALS['CI']->api_interface->get_json_response_bitla($request['request_url'],$request['api_key']);			   
			    $www_res = $this->format_as_tmx_response($response_data,$search_data['data'], $id_form, $id_to)['Search'];
                            //debug($id_form." - ".$id_to);
                            $f_response[] = $www_res;
                        }
                    }
					//exit; 
                    $formated_response = array();
                    foreach ($f_response as $key => $value) {
                        foreach ($value as $k => $v) {
                            array_push($formated_response,$v);
                        }
                    }
                     //debug($formated_response);exit('555'); 
                    if ($this->valid_search_result($formated_response)) {
                        $response['data']['result'] = @$formated_response;
                        if ($cache_search) {
                            $cache_exp = $this->CI->config->item('cache_bus_search_ttl');
                            $this->CI->cache->file->save($search_hash.$GLOBALS['CI']->entity_user_id, $response['data'], $cache_exp);
                        }
                    } else {
                        $response['status'] = false;
                    }
                } else {
                    $response['status'] = false;
                }
            } else {
                //read from cache
                $response['data'] = $cache_contents;
            }
        } else {
            $response['status'] = false;
        }
        //debug($response);exit('6');
        return $response;
    }

    /**
     * @param string	$route_schedule_id
     * @param datetime	$journey_date
     */
    function get_bus_details($route_schedule_id, $journey_date, $route_code, $ResultToken, $booking_source) {

        $response['data'] = array();
        $response['status'] = true;
        if (empty($route_schedule_id) == false and empty($journey_date) == false) {
            //get request
            $header_info = $this->get_header();
            $request = $this->GetRouteScheduleDetailsWithComm_request($route_schedule_id, $journey_date, $route_code, $ResultToken, $booking_source);
            //debug($request);die('bitla-details');
            //get data
            if ($request['status']) {

                $response_data = $GLOBALS['CI']->api_interface->get_json_response_bitla($request['request_url'],$request['api_key']);
                $formatted_seat_map_data = $this->busLayout_formatting($response_data);
                #debug($formatted_seat_map_data);exit('seat map format');
                if (valid_array($formatted_seat_map_data) == true && $formatted_seat_map_data['status'] == true) {
                    $response['data']['result'] = $formatted_seat_map_data['SeatLayout'];
                } else {
                    $response['status'] = false;
                }
            } else {
                $response['status'] = false;
            }
        } else {
            $response['status'] = false;
        }
        // debug($response);exit;
        return $response;
    }

    /**
     * @param string	$route_schedule_id
     * @param datetime	$journey_date
     */
    function get_bus_information($route_schedule_id, $journey_date) {
        $response['data'] = array();
        $response['status'] = true;
        if (empty($route_schedule_id) == false and empty($journey_date) == false) {
            //get request
            $request = $this->GetRouteInfo_request($route_schedule_id, $journey_date);
            //get data
            if ($request['status']) {
                $response_data = $GLOBALS['CI']->api_interface->get_xml_response($request['data']['service_url'], $request['data']['request']);
                $GLOBALS['CI']->custom_db->generate_static_response(json_encode($response_data));

                /* //$static_search_result_id = 2047;//seater
                  $static_search_result_id = 2049;//sleeper
                  $response_data = $GLOBALS['CI']->bus_model->get_static_response($static_search_result_id);
                 */
                if (valid_array($response_data) == true && @$response_data['soap:Envelope']['soap:Body']['GetRouteInfoResponse']['GetRouteInfoResult'] != false) {
                    $response['data']['result'] = @$response_data['soap:Envelope']['soap:Body']['GetRouteInfoResponse']['GetRouteInfoResult'];
                } else {
                    $response['status'] = false;
                }
            } else {
                $response['status'] = false;
            }
        } else {
            $response['status'] = false;
        }
        return $response;
    }

    /**
     * Block Seats And Proceed to booking or PG
     * @param number $search_id
     * @param array  $block_params
     */
    function block_seats($search_id, $block_params) {
        $response['data'] = array();
        $response['status'] = SUCCESS_STATUS;
        if (valid_array($block_params)) {
            //get request
            $request = $this->Tentative_Booking_request($block_params);
           //debug($request);exit('Tentative Booking request');
            //get data
            $header_info = $this->get_header();
            if ($request['status']) {

                $response_data1 = $GLOBALS['CI']->api_interface->get_json_response_bitla($request['url'],$this->Api_key,$request['data']);
				$GLOBALS['CI']->custom_db->generate_static_response(json_encode($request));
				$GLOBALS['CI']->custom_db->generate_static_response(json_encode($response_data1));
				//debug($response_data1); exit("In bitla block");
                $response_data = $this->format_tentative_booking($response_data1);
                #debug($response_data);exit;
                // for bitla,get the reponse and format todo-monday
                if (valid_array($response_data) == true && $response_data['Status'] == true) {
                    $response['data']['result'] = $response_data['HoldSeatsForSchedule'];
                } else {
                    $response['status'] = false;
                    $response['msg'] = $response_data['Message'];
                }
            } else {
                $response['status'] = FAILURE_STATUS;
            }
        } else {
            $response['status'] = FAILURE_STATUS;
            $response['msg'] = 'Invalid Operation, Hacking';
        }
		//debug($response); exit; 
        return $response; 
    }

    /**
     *
     */
    function process_booking($book_id, $booking_params) {
        echo "<pre>book_id BITLA===== ";print_r($book_id);
        echo "<pre>booking_params BITLA===== ";print_r($booking_params); die;
        $response['data'] = array();
        $response['status'] = SUCCESS_STATUS;
        $resposne['msg'] = 'Remote IO Error';
        if (valid_array($booking_params)) {
            // echo 'test';exit;
            //get request
            $request = $this->BookSeats_request($booking_params, $booking_params['booking_source'], $book_id);
            $header_info = $this->get_header();
            //get data
            //debug($request);die('BookSeats_request');
            if ($request['status']) {

                $response_data = $GLOBALS['CI']->api_interface->get_json_response_bitla($request['request_url'],$request['api_key'],$request['data']);
               // debug($response_data);exit('++');
			   $GLOBALS['CI']->custom_db->generate_static_response(json_encode($request));
                $GLOBALS['CI']->custom_db->generate_static_response(json_encode($response_data));
                /**    PROVAB LOGGER * */
                $GLOBALS['CI']->private_management_model->provab_xml_logger('Book_Seat', $book_id, 'bus', json_encode($request['data']), json_encode($response_data));
                 //debug($response_data);exit;
                if (valid_array($response_data) == true && $response_data['response']['code'] != 500) {
                    //$response['data']['result'] = $response_data['BookSeats'];
                    $response['data']['result'] = $response_data;
                } else {
                    $response['status'] = false;
                    $response['msg'] = $response_data['Message'];
                }
            } else {
                $response['status'] = false;
                $response['msg'] = 'Invalid Booking Request';
            }
        } else {
            $response['status'] = FAILURE_STATUS;
        }

        #debug($response);die('55');
        return $response;
    }

    function get_booking_details($booking_params, $booking_source) {
        // debug($booking_params);exit;
        $response['data'] = array();
        $response['status'] = SUCCESS_STATUS;
        $resposne['msg'] = 'Remote IO Error';
        if (valid_array($booking_params)) {

            //get request
            $request = $this->GetBookSeat_request($booking_params, $booking_source);

            $header_info = $this->get_header();
            //get data
            if ($request['status']) {

                $response_data = $GLOBALS['CI']->api_interface->get_json_response($request['data']['service_url'], $request['data']['request'], $header_info);
                // debug($response_data);exit;
                if (valid_array($response_data) == true && $response_data['Status'] == true) {
                    $response['data']['result'] = $this->convert_bookedeats_to_application_currency($response_data);
                    // debug($response);exit;
                    // $response['data']['result'] = $response_data['BookSeats'];
                } else {
                    $response['status'] = false;
                }
            } else {
                $response['status'] = false;
            }
        } else {
            $response['status'] = FAILURE_STATUS;
        }
        return $response;
    }
    /**
     * Formates Search Response
     * @param array $search_result
     * @param string $module(B2C/B2B)
     */
    public function format_search_response($search_result, $currency_obj, $search_id, $module,$module_type='B2B') {
    
        $formatted_search_data = array();
        $search_result = $search_result['data']['result'];
        foreach($search_result as $bus_key => $bus_result){
            if($module_type == 'B2B'){
                $api_price_details = $bus_result;
                $admin_price_details = $this->update_markup_currency($bus_result, $currency_obj,1, true, false, $module_type); 
                $agent_price_details = $this->update_markup_currency($bus_result, $currency_obj,1, false, true, $module_type);
                $b2b_price_details = $this->b2b_price_details($api_price_details, $admin_price_details, $agent_price_details, $currency_obj);
                $bus_result['b2b_PriceDetails'] = $b2b_price_details; //B2B PRICE DETAILS
                //debug($bus_result);exit;
            }
            else{

                $this->update_markup_currency($bus_result, $currency_obj,1, false, true, $module_type); 
            }
            $formatted_search_data[$bus_key] = $bus_result;
        }
       
        return $formatted_search_data;
        // debug($formatted_search_data);exit;
    }
    /**
     *
     * @param array $api_price_details
     * @param array $admin_price_details
     * @param array $agent_price_details
     * @return number
     */
    function b2b_price_details_1($api_price_details, $admin_price_details, $agent_price_details, $currency_obj) {

        $total_markup = $admin_price_details['Fare']-$api_price_details['Fare'];
        
        $gst_value = 0;
        //calculating the Bus GST
        if($total_markup > 0){
            $gst_details = $GLOBALS['CI']->custom_db->single_table_records('gst_master', '*', array('module' => 'bus'));
            if($gst_details['status'] == true){
                if($gst_details['data'][0]['gst'] > 0){
                    $gst_value = ($total_markup/100) * $gst_details['data'][0]['gst'];
                    $gst_value  = $gst_value;
                }
            }
        }
        // need to add the admin commission and agent commission
        /*if($api_price_details['CommAmount'] > 0){
           
        }*/

        //debug($api_price_details['CompanyName']);die('==');

        $user_oid = $GLOBALS['CI']->entity_user_id;
        $domain_details = $GLOBALS['CI']->custom_db->single_table_records('b2b_user_details','user_oid,comm_group_id', array('user_oid' => intval($user_oid)));
        $group_id = $domain_details['data'][0]['comm_group_id'];

        $__op_name = $api_price_details['CompanyName'];

        $this->commission = $currency_obj->get_commission($__op_name,$user_oid,$group_id, BITLA_BUS_BOOKING_SOURCE,'generic');
        //debug($this->commission);die('bitla commission');
        
        
        //Calculate master commission
         $master_commission = 0;
         $tds_val = 0;
        if(!empty($this->commission) && $this->commission['admin_commission_list']['master_commission'][0]['is_master'] == true){

            $comm_amnt = $this->commission['admin_commission_list']['master_commission'][0]['value'];
            $api_price_details['CommAmount'] = ($api_price_details['base_fare']*$comm_amnt)/100;
            $tds_val = $this->commission['admin_commission_list']['master_commission'][0]['tds'];
           
        }

        //debug($this->commission);die();
        // Calculate agent commission
        $commission_value = 0;
        //$tds_val = 0;
        if(!empty($this->commission) && $this->commission['admin_commission_list']['commission_value'][0]['value'] > 0){
            $commission_value = $this->commission['admin_commission_list']['commission_value'][0]['value'];
            //$tds_val = $this->commission['admin_commission_list']['commission_value'][0]['tds'];


        }

        //debug($commission_value);die();
        $total_price['Fare'] = $api_price_details['Fare'];
        $total_price['base_fare'] = $api_price_details['base_fare'];
        $total_price['_CustomerBuying'] = round($agent_price_details['Fare']+$gst_value,2);
        
        $total_price['_AdminBuying'] = round($api_price_details['Fare']-$api_price_details['CommAmount'],2);
        $total_price['_AgentMarkup'] =  $agent_price_details['Fare'] - $admin_price_details['Fare'];
        
        //$this->commission = $currency_obj->get_commission();
        $agent_commission = $this->calculate_commission($api_price_details['CommAmount']);

        $_AgentBuying = $admin_price_details['Fare']+$gst_value-($api_price_details['CommAmount']*$commission_value)/100 + ($agent_commission*$tds_val)/100 ;
           
        $total_price['_AgentBuying'] = round($_AgentBuying,2);
        $total_price['_Commission'] = round($agent_commission,2);
        $total_price['_AdminCommission'] = round($api_price_details['CommAmount'], 2);
        $total_price['_tdsCommission'] = round(($agent_commission*$tds_val)/100, 2);
        $total_price['_AgentEarning'] = round(($total_price['_Commission'] + $total_price['_AgentMarkup']- ($agent_commission*$tds_val)/100),2);
        $total_price['_TotalPayable'] = round($total_price['_AgentBuying'],2);
        $total_price['_GST'] = round($gst_value,2);
        //debug($total_price);exit;
        $total_price['service_tax'] = $api_price_details['service_tax'];
        $total_price['_Commission_org_pct'] = round($commission_value,2);
        $total_price['_admin_Commission_org_pct'] = round($comm_amnt,2);

        //debug($total_price);die('----------');
        return $total_price;
    }
    function b2b_price_details($api_price_details, $admin_price_details, $agent_price_details, $currency_obj) {

        $total_markup = $admin_price_details['Fare']-$api_price_details['Fare'];
        
        $gst_value = 0;
        //calculating the Bus GST
        if($total_markup > 0){
            $gst_details = $GLOBALS['CI']->custom_db->single_table_records('gst_master', '*', array('module' => 'bus'));
            if($gst_details['status'] == true){
                if($gst_details['data'][0]['gst'] > 0){
                    $gst_value = ($total_markup/100) * $gst_details['data'][0]['gst'];
                    $gst_value  = $gst_value;
                }
            }
        }
        // need to add the admin commission and agent commission
        /*if($api_price_details['CommAmount'] > 0){
           
        }*/

        //debug($api_price_details['CompanyName']);die('==');

        $user_oid = $GLOBALS['CI']->entity_user_id;
        $domain_details = $GLOBALS['CI']->custom_db->single_table_records('b2b_user_details','user_oid,comm_group_id', array('user_oid' => intval($user_oid)));
        $group_id = $domain_details['data'][0]['comm_group_id'];
        
        //$__op_name = $api_price_details['CompanyName'];
        if($api_price_details['booking_source'] == BITLA_BUS_BOOKING_SOURCE){
            $__op_name = $api_price_details['TripId'];
            $op_name = explode(':', $__op_name);
            $__op_name = $op_name[1];
        }else{
            $__op_name = '';    
        }
        $__op_name = '';
        $this->commission = $currency_obj->get_commission($__op_name,$user_oid,$group_id, BITLA_BUS_BOOKING_SOURCE,'generic');
        //debug($this->commission);die('bitla commission');
        
        
        //Calculate master commission
         $master_commission = 0;
         $tds_val = 0;
        if(!empty($this->commission) && $this->commission['admin_commission_list']['master_commission'][0]['is_master'] == true){
            $comm_amnt = $this->commission['admin_commission_list']['master_commission'][0]['value'];
            $api_price_details['CommAmount'] = ($api_price_details['base_fare']*$comm_amnt)/100;
            $tds_val = $this->commission['admin_commission_list']['master_commission'][0]['tds'];
        }
        //debug($this->commission);die();
        // Calculate agent commission
        $commission_value = 0;
        //$tds_val = 0;
        if(!empty($this->commission) && $this->commission['admin_commission_list']['commission_value'][0]['value'] > 0){
            $commission_value = $this->commission['admin_commission_list']['commission_value'][0]['value'];
            //$tds_val = $this->commission['admin_commission_list']['commission_value'][0]['tds'];


        }

        //debug($commission_value);die();
        $total_price['Fare'] = $api_price_details['Fare'];
        $total_price['base_fare'] = $api_price_details['base_fare'];
        $total_price['_CustomerBuying'] = round($agent_price_details['Fare']+$gst_value,2);
        
        $total_price['_AdminBuying'] = round($api_price_details['Fare']-$api_price_details['CommAmount'],2);
        $total_price['_AgentMarkup'] =  $agent_price_details['Fare'] - $admin_price_details['Fare'];
        
        //$this->commission = $currency_obj->get_commission();
        $agent_commission = $this->calculate_commission($api_price_details['CommAmount']);

        $_AgentBuying = $admin_price_details['Fare']+$gst_value-($api_price_details['CommAmount']*$commission_value)/100 + ($agent_commission*$tds_val)/100 ;
           
        $total_price['_AgentBuying'] = round($_AgentBuying,2);
        $total_price['_Commission'] = round($agent_commission,2);
        $total_price['_AdminCommission'] = round($api_price_details['CommAmount'], 2);
        $total_price['_tdsCommission'] = round(($agent_commission*$tds_val)/100, 2);
        $total_price['_AgentEarning'] = round(($total_price['_Commission'] + $total_price['_AgentMarkup']- ($agent_commission*$tds_val)/100),2);
        $total_price['_TotalPayable'] = round($total_price['_AgentBuying'],2);
        $total_price['_GST'] = round($gst_value,2);
        //debug($total_price);exit;
        $total_price['service_tax'] = $api_price_details['service_tax'];
        $total_price['_Commission_org_pct'] = round($commission_value,2);
        $total_price['_admin_Commission_org_pct'] = round($comm_amnt,2);

        //debug($total_price);die('----------');
        return $total_price;
    }
    /*Seat layout format*/
    public function seat_layout_format($bus_details, $currency_obj, $module, $module_type){
       
        $formatted_seat_data = array();
        foreach($bus_details as $bus_key => $bus_result){
          
            $bus_result['Fare'] = $bus_result['base_fare'];
            $api_price_details = $bus_result;
            //debug($api_price_details);die('[][][]');
            $admin_price_details = $this->update_markup_currency($bus_result, $currency_obj,1, true, false, $module_type); 
            $agent_price_details = $this->update_markup_currency($bus_result, $currency_obj,1, false, true, $module_type);
            $b2b_price_details = $this->b2b_price_details($api_price_details, $admin_price_details, $agent_price_details, $currency_obj);
            
            $bus_result['Fare'] = $b2b_price_details['_CustomerBuying']; 
            $formatted_seat_data[$bus_key] = $bus_result;
        }
        // debug($formatted_seat_data);exit;
        return $formatted_seat_data;
    }
    /*Seat layout format*/
    public function seat_book_format($bus_details, $currency_obj, $module, $module_type){
       
        
        //$bus_details['Fare'] = $bus_details['base_fare'];
        $bus_details['Fare'] = $bus_details['total_fare'];
        $api_price_details = $bus_details;
        //debug($api_price_details);die('90909');
        $admin_price_details = $this->update_markup_currency($bus_details, $currency_obj,1, true, false, $module_type); 
        $agent_price_details = $this->update_markup_currency($bus_details, $currency_obj,1, false, true, $module_type);
        $b2b_price_details = $this->b2b_price_details($api_price_details, $admin_price_details, $agent_price_details, $currency_obj);
        $bus_result['b2b_PriceDetails'] = $b2b_price_details; 
        return $bus_result;
    }
    /**
     * Balu A
     * Converts API data currency to preferred currency
     * @param unknown_type $search_result
     * @param unknown_type $currency_obj
     */
    public function search_data_in_preferred_currency($search_result, $currency_obj) {
        $raw_bus_list = force_multple_data_format($search_result['data']['result']);
        // debug($raw_bus_list);exit;
        $bus_list = array();
        foreach ($raw_bus_list as $k => $v) {
            $bus_list[$k] = $v;
            $fare_details = $this->preferred_currency_fare_object($v, $currency_obj);
            $bus_list[$k] = array_merge($bus_list[$k], $fare_details);
        }
        $search_result['data']['result'] = $bus_list;
        // debug($search_result);
        // exit;
        return $search_result;
    }

    /**
     * Balu A
     * Converts API data currency to preferred currency
     * @param unknown_type $seat_details
     * @param unknown_type $currency_obj
     */
    public function seatdetails_in_preferred_currency($seat_details, $bus_details, $currency_obj) {
        // debug($seat_details);exit;
       

        $raw_seat_details = force_multple_data_format($seat_details['data']['result']['result']);
        // debug($raw_seat_details);exit;
        if (valid_array($raw_seat_details[0]['value'])) {
            
        }
        $Route = $bus_details;
        $clsSeat = $raw_seat_details[0]['value'];
        //Rout Details
        
        $route_fare_details = $this->preferred_currency_fare_object($bus_details, $currency_obj);
        $Route = array_merge($Route, $route_fare_details);
        // debug($Route);exit;
        // $Route = array();
        //Seatlayout details
        $seat_list = array();
        foreach ($clsSeat as $sk => $sv) {
            $seat_list[$sk] = $sv;

            $seat_list[$sk]['Fare'] = get_converted_currency_value($currency_obj->force_currency_conversion($sv['total_fare']));
            $seat_list[$sk]['ChildFare'] = get_converted_currency_value($currency_obj->force_currency_conversion($sv['total_fare']));
            $seat_list[$sk]['InfantFare'] = 0;
        }
        $seat_details['data']['result']['Route'] = $Route;
        $seat_details['data']['result']['Layout']['SeatDetails']['clsSeat'] = $seat_list;
        // debug($seat_details);exit;
        return $seat_details;
    }

    /**
     * Balu A
     * Fare Details
     * Converts the API Currency to Preferred Currency
     * @param unknown_type $FareDetails
     */
    private function preferred_currency_fare_object($fare_details, $currency_obj) {
        // debug($fare_details);exit;
        $FareDetails = array();
        $FareDetails['API_Raw_Fare'] = $fare_details['Fare'];
        $FareDetails['Fare'] = get_converted_currency_value($currency_obj->force_currency_conversion($fare_details['Fare']));
        $FareDetails['SeaterFareNAC'] = get_converted_currency_value($currency_obj->force_currency_conversion(@$fare_details['SeaterFareNAC']));
        $FareDetails['SeaterFareAC'] = get_converted_currency_value($currency_obj->force_currency_conversion(@$fare_details['SeaterFareAC']));
        $FareDetails['SleeperFareNAC'] = get_converted_currency_value($currency_obj->force_currency_conversion(@$fare_details['SleeperFareNAC']));
        $FareDetails['SleeperFareAC'] = get_converted_currency_value($currency_obj->force_currency_conversion(@$fare_details['SleeperFareAC']));
        $FareDetails['CommAmount'] = get_converted_currency_value($currency_obj->force_currency_conversion($fare_details['CommAmount']));
        return $FareDetails;
    }

    /**
     * Balu A
     * Converts Display currency to application currency
     * @param unknown_type $fare_details
     * @param unknown_type $currency_obj
     * @param unknown_type $module
     */
    public function convert_token_to_application_currency($token, $currency_obj, $module) {
       
        $master_token = array();
        $seat_attr = array();
        $seats = array();
      
     
        if($module == 'b2c'){
             //Converting to application Currency
            $temp_seat_attr = $token['seat_attr'];
            $token['CommAmount'] = get_converted_currency_value($currency_obj->force_currency_conversion($token['CommAmount']));
            $seat_attr['markup_price_summary'] = get_converted_currency_value($currency_obj->force_currency_conversion($temp_seat_attr['markup_price_summary']));
            $seat_attr['total_price_summary'] = get_converted_currency_value($currency_obj->force_currency_conversion($temp_seat_attr['total_price_summary']));
            $seat_attr['domain_deduction_fare'] = get_converted_currency_value($currency_obj->force_currency_conversion($temp_seat_attr['domain_deduction_fare']));
            $seat_attr['default_currency'] = admin_base_currency();
            //Seats
            foreach ($temp_seat_attr['seats'] as $sk => $sv) {
                $seats[$sk] = $sv;
                $seats[$sk]['Fare'] = get_converted_currency_value($currency_obj->force_currency_conversion($sv['Fare']));
                $seats[$sk]['Markup_Fare'] = get_converted_currency_value($currency_obj->force_currency_conversion($sv['Markup_Fare']));
            }
            $seat_attr['seats'] = $seats;
            //Assigning the Converted Values
            $master_token = $token;
            $master_token['seat_attr'] = $seat_attr;
        }   
        if($module == 'b2b'){

            foreach ($token['seat_attr']['seats'] as $sk => $sv) {
               
                $seats[$sk]['Fare'] = get_converted_currency_value($currency_obj->force_currency_conversion($sv['Fare']));
                $seats[$sk]['_CustomerBuying'] = get_converted_currency_value($currency_obj->force_currency_conversion($sv['_CustomerBuying']));
                $seats[$sk]['_AdminBuying'] = get_converted_currency_value($currency_obj->force_currency_conversion($sv['_AdminBuying']));
                $seats[$sk]['_AgentMarkup'] = get_converted_currency_value($currency_obj->force_currency_conversion($sv['_AgentMarkup']));
                $seats[$sk]['_AgentBuying'] = get_converted_currency_value($currency_obj->force_currency_conversion($sv['_AgentBuying']));
                $seats[$sk]['_AdminCommission'] = get_converted_currency_value($currency_obj->force_currency_conversion($sv['_AdminCommission']));
                $seats[$sk]['_Commission'] = get_converted_currency_value($currency_obj->force_currency_conversion($sv['_Commission']));
                $seats[$sk]['_tdsCommission'] = get_converted_currency_value($currency_obj->force_currency_conversion($sv['_tdsCommission']));
                $seats[$sk]['_AgentEarning'] = get_converted_currency_value($currency_obj->force_currency_conversion($sv['_AgentEarning']));
                $seats[$sk]['_TotalPayable'] = get_converted_currency_value($currency_obj->force_currency_conversion($sv['_TotalPayable']));
                $seats[$sk]['_GST'] = get_converted_currency_value($currency_obj->force_currency_conversion($sv['_GST']));
                //new added
                $seats[$sk]['_ServiceTax'] = get_converted_currency_value($currency_obj->force_currency_conversion($sv['service_tax']));

                $seats[$sk]['seq_no'] = get_converted_currency_value($currency_obj->force_currency_conversion($sv['seq_no']));
                $seats[$sk]['decks'] = get_converted_currency_value($currency_obj->force_currency_conversion($sv['decks']));
                $seats[$sk]['SeatType'] = get_converted_currency_value($currency_obj->force_currency_conversion($sv['SeatType']));
                $seats[$sk]['IsAcSeat'] = get_converted_currency_value($currency_obj->force_currency_conversion($sv['IsAcSeat']));

                $seats[$sk]['base_fare'] = get_converted_currency_value($currency_obj->force_currency_conversion($sv['base_fare']));

            }
           
            $seat_fare['Fare'] = get_converted_currency_value($currency_obj->force_currency_conversion($token['fare']['Fare']));
            $seat_fare['_CustomerBuying'] = get_converted_currency_value($currency_obj->force_currency_conversion($token['fare']['_CustomerBuying']));
            $seat_fare['_AdminBuying'] = get_converted_currency_value($currency_obj->force_currency_conversion($token['fare']['_AdminBuying']));
            $seat_fare['_AgentMarkup'] = get_converted_currency_value($currency_obj->force_currency_conversion($token['fare']['_AgentMarkup']));
            $seat_fare['_AgentBuying'] = get_converted_currency_value($currency_obj->force_currency_conversion($token['fare']['_AgentBuying']));
            $seat_fare['_AdminCommission'] = get_converted_currency_value($currency_obj->force_currency_conversion($token['fare']['_AdminCommission']));
            $seat_fare['_Commission'] = get_converted_currency_value($currency_obj->force_currency_conversion($token['fare']['_Commission']));
            $seat_fare['_tdsCommission'] = get_converted_currency_value($currency_obj->force_currency_conversion($token['fare']['_tdsCommission']));
            $seat_fare['_AgentEarning'] = get_converted_currency_value($currency_obj->force_currency_conversion($token['fare']['_AgentEarning']));
            $seat_fare['_TotalPayable'] = get_converted_currency_value($currency_obj->force_currency_conversion($token['fare']['_TotalPayable']));
            $seat_fare['_GST'] = get_converted_currency_value($currency_obj->force_currency_conversion($token['fare']['_GST']));
            //new added
            $seat_fare['_ServiceTax'] = get_converted_currency_value($currency_obj->force_currency_conversion($token['fare']['_ServiceTax']));

            $seat_fare['base_fare'] = get_converted_currency_value($currency_obj->force_currency_conversion($token['fare']['base_fare']));
            
            $token['fare'] = $seat_fare;
            $master_token = $token;
            $master_token['seat_attr']['seats'] = $seats;
        
        }
    
        return $master_token;
    }

    /**
     * Balu A
     * Converts Display currency to application currency
     * @param unknown_type $fare_details
     * @param unknown_type $currency_obj
     * @param unknown_type $module
     */
    public function convert_holdseats_to_application_currency($hold_seats) {
        $master_hold_seats = array();
        $application_default_currency = admin_base_currency();
        $currency_obj = new Currency(array('module_type' => 'bus', 'from' => get_api_data_currency(), 'to' => admin_base_currency()));
        $passenger_list = force_multple_data_format($hold_seats['Passenger']['Passenger']);
        $passengers = array();
        foreach ($passenger_list as $pk => $pv) {
            $passengers[$pk] = $pv;
            $passengers[$pk]['Fare'] = get_converted_currency_value($currency_obj->force_currency_conversion($pv['Fare']));
        }
        $master_hold_seats = $hold_seats;
        $master_hold_seats['Passenger']['Passenger'] = $passengers;
        return $master_hold_seats;
    }

    /**
     * Balu A
     * Converts Display currency to application currency
     * @param unknown_type $fare_details
     * @param unknown_type $currency_obj
     * @param unknown_type $module
     */
    public function convert_bookedeats_to_application_currency($booked_seats) {
        // debug($booked_seats);exit;
        $master_booked_seats = array();
        $application_default_currency = admin_base_currency();
        $currency_obj = new Currency(array('module_type' => 'bus', 'from' => get_api_data_currency(), 'to' => admin_base_currency()));
        $passenger_list = force_multple_data_format($booked_seats['GetBookingDetails']['Passengers']);
        // debug($booked_seats);exit;
        $passengers = array();
        foreach ($passenger_list as $pk => $pv) {
            $passengers[$pk] = $pv;
            $passengers[$pk]['Fare'] = get_converted_currency_value($currency_obj->force_currency_conversion($pv['Fare']));
        }
        $master_booked_seats = $booked_seats;
        $master_booked_seats['TotalFare'] = get_converted_currency_value($currency_obj->force_currency_conversion($booked_seats['GetBookingDetails']['TotalFare']));
        $master_booked_seats['Passengers']['Passenger'] = $passengers;
        return $master_booked_seats;
    }

    /**
     * Reference number generated for booking from application
     * @param $app_booking_id
     * @param $params
     */
    function save_booking($app_booking_id, $params, $module) {
        //error_reporting(E_ALL);
        //debug($params);exit;
        $CI = & get_instance();
        //Need to return following data as this is needed to save the booking fare in the transaction details
        $response['fare'] = $response['domain_markup'] = $response['level_one_markup'] = 0;

        $domain_origin = $params['temp_booking_cache']['domain_list_fk'];
        $status = BOOKING_CONFIRMED;
        $app_reference = $app_booking_id;
        $booking_source = $params['temp_booking_cache']['booking_source'];
        $pnr = $params['result']['PNRNo'];
        $ticket = $params['result']['TicketNo'];
        $transaction = $params['result']['TicketNo'];
        $total_fare = $params['result']['TotalFare'];
        $currency_obj = $params['currency_obj'];
        $deduction_cur_obj = clone $currency_obj;
        $promo_currency_obj = $params['promo_currency_obj'];
        //PREFERRED TRANSACTION CURRENCY AND CURRENCY CONVERSION RATE
        $transaction_currency = get_application_currency_preference();
        $application_currency = admin_base_currency();
        $currency_conversion_rate = $currency_obj->transaction_currency_conversion_rate();
        $domain_markup = array();
        $level_one_markup = array();
        $total_fare = array();
        $currency = $transaction_currency;

        $journey_datetime = date('Y-m-d H:i:s', strtotime($params['temp_booking_cache']['book_attributes']['token']['JourneyDate']));
        $departure_datetime = date('Y-m-d H:i:s', strtotime($params['temp_booking_cache']['book_attributes']['token']['DepartureTime']));
        $arrival_datetime = date('Y-m-d H:i:s', strtotime($params['temp_booking_cache']['book_attributes']['token']['ArrivalTime']));
        $departure_from = $params['temp_booking_cache']['book_attributes']['token']['departure_from'];
        $arrival_to = $params['temp_booking_cache']['book_attributes']['token']['arrival_to'];
        $boarding_from = (empty($params['temp_booking_cache']['book_attributes']['token']['boarding_from']) == false ? $params['temp_booking_cache']['book_attributes']['token']['boarding_from'] : '').@$params['result']['ServiceProviderContact'];
        $dropping_at = (empty($params['temp_booking_cache']['book_attributes']['token']['dropping_to']) == false ? $params['temp_booking_cache']['book_attributes']['token']['dropping_to'] : $params['temp_booking_cache']['book_attributes']['token']['arrival_to']);
        $bus_type = $params['temp_booking_cache']['book_attributes']['token']['bus_type'];
        $operator = $params['temp_booking_cache']['book_attributes']['token']['operator'];
        $attributes = '';

        $CI->bus_model->save_booking_itinerary_details($app_reference, $journey_datetime, $departure_datetime, $arrival_datetime, $departure_from, $arrival_to, $boarding_from, $dropping_at, $bus_type, $operator, $attributes);

        // debug($params);exit;
        $passengers = force_multple_data_format($params['result']['Passengers']);

        $ResponseMessage = $params['result']['ticket_details'];

        $SeatFare = @$ResponseMessage['seat_fare_details'];
        
        $SeatFareDetails = $params['temp_booking_cache']['book_attributes']['token']['seat_attr']['seats'];
        
        if (valid_array($passengers) == true) {
            $pass_count = count($passengers);
            $extra_markup = $params['temp_booking_cache']['book_attributes']['markup']/$pass_count;
            foreach ($passengers as $key => $passenger) {
                $SeatNo = $passenger['SeatNo'];
                $SeatNos = explode('_', $SeatNo);
                if(isset($SeatNos[0])){
                    $SeatNo = $SeatNos[0];
                }
                else{
                    $SeatNo = $SeatNo;
                }
                //$title = get_enum_list('title_bus', $params['temp_booking_cache']['book_attributes']['pax_title'][$pass_count-1]);
             
                // echo 'Seat_number'.$SeatNo;exit; 
                $current_seat_fare =  @$SeatFareDetails[$SeatNo]; //FIXME: Values are not coming
                
                $name = $passenger['Name'];
                $age = $passenger['Age'];
                $gender = ($passenger['Gender'] == 'F' ? 2 : 1);
                $seat_no = $passenger['SeatNo'];
                $fare = @$current_seat_fare['Fare'];
                $status = BOOKING_CONFIRMED;
                $seat_type = $passenger['SeatType'];
                $is_ac_seat = $passenger['IsAcSeat'];
                $attr = ($current_seat_fare);
                $admin_tds = 0;
                $agent_tds = 0;

                if($gender == 1){
                    $title = 1;
                }else if($gender == 2){
                    $title = 2;
                }
               
                if ($module == 'b2c') {
                    //Agent commission is set to 0 in b2c
                    $agent_commission = 0;
                    $agent_markup = 0;
                    $no_of_seats = count($params['result']['ticket_details']['seat_fare_details']);
                    $admin_commission = $params['temp_booking_cache']['book_attributes']['token']['CommAmount'];
                    $admin_markup = $currency_obj->get_currency($current_seat_fare['Fare']);
                    $admin_markup = floatval($admin_markup['default_value'] - $current_seat_fare['Fare']);
                    $total_markup = $admin_markup;
                   
                    $total_fare[] = ($current_seat_fare['Fare']);
                    //$domain_markup[] = ($admin_commission+$admin_markup);
                    //$level_one_markup[] = ($agent_commission+$agent_markup);

                    $domain_markup[] = ($admin_markup);
                    $level_one_markup[] = ($agent_markup);
                } elseif ($module == 'b2b') {
                    
                    //B2B Calculation
                    $this->commission = $currency_obj->get_commission();
                    //Commission
                    $no_of_seats = count($params['result']['ticket_details']['seat_fare_details']);
                    $tieup_agent_commission_percentage = $params['result']['ticket_details']['commission'];
                    $seat_fare_data = $current_seat_fare;
               
                    
                    // $admin_commission = $seat_fare_data['_Commission'];
                    $admin_commission = $seat_fare_data['_AdminCommission'];
                    $agent_commission = $seat_fare_data['_Commission'];
                    // $agent_commission = $this->calculate_commission($admin_commission);
                    
                    //Markup
                    //Admin
                    $admin_markup = $currency_obj->get_currency($seat_fare_data['Fare'], true, true, false);
                    $price_with_admin_markup = $admin_markup['default_value'];
                    $admin_markup = floatval($price_with_admin_markup - $seat_fare_data['Fare']);
                    //Agent
                    $agent_markup = $currency_obj->get_currency($price_with_admin_markup, true, false, true);
                    // $agent_markup = floatval($agent_markup['default_value'] - $price_with_admin_markup);
                    $agent_markup = $agent_markup['original_markup']+$extra_markup;
					//$agent_markup = $seat_fare_data['_AgentMarkup']+$extra_markup;
                    // debug($seat_fare_data);exit;
                   //Transaction Details
                    $gst_percent = $GLOBALS['CI']->custom_db->single_table_records('gst_master', '*', array('module' => 'bus'))["data"][0]["gst"];

                    $gst_amount = ($admin_markup/100)*$gst_percent;

                    $total_fare[] = ($seat_fare_data['Fare']);
                    $total_markup = $admin_markup + $agent_markup;
                    $gst_value_fare [] = $gst_amount;
                    //Adding GST
                   
                    //$domain_markup[] = ($admin_commission+$admin_markup);
                    //$level_one_markup[] = ($agent_commission+$agent_markup);

                    $domain_markup[] = ($admin_markup);
                    $level_one_markup[] = ($agent_markup);
                }
               
                //TDS Calculation
                $admin_tds = $currency_obj->calculate_tds($admin_commission);
                $agent_tds = $currency_obj->calculate_tds($agent_commission);
               
                //Need to store fare also
                $CI->bus_model->save_booking_customer_details($app_reference, $title, $name, $age, $gender, $seat_no, $fare, $status, $seat_type, $is_ac_seat, $admin_commission, $admin_markup, $agent_commission, $agent_markup, $currency, $attr, $admin_tds, $agent_tds);
                $pass_count--;
            }
        }
        
        $total_fare = array_sum($total_fare);
       
        $domain_markup = array_sum($domain_markup);
        $level_one_markup = array_sum($level_one_markup);
        
        // echo $params['temp_booking_cache']['book_attributes']['token']['CancPolicy'];exit;
        $phone_number = $params['temp_booking_cache']['book_attributes']['passenger_contact'];
        $phone_code = $params['temp_booking_cache']['book_attributes']['phone_country_code'];
        $alternate_number = $params['temp_booking_cache']['book_attributes']['alternate_contact'];
        $payment_mode = $params['temp_booking_cache']['book_attributes']['payment_method'];
        $email = @$params['temp_booking_cache']['book_attributes']['billing_email'];
        $canacel_policy = $params['temp_booking_cache']['book_attributes']['token']['CancPolicy'];
        $selected_pm = $params['temp_booking_cache']['book_attributes']['selected_pm'];
        $created_by_id = intval(@$CI->entity_user_id);
		$travel_id = $params['result']['travel_id'];
        $bus_booking_origin = $CI->bus_model->save_booking_details($domain_origin, $status, $app_reference, $booking_source, $pnr, $ticket, $transaction, $phone_number, $alternate_number, $payment_mode, $created_by_id, $email, $transaction_currency, $currency_conversion_rate, $canacel_policy, $phone_code, $selected_pm, $travel_id);
        /**
         * ************ Update Convinence Fees And Other Details Start *****************
        */
        // Convinence_fees to be stored and discount
        $convinence = 0;
        $discount = 0;
        $convinence_value = 0;
        $convinence_type = 0;
        $convinence_per_pax = 0;
        $gst_value = 0;
       
        if ($module == 'b2c') {
            $master_search_id = 111;
            $total_transaction_amount = $total_fare+$domain_markup;
            
            $bd_attrs = $params['temp_booking_cache']['book_attributes'];
            //debug($bd_attrs); exit;
            $pg_name = $bd_attrs["selected_pm"];
            $payment_method = $bd_attrs["payment_method"];
            $bank_code = $bd_attrs["bank_code"];
            if($payment_method == "credit_card")
                $method = "CC";
            if($payment_method == "debit_card")
                $method = "DC";
            if($payment_method == "paytm_wallet")
                $method = "PPI";
            if($payment_method == "wallet")
                $method = "wallet";
            /*$convinence = $currency_obj->convenience_fees($total_transaction_amount, $master_search_id, $no_of_seats, $pg_details);*/
            $convinence_array = $currency_obj->get_instant_recharge_convenience_fees($total_transaction_amount, $method, $bank_code);
            $convinence = $convinence_array["cf"];
            $supplier_fees = $convinence_array["sf"];
            $pace_fees = $convinence_array["pf"];
			$convinence_type = "plus"; //$convinence_row['type'];
            $convinence_per_pax = 0; //$convinence_row['per_pax'];
			
           //debug($total_transaction_amount); debug($bd_attrs);exit;
            if($params['temp_booking_cache']['book_attributes']['promo_actual_value']){
                $discount = get_converted_currency_value ( $promo_currency_obj->force_currency_conversion ( $params['temp_booking_cache']['book_attributes']['promo_actual_value']) );
            }           
            //$discount = @$params ['booking_params'] ['promo_code_discount_val'];
            $promo_code = @$params['temp_booking_cache']['book_attributes']['promo_code'];
              //GST Calculation
            if($total_markup > 0 ){
                $total_markup = $no_of_seats*$total_markup;
                $gst_details = $GLOBALS['CI']->custom_db->single_table_records('gst_master', '*', array('module' => 'bus'));
                if($gst_details['status'] == true){
                    if($gst_details['data'][0]['gst'] > 0){
                        $gst_value = (($total_markup)* $gst_details['data'][0]['gst'])/100 ;
                    }
                }
                }
        } elseif ($module == 'b2b') {
            $tta_temp = $total_fare+$domain_markup+$level_one_markup;
            $bd_attrs = $params['temp_booking_cache']['book_attributes'];
            //debug($bd_attrs); exit;
            $pg_name = $bd_attrs["selected_pm"];
            $payment_method = $bd_attrs["payment_method"];
            $bank_code = $bd_attrs["bank_code"];
            if($payment_method == "credit_card")
                $method = "CC";
            if($payment_method == "debit_card")
                $method = "DC";
            if($payment_method == "paytm_wallet")
                $method = "PPI";
            if($payment_method == "wallet")
                $method = "wallet";
            /*$convinence = $currency_obj->convenience_fees($total_transaction_amount, $master_search_id, $no_of_seats, $pg_details);*/
            $convinence_array = $currency_obj->get_instant_recharge_convenience_fees($tta_temp, $method, $bank_code);
            $convinence = $convinence_array["cf"];
            $supplier_fees = $convinence_array["sf"];
            $pace_fees = $convinence_array["pf"];
            //debug($bd_attrs); exit;
            //$convinence_row = $currency_obj->get_convenience_fees();
            $convinence_value = $convinence; //$convinence_row['value'];
            $convinence_type = "plus"; //$convinence_row['type'];
            $convinence_per_pax = 0; //$convinence_row['per_pax'];
            $gst_value = array_sum($gst_value_fare);
            $promo_code ='';
            
        }
        $GLOBALS ['CI']->load->model ( 'transaction' );
        // SAVE Booking convinence_discount_details details
        $GLOBALS ['CI']->transaction->update_convinence_discount_details ( 'bus_booking_details', $app_reference, $discount, $promo_code, $convinence, $convinence_value, $convinence_type, $convinence_per_pax, $gst_value, $pace_fees, $supplier_fees );
        /**
         * ************ Update Convinence Fees And Other Details End *****************
        */

        $response['name'] = $name;
        $response['fare'] = $total_fare;
        $response['domain_markup'] = $domain_markup;
        $response['level_one_markup'] = $level_one_markup;
        $response['convinence'] = $convinence;
        $response['gst_value'] = $gst_value;
        $response['phone'] = $phone_number;
        $response['transaction_currency'] = $transaction_currency;
        $response['currency_conversion_rate'] = $currency_conversion_rate;
      
        return $response;
    }

    function format_pre_cancel_data($is_cancellable_response_data)
    {
        $cancel_raw_data = $is_cancellable_response_data["result"]["is_ticket_cancellable"];
        $pre_cancel_data["status"] = $cancel_raw_data["is_cancellable"];
        $pre_cancel_data["is_cancellable"] = $cancel_raw_data["is_cancellable"];
        $pre_cancel_data["cancel_percent"] = $cancel_raw_data["cancel_percent"];
        $pre_cancel_data["cancel_amount"] = $cancel_raw_data["cancellation_charges"];
        return $pre_cancel_data;
    }

    function pre_cancellation_data($booking_details, $app_reference)
    {
        $cancellation_request_params = $this->cancellation_request_params($booking_details, $app_reference);
        $is_cancellable_request = $this->is_cancellable($cancellation_request_params);

        //debug($is_cancellable_request);exit;
        if ($is_cancellable_request['status']) {
           
            $is_cancellable_response_data = $GLOBALS['CI']->api_interface->get_json_response_bitla(
                $is_cancellable_request['request_url'], $is_cancellable_request['api_key'], 
                $is_cancellable_request['request_data']);
            $is_cancellable_response_data = $this->format_pre_cancel_data($is_cancellable_response_data);
            //debug($is_cancellable_response_data); exit;
            return $is_cancellable_response_data;
        }
    }

    function pre_partial_cancel($seats,$tckt_no,$b_s){
        //die('777');
        $api_key = $this->Api_key;

        $request_data = array(
                'ticket_no' => $tckt_no,
                'seat_numbers' => $seats
            );
        $url = $this->Url.'gds/api/can_cancel.json';

        $response = $GLOBALS['CI']->api_interface->get_json_response_bitla($url,$api_key,$request_data);
        
        return $response['result'];
    }

    /*
     * Balu A
     * Process Cancellation Request
     */

    function cancel_full_booking($booking_details, $app_reference ,$seat_to_cancel = "") {
        //122
        /*debug($booking_details);
        debug($app_reference);
        debug($seat_to_cancel);
        die('here');*/
        //1.IsCancellable2 -  Check elgility for Cancellation
        //2.CancelTicket2 - Cancell the Ticket if elgible
        $response['data'] = array();
        $response['status'] = FAILURE_STATUS;
        $resposne['msg'] = 'Remote IO Error';
        //Format the Request for Cancellation
        $cancellation_request_params = array();
        // debug($booking_details);exit;
        $cancellation_request_params = $this->cancellation_request_params($booking_details, $app_reference,$seat_to_cancel);
        //debug($cancellation_request_params);exit('+++++');
        $is_cancellable_request = $this->is_cancellable($cancellation_request_params);
        //debug($is_cancellable_request);exit;
        $header_info = $this->get_header();
        if ($is_cancellable_request['status']) {
           
            $is_cancellable_response_data = $GLOBALS['CI']->api_interface->get_json_response_bitla($is_cancellable_request['request_url'],$is_cancellable_request['api_key'],$is_cancellable_request['request_data']);
            //debug($is_cancellable_response_data); exit;
            $this->CI->custom_db->generate_static_response(json_encode($is_cancellable_request));
            $this->CI->custom_db->generate_static_response(json_encode($is_cancellable_response_data));

            //$is_cancellable_response_data = $GLOBALS['CI']->bus_model->get_static_response(1719);
            if (valid_array($is_cancellable_response_data) == true) {
            
                $response['data'] = $is_cancellable_response_data; //Store Data
                $resposne['msg'] = 'Cancellation is Failed';
                $IsCancellable2Result = $is_cancellable_response_data['result']['is_ticket_cancellable'];

                if ($IsCancellable2Result['is_cancellable'] == '1') {
                    //If it is elgible for Cancellation, then proceed to cancel
                    //debug($cancellation_request_params);die('45');
                    $cancell_ticket_request = $this->cancel_ticket($cancellation_request_params, $app_reference);
                    //debug($cancell_ticket_request);
                    if ($cancell_ticket_request['status']) {
                
                        $cancell_ticket_response_data = $GLOBALS['CI']->api_interface->get_json_response_bitla($cancell_ticket_request['request_url'],$cancell_ticket_request['api_key'],$cancell_ticket_request['request_data']);

                        //debug($cancell_ticket_response_data);exit('data');
                        $this->CI->custom_db->generate_static_response(json_encode($cancell_ticket_request));
                        $this->CI->custom_db->generate_static_response(json_encode($cancell_ticket_response_data));

                        //$cancell_ticket_response_data = $GLOBALS['CI']->bus_model->get_static_response(1770);
                        if (valid_array($cancell_ticket_response_data) == true) {
                            $response['data'] = $cancell_ticket_response_data; //Store Data
                            $CancelTicket2Result = $cancell_ticket_response_data;

                            $response['status'] = SUCCESS_STATUS; //Update To Success Status
                            $resposne['msg'] = 'Cancellation is Success';
                        }
                    }
                }
            }
        }
        return $response;
    }

    /*
     * Balu A
     * Save the Cancellation Details into Database
     */

    function save_cancellation_data($app_reference, $cancellation_details, $cancel_type="",$seat_to_cancel=array(),$commission_to_deduct='') {
        #debug($cancellation_details);die('99');
        $CI = & get_instance();
        $response['data'] = array();
        $response['status'] = FAILURE_STATUS;
        $resposne['msg'] = 'Remote IO Error';
        //save cancellation details format
        $cancellation_details = $this->cancel_details_format($cancellation_details);
        $cancellation_status_details = $cancellation_details['data'];
        // echo 'herere';
        // debug($cancellation_status_details);exit;
        if ($cancellation_details['status'] == true) {
            $response['status'] = SUCCESS_STATUS;
            $booking_status = 'BOOKING_CANCELLED';
            $refund_status = 'PROCESSED';
            $response["result"]=$CI->bus_model->update_cancellation_details($app_reference, $booking_status, $cancellation_details, $refund_status,$cancel_type,$seat_to_cancel,$commission_to_deduct);
        }
       
        return $response;
    }
    function get_route_details($search_id, $route_schedule_id, $route_code){

        $this->CI->load->driver('cache');
        $search_data = $this->search_data($search_id);
        $search_hash = $this->search_hash.$GLOBALS['CI']->entity_user_id;
        $cache_contents = $this->CI->cache->file->get($search_hash);
        //debug($cache_contents);die('bitla-89');
        if(!empty($cache_contents)){
            $bus_inf_data = array();
            foreach($cache_contents['result'] as $bus_data){
                if($route_schedule_id == $bus_data['RouteScheduleId'] && $route_code == $bus_data['RouteCode']){
                    $bus_inf_data = $bus_data;
                    unset($bus_inf_data['Pickups']);
                    unset($bus_inf_data['Dropoffs']);
                    //unset($bus_inf_data['CancPolicy']);
                    break;
                }
            } 
            $response['bus_data'] = $bus_inf_data;
            $response['status'] = SUCCESS_STATUS;
        }
        else{
            $response['status'] = FAILURE_STATUS;
        }

       return $response;

    }
    /**
     * Index seat number and retun details
     */
    function index_seat_details($seatDetails) {
        $seat_fare = array();
        if (valid_array($seatDetails)) {
            $seatDetails = force_multple_data_format($seatDetails);
            foreach ($seatDetails as $k => $v) {
                $seat_fare[$v['seat_detail']['seat_number']] = $v['seat_detail'];
            }
        }
        return $seat_fare;
    }

    function parse_voucher_data($data) {
        $response = $data;
        return $response;
    }

    /**
     *  Balu A
     * check and return status is success or not
     * @param unknown_type $response_status
     */
    function valid_response($response_status) {
        $status = true;
        if ($response_status != SUCCESS_STATUS) {
            $status = false;
        }
        return $status;
    }

    /**
     *  Balu A
     * check if the search response is valid or not
     * @param array $search_result search result response to be validated
     */
    private function valid_search_result($search_result) {
        if (valid_array($search_result) == true and ( $search_result['Status']) != "false"
        ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if seat is blocked or not
     */
    function seat_blocked($data) {
        if ($data['soap:Envelope']['soap:Body']['HoldSeatsForScheduleResponse']['HoldSeatsForScheduleResult']['Response']['IsSuccess'] == 'true') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if booking is successfull
     * @param $data
     */
    function seat_booked($data) {
        if ($data['soap:Envelope']['soap:Body']['BookSeatsResponse']['BookSeatsResult']['Response']['IsSuccess'] == 'true') {
            return true;
        } else {
            return false;
        }
    }

    /**
     *  Balu A
     * convert search params to format
     */
    public function search_data($search_id) {
        $response['status'] = true;
        $response['data'] = array();
        if (empty($this->master_search_data) == true and valid_array($this->master_search_data) == false) {
            $clean_search_details = $GLOBALS['CI']->bus_model->get_safe_search_data($search_id);
            if ($clean_search_details['status'] == true) {
                $response['status'] = true;
                $response['data'] = $clean_search_details['data'];
                //28/12/2014 00:00:00 - date format
                $response['data']['bus_date_1'] = date('Y-m-d', strtotime($clean_search_details['data']['bus_date_1']));
                if (empty($clean_search_details['data']['bus_date_2']) == false) {
                    $response['data']['bus_date_2'] = date('Y-m-d', strtotime($clean_search_details['data']['bus_date_2']));
                }
                $bus_station_list = $GLOBALS['CI']->db_cache_api->get_bus_station_list(array('k' => 'name', 'v' => 'station_id'));
                //update station id to the list
                /*$response['data']['bus_station_from_id'] = $bus_station_list[$response['data']['bus_station_from']];
                $response['data']['bus_station_to_id'] = $bus_station_list[$response['data']['bus_station_to']];*/
                $bitla_from_to = $this->bitla_search_data($response['data']['bus_station_from'],$response['data']['bus_station_to']);
                $response['data']['bus_station_from_id'] = $bitla_from_to['from_id'];
                $response['data']['bus_station_to_id'] = $bitla_from_to['to_id'];
                $this->master_search_data = $response['data'];
                if (empty($response['data']['bus_station_from_id']) == true || empty($response['data']['bus_station_to_id']) == true) {
                    $response['status'] = false;
                }
            } else {
                $response['status'] = false;
            }
        } else {
            $response['data'] = $this->master_search_data;
        }
		//debug($GLOBALS["CI"]->entity_user_id."Hi"); exit;
        $this->search_hash = md5(serialized_data($search_id.$response['data'].BITLA_BUS_BOOKING_SOURCE));
        return $response;
    }

    /**
     * Create ROW * COL seat Matrix
     * @param array $sl_bus_seats
     */
    public function group_matrix($sl_bus_seats) {
        // debug($sl_bus_seats);exit;
        $seats = array();
        foreach ($sl_bus_seats as $k => $v) {
            $seats[$v['decks']][$v['row']][$v['col']] = $v;
        }
        // debug($seats);exit;
        return $seats;
    }

    /**
     * Create
     * @param array $cls_seat
     */
    public function group_deck($cls_seat) {
        // debug($cls_seat);exit;
        $deck = array();
        $deck_cols = $deck_config = array();
        foreach ($cls_seat as $__k => $__v) {
            // if($__v['decks'] == 'Lower'){
            //   $__v['decks'] = '1';
            // }
            // else if($__v['decks'] == 'Upper'){
            //    $__v['decks'] = '2';
            // }
            // debug($__v);exit;
            $deck[$__v['decks']][] = $__v;
            if (isset($deck_cols[$__v['decks']]) == false || in_array($__v['col'], $deck_cols[$__v['decks']]) == false) {
                $deck_cols[$__v['decks']][] = $__v['col'];
            }
            if (empty($__v['seat_no']) == false) {
                if (isset($deck_config[$__v['decks']]) == false) {
                    $deck_config[$__v['decks']] = array('min_row' => $__v['row'], 'max_row' => $__v['row'], 'min_col' => $__v['col'], 'max_col' => $__v['col']);
                } else {
                    if ($__v['row'] < $deck_config[$__v['decks']]['min_row']) {
                        $deck_config[$__v['decks']]['min_row'] = $__v['row'];
                    }
                    if ($__v['row'] > $deck_config[$__v['decks']]['max_row']) {
                        $deck_config[$__v['decks']]['max_row'] = $__v['row'];
                    }

                    if ($__v['col'] < $deck_config[$__v['decks']]['min_col']) {
                        $deck_config[$__v['decks']]['min_col'] = $__v['col'];
                    }
                    if ($__v['col'] > $deck_config[$__v['decks']]['max_col']) {
                        $deck_config[$__v['decks']]['max_col'] = $__v['col'];
                    }
                }
            }
        }
        // debug($deck);exit;
        return array('deck_config' => $deck_config, 'deck' => $deck, 'deck_cols' => $deck_cols);
    }

    /**
     * create array with seat number as index and seat details as value
     */
    function index_seat_number($seats) {
        // debug($seats);exit;
        $index_seats = array();
        foreach ($seats as $__k => $__v) {
            if (empty($__v['seat_no']) == false) {
                $index_seats[$__v['seat_no']] = $__v;
            }
        }
        return $index_seats;
    }

    /**
     * Create array with pick up point number as index and details as value
     * @param unknown_type $points
     */
    function index_pickup_number($points) {
        // debug($points);exit;
        $index_points = array();
        foreach ($points as $__k => $__v) {
            if (empty($__v['PickupCode']) == false) {
                $index_points[$__v['PickupCode']] = $__v;
            }
        }
        return $index_points;
    }

    /**
     * Create array with drop up point number as index and details as value
     * @param unknown_type $points
     */
    function index_drop_number($points) {
        // debug($points);exit;
        $index_points = array();
        foreach ($points as $__k => $__v) {
            if (empty($__v['DropoffCode']) == false) {
                $index_points[$__v['DropoffCode']] = $__v;
            }
        }
        return $index_points;
    }

    /**
     * update markup currency and return summary
     */
    function update_markup_currency(& $price_summary, & $currency_obj, $no_of_seats = 1, $level_one_markup = false, $current_domain_markup = true, $module_type='') {
      
        // $fare_tags = array('Fare', 'SeaterFareNAC', 'SeaterFareAC', 'SleeperFareNAC', 'SleeperFareAC', 'CommAmount');
        // $markup_list = array('Fare', 'SeaterFareNAC', 'SeaterFareAC', 'SleeperFareNAC', 'SleeperFareAC');
        // debug($price_summary);exit;
        $fare_tags = array('Fare');
        $markup_list = array('Fare');
        $markup_summary = array();
        
        foreach ($price_summary as $__k => $__v) {
            if (in_array($__k, $fare_tags)) {
                $ref_cur = $currency_obj->force_currency_conversion($__v); //Passing Value By Reference so dont remove it!!!
                $price_summary[$__k] = $ref_cur['default_value'];   //If you dont understand then go and study "Passing value by reference"
               
                if (in_array($__k, $markup_list)) {
                    $temp_price = $currency_obj->get_currency($__v, true, $level_one_markup, $current_domain_markup, $no_of_seats);
                } else {
                    $temp_price = $currency_obj->force_currency_conversion($__v);
                }
                if($module_type == 'B2C'){
                    $total_markup = $temp_price['default_value']-$price_summary['Fare'];
                
                    $gst_value = 0;
                    //calculating the Bus GST
                    if($total_markup > 0){
                        $gst_details = $GLOBALS['CI']->custom_db->single_table_records('gst_master', '*', array('module' => 'bus'));
                        if($gst_details['status'] == true){
                            if($gst_details['data'][0]['gst'] > 0){
                                $gst_value = ($total_markup/100) * $gst_details['data'][0]['gst'];
                                $gst_value  = $gst_value;
                            }
                        }
                    }
                   
                    $price_summary['Fare'] = roundoff_number($temp_price['default_value']+$gst_value);
                }
                else{
                    $price_summary['Fare'] = $temp_price['default_value'];
                }
              
                //adding service tax and tax to total
                // $markup_summary['gst'] = $gst_value;
                $markup_summary[$__k] = $temp_price['default_value'];
             
            }
        }
        // debug($price_summary);exit;
        return $markup_summary;
    }

    /**
     * Update Netfare tag for the response
     */
    function update_net_fare(& $price_summary) {
       // debug($price_summary);
        $net_fare_tags = array('Fare', 'SeaterFareNAC', 'SeaterFareAC', 'SleeperFareNAC', 'SleeperFareAC');
        $commission = $price_summary['B2BCommAmount']; //Agent Commission
        foreach ($net_fare_tags as $k => $v) {
            if (isset($price_summary[$v]) == true && intval($price_summary[$v]) > 0) {
                $price_summary['Net'][$v] = $price_summary[$v] - $commission;
                $price_summary['Commission'][$v] = $commission;
            }
        }
        // debug($price_summary);
        // exit;
        return $price_summary;
    }

    /**
     * Tax price is the price for which markup should not be added
     */
    function tax_service_sum($price_summary) {
        /* //sum of tax and service ;
          return abs($price_summary['ServiceTax']+$price_summary['Tax']); */
    }

    /**
     * calculate and return total price details
     */
    function total_price($price_summary) {
        /* return abs($price_summary['OfferedPriceRoundedOff']); */
    }

    function booking_url($search_id) {
        return base_url() . 'index.php/bus/booking/' . intval($search_id);
    }

    /**
     * get bus type based on params code
     * @param string $type
     */
    function get_bus_type($HasAC, $HasNAC, $HasSeater, $HasSleeper, $IsVolvo) {
      
        $bus_type = '';
        if (empty($IsVolvo) == false && $IsVolvo != false) {
            $bus_type .= '<span class="VOLVO bus-type">VOLVO</span> ';
        }

        if (empty($HasAC) == false && $HasAC != false) {
            
            $bus_type .= '<span class="AC bus-type">AC</span> ';
        }

        if (empty($HasNAC) == false && $HasNAC != false) {
            $bus_type .= '<span class="NON_AC bus-type">NON_AC</span> ';
        }

        if (empty($HasSleeper) == false && $HasSleeper != false) {
            $bus_type .= '<span class="SLEEPER bus-type">SLEEPER</span> ';
        }

        if (empty($HasSeater) == false && $HasSeater != false) {
            $bus_type .= '<span class="SEATER bus-type">SEATER</span> ';
        }
       
        return substr($bus_type, 0, -1);
    }

    function get_bus_type_count($HasAC, $HasNAC, $HasSeater, $HasSleeper, $IsVolvo) {
        $count = 0;
        foreach (func_get_args() as $k => $v) {
            if ($v == 'true') {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Update Commission details
     */
    function update_bus_search_commission($raw_bus_list, & $currency_obj) {
        
        $data_list = array();
        $this->commission = $currency_obj->get_commission();

        if (valid_array($raw_bus_list) == true && valid_array($this->commission) == true && intval($this->commission['admin_commission_list']['value']) > 0) {
            foreach ($raw_bus_list as $k => $v) {
                //update commission
                //$bus_row = array(); Preserving Row data before calculation
                if (valid_array($v) == true) {
                    $this->update_b2b_off_fare($v, $currency_obj);

                    $com = $this->calculate_commission($v['CommAmount']);
                    $this->set_b2b_comm_tag($v, $com);
                    $data_list[$k] = $v;
                }
            }
        } else {
            foreach ($raw_bus_list as $k => $v) {
                //update commission
                $bus_row = array();
                if (valid_array($v) == true) {
                    $this->update_b2b_off_fare($v, $currency_obj);
                    $this->set_b2b_comm_tag($v, 0);
                    $data_list[$k] = $v;
                }
            }
        }
        return $data_list;
    }

    function update_b2b_off_fare(& $bus, $currency_obj) {
        // debug($bus);exit;
        //$currency_obj->
        $fare_tags = array('Fare', 'SeaterFareNAC', 'SeaterFareAC', 'SleeperFareNAC', 'SleeperFareAC');
        foreach ($fare_tags as $k => $v) {
            if (isset($bus[$v]) == true && intval($bus[$v]) > 0) {
                $bus['ORG' . $v] = $bus[$v];
                $b2b_fare = $currency_obj->get_currency($bus[$v], true, true, false, 1);
                $b2b_fare = $currency_obj->get_currency($b2b_fare['default_value']);

                $bus[$v] = $b2b_fare['default_value'];
            }
        }
    }

    /**
     *
     * @param array $bus_details
     * @param object $currency_obj
     */
    function update_seat_layout_commission(& $bus_details, & $currency_obj) {
        
        $this->commission = $currency_obj->get_commission();
        $route_com = 0;
        $Route = $bus_details['Route'];
        $CommAmount = $bus_details['Route']['CommAmount'];
        $this->update_b2b_off_fare($bus_details['Route'], $currency_obj);
        if (intval($this->commission['admin_commission_list']['value']) > 0) {
            $route_com = $this->calculate_commission($bus_details['Route']['CommAmount']);
            $route_com_pct = $bus_details['Route']['CommPCT'];
            //Commission for seats
            $bus_details['Layout']['SeatDetails']['clsSeat'] = force_multple_data_format(@$bus_details['Layout']['SeatDetails']['clsSeat']);
            foreach ($bus_details['Layout']['SeatDetails']['clsSeat'] as $k => $v) {
                //adjusting % of commission
                if ($v['Fare'] > 0) {
                    $route_com_pct = (($CommAmount * 100) / $v['Fare']);
                    $v['CommPCT'] = $route_com_pct;
                    $v['CommAmount'] = $CommAmount;
                    $this->update_b2b_off_fare($v, $currency_obj);
                    $this->set_b2b_comm_tag($v, $route_com);
                    $bus_details['Layout']['SeatDetails']['clsSeat'][$k] = $v;
                }
            }
        } else {
            //zero commission for seats
            foreach ($bus_details['Layout']['SeatDetails']['clsSeat'] as $k => $v) {
                if ($v['Fare'] > 0) {
                    $route_com_pct = (($CommAmount * 100) / $v['Fare']);
                    $v['CommPCT'] = $route_com_pct;
                    $v['CommAmount'] = $CommAmount;
                    $this->update_b2b_off_fare($v, $currency_obj);
                    $this->set_b2b_comm_tag($v, $route_com);
                    $bus_details['Layout']['SeatDetails']['clsSeat'][$k] = $v;
                }
            }
        }
        $this->set_b2b_comm_tag($bus_details['Route'], $route_com);
    }

    /**
     * Add custom commission tag for b2b only
     * @param array		s$v
     * @param number	$b2b_com
     */
    function set_b2b_comm_tag(& $v, $b2b_com = 0) {
        $total_amt = $v['Fare'];
        if ($total_amt == 0) {
            $v['CommAmount'] = 0;
            $v['CommPCT'] = 0;
            $v['B2BCommAmount'] = 0;
            $v['B2BCommPct'] = 0;
            $v['ORG_CommPCT'] = 0;
            $v['ORG_CommAmount'] = 0;
        } else {
            $v['ORG_CommPCT'] = $v['CommPCT'];
            $v['ORG_CommAmount'] = $v['CommAmount'];

            $v['CommAmount'] = ($v['CommAmount']) - ($b2b_com);
            $v['CommPCT'] = ($v['CommAmount'] * 100) / ($total_amt);
            $v['B2BCommAmount'] = ($b2b_com);
            //Calculate %
            $v['B2BCommPct'] = number_format(($v['B2BCommAmount'] * 100) / ($total_amt), 2, '.', '');
        }

        $this->update_net_fare($v);
    }

    /**
     *
     */
    private function calculate_commission($agent_com) {
        //$agent_com_row = $this->commission['admin_commission_list'];
        $agent_com_row = $this->commission['admin_commission_list']['commission_value'][0];
       
        $b2b_comm = 0;
        if(!empty($agent_com_row)){
            if ($agent_com_row['value_type'] == 'percentage') {
            //%
            $b2b_comm = ($agent_com / 100) * $agent_com_row['value'];
            } else {
                //plus
                $b2b_comm = ($agent_com - $agent_com_row['value']);
            }
        }
        
        return number_format($b2b_comm, 2, '.', '');
    }

    /**
     *
     * @param array $fare_breakdown
     * @param array $seat_fare
     */
    function fare_breakdown_summary(& $fare_breakdown, $seat_fare) {
      
        //total_commission
        if (isset($fare_breakdown['total_commission']) == false) {
            $fare_breakdown['total_commission'] = $seat_fare['B2BCommAmount'];
        } else {
            $fare_breakdown['total_commission'] += $seat_fare['B2BCommAmount'];
        }

        // total_fare
        if (isset($fare_breakdown['total_fare']) == false) {
            $fare_breakdown['total_fare'] = $seat_fare['Markup_Fare'];
        } else {
            $fare_breakdown['total_fare'] += $seat_fare['Markup_Fare'];
        }

        //total_markup
        $markup = $seat_fare['Markup_Fare'] - $seat_fare['Fare'];
        if (isset($fare_breakdown['total_markup']) == false) {
            $fare_breakdown['total_markup'] = $markup;
        } else {
            $fare_breakdown['total_markup'] += $markup;
        }

        // total_netfare
        if (isset($fare_breakdown['total_netfare']) == false) {
            $fare_breakdown['total_netfare'] = $seat_fare['Net']['Fare'];
        } else {
            $fare_breakdown['total_netfare'] += $seat_fare['Net']['Fare'];
        }

        //tds_total_commission
        //$fare_breakdown['tds_total_commission'] = ($fare_breakdown['total_commission']/10);
        $fare_breakdown['tds_total_commission'] = ($GLOBALS['CI']->master_currency->calculate_tds($fare_breakdown['total_commission']));

        //total_payable
        $fare_breakdown['total_payable'] = $fare_breakdown['total_netfare'] + $fare_breakdown['tds_total_commission'];

        //total profit
        $fare_breakdown['total_earning'] = $fare_breakdown['total_commission'] + $fare_breakdown['total_markup'] - $fare_breakdown['tds_total_commission'];
    }

    private function get_header() {
        $response['UserName'] = $this->UserName;
        $response['Password'] = $this->Password;
        $response['DomainKey'] = $this->ClientId;
        $response['system'] = $this->system;
        return $response;
    }

    private function format_as_tmx_response($response_data,$search_data, $id_from, $id_to){
        //debug($response_data);die();
        $response = array();
        $response['Status'] = SUCCESS_STATUS ;

        $root_result = $response_data['result'][0];
        unset($response_data['result'][0]);
        $stage_names = $response_data['stage_names'];
        //debug($stage_names);die();

        $combine_details = array();
        foreach ($response_data['result'] as $key => $value) {
            $arr = array();
            $arr = array_combine($root_result, $value);
            $combine_details[] = $arr;
        }
        //debug($combine_details);die();
        $bus_details_array = array();
        //Balance Api
        $travel_id = $combine_details[0]['travel_id'];
        $api_balance = $this->get_balance_details($travel_id);
        if(is_array($combine_details) && !empty($combine_details)){
            if($api_balance['balance_amount'] > 0){
                foreach ($combine_details as $k => $v__seg_details) {
                    $_each_bus_details = array(); 
					$_each_bus_details['operator_id'] = $v__seg_details['travel_id'];
					$_each_bus_details['from_id'] = $id_from;
					$_each_bus_details['to_id'] = $id_to;
                    $_each_bus_details['compare_id'] = $v__seg_details['op_schedule_id'];
                    $_each_bus_details['BusNo'] = $v__seg_details['number'];
                    $_each_bus_details['CompanyName'] = $v__seg_details['operator_service_name'];
					
					$_each_bus_details['CompanyName'] = $this->opts_cache[$v__seg_details['travel_id']];
						
                    $_each_bus_details['CompanyId'] = '';
                    $_each_bus_details['ProvId'] = '';
                    $_each_bus_details['RouteCode'] = '';
                    $_each_bus_details['RouteScheduleId'] = $v__seg_details['id'];
                    $_each_bus_details['BusTypeName'] = $v__seg_details['bus_type'];

                    $_each_bus_details['BusLabel'] = $v__seg_details['bus_type'];

                    $d_date = $search_data['bus_date_1'];
                    $d_time = $v__seg_details['dep_time'];
                    $_each_bus_details['DeptTime'] = $d_date.' '.$d_time;
                    $_each_bus_details['DepartureTime'] = $d_date.' '.$d_time;

                    $date = new DateTime($_each_bus_details['DeptTime']);
                    $__exp_date = explode(':',$v__seg_details['duration']);
                    $date->modify('+'. $__exp_date[0].'hours'.' '.$__exp_date[1].'minutes');
                    $_each_bus_details['ArrTime'] = $date->format('Y-m-d h:i');
                    $_each_bus_details['ArrivalTime'] = $date->format('Y-m-d h:i');
                    ///////new
                    $_each_bus_details['HasNAC'] = '';
                    $_each_bus_details['HasAC'] = '';
                    if($v__seg_details['is_ac_bus'] == '1'){
                        $_each_bus_details['HasAC'] = '1';
                    }else{
                        $_each_bus_details['HasNAC'] = '';
                    }
                    $_each_bus_details['HasSeater'] = '';
                    $_each_bus_details['HasSleeper'] = '';
                    $_each_bus_details['SemiSeater'] = '';
                    if(strstr($v__seg_details['bus_type'],'Sleeper')){
                        $_each_bus_details['HasSleeper'] = '1';
                    }else if(strstr($v__seg_details['bus_type'],'Semi Sleeper')){
                        $_each_bus_details['SemiSeater'] = '1';
                    }else{
                        $_each_bus_details['HasSeater'] = '1';
                    }
                    $_each_bus_details['IsVolvo'] = '';
                    $_each_bus_details['CommAmount'] = '';
                    $_each_bus_details['DiscountAmt'] = '';

                    $_each_bus_details['TripId'] = $v__seg_details['trip_id'];
                    $_each_bus_details['CompanySuf'] = '';
                    $_each_bus_details['From'] = $search_data['bus_station_from'];
                    $_each_bus_details['To'] = $search_data['bus_station_to'];
                    $_each_bus_details['Duration'] = $v__seg_details['duration'];
                    
                    $_each_bus_details['BusStatus'] = array(
                        'BaseFares' => array(
                                '0' =>'0',
                                '1' =>'0',
                            ),  
                        'TotalTax'=>0,
                        'CurrencyCode'=>'INR',
                    );

                    $fare = array();
                    $fare_str = explode(',',$v__seg_details['fare_str']);
                    foreach ($fare_str as $k_fare => $v_fare) {  
                        $__p = explode(':',$v_fare);
                        array_push($fare,$__p['1']);
                    }
                    $_each_bus_details['Fare'] = min($fare);
                    $_each_bus_details['base_fare'] = min($fare);
                    $_each_bus_details['AvailableSeats'] = $v__seg_details['available_seats'];
                    $_each_bus_details['Max_Fare'] = max($fare);

                    //pickups
                    $pick = array();
                    $boarding_stages = explode(',',$v__seg_details['boarding_stages']);
                    foreach ($boarding_stages as $k_b_s => $v_b_s) {
                        $p = array();
                        $boarding_details = explode('|',$v_b_s);
                        foreach ($stage_names as $k_sn => $v_sn) {
                            if($k_sn == $boarding_details[0]){
                                $p['PickupCrossed'] = $v_sn;
                                $p['PickupArea'] = $v_sn;
                                $p['PickupName'] = $v_sn;
                                $p['PickupCode'] = $k_sn;
                            }
                        }
                        $p['PickupTime'] = $boarding_details[1];

                        $pick[] = $p;
                    }
                    $_each_bus_details['Pickups'] = $pick;

                    //Dropoffs
                    $dropoffs = array();
                    $dropoff_stages = explode(',',$v__seg_details['dropoff_stages']);
                    foreach ($dropoff_stages as $k_b_d => $v_b_d) {
                        $d = array();
                        $droping_details = explode('|',$v_b_d);
                        foreach ($stage_names as $k_dn => $v_dn) {
                            if($k_dn == $droping_details[0]){
                                $d['DropoffName'] = $v_dn;
                                $d['DropoffCode'] = $k_dn;
                            }
                        }
                        $d['DropoffTime'] = $droping_details[1];
                        $dropoffs[] = $d;
                    }
                    $_each_bus_details['Dropoffs']=$dropoffs;
					
                    $_each_bus_details['CancPolicy']=array();
					$canacel_policy=array();
					$cp = $this->opts_cache[$v__seg_details['travel_id']."_cancel_policy"];
					$cp = explode(",", $cp);
					foreach ($cp as $k1 => $v1) {
						//Hrs: Hours, CC: Canccellation Charges
						$hrs_n_cc = explode('|', $v1);
						$arr = array();
						$arr['Amt']='0';
						$arr['Pct']=$hrs_n_cc[1];
						$ex_cut = explode('-',$hrs_n_cc[0]);
						$arr['Mins'] = max($ex_cut)*60;
						$canacel_policy[]= $arr;
					}
					$_each_bus_details['CancPolicy']=$canacel_policy;
					/**
					$stat_cp = json_decode(base64_decode("W3siTWlucyI6NDgwLCJQY3QiOiIwIiwiQW10IjowfSx7Ik1pbnMiOjcyMCwiUGN0IjoiODAiLCJBbXQiOjB9LHsiTWlucyI6MTQ0MCwiUGN0IjoiNzAiLCJBbXQiOjB9LHsiTWlucyI6Mjg4MCwiUGN0IjoiNTAiLCJBbXQiOjB9LHsiTWlucyI6NDMyMCwiUGN0IjoiMjAiLCJBbXQiOjB9XQ=="), true);
					$_each_bus_details['CancPolicy']=$stat_cp;
					**/
					
                    $_each_bus_details['is_cancellable']=$v__seg_details['is_cancellable'];
                    $_each_bus_details['BusTypeNames']= array(
                        'IsAC'=>'',
                        'Seating'=>'',
                        'Make'=>'',
                    );
                    $_each_bus_details['ResultToken'] = md5(json_encode($v__seg_details));
                    $_each_bus_details['booking_source'] = BITLA_BUS_BOOKING_SOURCE;
                    $_each_bus_details['status'] = '1';


                    $bus_details_array[] = $_each_bus_details;
                    $response['Message'] = 'Successfull formatted...!';
                }
            }else{
                $response['Status'] = FAILURE_STATUS ;
                $response['Message'] = 'Low Balance.!';
            }
        }else{
            $response['Status'] = FAILURE_STATUS ;
            $response['Message'] = 'No Bus Found...!';
        }

        $response['Search'] = $bus_details_array;
        /*debug($response);
        die('456');*/ 
        return $response;
    }

    private function busLayout_formatting($response_data){

        //debug($response_data);die();
        $response =array();
        if(isset($response_data['result']['bus_layout'])){
            $bus_layout_details = $response_data['result']['bus_layout'];
            $coach_details = explode(',',$bus_layout_details['coach_details']);
            $fare_details = explode(',',$bus_layout_details['available']);
            $ladies_seats = explode(',',$bus_layout_details['ladies_seats']);
            $gents_seats = explode(',',$bus_layout_details['gents_seats']);
            $ladies_booked_seats = explode(',',$bus_layout_details['ladies_booked_seats']);

            $transaction_fare = explode(',',$response_data['result']['transaction_charges']);
        
            $each_col_seat = array();
            $count = 0;
            $row = 0;
            foreach ($coach_details as $k__row => $v__row) {
                $cols_details = explode('-', $v__row);
                $col = 0;
                foreach ($cols_details as $k_col => $v__col) {
                    $seat_details = explode('|',$v__col);

                    #debug($seat_details[1]);
                    if(!empty($seat_details[0])){

                        if($seat_details[1] == "SS")
                        {
                            $width = 1;
                            $length = 1;
                            $zIndex = 0;
                            $isSleeper = '';
                        }else if($seat_details[1] == "SLB" || $seat_details[1] == "LB" || $seat_details[1] == "DLB")
                        { 
                            if(strstr($response_data['result']['bus_type'],'1+1')){
                                $width = 2;
                                $length = 1;
                            }
                            else{ 
                                $width = 1;
                                $length = 2;
                            }
                            $zIndex = 0;
                            $isSleeper = "true";
                        }else if($seat_details[1] == "SUB" || $seat_details[1] == "UB" || $seat_details[1] == "DUB")
                        { 
                            if(strstr($response_data['result']['bus_type'],'1+1')){
                                $width = 2;
                                $length = 1;
                            }
                            else{;
                                $width = 1;
                                $length = 2;
                            }
                            $zIndex = 1;
                            $isSleeper = "true";
                        }else
                            {
                                $width = 1;
                                $length = 1;
                                $zIndex = 0;
                                $isSleeper = "";
                            }
                        
                        $l_u='';
                        if($zIndex == 0){
                            $l_u='Lower';
                        }else{
                            $l_u='Upper';
                        }

                        //***seat anf fare management***//
                        $b_fare = '';
                        $t_fare = '';
                        $status = -2;
                        $isAvailable = '0';
                        $service_tax = $response_data['result']['service_tax_percent'];
                        $convenience_charge = $response_data['result']['convenience_charge_percent'];
                        //debug($fare_details);die();
                        //$transaction_charges = '';
                        foreach ($fare_details as $k_fare => $v_fare) {
                            $each_fare_details = explode('|',$v_fare);
                            //debug($seat_details);
                            //debug($each_fare_details);die();
                            if($seat_details[0] == $each_fare_details[0]){
                                $b_fare = $each_fare_details[1];
                                
                                $isAvailable = '1';
                                $status = '1';
                                if(!empty($service_tax)){
                                    $s_tax = ($b_fare*$service_tax)/100;
                                }else{
                                    $s_tax = 0;
                                }
                                if(!empty($convenience_charge)){
                                    $c_tax = ($b_fare*$convenience_charge)/100;
                                }else{
                                    $c_tax = 0;
                                }

                                $t_fare = ($each_fare_details[1]+ $s_tax);
                            }
                        }
                        /////
                        $trans_fare = 0;
                        foreach ($transaction_fare as $k_fare => $trns_fare) {
                            $each_t_fare_details = explode('|',$trns_fare);
                            //debug($each_fare_details);die();
                            if($seat_details[0] == $each_t_fare_details[0]){
                                $trans_fare = $each_t_fare_details[1]; 
                            }
                        }

                        foreach ($ladies_booked_seats as $k_l_b => $v_l_b) {
                           if($v_l_b == $seat_details[0]){
                                $status = '-3';
                            } 
                        }
                        foreach ($ladies_seats as $k_l_s => $v_l_s) {
                           if($v_l_s == $seat_details[0]){
                                $status = '3';
                            } 
                        }
                        foreach ($gents_seats as $k_g_s => $v_g_s) {
                           if($v_g_s == $seat_details[0]){
                                $status = '2';
                            } 
                        }
                        

                        $each_seat_details = array(
                            'seq_no' => $count++,
                            'row' => $row,
                            'col' => $col,
                            'width' => $width,
                            'height' => $length,
                            'seat_type' => '',//$seat_details[1],
                            'seat_no' => $seat_details[0],
                            'seat_id' => $v['SEATNOID'],
                            'total_fare' => $t_fare + $trans_fare,
                            'base_fare' => $b_fare,
                            'status' => $status,
                            'decks' => $l_u,
                            'MaxRows' => '12',
                            'MaxCols' => '6',
                            'IsAvailable' => $isAvailable,
                            'service_tax' =>$s_tax + $trans_fare,
                            'trans_fare' =>$trans_fare,
                            'CompanyName' => $response_data['result']['service_name']
                        );

                        $each_col_seat[] = $each_seat_details;
                    }
                    $col++;
                }
                $row++;
            }
            
            $layout = array(
                'MaxRows' => '12',
                'MaxCols' => '6',
            );

            //**Pickups**//
            $boarding_stages = explode('~',$bus_layout_details['boarding_stages']);
            $Pickups = array();
            foreach ($boarding_stages as $k_b_s => $v_b_s) {
                $boarding_details = explode('|', $v_b_s);
                $__b_details = array();
                $__b_details['PickupCrossed'] = $boarding_details[2];
                $__b_details['Contact'] = $boarding_details[4];
                $__b_details['Landmark'] = $boarding_details[3];
                $__b_details['Address'] = $boarding_details[2];
                $__b_details['PickupTime'] = $boarding_details[1];
                $__b_details['PickupArea'] = $boarding_details[5];
                $__b_details['PickupName'] = $boarding_details[5];
                $__b_details['PickupCode'] = $boarding_details[0];

                $Pickups[] = $__b_details;
            }

            //**Pickups**//
            $dropoff_stages = explode('~',$bus_layout_details['dropoff_stages']);
            $Dropoffs = array();
            foreach ($dropoff_stages as $k_d_s => $v_d_s) {
                $dropoff_details = explode('|', $v_d_s);
                $__d_details = array();
                $__d_details['DropoffTime'] = $dropoff_details[1];
                $__d_details['DropoffName'] = $dropoff_details[3];
                $__d_details['DropoffCode'] = $dropoff_details[0];
                $Dropoffs[] = $__d_details;
            }
           
            $response['SeatLayout']['result']['value'] = $each_col_seat;
            $response['SeatLayout']['result']['layout']=$layout;
            $response['SeatLayout']['result']['Pickups']=$Pickups;
            $response['SeatLayout']['result']['Dropoffs']=$Dropoffs;
            $response['SeatLayout']['result']['Canc'] = array();
            $response['SeatLayout']['result']['seat_type']['seat_type']=array();
            $response['SeatLayout']['result']['seats']['seats']=array();
            $response['SeatLayout']['result']['status']['status']=array();
            $response['SeatLayout']['result']['total_fare']['total_fare']=array();
            $response['SeatLayout']['result']['base_fare']['base_fare']=array();
            $response['status'] = SUCCESS_STATUS;
        
        }else{
            $response['status'] = FAILURE_STATUS;
            $response['message'] = $response_data['response']['message'];
        }
        /*debug($response);
        die('HERE');*/
        return $response;
    }

    private function Tentative_Booking_request($block_params){
        /* debug($block_params); 
        die('new'); */
        $response = array();

        $no_of_seats = $block_params['token']['seat_attr']['seats'];
        $pax_title = $block_params['pax_title'];
        $pax_name = $block_params['contact_name'];
        $pax_age = $block_params['age'];
        $pax_gender = $block_params['gender'];

        $count = 0;
        $each_seat_details = array(); 
        foreach ($no_of_seats as $k_seat => $v_seat) {
            $seat_detail = array();
            //debug($v_seat);die('here');
            $seat_detail['seat_number'] =  strval("$k_seat");
            //fare changed Fare to base_fare
            //$seat_detail['fare'] =  $v_seat['Fare'];
			$bfare = strval($v_seat['base_fare']);
            $seat_detail['fare'] =  "$bfare";

            if($pax_title[$count] == 1 || $pax_title[$count] == 4 || $pax_title[$count] == 6){
                $title = "Mr";
            }else if($pax_title[$count] == 2 || $pax_title[$count] == 3 || $pax_title[$count] == 5){
                $title = "Ms";
            }
            $seat_detail['title'] = $title;

            $seat_detail['name'] = $pax_name[$count];
            $seat_detail['age'] = $pax_age[$count];

            if($pax_gender[$count] ==  1){
                $gender = "M";
            }else if($pax_gender[$count] ==  2){
                $gender = "F";
            }
            $seat_detail['sex'] = $gender;
            
            $seat_detail['is_primary'] = true;
            $seat_detail['id_card_type'] = '1';
            $seat_detail['id_card_number'] = '1111111111';
            $seat_detail['id_card_issued_by'] = 'oneone';

            $each_seat_details['seat_detail'][] = $seat_detail;
            $count++ ;
        }
        //$block_params['billing_email']
        $email = '';
        /* if(isset($block_params['billing_email']) && !empty($block_params['billing_email'])){
            $email = $block_params['billing_email'];
        }else{
            $email = $this->CI->entity_domain_voucher_email;
        } */
		$email = $this->CI->entity_domain_voucher_email;
        $contact_detail = array(
                'mobile_number' =>$block_params['passenger_contact'],
                'emergency_name' =>$pax_name[0],
                'email' => $email
            );

        $book_ticket = array(
                'seat_details' => $each_seat_details,
                'contact_detail' => $contact_detail
            );
		$JourneyDate = explode(' ',$block_params['token']['JourneyDate']);
        $JourneyDate = $JourneyDate[0];
		$from_id = $block_params['token']['from_id'];
		$to_id = $block_params['token']['to_id'];

        $request_data = array();
        $request_data['book_ticket'] = $book_ticket;
        $request_data['origin_id'] = $from_id;//$block_params['token']['Form_id'];
        $request_data['destination_id'] = $to_id; //$block_params['token']['To_id'];
        $request_data['boarding_at'] = $block_params['token']['PickUpID'];
		$request_data['drop_of'] = $block_params['token']['DropID'];
        $request_data['no_of_seats'] = $count;
        $request_data['travel_date'] = $JourneyDate;

        $url = $this->Url.'gds/api/tentative_booking/'.$block_params['token']['RouteScheduleId'].'.json';

        $response['data'] = json_encode($request_data);
        $response['status'] = SUCCESS_STATUS ;
        $response['url'] = $url;
       
        /* debug($response);
        die();*/
        return $response;
    }

    private function format_tentative_booking($response_data){

        $response = array();

        if(isset($response_data['result']['ticket_details'])){
            $response['Status'] = SUCCESS_STATUS;
            $response['HoldSeatsForSchedule'] = $response_data;
            
        }else {
            $response['Status'] = FAILURE_STATUS;
            $api_msg = $response_data['response']['message'];
            $message = str_replace('select','choose', $api_msg);
            $response['Message'] = $message;
        }

        return $response;
    }

    public function format_booking_details_bitla($booking, $temp_booking){

        $response = array();
        $result = array();

        $ticket_details = $booking['data']['result']['result']['ticket_details'];
        
            $result['IsCancelled'] = '';
            $result['RefundAmount'] = 0;
			$result['travel_id'] = $ticket_details['travel_id'];

            $result['TotalFare'] = $ticket_details['total_fare'];
            $result['TotalSeats'] = $ticket_details['no_of_seats'];

            $picks = $ticket_details['boarding_point_details'];
            $PickupInfo = array(
                'PickupTime' => $picks['dep_time'],
                'Address' => $picks['boarding_stage_address'], 
                'Phone' => $picks['contact_numbers'],
                'Landmark' => $picks['landmark'],
                'PickupName' => $picks['name']
            );
            $result['PickupInfo']=$PickupInfo;

            $TotalSeats = $ticket_details['no_of_seats'];
            $Passengers = array();
           
            for($i=0; $i < $TotalSeats; $i++) {
                $arr_pax = array();
                $gender = '';
                if($temp_booking['book_attributes']['gender'][$i] == 1){
                    $gender = 'M';
                }else{
                    $gender = 'F';
                }
                $arr_pax['Name'] = $temp_booking['book_attributes']['contact_name'][$i];
                $arr_pax['Age'] = $temp_booking['book_attributes']['age'][$i];
                $arr_pax['Gender'] = $gender;  
                $Passengers[]= $arr_pax;
            }

            $seat__d=array();
            foreach ($ticket_details['seat_fare_details'] as $key => $value) {
                $s__d=array();
                $s__d['SeatNo']= $value['seat_detail']['seat_number'];
                $s__d['IsAcSeat']= '';//$value['seat_detail'][''];
                $s__d['SeatType']=$value['seat_detail']['seat_type'];
                $s__d['Fare']=$value['seat_detail']['fare'];

                $seat__d[]=$s__d;
            }

            $pax_seat_details= array();
            for($i=0; $i < $TotalSeats; $i++){
                $pax_seat_details[]=array_merge($Passengers[$i],$seat__d[$i]);
            }

            $result['Passengers']=$pax_seat_details;

            $ContactInfo = array(
                'Mobile' =>$temp_booking['book_attributes']['passenger_contact'],
                'Phone' =>'',
                'Email' =>$temp_booking['book_attributes']['billing_email'],
                'CustomerName'=>$temp_booking['book_attributes']['contact_name'][0],
            );
            $result['ContactInfo']=$ContactInfo;
            $Cancellations = array();
            $result['Cancellations'] = $Cancellations;
            $result['BookingDate'] = date('Y-m-d h:i:s');
            $result['BusTypeName'] = $booking['result']['ticket_details']['bus_type'];

            $result['DepartureDateTime'] = $temp_booking['book_attributes']['token']['DepartureTime'];
            $result['ArrivalDateTime'] = $temp_booking['book_attributes']['token']['ArrivalTime'];
            $result['JourneyDate'] = $temp_booking['book_attributes']['token']['JourneyDate'];
            $result['ToCityName'] = $temp_booking['book_attributes']['token']['arrival_to'];
            $result['FromCityName'] = $temp_booking['book_attributes']['token']['departure_from'];

            $result['CompanyName'] = $ticket_details['travels'];
            $result['TicketNo'] = $ticket_details['ticket_number'];

            $result['seat_id'] = $ticket_details['seat_numbers'];
            $result['PNRNo'] = $ticket_details['operator_pnr'];

            $t_inner_details = array();
            foreach ($ticket_details['seat_fare_details'] as $key => $value ) {
                $seat_fare_details = array(); 

                $seat_fare_details['seat_detail']['tieup_agent_commission_percentage']='';
                $seat_fare_details['seat_detail']['fare']=$value['seat_detail']['fare'];
                $seat_fare_details['seat_detail']['seat_number'] = $value['seat_detail']['seat_number'];

                $t_inner_details[]=$seat_fare_details;
            }
            $ticket_details=array(
                'seat_fare_details'=>$t_inner_details,
                'commission' =>''
            );

            $response['result']=$result;
            $response['t_details']=$ticket_details;
            /*debug($result);
            die('8888');*/
            return $response;
    }

    private function bitla_search_data($from_name,$to_name){

        /*$bus_station_from_id = $GLOBALS['CI']->custom_db->single_table_records('bus_stations_new1','bitla_id', array('ets_city_name' => $from_name));
        $bus_station_to_id = $GLOBALS['CI']->custom_db->single_table_records('bus_stations_new1','bitla_id', array('ets_city_name' => $to_name));

        $response['status'] = SUCCESS_STATUS;
        $response['from_id']=$bus_station_from_id['data'][0]['bitla_id'];
        $response['to_id']=$bus_station_to_id['data'][0]['bitla_id'];*/

        $bus_station_from_id = $GLOBALS['CI']->custom_db->single_table_records('bus_stations_new1','bitla_id,sr_city_id,ganesh_city_id,seabird_city_id,sugama_city_id,kukkeshree_city_id,krl_city_id,sr_konduskar_id,srs_city_id', array('ets_city_name' => $from_name));

        $bus_station_to_id = $GLOBALS['CI']->custom_db->single_table_records('bus_stations_new1','bitla_id,sr_city_id,ganesh_city_id,seabird_city_id,sugama_city_id,kukkeshree_city_id,krl_city_id,sr_konduskar_id,srs_city_id', array('ets_city_name' => $to_name));

        $from_id = implode(',',$bus_station_from_id['data'][0]);
        $to_id = implode(',',$bus_station_to_id['data'][0]);
    
        $response['status'] = SUCCESS_STATUS;
        $response['from_id']=$from_id;
        $response['to_id']=$to_id;
        //debug($response);die();
        return $response;
    }

    public function cancel_details_format($cancellation_details){

        $response = array();
        $response['status'] = SUCCESS_STATUS;

        $cancel_details = $cancellation_details['data']['result']['cancel_ticket'];
        $ChargeAmt = $cancel_details['cancellation_charges'];
        $total_paid_by_agent = $cancel_details['refund_amount']+$cancel_details['cancellation_charges'];
        $ChargePct = ($ChargeAmt*100)/$total_paid_by_agent;
        $actual_refund_to_agent = $cancel_details['refund_amount']+$cancellation_details['admin_markup'];
        $data['CancelSeats'] = array(
                'ApiRefundAmount' =>$cancel_details['refund_amount'],
                'RefundAmount' =>$actual_refund_to_agent,
                'ChargeAmt' => $cancel_details['cancellation_charges'],
                'TotalFare' => $cancel_details['refund_amount'],
                'ChargePct' => $ChargePct,
				'supp_commission_reversed' => $cancellation_details["data"]["supp_commission_reversed"],
                'NewHoldId' => '',
                'NewTotalFare' => '',
            );
        $data['Status'] = SUCCESS_STATUS;
        $data['Message'] = 'SUCCESS';

        $response['data'] = $data;

        return $response;
    }

    private function get_balance_details($travel_id){
        if(!empty($travel_id)){
            //134,506 id for test
            $response = array();
            $api_key = $this->Api_key;
            $request_data = array(
                    'travel_id' => $travel_id,
                );
            $url = $this->Url.'gds/api/get_balance.json';

            $response_data = $GLOBALS['CI']->api_interface->get_json_response_bitla($url,$api_key,$request_data);

            if(valid_array($response_data) && isset($response_data['result']['balance_amount'])){
                $response['balance_amount'] = $response_data['result']['balance_amount'];
            }else{
                $response['balance_amount'] = 0;
            }

            return $response;
        }
    }

}
