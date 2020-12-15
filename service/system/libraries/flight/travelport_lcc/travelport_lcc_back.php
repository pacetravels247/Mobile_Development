<?php
require_once BASEPATH . 'libraries/flight/Common_api_flight.php';

class Travelport_lcc extends Common_Api_Flight {
    var $master_search_data;
    var $retunr_domestic;
    var $search_hash;
    protected $token;
    function __construct($config_table_origin) {
    // error_reporting(E_ALL);
    // ini_set('display_errors', 1);

        parent::__construct(META_AIRLINE_COURSE, TRAVELPORT_LCC_FLIGHT_BOOKING_SOURCE,$config_table_origin);
        $this->CI = &get_instance();
        $this->CI->load->library('Converter');
        $this->CI->load->library('ArrayToXML');
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
     * flight search request
     * 
     * @param $search_id unique
     *          id which identifies search details
     */
    function get_flight_list($flight_raw_data, $search_id) {
      
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned

        $search_data = $this->search_data($search_id);

        if ($search_data ['status'] == SUCCESS_STATUS) {
            
            foreach($flight_raw_data as $key => $raw_data){
                $clean_format_data = array();
                $formatted_seach_result = array();
                foreach ($raw_data as $k_trip => $v_trip) {
                    $search_response_array = array();
                    $search_response = utf8_encode($v_trip);
                    $search_response_array[0] = Converter::createArray($search_response);
                    if ($this->valid_search_result($search_response_array) == TRUE) {
                        $clean_format_data = $this->format_search_data_response($search_response_array, $search_data ['data']);
                        
                        if (valid_array($formatted_seach_result) == false) { //Assiging the flight data, if not set(only for first API data, for next API's, it will be merged)     
                            $formatted_seach_result = $clean_format_data['FlightDataList']['JourneyList'][0];
                        } else {
                            $clean_format_data = $clean_format_data['FlightDataList']['JourneyList'][0];
                            $formatted_seach_result = array_merge($formatted_seach_result, $clean_format_data);
                        }
                        if (valid_array($formatted_seach_result)) {
                            $response ['status'] = SUCCESS_STATUS;
                        } else {
                            $response ['status'] = FAILURE_STATUS;
                        }
                    }
                }
               $response ['data']['FlightDataList']['JourneyList'][$key] = $formatted_seach_result;
            }
        } else {
            $response ['status'] = FAILURE_STATUS;
        }
        // debug($response);exit;
        return $response;
    }

    /**
     * Fare Rule
     * @param unknown_type $request
     */
    public function get_fare_rules($flight_result) {

        //$this->air_pricing_request($flight_result);
        $arrSegmentData = $flight_result['flight_list'][0]['flight_detail'];
        $provide_code = $arrSegmentData[0]['provider_code'];
      
        $fare_rule_request = $this->fare_rule_request($arrSegmentData);
       
        if ($fare_rule_request['status'] == SUCCESS_STATUS) {
            $fare_rule_response = $this->process_request($fare_rule_request['request'],'','flight_rules(Travelport Flight)');
            $arrfare_result = Converter::createArray($fare_rule_response);
            
            if (valid_array($arrfare_result) == true) {
                $arrFareRule1 = $arrfare_result['SOAP:Envelope']['SOAP:Body']['air:AirFareRulesRsp']['air:FareRule'];
              
                if (!isset($arrFareRule1[0])) {
                    $arrFareRule[0] = $arrFareRule1;
                } else {
                    $arrFareRule = $arrFareRule1;
                }
                
                $arrFareData = array();
                $FareRules ='TP';
                for ($fr = 0; $fr < count($arrFareRule); $fr++) {
                    if($provide_code == '1G'){
                        $freRule = $arrFareRule[$fr]['air:FareRuleLong'];
                    }
                    else{
                        $freRule1 = $arrFareRule[$fr]['air:FareRuleLong'];
                        if (!isset($freRule[0])) {
                            $freRule[0] = $freRule1;
                        } else {
                            $freRule = $freRule1;
                        } 
                    }
                    $fareResp1[$fr]['Origin'] = $arrSegmentData[$fr]['origin'];
                    $fareResp1[$fr]['Destination'] = $arrSegmentData[$fr]['destination'];
                    $fareResp1[$fr]['Airline'] = $arrSegmentData[$fr]['carrier'];
                  
                    for ($ff = 0; $ff < count($freRule); $ff++) {

                        $arrFrRl = $freRule[$ff];

                        $FareRules .= $arrFrRl['@value'] . "<br/>";
                        $strFareCategory = $arrFrRl['@attributes']['Category'];
                        $fareResp[$ff]['fare_category'] = $arrFrRl['@attributes']['Category'];
                        $fareResp[$ff]['fare_type'] = $arrFrRl['@attributes']['Type'];
                        $arrFareData[$fr][$ff] = $fareResp[$ff];
                    }
                    $response ['status'] = SUCCESS_STATUS;
                    $fareResp1[$fr]['FareRules'] = $FareRules;
                    $response ['data']['FareRuleDetail'] = $this->format_fare_rule_response($fareResp1);
                }
            } else {
                $response ['message'] = 'Not Available';
            }
        } else {
            $response ['status'] = FAILURE_STATUS;
        }
       // debug($response);exit;
        return $response;
    }
    
    
function format_fare_rule_response($fare_rule_response) {
        $fare_rules = array();
        foreach ($fare_rule_response as $k => $v) {
            $fare_rules[$k]['Origin'] = $v['Origin'];
            $fare_rules[$k]['Destination'] = $v['Destination'];
            $fare_rules[$k]['Airline'] = $v['Airline'];
            $domain_base_currency = domain_base_currency();
            $domain_base_currency="USD";

            $currency_obj = new Currency(array('from' => get_application_default_currency(), 'to' => $domain_base_currency));

            $FareRulesArray = explode(" ", $v['FareRules']);

            foreach ($FareRulesArray as $key => $value) {
 
            if (trim($FareRulesArray[$key])=="INR" || trim($FareRulesArray[$key])=="Rs" || trim($FareRulesArray[$key])=="-INR") {
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
            $fare_rules[$k]['FareRules'] = implode(" ",$FareRulesArray);
        }
        return $fare_rules;
    }

    private function fare_rule_request($arrSegmentData) {
        
        if($arrSegmentData[0]['ProviderCode'] == '1G'){
            $strRequest = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
                <soapenv:Header/>
                <soapenv:Body>
                    <ns2:AirFareRulesReq xmlns="http://www.travelport.com/schema/common_v41_0" xmlns:ns2="http://www.travelport.com/schema/air_v41_0" FareRuleType="long" TraceId="394d96c00971c4545315e49584609ff6" TargetBranch="' . $this->config ['Target'] . '" AuthorizedBy="' . $this->config ['UserName'] . '">
                      <BillingPointOfSaleInfo OriginApplication="uAPI" />';
            
            for ($s = 0; $s < count($arrSegmentData); $s++) {
                if (isset($arrSegmentData[$s]['ProviderCode'])) {
                    $strProviderCode = $arrSegmentData[$s]['ProviderCode'];
                    $strFareInfoRef = $arrSegmentData[$s]['fare_info_ref'];
                    $strFarerulesref = $arrSegmentData[$s]['fare_info_value'];
                    $strRequest .= '<ns2:FareRuleKey ProviderCode="' . $strProviderCode . '" FareInfoRef="' . $strFareInfoRef . '">' . $strFarerulesref . '</ns2:FareRuleKey>';
                }
                }
            $strRequest .= '</ns2:AirFareRulesReq>
                        </soapenv:Body>
                    </soapenv:Envelope>';
        }
        else{
            $strRequest = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
                <soapenv:Header/>
                <soapenv:Body>
                    <air:AirFareRulesReq xmlns="http://www.travelport.com/schema/common_v41_0" xmlns:air="http://www.travelport.com/schema/air_v41_0" FareRuleType="long" TraceId="394d96c00971c4545315e49584609ff6" TargetBranch="' . $this->config ['Target'] . '" AuthorizedBy="' . $this->config ['UserName'] . '">
                      <BillingPointOfSaleInfo OriginApplication="uAPI" />';
                if (isset($arrSegmentData[0]['ProviderCode'])) {
                        $strProviderCode = $arrSegmentData[0]['ProviderCode'];
                        $strFareInfoRef = $arrSegmentData[0]['fare_info_ref'];
                        $strFarerulesref = $arrSegmentData[0]['fare_info_value'];
                        $strRequest .= '<air:FareRuleKey ProviderCode="' . $strProviderCode . '" FareInfoRef="' . $strFareInfoRef . '">' . $strFarerulesref . '</air:FareRuleKey>
                                        <air:AirFareRulesModifier>
                                            <air:AirFareRuleCategory FareInfoRef="'.$strFareInfoRef.'">
                                                <air:CategoryCode>CHG</air:CategoryCode>
                                            </air:AirFareRuleCategory>
                                        </air:AirFareRulesModifier>';             
                    }
                $strRequest .= '</air:AirFareRulesReq>
                        </soapenv:Body>
                    </soapenv:Envelope>';
        }
        $request ['request'] = $strRequest;
        $request ['status'] = SUCCESS_STATUS;
        return $request;
    }

    /**
     * Format Search data response
     */
    private function format_search_data_response($low_fare_search_res_data, $search_data) {
        $airport_list = array();
        $airline_list = array();
        $hand_baggage_list = array();
        $flight_data_list = array();
        $fare_type_list = check_active_fare_type($this->booking_source,get_domain_auth_id());

        foreach ($low_fare_search_res_data as $key => $low_fare_search_res) {
            $low_fare_search_res = $low_fare_search_res ['SOAP:Envelope'] ['SOAP:Body'];
            if(!empty($low_fare_search_res['air:LowFareSearchRsp']['air:FlightDetailsList']['air:FlightDetails'])){
                $currency_type = $low_fare_search_res ['air:LowFareSearchRsp'] ['@attributes'] ['CurrencyType'];
                // air:AirPricingSolution TO FIX - create function for airpricing solution
                $air_flight_list = $low_fare_search_res ['air:LowFareSearchRsp'] ['air:FlightDetailsList'];
                $air_pricing_solutions = $low_fare_search_res ['air:LowFareSearchRsp'] ['air:AirPricingSolution'];
                $air_segment_list = @$low_fare_search_res ['air:LowFareSearchRsp'] ['air:AirSegmentList'] ['air:AirSegment'];
                $air_fare_info_list = @$low_fare_search_res ['air:LowFareSearchRsp'] ['air:FareInfoList'] ['air:FareInfo'];
                $air_host_token_list = @$low_fare_search_res ['air:LowFareSearchRsp'] ['air:HostTokenList'];
               
                $arrBaggageData = array();
                $arrFareruleData = array();
                $arrHostTokenData = array();

                for ($b = 0; $b < count($air_fare_info_list); $b++) {
                    $fare_info_key_array[$air_fare_info_list[$b]['@attributes']['Key']] = $air_fare_info_list[$b]['@attributes']['FareFamily'];
                    $baggage_value ='';
                    $baggage_unit = '';
                    if (isset($air_fare_info_list[$b]['air:BaggageAllowance'])) {
                        if(isset($air_fare_info_list[$b]['air:BaggageAllowance']['air:MaxWeight']['@attributes']['Value'])){
                            $baggage_value =  $air_fare_info_list[$b]['air:BaggageAllowance']['air:MaxWeight']['@attributes']['Value'];
                            $baggage_unit =  $air_fare_info_list[$b]['air:BaggageAllowance']['air:MaxWeight']['@attributes']['Unit'];
                        }
                        else if(isset($air_fare_info_list[$b]['air:BaggageAllowance']['air:NumberOfPieces'])){
                            $baggage_value = $air_fare_info_list[$b]['air:BaggageAllowance']['air:NumberOfPieces'];
                            if($baggage_value > 1){
                                $baggage_unit = 'Pieces';
                            }
                            else{
                                $baggage_unit = 'Piece';
                            }
                        }
                 
                        $strFRefKey = $air_fare_info_list[$b]['@attributes']['Key'];
                        $strPlaces  = $air_fare_info_list[$b]['@attributes']['Origin'] . "_" . $air_fare_info_list[$b]['@attributes']['Destination'];
                        $strPasCode = $air_fare_info_list[$b]['@attributes']['PassengerTypeCode'];
                        $arrBag_Data['value'] = $baggage_value;
                        $arrBag_Data1['Baggage']      = $baggage_value . ' ' . $baggage_unit;
                        $arrBag_Data1['CabinBaggage'] = 0;
                        $arrBag_Data1['AvailableSeats'] = 0;
                        $arrBag_Data['unit'] = $air_fare_info_list[$b]['air:BaggageAllowance']['air:MaxWeight']['@attributes']['Unit'];
                        $arrBag_Data['key'] = $air_fare_info_list[$b]['@attributes']['Key'];
                        $arrBag_Data['dep_date'] = $air_fare_info_list[$b]['@attributes']['DepartureDate'];
                        $arrBag_Data['pas_cod'] = $strPasCode;
                        $arrBag_Data['origin'] = $air_fare_info_list[$b]['@attributes']['Origin'];
                        $arrBag_Data['destination'] = $air_fare_info_list[$b]['@attributes']['Destination'];
                        $arrBaggageData[$strFRefKey] = $arrBag_Data1;
                        $arrBaggageData[$strFRefKey][$strPlaces]['departure_date'] = $air_fare_info_list[$b]['@attributes']['DepartureDate'];
                    }
                    if (isset($air_fare_info_list[$b]['air:FareRuleKey']['@attributes'])) {
                        $arrfare_Data['FareInfoRef'] = $FareInfoRef = $air_fare_info_list[$b]['air:FareRuleKey']['@attributes']['FareInfoRef'];
                        $arrfare_Data['FareInfoValue'] = $air_fare_info_list[$b]['air:FareRuleKey']['@value'];
                        $arrfare_Data['ProviderCode'] = $air_fare_info_list[$b]['air:FareRuleKey']['@attributes']['ProviderCode'];
                        $arrfare_Data['FareBasis'] = $air_fare_info_list[$b]['@attributes']['FareBasis'];
                        $arrFareruleData[$FareInfoRef] = $arrfare_Data;
                    }
                }

                if((isset($air_host_token_list['common_v41_0:HostToken'][0]['@attributes'])) && valid_array($air_host_token_list)){
                    foreach($air_host_token_list['common_v41_0:HostToken'] as $host_key => $host_value){
                        $arrHostToken['HostTokenKey'] = $HostTokenKey = $host_value['@attributes']['Key'];
                        $arrHostToken['HostTokenValue'] =  $host_value['@value'];
                        $arrHostTokenData[$HostTokenKey] = $arrHostToken;
                    }
                }
                $air_pricing_solutions_data = force_multple_data_format(@$air_pricing_solutions);
                
                if (isset($air_pricing_solutions_data) && valid_array($air_pricing_solutions_data)) {
                    foreach ($air_pricing_solutions_data as $ap_key => $pricing_array) {
                        $AirPricingInfo = force_multple_data_format($pricing_array['air:AirPricingInfo']);
                        $flight_data_list [] = $this->format_air_pricing_solution($pricing_array, $fare_info_key_array, $air_segment_list, $arrBaggageData, $arrFareruleData, $search_data, $arrHostTokenData,$airport_list,$airline_list,$hand_baggage_list);
                       
                    } 
                }
                $response ['FlightDataList'] ['JourneyList'][$key] = $flight_data_list;
           
                unset($flight_data_list);
            }
            else{
                
                $response['status'] = FALSE;
            }
        }
        return $response;
    }
    /**
     * array $pricing_array
     */
    function format_air_pricing_solution($pricing_array, $fare_info_key_array, $air_segment_list1, $arrBaggageData, $arrFareruleData, $search_data, $arrHostTokenData,&$airport_list,&$airline_list,&$hand_baggage_list) {
        $CI = & get_instance();
        $air_segment_list = force_multple_data_format(@$air_segment_list1);
       
        $agent_commssion = 0;
        $yq_value = 0;
        $flight_journey = array();
      
       
        $journey_detail = force_multple_data_format(@$pricing_array ['air:Journey']);
        $air_pricing_info = force_multple_data_format(@$pricing_array ['air:AirPricingInfo']);

        $connection = '';
        if(isset($pricing_array ['air:Connection'])){
            $connection_arr = force_multple_data_format(@$pricing_array ['air:Connection']);
            if (isset($connection_arr) && valid_array($connection_arr)) {
                foreach ($connection_arr as $c_key => $connect_flight) {
                    if (isset($connect_flight ['@attributes'] ['SegmentIndex'])) {
                        $connection .= @$connect_flight ['@attributes'] ['SegmentIndex'] . ",";
                    } else {
                        $connection .= @$connect_flight ['SegmentIndex'] . ",";
                    }
                }
            }
        }

        $flight_key_list = array();
        $flight_detail_arr = array();
        /* air journey flight key for onward and return gropu */

        if (isset($journey_detail) && valid_array($journey_detail)) {
            $flight_detail_key = 0;
            foreach ($journey_detail as $j_key => $journey_flight) {

                if ($j_key == 0) {
                    $trip_type = 'onward';
                } else {
                    $trip_type = 'return';
                }
                //debug($journey_flight); exit;
                $air_segment_ref = force_multple_data_format(@$journey_flight ['air:AirSegmentRef']);

                if (isset($air_segment_ref) && valid_array($air_segment_ref)) {
                
                    foreach ($air_segment_ref as $seg_key => $segmnt) {

                        if (isset($air_segment_list) && !empty($air_segment_list)) {
                           foreach ($air_segment_list as $as_key => $segment) {
                                $air_segment_arr ['flight_detail_ref'] = $flight_detail_key = @$segment ['air:FlightDetailsRef'] ['@attributes'] ['Key'];
                                $air_code_share_info = @$segment ['air:CodeshareInfo'];
                                $is_leg = false;
                                $segment_ref = @$segment ['@attributes'] ['Key'];
                             
                                $flight_key = @$segmnt ['@attributes'] ['Key'];
                                $flight_detail_arr = array();
                                if ($flight_key == $segment_ref) {
                                    if (isset($air_code_share_info) && valid_array($air_code_share_info)) {
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['air_code_share'] = @$air_code_share_info ['@attributes'] ['OperatingCarrier'];
                                    }

                                    // booking_code
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['group']                     = @$segment ['@attributes'] ['Group'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['carrier']                   = @$segment ['@attributes'] ['Carrier'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['flight_number']             = @$segment ['@attributes'] ['FlightNumber'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['origin']                    = @$segment ['@attributes'] ['Origin'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['destination']               = @$segment ['@attributes'] ['Destination'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['departure_time']            = @$segment ['@attributes'] ['DepartureTime'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['arrival_time']              = @$segment ['@attributes'] ['ArrivalTime'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['flight_time']               = @$segment ['@attributes'] ['FlightTime'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['distance']                  = @$segment ['@attributes'] ['Distance'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['e_ticketability']           = @$segment ['@attributes'] ['ETicketability'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['equipment']                 = @$segment ['@attributes'] ['Equipment'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['change_of_plane']           = @$segment ['@attributes'] ['ChangeOfPlane'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['participant_level']         = @$segment ['@attributes'] ['ParticipantLevel'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['link_availability']         = @$segment ['@attributes'] ['LinkAvailability'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['polled_availability_option'] = @$segment ['@attributes'] ['PolledAvailabilityOption'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['optional_services_indicator']= @$segment ['@attributes'] ['OptionalServicesIndicator'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['availability_source']       = @$segment ['@attributes'] ['AvailabilitySource'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['flight_seg_key']            = $flight_key;
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['provider_code']             = @$segment ['air:AirAvailInfo'] ['@attributes'] ['ProviderCode'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['flight_detail_key']         = $flight_detail_key;
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['status']                    = @$segment ['@attributes'] ['Status'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['suppliercode']              = @$segment ['@attributes'] ['SupplierCode'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['apis_req_ref']              = @$segment ['@attributes'] ['APISRequirementsRef'];
                                    $flight_list_on_out ['connection'] = $connection;

                                    $flight_journey ['FlightDetails'] ['Details'][$j_key][] = $this->flight_detail_format($segment, $flight_key, $trip_type, $is_leg,$airport_list,$airline_list);
                                   
                                    $flight_key_list [$j_key . '-' . $seg_key] = $flight_key;
                                  
                                    $flight_detail_key ++;
                                } // end air_segment
                            } // end air_segment_list
                        } // end if air_segment_ref
                    } // end for air_segment_ref
                }
            }
        }

        // lay over time calculation
        foreach ($flight_journey['FlightDetails']['Details'] as $d_key => $d_value) {
            $no_of_stop = count($d_value); 
            if($no_of_stop>=2){
                for($i=0;$i<($no_of_stop-1);$i++){
                    if(isset($d_value[$i]) and isset($d_value[$i+1])){
                        $layover_first_time  = $d_value[$i]['Origin']['date_time'];
                        $layover_second_time = $d_value[$i+1]['Destination']['date_time'];
                        $duration_sec = calculate_duration(@$layover_first_time, @$layover_second_time);
                        $duration = get_duration_label(@$duration_sec);
                        $duration_sec             = $duration_sec/60;
                    }
                    $flight_journey['FlightDetails'][$d_key]['lay_over_time'][] = $duration;
                }
            }
        }

        // Price Information 
        $AirPricingInfo = force_multple_data_format(@$pricing_array['air:AirPricingInfo']);
       
        $provider_code = $air_segment_list[0]['air:AirAvailInfo'] ['@attributes'] ['ProviderCode'];
        
        // Built price array like travelfusion response
        $currency          = substr($pricing_array ['@attributes'] ['ApproximateTotalPrice'], 0, 3);
        $conversion_amount = $GLOBALS ['CI']->domain_management_model->get_currency_conversion_rate($currency);
        // debug($conversion_amount);exit;
        $display_price     = substr(@$pricing_array ['@attributes'] ['ApproximateTotalPrice'], 3);
        $total_tax         = substr(@$pricing_array ['@attributes'] ['ApproximateTaxes'], 3)+substr(@$pricing_array ['@attributes'] ['ApproximateFees'], 3);
        $approx_base_price = substr($pricing_array ['@attributes'] ['ApproximateBasePrice'], 3);

        if (isset($pricing_array['air:AirPricingInfo']) and valid_array($AirPricingInfo)) {

            $Refundable = 'false';
            if(isset($AirPricingInfo[0]['@attributes'])){
                if(isset($AirPricingInfo[0]['@attributes']['Refundable'])){
                    $Refundable = $AirPricingInfo[0]['@attributes']['Refundable'];
                }
            }
            
            $passenger_count = 0;
            $pass_code = '';
            $AirPricingInfos = $AirPricingInfo;
            //debug($pricing_array);exit;
            $tax_details = array();
            foreach ($AirPricingInfos as $key => $AirPricingInfo) {
               
                $BasePrice = 0;
                $Taxes = 0;
                $Fees = 0;
                $Passenger_type = force_multple_data_format($AirPricingInfo['air:PassengerType']);
                if(valid_array($Passenger_type)){
                    if (isset($Passenger_type[0]['@attributes'])) {
                        $pass_code = $Passenger_type[0]['@attributes']['Code'];
                    }else{
                        $pass_code = $Passenger_type[0]['Code'];
                    }
                    $passenger_count = count($Passenger_type);
                }

                $tax_info = $AirPricingInfo['air:TaxInfo'];

                foreach ($tax_info as $tax) {
                    if(isset($tax_details[$tax['@attributes']['Category']])){
                        $tax_details[$tax['@attributes']['Category']] += substr(@$tax['@attributes']['Amount'], 3)*$passenger_count;
                    }else{
                        $tax_details[$tax['@attributes']['Category']] = substr(@$tax['@attributes']['Amount'], 3)*$passenger_count;
                    }
                }

                if (isset($AirPricingInfo['@attributes']['EquivalentBasePrice'])) {
                    $BasePrice = substr($AirPricingInfo['@attributes']['EquivalentBasePrice'], 3);
                } else if (isset($AirPricingInfo['@attributes']['ApproximateBasePrice'])) {
                    $BasePrice = substr($AirPricingInfo['@attributes']['ApproximateBasePrice'], 3);
                } else {
                    $BasePrice = substr($AirPricingInfo['@attributes']['BasePrice'], 3);
                }
            
                if (isset($AirPricingInfo['@attributes']['ApproximateTaxes'])) {
                    $Taxes = substr($AirPricingInfo['@attributes']['ApproximateTaxes'], 3);
                } else {
                    if (isset($AirPricingInfo['@attributes']['Taxes'])) {
                        $Taxes = substr($AirPricingInfo['@attributes']['Taxes'], 3);
                    }
                }
                if (isset($AirPricingInfo['@attributes']['ApproximateFees'])) {
                    $Fees = substr($AirPricingInfo['@attributes']['ApproximateFees'], 3);
                } else {
                    if (isset($AirPricingInfo['@attributes']['Fees'])) {
                        $Fees = substr($AirPricingInfo['@attributes']['Fees'], 3);
                    }
                }
                if($pass_code=='CNN'){
                    $pass_code='CHD';
                }
                if(empty($fare_info_key_array[$AirPricingInfo['air:FareInfoRef']['@attributes']['Key']]) == false){
                    $fare_type = $fare_info_key_array[$AirPricingInfo['air:FareInfoRef']['@attributes']['Key']];
                }
                else{
                   $fare_type = 'Regular Fare'; 
                }

                $flight_journey ['Price']['Fare_Type'] = $fare_type;
                $flight_journey ['Price']['PassengerBreakup'][$pass_code]['PassengerCount'] = $passenger_count;
                $flight_journey ['Price']['PassengerBreakup'][$pass_code]['BasePrice'] = $conversion_amount['conversion_rate']*$BasePrice;
                $flight_journey ['Price']['PassengerBreakup'][$pass_code]['Tax'] = $conversion_amount['conversion_rate']*($Taxes+$Fees);
                $flight_journey ['Price']['PassengerBreakup'][$pass_code]['TotalPrice'] = $conversion_amount['conversion_rate']*($BasePrice + $Taxes+$Fees);
                
            }
         
            $booking_details_flight = force_multple_data_format(@$air_pricing_info[0] ['air:BookingInfo']);
            if (isset($booking_details_flight) && valid_array($booking_details_flight)) {
               
                foreach ($booking_details_flight as $b_fkey => $booking) {

                    $BookingCode = @$booking['@attributes']['BookingCode'];
                    $CabinClass = @$booking['@attributes']['CabinClass'];
               
                    $key_index = array_search($booking ['@attributes'] ['SegmentRef'], $flight_key_list);
                  
                    $explode_key = explode('-', $key_index);
                
                    $strFare_Ref_Key = $booking ['@attributes'] ['FareInfoRef'];
                    //$flight_journey ['FlightDetails'] ['Details'] [$b_fkey]['Attr'] = @$arrBaggageData[$strFare_Ref_Key];
                    $flight_journey ['FlightDetails'] ['Details'] [$explode_key [0]][$explode_key [1]]['CabinClass'] = $booking ['@attributes'] ['BookingCode'];
          
                    $carrier = $flight_journey['FlightDetails']['Details'][0][0]['OperatorCode'];

                    $hand_baggage = $this->get_hand_baggage_info($carrier,$hand_baggage_list);
                    if(isset($flight_journey['FlightDetails']['Details'][0][0]['OperatorCode']) && $flight_journey['FlightDetails']['Details'][0][0]['OperatorCode'] == '6E'){
                        
                        if($search_data['is_domestic'] == 1){
                            $arrBaggageData[$strFare_Ref_Key]['Baggage']= '15 Kg';
                        }
                        else if($search_data['from'] == 'CMB' || $search_data['from'] == 'DXB' || $search_data['from'] == 'MCT' || $search_data['from'] == 'SHJ' || $search_data['from'] == 'DOH' || $search_data['from'] == 'SIN'
                           || $search_data['to'] == 'CMB' || $search_data['to'] == 'DXB' || $search_data['to'] == 'MCT' || $search_data['to'] == 'SHJ' || $search_data['to'] == 'DOH' || $search_data['to'] == 'SIN'){
                            $arrBaggageData[$strFare_Ref_Key]['Baggage']= '30 Kg';
                        }
                        else{
                            $arrBaggageData[$strFare_Ref_Key]['Baggage']= '20 Kg';
                        }
                    }
                    if(!empty($hand_baggage)){
                        $arrBaggageData[$strFare_Ref_Key]['CabinBaggage'] = $hand_baggage;
                    }
                    // debug($arrBaggageData);exit;
                    $arrBaggageData[$strFare_Ref_Key]['AvailableSeats'] = $booking ['@attributes'] ['BookingCount'];
                    $flight_journey ['FlightDetails'] ['Details'] [$explode_key [0]][$explode_key [1]]['Attr']['Baggage'] = @$arrBaggageData[$strFare_Ref_Key]['Baggage'];
                    $flight_journey ['FlightDetails'] ['Details'] [$explode_key [0]][$explode_key [1]]['Attr']['CabinBaggage'] = @$arrBaggageData[$strFare_Ref_Key]['CabinBaggage'];
                    $flight_journey ['FlightDetails'] ['Details'] [$explode_key [0]][$explode_key [1]]['Attr']['AvailableSeats'] = @$arrBaggageData[$strFare_Ref_Key]['AvailableSeats'];

                    // $flight_journey ['FlightDetails'] ['Details'] [$explode_key [0]][$explode_key [1]]['fare_info_ref'] = $booking ['@attributes'] ['FareInfoRef'];
                    // $flight_journey ['FlightDetails'] ['Details'] [$explode_key [0]][$explode_key [1]]['fare_basis'] = @$arrFareruleData[$fare_info_ref]['FareBasis'];
                    // $flight_journey ['FlightDetails'] ['Details'] [$explode_key [0]][$explode_key [1]]['booking_code'] = $booking ['@attributes'] ['BookingCode'];
                    
                    $flight_list_on_out ['flight_list'] [$explode_key [0]] ['flight_detail'] [$explode_key [1]] ['booking_code'] = $booking ['@attributes'] ['BookingCode'];
                    $flight_list_on_out ['flight_list'] [$explode_key [0]] ['flight_detail'] [$explode_key [1]] ['booking_counts'] = $booking ['@attributes'] ['BookingCount'];
                    $flight_list_on_out ['flight_list'] [$explode_key [0]] ['flight_detail'] [$explode_key [1]] ['cabin_class'] = $booking ['@attributes'] ['CabinClass'];
                    $flight_list_on_out ['flight_list'] [$explode_key [0]] ['flight_detail'] [$explode_key [1]] ['fare_info_ref'] = $fare_info_ref = $booking ['@attributes'] ['FareInfoRef'];
                    $flight_list_on_out ['flight_list'] [$explode_key [0]] ['flight_detail'] [$explode_key [1]] ['host_token_key'] = $host_token_key = @$booking ['@attributes'] ['HostTokenRef'];
                    $flight_list_on_out ['flight_list'] [$explode_key [0]] ['flight_detail'] [$explode_key [1]] ['fare_basis'] = @$arrFareruleData[$fare_info_ref]['FareBasis'];
                    $flight_list_on_out ['flight_list'] [$explode_key [0]] ['flight_detail'] [$explode_key [1]] ['fare_info_value'] = @$arrFareruleData[$fare_info_ref]['FareInfoValue'];
                    $flight_list_on_out ['flight_list'] [$explode_key [0]] ['flight_detail'] [$explode_key [1]] ['ProviderCode'] = @$arrFareruleData[$fare_info_ref]['ProviderCode'];
                    $flight_list_on_out ['flight_list'] [$explode_key [0]] ['flight_detail'] [$explode_key [1]] ['host_token_value'] = @$arrHostTokenData[$host_token_key]['HostTokenValue'];
                    unset($arrHostTokenData[$host_token_key]);
                }
            }
          // debug($conversion_amount);exit;
            $carrier = @$flight_journey['FlightDetails']['Details'][0][0]['OperatorCode'];
            $flight_journey ['Price'] ['Currency'] = 'INR';
            $flight_journey ['Price'] ['TotalDisplayFare'] = $conversion_amount['conversion_rate']*$display_price;
            $flight_journey ['Price'] ['PriceBreakup'] ['Tax'] = $conversion_amount['conversion_rate']*$total_tax;
            $flight_journey ['Price'] ['PriceBreakup'] ['BasicFare'] = $conversion_amount['conversion_rate']*$approx_base_price;
            $flight_journey ['Price'] ['PriceBreakup']['Tax_Details'] = tax_breakup($tax_details);
            
            $check_com = true;
            flight_commission_calculation($check_com,$carrier, $search_data['is_domestic'], $this->booking_source,$CabinClass,$flight_journey,$approx_base_price,$tax_details);
            
            $flight_journey ['Attr']['IsRefundable'] = $Refundable;
            $flight_journey ['Attr']['AirlineRemark'] = '';
            $flight_journey ['HoldTicket'] = true;
        }
        if(valid_array($flight_journey['FlightDetails'])){
            $token_data [0] = $flight_list_on_out;
            $token_data [0]['booking_source'] = $this->booking_source;
            $flight_journey ['ResultToken'] = serialized_data($token_data);
            // $flight_journey ['token_key'] = md5($flight_journey ['ResultToken']);
           
            return $flight_journey;
        }
    }
private function flight_detail_format($segment, $flight_key, $trip_type, $is_leg,&$airport_list,&$airline_list) {
      
        $duration_sec = calculate_duration(@$segment ['@attributes'] ['DepartureTime'], @$segment ['@attributes'] ['ArrivalTime']);
     
        $duration = get_duration_label(@$duration_sec);
        $duration_sec             = $duration_sec/60;

        $origine_name             = get_airport_city(@$segment ['@attributes'] ['Origin'],'CN',$airport_list);
        $origine_airport_name     = get_airport_city(@$segment ['@attributes'] ['Origin'],'AN',$airport_list);
        $destination_name         = get_airport_city(@$segment ['@attributes'] ['Destination'],'CN',$airport_list);
        $destination_airport_name = get_airport_city(@$segment ['@attributes'] ['Destination'],'AN',$airport_list);

        $DepartureTime = explode('T', $segment ['@attributes'] ['DepartureTime']);
        $DepartureTime1 = explode('.', $DepartureTime[1]);

        $depart_date = date("Y-m-d", strtotime(@$segment ['@attributes'] ['DepartureTime']));
        $depart_time = date("H:i:s", strtotime(@$segment ['@attributes'] ['DepartureTime']));

        $arrival_date = date("Y-m-d", strtotime(@$segment ['@attributes'] ['ArrivalTime']));
        $arrival_time = date("H:i:s", strtotime(@$segment ['@attributes'] ['ArrivalTime']));

        $ArrivalTime = explode('T', $segment ['@attributes'] ['ArrivalTime']);
        $ArrivalTime1 = explode('.', $ArrivalTime[1]);

        $flight_detail_arr = array();

        $flight_detail_arr ['Origin'] = array(
            'AirportCode' => @$segment ['@attributes'] ['Origin'],
            'CityName' => $origine_name,
            'AirportName' => $origine_airport_name,
            'date_time' => @$segment ['@attributes'] ['DepartureTime'],
            'DateTime' => @$DepartureTime[0] . " " . @$DepartureTime1[0],
            'date' => @$DepartureTime[0], // Derive
            'time' => @$DepartureTime1[0], // Derive H i/h i a
            'FDTV' => strtotime(@$DepartureTime1[0])
        );

        $flight_detail_arr ['Destination'] = array(
            'AirportCode' => @$segment ['@attributes'] ['Destination'],
            'CityName' => $destination_name,
            'AirportName' => $destination_airport_name,
            'date_time' => @$segment ['@attributes'] ['ArrivalTime'],
            'DateTime' => @$ArrivalTime[0] . " " . @$ArrivalTime1[0],
            'date' => @$ArrivalTime[0], // Derive
            'time' => @$ArrivalTime1[0], // Derive H i/h i a
            'FATV' => strtotime(@$ArrivalTime1[0])
        );
        
        // $flight_detail_arr ['duration_seconds'] = $duration_sec;
        $flight_detail_arr ['Duration'] = @$duration;
        $flight_detail_arr ['OperatorCode'] = @$segment ['@attributes'] ['Carrier'];
        $flight_detail_arr ['OperatorName'] = get_airline_name($flight_detail_arr ['OperatorCode'],$airline_list);
        $flight_detail_arr ['FlightNumber'] = @$segment ['@attributes'] ['FlightNumber'];
        $flight_detail_arr ['is_leg'] = $is_leg;

        return $flight_detail_arr;
}
 /*
     *
     * get hand baggage based on airport code
     */
    private function get_hand_baggage_info($airline_code,&$baggage_list) {
        $CI = & get_instance();
        if(isset($baggage_list[$airline_code])){
            return $baggage_list[$airline_code];
        }else{
            $hand_baggage = $this->CI->custom_db->single_table_records('airline_baggage', '*', array('code' => $airline_code));
            if(isset($hand_baggage['data'][0])){
                return $baggage_list[$airline_code] = $hand_baggage['data'][0]['weight'].' Kg';
            }else{
                $hand_baggage = '';
            }
        }
    }

    /* date format */
    function travelport_date_format($date){
        $date = date("Y-m-d", strtotime(str_replace('/', '-', $date)));
        return $date;
    }
    /**
     * flight low fare search request sync
     * 
     * @param array $search_data            
     */
     function flight_low_fare_search_req($search_data) {
        // error_reporting(E_ALL);
        $request = array();
        $response ['status'] = SUCCESS_STATUS;
        $response ['data'] = array();
        $search_data ['method'] = 'Sync';

        if($search_data['cabin_class'] == 'all'){
            $cabin_class = 'Economy';
        }else{
            $cabin_class = ucfirst($search_data['cabin_class']);
        }
        $airline_list = array();
       // $airline_list = $this->get_airline_list($search_data['is_domestic']);
        /* Prefered airline details */
        $prefered_carrier_ACH = '';
        $prefered_carrier_GDS = '';
        
        if ($search_data["carrier"] != "" && $search_data["carrier"] != "all" && strlen($search_data["carrier"]) == 2) {
            $carrier_airline_list[] = $search_data["carrier"];
            $prefered_carrier_ACH = $this->prefered_airline($carrier_airline_list,'ACH');
            $prefered_carrier_GDS = $this->prefered_airline($carrier_airline_list,'1G');
        }
        if(empty($prefered_carrier_GDS)){
            $prefered_carrier_GDS = $this->prefered_airline($airline_list,'1G');
        }
        if(empty($prefered_carrier_ACH)){
            $prefered_carrier_ACH = $this->prefered_airline(array(),'ACH');
        }

        /* Cabin class details */
        $LegModifiers = '<AirLegModifiers><PermittedCabins>
                            <CabinClass Type="' . $cabin_class . '" xmlns="http://www.travelport.com/schema/common_v41_0"></CabinClass>
                       	</PermittedCabins>
						</AirLegModifiers>';
        
        $infant    = '';
        $adult     = '';
        $child     = '';
        $child_ACH = '';

        $adult     = $this->search_pax_type($search_data ['adult'],'ADT');
        $child     = $this->search_pax_type($search_data ['child'],'CNN');
        $child_ACH = $this->search_pax_type($search_data ['child'],'CHD');
        $infant    = $this->search_pax_type($search_data ['infant'],'INF');
        $search_method = "LowFareSearchReq";
        $circle = '';
        $circle_ach = '';

        $origin_code = $search_data ['from'];
        $desti_code = $search_data ['to'];
        $depart_date = $this->travelport_date_format($search_data ['depature']);
        $segment_info = '';
        if ($search_data['trip_type'] == 'oneway') {
            $segment_info = $this->search_segment($origin_code,$desti_code,$depart_date,$LegModifiers);
        } else if ($search_data['trip_type'] == 'return' && $search_data['is_domestic'] != 1) {
            $arrival_date = '';
        if(isset($search_data ['return']) and !empty($search_data ['return'])){
            $arrival_date = $this->travelport_date_format($search_data ['return']);
        }
        $segment_info  = $this->search_segment($origin_code,$desti_code,$depart_date,$LegModifiers);
        $segment_info .= $this->search_segment($desti_code,$origin_code,$arrival_date,$LegModifiers);
          
        } else if ($search_data['trip_type'] == 'return' && $search_data['is_domestic'] == 1) {
            $arrival_date = '';
            if(isset($search_data ['return']) and !empty($search_data ['return'])){
                $arrival_date = $this->travelport_date_format($search_data ['return']);
            }
            $segment_info = $this->search_segment($origin_code,$desti_code,$depart_date,$LegModifiers);
            $circle = $this->search_segment($desti_code,$origin_code,$arrival_date,$LegModifiers);
            
        }else if ($search_data['trip_type'] == 'multicity') {
            $arrMultiFrmCity = $search_data['from_city'];
            $arrMultiToCity = $search_data['to_city'];
            $arrMultiCheckin = $search_data['depature'];
            for ($mul = 0; $mul < count($arrMultiFrmCity); $mul++) {
                $origin_code_m = $arrMultiFrmCity[$mul];
                $desti_code_m = $arrMultiToCity[$mul];
                $depart_date_m = $this->travelport_date_format($arrMultiCheckin[$mul]);
                $segment_info .= $this->search_segment($origin_code_m,$desti_code_m,$depart_date_m,$LegModifiers);
            }
        }
        /*$this->config['flight_type'] = 'GDS';*/
        /* request for search flight */
        if($this->config['flight_type']=='BOTH' or $this->config['flight_type']=='GDS'){
            /* GDS Request */
            $travelport_request[0][] = $this->flight_type_search_request($search_method,$segment_info,$adult,$child,$infant,$prefered_carrier_GDS);
            if ($search_data['trip_type'] == 'return' && $search_data['is_domestic'] == 1){
                $travelport_request[1][]     = $this->flight_type_search_request($search_method,$circle,$adult,$child,$infant,$prefered_carrier_GDS);
             }
        }
        if($this->config['flight_type']=='BOTH' or $this->config['flight_type']=='LCC'){
            /* ACH Request */
            $travelport_request[0][] = $this->flight_type_search_request($search_method,$segment_info,$adult,$child_ACH,$infant,$prefered_carrier_ACH);
            if ($search_data['trip_type'] == 'return' && $search_data['is_domestic'] == 1){
                $travelport_request[1][] = $this->flight_type_search_request($search_method,$circle,$adult,$child_ACH,$infant,$prefered_carrier_ACH);
             }
        }

        $request ['request'] = $travelport_request;
        $request ['url'] = $this->config['EndPointUrl'];
        $request ['soap_action'] = '';
        $request ['status'] = SUCCESS_STATUS;
        //debug($request);exit;
        return $request;
    }

    function prefered_airline($airline_list = array(),$flight_type){
        $prefered_airline = '';
        if(valid_array($airline_list)){
            $prefered_airline = '<PermittedCarriers>';
            foreach ($airline_list as $key => $flight_name) {
                $prefered_airline .= '<Carrier xmlns="http://www.travelport.com/schema/common_v41_0" Code="'.$flight_name.'"/>';
            }
            $prefered_airline .= '</PermittedCarriers>';
        }

        $AirSearchModifiers = '<AirSearchModifiers MaxSolutions="100"><PreferredProviders>
                     <Provider xmlns="http://www.travelport.com/schema/common_v41_0" Code="'.$flight_type.'" />
                     </PreferredProviders>
                     </AirSearchModifiers>';

        return $AirSearchModifiers;
    }

    function search_pax_type($pax_conut=array(),$pax_type){
        $pax_type_str = '';
        if(isset($pax_conut)){
            for($i=0;$i<$pax_conut;$i++) {
                $pax_type_str .= '<SearchPassenger Code="'.$pax_type.'" xmlns="http://www.travelport.com/schema/common_v41_0" />';
            }
        }
        return $pax_type_str;
    }

    function search_segment($origin_code,$destination_code,$dep_date,$LegModifiers){
        $search_segment = '<SearchAirLeg>
                            <SearchOrigin>
                                <CityOrAirport Code="' . $origin_code . '" xmlns="http://www.travelport.com/schema/common_v41_0" />
                            </SearchOrigin>
                            <SearchDestination>
                                <CityOrAirport Code="' . $destination_code . '" xmlns="http://www.travelport.com/schema/common_v41_0" />
                            </SearchDestination>
                            <SearchDepTime PreferredTime="' . $dep_date . '" />
                           ' . $LegModifiers . '
                        </SearchAirLeg>';
        return $search_segment;
    }
    function flight_type_search_request($search_method,$segment_info,$adult,$child,$infant,$prefered_carrier){
        $flight_type_search_request = '<?xml version="1.0" encoding="utf-8"?>
            <s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
                <s:Header>
                    <Action s:mustUnderstand="1" xmlns="http://schemas.microsoft.com/ws/2005/05/addressing/none">localhost:8080/kestrel/AirService</Action>
                </s:Header>
                <s:Body xmlns:xsi="http://www.w3.org/2001/xmlschema-instance" xmlns:xsd="http://www.w3.org/2001/xmlschema">
                    <' . $search_method . ' SolutionResult="true" AuthorizedBy="user" TraceId="394d96c00971c4545315e49584609ff6" TargetBranch="' . $this->config ['Target'] . '" xmlns="http://www.travelport.com/schema/air_v41_0">
                        <BillingPointOfSaleInfo OriginApplication="UAPI" xmlns="http://www.travelport.com/schema/common_v41_0" />
                            ' . $segment_info . '
                            ' . $prefered_carrier . '
                            ' . $adult . '
                            ' . $child . '
                            ' . $infant . '
                            <AirPricingModifiers FaresIndicator="AllFares"></AirPricingModifiers>
                    </' . $search_method . '>
                   
                </s:Body>
            </s:Envelope>';
        return $flight_type_search_request;
    }

    public function format_update_fare_quote_response($air_price_response, $price_response, $search_id, $flight_details_key, $price_request,$flight_search_data) {
        $search_data = $this->search_data($search_id);
        $search_data = $search_data['data'];
       
        $status = TRUE;
        if ($status = SUCCESS_STATUS) {
            
            $passenger = array();
            $passengers_array = array();
            $change_panelty = array();
            $cncel_panelty = array();
            $flight_details = array();
            $flights = array();
            $flight_journey = $flight_search_data['flight_search_data'];

            if (isset($air_price_response ['SOAP:Envelope'] ['SOAP:Body'] ['air:AirPriceRsp'] ['air:AirPriceResult'])) {
                $itenary_details = $air_price_response ['SOAP:Envelope'] ['SOAP:Body'] ['air:AirPriceRsp'] ['air:AirItinerary'];
                $air_price_result_arr = $air_price_response ['SOAP:Envelope'] ['SOAP:Body'] ['air:AirPriceRsp'] ['air:AirPriceResult'] ['air:AirPricingSolution'];
                $transction_id = $air_price_response ['SOAP:Envelope'] ['SOAP:Body'] ['air:AirPriceRsp'] ['@attributes'] ['TransactionId'];


                // Take minimun value price array
                if (isset($air_price_result_arr) && valid_array($air_price_result_arr)) {
                    $air_price_result = force_multple_data_format($air_price_result_arr);
                    $air_price_result = $air_price_result [0];
                }
                $air_segment_booking_arr = array();
                $air_pricing_info_booking_arr = array();
                $booking_traveller_key = array();
                $air_pricing_sol = array();

                // AirPricingSolution
                $air_pricing_sol ['AirPricingSolution'] ['@attributes'] ['xmlns'] = "http://www.travelport.com/schema/air_v41_0";
                $air_pricing_sol ['AirPricingSolution'] ['@attributes'] ['Key'] = $air_price_result ['@attributes'] ['Key'];
                $air_pricing_sol ['AirPricingSolution'] ['@attributes'] ['TotalPrice'] = $air_price_result ['@attributes'] ['TotalPrice'];
                $air_pricing_sol ['AirPricingSolution'] ['@attributes'] ['BasePrice'] = $air_price_result ['@attributes'] ['BasePrice'];
                $air_pricing_sol ['AirPricingSolution'] ['@attributes'] ['ApproximateTotalPrice'] = $air_price_result ['@attributes'] ['ApproximateTotalPrice'];
                $air_pricing_sol ['AirPricingSolution'] ['@attributes'] ['ApproximateBasePrice'] = $air_price_result ['@attributes'] ['ApproximateBasePrice'];
                $air_pricing_sol ['AirPricingSolution'] ['@attributes'] ['EquivalentBasePrice'] = @$air_price_result ['@attributes'] ['EquivalentBasePrice'];
                $air_pricing_sol ['AirPricingSolution'] ['@attributes'] ['Taxes'] = $air_price_result ['@attributes'] ['Taxes'];
                $air_pricing_sol ['AirPricingSolution'] ['@attributes'] ['ApproximateTaxes'] = $air_price_result ['@attributes'] ['ApproximateTaxes'];
                $air_pricing_sol ['AirPricingSolution'] ['@attributes'] ['QuoteDate'] = @$air_price_result ['@attributes'] ['QuoteDate'];

               
                $air_seg_key = array();
                $air_seg_ref = force_multple_data_format($itenary_details ['air:AirSegment']);
                
                $segment_list = array();
                if (isset($air_seg_ref) && valid_array($air_seg_ref)) {
                    foreach ($air_seg_ref as $s_key => $seg_key) {
                        $provider_code = $seg_key['@attributes']['ProviderCode'];
                        $air_seg_key [] = $seg_key ['@attributes'] ['Key'];
                        $segment_list[$seg_key ['@attributes'] ['Key']]['Origin'] = $seg_key['@attributes']['Origin'];
                        $segment_list[$seg_key ['@attributes'] ['Key']]['Destination'] = $seg_key['@attributes']['Destination'];
                    }
                }
               
                $tax_details = array();

                // format Air price Result

                if (isset($air_price_result ['air:AirPricingInfo']) && valid_array($air_price_result ['air:AirPricingInfo'])) {
                    $currency          = substr($air_price_result ['@attributes'] ['ApproximateTotalPrice'], 0, 3);
                    $conversion_amount = $GLOBALS ['CI']->domain_management_model->get_currency_conversion_rate($currency);
                    // Passenger details starts here
                    $air_price_result ['air:AirPricingInfo'] = force_multple_data_format($air_price_result ['air:AirPricingInfo']);
                    $pass_key1 = 0;

                    foreach ($air_price_result ['air:AirPricingInfo'] as $ap_key => $air_price_sol) {
                        
                        // pax count
                        $Passenger_type = force_multple_data_format($air_price_sol['air:PassengerType']);

                        if(valid_array($Passenger_type)){
                          
                            if (isset($Passenger_type[0]['@attributes'])) {
                                $pass_code = $Passenger_type[0]['@attributes']['Code'];
                            }else{
                                $pass_code = $Passenger_type[0]['Code'];
                            }
                          
                            // booking_traveller_key[$pass_code] = $Passenger_type[0]['@attributes']['BookingTravelerRef'];
                            $passenger_count = count($Passenger_type);
                            
                        }
                        
                        foreach($Passenger_type as $pass_key => $pass_type){
                            $passenger_code = $pass_type['@attributes']['Code'];
                            $booking_traveller_key[$pass_key1]['Code'] = $passenger_code;
                            $booking_traveller_key[$pass_key1]['Key'] = @$pass_type['@attributes']['BookingTravelerRef'];
                           
                            $booking_traveller_keys[] = $pass_type['@attributes']['BookingTravelerRef'];
                        }
                        $pass_key1++;
                        
                        $fare_air_tax_detail_inf = force_multple_data_format(@$air_price_sol ['air:TaxInfo']);
                       
                        if (isset($fare_air_tax_detail_inf) && valid_array($fare_air_tax_detail_inf)) {
                            foreach ($fare_air_tax_detail_inf as $tax) {
                                if(isset($tax_details[$tax['@attributes']['Category']])){
                                    $tax_details[$tax['@attributes']['Category']] += $conversion_amount['conversion_rate']*substr(@$tax['@attributes']['Amount'], 3)*$passenger_count;
                                }else{
                                    $tax_details[$tax['@attributes']['Category']] = $conversion_amount['conversion_rate']*substr(@$tax['@attributes']['Amount'], 3)*$passenger_count;
                                }
                            }
                        }

                        if (isset($air_price_sol['@attributes']['EquivalentBasePrice'])) {
                            $BasePrice = substr($air_price_sol['@attributes']['EquivalentBasePrice'], 3);
                        } else if (isset($air_price_sol['@attributes']['ApproximateBasePrice'])) {
                            $BasePrice = substr($air_price_sol['@attributes']['ApproximateBasePrice'], 3);
                        } else {
                            $BasePrice = substr($air_price_sol['@attributes']['BasePrice'], 3);
                        }
                    
                        if (isset($air_price_sol['@attributes']['ApproximateTaxes'])) {
                            $Taxes = substr($air_price_sol['@attributes']['ApproximateTaxes'], 3);
                        } else {
                            if (isset($air_price_sol['@attributes']['Taxes'])) {
                                $Taxes = substr($air_price_sol['@attributes']['Taxes'], 3);
                            }
                        }
                        if (isset($air_price_sol['@attributes']['ApproximateFees'])) {
                            $Fees = substr($air_price_sol['@attributes']['ApproximateFees'], 3);
                        } else {
                            if (isset($air_price_sol['@attributes']['Fees'])) {
                                $Fees = substr($air_price_sol['@attributes']['Fees'], 3);
                            }
                        }

                        if($pass_code=='CNN'){
                            $pass_code='CHD';
                        }
                        $flight_journey ['Price']['PassengerBreakup'][$pass_code]['PassengerCount'] = $passenger_count;
                        $flight_journey ['Price']['PassengerBreakup'][$pass_code]['BasePrice'] = $conversion_amount['conversion_rate']*$BasePrice;
                        $flight_journey ['Price']['PassengerBreakup'][$pass_code]['Tax'] = $conversion_amount['conversion_rate']*($Taxes+$Fees);
                        $flight_journey ['Price']['PassengerBreakup'][$pass_code]['TotalPrice'] = $conversion_amount['conversion_rate']*($BasePrice + $Taxes+$Fees);

                        // XML for booking AirPricingInfo
                        $air_pricing_info_booking_arr ['AirPricingInfo'] ['@attributes'] ['Key'] = $air_price_sol ['@attributes'] ['Key'];
                        $air_pricing_info_booking_arr ['AirPricingInfo'] ['@attributes'] ['TotalPrice'] = $air_price_sol ['@attributes'] ['TotalPrice'];
                        $air_pricing_info_booking_arr ['AirPricingInfo'] ['@attributes'] ['BasePrice'] = $air_price_sol ['@attributes'] ['BasePrice'];
                        $air_pricing_info_booking_arr ['AirPricingInfo'] ['@attributes'] ['ApproximateTotalPrice'] = $air_price_sol ['@attributes'] ['ApproximateTotalPrice'];
                        $air_pricing_info_booking_arr ['AirPricingInfo'] ['@attributes'] ['ApproximateBasePrice'] = $air_price_sol ['@attributes'] ['ApproximateBasePrice'];
                        $air_pricing_info_booking_arr ['AirPricingInfo'] ['@attributes'] ['EquivalentBasePrice'] = @$air_price_sol ['@attributes'] ['EquivalentBasePrice'];
                        $air_pricing_info_booking_arr ['AirPricingInfo'] ['@attributes'] ['ApproximateTaxes'] = $air_price_sol ['@attributes'] ['ApproximateTaxes'];
                        $air_pricing_info_booking_arr ['AirPricingInfo'] ['@attributes'] ['Taxes'] = $air_price_sol ['@attributes'] ['Taxes'];
                        $air_pricing_info_booking_arr ['AirPricingInfo'] ['@attributes'] ['LatestTicketingTime'] = @$air_price_sol ['@attributes'] ['LatestTicketingTime'];
                        $air_pricing_info_booking_arr ['AirPricingInfo'] ['@attributes'] ['PricingMethod'] = @$air_price_sol ['@attributes'] ['PricingMethod'];
                        $air_pricing_info_booking_arr ['AirPricingInfo'] ['@attributes'] ['IncludesVAT'] = @$air_price_sol ['@attributes'] ['IncludesVAT'];
                        $air_pricing_info_booking_arr ['AirPricingInfo'] ['@attributes'] ['ETicketability'] = @$air_price_sol ['@attributes'] ['ETicketability'];
                        $air_pricing_info_booking_arr ['AirPricingInfo'] ['@attributes'] ['PlatingCarrier'] = @$air_price_sol ['@attributes'] ['PlatingCarrier'];
                        $air_pricing_info_booking_arr ['AirPricingInfo'] ['@attributes'] ['ProviderCode'] = @$air_price_sol ['@attributes'] ['ProviderCode'];
                        // air:FareInfo
                        $fare_info_response = force_multple_data_format($air_price_sol ['air:FareInfo']);
                     // debug($fare_info_response ); exit;
                       
                        if (isset($fare_info_response) && valid_array($fare_info_response)) {
                            
                            foreach ($fare_info_response as $fk_eky => $fare_infor) {
                                
                                $air_pricing_info_booking_arr ['AirPricingInfo'] ['FareInfo'] [$fk_eky] ['@attributes'] ['Key'] = $fare_infor ['@attributes'] ['Key'];
                                $air_pricing_info_booking_arr ['AirPricingInfo'] ['FareInfo'] [$fk_eky] ['@attributes'] ['FareBasis'] = $fare_infor ['@attributes'] ['FareBasis'];
                                $air_pricing_info_booking_arr ['AirPricingInfo'] ['FareInfo'] [$fk_eky] ['@attributes'] ['PassengerTypeCode'] = $fare_infor ['@attributes'] ['PassengerTypeCode'];
                                $air_pricing_info_booking_arr ['AirPricingInfo'] ['FareInfo'] [$fk_eky] ['@attributes'] ['Origin'] = $fare_infor ['@attributes'] ['Origin'];
                                $air_pricing_info_booking_arr ['AirPricingInfo'] ['FareInfo'] [$fk_eky] ['@attributes'] ['Destination'] = $fare_infor ['@attributes'] ['Destination'];
                                $air_pricing_info_booking_arr ['AirPricingInfo'] ['FareInfo'] [$fk_eky] ['@attributes'] ['EffectiveDate'] = $fare_infor ['@attributes'] ['EffectiveDate'];
                                $air_pricing_info_booking_arr ['AirPricingInfo'] ['FareInfo'] [$fk_eky] ['@attributes'] ['DepartureDate'] = $fare_infor ['@attributes'] ['DepartureDate'];
                                $air_pricing_info_booking_arr ['AirPricingInfo'] ['FareInfo'] [$fk_eky] ['@attributes'] ['Amount'] = $fare_infor ['@attributes'] ['Amount'];
                                if (isset($fare_infor ['@attributes'] ['NotValidBefore']) && !empty($fare_infor ['@attributes'] ['NotValidBefore'])) {
                                    $air_pricing_info_booking_arr ['AirPricingInfo'] ['FareInfo'] [$fk_eky] ['@attributes'] ['NotValidBefore'] = $fare_infor ['@attributes'] ['NotValidBefore'];
                                }
                                if (isset($fare_infor ['@attributes'] ['NotValidAfter']) && !empty($fare_infor ['@attributes'] ['NotValidAfter'])) {
                                    $air_pricing_info_booking_arr ['AirPricingInfo'] ['FareInfo'] [$fk_eky] ['@attributes'] ['NotValidAfter'] = $fare_infor ['@attributes'] ['NotValidAfter'];
                                }

                                if (isset($fare_infor ['air:FareRuleKey']) && valid_array($fare_infor ['air:FareRuleKey'])) {
                                    $air_pricing_info_booking_arr ['AirPricingInfo'] ['FareInfo'] [$fk_eky] ['FareRuleKey'] ['@attributes'] ['FareInfoRef'] = $fare_infor ['air:FareRuleKey'] ['@attributes'] ['FareInfoRef'];
                                    $air_pricing_info_booking_arr ['AirPricingInfo'] ['FareInfo'] [$fk_eky] ['FareRuleKey'] ['@attributes'] ['ProviderCode'] = $fare_infor ['air:FareRuleKey'] ['@attributes'] ['ProviderCode'];
                                    $air_pricing_info_booking_arr ['AirPricingInfo'] ['FareInfo'] [$fk_eky] ['FareRuleKey'] ['@value'] = $fare_infor ['air:FareRuleKey'] ['@value'];
                                }
                                
                               
                            }
                        }
                        
                        // air:BookingInfo
                        $fare_booking_info_Res = force_multple_data_format(@$air_price_sol ['air:BookingInfo']);

                        if (isset($fare_booking_info_Res) && valid_array($fare_booking_info_Res)) {
                            foreach ($fare_booking_info_Res as $bif_k => $booking_info_d) {
                                $air_pricing_info_booking_arr ['AirPricingInfo'] ['BookingInfo'] [$bif_k] ['@attributes'] ['BookingCode'] = $booking_info_d ['@attributes'] ['CabinClass'];
                                $air_pricing_info_booking_arr ['AirPricingInfo'] ['BookingInfo'] [$bif_k] ['@attributes'] ['CabinClass'] = $booking_info_d ['@attributes'] ['BookingCode'];
                                $air_pricing_info_booking_arr ['AirPricingInfo'] ['BookingInfo'] [$bif_k] ['@attributes'] ['FareInfoRef'] = $booking_info_d ['@attributes'] ['FareInfoRef'];
                                $air_pricing_info_booking_arr ['AirPricingInfo'] ['BookingInfo'] [$bif_k] ['@attributes'] ['SegmentRef'] = $booking_info_d ['@attributes'] ['SegmentRef'];
                            }
                        }
                       
                        // air:FareCalc
                        if (isset($air_price_sol ['air:FareCalc']) && !empty($air_price_sol ['air:FareCalc'])) {
                            $air_pricing_info_booking_arr ['AirPricingInfo'] ['FareCalc'] = $air_price_sol ['air:FareCalc'];
                        }

                        // air:PassengerType
                        $fare_air_pass_info = force_multple_data_format(@$air_price_sol ['air:PassengerType']);

                        if (isset($fare_air_pass_info) && valid_array($fare_air_pass_info)) {
                            foreach ($fare_air_pass_info as $fapass_k => $fare_passenger_info) {
                                $air_pricing_info_booking_arr ['AirPricingInfo'] ['PassengerType'] [$fapass_k] ['@attributes'] ['Code'] = @$fare_passenger_info ['@attributes'] ['Code'];
                                $air_pricing_info_booking_arr ['AirPricingInfo'] ['PassengerType'] [$fapass_k] ['@attributes'] ['BookingTravelerRef'] = @$fare_passenger_info ['@attributes'] ['BookingTravelerRef'];
                            }
                        }

                        // air:ChangePenalty
                        $air_chage_panelty_arr = force_multple_data_format(@$air_price_sol ['air:ChangePenalty']);
                        if (isset($air_chage_panelty_arr) && valid_array($air_chage_panelty_arr)) {
                            foreach ($air_chage_panelty_arr as $acpa_k => $change_panel) {
                                $air_pricing_info_booking_arr ['AirPricingInfo'] ['ChangePenalty'] [$acpa_k] ['Amount'] = @$change_panel ['air:Amount'];
                            }
                        }

                        // air:BaggageAllowances
                        $air_baggage_allow_arr = force_multple_data_format(@$air_price_sol ['air:BaggageAllowances'] ['air:BaggageAllowanceInfo']);
                        if (isset($air_baggage_allow_arr) && valid_array($air_baggage_allow_arr)) {
                            foreach ($air_baggage_allow_arr as $bg_al_k => $baggage_info) {
                                $air_pricing_info_booking_arr ['AirPricingInfo'] ['BaggageAllowances'] ['BaggageAllowanceInfo'] [$bg_al_k] ['@attributes'] ['TravelerType'] = $baggage_info ['@attributes'] ['TravelerType'];
                                $air_pricing_info_booking_arr ['AirPricingInfo'] ['BaggageAllowances'] ['BaggageAllowanceInfo'] [$bg_al_k] ['@attributes'] ['Origin'] = $baggage_info ['@attributes'] ['Origin'];
                                $air_pricing_info_booking_arr ['AirPricingInfo'] ['BaggageAllowances'] ['BaggageAllowanceInfo'] [$bg_al_k] ['@attributes'] ['Destination'] = $baggage_info ['@attributes'] ['Destination'];
                                $air_pricing_info_booking_arr ['AirPricingInfo'] ['BaggageAllowances'] ['BaggageAllowanceInfo'] [$bg_al_k] ['@attributes'] ['Carrier'] = $baggage_info ['@attributes'] ['Carrier'];
                                $air_pricing_info_booking_arr ['AirPricingInfo'] ['BaggageAllowances'] ['BaggageAllowanceInfo'] [$bg_al_k] ['URLInfo'] ['URL'] = @$baggage_info ['air:URLInfo'] ['air:URL'];

                                // taxt
                                $taxt_info_arr = force_multple_data_format($baggage_info ['air:TextInfo'] ['air:Text']);
                                if (isset($taxt_info_arr) && valid_array($taxt_info_arr)) {
                                    foreach ($taxt_info_arr as $tx_k => $txt_val) {
                                        $air_pricing_info_booking_arr ['AirPricingInfo'] ['BaggageAllowances'] ['BaggageAllowanceInfo'] [$bg_al_k] ['TextInfo'] ['Text'] [$tx_k] = @$txt_val;
                                    }
                                }
                                // air:BagDetails
                                $air_bag_details_res = force_multple_data_format($baggage_info ['air:BagDetails']);
                                if (isset($air_bag_details_res) && valid_array($air_bag_details_res)) {
                                    foreach ($air_bag_details_res as $beg_k => $beggage) {
                                        $air_pricing_info_booking_arr ['AirPricingInfo'] ['BaggageAllowances'] ['BaggageAllowanceInfo'] [$bg_al_k] ['BagDetails'] [$beg_k] ['@attributes'] ['ApplicableBags'] = @$beggage ['@attributes'] ['ApplicableBags'];
                                        // air:BaggageRestriction
                                        $baggage_rest_arr = force_multple_data_format($beggage ['air:BaggageRestriction']);
                                        // debug($baggage_rest_arr);exit;
                                        if (isset($baggage_rest_arr) && valid_array($baggage_rest_arr)) {
                                            foreach ($baggage_rest_arr as $begg_r_k => $beg_rest) {
                                                $air_pricing_info_booking_arr ['AirPricingInfo'] ['BaggageAllowances'] ['BaggageAllowanceInfo'] [$bg_al_k] ['BagDetails'] [$beg_k] ['BaggageRestriction'] ['TextInfo'] ['Text'] [$begg_r_k] = $beg_rest ['air:TextInfo'] ['air:Text'];
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        // air:CarryOnAllowanceInfo
                        $carry_on_baggage_arr = force_multple_data_format(@$air_price_sol ['air:BaggageAllowances'] ['air:CarryOnAllowanceInfo']);
                        if (isset($carry_on_baggage_arr) && valid_array($carry_on_baggage_arr)) {
                            foreach ($carry_on_baggage_arr as $carry_k => $carry_on_beg) {
                                $air_pricing_info_booking_arr ['AirPricingInfo'] ['BaggageAllowances'] ['CarryOnAllowanceInfo'] [$carry_k] ['@attributes'] ['Origin'] = $carry_on_beg ['@attributes'] ['Origin'];
                                $air_pricing_info_booking_arr ['AirPricingInfo'] ['BaggageAllowances'] ['CarryOnAllowanceInfo'] [$carry_k] ['@attributes'] ['Destination'] = $carry_on_beg ['@attributes'] ['Destination'];
                                $air_pricing_info_booking_arr ['AirPricingInfo'] ['BaggageAllowances'] ['CarryOnAllowanceInfo'] [$carry_k] ['@attributes'] ['Carrier'] = $carry_on_beg ['@attributes'] ['Carrier'];
                                $air_pricing_info_booking_arr ['AirPricingInfo'] ['BaggageAllowances'] ['CarryOnAllowanceInfo'] [$carry_k] ['TextInfo'] ['Text'] = $carry_on_beg ['air:TextInfo'] ['air:Text'];

                                // CarryOnDetails
                                if (isset($carry_on_beg ['air:CarryOnDetails']) && valid_array($carry_on_beg ['air:CarryOnDetails'])) {
                                    $carry_on_beg ['air:CarryOnDetails'] = force_multple_data_format($carry_on_beg ['air:CarryOnDetails']);
                                    foreach ($carry_on_beg ['air:CarryOnDetails'] as $cd_key => $carry_detl) {
                                        $air_pricing_info_booking_arr ['AirPricingInfo'] ['BaggageAllowances'] ['CarryOnAllowanceInfo'] [$carry_k] ['CarryOnDetails'] [$cd_key] ['@attributes'] ['ApplicableCarryOnBags'] = @$carry_detl ['@attributes'] ['ApplicableCarryOnBags'];
                                        $air_pricing_info_booking_arr ['AirPricingInfo'] ['BaggageAllowances'] ['CarryOnAllowanceInfo'] [$carry_k] ['CarryOnDetails'] [$cd_key] ['@attributes'] ['BasePrice'] = @$carry_detl ['@attributes'] ['BasePrice'];
                                        $air_pricing_info_booking_arr ['AirPricingInfo'] ['BaggageAllowances'] ['CarryOnAllowanceInfo'] [$carry_k] ['CarryOnDetails'] [$cd_key] ['@attributes'] ['TotalPrice'] = @$carry_detl ['@attributes'] ['TotalPrice'];
                                        if (isset($carry_detl ['air:BaggageRestriction']) && !empty($carry_detl ['air:BaggageRestriction'])) {
                                            $baggage_rest_carry_arr = force_multple_data_format(@$carry_detl ['air:BaggageRestriction']);
                                            if (isset($baggage_rest_carry_arr) && valid_array($baggage_rest_carry_arr)) {
                                                foreach ($baggage_rest_carry_arr as $begg_rc_k => $beg_carry_rest) {
                                                    $air_pricing_info_booking_arr ['AirPricingInfo'] ['BaggageAllowances'] ['CarryOnAllowanceInfo'] [$carry_k] ['CarryOnDetails'] [$cd_key] ['BaggageRestriction'] ['TextInfo'] ['Text'] [$begg_rc_k] = @$beg_carry_rest ['air:TextInfo'] ['air:Text'];
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        // change panelty
                        if (isset($air_price_sol ['air:ChangePenalty']) && valid_array($air_price_sol ['air:ChangePenalty'])) {
                            $change_panelty [] = @$air_price_sol ['air:ChangePenalty'] ['air:Amount'];
                        }
                        // cncel panelty
                        if (isset($air_price_sol ['air:CancelPenalty']) && valid_array($air_price_sol ['air:CancelPenalty'])) {
                            $cncel_panelty [] = @$air_price_sol ['air:CancelPenalty'] ['air:Amount'];
                        }
                    }

                    $currency          = substr($air_price_result ['@attributes'] ['ApproximateTotalPrice'], 0, 3);
                    $display_price     = substr(@$air_price_result ['@attributes'] ['ApproximateTotalPrice'], 3);
                    $total_tax         = substr(@$air_price_result ['@attributes'] ['ApproximateTaxes'], 3)+substr(@$air_price_result ['@attributes'] ['ApproximateFees'], 3);
                    $approx_base_price = substr($air_price_result ['@attributes'] ['ApproximateBasePrice'], 3);

                   
                    $carrier = @$flight_journey['FlightDetails']['Details'][0][0]['OperatorCode'];
                    $CabinClass = @$flight_journey['FlightDetails']['Details'][0][0]['class']['name'];
                    
                    $flight_journey ['Price'] ['Currency'] = $currency;
                    $flight_journey ['Price'] ['TotalDisplayFare'] = $conversion_amount['conversion_rate']*$display_price;
                    $flight_journey ['Price'] ['PriceBreakup'] ['Tax'] = $conversion_amount['conversion_rate']*$total_tax;
                    $flight_journey ['Price'] ['PriceBreakup'] ['BasicFare'] = $conversion_amount['conversion_rate']*$approx_base_price;
                    $flight_journey ['Price'] ['PriceBreakup']['Tax_Details'] = tax_breakup($tax_details);
                    
                    $check_com = true;
                    flight_commission_calculation($check_com,$carrier, $search_data['is_domestic'], $flight_search_data['booking_source'],$CabinClass,$flight_journey,$approx_base_price,$tax_details);
                    
                    // priceing end
    
                    $flight_details ['change_panelty'] = $change_panelty;
                    $flight_details ['cncel_panelty'] = $cncel_panelty;
                    
                   // $itenary_details
                    $air_pricing_xml = '';
                    $flight_air_seg_key_array = array();

                    if (isset($itenary_details ['air:AirSegment']) && valid_array($itenary_details ['air:AirSegment'])) {
                        $itenary_details = force_multple_data_format($itenary_details ['air:AirSegment']);
                        // $air_segment_booking_arr[$s_key]['key']
                        foreach ($itenary_details as $it_key => $air_segment) {
                            $flight_air_seg_key_array[] = @$air_segment ['@attributes'] ['Key'];
                            
                            if (in_array($air_segment ['@attributes'] ['Key'], $air_seg_key)) {
                                $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['@attributes'] ['Key'] = $air_segment ['@attributes'] ['Key'];
                                $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['@attributes'] ['Group'] = $air_segment ['@attributes'] ['Group'];
                                $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['@attributes'] ['Carrier'] = $air_segment ['@attributes'] ['Carrier'];
                                $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['@attributes'] ['FlightNumber'] = $air_segment ['@attributes'] ['FlightNumber'];
                                $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['@attributes'] ['ProviderCode'] = $air_segment ['@attributes'] ['ProviderCode'];
                                $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['@attributes'] ['Origin'] = $air_segment ['@attributes'] ['Origin'];
                                $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['@attributes'] ['Destination'] = $air_segment ['@attributes'] ['Destination'];
                                $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['@attributes'] ['DepartureTime'] = $air_segment ['@attributes'] ['DepartureTime'];
                                $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['@attributes'] ['ArrivalTime'] = $air_segment ['@attributes'] ['ArrivalTime'];
                                $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['@attributes'] ['FlightTime'] = $air_segment ['@attributes'] ['FlightTime'];
                                $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['@attributes'] ['TravelTime'] = $air_segment ['@attributes'] ['TravelTime'];
                                $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['@attributes'] ['Distance'] = @$air_segment ['@attributes'] ['Distance'];
                                $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['@attributes'] ['ClassOfService'] = $air_segment ['@attributes'] ['ClassOfService'];
                                $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['@attributes'] ['Equipment'] = @$air_segment ['@attributes'] ['Equipment'];
                                $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['@attributes'] ['ChangeOfPlane'] = $air_segment ['@attributes'] ['ChangeOfPlane'];
                                $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['@attributes'] ['OptionalServicesIndicator'] = $air_segment ['@attributes'] ['OptionalServicesIndicator'];
                                $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['@attributes'] ['AvailabilitySource'] = @$air_segment ['@attributes'] ['AvailabilitySource'];
                                $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['@attributes'] ['ParticipantLevel'] = @$air_segment ['@attributes'] ['ParticipantLevel'];
                                $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['@attributes'] ['LinkAvailability'] = @$air_segment ['@attributes'] ['LinkAvailability'];
                                $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['@attributes'] ['PolledAvailabilityOption'] = @$air_segment ['@attributes'] ['PolledAvailabilityOption'];
                                $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['@attributes'] ['AvailabilityDisplayType'] = @$air_segment ['@attributes'] ['AvailabilityDisplayType'];

                                // <CodeshareInfo OperatingCarrier="VY" OperatingFlightNumber="8770" /> air:CodeshareInfo
                                if (isset($air_segment ['air:CodeshareInfo'])) {
                                    if (valid_array($air_segment ['air:CodeshareInfo'])) { // debug($air_segment['air:CodeshareInfo']);
                                        $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['CodeshareInfo'] ['@attributes'] ['OperatingCarrier'] = str_replace('"', '', @$air_segment ['air:CodeshareInfo'] ['@attributes'] ['OperatingCarrier']);
                                        $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['CodeshareInfo'] ['@attributes'] ['OperatingFlightNumber'] = str_replace('"', '', @$air_segment ['air:CodeshareInfo'] ['@attributes'] ['OperatingFlightNumber']);
                                    } else {
                                        $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['CodeshareInfo'] ['@attributes'] ['OperatingCarrier'] = str_replace('"', '', @$air_segment ['air:CodeshareInfo']);
                                    }
                                }

                                if (isset($air_segment ['air:AirAvailInfo']) && valid_array($air_segment ['air:AirAvailInfo'])) {
                                    // booking count provide code
                                    $flights [$it_key] ['booking_count'] = @$air_segment ['air:AirAvailInfo'] ['air:BookingCodeInfo'] ['@attributes'] ['BookingCounts'];
                                    $flights [$it_key] ['provide_code'] = @$air_segment ['air:AirAvailInfo'] ['@attributes'] ['ProviderCode'];
                                    $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['AirAvailInfo'] ['@attributes'] ['ProviderCode'] = @$air_segment ['air:AirAvailInfo'] ['@attributes'] ['ProviderCode'];
                                    $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['AirAvailInfo'] ['BookingCodeInfo'] ['@attributes'] ['BookingCounts'] = @$air_segment ['air:AirAvailInfo'] ['air:BookingCodeInfo'] ['@attributes'] ['BookingCounts'];
                                }

                                // Fligth details
                                if (isset($air_segment ['air:FlightDetails']) && valid_array($air_segment ['air:FlightDetails'])) {
                                    
                                    $flights [$it_key] ['flight_detail_key'] = $air_segment ['air:FlightDetails'] ['@attributes'] ['Key'];

                                    $flights [$it_key] ['origin'] = $air_segment ['air:FlightDetails'] ['@attributes'] ['Origin'];
                                    $flights [$it_key] ['destination'] = $air_segment ['air:FlightDetails'] ['@attributes'] ['Destination'];

                                    /*$flights [$it_key] ['origin_city'] = get_airport_city($air_segment ['air:FlightDetails'] ['@attributes'] ['Origin']);*/
                                    $flights [$it_key] ['origin_city'] = $air_segment ['air:FlightDetails'] ['@attributes'] ['Origin'];

                                    /*$flights [$it_key] ['destination_city'] = get_airport_city($air_segment ['air:FlightDetails'] ['@attributes'] ['Destination']);*/
                                    $flights [$it_key] ['destination_city'] = $air_segment ['air:FlightDetails'] ['@attributes'] ['Destination'];
                                    $flights [$it_key] ['departure_date'] = date('d M Y', strtotime($air_segment ['air:FlightDetails'] ['@attributes'] ['DepartureTime']));
                                    $flights [$it_key] ['departure_datetime'] = $air_segment ['air:FlightDetails'] ['@attributes'] ['DepartureTime'];
                                    $flights [$it_key] ['arrival_date'] = date('d M Y', strtotime($air_segment ['air:FlightDetails'] ['@attributes'] ['ArrivalTime']));
                                    $flights [$it_key] ['departure_time'] = date('H:i', strtotime($air_segment ['air:FlightDetails'] ['@attributes'] ['DepartureTime']));
                                    $flights [$it_key] ['arrival_time'] = date('H:i', strtotime($air_segment ['air:FlightDetails'] ['@attributes'] ['ArrivalTime']));
                                    $flights [$it_key] ['flight_time'] = $air_segment ['air:FlightDetails'] ['@attributes'] ['FlightTime'];
                                    $flights [$it_key] ['travel_time'] = $air_segment ['air:FlightDetails'] ['@attributes'] ['TravelTime'];
                                    // $flights [$it_key] ['duration'] = get_duration_label(calculate_duration($air_segment ['air:FlightDetails'] ['@attributes'] ['DepartureTime'], $air_segment ['air:FlightDetails'] ['@attributes'] ['ArrivalTime']));
                                    $flights [$it_key] ['distance'] = $air_segment ['air:FlightDetails'] ['@attributes'] ['Distance'];

                                    $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['FlightDetails'] ['@attributes'] ['Key'] = $air_segment ['air:FlightDetails'] ['@attributes'] ['Key'];
                                    $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['FlightDetails'] ['@attributes'] ['Origin'] = $air_segment ['air:FlightDetails'] ['@attributes'] ['Origin'];
                                    $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['FlightDetails'] ['@attributes'] ['Destination'] = $air_segment ['air:FlightDetails'] ['@attributes'] ['Destination'];
                                    $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['FlightDetails'] ['@attributes'] ['DepartureTime'] = $air_segment ['air:FlightDetails'] ['@attributes'] ['DepartureTime'];
                                    $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['FlightDetails'] ['@attributes'] ['ArrivalTime'] = $air_segment ['air:FlightDetails'] ['@attributes'] ['ArrivalTime'];
                                    $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['FlightDetails'] ['@attributes'] ['FlightTime'] = $air_segment ['air:FlightDetails'] ['@attributes'] ['FlightTime'];
                                    $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['FlightDetails'] ['@attributes'] ['TravelTime'] = $air_segment ['air:FlightDetails'] ['@attributes'] ['TravelTime'];
                                    $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['FlightDetails'] ['@attributes'] ['Distance'] = $air_segment ['air:FlightDetails'] ['@attributes'] ['Distance'];
                                }

                                $flights [$it_key] ['air_seg_key'] = $air_segment ['@attributes'] ['Key'];
                                $flights [$it_key] ['group'] = $air_segment ['@attributes'] ['Group'];
                                $flights [$it_key] ['carrier'] = $air_segment ['@attributes'] ['Carrier'];
                                $flights [$it_key] ['flight_number'] = $air_segment ['@attributes'] ['FlightNumber'];
                                $flights [$it_key] ['class_of_service'] = $air_segment ['@attributes'] ['ClassOfService'];
                                $flights [$it_key] ['equipment'] = @$air_segment ['@attributes'] ['Equipment'];
                                $flights [$it_key] ['change_of_plane'] = $air_segment ['@attributes'] ['ChangeOfPlane'];
                                $flights [$it_key] ['optional_services_indicator'] = $air_segment ['@attributes'] ['OptionalServicesIndicator'];
                                $flights [$it_key] ['availability_source'] = @$air_segment ['@attributes'] ['AvailabilitySource'];
                                $flights [$it_key] ['participant_level'] = @$air_segment ['@attributes'] ['ParticipantLevel'];
                                $flights [$it_key] ['link_availability'] = @$air_segment ['@attributes'] ['LinkAvailability'];
                                $flights [$it_key] ['polled_availability_option'] = @$air_segment ['@attributes'] ['PolledAvailabilityOption'];
                                $flights [$it_key] ['availability_display_type'] = @$air_segment ['@attributes'] ['AvailabilityDisplayType'];
                            }
                        }
                        $response ['status'] = SUCCESS_STATUS;
                    }
                }
                //debug($flight_air_seg_key_array); exit;
                // $flight_journey['Air_segment_key_list'] = $flight_air_seg_key_array;
                // $flight_journey['Booking_traveller_list'] = $booking_traveller_key;
                $flight_details['JourneyList'][0][0] = $flight_journey;
                
                $air_pricing_sol ['AirPricingSolution'] ['AirSegment'] = $air_pricing_info_booking_arr ['AirSegment'];
                $air_pricing_sol ['AirPricingSolution'] ['AirPricingInfo'] = $air_pricing_info_booking_arr ['AirPricingInfo'];
                // debug($air_pricing_sol);exit;
                // FareNote
                $air_price_result ['air:FareNote'] = force_multple_data_format(@$air_price_result ['air:FareNote']);
                if (isset($air_price_result ['air:FareNote']) && valid_array($air_price_result ['air:FareNote'])) {
                    foreach ($air_price_result ['air:FareNote'] as $af_not_k => $air_fare_note) {
                        $air_pricing_sol ['AirPricingSolution'] ['FareNote'] [$af_not_k] ['@attributes'] ['Key'] = @$air_fare_note ['@attributes'] ['Key'];
                        $air_pricing_sol ['AirPricingSolution'] ['FareNote'] [$af_not_k] ['@value'] = @$air_fare_note ['@value'];
                    }
                }
                $air_price_result ['common_v41_0:HostToken'] = force_multple_data_format($air_price_result ['common_v41_0:HostToken']);
                if (isset($air_price_result ['common_v41_0:HostToken']) && valid_array($air_price_result ['air:FareNote'])) {
                    foreach ($air_price_result ['common_v41_0:HostToken'] as $af_not_k => $air_host_token) {
                        $air_pricing_sol ['AirPricingSolution'] ['HostToken'] [$af_not_k] ['@attributes'] ['Key'] = @$air_host_token ['@attributes'] ['Key'];
                        $air_pricing_sol ['AirPricingSolution'] ['HostToken'] [$af_not_k] ['@value'] = @$air_host_token ['@value'];
                    }
                }
               // debug($air_pricing_sol);
                $xml = ArrayToXML::createXML('AirPricingSolution', $air_pricing_sol);
                $air_pricing_xml .= str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $xml->saveXML());
                $air_pricing_xml = str_replace('<AirPricingSolution>', '', $air_pricing_xml);
                $air_pricing_xml = str_replace('</AirPricingSolution>
                </AirPricingSolution>', '</AirPricingSolution>', $air_pricing_xml);
                $air_pricing_xml = str_replace('</AirPricingSolution>
                </AirPricingSolution>', '</AirPricingSolution>', $air_pricing_xml);
                //echo $air_pricing_xml;exit;
                // $flight_details ['air_seg_key'] = $air_seg_key;
            }
            $AirPriceRes = $price_response;
            $AirPriceRes = str_replace('SOAP:', '', $AirPriceRes);
            $AirPriceRes = str_replace('air:', '', $AirPriceRes);
            $AirPriceRes = new SimpleXMLElement($AirPriceRes);

            $AirItinerary = $AirPriceRes->Body->AirPriceRsp->AirItinerary;
            $AirItinerary_xml = $AirItinerary->asXML();
            
            $AirPricingSolution = $AirPriceRes->Body->AirPriceRsp->AirPriceResult->AirPricingSolution;
            unset($AirPricingSolution->AirSegmentRef);
            $AirPricingSolution_xml = $AirPricingSolution->asXML();
          
            //storing the price_xml and ittinerary xml
            // $flight_details['JourneyList'][0][0]['price_xml'] = $AirPricingSolution_xml;
            // $flight_details['JourneyList'][0][0]['itinerary_xml'] = $AirItinerary_xml;
            if($provider_code == 'ACH'){
              
                //Meal information only for ACH flights
                $doc = new DOMDocument();
                $doc->loadXML($price_request);
                $xp = new DOMXPath($doc);
                foreach ($xp->query('//AirSegment') as $key => $tn) {
                    $seg_key = $air_seg_key[$key];
                    $tn->setAttribute('Key',$seg_key);
                   
                }
                foreach ($xp->query('//AirPriceReq') as $key => $tn) {
                    $tn->setAttribute('xmlns','http://www.travelport.com/schema/air_v41_0');
                    $tn->setAttribute('xmlns:common_v41_0','http://www.travelport.com/schema/common_v41_0');
                }

                foreach ($xp->query('//AirSegmentPricingModifiers') as $key => $tn) {
                    $seg_key = $air_seg_key[$key];
                    $tn->setAttribute('AirSegmentRef', $seg_key);
                }
                foreach ($xp->query('//AirPricingCommand') as $key => $tn) {
                    $tn->setAttribute('xmlns','http://www.travelport.com/schema/air_v41_0');
                }
                
                $passenger_xml ='';
                foreach($booking_traveller_key as $b_key => $b_value){
                    $passenger_xml .= '<SearchPassenger BookingTravelerRef="' . $b_value['Key'] . '" Code="'.$b_value['Code'].'" xmlns="http://www.travelport.com/schema/common_v41_0" ></SearchPassenger>';
                }
                
                $AirPricingCommand = $doc->getElementsByTagName("AirPricingCommand")->item(0);
                $fragment = $doc->createDocumentFragment();
                $fragment->appendXML($passenger_xml);
                $doc->documentElement->insertBefore($fragment, $AirPricingCommand);
                $price_request = $doc->saveXML();

                $price_request = str_replace('<?xml version="1.0"?>', '', $price_request);
                $price_request = str_replace('</AirPriceReq>', '', $price_request);
                // echo $price_request;exit;
                $optional_sevices = force_multple_data_format($air_price_result ['air:OptionalServices']['air:OptionalService']);
                
                $meal_array = array();
                $baggage_array = array();
                $meal_sort_array = array();
                $baggage_sort_array = array();
                $arr_key = 0;
                $arrb_key = 0;
                $sort_item = array();
                foreach ($optional_sevices as $opt_key => $opt_service) {
                    if($opt_service['@attributes']['Type'] == 'MealOrBeverage'){
                        $xml = ArrayToXML::createXML('OptionalService', $opt_service);
                        $opt_service_xml = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $xml->saveXML());
                        $opt_service_xml = str_replace('common_v41_0:', '', $opt_service_xml);
                        $opt_service_xml = str_replace('air:', '', $opt_service_xml);
                        $common_sevice_data = force_multple_data_format($opt_service['common_v41_0:ServiceData']);
                        $opt_service_segment = $common_sevice_data; 
                        if(count($common_sevice_data) > 1){
                            if(isset($opt_service['common_v41_0:ServiceInfo']['common_v41_0:Description'][0])){
                                $desc1 = $opt_service['common_v41_0:ServiceInfo']['common_v41_0:Description'][1];
                                if (strpos($desc1, 'Meal is available only on the first segment in a connecting flight') !== false) {
                                    unset($opt_service['common_v41_0:ServiceData'][1]);
                                    $opt_service_segment = $opt_service['common_v41_0:ServiceData']; 
                                }
                            }
                        }
                        // debug($booking_traveller_keys); exit;
                        foreach($opt_service_segment as $opt_seg){
                            $BookingTravelerRef = $opt_seg['@attributes']['BookingTravelerRef'];
                            $AirSegmentRef = $opt_seg['@attributes']['AirSegmentRef'];
                            $Air_k = array_search ($AirSegmentRef, $air_seg_key);
                            $Travel_key = array_search ($BookingTravelerRef, $booking_traveller_keys);
                            $price = substr($opt_service['@attributes']['ApproximateTotalPrice'], 3);
                            if(isset($meal_array[$Air_k][$Travel_key])){
                                $arr_key = count($meal_array[$Air_k][$Travel_key]);
                            }                      
                            if($price > 0){
                                
                                $meal_array[$Air_k][$Travel_key][$arr_key]['Code'] = $opt_service['@attributes']['Key'];
                                $meal_array[$Air_k][$Travel_key][$arr_key]['AirSegmentKey'] = $AirSegmentRef;
                                $meal_array[$Air_k][$Travel_key][$arr_key]['Price'] = $price;
                                $meal_array[$Air_k][$Travel_key][$arr_key]['Origin'] = $segment_list[$AirSegmentRef]['Origin'];
                                $meal_array[$Air_k][$Travel_key][$arr_key]['Destination'] = $segment_list[$AirSegmentRef]['Destination'];
                                $meal_array[$Air_k][$Travel_key][$arr_key]['Type'] = 'dynamic';
                                $meal_array[$Air_k][$Travel_key][$arr_key]['Description'] = $opt_service['@attributes']['DisplayText'];
                                $key1 ['key'][0]['Type'] = 'dynamic';
                                $key1 ['key'][0]['PriceXML'] = $price_request;
                                $key1 ['key'][0]['MealXML'] = $opt_service_xml;
                                $key1 ['key'][0]['Code'] = $opt_service['@attributes']['Key'];
                                $key1 ['key'][0]['Description'] = $opt_service['@attributes']['DisplayText'];
                                $meal_id = serialized_data($key1['key']);
                                $meal_array[$Air_k][$Travel_key][$arr_key]['MealId'] = $meal_id;
                            }
                        }
                        $opt_service_segment = array();
                    }
                     //baggage Information
                    if($opt_service['@attributes']['Type'] == 'Baggage'){
                        $xml = ArrayToXML::createXML('OptionalService', $opt_service);
                        $opt_service_xml = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $xml->saveXML());
                        $opt_service_xml = str_replace('common_v41_0:', '', $opt_service_xml);
                        $opt_service_xml = str_replace('air:', '', $opt_service_xml);
                        $common_sevice_data = force_multple_data_format($opt_service['common_v41_0:ServiceData']);
                        $opt_service_segment_baggage = $common_sevice_data; 
                        if(count($common_sevice_data) > 1){
                            if(isset($opt_service['common_v41_0:ServiceInfo']['common_v41_0:Description'][0])){
                                $desc1 = $opt_service['common_v41_0:ServiceInfo']['common_v41_0:Description'][1];
                                if (strpos($desc1, 'Meal is available only on the first segment in a connecting flight') !== false) {
                                    unset($opt_service['common_v41_0:ServiceData'][1]);
                                    $opt_service_segment_baggage = $opt_service['common_v41_0:ServiceData']; 
                                }
                            }
                        }
                        foreach($opt_service_segment_baggage as $opt_seg){
                            $BookingTravelerRef = $opt_seg['@attributes']['BookingTravelerRef'];
                            $AirSegmentRef = $opt_seg['@attributes']['AirSegmentRef'];
                            $Air_k_baggage = array_search ($AirSegmentRef, $air_seg_key);
                            $Travel_key_bgaggae = array_search ($BookingTravelerRef, $booking_traveller_keys);
                            $price = substr($opt_service['@attributes']['ApproximateTotalPrice'], 3);
                            if(isset($baggage_array[$Air_k_baggage][$Travel_key_bgaggae])){
                                $arrb_key = count($baggage_array[$Air_k_baggage][$Travel_key_bgaggae]);
                            }                      
                            if($price > 0){
                                $bagagge = substr($opt_service['@attributes']['ProviderDefinedType'],-2);
                                $baggage_array[$Air_k_baggage][$Travel_key_bgaggae][$arrb_key]['Code'] = $opt_service['@attributes']['Key'];
                                $baggage_array[$Air_k_baggage][$Travel_key_bgaggae][$arrb_key]['AirSegmentKey'] = $AirSegmentRef;
                                $baggage_array[$Air_k_baggage][$Travel_key_bgaggae][$arrb_key]['Price'] = $price;
                                $baggage_array[$Air_k_baggage][$Travel_key_bgaggae][$arrb_key]['Origin'] = $segment_list[$AirSegmentRef]['Origin'];
                                $baggage_array[$Air_k_baggage][$Travel_key_bgaggae][$arrb_key]['Destination'] = $segment_list[$AirSegmentRef]['Destination'];
                                $baggage_array[$Air_k_baggage][$Travel_key_bgaggae][$arrb_key]['Weight'] = $bagagge.' Kg';
                                $baggage_array[$Air_k_baggage][$Travel_key_bgaggae][$arrb_key]['Type'] = 'dynamic';
                                $key2 ['key'][0]['Type'] = 'dynamic';
                                $key2 ['key'][0]['PriceXML'] = $price_request;
                                $key2 ['key'][0]['BaggageXML'] = $opt_service_xml;
                                $key2 ['key'][0]['Code'] = $opt_service['@attributes']['Key'];
                                $key2 ['key'][0]['Description'] = $opt_service['@attributes']['DisplayText'];
                                $baggage_id = serialized_data($key2['key']);
                                $baggage_array[$Air_k_baggage][$Travel_key_bgaggae][$arrb_key]['BaggageId'] = $baggage_id;
                            }
                        }
                        $opt_service_segment_baggage = array();
                    }
                }
                foreach ($air_seg_key as $s_key => $seg_value) {
                    foreach($meal_array as $m_key => $m_value){
                        ksort($m_value);
                        if($air_seg_key[$s_key] == $m_value[0][0]['AirSegmentKey']){
                           $meal_sort_array[] = $m_value[0];
                           $meal_all_data[] = $m_value;
                        }
                    }
                    foreach($baggage_array as $b_key => $b_value){
                        ksort($b_value);
                        if($air_seg_key[$s_key] == $b_value[0][0]['AirSegmentKey']){
                            $baggage_sort_array[] = $b_value[0];
                            $baggage_all_data[] = $b_value;
                        }
                    }
                }
                $extra_sevice_data['all_meal_data'] = $meal_all_data;
                $extra_sevice_data['all_baggage_data'] = $baggage_all_data;
            }

            $key_data['key'] ['price_xml'] = $AirPricingSolution_xml;
            $key_data['key'] ['itinerary_xml'] = $AirItinerary_xml;
            $key_data['key'] ['Baggage'] = $baggage_sort_array;
            $key_data['key'] ['Meals'] = $meal_sort_array;
            $key_data['key'] ['all_data'] = $extra_sevice_data;
            $key_data['key'] ['booking_source'] = $this->booking_source;
            $key_data['key'] ['Air_segment_key_list'] = $flight_air_seg_key_array;
            $key_data['key'] ['Booking_traveller_list'] = $booking_traveller_key;
            // debug($key_data);exit;
            $flight_details['JourneyList'][0][0] ['ResultToken'] = serialized_data($key_data);
            $response ['data'] = $flight_details;
        }
        return $flight_details;
    }
    /**
     * Update Fare Quote
     * @param unknown_type $request
     */
    public function get_update_fare_quote($request, $search_id) {
        
        $fare_quote_request['flight_list'] = $request['flight_list'];
        $fare_quote_request['connection'] = $request['connection'];
        $fare_quote_request['booking_source'] = $request['booking_source'];
       
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        $update_fare_quote_request = $this->update_fare_quote_request($fare_quote_request, $search_id);

        $flight_details_key = $request['flight_list'][0]['flight_detail'][0]['fare_info_ref'];
        if ($update_fare_quote_request['status'] == SUCCESS_STATUS) {
            $price_response = $this->process_request($update_fare_quote_request['request'],'','flight_pricing(Travelport Flight)');
            // $price_response = $this->CI->db->query("select response from provab_api_response_history where origin=1716")->result_array()[0]['response'];
            // debug($price_response);exit;
            $update_fare_quote_response = Converter::createArray($price_response);

            if (valid_array($update_fare_quote_response) == true && isset($update_fare_quote_response['SOAP:Envelope'] ['SOAP:Body'] ['air:AirPriceRsp'] ['air:AirPriceResult'])) {
                $response ['status'] = SUCCESS_STATUS;
                $response ['data']['FareQuoteDetails'] = $this->format_update_fare_quote_response($update_fare_quote_response, $price_response, $search_id, $flight_details_key, $update_fare_quote_request['request_opt'],$request);
            } else {
                $response ['message'] = 'Not Available';
            }
        } else {
            $response ['status'] = FAILURE_STATUS;
        }
       
        return $response;
    }
    
    private function update_fare_quote_request($token, $search_id) {

        $air_segment = array();
        $CI = & get_instance();
      //  $search_id = '6045';
        $search_data = $this->search_data($search_id);
         if($search_data['data']['cabin_class'] == 'all' || $search_data['data']['cabin_class'] == 'All'){
            $cabin_class = 'Economy';
        }
        else{
            $cabin_class = ucfirst($search_data['data']['cabin_class']);
        }

        $provide = $token['flight_list'][0]['flight_detail'][0]['provider_code'];
        if($provide == '1G'){
            $child_type = 'CNN';
        } 
        else{
            $child_type = 'CHD';
        }
        
        $adults = $this->pax_xml_for_fare_quote($search_data['data'] ['adult_config'],'ADT',$paxId);
        $childs = $this->pax_xml_for_fare_quote($search_data['data'] ['child_config'],$child_type,$paxId,'10');
        $infants = $this->pax_xml_for_fare_quote($search_data['data'] ['infant_config'],'INF',$paxId,'01');
     
        $AirSegmentPricingModifiers1 = '';
        $host_token ='';
        
        // $exp_conn = explode ( ",", $flight->Connections );
        if (isset($token ['flight_list']) && valid_array($token['flight_list'])) {
            $origin_value = '';
            foreach ($token['flight_list'] as $fl_ley => $flights_list) {
                if (isset($flights_list ['flight_detail']) && valid_array($flights_list ['flight_detail'])) {
                    foreach ($flights_list ['flight_detail'] as $flight_key => $flight) {
                        $segment = '';
                        $group = $flight ['group'];
                        $carrier = $flight ['carrier'];
                        $flight_number = $flight ['flight_number'];
                        $origin = $flight ['origin'];
                        $origin_value .= $flight ['origin'];
                        $destination = $flight ['destination'];
                        $departure_time = $flight ['departure_time'];
                        $arrival_time = $flight ['arrival_time'];
                        $flight_time = $flight ['flight_time'];
                        $distance = isset($flight ['distance']) ? 'Distance="' . $flight ['distance'] . '"' : '';
                        $e_ticketability = (isset($flight ['e_ticketability'])) ? 'ETicketability="' . $flight ['e_ticketability'] . '"' : '';
                        $equipment = isset($flight ['equipment']) ? 'Equipment="' . $flight ['equipment'] . '"' : '';
                        $change_of_plane = $flight ['change_of_plane'];
                        $participant_level = isset($flight ['participant_level']) ? 'ParticipantLevel="' . $flight ['participant_level'] . '"' : '';
                        $polled_availability_option = (isset($flight ['polled_availability_option'])) ? 'PolledAvailabilityOption="' . $flight ['polled_availability_option'] . '"' : '';
                        $flight_seg_key = $flight ['flight_seg_key'];
                        $link_availability = (isset($flight ['link_availability'])) ? 'LinkAvailability="' . $flight ['link_availability'] . '"' : '';
                        $fare_info_ref = @$flight ['fare_info_ref'];
                        $fare_basis = @$flight ['fare_basis'];

                        $flight_detail_key = $flight ['flight_detail_key'];
                        $booking_counts = $flight ['booking_counts'];
                        $provider_code = $flight ['provider_code'];
                        $optional_services_indicator = $flight ['optional_services_indicator'];
                        $availability_source = (isset($flight ['availability_source'])) ? 'AvailabilitySource="' . $flight ['availability_source'] . '"' : '';
                        $air_code_share = isset($flight ['air_code_share']) && !empty($flight ['air_code_share']) ? '<CodeshareInfo>"' . @$flight ['air_code_share'] . '"</CodeshareInfo>' : '';
                        $class_of_service ='';
                        $status ='';
                        $apis_req_ref ='';
                        $suppliercode ='';
                        if ($provider_code == 'ACH') {
                            $class_of_service = isset($flight['booking_code']) && !empty($flight['booking_code']) ? 'ClassOfService="' . $flight['booking_code'] . '"' : '';        
                            $status = isset($flight['status']) && !empty($flight['status']) ? 'Status="' . $flight['status'] . '"' : '';     
                            $suppliercode = isset($flight['suppliercode']) && !empty($flight['suppliercode']) ? 'SupplierCode="' . $flight['suppliercode'] . '"' : '';        
                            $apis_req_ref = isset($flight['apis_req_ref']) && !empty($flight['apis_req_ref']) ? 'APISRequirementsRef="' . $flight['apis_req_ref'] . '"' : '';           
                        }
                        $host_tkn_ref = '';
                        if (isset($flight['host_token_key']) && !empty($flight['host_token_key'])) {
                            $host_token .= '<HostToken xmlns="http://www.travelport.com/schema/common_v41_0" Key="'.$flight['host_token_key'].'">'.$flight['host_token_value'].'</HostToken>';
                            $host_tkn_ref = 'HostTokenRef="' . $flight['host_token_key'] . '"';
                        }
                        if ($provider_code == '1G') { 
                            $segment = '<AirSegment  Key="' . $flight_seg_key . '" Group="' . $group . '" '.$host_tkn_ref.' Carrier="' . $carrier . '" FlightNumber="' . $flight_number . '" ProviderCode="' . $provider_code . '" Origin="' . $origin . '" Destination="' . $destination . '" DepartureTime="' . $departure_time . '" ArrivalTime="' . $arrival_time . '" FlightTime="' . $flight_time . '" ' . $distance . ' ' . $equipment . ' ChangeOfPlane="' . $change_of_plane . '" OptionalServicesIndicator="' . $optional_services_indicator . '" ' . $availability_source . ' ' . $participant_level . ' ' . $polled_availability_option . ' AvailabilityDisplayType="Fare Shop/Optimal Shop" ' . $e_ticketability . ' ' . $link_availability . '>
                            ' . $air_code_share . '
                            <AirAvailInfo ProviderCode="' . $provider_code . '">
                            <BookingCodeInfo BookingCounts="' . $booking_counts . '"></BookingCodeInfo>
                            </AirAvailInfo>
                            <FlightDetails ' . $equipment . ' Destination="' . $destination . '" Origin="' . $origin . '" Key="' . $flight_detail_key . '" FlightTime="' . $flight_time . '" ArrivalTime="' . $arrival_time . '" DepartureTime="' . $departure_time . '" ></FlightDetails>
                            </AirSegment>';
                         }
                         else{
                            $segment = '<AirSegment  Key="' . $flight_seg_key . '" '.$status.' '.$suppliercode.' '.$apis_req_ref.' Group="' . $group . '" '.$host_tkn_ref.' Carrier="' . $carrier . '" FlightNumber="' . $flight_number . '" ProviderCode="' . $provider_code . '" Origin="' . $origin . '" Destination="' . $destination . '" DepartureTime="' . $departure_time . '" ArrivalTime="' . $arrival_time . '" FlightTime="' . $flight_time . '" ' . $distance . ' ' . $equipment . ' ChangeOfPlane="' . $change_of_plane . '" OptionalServicesIndicator="' . $optional_services_indicator . '" ' . $availability_source . ' ' . $participant_level . ' ' . $polled_availability_option . $e_ticketability . ' ' . $link_availability . ' ' . $class_of_service . '>
                            ' . $air_code_share . '
                                <AirAvailInfo ProviderCode="' . $provider_code . '">
                                </AirAvailInfo>
                            </AirSegment>';
                         }
                        
                        $air_segment [] = $segment;
                        if ($provider_code == 'ACH') {
                            $AirSegmentPricingModifiers1 .= '<AirSegmentPricingModifiers FareBasisCode="'.$fare_basis.'" AirSegmentRef="'.$flight_seg_key.'"></AirSegmentPricingModifiers>';
                        }
                    }
                }
            }
        }
     
        $PermittedCabins = '<PermittedCabins>
                            <CabinClass xmlns="http://www.travelport.com/schema/common_v41_0" Type="'.$cabin_class.'"/>
                            </PermittedCabins>';
        $all_price_modfier ='';
        $form_of_payment ='';
        if ($provider_code == '1G') {
            $all_price_modfier = '<AirPricingModifiers FaresIndicator="AllFares">'.$PermittedCabins.'</AirPricingModifiers>';
            $form_of_payment = '<FormOfPayment xmlns="http://www.travelport.com/schema/common_v41_0"  Type="Cash"></FormOfPayment>';
        }
       
        $search_passenger = '<SearchPassenger Key="0" Code="ADT" xmlns="http://www.travelport.com/schema/common_v41_0" ></SearchPassenger>';
       
        $air_price_req = '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
            <s:Header>
                <Action s:mustUnderstand="1" xmlns="http://schemas.microsoft.com/ws/2005/05/addressing/none">http://192.168.0.25/horiizons/index.php/flight/lost</Action>
            </s:Header>
            <s:Body xmlns:xsi="http://www.w3.org/2001/xmlschema-instance" xmlns:xsd="http://www.w3.org/2001/xmlschema">
                <AirPriceReq TraceId="394d96c00971c4545315e49584609ff6" CheckOBFees="All" AuthorizedBy="user" TargetBranch="' . $this->config ['Target'] . '" xmlns="http://www.travelport.com/schema/air_v41_0" xmlns:common_v41_0="http://www.travelport.com/schema/common_v41_0">
                    <BillingPointOfSaleInfo OriginApplication="UAPI" xmlns="http://www.travelport.com/schema/common_v41_0"></BillingPointOfSaleInfo>
                    <AirItinerary>';
        $air_price_req_opt = '<AirPriceReq TraceId="394d96c00971c4545315e49584609ff6" CheckOBFees="All" AuthorizedBy="user" TargetBranch="' . $this->config ['Target'] . '" >
                    <BillingPointOfSaleInfo OriginApplication="UAPI" xmlns="http://www.travelport.com/schema/common_v41_0"></BillingPointOfSaleInfo>
                    <AirItinerary>';
        foreach ($air_segment as $as_key => $segmnt) {
            $air_price_req .= $segmnt;
            $air_price_req_opt.= $segmnt;
        }
        $air_price_req .=  $host_token .'
                     </AirItinerary>
                      ' . $all_price_modfier . '
                      ' . $adults . '
                      ' . $childs . '
                      ' . $infants . '
                    <AirPricingCommand xmlns="http://www.travelport.com/schema/air_v41_0" CabinClass="'.$cabin_class.'">' . $AirSegmentPricingModifiers1.'</AirPricingCommand>
                   ' . $form_of_payment . ' 
                </AirPriceReq>
            </s:Body>
        </s:Envelope>';
        $air_price_req_opt .= $host_token .'
                     </AirItinerary>
                      ' . $all_price_modfier . '
                     <AirPricingCommand CabinClass="'.$cabin_class.'">' . $AirSegmentPricingModifiers1.'</AirPricingCommand>
                   ' . $form_of_payment;
        $response ['status'] = SUCCESS_STATUS;
        $response ['request'] = $air_price_req;
        $response ['request_opt'] = $air_price_req_opt.'</AirPriceReq>';
        // debug($response);exit;
        return $response;
    }
    function pax_xml_for_fare_quote($pax_count,$pax_code,$paxId,$Age=''){
        $pax_xml = '';
        static $paxId = 1;
        if(!empty($Age)){
            $Age = 'Age="'.$Age.'"';
        }
         if ($pax_count != 0) {
            for ($i = 0; $i < $pax_count; $i ++) {
                $pax_xml .= '<SearchPassenger BookingTravelerRef="' . $paxId . '" Code="'.$pax_code.'" '.$Age.' xmlns="http://www.travelport.com/schema/common_v41_0" ></SearchPassenger>';
                $paxId ++;
            }
        }
        return $pax_xml;
    }

    /**
     * Extra Services
     * @param unknown_type $request
     */
    public function get_extra_services($request, $fare_quote_data, $search_id) {
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        $CI = &get_instance();
        $provider_code = $request['flight_list'][0]['flight_detail'][0]['provider_code'];
        //$provider_code = 'ACH';
        if($provider_code == 'ACH'){
            $fare_quote_data = json_decode($fare_quote_data[0], true);
            $ResultToken = unserialized_data($fare_quote_data['ResultToken']);
        }
       
        /* if($request['IsLCC'] != true ){//Extra Services Only fro LCC Airline
          $response ['message'] = 'Not Available'; // Message to be returned
          return $response;
          } */
        // $ssr_request = $this->seat_request($request, $search_id, $check_price_xml[0]['itinerary_xml']);
        // // debug($ssr_request);exit;
        // for ($i = 0; $i < count($ssr_request); $i++) {
        //     $response ['data']['ExtraServiceDetails']['Seat'][$i] = $ssr_request[$i]['airSeatMapDeatils']['NewRow'];
        // }
        // debug($response);exit;
        $response ['status'] = SUCCESS_STATUS;
        //$provider_code='1G';
        if($provider_code == '1G'){
            $response ['data']['ExtraServiceDetails']['MealPreference'] = $this->getMeals($request);
           if(!empty($this->seat_request($request))){
            $response ['data']['ExtraServiceDetails']['Seat'] = $this->seat_request($request);
           }
            
        }
        else{
            $response ['data']['ExtraServiceDetails']['Meals'] = $ResultToken['key']['Meals'];
            $response ['data']['ExtraServiceDetails']['Baggage'] = $ResultToken['key']['Baggage'];
            // if($check_meal_details['status'] == SUCCESS_STATUS ){
            //     $meal_details = $check_meal_details['data'][0]['meals_data'];
            //     $meal_details = json_decode(base64_decode($meal_details), true);
            //     $temp_meal = array_values($meal_details);
            //     $response ['data']['ExtraServiceDetails']['Meals'] = $temp_meal;
            // }
          //$response ['data']['ExtraServiceDetails']['Seat'] = $this->seat_request($request);

        }
        // debug($response);exit;
        return $response;
    }

    public function seat_request_old($request, $search_id, $itinerary_xml) {
        $arrResponse = Converter::createArray($itinerary_xml);
        // debug($arrResponse);exit;
        $seat_data = $arrResponse['AirItinerary']['AirSegment'];
        $seat_data_new = array();
        if (isset($seat_data[0])) {
            $seat_data_new = $seat_data;
        } else {
            $seat_data_new[0] = $seat_data;
        }
        $seat_xml = '';
        $seat_response1 = array();
        $provider = '1G';
        $seatResformated = array();
        //die;
        if (!(isset($seat_data_new[0]))) {
            $seat_data_new[0] = $seat_data_new;
        }
        // debug($seat_data_new);exit;
        $segmentCnt = 0;
        $journeyCnt = 0;
        for ($se = 0; $se < count($seat_data_new); $se++) {
            $pmyseat = $seat_data_new[$se]['@attributes'];
            //debug($pmyseat);die;
            $AirSegment_Key = $pmyseat['Key'];
            $Group = $pmyseat['Group'];
            $Carrier = @$pmyseat['Carrier'];
            $FlightNumber = @$pmyseat['FlightNumber'];
            $Origin = @$pmyseat['Origin'];
            $Destination = @$pmyseat['Destination'];
            $DepartureTime = @$pmyseat['DepartureTime'];
            $ArrivalTime = @$pmyseat['ArrivalTime'];
            $FlightTime = @$pmyseat['FlightTime'];
            $Distance = @$pmyseat['Distance'];
            $Equipment = @$pmyseat['Equipment'];
            $ChangeOfPlane = @$pmyseat['ChangeOfPlane'];
            $ClassOfService = @$pmyseat['ClassOfService'];
            $ParticipantLevel = @$pmyseat['ParticipantLevel'];
            $LinkAvailability = (@$pmyseat['LinkAvailability'] != "") ? @$pmyseat['LinkAvailability'] : "true";
            $PolledAvailabilityOption = @$pmyseat['PolledAvailabilityOption'];
            $OptionalServicesIndicator = @$pmyseat['OptionalServicesIndicator'];
            $AvailabilitySource = @$pmyseat['AvailabilitySource'];
            $AvailabilityDisplayType = @$pmyseat['AvailabilityDisplayType'];
            $seat_xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
                            <soapenv:Header />
                            <soapenv:Body>
                                <air:SeatMapReq TargetBranch="' . $this->config ['Target'] . '" TraceId="394d96c00971c4545315e49584609ff6" ReturnSeatPricing="true" AuthorizedBy="uAPI" xmlns:com="http://www.travelport.com/schema/common_v41_0" xmlns:univ="http://www.travelport.com/schema/universal_v41_0" xmlns:air="http://www.travelport.com/schema/air_v41_0">
                                    <BillingPointOfSaleInfo OriginApplication="UAPI" xmlns="http://www.travelport.com/schema/common_v41_0" />
                                    <air:AirSegment Key="' . $AirSegment_Key . '" Group="' . $Group . '" Carrier="' . $Carrier . '" FlightNumber="' . $FlightNumber . '" Origin="' . $Origin . '" Destination="' . $Destination . '" DepartureTime="' . $DepartureTime . '" ArrivalTime="' . $ArrivalTime . '" FlightTime="' . $FlightTime . '" Distance="' . $Distance . '" ETicketability="Yes" Equipment="' . $Equipment . '" ChangeOfPlane="' . $ChangeOfPlane . '" ClassOfService="' . $ClassOfService . '" ParticipantLevel="' . $ParticipantLevel . '" LinkAvailability="' . $LinkAvailability . '" PolledAvailabilityOption="' . $PolledAvailabilityOption . '" OptionalServicesIndicator="' . $OptionalServicesIndicator . '" AvailabilitySource="' . $AvailabilitySource . '" AvailabilityDisplayType="' . $AvailabilityDisplayType . '" ProviderCode="' . $provider . '">
                                        <air:AirAvailInfo ProviderCode="' . $provider . '" />
                                    </air:AirSegment>
                                </air:SeatMapReq>
                            </soapenv:Body>
                        </soapenv:Envelope>';
            //echo $seat_xml;exit;
            $seatRes[$se] = $this->process_request($seat_xml);
            // debug($seatRes);
            // $seatRes[$se] = file_get_contents('http://192.168.0.46/travelomatix_services/SeatMapRes.xml');
            // debug($seatRes[$se]);exit;

            $seatRes_arrar = Converter::createArray($seatRes[$se]);
            //debug($seatRes_arrar);die;
            if (isset($seatRes_arrar['SOAP:Body']['SOAP:Fault'])) {
                $seatResformated[$se] = $seatRes_arrar['SOAP:Body']['SOAP:Fault']['faultstring'];
            } else {
                $seatResformated[$se] = $this->seatFormat($seatRes_arrar, $segmentCnt, $journeyCnt, $search_id);
            }

            $segmentCnt++;
            //echo $strToLocation." == ".$Destination."<br/>";
            if ($strToLocation == $Destination) {
                $segmentCnt = 0;
                $journeyCnt = 1;
            }
            //debug($seatRes_arrar['SOAP:Body']['air:SeatMapRsp']);die;
        }
        // exit;
        return $seatResformated;
    }

    public function seatFormat_old($seat_res, $segmentCnt, $journeyCnt, $search_id) {
        
        $arrSeat_Data['search_data'][0] = $search_id;
        $arrSeat_Data['apiname'] = "Travelport";
        $arrSeat_Data['journey'] = $journeyCnt;
        $arrSeat_Data['segment'] = $segmentCnt;
        // debug($seat_res);exit();

        $arrSeatRows = array();
        $arrSeatPrice = array();
        if (isset($seat_res['SOAP:Envelope']['SOAP:Body']['air:SeatMapRsp']['air:OptionalServices'])) {
            $arrOptionalServices = $seat_res['SOAP:Envelope']['SOAP:Body']['air:SeatMapRsp']['air:OptionalServices'];
            if (isset($arrOptionalServices['air:OptionalService'])) {
                $arrOptSer = $arrOptionalServices['air:OptionalService'];
                if (isset($arrOptSer[0])) {
                    $arrOptSer1 = $arrOptSer;
                } else {
                    $arrOptSer1[0] = $arrOptSer;
                }
                // debug($arrOptSer1);exit;
                for ($opt = 0; $opt < count($arrOptSer1); $opt++) {
                    $arrOpt_services = $arrOptSer1[$opt]['@attributes'];
                    $strSeatKey = $arrOpt_services['Key'];
                    $arrOServ[$strSeatKey]['seat_currency'] = $current_currency_symbol;
                    $arrOServ[$strSeatKey]['base_price'] = substr($arrOpt_services['BasePrice'], 3);
                    $arrOServ[$strSeatKey]['tax_price'] = substr($arrOpt_services['Taxes'], 3);
                    $arrOServ[$strSeatKey]['total_price'] = substr($arrOpt_services['TotalPrice'], 3);
                    $arrSeatPrice[$strSeatKey] = $arrOServ[$strSeatKey];
                }
            }
        }
        if (isset($seat_res['SOAP:Envelope']['SOAP:Body']['air:SeatMapRsp']['air:AirSegment']['@attributes'])) {
            $flight_number = $seat_res['SOAP:Envelope']['SOAP:Body']['air:SeatMapRsp']['air:AirSegment']['@attributes']['FlightNumber'];
            $destination = $seat_res['SOAP:Envelope']['SOAP:Body']['air:SeatMapRsp']['air:AirSegment']['@attributes']['Destination'];
            $origion = $seat_res['SOAP:Envelope']['SOAP:Body']['air:SeatMapRsp']['air:AirSegment']['@attributes']['Origin'];
            $airline_code = $seat_res['SOAP:Envelope']['SOAP:Body']['air:SeatMapRsp']['air:AirSegment']['@attributes']['Carrier'];
        }
        // debug($seat_res);exit();
        if (isset($seat_res['SOAP:Envelope']['SOAP:Body']['air:SeatMapRsp']['air:Rows']['air:Row'])) {
            // echo 'herre';exit;
            $arrSeatData = $seat_res['SOAP:Envelope']['SOAP:Body']['air:SeatMapRsp']['air:Rows']['air:Row'];
            // echo count($arrSeatData);exit;
            $arrSeats = array();
            $arrSeatsNew = array();
            $seatNumbers = array();
            $columnChar = array();
            $for_location = array("Window", "Center seat (not window, not aisle)", "Center seat");
            $for_colmchar = array("Aisle", "Window", "Center seat (not window, not aisle)", "Center seat");
            $arrAsile = array();

            // $seats_new = array();
            for ($s = 0; $s < count($arrSeatData); $s++) {
                $strSeatNumber = $arrSeatData[$s]['@attributes']['Number'];
                $seatNumbers[] = $strSeatNumber;
                $arrSeatFacility = $arrSeatData[$s]['air:Facility'];
                $facility = array();
                $occupiedInd = array();
                $location = array();
                $inoperativeInd = array();
                $arrimitations = array();
                $premiumInd = array();
                $exitRowInd = array();
                $chargeableInd = array();
                $noInfantInd = array();
                $restrictInd = array();
                $seatAvailable = array();
                $seatType = array();
                $optServiceRef = array();
                //$seats_new = array();
                // debug($arrSeatFacility);exit;
                for ($f = 0; $f < count($arrSeatFacility); $f++) {
                    $fac_seatAttr = $arrSeatFacility[$f]['@attributes'];
                    //debug($fac_seatAttr);exit();
                    //echo $fac_seatAttr['Paid'];exit();
                    $strSeatCode = isset($fac_seatAttr['SeatCode']) ? $fac_seatAttr['SeatCode'] : "";
                    $strSeatType = "";
                    $strSeatAvail = "";
                    if ($strSeatCode != "") {
                        $strSeatType = $fac_seatAttr['Type'];
                        $strSeatAvail = $fac_seatAttr['Availability'];
                        $optServiceRef[] = isset($fac_seatAttr['OptionalServiceRef']) ? $fac_seatAttr['OptionalServiceRef'] : "";
                        $chargeableInd[] = isset($fac_seatAttr['Paid']) ? $fac_seatAttr['Paid'] : "false";
                    }
                    if ($s == 0) {
                        $arrAsile[] = ($fac_seatAttr['Type'] == "Seat") ? $fac_seatAttr['SeatCode'] : "Asile";
                    }

                    $arrAsileChar = $this->getSeatColChar($arrAsile);
                    // debug($arrAsileChar);
                    $seat_columns = $arrAsileChar['SeatChars'];

                    $seatType[] = $fac_seatAttr['Type'];
                    if ($strSeatType == "Seat") {
                        $arr_facility = explode("-", $strSeatCode);
                        if (isset($arr_facility[1])) {
                            $facility[] = $arr_facility[1];
                            $seatAvailable[] = $strSeatAvail;
                            if ($strSeatAvail == "Available") {
                                $occupiedInd[] = "false";
                            } else {
                                $occupiedInd[] = "true";
                            }
                            $inoperativeInd[] = "false";
                            $premiumInd[] = "false";
                            $exitRowInd[] = "false";
                            $noInfantInd[] = "false";
                            $restrictInd[] = "false";
                        }

                        if (isset($arrSeatFacility[$f]['air:Characteristic'])) {
                            $arrFaciltyChar = $arrSeatFacility[$f]['air:Characteristic'];
                            //debug($arrFaciltyChar);exit();
                            $strLocation = "";
                            $limitations = array();
                            for ($fc = 0; $fc < count($arrFaciltyChar); $fc++) {
                                $strFCVal = $arrFaciltyChar[$fc]['@attributes']['Value'];
                                if (in_array($strFCVal, $for_location)) {
                                    $strLocation = $strFCVal;
                                } else {
                                    if ($strFCVal != 'Asile') {
                                        $limitations[] = $strFCVal;
                                    }
                                }
                            }
                            $location[] = $strLocation;
                            $arrimitations[] = implode(",", $limitations);
                        } else {
                            $arrimitations[] = "";
                            $location[] = "";
                        }
                    } else {
                        
                    }
                }
                $arrSeatCharcter = $arrSeatData[$s]['air:Characteristic'];
                $seats[$s]['seatRowNumber'] = $strSeatNumber;
                $seats[$s]['seatType'] = $this->validateAsile($seatType);
                $seats[$s]['seatAvailable'] = $seatAvailable;
                $seats[$s]['seatColumn'] = $facility;
                $seats[$s]['serviceRef'] = $optServiceRef;
                $seats[$s]['Limitations'] = $arrimitations;
                $seats[$s]['Location'] = $location;
                $seats[$s]['occupiedInd'] = $occupiedInd;
                $seats[$s]['inoperativeInd'] = $inoperativeInd;
                $seats[$s]['premiumInd'] = $premiumInd;
                $seats[$s]['chargeableInd'] = $chargeableInd;
                $seats[$s]['exitRowInd'] = $exitRowInd;
                $seats[$s]['restrictedReclineInd'] = $restrictInd;
                $seats[$s]['noInfantInd'] = $noInfantInd;
                $seats[$s]['Facilities'] = $arrimitations;
                // debug($seat_columns);exit;
                $seats_new = array();
                $seat_columns_val = 0;
                foreach ($facility as $key1 => $value) {
                    $key = array();

                    $seats_new[$seat_columns_val]['FlightNumber'] = $flight_number;
                    $seats_new[$seat_columns_val]['Origin'] = $origion;
                    $seats_new[$seat_columns_val]['Destination'] = $destination;
                    $seats_new[$seat_columns_val]['AirlineCode'] = $airline_code;
                    $seats_new[$seat_columns_val]['RowNumber'] = $strSeatNumber;
                    $seats_new[$seat_columns_val]['SeatNumber'] = $strSeatNumber . $value;
                    if ($seatAvailable[$key1] == 'Available' || $seatAvailable[$key1] == 'Blocked') {
                        $seats_new[$seat_columns_val]['AvailablityType'] = 1;
                    } else {
                        $seats_new[$seat_columns_val]['AvailablityType'] = 0;
                    }
                    if ($chargeableInd[$key1] == 'true') {
                        $arrseg_key = $optServiceRef[$key1];

                        $seats_new[$seat_columns_val]['Price'] = $arrSeatPrice[$arrseg_key]['total_price'];
                    } else {
                        $seats_new[$seat_columns_val]['Price'] = 0;
                    }

                    $key ['key'][$seat_columns_val] = $seats_new;
                    $key ['key'][$seat_columns_val]['Type'] = 'dynamic';
                    // debug($key);
                    $seat_id = serialized_data($key['key']);
                    $seats_new[$seat_columns_val]['SeatId'] = '';
                    // unset($key);
                    $seat_columns_val++;
                }
                // debug($seats_new);
                $arrSeats[$s] = $seats[$s];
                $arrSeatsNew[$s] = $seats_new;
                //$arrSeatsNew = $seats_new;
            }
            
        }
        //exit();
        // $arrAsileChar = $this->getSeatColChar($arrAsile);
        $cabinData = array();
        $cabinData['firstRow'][0] = current($seatNumbers);
        $cabinData['lastRow'][0] = end($seatNumbers);
        $cabinData['classLocation'][0] = "";
        $cabinData['seatOccupationDefault'][0] = "";
        $cabinData['seatColumn'] = $arrAsileChar['SeatChars'];
        $cabinData['columnCharacteristic'] = $arrAsileChar['ColmChars'];
        $arrSeat_Data['airSeatMapDeatils']['cabin'] = $cabinData;
        $arrSeat_Data['airSeatMapDeatils']['row'] = $arrSeats;
        $arrSeat_Data['airSeatMapDeatils']['NewRow'] = $arrSeatsNew;
        $arrSeat_Data['airSeatMapDeatils']['seatprice'] = $arrSeatPrice;
        // debug($arrSeat_Data);exit();
        return $arrSeat_Data;
    }

    public function seat_request($request) {
      // debug($request);
        $seat_xml = '';
        $seat_response1 = array();
        $provider = '1G';
        $seatResformated = array();
        $flight_details = $request['flight_list'][0]['flight_detail'];
        $segmentCnt = 0;
        $journeyCnt = 0;
        for ($se = 0; $se < count($flight_details); $se++) {
            $AirSegment_Key = $flight_details[$se]['flight_seg_key'];
            $Group =  $flight_details[$se]['group'];
            $Carrier = @$flight_details[$se]['carrier'];
            $FlightNumber = @$flight_details[$se]['flight_number'];
            $Origin = @$flight_details[$se]['origin'];
            $Destination = @$flight_details[$se]['destination'];
            $FlightTime = @$flight_details[$se]['flight_time'];
            $DepartureTime = @$flight_details[$se]['departure_time'];
            $ArrivalTime = @$flight_details[$se]['arrival_time'];
            $Equipment = @$flight_details[$se]['equipment'];
            $ChangeOfPlane = @$flight_details[$se]['change_of_plane'];
            $ClassOfService =  @$flight_details[$se]['booking_code'];
            $Distance = @$flight_details[$se]['distance'];
            $ParticipantLevel = @$flight_details[$se]['participant_level'];
            $LinkAvailability = @$flight_details[$se]['link_availability'];
            $PolledAvailabilityOption = @$flight_details[$se]['polled_availability_option'];
            $AvailabilitySource = @$flight_details[$se]['availability_source'];
            $AvailabilityDisplayType = 'Fare Shop/Optimal Shop';
            $OptionalServicesIndicator = @$flight_details[$se]['optional_services_indicator'];
            // $pmyseat = $flight_details[$se]['@attributes'];
            // //debug($pmyseat);die;
            // $AirSegment_Key = $pmyseat['Key'];
            // $Group = $pmyseat['Group'];
            // $Carrier = @$pmyseat['Carrier'];
            // $FlightNumber = @$pmyseat['FlightNumber'];
            // $Origin = @$pmyseat['Origin'];
            // $Destination = @$pmyseat['Destination'];
            // $DepartureTime = @$pmyseat['DepartureTime'];
            // $ArrivalTime = @$pmyseat['ArrivalTime'];
            // $FlightTime = @$pmyseat['FlightTime'];
            // $Distance = @$pmyseat['Distance'];
            // $Equipment = @$pmyseat['Equipment'];
            // $ChangeOfPlane = @$pmyseat['ChangeOfPlane'];
            // $ClassOfService = @$pmyseat['ClassOfService'];
            // $ParticipantLevel = @$pmyseat['ParticipantLevel'];
            // $LinkAvailability = (@$pmyseat['LinkAvailability'] != "") ? @$pmyseat['LinkAvailability'] : "true";
            // $PolledAvailabilityOption = @$pmyseat['PolledAvailabilityOption'];
            // $OptionalServicesIndicator = @$pmyseat['OptionalServicesIndicator'];
            // $AvailabilitySource = @$pmyseat['AvailabilitySource'];
            // $AvailabilityDisplayType = @$pmyseat['AvailabilityDisplayType'];
            $seat_xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
                            <soapenv:Header />
                            <soapenv:Body>
                                <air:SeatMapReq TargetBranch="' . $this->config ['Target'] . '" TraceId="394d96c00971c4545315e49584609ff6" ReturnSeatPricing="false" AuthorizedBy="uAPI" xmlns:com="http://www.travelport.com/schema/common_v41_0" xmlns:univ="http://www.travelport.com/schema/universal_v41_0" xmlns:air="http://www.travelport.com/schema/air_v41_0">
                                    <BillingPointOfSaleInfo OriginApplication="UAPI" xmlns="http://www.travelport.com/schema/common_v41_0" />
                                    <air:AirSegment Key="' . $AirSegment_Key . '" Group="' . $Group . '" Carrier="' . $Carrier . '" FlightNumber="' . $FlightNumber . '" Origin="' . $Origin . '" Destination="' . $Destination . '" DepartureTime="' . $DepartureTime . '" ArrivalTime="' . $ArrivalTime . '" FlightTime="' . $FlightTime . '" Distance="' . $Distance . '" ETicketability="Yes" Equipment="' . $Equipment . '" ChangeOfPlane="' . $ChangeOfPlane . '" ClassOfService="' . $ClassOfService . '" ParticipantLevel="' . $ParticipantLevel . '" LinkAvailability="' . $LinkAvailability . '" PolledAvailabilityOption="' . $PolledAvailabilityOption . '" OptionalServicesIndicator="' . $OptionalServicesIndicator . '" AvailabilitySource="' . $AvailabilitySource . '" AvailabilityDisplayType="' . $AvailabilityDisplayType . '" ProviderCode="' . $provider . '">
                                        <air:AirAvailInfo ProviderCode="' . $provider . '" />
                                    </air:AirSegment>
                                </air:SeatMapReq>
                            </soapenv:Body>
                        </soapenv:Envelope>';
            // echo $seat_xml;exit;
             
            $seatRes[$se] = $this->process_request($seat_xml,'','Seating Request');
            $api_url = $this->config ['EndPointUrl'];
            $api_remarks = 'Seat Request(Travelport Flight)';
            $this->CI->api_model->store_api_request_booking($api_url, $seat_xml, $seatRes[$se], $api_remarks);
            // debug($seatRes);exit;
            //$seatRes[$se] = file_get_contents('http://192.168.0.46/travelomatix_services/SeatMapRes.xml');
            //$seatRes[$se] = file_get_contents(FCPATH."travelport_xmls/1GSeatmap_res.xml");
            $seatRes_arrar = Converter::createArray($seatRes[$se]);
            //debug($seatRes_arrar);die();
            if (isset($seatRes_arrar['SOAP:Body']['SOAP:Fault'])) {
                $seatResformated[$se] = $seatRes_arrar['SOAP:Body']['SOAP:Fault']['faultstring'];
            } else {
                $seatResformated[$se] = $this->seatFormat($seatRes_arrar, $segmentCnt, $journeyCnt, $search_id);
                
            }

            $segmentCnt++;
            //echo $strToLocation." == ".$Destination."<br/>";
            if ($strToLocation == $Destination) {
                $segmentCnt = 0;
                $journeyCnt = 1;
            }
            //debug($seatRes_arrar['SOAP:Body']['air:SeatMapRsp']);die;
        }
        // exit;
        //debug($seatResformated);die();
        return $seatResformated;
    }

    public function seatFormat($seat_res, $segmentCnt, $journeyCnt, $search_id) {
        // debug($seat_res);exit;
        error_reporting(0);

        // $arrSeat_Data['search_data'][0] = $search_id;
        // $arrSeat_Data['apiname'] = "Travelport";
        // $arrSeat_Data['journey'] = $journeyCnt;
        // $arrSeat_Data['segment'] = $segmentCnt;
        // debug($seat_res);exit();

        $arrSeatRows = array();
        $arrSeatPrice = array();
        
        if (isset($seat_res['SOAP:Envelope']['SOAP:Body']['air:SeatMapRsp']['air:AirSegment']['@attributes'])) {
            $flight_number = $seat_res['SOAP:Envelope']['SOAP:Body']['air:SeatMapRsp']['air:AirSegment']['@attributes']['FlightNumber'];
            $destination = $seat_res['SOAP:Envelope']['SOAP:Body']['air:SeatMapRsp']['air:AirSegment']['@attributes']['Destination'];
            $origion = $seat_res['SOAP:Envelope']['SOAP:Body']['air:SeatMapRsp']['air:AirSegment']['@attributes']['Origin'];
            $airline_code = $seat_res['SOAP:Envelope']['SOAP:Body']['air:SeatMapRsp']['air:AirSegment']['@attributes']['Carrier'];

            $airline_seg_key = $seat_res['SOAP:Envelope']['SOAP:Body']['air:SeatMapRsp']['air:AirSegment']['@attributes']['Key'];
        }
        //debug($flight_number);die();
        if (isset($seat_res['SOAP:Envelope']['SOAP:Body']['air:SeatMapRsp']['air:Rows']['air:Row'])) {
            // echo 'herre';exit;
            $arrSeatData = $seat_res['SOAP:Envelope']['SOAP:Body']['air:SeatMapRsp']['air:Rows']['air:Row'];
            
            $details=array();
            $seatCols=array();
            for ($s = 0; $s < count($arrSeatData); $s++) {
                $row_details=array();
                $air_facility=$arrSeatData[$s]['air:Facility'];
                foreach ($air_facility as $key => $value) {
                    if($value['@attributes']['Type']== "Seat"){
                        array_push($row_details,$value['@attributes']);    
                    }else if($value['@attributes']['Type']== "Aisle") {
                        array_push($row_details,array('seat_type'=>'Asile'));
                        foreach ($value['air:Facility'] as $k => $v) {
                            array_push($row_details,$v['@attributes']);  
                        }
                    }
                }


                
                $new_array=array();
                $cols=array();
                //debug($row_details);die();
                foreach ($row_details as $key => $value) {
                    //debug($value);
                    $seatcode = explode('-', $value['SeatCode']);
                    $availability=0;
                    if($value['Availability'] == "Available"){
                        $availability = 1;
                    }
                    $seat_id[0]['Type'] = 'dynamic'; 
                    $seat_id[0]['Code'] = $value['SeatCode']; 
                    $seat_id[0]['Origin'] = $origion; // need to change
                    $seat_id[0]['Destination'] = $destination; // need to change
                    $seat_id[0]['Airsegment'] = $airline_seg_key;
                    
                   if($value['seat_type'] != 'Asile'){
                        $new = array(
                            'FlightNumber' =>$flight_number,
                            'Origin' => $origion,
                            'Destination' => $destination,
                            'AirlineCode' => $airline_code,
                            'RowNumber' =>$seatcode[0],
                            'SeatNumber' =>$seatcode[0].$seatcode[1],
                            'AvailablityType'=>$availability,
                            'Price' => '0.00',
                            'SeatId' => base64_encode(serialize($seat_id)),
                        );
                        array_push($new_array,$new);
                        array_push($cols,$seatcode[1]);
                    }else{
                        array_push($new_array,array('type_asile'=>'Asile'));
                        array_push($cols,'Asile');
                    }
                    

                }

                $details[]=$new_array;
                $seatCols[]=$cols;
            }
        }
        
        /*$cabinData = array();
        $cabinData['firstRow'][0] = current($seatNumbers);
        $cabinData['lastRow'][0] = end($seatNumbers);
        $cabinData['classLocation'][0] = "";
        $cabinData['seatOccupationDefault'][0] = "";
        $cabinData['seatColumn'] = $arrAsileChar['SeatChars'];
        $cabinData['columnCharacteristic'] = $arrAsileChar['ColmChars'];
        $arrSeat_Data['airSeatMapDeatils']['cabin'] = $cabinData;
        $arrSeat_Data['airSeatMapDeatils']['row'] = $arrSeats;
        $arrSeat_Data['airSeatMapDeatils']['NewRow'] = $arrSeatsNew;
        $arrSeat_Data['airSeatMapDeatils']['seatprice'] = $arrSeatPrice;*/
         //debug($arrSeat_Data);exit();
       //debug($seatCols);die();
        //$cabinData['seatColumn'] = end($seatCols);
        //$cabinData['row_details'] = $details;

        array_push($details, array(end($seatCols)));
        return $details;
        //return $cabinData;
    }

    function validateAsile($seatType) {
        $arrNewSeatType = array();
        for ($s = 0; $s < count($seatType); $s++) {
            // $strSeatType = $seatType[$s].isset($seatType[$s+1])?($seatType[$s+1]=='Asile')?"-Asile":"":"";
            $strSeatType = $seatType[$s];
            if (isset($seatType[$s + 1])) {
                if ($seatType[$s + 1] == "Aisle") {
                    $strSeatType .= "_Aisle";
                }
            }
            if ($strSeatType != "Aisle") {
                $arrNewSeatType[] = $strSeatType;
            }
        }
        return $arrNewSeatType;
    }

    function getSeatColChar($arrAsile) {
        $arrSeatChars = array();
        $arrColmChars = array();
        $arrRetData = array();
        for ($a = 0; $a < count($arrAsile); $a++) {
            $strData = $arrAsile[$a];
            if ($strData != 'Asile') {
                $tempData = explode("-", $strData);
                $arrSeatChars[] = $tempData[1];
                $arrColmChars[] = "CenterSeat";
            } else {
                $arrSeatChars[] = "Asile";
                $arrColmChars[] = "Asile";
            }
        }
        $arrColmChars[0] = $arrColmChars[count($arrAsile) - 1] = "Window";
        $arrRetData['SeatChars'] = $arrSeatChars;
        $arrRetData['ColmChars'] = $arrColmChars;
        return $arrRetData;
    }

    function getMeals($request) {
        // debug($request);exit;
        $CI = &get_instance();
        $meals_list = $CI->db_cache_api->get_meals_travelport();
        $flight_details = $request['flight_list']['0']['flight_detail'];
        $meals_list_arr = array();

        $i = 0;
        foreach ($flight_details as $flight) {
            // debug($meals_list);exit;
            $meals_list_data = array();
            foreach ($meals_list as $j => $meals) {


                $meals_list_data1[0]['Code'] = $meals_list_data[$j]['Code'] = $meals['Code'];
                $meals_list_data1[0]['Description'] = $meals_list_data[$j]['Description'] = $meals['Description'];
                $meals_list_data[$j]['Origin'] = $flight['origin'];
                $meals_list_data[$j]['Destination'] = $flight['destination'];
                $meals_list_data1[0]['Type'] = 'static';
                // debug($meals_list_data1);
                $meals_list_data[$j]['MealId'] = base64_encode(serialize($meals_list_data1));
                unset($meals_list_data1[$j]);
            }


            $meals_list_arr[$i] = $meals_list_data;
            $i++;
        }
        return $meals_list_arr;
        // debug($meals_list_arr);exit;
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
        //debug($elgible_for_ticket_cancellation);exit;
        if ($elgible_for_ticket_cancellation['status'] == SUCCESS_STATUS) {
            $booking_details = $this->CI->flight_model->get_flight_booking_transaction_details($app_reference, $sequence_number, $this->booking_source);
            $booking_details = $booking_details['data'];
            $booking_transaction_details = $booking_details['booking_transaction_details'][0];
            $flight_booking_transaction_details_origin = $booking_transaction_details['origin'];

            $request_params = $booking_details;
            $request_params['passenger_origins'] = $ticket_ids;
            $request_params['IsFullBookingCancel'] = $IsFullBookingCancel;
            //debug($request_params);exit;
            //SendChange Request
            $send_change_request = $this->send_change_request($request_params);

            if ($send_change_request['status'] == SUCCESS_STATUS) {
                $response ['status'] = SUCCESS_STATUS;
                $response ['message'] = 'Cancellation Request is processing';
                $send_change_response = $send_change_request['data']['send_change_response'];
                // / debug($send_change_response);exit;
                //change
                $passenger_origin = $request_params['passenger_origins'];
                foreach ($passenger_origin as $origin) {
                    $this->CI->common_flight->update_ticket_cancel_status($app_reference, $sequence_number, $origin);
                }

            } else {
                $response ['message'] = $send_change_request['message'];
            }
        } else {
            $response ['message'] = $elgible_for_ticket_cancellation['message'];
        }
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

            $send_change_response = $this->process_request($send_change_request ['request'], $send_change_request ['url'],'SendChangeRequest(Travelport)');
            //$send_change_response = file_get_contents('http://192.168.0.46/travelomatix_services/cancelRes.xml');

            $send_change_response = Converter::createArray($send_change_response);
            // debug($send_change_response);exit;

            if (valid_array($send_change_response) == true && ($send_change_response['SOAP:Envelope']['SOAP:Body']['universal:UniversalRecordCancelRsp']['universal:ProviderReservationStatus']['@attributes']['Cancelled']) == true) {
                $response ['status'] = SUCCESS_STATUS;
                $response ['data']['send_change_response'] = $send_change_response;
            } else {
                $error_message = '';
                if (isset($send_change_response['SOAP:Envelope']['SOAP:Body']['SOAP:Fault']['faultstring'])) {
                    $error_message = $send_change_response['SOAP:Envelope']['SOAP:Body']['SOAP:Fault']['faultstring'];
                }
                if (empty($error_message) == true) {
                    $error_message = 'Cancellation Failed';
                }
                $response ['message'] = $error_message;
            }
        } else {
            $response ['status'] = FAILURE_STATUS;
        }
        //debug($response);exit;
        return $response;
    }

    /**
     * Forms the SendChangeRequest
     * @param unknown_type $request
     */
    private function format_send_change_request($params) {
        $booking_transaction_details = $params['booking_transaction_details'][0];
        $BookingId = trim($booking_transaction_details['book_id']);
        $pnr = trim($booking_transaction_details['pnr']);
        $booking_customer_details = $params['booking_customer_details'];
        $passenger_origins = $params['passenger_origins'];
        //debug($booking_transaction_details);exit;
        $request = array();
        if ($params['IsFullBookingCancel'] == true) {
            $RequestType = 1;
            $TicketIds = null;
            $Sectors = null;
        } else if ($params['IsFullBookingCancel'] == false) {
            $RequestType = 2;
            $Sectors = null;
            //Extract TicketId's
            //Indexing passenger origin with status
            $index_passenger_orign = array();
            foreach ($booking_customer_details as $pax_k => $pax_v) {
                $index_passenger_orign[$pax_v['origin']] = $pax_v;
            }
            $TicketIds = array();
            foreach ($passenger_origins as $k => $v) {
                $TicketIds[$k] = $index_passenger_orign[$v]['TicketId'];
            }
        }
        $CancelBookingReq = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:com="http://www.travelport.com/schema/common_v41_0" xmlns:univ="http://www.travelport.com/schema/universal_v41_0">
                            <soapenv:Header/>
                            <soapenv:Body>
                            <univ:UniversalRecordCancelReq AuthorizedBy="user" TraceId="394d96c00971c4545315e49584609ff6" TargetBranch="' . $this->config ['Target'] . '" UniversalRecordLocatorCode="' . $pnr . '" Version="1">
                            <com:BillingPointOfSaleInfo OriginApplication="UAPI"/>
                            </univ:UniversalRecordCancelReq>
                            </soapenv:Body>
                            </soapenv:Envelope>';
        //debug($CancelBookingReq);exit;
        $request ['request'] = $CancelBookingReq;
        $request ['url'] = 'https://americas.universal-api.pp.travelport.com/B2BGateway/connect/uAPI/UniversalRecordService';
        $request ['remarks'] = 'SendChangeRequest(Travelport)';
        $request ['status'] = SUCCESS_STATUS;
        return $request;
    }

    /**
     * check if the search response is valid or not
     * 
     * @param array $search_result
     *          search result response to be validated
     */
    function valid_search_result($search_result) {
        // debug($search_result);exit;
        if (valid_array($search_result) == true and isset($search_result[0] ['SOAP:Envelope'] ['SOAP:Body']) == true and isset($search_result [0]['SOAP:Envelope'] ['SOAP:Body'] ['air:LowFareSearchRsp']) == true) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * process soap API request
     * 
     * @param string $request           
     */
    function process_request($request, $url = '', $remarks) {
        if (!empty($url)) {
            $url = $url;
        } else {
            $url = $this->config ['EndPointUrl'];
        }

        $insert_id = $this->CI->api_model->store_api_request($url, $request, $remarks);
        $insert_id = intval(@$insert_id['insert_id']);
        $soapAction = '';
        $Authorization = base64_encode('Universal API/' . $this->config ['UserName'] . ':' . $this->config ['Password']);
        $httpHeader = array(
            "SOAPAction: {$soapAction}",
            "Content-Type: text/xml; charset=UTF-8",
            "Content-Encoding: UTF-8",
            "Authorization: Basic $Authorization",
            "Content-length: " . strlen($request),
            "Accept-Encoding: gzip,deflate"
        );
        $ch = curl_init();
       
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 180);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // sd
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
        $response = curl_exec($ch);
        //debug($response);exit;
        //Update the API Response
        $this->CI->api_model->update_api_response($response, $insert_id);
        $error = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
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
    
    
     # Hold Booking 
    function hold_ticket($booking_params, $app_reference, $sequence_number, $search_id) {

        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned

        $ticket_response = array();
        $book_response = array();

        $ResultToken = $booking_params['ResultToken'];
        //Format Booking Passenger Data
        //$booking_params['Passengers'] = $this->format_booking_passenger_object($booking_params);
        //Run Book Method
        $book_service_response = $this->run_book_service($booking_params, $app_reference, $sequence_number, $search_id);
        //debug($book_service_response);exit;
        if ($book_service_response['status'] == SUCCESS_STATUS) {
            $response ['status'] = SUCCESS_STATUS;
            $book_response = $book_service_response['data']['book_response'];
            //debug($book_response);exit;
            //Save BookFlight Details
            $this->save_book_response_details($booking_params, $book_response, $app_reference, $sequence_number, $search_id, '1G');

            //Run Non-LCC Ticket method
            $ticket_request_params = array();
            $ticket_request_params['PNR'] = $book_response['universal:AirCreateReservationRsp']['universal:UniversalRecord']['@attributes']['LocatorCode'];
            $ticket_request_params['BookingId'] = $book_response['universal:AirCreateReservationRsp']['universal:UniversalRecord']['air:AirReservation']['common_v41_0:SupplierLocator']['@attributes']['SupplierLocatorCode'];
            $ticket_request_params['TraceId'] = $book_response['universal:AirCreateReservationRsp']['@attributes']['TraceId'];
            //$ticket_service_response = $this->run_non_lcc_ticket_service($ticket_request_params, $app_reference, $sequence_number);
            $flight_booking_status = 'BOOKING_HOLD';
            $this->CI->common_flight->update_flight_booking_status($flight_booking_status, $app_reference, $sequence_number, $this->booking_source);

        } else {
            $response ['message'] = $book_service_response['message'];
            $flight_booking_status = 'BOOKING_FAILED';
            $this->CI->common_flight->update_flight_booking_status($flight_booking_status, $app_reference, $sequence_number, $this->booking_source);
        }
       
        return $response;
    }

    function process_booking($booking_params, $app_reference, $sequence_number, $search_id) {
    
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned

        $ticket_response = array();
        $book_response = array();
        $price_recheck_xml = array();
        $ResultToken = $booking_params['ResultToken'];
       // $provider_code = 'ACH';
        //Only for ACH flights need to check price recheck if meal or baggage selected
        if($provider_code == 'ACH'){
            $price_recheck_response = $this->price_recheck($booking_params);
            if($price_recheck_response['status'] == 1){
                if(valid_array($price_recheck_response['data'])){
                    $price_recheck_xml = $price_recheck_response['data'];
                }
            }
           
        }
       
        //Run Book Method
        $book_service_response = $this->run_book_service($booking_params, $app_reference, $sequence_number, $price_recheck_xml, $search_id,'');

        $ticketing_status = '';
         
        if ($book_service_response['status'] == SUCCESS_STATUS) {
            $response ['status'] = SUCCESS_STATUS;
            $book_response = $book_service_response['data']['book_response'];
            //Save BookFlight Details
            $provider_code = $booking_params['ResultToken']['flight_list'][0]['flight_detail'][0]['provider_code'];
            
             //Run Non-LCC Ticket method
            
            $flights_data = $booking_params['ResultToken']['flight_list'][0]['flight_detail'];
            $ticketing_status = "Tikcet";
            $this->save_book_response_details($booking_params, $book_response, $app_reference, $sequence_number, $search_id, $provider_code);
            if ($ticketing_status == "Tikcet") {
                $ticket_request_params = array();
                $ticket_request_params['PNR'] = $book_response['universal:AirCreateReservationRsp']['universal:UniversalRecord']['@attributes']['LocatorCode'];
                $ticket_request_params['AirReservationLocatoreCode'] = $book_response['universal:AirCreateReservationRsp']['universal:UniversalRecord']['air:AirReservation']['@attributes']['LocatorCode'];
                $ticket_request_params['BookingId'] = $book_response['universal:AirCreateReservationRsp']['universal:UniversalRecord']['air:AirReservation']['common_v41_0:SupplierLocator']['@attributes']['SupplierLocatorCode'];
                $ticket_request_params['TraceId'] = $book_response['universal:AirCreateReservationRsp']['@attributes']['TraceId'];
                $ticket_service_response = $this->run_non_lcc_ticket_service($ticket_request_params, $app_reference, $sequence_number);
                // debug($ticket_service_response);exit;
                if ($ticket_service_response['status'] == SUCCESS_STATUS) {
                    $ticket_response = $ticket_service_response['data']['ticket_response'];
                    $flight_booking_status = 'BOOKING_CONFIRMED';
                    $this->CI->common_flight->update_flight_booking_status($flight_booking_status, $app_reference, $sequence_number, $this->booking_source);
                }
            }

        } else {
            $response ['message'] = $book_service_response['message'];
            $flight_booking_status = 'BOOKING_FAILED';
            $this->CI->common_flight->update_flight_booking_status($flight_booking_status, $app_reference, $sequence_number, $this->booking_source);
        }
        if (valid_array($ticket_response) == true || valid_array($book_response) == true) {
            if (valid_array($ticket_response) == true) {
                $this->save_flight_ticket_details($booking_params, $book_response, $ticket_response, $app_reference, $sequence_number, $search_id);
            }
        }
       
        return $response;
    }

    private function run_book_service($booking_params, $app_reference, $sequence_number, $pricerec_check_xml, $search_id, $ach_status) {
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        $book_service_request = $this->run_book_service_request($booking_params, $pricerec_check_xml, $search_id, $ach_status);

        if ($book_service_request['status'] == SUCCESS_STATUS) {
             // debug($book_service_request ['request']);exit;
            $book_service_response = $this->process_request($book_service_request ['request'],'','flight_booking(Travelport Flight)');
            // debug($book_service_response);exit;
            $book_service_response = Converter::createArray($book_service_response);
            debug('Travelport Library 2719');
             debug($book_service_response);
        exit;
            if (valid_array($book_service_response) == true && isset($book_service_response['SOAP:Envelope']['SOAP:Body']['universal:AirCreateReservationRsp']['universal:UniversalRecord']['@attributes']['LocatorCode']) == true) {
                $response ['status'] = SUCCESS_STATUS;
                $response ['data']['book_response'] = $book_service_response['SOAP:Envelope']['SOAP:Body'];
            } else {
                $error_message = '';
                if (isset($book_service_response['SOAP:Envelope']['SOAP:Body']['SOAP:Fault']['faultcode']['faultstring'])) {
                    $error_message = $book_service_response['SOAP:Envelope']['SOAP:Body']['SOAP:Fault']['faultcode']['faultstring'];
                }
                if (empty($error_message) == true) {
                    $error_message = 'Booking Failed';
                    $response ['data']['book_response'] = $book_service_response;
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

    /*price recheck for ACH flights*/
    function price_recheck($params){
        // debug($params);exit;
        $response ['status'] = SUCCESS_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        $meal_xml ='';
        $baggage_xml ='';
        $passengers = $params['Passengers'];
        $price_xml = '';
        foreach($passengers as $pass_key => $passenger){
            if(isset($passenger['MealId'])){
                $MealsDetails = $passenger['MealId'];
                $meal_code_array = array();
                if(valid_array($MealsDetails) && !empty($MealsDetails)){
                    $MealsDetails = $this->meal_request_details($MealsDetails);
                    foreach ($params['ResultToken']['all_data']['all_meal_data'] as $mout_key => $mout_value ) {
                        foreach($mout_value[$pass_key] as $m_key => $m_value){
                            $meal_data = unserialized_data($m_value['MealId']);
                            $price_xml = $meal_data[0]['PriceXML'];
                            if(!in_array($meal_data[0]['Code'], $meal_code_array)){
                                if(isset($MealsDetails[$mout_key]['Description'])){
                                    $description = $MealsDetails[$mout_key]['Description'];
                                    if($description == $meal_data[0]['Description']){
                                       $meal_xml .= $meal_data[0]['MealXML'];
                                       array_push($meal_code_array, $meal_data[0]['Code']);  
                                    }
                                }
                               
                            }
                        }
                    }
                }
            }
            if(isset($passenger['BaggageId'])){
                $BaggageDetails = $passenger['BaggageId'];
                $baggage_code_array = array();
                if(valid_array($BaggageDetails) && !empty($BaggageDetails)){
                    $BaggageDetails = $this->baggage_request_details($BaggageDetails);
                    foreach ($params['ResultToken']['all_data']['all_baggage_data'] as $bout_key => $bout_value ) {
                        
                        foreach(@$bout_value[$pass_key] as $b_key => $b_value){
                            $baggage_data = unserialized_data($b_value['BaggageId']);
                            $price_xml = $baggage_data[0]['PriceXML'];
                            // debug($meal_data);exit;
                            if(!in_array($baggage_data[0]['Code'], $baggage_code_array)){
                                if(isset($BaggageDetails[$bout_key]['Description'])){
                                    $description = $BaggageDetails[$bout_key]['Description'];
                                    if($description == $baggage_data[0]['Description']){
                                       $baggage_xml .= $baggage_data[0]['BaggageXML'];
                                       array_push($baggage_code_array, $baggage_data[0]['Code']);  
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if(empty($meal_xml) == false || empty($baggage_xml) == false){
            $extra_service_xml ='<OptionalServices>'.$meal_xml.$baggage_xml.'</OptionalServices>';
            $doc = new DOMDocument();
            $doc->loadXML($extra_service_xml);
            $xp = new DOMXPath($doc);
            foreach ($xp->query('//OptionalService') as $key => $tn) {
                $tn->removeAttribute('OptionalServicesRuleRef');
            }
            foreach ($xp->query('//ServiceData') as $key => $tn) {
                $tn->setAttribute('xmlns','http://www.travelport.com/schema/common_v41_0');
            }
            foreach ($xp->query('//ServiceInfo') as $key => $tn) {
                $tn->setAttribute('xmlns','http://www.travelport.com/schema/common_v41_0');
            }
            $extra_service_xml = str_replace('<?xml version="1.0"?>', '', $doc->saveXML());
            $xml = $price_xml.$extra_service_xml;
            $api_request = '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
                    <s:Header>
                        <Action s:mustUnderstand="1" xmlns="http://schemas.microsoft.com/ws/2005/05/addressing/none">http://192.168.0.25/horiizons/index.php/flight/lost</Action>
                    </s:Header>
                    <s:Body xmlns:xsi="http://www.w3.org/2001/xmlschema-instance" xmlns:xsd="http://www.w3.org/2001/xmlschema">'.$xml.'</AirPriceReq></s:Body></s:Envelope>';
           
            $check_price_res = $this->process_request($api_request, '','PriceRecheck(Travelport Flight ACH)');
            // $check_price_res = $this->CI->db->query("select response from provab_api_response_history where origin=1724")->result_array()[0]['response'];
            $check_price_res = Converter::createArray($check_price_res);
            if(!isset($check_price_res['SOAP:Envelope']['SOAP:Body']['SOAP:Fault'])){
                //Air pricing solution xml
                $airpricing_solution = $check_price_res['SOAP:Envelope']['SOAP:Body']['air:AirPriceRsp']['air:AirPriceResult']['air:AirPricingSolution'];  
                $xml = ArrayToXML::createXML('AirPricingSolution', $airpricing_solution);
                $air_price_xml = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $xml->saveXML());
                $air_price_xml = str_replace('common_v41_0:', '', $air_price_xml);
                $air_price_xml = str_replace('air:', '', $air_price_xml);
            
                $doc = new DOMDocument();
                $doc->loadXML($air_price_xml);
                $domds = $doc;
                $AirSegmentRef = $domds->getElementsByTagName("AirSegmentRef");
                while ($AirSegmentRef->length > 0) {
                    $node = $AirSegmentRef->item(0);
                    $this->remove_node($node);
                }
                $air_price_xml = str_replace('<?xml version="1.0"?>', '', $doc->saveXML());

                //Air Itinerary xml
                $air_itinerary = $check_price_res['SOAP:Envelope']['SOAP:Body']['air:AirPriceRsp']['air:AirItinerary'];  
                $xml = ArrayToXML::createXML('AirItinerary', $air_itinerary);
                $air_itinerary_xml = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $xml->saveXML());
                $air_itinerary_xml = str_replace('common_v41_0:', '', $air_itinerary_xml);
                $air_itinerary_xml = str_replace('air:', '', $air_itinerary_xml);

                //Opt Service Total
                $opt_service = $check_price_res['SOAP:Envelope']['SOAP:Body']['air:AirPriceRsp']['air:AirPriceResult']['air:AirPricingSolution']['air:OptionalServices']['air:OptionalServicesTotal'];
                
                $xml = ArrayToXML::createXML('OptionalServicesTotal', $opt_service);
                
                $opt_service_xml = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $xml->saveXML());
                $opt_service_xml = str_replace('common_v41_0:', '', $opt_service_xml);
                $opt_service_xml = str_replace('air:', '', $opt_service_xml);
                
                $opt_services_list = $check_price_res['SOAP:Envelope']['SOAP:Body']['air:AirPriceRsp']['air:AirPriceResult']['air:AirPricingSolution']['air:OptionalServices']['air:OptionalService'];
                $opt_service_list_xml1 ='';
                foreach($opt_services_list as $opt_service_data){
                    if($opt_service_data['@attributes']['ServiceStatus'] == 'Priced' && ($opt_service_data['@attributes']['Type'] == 'MealOrBeverage' || $opt_service_data['@attributes']['Type'] == 'Baggage')){
                        $xml = ArrayToXML::createXML('OptionalService', $opt_service_data);
                        $opt_service_list_xml = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $xml->saveXML());
                        $opt_service_list_xml = str_replace('common_v41_0:', '', $opt_service_list_xml);
                        $opt_service_list_xml1 .= str_replace('air:', '', $opt_service_list_xml);
                    }
                 
                }
                $meal_xml = '<OptionalServices>'.$opt_service_xml.$opt_service_list_xml1.'</OptionalServices>';
                
                $doc = new DOMDocument();
                $doc->loadXML($meal_xml);
                $xp = new DOMXPath($doc);
               
                foreach ($xp->query('//ServiceData') as $key => $tn) {
                    $tn->setAttribute('xmlns','http://www.travelport.com/schema/common_v41_0');
                }
                foreach ($xp->query('//ServiceInfo') as $key => $tn) {
                    $tn->setAttribute('xmlns','http://www.travelport.com/schema/common_v41_0');
                }
                $meal_xml = str_replace('<?xml version="1.0"?>', '', $doc->saveXML());
                $response['data']['meal_xml'] = $meal_xml;
                $response['data']['air_price_xml'] = $air_price_xml;
                $response['data']['itinerary_xml'] = $air_itinerary_xml;
                $response ['status'] = SUCCESS_STATUS;
            }
            else{
                $response ['status'] = FAILURE_STATUS;
            }
        }
        return $response;
    }
    /**
     * Forms the Book request
     * @param unknown_type $request
     */
    private function run_book_service_request($params, $price_recheck_xml, $search_id, $ach_status) {
        $provider = $params['ResultToken']['flight_list'][0]['flight_detail'][0]['provider_code'];
        $provider = '1G';
        $carrier = $params['flight_data']['FlightDetails']['Details'][0][0]['OperatorCode'];
        $segmentdata = $params['flight_data']['FlightDetails']['Details'][0];
        $arrAllOrigins = array();
        $arrAllDest = array();
        for ($s = 1; $s < count($segmentdata) - 2; $s++) {
            $arrAllOrigins[] = $segmentdata[$s]['origin'];
            $arrAllDest[] = $segmentdata[$s]['destination'];
        }
       
        $CI = &get_instance();
        if(valid_array($price_recheck_xml)){
            $AirPricingSolution_xml = $price_recheck_xml['air_price_xml'];
            $AirItinerary_xml = $price_recheck_xml['itinerary_xml'];
            $meal_xml = $price_recheck_xml['meal_xml'];
        }
        else{
            $meal_xml ='';
            $AirPricingSolution_xml = $params['ResultToken']['price_xml'];
            $AirItinerary_xml = $params['ResultToken']['itinerary_xml'];
        }
        
        //   debug($AirItinerary_xml);exit;
        $AirItinerary_xml = str_replace("common_v41_0:", '', $AirItinerary_xml);
        $AirPricingSolution_xml = $AirPricingSolution_xml1 = str_replace('common_v41_0:', '', $AirPricingSolution_xml);

        $doc = new DOMDocument();
        $doc->loadXML($AirPricingSolution_xml);

        $domds = $doc;

        $OptionalServices = $domds->getElementsByTagName("OptionalServices");
        while ($OptionalServices->length > 0) {
            $node = $OptionalServices->item(0);
            $this->remove_node($node);
        }

        $TaxDetail = $domds->getElementsByTagName("TaxDetail");
        while ($TaxDetail->length > 0) {
            $node = $TaxDetail->item(0);
            $this->remove_node($node);
        }

        $arrItinaryXML = Converter::createArray($AirItinerary_xml);
       
        $arrAirSegment = $arrItinaryXML['AirItinerary']['AirSegment'];
        if (isset($arrAirSegment[0])) {
            $arrAirSegment1 = $arrAirSegment;
        } else {
            $arrAirSegment1[0] = $arrAirSegment;
        }

        $arrAirseg = array();
        $segcnt = 0;
        $concnt = 0;
        for ($air = 0; $air < count($arrAirSegment1); $air++) {
            $strOrigin = $arrAirSegment1[$air]['@attributes']['Origin'];
            if (in_array($strOrigin, $arrAllOrigins)) {
                $segcnt++;
                $concnt = 0;
            }
            $segCntData = $segcnt . "_" . $concnt;
            $arrAirseg[$segCntData] = $arrAirSegment1[$air]['@attributes']['Key'];
            $concnt++;
        }
        // debug($arrAirseg);exit;
        $HostToken = $doc->getElementsByTagName("FareNote");
        while ($HostToken->length > 0) {
            $node = $HostToken->item(0);
            $this->remove_node($node);
        }
        if(valid_array($price_recheck_xml)){
            $AirPricingSolution_xml = str_replace('<?xml version="1.0"?>', '', $doc->saveXML());
            $AirPricingSolution_xml = str_replace('</AirPricingSolution>', '', $AirPricingSolution_xml);
            $AirPricingSolution_xml = $AirPricingSolution_xml.$meal_xml.'</AirPricingSolution>';
        }
        $AirPricingSolution_xml = $doc->saveXML();

        $doc->loadXML($AirPricingSolution_xml);
        
        $array_sr = array('<AirItinerary>', '</AirItinerary>', '<?xml version="1.0"?>');
        $AirItinerary_xml = str_replace($array_sr, '', $AirItinerary_xml);
        $fragment = $doc->createDocumentFragment();
        $fragment->appendXML($AirItinerary_xml);

        $AirPricingInfo = $doc->getElementsByTagName("AirPricingInfo")->item(0);
        $AirPricingInfo->removeAttribute('PlatingCarrier');
       // debug($AirPricingInfo);exit;
        $doc->documentElement->insertBefore($fragment, $AirPricingInfo);
        $xp = new DOMXPath($doc);
        foreach ($xp->query('//Endorsement') as $key => $tn) {
            $tn->parentNode->removeChild($tn);
        }
        foreach ($xp->query('//APISRequirements') as $key => $tn) {
            $tn->parentNode->removeChild($tn);
        }
      
        foreach ($xp->query('//TaxInfoRef') as $key => $tn) {
             $tn->setAttribute('xmlns','http://www.travelport.com/schema/common_v41_0');
        }
        if($provider == 'ACH'){
            $tag_length = $xp->evaluate('/AirPricingSolution/HostToken')->length;
            if($tag_length > 0){
                $tag_length = $tag_length/2;
            }
            foreach ($xp->evaluate('/AirPricingSolution/HostToken') as $key => $tn) {
                if($tag_length > 0){
                    if($key < $tag_length){
                        $tn->parentNode->removeChild($tn);
                    }
                }
                else{
                   if($key == 0){
                        $tn->parentNode->removeChild($tn);
                    } 
                }
            }
        }
       
        // Adding BookingTravelerRef for Passengers
        $PassengerType = $xp->evaluate("/AirPricingSolution/AirPricingInfo//PassengerType");
        for ($i = 0; $i < $PassengerType->length; $i++) {
            $Passenger = $PassengerType->item($i);
            $Passenger->setAttribute("BookingTravelerRef", $i);
        }
        
        $AirPricingSolution_xml = str_replace('<?xml version="1.0"?>', '', $doc->saveXML());
        // debug($AirPricingSolution_xml);exit;
        $arrPriceXML = Converter::createArray($AirPricingSolution_xml1);
        $arrPriceKey = $arrPriceXML['AirPricingSolution']['@attributes']['Key'];
        
        $address = '';
        $Payment_address = '';
        $Payment_address = '<AddressName>' . $params['Passengers'][0]['City'] . ' , ' . $params['Passengers'][0]['CountryName'] . '</AddressName>
                        <Street>' . $params['Passengers'][0]['PinCode'] . '</Street>
                        <City>' . $params['Passengers'][0]['City'] . '</City>
                        <PostalCode>' . $params['Passengers'][0]['PinCode'] . '</PostalCode>
                        <Country>' . $params['Passengers'][0]['CountryCode'] . '</Country>';

        $address .= '<Address>' . $Payment_address . '</Address>';

        $paxId = 0;
        $prefix = '';
        $i = 0;
        
        $air_line_pcode = $params['flight_data']['FlightDetails']['Details'][0][0]['OperatorCode'];

        $pax_arr = array();
        foreach ($params['Passengers'] as $passenger) {
            $pax_data_xml = '';
            if($provider == '1G'){
                if (isset($passenger['MealId']) == true && valid_array($passenger['MealId']) == true) {
                    $MealsDetails = $passenger['MealId'];
                } else {
                    $MealsDetails = array();
                }
                $MealsDetails = $this->meal_request_details($MealsDetails);
            }
            $prefix = 'Prefix="' . (isset($passenger['Title']) ? $passenger['Title'] : '') . '"';
            if ($passenger['Gender'] == '1') {
                $gender = 'F';
            } else {
                $gender = 'M';
            }
            if ($i == 0) {
                $address = $address;
            } 
            else{
                if($provider == 'ACH'){
                    $address ='<Address />';
                }
                else{
                    $address ='';
                }
            }
            if($ach_status == 'hold'){
                $email = 'anitha.g.provab@gmail.com';
            }
            else{
                $email = $passenger['Email'];
            }
            $type = '';
            $NameRemark = '';
            if($passenger['PaxType'] == 1){
                $type = 'ADT';
            }else if ($passenger['PaxType'] == 2) {
                if($provider == '1G'){
                    $type = 'CNN';
                }
                else{
                    $type = 'CHD';
                }
                $NameRemark = '<NameRemark Category="AIR">
                                    <RemarkData>P-C10</RemarkData>
                                </NameRemark>';
            }else if($passenger['PaxType'] == 3){
                $type = 'INF';
                $NameRemark = '<NameRemark Category="AIR">
                                    <RemarkData>' . $passenger['DateOfBirth'] . '</RemarkData>
                                </NameRemark>';
            }  
            $Passenger_PassportNationality = $passenger['CountryCode'];
            $adult_passport_number = $passenger['PassportNumber'];
            $Passenger_PassportIssuedBy = $passenger['CountryCode'];
            $Passenger_DOBp = date('dMy', strtotime($passenger['DateOfBirth']));
            $Passenger_Genderp = $gender;
            $Passenger_DOEp = date('dMy', strtotime($passenger['PassportExpiry']));
            $Passenger_Firstnamep = $passenger['FirstName'];
            $Passenger_Lastnamep = $passenger['LastName'];
            $pax_data_xml .= '<BookingTraveler Key="' . $paxId . '" TravelerType="'.$type.'" DOB="' . $passenger['DateOfBirth'] . '" Gender="' . $gender . '" xmlns="http://www.travelport.com/schema/common_v41_0">
            <BookingTravelerName ' . $prefix . ' First="' . $passenger['FirstName'] . '"   Last="' . $passenger['LastName'] . '" ></BookingTravelerName>
            <PhoneNumber Number="' . $passenger['ContactNo'] . '" Type="Mobile" ></PhoneNumber>
            <Email EmailID="' . $email . '" Type="P" ></Email>';
            if ($passenger['PassportNumber'] != "" && $passenger['PassportExpiry'] != "") {
                $pax_data_xml .= '<SSR Carrier="' . $air_line_pcode . '" FreeText="P/' . $Passenger_PassportNationality . '/' . $adult_passport_number . '/' . $Passenger_PassportIssuedBy . '/' . $Passenger_DOBp . '/' . $Passenger_Genderp . '/' . $Passenger_DOEp . '/' . $Passenger_Lastnamep . '/' . $Passenger_Firstnamep . '" Status="HK" Type="DOCS"></SSR>';
            }
            if($provider == '1G'){
                if (isset($MealsDetails) && valid_array($MealsDetails)) {

                    $k = 0;
                    foreach ($arrAirseg as $AirKey => $AirVal) {
                        $pax_data_xml .= '<SSR Carrier="' . $params['FlightDetails']['Details'][0][$k]['OperatorCode'] . '" Status="HK" SegmentRef="' . $AirVal . '" Type="' . $MealsDetails[$k]['Code'] . '"></SSR>';
                        $k++;
                    }
                }
            }
            $pax_data_xml .= $NameRemark.$address . ' 
                    </BookingTraveler>';
            $pax_arr[$type] .= $pax_data_xml;
            $paxId++;
            $i++;
        }

        if ($provider == "ACH") {
           if($ach_status == 'hold'){
                $payment = '';
            }
            else{
                if($carrier == '6E'){
                    $payment = '<FormOfPayment Type="Credit" xmlns="http://www.travelport.com/schema/common_v41_0" Key="446408">
                    <CreditCard BankCountryCode="US" CVV="123" ExpDate="2025-09" Name="TEST TRAVELPORT" Number="5200000000000007" Type="MC">
                        <BillingAddress>
                            <AddressName>Home</AddressName>
                            <Street>2914 N. Dakota Avenue</Street>
                            <City>Denver</City>
                            <State>CO</State>
                            <PostalCode>80206</PostalCode>
                            <Country>US</Country>
                        </BillingAddress>
                    </CreditCard>
                </FormOfPayment>';
                }
                else if($carrier == 'I5' || $carrier == 'AK' || $carrier == 'FD'){
                    $payment = '<FormOfPayment Type="AgencyPayment" xmlns="http://www.travelport.com/schema/common_v41_0">
                            <AgencyPayment AgencyBillingIdentifier="1120INASPN" AgencyBillingPassword="Accen$2020@" />
                            </FormOfPayment>';
                }
            }
        }
        else{
            $payment = '<FormOfPayment xmlns="http://www.travelport.com/schema/common_v41_0" Key="' . $arrPriceKey . '" Type="Cash" />';
        }
        $ActionStatus = '<ActionStatus ProviderCode="' . $provider . '" TicketDate="T*" Type="ACTIVE" QueueCategory="01" xmlns="http://www.travelport.com/schema/common_v41_0" ></ActionStatus>';

        $AirCreateReservationReq = '<?xml version="1.0" encoding="utf-8"?>
        <s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
                        <s:Header/>
                        <s:Body>
                            <univ:AirCreateReservationReq xmlns:air="http://www.travelport.com/schema/air_v41_0"  xmlns:common_v41_0="http://www.travelport.com/schema/common_v41_0" xmlns:univ="http://www.travelport.com/schema/universal_v41_0" AuthorizedBy="user" TraceId="394d96c00971c4545315e49584609ff6" TargetBranch="' . $this->config ['Target'] . '" RetainReservation="Both">
                            <BillingPointOfSaleInfo  xmlns="http://www.travelport.com/schema/common_v41_0" OriginApplication="UAPI" ></BillingPointOfSaleInfo>
                            ' ;
                            if(isset($pax_arr['ADT'])){
                                $AirCreateReservationReq .= $pax_arr['ADT'];
                            } if(isset($pax_arr['INF'])){
                                $AirCreateReservationReq .= $pax_arr['INF'];
                            }if(isset($pax_arr['CNN'])){
                                $AirCreateReservationReq .= $pax_arr['CNN'];
                            }if(isset($pax_arr['CHD'])){
                                $AirCreateReservationReq .= $pax_arr['CHD'];
                            }
                            $AirCreateReservationReq .= 
                                $payment . '
                            ' . $AirPricingSolution_xml . '
                            ' . $ActionStatus . '
                            </univ:AirCreateReservationReq>
                        </s:Body>
                        </s:Envelope>';
        $AirCreateReservationReq = str_replace("HostToken Key", "HostToken xmlns='http://www.travelport.com/schema/common_v41_0' Key", $AirCreateReservationReq);
        $AirCreateReservationReq = str_replace("<AirPricingSolution", "<AirPricingSolution xmlns='http://www.travelport.com/schema/air_v41_0'", $AirCreateReservationReq);
      
        $request ['request'] = $AirCreateReservationReq;
        // debug($request);exit;
        $request ['status'] = SUCCESS_STATUS;
        return $request;
    }

    function BookingTraveler_Pax_XML($passenger,$air_line_pcode,$address='',$MealsDetails,$arrAirseg,$gender,$email){
        $prefix_a = 'Prefix="' . (isset($passenger['Title']) ? $passenger['Title'] : '') . '"';
      
        $Passenger_PassportNationality = $passenger['CountryCode'];
        $adult_passport_number = $passenger['PassportNumber'];
        $Passenger_PassportIssuedBy = $passenger['CountryCode'];
        $Passenger_DOBp = date('dMy', strtotime($passenger['DateOfBirth']));
        $Passenger_Genderp = $gender;
        $Passenger_DOEp = date('dMy', strtotime($passenger['PassportExpiry']));
        $Passenger_Firstnamep = $passenger['FirstName'];
        $Passenger_Lastnamep = $passenger['LastName'];
        $adults .= '<BookingTraveler Key="' . $paxId . '" TravelerType="ADT" DOB="' . $passenger['DateOfBirth'] . '" Gender="' . $gender . '" xmlns="http://www.travelport.com/schema/common_v41_0">
        <BookingTravelerName ' . $prefix_a . ' First="' . $passenger['FirstName'] . '"   Last="' . $passenger['LastName'] . '" ></BookingTravelerName>
        <PhoneNumber Number="' . $passenger['ContactNo'] . '" Type="Mobile" ></PhoneNumber>
        <Email EmailID="' . $email . '" Type="P" ></Email>';
        if ($passenger['PassportNumber'] != "" && $passenger['PassportExpiry'] != "") {
            $adults .= '<SSR Carrier="' . $air_line_pcode . '" FreeText="P/' . $Passenger_PassportNationality . '/' . $adult_passport_number . '/' . $Passenger_PassportIssuedBy . '/' . $Passenger_DOBp . '/' . $Passenger_Genderp . '/' . $Passenger_DOEp . '/' . $Passenger_Lastnamep . '/' . $Passenger_Firstnamep . '" Status="HK" Type="DOCS"></SSR>';
        }
        if (isset($MealsDetails) && valid_array($MealsDetails)) {
            $k = 0;
            foreach ($arrAirseg as $AirKey => $AirVal) {
                $adults .= '<SSR Carrier="' . $params['flight_data']['FlightDetails']['Details'][0][$k]['OperatorCode'] . '" Status="HK" SegmentRef="' . $AirVal . '" Type="' . $MealsDetails[$k]['Code'] . '"></SSR>';
                $k++;
            }
        }
        $adults .= $address . ' 

            </BookingTraveler>';
    }
    /**
     *
     * Enter description here ...
     * @param unknown_type $booking_params
     * @param unknown_type $app_reference
     * @param unknown_type $sequence_number
     */
    private function run_non_lcc_ticket_service($booking_params, $app_reference, $sequence_number) {
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        $non_lcc_ticket_service_request = $this->run_non_lcc_ticket_service_request($booking_params);
        if ($non_lcc_ticket_service_request['status'] == SUCCESS_STATUS) {
            $api_url = $this->config ['EndPointUrl'];
            $non_lcc_ticket_service_response = $this->process_request($non_lcc_ticket_service_request ['request'], $api_url, 'flight_gds_ticketing(Travelport Flight)');
            $non_lcc_ticket_service_response = Converter::createArray($non_lcc_ticket_service_response);
         
            if ($this->validate_ticket_response($non_lcc_ticket_service_response) == true) {
                $response ['status'] = SUCCESS_STATUS;
                $response ['data']['ticket_response'] = $non_lcc_ticket_service_response;
            } else {
                $error_message = '';
                if (isset($non_lcc_ticket_service_response['Response'] ['Error'] ['ErrorMessage'])) {
                    $error_message = $non_lcc_ticket_service_response['Response'] ['Error'] ['ErrorMessage'];
                }
                if (empty($error_message) == true) {
                    $error_message = 'Ticketing Failed';
                }
                $response ['message'] = $error_message;
                //Log Exception
                $exception_log_message = '';
                $this->CI->exception_logger->log_exception($app_reference, $this->booking_source_name . '- (<strong>TICKET</strong>)', $exception_log_message, $non_lcc_ticket_service_response);
            }
        } else {
            $response ['status'] = FAILURE_STATUS;
        }
       
        return $response;
    }
     /**
     * Forms the Non-LCC Ticket request
     * @param unknown_type $request
     */
    private function run_non_lcc_ticket_service_request($params) {
        
        $ticket_request ='<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:com="http://www.travelport.com/schema/common_v41_0" 
            xmlns:univ="http://www.travelport.com/schema/universal_v41_0">
               <soapenv:Header/>
               <soapenv:Body>
                  <air:AirTicketingReq BulkTicket="false" ReturnInfoOnFail="true" TargetBranch="' . $this->config ['Target'] . '" xmlns:air="http://www.travelport.com/schema/air_v41_0">
                     <com:BillingPointOfSaleInfo OriginApplication="UAPI"/>
                     <air:AirReservationLocatorCode>'.$params['AirReservationLocatoreCode'].'</air:AirReservationLocatorCode>
                          </air:AirTicketingReq>
               </soapenv:Body>
            </soapenv:Envelope>';

        $request ['request'] = $ticket_request;
        $request ['status'] = SUCCESS_STATUS;

        return $request;
    }
    private function validate_ticket_response($response) {
        
        if(isset($response['SOAP:Envelope']['SOAP:Body']['air:AirTicketingRsp']['air:ETR'][0]['air:Ticket']['@attributes']['TicketNumber'])){
            $ticket_number = $response['SOAP:Envelope']['SOAP:Body']['air:AirTicketingRsp']['air:ETR'][0]['air:Ticket']['@attributes']['TicketNumber'];
        }
        else{
            $ticket_number = @$response['SOAP:Envelope']['SOAP:Body']['air:AirTicketingRsp']['air:ETR']['air:Ticket']['@attributes']['TicketNumber'];
        } 
        
        if (valid_array($response) == true && $this->is_ticketing_error($response) == false &&
                isset($ticket_number) == true && empty($ticket_number) == false) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * Checks Ticketing Error
     */
    private function is_ticketing_error($response) {
       
        if (isset($response['SOAP:Envelope']['SOAP:Body']['air:TicketFailureInfo']) == true) {
            return true;
        } else {
            return false;
        }
    }
    private function save_flight_ticket_details($booking_params, $book_response, $ticket_response, $app_reference, $sequence_number, $search_id){
        
        $flight_booking_transaction_details_fk = $this->CI->custom_db->single_table_records('flight_booking_transaction_details', 'origin', array('app_reference' => $app_reference, 'sequence_number' => $sequence_number));
        $flight_booking_transaction_details_fk = $flight_booking_transaction_details_fk['data'][0]['origin'];
        $flight_booking_itinerary_details_fk = $this->CI->custom_db->single_table_records('flight_booking_itinerary_details', 'airline_code', array('app_reference' => $app_reference));
        if(isset($ticket_response['SOAP:Envelope']['SOAP:Body']['air:AirTicketingRsp']['air:ETR']['air:AirReservationLocatorCode'])){
            $pnr = $ticket_response['SOAP:Envelope']['SOAP:Body']['air:AirTicketingRsp']['air:ETR']['air:AirReservationLocatorCode'];
        }
        else{
            $pnr = $ticket_response['SOAP:Envelope']['SOAP:Body']['air:AirTicketingRsp']['air:ETR'][0]['air:AirReservationLocatorCode'];
        }
        $book_id = $book_response['universal:AirCreateReservationRsp']['universal:UniversalRecord']['@attributes']['LocatorCode'];

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
        $airsegment = $book_response['universal:AirCreateReservationRsp']['universal:UniversalRecord']['air:AirReservation']['air:AirSegment'];
        if(isset($airsegment[0])){
            $airsegment_list = $airsegment;
        }
        else{
            $airsegment_list[0] = $airsegment;
        }
        foreach($airsegment_list as $seg_key => $seg_value){
            
            $origin = $seg_value['@attributes']['Origin'];
            $destination = $seg_value['@attributes']['Destination'];
            $DepartureTime = explode('T', $seg_value ['@attributes'] ['DepartureTime']);
            $DepartureTime1 = explode('.', $DepartureTime[1]);
            $dept_time = $DepartureTime[0].' '.$DepartureTime1[0];
           
            //itinerary condition for update
            $update_itinerary_condition = array();
            $update_itinerary_condition['flight_booking_transaction_details_fk'] = $flight_booking_transaction_details_fk;
            $update_itinerary_condition['app_reference'] = $app_reference;
            $update_itinerary_condition['from_airport_code'] = $origin;
            $update_itinerary_condition['to_airport_code'] = $destination;
            $update_itinerary_condition['departure_datetime'] = $dept_time;

            //itinerary updated data
            $update_itinerary_data = array();
            $update_itinerary_data['airline_pnr'] = $book_response['universal:AirCreateReservationRsp']['universal:UniversalRecord']['air:AirReservation']['common_v41_0:SupplierLocator']['@attributes']['SupplierLocatorCode'];
            $GLOBALS['CI']->custom_db->update_record('flight_booking_itinerary_details', $update_itinerary_data, $update_itinerary_condition);
        }
       

        // $passenger_details = force_multple_data_format($passenger_details);
        $get_passenger_details_condition = array();
        $get_passenger_details_condition['flight_booking_transaction_details_fk'] = $flight_booking_transaction_details_fk;
        $passenger_details_data = $GLOBALS['CI']->custom_db->single_table_records('flight_booking_passenger_details', 'origin, passenger_type', $get_passenger_details_condition);
        $passenger_details_data = $passenger_details_data['data'];
        $passenger_origins = group_array_column($passenger_details_data, 'origin');
        $passenger_types = group_array_column($passenger_details_data, 'passenger_type');
        // echo 'mnngng';
        if(isset($ticket_response['SOAP:Envelope']['SOAP:Body']['air:AirTicketingRsp']['air:ETR']['air:Ticket']['@attributes']['TicketNumber'])){
            $ticket_number[0]['ticket_number'] = $ticket_response['SOAP:Envelope']['SOAP:Body']['air:AirTicketingRsp']['air:ETR']['air:Ticket']['@attributes']['TicketNumber'];
            $ticket_number[0]['ticket_id'] = $ticket_response['SOAP:Envelope']['SOAP:Body']['air:AirTicketingRsp']['air:ETR']['air:Ticket']['@attributes']['Key'];
        }
        else{
            $ticket_list = $ticket_response['SOAP:Envelope']['SOAP:Body']['air:AirTicketingRsp']['air:ETR'];
            foreach($ticket_list as $t_key => $t_value){
                // debug($t_value);exit;
                
                $ticket_number[$t_key]['ticket_number'] = $t_value['air:Ticket']['@attributes']['TicketNumber'];
                $ticket_number[$t_key]['passenger_type'] = $t_value['air:AirPricingInfo']['air:FareInfo']['@attributes']['PassengerTypeCode'];
                $ticket_number[$t_key]['ticket_id'] =  $t_value['air:Ticket']['@attributes']['TicketNumber'];
            }
        }
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
            if(empty($ticket_number[$pax_k]['ticket_id']) == false){
                $ticket_id = $ticket_number[$pax_k]['ticket_id'];
                $tkt_number = $ticket_number[$pax_k]['ticket_number'];
            }
            else {
                $ticket_id = '';
                $ticket_number = '';
            }
            //Update Passenger Ticket Details
            $this->CI->common_flight->update_passenger_ticket_info($passenger_fk, $ticket_id, $tkt_number, @$single_pax_fare_breakup[$pax_type]);
        }
      
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
     * Meal Details For Non-LCC Flights
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
        return $meal;
    }
    /**
     * Baggage Details For Non-LCC Flights
     */
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
                        $baggage[$baggage_k] = $temp_baggage[0];
                    }
                }
            }
        }
        // debug($meal);exit;
        return $baggage;
    }

    /**
     * Search Request
     * @param unknown_type $search_id
     */
    public function get_search_request($search_id) {
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        /* get search criteria based on search id */
        $search_data = $this->search_data($search_id);
      
        if ($search_data ['status'] == SUCCESS_STATUS) {
            // Flight search RQ

            $search_request = $this->flight_low_fare_search_req($search_data ['data']);
            // debug($search_request);exit;            
            if ($search_request ['status'] = SUCCESS_STATUS) {
                $response ['status'] = SUCCESS_STATUS;
                $curl_request = $this->form_curl_params($search_request['request'], $search_request ['url'], $search_request ['soap_action']);

                $response ['data'] = $curl_request['data'];
            }
        }
        // debug($response);exit;
        return $response;
    }
    /**
     * Save Book Service Response
     * @param unknown_type $book_response
     * @param unknown_type $app_reference
     * @param unknown_type $sequence_number
     */
    private function save_book_response_details($booking_params, $book_response, $app_reference, $sequence_number, $search_id, $provider_code) {
        $update_data = array();
        $update_condition = array();
        $update_data['book_id'] = $book_response['universal:AirCreateReservationRsp']['universal:UniversalRecord']['@attributes']['LocatorCode'];
        $update_data['airline_pnr'] = $book_response['universal:AirCreateReservationRsp']['universal:UniversalRecord']['air:AirReservation']['common_v41_0:SupplierLocator']['@attributes']['SupplierLocatorCode'];
        $gds_pnr_data = $book_response['universal:AirCreateReservationRsp']['universal:UniversalRecord']['universal:ProviderReservationInfo'];
        if(isset($gds_pnr_data[0])){
            $gds_pnrs = $gds_pnr_data;
        }
        else{
            $gds_pnrs[0] = $gds_pnr_data; 
        }
        $pnr ='';
        foreach($gds_pnrs as $pnrs){
            $ProviderCode = $pnrs['@attributes']['ProviderCode'];
            // debug($booking_params);exit;
            $provider_code = $booking_params['ResultToken']['flight_list'][0]['flight_detail'][0]['provider_code'];
            if($ProviderCode == $provider_code){
                $pnr = $pnrs['@attributes']['LocatorCode'];
            }
        }

        $update_data['pnr'] = $pnr;

        $update_condition['app_reference'] = $app_reference;
        $update_condition['sequence_number'] = $sequence_number;

        $this->CI->custom_db->update_record('flight_booking_transaction_details', $update_data, $update_condition);
        if($provider_code == '1G'){
            $flight_booking_status = 'BOOKING_HOLD';
        }
        else{
            $flight_booking_status = 'BOOKING_CONFIRMED';
        }
       
        $this->CI->common_flight->update_flight_booking_status($flight_booking_status, $app_reference, $sequence_number, $this->booking_source);
        //only for ACH
        if($provider_code == 'ACH'){
             $flight_booking_transaction_details_fk = $this->CI->custom_db->single_table_records('flight_booking_transaction_details', 'origin', array('app_reference' => $app_reference, 'sequence_number' => $sequence_number));
        $flight_booking_transaction_details_fk = $flight_booking_transaction_details_fk['data'][0]['origin'];
        $flight_booking_itinerary_details_fk = $this->CI->custom_db->single_table_records('flight_booking_itinerary_details', 'airline_code', array('app_reference' => $app_reference));

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
        $airsegment = $book_response['universal:AirCreateReservationRsp']['universal:UniversalRecord']['air:AirReservation']['air:AirSegment'];
        if(isset($airsegment[0])){
            $airsegment_list = $airsegment;
        }
        else{
            $airsegment_list[0] = $airsegment;
        }
        foreach($airsegment_list as $seg_key => $seg_value){
            
            $origin = $seg_value['@attributes']['Origin'];
            $destination = $seg_value['@attributes']['Destination'];
            $DepartureTime = explode('T', $seg_value ['@attributes'] ['DepartureTime']);
            $DepartureTime1 = explode('.', $DepartureTime[1]);
            $dept_time = $DepartureTime[0].' '.$DepartureTime1[0];
           
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
        // echo 'mnngng';
        $ticket_number = $pnr;
        
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
            $ticket_id = $ticket_number;
            $tkt_number = $ticket_number;
            
            //Update Passenger Ticket Details
            $this->CI->common_flight->update_passenger_ticket_info($passenger_fk, $ticket_id, $tkt_number, @$single_pax_fare_breakup[$pax_type]);
        }

        }
    }
    /*getting airline list */
    function get_airline_list($is_domestic){
        if(empty($is_domestic) == true){
            $is_domestic = 0;
        }
        else{
            $is_domestic = $is_domestic;
        }
        $domain_origin = get_domain_auth_id();
        $airline_list = $this->CI->flight_model->get_tp_active_airline_list($domain_origin, $is_domestic);
        $airline_list_data = array();
        foreach ($airline_list as $airline_key => $airline) {
            $airline_list_data[] = $airline['code'];
        }
        return $airline_list_data;
    }
   
    /**
     *
     * @param
     *          $search_id
     */
    function booking_url($search_id) {
        
    }

    /**
     * process soap API request
     *
     * @param string $request
     */
    function form_curl_params($request, $url) {
        // debug($request);exit;
        $data['status'] = SUCCESS_STATUS;
        $data['message'] = '';
        $data['data'] = array();
        $soapAction = '';
        //debug($this->config);exit;
        $Authorization = base64_encode('Universal API/' . $this->config['UserName'] . ':' . $this->config['Password']);

        $curl_data = array();
        $curl_data['booking_source'] = $this->booking_source;
        $curl_data['request'] = $request;

        $curl_data['url'] = $url;
        $curl_data['header'] = array(
            "SOAPAction: {$soapAction}",
            "Content-Type: text/xml; charset=UTF-8",
            "Content-Encoding: UTF-8",
            "Authorization: Basic $Authorization",
            "Content-length: " . strlen($request[0]),
            "Accept-Encoding: gzip,deflate"
        );

        $data['data'] = $curl_data;

        return $data;
    }

}
