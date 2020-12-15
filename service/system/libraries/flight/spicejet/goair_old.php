<?php

require_once BASEPATH . 'libraries/flight/Common_api_flight.php';

class Goair extends Common_Api_Flight {


    var $master_search_data;
    var $search_hash;
    protected $token;
    private $end_user_ip = '127.0.0.1';
    var $api_session_id;

    function __construct() {

        parent::__construct(META_AIRLINE_COURSE, GOAIR_FLIGHT_BOOKING_SOURCE);
        $this->CI = &get_instance();
        $this->CI->load->library('Converter');
        $this->CI->load->library('ArrayToXML');
        // $this->set_api_credentials();
        $this->set_api_signature();
        
    }
     /**
     * Setting Api Credentials
    */
    // private function set_api_credentials()
    // {
    //     $this->conversation_id = time().$this->config['sabre_email'];

    //     $this->message_id = "mid:".time().$this->config['sabre_email'];
    //     $this->timestamp  = gmdate("Y-m-d\TH-i-s\Z");
    //     $this->timetolive = gmdate("Y-m-d\TH-i-s\Z");

    // }
    /**
     * Setting Signature
    */
    public function set_api_signature($authentication_response = '') {
        $this->api_session_id = '1S4CwSr8H/o=|9j+nT4c9XgMBYk8WZioFztaZJozoMMndgrqu7U9D05ksDXfHRyNQ32mSLpgZRf/2tLXjgurdbcs2C4tdKgJfjwmGn+3ZkWfZMw42VAQdrQiFwXLCnnlxE2oECzI9MKH+BkNfsbI8sxw=';
        if (empty($this->api_session_id) == true) {

            if (empty($authentication_response) == false) {
               
                $authentication_response = Converter::createArray($authentication_response);
                // debug($authentication_response);exit;
                //store in database
                
                if ($this->valid_create_session_response($authentication_response)) {
                    $authenticate_token = $authentication_response['s:Envelope']['s:Body']['LogonResponse']['Signature'];                    
                    $session_id = trim($authenticate_token);

                    $this->CI->api_model->update_api_session_id($this->booking_source, $session_id);

                }
            } else {

                $session_expiry_time = 2; //In minutes

                $session_id = $this->CI->api_model->get_api_session_id($this->booking_source, $session_expiry_time);
               
                if (empty($session_id) == true) {

                    $authentication_request = $this->get_authentication_request(true);
                    if ($authentication_request['status'] == SUCCESS_STATUS) {
                        $authentication_request = $authentication_request['data'];
                        $authentication_response = $this->process_request($authentication_request ['request'], $authentication_request ['url'], $authentication_request ['SOAPAction'], $authentication_request ['remarks']);
                        // debug($authentication_response);exit;
                        $this->set_api_signature($authentication_response);
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
       
        if (empty($this->api_session_id) == true) {
            // return failure object as the login signature is not set
            return $response;
        }
        /* get search criteria based on search id */
        $search_data = $this->search_data($search_id);
        debug($search_data);exit;
        if ($search_data ['status'] == SUCCESS_STATUS) {
            // Flight search RQ
            $search_request = $this->search_request($search_data ['data']);
           
            if ($search_request ['status'] = SUCCESS_STATUS) {
                $response ['status'] = SUCCESS_STATUS;
                $curl_request = $this->form_curl_params($search_request ['request'], $search_request ['url'], $search_request['SOAPAction']);
                $response ['data'] = $curl_request['data'];
            }
        }
        debug($response);exit;
        return $response;
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
        $advanced_filters = '<FlightNumber i:nil="true"></FlightNumber>
                        <FlightType>All</FlightType>
                        <PaxCount>'.$total_pax.'</PaxCount>
                        <Dow>Daily</Dow>
                        <CurrencyCode>INR</CurrencyCode>
                        <DisplayCurrencyCode i:nil="true"></DisplayCurrencyCode>
                        <DiscountCode i:nil="true"></DiscountCode>
                        <PromotionCode></PromotionCode>
                        <AvailabilityType>Default</AvailabilityType>
                        <SourceOrganization i:nil="true"></SourceOrganization>
                        <MaximumConnectingFlights>0</MaximumConnectingFlights>
                        <AvailabilityFilter>Default</AvailabilityFilter>
                        <FareClassControl>CompressByProductClass</FareClassControl>
                        <MinimumFarePrice>0</MinimumFarePrice>
                        <MaximumFarePrice>0</MaximumFarePrice>
                        <ProductClassCode i:nil="true"></ProductClassCode>
                        <SSRCollectionsMode>All</SSRCollectionsMode>
                        <InboundOutbound>None</InboundOutbound>
                        <NightsStay>0</NightsStay>
                        <IncludeAllotments>false</IncludeAllotments>
                        <BeginTime i:nil="true"></BeginTime>
                        <EndTime i:nil="true"></EndTime>
                        <DepartureStations i:nil="true" xmlns:a="http://schemas.microsoft.com/2003/10/Serialization/Arrays"></DepartureStations>
                        <ArrivalStations i:nil="true" xmlns:a="http://schemas.microsoft.com/2003/10/Serialization/Arrays"></ArrivalStations>
                        <FareTypes xmlns:a="http://schemas.microsoft.com/2003/10/Serialization/Arrays">
                            <a:string>R</a:string>
                        </FareTypes>
                        <ProductClasses xmlns:a="http://schemas.microsoft.com/2003/10/Serialization/Arrays"></ProductClasses>
                        <FareClasses i:nil="true" xmlns:a="http://schemas.microsoft.com/2003/10/Serialization/Arrays"></FareClasses>';
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
        for($i=0; $i<count($search_params['from']); $i++){
            $AvailabilityRequest .= '<AvailabilityRequest>
                        <DepartureStation>'.trim($search_params['from'][$i]).'</DepartureStation>
                        <ArrivalStation>'.trim($search_params['to'][$i]).'</ArrivalStation>
                        <BeginDate>'.$search_params['depature'][$i].'</BeginDate>
                        <EndDate>'.$search_params['depature'][$i].'</EndDate>
                        <CarrierCode>G8</CarrierCode>'.$advanced_filters.'
                         <PaxPriceTypes>'.$pax_config.'</PaxPriceTypes>
                        <JourneySortKeys xmlns:a="http://schemas.navitaire.com/WebServices/DataContracts/Common/Enumerations">
                            <a:JourneySortKey>ServiceType</a:JourneySortKey>
                        </JourneySortKeys>
                        <TravelClassCodes xmlns:a="http://schemas.microsoft.com/2003/10/Serialization/Arrays"></TravelClassCodes>
                        <IncludeTaxesAndFees>true</IncludeTaxesAndFees>
                        <FareRuleFilter>Default</FareRuleFilter>
                        <LoyaltyFilter>MonetaryOnly</LoyaltyFilter>
                    </AvailabilityRequest>';
            if($search_params['trip_type'] == 'return') {  
                $AvailabilityRequest .= '<AvailabilityRequest>
                    <DepartureStation>'.trim($search_params['to'][$i]).'</DepartureStation>
                    <ArrivalStation>'.trim($search_params['from'][$i]).'</ArrivalStation>
                    <BeginDate>'.$search_params['return'][$i].'</BeginDate>
                    <EndDate>'.$search_params['return'][$i].'</EndDate>
                     <CarrierCode>G8</CarrierCode>'.$advanced_filters.'
                         <PaxPriceTypes>'.$pax_config.'</PaxPriceTypes>
                        <JourneySortKeys xmlns:a="http://schemas.navitaire.com/WebServices/DataContracts/Common/Enumerations">
                            <a:JourneySortKey>ServiceType</a:JourneySortKey>
                        </JourneySortKeys>
                        <TravelClassCodes xmlns:a="http://schemas.microsoft.com/2003/10/Serialization/Arrays"></TravelClassCodes>
                        <IncludeTaxesAndFees>true</IncludeTaxesAndFees>
                        <FareRuleFilter>Default</FareRuleFilter>
                        <LoyaltyFilter>MonetaryOnly</LoyaltyFilter>
                    </AvailabilityRequest>';  
            }  

        }
        $xml_request = '<?xml version="1.0" encoding="utf-8"?>
                        <s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
                            ' . $this->soap_header () . '
                            <s:Body>
                                <GetAvailabilityRequest xmlns="http://schemas.navitaire.com/WebServices/ServiceContracts/BookingService">
                                    <TripAvailabilityRequest xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Booking" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
                                        <AvailabilityRequests>
                                            ' . $AvailabilityRequest . '
                                        </AvailabilityRequests>
                                    </TripAvailabilityRequest>
                                </GetAvailabilityRequest>
                            </s:Body>
                        </s:Envelope>';
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
    /**
     * Returns Flight List
     * @param unknown_type $search_id
     */
    public function get_flight_list($flight_raw_data, $search_id) {
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        $search_data = $this->search_data($search_id);
        if ($search_data ['status'] == SUCCESS_STATUS) {
            $api_response = Converter::createArray($flight_raw_data);
            // debug($api_response);exit;
            if ($this->valid_search_result($api_response) == TRUE) {
                $clean_format_data = $this->format_search_data_response($api_response, $search_data ['data']);
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
    function format_search_data_response($search_result, $search_data) {
       
        $trip_type = isset($search_data ['is_domestic']) && !empty($search_data ['is_domestic']) ? 'domestic' : 'international';
        $Results = $search_result['s:Envelope']['s:Body']['GetAvailabilityByTripResponse']['GetTripAvailabilityResponse']['Schedules']['ArrayOfJourneyDateMarket'];
        $Results = force_multple_data_format($Results);
        // debug($Results);exit;
        $price_itinerary_keys = array();
        foreach($Results as $result_k => $result_v){
            $JourneyDateMarket = force_multple_data_format($result_v['JourneyDateMarket']);
            foreach($JourneyDateMarket as $jouney_k => $jouney_v){
                if(valid_array($jouney_v['Journeys'])){
                    $jounry_list = force_multple_data_format($jouney_v['Journeys']['Journey']);

                    foreach( $jounry_list as $flight_k => $flight_value) {

                        $Segments = force_multple_data_format ( $flight_value ['Segments'] ['Segment'] );
                        // debug($Segments);
                        $flight_details = array();
                        $key = array();
                        $key1 = array();
                        $key ['key'][$flight_k]['booking_source'] = $this->booking_source;
                        $key ['key'] [$flight_k] ['JourneySellKey'] [] = $flight_value ['JourneySellKey'];
                        $key1 ['key'] [0] ['booking_source'] = $this->booking_source;
                        $key1 ['key'] [0] ['JourneySellKey'] [] = $flight_value ['JourneySellKey'];
                        $flight_details = $this->flight_segment_summary($Segments, $flight_k, $key, $key1, $search_data);
                        
                        if (valid_array($flight_details)) {
                            foreach ($flight_details as $f__key => $flight_detail) {
                                $itinerary_key_details[$f__key][] = $this->itinerary_key_details ( $flight_detail ['key'], $flight_detail ['raw'] ['fare'] );
                                unset($flight_detail['key']);
                                unset($flight_detail['raw']);
                                $flight_list['JourneyList'] [$result_k] [] = $flight_detail;
                            }
                        }
                    }
                }
                
            }
        }
        // debug($itinerary_key_details);exit;
        if(valid_array($itinerary_key_details)) {

            foreach($itinerary_key_details as $___ky => $arr_key){
                $itenary_price_det = $this->get_itinerary_price ( $arr_key );
                
                if($itenary_price_det['status'] == SUCCESS_STATUS) {
                    $itinerary_price_details[] = $itenary_price_det['data'];    
                }
            }
        }
       
        // debug($flight_list);exit;
        $return_key = 0;
        // $arrival_station = array();
        foreach ($itinerary_price_details as $price_key => $price_data) {
            $journey_data = $price_data['s:Envelope']['s:Body']['PriceItineraryResponse']['Booking'];
            $journey_data = force_multple_data_format($journey_data['Journeys']['Journey']);
            
            if(isset($journey_data[0]['Segments']['Segment'][0])){
                $arrival_station = $journey_data[0]['Segments']['Segment'][0]['ArrivalStation'];
            }
            else{
                $arrival_station = $journey_data[0]['Segments']['Segment']['ArrivalStation']; 
            }
            // echo $_SERVER['REMOTE_ADDR'];exit;
            // echo $arrival_station;exit;
            $j_key = 0;
            $onward ='';
            foreach( $journey_data as $j_k => $journey_value) {
                $Segments = force_multple_data_format ( $journey_value ['Segments'] ['Segment'] );
                foreach($Segments as $k => $v){
                    // debug($v);
                    if(($arrival_station == $v['ArrivalStation']) || ($j_k == 0)){
                        $return_key = 0;
                    }
                    else if($onward!='yes'){
                        $onward = 'yes';
                        $return_key = 1;
                        $j_key = 0;
                    }
                  // echo 'return'.$return_key;
                  // echo "<br/>";
                    $result_token_arr[0]['booking_source'] = $this->booking_source;
                    $journey_fare = $v['Fares']['Fare'];
                    $price_data = $this->format_itineray_price_details_new($journey_fare, $search_data);
                    
                    if(count($flight_list['JourneyList']) > 1){
                        $flight_list['JourneyList'][$return_key][$j_key]['Price'] = $price_data;
                        $flight_list['JourneyList'][$return_key][$j_key]['ResultToken'][0]['flight_search_results'][0][0]['Price'] = $price_data;
                        $flight_list['JourneyList'][$return_key][$j_key]['ResultToken'][0]['flight_search_results'][0][0]['ResultToken'] = serialized_data($result_token_arr);

                        $flight_list['JourneyList'][$return_key][$j_key]['ResultToken'] = serialized_data($flight_list['JourneyList'][$return_key][$j_key]['ResultToken']);

                    }
                    else{
                        if(count($itinerary_price_details) > 1){
                            $flight_list['JourneyList'][0][$price_key]['Price'] = $price_data;

                            $flight_list['JourneyList'][0][$price_key]['ResultToken'][0]['flight_search_results'][0][0]['Price'] = $price_data;
                            $flight_list['JourneyList'][0][$price_key]['ResultToken'][0]['flight_search_results'][0][0]['ResultToken'] = serialized_data($result_token_arr);

                            $flight_list['JourneyList'][0][$price_key]['ResultToken'] = serialized_data($flight_list['JourneyList'][0][$price_key]['ResultToken']);

                        }
                        else{
                           // debug($price_data);exit;
                            $flight_list['JourneyList'][0][$j_k]['Price'] = $price_data;
                           
                            $flight_list['JourneyList'][0][$j_k]['ResultToken'][0]['flight_search_results'][0][0]['Price'] = $price_data;
                            $flight_list['JourneyList'][0][$j_k]['ResultToken'][0]['flight_search_results'][0][0]['Attr'] = $flight_list['JourneyList'][0][$j_k]['Attr'];
                            
                            $flight_list['JourneyList'][0][$j_k]['ResultToken'][0]['flight_search_results'][0][0]['ResultToken'] = serialized_data($result_token_arr);
                            // debug($flight_list['JourneyList'][0][$j_k]['ResultToken']);
                            $flight_list['JourneyList'][0][$j_k]['ResultToken'] = serialized_data($flight_list['JourneyList'][0][$j_k]['ResultToken']);
                        } 
                    }
                    
                   
                }
               $j_key++;
            }
        }
        // exit;
      // debug($response);exit;
        $response ['FlightDataList'] = $flight_list;
         // debug($response);exit;
        return $response;
        // debug($response);exit;

    }
     /**
     * Get flight details only
     *
     * @param array $segment
     */
    private function flight_segment_summary($segments, $journey_number, & $key, & $key1, $search_data, $cache_fare_object = false) {
       
        $summary = array();
        $flight_details = array();
        $price_details = array();
        $final_flight = array();
        $itineray_price = array();
        $fares_data = array();
        $SegmentSellKey = array ();
        // debug($segments);exit;
        $flightNumberList = array();
        foreach($segments as $k => $v){
            if(valid_array($v['Fares'])){
                $fares_data = @$v['Fares']['Fare'];
                $fares_data = force_multple_data_format(@$fares_data);
                $available_seats = @$fares_data[0]['AvailableCount'];
                $legs = $v['Legs']['Leg'];
                $legs = force_multple_data_format($legs);
                $is_leg = true;
                $attr = array();
                $attr ['action_status_code'] = $v ['ActionStatusCode'];
                $attr ['AvailableSeats'] = $available_seats;
                $attr ['Baggage'] = '15 Kg'; // neeed to check
                $attr ['CabinBaggage'] = '7 Kg'; // neeed to check
                foreach ($legs as $l_k => $l_v) {
                    // foreach($fares_data as $fare_key => $fares_value){
                        // debug($fares_value);exit;
                        $origin_code = $l_v ['DepartureStation'];
                        $destination_code = $l_v ['ArrivalStation'];
                        $departure_dt = str_replace('T',' ',$l_v ['STD']);
                        $arrival_dt = str_replace('T',' ',$l_v ['STA']);
                        $operator_code = $l_v['FlightDesignator']['a:CarrierCode'];
                        $operator_name = 'Goair';
                        $flight_number = $l_v['FlightDesignator']['a:FlightNumber'];
                        $no_of_stops = 0;
                        $cabin_class = 'Economy'; // fix me
                        $total_duration = ''; // need to do for API people
                        $stop_over ='';
                        $details[$k][] = $this->format_summary_array($journey_number, $origin_code, $destination_code, $departure_dt, $arrival_dt, $operator_code, $operator_name, $flight_number, $no_of_stops, $cabin_class, '', '', $total_duration, $is_leg, $attr, $stop_over);
                        if(in_array($v ['SegmentSellKey'],$SegmentSellKey) == false) {
                            $SegmentSellKey [] = $v ['SegmentSellKey'];
                        }
                        $flightNumberList [] = $l_v['FlightDesignator']['a:FlightNumber'];
                        $is_leg = false;
                    // }
                }
                $segment_intl [] = $v ['International'];
                foreach ( $fares_data as $f_k => $f_v ) {
                    $fare[$f_k][$f_v ['FareSellKey']] = $f_v;
                    $FareSellKey[$f_k][] = $f_v ['FareSellKey'];
                }
               $itineray_price['Fare'] = $fares_data; 
            }
            
        }
        if(valid_array($flightNumberList)){
        $key ['key'] [$journey_number] ['SegmentSellKey'] = $SegmentSellKey;
        $key ['key'] [$journey_number] ['FlightNumber'] = $flightNumberList;
        $key ['key'] [$journey_number] ['is_intl'] = $segment_intl;
        $key1 ['key'] [0] ['SegmentSellKey'] = $SegmentSellKey;
        $key1 ['key'] [0] ['FlightNumber'] = $flightNumberList;
        $key1 ['key'] [0] ['is_intl'] = $segment_intl;
        $fares_count = count($fares_data);
        
        
        $flight_ar = array();
        for($i=0; $i<$fares_count; $i++){
            $product_class = $itineray_price['Fare'][$i]['ProductClass'];
            $fare_sell_key = $itineray_price['Fare'][$i]['FareSellKey'];
            // $price = $this->format_itineray_price_details($itineray_price['Fare'][$i], $search_data);
            $price = array();
            $flight_ar ['FlightDetails']['Details'] = $details;
            $flight_ar ['Price'] = $price;
            $key1['key'][0]['flight_search_results'][0][0] = $flight_ar; 
            $AirlineRemark = 'GOAIR Booking';
            $IsLCC = '';
            $IsRefundable = false; // need to check
            $ResultIndex ='';
            $flight_ar ['raw'] ['fare'] = $fare[$i];
            $key ['key'] [$journey_number]['ResultIndex'] = $ResultIndex;
            $key ['key'] [$journey_number]['IsLCC'] = $IsLCC;
            $key ['key'] [$journey_number] ['FareSellKey'] = $FareSellKey[$i];
            $key1 ['key'][0]['ResultIndex'] = $ResultIndex; 
            $key1 ['key'] [0]['IsLCC'] = $IsLCC;
            $key1 ['key'] [0] ['FareSellKey'] = $FareSellKey[$i];
            // debug($key);exit;
            $flight_ar ['key'] = $key ['key'];
            $flight_ar1 ['key'] = $key1 ['key'];
            $flight_ar ['ResultToken'] = $flight_ar1['key'];
            $is_refundable = $IsRefundable;
            $flight_ar ['Attr']['IsRefundable'] = $is_refundable;
            $flight_ar ['Attr']['AirlineRemark'] = $AirlineRemark;
            $flight_ar ['Attr']['ProductClass'] = $product_class;
            $flight_ar ['Attr']['FareSellKey'] = $fare_sell_key;
            $final_flight[$i] = $flight_ar;
        }
        $response = $final_flight;
        // debug($response);exit;
        return $response;
 
        }
        
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
                   $response['data'] = $price_itinerary_response; 
                }
            }
        }
        // debug($responsep);exit;
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
                                <a:PaxCount>' . $search_data ['adult_config'] . '</a:PaxCount>
                            </PaxPriceType>', $search_data ['adult_config'] );
        }
        
        if ($search_data ['child_config'] > 0) {
            $pax_config .= str_repeat ( '<PaxPriceType>
                                <PaxType>CHD</PaxType>
                                <PaxDiscountCode i:nil="true"></PaxDiscountCode>
                                <a:PaxCount>' . $search_data ['child_config'] . '</a:PaxCount>
                            </PaxPriceType>', $search_data ['child_config'] );
        }
        $pax_config .= '</a:PaxPriceType>';
        
        $journey_sell_keys = '<a:JourneySellKeys>';
       
        foreach ( $key_details as $k => $v ) {
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
     * Formates Itineray Price Details
     * @param unknown_type $itineray_price
     */
    private function format_itineray_price_details($itineray_price, $search_data, $ticket_details_fare = false) {
        
        if ($ticket_details_fare) {
            $itineray_price['FareBreakdown'] = $this->format_ticket_detail_fare_breakdown($itineray_price['FareBreakdown']);
        }
        
        $price = array();
        $passenger_breakup = array();
        
        $total_base_fare = 0;
        $total_tax = 0;
        $pax_fare_list = $itineray_price['PaxFares']['PaxFare'];
        $pax_fare_list = force_multple_data_format($pax_fare_list);
        $pax_tax = 0;
        $pax_tax = 0;
        $currency_code ='';
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
                if($fare['ChargeType'] == 'Tax'){
                    $total_tax += $pax_tax = $pax_count*$fare['Amount'];
                }
                $pax_total_fare = $pax_base_fare + $pax_tax;

                $passenger_breakup[$pax_type]['BasePrice'] = $pax_base_fare;
                $passenger_breakup[$pax_type]['Tax'] = $pax_tax;
                $passenger_breakup[$pax_type]['TotalPrice'] = $pax_total_fare;
                $passenger_breakup[$pax_type]['PassengerCount'] = $pax_count;   
            }

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
        // debug($price);exit;
        return $price;
    }
      /**
     * Formates Itineray Price Details
     * @param unknown_type $itineray_price
     */
    private function format_itineray_price_details_new($itineray_price, $search_data, $ticket_details_fare = false) {
        
        if ($ticket_details_fare) {
            $itineray_price['FareBreakdown'] = $this->format_ticket_detail_fare_breakdown($itineray_price['FareBreakdown']);
        }
        
        $price = array();
        $passenger_breakup = array();
        
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
        // debug($price);exit;
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
                '<p>1. All Guests, including children and infants, must present valid identification at check-in. It is your responsibility to ensure you have the appropriate travel documents at all times.<br />
                2. Rescheduling - 2225 INR per pax per segment plus difference of fare and applicable taxes and surcharge.<br />
                3. Check-in starts &quot;2 hours&quot; before scheduled departure, and closes 45 minutes prior to the scheduled departure time. Guests are requested to report at least 2 hours prior to departure time, at Go Air Check-in counters.<br />
                4. Cancellation / Changes within &quot;2 hours&quot; of departure or failure to check-in for a Go Air flight at least 45 minutes prior to the scheduled departure time will result in the fare being forfeited. We require the request atleast 4 hours prior to departure, After that ticket will be count as a No show.</p>

                <p>&nbsp;&nbsp;&nbsp; 0-2 hours before scheduled departure .Only Governement /airport taxes and fees will be refunded.<br />
                &nbsp;&nbsp;&nbsp; 2 hours before scheduled departure. 2225 INR per passenger per segment.</p>

                <p>&nbsp;</p>';
            $fare_rule_response[0]['Origin'] = $origin;
            $fare_rule_response[0]['Destination'] = $destination;
            $fare_rule_response[0]['Airline'] = 'G8';
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
    public function get_update_fare_quote($request, $search_id) {
        // debug($request);exit;
        $CI = & get_instance();
        $jouney_data['JourneySellKey'] = $request['JourneySellKey'];
        $jouney_data['FareSellKey'] = $request['FareSellKey'];
        $seg_count = count($request['flight_search_results'][0][0]['FlightDetails']['Details'][0]);
        $flight_number = $request['FlightNumber'][0];
        $origin = $request['flight_search_results'][0][0]['FlightDetails']['Details'][0][0]['Origin']['AirportCode'];
        $destination = $request['flight_search_results'][0][0]['FlightDetails']['Details'][0][$seg_count-1]['Destination']['AirportCode'];
        $id = $search_id. "_". $origin. "_" .$destination;
        $check_price_keys = $this->CI->custom_db->single_table_records('goair_pricekeys', '*', array('search_id' => $id));
        if($check_price_keys['status'] == 0){
            $insert_data['search_id'] = $id;
            $insert_data['data'] = base64_encode(json_encode($jouney_data));
            $this->CI->custom_db->insert_record('goair_pricekeys', $insert_data);
        }
        else{
            $update['data'] = base64_encode(json_encode($jouney_data));
            $condition['search_id'] = $id;
            $this->CI->custom_db->update_record('goair_pricekeys',$update,$condition);
        }
        if (valid_array($request) == true ) {
            if(isset($request['flight_search_results'])){
                $response ['status'] = SUCCESS_STATUS;
                $response ['data']['FareQuoteDetails']['JourneyList'] = $request['flight_search_results'];
                $response ['data']['FareQuoteDetails']['JourneyList'][0][0]['JourneySellKey'] = $request['JourneySellKey'];
                $response ['data']['FareQuoteDetails']['JourneyList'][0][0]['FareSellKey'] = $request['FareSellKey'];
            }
            else {
                $response ['message'] = 'Not Available';
            }
        }
        else{
            $response ['status'] = FAILURE_STATUS;
        }
        
  
    return $response; 
    }
     /**
     * Process booking
     * @param array $booking_params
     */
    function process_booking($booking_params, $app_reference, $sequence_number, $search_id) {
        // echo 'ssss';exit;
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned

        $ticket_response = array();
        $book_response = array();

        $ResultToken = $booking_params['ResultToken'];
        $ticket_service_response['status']= FAILURE_STATUS;
        $sell_response = $this->get_sell_request($booking_params, $search_id);
        // debug($sell_response);exit;
        if($sell_response['status'] == SUCCESS_STATUS){
            $update_pass_response = $this->get_update_passenger_request($booking_params);
            if($update_pass_response['status'] == SUCCESS_STATUS){
                $add_payment_booking_res = $this->get_add_payemt_booking_request($update_pass_response['data']['api_total_fare']);
                if($add_payment_booking_res['status'] == SUCCESS_STATUS){
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

                    } else {
                        $response ['message'] = $book_service_response['message'];
                        $flight_booking_status = 'BOOKING_FAILED';
                        $this->CI->common_flight->update_flight_booking_status($flight_booking_status, $app_reference, $sequence_number, $this->booking_source);
                    }
                   $this->logout(); 
                }
            }

        }
        return $response;
       
    }
    function get_sell_request($booking_params, $search_id){
        $check_price_keys = $this->CI->flight_model->get_goair_pice_keys($search_id);
        // if(valid_array($check_price_keys) &&  empty($check_price_keys) == false){
        //     $price_key_count = count($check_price_keys);
        //     if($price_key_count > 1){
        //         foreach($check_price_keys as $price_key){
        //             $price_key = json_decode(base64_decode($price_key['data']), true);
        //             // $sell_keys['JourneySellKey'][] = $price_key['JourneySellKey'];
        //             foreach($price_key['JourneySellKey'] as $j_k => $journey_key){
        //                 $sell_keys['JourneySellKey'][] = $journey_key;
        //                 $sell_keys['FareSellKey'][] = $price_key['FareSellKey'][$j_k];
        //             }
        //         }
        //     $booking_params['flight_data']['JourneySellKey'] = $sell_keys['JourneySellKey'];
        //     $booking_params['flight_data']['FareSellKey'] = $sell_keys['FareSellKey'];
        //     }
        // }
        // debug($booking_params);exit;
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        $sell_request = $this->sell_request($booking_params);
        if ($sell_request['status'] = SUCCESS_STATUS) {
            $sell_response = $this->process_request($sell_request ['request'], $sell_request ['url'], $sell_request ['SOAPAction'], $sell_request ['remarks']);
            // $sell_response = file_get_contents(FCPATH."travelport_xmls/goair_sell_res.xml");
            $sell_response = Converter::createArray ( $sell_response );
            // debug($sell_response);exit;
            if ($this->valid_sell_response($sell_response) == TRUE) {
                $response ['status'] = SUCCESS_STATUS;
                $TotalCost = $sell_response ['s:Envelope'] ['s:Body'] ['SellResponse'] ['BookingUpdateResponseData'] ['Success'] ['PNRAmount'] ['TotalCost'];
                $api_total_fare = $TotalCost; // need to check the price with price itinerary request
               
            }
            
        }

        return $response;
         // debug($booking_params);exit;

    }
     /**
     * Sell RQ for api
     */
    private function sell_request($booking_params) {
        $journey_sell_keys = $booking_params['flight_data']['JourneySellKey'];
        $passengers = $booking_params['Passengers'];
        $fare_sell_keys = $booking_params['flight_data']['FareSellKey'];
        $JourneySellKeys ='';
        for($i=0; $i<count($journey_sell_keys); $i++){
            $JourneySellKeys .= '<a:SellKeyList>
                      <a:JourneySellKey>' . $journey_sell_keys [$i] . '</a:JourneySellKey>
                     <a:FareSellKey>' . $fare_sell_keys[$i] . '</a:FareSellKey>
                      <a:StandbyPriorityCode i:nil="true"/>
                      </a:SellKeyList>';
        }
        $pax_cnt = 0;
        $PaxPriceType = '';
        foreach($passengers as $pass){
            if($pass['PaxType'] == 3){
                $px_type = 'INF';
                continue;
            }
            else if($pass['PaxType'] == 2){
                $px_type = 'CHD';
            }
            else{
                $px_type = 'ADT';
            }
            $pax_cnt ++;
            $PaxPriceType .= '<a:PaxPriceType>
                                    <a:PaxType>' . $px_type . '</a:PaxType>
                                    <a:PaxDiscountCode i:nil="true"/>
                                </a:PaxPriceType>';
        }
        
        
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
                                        <b:AgentCode>' . $this->config ['agent_id'] . '</b:AgentCode>
                                        <b:OrganizationCode>' . $this->config ['organization_code'] . '</b:OrganizationCode>
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
    function get_update_passenger_request($booking_params){
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        $update_pass_req = $this->update_passenger_request($booking_params);

        if ($update_pass_req['status'] = SUCCESS_STATUS) {
            $update_pass_response = $this->process_request($update_pass_req ['request'], $update_pass_req ['url'], $update_pass_req ['SOAPAction'], $update_pass_req ['remarks']);
            // $update_pass_response = file_get_contents(FCPATH."travelport_xmls/goair_update_pass_res.xml");
            $update_pass_response = Converter::createArray ( $update_pass_response );
            // debug($sell_response);exit;
            if ($this->valid_update_passenger_response($update_pass_response) == TRUE) {
                $response ['status'] = SUCCESS_STATUS;
                $TotalCost = $update_pass_response ['s:Envelope'] ['s:Body'] ['UpdatePassengerResponse'] ['BookingUpdateResponseData'] ['Success'] ['PNRAmount'] ['TotalCost'];
                $response['data']['api_total_fare'] = $TotalCost; // need to check the price with price itinerary request
              
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
            // debug($pass);exit;
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
                                    <a:Title>' . $pass ['infant']['Title'] . '</a:Title>
                                </a:BookingName>
                            </a:Names>
                        </a:Infant>';
                } else {
                    $infant = '<a:Infant i:nil="true"/>';
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
                                        <a:Title>' . $pass ['Title'] . '</a:Title>
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
    function get_add_payemt_booking_request($total_cost){

        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        $add_payment_booking_request = $this->add_payment_booking_request($total_cost);
        if ($add_payment_booking_request['status'] = SUCCESS_STATUS) {
            $add_payment_booking_response = $this->process_request($add_payment_booking_request ['request'], $add_payment_booking_request ['url'], $add_payment_booking_request ['SOAPAction'], $add_payment_booking_request ['remarks']);
            // $update_pass_response = file_get_contents(FCPATH."travelport_xmls/goair_update_pass_res.xml");
            $add_payment_booking_response = Converter::createArray ( $add_payment_booking_response );
            // debug($sell_response);exit;
            if (isset ( $add_payment_booking_response ) && valid_array ( $add_payment_booking_response ) && isset ( $add_payment_booking_response ['s:Envelope'] ['s:Body'] ['AddPaymentToBookingResponse'] ['BookingPaymentResponse'] )) {
                $response ['status'] = SUCCESS_STATUS;
                $response ['data'] = $add_payment_booking_response;
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
                        <AccountNumber>' . $this->config ['organization_code'] . '</AccountNumber>
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
                                <b:AgentCode>' . $this->config ['agent_id'] . '</b:AgentCode>
                                <b:OrganizationCode>' . $this->config ['organization_code'] . '</b:OrganizationCode>
                                <b:DomainCode>' . $this->config ['agent_domain'] . '</b:DomainCode>
                                <b:LocationCode>www</b:LocationCode>
                            </SourcePOS>
                            <BookingHold i:nil="true"/>
                            <ReceivedBy i:nil="true"/>
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
                                    <CompanyName>Accentria</CompanyName>
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
                                    <SourceOrganization>' . $this->config ['organization_code'] . '</SourceOrganization>
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
            $get_book_details_response = Converter::createArray ( $get_book_details_response );
            if (valid_array($get_book_details_response) == true && isset($get_book_details_response['s:Envelope']['s:Body']['GetBookingResponse']['Booking']['BookingInfo']) == true && $get_book_details_response['s:Envelope']['s:Body']['GetBookingResponse']['Booking']['BookingInfo']['BookingStatus'] == 'Confirmed') {
                $response ['status'] = SUCCESS_STATUS;
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
        // debug($logout_response);exit;
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
    private function save_book_response_details($book_response, $app_reference, $sequence_number) {
        // debug($book_response);exit;
        $update_data = array();
        $update_condition = array();
        $pnr = $book_response['s:Envelope']['s:Body']['BookingCommitResponse']['BookingUpdateResponseData']['Success']['RecordLocator'];
        $update_data['pnr'] = $pnr;
        $update_data['book_id'] = $pnr;

        $update_condition['app_reference'] = $app_reference;
        $update_condition['sequence_number'] = $sequence_number;

        $this->CI->custom_db->update_record('flight_booking_transaction_details', $update_data, $update_condition);

        $flight_booking_status = 'BOOKING_CONFIRMED';
        $this->CI->common_flight->update_flight_booking_status($flight_booking_status, $app_reference, $sequence_number, $this->booking_source);
    }
    /* save flight ticket details */
    private function save_flight_ticket_details($booking_params, $book_response, $app_reference, $sequence_number, $search_id){
        // debug($booking_params);exit;
        $flight_booking_transaction_details_fk = $this->CI->custom_db->single_table_records('flight_booking_transaction_details', 'origin', array('app_reference' => $app_reference, 'sequence_number' => $sequence_number));
        $flight_booking_transaction_details_fk = $flight_booking_transaction_details_fk['data'][0]['origin'];
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
        //$this->CI->common_flight->update_flight_booking_tranaction_price_details($app_reference, $sequence_number, $fare_details['commissionable_fare'], $fare_details['admin_commission'], $fare_details['agent_commission'], $fare_details['admin_tds'], $fare_details['agent_tds'], $fare_details['admin_markup'], $fare_breakup);
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
    private function valid_search_result($search_result) {
            // debug($search_result);exit;
        if (valid_array($search_result) == true && isset($search_result['s:Envelope']['s:Body']['GetAvailabilityByTripResponse']) == true && isset($search_result['s:Envelope']['s:Body']['GetAvailabilityByTripResponse']['GetTripAvailabilityResponse']['Schedules']) == true && valid_array($search_result['s:Envelope']['s:Body']['GetAvailabilityByTripResponse']['GetTripAvailabilityResponse']['Schedules']['ArrayOfJourneyDateMarket']) == true) {
            return true;
        } else {
            return false;
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
    /**
     * Authentication Request
    */
    public function get_authentication_request($internal_request = false) {

        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned

        $active_booking_source = $this->is_active_booking_source();
        // debug($active_booking_source);exit;
        if ($active_booking_source['status'] == SUCCESS_STATUS) {
            $authenticate_request = $this->authenticate_request();
           
            if ($authenticate_request['status'] = SUCCESS_STATUS) {
                $response ['status'] = SUCCESS_STATUS;
                $curl_request = $this->form_curl_params($authenticate_request['request'], $authenticate_request['url'], $authenticate_request['SOAPAction']);
                $response ['data'] = $curl_request['data'];
            }
            if ($internal_request == true) {
                $response ['data']['remarks'] = 'Authentication(GoAir)';
            }
        }
        // debug($response);exit;
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
        $insert_id = $this->CI->api_model->store_api_request($url, $request, $remarks);
        $insert_id = intval(@$insert_id['insert_id']);

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