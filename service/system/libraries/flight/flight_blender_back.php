<?php
/**
 * Combines the Data from multiple APIs
 * @author Jaganath
 *
 */
Class Flight_Blender
{
    function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->library('multi_curl');
        $this->CI->load->library('flight/Common_Flight');
    }
    /**
     * Assigns the Curl Parameters(URL,Header info.,Request)
     * @param unknown_type $request_params
     * @param unknown_type $curl_request
     * @param unknown_type $curl_url
     * @param unknown_type $curl_header
     * @param unknown_type $curl_booking_source
     */
    public function assign_curl_params($request_params, & $curl_request, & $curl_url, & $curl_header, & $curl_booking_source,  & $curl_remarks)
    {
        $request = array($request_params['request']);
        $url = array($request_params['url']);
        $header = array($request_params['header']);
        $booking_source = array($request_params['booking_source']);
        $remarks = (isset($request_params['remarks']) == true ? array(trim($request_params['remarks'])) : array(''));
        
        $curl_request = array_merge($curl_request, $request);
        $curl_url = array_merge($curl_url, $url);
        $curl_header = array_merge($curl_header, $header);
        $curl_booking_source = array_merge($curl_booking_source, $booking_source);
        $curl_remarks = array_merge($curl_remarks, $remarks);
    }
    /**
     * Flight Active Booking Sources
     * 
     */
    private function flight_active_booking_sources($condition = array())
    {
        $active_booking_source_condition = array();
        $active_booking_source_condition[] = array('BS.meta_course_list_id' ,'=', '"'.META_AIRLINE_COURSE.'"');
        $active_booking_source_condition[] = array('DL.origin' ,'=', get_domain_auth_id());
        
        $active_booking_source_condition = array_merge($active_booking_source_condition, $condition);
        $active_booking_sources = $this->CI->db_cache_api->get_active_api_booking_source($active_booking_source_condition);

        return $active_booking_sources;
    }
    /**
     *  FIXME: check for other services(other than search)
     * Authenticates the API
     */
    private function api_authentication($active_booking_sources = array())
    {
        $curl_params = array();
        $curl_request = array();
        $curl_url = array();
        $curl_header = array();
        $curl_booking_source = array();
        $curl_remarks = array();
        if(valid_array($active_booking_sources) == true){
            $flight_active_booking_sources = $active_booking_sources;
        } else {
            $flight_active_booking_sources = $this->flight_active_booking_sources();
        }
        $flight_obj = array();

        foreach ($flight_active_booking_sources as $bs_k => $bs_v){
            if ($bs_v['check_auth'] == 1) {
            $flight_obj_ref = load_flight_lib($bs_v['booking_source'], '', true,$bs_v['config_table_origin']);
            $flight_obj[$bs_v['booking_source']] = $flight_obj_ref;
            //Authentication Request
            $authentication_request = $this->CI->$flight_obj_ref->get_authentication_request();

            if(empty($this->CI->$flight_obj_ref->api_session_id) == true && $authentication_request['status'] == SUCCESS_STATUS){
                $authentication_request['data']['remarks'] = $this->CI->$flight_obj_ref->booking_source_name;
                $this->assign_curl_params($authentication_request['data'],$curl_request, $curl_url, $curl_header, $curl_booking_source, $curl_remarks);
            }
           } 
        }
        $curl_params['booking_source'] = $curl_booking_source;
        $curl_params['request'] = $curl_request;
        $curl_params['url'] = $curl_url;
        $curl_params['header'] = $curl_header;
        $curl_params['remarks'] = $curl_remarks;
        $authentication_result = $this->CI->multi_curl->execute_multi_curl($curl_params);
        foreach ($flight_obj as $obj_k => $obj_v){
            if(valid_array($authentication_result) == true){
                
                if(isset($authentication_result[$this->CI->$obj_v->booking_source])){
                    $this->CI->$obj_v->set_api_session_id($authentication_result[$this->CI->$obj_v->booking_source]);
                }
            }
        }
    }
    /**
     * Returns flight list
     * @param int $search_id
     */
        
    public function flight_list($search_id, $cache_key) {
        $curl_params = array();
        $curl_request = array();
        $curl_url = array();
        $curl_header = array();
        $curl_booking_source = array();
        $curl_remarks = array();

        $seach_result = array();
        $formatted_seach_result = array();
        $final_flight_list = array();
        $final_flight_list['status'] = FAILURE_STATUS;
        $search_data = $this->search_data($search_id);
        $s_booking_source = $search_data['data']['SearchBookingSource'];
        switch($s_booking_source){
            case 'PTBSID0000000002':
                $condition = array(array('BS.source_id' ,'=', '"PTBSID0000000002"'));
                break;
            case 'PTBSID0000000009':
                $condition = array(array('BS.source_id' ,'=', '"PTBSID00000000071"'));
                break;
            case 'PTBSID0000000010':
                $condition = array(array('BS.source_id' ,'=', '"PTBSID0000000007"'));
                break;
            case 'PTBSID0000000011':
                $condition = array(array('BS.source_id' ,'=', '"PTBSID0000000023"'));
                break;
        }
        $flight_active_booking_sources = $this->flight_active_booking_sources($condition);
        // Temp book source
        if (valid_array($search_data['data']['from_country'])) {
            $segments_country = array_merge($search_data['data']['from_country'], $search_data['data']['to_country']);
        } else {
            $segments_country[] = $search_data['data']['from_country'];
            array_push($segments_country, $search_data['data']['to_country']);
        }
        foreach ($segments_country as $country) {
            if (strpos($country, 'USA') !== false || strpos($country, 'Canada') !== false) {
                $country_arr[] = 1;
            } else {
                $country_arr[] = 0;
            }
        }
        // if (in_array(1, $country_arr)) {
        //     foreach ($flight_active_booking_sources as $b_key => $booking_source) {
        //         if (strpos($booking_source['booking_source'], 'PTBSID0000000021') !== false) {
        //             if (count($flight_active_booking_sources) == 3) {
        //                 unset($flight_active_booking_sources[0]);
        //             }
        //             if (count($flight_active_booking_sources) == 2) {
        //                 if (in_array('PTBSID0000000002', array_column($flight_active_booking_sources, 'booking_source'))) {
        //                     unset($flight_active_booking_sources[0]);
        //                 }
        //             }
        //         } else {
        //             $flight_active_booking_sources[$b_key]['name'] = $booking_source['name'];
        //             $flight_active_booking_sources[$b_key]['booking_source'] = $booking_source['booking_source'];
        //             $flight_active_booking_sources[$b_key]['check_auth'] = $booking_source['check_auth'];
        //         }
        //     }
        // } else {
        //     $count = count($flight_active_booking_sources);
        //     foreach ($flight_active_booking_sources as $b_key => $booking_source) {
        //         if (strpos($booking_source['booking_source'], 'PTBSID0000000021') !== false) {
        //             unset($flight_active_booking_sources[$b_key]);
        //         }
        //     }
        // }
        $flight_obj = array();
        if(valid_array($flight_active_booking_sources)){
            foreach ($flight_active_booking_sources as $bs_k => $bs_v) {
                //if($bs_k == 1){
                    $flight_obj_ref = load_flight_lib($bs_v['booking_source'], '', true,$bs_v['config_table_origin']);
                    $flight_obj[$bs_v['booking_source']] = $flight_obj_ref;
                    
                    $search_request = $this->CI->$flight_obj_ref->get_search_request($search_id);

                    if ($search_request['status'] == SUCCESS_STATUS) {
                        $search_request['data']['remarks'] = $this->CI->$flight_obj_ref->booking_source_name;
                        /* merge the all API request */
                        $this->assign_curl_params($search_request['data'], $curl_request, $curl_url, $curl_header, $curl_booking_source, $curl_remarks);

                    }
                //}

            }
            $curl_params['booking_source'] = $curl_booking_source;
            $curl_params['request'] = $curl_request;
            $curl_params['url'] = $curl_url;
            $curl_params['header'] = $curl_header;
            $curl_params['remarks'] = $curl_remarks;
            $curl_params['search_id'] = $search_id;
        }else{
            echo "No API activated."; exit;
        }
       
        if(valid_array($curl_params)){
           
           $total_request_count = 0;
           /*if(($search_data['data']['is_domestic'] == 1) && ($search_data['data']['trip_type'] == 'return')&&($search_data['data']['SearchBookingSource'] == 'PTBSID0000000010')){
                $curl_params['booking_source'] = array('PTBSID0000000007' , 'PTBSID0000000007');
           }*/
            foreach($curl_params['booking_source'] as $key => $details)
            {
                if($details == SPICEJET_FLIGHT_BOOKING_SOURCE){
                    $request = $curl_params['request'][$key];
                   foreach($request as $req_key => $req_val){
                       $curl_params['booking_source'][$req_key] = $details;
                       $curl_params['request'][$req_key] = $req_val;
                       $curl_params['url'][$req_key] = $curl_params['url'][$key];
                       $curl_params['header'][$req_key] = $curl_params['header'][$key];
                       $curl_params['remarks'][$req_key] = $curl_params['remarks'][$key];
                       //$curl_params['search_id'][$req_key] = $curl_params[$key];
                   }
                }
                /*if($details == TRAVELPORT_FLIGHT_BOOKING_SOURCE || $details == SABARE_FLIGHT_BOOKING_SOURCE){*/
                if(($details == TRAVELPORT_FLIGHT_BOOKING_SOURCE) || ($details == TRAVELPORT_LCC_FLIGHT_BOOKING_SOURCE)){
                    $travelport_req = $curl_params['request'][$key];
                    foreach($travelport_req as $req_key => $request){
                        foreach ($request as $r_k => $r_v) {
                            $curl_params['booking_source'][$req_key]=$details;
                            $curl_params['booking_source_origin'][$req_key]=$req_key;
                            $curl_params['request'][$req_key]=$r_v;
                            $curl_params['url'][$req_key]=$curl_params['url'][$key];
                            $curl_params['header'][$req_key]=$curl_params['header'][$key];
                            $curl_params['remarks'][$req_key]=$curl_params['remarks'][$key];
                            // debug($curl_params);exit;
                            if(($details == TRAVELPORT_FLIGHT_BOOKING_SOURCE) || ($details == TRAVELPORT_LCC_FLIGHT_BOOKING_SOURCE)){
                                $tp_cpunt=count($curl_params['header'][$key]);
                                $curl_params['header'][$req_key][4] = "Content-length: " . strlen($r_v);
                            }
                            
                            $total_request_count++;
                       }

                    }
                   
                }
                
            }
        }
        $seach_result_array = $this->CI->multi_curl->execute_multi_curl($curl_params);
        foreach ($flight_obj as $fo_k => $fo_v) {
         // debug($seach_result_array[$fo_k]); exit;
            if (isset($seach_result_array[$fo_k]) == true) {
                $flight_data = $this->CI->$fo_v->get_flight_list($seach_result_array[$fo_k], $search_id);

                if ($flight_data['status'] == SUCCESS_STATUS && valid_array($flight_data['data']['FlightDataList']['JourneyList']) == true) {
                    //Merge Flight List
                    if (valid_array($formatted_seach_result) == false) {//Assiging the flight data, if not set(only for first API data, for next API's, it will be merged)
                        $formatted_seach_result = $flight_data;
                    } else {
                        $this->merge_flight_list($flight_data['data']['FlightDataList']['JourneyList'], $formatted_seach_result);
                    }
                }
            }
        }
        if (isset($formatted_seach_result['status']) == true && $formatted_seach_result['status'] == SUCCESS_STATUS) {
            if (count($flight_active_booking_sources) > 1) {

                $JourneyList = array();
                $JourneyList = $formatted_seach_result['data']['FlightDataList']['JourneyList'];
                //Eliminate Duplicate Flights
                //$JourneyList = $this->eliminate_duplicate_flights($JourneyList);
                
                $JourneyList = $this->sort_flight_list($JourneyList);
                //debug($JourneyList);exit;
                $formatted_seach_result['data']['FlightDataList']['JourneyList'] = $JourneyList;
            }
            $carry_cache_key = $cache_key;
            $this->CI->load->library('flight/common_flight');
            $formatted_seach_result['data']['FlightDataList'] ['JourneyList'] = $this->CI->common_flight->update_markup_and_insert_cache_key_to_token($formatted_seach_result['data']['FlightDataList'] ['JourneyList'], $carry_cache_key, $search_id);
            $final_flight_list = $formatted_seach_result;
        } else {
            $final_flight_list['message'] = 'No Flights Found';
        }
        return $final_flight_list;
    }
        
    /**
     * Fare Rules
     * @param unknown_type $request
     */
    public function fare_rules($request)
    {
        $fare_rule_result = array();
        $fare_rule_result['data'] = array();
        $fare_rule_result['status'] = FAILURE_STATUS;
        $fare_rule_result['message'] = '';
        
        $ResultToken = trim($request['ResultToken']);
        $flight_search_data = Common_Flight::read_record($ResultToken);
        if(valid_array($flight_search_data) == true){
            $flight_search_data = json_decode($flight_search_data[0], true);
            $fare_rule_request = array_values(unserialized_data($flight_search_data['ResultToken']));
            $fare_rule_request = $fare_rule_request[0];
            $booking_source = $fare_rule_request['booking_source'];
            
            $active_booking_source_condition = array();
            $active_booking_source_condition[] = array('BS.source_id', '=', '"'.$booking_source.'"');
            $flight_active_booking_sources = $this->flight_active_booking_sources($active_booking_source_condition);
            //Authenticate the API's
            $this->api_authentication($flight_active_booking_sources);
            
            $flight_obj_ref = load_flight_lib($booking_source);
            $fare_rule_data = $this->CI->$flight_obj_ref->get_fare_rules($fare_rule_request);
            $fare_rule_result = $fare_rule_data;
        } else {
            $fare_rule_result['message'] = 'Invalid Fare Rule Request';
        }
        return $fare_rule_result;
    }
    /**
     * Returns Updated Fare
     * @param unknown_type $request
     */
    public function update_fare_quote($request, $search_id, $cache_key)
    {
        $update_fare_quote = array();
        $update_fare_quote['data'] = array();
        $update_fare_quote['status'] = FAILURE_STATUS;
        $update_fare_quote['message'] = '';
         
        $ResultToken = trim($request['ResultToken']);
        $flight_search_data = Common_Flight::read_record($ResultToken);
       
        if(valid_array($flight_search_data) == true){
            $flight_search_data = json_decode($flight_search_data[0], true);
            $update_fare_quote_request = array_values(unserialized_data($flight_search_data['ResultToken']));
 
            $update_fare_quote_request = $update_fare_quote_request[0];
            $update_fare_quote_request['flight_search_data'] = $flight_search_data;
            $booking_source = $update_fare_quote_request['booking_source'];
            $active_booking_source_condition = array();
            $active_booking_source_condition[] = array('BS.source_id', '=', '"'.$booking_source.'"');
            $flight_active_booking_sources = $this->flight_active_booking_sources($active_booking_source_condition);
            //Authenticate the API's
           
            $this->api_authentication($flight_active_booking_sources);
            $flight_obj_ref = load_flight_lib($booking_source,'',true,'');
            if($booking_source != SABARE_FLIGHT_BOOKING_SOURCE){
                $update_fare_quote_data = $this->CI->$flight_obj_ref->get_update_fare_quote($update_fare_quote_request, $search_id);
                 // debug($update_fare_quote_data);exit;
            }
            else{
                $update_fare_quote_data = $this->CI->$flight_obj_ref->get_update_fare_quote_sabare($flight_search_data, $search_id);
               
            }
            if($update_fare_quote_data['status'] == SUCCESS_STATUS){
                $carry_cache_key = $cache_key;
                $this->CI->load->library ( 'flight/common_flight');

                $update_fare_quote_data = $this->CI->common_flight->update_markup_and_insert_cache_key_to_token ( $update_fare_quote_data['data']['FareQuoteDetails']['JourneyList'], $carry_cache_key, $search_id );
                if($_SERVER['REMOTE_ADDR'] =='192.168.0.88'){
                    $fare_quote_result_token = $update_fare_quote_data[0][0]['ResultToken'];
                    $data['fare_quote_token'] = $fare_quote_result_token;
                    $data['search_token'] = $request['ResultToken'];
                    $check_fare = $this->CI->custom_db->single_table_records('TP_result_token','*',array('search_token'=>$request['ResultToken']));
                    $this->CI->custom_db->delete_record('TP_result_token',array('search_token' => $request['ResultToken']));
                    $this->CI->db->insert('TP_result_token', $data);
                   
                }
                $update_fare_quote['status'] = SUCCESS_STATUS;
                $update_fare_quote['data']['FareQuoteDetails']['JourneyList'] = $update_fare_quote_data[0][0];
            } else {
                $update_fare_quote['message'] = $update_fare_quote_data['message'];
            }
        } else {
            $update_fare_quote['message'] = 'Invalid updateFareQuote Request';
        }
        
        return $update_fare_quote;
    }
    /**
     * Returns Extra Services
     * @param unknown_type $request
     */
    public function get_extra_services($request, $search_id, $cache_key)
    {
        $extra_services = array();
        $extra_services['data'] = array();
        $extra_services['status'] = FAILURE_STATUS;
        $extra_services['message'] = '';
        $ResultToken = trim($request['ResultToken']);

        //if($_SERVER['REMOTE_ADDR'] =='192.168.0.88'){
           $check_fare = $this->CI->custom_db->single_table_records('TP_result_token','*',array('search_token'=>$request['ResultToken']));
           $fare_quote_data = Common_Flight::read_record($check_fare['data'][0]['fare_quote_token']);
           
       // }
        $flight_search_data = Common_Flight::read_record($ResultToken);

        if(valid_array($flight_search_data) == true){
            $flight_search_data = json_decode($flight_search_data[0], true);

            $extra_services_request = array_values(unserialized_data($flight_search_data['ResultToken']));
            $extra_services_request = $extra_services_request[0];
            $booking_source = $extra_services_request['booking_source'];
            
            $active_booking_source_condition = array();
            $active_booking_source_condition[] = array('BS.source_id', '=', '"'.$booking_source.'"');
            $flight_active_booking_sources = $this->flight_active_booking_sources($active_booking_source_condition);
            //Authenticate the API's
            $this->api_authentication($flight_active_booking_sources);
            
            $flight_obj_ref = load_flight_lib($booking_source,'',true,'');
            
            if($booking_source != SABARE_FLIGHT_BOOKING_SOURCE){
                
                if((!@$extra_services_request['ResultToken'])){
                    $extra_services_request['ResultToken'] = $request['ResultToken'];
                }
                if($booking_source == SPICEJET_FLIGHT_BOOKING_SOURCE){
                    $extra_services_request['FlightDetails'] = $flight_search_data;
                }
                $extra_services_data = $this->CI->$flight_obj_ref->get_extra_services($extra_services_request, $fare_quote_data, $search_id);
            }
            else{
                $extra_services_data = $this->CI->$flight_obj_ref->get_extra_services($flight_search_data, $search_id);
            }
            if($extra_services_data['status'] == SUCCESS_STATUS){
                $carry_cache_key = $cache_key;
                $this->CI->load->library ( 'flight/common_flight');
                
                $extra_services_data = $this->CI->common_flight->cache_extra_services ( $extra_services_data['data']['ExtraServiceDetails'],$carry_cache_key);
                
                $extra_services['status'] = SUCCESS_STATUS;
                $extra_services['data']['ExtraServiceDetails'] = $extra_services_data;
            } else {
                $extra_services['message'] = $extra_services_data['message'];
            }
        } else {
            $extra_services['message'] = 'Invalid ExtraServices Request';
        }
        return $extra_services;
    }
    /**
     * Process the booking
     * @param unknown_type $request
     */
    public function process_booking($request, $search_id, $cache_key)
    {
        $booking_response = array();
        $booking_response['data'] = array();
        $booking_response['status'] = FAILURE_STATUS;
        $booking_response['message'] = '';
        
        //1.Validating Booking Parameteres
        $validate_booking_params = $this->validate_booking_params($request);
        if($validate_booking_params['status'] == FAILURE_STATUS){
            $booking_response['message'] = $validate_booking_params['message'];
            return $booking_response;
        }
        $ResultToken = trim($request['ResultToken']);
        $flight_data = Common_Flight::read_record($ResultToken);
        // debug($flight_data);exit;
        if(valid_array($flight_data) == true){
            $this->CI->load->library ( 'flight/common_flight');
            $flight_data = json_decode($flight_data[0], true);
            if(isset($flight_data['flight_search_data'])){
                $flight_data['FlightDetails'] = $flight_data['flight_search_data']['FlightDetails'];
                $flight_data['ResultToken'] = $flight_data['flight_search_data']['ResultToken'];
                $flight_data['Attr'] = $flight_data['flight_search_data']['Attr'];
                $flight_data['Price'] = $flight_data['flight_search_data']['Price'];
                unset($flight_data['flight_search_data']);
            }
                       
             #Get Flight Opeartor Code
            $OperatorCode='';
            if(isset($flight_data['FlightDetails']['Details'][0][0]['OperatorCode']))
            {
                $OperatorCode=$flight_data['FlightDetails']['Details'][0][0]['OperatorCode'];
            }
                        
            $ResultToken = array_values(unserialized_data($flight_data['ResultToken']));
            
            $ResultToken = $ResultToken[0];

            $booking_source = $ResultToken['booking_source'];
           // echo $booking_source;exit;
            //Updating the Fare Details with Markup & commission
            $flight_price_details = $this->CI->common_flight->final_booking_transaction_fare_details($flight_data['Price'], $search_id, $booking_source, $OperatorCode);

            $flight_data['Price'] = $flight_price_details['Price'];
            $flight_data['PriceBreakup'] = $flight_price_details['PriceBreakup'];
            
            //2.Check Domain Balance For the Booking
            $booking_transaction_amount = $flight_data['Price']['client_buying_price'];
            //if($this->CI->domain_management->verify_domain_balance($booking_transaction_amount, Flight::get_credential_type()) == SUCCESS_STATUS) {//Verify Domain Balance
                
                $passenger_details = $this->check_booking_passenger_params($request['Passengers']);
                $app_reference = $request['AppReference'];
                $sequence_number = $request['SequenceNumber'];
                //3.Save Flight Details

                $save_flight_booking = $this->CI->common_flight->save_flight_booking($flight_data, $passenger_details, $app_reference, $sequence_number, $booking_source, $search_id);
               
                if($save_flight_booking['status'] == SUCCESS_STATUS){
                    $book_req_params = array();
                    $book_req_params['ResultToken']=$ResultToken;
                    $book_req_params['Passengers']=$passenger_details;
                    $book_req_params['flight_data'] = $flight_data;
                    $active_booking_source_condition = array();
                    $active_booking_source_condition[] = array('BS.source_id', '=', '"'.$booking_source.'"');
                    $flight_active_booking_sources = $this->flight_active_booking_sources($active_booking_source_condition);
                            
                    //Authenticate the API's
                    $this->api_authentication($flight_active_booking_sources);
                    
                    $flight_obj_ref = load_flight_lib($booking_source,'',true,'');
                    $process_booking_response = $this->CI->$flight_obj_ref->process_booking($book_req_params, $app_reference, $sequence_number, $search_id);

                    if($process_booking_response['status'] == SUCCESS_STATUS){
                        //Deduct Flight Booking Amount
                        //echo 'Flight Blender : 509';
                        $this->CI->common_flight->deduct_flight_booking_amount($app_reference, $sequence_number);
                        //echo 'Flight Blender : 512';
                        //Get Flight Booking Details
                        $flight_booking_details = $this->CI->common_flight->get_flight_booking_transaction_details($app_reference, $sequence_number);
                        /*echo 'Flight Blender : 514';
                        debug($flight_booking_details);
                        exit;*/
                        $booking_response = $flight_booking_details;
                    } else {
                        $booking_response['message'] = $process_booking_response['message'];
                    }
                    
                    //Notification
                    $this->booking_not_confirmed_notification($app_reference, $sequence_number);                    
                    
                } else {//End of Save Booking Condition
                    $booking_response['message'] = $save_flight_booking['message'];
                }
            /*} else {//End of Verify Balance Condition
                  $passenger_details = $this->check_booking_passenger_params($request['Passengers']);
                $app_reference = $request['AppReference'];
                $sequence_number = $request['SequenceNumber'];
                $booking_response['message'] = 'Agency do not have enough balance.';
                $exception_log_message = '';
                $this->CI->exception_logger->log_exception($app_reference, $this->booking_source_name . '- (<strong>Agency do not have enough balance.</strong>)', $exception_log_message, $booking_response);
                $save_flight_booking = $this->CI->common_flight->save_flight_booking($flight_data, $passenger_details, $app_reference, $sequence_number, $booking_source, $search_id);
                
            }*/
        } else {
            $booking_response['message'] = 'Invalid CommitBooking Request';
        }
        //debug($booking_response);exit;
        return $booking_response;
    }
    /**
     * Hold Ticket
     * @param unknown_type $request
     */
    public function hold_ticket($request, $search_id, $cache_key)
    {
        $booking_response = array();
        $booking_response['data'] = array();
        $booking_response['status'] = FAILURE_STATUS;
        $booking_response['message'] = '';
        
        //1.Validating Booking Parameteres
        $validate_booking_params = $this->validate_booking_params($request);
        if($validate_booking_params['status'] == FAILURE_STATUS){
            $booking_response['message'] = $validate_booking_params['message'];
            return $booking_response;
        }
        $ResultToken = trim($request['ResultToken']);
        $flight_data = Common_Flight::read_record($ResultToken);
        if(valid_array($flight_data) == true){
            $this->CI->load->library ( 'flight/common_flight');
            $flight_data = json_decode($flight_data[0], true);
                        
                        #Get Flight Opeartor Code
                        $OperatorCode='';
                        if(isset($flight_data['FlightDetails']['Details'][0][0]['OperatorCode']))
                        {
                            $OperatorCode=$flight_data['FlightDetails']['Details'][0][0]['OperatorCode'];
                        }
                        
            $ResultToken = array_values(unserialized_data($flight_data['ResultToken']));
            $ResultToken = $ResultToken[0];
            $booking_source = $ResultToken['booking_source'];
            //Updating the Fare Details with Markup & commission
            $flight_price_details = $this->CI->common_flight->final_booking_transaction_fare_details($flight_data['Price'], $search_id, $booking_source ,$OperatorCode);
            $flight_data['Price'] = $flight_price_details['Price'];
            $flight_data['PriceBreakup'] = $flight_price_details['PriceBreakup'];
            
            //2.Check Domain Balance For the Booking
            $booking_transaction_amount = $flight_data['Price']['client_buying_price'];
            //if($this->CI->domain_management->verify_domain_balance($booking_transaction_amount, Flight::get_credential_type()) == SUCCESS_STATUS) {//Verify Domain Balance
                
                $passenger_details = $this->check_booking_passenger_params($request['Passengers']);
                $app_reference = $request['AppReference'];
                $sequence_number = $request['SequenceNumber'];
                //3.Save Flight Details
                $save_flight_booking = $this->CI->common_flight->save_flight_booking($flight_data, $passenger_details, $app_reference, $sequence_number, $booking_source, $search_id);
                
                if($save_flight_booking['status'] == SUCCESS_STATUS){
                    $book_req_params = array();
                    $book_req_params['ResultToken']=$ResultToken;
                    $book_req_params['Passengers']=$passenger_details;
                    $book_req_params['flight_data'] = $flight_data;
                    $active_booking_source_condition = array();
                    $active_booking_source_condition[] = array('BS.source_id', '=', '"'.$booking_source.'"');
                    $flight_active_booking_sources = $this->flight_active_booking_sources($active_booking_source_condition);
                    
                    //Authenticate the API's
                    $this->api_authentication($flight_active_booking_sources);
                    
                    $flight_obj_ref = load_flight_lib($booking_source);
                    $process_booking_response = $this->CI->$flight_obj_ref->hold_ticket($book_req_params, $app_reference, $sequence_number, $search_id);
                    if($process_booking_response['status'] == SUCCESS_STATUS){
                        //Get Flight Booking Details
                        $flight_booking_details = $this->CI->common_flight->get_flight_booking_transaction_details($app_reference, $sequence_number);
                        $booking_response = $flight_booking_details;
                    } else {
                        $booking_response['message'] = $process_booking_response['message'];
                    }
                } else {//End of Save Booking Condition
                    $booking_response['message'] = $save_flight_booking['message'];
                }
            //} else {//End of Verify Balance Condition
            //  $booking_response['message'] = 'In Sufficiant Balance';
            //}
        } else {
            $booking_response['message'] = 'Invalid HoldTicket Request';
        }
        return $booking_response;
    }
    /****
    ** Issue Hold Ticket 
    ** Jeeva
    ****/
    function issue_hold_ticket($request)
    {
        $app_reference = $request['AppReference'];
        $sequence_number = $request['SequenceNumber'];
        $ticket_id = '';
        $booking_id = $request['BookingId'];
        $pnr = $request['Pnr'];
        $response ['data'] = array ();
        $response ['status'] = FAILURE_STATUS;
        $response ['message'] = ''; 
        $booking_details = $this->CI->custom_db->single_table_records('flight_booking_transaction_details','*',array('app_reference'=>$app_reference,'pnr'=>$pnr,'sequence_number'=>$sequence_number,'status'=>'BOOKING_HOLD'));

        if(valid_array($booking_details) && $booking_details['status'] == SUCCESS_STATUS && $booking_details['data'][0]['hold_ticket_req_status'] == INACTIVE)
        {
            $get_booking_details  = $this->CI->flight_model->get_booking_details($app_reference);
            $master_booking_details  = $get_booking_details['data']['booking_details'][0];
            $passenger_details = $get_booking_details['data']['booking_customer_details'][0];
            $booking_transaction_details = $get_booking_details['data']['booking_transaction_details'][0];
            
            $fare_details = $booking_details['data'][0];
            $amount = $this->CI->domain_management->agent_buying_price($fare_details);
            $total_amount = $amount[0];
            $domain_booking_attr['app_reference'] =$app_reference;
            $domain_booking_attr['transaction_type'] = "Flight";
            //Deduct Domain Balance
                        
                        if ($this->CI->domain_management->verify_domain_balance($total_amount, Flight::get_credential_type()) == SUCCESS_STATUS) {//Verify Domain Balance
                        
                        #It can enable when need to deduct money for Hold to Confirm by customer
                        # Disabled instructed by Yuvaraj
            $deduct_domain_balance = $this->CI->domain_management->debit_domain_balance($total_amount, Flight::get_credential_type(), get_domain_auth_id(), $domain_booking_attr);//deduct the domain balance
            
            //Log the Transaction
            $agent_transaction_amount = ($total_amount-$fare_details['domain_markup']);
            $domain_markup =            $fare_details['domain_markup'];
            $level_one_markup =         0;
            $currency =                 $master_booking_details['currency'];
            $currency_conversion_rate = $master_booking_details['currency_conversion_rate'];
            $remarks =                  'flight Transaction was Successfully done';
            $this->CI->domain_management_model->save_transaction_details ( 'flight', $app_reference, $agent_transaction_amount, $domain_markup, $level_one_markup, $remarks, $currency, $currency_conversion_rate);
            
                        
                        
            //Update Issue Hold Ticket Status In Booking Transaction Details
            $update_issue_ticket_req_status = $this->CI->custom_db->update_record('flight_booking_transaction_details',array('hold_ticket_req_status'=>ACTIVE),array('app_reference'=>$app_reference,'pnr' => $pnr));
            
            $get_domain_name = $this->CI->custom_db->single_table_records('domain_list','domain_name',array('origin'=>get_domain_auth_id()));
            $domain_name = $get_domain_name['data'][0]['domain_name'];          
            $voucher_data = array();
            $voucher_data['AppReference'] = $app_reference;
            $voucher_data['PNR'] = $pnr;
            $voucher_data['BookingID'] = $booking_id;
            $voucher_data['status'] = $booking_details['status'];
            $voucher_data['travel_date'] = date("d M Y",strtotime($master_booking_details['journey_start'])).", ".date("H:i",strtotime($master_booking_details['journey_start']));
            $voucher_data['leade_pax_name'] = $passenger_details;
            $voucher_data['domain_name'] = $domain_name;
            $voucher_data['booking_api_name'] = $booking_transaction_details['booking_api_name'];
            
            //Send SMS to tmx support team
            $sms_template = $this->CI->load->view('voucher/ticket_hold_sms', $voucher_data,true);
            // send_alert_sms($sms_template);
            
            //Send mail to tmx support team
            $mail_template = $this->CI->load->view('voucher/ticket_hold', $voucher_data,true);
            $Email = $this->CI->config->item('alert_email_id');
            $this->CI->load->library('provab_mailer');
            $this->CI->provab_mailer->send_mail($Email, $domain_name.' - Confirm Hold Ticket',$mail_template);
            
            $response['status'] = SUCCESS_STATUS;
            $response ['message'] = 'Request Received, Will Update the Ticket Details Shortly'; 
                        }  
                       else {//End of Verify Balance Condition
                         $response ['status'] = FAILURE_STATUS;
                         $response ['message'] = 'In Sufficiant Balance to Confirm this HOLD Booking';
                     }  
            
        }else{
            $response ['status'] = FAILURE_STATUS;
        }       
        return $response;
    }
    /**
     * Process cancel Booking Request
     * @param unknown_type $request
     */
    public function cancel_booking($request)
    {
        $cancel_booking_response = array();
        $cancel_booking_response['data'] = array();
        $cancel_booking_response['status'] = FAILURE_STATUS;
        $cancel_booking_response['message'] = '';
        
        if(valid_array($request) == true){
            $app_reference = $request['AppReference'];
            $sequence_number = $request['SequenceNumber'];
            $flight_booking_details = $this->CI->flight_model->get_flight_booking_transaction_details($app_reference, $sequence_number);
            
            if($flight_booking_details['status'] == SUCCESS_STATUS){
                $booking_transaction_details = $flight_booking_details['data']['booking_transaction_details'][0];
                $booking_source = $booking_transaction_details['booking_source'];
                
                $active_booking_source_condition = array();
                $active_booking_source_condition[] = array('BS.source_id', '=', '"'.$booking_source.'"');
                $flight_active_booking_sources = $this->flight_active_booking_sources($active_booking_source_condition);
                //Authenticate the API's
                $this->api_authentication($flight_active_booking_sources);
                
                $flight_obj_ref = load_flight_lib($booking_source);
                $cancel_booking_details = $this->CI->$flight_obj_ref->cancel_booking($request);
                if($cancel_booking_details['status'] == SUCCESS_STATUS){
                    //Sending Notification
                    $app_reference = $request['AppReference'];
                    $sequence_number = $request['SequenceNumber'];
                    $IsFullBookingCancel = $request['IsFullBookingCancel'];
                    $ticket_ids = $request['TicketId'];
                    
                    $booking_details = $this->CI->flight_model->get_flight_booking_transaction_details($app_reference, $sequence_number);
                    $booking_details = $booking_details['data'];
                    $master_booking_details  =$booking_details['booking_details'][0];
                    $domain_name = $master_booking_details['domain_name'];
                    $booking_transaction_details  =$booking_details['booking_transaction_details'][0];
                    $flight_booking_transaction_details_origin =$booking_transaction_details['origin'];
                    $booking_customer_details  =$booking_details['booking_customer_details'];
                    
                    $passenger_ticket_details = $this->CI->common_flight->get_cancellation_reequested_pax_details($booking_customer_details, $ticket_ids);
                    
                    $ticket_cancel_request = array();
                    $ticket_cancel_request['domain_name'] = $domain_name;
                    $ticket_cancel_request['booking_transaction_details'] = $booking_transaction_details;
                    $ticket_cancel_request['passenger_ticket_details'] = $passenger_ticket_details;
                    
                    //Send SMS to tmx support team
                    $sms_template = $this->CI->load->view('flight/ticket_cancel_request_sms_template', $ticket_cancel_request,true);
                    // send_alert_sms($sms_template);
                    
                    //Send mail to tmx support team
                    $mail_template = $this->CI->load->view('flight/ticket_cancel_request_template', $ticket_cancel_request,true);
                    
                    $email = $this->CI->config->item('alert_email_id');
                    $subject = ucfirst($domain_name).' - Flight Ticket Cancellation Request';
                    $this->CI->load->library('provab_mailer');
                    
                    $this->CI->provab_mailer->send_mail($email, $subject,$mail_template);
                }
                $cancel_booking_response = $cancel_booking_details;
            } else {
                $cancel_booking_response['message'] = 'Invalid AppReference';
            }
        } else {
            $cancel_booking_response['message'] = 'Invalid CancelBooking Request';
        }
        return $cancel_booking_response;
    }
    /**
     * Validate Booking Params
     * @param unknown_type $request
     */
    private function validate_booking_params($request)
    {
        $success_status = true;
        $message = '';
        $data = array();
        //AppReference
        if($success_status == true){
            if(isset($request['AppReference']) == true){
                $AppReference = trim($request['AppReference']);
                if(empty($AppReference) == true || strlen($AppReference) > 40 || strlen($AppReference) < 10){
                    $success_status = false;
                    $message = 'AppReference should be between 10 to 40 in length';
                }
            } else {
                $success_status = false;
                $message = 'AppReference should be between 10 to 40 in length';
            }
        }
        //Sequence Number
        if($success_status == true){
            if(isset($request['SequenceNumber']) == true){
                $SequenceNumber = trim($request['SequenceNumber']);
                if(is_numeric($SequenceNumber) == false){
                    $success_status = false;
                    $message = 'SequenceNumber is Required';
                }
            } else {
                $success_status = false;
                $message = 'SequenceNumber is Required';
            }
        }
        //Passenger Data Validation
        if($success_status == true){
            if(isset($request['Passengers']) == true && valid_array($request['Passengers']) == true){
                //TODO: validate each and every attribute in Passengers array
            } else {
                $success_status = false;
                $message = 'Passengers information is Required';
            }
        }
        if($success_status == true){
            $success_status = SUCCESS_STATUS;
        } else {
            $success_status = FAILURE_STATUS;
        }
        $data['status'] = $success_status;
        $data['message'] = $message;
        return $data;
    }
    /**
     * 
     * Enter description here ...
     */
    function check_booking_passenger_params($passenger_details)
    {
        foreach ($passenger_details as $pk => $pv){
            $passenger_details[$pk] = $pv;
            //Passport Details
            if(isset($pv['PassportNumber']) == true && empty($pv['PassportNumber']) == false) {
                $PassportNumber = $pv['PassportNumber'];
            } else {
                $PassportNumber = rand(1111111111,9999999999);
            }
            if(isset($pv['PassportExpiry']) == true && empty($pv['PassportExpiry']) == false) {
                $PassportExpiry = $pv['PassportExpiry'];
            } else {
                $PassportExpiry = date('Y-m-d', strtotime('+5 years'));
            }
            $passenger_details[$pk]['PassportNumber'] = preg_replace('/\s+/', '', $PassportNumber);
            $passenger_details[$pk]['PassportExpiry'] = $PassportExpiry;
            $passenger_details [$pk] ['ContactNo'] = $this->validate_mobile_number($pv['ContactNo']);
        }
        return $passenger_details;
    }
    /**
     * Validates the mobile number
     * @param unknown_type $mobile_number
     */
    private function validate_mobile_number($mobile_number)
    {
        $mobile_number = trim($mobile_number);
        $mobile_number = ltrim($mobile_number, '0');
        if(strlen($mobile_number) < 10){
            $mobile_number_length = strlen($mobile_number);
            $required_extra_number_lengths = (10)-$mobile_number_length;
            $extra_numbers = str_repeat('0', $required_extra_number_lengths);
            $mobile_number = $extra_numbers.''.$mobile_number;
        }
        return $mobile_number;
    
    }
    /**
     * returns calendar fare
     * 
     */
    public function calendar_fare($request)
    {
        $curl_params = array();
        $curl_request = array();
        $curl_url = array();
        $curl_header = array();
        $curl_booking_source = array();
        $curl_remarks = array();
        
        $calendar_fare_result = array();
        $formatted_calendar_fare_result = array();
        $formatted_calendar_fare_result['data'] = array();
        $formatted_calendar_fare_result['status'] = FAILURE_STATUS;
        
        $active_booking_source_condition = array();
        $active_booking_source_condition[] = array('BS.source_id', '=', '"'.TBO_FLIGHT_BOOKING_SOURCE.'"');
        $flight_active_booking_sources = $this->flight_active_booking_sources($active_booking_source_condition);
        
        //Authenticate the API's 
        $this->api_authentication($flight_active_booking_sources);
        $flight_obj = array();
        foreach ($flight_active_booking_sources as $bs_k => $bs_v){
            $flight_obj_ref = load_flight_lib($bs_v['booking_source'], '', true);
            $flight_obj[$bs_v['booking_source']] = $flight_obj_ref;
            $search_request = $this->CI->$flight_obj_ref->get_calendar_fare_request($request);
            if($search_request['status'] == SUCCESS_STATUS){
                $search_request['data']['remarks'] = $this->CI->$flight_obj_ref->booking_source_name;
                $this->assign_curl_params($search_request['data'],$curl_request, $curl_url, $curl_header, $curl_booking_source, $curl_remarks);
            }
        }
        $curl_params['booking_source'] = $curl_booking_source;
        $curl_params['request'] = $curl_request;
        $curl_params['url'] = $curl_url;
        $curl_params['header'] = $curl_header;
        $curl_params['remarks'] = $curl_remarks;
        //Mutl-Curl API Call
        $calendar_fare_result = $this->CI->multi_curl->execute_multi_curl($curl_params);
        //Format the Flight List
        foreach ($flight_obj as $fo_k => $fo_v){
            $calendar_fare_data = $this->CI->$fo_v->get_calendar_fare($calendar_fare_result[$fo_k]);
            if($calendar_fare_data['status'] == SUCCESS_STATUS && valid_array($calendar_fare_data['data']['CalendarFareDetails']) == true){
                //Merge Flight List
                $formatted_calendar_fare_result = $calendar_fare_data;
            }
        }
        if($formatted_calendar_fare_result['status'] == SUCCESS_STATUS && valid_array($formatted_calendar_fare_result['data']) == true){
            $this->CI->load->library ( 'flight/common_flight');
            $formatted_calendar_fare_result['data']['CalendarFareDetails'] = $this->CI->common_flight->update_calendarfare_currency($formatted_calendar_fare_result['data']['CalendarFareDetails']);
        } else {
            $formatted_calendar_fare_result['message'] = 'No Data Found';
        }
        return $formatted_calendar_fare_result;
    }
    /**
     * returns calendar fare
     * 
     */
    public function update_calendar_fare($request)
    {
        $curl_params = array();
        $curl_request = array();
        $curl_url = array();
        $curl_header = array();
        $curl_booking_source = array();
        $curl_remarks = array();
        
        $calendar_fare_result = array();
        $formatted_calendar_fare_result = array();
        $formatted_calendar_fare_result['data'] = array();
        $formatted_calendar_fare_result['status'] = FAILURE_STATUS;
        
        $active_booking_source_condition = array();
        $active_booking_source_condition[] = array('BS.source_id', '=', '"'.TBO_FLIGHT_BOOKING_SOURCE.'"');
        $flight_active_booking_sources = $this->flight_active_booking_sources($active_booking_source_condition);
        
        //Authenticate the API's
        $this->api_authentication($flight_active_booking_sources);
        $flight_obj = array();
        foreach ($flight_active_booking_sources as $bs_k => $bs_v){
            $flight_obj_ref = load_flight_lib($bs_v['booking_source'], '', true);
            $flight_obj[$bs_v['booking_source']] = $flight_obj_ref;
            $search_request = $this->CI->$flight_obj_ref->get_update_calendar_fare_request($request);
            if($search_request['status'] == SUCCESS_STATUS){
                $search_request['data']['remarks'] = $this->CI->$flight_obj_ref->booking_source_name;
                $this->assign_curl_params($search_request['data'],$curl_request, $curl_url, $curl_header, $curl_booking_source, $curl_remarks);
            }
        }
        $curl_params['booking_source'] = $curl_booking_source;
        $curl_params['request'] = $curl_request;
        $curl_params['url'] = $curl_url;
        $curl_params['header'] = $curl_header;
        $curl_params['remarks'] = $curl_remarks;
        //Mutl-Curl API Call
        $calendar_fare_result = $this->CI->multi_curl->execute_multi_curl($curl_params);
        //Format the Flight List
        foreach ($flight_obj as $fo_k => $fo_v){
            $calendar_fare_data = $this->CI->$fo_v->get_calendar_fare($calendar_fare_result[$fo_k]);
            if($calendar_fare_data['status'] == SUCCESS_STATUS && valid_array($calendar_fare_data['data']['CalendarFareDetails']) == true){
                //Merge Flight List
                $formatted_calendar_fare_result = $calendar_fare_data;
            }
        }
        if($formatted_calendar_fare_result['status'] == SUCCESS_STATUS && valid_array($formatted_calendar_fare_result['data']) == true){
            $this->CI->load->library ( 'flight/common_flight');
            $formatted_calendar_fare_result['data']['CalendarFareDetails'] = $this->CI->common_flight->update_calendarfare_currency($formatted_calendar_fare_result['data']['CalendarFareDetails']);
        } else {
            $formatted_calendar_fare_result['message'] = 'No Data Found';
        }
        return $formatted_calendar_fare_result;
    }
    /**
     * 
     * Merges the Flight Data
     * @param array $flight_data
     * @param array $formatted_seach_result
     */
    private function merge_flight_list($flight_data, & $formatted_seach_result)
    {
        foreach ($flight_data as $fd_k => $fd_v) {
            $formatted_seach_result['data']['FlightDataList']['JourneyList'][$fd_k] = array_merge($formatted_seach_result['data']['FlightDataList']['JourneyList'][$fd_k], $fd_v);
        }
    }
    /*
     * Eliminates the Duplicate Flights
     */
    private function eliminate_duplicate_flights($JourneyList)
    {

        $new_journey_list = array();
        foreach ($JourneyList as $jl_k => $jl_v){
            $flight_data = array();

            foreach ($jl_v as $row_k => $row_v){
                //One Row Data

                $FlightDetails = $row_v['FlightDetails']['Details'];//2 loops
                // $TotalNetFare = floatval($row_v['Price']['TotalDisplayFare'] - $row_v['Price']['PriceBreakup']['AgentCommission']+$row_v['Price']['PriceBreakup']['AgentTdsOnCommision']);
                $TotalNetFare = floatval($row_v['Price']['TotalDisplayFare']);
                $array_name = '';
                foreach ($FlightDetails as $fd_k => $fd_v){
                    foreach ($fd_v as $flight_k => $flight_v){
                        $array_name .= $flight_v['FlightNumber'];
                        $array_name .= $flight_v['Origin']['AirportCode'];
                        $array_name .= $flight_v['Destination']['AirportCode'];
                        $array_name .= $flight_v['Origin']['FDTV'];
                        $array_name .= $flight_v['Destination']['FATV'];
                        // $array_name .= $flight_v['CabinClass'];
                    }
                }
               
                if(isset($flight_data[$array_name]) == true && valid_array($flight_data[$array_name]) == true){
                    // $Old_TotalNetFare = floatval($flight_data[$array_name]['Price']['TotalDisplayFare'] - $flight_data[$array_name]['Price']['PriceBreakup']['AgentCommission']+$flight_data[$array_name]['Price']['PriceBreakup']['AgentTdsOnCommision']);
                    $Old_TotalNetFare = floatval($flight_data[$array_name]['Price']['TotalDisplayFare']);
                    if($TotalNetFare < $Old_TotalNetFare){//If fare is low, then assign the new flight
                        $flight_data[$array_name] = $row_v;
                    }
                } else {
                    $flight_data[$array_name] = $row_v;
                }
                 
            }
            $new_journey_list[$jl_k] = $flight_data;
        }
        $final_journey_list = array();

        foreach ($new_journey_list as $njl_k => $njl_v){
            //Removing the assignned array index
            $final_journey_list[$njl_k] = array_values($njl_v);
        }
        // debug($final_journey_list); exit;
        return $final_journey_list;
    }
    /*
     * Sort the flights based on price
     */
    private function sort_flight_list($JourneyList)
    {
        $sorted_journey_list = array();
        foreach ($JourneyList as $jl_k => $jl_v){
            $sort_item = array ();
            foreach ( $jl_v as $row_k => $row_v ) {
                // $sort_item [$row_k] = floatval ( $row_v ['Price'] ['TotalDisplayFare'] );
                $sort_item [$row_k] = floatval ( $row_v ['Price'] ['TotalDisplayFare']- $row_v ['Price'] ['PriceBreakup']['AgentCommission'] + $row_v ['Price'] ['PriceBreakup']['AgentTdsOnCommision']);
            }
            array_multisort ( $sort_item, SORT_ASC, $jl_v );
            $sorted_journey_list[$jl_k] = $jl_v;
        }
        return $sorted_journey_list;
    }
    /**
     * Get Booking Details
     * @param unknown_type $request
     */
    public function booking_details($request)
    {
        $booking_details = array();
        $booking_details['data'] = array();
        $booking_details['status'] = FAILURE_STATUS;
        $booking_details['message'] = '';
        
        if(valid_array($request) == true && isset($request['AppReference']) == true && empty($request['AppReference']) == false){
            $app_reference = trim($request['AppReference']);
            
            $booking_data = $this->CI->flight_model->get_booking_details($app_reference);
            if($booking_data['status'] == SUCCESS_STATUS){
                $this->CI->load->library('booking_data_formatter');
                $formatted_data = $this->CI->booking_data_formatter->format_flight_booking_data($booking_data,'admin');
                
                //Formating Booking Transaction Details
                $transaction_details = array();
                foreach ($formatted_data['data']['booking_details'][0]['booking_transaction_details'] as $key => $value) {
                    $transaction_details[$key]['PNR'] = $value['pnr'];
                    $transaction_details[$key]['BookingID'] = $value['book_id'];
                    $transaction_details[$key]['SequenceNumber'] = $value['sequence_number'];
                    $transaction_details[$key]['Status'] = $value['status'];
                    foreach ($value['booking_customer_details'] as $k=>$b_d){
                        $cust_array['cust'][$k]['TicketId'] = $b_d['TicketId'];
                        $cust_array['cust'][$k]['Status'] = $b_d['status'];
                        $cust_array['cust'][$k]['TicketNumber'] = $b_d['TicketNumber'];
                    }
                    $transaction_details[$key]['BookingCustomer'] = $cust_array['cust'];
                }
                //Format Itinerary Details
                $booking_itinerary = array();
                foreach ($formatted_data['data']['booking_details'][0]['booking_itinerary_details'] as $key => $value) {
                    $booking_itinerary[$key]['AirlinePNR'] = $value['airline_pnr'];
                    $booking_itinerary[$key]['FromAirlineCode'] = $value['from_airport_code'];
                    $booking_itinerary[$key]['ToAirlineCode'] = $value['to_airport_code'];
                    $booking_itinerary[$key]['DepartureDatetime'] = $value['departure_datetime'];
                }
                //Assiging the data
                $data = array();
                $data['AppReference'] = $formatted_data['data']['booking_details'][0]['app_reference'];
                $data['MasterBookingStatus'] = $formatted_data['data']['booking_details'][0]['status'];
                $data['BoookingTransaction'] = $transaction_details;
                $data['BookingItineraryDetails'] = $booking_itinerary;
                
                $booking_details['status'] = SUCCESS_STATUS;
                $booking_details['data'] = $data;
            } else {
                $booking_details['message'] = 'Invalid AppReference ID';
            }
        }else {
            $booking_details['message'] = 'Invalid BookingDetails Request';
        }
        return $booking_details;
    }
    function message_queue()
    {
        $active_booking_source_condition = array();
        $active_booking_source_condition[] = array('BS.source_id', '=', '"'.MYSTIFLY_FLIGHT_BOOKING_SOURCE.'"');
        $flight_active_booking_sources = $this->flight_active_booking_sources($active_booking_source_condition);
        //Authenticate the API's
        $this->api_authentication($flight_active_booking_sources);
        $flight_obj_ref = load_flight_lib(MYSTIFLY_FLIGHT_BOOKING_SOURCE, '', true);
        
        return $this->CI->$flight_obj_ref->run_message_queues_service(array(''),'TEST432534254325',0);
    }
        /**
     * Send Notification if booking is not confirmed
     */
    private function booking_not_confirmed_notification($app_reference, $sequence_number)
    {
        //Send Notification, If Booking Failed
        $booking_details = $this->CI->flight_model->get_flight_booking_transaction_details($app_reference, $sequence_number);
        $booking_details = $booking_details['data'];
        $master_booking_details  =$booking_details['booking_details'][0];
        $booking_transaction_details  =$booking_details['booking_transaction_details'][0];
        
        if($booking_transaction_details['status'] != 'BOOKING_CONFIRMED'){
            $domain_name = $master_booking_details['domain_name'];
            $booking_failed_template = array();
            $booking_failed_template['domain_name'] = $domain_name;
            $booking_failed_template['booking_transaction_details'] = $booking_transaction_details;
            
            //Send SMS to tmx support team
            $sms_template = $this->CI->load->view('flight/booking_failed_sms_template', $booking_failed_template,true);
                if($booking_transaction_details['booking_api_name']=="Travelport-Flight")
                            {
                                // send_alert_sms_only_travelport($sms_template);
                            }
                            // send_alert_sms($sms_template);
            
            //Send Mail
            $mail_template = $this->CI->load->view('flight/booking_failed_mail_template', $booking_failed_template,true);
            $Email = $this->CI->config->item('alert_email_id');
            //Send mail
            $this->CI->load->library('provab_mailer');
            $this->CI->provab_mailer->send_mail($Email, $domain_name.' - Booking '.booking_status_label_text($booking_transaction_details['status']).' Status', $mail_template);
        }
    }
       public function search_data($search_id) {
        $response ['status'] = true;
        $response ['data'] = array();
            $clean_search_details = $this->CI->flight_model->get_safe_search_data($search_id);
            if ($clean_search_details ['status'] == true) {
                $response ['status'] = true;
                $response ['data'] = $clean_search_details ['data'];

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
                
            } else {
                $response ['status'] = false;
            }
        return $response;
    }
}
