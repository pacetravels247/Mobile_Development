<?php
require_once BASEPATH . 'libraries/flight/Common_api_flight.php';
class Spicejet extends Common_Api_Flight {
    var $master_search_data;
    var $search_hash;
    protected $token;
    private $end_user_ip = '127.0.0.1';
    public $api_session_id;
    function __construct($params = '') {
        parent::__construct(META_AIRLINE_COURSE, SPICEJET_FLIGHT_BOOKING_SOURCE);
        $this->CI = &get_instance();
        $this->CI->load->library('Converter');
        $this->CI->load->library('ArrayToXML');
        // $this->set_api_credentials();
        if(isset($params[0]['api_type'])){
            $update_api_id = true;
        }else{
            $update_api_id = false;
        }
        if(isset($params[0]['session_id_token'])){
            $session_id_token = $params[0]['session_id_token'];
        }else{
            $session_id_token = '';
        }
        $this->set_api_signature('', $update_api_id, $session_id_token);
    }
    /**
     * Setting Signature
    */
    public function set_api_signature($authentication_response = '', $update_api_id, $session_id_token) {
        // $this->api_session_id = '';
        if (empty($this->api_session_id) == true) {
            if (empty($authentication_response) == false) {
                $authentication_response = Converter::createArray($authentication_response);
                // debug($authentication_response);exit;
                //store in database
                if ($this->valid_create_session_response($authentication_response)) {
                    $authenticate_token = $authentication_response['s:Envelope']['s:Body']['LogonResponse']['Signature'];                    
                    $session_id = trim($authenticate_token);
                    $this->CI->api_model->update_api_session_id($this->booking_source, $session_id, $session_id_token);
                }
            } else {
              /* debug($computerName);
               exit();*/
                //$session_expiry_time = 15; //In minutes
                //$session_id = $this->CI->api_model->get_api_session_id($this->booking_source, $session_expiry_time);
                if($update_api_id == true){
                    $session_id ='';    
                }else{
                    $session_expiry_time = 20; //In minutes
                    $session_id = $this->CI->api_model->get_api_session_id($this->booking_source, $session_expiry_time, $session_id_token);
                }
                if (empty($session_id) == true) {
                    $authentication_request = $this->get_authentication_request(true);
                    if ($authentication_request['status'] == SUCCESS_STATUS) {
                        $authentication_request = $authentication_request['data'];
                        $authentication_response = $this->process_request($authentication_request ['request'], $authentication_request ['url'], $authentication_request ['SOAPAction'], $authentication_request ['remarks']);
                        $this->set_api_signature($authentication_response, $update_api_id, $session_id_token);
                    }
                }
            }
            if (empty($session_id) == false) {
                $this->api_session_id = $session_id;
            }
        }
    }
    /**
     * Search Request
     * @param unknown_type $search_id
     */
    public function get_search_request($search_id) {
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        $search_data = $this->search_data($search_id);
        /*if (empty($this->api_session_id) == true  || $this->search_allowed($search_data) === false) {*/
        if (empty($this->api_session_id) == true) {
            // return failure object as the login signature is not set
            debug(' Spicejet library in webserivecs get_search_request INside');
            exit();
            return $response;
        }
        /* get search criteria based on search id */
        
        if ($search_data ['status'] == SUCCESS_STATUS) {
           
            // Flight search RQ
            $search_request = $this->search_request($search_data ['data']);
            
            if ($search_request ['status'] = SUCCESS_STATUS) {
                $response ['status'] = SUCCESS_STATUS;
                $curl_request = $this->form_curl_params($search_request ['request'], $search_request ['url'], $search_request['SOAPAction']);
                $response ['data'] = $curl_request['data'];
            }
        }
         
        return $response;
    }
   private function search_allowed($search_data)
    {
        $search_data = $search_data['data'];
       /* if($search_data['trip_type'] !='multicity' && (empty($search_data['carrier'] == true) || $search_data['carrier'] == 'SG' )){*/
         if((empty($search_data['carrier'] == true) || $search_data['carrier'] == 'SG' )){
            $check_goair_airport = $this->CI->custom_db->single_table_records('goair_airport_list', 'origin', array('airport_code' => $search_data['from'], 'airport_code' => $search_data['to']));
            /*debug($this->CI->db->last_query());
            exit();*/
            if($check_goair_airport['status'] == true){
                return TRUE;
            } else {
                return FALSE;
            }
        }
        else{

            return FALSE;
        }
    }
     /**
     * Formates Search Request
     */
    private function search_request($search_data) {

        $response['status'] = SUCCESS_STATUS;
        $response['data']   = array();
        $search_params = $search_data;
        $trip_type = $search_params['trip_type'];
        $total_pax = $search_params['adult_config']+$search_params['child_config']+$search_params['infant_config'];
        if($search_params['cabin_class'] == 'economy'){
            $fare_clas="R";
            $product_class = '<a:string>GF</a:string>
                            <a:string>GS</a:string>';
        }
        else if($search_params['cabin_class'] == 'business'){
            $fare_clas="R";
            $product_class = '<a:string>GB</a:string>';
        }
        $advanced_filters = '<FlightNumber i:nil="true"></FlightNumber>
                        <FlightType>All</FlightType>
                        <PaxCount>'.$total_pax.'</PaxCount>
                        <Dow>Daily</Dow>
                        <CurrencyCode>INR</CurrencyCode>
                        <DisplayCurrencyCode i:nil="true"></DisplayCurrencyCode>
                        <DiscountCode i:nil="true"></DiscountCode>
                        <PromotionCode i:nil="true"></PromotionCode>
                        <AvailabilityType>Default</AvailabilityType>
                        <SourceOrganization i:nil="true"></SourceOrganization>
                        <MaximumConnectingFlights>20</MaximumConnectingFlights>
                        <AvailabilityFilter>ExcludeUnavailable</AvailabilityFilter>
                        <FareClassControl>Default</FareClassControl>
                        <MinimumFarePrice>0</MinimumFarePrice>
                        <MaximumFarePrice>0</MaximumFarePrice>
                        <ProductClassCode i:nil="true"></ProductClassCode>
                        <SSRCollectionsMode>None</SSRCollectionsMode>
                        <InboundOutbound>None</InboundOutbound>
                        <NightsStay>0</NightsStay>
                        <IncludeAllotments>false</IncludeAllotments>
                        <BeginTime i:nil="true"></BeginTime>
                        <EndTime i:nil="true"></EndTime>
                        <DepartureStations i:nil="true" xmlns:a="http://schemas.microsoft.com/2003/10/Serialization/Arrays"></DepartureStations>
                        <ArrivalStations i:nil="true" xmlns:a="http://schemas.microsoft.com/2003/10/Serialization/Arrays"></ArrivalStations>
                        <FareTypes xmlns:a="http://schemas.microsoft.com/2003/10/Serialization/Arrays">
                          <a:string>'.$fare_clas.'</a:string>
                          <a:string>GF</a:string>
                        </FareTypes>
                        <ProductClasses i:nil="true" xmlns:a="http://schemas.microsoft.com/2003/10/Serialization/Arrays">
                           '.$product_class.'
                        </ProductClasses>
                        <FareClasses i:nil="true" xmlns:a="http://schemas.microsoft.com/2003/10/Serialization/Arrays">
                        
                        </FareClasses>';
       $pax_config = '';
        
        if ($search_params ['adult_config'] > 0) {
            $pax_config .= '<PaxPriceType>
                                <PaxType>ADT</PaxType>
                                <PaxDiscountCode i:nil="true"></PaxDiscountCode>
                                <PaxCount>'.$search_params ['adult_config'].'</PaxCount>
                            </PaxPriceType>';
        }
        
        if ($search_params ['child_config'] > 0) {
            $pax_config .= '<PaxPriceType>
                                <PaxType>CHD</PaxType>
                                <PaxDiscountCode i:nil="true"></PaxDiscountCode>
                                <PaxCount>'.$search_params ['child_config'].'</PaxCount>
                            </PaxPriceType>';
        }


        $search_params['from'] = (is_array($search_params['from']) ? $search_params['from'] : array($search_params['from']));
        $search_params['to'] = (is_array($search_params['to']) ? $search_params['to'] : array($search_params['to']));
        $search_params['depature'] = (is_array($search_params['depature']) ? $search_params['depature'] : array($search_params['depature']));
        $search_params['return'] = (is_array(@$search_params['return']) ? @$search_params['return'] : array(@$search_params['return']));
       
        $AvailabilityRequest = '';
        $AvailabilityRequest_return ='';
        $AvailabilityRequest_return1 ='';
        for($i=0; $i<count($search_params['from']); $i++){
            $AvailabilityRequest .= '<AvailabilityRequest>
                        <DepartureStation>'.trim($search_params['from'][$i]).'</DepartureStation>
                        <ArrivalStation>'.trim($search_params['to'][$i]).'</ArrivalStation>
                        <BeginDate>'.$search_params['depature'][$i].'</BeginDate>
                        <EndDate>'.$search_params['depature'][$i].'</EndDate>
                        <CarrierCode>SG</CarrierCode>'.$advanced_filters.'
                         <PaxPriceTypes>'.$pax_config.'</PaxPriceTypes>
                        <JourneySortKeys xmlns:a="http://schemas.navitaire.com/WebServices/DataContracts/Common/Enumerations">
                        </JourneySortKeys>
                        <TravelClassCodes i:nil="true" xmlns:a="http://schemas.microsoft.com/2003/10/Serialization/Arrays"></TravelClassCodes>
                        <IncludeTaxesAndFees>true</IncludeTaxesAndFees>
                        <FareRuleFilter>Default</FareRuleFilter>
                        <LoyaltyFilter>MonetaryOnly</LoyaltyFilter>
                    </AvailabilityRequest>';
           
            if($search_params['trip_type'] == 'return') {  
                  $AvailabilityRequest_return1 .= '<AvailabilityRequest>
                    <DepartureStation>'.trim($search_params['to'][$i]).'</DepartureStation>
                    <ArrivalStation>'.trim($search_params['from'][$i]).'</ArrivalStation>
                    <BeginDate>'.$search_params['return'][$i].'</BeginDate>
                    <EndDate>'.$search_params['return'][$i].'</EndDate>
                     <CarrierCode>SG</CarrierCode>'.$advanced_filters.'
                         <PaxPriceTypes>'.$pax_config.'</PaxPriceTypes>
                        <JourneySortKeys xmlns:a="http://schemas.navitaire.com/WebServices/DataContracts/Common/Enumerations">
                        </JourneySortKeys>
                        <TravelClassCodes i:nil="true" xmlns:a="http://schemas.microsoft.com/2003/10/Serialization/Arrays"></TravelClassCodes>
                        <IncludeTaxesAndFees>true</IncludeTaxesAndFees>
                        <FareRuleFilter>Default</FareRuleFilter>
                        <LoyaltyFilter>MonetaryOnly</LoyaltyFilter>
                    </AvailabilityRequest>';  
            }   

        }
        $xml_request[0] = '<?xml version="1.0" encoding="utf-8"?>
                        <s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
                            ' . $this->soap_header () . '
                            <s:Body>
                                <GetAvailabilityRequest xmlns="http://schemas.navitaire.com/WebServices/ServiceContracts/BookingService">
                                    <TripAvailabilityRequest xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Booking" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
                                        <AvailabilityRequests>
                                            ' . $AvailabilityRequest . '
                                            '.$AvailabilityRequest_return1.'
                                        </AvailabilityRequests>
                                    </TripAvailabilityRequest>
                                </GetAvailabilityRequest>
                            </s:Body>
                        </s:Envelope>';
        if($search_params['trip_type'] == 'return' && $search_params['is_domestic'] == SUCCESS_STATUS) { 
            $authentication_request = $this->get_authentication_request(true);
            if ($authentication_request['status'] == SUCCESS_STATUS) {
                $authentication_request = $authentication_request['data'];
                $authentication_response = $this->process_request($authentication_request ['request'], $authentication_request ['url'], $authentication_request ['SOAPAction'], $authentication_request ['remarks']);

                $authentication_response = Converter::createArray($authentication_response);
                //store in database
                
                if ($this->valid_create_session_response($authentication_response)) {
                    $authenticate_token = $authentication_response['s:Envelope']['s:Body']['LogonResponse']['Signature'];                    
                    $this->api_session_id_ret = trim($authenticate_token);
                }
            }
           /* $xml_request[1] = '<?xml version="1.0" encoding="utf-8"?>
                            <s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
                                ' . $this->soap_header_ret () . '
                                <s:Body>
                                <GetAvailabilityRequest xmlns="http://schemas.navitaire.com/WebServices/ServiceContracts/BookingService">
                                    <TripAvailabilityRequest xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Booking" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
                                        <AvailabilityRequests>
                                            ' . $AvailabilityRequest_return . '
                                        </AvailabilityRequests>
                                    </TripAvailabilityRequest>
                                </GetAvailabilityRequest>
                            </s:Body>
                        </s:Envelope>';*/
        }
        $request ['request'] = $xml_request;
        $request ['url'] = $this->config['end_point']['booking'];
        $request ['SOAPAction'] = 'http://schemas.navitaire.com/WebServices/IBookingManager/GetAvailability';
        $request ['status'] = SUCCESS_STATUS;
        // debug($request);exit;
        return $request;
        // debug($request);exit;
    }
    /**
     * Soap Header to be used
     */
    function soap_header() {
        $header = '';
        $header .= '<s:Header>
            <h:ContractVersion xmlns:h="http://schemas.navitaire.com/WebServices">' . $this->config ['contract_version'] . '</h:ContractVersion>
            <h:EnableExceptionStackTrace xmlns:h="http://schemas.navitaire.com/WebServices">false</h:EnableExceptionStackTrace>
            <h:MessageContractVersion i:nil="true" xmlns:h="http://schemas.navitaire.com/WebServices" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">4.1.1</h:MessageContractVersion>
            <h:Signature xmlns:h="http://schemas.navitaire.com/WebServices">' . $this->api_session_id . '</h:Signature>
        </s:Header>';
        return $header;
    }
     function soap_header_ret() {
        $header = '';
        $header .= '<s:Header>
            <h:ContractVersion xmlns:h="http://schemas.navitaire.com/WebServices">' . $this->config ['contract_version'] . '</h:ContractVersion>
            <h:EnableExceptionStackTrace xmlns:h="http://schemas.navitaire.com/WebServices">false</h:EnableExceptionStackTrace>
            <h:MessageContractVersion i:nil="true" xmlns:h="http://schemas.navitaire.com/WebServices" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">4.1.1</h:MessageContractVersion>
            <h:Signature xmlns:h="http://schemas.navitaire.com/WebServices">' . $this->api_session_id_ret . '</h:Signature>
        </s:Header>';
        return $header;
    }
    /**
     * Returns Flight List
     * @param unknown_type $search_id
     */
    public function get_flight_list_old($flight_raw_data, $search_id) {
        // debug($flight_raw_data);exit;
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        $search_data = $this->search_data($search_id);
       
        if ($search_data ['status'] == SUCCESS_STATUS) {
            foreach($flight_raw_data as $key => $raw_data){
                $api_response = Converter::createArray($raw_data);
                // debug($api_response);exit;
                if ($this->valid_search_result($api_response, $search_data) == TRUE) {
                    // debug($api_response);exit;
                    $flight_data = $this->format_search_data_response($api_response, $search_id, $search_data ['data'], $key);
                    $clean_format_data['FlightDataList']['JourneyList'] [$key] = $flight_data[0];
                    if ($clean_format_data) {
                        $response ['status'] = SUCCESS_STATUS;
                    } else {
                        $response ['status'] = FAILURE_STATUS;
                    }
                }
                else {
                    $response ['status'] = FAILURE_STATUS;
                }
                if ($response ['status'] == SUCCESS_STATUS) {
                    $response ['data'] = $clean_format_data;
                }
            }
           
        }
        else {
            $response ['status'] = FAILURE_STATUS;
        }

       
        return $response;
    }
     public function get_flight_list($flight_raw_data, $search_id) {
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        $search_data = $this->search_data($search_id);
        $clean_format_data = array();
        $statu_array = array();
        if(!is_array($flight_raw_data)){
            $flight_raw_data = array($flight_raw_data);    
        }
        if ($search_data ['status'] == SUCCESS_STATUS) {
            if ($this->valid_search_result($flight_raw_data) == TRUE) {
                foreach($flight_raw_data as $key => $raw_data){
                    $api_response = Converter::createArray($raw_data);            
                    //debug($api_response);exit;
                    $flight_data = $this->format_search_data_response($api_response, $search_id, $search_data ['data'], $key);
                    
                    if(valid_array($flight_data)){
                        $clean_format_data['FlightDataList']['JourneyList']  = $flight_data;
                    }
                    if ($clean_format_data) {
                        $statu_array[$key] = 'true'; 
                        // $response ['status'] = SUCCESS_STATUS;
                    } else {
                        $statu_array[$key] = 'false'; 
                        // $response ['status'] = FAILURE_STATUS;
                    }
            }
            if (in_array('false', $statu_array, true)) {
                $response ['status'] = FAILURE_STATUS;
            }
            else{
                $response ['status'] = SUCCESS_STATUS;
                $response ['data'] = $clean_format_data;
               
            }
        }
        else{
            $response ['status'] = FAILURE_STATUS;
        }
           
    }
    else {
        $response ['status'] = FAILURE_STATUS;
    }
    return $response;
    }
      /**
     * Formates Search Response
     * Enter description here ...
     * @param unknown_type $search_result
     * @param unknown_type $search_data
     */
    function format_search_data_response($search_result, $search_id, $search_data, $res_key) {
  
        $trip_type = isset($search_data ['is_domestic']) && !empty($search_data ['is_domestic']) ? 'domestic' : 'international';
        $Results = $search_result['s:Envelope']['s:Body']['GetAvailabilityByTripResponse']['GetTripAvailabilityResponse']['Schedules']['ArrayOfJourneyDateMarket'];
        $response = array();
        if(valid_array($Results)){
            $Results = force_multple_data_format($Results);
            $itinerary_key_details = array();
            $price_itinerary_keys = array();
            $flight_list= array();

            foreach($Results as $result_k => $result_v){
                if (isset ( $result_v ['JourneyDateMarket'] ) == false) {
                   return false;
                }

                $JourneyDateMarket = force_multple_data_format($result_v['JourneyDateMarket']);
                // debug($JourneyDateMarket);exit;
                foreach($JourneyDateMarket as $jouney_k => $jouney_v){
                    if(valid_array($jouney_v['Journeys'])){
                        $jounry_list = force_multple_data_format($jouney_v['Journeys']['Journey']);
                        // debug($jounry_list);exit;
                        foreach( $jounry_list as $flight_k => $flight_value) {
                          
                            $Segments = force_multple_data_format ( $flight_value ['Segments'] ['Segment'] );
                            // debug($Segments);
                            $flight_details = array();
                            
                            $key1 = array();
                            $key1 ['key'] [0] ['JourneySellKey'] [] = $flight_value ['JourneySellKey'];
                            $key1 ['key'] [0] ['booking_source'] = $this->booking_source;
                            if($res_key == 0){
                                $key1 ['key'] [0] ['Signature'] = $this->api_session_id;
                            }
                            else{
                                $key1 ['key'] [0] ['Signature'] = $this->api_session_id_ret;
                            }
                            $flight_details = $this->flight_segment_summary($Segments, $flight_k, $key1, $search_data);
                            
                            if (valid_array($flight_details)) {
                                foreach ($flight_details as $f__key => $flight_detail) {
                                    // $itinerary_key_details[$f__key][] = $this->itinerary_key_details ( $flight_detail ['key'], $flight_detail ['raw'] ['fare'] );
                                    unset($flight_detail['key']);
                                    unset($flight_detail['raw']);
                                    $flight_list [$result_k] [] = $flight_detail;
                                }
                            }
                        }
                    }
                    
                }
            }
            // debug($flight_list);exit;
            // if(valid_array($itinerary_key_details)) {

            //     foreach($itinerary_key_details as $___ky => $arr_key){
            //         $itenary_price_det = $this->get_itinerary_price ( $arr_key );
                    
            //         if($itenary_price_det['status'] == SUCCESS_STATUS) {
            //             $itinerary_price_details[] = $itenary_price_det['data'];    
            //         }
            //     }
            // }
            // if (valid_array($itinerary_price_details)) {
            //     $this->update_search_itinerary_price_details ( $flight_list, $itinerary_price_details, $trip_type, $search_data );
            // }
            // If Round way international then make combinations else return as it is.
            if ((empty ( $search_data ['is_domestic'] ) == true && $search_data ['trip_type'] == 'return') == true) {
                // Combine data//0,1,2,3,4,5
                $response[0] = Common_Api_Flight::form_flight_combination ( $flight_list  [0], $flight_list [1],'',$search_id );
            }
            else if(($search_data ['trip_type'] == 'multicity') == true){
                if (valid_array ( $flight_list  )) {
                    $journey_cnt = COUNT ( $flight_list);
                    $list = '';
                    if (COUNT ( $search_data ['from'] ) == $journey_cnt) {
                        for($i = 0; $i < $journey_cnt; $i ++) {
                            if (valid_array ( $list )) {
                                $onward = $list;
                            } else {
                                $onward = $flight_list [$i];
                            }
                            
                            if (isset ( $flight_list [$i + 1] )) {
                                $list = Common_Api_Flight::form_flight_combination ( $onward, $flight_list [$i + 1],'', $search_id );
                            }
                        }
                    }
                    
                    $list = array_slice ( $list, 0, 41 );
                    $response[0] = $list;
                }
            }
            else{
                $response = $flight_list;
            }
        }
     //    if($_SERVER['REMOTE_ADDR'] == '184.95.42.178'){
     //    debug($response);exit;
     // }
        // debug($response);exit;
        
        return $response;
      
    }
    /**
     * update price details
     *
     * @param array $flight_list            
     * @param array $price_list         
     */
    function update_search_itinerary_price_details(& $flight_list, $price_list, $trip_type = 'domestic', $search_data) {
        // debug($price_list);
        // debug($flight_list);
        // exit;
        foreach ( $flight_list  as $j_k => & $j_v ) {
            foreach ( $j_v as $f_k => &$f_v ) {
                
                $keys = $f_v ['ResultToken']; // summary of keys
                foreach ( $keys as $key ) {
                    $journey_key = $key ['JourneySellKey'] [0];
                    
                    foreach ( $key ['FareSellKey'] as $k_k => & $k_v ) {
                        $segment_key = $key ['SegmentSellKey'] [$k_k]; // need to read from segment and not journey directly
                        $fare_sell_key = $k_v;
                        $f_k = $segment_key . '*_*' . $fare_sell_key;
                        foreach($price_list as $p__k => $price_l) {
                            
                            if(isset($price_l [$journey_key] ['fare'] [$f_k])) {
                               
                                // if($f_v['FlightDetails']['Details'][0][0]['FlightNumber'] == 22){
                                     
                                //      debug($price_l [$journey_key] ['fare'] [$f_k]);
                                // }
                                $this->format_itineray_price_details_new($f_v ['Price'],$price_l [$journey_key] ['fare'] [$f_k], $search_data) ;
                                //  if($f_v['FlightDetails']['Details'][0][0]['FlightNumber'] == 22){
                                //     debug($price_l [$journey_key] ['fare'] [$f_k]);
                                //     echo "original";
                                //     debug($f_v ['Price']);
                                // }

                                $cabin_class = $price_l [$journey_key] ['fare'] [$f_k]['ProductClass']; 
                                $baggage = @$price_l [$journey_key] ['fare'] [$f_k]['PaxSegments']['BaggageAllowanceWeight']." ".@$price_l [$journey_key] ['fare'] [$f_k]['PaxSegments']['BaggageAllowanceWeightType'];
                              
                               /*if($cabin_class == 'GS'){
                                    $cabin_class = 'GoSmart Economy';
                                } else if($cabin_class == 'SP'){
                                    $cabin_class = 'GoSpecial Economy';
                                }else if($cabin_class == 'BC'){
                                    $cabin_class = 'GoSmartCorporate Economy';
                                }
                                else if($cabin_class == 'GB'){
                                    $cabin_class = 'GoBusiness';
                                }
                                else if($cabin_class == 'GC'){
                                    $cabin_class = 'GoBusinessCorporate';
                                }*/
                                $deatils_array =  array();
                                foreach ($f_v['FlightDetails']['Details'] as $det_key => $leg) {
                                    foreach($leg as $leg_key => $leg_value){
                                        $leg_value['CabinClass'] = $cabin_class;
                                        $leg_value['Attr']['Baggage'] = $baggage;
                                        $deatils_array[$det_key][$leg_key] = $leg_value;
                                    }
                                }
                                $f_v['FlightDetails']['Details'] = $deatils_array;
                               
                                break;

                            }
                           
                           
                        }
                    }
                   
                }
                $f_v['ResultToken'] = serialized_data($f_v['ResultToken']);
            }

        }
    }
     /**
     * Get flight details only
     *
     * @param array $segment
     */
    private function flight_segment_summary($segments, $journey_number, & $key1, $search_data, $cache_fare_object = false) {
        // debug($segments);exit;
        $summary = array();
        $flight_details = array();
        $price_details = array();
        $final_flight = array();
        $itineray_price = array();
        $fares_data = array();
        $SegmentSellKey = array ();
       
        $flightNumberList = array();
        // debug($segments);exit;
        $total_duration = 0;
        $seg_count = count($segments);
        foreach($segments as $k => $v){
            if(valid_array($v['Fares'])){
                $Fares = @$v['Fares'];
                $total_duration = $this->wating_segment_time($segments[0]['DepartureStation'], $segments[$seg_count-1]['ArrivalStation'], $segments[0]['STD'], $segments[$seg_count-1]['STA']);
                $fares_data[]['Fare'] = force_multple_data_format(@$Fares['Fare']);
                // debug($fares_data);exit;
                $available_seats = @$fares_data[0]['Fare'][0]['AvailableCount'];
                $legs = $v['Legs']['Leg'];
                $legs = force_multple_data_format($legs);
                $is_leg = true;
                $attr = array();
                // $attr ['action_status_code'] = $v ['ActionStatusCode'];
                $attr ['AvailableSeats'] = $available_seats;
                if($search_data['is_domestic'] == 1){
                    $attr ['Baggage'] = '15 Kg'; // neeed to check
                    $attr ['CabinBaggage'] = '7 Kg'; // neeed to check
                }
                else{
                    $attr ['Baggage'] = '30 Kg'; // neeed to check
                    $attr ['CabinBaggage'] = '7 Kg'; // neeed to check
                }
                foreach ($legs as $l_k => $l_v) {
                    // foreach($fares_data as $fare_key => $fares_value){  
                        $origin_code = $l_v ['DepartureStation'];
                        $destination_code = $l_v ['ArrivalStation'];
                        $departure_dt = str_replace('T',' ',$l_v ['STD']);
                        $arrival_dt = str_replace('T',' ',$l_v ['STA']);
                        $operator_code = $l_v['FlightDesignator']['a:CarrierCode'];
                        $operator_name = 'Spicejet';
                        $flight_number = $l_v['FlightDesignator']['a:FlightNumber'];
                        $no_of_stops = 0;
                        $cabin_class = ''; // fix me
                        $duration = $this->wating_segment_time($l_v['DepartureStation'], $l_v['ArrivalStation'], $l_v['STD'], $l_v['STA']);
                        $stop_over ='';
                        // debug($attr);exit;
                        if(count($segments) > 1 && ($k == count($segments)-1)){
                           $total_duration = $total_duration;
                        }
                        else{
                            $total_duration = 0;
                        }
                        $origin_terminal = $l_v['LegInfo']['ArrivalTerminal'];
                        $destination_terminal = $l_v['LegInfo']['DepartureTerminal'];
                        $details[] = $this->format_summary_array($journey_number, $origin_code, $destination_code, $departure_dt, $arrival_dt, $operator_code, $operator_name, $flight_number, $no_of_stops, $cabin_class, '', '', $duration, $is_leg, $attr, $stop_over, $origin_terminal, $destination_terminal, $total_duration);
                        // $details[]['AccumulatedDuration'] = $total_duration;
                      
                     
                        if(in_array($v ['SegmentSellKey'],$SegmentSellKey) == false) {
                            $SegmentSellKey [] = $v ['SegmentSellKey'];
                        }
                        $flightNumberList [] = $l_v['FlightDesignator']['a:FlightNumber'];
                        $is_leg = false;
                    // }
                }
                $segment_intl [] = $v ['International'];
              
                $fare_key_info = force_multple_data_format($v['Fares']['Fare']);
                foreach ( $fare_key_info as $f_k => $f_v ) {
                    
                    $fare[$f_k][$f_v ['FareSellKey']] = $f_v;
                    // $key ['key'] [$journey_number] ['JourneySellKey'] = $key ['key'] [$journey_number]['JourneySellKey'][0];
                    $FareSellKey[$f_k][] = $f_v ['FareSellKey'];
                }
              
            }
            
        }
        $flight_num = array_unique($flightNumberList);
        $seg_count = count($flight_num);
        if(valid_array($flightNumberList)){
        $key1 ['key'] [0] ['SegmentSellKey'] = $SegmentSellKey;
        $key1 ['key'] [0] ['FlightNumber'] = $flightNumberList;
        $key1 ['key'] [0] ['is_intl'] = $segment_intl;
        $fares_count = count($fares_data[0]['Fare']);
        $flight_details ['Details'] [0] = $details;
       
        $flight_ar = array();
        for($i=0; $i<$fares_count; $i++){
            $price = $this->format_itineray_price_details($fares_data, $i, $search_data, $seg_count);
            //$price = array();
            $flight_ar ['FlightDetails'] = $flight_details;
            

            // $key1['key'][0]['flight_search_results'][0][0] = $flight_ar; 
            $AirlineRemark = 'SPICEJET Booking';
            $IsLCC = 1;
            $IsRefundable = true; // need to check
            $ResultIndex ='';
            $flight_ar ['raw'] ['fare'] = $fare[$i];
           
          
            $key1 ['key'][0]['ResultIndex'] = $ResultIndex; 
            $key1 ['key'] [0]['IsLCC'] = $IsLCC;
            $key1 ['key'] [0] ['FareSellKey'] = array_unique($FareSellKey[$i]);
      
            $flight_ar1 ['key'] = $key1 ['key'];
            $flight_ar ['ResultToken'] = serialized_data($flight_ar1['key']);
            $is_refundable = $IsRefundable;
            $flight_ar ['Attr']['IsRefundable'] = $is_refundable;
            $flight_ar ['Attr']['AirlineRemark'] = $AirlineRemark;
            $flight_ar ['Attr']['FareType'] = $price['ProductClass'];
            unset($price['ProductClass']);
            $flight_ar ['Price'] = $price;
            // $flight_ar ['Attr']['FareSellKey'] = $fare_sell_key;
            if(valid_array($price)){
                $final_flight[$i] = $flight_ar;
            }

            unset($flight_ar);
        }
        $response = $final_flight;
         //debug($response);exit; 
        return $response;
 
        }
        
    }
  
    /**
     * Balu A
     * Calculates the flight segment duration based on airport time zone offset
     * @param $departure_airport_code
     * @param $arrival_airport_code
     * @param $departure_datetime
     * @param $arrival_datetime
     */
    private function wating_segment_time($arrival_airport_city, $departure_airport_city, $arrival_datetime, $departure_datetime) {
        $departure_datetime = date('Y-m-d H:i:s', strtotime($departure_datetime));

        $arrival_datetime = date('Y-m-d H:i:s', strtotime($arrival_datetime));

        //Get TimeZone of Departure and Arrival Airport
        $departure_timezone_offset = $GLOBALS['CI']->flight_model->get_airport_timezone_offset($departure_airport_city, $departure_datetime);
        $arrival_timezone_offset = $GLOBALS['CI']->flight_model->get_airport_timezone_offset($arrival_airport_city, $arrival_datetime);
        //Converting TimeZone to Minutes
        $departure_timezone_offset = $this->convert_timezone_offset_to_minutes($departure_timezone_offset);
        $arrival_timezone_offset = $this->convert_timezone_offset_to_minutes($arrival_timezone_offset);
        //Getting Total time difference between 2 airports
        $timezone_offset = ($arrival_timezone_offset - $departure_timezone_offset);
        //Calculating the Waiting time between 2 segments
        $current_segment_arr = strtotime($arrival_datetime);
        $next_segment_dep = strtotime($departure_datetime);
        $segment_waiting_time = ($next_segment_dep - $current_segment_arr);

        //Converting into minutes
        $segment_waiting_time = ($segment_waiting_time) / 60; //Converting into minutes
        //Updating the total duration with time zone offset difference
        $segment_waiting_time = ($segment_waiting_time + $timezone_offset);
        return $segment_waiting_time;
    }
     /**
     * Converts the time zone offset to minutes
     * @param unknown_type $timezone_offset
     */
    private function convert_timezone_offset_to_minutes($timezone_offset) {
        $add_mode_sign = $timezone_offset[0];
        $time_zone_details = explode(':', $timezone_offset);
        $hours = abs(intval($time_zone_details[0]));
        $minutes = abs(intval($time_zone_details[1]));
        $minutes = $hours * 60 + $minutes;
        $minutes = ($add_mode_sign . $minutes);
        return $minutes;
    }

    function itinerary_key_details($flight_keys, $raw_fare_object) {
       foreach ( $flight_keys as $journey_number => $v ) {
            foreach ( $v ['JourneySellKey'] as $jk => $jv ) {
                $key_obj = array ();
                $key ['journey_sell_key'] = $jv;
                $key ['fare'] = array ();
                foreach ( $v ['FareSellKey'] as $fk => $fv ) {
                    $fare = array ();
                    $fare ['fare_sell_key'] = $fv;
                    $fare ['segment_sell_key'] = $v ['SegmentSellKey'] [$fk];
                    $fare ['is_allotment_market_fare'] = (@isset ( $raw_fare_object [$fk] ['IsAllotmentMarketFare'] ) ? $raw_fare_object [$fk] ['IsAllotmentMarketFare'] : false);
                    $fare ['flight_number'] = $v ['FlightNumber'] [$fk];
                    $fare ['is_intl'] = $v ['is_intl'] [$fk];
                    $key ['fare'] [] = $fare;
                }
            }
        }
        // debug($key);exit;
        return $key;
    }
    /**
     * Itinerary pricing details RQ
     *
     * @param array $key_details
     *          - Key details received from availability RQ
     * @return string
     */
    private function get_itinerary_price($key_details) {
        // debug($this->config);exit;
        $response ['status'] = FAILURE_STATUS;
        $response ['data'] = array ();
        $CI = & get_instance ();
        $key_list = $this->create_request_batch_keys ( $key_details );
        // debug($key_list);exit;
        foreach ( $key_list as $k => $v ) {
            $price_itinerary_request = $this->price_itinerary_request( $this->master_search_data, $v );
            if ($price_itinerary_request['status'] == SUCCESS_STATUS) {
                $price_itinerary_response = $this->process_request($price_itinerary_request ['request'], $price_itinerary_request ['url'], $price_itinerary_request ['SOAPAction'], $price_itinerary_request ['remarks']);
                $price_itinerary_response = Converter::createArray ( $price_itinerary_response );
                if ($this->valid_price_result($price_itinerary_response) == TRUE) {
                   $response ['status'] = SUCCESS_STATUS;
                   $clean_format_data = $this->format_price_data_response ( $price_itinerary_response, $key_details );
                   $response ['data'] = array_merge ( $response ['data'], $clean_format_data ['data'] );
                   $response ['status'] = SUCCESS_STATUS;
                }
            }
        }
        // debug($response);exit;
        return $response;
    }
    /**
     * Create batch keys for RQs by grouping them - with unique combinations
     *
     * @param array $key_details            
     * @return array - batch key group array
     */
    private function create_request_batch_keys($key_details) {
        $batch_flights = array ();
        $batch_number = 0;
        $request_batch = array ();
        foreach ( $key_details as $k => $flight_segs ) {
            if (count ( $flight_segs ['fare'] ) == 1) {
                // direct flights to first batch
                $batch_number = 1;
                // [Batch Number] [Stop Number]
                $batch_flights [1] [0] [] = $flight_segs ['fare'] [0] ['flight_number'];
            } else {
                $batch_number = $this->find_price_request_batch_number ( $flight_segs, $batch_flights );

            }
            $request_batch [$batch_number] [] = $flight_segs;
        }
        return $request_batch;
    }
    /**
     * Find batch number
     *
     * @param array $flight_segs            
     * @param array $batch_flights          
     */
    private function find_price_request_batch_number($flight_segs, & $batch_flights) {
        $batch = - 1;
        $current_batch = 0;
        while ( $batch < 0 ) {
            if (isset ( $batch_flights [$current_batch] ) == true) {
                $batch_pointer = $batch_flights [$current_batch];
                // check occurance in current batch pointer
                $failure_count = 0;
                foreach ( $flight_segs ['fare'] as $fk => $fv ) {
                    // check if current number of segments is matching
                    if (isset ( $batch_pointer [$fk] ) == true) {
                        // compare in current batch
                        if (in_array ( $fv ['flight_number'], $batch_pointer [$fk] ) == true) {
                            $failure_count ++;
                            break;
                        }
                    } else {
                        // segment count is more than batch pointer - so we found batch id
                        // $batch = $current_batch;
                        break;
                    }
                }
            } else {
                $failure_count = 0;
                $batch = $current_batch;
                $batch_flights [$batch] = array ();
            }
            
            if ($failure_count > 0) {
                $current_batch ++;
            } else {
                $batch = $current_batch;
            }
        }
        // push flights inside batch
        if ($batch > - 1) {
            foreach ( $flight_segs ['fare'] as $k => $v ) {
                $batch_flights [$batch] [$k] [] = $v ['flight_number'];
            }
        }
        return $batch;
    }
    /**
     * Itinerary Price Request GOAIR
     */
    private function price_itinerary_request($search_data, $key_details) {
        $total_pax = $search_data['adult_config']+$search_data['child_config'];
        $pax_config = '<a:PaxPriceType>';
        if ($search_data ['adult_config'] > 0) {
            $pax_config .= str_repeat ( '<PaxPriceType>
                                <PaxType>ADT</PaxType>
                                <PaxDiscountCode i:nil="true"></PaxDiscountCode>
                                
                            </PaxPriceType>', $search_data ['adult_config'] );
        }
        
        if ($search_data ['child_config'] > 0) {
            $pax_config .= str_repeat ( '<PaxPriceType>
                                <PaxType>CHD</PaxType>
                                <PaxDiscountCode i:nil="true"></PaxDiscountCode>
                                
                            </PaxPriceType>', $search_data ['child_config'] );
        }
        $pax_config .= '</a:PaxPriceType>';
        
        $journey_sell_keys = '<a:JourneySellKeys>';
       // debug($key_details);exit;
        foreach ( $key_details as $k => $v ) {
            if($k<6){
                $journey_sell_key = $v ['journey_sell_key'];
                $fare_sell_key = '';
                foreach ( $v ['fare'] as $f_k => $f_v ) {
                    $fare_sell_key .= $f_v ['fare_sell_key'] . '^';
                }
                $fare_sell_key = substr ( $fare_sell_key, 0, - 1 );
                $journey_sell_keys .= '<a:SellKeyList>
                                            <a:JourneySellKey>' . $journey_sell_key . '</a:JourneySellKey>
                                            <a:FareSellKey>' . $fare_sell_key . '</a:FareSellKey>
                                        <a:StandbyPriorityCode i:nil="true" />
                                    </a:SellKeyList>';
            }
            
        }
        $journey_sell_keys .= '</a:JourneySellKeys>';
        
        $price_iti_request = '<?xml version="1.0" encoding="UTF-8"?>
        <s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
            ' . $this->soap_header () . '
            <s:Body>
                <PriceItineraryRequest xmlns="http://schemas.navitaire.com/WebServices/ServiceContracts/BookingService">
                    <ItineraryPriceRequest xmlns:a="http://schemas.navitaire.com/WebServices/DataContracts/Booking" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
                        <a:TypeOfSale i:nil="true"></a:TypeOfSale>
                        <a:SSRRequest i:nil="true"></a:SSRRequest>
                        <a:SellRequest i:nil="true"/>
                        <a:SellByKeyRequest>
                            <a:ActionStatusCode>NU</a:ActionStatusCode>
                                ' . $journey_sell_keys . '
                                ' . $pax_config . '
                            <a:CurrencyCode>' . get_application_default_currency () . '</a:CurrencyCode>
                            <a:SourcePOS xmlns:b="http://schemas.navitaire.com/WebServices/DataContracts/Common" />
                            <a:PaxCount>' . $total_pax . '</a:PaxCount>
                            <a:TypeOfSale i:nil="true" />
                            <a:LoyaltyFilter>MonetaryOnly</a:LoyaltyFilter>
                            <a:IsAllotmentMarketFare>false</a:IsAllotmentMarketFare>
                            <a:SourceBookingPOS i:nil="true" xmlns:b="http://schemas.navitaire.com/WebServices/DataContracts/Common"/>
                            <a:PreventOverLap>false</a:PreventOverLap>
                            <a:ReplaceAllPassengersOnUpdate>false</a:ReplaceAllPassengersOnUpdate>
                            <a:ServiceBundleList i:nil="true" xmlns:b="http://schemas.microsoft.com/2003/10/Serialization/Arrays"/>
                            <a:ApplyServiceBundle>No</a:ApplyServiceBundle>
                        </a:SellByKeyRequest>
                        <a:PriceJourneyWithLegsRequest i:nil="true" />
                        <a:PriceItineraryBy>JourneyBySellKey</a:PriceItineraryBy>
                        <a:BookingStatus>Default</a:BookingStatus>
                    </ItineraryPriceRequest>
                </PriceItineraryRequest>
            </s:Body>
        </s:Envelope>';
        
        // debug($this->config);die; 
        //file_put_contents('xml_logs/Goair/PriceItineraryRequest.xml', $request ['payload'] ); // Done
        $request['request'] = $price_iti_request;
        $request ['url'] = $this->config ['end_point'] ['booking'];
        $request ['SOAPAction'] = 'http://schemas.navitaire.com/WebServices/IBookingManager/GetItineraryPrice';
        $request ['remarks'] = 'Price Itineray(Go Air)';
        $request ['status'] = SUCCESS_STATUS;
        // debug($request);exit;
        return $request;
    }
    /**
     * Format price data RS
     */
    function format_price_data_response($price_result) {
        $Journey = $price_result ['s:Envelope'] ['s:Body'] ['PriceItineraryResponse'] ['Booking'] ['Journeys'] ['Journey'];
        $Journey = force_multple_data_format ( $Journey );
        $price_list = array ();
        foreach ( $Journey as $j_k => $j_v ) {
            /*
             * debug($Journey[0]);
             * exit;
             */
            $journey_key = $j_v ['JourneySellKey'];
            $Segment = force_multple_data_format ( $j_v ['Segments'] ['Segment'] );
            foreach ( $Segment as $s_k => $s_v ) {
                $segment_sell_key = $s_v ['SegmentSellKey'];
                $Fare = force_multple_data_format ( $s_v ['Fares'] ['Fare'] );
               foreach ( $Fare as $f_k => $f_v ) {
                    $f_k = $segment_sell_key . DB_SAFE_SEPARATOR . $f_v ['FareSellKey'];
                    $price_list [$journey_key] ['fare'] [$f_k] = $f_v;
                    $segment = force_multple_data_format($s_v['PaxSegments']['PaxSegment']);
                    $price_list [$journey_key] ['fare'] [$f_k] ['PaxSegments']= $segment[0];
                }
            }
        }
        $response ['data'] = $price_list;

        return $response;
    }
     /**
     * Formates Itineray Price Details
     * @param unknown_type $itineray_price
     */
    private function format_itineray_price_details($segment_price, $i, $search_data, $seg_count) {
       
       // debug($segment_price);exit;
        $price = array();
        $passenger_breakup = array();
        
        $total_base_fare = 0;
        $total_tax = 0;
        $pax_tax = 0;
        $pax_base_fare = 0;
        $currency_code ='';
        $itineray_price = array();
        $class_of_service = '';
        $adult_base_price = 0;
        $child_base_price = 0;
        $infant_base_price = 0;
        $adult_tax = 0;
        $child_tax = 0;
        $infant_tax = 0;
        foreach ($segment_price as $it_key => $it_value) {

            if(isset($it_value['Fare'][$i])){
                 $itineray_price = $it_value['Fare'][$i];
            }

            $cabin_class= $itineray_price['ProductClass'];
            if($cabin_class == 'SS'){
                $cabin_class = 'SgSmart Fare';
            } else if($cabin_class == 'SP'){
                $cabin_class = 'GoSpecial Fare';
            }else if($cabin_class == 'BC'){
                $cabin_class = 'GoSmartCorporate Fare';
            }
            else if($cabin_class == 'GB'){
                $cabin_class = 'GoBusiness Fare';
            }
            else if($cabin_class == 'GF'){
                $cabin_class = 'GoFlexi Fare';
            }
            else if($cabin_class == 'GC'){
                $cabin_class = 'GoBusiness Corporate Fare';
            }
            $class_of_service = $itineray_price['ClassOfService'];
            if(valid_array($itineray_price) && valid_array($itineray_price['PaxFares'])){
                $pax_fare_list = $itineray_price['PaxFares']['PaxFare'];
                $pax_fare_list = force_multple_data_format($pax_fare_list);
                
                foreach($pax_fare_list as $pax_key => $pax_fare){
                    $pax_type = $pax_fare['PaxType'];
                    $fare_list = force_multple_data_format($pax_fare['ServiceCharges']['BookingServiceCharge']);
                    foreach($fare_list as $f_key => $fare){
                        $currency_code = $fare['CurrencyCode'];
                        $pax_count = 1;
                        if($pax_type == 'ADT'){
                            $pax_count = $search_data['adult_config'];
                            if($fare['ChargeType'] == 'FarePrice'){
                                $adult_base_price += $pax_count*$fare['Amount'];
                            }
                            $passenger_breakup[$pax_type]['BasePrice'] = $adult_base_price;
                            if($fare['ChargeType'] == 'Tax'){
                                $adult_tax += $pax_count*$fare['Amount'];
                            }
                            $passenger_breakup[$pax_type]['Tax'] = $adult_tax;
                            $passenger_breakup[$pax_type]['TotalPrice'] = $adult_base_price+$adult_tax;
                            $passenger_breakup[$pax_type]['PassengerCount'] = $pax_count;  
                        }
                        if($pax_type == 'CHD'){
                            $pax_count = $search_data['child_config'];
                             if($fare['ChargeType'] == 'FarePrice'){
                                $child_base_price += $pax_count*$fare['Amount'];
                            }
                            $passenger_breakup[$pax_type]['BasePrice'] = $child_base_price;
                            if($fare['ChargeType'] == 'Tax'){
                                $child_tax += $pax_count*$fare['Amount'];
                            }
                            $passenger_breakup[$pax_type]['Tax'] = $child_tax;
                            $passenger_breakup[$pax_type]['TotalPrice'] = $adult_base_price+$child_tax;
                            $passenger_breakup[$pax_type]['PassengerCount'] = $pax_count;  
                        }
                        if($pax_type == 'INF'){
                            $pax_count = $search_data['infant_config'];
                            if($fare['ChargeType'] == 'FarePrice'){
                                $infant_base_price += $pax_count*$fare['Amount'];
                            }
                            $passenger_breakup[$pax_type]['BasePrice'] = $infant_base_price;
                            if($fare['ChargeType'] == 'INF'){
                                $infant_tax += $pax_count*$fare['Amount'];
                            }
                            $passenger_breakup[$pax_type]['Tax'] = $infant_tax;
                            $passenger_breakup[$pax_type]['TotalPrice'] = $infant_base_price+$infant_tax;
                            $passenger_breakup[$pax_type]['PassengerCount'] = $pax_count;
                        }
                        if($fare['ChargeType'] == 'FarePrice'){
                            $total_base_fare += $pax_count*$fare['Amount'];
                            // $pax_base_fare = $pax_count*$fare['Amount'];

                        }
                        if($fare['ChargeType'] == 'Tax'){
                            $total_tax += $pax_count*$fare['Amount'];
                        }
						$pax_total_fare = $total_base_fare + $total_tax;

                        
                    }
                     // $pax_base_fare =0;
                }

                if($search_data['infant_config'] > 0){
                    if($search_data['is_domestic'] == true){
                        $infant_price = 1200;
                    }
                    else{
                        $infant_price = 2350;
                    }
                    $pax_tax_inf = $search_data['infant_config']*$infant_price;
                    $pax_count = $search_data['infant_config'];
                    $passenger_breakup['INF']['BasePrice'] = 0;
                    $passenger_breakup['INF']['Tax'] = $pax_tax_inf;
                    $passenger_breakup['INF']['TotalPrice'] = $pax_tax_inf;
                    $passenger_breakup['INF']['PassengerCount'] = $pax_count;   
                    $total_tax += $pax_tax_inf;
                }
            }
        }
       
       
        //commission calculation
        $carrier = 'SG';
        if($seg_count == 1){
            $YQ = 400;
        }
        else{
            $YQ = 600;
        }
        $check_comm = $this->CI->flight_model->check_commision_goair($carrier, $search_data['is_domestic'], $this->booking_source,get_domain_auth_id());
        $check_comm = json_decode(json_encode($check_comm), True);
        $PLB_commssion =0;
        $cash_back = 0 ;
        if(valid_array($check_comm)){
            
            if($search_data['cabin_class'] == 'economy'){
                $commission_per= $check_comm['e_basic_plus_yq_value'];
            }
            else if($search_data['cabin_class'] == 'business'){
                $commission_per= $check_comm['b_basic_plus_yq_value'];
            }

            //checking except classes are there or not
            $ExceptClass = array();
            if (!empty($check_comm['except_classes'])) {
                $ExceptClass = explode(',', $check_comm['except_classes']);
            }
            $available_class = array_search($class_of_service, $ExceptClass); 
            
            if (empty($available_class)) {
                if ($check_comm['value_type'] == 'percentage') {
                    $basic_plus_yq = $total_base_fare+$YQ;
                    $PLB_commssion = ($basic_plus_yq * $commission_per) / 100;
                    
                } else if ($check_comm['value_type'] == 'plus') {
                    $PLB_commssion = $commission_per;
                } else {
                    $PLB_commssion = 0;
                }
            } else {
                $PLB_commssion = 0;
            }
            $cash_back = $check_comm['cashback'];
        }
        $agent_commission = round($PLB_commssion+$cash_back, 2);
        $agent_tds = round(($PLB_commssion+$cash_back)*5/100, 2);
        // $total_fare = $total_base_fare+$total_tax-$agent_commission+$agent_tds;
        $total_fare = $total_base_fare+$total_tax;
        // echo $total_fare;exit;
        // $agent_commission = ($itineray_price['Fare']['PublishedFare'] - $itineray_price['Fare']['OfferedFare']);
        // $AgentCommission = roundoff_number($agent_commission, 2);
        // $AgentTdsOnCommision = roundoff_number((($agent_commission * 5) / 100), 2); //Calculate it from currency library
        //Assigning to Fare Object
        $price = $this->get_price_object();
        $price['Currency'] = $currency_code;
        $price['ProductClass'] = $cabin_class;
        $price['ClassOfService'] = $class_of_service;
        $price['TotalDisplayFare'] = $total_fare;
        $price['PriceBreakup']['Tax'] = $total_tax;
        $price['PriceBreakup']['BasicFare'] = $total_base_fare;
        $price['PriceBreakup']['AgentCommission'] = $agent_commission; // need to check
        $price['PriceBreakup']['AgentTdsOnCommision'] = $agent_tds;// need to check

        $price['PassengerBreakup'] = $passenger_breakup;
       

        // debug($price);exit;
        return $price;
    }
      /**
     * Formates Itineray Price Details
     * @param unknown_type $itineray_price
     */
    private function format_itineray_price_details_new( & $price, $itineray_price, $search_data) {
       
        $passenger_breakup = array();
        if(valid_array($itineray_price['PaxFares'])){
            $total_base_fare = 0;
            $total_tax = 0;
            $pax_fare_list = $itineray_price['PaxFares']['PaxFare'];
            $pax_fare_list = force_multple_data_format($pax_fare_list);
            $pax_tax = 0;
            $pax_tax = 0;
            $currency_code ='';
            
            if($search_data['child_config'] > 0){
                $pax_fare_list[1] = $pax_fare_list[0];
                $pax_fare_list[1]['PaxType'] = 'CHD';
            }
          
            foreach($pax_fare_list as $pax_key => $pax_fare){
                $pax_type = $pax_fare['PaxType'];
                $fare_list = force_multple_data_format($pax_fare['ServiceCharges']['BookingServiceCharge']);
                foreach($fare_list as $f_key => $fare){
                    $currency_code = $fare['CurrencyCode'];
                    $pax_count = 1;
                    
                    if($pax_type == 'ADT'){
                        $pax_count = $search_data['adult_config'];
                    }
                    if($pax_type == 'CHD'){
                        $pax_count = $search_data['child_config'];
                    }
                    if($pax_type == 'INF'){
                        $pax_count = $search_data['infant_config'];
                    }
                    if($fare['ChargeType'] == 'FarePrice'){
                        $total_base_fare += $pax_base_fare = $pax_count*$fare['Amount'];
                    }
                    else{
                         $pax_tax += $pax_count*$fare['Amount'];
                    }
                    $pax_total_fare = $pax_base_fare + $pax_tax;

                    $passenger_breakup[$pax_type]['BasePrice'] = $pax_base_fare;
                    $passenger_breakup[$pax_type]['Tax'] = $pax_tax;
                    $passenger_breakup[$pax_type]['TotalPrice'] = $pax_total_fare;
                    $passenger_breakup[$pax_type]['PassengerCount'] = $pax_count;   
                }
                $total_tax += $pax_tax;
                $pax_tax = 0;
            }
            if($search_data['infant_config'] > 0){
                $pax_tax_inf = $search_data['infant_config']*1200;
                $pax_count = $search_data['infant_config'];
                $passenger_breakup['INF']['BasePrice'] = 0;
                $passenger_breakup['INF']['Tax'] = $pax_count*$pax_tax_inf;
                $passenger_breakup['INF']['TotalPrice'] = $pax_count*$pax_tax;
                $passenger_breakup['INF']['PassengerCount'] = $pax_count;   
                $total_tax += $pax_tax_inf;
            }
            $total_fare = $total_base_fare+$total_tax;
            
          
            // $agent_commission = ($itineray_price['Fare']['PublishedFare'] - $itineray_price['Fare']['OfferedFare']);
            // $AgentCommission = roundoff_number($agent_commission, 2);
            // $AgentTdsOnCommision = roundoff_number((($agent_commission * 5) / 100), 2); //Calculate it from currency library
            //Assigning to Fare Object
            $price = $this->get_price_object();
            $price['Currency'] = $currency_code;
            $price['TotalDisplayFare'] = $total_fare;
            $price['PriceBreakup']['Tax'] = $total_tax;
            $price['PriceBreakup']['BasicFare'] = $total_base_fare;
            $price['PriceBreakup']['AgentCommission'] = 0; // need to check
            $price['PriceBreakup']['AgentTdsOnCommision'] = 0; // need to check

            $price['PassengerBreakup'] = $passenger_breakup;
            // $farequote_price = $price;
        }
        // de bug($price);exit;
        return $price;
    }
     /**
     * Fare Rule
     * @param unknown_type $request
     */
    public function get_fare_rules($request) {
        // debug($request);exit;
        $seg_count = count($request['flight_search_results'][0][0]['FlightDetails']['Details'][0]);
      
        $origin = $request['flight_search_results'][0][0]['FlightDetails']['Details'][0][0]['Origin']['AirportCode'];
        $destination = $request['flight_search_results'][0][0]['FlightDetails']['Details'][0][$seg_count-1]['Destination']['AirportCode'];
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        $fare_rule_request = $this->fare_rule_request($request);
        if ($fare_rule_request['status'] == SUCCESS_STATUS) {
            // $fare_rule_response = $this->process_request($fare_rule_request ['request'], $fare_rule_request ['url'], $fare_rule_request ['remarks']);
            $fare_rules = 
                '<p>1. Spicejet All Guests, including children and infants, must present valid identification at check-in. It is your responsibility to ensure you have the appropriate travel documents at all times.<br />
                2. Rescheduling - 2225 INR per pax per segment plus difference of fare and applicable taxes and surcharge.<br />
                3. Check-in starts &quot;2 hours&quot; before scheduled departure, and closes 45 minutes prior to the scheduled departure time. Guests are requested to report at least 2 hours prior to departure time, at Go Air Check-in counters.<br />
                4. Cancellation / Changes within &quot;2 hours&quot; of departure or failure to check-in for a Go Air flight at least 45 minutes prior to the scheduled departure time will result in the fare being forfeited. We require the request atleast 4 hours prior to departure, After that ticket will be count as a No show.</p>

                <p>&nbsp;&nbsp;&nbsp; 0-2 hours before scheduled departure .Only Governement /airport taxes and fees will be refunded.<br />
                &nbsp;&nbsp;&nbsp; 2 hours before scheduled departure. 2225 INR per passenger per segment.</p>

                <p>&nbsp;</p>';
            $fare_rule_response[0]['Origin'] = $origin;
            $fare_rule_response[0]['Destination'] = $destination;
            $fare_rule_response[0]['Airline'] = 'SG';
            $fare_rule_response[0]['FareRules'] = $fare_rules;
            // debug($fare_rule_response);exit;
            // $fare_rule_response = json_decode($fare_rule_response, true);
            if (isset($fare_rule_response)) {
                $response ['status'] = SUCCESS_STATUS;
                $response ['data']['FareRuleDetail'] = $this->format_fare_rule_response($fare_rule_response);
            } else {
                $response ['message'] = 'Not Available';
            }
        } else {
            $response ['status'] = FAILURE_STATUS;
        }
        // debug($response);exit;
        return $response;
    }
     /**
     * Forms the fare rule request
     * @param unknown_type $request
     */
    private function fare_rule_request($params) {
        // debug($params);exit;
        $fare_ruel_req = '<?xml version="1.0" encoding="UTF-8"?>
        <s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
            ' . $this->soap_header () . '
            <s:Body>
                <FareRuleRequest>
                <fareRuleReqData>
                    <book:FareBasisCode>AO9RBINX</book:FareBasisCode>
                    <CultureCode>en-GB</CultureCode>
                </fareRuleReqData>
            </FareRuleRequest>

            </s:Body>
        </s:Envelope>';
        $request['request'] = $fare_ruel_req;
        $request ['url'] = $this->config ['end_point'] ['booking'];
        $request ['SOAPAction'] = 'http://schemas.navitaire.com/WebServices/IBookingManager/GetFareRule';
        $request ['remarks'] = 'FareRules';
        $request ['status'] = SUCCESS_STATUS;
        // debug($request);exit;
        return $request;
        return $request;
    }
     function format_fare_rule_response($fare_rule_response) {
        $fare_rules = array();
        foreach ($fare_rule_response as $k => $v) {
            
            
            $fare_rules[$k]['Origin'] = $v['Origin'];
            $fare_rules[$k]['Destination'] = $v['Destination'];
            $fare_rules[$k]['Airline'] = $v['Airline'];


            $domain_base_currency = domain_base_currency();
            // $domain_base_currency="USD";

            $currency_obj = new Currency(array('from' => get_application_default_currency(), 'to' => $domain_base_currency));

            $FareRulesArray = explode(" ", $v['FareRules']);

            foreach ($FareRulesArray as $key => $value) {
 
                if (trim($FareRulesArray[$key])=="INR" || trim($FareRulesArray[$key])=="-
INR") {

                    $FareRulesArray[$key] = $domain_base_currency;
                    if (is_numeric($FareRulesArray[$key - 1])) {
                        $FareRulesArray[$key - 1] = get_converted_currency_value($currency_obj->force_currency_conversion($FareRulesArray[$key - 1]));
                        
                    }
                     else
              {
                        $Amount = preg_replace("/[^0-9]/", "", $FareRulesArray[$key - 1]);
                        if(is_numeric($Amount))
            {
                           
                           $FareRulesArray[$key - 1] = get_converted_currency_value($currency_obj->force_currency_conversion($Amount));
            }
              }
                    if (is_numeric($FareRulesArray[$key + 1])) {
                         
                        $FareRulesArray[$key + 1] = get_converted_currency_value($currency_obj->force_currency_conversion($FareRulesArray[$key + 1]));
                        
                    }
                     else
               {
                        $Amount = preg_replace("/[^0-9]/", "", $FareRulesArray[$key + 1]);
                        if(is_numeric($Amount))
            {
                           $FareRulesArray[$key + 1] = get_converted_currency_value($currency_obj->force_currency_conversion($Amount));
            }
              }
                }
            }
            
            
            //debug($FareRulesArray);exit;

             $fare_rules[$k]['FareRules'] = implode(" ",$FareRulesArray);
            //$fare_rules[$k]['FareRules'] = $v['FareRuleDetail'];
        }
        //debug($fare_rules);exit;
        return $fare_rules;
    }
    public function get_update_fare_quote($request, $search_id, $return_request='') {
        // debug($return_request);exit;
        $response ['status'] = FAILURE_STATUS;
        $search_data = $this->search_data($search_id);
        if($request['booking_source'] == SPICEJET_FLIGHT_BOOKING_SOURCE){
            $flight_data = unserialized_data($request['flight_search_data']['ResultToken']);
            if(valid_array($return_request)){
                $flight_retun_data = unserialized_data($return_request['flight_search_data']['ResultToken']);
                $flight_data[1] = $flight_retun_data[0];
            }
        }
        // debug($request);
        // debug($return_request);exit;
        if (valid_array($request) == true && valid_array($flight_data)) {
            if(isset($flight_data[0]['Signature'])){
                $this->api_session_id = $flight_data[0]['Signature'];
            }
            // debug($flight_data);exit;
            // $update_contact_response = $this->get_update_contact_request($request, $search_id);
            // $update_contact_response['status'] = SUCCESS_STATUS;
            // if($update_contact_response['status'] == SUCCESS_STATUS){
                $sell_response = $this->get_sell_request($flight_data, $search_id);
                // debug($request);exit;
                // $sell_response['status'] = SUCCESS_STATUS;
                if($sell_response['status'] == SUCCESS_STATUS){
                   
                    if(valid_array($return_request)){
                        $response ['status'] = SUCCESS_STATUS;
                        $onward_flight_data = $request['flight_search_data'];
                        $return_flight_data = $return_request['flight_search_data'];
                        $final_response = Common_Api_Flight::form_flight_combination ( $onward_flight_data, $return_flight_data,'',$search_id );
                        // debug($final_response);exit;
                        $response ['data']['FareQuoteDetails']['JourneyList'][0][0] = $final_response[0];
                    }
                    else{
                        $result_token[0]['booking_source'] = $flight_data[0]['booking_source'];
                        $result_token[0]['Signature'] = $this->api_session_id;
                        $response ['status'] = SUCCESS_STATUS;
                        $response ['data']['FareQuoteDetails']['JourneyList'][0][0] = $request;
                        // $response ['data']['FareQuoteDetails']['JourneyList'][0][0]['JourneySellKey'] = $request['JourneySellKey'];
                        // $response ['data']['FareQuoteDetails']['JourneyList'][0][0]['FareSellKey'] = $request['FareSellKey'];
                        $response ['data']['FareQuoteDetails']['JourneyList'][0][0]['ResultToken'] = serialized_data($result_token);
                    }
                }
                else {
                    $response ['message'] = 'Not Available';
                }
            // }
            // else{
            //     $response ['status'] = FAILURE_STATUS;
            // }
        }
        else{
            $response ['status'] = FAILURE_STATUS;
        }
       
        // debug($response);exit;
        return $response; 
    }
    function combined_data($onward_data, $return_data){
         debug($response);exit;
    }
     /**
     * Extra Services
     * @param unknown_type $request
     */
    public function get_extra_services($request,$farequote_data, $search_id) {
        error_reporting(E_ALL);
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        // Need to remove 2 line
        $response ['message'] = 'Not Available';
       
        $search_data = $this->search_data($search_id);
        $flight_data = unserialized_data($request['ResultToken']);
        if (valid_array($request) == true && valid_array($flight_data)) {
            if(isset($flight_data[0]['Signature'])){
                $this->api_session_id = $flight_data[0]['Signature'];
            }
        }
        $ssr_request = $this->ssr_request($request);
        // for($i=0; $i<count($ssr_request['request']); $i++){
        //debug($ssr_request ['request'][0]);
        $ssr_response_xml = $this->process_request($ssr_request ['request'][0], $ssr_request ['url'],  $ssr_request ['SOAPAction'], $ssr_request ['remarks']);
         // $ssr_response_xml = $this->CI->custom_db->get_static_response (227523);
            // $ssr_response_xml = file_get_contents(FCPATH."travelport_xmls/goair_ssr_res.xml");
         // debug($ssr_response_xml);exit;
            $ssr_response[0] = Converter::createArray ( $ssr_response_xml );
        // }
        $seat_availability_request = $this->get_seat_availability_request($request);
         //debug($seat_availability_request);exit;
        for($i=0; $i<count($seat_availability_request['request']); $i++){
            $seat_available_res_xml = $this->process_request($seat_availability_request ['request'][$i], $seat_availability_request ['url'],  $seat_availability_request ['SOAPAction'], $seat_availability_request ['remarks']);
            // $seat_available_res_xml = file_get_contents(FCPATH."travelport_xmls/goair_seat_avialabilityresponse.xml");
            // debug($seat_available_res_xml);exit;
            $seat_available_res[$i] = Converter::createArray ( $seat_available_res_xml );
        }
        if (valid_array ($ssr_response ) || valid_array ($seat_available_res )) {
            $response ['status'] = SUCCESS_STATUS;
            $response ['data']['ExtraServiceDetails'] = $this->format_extra_services($ssr_response, $flight_data, $search_id, $seat_available_res);
        }else{
            $response ['message'] = 'Not Available';
        }
        return $response;    
    }
    private function ssr_request($params) {
        $flight_data_info = $params['FlightDetails']['FlightDetails']['Details'];
        $flight_data_new = array();

        // foreach($flight_data as $flight_segment_data){
        //     foreach($flight_segment_data as $flight_key => $flight){
               
        //         $flight_data_new[$flight['FlightNumber']][] = $flight;
        //     }  
        // }
        
        // $flight_data_new = array_values($flight_data_new);
        // debug($flight_data);
        // debug($flight_data_new);exit;
        $ssr_request ='';
        $ssr_request1 ='';
        $seat_req = array();
        foreach($flight_data_info as $flight_key => $flight_data){
            $ssr_request .= '﻿<?xml version="1.0" encoding="utf-8"?>
                  <s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
                                <s:Header>
                                  <h:ContractVersion xmlns:h="http://schemas.navitaire.com/WebServices">'.$this->config['contract_version'].'</h:ContractVersion>
                                  <h:Signature xmlns:h="http://schemas.navitaire.com/WebServices">' . $this->api_session_id . '</h:Signature>
                               </s:Header>
                               <s:Body>
                                  <GetSSRAvailabilityForBookingRequest xmlns="http://schemas.navitaire.com/WebServices/ServiceContracts/BookingService">
                                     <SSRAvailabilityForBookingRequest xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Booking" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
                                    <SegmentKeyList>';
            foreach ($flight_data as $f_key => $f_value) {
                $dept_date = explode(' ', $f_value['Origin']['DateTime']);
                $origin = $f_value['Origin']['AirportCode'];
                $destination = $f_value['Destination']['AirportCode'];
                $flight_number = sprintf ( "% 4s", $f_value['FlightNumber']);
                $ssr_request1 .= '<LegKey>
                                              <CarrierCode xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Common">' . $flight_data[0]['OperatorCode'] . '</CarrierCode>
                                              <FlightNumber xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Common">' . $flight_number . '</FlightNumber>
                                              <OpSuffix i:nil="true" xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Common"></OpSuffix>
                                              <DepartureDate>' . $dept_date[0] . '</DepartureDate>
                                              <DepartureStation>' . $origin . '</DepartureStation>
                                              <ArrivalStation>' . $destination . '</ArrivalStation>
                                           </LegKey>';
            
            }
             $ssr_request .= $ssr_request1.'</SegmentKeyList><PassengerNumberList xmlns:a="http://schemas.microsoft.com/2003/10/Serialization/Arrays">
                                           <a:short>0</a:short>
                                        </PassengerNumberList>
                                        <InventoryControlled>true</InventoryControlled>
                                        <NonInventoryControlled>true</NonInventoryControlled>
                                        <SeatDependent>true</SeatDependent>
                                        <NonSeatDependent>true</NonSeatDependent>
                                        <CurrencyCode>INR</CurrencyCode>
                                     </SSRAvailabilityForBookingRequest>
                                  </GetSSRAvailabilityForBookingRequest>
                               </s:Body>
                            </s:Envelope>';
            $seat_req[$flight_key] = $ssr_request;
        }
        $request ['request'][0] = $ssr_request;
        $request ['url'] = $this->config['end_point']['booking'];
        $request ['remarks'] = 'SSR Request';
        $request ['SOAPAction'] = 'http://schemas.navitaire.com/WebServices/IBookingManager/GetSSRAvailabilityForBooking';
        $request ['status'] = SUCCESS_STATUS;
        return $request;
    }
    /**
     * Forms the update fare quote request
     * @param unknown_type $request
     */
    private function get_seat_availability_request($params) {

        $flight_data_info = $params['FlightDetails']['Details'];
        if(empty($flight_data_info)){
            $flight_data_info = $params['FlightDetails']['FlightDetails']['Details'];
        }
        $flight_data_new = array();
        foreach($flight_data_info as $flight_segment_data){
            foreach($flight_segment_data as $flight_key => $flight){
               $flight_data_new[$flight['FlightNumber']][] = $flight;
            }  
        }
        if(isset($params['Signature'])){
            $signature = $params['Signature'];
        }else{
            $signature = $this->api_session_id;
        }
        $flight_data_new = array_values($flight_data_new);
        // $flight_data_new = $flight_data_info;
        $seat_availability_request = array();
        $seat_availability_request1 = array();
        foreach ($flight_data_new as $f_key => $f_value) {
            $seg_count = count($f_value);
            $dept_date = explode(' ', $f_value[0]['Origin']['DateTime']);
            $origin = $f_value[0]['Origin']['AirportCode'];
            $destination = $f_value[$seg_count-1]['Destination']['AirportCode'];
            $flight_number = sprintf("% 4s", $f_value[0]['FlightNumber']);
            $seat_availability_request[$f_key] = '<?xml version="1.0" encoding="utf-8"?>
                        <s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
                        <s:Header>
                            <h:ContractVersion xmlns:h="http://schemas.navitaire.com/WebServices">'.$this->config['contract_version'].'</h:ContractVersion>
                            <h:Signature xmlns:h="http://schemas.navitaire.com/WebServices">' . $signature . '</h:Signature>
                        </s:Header>
                        <s:Body>
                            <GetSeatAvailabilityRequest xmlns="http://schemas.navitaire.com/WebServices/ServiceContracts/BookingService">
                            <SeatAvailabilityRequest xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Booking" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
                                    <STD>'.$dept_date[0].'</STD>
                                    <DepartureStation>'.$origin.'</DepartureStation>
                                    <ArrivalStation>'.$destination.'</ArrivalStation>
                                    <IncludeSeatFees>true</IncludeSeatFees>
                                    <SeatAssignmentMode>PreSeatAssignment</SeatAssignmentMode>
                                    <FlightNumber>'.$flight_number.'</FlightNumber>
                                    <CarrierCode>'.$f_value[0]['OperatorCode'].'</CarrierCode>
                                    <OpSuffix i:nil="true" />
                                    <CompressProperties>false</CompressProperties>
                                    <EnforceSeatGroupRestrictions>true</EnforceSeatGroupRestrictions>
                                    <PassengerIDs xmlns:a="http://schemas.navitaire.com/WebServices/DataContracts/Booking">
                                        <a:long>0</a:long>
                                    </PassengerIDs>
                                    <PassengerSeatPreferences i:nil="true" />
                                    <SeatGroup>0</SeatGroup>
                                    <SeatGroupSettings i:nil="true" />
                                    <EquipmentDeviations i:nil="true" />
                                    <IncludePropertyLookup>false</IncludePropertyLookup>
                                    <OverrideCarrierCode i:nil="true" />
                                    <OverrideFlightNumber i:nil="true" />
                                    <OverrideOpSuffix i:nil="true" />
                                    <OverrideSTD>0001-01-01T00:00:00</OverrideSTD>
                                    <OverrideDepartureStation i:nil="true" />
                                    <OverrideArrivalStation i:nil="true" />
                                    <CollectedCurrencyCode>INR</CollectedCurrencyCode>
                                    <ExcludeEquipmentConfiguration>false</ExcludeEquipmentConfiguration>
                                    </SeatAvailabilityRequest></GetSeatAvailabilityRequest>
                                    </s:Body>
                             </s:Envelope>';
        }
        $request ['request'] = $seat_availability_request;
        $request ['url'] = $this->config['end_point']['booking'];
        $request ['remarks'] = 'Seat Availablity';
        $request ['SOAPAction'] = 'http://schemas.navitaire.com/WebServices/IBookingManager/GetSeatAvailability';
        $request ['status'] = SUCCESS_STATUS;

        return $request;
    }
     /**
     * Formates Extra Services Details
     * @param unknown_type $extra_services
     */
    private function format_extra_services($extra_services, $flight_data, $search_id, $seat_response) {
        $search_data = $this->CI->flight_model->get_safe_search_data($search_id);
        $search_data = $search_data['data'];
        //Format Meal and Baggage Details
        $extra_service = $this->format_meals_baggage_details($extra_services, $flight_data, $search_data);
        $seat_map_details = $this->format_seat_details($seat_response, $flight_data, $search_data);
        $formatted_extra_services['Baggage'] = $extra_service['baggage_list'];
        $formatted_extra_services['Meals'] = $extra_service['meal_list'];
        $formatted_extra_services['Seat'] = $seat_map_details;
        return $formatted_extra_services;
    }
    /**
     * Returns Meal Details
     * @param unknown_type $extra_service_details
     */
    private function format_meals_baggage_details($extra_service_details, $flight_data, $search_data) {
        
        if($search_data['is_domestic'] == 1){
            $search_type = 'domestic';
        }
        else{
            $search_type = 'international';
        }
        $extra_service = $extra_service_details[0];
        $meal_baggage_list = array();
        if(isset($extra_service ['s:Envelope'] ['s:Body'] ['GetSSRAvailabilityForBookingResponse'])){
               $available_ssr = $extra_service ['s:Envelope'] ['s:Body'] ['GetSSRAvailabilityForBookingResponse'] ['SSRAvailabilityForBookingResponse'] ['SSRSegmentList'] ['SSRSegment'] ;
               $available_ssr = force_multple_data_format($available_ssr);
               if (isset ( $available_ssr ) && valid_array ( $available_ssr )) {
                    foreach ( $available_ssr as $__AK => $__ssrd ) { 
                        // debug($available_ssr);exit;
                        $origin =  $__ssrd['LegKey'] ['DepartureStation'];
                        $destination =  $__ssrd ['LegKey'] ['ArrivalStation'];
                        $flight_number =  $__ssrd ['LegKey'] ['FlightNumber'];
                        $carrier_code =  $__ssrd ['LegKey'] ['CarrierCode'];
                        // $dept_date =  $extra_service ['s:Envelope'] ['s:Body'] ['GetSSRAvailabilityForBookingResponse'] ['SSRAvailabilityForBookingResponse'] ['SSRSegmentList'] ['SSRSegment'] ['LegKey'] ['DepartureDate'];
                        // $dept_date =  $__ssrd ['LegKey'] ['DepartureDate'];
                        // $dept_date = str_replace(' ', 'T', $dept_date);
                        $dept_date1 = $__ssrd ['LegKey'] ['DepartureDate'];
                        foreach ($__ssrd['AvailablePaxSSRList']['AvailablePaxSSR'] as $key => $__ssr) {
                            if (isset ( $__ssr ['PaxSSRPriceList'] ['PaxSSRPrice'] ['PaxFee'] ['ServiceCharges'] ['BookingServiceCharge'] ) && valid_array ( $__ssr ['PaxSSRPriceList'] ['PaxSSRPrice'] ['PaxFee'] ['ServiceCharges'] ['BookingServiceCharge'] )) {
                                $booking_service_charge = $__ssr ['PaxSSRPriceList'] ['PaxSSRPrice'] ['PaxFee'] ['ServiceCharges'] ['BookingServiceCharge'];
                                $booking_service_charge = force_multple_data_format ( $booking_service_charge );
                                $amount = 0;
                                foreach ( $booking_service_charge as $f_k => $__charge ) {
                                    if ($__charge ['ChargeType'] == 'ServiceCharge') {
                                        $amount += @$__charge ['Amount'];
                                    }
                                }
                                $meal_baggage_list[$__AK] ["'" . $__ssr ['SSRCode'] . "'"] ['fare'] = $amount;
                            }

                            $meal_baggage_list[$__AK] ["'" . $__ssr ['SSRCode'] . "'"] ['code'] = "'" . $__ssr ['SSRCode'] . "'";
                            $meal_baggage_list[$__AK] ["'" . $__ssr ['SSRCode'] . "'"] ['Type'] = 'dynamic';
                            $meal_baggage_list[$__AK] ["'" . $__ssr ['SSRCode'] . "'"] ['Origin'] = $origin;
                            $meal_baggage_list[$__AK] ["'" . $__ssr ['SSRCode'] . "'"] ['Destination'] = $destination;
                            $meal_baggage_list[$__AK] ["'" . $__ssr ['SSRCode'] . "'"] ['FlightNumber'] = $flight_number;
                            $meal_baggage_list[$__AK] ["'" . $__ssr ['SSRCode'] . "'"] ['STD'] = $dept_date1;
                            $meal_baggage_list[$__AK] ["'" . $__ssr ['SSRCode'] . "'"] ['CarrierCode'] = $carrier_code;
                        }
                    }
                }
            }
        
        // debug($meal_baggage_list);exit;
        $meal_array = array ();
        $baggage_array = array ();
        $baggage_array1 = array();
        $meal_key1 = 0;
        $origin = $search_data['from'];
        $destination = $search_data['to'];
        if (isset ( $meal_baggage_list ) && valid_array ( $meal_baggage_list )) {
            foreach($meal_baggage_list as $meal_key => $meal_baggage){
               
                $meal_baggage_key = array_keys ( $meal_baggage );
                $meal_baggage_key = implode ( ',', $meal_baggage_key );
                $attributes = array (
                        'meal_baggage_list' => $meal_baggage_key 
                );
                // debug($meal_baggage);exit;
                $meal_list = $this->read_meals ( $search_type, $attributes );
               
                if($meal_list['status'] == SUCCESS_STATUS){
                    foreach($meal_list['meal'] as $m_key => $m_value){
                        // echo $meal_list['meal'][0]['Code'];exit;
                        $meal_array1[0]['FlightNumber'] = $meal_baggage["'" .$meal_list['meal'][0]['Code']."'" ]['FlightNumber'];
                        $meal_array1[0]['STD'] = $meal_baggage["'" .$meal_list['meal'][0]['Code']."'" ]['STD'];
                        $meal_array1[0]['CarrierCode'] = $meal_baggage["'" .$meal_list['meal'][0]['Code']."'" ]['CarrierCode'];

                        $meal_array[$meal_key][$m_key]['Code'] = $meal_array1[0]['Code'] = $m_value['Code'];
                        $meal_array[$meal_key][$m_key]['Description'] = $meal_array1[0]['Description'] = $m_value['Description'];
                        $meal_array[$meal_key][$m_key]['Price'] = $m_value['Price'];
                        $meal_array[$meal_key][$m_key]['Origin'] = $meal_array1[0]['Origin'] = $meal_baggage["'" .$meal_list['meal'][0]['Code']."'" ]['Origin'];
                        $meal_array[$meal_key][$m_key]['Destination'] = $meal_array1[0]['Destination'] = $meal_baggage["'" .$meal_list['meal'][0]['Code']."'" ]['Destination'];
                        $meal_array[$meal_key][$m_key]['Type'] = $meal_array1[0]['Type'] = $meal_baggage["'" .$meal_list['meal'][0]['Code']."'" ]['Type'];
                        $meal_array[$meal_key][$m_key]['MealId'] = base64_encode(serialize($meal_array1));
                    }
                }
                $baggage_list = $this->read_baggage ( $search_type, $attributes );
                // debug($baggage_list);exit;
               
                if($baggage_list['status'] == SUCCESS_STATUS){
                    if($meal_baggage["'" .$baggage_list['baggage'][0]['Code']."'" ]['Origin'] == $search_data['to']){
                        $meal_key1++;
                        $origin = $search_data['to'];
                        $destination = $search_data['from'];
                    }
                    foreach($baggage_list['baggage'] as $b_key => $b_value){
                        $weight = explode('-', $b_value['Description']);
                        $meal_bag_count = count($meal_baggage_list);
                        // debug($b_value);exit;
                        // echo $meal_list['meal'][0]['Code'];exit;
                        $baggage_array1[$b_key][$meal_key]['FlightNumber'] = $meal_baggage["'" .$baggage_list['baggage'][0]['Code']."'" ]['FlightNumber'];
                        $baggage_array1[$b_key][$meal_key]['STD'] = $meal_baggage["'" .$baggage_list['baggage'][0]['Code']."'" ]['STD'];
                        $baggage_array1[$b_key][$meal_key]['CarrierCode'] = $meal_baggage["'" .$baggage_list['baggage'][0]['Code']."'" ]['CarrierCode'];
                        $baggage_array1[$b_key][$meal_key]['Origin'] = $meal_baggage["'" .$baggage_list['baggage'][0]['Code']."'" ]['Origin'];
                        $baggage_array1[$b_key][$meal_key]['Destination'] = $meal_baggage["'" .$baggage_list['baggage'][0]['Code']."'" ]['Destination'];

                        $baggage_array[$meal_key1][$b_key]['Code'] = $baggage_array1[$b_key][$meal_key]['Code'] = $b_value['Code'];
                        $baggage_array[$meal_key1][$b_key]['Weight'] = $baggage_array1[$b_key][$meal_key]['Weight'] = $weight[1];
                        // $baggage_array[$meal_key][$b_key]['Price'] = $b_value['Price'];
                        $baggage_array[$meal_key1][$b_key]['Price'] = $meal_baggage["'" .$b_value['Code']."'" ]['fare'];
                        $baggage_array[$meal_key1][$b_key]['Origin'] = $origin;
                        $baggage_array[$meal_key1][$b_key]['Destination'] =  $destination;
                        $baggage_array[$meal_key1][$b_key]['Type'] = $baggage_array1[$b_key][$meal_key]['Type'] = $meal_baggage["'" .$baggage_list['baggage'][0]['Code']."'" ]['Type'];
                        // $baggage_array[0][$b_key]['BaggageId'] = base64_encode(serialize($baggage_array1));
                       
                    }

                }
                 // debug($baggage_array);exit;
            }
        }
        foreach ($baggage_array as $key => $b_value) {
            foreach ($b_value as $b_key => $value) {
                $bag_sereralize[0] =$baggage_array1[$b_key][$key];
                $baggage_array[$key][$b_key]['BaggageId'] =  base64_encode(serialize($bag_sereralize));

            }
           
        }
        // debug($baggage_array);exit;
        $response['meal_list'] = $meal_array;
        $response['baggage_list'] = $baggage_array;
       
        return $response;
    }
    function read_meals($price_tag, $attr) {
        $response ['status'] = FAILURE_STATUS;
        $CI = & get_instance ();
        $query = 'select SD.code as Code, SD.description as Description, SD.' . $price_tag . ' AS Price from goair_ssr_details SD where SD.type="meal" AND code IN (' . $attr ['meal_baggage_list'] . ')';
        $meal_list = $CI->db->query ( $query )->result_array ();
        if (isset ( $meal_list ) && valid_array ( $meal_list )) {
            $response ['meal'] = $meal_list;
            $response ['status'] = SUCCESS_STATUS;
        }

        return $response;
    }
    /**
     *
     * @ERROR!!!
     *
     * @see Common_Api_Flight::read_baggage()
     */
    function read_baggage($price_tag, $attr) {
        $response ['data'] = array ();
        $response ['status'] = FAILURE_STATUS;
        $CI = & get_instance ();
        $bg_query = 'select SD.code as Code, SD.description as Description, SD.' . $price_tag . ' AS Price from goair_ssr_details SD where SD.type="baggage" AND code IN (' . $attr ['meal_baggage_list'] . ')';
        // echo $bg_query;
        // exit();
        $baggage_list = $CI->db->query ( $bg_query )->result_array ();

        if (isset ( $baggage_list ) && valid_array ( $baggage_list )) {
            $response ['baggage'] = $baggage_list;
            $response ['status'] = SUCCESS_STATUS;
        }
        return $response;
    }
    /**
     * Returns Seat Details
     * @param unknown_type $extra_service_details
    */
     private function format_seat_details($extra_service_details, $flight_data, $search_data) {
        $seat_map_array = array();
       
       
        foreach($extra_service_details as $ext_key => $extra_service){
            
            if ($this->valid_seat_map_response ( $extra_service )) {
                
                $seat_group_arr = array ();
                $seat_group = $extra_service ['s:Envelope'] ['s:Body'] ['GetSeatAvailabilityResponse'] ['SeatAvailabilityResponse'] ['SeatGroupPassengerFees'] ['SeatGroupPassengerFee'];
                
                foreach ( $seat_group as $s_k => $seat ) {
                    $seat_group_arr [$seat ['SeatGroup']] = $seat ['PassengerFee'];
                }
                $new_Seat = array();
                $seats_compartment = $extra_service ['s:Envelope'] ['s:Body'] ['GetSeatAvailabilityResponse'] ['SeatAvailabilityResponse'] ['EquipmentInfos'] ['EquipmentInfo'] ['Compartments'] ['CompartmentInfo'];
                $seats_compartment = force_multple_data_format($seats_compartment);
               // debug($extra_service);exit;
                $segment_info = $extra_service ['s:Envelope'] ['s:Body'] ['GetSeatAvailabilityResponse'] ['SeatAvailabilityResponse'] ['Legs'] ['SeatAvailabilityLeg'];
                $segment_info = force_multple_data_format($segment_info);
                $seg_count = count($segment_info);
                // debug($segment_info);exit;
                foreach($seats_compartment as $sc_key => $compartment) {

                    $seats = $compartment['Seats'] ['SeatInfo'];

                    foreach ( $seats as $s_k => $seat_detail ) {

                        $seat_detail ['seat_fees'] = $seat_group_arr [$seat_detail ['SeatGroup']];
                           if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $seat_detail ['SeatDesignator'])){
                                if ($seat_detail ['SeatAvailability'] == 'Open') {
                                    $availability = 1;
                                }
                                else{
                                    $availability = 0;
                                }
                               $amt = 0;
                                if (isset ( $seat_detail ['seat_fees'] ) && valid_array ( $seat_detail ['seat_fees'] )) {
                                    $service_charge = $seat_detail ['seat_fees'] ['ServiceCharges'] ['BookingServiceCharge'];
                                    $service_charge = force_multple_data_format ( $service_charge );
                                    foreach ( $service_charge as $s_k => $service ) {
                                        if(strtolower($service['ChargeType']) === 'servicecharge'){
                                            $amt += $service ['Amount'];
                                        }
                                        $currency = $service ['CurrencyCode'];
                                    }
                                }
                               
                                $seat_no = preg_replace ( "/[0-9]/", "", $seat_detail ['SeatDesignator'] );
                            
                                $seat_row['AirlineCode'] = $segment_info[0]['FlightDesignator']['a:CarrierCode'];
                                $seat_row['FlightNumber'] = $segment_info[0]['FlightDesignator']['a:FlightNumber'];
                                $seat_row['AvailablityType'] = $availability;
                                $seat_row['RowNumber'] = ( int ) $seat_detail ['SeatDesignator'];
                                $seat_row['Origin'] = $segment_info[0]['DepartureStation'];
                                $seat_row['Destination'] = $segment_info[$seg_count-1]['ArrivalStation'];
                                $seat_row['Price'] = $amt;
                                $seat_row['SeatNumber'] = $seat_detail ['SeatDesignator'];
                                $seat_row['Type'] = $seat_detail ['SeatDesignator'];
                                $seat_id[0]['Type'] = 'dynamic'; 
                                $seat_id[0]['Code'] = $seat_detail ['SeatDesignator']; 
                                $seat_id[0]['FlightNumber'] = $segment_info[0]['FlightDesignator']['a:FlightNumber'];
                                $seat_id[0]['STD'] = $segment_info[0] ['STD']; 
                                $seat_id[0]['CarrierCode'] = $segment_info[0]['FlightDesignator']['a:CarrierCode'];
                                $seat_id[0]['Origin'] = $segment_info[0]['DepartureStation'];
                                $seat_id[0]['Destination'] = $segment_info[$seg_count-1]['ArrivalStation']; 
                                $seat_row['SeatId'] = base64_encode(serialize($seat_id));
                                if($seat_no == 'A'){
                                    $seat_no = 0;
                                }
                                else if($seat_no == 'B'){
                                    $seat_no = 1;
                                }
                                else if($seat_no == 'C'){
                                    $seat_no = 2;
                                }
                                else if($seat_no == 'D'){
                                    $seat_no = 3;
                                }
                                else if($seat_no == 'E'){
                                    $seat_no = 4;
                                }
                                else if($seat_no == 'F'){
                                    $seat_no = 5;
                                }
                                $new_Seat [( int ) $seat_detail ['SeatDesignator']] [$seat_no] = $seat_row;
                                unset($seat_id);
                            }
                            // debug($seat_row);
                        }
                    }
                }
            ksort ( $new_Seat );
            $new_Seat = array_values($new_Seat);
            // debug($new_Seat);
            // echo "saDsdfs";
            if (valid_array($new_Seat)) {
               $seat_map_array[$ext_key] = $new_Seat;
            }
        }
       
        return $seat_map_array;
    }
    function valid_seat_map_response($seat_map_response) {
        $strResp = false;
        if (isset ( $seat_map_response ['s:Envelope'] ['s:Body'] ['GetSeatAvailabilityResponse'] ['SeatAvailabilityResponse'] ['EquipmentInfos'] ['EquipmentInfo'] ['Compartments'] ['CompartmentInfo'] ['Seats'] ['SeatInfo'] ) && valid_array ( $seat_map_response ['s:Envelope'] ['s:Body'] ['GetSeatAvailabilityResponse'] ['SeatAvailabilityResponse'] ['EquipmentInfos'] ['EquipmentInfo'] ['Compartments'] ['CompartmentInfo'] ['Seats'] ['SeatInfo'] )) {
            $strResp = true;
        }
        if($strResp == false){
            if (isset ( $seat_map_response ['s:Envelope'] ['s:Body'] ['GetSeatAvailabilityResponse'] ['SeatAvailabilityResponse'] ['EquipmentInfos'] ['EquipmentInfo'] ['Compartments'] ['CompartmentInfo'] [0] ['Seats'] ['SeatInfo'] ) && valid_array ( $seat_map_response ['s:Envelope'] ['s:Body'] ['GetSeatAvailabilityResponse'] ['SeatAvailabilityResponse'] ['EquipmentInfos'] ['EquipmentInfo'] ['Compartments'] ['CompartmentInfo'] [0] ['Seats'] ['SeatInfo'] )) {
                $strResp = true;
            }           
        }
        return $strResp;
    }
     /**
     * Process booking
     * @param array $booking_params
     */
    function process_booking($booking_params, $app_reference, $sequence_number, $search_id) {
        $flight_data = $booking_params['ResultToken'];
     
        if (valid_array($flight_data)) {

            if(isset($flight_data['Signature'])){
              
                $this->api_session_id = $flight_data['Signature'];
            }
        }
       // echo "tetsts";exit;
        $search_data = $this->search_data($search_id);
        // debug($search_data);exit;
        // echo 'ssss';exit;
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned

        $ticket_response = array();
        $book_response = array();
        
        $ticket_service_response['status']= SUCCESS_STATUS;
        $passengers =  $booking_params['Passengers'];
        $seat_array = array();
        $meal_org_data = array();
        $meal_hh = array();
        $infant_array= array();
        $sell_response['status'] = SUCCESS_STATUS;
        $seat_response['status'] = SUCCESS_STATUS;
        $passengers = array_reverse($passengers);
        $passenger_count = 0;
        foreach($passengers as $pass_key => $passenger){

            // echo $passenger_count;
            // echo "<br/>";
            if(isset($passenger['MealId'])){
                $MealsDetails = $this->meal_request_details($passenger['MealId']);

                foreach($MealsDetails as $meals){
                    $meals['passenger_no'] = $passenger_count;
                    $meal_org_data['Meals'][$meals['Origin'].'-'.$meals['Destination']] = $meals;
                }
            }
            if(isset($passenger['BaggageId'])){
                $BaggageDetails = $this->baggage_request_details($passenger['BaggageId']);
                foreach($BaggageDetails as $baggage){
                    $baggage['passenger_no'] = $passenger_count;
                    $meal_org_data['Baggage'][$baggage['Origin'].'-'.$baggage['Destination']] = $baggage;
                
                }
            }
            if(isset($passenger['SeatId'])){
                $SeatDetails = $this->seat_request_details($passenger['SeatId']);
               
                foreach($SeatDetails as $seat){
                    $seat['passenger_no'] = $passenger_count;
                    $seat_array[$seat['Origin'].'-'.$seat['Destination']][$pass_key] = $seat;
                }
            }
            if($passenger['PaxType'] == 3){
                $infant_array[] = $pass_key;
            }
            else{
                $passenger_count++;
            }
           
            if(valid_array($meal_org_data)){
                 foreach($meal_org_data as $meals_ss_key => $meals_ss){
                    foreach ($meals_ss as $ff_key => $ff_value) {
                        $meal_hh[$ff_key][] = $ff_value;
                    }
                }
            }
            $meal_org_data = array();
          
        }
          // debug($infant_array);exit;
        if(valid_array($meal_hh) == false){
            $flight_data = $booking_params['flight_data']['FlightDetails']['Details'];
            
            $flight_data_new = array();

            foreach($flight_data as $flight_segment_data){
                foreach($flight_segment_data as $flight_key => $flight){
                   $flight_data_new[$flight['FlightNumber']][] = $flight;
                }  
            }
            
            $flight_data_new = array_values($flight_data_new);
            $flight_segment_data_new = array();
            foreach($flight_data_new as $flight_key => $flight_data){
                $flight_details_count = count($flight_data);
                $dept_date = explode(' ', $flight_data[0]['Origin']['DateTime']);
                $origin = $flight_data[0]['Origin']['AirportCode'];
                $destination = $flight_data[$flight_details_count-1]['Destination']['AirportCode'];
                $flight_segment_data_new[$origin.'-'.$destination][0]['FlightNumber'] = $flight_data[0]['FlightNumber'];
                $flight_segment_data_new[$origin.'-'.$destination][0]['STD'] = str_replace(' ', 'T', $flight_data[0]['Origin']['DateTime']);
                $flight_segment_data_new[$origin.'-'.$destination][0]['CarrierCode'] = $flight_data[0]['OperatorCode'];
                $flight_segment_data_new[$origin.'-'.$destination][0]['Origin'] = $origin;
                $flight_segment_data_new[$origin.'-'.$destination][0]['Destination'] = $destination;
              
            }
            $meal_hh = $flight_segment_data_new;

        }
     
        if(valid_array($meal_hh) ){
            $sell_response = $this->get_ssr_sell_request($meal_hh, $infant_array, $app_reference);
        }
        if(valid_array($seat_array)){
            $seat_response = $this->get_seat_assign_request($seat_array, $app_reference);
        }
        if($sell_response['status'] == SUCCESS_STATUS && $seat_response['status'] == SUCCESS_STATUS){
            $update_contact_response = $this->get_update_contact_request($booking_params);
           
            $update_pass_response = $this->get_update_passenger_request($booking_params, $app_reference);
            
            if($update_pass_response['status'] == SUCCESS_STATUS){
                $add_payment_booking_res = $this->get_add_payemt_booking_request($update_pass_response['data']['api_total_fare'], $app_reference);
                if($add_payment_booking_res['status'] == SUCCESS_STATUS){
                     $book_service_response = $this->run_book_service($booking_params, $app_reference, $sequence_number, $search_id);
                        if ($book_service_response['status'] == SUCCESS_STATUS) {
                            $response ['status'] = SUCCESS_STATUS;
                            $book_response = $book_service_response['data']['book_response'];
                            $get_booking_details_service_response = $this->run_get_booking_details_service($book_response, $app_reference, $sequence_number);
                            if($get_booking_details_service_response['status'] == SUCCESS_STATUS){
                                if($get_booking_details_service_response['message'] == 'Booking Hold'){
                                    $payment_status = 'BOOKING_HOLD';
                                }
                                else{
                                    $payment_status = 'BOOKING_CONFIRMED';
                                }
                            }
                            //Save BookFlight Details
                            $this->save_book_response_details($book_response, $app_reference, $sequence_number, $payment_status);
                            // debug($get_booking_details_service_response);exit;
                            // Save Ticket Details
                            $this->save_flight_ticket_details($booking_params, $book_response, $app_reference, $sequence_number, $search_id);

                        }


                   /* if($search_data['data']['trip_type'] == 'return' && $search_data['data']['is_domestic'] == 1){
                        $booking_details = $this->CI->custom_db->single_table_records('go_air_booking_round', '*', array('app_reference' => $app_reference, 'search_id' => $search_id));
                        if($booking_details['status'] == SUCCESS_STATUS){
                            
                            $book_service_response = $this->run_book_service($booking_params, $app_reference, $sequence_number, $search_id);
                            // if ($book_service_response['status'] == SUCCESS_STATUS) {
                            //     $response ['status'] = SUCCESS_STATUS;
                            //     $book_response = $book_service_response['data']['book_response'];

                            //     //Save BookFlight Details
                            //     $this->save_book_response_details($book_response, $app_reference, $sequence_number);
                            //     $get_booking_details_service_response = $this->run_get_booking_details_service($book_response, $app_reference, $sequence_number);
                                
                            //     // debug($get_booking_details_service_response);exit;
                            //     // Save Ticket Details
                            //     $this->save_flight_ticket_details($booking_params, $book_response, $app_reference, $sequence_number, $search_id);

                            //     //for onward updating the data
                                
                            //     // $app_reference_onward = $booking_details['data'][0]['app_reference'];
                            //     $booking_params_on = json_decode($booking_details['data'][0]['booking_params'], true);
                            //     $this->save_book_response_details($book_response, $app_reference, 0);

                            //     // debug($get_booking_details_service_response);exit;
                            //     // Save Ticket Details
                            //     $this->save_flight_ticket_details($booking_params_on, $book_response, $app_reference, 0, $search_id);

                            // }
                        }
                        else{
                            $insert_data['search_id'] = $search_id;
                            $insert_data['app_reference'] = $app_reference;
                            $insert_data['sequence_number'] = $sequence_number;
                            $insert_data['booking_status'] = 1;
                            $insert_data['booking_params'] = json_encode($booking_params);
                            $this->CI->custom_db->insert_record('go_air_booking_round', $insert_data);
                            //Save BookFlight Details
                            $flight_booking_status = 'BOOKING_CONFIRMED';
                            $this->CI->common_flight->update_flight_booking_status($flight_booking_status, $app_reference, $sequence_number, $this->booking_source);
                            $response ['status'] = SUCCESS_STATUS;
                            // debug($response);exit;
                           
                        }
                    }
                    else{
                        $book_service_response = $this->run_book_service($booking_params, $app_reference, $sequence_number, $search_id);
                        if ($book_service_response['status'] == SUCCESS_STATUS) {
                            $response ['status'] = SUCCESS_STATUS;
                            $book_response = $book_service_response['data']['book_response'];

                            //Save BookFlight Details
                            $this->save_book_response_details($book_response, $app_reference, $sequence_number);
                            $get_booking_details_service_response = $this->run_get_booking_details_service($book_response, $app_reference, $sequence_number);
                            
                            // debug($get_booking_details_service_response);exit;
                            // Save Ticket Details
                            $this->save_flight_ticket_details($booking_params, $book_response, $app_reference, $sequence_number, $search_id);

                        }
                    }*/
                   
                   
                    
                    //  else {
                    //     $response ['message'] = $book_service_response['message'];
                    //     $flight_booking_status = 'BOOKING_FAILED';
                    //     $this->CI->common_flight->update_flight_booking_status($flight_booking_status, $app_reference, $sequence_number, $this->booking_source);
                    // }
                   //$this->logout(); 
                }
            }

        }
        // debug($response);exit;
        return $response;
   
}
    function get_sell_request($booking_params, $search_id){
      
        // debug($journey_fare_key);exit;
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        $sell_request = $this->sell_request($booking_params, $search_id);
        // debug($sell_request);exit;
        if ($sell_request['status'] == SUCCESS_STATUS) {
            $sell_response = $this->process_request($sell_request ['request'], $sell_request ['url'], $sell_request ['SOAPAction'], $sell_request ['remarks']);
            // $sell_response = $this->CI->custom_db->get_static_response (219290);
            // debug($sell_response);exit;
            // $sell_response = $this->CI->db->query("select response from provab_api_response_history where origin=2730")->result_array()[0]['response'];
            $sell_response = Converter::createArray ( $sell_response );
            if ($this->valid_sell_response($sell_response) == TRUE) {
                $response ['status'] = SUCCESS_STATUS;
                $TotalCost = $sell_response ['s:Envelope'] ['s:Body'] ['SellResponse'] ['BookingUpdateResponseData'] ['Success'] ['PNRAmount'] ['TotalCost'];
                $api_total_fare = $TotalCost; // need to check the price with price itinerary request
            }
            
        }
        // debug($response);exit;
        return $response;
         // debug($booking_params);exit;

    }
     /**
     * Sell RQ for api
     */
    private function sell_request($booking_params, $search_id) {
        
        $search_data = $this->search_data($search_id);
        
        /*$fare_sell_keys = $booking_params['FareSellKey'];
        $journey_sell_keys = $booking_params['JourneySellKey'];
        $fare_sell_key ='';
        for($j=0; $j<count($fare_sell_keys); $j++){
            $fare_sell_key .= $fare_sell_keys[$j].'^';      
        } 
        $fare_sell_key = substr($fare_sell_key, 0,-1);
       
        for($i=0; $i<count($journey_sell_keys); $i++){
            $JourneySellKeys .= '<a:SellKeyList>
                      <a:JourneySellKey>' . $journey_sell_keys [$i] . '</a:JourneySellKey>
                        <a:FareSellKey>' . $fare_sell_key. '</a:FareSellKey>
                        <a:StandbyPriorityCode i:nil="true"/>
                      </a:SellKeyList>';
        }*/
        $fare_sell_key ='';
        $JourneySellKeys ='';
        foreach($booking_params as $b_key => $b_value){
            $fare_sell_keys = $b_value['FareSellKey'];
            $journey_sell_keys = $b_value['JourneySellKey'];
            foreach($fare_sell_keys as $f_key => $f_value){
                $fare_sell_key .= $f_value.'^';      
            }
            $fare_sell_key = substr($fare_sell_key, 0,-1);
            foreach($journey_sell_keys as $j_key => $j_value){
                $JourneySellKeys .= '<a:SellKeyList>
                  <a:JourneySellKey>' . $j_value . '</a:JourneySellKey>
                    <a:FareSellKey>' . $fare_sell_key. '</a:FareSellKey>
                    <a:StandbyPriorityCode i:nil="true"/>
                  </a:SellKeyList>'; 
            }
            $fare_sell_key ='';
        }

        $pax_cnt = $search_data['data']['adult_config']+$search_data['data']['child_config'];
        $PaxPriceType ='';
        for($adt = 0; $adt<$search_data['data']['adult_config']; $adt++){
            $PaxPriceType .= '<a:PaxPriceType>
                                    <a:PaxType>ADT</a:PaxType>
                                    <a:PaxDiscountCode i:nil="true"/>
                            </a:PaxPriceType>';
        }
        for($chd = 0; $chd<$search_data['data']['child_config']; $chd++){
            $PaxPriceType .= '<a:PaxPriceType>
                                    <a:PaxType>CHD</a:PaxType>
                                    <a:PaxDiscountCode i:nil="true"/>
                            </a:PaxPriceType>';
        }

        //tempor
        // $pax_cnt = 0;
        // $PaxPriceType = '';
        // foreach($passengers as $pass){
        //     if($pass['PaxType'] == 3){
        //         $px_type = 'INF';
        //         continue;
        //     }
        //     else if($pass['PaxType'] == 2){
        //         $px_type = 'CHD';
        //     }
        //     else{
        //         $px_type = 'ADT';
        //     }
        //     $pax_cnt ++;
        //     $PaxPriceType .= '<a:PaxPriceType>
        //                             <a:PaxType>' . $px_type . '</a:PaxType>
        //                             <a:PaxDiscountCode i:nil="true"/>
        //                         </a:PaxPriceType>';
        // }
        
        
        $request = array();
        $sell_request = '<?xml version="1.0" encoding="UTF-8"?>
            <s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
                ' . $this->soap_header () . '
                <s:Body>
                    <SellRequest xmlns="http://schemas.navitaire.com/WebServices/ServiceContracts/BookingService">
                        <SellRequestData xmlns:a="http://schemas.navitaire.com/WebServices/DataContracts/Booking" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
                            <a:SellBy>JourneyBySellKey</a:SellBy>
                            <a:SellJourneyByKeyRequest>
                                <a:SellJourneyByKeyRequestData>
                                    <a:ActionStatusCode>NN</a:ActionStatusCode>
                                    <a:JourneySellKeys>
                                        ' . $JourneySellKeys . '
                                    </a:JourneySellKeys>
                                    <a:PaxPriceType>
                                        ' . $PaxPriceType . '
                                    </a:PaxPriceType>
                                    <a:CurrencyCode>' . get_application_default_currency () . '</a:CurrencyCode>
                                    <a:SourcePOS xmlns:b="http://schemas.navitaire.com/WebServices/DataContracts/Common">
                                        <b:State>New</b:State>
                                        <b:AgentCode>' . $this->config ['organization_code'] . '</b:AgentCode>
                                        <b:OrganizationCode>' . $this->config ['agent_id'] . '</b:OrganizationCode>
                                        <b:DomainCode>' . $this->config ['agent_domain'] . '</b:DomainCode>
                                        <b:LocationCode>www</b:LocationCode>
                                    </a:SourcePOS>
                                    <a:PaxCount>' . $pax_cnt . '</a:PaxCount>
                                    <a:TypeOfSale i:nil="true"/>
                                    <a:LoyaltyFilter>MonetaryOnly</a:LoyaltyFilter>
                                    <a:IsAllotmentMarketFare>false</a:IsAllotmentMarketFare>
                                    <a:SourceBookingPOS i:nil="true"/>
                                    <a:PreventOverLap>false</a:PreventOverLap>
                                </a:SellJourneyByKeyRequestData>
                            </a:SellJourneyByKeyRequest>
                            <a:SellJourneyRequest i:nil="true"/>
                            <a:SellSSR i:nil="true"/>
                            <a:SellFee i:nil="true"/>
                        </SellRequestData>
                    </SellRequest>
                </s:Body>
            </s:Envelope>';
        $request ['request'] = $sell_request;
        $request ['url'] = $this->config['end_point']['booking'];
        $request ['remarks'] = 'Sell Request';
        $request ['SOAPAction'] = 'http://schemas.navitaire.com/WebServices/IBookingManager/Sell';
        $request ['status'] = SUCCESS_STATUS;
        // debug($request);exit;
        return $request;
    }
     function get_update_contact_request($booking_params){
      
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        $update_contact_request = $this->update_contact_request($booking_params);
        if ($update_contact_request['status'] = SUCCESS_STATUS) {
            $update_contact_response = $this->process_request($update_contact_request ['request'], $update_contact_request ['url'], $update_contact_request ['SOAPAction'], $update_contact_request ['remarks']);
            // $update_contact_response = $this->CI->custom_db->get_static_response (192);
            // $update_contact_response = $this->CI->db->query("select response from provab_api_response_history where origin=2548")->result_array()[0]['response'];
// 
            // debug($sell_response);exit;
            // $sell_response = file_get_contents(FCPATH."travelport_xmls/goair_sell_res.xml");
            $update_contact_response = Converter::createArray ( $update_contact_response );
            //debug($update_contact_response);exit;
            if ($this->valid_update_contact_response($update_contact_response) == TRUE) {
                $response ['status'] = SUCCESS_STATUS;
            }
            
        }

        return $response;
         // debug($booking_params);exit;

    }
    function update_contact_request($booking_params){
        // debug($booking_params);exit;
        if(array_key_exists('GST', $booking_params)){
            $state_info = $this->CI->custom_db->single_table_records('state_list','abbr',array('en_name' => $booking_params['GST']['GstState']));
            // debug($state_info);exit;
            $first_name = $booking_params['Passengers'][0]['FirstName'];
            $last_name = $booking_params['Passengers'][0]['LastName'];
            $title = $booking_params['Passengers'][0]['Title'];
            $email = $booking_params['GST']['GstEmail'];
            $mobile = $booking_params['GST']['GstPhone'];
            $pincode = $booking_params['Passengers'][0]['PinCode'];
            $country_code = $booking_params['Passengers'][0]['CountryCode'];
            $gst_number = $booking_params['GST']['Gstnumber'];
            $company_name = $booking_params['GST']['GstComapnyName'];
            $address = 'Electronic City';
            $city = $booking_params['Passengers'][0]['City'];
            $state = $state_info['data'][0]['abbr'];
        }
        else{
           
            /*$first_name = 'Vinay';
            $last_name = 'Shukla';
            $title = 'Mr';
            $email ='vinay@travelomatix.com';
            $mobile = 9916100864;
            $pincode = 560100;
            $country_code = 'IN';
            $gst_number = '29AANCA8324M2Z0';
            $company_name = 'ACCENTRIA SOLUTIONS PRIVATE LIMITED';
            $address = 'Electronic City';
            $city = 'Bangalore';
            $state = 'KA';*/
        }
        $sell_request = '<?xml version="1.0" encoding="UTF-8"?>
            <s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
                ' . $this->soap_header () . '
                <s:Body>
                   <UpdateContactsRequest xmlns="http://schemas.navitaire.com/WebServices/ServiceContracts/BookingService">
                    <updateContactsRequestData xmlns:c="http://schemas.navitaire.com/WebServices/DataContracts/Booking">
                <c:BookingContactList xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
                    <c:BookingContact>
                        <State xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Common">New</State>
                        <c:TypeCode>G</c:TypeCode>
                        <c:Names>
                            <c:BookingName>
                                <State xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Common">New</State>
                                <c:FirstName>'.$first_name.'</c:FirstName>
                                <c:MiddleName/>
                                <c:LastName>'.$last_name.'</c:LastName>
                                <c:Suffix i:nil="true"/>
                                <c:Title>'.$title.'</c:Title>
                            </c:BookingName>
                        </c:Names>
                        <c:EmailAddress>'.$email.'</c:EmailAddress>
                        <c:HomePhone>'.$mobile.'</c:HomePhone>
                        <c:WorkPhone i:nil="true"/>
                        <c:OtherPhone i:nil="true"/>
                        <c:Fax i:nil="true"/>
                        <c:CompanyName>'.$company_name.'</c:CompanyName>
                        <c:AddressLine1>'.$address.'</c:AddressLine1>
                        <c:City>'.$city.'</c:City>
                        <c:ProvinceState>'.$state.'</c:ProvinceState>
                        <c:PostalCode>'.$pincode.'</c:PostalCode>
                        <c:CountryCode>'.$country_code.'</c:CountryCode>
                        <c:CultureCode i:nil="true"/>
                        <c:DistributionOption>Email</c:DistributionOption>
                        <c:CustomerNumber>'.$gst_number.'</c:CustomerNumber>
                        <c:NotificationPreference>None</c:NotificationPreference>
                        <c:SourceOrganization i:nil="true"/>
                    </c:BookingContact>
                </c:BookingContactList>
            </updateContactsRequestData>
        </UpdateContactsRequest>
                </s:Body>
            </s:Envelope>';
        // echo $sell_request;exit;
        $request ['request'] = $sell_request;
        $request ['url'] = $this->config['end_point']['booking'];
        $request ['remarks'] = 'Update Contact Request';
        $request ['SOAPAction'] = 'http://schemas.navitaire.com/WebServices/IBookingManager/UpdateContacts';
        $request ['status'] = SUCCESS_STATUS;
        return $request;
    }
    /*ssr sell request*/
    function get_ssr_sell_request($booking_params, $infant_array, $app_reference){
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        $sell_request = $this->ssrsell_request($booking_params, $infant_array);
        if ($sell_request['status'] = SUCCESS_STATUS) {
            $sell_response = $this->process_request($sell_request ['request'], $sell_request ['url'], $sell_request ['SOAPAction'], $sell_request ['remarks']);
            // $sell_response = $this->CI->custom_db->get_static_response (191);
            $sell_response = Converter::createArray ( $sell_response );
            // debug($sell_response);exit;
            if ($this->valid_sell_response($sell_response) == TRUE) {
                $response ['status'] = SUCCESS_STATUS;
                $TotalCost = $sell_response ['s:Envelope'] ['s:Body'] ['SellResponse'] ['BookingUpdateResponseData'] ['Success'] ['PNRAmount'] ['TotalCost'];
                $api_total_fare = $TotalCost; // need to check the price with price itinerary request
               
            }
            else{
                $exception_log_message = '';
                $this->CI->exception_logger->log_exception($app_reference, $this->booking_source_name . '- (<strong>BOOK</strong>)', $exception_log_message, $sell_response);
            }
            
        }

        return $response;
    }
    /**
     * SSR Sell RQ for api
     */
    private function ssrsell_request($booking_params, $infant_array) {
       
        $request = array();
        $ssrsell_request = '<?xml version="1.0" encoding="UTF-8"?>
            <s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
                ' . $this->soap_header () . '
                <s:Body>
                    <SellRequest xmlns="http://schemas.navitaire.com/WebServices/ServiceContracts/BookingService">
                        <SellRequestData xmlns:a="http://schemas.navitaire.com/WebServices/DataContracts/Booking" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
                            <a:SellBy>SSR</a:SellBy>
                            <a:SellJourneyByKeyRequest i:nil="true"></a:SellJourneyByKeyRequest>
                            <a:SellJourneyRequest i:nil="true"></a:SellJourneyRequest>
                            <a:SellSSR>
                            <a:SSRRequest>
                            <a:SegmentSSRRequests>';
                            foreach($booking_params as $segment){
                                $ssrsell_request .= '<a:SegmentSSRRequest>
                                <a:FlightDesignator xmlns:b="http://schemas.navitaire.com/WebServices/DataContracts/Common">
                                        <b:CarrierCode>'.$segment[0]['CarrierCode'].'</b:CarrierCode>
                                        <b:FlightNumber>'.$segment[0]['FlightNumber'].'</b:FlightNumber>
                                        <b:OpSuffix i:nil="true"></b:OpSuffix>
                                    </a:FlightDesignator>
                                    <a:STD>'.$segment[0]['STD'].'</a:STD>
                                    <a:DepartureStation>'.$segment[0]['Origin'].'</a:DepartureStation>
                                    <a:ArrivalStation>'.$segment[0]['Destination'].'</a:ArrivalStation><a:PaxSSRs>';
                                 if(valid_array($infant_array)){
                                    for($infant=0; $infant<count($infant_array); $infant++){
                                        $ssrsell_request .= '
                                            <a:PaxSSR>
                                                <State xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Common">New</State>
                                                <a:ActionStatusCode>NN</a:ActionStatusCode>
                                                <a:ArrivalStation>'.$segment[0]['Destination'].'</a:ArrivalStation>
                                                <a:DepartureStation>'.$segment[0]['Origin'].'</a:DepartureStation>
                                                <a:PassengerNumber>'.$infant.'</a:PassengerNumber>
                                                <a:SSRCode>INFT</a:SSRCode>
                                                <a:SSRNumber>0</a:SSRNumber>
                                                <a:SSRDetail i:nil="true"></a:SSRDetail>
                                                <a:FeeCode i:nil="true"></a:FeeCode>
                                                <a:Note i:nil="true"></a:Note>
                                                <a:SSRValue>0</a:SSRValue>
                                            </a:PaxSSR>
                                        ';
                                    }
                                }
                                foreach($segment as $seg_key => $pax){
                                    if(isset($pax['Code'])){
                                        $ssrsell_request .= '
                                            <a:PaxSSR>
                                                <State xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Common">New</State>
                                                <a:ActionStatusCode>NN</a:ActionStatusCode>
                                                <a:ArrivalStation>'.$segment[0]['Destination'].'</a:ArrivalStation>
                                                <a:DepartureStation>'.$segment[0]['Origin'].'</a:DepartureStation>
                                                <a:PassengerNumber>'.$pax['passenger_no'].'</a:PassengerNumber>
                                                <a:SSRCode>'.$pax['Code'].'</a:SSRCode>
                                                <a:SSRNumber>0</a:SSRNumber>
                                                <a:SSRDetail i:nil="true"></a:SSRDetail>
                                                <a:FeeCode i:nil="true"></a:FeeCode>
                                                <a:Note i:nil="true"></a:Note>
                                                <a:SSRValue>0</a:SSRValue>
                                            </a:PaxSSR>';
                                    }
                                   
                                }
                               
                                $ssrsell_request .= '</a:PaxSSRs></a:SegmentSSRRequest>';
                            }

                            
                    $ssrsell_request .= '</a:SegmentSSRRequests>
                        <a:CurrencyCode>INR</a:CurrencyCode>
                        <a:CancelFirstSSR>false</a:CancelFirstSSR>
                        <a:SSRFeeForceWaiveOnSell>false</a:SSRFeeForceWaiveOnSell>
                    </a:SSRRequest>
                </a:SellSSR>
                <a:SellFee i:nil="true"></a:SellFee>
            </SellRequestData>
        </SellRequest>
    </s:Body>
    </s:Envelope>';

        $request ['request'] = $ssrsell_request;
        $request ['url'] = $this->config['end_point']['booking'];
        $request ['remarks'] = 'SSRSell Request';
        $request ['SOAPAction'] = 'http://schemas.navitaire.com/WebServices/IBookingManager/Sell';
        $request ['status'] = SUCCESS_STATUS;
        // debug($request);exit;
       return $request;

    }
     /*ssr sell request*/
    function get_seat_assign_request($booking_params, $app_reference){
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        $seat_request = $this->seat_assign_request($booking_params);
        if ($seat_request['status'] = SUCCESS_STATUS) {
            $seat_response = $this->process_request($seat_request ['request'], $seat_request ['url'], $seat_request ['SOAPAction'], $seat_request ['remarks']);
            // debug($seat_response);exit;
            // $sell_response = file_get_contents(FCPATH."travelport_xmls/goair_sell_res.xml");
            $seat_response = Converter::createArray ( $seat_response );
            // debug($sell_response);exit;
            if ($this->valid_assign_seat_response($seat_response) == TRUE) {
                $response ['status'] = SUCCESS_STATUS;
                $TotalCost = $seat_response ['s:Envelope'] ['s:Body'] ['AssignSeatsResponse'] ['BookingUpdateResponseData'] ['Success'] ['PNRAmount'] ['TotalCost'];
                $api_total_fare = $TotalCost; // need to check the price with price itinerary request
               
            }
            else{
                $exception_log_message = '';
                $this->CI->exception_logger->log_exception($app_reference, $this->booking_source_name . '- (<strong>BOOK</strong>)', $exception_log_message, $seat_response);
            }
            
        }

        return $response;
    }
    /**
     * Seat Assign RQ
     */
    private function seat_assign_request($booking_params) {
        $request = array();
        $segments ='';
        foreach($booking_params as $segment){
            foreach($segment as $seg_key => $pax){
                $flight_num_len = strlen($pax ['FlightNumber']);
                if($flight_num_len == 2){
                    $flight_num = "  ".$pax ['FlightNumber'];
                }
                else if($flight_num_len == 3){
                    $flight_num = " ".$pax ['FlightNumber'];
                }
                else{
                    $flight_num = $pax ['FlightNumber'];
                }
                // debug($pax);exit;
                 $segments .= '<SegmentSeatRequest>
                        <FlightDesignator xmlns:a="http://schemas.navitaire.com/WebServices/DataContracts/Common">
                            <a:CarrierCode>' . $pax ['CarrierCode'] . '</a:CarrierCode>
                            <a:FlightNumber>' . $flight_num . '</a:FlightNumber>
                            <a:OpSuffix i:nil="true"/>
                        </FlightDesignator>
                        <STD>' . $pax ['STD'] . '</STD>
                        <DepartureStation>' . $pax ['Origin'] . '</DepartureStation>
                        <ArrivalStation>' . $pax ['Destination'] . '</ArrivalStation>
                        <PassengerNumbers xmlns:a="http://schemas.microsoft.com/2003/10/Serialization/Arrays">
                            <a:short>' . $pax['passenger_no'] . '</a:short>
                        </PassengerNumbers>
                        <UnitDesignator>' . $pax ['Code'] . '</UnitDesignator>
                        <CompartmentDesignator i:nil="true"/>
                        <PassengerSeatPreferences i:nil="true"/>
                        <PassengerIDs xmlns:a="http://schemas.microsoft.com/2003/10/Serialization/Arrays">
                            <a:long>' .  $pax ['passenger_no'] . '</a:long>
                        </PassengerIDs>
                        <RequestedSSRs xmlns:a="http://schemas.microsoft.com/2003/10/Serialization/Arrays" i:nil="true"/>
                    </SegmentSeatRequest>';
            }
          
        }
        $segment_Seat_request = '<SegmentSeatRequests>
                                        ' . $segments . '
                                    </SegmentSeatRequests>';
            
            $sell_Seat_Request = '<SellSeatRequest xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Booking" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
                                    <BlockType>Session</BlockType>
                                    <SameSeatRequiredOnThruLegs>false</SameSeatRequiredOnThruLegs>
                                    <AssignNoSeatIfAlreadyTaken>false</AssignNoSeatIfAlreadyTaken>
                                    <AllowSeatSwappingInPNR>false</AllowSeatSwappingInPNR>
                                    <WaiveFee>false</WaiveFee>
                                    <ReplaceSpecificSeatRequest>false</ReplaceSpecificSeatRequest>
                                    <SeatAssignmentMode>AutoDetermine</SeatAssignmentMode>
                                    <IgnoreSeatSSRs>false</IgnoreSeatSSRs>
                                    ' . $segment_Seat_request . '
                                    <EquipmentDeviations i:nil="true"/>
                                    <CollectedCurrencyCode>INR</CollectedCurrencyCode>
                                </SellSeatRequest>';
            
            $seat_assign_request = '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/" >
                            ' . $this->soap_header () . '
                            <s:Body>
                                <AssignSeatsRequest xmlns="http://schemas.navitaire.com/WebServices/ServiceContracts/BookingService">
                                    ' . $sell_Seat_Request . '
                                </AssignSeatsRequest>
                            </s:Body>
                        </s:Envelope>';
        $request ['request'] = $seat_assign_request;
        $request ['url'] = $this->config['end_point']['booking'];
        $request ['remarks'] = 'Seat Assign';
        $request ['SOAPAction'] = 'http://schemas.navitaire.com/WebServices/IBookingManager/AssignSeats';
        $request ['status'] = SUCCESS_STATUS;
        // debug($request);exit;
       return $request;
    }
    function get_update_passenger_request($booking_params, $app_reference){
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        $update_pass_req = $this->update_passenger_request($booking_params);

        if ($update_pass_req['status'] = SUCCESS_STATUS) {
            $update_pass_response = $this->process_request($update_pass_req ['request'], $update_pass_req ['url'], $update_pass_req ['SOAPAction'], $update_pass_req ['remarks']);
            // $update_pass_response = $this->CI->custom_db->get_static_response (193);
            // $update_pass_response = file_get_contents(FCPATH."travelport_xmls/goair_update_pass_res.xml");
            $update_pass_response = Converter::createArray ( $update_pass_response );
            // debug($sell_response);exit;
            if ($this->valid_update_passenger_response($update_pass_response) == TRUE) {
                $response ['status'] = SUCCESS_STATUS;
                $TotalCost = $update_pass_response ['s:Envelope'] ['s:Body'] ['UpdatePassengerResponse'] ['BookingUpdateResponseData'] ['Success'] ['PNRAmount'] ['TotalCost'];
                $response['data']['api_total_fare'] = $TotalCost; // need to check the price with price itinerary request
              
            }
            else{
                $exception_log_message = '';
                $this->CI->exception_logger->log_exception($app_reference, $this->booking_source_name . '- (<strong>BOOK</strong>)', $exception_log_message, $update_pass_response);
            }
            
        }
        return $response;
    }
     /**
     * Update Passenger RQ for api
     */
    private function update_passenger_request($booking_params) {
       
        $passengers_data = $booking_params['Passengers'];
        // debug($passengers);exit;
        $inf = array();
        foreach($passengers_data as $pass_key => $pass){
            if ($pass['PaxType'] == 3) {
                $inf [] = $pass;
                continue;
            }
            $passengers [] = $pass;
        }
        if(valid_array($inf) && empty($inf) == false){
            foreach ( $inf as $i => $infant ) {
                if (isset ( $passengers [$i] ) == false) {
                    return false;
                }
                $passengers [$i] ['infant'] = $infant;
            }
        }
       
        
        $passengers_req = '';
       
        foreach($passengers as $pass_key => $pass){
            if($pass['Gender'] == 1){
                $gender = 'Male';
            }
            else{
                $gender = 'Female'; 
            }   
            if($pass['PaxType'] == 1){
                $pax_type = 'ADT';
            }
            else if($pass['PaxType'] == 2){
                $pax_type = 'CHD';
            }
            else{
                $pax_type = 'INF';  
            }
           if (isset ( $pass ['infant'] ) == true) {
            if($pass ['infant']['Gender'] == 1){
                $infant_gender = 'Male';
            }
            else{
                $infant_gender = 'Female';
            }
            if(trim($pass ['infant']['Title']) == 'Master'){
                $inf_ti = 'MR';
            }else{
                $inf_ti = $pass ['infant']['Title'];
            }
                    $infant = '<a:Infant>
                            <State
                                xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Common">New</State>
                            <a:DOB>' .  $pass ['infant']['DateOfBirth'] . '</a:DOB>
                            <a:Gender>' .  $infant_gender. '</a:Gender>
                            <a:Nationality>' .  $pass ['infant']['CountryCode'] . '</a:Nationality>
                            <a:ResidentCountry i:nil="true" />
                            <a:Names>
                                <a:BookingName>
                                    <State
                                        xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Common">New</State>
                                    <a:FirstName>' . $pass ['infant']['FirstName'] . '</a:FirstName>
                                    <a:MiddleName i:nil="true" />
                                    <a:LastName>' . $pass ['infant']['LastName'] . '</a:LastName>
                                    <a:Suffix i:nil="true" />
                                    <a:Title>' . $inf_ti . '</a:Title>
                                </a:BookingName>
                            </a:Names>
                        </a:Infant>';
                } else {
                    $infant = '<a:Infant i:nil="true"/>';
                }
                if(trim($pass ['Title']) == 'Master'){
                    $pas_tit = 'MR';
                }else{
                    $pas_tit = $pass ['Title'];
                }
            $passengers_req .= '<a:Passenger>
                                <State>New</State>
                                <a:PassengerPrograms i:nil="true"/>
                                <a:CustomerNumber i:nil="true"/>
                                <a:PassengerNumber>' . $pass_key . '</a:PassengerNumber>
                                <a:FamilyNumber>0</a:FamilyNumber>  
                                <a:PaxDiscountCode i:nil="true"/>
                                <a:Names>
                                    <a:BookingName>
                                        <State>New</State>
                                        <a:FirstName>' . $pass ['FirstName'] . '</a:FirstName>
                                        <a:MiddleName i:nil="true"/>
                                        <a:LastName>' . $pass ['LastName'] . '</a:LastName>
                                        <a:Suffix i:nil="true"/>
                                        <a:Title>' . $pas_tit . '</a:Title>
                                    </a:BookingName>
                                </a:Names>
                                ' . $infant . '
                                <a:PassengerInfo>
                                    <State>New</State>
                                    <a:Gender>' . $gender . '</a:Gender>
                                    <a:Nationality>' . $pass ['CountryCode'] . '</a:Nationality>
                                    <a:ResidentCountry i:nil="true"/>
                                    <a:WeightCategory>' . $gender . '</a:WeightCategory>
                                </a:PassengerInfo>
                                <a:PassengerTypeInfos>
                                    <a:PassengerTypeInfo>
                                        <a:State>New</a:State>
                                        <a:DOB>' . $pass ['DateOfBirth'] . '</a:DOB>
                                        <a:PaxType>' . $pax_type . '</a:PaxType>
                                    </a:PassengerTypeInfo>
                                </a:PassengerTypeInfos>
                                <a:PassengerInfos i:nil="true"/>
                                <a:PassengerInfants i:nil="true"/>
                                    <a:PseudoPassenger>false</a:PseudoPassenger>
                                    <a:PassengerTypeInfo i:nil="true"/>
                            </a:Passenger>';
        }
        $update_pass_req = '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
            ' . $this->soap_header () . '
            <s:Body>
                <UpdatePassengersRequest xmlns="http://schemas.navitaire.com/WebServices/ServiceContracts/BookingService">
                    <updatePassengersRequestData xmlns:a="http://schemas.navitaire.com/WebServices/DataContracts/Booking" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
                        <a:Passengers>
                            ' . $passengers_req . '
                        </a:Passengers>
                        <a:WaiveNameChangeFee>false</a:WaiveNameChangeFee>
                    </updatePassengersRequestData>
                </UpdatePassengersRequest>
            </s:Body>
        </s:Envelope>';
        $request ['request'] = $update_pass_req;
        $request ['url'] = $this->config['end_point']['booking'];
        $request ['remarks'] = 'Update Passengers';
        $request ['SOAPAction'] = 'http://schemas.navitaire.com/WebServices/IBookingManager/UpdatePassengers';
        $request ['status'] = SUCCESS_STATUS;
        return $request;
    }
    function get_add_payemt_booking_request($total_cost, $app_reference){

        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        $add_payment_booking_request = $this->add_payment_booking_request($total_cost);
        if ($add_payment_booking_request['status'] = SUCCESS_STATUS) {
            $add_payment_booking_response = $this->process_request($add_payment_booking_request ['request'], $add_payment_booking_request ['url'], $add_payment_booking_request ['SOAPAction'], $add_payment_booking_request ['remarks']);
            // $update_pass_response = file_get_contents(FCPATH."travelport_xmls/goair_update_pass_res.xml");
            // $add_payment_booking_response = $this->CI->custom_db->get_static_response (194);
            $add_payment_booking_response = Converter::createArray ( $add_payment_booking_response );
            // debug($sell_response);exit;
            if (isset ( $add_payment_booking_response ) && valid_array ( $add_payment_booking_response ) && isset ( $add_payment_booking_response ['s:Envelope'] ['s:Body'] ['AddPaymentToBookingResponse'] ['BookingPaymentResponse'] )) {
                $response ['status'] = SUCCESS_STATUS;
                $response ['data'] = $add_payment_booking_response;
            }
            else{
                $exception_log_message = '';
                $this->CI->exception_logger->log_exception($app_reference, $this->booking_source_name . '- (<strong>BOOK</strong>)', $exception_log_message, $add_payment_booking_response);
            }
            return $response;
        }
    }
     /**
     * Add Payment to Booking RQ for api
     */
    private function add_payment_booking_request($total_cost) {
        $add_payment_booking_req = '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/" >
            ' . $this->soap_header () . '
            <s:Body>
                <AddPaymentToBookingRequest xmlns="http://schemas.navitaire.com/WebServices/ServiceContracts/BookingService">
                    <addPaymentToBookingReqData xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Booking" xmlns:i="http://www.w3.org/2001/XMLSchema-instance"> 
                        <MessageState>New</MessageState>
                        <WaiveFee>false</WaiveFee>
                        <ReferenceType>Default</ReferenceType>
                        <PaymentMethodType>AgencyAccount</PaymentMethodType>
                        <PaymentMethodCode>AG</PaymentMethodCode>
                        <QuotedCurrencyCode>' . get_application_default_currency () . '</QuotedCurrencyCode>
                        <QuotedAmount>' . $total_cost . '</QuotedAmount>
                        <Status>New</Status>
                        <AccountNumberID>0</AccountNumberID>
                        <AccountNumber>' . strtoupper($this->config ['agent_id']) . '</AccountNumber>
                        <Expiration>2020-01-01T00:00:00</Expiration>
                        <ParentPaymentID>0</ParentPaymentID>
                        <Installments>0</Installments>
                        <PaymentText i:nil="true"/>
                        <Deposit>false</Deposit>
                        <PaymentFields i:nil="true"/>
                        <PaymentAddresses i:nil="true"/>
                        <AgencyAccount i:nil="true"/>
                        <CreditShell i:nil="true"/>
                        <CreditFile i:nil="true"/>
                        <PaymentVoucher i:nil="true"/>
                        <ThreeDSecureRequest i:nil="true"/>
                        <MCCRequest i:nil="true"/>
                        <AuthorizationCode i:nil="true"/>
                    </addPaymentToBookingReqData>
                </AddPaymentToBookingRequest>
            </s:Body>
        </s:Envelope>';
        $request ['request'] = $add_payment_booking_req;
        $request ['url'] = $this->config['end_point']['booking'];
        $request ['remarks'] = 'Add Payment to Booking';
        $request ['SOAPAction'] = 'http://schemas.navitaire.com/WebServices/IBookingManager/AddPaymentToBooking';
        $request ['status'] = SUCCESS_STATUS;
        return $request;
    }
       /**
     *
     * Enter description here ...
     * @param unknown_type $booking_params
     * @param unknown_type $app_reference
     * @param unknown_type $sequence_number
     */
    private function run_book_service($booking_params, $app_reference, $sequence_number, $search_id) {
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        $book_service_request = $this->run_book_service_request($booking_params, $search_id);
      
        if ($book_service_request['status'] == SUCCESS_STATUS) {
            $book_service_response = $this->process_request($book_service_request ['request'], $book_service_request ['url'], $book_service_request ['SOAPAction'], $book_service_request ['remarks']);
            // $book_service_response = $this->CI->custom_db->get_static_response (195);
            // $book_service_response = file_get_contents(FCPATH."travelport_xmls/goair_booking_res.xml");
            $book_service_response = Converter::createArray ( $book_service_response );
            if (valid_array($book_service_response) == true && isset($book_service_response['s:Envelope'] ['s:Body'] ['BookingCommitResponse'] ['BookingUpdateResponseData'] ['Success']) == true ) {
                $response ['status'] = SUCCESS_STATUS;
                $response ['data']['book_response'] = $book_service_response;
            } else {
                $error_message = '';
                if (isset($book_service_response['Response'] ['Error'] ['ErrorMessage'])) {
                    $error_message = $book_service_response['Response'] ['Error'] ['ErrorMessage'];
                }
                if (empty($error_message) == true) {
                    $error_message = 'Booking Failed';
                }
                $response ['message'] = $error_message;

                //Log Exception
                $exception_log_message = '';
                $this->CI->exception_logger->log_exception($app_reference, $this->booking_source_name . '- (<strong>BOOK</strong>)', $exception_log_message, $book_service_response);
            }
        } else {
            $response ['status'] = FAILURE_STATUS;
        }
       
        return $response;
    }
    /* * Forms the Book request
     * @param unknown_type $request
     */
    private function run_book_service_request($params, $search_id) {
        if(valid_array($params)) {
            $pax_cnt = 0;
            if (isset ( $params ['Passengers'] ) && valid_array ( $params ['Passengers'] )) {
                foreach ( $params ['Passengers'] as $p_key => $passenger ) {
                    if ($passenger['PaxType'] == 3) {
                        continue;
                    }
                    $pax_cnt ++;
                }
            }
            $booking_pass_details = $params ['Passengers'][0];
            $run_book_service_req = '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
                ' . $this->soap_header () . '
                <s:Body>
                    <BookingCommitRequest xmlns="http://schemas.navitaire.com/WebServices/ServiceContracts/BookingService">
                        <BookingCommitRequestData xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Booking" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
                            <State>New</State>
                            <RecordLocator i:nil="true"/>
                            <CurrencyCode>' . get_application_default_currency () . '</CurrencyCode>
                            <PaxCount>' . $pax_cnt . '</PaxCount>
                            <SystemCode i:nil="true"/>
                            <BookingID>0</BookingID>
                            <BookingParentID>0</BookingParentID>
                            <ParentRecordLocator i:nil="true"/>
                            <BookingChangeCode i:nil="true"/>
                            <GroupName i:nil="true"/>
                            <SourcePOS xmlns:b="http://schemas.navitaire.com/WebServices/DataContracts/Common">
                                <b:State>New</b:State>
                                <b:AgentCode>' . $this->config ['organization_code'] . '</b:AgentCode>
                                <b:OrganizationCode>' . $this->config ['agent_id'] . '</b:OrganizationCode>
                                <b:DomainCode>' . $this->config ['agent_domain'] . '</b:DomainCode>
                                <b:LocationCode>www</b:LocationCode>
                            </SourcePOS>
                            <BookingHold i:nil="true"/>
                            <ReceivedBy>
          			<State xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Common">New</State>
          			<ReceivedBy>' . $this->config ['agent_id'] . '</ReceivedBy>
          			<ReceivedReference i:nil="true"/>
          			<ReferralCode i:nil="true"/>
          			<LatestReceivedBy i:nil="true"/>
          			<LatestReceivedReference i:nil="true"/>
        		    </ReceivedBy>
                            <RecordLocators i:nil="true"/>
                            <Passengers i:nil="true"/>
                            <BookingComments i:nil="true"/>
                            <BookingContacts>
                                <BookingContact>
                                    <State>New</State>
                                    <TypeCode>P</TypeCode>
                                    <Names>
                                        <BookingName>
                                            <State>New</State>
                                            <FirstName>' . $booking_pass_details ['FirstName'] . '</FirstName>
                                            <MiddleName i:nil="true"/>
                                            <LastName>' . $booking_pass_details ['LastName'] . '</LastName>
                                            <Suffix i:nil="true"/>
                                            <Title>' . $booking_pass_details ['Title'] . '</Title>
                                        </BookingName>
                                    </Names>
                                    <EmailAddress>' . $booking_pass_details ['Email'] . '</EmailAddress>
                                    <HomePhone>' . $booking_pass_details ['ContactNo'] . '</HomePhone>
                                    <WorkPhone i:nil="true"/>
                                    <OtherPhone i:nil="true"/>
                                    <Fax i:nil="true"/>
                                    <CompanyName>Pacetravel</CompanyName>
                                    <AddressLine1>2nd Floor, Venkatadri IT Park, Electronic city</AddressLine1>
                                    <AddressLine2 i:nil="true"/>
                                    <AddressLine3 i:nil="true"/>
                                    <City>' . $booking_pass_details ['City'] . '</City>
                                    <ProvinceState>KA</ProvinceState>
                                    <PostalCode>' . $booking_pass_details ['PinCode'] . '</PostalCode>
                                    <CountryCode>IN</CountryCode>
                                    <CultureCode>en-GB</CultureCode>
                                    <DistributionOption>Email</DistributionOption>
                                    <CustomerNumber i:nil="true"/>
                                    <NotificationPreference>None</NotificationPreference>
                                    <SourceOrganization>' . $this->config ['agent_id'] . '</SourceOrganization>
                                </BookingContact>
                            </BookingContacts>                          
                            <NumericRecordLocator i:nil="true"/>
                            <RestrictionOverride>false</RestrictionOverride>
                            <ChangeHoldDateTime>false</ChangeHoldDateTime>
                            <WaiveNameChangeFee>false</WaiveNameChangeFee>
                            <WaivePenaltyFee>false</WaivePenaltyFee>
                            <WaiveSpoilageFee>false</WaiveSpoilageFee>
                            <DistributeToContacts>true</DistributeToContacts>
                        </BookingCommitRequestData>
                    </BookingCommitRequest>
                </s:Body>
            </s:Envelope>';
        $request ['request'] = $run_book_service_req;
        $request ['url'] = $this->config['end_point']['booking'];
        $request ['remarks'] = 'Commit Booking';
        $request ['SOAPAction'] = 'http://schemas.navitaire.com/WebServices/IBookingManager/BookingCommit';
        $request ['status'] = SUCCESS_STATUS;
        // debug($request);exit;
        return $request;
        }
    }
    function run_get_booking_details_service($booking_response){
        $pnr = $booking_response['s:Envelope']['s:Body']['BookingCommitResponse']['BookingUpdateResponseData']['Success']['RecordLocator'];
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        $get_book_details_req = $this->run_get_booking_details_service_request($pnr);
        if ($get_book_details_req['status'] == SUCCESS_STATUS) {
            // $get_book_details_response = file_get_contents(FCPATH."travelport_xmls/goair_get_boooking_details_res.xml");
            $get_book_details_response = $this->process_request($get_book_details_req ['request'], $get_book_details_req ['url'], $get_book_details_req ['SOAPAction'], $get_book_details_req ['remarks']);
            // $get_book_details_response = $this->CI->custom_db->get_static_response (196);
            $get_book_details_response = Converter::createArray ( $get_book_details_response );
            if (valid_array($get_book_details_response) == true && isset($get_book_details_response['s:Envelope']['s:Body']['GetBookingResponse']['Booking']['BookingInfo']) == true && $get_book_details_response['s:Envelope']['s:Body']['GetBookingResponse']['Booking']['BookingInfo']['BookingStatus'] == 'Confirmed') {
                $response ['status'] = SUCCESS_STATUS;
                $response ['data']['get_booking_details_response'] = $get_book_details_response['s:Envelope']['s:Body']['GetBookingResponse'];
            }
            else if (valid_array($get_book_details_response) == true && isset($get_book_details_response['s:Envelope']['s:Body']['GetBookingResponse']['Booking']['BookingInfo']) == true && $get_book_details_response['s:Envelope']['s:Body']['GetBookingResponse']['Booking']['BookingInfo']['BookingStatus'] == 'Hold') {
                $response ['status'] = SUCCESS_STATUS;
                $response ['message'] = 'Booking Hold';
                $response ['data']['get_booking_details_response'] = $get_book_details_response['s:Envelope']['s:Body']['GetBookingResponse'];
            } else {
                $error_message = 'Details Not Found';
                $response ['message'] = $error_message;
            }
        }
        else{
            $response ['status'] = FAILURE_STATUS;
        }
        return $response;
    }
    private function run_get_booking_details_service_request($pnr) {
        $run_get_book_details_req = '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
                ' . $this->soap_header () . '
                <s:Body>
                <GetBookingRequest xmlns="http://schemas.navitaire.com/WebServices/ServiceContracts/BookingService">
                    <GetBookingReqData xmlns:a="http://schemas.navitaire.com/WebServices/DataContracts/Booking" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
                        <a:GetBookingBy>RecordLocator</a:GetBookingBy>
                        <a:GetByRecordLocator xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Booking">
                            <RecordLocator>'.$pnr.'</RecordLocator>
                        </a:GetByRecordLocator>
                        <a:GetByThirdPartyRecordLocator i:nil="true"/>
                        <a:GetByID i:nil="true"/>
                    </GetBookingReqData>
                </GetBookingRequest>
                </s:Body>
            </s:Envelope>';
        $request ['request'] = $run_get_book_details_req;
        $request ['url'] = $this->config['end_point']['booking'];
        $request ['remarks'] = 'Get Booking Details';
        $request ['SOAPAction'] = 'http://schemas.navitaire.com/WebServices/IBookingManager/GetBooking';
        $request ['status'] = SUCCESS_STATUS;
        // debug($request);exit;
        return $request;
    }
    /* destrory the session at end */
    public function logout(){
        $logout_req  = '<?xml version="1.0" encoding="UTF-8"?>
                <s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
                    <s:Header>
                        <h:ContractVersion xmlns:h="http://schemas.navitaire.com/WebServices">'.$this->config['contract_version'].'</h:ContractVersion>
                        <h:Signature xmlns:h="http://schemas.navitaire.com/WebServices">' . $this->api_session_id . '</h:Signature>
                    </s:Header>
                    <s:Body>
                        <LogoutRequest xmlns="http://schemas.navitaire.com/WebServices/ServiceContracts/SessionService" />
                    </s:Body>
                </s:Envelope>';
        $logout_url = $this->config ['end_point'] ['session'];
        $logout_soap_action = 'http://schemas.navitaire.com/WebServices/ISessionManager/Logout';
        $remarks = 'Logout';
        $logout_response = $this->process_request($logout_req, $logout_url, $logout_soap_action, $remarks);
        $this->CI->custom_db->update_record('api_session_id', array('session_id' => ''),array('session_id' => $this->api_session_id));
        $this->api_session_id ='';
        // debug($logout_response);exit;
    }
    /**
     * Meal Details
     */
    private function meal_request_details($MealDetails) {

        $meal = array();
        if (valid_array($MealDetails) == true) {
            foreach ($MealDetails as $meal_k => $meal_v) {
                if (empty($meal_v) == false) {
                    $meal_data = Common_Flight::read_record($meal_v);

                    if (valid_array($meal_data) == true) {
                        $meal_data = json_decode($meal_data[0], true);
                        $temp_meal = array_values(unserialized_data($meal_data['MealId']));
                        if (isset($temp_meal[0]['Type'])) {
                            unset($temp_meal[0]['Type']);
                        }
                        $meal[$meal_k] = $temp_meal[0];
                    }
                }
            }
        }
        // debug($meal);exit;
        return $meal;
    }
     /**
     * Baggage Details
     */
   /* private function baggage_request_details($BaggageDetails) {

        $baggage = array();
        if (valid_array($BaggageDetails) == true) {
            foreach ($BaggageDetails as $baggage_k => $baggage_v) {
                if (empty($baggage_v) == false) {
                    $baggage_data = Common_Flight::read_record($baggage_v);

                    if (valid_array($baggage_data) == true) {
                        $baggage_data = json_decode($baggage_data[0], true);
                        $temp_baggage = array_values(unserialized_data($baggage_data['BaggageId']));
                        if (isset($temp_baggage[0]['Type'])) {
                            unset($temp_baggage[0]['Type']);
                        }
                        $baggage[$baggage_k] = $temp_baggage[0];
                    }
                }
            }
        }
        // debug($meal);exit;
        return $baggage;
    }*/
     private function baggage_request_details($BaggageDetails) {
        $baggage = array();
        if (valid_array($BaggageDetails) == true) {
            foreach ($BaggageDetails as $baggage_k => $baggage_v) {
                if (empty($baggage_v) == false) {
                    $baggage_data = Common_Flight::read_record($baggage_v);
                    if (valid_array($baggage_data) == true) {
                        $baggage_data = json_decode($baggage_data[0], true);
                        $temp_baggage = array_values(unserialized_data($baggage_data['BaggageId']));
                       
                        if (isset($temp_baggage[0]['Type'])) {
                            unset($temp_baggage[0]['Type']);
                        }
                      
                        $baggage = $temp_baggage;
                    }
                }
            }
            $flight_number = array();
            foreach($baggage as $b_key => $b_value){
                $new_baggage[$b_value['FlightNumber']][] = $b_value;
            }
            foreach($new_baggage as $b_key => $b_value){
                $new_baggage1[$b_key] = $b_value[0];
                $count = count($b_value);
                $new_baggage1[$b_key]['Origin'] = $b_value[0]['Origin'];
                $new_baggage1[$b_key]['Destination'] = $b_value[$count-1]['Destination'];
            }
          
        }
       
        return $new_baggage1;
    }
    /**
     * Seat Details
     */
    private function seat_request_details($SeatDetails) {

        $seat = array();
        if (valid_array($SeatDetails) == true) {
            foreach ($SeatDetails as $seat_k => $seat_v) {
                if (empty($seat_v) == false) {
                    $seat_data = Common_Flight::read_record($seat_v);

                    if (valid_array($seat_data) == true) {
                        $seat_data = json_decode($seat_data[0], true);
                        $temp_seat = array_values(unserialized_data($seat_data['SeatId']));
                        if (isset($temp_seat[0]['Type'])) {
                            unset($temp_seat[0]['Type']);
                        }
                        $seat[$seat_k] = $temp_seat[0];
                    }
                }
            }
        }
        // debug($meal);exit;
        return $seat;
    }
     /**
     * Process Cancel Booking
     * Online Cancellation
     */
    public function cancel_booking($request) {
        
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned

        $app_reference = $request['AppReference'];
        $sequence_number = $request['SequenceNumber'];
        $IsFullBookingCancel = $request['IsFullBookingCancel'];
        $ticket_ids = $request['TicketId'];

        $elgible_for_ticket_cancellation = $this->CI->common_flight->elgible_for_ticket_cancellation($app_reference, $sequence_number, $ticket_ids, $IsFullBookingCancel, $this->booking_source);
        // $elgible_for_ticket_cancellation['status'] = SUCCESS_STATUS;
        // debug($elgible_for_ticket_cancellation);exit;
        if ($elgible_for_ticket_cancellation['status'] == SUCCESS_STATUS) {
            $booking_details = $this->CI->flight_model->get_flight_booking_transaction_details($app_reference, $sequence_number, $this->booking_source);
            $booking_details = $booking_details['data'];
            $booking_transaction_details = $booking_details['booking_transaction_details'][0];
            $flight_booking_transaction_details_origin = $booking_transaction_details['origin'];
            $request_params = $booking_details;
            $request_params['passenger_origins'] = $ticket_ids;
            $send_change_request = $this->send_change_request($request_params);
            // debug($send_change_request);exit;
            if ($send_change_request['status'] == SUCCESS_STATUS) {
                $response ['status'] = SUCCESS_STATUS;
                $response ['message'] = 'Cancellation Request is processing';
                $send_change_response = $send_change_request['data']['send_change_response'];
                $passenger_origin = $request_params['passenger_origins'];
                $pass_count = count($passenger_origin);
                // debug($passenger_origin);exit;
                foreach ($passenger_origin as $origin) {
                    $this->save_ticket_cancellation_details($send_change_response, $origin, $pass_count);
                    $this->CI->common_flight->update_ticket_cancel_status($app_reference, $sequence_number, $origin);
                }
            
            } else {
                $response ['message'] = $send_change_request['message'];
            }
        } else {
            $response ['message'] = $elgible_for_ticket_cancellation['message'];
        }
        // debug($response);exit;
        return $response;
    }
     /**
     * Send ChangeRequest
     * @param unknown_type $booking_details
     * //ChangeRequestStatus: NotSet = 0,Unassigned = 1,Assigned = 2,Acknowledged = 3,Completed = 4,Rejected = 5,Closed = 6,Pending = 7,Other = 8
     */
    private function send_change_request($request_params) {
       
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        $send_change_request = $this->format_send_change_request($request_params);

        if ($send_change_request['status'] == SUCCESS_STATUS) {
            $send_change_response = $this->process_request($send_change_request ['request'], $send_change_request ['url'], $send_change_request ['SOAPAction'], $send_change_request ['remarks']);
            $send_change_response = Converter::createArray($send_change_response);
            
            if ($this->valid_cancellation_response ( $send_change_response )) {
                $response ['status'] = SUCCESS_STATUS;
                $response ['data']['send_change_response'] = $send_change_response;
            }
            else{
                $error_message = 'Cancellation Failed';
                $response ['message'] = $error_message;
            }
        }
        return $response;
    }
     /**
     * Forms the SendChangeRequest
     * @param unknown_type $request
     */
    private function format_send_change_request($params) {
     
        $pnr = $params['booking_transaction_details'][0]['pnr'];
        $booking_response['s:Envelope']['s:Body']['BookingCommitResponse']['BookingUpdateResponseData']['Success']['RecordLocator'] = $pnr;
        $run_book_det_res = $this->run_get_booking_details_service($booking_response);
        $journey_list = $run_book_det_res['data']['get_booking_details_response']['Booking']['Journeys']['Journey'];
        $journey_list = force_multple_data_format($journey_list);
        $journey = '';
        foreach($journey_list as $j_key => $j_value){
            
            $segment_xml = '';
            $segments = force_multple_data_format($j_value['Segments']['Segment']);
            foreach($segments as $seg_k => $seg_value){
              
                $segment_xml .= '<Segment>
                                    <State xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Common">New</State>
                                    <ActionStatusCode>' . $seg_value['ActionStatusCode'] . '</ActionStatusCode>
                                    <ArrivalStation>' . $seg_value['ArrivalStation'] . '</ArrivalStation>
                                    <CabinOfService i:nil="true"></CabinOfService>
                                    <ChangeReasonCode i:nil="true"></ChangeReasonCode>
                                    <DepartureStation>' . $seg_value['DepartureStation'] . '</DepartureStation>
                                    <PriorityCode i:nil="true"></PriorityCode>
                                    <SegmentType i:nil="true"></SegmentType>
                                    <STA>' . $seg_value['STA'] . '</STA>
                                    <STD>' . $seg_value['STD'] . '</STD>
                                    <International>false</International>
                                    <FlightDesignator xmlns:a="http://schemas.navitaire.com/WebServices/DataContracts/Common">
                                        <a:CarrierCode>' . $seg_value ['FlightDesignator']['a:CarrierCode'] . '</a:CarrierCode>
                                        <a:FlightNumber>' . $seg_value ['FlightDesignator']['a:FlightNumber'] . '</a:FlightNumber>
                                        <a:OpSuffix i:nil="true"></a:OpSuffix>
                                    </FlightDesignator>
                                    <XrefFlightDesignator i:nil="true" xmlns:a="http://schemas.navitaire.com/WebServices/DataContracts/Common"></XrefFlightDesignator>
                                    <Fares i:nil="true"></Fares>
                                    <Legs i:nil="true"></Legs>
                                    <PaxBags i:nil="true"></PaxBags>
                                    <PaxSeats i:nil="true"></PaxSeats>
                                    <PaxSSRs i:nil="true"></PaxSSRs>
                                    <PaxSegments i:nil="true"></PaxSegments>
                                    <PaxTickets i:nil="true"></PaxTickets>
                                    <PaxSeatPreferences i:nil="true"></PaxSeatPreferences>
                                    <SalesDate>0001-01-01T00:00:00</SalesDate>
                                    <SegmentSellKey i:nil="true"></SegmentSellKey>
                                    <PaxScores i:nil="true"></PaxScores>
                                    <ChannelType>Default</ChannelType>
                                </Segment>';
            }
            $journey .= '<Journeys>
                            <Journey>
                                <State xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Common">New</State>
                                <NotForGeneralUse>false</NotForGeneralUse>
                                <Segments>
                                    ' . $segment_xml . '
                                </Segments>
                                <JourneySellKey>' . $j_value['JourneySellKey'] . '</JourneySellKey>
                            </Journey>
                        </Journeys>';
        }
        $cancel_request = '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/" >
                        ' . $this->soap_header () . '
                        <s:Body>
                        <CancelRequest xmlns="http://schemas.navitaire.com/WebServices/ServiceContracts/BookingService">
                            <CancelRequestData xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Booking"
                            xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
                                <CancelBy>Journey</CancelBy>
                                <CancelJourney>
                                    <CancelJourneyRequest>
                                        ' . $journey . '
                                    <WaivePenaltyFee>false</WaivePenaltyFee>
                                    <WaiveSpoilageFee>false</WaiveSpoilageFee>
                                    <PreventReprice>false</PreventReprice>
                                    </CancelJourneyRequest>
                                </CancelJourney>
                                <CancelFee i:nil="true"></CancelFee>
                                <CancelSSR i:nil="true"></CancelSSR>
                            </CancelRequestData>
                        </CancelRequest>
                        </s:Body>
                        </s:Envelope>';
        $request ['request'] = $cancel_request;
        $request ['url'] = $this->config['end_point']['booking'];
        $request ['remarks'] = 'Cancel Booking';
        $request ['SOAPAction'] = 'http://schemas.navitaire.com/WebServices/IBookingManager/Cancel';
        $request ['status'] = SUCCESS_STATUS;
        // debug($request);exit;
        return $request;
    }
    function remove_node(&$node) {
        $pnode = $node->parentNode;
        // debug($pnode);exit;
        $this->remove_children($node);
        $pnode->removeChild($node);
    }
    function remove_children(&$node) {
        while ($node->firstChild) {
            while ($node->firstChild->firstChild) {
                $this->remove_children($node->firstChild);
            }

            $node->removeChild($node->firstChild);
        }
    }
    /**
     * Save Book Service Response
     * @param unknown_type $book_response
     * @param unknown_type $app_reference
     * @param unknown_type $sequence_number
     */
    private function save_book_response_details($book_response, $app_reference, $sequence_number, $payment_status) {
        // debug($book_response);exit;
        $update_data = array();
        $update_condition = array();
        $pnr = $book_response['s:Envelope']['s:Body']['BookingCommitResponse']['BookingUpdateResponseData']['Success']['RecordLocator'];
        $update_data['pnr'] = $pnr;
        $update_data['book_id'] = $pnr;

        $update_condition['app_reference'] = $app_reference;
        $update_condition['sequence_number'] = $sequence_number;

        $this->CI->custom_db->update_record('flight_booking_transaction_details', $update_data, $update_condition);
        if($payment_status == 'BOOKING_HOLD'){
            $update_data1['offline_supplier_name'] = 'payment not done';
            $update_condition1['app_reference'] = $app_reference;
            $this->CI->custom_db->update_record('flight_booking_details', $update_data1, $update_condition1);
        }
        $flight_booking_status = 'BOOKING_CONFIRMED';
        $this->CI->common_flight->update_flight_booking_status($flight_booking_status, $app_reference, $sequence_number, $this->booking_source);
    }
    /* save flight ticket details */
    private function save_flight_ticket_details($booking_params, $book_response, $app_reference, $sequence_number, $search_id){
        // debug($booking_params);exit;
        $flight_booking_transaction_details = $this->CI->custom_db->single_table_records('flight_booking_transaction_details', '*', array('app_reference' => $app_reference, 'sequence_number' => $sequence_number));
        $flight_booking_transaction_details_fk = $flight_booking_transaction_details['data'][0]['origin'];
        $flight_booking_itinerary_details_fk = $this->CI->custom_db->single_table_records('flight_booking_itinerary_details', 'airline_code', array('app_reference' => $app_reference));
        $pnr = $book_response['s:Envelope']['s:Body']['BookingCommitResponse']['BookingUpdateResponseData']['Success']['RecordLocator'];
        $book_id = $pnr;

        //1. Update Bookinf Id and PNR Details
        $update_pnr_data = array();
        $update_pnr_data['book_id'] = $book_id;
        $update_pnr_data['pnr'] = $pnr;
        $this->CI->custom_db->update_record('flight_booking_transaction_details', $update_pnr_data, array('origin' => $flight_booking_transaction_details_fk));

        //2.Update Price Details
        $passenger_details = $this->CI->custom_db->single_table_records('flight_booking_passenger_details', '', array('app_reference' => $app_reference));
        $passenger_details =$passenger_details['data'];
        // debug($passenger_details);exit;
        $itineray_price_details = $booking_params['flight_data']['PriceBreakup'];
        $itineray_price_details['TotalDisplayFare'] = $flight_booking_transaction_details['data'][0]['total_fare'];
  
        $airline_code = '';
        if (isset($flight_booking_itinerary_details_fk['data'][0]['airline_code'])) {
            $airline_code = $flight_booking_itinerary_details_fk['data'][0]['airline_code'];
        }
        $flight_price_details = $this->CI->common_flight->final_booking_transaction_fare_details($itineray_price_details, $search_id, $this->booking_source, $airline_code);
        // debug($flight_price_details);exit;
        $fare_details = $flight_price_details['Price'];
        $fare_breakup = $flight_price_details['PriceBreakup'];
        $passenger_breakup = $fare_breakup['PassengerBreakup'];
        $single_pax_fare_breakup = $this->CI->common_flight->get_single_pax_fare_breakup($passenger_breakup);
        //I have to fix this one
        // $this->CI->common_flight->update_flight_booking_tranaction_price_details($app_reference, $sequence_number, $fare_details['commissionable_fare'], $fare_details['admin_commission'], $fare_details['agent_commission'], $fare_details['admin_tds'], $fare_details['agent_tds'], $fare_details['admin_markup'], $fare_breakup);
        $update_data['fare_breakup'] = json_encode($itineray_price_details);
        
        $update_condition = array();
        $update_condition['app_reference'] = $app_reference;
        $update_condition['sequence_number'] = $sequence_number;
        $this->CI->custom_db->update_record('flight_booking_transaction_details', $update_data, $update_condition);
        //update Airline PNR
        $airsegment = $booking_params['flight_data']['FlightDetails']['Details'][0];
        
        foreach($airsegment as $seg_key => $seg_value){
            
            $origin = $seg_value['Origin']['AirportCode'];
            $destination = $seg_value['Destination']['AirportCode'];
            $dept_time = $seg_value['Origin']['DateTime'];
           
            //itinerary condition for update
            $update_itinerary_condition = array();
            $update_itinerary_condition['flight_booking_transaction_details_fk'] = $flight_booking_transaction_details_fk;
            $update_itinerary_condition['app_reference'] = $app_reference;
            $update_itinerary_condition['from_airport_code'] = $origin;
            $update_itinerary_condition['to_airport_code'] = $destination;
            $update_itinerary_condition['departure_datetime'] = $dept_time;

            //itinerary updated data
            $update_itinerary_data = array();
            $update_itinerary_data['airline_pnr'] = $pnr;
            $GLOBALS['CI']->custom_db->update_record('flight_booking_itinerary_details', $update_itinerary_data, $update_itinerary_condition);
        }
       

        // $passenger_details = force_multple_data_format($passenger_details);
        $get_passenger_details_condition = array();
        $get_passenger_details_condition['flight_booking_transaction_details_fk'] = $flight_booking_transaction_details_fk;
        $passenger_details_data = $GLOBALS['CI']->custom_db->single_table_records('flight_booking_passenger_details', 'origin, passenger_type', $get_passenger_details_condition);
        $passenger_details_data = $passenger_details_data['data'];
        $passenger_origins = group_array_column($passenger_details_data, 'origin');
        $passenger_types = group_array_column($passenger_details_data, 'passenger_type');
        foreach ($passenger_details as $pax_k => $pax_v) {
            $passenger_fk = intval(array_shift($passenger_origins));
            $pax_type = array_shift($passenger_types);
            
            switch ($pax_type) {
                case 'Adult':
                 $pax_type = 'ADT';
                    break;
                case 'Child':
                    $pax_type = 'CHD';
                    break;
                case 'Infant':
                    $pax_type = 'INF';
                    break;
            }
            $ticket_id = $pnr;
            $tkt_number = $pnr;
          
            //Update Passenger Ticket Details
            $this->CI->common_flight->update_passenger_ticket_info($passenger_fk, $ticket_id, $tkt_number, @$single_pax_fare_breakup[$pax_type]);
        }
      
    }
    /**
     * Save Cancellation Details
     * @param unknown_type $cancellation_details
     * @param unknown_type $passenger_origin
     */
    public function save_ticket_cancellation_details($cancellation_details, $passenger_origin, $pass_count) {
        // debug($cancellation_details);exit;
        $tcrn_number = $cancellation_details['s:Envelope']['s:Body']['CancelResponse']['BookingUpdateResponseData']['Success']['RecordLocator'];
        $refund_amount = $cancellation_details['s:Envelope']['s:Body']['CancelResponse']['BookingUpdateResponseData']['Success']['PNRAmount']['BalanceDue'];
        $cancellation_chargers = $cancellation_details['s:Envelope']['s:Body']['CancelResponse']['BookingUpdateResponseData']['Success']['PNRAmount']['TotalCost'];
        $cancelltion_tax = 0;
        $refund_amount = $refund_amount/$pass_count;
        $data['cancellation_processed_on'] = date('Y-m-d H:i:s');
        $data['RequestId'] = $tcrn_number;
        $data['API_RefundedAmount'] = ltrim($refund_amount,'-');
        $data['API_CancellationCharge'] = $cancellation_chargers/$pass_count;
        $data['API_ServiceTaxOnRefundAmount'] = $cancelltion_tax;
        $data['API_SwachhBharatCess'] = '0.00';
        $data['API_KrishiKalyanCess'] = '0.00';
        $data['ChangeRequestStatus'] = 1;
        $data['statusDescription'] = 'Unassigned';
        $data['current_status'] = 1;
        $data['StatusCode'] = '';
        $data['statusCategory'] = '';
        $data['refund_amount'] = '0.00';
        $data['cancellation_charge'] = '0.00';
        $data['service_tax_on_refund_amount'] = '0.00';
        $data['swachh_bharat_cess'] = '0.00';
        $data['krishi_Kalyan_cess'] = '0.00';
        $data['refund_comments'] = '';
        $data['refund_date'] = date('Y-m-d h:i:s');
        $data['created_by_id'] = '0';
        $data['created_datetime'] = date('Y-m-d h:i:s');

        $pax_cancel_details_exists = $this->CI->custom_db->single_table_records('flight_cancellation_details', '*', array('passenger_fk' => $passenger_origin));
        if ($pax_cancel_details_exists['status'] == true) {
            //Update the Data
            $this->CI->custom_db->update_record('flight_cancellation_details', $data, array('passenger_fk' => $passenger_origin));
        } else {
            //Insert Data
            $data['passenger_fk'] = $passenger_origin;
            $data['created_by_id'] = intval(@$this->entity_user_id);
            $data['created_datetime'] = date('Y-m-d H:i:s');
            $data['cancellation_requested_on'] = date('Y-m-d H:i:s');
            $this->CI->custom_db->insert_record('flight_cancellation_details', $data);
        } 
        // debug($cancellation_details);exit;
    }
    /**
     * check if the search RS is valid or not
     * @param array $search_result
     * search result RS to be validated
     */
    private function valid_search_result_old($search_result) {
            // debug($search_result);exit;
        if (valid_array($search_result) == true && isset($search_result['s:Envelope']['s:Body']['GetAvailabilityByTripResponse']) == true && isset($search_result['s:Envelope']['s:Body']['GetAvailabilityByTripResponse']['GetTripAvailabilityResponse']['Schedules']) == true && valid_array($search_result['s:Envelope']['s:Body']['GetAvailabilityByTripResponse']['GetTripAvailabilityResponse']['Schedules']['ArrayOfJourneyDateMarket']) == true) {
            return true;
        } else {
            if($search_data['data']['trip_type'] && $search_data['data']['is_domestic'] == true){
                return true;
            }
            return false;
        }
    }
     /**
     * check if the search RS is valid or not
     * @param array $search_result
     * search result RS to be validated
     */
    private function valid_search_result($flight_raw_data) {
        $status = array();
        foreach($flight_raw_data as $flight_data){
            $flight_data = Converter::createArray($flight_data);
            if (valid_array($flight_data) == true && isset($flight_data['s:Envelope']['s:Body']['GetAvailabilityByTripResponse']) == true && isset($flight_data['s:Envelope']['s:Body']['GetAvailabilityByTripResponse']['GetTripAvailabilityResponse']['Schedules']) == true && valid_array($flight_data['s:Envelope']['s:Body']['GetAvailabilityByTripResponse']['GetTripAvailabilityResponse']['Schedules']['ArrayOfJourneyDateMarket']) == true) {
                $status[] = 1;
            } else {
                $status[] = 0;
            }
        }
        if(count($status) > 1){
            $status = array_count_values($status);
            if($status[1] == 2){
                return true;
            }
        }
        else{
            if($status[0] == 1){
                return true;
            }
            else{
                return false;
            }
        }
     }
    /**
     * validate price RS details
     *
     * @param array $price_result           
     */
    private function valid_price_result($price_result) {
        if (valid_array ( $price_result ) == true and isset ( $price_result ['s:Envelope'] ) == true and isset ( $price_result ['s:Envelope'] ['s:Body'] ) == true and isset ( $price_result ['s:Envelope'] ['s:Body'] ['PriceItineraryResponse'] ) == true and isset ( $price_result ['s:Envelope'] ['s:Body'] ['PriceItineraryResponse'] ['Booking'] ) == true && valid_array ( @$price_result ['s:Envelope'] ['s:Body'] ['PriceItineraryResponse'] ['Booking'] ['Journeys'] )) {
            return true;
        } else {
           return false;
        }
    }
    private function valid_sell_response($sell_response) {
        if (isset ( $sell_response ) && isset ( $sell_response ['s:Envelope'] ['s:Body'] ['SellResponse'] ['BookingUpdateResponseData'] ['Success'] ['PNRAmount'] ) && valid_array ( $sell_response ['s:Envelope'] ['s:Body'] ['SellResponse'] ['BookingUpdateResponseData'] ['Success'] ['PNRAmount'] )) {
           
            return true;
        } else {
            return false;
        }
    }
      private function valid_update_contact_response($update_contact_response) {
        if (isset ( $update_contact_response ) && isset ( $update_contact_response ['s:Envelope'] ['s:Body'] ['UpdateContactsResponse'] ['BookingUpdateResponseData'] ['Success'] ['PNRAmount'] ) && valid_array ( $update_contact_response ['s:Envelope'] ['s:Body'] ['UpdateContactsResponse'] ['BookingUpdateResponseData'] ['Success'] ['PNRAmount'] )) {
           
            return true;
        } else {
            return false;
        }
    }
    private function valid_update_passenger_response($process_output) {
        if (isset ( $process_output ) && isset ( $process_output ['s:Envelope'] ['s:Body'] ['UpdatePassengerResponse'] ['BookingUpdateResponseData'] ['Success'] ) && valid_array ( $process_output ['s:Envelope'] ['s:Body'] ['UpdatePassengerResponse'] ['BookingUpdateResponseData'] ['Success'] )) {
            return true;
        } else {
            return false;
        }
    }
    function valid_cancellation_response($cancellation_response) {
        if (isset ( $cancellation_response ['s:Envelope'] ['s:Body'] ['CancelResponse'] ['BookingUpdateResponseData'] ['Success'] )) {
            return true;
        }
        return false;
    }
    function valid_meal_availability($meal_availibity) {
       
        if (isset ( $meal_availibity ['s:Envelope'] ['s:Body'] ['GetSSRAvailabilityForBookingResponse'] ) && valid_array ( $meal_availibity ['s:Envelope'] ['s:Body'] ['GetSSRAvailabilityForBookingResponse'] )) {
            return true;
        }
        return false;
    }
    private function valid_assign_seat_response($process_output) {
        if (isset ( $process_output ) && isset ( $process_output ['s:Envelope'] ['s:Body'] ['AssignSeatsResponse'] ['BookingUpdateResponseData'] ['Success'] ['PNRAmount'] ) && valid_array ( $process_output ['s:Envelope'] ['s:Body'] ['AssignSeatsResponse'] ['BookingUpdateResponseData'] ['Success'] ['PNRAmount'] )) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * Authentication Request
    */
    public function get_authentication_request($internal_request = false) { 
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        
        $active_booking_source = $this->is_active_booking_source();
       // $active_booking_source['status'] = SUCCESS_STATUS;
        if ($active_booking_source['status'] == SUCCESS_STATUS) {
            $authenticate_request = $this->authenticate_request();
           
            if ($authenticate_request['status'] = SUCCESS_STATUS) {
                $response ['status'] = SUCCESS_STATUS;
                $curl_request = $this->form_curl_params($authenticate_request['request'], $authenticate_request['url'], $authenticate_request['SOAPAction']);
                $response ['data'] = $curl_request['data'];
            }
            if ($internal_request == true) {
                $response ['data']['remarks'] = 'Authentication(Spicejet)';
            }
        }
        return $response;
    }
    public function valid_create_session_response($response)
    {   
        $result = false;
        if(isset($response['s:Envelope']['s:Body']['LogonResponse']['Signature']) ==true){
            $result =  true;
        }
        return $result;
    }
    /**
     * Authentcation RQ for api
     */
    private function authenticate_request() {
        // debug($this->config);exit;
        $request = array();
        $authentication_request = 
        '<s:Envelope xmlns:str="http://exslt.org/strings" xmlns:a="http://schemas.navitaire.com/WebServices/DataContracts/Booking" xmlns:b="http://schemas.navitaire.com/WebServices/DataContracts/Common" xmlns:s="http://schemas.xmlsoap.org/soap/envelope/" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
            <s:Header>
                <h:ContractVersion xmlns:h="http://schemas.navitaire.com/WebServices">'.$this->config['contract_version'].'</h:ContractVersion>
                <h:EnableExceptionStackTrace xmlns:h="http://schemas.navitaire.com/WebServices">false</h:EnableExceptionStackTrace>
            </s:Header>
            <s:Body>
                <LogonRequest xmlns="http://schemas.navitaire.com/WebServices/ServiceContracts/SessionService">
                    <logonRequestData xmlns:a="http://schemas.navitaire.com/WebServices/DataContracts/Session">
                        <a:DomainCode>'.$this->config['agent_domain'].'</a:DomainCode>
                        <a:AgentName>'.$this->config['agent_id'].'</a:AgentName>
                        <a:Password>'.$this->config['password'].'</a:Password>
                        <a:LocationCode i:nil="true" xmlns:i="http://www.w3.org/2001/XMLSchema-instance"/>
                        <a:RoleCode i:nil="true" xmlns:i="http://www.w3.org/2001/XMLSchema-instance"/>
                        <a:TerminalInfo i:nil="true" xmlns:i="http://www.w3.org/2001/XMLSchema-instance"/>
                        <a:ClientName i:nil="true" xmlns:i="http://www.w3.org/2001/XMLSchema-instance"/>
                    </logonRequestData>
                </LogonRequest>
            </s:Body>
        </s:Envelope>';
        $request ['request'] = $authentication_request;
        $request ['url'] = $this->config['end_point']['session'];
        $request ['SOAPAction'] = 'http://schemas.navitaire.com/WebServices/ISessionManager/Logon';
        $request ['status'] = SUCCESS_STATUS;
        // debug($request);exit;
        return $request;
    }
     /**
     * process soap API request
     *
     * @param string $request
    */
    function form_curl_params($request, $url, $soapaction) {
        $data['status'] = SUCCESS_STATUS;
        $data['message'] = '';
        $data['data'] = array();

        $curl_data = array();
        $curl_data['booking_source'] = $this->booking_source;
        $curl_data['request'] = $request;
        $curl_data['url'] = $url;
        $curl_data['SOAPAction'] = $soapaction;
        $curl_data['header'] = array( 'Content-Type: text/xml; charset="utf-8"',"SOAPAction: {$soapaction}");
        $data['data'] = $curl_data;
        // debug($data);exit;
        return $data;
    }
     /**
     * Process API Request
     * @param unknown_type $request
     * @param unknown_type $url
     */
    function process_request($request, $url, $soapaction, $remarks = '') {
        $irequest = $request; //serialize(base64_encode($request));      
        $insert_id = $this->CI->api_model->store_api_request($url, base64_encode($irequest), $remarks);
        $insert_id = intval(@$insert_id['insert_id']);
        // echo $remarks;exit;
        // echo $insert_id;exit;

        try {
            $httpHeader = array( 'Content-Type: text/xml; charset="utf-8"',"SOAPAction: {$soapaction}");
            $ch = curl_init(); 
            curl_setopt($ch, CURLOPT_URL, $url); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
            curl_setopt($ch, CURLOPT_TIMEOUT, 60); 
            curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
            curl_setopt($ch, CURLOPT_POST, TRUE); 
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
            curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
            $response = curl_exec($ch);
            $error = curl_getinfo($ch);
        } catch (Exception $e) {
            $response = 'No Response Recieved From API';
        }
        // debug($response);exit;
        //Update the API Response
        $this->CI->api_model->update_api_response($response, $insert_id);
        if($remarks == 'SSR Request'){
            // echo $insert_id;exit;
        }
        $error = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $response;
    }
    public function search_data($search_id) {
        $response ['status'] = true;
        $response ['data'] = array();
        if (empty($this->master_search_data) == true and valid_array($this->master_search_data) == false) {
            $clean_search_details = $this->CI->flight_model->get_safe_search_data($search_id);

            if ($clean_search_details ['status'] == true) {
                $response ['status'] = true;
                $response ['data'] = $clean_search_details ['data'];
                //debug($clean_search_details);exit;
                // 28/12/2014 00:00:00 - date format
                if ($clean_search_details['data']['trip_type'] == 'multicity') {
                    $response ['data'] ['from_city'] = $clean_search_details ['data'] ['from'];
                    $response ['data'] ['to_city'] = $clean_search_details ['data'] ['to'];
                    $response ['data'] ['depature'] = $clean_search_details ['data'] ['depature'];
                    $response ['data'] ['return'] = $clean_search_details ['data'] ['depature'];
                } else {
                    $response ['data'] ['from'] = substr(chop(substr($clean_search_details ['data'] ['from'], - 5), ')'), - 3);
                    $response ['data'] ['to'] = substr(chop(substr($clean_search_details ['data'] ['to'], - 5), ')'), - 3);
                    $response ['data'] ['depature'] = date("Y-m-d", strtotime($clean_search_details ['data'] ['depature'])) . 'T00:00:00';
                    if(isset($clean_search_details ['data'] ['return'])) {
                    $response ['data'] ['return'] = date("Y-m-d", strtotime($clean_search_details ['data'] ['return'])) . 'T00:00:00';
                    }
                }

                switch ($clean_search_details ['data'] ['trip_type']) {

                    case 'oneway' :
                        $response ['data'] ['type'] = 'OneWay';
                        break;

                    case 'circle' :
                        $response ['data'] ['type'] = 'Return';
                        $response ['data'] ['return'] = date("Y-m-d", strtotime($clean_search_details ['data'] ['return'])) . 'T00:00:00';
                        break;

                    default :
                        $response ['data'] ['type'] = 'OneWay';
                }
                if ($response ['data'] ['is_domestic'] == true and $response ['data'] ['trip_type'] == 'return') {
                    $response ['data'] ['domestic_round_trip'] = true;
                    //$response ['status'] = false;
                } else {
                    $response ['data'] ['domestic_round_trip'] = false;
                }
                $response ['data'] ['adult'] = $clean_search_details ['data'] ['adult_config'];
                $response ['data'] ['child'] = $clean_search_details ['data'] ['child_config'];
                $response ['data'] ['infant'] = $clean_search_details ['data'] ['infant_config'];
                $response ['data'] ['v_class'] = @$clean_search_details ['data'] ['v_class'];
                $response ['data'] ['carrier'] = implode($clean_search_details ['data'] ['carrier']);
                $this->master_search_data = $response ['data'];
            } else {
                $response ['status'] = false;
            }
        } else {
            $response ['data'] = $this->master_search_data;
        }
        $this->search_hash = md5(serialized_data($response ['data']));

        return $response;
    }

    /**
     * Update markup currency for price object of flight
     * 
     * @param object $price_summary         
     * @param object $currency_obj          
     */
    function update_markup_currency(& $price_summary, & $currency_obj) {
        
    }
    /**
     * get total price from summary object
     * 
     * @param object $price_summary         
     */
    function total_price($price_summary) {
        
    }
    /**
     * Process booking
     * @param array $booking_params
     */
    
}
