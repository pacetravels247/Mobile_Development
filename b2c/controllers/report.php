<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Provab - Provab Application
 * @subpackage Travel Portal
 * @version    V2
 */
class Report extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('bus_model');
		$this->load->model('hotel_model');
		$this->load->model('flight_model');
		$this->load->model('Package_Model');
		$this->load->model('car_model');
		$this->load->model('sightseeing_model');
		$this->load->model('transferv1_model');
		$this->load->library('booking_data_formatter');
	}
	function index()
	{
		$this->flight($offset=0);
	}
	function bookings()
	{
		$this->template->view('report/bookings');
	}
	/************************************** BUS REPORT STARTS ***********************************
	 /**
	 * Bus Report
	 * @param $offset
	 */
	function buses($offset=0)
	{
		validate_user_login();
		$condition = array();
		$total_records = $this->bus_model->booking($condition, true);
		$table_data = $this->bus_model->booking($condition, false, $offset, RECORDS_RANGE_2);
		$table_data = $this->booking_data_formatter->format_bus_booking_data($table_data, 'b2c');
		$page_data['table_data'] = $table_data['data'];
		// debug($table_data);exit;
		/** TABLE PAGINATION */
		$this->load->library('pagination');
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['base_url'] = base_url().'index.php/report/bus/';
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$page_data['total_rows'] = $config['total_rows'] = $total_records;
		$config['per_page'] = RECORDS_RANGE_2;
		$this->pagination->initialize($config);
		/** TABLE PAGINATION */
		$page_data['total_records'] = $config['total_rows'];
		$page_data['customer_email'] = $this->entity_email;
		$this->template->view('report/bus', $page_data);
	}
	/**
	 * Bus Booking Details
	 */
	function bus_booking_details()
	{
		$get_data = $this->input->get();
		if(valid_array($get_data) == true && empty($get_data['status']) == false &&
		empty($get_data['reference_id']) == false &&  empty($get_data['app_reference']) == false) {
			$booking_id = trim($get_data['reference_id']);
			$status = trim($get_data['status']);
			$app_reference = trim($get_data['app_reference']);
			$booking_details = $this->bus_model->get_booking_details($app_reference,$booking_id,$status);
			if(valid_array($booking_details) == true && $booking_details['status'] == SUCCESS_STATUS){
				//Assemble Booking Details
				$assembled_booking_details = $this->booking_data_formatter->format_bus_booking_data($booking_details, 'b2c');
				$page_data['data'] = $assembled_booking_details['data'];
				$this->template->view('bus/booking_details', $page_data);
			} else {
				redirect('general/index/bus?event=Invalid Booking ID');
			}
		} else {
			redirect('general/index/bus?event=Invalid Booking Details');
		}
	}
	/**
	 * Bus Ticket
	 */
	function get_bus_ticket()
	{
		$get_data = $this->input->get();
		if(valid_array($get_data) == true && empty($get_data['reference_id']) == false && empty($get_data['app_reference']) == false) {
			$booking_id = trim($get_data['reference_id']);
			$status = trim($get_data['status']);
			$app_reference = trim($get_data['app_reference']);
			$booking_details = $this->bus_model->get_booking_details($app_reference,$booking_id,$status);
			if(valid_array($booking_details) == true && $booking_details['status'] == SUCCESS_STATUS) {
				//Assemble Booking Details
				$assembled_booking_details = $this->booking_data_formatter->format_bus_booking_data($booking_details, 'b2c');
				$page_data['data'] = $assembled_booking_details['data'];
				$data = $this->template->isolated_view('bus/get_ticket', $page_data);
				header('Content-Type:application/json');
				echo json_encode(array('ticket' => get_compressed_output($data)));
				exit;
			} else {
				redirect('general/index/bus?event=Invalid Deatils');
			}
		} else {
			redirect('general/index/bus?event=Invalid Booking Details');
		}
	}
	/**
	 * Bus Invoice
	 */
	function get_bus_invoice()
	{
		$get_data = $this->input->get();
		if(valid_array($get_data) == true && empty($get_data['reference_id']) == false &&  empty($get_data['app_reference']) == false) {
			$booking_id = trim($get_data['reference_id']);
			$status = trim($get_data['status']);
			$app_reference = trim($get_data['app_reference']);
			$booking_details = $this->bus_model->get_booking_details($app_reference,$booking_id,$status);
			if(valid_array($booking_details) == true && $booking_details['status'] == SUCCESS_STATUS) {
				//Assemble Booking Details
				$assembled_booking_details = $this->booking_data_formatter->format_bus_booking_data($booking_details, 'b2c');
				$page_data['data'] = $assembled_booking_details['data'];
				$data = $this->template->isolated_view('bus/get_invoice', $page_data);
				header('Content-Type:application/json');
				echo json_encode(array('invoice' => get_compressed_output($data)));
				exit;
			} else {
				redirect('general/index/bus?event=Invalid Deatils');
			}
		} else {
			redirect('general/index/bus?event=Invalid Booking Details');
		}
	}
	/**
	 * Email Bus Ticket
	 * @param string $app_reference
	 * @param string $booking_source
	 * @param string $booking_status
	 * @param string $user_email_id
	 * @param string $operation
	 */

	function email_bus_ticket($app_reference, $booking_source='', $booking_status='', $user_email_id='', $operation='show_voucher')
	{
		echo 'Under working';exit;
		if (empty($app_reference) == false) {
			$booking_details = $this->bus_model->get_booking_details($app_reference, $booking_source, $booking_status);
			if ($booking_details['status'] == SUCCESS_STATUS) {
				$this->load->library("provab_pdf");
				$this->load->library('provab_mailer');
				//Assemble Booking Details
				$assembled_booking_details = $this->booking_data_formatter->format_bus_booking_data($booking_details, 'b2c');
				$page_data['data'] = $assembled_booking_details['data'];
				$mail_template = $this->template->isolated_view('bus/get_ticket', $page_data);
				//echo $mail_template;exit;
				$pdf = $this->provab_pdf->create_pdf($mail_template);
				//$pdf = "";
				$email = $this->entity_email;
				$user_email_id = trim($user_email_id);
				$this->provab_mailer->send_mail($user_email_id, 'ProApp - Bus Ticket', $mail_template,$pdf);
				header('Content-Type:application/json');
				echo json_encode(array('status' => SUCCESS_STATUS));
				exit;
			}else{
				header('Content-Type:application/json');
				echo json_encode(array('status' => "failed"));
				exit;
			}
		}else{
			redirect('general/index/bus?event=Invalid Deatils');
		}
	}
	/************************************** HOTEL REPORT STARTS ***********************************/
	/**
	 * Hotel Report
	 * @param $offset
	 */
	function hotels($offset=0)
	{
		validate_user_login();
		$condition = array();
		$total_records = $this->hotel_model->booking($condition, true);
		$table_data = $this->hotel_model->booking($condition, false, $offset, RECORDS_RANGE_2);
		$table_data = $this->booking_data_formatter->format_hotel_booking_data($table_data, 'b2c');
		$page_data['table_data'] = $table_data['data'];
		/** TABLE PAGINATION */
		$this->load->library('pagination');
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['base_url'] = base_url().'index.php/report/hotel/';
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$page_data['total_rows'] = $config['total_rows'] = $total_records;
		$config['per_page'] = RECORDS_RANGE_2;
		$this->pagination->initialize($config);
		/** TABLE PAGINATION */
		$page_data['total_records'] = $config['total_rows'];
		$page_data['customer_email'] = $this->entity_email;
		$this->template->view('report/hotel', $page_data);
	}
	/************************************** CAR REPORT STARTS ***********************************/
	/**
	 * Cae Report
	 * @param $offset
	 */
	function car($offset=0)
	{
		validate_user_login();
		$condition = array();
		$total_records = $this->car_model->booking($condition, true);
		$table_data = $this->car_model->booking($condition, false, $offset, RECORDS_RANGE_2);
		$table_data = $this->booking_data_formatter->format_car_booking_datas($table_data, 'b2c');
		$page_data['table_data'] = $table_data['data'];
		/** TABLE PAGINATION */
		$this->load->library('pagination');
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['base_url'] = base_url().'index.php/report/car/';
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$page_data['total_rows'] = $config['total_rows'] = $total_records;
		$config['per_page'] = RECORDS_RANGE_2;
		$this->pagination->initialize($config);
		/** TABLE PAGINATION */
		$page_data['total_records'] = $config['total_rows'];
		$page_data['customer_email'] = $this->entity_email;
		// debug($page_data);exit;
		$this->template->view('report/car', $page_data);
	}
	/**
	*Sightseeing Booking Details
	*/
	function activities($offset=0){
		validate_user_login();
		$condition = array();
		$total_records = $this->sightseeing_model->booking($condition, true);
		$table_data = $this->sightseeing_model->booking($condition, false, $offset, RECORDS_RANGE_2);
		$table_data = $this->booking_data_formatter->format_sightseeing_booking_data($table_data, 'b2c');
		$page_data['table_data'] = $table_data['data'];
		/** TABLE PAGINATION */
		$this->load->library('pagination');
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['base_url'] = base_url().'index.php/report/activities/';
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$page_data['total_rows'] = $config['total_rows'] = $total_records;
		$config['per_page'] = RECORDS_RANGE_2;
		$this->pagination->initialize($config);
		/** TABLE PAGINATION */
		$page_data['total_records'] = $config['total_rows'];
		$page_data['customer_email'] = $this->entity_email;
		$this->template->view('report/sightseeing', $page_data);
	}
	/**
	*Transfers Booking Details
	*/
	function transfers($offset=0){
		
		validate_user_login();
		$condition = array();
		$total_records = $this->transferv1_model->booking($condition, true);
		$table_data = $this->transferv1_model->booking($condition, false, $offset, RECORDS_RANGE_2);
		$table_data = $this->booking_data_formatter->format_transferv1_booking_data($table_data, 'b2c');
		$page_data['table_data'] = $table_data['data'];
		/** TABLE PAGINATION */
		$this->load->library('pagination');
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['base_url'] = base_url().'index.php/report/transferv1/';
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$page_data['total_rows'] = $config['total_rows'] = $total_records;
		$config['per_page'] = RECORDS_RANGE_2;
		$this->pagination->initialize($config);
		/** TABLE PAGINATION */
		$page_data['total_records'] = $config['total_rows'];
		$page_data['customer_email'] = $this->entity_email;
		$this->template->view('report/transferv1', $page_data);
	}

	/**
	 * Hotel Booking Dettails
	 */
	function hotel_booking_details()
	{
		$get_data = $this->input->get();
		if(valid_array($get_data) == true && empty($get_data['status']) == false &&
		empty($get_data['reference_id']) == false &&  empty($get_data['app_reference']) == false) {
			$booking_id = trim($get_data['reference_id']);
			$status = trim($get_data['status']);
			$app_reference = trim($get_data['app_reference']);
			$booking_details = $this->hotel_model->get_booking_details($app_reference,$booking_id,$status);
			if(valid_array($booking_details) == true && $booking_details['status'] == SUCCESS_STATUS){	
				//Assemble Booking Details
				$assembled_booking_details = $this->booking_data_formatter->format_hotel_booking_data($booking_details, 'b2c');
				$page_data['data'] = $assembled_booking_details['data'];
				$this->template->view('hotel/booking_details', $page_data);
			} else {
				redirect('general/index/bus?event=Invalid Booking ID');
			}
		} else {
			redirect('general/index/bus?event=Invalid Booking Details');
		}
	}
	/**
	 * Hotel Voucher
	 */
	function get_hotel_voucher()
	{
		$get_data = $this->input->get();
		if(valid_array($get_data) == true && empty($get_data['reference_id']) == false && empty($get_data['app_reference']) == false) {
			$booking_id = trim($get_data['reference_id']);
			$status = trim($get_data['status']);
			$app_reference = trim($get_data['app_reference']);
			$booking_details = $this->hotel_model->get_booking_details($app_reference,$booking_id,$status);
			if(valid_array($booking_details) == true && $booking_details['status'] == SUCCESS_STATUS) {
				//Assemble Booking Details
				$assembled_booking_details = $this->booking_data_formatter->format_hotel_booking_data($booking_details, 'b2c');
				$page_data['data'] = $assembled_booking_details['data'];
				$data = $this->template->isolated_view('hotel/get_voucher', $page_data);
				header('Content-Type:application/json');
				echo json_encode(array('ticket' => get_compressed_output($data)));
				exit;
			} else {
				redirect('general/index/bus?event=Invalid Deatils');
			}
		} else {
			redirect('general/index/bus?event=Invalid Booking Details');
		}
	}
	/**
	 * Hotel Invoice
	 */
	function get_hotel_invoice()
	{
		$get_data = $this->input->get();
		if(valid_array($get_data) == true && empty($get_data['reference_id']) == false &&  empty($get_data['app_reference']) == false) {
			$booking_id = trim($get_data['reference_id']);
			$status = trim($get_data['status']);
			$app_reference = trim($get_data['app_reference']);
			$booking_details = $this->hotel_model->get_booking_details($app_reference,$booking_id,$status);
			if(valid_array($booking_details) == true && $booking_details['status'] == SUCCESS_STATUS) {
				//Assemble Booking Details
				$assembled_booking_details = $this->booking_data_formatter->format_hotel_booking_data($booking_details, 'b2c');
				$page_data['data'] = $assembled_booking_details['data'];
				$data = $this->template->isolated_view('hotel/get_invoice', $page_data);
				header('Content-Type:application/json');
				echo json_encode(array('invoice' => get_compressed_output($data)));
				exit;
			} else {
				redirect('general/index/bus?event=Invalid Deatils');
			}
		} else {
			redirect('general/index/bus?event=Invalid Booking Details');
		}
	}
	/**
	 * Mail Hotel Voucher
	 * @param $app_reference
	 * @param $booking_source
	 * @param $booking_status
	 * @param $user_email_id
	 * @param $operation
	 */

	function email_hotel_voucher($app_reference, $booking_source='', $booking_status='', $user_email_id='', $operation='show_voucher')
	{
		//echo 'under working';exit;
		if (empty($app_reference) == false) {																																
			$booking_details = $this->hotel_model->get_booking_details($app_reference,$booking_source,$booking_status);
			if ($booking_details['status'] == SUCCESS_STATUS) {
				$this->load->library("provab_pdf");
				$this->load->library('provab_mailer');
				//Assemble Booking Details
				$assembled_booking_details = $this->booking_data_formatter->format_hotel_booking_data($booking_details, 'b2c');
				$page_data['data'] = $assembled_booking_details['data'];
				$mail_template = $this->template->isolated_view('hotel/get_voucher', $page_data);
				//echo $mail_template;exit;
				$pdf = $this->provab_pdf->create_pdf($mail_template);
				//echo $pdf;exit;
				//$pdf = "";
				$user_email_id = trim($user_email_id);
				$this->provab_mailer->send_mail($user_email_id, 'ProApp - Hotel Ticket', $mail_template,$pdf);
				header('Content-Type:application/json');
				echo json_encode(array('status' => SUCCESS_STATUS));
				exit;
			}else{
				header('Content-Type:application/json');
				echo json_encode(array('status' => "failed"));
				exit;
			}
		}else{
			redirect('general/index/bus?event=Invalid Deatils');
		}
	}

	/**
	 * Mail Hotel Voucher
	 * @param $app_reference
	 * @param $booking_source
	 * @param $booking_status
	 * @param $user_email_id
	 * @param $operation
	 */

	function email_sightseeing_voucher($app_reference, $booking_source='', $booking_status='', $user_email_id='', $operation='show_voucher')
	{
		//echo 'under working';exit;
		if (empty($app_reference) == false) {																																
			$booking_details = $this->sightseeing_model->get_booking_details($app_reference,$booking_source,$booking_status);
			if ($booking_details['status'] == SUCCESS_STATUS) {
				$this->load->library("provab_pdf");
				$this->load->library('provab_mailer');
				//Assemble Booking Details
				$assembled_booking_details = $this->booking_data_formatter->format_sightseeing_booking_data($booking_details, 'b2c');
				$page_data['data'] = $assembled_booking_details['data'];
				$mail_template = $this->template->isolated_view('sightseeing/get_voucher', $page_data);
				//echo $mail_template;exit;
				$pdf = $this->provab_pdf->create_pdf($mail_template);
				//echo $pdf;exit;
				//$pdf = "";
				$user_email_id = trim($user_email_id);
				$this->provab_mailer->send_mail($user_email_id, 'ProApp - Sightseeing Ticket', $mail_template,$pdf);
				header('Content-Type:application/json');
				echo json_encode(array('status' => SUCCESS_STATUS));
				exit;
			}else{
				header('Content-Type:application/json');
				echo json_encode(array('status' => "failed"));
				exit;
			}
		}else{
			redirect('general/index/sightseeing?event=Invalid Deatils');
		}
	}


	/************************************** FLIGHT REPORT STARTS ***********************************/
	/**
	 * Flight Report
	 * @param $offset
	 */
	function flights($offset=0)
	{
		validate_user_login();
		$current_user_id = $GLOBALS['CI']->entity_user_id;
		$condition = array();
		$total_records = $this->flight_model->booking($condition, true);
		$table_data = $this->flight_model->booking($condition, false, $offset, RECORDS_RANGE_2);
		
		$table_data = $this->booking_data_formatter->format_flight_booking_data($table_data, 'b2c');
		
		$page_data['table_data'] = $table_data['data'];
		$this->load->library('pagination');
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['base_url'] = base_url().'index.php/report/flight/';
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$page_data['total_rows'] = $config['total_rows'] = $total_records;
		$config['per_page'] = RECORDS_RANGE_2;
		$this->pagination->initialize($config);
		/** TABLE PAGINATION */
		$page_data['total_records'] = $config['total_rows'];
		$this->template->view('report/airline', $page_data);
	}
	/**
	 * Returns Flight Total Fare
	 * @param $fare_details
	 */
	private function flight_total_fare($fare_details)
	{
		$total_fare = array();
		foreach($fare_details as $k => $v) {
			$total_fare[$k] = roundoff_number($v['fare']+$v['admin_markup']+$v['agent_markup']);
		}
		return $total_fare;
	}
	/*
	 * Flight Booking Details
	 */
	function flight_booking_details()
	{
		echo 'under working';exit;
	}
	/*
	 * Flight Ticket
	 */
	function get_flight_ticket()
	{
		$get_data = $this->input->get();
		$booking_id = trim($get_data['reference_id']);
		$status = trim($get_data['status']);
		$app_reference = trim($get_data['app_reference']);
		if(valid_array($get_data) == true && empty($get_data['reference_id']) == false && empty($get_data['app_reference']) == false) {
			$booking_details = $this->flight_model->get_booking_details($app_reference,$booking_id,$status);
			if(valid_array($booking_details) == true && $booking_details['status'] == SUCCESS_STATUS) {
				$page_data['booking_details'] = $booking_details;
				//debug($page_data['booking_details']); exit;
				$data = $this->template->isolated_view('flight/get_eticket', $page_data);
				header('Content-Type:application/json');
				echo json_encode(array('ticket' => get_compressed_output($data)));
				exit;
			} else {
				redirect('general/index/bus?event=Invalid Deatils');
			}
		} else {
			redirect('general/index/bus?event=Invalid Booking Details');
		}
	}
	/**
	 * Flight Invoice
	 */
	function get_flight_invoice()
	{
		$get_data = $this->input->get();
		$booking_id = trim($get_data['reference_id']);
		$status = trim($get_data['status']);
		$app_reference = trim($get_data['app_reference']);
		if(valid_array($get_data) == true && empty($get_data['reference_id']) == false &&  empty($get_data['app_reference']) == false) {
			$booking_id = trim($get_data['reference_id']);
			$booking_details = $this->flight_model->get_booking_details($app_reference,$booking_id,$status);
			if(valid_array($booking_details) == true && $booking_details['status'] == SUCCESS_STATUS) {
				$page_data['booking_details'] = $booking_details;
				$data = $this->template->isolated_view('flight/get_invoice', $page_data);
				header('Content-Type:application/json');
				echo json_encode(array('invoice' => get_compressed_output($data)));
				exit;
			} else {
				redirect('general/index/bus?event=Invalid Deatils');
			}
		} else {
			redirect('general/index/bus?event=Invalid Booking Details');
		}
	}
	/*
	 * Mail Flight Ticket
	 */
	function email_flight_ticket()
	{
		echo 'under working';exit;
	}
	function holidays($offset=0)
	{
	
			///ini_set('display_errors', 1);
			//ini_set('display_startup_errors', 1);
			//error_reporting(E_ALL);
		$get_data = $this->input->get();
		//debug($get_data);exit;
		$page_data = array();
		if(isset($get_data['system_transaction_id'])){
			
			$condition = 'AND BD.app_reference ="'.$get_data['system_transaction_id'].'"';
		}else{
			$condition = '';
		}
		//$filter_data = $this->format_basic_search_filters();
		//$page_data['from_date'] = $filter_data['from_date'];
		//$page_data['to_date'] = $filter_data['to_date'];
		//$condition = $filter_data['filter_condition'];
		if(isset($get_data['filter_report_data']) == true && empty($get_data['filter_report_data']) == false) {
			$filter_report_data = trim($get_data['filter_report_data']);
			$search_filter_condition = '(BD.app_reference like "%'.$filter_report_data.'%" OR BD.pnr like "%'.$filter_report_data.'%")';
			$total_records = $this->bus_model->filter_booking_report($search_filter_condition, true);
			$table_data = $this->bus_model->filter_booking_report($search_filter_condition, false, $offset, RECORDS_RANGE_2);
		} else {
			$total_records = $this->Package_Model->booking($condition, true);
			$table_data = $this->Package_Model->booking($condition, false, $offset, RECORDS_RANGE_2);
		}
		$table_data = $this->booking_data_formatter->format_package_booking_data($table_data, 'b2b');
		$page_data['table_data'] = $table_data['data'];
		/** TABLE PAGINATION */
		$this->load->library('pagination');
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['base_url'] = base_url().'index.php/report/package_enquiry_report/';
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$page_data['total_rows'] = $config['total_rows'] = $total_records;
		$config['per_page'] = RECORDS_RANGE_2;
		$this->pagination->initialize($config);
		/** TABLE PAGINATION */
		$page_data['total_records'] = $config['total_rows'];
		$page_data['customer_email'] = $this->entity_email;
		$page_data['user_details'] = $this->custom_db->single_table_records('user','*',array('user_id'=>$this->entity_user_id))['data'][0];
		//debug($page_data);exit;
		$this->template->view('report/package_enquiry_report', $page_data);
	}
	function monthly_booking_report()
	{
		$this->template->view('report/monthly_booking_report');
	}
	/* print the voucher for all modules for B2C*/
	function print_voucher(){
error_reporting(E_ALL);
	    $post_data = $this->input->post();
	    if(isset($post_data) && valid_array($post_data)){
	    	if (empty($post_data['pnr_number']) == false) {
	    		$this->load->library('pagination');
	    		$page_data['total_rows'] = $config['total_rows'] = 1;
				$config['per_page'] = RECORDS_RANGE_2;
				$this->pagination->initialize($config);
				/** TABLE PAGINATION */
				$page_data['print_voucher'] = 'yes';
				$page_data['total_records'] = $config['total_rows'];
				
				$booking_status = 'BOOKING_CONFIRMED';
	    		if($post_data['module'] == PROVAB_FLIGHT_BOOKING_SOURCE){
	    			$booking_details = $this->flight_model->booking_guest_user($post_data['pnr_number'], $post_data['module'], $booking_status);
	    			if ($booking_details['status'] == SUCCESS_STATUS) {
	    				$assembled_booking_details = $this->booking_data_formatter->format_flight_booking_data($booking_details, 'b2c');	
						
						$page_data['table_data'] = $assembled_booking_details['data'];
						$this->template->view('report/airline', $page_data);
					}
	    			
	    		}
	    		if($post_data['module'] == PROVAB_HOTEL_BOOKING_SOURCE){
	    			$booking_details = $this->hotel_model->booking_guest_user($post_data['pnr_number'], $post_data['module'], $booking_status);
	    			if ($booking_details['status'] == SUCCESS_STATUS) {
	    				$assembled_booking_details = $this->booking_data_formatter->format_hotel_booking_data($booking_details, 'b2c');	
						
						$page_data['table_data'] = $assembled_booking_details['data'];
						$this->template->view('report/hotel', $page_data);
					}
	    			
	    		}
	    		if($post_data['module'] == PROVAB_BUS_BOOKING_SOURCE){
	    			$booking_details = $this->bus_model->booking_guest_user($post_data['pnr_number'], $post_data['module'], $booking_status);
	    			if ($booking_details['status'] == SUCCESS_STATUS) {
	    				$assembled_booking_details = $this->booking_data_formatter->format_bus_booking_data($booking_details, 'b2c');	
						
						$page_data['table_data'] = $assembled_booking_details['data'];
						$this->template->view('report/bus', $page_data);
					}
	    			
	    		}
	    		if($post_data['module'] == PROVAB_TRANSFERV1_BOOKING_SOURCE){
	    			$booking_details = $this->transferv1_model->booking_guest_user($post_data['pnr_number'], $post_data['module'], $booking_status);
	    			if ($booking_details['status'] == SUCCESS_STATUS) {
	    				$assembled_booking_details = $this->booking_data_formatter->format_transferv1_booking_data($booking_details, 'b2c');	
						
						$page_data['table_data'] = $assembled_booking_details['data'];
						$this->template->view('report/transferv1', $page_data);
					}
	    			
	    		}
	    		if($post_data['module'] == PROVAB_SIGHTSEEN_BOOKING_SOURCE){
	    			$booking_details = $this->sightseeing_model->booking_guest_user($post_data['pnr_number'], $post_data['module'], $booking_status);
	    			if ($booking_details['status'] == SUCCESS_STATUS) {
	    				$assembled_booking_details = $this->booking_data_formatter->format_sightseeing_booking_data($booking_details, 'b2c');	
						
						$page_data['table_data'] = $assembled_booking_details['data'];
						$this->template->view('report/sightseeing', $page_data);
					}
	    			
	    		}
	    		
	        }
	    }
	    else{
	      $this->template->view('report/print_voucher');
	    }
	}
	function pnr_status(){
		echo "no PNR found";
	}
} 


?>