<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once BASEPATH . 'libraries/Common_Api_Grind.php';

class Infinity_private extends Common_Api_Grind {

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
        $this->set_api_credentials();
    }

     private function set_api_credentials() {
        $bus_engine_system = $this->CI->bus_engine_system;
        $this->system = $bus_engine_system;
        $credentials = $this->CI->custom_db->single_table_records("supplier_credentials", "config", array("booking_source" => INFINITY_BUS_BOOKING_SOURCE, "mode"=>$bus_engine_system));
        $this->details = json_decode($credentials["data"][0]["config"], true);
        //$this->details = $this->CI->config->item ('vrl_bus_'.$bus_engine_system);
        $this->Url = $this->details ['api_url'];
        $this->VerifyCall = $this->details ['verify_call'];
        $this->Password = $this->details ['test_password'];

        //$source_request = $this->infinityGetSources();        
        //$this->ClientId = $this->details ['domain_key'];
    }

    private function execute_curl($request)
    {
        $url = $request['url'];
        $message = $request['request'];
        $soap_do = curl_init ($url);
        $header = array(
        "Content-Type: text/xml",        
        "Content-length: ".strlen($message),
        ); 
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false); 
        curl_setopt($soap_do, CURLOPT_POST, true );
        curl_setopt($soap_do, CURLOPT_POSTFIELDS, $message); 
        curl_setopt($soap_do, CURLOPT_HTTPHEADER, $header);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true); 
     
        $return = curl_exec($soap_do);
        $curl_info = curl_getinfo($soap_do);
        return $return;
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
    private function bus_search_request($bus_station_from_id, $bus_station_to_id, $bus_date, $number_of_seats = 1, $SearchId = 123456) {

        $response['status'] = SUCCESS_STATUS;
        $response['data'] = array();
        $verify_call = $this->VerifyCall; 
        $api_url  = $this->Url;
        $value=array($bus_station_from_id,$bus_station_to_id,$bus_date,$verify_call);
        $replace=array('[sourceid]','[destinationid]','[doj]','[verifycall]');

        $message = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
          <soap:Body>
            <GetAvailableRoutes xmlns="http://platformapi.itspl.net/">
             <FromID>[sourceid]</FromID>
             <ToID>[destinationid]</ToID>
             <JourneyDate>[doj]</JourneyDate>
             <VerifyCall>[verifycall]</VerifyCall>
            </GetAvailableRoutes>
          </soap:Body>
        </soap:Envelope>';
        $xml_req = str_replace($replace,$value,$message);   

        $request['status'] = SUCCESS_STATUS;
        $request['request'] = $xml_req;        
        $request['soap_action'] = '';
        $request['url'] = $api_url;
        return $request;
    }

    /**
     *
     */
    function GetRouteScheduleDetailsWithComm_request($route_schedule_id, $journey_date, $route_code, $ResultToken, $booking_source, $RefNumber) {
        $response['status'] = SUCCESS_STATUS;
        $response['data'] = array();
        $verify_call = $this->VerifyCall; 
        $api_url  = $this->Url;
        //GetCoachLayoutV3

        $value = array($RefNumber,$verify_call);
        $replace = array('[referenceno]','[verifycall]');

        $message = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                <GetSeatArrangementDetailsV3 xmlns="http://platformapi.itspl.net/">
                  <ReferenceNumber>[referenceno]</ReferenceNumber>
                  <VerifyCall>[verifycall]</VerifyCall>
                </GetSeatArrangementDetailsV3>
            </soap:Body>
        </soap:Envelope>';
        $xml_req = str_replace($replace,$value,$message);   

        $request['status'] = SUCCESS_STATUS;
        $request['request'] = $xml_req;
        $request['soap_action'] = '';
        $request['url'] = $api_url;
        //debug($request);die('456');
        return $request;    
    }

    private function cancellation_policy_request($company_id) {

        $response['status'] = SUCCESS_STATUS;
        $response['data'] = array();
        $verify_call = $this->VerifyCall; 
        $api_url  = $this->Url;
        
        $value = array($company_id,$verify_call);
        $replace = array('[companyid]','[verifycall]');

        $message = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <soap:Body>
                <GetCancellationPolicy xmlns="http://platformapi.itspl.net/">
                  <CompanyID>[companyid]</CompanyID>
                  <VerifyCall>[verifycall]</VerifyCall>
                </GetCancellationPolicy>
            </soap:Body>
        </soap:Envelope>';

        $xml_req = str_replace($replace,$value,$message);

        $request['status'] = SUCCESS_STATUS;
        $request['request'] = $xml_req;        
        $request['soap_action'] = '';
        $request['url'] = $api_url;
        return $request;
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
        
        $request= array();
        $verify_call = $this->VerifyCall; 
        $api_url  = $this->Url;

        if(!empty($booking_params)){
            //36,38 city id for test
            $infinity_from_to_id = $this->get_infinity_city_id($booking_params['token']['departure_from'],$booking_params['token']['arrival_to']);
            
            $reference_number = $booking_params['token']['RouteScheduleId'];
            $passenger_name = $booking_params['contact_name'][0];
            $pickup_id = $booking_params['token']['PickUpID'];
            $drop_id = $booking_params['token']['PickUpID'];
            $payble_amount = $booking_params['token']['fare']['Fare'];
            $customer_email = $booking_params['billing_email']; 
            $customer_phone = $booking_params['passenger_contact']; 
            $block_id = $booking_params['block_key']; 

            $seats_ar = array();
            foreach ($booking_params['token']['seat_attr']['seats'] as $key => $value) {
                array_push($seats_ar, $value['seat_id']); 
            }

            $passenger_count = count($booking_params['gender']);
            $seat_name_and_gender = ''; $seat_details = '';
            for ($pax_count = 0; $pax_count < $passenger_count ; $pax_count++) { 
                $gender = '';
                if($booking_params['gender'][$pax_count] == 1){
                    $gender = 'M';
                }else{
                    $gender = 'F';
                }
                $seat_name_and_gender .= $seats_ar[$pax_count].','.$gender.'|';  
                $seat_details .= $seats_ar[$pax_count].','.$booking_params['contact_name'][$pax_count].','.$customer_phone.','.$booking_params['age'][$pax_count].'|';              
            }
            $seat_name_and_gender = rtrim($seat_name_and_gender, '|'); 
            $seat_details = rtrim($seat_details, '|');                    
            
            $value=array($reference_number,$passenger_name,$seat_name_and_gender,$customer_email,$customer_phone,$pickup_id,$payble_amount,$passenger_count,$verify_call,$drop_id,$seat_details,$block_id);
            $replace=array('[referenceno]','[passengername]','[seatnames]','[email]','[contactno]','[pickupid]','[amount]','[seat]','[verifycall]','[dropid]','[seatdetails]','[blockid]');
            $message = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
              <soap:Body>
                <BookSeatV3 xmlns="http://platformapi.itspl.net/">
                  <BookSeat>
                    <ReferenceNumber>[referenceno]</ReferenceNumber>
                    <PassengerName>[passengername]</PassengerName>
                    <SeatNames>[seatnames]</SeatNames>
                    <Email>[email]</Email>
                    <Phone>[contactno]</Phone>
                    <PickUpID>[pickupid]</PickUpID>
                    <PayableAmount>[amount]</PayableAmount>
                    <TotalPassengers>[seat]</TotalPassengers>
                    <VerifyCall>[verifycall]</VerifyCall>
                    <DropID>[dropid]</DropID>
                    <Discount>0</Discount>
                    <SeatDetails>[seatdetails]</SeatDetails>
                    <GSTState>0</GSTState>
                    <GSTCompanyName></GSTCompanyName>
                    <GSTRegNo></GSTRegNo>
                    <APIPNRNo>[blockid]</APIPNRNo>
                  </BookSeat>
                </BookSeatV3>
              </soap:Body>
            </soap:Envelope>';
        
            $xml_req = str_replace($replace,$value,$message);

            $request['status'] = SUCCESS_STATUS;
            $request['request'] = $xml_req;
            $request['soap_action'] = '';
            $request['url'] = $api_url;
            return $request;           
        }
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

    function cancellation_request_params($booking_details, $app_reference,$seat_to_cancel ="") {
         /*debug($booking_details);
         debug($app_reference);
         debug($seat_to_cancel);
         exit('[][]');*/
        //debug(explode(',', $booking_details['PNRNo']));die('++');
        
        $arr_one = explode(',', $booking_details['PNRNo']);
        $arr_two = explode(',', $seat_to_cancel);
        $rtn_pnr = array_diff($arr_one, $arr_two);
        /*debug($ar);
        die('=========');
        $rtn_pnr = array();
        foreach (explode(',', $booking_details['PNRNo']) as $key => $value) {
            foreach (explode(',', $seat_to_cancel) as $k_pnr => $v_pnr) {
                if(trim($value) != trim($v_pnr)){
                   array_push($rtn_pnr, $value);
                }
            }
        }*/
        //debug($rtn_pnr);die();
        $cancellation_request_params['request_data'] = array();
        //$cancellation_request_params['request_data']['PNRNo'] = $booking_details['PNRNo'];
        //debug($seat_to_cancel);die();
        if(empty($seat_to_cancel)){
            $cancellation_request_params['request_data']['PNRNo'] = $booking_details['PNRNo'];
            $cancellation_request_params['request_data']['rtn_pnr'] = array();
        }else{
             $cancellation_request_params['request_data']['PNRNo'] = $seat_to_cancel;
             $cancellation_request_params['request_data']['rtn_pnr'] = implode(',', $rtn_pnr);
        }
        $cancellation_request_params['request_data']['TicketNo'] = $booking_details['TicketNo'];
        $cancellation_request_params['request_data']['SeatNos'] = $booking_details['SeatNos'];
        $cancellation_request_params['request_data']['booking_source'] = $booking_details['booking_source'];
        $cancellation_request_params['request_data']['AppReference'] = $app_reference;
        //debug($cancellation_request_params);die('==++');
        return $cancellation_request_params;
    }

    /*
     * Balu A
     * Checks whether ticket is elgible for Cancellation or not
     * API MethodName: IsCancellable2
     */

    function is_cancellable($cancellation_request_params) {

        $request_data = $cancellation_request_params['request_data'];
        $response = array();
        $verify_call = $this->VerifyCall; 
        $api_url  = $this->Url;
        $response['status'] = SUCCESS_STATUS;
        $PNRNo = $request_data['TicketNo'];
        
        $value=array($PNRNo,$verify_call);
        $replace=array('[pnr]','[verifycall]');

        $message = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
          <soap:Body>
            <CancelDetails xmlns="http://platformapi.itspl.net/">
              <PNRNo>[pnr]</PNRNo>
              <VerifyCall>[verifycall]</VerifyCall>
            </CancelDetails>
          </soap:Body>
        </soap:Envelope>';

        $xml_req = str_replace($replace,$value,$message);        

        $response['request'] = $xml_req;
        $response['soap_action'] = '';
        $response['url'] = $api_url;        
        return $response;
    }


    /*
     * Balu A
     * Cancell the Ticket(Complete Booking Cancel)
     * API MethodName: CancelTicket2
     */
    function cancel_ticket($cancellation_request_params) {

        $request_data = $cancellation_request_params['request_data'];
        $response = array();
        $verify_call = $this->VerifyCall; 
        $api_url  = $this->Url;
        $response['status'] = SUCCESS_STATUS;
        $PNRNo = $request_data['TicketNo'];
        
        $value=array($PNRNo,$verify_call);
        $replace=array('[pnr]','[verifycall]');

        $message = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
          <soap:Body>
            <ConfirmCancellation xmlns="http://platformapi.itspl.net/">
              <PNRNo>[pnr]</PNRNo>
              <VerifyCall>[verifycall]</VerifyCall>
            </ConfirmCancellation>
          </soap:Body>
        </soap:Envelope>';

        $xml_req = str_replace($replace,$value,$message);        

        $response['request'] = $xml_req;
        $response['soap_action'] = '';
        $response['url'] = $api_url;        
        return $response;
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
        //Cache
        $cache_search = $this->CI->config->item('cache_bus_search');
        $search_hash = $this->search_hash;
        $header_info = $this->get_header();

        if ($cache_search) {
            $cache_contents = $this->CI->cache->file->get($search_hash);
        }
        $cache_contents = "";
        //die($search_hash);
        if ($search_data['status'] == true) {

            if ($cache_search === false || ($cache_search === true && empty($cache_contents) == true)) {
                $request = $this->bus_search_request($search_data['data']['bus_station_from_id'],$search_data['data']['bus_station_to_id'], $search_data['data']['bus_date_1'], 1, $search_id);
                if ($request['status']) {

                    $response_data1 = $this->execute_curl($request);
                    // $response_data1 = file_get_contents('/var/www/html/system/libraries/bus/infinity_bus_search_resp.xml');
                    $response1 = str_replace("<soap:Body>","",$response_data1);
                    $response2 = str_replace("</soap:Body>","",$response1);     
                    $xml_string1 = preg_replace('/xs:/', '', $response2);       
                    $xml_string2 = preg_replace('/diffgr:/', '', $xml_string1); 
                    $xml = new \SimpleXMLElement($xml_string2);
                    $encode_search_resp = json_encode($xml);
                    $response_data = json_decode($encode_search_resp, true);
                    if ($this->valid_search_result($response_data)) {
                        //Format response like tmx bus fromat
                        $formated_response =$this->format_as_tmx_response($response_data,$search_data['data']);                        
                        $response['data']['result'] = $formated_response['Search'];                        
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
        //debug($response); exit("I am Here");
        return $response;
    }
    /**
     * @param string    $route_schedule_id
     * @param datetime  $journey_date
     */
    function get_bus_details($route_schedule_id, $journey_date, $route_code, $ResultToken, $booking_source, $RefNumber, $company_name) {

        $response['data'] = array();
        $response['status'] = true;
        // echo "<pre>";print_r($route_schedule_id);die;
        if (!empty($route_schedule_id)) {            
            //get request
            $header_info = $this->get_header();
            $request = $this->GetRouteScheduleDetailsWithComm_request($route_schedule_id, $journey_date, $route_code, $ResultToken, $booking_source, $RefNumber);
            
            //get data
            if ($request['status']) {                
                $seat_response_data = $this->execute_curl($request);
                // $seat_response_data = file_get_contents('/var/www/html/system/libraries/bus/infinity_bus_seat_resp.xml');
                
                $response1 = str_replace("<soap:Body>","",$seat_response_data);
                $response2 = str_replace("</soap:Body>","",$response1);   
                $xml_string1 = preg_replace('/xs:/', '', $response2);       
                $xml_string2 = preg_replace('/diffgr:/', '', $xml_string1);

                $xml = new \SimpleXMLElement($xml_string2);   
                $encode_seat_resp = json_encode($xml);
                $response_data = json_decode($encode_seat_resp, true);
                $formatted_seat_map_data = $this->busLayout_formatting($response_data, $company_name);
                
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
            //get request
            $request = $this->Tentative_Booking_request($block_params);   

            $header_info = $this->get_header();
            if ($request['status']) {
                $response_data = $this->execute_curl($request);  
                
                // $response_data = file_get_contents('/var/www/html/system/libraries/bus/infinity_blockseat_resp.xml');           
                
                $response1 = str_replace("<soap:Body>","",$response_data);
                $response2 = str_replace("</soap:Body>","",$response1);
                $xml_string1 = preg_replace('/xs:/', '', $response2);       
                $xml_string2 = preg_replace('/diffgr:/', '', $xml_string1);     

                // convertingc to XML
                $xml = new \SimpleXMLElement($xml_string2);  
                $encode_block_resp = json_encode($xml);
                $response_data = json_decode($encode_block_resp, true);                              
                ///formatting
                $response_data = $this->format_ValidateSeat($block_params,$response_data);
                //debug($response_data);exit('111222');
                if (valid_array($response_data) == true && $response_data['Status'] == true) {
                    $response['data']['result'] = $response_data['HoldSeatsForSchedule'];
                } else {
                    $response['status'] = false;
                    $response['msg'] = $response_data['Message'];
                }
            } else {
                $response['status'] = false;
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
        echo "<pre>book_id INFINITY===== ";print_r($book_id);
        echo "<pre>booking_params INFINITY===== ";print_r($booking_params); die;
        $response['data'] = array();
        $response['status'] = SUCCESS_STATUS;
        $resposne['msg'] = 'Remote IO Error';
        if (valid_array($booking_params)) {
            // echo 'test';exit;
            //get request
            $request = $this->BookSeats_request($booking_params, $booking_params['booking_source'], $book_id);
            //debug($request);//die('789');
            $header_info = $this->get_header();
            //get data
            if ($request['status']) {

                $response_data = $this->execute_curl($request);                     
                // $response_data = file_get_contents('/var/www/html/system/libraries/bus/infinity_confirm_seat_resp.xml');           
                
                $response1 = str_replace("<soap:Body>","",$response_data);
                $response2 = str_replace("</soap:Body>","",$response1);
                $xml_string1 = preg_replace('/xs:/', '', $response2);       
                $xml_string2 = preg_replace('/diffgr:/', '', $xml_string1);     

                // convertingc to XML
                $xml = new \SimpleXMLElement($xml_string2);  
                $encode_block_resp = json_encode($xml);
                $response_data = json_decode($encode_block_resp, true);                                      

                $GLOBALS['CI']->custom_db->generate_static_response(json_encode($response_data));
                /**    PROVAB LOGGER * */
                $GLOBALS['CI']->private_management_model->provab_xml_logger('Book_Seat', $book_id, 'bus', json_encode($request['data']), json_encode($response_data));

                $error_msg = 'Error from supplier end';
                if(isset($response_data['BookSeatV3Response']) && isset($response_data['BookSeatV3Response']['BookSeatV3Result'])){
                    $bookedResponse = $response_data['BookSeatV3Response']['BookSeatV3Result']; 
                    if(isset($bookedResponse['diffgram']) && isset($bookedResponse['diffgram']['DocumentElement'])){
                        if(isset($bookedResponse['diffgram']['DocumentElement']['ITSBookSeat'])){ 
                            if(isset($bookedResponse['diffgram']['DocumentElement']['ITSBookSeat']['Status']) && $bookedResponse['diffgram']['DocumentElement']['ITSBookSeat']['Status'] == 1){
                                $response['data']['result'] = $bookedResponse['diffgram']['DocumentElement'];
                            }else{
                                $response['status'] = false;
                                $response['msg'] =  $error_msg;    
                            }                                                                                
                        }else{
                            $response['status'] = false;
                            $response['msg'] =  $error_msg;
                        }
                    }
                }else{
                    $response['status'] = false;
                    $response['msg'] =  $error_msg;
                }                
            } else {
                $response['status'] = false;
                $response['msg'] = 'Invalid Booking Request';
            }
        } else {
            $response['status'] = FAILURE_STATUS;
        }
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

        //debug($api_price_details);die('++00++');
        $user_oid = $CI->entity_user_id;
        $user_oid = $GLOBALS['CI']->entity_user_id;
        $domain_details = $GLOBALS['CI']->custom_db->single_table_records('b2b_user_details','user_oid,comm_group_id', array('user_oid' => intval($user_oid)));
        $group_id = $domain_details['data'][0]['comm_group_id'];

        $this->commission = $currency_obj->get_commission($__trip_flight,$user_oid,$group_id, INFINITY_BUS_BOOKING_SOURCE,'generic');
        //debug($this->commission);die('=++++=');
        
        //Calculate master commission
        $master_commission = 0;
        if(!empty($this->commission) && $this->commission['admin_commission_list']['master_commission'][0]['is_master'] == true){

            $comm_amnt = $this->commission['admin_commission_list']['master_commission'][0]['value'];
            $api_price_details['CommAmount'] = ($api_price_details['base_fare']*$comm_amnt)/100;
           
        }

        //debug($this->commission);die();
        // Calculate agent commission
        $commission_value = 0;
        $tds_val = 0;
        if(!empty($this->commission) && $this->commission['admin_commission_list']['commission_value'][0]['value'] > 0){
            $commission_value = $this->commission['admin_commission_list']['commission_value'][0]['value'];
            $tds_val = $this->commission['admin_commission_list']['commission_value'][0]['tds'];


        }

       // debug($commission_value);die();


        //debug($api_price_details);die('=++++=');

        
        $total_price['Fare'] = $api_price_details['Fare'];
        $total_price['base_fare'] = $api_price_details['base_fare'];
        $total_price['_CustomerBuying'] = round($agent_price_details['Fare']+$gst_value,2);
        
        
        
        $total_price['_AdminBuying'] = round($api_price_details['Fare']-$api_price_details['CommAmount'],2);
        $total_price['_AgentMarkup'] =  $agent_price_details['Fare'] - $admin_price_details['Fare'];
        
        //$this->commission = $currency_obj->get_commission();
        $agent_commission = $this->calculate_commission($api_price_details['CommAmount']);
        $_AgentBuying = $admin_price_details['Fare']+$gst_value-($api_price_details['CommAmount']*$commission_value)/100 + ($agent_commission*$tds_val)/100 ;
        //debug($agent_commission);die();
           
        $total_price['_AgentBuying'] = round($_AgentBuying,2);
        $total_price['_Commission'] = $agent_commission;
        $total_price['_AdminCommission'] = round($api_price_details['CommAmount'], 2);
        $total_price['_tdsCommission'] = round(($agent_commission*$tds_val)/100, 2);
        $total_price['_AgentEarning'] = $total_price['_Commission'] + $total_price['_AgentMarkup'] - $total_price['_tdsCommission'];
        $total_price['_TotalPayable'] = round($total_price['_AgentBuying'],2);
        $total_price['_GST'] = round($gst_value,2);

        $total_price['service_tax'] = $api_price_details['service_tax'];
         //debug($total_price);exit('78');
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

        //debug($api_price_details);die('++00++');
        $user_oid = $CI->entity_user_id;
        $user_oid = $GLOBALS['CI']->entity_user_id;
        $domain_details = $GLOBALS['CI']->custom_db->single_table_records('b2b_user_details','user_oid,comm_group_id', array('user_oid' => intval($user_oid)));
        $group_id = $domain_details['data'][0]['comm_group_id'];

        $this->commission = $currency_obj->get_commission($__trip_flight,$user_oid,$group_id, INFINITY_BUS_BOOKING_SOURCE,'generic');
        //debug($this->commission);die('=++++=');
        
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

       // debug($commission_value);die();


        //debug($api_price_details);die('=++++=');

        
        $total_price['Fare'] = $api_price_details['Fare'];
        $total_price['base_fare'] = $api_price_details['base_fare'];
        $total_price['_CustomerBuying'] = round($agent_price_details['Fare']+$gst_value,2);
        
        
        
        $total_price['_AdminBuying'] = round($api_price_details['Fare']-$api_price_details['CommAmount'],2);
        $total_price['_AgentMarkup'] =  $agent_price_details['Fare'] - $admin_price_details['Fare'];
        
        //$this->commission = $currency_obj->get_commission();
        $agent_commission = $this->calculate_commission($api_price_details['CommAmount']);
        $_AgentBuying = $admin_price_details['Fare']+$gst_value-($api_price_details['CommAmount']*$commission_value)/100 + ($agent_commission*$tds_val)/100 ;
        //debug($agent_commission);die();
           
        $total_price['_AgentBuying'] = round($_AgentBuying,2);
        $total_price['_Commission'] = round($agent_commission,2);
        $total_price['_AdminCommission'] = round($api_price_details['CommAmount'], 2);
        $total_price['_tdsCommission'] = round(($agent_commission*$tds_val)/100, 2);
        $total_price['_AgentEarning'] = round(($total_price['_Commission'] + $total_price['_AgentMarkup']- ($agent_commission*$tds_val)/100),2);
        $total_price['_TotalPayable'] = round($total_price['_AgentBuying'],2);
        $total_price['_GST'] = round($gst_value,2);

        $total_price['service_tax'] = $api_price_details['service_tax'];
        $total_price['_Commission_org_pct'] = round($commission_value,2);
        $total_price['_admin_Commission_org_pct'] = round($comm_amnt,2);
         //debug($total_price);exit('78');
        return $total_price;
    }
    /*Seat layout format*/
    public function seat_layout_format($bus_details, $currency_obj, $module, $module_type){
        //debug($bus_details);die('7');
        $formatted_seat_data = array();
        foreach($bus_details as $bus_key => $bus_result){
          
            $bus_result['Fare'] = $bus_result['base_fare'];
            $bus_result['seat_id'] = $bus_result['seat_id'];
            $api_price_details = $bus_result;
            $admin_price_details = $this->update_markup_currency($bus_result, $currency_obj,1, true, false, $module_type); 
            $agent_price_details = $this->update_markup_currency($bus_result, $currency_obj,1, false, true, $module_type);
            $b2b_price_details = $this->b2b_price_details($api_price_details, $admin_price_details, $agent_price_details, $currency_obj);
            
            $bus_result['Fare'] = $b2b_price_details['_CustomerBuying']; 
            $formatted_seat_data[$bus_key] = $bus_result;
        }
         //debug($formatted_seat_data);exit;
        return $formatted_seat_data;
    }
    /*Seat layout format*/
    public function seat_book_format($bus_details, $currency_obj, $module, $module_type){
       
        //debug($bus_details);die();
        //$bus_details['Fare'] = $bus_details['base_fare'];
        $bus_details['Fare'] = $bus_details['total_fare'];
        $api_price_details = $bus_details;

        //debug($api_price_details);die('----');
        
        $admin_price_details = $this->update_markup_currency($bus_details, $currency_obj,1, true, false, $module_type); 
        $agent_price_details = $this->update_markup_currency($bus_details, $currency_obj,1, false, true, $module_type);
        $b2b_price_details = $this->b2b_price_details($api_price_details, $admin_price_details, $agent_price_details, $currency_obj);
        $bus_result['b2b_PriceDetails'] = $b2b_price_details; 
        //debug($bus_result);die('8');
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
        //debug($token);die('====');
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

                // $seats[$sk]['seq_no'] = get_converted_currency_value($currency_obj->force_currency_conversion($sv['seq_no']));
                // $seats[$sk]['decks'] = get_converted_currency_value($currency_obj->force_currency_conversion($sv['decks']));
                // $seats[$sk]['SeatType'] = get_converted_currency_value($currency_obj->force_currency_conversion($sv['SeatType']));
                // $seats[$sk]['IsAcSeat'] = get_converted_currency_value($currency_obj->force_currency_conversion($sv['IsAcSeat']));
                // $seats[$sk]['seat_id'] = get_converted_currency_value($currency_obj->force_currency_conversion($sv['seat_id']));
                // $seats[$sk]['base_fare'] = get_converted_currency_value($currency_obj->force_currency_conversion($sv['base_fare']));

                $seats[$sk]['seq_no'] = $sv['seq_no'];
                $seats[$sk]['decks'] = $sv['decks'];
                $seats[$sk]['SeatType'] = $sv['SeatType'];
                $seats[$sk]['IsAcSeat'] = $sv['IsAcSeat'];
                $seats[$sk]['seat_id'] = $sv['seat_id'];
                $seats[$sk]['base_fare'] = $sv['base_fare'];

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
        error_reporting(0);
         //debug($params);exit;
        // error_reporting(E_ALL);
        // debug($params);exit;
        $CI = & get_instance();
        //Need to return following data as this is needed to save the booking fare in the transaction details
        $response['fare'] = $response['domain_markup'] = $response['level_one_markup'] = 0;

        $domain_origin = $params['temp_booking_cache']['domain_list_fk'];
        $status = BOOKING_CONFIRMED;
        $app_reference = $app_booking_id;
        $booking_source = $params['temp_booking_cache']['booking_source'];
        $pnr = $params['result']['PNRNo'];
        $ticket = $params['result']['TicketNo'];
        $transaction = $params['result']['trans_no'];
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
        //debug($SeatFare);
        $SeatFareDetails = $params['temp_booking_cache']['book_attributes']['token']['seat_attr']['seats'];
        //debug($SeatFareDetails);die();
        
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
                //$title = get_enum_list('title', $params['temp_booking_cache']['book_attributes']['pax_title'][$key]);

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
        $bus_booking_origin = $CI->bus_model->save_booking_details($domain_origin, $status, $app_reference, $booking_source, $pnr, $ticket, $transaction, $phone_number, $alternate_number, $payment_mode, $created_by_id, $email, $transaction_currency, $currency_conversion_rate, $canacel_policy, $phone_code, $selected_pm);
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

    function format_pre_cancel_data($xml)
    {
        $cancel_confirm_decode = ''; $pre_cancel_data = [];
        if(isset($xml['CancelDetailsResponse']) && isset($xml['CancelDetailsResponse']['CancelDetailsResult'])){          
            $cancel_confirm_decode = $xml['CancelDetailsResponse']['CancelDetailsResult'];           
        }
        if(isset($cancel_confirm_decode['diffgram']) && isset($cancel_confirm_decode['diffgram']['DocumentElement']))               
        {
            if(isset($cancel_confirm_decode['diffgram']['DocumentElement']['ITSTicketCancelDetails'])){
                $cancel_raw_data = $cancel_confirm_decode['diffgram']['DocumentElement']['ITSTicketCancelDetails'];
                $pre_cancel_data["status"] = 1;
                $pre_cancel_data["is_cancellable"] = 1;

                $api_total_fare = isset($cancel_raw_data['TotalFare'])?$cancel_raw_data['TotalFare']:0;
                $refund_amt = isset($cancel_raw_data['RefundAmount'])?$cancel_raw_data['RefundAmount']:0;
                $cancellation_charges = $api_total_fare - $refund_amt;
                $cancellation_percentage = '';
                if($refund_amt == 0){
                    $cancellation_percentage = 100;
                }else{
                    $refund_perc = ($refund_amt / $api_total_fare) * 100;                    
                    $cancellation_percentage = 100 - $refund_perc;
                }     

                $pre_cancel_data["cancel_percent"] = floor($cancellation_percentage);
                $pre_cancel_data["cancel_amount"] = $cancellation_charges;
            }
        }        
        //Need to check this              
        return $pre_cancel_data;
    }

    function pre_cancellation_data($booking_details, $app_reference)
    {
        $cancellation_request_params = $this->cancellation_request_params($booking_details, $app_reference);
        $is_cancellable_request = $this->is_cancellable($cancellation_request_params);        
        if ($is_cancellable_request['status']) {   
            $is_cancellable_response_data = $this->execute_curl($is_cancellable_request);                                 
            // $is_cancellable_response_data = file_get_contents('/var/www/html/system/libraries/bus/infinity_is_cancellable_resp.xml');                                 
            $response1 = str_replace("<soap:Body>","",$is_cancellable_response_data);
            $response2 = str_replace("</soap:Body>","",$response1);       
            $xml_string1 = preg_replace('/xs:/', '', $response2);       
            $xml_string2 = preg_replace('/diffgr:/', '', $xml_string1);     

            // convertingc to XML
            $xml = new \SimpleXMLElement($xml_string2);
            $can_encoded_data = json_encode($xml);
            $is_cancellable_response_data = json_decode($can_encoded_data, true);
            $is_cancellable_response_data = $this->format_pre_cancel_data($is_cancellable_response_data);
            return $is_cancellable_response_data;
        }
    }

    function pre_partial_cancel($seats,$tckt_no,$b_s){
       
        $retain_pnr = $GLOBALS['CI']->custom_db->single_table_records('bus_booking_details', 'pnr,ticket', array('ticket' => $tckt_no));
        $pn = explode(',', $retain_pnr['data'][0]['pnr']);
        $rtn_pn = array();
        $rr_seats = explode(',', $seats);
        foreach ($pn as $k => $v) {
            if(!in_array($v, $rr_seats)){
                array_push($rtn_pn, $v);
            }
        }
        $rt_pn = '';
        if(!empty($rtn_pn)){
            $rt_pn = implode(',', $rtn_pn);
        }else{
            $rt_pn = '';
        }
        

        $url = $this->Url;
        $strRequest = '
            <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
              <soap:Body>
                <IsTicketCancellable xmlns="http://www.vrlgroup.in:88/vrltrvl/VrlWebService/BookingWebService.asmx">
                  <username>'.$this->UserName.'</username>
                  <password>'.$this->Password.'</password>
                  <TRNO>'.$tckt_no.'</TRNO>
                  <RETAINPNRS>'.$rt_pn.'</RETAINPNRS>
                  <CANCELPNRS>'.$seats.'</CANCELPNRS>
                  <OPENPNRS></OPENPNRS>
                </IsTicketCancellable>
              </soap:Body>
            </soap:Envelope>
            ';
            $soap_action = 'http://www.vrlgroup.in:88/vrltrvl/VrlWebService/BookingWebService.asmx/IsTicketCancellable';

            $is_can = $GLOBALS['CI']->api_interface->process_xml_request($strRequest,$url,$soap_action);

            
            $is_can = Converter::createArray($is_can);
        
            if(isset($is_can['soap:Envelope']['soap:Body']['IsTicketCancellableResponse']['IsTicketCancellableResult']['DETAILS']['CANCELOPEN']) && $is_can['soap:Envelope']['soap:Body']['IsTicketCancellableResponse']['IsTicketCancellableResult']['DETAILS']['CANCELOPEN']['STATUS'] == true){
                $response['is_ticket_cancellable']['is_cancellable'] = true;
                $response['is_ticket_cancellable']['cancellation_charges'] = $is_can['soap:Envelope']['soap:Body']['IsTicketCancellableResponse']['IsTicketCancellableResult']['DETAILS']['CANCELOPEN']['CANCEL_CHARGES'];
                $response['is_ticket_cancellable']['refund_amount'] = $is_can['soap:Envelope']['soap:Body']['IsTicketCancellableResponse']['IsTicketCancellableResult']['DETAILS']['CANCELOPEN']['CANCEL_REFUND_AMOUNT'];
            }else{
                $response['is_ticket_cancellable']['is_cancellable'] = false;
            }
            return $response;
    }
    
    /*
     * Balu A
     * Process Cancellation Request
     */

    function cancel_full_booking($booking_details, $app_reference,$seat_to_cancel="") {
        //1.IsCancellable2 -  Check elgility for Cancellation
        //2.CancelTicket2 - Cancell the Ticket if elgible
        $cancel_resp['data'] = array();
        $cancel_resp['status'] = FAILURE_STATUS;
        $cancel_resp['msg'] = 'Remote IO Error';
        //Format the Request for Cancellation
        $cancellation_request_params = array();
        // debug($booking_details);exit;
        $cancellation_request_params = $this->cancellation_request_params($booking_details, $app_reference,$seat_to_cancel);                
        $is_cancellable_request = $this->cancel_ticket($cancellation_request_params);                        
        $header_info = $this->get_header();
        if ($is_cancellable_request['status']) {
            $response_data = $this->execute_curl($is_cancellable_request);
            // $response_data = file_get_contents('/var/www/html/system/libraries/bus/infinity_confirm_cancel_resp.xml');
            $response1 = str_replace("<soap:Body>","",$response_data);
            $response2 = str_replace("</soap:Body>","",$response1);
            // $xml_string = preg_replace('/xmlns[^=]*="[^"]*"/i', '', $result);
            // $xml_string1 = preg_replace('/[a-zA-Z]+:/', '', $xml_string);        
            $xml_string1 = preg_replace('/xs:/', '', $response2);       
            $xml_string2 = preg_replace('/diffgr:/', '', $xml_string1);

            // convertingc to XML
            $response_data = new \SimpleXMLElement($xml_string2);
            $encoded_resp = json_encode($response_data);
            $cancell_ticket_response_data = json_decode($encoded_resp, true);

            $this->CI->custom_db->generate_static_response(json_encode($is_cancellable_request));
            $this->CI->custom_db->generate_static_response(json_encode($response_data));

            if (valid_array($cancell_ticket_response_data) == true) {
                if(isset($cancell_ticket_response_data['ConfirmCancellationResponse']) && $cancell_ticket_response_data['ConfirmCancellationResponse']['ConfirmCancellationResult']){
                    $cancel_confirm_decode = $cancell_ticket_response_data['ConfirmCancellationResponse']['ConfirmCancellationResult'];
                    if(isset($cancel_confirm_decode['diffgram']) && isset($cancel_confirm_decode['diffgram']['DocumentElement']))               
                    {                        
                        if(isset($cancel_confirm_decode['diffgram']['DocumentElement']['ConfirmCancellation']) && $cancel_confirm_decode['diffgram']['DocumentElement']['ConfirmCancellation']['Status'] == 1){
                            $cancel_resp['msg'] = 'Cancellation is Success';
                            $cancel_resp['status'] = SUCCESS_STATUS; //Update To Success Status                            
                            $cancel_resp['data']['TotalFare'] = $cancel_confirm_decode['diffgram']['DocumentElement']['ConfirmCancellation']['TotalFare']; //Store Data
                            $cancel_resp['data']['RefundAmount'] = $cancel_confirm_decode['diffgram']['DocumentElement']['ConfirmCancellation']['RefundAmount']; //Store Data
                            $cancel_resp['data']['SeatNames'] = $cancel_confirm_decode['diffgram']['DocumentElement']['ConfirmCancellation']['SeatNames']; //Store Data
                            $cancel_resp['data']['Status'] = $cancel_confirm_decode['diffgram']['DocumentElement']['ConfirmCancellation']['Status']; //Store Data                            
                        }
                    }    
                }                                                   
            }
        }    
        return $cancel_resp;
    }

    /*
     * Balu A
     * Save the Cancellation Details into Database
     */

    function save_cancellation_data($app_reference, $cancellation_details,$cancel_type="",$seat_to_cancel=array(),$commission_to_deduct='') {
        //debug($cancellation_details);die('99');
        $CI = & get_instance();
        $response['data'] = array();
        $response['status'] = FAILURE_STATUS;
        $resposne['msg'] = 'Remote IO Error';

        //save cancellation details format
        $cancellation_details = $this->cancel_details_format($cancellation_details);
        //debug($cancellation_details);die('9999');
        $cancellation_status_details = $cancellation_details['data'];        
        if ($cancellation_status_details['Status'] == true) {
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
        if(!empty($cache_contents)){
            $bus_inf_data = array();
            foreach($cache_contents['result'] as $bus_data){
                if($route_schedule_id == $bus_data['RouteScheduleId'] && $route_code == $bus_data['RouteCode']){                    
                    $bus_inf_data = $bus_data;
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
        $search_status = $search_result['GetAvailableRoutesResponse']['GetAvailableRoutesResult']['diffgram'];
        if (valid_array($search_result) == true && $search_status > 0) 
        {
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
                $response['data']['bus_date_1'] = $clean_search_details['data']['bus_date_1'];
                if (empty($clean_search_details['data']['bus_date_2']) == false) {
                    $response['data']['bus_date_2'] = date('Y-m-d', strtotime($clean_search_details['data']['bus_date_2']));
                }
                
                $bus_station_list = $GLOBALS['CI']->db_cache_api->get_bus_station_list(array('k' => 'name', 'v' => 'station_id'));
                //update station id to the list
                /*$response['data']['bus_station_from_id'] = $bus_station_list[$response['data']['bus_station_from']];
                $response['data']['bus_station_to_id'] = $bus_station_list[$response['data']['bus_station_to']];*/
                $vrl_from_to = $this->get_infinity_city_id($response['data']['bus_station_from'],$response['data']['bus_station_to']);
                $response['data']['bus_station_from_id'] = $vrl_from_to['from_id'];
                $response['data']['bus_station_to_id'] = $vrl_from_to['to_id'];
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
        $this->search_hash = md5(serialized_data($search_id.$response['data'].INFINITY_BUS_BOOKING_SOURCE));
        return $response;
    }

    /**
     * Create ROW * COL seat Matrix
     * @param array $sl_bus_seats
     */
    public function group_matrix($sl_bus_seats) {
        
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
        $deck_cols = $deck_config = array(); $gang_way = array();        
        foreach ($cls_seat as $__k => $__v) {
            if($__v['BlockType'] == 3){
                $gang_way[] = $__v['col'];
            }
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
        $gang_way = array_values(array_unique($gang_way));
        return array('deck_config' => $deck_config, 'deck' => $deck, 'deck_cols' => $deck_cols, 'gang_way' => $gang_way);
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

    //Formating As TMX
    function format_as_tmx_response($response_data,$search_data){
        $search = array();
        $response = array();
        $journey_list='';
        if(isset($response_data['GetAvailableRoutesResponse']['GetAvailableRoutesResult']['diffgram'])){
            $journey_list = $response_data['GetAvailableRoutesResponse']['GetAvailableRoutesResult']['diffgram']['DocumentElement']['AllRouteBusLists'];
        }
        
        if(!empty($journey_list)){
            foreach ($journey_list as $key => $value) {                
                $list = array();
                $departure_time = date("H:i:s", strtotime($value['CityTime']));
                $dep_time = date("H:i", strtotime($value['CityTime']));
                $arrival_time = date("H:i:s", strtotime($value['ArrivalTime']));
                $arr_time = date("H:i", strtotime($value['ArrivalTime']));
                $route_time = date("H:i:s", strtotime($value['RouteTime']));                 
                $time1 = new DateTime($departure_time);
                $time2 = new DateTime($arrival_time);                
                $interval = $time1->diff($time2);                
                $duration_hrs = $interval->format('%h') ;
                $duration_mins = $interval->format('%i');
                $duration = $duration_hrs .':'.$duration_mins;

                $list['CompanyName'] = $value['CompanyName'];
                $list['CompanyId'] = $value['CompanyID'];
                $list['ProvId'] = $value['CompanyID'];
                $list['RouteID'] = $value['RouteID'];

                // $list['BusTypeName'] = $value['ArrangementName'].' '.$value['BusTypeName'];                
                $list['BusTypeName'] = $value['BusTypeName'];                
                $list['BusLabel'] = $value['BusTypeName'];
                $list['RouteCode'] = $value['RouteID'];
                $list['RouteScheduleId'] = $value['ReferenceNumber'];
                $list['RouteTimeID'] = $value['RouteTimeID'];                
                $list['DeptTime'] = $dep_time;
                $list['DepartureTime'] = $dep_time;
                $list['ArrTime'] = $arr_time;
                $list['ArrivalTime'] = $arr_time;
                $list['RouteTime'] = $route_time;
                $list['RouteName'] = $value['RouteName'];
                $list['Kilometer'] = $value['Kilometer'];
                $list['FromCityId'] = $value['FromCityId'];
                $list['FromCityName'] = $value['FromCityName'];
                $list['ToCityId'] = $value['ToCityId'];
                $list['ToCityName'] = $value['ToCityName'];
                $list['BusType'] = $value['BusType'];
                $list['ArrangementID'] = $value['ArrangementID'];
                $list['ArrangementName'] = $value['ArrangementName'];
                $list['BusType'] = $value['BusType'];                
                $list['ReferenceNumber'] = $value['ReferenceNumber'];                
                $list['ApproxArrival'] = $value['ApproxArrival'];                
                $list['IsAPICommission'] = $value['IsAPICommission'];                
                $list['CityTime24'] = $value['CityTime24'];                
                $list['DiscountType'] = $value['DiscountType'];                
                $list['DiscountRate'] = $value['DiscountRate'];                
                $list['AllowReSchedule'] = $value['AllowReSchedule'];                
                $list['ReScheduleCharge'] = $value['ReScheduleCharge'];                
                $list['StopReScheduleMinutes'] = $value['StopReScheduleMinutes'];                
                $list['ReScheduleChargeType'] = $value['ReScheduleChargeType'];                
                $list['IsSocialDistanceMaintain'] = isset($value['IsSocialDistanceMaintain'])?$value['IsSocialDistanceMaintain']:'';                
                $list['SocialDistanceType'] = isset($value['SocialDistanceType'])?$value['SocialDistanceType']:'';                

                $list['HasNAC'] = '';
                $list['HasAC'] = '';
                $list['IsVolvo'] = '';
                if($value['BusType'] == 0){
                    $list['HasAC'] = '1';
                }else{
                    $list['HasNAC'] = '1';
                }
                $list['HasSeater'] = '';
                $list['HasSleeper'] = '';
                $list['SemiSeater'] = '';
                if($value['BusSeatType'] == 1 || $value['BusSeatType'] == 2){
                    $list['HasSleeper'] = '1';
                }else{
                    $list['HasSeater'] = '1';
                }

                $list['CommAmount'] = '';
                $list['DiscountAmt'] = '';
                $list['TripId'] = '';
                $list['CompanySuf'] = '';
                $list['From'] = $search_data['bus_station_from'];
                $list['To'] = $search_data['bus_station_to'];
                $list['Duration'] = $duration;

                $list['BusStatus'] = array(
                    'BaseFares' => array(
                            '0' =>'0',
                            '1' =>'0',
                        ),  
                    'TotalTax'=>0,
                    'CurrencyCode'=>'INR',
                );

                //$list['Fare'] = '0';
                $list['AvailableSeats'] = $value['EmptySeats'];

                //pickups
                $BOARDING = isset($value['BoardingPoints'])?$value['BoardingPoints']:''; 
                $pick = array();  
                if(!empty($BOARDING)){
                    $boarding_points = explode('#', $BOARDING);                    
                    foreach ($boarding_points as $board_key => $board_value) {
                        $p = array();
                        $explode_board = explode('|', $board_value);                        
                        $p['PickupCrossed'] = '';
                        $p['PickupTime'] = $explode_board[2];
                        $p['PickupArea'] = $explode_board[1];
                        $p['PickupName'] = $explode_board[1];
                        $p['PickupCode'] = $explode_board[0];
                        $pick[] = $p;                        
                    }                            
                }                                
                $list['Pickups'] = $pick;                

                //Dropoffs
                $DROPING = isset($value['DroppingPoints'])?$value['DroppingPoints']:''; 
                $dropoffs = array();  
                if(!empty($DROPING)){
                    $droping_points = explode('#', $DROPING);                              
                    foreach ($droping_points as $drop_key => $drop_value) {
                        $d = array();
                        $explode_drop = explode('|', $drop_value);                                            
                        $d['DropoffTime'] = $explode_drop[2];
                        $d['DropoffName'] = $explode_drop[1];
                        $d['DropoffCode'] = $explode_drop[0];                        
                        $dropoffs[] = $d;               
                    }                            
                }
                $list['Dropoffs'] = $dropoffs;

                $list['CancPolicy'] = array();
                $list['BusTypeNames'] = array(
                    'IsAC'=>'',
                    'Seating'=>'',
                    'Make'=>'',
                );

                //seat fare
                $fare['base_fare']=array();
                $fare['gst']=array();
                $fare['total_fare']=array();
                $seat_fare=array();

                $i = 0;
                $fare = array(); $BusCoachType = '';
                $bus_type = $value['BusType']; $bus_seat_type = $value['BusSeatType'];
                $seater_rate = 0; $sleeper_rate = 0; $slumber_rate = 0;
                $seater_service_tax = 0; $sleeper_service_tax = 0; $slumber_service_tax = 0;
                $seater_surcharge = 0; $sleeper_surcharge = 0; $slumber_surcharge = 0; 
                if($bus_type == 0){
                    // Code to get fare
                    $seater_rate = isset($value['AcSeatRate'])?$value['AcSeatRate']:0;
                    $sleeper_rate = isset($value['AcSleeperRate'])?$value['AcSleeperRate']:0;
                    $slumber_rate = isset($value['AcSlumberRate'])?$value['AcSlumberRate']:0;

                    $seater_service_tax = isset($value['AcSeatServiceTax'])?$value['AcSeatServiceTax']:0;
                    $sleeper_service_tax = isset($value['AcSlpServiceTax'])?$value['AcSlpServiceTax']:0;
                    $slumber_service_tax = isset($value['AcSlmbServiceTax'])?$value['AcSlmbServiceTax']:0;

                    $seater_surcharge = isset($value['AcSeatSurcharges'])?$value['AcSeatSurcharges']:0;
                    $sleeper_surcharge = isset($value['AcSlpSurcharges'])?$value['AcSlpSurcharges']:0;
                    $slumber_surcharge = isset($value['AcSlmbSurcharges'])?$value['AcSlmbSurcharges']:0;

                    // Code to get coach type
                    if($bus_seat_type == 1){
                        $BusCoachType = 3;
                    }else if($bus_seat_type == 2){
                        $BusCoachType = 1;
                    }else if($bus_seat_type == 3){
                        $BusCoachType = 1;
                    }

                }else if($bus_type == 1){
                    $seater_rate = isset($value['NonAcSeatRate'])?$value['NonAcSeatRate']:0;
                    $sleeper_rate = isset($value['NonAcSleeperRate'])?$value['NonAcSleeperRate']:0;
                    $slumber_rate = isset($value['NonAcSlumberRate'])?$value['NonAcSlumberRate']:0;

                    $seater_service_tax = isset($value['NonAcSeatServiceTax'])?$value['NonAcSeatServiceTax']:0;
                    $sleeper_service_tax = isset($value['NonAcSlpServiceTax'])?$value['NonAcSlpServiceTax']:0;
                    $slumber_service_tax = isset($value['NonAcSlmbServiceTax'])?$value['NonAcSlmbServiceTax']:0;

                    $seater_surcharge = isset($value['NonAcSeatSurcharges'])?$value['NonAcSeatSurcharges']:0;
                    $sleeper_surcharge = isset($value['NonAcSlpSurcharges'])?$value['NonAcSlpSurcharges']:0;
                    $slumber_surcharge = isset($value['NonAcSlmbSurcharges'])?$value['NonAcSlmbSurcharges']:0;

                    // Code to get coach type
                    if($bus_seat_type == 1){
                        $BusCoachType = 4;
                    }else if($bus_seat_type == 2){
                        $BusCoachType = 2;
                    }else if($bus_seat_type == 3){
                        $BusCoachType = 2;
                    }
                }

                $basic_arr = array();
                if($seater_rate > 0){
                    $fare[$i]['base_fare'] = $seater_rate - $seater_service_tax - $seater_surcharge;
                    $fare[$i]['gst'] = $seater_service_tax + $seater_surcharge;
                    $fare[$i]['total_fare'] = $seater_rate;
                    $basic_arr['base_fare'][$i] = $fare[$i]['base_fare'];
                    $basic_arr['total_fare'][$i] = $fare[$i]['total_fare'];
                    $basic_arr['gst'][$i] = $fare[$i]['gst'];
                    $i++;
                }
                if($sleeper_rate > 0){
                    $fare[$i]['base_fare'] = $sleeper_rate - $sleeper_service_tax - $sleeper_surcharge;
                    $fare[$i]['gst'] = $sleeper_service_tax + $sleeper_surcharge;
                    $fare[$i]['total_fare'] = $sleeper_rate;
                    $basic_arr['base_fare'][$i] = $fare[$i]['base_fare'];
                    $basic_arr['total_fare'][$i] = $fare[$i]['total_fare'];
                    $basic_arr['gst'][$i] = $fare[$i]['gst'];
                    $i++;
                }
                if($slumber_rate > 0){                     
                    $fare[$i]['base_fare'] = $slumber_rate - $slumber_service_tax - $slumber_surcharge;
                    $fare[$i]['gst'] = $slumber_service_tax + $slumber_surcharge;
                    $fare[$i]['total_fare'] = $slumber_rate;
                    $basic_arr['base_fare'][$i] = $fare[$i]['base_fare'];
                    $basic_arr['total_fare'][$i] = $fare[$i]['total_fare'];
                    $basic_arr['gst'][$i] = $fare[$i]['gst'];
                    $i++;
                }
                // $list['BookedSeat'] = $booked_seat;

                
                $list['FareData'] = $fare;
                $list['Fare'] = min($basic_arr['total_fare']);
                $list['Max_Fare'] = max($basic_arr['total_fare']);
                $list['base_fare'] = min($basic_arr['base_fare']);
                $list['service_tax'] = min($basic_arr['gst']);
                $list['ResultToken'] = md5(json_encode($value));
                $list['booking_source'] = INFINITY_BUS_BOOKING_SOURCE;
                $list['status'] = '1';

                $search[]=$list;
            } 
        }
        $response['Status']='1';
        $response['Message']='';
        $response['Search']=$search;        
        return $response;
    }

    private function GetCoachSeats($route_code){
        $url = $this->Url;
        $response = array();

        $request = '
            <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
              <soap:Body>
                <GetCoachSeats xmlns="http://www.vrlgroup.in:88/vrltrvl/VrlWebService/BookingWebService.asmx">
                    <username>'.$this->UserName.'</username>
                    <password>'.$this->Password.'</password>
                    <COACHID>'.$route_code.'</COACHID>
                </GetCoachSeats>
              </soap:Body>
            </soap:Envelope>
        ';
        $soap_action = 'http://www.vrlgroup.in:88/vrltrvl/VrlWebService/BookingWebService.asmx/GetCoachSeats';

        $response_data = $GLOBALS['CI']->api_interface->process_xml_request($request,$url,$soap_action);
        $response = Converter::createArray($response_data);

        $response = $response['soap:Envelope']['soap:Body']['GetCoachSeatsResponse']['GetCoachSeatsResult'];
        return $response;
    }

    private function GetCoachList(){
        $url = $this->Url;
        $response = array();
        $request = '
                    <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                      <soap:Body>
                        <GetCoachList xmlns="http://www.vrlgroup.in:88/vrltrvl/VrlWebService/BookingWebService.asmx">
                          <username>'.$this->UserName.'</username>
                          <password>'.$this->Password.'</password>
                        </GetCoachList>
                      </soap:Body>
                    </soap:Envelope>
                ';

        $soap_action = 'http://www.vrlgroup.in:88/vrltrvl/VrlWebService/BookingWebService.asmx/GetCoachList';

        $response_data = $GLOBALS['CI']->api_interface->process_xml_request($request,$url,$soap_action);
        $response = Converter::createArray($response_data);  
        $response = $response['soap:Envelope']['soap:Body']['GetCoachListResponse']['GetCoachListResult'];
        //debug($response);die();
        return $response;      
    }

    private function busLayout_formatting($response_data, $company_name){        
        $response = array(); $checkStatus = array(); $company_id = ''; $board_data = ''; $drop_data = '';
        if(isset($response_data) && !empty($response_data) && isset($response_data['GetSeatArrangementDetailsV3Response']['GetSeatArrangementDetailsV3Result']['diffgram']))
        { 
            $layout_details = $response_data['GetSeatArrangementDetailsV3Response']['GetSeatArrangementDetailsV3Result']['diffgram']['DocumentElement']['ITSSeatDetails'];
            // Sort seats by rows in ascending order
            $seat_layout_rows = array_column($layout_details, 'Row');
            array_multisort($seat_layout_rows, SORT_ASC, $layout_details);

            $each_col_seat = array();
            $count = 0;            
            foreach ($layout_details as $seat_key => $seat_value) {
                $width = ''; $length = ''; $zIndex = '';
                // if($seat_value['BlockType'] != 3){
                    if($seat_value['SeatType'] == 0 || $seat_value['SeatType'] == 2){
                        $width = 1;
                        $length = 1;
                        $zIndex = 0; 
                        $checkStatus["sleeper"] = '';
                    }else if($seat_value['SeatType'] == 1){                    
                        if($seat_value['RowSpan'] == 0 && $seat_value['ColumnSpan'] == 2){
                            $length = 1;
                            $width = 2;
                        }else{
                            $length = 2;
                            $width = 1;                            
                        }                        
                        if($seat_value['UpLowBerth'] == 'LB'){                                                      
                            $zIndex = 0; 
                            $checkStatus["sleeper"] = 1;
                        }else if($seat_value['UpLowBerth'] == 'UB'){                            
                            $zIndex = 1;
                            $checkStatus["sleeper"] = 1;         
                        }
                    } 

                    $b_fare = 0;
                    $t_fare = 0;
                    $s_tax = 0;
                    $isAvailable = '0';
                    $status = 1;

                    if($seat_value['IsLadiesSeat'] == 'Y' && $seat_value['Available'] == 'Y'){
                        $status = 3;
                        $isAvailable = '1';
                    }else if($seat_value['IsLadiesSeat'] == 'Y' && $seat_value['Available'] == 'N'){
                        $status = -3;
                        $isAvailable = '0';
                    }else if($seat_value['IsLadiesSeat'] == 'N' && $seat_value['Available'] == 'N'){
                        $status = -2;
                        $isAvailable = '0';
                    }else if($seat_value['Available'] == 'Y'){
                        $status = 1;
                        $isAvailable = '1';
                    }

                    if(isset($seat_value['ServiceTax'])){
                        $s_tax = $seat_value['ServiceTax'];
                    }
                    if(isset($seat_value['Surcharges'])){
                        // $service_tax += $seat_value['Surcharges'];
                    }
                    if(isset($seat_value['BaseFare'])){
                        $b_fare = $seat_value['BaseFare'];
                    }
                    if(isset($seat_value['SeatRate'])){
                        $t_fare = $seat_value['SeatRate'];
                    }       

                    $l_u='';
                    if($zIndex == 0){
                        $l_u='Lower';
                    }else{
                        $l_u='Upper';
                    }      

                    $company_id = $seat_value['CompanyID']; 
                    $board_data = $seat_value['BoardingPoints'];  
                    $drop_data = $seat_value['DroppingPoints'];  

                    $each_seat_details = array(
                        'seq_no' => $count++,
                        'row' => $seat_value['Row'],
                        'col' => $seat_value['Column'],
                        'width' => $width,
                        'height' => $length,
                        'seat_type' => '',//$seat_details[1],
                        'seat_no' => $seat_value['SeatNo'],
                        'seat_id' => $seat_value['SeatNo'],
                        'total_fare' => $t_fare,
                        'base_fare' => $b_fare,
                        'status' => $status,
                        'decks' => $l_u,
                        'MaxRows' => '12',
                        'MaxCols' => '8',
                        'IsAvailable' => $isAvailable,
                        'service_tax' =>$s_tax,
                        'trans_fare' =>'',
                        'CompanyName' => $company_name,
                        'ReferenceNumber' => $seat_value['ReferenceNumber'],
                        'CompanyID' => $company_id,
                        'RouteID' => $seat_value['RouteID'],
                        'RouteTimeID' => $seat_value['RouteTimeID'],
                        'ArrangementID' => $seat_value['ArrangementID'],
                        'BlockType' => $seat_value['BlockType'],
                        'RowSpan' => $seat_value['RowSpan'],
                        'ColumnSpan' => $seat_value['ColumnSpan']
                    );
                    $each_col_seat[] = $each_seat_details;  
                // } // BlockType if end             
            }            
            $layout = array(
                'MaxRows' => '12',
                'MaxCols' => '8',
            );

            $Pickups = array();
            $board_data = explode('#', $board_data);
            foreach ($board_data as $k_b_s => $v_b_s) {
                $boarding_details = explode('|', $v_b_s);
                $__b_details = array();
                $__b_details['PickupCrossed'] = '';
                $__b_details['Contact'] = '';
                $__b_details['Landmark'] = '';
                $__b_details['Address'] = $boarding_details[1];
                $__b_details['PickupTime'] = $boarding_details[2];
                $__b_details['PickupArea'] = '';
                $__b_details['PickupName'] = $boarding_details[1];
                $__b_details['PickupCode'] = $boarding_details[0];

                $Pickups[] = $__b_details;
            }

            $dropoff_stages = explode('#',$drop_data);
            $Dropoffs = array();
            foreach ($dropoff_stages as $k_d_s => $v_d_s) {
                $dropoff_details = explode('|', $v_d_s);
                $__d_details = array();
                $__d_details['DropoffTime'] = $dropoff_details[2];
                $__d_details['DropoffName'] = $dropoff_details[1];
                $__d_details['DropoffCode'] = $dropoff_details[0];
                $Dropoffs[] = $__d_details;
            }

            $Canc = array();
            $cancel_policy_req = $request = $this->cancellation_policy_request($company_id);
            if ($cancel_policy_req['status']) {

                $response_data1 = $this->execute_curl($request);
                // $response_data1 = file_get_contents('/var/www/html/system/libraries/bus/infinity_cancel_policy_resp.xml');
                $response1 = str_replace("<soap:Body>","",$response_data1);
                $response2 = str_replace("</soap:Body>","",$response1);     
                $xml_string1 = preg_replace('/xs:/', '', $response2);       
                $xml_string2 = preg_replace('/diffgr:/', '', $xml_string1); 
                $xml = new \SimpleXMLElement($xml_string2);
                $encode_cancel_resp = json_encode($xml);
                $cancel_policy4 = json_decode($encode_cancel_resp, true);
                
                if(isset($cancel_policy4['GetCancellationPolicyResponse']['GetCancellationPolicyResult']['CancellationPolicy']) && !empty($cancel_policy4['GetCancellationPolicyResponse']['GetCancellationPolicyResult']['CancellationPolicy'])){
                    if(isset($cancel_policy4['GetCancellationPolicyResponse']['GetCancellationPolicyResult']['CancellationPolicy']['Policy']['PolicyDetails'])){
                        $policy_details = $cancel_policy4['GetCancellationPolicyResponse']['GetCancellationPolicyResult']['CancellationPolicy']['Policy']['PolicyDetails'];
                        $policy_data = array();
                        if(isset($policy_details[0])){
                            $policy_data = $policy_details;
                        }else{
                            $policy_data[0] = $policy_details;
                        }
                        $c__P = array();
                        if(isset($cancel_policy4['CancellationPolicy']['NoCancelWithinMinutes'])){
                            // $c__P['Amt'] = 0;
                            $c__P['Pct'] = 100;
                            $c__P['Mins'] = $cancel_policy4['CancellationPolicy']['NoCancelWithinMinutes'];                            
                            $Canc[] = $c__P;
                        }
                        foreach ($policy_data as $pkey => $pvalue) {
                            $c__P = array();
                            // $c__P['Amt'] = 0;
                            $c__P['Pct'] = 100 - $pvalue['DeductPercent'];
                            $c__P['Mins'] = $pvalue['FromMinutes'].' - '.$pvalue['ToMinutes'];                            
                            $Canc[] = $c__P;
                        }                                
                    }
                }                
            } else {
                $response['status'] = false;
            }         

            $response['SeatLayout']['result']['value'] = $each_col_seat;
            $response['SeatLayout']['result']['layout']=$layout;
            $response['SeatLayout']['result']['Pickups']=$Pickups;
            $response['SeatLayout']['result']['Dropoffs']=$Dropoffs;
            $response['SeatLayout']['result']['Canc'] = $Canc;
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
        return $response;
    }

    private function Tentative_Booking_request($block_params){
        $request= array();

        $verify_call = $this->VerifyCall; 
        $api_url  = $this->Url;
        
        if(!empty($block_params)){

            $infinity_from_to_id = $this->get_infinity_city_id($block_params['token']['departure_from'],$block_params['token']['arrival_to']);
            
            $reference_number = $block_params['token']['RouteScheduleId'];
            $passenger_name = $block_params['contact_name'][0];
            $pickup_id = $block_params['token']['PickUpID'];
            $payble_amount = $block_params['token']['fare']['Fare'];
            $customer_email = $block_params['billing_email']; 
            $customer_phone = $block_params['passenger_contact']; 

            $seats_ar = array();
            foreach ($block_params['token']['seat_attr']['seats'] as $key => $value) {
                array_push($seats_ar, $value['seat_id']); 
            }

            $passenger_count = count($block_params['gender']);
            $seat_name_and_gender = '';
            for ($pax_count = 0; $pax_count < $passenger_count ; $pax_count++) { 
                $gender = '';
                if($block_params['gender'][$pax_count] == 1){
                    $gender = 'M';
                }else{
                    $gender = 'F';
                }
                $seat_name_and_gender .= $seats_ar[$pax_count].','.$gender.'|';                
            }
            $seat_name_and_gender = rtrim($seat_name_and_gender, '|');
            
            $value=array($reference_number,$passenger_name,$seat_name_and_gender,$customer_email,$customer_phone,$pickup_id,$payble_amount,$passenger_count,$verify_call);
            $replace=array('[referenceno]','[passengername]','[seatnames]','[email]','[contactno]','[pickupid]','[amount]','[seat]','[verifycall]');

            $message = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
              <soap:Body>
                <BlockSeatV2 xmlns="http://platformapi.itspl.net/">
                  <ReferenceNumber>[referenceno]</ReferenceNumber>
                  <PassengerName>[passengername]</PassengerName>
                  <SeatNames>[seatnames]</SeatNames>
                  <Email>[email]</Email>
                  <Phone>[contactno]</Phone>
                  <PickupID>[pickupid]</PickupID>
                  <PayableAmount>[amount]</PayableAmount>
                  <TotalPassengers>[seat]</TotalPassengers>
                  <VerifyCall>[verifycall]</VerifyCall>
                </BlockSeatV2>
              </soap:Body>
            </soap:Envelope>';
        
            $xml_req = str_replace($replace,$value,$message);

            $request['status'] = SUCCESS_STATUS;
            $request['request'] = $xml_req;
            $request['soap_action'] = '';
            $request['url'] = $api_url;
            
            return $request;   
        }        
    }

    private function format_ValidateSeat($block_params,$response_data){
        $response = array(); 
        $error_msg = 'Error from supplier end';       
        if(isset($response_data['BlockSeatV2Response']) && isset($response_data['BlockSeatV2Response']['BlockSeatV2Result'])){  
            $bookresponse = $response_data['BlockSeatV2Response']['BlockSeatV2Result'];
            if(isset($bookresponse['diffgram']) && isset($bookresponse['diffgram']['DocumentElement'])){
                if(isset($bookresponse['diffgram']['DocumentElement']['ITSBlockSeatV2'])){
                    if(isset($bookresponse['diffgram']['DocumentElement']['ITSBlockSeatV2']['Status']) && $bookresponse['diffgram']['DocumentElement']['ITSBlockSeatV2']['Status'] == 1){
                        $get_ticket['status']= $bookresponse['diffgram']['DocumentElement']['ITSBlockSeatV2']['Status'];
                        $get_ticket['BlockId']= $bookresponse['diffgram']['DocumentElement']['ITSBlockSeatV2']['BlockID'];
                        $get_ticket['message'] = "Success";
                        $response['Status'] = SUCCESS_STATUS;
                        $response['HoldSeatsForSchedule'] = $get_ticket;
                    }else{
                        $response['Status'] = FAILURE_STATUS;                                                
                        $response['Message'] = $error_msg;
                    }
                }else{
                    $response['Status'] = FAILURE_STATUS;
                    // $api_msg = $response_data['response']['message'];
                    // $message = str_replace('select','choose', $api_msg);
                    $response['Message'] = $error_msg;
                }     
            }else{
                $response['Status'] = FAILURE_STATUS;
                // $api_msg = $response_data['response']['message'];
                // $message = str_replace('select','choose', $api_msg);
                $response['Message'] = $error_msg;
            }                                 
        }else {
            $response['Status'] = FAILURE_STATUS;
            // $api_msg = $response_data['response']['message'];
            // $message = str_replace('select','choose', $api_msg);
            $response['Message'] = $error_msg;
        }
        return $response;
    }

    function format_booking_details($booking, $temp_booking){

        $response=array();
        $result = array();

        $result['IsCancelled'] = '';
        $result['RefundAmount'] = 0;
        $token_data = $temp_booking['book_attributes']['token'];
        $result['TotalFare'] = $token_data['fare']['Fare'];
        $result['TotalSeats'] = count($temp_booking['book_attributes']['contact_name']);        
        $picks = explode(',',$temp_booking['book_attributes']['token']['boarding_from']);

        $PickupInfo = array(
            'PickupTime' => '',
            'Address' => $picks[0], 
            'Phone' => '',
            'Landmark' => '',
            'PickupName' => ''
            );
        $result['PickupInfo']=$PickupInfo;
        $TotalSeats = count($temp_booking['book_attributes']['contact_name']);
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
        //debug($Passengers);//die('000');
        $seat__d=array();  $_p_nm = array(); $_s_id = array();
        foreach ($temp_booking['book_attributes']['token']['seat_attr']['seats'] as $key => $value) {
            $s__d=array();
            $s__d['SeatNo']=$key;
            $s__d['IsAcSeat']=$value['IsAcSeat'];
            $s__d['SeatType']=$value['SeatType'];
            $s__d['Fare']=$value['Fare'];

            $seat__d[]=$s__d;
            $_s_id[] = $key;
            $_p_nm[] = $booking['data']['result']['ITSBookSeat']['PNRNO'];
        }
        //debug($seat__d);die('111');
        $pax_seat_details= array();
        for($i=0; $i < $TotalSeats; $i++){
            $pax_seat_details[]=array_merge($Passengers[$i],$seat__d[$i]);
        }
        //debug($pax_seat_details);die('000');
        $result['Passengers']=$pax_seat_details;
        //contact info
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
        $result['BusTypeName'] = $temp_booking['book_attributes']['token']['bus_type'];
        $result['DepartureDateTime'] = $temp_booking['book_attributes']['token']['DepartureTime'];
        $result['ArrivalDateTime'] = $temp_booking['book_attributes']['token']['ArrivalTime'];
        $result['JourneyDate'] = $temp_booking['book_attributes']['token']['JourneyDate'];
        $result['ToCityName'] = $temp_booking['book_attributes']['token']['arrival_to'];
        $result['FromCityName'] = $temp_booking['book_attributes']['token']['departure_from'];
        $result['CompanyName'] = $token_data['operator'];
        $result['TicketNo'] = $booking['data']['result']['ITSBookSeat']['PNRNO'];

        $PNR = $booking['data']['result']['ITSBookSeat']['PNRNO'];        
        $result['seat_id'] = '';
        $result['PNRNo'] = '';
        if($TotalSeats > 0){
            $result['seat_id'] = implode(',',$_s_id);
            $result['PNRNo'] = implode(',',$_p_nm);
        }else{
            $__PNRNo = explode('!',$PNR);
            $result['seat_id'] = $_s_id[0];
            $result['PNRNo'] = $_p_nm[0];
        }
        $result['trans_no'] = $booking['data']['result']['ITSBookSeat']['PNRNO'];
        
        $t_inner_details = array();
        foreach ($temp_booking['book_attributes']['token']['seat_attr']['seats'] as $key => $value ) {
            $seat_fare_details = array(); 

            $seat_fare_details['seat_detail']['tieup_agent_commission_percentage']='';
            $seat_fare_details['seat_detail']['fare']=$value['Fare'];
            $seat_fare_details['seat_detail']['seat_number'] = $key;

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

    private function get_infinity_city_id($from_name,$to_name){
        //debug($search_data);die();
        $bus_station_from_id = $GLOBALS['CI']->custom_db->single_table_records('bus_stations_new1','infinity_city_id', array('ets_city_name' => $from_name));
        $bus_station_to_id = $GLOBALS['CI']->custom_db->single_table_records('bus_stations_new1','infinity_city_id', array('ets_city_name' => $to_name));

        $response['status'] = SUCCESS_STATUS;
        $response['from_id']=$bus_station_from_id['data'][0]['infinity_city_id'];
        $response['to_id']=$bus_station_to_id['data'][0]['infinity_city_id'];
            
        return $response;
    }

    public function cancel_details_format($cancellation_details){

        $response = array();
        $response['status'] = SUCCESS_STATUS;

        $cancel_details = $cancellation_details['data']['soap:Envelope']['soap:Body']['CancelBookingResponse']['CancelBookingResult']['DETAILS']['CANCELOPEN'];
        
        // $actual_refund_to_agent = $cancellation_details['data']['RefundAmount'] + $cancellation_details['admin_markup'];
        $actual_refund_to_agent = $cancellation_details['data']['RefundAmount'];

        $cancel_perc = '';
        if($cancellation_details['data']['RefundAmount'] == 0){
            $cancel_perc = 100;
        }else{
            $refund_perc = ($cancellation_details['data']['RefundAmount'] / $cancellation_details['data']['TotalFare']) * 100;
            $cancel_perc = 100 - $refund_perc;
        }   

        $data['CancelSeats'] = array(
                'ApiRefundAmount' =>$cancellation_details['data']['RefundAmount'],
                'RefundAmount' =>$actual_refund_to_agent,
                'ChargeAmt' => $cancellation_details['data']['TotalFare'] - $cancellation_details['data']['RefundAmount'],
                'TotalFare' => $cancellation_details['data']['TotalFare'],
                'ChargePct' => $cancel_perc,
                'supp_commission_reversed' => $cancellation_details["data"]["supp_commission_reversed"],
                'NewHoldId' => '',
                'NewTotalFare' => '',
            );
        $data['Status'] = SUCCESS_STATUS;
        $data['Message'] = $cancellation_details['msg'];

        $response['data'] = $data;
        return $response;
    }

     public function infinityGetSources()
    {
        $source_request = $this->infinity_source_request();
        
        if ($source_request['status']) {
            $result = $this->execute_curl($source_request);  
            $response1 = str_replace("<soap:Body>","",$result);
            $response2 = str_replace("</soap:Body>","",$response1);

            $xml_string1 = preg_replace('/xs:/', '', $response2);
            $xml_string2 = preg_replace('/diffgr:/', '', $xml_string1);
            // convertingc to XML
            $xml = new \SimpleXMLElement($xml_string2);
            $response = '';
            if(isset($xml->GetSourcesResponse) && isset($xml->GetSourcesResponse->GetSourcesResult)){
                $response = $xml->GetSourcesResponse->GetSourcesResult;
            }else{
                $response = '';
            }
            $encode_search_resp = json_encode($response);
            $response_data = json_decode($encode_search_resp, true);
            echo "<pre>response_data----->>"; print_r(json_encode($response_data['diffgram']['DocumentElement']['ITSSources']));die;
        }      
    }

    public function infinity_source_request()
    {
        $verify_call = $this->VerifyCall; 
        $api_url  = $this->Url;
        $value=array($verify_call);
        $replace=array('[verifycall]');
        $xml_req = '<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
          <soap:Body>
            <GetSources xmlns="http://platformapi.itspl.net/">
              <verifyCall>[verifycall]</verifyCall>
            </GetSources>
          </soap:Body>
        </soap:Envelope>';
        $xml_req = str_replace($replace,$value,$xml_req);
        $request['status'] = SUCCESS_STATUS;
        $request['request'] = $xml_req;
        $request['soap_action'] = '';
        $request['url'] = $api_url;
        return $request;
    }

}
