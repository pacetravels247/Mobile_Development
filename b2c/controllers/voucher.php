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
		$this->load->model('car_model');
		$this->load->model('transferv1_model');
		$this->load->model('sightseeing_model');
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
			$terms_conditions = $this->custom_db->single_table_records('terms_conditions','description', array('module' =>'bus'));
			// debug($booking_details);exit;
			if ($booking_details['status'] == SUCCESS_STATUS) {
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_bus_booking_data($booking_details, 'b2c');
				$page_data['data'] = $assembled_booking_details['data'];
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
						$page_data['data']['terms_conditions'] = $terms_conditions['data'][0]['description'];
					}
				
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
	/*For Sightseeing*/
	function sightseeing($app_reference, $booking_source='', $booking_status='', $operation='show_voucher'){
		$this->load->model('sightseeing_model');

		if (empty($app_reference) == false) {
			$booking_details = $this->sightseeing_model->get_booking_details($app_reference, $booking_source, $booking_status);
			$terms_conditions = $this->custom_db->single_table_records('terms_conditions','description', array('module' =>'activity'));
			
			if ($booking_details['status'] == SUCCESS_STATUS) {
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_sightseeing_booking_data($booking_details, 'b2c');
				

				$page_data['data'] = $assembled_booking_details['data'];
                if(isset($assembled_booking_details['data']['booking_details'][0])){
					//get agent address & logo for b2b voucher
				
					$domain_address = $this->custom_db->single_table_records ('domain_list','address,domain_logo,phone,domain_name,phone_code',array('origin'=>get_domain_auth_id()));
					$page_data['data']['address'] =$domain_address['data'][0]['address'];
					$page_data['data']['logo'] = $domain_address['data'][0]['domain_logo'];
					$page_data['data']['phone'] = $domain_address['data'][0]['phone'];
					$page_data['data']['phone'] = $domain_address['data'][0]['phone_code'];
					$page_data['data']['domainname'] = $domain_address['data'][0]['domain_name'];
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
				}
			}
		}
	}
	/*Transfers Viator*/
	function transferv1($app_reference, $booking_source='', $booking_status='', $operation='show_voucher'){

		if (empty($app_reference) == false) {
			$booking_details = $this->transferv1_model->get_booking_details($app_reference, $booking_source, $booking_status);
			$terms_conditions = $this->custom_db->single_table_records('terms_conditions','description', array('module' =>'transfer'));
			
			if ($booking_details['status'] == SUCCESS_STATUS) {
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_transferv1_booking_data($booking_details, 'b2c');
				

				$page_data['data'] = $assembled_booking_details['data'];
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
						$page_data['data']['terms_conditions'] = $terms_conditions['data'][0]['description'];
					}
					
				}

				switch ($operation) {
					case 'show_voucher' : $this->template->view('voucher/transferv1_voucher', $page_data);
					break;
					case 'show_pdf' :
						$this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$get_view=$this->template->isolated_view('voucher/transferv1_pdf', $page_data);
						$create_pdf->create_pdf($get_view,'show');
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
			$terms_conditions = $this->custom_db->single_table_records('terms_conditions','description', array('module' =>'hotel'));
			if ($booking_details['status'] == SUCCESS_STATUS) {
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_hotel_booking_data($booking_details, 'b2c');
				$page_data['data'] = $assembled_booking_details['data'];
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
                        
			$terms_conditions = $this->custom_db->single_table_records('terms_conditions','description', array('module' =>'flight'));
			 //debug($booking_details);exit;
			if ($booking_details['status'] == SUCCESS_STATUS) {
				load_flight_lib(PROVAB_FLIGHT_BOOKING_SOURCE);
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_flight_booking_data($booking_details, 'b2c');	
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
					// echo $booking_source;exit;

					$domain_address = $this->custom_db->single_table_records ( 'domain_list','address,domain_logo,phone,domain_name,phone_code',array('origin'=>get_domain_auth_id()));
					// debug($domain_address);exit;
					$page_data['data']['address'] =$domain_address['data'][0]['address'];
					$page_data['data']['phone'] =$domain_address['data'][0]['phone'];
					$page_data['data']['phone_code'] =$domain_address['data'][0]['phone_code'];
					$page_data['data']['domainname'] =$domain_address['data'][0]['domain_name'];
					$page_data['data']['logo'] = $domain_address['data'][0]['domain_logo'];
					$page_data['data']['terms_conditions'] = '';
					if($terms_conditions['status'] == SUCCESS_STATUS){
						$page_data['data']['terms_conditions'] = $terms_conditions['data'][0]['description'];
					}	
			
				}
				$admin_details = $this->custom_db->single_table_records ( 'user','office_phone',array('user_type'=>1));
				$customer_support = $admin_details['data'][0]['office_phone'];
				$page_data['customer_support'] = $customer_support;
				// $operation = 'show_pdf';
                                # Get Passenger Email ID
                $email=$booking_details['data']['booking_details'][0]['email'];
                $mail_status=$booking_details['data']['booking_details'][0]['mail_status'];        
				// debug($assembled_booking_details);exit;
				switch ($operation) {
					case 'show_voucher' : 
					if($booking_source == TRAVELPORT_GDS_BOOKING_SOURCE && count($airlines) > 1){
						$this->template->view('voucher/flight_voucher_GDS', $page_data);
					}
					else{
						$this->template->view('voucher/flight_voucher', $page_data);
					}
                     if(empty($email)==false && $mail_status == 0) {
                     	if($booking_source == TRAVELPORT_GDS_BOOKING_SOURCE && count($airlines) > 1){
							$mail_template = $this->template->isolated_view('voucher/flight_voucher_GDS', $page_data);
                         	$this->provab_mailer->send_mail($email, domain_name().' - Flight Ticket',$mail_template);
						}
						else{
						 	$mail_template = $this->template->isolated_view('voucher/flight_voucher', $page_data);
                            $this->provab_mailer->send_mail($email, domain_name().' - Flight Ticket',$mail_template);
						}
                    }
                                        
                    break;
					case 'email_voucher':
                                            
                                                $this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$mail_template = $this->template->isolated_view('voucher/flight_pdf', $page_data);
                         
						$pdf = $create_pdf->create_pdf($mail_template,'');
                                                                   
                                                $this->provab_mailer->send_mail($email, domain_name().' - Flight Ticket',$mail_template ,$pdf);
						break;
					case 'show_pdf' :
                                                $this->load->library('provab_pdf');
						$create_pdf = new Provab_Pdf();
						$get_view=$this->template->isolated_view('voucher/flight_pdf', $page_data);
						
						$create_pdf->create_pdf($get_view,'show');
						$this->custom_db->update_record('flight_booking_details', array('mail_status' =>1),array('app_reference' => $app_reference));
					
				}
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
                $assembled_booking_details = $this->booking_data_formatter->format_car_booking_datas($booking_details, 'b2c');
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
			else if($module == 'car'){
				$booking_details = $this->car_model->get_booking_details ( $app_reference, $booking_source, $booking_status );
			}
			else if($module == 'activities'){
				$booking_details = $this->sightseeing_model->get_booking_details ( $app_reference, $booking_source, $booking_status );
			}
			else if($module == 'transfers'){
				$booking_details = $this->transferv1_model->get_booking_details ( $app_reference, $booking_source, $booking_status );
			}

			// debug($booking_details);exit;
			//$booking_details['data']['booking_customer_details'] = $this->booking_data_formatter->add_pax_details($booking_details['data']['booking_customer_details']);
			//$assembled_booking_details = $this->booking_data_formatter->format_flight_booking_data($booking_details, 'b2c');	
			//debug($booking_details);  die();

			$email = $booking_details['data']['booking_details'][0]['email'];
			//$email = 'elamathisidhu@gmail.com';
			if ($booking_details ['status'] == SUCCESS_STATUS) {
				if($module == 'flight'){
					// Assemble Booking Data
					$assembled_booking_details = $this->booking_data_formatter->format_flight_booking_data ( $booking_details, 'b2c' );
					
					 
					// debug($page_data);exit;
					$page_data ['data'] = $assembled_booking_details ['data'];
					if(isset($assembled_booking_details['data']['booking_details'][0])){
						//get agent address & logo for b2b voucher
						
						$domain_address = $this->custom_db->single_table_records ( 'domain_list','address,domain_logo,phone,domain_name',array('origin'=>get_domain_auth_id()));
						// debug($domain_address);exit;
						$page_data['data']['address'] =$domain_address['data'][0]['address'];
						$page_data['data']['phone'] =$domain_address['data'][0]['phone'];
						$page_data['data']['domainname'] =$domain_address['data'][0]['domain_name'];
						$page_data['data']['logo'] = $domain_address['data'][0]['domain_logo'];
					}
					// debug($page_data);exit;
					$mail_template = $this->template->isolated_view ( 'voucher/flight_voucher', $page_data );
					// debug($mail_template);exit;
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
				else if($module == 'car'){
					// Assemble Booking Data
					$assembled_booking_details = $this->booking_data_formatter->format_car_booking_datas ( $booking_details, 'b2c' );
					//debug($assembled_booking_details);exit;
					$page_data ['data'] = $assembled_booking_details ['data'];
					$mail_template = $this->template->isolated_view ( 'voucher/car_voucher', $page_data );
					$subject = 'Car Details';
				}else if($module=='activities'){
					// Assemble Booking Data
					$assembled_booking_details = $this->booking_data_formatter->format_sightseeing_booking_data ( $booking_details, 'b2c' );
					//debug($assembled_booking_details);exit;
					$page_data ['data'] = $assembled_booking_details ['data'];
					$domain_address = $this->custom_db->single_table_records ( 'domain_list','address,domain_logo,phone,domain_name',array('origin'=>get_domain_auth_id()));
						// debug($domain_address);exit;
						$page_data['data']['address'] =$domain_address['data'][0]['address'];
						$page_data['data']['phone'] =$domain_address['data'][0]['phone'];
						$page_data['data']['domainname'] =$domain_address['data'][0]['domain_name'];
						$page_data['data']['logo'] = $domain_address['data'][0]['domain_logo'];

					$mail_template = $this->template->isolated_view ( 'voucher/sightseeing_voucher', $page_data );
					$subject = 'Activities Details';
				}else if($module=='transfers'){
					// Assemble Booking Data
					$assembled_booking_details = $this->booking_data_formatter->format_transferv1_booking_data ( $booking_details, 'b2c' );				

					//debug($assembled_booking_details);exit;
					$page_data ['data'] = $assembled_booking_details ['data'];
					$domain_address = $this->custom_db->single_table_records ( 'domain_list','address,domain_logo,phone,domain_name',array('origin'=>get_domain_auth_id()));
						// debug($domain_address);exit;
						$page_data['data']['address'] =$domain_address['data'][0]['address'];
						$page_data['data']['phone'] =$domain_address['data'][0]['phone'];
						$page_data['data']['domainname'] =$domain_address['data'][0]['domain_name'];
						$page_data['data']['logo'] = $domain_address['data'][0]['domain_logo'];
					$mail_template = $this->template->isolated_view ( 'voucher/transferv1_voucher', $page_data );
					$subject = 'Transfers Details';
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
	function package($app_reference, $booking_source='', $booking_status='', $operation='show_voucher',$email=''){
		
		$this->load->model('Package_Model');
		$this->load->model('user_model');
		if (empty($app_reference) == false) {
			$booking_details = $this->Package_Model->get_booking_details($app_reference, $booking_source, $booking_status);
			//debug($booking_details);exit("FAsdf");
			//$terms_conditions = $this->custom_db->single_table_records('terms_conditions','credit_note', array('module' =>'activity'));
			if ($booking_details['status'] == SUCCESS_STATUS) {
				//Assemble Booking Data
				$assembled_booking_details = $this->booking_data_formatter->format_package_booking_data($booking_details, 'b2c');
			//debug($assembled_booking_details);exit("fasdf");
				 
				//debug($assembled_booking_details);
				$page_data['data'] = $assembled_booking_details['data'];
                if($assembled_booking_details['data']['booking_details'][0]['created_by_id'] > 0){
					
						$get_agent_info = $this->user_model->get_customer_info($assembled_booking_details['data']['booking_details'][0]['created_by_id']);
						//echo $this->db->last_query();
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
public function b2c_voucher($tour_id,$operation='show_broucher',$mail = 'no-mail',$quotation_id = '',$app_reference = '',$email = '',$redirect = '',$ex_data = array())
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
				$this->template->view('holiday/b2c_broucher',$page_data);
				break;
			case 'show_pdf' :
				$get_view = $this->template->isolated_view ( 'holiday/b2c_broucher_pdf',$page_data );
				$this->load->library ( 'provab_pdf' );
				$this->provab_pdf->create_pdf ( $get_view, 'D');   
				break;
			case 'mail' :
				$mail_template =$this->template->isolated_view('holiday/b2c_broucher',$page_data);   
				$this->load->library ( 'provab_mailer' ); 
				$get_view = $this->template->isolated_view ( 'holiday/b2c_broucher_pdf',$page_data );
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
					redirect(base_url().'report/holidays','refresh');
				}
				break;
		}
	}
}

