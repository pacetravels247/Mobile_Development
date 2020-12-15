<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Provab
 * @subpackage Hotel
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V1
 */

class Hotel extends CI_Controller {
	private $current_module;
	public function __construct()
	{
		parent::__construct();
		//we need to activate hotel api which are active for current domain and load those libraries
		$this->index();
		$this->load->model('hotel_model');
		//$this->output->enable_profiler(TRUE);
		$this->current_module = $this->config->item('current_module');
		$this->load->library('api_balance_manager');
		$this->load->library('utility/notification', '', 'notification');
	}

	/**
	 * index page of application will be loaded here
	 */
	function index()
	{

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
			$safe_search_data = $this->hotel_model->get_safe_search_data ( $search_id );
			$day_diff = get_date_difference($safe_search_data['data']['from_date'], $new_date);
			if(valid_array($safe_search_data) == true && $safe_search_data['status'] == true) {
				$safe_search_data = $safe_search_data['data'];
				$search_params = array();
				$search_params['city'] = trim($safe_search_data['location']);
				$search_params['hotel_destination'] = '';
				$search_params['hotel_checkin'] = date('d-m-Y', strtotime($new_date));//Adding new Date
				$search_params['hotel_checkout'] = add_days_to_date($day_diff, $safe_search_data['to_date']);
				$search_params['rooms'] = intval($safe_search_data['room_count']);
				$search_params['adult'] = $safe_search_data['adult_config'];
				$search_params['child'] = $safe_search_data['child_config'];
				$search_params['childAge_1'] = $safe_search_data['child_config'];
				redirect(base_url().'index.php/general/pre_hotel_search/?'.http_build_query($search_params));
			} else {
				$this->template->view ( 'general/popup_redirect');
			}
		} else {
			$this->template->view ( 'general/popup_redirect');
		}
	}
	/**
	 *  Balu A
	 * Load Hotel Search Result
	 * @param number $search_id unique number which identifies search criteria given by user at the time of searching
	 */
	function search($search_id)
	{	
		$safe_search_data = $this->hotel_model->get_safe_search_data($search_id);
		// Get all the hotels bookings source which are active
		$active_booking_source = $this->hotel_model->active_booking_source();
		if ($safe_search_data['status'] == true and valid_array($active_booking_source) == true) {
			$safe_search_data['data']['search_id'] = abs($search_id);
			$this->template->view('hotel/search_result_page', array('hotel_search_params' => $safe_search_data['data'], 'active_booking_source' => $active_booking_source));
		} else {
			$this->template->view ( 'general/popup_redirect');
		}
	}

	/**
	 *  Balu A
	 * Load hotel details based on booking source
	 */
	function hotel_details($search_id)
	{	

		$params = $this->input->get();
		//debug($params);die();
		$safe_search_data = $this->hotel_model->get_safe_search_data($search_id);
		$safe_search_data['data']['search_id'] = abs($search_id);
		
		//$currency_obj = new Currency(array('module_type' => 'hotel', 'from' => get_application_default_currency(), 'to' => get_application_currency_preference()));
		$currency_obj = new Currency(array('module_type' => 'hotel','from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
		if (isset($params['booking_source']) == true) {

			//We will load different page for different API providers... As we have dependency on API for hotel details page
			load_hotel_lib($params['booking_source']);
			if ($params['booking_source'] == PROVAB_HOTEL_BOOKING_SOURCE && isset($params['ResultIndex']) == true
			and isset($params['op']) == true and
			$params['op'] == 'get_details' and $safe_search_data['status'] == true) {

				$params['ResultIndex']	= urldecode($params['ResultIndex']);
				$raw_hotel_details = $this->hotel_lib->get_hotel_details($params['ResultIndex']);
				//debug($raw_hotel_details);die('$raw_hotel_details - tmx');
				if ($raw_hotel_details['status']) {

					if($raw_hotel_details['data']['HotelInfoResult']['HotelDetails']['first_room_details']['Price']){
						 $HotelCode=$raw_hotel_details['data']['HotelInfoResult']['HotelDetails']['HotelCode'];                            
						//calculation Markup for first room 
						$raw_hotel_details['data']['HotelInfoResult']['HotelDetails']['first_room_details']['Price'] = $this->hotel_lib->update_booking_markup_currency($raw_hotel_details['data']['HotelInfoResult']['HotelDetails']['first_room_details']['Price'],$currency_obj,$search_id,true,true);
						 $image_mask=$this->hotel_model->add_hotel_images($search_id,$raw_hotel_details['data']['HotelInfoResult']['HotelDetails']['Images'],$HotelCode);
					}
					$this->template->view('hotel/tbo/tbo_hotel_details_page', array('currency_obj' => $currency_obj, 'hotel_details' => $raw_hotel_details['data'], 'hotel_search_params' => $safe_search_data['data'], 'active_booking_source' => $params['booking_source'], 'params' => $params));
				} else {
					redirect(base_url().'index.php/hotel/exception?op=Remote IO error @ Session Expiry&notification=session');
				}
			} else if($params['booking_source'] == REZLIVE_HOTEL && isset($params['ResultIndex']) == true and isset($params['op']) == true and
				$params['op'] == 'get_details' and $safe_search_data['status'] == true){

				$params['ResultIndex']	= urldecode($params['ResultIndex']);
				$raw_hotel_details = $this->hotel_lib->get_hotel_details($safe_search_data['data'],$params['hotel_code'],'b2b');
				//debug($raw_hotel_details);die('$raw_hotel_details - rezlive');

				if ($raw_hotel_details['status']) {

					if($raw_hotel_details['data']['HotelInfoResult']['HotelDetails']['first_room_details']['Price']){
						 $HotelCode=$raw_hotel_details['data']['HotelInfoResult']['HotelDetails']['HotelCode'];                            
						//calculation Markup for first room 
						$raw_hotel_details['data']['HotelInfoResult']['HotelDetails']['first_room_details']['Price'] = $this->hotel_lib->update_booking_markup_currency($raw_hotel_details['data']['HotelInfoResult']['HotelDetails']['first_room_details']['Price'],$currency_obj,$search_id,true,true);
						 $image_mask = $this->hotel_model->add_hotel_images($search_id,$raw_hotel_details['data']['HotelInfoResult']['HotelDetails']['Images'],$HotelCode);
					}
					//die('===');
					$this->template->view('hotel/tbo/tbo_hotel_details_page', array('currency_obj' => $currency_obj, 'hotel_details' => $raw_hotel_details['data'], 'hotel_search_params' => $safe_search_data['data'], 'active_booking_source' => $params['booking_source'], 'params' => $params));
				} else {
					redirect(base_url().'index.php/hotel/exception?op=Remote IO error @ Session Expiry&notification=session');
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
	 * Passenger Details page for final bookings
	 * Here we need to run booking based on api
	 */
	function booking($search_id)
	{
		$pre_booking_params = $this->input->post();
		// debug($pre_booking_params);die('booking');
		$safe_search_data = $this->hotel_model->get_safe_search_data($search_id);
		$safe_search_data['data']['search_id'] = abs($search_id);
		$page_data['active_payment_options'] = $this->module_model->get_active_payment_module_list();
		if (isset($pre_booking_params['booking_source']) == true) {
			//We will load different page for different API providers... As we have dependency on API for hotel details page
			$page_data['search_data'] = $safe_search_data['data'];
			load_hotel_lib($pre_booking_params['booking_source']);
			//Need to fill pax details by default if user has already logged in
			$this->load->model('user_model');
			$page_data['pax_details'] = array();
			$agent_details = $this->user_model->get_current_user_details();
			$page_data['agent_address'] = $agent_details[0]['address'];

			header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache");

			if ($pre_booking_params['booking_source'] == PROVAB_HOTEL_BOOKING_SOURCE and
			isset($pre_booking_params['op']) == true and $pre_booking_params['op'] == 'block_room' and $safe_search_data['status'] == true)
			{
				
				$pre_booking_params['token'] = unserialized_data($pre_booking_params['token'], $pre_booking_params['token_key']);
				if ($pre_booking_params['token'] != false) {

					$room_block_details = $this->hotel_lib->block_room($pre_booking_params);
					//debug($room_block_details);die('789');

					//debug($room_block_details); exit;
					if ($room_block_details['status'] == false) {
						redirect(base_url().'index.php/hotel/exception?op='.$room_block_details['data']['msg']);
					}
					
					//Converting API currency data to preferred currency
					$currency_obj = new Currency(array('module_type' => 'hotel','from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
					$room_block_details = $this->hotel_lib->roomblock_data_in_preferred_currency($room_block_details, $currency_obj,$search_id,'b2b');
					//Display
					$currency_obj = new Currency(array('module_type' => 'hotel', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
					
					$cancel_currency_obj = new Currency(array('module_type' => 'hotel','from' => get_api_data_currency(), 'to' => get_application_currency_preference()));

					$pre_booking_params = $this->hotel_lib->update_block_details($room_block_details['data']['response']['BlockRoomResult'], $pre_booking_params,$cancel_currency_obj);

					/*
					 * Update Markup
					 */
					$pre_booking_params['markup_price_summary'] = $this->hotel_lib->update_booking_markup_currency($pre_booking_params['price_summary'], $currency_obj, $safe_search_data['data']['search_id'], true, true);
					$phone_code_record = $this->custom_db->single_table_records('user', '*');

					if ($room_block_details['status'] == SUCCESS_STATUS) {
						if(!empty($this->entity_country_code)){
							$page_data['user_country_code'] = $this->entity_country_code;
						}
						else{
							//$page_data['user_country_code'] = '';	
							$page_data['user_country_code'] = $phone_code_record['data'][0]['country_code'];
						}
						//debug($page_data['user_country_code']);exit;
						$page_data['booking_source'] = $pre_booking_params['booking_source'];
						$page_data['pre_booking_params'] = $pre_booking_params;
						$page_data['pre_booking_params']['default_currency'] = get_application_default_currency();
						$page_data['iso_country_list']	= $this->db_cache_api->get_iso_country_list();
						$page_data['country_list']		= $this->db_cache_api->get_country_list();
						$page_data['currency_obj']		= $currency_obj;
						$page_data['total_price']		= $this->hotel_lib->total_price($pre_booking_params['markup_price_summary']);
						$page_data['convenience_fees']  = ceil($currency_obj->convenience_fees($page_data['total_price'], $page_data['search_data']['search_id']));
						$page_data['tax_service_sum']	=  $this->hotel_lib->tax_service_sum($pre_booking_params['markup_price_summary'], $pre_booking_params['price_summary']);
						//debug($page_data);exit;
						$Domain_record = $this->custom_db->single_table_records('domain_list', '*');
					$page_data['active_data'] =$Domain_record['data'][0];
					$temp_record = $this->custom_db->single_table_records('api_country_list', '*');
					//debug($temp_record);exit;
					$page_data['phone_code'] =$temp_record['data'];
						$this->template->view('hotel/tbo/tbo_booking_page', $page_data);
					}
				}
			}else if($pre_booking_params['booking_source'] == REZLIVE_HOTEL){

				$pre_booking_params['token'] = unserialized_data($pre_booking_params['token'], $pre_booking_params['token_key']);

				if ($pre_booking_params['token'] != false) {

					$room_configuration = array();
					$room_configuration['rooms'] = $pre_booking_params['rooms'];
					$room_configuration['adults'] = $pre_booking_params['adults'];
					$room_configuration['childs'] = $pre_booking_params['childs'];

					$room_block_details = $this->hotel_lib->pre_booking_check($safe_search_data,$pre_booking_params,$safe_search_data);
					//debug($room_block_details);die('rezlive-prebook');

					//debug($room_block_details); exit("999");
					if ($room_block_details['status'] == false) {
						redirect(base_url().'index.php/hotel/exception?op='.$room_block_details['data']['msg']);
					}

					//format to tmx response
					$room_block_details = $this->hotel_lib->format_room_block_details($room_block_details);

					// debug($room_block_details);die('0000000000000');

					
					//Converting API currency data to preferred currency
					$currency_obj = new Currency(array('module_type' => 'hotel','from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
					$room_block_details = $this->hotel_lib->roomblock_data_in_preferred_currency($room_block_details, $currency_obj,$search_id,'b2b');
					//Display
					//debug($room_block_details);exit('8');
					$currency_obj = new Currency(array('module_type' => 'hotel', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
					
					$cancel_currency_obj = new Currency(array('module_type' => 'hotel','from' => get_api_data_currency(), 'to' => get_application_currency_preference()));

					$pre_booking_params = $this->hotel_lib->update_block_details($room_block_details['data']['response']['BlockRoomResult'], $pre_booking_params,$cancel_currency_obj);
					//debug($pre_booking_params);die('265');
					/*
					 * Update Markup
					 */
					//$pre_booking_params['extra_data'] = $room_block_details[]
					$pre_booking_params['markup_price_summary'] = $this->hotel_lib->update_booking_markup_currency($pre_booking_params['price_summary'], $currency_obj, $safe_search_data['data']['search_id'], true, true);
					$phone_code_record = $this->custom_db->single_table_records('user', '*');
					//debug($pre_booking_params);exit;
					if ($room_block_details['status'] == SUCCESS_STATUS) {
						if(!empty($this->entity_country_code)){
							$page_data['user_country_code'] = $this->entity_country_code;
						}
						else{
							//$page_data['user_country_code'] = '';	
							$page_data['user_country_code'] = $phone_code_record['data'][0]['country_code'];
						}
						//debug($page_data['user_country_code']);exit;
						$page_data['booking_source'] = $pre_booking_params['booking_source'];
						$page_data['pre_booking_params'] = $pre_booking_params;
						//debug($pre_booking_params);exit;
						$page_data['pre_booking_params']['default_currency'] = get_application_default_currency();
						$page_data['iso_country_list']	= $this->db_cache_api->get_iso_country_list();
						$page_data['country_list']		= $this->db_cache_api->get_country_list();
						$page_data['currency_obj']		= $currency_obj;
						$page_data['total_price']		= $this->hotel_lib->total_price($pre_booking_params['markup_price_summary']);
						$page_data['convenience_fees']  = ceil($currency_obj->convenience_fees($page_data['total_price'], $page_data['search_data']['search_id']));
						$page_data['tax_service_sum']	=  $this->hotel_lib->tax_service_sum($pre_booking_params['markup_price_summary'], $pre_booking_params['price_summary']);
						//debug($page_data);exit;
						$Domain_record = $this->custom_db->single_table_records('domain_list', '*');
					$page_data['active_data'] =$Domain_record['data'][0];
					$temp_record = $this->custom_db->single_table_records('api_country_list', '*');
					//debug($page_data);exit;
					$page_data['markup_limits'] = $this->db->where(array('module_type' =>'hotel'))->get('agent_markup_limit')->result_array();
					$page_data['phone_code'] =$temp_record['data'];
						$this->template->view('hotel/tbo/tbo_booking_page', $page_data);
					}
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
	 * Secure Booking of hotel
	 * 255 single adult static booking request 2310
	 * 261 double room static booking request 2308
	 */
	function pre_booking($search_id=2310, $static_search_result_id=255)
	{
		// redirect(base_url().'index.php/general/booking_not_allowed');		
		// exit;
		$post_params = $this->input->post();
		if($this->entity_status==LOCK && ($post_params ['selected_pm']=="WALLET"))
		{
			redirect(base_url().'index.php/flight/exception?op=locked_user&notification=locked_user');
			exit;
		}

		$post_params['billing_city'] = 'Bangalore';
		$post_params['billing_zipcode'] = '560100';
		$selected_pm=$post_params ['selected_pm'];
		//$this->custom_db->generate_static_response(json_encode($post_params));
		//Insert To temp_booking and proceed
		/*$post_params = $this->hotel_model->get_static_response($static_search_result_id);*/

		//Make sure token and temp token matches
		$valid_temp_token = unserialized_data($post_params['token'], $post_params['token_key']);
		//debug($valid_temp_token);die('56');
		if ($valid_temp_token != false) {
			load_hotel_lib($post_params['booking_source']);
			/****Convert Display currency to Application default currency***/
			//After converting to default currency, storing in temp_booking
			$post_params['token'] = unserialized_data($post_params['token']);
			$post_params['token']['selected_pm'] = $selected_pm;
			$currency_obj = new Currency ( array (
						'module_type' => 'hotel',
						'from' => get_application_currency_preference (),
						'to' => admin_base_currency () 
				));
			
			$post_params['token'] = $this->hotel_lib->convert_token_to_application_currency($post_params['token'], $currency_obj, $this->current_module);
			$post_params['token'] = serialized_data($post_params['token']);
			$temp_token = unserialized_data($post_params['token']);

			//Insert To temp_booking and proceed
			$temp_booking = $this->module_model->serialize_temp_booking_record($post_params, HOTEL_BOOKING);
			//debug($temp_token); exit;
			//debug($temp_booking);die('45');
			$book_id = $temp_booking['book_id'];
			$book_origin = $temp_booking['temp_booking_origin'];
			
			if ($post_params['booking_source'] == PROVAB_HOTEL_BOOKING_SOURCE || $post_params['booking_source'] == REZLIVE_HOTEL) {
				$amount	  = $this->hotel_lib->total_price($temp_token['markup_price_summary']);
				//debug($amount);exit;
				$currency = $temp_token['default_currency'];
			}
			$currency_obj = new Currency ( array (
						'module_type' => 'hotel',
						'from' => admin_base_currency (),
						'to' => admin_base_currency () 
			) );
			/********* Convinence Fees End ********/
			 	
			/********* Promocode Start ********/
			$promocode_discount = 0;
			/********* Promocode End ********/

			//details for PGI
			$email = $post_params ['billing_email'];
			$phone = $post_params ['passenger_contact'];
			$verification_amount = ceil($amount+$convenience_fees-$promocode_discount);
			$firstname = $post_params ['first_name'] ['0'];
			$productinfo = META_ACCOMODATION_COURSE;
			//check current balance before proceeding further
			$agent_paybleamount = $currency_obj->get_agent_paybleamount($verification_amount);
			//debug($agent_paybleamount);die('56');
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
            $con_row = $this->master_currency->get_instant_recharge_convenience_fees($amount, $method, $bank_code);
			if ($domain_balance_status == true || $selected_pm) {
				switch($post_params['payment_method']) {
					case PAY_NOW :
						$this->load->model('transaction');
						$pg_currency_conversion_rate = $currency_obj->payment_gateway_currency_conversion_rate();
						$this->transaction->create_payment_record($book_id, $amount, $firstname, $email, $phone, $productinfo, $con_row['cf'], $promocode_discount, $pg_currency_conversion_rate, $selected_pm, $payment_mode);
						//redirect(base_url().'index.php/payment_gateway/payment/'.$book_id.'/'.$book_origin);
						redirect(base_url().'index.php/payment_gateway/payment/'.$book_id.'/'.$book_origin.'/'.$selected_pm);
						break;
					case PAY_AT_BANK : echo 'Under Construction - Remote IO Error';exit;
					break;
				}
			} else {
				redirect(base_url().'index.php/hotel/exception?op=Amount Hotel Booking&notification=insufficient_balance');
			}
		} else {
			redirect(base_url().'index.php/hotel/exception?op=Remote IO error @ Hotel Booking&notification=validation');
		}
	}

	/*
		process booking in backend until show loader 
	*/
	function process_booking($book_id, $temp_book_origin, $is_paid_by_pg=0){
		
		if($book_id != '' && $temp_book_origin != '' && intval($temp_book_origin) > 0){

			$page_data ['form_url'] = base_url () . 'index.php/hotel/secure_booking';
			$page_data ['form_method'] = 'POST';
			$page_data ['form_params'] ['book_id'] = $book_id;
			$page_data ['form_params'] ['temp_book_origin'] = $temp_book_origin;
			$page_data ['form_params'] ['is_paid_by_pg'] = $is_paid_by_pg;
			$this->template->view('share/loader/booking_process_loader', $page_data);	

		}else{
			redirect(base_url().'index.php/hotel/exception?op=Invalid request&notification=validation');
		}
		
	}

	/**
	 *  Balu A
	 *Do booking once payment is successfull - Payment Gateway
	 *and issue voucher
	 *HB11-152109-443266/1
	 *HB11-154107-854480/2
	 */
	function secure_booking()
	{	
		
		$post_data = $this->input->post();
		//debug($post_data);die('22');
		if(valid_array($post_data) == true && isset($post_data['book_id']) == true && isset($post_data['temp_book_origin']) == true &&
			empty($post_data['book_id']) == false && intval($post_data['temp_book_origin']) > 0){
			//verify payment status and continue
			$book_id = trim($post_data['book_id']);
			$temp_book_origin = intval($post_data['temp_book_origin']);
		} else{
			redirect(base_url().'index.php/hotel/exception?op=InvalidBooking&notification=invalid');
		}
		
		//Check whether amount is paid through PG
		$is_paid_by_pg=$post_data['is_paid_by_pg'];
		//run booking request and do booking
		$temp_booking = $this->module_model->unserialize_temp_booking_record($book_id, $temp_book_origin);

		//debug($temp_booking);die('222');
		//Delete the temp_booking record, after accessing
		//$this->module_model->delete_temp_booking_record ($book_id, $temp_book_origin);
		
		load_hotel_lib($temp_booking['booking_source']);
		//verify payment status and continue
		$total_booking_price = $this->hotel_lib->total_price($temp_booking['book_attributes']['token']['markup_price_summary']);
		$api_amount = $temp_booking['book_attributes']['token']['price_summary']['PublishedPrice'];

		$currency = $temp_booking['book_attributes']['token']['default_currency'];
		$currency_obj = new Currency(array('module_type' => 'hotel', 'from' => admin_base_currency(), 'to' => admin_base_currency()));
		//also verify provab balance
		//check current balance before proceeding further
		$agent_paybleamount = $currency_obj->get_agent_paybleamount($total_booking_price);
		$domain_balance_status = $this->domain_management_model->verify_current_balance($agent_paybleamount['amount'], $agent_paybleamount['currency']);
		//debug($temp_booking);exit('12');
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
		if ($domain_balance_status || $is_paid_by_pg) {
			//lock table
		
			if ($temp_booking != false) {
				switch ($temp_booking['booking_source']) {
					case PROVAB_HOTEL_BOOKING_SOURCE :
					case REZLIVE_HOTEL :
						//FIXME : COntinue from here - Booking request
						$booking = $this->hotel_lib->process_booking($book_id, $temp_booking['book_attributes']);
						//Save booking based on booking status and book id
						break;
				}
				//debug($booking);die('78');
				if ($booking['status'] == SUCCESS_STATUS) {
					$booking['data']['currency_obj'] = $currency_obj;
					//debug($booking['data']); exit;
					//Save booking based on booking status and book id					
                	$api_amount = 0 - $api_amount;
					$this->api_balance_manager->update_api_balance($temp_booking['booking_source'], $api_amount);

					$data = $this->hotel_lib->save_booking($book_id, $booking['data'], $this->current_module);
					if($is_paid_by_pg)
					{
						$agent_earning = $data["agent_markup"];
						$remarks = "Your ernings on Hotel booking credited to wallet";
						$crdit_towards = "Hotel booking";
						$this->notification->credit_balance($this->entity_user_id, $book_id, $crdit_towards, $agent_earning, 0, $remarks);
					}
					
					$this->domain_management_model->update_transaction_details('hotel', $book_id, $data['fare'], $data['admin_markup'], $data['agent_markup'], $data['convinence'], $data['discount'],$data['transaction_currency'], $data['currency_conversion_rate'],
						$is_paid_by_pg);
					//deduct balance and continue

					//save to accounting software
                    /*$this->load->library('xlpro');
                    $this->xlpro->get_hotel_booking_details($booking,$temp_booking);*/

					redirect(base_url().'index.php/voucher/hotel/'.$book_id.'/'.$temp_booking['booking_source'].'/'.$data['booking_status'].'/show_voucher/0/1');
				}
				if ($is_paid_by_pg && in_array($booking['status'], array(BOOKING_ERROR, FAILURE_STATUS))){
					$pg_name = $temp_booking['book_attributes']['selected_pm'];
					redirect ( base_url () . 'index.php/payment_gateway/refund/'.$book_id.'/'.$pg_name);
					//exit;
				}
				else {
					redirect(base_url().'index.php/hotel/exception?op=booking_exception&notification='.$booking['msg']);
				}
			}
			//release table lock
		} else {
			redirect(base_url().'index.php/hotel/exception?op=Remote IO error @ Insufficient&notification=validation');
		}
	}

	function test(){
		$currency_obj = new Currency(array('module_type' => 'hotel', 'from' => get_application_default_currency(), 'to' => get_application_default_currency()));
		debug($currency_obj);
	}

	/**
	 *  Balu A
	 *Process booking on hold - pay at bank
	 */
	function booking_on_hold($book_id)
	{

	}
	/**
	 * Balu A
	 */
	function pre_cancellation($app_reference, $booking_source)
	{
		if (empty($app_reference) == false && empty($booking_source) == false) {
			$page_data = array();
			$booking_details = $this->hotel_model->get_booking_details($app_reference, $booking_source);
			if ($booking_details['status'] == SUCCESS_STATUS) {
				$this->load->library('booking_data_formatter');
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_hotel_booking_data($booking_details, 'b2b');
				$page_data['data'] = $assembled_booking_details['data'];
				$this->template->view('hotel/pre_cancellation', $page_data);
			} else {
				redirect('security/log_event?event=Invalid Details');
			}
		} else {
			redirect('security/log_event?event=Invalid Details');
		}
	}
	/*
	 * Balu A
	 * Process the Booking Cancellation
	 * Full Booking Cancellation
	 *
	 */
	function cancel_booking($app_reference, $booking_source)
	{
		if(empty($app_reference) == false) {
			$master_booking_details = $this->hotel_model->get_booking_details($app_reference, $booking_source);
			if ($master_booking_details['status'] == SUCCESS_STATUS) {
				$this->load->library('booking_data_formatter');
				$master_booking_details = $this->booking_data_formatter->format_hotel_booking_data($master_booking_details, 'b2b');
				$master_booking_details = $master_booking_details['data']['booking_details'][0];
				load_hotel_lib($booking_source);
				$cancellation_details = $this->hotel_lib->cancel_booking($master_booking_details);//Invoke Cancellation Methods
				if($cancellation_details['status'] == false) {
					$query_string = '?error_msg='.$cancellation_details['msg'];
				} else {
					$api_refund_amount = $this->cancellation_details($app_reference, $booking_source, 1);
					if(empty($api_refund_amount) || $api_refund_amount == NULL)
						$api_refund_amount = 0;
					$this->api_balance_manager->update_api_balance($booking_source, $api_refund_amount);
					$query_string = '';
				}
				redirect('hotel/cancellation_details/'.$app_reference.'/'.$booking_source.$query_string);
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
	function cancellation_details($app_reference, $booking_source, $return_api_refund_amount=0)
	{
		if (empty($app_reference) == false && empty($booking_source) == false) {
			$master_booking_details = $GLOBALS['CI']->hotel_model->get_booking_details($app_reference, $booking_source);
			if ($master_booking_details['status'] == SUCCESS_STATUS) {
				$page_data = array();
				$this->load->library('booking_data_formatter');
				$master_booking_details = $this->booking_data_formatter->format_hotel_booking_data($master_booking_details, 'b2b');
				$page_data['data'] = $master_booking_details['data'];

				$api_amount = $page_data["data"]["booking_details"][0]["cancellation_details"]["API_RefundedAmount"];
				if($return_api_refund_amount == 1)
					return $api_amount;
				
				$this->template->view('hotel/cancellation_details', $page_data);
			} else {
				redirect('security/log_event?event=Invalid Details');
			}
		} else {
			redirect('security/log_event?event=Invalid Details');
		}

	}
	function map()
	{
		$details = $this->input->get();
		$geo_codes['data']['latitude'] = $details['lat'];
		$geo_codes['data']['longtitude'] = $details['lon'];
		$geo_codes['data']['hotel_name'] = urldecode($details['hn']);
		$geo_codes['data']['star_rating'] = $details['sr'];
		$geo_codes['data']['city'] = urldecode($details['c']);
		$geo_codes['data']['hotel_image'] = urldecode($details['img']);
		echo $this->template->isolated_view('hotel/location_map', $geo_codes);
	}
	/**
	 * Balu A
	 * Displays Cancellation Refund Details
	 * @param unknown_type $app_reference
	 * @param unknown_type $status
	 */
	public function cancellation_refund_details()
	{
		$get_data = $this->input->get();
		if(isset($get_data['app_reference']) == true && isset($get_data['booking_source']) == true && isset($get_data['status']) == true && $get_data['status'] == 'BOOKING_CANCELLED'){
			$app_reference = trim($get_data['app_reference']);
			$booking_source = trim($get_data['booking_source']);
			$status = trim($get_data['status']);
			$booking_details = $this->hotel_model->get_booking_details($app_reference, $booking_source, $status);
			if($booking_details['status'] == SUCCESS_STATUS){
				$page_data = array();
				$page_data['booking_data'] = 		$booking_details['data'];
				$this->template->view('hotel/cancellation_refund_details', $page_data);
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
	function exception()
	{
		$module = META_ACCOMODATION_COURSE;
		$op = (empty($_GET['op']) == true ? '' : $_GET['op']);
		$notification = (empty($_GET['notification']) == true ? '' : $_GET['notification']);
		
		if($op == 'Some Problem Occured. Please Search Again to continue'){
			$op = 'Some Problem Occured. ';
		}
		if($notification == 'Invalid CommitBooking Request'){
			$message = 'Session is Expired';
		}
		else if($notification == 'Some Problem Occured. Please Search Again to continue' ){
			$message = 'Some Problem Occured';
		}
		else if($notification == 'locked_msg' ){
			$message = 'Your Account have been locked. Please contact admin to Unlock it.';
		}
		else{
			$message = $notification;
		}
		$exception = $this->module_model->flight_log_exception($module, $op, $message);
		$exception = base64_encode(json_encode($exception));
		// debug($exception);exit;
		//set ip log session before redirection
		$this->session->set_flashdata(array('log_ip_info' => true));
		$is_session = false;
		
		if($notification=='session'){
			$is_session =true;
		}
		
		redirect(base_url().'index.php/hotel/event_logger/'.$exception.'/'.$is_session.'/'.$op);
	}

	function event_logger($exception='',$is_session='',$op='')
	{
		
		$log_ip_info = $this->session->flashdata('log_ip_info');
		if(strtolower(urldecode($op))=='not available'){
			$op='';
		}
		$this->template->view('hotel/exception', array('log_ip_info' => $log_ip_info, 'exception' => $exception,'is_session'=>$is_session ,'message'=>$op));
	}

	//update country name in api_hotel_master table
	function update_country_name(){
		ini_set('memory_limit',-1);
		ini_set('max_execution_time', 0);
		// $destinatio_code = $this->db->query('select * from api_city_master where country_code like "%D!%"')->result_array();
		// //error_reporting(E_ALL);
		// foreach ($destinatio_code as $key => $value) {
			
		// 	$update_arr = [];
		// 	$update_arr['city_name'] = $value['city_name'].' '.$value['destination_code'];
		// 	$update_arr['destination_code'] = $value['country_code'];
		// 	$country_code = $this->custom_db->single_table_records('api_destination_master','*',array('destination_code'=>$value['country_code']));
		// 	if($country_code['status']==1){
		// 		$update_arr['country_code'] = $country_code['data'][0]['country'];
		// 	}
		// 	$condition = array('origin'=>$value['origin']);
		// 	$this->db->update('api_city_master', $update_arr, $condition);
			
		// 	//$update = $this->custom_db->update_record('api_city_master',$update_arr,array('country_code'=>$value['origin']));
			
			
		// }
		// echo "success";exit;
		$select_country = $this->custom_db->single_table_records('api_country_master','*',array());
		
		ini_set('memory_limit',-1);
		ini_set('max_execution_time', 0);
		foreach ($select_country['data'] as $key => $value) {
			$select_city_country = $this->custom_db->single_table_records('api_city_master','*',array('country_code'=>$value['iso_country_code']));
			$update_record['country_name'] = $value['country_name'];
			$this->custom_db->update_record('api_city_master',$update_record,array('country_code'=>$value['iso_country_code']));
			
		}
		 echo "success";
		 exit;
	}
	/**
	*Get Hotel HOLD Booking status (GRN)
	*/
	function get_pending_booking_status($app_reference,$booking_source,$status){
		$status = 0;	
		if($status=='BOOKING_HOLD'){
			$booking_source = $booking_source;
			$app_reference = $app_reference;
			$status = $status;
			$booking_details = $this->hotel_model->get_booking_details($app_reference, $booking_source, $status);
			if($booking_details['status']==1){
				$booking_reference = $booking_details['data']['booking_details'][0]['booking_reference'];
				
				load_hotel_lib($booking_source);
				$hold_booking_status = $this->hotel_lib->get_hotel_booking_status($app_reference);
				if($hold_booking_status['status']==true){
					$status = 1;
				}
			}
		}	
		echo  $status;
	}
	    function image_cdn($index,$search_id,$HotelCode)
	{
            $HotelCode= base64_decode($HotelCode);
         $image_url= $this->custom_db->single_table_records('hotel_image_url','image_url',array('search_id'=>$search_id,'ResultIndex'=>$index,'hotel_code'=>$HotelCode));
         //debug($image_url);exit;
         $image_url=$image_url['data'][0]['image_url'];
         
         header("Content-type: image/gif");
          echo  file_get_contents($image_url);
	}
    function image_details_cdn($HotelCode,$images_index)
	{
         $HotelCode= base64_decode($HotelCode);
         $image_url= $this->custom_db->single_table_records('hotel_image_url','image_url',array('hotel_code'=>$HotelCode,'ResultIndex'=>$images_index));
         $image_url=$image_url['data'][0]['image_url'];
         header("Content-type: image/gif");
         echo  file_get_contents($image_url);
	}

	/*function image_details_cdn($HotelCode,$images_index)
	{
		
         $HotelCode= base64_decode($HotelCode);
         $image_url= $this->custom_db->single_table_records('rz_hotel_images','image_url',array('hotel_code'=>$HotelCode));
         $image_url=$image_url['data'][0]['image_url'];
         header("Content-type: image/gif");
         echo  file_get_contents($image_url);
	}*/
function get_hotel_data(){
		
		$params = $this->input->post();
		//debug($params);
		$search_id=$params['search_id'];
		$safe_search_data = $this->hotel_model->get_safe_search_data($search_id);
		$safe_search_data['data']['search_id'] = abs($search_id);
		
		//$currency_obj = new Currency(array('module_type' => 'hotel', 'from' => get_application_default_currency(), 'to' => get_application_currency_preference()));
		$currency_obj = new Currency(array('module_type' => 'hotel','from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
		if (isset($params['booking_source']) == true) {

			//We will load different page for different API providers... As we have dependency on API for hotel details page
			load_hotel_lib($params['booking_source']);
			//debug($safe_search_data);die('$raw_hotel_details - rezlive');
			if ($params['booking_source'] == PROVAB_HOTEL_BOOKING_SOURCE && isset($params['ResultIndex']) == true
			and isset($params['op']) == true and
			$params['op'] == 'get_details' and $safe_search_data['status'] == true) {

				$params['ResultIndex']	= urldecode($params['ResultIndex']);
				$raw_hotel_details = $this->hotel_lib->get_hotel_details($params['ResultIndex']);
				//debug($raw_hotel_details);die('$raw_hotel_details - tmx');
				if ($raw_hotel_details['status']) {

					if($raw_hotel_details['data']['HotelInfoResult']['HotelDetails']['first_room_details']['Price']){
						 $HotelCode=$raw_hotel_details['data']['HotelInfoResult']['HotelDetails']['HotelCode'];                            
						//calculation Markup for first room 
						$raw_hotel_details['data']['HotelInfoResult']['HotelDetails']['first_room_details']['Price'] = $this->hotel_lib->update_booking_markup_currency($raw_hotel_details['data']['HotelInfoResult']['HotelDetails']['first_room_details']['Price'],$currency_obj,$search_id,true,true);
						 $image_mask=$this->hotel_model->add_hotel_images($search_id,$raw_hotel_details['data']['HotelInfoResult']['HotelDetails']['Images'],$HotelCode);
					}
					$this->template->view('hotel/tbo/tbo_hotel_details_page', array('currency_obj' => $currency_obj, 'hotel_details' => $raw_hotel_details['data'], 'hotel_search_params' => $safe_search_data['data'], 'active_booking_source' => $params['booking_source'], 'params' => $params));
				} else {
					redirect(base_url().'index.php/hotel/exception?op=Remote IO error @ Session Expiry&notification=session');
				}
			} else if($params['booking_source'] == REZLIVE_HOTEL && isset($params['ResultIndex']) == true and isset($params['op']) == true and
				$params['op'] == 'get_details' and $safe_search_data['status'] == true){
				
				
				$params['ResultIndex']	= urldecode($params['ResultIndex']);
				$raw_hotel_details = $this->hotel_lib->get_hotel_details($safe_search_data['data'],$params['hotel_code'],'b2b');

				if ($raw_hotel_details['status']) {

					if($raw_hotel_details['data']['HotelInfoResult']['HotelDetails']['first_room_details']['Price']){
						 $HotelCode=$raw_hotel_details['data']['HotelInfoResult']['HotelDetails']['HotelCode'];                            
						//calculation Markup for first room 
						$raw_hotel_details['data']['HotelInfoResult']['HotelDetails']['first_room_details']['Price'] = $this->hotel_lib->update_booking_markup_currency($raw_hotel_details['data']['HotelInfoResult']['HotelDetails']['first_room_details']['Price'],$currency_obj,$search_id,true,true);
						 $image_mask = $this->hotel_model->add_hotel_images($search_id,$raw_hotel_details['data']['HotelInfoResult']['HotelDetails']['Images'],$HotelCode);
					}
					//die('===');
					//$this->template->view('hotel/tbo/tbo_hotel_details_page', array('currency_obj' => $currency_obj, 'hotel_details' => $raw_hotel_details['data'], 'hotel_search_params' => $safe_search_data['data'], 'active_booking_source' => $params['booking_source'], 'params' => $params));
					echo $this->template->isolated_view('hotel/tbo/tbo_hotel_details_page_new', array('currency_obj' => $currency_obj, 'hotel_details' => $raw_hotel_details['data'], 'hotel_search_params' => $safe_search_data['data'], 'active_booking_source' => $params['booking_source'], 'params' => $params));
				} else {
					redirect(base_url().'index.php/hotel/exception?op=Remote IO error @ Session Expiry&notification=session');
				}

			} else {
				//debug($params);die('$raw_hotel_details - rezlive');
				redirect(base_url());
			}
		} else {
			redirect(base_url());
		}
	}
}
