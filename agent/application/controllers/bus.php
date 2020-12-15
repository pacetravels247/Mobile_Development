<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *
 * @package    Provab
 * @subpackage Bus
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V1
 */
class Bus extends CI_Controller {

    private $current_module;

    public function __construct() {
        parent::__construct();
        //we need to activate bus api which are active for current domain and load those libraries
        $this->index();
        $this->load->model('bus_model');
        $this->load->model('user_model'); // we need to load user model to access provab sms library
        $this->load->model('domain_management_model'); 
        $this->load->library('provab_mailer');
        $this->load->library('provab_sms'); // we need this provab_sms library to send sms.
        $this->load->library('utility/notification', '', 'notification');
        $this->current_module = $this->config->item('current_module');
        $this->load->library('api_balance_manager');
        $this->load->library('master_currency');
    }

    /**
     * index page of application will be loaded here
     */
    function index() {
        
    }

    /**
     *  Balu A
     * Load bus Search Result
     * @param number $search_id unique number which identifies search criteria given by user at the time of searching
     */
    function search($search_id) {
        $safe_search_data = $this->bus_model->get_safe_search_data($search_id);
        // debug($safe_search_data);exit;
        // Get all the busses bookings source which are active
        $active_booking_source = $this->bus_model->active_booking_source();
        if ($safe_search_data['status'] == true and valid_array($active_booking_source) == true) {
            $safe_search_data['data']['search_id'] = abs($search_id);
            
            $this->template->view('bus/search_result_page', array('bus_search_params' => $safe_search_data['data'], 'active_booking_source' => $active_booking_source));
        } else {
            $this->template->view('general/popup_redirect');
        }
    }

    /**
     * Balu A
     * @param int $search_id
     * @param date $date
     */
    function prev_next_day_search($search_id, $new_search_date) {
        $current_date = date('d-m-Y');
        if (intval($search_id) > 0 && strtotime($new_search_date) >= strtotime($current_date)) {
            $safe_search_data = $this->bus_model->get_safe_search_data($search_id);
            if ($safe_search_data['status'] == true) {
                $search_params = array();
                $search_params['bus_station_from'] = trim($safe_search_data['data']['bus_station_from']);
                $search_params['from_station_id'] = '';
                $search_params['bus_station_to'] = trim($safe_search_data['data']['bus_station_to']);
                $search_params['to_station_id'] = '';
                $search_params['bus_date_1'] = date('d-m-Y', strtotime($new_search_date));
                redirect(base_url() . 'index.php/general/pre_bus_search?' . http_build_query($search_params));
            } else {
                $this->template->view('general/popup_redirect');
            }
        } else {
            $this->template->view('general/popup_redirect');
        }
    }

    /**
     *  Balu A
     * Passenger Details page for final bookings
     * Here we need to run booking based on api
     */
    function booking($search_id) {
        $pre_booking_params = $this->input->post();
        #debug('55');die();
        // debug($pre_booking_params);exit;
        $safe_search_data = $this->bus_model->get_safe_search_data($search_id);
        $safe_search_data['data']['search_id'] = abs($search_id);
        $page_data['active_payment_options'] = $this->module_model->get_active_payment_module_list();
        if (isset($pre_booking_params['booking_source']) == true) {
            $currency_obj = new Currency(array('module_type' => 'bus', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
            //We will load different page for different API providers... As we have dependency on API for bus details page
            $page_data['search_data'] = $safe_search_data['data'];
            load_bus_lib($pre_booking_params['booking_source']);
            //Need to fill pax details by default if user has already logged in
            $this->load->model('user_model');
            $page_data['pax_details'] = array();

            //Not to show cache data in browser
            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            #debug($pre_booking_params);die();
            if (($pre_booking_params['booking_source'] == PROVAB_BUS_BOOKING_SOURCE || $pre_booking_params['booking_source'] == VRL_BUS_BOOKING_SOURCE || $pre_booking_params['booking_source'] == BITLA_BUS_BOOKING_SOURCE || $pre_booking_params['booking_source'] == KADRI_BUS_BOOKING_SOURCE || $pre_booking_params['booking_source'] == GOTOUR_BUS_BOOKING_SOURCE || $pre_booking_params['booking_source'] == BARDE_BUS_BOOKING_SOURCE || $pre_booking_params['booking_source'] == KONDUSKAR_BUS_BOOKING_SOURCE || $pre_booking_params['booking_source'] == SRS_BUS_BOOKING_SOURCE || $pre_booking_params['booking_source'] == ETS_BUS_BOOKING_SOURCE || $pre_booking_params['booking_source'] == KUKKESHREE_BUS_BOOKING_SOURCE || $pre_booking_params['booking_source'] == KRL_BUS_BOOKING_SOURCE || $pre_booking_params['booking_source'] == INFINITY_BUS_BOOKING_SOURCE) && isset($pre_booking_params['route_schedule_id']) == true and isset($pre_booking_params['pickup_id']) == true and
                count($pre_booking_params['seat']) > 0 and $safe_search_data['status'] == true) {
                $pre_booking_params['token'] = unserialized_data($pre_booking_params['token'], $pre_booking_params['token_key']);
                $bus_details = $pre_booking_params['token'];
                //debug($bus_details);die('789');
              
                //$bus_details = $this->bus_lib->get_bus_details($pre_booking_params['route_schedule_id'], $pre_booking_params['journey_date']);
                if ($pre_booking_params['token'] != false && valid_array($bus_details)) {
                    //Index seat numbers
                    $indexed_seats = $GLOBALS ['CI']->bus_lib->index_seat_number(force_multple_data_format($bus_details ['Layout'] ['SeatDetails'] ['clsSeat']));
                    
                    //Filter only selected seats
                    $selected_seats = array();
                    $total_fare = 0;
                    $_CustomerBuying = 0;
                    $_AdminBuying = 0;
                    $_AgentMarkup = 0;
                    $_AgentBuying = 0;
                    $_Commission = 0;
                    $_tdsCommission = 0;
                    $_AgentEarning = 0;
                    $_TotalPayable = 0;
                    $_GST = 0;
                    $_ServiceTax = 0;
                    $domain_currency_obj = $currency_obj;
                    $page_data['domain_currency_obj'] = $domain_currency_obj;
                    $fare_breakdown = array();
                    $route = $pre_booking_params['token']['Route'];
                    foreach ($pre_booking_params['seat'] as $ssk => $ssv) {
                       
                        $cur_seat_attr = $indexed_seats[$ssv];

                        $cur_seat_attr['CommAmount'] = $route['CommAmount'];
                        //debug($cur_seat_attr);die('789');
                        $formated_seat_data1 = $this->bus_lib->seat_book_format($cur_seat_attr, $currency_obj, 'bus','B2B');
                        //debug($formated_seat_data1);die('789');
                        $formated_seat_data[$ssv] = $formated_seat_data1['b2b_PriceDetails'];
                        $formated_seat_data[$ssv]['seq_no'] = $indexed_seats[$ssv]['seq_no'];
                        $formated_seat_data[$ssv]['seat_id'] = $indexed_seats[$ssv]['seat_id'];
                        $formated_seat_data[$ssv]['decks'] = $indexed_seats[$ssv]['decks'];
                        $formated_seat_data[$ssv]['SeatType'] = $indexed_seats[$ssv]['seat_type'];
                        $formated_seat_data[$ssv]['IsAcSeat'] = $bus_details['Route']['HasAC'];
                        $temp_currency = $currency_obj->get_currency($cur_seat_attr ['Fare'], true, false, true);
                        
                        $total_fare += $formated_seat_data[$ssv]['Fare'];
                        $_CustomerBuying += $formated_seat_data[$ssv]['_CustomerBuying'];
                        $_AdminBuying += $formated_seat_data[$ssv]['_AdminBuying'];
                        $_AgentMarkup += $formated_seat_data[$ssv]['_AgentMarkup'];
                        $_AgentBuying += $formated_seat_data[$ssv]['_AgentBuying'];
                        $_Commission += $formated_seat_data[$ssv]['_Commission'];
                        $_tdsCommission += $formated_seat_data[$ssv]['_tdsCommission'];
                        $_AgentEarning += $formated_seat_data[$ssv]['_AgentEarning'];
                        $_TotalPayable += $formated_seat_data[$ssv]['_TotalPayable'];
                        $_GST += $formated_seat_data[$ssv]['_GST'];

                        $_ServiceTax += $formated_seat_data[$ssv]['service_tax'];

                        $total_seats_fare_data['Fare'] = $total_fare;
                        $total_seats_fare_data['_CustomerBuying'] = $_CustomerBuying;
                        $total_seats_fare_data['_AdminBuying'] = $_AdminBuying;
                        $total_seats_fare_data['_AgentMarkup'] = $_AgentMarkup;
                        $total_seats_fare_data['_AgentBuying'] = $_AgentBuying;
                        $total_seats_fare_data['_Commission'] = $_Commission;
                        $total_seats_fare_data['_tdsCommission'] = $_tdsCommission;
                        $total_seats_fare_data['_AgentEarning'] = $_AgentEarning;
                        $total_seats_fare_data['_TotalPayable'] = $_TotalPayable;
                        $total_seats_fare_data['_GST'] = $_GST;
                        // $total_seats_fare_data 
                        $total_seats_fare_data['_ServiceTax'] = $_ServiceTax;
                    }
                    
                    $page_data['default_currency'] = $temp_currency['default_currency'];
                    $page_data['default_currency_symbol'] = $domain_currency_obj->get_currency_symbol($page_data['default_currency']);
                    $bus_details ['Fare'] = $total_seats_fare_data;
                    $bus_details ['seat_attr'] = $formated_seat_data;
                    $bus_details ['Layout'] ['SeatDetails'] ['clsSeat'] = $selected_seats;
                    $bus_details ['Pickup'] ['clsPickup'] = $GLOBALS ['CI']->bus_lib->index_pickup_number(force_multple_data_format(@$bus_details ['result']['Pickups']));
                    $bus_details ['Drop'] ['clsDrop'] = $GLOBALS ['CI']->bus_lib->index_drop_number(force_multple_data_format(@$bus_details ['result']['Dropoffs']));
                    $bus_details ['CancellationCharges'] ['clsCancellationCharge'] = force_multple_data_format($bus_details ['result'] ['Canc']);
                    // debug($bus_details);exit;
                    //----------- page data
                    $page_data['details'] = $bus_details; 
                    $page_data['pre_booking_params'] = $pre_booking_params;
                    $page_data['pre_booking_params']['default_currency'] = admin_base_currency();
                    $page_data['iso_country_list'] = $this->db_cache_api->get_iso_country_list();
                    $page_data['country_list'] = $this->db_cache_api->get_country_list();
                    $page_data['currency_obj'] = $currency_obj;
                    //Summarize Price
                    //$page_data['price_summary'] = '';

                    $page_data['pax_title_enum'] = get_enum_list('title_bus');
                    $gender_enum = get_enum_list('gender');
                    // TRAVELYAARI does not support others gender so we need to unset this
                    unset($gender_enum [3]);
                    $page_data['gender_enum'] = $gender_enum;
                    $Domain_record = $this->custom_db->single_table_records('domain_list', '*');
                    $agent_data = $this->custom_db->single_table_records('user', '*',array('user_id' =>$this->entity_user_id));
                    // debug($agent_data);exit;
                    $page_data['active_data'] = $Domain_record['data'][0];
                    $page_data['agent_data'] = $agent_data['data'][0];
                    $temp_record = $this->custom_db->get_phone_code_list();
                    $page_data['phone_code'] =$temp_record;

                    $page_data['markup_limits'] = $this->db->where(array('module_type' =>'bus'))->get('agent_markup_limit')->result_array();
                    // debug($page_data);exit;
                    $this->template->view('bus/travelyaari/travelyaari_booking_page', $page_data);
                }
            } else {
                redirect(base_url());
            }
        } else {
            redirect(base_url());
        }
    }

    /**
     *  Balu A
     * Secure Booking of bus
     * 2879 single adult static booking request 2500
     * 261 double room static booking request 2308
     */
    function pre_booking($search_id = 2500, $static_search_result_id = 2879) {
        // redirect(base_url().'index.php/general/booking_not_allowed');     
        // exit;
        $post_params = $this->input->post();

        if($this->entity_status==LOCK && ($post_params ['selected_pm']=="WALLET"))
        {
            redirect(base_url().'index.php/flight/exception?op=locked_user&notification=locked_user');
            exit;
        }
        $this->custom_db->generate_static_response(serialized_data($post_params));
        //Insert To temp_booking and proceed
        /* $post_params = $this->bus_model->get_static_response($static_search_result_id); */
        //Make sure token and temp token matches
        $valid_temp_token = unserialized_data($post_params['token'], $post_params['token_key']);
        //debug($valid_temp_token);die('456');
        if ($valid_temp_token != false) {
			//debug($post_params['booking_source']); exit; 
            load_bus_lib($post_params['booking_source']);
            /*             * **Convert Display currency to Application default currency** */
            //After converting to default currency, storing in temp_booking
            $post_params['token'] = unserialized_data($post_params['token']);
         //debug($post_params);exit('***');
            $currency_obj = new Currency(array(
                'module_type' => 'bus',
                'from' => get_application_currency_preference(),
                'to' => admin_base_currency()
            ));
            //debug($post_params); exit;
            $post_params['token'] = $this->bus_lib->convert_token_to_application_currency($post_params['token'], $currency_obj, $this->current_module);
			
            if(trim($post_params['markup']) != "" && $post_params['markup']>=0){
				foreach($post_params['token']['seat_attr']['seats'] AS $seat_key => $seat_val)
				{
					$post_params['token']['seat_attr']['seats'][$seat_key]['_AgentMarkup'] = 0; 
				}
				$post_params['token']['fare']['_AgentMarkup'] = 0;
			}
           
			//debug($post_params); exit;
            $post_params['token'] = serialized_data($post_params['token']);
            $temp_token = unserialized_data($post_params['token']);
            $amount = $temp_token['fare']['_AgentBuying'];
            //debug($temp_token['fare']); exit;
            $cust_buying = $temp_token['fare']['_CustomerBuying'];
            // if ($post_params['booking_source'] == PROVAB_BUS_BOOKING_SOURCE) {
            //     $amount = $temp_token['seat_attr']['domain_deduction_fare'];
            //     $currency = $temp_token['seat_attr']['default_currency'];
            // }
            //check current balance before proceeding further
            $agent_paybleamount = $currency_obj->get_agent_paybleamount($amount);
            
            $domain_balance_status = $this->domain_management_model->verify_current_balance($agent_paybleamount['amount'], $agent_paybleamount['currency']);
            
            $selected_pm=$post_params ['selected_pm'];
            if(isset($post_params ['bank_code']) && !empty($post_params ['bank_code'])){
                $bank_code = $post_params ['bank_code'];
            }
            else
                $bank_code = 0;
            $selected_pm_array = explode("_", $selected_pm);
            $selected_pm = $selected_pm_array[0];
            $method = $selected_pm_array[1];
            
            if($selected_pm == "WALLET")
                $method = "wallet";
            if($method=="CC")
                $payment_mode = "credit_card";
            else if($method=="DC")
                $payment_mode = "debit_card";
            else if($method=="PPI")
                $payment_mode = "paytm_wallet";
            else if($selected_pm=="TECHP")
                $payment_mode = "net_banking";
            else
                $payment_mode = "wallet";
            
            if ($domain_balance_status == true || $selected_pm) {
                //Block Seats
                //run block and then booking request
                $post_params['token'] = $temp_token;
                //debug($post_params);die('555');
                $block_status = $this->bus_lib->block_seats($search_id, $post_params);
                //debug($block_status);die('99999');
                if ($block_status['status'] == SUCCESS_STATUS) {
                    $post_params['block_key'] = $block_status['data']['result']['HoldKey'];
                    $post_params['block_data'] = $block_status['data']['result']['Passenger'];
                    if($post_params['booking_source'] == BITLA_BUS_BOOKING_SOURCE){
                        $post_params['block_ticket_data'] = $block_status['data']['result'];
                    }else if($post_params['booking_source'] == SRS_BUS_BOOKING_SOURCE){
                        $post_params['block_ticket_data'] = $block_status['data']['result'];
                    }else if($post_params['booking_source'] == ETS_BUS_BOOKING_SOURCE){
                        $post_params['block_ticket_data'] = $block_status['data']['result'];
                    }else if(($post_params['booking_source'] == KUKKESHREE_BUS_BOOKING_SOURCE || $post_params['booking_source'] == KRL_BUS_BOOKING_SOURCE)){
                        $post_params['block_ticket_data'] = $block_status['data']['result'];
                    }else if(($post_params['booking_source'] == KADRI_BUS_BOOKING_SOURCE || $post_params['booking_source'] == GOTOUR_BUS_BOOKING_SOURCE || $post_params['booking_source'] == KONDUSKAR_BUS_BOOKING_SOURCE || $post_params['booking_source'] == BARDE_BUS_BOOKING_SOURCE)){
                        $post_params['block_ticket_data'] = $block_status['data']['result'];
                    }else if(($post_params['booking_source'] == INFINITY_BUS_BOOKING_SOURCE)){
                        $post_params['block_ticket_data'] = $block_status['data']['result'];
                        $post_params['block_key'] = $block_status['data']['result']['BlockId'];                        
                    }  
                    //update seat block details and continue
                    $post_params['token'] = serialized_data($post_params['token']);
                    $temp_booking = $this->module_model->serialize_temp_booking_record($post_params, BUS_BOOKING);
                    $book_id = $temp_booking['book_id'];
                    $book_origin = $temp_booking['temp_booking_origin'];

                    //details for PGI
                    $email = $post_params ['billing_email'];
                    $phone = $post_params ['passenger_contact'];
                    $pgi_amount = $cust_buying;
                    $firstname = $post_params ['contact_name'] ['0'];
                    $productinfo = META_BUS_COURSE;
                    
                    $con_row = $this->master_currency->get_instant_recharge_convenience_fees($pgi_amount, $method, $bank_code);
                    switch ($post_params['payment_method']) {
                        case PAY_NOW :
                            $this->load->model('transaction');
                            $pg_currency_conversion_rate = $currency_obj->payment_gateway_currency_conversion_rate();
                            $this->transaction->create_payment_record($book_id, $pgi_amount, $firstname, $email, $phone, $productinfo, $con_row['cf'], 0, $pg_currency_conversion_rate, $selected_pm, $payment_mode); 
                            redirect(base_url().'index.php/payment_gateway/payment/'.$book_id.'/'.$book_origin.'/'.$selected_pm);
                            //redirect(base_url() . 'index.php/bus/process_booking/' . $book_id . '/' . $book_origin);
                            break;
                        case PAY_AT_BANK : echo 'Under Construction - Remote IO Error';
                            exit;
                            break;
                    }
                } else {
					//debug($block_status['msg']); exit;
                    redirect(base_url() . 'index.php/bus/exception?op=seat_block&notification=' . $block_status['msg']);
                }
            } else {
                redirect(base_url() . 'index.php/bus/exception?op=booking_balance&notification=insufficient balance');
            }
        }

        redirect(base_url() . 'index.php/bus/exception?op=validation_hack&notification=Remote IO error @ Bus Booking');
    }

    /*
      process booking in backend until show loader
     */

    function process_booking($book_id, $temp_book_origin, $is_paid_by_pg=0) {
        //$this->provab_mailer->check_for_low_balance_n_alert();
        //$this->provab_sms->check_for_low_balance_n_alert();
        if ($book_id != '' && $temp_book_origin != '' && intval($temp_book_origin) > 0) {

            $page_data ['form_url'] = base_url() . 'index.php/bus/secure_booking';
            $page_data ['form_method'] = 'POST';
            $page_data ['form_params'] ['book_id'] = $book_id;
            $page_data ['form_params'] ['temp_book_origin'] = $temp_book_origin;
            $page_data ['form_params'] ['is_paid_by_pg'] = $is_paid_by_pg;
            $this->template->view('share/loader/booking_process_loader', $page_data);
        } else {
            redirect(base_url() . 'index.php/bus/exception?op=Invalid request&notification=validation');
        }
    }

    /**
     *  Balu A
     * Do booking once payment is successfull - Payment Gateway
     * and issue voucher
     * BB19-133522-532376/45
     */
    function secure_booking()
    {
        
        // error_reporting(E_ALL);
        $post_data = $this->input->post();
        //debug($post_data);die('HERE');
        if (valid_array($post_data) == true && isset($post_data['book_id']) == true && isset($post_data['temp_book_origin']) == true &&
                empty($post_data['book_id']) == false && intval($post_data['temp_book_origin']) > 0) {
            //verify payment status and continue
            $book_id = trim($post_data['book_id']);
            $temp_book_origin = intval($post_data['temp_book_origin']);
        }
         else {
            redirect(base_url() . 'index.php/bus/exception?op=InvalidBooking&notification=invalid');
        }
        //Check whether amount is paid through PG
        $is_paid_by_pg=$post_data['is_paid_by_pg'];
        //run booking request and do booking
        $temp_booking = $this->module_model->unserialize_temp_booking_record($book_id, $temp_book_origin);
       //debug($temp_booking);die('789');
        //Delete the temp_booking record, after accessing
        // $this->module_model->delete_temp_booking_record($book_id, $temp_book_origin);
        load_bus_lib($temp_booking['booking_source']);
        //verify payment status and continue
        $amount = $temp_booking['book_attributes']['token']['fare']['_AgentBuying'];
        $selected_pm = $temp_booking['book_attributes']['selected_pm'];
        //debug($temp_booking); exit;
        $selected_pm_array = explode("_", $selected_pm);
        $selected_pm = $selected_pm_array[0];
        $method = $selected_pm_array[1];
        //debug($selected_pm_array); exit;
        if($method=="CC"){
            $temp_booking['book_attributes']['payment_method'] = "credit_card";
            $temp_booking['book_attributes']['bank_code'] = 0;
            $temp_booking['book_attributes']['selected_pm'] = $selected_pm;
        }
        else if($method=="DC"){
            $temp_booking['book_attributes']['payment_method'] = "debit_card";
            $temp_booking['book_attributes']['bank_code'] = 0;
            $temp_booking['book_attributes']['bank_code'] = 0;
            $temp_booking['book_attributes']['selected_pm'] = $selected_pm;
        }
        else if($method=="PPI"){
            $temp_booking['book_attributes']['payment_method'] = "paytm_wallet";
            $temp_booking['book_attributes']['bank_code'] = 0;
            $temp_booking['book_attributes']['selected_pm'] = $selected_pm;
        }
        else if($selected_pm == "TECHP"){
            $temp_booking['book_attributes']['payment_method'] = "net_banking";
        }
        else
        {
            $temp_booking['book_attributes']['payment_method'] = "wallet";
        }
		$seat_wise_price_arr = $temp_booking['book_attributes']['token']['seat_attr']['seats'];
		$admin_comm = 0;
		foreach($seat_wise_price_arr AS $seat_price)
		{
			$admin_comm += $seat_price["_AdminCommission"];
		}
		$adm_tds = ($admin_comm/100)*5;
        $api_amount = $temp_booking['book_attributes']['token']['fare']['_AdminBuying']+$adm_tds;
		//debug($api_amount); exit; 
        // $currency = $temp_booking['book_attributes']['token']['seat_attr']['default_currency'];
        $currency_obj = new Currency(array('module_type' => 'bus', 'from' => admin_base_currency(), 'to' => admin_base_currency()));
        //also verify provab balance
        //check current balance before proceeding further
        $agent_paybleamount = $currency_obj->get_agent_paybleamount($amount);
        $agent_earning = $temp_booking['book_attributes']['token']['fare']['_AgentEarning'];
		
        $domain_balance_status = $this->domain_management_model->verify_current_balance($agent_paybleamount['amount'], $agent_paybleamount['currency']);
        //debug($domain_balance_status);die('balance');


        if ($domain_balance_status || $is_paid_by_pg) {
            //lock table
            if ($temp_booking != false) {
                switch ($temp_booking['booking_source']) {
                    case PROVAB_BUS_BOOKING_SOURCE :
                    case VRL_BUS_BOOKING_SOURCE :
                    case BITLA_BUS_BOOKING_SOURCE :
					case SRS_BUS_BOOKING_SOURCE :
                    case ETS_BUS_BOOKING_SOURCE :
                    case KUKKESHREE_BUS_BOOKING_SOURCE :
                    case KRL_BUS_BOOKING_SOURCE :
                    case KADRI_BUS_BOOKING_SOURCE :
                    case GOTOUR_BUS_BOOKING_SOURCE :
                    case KONDUSKAR_BUS_BOOKING_SOURCE :
                    case BARDE_BUS_BOOKING_SOURCE :
                    case INFINITY_BUS_BOOKING_SOURCE :
                        $booking = $this->bus_lib->process_booking($book_id, $temp_booking['book_attributes']);
                        //debug($booking);die('booking');
                        break;
                        //
                }
                
                if ($booking['status'] == SUCCESS_STATUS) {
					if($is_paid_by_pg)
					{
						$remarks = "Your ernings on bus booking credited to wallet";
						$crdit_towards = "Bus booking";
						$this->notification->credit_balance($this->entity_user_id, $book_id, $crdit_towards, $agent_earning, 0, $remarks);
					}
                    if($temp_booking['booking_source'] != BITLA_BUS_BOOKING_SOURCE && $temp_booking['booking_source'] != SRS_BUS_BOOKING_SOURCE && $temp_booking['booking_source'] != ETS_BUS_BOOKING_SOURCE)
                    {
                        $api_amount = 0-$api_amount;
                        $this->api_balance_manager->update_api_balance($temp_booking['booking_source'], $api_amount);
                    }
                    switch ($temp_booking['booking_source']) {
                        case PROVAB_BUS_BOOKING_SOURCE :
                            $get_bus_details = $this->bus_lib->get_booking_details($booking, $temp_booking['booking_source']);
                            $bookings['data']['result'] = $get_bus_details['data']['result']['GetBookingDetails'];
                            $bookings['data']['result']['ticket_details'] = $booking['data']['result']['ticket_details'];
                            $bookings['data']['temp_booking_cache'] = $temp_booking;
                        break;
                        case VRL_BUS_BOOKING_SOURCE :
                            $get_bus_details = $this->bus_lib->format_booking_details($booking, $temp_booking);
                            //debug($get_bus_details);die('5555');
                            $bookings['data']['result'] = $get_bus_details['result'];
                            $bookings['data']['result']['ticket_details'] = $get_bus_details['t_details'];
                            $bookings['data']['temp_booking_cache'] = $temp_booking;
                        break;
                        case BITLA_BUS_BOOKING_SOURCE :
                            $get_bus_details = $this->bus_lib->format_booking_details_bitla($booking, $temp_booking);
                            //debug($get_bus_details);exit('5555');
                            $bookings['data']['result'] = $get_bus_details['result'];
                            $bookings['data']['result']['ticket_details'] = $get_bus_details['t_details'];
                            $bookings['data']['temp_booking_cache'] = $temp_booking;
                        break;
						case SRS_BUS_BOOKING_SOURCE :
                            $get_bus_details = $this->bus_lib->format_booking_details_bitla($booking, $temp_booking);
                            //debug($get_bus_details);exit('5555');
                            $bookings['data']['result'] = $get_bus_details['result'];
                            $bookings['data']['result']['ticket_details'] = $get_bus_details['t_details'];
                            $bookings['data']['temp_booking_cache'] = $temp_booking;
                        break;
                        case ETS_BUS_BOOKING_SOURCE :
                            $get_bus_details = $this->bus_lib->format_booking_details($booking, $temp_booking);
                            $bookings['data']['result'] = $get_bus_details['result'];
                            $bookings['data']['result']['ticket_details'] = $get_bus_details['t_details'];
                            $bookings['data']['temp_booking_cache'] = $temp_booking;
                        break;
                        case KUKKESHREE_BUS_BOOKING_SOURCE :
                            $get_bus_details = $this->bus_lib->format_booking_details_kukkeshree($booking, $temp_booking);
                            //debug($get_bus_details);exit('5555');
                            $bookings['data']['result'] = $get_bus_details['result'];
                            $bookings['data']['result']['ticket_details'] = $get_bus_details['t_details'];
                            $bookings['data']['temp_booking_cache'] = $temp_booking;
                        break;
                        case KRL_BUS_BOOKING_SOURCE :
                            $get_bus_details = $this->bus_lib->format_booking_details_kukkeshree($booking, $temp_booking);
                            //debug($get_bus_details);exit('5555');
                            $bookings['data']['result'] = $get_bus_details['result'];
                            $bookings['data']['result']['ticket_details'] = $get_bus_details['t_details'];
                            $bookings['data']['temp_booking_cache'] = $temp_booking;
                        break;
                        case KADRI_BUS_BOOKING_SOURCE :                            
                            $get_bus_details = $this->bus_lib->format_booking_details($booking, $temp_booking);                            
                            $bookings['data']['result'] = $get_bus_details['result'];
                            $bookings['data']['result']['ticket_details'] = $get_bus_details['t_details'];
                            $bookings['data']['temp_booking_cache'] = $temp_booking;
                        break;
                        case GOTOUR_BUS_BOOKING_SOURCE :                            
                            $get_bus_details = $this->bus_lib->format_booking_details($booking, $temp_booking);                            
                            $bookings['data']['result'] = $get_bus_details['result'];
                            $bookings['data']['result']['ticket_details'] = $get_bus_details['t_details'];
                            $bookings['data']['temp_booking_cache'] = $temp_booking;
                        break;
                        case KONDUSKAR_BUS_BOOKING_SOURCE :                            
                            $get_bus_details = $this->bus_lib->format_booking_details($booking, $temp_booking);                            
                            $bookings['data']['result'] = $get_bus_details['result'];
                            $bookings['data']['result']['ticket_details'] = $get_bus_details['t_details'];
                            $bookings['data']['temp_booking_cache'] = $temp_booking;
                        break;
                        case BARDE_BUS_BOOKING_SOURCE :                            
                            $get_bus_details = $this->bus_lib->format_booking_details($booking, $temp_booking);                            
                            $bookings['data']['result'] = $get_bus_details['result'];
                            $bookings['data']['result']['ticket_details'] = $get_bus_details['t_details'];
                            $bookings['data']['temp_booking_cache'] = $temp_booking;
                        break;
                        case INFINITY_BUS_BOOKING_SOURCE :
                            $get_bus_details = $this->bus_lib->format_booking_details($booking, $temp_booking);                            
                            $bookings['data']['result'] = $get_bus_details['result'];
                            $bookings['data']['result']['ticket_details'] = $get_bus_details['t_details'];
                            $bookings['data']['temp_booking_cache'] = $temp_booking;
                        break;
                    }
                    //die();
                     //debug($bookings);exit('---------');
                    $currency_obj = new Currency(array('module_type' => 'bus', 'from' => admin_base_currency(), 'to' => admin_base_currency()));
                    $bookings['data']['currency_obj'] = $currency_obj;
                    //Save booking based on booking status and book id
                    $data = $this->bus_lib->save_booking($book_id, $bookings['data'], 'b2b');
                    $this->domain_management_model->update_transaction_details('bus', $book_id, $data['fare'], $data['domain_markup'], $data['level_one_markup'], @$data['convinence'], @$data['discount'], $data['transaction_currency'], $data['currency_conversion_rate'], $is_paid_by_pg);

                    
                    //save to accounting software
                    /*$this->load->library('xlpro');
                    $this->xlpro->get_bus_booking_details($booking,$temp_booking);*/
                    
                    //deduct balance and continue
                    // Sms config & Checkpoint
                    /* if(active_sms_checkpoint('booking'))
                      {
                      $msg = "Dear ".$data['name']." Thank you for Booking your ticket with us.Ticket Details will be sent to your email id";
                      //echo $msg;exit;
                      $msg = urlencode($msg);
                      $sms_status = $this->provab_sms->send_msg($data['phone'],$msg);
                      //return $sms_status;
                      } */
                    //sms config ends here,

                    redirect(base_url() . 'index.php/voucher/bus/' . $book_id . '/' . $temp_booking['booking_source'] . '/BOOKING_CONFIRMED/show_voucher/0/1');
                } else {
                    /*$pg_name = $data['booking_billing_type'];
                    if ($is_paid_by_pg){
                        $pg_name = $data['booking_billing_type'];
                        redirect ( base_url () . 'index.php/payment_gateway/refund/'.$book_id.'/'.$pg_name);
                        exit;
                    }
                    redirect ( base_url () . 'index.php/payment_gateway/refund/'.$book_id.'/'.$pg_name);
                    exit;*/
                    redirect(base_url() . 'index.php/bus/exception?op=booking_exception&notification=' . $booking['msg']);
                }
            }
            //release table lock
        } else {
            redirect(base_url() . 'index.php/bus/exception?op=Remote IO error @ Insufficient&notification=validation');
        }
        redirect(base_url() . 'index.php/bus/exception?op=Remote IO error @ bus Secure Booking&notification=validation');
    }

    /**
     *  Balu A
     * Process booking on hold - pay at bank
     */
    function booking_on_hold($book_id) {
        
    }

    /**
     * Balu A
     */
    function pre_cancellation($app_reference, $booking_source) {
        if (empty($app_reference) == false && empty($booking_source) == false) {
        	load_bus_lib($booking_source);
            $page_data = array();
            $master_booking_details = $this->bus_model->get_booking_details($app_reference, $booking_source);
            if ($master_booking_details['status'] == SUCCESS_STATUS) {
                $this->load->library('booking_data_formatter');
                //Assemble Booking Data
                $master_booking_details = $this->booking_data_formatter->format_bus_booking_data($master_booking_details, 'b2b');
                $page_data['data'] = $master_booking_details['data'];
                
                //Getting cancellation charges before cancelling
                $master_booking_details = $master_booking_details['data']['booking_details'][0];
                $PNRNo = trim($master_booking_details['pnr']);
                $TicketNo = trim($master_booking_details['ticket']);
                $SetaNos = $master_booking_details['seat_numbers'];
                $booking_details = array();
                $booking_details['PNRNo'] = $PNRNo;
                $booking_details['TicketNo'] = $TicketNo;
                $booking_details['SeatNos'] = $SetaNos;
                $booking_details['booking_source'] = $master_booking_details['booking_source'];
                $is_cancellable_response_data = $this->bus_lib->pre_cancellation_data($booking_details, $app_reference);
		        //debug($is_cancellable_response_data); exit;
                $page_data["data"]["pre_cancel_data"] = $is_cancellable_response_data;
                //debug($page_data);die();
                $this->template->view('bus/pre_cancellation', $page_data);
            } else {
                redirect('security/log_event?event=Invalid Details');
            }
        } else {
            redirect('security/log_event?event=Invalid Details');
        }
    }

    function get_partial_cancel_value(){
        $this->load->library('provab_mailer');
        $seats = $_POST['seats'];
		$po_list = $_POST['po_list'];
        $tckt_no = $_POST['t_no'];
        $b_s = $_POST['b_s'];
        load_bus_lib($b_s);

        if($b_s == VRL_BUS_BOOKING_SOURCE){
            $s_can = array();
            foreach (explode(',', $seats) as$key => $value) {
                $s_n = explode('-', $value);
                array_push($s_can, $s_n[1]);
            }
            $seats = implode(',', $s_can);
        }
		$pos = explode(",", $po_list);
		$bill_det= array();
		$bill_det["total_fare"] = 0;
		$bill_det["commission_reversed"] = 0;
		$bill_det["tds"] = 0;
		$bill_det["gst"] = 0;
		$bill_det["api_tax"] = 0;
		$bill_det["agent_buying"] = 0;
		$bill_det["agent_markup"] = 0;
		$bill_det["customer_buying"] = 0;
		$bill_det["grand_total"] = 0;
		foreach($pos AS $po)
		{
			$bus_cd = $this->custom_db->single_table_records("bus_booking_customer_details", "*", array("origin"=> $po))["data"][0];
			$attr = json_decode($bus_cd["attr"], true);
			$bill_det["total_fare"] += $attr["_AgentBuying"]+$attr["_Commission"]-$attr["_tdsCommission"];
			$bill_det["commission_reversed"] += $attr["_Commission"];
			$bill_det["tds"] += $attr["_tdsCommission"];
			$bill_det["gst"] += $attr["_GST"];
			$bill_det["api_tax"] += $attr["_ServiceTax"];
			$bill_det["agent_buying"] += $attr["_AgentBuying"];
			$bill_det["customer_buying"] += $attr["_CustomerBuying"];
			$bill_det["agent_markup"] = $bill_det["customer_buying"]-$bill_det["total_fare"]; 
			$bill_det["grand_total"] = $bill_det["customer_buying"];
		}
        $reaponse = $this->bus_lib->pre_partial_cancel($seats,$tckt_no,$b_s);
		$reaponse["is_ticket_cancellable"]["bill_det"] = $bill_det;
        if(isset($reaponse['is_ticket_cancellable']) && $reaponse['is_ticket_cancellable']['is_cancellable'] == true){
            echo json_encode($reaponse['is_ticket_cancellable']);
        }else{
            exit();
        }
    }

    /*
     * Balu A
     * Process the Booking Cancellation
     * Full Booking Cancellation
     *
     */

    function cancel_booking() {
        //echo 'Under Construction';exit;
        //$app_reference, $booking_source
        $post_data = $this->input->post();
        $app_reference = $post_data['app_reference'];
        $booking_source = $post_data['booking_source'];
        $seat_to_cancel = '';
        $seat_no = array();
        if($booking_source == ETS_BUS_BOOKING_SOURCE || $booking_source == INFINITY_BUS_BOOKING_SOURCE){
            $cancel_type = 'full';
        }else if($booking_source == VRL_BUS_BOOKING_SOURCE){
            $cancel_type = $post_data['cancel_type'];
            $seat_no = array();
            $s_can = array();
            foreach (explode(',', $post_data['selected_seat']) as $key => $value) {
                $s_n = explode('-', $value);
                array_push($seat_no, $s_n[0]);
                array_push($s_can, $s_n[1]);
            }
            $seat_to_cancel = implode(',', $s_can);
        }else{
            $cancel_type = $post_data['cancel_type'];
            $seat_to_cancel = $post_data['selected_seat'];
            $seat_no = explode(',', $post_data['selected_seat']);
        }
        //debug($seat_to_cancel);die();
        if (empty($app_reference) == false) {
            $master_booking_details = $this->bus_model->get_booking_details($app_reference, $booking_source);
            //debug($master_booking_details);die('----------');
            if ($master_booking_details['status'] == SUCCESS_STATUS) {
                $this->load->library('booking_data_formatter');
                $master_booking_details = $this->booking_data_formatter->format_bus_booking_data($master_booking_details, 'b2b');
                //debug($master_booking_details);die('+===');
				$booking_data = $master_booking_details;
                $master_booking_details = $master_booking_details['data']['booking_details'][0];
				//debug($master_booking_details); exit;
				$lead_phone = trim($master_booking_details['phone_number']);
				$agent_phone = trim($this->CI->entity_phone);
                $PNRNo = trim($master_booking_details['pnr']);
                $TicketNo = trim($master_booking_details['ticket']);
                $SetaNos = $master_booking_details['seat_numbers'];
                $booking_details = array();
                $booking_details['PNRNo'] = $PNRNo;
                $booking_details['TicketNo'] = $TicketNo;
                $booking_details['SeatNos'] = $SetaNos;
                $booking_details['booking_source'] = $master_booking_details['booking_source'];
                //debug($master_booking_details);exit;
                load_bus_lib($booking_source);
                $cancellation_details = $this->bus_lib->cancel_full_booking($booking_details, $app_reference,$seat_to_cancel); //Invoke Cancellation Methods
                 //debug($cancellation_details);exit();
                if ($cancellation_details['status'] == true) {//IF Cancellation is Success
                    $no_of_passengers = count($master_booking_details["booking_customer_details"]);
                    $markup_to_credit_back = $master_booking_details["admin_markup"]/$no_of_passengers;

                    $__comm = ($master_booking_details["agent_commission"] - $master_booking_details["agent_tds"]);
					$__adm_comm = ($master_booking_details["admin_commission"] - $master_booking_details["admin_tds"]);
					
                    $commission_to_deduct = 0;
					$supp_comm_reversed = 0;
                    if($booking_source == ETS_BUS_BOOKING_SOURCE || $booking_source == INFINITY_BUS_BOOKING_SOURCE){
                        $commission_to_deduct = $__comm;
						$gst_to_add = $master_booking_details["gst"];
						$markup_to_credit_back = $master_booking_details["admin_markup"];
						$supp_comm_reversed = $__adm_comm;
                    }else{
                        $_to_be_cncl = count($seat_no);
                        $_total_seat = count($master_booking_details['booking_customer_details']);
                        $commission_to_deduct = ($__comm/$_total_seat)*$_to_be_cncl;
						$gst_to_add = ($master_booking_details["gst"]/$no_of_passengers)*$_to_be_cncl;
						$markup_to_credit_back = $markup_to_credit_back*$_to_be_cncl;
						$supp_comm_reversed = ($__adm_comm/$no_of_passengers)*$_to_be_cncl;
                    }
					//debug($supp_comm_reversed); exit;
                    $cancellation_details["admin_markup"] = $markup_to_credit_back;
					$cancellation_details["data"]["supp_commission_reversed"] = $supp_comm_reversed;
                    $cancellation_details1 = $this->bus_lib->save_cancellation_data($app_reference, $cancellation_details, $cancel_type,$seat_no,$commission_to_deduct); //Save Cancellation Data
                    $cancellation_id = $cancellation_details1["result"]["insert_id"];
					
					$update_booking_data = array();
					$update_booking_data["cancelled_date"] = date("Y-m-d H:i:s");
					$update_booking_data["is_cancelled"] = 1;
					$update_condition["app_reference"] = $app_reference;
					$this->custom_db->update_record("bus_booking_details", $update_booking_data, $update_condition);
					
                    //debug($cancellation_details); exit;
            		//Update Agent Balance
            		$cancellation_details = $this->custom_db->single_table_records("bus_cancellation_details", "*", 
            			array("origin"=>$cancellation_id));
					$comm_reversed = $cancellation_details['data'][0]['commission_reversed'];
					$tds = ($comm_reversed/100)*5;
					//Plus $tds from below $refund _amount have been taken out
                    $refund_amount  = ($cancellation_details['data'][0]['refund_amount'] - $comm_reversed);
                    $cancel_charge  = $cancellation_details['data'][0]['cancel_charge'];
                    
                    $api_amount  = $cancellation_details['data'][0]['api_refund_amount'];
                    $this->api_balance_manager->update_api_balance($booking_details['booking_source'], $api_amount);

                    // Update Transaction log
                    $remarks = "Bus Cancellation refund credited to Wallet";
                    $crdit_towards = "Bus Cancellation";
                    $this->notification->credit_balance($this->entity_user_id, $app_reference, $crdit_towards, $refund_amount, $cancel_charge, $remarks);
					//Send SMS
					$this->provab_sms->send_msg($lead_phone, $booking_data, "594355");
					$this->provab_sms->send_msg($agent_phone, $booking_data, "594355");
                    
					//redirect('voucher/bus/' . $app_reference . '/' . $booking_source.'/BOOKING_CANCELLED/send_credit_note/0/1/'.$refund_amount.'/'.$cancel_charge);
                    redirect('bus/ticket_cancellation_details?app_reference='.$app_reference . '&booking_source='.$booking_source.'&status=BOOKING_CANCELLED');
                    //save to accounting software
                    /*$this->load->library('xlpro');
                    $this->xlpro->get_bus_sales_return_details($app_reference,$booking_details,$cancellation_details);
                    die();*/
                }
                redirect('bus/cancellation_details/' . $app_reference . '/' . $booking_source);
            } else {
                redirect('security/log_event?event=Invalid Details');
            }
        } else {
            redirect('security/log_event?event=Invalid Details');
        }
    }

    /**
     * Balu A
     * Cancellation Details
     * @param $app_reference
     * @param $booking_source
     */
    function cancellation_details($app_reference, $booking_source) {
        if (empty($app_reference) == false && empty($booking_source) == false) {
            $master_booking_details = $GLOBALS['CI']->bus_model->get_booking_details($app_reference, $booking_source);
            if ($master_booking_details['status'] == SUCCESS_STATUS) {
                $page_data = array();
                $this->load->library('booking_data_formatter');
                $master_booking_details = $this->booking_data_formatter->format_bus_booking_data($master_booking_details, 'b2b');
                $page_data['data'] = $master_booking_details['data'];
                $this->template->view('bus/cancellation_details', $page_data);
            } else {
                redirect('security/log_event?event=Invalid Details');
            }
        } else {
            redirect('security/log_event?event=Invalid Details');
        }
    }
    /**
     * Displays Cancellation Ticket Details
     */
    public function ticket_cancellation_details()
    {
        $get_data = $this->input->get();
        //debug($get_data); exit;
        if(isset($get_data['app_reference']) == true && isset($get_data['booking_source']) == true && isset($get_data['status']) == true){
            $app_reference = trim($get_data['app_reference']);
            $booking_source = trim($get_data['booking_source']);
            $status = trim($get_data['status']);
            $booking_details = $this->bus_model->get_booking_details($app_reference, $booking_source);
             // debug($booking_details);exit;
            if($booking_details['status'] == SUCCESS_STATUS){
                $this->load->library('booking_data_formatter');
                $booking_details = $this->booking_data_formatter->format_bus_booking_data($booking_details, $this->config->item('current_module'));
                $page_data = array();
                $booked_user_id = intval($booking_details['data']['booking_details'][0]['created_by_id']);
                $booked_user_details = array();
                $is_agent = false;
                $user_condition[] = array('U.user_id' ,'=', $booked_user_id);
                $booked_user_details = $this->user_model->get_user_details($user_condition);
                if(valid_array($booked_user_details) == true){
                    $booked_user_details = $booked_user_details[0];
                    if($booked_user_details['user_type'] == B2B_USER){
                        $is_agent = true;
                    }
                }
                $page_data['booking_data'] = $booking_details['data'];
//debug($page_data['booking_data']); die;
                $page_data['booked_user_details'] = $booked_user_details;
                $page_data['is_agent'] =            $is_agent;
                $this->template->view('bus/ticket_cancellation_details', $page_data);
            } else {
                redirect(base_url());
            }
        } else {
            redirect(base_url());
        }
    }
    /**
     * Balu A
     * Displays Cancellation Refund Details
     * @param unknown_type $app_reference
     * @param unknown_type $status
     */
    public function cancellation_refund_details() {
        $get_data = $this->input->get();
        if (isset($get_data['app_reference']) == true && isset($get_data['booking_source']) == true && isset($get_data['status']) == true && $get_data['status'] == 'BOOKING_CANCELLED') {
            $app_reference = trim($get_data['app_reference']);
            $booking_source = trim($get_data['booking_source']);
            $status = trim($get_data['status']);
            $call_to_show = "partial_cancellation_also"; 
            $booking_details = $this->bus_model->get_booking_details($app_reference, $booking_source, $status, $call_to_show);
            if ($booking_details['status'] == SUCCESS_STATUS) {
                $page_data = array();
                $page_data['booking_data'] = $booking_details;
                $this->template->view('bus/cancellation_refund_details', $page_data);
            } else {
                redirect(base_url());
            }
        } else {
            redirect(base_url());
        }
    }

    /**
     * Balu A
     */
   function exception() {
        $module = META_BUS_COURSE;
        $op = @$_GET['op'];
        $notification = @$_GET['notification'];
        // echo $notification;exit;
        $exception = $this->module_model->flight_log_exception ( $module, $op, $notification );
        //debug($exception);exit;
        $exception = urlencode(json_encode($exception));
        //debug($exception);exit;
        // set ip log session before redirection
        $this->session->set_flashdata ( array (
                'log_ip_info' => true 
        ) );
        redirect ( base_url () . 'index.php/bus/event_logger/' . $exception );
    }

    function event_logger($exception = '') {
        $log_ip_info = $this->session->flashdata('log_ip_info');
        $this->template->view('bus/exception', array('log_ip_info' => $log_ip_info, 'exception' => $exception));
    }


    /////
    function bus_search_test(){
        $__to = $_GET['to'];
        $__from = $_GET['from'];
        $__date = date('d-m-Y');
        $from_station_id=0;
        $to_station_id = 0;

        $array = array(
            'bus_station_from' => $__from,
            'from_station_id' => $from_station_id,
            'bus_station_to' => $__to,
            'to_station_id' => $to_station_id,
            'bus_date_1' => $__date,
        );

        $ar = array(
            'search_type' => 'VHCID1433498307',
            'search_data' => json_encode($array),
            'created_datetime' => date('Y-m-d h:i:s'),
        );

        $s_data =  $this->custom_db->insert_record('search_history',$ar);
        
        $search_id = $s_data['insert_id'];
        $search_params1['bus_station_from'] = trim($__from);
        $search_params1['from_station_id'] = '';
        $search_params1['bus_station_to'] = trim($__to);
        $search_params1['to_station_id'] = '';
        $search_params1['bus_date_1'] = $__date;

       // /index.php/bus/search/2386?bus_station_from=Bangalore&from_station_id=1570&bus_station_to=Hyderabad&to_station_id=6505&bus_date_1=26-12-2019
        redirect(base_url() . 'index.php/bus/search/'.$search_id.'?' . http_build_query($search_params1));
    }

}
