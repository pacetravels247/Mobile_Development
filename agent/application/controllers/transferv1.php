<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * @package    Provab
 * @subpackage Transfers
 * @author     Elavarasi<elavarasi.k@provabmial.com>
 * @version    V1
 */
//error_reporting(E_ALL);
class Transferv1 extends CI_Controller {
	private $current_module;
	public function __construct()
	{
		parent::__construct();
		//we need to activate transfer api which are active for current domain and load those libraries
		$this->load->model('transferv1_model');
		$this->load->library('social_network/facebook');//Facebook Library to enable login button		
		//$this->output->enable_profiler(TRUE);
		$this->current_module = $this->config->item('current_module');
		$this->load->library('api_balance_manager');
	}

	/**
	 * index page of application will be loaded here
	 */
	function index()
	{
		//	echo number_format(0, 2, '.', '');
	}

	/**
	 *  Elavarasi
	 * Load Transfers Search Result
	 * @param number $search_id unique number which identifies search criteria given by user at the time of searching
	 */
	function search($search_id)
	{	
		$safe_search_data = $this->transferv1_model->get_safe_search_data($search_id,META_TRANSFERV1_COURSE);
		
		// Get all the hotels bookings source which are active
		$active_booking_source = $this->transferv1_model->active_booking_source();
		
		if ($safe_search_data['status'] == true and valid_array($active_booking_source) == true) {
			$safe_search_data['data']['search_id'] = abs($search_id);
			$this->template->view('transferv1/search_result_page', array('sight_seen_search_params' => $safe_search_data['data'], 'active_booking_source' => $active_booking_source));
		} else {
			$this->template->view ( 'general/popup_redirect');
		}
	}

	/**
	 *  Elavarasi
	 * Get Product Details
	 */
	function transfer_details()
	{
		$params = $this->input->get();
		$safe_search_data = $this->transferv1_model->get_safe_search_data($params['search_id'],META_TRANSFERV1_COURSE);

		$safe_search_data['data']['search_id'] = abs($params['search_id']);
		$currency_obj = new Currency(array('module_type' => 'transferv1','from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
		

		$search_id = $params['search_id'];
		if (isset($params['booking_source']) == true) {
			//We will load different page for different API providers... As we have dependency on API for hotel details page
			load_transferv1_lib($params['booking_source']);
			if ($params['booking_source'] == PROVAB_TRANSFERV1_BOOKING_SOURCE && isset($params['result_token']) == true and isset($params['op']) == true and
			$params['op'] == 'get_details' and $safe_search_data['status'] == true) {

				$params['result_token']	= urldecode($params['result_token']);
				$raw_product_deails = $this->transferv1_lib->get_product_details($params);				
				// debug($raw_product_deails);
				// exit;
				if ($raw_product_deails['status']) {
					 $calendar_availability_date = $this->enable_calendar_availability($raw_product_deails['data']['ProductDetails']['TransferInfoResult']['Product_available_date']);
					
					$raw_product_deails['data']['ProductDetails']['TransferInfoResult']['Calendar_available_date'] = $calendar_availability_date;

					if($raw_product_deails['data']['ProductDetails']['TransferInfoResult']['Price']){

						//details data in preffered currency
						
						#debug($raw_product_deails['data']['ProductDetails']['SSInfoResult']['Price']);

						$Price = $this->transferv1_lib->details_data_in_preffered_currency($raw_product_deails['data']['ProductDetails']['TransferInfoResult']['Price'],$currency_obj,'b2b');
						
						$currency_obj = new Currency(array('module_type' => 'transferv1','from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
						//debug($Price);

						$this->transferv1_lib->get_commission($Price ,$currency_obj);
						//debug($Price);
						//calculation Markup 
						//need to calculate//admin
						$admin_price_details  = $this->transferv1_lib->update_booking_markup_currency($Price,$currency_obj,$search_id,true,false,'b2b');
						//echo "amidn";
						//debug($admin_price_details);
						
						$agent_price_details = 	$this->transferv1_lib->update_booking_markup_currency($Price,$currency_obj,$search_id,true,true,'b2b');
						//echo "agent";
						// debug($Price);exit;

						$b2b_price_details = $this->transferv1_lib->b2b_price_details($Price,$admin_price_details,$agent_price_details,$currency_obj);
						
						//debug($b2b_price_details);

						$raw_product_deails['data']['ProductDetails']['TransferInfoResult']['Price'] = $b2b_price_details;
						//debug($raw_product_deails['data']['ProductDetails']['SSInfoResult']['Price']);
						//exit;
					}
					
					$this->template->view('transferv1/viator/sightseeing_details', array('currency_obj' => $currency_obj, 'product_details' => $raw_product_deails['data']['ProductDetails']['TransferInfoResult'], 'search_params' => $safe_search_data['data'], 'search_id'=>$search_id,'active_booking_source' => $params['booking_source'], 'params' => $params));
				} else {
					
					$msg= $raw_product_deails['Message'];

					redirect(base_url().'index.php/transferv1/exception?op='.$msg.'&notification=session');
				}
			} else {
				redirect(base_url());
			}
		} else {
			redirect(base_url());
		}
	}
	/**
	*Elavarasi
	*Enable Particular day only in calendar
	*/
	public function enable_calendar_availability($calendar_availability_date){
		
		if(valid_array($calendar_availability_date)){
			$available_str=array();
			 foreach ($calendar_availability_date as $m_key => $m_value) {
			 	$avail_month_str = $m_key;
			 	
			 	foreach ($m_value as $d_key => $d_value) {
			 		//j- to remove 0 from date, n to remove 0 from month
			 		$date_str = $avail_month_str.'-'.$d_value;
			 		$available_str[] = date('j-n-Y',strtotime($date_str));
			 		
			 	}
			 }
			
			return $available_str;
		}else{
			return '';
		}
	}
	/**
	*Elavarasi
	*Getting Available Tourgrade Based on Passenger Mix
	*/
	public function select_tourgrade(){
		$post_params = $this->input->post();
		// debug($post_params);
		// exit;
		//debug($get_params)
		$response['data'] = '';
        $response['msg'] = '';
        $response['status'] = FAILURE_STATUS;
		$safe_search_data = $this->transferv1_model->get_safe_search_data($post_params['search_id'],META_TRANSFERV1_COURSE);
		//debug($get_params);exit;
		if(trim($post_params['product_code'])!=''){

			load_transferv1_lib($post_params['booking_source']);
			if(trim($post_params['op']=='check_tourgrade')&&$post_params['product_code']!=''){

					$search_product_tourgrade = $this->transferv1_lib->get_tourgrade_list($post_params);


					$currency_obj = new Currency(array('module_type' => 'transferv1','from' => get_api_data_currency(), 'to' => get_application_currency_preference()));

					
					if($search_product_tourgrade['status']){

						$module_currency_obj = new Currency(array('module_type' => 'transferv1','from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));

						$raw_product_list = $this->transferv1_lib->tourgrade_in_preferred_currency($search_product_tourgrade['data'], $currency_obj,$module_currency_obj,'b2b');

						$search_product_tourgrade['data'] = $raw_product_list;
						$response['data'] = get_compressed_output($this->template->isolated_view('transferv1/viator/select_tourgrade',
							array('tourgrade_list'=>$search_product_tourgrade['data'],'search_params'=>$post_params,
								'currency_obj'=>$currency_obj,
								'booking_source'=>$post_params['booking_source'])));
						$response['msg'] = 'success';
						$response['status'] = SUCCESS_STATUS;
						
					}else{
						$search_product_tourgrade['Message'] = $search_product_tourgrade['Message'];
						
						if(!valid_array($search_product_tourgrade['data'])){
							
							$response['msg'] = $search_product_tourgrade['Message'] ;
						
						}
					}
					
					

					 
			}else{
				
			}
		}else{
			
		}
		 $this->output_compressed_data($response);
		
	}
	/**
     * Compress and output data
     * @param array $data
     */
    private function output_compressed_data($data) {


        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        ob_start("ob_gzhandler");
        header('Content-type:application/json');
        echo json_encode($data);
        ob_end_flush();
        exit;
    }
	/**
	*Ealvarasi check the available date for the product
	**/
	public function select_date(){
		$post_params = $this->input->post();

		$selected_date = trim($post_params['selected_date']);
		//echo $selected_date;exit;
		//debug($get_params);
		if(!empty($selected_date)){
			
			$product_get_booking_date = json_decode(base64_decode($post_params['available_date']),true);
			if(valid_array($product_get_booking_date)){

				$selected_date_details = $product_get_booking_date;
				//debug($selected_date_details);exit;
				$options = '';

				//debug($selected_date_details[$selected_date]);exit;
				foreach ($selected_date_details[$selected_date] as $key => $value) {
					$selected = '';
					if(isset($post_params['s_date'])){

						if($value==$post_params['s_date']){
							$selected =' selected';
						}
					}
					
					$options .='<option value='.$value.' '.$selected.' >'.($value).'</option>';
				}
				
				echo $options;
				exit;
				
			}
		}
	}
	/**
	 *  Elavarasi
	 * Passenger Details page for final bookings
	 * Here we need to run booking based on api
	 */
	function booking()
	{
		$pre_booking_params = $this->input->post();
		
		$search_id = $pre_booking_params['search_id'];
		$safe_search_data = $this->transferv1_model->get_safe_search_data($pre_booking_params['search_id'],META_TRANSFERV1_COURSE);

		$safe_search_data['data']['search_id'] = abs($search_id);
		$page_data['active_payment_options'] = $this->module_model->get_active_payment_module_list();

		if (isset($pre_booking_params['booking_source']) == true) {
			
			//We will load different page for different API providers... As we have dependency on API for tourgrade details page
			$page_data['search_data'] = $safe_search_data['data'];
			load_transferv1_lib($pre_booking_params['booking_source']);
			//Need to fill pax details by default if user has already logged in
			$this->load->model('user_model');
			$page_data['pax_details'] = array();
			$agent_details = $this->user_model->get_current_user_details();
			
			$page_data['agent_address'] = $agent_details[0]['address'];
			
			header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache");

			if ($pre_booking_params['booking_source'] == PROVAB_TRANSFERV1_BOOKING_SOURCE && isset($pre_booking_params['tour_uniq_id']) == true and
			isset($pre_booking_params['op']) == true and $pre_booking_params['op'] == 'block_trip' and $safe_search_data['status'] == true)
			{
				
				if ($pre_booking_params['tour_uniq_id'] != false) {

					$trip_block_details = $this->transferv1_lib->block_trip($pre_booking_params);
					
					#debug($trip_block_details);
					#exit;
					if ($trip_block_details['status'] == false) {
						redirect(base_url().'index.php/transferv1/exception?op='.$trip_block_details['data']['msg']);
					}
					//Converting API currency data to preferred currency
					$currency_obj = new Currency(array('module_type' => 'transferv1','from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
					
					$trip_block_details = $this->transferv1_lib->tripblock_data_in_preferred_currency($trip_block_details, $currency_obj,'b2b');				
					// debug($trip_block_details);
					// exit;
					//Display
					$currency_obj = new Currency(array('module_type' => 'transferv1', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
					$pre_booking_params = $this->transferv1_lib->update_block_details($trip_block_details['data']['BlockTrip'], $pre_booking_params,$currency_obj,'b2b');
					#debug($pre_booking_params);
					#exit;
					$pre_booking_params['markup_price_summary'] =  $this->transferv1_lib->update_commission_markup_module_wise($pre_booking_params['Price'],$currency_obj);		
					
					if ($trip_block_details['status'] == SUCCESS_STATUS) {
						if(!empty($this->entity_country_code)){
							$page_data['user_country_code'] = $this->entity_country_code;
						}
						else{
							$page_data['user_country_code'] = '';	
						}
						$page_data['booking_source'] = $pre_booking_params['booking_source'];
						$page_data['pre_booking_params'] = $pre_booking_params;
						$page_data['pre_booking_params']['default_currency'] = get_application_currency_preference();
						
						$page_data['iso_country_list']	= $this->db_cache_api->get_iso_country_list();
						$page_data['country_list']		= $this->db_cache_api->get_country_list();
						$page_data['currency_obj']		= $currency_obj;
						$page_data['total_price']		= $this->transferv1_lib->total_price($pre_booking_params['markup_price_summary']);
						$page_data['convenience_fees'] =0;
						//calculate convience fees by pax wise
						$ageband_details = $trip_block_details['data']['BlockTrip']['AgeBands'];
						
						$page_data['tax_service_sum']	=  $this->transferv1_lib->tax_service_sum($pre_booking_params['markup_price_summary'], $pre_booking_params['price_summary']);

						//Traveller Details
						$page_data['traveller_details'] = $this->user_model->get_current_user_details();
						//Get the country phone code 
						$Domain_record = $this->custom_db->single_table_records('domain_list', '*');
						$page_data['active_data'] =$Domain_record['data'][0];
						$temp_record = $this->custom_db->single_table_records('api_country_list', '*');
						$page_data['phone_code'] =$temp_record['data'];
						
						$page_data['search_id'] = $search_id;

						// debug($page_data);
						// exit;
						$this->template->view('transferv1/viator/viator_booking_page', $page_data);
					}
				} else {
					redirect(base_url().'index.php/transferv1/exception?op=Data Modification&notification=Data modified while transfer(Invalid Data received while validating tokens)');
				}
			} else {

				redirect(base_url());
			}
		} else {
			redirect(base_url());
		}
	}

	/**
	 *  Elavarasi
	 * sending for booking	 
	 */
	function pre_booking($search_id)
	{
		
		$post_params = $this->input->post();
		if($this->entity_status==LOCK && ($post_params ['selected_pm']=="WALLET"))
		{
			redirect(base_url().'index.php/flight/exception?op=locked_user&notification=locked_user');
			exit;
		}
		$post_params['billing_city'] = 'Bangalore';
		$post_params['billing_zipcode'] = '560100';
		$post_params['billing_address_1'] = '2nd Floor, Venkatadri IT Park, HP Avenue,, Konnappana Agrahara, Electronic city';
		$selected_pm=$post_params ['selected_pm'];

		//Make sure token and temp token matches
		$valid_temp_token = unserialized_data($post_params['token'], $post_params['token_key']);
		
		if ($valid_temp_token != false) {

			load_transferv1_lib($post_params['booking_source']);
			/****Convert Display currency to Application default currency***/
			//After converting to default currency, storing in temp_booking
			$post_params['token'] = unserialized_data($post_params['token']);
			$currency_obj = new Currency ( array (
						'module_type' => 'transferv1',
						'from' => get_application_currency_preference (),
						'to' => get_application_default_currency () 
				));
			$post_params['token'] = $this->transferv1_lib->convert_token_to_application_currency($post_params['token'], $currency_obj, $this->current_module);
			
			$post_params['token'] = serialized_data($post_params['token']);

			$temp_token = unserialized_data($post_params['token']);
			//Insert To temp_booking and proceed
			$temp_booking = $this->module_model->serialize_temp_booking_record($post_params, TRANSFER_BOOKING);
			$book_id = $temp_booking['book_id'];
			$book_origin = $temp_booking['temp_booking_origin'];
		
			// debug($temp_token);
			// exit;
			if ($post_params['booking_source'] == PROVAB_TRANSFERV1_BOOKING_SOURCE) {
				$amount	  = $this->transferv1_lib->total_price($temp_token['markup_price_summary'],'b2b');

				$currency = $temp_token['default_currency'];
			}
			$currency_obj = new Currency ( array (
						'module_type' => 'transferv1',
						'from' => admin_base_currency (),
						'to' => admin_base_currency () 
			) );
			/********* Convinence Fees Start ********/
			$convenience_fees = 0;
			/********* Convinence Fees End ********/
			 	
			/********* Promocode Start ********/
			$promocode_discount =0;
			/********* Promocode End ********/

			//details for PGI
			
			$email = $post_params ['billing_email'];
			$phone = $post_params ['passenger_contact'];
//			$verification_amount = roundoff_number($amount+$convenience_fees-$promocode_discount);
			$verification_amount = roundoff_number($amount);
			$firstname = $post_params ['first_name'] ['0'];
			$productinfo = META_TRANSFERV1_COURSE;

			$agent_paybleamount = $currency_obj->get_agent_paybleamount($verification_amount);
		
			//check current balance before proceeding further
			$domain_balance_status = $this->domain_management_model->verify_current_balance($agent_paybleamount['amount'], $currency);			
			if ($domain_balance_status == true || $selected_pm) {
				switch($post_params['payment_method']) {
					case PAY_NOW :
						$this->load->model('transaction');
						$pg_currency_conversion_rate = $currency_obj->payment_gateway_currency_conversion_rate();
						$this->transaction->create_payment_record($book_id, $verification_amount, $firstname, $email, $phone, $productinfo, $convenience_fees, $promocode_discount, $pg_currency_conversion_rate);
						redirect(base_url().'index.php/payment_gateway/payment/'.$book_id.'/'.$book_origin.'/'.$selected_pm);		
						//redirect(base_url().'index.php/payment_gateway/payment/'.$book_id.'/'.$book_origin);						
						break;
					case PAY_AT_BANK : echo 'Under Construction - Remote IO Error';exit;
					break;
				}
			} else {
				redirect(base_url().'index.php/transferv1/exception?op=Amount Transfers Booking&notification=insufficient_balance');
			}
		} else {
			redirect(base_url().'index.php/transferv1/exception?op=Remote IO error @ Transfers Booking&notification=validation');
		}
	}


	/*
		process booking in backend until show loader 
	*/
	function process_booking($book_id, $temp_book_origin, $is_paid_by_pg=0){
		
		if($book_id != '' && $temp_book_origin != '' && intval($temp_book_origin) > 0){

			$page_data ['form_url'] = base_url () . 'index.php/transferv1/secure_booking';
			$page_data ['form_method'] = 'POST';
			$page_data ['form_params'] ['book_id'] = $book_id;
			$page_data ['form_params'] ['temp_book_origin'] = $temp_book_origin;
			$page_data ['form_params'] ['is_paid_by_pg'] = $is_paid_by_pg;
			$this->template->view('share/loader/booking_process_loader', $page_data);	

		}else{
			redirect(base_url().'index.php/transferv1/exception?op=Invalid request&notification=validation');
		}
		
	}

	/**
	 * Elavarasi
	 *Do booking once payment is successfull - Payment Gateway
	 *and issue voucher
	 */
	function secure_booking()
	{
		$post_data = $this->input->post();
		
		//debug($post_data);exit;
		if(valid_array($post_data) == true && isset($post_data['book_id']) == true && isset($post_data['temp_book_origin']) == true &&
			empty($post_data['book_id']) == false && intval($post_data['temp_book_origin']) > 0){
			//verify payment status and continue
			$book_id = trim($post_data['book_id']);
			$temp_book_origin = intval($post_data['temp_book_origin']);
			
			
		} else{
			redirect(base_url().'index.php/transferv1/exception?op=InvalidBooking&notification=invalid');
		}
		//Check whether amount is paid through PG
		$is_paid_by_pg=$post_data['is_paid_by_pg'];		
		//run booking request and do booking
		$temp_booking = $this->module_model->unserialize_temp_booking_record($book_id, $temp_book_origin);
		//debug($temp_booking);exit;
		//Delete the temp_booking record, after accessing
		$this->module_model->delete_temp_booking_record ($book_id, $temp_book_origin);
		load_transferv1_lib($temp_booking['booking_source']);
		//verify payment status and continue
		

		$total_booking_price = $this->transferv1_lib->total_price($temp_booking['book_attributes']['token']['markup_price_summary'],'b2b');
		$currency_obj = new Currency ( array (
					'module_type' => 'transferv1',
					'from' => admin_base_currency (),
					'to' => admin_base_currency () 
		) );

		$currency = $temp_booking['book_attributes']['token']['default_currency'];
		$api_amount = $temp_booking['book_attributes']['token']['API_Price']['NetFare'];
		$agent_paybleamount = $currency_obj->get_agent_paybleamount($total_booking_price);

		//also verify provab balance
		//check current balance before proceeding further
		$domain_balance_status = $this->domain_management_model->verify_current_balance($agent_paybleamount['amount'], $currency);
		// debug($temp_booking);exit;
		if ($domain_balance_status || $is_paid_by_pg) {
			//lock table
			if ($temp_booking != false) {
				switch ($temp_booking['booking_source']) {
					case PROVAB_TRANSFERV1_BOOKING_SOURCE :
					
						//FIXME : COntinue from here - Booking request
						$booking = $this->transferv1_lib->process_booking($book_id, $temp_booking['book_attributes']);
						//Save booking based on booking status and book id
						break;
				}
				
				if ($booking['status'] == SUCCESS_STATUS) {
					
                	$api_amount = 0 - $api_amount;
					$this->api_balance_manager->update_api_balance($temp_booking['booking_source'], $api_amount);

					$currency_obj = new Currency(array('module_type' => 'transferv1', 'from' => admin_base_currency(), 'to' => admin_base_currency()));
					$promo_currency_obj = new Currency(array('module_type' => 'transferv1', 'from' => get_application_currency_preference(), 'to' => admin_base_currency()));
					$booking['data']['currency_obj'] = $currency_obj;
					$booking['data']['promo_currency_obj']=$promo_currency_obj;
					//Save booking based on booking status and book id
					$data = $this->transferv1_lib->save_booking($book_id, $booking['data'],$this->current_module);
					
					$this->domain_management_model->update_transaction_details('transferv1', $book_id, $data['fare'], $data['admin_markup'], $data['agent_markup'], $data['convinence'], $data['discount'],$data['transaction_currency'], $data['currency_conversion_rate'], $is_paid_by_pg);

					redirect(base_url().'index.php/voucher/transfers/'.$book_id.'/'.$temp_booking['booking_source'].'/'.$data['booking_status'].'/show_voucher/0/1');
				}
				if ($is_paid_by_pg && in_array($booking['status'], array(BOOKING_ERROR, FAILURE_STATUS))){
					$pg_name = $temp_booking['book_attributes']['selected_pm'];
					redirect ( base_url () . 'index.php/payment_gateway/refund/'.$book_id.'/'.$pg_name);
					//exit;
				}
				else {
					redirect(base_url().'index.php/transferv1/exception?op=booking_exception&notification='.$booking['msg']);
				}
			}
			//release table lock
		} else {
			redirect(base_url().'index.php/transferv1/exception?op=Remote IO error @ Insufficient&notification=validation');
		}
		//redirect(base_url().'index.php/hotel/exception?op=Remote IO error @ Hotel Secure Booking&notification=validation');
	}

	function test(){
		$currency_obj = new Currency(array('module_type' => 'sightseeing', 'from' => admin_base_currency(), 'to' => admin_base_currency()));
		debug($currency_obj);
	}

	
	/**
	 * Elavarasi
	 */
	function pre_cancellation($app_reference, $booking_source)
	{
		if (empty($app_reference) == false && empty($booking_source) == false) {
			$page_data = array();
			$booking_details = $this->transferv1_model->get_booking_details($app_reference, $booking_source);
			if ($booking_details['status'] == SUCCESS_STATUS) {
				$this->load->library('booking_data_formatter');
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_transferv1_booking_data($booking_details, 'b2b');
				$page_data['data'] = $assembled_booking_details['data'];
				$this->template->view('transferv1/pre_cancellation', $page_data);
			} else {
				redirect('security/log_event?event=Invalid Details');
			}
		} else {
			redirect('security/log_event?event=Invalid Details');
		}
	}
	/*
	 * Elavarasi
	 * Process the Booking Cancellation
	 * Full Booking Cancellation
	 *
	 */
	function cancel_booking($app_reference, $booking_source)
	{
		if(empty($app_reference) == false) {
			$get_params = $this->input->get();

			$master_booking_details = $this->transferv1_model->get_booking_details($app_reference, $booking_source);
			if ($master_booking_details['status'] == SUCCESS_STATUS) {
				$this->load->library('booking_data_formatter');
				$master_booking_details = $this->booking_data_formatter->format_transferv1_booking_data($master_booking_details, 'b2b');
				$master_booking_details = $master_booking_details['data']['booking_details'][0];
				load_transferv1_lib($booking_source);
				$cancellation_details = $this->transferv1_lib->cancel_booking($master_booking_details,$get_params);//Invoke Cancellation Methods
				if($cancellation_details['status'] == false) {
					$query_string = '?error_msg='.$cancellation_details['msg'];
				} else {
					$query_string = '';
				}
				redirect('transferv1/cancellation_details/'.$app_reference.'/'.$booking_source.$query_string);
			} else {
				redirect('security/log_event?event=Invalid Details');
			}
		} else {
			redirect('security/log_event?event=Invalid Details');
		}
	}
	 /**
	 * Elavarasi K
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
			$booking_details = $this->transferv1_model->get_booking_details($app_reference, $booking_source, $status);
			if($booking_details['status'] == SUCCESS_STATUS){
				$page_data = array();
				$page_data['booking_data'] = 		$booking_details['data'];
				$this->template->view('transferv1/cancellation_refund_details', $page_data);
			} else {
				redirect(base_url());
			}
		} else {
			redirect(base_url());
		}
	}

	/**
	 * Elavarasi
	 * Cancellation Details
	 * @param $app_reference
	 * @param $booking_source
	 */
	function cancellation_details($app_reference, $booking_source)
	{
		if (empty($app_reference) == false && empty($booking_source) == false) {
			$master_booking_details = $GLOBALS['CI']->transferv1_model->get_booking_details($app_reference, $booking_source);
			if ($master_booking_details['status'] == SUCCESS_STATUS) {
				$page_data = array();
				$this->load->library('booking_data_formatter');
				$master_booking_details = $this->booking_data_formatter->format_transferv1_booking_data($master_booking_details, 'b2b');
				$page_data['data'] = $master_booking_details['data'];
				$this->template->view('transferv1/cancellation_details', $page_data);
			} else {
				redirect('security/log_event?event=Invalid Details');
			}
		} else {
			redirect('security/log_event?event=Invalid Details');
		}

	}
	


	/**
	 * Elavarasi
	 */
	function exception($redirect=true)
	{
		$module = META_TRANSFERV1_COURSE;
		$op = (empty($_GET['op']) == true ? '' : $_GET['op']);
		$notification = (empty($_GET['notification']) == true ? '' : $_GET['notification']);
		if($op == 'Some Problem Occured. Please Search Again to continue'){
			$op = 'Some Problem Occured. ';
		}
		if($notification=='In Sufficiant Balance'){
		
			$notification = 'In Sufficiant Balance For Transfers';
		}

		$eid = $this->module_model->log_exception($module, $op, $notification);

		//set ip log session before redirection
		$this->session->set_flashdata(array('log_ip_info' => true));
		
		if($redirect){
			redirect(base_url().'index.php/transferv1/event_logger/'.$eid);
		}
		
	}

	function event_logger($eid='')
	{
		
		$log_ip_info = $this->session->flashdata('log_ip_info');
		$exception_data  = $this->custom_db->single_table_records('exception_logger','*',array('exception_id'=>$eid),0,1);
		$exception=$exception_data['data'][0];
		$this->template->view('transferv1/exception', array('log_ip_info' => $log_ip_info, 'eid' => $eid,'exception'=>$exception));
	}

	
}
