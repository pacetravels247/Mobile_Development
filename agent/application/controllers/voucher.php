<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Provab
 * @subpackage Bus
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V1
 */

class Voucher extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		//$this->load->library("provab_pdf");
		$this->load->library('provab_mailer');
		$this->load->library('booking_data_formatter');
		$this->load->library('provab_sms');
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
		$this->load->model('bus_model');
		if (empty($app_reference) == false) {
			$call_to_show = "partial_cancellation_also"; 
			$booking_details = $this->bus_model->get_booking_details($app_reference, $booking_source, $booking_status, $call_to_show);
			$terms_conditions = $this->custom_db->single_table_records('terms_conditions','*', array('module' =>'bus'));
		
			if ($booking_details['status'] == SUCCESS_STATUS || $booking_details['status'] == 'BOOKING_CANCELLED') {
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_bus_booking_data($booking_details, 'b2b');
				$page_data['data'] = $assembled_booking_details['data'];
				if(isset($assembled_booking_details['data']['booking_details'][0])){
					//get agent address & logo for b2b voucher		if($assembled_booking_details['data']['booking_details'][0]['created_by_id'] > 0){
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
					
					}
					$page_data['data']['terms_conditions'] = '';
					if($terms_conditions['status'] == SUCCESS_STATUS){
						if($operation=="send_credit_note")
							$page_data['data']['terms_conditions'] = $terms_conditions['data'][0]['credit_note'];
						else
							$page_data['data']['terms_conditions'] = $terms_conditions['data'][0]['description'];
					}
					//debug($booking_details);die();
				// Adv Banner
            	$condition = array("created_by" =>$this->entity_user_id,"status" =>1);
				$page_data['adv_data'] = $this->domain_management_model->get_add($condition);
				$phone = $GLOBALS['CI']->entity_phone;
				$info = $page_data["data"]["booking_details"][0];
				$lead_phone = $info["lead_pax_phone_number"];
				$email = $info['email'];
				$lead_email = $info["lead_pax_email"];
				switch ($operation) {
					case 'show_voucher' :
						/*$page_data['button'] = ACTIVE;
						$page_datap['image'] = ACTIVE;*/
						$this->template->view('voucher/bus_voucher', $page_data);
						if((empty($email)==false) && ($fire_email_sms > 0)){
							if(empty($this->session->userdata('notification_flag'))){
								$this->session->set_userdata('notification_flag',1);
	                         	$mail_template = $this->template->isolated_view('voucher/email_bus_voucher', $page_data);
	                         	$this->provab_sms->fire_sms_to_ccs($page_data, "507856");
	                         	$this->provab_sms->send_msg($phone, $page_data, "508475");
	                        	$this->provab_sms->send_msg($lead_phone, $page_data, "507856");
	                         	$this->provab_mailer->send_mail($email, domain_name().' - Bus Ticket',$mail_template);
	                         }
                       }
					break;
					case 'show_pdf' :
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$get_view=$this->template->isolated_view('voucher/bus_pdf', $page_data);
						$create_pdf->create_pdf($get_view,'show');
					case 'email_voucher' :
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$mail_template = $this->template->isolated_view('voucher/email_bus_voucher', $page_data);
						$pdf = ''; //$pdf = $create_pdf->create_pdf($mail_template,'');
						$this->provab_mailer->send_mail($to_email, domain_name().' - Bus Ticket',$mail_template ,$pdf);
						//$this->provab_sms->send_msg($phone, $page_data, "508475");
						break;
					case 'sms_voucher' :
						if($page_data["data"]["booking_details"][0]["status"] == "BOOKING_CANCELLED")
						{
							$this->provab_sms->send_msg($phone, $page_data, "594355");
							$this->provab_sms->fire_sms_to_ccs($page_data, "594355");
						}
						if($page_data["data"]["booking_details"][0]["status"] == "BOOKING_CONFIRMED")
						{
                        	$this->provab_sms->send_msg($to_email, $page_data, "508475");
                        	$this->provab_sms->fire_sms_to_ccs($page_data, "508475");
						}
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
						$page_data["cancellation_details"] = $this->custom_db->single_table_records('bus_cancellation_details','*',array('app_reference'=>$app_reference));
						$this->template->view('voucher/bus_credit_invoice', $page_data);
						$mail_template = $this->template->isolated_view('voucher/bus_credit_invoice', $page_data);
						if((empty($email)==false) && ($fire_email_sms > 0)){
							if($to_customer)
							{
								$this->provab_sms->send_msg($lead_phone, $page_data, "594355");
								$this->provab_mailer->send_mail($lead_email, domain_name().' - Bus Cancellation Credit Note',	$mail_template);
							}
							else{
								$this->provab_mailer->send_mail($email, domain_name().' - Bus Cancellation Credit Note',	$mail_template);
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
		/*For Sightseeing*/
	function activities($app_reference, $booking_source='', $booking_status='', $operation='show_voucher',$email=''){
		$this->load->model('sightseeing_model');

		if (empty($app_reference) == false) {
			$booking_details = $this->sightseeing_model->get_booking_details($app_reference, $booking_source, $booking_status);
			$terms_conditions = $this->custom_db->single_table_records('terms_conditions','credit_note', array('module' =>'activity'));
			
			if ($booking_details['status'] == SUCCESS_STATUS) {
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_sightseeing_booking_data($booking_details, 'b2b');
				

				$page_data['data'] = $assembled_booking_details['data'];
                if($assembled_booking_details['data']['booking_details'][0]['created_by_id'] > 0){
						$get_agent_info = $this->user_model->get_agent_info($assembled_booking_details['data']['booking_details'][0]['created_by_id']);
						
						if(!empty($get_agent_info)){
							$page_data['data']['address'] = $get_agent_info[0]['address'];
							if(empty($get_agent_info[0]['image']) == false){
								$page_data['data']['logo'] = $get_agent_info[0]['image'];
							}
							else{
								$page_data['data']['logo'] = $page_data['data']['booking_details_app'][$app_reference]['domain_logo'];
							}
							$page_data['data']['phone'] = $get_agent_info[0]['phone'];
							$page_data['data']['domainname'] = $get_agent_info[0]['agency_name'];
							

						}
				}
				$page_data['data']['terms_conditions'] = '';
					if($terms_conditions['status'] == SUCCESS_STATUS){
						$page_data['data']['terms_conditions'] = $terms_conditions['data'][0]['credit_note'];
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
					case 'email_voucher' :
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$mail_template = $this->template->isolated_view('voucher/sightseeing_pdf', $page_data);
						$pdf = $create_pdf->create_pdf($mail_template,'');
						$this->provab_mailer->send_mail($email, domain_name().' - Acitivity Ticket',$mail_template ,$pdf);
						break;
				}
			}
		}
	}
		/*For Transfers*/
	function transfers($app_reference, $booking_source='', $booking_status='', $operation='show_voucher', $to_email=0, $fire_email_sms=0, $refund_amount = 0, $cancellation_charges = 0, $to_customer=0){
		$this->load->model('transferv1_model');

		if (empty($app_reference) == false) {
			$booking_details = $this->transferv1_model->get_booking_details($app_reference, $booking_source, $booking_status);
			$terms_conditions = $this->custom_db->single_table_records('terms_conditions','credit_note', array('module' =>'transfer'));
			
			if ($booking_details['status'] == SUCCESS_STATUS) {
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_transferv1_booking_data($booking_details, 'b2b');
				

				$page_data['data'] = $assembled_booking_details['data'];
                if($assembled_booking_details['data']['booking_details'][0]['created_by_id'] > 0){
						$get_agent_info = $this->user_model->get_agent_info($assembled_booking_details['data']['booking_details'][0]['created_by_id']);
						
						if(!empty($get_agent_info)){
							$page_data['data']['address'] = $get_agent_info[0]['address'];
							if(empty($get_agent_info[0]['image']) == false){
								$page_data['data']['logo'] = $get_agent_info[0]['image'];
							}
							else{
								$page_data['data']['logo'] = $page_data['data']['booking_details_app'][$app_reference]['domain_logo'];
							}
							$page_data['data']['phone'] = $get_agent_info[0]['phone'];
							$page_data['data']['domainname'] = $get_agent_info[0]['agency_name'];
							

						}
				}
				$page_data['data']['terms_conditions'] = '';
					if($terms_conditions['status'] == SUCCESS_STATUS){
						$page_data['data']['terms_conditions'] = $terms_conditions['data'][0]['credit_note'];
					}
				$phone = $GLOBALS['CI']->entity_phone;
				$info = $page_data["data"]["booking_details"][0];
				$lead_phone = $info["lead_pax_phone_number"];
				$email = $info['email'];
				$lead_email = $info["lead_pax_email"];
				switch ($operation) {
					case 'show_voucher' : 
					if((empty($email)==false) && ($fire_email_sms > 0)){
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$mail_template = $this->template->isolated_view('voucher/transferv1_pdf', $page_data);
						$pdf = ''; //$create_pdf->create_pdf($mail_template, '');
						$this->provab_mailer->send_mail($email, domain_name().' - Transfers Ticket',$mail_template ,$pdf);
						}
						$this->template->view('voucher/transferv1_voucher', $page_data);
					break;
					case 'show_pdf' :
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$get_view=$this->template->isolated_view('voucher/transferv1_pdf', $page_data);
						$create_pdf->create_pdf($get_view,'show');
						break;
					case 'email_voucher' :
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$mail_template = $this->template->isolated_view('voucher/transferv1_pdf', $page_data);
						$pdf = ''; //$create_pdf->create_pdf($mail_template,'');
						$this->provab_mailer->send_mail($to_email, domain_name().' - Transfers Ticket',$mail_template ,$pdf);
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
							$this->template->view('voucher/transferv1_credit_invoice', $page_data);
							$mail_template = $this->template->isolated_view('voucher/transferv1_credit_invoice', $page_data);
							if((empty($email)==false) && ($fire_email_sms > 0)){
								if($to_customer)
								{
									//$this->provab_sms->send_msg($lead_phone, $page_data, "594355");
									$this->provab_mailer->send_mail($lead_email, domain_name().' - Transfer Cancellation Credit Note',	$mail_template);
								}
								else{
									$this->provab_mailer->send_mail($email, domain_name().' - Hotel Transfer Credit Note',	$mail_template);
									//$this->provab_sms->send_msg($phone, $page_data, "594355");
								}
								$this->provab_sms->fire_sms_to_ccs($page_data, "594355");
							}
							//redirect('bus/cancellation_details/' . $app_reference . '/' . $booking_source);
							break;
				}
			}
		}
	}

	function hotel($app_reference, $booking_source='', $booking_status='', $operation='show_voucher', $to_email=0, $fire_email_sms=0, $refund_amount = 0, $cancellation_charges = 0, $to_customer=0)
	{
		$this->load->model('hotel_model');
		if (empty($app_reference) == false) {
			$booking_details = $this->hotel_model->get_booking_details($app_reference, $booking_source, $booking_status);
			$terms_conditions = $this->custom_db->single_table_records('terms_conditions','credit_note', array('module' =>'hotel'));
			if ($booking_details['status'] == SUCCESS_STATUS) {
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_hotel_booking_data($booking_details, 'b2b');
				$page_data['data'] = $assembled_booking_details['data'];
				if(isset($assembled_booking_details['data']['booking_details'][0])){
					//get agent address & logo for b2b voucher
					if($assembled_booking_details['data']['booking_details'][0]['created_by_id'] > 0){
						$get_agent_info = $this->user_model->get_agent_info($assembled_booking_details['data']['booking_details'][0]['created_by_id']);

						
						if(!empty($get_agent_info)){
							$page_data['data']['address'] = $get_agent_info[0]['address'];
							if(empty($get_agent_info[0]['image']) == false){
								$page_data['data']['logo'] = $get_agent_info[0]['image'];
							}
							else{
								$page_data['data']['logo'] = $page_data['data']['booking_details_app'][$app_reference]['domain_logo'];
							}
							$page_data['data']['country_code'] = $get_agent_info[0]['country_code'];
							$page_data['data']['phone'] = $get_agent_info[0]['phone'];
							$page_data['data']['domainname'] = $get_agent_info[0]['agency_name'];
						}
					}
					$page_data['data']['terms_conditions'] = '';
					if($terms_conditions['status'] == SUCCESS_STATUS){
						$page_data['data']['terms_conditions'] = $terms_conditions['data'][0]['credit_note'];
					}
				
				}
				// Adv Banner
            	$condition = array("created_by" =>$this->entity_user_id,"status" =>1);
				$page_data['adv_data'] = $this->domain_management_model->get_add($condition);
				$phone = $GLOBALS['CI']->entity_phone;
				$info = $page_data["data"]["booking_details"][0];
				$lead_phone = $info["lead_pax_phone_number"];
				$email = $info['email'];
				$lead_email = $info["lead_pax_email"];
				switch ($operation) {
					case 'show_voucher' : $this->template->view('voucher/hotel_voucher', $page_data);
					if((empty($email)==false) && ($fire_email_sms > 0)){
						$mail_template = $this->template->isolated_view('voucher/email_hotel_voucher', $page_data);
						if((empty($email)==false) && ($fire_email_sms > 0)){
							$pdf = ''; //$pdf = $create_pdf->create_pdf($mail_template,'');
							$this->provab_mailer->send_mail($email, domain_name().' - Hotel Ticket',$mail_template ,$pdf);
	                    	$this->provab_sms->send_msg($lead_phone, $page_data, "704017");
	                    	$this->provab_sms->fire_sms_to_ccs($page_data, "704017");
	                    }
                    }
					break;
					case 'show_pdf' :
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$get_view=$this->template->isolated_view('voucher/hotel_pdf', $page_data);
						$create_pdf->create_pdf($get_view,'show');
						break;
					case 'sms_voucher' :
						if($page_data["data"]["booking_details"][0]["status"] == "BOOKING_CONFIRMED")
                        	$this->provab_sms->send_msg($to_email, $page_data, "704017");
                        $this->provab_sms->fire_sms_to_ccs($page_data, "704017");
						break;
					case 'email_voucher' :
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$mail_template = $this->template->isolated_view('voucher/email_hotel_voucher', $page_data);
						//$pdf = $create_pdf->create_pdf($mail_template,'');
						$this->provab_mailer->send_mail($to_email, domain_name().' - Hotel Ticket',$mail_template ,$pdf);
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
						$this->template->view('voucher/hotel_credit_invoice', $page_data);
						$mail_template = $this->template->isolated_view('voucher/hotel_credit_invoice', $page_data);
						if((empty($email)==false) && ($fire_email_sms > 0)){
							if($to_customer)
							{
								$this->provab_sms->send_msg($lead_phone, $page_data, "594355");
								$this->provab_mailer->send_mail($lead_email, domain_name().' - Hotel Cancellation Credit Note',	$mail_template);
							}
							else{
								$this->provab_mailer->send_mail($email, domain_name().' - Hotel Cancellation Credit Note',	$mail_template);
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

	/**
	 *
	 */
	function flight($app_reference, $booking_source='', $booking_status='', $operation='show_voucher',$to_email=0, $fire_email_sms=0, $refund_amount = 0, $cancellation_charges = 0, $to_customer=0)
	{
		// debug($fire_email_sms);exit;
		$page_data["operation"] = $operation;
		
		$this->load->model('flight_model');
		if (empty($app_reference) == false) {
			$booking_details = $this->flight_model->get_booking_details($app_reference, $booking_source);
			$terms_conditions = $this->custom_db->single_table_records('terms_conditions','*', array('module' =>'flight'));
				load_flight_lib(PROVAB_FLIGHT_BOOKING_SOURCE);
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_flight_booking_data($booking_details, 'b2b');
				// debug($assembled_booking_details); exit;
				$page_data['data'] = $assembled_booking_details['data'];
				if(isset($assembled_booking_details['data']['booking_details'][0])){
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
					
					//get agent address & logo for b2b voucher
					if($assembled_booking_details['data']['booking_details'][0]['created_by_id'] > 0){
						$get_agent_info = $this->user_model->get_agent_info($assembled_booking_details['data']['booking_details'][0]['created_by_id']);						
						if(!empty($get_agent_info)){
							$page_data['data']['address'] = $get_agent_info[0]['address'];
							if(empty($get_agent_info[0]['logo']) == false){
								$page_data['data']['logo'] = $get_agent_info[0]['logo'];
							}
							else{
								$page_data['data']['logo'] = $page_data['data']['booking_details_app'][$app_reference]['domain_logo'];
							}
							$page_data['data']['phone'] = $get_agent_info[0]['phone'];
							$page_data['data']['country_code'] = $get_agent_info[0]['country_code'];
							$page_data['data']['domainname'] = $get_agent_info[0]['agency_name'];
							$page_data['data']['gst_name'] = $get_agent_info[0]['gst_name'];
							$page_data['data']['gst_number'] = $get_agent_info[0]['gst_number'];
							$page_data['data']['agency_email'] = provab_decrypt($get_agent_info[0]['email']); // Shrikant
						}
					}
					$page_data['data']['terms_conditions'] = '';
					if($terms_conditions['status'] == SUCCESS_STATUS){
						$page_data['data']['terms_conditions'] = $terms_conditions['data'][0]['description'];
					}	
				
				}
				//debug($page_data);exit;
				//get the address
				if(isset($assembled_booking_details['data']['booking_details'][0]['created_by_id'])){
					 $get_address= $this->custom_db->single_table_records ( 'user','address',array('user_id'=>$assembled_booking_details['data']['booking_details'][0]['created_by_id']));
					 //debug($get_address);exit;
					 $page_data['data']['address'] = $get_address['data'][0]['address'];
					 
				}
				$admin_phone = $this->custom_db->single_table_records ( 'user','phone',array('user_type'=>1));
				$admin_phone = $admin_phone['data'][0]['phone'];
				// debug($admin_phone);exit;
				$phone = $GLOBALS['CI']->entity_phone;
				$info = $page_data["data"]["booking_details"][0];
				$lead_phone = $info["lead_pax_phone_number"];
				// echo $lead_phone;exit;
				// echo $lead_phone;exit;
				$lead_email = $info["lead_pax_email"];
				$email = $info['email'];
				$is_sms = $assembled_booking_details['data']['booking_details_app'][$app_reference]['is_sms'];
				// echo $is_sms;exit;
				if($booking_source == TRAVELPORT_GDS_BOOKING_SOURCE && $booking_status == 'BOOKING_HOLD' && $is_sms == 0){
					$sms_data['GDSPNR'] = $assembled_booking_details['data']['booking_details_app'][$app_reference]['booking_transaction_details'][0]['gds_pnr'];
					$sms_data['AgentId'] = $GLOBALS['CI']->entity_uuid;
					$sms_data['PACEPNR'] = $app_reference;
					
					$this->provab_sms->send_msg($admin_phone, $sms_data, "620754"); //Admmin SMS
					$this->provab_sms->send_msg($lead_phone, $sms_data, "586183"); //Agent SMS
					$this->custom_db->update_record('flight_booking_details',array('is_sms' => 1), array('app_reference' => $app_reference));
				}
				$page_data["fire_email_sms"] = 0;
				//debug($page_data['data']['phone_code']);exit;
               // Adv Banner
               $condition = array("created_by" =>$this->entity_user_id,"status" =>1);
			   $page_data['adv_data'] = $this->domain_management_model->get_add($condition);
			   // echo $fire_email_sms;exit;
				switch ($operation) {
					case 'show_voucher' : 
					if($booking_source == TRAVELPORT_GDS_BOOKING_SOURCE && count($airlines) > 1){
						
						$this->template->view('voucher/flight_voucher_GDS', $page_data);
					}
					else{
						$this->template->view('voucher/flight_voucher', $page_data);
					}
					$page_data["fire_email_sms"] = $fire_email_sms;
					if((empty($email)==false) && ($fire_email_sms > 0)){
						$sms_data['passegner_name'] = $assembled_booking_details['data']['booking_details'][0]['lead_pax_name'];
						// $this->provab_sms->fire_sms_to_ccs($page_data, "855816");
						// $this->provab_sms->send_msg($lead_phone, $page_data, "855816");
						
						// $this->provab_sms->fire_sms_to_ccs($page_data, "492940");
						// $this->provab_sms->send_msg($admin_phone, $page_data, "492940");
	                     $mail_template = $this->template->isolated_view('voucher/flight_voucher', $page_data);
	                     $this->provab_mailer->send_mail($email, domain_name().' - Flight Ticket',$mail_template);
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
					
                        //debug($get_view); exit;
						$create_pdf->create_pdf($get_view,'show');
						break;
					case 'sms_voucher' :
						if($page_data["data"]["booking_details"][0]["status"] == "BOOKING_CONFIRMED")
                        	$this->provab_sms->send_msg($to_email, $page_data, "855816");
                        	$this->provab_sms->fire_sms_to_ccs($page_data, "855816");
						break;
					case 'email_voucher':
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$mail_template = $this->template->isolated_view('voucher/flight_voucher', $page_data);
						$pdf = ''; //$pdf = $create_pdf->create_pdf($mail_template,'');
						$this->provab_mailer->send_mail($to_email, domain_name().' - Flight Ticket', $mail_template ,$pdf);
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
						$page_data['data']['terms_conditions'] = $terms_conditions['data'][0]['terms_conditions'];
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
                $assembled_booking_details = $this->booking_data_formatter->format_car_booking_datas($booking_details, 'b2b');
                // debug($assembled_booking_details);exit;
                $page_data['data'] = $assembled_booking_details['data'];
                if (isset($assembled_booking_details['data']['booking_details'][0])) {
                    //get agent address & logo for b2b voucher

                    $domain_address = $this->custom_db->single_table_records('domain_list', 'address,domain_logo', array('origin' => get_domain_auth_id()));
                    $page_data['data']['address'] = $domain_address['data'][0]['address'];
                    $page_data['data']['logo'] = $domain_address['data'][0]['domain_logo'];
                   
                }
                // debug($page_data);exit;
                switch ($operation) {
                    case 'show_voucher' : $this->template->view('voucher/car_voucher', $page_data);
                        break;
                    case 'show_pdf' :
                        $this->load->library('provab_pdf');
                        $create_pdf = new Provab_Pdf();
                        $get_view = $this->template->isolated_view('voucher/car_pdf', $page_data);
                        $create_pdf->create_pdf($get_view, 'show');

                        break;
                    case 'email_voucher' :
                        $email = $this->load->library('provab_pdf');
                        $email = @$booking_details['data']['booking_details'][0]['email'];
                        $create_pdf = new Provab_Pdf();
                        $mail_template = $this->template->isolated_view('voucher/car_pdf', $page_data);
                        $pdf = $create_pdf->create_pdf($mail_template, '');
                        $this->provab_mailer->send_mail($email, domain_name() . ' - Car Ticket', $mail_template, $pdf);
                        break;
                }
            }
        }
    }

    ///////////
    function bus_voucher_add_markup(){
    	$ref_no = $_POST['app_no'];
    	$amount = floatval($_POST['amount']);
    	//$agent_markup=$_POST['agent_markup'];
		$agent_markup = 0;
    	//echo $amount.'=='.$ref_no;
    	$btd = $this->custom_db->single_table_records('bus_booking_customer_details', 'count(origin) AS no_of_rows', array('app_reference' => $ref_no));
    	$count = $btd["data"][0]["no_of_rows"];
    	
    	if($amount == 0){
    		$update_data=array(
	    			'agent_markup'=>0
	    		);
    	}else{
    		$new_amount = ($agent_markup+$amount)/$count;
	    	$update_data=array(
	    			'agent_markup'=>$new_amount
	    		);
    	}
    	$status=$this->custom_db->update_record('bus_booking_customer_details',$update_data,array('app_reference' => $ref_no));
    }
    function flight_voucher_add_markup(){
    	$ref_no = trim($_POST['app_no']);
    	$amount = floatval($_POST['amount']);
    	$agent_markup=floatval($_POST['agent_markup']);
    	//echo $amount.'=='.$ref_no.'=='.$agent_markup;die();
    	$btd = $this->custom_db->single_table_records('flight_booking_transaction_details', 'count(origin) AS no_of_rows', array('app_reference' => $ref_no));
    	$count = $btd["data"][0]["no_of_rows"];
    	
    	if($amount == 0){
    		$update_data=array(
	    			'agent_markup'=>0
	    		);
    	}else{
    		$new_amount = $amount/$count; //($agent_markup+$amount)/$count;
	    	$update_data=array(
	    			'agent_markup'=>$new_amount
	    		);
    	}
    	//debug($update_data); exit;
    	$status=$this->custom_db->update_record('flight_booking_transaction_details',$update_data,array('app_reference' => $ref_no));
    }
    function hotel_voucher_add_markup(){
    	$ref_no=$_POST['app_no'];
    	$amount=$_POST['amount'];
    	$agent_markup=$_POST['agent_markup'];
    	//echo $amount.'=='.$ref_no;
    	
    	if($amount == 0){
    		$update_data=array(
	    			'agent_markup'=>0
	    		);
    	}else{
    		$new_amount=$agent_markup+$amount;
	    	$update_data=array(
	    			'agent_markup'=>$new_amount
	    		);
    	}
    	$status=$this->custom_db->update_record('hotel_booking_itinerary_details',$update_data,array('app_reference' => $ref_no));
    }
	function package($app_reference, $booking_source='', $booking_status='', $operation='show_voucher',$email=''){
		
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
		$this->load->model('Package_Model');

		if (empty($app_reference) == false) {
			$booking_details = $this->Package_Model->get_booking_details($app_reference, $booking_source, $booking_status);
			//debug($booking_details);exit("FAsdf");
			//$terms_conditions = $this->custom_db->single_table_records('terms_conditions','credit_note', array('module' =>'activity'));
			if ($booking_details['status'] == SUCCESS_STATUS) {
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_package_booking_data($booking_details, 'b2b');
			//debug($assembled_booking_details);exit("fasdf");
				
				//debug($assembled_booking_details);
				$page_data['data'] = $assembled_booking_details['data'];
                if($assembled_booking_details['data']['booking_details'][0]['created_by_id'] > 0){
						$get_agent_info = $this->user_model->get_agent_info($assembled_booking_details['data']['booking_details'][0]['created_by_id']);
					//debug($get_agent_info);exit;
						if(!empty($get_agent_info)){
							$page_data['data']['address'] = $get_agent_info[0]['address'];
							if(empty($get_agent_info[0]['image']) == false){
								$page_data['data']['logo'] = $get_agent_info[0]['image'];
							}
							else{
								$page_data['data']['logo'] = $page_data['data']['booking_details_app'][$app_reference]['domain_logo'];
							}
							$page_data['data']['phone'] = $get_agent_info[0]['phone'];
							$page_data['data']['domainname'] = $get_agent_info[0]['agency_name'];
							$page_data['data']['user_details'] = $get_agent_info;

						}
				}
				$page_data['data']['terms_conditions'] = '';
					if($terms_conditions['status'] == SUCCESS_STATUS){
						$page_data['data']['terms_conditions'] = $terms_conditions['data'][0]['credit_note'];
					}

				switch ($operation) {
					case 'show_voucher' : 
					//debug($page_data);exit("Fasdfsdf");
					$this->template->view('voucher/package_voucher', $page_data);
					break;
					case 'show_pdf_voucher' :
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$get_view=$this->template->isolated_view('voucher/package_voucher_pdf', $page_data);
						$create_pdf->create_pdf($get_view,'show');
						break;
					case 'email_voucher' :
							echo $email ; 
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$mail_template = $this->template->isolated_view('voucher/package_voucher_pdf', $page_data);
						$pdf = $create_pdf->create_pdf($mail_template,'');
							//echo $mail_template ; exit;
							$mail_template="Pace Travels - Package Ticket";
						$this->provab_mailer->send_mail($email, domain_name().' - Package Ticket',$mail_template,$pdf);
						break;
				}
			}
		}
	}
	public function b2b_voucher($tour_id,$operation='show_broucher',$mail = 'no-mail',$quotation_id = '',$app_reference = '',$email = '',$redirect = '',$ex_data = array())
    {
		//echo $tour_id;  echo $email;exit;
		error_reporting(0);
		$page_data['tour_id'] = $tour_id;
		$this->load->model('Package_Model');
		$page_data['menu'] = false;
		$page_data ['tour_data']            = $this->Package_Model->tour_data($tour_id);
		$page_data ['tours_itinerary']      = $this->Package_Model->tours_itinerary($tour_id);
		// debug($dep_date); exit;
		$page_data ['tours_itinerary_dw']   = @$this->Package_Model->tours_itinerary_dw($tour_id);
		$page_data ['tours_hotel_det']   		= @$this->Package_Model->tour_hotel_city_data($tour_id);
		//debug($page_data ['tours_hotel_det']); exit;
		$page_data ['tours_itinerary_wd']   = $this->Package_Model->tours_itinerary_dw($tour_id);
		$page_data ['tours_date_price']     = $this->Package_Model->tours_date_price($tour_id);
		if($page_data['tour_data']['package_type']=='fit'){
			$page_data['dep_dates'] = $this->custom_db->single_table_records('tour_valid_from_to_date', '*', array('tour_id'=>$tour_id))['data'];
		 
		}else{
			$page_data['dep_dates'] = $this->custom_db->single_table_records('tour_dep_dates', '*', array('tour_id'=>$tour_id))['data'];
		}
     // $tour_data = $this->custom_db->get_result_by_query("select group_concat(airliner_price) pricing, group_concat(occupancy) occ,final_airliner_price,markup,group_concat(markup) markup ,tour_id, from_date, to_date , currency from tour_price_management where tour_id = ".$tour_id." group by from_date, to_date ");
		$b2b_tour_data = $this->custom_db->get_result_by_query("select * from tour_price_management where tour_id = ".$tour_id." and package_type ='B2B' ");
		$b2c_tour_data = $this->custom_db->get_result_by_query("select * from tour_price_management where tour_id = ".$tour_id." and package_type ='B2C' ");
	//echo $this->db->last_query();
		$page_data['tours_city_name'] = $this->Package_Model->tours_city_name();
		$page_data['b2b_tour_price'] = json_decode(json_encode($b2b_tour_data),true);
		$page_data['b2c_tour_price'] = json_decode(json_encode($b2c_tour_data),true);
      //debug($page_data); exit('');
		$visited_city = array();
		$tour_cities = $page_data['tour_data']['tours_city'];
		$tour_cities_array = explode(",", $tour_cities);
		foreach ($tour_cities_array as $t_city) {
		$visited_city[]   = $this->custom_db->single_table_records('tours_city', '*', array('id'=>$t_city))['data'][0];
		}
		$categories = array();
		$tour_types = $page_data['tour_data']['tour_type'];
		$tour_types_array = explode(",", $tour_types);
		foreach ($tour_types_array as $tt_id) {
			$categories[]   = $this->custom_db->single_table_records('tour_type', '*', array('id'=>$tt_id))['data'][0];
		}
		$activities = array();
		$tour_themes = $page_data['tour_data']['theme'];
		$tour_themes_array = explode(",", $tour_themes);
		foreach ($tour_themes_array as $tth_id) {
			$activities[]   = $this->custom_db->single_table_records('tour_subtheme', '*', array('id'=>$tth_id))['data'][0];
		}
		$page_data['visited_city'] = $visited_city;
		$page_data['categories'] = $categories;
		$page_data['activities'] = $activities;
	 
		if ($quotation_id!='') {
			$quotation_details = $this->Package_Model->quotation_details($quotation_id);
			if ($quotation_details['status']==1) {
				$page_data['quotation_details'] = $quotation_details['data'];
			}
		}
		if ($app_reference!='') {
			$booking_details = $this->Package_Model->booking_details($app_reference);
			if ($booking_details['status']==1) {
				$page_data['booking_details'] = $booking_details['data'];
			}
		}
		// echo $mail;exit;
		if($mail == 'mail') { 
			$operation="mail";
			if(!empty($email)){
				$email = $email;
			}
			else{
				if($this->input->post('email')){  
					$email = $this->input->post('email');
					$email_body = $this->input->post('email_body');
				}
			}
			// echo $email;exit;
  
		}
		// echo $operation;exit;
		switch ($operation) {
			case 'show_broucher' : 
				$page_data['menu'] = true;
				//debug($page_data);exit();
				$this->template->view('holiday/b2b_broucher',$page_data);
				break;
			case 'show_pdf' :
				$get_view = $this->template->isolated_view ( 'holiday/b2b_broucher_pdf',$page_data );
				$this->load->library ( 'provab_pdf' );
				$this->provab_pdf->create_pdf ( $get_view, 'D');   
				break;
			case 'mail' :
				$mail_template =$this->template->isolated_view('holiday/b2b_broucher',$page_data);   
				$this->load->library ( 'provab_mailer' ); 
				$get_view = $this->template->isolated_view ( 'holiday/b2b_broucher_pdf',$page_data );
				$this->load->library ( 'provab_pdf' );
				//$pdf = $this->provab_pdf->create_pdf($get_view,'F');
				$create_pdf = new Provab_Pdf();
				$pdf = $create_pdf->create_pdf($mail_template,'');
								//$mail_template = $this->template->isolated_view('voucher/sightseeing_pdf', $page_data);
								//$this->provab_mailer->send_mail($email, domain_name().' - Sightseeing Ticket',$mail_template ,$pdf);
		  
		  
				if(count($ex_data)>0){        
					$message = '<strong style="line-height:25px; font-size:16px;">Good day '.$ex_data['name'].',</strong><br>
						<span style="line-height:25px; font-size:15px;">Please find the Holiday Package below. </span>';
						if($ex_data['booking_url']){  
							$message .= '<a style="line-height:25px; font-size:16px;" href="'.$ex_data['booking_url'].'" target="_blank">Click here to pay</a><br><br>';
						}
				}
				$res = $this->provab_mailer->send_mail($email, 'Holiday Brochure', $email_body,$pdf); 
				if(!empty($redirect)){
					return true;
				}else{
					redirect(base_url().'tours/b2b_voucher/'.$tour_id,'refresh');
				}
				break;
		}
	}
	public function send_low_bal(){
		$lead_phone = '9611577298';
		$page_data = array();
		$this->provab_sms->send_msg($lead_phone, $page_data, "739920");
	}
}
