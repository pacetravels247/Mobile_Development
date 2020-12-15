<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
ini_set('max_execution_time', 300);
/**
 *
 * @package    Provab
 * @subpackage Flight
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V1
 */

class Flight extends CI_Controller {
	private $current_module;
	public function __construct()
	{
		parent::__construct();
		//$this->output->enable_profiler(TRUE);
		$this->load->model('flight_model');
		$this->load->model('user_model');
		$this->load->model('domain_management_model');
		$this->current_module = $this->config->item('current_module');
		$this->load->library('booking_data_formatter');
		$this->load->library('utility/notification','notification');
		$this->load->library('api_balance_manager');
	}

	function get_booking_details($app_reference)
	{
		//
		$condition[] = array('BD.app_reference', '=', $this->db->escape($app_reference));
		$details = $this->flight_model->get_booking_details($app_reference);
		if ($details['status'] == SUCCESS_STATUS) {
			$booking_source = $details['data']['booking_details']['booking_source'];
			load_flight_lib($booking_source);
			$this->flight_lib->get_booking_details($details['data']['booking_details'], $details['data']['booking_transaction_details']);
		}
	}
	/**
	 * Cancellation
	 * Balu A
	 */
	function pre_cancellation($app_reference, $booking_source)
	{
		if (empty($app_reference) == false && empty($booking_source) == false) {
			$page_data = array();
			$booking_details = $this->flight_model->get_booking_details($app_reference, $booking_source);
			if ($booking_details['status'] == SUCCESS_STATUS) {
				$this->load->library('booking_data_formatter');
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_flight_booking_data($booking_details,$this->current_module);
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
	function cancel_booking()
	{
		//error_reporting(E_ALL);
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
				if($cancellation_details["status"]){
					$cancellation_details_temp = base64_encode(json_encode($cancellation_details));
					$api_refund_amount = $this->cancellation_details($app_reference, $booking_source, $cancellation_details_temp, 1);
					$this->api_balance_manager->update_api_balance($booking_source, $api_refund_amount);
				}
				$cancellation_details = base64_encode(json_encode($cancellation_details));

				redirect('flight/cancellation_details/'.$app_reference.'/'.$booking_source.'/'.$cancellation_details);
			} else {
				redirect('security/log_event?event=Invalid Details');
			}
		} else {
			redirect('security/log_event?event=Invalid Details');
		}
	}
	/**
	 *
	 * @param $app_reference
	 * @param $booking_source
	 */
	function cancellation_details($app_reference, $booking_source, $cancellation_details, $return_api_refund_amount = 0)
	{
		$cancellation_details = json_decode(base64_decode($cancellation_details), true);
		if (empty($app_reference) == false && empty($booking_source) == false) {
			$master_booking_details = $GLOBALS['CI']->flight_model->get_booking_details($app_reference, $booking_source);
			if ($master_booking_details['status'] == SUCCESS_STATUS) {
				$page_data = array();
				$this->load->library('booking_data_formatter');
				$master_booking_details = $this->booking_data_formatter->format_flight_booking_data($master_booking_details, 'b2c');

                                
				$page_data['data'] = $master_booking_details['data'];
				$page_data['cancellation_status'] = $cancellation_details['status'];
				$page_data['cancellation_message'] = $cancellation_details['message'];

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
	 * Get supplier cancellation status
	 */
	public function update_supplier_cancellation_status_details()
	{
		$get_data = $this->input->get();

		if(isset($get_data['app_reference']) == true && isset($get_data['booking_source']) == true && isset($get_data['passenger_status']) == true && $get_data['passenger_status'] == 'BOOKING_CANCELLED' && isset($get_data['passenger_origin']) == true && intval($get_data['passenger_origin']) > 0){
			$app_reference = trim($get_data['app_reference']);
			$booking_source = trim($get_data['booking_source']);
			$passenger_origin = trim($get_data['passenger_origin']);
			$passenger_status = trim($get_data['passenger_status']);
			$booking_details = $this->flight_model->get_passenger_ticket_info($app_reference, $passenger_origin, $passenger_status);
			if($booking_details['status'] == SUCCESS_STATUS){
				$master_booking_details = $booking_details['data']['booking_details'][0];
				$booking_customer_details = $booking_details['data']['booking_customer_details'][0];
				$cancellation_details = $booking_details['data']['cancellation_details'][0];
				$booking_source = $master_booking_details['booking_source'];
				$request_data = array();
				$request_data['AppReference'] = 		$booking_customer_details['app_reference'];
				$request_data['SequenceNumber'] =		$booking_customer_details['sequence_number'];
				$request_data['BookingId'] = 			$booking_customer_details['book_id'];
				$request_data['PNR'] = 					$booking_customer_details['pnr'];
				$request_data['TicketId'] = 			$booking_customer_details['TicketId'];
				$request_data['ChangeRequestId'] =	$cancellation_details['RequestId'];
				load_flight_lib($booking_source);
				$supplier_ticket_refund_details = $this->flight_lib->get_supplier_ticket_refund_details($request_data);
				if($supplier_ticket_refund_details['status'] == SUCCESS_STATUS){
					$this->flight_model->update_supplier_ticket_refund_details($passenger_origin, $supplier_ticket_refund_details['data']);
				}
			}
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
			 // debug($booking_details);exit;
			if($booking_details['status'] == SUCCESS_STATUS){
				$this->load->library('booking_data_formatter');
				$booking_details = $this->booking_data_formatter->format_flight_booking_data($booking_details, $this->config->item('current_module'));
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
				$page_data['booked_user_details'] =	$booked_user_details;
				$page_data['is_agent'] = 			$is_agent;
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
			$booking_status = trim($get_data['booking_status']);
			$booking_details = $this->flight_model->get_passenger_ticket_info($app_reference, $passenger_origin, $passenger_status);
			if($booking_details['status'] == SUCCESS_STATUS){
				$booked_user_id = intval($booking_details['data']['booking_details'][0]['created_by_id']);
				$booked_user_details = array(); 
				$is_agent = false;
				$user_condition[] = array('U.user_id' ,'=', $booked_user_id);
				$booked_user_details = $this->user_model->get_user_details($user_condition);
				if(valid_array($booked_user_details) == true){
					$booked_user_details = $booked_user_details[0];
					if($booked_user_details['user_type'] == B2B_USER){
						$is_agent = true;
						$module = "b2b";
					}
					else
						$module = "b2c";
				}
				$master_booking_details = $this->flight_model->get_booking_details($app_reference, $booking_source, $booking_status);
				$master_booking_details = $this->booking_data_formatter->format_flight_booking_data($master_booking_details, $module);
				$page_data = array();
				$page_data['booking_data'] = $booking_details['data'];
				$page_data['master_booking_details'] = $master_booking_details;
				$page_data['booked_user_details'] =	$booked_user_details;
				$page_data['is_agent'] = 			$is_agent;
				$this->template->view('flight/cancellation_refund_details', $page_data);
			} else {
				redirect(base_url());
			}
		} else {
			redirect(base_url());
		}
	}
	public function update_supplier_refund_details()
	{
		$post_data = $this->input->post();
		$passenger_origin = intval($post_data['passenger_origin_for_supp_upd']);
		$update_refund_details["API_RefundedAmount"] = $post_data["api_refund_amount"];
		$update_refund_details["API_CancellationCharge"] = $post_data["api_cancellation_charge"];
		$update_refund_details["API_ServiceTaxOnRefundAmount"] = 0; //$post_data["api_service_tax"];
		$update_refund_details["API_SwachhBharatCess"] = 0; //$post_data["api_swatch_bharat"];
		$update_refund_details["API_refund_status"] = $post_data["api_refund_status"];
		//debug($post_data); exit;
		$update_refund_condition['passenger_fk'] =$passenger_origin; 
		$this->custom_db->update_record('flight_cancellation_details', $update_refund_details, $update_refund_condition);

		$app_reference = $post_data["app_reference_for_supp_upd"];
		$passenger_origin = $post_data["passenger_origin_for_supp_upd"];
		$passenger_status = $post_data["passenger_status_for_supp_upd"];

		$booking_details = $this->flight_model->get_passenger_ticket_info($app_reference, $passenger_origin, $passenger_status);
		//debug($booking_details); exit;
		$master_booking_details = $booking_details['data']['booking_details'][0];

		$redirect_url_params['app_reference'] = $app_reference;
		$redirect_url_params['booking_source'] = $master_booking_details['booking_source'];
		$redirect_url_params['passenger_status'] = $passenger_status;
		$redirect_url_params['passenger_origin'] = $passenger_origin;

		redirect('flight/cancellation_refund_details?'.http_build_query($redirect_url_params));
	}
	/**
	 * Balu A
	 * Update Ticket Refund Details
	 */

	/** 
	 ** Issue hold ticket 
	 **	Jeevanandam K
	**/
	function run_ticketing_method($app_reference,$booking_source)
	{	
		$response ['data'] = array ();
		$response ['Status'] = FAILURE_STATUS;
		$response ['Message'] = '';	

		load_flight_lib($booking_source);
		$this->load->library('booking_data_formatter');
		$token_detail = $GLOBALS['CI']->custom_db->single_table_records('flight_booking_transaction_details','*',array('app_reference'=>$app_reference,'status'=>"BOOKING_HOLD"));

		if(valid_array($token_detail) && $token_detail['status'] == SUCCESS_STATUS)
		{
			$token_details = $token_detail['data']['0'];
			if($token_details['hold_ticket_req_status'] == INACTIVE )
			{
				$sequence_number = $token_details['sequence_number'];
				$pnr = $token_details['pnr'];
				$booking_id = $token_details['book_id'];

				$booked_user_details = $this->flight_model->get_booked_user_details($app_reference);
				if($booked_user_details[0]['user_type'] == B2B_USER){
					$agent_id = $booked_user_details[0]['created_by_id'];
					$agent_details = $this->domain_management_model->get_agent_details($agent_id);
					
					$page_data['agent_details'] = $agent_details;
					$agent_base_currency = $agent_details['agent_base_currency'];
					
					$currency_obj = new Currency();
					$currency_conversion_rate = $currency_obj->getConversionRate(false, get_application_default_currency(), $agent_base_currency);//Currency conversion rate of the domain currency
									
					
					if(valid_array($agent_details) == false){//Invalid Agent ID
						redirect(base_url());
					}
					
					$page_data['agent_id'] = $agent_id;
					$amount = $this->booking_data_formatter->agent_buying_price($token_detail['data']);
					$post_data['amount'] = -abs($amount[0]);
					
					$debit_amount = ($post_data['amount']*$currency_conversion_rate);					
					
					
					$post_data['app_reference'] = $app_reference;
					$post_data['agent_list_fk'] = $agent_id;
					$post_data['remarks'] = "Flight transaction successfully done";
					$post_data['amount'] = $debit_amount;
					$post_data['currency'] = $agent_details['agent_base_currency'];
					$post_data['currency_conversion_rate'] = $currency_conversion_rate;
					$post_data['issued_for'] = 'Debited Towards: Flight ';
					$this->domain_management_model->process_direct_credit_debit_transaction($post_data);
					
					//Update Issue Hold Ticket Status In Booking Transaction Details
					$update_issue_ticket_req_status = $this->custom_db->update_record('flight_booking_transaction_details',array('hold_ticket_req_status'=>ACTIVE),array('app_reference'=>$app_reference,'pnr' => $pnr));

					$ticket_response = $this->flight_lib->issue_hold_ticket($app_reference,$sequence_number,$pnr,$booking_id);

					if($ticket_response['status'] == SUCCESS_STATUS)
					{

						$response['Status'] = SUCCESS_STATUS;
						$response['Message'] = "Request Sent Successfully !!";
					}else{
						$response['Status'] = FAILURE_STATUS;
						$response['Message'] = "Failed to send request !!";	
					}
				}else{
					$response['Status'] = FAILURE_STATUS;
					$response['Message'] = "Booking Details Not Found !!";
				}
			}else{
				$response['Status'] = FAILURE_STATUS;
				$response['Message'] = "Request Already Sent !!";
			}
			
		}else{
			$response['Status'] = FAILURE_STATUS;
			$response['Message'] = "Booking Details Not Found !!";
		}

		
		echo json_encode($response);
	}
	/**
	 * Balu A
	 */
	function exception()
	{
		$module = META_AIRLINE_COURSE;
		$op = @$_GET['op'];
		$notification = @$_GET['notification'];
		$eid = $this->module_model->log_exception($module, $op, $notification);
		//set ip log session before redirection
		$this->session->set_flashdata(array('log_ip_info' => true));
		redirect(base_url().'index.php/flight/event_logger/'.$eid);
	}

	function event_logger($eid='')
	{
		$log_ip_info = $this->session->flashdata('log_ip_info');
		$this->template->view('flight/exception', array('log_ip_info' => $log_ip_info, 'eid' => $eid));
	}
    function exception_log_details() {
        $get_data = $this->input->get();
        
        $result=$this->flight_model->exception_log_details($get_data);
        if($result=="null")
        {   $res['Status']=0;
            $res['Message']='Booking may confirmed, Please contact API support team';
            echo json_encode($res);
        }else {
        echo $result;exit;
        }
    }
     /*
     *
     * Flight(Airport) auto suggest
     *
     */

    function get_airport_code_list() {

        $term = $this->input->get('term'); //retrieve the search term that autocomplete sends
        $term = trim(strip_tags($term));
        $result = array();
        
        $__airports = $this->flight_model->get_airport_list($term)->result();
        if (valid_array($__airports) == false) {
            $__airports = $this->flight_model->get_airport_list('')->result();
        }
       
        $airports = array();
        foreach ($__airports as $airport) {
         	$airports['label'] = $airport->airport_city . ', ' . $airport->country . ' (' . $airport->airport_code . ')';
            $airports['value'] = $airport->airport_city . ' (' . $airport->airport_code . ')';
            $airports['id'] = $airport->origin;
            
            // if (empty($airport->top_destination) == false) {
            //     $airports['category'] = 'Top cities';
            //     $airports['type'] = 'Top cities';
            // } else {
            //     $airports['category'] = 'Search Results';
            //     $airports['type'] = 'Search Results';
            // }
            $airports['category'] = 'Search Results';
            $airports['type'] = 'Search Results';
            array_push($result, $airports);
        }
        $this->output_compressed_data($result);
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

    ///flight_offline_cancel

   /* public function flight_offline_cancel($app_reference, $booking_source='', $booking_status='', $operation='show_voucher',$module=''){

    }*/
    function flight_offline_cancel($app_reference, $booking_source='', $booking_status='', $operation='show_voucher',$module='')
	{
		$this->load->model('flight_model');
		$post_data = $this->input->post();
		//debug($post_data);die;
		if (empty($app_reference) == false && empty($post_data) ==true) {


			$booking_details = $this->flight_model->get_booking_details($app_reference, $booking_source, $booking_status);
			
			if ($booking_details['status'] == SUCCESS_STATUS) {
				load_flight_lib(PROVAB_FLIGHT_BOOKING_SOURCE);
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_flight_booking_data($booking_details, 'b2c', false);				
				$page_data['data'] = $assembled_booking_details['data'];
				
				if(isset($assembled_booking_details['data']['booking_details'][0])){
					//get agent address & logo for b2b voucher
					
					$domain_address = $this->custom_db->single_table_records ( 'domain_list','address,domain_logo',array('origin'=>get_domain_auth_id()));
					$page_data['data']['address'] =$domain_address['data'][0]['address'];
					$page_data['data']['logo'] = $domain_address['data'][0]['domain_logo'];
					if($assembled_booking_details['data']['booking_details'][0]['created_by_id'] > 0){
						$get_agent_info = $this->user_model->get_agent_info($assembled_booking_details['data']['booking_details'][0]['created_by_id']);
						if(!empty($get_agent_info)){
						$page_data['data']['address'] = $get_agent_info[0]['address'];
						$page_data['data']['logo'] = $get_agent_info[0]['logo'];
						}
					}
			
				}
				 $page_data['supliers_list'] = $this->domain_management_model->get_flight_suplier_source();
				 $page_data['current_module'] = $module;
				 //debug($page_data);
				switch ($operation) {
					case 'show_voucher' : $this->template->view('flight/offline_cancellation_details', $page_data);
					break;
				}
			}
		}
		if(empty($post_data) == false){
			//debug($post_data);die();
			$booking_details = $this->flight_model->get_booking_details($app_reference, $booking_source, $booking_status);
			if ($booking_details['status'] == SUCCESS_STATUS) {
				//debug($booking_details);
				$booking_transaction_details = $booking_details['data']['booking_transaction_details'][0];
				$assembled_booking_details = $this->booking_data_formatter->format_flight_booking_data($booking_details, 'b2c', false);
				$old_grand_total = $assembled_booking_details['data']['booking_details'][0]['grand_total'];

				$new_admin_markup =($post_data['grand_total'] - $old_grand_total);
				$admin_markup = $booking_transaction_details['admin_markup'];
				
				// Start Update Transaction Details 
				$cond['app_reference'] = $app_reference;
				$transaction_details['pnr'] = $post_data['pnr'] ;
				//$transaction_details['book_id'] = $post_data['booking_id'] ;
				$transaction_details['admin_markup'] = ($admin_markup+$new_admin_markup);
				$transaction_details['status']  = $post_data['status'];
				$this->custom_db->update_record('flight_booking_transaction_details',$transaction_details,$cond);
				// End Update Transaction Details 

				// Start Updating Ticket Details
				if(empty($post_data['ticket_number']) == false){
					foreach ($post_data['ticket_number'] as $key => $value) {
						$ticket_cond['passenger_fk'] = $key;
						$ticket_data['TicketNumber'] = $value;
						$ticket_data['IssueDate']    = date('Y-m-d H:i:s');
						$this->custom_db->update_record('flight_passenger_ticket_info',$ticket_data,$ticket_cond);
					}
				}
				// End Updating Ticket Details
				
				// Start Updating Itinerary Details
				if(empty($post_data['airline_pnr']) == false){
					foreach ($post_data['airline_pnr'] as $key => $value) {
						$itinerary_cond['origin'] = $key;
						$itinerary_data['airline_pnr'] = $value;
						$this->custom_db->update_record('flight_booking_itinerary_details',$itinerary_data,$itinerary_cond);

					}
				}
				// End Updating Itinerary Details

				// Start Updating Booking Details
				$booking_cond['app_reference'] = $app_reference;
				$booking_data['booking_source'] = $post_data['booking_source'];
				$booking_data['status'] = $post_data['status'];
				$this->custom_db->update_record('flight_booking_details',$booking_data,$booking_cond);
				// End Updating Booking Details
				
				// Start Updating Passengers Details
				$passenger_info['status'] = $post_data['status'];
				$this->custom_db->update_record('flight_booking_passenger_details',$passenger_info,array('app_reference'=>$app_reference));
				// End Updating Passengers Details

				//  Updating Cncellation Details
				if($post_data['status'] == 'BOOKING_CANCELLED' && $post_data['check_pax'] != ''){
					foreach ($post_data['check_pax'] as $key => $value) {
						$cancellation_data = array(
								'passenger_fk' => $value,
								'RequestId' =>1,
								'ChangeRequestStatus' =>1,
								'statusDescription' =>'',
								'API_RefundedAmount' => $post_data['api_r_amount'],
								'API_CancellationCharge' =>0,
								'API_ServiceTaxOnRefundAmount' =>0,
								'API_SwachhBharatCess' =>0,
								'API_refund_status' => 'PROCESSED',
								'cancellation_requested_on' => date('Y-m-d h:i:s'),
								'cancellation_processed_on' => date('Y-m-d h:i:s'),
								'refund_amount' => $post_data['refund_amount'],
								'cancellation_charge' => $post_data['cancellation_charge'],
								'service_tax_on_refund_amount' =>0,
								'swachh_bharat_cess' =>0,
								'refund_status' => $post_data['refund_status'],
								'refund_payment_mode' => 'offline',
								'refund_comments' =>'',
								'refund_date' => date('Y-m-d h:i:s'),
								'created_by_id' => $GLOBALS['CI']->entity_user_id,
								'created_datetime' => date('Y-m-d h:i:s'),
							);
						//insert camcellation details
						$new_cans_id = $this->custom_db->insert_record('flight_cancellation_details',$cancellation_data);
					}
					//debug($cancellation_data);die();

					//1.Crdeit the Refund Amount to Respective Agent
					$booked_user_id = $post_data['agent_id'];
					$agent_refund_amount = $post_data['refund_amount'];
					$fare = -($post_data['refund_amount']);
					$domain_markup=0;
					$level_one_markup=0;
					$convinence = 0;
					$discount = 0;
					/*$remarks = 'flight Refund was Successfully done';
					$this->domain_management_model->save_transaction_details('flight', $app_reference, $fare, $domain_markup, $level_one_markup, $remarks, $convinence, $discount, $booking_currency, $currency_conversion_rate, $booked_user_id);

					//update agent balance
					$this->domain_management_model->update_agent_balance($agent_refund_amount, $booked_user_id);*/

					$credit_towards = 'Flight Offline Cancellation';
					$comments = 'Refund Amount credited to agent wallet';

					$this->notification->credit_balance($booked_user_id, $app_reference, $credit_towards, $agent_refund_amount, 0, $comments);

				}
				$post_data['module'] = 'b2b_flight_voucher';
				// Start Updating Remark Details
				redirect(base_url().'index.php/voucher/'.$post_data['module'].'/'.$app_reference.'/'.$post_data['booking_source'].'/'.$post_data['status']);
			}
		}
	}

	//***********************************************Pace Travels************************************************
	public function update_ticket_refund_details()
	{
		$post_data = $this->input->post();
		$redirect_url_params = array();
		$this->form_validation->set_rules('app_reference', 'app_reference', 'trim|required');
		$this->form_validation->set_rules('passenger_origin', 'passenger_origin', 'trim|required');
		if ($this->form_validation->run()) {
			$app_reference = 				trim($post_data['app_reference']);
			$refund_payment_mode = 			trim($post_data['refund_payment_mode']);
			$refund_amount = 				floatval($post_data['refund_amount']);
			$cancellation_charge = 			floatval($post_data['cancellation_charge']);
			$service_tax_on_refund_amount =	floatval($post_data['GST']);
			//$swachh_bharat_cess = 			floatval($post_data['swachh_bharat_cess']);
			$refund_status 		= 			"PROCESSED";
			$refund_comments 	= 			trim($post_data['refund_comments']);
			$refund_commission 	= 			0;
			$refund_tds 		= 			0;
			//************************Agent Details*******************************************************
			$booked_user_id = intval($post_data['agent_id']);
			$user_condition[] = array('U.user_id' ,'=', $booked_user_id);
			$booked_user_details = $this->user_model->get_user_details($user_condition);
			$currency_conversion_rate = '';
			$is_agent = false;

			foreach ($post_data['pass_fk'] as $key => $value) {
				$passenger_origin 						= 	intval($value);
				$passenger_status 						= 	trim($post_data['status'][$key]);
				$cancellation_charge_per_pass 			= 	floatval($post_data['sup_cancel_charge'][$key]);
				$pace_charge_per_pass 			        = 	floatval($post_data['pace_cancel_charge'][$key]);
				$base_fare_per_pass 			        = 	floatval($post_data['TotalPrice'][$key]);
				$tax_per_pass 			                = 	floatval($post_data['Tax'][$key]);
				$service_tax_on_refund_amount_per_pass 	=	(18/100) * $pace_charge_per_pass;
				$refund_amount_per_pass 				= 	floatval(($base_fare_per_pass + $tax_per_pass) - ($cancellation_charge_per_pass + $pace_charge_per_pass + $service_tax_on_refund_amount_per_pass));
				if($passenger_status == 'BOOKING_INPROGRESS'){
					//Get Ticket Details
				$booking_details = $this->flight_model->get_passenger_ticket_info($app_reference, $passenger_origin, $passenger_status);
				
				if($booking_details['status'] == SUCCESS_STATUS){
					$master_booking_details = $booking_details['data']['booking_details'][0];
					$booking_customer_details = $booking_details['data']['booking_customer_details'][0];
					$cancellation_details = $booking_details['data']['cancellation_details'][0];
					$booking_currency = $master_booking_details['currency'];//booking currency
					$refund_commission 	= 			$booking_customer_details['agent_commission'];
					$refund_tds 		= 			$booking_customer_details['agent_tds'];
					
					
					if(valid_array($booked_user_details) == true && $booked_user_details[0]['user_type'] == B2B_USER){
						$is_agent = true;
					}
					$currency_obj = new Currency(array('from' => get_application_default_currency() , 'to' => $booking_currency));
					$currency_conversion_rate = $currency_obj->currency_conversion_value(true, get_application_default_currency(), $booking_currency);
				
					//UPDATE THE REFUND DETAILS
					//Update Condition
					$update_refund_condition = array();
					$update_refund_condition['passenger_fk'] =	$passenger_origin;
					//Update Data
					$update_refund_details = array();
					$update_refund_details['refund_payment_mode'] = 			$refund_payment_mode;
					$update_refund_details['refund_amount'] =					$refund_amount_per_pass;
					$update_refund_details['cancellation_charge'] = 			$cancellation_charge_per_pass + $pace_charge_per_pass;
					$update_refund_details['service_tax_on_refund_amount'] =	$service_tax_on_refund_amount_per_pass;
					$update_refund_details['swachh_bharat_cess'] = 				0;
					$update_refund_details['refund_status'] = 					$refund_status;
					$update_refund_details['refund_comments'] = 				$refund_comments;
					$update_refund_details['currency'] = 						$booking_currency;
					$update_refund_details['currency_conversion_rate'] = 		$currency_conversion_rate;
					$update_refund_details['commission_reversed'] = !empty($refund_commission)?$refund_commission:0;
					if($refund_status == 'PROCESSED'){
						$update_refund_details['refund_date'] = 				date('Y-m-d H:i:s');
					}
					$this->custom_db->update_record('flight_cancellation_details', $update_refund_details, $update_refund_condition);

					//********************Update Passenger Status********************************************
					$update_pass_status_condition = array();
					$update_pass_status_condition['origin'] =	$passenger_origin;
					$update_pass_status['status'] = 'BOOKING_CANCELLED';
					$this->custom_db->update_record('flight_booking_passenger_details', $update_pass_status, $update_pass_status_condition);
				}
				}
			}
				//***********************UPDATING AGENT BALANCE****************************************************
				if($refund_status == 'PROCESSED' && floatval($refund_amount) > 0 && $is_agent == true){
					//1.Crdeit the Refund Amount to Respective Agent
					$agent_refund_amount = ($currency_conversion_rate*$refund_amount);//converting to agent currency
					$agent_refund_amount = $agent_refund_amount - $refund_commission + $refund_tds; 
					
					//2.Add Transaction Log for the Refund
					$fare = -($agent_refund_amount);//dont remove: converting to negative
					$domain_markup=0;
					$level_one_markup=0;
					$convinence = 0;
					$discount = 0;
					$remarks = 'flight Refund was Successfully done';
					$this->domain_management_model->save_transaction_details('flight', $app_reference, $fare, $domain_markup, $level_one_markup, $remarks, $convinence, $discount, $booking_currency, $currency_conversion_rate, $booked_user_id);

					//update agent balance
					$this->domain_management_model->update_agent_balance($agent_refund_amount, $booked_user_id);
				}

				$redirect_url_params['app_reference'] = $app_reference;
				$redirect_url_params['booking_source'] = $master_booking_details['booking_source'];
				$redirect_url_params['passenger_status'] = $passenger_status;
				$redirect_url_params['passenger_origin'] = $passenger_origin;
		}
		redirect('report/cancellation_queue');
	}

	public function reject_refund(){
		//Update Condition
					$passenger_origin = $this->input->post('pass_fk');
					$refund_comments  = $this->input->post('comment');
					$update_refund_condition = array();
					$update_refund_condition['passenger_fk'] =	$passenger_origin;
					//Update Data
					$update_refund_details = array();
					$update_refund_details['refund_status'] = 					'REJECTED';
					$update_refund_details['refund_comments'] = 				$refund_comments;
					if($refund_status == 'REJECTED'){
						$update_refund_details['refund_date'] = 				date('Y-m-d H:i:s');
					}
					$this->custom_db->update_record('flight_cancellation_details', $update_refund_details, $update_refund_condition);
					//********************Update Passenger Status********************************************
					$update_pass_status_condition = array();
					$update_pass_status_condition['origin'] =	$passenger_origin;
					$update_pass_status['status'] = 'REJECTED';
					$this->custom_db->update_record('flight_booking_passenger_details', $update_pass_status, $update_pass_status_condition);
					echo "updated";
	}

	public function process_cancellation_refund_details()
	{
		$get_data = $this->input->get();
		if(isset($get_data['app_reference']) == true && isset($get_data['booking_source']) == true){
			$app_reference = trim($get_data['app_reference']);
			$booking_source = trim($get_data['booking_source']);
			//$passenger_origin = trim($get_data['passenger_origin']);
			//$passenger_status = trim($get_data['passenger_status']);
			$booking_details = $this->flight_model->get_passenger_ticket_info_by_app_reference($app_reference);
			if($booking_details['status'] == SUCCESS_STATUS){
				$booked_user_id = intval($booking_details['data']['booking_details'][0]['created_by_id']);
				$booked_user_details = array();
				$is_agent = false;
				$user_condition[] = array('U.user_id' ,'=', $booked_user_id);
				$booked_user_details = $this->user_model->get_user_details($user_condition);
				if(valid_array($booked_user_details) == true){
					$booked_user_details = $booked_user_details[0];
					if($booked_user_details['user_type'] == B2B_USER){
						$is_agent = true;
						$module = "b2b";
					}
					else
						$module = "b2c";
				}
				$master_booking_details = $this->flight_model->get_booking_details($app_reference, $booking_source, $booking_details['status']);
				$master_booking_details = $this->booking_data_formatter->format_flight_booking_data($master_booking_details, $module);
				$page_data = array();
				$page_data['booking_data'] = $booking_details['data'];
				$page_data['master_booking_details'] = $master_booking_details;
				$page_data['booked_user_details'] =	$booked_user_details;
				$page_data['is_agent'] = 			$is_agent;
				$this->template->view('flight/cancellation_refund_details', $page_data);
			} else {
				redirect(base_url());
			}
		} else {
			redirect(base_url());
		}
	}

}
