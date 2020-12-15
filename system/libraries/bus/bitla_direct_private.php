<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once BASEPATH . 'libraries/Common_Api_Grind.php';

class Bitla_direct_private extends Common_Api_Grind {

    private $EndUserIp = '127.0.0.1';
    private $TokenId; //    Token ID that needs to be echoed back in every subsequent request
    private $master_search_data;
    private $commission = array();
    private $ClientId;
    private $UserName;
    private $Password;
    private $system;
    private $Url;
    private $net_fare_tags = array('Fare', 'SeaterFareNAC', 'SeaterFareAC', 'SleeperFareNAC', 'SleeperFareAC');
    public $search_hash;

    public function __construct() {
        $this->CI = &get_instance();
        $GLOBALS['CI']->load->library('Api_Interface');
        $GLOBALS['CI']->load->library('Converter');
        $GLOBALS['CI']->load->model('bus_model');        
    }

    private function set_api_credentials($booking_source) {
        $bus_engine_system = $this->CI->bus_engine_system;
        $this->system = $bus_engine_system;
        $credentials = $this->CI->custom_db->single_table_records("supplier_credentials", "config", array("booking_source" => $booking_source, "mode"=>$bus_engine_system));
        $this->details = json_decode($credentials["data"][0]["config"], true);        
        return $this->details;
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
    private function bus_search_request($bus_station_from_id, $bus_station_to_id, $bus_date, $number_of_seats = 1, $SearchId = 123456, $booking_source) {
        
        //134,506 id for test
        $bus_station_from_id = $bus_station_from_id;
        $bus_station_to_id = $bus_station_to_id;
        $bus_date = $bus_date;

        $api_credentials = $this->set_api_credentials($booking_source);            
        $api_key = $api_credentials['api_key'];
        $this->Url = $api_credentials['api_url']; 
        $url = $this->Url.'dir/api/available_routes/'.$bus_station_from_id.'/'.$bus_station_to_id .'/'.$bus_date.'.json';
                               
        // $url = $this->Url.'dir/api/cities.json'; // URL to get cities
        $response['status'] = SUCCESS_STATUS;
        $response['request_url'] = $url;
        $response['api_key'] = $api_key;
        return $response;
    }

    /**
     *
     */
    function GetRouteScheduleDetailsWithComm_request($route_schedule_id, $journey_date, $route_code, $ResultToken, $booking_source) {        
        $response['status'] = SUCCESS_STATUS;

        $api_credentials = $this->set_api_credentials($booking_source);            
        $api_key = $api_credentials['api_key'];
        $this->Url = $api_credentials['api_url']; 
        $url = $this->Url.'dir/api/service_details/'.$route_schedule_id.'.json'; 
        
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
        //  <soap:Body>
        //      <HoldSeatsForSchedule xmlns="http://tempuri.org/">
        //          <Authentication>
        //              <UserName>' . $this->UserName . '</UserName>
        //              <PassWord>' . $this->Password . '</PassWord>
        //              <Domain_Key>' . $this->Domain_Key . '</Domain_Key>
        //              <System>' . $this->system . '</System>
        //          </Authentication>
        //          <RouteScheduleId>' . $params['token']['RouteScheduleId'] . '</RouteScheduleId>
        //          <JourneyDate>' . date('Y-m-d', strtotime($params['token']['JourneyDate'])) . '</JourneyDate>
        //          <PickUpID>' . $params['token']['PickUpID'] . '</PickUpID>
        //          <ContactInformation>
        //              <CustomerName>' . get_enum_list('title', $params['pax_title'][0]) . ' ' . $params['contact_name'][0] . '</CustomerName>
        //              <Email>' . $params['billing_email'] . '</Email>
        //              <Phone>' . $params['alternate_contact'] . '</Phone>
        //              <Mobile>' . $params['passenger_contact'] . '</Mobile>
        //          </ContactInformation>
        //          <Passengers>
        //          ' . $Passenger . '
        //          </Passengers>
        //      </HoldSeatsForSchedule>
        //  </soap:Body>
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
        $response['status'] = SUCCESS_STATUS;
        $url = "";
        if(isset($booking_params['block_ticket_data']['result'])){            
            $pnr_number = $booking_params['block_ticket_data']['result']['ticket_details']['pnr_number'];
            $operator_pnr_number = $booking_params['block_ticket_data']['result']['ticket_details']['operator_pnr'];            

            $api_credentials = $this->set_api_credentials($booking_source);            
            $api_key = $api_credentials['api_key'];
            $this->Url = $api_credentials['api_url']; 
            $url = $this->Url.'dir/api/confirm_booking/'.$pnr_number.'.json';
            
            $data = array(
                "pnr_number" => $operator_pnr_number,
            );
            $response['data'] = json_encode($data);
        }else{
            $response['status'] = FAILURE_STATUS;
        }
        
        
        $response['request_url'] = $url;
        $response['api_key'] = $api_key;

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
        $api_key = ''; $url = '';

        $api_credentials = $this->set_api_credentials($request_data['booking_source']);            
        $api_key = $api_credentials['api_key'];
        $this->Url = $api_credentials['api_url'];             
        $url = $this->Url.'dir/api/can_cancel.json';
            
        $ticket_no = $request_data['TicketNo'];
        $seat_no = $request_data['SeatNos'];
        $request_data = array(
                'ticket_no' => $ticket_no,
                'seat_numbers' => $seat_no
            );        
        
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
        $api_credentials = $this->set_api_credentials($cancellation_request_params['request_data']['booking_source']);            
        $api_key = $api_credentials['api_key'];
        $this->Url = $api_credentials['api_url'];        
        $url = $this->Url.'dir/api/cancel_booking.json';
        
        $ticket_no = $cancellation_request_params['request_data']['TicketNo'];
        $seat_no = $cancellation_request_params['request_data']['SeatNos'];
        $request_data = array(
                'ticket_no' => $ticket_no,
                'seat_numbers' => $seat_no
            );        
     
        $response['status'] = SUCCESS_STATUS;
        $response['request_url'] = $url;
        $response['api_key'] = $api_key;
        $response['request_data'] = $request_data;
        return $response;
    }

    /**
     *  Balu A
     * get search result from travelyaari
     * @param number $search_id unique id which identifies search details
     */
    function get_bus_list($search_id = '', $booking_source='') {        
        $this->CI->load->driver('cache');
        $response['data'] = array();
        $formated_response = array();
        $response['status'] = true;
        $search_data = $this->search_data($search_id, $booking_source);
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
                $request['status'] = true;
                if ($request['status']) {

                    $id_form = $search_data['data']['bus_station_from_id'];
                    $id_to = $search_data['data']['bus_station_to_id'];
                    if(($id_form > 0) || ($id_to > 0)){
                        $request = $this->bus_search_request($id_form,$id_to, $search_data['data']['bus_date_1'], 1, $search_id,$booking_source);
                        $response_data = $GLOBALS['CI']->api_interface->get_json_response_bitla($request['request_url'],$request['api_key']);
                        $formated_response = $this->format_as_tmx_response($response_data,$search_data['data'],$booking_source)['Search'];                            
                    }
                    if ($this->valid_search_result($formated_response)) {
                        $response['data']['result'] = @$formated_response;                        
                        if ($cache_search) {
                            $cache_exp = $this->CI->config->item('cache_bus_search_ttl');
                            $this->CI->cache->file->save($search_hash, $response['data'], $cache_exp);
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
        return $response;
    }

    /**
     * @param string    $route_schedule_id
     * @param datetime  $journey_date
     */
    function get_bus_details($route_schedule_id, $journey_date, $route_code, $ResultToken, $booking_source) {        
        $response['data'] = array();
        $response['status'] = true;
        if (empty($route_schedule_id) == false and empty($journey_date) == false) {
            //get request
            $header_info = $this->get_header();
            $request = $this->GetRouteScheduleDetailsWithComm_request($route_schedule_id, $journey_date, $route_code, $ResultToken, $booking_source);            
            //get data
            if ($request['status']) {              
                $response_data = $GLOBALS['CI']->api_interface->get_json_response_bitla($request['request_url'],$request['api_key']);                             

                $formatted_seat_map_data = $this->busLayout_formatting($response_data, $booking_source);
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
     * @param string    $route_schedule_id
     * @param datetime  $journey_date
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
            $request = $this->Tentative_Booking_request($block_params);                      
            //get data
            $header_info = $this->get_header();
            if ($request['status']) {                     
                $response_data1 = $GLOBALS['CI']->api_interface->get_json_response_bitla($request['url'],$request['api_key'],$request['data']);             
                
                $response_data = $this->format_tentative_booking($response_data1);
                echo "<pre>response_data-->>"; print_r($response_data);die;
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
        return $response;
    }

    /**
     *
     */
    function process_booking($book_id, $booking_params) {
        echo "<pre>book_id BITLA DIRECT===== ";print_r($book_id);
        echo "<pre>booking_params BITLA DIRECT===== ";print_r($booking_params); die;
        $response['data'] = array();
        $response['status'] = SUCCESS_STATUS;
        $resposne['msg'] = 'Remote IO Error';
        if (valid_array($booking_params)) {
            // echo 'test';exit;
            //get request
            $request = $this->BookSeats_request($booking_params, $booking_params['booking_source'], $book_id);
            $header_info = $this->get_header();
            //get data
            if ($request['status']) {                
                $response_data = $GLOBALS['CI']->api_interface->get_json_response_bitla($request['request_url'],$request['api_key'],$request['data']);

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
                // debug($bus_result);exit;
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
        
        // $__op_name = $api_price_details['TripId'];
        $__op_name = '';
       
        $this->commission = $currency_obj->get_commission($__op_name,$user_oid,$group_id, $api_price_details['booking_source'],'generic');               
        //Calculate master commission
         $master_commission = 0;
         $tds_val = 0;
        if(!empty($this->commission) && $this->commission['admin_commission_list']['master_commission'][0]['is_master'] == true){
            $comm_amnt = $this->commission['admin_commission_list']['master_commission'][0]['value'];
            $api_price_details['CommAmount'] = ($api_price_details['base_fare']*$comm_amnt)/100;
            $tds_val = $this->commission['admin_commission_list']['master_commission'][0]['tds'];
        }
        
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
                    $agent_markup = $seat_fare_data['_AgentMarkup']+$extra_markup;
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

    function format_pre_cancel_data($is_cancellable_response_data, $booking_source)
    {
        $pre_cancel_data = array();
        if($booking_source == KADRI_BUS_BOOKING_SOURCE){
            $cancel_raw_data = $is_cancellable_response_data["result"]["is_ticket_cancellable"];
            $pre_cancel_data["status"] = $cancel_raw_data["is_cncl"];
            $pre_cancel_data["is_cancellable"] = $cancel_raw_data["is_cncl"];
            $pre_cancel_data["cancel_percent"] = $cancel_raw_data["cancel-percent"];
            $pre_cancel_data["cancel_amount"] = $cancel_raw_data["cancel_charges"];            
        }
        if($booking_source == GOTOUR_BUS_BOOKING_SOURCE){
            $cancel_raw_data = $is_cancellable_response_data["result"]["is_ticket_cancellable"];
            $pre_cancel_data["status"] = $cancel_raw_data["can_cancel"];
            $pre_cancel_data["is_cancellable"] = $cancel_raw_data["can_cancel"];
            $pre_cancel_data["cancel_percent"] = $cancel_raw_data["cancel-percent"];
            $pre_cancel_data["cancel_amount"] = $cancel_raw_data["cancel-charges"];            
        }  
        if($booking_source == KONDUSKAR_BUS_BOOKING_SOURCE){
            $cancel_raw_data = $is_cancellable_response_data["result"]["is_ticket_cancellable"];
            $pre_cancel_data["status"] = $cancel_raw_data["can_cancel"];
            $pre_cancel_data["is_cancellable"] = $cancel_raw_data["can_cancel"];
            $pre_cancel_data["cancel_percent"] = $cancel_raw_data["cancel_percent"];
            $pre_cancel_data["cancel_amount"] = $cancel_raw_data["cancel_charges"];            
        }
        if($booking_source == BARDE_BUS_BOOKING_SOURCE){
            $cancel_raw_data = $is_cancellable_response_data["result"]["is_ticket_cancellable"];
            $pre_cancel_data["status"] = $cancel_raw_data["is_cancellable"];
            $pre_cancel_data["is_cancellable"] = $cancel_raw_data["is_cancellable"];
            $pre_cancel_data["cancel_percent"] = $cancel_raw_data["cancel_percent"];
            $pre_cancel_data["cancel_amount"] = $cancel_raw_data["cancel-charges"];            
        }      
        return $pre_cancel_data;   
    }

    function pre_cancellation_data($booking_details, $app_reference)
    {        
        $cancellation_request_params = $this->cancellation_request_params($booking_details, $app_reference);
        $is_cancellable_request = $this->is_cancellable($cancellation_request_params);        
        if ($is_cancellable_request['status']) {            
            $is_cancellable_response_data = $GLOBALS['CI']->api_interface->get_json_response_bitla($is_cancellable_request['request_url'], $is_cancellable_request['api_key'], $is_cancellable_request['request_data']);
            
            // Kadri Travels Can Cancel Response
            // $is_cancellable_response_data = json_decode('{"result":{"is_ticket_cancellable":{"is_cncl":true,"cancel-percent":25.0,"refund_amount":675.0,"cancel_charges":225.0}}}',true);

            // Gotour Travels Can Cancel Response
            // $is_cancellable_response_data = json_decode('{"result":{"is_ticket_cancellable":{"can_cancel":true,"cancel-percent":15,"refund_amount":802.31,"cancel-charges":141.59}}}',true);
            
            // Konduskar Travels Can Cancel Response
            // $is_cancellable_response_data = json_decode('{"result":{"is_ticket_cancellable":{"can_cancel":true,"cancel_percent":5,"refund_amount":450,"cancel_charges":25}}}',true);

            // Barde Travels Can Cancel Response
            // $is_cancellable_response_data = json_decode('{"result":{"is_ticket_cancellable":{"is_cancellable":true,"cancel_percent":5,"refund_amount":845.5,"cancel-charges":44.5}}}',true);
            
            $is_cancellable_response_data = $this->format_pre_cancel_data($is_cancellable_response_data, $booking_details['booking_source']);                        
            return $is_cancellable_response_data;
        }
    }

    function pre_partial_cancel($seats,$tckt_no,$b_s){

        $api_credentials = $this->set_api_credentials($b_s);            
        $api_key = $api_credentials['api_key'];
        $this->Url = $api_credentials['api_url'];             
        $url = $this->Url.'dir/api/can_cancel.json';
        $request_data = array(
                'ticket_no' => $tckt_no,
                'seat_numbers' => $seats
            );
        $response = $GLOBALS['CI']->api_interface->get_json_response_bitla($url,$api_key,$request_data);
        
        // Kadri Travels Can Cancel Response
        // $response = json_decode('{"result":{"is_ticket_cancellable":{"is_cncl":true,"cancel-percent":25.0,"refund_amount":675.0,"cancel_charges":225.0}}}',true);

        // Gotour Travels Can Cancel Response
        // $response = json_decode('{"result":{"is_ticket_cancellable":{"can_cancel":true,"cancel-percent":5,"refund_amount":498.75,"cancel-charges":26.25}}}',true);

        // Gotour Travels Can Cancel Response
        // $response = json_decode('{"result":{"is_ticket_cancellable":{"can_cancel":true,"cancel_percent":5,"refund_amount":450,"cancel_charges":25}}}',true);
        
        // Barde Travels Can Cancel Response
        // $response = json_decode('{"result":{"is_ticket_cancellable":{"is_cancellable":true,"cancel_percent":5,"refund_amount":845.5,"cancel-charges":44.5}}}',true);
        
        if($b_s == KADRI_BUS_BOOKING_SOURCE){
            if(isset($response['result']['is_ticket_cancellable'])){
                $response['result']['is_ticket_cancellable']['is_cancellable'] = isset($response['result']['is_ticket_cancellable']['is_cncl'])?$response['result']['is_ticket_cancellable']['is_cncl']:0;    
                $response['result']['is_ticket_cancellable']['cancellation_charges'] = $response['result']['is_ticket_cancellable']['cancel_charges'];
                $response['result']['is_ticket_cancellable']['cancel_percent'] = $response['result']['is_ticket_cancellable']['cancel-percent'];                
            }
        }
        if($b_s == GOTOUR_BUS_BOOKING_SOURCE){
            if(isset($response['result']['is_ticket_cancellable'])){
                $response['result']['is_ticket_cancellable']['is_cancellable'] = isset($response['result']['is_ticket_cancellable']['can_cancel'])?$response['result']['is_ticket_cancellable']['can_cancel']:0;    
                $response['result']['is_ticket_cancellable']['cancellation_charges'] = $response['result']['is_ticket_cancellable']['cancel-charges'];
                $response['result']['is_ticket_cancellable']['cancel_percent'] = $response['result']['is_ticket_cancellable']['cancel-percent'];                
            }
        }
        if($b_s == KONDUSKAR_BUS_BOOKING_SOURCE){
            if(isset($response['result']['is_ticket_cancellable'])){
                $response['result']['is_ticket_cancellable']['is_cancellable'] = isset($response['result']['is_ticket_cancellable']['can_cancel'])?$response['result']['is_ticket_cancellable']['can_cancel']:0;    
                $response['result']['is_ticket_cancellable']['cancellation_charges'] = $response['result']['is_ticket_cancellable']['cancel_charges'];
                $response['result']['is_ticket_cancellable']['cancel_percent'] = $response['result']['is_ticket_cancellable']['cancel_percent'];                
            }
        } 
        if($b_s == BARDE_BUS_BOOKING_SOURCE){            
            if(isset($response['result']['is_ticket_cancellable'])){
                $response['result']['is_ticket_cancellable']['is_cancellable'] = isset($response['result']['is_ticket_cancellable']['is_cancellable'])?$response['result']['is_ticket_cancellable']['is_cancellable']:0;    
                $response['result']['is_ticket_cancellable']['cancellation_charges'] = $response['result']['is_ticket_cancellable']['cancel-charges'];
                $response['result']['is_ticket_cancellable']['cancel_percent'] = $response['result']['is_ticket_cancellable']['cancel_percent'];                
            }
        }      
        return $response['result'];
    }

    /*
     * Balu A
     * Process Cancellation Request
     */

    function cancel_full_booking($booking_details, $app_reference ,$seat_to_cancel = "") {
        //1.IsCancellable2 -  Check elgility for Cancellation
        //2.CancelTicket2 - Cancell the Ticket if elgible                      
        $response['data'] = array();
        $response['status'] = FAILURE_STATUS;          
        $response['booking_source'] = $booking_details['booking_source'];
        $response['msg'] = 'Remote IO Error';
             
        //Format the Request for Cancellation
        $cancellation_request_params = array();
        // debug($booking_details);exit;
        $cancellation_request_params = $this->cancellation_request_params($booking_details, $app_reference,$seat_to_cancel);                
        $is_cancellable_request = $this->is_cancellable($cancellation_request_params);
        
        $header_info = $this->get_header();        
        if ($is_cancellable_request['status']) {
           
            $is_cancellable_response_data = $GLOBALS['CI']->api_interface->get_json_response_bitla($is_cancellable_request['request_url'],$is_cancellable_request['api_key'],$is_cancellable_request['request_data']);
            
            // Kadri Cancellation Response
            // $is_cancellable_response_data = json_decode('{"result":{"is_ticket_cancellable":{"is_cncl":true,"cancel-percent":25.0,"refund_amount":675.0,"cancel_charges":225.0}}}',true);

            // Gotour Cancellation Response
            // $is_cancellable_response_data = json_decode('{"result":{"is_ticket_cancellable":{"can_cancel":true,"cancel-percent":15,"refund_amount":802.31,"cancel-charges":141.59}}}',true);
            
            // Konduskar Cancellation Response
            // $is_cancellable_response_data = json_decode('{"result":{"is_ticket_cancellable":{"can_cancel":true,"cancel_percent":5,"refund_amount":450,"cancel_charges":25}}}',true);
            
            // Barde Travels Can Cancel Response
            // $is_cancellable_response_data = json_decode('{"result":{"is_ticket_cancellable":{"is_cancellable":true,"cancel_percent":5,"refund_amount":845.5,"cancel-charges":44.5}}}',true);

            if($booking_details['booking_source'] == KADRI_BUS_BOOKING_SOURCE){
                if(isset($is_cancellable_response_data['result']['is_ticket_cancellable'])){
                    $is_cancellable_response_data['result']['is_ticket_cancellable']['is_cancellable'] = isset($is_cancellable_response_data['result']['is_ticket_cancellable']['is_cncl'])?$is_cancellable_response_data['result']['is_ticket_cancellable']['is_cncl']:0;    
                    $is_cancellable_response_data['result']['is_ticket_cancellable']['cancellation_charges'] = $is_cancellable_response_data['result']['is_ticket_cancellable']['cancel_charges'];
                    $is_cancellable_response_data['result']['is_ticket_cancellable']['cancel_percent'] = $is_cancellable_response_data['result']['is_ticket_cancellable']['cancel-percent'];                    
                }
            } 
            if($booking_details['booking_source'] == GOTOUR_BUS_BOOKING_SOURCE){
                if(isset($is_cancellable_response_data['result']['is_ticket_cancellable'])){
                    $is_cancellable_response_data['result']['is_ticket_cancellable']['is_cancellable'] = isset($is_cancellable_response_data['result']['is_ticket_cancellable']['can_cancel'])?$is_cancellable_response_data['result']['is_ticket_cancellable']['can_cancel']:0;    
                    $is_cancellable_response_data['result']['is_ticket_cancellable']['cancellation_charges'] = $is_cancellable_response_data['result']['is_ticket_cancellable']['cancel-charges'];
                    $is_cancellable_response_data['result']['is_ticket_cancellable']['cancel_percent'] = $is_cancellable_response_data['result']['is_ticket_cancellable']['cancel-percent'];                    
                }
            }
            if($booking_details['booking_source'] == KONDUSKAR_BUS_BOOKING_SOURCE){
                if(isset($is_cancellable_response_data['result']['is_ticket_cancellable'])){
                    $is_cancellable_response_data['result']['is_ticket_cancellable']['is_cancellable'] = isset($is_cancellable_response_data['result']['is_ticket_cancellable']['can_cancel'])?$is_cancellable_response_data['result']['is_ticket_cancellable']['can_cancel']:0;    
                    $is_cancellable_response_data['result']['is_ticket_cancellable']['cancellation_charges'] = $is_cancellable_response_data['result']['is_ticket_cancellable']['cancel_charges'];
                    $is_cancellable_response_data['result']['is_ticket_cancellable']['cancel_percent'] = $is_cancellable_response_data['result']['is_ticket_cancellable']['cancel_percent'];                    
                }
            }
            if($booking_details['booking_source'] == BARDE_BUS_BOOKING_SOURCE){
                if(isset($is_cancellable_response_data['result']['is_ticket_cancellable'])){
                    $is_cancellable_response_data['result']['is_ticket_cancellable']['is_cancellable'] = isset($is_cancellable_response_data['result']['is_ticket_cancellable']['is_cancellable'])?$is_cancellable_response_data['result']['is_ticket_cancellable']['is_cancellable']:0;    
                    $is_cancellable_response_data['result']['is_ticket_cancellable']['cancellation_charges'] = $is_cancellable_response_data['result']['is_ticket_cancellable']['cancel-charges'];
                    $is_cancellable_response_data['result']['is_ticket_cancellable']['cancel_percent'] = $is_cancellable_response_data['result']['is_ticket_cancellable']['cancel_percent'];                    
                }
            }            
            $this->CI->custom_db->generate_static_response(json_encode($is_cancellable_request));
            $this->CI->custom_db->generate_static_response(json_encode($is_cancellable_response_data));

            if (valid_array($is_cancellable_response_data) == true) {
            
                $response['data'] = $is_cancellable_response_data; //Store Data
                $response['msg'] = 'Cancellation is Failed';
                $IsCancellable2Result = $is_cancellable_response_data['result']['is_ticket_cancellable'];                
                if ($IsCancellable2Result['is_cancellable'] == '1') {                                      
                    $cancell_ticket_request = $this->cancel_ticket($cancellation_request_params, $app_reference);                    
                    if ($cancell_ticket_request['status']) {                                                
                        $cancell_ticket_response_data = $GLOBALS['CI']->api_interface->get_json_response_bitla($cancell_ticket_request['request_url'],$cancell_ticket_request['api_key'],$cancell_ticket_request['request_data']);
                        
                        // Kadri Confirm Cancellation Response
                        // $cancell_ticket_response_data = json_decode('{"result":{"cancel_ticket":{"refund_amount":675,"cancel_charges":225,"seat_numbers":"L4","cancel_seat_details":[{"cancel_seat_detail":{"seat_number":"L4","cancel-percent":25,"refund_amount":675,"cancelled_fare":225,"base_cancelled_fare":225,"cancelled_service_tax":0}}],"operator_gst_details":{"category":"unregistered","sale_type":"Inter State","trans_type":"Ecommerce","registration_name":"Kadri Travels","gst_id":"08AAECP7257R1ZJ","cgst_percentage":0,"sgst_percentage":0,"igst_percentage":5,"tcs_percentage":0,"cancellation_charges":225,"cgst_amount":0,"sgst_amount":0,"igst_amount":23.75,"tcs_amount":0}}}}', true);

                        // Gotour Confirm Cancellation Response
                        // $cancell_ticket_response_data = json_decode('{"result":{"cancel_ticket":{"refund_amount":802.31,"cancel-charges":141.59,"seat_numbers":"B1","cancel_seat_details":[{"cancel_seat_detail":{"seat_number":"B1","cancel-percent":15,"refund_amount":802.31,"cancelled_fare":141.59,"base_cancelled_fare":141.59,"cancelled_service_tax":0}}],"operator_gst_details":{"category":"unregistered","sale_type":"Inter State","trans_type":"Ecommerce","registration_name":"PARSHWANATH TRAVELS PRIVATE LIMITED","gst_id":"08AAECP7257R1ZJ","cgst_percentage":0,"sgst_percentage":0,"igst_percentage":5,"tcs_percentage":0,"cancellation_charges":141.59,"cgst_amount":0,"sgst_amount":0,"igst_amount":23.75,"tcs_amount":0}}}}', true);
                        
                        // Konduskar Confirm Cancellation Response
                        // $cancell_ticket_response_data = json_decode('{"result":{"cancel_ticket":{"refund_amount":450,"cancel-charges":25,"seat_numbers":"C5","cancel_seat_details":[{"cancel_seat_detail":{"seat_number":"C5","cancel-percent":5,"refund_amount":450,"cancelled_fare":25,"base_cancelled_fare":25,"cancelled_service_tax":0}}],"operator_gst_details":{"category":"unregistered","sale_type":"Inter State","trans_type":"Ecommerce","registration_name":"PARSHWANATH TRAVELS PRIVATE LIMITED","gst_id":"08AAECP7257R1ZJ","cgst_percentage":0,"sgst_percentage":0,"igst_percentage":5,"tcs_percentage":0,"cancellation_charges":25,"cgst_amount":0,"sgst_amount":0,"igst_amount":23.75,"tcs_amount":0}}}}', true);

                        // Barde Confirm Cancellation Response
                        // $cancell_ticket_response_data = json_decode('{"result":{"cancel_ticket":{"refund_amount":845.5,"cancel-charges":44.5,"seat_numbers":"22","cancel_seat_details":[{"cancel_seat_detail":{"seat_number":"22","cancel_percent":5,"refund_amount":845.5,"cancelled_fare":44.5,"base_cancelled_fare":44.5,"cancelled_service_tax":0}}],"operator_gst_details":{"category":"unregistered","sale_type":"Inter State","trans_type":"Ecommerce","registration_name":"PARSHWANATH TRAVELS PRIVATE LIMITED","gst_id":"08AAECP7257R1ZJ","cgst_percentage":0,"sgst_percentage":0,"igst_percentage":5,"tcs_percentage":0,"cancellation_charges":44.5,"cgst_amount":0,"sgst_amount":0,"igst_amount":23.75,"tcs_amount":0}}}}', true);

                        $this->CI->custom_db->generate_static_response(json_encode($cancell_ticket_request));
                        $this->CI->custom_db->generate_static_response(json_encode($cancell_ticket_response_data));

                        //$cancell_ticket_response_data = $GLOBALS['CI']->bus_model->get_static_response(1770);
                        if (valid_array($cancell_ticket_response_data) == true) {
                            $response['data'] = $cancell_ticket_response_data; //Store Data
                            $CancelTicket2Result = $cancell_ticket_response_data;

                            $response['status'] = SUCCESS_STATUS; //Update To Success Status
                            $response['msg'] = 'Cancellation is Success';                            
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
    function get_route_details($search_id, $route_schedule_id, $route_code, $booking_source){
        $this->CI->load->driver('cache');
        $search_data = $this->search_data($search_id,$booking_source);
        $search_hash = $this->search_hash;
        $cache_contents = $this->CI->cache->file->get($search_hash);
        
        if(!empty($cache_contents)){
            $bus_inf_data = array();
            foreach($cache_contents['result'] as $bus_data){
                if($route_schedule_id == $bus_data['RouteScheduleId'] && $route_code == $bus_data['RouteCode']){
                    $bus_inf_data = $bus_data;
                    unset($bus_inf_data['Pickups']);
                    unset($bus_inf_data['Dropoffs']);
                    unset($bus_inf_data['CancPolicy']);
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
    public function search_data($search_id, $booking_source='') {        
        $response['status'] = true;
        $response['data'] = array();        
        // if (empty($this->master_search_data) == true and valid_array($this->master_search_data) == false) { //Commented by Shrikant
            $clean_search_details = $GLOBALS['CI']->bus_model->get_safe_search_data($search_id);
            if ($clean_search_details['status'] == true) {
                $response['status'] = true;
                $response['data'] = $clean_search_details['data'];
                $response['data']['bus_date_1'] = date('Y-m-d', strtotime($clean_search_details['data']['bus_date_1']));
                if (empty($clean_search_details['data']['bus_date_2']) == false) {
                    $response['data']['bus_date_2'] = date('Y-m-d', strtotime($clean_search_details['data']['bus_date_2']));
                }
                $bus_station_list = $GLOBALS['CI']->db_cache_api->get_bus_station_list(array('k' => 'name', 'v' => 'station_id'));               
                $bitla_from_to = $this->bitla_search_data($response['data']['bus_station_from'],$response['data']['bus_station_to'],$booking_source);
                $response['data']['bus_station_from_id'] = $bitla_from_to['from_id'];
                $response['data']['bus_station_to_id'] = $bitla_from_to['to_id'];
                $this->master_search_data = $response['data'];
                if (empty($response['data']['bus_station_from_id']) == true || empty($response['data']['bus_station_to_id']) == true) {
                    $response['status'] = false;
                }
            } else {
                $response['status'] = false;
            }
        // } else { //Commented by Shrikant
        //     $response['data'] = $this->master_search_data; //Commented by Shrikant
        // } // Commented by Shrikant
        // $this->search_hash = md5(serialized_data($response['data'].BITLA_BUS_BOOKING_SOURCE)); // Commented by Shrikant
        $this->search_hash = md5(serialized_data($response['data'].$booking_source));
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
     * @param array     s$v
     * @param number    $b2b_com
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

    private function format_as_tmx_response($response_data,$search_data,$booking_source){
        $response = array();
        $response['Status'] = SUCCESS_STATUS ;

        $bus_route_details = $response_data['routes'];
        //debug($bus_route_details);die('==');

        $bus_details_array = array();
        if(is_array($bus_route_details) && !empty($bus_route_details)){              
            foreach ($bus_route_details as $k => $v__seg_details) {
                $_each_bus_details = array();
                $_each_bus_details['BusNo'] = $v__seg_details['route']['number'];
                $_each_bus_details['CompanyName'] = $v__seg_details['route']['operator_service_name'];
                $_each_bus_details['CompanyId'] = '';
                $_each_bus_details['ProvId'] = '';
                $_each_bus_details['RouteCode'] = '';
                $_each_bus_details['RouteScheduleId'] = $v__seg_details['route']['id'];
                $_each_bus_details['BusTypeName'] = $v__seg_details['route']['bus_type'];

                $_each_bus_details['BusLabel'] = $v__seg_details['route']['bus_type'];

                $d_date = $search_data['bus_date_1'];
                if($booking_source == KADRI_BUS_BOOKING_SOURCE){
                    $d_time = $v__seg_details['route']['dep_time'];
                }else if($booking_source == GOTOUR_BUS_BOOKING_SOURCE){
                    $d_time = $v__seg_details['route']['departure_time'];
                }else if($booking_source == KONDUSKAR_BUS_BOOKING_SOURCE || $booking_source == BARDE_BUS_BOOKING_SOURCE){
                    $d_time = $v__seg_details['route']['deptime'];
                }                
                $_each_bus_details['DeptTime'] = $d_time;
                //$_each_bus_details['DepartureTime'] = $d_date.' '.$d_time;
                $_each_bus_details['DepartureTime'] = $d_time; //ashwini
                if($booking_source == KADRI_BUS_BOOKING_SOURCE){
                    $_each_bus_details['ArrTime'] = $v__seg_details['route']['arrival-time'];
                    $_each_bus_details['ArrivalTime'] = $v__seg_details['route']['arrival-time'];
                }else if($booking_source == GOTOUR_BUS_BOOKING_SOURCE || $booking_source == KONDUSKAR_BUS_BOOKING_SOURCE){
                    $_each_bus_details['ArrTime'] = $v__seg_details['route']['arr-time'];
                    $_each_bus_details['ArrivalTime'] = $v__seg_details['route']['arr-time'];
                }else if($booking_source == BARDE_BUS_BOOKING_SOURCE){
                    $_each_bus_details['ArrTime'] = $v__seg_details['route']['arrtime'];
                    $_each_bus_details['ArrivalTime'] = $v__seg_details['route']['arrtime'];
                }                 
                ///////new
                $_each_bus_details['HasNAC'] = '';
                $_each_bus_details['HasAC'] = '';
                if($v__seg_details['route']['is_ac_bus'] == '1'){
                    $_each_bus_details['HasAC'] = '1';
                }else{
                    $_each_bus_details['HasNAC'] = '';
                }
                $_each_bus_details['HasSeater'] = '';
                $_each_bus_details['HasSleeper'] = '';
                $_each_bus_details['SemiSeater'] = '';
                if(strstr($v__seg_details['route']['bus_type'],'Sleeper')){
                    $_each_bus_details['HasSleeper'] = '1';
                }else if(strstr($v__seg_details['route']['bus_type'],'Semi Sleeper')){
                    $_each_bus_details['SemiSeater'] = '1';
                }else{
                    $_each_bus_details['HasSeater'] = '1';
                }
                $_each_bus_details['IsVolvo'] = '';
                $_each_bus_details['CommAmount'] = '';
                $_each_bus_details['DiscountAmt'] = '';

                $_each_bus_details['TripId'] = $v__seg_details['route']['travel_id'];
                $_each_bus_details['CompanySuf'] = '';
                $_each_bus_details['From'] = $search_data['bus_station_from'];
                $_each_bus_details['To'] = $search_data['bus_station_to'];
                $_each_bus_details['Duration'] = $v__seg_details['route']['duration'];
                
                $_each_bus_details['BusStatus'] = array(
                    'BaseFares' => array(
                            '0' =>'0',
                            '1' =>'0',
                        ),  
                    'TotalTax'=>0,
                    'CurrencyCode'=>'INR',
                );

                $fare = array();
                if($booking_source == KADRI_BUS_BOOKING_SOURCE || $booking_source == BARDE_BUS_BOOKING_SOURCE){
                    $fare_str = explode(',',$v__seg_details['route']['rate']);
                }else if($booking_source == GOTOUR_BUS_BOOKING_SOURCE || $booking_source == KONDUSKAR_BUS_BOOKING_SOURCE){
                    $fare_str = explode(',',$v__seg_details['route']['fare']);
                }                
                foreach ($fare_str as $k_fare => $v_fare) {  
                    $__p = explode(':',$v_fare);
                    array_push($fare,$__p['1']);
                }
                $_each_bus_details['Fare'] = min($fare);
                $_each_bus_details['base_fare'] = min($fare);
                $_each_bus_details['AvailableSeats'] = $v__seg_details['route']['available_seats'];
                $_each_bus_details['Max_Fare'] = max($fare);

                //pickups
                $_each_bus_details['Pickups'] = array();
                //Dropoffs
                $_each_bus_details['Dropoffs']= array();

                $_each_bus_details['CancPolicy']= array();
                if($booking_source == KADRI_BUS_BOOKING_SOURCE){
                    $_each_bus_details['is_cancellable']=$v__seg_details['route']['is_cancellable'];
                }else if($booking_source == GOTOUR_BUS_BOOKING_SOURCE){
                    $_each_bus_details['is_cancellable']=$v__seg_details['route']['is_cncl'];
                }else if($booking_source == KONDUSKAR_BUS_BOOKING_SOURCE || $booking_source == BARDE_BUS_BOOKING_SOURCE){
                    $_each_bus_details['is_cancellable']=$v__seg_details['route']['can_cancel'];
                }                 
                $_each_bus_details['BusTypeNames']= array(
                    'IsAC'=>'',
                    'Seating'=>'',
                    'Make'=>'',
                );
                $_each_bus_details['ResultToken'] = md5(json_encode($v__seg_details));
                $_each_bus_details['booking_source'] = $booking_source;
                $_each_bus_details['status'] = '1';
                $_each_bus_details['amenities'] = isset($v__seg_details['route']['amenities'])?$v__seg_details['route']['amenities']:'';

                $bus_details_array[] = $_each_bus_details;
                $response['Message'] = 'Successfull formatted...!';
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

    private function busLayout_formatting($response_data, $booking_source){        
        if(isset($response_data['service_details'])){                          
            $coach_layout = $response_data['service_details']['coach_layout'];
            $seat_details = $response_data['service_details']['coach_layout']['seat_details'];
            if($booking_source == KONDUSKAR_BUS_BOOKING_SOURCE){
                $seat_price = explode(',', $response_data['service_details']['rate']);
            }else{
                $seat_price = explode(',', $response_data['service_details']['tariff']);
            }           
            $CompanyName = $response_data['service_details']['operator_service_name'];   
            if($booking_source == KADRI_BUS_BOOKING_SOURCE){
                $is_st_applicable = $response_data['service_details']['is_service_tax_applicable'];
            }else if($booking_source == GOTOUR_BUS_BOOKING_SOURCE){
                $is_st_applicable = $response_data['service_details']['is_st_applicable'];
            }else if($booking_source == KONDUSKAR_BUS_BOOKING_SOURCE){
                $is_st_applicable = $response_data['service_details']['is_service_tax_app'];
            }else if($booking_source == BARDE_BUS_BOOKING_SOURCE){
                $is_st_applicable = $response_data['service_details']['is_stax_allowed'];
            }                     
            $service_tax_pct = $response_data['service_details']['service_tax_percent'];
            $convi_chrg_pct = $response_data['service_details']['convenience_charge_percent'];

            $stages = $response_data['service_details']['stages'];
            
            $max_row = $coach_layout['no_of_rows'];
            $max_col = $coach_layout['no_of_cols'];
            $total_seats = $coach_layout['total_seats'];
            $avil_seats = $coach_layout['available_seats'];

            $seat_result = array();
            $seq_no = 0;
            foreach ($seat_details as $k_seat => $v_seat) {
                if($v_seat['seat']['is_seat'] == true){
                    $base_fare = 0;
                    foreach ($seat_price as $k_st_price => $v_st_price) {
                        $__s_value = explode(':', $v_st_price);
                        if($__s_value[0] == $v_seat['seat']['type']){
                            $base_fare = $__s_value[1];
                        }
                    }
                    $service_tax = 0;
                    if(($is_st_applicable == true && $service_tax_pct > 0)){
                        $service_tax = ($base_fare * $service_tax_pct)/100;
                    }                    
                    $width = 0;
                    $length = 0;
                    $zIndex = 0;
                    if($v_seat['seat']['type'] == "SS")
                    {
                        $width = 1;
                        $length = 1;
                        $zIndex = 0;
                    }else if($v_seat['seat']['type'] == 'SUB' || $v_seat['seat']['type'] == 'DUB' || $v_seat['seat']['type'] == 'UB'){

                        if(strstr($response_data['service_details']['bus_type'],'1+1')){
                            $width = 2;
                            $length = 1;
                        }else{
                            $width = 1;
                            $length = 2;
                        }
                        $zIndex = 1;

                    }else if($v_seat['seat']['type'] == 'SLB' || $v_seat['seat']['type'] == 'DLB' || $v_seat['seat']['type'] == 'LB'){

                        if(strstr($response_data['service_details']['bus_type'],'1+1')){
                            $width = 2;
                            $length = 1;
                        }else{
                            $width = 1;
                            $length = 2;
                        }
                        $zIndex = 0;

                    }else{
                        $width = 1;
                        $length = 1;
                        $zIndex = 0;
                    }

                    $decks = '';
                    if($zIndex > 0){
                        $decks = 'Upper';
                    }else{
                        $decks = 'Lower';
                    }

                    ///debug($v_seat['seat']);die('1111111');
                    $status = '1';
                    $is_available = 0;
                    if($v_seat['seat']['available'] == true){
                        $is_available = '1';
                        if($v_seat['seat']['is_reserved_for_ladies'] == true){
                        $status = '3';
                        }else if($v_seat['seat']['is_reserved_for_gents'] == true){
                            $status = '2';
                        }else{
                            $status = '1';
                        }
                    }else{
                        if($v_seat['seat']['is_ladies_seat'] == true){
                            $is_available = '0';
                            $status = '-3';
                        }else{
                            $is_available = '0';
                            $status = '-2';
                        }
                    }

                    
                    $each_seat_details = array(
                        'seq_no' => $seq_no++,
                        'row' => $v_seat['seat']['row_id'],
                        'col' => $v_seat['seat']['col_id'],
                        'width' => $width,
                        'height' => $length,
                        'seat_type' => $v_seat['seat']['type'],
                        'seat_no' => $v_seat['seat']['number'],
                        'seat_id' => '',
                        'total_fare' => ($base_fare + $service_tax),
                        'base_fare' => $base_fare,
                        'status' => $status,
                        'decks' => $decks,
                        'MaxRows' => $max_row,
                        'MaxCols' => $max_col,
                        'IsAvailable' => $is_available,
                        'service_tax' => $service_tax,
                        'trans_fare' => 0,
                        'CompanyName' => $CompanyName,
                        'TripId' => $response_data['service_details']['travel_id'],
                    );                 
                    $seat_result[] = $each_seat_details;
                }
            }

            $layout = array(
                'MaxRows' => $max_row,
                'MaxCols' => $max_col,
            );

            //**Pickups And Dropoffs**//
            $boarding_stage = array();
            $dropping_stage = array();
            foreach ($stages as $k_stages => $v_stages) {
                if($v_stages['stage']['type'] == 'Boarding at'){
                    $list_pickup = array();
                    
                    $list_pickup['PickupCrossed'] = $v_stages['stage']['name'];
                    $list_pickup['Contact'] = $v_stages['stage']['contact_numbers'];
                    $list_pickup['Landmark'] = $v_stages['stage']['landmark'];
                    $list_pickup['Address'] = $v_stages['stage']['address'];
                    $list_pickup['PickupTime'] = $v_stages['stage']['time'];
                    $list_pickup['PickupArea'] = $v_stages['stage']['ref_stage_name'];
                    $list_pickup['PickupName'] = $v_stages['stage']['name'];
                    $list_pickup['PickupCode'] = $v_stages['stage']['id'];

                    $boarding_stage[] = $list_pickup;
                }else{
                    $list_dropoffs = array();
                    
                    $list_dropoffs['DropoffTime'] = $v_stages['stage']['time'];
                    $list_dropoffs['DropoffName'] = $v_stages['stage']['name'];
                    $list_dropoffs['DropoffCode'] = $v_stages['stage']['id'];

                    $dropping_stage[] = $list_dropoffs;
                }
            }            
            $cancellation_policy_request = $this->cancellationPolicy($booking_source);
            $response_data = $GLOBALS['CI']->api_interface->get_json_response_bitla($cancellation_policy_request['request_url'],$cancellation_policy_request['api_key']);                                                        

            // Kadri Cancellation Policy
            // $response_data = json_decode('{"cancellation_policies":[[0,5,100],[5,12,50],[12,24,30],[24,168,30],[168,360,25],[360,720,20]]}',true);                                                        
            
            // Gotour Cancellation Policy
            // $response_data = json_decode('{"cancellation_policies":[[0,12,100],[12,24,75],[24,720,15]]}',true);                                                        

            // Konduskar Cancellation Policy
            // $response_data = json_decode('{"cancellation_policies":[[0,8,100],[8,24,20],[24,720,20]]}',true);                                                        
            
            // Barde Cancellation Policy
            // $response_data = json_decode('{"cancellation_policies":[[0,12,100],[12,24,50],[24,72,20],[72,720,15]]}',true);                                                        
            
            $canccel_policy = $this->format_cancellation_policy($response_data, $booking_source);
            $response['SeatLayout']['result']['value'] = $seat_result;
            $response['SeatLayout']['result']['layout'] = $layout;
            $response['SeatLayout']['result']['Pickups'] = $boarding_stage;
            $response['SeatLayout']['result']['Dropoffs'] = $dropping_stage;

            // $c__P = array();
            // $c__P['Pct'] = 100;
            // $c__P['Mins'] = '0 - 300';                            
            // $Canc[] = $c__P;
            // $c__P = array();
            // $c__P['Pct'] = 50;
            // $c__P['Mins'] = '300 - 720';                            
            // $Canc[] = $c__P;
            // $c__P = array();
            // $c__P['Pct'] = 30;
            // $c__P['Mins'] = '720 - 10080';                            
            // $Canc[] = $c__P;
            // $c__P = array();
            // $c__P['Pct'] = 25;
            // $c__P['Mins'] = '10080 - 21600';                            
            // $Canc[] = $c__P;
            // $c__P = array();
            // $c__P['Pct'] = 20;
            // $c__P['Mins'] = '21600 - 43200';                            
            // $Canc[] = $c__P;

            $response['SeatLayout']['result']['Canc'] = $canccel_policy;
            $response['SeatLayout']['result']['seat_type']['seat_type'] =array();
            $response['SeatLayout']['result']['seats']['seats']=array();
            $response['SeatLayout']['result']['status']['status']=array();
            $response['SeatLayout']['result']['total_fare']['total_fare']=array();
            $response['SeatLayout']['result']['base_fare']['base_fare']=array();
            $response['status'] = SUCCESS_STATUS;
        
        }else{
            $response['status'] = FAILURE_STATUS;
            $response['message'] = $response_data['response']['message'];
        }
        return $response;
    }

    private function Tentative_Booking_request($block_params){
        /*debug($block_params['billing_email']);
        die('new');*/
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
            $seat_detail['seat_number'] = strval($k_seat);
            //fare changed Fare to base_fare
            //$seat_detail['fare'] =  $v_seat['Fare'];
            $seat_detail['fare'] =  strval($v_seat['base_fare']);
            // $seat_detail['fare'] =  $v_seat['base_fare'] + $v_seat['_ServiceTax'];

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
        //134,506 static data for test
        $bitla_from_to_id = $this->bitla_search_data($block_params['token']['departure_from'],$block_params['token']['arrival_to'],$block_params['booking_source']);
        $JourneyDate = $JourneyDate[0];
        $from_id = $bitla_from_to_id['from_id'];
        $to_id = $bitla_from_to_id['to_id'];

        $request_data = array(); $api_key = ''; $url = ''; 
        if($block_params['booking_source'] == KADRI_BUS_BOOKING_SOURCE){
            $request_data['book_ticket'] = $book_ticket;
            $request_data['from_city_id'] = $from_id;//$block_params['token']['Form_id'];
            $request_data['to_city_id'] = $to_id; //$block_params['token']['To_id'];
            $request_data['pick_up_stage'] = $block_params['token']['PickUpID'];
            $request_data['sel-seats-count'] = $count;
            $request_data['doj'] = $JourneyDate;

            $api_credentials = $this->set_api_credentials($block_params['booking_source']);            
            $api_key = $api_credentials['api_key'];
            $this->Url = $api_credentials['api_url'];             
            $url = $this->Url.'dir/api/tentative_booking/'.$block_params['token']['RouteScheduleId'].'.json';
        }
        if($block_params['booking_source'] == GOTOUR_BUS_BOOKING_SOURCE){
            $request_data['book_ticket'] = $book_ticket;
            $request_data['origin-id'] = $from_id;//$block_params['token']['Form_id'];
            $request_data['destinationid'] = $to_id; //$block_params['token']['To_id'];
            $request_data['pickup_stage'] = $block_params['token']['PickUpID'];
            $request_data['boarding_at'] = $block_params['token']['PickUpID'];
            $request_data['drop_of'] = $block_params['token']['DropID'];
            $request_data['total_seats'] = $count;
            $request_data['journey_date'] = $JourneyDate;

            $api_credentials = $this->set_api_credentials($block_params['booking_source']);            
            $api_key = $api_credentials['api_key'];
            $this->Url = $api_credentials['api_url'];             
            $url = $this->Url.'dir/api/tentative_booking/'.$block_params['token']['RouteScheduleId'].'.json';
        } 
        if($block_params['booking_source'] == KONDUSKAR_BUS_BOOKING_SOURCE){
            $request_data['book_ticket'] = $book_ticket;
            $request_data['from-city'] = $from_id;//$block_params['token']['Form_id'];
            $request_data['to_city_id'] = $to_id; //$block_params['token']['To_id'];
            $request_data['boarding-at'] = $block_params['token']['PickUpID'];
            $request_data['boarding_at'] = $block_params['token']['PickUpID'];
            $request_data['drop_of'] = $block_params['token']['DropID'];
            $request_data['total_seats'] = $count;
            $request_data['traveldate'] = $JourneyDate;

            $api_credentials = $this->set_api_credentials($block_params['booking_source']);            
            $api_key = $api_credentials['api_key'];
            $this->Url = $api_credentials['api_url'];             
            $url = $this->Url.'dir/api/tentative_booking/'.$block_params['token']['RouteScheduleId'].'.json';
        }
        if($block_params['booking_source'] == BARDE_BUS_BOOKING_SOURCE){
            $request_data['book_ticket'] = $book_ticket;
            $request_data['from_city_id'] = $from_id;//$block_params['token']['Form_id'];
            $request_data['destination_id'] = $to_id; //$block_params['token']['To_id'];
            $request_data['boarding_at'] = $block_params['token']['PickUpID'];
            $request_data['drop-of'] = $block_params['token']['DropID'];
            $request_data['total-seats'] = $count;
            $request_data['trvldate'] = $JourneyDate;

            $api_credentials = $this->set_api_credentials($block_params['booking_source']);            
            $api_key = $api_credentials['api_key'];
            $this->Url = $api_credentials['api_url'];             
            $url = $this->Url.'dir/api/tentative_booking/'.$block_params['token']['RouteScheduleId'].'.json';
        }        
        $response['data'] = json_encode($request_data);
        $response['status'] = SUCCESS_STATUS ;
        $response['url'] = $url;       
        $response['api_key'] = $api_key;
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

    public function format_booking_details($booking, $temp_booking){

        $response = array();
        $result = array();        
        $ticket_details = $booking['data']['result']['result']['ticket_details'];              
            $result['IsCancelled'] = '';
            $result['RefundAmount'] = 0;
            $result['travel_id'] = $ticket_details['travel_id'];
            $TotalSeats = 0;

            $result['TotalFare'] = $ticket_details['total_fare'];
            if($temp_booking['booking_source'] == KADRI_BUS_BOOKING_SOURCE){
                $result['TotalSeats'] = $ticket_details['total-seats-count'];
                $TotalSeats = $ticket_details['total-seats-count'];
            }
            if($temp_booking['booking_source'] == GOTOUR_BUS_BOOKING_SOURCE || $temp_booking['booking_source'] == KONDUSKAR_BUS_BOOKING_SOURCE || $temp_booking['booking_source'] == BARDE_BUS_BOOKING_SOURCE){
                $result['TotalSeats'] = $ticket_details['no-of-seats'];
                $TotalSeats = $ticket_details['no-of-seats'];
            }            

            $picks = $ticket_details['boarding_point_details'];
            $PickupInfo = array(
                'PickupTime' => $picks['dep_time'],
                'Address' => $picks['boarding_stage_address'], 
                'Phone' => $picks['contact_numbers'],
                'Landmark' => $picks['landmark'],
                'PickupName' => $picks['name']
            );
            $result['PickupInfo']=$PickupInfo;            
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
                $s__d['IsAcSeat'] = isset($value['seat_detail']['IsAcSeat'])?$value['seat_detail']['IsAcSeat']:0;;
                // $s__d['SeatType'] = $value['seat_detail']['seat_type'];
                $s__d['SeatType'] = isset($value['seat_detail']['seat_type'])?$value['seat_detail']['seat_type']:'';
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
            $result['BusTypeName'] = $ticket_details['bus_type'];

            $result['DepartureDateTime'] = $temp_booking['book_attributes']['token']['DepartureTime'];
            $result['ArrivalDateTime'] = $temp_booking['book_attributes']['token']['ArrivalTime'];
            $result['JourneyDate'] = $temp_booking['book_attributes']['token']['JourneyDate'];
            $result['ToCityName'] = $temp_booking['book_attributes']['token']['arrival_to'];
            $result['FromCityName'] = $temp_booking['book_attributes']['token']['departure_from'];

            $result['CompanyName'] = $ticket_details['travels'];
            $result['TicketNo'] = $ticket_details['ticket_number'];
            if($temp_booking['booking_source'] == KADRI_BUS_BOOKING_SOURCE){
                $result['seat_id'] = $ticket_details['seat-nos'];
            }
            if($temp_booking['booking_source'] == GOTOUR_BUS_BOOKING_SOURCE || $temp_booking['booking_source'] == KONDUSKAR_BUS_BOOKING_SOURCE){
                $result['seat_id'] = $ticket_details['seat_numbers'];
            }            
            $result['PNRNo'] = $ticket_details['operator_pnr'];

            $t_inner_details = array();
            foreach ($ticket_details['seat_fare_details'] as $key => $value ) {
                $seat_fare_details = array(); 

                $seat_fare_details['seat_detail']['tieup_agent_commission_percentage']=isset($value['seat_detail']['tieup_agent_commission_percentage'])?$value['seat_detail']['tieup_agent_commission_percentage']:'';
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
            return $response;
    }

    private function bitla_search_data($from_name,$to_name,$booking_source){
        $from_id = '';
        $to_id = '';
        if($booking_source == KADRI_BUS_BOOKING_SOURCE){
            $bus_station_from_id = $GLOBALS['CI']->custom_db->single_table_records('bus_stations_new1','kadri_city_id', array('ets_city_name' => $from_name));
            $bus_station_to_id = $GLOBALS['CI']->custom_db->single_table_records('bus_stations_new1','kadri_city_id', array('ets_city_name' => $to_name));
            $from_id = $bus_station_from_id['data'][0]['kadri_city_id'];
            $to_id = $bus_station_to_id['data'][0]['kadri_city_id'];
        }else if($booking_source == GOTOUR_BUS_BOOKING_SOURCE){
            $bus_station_from_id = $GLOBALS['CI']->custom_db->single_table_records('bus_stations_new1','gotour_city_id', array('ets_city_name' => $from_name));
            $bus_station_to_id = $GLOBALS['CI']->custom_db->single_table_records('bus_stations_new1','gotour_city_id', array('ets_city_name' => $to_name));
            $from_id = $bus_station_from_id['data'][0]['gotour_city_id'];
            $to_id = $bus_station_to_id['data'][0]['gotour_city_id'];
        }else if($booking_source == KONDUSKAR_BUS_BOOKING_SOURCE){
            $bus_station_from_id = $GLOBALS['CI']->custom_db->single_table_records('bus_stations_new1','konduskar_city_id', array('ets_city_name' => $from_name));
            $bus_station_to_id = $GLOBALS['CI']->custom_db->single_table_records('bus_stations_new1','konduskar_city_id', array('ets_city_name' => $to_name));
             $from_id = $bus_station_from_id['data'][0]['konduskar_city_id'];
            $to_id = $bus_station_to_id['data'][0]['konduskar_city_id'];
        }else if($booking_source == BARDE_BUS_BOOKING_SOURCE){
            $bus_station_from_id = $GLOBALS['CI']->custom_db->single_table_records('bus_stations_new1','barde_city_id', array('ets_city_name' => $from_name));
            $bus_station_to_id = $GLOBALS['CI']->custom_db->single_table_records('bus_stations_new1','barde_city_id', array('ets_city_name' => $to_name));
            $from_id = $bus_station_from_id['data'][0]['barde_city_id'];
            $to_id = $bus_station_to_id['data'][0]['barde_city_id'];
        }      
        
        $response['status'] = SUCCESS_STATUS;
        $response['from_id']=$from_id;
        $response['to_id']=$to_id;
        return $response;
    }

    public function cancel_details_format($cancellation_details){

        $response = array();
        $response['status'] = SUCCESS_STATUS;                     
        $cancel_details = $cancellation_details['data']['result']['cancel_ticket'];
        if($cancellation_details['booking_source'] == KADRI_BUS_BOOKING_SOURCE){
            $ChargeAmt = $cancel_details['cancel_charges'];
            $total_paid_by_agent = $cancel_details['refund_amount']+$cancel_details['cancel_charges'];
            $ChargePct = ($ChargeAmt*100)/$total_paid_by_agent;
            $actual_refund_to_agent = $cancel_details['refund_amount']+$cancellation_details['admin_markup'];
            $data['CancelSeats'] = array(
                    'ApiRefundAmount' =>$cancel_details['refund_amount'],
                    'RefundAmount' =>$actual_refund_to_agent,
                    'ChargeAmt' => $cancel_details['cancel_charges'],
                    'TotalFare' => $cancel_details['refund_amount'],
                    'ChargePct' => $ChargePct,
                    'supp_commission_reversed' => $cancellation_details["data"]["supp_commission_reversed"],
                    'NewHoldId' => '',
                    'NewTotalFare' => '',
                );
        }
        if($cancellation_details['booking_source'] == GOTOUR_BUS_BOOKING_SOURCE || $cancellation_details['booking_source'] == KONDUSKAR_BUS_BOOKING_SOURCE || $cancellation_details['booking_source'] == BARDE_BUS_BOOKING_SOURCE){
            $ChargeAmt = $cancel_details['cancel-charges'];
            $total_paid_by_agent = $cancel_details['refund_amount']+$cancel_details['cancel-charges'];
            $ChargePct = ($ChargeAmt*100)/$total_paid_by_agent;
            $actual_refund_to_agent = $cancel_details['refund_amount']+$cancellation_details['admin_markup'];
            $data['CancelSeats'] = array(
                    'ApiRefundAmount' =>$cancel_details['refund_amount'],
                    'RefundAmount' =>$actual_refund_to_agent,
                    'ChargeAmt' => $cancel_details['cancel-charges'],
                    'TotalFare' => $cancel_details['refund_amount'],
                    'ChargePct' => $ChargePct,
                    'supp_commission_reversed' => $cancellation_details["data"]["supp_commission_reversed"],
                    'NewHoldId' => '',
                    'NewTotalFare' => '',
                );
        }                
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

    private function cancellationPolicy($booking_source){        

        $api_credentials = $this->set_api_credentials($booking_source);            
        $api_key = $api_credentials['api_key'];
        $this->Url = $api_credentials['api_url']; 
        $url = $this->Url.'dir/api/cancellation_policies.json';
                                 
        $response['status'] = SUCCESS_STATUS;
        $response['request_url'] = $url;
        $response['api_key'] = $api_key;

        return $response;
    }

    private function format_cancellation_policy($response, $booking_source)
    {
        $cancellation_response = array();
        if(isset($response['cancellation_policies'])){
            foreach ($response['cancellation_policies'] as $c_key => $c_value) {
                $c__P = array();
                $c__P['Pct'] = $c_value[2];
                $c__P['Mins'] = $c_value[0].' - '.$c_value[1];  
                $cancellation_response[] = $c__P;
            }
        }
        return $cancellation_response;
    }
}