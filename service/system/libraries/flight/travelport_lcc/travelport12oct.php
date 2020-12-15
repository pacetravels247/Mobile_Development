<?php
require_once BASEPATH . 'libraries/flight/Common_api_flight.php';

class Travelport extends Common_Api_Flight {

    var $master_search_data;
    var $retunr_domestic;
    public $_random_value_price = '5555555';
    var $search_hash;
    protected $token;
    var $api_session_id;

    function __construct() {
        parent::__construct(META_AIRLINE_COURSE, TRAVELPORT_FLIGHT_BOOKING_SOURCE);
        $this->CI = &get_instance();
        $this->CI->load->library('Converter');
        $this->CI->load->library('ArrayToXML');
        $this->set_api_credentials();
        //$this->set_api_session_id();
    }

    /**
     * set API credentials
     */
    private function set_api_credentials() {

        $engine_system = $this->CI->config->item('flight_engine_system');

        $this->system = $engine_system;
        $this->details = $this->CI->config->item('travelport_flight_' . $engine_system);
        $this->url = $this->details ['api_url'];
        $this->username = $this->details ['username'];
        $this->password = $this->details ['password'];
        //$this->target_branch = $this->config ['Target'];
        // $this->username = $this->details ['username'];
    }

    /**
     * request Header
     */
    private function get_header() {
        // Vipassana centre
        $header = array(
            'Accept: text/xml',
            'Accept-Encoding: gzip, deflate',
            'Content-Type: text/xml; charset=utf-8'
        );
        return array(
            'header' => $header
        );
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
        // debug($flight_raw_data);exit;
        //debug($search_response_array);exit;
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned

        $search_data = $this->search_data($search_id);
        if ($search_data ['status'] == SUCCESS_STATUS) {
            $search_response_array[] = Converter::createArray($flight_raw_data);
            if (!empty($this->retunr_domestic)) {
                $search_response = $this->process_request($this->retunr_domestic ['request']);
                $search_response_array[] = Converter::createArray($search_response);
            }
            // debug($search_response_array);exit;
            if ($this->valid_search_result($search_response_array) == TRUE) {
                $clean_format_data = $this->format_search_data_response($search_response_array, $search_data ['data']);
                if ($clean_format_data) {
                    $response ['status'] = SUCCESS_STATUS;
                } else {
                    $response ['status'] = FAILURE_STATUS;
                }
            } else {
                $response ['status'] = FAILURE_STATUS;
            }

            if ($response ['status'] == SUCCESS_STATUS) {
                $response ['data'] = $clean_format_data;
            }
        } else {
            $response ['status'] = FAILURE_STATUS;
        }
        //debug($search_response_array);exit;
        return $response;
    }

    /**
     * Fare Rule
     * @param unknown_type $request
     */
    public function get_fare_rules($flight_result) {
        //$this->air_pricing_request($flight_result);
        $arrSegmentData = $flight_result['flight_list'][0]['flight_detail'];
        $fare_rule_request = $this->fare_rule_request($arrSegmentData);
        // debug($arrSegmentData);exit;
        if ($fare_rule_request['status'] == SUCCESS_STATUS) {
            $fare_rule_response = $this->process_request($fare_rule_request['request']);

            $arrfare_result = Converter::createArray($fare_rule_response);
            //debug($arrfare_result);exit;
            if (valid_array($arrfare_result) == true) {
                $arrFareRule1 = $arrfare_result['SOAP:Envelope']['SOAP:Body']['air:AirFareRulesRsp']['air:FareRule'];
                if (!isset($arrFareRule1[0])) {
                    $arrFareRule[0] = $arrFareRule1;
                } else {
                    $arrFareRule = $arrFareRule1;
                }
                //debug($arrFareRule);exit;
                $arrFareData = array();
                $FareRules = '';
                for ($fr = 0; $fr < count($arrFareRule); $fr++) {
                    $freRule = $arrFareRule[$fr]['air:FareRuleLong'];
                    // pr($freRule);exit();
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
                    //debug($fareResp1);exit;
                    $response ['data']['FareRuleDetail'] = $fareResp1;
                }
            } else {
                $response ['message'] = 'Not Available';
            }
        } else {
            $response ['status'] = FAILURE_STATUS;
        }
        //debug($response);exit;
        return $response;
    }

    private function fare_rule_request($arrSegmentData) {
        $strRequest = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
                <soapenv:Header/>
                <soapenv:Body>
                    <ns2:AirFareRulesReq xmlns="http://www.travelport.com/schema/common_v41_0" xmlns:ns2="http://www.travelport.com/schema/air_v41_0" FareRuleType="long" TraceId="394d96c00971c4545315e49584609ff6" TargetBranch="' . $this->config ['Target'] . '" AuthorizedBy="' . $this->config ['UserName'] . '">
                      <BillingPointOfSaleInfo OriginApplication="uAPI" />';
        //debug($arrSegmentData);exit;
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
        $request ['request'] = $strRequest;
        $request ['status'] = SUCCESS_STATUS;
        return $request;
    }

    function get_flight_list_old($search_id) {

        $response ['data'] = array();
        $response ['status'] = SUCCESS_STATUS;

        /* get search criteria based on search id */
        $search_data = $this->search_data($search_id);

        $header_info = $this->get_header();


        if ($search_data ['status'] == SUCCESS_STATUS) {

            // Flight search request
            $flight_search_request = $this->flight_low_fare_search_req($search_data ['data']);
            //debug($flight_search_request);exit;
            if ($flight_search_request ['status'] = SUCCESS_STATUS) {
                // return_request
                $search_response = $this->process_request($flight_search_request['payload']);
                debug($search_response);
                exit;
                $search_response_array = Converter::createArray($search_response);
                // debug($search_response_array);exit;
                // echo 'air:AirSegmentList';
                // debug(COUNT($search_response_array['SOAP:Envelope']['SOAP:Body']['air:LowFareSearchRsp']['air:AirSegmentList']));debug($search_response_array);exit;
                if ($this->valid_search_result($search_response_array) == TRUE) {
                    $key = 0;
                    // SAVE response in 'test' table
                    // $this->CI->custom_db->generate_static_response(json_encode($search_response));
                    $clean_format_data = $this->format_search_data_response($search_response_array, $search_data ['data'], $key);
                    $response ['data'] ['flight_data_list'] ['journey_list'] [0] = $clean_format_data;
                    if ($cache_search) {
                        $cache_exp = $this->CI->config->item('cache_flight_search_ttl');
                        $this->CI->cache->file->save($search_hash, $response ['data'], $cache_exp);
                    }
                }

                if (isset($flight_search_request ['data'] ['return_request']) && !empty($flight_search_request ['data'] ['return_request'])) {
                    $return_search_response = $this->process_request($flight_search_request ['data'] ['return_request']);
                    $return_search_response_array = Converter::createArray($return_search_response);

                    if ($this->valid_search_result($return_search_response_array) == TRUE) {
                        $key = 1;
                        // SAVE response in 'test' table
                        // $this->CI->custom_db->generate_static_response(json_encode($search_response));
                        $clean_format_data = $this->format_search_data_response($return_search_response_array, $search_data ['data'], $key);

                        $response ['data'] ['flight_data_list'] ['journey_list'] [1] = $clean_format_data;
                        if ($cache_search) {
                            $cache_exp = $this->CI->config->item('cache_flight_search_ttl');
                            $this->CI->cache->file->save($search_hash, $response ['data'], $cache_exp);
                        }
                    }
                }
            }
        } else {
            $response ['status'] = FAILURE_STATUS;
        }
        //debug($response);exit;
        return $response;
    }

    function air_create_reservation($params) {
        echo 'Ticket Booked Successfully On Our Test Environment';
        echo '<br>Neptune System Reference For This Booking Request : NPT-' . rand(1000, 100000) . '<br>';
        echo 'Booked At : ' . date('d-M-Y H:i:s');
        exit();
        $air_create_reservation_request = $this->air_create_reservation_request($params);
        if ($air_create_reservation_request ['status'] == SUCCESS_STATUS) {
            $air_create_reservation_response = $this->process_request($air_create_reservation_request ['data'] ['request']);

            $test = Converter::createArray($search_response);
            echo 'inside';
            debug($air_create_reservation_response);
            exit();
        }
    }

    private function air_create_reservation_request($params, $token_data) {
        $response ['data'] = array();
        $response ['status'] = FAILURE_STATUS;
        // debug($params);
        $booking_source = $params ['booking_source'];
        $passenger_title_arr = $params ['name_title'];
        $passenger_first_name_arr = $params ['first_name'];
        $passenger_last_name_arr = $params ['last_name'];
        $passenger_date_of_birth_arr = $params ['date_of_birth'];
        $passenger_passport_number_arr = $params ['passenger_passport_number'];
        $passenger_passport_issuing_country_arr = $params ['passenger_passport_issuing_country'];
        $passenger_passport_expiry_date_arr = @$params ['passenger_passport_expiry_date'];
        $passenger_type_arr = $params ['passenger_type'];
        $lead_passenger_arr = $params ['lead_passenger'];
        $gender_arr = $params ['gender'];
        $passenger_nationality_arr = $params ['passenger_nationality'];

        $street = $params ['billing_address_1'];
        $city = $params ['billing_city'];
        $state = $params ['billing_city'];
        $billing_country = $params ['billing_country'];
        $billing_passenger_contact = $params ['passenger_contact'];
        $postal_code = $params ['billing_zipcode'];
        $billing_email = $params ['billing_email'];
        $payment_method = $params ['payment_method'];

        $country = '';
        $sql = 'SELECT iso_country_code FROM `api_country_list` WHERE `origin` = ' . $billing_country;
        $CI = &get_instance();
        $country = $CI->db->query($sql)->row();
        $country = $country->iso_country_code;

        $holder_details ['first_name'] = $passenger_first_name_arr [0];
        $holder_details ['last_name'] = $passenger_last_name_arr [0];
        $holder_details ['dob'] = $passenger_date_of_birth_arr [0];
        $holder_details ['passport_no'] = $passenger_passport_number_arr [0];
        $holder_details ['passport_issue_country'] = $passenger_passport_issuing_country_arr [0];
        $holder_details ['passport_exp_date'] = $passenger_passport_expiry_date_arr [0];
        $holder_details ['passenger_type'] = $passenger_type_arr [0];
        $holder_details ['gender'] = get_enum_list('gender', $gender_arr [0]);
        $holder_details ['pass_nationality'] = $passenger_nationality_arr [0];

        $address = '<Address>
                    <AddressName>' . $holder_details ['first_name'] . '</AddressName>
                    <Street>' . $street . '</Street>
                    <City>' . $city . '</City>
                    <State>' . $state . '</State>
                    <PostalCode>' . $postal_code . '</PostalCode>
                    <Country>' . $country . '</Country>
                </Address>';

        $connection = $token_data ['connection'];
        $air_price_xml = str_replace('<?xml version="1.0"?>', '', str_replace('air:', '', $token_data ['price_xml']));

        // search criteria
        $search_data = $this->search_data($params ['search_id']);
        $search_data = $search_data ['data'];
        $total_price = $token_data ['total_price'];
        $currency = $token_data ['total_price_curr'];

        // create request
        $adults = '';
        $adult_config = $search_data ['adult_config'];
        for ($i = 0; $i < $adult_config; $i ++) {
            $gender_pax = get_enum_list('gender', $gender_arr [$i]);
            $gender_pax = substr($gender_pax, 0, 1);

            if (substr($gender_pax, 0, 1) == "F") {
                $prefix = 'Prefix="Ms"';
            } else {
                $prefix = 'Prefix="Mr"';
            }
            $ssr_a = '<SSR FreeText="P/IN/' . $passenger_passport_number_arr [$i] . '/' . $billing_country . '/' . date('dMy', strtotime(str_replace('/', '-', $passenger_date_of_birth_arr [$i]))) . '/' . $holder_details ['gender'] . '/' . date('dMy', strtotime(str_replace("/", "-", $holder_details ['passport_exp_date']))) . '/' . $holder_details ['last_name'] . '/' . $holder_details ['first_name'] . '" Type="DOCS"/>';
            $adults .= '<BookingTraveler xmlns="http://www.travelport.com/schema/common_v41_0" Key="0" TravelerType="ADT" DOB="' . date('Y-m-d', strtotime(str_replace('/', '-', $passenger_date_of_birth_arr [$i]))) . '" Gender="' . $gender_pax . '"     >
                    <BookingTravelerName ' . $prefix . ' First="' . $passenger_first_name_arr [$i] . '" Last="' . $passenger_last_name_arr [$i] . '" />
                    <PhoneNumber Number="' . $billing_passenger_contact . '" Type="Mobile" />
                    <Email EmailID="' . $billing_email . '" Type="P" />
                    ' . $ssr_a . '    
                    ' . $address . '
                </BookingTraveler>';
        }

        // child
        $childs = '';
        $child_config = $search_data ['child_config'];
        for ($j = 0; $j < $child_config; $j ++) {
            // NEED TO FIX
            // $child_patch .= '<SearchPassenger Code="CNN" Age="10" xmlns="http://www.travelport.com/schema/common_v41_0" />';
        }
        // infant
        $infants = '';
        $infant_config = $search_data ['infant_config'];
        for ($k = 0; $k < $infant_config; $k ++) {
            // NEED TO FIX
        }

        // flights
        $flights = $token_data ['flights'];
        $provider = @$flights [0] ['provide_code'];
        if ($provider == "1G") {
            $payment = '<FormOfPayment xmlns="http://www.travelport.com/schema/common_v41_0"  Type="Cash"/>';
            // QueueCategory="01"
            $ticketDate = str_replace('/', '-', date('Y-m-d', strtotime(@$flights [0] ['departure_datetime']))) . "T" . date("H:i:s");
            $PointOfSale = '<PointOfSale ProviderCode="' . $provider . '"></PointOfSale>';
            // $ActionStatus = '<ActionStatus ProviderCode="' . $provider . '" TicketDate="'.$ticketDate.'" Type="TAU" xmlns="http://www.travelport.com/schema/common_v41_0" ></ActionStatus>';Type="ACTIVE"
            $ActionStatus = '<ActionStatus xmlns="http://www.travelport.com/schema/common_v41_0" ProviderCode="' . $provider . '" TicketDate="' . $ticketDate . '" Type="TAU" QueueCategory="01"/>';
        } else {
            $LatestTicketingTime = str_replace(" ", "T", date("Y-m-d H:m:s", strtotime("+ 5 Hours")));
            $payment = '<FormOfPayment xmlns="http://www.travelport.com/schema/common_v41_0" Type="Cash">
            <CreditCard CVV="737" ExpDate="2017-06" Name="Veera K" Number="4111111111111111" Type="VI">
              <BillingAddress>
               ' . $address . '
              </BillingAddress>
            </CreditCard>
          </FormOfPayment>';
            $ActionStatus = '<ActionStatus xmlns="http://www.travelport.com/schema/common_v41_0" ProviderCode="' . $provider . '" TicketDate="T*" Type="TAU" QueueCategory="01"/>';
            // $ActionStatus = '<ActionStatus ProviderCode="' . $provider . '" TicketDate="' . $LatestTicketingTime . '" Type="TAW" xmlns="http://www.travelport.com/schema/common_v41_0" ></ActionStatus>';
        }

        // $air_price_xml = str_replace('Endorsement','common_v34_0:Endorsement',$air_price_xml);
        $AirCreateReservationReq = '<?xml version="1.0" encoding="UTF-8"?>
            <s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
                        <s:Header/>
                        <s:Body>
                          <AirCreateReservationReq xmlns="http://www.travelport.com/schema/universal_v41_0" RetainReservation="Both" RuleName="COMM" TargetBranch="P7039804">
                            <BillingPointOfSaleInfo xmlns="http://www.travelport.com/schema/common_v41_0" OriginApplication="UAPI"/>
                            ' . $adults . '
                            ' . $childs . '
                            ' . $infants . '
                            ' . $payment . '
                            ' . $air_price_xml . '
                            ' . $ActionStatus . '
                            </AirCreateReservationReq>
                        </s:Body>
                        </s:Envelope>';
        $response ['status'] = SUCCESS_STATUS;
        //debug($AirCreateReservationReq);exit;
        $response ['data'] ['request'] = $AirCreateReservationReq;
        return $response;
    }

    /**
     * Format Search data response
     */
    private function format_search_data_response($low_fare_search_res_data, $search_data) {
        // debug($low_fare_search_res_data);exit;
        $flight_data_list = array();
        foreach ($low_fare_search_res_data as $key => $low_fare_search_res) {
            $low_fare_search_res = $low_fare_search_res ['SOAP:Envelope'] ['SOAP:Body'];
            //debug($low_fare_search_res);exit;
            // If error is there then return false
            if (isset($low_fare_search_res ['SOAP:Fault'])) {
                return false;
            }
            // debug($low_fare_search_res['air:LowFareSearchRsp']['air:AirSegmentList']);exit;
            $currency_type = $low_fare_search_res ['air:LowFareSearchRsp'] ['@attributes'] ['CurrencyType'];

            // air:AirPricingSolution TO FIX - create function for airpricing solution
            $air_flight_list = $low_fare_search_res ['air:LowFareSearchRsp'] ['air:FlightDetailsList'];
            $air_pricing_solutions = $low_fare_search_res ['air:LowFareSearchRsp'] ['air:AirPricingSolution'];
            $air_segment_list = @$low_fare_search_res ['air:LowFareSearchRsp'] ['air:AirSegmentList'] ['air:AirSegment'];
            $air_fare_info_list = @$low_fare_search_res ['air:LowFareSearchRsp'] ['air:FareInfoList'] ['air:FareInfo'];
            //debug($air_fare_info_list);exit;
            $arrBaggageData = array();
            $arrFareruleData = array();
            for ($b = 0; $b < count($air_fare_info_list); $b++) {
                if (isset($air_fare_info_list[$b]['air:BaggageAllowance']['air:MaxWeight']['@attributes'])) {
                    //echo "Here<br/>";
                    $strFRefKey = $air_fare_info_list[$b]['@attributes']['Key'];
                    $strPlaces = $air_fare_info_list[$b]['@attributes']['Origin'] . "_" . $air_fare_info_list[$b]['@attributes']['Destination'];
                    $strPasCode = $air_fare_info_list[$b]['@attributes']['PassengerTypeCode'];
                    $arrBag_Data['value'] = $air_fare_info_list[$b]['air:BaggageAllowance']['air:MaxWeight']['@attributes']['Value'];
                    $arrBag_Data1['Baggage'] = $air_fare_info_list[$b]['air:BaggageAllowance']['air:MaxWeight']['@attributes']['Value'] . ' ' . $air_fare_info_list[$b]['air:BaggageAllowance']['air:MaxWeight']['@attributes']['Unit'];
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
                //debug($arrBaggageData);exit;
                if (isset($air_fare_info_list[$b]['air:FareRuleKey']['@attributes'])) {
                    $arrfare_Data['FareInfoRef'] = $FareInfoRef = $air_fare_info_list[$b]['air:FareRuleKey']['@attributes']['FareInfoRef'];
                    $arrfare_Data['FareInfoValue'] = $air_fare_info_list[$b]['air:FareRuleKey']['@value'];
                    $arrfare_Data['ProviderCode'] = $air_fare_info_list[$b]['air:FareRuleKey']['@attributes']['ProviderCode'];
                    $arrFareruleData[$FareInfoRef] = $arrfare_Data;
                }
            }
            //debug($arrFareruleData);exit;
            if(isset($air_pricing_solutions[0])){
                $air_pricing_solutions_data = $air_pricing_solutions;
            }
            else{
                $air_pricing_solutions_data[0] = $air_pricing_solutions;
            }
           
            if (isset($air_pricing_solutions_data) && valid_array($air_pricing_solutions_data)) {
                foreach ($air_pricing_solutions_data as $ap_key => $pricing_array) {
                   
                    $flight_data_list [] = $this->format_air_pricing_solution($pricing_array, $air_segment_list, $arrBaggageData, $arrFareruleData, $search_data);
                    
                   
                } 
            }

            // debug($flight_data_list);exit;
            $response ['FlightDataList'] ['JourneyList'][$key] = $flight_data_list;
            unset($flight_data_list);
        }
        // debug($response);exit;
        return $response;
    }

    /**
     * array $pricing_array
     */
    function format_air_pricing_solution($pricing_array, $air_segment_list1, $arrBaggageData, $arrFareruleData, $search_data) {
        if(isset($air_segment_list1[0])){
           $air_segment_list = $air_segment_list1;
        }
        else{
             $air_segment_list[0] = $air_segment_list1;    
        }
        $CI = & get_instance();
         
        $agent_commssion = 0;
        $yq_value = 0;
        $flight_journey = array();

        $journey_detail = force_multple_data_format(@$pricing_array ['air:Journey']);
        //    debug($pricing_array);exit;
        $air_pricing_info = @$pricing_array ['air:AirPricingInfo'];
        $connection_arr = force_multple_data_format(@$pricing_array ['air:Connection']);

        $connection = '';
        if (isset($connection_arr) && valid_array($connection_arr)) {
            foreach ($connection_arr as $c_key => $connect_flight) {
                if (isset($connect_flight ['@attributes'] ['SegmentIndex'])) {
                    $connection .= @$connect_flight ['@attributes'] ['SegmentIndex'] . ",";
                } else {
                    $connection .= @$connect_flight ['SegmentIndex'] . ",";
                }
            }
        }
        //debug($pricing_array);exit;
        $AirPricingInfo = $pricing_array['air:AirPricingInfo'];
        
        if (isset($pricing_array['air:AirPricingInfo'][0])) {
            
            $tax_info = $pricing_array['air:AirPricingInfo'][0]['air:TaxInfo'];

            $yqvalue = '';
            $YR_value = '';
            foreach ($tax_info as $tax) {
                if ($tax['@attributes']['Category'] == 'YQ') {
                    $yqvalue .= substr(@$tax['@attributes']['Amount'], 3);
                }
                if ($tax['@attributes']['Category'] == 'YR') {
                    $YR_value .= substr(@$tax['@attributes']['Amount'], 3);
                }
            }

            # Checking the value of YQ params
            if (!empty($yqvalue)) {
                $yqvalue = $yqvalue;
            } else {
                $yqvalue = '0.00';
            }


            # Checking the value of YR params
            if (!empty($YR_value)) {
                $YR_value = $YR_value;
            } else {
                $YR_value = '0.00';
            }


            $AirPricingInfo_Attr = $AirPricingInfo[0]['@attributes'];
            
            $AirPricingInfo_Attr = json_encode($AirPricingInfo_Attr);
            $AirPricingInfo_Attr = json_decode($AirPricingInfo_Attr);
            if (isset($AirPricingInfo_Attr->Refundable)) {
                $Refundable = $AirPricingInfo_Attr->Refundable;
            } else {
                $Refundable = 'false';
            }
            $AirPricingInfos = $AirPricingInfo;
            foreach ($AirPricingInfos as $key => $AirPricingInfo) {
                
                $Passenger_type = $AirPricingInfo['air:PassengerType'];
                unset($Passenger_type['@value']);
                foreach ($Passenger_type as $key => $Passenger_type_details) {
                    if (isset($Passenger_type_details['@attributes'])) {
                        $All_Passenger[] = $Passenger_type_details['@attributes']['Code'];
                    } else {
                        $All_Passenger[] = $Passenger_type_details['Code'];
                    }
                }

                if (isset($AirPricingInfo['@attributes']['EquivalentBasePrice'])) {
                    $BasePrice = substr($AirPricingInfo['@attributes']['EquivalentBasePrice'], 3);
                    $BasePrice_Curr = substr($AirPricingInfo['@attributes']['EquivalentBasePrice'], 0, 3);
                    // $BasePrice = $this->flight_model->currency_convertor($BasePrice, $BasePrice_Curr, CURR);
                    $Pass_BasePrice[] = $BasePrice;
                } elseif (isset($AirPricingInfo['@attributes']['ApproximateBasePrice'])) {
                    $BasePrice = substr($AirPricingInfo['@attributes']['ApproximateBasePrice'], 3);
                    $BasePrice_Curr = substr($AirPricingInfo['@attributes']['ApproximateBasePrice'], 0, 3);
                    //$BasePrice = $this->flight_model->currency_convertor($BasePrice, $BasePrice_Curr, CURR);
                    $Pass_BasePrice[] = $BasePrice;
                } else {
                    $BasePrice = substr($AirPricingInfo['@attributes']['BasePrice'], 3);
                    $BasePrice_Curr = substr($AirPricingInfo['@attributes']['BasePrice'], 0, 3);
                    //$BasePrice = $this->flight_model->currency_convertor($BasePrice, $BasePrice_Curr, CURR);
                    $Pass_BasePrice[] = $BasePrice;
                }
                if (isset($AirPricingInfo['@attributes']['ApproximateTaxes'])) {
                    $Taxes = substr($AirPricingInfo['@attributes']['ApproximateTaxes'], 3);
                    $Taxes_Curr = substr($AirPricingInfo['@attributes']['ApproximateTaxes'], 0, 3);
                    //$Taxes = $this->flight_model->currency_convertor($Taxes, $Taxes_Curr, CURR);
                    $Pass_Taxes[] = $Taxes;
                } else {
                    if (isset($AirPricingInfo['@attributes']['Taxes'])) {
                        $Taxes = substr($AirPricingInfo['@attributes']['Taxes'], 3);
                        $Taxes_Curr = substr($AirPricingInfo['@attributes']['Taxes'], 0, 3);
                        //$Taxes = $this->flight_model->currency_convertor($Taxes, $Taxes_Curr, CURR);
                    } else {
                        $Taxes = 0;
                    }
                    $Pass_Taxes[] = $Taxes;
                }
            }
            $All_Passengers = implode(',', $All_Passenger);
            $Pass_BasePrice = implode(',', $Pass_BasePrice);
            $Pass_Taxes = implode(',', $Pass_Taxes);
        } else {
            $tax_info = $pricing_array['air:AirPricingInfo']['air:TaxInfo'];

            $yqvalue = '';
            $YR_value = '';
            foreach ($tax_info as $tax) {
                if ($tax['@attributes']['Category'] == 'YQ') {
                    $yqvalue .= substr(@$tax['@attributes']['Amount'], 3);
                }
                if ($tax['@attributes']['Category'] == 'YR') {
                    $YR_value .= substr(@$tax['@attributes']['Amount'], 3);
                }
            }

            # Checking the value of YQ params
            if (!empty($yqvalue)) {
                $yqvalue = $yqvalue;
            } else {
                $yqvalue = '0.00';
            }

            # Checking the value of YR params
            if (!empty($YR_value)) {
                $YR_value = $YR_value;
            } else {
                $YR_value = '0.00';
            }


            $Passenger_type = $AirPricingInfo['air:PassengerType'];
            unset($Passenger_type['@value']);
            //debug($Passenger_type);exit;
            $AirPricingInfo_Attr = $AirPricingInfo['@attributes'];
            //debug($AirPricingInfo_Attr);exit;
            $AirPricingInfo_Attr = json_encode($AirPricingInfo_Attr);
            $AirPricingInfo_Attr = json_decode($AirPricingInfo_Attr);
            //debug($AirPricingInfo_Attr);exit;
            if (isset($AirPricingInfo_Attr->Refundable)) {
                $Refundable = $AirPricingInfo_Attr->Refundable;
            } else {
                $Refundable = 'false';
            }
            //debug($Passenger_type);
            foreach ($Passenger_type as $key => $Passenger_type_details) {
                //debug($Passenger_type_details);
                if (!empty($Passenger_type_details['@attributes'])) {
                    $All_Passenger[] = $Passenger_type_details['@attributes']['Code'];
                } else {
                    $All_Passenger[] = $Passenger_type_details['Code'];
                }

                // exit;
                $All_Passengers = implode(',', $All_Passenger);
                if (isset($AirPricingInfo_Attr->EquivalentBasePrice)) {
                    $BasePrice = substr($AirPricingInfo_Attr->EquivalentBasePrice, 3);
                    $BasePrice_Curr = substr($AirPricingInfo_Attr->EquivalentBasePrice, 0, 3);
                    //  $BasePrice = $this->flight_model->currency_convertor($BasePrice, $BasePrice_Curr, CURR);
                    $Pass_BasePrice[] = $BasePrice;
                } elseif (isset($AirPricingInfo_Attr->ApproximateBasePrice)) {
                    $BasePrice = substr($AirPricingInfo_Attr->ApproximateBasePrice, 3);
                    $BasePrice_Curr = substr($AirPricingInfo_Attr->ApproximateBasePrice, 0, 3);
                    //$BasePrice = $this->flight_model->currency_convertor($BasePrice, $BasePrice_Curr, CURR);
                    $Pass_BasePrice[] = $BasePrice;
                } else {
                    $BasePrice = substr($AirPricingInfo_Attr->BasePrice, 3);
                    $BasePrice_Curr = substr($AirPricingInfo_Attr->BasePrice, 0, 3);
                    //$BasePrice = $this->flight_model->currency_convertor($BasePrice, $BasePrice_Curr, CURR);
                    $Pass_BasePrice[] = $BasePrice;
                }
                if (isset($AirPricingInfo_Attr->ApproximateTaxes)) {
                    $Taxes = substr($AirPricingInfo_Attr->ApproximateTaxes, 3);
                    $Taxes_Curr = substr($AirPricingInfo_Attr->ApproximateTaxes, 0, 3);
                    //$Taxes = $this->flight_model->currency_convertor($Taxes, $Taxes_Curr, CURR);
                    $Pass_Taxes[] = $Taxes;
                } else {
                    $Taxes = substr($AirPricingInfo_Attr->Taxes, 3);
                    $Taxes_Curr = substr($AirPricingInfo_Attr->Taxes, 0, 3);
                    // $Taxes = $this->flight_model->currency_convertor($Taxes, $Taxes_Curr, CURR);
                    $Pass_Taxes[] = $Taxes;
                }
            }
            // $All_Passengers = implode(',', $All_Passenger);
            $Pass_BasePrice = implode(',', $Pass_BasePrice);
            $Pass_Taxes = implode(',', $Pass_Taxes);
        }
        //debug($Pass_BasePrice);exit;
        $passenger_count = array_count_values($All_Passenger);
        $split_base_price = explode(",", $Pass_BasePrice);
        $split_tax_price = explode(",", $Pass_Taxes);
        $flight_journey ['Price']['PassengerBreakup']['ADT']['PassengerCount'] = $passenger_count['ADT'];
        $flight_journey ['Price']['PassengerBreakup']['ADT']['BasePrice'] = $split_base_price[0];
        $flight_journey ['Price']['PassengerBreakup']['ADT']['Tax'] = $split_tax_price[0];
        $flight_journey ['Price']['PassengerBreakup']['ADT']['TotalPrice'] = $split_base_price[0] + $split_tax_price[0];
        if (isset($passenger_count['CNN'])) {
            $flight_journey ['Price']['PassengerBreakup']['CHD']['PassengerCount'] = $passenger_count['CNN'];
            $flight_journey ['Price']['PassengerBreakup']['CHD']['BasePrice'] = $split_base_price[1];
            $flight_journey ['Price']['PassengerBreakup']['CHD']['Tax'] = $split_tax_price[1];
            $flight_journey ['Price']['PassengerBreakup']['CHD']['TotalPrice'] = $split_base_price[1] + $split_tax_price[1];
        }
        if (isset($passenger_count['INF'])) {
            $flight_journey ['Price']['PassengerBreakup']['INF']['PassengerCount'] = $passenger_count['INF'];
            $flight_journey ['Price']['PassengerBreakup']['INF']['BasePrice'] = $split_base_price[2];
            $flight_journey ['Price']['PassengerBreakup']['INF']['Tax'] = $split_tax_price[2];
            $flight_journey ['Price']['PassengerBreakup']['INF']['TotalPrice'] = $split_base_price[2] + $split_tax_price[2];
        }
        //$flight_journey ['connections'] = $connection;
        //$flight_journey ['route_id'] = $pricing_array ['@attributes'] ['Key'];
        //$flight_journey ['vendor'] = '';
        //$flight_journey ['provider_code'] = @$air_pricing_info ['@attributes'] ['ProviderCode'];
        //$flight_journey ['air_pricing_key'] = @$air_pricing_info ['@attributes'] ['Key'];
        // debug($pricing_array);exit;
        // Built price array like travelfusion response
        $currency = substr($pricing_array ['@attributes'] ['TotalPrice'], 0, 3);
        $display_price = substr(@$pricing_array ['@attributes'] ['TotalPrice'], 3);
        $total_tax = substr(@$pricing_array ['@attributes'] ['ApproximateTaxes'], 3);
        $approx_base_price = substr($pricing_array ['@attributes'] ['ApproximateBasePrice'], 3);



        //$flight_journey ['fare'] [0] = $flight_journey ['price'];

        $flight_key_list = array();
        $flight_detail_arr = array();
        /* air journey flight key for onward and return gropu */

        if (isset($journey_detail) && valid_array($journey_detail)) {
            $flight_detail_key = 0;
            // debug($journey_detail);exit;
            //$arrBaggageData = array();
            foreach ($journey_detail as $j_key => $journey_flight) {

                if ($j_key == 0) {
                    $trip_type = 'onward';
                } else {
                    $trip_type = 'return';
                }

                // debug($arrBaggageData);
                $air_segment_ref = force_multple_data_format(@$journey_flight ['air:AirSegmentRef']);

                if (isset($air_segment_ref) && valid_array($air_segment_ref)) {
                    //debug($air_segment_ref);exit;
                    foreach ($air_segment_ref as $seg_key => $segmnt) {
                        if (isset($air_segment_list) && !empty($air_segment_list)) {
                            // debug($air_segment_list);exit;
                            // debug($air_segment_list);exit;
                            foreach ($air_segment_list as $as_key => $segment) {
                                //Agent Commission
                                // echo 'Base:'.$approx_base_price;
                                // echo "<br/>";
                                // echo 'YQ:'.$yq_value;
                                // echo "<br/>";
                                // echo 'Agent'.$agent_commssion;
                                // echo "<br/>";
                                // echo 'TDS'.$tds_commission;
                                // echo "<br/>";
                                // debug($check_comm);exit;

                                $air_segment_arr ['flight_detail_ref'] = $flight_detail_key = @$segment ['air:FlightDetailsRef'] ['@attributes'] ['Key'];
                                $air_code_share_info = @$segment ['air:CodeshareInfo'];
                                $is_leg = false;
                                $segment_ref = @$segment ['@attributes'] ['Key'];
                                // if(isset($air_segment_ref) && valid_array($air_segment_ref)) {
                                // foreach($air_segment_ref as $seg_key => $segmnt) {

                                $flight_key = @$segmnt ['@attributes'] ['Key'];
                                $flight_detail_arr = array();
                                if ($flight_key == $segment_ref) {
                                    if (isset($air_code_share_info) && valid_array($air_code_share_info)) {
                                        $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['air_code_share'] = @$air_code_share_info ['@attributes'] ['OperatingCarrier'];
                                    }
                                    // booking_code
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['booking_counts'] = @$segment ['air:AirAvailInfo'] ['air:BookingCodeInfo'] ['@attributes'] ['BookingCounts'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['group'] = @$segment ['@attributes'] ['Group'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['carrier'] = @$segment ['@attributes'] ['Carrier'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['flight_number'] = @$segment ['@attributes'] ['FlightNumber'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['origin'] = @$segment ['@attributes'] ['Origin'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['destination'] = @$segment ['@attributes'] ['Destination'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['departure_time'] = @$segment ['@attributes'] ['DepartureTime'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['arrival_time'] = @$segment ['@attributes'] ['ArrivalTime'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['flight_time'] = @$segment ['@attributes'] ['FlightTime'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['distance'] = @$segment ['@attributes'] ['Distance'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['e_ticketability'] = @$segment ['@attributes'] ['ETicketability'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['equipment'] = @$segment ['@attributes'] ['Equipment'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['change_of_plane'] = @$segment ['@attributes'] ['ChangeOfPlane'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['participant_level'] = @$segment ['@attributes'] ['ParticipantLevel'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['link_availability'] = @$segment ['@attributes'] ['LinkAvailability'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['polled_availability_option'] = @$segment ['@attributes'] ['PolledAvailabilityOption'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['optional_services_indicator'] = @$segment ['@attributes'] ['OptionalServicesIndicator'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['availability_source'] = @$segment ['@attributes'] ['AvailabilitySource'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['flight_seg_key'] = $flight_key;
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['provider_code'] = @$segment ['air:AirAvailInfo'] ['@attributes'] ['ProviderCode'];
                                    $flight_list_on_out ['flight_list'] [$j_key] ['flight_detail'] [$seg_key] ['flight_detail_key'] = $flight_detail_key;

                                    $flight_list_on_out ['connection'] = $connection;

                                    $flight_journey ['FlightDetails'] ['Details'][$j_key][] = $this->flight_detail_format($segment, $flight_key, $trip_type, $is_leg);
                                    // debug($flight_journey);exit;

                                    $flight_key_list [$j_key . '-' . $seg_key] = $flight_key;
                                    //debug($flight_key_list);exit;
                                    $flight_detail_key ++;
                                }
                                // }
                                // }
                            }
                        }
                    }
                }

                // $flight_journey['flight_details']['flight_list'][$j_key]['travel_time'] = $journey_flight['@attributes']['TravelTime'];
            }
        }
        // debug($flight_journey);exit;
        // exit;
        
        // Booking info

        if (isset($air_pricing_info) && valid_array($air_pricing_info)) {
            if (isset($air_pricing_info[0] ['air:BookingInfo'])) {
                $booking_details_flight = force_multple_data_format(@$air_pricing_info[0] ['air:BookingInfo']);
                if(isset($air_pricing_info[0]['air:BookingInfo'][0]))
                {
                    $BookingCode = @$air_pricing_info[0]['air:BookingInfo'][0]['@attributes']['BookingCode'];
                    $CabinClass = @$air_pricing_info[0]['air:BookingInfo'][0]['@attributes']['CabinClass'];
                }
                else 
                {
                    $BookingCode = @$air_pricing_info[0]['air:BookingInfo']['@attributes']['BookingCode'];
                    $CabinClass = @$air_pricing_info[0]['air:BookingInfo']['@attributes']['CabinClass'];
                }
                //$BookingCode = @$air_pricing_info[0] ['air:BookingInfo']['@attributes']['BookingCode'];
                //$CabinClass = @$air_pricing_info[0] ['air:BookingInfo']['@attributes']['CabinClass'];
            } else {

                $booking_details_flight = force_multple_data_format(@$air_pricing_info ['air:BookingInfo']);
                if(isset($air_pricing_info['air:BookingInfo'][0]))
                {
                $BookingCode = @$air_pricing_info['air:BookingInfo'][0]['@attributes']['BookingCode'];
                $CabinClass = @$air_pricing_info['air:BookingInfo'][0]['@attributes']['CabinClass'];
                }
                else 
                {
                $BookingCode = @$air_pricing_info['air:BookingInfo']['@attributes']['BookingCode'];
                $CabinClass = @$air_pricing_info['air:BookingInfo']['@attributes']['CabinClass'];
                }
            }

          
            if (isset($booking_details_flight) && valid_array($booking_details_flight)) {
                    $arrBaggageData[$strFare_Ref_Key]['AvailableSeats']=array();
                foreach ($booking_details_flight as $b_fkey => $booking) {
                    $key_index = array_search($booking ['@attributes'] ['SegmentRef'], $flight_key_list);
                    $explode_key = explode('-', $key_index);
                    $strFare_Ref_Key = $booking ['@attributes'] ['FareInfoRef'];
                    //$flight_journey ['FlightDetails'] ['Details'] [$b_fkey]['Attr'] = @$arrBaggageData[$strFare_Ref_Key];
                    $flight_journey ['FlightDetails'] ['Details'] [$explode_key [0]][$explode_key [1]]['operator_class'] = $booking ['@attributes'] ['BookingCode'];
                    $flight_journey ['FlightDetails'] ['Details'] [$explode_key [0]][$explode_key [1]]['CabinClass'] = $booking ['@attributes'] ['BookingCode'];
                    $flight_journey ['FlightDetails'] ['Details'] [$explode_key [0]][$explode_key [1]]['class'] ['name'] = $booking ['@attributes'] ['CabinClass'];
                    $arrBaggageData[$strFare_Ref_Key]['AvailableSeats']=$booking ['@attributes'] ['BookingCount'];
                    $flight_journey ['FlightDetails'] ['Details'] [$explode_key [0]][$explode_key [1]]['Attr'] = @$arrBaggageData[$strFare_Ref_Key];
                    $flight_journey ['FlightDetails'] ['Details'] [$explode_key [0]][$explode_key [1]]['fare_info_ref'] = $booking ['@attributes'] ['FareInfoRef'];
                    $flight_journey ['FlightDetails'] ['Details'] [$explode_key [0]][$explode_key [1]]['booking_code'] = $booking ['@attributes'] ['CabinClass'];
                    $flight_list_on_out ['flight_list'] [$explode_key [0]] ['flight_detail'] [$explode_key [1]] ['booking_code'] = $booking ['@attributes'] ['CabinClass'];
                    $flight_list_on_out ['flight_list'] [$explode_key [0]] ['flight_detail'] [$explode_key [1]] ['cabin_class'] = $booking ['@attributes'] ['CabinClass'];
                    $flight_list_on_out ['flight_list'] [$explode_key [0]] ['flight_detail'] [$explode_key [1]] ['fare_info_ref'] = $fare_info_ref = $booking ['@attributes'] ['FareInfoRef'];
                    $flight_list_on_out ['flight_list'] [$explode_key [0]] ['flight_detail'] [$explode_key [1]] ['fare_info_value'] = @$arrFareruleData[$fare_info_ref]['FareInfoValue'];
                    $flight_list_on_out ['flight_list'] [$explode_key [0]] ['flight_detail'] [$explode_key [1]] ['ProviderCode'] = @$arrFareruleData[$fare_info_ref]['ProviderCode'];
                }
            }

            //debug($flight_list_on_out);exit;
            // $AgentCommission = '0.00';
            // $AgentTdsOnCommision = '0.00';
            
            $carrier = @$flight_journey['FlightDetails']['Details'][0][0]['OperatorCode'];
            $check_comm = $CI->flight_model->check_commision($carrier, $search_data['is_domestic'], $this->booking_source);


            #Checking Booking Classes for adding Commission
            $ExceptClass = array();
            if (!empty($check_comm->except_classes)) {
                $ExceptClass = explode(',', $check_comm->except_classes);
            }


            # Checking Calculation for Economy Fare
            if (trim($CabinClass) == "Economy") {
                $iata_commssion=0;
                $PLB_commssion=0;
                #Add IATA Commission with Basic Fare
                $value_type="percentage";
                if ($check_comm->value_type == 'percentage') {
                    $iata_commssion = ($approx_base_price * $check_comm->iata) / 100;
                } else if ($check_comm->value_type == "plus") {
                    $iata_commssion = $check_comm->iata;
                } else {
                    $iata_commssion = 0;
                }

                #Add PLB Commission With Basic+ YQ/YR   
                $available_class = '';
                $available_class = array_search($BookingCode, $ExceptClass); // $key = 2;

                if (empty($available_class)) {

                    if ($check_comm->value_type == 'percentage') {

                        if ($check_comm->e_basic_plus_yq_type == "YQ") {
                            $YQ_value = $yqvalue;
                        }
                        if ($check_comm->e_basic_plus_yq_type == "YR") {
                            $YQ_value = $YR_value;
                        }

                        $basic_plus_yq = (($approx_base_price - $iata_commssion) + $YQ_value);

                        $PLB_commssion = ($basic_plus_yq * $check_comm->e_basic_plus_yq_value) / 100;
                    } else if ($check_comm->value_type == 'plus') {
                        $PLB_commssion = $check_comm->e_basic_plus_yq_value;
                    } else {
                        $PLB_commssion = 0;
                    }
                } else {
                    $PLB_commssion = 0;
                }
            }


            # Checking Calculation for Business Fare
            if (trim($CabinClass) == "Business") {
                $iata_commssion=0;
                $PLB_commssion=0;
                #Add IATA Commission with Basic Fare
                if ($check_comm->value_type == 'percentage') {
                    $iata_commssion = ($approx_base_price * $check_comm->iata) / 100;
                } else if ($check_comm->value_type == 'plus') {
                    $iata_commssion = $check_comm->iata;
                } else {
                    $iata_commssion = 0;
                }
                $available_class = '';
                $available_class = array_search($BookingCode, $ExceptClass); // $key = 2;

                if (empty($available_class)) {
                    #Add PLB Commission With Basic+ YQ/YR     
                    if ($check_comm->value_type == 'percentage') {

                        if ($check_comm->b_basic_plus_yq_type == "YQ") {
                            $YQ_value = $yqvalue;
                        }
                        if ($check_comm->b_basic_plus_yq_type == "YR") {
                            $YQ_value = $YR_value;
                        }

                        $basic_plus_yq = (($approx_base_price - $iata_commssion) + $YQ_value);

                        $PLB_commssion = ($basic_plus_yq * $check_comm->b_basic_plus_yq_value) / 100;
                    } else if ($check_comm->value_type == 'plus') {
                        $PLB_commssion = $check_comm->b_basic_plus_yq_value;
                    } else {
                        $PLB_commssion = 0;
                    }
                }
            }

            #Cash Back Option
            $CashBackAmount=0;
             $CashBack=$check_comm->cashback;
            if($CashBack > 0)
            {
                $CashBackAmount=$CashBack;
            }       


            $tds_commission = ($agent_commssion + $yq_value) * 5 / 100;
             $flight_journey ['Price'] ['Currency'] = $currency;
            $flight_journey ['Price'] ['TotalDisplayFare'] = $display_price;
            $flight_journey ['Price'] ['PriceBreakup'] ['Tax'] = $total_tax;
            $flight_journey ['Price'] ['PriceBreakup'] ['BasicFare'] = $approx_base_price;
            $flight_journey ['Price'] ['PriceBreakup'] ['YQValue'] = $yqvalue;
            
              $flight_journey ['Price'] ['PriceBreakup'] ['CommissionEarned'] = @$iata_commssion;
              $flight_journey ['Price'] ['PriceBreakup'] ['PLBEarned'] = (@$PLB_commssion + $CashBackAmount);
              $flight_journey ['Price'] ['PriceBreakup'] ['TdsOnCommission'] = ((@$iata_commssion * 5)/100);
              $flight_journey ['Price'] ['PriceBreakup'] ['TdsOnPLB'] = (((@$PLB_commssion+$CashBackAmount) * 5)/100);
            
            
            $flight_journey ['Price'] ['PriceBreakup'] ['AgentCommission'] = @$iata_commssion + @$PLB_commssion + $CashBackAmount;
            $flight_journey ['Price'] ['PriceBreakup'] ['AgentTdsOnCommision'] = ((@$iata_commssion * 5)/100)+((@$PLB_commssion * 5)/100) + (($CashBackAmount*5)/100);

            $flight_journey ['Price'] ['pax_breakup'] = array();
            $flight_journey ['Attr']['IsRefundable'] = $Refundable;
            $flight_journey ['Attr']['AirlineRemark'] = '';


            // Tax info
            // $tax_info = force_multple_data_format(@$air_pricing_info ['air:TaxInfo']);
            // if (isset($tax_info) && valid_array($tax_info)) {
            //     foreach ($tax_info as $t_key => $taxes) {
            //         $flight_journey ['taxes'] [$t_key] ['category'] = $taxes ['@attributes'] ['Category'];
            //         $flight_journey ['taxes'] [$t_key] ['amount'] = $taxes ['@attributes'] ['Amount'];
            //     }
            // }
        }

        $token_data [0] = $flight_list_on_out;
        $token_data [0]['booking_source'] = $this->booking_source;
        //debug($token_data);exit;
        $flight_journey ['ResultToken'] = serialized_data($token_data);
        $flight_journey ['booking_source'] = $this->booking_source;
        $flight_journey ['token_key'] = md5($flight_journey ['ResultToken']);
        $flight_journey ['flight_details']['summery'] = $flight_list_on_out;
        //debug($flight_list_on_out);exit;
        // Create flight summery
        if (isset($flight_list_on_out ['flight_list']) && valid_array($flight_list_on_out ['flight_list'])) {
            foreach ($flight_list_on_out ['flight_list'] as $fl_direction => $list) {
                //$flight_journey ['summary'] [] = $this->flight_segment_summary($list, $fl_direction);
            }
        }
        // debug($flight_journey);exit;
        //echo 'herre';exit;
        // $first_flight = current($flight_journey['flight_details']['details']);
        // $last_flight = end($flight_journey['flight_details']['details']);
        // $flight_journey['flight_details']['summery'] = $this->format_segment_summery($first_flight,$last_flight);
        // debug($first_flight);echo 'last';debug($last_flight);exit;
        // debug($flight_journey);echo '$$flight_journey';
        return $flight_journey;
    }

  private function flight_segment_summary($journey_detail, $air_seg_key, $air_price_result, $random_value, $search_id) {
        // debug($air_price_result);exit;
        $search_data = $this->search_data($search_id);
        // debug($search_data);exit;
        $CI = & get_instance();
        $air_segment_list = $journey_detail['air:AirSegment'];
        $air_pricing_info = @$air_price_result ['air:AirPricingInfo'];
        if (isset($air_pricing_info) && valid_array($air_pricing_info)) {
            if (isset($air_pricing_info[0] ['air:BookingInfo'])) {
                $booking_details_flight = force_multple_data_format(@$air_pricing_info[0] ['air:BookingInfo']);
            }
            else{
               $booking_details_flight = force_multple_data_format(@$air_pricing_info ['air:BookingInfo']); 
            }
        }
        // debug($booking_details_flight);exit;
        foreach($booking_details_flight as $flight_data){
            $fare_info[$flight_data['@attributes']['FareInfoRef']][] = $flight_data['@attributes']['SegmentRef'];
             
        }
        //debug($pricing_array);exit;
        $AirPricingInfo = $air_price_result['air:AirPricingInfo'];
        //debug($AirPricingInfo);exit;
        if (isset($air_price_result['air:AirPricingInfo'][0])) {
            $tax_info = $air_price_result['air:AirPricingInfo'][0]['air:TaxInfo'];
            $yqvalue = '';
            $YR_value = '';
            foreach ($tax_info as $tax) {
                if ($tax['@attributes']['Category'] == 'YQ') {
                    $yqvalue .= substr(@$tax['@attributes']['Amount'], 3);
                }
                if ($tax['@attributes']['Category'] == 'YR') {
                    $YR_value .= substr(@$tax['@attributes']['Amount'], 3);
                }
            }
            # Checking the value of YQ params
            if (!empty($yqvalue)) {
                $yqvalue = $yqvalue;
            } else {
                $yqvalue = '0.00';
            }


            # Checking the value of YR params
            if (!empty($YR_value)) {
                $YR_value = $YR_value;
            } else {
                $YR_value = '0.00';
            }

            $AirPricingInfo_Attr = $AirPricingInfo[0]['@attributes'];
            //debug($AirPricingInfo_Attr);exit;
            $AirPricingInfo_Attr = json_encode($AirPricingInfo_Attr);
            $AirPricingInfo_Attr = json_decode($AirPricingInfo_Attr);
            if (isset($AirPricingInfo_Attr->Refundable)) {
                $Refundable = $AirPricingInfo_Attr->Refundable;
            } else {
                $Refundable = false;
            }
            $AirPricingInfos = $AirPricingInfo;
            foreach ($AirPricingInfos as $key => $AirPricingInfo) {
                $Passenger_type = $AirPricingInfo['air:PassengerType'];
                unset($Passenger_type['@value']);
                foreach ($Passenger_type as $key => $Passenger_type_details) {
                    if (isset($Passenger_type_details['@attributes'])) {
                        $All_Passenger[] = $Passenger_type_details['@attributes']['Code'];
                    } else {
                        $All_Passenger[] = $Passenger_type_details['Code'];
                    }
                }

                if (isset($AirPricingInfo['@attributes']['EquivalentBasePrice'])) {
                    $BasePrice = substr($AirPricingInfo['@attributes']['EquivalentBasePrice'], 3);
                    $BasePrice_Curr = substr($AirPricingInfo['@attributes']['EquivalentBasePrice'], 0, 3);
                    // $BasePrice = $this->flight_model->currency_convertor($BasePrice, $BasePrice_Curr, CURR);
                    $Pass_BasePrice[] = $BasePrice;
                } elseif (isset($AirPricingInfo['@attributes']['ApproximateBasePrice'])) {
                    $BasePrice = substr($AirPricingInfo['@attributes']['ApproximateBasePrice'], 3);
                    $BasePrice_Curr = substr($AirPricingInfo['@attributes']['ApproximateBasePrice'], 0, 3);
                    //$BasePrice = $this->flight_model->currency_convertor($BasePrice, $BasePrice_Curr, CURR);
                    $Pass_BasePrice[] = $BasePrice;
                } else {
                    $BasePrice = substr($AirPricingInfo['@attributes']['BasePrice'], 3);
                    $BasePrice_Curr = substr($AirPricingInfo['@attributes']['BasePrice'], 0, 3);
                    //$BasePrice = $this->flight_model->currency_convertor($BasePrice, $BasePrice_Curr, CURR);
                    $Pass_BasePrice[] = $BasePrice;
                }
                if (isset($AirPricingInfo['@attributes']['ApproximateTaxes'])) {
                    $Taxes = substr($AirPricingInfo['@attributes']['ApproximateTaxes'], 3);
                    $Taxes_Curr = substr($AirPricingInfo['@attributes']['ApproximateTaxes'], 0, 3);
                    //$Taxes = $this->flight_model->currency_convertor($Taxes, $Taxes_Curr, CURR);
                    $Pass_Taxes[] = $Taxes;
                } else {
                    if (isset($AirPricingInfo['@attributes']['Taxes'])) {
                        $Taxes = substr($AirPricingInfo['@attributes']['Taxes'], 3);
                        $Taxes_Curr = substr($AirPricingInfo['@attributes']['Taxes'], 0, 3);
                        //$Taxes = $this->flight_model->currency_convertor($Taxes, $Taxes_Curr, CURR);
                    } else {
                        $Taxes = 0;
                    }
                    $Pass_Taxes[] = $Taxes;
                }
            }
            $All_Passengers = implode(',', $All_Passenger);
            $Pass_BasePrice = implode(',', $Pass_BasePrice);
            $Pass_Taxes = implode(',', $Pass_Taxes);
        } else {
            $tax_info = $air_price_result['air:AirPricingInfo']['air:TaxInfo'];
            $yqvalue = '';
            $YR_value = '';
            foreach ($tax_info as $tax) {
                if ($tax['@attributes']['Category'] == 'YQ') {
                    $yqvalue .= substr(@$tax['@attributes']['Amount'], 3);
                }
                if ($tax['@attributes']['Category'] == 'YR') {
                    $YR_value .= substr(@$tax['@attributes']['Amount'], 3);
                }
            }

            # Checking the value of YQ params
            if (!empty($yqvalue)) {
                $yqvalue = $yqvalue;
            } else {
                $yqvalue = '0.00';
            }

            # Checking the value of YR params
            if (!empty($YR_value)) {
                $YR_value = $YR_value;
            } else {
                $YR_value = '0.00';
            }
            $Passenger_type = $AirPricingInfo['air:PassengerType'];
            unset($Passenger_type['@value']);
            //debug($Passenger_type);exit;
            $AirPricingInfo_Attr = $AirPricingInfo['@attributes'];
            //debug($AirPricingInfo_Attr);exit;
            $AirPricingInfo_Attr = json_encode($AirPricingInfo_Attr);
            $AirPricingInfo_Attr = json_decode($AirPricingInfo_Attr);
            //debug($AirPricingInfo_Attr);exit;
            if (isset($AirPricingInfo_Attr->Refundable)) {
                $Refundable = $AirPricingInfo_Attr->Refundable;
            } else {
                $Refundable = false;
            }
            //debug($Passenger_type);
            foreach ($Passenger_type as $key => $Passenger_type_details) {
                //debug($Passenger_type_details);
                if (!empty($Passenger_type_details['@attributes'])) {
                    $All_Passenger[] = $Passenger_type_details['@attributes']['Code'];
                } else {
                    $All_Passenger[] = $Passenger_type_details['Code'];
                }

                // exit;
                $All_Passengers = implode(',', $All_Passenger);
                if (isset($AirPricingInfo_Attr->EquivalentBasePrice)) {
                    $BasePrice = substr($AirPricingInfo_Attr->EquivalentBasePrice, 3);
                    $BasePrice_Curr = substr($AirPricingInfo_Attr->EquivalentBasePrice, 0, 3);
                    //  $BasePrice = $this->flight_model->currency_convertor($BasePrice, $BasePrice_Curr, CURR);
                    $Pass_BasePrice[] = $BasePrice;
                } elseif (isset($AirPricingInfo_Attr->ApproximateBasePrice)) {
                    $BasePrice = substr($AirPricingInfo_Attr->ApproximateBasePrice, 3);
                    $BasePrice_Curr = substr($AirPricingInfo_Attr->ApproximateBasePrice, 0, 3);
                    //$BasePrice = $this->flight_model->currency_convertor($BasePrice, $BasePrice_Curr, CURR);
                    $Pass_BasePrice[] = $BasePrice;
                } else {
                    $BasePrice = substr($AirPricingInfo_Attr->BasePrice, 3);
                    $BasePrice_Curr = substr($AirPricingInfo_Attr->BasePrice, 0, 3);
                    //$BasePrice = $this->flight_model->currency_convertor($BasePrice, $BasePrice_Curr, CURR);
                    $Pass_BasePrice[] = $BasePrice;
                }
                if (isset($AirPricingInfo_Attr->ApproximateTaxes)) {
                    $Taxes = substr($AirPricingInfo_Attr->ApproximateTaxes, 3);
                    $Taxes_Curr = substr($AirPricingInfo_Attr->ApproximateTaxes, 0, 3);
                    //$Taxes = $this->flight_model->currency_convertor($Taxes, $Taxes_Curr, CURR);
                    $Pass_Taxes[] = $Taxes;
                } else {
                    $Taxes = substr($AirPricingInfo_Attr->Taxes, 3);
                    $Taxes_Curr = substr($AirPricingInfo_Attr->Taxes, 0, 3);
                    // $Taxes = $this->flight_model->currency_convertor($Taxes, $Taxes_Curr, CURR);
                    $Pass_Taxes[] = $Taxes;
                }
            }
            // $All_Passengers = implode(',', $All_Passenger);
            $Pass_BasePrice = implode(',', $Pass_BasePrice);
            $Pass_Taxes = implode(',', $Pass_Taxes);
        }
        //debug($Pass_BasePrice);exit;
        $passenger_count = array_count_values($All_Passenger);
        $split_base_price = explode(",", $Pass_BasePrice);
        $split_tax_price = explode(",", $Pass_Taxes);
        $flight_journey ['Price']['PassengerBreakup']['ADT']['PassengerCount'] = $passenger_count['ADT'];
        $flight_journey ['Price']['PassengerBreakup']['ADT']['BasePrice'] = $passenger_count['ADT']*$split_base_price[0];
        $flight_journey ['Price']['PassengerBreakup']['ADT']['Tax'] = $split_tax_price[0];
        $flight_journey ['Price']['PassengerBreakup']['ADT']['TotalPrice'] = $split_base_price[0] + $split_tax_price[0];
        if (isset($passenger_count['CNN'])) {
            $flight_journey ['Price']['PassengerBreakup']['CHD']['PassengerCount'] = $passenger_count['CNN'];
            $flight_journey ['Price']['PassengerBreakup']['CHD']['BasePrice'] = $passenger_count['CNN']*$split_base_price[1];
            $flight_journey ['Price']['PassengerBreakup']['CHD']['Tax'] = $split_tax_price[1];
            $flight_journey ['Price']['PassengerBreakup']['CHD']['TotalPrice'] = $split_base_price[1] + $split_tax_price[1];
        }
        if (isset($passenger_count['INF'])) {
            $flight_journey ['Price']['PassengerBreakup']['INF']['PassengerCount'] = $passenger_count['INF'];
            $flight_journey ['Price']['PassengerBreakup']['INF']['BasePrice'] = $passenger_count['INF']*$split_base_price[2];
            $flight_journey ['Price']['PassengerBreakup']['INF']['Tax'] = $split_tax_price[2];
            $flight_journey ['Price']['PassengerBreakup']['INF']['TotalPrice'] = $split_base_price[2] + $split_tax_price[2];
        }
        //$flight_journey ['connections'] = $connection;
        //$flight_journey ['route_id'] = $pricing_array ['@attributes'] ['Key'];
        //$flight_journey ['vendor'] = '';
        //$flight_journey ['provider_code'] = @$air_pricing_info ['@attributes'] ['ProviderCode'];
        //$flight_journey ['air_pricing_key'] = @$air_pricing_info ['@attributes'] ['Key'];
        // Built price array like travelfusion response
        $currency = substr($air_price_result ['@attributes'] ['TotalPrice'], 0, 3);
        $display_price = substr(@$air_price_result ['@attributes'] ['TotalPrice'], 3);
        $total_tax = substr(@$air_price_result ['@attributes'] ['ApproximateTaxes'], 3);
        $approx_base_price = substr($air_price_result ['@attributes'] ['ApproximateBasePrice'], 3);



        $flight_key_list = array();
        $flight_detail_arr = array();
        /* air journey flight key for onward and return gropu */

        //if (isset($air_segment_list) && valid_array($air_segment_list)) {
        $flight_detail_key = 0;
        if (isset($air_segment_list[0])) {

            $air_segment_list1 = $air_segment_list;
        } else {

            $air_segment_list1[0] = $air_segment_list;
        }

        // debug($air_segment_list1);exit;
        //$arrBaggageData = array();
        //foreach ($journey_detail as $j_key => $journey_flight) {
        // debug($air_segment_list);exit;
        //$air_segment_ref = force_multple_data_format(@$journey_flight ['air:AirSegmentRef']);
        //if (isset($air_segment_ref) && valid_array($air_segment_ref)) {
        //debug($air_segment_ref);exit;
        $flight_list_on_out = array();
        if($search_data['data']['trip_type'] == 'return' && $search_data['data']['domestic_round_trip'] == 0){
            $key = 0;
            foreach ($fare_info as $key1 => $fare_data) {
                 if (isset($air_segment_list1) && !empty($air_segment_list1)) {
                    // debug($air_segment_list1);exit;
                    if($fare_data){
                        $seg_key1 = 0;
                        foreach ($air_segment_list1 as $seg_key => $segment) {
                            if (in_array($segment ['@attributes'] ['Key'], $fare_data)) {
                                $air_segment_arr ['flight_detail_ref'] = $flight_detail_key = @$segment ['air:FlightDetailsRef'] ['@attributes'] ['Key'];
                                $air_code_share_info = @$segment ['air:CodeshareInfo'];
                                $is_leg = false;
                                $segment_ref = @$segment ['@attributes'] ['Key'];
                                // if(isset($air_segment_ref) && valid_array($air_segment_ref)) {
                                // foreach($air_segment_ref as $seg_key => $segmnt) {

                                $flight_key = @$segment ['@attributes'] ['Key'];

                                $flight_detail_arr = array();
                                //if ($flight_key == $segment_ref) {
                                if (isset($air_code_share_info) && valid_array($air_code_share_info)) {
                                    $flight_list_on_out ['flight_list'] [$key] ['flight_detail'] [$seg_key] ['air_code_share'] = @$air_code_share_info ['@attributes'] ['OperatingCarrier'];
                                }
                                // booking_code
                                $flight_list_on_out ['flight_list'] [$key] ['flight_detail'] [$seg_key] ['random_number'] = $random_value;
                                $flight_list_on_out ['flight_list'] [$key] ['flight_detail'] [$seg_key] ['booking_counts'] = @$segment ['air:AirAvailInfo'] ['air:BookingCodeInfo'] ['@attributes'] ['BookingCounts'];
                                $flight_list_on_out ['flight_list'] [$key] ['flight_detail'] [$seg_key] ['group'] = @$segment ['@attributes'] ['Group'];
                                $flight_list_on_out ['flight_list'] [$key] ['flight_detail'] [$seg_key] ['carrier'] = @$segment ['@attributes'] ['Carrier'];
                                $flight_list_on_out ['flight_list'] [$key] ['flight_detail'] [$seg_key] ['flight_number'] = @$segment ['@attributes'] ['FlightNumber'];
                                $flight_list_on_out ['flight_list'] [$key] ['flight_detail'] [$seg_key] ['origin'] = @$segment ['@attributes'] ['Origin'];
                                $flight_list_on_out ['flight_list'] [$key] ['flight_detail'] [$seg_key] ['destination'] = @$segment ['@attributes'] ['Destination'];
                                $flight_list_on_out ['flight_list'] [$key] ['flight_detail'] [$seg_key] ['departure_time'] = @$segment ['@attributes'] ['DepartureTime'];
                                $flight_list_on_out ['flight_list'] [$key] ['flight_detail'] [$seg_key] ['arrival_time'] = @$segment ['@attributes'] ['ArrivalTime'];
                                $flight_list_on_out ['flight_list'] [$key] ['flight_detail'] [$seg_key] ['flight_time'] = @$segment ['@attributes'] ['FlightTime'];
                                $flight_list_on_out ['flight_list'] [$key] ['flight_detail'] [$seg_key] ['distance'] = @$segment ['@attributes'] ['Distance'];
                                $flight_list_on_out ['flight_list'] [$key] ['flight_detail'] [$seg_key] ['e_ticketability'] = @$segment ['@attributes'] ['ETicketability'];
                                $flight_list_on_out ['flight_list'] [$key] ['flight_detail'] [$seg_key] ['equipment'] = @$segment ['@attributes'] ['Equipment'];
                                $flight_list_on_out ['flight_list'] [$key] ['flight_detail'] [$seg_key] ['change_of_plane'] = @$segment ['@attributes'] ['ChangeOfPlane'];
                                $flight_list_on_out ['flight_list'] [$key] ['flight_detail'] [$seg_key] ['participant_level'] = @$segment ['@attributes'] ['ParticipantLevel'];
                                $flight_list_on_out ['flight_list'] [$key] ['flight_detail'] [$seg_key] ['link_availability'] = @$segment ['@attributes'] ['LinkAvailability'];
                                $flight_list_on_out ['flight_list'] [$key] ['flight_detail'] [$seg_key] ['polled_availability_option'] = @$segment ['@attributes'] ['PolledAvailabilityOption'];
                                $flight_list_on_out ['flight_list'] [$key] ['flight_detail'] [$seg_key] ['optional_services_indicator'] = @$segment ['@attributes'] ['OptionalServicesIndicator'];
                                $flight_list_on_out ['flight_list'] [$key] ['flight_detail'] [$seg_key] ['availability_source'] = @$segment ['@attributes'] ['AvailabilitySource'];
                                $flight_list_on_out ['flight_list'] [$key] ['flight_detail'] [$seg_key] ['flight_seg_key'] = $flight_key;
                                $flight_list_on_out ['flight_list'] [$key] ['flight_detail'] [$seg_key] ['provider_code'] = @$segment ['air:AirAvailInfo'] ['@attributes'] ['ProviderCode'];
                                $flight_list_on_out ['flight_list'] [$key] ['flight_detail'] [$seg_key] ['flight_detail_key'] = $flight_detail_key;

                                //$flight_list_on_out ['connection'] = $connection;

                                $flight_journey ['FlightDetails'] ['Details'][$key][] = $this->flight_detail_format($segment, $flight_key, $trip_type = '', $is_leg);

                                $flight_key_list [$key . '-' . $seg_key1] = $flight_key;
                                //debug($flight_key_list);exit;
                                $flight_detail_key ++;
                                //}
                                // }
                                $seg_key1++;
                            }
                        }
                    }
                }
            $key++;
            }
        }
        else{
         
            if (isset($air_segment_list1) && !empty($air_segment_list1)) {
            // debug($air_segment_list1);exit;
                foreach ($air_segment_list1 as $seg_key => $segment) {
                    if (in_array($segment ['@attributes'] ['Key'], $air_seg_key)) {
                        $air_segment_arr ['flight_detail_ref'] = $flight_detail_key = @$segment ['air:FlightDetailsRef'] ['@attributes'] ['Key'];
                        $air_code_share_info = @$segment ['air:CodeshareInfo'];
                        $is_leg = false;
                        $segment_ref = @$segment ['@attributes'] ['Key'];
                        // if(isset($air_segment_ref) && valid_array($air_segment_ref)) {
                        // foreach($air_segment_ref as $seg_key => $segmnt) {

                        $flight_key = @$segment ['@attributes'] ['Key'];

                        $flight_detail_arr = array();
                        //if ($flight_key == $segment_ref) {
                        if (isset($air_code_share_info) && valid_array($air_code_share_info)) {
                            $flight_list_on_out ['flight_list'] [$key] ['flight_detail'] [$seg_key] ['air_code_share'] = @$air_code_share_info ['@attributes'] ['OperatingCarrier'];
                        }
                        // booking_code
                        $flight_list_on_out ['flight_list'] [0] ['flight_detail'] [$seg_key] ['random_number'] = $random_value;
                        $flight_list_on_out ['flight_list'] [0] ['flight_detail'] [$seg_key] ['booking_counts'] = @$segment ['air:AirAvailInfo'] ['air:BookingCodeInfo'] ['@attributes'] ['BookingCounts'];
                        $flight_list_on_out ['flight_list'] [0] ['flight_detail'] [$seg_key] ['group'] = @$segment ['@attributes'] ['Group'];
                        $flight_list_on_out ['flight_list'] [0] ['flight_detail'] [$seg_key] ['carrier'] = @$segment ['@attributes'] ['Carrier'];
                        $flight_list_on_out ['flight_list'] [0] ['flight_detail'] [$seg_key] ['flight_number'] = @$segment ['@attributes'] ['FlightNumber'];
                        $flight_list_on_out ['flight_list'] [0] ['flight_detail'] [$seg_key] ['origin'] = @$segment ['@attributes'] ['Origin'];
                        $flight_list_on_out ['flight_list'] [0] ['flight_detail'] [$seg_key] ['destination'] = @$segment ['@attributes'] ['Destination'];
                        $flight_list_on_out ['flight_list'] [0] ['flight_detail'] [$seg_key] ['departure_time'] = @$segment ['@attributes'] ['DepartureTime'];
                        $flight_list_on_out ['flight_list'] [0] ['flight_detail'] [$seg_key] ['arrival_time'] = @$segment ['@attributes'] ['ArrivalTime'];
                        $flight_list_on_out ['flight_list'] [0] ['flight_detail'] [$seg_key] ['flight_time'] = @$segment ['@attributes'] ['FlightTime'];
                        $flight_list_on_out ['flight_list'] [0] ['flight_detail'] [$seg_key] ['distance'] = @$segment ['@attributes'] ['Distance'];
                        $flight_list_on_out ['flight_list'] [0] ['flight_detail'] [$seg_key] ['e_ticketability'] = @$segment ['@attributes'] ['ETicketability'];
                        $flight_list_on_out ['flight_list'] [0] ['flight_detail'] [$seg_key] ['equipment'] = @$segment ['@attributes'] ['Equipment'];
                        $flight_list_on_out ['flight_list'] [0] ['flight_detail'] [$seg_key] ['change_of_plane'] = @$segment ['@attributes'] ['ChangeOfPlane'];
                        $flight_list_on_out ['flight_list'] [0] ['flight_detail'] [$seg_key] ['participant_level'] = @$segment ['@attributes'] ['ParticipantLevel'];
                        $flight_list_on_out ['flight_list'] [0] ['flight_detail'] [$seg_key] ['link_availability'] = @$segment ['@attributes'] ['LinkAvailability'];
                        $flight_list_on_out ['flight_list'] [0] ['flight_detail'] [$seg_key] ['polled_availability_option'] = @$segment ['@attributes'] ['PolledAvailabilityOption'];
                        $flight_list_on_out ['flight_list'] [0] ['flight_detail'] [$seg_key] ['optional_services_indicator'] = @$segment ['@attributes'] ['OptionalServicesIndicator'];
                        $flight_list_on_out ['flight_list'] [0] ['flight_detail'] [$seg_key] ['availability_source'] = @$segment ['@attributes'] ['AvailabilitySource'];
                        $flight_list_on_out ['flight_list'] [0] ['flight_detail'] [$seg_key] ['flight_seg_key'] = $flight_key;
                        $flight_list_on_out ['flight_list'] [0] ['flight_detail'] [$seg_key] ['provider_code'] = @$segment ['air:AirAvailInfo'] ['@attributes'] ['ProviderCode'];
                        $flight_list_on_out ['flight_list'] [0] ['flight_detail'] [$seg_key] ['flight_detail_key'] = $flight_detail_key;

                        //$flight_list_on_out ['connection'] = $connection;

                        $flight_journey ['FlightDetails'] ['Details'][0][] = $this->flight_detail_format($segment, $flight_key, $trip_type = '', $is_leg);

                        $flight_key_list [0 . '-' . $seg_key] = $flight_key;
                        //debug($flight_key_list);exit;
                        $flight_detail_key ++;
                    }
                }
            }
        }

        if (isset($air_pricing_info) && valid_array($air_pricing_info)) {
            if (isset($air_pricing_info[0] ['air:BookingInfo'])) {
                $booking_details_flight = force_multple_data_format(@$air_pricing_info[0] ['air:BookingInfo']);
                $air_baggage_allow_arr = force_multple_data_format(@$air_pricing_info[0]['air:BaggageAllowances']['air:BaggageAllowanceInfo']);
                if(isset($air_pricing_info[0]['air:BookingInfo'][0]))
                {
                    $BookingCode = @$air_pricing_info[0]['air:BookingInfo'][0]['@attributes']['BookingCode'];
                    $CabinClass = @$air_pricing_info[0]['air:BookingInfo'][0]['@attributes']['CabinClass'];
                }
                else 
                {
                    $BookingCode = @$air_pricing_info[0]['air:BookingInfo']['@attributes']['BookingCode'];
                    $CabinClass = @$air_pricing_info[0]['air:BookingInfo']['@attributes']['CabinClass'];
                }
                //$BookingCode = @$air_pricing_info[0] ['air:BookingInfo']['@attributes']['BookingCode'];
                //$CabinClass = @$air_pricing_info[0] ['air:BookingInfo']['@attributes']['CabinClass'];
            } else {
                $booking_details_flight = force_multple_data_format(@$air_pricing_info ['air:BookingInfo']);
                $air_baggage_allow_arr = force_multple_data_format(@$air_pricing_info ['air:BaggageAllowances']['air:BaggageAllowanceInfo']);
                 if(isset($air_pricing_info['air:BookingInfo'][0]))
                {
                $BookingCode = @$air_pricing_info['air:BookingInfo'][0]['@attributes']['BookingCode'];
                $CabinClass = @$air_pricing_info['air:BookingInfo'][0]['@attributes']['CabinClass'];
                }
                else 
                {
                $BookingCode = @$air_pricing_info['air:BookingInfo']['@attributes']['BookingCode'];
                $CabinClass = @$air_pricing_info['air:BookingInfo']['@attributes']['CabinClass'];
                }
            }


            $arrBaggageData = array();
            $arrBaggageDataValue = array();
            if (isset($air_baggage_allow_arr) && valid_array($air_baggage_allow_arr)) {
                foreach ($air_baggage_allow_arr as $bg_al_k => $baggage_info) {
                    //debug($baggage_info);exit;
                    $arrBaggageData [$bg_al_k] ['@attributes'] ['TravelerType'] = $baggage_info ['@attributes'] ['TravelerType'];
                    $arrBaggageData [$bg_al_k] ['@attributes'] ['Origin'] = $baggage_info ['@attributes'] ['Origin'];
                    $arrBaggageData [$bg_al_k] ['@attributes'] ['Destination'] = $baggage_info ['@attributes'] ['Destination'];
                    $arrBaggageData [$bg_al_k] ['@attributes'] ['Carrier'] = $baggage_info ['@attributes'] ['Carrier'];
                    $arrBaggageData [$bg_al_k] ['URLInfo'] ['URL'] = @$baggage_info ['air:URLInfo'] ['air:URL'];

                    // taxt
                    $taxt_info_arr = force_multple_data_format($baggage_info ['air:TextInfo'] ['air:Text']);
                    $baggage_value = 0;
                    if (isset($taxt_info_arr) && valid_array($taxt_info_arr)) {
                        foreach ($taxt_info_arr as $tx_k => $txt_val) {
                            $baggage = explode('K', $txt_val);
                            $arrBaggageData [$bg_al_k] ['TextInfo'] ['Text'] [$tx_k] = @$txt_val;
                            $baggage_value += $baggage['0'];
                            $arrBaggageDataValue ['Baggagess'] = $baggage_value . ' Kg';
                            $arrBaggageDataValue ['CabinBaggage'] = 0;
                        }
                    }
                }
            }
            //debug($arrBaggageDataValue);exit;
            if (isset($booking_details_flight) && valid_array($booking_details_flight)) {

                foreach ($booking_details_flight as $b_fkey => $booking) {
                    $key_index = array_search($booking ['@attributes'] ['SegmentRef'], $flight_key_list);

                    $explode_key = explode('-', $key_index);
                    $strFare_Ref_Key = $booking ['@attributes'] ['FareInfoRef'];
                    //$flight_journey ['FlightDetails'] ['Details'] [$b_fkey]['Attr'] = @$arrBaggageData[$strFare_Ref_Key];
                    $flight_journey ['FlightDetails'] ['Details'] [$explode_key [0]][$explode_key [1]]['operator_class'] = $booking ['@attributes'] ['CabinClass'];
                    $flight_journey ['FlightDetails'] ['Details'] [$explode_key [0]][$explode_key [1]]['CabinClass'] = $booking ['@attributes'] ['CabinClass'];
                    $flight_journey ['FlightDetails'] ['Details'] [$explode_key [0]][$explode_key [1]]['class'] ['name'] = $booking ['@attributes'] ['CabinClass'];
                    $flight_journey ['FlightDetails'] ['Details'] [$explode_key [0]][$explode_key [1]]['Attr'] = @$arrBaggageDataValue;
                    $flight_journey ['FlightDetails'] ['Details'] [$explode_key [0]][$explode_key [1]]['fare_info_ref'] = $booking ['@attributes'] ['FareInfoRef'];
                    $flight_journey ['FlightDetails'] ['Details'] [$explode_key [0]][$explode_key [1]]['booking_code'] = $booking ['@attributes'] ['BookingCode'];
                    $flight_list_on_out ['flight_list'] [$explode_key [0]] ['flight_detail'] [$explode_key [1]] ['booking_code'] = $booking ['@attributes'] ['BookingCode'];
                    $flight_list_on_out ['flight_list'] [$explode_key [0]] ['flight_detail'] [$explode_key [1]] ['cabin_class'] = $booking ['@attributes'] ['CabinClass'];
                    $flight_list_on_out ['flight_list'] [$explode_key [0]] ['flight_detail'] [$explode_key [1]] ['fare_info_ref'] = $fare_info_ref = $booking ['@attributes'] ['FareInfoRef'];
                    $flight_list_on_out ['flight_list'] [$explode_key [0]] ['flight_detail'] [$explode_key [1]] ['fare_info_value'] = @$arrFareruleData[$fare_info_ref]['FareInfoValue'];
                    $flight_list_on_out ['flight_list'] [$explode_key [0]] ['flight_detail'] [$explode_key [1]] ['ProviderCode'] = @$arrFareruleData[$fare_info_ref]['ProviderCode'];
                }
            }

            //debug($flight_list_on_out);exit;
            $carrier = @$flight_journey['FlightDetails']['Details'][0][0]['OperatorCode'];
            if (!empty($search_data['data']['is_domestic']) && $search_data['data']['is_domestic'] == 1) {
                $is_domestic = 1;
            } else {
                $is_domestic = 0;
            }
            $check_comm = $CI->flight_model->check_commision($carrier, $is_domestic, $this->booking_source);


            #Checking Booking Classes for adding Commission
            $ExceptClass = array();
            if (!empty($check_comm->except_classes)) {
                $ExceptClass = explode(',', $check_comm->except_classes);
            }


            # Checking Calculation for Economy Fare
            if (trim($CabinClass) == "Economy") {
                $iata_commssion = 0;
                $PLB_commssion = 0;
                #Add IATA Commission with Basic Fare
                if ($check_comm->value_type == 'percentage') {
                    $iata_commssion = ($approx_base_price * $check_comm->iata) / 100;
                } else if ($check_comm->value_type == 'plus') {
                    $iata_commssion = $check_comm->iata;
                } else {
                    $iata_commssion = 0;
                }

                #Add PLB Commission With Basic+ YQ/YR   
                $available_class = '';
                $available_class = array_search($BookingCode, $ExceptClass); // $key = 2;

                if (empty($available_class)) {

                    if ($check_comm->value_type == 'percentage') {

                        if ($check_comm->e_basic_plus_yq_type == "YQ") {
                            $YQ_value = $yqvalue;
                        }
                        if ($check_comm->e_basic_plus_yq_type == "YR") {
                            $YQ_value = $YR_value;
                        }

                        $basic_plus_yq = (($approx_base_price - $iata_commssion) + $YQ_value);

                        $PLB_commssion = ($basic_plus_yq * $check_comm->e_basic_plus_yq_value) / 100;
                    } else if ($check_comm->value_type == 'plus') {
                        $PLB_commssion = $check_comm->e_basic_plus_yq_value;
                    } else {
                        $PLB_commssion = 0;
                    }
                } else {
                    $PLB_commssion = 0;
                }
            }


            # Checking Calculation for Business Fare
            if (trim($CabinClass) == "Business") {
                $iata_commssion = 0;
                $PLB_commssion = 0;
                #Add IATA Commission with Basic Fare
                if ($check_comm->value_type == 'percentage') {
                    $iata_commssion = ($approx_base_price * $check_comm->iata) / 100;
                } else if ($check_comm->value_type == 'plus') {
                    $iata_commssion = $check_comm->iata;
                } else {
                    $iata_commssion = 0;
                }
                $available_class = '';
                $available_class = array_search($BookingCode, $ExceptClass); // $key = 2;

                if (empty($available_class)) {
                    #Add PLB Commission With Basic+ YQ/YR     
                    if ($check_comm->value_type == 'percentage') {

                        if ($check_comm->b_basic_plus_yq_type == "YQ") {
                            $YQ_value = $yqvalue;
                        }
                        if ($check_comm->b_basic_plus_yq_type == "YR") {
                            $YQ_value = $YR_value;
                        }

                        $basic_plus_yq = (($approx_base_price - $iata_commssion) + $YQ_value);

                        $PLB_commssion = ($basic_plus_yq * $check_comm->b_basic_plus_yq_value) / 100;
                    } else if ($check_comm->value_type == 'plus') {
                        $PLB_commssion = $check_comm->b_basic_plus_yq_value;
                    } else {
                        $PLB_commssion = 0;
                    }
                }
            }

            #Cash Back Option
            $CashBackAmount=0;
             $CashBack=$check_comm->cashback;
            if($CashBack > 0)
            {
                $CashBackAmount=$CashBack;
            }       


            //  $tds_commission = ($agent_commssion + $yq_value) * 5 / 100;
             $flight_journey ['Price'] ['Currency'] = $currency;
            $flight_journey ['Price'] ['TotalDisplayFare'] = $display_price;
            $flight_journey ['Price'] ['PriceBreakup'] ['Tax'] = $total_tax;
            $flight_journey ['Price'] ['PriceBreakup'] ['BasicFare'] = $approx_base_price;
            
              $flight_journey ['Price'] ['PriceBreakup'] ['CommissionEarned'] = @$iata_commssion;
              $flight_journey ['Price'] ['PriceBreakup'] ['PLBEarned'] = (@$PLB_commssion + $CashBackAmount);
              $flight_journey ['Price'] ['PriceBreakup'] ['TdsOnCommission'] = ((@$iata_commssion * 5)/100);
              $flight_journey ['Price'] ['PriceBreakup'] ['TdsOnPLB'] = (((@$PLB_commssion+$CashBackAmount) * 5)/100);
              
            $flight_journey ['Price']['PriceBreakup']['AgentCommission'] = @$iata_commssion + @$PLB_commssion + $CashBackAmount;;
            $flight_journey ['Price']['PriceBreakup']['AgentTdsOnCommision'] = ((@$iata_commssion * 5)/100)+((@$PLB_commssion * 5)/100)+ (($CashBackAmount*5)/100);;
            $flight_journey ['Price'] ['pax_breakup'] = array();
            $flight_journey ['Attr']['IsRefundable'] = $Refundable;
            $flight_journey ['Attr']['AirlineRemark'] = 'this is a test from aditya';


            // Tax info
            // $tax_info = force_multple_data_format(@$air_pricing_info ['air:TaxInfo']);
            // if (isset($tax_info) && valid_array($tax_info)) {
            //     foreach ($tax_info as $t_key => $taxes) {
            //         $flight_journey ['taxes'] [$t_key] ['category'] = $taxes ['@attributes'] ['Category'];
            //         $flight_journey ['taxes'] [$t_key] ['amount'] = $taxes ['@attributes'] ['Amount'];
            //     }
            // }
        }

        $token_data [0] = $flight_list_on_out;
        $token_data [0]['booking_source'] = $this->booking_source;
        //debug($token_data);exit;
        $flight_journey ['ResultToken'] = serialized_data($token_data);
        $flight_journey ['Attr'] = @$arrBaggageDataValue;
        $flight_journey ['booking_source'] = $this->booking_source;
        $flight_journey ['token_key'] = md5($flight_journey ['ResultToken']);
        $flight_journey ['flight_details']['summery'] = $flight_list_on_out;
        $flight_journey_new['JourneyList'][0][0] = $flight_journey;
        // debug($flight_journey_new);exit;
        // Create flight summery
        if (isset($flight_list_on_out ['flight_list']) && valid_array($flight_list_on_out ['flight_list'])) {
            foreach ($flight_list_on_out ['flight_list'] as $fl_direction => $list) {
                //$flight_journey ['summary'] [] = $this->flight_segment_summary($list, $fl_direction);
            }
        }
        // debug($flight_journey_new);exit;
        //echo 'herre';exit;
        // $first_flight = current($flight_journey['flight_details']['details']);
        // $last_flight = end($flight_journey['flight_details']['details']);
        // $flight_journey['flight_details']['summery'] = $this->format_segment_summery($first_flight,$last_flight);
        // debug($first_flight);echo 'last';debug($last_flight);exit;
        // debug($flight_journey);echo '$$flight_journey';
        return $flight_journey_new;
    }

    /**
     *
     * @param
     *          $flight_list
     * @param
     *          $direction
     */
    private function flight_segment_summary_old($flight_list, $direction) {

        $flight_list = @$flight_list ['flight_detail'];
        $first_flight = reset($flight_list);
        $last_flight = end($flight_list);

        $departure_time = date("H:i", strtotime($first_flight ['departure_time']));
        $departure_date = date("d M", strtotime($first_flight ['departure_time']));
        $arrival_time = date("H:i", strtotime($last_flight ['arrival_time']));
        $arrival_date = date("d M", strtotime($last_flight ['arrival_time']));

        $origin_code = $first_flight ['origin'];
        $destination_code = $last_flight ['destination'];

        /* get origin and destination name */
        $origine_name = $this->get_airport_city($origin_code);
        $destination_name = $this->get_airport_city($destination_code);

        $no_of_stops = count($flight_list) - 1;
        $duration = calculate_duration($first_flight ['departure_time'], $last_flight ['arrival_time']);
        $class = isset($first_flight ['cabin_class']) && !empty($first_flight ['cabin_class']) ? $first_flight ['cabin_class'] : 'economy';
        $operator = @$first_flight ['carrier'];
        $flight_number = @$first_flight ['flight_number'];
        $flight_seg_key = @$first_flight ['flight_seg_key'];
        $operator_name = $operator;
        //$summary ='';
        $summary = $this->format_summary_array($direction, $origin_code, $destination_code, $first_flight ['departure_time'], $last_flight ['arrival_time'], $operator, $operator_name, $flight_number, $no_of_stops, $class, $origine_name, $destination_name);
        //echo 'egrere';exit;
        return $summary;
    }

    private function flight_detail_format($segment, $flight_key, $trip_type, $is_leg) {
        $duration_sec = calculate_duration(@$segment ['@attributes'] ['DepartureTime'], @$segment ['@attributes'] ['ArrivalTime']);
        $duration = get_duration_label(@$duration_sec);

        $origine_name = $this->get_airport_city(@$segment ['@attributes'] ['Origin']);
        $destination_name = $this->get_airport_city(@$segment ['@attributes'] ['Destination']);

        $DepartureTime = explode('T', $segment ['@attributes'] ['DepartureTime']);
        $DepartureTime1 = explode('.', $DepartureTime[1]);

        $depart_date = date("Y-m-d", strtotime(@$segment ['@attributes'] ['DepartureTime']));
        $depart_time = date("H:i:s", strtotime(@$segment ['@attributes'] ['DepartureTime']));

        $arrival_date = date("Y-m-d", strtotime(@$segment ['@attributes'] ['ArrivalTime']));
        $arrival_time = date("H:i:s", strtotime(@$segment ['@attributes'] ['ArrivalTime']));

        $ArrivalTime = explode('T', $segment ['@attributes'] ['ArrivalTime']);
        $ArrivalTime1 = explode('.', $ArrivalTime[1]);

        $flight_detail_arr = array();
        $flight_detail_arr ['journey_number'] = $trip_type;

        $flight_detail_arr ['Origin'] = array(
            'AirportCode' => @$segment ['@attributes'] ['Origin'],
            'CityName' => $origine_name,
            'AirportName' => $origine_name,
            'date_time' => @$segment ['@attributes'] ['DepartureTime'],
            'DateTime' => @$DepartureTime[0] . " " . @$DepartureTime1[0],
            'date' => @$DepartureTime[0], // Derive
            'time' => @$DepartureTime1[0], // Derive H i/h i a
            'FDTV' => strtotime(@$DepartureTime1[0])
        );

        $flight_detail_arr ['Destination'] = array(
            'AirportCode' => @$segment ['@attributes'] ['Destination'],
            'CityName' => $destination_name,
            'AirportName' => $destination_name,
            'date_time' => @$segment ['@attributes'] ['ArrivalTime'],
            'DateTime' => @$ArrivalTime[0] . " " . @$ArrivalTime1[0],
            'date' => @$ArrivalTime[0], // Derive
            'time' => @$ArrivalTime1[0], // Derive H i/h i a
            'FATV' => strtotime(@$ArrivalTime1[0])
        );
        //debug($segment);
        // echo "heeee";



        $flight_detail_arr ['duration_seconds'] = $duration_sec;
        $flight_detail_arr ['duration'] = @$duration;
        $flight_detail_arr ['OperatorCode'] = @$segment ['@attributes'] ['Carrier'];
        $flight_detail_arr ['OperatorName'] = $this->get_airline_name($flight_detail_arr ['OperatorCode']);
        $flight_detail_arr ['FlightNumber'] = @$segment ['@attributes'] ['FlightNumber'];
        //$flight_detail_arr ['CabinClass'] = '';
        // $flight_detail_arr ['Attr']['AvailableSeats'] = '';

        $flight_detail_arr ['is_leg'] = $is_leg;

        return $flight_detail_arr;
    }

    /*
     *
     * get airport city based on airport code
     */

    private function get_airport_city($airport_code) {
        $CI = & get_instance();

        $airport_name = $CI->db_cache_api->get_airport_city_name(array(
            'airport_code' => $airport_code
        ));
        $airport_name = $airport_name ['airport_city'];
        return $airport_name;
    }

    /*
     *
     * get airport city based on airport code
     */

    private function get_airline_name($airline_code) {
        $CI = & get_instance();

        $airline_name = $CI->db_cache_api->get_airline_code_list(array(
            'code' => $airline_code
        ));
        //debug($airline_name);exit;
        // echo $airline_name [$airline_code];exit;
        $airport_name = $airline_name [$airline_code];
        return $airport_name;
    }

    /**
     * flight low fare search request sync
     * 
     * @param array $search_data            
     */
     function flight_low_fare_search_req($search_data) {
        // debug($search_data);exit;
        $response ['status'] = SUCCESS_STATUS;
        $response ['data'] = array();
        $search_data ['method'] = 'Sync';


        /* paxes details */
        $prefered_carrier = "";
        if ($search_data["carrier"] != "" && $search_data["carrier"] != "all" && strlen($search_data["carrier"]) == 2) {
            $prefered_carrier = '<PermittedCarriers>
                        <Carrier xmlns="http://www.travelport.com/schema/common_v41_0" Code="' . $search_data["carrier"] . '"/>
                        </PermittedCarriers>';
        }
        $AirSearchModifiers = '<AirSearchModifiers MaxSolutions="100"><PreferredProviders>
                     <Provider xmlns="http://www.travelport.com/schema/common_v41_0" Code="1G" />
                     </PreferredProviders>
                     ' . $prefered_carrier . '
                    </AirSearchModifiers>';
        $LegModifiers = '<AirLegModifiers>
        <PreferredCabins>
        <CabinClass Type="' . $search_data['cabin_class'] . '" xmlns="http://www.travelport.com/schema/common_v41_0"></CabinClass>
        </PreferredCabins>
        </AirLegModifiers>';
        $infant = '';
        $adult = '';
        $child = '';

        for ($i = 0; $i < $search_data ['adult']; $i ++) {
            $adult .= '<SearchPassenger Code="ADT" xmlns="http://www.travelport.com/schema/common_v41_0" />';
        }
        for ($i = 0; $i < $search_data ['child']; $i ++) {
            $child .= '<SearchPassenger Code="CNN" Age="10" xmlns="http://www.travelport.com/schema/common_v41_0" />';
        }
        for ($i = 0; $i < $search_data ['infant']; $i ++) {
            $infant .= '<SearchPassenger Code="INF" Age="01" xmlns="http://www.travelport.com/schema/common_v41_0" />';
        }

        $provider = "";
        if ($search_data ['method'] == "Asynch") {
            $search_method = "LowFareSearchAsynchReq";
            $AsynchModifiers = '<AirSearchAsynchModifiers  >
                                    <InitialAsynchResult   MaxWait="02"/>
                                </AirSearchAsynchModifiers>';
        } else {
            $search_method = "LowFareSearchReq";
            $AsynchModifiers = "";
        }

        $AsynchModifiers = "";

        $circle = '';
        if ($search_data['trip_type'] == 'return' && $search_data['is_domestic'] != 1) {
            $origin_code = $search_data ['from'];
            $desti_code = $search_data ['to'];
            $depart_date = date("Y-m-d", strtotime(str_replace('/', '-', $search_data ['depature'])));
            $depart_date = '<SearchDepTime PreferredTime="' . $depart_date . '" />';
            $arrival_date = date("Y-m-d", strtotime(str_replace('/', '-', $search_data ['return'])));
            $arrival_date = '<SearchDepTime PreferredTime="' . $arrival_date . '" />';

            $circle = '<SearchAirLeg>
                            <SearchOrigin>
                                <CityOrAirport Code="' . $origin_code . '" xmlns="http://www.travelport.com/schema/common_v41_0" />
                            </SearchOrigin>
                            <SearchDestination>
                                <CityOrAirport Code="' . $desti_code . '" xmlns="http://www.travelport.com/schema/common_v41_0" />
                            </SearchDestination>
                             ' . $depart_date . '
                             ' . $LegModifiers . '
                        </SearchAirLeg>';
            $circle .= '<SearchAirLeg>
                            <SearchOrigin>
                                <CityOrAirport Code="' . $desti_code . '" xmlns="http://www.travelport.com/schema/common_v41_0" />
                            </SearchOrigin>
                            <SearchDestination>
                                <CityOrAirport Code="' . $origin_code . '" xmlns="http://www.travelport.com/schema/common_v41_0" />
                            </SearchDestination>
                             ' . $arrival_date . '
                             ' . $LegModifiers . '
                        </SearchAirLeg>';
            $oneway = $circle;
        } else if ($search_data['trip_type'] == 'return' && $search_data['is_domestic'] == 1) {
            $origin_code = $search_data ['from'];
            $desti_code = $search_data ['to'];
            $depart_date = date("Y-m-d", strtotime(str_replace('/', '-', $search_data ['depature'])));
            $depart_date = '<SearchDepTime PreferredTime="' . $depart_date . '" />';
            $arrival_date = date("Y-m-d", strtotime(str_replace('/', '-', $search_data ['return'])));
            $arrival_date = '<SearchDepTime PreferredTime="' . $arrival_date . '" />';
            $oneway = '<SearchAirLeg>
                            <SearchOrigin>
                                <CityOrAirport Code="' . $origin_code . '" xmlns="http://www.travelport.com/schema/common_v41_0" />
                            </SearchOrigin>
                            <SearchDestination>
                                <CityOrAirport Code="' . $desti_code . '" xmlns="http://www.travelport.com/schema/common_v41_0" />
                            </SearchDestination>
                             ' . $depart_date . '
                             ' . $LegModifiers . '
                        </SearchAirLeg>';

            $circle = '<SearchAirLeg>
                            <SearchOrigin>
                                <CityOrAirport Code="' . $desti_code . '" xmlns="http://www.travelport.com/schema/common_v41_0" />
                            </SearchOrigin>
                            <SearchDestination>
                                <CityOrAirport Code="' . $origin_code . '" xmlns="http://www.travelport.com/schema/common_v41_0" />
                            </SearchDestination>
                             ' . $arrival_date . '
                             ' . $LegModifiers . '
                        </SearchAirLeg>';

            $request = '<?xml version="1.0" encoding="utf-8"?>
            <s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
                <s:Header>
                    <Action s:mustUnderstand="1" xmlns="http://schemas.microsoft.com/ws/2005/05/addressing/none">localhost:8080/kestrel/AirService</Action>
                </s:Header>
                <s:Body xmlns:xsi="http://www.w3.org/2001/xmlschema-instance" xmlns:xsd="http://www.w3.org/2001/xmlschema">
                    <' . $search_method . ' SolutionResult="true" AuthorizedBy="user" TraceId="394d96c00971c4545315e49584609ff6" TargetBranch="' . $this->config ['Target'] . '" xmlns="http://www.travelport.com/schema/air_v41_0">
                        <BillingPointOfSaleInfo OriginApplication="UAPI" xmlns="http://www.travelport.com/schema/common_v41_0" />
                            ' . $circle . '
                            ' . $provider . '
                            ' . $AirSearchModifiers . '
                            ' . $adult . '
                            ' . $child . '
                            ' . $infant . '
                            <AirPricingModifiers FaresIndicator="AllFares"></AirPricingModifiers>
                             ' . $AsynchModifiers . '
                    </' . $search_method . '>
                   
                </s:Body>
            </s:Envelope>';
            $request1 ['payload_return'] = $request;
        } else if ($search_data['trip_type'] == 'oneway') {
            $origin_code = $search_data ['from'];
            $desti_code = $search_data ['to'];
            $depart_date = date("Y-m-d", strtotime(str_replace('/', '-', $search_data ['depature'])));

            $dep_date = '<SearchDepTime PreferredTime="' . $depart_date . '" />';

            $oneway = '<SearchAirLeg>
                            <SearchOrigin>
                                <CityOrAirport Code="' . $origin_code . '" xmlns="http://www.travelport.com/schema/common_v41_0" />
                            </SearchOrigin>
                            <SearchDestination>
                                <CityOrAirport Code="' . $desti_code . '" xmlns="http://www.travelport.com/schema/common_v41_0" />
                            </SearchDestination>
                           ' . $dep_date . '
                           ' . $LegModifiers . '
                        </SearchAirLeg>';
        } else if ($search_data['trip_type'] == 'multicity') {
            $arrMultiFrmCity = $search_data['from_city'];
            $arrMultiToCity = $search_data['to_city'];
            $arrMultiCheckin = $search_data['depature'];
            // debug($search_data);exit;
            $oneway = '';
            for ($mul = 0; $mul < count($arrMultiFrmCity); $mul++) {
                $origin_code_m = $arrMultiFrmCity[$mul];
                $desti_code_m = $arrMultiToCity[$mul];
                $depart_date_m = date("Y-m-d", strtotime($arrMultiCheckin[$mul]));
                $dep_date_m = '<SearchDepTime PreferredTime="' . $depart_date_m . '" />';
                $oneway .= '<SearchAirLeg>
                                    <SearchOrigin>
                                        <CityOrAirport Code="' . $origin_code_m . '" xmlns="http://www.travelport.com/schema/common_v41_0" />
                                    </SearchOrigin>
                                    <SearchDestination>
                                        <CityOrAirport Code="' . $desti_code_m . '" xmlns="http://www.travelport.com/schema/common_v41_0" />
                                    </SearchDestination>
                                    ' . $dep_date_m . '
                                    ' . $LegModifiers . '
                                    </SearchAirLeg>';
            }
            //debug($oneway);exit;
        }
        /* request for search flight */
        $request = '<?xml version="1.0" encoding="utf-8"?>
            <s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
                <s:Header>
                    <Action s:mustUnderstand="1" xmlns="http://schemas.microsoft.com/ws/2005/05/addressing/none">localhost:8080/kestrel/AirService</Action>
                </s:Header>
                <s:Body xmlns:xsi="http://www.w3.org/2001/xmlschema-instance" xmlns:xsd="http://www.w3.org/2001/xmlschema">
                    <' . $search_method . ' SolutionResult="true" AuthorizedBy="user" TraceId="394d96c00971c4545315e49584609ff6" TargetBranch="' . $this->config ['Target'] . '" xmlns="http://www.travelport.com/schema/air_v41_0">
                        <BillingPointOfSaleInfo OriginApplication="UAPI" xmlns="http://www.travelport.com/schema/common_v41_0" />
                            ' . $oneway . '
                            ' . $provider . '
                            ' . $AirSearchModifiers . '
                            ' . $adult . '
                            ' . $child . '
                            ' . $infant . '
                            <AirPricingModifiers FaresIndicator="AllFares"></AirPricingModifiers>
                             ' . $AsynchModifiers . '
                    </' . $search_method . '>
                   
                </s:Body>
            </s:Envelope>';
        
        $request1 ['payload'] = $request;
        $request1 ['url'] = $this->config['EndPointUrl'];
        $request1 ['soap_action'] = '';
        $request1 ['status'] = SUCCESS_STATUS;
        //debug($request1);exit;       
        return $request1;
    }

    public function format_update_fare_quote_response($air_price_response, $price_response, $search_id, $flight_details_key) {

        //debug($air_price_response);exit;
        //commented
        // $response ['data'] = array();
        // $response ['status'] = FAILURE_STATUS;
        // $currency_obj = new Currency(array(
        //     'module_type' => 'fligt',
        //     'from' => get_application_default_currency(),
        //     'to' => get_application_transaction_currency_preference()
        //         ));
        // // Air pricing request
        // $connections = @$token ['connection'];
        // $air_price_request = $this->air_pricing_request($token);
        // // debug($air_price_request);exit;
        // $booking_array = array();
        // $air_Seg_xml = '';
        // $air_pricing_xml = '';
        $random_value = mt_rand(100000, 999999);

        $status = TRUE;
        if ($status = SUCCESS_STATUS) {
            //commented
            //if ($air_price_request ['status'] = SUCCESS_STATUS) {
            // $air_price_response = $this->process_request($air_price_request ['data'] ['request']);
            // $xml = simplexml_load_string($air_price_response);
            // $xml->registerXPathNamespace('SOAP', 'http://schemas.xmlsoap.org/soap/envelope/');
            // $xml->registerXPathNamespace('air', 'http://www.travelport.com/schema/air_v41_0');
            // $nodes = $xml->xpath('//SOAP:Envelope/SOAP:Body/air:AirPriceRsp/air:AirPriceResult/air:AirPricingSolution');
            // $price_solution_xml = '';
            // if (isset($nodes [0])) {
            //     $price_solution_xml = $nodes [0]->asXML();
            // }
            // $nodes_ite = $xml->xpath('//SOAP:Envelope/SOAP:Body/air:AirPriceRsp/air:AirItinerary/air:AirSegment');
            // $segment_solution_xml = array();
            // foreach ($nodes_ite as $k => $vsm) {
            //     $key_objm = $vsm ['Key'];
            //     $key_objm = json_encode($key_objm);
            //     $key_arrm = json_decode($key_objm, TRUE);
            //     $sekKey = $key_arrm [0];
            //     $segment_solution_xml [$sekKey] = $vsm->asXML();
            // }
            //$air_price_response_arr = Converter::createArray($air_price_response);
            //debug($air_price_response);exit;
            $passenger = array();
            $passengers_array = array();
            $change_panelty = array();
            $cncel_panelty = array();
            $flight_details = array();
            $flights = array();

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
                $air_pricing_sol ['AirPricingSolution'] ['@attributes'] ['QuoteDate'] = $air_price_result ['@attributes'] ['QuoteDate'];

                $air_seg_key = '';
                $air_seg_ref = force_multple_data_format($air_price_result ['air:AirSegmentRef']);
                if (isset($air_seg_ref) && valid_array($air_seg_ref)) {
                    foreach ($air_seg_ref as $s_key => $seg_key) {
                        $air_seg_key [] = $seg_key ['@attributes'] ['Key'];
                    }
                }

                // format Air price Result
                if (isset($air_price_result ['air:AirPricingInfo']) && valid_array($air_price_result ['air:AirPricingInfo'])) {
                    // Passenger details starts here
                    $air_price_result ['air:AirPricingInfo'] = force_multple_data_format($air_price_result ['air:AirPricingInfo']);
                    foreach ($air_price_result ['air:AirPricingInfo'] as $ap_key => $air_price_sol) {
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

                        // air:TaxInfo
                        $fare_air_tax_detail_inf = force_multple_data_format(@$air_price_sol ['air:TaxInfo']);

                        if (isset($fare_air_tax_detail_inf) && valid_array($fare_air_tax_detail_inf)) {
                            foreach ($fare_air_tax_detail_inf as $tx_keym => $tax_detailsm) {
                                $air_pricing_info_booking_arr ['AirPricingInfo'] ['TaxInfo'] [$tx_keym] ['@attributes'] ['Category'] = $tax_detailsm ['@attributes'] ['Category'];
                                $air_pricing_info_booking_arr ['AirPricingInfo'] ['TaxInfo'] [$tx_keym] ['@attributes'] ['Amount'] = $tax_detailsm ['@attributes'] ['Amount'];
                                $air_pricing_info_booking_arr ['AirPricingInfo'] ['TaxInfo'] [$tx_keym] ['@attributes'] ['Key'] = $tax_detailsm ['@attributes'] ['Key'];
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
                        $carry_on_baggage_arr = force_multple_data_format($air_price_sol ['air:BaggageAllowances'] ['air:CarryOnAllowanceInfo']);
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

                        // Price details
                        $passenger_total_price_val = isset($air_price_sol ['@attributes'] ['ApproximateTotalPrice']) && !empty($air_price_sol ['@attributes'] ['ApproximateTotalPrice']) ? @$air_price_sol ['@attributes'] ['ApproximateTotalPrice'] : @$air_price_sol ['@attributes'] ['TotalPrice'];
                        $passenger_total_price = substr($passenger_total_price_val, 3);
                        $passenger_total_price_currency = substr($passenger_total_price_val, 0, 3);
                        // $currency_obj->getConversionRate(true,$passenger_total_price_currency,get_application_transaction_currency_preference());
                        // $converted_total_price_rate = $currency_obj->force_currency_conversion($passenger_total_price);
                        // $passenger_total_price = @$converted_total_price_rate['default_value'];
                        // $passenger_total_price_currency = @$converted_total_price_rate['default_currency'];

                        $passenger_base_price_val = isset($air_price_sol ['@attributes'] ['EquivalentBasePrice']) && !empty($air_price_sol ['@attributes'] ['EquivalentBasePrice']) ? $air_price_sol ['@attributes'] ['EquivalentBasePrice'] : $air_price_sol ['@attributes'] ['ApproximateBasePrice'];
                        $passenger_base_price = substr($passenger_base_price_val, 3);
                        $passenger_base_price_currency = substr($passenger_base_price_val, 0, 3);
                        // $currency_obj->getConversionRate(true,$passenger_base_price_currency,get_application_transaction_currency_preference());
                        // $converted_base_price_rate = $currency_obj->force_currency_conversion($passenger_base_price);
                        // $passenger_base_price = @$converted_base_price_rate['default_value'];
                        // $passenger_base_price_currency = @$converted_base_price_rate['default_currency'];

                        $passenger_taxes_val = isset($air_price_sol ['@attributes'] ['ApproximateTaxes']) && !empty($air_price_sol ['@attributes'] ['ApproximateTaxes']) ? $air_price_sol ['@attributes'] ['ApproximateTaxes'] : $air_price_sol ['@attributes'] ['Taxes'];
                        $passenger_taxes = substr($passenger_taxes_val, 3);
                        $passenger_taxes_currency = substr($passenger_taxes_val, 0, 3);
                        // $currency_obj->getConversionRate(true,$passenger_taxes_currency,get_application_transaction_currency_preference());
                        // $converted_taxes_rate = $currency_obj->force_currency_conversion($passenger_taxes);
                        // $passenger_taxes = @$converted_taxes_rate['default_value'];
                        // $passenger_taxes_currency = @$converted_taxes_rate['default_currency'];

                        $passenger ['total_price'] = $passenger_total_price;
                        $passenger ['base_price'] = $passenger_base_price;
                        $passenger ['taxes'] = $passenger_taxes;
                        $passenger ['total_price_currency'] = $passenger_total_price_currency;
                        $passenger ['base_price_currency'] = $passenger_base_price_currency;
                        $passenger ['taxes_currency'] = $passenger_taxes_currency;

                        // Passengers type
                        $passenger_types = $air_price_sol ['air:PassengerType'];
                        if (isset($passenger_types) && valid_array($passenger_types)) {
                            $pass_type_code = isset($passenger_types ['@attributes'] ['Code']) ? @$passenger_types ['@attributes'] ['Code'] : 'ADT';
                            $passengers_array [$pass_type_code] = $passenger;
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
                    $flight_details ['passengers'] = $passengers_array;
                    $flight_details ['change_panelty'] = $change_panelty;
                    $flight_details ['cncel_panelty'] = $cncel_panelty;
                    // Passenger ends here

                    $total_price_val = isset($air_price_result ['@attributes'] ['ApproximateTotalPrice']) && !empty($air_price_result ['@attributes'] ['ApproximateTotalPrice']) ? $air_price_result ['@attributes'] ['ApproximateTotalPrice'] : $air_price_result ['@attributes'] ['TotalPrice'];
                    $total_price = substr($total_price_val, 3);
                    $total_price_currency = substr($total_price_val, 0, 3);
                    //commented
                    // $currency_obj->getConversionRate(true, $total_price_currency, 'INR');
                    // $converted_total_rate = $currency_obj->force_currency_conversion($total_price);
                    // $total_price = @$converted_total_rate ['default_value'];
                    // $total_price_currency = @$converted_total_rate ['default_currency'];

                    $base_price_val = isset($air_price_result ['@attributes'] ['EquivalentBasePrice']) && !empty($air_price_result ['@attributes'] ['EquivalentBasePrice']) ? $air_price_result ['@attributes'] ['EquivalentBasePrice'] : $air_price_result ['@attributes'] ['ApproximateBasePrice'];
                    $base_price = substr($base_price_val, 3);
                    $base_price_currency = substr($base_price_val, 0, 3);
                    // $currency_obj->getConversionRate(true, $base_price_currency, get_application_transaction_currency_preference());
                    // $converted_base_rate = $currency_obj->force_currency_conversion($base_price);
                    // $base_price = @$converted_base_rate ['default_value'];
                    // $base_price_currency = @$converted_base_rate ['default_currency'];

                    $taxes_val = isset($air_price_result ['@attributes'] ['ApproximateTaxes']) && !empty($air_price_result ['@attributes'] ['ApproximateTaxes']) ? $air_price_result ['@attributes'] ['ApproximateTaxes'] : $air_price_result ['@attributes'] ['Taxes'];
                    $taxes = substr($taxes_val, 3);
                    $taxes_currency = substr($taxes_val, 0, 3);
                    // $currency_obj->getConversionRate(true, $taxes_currency, get_application_transaction_currency_preference());
                    // $converted_taxes = $currency_obj->force_currency_conversion($taxes);
                    // $taxes = @$converted_taxes ['default_value'];
                    // $taxes_currency = @$converted_taxes ['default_currency'];

                    $flight_details ['total_price'] = $total_price;
                    $flight_details ['total_price_curr'] = $total_price_currency;
                    $flight_details ['base_price'] = $base_price;
                    $flight_details ['base_price_curr'] = $base_price_currency;
                    $flight_details ['taxes'] = $taxes;
                    $flight_details ['taxes_curr'] = $taxes_currency;
                    $flight_details = $this->flight_segment_summary($itenary_details, $air_seg_key, $air_price_result, $random_value, $search_id);
                    // $itenary_details
                    $air_pricing_xml = '';
                    if (isset($itenary_details ['air:AirSegment']) && valid_array($itenary_details ['air:AirSegment'])) {
                        $itenary_details = force_multple_data_format($itenary_details ['air:AirSegment']);
                        // $air_segment_booking_arr[$s_key]['key']
                        foreach ($itenary_details as $it_key => $air_segment) {
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
                                $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['@attributes'] ['Distance'] = $air_segment ['@attributes'] ['Distance'];
                                $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['@attributes'] ['ClassOfService'] = $air_segment ['@attributes'] ['ClassOfService'];
                                $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['@attributes'] ['Equipment'] = @$air_segment ['@attributes'] ['Equipment'];
                                $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['@attributes'] ['ChangeOfPlane'] = $air_segment ['@attributes'] ['ChangeOfPlane'];
                                $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['@attributes'] ['OptionalServicesIndicator'] = $air_segment ['@attributes'] ['OptionalServicesIndicator'];

                                $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['@attributes'] ['AvailabilitySource'] = $air_segment ['@attributes'] ['AvailabilitySource'];
                                $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['@attributes'] ['ParticipantLevel'] = @$air_segment ['@attributes'] ['ParticipantLevel'];
                                $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['@attributes'] ['LinkAvailability'] = @$air_segment ['@attributes'] ['LinkAvailability'];
                                $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['@attributes'] ['PolledAvailabilityOption'] = $air_segment ['@attributes'] ['PolledAvailabilityOption'];
                                $air_pricing_info_booking_arr ['AirSegment'] [$it_key] ['@attributes'] ['AvailabilityDisplayType'] = $air_segment ['@attributes'] ['AvailabilityDisplayType'];

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
                                    $flights [$it_key] ['origin_city'] = $this->get_airport_city($air_segment ['air:FlightDetails'] ['@attributes'] ['Origin']);
                                    $flights [$it_key] ['destination_city'] = $this->get_airport_city($air_segment ['air:FlightDetails'] ['@attributes'] ['Destination']);
                                    $flights [$it_key] ['departure_date'] = date('d M Y', strtotime($air_segment ['air:FlightDetails'] ['@attributes'] ['DepartureTime']));
                                    $flights [$it_key] ['departure_datetime'] = $air_segment ['air:FlightDetails'] ['@attributes'] ['DepartureTime'];
                                    $flights [$it_key] ['arrival_date'] = date('d M Y', strtotime($air_segment ['air:FlightDetails'] ['@attributes'] ['ArrivalTime']));
                                    $flights [$it_key] ['departure_time'] = date('H:i', strtotime($air_segment ['air:FlightDetails'] ['@attributes'] ['DepartureTime']));
                                    $flights [$it_key] ['arrival_time'] = date('H:i', strtotime($air_segment ['air:FlightDetails'] ['@attributes'] ['ArrivalTime']));
                                    $flights [$it_key] ['flight_time'] = $air_segment ['air:FlightDetails'] ['@attributes'] ['FlightTime'];
                                    $flights [$it_key] ['travel_time'] = $air_segment ['air:FlightDetails'] ['@attributes'] ['TravelTime'];
                                    $flights [$it_key] ['duration'] = get_duration_label(calculate_duration($air_segment ['air:FlightDetails'] ['@attributes'] ['DepartureTime'], $air_segment ['air:FlightDetails'] ['@attributes'] ['ArrivalTime']));
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
                                $flights [$it_key] ['availability_source'] = $air_segment ['@attributes'] ['AvailabilitySource'];
                                $flights [$it_key] ['participant_level'] = @$air_segment ['@attributes'] ['ParticipantLevel'];
                                $flights [$it_key] ['link_availability'] = @$air_segment ['@attributes'] ['LinkAvailability'];
                                $flights [$it_key] ['polled_availability_option'] = $air_segment ['@attributes'] ['PolledAvailabilityOption'];
                                $flights [$it_key] ['availability_display_type'] = $air_segment ['@attributes'] ['AvailabilityDisplayType'];
                            }
                        }
                        $response ['status'] = SUCCESS_STATUS;
                    }
                }

                $air_pricing_sol ['AirPricingSolution'] ['AirSegment'] = $air_pricing_info_booking_arr ['AirSegment'];
                $air_pricing_sol ['AirPricingSolution'] ['AirPricingInfo'] = $air_pricing_info_booking_arr ['AirPricingInfo'];
                //debug($air_price_result);exit;
                // FareNote
                $air_price_result ['air:FareNote'] = force_multple_data_format($air_price_result ['air:FareNote']);
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
                $flight_details ['air_seg_key'] = $air_seg_key;
                //$air_pricing_xml = str_replace('air:', '', str_replace('common_v41_0:', '', $air_pricing_xml));
                //debug($air_pricing_xml);exit;
                //$air_pricing_xml ='';
                // debug($air_pricing_xml);exit;
            }

            // New element to be inserted
            /*
             * foreach($air_seg_key as $s_key => $air_seg) {
             * $insert = new SimpleXMLElement(str_replace('air:','',$air_seg));
             * // Get the last nodeA element
             * //$sxe->registerXPathNamespace('air', 'http://www.travelport.com/schema/air_v41_0');
             * $target = current($sxe->xpath('//AirPricingSolution/AirSegmentRef'));
             * $target_dom = dom_import_simplexml($target);
             * $insert_dom = $target_dom->ownerDocument->importNode(dom_import_simplexml($insert), true);
             * if ($target_dom->nextSibling) {
             * $target_dom->parentNode->insertBefore($insert_dom, $target_dom->nextSibling);
             * } else {
             * $target_dom->parentNode->appendChild($insert_dom);
             * }
             * }
             * $connection_Str = '<air:Connection/>';
             * $connections = explode(',',$connections);
             */
            // debug($connections);
            // debug($connections);exit;
            // $price_xml = $sxe->asXML();
            // $xml = simplexml_load_string($price_xml);
            // unset($xml->AirSegmentRef); // this would remove node c
            // foreach($xml->AirSegment as $m => $v) {
            // unset($v->CodeshareInfo);
            // unset($v->AirAvailInfo);
            //
            // }
            // unset($xml->FareNote);
            // $price_xml_final = $xml->asXML();
            //debug($flight_details);exit;
            $flight_details ['flights'] = $flights;
            $flight_details ['price_xml'] = $air_pricing_xml;
            $AirPriceRes = $price_response;
            $AirPriceRes = str_replace('SOAP:', '', $AirPriceRes);
            $AirPriceRes = str_replace('air:', '', $AirPriceRes);
            $AirPriceRes = new SimpleXMLElement($AirPriceRes);

            $AirItinerary = $AirPriceRes->Body->AirPriceRsp->AirItinerary;
            $AirItinerary_xml = $AirItinerary->asXML();
            //echo '<pre/>xml';print_r($AirItinerary_xml);
            $AirPricingSolution = $AirPriceRes->Body->AirPriceRsp->AirPriceResult->AirPricingSolution;
            unset($AirPricingSolution->AirSegmentRef);
            $AirPricingSolution_xml = $AirPricingSolution->asXML();
            $travelport_price_xml_arr = array(
                'price_xml' => $AirPricingSolution_xml,
                'itinerary_xml' => $AirItinerary_xml,
                'serach_id' => $search_id . "_" . $random_value
            );
            $search_id1 = $search_id . "_" . $random_value;
            $CI = &get_instance();
            $check_price_xml = $CI->db_cache_api->get_travelport_flight_price_xml($search_id1);
            if (!empty($check_price_xml)) {
                $CI->db_cache_api->update_price_xml($AirPricingSolution_xml, $AirItinerary_xml, $search_id1);
            } else {
                $CI->db->insert('travelport_price_xml', $travelport_price_xml_arr);
            }

            //  $access_data = Common_Flight::insert_record ( $flight_details['air_seg_key'][0], $air_pricing_xml);
            //  $flight_details ['JourneyList'][0][0]['flight_details']['summery']['price_xml'] = $air_pricing_xml;
            $response ['data'] = $flight_details;
        }
        //debug($flight_details);exit;
        return $flight_details;
    }

    /**
     * Update Fare Quote
     * @param unknown_type $request
     */
    public function get_update_fare_quote($request, $search_id) {
        // debug($request);exit;
        $this->_random_value_price = "667766886";
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        $update_fare_quote_request = $this->update_fare_quote_request($request, $search_id);
        // echo 'herre'.$request['0']['flight_list']['0']['flight_detail_key'];exit;
        // debug($request);exit;
        $flight_details_key = $request['flight_list'][0]['flight_detail'][0]['fare_info_ref'];
        if ($update_fare_quote_request['status'] == SUCCESS_STATUS) {
            $price_response = $this->process_request($update_fare_quote_request['request']);
            //debug($price_response);exit;
            $api_url = $this->config ['EndPointUrl'];
            $api_request = $update_fare_quote_request ['request'];
            $api_response = $price_response;
            $api_remarks = 'flight_pricing(Travelport Flight)';
            $this->CI->api_model->store_api_request_booking($api_url, $api_request, $api_response, $api_remarks);
            $update_fare_quote_response = Converter::createArray($price_response);
            if (valid_array($update_fare_quote_response) == true && isset($update_fare_quote_response['SOAP:Envelope'] ['SOAP:Body'] ['air:AirPriceRsp'] ['air:AirPriceResult'])) {
                $response ['status'] = SUCCESS_STATUS;
                $response ['data']['FareQuoteDetails'] = $this->format_update_fare_quote_response($update_fare_quote_response, $price_response, $search_id, $flight_details_key);
            } else {
                $response ['message'] = 'Not Available';
            }
        } else {
            $response ['status'] = FAILURE_STATUS;
        }
        // debug($response);exit;
        return $response;
    }

    private function update_fare_quote_request($token, $search_id) {
        $token = force_multple_data_format($token);
        $air_segment = array();
        $CI = & get_instance();
        //$search_id = '6045';
        $search_data = $this->search_data($search_id);
        //debug($search_data);exit;
        $adults = '';
        $childs = '';
        $infants = '';

        $adult_count = $search_data['data'] ['adult_config'];
        $child_count = $search_data['data'] ['child_config'];
        $infant_count = $search_data['data'] ['infant_config'];
        $paxId = 1;
        if ($adult_count != 0) {
            for ($i = 0; $i < $adult_count; $i ++) {
                $adults .= '<SearchPassenger Key="' . $paxId . '" Code="ADT" xmlns="http://www.travelport.com/schema/common_v41_0" ></SearchPassenger>';
                $paxId ++;
            }
        }
        if ($child_count != 0) {
            for ($k = 0; $k < $child_count; $k ++) {
                $childs .= '<SearchPassenger Key="' . $paxId . '" Code="CNN" Age="10" xmlns="http://www.travelport.com/schema/common_v41_0" ></SearchPassenger>';
                $paxId ++;
            }
        }
        if ($infant_count != 0) {
            for ($j = 0; $j < $infant_count; $j ++) {
                $infants .= '<SearchPassenger Key="' . $paxId . '" Code="INF" Age="01" xmlns="http://www.travelport.com/schema/common_v41_0" ></SearchPassenger>';
                $paxId ++;
            }
        }
        // debug($token);exit;
        // $exp_conn = explode ( ",", $flight->Connections );
        if (isset($token [0]['flight_list']) && valid_array($token [0] ['flight_list'])) {
            $origin_value = '';
            foreach ($token[0]['flight_list'] as $fl_ley => $flights_list) {
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
                        $flight_detail_key = $flight ['flight_detail_key'];
                        $booking_counts = $flight ['booking_counts'];
                        $provider_code = $flight ['provider_code'];
                        $optional_services_indicator = $flight ['optional_services_indicator'];
                        $availability_source = (isset($flight ['availability_source'])) ? 'AvailabilitySource="' . $flight ['availability_source'] . '"' : '';
                        $air_code_share = isset($flight ['air_code_share']) && !empty($flight ['air_code_share']) ? '<CodeshareInfo>"' . @$flight ['air_code_share'] . '"</CodeshareInfo>' : '';

                        /*
                         * if ($flight['provider_code'] == "ACH") {
                         * $HostToken_content2 .= '<common_v41_0:HostToken xmlns="http://www.travelport.com/schema/common_v41_0" Key="' . $segment->HostTokenRef . '">' . $HostTokencontent . '</common_v41_0:HostToken>';
                         * $AirSegmentPricingModifiers2 .= '<AirSegmentPricingModifiers AirSegmentRef="' . $segment->AirSegment_Key . '" FareBasisCode="' . $segment->FareBasis . '" ></AirSegmentPricingModifiers>';
                         * // $segments .= '<AirSegment '.$APISRequirementsRef.' ArrivalTime="'.$segment->ArrivalTime.'" Carrier="'.$segment->Carrier.'" ChangeOfPlane="'.$segment->ChangeOfPlane.'" ClassOfService="'.$segment->BookingCode.'" DepartureTime="'.$segment->DepartureTime.'" Destination="'.$segment->Destination.'" '.$Distance.' '.$ETicketability.' '.$Equipment.' FlightNumber="'.$segment->FlightNumber.'" FlightTime="'.$segment->FlightTime.'" Group="'.$segment->Group.'" '.$HostTokenRef.' Key="'.$segment->AirSegment_Key.'" OptionalServicesIndicator="'.$segment->OptionalServicesIndicator.'" Origin="'.$segment->Origin.'" ProviderCode="'.$segment->ProviderCode.'" '.$Status.' '.$SupplierCode.' ></AirSegment>';
                         * $segments .= '<AirSegment ArrivalTime="' . $segment->ArrivalTime . '" Carrier="' . $segment->Carrier . '" ClassOfService="' . $segment->BookingCode . '" DepartureTime="' . $segment->DepartureTime . '" Destination="' . $segment->Destination . '" ' . $Distance . ' ' . $Equipment . ' FlightNumber="' . $segment->FlightNumber . '" FlightTime="' . $segment->FlightTime . '" Group="' . $segment->Group . '" ' . $HostTokenRef . ' Key="' . $segment->AirSegment_Key . '" Origin="' . $segment->Origin . '" ProviderCode="' . $segment->ProviderCode . '" ' . $Status . ' ' . $SupplierCode . ' ></AirSegment>';
                         * } else {
                         * $segments .= '<AirSegment Key="' . $segment->AirSegment_Key . '" Group="' . $segment->Group . '" Carrier="' . $segment->Carrier . '" FlightNumber="' . $segment->FlightNumber . '" ProviderCode="' . $segment->ProviderCode . '" Origin="' . $segment->Origin . '" Destination="' . $segment->Destination . '" DepartureTime="' . $segment->DepartureTime . '" ArrivalTime="' . $segment->ArrivalTime . '" FlightTime="' . $segment->FlightTime . '" ' . $Distance . ' ClassOfService="' . $segment->BookingCode . '" ' . $Equipment . ' ChangeOfPlane="' . $segment->ChangeOfPlane . '" OptionalServicesIndicator="' . $segment->OptionalServicesIndicator . '" ' . $AvailabilitySource . ' ' . $ParticipantLevel . ' ' . $PolledAvailabilityOption . ' ' . $AvailabilityDisplayType . ' ' . $ETicketability . ' ' . $LinkAvailability . '>
                         * ' . $CodeshareInfo . '
                         * <AirAvailInfo ProviderCode="' . $segment->ProviderCode . '">
                         * <BookingCodeInfo BookingCounts="' . $segment->BookingCounts . '"></BookingCodeInfo>
                         * </AirAvailInfo>
                         * <FlightDetails ' . $Equipment . ' ' . $OriginTerminal . ' ' . $DestinationTerminal . ' Destination="' . $segment->Destination . '" Origin="' . $segment->Origin . '" Key="' . $segment->FlightDetail_Key . '" FlightTime="' . $segment->FlightTime . '" ArrivalTime="' . $segment->ArrivalTime . '" DepartureTime="' . $segment->DepartureTime . '" ></FlightDetails>
                         * ' . $connection_indc . '
                         * </AirSegment>';
                         * }
                         */
                        $segment = '<AirSegment  Key="' . $flight_seg_key . '" Group="' . $group . '" Carrier="' . $carrier . '" FlightNumber="' . $flight_number . '" ProviderCode="' . $provider_code . '" Origin="' . $origin . '" Destination="' . $destination . '" DepartureTime="' . $departure_time . '" ArrivalTime="' . $arrival_time . '" FlightTime="' . $flight_time . '" ' . $distance . ' ' . $equipment . ' ChangeOfPlane="' . $change_of_plane . '" OptionalServicesIndicator="' . $optional_services_indicator . '" ' . $availability_source . ' ' . $participant_level . ' ' . $polled_availability_option . ' AvailabilityDisplayType="Fare Shop/Optimal Shop" ' . $e_ticketability . ' ' . $link_availability . '>
                            ' . $air_code_share . '
                            <AirAvailInfo ProviderCode="' . $provider_code . '">
                                <BookingCodeInfo BookingCounts="' . $booking_counts . '"></BookingCodeInfo>
                            </AirAvailInfo>
                            <FlightDetails ' . $equipment . ' Destination="' . $destination . '" Origin="' . $origin . '" Key="' . $flight_detail_key . '" FlightTime="' . $flight_time . '" ArrivalTime="' . $arrival_time . '" DepartureTime="' . $departure_time . '" ></FlightDetails>
                        </AirSegment>';
                        // OriginTerminal="S" DestinationTerminal="4"
                        $air_segment [] = $segment;
                    }
                }
            }
        }
         $cabin_class = ucfirst($search_data['data']['cabin_class']);
        // debug($search_data);exit;
        $PermittedCabins = '<PermittedCabins>
                            <CabinClass xmlns="http://www.travelport.com/schema/common_v41_0" Type="'.$cabin_class.'"/>
                            </PermittedCabins>';
        $all_price_modfier = '<AirPricingModifiers FaresIndicator="AllFares">'.$PermittedCabins.'</AirPricingModifiers>';
        $air_pricing_command = '<AirPricingCommand xmlns="http://www.travelport.com/schema/air_v39_0" CabinClass="'.$cabin_class.'"></AirPricingCommand>';
        $form_of_payment = '<FormOfPayment xmlns="http://www.travelport.com/schema/common_v41_0"  Type="Cash"/>';
        $search_passenger = '<SearchPassenger Key="0" Code="ADT" xmlns="http://www.travelport.com/schema/common_v41_0" ></SearchPassenger>';
        $HostToken_content = '';
        $HostToken_content1 = '';
        $HostToken_content2 = '';
        $AirSegmentPricingModifiers1 = '';
        $AirSegmentPricingModifiers2 = '';
        $AirSegmentPricingModifiers = '';

        $air_price_req = '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
            <s:Header>
                <Action s:mustUnderstand="1" xmlns="http://schemas.microsoft.com/ws/2005/05/addressing/none">http://192.168.0.25/horiizons/index.php/flight/lost</Action>
            </s:Header>
            <s:Body xmlns:xsi="http://www.w3.org/2001/xmlschema-instance" xmlns:xsd="http://www.w3.org/2001/xmlschema">
                <AirPriceReq TraceId="394d96c00971c4545315e49584609ff6" TargetBranch="' . $this->config ['Target'] . '" xmlns="http://www.travelport.com/schema/air_v41_0" xmlns:common_v41_0="http://www.travelport.com/schema/common_v41_0">
                    <BillingPointOfSaleInfo OriginApplication="UAPI" xmlns="http://www.travelport.com/schema/common_v41_0"></BillingPointOfSaleInfo>
                    <AirItinerary>';
        foreach ($air_segment as $as_key => $segmnt) {
            $air_price_req .= $segmnt;
        }
        $air_price_req .= $HostToken_content . '
                        ' . $HostToken_content1 . '
                        ' . $HostToken_content2 . '
                    </AirItinerary>
                      ' . $all_price_modfier . '
                      ' . $adults . '
                      ' . $childs . '
                      ' . $infants . '
                    <AirPricingCommand xmlns="http://www.travelport.com/schema/air_v41_0" CabinClass="'.$search_data['data']['cabin_class'].'">' . $AirSegmentPricingModifiers1 . $AirSegmentPricingModifiers2 . $AirSegmentPricingModifiers . '</AirPricingCommand>
                   ' . $form_of_payment . ' 
                </AirPriceReq>
            </s:Body>
        </s:Envelope>';
        $response ['status'] = SUCCESS_STATUS;
        $response ['request'] = $air_price_req;
        //$path = FCPATH . "/AirPriceRQ" . $origin_value . ".xml";
        //$fp = fopen($path, "wb");
        //fwrite($fp, $air_price_req);
        //fclose($fp);
        //debug($air_price_req);exit;
        //$price_response  = $this->process_request($air_price_req);
        //$this->air_pricing_details($price_response);
        //debug($price_response);exit;
        return $response;
    }

    /**
     * Extra Services
     * @param unknown_type $request
     */
    public function get_extra_services($request, $search_id) {
        // echo 'herre'.$search_id;exit;
        // debug($request);exit;
        $search_id = '6696';
        $CI = &get_instance();
        $check_price_xml = $CI->db_cache_api->get_travelport_flight_price_seat_xml($search_id);
        // debug($check_price_xml);exit;
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned

        /* if($request['IsLCC'] != true ){//Extra Services Only fro LCC Airline
          $response ['message'] = 'Not Available'; // Message to be returned
          return $response;
          } */
        $ssr_request = $this->seat_request($request, $search_id, $check_price_xml[0]['itinerary_xml']);
        // debug($ssr_request);exit;
        for ($i = 0; $i < count($ssr_request); $i++) {
            $response ['data']['ExtraServiceDetails']['Seat'][$i] = $ssr_request[$i]['airSeatMapDeatils']['NewRow'];
        }
        // debug($response);exit;
        $response ['status'] = SUCCESS_STATUS;
        $response ['data']['ExtraServiceDetails']['MealPreference'] = $this->getMeals($request);
        // debug($response);exit;
        return $response;
        // debug($response);exit;
    }

    public function seat_request($request, $search_id, $itinerary_xml) {
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

    public function seatFormat($seat_res, $segmentCnt, $journeyCnt, $search_id) {
        // debug($seat_res);exit;
        error_reporting(0);

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
            // exit;
            // exit;
            // echo 'hssserre';
            // debug($seats_new);exit;
            // debug($arrSeatsNew);exit;
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

                // foreach ($ticket_cancel_ids as $tck => $tcv){
                //     $ChangeRequestId = $tcv['ChangeRequestId'];
                //     $TicketId = $tcv['TicketId'];
                //     //GetChangeRequestStatus
                //     $get_change_request_status = $this->get_change_request_status($ChangeRequestId);
                //     if($get_change_request_status['status'] == SUCCESS_STATUS){
                //         $ticket_cancellation_details = $get_change_request_status['data']['get_change_response'];
                //     } else {
                //         $ticket_cancellation_details = $tcv;
                //     }
                //     $passenger_ticket_details = $this->CI->db->query('select FP.origin from flight_booking_passenger_details FP 
                //                                     join flight_passenger_ticket_info FT on FT.passenger_fk=FP.origin
                //                                     where FP.flight_booking_transaction_details_fk='.$flight_booking_transaction_details_origin.' and FT.TicketId='.$TicketId)->row_array();
                //     $passenger_origin = $passenger_ticket_details['origin'];
                //     //Save Cancellation Details
                //     $this->save_ticket_cancellation_details($ticket_cancellation_details, $passenger_origin);
                //     $this->CI->common_flight->update_ticket_cancel_status($app_reference, $sequence_number, $passenger_origin);
                //     //Update Ticket Cancellation Status
                // }
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

            $send_change_response = $this->process_request($send_change_request ['request'], $send_change_request ['url']);
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
    function process_request($request, $url = '') {
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
        if (!empty($url)) {
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            curl_setopt($ch, CURLOPT_URL, $this->config ['EndPointUrl']);
        }

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
            $this->save_book_response_details($book_response, $app_reference, $sequence_number);

            //Run Non-LCC Ticket method
            $ticket_request_params = array();
            $ticket_request_params['PNR'] = $book_response['universal:AirCreateReservationRsp']['universal:UniversalRecord']['@attributes']['LocatorCode'];
            $ticket_request_params['BookingId'] = $book_response['universal:AirCreateReservationRsp']['universal:UniversalRecord']['air:AirReservation']['common_v41_0:SupplierLocator']['@attributes']['SupplierLocatorCode'];
            $ticket_request_params['TraceId'] = $book_response['universal:AirCreateReservationRsp']['@attributes']['TraceId'];
            //$ticket_service_response = $this->run_non_lcc_ticket_service($ticket_request_params, $app_reference, $sequence_number);
            $flight_booking_status = 'BOOKING_HOLD';
            $this->CI->common_flight->update_flight_booking_status($flight_booking_status, $app_reference, $sequence_number, $this->booking_source);

            // if($ticket_service_response['status'] == SUCCESS_STATUS){
            //     $ticket_response = $ticket_service_response['data']['ticket_response'];
            //     $flight_booking_status = 'BOOKING_CONFIRMED';
            //     $this->CI->common_flight->update_flight_booking_status($flight_booking_status, $app_reference, $sequence_number, $this->booking_source);
            // }
            // debug($response);exit;
        } else {
            $response ['message'] = $book_service_response['message'];
            $flight_booking_status = 'BOOKING_FAILED';
            $this->CI->common_flight->update_flight_booking_status($flight_booking_status, $app_reference, $sequence_number, $this->booking_source);
        }
        // $this->save_flight_ticket_details($booking_params, $app_reference, $sequence_number, $search_id);

        // if(valid_array($ticket_response) == true || valid_array($book_response) == true){
        //     if(valid_array($ticket_response) == true){
        //         //Run GetBookingDetails method only if Ticket Service is success
        //         $get_booking_details_req = array();
        //         $get_booking_details_req['PNR'] = $ticket_response['PNR'];
        //         $get_booking_details_req['BookingId'] = $ticket_response['BookingId'];
        //         $get_booking_details_service_response = $this->run_get_booking_details_service($get_booking_details_req, $app_reference, $sequence_number);
        //         if($get_booking_details_service_response['status'] == SUCCESS_STATUS){
        //             $fligt_ticket_details = $get_booking_details_service_response['data']['get_booking_details_response'];//GetBookingDetails Service Response
        //         } else {
        //             $fligt_ticket_details = $ticket_response;//Ticket Service Response
        //         }
        //     } else {
        //         $fligt_ticket_details = $book_response;//Book Service Response
        //     }
        //     //Save Ticket Details
        //     $this->save_flight_ticket_details($fligt_ticket_details, $app_reference, $sequence_number, $search_id);
        // }
        //debug($response);exit;

        return $response;
    }

    function process_booking($booking_params, $app_reference, $sequence_number, $search_id) {

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
            $this->save_book_response_details($book_response, $app_reference, $sequence_number);

            //Run Non-LCC Ticket method
            $ticket_request_params = array();
            $ticket_request_params['PNR'] = $book_response['universal:AirCreateReservationRsp']['universal:UniversalRecord']['@attributes']['LocatorCode'];
            $ticket_request_params['BookingId'] = $book_response['universal:AirCreateReservationRsp']['universal:UniversalRecord']['air:AirReservation']['common_v41_0:SupplierLocator']['@attributes']['SupplierLocatorCode'];
            $ticket_request_params['TraceId'] = $book_response['universal:AirCreateReservationRsp']['@attributes']['TraceId'];
            //$ticket_service_response = $this->run_non_lcc_ticket_service($ticket_request_params, $app_reference, $sequence_number);
            $flight_booking_status = 'BOOKING_HOLD';
            $this->CI->common_flight->update_flight_booking_status($flight_booking_status, $app_reference, $sequence_number, $this->booking_source);

            // if($ticket_service_response['status'] == SUCCESS_STATUS){
            //     $ticket_response = $ticket_service_response['data']['ticket_response'];
            //     $flight_booking_status = 'BOOKING_CONFIRMED';
            //     $this->CI->common_flight->update_flight_booking_status($flight_booking_status, $app_reference, $sequence_number, $this->booking_source);
            // }
            // debug($response);exit;
        } else {
            $response ['message'] = $book_service_response['message'];
            $flight_booking_status = 'BOOKING_FAILED';
            $this->CI->common_flight->update_flight_booking_status($flight_booking_status, $app_reference, $sequence_number, $this->booking_source);
        }
        $this->save_flight_ticket_details($booking_params, $book_response, $app_reference, $sequence_number, $search_id);

        // if(valid_array($ticket_response) == true || valid_array($book_response) == true){
        //     if(valid_array($ticket_response) == true){
        //         //Run GetBookingDetails method only if Ticket Service is success
        //         $get_booking_details_req = array();
        //         $get_booking_details_req['PNR'] = $ticket_response['PNR'];
        //         $get_booking_details_req['BookingId'] = $ticket_response['BookingId'];
        //         $get_booking_details_service_response = $this->run_get_booking_details_service($get_booking_details_req, $app_reference, $sequence_number);
        //         if($get_booking_details_service_response['status'] == SUCCESS_STATUS){
        //             $fligt_ticket_details = $get_booking_details_service_response['data']['get_booking_details_response'];//GetBookingDetails Service Response
        //         } else {
        //             $fligt_ticket_details = $ticket_response;//Ticket Service Response
        //         }
        //     } else {
        //         $fligt_ticket_details = $book_response;//Book Service Response
        //     }
        //     //Save Ticket Details
        //     $this->save_flight_ticket_details($fligt_ticket_details, $app_reference, $sequence_number, $search_id);
        // }
        //debug($response);exit;

        return $response;
    }

    private function run_book_service($booking_params, $app_reference, $sequence_number, $search_id) {
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        $t = time();
        $book_service_request = $this->run_book_service_request($booking_params, $search_id);
        if ($book_service_request['status'] == SUCCESS_STATUS) {
            // debug($book_service_request ['request']);exit;
            $book_service_response = $this->process_request($book_service_request ['request']);
            $api_url = $this->config ['EndPointUrl'];
            $api_request = $book_service_request ['request'];
            $api_response = $book_service_response;
            $api_remarks = 'flight_booking(Travelport Flight)';
            $this->CI->api_model->store_api_request_booking($api_url, $api_request, $api_response, $api_remarks);
            // debug($book_service_response);exit;
            //$path = FCPATH . "/AircreateRS".$t.".xml";
            //$fp = fopen($path,"wb");fwrite($fp,$book_service_response);fclose($fp);    
            //$book_service_response = $this->CI->custom_db->get_static_response (2);//Static bOOk Data//1=>Failed;2=> Success
            $book_service_response = Converter::createArray($book_service_response);

            // debug($book_service_response);exit;
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
                }
                $response ['message'] = $error_message;

                //Log Exception
                $exception_log_message = '';
                $this->CI->exception_logger->log_exception($app_reference, $this->booking_source_name . '- (<strong>BOOK</strong>)', $exception_log_message, $book_service_response);
            }
        } else {
            $response ['status'] = FAILURE_STATUS;
        }
        // debug($response);exit;
        return $response;
    }

    /**
     * Forms the Book request
     * @param unknown_type $request
     */
    private function run_book_service_request($params, $search_id) {
        // debug($params);exit;
        $segmentdata = $params['ResultToken']['flight_list'][0]['flight_detail'];
        $search_id1 = $search_id . "_" . $params['ResultToken']['flight_list'][0]['flight_detail'][0]['random_number'];
        $arrAllOrigins = array();
        $arrAllDest = array();
        for ($s = 1; $s < count($segmentdata) - 2; $s++) {
            $arrAllOrigins[] = $segmentdata[$s]['origin'];
            $arrAllDest[] = $segmentdata[$s]['destination'];
        }
        $CI = &get_instance();
        $check_price_xml = $CI->db_cache_api->get_travelport_flight_price_xml($search_id1);
        $AirPricingSolution_xml = $check_price_xml[0]['price_xml'];
        $AirItinerary_xml = $check_price_xml[0]['itinerary_xml'];
        //debug($AirPricingSolution_xml);
        $AirItinerary_xml = str_replace("common_v41_0:", '', $AirItinerary_xml);
        $AirPricingSolution_xml = $AirPricingSolution_xml1 = str_replace('common_v41_0:', '', $AirPricingSolution_xml);

        //echo "<pre>";print_r($AirPricingSolution_xml);exit;
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
        // debug($arrItinaryXML);exit;
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
        $AirPricingSolution_xml = $doc->saveXML();

        $doc->loadXML($AirPricingSolution_xml);

        $array_sr = array('<AirItinerary>', '</AirItinerary>', '<?xml version="1.0"?>');
        $AirItinerary_xml = str_replace($array_sr, '', $AirItinerary_xml);
        $fragment = $doc->createDocumentFragment();
        $fragment->appendXML($AirItinerary_xml);
        $AirPricingInfo = $doc->getElementsByTagName("AirPricingInfo")->item(0);
        $AirPricingInfo->removeAttribute('PlatingCarrier');
        $doc->documentElement->insertBefore($fragment, $AirPricingInfo);

        $xp = new DOMXPath($doc);
        foreach ($xp->query('//Endorsement') as $key => $tn) {
            $tn->parentNode->removeChild($tn);
        }

        // Adding BookingTravelerRef for Passengers
        $PassengerType = $xp->evaluate("/AirPricingSolution/AirPricingInfo//PassengerType");
        for ($i = 0; $i < $PassengerType->length; $i++) {
            $Passenger = $PassengerType->item($i);
            //if($BookingTravelerRef == ''){
            $Passenger->setAttribute("BookingTravelerRef", $i);
            //}else{
            $Passenger->setAttribute("BookingTravelerRef", $i);
            //}                                             
        }
        $AirPricingSolution_xml = str_replace('<?xml version="1.0"?>', '', $doc->saveXML());
        $arrPriceXML = Converter::createArray($AirPricingSolution_xml1);
        $arrPriceKey = $arrPriceXML['AirPricingSolution']['@attributes']['Key'];
        //debug($arrPriceXML);exit;
        $address = '';
        $Payment_address = '';
        $Payment_address = '<AddressName>' . $params['Passengers'][0]['City'] . ' , ' . $params['Passengers'][0]['CountryName'] . '</AddressName>
                        <Street>' . $params['Passengers'][0]['PinCode'] . '</Street>
                        <City>' . $params['Passengers'][0]['City'] . '</City>
                        <PostalCode>' . $params['Passengers'][0]['PinCode'] . '</PostalCode>
                        <Country>' . $params['Passengers'][0]['CountryCode'] . '</Country>';

        $address .= '<Address>' . $Payment_address . '</Address>';

        $adults = '';
        $paxId = 0;
        $prefix_a = '';
        $childs = '';
        $prefix_c = '';
        $infants = '';
        $prefix_i = '';
        $i = 0;
        $provider = '1G';
        $air_line_pcode = $params['ResultToken']['flight_list'][0]['flight_detail'][0]['carrier'];
        // debug($params);exit;
        foreach ($params['Passengers'] as $passenger) {
            if (isset($passenger['MealId']) == true && valid_array($passenger['MealId']) == true) {
                $MealsDetails = $passenger['MealId'];
            } else {
                $MealsDetails = array();
            }
            $MealsDetails = $this->meal_request_details($MealsDetails);
            // debug($MealsDetails);
            if ($passenger['PaxType'] == 1) {
                $prefix_a = 'Prefix="' . (isset($passenger['Title']) ? $passenger['Title'] : '') . '"';
                if ($passenger['Gender'] == '1') {
                    $gender = 'F';
                } else {
                    $gender = 'M';
                }
                if ($i == 0) {
                    $address = $address;
                } else {
                    $address = '';
                }
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
                <Email EmailID="' . $passenger['Email'] . '" Type="P" ></Email>';
                if ($passenger['PassportNumber'] != "" && $passenger['PassportExpiry'] != "") {
                    $adults .= '<SSR Carrier="' . $air_line_pcode . '" FreeText="P/' . $Passenger_PassportNationality . '/' . $adult_passport_number . '/' . $Passenger_PassportIssuedBy . '/' . $Passenger_DOBp . '/' . $Passenger_Genderp . '/' . $Passenger_DOEp . '/' . $Passenger_Lastnamep . '/' . $Passenger_Firstnamep . '" Status="HK" Type="DOCS"></SSR>';
                }
                if (isset($MealsDetails) && valid_array($MealsDetails)) {
                    $k = 0;
                    foreach ($arrAirseg as $AirKey => $AirVal) {
                        $adults .= '<SSR Carrier="' . $params['ResultToken']['flight_list'][0]['flight_detail'][$k]['carrier'] . '" Status="HK" SegmentRef="' . $AirVal . '" Type="' . $MealsDetails[$k]['Code'] . '"></SSR>';
                        $k++;
                    }
                }
                $adults .= $address . ' 

                    </BookingTraveler>';
            } else if ($passenger['PaxType'] == 2) {
                $prefix_c = 'Prefix="' . (isset($passenger['Title']) ? $passenger['Title'] : '') . '"';
                if ($passenger['Gender'] == '1') {
                    $gender = 'F';
                } else {
                    $gender = 'M';
                }
                if ($i == 0) {
                    $address = $address;
                } else {
                    $address = '';
                }
                $Passenger_PassportNationality = $passenger['CountryCode'];
                $adult_passport_number = $passenger['PassportNumber'];
                $Passenger_PassportIssuedBy = $passenger['CountryCode'];
                $Passenger_DOBp = date('dMy', strtotime($passenger['DateOfBirth']));
                $Passenger_Genderp = $gender;
                $Passenger_DOEp = date('dMy', strtotime($passenger['PassportExpiry']));
                $Passenger_Firstnamep = $passenger['FirstName'];
                $Passenger_Lastnamep = $passenger['LastName'];
                $childs .= '<BookingTraveler Key="' . $paxId . '" TravelerType="CNN" DOB="' . $passenger['DateOfBirth'] . '" Gender="' . $gender . '" xmlns="http://www.travelport.com/schema/common_v41_0">
                <BookingTravelerName ' . $prefix_c . ' First="' . $passenger['FirstName'] . '"   Last="' . $passenger['LastName'] . '" ></BookingTravelerName>
                <PhoneNumber Number="' . $passenger['ContactNo'] . '" Type="Mobile" ></PhoneNumber>
                <Email EmailID="' . $passenger['Email'] . '" Type="P" ></Email>';
                if ($passenger['PassportNumber'] != "" && $passenger['PassportExpiry'] != "") {
                    $childs .= '<SSR Carrier="' . $air_line_pcode . '" FreeText="P/' . $Passenger_PassportNationality . '/' . $adult_passport_number . '/' . $Passenger_PassportIssuedBy . '/' . $Passenger_DOBp . '/' . $Passenger_Genderp . '/' . $Passenger_DOEp . '/' . $Passenger_Lastnamep . '/' . $Passenger_Firstnamep . '" Status="HK" Type="DOCS"></SSR>';
                }
                if (isset($MealsDetails) && valid_array($MealsDetails)) {
                    $k = 0;
                    foreach ($arrAirseg as $AirKey => $AirVal) {
                        $childs .= '<SSR Carrier="' . $params['ResultToken']['flight_list'][0]['flight_detail'][$k]['carrier'] . '" Status="HK" SegmentRef="' . $AirVal . '" Type="' . $MealsDetails[$k]['Code'] . '"></SSR>';
                        $k++;
                    }
                }
                $childs .= '<NameRemark Category="AIR">
                                    <RemarkData>P-C10</RemarkData>
                                </NameRemark>';

                $childs .= '</BookingTraveler>';
            } else if ($passenger['PaxType'] == 3) {
                $prefix_i = 'Prefix="' . (isset($passenger['Title']) ? $passenger['Title'] : '') . '"';
                if ($passenger['Gender'] == '1') {
                    $gender = 'F';
                } else {
                    $gender = 'M';
                }
                if ($i == 0) {
                    $address = $address;
                } else {
                    $address = '';
                }
                $Passenger_PassportNationality = $passenger['CountryCode'];
                $adult_passport_number = $passenger['PassportNumber'];
                $Passenger_PassportIssuedBy = $passenger['CountryCode'];
                $Passenger_DOBp = date('dMy', strtotime($passenger['DateOfBirth']));
                $Passenger_Genderp = $gender;
                $Passenger_DOEp = date('dMy', strtotime($passenger['PassportExpiry']));
                $Passenger_Firstnamep = $passenger['FirstName'];
                $Passenger_Lastnamep = $passenger['LastName'];
                $infants .= '<BookingTraveler Key="' . $paxId . '" TravelerType="INF" DOB="' . $passenger['DateOfBirth'] . '" Gender="' . $gender . '" xmlns="http://www.travelport.com/schema/common_v41_0">
                <BookingTravelerName ' . $prefix_i . ' First="' . $passenger['FirstName'] . '"   Last="' . $passenger['LastName'] . '" ></BookingTravelerName>
                <PhoneNumber Number="' . $passenger['ContactNo'] . '" Type="Mobile" ></PhoneNumber>
                <Email EmailID="' . $passenger['Email'] . '" Type="P" ></Email>';
                if ($passenger['PassportNumber'] != "" && $passenger['PassportExpiry'] != "") {
                    $infants .= '<SSR Carrier="' . $air_line_pcode . '" FreeText="P/' . $Passenger_PassportNationality . '/' . $adult_passport_number . '/' . $Passenger_PassportIssuedBy . '/' . $Passenger_DOBp . '/' . $Passenger_Genderp . '/' . $Passenger_DOEp . '/' . $Passenger_Lastnamep . '/' . $Passenger_Firstnamep . '" Status="HK" Type="DOCS"></SSR>';
                }
                $infants .= '<NameRemark Category="AIR">
                                    <RemarkData>' . $passenger['DateOfBirth'] . '</RemarkData>
                                </NameRemark>';

                $infants .= '</BookingTraveler>';
            }
            $paxId++;
            $i++;
        }

        if ($provider == "1G") {
            $payment = '<FormOfPayment xmlns="http://www.travelport.com/schema/common_v41_0" Key="' . $arrPriceKey . '" Type="Cash" />';
            $ActionStatus = '<ActionStatus ProviderCode="' . $provider . '" TicketDate="T*" Type="ACTIVE" QueueCategory="01" xmlns="http://www.travelport.com/schema/common_v41_0" ></ActionStatus>';
        }
        $AirCreateReservationReq = '<?xml version="1.0" encoding="utf-8"?>
        <s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
                        <s:Header/>
                        <s:Body>
                            <univ:AirCreateReservationReq xmlns:air="http://www.travelport.com/schema/air_v41_0"  xmlns:common_v41_0="http://www.travelport.com/schema/common_v41_0" xmlns:univ="http://www.travelport.com/schema/universal_v41_0" AuthorizedBy="user" TraceId="394d96c00971c4545315e49584609ff6" TargetBranch="' . $this->config ['Target'] . '" RetainReservation="None">
                            <BillingPointOfSaleInfo  xmlns="http://www.travelport.com/schema/common_v41_0" OriginApplication="UAPI" ></BillingPointOfSaleInfo>
                            ' . $adults . '
                            ' . $infants . '
                            ' . $childs . '                            
                            ' . $payment . '
                            ' . $AirPricingSolution_xml . '
                            ' . $ActionStatus . '
                            </univ:AirCreateReservationReq>
                        </s:Body>
                        </s:Envelope>';
        $AirCreateReservationReq = str_replace("HostToken Key", "HostToken xmlns='http://www.travelport.com/schema/common_v41_0' Key", $AirCreateReservationReq);
        $AirCreateReservationReq = str_replace("<AirPricingSolution", "<AirPricingSolution xmlns='http://www.travelport.com/schema/air_v41_0'", $AirCreateReservationReq);
        // debug($AirCreateReservationReq);exit;
        //$air_create_reservation_response = $this->process_request($AirCreateReservationReq);
        //$AirCreateReservationReq = file_get_contents('http://192.168.0.46/travelomatix_services/travelport_booking_req.xml');
        // $air_create_reservation_response = $this->process_request($AirCreateReservationReq);
        $path = FCPATH . "/AircreateRQ" . $air_line_pcode . ".xml";
        $fp = fopen($path, "wb");
        fwrite($fp, $AirCreateReservationReq);
        fclose($fp);
        // $path = FCPATH . "/AircreateRS".$air_line_pcode.".xml";
        // $fp = fopen($path,"wb");fwrite($fp,$air_create_reservation_response);fclose($fp);
        //debug($AirCreateReservationReq);
        // debug($air_create_reservation_response);exit;
        // echo $AirCreateReservationReq;exit;
        $request ['request'] = $AirCreateReservationReq;
        $request ['status'] = SUCCESS_STATUS;
        return $request;
    }
     private function save_flight_ticket_details($booking_params, $book_response, $app_reference, $sequence_number, $search_id){
        // debug($booking_params);exit;
        $flight_booking_transaction_details_fk = $this->CI->custom_db->single_table_records('flight_booking_transaction_details', 'origin', array('app_reference' => $app_reference, 'sequence_number' => $sequence_number));
        $flight_booking_transaction_details_fk = $flight_booking_transaction_details_fk['data'][0]['origin'];
        $flight_booking_itinerary_details_fk = $this->CI->custom_db->single_table_records('flight_booking_itinerary_details', 'airline_code', array('app_reference' => $app_reference));

        $passenger_details = $this->CI->custom_db->single_table_records('flight_booking_passenger_details', '', array('app_reference' => $app_reference));
        $passenger_details =$passenger_details['data'];
        // debug($passenger_details);exit;
        $itineray_price_details = $booking_params['flight_data']['PriceBreakup'];
        // debug($itineray_price_details);exit;
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
        

        // $passenger_details = force_multple_data_format($passenger_details);
        $get_passenger_details_condition = array();
        $get_passenger_details_condition['flight_booking_transaction_details_fk'] = $flight_booking_transaction_details_fk;
        $passenger_details_data = $GLOBALS['CI']->custom_db->single_table_records('flight_booking_passenger_details', 'origin, passenger_type', $get_passenger_details_condition);
        $passenger_details_data = $passenger_details_data['data'];
        $passenger_origins = group_array_column($passenger_details_data, 'origin');
        $passenger_types = group_array_column($passenger_details_data, 'passenger_type');
        // echo 'mnngng';
        // debug($passenger_details);exit;
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
            $ticket_id = '';
            $ticket_number = '';
            
           
            //Update Passenger Ticket Details
            $this->CI->common_flight->update_passenger_ticket_info($passenger_fk, $ticket_id, $ticket_number, $single_pax_fare_breakup[$pax_type]);
        }
    }
    /**
     * Process booking
     * 
     * @param string $book_id           
     * @param array $booking_params         
     */
    // function process_booking($book_id, $temp_booking) {
    function process_booking_old($booking_params, $app_reference, $sequence_number, $search_id) {
        //echo 'herre';exit;
        $response ['status'] = FAILURE_STATUS;
        $response ['data'] = array();
        $booking_id = $temp_booking ['tmp_flight_pre_booking_id'];
        $b_source = $this->source_code;

        // booking source
        $booking_src_arr = unserialized_data($temp_booking ['booking_source']);
        $key = array_search(TRAVELPORT_FLIGHT_BOOKING_SOURCE, $booking_src_arr);
        $no_of_keys = count(array_keys($booking_src_arr, TRAVELPORT_FLIGHT_BOOKING_SOURCE));
        if ($no_of_keys == 2) {
            // both are travelport
            $token_data_list [0] = unserialized_data($temp_booking ['token'] [0] ['token']);
            $token_data_list [1] = unserialized_data($temp_booking ['token'] [1] ['token']);
        } else {
            // oneway travelport
            $token_data_list [0] = unserialized_data($temp_booking ['token'] [$key] ['token']);
        }

        if (isset($token_data_list) && valid_array($token_data_list)) {
            foreach ($token_data_list as $_fk => $_btkn) {
                $air_create_reservation_request = $this->air_create_reservation_request($temp_booking, $_btkn);
                if ($air_create_reservation_request ['status'] == SUCCESS_STATUS) {
                    $air_create_reservation_response [] = $this->process_request($air_create_reservation_request ['data'] ['request']);

                    // update booking status here FIXIT
                }
            }

            if (isset($air_create_reservation_response) && valid_array($air_create_reservation_response)) {
                $response ['data'] = $air_create_reservation_response;
                $response ['status'] = SUCCESS_STATUS;
            }
        }
        return $response;
    }

    function remove_node(&$node) {
        $pnode = $node->parentNode;
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
        // debug($meal);exit;
        return $meal;
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
        // debug($search_data);exit;
        /* if (empty($this->api_session_id) == true) {
          return $response;
          } */
        //echo "cpoming";exit;
        if ($search_data ['status'] == SUCCESS_STATUS) {
            // Flight search RQ

            $search_request = $this->flight_low_fare_search_req($search_data ['data']);
            //debug($search_request);exit;            
            if ($search_request ['status'] = SUCCESS_STATUS) {
                $response ['status'] = SUCCESS_STATUS;

                $curl_request = $this->form_curl_params($search_request['payload'], $search_request ['url'], $search_request ['soap_action']);
                if (isset($search_request['payload_return'])) {
                    $curl_request[] = $this->form_curl_params($search_request['payload_return'], $search_request ['url'], $search_request ['soap_action']);
                    $response ['data'][] = $curl_request[0]['data'];
                    $this->retunr_domestic = $curl_request[0]['data'];
                }

                $response ['data'] = $curl_request['data'];
            }
        }
        //debug($this->retunr_domestic);exit;
        return $response;
    }

    /**
     * Save Book Service Response
     * @param unknown_type $book_response
     * @param unknown_type $app_reference
     * @param unknown_type $sequence_number
     */
    private function save_book_response_details($book_response, $app_reference, $sequence_number) {
        $update_data = array();
        $update_condition = array();
        $update_data['book_id'] = $book_response['universal:AirCreateReservationRsp']['universal:UniversalRecord']['@attributes']['LocatorCode'];
        $update_data['airline_pnr'] = $book_response['universal:AirCreateReservationRsp']['universal:UniversalRecord']['air:AirReservation']['common_v41_0:SupplierLocator']['@attributes']['SupplierLocatorCode'];
        $update_data['pnr'] = $book_response['universal:AirCreateReservationRsp']['universal:UniversalRecord']['universal:ProviderReservationInfo']['@attributes']['LocatorCode'];

        $update_condition['app_reference'] = $app_reference;
        $update_condition['sequence_number'] = $sequence_number;

        $this->CI->custom_db->update_record('flight_booking_transaction_details', $update_data, $update_condition);

        $flight_booking_status = 'BOOKING_HOLD';
        $this->CI->common_flight->update_flight_booking_status($flight_booking_status, $app_reference, $sequence_number, $this->booking_source);
    }

    /**
     *
     * @param
     *          $search_id
     */
    function booking_url($search_id) {
        
    }

    function pre_booking_process($unique_onward_return_booking_source, $source_index, $token_param, $transaction_id, $search_id, & $flight_data) { // debug($source_index);exit;
        $response = array();
        $search_data = $this->search_data($search_id);

        // $response['status'] = FAILURE_STATUS;
        // obtain fare information for an Air Itinerary

        $air_price_info = $this->air_pricing_details($token_param);
        $token_param = force_multple_data_format($token_param);
        if ($air_price_info ['status'] == SUCCESS_STATUS) {
            // if($source_index == 0) {
            // $currnecy = $air_price_info['data']['total_price_curr'];
            // $api_total_display_fare = $air_price_info['data']['total_price'];
            // $api_total_tax = $air_price_info['data']['taxes'];
            // $api_total_fare = $air_price_info['data']['base_price'];
            // }else {
            // $currnecy = $air_price_info['data']['total_price_curr'];
            // $api_total_display_fare = $flight_data['price']['api_total_display_fare'] + $air_price_info['data']['total_price'];
            // $api_total_tax = $flight_data['price']['total_breakup']['api_total_tax'] + $air_price_info['data']['taxes'];
            // $api_total_fare = $flight_data['price']['total_breakup']['api_total_fare'] + $air_price_info['data']['base_price'];
            // }
            // $flight_data['price'] = array(
            // 'api_currency' => $currnecy,
            // 'api_total_display_fare' => $api_total_display_fare,
            // 'total_breakup' => array(
            // 'api_total_tax' => $api_total_tax,
            // 'api_total_fare' => $api_total_fare,
            // ),
            // 'pax_breakup' => array()
            // );

            $flight_data ['fare'] [$source_index] = array(
                'api_currency' => $air_price_info ['data'] ['total_price_curr'],
                'api_total_display_fare' => $air_price_info ['data'] ['total_price'],
                'total_breakup' => array(
                    'api_total_tax' => $air_price_info ['data'] ['taxes'],
                    'api_total_fare' => $air_price_info ['data'] ['base_price']
                ),
                'pax_breakup' => array()
            );
            $page_data ['flight_details'] = $air_price_info ['data'];
            $page_data ['flight_details'] ['connection'] = $token_param [0] ['connection'];
            // $this->template->view ( 'flight/travelport/travelport_booking_page', $page_data );
            if (!empty($air_price_info ['data'] ['price_xml'])) {
                $token = serialized_data($page_data ['flight_details']);
                $response ['status'] = SUCCESS_STATUS;
                $response = array(
                    'token' => $token
                );

                $travelport_price_xml_arr = array(
                    'attributes' => json_encode(array(
                        'price_xml' => $air_price_info ['data'] ['price_xml']
                    )),
                    'reference_id' => $transaction_id,
                    'booking_source' => $this->source_code,
                    'comments' => 'Travelport',
                    'created_by_id' => intval(@$GLOBALS ['CI']->entity_user_id),
                    'created_datetime' => date('Y-m-d H:i:s')
                );
                $CI = &get_instance();
                $CI->db->insert('tmp_flight_pre_booking_details', $travelport_price_xml_arr);
                // $response['status'] = SUCCESS_STATUS;
            }
        }
        return $response;
    }

    /**
     * process soap API request
     *
     * @param string $request
     */
    function form_curl_params($request, $url) {
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
            "Content-length: " . strlen($request),
            "Accept-Encoding: gzip,deflate"
        );

        $data['data'] = $curl_data;

        return $data;
    }

}
