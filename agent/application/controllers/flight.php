<?php
if (! defined ( 'BASEPATH' ))
exit ( 'No direct script access allowed' );
ini_set ( 'max_execution_time', 300 );

/**
 *
 * @package Provab
 * @subpackage Flight
 * @author Balu A<balu.provab@gmail.com>
 * @version V1
 */
//error_reporting(0);
class Flight extends CI_Controller {
    
	private $current_module;
	public function __construct() {
		parent::__construct ();
              
		// $this->output->enable_profiler(TRUE);
		$this->load->model ( 'flight_model' );
		$this->load->model ( 'user_model' ); // we need to load user model to access provab sms library
		$this->load->library ('provab_mailer');
		$this->load->library ('provab_sms'); // we need this provab_sms library to send sms.
		$this->current_module = $this->config->item('current_module');
		$this->load->library('api_balance_manager');
        $this->load->library('utility/notification', '', 'notification');
	}
	/**
	 * FIXME : REMOVE THIS - Balu A
	 */
	function booking_summary()
	{
		$this->template->view('flight/booking_summary');
	}

	/**
	 * App Validation and reset of data
	 */
	function pre_calendar_fare_search()
	{
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
		redirect(base_url().'index.php/flight/calendar_fare?'.http_build_query($page_params));
	}

	/**
	 * Airfare calendar
	 */
	function calendar_fare()
	{
		$params = $this->input->get();
		$active_booking_source = $this->flight_model->active_booking_source ();
		if (valid_array($active_booking_source) == true) {
			$safe_search_data = $this->flight_model->calendar_safe_search_data($params);
			$page_params = array (
					'flight_search_params' => $safe_search_data ,
					'active_booking_source' => $active_booking_source
			);
			$page_params ['from_currency'] = get_application_default_currency ();
			$page_params ['to_currency'] = get_application_currency_preference ();
			$this->template->view ( 'flight/calendar_fare_result', $page_params );
		}
	}
	/**
	 * Jaganaath
	 */
	function add_days_todate()
	{
		$get_data = $this->input->get();
		if(isset($get_data['search_id']) == true && intval($get_data['search_id']) > 0 && isset($get_data['new_date']) == true && empty($get_data['new_date']) == false) {
			$search_id = intval($get_data['search_id']);
			$new_date = trim($get_data['new_date']);
			$safe_search_data = $this->flight_model->get_safe_search_data ( $search_id );

			$day_diff = get_date_difference($safe_search_data['data']['depature'], $new_date);
			if(valid_array($safe_search_data) == true && $safe_search_data['status'] == true) {
				$safe_search_data = $safe_search_data['data'];
				$search_params = array();
				$search_params['trip_type'] = trim($safe_search_data['trip_type']);
				$search_params['from'] = trim($safe_search_data['from']);
				$search_params['to'] = trim($safe_search_data['to']);
				$search_params['depature'] = date('d-m-Y', strtotime($new_date));//Adding new Date
				if(isset($safe_search_data['return'])) {
					$search_params['return'] = add_days_to_date($day_diff, $safe_search_data['return']);//Check it
				}
				$search_params['adult'] = intval($safe_search_data['adult_config']);
				$search_params['child'] = intval($safe_search_data['child_config']);
				$search_params['infant'] = intval($safe_search_data['infant_config']);
				$search_params['search_flight'] = 'search';
				$search_params['v_class'] = trim($safe_search_data['v_class']);
				$search_params['carrier'] = $safe_search_data['carrier'];
				$search_params['lcc_gds'] = '0';
				$search_params['conn_direct'] = '0';

				redirect(base_url().'index.php/general/pre_flight_search/?'.http_build_query($search_params));
			} else {
				$this->template->view ( 'general/popup_redirect');
			}
		} else {
			$this->template->view ( 'general/popup_redirect');
		}
	}
	/**
	 * Balu A
	 * Search Request from Fare Calendar
	 */
	function pre_fare_search_result()
	{
		$get_data = $this->input->get();
		if(isset($get_data['from']) == true && empty($get_data['from']) == false &&
		isset($get_data['to']) == true && empty($get_data['to']) == false &&
		isset($get_data['depature']) == true && empty($get_data['depature']) == false) {
			$from = trim($get_data['from']);
			$to = trim($get_data['to']);
			$depature = trim($get_data['depature']);
			$from_loc_details = $this->custom_db->single_table_records('flight_airport_list', '*', array('airport_code' => $from));
			$to_loc_details = $this->custom_db->single_table_records('flight_airport_list', '*', array('airport_code' => $to));
			if($from_loc_details['status'] == true && $to_loc_details['status'] == true) {
				$depature = date('Y-m-d', strtotime($depature));
				$airport_code = trim($from_loc_details['data'][0]['airport_code']);
				$airport_city = trim($from_loc_details['data'][0]['airport_city']);
				$from = $airport_city.' ('.$airport_code.')';
				//To
				$airport_code = trim($to_loc_details['data'][0]['airport_code']);
				$airport_city = trim($to_loc_details['data'][0]['airport_city']);
				$to = $airport_city.' ('.$airport_code.')';

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
				redirect(base_url().'index.php/general/pre_flight_search/?'.http_build_query($search_params));
			} else {
				$this->template->view ( 'general/popup_redirect');
			}
		} else {
			$this->template->view ( 'general/popup_redirect');
		}
	}
	/**
	 * Search Result
	 * @param number $search_id
	 */
	function search($search_id)
	{     
		$safe_search_data = $this->flight_model->get_safe_search_data ( $search_id );
        $search_url = $_SERVER['QUERY_STRING'];  
        // echo $search_url;exit;   
		// Get all the FLIGHT bookings source which are active
		$active_booking_source = $this->flight_model->active_booking_source ();
		if (valid_array ( $active_booking_source ) == true and $safe_search_data ['status'] == true) {
			$safe_search_data ['data'] ['search_id'] = abs ( $search_id );
			$page_params = array (
					'flight_search_params' => $safe_search_data ['data'],
					'active_booking_source' => $active_booking_source 
			);
			$page_params ['from_currency'] = get_application_default_currency ();
			$page_params ['to_currency'] = get_application_currency_preference ();

			//Need to check if its domestic travel
			$from_loc = $safe_search_data['data']['from_loc'];
			$to_loc = $safe_search_data['data']['to_loc'];
			$page_params['is_domestic_one_way_flight'] = false;
			if ($safe_search_data['data']['trip_type'] == 'oneway') {
				$page_params['is_domestic_one_way_flight'] = $this->flight_model->is_domestic_flight($from_loc, $to_loc);
			}
			$page_params['airline_list'] = $this->db_cache_api->get_airline_code_list();
			$insert_data['url'] = $search_url;
			$insert_data['search_id'] = $search_id;
			$this->custom_db->delete_record('search_url', array('search_id' => $search_id));
			$this->custom_db->insert_record('search_url', $insert_data);
			$page_params['search_url'] = $search_url;
			//Balu A
			$this->template->view ( 'flight/search_result_page', $page_params );
		} else {
			if ($safe_search_data['status'] == true) {
				$this->template->view ( 'general/popup_redirect');
			} else {
				$this->template->view ( 'flight/exception');
			}
		}
	}
	/**
	 * Balu A
	 * Passenger Details page for final bookings
	 * Here we need to run booking based on api
	 * View Page for booking
	 */
	function booking($search_id) 
	{
		$pre_booking_params = $this->input->post ();
		
		$search_hash = $pre_booking_params ['search_hash'];

		load_flight_lib ( $pre_booking_params ['booking_source'] );
		$safe_search_data = $this->flight_lib->search_data ( $search_id );

		$safe_search_data ['data'] ['search_id'] = intval ( $search_id );
		$token = $this->flight_lib->unserialized_token ( $pre_booking_params ['token'], $pre_booking_params ['token_key'] );
		if ($token ['status'] == SUCCESS_STATUS) {
			$pre_booking_params ['token'] = $token ['data'] ['token'];
		}
        if($safe_search_data['data']['is_domestic'] == 1){
            $type = 'domestic';
        }else{
            $type = 'international';
        }
        $flights = $search_result['JourneyList'];
        $bs = $GLOBALS['CI']->custom_db->single_table_records('booking_source', 'origin', array('source_id' => $pre_booking_params['booking_source']));
        $bs_origin = $bs['data'][0]['origin'];

		if (isset ( $pre_booking_params ['booking_source'] ) == true && $safe_search_data ['status'] == true) {
			//Balu A - Check Travel is Domestic or International
			$from_loc = $safe_search_data['data']['from_loc'];
			$to_loc = $safe_search_data['data']['to_loc'];
			$safe_search_data['data']['is_domestic_flight'] = $this->flight_model->is_domestic_flight($from_loc, $to_loc);
			
			$page_data ['active_payment_options'] = $this->module_model->get_active_payment_module_list ();
			$page_data ['search_data'] = $safe_search_data ['data'];
			$currency_obj = new Currency ( array (
					'module_type' => 'flight',
					'from' => get_application_currency_preference (),
					'to' => get_application_currency_preference () 
			) );
			// We will load different page for different API providers... As we have dependency on API for Flight details
			$page_data ['search_data'] = $safe_search_data ['data'];
			//Need to fill pax details by default if user has already logged in
			$this->load->model('user_model');
			$page_data['pax_details'] = array();
			$agent_details = $this->user_model->get_current_user_details();
                        
			$page_data['agent_address'] = $agent_details[0]['address'];
                        $page_data['pax_details'] = $agent_details;

			//Not to show cache data in browser
			header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache");
			switch ($pre_booking_params ['booking_source']) {
				case PROVAB_FLIGHT_BOOKING_SOURCE :
				case STAR_BOOKING_SOURCE :
				case SPICEJET_BOOKING_SOURCE :
				case TRAVELPORT_GDS_BOOKING_SOURCE :
				case TRAVELPORT_ACH_BOOKING_SOURCE :
				case INDIGO_BOOKING_SOURCE :
					// upate fare details
                    $before_fare = $pre_booking_params['token'][0]['Price']['TotalDisplayFare']; 
                    
					$quote_update = $this->fare_quote_booking ( $pre_booking_params );
					if ($quote_update ['status'] == FAILURE_STATUS) {
						redirect ( base_url () . 'index.php/flight/exception?op=Remote IO error @ Session Expiry&notification=session' );
					} else {
						$token = $quote_update['data']['token'];

						foreach($token as $tk_key => $tk_val){
							$base_fare = $tk_val['api_original_price_details']['PriceBreakup']['BasicFare'];
							if($pre_booking_params ['booking_source'] == TRAVELPORT_GDS_BOOKING_SOURCE || $pre_booking_params ['booking_source'] == TRAVELPORT_ACH_BOOKING_SOURCE){
								$class = $tk_val['FlightDetails']['Details'][0][0]['CabinClass'];
								$operator_code = $tk_val['FlightDetails']['Details'][0][0]['OperatorCode'];
								$master_commission = $GLOBALS['CI']->flight_model->master_tp_commission($bs_origin, $class, $operator_code, $type);
								
								if(empty($master_commission) || (@$master_commission[0]['value'] == '0.00')){
				                    $master_commission = $GLOBALS['CI']->flight_model->master_commission_generic_tp($pre_booking_params ['booking_source']);
				                    

				                    if(empty($master_commission) || ($master_commission[0]['value'] == '0.00')){
				                        $master_commission = $GLOBALS['CI']->flight_model->master_general_commission_tp();
				                        
				                    }

				                    if(!empty($master_commission)){
				                        $commission = $master_commission[0]['value'];
				                        $tds = $master_commission[0]['tds'];
				                    }else{
				                        $commission = 0;
				                        $tds = 0;   
				                    }
				                    $commission_value = ($base_fare) * ($commission / 100);;
				                    $commisssion_tds = ($commission_value) * ($tds/100);

			                 	}else{
				                    $basic_commission = $master_commission[0]['value']+$master_commission[0]['plb']+$master_commission[0]['iata'];
				                    $yq_commission = $master_commission[0]['yq'];
				                    $tds_commission = $master_commission[0]['tds'];
				                    $basic_commission_value = ($base_fare) * ($basic_commission / 100);
				                    if(isset($api_original_price_details['PriceBreakup']['Tax_Details']['YQ'])){
				                        $yq_commission_value = ($api_original_price_details['PriceBreakup']['Tax_Details']['YQ']) * ($yq_commission / 100);
				                        $basic_commission_value = $basic_commission_value + $yq_commission_value;
				                    }
				                    $commission_value = $basic_commission_value;
				                    $commisssion_tds = ($commission_value) * ($tds_commission/100);
			                 	}
		                 	}else{
		                 		$master_commission = $GLOBALS['CI']->flight_model->master_commission($pre_booking_params ['booking_source']);

						        if(empty($master_commission) || ($master_commission[0]['value'] == '0.00')){
						            $master_commission = $GLOBALS['CI']->flight_model->master_general_commission();
						        }
						        if(!empty($master_commission)){
						            $commission = $master_commission[0]['value'];
						            $tds = $master_commission[0]['tds'];
						        }else{
						            $commission = 0;
						            $ctds = 0;   
						        }
						        $commission_value = ($base_fare) * ($commission / 100);;
				                    $commisssion_tds = ($commission_value) * ($tds/100);
		                 	}
		                 	$quote_update['data']['token'][$tk_key]['FareDetails']['AgentCommission'] = $commission_value;
		                 	$quote_update['data']['token'][$tk_key]['FareDetails']['AgentTdsOnCommision'] = $commisssion_tds;
						}
						$pre_booking_params = $quote_update ['data'];
						//Get Extra Services
						$extra_services = $this->get_extra_services($pre_booking_params);
						if($extra_services['status'] == SUCCESS_STATUS){
							$page_data['extra_services'] = $extra_services['data'];
						} else {
							$page_data['extra_services'] = array();
						}
						
					}
                                        
					// Load View
					$page_data ['booking_source'] = $pre_booking_params ['booking_source'];
					/*$page_data ['ProvabAuthKey'] = $pre_booking_provabauthkey;*/
					$page_data ['pre_booking_params'] = $pre_booking_params;
					$page_data ['pre_booking_params'] ['default_currency'] = get_application_default_currency ();
					$page_data ['iso_country_list'] = $this->db_cache_api->get_iso_country_code();
					$page_data ['country_list'] = $this->db_cache_api->get_iso_country_code();
					$page_data ['currency_obj'] = $currency_obj;
					$page_data ['state_list'] = $this->custom_db->get_state_list();
					
					//Extracting Segment Summary and Fare Details
					$updated_flight_details = $pre_booking_params['token'];
					
					$flight_details = array();
					$is_price_Changed = false;
					foreach($updated_flight_details as $k => $v) {
						//TODO: Implement this using old and new price
						$after_fare = $updated_flight_details[0]['api_original_price_details']['TotalDisplayFare'];
						if(!empty($after_fare) && ($before_fare != $after_fare)) {
							$is_price_Changed = true;
						}
						$temp_flight_details = $this->flight_lib->extract_flight_segment_fare_details($v, $currency_obj, $search_id, $this->current_module);

						unset($temp_flight_details[0]['BookingType']);//Not needed in Next page
						$flight_details[$k] = $temp_flight_details[0];
					}
                                        
					//Merge the Segment Details and Fare Details For Printing Purpose
					$flight_pre_booking_summary = $this->flight_lib->merge_flight_segment_fare_details($flight_details);

					$fare_details = $flight_pre_booking_summary['FareDetails'][$this->current_module.'_PriceDetails'];
					
					$admin_price_details = $flight_pre_booking_summary["FareDetails"]["api_PriceDetails"];
					$api_amount = $fare_details['_AdminBuying']-$admin_price_details["AgentCommission"]+$admin_price_details["AgentTdsOnCommision"];
					$api_balance = $this->domain_management_model->verify_api_balance ( $api_amount, $pre_booking_params ['booking_source']);
				
					// $api_balance = 0;
					if($api_balance > $api_amount){
						
						$pre_booking_params['token'] = $flight_details;
						$page_data ['pre_booking_params'] = $pre_booking_params;
						$page_data['pre_booking_summery'] = $flight_pre_booking_summary;
						$page_data['is_price_Changed'] = $is_price_Changed;
						$Domain_record = $this->custom_db->single_table_records('domain_list', '*');
						$page_data['active_data'] =$Domain_record['data'][0];
						$temp_record = $this->custom_db->get_phone_code_list();
						$page_data['phone_code'] =$temp_record;

						/*
							session expiry time calculation 
						*/
						
						$page_data['session_expiry_details'] = $this->flight_lib->set_flight_search_session_expiry(true, $search_hash);
						
						//Pusing ExtraService details to pre_booking_params array()
						$page_data['pre_booking_params']['extra_services'] = $extra_services;

						$page_data['markup_limits'] = $this->db->where(array('module_type' =>'flight'))->get('agent_markup_limit')->result_array();

						$this->template->view ( 'flight/tbo/tbo_booking_page', $page_data );
						break;
					}
					else{
						//Added by Anitha Sending SMS for low balance
						$agent_data = $this->user_model->get_agent_info($this->entity_user_id);
						// debug($flight_details);exit;
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
						
						$sms_data['agent_id'] = provab_decrypt($agent_data[0]['uuid']);
						$sms_data['airline'] = $airline_code;
						$sms_data['sector'] = $safe_search_data['data']['from'].'-'.$safe_search_data['data']['to'];
						$sms_data['no_of_passenger'] = $safe_search_data['data']['adult']+$safe_search_data['data']['child']+$safe_search_data['data']['infant'];
						$sms_data['ticket_amount'] = $api_amount;
						$sms_data['balance_amount'] = $api_balance;

						// debug($sms_data);exit;
						for($i=0; $i<count($phone_number); $i++){
							$this->provab_sms->send_msg($phone_number[$i], $sms_data, "739920");
						}

						redirect(base_url () . 'index.php/flight/exception?op=booking_exception&t&notification=Booking error from supplier Please contact pace support team');
					}
					
			}
		} else {
			// redirect(base_url());
		}
	}

	/**
	 * Fare Quote Booking
	 * This will be used for TBO LCC carrier
	 */
	private function fare_quote_booking($flight_booking_details) 
	{
		$fare_quote_details =  $this->flight_lib->fare_quote_details ( $flight_booking_details );
              //  debug($fare_quote_details);exit;
		if($fare_quote_details['status'] == SUCCESS_STATUS) {
			//Converting API currency data to preferred currency
			$currency_obj = new Currency(array('module_type' => 'flight','from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
			$fare_quote_details = $this->flight_lib->farequote_data_in_preferred_currency($fare_quote_details, $currency_obj);
			/*debug($fare_quote_details); exit();*/
		}
		
		return $fare_quote_details;
	}
	/**
	 * Get Extra Services
	 */
	private function get_extra_services($flight_booking_details)
	{
		$extra_service_details =  $this->flight_lib->get_extra_services ( $flight_booking_details );
		return $extra_service_details;
	}
	/**
	 * Balu A
	 * Secure Booking of FLIGHT
	 * 255 single adult static booking request 2310
	 * 261 double room static booking request 2308
	 *
	 * Process booking no view page
	 */
	function pre_booking($search_id) 
	{
		$post_params = $this->input->post ();
		$date_of_birth = $post_params['date_of_birth'];

		foreach ($date_of_birth as $d_key => $d_value) {
			$post_params['date_of_birth'][$d_key] = date('Y-m-d', strtotime($d_value));
		}
		
		if($this->entity_status==LOCK && ($post_params ['selected_pm']=="WALLET"))
		{
			redirect(base_url().'index.php/flight/exception?op=locked_user&notification=locked_user');
			exit;
		}
		//debug($post_params);exit;
		if(valid_array ( $post_params ) == false){
			redirect(base_url ());
		}
		$admin_user = $this->custom_db->single_table_records ( 'user', '*',array ('user_type' => ADMIN))["data"][0];
		if(empty(trim($post_params["gst_number"])))
		{
            /*$post_params["gst_email"] ='vinay@travelomatix.com';
            $post_params["gst_phone"] = 9916100864;
            $post_params["gst_number"] = '29AANCA8324M2Z0';
            $post_params["gst_company_name"] = 'ACCENTRIA SOLUTIONS PRIVATE LIMITED';
            $post_params["gst_address"] = 'Electronic City';
			$post_params["gst_state"] = 'Electronic City';*/
			
            $post_params["gst_email"] = $admin_user["gst_email"];
            $post_params["gst_phone"] = $admin_user["gst_phone"];
            $post_params["gst_number"] = $admin_user["gst_number"];
            $post_params["gst_company_name"] = $admin_user["gst_name"];
            $post_params["gst_address"] = $admin_user["gst_address"];
            $post_params["gst_state"] = $admin_user["state"];
		}
		// $this->custom_db->generate_static_response(json_encode($post_params));
		// Insert To temp_booking and proceed
		/* $post_params = $this->flight_model->get_static_response($static_search_result_id); */

		//Setting Static Data - Balu A
		$post_params['billing_city'] = 'Bangalore';
		$post_params['billing_zipcode'] = '560100';
		
		$selected_pm=$post_params ['selected_pm'];
		// Make sure token and temp token matches
		$valid_temp_token = unserialized_data($post_params['token'], $post_params['token_key']);
		if ($valid_temp_token != false) {
			load_flight_lib ($post_params ['booking_source']);
			$amount = 0;
			$currency = '';
			/****Convert Display currency to Application default currency***/
			//After converting to default currency, storing in temp_booking
			$post_params['token'] = unserialized_data($post_params['token']);
			$currency_obj = new Currency ( array (
					'module_type' => 'flight',
					'from' => get_application_currency_preference (),
					'to' => admin_base_currency() 
			));
			$post_params['token']['token'] = $this->flight_lib->convert_token_to_application_currency($post_params['token']['token'], $currency_obj, $this->current_module);	
			//Convert to Extra Services to application currency
			 if(isset($post_params['token']['extra_services']) == true){
			 	$post_params['token']['extra_services'] = $this->flight_lib->convert_extra_services_to_application_currency($post_params['token']['extra_services'], $currency_obj);
			 	//Get Extra Service Price
				$extra_service_price = $this->extra_service_price($post_params);
			 } else {
			 	$extra_service_price = 0;
			 }	
			 if(trim($post_params['markup'])!="" && $post_params['markup']>=0){
				 $post_params['token']['token'][0]['FareDetails']['b2b_PriceDetails']['_AgentMarkup'] = 0;
			 }
			$post_params['token'] = serialized_data($post_params['token']);
			
			//Reindex Passport Month
			$post_params['passenger_passport_expiry_month'] = $this->flight_lib->reindex_passport_expiry_month($post_params['passenger_passport_expiry_month'], $search_id);
			
			$temp_booking = $this->module_model->serialize_temp_booking_record ($post_params, FLIGHT_BOOKING );
                        
			$book_id = $temp_booking ['book_id'];
			$book_origin = $temp_booking ['temp_booking_origin'];
			if ($post_params ['booking_source'] == PROVAB_FLIGHT_BOOKING_SOURCE || 
			($post_params ['booking_source'] == STAR_BOOKING_SOURCE) || 
			($post_params ['booking_source'] == SPICEJET_BOOKING_SOURCE) || 
			($post_params ['booking_source'] == TRAVELPORT_GDS_BOOKING_SOURCE) || 
			($post_params ['booking_source'] == TRAVELPORT_ACH_BOOKING_SOURCE) ||
			($post_params ['booking_source'] == INDIGO_BOOKING_SOURCE)) {
				$currency_obj = new Currency ( array (
						'module_type' => 'flight',
						'from' => admin_base_currency (),
						'to' => admin_base_currency () 
				));
				$temp_token = unserialized_data ( $post_params ['token'] );
				$flight_details = $temp_token['token'];
				$flight_booking_summary = $this->flight_lib->merge_flight_segment_fare_details($flight_details);
				$fare_details = $flight_booking_summary['FareDetails'][$this->current_module.'_PriceDetails'];
				$amount = $fare_details['_AgentBuying'];
				
				//Adding Extra Service Price to booking amount
				$amount += $extra_service_price;
				$actual_fare = $fare_details['_CustomerBuying']; //send actual fare for checking as requested by client
				$currency = $fare_details['Currency'];
			}
			
			$email= $post_params ['billing_email'];
			$phone = $post_params ['passenger_contact'];
			$pgi_amount = $actual_fare;
			$firstname = $post_params ['first_name'] ['0'] . " " . $post_params ['last_name'] ['0'];
			$book_id = $book_id;
			$productinfo = META_AIRLINE_COURSE ;
			//check current balance before proceeding further
			$agent_paybleamount = $currency_obj->get_agent_paybleamount($amount);
			//If its Hold Ticket then dont check the agent balance
			if(isset($post_params['ticket_method']) == true && $post_params['ticket_method'] == 'hold_ticket'){
				//If its Hold Ticket then dont check the agent balance
				$domain_balance_status = SUCCESS_STATUS;
			} else {
				$domain_balance_status = $this->domain_management_model->verify_current_balance ( $agent_paybleamount['amount'], $agent_paybleamount['currency'] );
			}
			
			
			// echo PAYU_PGI; exit;
			if ($domain_balance_status == true || $selected_pm) {
				//Save the Booking Data
			    $booking_data = $this->module_model->unserialize_temp_booking_record ( $book_id, $book_origin );        	
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
			
				$data = $this->flight_lib->save_booking($book_id, $book_params,$currency_obj, $this->current_module);
				
				// if( get_client_ip() == '27.59.143.42'){
				// 	echo "testts";exit;
				// }
				// debug($data); exit;
				switch ($post_params ['payment_method']) {
					case PAY_NOW :
						$this->load->model('transaction');
						$pg_currency_conversion_rate = $currency_obj->payment_gateway_currency_conversion_rate();
						$this->transaction->create_payment_record($book_id, $pgi_amount, $firstname, $email, $phone, $productinfo, $con_row['cf'], 0, $pg_currency_conversion_rate, $selected_pm, $booking_data['book_attributes']['payment_method']);
						
						redirect(base_url().'index.php/payment_gateway/payment/'.$book_id.'/'.$book_origin.'/'.$selected_pm);
						break;
					case PAY_AT_BANK :
						echo 'Under Construction - Remote IO Error';
						exit ();
						break;
				}
			} else {
				redirect ( base_url () . 'index.php/flight/exception?op=Amount Flight Booking&notification=insufficient_balance' );
			}
		}
		redirect ( base_url () . 'index.php/flight/exception?op=Remote IO error @ FLIGHT Booking&notification=validation' );
	}
	/**
	 * Calculates Selected Extra service Price
	 * @param unknown_type $post_params
	 */
	private function extra_service_price($post_params)
	{
		$extra_service_details = $this->flight_lib->extract_extra_service_details($post_params);
		//Bagggage Price
		$baggage_index = 0;
		$baggage_price = 0;
		while(isset($post_params["baggage_$baggage_index"]) == true){
			foreach ($post_params["baggage_$baggage_index"] as $bag_k => $bag_v){
				if(isset($extra_service_details['ExtraServiceDetails']['Baggage'][$bag_v])){
					$baggage_price += $extra_service_details['ExtraServiceDetails']['Baggage'][$bag_v]['Price'];
				}
			}
			$baggage_index++;
		}
		
		//Meal Price
		$meal_index = 0;
		$meal_price = 0;
		while(isset($post_params["meal_$meal_index"]) == true){
			foreach ($post_params["meal_$meal_index"] as $meal_k => $meal_v){
				if(isset($extra_service_details['ExtraServiceDetails']['Meals'][$meal_v])){
					$meal_price += $extra_service_details['ExtraServiceDetails']['Meals'][$meal_v]['Price'];
				}
			}
			$meal_index++;
		}
		
		//Seat Price
		$seat_index = 0;
		$seat_price = 0;
		while(isset($post_params["seat_$seat_index"]) == true){
			foreach ($post_params["seat_$seat_index"] as $seat_k => $seat_v){
				if(isset($extra_service_details['ExtraServiceDetails']['Seat'][$seat_v])){
					$seat_price += $extra_service_details['ExtraServiceDetails']['Seat'][$seat_v]['Price'];
				}
			}
			$seat_index++;
		}
		
		$extra_service_total_price = ($baggage_price+$meal_price+$seat_price);
		
		return $extra_service_total_price;
	}
	/*
		process booking in backend until show loader 
	*/
	function process_booking($book_id, $temp_book_origin, $is_paid_by_pg=0){
		
		if($book_id != '' && $temp_book_origin != '' && intval($temp_book_origin) > 0){
			//Please place the below calls depending on where you need to check and send balance alerts.
        	//$this->provab_mailer->check_for_low_balance_n_alert();
        	//$this->provab_sms->check_for_low_balance_n_alert();
			$page_data ['form_url'] = base_url () . 'index.php/flight/secure_booking';
			$page_data ['form_method'] = 'POST';
			$page_data ['form_params'] ['book_id'] = $book_id;
			$page_data ['form_params'] ['temp_book_origin'] = $temp_book_origin;
			$page_data ['form_params'] ['is_paid_by_pg'] = $is_paid_by_pg;
			$this->template->view('share/loader/booking_process_loader', $page_data);	

		}else{
			redirect(base_url().'index.php/flight/exception?op=Invalid request&notification=validation');
		}
		
	}
	/**
	Maintaining Quota Counter
	@param booking_source is booking source id
	@param airline_code is code of the airline as per airline_list table
	**/
	function update_quota_counter($booking_source, $qc_trpper, $pass_count, $is_domestic_qc)
	{
		$type = "International";
		if($is_domestic_qc)
			$type = "Domestic";
		foreach($qc_trpper AS $ac_array)
		{
			foreach($ac_array AS $airline_code)
			{
				$condition  = array("airline_code"=>$airline_code, "booking_source_id"=>$booking_source, "type"=>$type);
				$old_quota = $this->custom_db->single_table_records("quota_counter", "*", $condition);
				if($old_quota["status"]){
					$old_quota = $old_quota["data"][0];
					$data["consumed_quota"] = intval($old_quota["consumed_quota"])+$pass_count;
					//debug($old_quota["consumed_quota"]." - ".$number); exit;
					if($data["consumed_quota"]<=$old_quota["given_quota"])
						$this->custom_db->update_record("quota_counter", $data,	$condition);
				}
			}
		}
	}
	/**
	 * Balu A
	 * Do booking once payment is successfull - Payment Gateway
	 * and issue voucher
	 */
	function secure_booking()
	{
		error_reporting(E_ALL);
		$post_data = $this->input->post();
		if(valid_array($post_data) == true && isset($post_data['book_id']) == true && isset($post_data['temp_book_origin']) == true &&
			empty($post_data['book_id']) == false && intval($post_data['temp_book_origin']) > 0){
			//verify payment status and continue
			$book_id = trim($post_data['book_id']);
			$temp_book_origin = intval($post_data['temp_book_origin']);
		} else{
			redirect(base_url().'index.php/flight/exception?op=InvalidBooking&notification=invalid');
		}

		//Check whether amount is paid through PG
		$is_paid_by_pg=$post_data['is_paid_by_pg'];

		// run booking request and do booking
		$temp_booking = $this->module_model->unserialize_temp_booking_record ( $book_id, $temp_book_origin );
		
		//Delete the temp_booking record, after accessing
		//$this->module_model->delete_temp_booking_record ($book_id, $temp_book_origin);
		
		load_flight_lib ( $temp_booking ['booking_source'] );
		if (($temp_booking ['booking_source'] == PROVAB_FLIGHT_BOOKING_SOURCE) || 
			($temp_booking ['booking_source'] == STAR_BOOKING_SOURCE) || 
			($temp_booking ['booking_source'] == SPICEJET_BOOKING_SOURCE) || 
			($temp_booking ['booking_source'] == TRAVELPORT_GDS_BOOKING_SOURCE) || 
			($temp_booking ['booking_source'] == TRAVELPORT_ACH_BOOKING_SOURCE) ||
			($temp_booking ['booking_source'] == INDIGO_BOOKING_SOURCE)){
			$currency_obj = new Currency ( array (
					'module_type' => 'flight',
					'from' => admin_base_currency (),
					'to' => admin_base_currency () 
			) );
			$flight_details = $temp_booking ['book_attributes'] ['token'] ['token'];
			$flight_booking_summary = $this->flight_lib->merge_flight_segment_fare_details($flight_details);
			$fare_details = $flight_booking_summary['FareDetails'][$this->current_module.'_PriceDetails'];
			
			$total_booking_price = $fare_details['_AgentBuying'];
			$currency = $fare_details['Currency'];
		}
		// verify payment status and continue
		// Flight_Model::lock_tables();
		$admin_price_details = $flight_booking_summary["FareDetails"]["api_PriceDetails"];
		$b2b_price_details = $flight_booking_summary["FareDetails"]["b2b_PriceDetails"];
		$agent_paybleamount = $currency_obj->get_agent_paybleamount($total_booking_price);
		$agent_earning = $b2b_price_details['_AgentEarning'];
		//debug($temp_booking); exit;
		$api_amount = $fare_details['_AdminBuying']-$admin_price_details["AgentCommission"]+$admin_price_details["AgentTdsOnCommision"];
		//If its Hold Ticket then dont check the agent balance
		if(isset($temp_booking['book_attributes']['ticket_method']) == true && $temp_booking['book_attributes']['ticket_method'] == 'hold_ticket'){
			//If its Hold Ticket then dont check the agent balance
			$domain_balance_status = SUCCESS_STATUS;
		} else {
			$domain_balance_status = $this->domain_management_model->verify_current_balance ( $agent_paybleamount['amount'], $agent_paybleamount['currency'] );
		}
		
		if ($domain_balance_status || $is_paid_by_pg) {
			if ($temp_booking != false) {
				switch ($temp_booking ['booking_source']) {
					case PROVAB_FLIGHT_BOOKING_SOURCE :
					case STAR_BOOKING_SOURCE :
					case SPICEJET_BOOKING_SOURCE :
					case TRAVELPORT_GDS_BOOKING_SOURCE :
					case TRAVELPORT_ACH_BOOKING_SOURCE :
					case INDIGO_BOOKING_SOURCE : 
					//debug($temp_booking); exit;
						try {
							$booking = $this->flight_lib->process_booking ( $book_id, $temp_booking ['book_attributes'] );
						}catch (Exception $e) {
							$booking ['status'] = BOOKING_ERROR;
						}
						// Update booking based on booking status and book id
						break;
				}
				//Failed booking logs in separate file, FIXME ---------------------------
				if (in_array($booking ['status'], array(SUCCESS_STATUS, BOOKING_CONFIRMED, BOOKING_PENDING, BOOKING_FAILED, BOOKING_ERROR, BOOKING_HOLD,FAILURE_STATUS)) == true) {
					$currency_obj = new Currency ( array (
							'module_type' => 'flight',
							'from' => admin_base_currency (),
							'to' => admin_base_currency () 
					) );
					
					$booking ['data'] ['booking_params'] ['currency_obj'] = $currency_obj;
					//Update the booking Details
					$ticket_details = @$booking ['data'] ['ticket'];
					$ticket_details['master_booking_status'] = $booking ['status'];
					//Updating Booking Details
					$data = $this->flight_lib->update_booking_details( $book_id, $booking ['data'] ['booking_params'], $ticket_details, $this->current_module);		
					//BOOKING_ERROR have been removed from here.
					if (in_array($booking ['status'], array(SUCCESS_STATUS, BOOKING_CONFIRMED, BOOKING_PENDING, BOOKING_HOLD)) == true) {
							if(isset($temp_booking ['book_attributes']['token']['extra_services']) == true){
								//Get Extra Service Price
								$extra_service_price = $this->extra_service_price($temp_booking ['book_attributes']);
							 } else {
								$extra_service_price = 0;
							 }
							if($is_paid_by_pg)
							{
								$remarks = "Your ernings on flight booking credited to wallet";
								$crdit_towards = "Flight booking";
								$this->notification->credit_balance($this->entity_user_id, $book_id, $crdit_towards, $agent_earning, 0, $remarks);
							}
							if($temp_booking['booking_source'] != PROVAB_FLIGHT_BOOKING_SOURCE)
		                    {
		                    	$api_amount = 0 - $api_amount;
								$this->api_balance_manager->update_api_balance($temp_booking['booking_source'], $api_amount);
							}
						//Deducting Agent Balance and Updating Transaction Log
						$this->domain_management_model->update_transaction_details ( 'flight', $book_id, $data ['fare'], $data['admin_markup'], $data['agent_markup'], $data['convinence'], $data['discount'],$data['transaction_currency'], $data['currency_conversion_rate'], 
							$is_paid_by_pg, $extra_service_price);
						//debug($temp_booking); exit;
						//Updating quota counter
						$qc_tripper = array();
						$pass_count = 0;
						$bs_id = $temp_booking["booking_source"];
 						$token_data = $temp_booking["book_attributes"]["token"]["token"][0];
						$is_domestic_qc = $temp_booking["book_attributes"]["token"]["is_domestic"];
						$qc_pass = $token_data["PassengerFareBreakdown"];
						$adt_count = $qc_pass["ADT"]["PassengerCount"];
						$pass_count += $adt_count;
						if(isset($qc_pass["CHD"])){
							$chd_count = $qc_pass["CHD"]["PassengerCount"];
							$pass_count += $chd_count;
						}
						if(isset($qc_pass["INF"])){
							$inf_count = $qc_pass["INF"]["PassengerCount"];
							$pass_count += $inf_count;
						}
						$trippers = $temp_booking["book_attributes"]["token"]["token"];
						foreach($trippers AS $trip_key => $trip)
						{
							$temp_qc_arr=array();
							foreach($trip["SegmentDetails"][0] AS $qc_sd_k=>$qc_sd)
							{
								$temp_qc_arr[] = $qc_sd["AirlineDetails"]["AirlineCode"];
							}
							$qc_tripper[] = array_unique($temp_qc_arr);
						}
 						if (in_array($booking ['status'], array(BOOKING_CONFIRMED)) == true)
 						{
							$this->update_quota_counter($bs_id, $qc_tripper, $pass_count, $is_domestic_qc);
 						}
						//Updating quota counter #End
					}
				if ($is_paid_by_pg && in_array($booking['status'], array(BOOKING_ERROR, FAILURE_STATUS))){
					$pg_name = $temp_booking['book_attributes']['selected_pm'];
					redirect ( base_url () . 'index.php/payment_gateway/refund/'.$book_id.'/'.$pg_name);
					exit;
				}

				//save to accounting software
				if(isset($data['status']) && $data['status'] == 'BOOKING_CONFIRMED'){
					//////
					//$this->load->library('xlpro');
					//$this->xlpro->get_flight_booking_details($booking,$temp_booking);
				}

				if (in_array ( $data ['status'], array (
					'BOOKING_CONFIRMED',
					'BOOKING_PENDING',
					'BOOKING_HOLD'
					) )) {
					if($data ['status'] == 'BOOKING_HOLD' ){

						redirect(base_url () . 'index.php/flight/exception?op=booking_hold_exception&t&notification=Your booking is under process. Kindly wait for the confirmation. Please contact customer support team.');
					}
					else{
						redirect ( base_url () . 'index.php/voucher/flight/' . $book_id . '/' . $temp_booking ['booking_source'] . '/' . $data ['status'] . '/show_voucher/1/1' );
					}
						// Sms config & Checkpoint
						/* if (active_sms_checkpoint ( 'booking' )) {
						$msg = "Dear " . $data ['name'] . " Thank you for Booking your ticket with us.Ticket Details will be sent to your email id";
						$msg = urlencode ( $msg );
						$sms_status = $this->provab_sms->send_msg ( $data ['phone'], $msg );
						// return $sms_status;
						} */
						
					} else {
						// debug($booking);exit;
						redirect ( base_url () . 'index.php/flight/exception?op=booking_exception&notification=' . $booking ['message'] );
					}
							
				} else {
					redirect ( base_url () . 'index.php/flight/exception?op=booking_exception&notification=' . $booking ['message'] );
				}
			}
			// release table lock
			Flight_Model::release_locked_tables ();
		} else {
			// release table lock
			Flight_Model::release_locked_tables ();
			exit ();
		}
	
	
}
	

	/**
	 * Balu A
	 * Process booking on hold - pay at bank
	 * Issue Ticket Later
	 */
	function booking_on_hold($book_id) {

	}
	/**
	 * Balu A
	 */
	function pre_cancellation($app_reference, $booking_source)
	{
		if (empty($app_reference) == false && empty($booking_source) == false) {
			$page_data = array();
			$booking_details = $this->flight_model->get_booking_details($app_reference, $booking_source);
			if ($booking_details['status'] == SUCCESS_STATUS) {
				$void = false;
				$this->load->library('booking_data_formatter');
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_flight_booking_data($booking_details, 'b2b');
				$page_data['data'] = $assembled_booking_details['data'];
				if($booking_source == TRAVELPORT_GDS_BOOKING_SOURCE){
						$booking_date = date('Y-m-d',strtotime($booking_details['data']['booking_details'][0]['created_datetime']));
						$currrent_date = date('Y-m-d');
						if($booking_date == $currrent_date){
							$void = true;
						}	
				}
				$page_data['void'] = $void;
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
	function cancel_booking()
	{
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
				$cancellation_details = $this->flight_lib->cancel_booking($booking_details, $passenger_origin, $passenger_ticket_id,$post_data['cancel_res']);
				$phone = $GLOBALS['CI']->entity_phone;
				if($cancellation_details["status"]){
					$is_void = $post_data['is_void'];
					$this->custom_db->update_record('flight_booking_details',array('is_void' => $is_void), array('app_reference' => $app_reference));	
					$api_refund_amount = $this->cancellation_details($app_reference, $booking_source, $cancellation_details["status"], 1);
					$this->api_balance_manager->update_api_balance($booking_source, $api_refund_amount);
					//One of these sms must go to supervision
					$this->provab_sms->send_msg($this->CI->entity_domain_phone, $booking_details, "654507");//supervision
					$this->provab_sms->send_msg($phone, $booking_details, "568350");//agent
				}
				redirect('flight/cancellation_details/'.$app_reference.'/'.$booking_source.'/'.$cancellation_details['status']);
			} else {
				redirect('security/log_event?event=Invalid Details');
			}
		} else {
			redirect('security/log_event?event=Invalid Details');
		}
	}
	function cancellation_details($app_reference, $booking_source, $cancellation_status, $return_api_refund_amount = 0)
	{
		if (empty($app_reference) == false && empty($booking_source) == false) {
		$master_booking_details = $GLOBALS['CI']->flight_model->get_booking_details($app_reference, $booking_source);
		if ($master_booking_details['status'] == SUCCESS_STATUS) {
			$page_data = array();
			$this->load->library('booking_data_formatter');
			$master_booking_details = $this->booking_data_formatter->format_flight_booking_data($master_booking_details, 'b2b');
			$page_data['data'] = $master_booking_details['data'];
			$page_data['cancellation_status'] = $cancellation_status;
			$api_amount = 0;
			$trans_det = $page_data["data"]["booking_details"][0]["booking_transaction_details"];
			if($return_api_refund_amount == 1)
			{
				foreach($trans_det AS $td)
				{
					foreach ($td["booking_customer_details"] as $customer) {
						if(empty($customer["cancellation_details"]["API_RefundedAmount"]) || $customer["cancellation_details"]["API_RefundedAmount"] == NULL)
							$refund_amount = 0;
						else
							$refund_amount = $customer["cancellation_details"]["API_RefundedAmount"];

						$api_amount += $refund_amount;
					}
				}
				return $api_amount;
			}
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
	 * Displays Cancellation Ticket Details
	 */
	public function ticket_cancellation_details()
	{
		$get_data = $this->input->get();
		if(isset($get_data['app_reference']) == true && isset($get_data['booking_source']) == true && isset($get_data['status']) == true){
			$app_reference = trim($get_data['app_reference']);
			$booking_source = trim($get_data['booking_source']);
			$status = trim($get_data['status']);
			$booking_details = $this->flight_model->get_booking_details($app_reference, $booking_source, $status);
			if($booking_details['status'] == SUCCESS_STATUS){
				$this->load->library('booking_data_formatter');
				$booking_details = $this->booking_data_formatter->format_flight_booking_data($booking_details, $this->config->item('current_module'));
				$page_data = array();
				$page_data['booking_data'] = $booking_details['data'];
				$this->template->view('flight/ticket_cancellation_details', $page_data);
			} else {
				redirect(base_url());
			}
		} else {
			redirect(base_url());
		}
	}
	/**
	 * Balu A
	 * Displays Ticket cancellation Refund details
	 */
	public function cancellation_refund_details()
	{
		$get_data = $this->input->get();
		if(isset($get_data['app_reference']) == true && isset($get_data['booking_source']) == true && isset($get_data['passenger_status']) == true && $get_data['passenger_status'] == 'BOOKING_CANCELLED' && isset($get_data['passenger_origin']) == true && intval($get_data['passenger_origin']) > 0){
			$app_reference = trim($get_data['app_reference']);
			$booking_source = trim($get_data['booking_source']);
			$passenger_origin = trim($get_data['passenger_origin']);
			$passenger_status = trim($get_data['passenger_status']);
			$booking_details = $this->flight_model->get_passenger_ticket_info($app_reference, $passenger_origin, $passenger_status);
			$whole_booking_details = $this->flight_model->get_booking_details($get_data['app_reference']);
			if($booking_details['status'] == SUCCESS_STATUS){
				$page_data = array();
				$page_data['booking_data'] = $booking_details['data'];
				$page_data['whole_booking_data'] = $whole_booking_details['data'];
				$this->template->view('flight/cancellation_refund_details', $page_data);
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
		$module = META_AIRLINE_COURSE;
		$op = @$_GET ['op'];
		$notification = @$_GET ['notification'];
		// echo $notification;exit;
		if($notification == 'Booking is already done for the same criteria for PNR'){
			$message = 'Please add another criteria and try again';
		}
		else if($notification == 'SEAT NOT AVAILABLE' || $notification == 'seat no available'){
			$message = 'Please book another flight and try again';
		}
		else if($notification == 'Sell Failure'){
			$message = 'Please try again for the same criteria';
		}
		else if($notification == 'The requested class of service is sold out.'){
			$message = 'Please try another booking';
		}
		else if($notification == 'Supplier Interaction Failed while adding Pax Details. Reason: 18|Presentation|Fusion DSC found an exception !\n\tThe data does not match the maximum length: \n\tFor data element: freetext\n\tData length should be at least 1 and at most 70\n\tCurrent position in buffer'){
			$message = 'Please add more than 2 characters in the name field and try agian';
		}
		else if($notification == 'Agency do not have enough balance.'){
			$message = 'Please add balance and try again';
		}
		else if($notification == 'Invalid CommitBooking Request'){
			$message = 'Session is Expired. Please try again';
		}
		else if($notification == 'session'){
			$message = 'Session is Expired. Please try again';
		}
		else if($notification=="locked_user"){
			$message = 'Your account is locked, please contact Admin to Unlock it.';
		}
		else if($notification=="Your booking is under process. Kindly wait for the confirmation. Please contact customer support team."){
			$message = $notification;
		}
		else{
			$message = $notification .' Please try again';
		}
		// echo $message;exit;
		$exception = $this->module_model->flight_log_exception ( $module, $op, $message );
		
		$exception = urlencode(json_encode($exception));
		// debug($exception);exit;
		// set ip log session before redirection
		$this->session->set_flashdata ( array (
				'log_ip_info' => true 
		) );
		redirect ( base_url () . 'index.php/flight/event_logger/' . $exception );
	}
	function event_logger($exception = '') {
		$log_ip_info = $this->session->flashdata ( 'log_ip_info' );
		// echo $exception;exit;
		$this->template->view ( 'flight/exception', array (
				'log_ip_info' => $log_ip_info,
				'exception' => $exception 
		) );
	}
	/**
	 * Test booking for sending to PayuMoney to show the Issue
	 */
	function test_booking(){
		
		$book_id = trim('FB28-155801-537125');
		$book_origin = 665;
		redirect('transaction/payment/'.$book_id.'/'.$book_origin);
		
	}
	function test_post_data(){
		
	}
}
