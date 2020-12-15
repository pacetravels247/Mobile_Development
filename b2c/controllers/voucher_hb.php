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
		$this->load->model('flight_model');
		$this->load->model('hotel_model');
		//we need to activate bus api which are active for current domain and load those libraries
		//$this->output->enable_profiler(TRUE);
	}

	/**
	 *
	 */
	function bus($app_reference, $booking_source='', $booking_status='', $operation='show_voucher')
	{

		error_reporting(0);
		//echo 'under working';exit;
		$this->load->model('bus_model');
		if (empty($app_reference) == false) {

			$booking_details = $this->bus_model->get_booking_details($app_reference, $booking_source, $booking_status);
			
			if ($booking_details['status'] == SUCCESS_STATUS) {
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_bus_booking_data($booking_details, 'b2c');
				// debug($assembled_booking_details);exit;
				$page_data['data'] = $assembled_booking_details['data'];
				if(isset($assembled_booking_details['data']['booking_details'][0])){
					//get agent address & logo for b2b voucher
					$domain_address = $this->custom_db->single_table_records ( 'domain_list','address,domain_logo',array('origin'=>get_domain_auth_id()));
					$page_data['data']['address'] =$domain_address['data'][0]['address'];
					$page_data['data']['logo'] = $domain_address['data'][0]['domain_logo'];
					
				
				}
				// echo 'herre'.$operation;exit;
				switch ($operation) {
					case 'show_voucher' :
						$page_data['button'] = ACTIVE;
						$page_datap['image'] = ACTIVE;
						$this->template->view('voucher/bus_voucher', $page_data);
					break;
					case 'show_pdf' :
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$get_view=$this->template->isolated_view('voucher/bus_pdf', $page_data);
						$create_pdf->create_pdf($get_view,'show');
						break;
					case 'email_voucher' : 
						$page_data['button'] = INACTIVE;
						$page_data['image'] = INACTIVE;
						$mail_template = $this->template->isolated_view('voucher/bus', $page_data);
						//$pdf = $this->provab_pdf->create_pdf($mail_template);
						$pdf = "";
						$email = $this->entity_email;
						$this->provab_mailer->send_mail($email, domain_name().' - Bus Ticket', $mail_template,$pdf);
					break;
				}
			}
		}
	}

	function hotel($app_reference, $booking_source='', $booking_status='', $operation='show_voucher')
	{
		$this->load->model('hotel_model');
		if (empty($app_reference) == false) {
			$booking_details = $this->hotel_model->get_booking_details($app_reference, $booking_source, $booking_status);
			if ($booking_details['status'] == SUCCESS_STATUS) {
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_hotel_booking_data($booking_details, 'b2c');
				$page_data['data'] = $assembled_booking_details['data'];
                if(isset($assembled_booking_details['data']['booking_details'][0])){
					//get agent address & logo for b2b voucher
				
					$domain_address = $this->custom_db->single_table_records ('domain_list','address,domain_logo',array('origin'=>get_domain_auth_id()));
					$page_data['data']['address'] =$domain_address['data'][0]['address'];
					$page_data['data']['logo'] = $domain_address['data'][0]['domain_logo'];
					
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
				}
			}
		}
	}

	/**
	 *
	 */
	function flight($app_reference, $booking_source='', $booking_status='', $operation='show_voucher',$email='')
	{
		$this->load->model('flight_model');
		if (empty($app_reference) == false) {
			$booking_details = $this->flight_model->get_booking_details($app_reference, $booking_source, $booking_status);
			if ($booking_details['status'] == SUCCESS_STATUS) {
				load_flight_lib(PROVAB_FLIGHT_BOOKING_SOURCE);
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_flight_booking_data($booking_details, 'b2c');	
				$page_data['data'] = $assembled_booking_details['data'];
				 if(isset($assembled_booking_details['data']['booking_details'][0])){
					//get agent address & logo for b2b voucher
					
					$domain_address = $this->custom_db->single_table_records ( 'domain_list','address,domain_logo',array('origin'=>get_domain_auth_id()));
					$page_data['data']['address'] =$domain_address['data'][0]['address'];
					$page_data['data']['logo'] = $domain_address['data'][0]['domain_logo'];
						
			
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
					case 'email_voucher':
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$mail_template = $this->template->isolated_view('voucher/flight_pdf', $page_data);
						$pdf = $create_pdf->create_pdf($mail_template,'');
						$this->provab_mailer->send_mail($email, domain_name().' - Flight Ticket',$mail_template ,$pdf);
						break;
				}
			}
		}
	}
		/*
		send email ticket 
	*/
	function email_ticket(){
		$post_params = $this->input->post ();
		//debug($post_params);exit;
		$app_reference = $post_params['app_reference'];
		$booking_source = $post_params['booking_source'];
		$booking_status = $post_params['status'];
		$module = $post_params['module'];
		//$email = $post_params['email'];

		if (empty ( $app_reference ) == false) {

			$this->load->library ( 'provab_mailer' );
			$this->load->library ( 'booking_data_formatter' );
			if($module == 'flight'){
				$booking_details = $this->flight_model->get_booking_details ( $app_reference, $booking_source, $booking_status );
			}
			else if($module == 'hotel'){
				$booking_details = $this->hotel_model->get_booking_details ( $app_reference, $booking_source, $booking_status );
			}
			
			//$booking_details['data']['booking_customer_details'] = $this->booking_data_formatter->add_pax_details($booking_details['data']['booking_customer_details']);
			//$assembled_booking_details = $this->booking_data_formatter->format_flight_booking_data($booking_details, 'b2c');	
			//debug($booking_details);  die();
			$email = $booking_details['data']['booking_details'][0]['email'];
			$email = 'anitha.g.provab@gmail.com';
			if ($booking_details ['status'] == SUCCESS_STATUS) {
				if($module == 'flight'){
					// Assemble Booking Data
					$assembled_booking_details = $this->booking_data_formatter->format_flight_booking_data ( $booking_details, 'b2c' );
					//debug($assembled_booking_details);exit;
					$page_data ['data'] = $assembled_booking_details ['data'];
					$mail_template = $this->template->isolated_view ( 'voucher/flight_voucher', $page_data );
					$subject = 'Flight Details';
				}
				else if($module == 'hotel'){
					// Assemble Booking Data
					$assembled_booking_details = $this->booking_data_formatter->format_hotel_booking_data ( $booking_details, 'b2c' );
					//debug($assembled_booking_details);exit;
					$page_data ['data'] = $assembled_booking_details ['data'];
					$mail_template = $this->template->isolated_view ( 'voucher/hotel_voucher', $page_data );
					$subject = 'Hotel Details';
				}
				

				$status = $this->provab_mailer->send_mail ( $email, $subject, $mail_template, '' );
				//debug($status);exit;
				$status = array (
						"STATUS" => "true" 
				);
				echo json_encode ( $status );
			}

		}else{

			$status = array (
						"STATUS" => "false" 
				);
			echo json_encode ($status);
		}
	}
}
