<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * @package    Provab
 * @subpackage Car
 * @author     Anitha J<anitha.g.provab@gmail.com>
 * @version    V1
 */

class Car extends CI_Controller {
	private $current_module;
	public function __construct()
	{
		parent::__construct();
		$this->load->model ( 'car_model' );
		$this->current_module = $this->config->item('current_module');
	}
	function index()
	{
		//	echo number_format(0, 2, '.', '');
	}
	/**
	 *  Anitha G
	 * Load Car Search Result
	 * @param number $search_id unique number which identifies search criteria given by user at the time of searching
	 */
	function search($search_id)
	{


		$safe_search_data = $this->car_model->get_safe_search_data($search_id);
		
		// Get all the Cars
		$active_booking_source = $this->car_model->car_booking_source();
		
		if ($safe_search_data['status'] == true and valid_array($active_booking_source) == true) {
			$safe_search_data['data']['search_id'] = abs($search_id);	
			// debug($safe_search_data);exit;
			$page_params = array (
					'car_search_params' => $safe_search_data ['data'],
					'active_booking_source' => $active_booking_source 
			);
			$page_params ['from_currency'] = get_application_default_currency ();
			$page_params ['to_currency'] = get_application_currency_preference ();
			//debug($modify_safe_search_data); exit;
			
			$this->template->view('car/search_result_page', $page_params);
		} else {
			if ($safe_search_data['status'] == true) {
				$this->template->view ( 'general/popup_redirect');
			} else {
				$this->template->view ( 'flight/exception');
			}
			// $this->template->view ( 'general/popup_redirect');
		}

	}
	 /**
	 * Load Car Request
	 * */
  	function car_details($search_id){
  	$params = $this->input->post(); 
    // debug($params);exit;
    $this->load->model('user_model');
  	$safe_search_data = $this->car_model->get_safe_search_data($search_id);
  	$currency_obj = new Currency(array('module_type' => 'car', 'from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
  	if (isset($params['booking_source']) == true) {
  		load_car_lib($params['booking_source']);
  		if ($params['booking_source'] == PROVAB_CAR_BOOKING_SOURCE && isset($params['ResultIndex']) == true and isset($params['op']) == true and
			$params['op'] == 'get_details' and $safe_search_data['status'] == true) {
  			
  			$raw_car_details = $this->car_lib->get_rate_rules($params['ResultIndex']);
  		// debug($raw_car_details);exit;
  			$temp_record = $this->custom_db->single_table_records('api_country_list', '*');
            $page_data['phone_code'] = $temp_record['data'];
  			$Domain_record = $this->custom_db->single_table_records('domain_list', '*');
            $page_data['active_data'] = $Domain_record['data'][0];
            $total_price = $raw_car_details['data']['RateRule']['CarRuleResult'][0]['TotalCharge']['EstimatedTotalAmount'];
            $page_data['search_id'] = $search_id;
           	$page_data ['country_list'] = $this->db_cache_api->get_iso_country_code ();
           	$page_data['convenience_fees']  = $currency_obj->convenience_fees($total_price, $page_data['search_id']);
           	$page_data['active_payment_options'] = $this->module_model->get_active_payment_module_list();
  			$page_data['pax_details'] = $this->user_model->get_current_user_details();
  			if(!empty($this->entity_country_code)){
				$page_data['user_country_code'] = $this->entity_country_code;
			}
			else{
				$page_data['user_country_code'] = '';	
			}
  			if(isset($raw_car_details['status']) && $raw_car_details['status'] == true) {
  				$car_rate_result = $raw_car_details['data'];
  				$raw_car_rate_result = $raw_car_details['data']['RateRule']['CarRuleResult'][0];
  				$page_data['raw_car_rate_result'] = $raw_car_rate_result; 
  				// debug($raw_car_rate_result);exit;
  				if(isset($car_rate_result) && valid_array($car_rate_result)){
  					$currency_obj = new Currency(array('module_type' => 'car', 'from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
                    $car_rate_result = $this->car_lib->car_rule_in_preferred_currency($car_rate_result, 'b2b', $currency_obj, $search_id);
  					// debug($car_rate_result);exit;
  					// $raw_car_book['data'] = $this->car_lib->formate_rate_rule_data($car_rate_result,$params);
  					//update_markup_currency need to add markup
					$this->template->view('car/car_booking_page', array('currency_obj' => $currency_obj, 'car_rules' => $car_rate_result, 'car_search_params' => $safe_search_data['data'], 'active_booking_source' => $params['booking_source'], 'params' => $params,'page_data' => $page_data));
  							
  				}else {
  					redirect(base_url().'index.php/car/exceptio?nop=Remote IO error - Cache has no data @ Insufficient&notification=validation');
  				}
  			}
  		}
		else {
			redirect(base_url());
		}
  	}else {
		redirect(base_url());
	}
  }
 
	function pre_booking($search_id)
	{
		
		$post_params = $this->input->post();
		// debug($post_params);exit;
		//Make sure token and temp token matches
		$valid_temp_token = unserialized_data($post_params['token'], $post_params['token_key']);
		
		
		if ($valid_temp_token != false) {

			load_car_lib($post_params['booking_source']);
			/****Convert Display currency to Application default currency***/
			//After converting to default currency, storing in temp_booking
			$post_params['token'] = unserialized_data($post_params['token']);
			// debug($post_params);exit;
			$currency_obj = new Currency ( array (
						'module_type' => 'car',
						'from' => get_application_currency_preference (),
						'to' => admin_base_currency () 
				));
			// debug($post_params['token']);exit;
			//need to do
			// $post_params['token'] = $this->car_lib->convert_token_to_application_currency($post_params['token'], $currency_obj, $this->current_module);
			
			$post_params['token'] = serialized_data($post_params['token']);
			$temp_token = unserialized_data($post_params['token']);
			$temp_token ['default_currency'] = admin_base_currency ();
			//Insert To temp_booking and proceed
			$temp_booking = $this->module_model->serialize_temp_booking_record($post_params, CAR_BOOKING);
			$book_id = $temp_booking['book_id'];
			// echo $book_id;exit;
			$book_origin = $temp_booking['temp_booking_origin'];
			
			$amount = $temp_token['TotalCharge']['EstimatedTotalAmount'];
			$currency_obj = new Currency ( array (
						'module_type' => 'car',
						'from' => admin_base_currency (),
						'to' => admin_base_currency () 
			) );
			/********* Convinence Fees Start ********/
			$convenience_fees = $currency_obj->convenience_fees($amount, $search_id);
			/********* Convinence Fees End ********/
			 	
			/********* Promocode Start ********/
			$promocode_discount = $post_params['promo_code_discount_val'];
			/********* Promocode End ********/
			// echo $convenience_fees;exit;
			//details for PGI
			
			$email = $post_params ['billing_email'];
			$phone = $post_params ['passenger_contact'];
			$currency = $temp_token['TotalCharge']['CurrencyCode'];
			$verification_amount = roundoff_number($amount);
			$firstname = $post_params ['first_name'];
			$productinfo = META_CAR_COURSE;
			// echo $verification_amount;exit;
			$agent_paybleamount = $currency_obj->get_agent_paybleamount($verification_amount);
			//check current balance before proceeding further
			$domain_balance_status = $this->domain_management_model->verify_current_balance($verification_amount, $currency);			
			if ($domain_balance_status == true) {
				switch($post_params['payment_method']) {
					case PAY_NOW :
						$this->load->model('transaction');
						$pg_currency_conversion_rate = $currency_obj->payment_gateway_currency_conversion_rate();
						$this->transaction->create_payment_record($book_id, $verification_amount, $firstname, $email, $phone, $productinfo, $convenience_fees, $promocode_discount, $pg_currency_conversion_rate);
						redirect(base_url().'index.php/payment_gateway/payment/'.$book_id.'/'.$book_origin);						
						// redirect(base_url().'index.php/car/process_booking/'.$book_id.'/'.$book_origin);						
					break;
					case PAY_AT_BANK : echo 'Under Construction - Remote IO Error';exit;
					break;
				}
			} else {
				redirect(base_url().'index.php/car/exception?op=Amount Hotel Booking&notification=insufficient_balance');
			}
		} else {
			redirect(base_url().'index.php/car/exception?op=Remote IO error @ Hotel Booking&notification=validation');
		}
	}
	 /**
     * Anitha G
     */
    function exception() {
        $module = META_BUS_COURSE;
        $op = @$_GET['op'];
        $notification = @$_GET['notification'];
        $eid = $this->module_model->log_exception($module, $op, $notification);
        //set ip log session before redirection
        $this->session->set_flashdata(array('log_ip_info' => true));
        redirect(base_url() . 'index.php/car/event_logger/' . $eid);
    }

    function event_logger($eid = '') {
        $log_ip_info = $this->session->flashdata('log_ip_info');
        $this->template->view('car/exception', array('log_ip_info' => $log_ip_info, 'eid' => $eid));
    }
	function booking()
	{

		$this->template->view('car/car_booking_page');
	}
	/*
      process booking in backend until show loader
     */

    function process_booking($book_id, $temp_book_origin) {

        if ($book_id != '' && $temp_book_origin != '' && intval($temp_book_origin) > 0) {

            $page_data ['form_url'] = base_url() . 'index.php/car/secure_booking';
            $page_data ['form_method'] = 'POST';
            $page_data ['form_params'] ['book_id'] = $book_id;
            $page_data ['form_params'] ['temp_book_origin'] = $temp_book_origin;

            $this->template->view('share/loader/booking_process_loader', $page_data);
        } else {
            redirect(base_url() . 'index.php/car/exception?op=Invalid request&notification=validation');
        }
    }
     /**
     * 
     * Do booking once payment is successfull - Payment Gateway
     * and issue voucher
     * CB19-133522-532376/45
     */
    function secure_booking() {
    	$post_data = $this->input->post();
    	// debug($post_data);exit;
      	if (valid_array($post_data) == true && isset($post_data['book_id']) == true && isset($post_data['temp_book_origin']) == true &&
                empty($post_data['book_id']) == false && intval($post_data['temp_book_origin']) > 0) {
            //verify payment status and continue
            $book_id = trim($post_data['book_id']);
            $temp_book_origin = intval($post_data['temp_book_origin']);
        } else {
            redirect(base_url() . 'index.php/bus/exception?op=InvalidBooking&notification=invalid');
        }
        //run booking request and do booking
        $temp_booking = $this->module_model->unserialize_temp_booking_record($book_id, $temp_book_origin);
    	//Delete the temp_booking record, after accessing
		// $this->module_model->delete_temp_booking_record ($book_id, $temp_book_origin);
    	// debug($temp_booking);exit;
    	load_car_lib($temp_booking['booking_source']);
    	$amount = $temp_booking['book_attributes']['token']['TotalCharge']['EstimatedTotalAmount'];
    	$currency = $temp_booking['book_attributes']['token']['TotalCharge']['CurrencyCode'];
        $currency_obj = new Currency ( array (
						'module_type' => 'car',
						'from' => get_application_currency_preference (),
						'to' => admin_base_currency () 
				));
        //check current balance before proceeding further
        $domain_balance_status = $this->domain_management_model->verify_current_balance($amount, $currency);
        // echo $domain_balance_status;exit;
        if ($domain_balance_status) {
        	 if ($temp_booking != false) {
                switch ($temp_booking['booking_source']) {
                    case PROVAB_CAR_BOOKING_SOURCE :
                        $booking = $this->car_lib->process_booking($book_id, $temp_booking['book_attributes']);
                          break;
               			}
               			// debug($booking);exit;
                        if ($booking['status'] == SUCCESS_STATUS) {
                        	
							
							$booking['data']['currency_obj'] = $currency_obj;
							$booking['data']['temp_booking'] = $temp_booking; 
							
							//Save booking based on booking status and book id
							$data = $this->car_lib->save_booking($book_id, $booking['data'],'b2b');
							// debug($data);exit;
							$this->domain_management_model->update_transaction_details('car', $book_id, $data['fare'], $data['admin_markup'], $data['agent_markup'], $data['convinence'], $data['discount'],$data['transaction_currency'], $data['currency_conversion_rate'] );
                        	redirect(base_url() . 'index.php/voucher/car/' . $book_id . '/' . $temp_booking['booking_source'] . '/BOOKING_CONFIRMED/show_voucher');
                        }
                        else {
                    		redirect(base_url() . 'index.php/car/exception?op=booking_exception&notification=' . $booking['msg']);
                		}
                
            }
        }
    }
    /**
	 * Anitha G
	 */
	function pre_cancellation($app_reference, $booking_source)
	{
		if (empty($app_reference) == false && empty($booking_source) == false) {
			$page_data = array();
			$booking_details = $this->car_model->get_booking_details($app_reference, $booking_source);
			if ($booking_details['status'] == SUCCESS_STATUS) {
				$this->load->library('booking_data_formatter');
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_car_booking_datas($booking_details, 'b2b');
				$page_data['data'] = $assembled_booking_details['data'];
				$this->template->view('car/pre_cancellation', $page_data);
			} else {
				redirect('security/log_event?event=Invalid Details');
			}
		} else {
			redirect('security/log_event?event=Invalid Details');
		}
	}
	
	/*
	 * Anitha G
	 * Process the Booking Cancellation
	 * Full Booking Cancellation
	 *
	 */
	function cancel_booking($app_reference, $booking_source)
	{
		if(empty($app_reference) == false) {
			$master_booking_details = $this->car_model->get_booking_details($app_reference, $booking_source);
			if ($master_booking_details['status'] == SUCCESS_STATUS) {
				$this->load->library('booking_data_formatter');
				$master_booking_details = $this->booking_data_formatter->format_car_booking_datas($master_booking_details, 'b2b');
				$master_booking_details = $master_booking_details['data']['booking_details'][0];
				load_car_lib($booking_source);
				$cancellation_details = $this->car_lib->cancel_booking($master_booking_details);//Invoke Cancellation Methods
				if($cancellation_details['status'] == false) {
					$query_string = '?error_msg='.$cancellation_details['msg'];
				} else {
					$query_string = '';
				}
				redirect('car/cancellation_details/'.$app_reference.'/'.$booking_source.$query_string);
			} else {
				redirect('security/log_event?event=Invalid Details');
			}
		} else {
			redirect('security/log_event?event=Invalid Details');
		}
	}
	/**
	 * Anitha G
	 * Cancellation Details
	 * @param $app_reference
	 * @param $booking_source
	 */
	function cancellation_details($app_reference, $booking_source)
	{
		if (empty($app_reference) == false && empty($booking_source) == false) {
			$master_booking_details = $GLOBALS['CI']->car_model->get_booking_details($app_reference, $booking_source);
			if ($master_booking_details['status'] == SUCCESS_STATUS) {
				$page_data = array();
				$this->load->library('booking_data_formatter');
				$master_booking_details = $this->booking_data_formatter->format_car_booking_datas($master_booking_details, 'b2b');
				$page_data['data'] = $master_booking_details['data'];
				$this->template->view('car/cancellation_details', $page_data);
			} else {
				redirect('security/log_event?event=Invalid Details');
			}
		} else {
			redirect('security/log_event?event=Invalid Details');
		}

	}
    /**
	 * Anitha G
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
			$booking_details = $this->car_model->get_booking_details($app_reference, $booking_source, $status);
			if($booking_details['status'] == SUCCESS_STATUS){
				$page_data = array();
				$page_data['booking_data'] = 		$booking_details['data'];
				$this->template->view('car/cancellation_refund_details', $page_data);
			} else {
				redirect(base_url());
			}
		} else {
			redirect(base_url());
		}
	}
	function Voucher()
	{

		$this->template->view('voucher/car_voucher');
	}
}