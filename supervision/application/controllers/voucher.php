<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Provab
 * @subpackage Bus
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V1
 */
class Voucher extends CI_Controller {
	private $current_module;
	public function __construct()
	{
		parent::__construct();
		$this->load->library('booking_data_formatter');
		$this->load->library('provab_mailer');
		$this->load->library('provab_sms');
		$this->current_module = $this->config->item('current_module');
		$this->load->model('domain_management_model');
		$this->load->model('db_cache_api');
		$this->load->library('utility/notification', 'notification');
		//$this->load->library('provab_pdf');
		
		//we need to activate bus api which are active for current domain and load those libraries
		//$this->output->enable_profiler(TRUE);
	}

	/**
	 *
	 */
	function multivoucher($app_reference, $booking_source, $booking_status)
	{
		$this->load->model('flight_model');
		$page_data["bd"] = $this->flight_model->get_booking_details($app_reference, $booking_source);
		$this->template->view("report/multi_voucher_links", $page_data);
	}
	
	/**
	 *
	 */
	function bus($app_reference, $booking_source='', $booking_status='', $operation='show_voucher',$to_email=0, $fire_email_sms=0, $refund_amount = 0, $cancellation_charges = 0, $to_customer=0)
	{
		//echo 'under working';exit;
		$this->load->model('bus_model');
		if (empty($app_reference) == false) {
			$call_to_show = "partial_cancellation_also"; 
			$booking_details = $this->bus_model->get_booking_details($app_reference, $booking_source, $booking_status, $call_to_show);
			$terms_conditions = $this->custom_db->single_table_records('terms_conditions','*', array('module' =>'bus'));
			if ($booking_details['status'] == SUCCESS_STATUS) {
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_bus_booking_data($booking_details, $this->current_module);
				$page_data['data'] = $assembled_booking_details['data'];
				// debug($assembled_booking_details);exit;
				if(isset($assembled_booking_details['data']['booking_details'][0])){
					//get agent address & logo for b2b voucher
					$domain_address = $this->custom_db->single_table_records ('domain_list','address,domain_logo,phone,domain_name,phone_code',array('origin'=>get_domain_auth_id()));
					$page_data['data']['address'] =$domain_address['data'][0]['address'];
					$page_data['data']['logo'] = $domain_address['data'][0]['domain_logo'];
					$page_data['data']['phone'] = $domain_address['data'][0]['phone'];
					$page_data['data']['phone_code'] = $domain_address['data'][0]['phone_code'];
					$page_data['data']['domainname'] = $domain_address['data'][0]['domain_name'];
					$page_data['data']['terms_conditions'] = '';
					if($terms_conditions['status'] == SUCCESS_STATUS){
						if($operation=="send_credit_note")
							$page_data['data']['terms_conditions'] = $terms_conditions['data'][0]['credit_note'];
						else
							$page_data['data']['terms_conditions'] = $terms_conditions['data'][0]['description'];
					}
				}
				$info = $page_data["data"]["booking_details"][0];
				$lead_phone = $info["lead_pax_phone_number"];
				$email = $info['email'];
				$lead_email = $info["lead_pax_email"];
				switch ($operation) {
					case 'show_voucher' : 
						$get_agent_info = $this->user_model->get_agent_info($assembled_booking_details['data']['booking_details'][0]['created_by_id']);						
						// debug($get_agent_info);exit;
						if(!empty($get_agent_info)){
							$page_data['data']['address'] = $get_agent_info[0]['address'];
							if(empty($get_agent_info[0]['image']) == false){
								$page_data['data']['logo'] = $get_agent_info[0]['logo'];
							}
							else{
								$page_data['data']['logo'] = $page_data['data']['booking_details_app'][$app_reference]['domain_logo'];
							}
							$page_data['data']['phone_code'] = $get_agent_info[0]['phone_code'];
							$page_data['data']['phone'] = $get_agent_info[0]['phone'];
							$page_data['data']['domainname'] = $get_agent_info[0]['agency_name'];
						}
						$this->template->view('voucher/bus_voucher', $page_data);
					break;
					case 'show_pdf' :
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$get_view=$this->template->isolated_view('voucher/bus_pdf', $page_data);
						$create_pdf->create_pdf($get_view,'show');					
						break;
						
					case 'email_voucher' :
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$mail_template = $this->template->isolated_view('voucher/bus_voucher', $page_data);
						//$pdf = $create_pdf->create_pdf($mail_template,'');
						$this->provab_mailer->send_mail($to_email, domain_name().' - Bus Ticket',$mail_template ,$pdf);
						break;
					case 'send_credit_note' :
					$get_agent_info = $this->user_model->get_agent_info($assembled_booking_details['data']['booking_details'][0]['created_by_id']);						
						// debug($get_agent_info);exit;
						if(!empty($get_agent_info)){
							$page_data['data']['address'] = $get_agent_info[0]['address'];
							if(empty($get_agent_info[0]['image']) == false){
								$page_data['data']['logo'] = $get_agent_info[0]['logo'];
							}
							else{
								$page_data['data']['logo'] = $page_data['data']['booking_details_app'][$app_reference]['domain_logo'];
							}
							$page_data['data']['phone'] = $get_agent_info[0]['phone'];
							$page_data['data']['domainname'] = $get_agent_info[0]['agency_name'];
						}
					$domain_address = $this->custom_db->single_table_records ( 'domain_list','address,domain_logo,domain_name,phone',array('origin'=>get_domain_auth_id()));
					$page_data['admin_details']['address'] =$domain_address['data'][0]['address'];
					$page_data['admin_details']['logo'] = $domain_address['data'][0]['domain_logo'];
					$page_data['admin_details']['phone'] =$domain_address['data'][0]['phone'];
					$page_data['admin_details']['domainname'] =$domain_address['data'][0]['domain_name'];
						$page_data["refund_amount"] = $refund_amount;
						$page_data["cancel_charge"] = $cancellation_charges;
						$page_data["to_customer"] = $to_customer;
						$page_data["cancellation_details"] = $this->custom_db->single_table_records('bus_cancellation_details','*',array('app_reference'=>$app_reference));
						$this->template->view('voucher/bus_credit_invoice', $page_data);
						$mail_template = $this->template->isolated_view('voucher/bus_credit_invoice', $page_data);
						$phone = $GLOBALS['CI']->entity_phone;
						if((empty($email)==false) && ($fire_email_sms > 0)){
							if($to_customer)
							{
								$this->provab_sms->send_msg($lead_phone, $page_data, "594355");
								$this->provab_mailer->send_mail($lead_email, domain_name().' - Bus Cancellation Credit Note',	$mail_template);
							}
							else{
								$phone = $page_data['data']['phone'];
								$this->provab_mailer->send_mail($email, domain_name().' - Bus Cancellation Credit Note',	$mail_template);
								//$this->provab_sms->send_msg($phone, $page_data, "594355");
							}
							//$this->provab_sms->fire_sms_to_ccs($page_data, "594355");
						}
						//redirect('bus/cancellation_details/' . $app_reference . '/' . $booking_source);
						break;
				}
			} else {
				redirect('security/log_event?event=Invalid AppReference');
			}
		} else {
			redirect('security/log_event?event=Invalid AppReference');
		}
	}

	

 
	/**
	 *
	 */
	function hotel($app_reference, $booking_source='', $booking_status='', $operation='show_voucher',$email='')
	{
		$this->load->model('hotel_model');
		if (empty($app_reference) == false) {
			$booking_details = $this->hotel_model->get_booking_details($app_reference, $booking_source, $booking_status);
			$terms_conditions = $this->custom_db->single_table_records('terms_conditions','credit_note', array('module' =>'hotel'));
			//debug($booking_details)
			if ($booking_details['status'] == SUCCESS_STATUS) {
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_hotel_booking_data($booking_details, $this->current_module);
				$page_data['data'] = $assembled_booking_details['data'];
				if(isset($assembled_booking_details['data']['booking_details'][0])){
					//get agent address & logo for b2b voucher
				
					$domain_address = $this->custom_db->single_table_records ('domain_list','address,domain_logo,phone,domain_name,phone_code',array('origin'=>get_domain_auth_id()));
					$page_data['data']['address'] =$domain_address['data'][0]['address'];
					$page_data['data']['logo'] = $domain_address['data'][0]['domain_logo'];
					$page_data['data']['phone'] = $domain_address['data'][0]['phone'];
					$page_data['data']['domainname'] = $domain_address['data'][0]['domain_name'];

					if($assembled_booking_details['data']['booking_details'][0]['created_by_id'] > 0){
						$get_agent_info = $this->user_model->get_agent_info($assembled_booking_details['data']['booking_details'][0]['created_by_id']);
						if(!empty($get_agent_info)){
							$page_data['data']['address'] = $get_agent_info[0]['address'];
							$page_data['data']['logo'] = $get_agent_info[0]['logo'];
							$page_data['data']['phone'] = $get_agent_info[0]['phone'];
							$page_data['data']['domainname'] = $get_agent_info[0]['agency_name'];

						}
					}
					$page_data['data']['terms_conditions'] = '';
					if($terms_conditions['status'] == SUCCESS_STATUS){
						$page_data['data']['terms_conditions'] = $terms_conditions['data'][0]['credit_note'];
					}
				
				}
				//debug($page_data);exit;
				switch ($operation) {
					case 'show_voucher' : $this->template->view('voucher/hotel_voucher', $page_data);
					break;
					case 'show_pdf' :						
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$get_view=$this->template->isolated_view('voucher/hotel_pdf', $page_data);						
						$create_pdf->create_pdf($get_view,'show');
						
					break;
					case 'email_voucher' :
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$mail_template = $this->template->isolated_view('voucher/hotel_pdf', $page_data);
						//$pdf = $create_pdf->create_pdf($mail_template,'');
						$this->provab_mailer->send_mail($email, domain_name().' - Hotel Ticket',$mail_template ,$pdf);
						break;
				}
			}
		}
	}
	/**
	 *Sightseeing Voucher
	 */
	function sightseeing($app_reference, $booking_source='', $booking_status='', $operation='show_voucher',$email='')
	{	
		
		$this->load->model('sightseeing_model');
		
		if (empty($app_reference) == false) {
			$booking_details = $this->sightseeing_model->get_booking_details($app_reference, $booking_source, $booking_status);
			$terms_conditions = $this->custom_db->single_table_records('terms_conditions','description', array('module' =>'activity'));
			
			if ($booking_details['status'] == SUCCESS_STATUS) {
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_sightseeing_booking_data($booking_details, $this->current_module);

				$page_data['data'] = $assembled_booking_details['data'];
				if(isset($assembled_booking_details['data']['booking_details'][0])){
					//get agent address & logo for b2b voucher
				
					$domain_address = $this->custom_db->single_table_records ('domain_list','address,domain_logo,phone,domain_name',array('origin'=>get_domain_auth_id()));
					$page_data['data']['address'] =$domain_address['data'][0]['address'];
					$page_data['data']['logo'] = $domain_address['data'][0]['domain_logo'];
					$page_data['data']['phone'] = $domain_address['data'][0]['phone'];
					$page_data['data']['domainname'] = $domain_address['data'][0]['domain_name'];
					if($assembled_booking_details['data']['booking_details'][0]['created_by_id'] > 0){
						$get_agent_info = $this->user_model->get_agent_info($assembled_booking_details['data']['booking_details'][0]['created_by_id']);
						if(!empty($get_agent_info)){
							$page_data['data']['address'] = $get_agent_info[0]['address'];
							$page_data['data']['logo'] = $get_agent_info[0]['logo'];
							$page_data['data']['phone'] = $get_agent_info[0]['phone'];
							$page_data['data']['domainname'] = $get_agent_info[0]['agency_name'];

						}
					}
					$page_data['data']['terms_conditions'] = '';
					if($terms_conditions['status'] == SUCCESS_STATUS){
						$page_data['data']['terms_conditions'] = $terms_conditions['data'][0]['description'];
					}
				
				
				}
				//debug($page_data);exit;
				switch ($operation) {
					case 'show_voucher' : $this->template->view('voucher/sightseeing_voucher', $page_data);
					break;
					case 'show_pdf' :						
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$get_view=$this->template->isolated_view('voucher/sightseeing_pdf', $page_data);						
						$create_pdf->create_pdf($get_view,'show');
						
					break;
					case 'email_voucher' :
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$mail_template = $this->template->isolated_view('voucher/sightseeing_pdf', $page_data);
						//$pdf = $create_pdf->create_pdf($mail_template,'');
						$this->provab_mailer->send_mail($email, domain_name().' - Sightseeing Ticket',$mail_template ,$pdf);
						break;
				}
			}
		}
	}
	/**
	 *Sightseeing Voucher
	 */
	function transfers($app_reference, $booking_source='', $booking_status='', $operation='show_voucher',$email='')
	{	
		
		$this->load->model('transferv1_model');
		
		if (empty($app_reference) == false) {
			$booking_details = $this->transferv1_model->get_booking_details($app_reference, $booking_source, $booking_status);
			$terms_conditions = $this->custom_db->single_table_records('terms_conditions','credit_note', array('module' =>'transfer'));
			
			if ($booking_details['status'] == SUCCESS_STATUS) {
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_transferv1_booking_data($booking_details, $this->current_module);

				$page_data['data'] = $assembled_booking_details['data'];
				if(isset($assembled_booking_details['data']['booking_details'][0])){
					//get agent address & logo for b2b voucher
				
					$domain_address = $this->custom_db->single_table_records ('domain_list','address,domain_logo,phone,domain_name,phone_code',array('origin'=>get_domain_auth_id()));
					$page_data['data']['address'] =$domain_address['data'][0]['address'];
					$page_data['data']['logo'] = $domain_address['data'][0]['domain_logo'];
					$page_data['data']['phone'] = $domain_address['data'][0]['phone'];
					$page_data['data']['phone_code'] = $domain_address['data'][0]['phone_code'];
					$page_data['data']['domainname'] = $domain_address['data'][0]['domain_name'];

					if($assembled_booking_details['data']['booking_details'][0]['created_by_id'] > 0){
						$get_agent_info = $this->user_model->get_agent_info($assembled_booking_details['data']['booking_details'][0]['created_by_id']);
						if(!empty($get_agent_info)){
							$page_data['data']['address'] = $get_agent_info[0]['address'];
							$page_data['data']['logo'] = $get_agent_info[0]['logo'];
							$page_data['data']['phone'] = $get_agent_info[0]['phone'];
							$page_data['data']['domainname'] = $get_agent_info[0]['agency_name'];
						}
					}
					$page_data['data']['terms_conditions'] = '';
					if($terms_conditions['status'] == SUCCESS_STATUS){
						$page_data['data']['terms_conditions'] = $terms_conditions['data'][0]['credit_note'];
					}
				
				
				}
				//debug($page_data);exit;
				switch ($operation) {
					case 'show_voucher' : $this->template->view('voucher/transfer_voucher', $page_data);
					break;
					case 'show_pdf' :						
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$get_view=$this->template->isolated_view('voucher/transfer_pdf', $page_data);						
						$create_pdf->create_pdf($get_view,'show');
						
					break;
					case 'email_voucher' :
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$mail_template = $this->template->isolated_view('voucher/transfer_pdf', $page_data);
						//$pdf = $create_pdf->create_pdf($mail_template,'');
						$this->provab_mailer->send_mail($email, domain_name().' - Transfers Ticket',$mail_template ,$pdf);
						break;
				}
			}
		}
	}
	
	function flight($app_reference, $booking_source='', $booking_status='', $operation='show_voucher',$to_email=0, $fire_email_sms=0, $refund_amount = 0, $cancellation_charges = 0, $to_customer=0)
	{
		$this->load->model('flight_model');
		if (empty($app_reference) == false) {
			$booking_details = $this->flight_model->get_booking_details($app_reference, $booking_source, $booking_status);
			$terms_conditions = $this->custom_db->single_table_records('terms_conditions','*', array('module' =>'flight'));
			
			if ($booking_details['status'] == SUCCESS_STATUS) {
				load_flight_lib(PROVAB_FLIGHT_BOOKING_SOURCE);
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_flight_booking_data($booking_details, $this->current_module, false);
				//$assembled_booking_details = $this->booking_data_formatter->format_flight_booking_data($booking_details, $this->current_module);				
				$page_data['data'] = $assembled_booking_details['data'];
				
			if(isset($assembled_booking_details['data']['booking_details'][0])){
					//get agent address & logo for b2b voucher
					$booking_iti_details = $assembled_booking_details['data']['booking_details'][0]['booking_itinerary_details']; 
					$airlines = array();
					if($booking_source == TRAVELPORT_GDS_BOOKING_SOURCE){
						$airlines = array();
						foreach($booking_iti_details as $it_details){
							$airlines[] = $it_details['airline_code'];
						}
						$airlines = array_unique($airlines);
						// debug($airlines);exit;
					}
					// debug($airlines);exit;
					$domain_address = $this->custom_db->single_table_records ( 'domain_list','address,domain_logo,phone,domain_name',array('origin'=>get_domain_auth_id()));
					$page_data['data']['address'] =$domain_address['data'][0]['address'];
					$page_data['data']['logo'] = $domain_address['data'][0]['domain_logo'];
					$page_data['data']['phone'] =$domain_address['data'][0]['phone'];
					$page_data['data']['phone_code'] ='';
					$page_data['data']['domainname'] =$domain_address['data'][0]['domain_name'];
					if($assembled_booking_details['data']['booking_details'][0]['created_by_id'] > 0){
						$get_agent_info = $this->user_model->get_agent_info($assembled_booking_details['data']['booking_details'][0]['created_by_id']);
						if(!empty($get_agent_info)){
							$page_data['data']['address'] = $get_agent_info[0]['address'];
							$page_data['data']['logo'] = $get_agent_info[0]['logo'];
							$page_data['data']['phone'] = $get_agent_info[0]['phone'];
							$page_data['data']['domainname'] = $get_agent_info[0]['agency_name'];
						} 
					}
					//debug($terms_conditions); exit; 
					$page_data['data']['terms_conditions'] = '';
					if($terms_conditions['status'] == SUCCESS_STATUS){
						$page_data['data']['terms_conditions'] = $terms_conditions['data'][0]['description'];
					}
			
				}
				$info = $page_data["data"]["booking_details"][0];
				$lead_phone = $info["lead_pax_phone_number"];
				$lead_email = $info["lead_pax_email"];
				$email = $info['email'];
				
				$with_fare = 1;
				if(isset($_GET['with_fare'])){
					if($_GET['with_fare'] !=1 ){
						$with_fare = 0;
					}
				}
				$page_data['with_fare'] = $with_fare;		
				switch ($operation) {
					case 'show_voucher' : 
					if($booking_source == TRAVELPORT_GDS_BOOKING_SOURCE && count($airlines) > 1){
						$this->template->view('voucher/flight_voucher_GDS', $page_data);
					}
					else{
						$this->template->view('voucher/flight_voucher', $page_data);
					}
					break;
					case 'show_pdf' :
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						if($booking_source == TRAVELPORT_GDS_BOOKING_SOURCE && count($airlines) > 1){
							$get_view=$this->template->isolated_view('voucher/flight_pdf_GDS', $page_data);
						}
						else{
							$get_view=$this->template->isolated_view('voucher/flight_pdf', $page_data);
						}
						// $get_view=$this->template->isolated_view('voucher/flight_pdf', $page_data);
						//debug($get_view);exit;
						$create_pdf->create_pdf($get_view,'show');
						break;
				   case 'email_voucher':
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$mail_template = $this->template->isolated_view('voucher/flight_pdf', $page_data);
						//$pdf = $create_pdf->create_pdf($mail_template,'');
						$this->provab_mailer->send_mail($to_email, domain_name().' - Flight Ticket',$mail_template ,$pdf);
						break;
				   case 'send_credit_note' :
					$domain_address = $this->custom_db->single_table_records ( 'domain_list','address,domain_logo,domain_name,phone',array('origin'=>get_domain_auth_id()));
					$page_data['admin_details']['address'] =$domain_address['data'][0]['address'];
					$page_data['admin_details']['logo'] = $domain_address['data'][0]['domain_logo'];
					$page_data['admin_details']['phone'] =$domain_address['data'][0]['phone'];
					$page_data['admin_details']['domainname'] =$domain_address['data'][0]['domain_name'];
						$page_data["refund_amount"] = $refund_amount;
						$page_data["cancel_charge"] = $cancellation_charges;
						$page_data["to_customer"] = $to_customer;

						$page_data["cancellation_details"] = $this->custom_db->single_table_records('flight_cancellation_details','*',array('passenger_fk'=>$_GET["passenger_origin"]));
						$page_data['data']['terms_conditions'] = $terms_conditions['data'][0]['credit_note'];
						$this->template->view('voucher/flight_credit_invoice', $page_data);
						$mail_template = $this->template->isolated_view('voucher/flight_credit_invoice', $page_data);
						if((empty($email)==false) && ($fire_email_sms > 0)){
							if($to_customer)
							{
								$this->provab_sms->send_msg($lead_phone, $page_data, "594355");
								$this->provab_mailer->send_mail($lead_email, domain_name().' - Flight Cancellation Credit Note',	$mail_template);
							}
							else{
								$this->provab_mailer->send_mail($email, domain_name().' - Flight Cancellation Credit Note',	$mail_template);
								$this->provab_sms->send_msg($phone, $page_data, "594355");
							}
							$this->provab_sms->fire_sms_to_ccs($page_data, "594355"); 
						}
						//redirect('bus/cancellation_details/' . $app_reference . '/' . $booking_source);
						break;	
				}
			}
		}
	}
	function b2c_flight_voucher($app_reference, $booking_source='', $booking_status='', $operation='show_voucher',$email='')
	{
		$this->load->model('flight_model');
		if (empty($app_reference) == false) {
			$booking_details = $this->flight_model->get_booking_details($app_reference, $booking_source, $booking_status);
			$terms_conditions = $this->custom_db->single_table_records('terms_conditions','description', array('module' =>'flight'));
			if ($booking_details['status'] == SUCCESS_STATUS) {
				load_flight_lib(PROVAB_FLIGHT_BOOKING_SOURCE);
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_flight_booking_data($booking_details, 'b2c', false);				
				
				$page_data['data'] = $assembled_booking_details['data'];
				
			if(isset($assembled_booking_details['data']['booking_details'][0])){
					//get agent address & logo for b2b voucher
					
					$domain_address = $this->custom_db->single_table_records ( 'domain_list','address,domain_logo,domain_name,phone,phone_code',array('origin'=>get_domain_auth_id()));
					$page_data['data']['address'] =$domain_address['data'][0]['address'];
					$page_data['data']['logo'] = $domain_address['data'][0]['domain_logo'];
					$page_data['data']['phone_code'] =$domain_address['data'][0]['phone_code'];
					$page_data['data']['phone'] =$domain_address['data'][0]['phone'];
					$page_data['data']['domainname'] =$domain_address['data'][0]['domain_name'];
					// debug($assembled_booking_details);exit;
					// if($assembled_booking_details['data']['booking_details'][0]['created_by_id'] > 0){
					// 	$get_agent_info = $this->user_model->get_agent_info($assembled_booking_details['data']['booking_details'][0]['created_by_id']);
					// 	// debug($get_agent_info);exit;
					// 	if(!empty($get_agent_info)){
					// 	$page_data['data']['address'] = $get_agent_info[0]['address'];
					// 	$page_data['data']['logo'] = $get_agent_info[0]['logo'];
					// 	}

					// }
					$page_data['data']['terms_conditions'] = '';
					if($terms_conditions['status'] == SUCCESS_STATUS){
						$page_data['data']['terms_conditions'] = $terms_conditions['data'][0]['description'];
					}
			
				}
				// debug($page_data);exit;
				switch ($operation) {
					case 'show_voucher' : $this->template->view('voucher/flight_voucher', $page_data);
					break;
					case 'show_pdf' :
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$get_view=$this->template->isolated_view('voucher/flight_pdf', $page_data);
						$create_pdf->create_pdf($get_view,'show');
						break;
				   case 'email_voucher':
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$mail_template = $this->template->isolated_view('voucher/flight_pdf', $page_data);
						//$pdf = $create_pdf->create_pdf($mail_template,'');
						$this->provab_mailer->send_mail($email, domain_name().' - Flight Ticket',$mail_template ,$pdf);
						break;
				}
			}
		}
	}
	function b2b_flight_voucher($app_reference, $booking_source='', $booking_status='', $operation='show_voucher',$email='')
	{
		$this->load->model('flight_model');
		if (empty($app_reference) == false) {
			$booking_details = $this->flight_model->get_booking_details($app_reference, $booking_source, $booking_status);
			$terms_conditions = $this->custom_db->single_table_records('terms_conditions','description', array('module' =>'flight'));
			load_flight_lib(PROVAB_FLIGHT_BOOKING_SOURCE);
			//Assemble Booking Data
			$assembled_booking_details = $this->booking_data_formatter->format_flight_booking_data($booking_details, $this->current_module);				
			// debug($assembled_booking_details);exit;
			$page_data['data'] = $assembled_booking_details['data'];
				
			if(isset($assembled_booking_details['data']['booking_details'][0])){
					//get agent address & logo for b2b voucher
					
					$domain_address = $this->custom_db->single_table_records ( 'domain_list','address,domain_logo,domain_name,phone,phone_code',array('origin'=>get_domain_auth_id()));
					$page_data['data']['address'] =$domain_address['data'][0]['address'];
					$page_data['data']['logo'] = $domain_address['data'][0]['domain_logo'];
					$page_data['data']['phone'] =$domain_address['data'][0]['phone'];
					$page_data['data']['phone_code'] =$domain_address['data'][0]['phone_code'];
					$page_data['data']['domainname'] =$domain_address['data'][0]['domain_name'];
					// if($assembled_booking_details['data']['booking_details'][0]['created_by_id'] > 0){
					// 	$get_agent_info = $this->user_model->get_agent_info($assembled_booking_details['data']['booking_details'][0]['created_by_id']);
					// 	// debug($get_agent_info);exit;
					// 	if(!empty($get_agent_info)){
					// 	$page_data['data']['address'] = $get_agent_info[0]['address'];
					// 	$page_data['data']['logo'] = $get_agent_info[0]['logo'];
					// 	$page_data['data']['phone'] = $get_agent_info[0]['phone'];
					// 	$page_data['data']['domainname'] = $get_agent_info[0]['agency_name'];
					// 	}
					// }
					$page_data['data']['terms_conditions'] = '';
					if($terms_conditions['status'] == SUCCESS_STATUS){
						$page_data['data']['terms_conditions'] = $terms_conditions['data'][0]['description'];
					}
			
				}
				switch ($operation) {
					case 'show_voucher' : $this->template->view('voucher/flight_voucher', $page_data);
					break;
					case 'show_pdf' :
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$get_view=$this->template->isolated_view('voucher/flight_pdf', $page_data);
						$create_pdf->create_pdf($get_view,'show');
						break;
				   case 'email_voucher':
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$mail_template = $this->template->isolated_view('voucher/flight_pdf', $page_data);
						//$pdf = $create_pdf->create_pdf($mail_template,'');
						$this->provab_mailer->send_mail($email, domain_name().' - Flight Ticket',$mail_template ,$pdf);
						break;
				}
		}
	}
	function b2c_hotel_voucher($app_reference, $booking_source='', $booking_status='', $operation='show_voucher',$email='')
	{
		$this->load->model('hotel_model');
		if (empty($app_reference) == false) {
			$booking_details = $this->hotel_model->get_booking_details($app_reference, $booking_source, $booking_status);
			$terms_conditions = $this->custom_db->single_table_records('terms_conditions','description', array('module' =>'hotel'));
					//echo $this->db->last_query();exit;
			if ($booking_details['status'] == SUCCESS_STATUS) {
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_hotel_booking_data($booking_details, 'b2c');
				$page_data['data'] = $assembled_booking_details['data'];
				if(isset($assembled_booking_details['data']['booking_details'][0])){
					//get agent address & logo for b2b voucher
				
					$domain_address = $this->custom_db->single_table_records ('domain_list','address,domain_logo,domain_name,phone,phone_code',array('origin'=>get_domain_auth_id()));
					$page_data['data']['address'] =$domain_address['data'][0]['address'];
					$page_data['data']['logo'] = $domain_address['data'][0]['domain_logo'];

					$page_data['data']['phone'] = $domain_address['data'][0]['phone'];
					$page_data['data']['phone_code'] = $domain_address['data'][0]['phone_code'];
					
					$page_data['data']['domainname'] = $domain_address['data'][0]['domain_name'];
					

					// if($assembled_booking_details['data']['booking_details'][0]['created_by_id'] > 0){
					// 	$get_agent_info = $this->user_model->get_agent_info($assembled_booking_details['data']['booking_details'][0]['created_by_id']);
					// 	if(!empty($get_agent_info)){
					// 		$page_data['data']['address'] = $get_agent_info[0]['address'];
					// 		$page_data['data']['logo'] = $get_agent_info[0]['logo'];
					// 		$page_data['data']['phone'] = $get_agent_info[0]['phone'];
					// 		$page_data['data']['domainname'] = $get_agent_info[0]['domain_name'];

					// 	}
				}$page_data['data']['terms_conditions'] = '';
					if($terms_conditions['status'] == SUCCESS_STATUS){
						$page_data['data']['terms_conditions'] = $terms_conditions['data'][0]['description'];
					}	
			}
			switch ($operation) {
				case 'show_voucher' : $this->template->view('voucher/hotel_voucher', $page_data);
				break;
				case 'show_pdf' :						
					$this->load->library('provab_pdf');
					$create_pdf = new Provab_Pdf();
					$get_view=$this->template->isolated_view('voucher/hotel_pdf', $page_data);						
					$create_pdf->create_pdf($get_view,'show');
					
				break;
				case 'email_voucher' :
					$this->load->library('provab_pdf');
					$create_pdf = new Provab_Pdf();
					$mail_template = $this->template->isolated_view('voucher/hotel_pdf', $page_data);
					//$pdf = $create_pdf->create_pdf($mail_template,'');
					$this->provab_mailer->send_mail($email, domain_name().' - Hotel Ticket',$mail_template ,$pdf);
					break;
			}
		}
	}

	function b2b_hotel_voucher($app_reference, $booking_source='', $booking_status='', $operation='show_voucher',$email='')
	{
		$this->load->model('hotel_model');
		if (empty($app_reference) == false) {
			$booking_details = $this->hotel_model->get_booking_details($app_reference, $booking_source, $booking_status);
			if ($booking_details['status'] == SUCCESS_STATUS) {
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_hotel_booking_data($booking_details, $this->current_module);
				$page_data['data'] = $assembled_booking_details['data'];
				if(isset($assembled_booking_details['data']['booking_details'][0])){
					//get agent address & logo for b2b voucher
				
					$domain_address = $this->custom_db->single_table_records ('domain_list','address,domain_logo,phone,domain_name',array('origin'=>get_domain_auth_id()));
					$page_data['data']['address'] =$domain_address['data'][0]['address'];
					$page_data['data']['logo'] = $domain_address['data'][0]['domain_logo'];
					$page_data['data']['phone'] = $domain_address['data'][0]['phone'];
					$page_data['data']['domainname'] = $domain_address['data'][0]['domain_name'];

					// if($assembled_booking_details['data']['booking_details'][0]['created_by_id'] > 0){
					// 	$get_agent_info = $this->user_model->get_agent_info($assembled_booking_details['data']['booking_details'][0]['created_by_id']);
					// 	if(!empty($get_agent_info)){
					// 		$page_data['data']['address'] = $get_agent_info[0]['address'];
					// 		$page_data['data']['logo'] = $get_agent_info[0]['logo'];
					// 		$page_data['data']['phone'] = $get_agent_info[0]['phone'];
					// 		$page_data['data']['domainname'] = $get_agent_info[0]['agency_name'];

					// 	}
					// }
				
				}
				switch ($operation) {
					case 'show_voucher' : $this->template->view('voucher/hotel_voucher', $page_data);
					break;
					case 'show_pdf' :						
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$get_view=$this->template->isolated_view('voucher/hotel_pdf', $page_data);						
						$create_pdf->create_pdf($get_view,'show');
						
					break;
					case 'email_voucher' :
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$mail_template = $this->template->isolated_view('voucher/hotel_pdf', $page_data);
						//$pdf = $create_pdf->create_pdf($mail_template,'');
						$this->provab_mailer->send_mail($email, domain_name().' - Hotel Ticket',$mail_template ,$pdf);
						break;
				}
			}
		}
	}
	function b2c_bus_voucher($app_reference, $booking_source='', $booking_status='', $operation='show_voucher',$email='')
	{
		//echo 'under working';exit;
		$this->load->model('bus_model');
		if (empty($app_reference) == false) {
			$booking_details = $this->bus_model->get_booking_details($app_reference, $booking_source, $booking_status);
			if ($booking_details['status'] == SUCCESS_STATUS) {
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_bus_booking_data($booking_details, 'b2c');
				$page_data['data'] = $assembled_booking_details['data'];
				
				if(isset($assembled_booking_details['data']['booking_details'][0])){
					//get agent address & logo for b2b voucher
					$domain_address = $this->custom_db->single_table_records ( 'domain_list','address,domain_logo,phone_code',array('origin'=>get_domain_auth_id()));
					//print_r($domain_address);exit;//
					$page_data['data']['address'] =$domain_address['data'][0]['address'];
					$page_data['data']['logo'] = $domain_address['data'][0]['domain_logo'];
					$page_data['data']['phone_code'] =$domain_address['data'][0]['phone_code'];
					// if($assembled_booking_details['data']['booking_details'][0]['created_by_id'] > 0){
					// 	$get_agent_info = $this->user_model->get_agent_info($assembled_booking_details['data']['booking_details'][0]['created_by_id']);
					// 	if(!empty($get_agent_info)){
					// 		$page_data['data']['address'] = $get_agent_info[0]['address'];
					// 		$page_data['data']['logo'] = $get_agent_info[0]['logo'];
					// 	}
					// }
				
				}
				switch ($operation) {
					case 'show_voucher' : $this->template->view('voucher/bus_voucher', $page_data);
					break;
					case 'show_pdf' :
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$get_view=$this->template->isolated_view('voucher/bus_pdf', $page_data);
						$create_pdf->create_pdf($get_view,'show');					
						break;
						
					case 'email_voucher' :
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$mail_template = $this->template->isolated_view('voucher/bus_pdf', $page_data);
						//$pdf = $create_pdf->create_pdf($mail_template,'');
						$this->provab_mailer->send_mail($email, domain_name().' - Bus Ticket',$mail_template ,$pdf);
						break;
				}
			} else {
				redirect('security/log_event?event=Invalid AppReference');
			}
		} else {
			redirect('security/log_event?event=Invalid AppReference');
		}
	}
	
	function b2b_bus_voucher($app_reference, $booking_source='', $booking_status='', $operation='show_voucher',$email='')
	{
		//echo 'under working';exit;
		$this->load->model('bus_model');
		if (empty($app_reference) == false) {
			$booking_details = $this->bus_model->get_booking_details($app_reference, $booking_source, $booking_status);
			if ($booking_details['status'] == SUCCESS_STATUS) {
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_bus_booking_data($booking_details, $this->current_module);
				$page_data['data'] = $assembled_booking_details['data'];
				
				if(isset($assembled_booking_details['data']['booking_details'][0])){
					//get agent address & logo for b2b voucher
					$domain_address = $this->custom_db->single_table_records ( 'domain_list','address,domain_logo',array('origin'=>get_domain_auth_id()));
					$page_data['data']['address'] =$domain_address['data'][0]['address'];
					$page_data['data']['logo'] = $domain_address['data'][0]['domain_logo'];
					// if($assembled_booking_details['data']['booking_details'][0]['created_by_id'] > 0){
					// 	$get_agent_info = $this->user_model->get_agent_info($assembled_booking_details['data']['booking_details'][0]['created_by_id']);
					// 	if(!empty($get_agent_info)){
					// 		$page_data['data']['address'] = $get_agent_info[0]['address'];
					// 		$page_data['data']['logo'] = $get_agent_info[0]['logo'];
					// 	}
					// }
				
				}
				switch ($operation) {
					case 'show_voucher' : $this->template->view('voucher/bus_voucher', $page_data);
					break;
					case 'show_pdf' :
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$get_view=$this->template->isolated_view('voucher/bus_pdf', $page_data);
						$create_pdf->create_pdf($get_view,'show');					
						break;
						
					case 'email_voucher' :
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$mail_template = $this->template->isolated_view('voucher/bus_pdf', $page_data);
						//$pdf = $create_pdf->create_pdf($mail_template,'');
						$this->provab_mailer->send_mail($email, domain_name().' - Bus Ticket',$mail_template ,$pdf);
						break;
				}
			} else {
				redirect('security/log_event?event=Invalid AppReference');
			}
		} else {
			redirect('security/log_event?event=Invalid AppReference');
		}
	}
	  /**
     * Car Vocuher
     */
    function car($app_reference, $booking_source = '', $booking_status = '', $operation = 'show_voucher', $email ='') {
        $this->load->model('car_model');
        if (empty($app_reference) == false) {
            $booking_details = $this->car_model->get_booking_details($app_reference, $booking_source, $booking_status);
            // debug($booking_details);exit;
            if ($booking_details['status'] == SUCCESS_STATUS) {
                //Assemble Booking Data
                $assembled_booking_details = $this->booking_data_formatter->format_car_booking_datas($booking_details, 'b2c');
                // debug($assembled_booking_details);exit;
                $page_data['data'] = $assembled_booking_details['data'];
                if (isset($assembled_booking_details['data']['booking_details'][0])) {
                    //get agent address & logo for b2b voucher

                    $domain_address = $this->custom_db->single_table_records('domain_list', 'address,domain_logo', array('origin' => get_domain_auth_id()));
                    $page_data['data']['address'] = $domain_address['data'][0]['address'];
                    $page_data['data']['logo'] = $domain_address['data'][0]['domain_logo'];
                   
                }
                switch ($operation) {
                    case 'show_voucher' : $this->template->view('voucher/car_voucher', $page_data);
                        break;
                    case 'show_pdf' :
                        $this->load->library('provab_pdf');
                        $create_pdf = new Provab_Pdf();
                        $get_view = $this->template->isolated_view('voucher/car_pdf', $page_data);
                        // debug($get_view);exit;
                        $create_pdf->create_pdf($get_view, 'show');

                        break;
                    case 'email_voucher' :
                        $email = $this->load->library('provab_pdf');
                        $email = @$booking_details['data']['booking_details'][0]['email'];
                        $create_pdf = new Provab_Pdf();
                        $mail_template = $this->template->isolated_view('voucher/car_pdf', $page_data);
                        //$pdf = $create_pdf->create_pdf($mail_template, '');
                        $this->provab_mailer->send_mail($email, domain_name() . ' - Car Ticket', $mail_template, $pdf);
                        break;
                }
            }
        }
    }
	function flight_invoice($app_reference, $booking_source='', $booking_status='', $operation='show_voucher')
	{
		
		$this->load->model('flight_model');
		if (empty($app_reference) == false) {
			$data = $this->flight_model->get_booking_details($app_reference, $booking_source, $booking_status);
			// debug($data);exit;
			if ($data['status'] == SUCCESS_STATUS) {
				//depending on booking source we need to convert to view array
				load_flight_lib($data['data']['booking_details']['booking_source']);
				$page_data = $this->flight_lib->parse_voucher_data($data['data']);
				$domain_details = $this->custom_db->single_table_records('domain_list', '*', array('origin' => $page_data['booking_details']['domain_origin']));
				$page_data['domain_details'] = $domain_details['data'][0];
				switch ($operation) {
					case 'show_voucher' : $this->template->view('voucher/flight_invoice', $page_data);
					break;
				}
			}
		}
	}
        
         function flight_invoice_GST($app_reference, $booking_source='', $booking_status='', $module='')
	{
            error_reporting(0);
            $this->load->model('flight_model');
		if (empty($app_reference) == false) {
			$booking_details = $this->flight_model->get_booking_details($app_reference, $booking_source, $booking_status);
			if ($booking_details['status'] == SUCCESS_STATUS) {
				load_flight_lib(PROVAB_FLIGHT_BOOKING_SOURCE);
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_flight_booking_data($booking_details, $this->current_module);				
				$page_data['data'] = $assembled_booking_details['data'];
				
			               if(isset($assembled_booking_details['data']['booking_details'][0])){
					//get agent address & logo for b2b voucher
					$domain_address = $this->custom_db->single_table_records ( 'domain_list','address,domain_logo,domain_name,phone',array('origin'=>get_domain_auth_id()));

					$page_data['admin_details']['address'] =$domain_address['data'][0]['address'];
					$page_data['admin_details']['logo'] = $domain_address['data'][0]['domain_logo'];
					$page_data['admin_details']['phone'] =$domain_address['data'][0]['phone'];
					$page_data['admin_details']['domainname'] =$domain_address['data'][0]['domain_name'];
					
						if($assembled_booking_details['data']['booking_details'][0]['created_by_id'] > 0){
							$get_agent_info = $this->user_model->get_agent_info($assembled_booking_details['data']['booking_details'][0]['created_by_id']);
							
							if(!empty($get_agent_info)){
							$page_data['data']['address'] = $get_agent_info[0]['address'];
							$page_data['data']['logo'] = $get_agent_info[0]['logo'];
							$page_data['data']['phone'] = $get_agent_info[0]['phone'];
							$page_data['data']['domainname'] = $get_agent_info[0]['agency_name'];
							}
						}else{
							$page_data['data']['address'] = $assembled_booking_details['data']['booking_details'][0]['cutomer_address'];
						
							$page_data['data']['phone'] = $assembled_booking_details['data']['booking_details'][0]['lead_pax_phone_number'];
							$page_data['data']['domainname'] = $assembled_booking_details['data']['booking_details'][0]['lead_pax_name'];

							$page_data['data']['domaincountry']= $assembled_booking_details['data']['booking_details'][0]['cutomer_country'];
						}
			        }
                     
                    //debug($page_data);
                    // exit;
                    $page_data['module'] =$module;
                    $this->template->view('voucher/flight_invoice_new', $page_data);
			        }
                                
		}
    }
        
      
    function hotel_invoice_GST($app_reference, $booking_source='', $booking_status='', $module='')
	{
            error_reporting(0);
            $this->load->model('hotel_model');
		if (empty($app_reference) == false) {
			$booking_details = $this->hotel_model->get_booking_details($app_reference, $booking_source, $booking_status);
			if ($booking_details['status'] == SUCCESS_STATUS) {
				load_flight_lib(PROVAB_FLIGHT_BOOKING_SOURCE);
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_hotel_booking_data($booking_details, $this->current_module);				
				$page_data['data'] = $assembled_booking_details['data'];
				
			               if(isset($assembled_booking_details['data']['booking_details'][0])){
					//get agent address & logo for b2b voucher
					$domain_address = $this->custom_db->single_table_records ( 'domain_list','address,domain_logo,domain_name,phone',array('origin'=>get_domain_auth_id()));

					$page_data['admin_details']['address'] =$domain_address['data'][0]['address'];
					$page_data['admin_details']['logo'] = $domain_address['data'][0]['domain_logo'];
					$page_data['admin_details']['phone'] =$domain_address['data'][0]['phone'];
					$page_data['admin_details']['domainname'] =$domain_address['data'][0]['domain_name'];
					
						if($assembled_booking_details['data']['booking_details'][0]['created_by_id'] > 0){
							$get_agent_info = $this->user_model->get_agent_info($assembled_booking_details['data']['booking_details'][0]['created_by_id']);
							
							if(!empty($get_agent_info)){
							$page_data['data']['address'] = $get_agent_info[0]['address'];
							$page_data['data']['logo'] = $get_agent_info[0]['logo'];
							$page_data['data']['phone'] = $get_agent_info[0]['phone'];
							$page_data['data']['domainname'] = $get_agent_info[0]['agency_name'];
							}
						}else{
							$page_data['data']['address'] = $assembled_booking_details['data']['booking_details'][0]['cutomer_address'];
						
							$page_data['data']['phone'] = $assembled_booking_details['data']['booking_details'][0]['lead_pax_phone_number'];
							$page_data['data']['domainname'] = $assembled_booking_details['data']['booking_details'][0]['lead_pax_name'];

							$page_data['data']['domaincountry']= $assembled_booking_details['data']['booking_details'][0]['cutomer_country'];
						}
			        }
                     
                    // debug($page_data);
                    // exit;
                    $page_data['module'] =$module;
                    $this->template->view('voucher/hotel_invoice', $page_data);
			        }
                                
		}
        }   
     function bus_invoice_GST($app_reference, $booking_source='', $booking_status='', $module='')
	{
            error_reporting(0);
            $this->load->model('bus_model');
		if (empty($app_reference) == false) {
			$booking_details = $this->bus_model->get_booking_details($app_reference, $booking_source, $booking_status);
			if ($booking_details['status'] == SUCCESS_STATUS) {
				load_flight_lib(PROVAB_FLIGHT_BOOKING_SOURCE);
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_bus_booking_data($booking_details, $this->current_module);				
				$page_data['data'] = $assembled_booking_details['data'];
				
                if(isset($assembled_booking_details['data']['booking_details'][0])){
					//get agent address & logo for b2b voucher
					$domain_address = $this->custom_db->single_table_records ( 'domain_list','address,domain_logo,domain_name,phone',array('origin'=>get_domain_auth_id()));

					$page_data['admin_details']['address'] =$domain_address['data'][0]['address'];
					$page_data['admin_details']['logo'] = $domain_address['data'][0]['domain_logo'];
					$page_data['admin_details']['phone'] =$domain_address['data'][0]['phone'];
					$page_data['admin_details']['domainname'] =$domain_address['data'][0]['domain_name'];
					
						if($assembled_booking_details['data']['booking_details'][0]['created_by_id'] > 0){
							$get_agent_info = $this->user_model->get_agent_info($assembled_booking_details['data']['booking_details'][0]['created_by_id']);
							
							if(!empty($get_agent_info)){
							$page_data['data']['address'] = $get_agent_info[0]['address'];
							$page_data['data']['logo'] = $get_agent_info[0]['logo'];
							$page_data['data']['phone'] = $get_agent_info[0]['phone'];
							$page_data['data']['domainname'] = $get_agent_info[0]['agency_name'];
							}
						}else{
							$page_data['data']['address'] = $assembled_booking_details['data']['booking_details'][0]['cutomer_address'];
						
							$page_data['data']['phone'] = $assembled_booking_details['data']['booking_details'][0]['lead_pax_phone_number'];
							$page_data['data']['domainname'] = $assembled_booking_details['data']['booking_details'][0]['lead_pax_name'];

							$page_data['data']['domaincountry']= $assembled_booking_details['data']['booking_details'][0]['cutomer_country'];
						}
			        }
                     
                    // debug($page_data);
                    // exit;
                    $page_data['module'] =$module;
                    $this->template->view('voucher/bus_invoice', $page_data);
			        }
                                
		}
    }  
    function activity_invoice_GST($app_reference, $booking_source='', $booking_status='', $module='')
	{
            error_reporting(0);
            $this->load->model('sightseeing_model');
		if (empty($app_reference) == false) {
			$booking_details = $this->sightseeing_model->get_booking_details($app_reference, $booking_source, $booking_status);
			if ($booking_details['status'] == SUCCESS_STATUS) {
				load_flight_lib(PROVAB_FLIGHT_BOOKING_SOURCE);
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_sightseeing_booking_data($booking_details, $this->current_module);				
				$page_data['data'] = $assembled_booking_details['data'];
				
			               if(isset($assembled_booking_details['data']['booking_details'][0])){
					//get agent address & logo for b2b voucher
					$domain_address = $this->custom_db->single_table_records ( 'domain_list','address,domain_logo,domain_name,phone',array('origin'=>get_domain_auth_id()));

					$page_data['admin_details']['address'] =$domain_address['data'][0]['address'];
					$page_data['admin_details']['logo'] = $domain_address['data'][0]['domain_logo'];
					$page_data['admin_details']['phone'] =$domain_address['data'][0]['phone'];
					$page_data['admin_details']['domainname'] =$domain_address['data'][0]['domain_name'];
					
						if($assembled_booking_details['data']['booking_details'][0]['created_by_id'] > 0){
							$get_agent_info = $this->user_model->get_agent_info($assembled_booking_details['data']['booking_details'][0]['created_by_id']);
							
							if(!empty($get_agent_info)){
							$page_data['data']['address'] = $get_agent_info[0]['address'];
							$page_data['data']['logo'] = $get_agent_info[0]['logo'];
							$page_data['data']['phone'] = $get_agent_info[0]['phone'];
							$page_data['data']['domainname'] = $get_agent_info[0]['agency_name'];
							}
						}else{
							$page_data['data']['address'] = $assembled_booking_details['data']['booking_details'][0]['cutomer_address'];
						
							$page_data['data']['phone'] = $assembled_booking_details['data']['booking_details'][0]['lead_pax_phone_number'];
							$page_data['data']['domainname'] = $assembled_booking_details['data']['booking_details'][0]['lead_pax_name'];

							$page_data['data']['domaincountry']= $assembled_booking_details['data']['booking_details'][0]['cutomer_country'];
						}
			        }
                     
                    // debug($page_data);
                    // exit;
                    $page_data['module'] =$module;
                    $this->template->view('voucher/activity_invoice', $page_data);
			        }
                                
		}
    }
    function transfer_invoice_GST($app_reference, $booking_source='', $booking_status='', $module='')
	{
            error_reporting(0);
        $this->load->model('transferv1_model');
		if (empty($app_reference) == false) {
			$booking_details = $this->transferv1_model->get_booking_details($app_reference, $booking_source, $booking_status);
			if ($booking_details['status'] == SUCCESS_STATUS) {
				load_flight_lib(PROVAB_FLIGHT_BOOKING_SOURCE);
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_transferv1_booking_data($booking_details, $this->current_module);				
				$page_data['data'] = $assembled_booking_details['data'];
				
               if(isset($assembled_booking_details['data']['booking_details'][0])){
					//get agent address & logo for b2b voucher
					$domain_address = $this->custom_db->single_table_records ( 'domain_list','address,domain_logo,domain_name,phone',array('origin'=>get_domain_auth_id()));

					$page_data['admin_details']['address'] =$domain_address['data'][0]['address'];
					$page_data['admin_details']['logo'] = $domain_address['data'][0]['domain_logo'];
					$page_data['admin_details']['phone'] =$domain_address['data'][0]['phone'];
					$page_data['admin_details']['domainname'] =$domain_address['data'][0]['domain_name'];
					
						if($assembled_booking_details['data']['booking_details'][0]['created_by_id'] > 0){
							$get_agent_info = $this->user_model->get_agent_info($assembled_booking_details['data']['booking_details'][0]['created_by_id']);
							
							if(!empty($get_agent_info)){
							$page_data['data']['address'] = $get_agent_info[0]['address'];
							$page_data['data']['logo'] = $get_agent_info[0]['logo'];
							$page_data['data']['phone'] = $get_agent_info[0]['phone'];
							$page_data['data']['domainname'] = $get_agent_info[0]['agency_name'];
							}
						}else{
							$page_data['data']['address'] = $assembled_booking_details['data']['booking_details'][0]['cutomer_address'];
						
							$page_data['data']['phone'] = $assembled_booking_details['data']['booking_details'][0]['lead_pax_phone_number'];
							$page_data['data']['domainname'] = $assembled_booking_details['data']['booking_details'][0]['lead_pax_name'];

							$page_data['data']['domaincountry']= $assembled_booking_details['data']['booking_details'][0]['cutomer_country'];
						}
			        }
                     
                    // debug($page_data);
                    // exit;
                    $page_data['module'] =$module;
                    $this->template->view('voucher/transfer_invoice', $page_data);
			        }
                                
		}
    }
        
	function b2c_sightseeing_voucher($app_reference, $booking_source='', $booking_status='', $operation='show_voucher',$email='')
	{
		$this->load->model('sightseeing_model');
		if (empty($app_reference) == false) {
			$booking_details = $this->sightseeing_model->get_booking_details($app_reference, $booking_source, $booking_status);
			$terms_conditions = $this->custom_db->single_table_records('terms_conditions','description', array('module' =>'activity'));
			
			if ($booking_details['status'] == SUCCESS_STATUS) {
				load_sightseen_lib(PROVAB_SIGHTSEEN_BOOKING_SOURCE);
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_sightseeing_booking_data($booking_details, 'b2c', false);				
				$page_data['data'] = $assembled_booking_details['data'];
				
			if(isset($assembled_booking_details['data']['booking_details'][0])){
					//get agent address & logo for b2b voucher
					
					$domain_address = $this->custom_db->single_table_records ( 'domain_list','address,domain_logo,domain_name,phone,phone_code',array('origin'=>get_domain_auth_id()));
					$page_data['data']['address'] =$domain_address['data'][0]['address'];
					$page_data['data']['logo'] = $domain_address['data'][0]['domain_logo'];
					$page_data['data']['phone'] = $domain_address['data'][0]['phone'];
					$page_data['data']['phone_code'] = $domain_address['data'][0]['phone_code'];
					
					$page_data['data']['domainname'] = $domain_address['data'][0]['domain_name'];

					// if($assembled_booking_details['data']['booking_details'][0]['created_by_id'] > 0){
					// 	$get_agent_info = $this->user_model->get_agent_info($assembled_booking_details['data']['booking_details'][0]['created_by_id']);
					// 	if(!empty($get_agent_info)){
					// 		$page_data['data']['address'] = $get_agent_info[0]['address'];
					// 		$page_data['data']['logo'] = $get_agent_info[0]['logo'];
					// 		$page_data['data']['phone'] = $get_agent_info[0]['phone'];
					// 		$page_data['data']['domainname'] = $get_agent_info[0]['domain_name'];
					// 	}
					// }
					$page_data['data']['terms_conditions'] = '';
					if($terms_conditions['status'] == SUCCESS_STATUS){
						$page_data['data']['terms_conditions'] = $terms_conditions['data'][0]['description'];
					}
			
				}
				//debug($page_data);exit;
				switch ($operation) {
					case 'show_voucher' : $this->template->view('voucher/sightseeing_voucher', $page_data);
					break;
					case 'show_pdf' :
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$get_view=$this->template->isolated_view('voucher/sightseeing_pdf', $page_data);
						$create_pdf->create_pdf($get_view,'show');
						break;
				   case 'email_voucher':
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$mail_template = $this->template->isolated_view('voucher/sightseeing_pdf', $page_data);
						//$pdf = $create_pdf->create_pdf($mail_template,'');
						$this->provab_mailer->send_mail($email, domain_name().' - Sightseeing Ticket',$mail_template ,$pdf);
						break;
				}
			}
		}
	}
	function b2b_sightseeing_voucher($app_reference, $booking_source='', $booking_status='', $operation='show_voucher',$email='')
	{
		$this->load->model('sightseeing_model');
		if (empty($app_reference) == false) {
			$booking_details = $this->sightseeing_model->get_booking_details($app_reference, $booking_source, $booking_status);
			$terms_conditions = $this->custom_db->single_table_records('terms_conditions','description', array('module' =>'activity'));
			if ($booking_details['status'] == SUCCESS_STATUS) {
				load_sightseen_lib(PROVAB_SIGHTSEEN_BOOKING_SOURCE);
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_sightseeing_booking_data($booking_details,'b2b');				
				$page_data['data'] = $assembled_booking_details['data'];
				
			if(isset($assembled_booking_details['data']['booking_details'][0])){
					//get agent address & logo for b2b voucher
					
					$domain_address = $this->custom_db->single_table_records ( 'domain_list','address,domain_logo,phone,domain_name,phone_code',array('origin'=>get_domain_auth_id()));
					$page_data['data']['address'] =$domain_address['data'][0]['address'];
					$page_data['data']['logo'] = $domain_address['data'][0]['domain_logo'];
					$page_data['data']['phone'] = $domain_address['data'][0]['phone'];
					$page_data['data']['phone_code'] = $domain_address['data'][0]['phone_code'];
					$page_data['data']['domainname'] = $domain_address['data'][0]['domain_name'];
					// if($assembled_booking_details['data']['booking_details'][0]['created_by_id'] > 0){
					// 	$get_agent_info = $this->user_model->get_agent_info($assembled_booking_details['data']['booking_details'][0]['created_by_id']);
					// 	if(!empty($get_agent_info)){
					// 	$page_data['data']['address'] = $get_agent_info[0]['address'];
					// 	$page_data['data']['logo'] = $get_agent_info[0]['logo'];
					// 	$page_data['data']['phone'] = $get_agent_info[0]['phone'];
					// 	$page_data['data']['domainname'] = $get_agent_info[0]['agency_name'];
					// 	}
					// }
					$page_data['data']['terms_conditions'] = '';
					if($terms_conditions['status'] == SUCCESS_STATUS){
						$page_data['data']['terms_conditions'] = $terms_conditions['data'][0]['description'];
					}
			
				}
				switch ($operation) {
					case 'show_voucher' : $this->template->view('voucher/sightseeing_voucher', $page_data);
					break;
					case 'show_pdf' :
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$get_view=$this->template->isolated_view('voucher/sightseeing_pdf', $page_data);
						$create_pdf->create_pdf($get_view,'show');
						break;
				   case 'email_voucher':
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$mail_template = $this->template->isolated_view('voucher/sightseeing_pdf', $page_data);
						//$pdf = $create_pdf->create_pdf($mail_template,'');
						$this->provab_mailer->send_mail($email, domain_name().' - Sightseeing Ticket',$mail_template ,$pdf);
						break;
				}
			}
		}
	}
	function b2c_transfers_voucher($app_reference, $booking_source='', $booking_status='', $operation='show_voucher',$email='')
	{
		$this->load->model('transferv1_model');
		if (empty($app_reference) == false) {
			$booking_details = $this->transferv1_model->get_booking_details($app_reference, $booking_source, $booking_status);
			$terms_conditions = $this->custom_db->single_table_records('terms_conditions','description', array('module' =>'transfers'));
			if ($booking_details['status'] == SUCCESS_STATUS) {
				load_transferv1_lib(PROVAB_TRANSFERV1_BOOKING_SOURCE);
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_transferv1_booking_data($booking_details, 'b2c', false);				
				$page_data['data'] = $assembled_booking_details['data'];
				
			if(isset($assembled_booking_details['data']['booking_details'][0])){
					//get agent address & logo for b2b voucher
					
					$domain_address = $this->custom_db->single_table_records ( 'domain_list','address,domain_logo,phone,domain_name,phone_code',array('origin'=>get_domain_auth_id()));
					$page_data['data']['address'] =$domain_address['data'][0]['address'];
					$page_data['data']['logo'] = $domain_address['data'][0]['domain_logo'];
					$page_data['data']['phone'] = $domain_address['data'][0]['phone'];
					$page_data['data']['phone_code'] = $domain_address['data'][0]['phone_code'];
					
					$page_data['data']['domainname'] = $domain_address['data'][0]['domain_name'];

					// if($assembled_booking_details['data']['booking_details'][0]['created_by_id'] > 0){
					// 	$get_agent_info = $this->user_model->get_agent_info($assembled_booking_details['data']['booking_details'][0]['created_by_id']);
					// 	if(!empty($get_agent_info)){
					// 		$page_data['data']['address'] = $get_agent_info[0]['address'];
					// 		$page_data['data']['logo'] = $get_agent_info[0]['logo'];
					// 		$page_data['data']['phone'] = $get_agent_info[0]['phone'];
					// 		$page_data['data']['domainname'] = $get_agent_info[0]['domain_name'];

					// 	}
					// }
					$page_data['data']['terms_conditions'] = '';
					if($terms_conditions['status'] == SUCCESS_STATUS){
						$page_data['data']['terms_conditions'] = $terms_conditions['data'][0]['description'];
					}
			
				}
				//debug($page_data);exit;
				switch ($operation) {
					case 'show_voucher' : $this->template->view('voucher/transfer_voucher', $page_data);
					break;
					case 'show_pdf' :
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$get_view=$this->template->isolated_view('voucher/transfer_pdf', $page_data);
						$create_pdf->create_pdf($get_view,'show');
						break;
				   case 'email_voucher':
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$mail_template = $this->template->isolated_view('voucher/transfer_pdf', $page_data);
						//$pdf = $create_pdf->create_pdf($mail_template,'');
						$this->provab_mailer->send_mail($email, domain_name().' - Transfers Ticket',$mail_template ,$pdf);
						break;
				}
			}
		}
	}
	function b2b_transfers_voucher($app_reference, $booking_source='', $booking_status='', $operation='show_voucher',$email='')
	{
		$this->load->model('transferv1_model');
		if (empty($app_reference) == false) {
			$booking_details = $this->transferv1_model->get_booking_details($app_reference, $booking_source, $booking_status);
			$terms_conditions = $this->custom_db->single_table_records('terms_conditions','description', array('module' =>'transfers'));
			if ($booking_details['status'] == SUCCESS_STATUS) {
				load_transferv1_lib(PROVAB_TRANSFERV1_BOOKING_SOURCE);
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_transferv1_booking_data($booking_details, $this->current_module);				
				$page_data['data'] = $assembled_booking_details['data'];
				
			if(isset($assembled_booking_details['data']['booking_details'][0])){
					//get agent address & logo for b2b voucher
					
					$domain_address = $this->custom_db->single_table_records ( 'domain_list','address,domain_logo,phone,domain_name,phone_code',array('origin'=>get_domain_auth_id()));
					$page_data['data']['address'] =$domain_address['data'][0]['address'];
					$page_data['data']['logo'] = $domain_address['data'][0]['domain_logo'];
					$page_data['data']['phone'] = $domain_address['data'][0]['phone'];
					$page_data['data']['phone_code'] = $domain_address['data'][0]['phone_code'];
					$page_data['data']['domainname'] = $domain_address['data'][0]['domain_name'];

					// if($assembled_booking_details['data']['booking_details'][0]['created_by_id'] > 0){
					// 	$get_agent_info = $this->user_model->get_agent_info($assembled_booking_details['data']['booking_details'][0]['created_by_id']);
					// 	if(!empty($get_agent_info)){
					// 		$page_data['data']['address'] = $get_agent_info[0]['address'];
					// 		$page_data['data']['logo'] = $get_agent_info[0]['logo'];
					// 		$page_data['data']['phone'] = $get_agent_info[0]['phone'];
					// 		$page_data['data']['domainname'] = $get_agent_info[0]['agency_name'];
					// 	}
					// }
					$page_data['data']['terms_conditions'] = '';
					if($terms_conditions['status'] == SUCCESS_STATUS){
						$page_data['data']['terms_conditions'] = $terms_conditions['data'][0]['description'];
					}
			
				}
				switch ($operation) {
					case 'show_voucher' : $this->template->view('voucher/transfer_voucher', $page_data);
					break;
					case 'show_pdf' :
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$get_view=$this->template->isolated_view('voucher/transfer_pdf', $page_data);
						$create_pdf->create_pdf($get_view,'show');
						break;
				   case 'email_voucher':
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$mail_template = $this->template->isolated_view('voucher/transfer_pdf', $page_data);
						//$pdf = $create_pdf->create_pdf($mail_template,'');
						$this->provab_mailer->send_mail($email, domain_name().' - Transfers Ticket',$mail_template ,$pdf);
						break;
				}
			}
		}
	}


	/// Edit Voucher

	function voucher_edit($app_reference, $booking_source='', $booking_status='', $operation='show_voucher',$module='')
	{

		$this->load->model('flight_model');
		$post_data = $this->input->post();
		//debug($post_data);die;
		$new_grand_total = $post_data["grand_total"];
		
		if (empty($app_reference) == false && empty($post_data) ==true) {


			$booking_details = $this->flight_model->get_booking_details($app_reference, $booking_source, $booking_status);
			
			if ($booking_details['status'] == SUCCESS_STATUS) {
				load_flight_lib(PROVAB_FLIGHT_BOOKING_SOURCE);
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_flight_booking_data($booking_details, 'b2b', false);				
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

				switch ($operation) {
					case 'show_voucher' : 
					$page_data['airline_list'] = $this->db_cache_api->get_airline_list($from = array('k' => 'code','v' => 'name'));
					$this->template->view('voucher/edit_flight_voucher', $page_data);
					break;
				}
			}
		}
		if(empty($post_data) == false){
			
			$booking_details = $this->flight_model->get_booking_details($app_reference, $booking_source, $booking_status);
			$temp_bd = $booking_details;

			if ($booking_details['status'] == SUCCESS_STATUS) {
				//debug($booking_details); 
				$all_trans = $booking_details['data']['booking_transaction_details'];
				$booking_transaction_details = $booking_details['data']['booking_transaction_details'][0];
				$assembled_booking_details = $this->booking_data_formatter->format_flight_booking_data($booking_details, 'b2b', false);
				$itinrary_details = $temp_bd["data"]["booking_itinerary_details"];
				//debug();die();
				$trip_type = $assembled_booking_details['data']['booking_details_app'][$app_reference]['trip_type'];
				$is_domestic = $assembled_booking_details['data']['booking_details_app'][$app_reference]['is_domestic'];

				$old_grand_total = $assembled_booking_details['data']['booking_details'][0]['grand_total'];

				$extra_amount = $temp_bd["data"]["booking_details"][0]["extra_amount"];
				$diff_amount =($post_data['grand_total'] - $old_grand_total);
				$extra_amount += $diff_amount;
				
				$admin_markup = $booking_transaction_details['admin_markup'];
				$airline_pnr_FT= $post_data["airline_pnr"][$itinrary_details[0]["origin"]];
				// Start Update Transaction Details 
				$cond['app_reference'] = $app_reference;
				$transaction_details = array(
						'gds_pnr' => $post_data['pnr'],
						'pnr' => $airline_pnr_FT,
						//'admin_markup' => ($admin_markup+$new_admin_markup),
						'status' => $post_data['status'],
						'extra_pax_fare' => $post_data['extra_pax_charge'],
					);
				$this->custom_db->update_record('flight_booking_transaction_details',$transaction_details,$cond);

				$upd_booking_details["extra_amount"] = $extra_amount;
				$upd_booking_details["extra_admin_markup"] = $post_data["extra_admin_markup"];
				$upd_booking_details["extra_gst"] = $post_data["extra_gst"];
				$this->custom_db->update_record('flight_booking_details',$upd_booking_details,$cond);
				
				$pax_no = count($post_data['pax_firstname']);
				$no_trans = count($all_trans);
				// End Update Transaction Details 
				
				// echo $iti_origin;exit;
				foreach($itinrary_details AS $it_key => $it_det)
				{
					//debug($it_det); exit;
					$from_an_ac_arr = explode("(", $post_data["from_ac"][$it_det["origin"]]);
					$from_an = trim($from_an_ac_arr[0]);
					$from_ac = rtrim(trim($from_an_ac_arr[1]), ")");
					
					$to_an_ac_arr = explode("(", $post_data["to_ac"][$it_det["origin"]]);
					$to_an = trim($to_an_ac_arr[0]);
					$to_ac = rtrim(trim($to_an_ac_arr[1]), ")");
					
					$al_dt = $this->custom_db->single_table_records("airline_list", "*", array("code" => $post_data["carrier"][$it_det["origin"]]))["data"][0];

					$it_cond = array("origin" => $it_det["origin"]);
					$it_det_upd["airline_code"] = $post_data["carrier"][$it_det["origin"]];
					$it_det_upd["airline_name"] = $al_dt["name"];
					$it_det_upd["airline_pnr"] = $post_data["airline_pnr"][$it_det["origin"]];
					$it_det_upd["flight_number"] = $post_data["flight_number"][$it_det["origin"]];
					$it_det_upd["from_airport_code"] = $from_ac;
					$it_det_upd["from_airport_name"] = $from_an;
					$it_det_upd["to_airport_code"] = $to_ac;
					$it_det_upd["to_airport_name"] = $to_an;
					$it_det_upd["departure_datetime"] = $post_data["dep_date"][$it_det["origin"]];
					$it_det_upd["arrival_datetime"] = $post_data["arr_date"][$it_det["origin"]];
					
					//debug($it_det_upd); exit;
					$this->custom_db->update_record('flight_booking_itinerary_details', $it_det_upd, $it_cond);
				} 
				//Retake booking data after itinerary update_baggage_data
				$booking_details = $this->flight_model->get_booking_details($app_reference, $booking_source, $booking_status);
				$assembled_booking_details = $this->booking_data_formatter->format_flight_booking_data($booking_details, 'b2b', false);
				//Update pasenfer details
				//debug($post_data); exit;
				foreach($all_trans AS $trans_key => $trans){
				for($i = 0; $i < $pax_no; $i++){
					if($trans_key == 1 && $is_domestic == 1)
						$fd_pref = "r_";
					else
						$fd_pref = "";
					$pax_f_name = $post_data[$fd_pref.'pax_firstname'][$i];
					$pax_l_name = $post_data[$fd_pref.'pax_lastname'][$i];
					$DOB = $post_data[$fd_pref.'DOB'][$i];
					if(isset($post_data[$fd_pref.'passport_No'][$i])){
						$passport_No = $post_data[$fd_pref.'passport_No'][$i];
					}else{
						$passport_No = '';
					}
					if(isset($post_data[$fd_pref.'pass_iss_cont'][$i])){
						$pass_iss_cont = $post_data[$fd_pref.'pass_iss_cont'][$i];
					}else{
						$pass_iss_cont = '';
					}

					if(isset($post_data[$fd_pref.'pass_exp_date'][$i])){
						$pass_exp_date = $post_data['pass_exp_date'][$i];
					}else{
						$pass_exp_date = '2050-12-12';
					}
					
					 
					if(array_key_exists($i, $post_data[$fd_pref.'pax_origin'])){
						$pax_cond['app_reference'] = $app_reference;
						$pax_cond['origin'] = $post_data[$fd_pref.'pax_origin'][$i];
						if(trim($DOB) == "NULL")
							$DOB = NULL;

						$pax_details = array(
							'first_name' => $pax_f_name,
							'last_name' => $pax_l_name,
							'date_of_birth' => $DOB,
							'passport_number' => $passport_No,
							'passport_issuing_country' => $pass_iss_cont,
							'passport_expiry_date' => $pass_exp_date,
							'status' => $post_data['status'],
						);
						//debug($pax_details);
						if($DOB == NULL)
							unset($pax_details["date_of_birth"]);

						$this->custom_db->update_record('flight_booking_passenger_details',$pax_details,$pax_cond);
					}else{
						$trnx_key = $assembled_booking_details['data']['booking_details_app'][$app_reference]['booking_transaction_details'][0]['origin'];
					
						$pax_details = array(
							'app_reference' => $app_reference,
							'flight_booking_transaction_details_fk' => $trnx_key,
							'passenger_type' => 'Adult',
							'is_lead' =>0,
							'title' => 1,
							'first_name' => $pax_f_name,
							'middle_name' => '',
							'last_name' => $pax_l_name,
							'status' => $post_data['status'],
							'date_of_birth' => $DOB,
							'gender' =>1,
							'passenger_nationality'=>$pass_iss_cont,
							'passport_number' => $passport_No,
							'passport_issuing_country' => $pass_iss_cont,
							'passport_expiry_date' => $pass_exp_date,
							'status' => 'BOOKING_CONFIRMED',
							'attributes'=>'',
						);
						
						$new_cust_id = $this->custom_db->insert_record('flight_booking_passenger_details',$pax_details);
					}

					
					$custmore_id = '';
					if($post_data[$fd_pref.'pax_origin'][$i] > 0){
						$custmore_id = $post_data[$fd_pref.'pax_origin'][$i];
					}else{
						$custmore_id = $new_cust_id['insert_id'];
					}
					$ticket_cond['passenger_fk'] = $custmore_id;

					//check baggage details
					$baggage_details = $this->custom_db->single_table_records('flight_booking_baggage_details','*', array('passenger_fk' =>$custmore_id));
					
					if($baggage_details['status'] == true){
						if(empty($post_data[$fd_pref.'ticket_bagg_q'][$i]) == false && empty($post_data[$fd_pref.'ticket_bagg_p'][$i]) == false){
							$bagg_cond['passenger_fk'] = $custmore_id;
							$to = $assembled_booking_details['data']['booking_details_app'][$app_reference]['to_loc'];
							$from = $assembled_booking_details['data']['booking_details_app'][$app_reference]['from_loc'];
							$ticket_bagg_q = $post_data[$fd_pref.'ticket_bagg_q'][$i];
							$ticket_bagg_p = $post_data[$fd_pref.'ticket_bagg_p'][$i];
							if($ticket_bagg_p == ''){
								$ticket_bagg_p = 0;
							}
							$update_baggage_data = array(
									'from_airport_code' => $from,
									'to_airport_code' => $to,
									'description' => $ticket_bagg_q,
									'price' => $ticket_bagg_p
								);
							$this->custom_db->update_record('flight_booking_baggage_details',$update_baggage_data,$bagg_cond);
						}
					}else{

						if(empty($post_data[$fd_pref.'ticket_bagg_q'][$i]) == false && empty($post_data[$fd_pref.'ticket_bagg_p'][$i]) == false){
							$to = $assembled_booking_details['data']['booking_details_app'][$app_reference]['to_loc'];
							$from = $assembled_booking_details['data']['booking_details_app'][$app_reference]['from_loc'];
							$ticket_bagg_q = $post_data[$fd_pref.'ticket_bagg_q'][$i];
							$ticket_bagg_p = $post_data[$fd_pref.'ticket_bagg_p'][$i];
							if($ticket_bagg_p == ''){
								$ticket_bagg_p = 0;
							}
							$insert_baggage_data = array(
									'passenger_fk' => $custmore_id,
									'from_airport_code' => $from,
									'to_airport_code' => $to,
									'description' => $ticket_bagg_q,
									'price' => $ticket_bagg_p,
									'code' =>'',
								);
							$new_bagg_id = $this->custom_db->insert_record('flight_booking_baggage_details',$insert_baggage_data);
						}
						
					}

					//check meals details
					$meals_details = $this->custom_db->single_table_records('flight_booking_meal_details','*', array('passenger_fk' =>$custmore_id));
					if($meals_details['status'] == true){
						if(empty($post_data[$fd_pref.'ticket_meals_q'][$i]) == false && empty($post_data[$fd_pref.'ticket_meals_p'][$i]) == false){
							$meal_cond['passenger_fk'] = $custmore_id;
							$to = $assembled_booking_details['data']['booking_details_app'][$app_reference]['to_loc'];
							$from = $assembled_booking_details['data']['booking_details_app'][$app_reference]['from_loc'];
							$ticket_meals_q = $post_data[$fd_pref.'ticket_meals_q'][$i];
							$ticket_meals_p = $post_data[$fd_pref.'ticket_meals_p'][$i];
							if($ticket_meals_p == ''){
								$ticket_meals_p = 0;
							}
							$update_meal_data = array(
									'from_airport_code'=> $to,
								    'to_airport_code' => $from,
									'description' => $ticket_meals_q,
									'price' => $ticket_meals_p
								);
							$this->custom_db->update_record('flight_booking_meal_details',$update_meal_data,$meal_cond);
						}
					}else{
						if(empty($post_data[$fd_pref.'ticket_meals_q'][$i]) == false && empty($post_data[$fd_pref.'ticket_meals_p'][$i]) == false){
							$to = $assembled_booking_details['data']['booking_details_app'][$app_reference]['to_loc'];
							$from = $assembled_booking_details['data']['booking_details_app'][$app_reference]['from_loc'];
							$ticket_meals_q = $post_data[$fd_pref.'ticket_meals_q'][$i];
							$ticket_meals_p = $post_data[$fd_pref.'ticket_meals_p'][$i];
							if($ticket_meals_p == ''){
								$ticket_meals_p = 0;
							}
							$insert_meal_data = array(
								    'passenger_fk' => $custmore_id,
								    'from_airport_code'=> $to,
								    'to_airport_code' => $from,
									'description' => $ticket_meals_q,
									'price' => $ticket_meals_p,
									'code' => '',
									'type' => 'static',
								);
							$new_meal_id = $this->custom_db->insert_record('flight_booking_meal_details',$insert_meal_data);
						}
					}

					//check seat details
					$seat_details = $this->custom_db->single_table_records('flight_booking_seat_details','*', array('passenger_fk' =>$custmore_id));

					if($seat_details['status'] == true){
						if(empty($post_data[$fd_pref.'ticket_seat_q'][$i]) == false && empty($post_data[$fd_pref.'ticket_seat_p'][$i]) == false){
							$seat_cond['passenger_fk'] = $custmore_id;
							$to = $assembled_booking_details['data']['booking_details_app'][$app_reference]['to_loc'];
							$from = $assembled_booking_details['data']['booking_details_app'][$app_reference]['from_loc'];
							$flight_no = $assembled_booking_details['data']['booking_details_app'][$app_reference]['booking_itinerary_details'][0]['flight_number'];
							$arline_code = $assembled_booking_details['data']['booking_details_app'][$app_reference]['booking_itinerary_details'][0]['airline_code'];
							$ticket_seat_q = $post_data[$fd_pref.'ticket_seat_q'][$i];
							$ticket_seat_p = $post_data[$fd_pref.'ticket_seat_p'][$i];
							if($ticket_seat_p == ''){
								$ticket_seat_p = 0;
							}
							$update_seat_data = array(
									'from_airport_code' => $from,
									'to_airport_code' => $to,
									'airline_code' => $arline_code,
									'flight_number' => $flight_no,
									'code' => $ticket_seat_q,
									'price' => $ticket_seat_p
								);
							$this->custom_db->update_record('flight_booking_seat_details',$update_seat_data,$seat_cond);
						}
					}else{
						if(empty($post_data[$fd_pref.'ticket_seat_q'][$i]) == false && empty($post_data[$fd_pref.'ticket_seat_p'][$i]) == false){
							$to = $assembled_booking_details['data']['booking_details_app'][$app_reference]['booking_itinerary_details'][0]['to_airport_code'];
							$from = $assembled_booking_details['data']['booking_details_app'][$app_reference]['booking_itinerary_details'][0]['from_airport_code'];
							$flight_no = $assembled_booking_details['data']['booking_details_app'][$app_reference]['booking_itinerary_details'][0]['flight_number'];
							$arline_code = $assembled_booking_details['data']['booking_details_app'][$app_reference]['booking_itinerary_details'][0]['airline_code'];
							$ticket_seat_q = $post_data[$fd_pref.'ticket_seat_q'][$i];
							$ticket_seat_p = $post_data[$fd_pref.'ticket_seat_p'][$i];
							if($ticket_seat_p == ''){
								$ticket_seat_p = 0;
							}
							$insert_seat_data = array(
									'passenger_fk' => $custmore_id,
									'from_airport_code' => $from,
									'to_airport_code' => $to,
									'airline_code' => $arline_code,
									'flight_number' => $flight_no,
									'description' => '',
									'type' => 'dynamic',
									'code' => $ticket_seat_q,
									'price' => $ticket_seat_p
								);
							$new_seat_id = $this->custom_db->insert_record('flight_booking_seat_details',$insert_seat_data);
						}
					}

					//Round trip 
					if($trip_type == 'circle' && $is_domestic == ''){

					$to = $assembled_booking_details['data']['booking_details_app'][$app_reference]['to_loc'];

					$from = $assembled_booking_details['data']['booking_details_app'][$app_reference]['from_loc'];
					
					$cond = array(
							'passenger_fk' => $custmore_id,
							'from_airport_code' => $to,
							'to_airport_code' =>$from,
						);

						//check baggage details
						$baggage_details = $this->custom_db->single_table_records('flight_booking_baggage_details','*',$cond);
						
						if($baggage_details['status'] == true){
							if(empty($post_data['ticket_bagg_q_r'][$i]) == false && empty($post_data['ticket_bagg_p_r'][$i]) == false){
								$bagg_cond['passenger_fk'] = $custmore_id;
								$ticket_bagg_q_r = $post_data['ticket_bagg_q_r'][$i];
								$ticket_bagg_p_r = $post_data['ticket_bagg_p_r'][$i];
								if($ticket_bagg_p_r == ''){
									$ticket_bagg_p_r = 0;
								}
								$update_baggage_data = array(
										'description' => $ticket_bagg_q_r,
										'price' => $ticket_bagg_p_r
									);

								$this->custom_db->update_record('flight_booking_baggage_details',$update_baggage_data,$cond);
							}
						}else{

							if(empty($post_data['ticket_bagg_q_r'][$i]) == false && empty($post_data['ticket_bagg_p_r'][$i]) == false){
								$ticket_bagg_q_r = $post_data['ticket_bagg_q_r'][$i];
								$ticket_bagg_p_r = $post_data['ticket_bagg_p_r'][$i];
								if($ticket_bagg_p_r == ''){
									$ticket_bagg_p_r = 0;
								}
								$insert_baggage_data = array(
										'passenger_fk' => $custmore_id,
										'from_airport_code' => $to,
										'to_airport_code' => $from,
										'description' => $ticket_bagg_q_r,
										'price' => $ticket_bagg_p_r,
										'code' =>'',
									);
								$new_bagg_id = $this->custom_db->insert_record('flight_booking_baggage_details',$insert_baggage_data);
							}
						}

						//check meals details
						$meals_details = $this->custom_db->single_table_records('flight_booking_meal_details','*', $cond);
						if($meals_details['status'] == true){
							if(empty($post_data['ticket_meals_q_r'][$i]) == false && empty($post_data['ticket_meals_p_r'][$i]) == false){
								$meal_cond['passenger_fk'] = $custmore_id;
								$ticket_meals_q_r = $post_data['ticket_meals_q_r'][$i];
								$ticket_meals_p_r = $post_data['ticket_meals_p_r'][$i];
								if($ticket_meals_p_r == ''){
									$ticket_meals_p_r = 0;
								}
								$update_meal_data = array(
										'description' => $ticket_meals_q_r,
										'price' => $ticket_meals_p_r
									);
								$this->custom_db->update_record('flight_booking_meal_details',$update_meal_data,$cond);
							}
						}else{
							if(empty($post_data['ticket_meals_q_r'][$i]) == false && empty($post_data['ticket_meals_p_r'][$i]) == false){
								$ticket_meals_q_r = $post_data['ticket_meals_q_r'][$i];
								$ticket_meals_p_r = $post_data['ticket_meals_p_r'][$i];
								if($ticket_meals_p_r == ''){
									$ticket_meals_p_r = 0;
								}
								$insert_meal_data = array(
									    'passenger_fk' => $custmore_id,
									    'from_airport_code'=> $to,
									    'to_airport_code' => $from,
										'description' => $ticket_meals_q_r,
										'price' => $ticket_meals_p_r,
										'code' => '',
										'type' => 'static',
									);
								$new_meal_id = $this->custom_db->insert_record('flight_booking_meal_details',$insert_meal_data);
							}
						}

						//check seat details
						$seat_details = $this->custom_db->single_table_records('flight_booking_seat_details','*', $cond);

						if($seat_details['status'] == true){
							if(empty($post_data['ticket_seat_q_r'][$i]) == false && empty($post_data['ticket_seat_p_r'][$i]) == false){
								$seat_cond['passenger_fk'] = $custmore_id;
								$ticket_seat_q_r = $post_data['ticket_seat_q_r'][$i];
								$ticket_seat_p_r = $post_data['ticket_seat_p_r'][$i];
								if($ticket_seat_p_r == ''){
									$ticket_seat_p_r = 0;
								}
								$update_seat_data = array(
										'code' => $ticket_seat_q_r,
										'price' => $ticket_seat_p_r
									);
								$this->custom_db->update_record('flight_booking_seat_details',$update_seat_data,$cond);
							}
						}else{
							if(empty($post_data['ticket_seat_q_r'][$i]) == false && empty($post_data['ticket_seat_p_r'][$i]) == false){
								$flight_no = $assembled_booking_details['data']['booking_details_app'][$app_reference]['booking_itinerary_details'][1]['flight_number'];
								$arline_code = $assembled_booking_details['data']['booking_details_app'][$app_reference]['booking_itinerary_details'][1]['airline_code'];
								$ticket_seat_q_r = $post_data['ticket_seat_q_r'][$i];
								$ticket_seat_p_r = $post_data['ticket_seat_p_r'][$i];
								if($ticket_seat_p_r == ''){
									$ticket_seat_p_r = 0;
								}
								$insert_seat_data = array(
										'passenger_fk' => $custmore_id,
										'from_airport_code' => $to,
										'to_airport_code' => $from,
										'airline_code' => $arline_code,
										'flight_number' => $flight_no,
										'description' => '',
										'type' => 'dynamic',
										'code' => $ticket_seat_q_r,
										'price' => $ticket_seat_p_r
									);
								$new_seat_id = $this->custom_db->insert_record('flight_booking_seat_details',$insert_seat_data);
							}
						}	

					}
					// debug($post_data['ticket_number'][$i]);exit;
					//check ticket details
					$ticket_details = $this->custom_db->single_table_records('flight_passenger_ticket_info','*', array('passenger_fk' =>$custmore_id));

					if($ticket_details['status'] == true){
						$ticket_cond['passenger_fk'] = $custmore_id;
						$update_ticket_data = array(
								'TicketNumber' => $post_data['ticket_number'][$i]
							);
						// debug($ticket_cond);exit;
						$this->custom_db->update_record('flight_passenger_ticket_info',$update_ticket_data,$ticket_cond);
					}else{
						$insert_ticket_data = array(
								'passenger_fk' => $custmore_id,
								'TicketId' => '',
								'TicketNumber' => $post_data['ticket_number'][$i],
								'IssueDate' => date('Y-m-d'),
								'Fare' => '',
								'SegmentAdditionalInfo' => '',
								'ValidatingAirline' =>'',
								'CorporateCode' => '',
								'TourCode' => '',
								'Endorsement' => '',
								'Remarks' => '',
								'ServiceFeeDisplayType' => '',
							);

						$this->custom_db->insert_record('flight_passenger_ticket_info',$insert_ticket_data);
					}

				}
				}
				//die('====');
				
				// Start Updating Booking Details
				$booking_cond['app_reference'] = $app_reference;
				$booking_data['booking_source'] = $post_data['booking_source'];
				$booking_data['status'] = $post_data['status'];
				$this->custom_db->update_record('flight_booking_details',$booking_data,$booking_cond);
				// End Updating Booking Details
				
				//Deducting agent balance #Start
				$diff_amt = $new_grand_total-$old_agent_netfare; 

				if($diff_amount>1)
				{
					$agent_id = $temp_bd["data"]["booking_details"][0]["created_by_id"];
					$trans_upd['app_reference'] = $app_reference;
					$amt_to_debt_or_credit = 0-$diff_amount;
					$trans_upd['agent_list_fk'] = $agent_id;
					$trans_upd['remarks'] = "Amount Debited After ticket manupulation";
					$trans_upd['amount'] = $amt_to_debt_or_credit;
					$trans_upd['currency'] = "INR";
					$trans_upd['currency_conversion_rate'] = 1;
					$trans_upd['issued_for'] = 'Debited towards : Change in price after manupulating ticket details';
					//debug($trans_upd); exit;
					$this->domain_management_model->process_direct_credit_debit_transaction($trans_upd, "Debit");
				}
				
				//Deducting agent balance #End
				redirect(base_url().'index.php/voucher/'.$post_data['module'].'/'.$app_reference.'/'.$post_data['booking_source'].'/'.$post_data['status']);
			}

			
		}
		
	}
}

