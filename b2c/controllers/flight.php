<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *
 * @package Provab
 * @subpackage Flight
 * @author Balu A<balu.provab@gmail.com>
 * @version V1
 */
class Flight extends CI_Controller {

    private $current_module;

    public function __construct() {
        parent::__construct();
        // $this->output->enable_profiler(TRUE);
        $this->load->model('flight_model');
        $this->load->model('user_model'); // we need to load user model to access provab sms library
        $this->load->library('provab_sms'); // we need this provab_sms library to send sms.
        $this->load->library('social_network/facebook'); //Facebook Library to enable share button
        $this->current_module = $this->config->item('current_module');
        $this->load->library('provab_mailer');
    }

    /**
     * App Validation and reset of data
     */
    function pre_calendar_fare_search() {
        $params = $this->input->get();
        $safe_search_data = $this->flight_model->calendar_safe_search_data($params);
        //Need to check if its domestic travel
        $from_loc = $safe_search_data['from_loc'];
        $to_loc = $safe_search_data['to_loc'];
        $safe_search_data['is_domestic_one_way_flight'] = false;

        $safe_search_data['is_domestic_one_way_flight'] = $this->flight_model->is_domestic_flight($from_loc, $to_loc);
        if ($safe_search_data['is_domestic_one_way_flight'] == false) {
            $page_params['from'] = '';
            $page_params['to'] = '';
        } else {
            $page_params['from'] = $safe_search_data['from'];
            $page_params['to'] = $safe_search_data['to'];
        }

        $page_params['depature'] = $safe_search_data['depature'];
        $page_params['carrier'] = $safe_search_data['carrier'];
        $page_params['adult'] = $safe_search_data['adult'];
        redirect(base_url() . 'index.php/flight/calendar_fare?' . http_build_query($page_params));
    }

    /**
     * Airfare calendar
     */
    function calendar_fare() {
        $params = $this->input->get();
        $active_booking_source = $this->flight_model->active_booking_source();
        if (valid_array($active_booking_source) == true) {
            $safe_search_data = $this->flight_model->calendar_safe_search_data($params);
            $page_params = array(
                'flight_search_params' => $safe_search_data,
                'active_booking_source' => $active_booking_source
            );
            $page_params ['from_currency'] = get_application_default_currency();
            $page_params ['to_currency'] = get_application_currency_preference();
            $this->template->view('flight/calendar_fare_result', $page_params);
        }
    }

    /**
     * Jaganaath
     */
    function add_days_todate() {

        $get_data = $this->input->get();
        if (isset($get_data['search_id']) == true && intval($get_data['search_id']) > 0 && isset($get_data['new_date']) == true && empty($get_data['new_date']) == false) {
            $search_id = intval($get_data['search_id']);
            $new_date = trim($get_data['new_date']);
            $safe_search_data = $this->flight_model->get_safe_search_data($search_id);

            $day_diff = get_date_difference($safe_search_data['data']['depature'], $new_date);
            if (valid_array($safe_search_data) == true && $safe_search_data['status'] == true) {
                $safe_search_data = $safe_search_data['data'];
                $search_params = array();
                $search_params['trip_type'] = trim($safe_search_data['trip_type']);
                $search_params['from'] = trim($safe_search_data['from']);
                $search_params['to'] = trim($safe_search_data['to']);
                $search_params['depature'] = date('d-m-Y', strtotime($new_date)); //Adding new Date
                if (isset($safe_search_data['return'])) {
                    $search_params['return'] = add_days_to_date($day_diff, $safe_search_data['return']); //Check it
                }
                $search_params['adult'] = intval($safe_search_data['adult_config']);
                $search_params['child'] = intval($safe_search_data['child_config']);
                $search_params['infant'] = intval($safe_search_data['infant_config']);
                $search_params['search_flight'] = 'search';
                $search_params['v_class'] = trim($safe_search_data['v_class']);
                $search_params['carrier'] = $safe_search_data['carrier'];
                redirect(base_url() . 'index.php/general/pre_flight_search/?' . http_build_query($search_params));
            } else {
                $this->template->view('general/popup_redirect');
            }
        } else {
            $this->template->view('general/popup_redirect');
        }
    }

    /**
     * Balu A
     * Search Request from Fare Calendar
     */
    function pre_fare_search_result() {
        $get_data = $this->input->get();
        if (isset($get_data['from']) == true && empty($get_data['from']) == false &&
                isset($get_data['to']) == true && empty($get_data['to']) == false &&
                isset($get_data['depature']) == true && empty($get_data['depature']) == false) {
            $from = trim($get_data['from']);
            $to = trim($get_data['to']);
            $depature = trim($get_data['depature']);
            $from_loc_details = $this->custom_db->single_table_records('flight_airport_list', '*', array('airport_code' => $from));
            $to_loc_details = $this->custom_db->single_table_records('flight_airport_list', '*', array('airport_code' => $to));
            if ($from_loc_details['status'] == true && $to_loc_details['status'] == true) {
                $depature = date('Y-m-d', strtotime($depature));
                $airport_code = trim($from_loc_details['data'][0]['airport_code']);
                $airport_city = trim($from_loc_details['data'][0]['airport_city']);
                $from = $airport_city . ' (' . $airport_code . ')';
                //To
                $airport_code = trim($to_loc_details['data'][0]['airport_code']);
                $airport_city = trim($to_loc_details['data'][0]['airport_city']);
                $to = $airport_city . ' (' . $airport_code . ')';

                //Forming Search Request
                $search_params = array();
                $search_params['trip_type'] = 'oneway';
                $search_params['from'] = $from;
                $search_params['to'] = $to;
                $search_params['depature'] = $depature;
                $search_params['adult'] = 1;
                $search_params['child'] = 0;
                $search_params['infant'] = 0;
                $search_params['search_flight'] = 'search';
                $search_params['v_class'] = 'Economy';
                $search_params['carrier'] = array('');
                redirect(base_url() . 'index.php/general/pre_flight_search/?' . http_build_query($search_params));
            } else {
                $this->template->view('general/popup_redirect');
            }
        } else {
            $this->template->view('general/popup_redirect');
        }
    }

    /**
     * Search Result
     * @param number $search_id
     */
    function search($search_id) {
        $safe_search_data = $this->flight_model->get_safe_search_data($search_id);
        // Get all the FLIGHT bookings source which are active
        $active_booking_source = $this->flight_model->active_booking_source();

        if (valid_array($active_booking_source) == true && $safe_search_data ['status'] == true) {
            $safe_search_data ['data'] ['search_id'] = abs($search_id);
            $page_params = array(
                'flight_search_params' => $safe_search_data ['data'],
                'active_booking_source' => $active_booking_source
            );
            $page_params ['from_currency'] = get_application_default_currency();
            $page_params ['to_currency'] = get_application_currency_preference();

            //Need to check if its domestic travel
            $from_loc = $safe_search_data['data']['from_loc'];
            $to_loc = $safe_search_data['data']['to_loc'];
            $page_params['is_domestic_one_way_flight'] = false;
            if ($safe_search_data['data']['trip_type'] == 'oneway') {
                $page_params['is_domestic_one_way_flight'] = $this->flight_model->is_domestic_flight($from_loc, $to_loc);
            }
            $page_params['airline_list'] = $this->db_cache_api->get_airline_code_list(); //Balu A
            $this->template->view('flight/search_result_page', $page_params);
        } else {
            if ($safe_search_data['status'] == true) {
                $this->template->view('general/popup_redirect');
            } else {
                $this->template->view('flight/exception');
            }
        }
    }

    /**
     * Balu A
     * Passenger Details page for final bookings
     * Here we need to run farequote/booking based on api
     * View Page for booking
     */
    function booking($search_id) {

        $pre_booking_params = $this->input->post();
        //debug($pre_booking_params);exit;
        $search_hash = $pre_booking_params ['search_hash'];
        load_flight_lib($pre_booking_params ['booking_source']);
        $safe_search_data = $this->flight_lib->search_data($search_id);
        $safe_search_data ['data'] ['search_id'] = intval($search_id);
        $token = $this->flight_lib->unserialized_token($pre_booking_params ['token'], $pre_booking_params ['token_key']);
        if ($token ['status'] == SUCCESS_STATUS) {
            $pre_booking_params ['token'] = $token ['data'] ['token'];
        }
        if (isset($pre_booking_params ['booking_source']) == true && $safe_search_data ['status'] == true) {
            //Balu A - Check Travel is Domestic or International
            $from_loc = $safe_search_data['data']['from_loc'];
            $to_loc = $safe_search_data['data']['to_loc'];
            $safe_search_data['data']['is_domestic_flight'] = $this->flight_model->is_domestic_flight($from_loc, $to_loc);

            $page_data ['active_payment_options'] = $this->module_model->get_active_payment_module_list();
            $page_data ['search_data'] = $safe_search_data ['data'];
            // We will load different page for different API providers... As we have dependency on API for Flight details
            $page_data ['search_data'] = $safe_search_data ['data'];
            //Need to fill pax details by default if user has already logged in
            $this->load->model('user_model');
            $page_data['pax_details'] = $this->user_model->get_current_user_details();


            //Not to show cache data in browser
            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            // debug($pre_booking_params);exit;
            switch ($pre_booking_params ['booking_source']) {
                case PROVAB_FLIGHT_BOOKING_SOURCE :
                case STAR_BOOKING_SOURCE :
                case SPICEJET_BOOKING_SOURCE :
                case TRAVELPORT_GDS_BOOKING_SOURCE :
                case TRAVELPORT_ACH_BOOKING_SOURCE :
                case INDIGO_BOOKING_SOURCE :
                    // upate fare details
                    $quote_update = $this->fare_quote_booking($pre_booking_params);

                    // $extra_services = $this->get_extra_services($pre_booking_params);
                    // exit;
                    if ($quote_update ['status'] == FAILURE_STATUS) {
                        redirect(base_url() . 'index.php/flight/exception?op=Remote IO error @ Session Expiry&notification=session');
                    } else {
                        $pre_booking_params = $quote_update ['data'];

                        //Get Extra Services

                        $extra_services = $this->get_extra_services($pre_booking_params);
                        if ($extra_services['status'] == SUCCESS_STATUS) {
                            $page_data['extra_services'] = $extra_services['data'];
                        } else {
                            $page_data['extra_services'] = array();
                        }
                    }
                    $currency_obj = new Currency(array(
                        'module_type' => 'flight',
                        'from' => get_application_currency_preference(),
                        'to' => get_application_currency_preference()
                    ));
                    // Load View
                    $page_data ['booking_source'] = $pre_booking_params ['booking_source'];
                    $page_data ['pre_booking_params'] ['default_currency'] = get_application_default_currency();
                    $page_data ['iso_country_list'] = $this->db_cache_api->get_iso_country_code();
                    $page_data ['country_list'] = $this->db_cache_api->get_iso_country_code();
                    $page_data ['currency_obj'] = $currency_obj;
                    //Traveller Details
                    $page_data['traveller_details'] = $this->user_model->get_user_traveller_details();
                    //Extracting Segment Summary and Fare Details
                    // debug($pre_booking_params);exit;
                    $updated_flight_details = $pre_booking_params['token'];
                    $is_price_Changed = false;
                    $flight_details = array();
                    foreach ($updated_flight_details as $k => $v) {

                        //TODO: Implement this using old and new price
                        /* if($is_price_Changed == false && $v['IsPriceChanged'] == true) {
                          $is_price_Changed = true;
                          } */
                        $temp_flight_details = $this->flight_lib->extract_flight_segment_fare_details($v, $currency_obj, $search_id, $this->current_module);
                        unset($temp_flight_details[0]['BookingType']); //Not needed in Next page
                        $flight_details[$k] = $temp_flight_details[0];
                    }
                    // debug($flight_details);exit;
                    //Merge the Segment Details and Fare Details For Printing Purpose
                    $flight_pre_booking_summary = $this->flight_lib->merge_flight_segment_fare_details($flight_details);
                    // debug($flight_pre_booking_summary);exit;
                    $fare_details = $flight_pre_booking_summary['FareDetails'][$this->current_module.'_PriceDetails'];
                    
                    $admin_price_details = $flight_pre_booking_summary["FareDetails"]["api_PriceDetails"];

                    $api_amount = $fare_details['TotalFare']-$admin_price_details["AgentCommission"]+$admin_price_details["AgentTdsOnCommision"];
                    $api_balance = $this->domain_management_model->verify_api_balance ( $api_amount, $pre_booking_params ['booking_source']);
                    // $api_balance = 0;
                    if($api_balance > $api_amount){

                        $pre_booking_params['token'] = $flight_details;
                        $page_data ['pre_booking_params'] = $pre_booking_params;
                        $page_data['pre_booking_summery'] = $flight_pre_booking_summary;
                        $TotalPrice = $flight_pre_booking_summary['FareDetails'][$this->current_module . '_PriceDetails']['TotalFare'];
                        $page_data['convenience_fees'] = $currency_obj->convenience_fees($TotalPrice, $search_id);
                        $page_data['is_price_Changed'] = $is_price_Changed;

                        //Get the country phone code 
                        $Domain_record = $this->custom_db->single_table_records('domain_list', '*');


                        $page_data['active_data'] = $Domain_record['data'][0];
                        $temp_record = $this->custom_db->get_phone_code_list();

                        $page_data['phone_code'] = $temp_record;


                        if (!empty($this->entity_country_code)) {
                            $mobile_code = $this->db_cache_api->get_mobile_code($this->entity_country_code);
                            $page_data['user_country_code'] = $mobile_code;
                        } else {
                            $page_data['user_country_code'] = $Domain_record['data'][0]['phone_code'];
                        }
                        $convinence_fees_row = $this->private_management_model->get_convinence_fees('flight', $search_id);
                        // debug($convinence_fees_row);exit;
                        $page_data['org_convience_fee'] = 0;
                        if (valid_array($convinence_fees_row)) {
                            if ($convinence_fees_row['type'] == 'percentage') {
                                $page_data['org_convience_fee'] = $convinence_fees_row['value'];
                            }
                        }
                        $page_data['convenience_fees_orginal'] = $convinence_fees_row;
                        $state_list = $this->custom_db->single_table_records('state_list', '*');
                        $state_list_array = array();
                        foreach ($state_list['data'] as $key => $state) {
                            $state_list_array[$state['en_name']] = $state['en_name'];
                        }
                        $page_data['state_list'] = $state_list_array;


                        //session expiry time calculation
                        $page_data['session_expiry_details'] = $this->flight_lib->set_flight_search_session_expiry(true, $search_hash);

                        //Pusing ExtraService details to pre_booking_params array()
                        $page_data['pre_booking_params']['extra_services'] = $extra_services;
                        $temp = $this->custom_db->single_table_records('insurance');

                        $page_data['insurance'] = $temp['data'][0];
                        // debug($page_data);exit;
                        $this->template->view('flight/tbo/tbo_booking_page', $page_data);
                        break;
                    }
                    else{
                        //Added by Anitha Sending SMS for low balance
                        $airline_code = $flight_pre_booking_summary['SegmentSummary'][0]['AirlineDetails']['AirlineName'];
                        $admin_info = $this->custom_db->single_table_records('user','*',array('user_type' =>1));
                        $sms_user_list = $this->user_model->get_all_sms_number_list('739920');
                        // debug($sms_user_list);
                        $admin_phone = $admin_info['data'][0]['phone'];
                        
                        // echo $admin_phone;exit;
                        $phone_number[] = $admin_phone;
                        foreach($sms_user_list as $sms_key => $phone_list){
                            if(!in_array($phone_list['phone'], $phone_number)){
                                $phone_number[] = $phone_list['phone'];
                            }
                        }
                        
                        $sms_data['agent_id'] = "B2C";
                        $sms_data['airline'] = $airline_code;
                        $sms_data['sector'] = $safe_search_data['data']['from'].'-'.$safe_search_data['data']['to'];
                        $sms_data['no_of_passenger'] = $safe_search_data['data']['adult']+$safe_search_data['data']['child']+$safe_search_data['data']['infant'];
                        $sms_data['ticket_amount'] = $api_amount;
                        $sms_data['balance_amount'] = $api_balance;
                        // debug($sms_data);exit;
                        for($i=0; $i<count($phone_number); $i++){
                            $this->provab_sms->send_msg($phone_number[$i], $sms_data, "739920");
                        }
                        // echo "fdgdfghf";exit;
                        redirect(base_url () . 'index.php/flight/exception?op=booking_exception&t&notification=Booking error from supplier Please contact pace support team');
                    }
                }
            } else {
                redirect(base_url());
            }
    }

    /**
     * Fare Quote Booking
     */
    private function fare_quote_booking($flight_booking_details) {

        $fare_quote_details = $this->flight_lib->fare_quote_details($flight_booking_details);
        // debug($fare_quote_details);
        if ($fare_quote_details['status'] == SUCCESS_STATUS && valid_array($fare_quote_details) == true) {
            //Converting API currency data to preferred currency
            $currency_obj = new Currency(array('module_type' => 'flight', 'from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
            $fare_quote_details = $this->flight_lib->farequote_data_in_preferred_currency($fare_quote_details, $currency_obj);
            // debug($fare_quote_details);exit;
        }
        return $fare_quote_details;
    }

    /**
     * Get Extra Services
     */
    private function get_extra_services($flight_booking_details) {
        $extra_service_details = $this->flight_lib->get_extra_services($flight_booking_details);
        return $extra_service_details;
    }

    /**
     * Balu A
     * Secure Booking of FLIGHT
     * Process booking no view page
     */
    function pre_booking($search_id) {

        $post_params = $this->input->post();

        if (valid_array($post_params) == false) {
            redirect(base_url());
        }
        //Setting Static Data - Balu A
        $post_params['billing_city'] = 'Bangalore';
        $post_params['billing_zipcode'] = '560100';
        $post_params['billing_address_1'] = '2nd Floor, Venkatadri IT Park, HP Avenue,, Konnappana Agrahara, Electronic city';
        // Make sure token and temp token matches
        $valid_temp_token = unserialized_data($post_params ['token'], $post_params ['token_key']);

        if ($valid_temp_token != false) {
            load_flight_lib($post_params ['booking_source']);
            $amount = 0;
            $currency = '';
            /*             * **Convert Display currency to Application default currency** */
            //After converting to default currency, storing in temp_booking
            $post_params['token'] = unserialized_data($post_params['token']);
            $currency_obj = new Currency(array(
                'module_type' => 'flight',
                'from' => get_application_currency_preference(),
                'to' => get_application_default_currency()
            ));

            $post_params['token']['token'] = $this->flight_lib->convert_token_to_application_currency($post_params['token']['token'], $currency_obj, $this->current_module);

            //Convert to Extra Services to application currency
            if (isset($post_params['token']['extra_services']) == true) {
                $post_params['token']['extra_services'] = $this->flight_lib->convert_extra_services_to_application_currency($post_params['token']['extra_services'], $currency_obj);
            }

            $post_params['token'] = serialized_data($post_params['token']);

            //Reindex Passport Month
            $post_params['passenger_passport_expiry_month'] = $this->flight_lib->reindex_passport_expiry_month($post_params['passenger_passport_expiry_month'], $search_id);
            //debug($post_params);exit;
            $temp_booking = $this->module_model->serialize_temp_booking_record($post_params, FLIGHT_BOOKING);

            $book_id = $temp_booking ['book_id'];
            $book_origin = $temp_booking ['temp_booking_origin'];
            if ($post_params ['booking_source'] == PROVAB_FLIGHT_BOOKING_SOURCE || $post_params ['booking_source'] == STAR_BOOKING_SOURCE || $post_params ['booking_source'] == SPICEJET_BOOKING_SOURCE || $post_params ['booking_source'] == TRAVELPORT_GDS_BOOKING_SOURCE || $post_params ['booking_source'] == TRAVELPORT_ACH_BOOKING_SOURCE) {
                $currency_obj = new Currency(array(
                    'module_type' => 'flight',
                    'from' => admin_base_currency(),
                    'to' => admin_base_currency()
                ));
                $temp_token = unserialized_data($post_params ['token']);
                $flight_details = $temp_token['token'];
                $flight_booking_summary = $this->flight_lib->merge_flight_segment_fare_details($flight_details);
                $fare_details = $flight_booking_summary['FareDetails'][$this->current_module . '_PriceDetails'];
                $amount = $fare_details['TotalFare'];


                $currency = $fare_details['CurrencySymbol'];
            }

            /*             * ******* Promocode Start ******* */
            $promocode_discount = $post_params['promo_code_discount_val'];
            /*             * ******* Promocode End ******* */

            $email = $post_params ['billing_email'];
            $phone = $post_params ['passenger_contact'];
            $firstname = $post_params ['first_name'] ['0'] . " " . $post_params ['last_name'] ['0'];
            $book_id = $book_id;
            $productinfo = META_AIRLINE_COURSE;

            //Save the Booking Data
            $booking_data = $this->module_model->unserialize_temp_booking_record($book_id, $book_origin);
			
			//Getting convenience fees
			$selected_pm=$post_params ['selected_pm'];
            if(isset($post_params ['bank_code']) && !empty($post_params ['bank_code'])){
                $bank_code = $post_params ['bank_code'];
            }
            else
                $bank_code = 0;
            $selected_pm_array = explode("_", $selected_pm);
            $selected_pm = $selected_pm_array[0];
            $method = $selected_pm_array[1];
            $method = $selected_pm_array[1];
	        //debug($selected_pm_array); exit;
	        if($method=="CC"){
	            $booking_data['book_attributes']['payment_method'] = "credit_card";
	            $booking_data['book_attributes']['bank_code'] = 0;
	            $booking_data['book_attributes']['selected_pm'] = $selected_pm;
	        }
	        else if($method=="DC"){
	            $booking_data['book_attributes']['payment_method'] = "debit_card";
	            $booking_data['book_attributes']['bank_code'] = 0;
	            $booking_data['book_attributes']['bank_code'] = 0;
	            $booking_data['book_attributes']['selected_pm'] = $selected_pm;
	        }
	        else if($method=="PPI"){
	            $booking_data['book_attributes']['payment_method'] = "paytm_wallet";
	            $booking_data['book_attributes']['bank_code'] = 0;
	            $booking_data['book_attributes']['selected_pm'] = $selected_pm;
	        }
	        else if($selected_pm=="TECHP"){
	            $booking_data['book_attributes']['payment_method'] = "net_banking";
	        }
	        else
	        {
	            $booking_data['book_attributes']['payment_method'] = "wallet";
	            $method="wallet";
	        }
			$con_row = $this->master_currency->get_instant_recharge_convenience_fees($pgi_amount, $method, $bank_code);
			
            $book_params = $booking_data['book_attributes'];

            $data = $this->flight_lib->save_booking($book_id, $book_params, $currency_obj, $this->current_module);

            //Add Extra Service Price to Booking Amount
            $extra_services_total_price = $this->flight_model->get_extra_services_total_price($book_id);
            $amount += $extra_services_total_price;

            /*             * ******* Convinence Fees Start ******* */
            $convenience_fees = ceil($currency_obj->convenience_fees($amount, $search_id));
            /*             * ******* Convinence Fees End ******* */

            # Get Insurance Amount
            $temp = $this->custom_db->single_table_records('insurance');
            // $insurance_data = $temp['data'][0]['amount'];
            // if (is_numeric($insurance_data)) {
            //     $insurance_amount = $insurance_data;
            // } else {
            //     $insurance_amount = 0; = 0;
            // }
            $gst_value = $temp_token['gst_value'];
            $selected_pm=$post_params ['selected_pm'];
            if(isset($post_params ['bank_code']) && !empty($post_params ['bank_code'])){
                $bank_code = $post_params ['bank_code'];
            }else{
                $bank_code = 0;
            }
            $selected_pm_array = explode("_", $selected_pm);
            $selected_pm = $selected_pm_array[0];
            $method = $selected_pm_array[1];


            $insurance_amount = 0;
            switch ($post_params ['payment_method']) {
                case PAY_NOW :
                    //redirect(base_url().'index.php/flight/process_booking/'.$book_id.'/'.$book_origin);	
                    $this->load->model('transaction');
                    $pg_currency_conversion_rate = $currency_obj->payment_gateway_currency_conversion_rate();
                    $this->transaction->create_payment_record($book_id, $amount, $firstname, $email, $phone, $productinfo, $con_row['cf'], $promocode_discount, $pg_currency_conversion_rate, $insurance_amount,$gst_value,$selected_pm, $booking_data['book_attributes']['payment_method']);

                    redirect(base_url() . 'index.php/payment_gateway/payment/' . $book_id . '/' . $book_origin. '/' . $selected_pm);

                    // redirect(base_url().'index.php/flight/process_booking/'.$book_id.'/'.$book_origin);	
                    // redirect(base_url() . 'index.php/flight/review_passengers/' . $book_id . '/' . $book_origin);
                    break;
                case PAY_AT_BANK :
                    echo 'Under Construction - Remote IO Error';
                    exit();
                    break;
            }
        }
        redirect(base_url() . 'index.php/flight/exception?op=Remote IO error @ FLIGHT Booking&notification=validation');
    }

    /* review page */

    public function review_passengers($app_reference = '', $book_origin = '') {
        $page_data['app_reference'] = $app_reference;
        $page_data['book_origin'] = $book_origin;

        $this->load->model('flight_model');
        $this->load->library('booking_data_formatter');
        if (empty($app_reference) == false) {

            $booking_details = $this->flight_model->get_booking_details($app_reference, $booking_source, $booking_status);
            if ($booking_details['status'] == SUCCESS_STATUS) {
                load_flight_lib(PROVAB_FLIGHT_BOOKING_SOURCE);
                $assembled_booking_details = $this->booking_data_formatter->format_flight_booking_data($booking_details, 'b2c');
                // debug($assembled_booking_details);die;
                $page_data['data'] = $assembled_booking_details['data'];
                $address = json_decode($booking_details['data']['booking_details']['0']['attributes'], true);
                $page_data['data']['address'] = $address['address'];

                $page_data['data']['logo'] = $assembled_booking_details['data']['booking_details']['0']['domain_logo'];
                $page_data['data']['email'] = $booking_details['data']['booking_details']['0']['email'];
                $page_data ['country_list'] = $this->db_cache_api->get_iso_country_code();
                if (!empty($this->entity_country_code)) {
                    $page_data['user_country_code'] = $this->entity_country_code;
                } else {
                    $page_data['user_country_code'] = '92';
                }
                $page_data['phone_code'] = $this->custom_db->get_phone_code_list();
                $this->template->view('flight/review_passangers_details', $page_data);
            }
        }
    }

    function edit_pax() {
        $params = $this->input->post();

        if (count($params)) {
            $id = $params["origin"];
            $app_reference = $params["app_reference"];
            if (!$params['is_domestic']) {
                $passport_issuing_country = $GLOBALS['CI']->db_cache_api->get_country_list(array('k' => 'origin', 'v' => 'name'), array('origin' => $params['passenger_passport_issuing_country']));
                $params['passport_issuing_country'] = $passport_issuing_country[$params['passenger_passport_issuing_country']];
                $expiry_date = $params["date"][0] . "-" . $params["date"][1] . "-" . $params["date"][2];
                $params["passport_expiry_date"] = $expiry_date;
                $update_data['passport_expiry_date'] = $expiry_date;
                $update_data['passport_number'] = $params['passport_number'];
            }
            $update_data['origin'] = $params['origin'];
            $update_data['app_reference'] = $params['app_reference'];
            $update_data['first_name'] = $params['first_name'];
            $update_data['last_name'] = $params['last_name'];
            $update_data['date_of_birth'] = $params['date_of_birth'];
     $this->flight_model->update_pax_details($update_data, $id);
            redirect("flight/review_passengers/" . $app_reference);
        }
    }

    function edit_booking_details() {
        $params = $this->input->post();
        if (count($params)) {
            $id = $params["origin"];
            $app_reference = $params["app_reference"];
            $update_data["email"] = $params["email"];
            $update_data["phone"] = $params["phone"];
            $this->flight_model->update_booking_details($update_data, $id);
            redirect("flight/review_passengers/" . $app_reference);
        }
    }

    /*
      process booking in backend until show loader
     */

    function process_booking($book_id, $temp_book_origin) {

        if ($book_id != '' && $temp_book_origin != '' && intval($temp_book_origin) > 0) {

            $page_data ['form_url'] = base_url() . 'index.php/flight/secure_booking';
            $page_data ['form_method'] = 'POST';
            $page_data ['form_params'] ['book_id'] = $book_id;
            $page_data ['form_params'] ['temp_book_origin'] = $temp_book_origin;

            $this->template->view('share/loader/booking_process_loader', $page_data);
        } else {
            redirect(base_url() . 'index.php/flight/exception?op=Invalid request&notification=validation');
        }
    }

    /**
     * Balu A
     * Do booking once payment is successfull - Payment Gateway
     * and issue voucher
     */
    function secure_booking() {
        $post_data = $this->input->post();
        //debug($post_data);die();
        if (valid_array($post_data) == true && isset($post_data['book_id']) == true && isset($post_data['temp_book_origin']) == true &&
                empty($post_data['book_id']) == false && intval($post_data['temp_book_origin']) > 0) {

            //verify payment status and continue
            $book_id = trim($post_data['book_id']);
            $temp_book_origin = intval($post_data['temp_book_origin']);
            $this->load->model('transaction');

            $booking_status = $this->transaction->get_payment_status($book_id);

            $booking_status['status'] = 'accepted';
            if ($booking_status['status'] != 'accepted') {
                redirect(base_url() . 'index.php/flight/exception?op=Payment Failed&notification=Payment Failed');
            }
        } else {
            redirect(base_url() . 'index.php/flight/exception?op=InvalidBooking&notification=invalid');
        }

        //run booking request and do booking
        $temp_booking = $this->module_model->unserialize_temp_booking_record($book_id, $temp_book_origin);


        //load_trawelltag_lib(PROVAB_INSURANCE_BOOKING_SOURCE);
        //$response=array();


        //$response = $this->trawelltag->create_policy($temp_booking['book_attributes']);

        //debug($temp_booking['book_attributes']);exit;
        //Delete the temp_booking record, after accessing
        // $this->module_model->delete_temp_booking_record ($book_id, $temp_book_origin);

        load_flight_lib($temp_booking ['booking_source']);
        if ($temp_booking ['booking_source'] == PROVAB_FLIGHT_BOOKING_SOURCE || $temp_booking ['booking_source'] == STAR_BOOKING_SOURCE || $temp_booking ['booking_source'] == SPICEJET_BOOKING_SOURCE || $temp_booking ['booking_source'] == TRAVELPORT_GDS_BOOKING_SOURCE || $temp_booking ['booking_source'] == TRAVELPORT_ACH_BOOKING_SOURCE) {
            $currency_obj = new Currency(array(
                'module_type' => 'flight',
                'from' => admin_base_currency(),
                'to' => admin_base_currency()
            ));
            $flight_details = $temp_booking ['book_attributes'] ['token'] ['token'];
            $flight_booking_summary = $this->flight_lib->merge_flight_segment_fare_details($flight_details);
            $fare_details = $flight_booking_summary['FareDetails'][$this->current_module . '_PriceDetails'];
            $currency = $fare_details['Currency'];
        }
        // debug($temp_booking);exit;
        // verify payment status and continue
        if ($temp_booking != false) {
            switch ($temp_booking ['booking_source']) {
                case PROVAB_FLIGHT_BOOKING_SOURCE :
                case STAR_BOOKING_SOURCE :
                case SPICEJET_BOOKING_SOURCE :
                case TRAVELPORT_GDS_BOOKING_SOURCE :
                case TRAVELPORT_ACH_BOOKING_SOURCE :
                    try {
                        $booking = $this->flight_lib->process_booking($book_id, $temp_booking ['book_attributes']);
                    } catch (Exception $e) {
                        $booking ['status'] = BOOKING_ERROR;
                    }
                    // Save booking based on booking status and book id
                    break;
            }
            if (in_array($booking ['status'], array(SUCCESS_STATUS, BOOKING_CONFIRMED, BOOKING_PENDING, BOOKING_FAILED, BOOKING_ERROR, BOOKING_HOLD, FAILURE_STATUS)) == true) {
                $currency_obj = new Currency(array(
                    'module_type' => 'flight',
                    'from' => admin_base_currency(),
                    'to' => admin_base_currency()
                ));
                $booking ['data'] ['booking_params'] ['currency_obj'] = $currency_obj;
                //Update the booking Details
                $ticket_details = @$booking ['data'] ['ticket'];
                $ticket_details['master_booking_status'] = $booking ['status'];

                $data = $this->flight_lib->update_booking_details($book_id, $booking ['data'] ['booking_params'], $ticket_details, $this->current_module);

                //Update Transaction Details
                $this->domain_management_model->update_transaction_details('flight', $book_id, $data ['fare'], $data ['admin_markup'], $data ['agent_markup'], $data['convinence'], $data['discount'], $data['transaction_currency'], $data['currency_conversion_rate']);


                if (in_array($data ['status'], array(
                            'BOOKING_CONFIRMED',
                            'BOOKING_PENDING',
                            'BOOKING_HOLD'
                        ))) {

                    if($data ['status'] == 'BOOKING_HOLD' ){

                        redirect(base_url () . 'index.php/flight/exception?op=booking_hold_exception&t&notification=Your booking is under process. Kindly wait for the confirmation. Please contact customer support team.');
                    }
                    else{
                        redirect(base_url() . 'index.php/voucher/flight/' . $book_id . '/' . $temp_booking ['booking_source'] . '/' . $data ['status'] . '/show_voucher');
                    }
                    // Sms config & Checkpoint
                    /* if (active_sms_checkpoint ( 'booking' )) {
                      $msg = "Dear " . $data ['name'] . " Thank you for Booking your ticket with us.Ticket Details will be sent to your email id";
                      $msg = urlencode ( $msg );
                      $sms_status = $this->provab_sms->send_msg ( $data ['phone'], $msg );
                      // return $sms_status;
                      } */


                    # Call Insurance API
                    //  load_trawelltag_lib(PROVAB_INSURANCE_BOOKING_SOURCE);
                    //$response=array();
                    // $response = $this->trawelltag->create_policy($temp_booking['book_attributes']);
                    //  $response['app_reference']=$book_id;
                    // $response['travel_date']='2019-04-11';
                    //$save_details = $this->flight_model->save_insurance_details($response);


                    
                } else {
                    redirect(base_url() . 'index.php/flight/exception?op=booking_exception&notification=' . $booking ['message']);
                }
            } else {
                redirect(base_url() . 'index.php/flight/exception?op=booking_exception&notification=' . $booking ['message']);
            }
        }
    }

    /**
     * Balu A
     * Process booking on hold - pay at bank
     * Issue Ticket Later
     */
    function booking_on_hold($book_id) {

        load_trawelltag_lib(PROVAB_INSURANCE_BOOKING_SOURCE);

        $response = array();
        $response = $this->trawelltag->create_policy($response);
        debug($response);
        exit;
        $response['app_reference'] = 'FB-4251-45782-24578';
        $response['travel_date'] = '2019-04-11';
        $save_details = $this->flight_model->save_insurance_details($response);
    }

    /**
     * Balu A
     */
    function pre_cancellation($app_reference, $booking_source) {
        if (empty($app_reference) == false && empty($booking_source) == false) {
            $page_data = array();
            $booking_details = $this->flight_model->get_booking_details($app_reference, $booking_source);
            if ($booking_details['status'] == SUCCESS_STATUS) {
                $this->load->library('booking_data_formatter');
                //Assemble Booking Data
                $assembled_booking_details = $this->booking_data_formatter->format_flight_booking_data($booking_details, $this->current_module);
                $page_data['data'] = $assembled_booking_details['data'];
                $this->template->view('flight/pre_cancellation', $page_data);
            } else {
                redirect('security/log_event?event=Invalid Details');
            }
        } else {
            redirect('security/log_event?event=Invalid Details');
        }
    }

    /**
     * Balu A
     * @param $app_reference
     */
    function cancel_booking() {
        $post_data = $this->input->post();
        if (isset($post_data['app_reference']) == true && isset($post_data['booking_source']) == true && isset($post_data['transaction_origin']) == true &&
                valid_array($post_data['transaction_origin']) == true && isset($post_data['passenger_origin']) == true && valid_array($post_data['passenger_origin']) == true) {
            $app_reference = trim($post_data['app_reference']);
            $booking_source = trim($post_data['booking_source']);
            $transaction_origin = $post_data['transaction_origin'];
            $passenger_origin = $post_data['passenger_origin'];

            $booking_details = $GLOBALS['CI']->flight_model->get_booking_details($app_reference, $booking_source);
            if ($booking_details['status'] == SUCCESS_STATUS) {
                load_flight_lib($booking_source);
                //Formatting the Data
                $this->load->library('booking_data_formatter');
                $booking_details = $this->booking_data_formatter->format_flight_booking_data($booking_details, $this->current_module);
                $booking_details = $booking_details['data'];
                //Grouping the Passenger Ticket Ids
                $grouped_passenger_ticket_details = $this->flight_lib->group_cancellation_passenger_ticket_id($booking_details, $passenger_origin);
                $passenger_origin = $grouped_passenger_ticket_details['passenger_origin'];
                $passenger_ticket_id = $grouped_passenger_ticket_details['passenger_ticket_id'];
                $cancellation_details = $this->flight_lib->cancel_booking($booking_details, $passenger_origin, $passenger_ticket_id);
                // debug($cancellation_details);exit;
                redirect('flight/cancellation_details/' . $app_reference . '/' . $booking_source . '/' . $cancellation_details['status']);
            } else {
                redirect('security/log_event?event=Invalid Details');
            }
        } else {
            redirect('security/log_event?event=Invalid Details');
        }
    }

    function cancellation_details($app_reference, $booking_source, $cancellation_status) {
        if (empty($app_reference) == false && empty($booking_source) == false) {
            $master_booking_details = $GLOBALS['CI']->flight_model->get_booking_details($app_reference, $booking_source);
            if ($master_booking_details['status'] == SUCCESS_STATUS) {
                $page_data = array();
                $this->load->library('booking_data_formatter');
                $master_booking_details = $this->booking_data_formatter->format_flight_booking_data($master_booking_details, $this->current_module);
                $page_data['data'] = $master_booking_details['data'];
                $page_data['cancellation_status'] = $cancellation_status;
                $this->template->view('flight/cancellation_details', $page_data);
            } else {
                redirect('security/log_event?event=Invalid Details');
            }
        } else {
            redirect('security/log_event?event=Invalid Details');
        }
    }

    /**
     * Balu A
     */
    function exception() {
        $module = META_AIRLINE_COURSE;
        $op = @$_GET ['op'];
        $notification = @$_GET ['notification'];
        // echo $notification;exit;
        if ($notification == 'Booking is already done for the same criteria for PNR') {
            $message = 'Please add another criteria and try again';
        } else if ($notification == 'SEAT NOT AVAILABLE' || $notification == 'seat no available') {
            $message = 'Please book another flight and try again';
        } else if ($notification == 'Sell Failure') {
            $message = 'Please try again for the same criteria';
        } else if ($notification == 'The requested class of service is sold out.') {
            $message = 'Please try another booking';
        } else if ($notification == 'Supplier Interaction Failed while adding Pax Details. Reason: 18|Presentation|Fusion DSC found an exception !\n\tThe data does not match the maximum length: \n\tFor data element: freetext\n\tData length should be at least 1 and at most 70\n\tCurrent position in buffer') {
            $message = 'Please add more than 2 characters in the name field and try agian';
        } else if ($notification == 'Agency do not have enough balance.') {
            $message = 'Please add balance and try again';
        } else if ($notification == 'Invalid CommitBooking Request') {
            $message = 'Session is Expired. Please try again';
        } else if ($notification == 'session') {
            $message = 'Session is Expired. Please try again';
        } 
        else if($notification=="Your booking is under process. Kindly wait for the confirmation. Please contact customer support team."){
            $message = $notification;
        }
        else {
            $message = $notification . ' Please try again';
        }
        // echo $message;exit;
        $exception = $this->module_model->flight_log_exception($module, $op, $message);

        $exception = base64_encode(json_encode($exception));
        // debug($exception);exit;
        // set ip log session before redirection
        $this->session->set_flashdata(array(
            'log_ip_info' => true
        ));
        redirect(base_url() . 'index.php/flight/event_logger/' . $exception);
    }

    function event_logger($exception = '') {
      
        $log_ip_info = $this->session->flashdata('log_ip_info');
        $this->template->view('flight/exception', array(
            'log_ip_info' => $log_ip_info,
            'exception' => $exception
        ));
    }

    function test_server() {
        $data = $this->custom_db->single_table_records('test', '*', array('origin' => 851));
        $response = json_decode($data['data'][0]['test'], true);
    }

    function mail_send_voucher($app_reference, $booking_source, $booking_status, $module) {
        // error_reporting(E_ALL);
        send_email($app_reference, $booking_source, $booking_status, $module);
    }

}
