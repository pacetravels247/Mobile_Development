<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once BASEPATH . 'libraries/Common_Api_Grind.php';

/**
 *
 * @package    Provab
 * @subpackage API
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V1
 */
class Travelport extends Common_Api_Grind {

    protected $ClientId;
    protected $UserName;
    protected $Password;
    protected $system;   //test/live   -   System to which we have to connect in web service
    protected $Url;
    private $service_url;
    private $TokenId; //    Token ID that needs to be echoed back in every subsequent request
    protected $ins_token_file;
    private $CI;
    private $commission = array();
    var $master_search_data;
    var $search_hash; //search

    public function __construct() {
        parent::__construct();
        $this->CI = &get_instance();
        $this->CI->load->library('Api_Interface');
        $this->CI->load->model('flight_model');
        $this->set_api_credentials();
    }

    private function set_api_credentials() {
        // echo $this->CI->flight_engine_system;exit;
    /*    $flight_engine_system = $this->CI->flight_engine_system;
        $this->system = $flight_engine_system;
        $user_name = $this->CI->flight_engine_system. '_username';
        $password = $this->CI->flight_engine_system. '_password';
        $this->UserName = $this->CI->$user_name;
        $this->Password = $this->CI->$password;
        $this->Url =  $this->CI->flight_url;
        $this->ClientId = $this->CI->domain_key;*/
        

        $flight_engine_system = $this->CI->config->item('flight_engine_system');
        $this->system = $flight_engine_system;
        $this->details = $this->CI->config->item ('travelport_flight_'.$flight_engine_system);
        /*debug($this->details); exit;
        $this->Url = $this->details ['api_url'];
        $this->UserName = $this->details ['test_username'];
        $this->Password = $this->details ['test_password'];
        $this->DomainKey = $this->details ['domain_key'];
        $this->ClientId = $this->details ['domain_key'];*/

        $this->Url = 'http://192.168.0.30/quickdataprovider/webservices/index.php/flight/service/';
        $this->UserName = 'test245274';
        $this->Password = 'test@245';
        $this->DomainKey = CURRENT_TRAVELPORT_DOMAIN_KEY;
        $this->ClientId = CURRENT_TRAVELPORT_DOMAIN_KEY;
       
        //$this->UserName = 'test';
        //$this->Password = 'password'; // miles@123 for b2b
    }

   /**/

    function credentials($service) {
        switch ($service) {
            case 'Search':
                $this->service_url = $this->Url . 'Search';
                break;
            case 'FareRule':
                $this->service_url = $this->Url . 'FareRule';
                break;
            case 'UpdateFareQuote':
                $this->service_url = $this->Url . 'UpdateFareQuote';
                break;
            case 'ExtraServices':
                $this->service_url = $this->Url . 'ExtraServices';
                break;
            case 'HoldTicket':
                $this->service_url = $this->Url . 'HoldTicket';
                break;
            case 'CommitBooking':
                $this->service_url = $this->Url . 'CommitBooking';
                break;
            case 'IssueHoldTicket':
                $this->service_url = $this->Url . 'IssueHoldTicket';
                break;
            case 'CancelBooking':
                $this->service_url = $this->Url . 'CancelBooking';
                break;
            case 'GetCalendarFare':
                $this->service_url = $this->Url . 'GetCalendarFare';
                break;
            case 'UpdateCalendarFareOfDay':
                $this->service_url = $this->Url . 'UpdateCalendarFareOfDay';
                break;
            case 'BookingDetails':
                $this->service_url = $this->Url . 'BookingDetails';
                break;
            case 'TicketRefundDetails':
                $this->service_url = $this->Url . 'TicketRefundDetails';
                break;
        }
    }

    /*
     *
     * Convert Object To Array
     *
     */

    public function objectToArray($d) {
        if (is_object($d)) {
            $d = get_object_vars($d);
        }

        if (is_array($d)) {
            return array_map(array($this, 'objectToArray'), $d);
        } else {
            return $d;
        }
    }

    /**
     *  Balu A
     *
     * TBO auth token will be returned
     */
    public function get_authenticate_token() {
        return $GLOBALS['CI']->session->userdata('tb_auth_token');
    }

    /**
     * request Header
     */
    private function get_header() {
        $response['UserName'] = $this->UserName;
        $response['Password'] = $this->Password;
        $response['DomainKey'] = $this->DomainKey;
        $response['system'] = $this->system;
        return $response;
    }

    /**
     * get Flight search request details
     * @param array $search_params data to be used while searching of flight
     */
    function flight_search_request($search_params) {

        $response['status'] = SUCCESS_STATUS;
        $response['data'] = array();
        /** Request to be formed for search * */
        $this->credentials('Search');
        $request_params = array();
        //Converting to an array
        $search_params['from'] = (is_array($search_params['from']) ? $search_params['from'] : array($search_params['from']));
        $search_params['to'] = (is_array($search_params['to']) ? $search_params['to'] : array($search_params['to']));
        $search_params['depature'] = (is_array($search_params['depature']) ? $search_params['depature'] : array($search_params['depature']));
        $search_params['return'] = (is_array($search_params['return']) ? $search_params['return'] : array($search_params['return']));
        $segments = array();
        for ($i = 0; $i < count($search_params['from']); $i++) {
            $segments[$i]['Origin'] = $search_params['from'][$i];
            $segments[$i]['Destination'] = $search_params['to'][$i];
            $segments[$i]['DepartureDate'] = $search_params['depature'][$i];
            if ($search_params['type'] == 'Return') {
                $segments[$i]['ReturnDate'] = $search_params['return'][$i];
            }
        }
        $request_params['AdultCount'] = $search_params['adult'];
        $request_params['ChildCount'] = $search_params['child'];
        $request_params['InfantCount'] = $search_params['infant'];
        $request_params['JourneyType'] = $search_params['type'];
        $request_params['PreferredAirlines'] = array($search_params['carrier']);
        $request_params['CabinClass'] = $search_params['v_class'];

        $request_params['Segments'] = $segments;
        //debug($request_params);exit;
        $response['data']['request'] = json_encode($request_params);
        $response['data']['service_url'] = $this->service_url;

        return $response;
    }

    /**
     * get fare rules request
     * @param $data_key     data to be used in the result index - comes from search result
     * @param $search_key   session id of the search  -  session identifies each search
     */
    function fare_details_request($data_key, $search_key) {
        $response['status'] = SUCCESS_STATUS;
        $response['data'] = array();
        $request_params = array();
        $this->credentials('FareRule');
        if (empty($data_key) == false) {
            $request_params['ResultToken'] = $search_key;
        } else {
            $response['status'] = FAILURE_STATUS;
        }
        $response['data']['request'] = json_encode($request_params);
        $response['data']['service_url'] = $this->service_url;
        return $response;
    }

    /**
     * get fare quote request
     * @param $data_key     data to be used in the result index - comes from search result
     * @param $search_key   session id of the search  -  session identifies each search
     */
    function fare_quote_request($data_key, $search_key) {
        $response['status'] = SUCCESS_STATUS;
        $response['data'] = array();
        $request_params = array();
        $this->credentials('UpdateFareQuote');
        if (empty($data_key) == false) {
            $request_params['ResultToken'] = $search_key;
        } else {
            $response['status'] = FAILURE_STATUS;
        }
        $response['data']['request'] = json_encode($request_params);
        $response['data']['service_url'] = $this->service_url;
        return $response;
    }

    /**
     * extra service request
     * @param $data_key     data to be used in the result index - comes from search result
     * @param $search_key   session id of the search  -  session identifies each search
     */
    function extra_services_request($data_key, $search_key) {
        $response['status'] = SUCCESS_STATUS;
        $response['data'] = array();
        $request_params = array();
        $this->credentials('ExtraServices');
        if (empty($data_key) == false) {
            $request_params['ResultToken'] = $search_key;
        } else {
            $response['status'] = FAILURE_STATUS;
        }
        $response['data']['request'] = json_encode($request_params);
        $response['data']['service_url'] = $this->service_url;
        return $response;
    }

    /**
     * Create Booking Request
     * @param array $booking_params
     */
    private function commit_booking_request($booking_params, $app_reference) {
        $response['status'] = SUCCESS_STATUS;
        $response['data'] = array();
        $request_params = array();
        $this->credentials('CommitBooking');
        $request_params['AppReference'] = trim($app_reference);
        $request_params['SequenceNumber'] = $booking_params['SequenceNumber'];
        $request_params['ResultToken'] = $booking_params['ProvabAuthKey'];
        $request_params['Passengers'] = $booking_params['Passenger'];
        //GST
        if(isset($booking_params['GST'])){
            $request_params['GST']['Gstnumber'] = trim(@$booking_params['GST']['gst_numebr']);
            $request_params['GST']['GstComapnyName'] = trim(@$booking_params['GST']['gst_company_name']);
            $request_params['GST']['GstEmail'] = trim(@$booking_params['GST']['gst_email']);
            $request_params['GST']['GstPhone'] = trim(@$booking_params['GST']['gst_phone']);
            $request_params['GST']['GstState'] = trim(@$booking_params['GST']['gst_state']);
            $request_params['GST']['GstAddress'] = trim(@$booking_params['GST']['gst_address']);
        }
        $response['data']['request'] = json_encode($request_params);

        $response['data']['service_url'] = $this->service_url;

        return $response;
    }

    /**
     * Hold Booking Request
     * @param array $booking_params
     */
    private function hold_booking_request($booking_params, $app_reference) {
        $response['status'] = SUCCESS_STATUS;
        $response['data'] = array();
        $request_params = array();
        $this->credentials('HoldTicket');
        $request_params['AppReference'] = trim($app_reference);
        $request_params['SequenceNumber'] = $booking_params['SequenceNumber'];
        $request_params['ResultToken'] = $booking_params['ProvabAuthKey'];
        $request_params['Passengers'] = $booking_params['Passenger'];
        $response['data']['request'] = json_encode($request_params);
        $response['data']['service_url'] = $this->service_url;
        return $response;
    }

    /**
     * Balu A- Cancellation Request
     * Request Format For CancelBooking Method
     * @param $cancell_request_params
     */
    function cancel_booking_request($cancell_request_params, $app_reference) {

        $response['status'] = SUCCESS_STATUS;
        $response['data'] = array();
        $this->credentials('CancelBooking');
        $request_params = array();
        $request_params['AppReference'] = $app_reference;
        $request_params['SequenceNumber'] = $cancell_request_params['SequenceNumber'];
        $request_params['BookingId'] = $cancell_request_params['BookingId'];
        $request_params['PNR'] = $cancell_request_params['PNR'];
        $request_params['TicketId'] = $cancell_request_params['TicketId'];
        $request_params['IsFullBookingCancel'] = $cancell_request_params['IsFullBookingCancel'];
        $response['data']['request'] = json_encode($request_params);
        $response['data']['service_url'] = $this->service_url;
        return $response;
    }

    /**
     * Balu A
     * Request For getting cancellation Refund details
     * @param $request_data
     */
    function ticket_refund_details_request($request_data) {
        $response['status'] = SUCCESS_STATUS;
        $response['data'] = array();
        $this->credentials('TicketRefundDetails');
        $request_params = array();
        $request_params['AppReference'] = $request_data['AppReference'];
        $request_params['SequenceNumber'] = $request_data['SequenceNumber'];
        $request_params['BookingId'] = $request_data['BookingId'];
        $request_params['PNR'] = $request_data['PNR'];
        $request_params['TicketId'] = $request_data['TicketId'];
        $request_params['ChangeRequestId'] = $request_data['ChangeRequestId'];

        $response['data']['request'] = json_encode($request_params);
        $response['data']['service_url'] = $this->service_url;
        return $response;
    }

    /**
     * Get Booking Details Request
     * @param string $book_id
     * @param string $pnr
     * @param string $booking_source
     */
    function booking_details_request($book_id, $pnr) {
        $response['status'] = SUCCESS_STATUS;
        $response['data'] = array();
        $request_params = array();
        $this->credentials('GetBookingDetails');
        $request_params['BookingId'] = $book_id;
        $request_params['PNR'] = $pnr;
        $response['data']['request'] = json_encode($request_params);
        $response['data']['service_url'] = $this->service_url;
        return $response;
    }

    //****************************************************************************
    /**
     * Fare calendar request
     * @param array $search_params
     */
    function calendar_fare_request($search_params) {
        $response['status'] = SUCCESS_STATUS;
        $response['data'] = array();
        $request_params = array();
        $this->credentials('GetCalendarFare');
        //Segments
        $segments = array();
        $segments['Origin'] = $search_params['from'];
        $segments['Destination'] = $search_params['to'];
        $segments['CabinClass'] = $search_params['cabin'];
        $segments['DepartureDate'] = $search_params['depature'];

        $request_params['JourneyType'] = $search_params['trip_type'];
        $request_params['Segments'] = $segments;
        $request_params['PreferredAirlines'] = $search_params['carrier'];
        $response['data']['request'] = json_encode($request_params);
        $response['data']['service_url'] = $this->service_url;
        return $response;
    }

    /**
     * Day Fare Request
     * @param $search_params
     */
    function day_fare_request($search_params) {
        $response['status'] = SUCCESS_STATUS;
        $response['data'] = array();
        $request_params = array();
        //$search_params['adult']
        $this->credentials('UpdateCalendarFareOfDay');
        //Segments
        $segments = array();
        $segments['Origin'] = $search_params['from'];
        $segments['Destination'] = $search_params['to'];
        $segments['CabinClass'] = $search_params['cabin'];
        $segments['DepartureDate'] = $search_params['depature'];

        $request_params['JourneyType'] = $search_params['trip_type'];
        $request_params['Segments'] = $segments;
        $request_params['PreferredAirlines'] = $search_params['carrier'];
        $response['data']['request'] = json_encode($request_params);
        $response['data']['service_url'] = $this->service_url;
        return $response;
    }

    /**
     * Calendar Fare
     * @param $search_params
     */
    function get_fare_list($search_params) {
        $response['data'] = array();
        $response['status'] = true;
        $header_info = $this->get_header();
        //get request
        $search_request = $this->calendar_fare_request($search_params);
        //get data
        if ($search_request['status']) {
            //$this->CI->custom_db->generate_static_response(json_encode($search_request['data']));
            $search_response = $this->CI->api_interface->get_json_response($search_request['data']['service_url'], $search_request['data']['request'], $header_info);
            //$this->CI->custom_db->generate_static_response(json_encode($search_response));
            //$search_response = $GLOBALS['CI']->flight_model->get_static_response(526);
            if ($this->valid_api_response($search_response)) {
                $response['data'] = $search_response['GetCalendarFare'];
            } else {
                $response['status'] = false;
            }
        } else {
            $response['status'] = false;
        }

        return $response;
    }

    /**
     * Calendar Day Fare
     */
    function get_day_fare($search_params) {
        $response['data'] = array();
        $response['status'] = true;
        $header_info = $this->get_header();
        //get request
        $search_request = $this->day_fare_request($search_params);
        //get data
        if ($search_request['status']) {
            //$this->CI->custom_db->generate_static_response(json_encode($search_request['data']));
            $search_response = $this->CI->api_interface->get_json_response($search_request['data']['service_url'], $search_request['data']['request'], $header_info);
            //$this->CI->custom_db->generate_static_response(json_encode($search_response));
            //$search_response = $GLOBALS['CI']->flight_model->get_static_response(526);
            if ($this->valid_api_response($search_response)) {
                $response['data'] = $search_response['UpdateCalendarFareOfDay'];
            } else {
                $response['status'] = false;
            }
        } else {
            $response['status'] = false;
        }

        return $response;
    }

    /**
     * Format for generic view
     * @param array $raw_fare_list
     */
    function format_cheap_fare_list($raw_fare_list, $strict_format = false) {

        $fare_list = array();
        $response['status'] = SUCCESS_STATUS;
        $response['data'] = $fare_list;
        $response['msg'] = '';
        $GetCalendarFareResult = $raw_fare_list['CalendarFareDetails'];
        if (valid_array($GetCalendarFareResult) == true) {
            $LowestFareOfDayInMonth = $GetCalendarFareResult;
            foreach ($LowestFareOfDayInMonth as $k => $day_fare) {
                if (valid_array($day_fare) == true) {
                    $fare_list_obj['airline_code'] = $day_fare['AirlineCode'];
                    $fare_list_obj['airline_icon'] = SYSTEM_IMAGE_DIR . 'airline_logo/' . $day_fare['AirlineCode'] . '.gif';
                    $fare_list_obj['airline_name'] = $day_fare['AirlineName'];

                    $fare_list_obj['departure_date'] = local_date($day_fare['DepartureDate']);
                    $fare_list_obj['departure_time'] = local_time($day_fare['DepartureDate']);
                    $fare_list_obj['departure'] = $day_fare['DepartureDate'];
                    $fare_list_obj['BaseFare'] = $day_fare['BaseFare']; //Base Fare
                    $fare_list_obj['tax'] = $day_fare['Tax'] + $day_fare['FuelSurcharge'];
                } else {
                    $fare_list_obj = false;
                }
                if (valid_array($day_fare) == true) {
                    $fare_list[db_current_datetime(add_days_to_date(0, $day_fare['DepartureDate']))] = $fare_list_obj;
                }
            }
            $response['data'] = $fare_list;
        } else {
            $response['status'] = FAILURE_STATUS;
        }
        return $response;
    }

    /**
     * Format for generic view
     * @param array $raw_fare_list
     */
    function format_day_fare_list($raw_fare_list) {
        $fare_list = array();
        $response['status'] = SUCCESS_STATUS;
        $response['data'] = $fare_list;
        $response['msg'] = '';
        $UpdateCalendarFareOfDayResult = $raw_fare_list['CalendarFareDetails'];
        if (valid_array($UpdateCalendarFareOfDayResult) == true) {
            $CheapestFareOfDay = $UpdateCalendarFareOfDayResult;
            foreach ($CheapestFareOfDay as $k => $day_fare) {
                if (valid_array($day_fare) == true) {
                    $fare_list_obj['airline_code'] = $day_fare['AirlineCode'];
                    $fare_list_obj['airline_icon'] = SYSTEM_IMAGE_DIR . 'airline_logo/' . $day_fare['AirlineCode'] . '.gif';
                    $fare_list_obj['airline_name'] = $day_fare['AirlineName'];

                    $fare_list_obj['departure_date'] = local_date($day_fare['DepartureDate']);
                    $fare_list_obj['departure_time'] = local_time($day_fare['DepartureDate']);
                    $fare_list_obj['departure'] = $day_fare['DepartureDate'];
                    $fare_list_obj['BaseFare'] = $day_fare['BaseFare']; //Base Fare
                    $fare_list_obj['tax'] = $day_fare['Tax'] + $day_fare['FuelSurcharge'];
                } else {
                    $fare_list_obj = false;
                }
                $fare_list[db_current_datetime(add_days_to_date($k, $day_fare['DepartureDate']))] = $fare_list_obj;
            }
            $response['data'] = $fare_list;
        } else {
            $response['status'] = FAILURE_STATUS;
        }
        return $response;
    }

    //****************************************************************************

    /**
     * get search result from tbo
     * @param number $search_id unique id which identifies search details
     */
    function get_flight_list($search_id = '') {
        $this->CI->load->driver('cache');
        $response['data'] = array();
        $response['status'] = true;
        $search_data = $this->search_data($search_id);

        $header_info = $this->get_header();
        //generate unique searchid string to enable caching
        $cache_search = $this->CI->config->item('cache_flight_search');
        $search_hash = $this->search_hash;
        if ($cache_search) {
            $cache_contents = $this->CI->cache->file->get($search_hash);
        }

        if ($search_data['status'] == true) {
            if ($cache_search === false || ($cache_search === true && empty($cache_contents) == true)) {
                //get request
                $search_request = $this->flight_search_request($search_data['data']);
             
                //get data
                if ($search_request['status']) {
                    
                    $search_response = $this->CI->api_interface->get_json_response($search_request['data']['service_url'], $search_request['data']['request'], $header_info);
                    //$this->CI->custom_db->generate_static_response(json_encode($search_response));
                    //$search_response = $this->CI->flight_model->get_static_response(336);//336=>oneway;=>domestic roundway;=> Multicity

                    if ($this->valid_api_response($search_response)) {
                        $response['data'] = $search_response;
                        $response['search_hash'] = $search_hash;
                        $response['from_cache'] = false;
                        $response['from_currency'] = 'USD';
                        $response['cabin_class'] = $search_data['data']['v_class'];
                        if ($cache_search) {
                            $cache_exp = $this->CI->config->item('cache_flight_search_ttl');
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
                $response['search_hash'] = $search_hash;
                $response['from_cache'] = true;
            }
        } else {
            $response['status'] = false;
        }
        //debug($response);exit;
        return $response;
    }

    /**
     * Get Fare Details based on fare key
     * @param array  $data_row           data row of the result
     * @param string $search_session_key search session key
     */
    function get_fare_details($data_row, $search_session_key) {
        $response['data'] = array();
        $response['status'] = FAILURE_STATUS;
        $api_request = $this->fare_details_request($data_row, $search_session_key);
        //get data
        if ($api_request['status']) {
            $header_info = $this->get_header();
            $api_response = $this->CI->api_interface->get_json_response($api_request['data']['service_url'], $api_request['data']['request'], $header_info);

            //$this->CI->custom_db->generate_static_response(json_encode($api_response));
            //$api_response = $this->CI->flight_model->get_static_response(35);
            if ($this->valid_api_response($api_response)) {
                $response['data'] = $api_response['FareRule']['FareRuleDetail'];
                $response['status'] = SUCCESS_STATUS;
            }
        }
        return $response;
    }

    /**
     * Get Fare Quote Details
     * @param array $flight_booking_details
     */
    function fare_quote_details($flight_booking_details) {
        // debug($flight_booking_details);exit;
        $response['status'] = SUCCESS_STATUS; // update
        extract($flight_booking_details);
        $unique_search_access_key = array_unique($flight_booking_details['search_access_key']);
        if (count($unique_search_access_key) == 1) {
            //single request - all search except domestic round way uses this
            if (count($flight_booking_details['search_access_key']) == 1) {
                $tmp_token = $this->run_fare_quote(array($flight_booking_details['token'][0]), $flight_booking_details['search_access_key'][0]);

                $this->update_fare_quote_details($flight_booking_details, 0, $tmp_token['data'], $tmp_token['status'], $response);
            } elseif (count($flight_booking_details['search_access_key']) == 2) {
                //(domestic and round)
                //---Merge both and send single key
                echo 'Under Construction - Balu A';
                exit;
            }
        } else {
            //multiple request - domestic round way uses this T1 - R1, T2 - R2
            foreach ($flight_booking_details['token'] as $___k => $___v) {
                if ($response['status'] == SUCCESS_STATUS) {
                    //If LCC THEN RUN ELSE JUST UPDATE SAME VALUE IF NEEDED
                    $tmp_token = $this->run_fare_quote(array($___v), $flight_booking_details['search_access_key'][$___k]);
                    $this->update_fare_quote_details($flight_booking_details, $___k, $tmp_token['data'], $tmp_token['status'], $response);
                }
            }
        }

        //Update response with the data returned - $flight_booking_details
        $response['data'] = $flight_booking_details;

        if (count($unique_search_access_key) != 1) {
            /* foreach($response['token'] as $k=>$v){

              $response['data']['token'][$k]['ProvabAuthKey']=$v['ProvabAuthKey'];
              $response['ProvabAuthKey']="return";   // remove this for testing
              } */
            unset($response['token']);
        }
      //  echo 'fq';
        //echo json_encode($response); die;
//debug($response); exit;

        return $response;
    }

    /**
     * Extra Service
     * @param unknown_type $flight_booking_details
     */
    public function get_extra_services($flight_booking_details) {
        $response['status'] = FAILURE_STATUS;
        $data = array();

        extract($flight_booking_details);
        $unique_search_access_key = array_unique($flight_booking_details['search_access_key']);
        if (count($unique_search_access_key) == 1) {
            //single request - all search except domestic round way uses this
            if (count($flight_booking_details['search_access_key']) == 1) {
                $journey_type = 'full_journey';
                $extra_services = $this->run_extra_services(array($flight_booking_details['token'][0]), $flight_booking_details['search_access_key'][0], $journey_type);
                if ($this->validate_extra_services_data($extra_services) == true) {
                    $response['status'] = SUCCESS_STATUS;
                    $data[0] = $extra_services['data'];
                }
            }
        } else {
            //multiple request - domestic round way
            foreach ($flight_booking_details['token'] as $___k => $___v) {
                if ($___k == 0) {
                    $journey_type = 'onward_journey';
                } else {
                    $journey_type = 'return_journey';
                }
                $extra_services = $this->run_extra_services(array($___v), $flight_booking_details['search_access_key'][$___k], $journey_type);
                if ($this->validate_extra_services_data($extra_services) == true) {
                    $response['status'] = SUCCESS_STATUS;
                    $data[$___k] = $extra_services['data'];
                }
            }
            $data = array_values($data);
        }

        if ($response['status'] == SUCCESS_STATUS) {
            $data = $this->format_extra_services($data);
        }
        $response['data'] = $data;
        return $response;
    }

    /**
     * Validates the extra services data
     * @param unknown_type $extra_services
     */
    private function validate_extra_services_data($extra_services) {
        if ($extra_services['status'] == SUCCESS_STATUS && isset($extra_services['data']['ExtraServiceDetails']) == true && valid_array($extra_services['data']['ExtraServiceDetails']) == true) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Fomates extra services
     */
    private function format_extra_services($data) {
        $extra_services = array();

        //Baggage
        $Baggage = array();
        foreach ($data as $k => $v) {
            foreach ($v as $ex_sk => $ex_sv) {
                if (isset($ex_sv['Baggage']) == true && valid_array($ex_sv['Baggage']) == true) {
                    $Baggage = array_merge($Baggage, $ex_sv['Baggage']);
                }
            }
        }
        if (valid_array($Baggage)) {
            $extra_services['ExtraServiceDetails']['Baggage'] = $Baggage;
        }
        //Meals
        $Meals = array();
        foreach ($data as $k => $v) {
            foreach ($v as $ex_sk => $ex_sv) {
                if (isset($ex_sv['Meals']) == true && valid_array($ex_sv['Meals']) == true) {
                    $Meals = array_merge($Meals, $ex_sv['Meals']);
                }
            }
        }
        if (valid_array($Meals)) {
            $extra_services['ExtraServiceDetails']['Meals'] = $Meals;
        }
        //Seat
        $Seat = array();
        foreach ($data as $k => $v) {
            foreach ($v as $ex_sk => $ex_sv) {
                if (isset($ex_sv['Seat']) == true && valid_array($ex_sv['Seat']) == true) {
                    $Seat = array_merge($Seat, $ex_sv['Seat']);
                }
            }
        }
        if (valid_array($Seat)) {
            $extra_services['ExtraServiceDetails']['Seat'] = $Seat;
        }
        //MealPreference
        $MealPreference = array();
        foreach ($data as $k => $v) {
            foreach ($v as $ex_sk => $ex_sv) {
                if (isset($ex_sv['MealPreference']) == true && valid_array($ex_sv['MealPreference']) == true) {
                    $MealPreference = array_merge($MealPreference, $ex_sv['MealPreference']);
                }
            }
        }
        if (valid_array($MealPreference)) {
            $extra_services['ExtraServiceDetails']['MealPreference'] = $MealPreference;
        }
        //SeatPreference
        $SeatPreference = array();
        foreach ($data as $k => $v) {
            foreach ($v as $ex_sk => $ex_sv) {
                if (isset($ex_sv['SeatPreference']) == true && valid_array($ex_sv['SeatPreference']) == true) {
                    $SeatPreference = array_merge($SeatPreference, $ex_sv['SeatPreference']);
                }
            }
        }
        if (valid_array($SeatPreference)) {
            $extra_services['ExtraServiceDetails']['SeatPreference'] = $SeatPreference;
        }

        return $extra_services;
    }

    /**
     * Check if ticketed
     * @param array $api_response
     * @return boolean|string
     */
    private function format_ticket_response($api_response) {
        $response['status'] = BOOKING_FAILED; //Master Booking status
        $response['data'] = array();
        $response['message'] = '';
        $ticket_details = array();
        if (valid_array($api_response) == true) {
            foreach ($api_response as $k => $v) {
                if ($v['status'] == SUCCESS_STATUS) {
                    $ticket_details[$k]['data'] = $v['data'];
                    $ticket_details[$k]['status'] = SUCCESS_STATUS;
                    $response['status'] = SUCCESS_STATUS; //DONT CHANGE(single ticket can be successfull)
                } else {
                    $ticket_details[$k]['data'] = $v['data'];
                    $ticket_details[$k]['status'] = $v['status'];
                }
            }
            $response['data']['TicketDetails'] = $ticket_details;
        }
        return $response;
    }

    /**
     *
     * @param array  $flight_booking_details    flight booking details passed with reference
     * @param number $index                     index to be updated
     * @param array  $new_quote_details         new details to be updated to index
     */
    function update_fare_quote_details(& $flight_booking_details, $index, $new_quote_details, $process_quote_request, & $response) {

        if ($process_quote_request != FAILURE_STATUS) {
            $flight_booking_details['token'][$index] = $new_quote_details;
            $flight_booking_details['token_key'][$index] = serialized_data($flight_booking_details['token'][$index]);
        } else {
            $response['status'] = FAILURE_STATUS;
        }
    }

    /**
     * @param array $data_key
     * @param string $search_access_key
     */
    private function run_fare_quote($data_key, $search_access_key) {
       /* debug($data_key);
        debug($search_access_key);
        exit;*/
        $response['data'] = array();
        $response['status'] = FAILURE_STATUS;
        $api_request = $this->fare_quote_request($data_key, $search_access_key);
        //get data
        if ($api_request['status']) {
            $header_info = $this->get_header();

            $api_response = $this->CI->api_interface->get_json_response($api_request['data']['service_url'], $api_request['data']['request'], $header_info);
           /* echo 'fare quotesss';
            debug($api_response); exit;*/
            //$this->CI->custom_db->generate_static_response(json_encode($api_response));
            //$api_response = $this->CI->flight_model->get_static_response(151);//91//later

            if ($this->valid_api_response($api_response)) {
                $response['data'] = $api_response['UpdateFareQuote']['FareQuoteDetails']['JourneyList'];
                $response['status'] = SUCCESS_STATUS;
            }
        }
        return $response;
    }

    /**
     * @param array $data_key
     * @param string $search_access_key
     */
    private function run_extra_services($data_key, $search_access_key, $journey_type) {
        $response['data'] = array();
        $response['status'] = FAILURE_STATUS;
        $api_request = $this->extra_services_request($data_key, $search_access_key);
        //get data
        if ($api_request['status']) {
            $header_info = $this->get_header();

            $api_response = $this->CI->api_interface->get_json_response($api_request['data']['service_url'], $api_request['data']['request'], $header_info);
            //$this->CI->custom_db->generate_static_response(json_encode($api_response));
            //$api_response = $this->CI->flight_model->get_static_response(248);//152//154//later

            if ($this->valid_api_response($api_response)) {
                $response['data']['ExtraServiceDetails'] = $this->extra_services_in_preferred_currency($api_response['ExtraServices']['ExtraServiceDetails'], $journey_type);
                $response['status'] = SUCCESS_STATUS;
            }
        }
        return $response;
    }

    /**
     * Wrapper - 1 for booking
     * Balu A
     * Process Booking
     * @param array $booking_params
     */
    public function process_booking($book_id, $booking_params) {
        //debug($booking_params);exit;
        //Adding SequenceNumber
        foreach ($booking_params['token']['token'] as $k => $v) {
            $booking_params['token']['token'][$k]['SequenceNumber'] = $k;
        }
        $response['status'] = SUCCESS_STATUS;
        $wrapper_token = $booking_params['token'];
        $book_response = array();
        $book_response = $this->book_flight($book_id, $booking_params);
        /*echo 'Book Res';
        debug($book_response); exit;*/
        if ($book_response['status'] == FAILURE_STATUS) {
            $response['status'] = FAILURE_STATUS;
            $response['message'] = $book_response['message'];
        } else {
            $ticket_response = $book_response;
            $response['status'] = $ticket_response['status'];
        }
        //Extracting Response
        $response['data']['ticket']['TicketDetails'] = @$ticket_response['data'];
        $response['data']['book_id'] = $book_id;
        $response['data']['booking_params'] = $booking_params;

        return $response;
    }

    /**
     * Do Booking of Flight
     * @param $book_id
     * @param $booking_params
     */
    function book_flight($book_id, $booking_params) 
{        $response['status'] = FAILURE_STATUS;

        
        $booking_response = array();
        $token_wrapper = $booking_params['token'];
        $op = $booking_params['op'];
        //check ONE WAY - Domestic / Intl & ROUND WAY - Intl - Run Once
        $unique_search_access_key = array_unique($token_wrapper['search_access_key']);

        if (count($unique_search_access_key) == 1) { // Single session is one request
            if (count($token_wrapper['search_access_key']) == 1) {
                //Extract Passenger Information
                $passenger = $this->extract_passenger_info($booking_params, $token_wrapper['token'][0]['SequenceNumber']);

                if (isset($booking_params['ticket_method']) && $booking_params['ticket_method'] === 'hold_ticket') {//HOLD TICKET
                    $tmp_res = $this->run_hold_booking($op, $book_id, $token_wrapper['token'][0], $passenger, $token_wrapper['search_access_key'][0]);
                } else {//DIRECT TICKETING
                    $tmp_res = $this->run_commit_booking($op, $book_id, $token_wrapper['token'][0], $passenger, $token_wrapper['search_access_key'][0]);
                }


                if ($this->valid_flight_booking_status($tmp_res['status']) == true) {
                    $booking_response[] = $tmp_res['data'];
                    $response['status'] = $tmp_res['status'];
                } else {
                    $response['message'] = $tmp_res['message'];
                    $response['status'] = FAILURE_STATUS;
                }
            }
        } else { // multiple request is two request
            //Domestic Round - Run Twice
            foreach ($token_wrapper['token'] as $___k => $___v) {
                //Extract Passenger Information
                $passenger = $this->extract_passenger_info($booking_params, $___v['SequenceNumber']);

                $tmp_resp = $this->run_commit_booking($op, $book_id, $___v, $passenger, $token_wrapper['search_access_key'][$___k]);
                if ($this->valid_flight_booking_status($tmp_resp['status']) == true) {

                    $booking_response[$___k] = $tmp_resp['data'];

                    if ($response['status'] != BOOKING_CONFIRMED) {
                        $response['status'] = $tmp_resp['status'];
                    }
                } else {
                    $booking_response[$___k]['Status'] = $tmp_resp['status'];
                    $booking_response[$___k]['Message'] = $tmp_resp['message'];
                    $response['message'] = @$booking_response[$___k]['message'];
                    if ($this->valid_flight_booking_status($response['status']) == false) {//Even if one booking is Hold/Success, return the status as Hold/Success
                        $response['status'] = FAILURE_STATUS;
                    }
                    break;
                }
            }
        }
        $response['data'] = $booking_response;
        return $response;
    }

    /**
     * Booking status is valid or not
     * @param unknown_type $booking_status
     */
    private function valid_flight_booking_status($booking_status) {
        if (in_array($booking_status, array(BOOKING_CONFIRMED, BOOKING_HOLD)) == true) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Book Flight
     * @param $book_id          temporary book id used to make payment :p
     * @param $booking_params   all the booking data wrapped in array
     */
    function run_commit_booking($op, $book_id, $token, $passenger, $search_access_key) {
        $response['data'] = array();
        $response['status'] = FAILURE_STATUS;
        $response['message'] = '';
        $SequenceNumber = $token['SequenceNumber'];
        $booking_params['Passenger'] = $this->WSPassenger($passenger);
        //GST Details
        if(empty($passenger['gst_numebr']) == false){
            $booking_params['GST'] = $passenger;
        }
        //Prova Auth key
        $booking_params['ProvabAuthKey'] = $token['ProvabAuthKey'];
        $booking_params['SequenceNumber'] = $SequenceNumber;
        $api_request = $this->commit_booking_request($booking_params, $book_id);
        // debug($api_request);exit;
        //get data
        if ($api_request['status']) {
            $header_info = $this->get_header();

            $this->CI->custom_db->generate_static_response(json_encode($api_request['data']['request']));
            //debug($api_request['data']['service_url']);
            //debug($api_request['data']['request']);

            $api_response = $this->CI->api_interface->get_json_response($api_request['data']['service_url'], $api_request['data']['request'], $header_info);
          //  echo 'Resp';
            //debug($api_response); exit;
            $this->CI->custom_db->generate_static_response(json_encode($api_response));

            //$static_id =  378;
            //$api_response = $this->CI->flight_model->get_static_response($static_id);//378

            if ($this->valid_commit_booking_response($api_response) == true) {
                $api_response['CommitBooking']['BookingDetails']['Price'] = $this->convert_bookingdata_to_application_currency($api_response['CommitBooking']['BookingDetails']['Price']);
                $response['data'] = $api_response;
                $response['status'] = $api_response['Status'];
            } else {
                $response['message'] = @$api_response['Message'];
                $response['status'] = FAILURE_STATUS;
            }
        }
        /** PROVAB LOGGER * */
        $GLOBALS['CI']->private_management_model->provab_xml_logger('Commit Booking', $book_id, 'flight', json_encode($api_request['data']), json_encode($api_response));
        return $response;
    }

    /**
     * Hold Ticket
     * @param $book_id          temporary book id used to make payment :p
     * @param $booking_params   all the booking data wrapped in array
     */
    function run_hold_booking($op, $book_id, $token, $passenger, $search_access_key) {
        $response['data'] = array();
        $response['status'] = FAILURE_STATUS;
        $response['message'] = '';
        $SequenceNumber = $token['SequenceNumber'];
        $booking_params['Passenger'] = $this->WSPassenger($passenger);
        //Prova Auth key
        $booking_params['ProvabAuthKey'] = $token['ProvabAuthKey'];
        $booking_params['SequenceNumber'] = $SequenceNumber;
        $api_request = $this->hold_booking_request($booking_params, $book_id);
        //get data
        if ($api_request['status']) {
            $header_info = $this->get_header();

            $this->CI->custom_db->generate_static_response(json_encode($api_request['data']['request']));

            $api_response = $this->CI->api_interface->get_json_response($api_request['data']['service_url'], $api_request['data']['request'], $header_info);
            $this->CI->custom_db->generate_static_response(json_encode($api_response));

            /* $static_id =     1198;
              $api_response = $this->CI->flight_model->get_static_response($static_id);//378 */

            if ($this->valid_commit_booking_response($api_response) == true) {
                $api_response['CommitBooking'] = $api_response['HoldTicket'];
                unset($api_response['HoldTicket']);
                $api_response['CommitBooking']['BookingDetails']['Price'] = $this->convert_bookingdata_to_application_currency($api_response['CommitBooking']['BookingDetails']['Price']);

                $response['data'] = $api_response;
                $response['status'] = $api_response['Status'];
            } else {
                $response['message'] = @$api_response['Message'];
                $response['status'] = FAILURE_STATUS;
            }
        }
        /** PROVAB LOGGER * */
        $GLOBALS['CI']->private_management_model->provab_xml_logger('Hold Booking', $book_id, 'flight', json_encode($api_request['data']), json_encode($api_response));
        return $response;
    }

    /**
     * Forms a group based on passenger origin and transaction_fk
     * @param $booking_details
     * @param $passenger_origin
     */
     function group_cancellation_passenger_ticket_id($booking_details, $passenger_origin) {
        $booking_details = $booking_details['booking_details'][0];
        $booking_transaction_details = $booking_details['booking_transaction_details'];
        $indexed_passenger_ticket_id = array();
        $indexed_passenger_origin = array();
        foreach ($booking_transaction_details as $tk => $tv) {
            $booking_customer_details = $tv['booking_customer_details'];

            foreach ($booking_customer_details as $ck => $cv) {
                // if (in_array($cv['origin'], $passenger_origin) == true) {
                //     $indexed_passenger_ticket_id[$tv['origin']][$ck] = (int) $cv['TicketId']; //Ticket Ids
                //     $indexed_passenger_origin[$tv['origin']][$ck] = $cv['origin']; //Passenger Origin
                // }
                
                if(in_array($cv['origin'], $passenger_origin) == true){
                    $indexed_passenger_ticket_id[$tv['origin']][$ck] = (int)$cv['api_passenger_origin'];//Ticket Ids
                    $indexed_passenger_origin[$tv['origin']][$ck] = $cv['origin'];//Passenger Origin
                }
            }
            if (isset($indexed_passenger_ticket_id[$tv['origin']])) {
                $indexed_passenger_ticket_id[$tv['origin']] = array_values($indexed_passenger_ticket_id[$tv['origin']]);
                $indexed_passenger_origin[$tv['origin']] = array_values($indexed_passenger_origin[$tv['origin']]);
            }
        }
        return array('passenger_origin' => $indexed_passenger_origin, 'passenger_ticket_id' => $indexed_passenger_ticket_id);
    }
    /**
     * Balu A
     * Flight Booking Cancel
     * @param $master_booking_details
     * @param $passenger_origin => $passenger_origin indexed with Transaction Origin
     * @param $passenger_ticket_id => Ticket Ids indexed with Transaction Origin
     */
    function cancel_booking($master_booking_details, $passenger_origin, $passenger_ticket_id) {
        $response['data'] = array();
        $response['status'] = FAILURE_STATUS;
        $response['message'] = '';
        $booking_details = $master_booking_details['booking_details']['0'];
        $app_reference = $booking_details['app_reference'];
        $booking_transaction_details = $booking_details['booking_transaction_details'];
        $passenger_origins = array(); //Change Request IDs
        foreach ($booking_transaction_details as $transaction_details_k => $transaction_details_v) {
            $transaction_origin = $transaction_details_v['origin'];
            if (isset($passenger_ticket_id[$transaction_origin]) == true && valid_array($passenger_ticket_id[$transaction_origin]) == true) {
                //If Ticket Ids exists for the Transaction, then run the cancel request for Requested Pax Tickets
                $pax_ticket_ids = $passenger_ticket_id[$transaction_origin];
                $pax_count = count($transaction_details_v['booking_customer_details']);
                $pax_cancel_count = count($pax_ticket_ids);
                if ($pax_count == $pax_cancel_count) {
                    $IsFullBookingCancel = true;
                } else {
                    $IsFullBookingCancel = false;
                }
                $api_booking_id = trim($transaction_details_v['book_id']);
                $pnr = trim($transaction_details_v['pnr']);
                $app_reference = trim($transaction_details_v['app_reference']);
                $cancell_request_params['SequenceNumber'] = (int) $transaction_details_v['sequence_number'];
                $cancell_request_params['BookingId'] = $api_booking_id;
                $cancell_request_params['PNR'] = $pnr;
                $cancell_request_params['TicketId'] = $pax_ticket_ids;
                $cancell_request_params['IsFullBookingCancel'] = $IsFullBookingCancel;
                $cancel_booking_request = $this->cancel_booking_request($cancell_request_params, $app_reference);
                #debug($cancel_booking_request); die;
                if ($cancel_booking_request['status']) {
                    $header_info = $this->get_header();
                    $send_change_response = $this->CI->api_interface->get_json_response($cancel_booking_request['data']['service_url'], $cancel_booking_request['data']['request'], $header_info);

                    $this->CI->custom_db->generate_static_response(json_encode($send_change_response));

                    //$send_change_response = $this->CI->flight_model->get_static_response(536);//493=>success;492=>failed

                    if (isset($send_change_response['Status']) == true && $send_change_response['Status'] == SUCCESS_STATUS) {
                        $passenger_origins[$transaction_origin] = $passenger_origin[$transaction_origin];
                    }
                }
            }
        }
        if (valid_array($passenger_origins) == true) {
            $response['status'] = SUCCESS_STATUS;
            $this->update_ticket_cancellation_status($app_reference, $passenger_origins);
        }
        return $response;
    }

    /**
     * Balu A
     * Update the Cancelled Ticket Status
     * @param unknown_type $app_reference
     * @param unknown_type $passenger_ticket_id
     * @param unknown_type $ChangeRequestIds
     */
    public function update_ticket_cancellation_status($app_reference, $passenger_ticket_origins) {
        $booking_details = $GLOBALS['CI']->flight_model->get_booking_details($app_reference);
        $current_module = $GLOBALS['CI']->config->item('current_module');
        $GLOBALS['CI']->load->library('booking_data_formatter');
        $booking_details = $GLOBALS['CI']->booking_data_formatter->format_flight_booking_data($booking_details, $current_module);
        $booking_details = $booking_details['data']['booking_details']['0'];
        $booking_transaction_details = $booking_details['booking_transaction_details'];
        foreach ($booking_transaction_details as $transaction_details_k => $transaction_details_v) {
            $transaction_origin = $transaction_details_v['origin'];
            if (isset($passenger_ticket_origins[$transaction_origin]) == true && valid_array($passenger_ticket_origins[$transaction_origin]) == true) {
                //If Ticket Ids exists for the Transaction, then run the get cancel status request
                $api_booking_id = trim($transaction_details_v['book_id']);
                $pnr = trim($transaction_details_v['pnr']);
                $app_reference = trim($transaction_details_v['app_reference']);
                $sequence_number = (int) $transaction_details_v['sequence_number'];

                $passenger_origins = array_values($passenger_ticket_origins[$transaction_origin]);
                foreach ($passenger_origins as $tick_k => $tick_v) {
                    $pax_origin = array_shift($passenger_origins);
                    //Update Cancellation Status
                    $booking_status = 'BOOKING_INPROGRESS';
                    $passenger_update_data = array();
                    $passenger_update_data['status'] = $booking_status;
                    $passenger_update_condition = array();
                    $passenger_update_condition['origin'] = $pax_origin;
                    $this->CI->custom_db->update_record('flight_booking_passenger_details', $passenger_update_data, $passenger_update_condition);
                    $this->CI->flight_model->add_flight_cancellation_details($pax_origin);
                    // echo $this->CI->db->last_query();
                    // echo '<br/>';
                }
                $GLOBALS['CI']->flight_model->update_flight_booking_transaction_cancel_status($transaction_origin);
            }
        }//End of Transaction Loop
        //Update Master Booking Status
        $GLOBALS['CI']->flight_model->update_flight_booking_cancel_status($app_reference);
    }

    /**
     * Balu A
     * API request for getting Ticket cancellation status
     * @param unknown_type $request_params
     * @param unknown_type $app_reference
     */
    public function get_supplier_ticket_refund_details($request_params) {
        $response['data'] = array();
        $response['status'] = SUCCESS_STATUS;
        $response['messge'] = '';
        $api_request = $this->ticket_refund_details_request($request_params);
        if ($api_request['status'] == true) {
            $header_info = $this->get_header();
            $api_response = $this->CI->api_interface->get_json_response($api_request['data']['service_url'], $api_request['data']['request'], $header_info);
            //$this->CI->custom_db->generate_static_response(json_encode($api_response));
            if ($this->valid_api_response($api_response) == true) {
                $response['data'] = $api_response['TicketRefundDetails'];
                $response['status'] = SUCCESS_STATUS;
            } else {
                $response['message'] = @$api_response['Message'];
                $status = empty($api_response['Status']) == true ? FAILURE_STATUS : $api_response['Status'];
                $response['status'] = $status;
            }
        }
        return $response;
    }

    /**
     * Formates Passenger Info for Booking
     * @param unknown_type $passenger
     * @param unknown_type $passenger_token
     */
    private function WSPassenger($passenger) {
        $tmp_passenger = array();
        $total_pax_count = count($passenger['passenger_type']);
        $i = 0;
        for ($i = 0; $i < $total_pax_count; $i++) {
            $tmp_passenger[$i]['IsLeadPax'] = $passenger['lead_passenger'][$i];
            $tmp_passenger[$i]['Title'] = $passenger['name_title'][$i];
            $tmp_passenger[$i]['FirstName'] = ((strlen($passenger['first_name'][$i]) < 2) ? str_repeat($passenger['first_name'][$i], 2) : $passenger['first_name'][$i]);
            $tmp_passenger[$i]['LastName'] = ((strlen($passenger['last_name'][$i]) < 2) ? str_repeat($passenger['last_name'][$i], 2) : $passenger['last_name'][$i]);
            $tmp_passenger[$i]['PaxType'] = $passenger['passenger_type'][$i];
            $tmp_passenger[$i]['Gender'] = $passenger['gender'][$i];
            $tmp_passenger[$i]['DateOfBirth'] = date('Y-m-d', strtotime($passenger['date_of_birth'][$i]));

            if (empty($passenger['passport_number'][$i]) == false and empty($passenger['passport_expiry_date'][$i]) == false) {
                $tmp_passenger[$i]['PassportNumber'] = $passenger['passport_number'][$i];
                $tmp_passenger[$i]['PassportExpiry'] = $passenger['passport_expiry_date'][$i];
            } else {
                $tmp_passenger[$i]['PassportNumber'] = '';
                $tmp_passenger[$i]['PassportExpiry'] = null;
            }

            $tmp_passenger[$i]['CountryCode'] = $passenger['passenger_nationality'][$i];
            $tmp_passenger[$i]['CountryName'] = $passenger['billing_country_name'];
            $tmp_passenger[$i]['ContactNo'] = $passenger['passenger_contact'];
            $tmp_passenger[$i]['City'] = $passenger['billing_city'];
            $tmp_passenger[$i]['PinCode'] = $passenger['billing_zipcode'];

            $tmp_passenger[$i]['AddressLine1'] = $passenger['billing_address_1'];
            $tmp_passenger[$i]['AddressLine2'] = $passenger['billing_address_1'];
            $tmp_passenger[$i]['Email'] = $passenger['billing_email'];


            //Baggage
            if (isset($passenger['baggage'][$i]) == true && valid_array($passenger['baggage'][$i]) == true) {
                $tmp_passenger[$i]['BaggageId'] = $passenger['baggage'][$i];
            }

            //Meals
            if (isset($passenger['meal'][$i]) == true && valid_array($passenger['meal'][$i]) == true) {
                $tmp_passenger[$i]['MealId'] = $passenger['meal'][$i];
            }

            //Seat
            if (isset($passenger['seat'][$i]) == true && valid_array($passenger['seat'][$i]) == true) {
                $tmp_passenger[$i]['SeatId'] = $passenger['seat'][$i];
            }
        }

        return $tmp_passenger;
    }

    /**
     * Get Booking Details
     * @param array $booking_details
     */
    function extract_booking_details($booking_details = array()) {
        if (valid_array($booking_details) == true && $booking_details['Status'] == SUCCESS_STATUS) {
            $data['pnr'] = $booking_details['Book']['BookingDetails']['PNR'];
            $data['booking_id'] = $booking_details['Book']['BookingDetails']['BookingId'];
            return $data;
        }
    }

    /**
     * get only passenger info from booking form
     * @param $booking_params
     */
    private function extract_passenger_info($booking_params, $SequenceNumber) {
        $extra_service_details = $this->extract_extra_service_details($booking_params);

        $country_list = $GLOBALS['CI']->db_cache_api->get_country_list(array('k' => 'origin', 'v' => 'iso_country_code'));
        //$city_list = $GLOBALS['CI']->db_cache_api->get_city_list();
        $passenger['lead_passenger'] = $booking_params['lead_passenger'];
        foreach ($booking_params['name_title'] as $__k => $__v) {
            $passenger['name_title'][$__k] = @get_enum_list('title', $__v);
        }
        $passenger['first_name'] = $booking_params['first_name'];
        //$passenger['middle_name']         = $booking_params['middle_name'];
        $passenger['last_name'] = $booking_params['last_name'];
        $passenger['date_of_birth'] = $booking_params['date_of_birth'];
        foreach ($booking_params['passenger_type'] as $__k => $__v) {
            $passenger['passenger_type'][$__k] = $this->pax_type($__v);
        }
        foreach ($booking_params['gender'] as $__k => $__v) {
            $gender = (isset($__v) ? get_enum_list('gender', $__v) : '');
            $passenger['gender'][$__k] = $this->gender_type($gender);
        }
        foreach ($booking_params['passenger_nationality'] as $__k => $__v) {
            $passenger['passenger_nationality'][$__k] = (isset($country_list[$__v]) ? $country_list[$__v] : '');
        }

        foreach ($booking_params['passenger_passport_issuing_country'] as $__k => $__v) {
            $passenger['passenger_passport_issuing_country'][$__k] = (isset($country_list[$__v]) ? $country_list[$__v] : '');
        }
        //$passenger['passport_number'] = $booking_params['passenger_passport_number'];
        $passenger['passport_number'] = preg_replace('/\s+/', '', $booking_params['passenger_passport_number']);


        foreach ($passenger['passport_number'] as $__k => $__v) {
            if (empty($__v) == false) {
                //FIXME
                $pass_date = strtotime($booking_params['passenger_passport_expiry_year'][$__k] . '-' . $booking_params['passenger_passport_expiry_month'][$__k] . '-' . $booking_params['passenger_passport_expiry_day'][$__k]);
                $passenger['passport_expiry_date'][$__k] = date('Y-m-d', $pass_date);
            } else {
                $passenger['passport_expiry_date'][$__k] = '';
            }
        }

        if ($SequenceNumber == 0) {
            $journy_type = array('full_journey', 'onward_journey');
        } else {
            $journy_type = array('return_journey');
        }

        //Baggage
        if (isset($extra_service_details['ExtraServiceDetails']['Baggage']) == true && valid_array($extra_service_details['ExtraServiceDetails']['Baggage']) == true) {
            $Baggage = $extra_service_details['ExtraServiceDetails']['Baggage'];

            foreach ($booking_params['first_name'] as $__k => $__v) {
                $baggage_index = 0;
                $passenger_baggage = array();

                while (isset($booking_params["baggage_$baggage_index"]) == true) {
                    if (isset($booking_params["baggage_$baggage_index"][$__k]) == true && empty($booking_params["baggage_$baggage_index"][$__k]) == false && in_array($Baggage[$booking_params["baggage_$baggage_index"][$__k]]['JourneyType'], $journy_type) == true) {

                        $passenger_baggage[] = $booking_params["baggage_$baggage_index"][$__k];
                    }
                    $baggage_index++;
                }//while ends

                if (valid_array($passenger_baggage) == true) {
                    $passenger['baggage'][$__k] = $passenger_baggage;
                }
            }
        }//Baggage ends
        //Meals
        if (isset($extra_service_details['ExtraServiceDetails']['Meals']) == true && valid_array($extra_service_details['ExtraServiceDetails']['Meals']) == true) {
            $Meals = $extra_service_details['ExtraServiceDetails']['Meals'];

            foreach ($booking_params['first_name'] as $__k => $__v) {
                $meal_index = 0;
                $passenger_meal = array();
                while (isset($booking_params["meal_$meal_index"]) == true) {
                    if (isset($booking_params["meal_$meal_index"][$__k]) == true && empty($booking_params["meal_$meal_index"][$__k]) == false && in_array($Meals[$booking_params["meal_$meal_index"][$__k]]['JourneyType'], $journy_type) == true) {
                        $passenger_meal[] = $booking_params["meal_$meal_index"][$__k];
                    }
                    $meal_index++;
                }
                if (valid_array($passenger_meal) == true) {
                    $passenger['meal'][$__k] = $passenger_meal;
                }
            }
        }//Meal ends
        //Meals Preference
        if (isset($extra_service_details['ExtraServiceDetails']['MealPreference']) == true && valid_array($extra_service_details['ExtraServiceDetails']['MealPreference']) == true) {
            $Meals = $extra_service_details['ExtraServiceDetails']['MealPreference'];

            foreach ($booking_params['first_name'] as $__k => $__v) {
                $meal_index = 0;
                $passenger_meal_pref = array();
                while (isset($booking_params["meal_pref$meal_index"]) == true) {
                    if (isset($booking_params["meal_pref$meal_index"][$__k]) == true && empty($booking_params["meal_pref$meal_index"][$__k]) == false && in_array($Meals[$booking_params["meal_pref$meal_index"][$__k]]['JourneyType'], $journy_type) == true) {
                        $passenger_meal_pref[] = $booking_params["meal_pref$meal_index"][$__k];
                    }
                    $meal_index++;
                }
                if (valid_array($passenger_meal_pref) == true) {
                    $passenger['meal'][$__k] = $passenger_meal_pref;
                }
            }
        }//Meal Preference ends
        //Seat
        if (isset($extra_service_details['ExtraServiceDetails']['Seat']) == true && valid_array($extra_service_details['ExtraServiceDetails']['Seat']) == true) {
            $Seat = $extra_service_details['ExtraServiceDetails']['Seat'];

            foreach ($booking_params['first_name'] as $__k => $__v) {
                $seat_index = 0;
                $passenger_seat = array();
                while (isset($booking_params["seat_$seat_index"]) == true) {
                    if (isset($booking_params["seat_$seat_index"][$__k]) == true && empty($booking_params["seat_$seat_index"][$__k]) == false && in_array($Seat[$booking_params["seat_$seat_index"][$__k]]['JourneyType'], $journy_type) == true) {
                        $passenger_seat[] = $booking_params["seat_$seat_index"][$__k];
                    }
                    $seat_index++;
                }
                if (valid_array($passenger_seat) == true) {
                    $passenger['seat'][$__k] = $passenger_seat;
                }
            }
        }//Seat ends
        //Seat Preference
        if (isset($extra_service_details['ExtraServiceDetails']['SeatPreference']) == true && valid_array($extra_service_details['ExtraServiceDetails']['SeatPreference']) == true) {
            $SeatPreference = $extra_service_details['ExtraServiceDetails']['SeatPreference'];

            foreach ($booking_params['first_name'] as $__k => $__v) {
                $seat_index = 0;
                $passenger_seat_pref = array();
                while (isset($booking_params["seat_pref$seat_index"]) == true) {
                    if (isset($booking_params["seat_pref$seat_index"][$__k]) == true && empty($booking_params["seat_pref$seat_index"][$__k]) == false && in_array($SeatPreference[$booking_params["seat_pref$seat_index"][$__k]]['JourneyType'], $journy_type) == true) {
                        $passenger_seat_pref[] = $booking_params["seat_pref$seat_index"][$__k];
                    }
                    $seat_index++;
                }
                if (valid_array($passenger_seat_pref) == true) {
                    $passenger['seat'][$__k] = $passenger_seat_pref;
                }
            }
        }//Seat Preference ends

        $passenger['billing_country'] = $country_list[$booking_params['billing_country']];
        $passenger['billing_country_name'] = 'India'; //FIXME: Make it Dynamic
        //$passenger['billing_city'] = $city_list[$booking_params['billing_city']];
        $passenger['billing_city'] = $booking_params['billing_city'];
        $passenger['billing_zipcode'] = $booking_params['billing_zipcode'];
        $passenger['billing_email'] = $booking_params['billing_email'];
        $passenger['billing_address_1'] = $booking_params['billing_address_1'];
        $passenger['passenger_contact'] = $booking_params['passenger_contact'];

        //GST Details
        if(isset($booking_params['gst_number'])){
            // debug($booking_params);exit;
            $passenger['gst_numebr'] = $booking_params['gst_number'];
            $passenger['gst_company_name'] = $booking_params['gst_company_name'];
            $passenger['gst_email'] = $booking_params['gst_email'];
            $passenger['gst_phone'] = $booking_params['gst_phone'];
            $passenger['gst_phone'] = $booking_params['gst_phone'];
            $passenger['gst_state'] = $booking_params['gst_state'];
            $passenger['gst_address'] = $booking_params['gst_address'];
            
        }
        return $passenger;
    }

    private function pax_type($pax_type) {
        switch (strtoupper($pax_type)) {
            case 'ADULT' : $pax_type = "1";
                break;
            case 'CHILD' : $pax_type = "2";
                break;
            case 'INFANT' : $pax_type = "3";
                break;
        }
        return $pax_type;
    }

    private function gender_type($pax_type) {
        switch (strtoupper($pax_type)) {
            case 'MALE' : $pax_type = "1";
                break;
            case 'FEMALE' : $pax_type = "2";
        }
        return $pax_type;
    }

    private function tbo_source_enum($source) {
        switch ($source) {
            case 'WorldSpan' : $source = 0;
                break;
            case 'Abacus' : $source = 1;
                break;
            case 'SpiceJet' : $source = 2;
                break;
            case 'Amadeus' : $source = 3;
                break;
            case 'Galileo' : $source = 4;
                break;
            case 'Indigo' : $source = 5;
                break;
            case 'Paramount' : $source = 6;
                break;
            case 'AirDeccan' : $source = 7;
                break;
            case 'MDLR' : $source = 8;
                break;
            case 'GoAir' : $source = 9;
                break;
        }
        return $source;
    }

    /**
     * TBO SOurce Name
     * @param unknown_type $source
     */
    private function get_tbo_source_name($source) {
        switch ($source) {
            case 0 : $source = 'WorldSpan';
                break;
            case 1 : $source = 'Abacus';
                break;
            case 3: $source = 'SpiceJet';
                break;
            case 4 : $source = 'Amadeus';
                break;
            case 5 : $source = 'Galileo';
                break;
            case 6 : $source = 'Indigo';
                break;
            /* case 6 : $source = 'Paramount';
              break; */
            /* case 7 : $source = 'AirDeccan';
              break; */
            /* case  8 : $source = 'MDLR';
              break; */
            case 10: $source = 'GoAir';
                break;
            case 19 : $source = 'AirAsia';
                break;
            case 13 : $source = 'AirArabia';
                break;
            case 17 : $source = 'FlyDubai';
                break;
            case 14 : $source = 'AirIndiaExpress';
                break;
            case 46 : $source = 'AirCosta';
                break;
            case 48 : $source = 'BhutanAirlines';
                break;
            case 49 : $source = 'AirPegasus';
                break;
            case 50 : $source = 'TruJet';
                break;
        }
        return $source;
    }

    /**
     * check the response is valid or not
     * @param array $api_response  response to be validated
     */
    function valid_api_response($api_response) {
        if (empty($api_response) == false && valid_array($api_response) == true and isset($api_response['Status']) == true and $api_response['Status'] == SUCCESS_STATUS) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Validates Commit Booking Response
     * @param unknown_type $api_response
     */
    private function valid_commit_booking_response($api_response) {
        if (empty($api_response) == false && valid_array($api_response) == true and isset($api_response['Status']) == true and in_array($api_response['Status'], array(SUCCESS_STATUS, BOOKING_CONFIRMED, BOOKING_HOLD))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * convert search params to TBO format
     */
    public function search_data($search_id) {
        $response['status'] = true;
        $response['data'] = array();
        if (empty($this->master_search_data) == true and valid_array($this->master_search_data) == false) {
            $is_roundtrip = false;
            $is_multicity = false;
            $clean_search_details = $this->CI->flight_model->get_safe_search_data($search_id);
            if ($clean_search_details['status'] == true) {
                $response['status'] = true;
                $response['data'] = $clean_search_details['data'];
                $response['data']['from_city'] = $clean_search_details['data']['from'];
                $response['data']['to_city'] = $clean_search_details['data']['to'];

                switch ($clean_search_details['data']['trip_type']) {
                    case 'oneway':
                        $response['data']['type'] = 'OneWay';
                        $response['data']['depature'] = date("Y-m-d", strtotime($clean_search_details['data']['depature'])) . 'T00:00:00';
                        $response['data']['return'] = date("Y-m-d", strtotime($clean_search_details['data']['depature'])) . 'T00:00:00';
                        $response['data']['from'] = substr(chop(substr($clean_search_details['data']['from'], -5), ')'), -3);
                        $response['data']['to'] = substr(chop(substr($clean_search_details['data']['to'], -5), ')'), -3);
                        break;
                    case 'circle':
                        $response['data']['type'] = 'Return';
                        $response['data']['depature'] = date("Y-m-d", strtotime($clean_search_details['data']['depature'])) . 'T00:00:00';
                        $response['data']['return'] = date("Y-m-d", strtotime($clean_search_details['data']['return'])) . 'T00:00:00';
                        $response['data']['from'] = substr(chop(substr($clean_search_details['data']['from'], -5), ')'), -3);
                        $response['data']['to'] = substr(chop(substr($clean_search_details['data']['to'], -5), ')'), -3);
                        $is_roundtrip = true;
                        break;
                    case 'multicity':
                        $response['data']['type'] = 'Multicity';
                        $is_multicity = true;
                        for ($i = 0; $i < count($clean_search_details['data']['depature']); $i++) {
                            $response['data']['depature'][$i] = date("Y-m-d", strtotime($clean_search_details['data']['depature'][$i])) . 'T00:00:00';
                            $response['data']['return'][$i] = date("Y-m-d", strtotime($clean_search_details['data']['depature'][$i])) . 'T00:00:00';
                            $response['data']['from'][$i] = substr(chop(substr($clean_search_details['data']['from'][$i], -5), ')'), -3);
                            $response['data']['to'][$i] = substr(chop(substr($clean_search_details['data']['to'][$i], -5), ')'), -3);
                        }
                        break;

                    default : $response['data']['type'] = 'OneWay';
                }
                $response['data']['adult'] = $clean_search_details['data']['adult_config'];
                $response['data']['child'] = $clean_search_details['data']['child_config'];
                $response['data']['infant'] = $clean_search_details['data']['infant_config'];
                $response['data']['total_passenger'] = intval($clean_search_details['data']['adult_config'] + $clean_search_details['data']['child_config'] + $clean_search_details['data']['infant_config']);
                $response['data']['v_class'] = $clean_search_details['data']['v_class'];
                $response['data']['carrier'] = implode($clean_search_details['data']['carrier']);

                $response['data']['is_roundtrip'] = $is_roundtrip;
                $response['data']['is_multicity'] = $is_multicity;

                $this->master_search_data = $response['data'];
            } else {
                $response['status'] = false;
            }
        } else {
            $response['data'] = $this->master_search_data;
        }
        $this->search_hash = md5(serialized_data($response['data']));
        return $response;
    }

    /**
     * Search Data for day fare
     * @param unknown_type $par
     */
    function calendar_day_fare_safe_search_data($params) {

        $response['status'] = true;
        $response['data'] = array();
        //Origin
        if (isset($params['from'])) {
            $response['data']['from'] = $params['from'];
        } else {
            $response['status'] = false;
        }

        if (isset($params['to'])) {
            $response['data']['to'] = $params['to'];
        } else {
            $response['status'] = false;
        }

        if (isset($params['depature'])) {
            if (strtotime($params['depature']) < time()) {
                $response['data']['depature'] = date('Y-m-d');
            } else {
                $response['data']['depature'] = date('Y-m-d', strtotime($params['depature']));
            }
        } else {
            $response['status'] = false;
        }

        if (isset($params['session_id'])) {
            $response['data']['session_id'] = $params['session_id'];
        } else {
            $response['status'] = false;
        }
        return $response;
    }

    /**
     * Search data for fare search result
     * @param array $search_data
     */
    function calendar_safe_search_data($search_data) {
        $safe_data = array();
        //Origin
        if (isset($search_data['from']) == true and empty($search_data['from']) == false) {
            $safe_data['from'] = $search_data['from'];
        } else {
            $safe_data['from'] = 'DEL';
        }

        //Destination
        if (isset($search_data['to']) == true and empty($search_data['to']) == false) {
            $safe_data['to'] = $search_data['to'];
        } else {
            $safe_data['to'] = 'BOM';
        }

        //PreferredCarrier
        if (isset($search_data['carrier']) == true and empty($search_data['carrier']) == false) {
            $safe_data['carrier'] = implode(',', $search_data['carrier']);
        } else {
            $safe_data['carrier'] = '';
        }

        //AdultCount
        if (isset($search_data['adult']) == true and empty($search_data['adult']) == false and intval($search_data['adult']) > 0) {
            $safe_data['adult'] = intval($search_data['adult']);
        } else {
            $safe_data['adult'] = 1;
        }

        //DepartureDate
        if (isset($search_data['depature']) == true and empty($search_data['depature']) == false) {
            if (strtotime($search_data['depature']) < time()) {
                $safe_data['depature'] = date('Y-m-d');
            } else {
                $safe_data['depature'] = date('Y-m-d', strtotime($search_data['depature']));
            }
        } else {
            $safe_data['depature'] = date('Y-m-d');
        }
        //Type
        $safe_data['trip_type'] = 'OneWay';
        //CabinClass
        $safe_data['cabin'] = 'Economy';
        //ReturnDate
        $safe_data['return'] = '';
        //PromotionalPlanType
        $safe_data['PromotionalPlanType'] = 'Normal';
        return $safe_data;
    }

    /**
     * Check and tell if flight response is round way and domestic
     */
    function is_domestic_round_way_flight($flight_search_result) {
        if ($flight_search_result['Search']['SearchResult']['IsDomestic'] == true and $flight_search_result['Search']['SearchResult']['RoundTrip'] == true) {
            return true;
        }
    }

    private function way_multiplier($way_type, $domestic, $search_id = 0) {
        $way_count = 0;
        if ($way_type == 'multicity') {
            $search_data = $this->search_data($search_id);
            $way_count = intval(count($search_data['data']['from']));
        } else if ($way_type == 'oneway' || $domestic == true) {
            $way_count = 1;
        } else {
            $way_count = 2;
        }
        return $way_count;
    }

    /**
     * Makrup for search result
     * @param array $price_summary
     * @param object $currency_obj
     * @param boolean $level_one_markup
     * @param boolean $current_domain_markup
     * @param number $search_id
     */
    function update_search_markup_currency(& $price_summary, & $currency_obj, $level_one_markup = false, $current_domain_markup = true, $search_id = 0, $specific_markup_config = array(), $module='', $seg_count='') {
        if (intval($search_id) > 0) {
            $search_data = $this->search_data($search_id);
        }

        $total_pax = intval($this->master_search_data['adult_config'] + $this->master_search_data['child_config'] + $this->master_search_data['infant_config']);
        $trip_type = $this->master_search_data['trip_type'];

        $way_count = $this->way_multiplier($this->master_search_data['trip_type'], $this->master_search_data['is_domestic'], $search_id);
        //$multiplier = ($total_pax * $way_count);
        $multiplier = ($total_pax * $seg_count);
        return $this->update_markup_currency($price_summary, $currency_obj, $level_one_markup, $current_domain_markup, $multiplier, $specific_markup_config, $module);
    }

    /**
     * Markup pax wise
     */
    function update_pax_markup_currency(& $price_summary, & $currency_obj, $level_one_markup = false, $current_domain_markup = true, $pax_count = 0, $search_id = 0) {
        if (intval($search_id) > 0) {
            $search_data = $this->search_data($search_id);
        }

        if (intval($pax_count) > 0) {
            $total_pax = intval($pax_count);
        } else {
            $total_pax = intval($this->master_search_data['adult_config'] + $this->master_search_data['child_config'] + $this->master_search_data['infant_config']);
        }

        $way_count = $this->way_multiplier($this->master_search_data['trip_type'], $this->master_search_data['is_domestic']);

        $multiplier = ($total_pax * $way_count);
        return $this->update_markup_currency($price_summary, $currency_obj, $level_one_markup, $current_domain_markup, $multiplier);
    }

    /**
     * update markup currency and return summary
     */
    function update_markup_currency(& $price_summary, & $currency_obj, $level_one_markup = false, $current_domain_markup = true, $multiplier = 1, $specific_markup_config = array(), $module='') {

        #debug($price_summary);

        $markup_list = array('OfferedFare');
        $markup_summary = array();
        foreach ($price_summary as $__k => $__v) {
            if (is_numeric($__v) == true) {
                $ref_cur = $currency_obj->force_currency_conversion($__v); //Passing Value By Reference so dont remove it!!!
                $price_summary[$__k] = $ref_cur['default_value'];   //If you dont understand then go and study "Passing value by reference"

                if (in_array($__k, $markup_list)) {
                    $temp_price = $currency_obj->get_currency($__v, true, $level_one_markup, $current_domain_markup, $multiplier, $specific_markup_config);
                } elseif (is_array($__v) == false) {
                    $temp_price = $currency_obj->force_currency_conversion($__v);
                } else {
                    $temp_price['default_value'] = $__v;
                }
                $markup_summary[$__k] = $temp_price['default_value'];
            }
        }
        // debug($markup_summary);exit;
        //Markup
        //PublishedFare
        $Markup = 0;
        $price_summary['_Markup'] = 0;
        if (isset($markup_summary['OfferedFare'])) {
            $Markup = $markup_summary['OfferedFare'] - $price_summary['OfferedFare'];
            $markup_summary['PublishedFare'] = $markup_summary['PublishedFare'] + $Markup;
        }
        //Adding GST
        $gst_value = 0;
        if($module == 'b2c'){
            if($Markup > 0 ){
                $gst_details = $GLOBALS['CI']->custom_db->single_table_records('gst_master', '*', array('module' => 'flight'));
                if($gst_details['status'] == true){
                    if($gst_details['data'][0]['gst'] > 0){
                        $gst_value = ($Markup/100) * $gst_details['data'][0]['gst'];
                    }
                }
            }
        }
        

        $markup_summary['_GST'] = number_format($gst_value, 2);
        $markup_summary['_Markup'] = $Markup;

        // debug($markup_summary);
        // exit;
        return $markup_summary;
    }

    /**
     * Balu A
     * Update Netfare tag for the response
     */
    function update_net_fare($token) {
        $net_price_summary = array();
        $net_fare_tags = array('ServiceTax', 'AdditionalTxnFee', 'AgentCommission', 'TdsOnCommission', 'IncentiveEarned', 'TdsOnIncentive', 'PublishedFare', 'AirTransFee', 'Discount', 'OtherCharges', 'FuelSurcharge', 'TransactionFee', 'ReverseHandlingCharge', 'OfferedFare', 'AgentServiceCharge', 'AgentConvienceCharges');
        foreach ($token as $k => $v) {
            $fare = $v['Fare'];
            foreach ($fare as $fare_k => $fare_v) {
                if (in_array($fare_k, $net_fare_tags)) {
                    if (isset($net_price_summary[$fare_k]) == true) {
                        $net_price_summary[$fare_k] += $fare_v;
                    } else {
                        $net_price_summary[$fare_k] = $fare_v;
                    }
                }
            }
        }
        $net_price_summary['TotalCommission'] = ($net_price_summary['PublishedFare'] - $net_price_summary['OfferedFare']);
        return $net_price_summary;
    }

    /**
     * Tax price is the price for which markup should not be added
     */
    function tax_service_sum($markup_price_summary, $api_price_summary, $retain_commission = false) {
     
       // debug($markup_price_summary);exit;
        //AirlineTransFee - Not Available
        //sum of tax and service ;
        if ($retain_commission == true) {
            $commission = 0;
            $commission_tds = 0;
        } else {
            $commission = $markup_price_summary['AgentCommission'];
            $commission_tds = $markup_price_summary['AgentTdsOnCommision'];
        }
        $markup_price = 0;
        $markup_price = $markup_price_summary['OfferedFare'] - $api_price_summary['OfferedFare'];
        return ((floatval($markup_price + @$markup_price_summary['AdditionalTxnFee']) + floatval(@$markup_price_summary['Tax']) + floatval(@$markup_price_summary['OtherCharges']) + floatval(@$markup_price_summary['ServiceTax'])) - $commission + $commission_tds+floatval(@$markup_price_summary['_GST']));
      
    }

    /**
     * calculate and return total price details
     */
    function total_price($price_summary, $retain_commission = false, $currency_obj = '') {
        //debug($price_summary);exit;
        $com = 0;
        $com_tds = 0;
        if ($retain_commission == false) {
            $com = 0;
            $com_tds += floatval($currency_obj->calculate_tds($price_summary['AgentCommission']));
            $com_tds += floatval($currency_obj->calculate_tds(@$price_summary['PLBEarned']));
            $com_tds += floatval($currency_obj->calculate_tds(@$price_summary['IncentiveEarned']));
        } else {
            $com += floatval(@$price_summary['AgentCommission']);
            $com += floatval(@$price_summary['PLBEarned']);
            $com += floatval(@$price_summary['IncentiveEarned']);
            $com_tds = 0;
        }
        // echo 'heeer'.$retain_commission;
        // echo (floatval(@$price_summary['OfferedFare']) + $com + $com_tds);exit;
        return (floatval(@$price_summary['OfferedFare']) + $com + $com_tds+$price_summary['_GST']);
    }

    /**
     *
     * @param array $api_price_details
     * @param array $admin_price_details
     * @param array $agent_price_details
     * @return number
     */
    function b2b_price_details($api_price_details, $admin_price_details, $agent_price_details, $currency_obj) {
        // debug($admin_price_details);exit;
        $total_price['BaseFare'] = $api_price_details['BaseFare'];
        $total_price['_CustomerBuying'] = $agent_price_details['PublishedFare'];
        $_AgentBuying = $admin_price_details['OfferedFare'];
        $total_price['_AdminBuying'] = $api_price_details['OfferedFare'];
        $total_price['_AgentMarkup'] = $total_price['_Markup'] = $agent_price_details['OfferedFare'] - $admin_price_details['OfferedFare'];
        $total_price['_AdminMarkup'] = ($_AgentBuying - $total_price['_AdminBuying']);
        $total_price['_OrgAdminMarkup'] = $admin_price_details['_Markup'];
        $total_markup = $total_price['_OrgAdminMarkup'] + $total_price['_AgentMarkup'];
      
        $gst_value = 0;
        if($total_price['_OrgAdminMarkup'] > 0 ){
            $gst_details = $GLOBALS['CI']->custom_db->single_table_records('gst_master', '*', array('module' => 'flight'));
            if($gst_details['status'] == true){
                if($gst_details['data'][0]['gst'] > 0){
                    $gst_value = ($total_price['_OrgAdminMarkup']/100) * $gst_details['data'][0]['gst'];
                    $gst_value  = roundoff_number($gst_value);
                }
            }
        }
        $total_price['_AgentBuying'] = $admin_price_details['OfferedFare']+$gst_value;
        $total_price['_Commission'] = round($agent_price_details['PublishedFare'] - $agent_price_details['OfferedFare'], 3);
        $total_price['_tdsCommission'] = $currency_obj->calculate_tds($total_price['_Commission']); //Includes TDS ON PLB AND COMMISSION
        $total_price['_AgentEarning'] = $total_price['_Commission'] + $total_price['_Markup'] - $total_price['_tdsCommission'];
        $total_price['_TaxSum'] = $agent_price_details['PublishedFare'] - $agent_price_details['BaseFare'] + $gst_value;
        $total_price['_CustomerBuying'] =  $total_price['_CustomerBuying'] + $gst_value;
        $total_price['_BaseFare'] = $agent_price_details['BaseFare'];
        $total_price['_TotalPayable'] = $total_price['_AgentBuying'] + $total_price['_tdsCommission'];
        $total_price['_GST'] = $gst_value;
        // debug($total_price);
        // exit;
        return $total_price;
    }

    /**
     * Update Commission details
     */
    function get_commission(& $__trip_flight, & $currency_obj) {
        #cadebug($__trip_flight['FareDetails']);

        $this->commission = $currency_obj->get_commission();
        if (valid_array($this->commission) == true && intval($this->commission['admin_commission_list']['value']) > 0) {
            //update commission
            //$bus_row = array(); Preserving Row data before calculation
            $core_agent_commision = ($__trip_flight['FareDetails']['PublishedFare'] - $__trip_flight['FareDetails']['OfferedFare']);
           
            $com = $this->calculate_commission($core_agent_commision);
            
            
            $this->set_b2b_comm_tag($__trip_flight['FareDetails'], $com, $currency_obj);
        } else {
            //update commission
            $this->set_b2b_comm_tag($__trip_flight['FareDetails'], 0, $currency_obj);
        }
    }

    /**
     * Add custom commission tag for b2b only
     * @param array     s$v
     * @param number    $b2b_com
     */
    function set_b2b_comm_tag(& $v, $b2b_com = 0, $currency_obj) {
        

        $v['ORG_AgentCommission'] = $v['AgentCommission'];
        $v['ORG_TdsOnCommission'] = $v['AgentTdsOnCommision'];
        $v['ORG_OfferedFare'] = $v['OfferedFare'];

        //$admin_com = $v['AgentCommission'] - $b2b_com;
        $core_agent_commision = ($v['PublishedFare'] - $v['OfferedFare']);
        $admin_com = $core_agent_commision - $b2b_com;

        $v['OfferedFare'] = $v['OfferedFare'] + $admin_com;
        $v['AgentCommission'] = $b2b_com;
        $v['TdsOnCommission'] = $currency_obj->calculate_tds($core_agent_commision);
       
       
    }

    /**
     *
     */
    private function calculate_commission($agent_com) {
        #debug($this->commission);
       # echo $agent_com;

        $agent_com_row = $this->commission['admin_commission_list'];
        $b2b_comm = 0;
        if ($agent_com_row['value_type'] == 'percentage') {
            //%
            $b2b_comm = ($agent_com / 100) * $agent_com_row['value'];
        } else {
            //plus
            $b2b_comm = ($agent_com - $agent_com_row['value']);
        }
        #debug($b2b_comm);
        #exit;
        return number_format($b2b_comm, 2, '.', '');
    }

    /**
     * return booking form
     */
    function booking_form($isDomestic, $token = '', $token_key = '', $search_access_key = '', $promotional_plan_type = '', $booking_source = TRAVELPORT_FLIGHT_BOOKING_SOURCE) {
        $booking_form = '';

        $booking_form .= '<input type="hidden" name="is_domestic" class="" value="' . $isDomestic . '">';
        $booking_form .= '<input type="hidden" name="token[]" class="token data-access-key" value="' . $token . '">';
        $booking_form .= '<input type="hidden" name="token_key[]" class="token_key" value="' . $token_key . '">';
        $booking_form .= '<input type="hidden" name="search_access_key[]" class="search-access-key" value="' . $search_access_key . '">';
        $booking_form .= '<input type="hidden" name="promotional_plan_type[]" class="promotional-plan-type" value="' . $promotional_plan_type . '">';

        if (empty($booking_source) == false) {
            $booking_form .= '<input type="hidden" name="booking_source" class="booking-source" value="' . $booking_source . '">';
        }
        //debug($booking_form);exit;
        return $booking_form;
    }

    /**
     * booking_url to be used
     */
    function booking_url($search_id) {
        return base_url() . 'index.php/flight/booking/' . intval($search_id);
    }

    /**
     * combine and get tbo booking form
     */
    function get_form_content($form_1, $form_2) {
        $booking_form = '';
        $lcc = (($form_1['is_lcc[]'] == true || $form_2['is_lcc[]'] == true) ? true : false);
        //booking_type - decide it based on f1 is_lcc and f2 is_lcc
        $booking_type = $this->get_booking_type($lcc);
        $booking_form .= $this->booking_form(true, $form_1['token[]'], $form_1['token_key[]'], $form_1['search_access_key[]']);
        $booking_form .= $this->booking_form(true, $form_2['token[]'], $form_2['token_key[]'], $form_2['search_access_key[]']);
        return $booking_form;
    }

    /**
     * Return booking type
     */
    function get_booking_type($is_lcc) {
        if ($is_lcc) {
            return LCC_BOOKING;
        } else {
            return NON_LCC_BOOKING;
        }
    }

    /**
     * Return unserialized data
     * @param array $token      serialized data having token
     * @param array $token_key  serialized data having token key
     */
    public function unserialized_token($token, $token_key) {
        $response['data'] = array();
        $response['status'] = true;
        foreach ($token as $___k => $___v) {
            $tmp_tkn = $this->read_token($___v);
            if ($tmp_tkn != false) {
                $response['data']['token'][$___k] = $tmp_tkn;
                $response['data']['token_key'] = $token_key[$___k];
            } else {
                $response['data']['token'][$___k] = false;
            }

            if ($response['status'] == true) {
                if ($response['data']['token'][$___k] == false) {
                    $response['status'] = false;
                }
            }
        }
        return $response;
    }

    /**
     * Balu A
     * Converts API data currency to preferred currency
     * @param unknown_type $search_result
     * @param unknown_type $currency_obj
     */
    public function search_data_in_preferred_currency($search_result, $currency_obj) {
        $flights = $search_result['JourneyList'];

        $flight_list = array();
        
        $flights = $search_result['JourneyList'];
        $flight_list = array();
        foreach ($flights as $fk => $fv) {
            foreach ($fv as $list_k => $list_v) {
                $flight_list[$fk][$list_k] = $list_v;
                $flight_list[$fk][$list_k]['FareDetails'] = $this->preferred_currency_fare_object($list_v['Price'], $currency_obj);
                $flight_list[$fk][$list_k]['PassengerFareBreakdown'] = $this->preferred_currency_paxwise_breakup_object($list_v['Price']['PassengerBreakup'], $currency_obj);
            }
        }
        $search_result['JourneyList'] = $flight_list;
        return $search_result;
    }

    /**
     * Converts API data currency to preferred currency
     * Balu A
     * @param unknown_type $search_result
     * @param unknown_type $currency_obj
     */
    public function farequote_data_in_preferred_currency($fare_quote_details, $currency_obj) {
        $flight_quote = $fare_quote_details['data']['token'];
        $flight_quote_data = array();
        foreach ($flight_quote as $fk => $fv) {
            $flight_quote_data[$fk] = $fv;
            $flight_quote_data[$fk]['FareDetails'] = $this->preferred_currency_fare_object($fv['Price'], $currency_obj);
            $flight_quote_data[$fk]['PassengerFareBreakdown'] = $this->preferred_currency_paxwise_breakup_object($fv['Price']['PassengerBreakup'], $currency_obj);
            unset($flight_quote_data[$fk]['Price']);
        }
        $fare_quote_details['data']['token'] = $flight_quote_data;
        return $fare_quote_details;
    }

    /**
     * Converts Extra services Price details to preferred currency
     * @param unknown_type $extra_services
     */
    private function extra_services_in_preferred_currency($extra_services, $journey_type) {
        $currency_obj = new Currency(array('module_type' => 'flight', 'from' => get_api_data_currency(), 'to' => get_application_currency_preference()));

        //Baggage
        if (isset($extra_services['Baggage']) == true && valid_array($extra_services['Baggage']) == true) {
            foreach ($extra_services['Baggage'] as $bag_k => $bag_v) {
                foreach ($bag_v as $bd_k => $bd_v) {
                    //Convert the Price to prefeerd currency
                    $Price = $extra_services['Baggage'][$bag_k][$bd_k]['Price'];
                    $Price = get_converted_currency_value($currency_obj->force_currency_conversion($Price));
                    $extra_services['Baggage'][$bag_k][$bd_k]['Price'] = $Price;
                    $extra_services['Baggage'][$bag_k][$bd_k]['JourneyType'] = $journey_type;
                }
            }
        }

        //Meals
        if (isset($extra_services['Meals']) == true && valid_array($extra_services['Meals']) == true) {
            foreach ($extra_services['Meals'] as $meal_k => $meal_v) {
                foreach ($meal_v as $md_k => $md_v) {
                    //Convert the Price to prefeerd currency
                    $Price = $extra_services['Meals'][$meal_k][$md_k]['Price'];
                    $Price = get_converted_currency_value($currency_obj->force_currency_conversion($Price));
                    $extra_services['Meals'][$meal_k][$md_k]['Price'] = $Price;
                    $extra_services['Meals'][$meal_k][$md_k]['JourneyType'] = $journey_type;
                }
            }
        }
        //Seat
        if (isset($extra_services['Seat']) == true && valid_array($extra_services['Seat']) == true) {

            foreach ($extra_services['Seat'] as $seat_k => $seat_v) {
                foreach ($seat_v as $sd_k => $sd_v) {
                    foreach ($sd_v as $seat_index => $seat_value) {
                        //Convert the Price to prefeerd currency
                        $Price = $seat_value['Price'];
                        $Price = get_converted_currency_value($currency_obj->force_currency_conversion($Price));
                        $extra_services['Seat'][$seat_k][$sd_k][$seat_index]['Price'] = $Price;
                        $extra_services['Seat'][$seat_k][$sd_k][$seat_index]['JourneyType'] = $journey_type;
                    }
                }
            }
        }

        //Meal Preference
        if (isset($extra_services['MealPreference']) == true && valid_array($extra_services['MealPreference']) == true) {
            foreach ($extra_services['MealPreference'] as $meal_k => $meal_v) {
                foreach ($meal_v as $md_k => $md_v) {
                    $extra_services['MealPreference'][$meal_k][$md_k]['JourneyType'] = $journey_type;
                }
            }
        }

        //Seat Preference
        if (isset($extra_services['SeatPreference']) == true && valid_array($extra_services['SeatPreference']) == true) {
            foreach ($extra_services['SeatPreference'] as $seat_k => $seat_v) {
                foreach ($seat_v as $sd_k => $sd_v) {
                    $extra_services['SeatPreference'][$seat_k][$sd_k]['JourneyType'] = $journey_type;
                }
            }
        }

        return $extra_services;
    }

    /**
     * Balu A
     * Converts Display currency to application currency
     * @param unknown_type $fare_details
     * @param unknown_type $currency_obj
     * @param unknown_type $module
     */
    public function convert_token_to_application_currency($token, $currency_obj, $module) {
        $token_details = $token;
        $token = array();
        $application_default_currency = admin_base_currency();
        foreach ($token_details as $tk => $tv) {
            $token[$tk] = $tv;
            $temp_fare_details = $tv['FareDetails'];
            //Fare Details
            $FareDetails = array();
            if ($module == 'b2c') {
                $PriceDetails = $temp_fare_details[$module . '_PriceDetails'];

                $FareDetails['b2c_PriceDetails']['BaseFare'] = get_converted_currency_value($currency_obj->force_currency_conversion($PriceDetails['BaseFare']));
                $FareDetails['b2c_PriceDetails']['TotalTax'] = get_converted_currency_value($currency_obj->force_currency_conversion($PriceDetails['TotalTax']));
                $FareDetails['b2c_PriceDetails']['TotalFare'] = get_converted_currency_value($currency_obj->force_currency_conversion($PriceDetails['TotalFare']));
                $FareDetails['b2c_PriceDetails']['Currency'] = $application_default_currency;
                $FareDetails['b2c_PriceDetails']['CurrencySymbol'] = $currency_obj->get_currency_symbol($currency_obj->to_currency);
            } else if ($module == 'b2b') {
                $PriceDetails = $temp_fare_details[$module . '_PriceDetails'];

                $FareDetails['b2b_PriceDetails']['BaseFare'] = get_converted_currency_value($currency_obj->force_currency_conversion($PriceDetails['BaseFare']));
                $FareDetails['b2b_PriceDetails']['_CustomerBuying'] = get_converted_currency_value($currency_obj->force_currency_conversion($PriceDetails['_CustomerBuying']));
                $FareDetails['b2b_PriceDetails']['_AgentBuying'] = get_converted_currency_value($currency_obj->force_currency_conversion($PriceDetails['_AgentBuying']));
                $FareDetails['b2b_PriceDetails']['_AdminBuying'] = get_converted_currency_value($currency_obj->force_currency_conversion($PriceDetails['_AdminBuying']));
                $FareDetails['b2b_PriceDetails']['_Markup'] = get_converted_currency_value($currency_obj->force_currency_conversion($PriceDetails['_Markup']));
                $FareDetails['b2b_PriceDetails']['_AgentMarkup'] = get_converted_currency_value($currency_obj->force_currency_conversion($PriceDetails['_AgentMarkup']));
                $FareDetails['b2b_PriceDetails']['_AdminMarkup'] = get_converted_currency_value($currency_obj->force_currency_conversion($PriceDetails['_AdminMarkup']));
                $FareDetails['b2b_PriceDetails']['_Commission'] = get_converted_currency_value($currency_obj->force_currency_conversion($PriceDetails['_Commission']));
                $FareDetails['b2b_PriceDetails']['_tdsCommission'] = get_converted_currency_value($currency_obj->force_currency_conversion($PriceDetails['_tdsCommission']));
                $FareDetails['b2b_PriceDetails']['_AgentEarning'] = get_converted_currency_value($currency_obj->force_currency_conversion($PriceDetails['_AgentEarning']));
                $FareDetails['b2b_PriceDetails']['_TaxSum'] = get_converted_currency_value($currency_obj->force_currency_conversion($PriceDetails['_TaxSum']));
                $FareDetails['b2b_PriceDetails']['_BaseFare'] = get_converted_currency_value($currency_obj->force_currency_conversion($PriceDetails['_BaseFare']));
                $FareDetails['b2b_PriceDetails']['_TotalPayable'] = get_converted_currency_value($currency_obj->force_currency_conversion($PriceDetails['_TotalPayable']));
                $FareDetails['b2b_PriceDetails']['Currency'] = $application_default_currency;
                $FareDetails['b2b_PriceDetails']['CurrencySymbol'] = $currency_obj->get_currency_symbol($currency_obj->to_currency);
                
            }

            $FareDetails['api_PriceDetails'] = $this->preferred_currency_fare_object($temp_fare_details['api_PriceDetails'], $currency_obj, $application_default_currency);
            $token[$tk]['FareDetails'] = $FareDetails;
            //Passenger Breakdown
            $token[$tk]['PassengerFareBreakdown'] = $this->preferred_currency_paxwise_breakup_object($tv['PassengerFareBreakdown'], $currency_obj);
        }
        return $token;
    }

    /**
     * Convert Extra Services to Applicatiob Currency
     * @param unknown_type $token
     * @param unknown_type $currency_obj
     */
    public function convert_extra_services_to_application_currency($extra_service_details, $currency_obj) {
        //Baggage
        
        if (isset($extra_service_details['data']['ExtraServiceDetails']['Baggage']) == true && valid_array($extra_service_details['data']['ExtraServiceDetails']['Baggage']) == true) {

            foreach ($extra_service_details['data']['ExtraServiceDetails']['Baggage'] as $bag_k => $bag_v) {
                foreach ($bag_v as $bd_k => $bd_v) {
                    //Convert the Price to application currency
                    $Price = $bd_v['Price'];
                    $Price = get_converted_currency_value($currency_obj->force_currency_conversion($Price));
                    $extra_service_details['data']['ExtraServiceDetails']['Baggage'][$bag_k][$bd_k]['Price'] = $Price;
                }
            }
        }
      
        if (isset($extra_service_details['data']['ExtraServiceDetails']['Seat']) == true && valid_array($extra_service_details['data']['ExtraServiceDetails']['Seat']) == true) {

            foreach ($extra_service_details['data']['ExtraServiceDetails']['Seat'] as $Seat_k => $Seat_v) {
                foreach ($Seat_v as $st_k => $st_v) {
                    
                   foreach($st_v as $seat_ind_k => $seat_ind_v) {
                       
                    //Convert the Price to application currency
                    $Price = $seat_ind_v['Price'];
                    $Price = get_converted_currency_value($currency_obj->force_currency_conversion($Price));
                    $extra_service_details['data']['ExtraServiceDetails']['Seat'][$Seat_k][$st_k][$seat_ind_k]['Price'] = $Price;
                   }
                }
            }
        }

        //Meals
        if (isset($extra_service_details['data']['ExtraServiceDetails']['Meals']) == true && valid_array($extra_service_details['data']['ExtraServiceDetails']['Meals']) == true) {

            foreach ($extra_service_details['data']['ExtraServiceDetails']['Meals'] as $meal_k => $meal_v) {
                foreach ($meal_v as $md_k => $md_v) {
                    //Convert the Price to application currency
                    $Price = $md_v['Price'];
                    $Price = get_converted_currency_value($currency_obj->force_currency_conversion($Price));
                    $extra_service_details['data']['ExtraServiceDetails']['Meals'][$meal_k][$md_k]['Price'] = $Price;
                }
            }
        }
        return $extra_service_details;
    }

    /**
     * Formates Search Response
     * @param array $search_result
     * @param string $module(B2C/B2B)
     */
    public function format_search_response($search_result, $currency_obj, $search_id, $module, $from_cache = false, $search_hash = '') {

        $formatted_search_data = array();
        $journey_summary = $this->extract_journey_details($search_id);
        
        //Flight List
        $flights = $search_result['JourneyList'];
        $formatted_flight_list = array();
        $ins_token = true;
        $formatted_flight_list = $this->extract_flight_details($flights, $currency_obj, $search_id, $module, $ins_token);
        //debug($formatted_flight_list);
        //Assigning the Data
        $formatted_search_data['booking_url'] = $this->booking_url(intval($search_id));
        $formatted_search_data['data']['JourneySummary'] = $journey_summary;
        $formatted_search_data['data']['Flights'] = $formatted_flight_list;

        // set session expiry time
        $session_expiry_details = $this->set_flight_search_session_expiry($from_cache, $search_hash);
        $formatted_search_data['session_expiry_details'] = $session_expiry_details;

        #debug($formatted_search_data);
       # exit;
        return $formatted_search_data;
    }

    /**
     * Extracts Journey Details
     * @param unknown_type $search_result
     */
    private function extract_journey_details($search_id) {
        $search_data = $this->search_data($search_id);
        $search_data = $search_data['data'];
        $PassengerConfig = array();
        $PassengerConfig['Adult'] = intval($search_data['adult']);
        $PassengerConfig['Child'] = intval($search_data['child']);
        $PassengerConfig['Infant'] = intval($search_data['infant']);
        $PassengerConfig['TotalPassenger'] = intval($search_data['total_passenger']);
        //Journey Summary
        $journey_summary = array();

        $origin = is_array($search_data['from']) ? $search_data['from'][0] : $search_data['from'];
        $destination = is_array($search_data['to']) ? end($search_data['to']) : $search_data['to'];


        $journey_summary['Origin'] = $origin;
        $journey_summary['Destination'] = $destination;

        $journey_summary['IsDomestic'] = $search_data['is_domestic'];
        $journey_summary['RoundTrip'] = $search_data['is_roundtrip'];
        $journey_summary['MultiCity'] = $search_data['is_multicity'];

        $journey_summary['PassengerConfig'] = $PassengerConfig;
        if ($journey_summary['IsDomestic'] == true && $journey_summary['RoundTrip'] == true) {
            $is_domestic_roundway = true;
        } else {
            $is_domestic_roundway = false;
        }
        $journey_summary['IsDomesticRoundway'] = $is_domestic_roundway;
        return $journey_summary;
    }

    /**
     * Extracts Flight Details
     *
     */
    public function extract_flight_details($flights, $currency_obj, $search_id, $module, $ins_token = false) {
        $formatted_flight_list = array();
        //Token Details
        $token = array(); //This will be stored in local file so less data gets transmitted
        $this->ins_token_file = time() . rand(100, 10000);
        foreach ($flights as $fk => $fv) {
            $formatted_flight_list[$fk] = $this->extract_flight_segment_fare_details($fv, $currency_obj, $search_id, $module, $ins_token, $token, $fk);
        }
        $ins_token === true ? $this->save_token($token) : '';
        return $formatted_flight_list;
    }

    /**
     * Extracts Flight Segment Details and Fare Details
     */
    public function extract_flight_segment_fare_details($flights, $currency_obj, $search_id, $module, $ins_token = false, & $token = array(), $flight_index = 0) {
      
        //debug($flights);exit;
        $flights = force_multple_data_format($flights);
       
        $flight_list = array();
        foreach ($flights as $list_k => $list_v) {
            //debug($list_v);exit;
            //Pushing data into the Token
            if ($ins_token === true) {
                $tkn_key = $flight_index . $list_k;
                $this->push_token($list_v, $token, $tkn_key);
            }
            $flight_list[$list_k]['AirlineRemark'] = $this->filter_airline_remark(@$list_v['Attr']['AirlineRemark'], $module);
            $flight_list[$list_k]['FareDetails'] = $this->get_fare_object($list_v, $currency_obj, $search_id, $module);
            $flight_list[$list_k]['PassengerFareBreakdown'] = $list_v['PassengerFareBreakdown'];
            $segments = $this->extract_segment_details($list_v['FlightDetails']['Details']);
            $flight_list[$list_k]['SegmentSummary'] = $segments['segment_summary'];
            $flight_list[$list_k]['SegmentDetails'] = $segments['segment_full_details'];
            $flight_list[$list_k]['ProvabAuthKey'] = $list_v['ResultToken'];

            $flight_list[$list_k]['booking_source'] = TRAVELPORT_FLIGHT_BOOKING_SOURCE;

            //Hold Ticket
            if (isset($list_v['HoldTicket']) == true) {
                $hold_ticket = $list_v['HoldTicket'];
            } else {
                $hold_ticket = false;
            }
            $flight_list[$list_k]['HoldTicket'] = $hold_ticket;

            if (isset($list_v['Token']) == true) {
                $flight_list[$list_k]['Token'] = $list_v['Token'];
            }
            if (isset($list_v['TokenKey']) == true) {
                $flight_list[$list_k]['TokenKey'] = $list_v['TokenKey'];
            }
            if (isset($list_v['Attr']) == true) {
                $flight_list[$list_k]['Attr'] = $list_v['Attr'];
            }
        }
        
        return $flight_list;
    }

    /**
     * 
     * Enter description here ...
     */
    private function filter_airline_remark($AirlineRemark, $module) {
        $filtered_airline_remark = '';
        if ($module == 'b2c') {
            if (preg_match_all('~\b(special|bag|meal|meals)\b~i', $AirlineRemark) == true && preg_match_all('~\b(Series|operated|commissionable)\b~i', $AirlineRemark) == false) {
                $filtered_airline_remark = $AirlineRemark;
            }
        } else if ($module == 'b2b') {
            if (preg_match_all('~\b(special|bag|meal|meals)\b~i', $AirlineRemark) == true && preg_match_all('~\b(Series|operated)\b~i', $AirlineRemark) == false) {
                $filtered_airline_remark = $AirlineRemark;
            }
        }
        return $filtered_airline_remark;
    }

    /**
     * Merges Flight Segment Details and Fare Details
     */
    public function merge_flight_segment_fare_details($flight_details) {
        $flight_pre_booking_summery = array();
        $PassengerFareBreakdown = array();
        $SegmentDetails = array();
        $SegmentSummary = array();
        $FareDetails = $this->merge_fare_details($flight_details);
        $PassengerFareBreakdown = $this->merge_passenger_fare_break_down($flight_details);
        $SegmentDetails = $this->merge_segment_details($flight_details);
        $SegmentSummary = $this->merge_segment_summary($flight_details);

        $flight_pre_booking_summery['FareDetails'] = $FareDetails;
        $flight_pre_booking_summery['PassengerFareBreakdown'] = $PassengerFareBreakdown;
        $flight_pre_booking_summery['SegmentDetails'] = $SegmentDetails;
        $flight_pre_booking_summery['SegmentSummary'] = $SegmentSummary;
        $flight_pre_booking_summery['HoldTicket'] = $flight_details[0]['HoldTicket'];
        return $flight_pre_booking_summery;
    }

    /**
     * Merges Fare Details
     * @param unknown_type $flight_details
     */
    public function merge_fare_details($flight_details) {
        // debug($flight_details);exit;
        $FareDetails = array();
        $temp_fare_details = group_array_column($flight_details, 'FareDetails');
        $APIPriceDetails = array_merge_numeric_values(group_array_column($temp_fare_details, 'api_PriceDetails'));
        if (isset($temp_fare_details[0]['b2c_PriceDetails']) == true) {//B2C
            $B2CPriceDetails = array_merge_numeric_values(group_array_column($temp_fare_details, 'b2c_PriceDetails'));
            $FareDetails['b2c_PriceDetails'] = $B2CPriceDetails;
        } elseif (isset($temp_fare_details[0]['b2b_PriceDetails']) == true) {//B2B
            $B2BPriceDetails = array_merge_numeric_values(group_array_column($temp_fare_details, 'b2b_PriceDetails'));
            $FareDetails['b2b_PriceDetails'] = $B2BPriceDetails;
        }
        $FareDetails['api_PriceDetails'] = $APIPriceDetails;
        return $FareDetails;
    }

    /**
     * Merge Passenger Breakdown details
     */
    public function merge_passenger_fare_break_down($flight_details) {
        $PassengerFareBreakdown = array();
        $tmp_fare_breakdown = group_array_column($flight_details, 'PassengerFareBreakdown');
        foreach ($tmp_fare_breakdown as $k => $v) {
            foreach ($v as $pax_k => $pax_v) {
                $pax_type = $pax_k;
                if (isset($PassengerFareBreakdown[$pax_type]) == false) {
                    $PassengerFareBreakdown[$pax_type]['PassengerType'] = $pax_type;
                    $PassengerFareBreakdown[$pax_type]['Count'] = $pax_v['PassengerCount'];
                    $PassengerFareBreakdown[$pax_type]['BaseFare'] = $pax_v['BaseFare'];
                } else {
                    $PassengerFareBreakdown[$pax_type]['BaseFare'] += $pax_v['BaseFare'];
                }
            }
        }
        return $PassengerFareBreakdown;
    }

    /**
     * Merges Flight Segment Details
     * @param unknown_type $flight_details
     */
    public function merge_segment_details($flight_details) {
        $SegmentDetails = array();
        foreach ($flight_details as $k => $v) {
            $SegmentDetails = array_merge($SegmentDetails, $v['SegmentDetails']);
        }
        return $SegmentDetails;
    }

    /**
     * Merges Flight Segment Summery
     * @param unknown_type $flight_details
     */
    public function merge_segment_summary($flight_details) {
        $SegmentSummary = array();
        foreach ($flight_details as $k => $v) {
            $SegmentSummary = array_merge($SegmentSummary, $v['SegmentSummary']);
        }
        return $SegmentSummary;
    }

    /**
     * Fare Details: Calcualtes Markup and Commission
     * @param unknown_type $flight_details
     * @param unknown_type $currency_obj
     * @param unknown_type $module
     * @param unknown_type $search_id
     * @return unknown
     */
    private function get_fare_object($flight_details, $currency_obj, $search_id, $module) {

        $FareDetails = array();
        $b2c_price_details = array();
        $b2b_fare_details = array();
        $seg_count = 0;
        foreach($flight_details['FlightDetails']['Details'] as $details_key => $f_details){
            $seg_count += count($f_details);
        }
          
        $api_price_details = $flight_details['FareDetails'];
        //debug($api_price_details);exit;
        $currency_symbol = $currency_obj->get_currency_symbol($currency_obj->to_currency);
        //SPECIFIC MARKUP CONFIG DETAILS
        $specific_markup_config = array();
        $specific_markup_config = $this->get_airline_specific_markup_config($flight_details['FlightDetails']['Details']); //Get the Airline code for setting airline-wise markup
        //Updating the Commission
        if ($module == 'b2c') {
            //B2C
            $admin_price_details = $this->update_search_markup_currency($flight_details['FareDetails'], $currency_obj, false, true, $search_id, $specific_markup_config,'b2c', $seg_count); //B2c:DONT CHANGE
            //debug($admin_price_details);exit;
            $o_Total_Tax = ($this->tax_service_sum($admin_price_details, $flight_details['FareDetails']));
            $o_Total_Fare = ($this->total_price($admin_price_details, false, $currency_obj));
            $b2c_price_details['BaseFare'] = $api_price_details['BaseFare'];
            $b2c_price_details['TotalTax'] = $o_Total_Tax;
            $b2c_price_details['TotalFare'] = $o_Total_Fare;
            $b2c_price_details['Currency'] = $api_price_details['Currency'];
            $b2c_price_details['CurrencySymbol'] = $currency_symbol;
            $b2c_price_details['_GST'] = $admin_price_details['_GST'];

            $FareDetails['b2c_PriceDetails'] = $b2c_price_details; //B2C PRICE DETAILS
        } else if ($module == 'b2b') {
            //B2B
            //Updating the Commission
            #debug($api_price_details);

            $this->get_commission($flight_details, $currency_obj);
            $admin_price_details = $this->update_search_markup_currency($flight_details['FareDetails'], $currency_obj, true, false, $search_id, $specific_markup_config,'', $seg_count); //B2B:DONT CHANGE
            #debug( $admin_price_details);

            $agent_price_details = $this->update_search_markup_currency($flight_details['FareDetails'], $currency_obj, true, true, $search_id, $specific_markup_config,'', $seg_count);
            #debug(  $agent_price_details);
//            debug($admin_price_details);debug($agent_price_details);exit;
            $b2b_price_details = $this->b2b_price_details($api_price_details, $admin_price_details, $agent_price_details, $currency_obj);
            // debug($b2b_price_details);
            // exit;
            $b2b_price_details['Currency'] = $api_price_details['Currency'];
            $b2b_price_details['CurrencySymbol'] = $currency_symbol;
            $FareDetails['b2b_PriceDetails'] = $b2b_price_details; //B2B PRICE DETAILS
        }
        $FareDetails['api_PriceDetails'] = $api_price_details; //API PRICE DETAILS
        return $FareDetails;
    }

    /**
     * Balu A
     * Fare Details
     * Converts the API Currency to Preferred Currency
     * @param unknown_type $FareDetails
     */
    private function preferred_currency_fare_object($fare_details, $currency_obj, $default_currency = '') {
        
        if (isset($fare_details['TotalDisplayFare']) == true && isset($fare_details['PriceBreakup']) == true) {
            $base_fare = $fare_details['PriceBreakup']['BasicFare'];
            $tax = $fare_details['PriceBreakup']['Tax'];
            $published_fare = $fare_details['TotalDisplayFare'];
            $agent_commission = $fare_details['PriceBreakup']['AgentCommission'];
            $agent_tds_on_commission = $fare_details['PriceBreakup']['AgentTdsOnCommision'];
        } else {
            $base_fare = $fare_details['BaseFare'];
            $tax = $fare_details['Tax'];
            $published_fare = $fare_details['PublishedFare'];
            $agent_commission = $fare_details['AgentCommission'];
            $agent_tds_on_commission = $fare_details['AgentTdsOnCommision'];
        }
        #debug($fare_details);

        $FareDetails = array();
        $FareDetails['Currency'] = empty($default_currency) == false ? $default_currency : get_application_currency_preference();
        $FareDetails['BaseFare'] = get_converted_currency_value($currency_obj->force_currency_conversion($base_fare));
        $FareDetails['Tax'] = get_converted_currency_value($currency_obj->force_currency_conversion($tax));
        $FareDetails['PublishedFare'] = get_converted_currency_value($currency_obj->force_currency_conversion($published_fare));
        $FareDetails['AgentCommission'] = get_converted_currency_value($currency_obj->force_currency_conversion($agent_commission));
        $FareDetails['AgentTdsOnCommision'] = get_converted_currency_value($currency_obj->force_currency_conversion($agent_tds_on_commission));
        $OfferedFare = ($FareDetails['PublishedFare'] - $FareDetails['AgentCommission']);
        $FareDetails['OfferedFare'] = $OfferedFare;

        #debug($FareDetails);
        #exit;
        return $FareDetails;
    }

    /**
     * Balu A
     * Passenger Fare Breakdown details
     * Converts the API Currency to Preferred Currency
     * @param unknown_type $FareDetails
     */
    public function preferred_currency_paxwise_breakup_object($fare_details, $currency_obj) {
        $PassengerFareBreakdown = array();
        foreach ($fare_details as $k => $v) {
            $PassengerFareBreakdown[$k] = $v;
            if (isset($v['BasePrice'])) {
                $base_fare = $PassengerFareBreakdown[$k]['BasePrice'];
                unset($PassengerFareBreakdown[$k]['BasePrice']);
            } else {
                $base_fare = $PassengerFareBreakdown[$k]['BaseFare'];
            }
            $PassengerFareBreakdown[$k]['BaseFare'] = get_converted_currency_value($currency_obj->force_currency_conversion($base_fare));
        }
        return $PassengerFareBreakdown;
    }

    /**
     * Returns First segment Airline Code to set the markup based on Airline
     */
    public function get_airline_specific_markup_config($segment_details) {
        $specific_markup_config = array();
        if (isset($segment_details[0][0]['OperatorCode'])) {
            $airline_code = $segment_details[0][0]['OperatorCode'];
        } else {
            $airline_code = $segment_details[0][0]['AirlineDetails']['AirlineCode'];
        }
        $category = 'airline_wise';
        $specific_markup_config[] = array('category' => $category, 'ref_id' => $airline_code);
        return $specific_markup_config;
    }

    /**
     * Formates Segment Summary
     * @param unknown_type $segment_details
     */
    public function extract_segment_details($segment_details) {
        $segment_summary = array();
        $segment_full_details = array();
        foreach ($segment_details as $seg_k => $seg_v) {
            $this->update_segment_details($seg_v);
            
            //Segment Summry
            $OriginDetails = $seg_v[0]['Origin'];
            $AirlineDetails = $seg_v[0]['AirlineDetails'];
            $OriginDetails['_DateTime'] = local_time($OriginDetails['DateTime']);
            $OriginDetails['_Date'] = local_date_new($OriginDetails['DateTime']);
            $last_segment_details = end($seg_v);
            $DestinationDetails = $last_segment_details['Destination'];
            $DestinationDetails['_DateTime'] = local_time($DestinationDetails['DateTime']);
            $DestinationDetails['_Date'] = local_date_new($DestinationDetails['DateTime']);
            $total_stops = (count($seg_v) - 1);
            $total_duaration = $this->segment_total_duration($seg_v);
            $segment_summary[$seg_k]['AirlineDetails'] = $AirlineDetails;
            $segment_summary[$seg_k]['OriginDetails'] = $OriginDetails;
            $segment_summary[$seg_k]['DestinationDetails'] = $DestinationDetails;
            $segment_summary[$seg_k]['TotalStops'] = $total_stops;
            $segment_summary[$seg_k]['TotalDuaration'] = $total_duaration;
            //Segment Details
            foreach ($seg_v as $seg_details_k => $seg_details_v) {
                // debug($seg_details_v);exit;
                //Origin Details
                $AirlineDetails = $seg_details_v['AirlineDetails'];
                $OriginDetails = $seg_details_v['Origin'];

                $OriginDetails['_DateTime'] = local_time($OriginDetails['DateTime']);
                $OriginDetails['_Date'] = local_date_new($OriginDetails['DateTime']);
                //Destination Details
                $DestinationDetails = $seg_details_v['Destination'];
                $DestinationDetails['_DateTime'] = local_time($DestinationDetails['DateTime']);
                $DestinationDetails['_Date'] = local_date_new($DestinationDetails['DateTime']);
                $SegmentDuration = get_time_duration_label($seg_details_v['SegmentDuration'] * 60); //Converting into seconds

                if (isset($seg_v[$seg_details_k + 1]) == true) {
                    $next_seg_info = $seg_v[$seg_details_k + 1];
                    $WaitingTime = (get_time_duration_label(calculate_duration($seg_details_v['Destination']['DateTime'], $next_seg_info['Origin']['DateTime'])));
                }
                $Baggage = '';
                $CabinBaggage = '';
                if (valid_array($seg_details_v['Attr']) == true) {
                    $Baggage = @$seg_details_v['Attr']['Baggage'];
                    $CabinBaggage = @$seg_details_v['Attr']['CabinBaggage'];
                    if (isset($seg_details_v['Attr']['AvailableSeats'])) {
                        $segment_full_details[$seg_k][$seg_details_k]['AvailableSeats'] = $seg_details_v['Attr']['AvailableSeats'];
                    }
                }
                $segment_full_details[$seg_k][$seg_details_k]['Baggage'] = $Baggage;
                $segment_full_details[$seg_k][$seg_details_k]['CabinBaggage'] = $CabinBaggage;
                $segment_full_details[$seg_k][$seg_details_k]['AirlineDetails'] = $AirlineDetails;
                $segment_full_details[$seg_k][$seg_details_k]['OriginDetails'] = $OriginDetails;
                $segment_full_details[$seg_k][$seg_details_k]['DestinationDetails'] = $DestinationDetails;
                $segment_full_details[$seg_k][$seg_details_k]['SegmentDuration'] = $SegmentDuration;
                $segment_full_details[$seg_k][$seg_details_k]['StopOver'] = @$seg_details_v['stop_over'];
                if (isset($WaitingTime) == true) {
                    $segment_full_details[$seg_k][$seg_details_k]['WaitingTime'] = $WaitingTime;
                }
            }
        }
        // exit;
        $data['segment_summary'] = $segment_summary;
        $data['segment_full_details'] = $segment_full_details;
        return $data;
    }

    /**
     * Segments  Duration
     */
    private function update_segment_details(& $segments) {
        foreach ($segments as $k => & $v) {
           
            $v['SegmentDuration'] = $this->flight_segment_duration($v['Origin']['AirportCode'], $v['Destination']['AirportCode'], $v['Origin']['DateTime'], $v['Destination']['DateTime']);
            $AirlineDetails = array();
            $AirlineDetails['AirlineCode'] = $v['OperatorCode'];
            $AirlineDetails['AirlineName'] = $v['OperatorName'];
            $AirlineDetails['FlightNumber'] = $v['FlightNumber'];
            $AirlineDetails['FareClass'] = $v['CabinClass'];
            $AirlineDetails['FareClassCode'] = @$v['booking_code'];
            unset($v['OperatorCode'], $v['OperatorName'], $v['FlightNumber'], $v['CabinClass'], $v['DisplayOperatorCode']);
            $v['AirlineDetails'] = $AirlineDetails;
        }
    }

    /**
     * Segments Total Duration
     */
    private function segment_total_duration($segments) {
        $total_duration = 0;
        foreach ($segments as $k => $v) {
            $total_duration += $v['SegmentDuration'];
            //adding waiting time
            if (isset($segments[$k + 1]['Origin']) == true) {
                $total_duration += $this->wating_segment_time($v['Destination']['AirportCode'], $segments[$k + 1]['Origin']['AirportCode'], $v['Destination']['DateTime'], $segments[$k + 1]['Origin']['DateTime']);
            }
        }
        $total_duration = ($total_duration * 60); //Converting into seconds
        return get_time_duration_label($total_duration);
    }

    /**
     * Balu A
     * Calculates the flight segment duration based on airport time zone offset
     * @param $departure_airport_code
     * @param $arrival_airport_code
     * @param $departure_datetime
     * @param $arrival_datetime
     */
    private function flight_segment_duration($departure_airport_code, $arrival_airport_code, $departure_datetime, $arrival_datetime) {
        $departure_datetime = date('Y-m-d H:i:s', strtotime($departure_datetime));
        $arrival_datetime = date('Y-m-d H:i:s', strtotime($arrival_datetime));
        //Get TimeZone of Departure and Arrival Airport
        $departure_timezone_offset = $this->get_airport_timezone_offset($departure_airport_code, $departure_datetime);
        $arrival_timezone_offset = $this->get_airport_timezone_offset($arrival_airport_code, $arrival_datetime);
       
        //Converting TimeZone to Minutes
        $departure_timezone_offset = $this->convert_timezone_offset_to_minutes($departure_timezone_offset);
        $arrival_timezone_offset = $this->convert_timezone_offset_to_minutes($arrival_timezone_offset);
        //Getting Total time difference between 2 airports
        $timezone_offset = ($departure_timezone_offset - $arrival_timezone_offset);
        // echo 'timezone '.$timezone_offset;
        // echo "<br/>";
        //Calculating Total Duration Time
        $segment_duration = calculate_duration($departure_datetime, $arrival_datetime);
  // echo 'duration '.$segment_duration;
  //       echo "<br/>";
        //Converting into minutes
        $segment_duration = ($segment_duration) / 60; //Converting int minutes
        //Updating the total duration with time zone offset difference
        $segment_duration = ($segment_duration + $timezone_offset);

        return $segment_duration;
    }

    /**
     * Balu A
     * Returns Airport timezone offset
     * @param $airport_code
     */
    private function get_airport_timezone_offset($airport_code, $journey_date) {
        //FIXME: cache the data
        $journey_month = date('m', strtotime($journey_date));
        $query = 'select FAL.airport_code,FAT.start_month,FAT.end_month,FAT.timezone_offset from flight_airport_list FAL
                    join flight_airport_timezone_offset FAT on FAT.flight_airport_list_fk=FAL.origin
                    where airport_code = "' . $airport_code . '" and (start_month<=' . $journey_month . ' or end_month>=' . $journey_month . ')
                    order by 
                    CASE
                    WHEN start_month    = ' . $journey_month . ' THEN 1
                    WHEN end_month  = ' . $journey_month . ' THEN 2
                    ELSE 3 END';
                    // echo $query;
                    // echo "<br/>";
        $timezone_offset = $this->CI->db->query($query)->result_array();
        return $timezone_offset[0]['timezone_offset'];
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

    /**
     * Save token and cache the data
     * @param array $token
     */
    private function save_token($token) {
        $file = DOMAIN_TMP_UPLOAD_DIR . $this->ins_token_file . '.json';
        file_put_contents($file, json_encode($token));
    }

    /**
     * adds token and token key to flight and push data to token for caching
     * @param array $flight Flight for which token and token key has to be generated
     * @param array $token  Token array for caching
     * @param string $key   Key to be used for caching
     */
    private function push_token(& $flight, & $token, $key) {
        //push data inside token before adding token and key values
        $token[$key] = $flight;

        //Adding token and token key
        $flight['Token'] = serialized_data($this->ins_token_file . DB_SAFE_SEPARATOR . $key);
        $flight['TokenKey'] = md5($flight['Token']);
    }

    public function read_token($token_key) {
        $token_key = explode(DB_SAFE_SEPARATOR, unserialized_data($token_key));
        if (valid_array($token_key) == true) {
            $file = DOMAIN_TMP_UPLOAD_DIR . $token_key[0] . '.json'; //File name
            $index = $token_key[1]; // access key

            if (file_exists($file) == true) {
                $token_content = file_get_contents($file);
                if (empty($token_content) == false) {
                    $token = json_decode($token_content, true);
                    if (valid_array($token) == true && isset($token[$index]) == true) {
                        return $token[$index];
                    } else {
                        return false;
                        echo 'Token data not found';
                        exit;
                    }
                } else {
                    return false;
                    echo 'Invalid File access';
                    exit;
                }
            } else {
                return false;
                echo 'Invalid Token access';
                exit;
            }
        } else {
            return false;
            echo 'Invalid Token passed';
            exit;
        }
    }

    /**
     * parse data according to voucher needs
     * @param array $data
     */
    function parse_voucher_data($data) {
        $response = $data;
        return $response;
    }

    function group_segment_indicator($cur_WSSegment) {
        $segment_indicator_group = array();
        $current_SegmentIndicator = $cur_WSSegment[0]['SegmentIndicator'];
        foreach ($cur_WSSegment as $__k => $__v) {
            if ($__v['SegmentIndicator'] != $current_SegmentIndicator) {
                $current_SegmentIndicator = intval($__v['SegmentIndicator']);
            }
            $segment_indicator_group[$current_SegmentIndicator][] = $__v;
        }
        return $segment_indicator_group;
    }

    function get_trip_segment_summary($private_trip_indicator_group, $currency_obj, $level_one_markup = false, $current_domain_markup = true) {
        $tmp_summary = '';
        if (count($private_trip_indicator_group) == 1) {
            $domestic_round_way_flight = false;
        } elseif (count($private_trip_indicator_group) == 2) {
            $domestic_round_way_flight = true;
        }
        $index = 0;
        $price['TotalTax'] = $price['BaseFare'] = $price['TotalPrice'] = 0;
        foreach ($private_trip_indicator_group as $__tirp_indicator => $__trip_flights) {
            foreach ($__trip_flights as $__trip_flight_k => $__trip_flight_v) {
                $inner_summary = $outer_summary = '';
                $cur_TripIndicator = $__trip_flight_v['TripIndicator'];
                $cur_WSPTCFare = force_multple_data_format($__trip_flight_v['FareBreakdown']['WSPTCFare']);
                $cur_Origin = $__trip_flight_v['Origin'];
                $cur_Destination = $__trip_flight_v['Destination'];
                $cur_WSSegment = $this->group_segment_indicator(force_multple_data_format($__trip_flight_v['Segment']['WSSegment'])); //Group All Flights With Segments
                $cur_IbDuration = isset($__trip_flight_v['IbDuration']) ? $__trip_flight_v['IbDuration'] : 0;
                $cur_ObDuration = $__trip_flight_v['ObDuration'];
                $cur_Source = $__trip_flight_v['Source'];
                $cur_FareRule = $__trip_flight_v['FareRule'];
                $cur_IsLcc = $__trip_flight_v['IsLCC'];
                $cur_IbSegCount = isset($__trip_flight_v['IbSegCount']) ? $__trip_flight_v['IbSegCount'] : 0;
                $cur_ObSegCount = $__trip_flight_v['ObSegCount'];
                $cur_PromotionalPlanType = isset($__trip_flight_v['PromotionalPlanType']) ? $__trip_flight_v['PromotionalPlanType'] : 'N/A';
                $cur_NonRefundable = isset($__trip_flight_v['NonRefundable']) ? $__trip_flight_v['NonRefundable'] : false;
                $cur_SegmentKey = $__trip_flight_v['SegmentKey'];
                $cur_WSResult = serialized_data($__trip_flight_v);
                $cur_Fare = $__trip_flight_v['FareDetails'];
                $temp_price_details = $this->update_search_markup_currency($cur_Fare, $currency_obj, $level_one_markup, $current_domain_markup);
                $o_BaseFare = ($temp_price_details['BaseFare']);
                $cur_Currency = $currency_obj->to_currency;
                $o_Total_Tax = ($this->tax_service_sum($temp_price_details, $cur_Fare));
                $o_Total_Fare = ($this->total_price($temp_price_details, false, $currency_obj));
                $price['TotalTax'] += $o_Total_Tax;
                $price['BaseFare'] += $o_BaseFare;
                $price['TotalPrice'] += $o_Total_Fare;
                //Outer Summary - START
                foreach ($cur_WSSegment as $__segment_k => $__segment_v) {
                    $tmp_origin = current($__segment_v);
                    $tmp_destination = end($__segment_v);
                    $__stop_count = (count($__segment_v) - 1);
                    //calculate total segment travel duration
                    $total_segment_travel_duration = calculate_duration($tmp_origin['DepTIme'], $tmp_destination['ArrTime']);
                    $tmp_summary[$index]['from_loc'] = $tmp_origin['Origin']['CityName'];
                    $tmp_summary[$index]['from_loc_code'] = $tmp_origin['Origin']['CityCode'];
                    $tmp_summary[$index]['to_loc'] = $tmp_destination['Destination']['CityName'];
                    $tmp_summary[$index]['to_loc_code'] = $tmp_destination['Destination']['CityCode'];
                    $tmp_summary[$index]['from_date'] = date('D d - M', strtotime($tmp_origin['DepTIme']));
                    $tmp_summary[$index]['to_date'] = date('D d - M', strtotime($tmp_destination['ArrTime']));
                    $tmp_summary[$index]['airline_code'] = $tmp_origin['Airline']['AirlineCode'];
                    $tmp_summary[$index]['airline_name'] = $tmp_origin['Airline']['AirlineName'];
                    $tmp_summary[$index]['from_time'] = date('h:i a', strtotime($tmp_origin['DepTIme']));
                    $tmp_summary[$index]['to_time'] = date('h:i a', strtotime($tmp_destination['ArrTime']));
                    $tmp_summary[$index]['duration'] = $total_segment_travel_duration;
                    $tmp_summary[$index]['stops'] = (count($__segment_v) - 1);
                    $index++;
                }
            }
        }
        return array('summary' => $tmp_summary, 'price' => $price, 'currency' => $currency_obj->to_currency);
    }

    /*
     * Converts Booking data to Application Currency
     */

    function convert_bookingdata_to_application_currency($booking_price_details) {
        $converted_price_details = array();
        $application_default_currency = admin_base_currency();
        $currency_obj = new Currency(array('module_type' => 'flight', 'from' => get_api_data_currency(), 'to' => admin_base_currency()));
        $converted_price_details['FareDetails'] = $this->preferred_currency_fare_object($booking_price_details, $currency_obj, $application_default_currency);
        //PassengerBreakup
        $converted_price_details['PassengerFareBreakdown'] = $this->preferred_currency_paxwise_breakup_object($booking_price_details['PassengerBreakup'], $currency_obj);
        return $converted_price_details;
    }

    /**
     * Returns Final Price Details For the booking
     * @param unknown_type $Fare
     * @param unknown_type $multiplier
     * @param unknown_type $specific_markup_config
     * @param unknown_type $currency_obj
     * @param unknown_type $deduction_cur_obj
     * @param unknown_type $module
     */
    private function get_final_booking_price_details($Fare, $multiplier, $specific_markup_config, $currency_obj, $deduction_cur_obj, $module) {
        $data = array();
        $core_agent_commision = ($Fare['PublishedFare'] - $Fare['OfferedFare']);
       // echo "core_agent_commision".$core_agent_commision.'<br/>';
        $commissionable_fare = $Fare['PublishedFare'];
        if ($module == 'b2c') {
            $trans_total_fare = $this->total_price($Fare, false, $currency_obj);
            // echo $trans_total_fare;exit;            
            $markup_total_fare = $currency_obj->get_currency($Fare['OfferedFare'], true, false, true, $multiplier, $specific_markup_config);

            $ded_total_fare = $deduction_cur_obj->get_currency($Fare['OfferedFare'], true, true, false, $multiplier, $specific_markup_config);
            $admin_markup = roundoff_number($markup_total_fare['default_value'] - $ded_total_fare['default_value']);
            $admin_commission = $core_agent_commision;
            $agent_markup = 0;
            $agent_commission = 0;
        } else {
            //B2B Calculation
            //Markup # Modified Balu
           # debug($Fare);
            $trans_total_fare = $Fare['PublishedFare'];
            # echo "commission";
            
            $this->commission = $currency_obj->get_commission();
            $AgentCommission = $this->calculate_commission($core_agent_commision);
            #debug($this->commission);

            $admin_commission = roundoff_number($core_agent_commision - $AgentCommission); //calculate here
            $agent_commission = roundoff_number($AgentCommission);
            
            #echo "agent_commission".$agent_commission.'<br/>';
            $admin_net_rate=($trans_total_fare-$agent_commission);

           # echo "admin_net_rate".$admin_net_rate.'<br/>';

            $markup_total_fare = $currency_obj->get_currency($admin_net_rate, true, true, false, $multiplier, $specific_markup_config);
            
           # debug($markup_total_fare);

            $admin_markup = abs($markup_total_fare['default_value'] - $admin_net_rate);
           # echo $admin_markup;

            $agent_net_rate=(($trans_total_fare + $admin_markup)-$agent_commission);
            $ded_total_fare = $deduction_cur_obj->get_currency($agent_net_rate, true, false, true, $multiplier, $specific_markup_config);

           # debug($ded_total_fare);

            $agent_markup = roundoff_number($ded_total_fare['default_value'] - $agent_net_rate);
          
           
        }
        //TDS Calculation
        $admin_tds = $currency_obj->calculate_tds($admin_commission);
        $agent_tds = $currency_obj->calculate_tds($agent_commission);

        $data['commissionable_fare'] = $commissionable_fare;
        $data['trans_total_fare'] = $trans_total_fare;
        $data['admin_markup'] = $admin_markup;
        $data['agent_markup'] = $agent_markup;
        $data['admin_commission'] = $admin_commission;
        $data['agent_commission'] = $agent_commission;
        $data['admin_tds'] = $admin_tds;
        $data['agent_tds'] = $agent_tds;
      
       //  debug($data);
       // exit;
        return $data;
    }

    /**
     * Reference number generated for booking from application
     * @param $app_booking_id
     * @param $params
     */
    function save_booking($app_booking_id, $book_params, $currency_obj, $module = 'b2c') {
        //Need to return following data as this is needed to save the booking fare in the transaction details
        $response['fare'] = $response['domain_markup'] = $response['level_one_markup'] = 0;
        $book_total_fare = array();
        $book_domain_markup = array();
        $book_level_one_markup = array();
        $master_transaction_status = 'BOOKING_INPROGRESS';
        $master_search_id = $book_params['search_id'];

        $domain_origin = get_domain_auth_id();
        $app_reference = $app_booking_id;
        $booking_source = $book_params['token']['booking_source'];

        //PASSENGER DATA UPDATE
        $total_pax_count = count($book_params['passenger_type']);
        $pax_count = $total_pax_count;

        //Extract ExtraService Details
        $extra_service_details = $this->extract_extra_service_details($book_params);

        //PREFERRED TRANSACTION CURRENCY AND CURRENCY CONVERSION RATE 
        $transaction_currency = get_application_currency_preference();
        $application_currency = admin_base_currency();
        $currency_conversion_rate = $currency_obj->transaction_currency_conversion_rate();
        //********************** only for calculation
        $safe_search_data = $this->search_data($master_search_id);
        $safe_search_data = $safe_search_data['data'];
        $safe_search_data['is_domestic_one_way_flight'] = false;
        $from_to_trip_type = $safe_search_data['trip_type'];
        $cabin_class = $safe_search_data['v_class'];
        if (strtolower($from_to_trip_type) == 'multicity') {
            $from_loc = $safe_search_data['from'][0];
            $to_loc = end($safe_search_data['to']);
            $journey_from = $safe_search_data['from_city'][0];
            $journey_to = end($safe_search_data['to_city']);
        } else {
            $from_loc = $safe_search_data['from'];
            $to_loc = $safe_search_data['to'];
            $journey_from = $safe_search_data['from_city'];
            $journey_to = $safe_search_data['to_city'];
        }
       
        $safe_search_data['is_domestic_one_way_flight'] = $GLOBALS['CI']->flight_model->is_domestic_flight($from_loc, $to_loc);
        if ($safe_search_data['is_domestic_one_way_flight'] == false && strtolower($from_to_trip_type) == 'circle') {
            $multiplier = $pax_count * 2; //Multiply with 2 for international round way
        } else if (strtolower($from_to_trip_type) == 'multicity') {
            $multiplier = $pax_count * count($safe_search_data['from']);
        } else {
            $multiplier = $pax_count;
        }
        $token = $book_params['token']['token'];

        //********************* only for calculation
        $master_booking_source = array();
        $currency = $currency_obj->to_currency;
        $deduction_cur_obj = clone $currency_obj;
        //Storing Flight Details - Every Segment can repeate also
        $segment_summary = array();
        // debug($token);exit;
        foreach ($token as $token_index => $token_value) {
            
            $segment_details = $token_value['SegmentDetails'];
            $segment_summary[$token_index] = $token_value['SegmentSummary'];
            $Fare = $token_value['FareDetails']['api_PriceDetails'];
          
            $tmp_domain_markup = 0;
            $tmp_level_one_markup = 0;
            $itinerary_price = $Fare['BaseFare'];

            //Calculation is different for b2b and b2c
            //Specific Markup Config
            $specific_markup_config = array();
            $specific_markup_config = $this->get_airline_specific_markup_config($segment_details); //Get the Airline code for setting airline-wise markup
            $final_booking_price_details = $this->get_final_booking_price_details($Fare, $multiplier, $specific_markup_config, $currency_obj, $deduction_cur_obj, $module);
            if($module == 'b2c'){
                $total_markup = $final_booking_price_details['admin_markup'];
            }
            else{
                $final_booking_price_details['agent_markup'] += $book_params["markup"];
                $total_markup = $final_booking_price_details['admin_markup'] + $final_booking_price_details['agent_markup'];
            }
            $gst_value = 0;
            if($final_booking_price_details['admin_markup'] > 0 ){
                $gst_details = $GLOBALS['CI']->custom_db->single_table_records('gst_master', '*', array('module' => 'flight'));
                if($gst_details['status'] == true){
                    if($gst_details['data'][0]['gst'] > 0){
                        $gst_value = ($final_booking_price_details['admin_markup']/100) * $gst_details['data'][0]['gst'];
                    }
                }
            }

          
            $commissionable_fare = $final_booking_price_details['commissionable_fare'];
            $trans_total_fare = $final_booking_price_details['trans_total_fare'];
            $admin_markup = $final_booking_price_details['admin_markup'];
            $agent_markup = $final_booking_price_details['agent_markup'];
            $admin_commission = $final_booking_price_details['admin_commission'];
            $agent_commission = $final_booking_price_details['agent_commission'];
            $admin_tds = $final_booking_price_details['admin_tds'];
            $agent_tds = $final_booking_price_details['agent_tds'];
            
            //**************Ticketing For Each Token START
            //Following Variables are used to save Transaction and Pax Ticket Details
            $pnr = '';
            $book_id = '';
            $source = '';
            $ref_id = '';
            $transaction_status = 0;
            $GetBookingResult = array();
            $transaction_description = '';
            $getbooking_StatusCode = '';
            $getbooking_Description = '';
            $getbooking_Category = '';
            $WSTicket = array();
            $WSFareRule = array();
            //Saving Flight Transaction Details
            $tranaction_attributes = array();
            $pnr = '';
            $book_id = '';
            //$source = $this->get_tbo_source_name($token_value['Source']);
            $source = '';
            $ref_id = '';
            $transaction_status = $master_transaction_status;
            $transaction_description = '';
            //Get Booking Details
            $getbooking_status_details = '';
            $getbooking_StatusCode = '';
            $getbooking_Description = '';
            $getbooking_Category = '';
            $tranaction_attributes['Fare'] = $Fare;
            $sequence_number = $token_index;
            //Transaction Log Details
            $ticket_trans_status_group[] = $transaction_status;
            $book_total_fare[] = $trans_total_fare;
            $book_domain_markup[] = $admin_markup;
            $book_level_one_markup[] = $agent_markup;
            //Need individual transaction price details
            //SAVE Transaction Details
            $transaction_insert_id = $GLOBALS['CI']->flight_model->save_flight_booking_transaction_details(
                    $app_reference, $transaction_status, $transaction_description, $pnr, $book_id, $source, $ref_id, json_encode($tranaction_attributes), $sequence_number, $currency, $commissionable_fare, $admin_markup, $agent_markup, $admin_commission, $agent_commission, $getbooking_StatusCode, $getbooking_Description, $getbooking_Category, $admin_tds, $agent_tds, $gst_value
            );
            $transaction_insert_id = $transaction_insert_id['insert_id'];

            //Saving Passenger Details
            $i = 0;
            for ($i = 0; $i < $total_pax_count; $i++) {
                $passenger_type = $book_params['passenger_type'][$i];
                $is_lead = $book_params['lead_passenger'][$i];
                $title = get_enum_list('title', $book_params['name_title'][$i]);
                $first_name = $book_params['first_name'][$i];
                $middle_name = ''; //$book_params['middle_name'][$i];
                $last_name = $book_params['last_name'][$i];
                $date_of_birth = $book_params['date_of_birth'][$i];
                $gender = get_enum_list('gender', $book_params['gender'][$i]);

                $passenger_nationality_id = intval($book_params['passenger_nationality'][$i]);
                $passport_issuing_country_id = intval($book_params['passenger_passport_issuing_country'][$i]);
                $passenger_nationality = $GLOBALS['CI']->db_cache_api->get_country_list(array('k' => 'origin', 'v' => 'name'), array('origin' => $passenger_nationality_id));
                $passport_issuing_country = $GLOBALS['CI']->db_cache_api->get_country_list(array('k' => 'origin', 'v' => 'name'), array('origin' => $passport_issuing_country_id));

                $passenger_nationality = isset($passenger_nationality[$passenger_nationality_id]) ? $passenger_nationality[$passenger_nationality_id] : '';
                $passport_issuing_country = isset($passport_issuing_country[$passport_issuing_country_id]) ? $passport_issuing_country[$passport_issuing_country_id] : '';

                $passport_number = $book_params['passenger_passport_number'][$i];
                $passport_expiry_date = $book_params['passenger_passport_expiry_year'][$i] . '-' . $book_params['passenger_passport_expiry_month'][$i] . '-' . $book_params['passenger_passport_expiry_day'][$i];
                //$status = 'BOOKING_CONFIRMED';//Check it
                $status = $master_transaction_status;
                $passenger_attributes = array();
                if($passenger_type == 'Adult'){
                    $passenger_attributes = $token_value['PassengerFareBreakdown']['ADT']; 
                }else if($passenger_type == 'Child'){
                    $passenger_attributes = $token_value['PassengerFareBreakdown']['CHD'];
                }else{
                    $passenger_attributes = $token_value['PassengerFareBreakdown']['INF'];
                }


                $flight_booking_transaction_details_fk = $transaction_insert_id; //Adding Transaction Details Origin
                //SAVE Pax Details
                $pax_insert_id = $GLOBALS['CI']->flight_model->save_flight_booking_passenger_details(
                        $app_reference, $passenger_type, $is_lead, $title, $first_name, $middle_name, $last_name, $date_of_birth, $gender, $passenger_nationality, $passport_number, $passport_issuing_country, $passport_expiry_date, $status, json_encode($passenger_attributes), $flight_booking_transaction_details_fk);

                //Save passenger ticket information
                $passenger_ticket_info = $GLOBALS['CI']->flight_model->save_passenger_ticket_info($pax_insert_id['insert_id']);
            }//Adding Pax Details Ends
            //Saving Segment Details
            foreach ($segment_details as $seg_k => $seg_v) {
                $curr_segment_indicator = 1;
                foreach ($seg_v as $ws_key => $ws_val) {
                    $FareRestriction = '';
                    $FareBasisCode = '';
                    $FareRuleDetail = '';
                    $airline_pnr = '';
                    $cabin_baggage = $ws_val['CabinBaggage'];
                    $checkin_baggage = $ws_val['Baggage'];
                    $is_refundable = @$token_value['Attr']['IsRefundable']; 
                    if(isset($is_refundable) && !empty($is_refundable) && $is_refundable == 1){
                        $is_refundable = 'Refundable';
                    }
                    else{
                         $is_refundable = 'Non Refundable';
                    }
                    $AirlineDetails = $ws_val['AirlineDetails'];
                    $OriginDetails = $ws_val['OriginDetails'];
                    $DestinationDetails = $ws_val['DestinationDetails'];
                    //$segment_indicator = $ws_val['SegmentIndicator'];
                    $segment_indicator = ($curr_segment_indicator++);

                    $airline_code = $AirlineDetails['AirlineCode'];
                    $airline_name = $AirlineDetails['AirlineName'];
                    $flight_number = $AirlineDetails['FlightNumber'];
                    $fare_class = $AirlineDetails['FareClass'];
                    $from_airport_code = $OriginDetails['AirportCode'];
                    $from_airport_name = $OriginDetails['AirportName'];
                    $to_airport_code = $DestinationDetails['AirportCode'];
                    $to_airport_name = $DestinationDetails['AirportName'];
                    $departure_datetime = date('Y-m-d H:i:s', strtotime($OriginDetails['DateTime']));
                    $arrival_datetime = date('Y-m-d H:i:s', strtotime($DestinationDetails['DateTime']));
                    $iti_status = '';
                    $operating_carrier = $AirlineDetails['AirlineCode'];
                    $attributes = array('craft' => @$ws_val['Craft'], 'ws_val' => $ws_val);
                    //SAVE ITINERARY
                    $GLOBALS['CI']->flight_model->save_flight_booking_itinerary_details(
                            $app_reference, $segment_indicator, $airline_code, $airline_name, $flight_number, $fare_class, $from_airport_code, $from_airport_name, $to_airport_code, $to_airport_name, $departure_datetime, $arrival_datetime, $iti_status, $operating_carrier, json_encode($attributes), $FareRestriction, $FareBasisCode, $FareRuleDetail, $airline_pnr, $cabin_baggage, $checkin_baggage, $is_refundable);
                }
            }//End Of Segments Loop
        }//End Of Token Loop
        //Save Master Booking Details
        $book_total_fare = array_sum($book_total_fare);
        $book_domain_markup = array_sum($book_domain_markup);
        $book_level_one_markup = array_sum($book_level_one_markup);

        $phone = $book_params['passenger_contact'];
        $alternate_number = '';
        $email = $book_params['billing_email'];
        $start = $token[0];
        $end = end($token);

        $journey_start = $segment_summary[0][0]['OriginDetails']['DateTime'];
        $journey_start = date('Y-m-d H:i:s', strtotime($journey_start));
        $journey_end = end(end($segment_summary));
        $journey_end = $journey_end['DestinationDetails']['DateTime'];
        $journey_end = date('Y-m-d H:i:s', strtotime($journey_end));
        $payment_mode = $book_params['payment_method'];
        $created_by_id = intval(@$GLOBALS['CI']->entity_user_id);

        $passenger_country_id = intval($book_params['billing_country']);
        //$passenger_city_id = intval($book_params['billing_city']);
        $passenger_country = $GLOBALS['CI']->db_cache_api->get_country_list(array('k' => 'origin', 'v' => 'name'), array('origin' => $passenger_country_id));
        //$passenger_city = $GLOBALS['CI']->db_cache_api->get_city_list(array('k' => 'origin', 'v' => 'destination'), array('origin' => $passenger_city_id));

        $passenger_country = isset($passenger_country[$passenger_country_id]) ? $passenger_country[$passenger_country_id] : '';
        //$passenger_city = isset($passenger_city[$passenger_city_id]) ? $passenger_city[$passenger_city_id] : '';
        $passenger_city = $book_params['billing_city'];

        $attributes = array('country' => $passenger_country, 'city' => $passenger_city, 'zipcode' => $book_params['billing_zipcode'], 'address' => $book_params['billing_address_1']);
        $flight_booking_status = $master_transaction_status;
        $gst_details ='';
        if(!empty($book_params['gst_number'])){
            $gst_details['gst_number'] = $book_params['gst_number'];
            $gst_details['gst_company_name'] = $book_params['gst_company_name'];
            $gst_details['gst_email'] = $book_params['gst_email'];
            $gst_details['gst_phone'] = $book_params['gst_phone'];
            $gst_details['gst_address'] = $book_params['gst_address'];
            $gst_details['gst_state'] = $book_params['gst_state'];
            $gst_details = json_encode($gst_details);
        }
      
        //SAVE Booking Details
        $booking_billing_type = $book_params['selected_pm'];
        $GLOBALS['CI']->flight_model->save_flight_booking_details(
                $domain_origin, $flight_booking_status, $app_reference, $cabin_class, $booking_source, $phone, $alternate_number, $email, $journey_start, $journey_end, $journey_from, $journey_to, $payment_mode, json_encode($attributes), $created_by_id, $from_loc, $to_loc, $from_to_trip_type, $transaction_currency, $currency_conversion_rate, $gst_details, $phone_country_code, $booking_billing_type
        );

        //Save Passenger Baggage Details
        
        if (isset($extra_service_details['ExtraServiceDetails']['Baggage']) == true && valid_array($extra_service_details['ExtraServiceDetails']['Baggage']) == true) {
            $this->save_passenger_baggage_info($app_reference, $book_params, $extra_service_details['ExtraServiceDetails']['Baggage']);
        }


        //Save Passenger Meals Details
        if (isset($extra_service_details['ExtraServiceDetails']['Meals']) == true && valid_array($extra_service_details['ExtraServiceDetails']['Meals']) == true) {
            $this->save_passenger_meal_info($app_reference, $book_params, $extra_service_details['ExtraServiceDetails']['Meals']);
        }
        //Save Passenger Meals Details
        if (isset($extra_service_details['ExtraServiceDetails']['Seat']) == true && valid_array($extra_service_details['ExtraServiceDetails']['Seat']) == true) {
            $this->save_passenger_seat_info($app_reference, $book_params, $extra_service_details['ExtraServiceDetails']['Seat']);
        }
        //Meal Preference
        if (isset($extra_service_details['ExtraServiceDetails']['MealPreference']) == true && valid_array($extra_service_details['ExtraServiceDetails']['MealPreference']) == true) {
            $this->save_passenger_meal_preference($app_reference, $book_params, $extra_service_details['ExtraServiceDetails']['MealPreference']);
        }
        //Seat Preference
        if (isset($extra_service_details['ExtraServiceDetails']['SeatPreference']) == true && valid_array($extra_service_details['ExtraServiceDetails']['SeatPreference']) == true) {
            $this->save_passenger_seat_preference($app_reference, $book_params, $extra_service_details['ExtraServiceDetails']['SeatPreference']);
        }

        //Add Extra Service Price to published price
        $GLOBALS['CI']->flight_model->add_extra_service_price_to_published_fare($app_reference);

        //Adding Extra services Total Price
        $extra_services_total_price = $GLOBALS['CI']->flight_model->get_extra_services_total_price($app_reference);
        $book_total_fare += $extra_services_total_price;

        /*         * ************ Update Convinence Fees And Other Details Start ***************** */
        //Convinence_fees to be stored and discount
        $convinence = 0;
        $discount = 0;
        $convinence_value = 0;
        $convinence_type = 0;
        $convinence_type = 0;
        if ($module == 'b2c') {
            $total_transaction_amount = $book_total_fare + $book_domain_markup;
            $convinence = $currency_obj->convenience_fees($total_transaction_amount, $master_search_id);
            $convinence_row = $currency_obj->get_convenience_fees();
            $convinence_value = $convinence_row['value'];
            $convinence_type = $convinence_row['type'];
            $convinence_per_pax = $convinence_row['per_pax'];
            $discount = $book_params['promo_code_discount_val'];
            $promo_code = $book_params['promo_code'];
        } elseif ($module == 'b2b') {
            $discount = 0;
            $convinence_per_pax = 0;
        }
        $GLOBALS['CI']->load->model('transaction');
        //SAVE Convinience and Discount Details
        $GLOBALS['CI']->transaction->update_convinence_discount_details('flight_booking_details', $app_reference, $discount, $promo_code, $convinence, $convinence_value, $convinence_type, $convinence_per_pax);
        /*         * ************ Update Convinence Fees And Other Details End ***************** */

        /**
         * Data to be returned after transaction is saved completely
         */
        $response['fare'] = $book_total_fare;
        $response['admin_markup'] = $book_domain_markup;
        $response['agent_markup'] = $book_level_one_markup;
        $response['convinence'] = $convinence;
        $response['discount'] = $discount;

        $response['status'] = $flight_booking_status;
        $response['status_description'] = $transaction_description;
        $response['name'] = $first_name;
        $response['phone'] = $phone;

        return $response;
    }

    /**
     * Save Baggage Details
     */
    private function save_passenger_baggage_info($app_reference, $book_params, $baggage_details) {
        $stored_booking_details = $GLOBALS['CI']->flight_model->get_booking_details($app_reference);
        $GLOBALS['CI']->load->library('booking_data_formatter');
        $booking_details = $GLOBALS['CI']->booking_data_formatter->format_flight_booking_data($stored_booking_details, $GLOBALS['CI']->config->item('current_module'));
        $booking_details = $booking_details['data']['booking_details']['0'];
        $booking_transaction_details = $booking_details['booking_transaction_details'];

        $baggage_index = 0;
        while (isset($book_params["baggage_$baggage_index"]) == true) {
            foreach ($booking_transaction_details as $tr_k => $tr_v) {
                if (count($booking_transaction_details) == 2) {
                    if ($tr_k == 0) {
                        $journy_type = 'onward_journey';
                    } else {
                        $journy_type = 'return_journey';
                    }
                } else {
                    $journy_type = 'full_journey';
                }

                //
                foreach ($book_params["baggage_$baggage_index"] as $bag_k => $bag_v) {

                    if (empty($bag_v) == false && isset($baggage_details[$bag_v]) == true && $baggage_details[$bag_v]['JourneyType'] == $journy_type) {
                        $passenger_fk = $tr_v['booking_customer_details'][$bag_k]['origin'];
                        $from_airport_code = $baggage_details[$bag_v]['Origin'];
                        $to_airport_code = $baggage_details[$bag_v]['Destination'];
                        $description = $baggage_details[$bag_v]['Weight'];
                        $price = $baggage_details[$bag_v]['Price'];
                        $code = $baggage_details[$bag_v]['Code'];

                        //Save passenger baggage information
                        $GLOBALS['CI']->flight_model->save_passenger_baggage_info($passenger_fk, $from_airport_code, $to_airport_code, $description, $price, $code);
                    }
                }
            }
            $baggage_index++;
        }
    }

    /**
     * Save Meal Details
     */
    private function save_passenger_meal_info($app_reference, $book_params, $meal_details) {
        $stored_booking_details = $GLOBALS['CI']->flight_model->get_booking_details($app_reference);
        $GLOBALS['CI']->load->library('booking_data_formatter');
        $booking_details = $GLOBALS['CI']->booking_data_formatter->format_flight_booking_data($stored_booking_details, $GLOBALS['CI']->config->item('current_module'));
        $booking_details = $booking_details['data']['booking_details']['0'];
        $booking_transaction_details = $booking_details['booking_transaction_details'];

        $meal_index = 0;
        while (isset($book_params["meal_$meal_index"]) == true) {
            foreach ($booking_transaction_details as $tr_k => $tr_v) {
                if (count($booking_transaction_details) == 2) {
                    if ($tr_k == 0) {
                        $journy_type = 'onward_journey';
                    } else {
                        $journy_type = 'return_journey';
                    }
                } else {
                    $journy_type = 'full_journey';
                }

                //
                foreach ($book_params["meal_$meal_index"] as $meal_k => $meal_v) {

                    if (empty($meal_v) == false && isset($meal_details[$meal_v]) == true && $meal_details[$meal_v]['JourneyType'] == $journy_type) {
                        $passenger_fk = $tr_v['booking_customer_details'][$meal_k]['origin'];
                        $from_airport_code = $meal_details[$meal_v]['Origin'];
                        $to_airport_code = $meal_details[$meal_v]['Destination'];
                        $description = $meal_details[$meal_v]['Description'];
                        $price = $meal_details[$meal_v]['Price'];
                        $code = $meal_details[$meal_v]['Code'];
                        //Save passenger meal information
                        $GLOBALS['CI']->flight_model->save_passenger_meals_info($passenger_fk, $from_airport_code, $to_airport_code, $description, $price, $code);
                    }
                }
            }
            $meal_index++;
        }
    }

    /**
     * Save Meal Preference Details
     */
    private function save_passenger_meal_preference($app_reference, $book_params, $meal_details) {
        $stored_booking_details = $GLOBALS['CI']->flight_model->get_booking_details($app_reference);
        $GLOBALS['CI']->load->library('booking_data_formatter');
        $booking_details = $GLOBALS['CI']->booking_data_formatter->format_flight_booking_data($stored_booking_details, $GLOBALS['CI']->config->item('current_module'));
        $booking_details = $booking_details['data']['booking_details']['0'];
        $booking_transaction_details = $booking_details['booking_transaction_details'];

        $meal_index = 0;
        while (isset($book_params["meal_pref$meal_index"]) == true) {
            foreach ($booking_transaction_details as $tr_k => $tr_v) {
                if (count($booking_transaction_details) == 2) {
                    if ($tr_k == 0) {
                        $journy_type = 'onward_journey';
                    } else {
                        $journy_type = 'return_journey';
                    }
                } else {
                    $journy_type = 'full_journey';
                }

                //
                foreach ($book_params["meal_pref$meal_index"] as $meal_k => $meal_v) {

                    if (empty($meal_v) == false && isset($meal_details[$meal_v]) == true && $meal_details[$meal_v]['JourneyType'] == $journy_type) {
                        $passenger_fk = $tr_v['booking_customer_details'][$meal_k]['origin'];
                        $from_airport_code = $meal_details[$meal_v]['Origin'];
                        $to_airport_code = $meal_details[$meal_v]['Destination'];
                        $description = $meal_details[$meal_v]['Description'];
                        $price = 0;
                        $code = $meal_details[$meal_v]['Code'];
                        //Save passenger meal information
                        $GLOBALS['CI']->flight_model->save_passenger_meals_info($passenger_fk, $from_airport_code, $to_airport_code, $description, $price, $code, 'static');
                    }
                }
            }
            $meal_index++;
        }
    }

    /**
     * Save Seat Details
     */
    private function save_passenger_seat_info($app_reference, $book_params, $seat_details) {
        $stored_booking_details = $GLOBALS['CI']->flight_model->get_booking_details($app_reference);
        $GLOBALS['CI']->load->library('booking_data_formatter');
        $booking_details = $GLOBALS['CI']->booking_data_formatter->format_flight_booking_data($stored_booking_details, $GLOBALS['CI']->config->item('current_module'));
        $booking_details = $booking_details['data']['booking_details']['0'];
        $booking_transaction_details = $booking_details['booking_transaction_details'];

        $seat_index = 0;
        while (isset($book_params["seat_$seat_index"]) == true) {
            foreach ($booking_transaction_details as $tr_k => $tr_v) {
                if (count($booking_transaction_details) == 2) {
                    if ($tr_k == 0) {
                        $journy_type = 'onward_journey';
                    } else {
                        $journy_type = 'return_journey';
                    }
                } else {
                    $journy_type = 'full_journey';
                }

                //
                foreach ($book_params["seat_$seat_index"] as $seat_k => $seat_v) {

                    if (empty($seat_v) == false && isset($seat_details[$seat_v]) == true && $seat_details[$seat_v]['JourneyType'] == $journy_type) {

                        $passenger_fk = $tr_v['booking_customer_details'][$seat_k]['origin'];
                        $from_airport_code = $seat_details[$seat_v]['Origin'];
                        $to_airport_code = $seat_details[$seat_v]['Destination'];
                        $description = '';
                        $price = $seat_details[$seat_v]['Price'];
                        $code = $seat_details[$seat_v]['SeatNumber'];
                        $airline_code = $seat_details[$seat_v]['AirlineCode'];
                        $flight_number = $seat_details[$seat_v]['FlightNumber'];

                        //Save passenger seat information
                        $GLOBALS['CI']->flight_model->save_passenger_seat_info($passenger_fk, $from_airport_code, $to_airport_code, $description, $price, $code, 'dynamic', $airline_code, $flight_number);
                    }
                }
            }
            $seat_index++;
        }
    }

    /**
     * Save Seat Preference  Details
     */
    private function save_passenger_seat_preference($app_reference, $book_params, $seat_details) {
        $stored_booking_details = $GLOBALS['CI']->flight_model->get_booking_details($app_reference);
        $GLOBALS['CI']->load->library('booking_data_formatter');
        $booking_details = $GLOBALS['CI']->booking_data_formatter->format_flight_booking_data($stored_booking_details, $GLOBALS['CI']->config->item('current_module'));
        $booking_details = $booking_details['data']['booking_details']['0'];
        $booking_transaction_details = $booking_details['booking_transaction_details'];

        $seat_index = 0;
        while (isset($book_params["seat_pref$seat_index"]) == true) {
            foreach ($booking_transaction_details as $tr_k => $tr_v) {
                if (count($booking_transaction_details) == 2) {
                    if ($tr_k == 0) {
                        $journy_type = 'onward_journey';
                    } else {
                        $journy_type = 'return_journey';
                    }
                } else {
                    $journy_type = 'full_journey';
                }

                //
                foreach ($book_params["seat_pref$seat_index"] as $seat_k => $seat_v) {

                    if (empty($seat_v) == false && isset($seat_details[$seat_v]) == true && $seat_details[$seat_v]['JourneyType'] == $journy_type) {
                        $passenger_fk = $tr_v['booking_customer_details'][$seat_k]['origin'];
                        $from_airport_code = $seat_details[$seat_v]['Origin'];
                        $to_airport_code = $seat_details[$seat_v]['Destination'];
                        $description = $seat_details[$seat_v]['Description'];
                        $price = 0;
                        $code = $seat_details[$seat_v]['Code'];
                        //Save passenger seat information
                        $GLOBALS['CI']->flight_model->save_passenger_seat_info($passenger_fk, $from_airport_code, $to_airport_code, $description, $price, $code, 'static');
                    }
                }
            }
            $seat_index++;
        }
    }

    /**
     * Updates the Booking Details:Status, Price and Ticket Details
     */
    public function update_booking_details($book_id, $book_params, $ticket_details, $module = 'b2c') {
        $response = array();
        $book_total_fare = array();
        $book_domain_markup = array();
        $book_level_one_markup = array();

        $app_reference = $book_id;
        $master_search_id = $book_params['search_id'];
        //Setting Master Booking Status
        $master_transaction_status = $this->status_code_value($ticket_details['master_booking_status']);
        if (isset($ticket_details['TicketDetails']) == true && valid_array($ticket_details['TicketDetails']) == true) {
            $ticket_details = $ticket_details['TicketDetails'];
        } else {
            $ticket_details = array();
        }
        $saved_booking_data = $GLOBALS['CI']->flight_model->get_booking_details($book_id);
        if ($saved_booking_data['status'] == false) {
            $response['status'] = BOOKING_ERROR;
            $response['msg'] = 'No Data Found';
            return $response;
        }

        //Extracting the Saved data
        $s_master_data = $saved_booking_data['data']['booking_details'][0];
        $s_booking_itinerary_details = $saved_booking_data['data']['booking_itinerary_details'];
        $s_booking_transaction_details = $saved_booking_data['data']['booking_transaction_details'];
        $s_booking_customer_details = $saved_booking_data['data']['booking_customer_details'];
        $first_name = $s_booking_customer_details[0]['first_name'];
        $phone = $s_master_data['phone'];
        $current_master_booking_status = $s_master_data['status'];
        //Extracting the Origins
        $transaction_origins = group_array_column($s_booking_transaction_details, 'origin');
        $passenger_origins = group_array_column($s_booking_customer_details, 'origin');
        $itinerary_origins = group_array_column($s_booking_itinerary_details, 'origin');
        //Indexing the data with origin
        $indexed_transaction_details = array();
        foreach ($s_booking_transaction_details as $s_tk => $s_tv) {
            $indexed_transaction_details[$s_tv['origin']] = $s_tv;
        }
        //1.Update : flight_booking_details
        $flight_master_booking_status = $master_transaction_status;
        $GLOBALS['CI']->custom_db->update_record('flight_booking_details', array('status' => $master_transaction_status), array('app_reference' => $app_reference));

        $total_pax_count = count($book_params['passenger_type']);
        $pax_count = $total_pax_count;
        //********************** only for calculation
        $safe_search_data = $this->search_data($master_search_id);
        $safe_search_data = $safe_search_data['data'];
        $from_loc = $safe_search_data['from'];
        $to_loc = $safe_search_data['to'];
        $safe_search_data['is_domestic_one_way_flight'] = false;
        $from_to_trip_type = $safe_search_data['trip_type'];

        $safe_search_data['is_domestic_one_way_flight'] = $GLOBALS['CI']->flight_model->is_domestic_flight($from_loc, $to_loc);
        if ($safe_search_data['is_domestic_one_way_flight'] == false && strtolower($from_to_trip_type) == 'circle') {
            $multiplier = $pax_count * 2; //Multiply with 2 for international round way
        } else if (strtolower($from_to_trip_type) == 'multicity') {
            $multiplier = $pax_count * count($safe_search_data['from']);
        } else {
            $multiplier = $pax_count;
        }
        //********************* only for calculation
        $currency_obj = $book_params['currency_obj'];
        $currency = $currency_obj->to_currency;
        $deduction_cur_obj = clone $currency_obj;
        //PREFERRED TRANSACTION CURRENCY AND CURRENCY CONVERSION RATE 
        $transaction_currency = get_application_currency_preference();
        $application_currency = admin_base_currency();
        $currency_conversion_rate = $currency_obj->transaction_currency_conversion_rate();

        if (valid_array($ticket_details) == true) {
            //Ticket Loop Starts
            foreach ($ticket_details as $ticket_index => $ticket_value) {
                $transaction_details_origin = intval($transaction_origins[$ticket_index]);

                if ($this->valid_flight_booking_status($ticket_value['Status']) == true) {//IF Ticket is HOLD/CONFIRMED
                    $status = $this->status_code_value($ticket_value['Status']);
                    $ticket_value = $ticket_value['CommitBooking']['BookingDetails'];

                    $api_booking_id = $ticket_value['BookingId'];
                    $pnr = $ticket_value['PNR'];
                    $Fare = $ticket_value['Price']['FareDetails'];
                    $PassengerFareBreakdown = $ticket_value['Price']['PassengerFareBreakdown'];
                    $segment_details = $ticket_value['JourneyList']['FlightDetails']['Details'];
                    $passenger_details = $ticket_value['PassengerDetails'];

                    $tmp_domain_markup = 0;
                    $tmp_level_one_markup = 0;
                    $itinerary_price = $Fare['BaseFare'];
                    //Calculation is different for b2b and b2c
                    //Specific Markup Config
                    $specific_markup_config = array();
                    $specific_markup_config = $this->get_airline_specific_markup_config($segment_details); //Get the Airline code for setting airline-wise markup

                    $final_booking_price_details = $this->get_final_booking_price_details($Fare, $multiplier, $specific_markup_config, $currency_obj, $deduction_cur_obj, $module);
                    $commissionable_fare = $final_booking_price_details['commissionable_fare'];
                    $trans_total_fare = $final_booking_price_details['trans_total_fare'];
                    $admin_markup = $final_booking_price_details['admin_markup'];
                    $agent_markup = $final_booking_price_details['agent_markup'];
                    $admin_commission = $final_booking_price_details['admin_commission'];
                    $agent_commission = $final_booking_price_details['agent_commission'];
                    $admin_tds = $final_booking_price_details['admin_tds'];
                    $agent_tds = $final_booking_price_details['agent_tds'];


                    //2.Update : flight_booking_transaction_details
                    $update_transaction_condition = array();
                    $update_transaction_data = array();
                    $update_transaction_condition['origin'] = $transaction_details_origin;
                    $update_transaction_data['pnr'] = $pnr;
                    $update_transaction_data['book_id'] = $api_booking_id;
                    $update_transaction_data['status'] = $status;
                    $update_transaction_data['total_fare'] = $commissionable_fare;
                    $update_transaction_data['admin_commission'] = $admin_commission;
                    $update_transaction_data['agent_commission'] = $agent_commission;
                    $update_transaction_data['admin_tds'] = $admin_tds;
                    $update_transaction_data['agent_tds'] = $agent_tds;
                    # Disabled BY Balu
                   // $update_transaction_data['admin_markup'] = $admin_markup;
                   // $update_transaction_data['agent_markup'] = $agent_markup;
                    //For Transaction Log
                    $book_total_fare[] = $trans_total_fare;
                    $book_domain_markup[] = $admin_markup;
                    $book_level_one_markup[] = $agent_markup;

                    $GLOBALS['CI']->custom_db->update_record('flight_booking_transaction_details', $update_transaction_data, $update_transaction_condition);

                    //3.Update: flight_booking_passenger_details
                    $update_passenger_condition = array();
                    $update_passenger_data = array();
                    $update_passenger_condition['flight_booking_transaction_details_fk'] = $transaction_details_origin;
                    $update_passenger_data['status'] = $master_transaction_status;
                    $GLOBALS['CI']->custom_db->update_record('flight_booking_passenger_details', $update_passenger_data, $update_passenger_condition);

                    //4.Update Ticket details to flight_passenger_ticket_info
                    $single_pax_fare_breakup = $this->get_single_pax_fare_breakup($PassengerFareBreakdown);
                    foreach ($passenger_details as $pax_k => $pax_v) {
                        $passenger_fk = intval(array_shift($passenger_origins));
                        if(isset($pax_v['TicketId'])){
                            $TicketId = $pax_v['TicketId'];
                        }else{
                            $TicketId = 0;
                        }
                        if(isset($pax_v['PassengerId'])){
                            $api_passenger_origin = $pax_v['PassengerId'];
                        }else{
                            $api_passenger_origin = 0;
                        }
                        
                        // $TicketId = $pax_v['PassengerId'];
                        $TicketNumber = $pax_v['TicketNumber'];
                        $IssueDate = '';
                        $Fare = json_encode($single_pax_fare_breakup[$pax_v['PassengerType']]);
                        $SegmentAdditionalInfo = '';
                        $ValidatingAirline = '';
                        $CorporateCode = '';
                        $TourCode = '';
                        $Endorsement = '';
                        $Remarks = '';
                        $ServiceFeeDisplayType = '';
                        //SAVE PAX Ticket Details
                        $GLOBALS['CI']->flight_model->update_passenger_ticket_info($passenger_fk, $TicketId, $TicketNumber, $IssueDate, $Fare, $SegmentAdditionalInfo, $ValidatingAirline, $CorporateCode, $TourCode, $Endorsement, $Remarks, $ServiceFeeDisplayType, $api_passenger_origin);
                    }
                    //5. Update :flight_booking_itinerary_details
                    foreach ($segment_details as $seg_k => $seg_v) {
                        foreach ($seg_v as $ws_key => $ws_val) {
                            $update_segment_condition = array();
                            $update_segement_data = array();
                            $update_segment_condition['origin'] = intval(array_shift($itinerary_origins));
                            $update_segement_data['airline_pnr'] = $ws_val['AirlinePNR'];
                            $attributes = array();
                            $attributes['departure_terminal'] = $ws_val['Origin']['Terminal'];
                            $attributes['arrival_terminal'] = $ws_val['Destination']['Terminal'];
                            $attributes['CabinClass'] = $ws_val['CabinClass'];
                            $attributes['Attr'] = $ws_val['Attr'];

                            $update_segement_data['attributes'] = json_encode($attributes);
                            $update_segement_data['status'] = '';

                            $update_segement_data['FareRestriction'] = '';
                            $update_segement_data['FareBasisCode'] = '';
                            $update_segement_data['FareRuleDetail'] = '';

                            $GLOBALS['CI']->custom_db->update_record('flight_booking_itinerary_details', $update_segement_data, $update_segment_condition);
                        }
                    }
                } else {//IF Ticket is Failed
                    $GLOBALS['CI']->flight_model->update_flight_booking_transaction_failure_status($app_reference, $transaction_details_origin);
                    //For Transaction Log
                    $book_total_fare[] = $indexed_transaction_details[$transaction_details_origin]['total_fare'];
                    $book_domain_markup[] = $indexed_transaction_details[$transaction_details_origin]['admin_markup'];
                    $book_level_one_markup[] = $indexed_transaction_details[$transaction_details_origin]['agent_markup'];
                }
            }//Ticket Loop Ends
        } else {
            foreach ($indexed_transaction_details as $itd_k => $itd_v) {
                $transaction_details_origin = $itd_v['origin'];
                $GLOBALS['CI']->flight_model->update_flight_booking_transaction_failure_status($app_reference, $transaction_details_origin);

                $book_total_fare[] = $itd_v['total_fare'];
                $book_domain_markup[] = $itd_v['admin_markup'];
                $book_level_one_markup[] = $itd_v['agent_markup'];
            }
        }


        /**
         * Data to be returned after transaction is saved completely
         */
        $transaction_description = '';
        $book_total_fare = array_sum($book_total_fare);
        $book_domain_markup = array_sum($book_domain_markup);
        $book_level_one_markup = array_sum($book_level_one_markup);
        $discount = 0;


        //Adding Extra services Total Price
        $extra_services_total_price = $GLOBALS['CI']->flight_model->get_extra_services_total_price($app_reference);
        $book_total_fare += $extra_services_total_price;

        if ($module == 'b2c') {
            $total_transaction_amount = $book_total_fare + $book_domain_markup;
            $convinence = $currency_obj->convenience_fees($total_transaction_amount, $master_search_id);
        } else {
            $convinence = 0;
        }
        $response['fare'] = $book_total_fare;
        $response['admin_markup'] = $book_domain_markup;
        $response['agent_markup'] = $book_level_one_markup;
        $response['convinence'] = $convinence;
        $response['discount'] = $discount;

        $response['status'] = $flight_master_booking_status;
        $response['status_description'] = $transaction_description;
        $response['name'] = $first_name;
        $response['phone'] = $phone;
        $response['transaction_currency'] = $transaction_currency;
        $response['currency_conversion_rate'] = $currency_conversion_rate;

        return $response;
    }

    function get_booking_status($transaction_status) {
        $successfull_status_array = $this->successfull_booking_status();
        if (in_array(intval($transaction_status), $successfull_status_array) == true) {
            $transaction_status = 'BOOKING_CONFIRMED';
        } else {
            if (in_array($transaction_status, array(30, 25, 24, 23, 22, 21, 20, 19, 18, 17, 16, 15, 10, 6, 1))) {
                $transaction_status = 'BOOKING_FAILED';
            } else {
                $transaction_status = 'BOOKING_PENDING';
            }
        }
        return $transaction_status;
    }

    function successfull_booking_status() {
        return array(9, 14, 5);
    }

    /**
     * Balu A
     * Get Fare Class
     * @param unknown_type $fare_class
     * @return multitype:unknown
     */
    function get_fare_class($fare_class) {
        $fare_class = trim(strtoupper($fare_class));
        //Assigning Fare Class
        if (in_array($fare_class, get_enum_list('first_class'))) {
            $fare_class = 'First Class';
        } else if (in_array($fare_class, get_enum_list('buisness_class'))) {
            $fare_class = 'Buiness Class';
        } else if (in_array($fare_class, get_enum_list('economy_class'))) {
            $fare_class = 'Economy Class';
        } else if (in_array($fare_class, get_enum_list('coach_class'))) {
            $fare_class = 'Coach Class';
        } else {
            $fare_class = 'N/A';
        }
        return $fare_class;
    }

    public function update_flight_booking_details($app_reference) {
        $saved_booking_data = $GLOBALS['CI']->flight_model->get_booking_details($app_reference);

        //Extracting the Saved data
        $s_master_data = $saved_booking_data['data']['booking_details'][0];
        $s_booking_itinerary_details = $saved_booking_data['data']['booking_itinerary_details'];
        $s_booking_transaction_details = $saved_booking_data['data']['booking_transaction_details'];
        $itinerary_origins = group_array_column($s_booking_itinerary_details, 'origin');
        foreach ($s_booking_transaction_details as $k => $v) {
            $booking_id = $v['book_id'];
            $pnr = $v['pnr'];
            if (empty($booking_id) == false && empty($pnr) == false) {
                $api_booking_details = $this->get_booking_details($booking_id, $pnr);
                if ($api_booking_details['status'] == SUCCESS_STATUS) {//Updating the details
                    $FlightItinerary = $api_booking_details['data']['api_booking_details']['FlightItinerary'];
                    $segment_details = $FlightItinerary['SegmentDetails'];
                    $Fare = $FlightItinerary['FareDetails'];
                    $passenger_details = $FlightItinerary['PassengerDetails'];
                    $fare_rule = $FlightItinerary['FareRule'];
                    foreach ($segment_details as $seg_k => $seg_v) {
                        foreach ($seg_v as $ws_key => $ws_val) {
                            $update_segment_condition = array();
                            $update_segement_data = array();
                            $update_segment_condition['origin'] = array_shift($itinerary_origins);
                            $update_segement_data['airline_pnr'] = $ws_val['AirlinePNR'];
                            $update_segement_data['status'] = $ws_val['Status'];
                            $GLOBALS['CI']->custom_db->update_record('flight_booking_itinerary_details', $update_segement_data, $update_segment_condition);
                        }
                    }
                }
            }
        }
    }

    /**
     * Sagar Wakchaure
     * get pnr details
     * @param unknown $app_reference
     * @return string
     */
    function get_update_pnr_request($app_reference) {
        $response['status'] = SUCCESS_STATUS;
        $response['data'] = array();
        $request_params = array();
        $this->credentials('BookingDetails');
        if (empty($app_reference) == false) {
            $request_params['AppReference'] = $app_reference;
        } else {
            $response['status'] = FAILURE_STATUS;
        }
        $response['data']['request'] = json_encode($request_params);
        $response['data']['service_url'] = $this->service_url;
        return $response;
    }

    /**
     * Sagar Wakchaure
     * Update PNR
     * @param unknown $app_reference
     * @return string[]|unknown[]
     */
    function update_pnr_details($app_reference) {
        $response['data'] = array();
        $response['status'] = FAILURE_STATUS;
        $api_request = $this->get_update_pnr_request($app_reference);
        if ($api_request['status']) {
            $header_info = $this->get_header();
            $api_response = $this->CI->api_interface->get_json_response($api_request['data']['service_url'], $api_request['data']['request'], $header_info);
            if ($this->valid_api_response($api_response)) {
                $response['data'] = $api_response['BookingDetails'];
                $response['status'] = SUCCESS_STATUS;
            }
        }

        return $response;
    }

    /**
     * Sachin
     * set flight search session expiry time
     * @param unknown_type $from_cache
     * @param unknown_type $search_hash
     */
    function set_flight_search_session_expiry($from_cache = true, $search_hash) {
        $response = array();
        if ($from_cache == false) {
            $GLOBALS['CI']->session->set_userdata(array($search_hash => date("Y-m-d H:i:s")));
            $response['session_start_time'] = $GLOBALS ['CI']->config->item('flight_search_session_expiry_period');
        } else {
            $start_time = $GLOBALS['CI']->session->userdata($search_hash);
            $current_time = date("Y-m-d H:i:s");
            $diff = strtotime($current_time) - strtotime($start_time);
            $response['session_start_time'] = $GLOBALS ['CI']->config->item('flight_search_session_expiry_period') - $diff;
        }
        $response['search_hash'] = $search_hash;
        return $response;
    }

    /**
     * 
     * Enter description here ...
     */
    private function status_code_value($status_code) {
        switch ($status_code) {
            case BOOKING_CONFIRMED:
            case SUCCESS_STATUS:
                $status_value = 'BOOKING_CONFIRMED';
                break;
            case BOOKING_HOLD:
                $status_value = 'BOOKING_HOLD';
                break;
            default:
                $status_value = 'BOOKING_FAILED';
        }
        return $status_value;
    }

    /**
     * Returns Single Pax Breakdown
     * @param unknown_type $passenger_fare_breakdown
     */
    private function get_single_pax_fare_breakup($passenger_fare_breakdown) {
        $single_pax_fare_breakup = array();
        foreach ($passenger_fare_breakdown as $k => $v) {
            $PassengerCount = $v['PassengerCount'];
            $single_pax_fare_breakup[$k]['BaseFare'] = ($v['BaseFare'] / $PassengerCount);
            $single_pax_fare_breakup[$k]['Tax'] = ($v['Tax'] / $PassengerCount);
            $single_pax_fare_breakup[$k]['TotalPrice'] = ($v['TotalPrice'] / $PassengerCount);
        }
        return $single_pax_fare_breakup;
    }

    /**
     * 
     * Enter description here ...
     */
    public function reindex_passport_expiry_month($passenger_passport_expiry_month, $search_id) {
        $safe_search_data = $this->search_data($search_id);
        $is_domestic = $safe_search_data['data']['is_domestic'];
        if ($is_domestic == false) {
            foreach ($passenger_passport_expiry_month as $k => $v) {
                $passenger_passport_expiry_month[$k] = ($v + 1);
            }
        }
        return $passenger_passport_expiry_month;
    }

    /**
     * Issue Hold Ticket Request
     * @param unknown_type $app_reference
     * @param unknown_type $sequence_number
     * @param unknown_type $pnr
     * @param unknown_type $booking_id
     */
    function issue_hold_ticket($app_reference, $sequence_number, $pnr, $booking_id) {
        $header = $this->get_header();
        $issue_ticket_request = $this->issue_hold_ticket_request($sequence_number, $pnr, $booking_id, $app_reference);
        if ($issue_ticket_request['status'] == SUCCESS_STATUS) {
            $update_issue_ticket_response = $GLOBALS['CI']->api_interface->get_json_response($issue_ticket_request['data']['service_url'], $issue_ticket_request['data']['request'], $header);
            $GLOBALS['CI']->custom_db->generate_static_response(json_encode($update_issue_ticket_response));

            if ($update_issue_ticket_response['Status'] == SUCCESS_STATUS) {
                $response['status'] = SUCCESS_STATUS;
            } else {
                $response['status'] = FAILURE_STATUS;
            }
        } else {
            $response['status'] = FAILURE_STATUS;
        }
        return $response;
    }

    function issue_hold_ticket_request($sequence_number, $pnr, $booking_id, $app_reference) {

        $response['status'] = true;
        $request['AppReference'] = $app_reference;
        $request['SequenceNumber'] = $sequence_number;
        $request['Pnr'] = $pnr;
        $request['BookingId'] = $booking_id;
        $response['data']['request'] = json_encode($request);
        $this->credentials('IssueHoldTicket');
        $response['data']['service_url'] = $this->service_url;
        return $response;
    }

    /**
     * 
     * extract ExtraService details
     * @param unknown_type $book_params
     */
    public function extract_extra_service_details($book_params) {
        $extra_services = array();
        if (isset($book_params['token']['extra_services']) && isset($book_params['token']['extra_services']['status']) == true && $book_params['token']['extra_services']['status'] == SUCCESS_STATUS && isset($book_params['token']['extra_services']['data']['ExtraServiceDetails']) == true && valid_array($book_params['token']['extra_services']['data']['ExtraServiceDetails']) == true) {

            $ExtraServiceDetails = $book_params['token']['extra_services']['data']['ExtraServiceDetails'];

            //re-index baggage details with BaggageId
            $reindexed_baggage = array();
            if (isset($ExtraServiceDetails['Baggage']) == true && valid_array($ExtraServiceDetails['Baggage']) == true) {
                $Baggage = $ExtraServiceDetails['Baggage'];
                foreach ($Baggage as $ob_k => $ob_v) {
                    foreach ($ob_v as $bk => $bv) {
                        $reindexed_baggage[$bv['BaggageId']] = $bv;
                    }
                }
            }

            //re-index meal details with MealId
            $reindexed_meal = array();
            if (isset($ExtraServiceDetails['Meals']) == true && valid_array($ExtraServiceDetails['Meals']) == true) {
                $Meals = $ExtraServiceDetails['Meals'];
                foreach ($Meals as $om_k => $om_v) {
                    foreach ($om_v as $mk => $mv) {
                        $reindexed_meal[$mv['MealId']] = $mv;
                    }
                }
            }
            //re-index seat details with SeatId
            $reindexed_seat = array();
            if (isset($ExtraServiceDetails['Seat']) == true && valid_array($ExtraServiceDetails['Seat']) == true) {
                $Seat = $ExtraServiceDetails['Seat'];
                foreach ($Seat as $os_k => $os_v) {
                    foreach ($os_v as $sk => $sv) {
                        foreach ($sv as $seat_index => $seat_value) {
                            $reindexed_seat[$seat_value['SeatId']] = $seat_value;
                        }
                    }
                }
            }
            //Meal Preference - re-index meal details with MealId
            $reindexed_meal_pref = array();
            if (isset($ExtraServiceDetails['MealPreference']) == true && valid_array($ExtraServiceDetails['MealPreference']) == true) {
                $Meals = $ExtraServiceDetails['MealPreference'];
                foreach ($Meals as $om_k => $om_v) {
                    foreach ($om_v as $mk => $mv) {
                        $reindexed_meal_pref[$mv['MealId']] = $mv;
                    }
                }
            }
            //Seat Preference - re-index seat details with SeatId
            $reindexed_seat_pref = array();
            if (isset($ExtraServiceDetails['SeatPreference']) == true && valid_array($ExtraServiceDetails['SeatPreference']) == true) {
                $Seats = $ExtraServiceDetails['SeatPreference'];
                foreach ($Seats as $os_k => $os_v) {
                    foreach ($os_v as $sk => $sv) {
                        $reindexed_seat_pref[$sv['SeatId']] = $sv;
                    }
                }
            }

            //Assigning the values
            if (valid_array($reindexed_baggage) == true) {
                $extra_services['ExtraServiceDetails']['Baggage'] = $reindexed_baggage;
            }
            if (valid_array($reindexed_meal) == true) {
                $extra_services['ExtraServiceDetails']['Meals'] = $reindexed_meal;
            }
            if (valid_array($reindexed_seat) == true) {
                $extra_services['ExtraServiceDetails']['Seat'] = $reindexed_seat;
            }
            if (valid_array($reindexed_meal_pref) == true) {
                $extra_services['ExtraServiceDetails']['MealPreference'] = $reindexed_meal_pref;
            }
            if (valid_array($reindexed_seat_pref) == true) {
                $extra_services['ExtraServiceDetails']['SeatPreference'] = $reindexed_seat_pref;
            }
        }

        return $extra_services;
    }

}
