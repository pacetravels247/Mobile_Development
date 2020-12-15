<?php

require_once BASEPATH . 'libraries/flight/Common_api_flight.php';

class TBO extends Common_Api_Flight {

    var $master_search_data;
    var $search_hash;
    protected $token;
    private $end_user_ip = '54.151.201.32';
    var $api_session_id;

    function __construct() {
        parent::__construct(META_AIRLINE_COURSE, TBO_FLIGHT_BOOKING_SOURCE);
        $this->CI = &get_instance();
        $this->CI->load->library('Converter');
        $this->set_api_session_id();
    }

    /**
     * Setting Session ID
     */
    public function set_api_session_id($authentication_response = '') {
        if (empty($this->api_session_id) == true) {
            if (empty($authentication_response) == false) {
                //store in database

                $authentication_response = json_decode($authentication_response, true);
                if (valid_array($authentication_response) == true && $authentication_response['Status'] == true && isset($authentication_response['TokenId']) == true && empty($authentication_response['TokenId']) == false) {
                    $session_id = trim($authentication_response['TokenId']);

                    $this->CI->api_model->update_api_session_id($this->booking_source, $session_id);
                }
            } else {

                $session_expiry_time = 10; //In minutes

                $session_id = $this->CI->api_model->get_api_session_id($this->booking_source, $session_expiry_time);

                if (empty($session_id) == true) {
                    $authentication_request = $this->get_authentication_request(true);
                    if ($authentication_request['status'] == SUCCESS_STATUS) {
                        $authentication_request = $authentication_request['data'];
                        $authentication_response = $this->process_request($authentication_request ['request'], $authentication_request ['url'], $authentication_request ['remarks']);
                        $this->set_api_session_id($authentication_response);
                    }
                }
            }
            if (empty($session_id) == false) {
                $this->api_session_id = $session_id;
            }
        }
    }

    /**
     * (non-PHPdoc)
     * @see Common_Api_Grind::search_data()
     */
    public function search_data($search_id) {
        $response ['status'] = true;
        $response ['data'] = array();
        $CI = & get_instance();
        if (empty($this->master_search_data) == true and valid_array($this->master_search_data) == false) {
            //$clean_search_details = $this->get_test_params('search');
            $clean_search_details = $CI->flight_model->get_safe_search_data($search_id);
            if ($clean_search_details ['status'] == true) {
                $response ['status'] = true;
                $response ['data'] = $clean_search_details ['data'];

                if ($clean_search_details ['data'] ['trip_type'] == 'multicity') {
                    $response ['data'] ['from'] = $clean_search_details ['data'] ['from'];
                    $response ['data'] ['to'] = $clean_search_details ['data'] ['to'];
                    $response ['data'] ['depature'] = $clean_search_details ['data'] ['depature'];
                    $response ['data'] ['return'] = $clean_search_details ['data'] ['depature'];
                } else {
                    $response ['data'] ['from'] = substr(chop(substr($clean_search_details ['data'] ['from'], - 5), ')'), - 3);
                    $response ['data'] ['to'] = substr(chop(substr($clean_search_details ['data'] ['to'], - 5), ')'), - 3);
                    $response ['data'] ['depature'] = date("Y-m-d", strtotime($clean_search_details ['data'] ['depature'])) . 'T00:00:00';
                    $response ['data'] ['return'] = date("Y-m-d", strtotime($clean_search_details ['data'] ['depature'])) . 'T00:00:00';
                }
                switch ($clean_search_details ['data'] ['trip_type']) {

                    case 'oneway' :
                        $response ['data'] ['type'] = 'oneway';
                        break;

                    case 'return' :
                        $response ['data'] ['type'] = 'return';
                        $response ['data'] ['return'] = date("Y-m-d", strtotime($clean_search_details ['data'] ['return'])) . 'T00:00:00';
                        break;
                    case 'multicity' :
                        $response ['data'] ['type'] = 'multicity';
                        break;
                    default :
                        $response ['data'] ['type'] = 'oneway';
                }

                if ($response ['data'] ['is_domestic'] == true and $response ['data'] ['trip_type'] == 'return') {
                    $response ['data'] ['domestic_round_trip'] = true;
                } else {
                    $response ['data'] ['domestic_round_trip'] = false;
                }
                $response ['data'] ['adult'] = $clean_search_details ['data'] ['adult_config'];
                $response ['data'] ['child'] = $clean_search_details ['data'] ['child_config'];
                $response ['data'] ['infant'] = $clean_search_details ['data'] ['infant_config'];
                $response ['data'] ['total_pax'] = $clean_search_details ['data'] ['total_pax'];
                $response ['data'] ['carrier'] = $clean_search_details ['data'] ['carrier'];
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
     * Authentcation RQ for api
     */
    private function authenticate_request() {
        $request = array();
        $AuthenticationRequest = array();
        $AuthenticationRequest['ClientId'] = $this->config['ClientId'];
        $AuthenticationRequest['UserName'] = $this->config['UserName'];
        $AuthenticationRequest['Password'] = $this->config['Password'];
        $AuthenticationRequest['EndUserIp'] = $this->end_user_ip;
        $request ['request'] = json_encode($AuthenticationRequest);
        $request ['url'] = $this->config['AuthenticationUrl'] . 'Authenticate';
        $request ['status'] = SUCCESS_STATUS;
        return $request;
    }

    /**
     * Formates Search Request
     */
    private function search_request($search_data) {
        $travels = array();
        $locs = array();
        $from = (!valid_array($search_data ['from'])) ? array(
            $search_data ['from']
                ) : $search_data ['from'];
        $to = (!valid_array($search_data ['to'])) ? array(
            $search_data ['to']
                ) : $search_data ['to'];
        $start = (!valid_array($search_data ['depature'])) ? array(
            $search_data ['depature']
                ) : $search_data ['depature'];
        if ($search_data ['trip_type'] == 'oneway') {
            $travels [] = array(
                'from' => $from [0],
                'to' => $to [0],
                'start' => $start [0]
            );
        } elseif ($search_data ['trip_type'] == 'return') {
            $travels [] = array(
                'from' => $from [0],
                'to' => $to [0],
                'start' => $start [0]
            );
            $travels [] = array(
                'from' => $to [0],
                'to' => $from [0],
                'start' => $search_data ['return']
            );
        } else {
            // multicity
            foreach ($from as $tk => $tv) {
                $travels [] = array(
                    'from' => $from [$tk],
                    'to' => $to [$tk],
                    'start' => $start [$tk]
                );
            }
        }
        $request = array();
        $search_request = array();
        $search_request ['EndUserIp'] = $this->end_user_ip;
        $search_request['TokenId'] = $this->api_session_id;
        $search_request['AdultCount'] = intval($search_data ['adult']);
        $search_request['ChildCount'] = intval($search_data ['child']);
        $search_request['InfantCount'] = intval($search_data ['infant']);
        $search_request['DirectFlight'] = false;
        $search_request['OneStopFlight'] = false;
        $search_request ['JourneyType'] = $this->get_journey_type($search_data['type']);
        //SEGMENT DATA
        foreach ($travels as $t_k => $t_v) {
            $segment_data[$t_k]['Origin'] = $t_v['from'];
            $segment_data[$t_k]['Destination'] = $t_v['to'];
            $segment_data[$t_k]['FlightCabinClass'] = $this->get_cabin_class_id($search_data['cabin_class']);
            $segment_data[$t_k]['PreferredDepartureTime'] = $t_v['start'];
        }
        if (isset($search_data['sources']) == true && empty($search_data['sources']) == false) {
            $search_request ['Sources'] = $search_data['sources'];
        } else {
            $search_request ['Sources'] = null;
        }
        if (valid_array($search_data['carrier']) == true && empty($search_data['carrier'][0]) == false) {
            //Overriding the Sources if PreferrefAIrlines are set
            //PreferredAirlnes will work only on GDS
            $search_request ['Sources'] = array('GDS');
            //$search_request ['Sources'] = array('LCC');
            $PreferredAirlines = $search_data ['carrier'];
        } else {
            $PreferredAirlines = null;
        }
        $search_request ['PreferredAirlines'] = $PreferredAirlines;
        $search_request ['Segments'] = $segment_data;
        $request ['request'] = json_encode($search_request);
        $request ['url'] = $this->config['EndPointUrl'] . 'Search';
        $request ['status'] = SUCCESS_STATUS;
        return $request;
    }

    /**
     * Forms the Calendar Fare Request
     * Enter description here ...
     * @param unknown_type $request
     */
    private function calendar_fare_request($cf_request) {
        $request = array();
        $calendar_fare_request = array();
        $calendar_fare_request['EndUserIp'] = $this->end_user_ip;
        $calendar_fare_request['TokenId'] = $this->api_session_id;
        $calendar_fare_request['JourneyType'] = $this->get_journey_type('oneway'); //Supports one way 1 only
        $calendar_fare_request['PreferredAirlines'] = $cf_request['PreferredAirlines'];

        $segment_details = $cf_request['Segments'];
        $segments ['Origin'] = $segment_details['Origin'];
        $segments ['Destination'] = $segment_details['Destination'];
        $segments ['FlightCabinClass'] = $this->get_cabin_class_id($segment_details['CabinClass']);
        $segments ['PreferredDepartureTime'] = date('Y-m-d', strtotime($segment_details['DepartureDate'])) . 'T00:00:00';

        $calendar_fare_request ['Segments'][] = $segments;
        $calendar_fare_request ['Sources'] = null; //Optinal

        $request ['request'] = json_encode($calendar_fare_request);
        $request ['url'] = $this->config['EndPointUrl'] . 'GetCalendarFare';
        $request ['status'] = SUCCESS_STATUS;
        return $request;
    }

    /**
     * Forms the Update Calendar Fare Request
     * Enter description here ...
     * @param unknown_type $request
     */
    private function update_calendar_fare_request($cf_request) {
        $request = array();
        $calendar_fare_request = array();
        $calendar_fare_request['EndUserIp'] = $this->end_user_ip;
        $calendar_fare_request['TokenId'] = $this->api_session_id;
        $calendar_fare_request['JourneyType'] = $this->get_journey_type('oneway'); //Supports one way 1 only
        $calendar_fare_request['PreferredAirlines'] = $cf_request['PreferredAirlines'];

        $segment_details = $cf_request['Segments'];
        $segments ['Origin'] = $segment_details['Origin'];
        $segments ['Destination'] = $segment_details['Destination'];
        $segments ['FlightCabinClass'] = $this->get_cabin_class_id($segment_details['CabinClass']);
        $segments ['PreferredDepartureTime'] = date('Y-m-d', strtotime($segment_details['DepartureDate'])) . 'T00:00:00';

        $calendar_fare_request ['Segments'][] = $segments;
        $calendar_fare_request ['Sources'] = null; //Optinal

        $request ['request'] = json_encode($calendar_fare_request);
        $request ['url'] = $this->config['EndPointUrl'] . 'UpdateCalendarFareOfDay';
        $request ['status'] = SUCCESS_STATUS;
        return $request;
    }

    /**
     * Authentication Request
     */
    public function get_authentication_request($internal_request = false) {
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        $active_booking_source = $this->is_active_booking_source();
        if ($active_booking_source['status'] == SUCCESS_STATUS) {
            $authenticate_request = $this->authenticate_request();
            if ($authenticate_request['status'] = SUCCESS_STATUS) {
                $response ['status'] = SUCCESS_STATUS;
                $curl_request = $this->form_curl_params($authenticate_request['request'], $authenticate_request['url']);
                $response ['data'] = $curl_request['data'];
            }
            if ($internal_request == true) {
                $response ['data']['remarks'] = 'Authentication(TBO)';
            }
        }
        return $response;
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
        if ($search_data ['status'] == SUCCESS_STATUS) {
            // Flight search RQ
            $search_request = $this->search_request($search_data ['data']);

            if ($search_request ['status'] = SUCCESS_STATUS) {
                $response ['status'] = SUCCESS_STATUS;
                $curl_request = $this->form_curl_params($search_request ['request'], $search_request ['url']);
                $response ['data'] = $curl_request['data'];
            }
        }
        return $response;
    }

    /**
     * Forms the fare rule request
     * @param unknown_type $request
     */
    private function fare_rule_request($params) {
        $request = array();
        $fare_rule_request = array();
        $fare_rule_request['EndUserIp'] = $this->end_user_ip;
        $fare_rule_request['TokenId'] = $this->api_session_id;
        $fare_rule_request ['TraceId'] = $params['TraceId'];
        $fare_rule_request ['ResultIndex'] = $params['ResultIndex'];

        $request ['request'] = json_encode($fare_rule_request);
        $request ['url'] = $this->config['EndPointUrl'] . 'FareRule';
        $request ['remarks'] = 'FareRule(TBO)';
        $request ['status'] = SUCCESS_STATUS;
        return $request;
    }

    /**
     * Forms the update fare quote request
     * @param unknown_type $request
     */
    private function update_fare_quote_request($params) {
        $request = array();
        $fare_quote_request = array();
        $fare_quote_request['EndUserIp'] = $this->end_user_ip;
        $fare_quote_request['TokenId'] = $this->api_session_id;
        $fare_quote_request ['TraceId'] = $params['TraceId'];
        $fare_quote_request ['ResultIndex'] = $params['ResultIndex'];

        $request ['request'] = json_encode($fare_quote_request);
        $request ['url'] = $this->config['EndPointUrl'] . 'FareQuote';
        $request ['remarks'] = 'FareQuote(TBO)';
        $request ['status'] = SUCCESS_STATUS;
        return $request;
    }

    /**
     * Forms the update fare quote request
     * @param unknown_type $request
     */
    private function ssr_request($params) {
        $request = array();
        $ssr_request = array();
        $ssr_request['EndUserIp'] = $this->end_user_ip;
        $ssr_request['TokenId'] = $this->api_session_id;
        $ssr_request ['TraceId'] = $params['TraceId'];
        $ssr_request ['ResultIndex'] = $params['ResultIndex'];
        $request ['request'] = json_encode($ssr_request);
        $request ['url'] = $this->config['EndPointUrl'] . 'SSR';
        $request ['remarks'] = 'SSR(TBO)';
        $request ['status'] = SUCCESS_STATUS;
        return $request;
    }

    /**
     * Forms the update fare quote request
     * @param unknown_type $request
     */
    private function run_lcc_ticket_service_request($params) {
        $request = array();
        $ticket_request = array();
        $ticket_request['EndUserIp'] = $this->end_user_ip;
        $ticket_request['TokenId'] = $this->api_session_id;
        $ticket_request ['TraceId'] = $params['ResultToken']['TraceId'];
        $ticket_request ['ResultIndex'] = $params['ResultToken']['ResultIndex'];
        $ticket_request['Passengers'] = $params['Passengers'];

        $request ['request'] = json_encode($ticket_request);
        $request ['url'] = $this->config['EndPointUrl'] . 'Ticket';
        $request ['remarks'] = 'LCC Ticket(TBO)';
        $request ['status'] = SUCCESS_STATUS;

        return $request;
    }

    /**
     * Forms the Book request
     * @param unknown_type $request
     */
    private function run_book_service_request($params) {
        $request = array();
        $book_request = array();
        $book_request['EndUserIp'] = $this->end_user_ip;
        $book_request['TokenId'] = $this->api_session_id;
        $book_request ['TraceId'] = $params['ResultToken']['TraceId'];
        $book_request ['ResultIndex'] = $params['ResultToken']['ResultIndex'];
        $book_request['Passengers'] = $params['Passengers'];
        $request ['request'] = json_encode($book_request);
        $request ['url'] = $this->config['EndPointUrl'] . 'Book';
        $request ['remarks'] = 'Book(TBO)';
        $request ['status'] = SUCCESS_STATUS;
        return $request;
    }

    /**
     * Forms the Non-LCC Ticket request
     * @param unknown_type $request
     */
    private function run_non_lcc_ticket_service_request($params) {
        $request = array();
        $ticket_request = array();
        $ticket_request['EndUserIp'] = $this->end_user_ip;
        $ticket_request['TokenId'] = $this->api_session_id;
        $ticket_request ['TraceId'] = $params['TraceId'];
        $ticket_request ['PNR'] = $params['PNR'];
        $ticket_request ['BookingId'] = $params['BookingId'];

        $request ['request'] = json_encode($ticket_request);
        $request ['url'] = $this->config['EndPointUrl'] . 'Ticket';
        $request ['remarks'] = 'Non-LCC Ticket(TBO)';
        $request ['status'] = SUCCESS_STATUS;

        return $request;
    }

    /**
     * Forms the GetBookingDetails request
     * @param unknown_type $request
     */
    private function run_get_booking_details_service_request($params) {
        $request = array();
        $get_booking_details_request = array();
        $get_booking_details_request['EndUserIp'] = $this->end_user_ip;
        $get_booking_details_request['TokenId'] = $this->api_session_id;
        $get_booking_details_request ['PNR'] = $params['PNR'];
        $get_booking_details_request ['BookingId'] = $params['BookingId'];

        $request ['request'] = json_encode($get_booking_details_request);
        $request ['url'] = $this->config['EndPointUrl'] . 'GetBookingDetails';
        $request ['remarks'] = 'GetBookingDetails(TBO)';
        $request ['status'] = SUCCESS_STATUS;
        return $request;
    }

    /**
     * Forms the SendChangeRequest
     * @param unknown_type $request
     */
    private function format_send_change_request($params) {
        $booking_transaction_details = $params['booking_transaction_details'][0];
        $BookingId = trim($booking_transaction_details['book_id']);
        $booking_customer_details = $params['booking_customer_details'];
        $passenger_origins = $params['passenger_origins'];
        $request = array();
        $send_change_request['EndUserIp'] = $this->end_user_ip;
        $send_change_request['TokenId'] = $this->api_session_id;
        $send_change_request['BookingId'] = $BookingId;
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
        $send_change_request ['RequestType'] = $RequestType; //NotSet = 0;FullCancellation = 1;PartialCancellation = 2;Reissuance = 3
        $send_change_request ['CancellationType'] = 0; //NotSet = 0;NoShow = 1;FlightCancelled = 2;Others = 3
        $send_change_request ['Sectors'] = $Sectors; //Mandatory only in case of partial cancellation
        $send_change_request ['TicketId'] = $TicketIds; //send multiple comma separated ticket id;Mandatory only in case of partial cancellations
        $send_change_request ['Remarks'] = 'Process the Cancellation';

        $request ['request'] = json_encode($send_change_request);
        $request ['url'] = $this->config['EndPointUrl'] . 'SendChangeRequest';
        $request ['remarks'] = 'SendChangeRequest(TBO)';
        $request ['status'] = SUCCESS_STATUS;
        return $request;
    }

    /**
     * Forms the GetChangeRequestStatus
     * @param unknown_type $request
     */
    private function format_get_change_request_status_request($ChangeRequestId) {
        $request = array();
        $get_change_request['EndUserIp'] = $this->end_user_ip;
        $get_change_request['TokenId'] = $this->api_session_id;
        $get_change_request['ChangeRequestId'] = $ChangeRequestId;

        $request ['request'] = json_encode($get_change_request);
        $request ['url'] = $this->config['EndPointUrl'] . 'GetChangeRequestStatus';
        $request ['remarks'] = 'GetChangeRequestStatus(TBO)';
        $request ['status'] = SUCCESS_STATUS;
        return $request;
    }

    /**
     * Calendar Fare Request
     */
    public function get_calendar_fare_request($request) {
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        if (empty($this->api_session_id) == true) {
            return $response;
        }
        // Calendar Fare RQ
        $calendar_fare_request = $this->calendar_fare_request($request);
        if ($calendar_fare_request ['status'] = SUCCESS_STATUS) {
            $response ['status'] = SUCCESS_STATUS;
            $curl_request = $this->form_curl_params($calendar_fare_request ['request'], $calendar_fare_request ['url']);
            $response ['data'] = $curl_request['data'];
        }
        return $response;
    }

    /**
     * Calendar Fare Request
     */
    public function get_update_calendar_fare_request($request) {
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        if (empty($this->api_session_id) == true) {
            // return failure object as the login signature is not set
            return $response;
        }
        // Calendar Fare RQ
        $calendar_fare_request = $this->update_calendar_fare_request($request);
        if ($calendar_fare_request ['status'] = SUCCESS_STATUS) {
            $response ['status'] = SUCCESS_STATUS;
            $curl_request = $this->form_curl_params($calendar_fare_request ['request'], $calendar_fare_request ['url']);
            $response ['data'] = $curl_request['data'];
        }
        return $response;
    }

    /**
     * Returns Flight List
     * @param unknown_type $search_id
     */
    public function get_flight_list($flight_raw_data, $search_id) {// exit('tbo');
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        
        $search_data = $this->search_data($search_id);
        if ($search_data ['status'] == SUCCESS_STATUS) {
            $api_response = json_decode($flight_raw_data, true);
            if ($this->valid_search_result($api_response) == TRUE) {
                $clean_format_data = $this->format_search_data_response($api_response, $search_data ['data']);
                //debug($clean_format_data);exit;
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
        return $response;
    }

    /**
     * Fare Rule
     * @param unknown_type $request
     */
    public function get_fare_rules($request) {
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        $fare_rule_request = $this->fare_rule_request($request);
        if ($fare_rule_request['status'] == SUCCESS_STATUS) {
            $fare_rule_response = $this->process_request($fare_rule_request ['request'], $fare_rule_request ['url'], $fare_rule_request ['remarks']);
            $fare_rule_response = json_decode($fare_rule_response, true);
            if (valid_array($fare_rule_response) == true && isset($fare_rule_response['Response']) == true && $fare_rule_response['Response']['ResponseStatus'] == SUCCESS_STATUS) {
                $response ['status'] = SUCCESS_STATUS;
                $response ['data']['FareRuleDetail'] = $this->format_fare_rule_response($fare_rule_response);
            } else {
                $response ['message'] = 'Not Available';
            }
        } else {
            $response ['status'] = FAILURE_STATUS;
        }
        return $response;
    }

    /**
     * Update Fare Quote
     * @param unknown_type $request
     */
    public function get_update_fare_quote($request, $search_id) {
        // echo "tettst";exit;

        // debug($request);exit;
     
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['ErrorMessage'] = ''; // Message to be returned
        $response ['ErrorCode'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        $update_fare_quote_request = $this->update_fare_quote_request($request);
        if ($update_fare_quote_request['status'] == SUCCESS_STATUS) {

            $update_fare_quote_response = $this->process_request($update_fare_quote_request ['request'], $update_fare_quote_request ['url'], $update_fare_quote_request ['remarks']);

            // $update_fare_quote_response = $this->CI->custom_db->get_static_response (231455);//Static Data//159
            // debug($update_fare_quote_response);exit;
            $update_fare_quote_response = json_decode($update_fare_quote_response, true);

            if (valid_array($update_fare_quote_response) == true && isset($update_fare_quote_response['Response']) == true && $update_fare_quote_response['Response']['ResponseStatus'] == SUCCESS_STATUS) {
                $search_price = $request['flight_search_data']['Price']['TotalDisplayFare'];
                $response ['status'] = SUCCESS_STATUS;
                $response ['data']['FareQuoteDetails'] = $this->format_update_fare_quote_response($update_fare_quote_response['Response'], $search_id, $search_price);
                // $farequote_price = $response ['data']['FareQuoteDetails']['JourneyList'][0][0]['Price']['TotalDisplayFare'];
                
            } else {
                $response ['message'] = 'Not Available';

                if (isset($update_fare_quote_response['Response']['Error']['ErrorMessage'])) {
                    $response ['description'] = $update_fare_quote_response['Response']['Error']['ErrorMessage'];
                    $response ['errorcode'] = $update_fare_quote_response['Response']['Error']['ErrorCode'];
                }
            }
        } else {
            $response ['status'] = FAILURE_STATUS;
        }
        return $response;
    }

    /**
     * Extra Services
     * @param unknown_type $request
     */
    public function get_extra_services($request, $flight_data='', $search_id) {
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        // debug($request);exit;
        /* if($request['IsLCC'] != true ){//Extra Services Only fro LCC Airline
          $response ['message'] = 'Not Available'; // Message to be returned
          return $response;
          } */

        $ssr_request = $this->ssr_request($request);
        if ($ssr_request['status'] == SUCCESS_STATUS) {
            $flight_data = Common_Flight::read_record($request['ResultToken']);
            $flight_data = json_decode($flight_data[0], true);
           // debug($flight_data);exit;
            $ssr_response = $this->process_request($ssr_request ['request'], $ssr_request ['url'], $ssr_request ['remarks']);

            // $ssr_response = $this->CI->custom_db->get_static_response (231456);//Static Data//217=> separate segment

            $ssr_response = json_decode($ssr_response, true);
            if (valid_array($ssr_response) == true && isset($ssr_response['Response']) == true && $ssr_response['Response']['ResponseStatus'] == SUCCESS_STATUS) {
                $response ['status'] = SUCCESS_STATUS;
                $response ['data']['ExtraServiceDetails'] = $this->format_extra_services($ssr_response['Response'], $search_id, $flight_data);
            } else {
                $response ['message'] = 'Not Available';
            }
        } else {
            $response ['status'] = FAILURE_STATUS;
        }
        return $response;
    }

    /**
     * Formates Extra Services Details
     * @param unknown_type $extra_services
     */
    private function format_extra_services($extra_services, $search_id, $flight_data) {
        $search_data = $this->CI->flight_model->get_safe_search_data($search_id);
        $search_data = $search_data['data'];
        // debug($search_id);exit;
        $formatted_extra_services = array();

        //Format Baggage Details
        $Baggage = $this->format_baggage_details($extra_services, $search_data, $flight_data);

        //Format Meal Details
        $Meals = $this->format_meals_details($extra_services);

        //Format Seat Details
        $Seat = $this->format_seat_details($extra_services);

        //Format Meal Preference Details
        $MealPreference = $this->format_meals_preference_details($extra_services, $search_data);

        //SeatPreference
        $SeatPreference = $this->format_seat_preference_details($extra_services, $search_data);

        if ($Baggage['status'] == SUCCESS_STATUS) {
            $formatted_extra_services['Baggage'] = $Baggage['data'];
        }
        if ($Meals['status'] == SUCCESS_STATUS) {
            $formatted_extra_services['Meals'] = $Meals['data'];
        }
        if ($Seat['status'] == SUCCESS_STATUS) {
            $formatted_extra_services['Seat'] = $Seat['data'];
        }
        if ($MealPreference['status'] == SUCCESS_STATUS) {
            $formatted_extra_services['MealPreference'] = $MealPreference['data'];
        }
        if ($SeatPreference['status'] == SUCCESS_STATUS) {
            $formatted_extra_services['SeatPreference'] = $SeatPreference['data'];
        }
        return $formatted_extra_services;
    }

     /**
     * Returns Baggae Details
     * @param unknown_type $extra_service_details
     */
    private function format_baggage_details($extra_service_details, $search_data, $flight_data) {
        // debug($search_data);exit;
        $baggage_details = array();
        $baggage_details['status'] = FAILURE_STATUS;
        $baggage_details['message'] = '';
        $baggage_details['data'] = array();
        if (isset($extra_service_details['Baggage']) == true && valid_array($extra_service_details['Baggage']) == true) {
            $baggage_data = array();
            $Baggage = $extra_service_details['Baggage'];
            foreach ($Baggage as $bag_k => $bag_v) {
                $inner_index = 0;
                foreach ($bag_v as $bd_k => $bd_v) {
                    $key = array();
                    if (intval($bd_v['Weight']) > 0 && $bag_k == 0) {//NotSet = 0;Segment = 1;FullJourney = 2
                        
                            $Origin = $bd_v['Origin'];
                        $Destination = $bd_v['Destination'];
                        $Destination = $search_data['to'];
                        $outer_index = $Origin . '-' . $Destination;
                        
                         if (isset($baggage_data[$outer_index]) == false) {
                            $inner_index = 0;
                        } else {
                            $inner_index++;
                        }
                        
                        
                        $key ['key'][$inner_index] = $bd_v;
                        // $baggage_id = serialized_data($key['key']);

                        $baggage_data[$outer_index][$inner_index]['BaggageId'] = $key;
                        $baggage_data[$outer_index][$inner_index]['Origin'] = $bd_v['Origin'];
                        $baggage_data[$outer_index][$inner_index]['Destination'] = $bd_v['Destination'];
                        $baggage_data[$outer_index][$inner_index]['Price'] = $bd_v['Price'];
                        $baggage_data[$outer_index][$inner_index]['Weight'] = $bd_v['Weight'] . 'Kg';
                        $baggage_data[$outer_index][$inner_index]['Code'] = $bd_v['Code'];

                        $inner_index++;
                    }
                }
            }
            // debug($flight_data);
            // debug($search_data);
            // exit;

            if (valid_array($baggage_data)) {
                $baggage_data = array_values($baggage_data);
                $bagge_data_new = array();
                if($search_data['trip_type'] == 'oneway' || $search_data['trip_type'] == 'return' && $search_data['is_domestic'] == 1){
                    $seg_count = count($flight_data['FlightDetails']['Details'][0]);
                    foreach($baggage_data as $bg_key => $bg_data){
                      if($bg_key == 0){
                            foreach($bg_data as $bd_key => $bd_value){
                                $bagge_id = $bd_value['BaggageId']['key'][$bd_key];
                                $bagge_id['Destination'] = $flight_data['FlightDetails']['Details'][0][$seg_count-1]['Destination']['AirportCode'];
                                $key1 ['key'][$bd_key] = $bagge_id;
                                // debug($key1);exit;
                                $bd_value['BaggageId'] = serialized_data($key1['key']);;
                                $bd_value['Destination'] = $flight_data['FlightDetails']['Details'][0][$seg_count-1]['Destination']['AirportCode'];
                                $bagge_data_new[$bg_key][$bd_key] = $bd_value;
                                // debug($bd_value);exit;
                            }
                        }
                       
                    }
                }
                // else if( $search_data['trip_type'] == 'return' && empty($search_data['is_domestic']) == true{
                //      $seg_count = count($flight_data['FlightDetails']['Details'][0]);
                //     foreach($baggage_data as $bg_key => $bg_data){
                //         if(($bg_key == 0 || $flight_data['FlightDetails']['Details'][1]['Origin']['AirportCode'] == $bg_data[0]['Origin']) && count($bagge_data_new)<3){
                //             foreach($bg_data as $bd_key => $bd_value){
                //                 $bagge_id = $bd_value['BaggageId']['key'][$bd_key];
                //                 $bagge_id['Destination'] = $flight_data['FlightDetails']['Details'][0][$seg_count-1]['Destination']['AirportCode'];
                //                 $key1 ['key'][$bd_key] = $bagge_id;
                //                 // debug($key1);exit;
                //                 $bd_value['BaggageId'] = serialized_data($key1['key']);;
                //                 $bd_value['Destination'] = $flight_data['FlightDetails']['Details'][0][$seg_count-1]['Destination']['AirportCode'];
                //                 $bagge_data_new[$bg_key][$bd_key] = $bd_value;
                //                 // debug($bd_value);exit;
                //             }

                //         }
                //     }
                // }
                else{
                    $bagge_data_new = $baggage_data;
                }
                // debug($bagge_data_new);exit;
                $baggage_details['status'] = SUCCESS_STATUS;
                $baggage_details['data'] = $bagge_data_new;
            }
        }
        return $baggage_details;
    }

    /**
     * Returns Meal Details
     * @param unknown_type $extra_service_details
     */
    private function format_meals_details($extra_service_details) {
        $meals_details = array();
        $meals_details['status'] = FAILURE_STATUS;
        $meals_details['message'] = '';
        $meals_details['data'] = array();

        if (isset($extra_service_details['MealDynamic']) == true && valid_array($extra_service_details['MealDynamic']) == true) {
            $meals_data = array();
            $MealDynamic = $extra_service_details['MealDynamic'];
            foreach ($MealDynamic as $meal_k => $meal_v) {

                foreach ($meal_v as $md_k => $md_v) {
                    $key = array();
                    if (intval($md_v['Quantity']) == 1) {//NotSet = 0;Segment = 1;FullJourney = 2
                        $Origin = $md_v['Origin'];
                        $Destination = $md_v['Destination'];

                        $outer_index = $Origin . '-' . $Destination;

                        if (isset($meals_data[$outer_index]) == false) {
                            $inner_index = 0;
                        } else {
                            $inner_index++;
                        }

                        $key ['key'][$inner_index] = $md_v;
                        $key ['key'][$inner_index]['Type'] = 'dynamic';
                        $meal_id = serialized_data($key['key']);

                        $meals_data[$outer_index][$inner_index]['MealId'] = $meal_id;
                        $meals_data[$outer_index][$inner_index]['Origin'] = $Origin;
                        $meals_data[$outer_index][$inner_index]['Destination'] = $Destination;
                        $meals_data[$outer_index][$inner_index]['Price'] = $md_v['Price'];
                        $meals_data[$outer_index][$inner_index]['Description'] = $md_v['AirlineDescription'];
                        $meals_data[$outer_index][$inner_index]['Code'] = $md_v['Code'];
                    }
                }
            }
            if (valid_array($meals_data)) {
                $meals_data = array_values($meals_data);
                $meals_details['status'] = SUCCESS_STATUS;
                $meals_details['data'] = $meals_data;
            }
        }
        return $meals_details;
    }

    /**
     * Returns Seat Details
     * @param unknown_type $extra_service_details
     */
    private function format_seat_details($extra_service_details) {
        $seat_details = array();
        $seat_details['status'] = FAILURE_STATUS;
        $seat_details['message'] = '';
        $seat_details['data'] = array();
        $seat_segment_counter = 0;
        if (isset($extra_service_details['SeatDynamic']) == true && valid_array($extra_service_details['SeatDynamic']) == true) {
            $seat_data = array();
            $SeatDynamic = $extra_service_details['SeatDynamic'];
            foreach ($SeatDynamic as $seat_k => $seat_v) {//loop1
                foreach ($seat_v as $segment_seat_k => $segment_seat_v) {//loop2
                    foreach ($segment_seat_v as $sd_k => $sd_v) {//loop3
                        foreach ($sd_v['RowSeats'] as $row_sd_k => $row_sd_v) {//loop4
                            $seat_row = array();
                            $seat_row_counter = 0;
                            foreach ($row_sd_v['Seats'] as $seat_index => $seat_value) {//loop5
                                $key = array();

                                if (intval($seat_value['AvailablityType']) > 0) {//AvailablityType Condition
                                    $Origin = $seat_value['Origin'];
                                    $Destination = $seat_value['Destination'];

                                    $key ['key'][$seat_row_counter] = $seat_value;
                                    $key ['key'][$seat_row_counter]['Type'] = 'dynamic';
                                    $seat_id = serialized_data($key['key']);


                                    $seat_row[$seat_row_counter]['AirlineCode'] = $seat_value['AirlineCode'];
                                    $seat_row[$seat_row_counter]['FlightNumber'] = $seat_value['FlightNumber'];
                                    $seat_row[$seat_row_counter]['AvailablityType'] = $seat_value['AvailablityType'];
                                    $seat_row[$seat_row_counter]['RowNumber'] = $seat_value['RowNo'];
                                    $seat_row[$seat_row_counter]['Origin'] = $Origin;
                                    $seat_row[$seat_row_counter]['Destination'] = $Destination;
                                    $seat_row[$seat_row_counter]['Price'] = $seat_value['Price'];
                                    $seat_row[$seat_row_counter]['SeatNumber'] = $seat_value['Code'];
                                    $seat_row[$seat_row_counter]['SeatId'] = $seat_id;
                                    $seat_row_counter++;
                                }//AvailablityType Condition
                            }//loop5

                            if (valid_array($seat_row) == true) {
                                //assign the seat row
                                $seat_data[$seat_segment_counter][] = $seat_row;
                            }
                        }//loop4
                        $seat_segment_counter++;
                    }//loop3
                }//loop2
            }//loop1

            if (valid_array($seat_data)) {
                $seat_details['status'] = SUCCESS_STATUS;
                $seat_details['data'] = $seat_data;
            }
        }
        return $seat_details;
    }

    /**
     * Returns Meal Preference Details
     * @param unknown_type $extra_service_details
     */
    private function format_meals_preference_details($extra_service_details, $search_data) {
        $meals_details = array();
        $meals_details['status'] = FAILURE_STATUS;
        $meals_details['message'] = '';
        $meals_details['data'] = array();

        if (isset($extra_service_details['Meal']) == true && valid_array($extra_service_details['Meal']) == true) {
            $Origin = trim(is_array($search_data['from']) ? $search_data['from'][0] : $search_data['from']);
            $Destination = trim(is_array($search_data['to']) ? end($search_data['to']) : $search_data['to']);
            $meals_data = array();
            $MealPreference = $extra_service_details['Meal'];
            foreach ($MealPreference as $md_k => $md_v) {
                $key = array();
                $key ['key'][$md_k] = $md_v;
                $key ['key'][$md_k]['Type'] = 'static';

                $meal_id = serialized_data($key['key']);
                $meals_data[0][$md_k]['MealId'] = $meal_id;
                $meals_data[0][$md_k]['Origin'] = $Origin;
                $meals_data[0][$md_k]['Destination'] = $Destination;
                $meals_data[0][$md_k]['Description'] = $md_v['Description'];
                $meals_data[0][$md_k]['Code'] = $md_v['Code'];
            }
            if (valid_array($meals_data)) {
                $meals_details['status'] = SUCCESS_STATUS;
                $meals_details['data'] = $meals_data;
            }
        }
        return $meals_details;
    }

    /**
     * Returns Seat Preference Details
     * @param unknown_type $extra_service_details
     */
    private function format_seat_preference_details($extra_service_details, $search_data) {
        $seat_details = array();
        $seat_details['status'] = FAILURE_STATUS;
        $seat_details['message'] = '';
        $seat_details['data'] = array();

        if (isset($extra_service_details['SeatPreference']) == true && valid_array($extra_service_details['SeatPreference']) == true) {
            $Origin = trim(is_array($search_data['from']) ? $search_data['from'][0] : $search_data['from']);
            $Destination = trim(is_array($search_data['to']) ? end($search_data['to']) : $search_data['to']);
            $seat_data = array();
            $SeatPreference = $extra_service_details['SeatPreference'];
            foreach ($SeatPreference as $sd_k => $sd_v) {
                $key = array();
                $key ['key'][$sd_k] = $sd_v;
                $key ['key'][$sd_k]['Type'] = 'static';

                $seat_id = serialized_data($key['key']);
                $seat_data[0][$sd_k]['SeatId'] = $seat_id;
                $seat_data[0][$sd_k]['Origin'] = $Origin;
                $seat_data[0][$sd_k]['Destination'] = $Destination;
                $seat_data[0][$sd_k]['Description'] = $sd_v['Description'];
                $seat_data[0][$sd_k]['Code'] = $sd_v['Code'];
            }
            if (valid_array($seat_data)) {
                $seat_details['status'] = SUCCESS_STATUS;
                $seat_details['data'] = $seat_data;
            }
        }
        return $seat_details;
    }

    /**
     * Process booking
     * @param array $booking_params
     */
    function process_booking($booking_params, $app_reference, $sequence_number, $search_id) {
        
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned

        $ticket_response = array();
        $book_response = array();

        $ResultToken = $booking_params['ResultToken'];
        $ticket_service_response['status'] = FAILURE_STATUS;



        # Check GST Mandatory or Not
        $IsGSTMandatory = false;
        if (isset($booking_params['flight_data']['IsGSTMandatory'])) {
            if ($booking_params['flight_data']['IsGSTMandatory'] == true) {
                $IsGSTMandatory = $booking_params['flight_data']['IsGSTMandatory'];
            }
        }
        $booking_params['IsGSTMandatory'] = $IsGSTMandatory;


        //Format Booking Passenger Data
        $booking_params['Passengers'] = $this->format_booking_passenger_object($booking_params);

        if ($ResultToken['IsLCC'] == true) {
            //Run LCC Ticket method
            $AirlineCode = '';
            if (isset($booking_params['flight_data']['FlightDetails']['Details'][0][0]['OperatorCode'])) {
                $AirlineCode = $booking_params['flight_data']['FlightDetails']['Details'][0][0]['OperatorCode'];
            }
            $all_hold_airline_list_1 = $this->CI->custom_db->single_table_records('hold_airline_list', '*', array('status' => 1, 'domain_origin' => get_domain_auth_id(), 'code' => $AirlineCode));

            /* $data_d=array();
              $data_d['Flight'] = json_encode($booking_params);
              $data_d['OperatorCode'] = $AirlineCode;
              $data_d['Type'] = "LCC";
              $data_d['Db_res'] = json_encode($all_hold_airline_list_1);
              $data_d['app_reference'] = $app_reference;
              $data_d['sequence_number'] = $sequence_number;

              $this->CI->custom_db->insert_record('hold_airline_admin', $data_d); */

            $ticketing_status = '';
            if ($all_hold_airline_list_1['status'] == true) {
                $ticketing_status = "HOLD";
            }




            if ($ticketing_status == "HOLD") {
                $ticket_service_response['status'] = "BOOKING_HOLD";
            } else {
                //Run LCC Ticket method
                $ticket_service_response = $this->run_lcc_ticket_service($booking_params, $app_reference, $sequence_number);
            }

            //  if ($ticket_service_response['status'] == SUCCESS_STATUS) {  
            if ($ticket_service_response['status'] == SUCCESS_STATUS || empty($ticket_service_response['data']['ticket_response']['PNR']) == false) {

                $response ['status'] = SUCCESS_STATUS;
                $ticket_response = $ticket_service_response['data']['ticket_response'];

                $flight_booking_status = "BOOKING_CONFIRMED";
                $this->CI->common_flight->update_flight_booking_status($flight_booking_status, $app_reference, $sequence_number, $this->booking_source);
            }
            // else if($ticket_service_response['status']=="BOOKING_HOLD")
            else if ($ticketing_status == "HOLD") {
                $response ['status'] = SUCCESS_STATUS;
                $response ['message'] = "Your Booking Hold, Please contact Administrator";
                $ticket_response = true;
                $flight_booking_status = 'BOOKING_HOLD';
                $this->CI->common_flight->update_flight_booking_status($flight_booking_status, $app_reference, $sequence_number, $this->booking_source);
            } else {
                $response ['message'] = $ticket_service_response['message'];
                $flight_booking_status = 'BOOKING_FAILED';
                $this->CI->common_flight->update_flight_booking_status($flight_booking_status, $app_reference, $sequence_number, $this->booking_source);
            }
        } else {

            //Run Book Method for GDS carrier 
            $book_service_response = $this->run_book_service($booking_params, $app_reference, $sequence_number);
            if ($book_service_response['status'] == SUCCESS_STATUS) {
                $response ['status'] = SUCCESS_STATUS;
                $book_response = $book_service_response['data']['book_response'];

                //Save BookFlight Details
                $this->save_book_response_details($book_response, $app_reference, $sequence_number);

                #Set HOLD option for few Airlines
                /*  $booking_hold = '';
                  if (isset($book_response['FlightItinerary']['Segments'])) {
                  $booking_segments = $book_response['FlightItinerary']['Segments'];
                  foreach ($booking_segments as $key => $value) {
                  $AirlineCode = $value['Airline']['AirlineCode'];
                  if ($AirlineCode == "9W") {
                  //  $ticketing_status = "HOLD";
                  $ticketing_status = "Tikcet";
                  } else {
                  $ticketing_status = "Tikcet";
                  }
                  }
                  } */

                /* Set HOLD option for customer Wise and Airline Wise 
                 * Controlled from Admin Panel 
                 */

                if (isset($book_response['FlightItinerary']['Segments'])) {
                    $booking_segments = $book_response['FlightItinerary']['Segments'];
                    foreach ($booking_segments as $key => $value) {

                        $AirlineCode = $value['Airline']['AirlineCode'];

                        $all_hold_airline_list = $this->CI->custom_db->single_table_records('hold_airline_list', '*', array('status' => 1, 'domain_origin' => get_domain_auth_id(), 'code' => $AirlineCode));

                        $data_d1 = array();
                        $data_d1['Flight'] = json_encode($value);
                        $data_d1['OperatorCode'] = $AirlineCode;
                        $data_d1['Type'] = "GDS";
                        $data_d1['Db_res'] = json_encode($all_hold_airline_list);
                        $data_d1['app_reference'] = $app_reference;
                        $data_d1['sequence_number'] = $sequence_number;

                        $this->CI->custom_db->insert_record('hold_airline_admin', $data_d1);

                        $ticketing_status = '';
                        if ($all_hold_airline_list['status'] == true) {
                            $ticketing_status = "HOLD";
                        } else {
                            $ticketing_status = "Tikcet";
                        }
                    }
                }

                if ($ticketing_status == "Tikcet") {
                    //Run Non-LCC Ticket method
                    $ticket_request_params = array();
                    $ticket_request_params['PNR'] = $book_response['PNR'];
                    $ticket_request_params['BookingId'] = $book_response['BookingId'];
                    $ticket_request_params['TraceId'] = $ResultToken['TraceId'];
                    $ticket_service_response = $this->run_non_lcc_ticket_service($ticket_request_params, $app_reference, $sequence_number);

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
        }
        if (valid_array($ticket_response) == true || valid_array($book_response) == true) {

            if (valid_array($ticket_response) == true) {
                //Run GetBookingDetails method only if Ticket Service is success
                $get_booking_details_req = array();
                $get_booking_details_req['PNR'] = $ticket_response['PNR'];
                $get_booking_details_req['BookingId'] = $ticket_response['BookingId'];

                $get_booking_details_service_response = $this->run_get_booking_details_service($get_booking_details_req, $app_reference, $sequence_number);
                if ($get_booking_details_service_response['status'] == SUCCESS_STATUS) {
                    $fligt_ticket_details = $get_booking_details_service_response['data']['get_booking_details_response']; //GetBookingDetails Service Response
                } else {
                    $fligt_ticket_details = $ticket_response; //Ticket Service Response
                }
            } else {
                $fligt_ticket_details = $book_response; //Book Service Response
            }
            //Save Ticket Details
            $this->save_flight_ticket_details($fligt_ticket_details, $app_reference, $sequence_number, $search_id);
        }

        return $response;
    }

    /**
     * Hold the ticket
     * @param array $booking_params
     */
    function hold_ticket($booking_params, $app_reference, $sequence_number, $search_id) {
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned

        $book_response = array();

        $ResultToken = $booking_params['ResultToken'];


        # Check GST Mandatory or Not
        $IsGSTMandatory = false;
        if (isset($booking_params['flight_data']['IsGSTMandatory'])) {
            if ($booking_params['flight_data']['IsGSTMandatory'] == true) {
                $IsGSTMandatory = $booking_params['flight_data']['IsGSTMandatory'];
            }
        }
        $booking_params['IsGSTMandatory'] = $IsGSTMandatory;


        //Format Booking Passenger Data
        $booking_params['Passengers'] = $this->format_booking_passenger_object($booking_params);

        //Run Book Method
        $book_service_response = $this->run_book_service($booking_params, $app_reference, $sequence_number);
        if ($book_service_response['status'] == SUCCESS_STATUS) {
            $response ['status'] = SUCCESS_STATUS;
            $book_response = $book_service_response['data']['book_response'];

            //Save BookFlight Details
            $this->save_book_response_details($book_response, $app_reference, $sequence_number);
        } else {
            $response ['message'] = $book_service_response['message'];
            $flight_booking_status = 'BOOKING_FAILED';
            $this->CI->common_flight->update_flight_booking_status($flight_booking_status, $app_reference, $sequence_number, $this->booking_source);
        }

        if (valid_array($book_response) == true) {

            $fligt_ticket_details = $book_response; //Book Service Response
            //Save Ticket Details
            $this->save_flight_ticket_details($fligt_ticket_details, $app_reference, $sequence_number, $search_id);
        }

        return $response;
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
        if ($elgible_for_ticket_cancellation['status'] == SUCCESS_STATUS) {
            $booking_details = $this->CI->flight_model->get_flight_booking_transaction_details($app_reference, $sequence_number, $this->booking_source);
            $booking_details = $booking_details['data'];
            $booking_transaction_details = $booking_details['booking_transaction_details'][0];
            $flight_booking_transaction_details_origin = $booking_transaction_details['origin'];

            $request_params = $booking_details;
            $request_params['passenger_origins'] = $ticket_ids;
            $request_params['IsFullBookingCancel'] = $IsFullBookingCancel;

            //SendChange Request
            $send_change_request = $this->send_change_request($request_params);
            if ($send_change_request['status'] == SUCCESS_STATUS) {
                $response ['status'] = SUCCESS_STATUS;
                $response ['message'] = 'Cancellation Request is processing';
                $send_change_response = $send_change_request['data']['send_change_response'];

                $ticket_cancel_ids = $send_change_response['TicketCRInfo'];
                foreach ($ticket_cancel_ids as $tck => $tcv) {
                    $ChangeRequestId = $tcv['ChangeRequestId'];
                    $TicketId = $tcv['TicketId'];

                    //GetChangeRequestStatus
                    $get_change_request_status = $this->get_change_request_status($ChangeRequestId);
                    if ($get_change_request_status['status'] == SUCCESS_STATUS) {
                        $ticket_cancellation_details = $get_change_request_status['data']['get_change_response'];
                    } else {
                        $ticket_cancellation_details = $tcv;
                    }

                    $passenger_ticket_details = $this->CI->db->query('select FP.origin from flight_booking_passenger_details FP 
													join flight_passenger_ticket_info FT on FT.passenger_fk=FP.origin
													where FP.flight_booking_transaction_details_fk=' . $flight_booking_transaction_details_origin . ' and FT.TicketId=' . $TicketId)->row_array();
                    $passenger_origin = $passenger_ticket_details['origin'];
                    //Save Cancellation Details
                    $this->save_ticket_cancellation_details($ticket_cancellation_details, $passenger_origin);

                    $this->CI->common_flight->update_ticket_cancel_status($app_reference, $sequence_number, $passenger_origin);
                    //Update Ticket Cancellation Status
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
     *
     * Enter description here ...
     * @param unknown_type $booking_params
     * @param unknown_type $app_reference
     * @param unknown_type $sequence_number
     */
    private function run_lcc_ticket_service($booking_params, $app_reference, $sequence_number) {
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        $lcc_ticket_service_request = $this->run_lcc_ticket_service_request($booking_params);
        if ($lcc_ticket_service_request['status'] == SUCCESS_STATUS) {

            $lcc_ticket_service_response = $this->process_request($lcc_ticket_service_request ['request'], $lcc_ticket_service_request ['url'], $lcc_ticket_service_request ['remarks']);

            // $lcc_ticket_service_response = $this->CI->custom_db->get_static_response (218566);//Static bOOk Data//7=>Failed;6=> Success

            $lcc_ticket_service_response = json_decode($lcc_ticket_service_response, true);


            if ($this->validate_ticket_response($lcc_ticket_service_response) == true) {
                $response ['status'] = SUCCESS_STATUS;
                $response ['data']['ticket_response'] = $lcc_ticket_service_response['Response'] ['Response'];
            } else {
                $error_message = '';
                if (isset($lcc_ticket_service_response['Response'] ['Error'] ['ErrorMessage'])) {
                    $error_message = $lcc_ticket_service_response['Response'] ['Error'] ['ErrorMessage'];
                }
                if (empty($error_message) == true) {
                    $error_message = 'Ticketing Failed';
                }
                $response ['message'] = $error_message;

                //Log Exception
                $exception_log_message = '';
                $this->CI->exception_logger->log_exception($app_reference, $this->booking_source_name . '- (<strong>TICKET</strong>)', $exception_log_message, $lcc_ticket_service_response);
            }
        } else {
            $response ['status'] = FAILURE_STATUS;
        }
        return $response;
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $booking_params
     * @param unknown_type $app_reference
     * @param unknown_type $sequence_number
     */
    private function run_book_service($booking_params, $app_reference, $sequence_number) {
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        $book_service_request = $this->run_book_service_request($booking_params);
        if ($book_service_request['status'] == SUCCESS_STATUS) {

            $book_service_response = $this->process_request($book_service_request ['request'], $book_service_request ['url'], $book_service_request ['remarks']);

            //$book_service_response = $this->CI->custom_db->get_static_response (2);//Static bOOk Data//1=>Failed;2=> Success

            $book_service_response = json_decode($book_service_response, true);
            if (valid_array($book_service_response) == true && isset($book_service_response['Response']['Response']) == true && ($book_service_response['Response']['Response']['Status'] == SUCCESS_STATUS || $book_service_response['Response']['Response']['Status'] == 5)) {
                $response ['status'] = SUCCESS_STATUS;
                $response ['data']['book_response'] = $book_service_response['Response'] ['Response'];
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

            $non_lcc_ticket_service_response = $this->process_request($non_lcc_ticket_service_request ['request'], $non_lcc_ticket_service_request ['url'], $non_lcc_ticket_service_request ['remarks']);

            //$non_lcc_ticket_service_response = $this->CI->custom_db->get_static_response (3);//Static bOOk Data//4=>Failed;3=> Success

            $non_lcc_ticket_service_response = json_decode($non_lcc_ticket_service_response, true);

            if ($this->validate_ticket_response($non_lcc_ticket_service_response) == true) {
                $response ['status'] = SUCCESS_STATUS;
                $response ['data']['ticket_response'] = $non_lcc_ticket_service_response['Response'] ['Response'];
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
     *
     * Enter description here ...
     * @param unknown_type $booking_params
     * @param unknown_type $app_reference
     * @param unknown_type $sequence_number
     */
    private function run_get_booking_details_service($request_params, $app_reference, $sequence_number) {
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned

        $get_booking_details_service_request = $this->run_get_booking_details_service_request($request_params);

        if ($get_booking_details_service_request['status'] == SUCCESS_STATUS) {

            $get_booking_details_service_response = $this->process_request($get_booking_details_service_request ['request'], $get_booking_details_service_request ['url'], $get_booking_details_service_request ['remarks']);

            // $get_booking_details_service_response = $this->CI->custom_db->get_static_response (218567);//Static bOOk Data//5=>Success

            $get_booking_details_service_response = json_decode($get_booking_details_service_response, true);

            if (valid_array($get_booking_details_service_response) == true && isset($get_booking_details_service_response['Response']) == true && $get_booking_details_service_response['Response']['ResponseStatus'] == SUCCESS_STATUS) {
                $response ['status'] = SUCCESS_STATUS;
                $response ['data']['get_booking_details_response'] = $get_booking_details_service_response['Response'];
            } else {
                $error_message = 'Details Not Found';
                $response ['message'] = $error_message;
            }
        } else {
            $response ['status'] = FAILURE_STATUS;
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
            $send_change_response = $this->process_request($send_change_request ['request'], $send_change_request ['url'], $send_change_request ['remarks']);
            //$send_change_response = $this->CI->custom_db->get_static_response (34);

            $send_change_response = json_decode($send_change_response, true);

            if (valid_array($send_change_response) == true && isset($send_change_response['Response']) == true && $send_change_response['Response']['ResponseStatus'] == SUCCESS_STATUS) {
                $response ['status'] = SUCCESS_STATUS;
                $response ['data']['send_change_response'] = $send_change_response['Response'];
            } else {
                $error_message = '';
                if (isset($send_change_response['Response'] ['Error'] ['ErrorMessage'])) {
                    $error_message = $send_change_response['Response'] ['Error'] ['ErrorMessage'];
                }
                if (empty($error_message) == true) {
                    $error_message = 'Cancellation Failed';
                }
                $response ['message'] = $error_message;
            }
        } else {
            $response ['status'] = FAILURE_STATUS;
        }
        return $response;
    }

    /**
     * GetChangeRequestStatus
     * @param unknown_type $booking_details
     * //ChangeRequestStatus: NotSet = 0,Unassigned = 1,Assigned = 2,Acknowledged = 3,Completed = 4,Rejected = 5,Closed = 6,Pending = 7,Other = 8
     */
    public function get_change_request_status($ChangeRequestId) {
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        $get_change_request_status_request = $this->format_get_change_request_status_request($ChangeRequestId);
        if ($get_change_request_status_request['status'] == SUCCESS_STATUS) {

            $get_change_request_status_response = $this->process_request($get_change_request_status_request ['request'], $get_change_request_status_request ['url'], $get_change_request_status_request ['remarks']);

            //$get_change_request_status_response = $this->CI->custom_db->get_static_response (35);

            $get_change_request_status_response = json_decode($get_change_request_status_response, true);

            if (valid_array($get_change_request_status_response) == true && isset($get_change_request_status_response['Response']) == true && $get_change_request_status_response['Response']['ResponseStatus'] == SUCCESS_STATUS) {
                $response ['status'] = SUCCESS_STATUS;
                $response ['data']['get_change_response'] = $get_change_request_status_response['Response'];
            }
        } else {
            $response ['status'] = FAILURE_STATUS;
        }
        return $response;
    }

    /**
     * Validates the Ticket Resposne 
     * //TicketStatus
      Failed = 0,
      Successful = 1,
      NotSaved = 2,
      NotCreated = 3,
      NotAllowed = 4,
      InProgress = 5,
      TicketeAlreadyCreated= 6,
      PriceChanged = 8,
      OtherError = 9*
     */
    private function validate_ticket_response($response) {
        if (valid_array($response) == true && $this->is_ticketing_error($response) == false &&
                isset($response['Response']['Response']['TicketStatus']) == true && in_array($response['Response']['Response']['TicketStatus'], array(1, 5))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks Ticketing Error
     */
    private function is_ticketing_error($response) {
        //14 => Duplicate Booking Error
        $ticket_error_status_codes = array(14);
        if (isset($response['Response']['Error']['ErrorCode']) == true && in_array($response['Response']['Error']['ErrorCode'], $ticket_error_status_codes)) {
            return true;
        } else {
            return false;
        }
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

        $update_data['pnr'] = $book_response['PNR'];
       
        $update_data['book_id'] = $book_response['BookingId'];

        $update_condition['app_reference'] = $app_reference;
        $update_condition['sequence_number'] = $sequence_number;

        $this->CI->custom_db->update_record('flight_booking_transaction_details', $update_data, $update_condition);

        $flight_booking_status = 'BOOKING_HOLD';
        $this->CI->common_flight->update_flight_booking_status($flight_booking_status, $app_reference, $sequence_number, $this->booking_source);
    }

    /**
     * Save Flight Ticket Details
     * @param unknown_type $fligt_ticket_details
     * @param unknown_type $app_reference
     * @param unknown_type $sequence_number
     */
    private function save_flight_ticket_details($fligt_ticket_details, $app_reference, $sequence_number, $search_id) {
        $flight_booking_transaction_details_fk = $this->CI->custom_db->single_table_records('flight_booking_transaction_details', 'origin', array('app_reference' => $app_reference, 'sequence_number' => $sequence_number));
        $flight_booking_itinerary_details_fk = $this->CI->custom_db->single_table_records('flight_booking_itinerary_details', 'airline_code', array('app_reference' => $app_reference));
        $flight_booking_transaction_details_fk = $flight_booking_transaction_details_fk['data'][0]['origin'];

        $FlightItinerary = $fligt_ticket_details['FlightItinerary'];
        $book_id = $FlightItinerary['BookingId'];
        $pnr = $FlightItinerary['PNR'];
        $airline_pnr = $pnr;
        if(isset($FlightItinerary['Segments'][0]['AirlinePNR'])){
            if(empty($FlightItinerary['Segments'][0]['AirlinePNR']) == false){
                 $airline_pnr = $FlightItinerary['Segments'][0]['AirlinePNR'];
            }
        }
       
        
        //1. Update Bookinf Id and PNR Details
        $update_pnr_data = array();
        $update_pnr_data['book_id'] = $book_id;
        $update_pnr_data['pnr'] = $pnr;
        $update_pnr_data['airline_pnr'] = $airline_pnr;
        // debug($update_pnr_data);exit;
        $this->CI->custom_db->update_record('flight_booking_transaction_details', $update_pnr_data, array('origin' => $flight_booking_transaction_details_fk));

        //2.Update Price Details
        $itineray_price_details = array();
        $itineray_price_details['Fare'] = $FlightItinerary['Fare'];
        $passenger_details = $itineray_price_details['FareBreakdown'] = $FlightItinerary['Passenger'];
        $segment_details = $FlightItinerary['Segments'];
        $itineray_price_details = $this->format_itineray_price_details($itineray_price_details, true);

        $airline_code = '';
        if (isset($flight_booking_itinerary_details_fk['data'][0]['airline_code'])) {
            $airline_code = $flight_booking_itinerary_details_fk['data'][0]['airline_code'];
        }

        $this->CI->load->library('flight/common_flight');
        $flight_price_details = $this->CI->common_flight->final_booking_transaction_fare_details($itineray_price_details, $search_id, $this->booking_source, $airline_code);

        $fare_details = $flight_price_details['Price'];
        $fare_breakup = $flight_price_details['PriceBreakup'];
        $passenger_breakup = $fare_details['passenger_breakup'];
        $single_pax_fare_breakup = $this->CI->common_flight->get_single_pax_fare_breakup($passenger_breakup);
        $this->CI->common_flight->update_flight_booking_tranaction_price_details($app_reference, $sequence_number, $fare_details['commissionable_fare'], $fare_details['admin_commission'], $fare_details['agent_commission'], $fare_details['admin_tds'], $fare_details['agent_tds'], $fare_details['admin_markup'], $fare_breakup);


        //4.Update Passenger Details: Ticket Details,Passenger Breakdown
        $passenger_details = force_multple_data_format($passenger_details);
        $get_passenger_details_condition = array();
        $get_passenger_details_condition['flight_booking_transaction_details_fk'] = $flight_booking_transaction_details_fk;
        $passenger_details_data = $GLOBALS['CI']->custom_db->single_table_records('flight_booking_passenger_details', 'origin, passenger_type', $get_passenger_details_condition);
        $passenger_details_data = $passenger_details_data['data'];
        $passenger_origins = group_array_column($passenger_details_data, 'origin');
        $passenger_types = group_array_column($passenger_details_data, 'passenger_type');
        $total_baggage = 0;
        $baggage_type = 'Kg';
        foreach ($passenger_details as $pax_k => $pax_v) {
            $passenger_fk = intval(array_shift($passenger_origins));
            $pax_type = array_shift($passenger_types);
            $total_baggage += (int) $pax_v['SegmentAdditionalInfo'][0]['Baggage'];
            $bagge_type_val = $pax_v['SegmentAdditionalInfo'][0]['Baggage'];
            $bagge_type_val = explode(' ', $bagge_type_val);
            $baggage_type = $bagge_type_val[1];
            switch (strtolower($pax_type)) {
                case 'adult':
                    $pax_type = 'ADT';
                    break;
                case 'child':
                    $pax_type = 'CHD';
                    break;
                case 'infant':
                    $pax_type = 'INF';
                    break;
            }

            if (isset($pax_v['Ticket']) == true && valid_array($pax_v['Ticket']) == true) {
                $ticket_id = $pax_v['Ticket']['TicketId'];
                $ticket_number = $pax_v['Ticket']['TicketNumber'];
            } else {
                $ticket_id = '';
                $ticket_number = '';
            }
            //Update Passenger Ticket Details
            $this->CI->common_flight->update_passenger_ticket_info($passenger_fk, $ticket_id, $ticket_number, $single_pax_fare_breakup[$pax_type]);
        }
        $total_baggage = $total_baggage . ' ' . $baggage_type;
        //3.Update Segment Details:PNR,Terminal,Baggage
        foreach ($segment_details as $bsk => $bsv) {
            //itinerary condition for update
            $update_itinerary_condition = array();
            $update_itinerary_condition['flight_booking_transaction_details_fk'] = $flight_booking_transaction_details_fk;
            $update_itinerary_condition['app_reference'] = $app_reference;
            $update_itinerary_condition['from_airport_code'] = $bsv['Origin']['Airport']['AirportCode'];
            $update_itinerary_condition['to_airport_code'] = $bsv['Destination']['Airport']['AirportCode'];
            $update_itinerary_condition['departure_datetime'] = date('Y-m-d H:i:s', strtotime($bsv['Origin']['DepTime']));
           
            //itinerary updated data
            $update_itinerary_data = array();
            $update_itinerary_data['airline_pnr'] = ((isset($bsv['AirlinePNR']) && empty($bsv['AirlinePNR']) == false) ? $bsv['AirlinePNR'] : $pnr);
            
            $attributes['baggage'] = $total_baggage;
            $attributes['departure_terminal'] = $bsv['Origin']['Airport']['Terminal'];
            $attributes['arrival_terminal'] = $bsv['Destination']['Airport']['Terminal'];
            $update_itinerary_data['attributes'] = json_encode($attributes);

            $GLOBALS['CI']->custom_db->update_record('flight_booking_itinerary_details', $update_itinerary_data, $update_itinerary_condition);
        }
    }

    /**
     * Save Cancellation Details
     * @param unknown_type $cancellation_details
     * @param unknown_type $passenger_origin
     */
    public function save_ticket_cancellation_details($cancellation_details, $passenger_origin) {
        //Adding Cancellation Details
        //Add/Update Pax Cancellation Details
        //StatusCode:NotSet = 0,Unassigned = 1,Assigned = 2,Acknowledged = 3,Completed = 4,Rejected = 5,Closed = 6,Pending = 7,Other = 8
        if ($cancellation_details['ChangeRequestStatus'] == 4) {
            $data['cancellation_processed_on'] = date('Y-m-d H:i:s');
        }
        $data['RequestId'] = $cancellation_details['ChangeRequestId'];
        $data['API_RefundedAmount'] = (isset($cancellation_details['RefundedAmount']) ? $cancellation_details['RefundedAmount'] : 0);
        $data['API_CancellationCharge'] = (isset($cancellation_details['CancellationCharge']) ? $cancellation_details['CancellationCharge'] : 0);
        $data['API_ServiceTaxOnRefundAmount'] = (isset($cancellation_details['ServiceTaxOnRAF']) ? $cancellation_details['ServiceTaxOnRAF'] : 0);
        $data['API_SwachhBharatCess'] = (isset($cancellation_details['SwachhBharatCess']) ? $cancellation_details['SwachhBharatCess'] : 0);
        $data['API_KrishiKalyanCess'] = (isset($cancellation_details['KrishiKalyanCess']) ? $cancellation_details['KrishiKalyanCess'] : 0);

        $data['ChangeRequestStatus'] = $cancellation_details['ChangeRequestStatus'];
        $data['statusDescription'] = $this->get_cancellation_status_description($data['ChangeRequestStatus']);
        $data['current_status'] = $cancellation_details['ChangeRequestStatus'];

        /*** Addedd Newly Start **/
        $data['StatusCode'] = ' ';
        $data['statusCategory'] = ' ';
        $data['cancellation_processed_on'] = date('Y-m-d H:i:s');
        $data['refund_amount'] = 0;
        $data['cancellation_charge'] = 0;
        $data['service_tax_on_refund_amount'] = 0;
        $data['swachh_bharat_cess'] = 0;
        $data['refund_status'] = 'PROCESSED';
        $data['refund_payment_mode'] = 'offline';
        $data['refund_date'] = date('Y-m-d H:i:s');
        $data['currency'] = 0;
        $data['currency_conversion_rate'] = 0;
        $data['refund_comments'] = ' ';
        /*** Addedd Newly End **/


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
    }

    /**
     * Returns Cancellation status description
     */
    private function get_cancellation_status_description($ChangeRequestStatus) {
        $description = '';
        //NotSet = 0,Unassigned = 1,Assigned = 2,Acknowledged = 3,Completed = 4,Rejected = 5,Closed = 6,Pending = 7,Other = 8
        switch ($ChangeRequestStatus) {
            case 1: $description = 'Unassigned';
                break;
            case 2: $description = 'Assigned';
                break;
            case 3: $description = 'Acknowledged';
                break;
            case 4: $description = 'Completed';
                break;
            case 5: $description = 'Rejected';
                break;
            case 6: $description = 'Closed';
                break;
            case 7: $description = 'Pending';
                break;
            case 7: $description = 'Other';
                break;
            default:$description = 'NotSet';
        }
        return $description;
    }

    /**
     * Formats Passenger Details
     * Assign the FareBreakdown for Each Passenger
     */
    private function format_booking_passenger_object($request_params) {
        $fare_break_down = ($request_params['ResultToken']['FareBreakdown']); //FareBreakdown
        $passengers = $request_params['Passengers'];
        $passenger_fare_breakdown = $this->assign_booking_passenger_fare_breakdown($fare_break_down);
        $passenger_data = array();
        $IsLCC = $request_params['ResultToken']['IsLCC'];
        //Default Passport Expiry
        $default_passport_expiry_date = date('Y-m-d', strtotime('+5 years'));
        foreach ($passengers as $k => $v) {
            //DOB
            if (isset($v['DateOfBirth']) == true && empty($v['DateOfBirth']) == false) {
                $pax_dob = $v['DateOfBirth'];
            } else {
                $pax_dob = $this->get_pax_default_dob($v['PaxType']);
            }
            //Passport
            if (isset($v['PassportExpiry']) == true && empty($v['PassportExpiry']) == false) {
                $passport_expiry = $v['PassportExpiry'];
            } else {
                $passport_expiry = $default_passport_expiry_date;
            }
            if (isset($v['PassportNumber']) == true && empty($v['PassportNumber']) == false) {
                $passport_number = $v['PassportNumber'];
            } else {
                $passport_number = rand(1111111111, 9999999999);
            }
            //AddressLine2
            if (isset($v['AddressLine2']) == true && empty($v['AddressLine2']) == false) {
                $address_line2 = $v['AddressLine2'];
            } else {
                $address_line2 = 'Bangalore';
            }
            $passenger_data [$k] ['Title'] = $v['Title'];
            $passenger_data [$k] ['FirstName'] = $v['FirstName'];
            $passenger_data [$k] ['LastName'] = $v['LastName'];
            $passenger_data [$k] ['PaxType'] = $v['PaxType'];
            $passenger_data [$k] ['DateOfBirth'] = $pax_dob . 'T00:00:00'; // optional
            $passenger_data [$k] ['Gender'] = $v['Gender'];
            //$passenger_data [$k] ['PassportNo'] = $passport_number;
            $passenger_data [$k] ['PassportNo'] = preg_replace('/\s+/', '', $passport_number); // optional

            $passenger_data [$k] ['PassportExpiry'] = $passport_expiry . 'T00:00:00'; // optional
            $passenger_data [$k] ['AddressLine1'] = substr($v['AddressLine1'], 0, 31);
            $passenger_data [$k] ['AddressLine2'] = ''; // optional
            $passenger_data [$k] ['City'] = $v['City'];
            $passenger_data [$k] ['CountryCode'] = $v['CountryCode'];
            $passenger_data [$k] ['CountryName'] = $v['CountryName'];
            $passenger_data [$k] ['ContactNo'] = $this->validate_mobile_number($v['ContactNo']);
            $passenger_data [$k] ['Email'] = $v['Email'];
            if ($v['IsLeadPax'] == 1) {
                $v['IsLeadPax'] = true;
            } else {
                $v['IsLeadPax'] = false;
            }
            $passenger_data [$k] ['IsLeadPax'] = $v['IsLeadPax'];
            $passenger_data [$k] ['FFAirline'] = null; // optional
            $passenger_data [$k] ['FFNumber'] = null; // optional


            if ($request_params['IsGSTMandatory'] == true) {
                $passenger_data [$k] ['GSTCompanyAddress'] = "Bangalore";
                $passenger_data [$k] ['GSTCompanyContactNumber'] = "9916100864";
                $passenger_data [$k] ['GSTCompanyName'] = "ACCENTRIA SOLUTIONS PRIVATE LIMITED";
                $passenger_data [$k] ['GSTNumber'] = "29AANCA8324M2Z0";
                $passenger_data [$k] ['GSTCompanyEmail'] = "vinay@travelomatix.com";
            }


            $passenger_data [$k] ['Fare'] = $passenger_fare_breakdown [$v ['PaxType']];
            if ($IsLCC == true) {//'LCC FLIGHT
                if (isset($v['BaggageId']) == true && valid_array($v['BaggageId']) == true) {
                    $BaggageDetails = $v['BaggageId'];
                } else {
                    $BaggageDetails = array();
                }
                if (isset($v['MealId']) == true && valid_array($v['MealId']) == true) {
                    $MealsDetails = $v['MealId'];
                } else {
                    $MealsDetails = array();
                }
                if (isset($v['SeatId']) == true && valid_array($v['SeatId']) == true) {
                    $SeatDetails = $v['SeatId'];
                } else {
                    $SeatDetails = array();
                }
                //Baggage Details
                if (isset($v['BaggageId']) == true && valid_array($v['BaggageId']) == true) {
                    $passenger_data [$k] ['Baggage'] = $this->formate_lcc_baggage_request_details($BaggageDetails);
                }
                //Meals Details
                if (isset($v['MealId']) == true && valid_array($v['MealId']) == true) {
                    $passenger_data [$k] ['MealDynamic'] = $this->formate_lcc_meal_request_details($MealsDetails);
                }
                //Seat Details
                if (isset($v['SeatId']) == true && valid_array($v['SeatId']) == true) {
                    $SeatDetails = $this->formate_lcc_seat_request_details($SeatDetails);
                    if (valid_array($SeatDetails) == true) {
                        $passenger_data [$k] ['SeatDynamic'] = $SeatDetails;
                    }
                }
            } else if ($IsLCC == false) {//Non-LCC FLIGHT
                if (isset($v['MealId']) == true && valid_array($v['MealId']) == true) {
                    $MealsDetails = $v['MealId'];
                } else {
                    $MealsDetails = array();
                }
                if (isset($v['SeatId']) == true && valid_array($v['SeatId']) == true) {
                    $SeatDetails = $v['SeatId'];
                } else {
                    $SeatDetails = array();
                }
                //Meals Details
                $MealsDetails = $this->formate_non_lcc_meal_request_details($MealsDetails);
                if (valid_array($MealsDetails) == true) {
                    $passenger_data [$k] ['Meal'] = $MealsDetails;
                }
                //Seat Details
                $SeatDetails = $this->formate_non_lcc_seat_request_details($SeatDetails);
                if (valid_array($SeatDetails) == true) {
                    $passenger_data [$k] ['Seat'] = $SeatDetails;
                }
            }
        }
        return $passenger_data;
    }

    /**
     * Formats the Fare Request For Passenger-wise
     *
     * @param unknown_type $passenger_token
     */
    private function assign_booking_passenger_fare_breakdown($passenger_token) {
        $passenger_token = force_multple_data_format($passenger_token);
        $Fare = array();
        foreach ($passenger_token as $k => $v) {
            $Fare [$v ['PassengerType']] ['BaseFare'] = ($v ['BaseFare'] / $v ['PassengerCount']);
            $Fare [$v ['PassengerType']] ['Tax'] = ($v ['Tax'] / $v ['PassengerCount']);
            //$Fare [$v ['PassengerType']] ['TransactionFee'] = ($v ['TransactionFee'] / $v ['PassengerCount']);
            $Fare [$v ['PassengerType']] ['TransactionFee'] = 0;
            $Fare [$v ['PassengerType']] ['YQTax'] = ($v ['YQTax'] / $v ['PassengerCount']);
            $Fare [$v ['PassengerType']] ['AdditionalTxnFeeOfrd'] = ($v ['AdditionalTxnFeeOfrd'] / $v ['PassengerCount']);
            $Fare [$v ['PassengerType']] ['AdditionalTxnFeePub'] = ($v ['AdditionalTxnFeePub'] / $v ['PassengerCount']);
            //$Fare [$v ['PassengerType']] ['AirTransFee'] = ($v ['AirTransFee'] / $v ['PassengerCount']);
            $Fare [$v ['PassengerType']] ['AirTransFee'] = 0;
        }
        return $Fare;
    }

    /**
     * Baggage Details For LCC Flights
     */
    private function formate_lcc_baggage_request_details($BaggageDetails) {
        $baggage = array();
        if (valid_array($BaggageDetails) == true) {
            foreach ($BaggageDetails as $bag_k => $bag_v) {
                if (empty($bag_v) == false) {
                    $baggage_data = Common_Flight::read_record($bag_v);
                    if (valid_array($baggage_data) == true) {
                        $baggage_data = json_decode($baggage_data[0], true);
                        $temp_baggage = array_values(unserialized_data($baggage_data['BaggageId']));
                        $baggage[$bag_k] = $temp_baggage[0];
                    }
                }
            }
        } else {
            $baggage ['WayType'] = 0; //NotSet = 0;Segment = 1;FullJourney = 2
            $baggage ['Code'] = ''; //Baggage code
            $baggage ['Description'] = ''; //[NotSet = 0;Included = 1;Direct = 2;Imported = 3;UpGrade = 4;ImportedUpgrade = 5
            $baggage ['Weight'] = '';
            $baggage ['Currency'] = '';
            $baggage ['Price'] = '';
            $baggage ['Origin'] = '';
            $baggage ['Destination'] = '';
        }
        return $baggage;
    }

    /**
     * Meal Details For LCC Flights
     */
    private function formate_lcc_meal_request_details($MealDetails) {
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
        } else {
            $meal ['WayType'] = 0; //NotSet = 0;Segment = 1;FullJourney = 2
            $meal ['Code'] = ''; //Meal code
            $meal ['Description'] = ''; //[NotSet = 0;Included = 1;Direct = 2;Imported = 3;UpGrade = 4;ImportedUpgrade = 5
            $meal ['AirlineDescription'] = '';
            $meal ['Quantity'] = '';
            $meal ['Price'] = '';
            $meal ['Currency'] = '';
            $meal ['Origin'] = '';
            $meal ['Destination'] = '';
        }
        return $meal;
    }

    /**
     * Seat Details For LCC Flights
     */
    private function formate_lcc_seat_request_details($SeatDetails) {
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
        return $seat;
    }

    /**
     * Meal Details For Non-LCC Flights
     */
    private function formate_non_lcc_meal_request_details($MealDetails) {
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
                        $meal = $temp_meal[0];
                    }
                }
            }
        }
        return $meal;
    }

    /**
     * Seat Details For Non-LCC Flights
     */
    private function formate_non_lcc_seat_request_details($SeatDetails) {
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
                        $seat = $temp_seat[0];
                    }
                }
            }
        }
        return $seat;
    }

    /**
     * Validates the mobile number
     * @param unknown_type $mobile_number
     */
    private function validate_mobile_number($mobile_number) {
        $mobile_number = trim($mobile_number);
        $mobile_number = ltrim($mobile_number, '0');
        if (strlen($mobile_number) < 10) {
            $mobile_number_length = strlen($mobile_number);
            $required_extra_number_lengths = (10) - $mobile_number_length;
            $extra_numbers = str_repeat('0', $required_extra_number_lengths);
            $mobile_number = $extra_numbers . '' . $mobile_number;
            //$mobile_number =  $mobile_number.''.$extra_numbers;
        }
        return $mobile_number;
    }

    /**
     * Formates Search Response
     * Enter description here ...
     * @param unknown_type $search_result
     * @param unknown_type $search_data
     */
    function format_search_data_response($search_result, $search_data) {
        $trip_type = isset($search_data ['is_domestic']) && !empty($search_data ['is_domestic']) ? 'domestic' : 'international';
        $Results = $search_result ['Response'] ['Results'];
        $flight_list = array();
        $TraceId = $search_result['Response']['TraceId'];
        foreach ($Results as $result_k => $result_v) {
            foreach ($result_v as $journey_array_k => $journey_array_v) {
                $flight_details = array();
                $key = array();
                $key['key'][$journey_array_k]['booking_source'] = $this->booking_source;
                $key['key'][$journey_array_k]['TraceId'] = $TraceId;
                $flight_details = $this->flight_segment_summary($journey_array_v, $journey_array_k, $key);
                if (valid_array($flight_details)) {
                    foreach ($flight_details as $f__key => $flight_detail) {
                        $flight_list['JourneyList'] [$result_k] [] = $flight_detail;
                    }
                }
            }
        }
        $response ['FlightDataList'] = $flight_list;
        return $response;
    }

    /**
     * Get flight details only
     *
     * @param array $segment
     */
    private function flight_segment_summary($journey_array, $journey_number, & $key, $cache_fare_object = false) {
        $summary = array();
        $flight_details = array();
        $price_details = array();
        $final_flight = array();
        // Loop on data to form details array
        $details = array();
        $flightNumberList = array();
        $segment_intl = array();
        $FareSellKey = array();
        $fare_object = array();
        $core_fare_object = $journey_array['Fare'];
        $core_fare_break_down_object = $journey_array['FareBreakdown'];
        $itineray_price['Fare'] = $core_fare_object;
        $itineray_price['FareBreakdown'] = $core_fare_break_down_object;
        $segments = $journey_array['Segments'];
        $IsRefundable = $journey_array['IsRefundable'];
        $ResultIndex = $journey_array['ResultIndex'];
        $IsLCC = $journey_array['IsLCC'];
        $AirlineRemark = $journey_array['AirlineRemark'];

        foreach ($segments as $k => $v) {
            // legs Loop
            $legs = force_multple_data_format($v);
            $is_leg = true;
            $attr = array();
            foreach ($legs as $l_k => $l_v) {
                $origin_code = $l_v ['Origin']['Airport']['AirportCode'];
                $destination_code = $l_v ['Destination']['Airport']['AirportCode'];
                $departure_dt = db_current_datetime($l_v ['Origin']['DepTime']);
                $arrival_dt = db_current_datetime($l_v ['Destination']['ArrTime']);
                $stop_over = '';
                if ($l_v['StopOver'] == 1) {
                    $flight_airport = $this->CI->flight_model->get_airport_city_name($l_v['StopPoint']);
                    if (isset($flight_airport) && valid_array($flight_airport)) {
                        // echo $l_v['StopPointArrivalTime'];
                        $arrival_time = str_replace('T', ' ', $l_v['StopPointArrivalTime']);

                        $arrival_time = date_create($arrival_time);

                        $departure_time = str_replace('T', ' ', $l_v['StopPointDepartureTime']);
                        $departure_time = date_create($departure_time);
                        $duration = date_diff($departure_time, $arrival_time);

                        $stop_over = 'This Flight has a technical stop at ' . $flight_airport->airport_city . '(' . $flight_airport->airport_code . ') for ' . $duration->format('%h') . ' hour ' . $duration->format('%i') . ' minutes';
                    } else {
                        if (isset($l_v['StopPointDepartureTime']) && isset($l_v['StopPointArrivalTime']) && $l_v['StopPoint']) {
                            $arrival_time = str_replace('T', ' ', $l_v['StopPointArrivalTime']);

                            $arrival_time = date_create($arrival_time);

                            $departure_time = str_replace('T', ' ', $l_v['StopPointDepartureTime']);
                            $departure_time = date_create($departure_time);
                            $duration = date_diff($departure_time, $arrival_time);
                            $stop_over = 'This Flight has a technical stop at ' . $l_v['StopPoint'] . ' ' . $duration->format('%h') . ' hour ' . $duration->format('%i') . ' minutes';
                        } else {
                            $stop_over = 'This Flight has a one technical stop';
                        }
                    }
                }

                $attr['Baggage'] = @$l_v['Baggage'];
                if (isset($l_v['CabinBaggage']) && empty($l_v['CabinBaggage']) == false) {
                    $attr['CabinBaggage'] = @$l_v['CabinBaggage'];
                } else {
                    $carrier = $l_v['Airline']['AirlineCode'];
                    $hand_baggage = $this->CI->custom_db->single_table_records('airline_baggage', '*', array('code' => $carrier));
                    if ($hand_baggage['status'] == SUCCESS_STATUS) {
                        $attr['CabinBaggage'] = $hand_baggage['data'][0]['weight'] . ' Kg';
                    } else {
                        $attr['CabinBaggage'] = '0 Kg';
                    }
                }
                if (isset($l_v['NoOfSeatAvailable']) == true) {
                    $attr['AvailableSeats'] = $l_v['NoOfSeatAvailable'];
                }
                if (isset($l_v['AirlinePNR']) == true) {
                    $attr['AirlinePNR'] = $l_v['AirlinePNR']; //In Ticket Method and GetBooking Details We will get AirlinePNR
                }
                $no_of_stops = 0;
                $cabin_class = $l_v['Airline']['FareClass'];
                $operator_code = $l_v['Airline']['AirlineCode'];

                $blocked_list = $this->CI->custom_db->single_table_records('blocked_airline', '*', array('airline_list' => $operator_code, 'booking_source' => $this->booking_source));
                # For Blocking Airline from Specific API
                if ($blocked_list['status'] == true) {
                    return false;
                }

                /* # For Blocking Airline from Specific API
                  if($operator_code=="6E" || $operator_code=="I5" || $operator_code=="AK" || $operator_code=="FD")
                  {
                  return false;
                  } */


                $operator_name = $l_v['Airline']['AirlineName'];
                if(trim($operator_name) == trim($operator_code)){
                    $operator_name = $this->get_airline_name($operator_code);
                }
                $flight_number = $l_v['Airline']['FlightNumber'];
                $origin_terminal = $l_v['Origin']['Airport']['Terminal'];
                $destination_terminal = $l_v['Destination']['Airport']['Terminal'];
                $total_duration = $l_v['Duration'];
                $details[$k][] = $this->format_summary_array($journey_number, $origin_code, $destination_code, $departure_dt, $arrival_dt, $operator_code, $operator_name, $flight_number, $no_of_stops, $cabin_class, '', '', $total_duration, $is_leg, $attr, $stop_over, $origin_terminal, $destination_terminal);
                $flightNumberList [] = $l_v['Airline']['FlightNumber'];
                $is_leg = false;
            }
        }
        //Fare
        $price = $this->format_itineray_price_details($itineray_price);


        $flight_details ['Details'] = $details;
        $flight_ar ['FlightDetails'] = $flight_details;
        $flight_ar ['Price'] = $price;
        if ($cache_fare_object == true) {
            $key ['key'] [$journey_number]['Fare'] = $core_fare_object;
            $key ['key'] [$journey_number]['FareBreakdown'] = $core_fare_break_down_object;
        }
        $key ['key'] [$journey_number]['ResultIndex'] = $ResultIndex;
        $key ['key'] [$journey_number]['IsLCC'] = $IsLCC;
        $flight_ar ['ResultToken'] = serialized_data($key['key']);
        if ($journey_array['IsGSTMandatory'] == true) {
            $flight_ar ['IsGSTMandatory'] = true;
        }
        $is_refundable = $IsRefundable;
        $flight_ar ['Attr']['IsRefundable'] = $is_refundable;
        $flight_ar ['Attr']['AirlineRemark'] = $AirlineRemark;
        $final_flight[] = $flight_ar;
        $response = $final_flight;
        return $response;
    }

    /**
     * Formates Itineray Price Details
     * @param unknown_type $itineray_price
     */
    private function format_itineray_price_details($itineray_price, $ticket_details_fare = false) {
        if ($ticket_details_fare) {
            $itineray_price['FareBreakdown'] = $this->format_ticket_detail_fare_breakdown($itineray_price['FareBreakdown']);
        }
        $price = array();
        $passenger_breakup = array();
        $OtherCharges = $itineray_price['Fare']['OtherCharges'];
        $currency_code = $itineray_price['Fare']['Currency'];
        $base_fare = $itineray_price['Fare']['BaseFare'];
        $tax = ($itineray_price['Fare']['Tax'] + $OtherCharges); //FIXME: check where to add Other Charges
        $total_fare = $itineray_price['Fare']['PublishedFare'];
        $pax_fare_breakdown = $itineray_price['FareBreakdown'];
        $pax_wise_other_charges = $this->pax_wise_othercharges($pax_fare_breakdown, $OtherCharges);
        foreach ($pax_fare_breakdown as $k => $v) {
            $pax_type = $this->get_passenger_type($v['PassengerType']);
            $pax_count = $v['PassengerCount'];
            $pax_base_fare = $v['BaseFare'];
            $pax_tax = ($v['Tax'] + ($pax_wise_other_charges * $pax_count)); //FIXME: check where to add Other Charges
            $pax_total_fare = ($pax_base_fare + $pax_tax);

            $passenger_breakup[$pax_type]['BasePrice'] = $pax_base_fare;
            $passenger_breakup[$pax_type]['Tax'] = $pax_tax;
            $passenger_breakup[$pax_type]['TotalPrice'] = $pax_total_fare;
            $passenger_breakup[$pax_type]['PassengerCount'] = $pax_count;
        }
        $agent_commission = ($itineray_price['Fare']['PublishedFare'] - $itineray_price['Fare']['OfferedFare']);
        $AgentCommission = roundoff_number($agent_commission, 2);
        $AgentTdsOnCommision = roundoff_number((($agent_commission * 5) / 100), 2); //Calculate it from currency library
        //Assigning to Fare Object
        $price = $this->get_price_object();
        $price['Currency'] = $currency_code;
        $price['TotalDisplayFare'] = $total_fare;
        $price['PriceBreakup']['Tax'] = $tax;
        $price['PriceBreakup']['BasicFare'] = $base_fare;
        $price['PriceBreakup']['AgentCommission'] = $AgentCommission;
        $price['PriceBreakup']['AgentTdsOnCommision'] = $AgentTdsOnCommision;

        $price['PassengerBreakup'] = $passenger_breakup;

        return $price;
    }

    /**
     * Formates Ticket Fare Details
     * @param unknown_type $FareBreakdown
     */
    private function format_ticket_detail_fare_breakdown($FareBreakdown) {
        $pax_type = array_column($FareBreakdown, 'PaxType');
        $pax_type = array_count_values($pax_type);

        $formatted_fare_breakdown = array();
        $stored_pax_type = array();
        foreach ($FareBreakdown as $k => $v) {
            $PassengerType = $v['PaxType'];
            if (in_array($PassengerType, $stored_pax_type) == false) {
                array_push($stored_pax_type, $PassengerType);
                $PassengerCount = $pax_type[$PassengerType];

                $formatted_fare_breakdown[$k]['BaseFare'] = ($v['Fare']['BaseFare'] * $PassengerCount);
                $formatted_fare_breakdown[$k]['Tax'] = ($v['Fare']['Tax'] * $PassengerCount);
                $formatted_fare_breakdown[$k]['PassengerType'] = $PassengerType;
                $formatted_fare_breakdown[$k]['PassengerCount'] = $PassengerCount;
            }
        }
        return $formatted_fare_breakdown;
    }

    /**
     * Formates Calendar Fare data
     * @param unknown_type $calendar_fare_data
     */
    public function get_calendar_fare($calendar_fare_raw_data) {
        $response ['status'] = FAILURE_STATUS; // Status Of Operation
        $response ['message'] = ''; // Message to be returned
        $response ['data'] = array(); // Data to be returned
        $api_response = json_decode($calendar_fare_raw_data, true);
        if (valid_array($api_response) == true && isset($api_response['Response']) == true && $api_response['Response']['ResponseStatus'] == SUCCESS_STATUS) {

            $temp_response = $api_response['Response'];
            $calendar_fare_details = array();
            $calendar_fare_details['TraceId'] = $temp_response['TraceId'];
            $calendar_fare_details['Origin'] = $temp_response['Origin'];
            $calendar_fare_details['Destination'] = $temp_response['Destination'];
            $calendar_fare_details['CalendarFareDetails'] = $temp_response['SearchResults'];
            $response ['data'] = $calendar_fare_details;
            $response['status'] = SUCCESS_STATUS;
        } else {
            $response ['status'] = FAILURE_STATUS;
        }
        return $response;
    }

    /**
     * Formates Fare Rule Response
     * @param unknown_type $fare_rule_response
     */
    // Hided by Balu

    function format_fare_rule_response1($fare_rule_response) {
        $fare_rules = array();
        foreach ($fare_rule_response['Response']['FareRules'] as $k => $v) {
            $fare_rules[$k]['Origin'] = $v['Origin'];
            $fare_rules[$k]['Destination'] = $v['Destination'];
            $fare_rules[$k]['Airline'] = $v['Airline'];
            $fare_rules[$k]['FareRules'] = $v['FareRuleDetail'];
        }
        return $fare_rules;
    }

    function format_fare_rule_response($fare_rule_response) {
        $fare_rules = array();
        foreach ($fare_rule_response['Response']['FareRules'] as $k => $v) {
            $fare_rules[$k]['Origin'] = $v['Origin'];
            $fare_rules[$k]['Destination'] = $v['Destination'];
            $fare_rules[$k]['Airline'] = $v['Airline'];


            $domain_base_currency = domain_base_currency();
            //$domain_base_currency="USD";

            $currency_obj = new Currency(array('from' => get_application_default_currency(), 'to' => $domain_base_currency));

            $FareRulesArray = explode(" ", $v['FareRuleDetail']);

            foreach ($FareRulesArray as $key => $value) {

                if (trim($FareRulesArray[$key])=="INR" || trim($FareRulesArray[$key])=="INR.") {

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
            //$fare_rules[$k]['FareRules'] = $v['FareRuleDetail'];
        }
        return $fare_rules;
    }

    /**
     * Format Update Farequote Response
     * @param unknown_type $update_fare_quote_response
     */
    function format_update_fare_quote_response($update_fare_quote_response, $search_id, $search_price) {
        $search_data = $this->CI->flight_model->get_safe_search_data($search_id);
        $is_domestic = $search_data['data']['is_domestic'];

        $Results = force_multple_data_format($update_fare_quote_response['Results']);
        $TraceId = $update_fare_quote_response['TraceId'];
        $update_fare_quote = array();
        foreach ($Results as $journey_array_k => $journey_array_v) {
            $flight_details = array();
            $key = array();
            $key['key'][$journey_array_k]['booking_source'] = $this->booking_source;
            $key['key'][$journey_array_k]['TraceId'] = $TraceId;
            if ($Results[0]['IsLCC'] == false) {
                // if ($is_domestic == false && $Results[0]['IsLCC'] == false) {//only for international booking, enable hold option
                $hold_ticket = true;
            } else {
                $hold_ticket = false;
            }
            $price_changed = false;
            $flight_details = $this->flight_segment_summary($journey_array_v, $journey_array_k, $key, true);
            if (valid_array($flight_details)) {
                foreach ($flight_details as $f__key => $flight_detail) {
                    $fare_quote_price = $flight_detail['Price']['TotalDisplayFare'];
                    $diff_price = $search_price-$fare_quote_price;
                    if($diff_price < -2 || $diff_price > 2){
                        $price_changed = true;
                    }
                    // debug($flight_detail);exit;
                    $flight_detail['HoldTicket'] = $hold_ticket;
                    $flight_detail['Attr']['IsPriceChanged'] = $price_changed;
                    $update_fare_quote['JourneyList'] [$journey_array_k] [] = $flight_detail;
                }
            }
        }
        return $update_fare_quote;
    }

    /**
     * Calculates Passenger-Wise Other Charges
     *
     * @param unknown_type $pax_fare_breakdown
     * @param unknown_type $OtherCharges
     */
    private function pax_wise_othercharges($pax_fare_breakdown, $OtherCharges) {
        $pax_wise_other_charges = 0;
        $total_pax_count = 0;
        foreach ($pax_fare_breakdown as $k => $v) {
            $total_pax_count += $v['PassengerCount'];
        }
        $pax_wise_other_charges = round(($OtherCharges / $total_pax_count), 3);
        return $pax_wise_other_charges;
    }

    /**
     * Rerurns Passenger Type
     * @param unknown_type $PassengerType
     */
    private function get_passenger_type($PassengerType) {
        $type = '';
        switch ($PassengerType) {
            case 1;
                $type = 'ADT';
                break;
            case 2;
                $type = 'CHD';
                break;
            case 3;
                $type = 'INF';
                break;
        }
        return $type;
    }

    /**
     * Returns Airline Name based on airline code
     * @param unknown_type $airline_code
     */
    private function get_airline_name($airline_code) {
        $airline_name = '';
        $airline_code_list = $this->CI->db_cache_api->get_airline_code_list();
        if (isset($airline_code_list[$airline_code])) {
            $airline_name = $airline_code_list[$airline_code];
        }
        return $airline_name;
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
     * check if the search RS is valid or not
     * @param array $search_result
     * search result RS to be validated
     */
    private function valid_search_result($search_result) {
        if (valid_array($search_result) == true && isset($search_result['Response']) == true && $search_result['Response']['ResponseStatus'] == true && isset($search_result['Response']['Results']) == true && valid_array($search_result['Response']['Results']) == true) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get Cabin Class Search ID
     * @param unknown_type $CabinClass
     */
    private function get_cabin_class_id($CabinClass) {
        switch (strtolower($CabinClass)) {//
            case 'all';
                $FlightCabinClass = 1;
                break;
            case 'economy';
                $FlightCabinClass = 2;
                break;
            case 'premiumeconomy';
                $FlightCabinClass = 3;
                break;
            case 'business';
                $FlightCabinClass = 4;
                break;
            case 'premiumbusiness';
                $FlightCabinClass = 5;
                break;
            case 'first';
                $FlightCabinClass = 6;
                break;
        }
        return $FlightCabinClass;
    }

    /*
     * Returns Journey Type
     */

    private function get_journey_type($journey_type) {
        $type = '';
        switch (strtoupper($journey_type)) {
            case 'ONEWAY';
                $type = 1;
                break;
            case 'RETURN';
                $type = 2;
                break;
            case 'MULTICITY';
                $type = 3;
                break;
            case 'ADVANCEDSEARCH';
                $type = 4;
                break;
            case 'SPECIALRETURN';
                $type = 5;
                break;
        }
        return $type;
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

        $curl_data = array();
        $curl_data['booking_source'] = $this->booking_source;
        $curl_data['request'] = $request;
        $curl_data['url'] = $url;
        $curl_data['header'] = array(
            'Content-Type:application/json',
            'Accept-Encoding:gzip, deflate'
        );
        $data['data'] = $curl_data;
        return $data;
    }

    /**
     * Process API Request
     * @param unknown_type $request
     * @param unknown_type $url
     */
    function process_request($request, $url, $remarks = '') {
        $insert_id = $this->CI->api_model->store_api_request($url, $request, $remarks);
        $insert_id = intval(@$insert_id['insert_id']);

        try {
            $cs = curl_init();
            curl_setopt($cs, CURLOPT_URL, $url);
            curl_setopt($cs, CURLOPT_TIMEOUT, 180);
            curl_setopt($cs, CURLOPT_HEADER, 0);
            curl_setopt($cs, CURLOPT_RETURNTRANSFER, 1);
            if (empty($request) == false) {
                curl_setopt($cs, CURLOPT_POST, 1);
                curl_setopt($cs, CURLOPT_POSTFIELDS, $request);
            }
            curl_setopt($cs, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($cs, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($cs, CURLOPT_SSLVERSION, 3);
            curl_setopt($cs, CURLOPT_FOLLOWLOCATION, true);

            $header = array(
                'Content-Type:application/json',
                'Accept-Encoding:gzip, deflate'
            );
            curl_setopt($cs, CURLOPT_HTTPHEADER, $header);
            curl_setopt($cs, CURLOPT_ENCODING, "gzip");
            $response = curl_exec($cs);
            //  debug($response);exit;
            $error = curl_getinfo($cs);
        } catch (Exception $e) {
            $response = 'No Response Recieved From API';
        }
       
        //Update the API Response
        $this->CI->api_model->update_api_response($response, $insert_id);
         // echo $insert_id;exit;
        $error = curl_getinfo($cs, CURLINFO_HTTP_CODE);
        curl_close($cs);
        return $response;
    }
    //Get TBO Balance
    public function get_agent_balance(){
        $BalanceRequest['ClientId'] = $this->config['ClientId'];
        $BalanceRequest['TokenAgencyId'] = "41893";
        $BalanceRequest['TokenMemberId'] = "45702";
        $BalanceRequest['TokenId'] = $this->api_session_id;
        $BalanceRequest['EndUserIp'] = $this->end_user_ip;
        $request= json_encode($BalanceRequest);
        $url = $this->config['AuthenticationUrl'] . 'GetAgencyBalance';
        $balance_response = $this->process_request($request, $url, "Balance(TBO)");
        $balance_response = json_decode($balance_response, true);
        // debug($balance_response);exit;
        $response['status'] = FAILURE_STATUS;
        if($balance_response['Status'] == SUCCESS_STATUS){
            $response['status'] = SUCCESS_STATUS;
            $response['data']['balance'] = ($balance_response['CashBalance'] + $balance_response['CreditBalance']);
        }
        // debug($response);exit;
       return $response;
    }

}
