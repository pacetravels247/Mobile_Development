<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Provab - Provab Application
 * @subpackage Travel Portal
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V2
 */

class Report extends CI_Controller {
	private $current_module;
	public function __construct()
	{
		parent::__construct();
		$this->load->model('bus_model');
		$this->load->model('hotel_model');
		$this->load->model('flight_model');
		$this->load->model('sightseeing_model');
		$this->load->model('transferv1_model');
		$this->load->model('car_model');
		$this->load->model('user_model');
		$this->load->library('booking_data_formatter');
		$this->load->model('domain_management_model');
		$this->load->model('transaction_model');
		$this->current_module = $this->config->item('current_module');
//		$this->load->library('export');

	}
	function index()
	{
		redirect('general');
	}

	function monthly_booking_report()
	{
		$this->template->view('report/monthly_booking_report');
	}


	function bus($offset=0)
	{
		$get_data = $this->input->get();
		$condition = array();
		$page_data = array();
		if(valid_array($get_data) == true) {
			//From-Date and To-Date
			$from_date = trim(@$get_data['created_datetime_from']);
			$to_date = trim(@$get_data['created_datetime_to']);
			//Auto swipe date
			if(empty($from_date) == false && empty($to_date) == false)
			{
				$valid_dates = auto_swipe_dates($from_date, $to_date);
				$from_date = $valid_dates['from_date'];
				$to_date = $valid_dates['to_date'];
			}
			if(empty($from_date) == false) {
				$condition[] = array('BD.created_datetime', '>=', $this->db->escape(db_current_datetime($from_date)));
			}
			if(empty($to_date) == false) {
				$condition[] = array('BD.created_datetime', '<=', $this->db->escape(db_current_datetime($to_date)));
			}

			if (empty($get_data['created_by_id']) == false) {
				$condition[] = array('BD.created_by_id', '=', $this->db->escape($get_data['created_by_id']));
			}

			if (empty($get_data['status']) == false && strtolower($get_data['status']) != 'all') {
				$condition[] = array('BD.status', '=', $this->db->escape($get_data['status']));
			}

			if (empty($get_data['phone']) == false) {
				$condition[] = array('BD.phone_number', ' like ', $this->db->escape('%'.$get_data['phone'].'%'));
			}

			if (empty($get_data['email']) == false) {
				$condition[] = array('BD.email', ' like ', $this->db->escape('%'.$get_data['email'].'%'));
			}

			if (empty($get_data['app_reference']) == false) {
				$condition[] = array('BD.app_reference', ' like ', $this->db->escape('%'.$get_data['app_reference'].'%'));
			}
			$page_data['from_date'] = $from_date;
			$page_data['to_date'] = $to_date;
		}
		$total_records = $this->bus_model->booking($condition, true);
		$table_data = $this->bus_model->booking($condition, false, $offset, RECORDS_RANGE_2);
		$table_data = $this->booking_data_formatter->format_bus_booking_data($table_data,$this->current_module);
		$page_data['table_data'] = $table_data['data'];
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
		$page_data['search_params'] = $get_data;
		$page_data['status_options'] = get_enum_list('booking_status_options');
		$this->template->view('report/bus', $page_data);
	}

	function hotel($offset=0)
	{
		$condition = array();
		$get_data = $this->input->get();
		if(valid_array($get_data) == true) {
			//From-Date and To-Date
			$from_date = trim(@$get_data['created_datetime_from']);
			$to_date = trim(@$get_data['created_datetime_to']);
			//Auto swipe date
			if(empty($from_date) == false && empty($to_date) == false)
			{
				$valid_dates = auto_swipe_dates($from_date, $to_date);
				$from_date = $valid_dates['from_date'];
				$to_date = $valid_dates['to_date'];
			}
			if(empty($from_date) == false) {
				$condition[] = array('BD.created_datetime', '>=', $this->db->escape(db_current_datetime($from_date)));
			}
			if(empty($to_date) == false) {
				$condition[] = array('BD.created_datetime', '<=', $this->db->escape(db_current_datetime($to_date)));
			}

			if (empty($get_data['created_by_id']) == false) {
				$condition[] = array('BD.created_by_id', '=', $this->db->escape($get_data['created_by_id']));
			}

			if (empty($get_data['status']) == false && strtolower($get_data['status']) != 'all') {
				$condition[] = array('BD.status', '=', $this->db->escape($get_data['status']));
			}

			if (empty($get_data['phone']) == false) {
				$condition[] = array('BD.phone_number', ' like ', $this->db->escape('%'.$get_data['phone'].'%'));
			}

			if (empty($get_data['email']) == false) {
				$condition[] = array('BD.email', ' like ', $this->db->escape('%'.$get_data['email'].'%'));
			}

			if (empty($get_data['app_reference']) == false) {
				$condition[] = array('BD.app_reference', ' like ', $this->db->escape('%'.$get_data['app_reference'].'%'));
			}
			$page_data['from_date'] = $from_date;
			$page_data['to_date'] = $to_date;
		}
		$total_records = $this->hotel_model->booking($condition, true);
		$table_data = $this->hotel_model->booking($condition, false, $offset, RECORDS_RANGE_2);
		$table_data = $this->booking_data_formatter->format_hotel_booking_data($table_data,$this->current_module);
		$page_data['table_data'] = $table_data['data'];
		$this->load->library('pagination');
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['base_url'] = base_url().'index.php/report/hotel/';
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$page_data['total_rows'] = $config['total_rows'] = $total_records;
		$config['per_page'] = RECORDS_RANGE_2;
		$this->pagination->initialize($config);
		/** TABLE PAGINATION */
		$page_data['total_records'] = $config['total_rows'];
		//debug($page_data);exit;
		$page_data['search_params'] = $get_data;
		$page_data['status_options'] = get_enum_list('booking_status_options');
		$this->template->view('report/hotel', $page_data);
	}

	
	
	
	function b2c_bus_report($offset=0)
	{
		$get_data = $this->input->get();
		$condition = array();
		$page_data = array();

		$filter_data = $this->format_basic_search_filters('bus');
		$page_data['from_date'] = $filter_data['from_date'];
		$page_data['to_date'] = $filter_data['to_date'];
		$condition = $filter_data['filter_condition'];

		//debug($get_data); die;
		/*if(valid_array($get_data) == true) {
			//From-Date and To-Date
			$from_date = trim(@$get_data['created_datetime_from']);
			$to_date = trim(@$get_data['created_datetime_to']);
			//Auto swipe date
			if(empty($from_date) == false && empty($to_date) == false)
			{
				$valid_dates = auto_swipe_dates($from_date, $to_date);
				$from_date = $valid_dates['from_date'];
				$to_date = $valid_dates['to_date'];
			}
			if(empty($from_date) == false) {
				$condition[] = array('BD.created_datetime', '>=', $this->db->escape(db_current_datetime($from_date)));
			}
			if(empty($to_date) == false) {
				$condition[] = array('BD.created_datetime', '<=', $this->db->escape(db_current_datetime($to_date)));
			}
	
			if (empty($get_data['created_by_id']) == false) {
				$condition[] = array('BD.created_by_id', '=', $this->db->escape($get_data['created_by_id']));
			}
	
			if (empty($get_data['status']) == false && strtolower($get_data['status']) != 'all') {
				$condition[] = array('BD.status', '=', $this->db->escape($get_data['status']));
			}
	
			// if (empty($get_data['phone']) == false) {
			// 	$condition[] = array('BD.phone_number', ' like ', $this->db->escape('%'.$get_data['phone'].'%'));
			// }
	
			// if (empty($get_data['email']) == false) {
			// 	$condition[] = array('BD.email', ' like ', $this->db->escape('%'.$get_data['email'].'%'));
			// }
	
			if (empty($get_data['app_reference']) == false) {
				$condition[] = array('BD.app_reference', ' like ', $this->db->escape('%'.$get_data['app_reference'].'%'));
			}
			if (empty($get_data['pnr']) == false) {
				$condition[] = array('BD.pnr', ' like ', $this->db->escape('%'.$get_data['pnr'].'%'));
			}
			$page_data['from_date'] = $from_date;
			$page_data['to_date'] = $to_date;
		}*/
	
		$total_records = $this->bus_model->b2c_bus_report($condition, true);
		$table_data = $this->bus_model->b2c_bus_report($condition, false, $offset, RECORDS_RANGE_2);
		// debug($table_data); exit;
		$table_data = $this->booking_data_formatter->format_bus_booking_data($table_data,$this->current_module);
		
		$page_data['table_data'] = $table_data['data'];

		// debug($table_data); exit;

		/** TABLE PAGINATION */
		$this->load->library('pagination');
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['base_url'] = base_url().'index.php/report/b2c_bus_report/';
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$page_data['total_rows'] = $config['total_rows'] = $total_records;
		$config['per_page'] = RECORDS_RANGE_2;
		$this->pagination->initialize($config);
		/** TABLE PAGINATION */
		$page_data['total_records'] = $config['total_rows'];
		$page_data['customer_email'] = $this->entity_email;
		$page_data['search_params'] = $get_data;
		$page_data['status_options'] = get_enum_list('booking_status_options');
		//debug($page_data); die;
		$this->template->view('report/b2c_report_bus', $page_data);
	}
	
	
	function b2b_bus_report($offset=0)
	{
		$get_data = $this->input->get();
		$condition = array();
		$page_data = array();

		$filter_data = $this->format_basic_search_filters('bus', 1);
		$page_data['from_date'] = $filter_data['from_date'];
		$page_data['to_date'] = $filter_data['to_date'];
		$condition = $filter_data['filter_condition'];

		//debug($condition); die;
		$total_records = $this->bus_model->b2b_bus_report($condition, true);
		$table_data = $this->bus_model->b2b_bus_report($condition, false, $offset, RECORDS_RANGE_2);
		$table_data = $this->booking_data_formatter->format_bus_booking_data($table_data,'b2b');
		$page_data['table_data'] = $table_data['data'];
		/** TABLE PAGINATION */
		$this->load->library('pagination');
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['base_url'] = base_url().'index.php/report/b2b_bus_report/';
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$page_data['total_rows'] = $config['total_rows'] = $total_records;
		$config['per_page'] = RECORDS_RANGE_2;
		$this->pagination->initialize($config);
		/** TABLE PAGINATION */
		$page_data['total_records'] = $config['total_rows'];
		$page_data['customer_email'] = $this->entity_email;
		$page_data['search_params'] = $get_data;
		$page_data['status_options'] = get_enum_list('booking_status_options');

		$agent_info = $this->custom_db->single_table_records('user','*',array('user_type'=>B2B_USER,'domain_list_fk'=>get_domain_auth_id()));
		
		$page_data['agent_details'] = magical_converter(array('k' => 'user_id', 'v' => 'agency_name'), $agent_info);
		$page_data["supplier_list"] = $this->domain_management_model->get_all_suplliers(META_BUS_COURSE);
		$page_data["bitla_direct_api_list"] = $this->bus_model->bitla_direct_api_list();
		$page_data['agency_list'] = $this->domain_management_model->get_agent_list();
		$this->template->view('report/b2b_report_bus', $page_data);
	}
	
	
	
	function b2b_hotel_report($offset=0)
	{
		$condition = array();
		$get_data = $this->input->get();

		$filter_data = $this->format_basic_search_filters();
		$page_data['from_date'] = $filter_data['from_date'];
		$page_data['to_date'] = $filter_data['to_date'];
		$condition = $filter_data['filter_condition'];
		
		$total_records = $this->hotel_model->b2b_hotel_report($condition, true);
		$table_data = $this->hotel_model->b2b_hotel_report($condition, false, $offset, RECORDS_RANGE_2);
		$table_data = $this->booking_data_formatter->format_hotel_booking_data($table_data, $this->current_module);
		$page_data['table_data'] = $table_data['data'];
		$this->load->library('pagination');
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['base_url'] = base_url().'index.php/report/b2b_hotel_report/';
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$page_data['total_rows'] = $config['total_rows'] = $total_records;
		$config['per_page'] = RECORDS_RANGE_2;
		$this->pagination->initialize($config);
		/** TABLE PAGINATION */
		$page_data['total_records'] = $config['total_rows'];
		//debug($page_data);exit;
		$page_data['search_params'] = $get_data;
		$page_data['status_options'] = get_enum_list('booking_status_options');
		
		$agent_info = $this->custom_db->single_table_records('user','*',array('user_type'=>B2B_USER,'domain_list_fk'=>get_domain_auth_id()));
		
		$page_data['agent_details'] = magical_converter(array('k' => 'user_id', 'v' => 'agency_name'), $agent_info);
		$page_data['agency_list'] = $this->domain_management_model->get_agent_list();
		$page_data["supplier_list"] = $this->domain_management_model->get_all_suplliers(META_ACCOMODATION_COURSE);
		$this->template->view('report/b2b_report_hotel', $page_data);
	}
	
	
	function b2c_hotel_report($offset=0)
	{
		$condition = array();
		$get_data = $this->input->get();

		$filter_data = $this->format_basic_search_filters();
		$page_data['from_date'] = $filter_data['from_date'];
		$page_data['to_date'] = $filter_data['to_date'];
		$condition = $filter_data['filter_condition'];

		/*if(valid_array($get_data) == true) {
			//From-Date and To-Date
			$from_date = trim(@$get_data['created_datetime_from']);
			$to_date = trim(@$get_data['created_datetime_to']);
			//Auto swipe date
			if(empty($from_date) == false && empty($to_date) == false)
			{
				$valid_dates = auto_swipe_dates($from_date, $to_date);
				$from_date = $valid_dates['from_date'];
				$to_date = $valid_dates['to_date'];
			}
			if(empty($from_date) == false) {
				$condition[] = array('BD.created_datetime', '>=', $this->db->escape(db_current_datetime($from_date)));
			}
			if(empty($to_date) == false) {
				$condition[] = array('BD.created_datetime', '<=', $this->db->escape(db_current_datetime($to_date)));
			}
	
			if (empty($get_data['created_by_id']) == false) {
				$condition[] = array('BD.created_by_id', '=', $this->db->escape($get_data['created_by_id']));
			}
	
			if (empty($get_data['status']) == false && strtolower($get_data['status']) != 'all') {
				$condition[] = array('BD.status', '=', $this->db->escape($get_data['status']));
			}
	
			// if (empty($get_data['phone']) == false) {
			// 	$condition[] = array('BD.phone_number', ' like ', $this->db->escape('%'.$get_data['phone'].'%'));
			// }
	
			// if (empty($get_data['email']) == false) {
			// 	$condition[] = array('BD.email', ' like ', $this->db->escape('%'.$get_data['email'].'%'));
			// }
	
			if (empty($get_data['app_reference']) == false) {
				$condition[] = array('BD.app_reference', 'like',$this->db->escape('%'.$get_data['app_reference'].'%'));
			}
			$page_data['from_date'] = $from_date;
			$page_data['to_date'] = $to_date;
		}*/
		//debug($this->session->userdata('id'));die;
		$total_records = $this->hotel_model->b2c_hotel_report($condition, true);	
	//	debug($total_records); die;
		$table_data = $this->hotel_model->b2c_hotel_report($condition, false, $offset, RECORDS_RANGE_2);
			//debug($table_data['data']); exit;
		$table_data = $this->booking_data_formatter->format_hotel_booking_data($table_data,$this->current_module);
		$page_data['table_data'] = $table_data['data'];
		$this->load->library('pagination');
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['base_url'] = base_url().'index.php/report/b2c_hotel_report/';
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$page_data['total_rows'] = $config['total_rows'] = $total_records;
		$config['per_page'] = RECORDS_RANGE_2;
		$this->pagination->initialize($config);
		/** TABLE PAGINATION */
		$page_data['total_records'] = $config['total_rows'];
		//debug($page_data);exit;
		$page_data['search_params'] = $get_data;
		$page_data['status_options'] = get_enum_list('booking_status_options');
		$this->template->view('report/b2c_report_hotel', $page_data);
	}
	/*B2c sightseeing Report*/
	function b2c_activities_report($offset=0)
	{
		$condition = array();
		$get_data = $this->input->get();

		$filter_data = $this->format_basic_search_filters();
		$page_data['from_date'] = $filter_data['from_date'];
		$page_data['to_date'] = $filter_data['to_date'];
		$condition = $filter_data['filter_condition'];

		$total_records = $this->sightseeing_model->b2c_sightseeing_report($condition, true);	
		
	//	debug($total_records); die;
		$table_data = $this->sightseeing_model->b2c_sightseeing_report($condition, false, $offset, RECORDS_RANGE_2);
			//debug($table_data['data']); exit;
		$table_data = $this->booking_data_formatter->format_sightseeing_booking_data($table_data,$this->current_module);
		$page_data['table_data'] = $table_data['data'];
		$this->load->library('pagination');
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['base_url'] = base_url().'index.php/report/b2c_sightseeing_report/';
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$page_data['total_rows'] = $config['total_rows'] = $total_records;
		$config['per_page'] = RECORDS_RANGE_2;
		$this->pagination->initialize($config);
		/** TABLE PAGINATION */
		$page_data['total_records'] = $config['total_rows'];
		//debug($page_data);exit;
		$page_data['search_params'] = $get_data;
		$page_data['status_options'] = get_enum_list('booking_status_options');
		$this->template->view('report/b2c_report_sightseeing', $page_data);
	}
		/**
	 * Sightseeing Report for b2b flight
	 * @param $offset
	 */
	function b2b_activities_report($offset=0)
	{
		$current_user_id = $GLOBALS['CI']->entity_user_id;
		$get_data = $this->input->get();
		$condition = array();
		$filter_data = $this->format_basic_search_filters();
		$page_data['from_date'] = $filter_data['from_date'];
		$page_data['to_date'] = $filter_data['to_date'];
		$condition = $filter_data['filter_condition'];

		$total_records = $this->sightseeing_model->b2b_sightseeing_report($condition, true);
		//echo '<pre>'; print_r($page_data); die;
		$table_data = $this->sightseeing_model->b2b_sightseeing_report($condition, false, $offset, RECORDS_RANGE_2);
		$table_data = $this->booking_data_formatter->format_sightseeing_booking_data($table_data, $this->current_module);
		// debug($table_data);
		// exit;
		$page_data['table_data'] = $table_data['data'];
		
		$this->load->library('pagination');
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['base_url'] = base_url().'index.php/report/b2b_sightseeing_report/';
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$page_data['total_rows'] = $config['total_rows'] = $total_records;
		$config['per_page'] = RECORDS_RANGE_2;
		$this->pagination->initialize($config);
		/** TABLE PAGINATION */
		$page_data['total_records'] = $config['total_rows'];
		$page_data['search_params'] = $get_data;
		$page_data['status_options'] = get_enum_list('booking_status_options');

		$user_cond = [];
		$user_cond [] = array('U.user_type','=',' (', B2B_USER, ')');
		$user_cond [] = array('U.domain_list_fk' , '=' ,get_domain_auth_id());

		//$agent_info['data'] = $this->user_model->b2b_user_list($user_cond,false);

		$agent_info = $this->custom_db->single_table_records('user','*',array('user_type'=>B2B_USER,'domain_list_fk'=>get_domain_auth_id()));

		$page_data['agent_details'] = magical_converter(array('k' => 'user_id', 'v' => 'agency_name'), $agent_info);
		$page_data['agency_list'] = $this->domain_management_model->get_agent_list();			
		$page_data["supplier_list"] = $this->domain_management_model->get_all_suplliers(META_SIGHTSEEING_COURSE);
		$this->template->view('report/b2b_sightseeing', $page_data);
	}
	/*B2B Transfer Report*/
	function b2b_transfers_report($offset=0){
		$current_user_id = $GLOBALS['CI']->entity_user_id;
		$get_data = $this->input->get();
		$condition = array();
		$filter_data = $this->format_basic_search_filters();
		$page_data['from_date'] = $filter_data['from_date'];
		$page_data['to_date'] = $filter_data['to_date'];
		$condition = $filter_data['filter_condition'];

		$total_records = $this->transferv1_model->b2b_transferv1_report($condition, true);
		//echo '<pre>'; print_r($page_data); die;
		$table_data = $this->transferv1_model->b2b_transferv1_report($condition, false, $offset, RECORDS_RANGE_2);
		$table_data = $this->booking_data_formatter->format_transferv1_booking_data($table_data, $this->current_module);
		// debug($table_data);
		// exit;
		$page_data['table_data'] = $table_data['data'];
		
		$this->load->library('pagination');
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['base_url'] = base_url().'index.php/report/b2b_transfers_report/';
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$page_data['total_rows'] = $config['total_rows'] = $total_records;
		$config['per_page'] = RECORDS_RANGE_2;
		$this->pagination->initialize($config);
		/** TABLE PAGINATION */
		$page_data['total_records'] = $config['total_rows'];
		$page_data['search_params'] = $get_data;
		$page_data['status_options'] = get_enum_list('booking_status_options');

		$user_cond = [];
		$user_cond [] = array('U.user_type','=',' (', B2B_USER, ')');
		$user_cond [] = array('U.domain_list_fk' , '=' ,get_domain_auth_id());

		//$agent_info['data'] = $this->user_model->b2b_user_list($user_cond,false);

		$agent_info = $this->custom_db->single_table_records('user','*',array('user_type'=>B2B_USER,'domain_list_fk'=>get_domain_auth_id()));

		$page_data['agent_details'] = magical_converter(array('k' => 'user_id', 'v' => 'agency_name'), $agent_info);		
		$page_data['agency_list'] = $this->domain_management_model->get_agent_list();			
		$page_data["supplier_list"] = $this->domain_management_model->get_all_suplliers(META_TRANSFERV1_COURSE);
		$this->template->view('report/b2b_transfer', $page_data);
	}
	/*B2c Transfer Report*/
	function b2c_transfers_report($offset=0)
	{
		$condition = array();
		$get_data = $this->input->get();

		$filter_data = $this->format_basic_search_filters();
		$page_data['from_date'] = $filter_data['from_date'];
		$page_data['to_date'] = $filter_data['to_date'];
		$condition = $filter_data['filter_condition'];

		$total_records = $this->transferv1_model->b2c_transferv1_report($condition, true);	
		
	//	debug($total_records); die;
		$table_data = $this->transferv1_model->b2c_transferv1_report($condition, false, $offset, RECORDS_RANGE_2);
			//debug($table_data['data']); exit;
		$table_data = $this->booking_data_formatter->format_transferv1_booking_data($table_data,$this->current_module);
		$page_data['table_data'] = $table_data['data'];
		$this->load->library('pagination');
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['base_url'] = base_url().'index.php/report/b2c_transfers_report/';
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$page_data['total_rows'] = $config['total_rows'] = $total_records;
		$config['per_page'] = RECORDS_RANGE_2;
		$this->pagination->initialize($config);
		/** TABLE PAGINATION */
		$page_data['total_records'] = $config['total_rows'];
		//debug($page_data);exit;
		$page_data['search_params'] = $get_data;
		$page_data['status_options'] = get_enum_list('booking_status_options');
		$this->template->view('report/b2c_transferv1_report', $page_data);
	}
	/* car reports */
	
	function b2c_car_report($offset=0)
	{
		$get_data = $this->input->get();
        $condition = array();
        $page_data = array();
        $filter_data = $this->format_basic_search_filters('bus');
        $page_data['from_date'] = $filter_data['from_date'];
        $page_data['to_date'] = $filter_data['to_date'];
        $condition = $filter_data['filter_condition'];

        $total_records = $this->car_model->b2c_car_report($condition, true);
   
        $table_data = $this->car_model->b2c_car_report($condition, false, $offset, RECORDS_RANGE_2);
       
        $table_data = $this->booking_data_formatter->format_car_booking_datas($table_data , $this->current_module);
       	// debug($table_data);exit;
       	$page_data['table_data'] = $table_data['data'];
        
        /** TABLE PAGINATION */
        $this->load->library('pagination');
        if (count($_GET) > 0)
            $config['suffix'] = '?' . http_build_query($_GET, '', "&");
        $config['base_url'] = base_url() . 'index.php/report/car/';
        $config['first_url'] = $config['base_url'] . '?' . http_build_query($_GET);
        $page_data['total_rows'] = $config['total_rows'] = $total_records;
        $config['per_page'] = RECORDS_RANGE_2;
        $this->pagination->initialize($config);
        /** TABLE PAGINATION */
        $page_data['total_records'] = $config['total_rows'];
        $page_data['customer_email'] = $this->entity_email;
       $page_data['search_params'] = $get_data;
        $page_data['status_options'] = get_enum_list('booking_status_options');
        $this->template->view('report/b2c_car_report', $page_data);
        

	}
	/* car reports  for B2B*/
	
	function b2b_car_report($offset=0)
	{
		$get_data = $this->input->get();
        $condition = array();
        $page_data = array();
        $filter_data = $this->format_basic_search_filters('bus');
        $page_data['from_date'] = $filter_data['from_date'];
        $page_data['to_date'] = $filter_data['to_date'];
        $condition = $filter_data['filter_condition'];

        $total_records = $this->car_model->b2b_car_report($condition, true);
   
        $table_data = $this->car_model->b2b_car_report($condition, false, $offset, RECORDS_RANGE_2);
       	// echo $this->current_module;exit;
        $table_data = $this->booking_data_formatter->format_car_booking_datas($table_data , $this->current_module);
       	// debug($table_data);exit;
       	$page_data['table_data'] = $table_data['data'];
        
        /** TABLE PAGINATION */
        $this->load->library('pagination');
        if (count($_GET) > 0)
            $config['suffix'] = '?' . http_build_query($_GET, '', "&");
        $config['base_url'] = base_url() . 'index.php/report/car/';
        $config['first_url'] = $config['base_url'] . '?' . http_build_query($_GET);
        $page_data['total_rows'] = $config['total_rows'] = $total_records;
        $config['per_page'] = RECORDS_RANGE_2;
        $this->pagination->initialize($config);
        /** TABLE PAGINATION */
        $page_data['total_records'] = $config['total_rows'];
        $page_data['customer_email'] = $this->entity_email;
       $page_data['search_params'] = $get_data;
        $page_data['status_options'] = get_enum_list('booking_status_options');
        $this->template->view('report/b2c_car_report', $page_data);
        

	}
	
	/**
	 * Flight Report
	 * @param $offset
	 */
	function flight($offset=0)
	{
		$current_user_id = $GLOBALS['CI']->entity_user_id;
		$get_data = $this->input->get();
		$condition = array();
		if(valid_array($get_data) == true) {
			//From-Date and To-Date
			$from_date = trim(@$get_data['created_datetime_from']);
			$to_date = trim(@$get_data['created_datetime_to']);
			//Auto swipe date
			if(empty($from_date) == false && empty($to_date) == false)
			{
				$valid_dates = auto_swipe_dates($from_date, $to_date);
				$from_date = $valid_dates['from_date'];
				$to_date = $valid_dates['to_date'];
			}
			if(empty($from_date) == false) {
				$condition[] = array('BD.created_datetime', '>=', $this->db->escape(db_current_datetime($from_date)));
			}
			if(empty($to_date) == false) {
				$condition[] = array('BD.created_datetime', '<=', $this->db->escape(db_current_datetime($to_date)));
			}

			if (empty($get_data['created_by_id']) == false) {
				$condition[] = array('BD.created_by_id', '=', $this->db->escape($get_data['created_by_id']));
			}

			if (empty($get_data['status']) == false && strtolower($get_data['status']) != 'all') {
				$condition[] = array('BD.status', '=', $this->db->escape($get_data['status']));
			}

			if (empty($get_data['phone']) == false) {
				$condition[] = array('BD.phone', ' like ', $this->db->escape('%'.$get_data['phone'].'%'));
			}

			if (empty($get_data['email']) == false) {
				$condition[] = array('BD.email', ' like ', $this->db->escape('%'.$get_data['email'].'%'));
			}

			if (empty($get_data['app_reference']) == false) {
				$condition[] = array('BD.app_reference', ' like ', $this->db->escape('%'.$get_data['app_reference'].'%'));
			}
			$page_data['from_date'] = $from_date;
			$page_data['to_date'] = $to_date;
		}
		$total_records = $this->flight_model->booking($condition, true);
		$table_data = $this->flight_model->booking($condition, false, $offset, RECORDS_RANGE_2);
		$table_data = $this->booking_data_formatter->format_flight_booking_data($table_data,$this->current_module);
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
		$page_data['search_params'] = $get_data;
		$page_data['status_options'] = get_enum_list('booking_status_options');
		$this->template->view('report/airline', $page_data);
	}
	
	/**
	 * Flight Report for b2c flight
	 * @param $offset
	 */
	function b2c_flight_report($offset=0)
	{
		$current_user_id = $GLOBALS['CI']->entity_user_id;
		$get_data = $this->input->get();
		//debug($get_data); die;
		$condition = array();

		$filter_data = $this->format_basic_search_filters();
		$page_data['from_date'] = $filter_data['from_date'];
		$page_data['to_date'] = $filter_data['to_date'];
		$condition = $filter_data['filter_condition'];
		
		//$condition[] = array('U.user_type', '=', B2C_USER, ' OR ', 'BD.created_by_id');
		$total_records = $this->flight_model->b2c_flight_report($condition, true);		
		
		$table_data = $this->flight_model->b2c_flight_report($condition, false, $offset, RECORDS_RANGE_2);
		$table_data = $this->booking_data_formatter->format_flight_booking_data($table_data, 'b2c', false);
		
		//Export report


		$page_data['table_data'] = $table_data['data'];
		$this->load->library('pagination');
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['base_url'] = base_url().'index.php/report/b2c_flight_report/';
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$page_data['total_rows'] = $config['total_rows'] = $total_records;
		$config['per_page'] = RECORDS_RANGE_2;
		$this->pagination->initialize($config);
                
               
                
                
		/** TABLE PAGINATION */
		$page_data['total_records'] = $config['total_rows'];
		$page_data['search_params'] = $get_data;
		$page_data['status_options'] = get_enum_list('booking_status_options');
		$this->template->view('report/b2c_report_airline', $page_data);
	}
	

	
	/**
	 * Flight Report for b2b flight
	 * @param $offset
	 */
	function b2b_flight_report($offset=0)
	{
		$current_user_id = $GLOBALS['CI']->entity_user_id;
		$get_data = $this->input->get();
		$condition = array();
		$filter_data = $this->format_basic_search_filters();
		$page_data['from_date'] = $filter_data['from_date'];
		$page_data['to_date'] = $filter_data['to_date'];
		$condition = $filter_data['filter_condition'];

		$total_records = $this->flight_model->b2b_flight_report($condition, true);
		
		$table_data = $this->flight_model->b2b_flight_report($condition, false, $offset, RECORDS_RANGE_2);
		//echo '<pre>'; print_r($table_data); die;
		$table_data = $this->booking_data_formatter->format_flight_booking_data($table_data, $this->current_module);
		$page_data['table_data'] = $table_data['data'];
		
		$this->load->library('pagination');
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['base_url'] = base_url().'index.php/report/b2b_flight_report/';
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$page_data['total_rows'] = $config['total_rows'] = $total_records;
		$config['per_page'] = RECORDS_RANGE_2;
		$this->pagination->initialize($config);
		/** TABLE PAGINATION */
		$page_data['total_records'] = $config['total_rows'];
		$page_data['search_params'] = $get_data;
		$page_data['status_options'] = get_enum_list('booking_status_options');

		$user_cond = [];
		$user_cond [] = array('U.user_type','=',' (', B2B_USER, ')');
		$user_cond [] = array('U.domain_list_fk' , '=' ,get_domain_auth_id());

		//$agent_info['data'] = $this->user_model->b2b_user_list($user_cond,false);

		$agent_info = $this->custom_db->single_table_records('user','*',array('user_type'=>B2B_USER,'domain_list_fk'=>get_domain_auth_id()));

		$page_data['agent_details'] = magical_converter(array('k' => 'user_id', 'v' => 'agency_name'), $agent_info);	
		$page_data['agency_list'] = $this->domain_management_model->get_agent_list();	
		$page_data["supplier_list"] = $this->domain_management_model->get_all_suplliers(META_AIRLINE_COURSE);
		$this->template->view('report/b2b_report_airline', $page_data);
	}
	
	
	function update_flight_booking_details($app_reference, $booking_source)
	{
		load_flight_lib($booking_source);
		$this->flight_lib->update_flight_booking_details($app_reference);
		//FIXME: Return the status
	}
	
	/**
	 * Sagar Wakchaure
	 *Update pnr Details 
	 * @param unknown $app_reference
	 * @param unknown $booking_source
	 * @param unknown $booking_status
	 */
	function update_pnr_details($app_reference, $booking_source,$booking_status)
	{
              
		load_flight_lib($booking_source);
		$response = $this->flight_lib->update_pnr_details($app_reference);
              
		$get_pnr_updated_status = $this->flight_model->update_pnr_details($response,$app_reference, $booking_source,$booking_status);
		echo $get_pnr_updated_status;
	}
	
function package()
	{
		echo '<h4>Under Working</h4>';
	}
	
	function b2b_package_report()
	{
		echo '<h4>Under Working</h4>';
	}
	
	function b2c_package_report()
	{
		echo '<h4>Under Working</h4>';
	}

	private function format_basic_search_filters($module='', $is_travel_id = 0)
	{
		$get_data = $this->input->get();
		//debug($get_data);die('8');

		if(valid_array($get_data) == true) {
			$filter_condition = array();
			if(isset($get_data['supplier_id']) == true && empty($get_data['supplier_id']) == false) {
				$filter_condition[] = array('BD.booking_source', '=', '"'.$get_data['supplier_id'].'"');
			}
			//From-Date and To-Date
			$from_date = trim(@$get_data['created_datetime_from']);
			$to_date = trim(@$get_data['created_datetime_to']);
			//Auto swipe date
			if(empty($from_date) == false && empty($to_date) == false)
			{
				$valid_dates = auto_swipe_dates($from_date, $to_date);
				$from_date = $valid_dates['from_date'].' 00:00:00';
				$to_date = $valid_dates['to_date'].' 23:59:59';
			}
			if(empty($from_date) == false) {
				$filter_condition[] = array('BD.created_datetime', '>=', $this->db->escape(db_current_datetime($from_date)));
			}
			if(empty($to_date) == false) {
				$filter_condition[] = array('BD.created_datetime', '<=', $this->db->escape(db_current_datetime($to_date)));
			}
	
			/*if (empty($get_data['created_by_id']) == false) {
				$filter_condition[] = array('BD.created_by_id', '=', $this->db->escape($get_data['created_by_id']));
			}*/
			
			if (empty($get_data['created_by_id']) == false && strtolower($get_data['created_by_id'])!='all') {
				$filter_condition[] = array('BD.created_by_id', '=', $this->db->escape($get_data['created_by_id']));
			}
	
			if (empty($get_data['status']) == false && strtolower($get_data['status']) != 'all') {
				$filter_condition[] = array('BD.status', '=', $this->db->escape($get_data['status']));
			}
		
			/*if (empty($get_data['phone']) == false) {
				$filter_condition[] = array('BD.phone', ' like ', $this->db->escape('%'.$get_data['phone'].'%'));
			}
	
			if (empty($get_data['email']) == false) {
				$filter_condition[] = array('BD.email', ' like ', $this->db->escape('%'.$get_data['email'].'%'));
			}*/
			
			if($module == 'bus'){
				if (empty($get_data['pnr']) == false) {
					$filter_condition[] = array('BD.pnr', ' like ', $this->db->escape('%'.$get_data['pnr'].'%'));
				}
				if(isset($get_data['travel_id']) && $is_travel_id)
				{
					$filter_condition[] = array('BD.travel_id', ' = ', $this->db->escape($get_data['travel_id']));
				}
				
			}else{
				if (empty($get_data['pnr']) == false) {
					$filter_condition[] = array('BT.pnr', ' like ', $this->db->escape('%'.$get_data['pnr'].'%'));
				}
			}
			
	
			if (empty($get_data['app_reference']) == false) {
				$filter_condition[] = array('BD.app_reference', ' like ', $this->db->escape('%'.$get_data['app_reference'].'%'));
			}
			
			$page_data['from_date'] = $from_date;
			$page_data['to_date'] = $to_date;

			//Today's Booking Data
			if(isset($get_data['today_booking_data']) == true && empty($get_data['today_booking_data']) == false) {
				$filter_condition[] = array('DATE(BD.created_datetime)', '=', '"'.date('Y-m-d').'"');
			}
			//Last day Booking Data
			if(isset($get_data['last_day_booking_data']) == true && empty($get_data['last_day_booking_data']) == false) {
				$filter_condition[] = array('DATE(BD.created_datetime)', '=', '"'.trim($get_data['last_day_booking_data']).'"');
			}
			//Previous Booking Data: last 3 days, 7 days, 15 days, 1 month and 3 month
			if(isset($get_data['prev_booking_data']) == true && empty($get_data['prev_booking_data']) == false) {
				$filter_condition[] = array('DATE(BD.created_datetime)', '>=', '"'.trim($get_data['prev_booking_data']).'"');
			}

			if (empty($get_data['source_id']) == false && strtolower($get_data['source_id']) != 'all') {
				$filter_condition[] = array('BD.booking_source', '=', $this->db->escape($get_data['source_id']));
			}
			
			return array('filter_condition' => $filter_condition, 'from_date' => $from_date, 'to_date' => $to_date);
		}
	}
	# Get Pending Refund
	public function get_customer_details($app_reference,$booking_source,$booking_status, $module)
	{

        if($module == 'flight'){
        	$booking_details = $this->flight_model->get_booking_details($app_reference, $booking_source, $booking_status);
		}
		else if($module == 'hotel'){
			$booking_details = $this->hotel_model->get_booking_details($app_reference, $booking_source, $booking_status);
	
		}
		else if($module == 'bus'){
			$booking_details = $this->bus_model->get_booking_details($app_reference, $booking_source, $booking_status);
	
		}
		//debug($booking_details);exit;
		$booking_details['module'] = $module;
       
            if($booking_details['status'] == SUCCESS_STATUS && valid_array($booking_details['data']) ==true){
				$from_loc = $booking_details['data']['booking_details'][0]['from_loc'];
				$to_loc = $booking_details['data']['booking_details'][0]['to_loc'];
				$booking_details["is_domestic"] = $this->flight_model->is_domestic_flight($from_loc, $to_loc);
				$response['data'] = get_compressed_output(
				$this->template->isolated_view('report/customer_details',
						array('customer_details' => $booking_details,)));
			}

        $this->output_compressed_data($response); 
        
	}
	private function output_compressed_data($data)
	{	
	
            ini_set('always_populate_raw_post_data', '-1');
            
	   while (ob_get_level() > 0) { ob_end_clean() ; }
	   ob_start("ob_gzhandler");
	   ini_set("memory_limit", "-1");set_time_limit(0);
	   header('Content-type:application/json');
	   
		echo json_encode($data);
	    ob_end_flush();
	   exit;
	}
	 /*
     * For Confirmed Booking
     * Export AirlineReport details to Excel Format or PDF
     */

    public function export_confirmed_booking_airline_report($op = '') {
        $this->load->model('flight_model');
        $get_data = $this->input->get();
        $condition = array();
        //From-Date and To-Date
        $from_date = trim(@$get_data['created_datetime_from']);
        $to_date = trim(@$get_data['created_datetime_to']);

        $filter_data = $this->format_basic_search_filters('flight');
        $condition = $filter_data['filter_condition'];

        //Unset the Status Filter
        if (valid_array($condition) == true) {
            foreach ($condition as $ck => $cv) {

                if ($cv[0] == 'BD.status') {
                    unset($condition[$ck]);
                }
            }
        }

        //Adding Confirmed Status Filter
        $condition[] = array('BD.status', '=', $this->db->escape('BOOKING_CONFIRMED'));

        $flight_booking_data = $this->flight_model->b2c_flight_report($condition, false, 0, 2000); //Maximum 500 Data Can be exported at time
        $flight_booking_data = $this->booking_data_formatter->format_flight_booking_data($flight_booking_data, $this->current_module);
        $flight_booking_data = $flight_booking_data['data']['booking_details'];



        $export_data = array();
        // debug($flight_booking_data);exit;
        foreach ($flight_booking_data as $k => $v) {
           
			$export_data[$k]['app_reference'] = $v['app_reference'];
            $export_data[$k]['lead_pax_name'] = $v['lead_pax_name'];
            $export_data[$k]['lead_pax_email'] = $v['lead_pax_email'];
            $export_data[$k]['lead_pax_phone_number'] = $v['lead_pax_phone_number'];
            $export_data[$k]['gds_pnr'] = $v['pnr'];
            $export_data[$k]['airline_pnr'] = $v['booking_itinerary_details'][0]['airline_pnr'];
            $export_data[$k]['airline_code'] = $v['booking_itinerary_details'][0]['airline_code'];
            $export_data[$k]['journey_from'] = $v['journey_from'];
           	$export_data[$k]['journey_to'] = $v['journey_to'];
           	$export_data[$k]['journey_start'] = $v['journey_start'];
           	$export_data[$k]['journey_end'] = $v['journey_end'];
           	$export_data[$k]['commission_fare'] = $v['fare'];
           	$export_data[$k]['commission'] = $v['net_commission'];
           	$export_data[$k]['tds'] = $v['net_commission_tds'];
           	$export_data[$k]['net_fare'] = $v['net_fare'];
           	$export_data[$k]['gst'] = $v['gst'];
           	$export_data[$k]['convinence_amount'] = $v['convinence_amount'];
           	$export_data[$k]['discount'] = $v['discount'];
           	$export_data[$k]['grand_total'] = $v['grand_total'];
           	$export_data[$k]['booked_date'] = date('d-m-Y', strtotime($v['booked_date']));
           	
        }

        if ($op == 'excel') { // excel export
           $headings = array('a1' => 'Sl. No.',
                    'b1' => 'Appreference',
                    'c1' => 'Lead Pax Name',
                    'd1' => 'Lead Pax Email',
                    'e1' => 'Lead Pax Phone',
                    'f1' => 'GDS PNR',
                    'g1' => 'Airline PNR',
                    'h1' => 'Airline Code',
                    'i1' => 'From',
                    'j1' => 'To',
                    'k1' => 'Form Date',
                    'l1' => 'To Date',
                    'm1' => 'Commission Fare',
					'n1' => 'Commission',
                    'o1' => 'TDS',
                    'p1' => 'Net Fare',
                    'q1' => 'GST',
                    'r1' => 'Convinence Amount',
                    's1' => 'Discount',
                    't1' => 'Customer Paid',
                    'u1' => 'Booked Date',
                );
                // field names in data set 
                $fields = array('a' => '', // empty for sl. no.
                    'b' => 'app_reference',
                    'c' => 'lead_pax_name',
                    'd' => 'lead_pax_email',
                    'e' => 'lead_pax_phone_number',
                    'f' => 'gds_pnr',
                    'g' => 'airline_pnr',
                    'h' => 'airline_code',
                    'i' => 'journey_from',
                    'j' => 'journey_to',
                    'k' => 'journey_start',
                    'l' => 'journey_end',
                    'm' => 'commission_fare',
                    'n' => 'commission',
                    'o' => 'tds',
                    'p' => 'net_fare',
                    'q' => 'gst',
                    'r' => 'convinence_amount',
                    's' => 'discount',
                    't' => 'grand_total',
                    'u' => 'booked_date',
                    
                );
           
            $excel_sheet_properties = array(
                'title' => 'Confirmed_Booking_AirlineReport_' . date('d-M-Y'),
                'creator' => 'Accentria Solutions',
                'description' => 'Confirmed_Booking_AirlineReport',
                'sheet_title' => 'Confirmed_Booking_AirlineReport'
            );

            $this->load->library('provab_excel'); // we need this provab_excel library to export excel.
            $this->provab_excel->excel_export($headings, $fields, $export_data, $excel_sheet_properties);
        } 
    }
     /*
     * For Cancelled Booking
     * Export AirlineReport details to Excel Format or PDF
     */

    public function export_cancelled_booking_airline_report($op = '') {
        $this->load->model('flight_model');
        $get_data = $this->input->get();
        $condition = array();
        //From-Date and To-Date
        $from_date = trim(@$get_data['created_datetime_from']);
        $to_date = trim(@$get_data['created_datetime_to']);

        $filter_data = $this->format_basic_search_filters('flight');
        $condition = $filter_data['filter_condition'];

        //Unset the Status Filter
        if (valid_array($condition) == true) {
            foreach ($condition as $ck => $cv) {

                if ($cv[0] == 'BD.status') {
                    unset($condition[$ck]);
                }
            }
        }

        //Adding Confirmed Status Filter
        $condition[] = array('BD.status', '=', $this->db->escape('BOOKING_CANCELLED'));

        $flight_booking_data = $this->flight_model->b2c_flight_report($condition, false, 0, 2000); //Maximum 500 Data Can be exported at time
        $flight_booking_data = $this->booking_data_formatter->format_flight_booking_data($flight_booking_data, $this->current_module);
        $flight_booking_data = $flight_booking_data['data']['booking_details'];



        $export_data = array();
        // debug($flight_booking_data);exit;
        foreach ($flight_booking_data as $k => $v) {
           
			$export_data[$k]['app_reference'] = $v['app_reference'];
            $export_data[$k]['lead_pax_name'] = $v['lead_pax_name'];
            $export_data[$k]['lead_pax_email'] = $v['lead_pax_email'];
            $export_data[$k]['lead_pax_phone_number'] = $v['lead_pax_phone_number'];
            $export_data[$k]['gds_pnr'] = $v['pnr'];
            $export_data[$k]['airline_pnr'] = $v['booking_itinerary_details'][0]['airline_pnr'];
            $export_data[$k]['airline_code'] = $v['booking_itinerary_details'][0]['airline_code'];
            $export_data[$k]['journey_from'] = $v['journey_from'];
           	$export_data[$k]['journey_to'] = $v['journey_to'];
           	$export_data[$k]['journey_start'] = $v['journey_start'];
           	$export_data[$k]['journey_end'] = $v['journey_end'];
           	$export_data[$k]['commission_fare'] = $v['fare'];
           	$export_data[$k]['commission'] = $v['net_commission'];
           	$export_data[$k]['tds'] = $v['net_commission_tds'];
           	$export_data[$k]['net_fare'] = $v['net_fare'];
           	$export_data[$k]['gst'] = $v['gst'];
           	$export_data[$k]['convinence_amount'] = $v['convinence_amount'];
           	$export_data[$k]['discount'] = $v['discount'];
           	$export_data[$k]['grand_total'] = $v['grand_total'];
           	$export_data[$k]['booked_date'] = date('d-m-Y', strtotime($v['booked_date']));
           	
        }

        if ($op == 'excel') { // excel export
           $headings = array('a1' => 'Sl. No.',
                    'b1' => 'Appreference',
                    'c1' => 'Lead Pax Name',
                    'd1' => 'Lead Pax Email',
                    'e1' => 'Lead Pax Phone',
                    'f1' => 'GDS PNR',
                    'g1' => 'Airline PNR',
                    'h1' => 'Airline Code',
                    'i1' => 'From',
                    'j1' => 'To',
                    'k1' => 'Form Date',
                    'l1' => 'To Date',
                    'm1' => 'Commission Fare',
					'n1' => 'Commission',
                    'o1' => 'TDS',
                    'p1' => 'Net Fare',
                    'q1' => 'GST',
                    'r1' => 'Convinence Amount',
                    's1' => 'Discount',
                    't1' => 'Customer Paid',
                    'u1' => 'Booked Date',
                );
                // field names in data set 
                $fields = array('a' => '', // empty for sl. no.
                    'b' => 'app_reference',
                    'c' => 'lead_pax_name',
                    'd' => 'lead_pax_email',
                    'e' => 'lead_pax_phone_number',
                    'f' => 'gds_pnr',
                    'g' => 'airline_pnr',
                    'h' => 'airline_code',
                    'i' => 'journey_from',
                    'j' => 'journey_to',
                    'k' => 'journey_start',
                    'l' => 'journey_end',
                    'm' => 'commission_fare',
                    'n' => 'commission',
                    'o' => 'tds',
                    'p' => 'net_fare',
                    'q' => 'gst',
                    'r' => 'convinence_amount',
                    's' => 'discount',
                    't' => 'grand_total',
                    'u' => 'booked_date',
                    
                );
           
            $excel_sheet_properties = array(
                'title' => 'Cancelled_Booking_AirlineReport_' . date('d-M-Y'),
                'creator' => 'Accentria Solutions',
                'description' => 'Cancelled_Booking_AirlineReport',
                'sheet_title' => 'Cancelled_Booking_AirlineReport'
            );

            $this->load->library('provab_excel'); // we need this provab_excel library to export excel.
            $this->provab_excel->excel_export($headings, $fields, $export_data, $excel_sheet_properties);
        } 
    }
    public function export_confirmed_booking_airline_report_b2b($op = '', $b_status='') {
        $this->load->model('flight_model');
        $get_data = $this->input->get();
        $condition = array();
        //From-Date and To-Date
        $from_date = trim(@$get_data['created_datetime_from']);
        $to_date = trim(@$get_data['created_datetime_to']);

        $filter_data = $this->format_basic_search_filters('flight');
        $condition = $filter_data['filter_condition'];

        //Unset the Status Filter
        if (valid_array($condition) == true) {
            foreach ($condition as $ck => $cv) {

                if ($cv[0] == 'BD.status') {
                    unset($condition[$ck]);
                }
            }
        }
        //Adding Confirmed Status Filter
        $condition[] = array('BD.status', '=', $this->db->escape('BOOKING_CONFIRMED'));
        if($b_status != "confirmed_cancelled"){
        	$flight_booking_data = $this->flight_model->b2b_flight_report($condition, false, 0, 2000); 
    	}
        //Maximum 500 Data Can be exported at time
        if($b_status == "confirmed_cancelled"){
        	$flight_booking_data = $this->flight_model->b2b_flight_report($condition, false, 0, 2000, $b_status);
        	$sheet_name = "Confirmed_Cancelled";
        }
        else
        	$sheet_name = "Confirmed";


        $flight_booking_data = $this->booking_data_formatter->format_flight_booking_data($flight_booking_data, $this->current_module);
        $flight_booking_data = $flight_booking_data['data']['booking_details'];



        $export_data = array();
         //debug($flight_booking_data);exit;
        foreach ($flight_booking_data as $k => $v) {
        	//debug($v); exit;
			$export_data[$k]['app_reference'] = $v['app_reference'];
			$export_data[$k]['status'] = $v['status'];
			$export_data[$k]['agency_name'] = $v['agency_name'];
			$export_data[$k]['agency_name'] = $v['agency_name']." - ".provab_decrypt($v['agency_id']);
            $export_data[$k]['lead_pax_name'] = $v['lead_pax_name'];
            $export_data[$k]['lead_pax_email'] = $v['lead_pax_email'];
            $export_data[$k]['lead_pax_phone_number'] = $v['lead_pax_phone_number'];
            $export_data[$k]['gds_pnr'] = $v['pnr'];
            $export_data[$k]['supp_name'] = $v['supp_name'];
            $export_data[$k]['airline_name'] = $v['booking_itinerary_details'][0]['airline_name'];
            $export_data[$k]['airline_pnr'] = $v['booking_itinerary_details'][0]['airline_pnr'];
            $export_data[$k]['airline_code'] = $v['booking_itinerary_details'][0]['airline_code'];
            $export_data[$k]['journey_from'] = $v['journey_from'];
           	$export_data[$k]['journey_to'] = $v['journey_to'];
           	$export_data[$k]['journey_start'] = $v['journey_start'];
           	$export_data[$k]['journey_end'] = $v['journey_end'];
           	$export_data[$k]['convenience'] = $v['convinence_amount'];
           	//$export_data[$k]['trip_type_label'] = $v['trip_type_label'];

           	$attrs = json_decode($v["booking_transaction_details"][0]["attributes"], true);
           	//debug($attrs); exit;
           	$export_data[$k]['base_fare'] = $attrs["Fare"]["BaseFare"];
           	$export_data[$k]['tax'] = $attrs["Fare"]["Tax"];

           	$export_data[$k]['commission_fare'] = $v['fare'];
           	$export_data[$k]['commission'] = $v['admin_commission'];
           	$export_data[$k]['tds'] = $v['admin_tds'];
           	$export_data[$k]['net_fare'] = $v['net_fare'];
           	$export_data[$k]['agent_commission'] = $v['agent_commission'];
           	$export_data[$k]['agent_tds'] = $v['agent_tds'];
           	$export_data[$k]['pace_commission'] = $v['admin_commission']-$v['agent_commission'];
           	$export_data[$k]['pace_tds'] = $v['admin_tds']-$v['agent_tds'];
           	$profit = $export_data[$k]['pace_commission']+$v['admin_markup']-$export_data[$k]['pace_tds'];
            $total_profit += $profit;
           	$export_data[$k]['gst'] = $v['gst'];
           	$export_data[$k]['admin_markup'] = $v['admin_markup'];
           	$export_data[$k]['agent_markup'] = $v['agent_markup'];
           	$export_data[$k]['agent_commission'] = $v['agent_commission'];
           	$export_data[$k]['agent_tds'] = $v['agent_tds'];
           	$export_data[$k]['agent_buying_price'] = $v['agent_buying_price'];
           	$export_data[$k]['admin_buying_price'] = $v['admin_buying_price'];
           	$export_data[$k]['grand_total'] = $v['grand_total'];
           	$export_data[$k]['profit'] = $profit;
           	$export_data[$k]['booked_date'] = date('d-m-Y', strtotime($v['booked_date']));
           	$export_data[$k]['status'] = $v['status'];
           	
        }
        if(!empty($export_data))
        {
        	$export_data['last_row']['app_reference']="Total";
        	$export_data['last_row']['profit']=$total_profit;
        }

        if ($op == 'excel') { // excel export
           $headings = array('a1' => 'Sl. No.',
                    'b1' => 'Appreference',
                    'c1' => 'Agency Name',
                    'd1' => 'Lead Pax Name',
                    'e1' => 'Lead Pax Email',
                    'f1' => 'Lead Pax Phone',
                    'g1' => 'Sup. Name',
                    'h1' => 'Airline Name',
                    'i1' => 'GDS PNR',
                    'j1' => 'Airline PNR',
                    'k1' => 'Airline Code',
                    'l1' => 'From',
                    'm1' => 'To',
                    'n1' => 'Form Date',
                    'o1' => 'To Date',
                    'p1' => 'Base Fare',
                    'q1' => 'Tax',
                    'r1' => 'Fare',
					's1' => 'Supp. Comm',
                    't1' => 'Supp. TDS',
                    'u1' => 'Pace Comm',
                    'v1' => 'Pace Tds',
                    'w1' => 'Net Fare',
                    'x1' => 'GST',
                    'y1' => 'Admin Markup',
                    'z1' => 'Agent Mark up',
                    'aa1' => 'Agent Commission',
                    'ab1' => 'Agent Tds',
                    'ac1' => 'Agent NetFare',
                    'ad1' =>'Admin Netfare',
                    'ae1' =>'Customer Paid',
                    'af1' =>'Your Profit',
                    'ag1' =>'Convenience',
                    'ah1' =>'Booked Date',
                    'ai1' =>'Booking Status'
                );
                // field names in data set 
                $fields = array('a' => '', // empty for sl. no.
                    'b' => 'app_reference',
                    'c' => 'agency_name',
                    'd' => 'lead_pax_name',
                    'e' => 'lead_pax_email',
                    'f' => 'lead_pax_phone_number',
                    'g' => 'supp_name',
                    'h' => 'airline_name',
                    'i' => 'gds_pnr',
                    'j' => 'airline_pnr',
                    'k' => 'airline_code',
                    'l' => 'journey_from',
                    'm' => 'journey_to',
                    'n' => 'journey_start',
                    'o' => 'journey_end',
                    'p' => 'base_fare',
                    'q' => 'tax',
                    'r' => 'commission_fare',
                    's' => 'commission',
                    't' => 'tds',
                    'u' => 'pace_commission',
                    'v' => 'pace_tds',
                    'w' => 'net_fare',
                    'x' => 'gst',
                    'y' => 'admin_markup',
                    'z' => 'agent_markup',
                  	'aa' => 'agent_commission',
                    'ab' => 'agent_tds',
                    'ac' => 'agent_buying_price',
                    'ad' =>'admin_buying_price',
                    'ae' =>'grand_total',
                    'af' =>'profit',
                    'ag' =>'convenience',
                    'ah' =>'booked_date',
                    'ai' =>'status'
                );
           
            $excel_sheet_properties = array(
                'title' => $sheet_name.'_AirlineReport_' . date('d-M-Y'),
                'creator' => 'Accentria Solutions',
                'description' => $sheet_name.'_AirlineReport',
                'sheet_title' => $sheet_name.'_AirlineReport'
            );

            $this->load->library('provab_excel'); // we need this provab_excel library to export excel.
            $this->provab_excel->excel_export($headings, $fields, $export_data, $excel_sheet_properties);
        } 
    }
    public function export_cancelled_booking_airline_report_b2b($op = '') {
        $this->load->model('flight_model');
        $get_data = $this->input->get();
        $condition = array();
        //From-Date and To-Date
        $from_date = trim(@$get_data['created_datetime_from']);
        $to_date = trim(@$get_data['created_datetime_to']);

        $filter_data = $this->format_basic_search_filters('flight');
        $condition = $filter_data['filter_condition'];

        //Unset the Status Filter
        if (valid_array($condition) == true) {
            foreach ($condition as $ck => $cv) {

                if ($cv[0] == 'BD.status') {
                    unset($condition[$ck]);
                }
            }
        }

        //Adding Confirmed Status Filter
        $condition[] = array('BD.status', '=', $this->db->escape('BOOKING_CANCELLED'));

        $flight_booking_data = $this->flight_model->b2b_flight_report($condition, false, 0, 2000); //Maximum 500 Data Can be exported at time
        $flight_booking_data = $this->booking_data_formatter->format_flight_booking_data($flight_booking_data, $this->current_module);
        $flight_booking_data = $flight_booking_data['data']['booking_details'];



        $export_data = array();
        // debug($flight_booking_data);exit;
        $export_data = array();
         //debug($flight_booking_data);exit;
        foreach ($flight_booking_data as $k => $v) {
        	//debug($v); exit;
			$export_data[$k]['app_reference'] = $v['app_reference'];
			$export_data[$k]['status'] = $v['status'];
			$export_data[$k]['agency_name'] = $v['agency_name'];
			$export_data[$k]['agency_name'] = $v['agency_name']." - ".provab_decrypt($v['agency_id']);
            $export_data[$k]['lead_pax_name'] = $v['lead_pax_name'];
            $export_data[$k]['lead_pax_email'] = $v['lead_pax_email'];
            $export_data[$k]['lead_pax_phone_number'] = $v['lead_pax_phone_number'];
            $export_data[$k]['gds_pnr'] = $v['pnr'];
            $export_data[$k]['supp_name'] = $v['supp_name'];
            $export_data[$k]['airline_name'] = $v['booking_itinerary_details'][0]['airline_name'];
            $export_data[$k]['airline_pnr'] = $v['booking_itinerary_details'][0]['airline_pnr'];
            $export_data[$k]['airline_code'] = $v['booking_itinerary_details'][0]['airline_code'];
            $export_data[$k]['journey_from'] = $v['journey_from'];
           	$export_data[$k]['journey_to'] = $v['journey_to'];
           	$export_data[$k]['journey_start'] = $v['journey_start'];
           	$export_data[$k]['journey_end'] = $v['journey_end'];
           	$export_data[$k]['convenience'] = $v['convinence_amount'];
           	//$export_data[$k]['trip_type_label'] = $v['trip_type_label'];

           	$attrs = json_decode($v["booking_transaction_details"][0]["attributes"], true);
           	//debug($attrs); exit;
           	$export_data[$k]['base_fare'] = $attrs["Fare"]["BaseFare"];
           	$export_data[$k]['tax'] = $attrs["Fare"]["Tax"];

           	$export_data[$k]['commission_fare'] = $v['fare'];
           	$export_data[$k]['commission'] = $v['admin_commission'];
           	$export_data[$k]['tds'] = $v['admin_tds'];
           	$export_data[$k]['net_fare'] = $v['net_fare'];
           	$export_data[$k]['agent_commission'] = $v['agent_commission'];
           	$export_data[$k]['agent_tds'] = $v['agent_tds'];
           	$export_data[$k]['pace_commission'] = $v['admin_commission']-$v['agent_commission'];
           	$export_data[$k]['pace_tds'] = $v['admin_tds']-$v['agent_tds'];
           	$profit = $export_data[$k]['pace_commission']+$v['admin_markup']-$export_data[$k]['pace_tds'];
            $total_profit += $profit;
           	$export_data[$k]['gst'] = $v['gst'];
           	$export_data[$k]['admin_markup'] = $v['admin_markup'];
           	$export_data[$k]['agent_markup'] = $v['agent_markup'];
           	$export_data[$k]['agent_commission'] = $v['agent_commission'];
           	$export_data[$k]['agent_tds'] = $v['agent_tds'];
           	$export_data[$k]['agent_buying_price'] = $v['agent_buying_price'];
           	$export_data[$k]['admin_buying_price'] = $v['admin_buying_price'];
           	$export_data[$k]['grand_total'] = $v['grand_total'];
           	$export_data[$k]['profit'] = $profit;
           	$export_data[$k]['booked_date'] = date('d-m-Y', strtotime($v['booked_date']));
           	$export_data[$k]['status'] = $v['status'];

           	$fcds = $this->flight_model->get_cancellation_details($v['app_reference']);

           	//Cancellation & Refund Details #START
           	$api_refund_status = "INPROGRESS";
           	$api_refund = 0;
           	$api_cancellation_charge = 0;
           	$pace_refund_status = "INPROGRESS";
           	$pace_refund = 0;
           	$pace_cancellation_charge = 0;
           	$commission_reversed = 0;
           	foreach ($fcds as $key => $value) {
           		//debug($value); exit;
           		$api_refund_status = $value["API_refund_status"];
           		if(isset($value["API_RefundedAmount"]) && !empty($value["API_RefundedAmount"])){
           			$api_refund += $value["API_RefundedAmount"];
           		}
           	$commission_reversed += $value["commission_reversed"];
           	if(isset($value["API_CancellationCharge"]) && !empty($value["API_CancellationCharge"])){
           		$api_cancellation_charge += $value["API_CancellationCharge"];
           		}
	           	$pace_refund_status = $value["refund_status"];
	           	$pace_refund += $value["refund_amount"];
	           	$pace_cancellation_charge += $value["cancellation_charge"];
           	}

           	$export_data[$k]["api_refund_status"] = $api_refund_status;
           	$api_cancellation_charge = $api_cancellation_charge;
           	$export_data[$k]["pace_refund"] = $pace_refund;
           	$export_data[$k]["pace_cancellation_charge"] = $pace_cancellation_charge;
           	$export_data[$k]["pace_refund_status"] = $pace_refund_status;
           	$export_data[$k]["supp_refund"] = $api_refund;
           	$export_data[$k]["api_cancellation_charge"] = $api_cancellation_charge;
           	$export_data[$k]["commission_reversed"] = $commission_reversed;
           	//Cancellation & Refund Details #END

           }
        if(!empty($export_data))
        {
        	$export_data['last_row']['app_reference']="Total";
        	$export_data['last_row']['profit']=$total_profit;
        }

        if ($op == 'excel') { // excel export
           $headings = array('a1' => 'Sl. No.',
                    'b1' => 'Appreference',
                    'c1' => 'Agency Name',
                    'd1' => 'Lead Pax Name',
                    'e1' => 'Lead Pax Email',
                    'f1' => 'Lead Pax Phone',
                    'g1' => 'Sup. Name',
                    'h1' => 'Airline Name',
                    'i1' => 'GDS PNR',
                    'j1' => 'Airline PNR',
                    'k1' => 'Airline Code',
                    'l1' => 'From',
                    'm1' => 'To',
                    'n1' => 'Form Date',
                    'o1' => 'To Date',
                    'p1' => 'Base Fare',
                    'q1' => 'Tax',
                    'r1' => 'Fare',
					's1' => 'Supp. Comm',
                    't1' => 'Supp. TDS',
                    'u1' => 'Pace Comm',
                    'v1' => 'Pace Tds',
                    'w1' => 'Net Fare',
                    'x1' => 'GST',
                    'y1' => 'Admin Markup',
                    'z1' => 'Agent Mark up',
                    'aa1' => 'Agent Commission',
                    'ab1' => 'Agent Tds',
                    'ac1' => 'Agent NetFare',
                    'ad1' =>'Admin Netfare',
                    'ae1' =>'Customer Paid',
                    'af1' =>'Your Profit',
                    'ag1' =>'Sup. Status',
                    'ah1' =>'Sup. Charge',
                    'ai1' =>'Sup. Refund',
                    'aj1' =>'Pace. Status',
                    'ak1' =>'Pace. Charge',
                    'al1' =>'Comm. Reversed',
                    'am1' =>'Pace. Refund',
                    'an1' =>'Convenience', 
                    'ao1' =>'Booked Date',
                    'ap1' =>'Booking Status'
                );
                // field names in data set 
                $fields = array('a' => '', // empty for sl. no.
                    'b' => 'app_reference',
                    'c' => 'agency_name',
                    'd' => 'lead_pax_name',
                    'e' => 'lead_pax_email',
                    'f' => 'lead_pax_phone_number',
                    'g' => 'supp_name',
                    'h' => 'airline_name',
                    'i' => 'gds_pnr',
                    'j' => 'airline_pnr',
                    'k' => 'airline_code',
                    'l' => 'journey_from',
                    'm' => 'journey_to',
                    'n' => 'journey_start',
                    'o' => 'journey_end',
                    'p' => 'base_fare',
                    'q' => 'tax',
                    'r' => 'commission_fare',
                    's' => 'commission',
                    't' => 'tds',
                    'u' => 'pace_commission',
                    'v' => 'pace_tds',
                    'w' => 'net_fare',
                    'x' => 'gst',
                    'y' => 'admin_markup',
                    'z' => 'agent_markup',
                  	'aa' => 'agent_commission',
                    'ab' => 'agent_tds',
                    'ac' => 'agent_buying_price',
                    'ad' =>'admin_buying_price',
                    'ae' =>'grand_total',
                    'af' =>'profit',
                    'ag' =>'api_refund_status',
                    'ah' =>'api_cancellation_charge',
                    'ai' =>'supp_refund',
                    'aj' =>'pace_refund_status',
                    'ak' =>'pace_cancellation_charge',
                    'al' =>'commission_reversed',
                    'am' =>'pace_refund',
                    'an' =>'convenience', 
                    'ao' =>'booked_date',
                    'ap' =>'status'
                );
           
            $excel_sheet_properties = array(
                'title' => 'cancelled_Booking_AirlineReport_' . date('d-M-Y'),
                'creator' => 'Accentria Solutions',
                'description' => 'cancelled_Booking_AirlineReport',
                'sheet_title' => 'cancelled_Booking_AirlineReport'
            );

            $this->load->library('provab_excel'); // we need this provab_excel library to export excel.
            $this->provab_excel->excel_export($headings, $fields, $export_data, $excel_sheet_properties);
        } 
    }
     public function export_confirmed_booking_hotel_report($op = '') {
        $this->load->model('hotel_model');
        $get_data = $this->input->get();
        $condition = array();
        //From-Date and To-Date
        $from_date = trim(@$get_data['created_datetime_from']);
        $to_date = trim(@$get_data['created_datetime_to']);

        $filter_data = $this->format_basic_search_filters('hotel');
        $condition = $filter_data['filter_condition'];

        //Unset the Status Filter
        if (valid_array($condition) == true) {
            foreach ($condition as $ck => $cv) {

                if ($cv[0] == 'BD.status') {
                    unset($condition[$ck]);
                }
            }
        }

        //Adding Confirmed Status Filter
        $condition[] = array('BD.status', '=', $this->db->escape('BOOKING_CONFIRMED'));
        $hotel_booking_data = $this->hotel_model->b2c_hotel_report($condition, false, 0, 2000); //Maximum 500 Data Can be exported at time
        $hotel_booking_data = $this->booking_data_formatter->format_hotel_booking_data($hotel_booking_data, $this->current_module);
        $hotel_booking_data = $hotel_booking_data['data']['booking_details'];



        $export_data = array();
        //debug($hotel_booking_data);exit;
        foreach ($hotel_booking_data as $k => $v) {
           
			$export_data[$k]['Reference No'] = $v['app_reference'];
			$export_data[$k]['Confirmation_Reference'] = $v['confirmation_reference'];
			$export_data[$k]['lead_pax_name'] = $v['lead_pax_name'];
            $export_data[$k]['lead_pax_email'] = $v['lead_pax_email'];
            $export_data[$k]['lead_pax_phone_number'] = $v['lead_pax_phone_number'];
            $export_data[$k]['Hotel Name'] = $v['hotel_name'];
            $export_data[$k]['No.of rooms'] = $v['total_rooms'];
            $export_data[$k]['No.of Adult'] = $v['adult_count'];
            $export_data[$k]['No.of Child'] = $v['child_count'];
           	$export_data[$k]['city'] = $v['hotel_location'];
           	$export_data[$k]['check_in'] = $v['hotel_check_in'];
           	$export_data[$k]['check_out'] = $v['hotel_check_out'];
           	$export_data[$k]['commission_fare'] = $v['fare'];
           	$export_data[$k]['TDS'] = $v['TDS'];
           	$export_data[$k]['Admin_markup'] = $v['admin_markup'];
           	$export_data[$k]['convinence_amount'] = $v['convinence_amount'];
           	$export_data[$k]['gst'] = $v['gst'];
           	$export_data[$k]['Discount'] = $v['discount'];
           	$export_data[$k]['grand_total'] = $v['grand_total'];
           	$export_data[$k]['booked_on'] = date('d-m-Y', strtotime($v['voucher_date']));
           	        		
           	
        }
//debug($export_data[$k]['Payment Status']);exit;
        if ($op == 'excel') { // excel export
           $headings = array('a1' => 'Sl. No.',
                    'b1' => 'Reference No',
                    'c1' => 'Lead Pax Name',
                    'd1' => 'Lead Pax Email',
                    'e1' => 'Lead Pax Phone',
                    'f1' => 'Confirmation_Reference',
                    'g1' => 'Hotel Name',
                    'h1' => 'No.of rooms',
                    'i1' => 'No.of Adult',
                    'j1' => 'No.of Child',
                    'k1' => 'city',
                    'l1' => 'check_in',
                    'm1' => 'check_out',
					'n1' => 'Commission Fare',
                    'o1' => 'TDS',
                    'p1' => 'Admin Markup',
                    'q1' => 'GST',
                    'r1' => 'convinence Fee',
                    's1' => 'Discount',
                    't1' => 'Grand Total',
                    'u1' => 'Booked On',
                   
                );
                // field names in data set 
                $fields = array('a' => '', // empty for sl. no.
                    'b' => 'Reference No',
                    'c' => 'lead_pax_name',
                    'd' => 'lead_pax_email',
                    'e' => 'lead_pax_phone_number',
                    'f' => 'Confirmation_Reference',
                    'g' => 'Hotel Name',
                    'h' => 'No.of rooms',
                    'i' => 'No.of Adult',
                    'j' => 'No.of Child',
                    'k' => 'city',
                    'l' => 'check_in',
                    'm' => 'check_out',
                    'n' => 'commission_fare',
                    'o' => 'TDS',
                    'p' => 'Admin_markup',
                    'q' => 'convinence_amount',
                    'r' => 'gst',
                    's' => 'Discount',
                  	't' => 'grand_total',
                    'u' => 'booked_on',
                                        
                );
           
            $excel_sheet_properties = array(
                'title' => 'Confirmed_Booking_HotelReport_' . date('d-M-Y'),
                'creator' => 'Accentria Solutions',
                'description' => 'Confirmed_Booking_HotelReport',
                'sheet_title' => 'Confirmed_Booking_HotelReport'
            );

            $this->load->library('provab_excel'); // we need this provab_excel library to export excel.
            $this->provab_excel->excel_export($headings, $fields, $export_data, $excel_sheet_properties);
        } 
    }
    public function export_cancelled_booking_hotel_report($op = '') {
        $this->load->model('hotel_model');
        $get_data = $this->input->get();
        $condition = array();
        //From-Date and To-Date
        $from_date = trim(@$get_data['created_datetime_from']);
        $to_date = trim(@$get_data['created_datetime_to']);

        $filter_data = $this->format_basic_search_filters('hotel');
        $condition = $filter_data['filter_condition'];

        //Unset the Status Filter
        if (valid_array($condition) == true) {
            foreach ($condition as $ck => $cv) {

                if ($cv[0] == 'BD.status') {
                    unset($condition[$ck]);
                }
            }
        }

        //Adding Confirmed Status Filter
        $condition[] = array('BD.status', '=', $this->db->escape('BOOKING_CANCELLED'));

        $hotel_booking_data = $this->hotel_model->b2c_hotel_report($condition, false, 0, 2000); //Maximum 500 Data Can be exported at time
        $hotel_booking_data = $this->booking_data_formatter->format_hotel_booking_data($hotel_booking_data, $this->current_module);
        $hotel_booking_data = $hotel_booking_data['data']['booking_details'];



        $export_data = array();
        //debug($hotel_booking_data);exit;
        foreach ($hotel_booking_data as $k => $v) {
           
			$export_data[$k]['Reference No'] = $v['app_reference'];
			$export_data[$k]['Confirmation_Reference'] = $v['confirmation_reference'];
			$export_data[$k]['lead_pax_name'] = $v['lead_pax_name'];
            $export_data[$k]['lead_pax_email'] = $v['lead_pax_email'];
            $export_data[$k]['lead_pax_phone_number'] = $v['lead_pax_phone_number'];
            $export_data[$k]['Hotel Name'] = $v['hotel_name'];
            $export_data[$k]['No.of rooms'] = $v['total_rooms'];
            $export_data[$k]['No.of Adult'] = $v['adult_count'];
            $export_data[$k]['No.of Child'] = $v['child_count'];
           	$export_data[$k]['city'] = $v['hotel_location'];
           	$export_data[$k]['check_in'] = $v['hotel_check_in'];
           	$export_data[$k]['check_out'] = $v['hotel_check_out'];
           	$export_data[$k]['commission_fare'] = $v['fare'];
           	$export_data[$k]['TDS'] = $v['TDS'];
           	$export_data[$k]['Admin_markup'] = $v['admin_markup'];
           	$export_data[$k]['convinence_amount'] = $v['convinence_amount'];
           	$export_data[$k]['gst'] = $v['gst'];
           	$export_data[$k]['Discount'] = $v['discount'];
           	$export_data[$k]['grand_total'] = $v['grand_total'];
           	$export_data[$k]['booked_on'] = date('d-m-Y', strtotime($v['voucher_date']));
           	        		
           	
        }
//debug($export_data[$k]['Payment Status']);exit;
        if ($op == 'excel') { // excel export
           $headings = array('a1' => 'Sl. No.',
                    'b1' => 'Reference No',
                    'c1' => 'Lead Pax Name',
                    'd1' => 'Lead Pax Email',
                    'e1' => 'Lead Pax Phone',
                    'f1' => 'Confirmation_Reference',
                    'g1' => 'Hotel Name',
                    'h1' => 'No.of rooms',
                    'i1' => 'No.of Adult',
                    'j1' => 'No.of Child',
                    'k1' => 'city',
                    'l1' => 'check_in',
                    'm1' => 'check_out',
					'n1' => 'Commission Fare',
                    'o1' => 'TDS',
                    'p1' => 'Admin Markup',
                    'q1' => 'GST',
                    'r1' => 'convinence Fee',
                    's1' => 'Discount',
                    't1' => 'Grand Total',
                    'u1' => 'Booked On',
                   
                );
                // field names in data set 
                $fields = array('a' => '', // empty for sl. no.
                    'b' => 'Reference No',
                    'c' => 'lead_pax_name',
                    'd' => 'lead_pax_email',
                    'e' => 'lead_pax_phone_number',
                    'f' => 'Confirmation_Reference',
                    'g' => 'Hotel Name',
                    'h' => 'No.of rooms',
                    'i' => 'No.of Adult',
                    'j' => 'No.of Child',
                    'k' => 'city',
                    'l' => 'check_in',
                    'm' => 'check_out',
                    'n' => 'commission_fare',
                    'o' => 'TDS',
                    'p' => 'Admin_markup',
                    'q' => 'convinence_amount',
                    'r' => 'gst',
                    's' => 'Discount',
                  	't' => 'grand_total',
                    'u' => 'booked_on',
                                        
                );
           
            $excel_sheet_properties = array(
                'title' => 'Cancelled_Booking_HotelReport_' . date('d-M-Y'),
                'creator' => 'Accentria Solutions',
                'description' => 'Cancelled_Booking_HotelReport',
                'sheet_title' => 'Cancelled_Booking_HotelReport'
            );

            $this->load->library('provab_excel'); // we need this provab_excel library to export excel.
            $this->provab_excel->excel_export($headings, $fields, $export_data, $excel_sheet_properties);
        } 
    }
 public function export_confirmed_booking_hotel_report_b2b($op = '', $b_status='') {
        $this->load->model('hotel_model');
        $get_data = $this->input->get();
        $condition = array();
        //From-Date and To-Date
        $from_date = trim(@$get_data['created_datetime_from']);
        $to_date = trim(@$get_data['created_datetime_to']);

        $filter_data = $this->format_basic_search_filters('hotel');
        $condition = $filter_data['filter_condition'];

        //Unset the Status Filter
        if (valid_array($condition) == true) {
            foreach ($condition as $ck => $cv) {

                if ($cv[0] == 'BD.status') {
                    unset($condition[$ck]);
                }
            }
        }

        if($b_status != "confirmed_cancelled"){
	        //Adding Confirmed Status Filter
	        $condition[] = array('BD.status', '=', $this->db->escape('BOOKING_CONFIRMED'));
	        $hotel_booking_data = $this->hotel_model->b2b_hotel_report($condition, false, 0, 2000); //Maximum 500 Data Can be exported at time
    	}
        if($b_status == "confirmed_cancelled"){
        	$hotel_booking_data = $this->hotel_model->b2b_hotel_report($condition, false, 0, 2000, $b_status);
        	$sheet_name = "confirmed_cancelled";
        }
        else
        {
        	$sheet_name = "confirmed";
        }
        $hotel_booking_data = $this->booking_data_formatter->format_hotel_booking_data($hotel_booking_data, $this->current_module);
        $hotel_booking_data = $hotel_booking_data['data']['booking_details'];

        $export_data = array();
        foreach ($hotel_booking_data as $k => $v) {
			$export_data[$k]['Reference No'] = $v['app_reference'];
			$export_data[$k]['supp_name'] = $v['supp_name'];
			$export_data[$k]['Confirmation_Reference'] = $v['confirmation_reference'];
			$export_data[$k]['lead_pax_name'] = $v['lead_pax_name'];
            $export_data[$k]['lead_pax_email'] = $v['lead_pax_email'];
            $export_data[$k]['lead_pax_phone_number'] = $v['lead_pax_phone_number'];
            $export_data[$k]['Hotel Name'] = $v['hotel_name'];
            $export_data[$k]['Hotel Location'] = $v['hotel_location'];
            $export_data[$k]['No.of rooms'] = $v['total_rooms'];
            $export_data[$k]['No.of Adult'] = $v['adult_count'];
            $export_data[$k]['No.of Child'] = $v['child_count'];
           	$export_data[$k]['city'] = $v['hotel_location'];
           	$export_data[$k]['check_in'] = $v['hotel_check_in'];
           	$export_data[$k]['check_out'] = $v['hotel_check_out'];
           	$export_data[$k]['commission_fare'] = $v['fare'];
           	$export_data[$k]['TDS'] = $v['TDS'];
           	$export_data[$k]['Admin_markup'] = $v['admin_markup'];
           	$profit = $v['admin_markup'];
           	$total_profit += $profit;
           	$export_data[$k]['profit'] = $profit;
           	$export_data[$k]['agent_markup'] = $v['agent_markup'];
           	$export_data[$k]['convinence_amount'] = $v['convinence_amount'];
           	$export_data[$k]['gst'] = $v['gst'];
           	$export_data[$k]['Discount'] = $v['discount'];
           	$export_data[$k]['grand_total'] = $v['grand_total'];
           	$export_data[$k]['booked_on'] = date('d-m-Y', strtotime($v['voucher_date']));	
           	$export_data[$k]['status'] = $v['status'];
        }
         if(!empty($export_data))
        {
        	$export_data['last_row']['Reference No']="Total";
        	$export_data['last_row']['profit']=$total_profit;
        }
        //debug($export_data); exit;
        if ($op == 'excel') { // excel export
           $headings = array('a1' => 'Sl. No.',
           			'b1' => 'Supp. Name',
                    'c1' => 'Reference No',
                    'd1' => 'Lead Pax Name',
                    'e1' => 'Lead Pax Email',
                    'f1' => 'Lead Pax Phone',
                    'g1' => 'Confirmation_Reference',
                    'h1' => 'Hotel Name',
                    'i1' => 'No.of rooms',
                    'j1' => 'No.of Adult',
                    'k1' => 'No.of Child',
                    'l1' => 'city',
                    'm1' => 'check_in',
                    'n1' => 'check_out',
					'o1' => 'Fare',
                    'p1' => 'Admin Markup',
                    'q1' => 'Agent Markup',
                    'r1' => 'GST',
                    's1' => 'Convinence',
                    't1' => 'Discount',
                    'u1' => 'Grand Total',
                    'v1' => 'Profit',
                    'w1' => 'Booked On',
                    'x1' => 'Booking Status'
                );
                // field names in data set 
                $fields = array('a' => '', // empty for sl. no.
                    'b' => 'supp_name',
                    'c' => 'Reference No',
                    'd' => 'lead_pax_name',
                    'e' => 'lead_pax_email',
                    'f' => 'lead_pax_phone_number',
                    'g' => 'Confirmation_Reference',
                    'h' => 'Hotel Name',
                    'i' => 'No.of rooms',
                    'j' => 'No.of Adult',
                    'k' => 'No.of Child',
                    'l' => 'city',
                    'm' => 'check_in',
                    'n' => 'check_out',
                    'o' => 'commission_fare',
                    'p' => 'Admin_markup',
                    'q' => 'agent_markup',
                    'r' => 'convinence_amount',
                    's' => 'gst',
                    't' => 'Discount',
                  	'u' => 'grand_total',
                  	'v' => 'profit',
                    'w' => 'booked_on',
                    'x' => 'status'                   
                );
           
            $excel_sheet_properties = array(
                'title' => $sheet_name.'_HotelReport_' . date('d-M-Y'),
                'creator' => 'Accentria Solutions',
                'description' => $sheet_name.'_HotelReport',
                'sheet_title' => $sheet_name.'_HotelReport'
            );

            $this->load->library('provab_excel'); // we need this provab_excel library to export excel.
            $this->provab_excel->excel_export($headings, $fields, $export_data, $excel_sheet_properties);
        } 
    }

    public function export_cancelled_booking_hotel_report_b2b($op = '') {
        $this->load->model('hotel_model');
        $get_data = $this->input->get();
        $condition = array();
        //From-Date and To-Date
        $from_date = trim(@$get_data['created_datetime_from']);
        $to_date = trim(@$get_data['created_datetime_to']);

        $filter_data = $this->format_basic_search_filters('hotel');
        $condition = $filter_data['filter_condition'];

        //Unset the Status Filter
        if (valid_array($condition) == true) {
            foreach ($condition as $ck => $cv) {

                if ($cv[0] == 'BD.status') {
                    unset($condition[$ck]);
                }
            }
        }

        //Adding Confirmed Status Filter
        $condition[] = array('BD.status', '=', $this->db->escape('BOOKING_CANCELLED'));

        $hotel_booking_data = $this->hotel_model->b2b_hotel_report($condition, false, 0, 2000); //Maximum 500 Data Can be exported at time
        $hotel_booking_data = $this->booking_data_formatter->format_hotel_booking_data($hotel_booking_data, $this->current_module);
        $hotel_booking_data = $hotel_booking_data['data']['booking_details'];



        $export_data = array();
        //debug($hotel_booking_data);exit;
       foreach ($hotel_booking_data as $k => $v) {
			$export_data[$k]['Reference No'] = $v['app_reference'];
			$export_data[$k]['supp_name'] = $v['supp_name'];
			$export_data[$k]['Confirmation_Reference'] = $v['confirmation_reference'];
			$export_data[$k]['lead_pax_name'] = $v['lead_pax_name'];
            $export_data[$k]['lead_pax_email'] = $v['lead_pax_email'];
            $export_data[$k]['lead_pax_phone_number'] = $v['lead_pax_phone_number'];
            $export_data[$k]['Hotel Name'] = $v['hotel_name'];
            $export_data[$k]['Hotel Location'] = $v['hotel_location'];
            $export_data[$k]['No.of rooms'] = $v['total_rooms'];
            $export_data[$k]['No.of Adult'] = $v['adult_count'];
            $export_data[$k]['No.of Child'] = $v['child_count'];
           	$export_data[$k]['city'] = $v['hotel_location'];
           	$export_data[$k]['check_in'] = $v['hotel_check_in'];
           	$export_data[$k]['check_out'] = $v['hotel_check_out'];
           	$export_data[$k]['commission_fare'] = $v['fare'];
           	$export_data[$k]['TDS'] = $v['TDS'];
           	$export_data[$k]['Admin_markup'] = $v['admin_markup'];
           	$profit = $v['admin_markup'];
           	$total_profit += $profit;
           	$export_data[$k]['profit'] = $profit;
           	$export_data[$k]['agent_markup'] = $v['agent_markup'];
           	$export_data[$k]['convinence_amount'] = $v['convinence_amount'];
           	$export_data[$k]['gst'] = $v['gst'];
           	$export_data[$k]['Discount'] = $v['discount'];
           	$export_data[$k]['grand_total'] = $v['grand_total'];
           	$export_data[$k]['booked_on'] = date('d-m-Y', strtotime($v['voucher_date']));	
           	$export_data[$k]['status'] = $v['status'];
        }
         if(!empty($export_data))
        {
        	$export_data['last_row']['Reference No']="Total";
        	$export_data['last_row']['profit']=$total_profit;
        }
        //debug($export_data); exit;
        if ($op == 'excel') { // excel export
           $headings = array('a1' => 'Sl. No.',
           			'b1' => 'Supp. Name',
                    'c1' => 'Reference No',
                    'd1' => 'Lead Pax Name',
                    'e1' => 'Lead Pax Email',
                    'f1' => 'Lead Pax Phone',
                    'g1' => 'Confirmation_Reference',
                    'h1' => 'Hotel Name',
                    'i1' => 'No.of rooms',
                    'j1' => 'No.of Adult',
                    'k1' => 'No.of Child',
                    'l1' => 'city',
                    'm1' => 'check_in',
                    'n1' => 'check_out',
					'o1' => 'Fare',
                    'p1' => 'Admin Markup',
                    'q1' => 'Agent Markup',
                    'r1' => 'GST',
                    's1' => 'Convinence',
                    't1' => 'Discount',
                    'u1' => 'Grand Total',
                    'v1' => 'Profit',
                    'w1' => 'Booked On',
                    'x1' => 'Booking Status'
                );
                // field names in data set 
                $fields = array('a' => '', // empty for sl. no.
                    'b' => 'supp_name',
                    'c' => 'Reference No',
                    'd' => 'lead_pax_name',
                    'e' => 'lead_pax_email',
                    'f' => 'lead_pax_phone_number',
                    'g' => 'Confirmation_Reference',
                    'h' => 'Hotel Name',
                    'i' => 'No.of rooms',
                    'j' => 'No.of Adult',
                    'k' => 'No.of Child',
                    'l' => 'city',
                    'm' => 'check_in',
                    'n' => 'check_out',
                    'o' => 'commission_fare',
                    'p' => 'Admin_markup',
                    'q' => 'agent_markup',
                    'r' => 'convinence_amount',
                    's' => 'gst',
                    't' => 'Discount',
                  	'u' => 'grand_total',
                  	'v' => 'profit',
                    'w' => 'booked_on',
                    'x' => 'status'                   
                );
           
            $excel_sheet_properties = array(
                'title' => 'Cancelled_Booking_HotelReport_' . date('d-M-Y'),
                'creator' => 'Accentria Solutions',
                'description' => 'Cancelled_Booking_HotelReport',
                'sheet_title' => 'Cancelled_Booking_HotelReport'
            );

            $this->load->library('provab_excel'); // we need this provab_excel library to export excel.
            $this->provab_excel->excel_export($headings, $fields, $export_data, $excel_sheet_properties);
        } 
    }
     public function export_confirmed_booking_bus_report($op = '') {
        $this->load->model('bus_model');
        $get_data = $this->input->get();
        $condition = array();
        //From-Date and To-Date
        $from_date = trim(@$get_data['created_datetime_from']);
        $to_date = trim(@$get_data['created_datetime_to']);

        $filter_data = $this->format_basic_search_filters('bus');
        $condition = $filter_data['filter_condition'];

        //Unset the Status Filter
        if (valid_array($condition) == true) {
            foreach ($condition as $ck => $cv) {

                if ($cv[0] == 'BD.status') {
                    unset($condition[$ck]);
                }
            }
        }

        //Adding Confirmed Status Filter
        $condition[] = array('BD.status', '=', $this->db->escape('BOOKING_CONFIRMED'));

        $bus_booking_data = $this->bus_model->b2c_bus_report($condition, false, 0, 2000); //Maximum 500 Data Can be exported at time
        $bus_booking_data = $this->booking_data_formatter->format_bus_booking_data($bus_booking_data, $this->current_module);
        $bus_booking_data = $bus_booking_data['data']['booking_details'];



        $export_data = array();
        //debug($bus_booking_data);exit;
        foreach ($bus_booking_data as $k => $v) {
           
			$export_data[$k]['app_reference'] = $v['app_reference'];
			$export_data[$k]['lead_pax_name'] = $v['lead_pax_name'];
            $export_data[$k]['lead_pax_email'] = $v['lead_pax_email'];
            $export_data[$k]['lead_pax_phone_number'] = $v['lead_pax_phone_number'];
            $export_data[$k]['Pnr'] = $v['pnr'];
            $export_data[$k]['operator'] = $v['operator'];
            $export_data[$k]['from'] = $v['departure_from'];
            $export_data[$k]['to'] = $v['arrival_to'];
           	$export_data[$k]['bus_type'] = $v['bus_type'];
           	$export_data[$k]['Comm.Fare'] = $v['fare'];
           	$export_data[$k]['commission'] = $v['admin_commission'];
           	$export_data[$k]['TDS'] = $v['admin_tds'];
           	$export_data[$k]['NetFare'] = $v['admin_buying_price'];
           	$export_data[$k]['convinence_amount'] = $v['convinence_amount'];
           	$export_data[$k]['Markup'] = $v['admin_markup'];
           	$export_data[$k]['gst'] = $v['gst'];
           	$export_data[$k]['Discount'] = $v['discount'];
           	$export_data[$k]['grand_total'] = $v['grand_total'];
           	$export_data[$k]['Travel date'] = $v['journey_datetime'];
           	$export_data[$k]['booked_date'] = date('d-m-Y', strtotime($v['booked_date']));
           	        		
           	
        }
//debug($export_data[$k]['Payment Status']);exit;
        if ($op == 'excel') { // excel export
           $headings = array('a1' => 'Sl. No.',
                    'b1' => 'app_reference',
                    'c1' => 'Lead Pax Name',
                    'd1' => 'Lead Pax Email',
                    'e1' => 'Lead Pax Phone',
                    'f1' => 'Pnr',
                    'g1' => 'operator',
                    'h1' => 'From',
                    'i1' => 'To',
                    'j1' => 'Seat Type',
                    'k1' => 'commision Fare',
                    'l1' => 'commission',
                    'm1' => 'Tds',
					'n1' => 'Net Fare',
                    'o1' => 'Conivence Fee',
                    'p1' => 'Markup',
                    'q1' => 'GST',
                    'r1' => 'Discount',
                    's1' => 'Total Fare',
                    't1' => 'Travel date',
                    'u1' => 'Booked On',
                   
                );
                // field names in data set 
                $fields = array('a' => '', // empty for sl. no.
                    'b' => 'app_reference',
                    'c' => 'lead_pax_name',
                    'd' => 'lead_pax_email',
                    'e' => 'lead_pax_phone_number',
                    'f' => 'Pnr',
                    'g' => 'operator',
                    'h' => 'from',
                    'i' => 'to',
                    'j' => 'bus_type',
                    'k' => 'Comm.Fare',
                    'l' => 'commission',
                    'm' => 'TDS',
                    'n' => 'NetFare',
                    'o' => 'convinence_amount',
                    'p' => 'Markup',
                    'q' => 'gst',
                    'r' => 'Discount',
                    's' => 'grand_total',
                  	't' => 'Travel date',
                    'u' => 'booked_date',
                                        
                );
           
            $excel_sheet_properties = array(
                'title' => 'Confirmed_Booking_BusReport_' . date('d-M-Y'),
                'creator' => 'Accentria Solutions',
                'description' => 'Confirmed_Booking_BusReport',
                'sheet_title' => 'Confirmed_Booking_BusReport'
            );

            $this->load->library('provab_excel'); // we need this provab_excel library to export excel.
            $this->provab_excel->excel_export($headings, $fields, $export_data, $excel_sheet_properties);
        } 
    }
    public function export_cancelled_booking_bus_report($op = '') {
        $this->load->model('bus_model');
        $get_data = $this->input->get();
        $condition = array();
        //From-Date and To-Date
        $from_date = trim(@$get_data['created_datetime_from']);
        $to_date = trim(@$get_data['created_datetime_to']);

        $filter_data = $this->format_basic_search_filters('bus');
        $condition = $filter_data['filter_condition'];

        //Unset the Status Filter
        if (valid_array($condition) == true) {
            foreach ($condition as $ck => $cv) {

                if ($cv[0] == 'BD.status') {
                    unset($condition[$ck]);
                }
            }
        }

        //Adding Confirmed Status Filter
        $condition[] = array('BD.status', '=', $this->db->escape('BOOKING_CANCELLED'));

        $bus_booking_data = $this->bus_model->b2c_bus_report($condition, false, 0, 2000); //Maximum 500 Data Can be exported at time
        $bus_booking_data = $this->booking_data_formatter->format_bus_booking_data($bus_booking_data, $this->current_module);
        $bus_booking_data = $bus_booking_data['data']['booking_details'];



        $export_data = array();
        //debug($bus_booking_data);exit;
        foreach ($bus_booking_data as $k => $v) {
           
			$export_data[$k]['app_reference'] = $v['app_reference'];
			$export_data[$k]['lead_pax_name'] = $v['lead_pax_name'];
            $export_data[$k]['lead_pax_email'] = $v['lead_pax_email'];
            $export_data[$k]['lead_pax_phone_number'] = $v['lead_pax_phone_number'];
            $export_data[$k]['Pnr'] = $v['pnr'];
            $export_data[$k]['operator'] = $v['operator'];
            $export_data[$k]['from'] = $v['departure_from'];
            $export_data[$k]['to'] = $v['arrival_to'];
           	$export_data[$k]['bus_type'] = $v['bus_type'];
           	$export_data[$k]['Comm.Fare'] = $v['fare'];
           	$export_data[$k]['commission'] = $v['admin_commission'];
           	$export_data[$k]['TDS'] = $v['admin_tds'];
           	$export_data[$k]['NetFare'] = $v['admin_buying_price'];
           	$export_data[$k]['convinence_amount'] = $v['convinence_amount'];
           	$export_data[$k]['Markup'] = $v['admin_markup'];
           	$export_data[$k]['gst'] = $v['gst'];
           	$export_data[$k]['Discount'] = $v['discount'];
           	$export_data[$k]['grand_total'] = $v['grand_total'];
           	$export_data[$k]['Travel date'] = $v['journey_datetime'];
           	$export_data[$k]['booked_date'] = date('d-m-Y', strtotime($v['booked_date']));
           	        		
           	
        }
//debug($export_data[$k]['Payment Status']);exit;
        if ($op == 'excel') { // excel export
           $headings = array('a1' => 'Sl. No.',
                    'b1' => 'app_reference',
                    'c1' => 'Lead Pax Name',
                    'd1' => 'Lead Pax Email',
                    'e1' => 'Lead Pax Phone',
                    'f1' => 'Pnr',
                    'g1' => 'operator',
                    'h1' => 'From',
                    'i1' => 'To',
                    'j1' => 'Seat Type',
                    'k1' => 'commision Fare',
                    'l1' => 'commission',
                    'm1' => 'Tds',
					'n1' => 'Net Fare',
                    'o1' => 'Conivence Fee',
                    'p1' => 'Markup',
                    'q1' => 'GST',
                    'r1' => 'Discount',
                    's1' => 'Total Fare',
                    't1' => 'Travel date',
                    'u1' => 'Booked On',
                   
                );
                // field names in data set 
                $fields = array('a' => '', // empty for sl. no.
                    'b' => 'app_reference',
                    'c' => 'lead_pax_name',
                    'd' => 'lead_pax_email',
                    'e' => 'lead_pax_phone_number',
                    'f' => 'Pnr',
                    'g' => 'operator',
                    'h' => 'from',
                    'i' => 'to',
                    'j' => 'bus_type',
                    'k' => 'Comm.Fare',
                    'l' => 'commission',
                    'm' => 'TDS',
                    'n' => 'NetFare',
                    'o' => 'convinence_amount',
                    'p' => 'Markup',
                    'q' => 'gst',
                    'r' => 'Discount',
                    's' => 'grand_total',
                  	't' => 'Travel date',
                    'u' => 'booked_date',
                                        
                );
           
            $excel_sheet_properties = array(
                'title' => 'Cancelled_Booking_BusReport_' . date('d-M-Y'),
                'creator' => 'Accentria Solutions',
                'description' => 'Cancelled_Booking_BusReport',
                'sheet_title' => 'Cancelled_Booking_BusReport'
            );

            $this->load->library('provab_excel'); // we need this provab_excel library to export excel.
            $this->provab_excel->excel_export($headings, $fields, $export_data, $excel_sheet_properties);
        } 
    }
public function export_confirmed_booking_bus_report_b2b($op = '', $b_status = '') {
        $this->load->model('bus_model');
        $get_data = $this->input->get();
        $condition = array();
        //From-Date and To-Date
        $from_date = trim(@$get_data['created_datetime_from']);
        $to_date = trim(@$get_data['created_datetime_to']);

        $filter_data = $this->format_basic_search_filters('bus', 1);
        $condition = $filter_data['filter_condition'];
		//debug($condition); exit;
        //Unset the Status Filter
        if (valid_array($condition) == true) {
            foreach ($condition as $ck => $cv) {

                if ($cv[0] == 'BD.status') {
                    unset($condition[$ck]);
                }
            }
        }

        if($b_status != "confirmed_cancelled"){
	        //Adding Confirmed Status Filter
	        $condition[] = array('BD.status', '=', $this->db->escape('BOOKING_CONFIRMED'));
	        $bus_booking_data = $this->bus_model->b2b_bus_report($condition, false, 0, 2000);
	    }

        if($b_status == "confirmed_cancelled"){
        	$bus_booking_data = $this->bus_model->b2b_bus_report($condition, false, 0, 2000, $b_status);
        	$sheet_name = "confirmed_cancelled";
        }
        else
        	$sheet_name = "confirmed";
		//echo $this->bus_model->db->last_query(); exit;
        //Maximum 500 Data Can be exported at time
        $bus_booking_data = $this->booking_data_formatter->format_bus_booking_data($bus_booking_data, "b2b");
        $bus_booking_data = $bus_booking_data['data']['booking_details'];


        $total_profit = 0;
        $export_data = array();
        //debug($bus_booking_data);exit;
        foreach ($bus_booking_data as $k => $v) {
        	//debug($v); exit;
			$export_data[$k]['app_reference'] = $v['app_reference'];
			$export_data[$k]['agency_name'] = $v['agency_name']." - ".provab_decrypt($v['agency_id']);
			$export_data[$k]['lead_pax_name'] = $v['lead_pax_name'];
            $export_data[$k]['lead_pax_email'] = $v['lead_pax_email'];
            $export_data[$k]['lead_pax_phone_number'] = $v['lead_pax_phone_number'];
            $export_data[$k]['supp_name'] = $v['supp_name'];
            $export_data[$k]['Pnr'] = $v['pnr'];
            $export_data[$k]['Transaction'] = $v['transaction'];
            $export_data[$k]['operator'] = $v['operator'];
            $export_data[$k]['from'] = $v['departure_from'];
            $export_data[$k]['to'] = $v['arrival_to'];
           	$export_data[$k]['bus_type'] = $v['bus_type'];
           	$export_data[$k]['base_fare'] = $v['total_api_base_fare'];
           	$export_data[$k]['tax'] = $v['total_api_tax'];
           	$export_data[$k]['Comm.Fare'] = $v['fare'];
           	$export_data[$k]['Netfare'] = $v['admin_buying_price']+$v['admin_tds'];
           	$export_data[$k]['admin_markup'] = $v['admin_markup'];
           	$export_data[$k]['agent_markup'] = $v['agent_markup'];
           	$export_data[$k]['admin_commission'] = $v['admin_commission'];
           	$export_data[$k]['agent_commission'] = $v['agent_commission'];
           	$export_data[$k]['admin_tds'] = $v['admin_tds'];
           	$export_data[$k]['agent_tds'] = $v['agent_tds'];
           	$export_data[$k]['pace_commission'] = $v['admin_commission']-$v['agent_commission'];
           	$export_data[$k]['pace_tds'] = $v['admin_tds']-$v['agent_tds'];

           	$profit = $v['admin_markup']+$export_data[$k]['pace_commission']-$export_data[$k]['pace_tds'];
            $total_profit += $profit;

           	$export_data[$k]['gst'] = $v['gst'];
			$export_data[$k]['convenience'] = $v['convinence_amount'];
           	$export_data[$k]['Price Deducted From Agent'] = $v['agent_buying_price'];
           	$export_data[$k]['grand_total'] = $v['grand_total'];
           	$export_data[$k]['profit'] = $profit;
           	$export_data[$k]['booked_date'] = date('d-m-Y', strtotime($v['booked_date']));
           	$export_data[$k]['status'] = $v['status'];

			$do_det = $this->custom_db->single_table_records("booking_source", "*", array("travel_id" => $v['travel_id']));
			//debug($v['travel_id']);
			if($v['travel_id'] != 0 && $do_det["status"] == 1)
			{
				$export_data[$k]['direct_operator'] = $do_det["data"][0]["name"];
			}
			else
				$export_data[$k]['direct_operator'] = "NA";
           	
        }
        if(!empty($export_data))
        {
        	$export_data['last_row']['app_reference'] = "Total";
        	$export_data['last_row']['profit'] = $total_profit;
        }

        if ($op == 'excel') { // excel export
           $headings = array('a1' => 'Sl. No.',
                    'b1' => 'app_reference',
                    'c1' => 'agency_name',
                    'd1' => 'Lead Pax Name',
                    'e1' => 'Lead Pax Email',
                    'f1' => 'Lead Pax Phone',
                    'g1' => 'Sup. Name',
					'h1' => 'Direct API',
                    'i1' => 'Pnr',
                    'j1' => 'operator',
                    'k1' => 'From',
                    'l1' => 'To',
                    'm1' => 'Seat Type',
                    'n1' => 'Base Fare',
                    'o1' => 'Tax',
                    'p1' => 'Supp. Total Fare',
                    'q1' => 'Netfare',
                    'r1' => 'Admin_markup',
					's1' => 'Agent_markup',
					't1' => 'Supp. commission',
                    'u1' => 'Supp. tds',
                    'v1' => 'Pace_commission',
                    'w1' => 'Pace_tds',
                    'x1' => 'Agent_commission',
                    'y1' => 'Agent_tds',
                    'z1' => 'Gst',
                    'aa1' => 'Price Deducted From Agent',
                    'ab1' => 'Convenience',
                    'ac1' => 'Total Price',
                    'ad1' => 'Your Profit',
                    'ae1' => 'Booked On',
                    'af1' => 'Transaction No',
                    'ag1' => 'Booking Status'
                );
                // field names in data set 
                $fields = array('a' => '', // empty for sl. no.
                    'b' => 'app_reference',
                    'c' => 'agency_name',
                    'd' => 'lead_pax_name',
                    'e' => 'lead_pax_email',
                    'f' => 'lead_pax_phone_number',
                    'g' => 'supp_name',
					'h' => 'direct_operator',
                    'i' => 'Pnr',
                    'j' => 'operator',
                    'k' => 'from',
                    'l' => 'to',
                    'm' => 'bus_type',
                    'n' => 'base_fare',
                    'o' => 'tax',
                    'p' => 'Comm.Fare',
                    'q' => 'Netfare',
                    'r' => 'admin_markup',
                    's' => 'agent_markup',
                    't' => 'admin_commission',
                    'u' => 'admin_tds',
                    'v' => 'pace_commission',
                    'w' => 'pace_tds',
                    'x' => 'agent_commission',
                    'y' => 'agent_tds',
                    'z' => 'gst',
                  	'aa' => 'Price Deducted From Agent',
                  	'ab' => 'convenience',
                    'ac' => 'grand_total',
                    'ad' => 'profit',
                    'ae' => 'booked_date',
                    'af' => 'Transaction',
                    'ag' => 'status'               	    
                );
           
            $excel_sheet_properties = array(
                'title' => $sheet_name.'_BusReport_' . date('d-M-Y'),
                'creator' => 'Accentria Solutions',
                'description' => $sheet_name.'_BusReport',
                'sheet_title' => $sheet_name.'_BusReport'
            );

            $this->load->library('provab_excel'); // we need this provab_excel library to export excel.
            $this->provab_excel->excel_export($headings, $fields, $export_data, $excel_sheet_properties);
        } 
    }
    public function export_cancelled_booking_bus_report_b2b($op = '') {
        $this->load->model('bus_model');
        $get_data = $this->input->get();
        $condition = array();
        //From-Date and To-Date
        $from_date = trim(@$get_data['created_datetime_from']);
        $to_date = trim(@$get_data['created_datetime_to']);

        $filter_data = $this->format_basic_search_filters('bus', 1);
        $condition = $filter_data['filter_condition'];

        //Unset the Status Filter
        if (valid_array($condition) == true) {
            foreach ($condition as $ck => $cv) {

                if ($cv[0] == 'BD.status') {
                    unset($condition[$ck]);
                }
            }
        }

        //Adding Confirmed Status Filter
        $condition[] = array('BD.is_cancelled', '=', 1);

        $bus_booking_data = $this->bus_model->b2b_bus_report($condition, false, 0, 2000);
         //Maximum 500 Data Can be exported at time
        $bus_booking_data = $this->booking_data_formatter->format_bus_booking_data($bus_booking_data, 'b2b');
        $bus_booking_data = $bus_booking_data['data']['booking_details'];


		
        $export_data = array();
        //debug($bus_booking_data);exit;
        foreach ($bus_booking_data as $k => $v) {
           
			//debug($v); exit;
			$export_data[$k]['app_reference'] = $v['app_reference'];
			$export_data[$k]['agency_name'] = $v['agency_name']." - ".provab_decrypt($v['agency_id']);
			$export_data[$k]['lead_pax_name'] = $v['lead_pax_name'];
            $export_data[$k]['lead_pax_email'] = $v['lead_pax_email'];
            $export_data[$k]['lead_pax_phone_number'] = $v['lead_pax_phone_number'];
            $export_data[$k]['supp_name'] = $v['supp_name'];
            $export_data[$k]['Pnr'] = $v['pnr'];
            $export_data[$k]['Transaction'] = $v['transaction'];
            $export_data[$k]['operator'] = $v['operator'];
            $export_data[$k]['from'] = $v['departure_from'];
            $export_data[$k]['to'] = $v['arrival_to'];
           	$export_data[$k]['bus_type'] = $v['bus_type'];
           	$export_data[$k]['base_fare'] = $v['total_api_base_fare'];
           	$export_data[$k]['tax'] = $v['total_api_tax'];
           	$export_data[$k]['Comm.Fare'] = $v['fare'];
           	$export_data[$k]['Netfare'] = $v['admin_buying_price']+$v['admin_tds'];
           	$export_data[$k]['admin_markup'] = $v['admin_markup'];
           	$export_data[$k]['agent_markup'] = $v['agent_markup'];
           	$export_data[$k]['admin_commission'] = $v['admin_commission'];
           	$export_data[$k]['agent_commission'] = $v['agent_commission'];
           	$export_data[$k]['admin_tds'] = $v['admin_tds'];
           	$export_data[$k]['agent_tds'] = $v['agent_tds'];
           	$export_data[$k]['pace_commission'] = $v['admin_commission']-$v['agent_commission'];
           	$export_data[$k]['pace_tds'] = $v['admin_tds']-$v['agent_tds'];
           	$export_data[$k]['convenience'] = $v['convinence_amount'];

           	$profit = $v['admin_markup']+$export_data[$k]['pace_commission']-$export_data[$k]['pace_tds'];
            $total_profit += $profit;

           	$export_data[$k]['gst'] = $v['gst'];
           	$export_data[$k]['Price Deducted From Agent'] = $v['agent_buying_price'];
           	$export_data[$k]['grand_total'] = $v['grand_total'];
           	$export_data[$k]['profit'] = $profit;
           	$export_data[$k]['booked_date'] = date('d-m-Y', strtotime($v['booked_date']));
           	$export_data[$k]['status'] = $v['status'];        		

           	//Cancellation & Refund Details #START
           	$bcds = $this->bus_model->get_cancellation_details($v['app_reference']);
           	$refund_status = "INPROGRESS";
           	$refund = 0;
           	$cancellation_charge = 0;
			$api_refund_status = "INPROGRESS";
           	$api_refund = 0;
           	$api_cancellation_charge = 0;
           	$commission_reversed = 0;
			$supplier_commission_reversed = 0;
			$cancelled_seats = 0;
           	foreach ($bcds as $key => $value) {
           		//debug($value); exit;
           		$refund_status = $value["refund_status"];
           		if(isset($value["refund_amount"]) && !empty($value["refund_amount"])){
           			$refund += $value["refund_amount"];
					$api_refund += $value["api_refund_amount"];
           		}
           	$commission_reversed += $value["commission_reversed"];
			$supplier_commission_reversed += $value["supp_commission_reversed"];
           	if(isset($value["cancel_charge"]) && !empty($value["cancel_charge"])){
           		$cancellation_charge += $value["cancel_charge"];
				$api_cancellation_charge += $value["api_cancel_charge"];
           		}
				$cancelled_seats++;
           	}
           	$export_data[$k]['refund_status'] = $refund_status;
			$export_data[$k]['commission_reversed'] = $commission_reversed;
			$export_data[$k]['supplier_commission_reversed'] = $supplier_commission_reversed;
           	$export_data[$k]['refund_amount'] = $refund-$commission_reversed;
           	$export_data[$k]['cancel_charge'] = $cancellation_charge;
			$export_data[$k]['api_refund_amount'] = $api_refund-$supplier_commission_reversed;
           	$export_data[$k]['api_cancel_charge'] = $api_cancellation_charge;
			$export_data[$k]['cancelled_seats'] = $cancelled_seats;
			$export_data[$k]['journey_date'] = $v["journey_datetime"];
			$export_data[$k]['cancelled_date'] = $v["cancelled_date"];
           	
        }
        //debug($export_data);exit;
        if(!empty($export_data))
        {
        	$export_data['last_row']['app_reference'] = "Total";
        	$export_data['last_row']['profit'] = $total_profit;
        }

        if ($op == 'excel') { // excel export
           $headings = array('a1' => 'Sl. No.',
                    'b1' => 'app_reference',
                    'c1' => 'Transaction No',
                    'd1' => 'agency_name',
                    'e1' => 'Sup. Name',
                    'f1' => 'Pnr',
                    'g1' => 'operator',
					'h1' => 'Seats',
                    'i1' => 'Base Fare',
                    'j1' => 'Tax',
                    'k1' => 'Supp. Total Fare',
                    'l1' => 'Admin Netfare',
                    'm1' => 'Admin_markup',
					'n1' => 'Agent_markup',
					'o1' => 'Supp. commission',
                    'p1' => 'Supp. tds',
                    'q1' => 'Pace_commission',
                    'r1' => 'Pace_tds',
                    's1' => 'Agent_commission',
                    't1' => 'Agent_tds',
                    'u1' => 'Gst',
                    'v1' => 'convenience',
                    'w1' => 'Supp. Cancel Charge',
                    'x1' => 'Supp. Refund',
					'y1' => 'Supp Comm. Reversed',
                    'z1' => 'Refund Status',
					'aa1' => 'Cancel Charge',
                    'ab1' => 'Refund',
                    'ac1' => 'Comm. Reversed',
                    'ad1' => 'Total Price',
                    'ae1' => 'Booked On',
					'af1' => 'Journey On',
					'ag1' => 'Cancelled On',
                    'ah1' => 'Booking Status'
                );
           // field names in data set 
                $fields = array('a' => '', // empty for sl. no.
                    'b' => 'app_reference',
                    'c' => 'Transaction',					
                    'd' => 'agency_name',
                    'e' => 'supp_name',
                    'f' => 'Pnr',
                    'g' => 'operator',
					'h' => 'cancelled_seats',
                    'i' => 'base_fare',
                    'j' => 'tax',
                    'k' => 'Comm.Fare',
                    'l' => 'Netfare',
                    'm' => 'admin_markup',
                    'n' => 'agent_markup',
                    'o' => 'admin_commission',
                    'p' => 'admin_tds',
                    'q' => 'pace_commission',
                    'r' => 'pace_tds',
                    's' => 'agent_commission',
                    't' => 'agent_tds',
                    'u' => 'gst',
                  	'v' => 'convenience',
                    'w' => 'cancel_charge',
                    'x' => 'api_refund_amount',
					'y' => 'supplier_commission_reversed',
					'z' => 'refund_status',
                    'aa' => 'cancel_charge',
                    'ab' => 'refund_amount',
                    'ac' => 'commission_reversed',
                    'ad' => 'grand_total',
                    'ae' => 'booked_date',
					'af' => 'journey_date',
					'ag' => 'cancelled_date',
                    'ah' => 'status'               	    
                );
            $excel_sheet_properties = array(
                'title' => 'Cancelled_Booking_BusReport_' . date('d-M-Y'),
                'creator' => 'Accentria Solutions',
                'description' => 'Cancelled_Booking_BusReport',
                'sheet_title' => 'Cancelled_Booking_BusReport'
            );

            $this->load->library('provab_excel'); // we need this provab_excel library to export excel.
            $this->provab_excel->excel_export($headings, $fields, $export_data, $excel_sheet_properties);
        } 
    }

public function export_confirmed_booking_transfer_report($op = '') {
        $this->load->model('transferv1_model');
        $get_data = $this->input->get();
        $condition = array();
        //From-Date and To-Date
        $from_date = trim(@$get_data['created_datetime_from']);
        $to_date = trim(@$get_data['created_datetime_to']);

        $filter_data = $this->format_basic_search_filters('transfers');
        $condition = $filter_data['filter_condition'];

        //Unset the Status Filter
        if (valid_array($condition) == true) {
            foreach ($condition as $ck => $cv) {

                if ($cv[0] == 'BD.status') {
                    unset($condition[$ck]);
                }
            }
        }

        //Adding Confirmed Status Filter
        $condition[] = array('BD.status', '=', $this->db->escape('BOOKING_CONFIRMED'));

        $transfer_booking_data = $this->transferv1_model->b2c_transferv1_report($condition, false, 0, 2000); //Maximum 500 Data Can be exported at time
        $transfer_booking_data = $this->booking_data_formatter->format_transferv1_booking_data($transfer_booking_data, $this->current_module);
        $transfer_booking_data = $transfer_booking_data['data']['booking_details'];



        $export_data = array();
        //debug($transfer_booking_data);exit;
        foreach ($transfer_booking_data as $k => $v) {
           
			$export_data[$k]['app_reference'] = $v['app_reference'];
			$export_data[$k]['confirmation_reference'] = $v['confirmation_reference'];
			$export_data[$k]['lead_pax_name'] = $v['lead_pax_name'];
            $export_data[$k]['lead_pax_email'] = $v['lead_pax_email'];
            $export_data[$k]['lead_pax_phone_number'] = $v['lead_pax_phone_number'];
            $export_data[$k]['product_name'] = $v['product_name'];
            $export_data[$k]['grade_desc'] = $v['grade_desc'];
            $export_data[$k]['travel_date'] = $v['travel_date'];
           	$export_data[$k]['NO of adult_count'] = $v['adult_count'];
           	$export_data[$k]['NO of child_count'] = $v['child_count'];
           	$export_data[$k]['NO of youth_count'] = $v['youth_count'];
           	$export_data[$k]['NO of senior_count'] = $v['senior_count'];
           	$export_data[$k]['NO of infant_count'] = $v['infant_count'];
           	$export_data[$k]['Comm.Fare'] = $v['fare'];
           	$export_data[$k]['commission'] = $v['admin_commission'];
           	$export_data[$k]['tds'] = $v['net_commission_tds'];
           	$export_data[$k]['admin_net_fare'] = $v['admin_net_fare'];
           	$export_data[$k]['admin_markup'] = $v['admin_markup'];
           	$export_data[$k]['convinence_amount'] = $v['convinence_amount'];
           	$export_data[$k]['gst'] = $v['gst'];
           	$export_data[$k]['Discount'] = $v['discount'];
           	$export_data[$k]['grand_total'] = $v['grand_total'];
           	$export_data[$k]['Travel date'] = $v['journey_datetime'];
           	$export_data[$k]['booked_date'] = date('d-m-Y', strtotime($v['voucher_date']));
           	        		
           	
        }
//debug($export_data[$k]['booked_date']);exit;
        if ($op == 'excel') { // excel export
        	//error_reporting(E_ALL);
           $headings = array('a1' => 'Sl. No.',
                    'b1' => 'APP reference',
                    'c1' => 'Lead Pax Name',
                    'd1' => 'Lead Pax Email',
                    'e1' => 'Lead Pax Phone',
                    'f1' => 'confirmation reference',
                    'g1' => 'product name',
                    'h1' => 'No of Adult',
                    'i1' => 'No of Child',
                    'j1' => 'No of youth',
                    'k1' => 'No of senior',
                    'l1' => 'No of infant',
                    'm1' => 'City',
					'n1' => 'Travel Date',
                    'o1' => 'Commission Fare',
                    'p1' => 'Commission',
                    'q1' => 'TDS',
                    'r1' => 'Admin NetFare',
                    's1' => 'Admin Markup',
                    't1' => 'GST',
                    'u1' => 'Discount',
                    'v1' => 'Total Fare',
                    'w1' =>'Convinence Fee',
                    'x1'=> 'Booked On',
                    
                );
                // field names in data set 
                $fields = array('a' => '', // empty for sl. no.
                    'b' => 'app_reference',
                    'c' => 'lead_pax_name',
                    'd' => 'lead_pax_email',
                    'e' => 'lead_pax_phone_number',
                    'f' => 'confirmation_reference',
                    'g' => 'product_name',
                    'h' => 'NO of adult_count',
                    'i' => 'NO of child_count',
                    'j' => 'NO of youth_count',
                    'k' => 'NO of senior_count',
                    'l' => 'NO of infant_count',
                    'm' => 'grade_desc',
                    'n' => 'travel_date',
                    'o' => 'Comm.Fare',
                    'p' => 'commission',
                    'q' => 'tds',
                    'r' => 'admin_net_fare',
                    's' => 'admin_markup',
                  	't' => 'gst',
                    'u' => 'Discount',
                    'v' => 'grand_total',
                    'w' => 'convinence_amount',
                    'x' => 'booked_date',
                                        
                );
           
            $excel_sheet_properties = array(
                'title' => 'Confirmed_Booking_transferReport_' . date('d-M-Y'),
                'creator' => 'Accentria Solutions',
                'description' => 'Confirmed_Booking_transferReport',
                'sheet_title' => 'Confirmed_Booking_transferReport'
            );

            $this->load->library('provab_excel'); // we need this provab_excel library to export excel.
            $this->provab_excel->excel_export($headings, $fields, $export_data, $excel_sheet_properties);
        } 
    }
    public function export_cancelled_booking_transfer_report($op = '') {
        $this->load->model('transferv1_model');
        $get_data = $this->input->get();
        $condition = array();
        //From-Date and To-Date
        $from_date = trim(@$get_data['created_datetime_from']);
        $to_date = trim(@$get_data['created_datetime_to']);

        $filter_data = $this->format_basic_search_filters('transfers');
        $condition = $filter_data['filter_condition'];

        //Unset the Status Filter
        if (valid_array($condition) == true) {
            foreach ($condition as $ck => $cv) {

                if ($cv[0] == 'BD.status') {
                    unset($condition[$ck]);
                }
            }
        }

        //Adding Confirmed Status Filter
        $condition[] = array('BD.status', '=', $this->db->escape('BOOKING_CANCELLED'));

        $transfer_booking_data = $this->transferv1_model->b2c_transferv1_report($condition, false, 0, 2000); //Maximum 500 Data Can be exported at time
        $transfer_booking_data = $this->booking_data_formatter->format_transferv1_booking_data($transfer_booking_data, $this->current_module);
        $transfer_booking_data = $transfer_booking_data['data']['booking_details'];



        $export_data = array();
        //debug($transfer_booking_data);exit;
        foreach ($transfer_booking_data as $k => $v) {
           
			$export_data[$k]['app_reference'] = $v['app_reference'];
			$export_data[$k]['confirmation_reference'] = $v['confirmation_reference'];
			$export_data[$k]['lead_pax_name'] = $v['lead_pax_name'];
            $export_data[$k]['lead_pax_email'] = $v['lead_pax_email'];
            $export_data[$k]['lead_pax_phone_number'] = $v['lead_pax_phone_number'];
            $export_data[$k]['product_name'] = $v['product_name'];
            $export_data[$k]['grade_desc'] = $v['grade_desc'];
            $export_data[$k]['travel_date'] = $v['travel_date'];
           	$export_data[$k]['NO of adult_count'] = $v['adult_count'];
           	$export_data[$k]['NO of child_count'] = $v['child_count'];
           	$export_data[$k]['NO of youth_count'] = $v['youth_count'];
           	$export_data[$k]['NO of senior_count'] = $v['senior_count'];
           	$export_data[$k]['NO of infant_count'] = $v['infant_count'];
           	$export_data[$k]['Comm.Fare'] = $v['fare'];
           	$export_data[$k]['commission'] = $v['admin_commission'];
           	$export_data[$k]['tds'] = $v['net_commission_tds'];
           	$export_data[$k]['admin_net_fare'] = $v['admin_net_fare'];
           	$export_data[$k]['admin_markup'] = $v['admin_markup'];
           	$export_data[$k]['convinence_amount'] = $v['convinence_amount'];
           	$export_data[$k]['gst'] = $v['gst'];
           	$export_data[$k]['Discount'] = $v['discount'];
           	$export_data[$k]['grand_total'] = $v['grand_total'];
           	$export_data[$k]['Travel date'] = $v['journey_datetime'];
           	$export_data[$k]['booked_date'] = date('d-m-Y', strtotime($v['voucher_date']));
           	        		
           	
        }
//debug($export_data[$k]['booked_date']);exit;
        if ($op == 'excel') { // excel export
        	//error_reporting(E_ALL);
           $headings = array('a1' => 'Sl. No.',
                    'b1' => 'APP reference',
                    'c1' => 'Lead Pax Name',
                    'd1' => 'Lead Pax Email',
                    'e1' => 'Lead Pax Phone',
                    'f1' => 'confirmation reference',
                    'g1' => 'product name',
                    'h1' => 'No of Adult',
                    'i1' => 'No of Child',
                    'j1' => 'No of youth',
                    'k1' => 'No of senior',
                    'l1' => 'No of infant',
                    'm1' => 'City',
					'n1' => 'Travel Date',
                    'o1' => 'Commission Fare',
                    'p1' => 'Commission',
                    'q1' => 'TDS',
                    'r1' => 'Admin NetFare',
                    's1' => 'Admin Markup',
                    't1' => 'GST',
                    'u1' => 'Discount',
                    'v1' => 'Total Fare',
                    'w1' =>'Convinence Fee',
                    'x1'=> 'Booked On',
                    );
                // field names in data set 
                $fields = array('a' => '', // empty for sl. no.
                    'b' => 'app_reference',
                    'c' => 'lead_pax_name',
                    'd' => 'lead_pax_email',
                    'e' => 'lead_pax_phone_number',
                    'f' => 'confirmation_reference',
                    'g' => 'product_name',
                    'h' => 'NO of adult_count',
                    'i' => 'NO of child_count',
                    'j' => 'NO of youth_count',
                    'k' => 'NO of senior_count',
                    'l' => 'NO of infant_count',
                    'm' => 'grade_desc',
                    'n' => 'travel_date',
                    'o' => 'Comm.Fare',
                    'p' => 'commission',
                    'q' => 'tds',
                    'r' => 'admin_net_fare',
                    's' => 'admin_markup',
                  	't' => 'gst',
                    'u' => 'Discount',
                    'v' => 'grand_total',
                    'w' => 'convinence_amount',
                    'x' => 'booked_date',
                                        
                );
           
           
            $excel_sheet_properties = array(
                'title' => 'Confirmed_Booking_transferReport_' . date('d-M-Y'),
                'creator' => 'Accentria Solutions',
                'description' => 'Confirmed_Booking_transferReport',
                'sheet_title' => 'Confirmed_Booking_transferReport'
            );

            $this->load->library('provab_excel'); // we need this provab_excel library to export excel.
            $this->provab_excel->excel_export($headings, $fields, $export_data, $excel_sheet_properties);
        } 
    }
    public function export_confirmed_booking_transfer_report_b2b($op = '') {
        $this->load->model('transferv1_model');
        $get_data = $this->input->get();
        $condition = array();
        //From-Date and To-Date
        $from_date = trim(@$get_data['created_datetime_from']);
        $to_date = trim(@$get_data['created_datetime_to']);

        $filter_data = $this->format_basic_search_filters('transfers');
        $condition = $filter_data['filter_condition'];

        //Unset the Status Filter
        if (valid_array($condition) == true) {
            foreach ($condition as $ck => $cv) {

                if ($cv[0] == 'BD.status') {
                    unset($condition[$ck]);
                }
            }
        }

        //Adding Confirmed Status Filter
        $condition[] = array('BD.status', '=', $this->db->escape('BOOKING_CONFIRMED'));

        $transfer_booking_data = $this->transferv1_model->b2b_transferv1_report($condition, false, 0, 2000); //Maximum 500 Data Can be exported at time
        $transfer_booking_data = $this->booking_data_formatter->format_transferv1_booking_data($transfer_booking_data, $this->current_module);
        $transfer_booking_data = $transfer_booking_data['data']['booking_details'];



        $export_data = array();
        //debug($transfer_booking_data);exit;
        foreach ($transfer_booking_data as $k => $v) {
           
			$export_data[$k]['app_reference'] = $v['app_reference'];
			$export_data[$k]['agency_name'] = $v['agency_name'];
			$export_data[$k]['lead_pax_name'] = $v['lead_pax_name'];
            $export_data[$k]['lead_pax_email'] = $v['lead_pax_email'];
            $export_data[$k]['lead_pax_phone_number'] = $v['lead_pax_phone_number'];
            $export_data[$k]['product_name'] = $v['product_name'];
            $export_data[$k]['Destination'] = $v['Destination'];
            $export_data[$k]['created_datetime'] = $v['created_datetime'];
            $export_data[$k]['travel_date'] = $v['travel_date'];
           	$export_data[$k]['confirmation_reference'] = $v['confirmation_reference'];
           	$export_data[$k]['Comm_Fare'] = $v['fare'];
           	$export_data[$k]['commission'] = $v['admin_commission'];
           	$export_data[$k]['admin_tds'] = $v['admin_tds'];
           	$export_data[$k]['net_fare'] = $v['net_fare'];
           	$export_data[$k]['admin_profit'] = $v['admin_commission'];
           	$export_data[$k]['admin_markup'] = $v['admin_markup'];
           	$export_data[$k]['agent_commission'] = $v['agent_commission'];
           	$export_data[$k]['agent_tds'] = $v['agent_tds'];
           	$export_data[$k]['agent_netfare'] = $v['agent_buying_price'];
           	$export_data[$k]['agent_markup'] = $v['agent_markup'];
           	$export_data[$k]['gst'] = $v['gst'];
           	$export_data[$k]['grand_total'] = $v['grand_total'];
           	        		
           	
        }
//debug($export_data[$k]['booked_date']);exit;
        if ($op == 'excel') { // excel export
        	//error_reporting(E_ALL);
           $headings = array('a1' => 'Sl. No.',
                    'b1' => 'APP reference',
                    'c1' => 'Agency name',
                    'd1' => 'Lead Pax Name',
                    'e1' => 'Lead Pax Email',
                    'f1' => 'Lead Pax Phone Number',
                    'g1' => 'Activity Name',
                    'h1' => 'Acitvity Location',
                    'i1' => 'Booked On',
                    'j1' => 'Journey Date',
                    'k1' => 'Confirmation Reference',
                    'l1' => 'Commission Fare',
                    'm1' => 'Commission',
					'n1' => 'TDS',
                    'o1' => 'Admin NetFare',
                    'p1' => 'Admin Profit',
                    'q1' => 'Admin Markup',
                    'r1' => 'Agent Commission',
                    's1' => 'Agent TDS',
                    't1' => 'Agent Net Fare',
                    'u1' => 'Agent Markup',
                    'v1' => 'GST',
                    'z1' => 'TotalFare',
                   
                );
                // field names in data set 
                $fields = array('a' => '', // empty for sl. no.
                    'b' => 'app_reference',
                    'c' => 'agency_name',
                    'd' => 'lead_pax_name',
                    'e' => 'lead_pax_email',
                    'f' => 'lead_pax_phone_number',
                    'g' => 'product_name',
                    'h' => 'Destination',
                    'i' => 'created_datetime',
                    'j' => 'travel_date',
                    'k' => 'confirmation_reference',
                    'l' => 'Comm_Fare',
                    'm' => 'commission',
                    'n' => 'admin_tds',
                    'o' => 'net_fare',
                    'p' => 'admin_profit',
                    'q' => 'admin_markup',
                    'r' => 'agent_commission',
                    's' => 'agent_tds',
                  	't' => 'agent_netfare',
                    'u' => 'agent_markup',
                    'v' => 'gst',
                    'z' => 'grand_total',
                                        
                );
           
            $excel_sheet_properties = array(
                'title' => 'Confirmed_Booking_transferReport_' . date('d-M-Y'),
                'creator' => 'Accentria Solutions',
                'description' => 'Confirmed_Booking_transferReport',
                'sheet_title' => 'Confirmed_Booking_transferReport'
            );

            $this->load->library('provab_excel'); // we need this provab_excel library to export excel.
            $this->provab_excel->excel_export($headings, $fields, $export_data, $excel_sheet_properties);
        } 
    }
     public function export_cancelled_booking_transfer_report_b2b($op = '') {
        $this->load->model('transferv1_model');
        $get_data = $this->input->get();
        $condition = array();
        //From-Date and To-Date
        $from_date = trim(@$get_data['created_datetime_from']);
        $to_date = trim(@$get_data['created_datetime_to']);

        $filter_data = $this->format_basic_search_filters('transfers');
        $condition = $filter_data['filter_condition'];

        //Unset the Status Filter
        if (valid_array($condition) == true) {
            foreach ($condition as $ck => $cv) {

                if ($cv[0] == 'BD.status') {
                    unset($condition[$ck]);
                }
            }
        }

        //Adding Confirmed Status Filter
        $condition[] = array('BD.status', '=', $this->db->escape('BOOKING_CANCELLED'));

        $transfer_booking_data = $this->transferv1_model->b2b_transferv1_report($condition, false, 0, 2000); //Maximum 500 Data Can be exported at time
        $transfer_booking_data = $this->booking_data_formatter->format_transferv1_booking_data($transfer_booking_data, $this->current_module);
        $transfer_booking_data = $transfer_booking_data['data']['booking_details'];



        $export_data = array();
        //debug($transfer_booking_data);exit;
        foreach ($transfer_booking_data as $k => $v) {
           
			$export_data[$k]['app_reference'] = $v['app_reference'];
			$export_data[$k]['agency_name'] = $v['agency_name'];
			$export_data[$k]['lead_pax_name'] = $v['lead_pax_name'];
            $export_data[$k]['lead_pax_email'] = $v['lead_pax_email'];
            $export_data[$k]['lead_pax_phone_number'] = $v['lead_pax_phone_number'];
            $export_data[$k]['product_name'] = $v['product_name'];
            $export_data[$k]['Destination'] = $v['Destination'];
            $export_data[$k]['created_datetime'] = $v['created_datetime'];
            $export_data[$k]['travel_date'] = $v['travel_date'];
           	$export_data[$k]['confirmation_reference'] = $v['confirmation_reference'];
           	$export_data[$k]['Comm_Fare'] = $v['fare'];
           	$export_data[$k]['commission'] = $v['admin_commission'];
           	$export_data[$k]['admin_tds'] = $v['admin_tds'];
           	$export_data[$k]['net_fare'] = $v['net_fare'];
           	$export_data[$k]['admin_profit'] = $v['admin_commission'];
           	$export_data[$k]['admin_markup'] = $v['admin_markup'];
           	$export_data[$k]['agent_commission'] = $v['agent_commission'];
           	$export_data[$k]['agent_tds'] = $v['agent_tds'];
           	$export_data[$k]['agent_netfare'] = $v['agent_buying_price'];
           	$export_data[$k]['agent_markup'] = $v['agent_markup'];
           	$export_data[$k]['gst'] = $v['gst'];
           	$export_data[$k]['grand_total'] = $v['grand_total'];
           	        		
           	
        }
//debug($export_data[$k]['booked_date']);exit;
        if ($op == 'excel') { // excel export
        	//error_reporting(E_ALL);
           $headings = array('a1' => 'Sl. No.',
                    'b1' => 'APP reference',
                    'c1' => 'Agency name',
                    'd1' => 'Lead Pax Name',
                    'e1' => 'Lead Pax Email',
                    'f1' => 'Lead Pax Phone Number',
                    'g1' => 'Activity Name',
                    'h1' => 'Acitvity Location',
                    'i1' => 'Booked On',
                    'j1' => 'Journey Date',
                    'k1' => 'Confirmation Reference',
                    'l1' => 'Commission Fare',
                    'm1' => 'Commission',
					'n1' => 'TDS',
                    'o1' => 'Admin NetFare',
                    'p1' => 'Admin Profit',
                    'q1' => 'Admin Markup',
                    'r1' => 'Agent Commission',
                    's1' => 'Agent TDS',
                    't1' => 'Agent Net Fare',
                    'u1' => 'Agent Markup',
                    'v1' => 'GST',
                    'z1' => 'TotalFare',
                   
                );
                // field names in data set 
                $fields = array('a' => '', // empty for sl. no.
                    'b' => 'app_reference',
                    'c' => 'agency_name',
                    'd' => 'lead_pax_name',
                    'e' => 'lead_pax_email',
                    'f' => 'lead_pax_phone_number',
                    'g' => 'product_name',
                    'h' => 'Destination',
                    'i' => 'created_datetime',
                    'j' => 'travel_date',
                    'k' => 'confirmation_reference',
                    'l' => 'Comm_Fare',
                    'm' => 'commission',
                    'n' => 'admin_tds',
                    'o' => 'net_fare',
                    'p' => 'admin_profit',
                    'q' => 'admin_markup',
                    'r' => 'agent_commission',
                    's' => 'agent_tds',
                  	't' => 'agent_netfare',
                    'u' => 'agent_markup',
                    'v' => 'gst',
                    'z' => 'grand_total',
                                        
                );
           
            $excel_sheet_properties = array(
                'title' => 'Cancelled_Booking_transferReport_' . date('d-M-Y'),
                'creator' => 'Accentria Solutions',
                'description' => 'Cancelled_Booking_transferReport',
                'sheet_title' => 'Cancelled_Booking_transferReport'
            );

            $this->load->library('provab_excel'); // we need this provab_excel library to export excel.
            $this->provab_excel->excel_export($headings, $fields, $export_data, $excel_sheet_properties);
        } 
    }
    public function export_confirmed_booking_activities_report($op = '') {
        $this->load->model('sightseeing_model');
        $get_data = $this->input->get();
        $condition = array();
        //From-Date and To-Date
        $from_date = trim(@$get_data['created_datetime_from']);
        $to_date = trim(@$get_data['created_datetime_to']);

        $filter_data = $this->format_basic_search_filters('activities');
        $condition = $filter_data['filter_condition'];

        //Unset the Status Filter
        if (valid_array($condition) == true) {
            foreach ($condition as $ck => $cv) {

                if ($cv[0] == 'BD.status') {
                    unset($condition[$ck]);
                }
            }
        }

        //Adding Confirmed Status Filter
        $condition[] = array('BD.status', '=', $this->db->escape('BOOKING_CONFIRMED'));

        $activites_booking_data = $this->sightseeing_model->b2c_sightseeing_report($condition, false, 0, 2000); //Maximum 500 Data Can be exported at time
        $activites_booking_data = $this->booking_data_formatter->format_sightseeing_booking_data($activites_booking_data, $this->current_module);
        $activites_booking_data = $activites_booking_data['data']['booking_details'];



        $export_data = array();
        //debug($activites_booking_data);exit;
        foreach ($activites_booking_data as $k => $v) {
           
			$export_data[$k]['app_reference'] = $v['app_reference'];
			$export_data[$k]['confirmation_reference'] = $v['confirmation_reference'];
			$export_data[$k]['lead_pax_name'] = $v['lead_pax_name'];
            $export_data[$k]['lead_pax_email'] = $v['lead_pax_email'];
            $export_data[$k]['lead_pax_phone_number'] = $v['lead_pax_phone_number'];
            $export_data[$k]['product_name'] = $v['product_name'];
            $export_data[$k]['No of Adults'] = $v['adult_count'];
            $export_data[$k]['No of Child'] = $v['child_count'];
            $export_data[$k]['No of youth'] = $v['youth_count'];
            $export_data[$k]['No of Senior'] = $v['senior_count'];
            $export_data[$k]['No of infant'] = $v['infant_count'];
            $export_data[$k]['location'] = $v['cutomer_city'];
            //$export_data[$k]['created_datetime'] = $v['created_datetime'];
            $export_data[$k]['travel_date'] = $v['travel_date'];
           	//$export_data[$k]['currency'] = $v['currency'];
           	$export_data[$k]['Comm_Fare'] = $v['fare'];
           	$export_data[$k]['commission'] = $v['admin_commission'];
           	$export_data[$k]['admin_tds'] = $v['admin_tds'];
           	$export_data[$k]['net_fare'] = $v['admin_net_fare'];
           //	$export_data[$k]['admin_profit'] = $v['admin_commission'];
           	$export_data[$k]['admin_markup'] = $v['admin_markup'];
           	$export_data[$k]['convinence_amount'] = $v['convinence_amount'];
           	$export_data[$k]['gst'] = $v['gst'];
           	$export_data[$k]['Discount'] = $v['discount'];
           	$export_data[$k]['amount'] = $v['grand_total'];
           	$export_data[$k]['Booked_on'] = $v['voucher_date'];
           //	$export_data[$k]['grand_total'] = $v['grand_total'];
           	        		
           	
        }
//debug($export_data[$k]['booked_date']);exit;
        if ($op == 'excel') { // excel export
        	//error_reporting(E_ALL);
           $headings = array('a1' => 'Sl. No.',
                    'b1' => 'APP reference',
                    'c1' => 'Confirmation_Reference',
                    'd1' => 'Lead Pax Name',
                    'e1' => 'Lead Pax Email',
                    'f1' => 'Lead Pax Phone Number',
                    'g1' => 'Product Name',
                    'h1' => 'No of Adults',
                    'i1' => 'No of Child',
                    'j1' => 'No of youth',
                    'k1' => 'No of Senior',
                    'l1' => 'No of infant',
                    'm1' => 'City',
					'n1' => 'Travel Date',
                   //'o1' => 'Currency',
                    'p1' => 'Commission Fare',
                    'q1' => 'Commission',
                    'r1' => 'Tds',
                    's1' => 'Admin NetFare',
                    't1' => 'Admin Markup',
                    'u1' => 'Convinence amount',
                    'v1' => 'GST',
                    'w1' => 'Discount',
                   'x1' => 'Customer Paid amount',
                    'y1' => 'Booked On',
                   
                );
                // field names in data set 
                $fields = array('a' => '', // empty for sl. no.
                    'b' => 'app_reference',
                    'c' => 'confirmation_reference',
                    'd' => 'lead_pax_name',
                    'e' => 'lead_pax_email',
                    'f' => 'lead_pax_phone_number',
                    'g' => 'product_name',
                    'h' => 'No of Adults',
                    'i' => 'No of Child',
                    'j' => 'No of youth',
                    'k' => 'No of Senior',
                    'l' => 'No of infant',
                    'm' => 'location',
                    'n' => 'travel_date',
                   // 'o' => 'currency',
                    'p' => 'Comm_Fare',
                    'q' => 'commission',
                    'r' => 'admin_tds',
                    's' => 'net_fare',
                  	't' => 'admin_markup',
                    'u' => 'convinence_amount',
                    'v' => 'gst',
                    'w' => 'Discount',
                   'x' => 'amount',
                   'y' => 'Booked_on',
                                        
                );
           
            $excel_sheet_properties = array(
                'title' => 'Confirmed_Booking_activitesReport_' . date('d-M-Y'),
                'creator' => 'Accentria Solutions',
                'description' => 'Confirmed_Booking_activitesReport',
                'sheet_title' => 'Confirmed_Booking_activitesReport'
            );

            $this->load->library('provab_excel'); // we need this provab_excel library to export excel.
            $this->provab_excel->excel_export($headings, $fields, $export_data, $excel_sheet_properties);
        } 
    }
    public function export_cancelled_booking_activities_report($op = '') {
        $this->load->model('sightseeing_model');
        $get_data = $this->input->get();
        $condition = array();
        //From-Date and To-Date
        $from_date = trim(@$get_data['created_datetime_from']);
        $to_date = trim(@$get_data['created_datetime_to']);

        $filter_data = $this->format_basic_search_filters('activities');
        $condition = $filter_data['filter_condition'];

        //Unset the Status Filter
        if (valid_array($condition) == true) {
            foreach ($condition as $ck => $cv) {

                if ($cv[0] == 'BD.status') {
                    unset($condition[$ck]);
                }
            }
        }

        //Adding Confirmed Status Filter
        $condition[] = array('BD.status', '=', $this->db->escape('BOOKING_CANCELLED'));

        $activites_booking_data = $this->sightseeing_model->b2c_sightseeing_report($condition, false, 0, 2000); //Maximum 500 Data Can be exported at time
        $activites_booking_data = $this->booking_data_formatter->format_sightseeing_booking_data($activites_booking_data, $this->current_module);
        $activites_booking_data = $activites_booking_data['data']['booking_details'];



        $export_data = array();
        //debug($activites_booking_data);exit;
        foreach ($activites_booking_data as $k => $v) {
           
			$export_data[$k]['app_reference'] = $v['app_reference'];
			$export_data[$k]['confirmation_reference'] = $v['confirmation_reference'];
			$export_data[$k]['lead_pax_name'] = $v['lead_pax_name'];
            $export_data[$k]['lead_pax_email'] = $v['lead_pax_email'];
            $export_data[$k]['lead_pax_phone_number'] = $v['lead_pax_phone_number'];
            $export_data[$k]['product_name'] = $v['product_name'];
            $export_data[$k]['No of Adults'] = $v['adult_count'];
            $export_data[$k]['No of Child'] = $v['child_count'];
            $export_data[$k]['No of youth'] = $v['youth_count'];
            $export_data[$k]['No of Senior'] = $v['senior_count'];
            $export_data[$k]['No of infant'] = $v['infant_count'];
            $export_data[$k]['location'] = $v['cutomer_city'];
            //$export_data[$k]['created_datetime'] = $v['created_datetime'];
            $export_data[$k]['travel_date'] = $v['travel_date'];
           //	$export_data[$k]['currency'] = $v['currency'];
           	$export_data[$k]['Comm_Fare'] = $v['fare'];
           	$export_data[$k]['commission'] = $v['admin_commission'];
           	$export_data[$k]['admin_tds'] = $v['admin_tds'];
           	$export_data[$k]['net_fare'] = $v['admin_net_fare'];
           //	$export_data[$k]['admin_profit'] = $v['admin_commission'];
           	$export_data[$k]['admin_markup'] = $v['admin_markup'];
           	$export_data[$k]['convinence_amount'] = $v['convinence_amount'];
           	$export_data[$k]['gst'] = $v['gst'];
           	$export_data[$k]['Discount'] = $v['discount'];
           	$export_data[$k]['amount'] = $v['grand_total'];
           	$export_data[$k]['Booked_on'] = $v['voucher_date'];
           //	$export_data[$k]['grand_total'] = $v['grand_total'];
           	        		
           	
        }
//debug($export_data[$k]['booked_date']);exit;
        if ($op == 'excel') { // excel export
        	//error_reporting(E_ALL);
           $headings = array('a1' => 'Sl. No.',
                    'b1' => 'APP reference',
                    'c1' => 'Confirmation_Reference',
                    'd1' => 'Lead Pax Name',
                    'e1' => 'Lead Pax Email',
                    'f1' => 'Lead Pax Phone Number',
                    'g1' => 'Product Name',
                    'h1' => 'No of Adults',
                    'i1' => 'No of Child',
                    'j1' => 'No of youth',
                    'k1' => 'No of Senior',
                    'l1' => 'No of infant',
                    'm1' => 'City',
					'n1' => 'Travel Date',
                   // 'o1' => 'Currency',
                    'p1' => 'Commission Fare',
                    'q1' => 'Commission',
                    'r1' => 'Tds',
                    's1' => 'Admin NetFare',
                    't1' => 'Admin Markup',
                    'u1' => 'Convinence amount',
                    'v1' => 'GST',
                    'w1' => 'Discount',
                   'x1' => 'Customer Paid amount',
                    'y1' => 'Booked On',
                   
                );
                // field names in data set 
                $fields = array('a' => '', // empty for sl. no.
                    'b' => 'app_reference',
                    'c' => 'confirmation_reference',
                    'd' => 'lead_pax_name',
                    'e' => 'lead_pax_email',
                    'f' => 'lead_pax_phone_number',
                    'g' => 'product_name',
                    'h' => 'No of Adults',
                    'i' => 'No of Child',
                    'j' => 'No of youth',
                    'k' => 'No of Senior',
                    'l' => 'No of infant',
                    'm' => 'location',
                    'n' => 'travel_date',
                    //'o' => 'currency',
                    'p' => 'Comm_Fare',
                    'q' => 'commission',
                    'r' => 'admin_tds',
                    's' => 'net_fare',
                  	't' => 'admin_markup',
                    'u' => 'convinence_amount',
                    'v' => 'gst',
                    'w' => 'Discount',
                   'x' => 'amount',
                   'y' => 'Booked_on',
                                        
                );
           
            $excel_sheet_properties = array(
                'title' => 'Cancelled_Booking_activitesReport_' . date('d-M-Y'),
                'creator' => 'Accentria Solutions',
                'description' => 'Cancelled_Booking_activitesReport',
                'sheet_title' => 'Cancelled_Booking_activitesReport'
            );

            $this->load->library('provab_excel'); // we need this provab_excel library to export excel.
            $this->provab_excel->excel_export($headings, $fields, $export_data, $excel_sheet_properties);
        } 
    }
     public function export_confirmed_booking_activities_report_b2b($op = '') {
        $this->load->model('sightseeing_model');
        $get_data = $this->input->get();
        $condition = array();
        //From-Date and To-Date
        $from_date = trim(@$get_data['created_datetime_from']);
        $to_date = trim(@$get_data['created_datetime_to']);

        $filter_data = $this->format_basic_search_filters('activities');
        $condition = $filter_data['filter_condition'];

        //Unset the Status Filter
        if (valid_array($condition) == true) {
            foreach ($condition as $ck => $cv) {

                if ($cv[0] == 'BD.status') {
                    unset($condition[$ck]);
                }
            }
        }

        //Adding Confirmed Status Filter
        $condition[] = array('BD.status', '=', $this->db->escape('BOOKING_CONFIRMED'));

        $activites_booking_data = $this->sightseeing_model->b2b_sightseeing_report($condition, false, 0, 2000); //Maximum 500 Data Can be exported at time
        $activites_booking_data = $this->booking_data_formatter->format_sightseeing_booking_data($activites_booking_data, $this->current_module);
        $activites_booking_data = $activites_booking_data['data']['booking_details'];



        $export_data = array();
       // debug($activites_booking_data);exit;
        foreach ($activites_booking_data as $k => $v) {
           
			$export_data[$k]['app_reference'] = $v['app_reference'];
			$export_data[$k]['agency_name'] = $v['agency_name'];
			$export_data[$k]['lead_pax_name'] = $v['lead_pax_name'];
            $export_data[$k]['lead_pax_email'] = $v['lead_pax_email'];
            $export_data[$k]['lead_pax_phone_number'] = $v['lead_pax_phone_number'];
            $export_data[$k]['product_name'] = $v['product_name'];
            $export_data[$k]['location'] = $v['destination_name'];
           	$export_data[$k]['Booked_on'] = $v['voucher_date'];
            $export_data[$k]['travel_date'] = $v['travel_date'];
           	$export_data[$k]['confirmation_reference'] = $v['confirmation_reference'];
           	$export_data[$k]['Comm_Fare'] = $v['fare'];
           	$export_data[$k]['commission'] = $v['net_commission'];
           	$export_data[$k]['tds'] = $v['net_commission_tds'];
           	$export_data[$k]['net_fare'] = $v['net_fare'];
           	$export_data[$k]['admin_commission'] = $v['admin_commission'];
           	$export_data[$k]['admin_markup'] = $v['admin_markup'];
           	$export_data[$k]['agent_commission'] = $v['agent_commission'];
           	$export_data[$k]['agent_tds'] = $v['agent_tds'];
           	$export_data[$k]['agent_buying_price'] = $v['agent_buying_price'];
           	$export_data[$k]['agent_markup'] = $v['agent_markup'];
           	$export_data[$k]['gst'] = $v['gst'];
           	$export_data[$k]['grand_total'] = $v['grand_total'];
           	        		
           	
        }
//debug($export_data[$k]['booked_date']);exit;
        if ($op == 'excel') { // excel export
        	//error_reporting(E_ALL);
           $headings = array('a1' => 'Sl. No.',
                    'b1' => 'APP reference',
                    'c1' => 'Agency Name',
                    'd1' => 'Lead Pax Name',
                    'e1' => 'Lead Pax Email',
                    'f1' => 'Lead Pax Phone Number',
                    'g1' => 'Activity Name',
                    'h1' => 'Acitvity Location',
                    'i1' => 'BookedOn',
                    'j1' => 'JourneyDate',
                    'k1' => 'Confirmation Reference',
                    'l1' => 'Commission Fare	',
                    'm1' => 'Commission',
					'n1' => 'TDS',
                    'o1' => 'Admin NetFare',
                    'p1' => 'Admin Profit',
                    'q1' => 'Admin Markup',
                    'r1' => 'Agent Commission',
                    's1' => 'Agent TDS',
                    't1' => 'Agent Net Fare',
                    'u1' => 'Agent Markup',
                    'v1' => 'GST',
                    'w1' => 'TotalFare',
                   
                   
                );
                // field names in data set 
                $fields = array('a' => '', // empty for sl. no.
                    'b' => 'app_reference',
                    'c' => 'agency_name',
                    'd' => 'lead_pax_name',
                    'e' => 'lead_pax_email',
                    'f' => 'lead_pax_phone_number',
                    'g' => 'product_name',
                    'h' => 'location',
                    'i' => 'Booked_on',
                    'j' => 'travel_date',
                    'k' => 'confirmation_reference',
                    'l' => 'Comm_Fare',
                    'm' => 'commission',
                    'n' => 'tds',
                    'o' => 'net_fare',
                    'p' => 'admin_commission',
                    'q' => 'admin_markup',
                    'r' => 'agent_commission',
                    's' => 'agent_tds',
                  	't' => 'agent_buying_price',
                    'u' => 'agent_markup',
                    'v' => 'gst',
                    'w' => 'grand_total',
                   
                                        
                );
           
            $excel_sheet_properties = array(
                'title' => 'Confirmed_Booking_activitesReport_' . date('d-M-Y'),
                'creator' => 'Accentria Solutions',
                'description' => 'Confirmed_Booking_activitesReport',
                'sheet_title' => 'Confirmed_Booking_activitesReport'
            );

            $this->load->library('provab_excel'); // we need this provab_excel library to export excel.
            $this->provab_excel->excel_export($headings, $fields, $export_data, $excel_sheet_properties);
        } 
    }

    public function export_cancelled_booking_activities_report_b2b($op = '') {
        $this->load->model('sightseeing_model');
        $get_data = $this->input->get();
        $condition = array();
        //From-Date and To-Date
        $from_date = trim(@$get_data['created_datetime_from']);
        $to_date = trim(@$get_data['created_datetime_to']);

        $filter_data = $this->format_basic_search_filters('activities');
        $condition = $filter_data['filter_condition'];

        //Unset the Status Filter
        if (valid_array($condition) == true) {
            foreach ($condition as $ck => $cv) {

                if ($cv[0] == 'BD.status') {
                    unset($condition[$ck]);
                }
            }
        }

        //Adding Confirmed Status Filter
        $condition[] = array('BD.status', '=', $this->db->escape('BOOKING_CANCELLED'));

        $activites_booking_data = $this->sightseeing_model->b2b_sightseeing_report($condition, false, 0, 2000); //Maximum 500 Data Can be exported at time
        $activites_booking_data = $this->booking_data_formatter->format_sightseeing_booking_data($activites_booking_data, $this->current_module);
        $activites_booking_data = $activites_booking_data['data']['booking_details'];



        $export_data = array();
       // debug($activites_booking_data);exit;
        foreach ($activites_booking_data as $k => $v) {
           
			$export_data[$k]['app_reference'] = $v['app_reference'];
			$export_data[$k]['agency_name'] = $v['agency_name'];
			$export_data[$k]['lead_pax_name'] = $v['lead_pax_name'];
            $export_data[$k]['lead_pax_email'] = $v['lead_pax_email'];
            $export_data[$k]['lead_pax_phone_number'] = $v['lead_pax_phone_number'];
            $export_data[$k]['product_name'] = $v['product_name'];
            $export_data[$k]['location'] = $v['destination_name'];
           	$export_data[$k]['Booked_on'] = $v['voucher_date'];
            $export_data[$k]['travel_date'] = $v['travel_date'];
           	$export_data[$k]['confirmation_reference'] = $v['confirmation_reference'];
           	$export_data[$k]['Comm_Fare'] = $v['fare'];
           	$export_data[$k]['commission'] = $v['net_commission'];
           	$export_data[$k]['tds'] = $v['net_commission_tds'];
           	$export_data[$k]['net_fare'] = $v['net_fare'];
           	$export_data[$k]['admin_commission'] = $v['admin_commission'];
           	$export_data[$k]['admin_markup'] = $v['admin_markup'];
           	$export_data[$k]['agent_commission'] = $v['agent_commission'];
           	$export_data[$k]['agent_tds'] = $v['agent_tds'];
           	$export_data[$k]['agent_buying_price'] = $v['agent_buying_price'];
           	$export_data[$k]['agent_markup'] = $v['agent_markup'];
           	$export_data[$k]['gst'] = $v['gst'];
           	$export_data[$k]['grand_total'] = $v['grand_total'];
           	        		
           	
        }
//debug($export_data[$k]['booked_date']);exit;
        if ($op == 'excel') { // excel export
        	//error_reporting(E_ALL);
           $headings = array('a1' => 'Sl. No.',
                    'b1' => 'APP reference',
                    'c1' => 'Agency Name',
                    'd1' => 'Lead Pax Name',
                    'e1' => 'Lead Pax Email',
                    'f1' => 'Lead Pax Phone Number',
                    'g1' => 'Activity Name',
                    'h1' => 'Acitvity Location',
                    'i1' => 'BookedOn',
                    'j1' => 'JourneyDate',
                    'k1' => 'Confirmation Reference',
                    'l1' => 'Commission Fare	',
                    'm1' => 'Commission',
					'n1' => 'TDS',
                    'o1' => 'Admin NetFare',
                    'p1' => 'Admin Profit',
                    'q1' => 'Admin Markup',
                    'r1' => 'Agent Commission',
                    's1' => 'Agent TDS',
                    't1' => 'Agent Net Fare',
                    'u1' => 'Agent Markup',
                    'v1' => 'GST',
                    'w1' => 'TotalFare',
                   
                   
                );
                // field names in data set 
                $fields = array('a' => '', // empty for sl. no.
                    'b' => 'app_reference',
                    'c' => 'agency_name',
                    'd' => 'lead_pax_name',
                    'e' => 'lead_pax_email',
                    'f' => 'lead_pax_phone_number',
                    'g' => 'product_name',
                    'h' => 'location',
                    'i' => 'Booked_on',
                    'j' => 'travel_date',
                    'k' => 'confirmation_reference',
                    'l' => 'Comm_Fare',
                    'm' => 'commission',
                    'n' => 'tds',
                    'o' => 'net_fare',
                    'p' => 'admin_commission',
                    'q' => 'admin_markup',
                    'r' => 'agent_commission',
                    's' => 'agent_tds',
                  	't' => 'agent_buying_price',
                    'u' => 'agent_markup',
                    'v' => 'gst',
                    'w' => 'grand_total',
                   
                                        
                );
           
            $excel_sheet_properties = array(
                'title' => 'Cancelled_Booking_activitesReport_' . date('d-M-Y'),
                'creator' => 'Accentria Solutions',
                'description' => 'Cancelled_Booking_activitesReport',
                'sheet_title' => 'Cancelled_Booking_activitesReport'
            );

            $this->load->library('provab_excel'); // we need this provab_excel library to export excel.
            $this->provab_excel->excel_export($headings, $fields, $export_data, $excel_sheet_properties);
        } 
    }

    //cancellation reports
    function b2b_bus_cancel_report($offset=0)
	{
		$get_data = $this->input->get();
		//debug($get_data);die();
		$condition = array();
		$page_data = array();

		$filter_data = $this->format_basic_search_filters('bus');
		$page_data['from_date'] = $filter_data['from_date'];
		$page_data['to_date'] = $filter_data['to_date'];
		$condition = $filter_data['filter_condition'];

		//debug($condition); die;
		$total_records = $this->bus_model->b2b_cancel_bus_report($condition, true);
		$table_data = $this->bus_model->b2b_cancel_bus_report($condition, false, $offset, RECORDS_RANGE_2);
		$table_data = $this->booking_data_formatter->format_bus_booking_data($table_data,'b2b');
		$page_data['table_data'] = $table_data['data'];
		/** TABLE PAGINATION */
		$this->load->library('pagination');
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['base_url'] = base_url().'index.php/report/b2b_bus_cancel_report/';
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$page_data['total_rows'] = $config['total_rows'] = $total_records;
		$config['per_page'] = RECORDS_RANGE_2;
		$this->pagination->initialize($config);
		/** TABLE PAGINATION */
		$page_data['total_records'] = $config['total_rows'];
		$page_data['customer_email'] = $this->entity_email;
		$page_data['search_params'] = $get_data;
		$page_data['status_options'] = get_enum_list('booking_status_options');
		$page_data['supplier_option'] = $this->bus_model->bus_supplier();
		/*debug($page_data['status_options']);
		debug($page_data['supplier_option']);
		die();*/
		$agent_info = $this->custom_db->single_table_records('user','*',array('user_type'=>B2B_USER,'domain_list_fk'=>get_domain_auth_id()));
		
		$page_data['agent_details'] = magical_converter(array('k' => 'user_id', 'v' => 'agency_name'), $agent_info);

		$this->template->view('report/b2b_bus_cancel_report', $page_data);
	}

	function b2b_flight_cancel_report($offset=0){
		$current_user_id = $GLOBALS['CI']->entity_user_id;
		$get_data = $this->input->get();
		$condition = array();
		$filter_data = $this->format_basic_search_filters();
		$page_data['from_date'] = $filter_data['from_date'];
		$page_data['to_date'] = $filter_data['to_date'];
		$condition = $filter_data['filter_condition'];

		$total_records = $this->flight_model->b2b_flight_cancel_report($condition, true);
		//echo '<pre>'; print_r($page_data); die;
		$table_data = $this->flight_model->b2b_flight_cancel_report($condition, false, $offset, RECORDS_RANGE_2);
		$table_data = $this->booking_data_formatter->format_flight_booking_data($table_data, $this->current_module);
		$page_data['table_data'] = $table_data['data'];
		
		$this->load->library('pagination');
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['base_url'] = base_url().'index.php/report/b2b_cancel_report_airline/';
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$page_data['total_rows'] = $config['total_rows'] = $total_records;
		$config['per_page'] = RECORDS_RANGE_2;
		$this->pagination->initialize($config);
		/** TABLE PAGINATION */
		$page_data['total_records'] = $config['total_rows'];
		$page_data['search_params'] = $get_data;
		$page_data['status_options'] = get_enum_list('booking_status_options');
		$page_data['supplier_option'] = $this->flight_model->flight_supplier();
		//debug($page_data['supplier_option']);die(//);

		$user_cond = [];
		$user_cond [] = array('U.user_type','=',' (', B2B_USER, ')');
		$user_cond [] = array('U.domain_list_fk' , '=' ,get_domain_auth_id());

		//$agent_info['data'] = $this->user_model->b2b_user_list($user_cond,false);

		$agent_info = $this->custom_db->single_table_records('user','*',array('user_type'=>B2B_USER,'domain_list_fk'=>get_domain_auth_id()));

		$page_data['agent_details'] = magical_converter(array('k' => 'user_id', 'v' => 'agency_name'), $agent_info);		
		
		$this->template->view('report/b2b_cancel_report_airline', $page_data);
	}

	function b2c_flight_cancel_report($offset=0)
	{
		$current_user_id = $GLOBALS['CI']->entity_user_id;
		$get_data = $this->input->get();
		//debug($get_data); die;
		$condition = array();

		$filter_data = $this->format_basic_search_filters();
		$page_data['from_date'] = $filter_data['from_date'];
		$page_data['to_date'] = $filter_data['to_date'];
		$condition = $filter_data['filter_condition'];
		 
		//$condition[] = array('U.user_type', '=', B2C_USER, ' OR ', 'BD.created_by_id');
		$total_records = $this->flight_model->b2c_flight_cancel_report($condition, true);	
		$table_data = $this->flight_model->b2c_flight_cancel_report($condition, false, $offset, RECORDS_RANGE_2);
		$table_data = $this->booking_data_formatter->format_flight_booking_data($table_data, 'b2c', false);
		
		//Export report


		$page_data['table_data'] = $table_data['data'];
		$this->load->library('pagination');
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['base_url'] = base_url().'index.php/report/b2c_cancel_report_airline/';
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$page_data['total_rows'] = $config['total_rows'] = $total_records;
		$config['per_page'] = RECORDS_RANGE_2;
		$this->pagination->initialize($config);
                
               
                
                
		/** TABLE PAGINATION */
		$page_data['total_records'] = $config['total_rows'];
		$page_data['search_params'] = $get_data;
		$page_data['status_options'] = get_enum_list('booking_status_options');
		$page_data['supplier_option'] = $this->flight_model->flight_supplier();
		$this->template->view('report/b2c_cancel_report_airline', $page_data);
	}

	function b2c_bus_cancel_report($offset=0)
	{
		$get_data = $this->input->get();
		$condition = array();
		$page_data = array();

		$filter_data = $this->format_basic_search_filters('bus');
		$page_data['from_date'] = $filter_data['from_date'];
		$page_data['to_date'] = $filter_data['to_date'];
		$condition = $filter_data['filter_condition'];
	
		$total_records = $this->bus_model->b2c_cancel_bus_report($condition, true);
		$table_data = $this->bus_model->b2c_cancel_bus_report($condition, false, $offset, RECORDS_RANGE_2);
		// debug($table_data); exit;
		$table_data = $this->booking_data_formatter->format_bus_booking_data($table_data,$this->current_module);
		
		$page_data['table_data'] = $table_data['data'];

		// debug($table_data); exit;

		/** TABLE PAGINATION */
		$this->load->library('pagination');
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['base_url'] = base_url().'index.php/report/b2c_cancel_report_bus/';
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$page_data['total_rows'] = $config['total_rows'] = $total_records;
		$config['per_page'] = RECORDS_RANGE_2;
		$this->pagination->initialize($config);
		/** TABLE PAGINATION */
		$page_data['total_records'] = $config['total_rows'];
		$page_data['customer_email'] = $this->entity_email;
		$page_data['search_params'] = $get_data;
		$page_data['status_options'] = get_enum_list('booking_status_options');
		$page_data['supplier_option'] = $this->bus_model->bus_supplier();
		//debug($page_data); die;
		$this->template->view('report/b2c_cancel_report_bus', $page_data);
	}

	//offline_flight_report
	function offline_flight_report($offset=0)
	{	error_reporting(0);

		$current_user_id = $GLOBALS['CI']->entity_user_id;
		$get_data = $this->input->get();
		$condition = array();
		$filter_data = $this->format_basic_search_filters();
		$page_data['from_date'] = $filter_data['from_date'];
		$page_data['to_date'] = $filter_data['to_date'];
		$condition = $filter_data['filter_condition'];

		$total_records = $this->flight_model->offline_flight_report($condition, true,0,100000000000);
		//echo '<pre>'; print_r($total_records); die;
		$table_data = $this->flight_model->offline_flight_report($condition, false, $offset, RECORDS_RANGE_2);
		
		if(!empty($table_data['data']['booking_details'])){
			$table_data = $this->booking_data_formatter->format_flight_booking_data($table_data, $this->current_module);
		}

		$page_data['table_data'] = $table_data['data'];
		$this->load->library('pagination');
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['base_url'] = base_url().'index.php/report/offline_flight_report/';
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$page_data['total_rows'] = $config['total_rows'] = $total_records;
		$config['per_page'] = RECORDS_RANGE_2;
		$this->pagination->initialize($config);
		/** TABLE PAGINATION */
		$page_data['total_records'] = $config['total_rows'];
		$page_data['search_params'] = $get_data;
		$page_data['status_options'] = get_enum_list('booking_status_options');

		$user_cond = [];
		$user_cond [] = array('U.user_type','=',' (', ADMIN, ')');
		$user_cond [] = array('U.domain_list_fk' , '=' ,get_domain_auth_id());

		//$agent_info['data'] = $this->user_model->b2b_user_list($user_cond,false);

		$agent_info = $this->custom_db->single_table_records('user','*',array('user_type'=>ADMIN,'domain_list_fk'=>get_domain_auth_id()));
		
		$page_data['agent_details'] = magical_converter(array('k' => 'user_id', 'v' => 'agency_name'), $agent_info);		
		//debug($page_data);die();
		$this->template->view('report/offline_report_airline', $page_data);
	}

	function offline_bus_report($offset=0)
	{	error_reporting(0);

		$current_user_id = $GLOBALS['CI']->entity_user_id;
		$get_data = $this->input->get();
		$condition = array();
		$filter_data = $this->format_basic_search_filters();
		$page_data['from_date'] = $filter_data['from_date'];
		$page_data['to_date'] = $filter_data['to_date'];
		$condition = $filter_data['filter_condition'];

		$total_records = $this->bus_model->offline_bus_report($condition, true);
		$table_data = $this->bus_model->offline_bus_report($condition, false, $offset, RECORDS_RANGE_2);
		
		
		if(!empty($table_data['data']['booking_details'])){
			$table_data = $this->booking_data_formatter->format_bus_booking_data($table_data,'b2b');
		}

		$page_data['table_data'] = $table_data['data'];
		
		$this->load->library('pagination');
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['base_url'] = base_url().'index.php/report/offline_report_airline/';
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$page_data['total_rows'] = $config['total_rows'] = $total_records;
		$config['per_page'] = RECORDS_RANGE_2;
		$this->pagination->initialize($config);
		/** TABLE PAGINATION */
		$page_data['total_records'] = $config['total_rows'];
		$page_data['search_params'] = $get_data;
		$page_data['status_options'] = get_enum_list('booking_status_options');

		$user_cond = [];
		$user_cond [] = array('U.user_type','=',' (', ADMIN, ')');
		$user_cond [] = array('U.domain_list_fk' , '=' ,get_domain_auth_id());

		//$agent_info['data'] = $this->user_model->b2b_user_list($user_cond,false);

		$agent_info = $this->custom_db->single_table_records('user','*',array('user_type'=>ADMIN,'domain_list_fk'=>get_domain_auth_id()));
		
		$page_data['agent_details'] = magical_converter(array('k' => 'user_id', 'v' => 'agency_name'), $agent_info);		
		//debug($page_data);die();
		$this->template->view('report/offline_report_bus', $page_data);
	}

	function offline_hotel_report($offset=0)
	{	//error_reporting(E_ALL);
		//die('78');
		$current_user_id = $GLOBALS['CI']->entity_user_id;
		$get_data = $this->input->get();
		$condition = array();
		$filter_data = $this->format_basic_search_filters();
		$page_data['from_date'] = $filter_data['from_date'];
		$page_data['to_date'] = $filter_data['to_date'];
		$condition = $filter_data['filter_condition'];
		$condition[] = array("booking_billing_type", "=", "'offline'");
		$total_records = $this->hotel_model->offline_hotel_report($condition, true);
		$table_data = $this->hotel_model->offline_hotel_report($condition, false, $offset, RECORDS_RANGE_2);
		
		
		if(!empty($table_data['data']['booking_details'])){
			$table_data = $this->booking_data_formatter->format_hotel_booking_data($table_data,'b2b');
		}

		$page_data['table_data'] = $table_data['data'];
		
		$this->load->library('pagination');
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['base_url'] = base_url().'index.php/report/offline_report_airline/';
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$page_data['total_rows'] = $config['total_rows'] = $total_records;
		$config['per_page'] = RECORDS_RANGE_2;
		$this->pagination->initialize($config);
		/** TABLE PAGINATION */
		$page_data['total_records'] = $config['total_rows'];
		$page_data['search_params'] = $get_data;
		$page_data['status_options'] = get_enum_list('booking_status_options');

		$user_cond = [];
		$user_cond [] = array('U.user_type','=',' (', ADMIN, ')');
		$user_cond [] = array('U.domain_list_fk' , '=' ,get_domain_auth_id());

		//$agent_info['data'] = $this->user_model->b2b_user_list($user_cond,false);

		$agent_info = $this->custom_db->single_table_records('user','*',array('user_type'=>ADMIN,'domain_list_fk'=>get_domain_auth_id()));
		
		$page_data['agent_details'] = magical_converter(array('k' => 'user_id', 'v' => 'agency_name'), $agent_info);		
		//debug($page_data);die();
		$this->template->view('report/offline_report_hotel', $page_data);
	}

	/**
	 * Show supplier wise Booking list
	 */
	function bookings_list($module='')
	{
		$get_data = $this->input->get();
		// debug($get_data); exit;
		$condition = array();
		$page_data = array();
		//From-Date and To-Date
		$from_date = trim(@$get_data['created_datetime_from']);
		$to_date = trim(@$get_data['created_datetime_to']);
		//Auto swipe date
		if(empty($from_date) == false && empty($to_date) == false){
			$valid_dates = auto_swipe_dates($from_date, $to_date);
			$from_date = $valid_dates['from_date'];
			$to_date = $valid_dates['to_date'];
		}
		$is_bs_qry = 0;
		if (trim(@$get_data['supplier']) != '') {
			$is_bs_qry = 1;
			$condition[] = array('BD.booking_source', '=', $this->db->escape($get_data['supplier']));
		}
		
		if(empty($from_date) == false) {
			$ymd_from_date = date('Y-m-d', strtotime($from_date));
			$condition[] = array('BD.created_datetime', '>=', $this->db->escape($ymd_from_date));
		}

		if(empty($to_date) == false) {
			$ymd_to_date = date('Y-m-d', strtotime($to_date));
			$condition[] = array('BD.created_datetime', '<=', $this->db->escape($ymd_to_date));
		}

		if (trim(@$get_data['status']) != '') {
			$condition[] = array('BD.status', '=', $this->db->escape($get_data['status']));
		}
		// debug($condition); exit;

		$page_data['table_data'] = $this->module_model->bookings_count($condition, false, $module );
		$page_data['search_params'] = $get_data;
		$page_data['list_data'] = $this->module_model->get_flight_suppliers($module);
		//debug($condition); exit;
		if($module == 'flight'){
			$supp_wise_list = $page_data['table_data'];
			$size = count($condition);
			foreach($supp_wise_list as $index => $sup){
				if(!$is_bs_qry)
					$condition[$size] = array('BD.booking_source', '=', $this->db->escape($sup["sup_id"]));
				
				$pax_count = $this->module_model->get_pax_count($condition, $module)[0]["total_bookings"];
				$page_data['table_data'][$index]["total_bookings"] = $pax_count;
			}
		}
		//debug($page_data); exit;
		$this->template->view('report/booking_list', $page_data);
	}

	function format_flight_tds_gst_report($ftg_raw, $btg_raw, $user_list, $uuid_for_filer)
	{
		$formatted_data = array();
		foreach($ftg_raw AS $k=>$v)
		{
			$formatted_flight_data[$v["uuid"]]=$v;
		}
		$ftg = $formatted_flight_data;

		foreach($btg_raw AS $k=>$v)
		{
			$formatted_bus_data[$v["uuid"]]=$v;
		}
		$btg = $formatted_bus_data;

		$bus_gst_perc = $this->custom_db->single_table_records("gst_master", "gst", array("module"=>"bus"))["data"][0]["gst"];

		foreach($user_list AS $ukey=>$uval) {
			extract($uval);
			if($uuid_for_filer != 0 && $uuid_for_filer != $uuid)
				continue;
			$bus_gst = ($btg[$uuid]["admin_markup"]/100)*$bus_gst_perc;

			$user_list[$ukey]["ftg"]["admin_commission"] = 0;
			$user_list[$ukey]["ftg"]["agent_commission"] = 0;
			$user_list[$ukey]["ftg"]["admin_tds"] = 0;
			$user_list[$ukey]["ftg"]["agent_tds"] = 0;
			$user_list[$ukey]["ftg"]["gst"] = 0;

			$user_list[$ukey]["btg"]["admin_commission"] = 0;
			$user_list[$ukey]["btg"]["agent_commission"] = 0;
			$user_list[$ukey]["btg"]["admin_tds"] = 0;
			$user_list[$ukey]["btg"]["agent_tds"] = 0;
			$user_list[$ukey]["btg"]["gst"] = 0;

			if((isset($ftg[$uuid]) && !empty($ftg[$uuid])) && (isset($btg[$uuid]) && !empty($btg[$uuid])))
			{
				$fpc = $ftg[$uuid]["admin_commission"]-$ftg[$uuid]["agent_commission"];
				$fpt = $ftg[$uuid]["admin_tds"]-$ftg[$uuid]["agent_tds"];
				$user_list[$ukey]["ftg"] = $ftg[$uuid];
				$user_list[$ukey]["ftg"]["pace_commission"] = $fpc;
				$user_list[$ukey]["ftg"]["pace_tds"] = $fpt;

				$bpc = $btg[$uuid]["admin_commission"]-$btg[$uuid]["agent_commission"];
				$bpt = $btg[$uuid]["admin_tds"]-$btg[$uuid]["agent_tds"];
				$user_list[$ukey]["btg"] = $btg[$uuid];
				$user_list[$ukey]["btg"]["pace_commission"] = $bpc;
				$user_list[$ukey]["btg"]["pace_tds"] = $bpt;
				$user_list[$ukey]["btg"]["gst"] = $bus_gst;
			}
			else if(isset($ftg[$uuid]) && !empty($ftg[$uuid]))
			{
				$fpc = $ftg[$uuid]["admin_commission"]-$ftg[$uuid]["agent_commission"];
				$fpt = $ftg[$uuid]["admin_tds"]-$ftg[$uuid]["agent_tds"];
				$user_list[$ukey]["ftg"] = $ftg[$uuid];
				$user_list[$ukey]["ftg"]["pace_commission"] = $fpc;
				$user_list[$ukey]["ftg"]["pace_tds"] = $fpt;
			}
			else if(isset($btg[$uuid]) && !empty($btg[$uuid]))
			{
				$bpc = $btg[$uuid]["admin_commission"]-$btg[$uuid]["agent_commission"];
				$bpt = $btg[$uuid]["admin_tds"]-$btg[$uuid]["agent_tds"];
				$user_list[$ukey]["btg"] = $btg[$uuid];
				$user_list[$ukey]["btg"]["pace_commission"] = $bpc;
				$user_list[$ukey]["btg"]["pace_tds"] = $bpt;
				$user_list[$ukey]["btg"]["gst"] = $bus_gst;
			}
			else{
				unset($user_list[$ukey]);
			}
		}
		return $user_list;
	}
	function tds_gst_report()
	{
		$get_data = $this->input->get();
		$from_date = @$get_data["date_from"];
		$to_date = @$get_data["date_to"];
		$uuid = @$get_data["uuid"];
		$export_excel = @$get_data["export_excel"];
		if(empty($from_date) == false) {
			$ymd_from_date = date('Y-m-d', strtotime($from_date));
			$condition[] = array('bd.created_datetime', '>=', $this->db->escape($ymd_from_date));
		}

		if(empty($to_date) == false) {
			$ymd_to_date = date('Y-m-d', strtotime($to_date));
			$condition[] = array('bd.created_datetime', '<=', $this->db->escape($ymd_to_date));
		}
		$uuid_for_filer = 0;
		if(empty($uuid) == false) {
			$uuid = provab_encrypt($uuid);
			$condition[] = array('u.uuid', '=', $this->db->escape($uuid));
			$uuid_for_filer = $uuid;
		}
		$page_data = array();
		$page_data["search_params"] = $get_data;
		$user_list = $this->domain_management_model->get_agent_list();
		$flight_tds_gst = $this->transaction_model->flight_tds_gst_report($condition);
		$bus_tds_gst = $this->transaction_model->bus_tds_gst_report($condition);
		$page_data["tds_gst"]=$this->format_flight_tds_gst_report($flight_tds_gst, $bus_tds_gst, $user_list, $uuid_for_filer);
		$page_data['user_list'] = $user_list;
		if(isset($export_excel))
			$this->export_tds_gst_report($page_data["tds_gst"]);
		$this->template->view("report/gst_tds_reports", $page_data);
	}
	public function export_tds_gst_report($data) {
        $export_data = array();
       foreach ($data as $k => $v) {
       		$ftg = $v["ftg"];
       		$btg = $v["btg"];
	   		$export_data[$k]['agency_name'] = $v['agency_name'];
			$export_data[$k]['agency_id'] = provab_decrypt($v['uuid']);
	       	$export_data[$k]['fsc'] = $ftg['admin_commission'];
	       	$export_data[$k]['fst'] = $ftg['admin_tds'];
	       	$export_data[$k]['fac'] = $ftg['agent_commission'];
	       	$export_data[$k]['fat'] = $ftg['agent_tds'];
	       	$export_data[$k]['fpc'] = $ftg['admin_commission']-$ftg['agent_commission'];
	       	$export_data[$k]['fpt'] = $ftg['admin_tds']-$ftg['agent_tds'];
	       	$export_data[$k]['fgst'] = $ftg['gst'];
	       	$export_data[$k]['bsc'] = $btg['admin_commission'];
	       	$export_data[$k]['bst'] = $btg['admin_tds'];
	       	$export_data[$k]['bac'] = $btg['agent_commission'];
	       	$export_data[$k]['bat'] = $btg['agent_tds'];
	       	$export_data[$k]['bpc'] = $btg['admin_commission']-$ftg['agent_commission'];
	       	$export_data[$k]['bpt'] = $btg['admin_tds']-$ftg['agent_tds'];
	       	$export_data[$k]['bgst'] = $ftg['gst'];         	
        }
           $headings = array('a1' => 'Sl. No.',
                    'b1' => 'Agency Name',
                    'c1' => 'Agency Id',
                    'd1' => 'FSC',
                    'e1' => 'FST',
                    'f1' => 'FAC',
                    'g1' => 'FAT',
                    'h1' => 'FPC',
                    'i1' => 'FPT',
                    'j1' => 'FGST',
                    'k1' => 'BSC',
                    'l1' => 'BST',
                    'm1' => 'BAC',
                    'n1' => 'BAT',
                    'o1' => 'BPC',
                    'p1' => 'BPT',
                    'q1' => 'BGST',
                );
                // field names in data set 
                $fields = array('a' => '', // empty for sl. no.
                    'b' => 'agency_name',
                    'c' => 'agency_id',
                    'd' => 'fsc',
                    'e' => 'fst',
                    'f' => 'fac',
                    'g' => 'fat',
                    'h' => 'fpc',
                    'i' => 'fpt',
                    'j' => 'fgst',
                    'k' => 'bsc',
                    'l' => 'bst',
                    'm' => 'bac',
                    'n' => 'bat',
                    'o' => 'bpc',
                    'p' => 'bpt',
                    'q' => 'bgst',
                );
           
            $excel_sheet_properties = array(
                'title' => 'TDS_GST_Report'. date('d-M-Y'),
                'creator' => 'Provab',
                'description' => 'TDS_GST_Report',
                'sheet_title' => 'TDS_GST_Report'
            );

            $this->load->library('provab_excel'); // we need this provab_excel library to export excel.
            $this->provab_excel->excel_export($headings, $fields, $export_data, $excel_sheet_properties);
        }

        /**
	 * Flight Report for b2b flight
	 * @param $offset
	 */
	function b2b_common_report($offset=0)
	{	
		$get_data = $this->input->get();
		if(isset($get_data['today_booking_data']) && !empty($get_data['today_booking_data'])){
			$get_data['created_datetime_from'] = $get_data['today_booking_data'];
		}
		$common_report_data = $this->domain_management_model->get_common_booking_details($get_data);
		$page_data["crd"] = $common_report_data;

		$page_data['agency_list'] = $this->domain_management_model->get_agent_list();	
		$page_data["supplier_list"] = $this->domain_management_model->get_all_suplliers(META_AIRLINE_COURSE);

		$this->template->view('report/common_report', $page_data);
	}
	/**
	 * Flight Booking Queue
	 * @param $offset
	 */
	public function booking_queue($offset=0){
		// error_reporting(E_ALL);
		$get_data = $this->input->get();
		$condition = array();
		$filter_data = $this->format_basic_search_filters();
		$page_data['from_date'] = $filter_data['from_date'];
		$page_data['to_date'] = $filter_data['to_date'];
		$condition = $filter_data['filter_condition'];

		$total_records = $this->flight_model->flight_queue_report($condition, true);
		// debug($total_records);exit;
		$table_data = $this->flight_model->flight_queue_report($condition, false, $offset, RECORDS_RANGE_2);
		// echo '<pre>'; print_r($table_data); die;
		$table_data = $this->booking_data_formatter->format_flight_booking_data($table_data, $this->current_module);
		$page_data['table_data'] = $table_data['data'];
		
		$this->load->library('pagination');
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['base_url'] = base_url().'index.php/report/booking_queue/';
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$page_data['total_rows'] = $config['total_rows'] = $total_records;
		$config['per_page'] = RECORDS_RANGE_2;
		$this->pagination->initialize($config);
		/** TABLE PAGINATION */
		$page_data['total_records'] = $config['total_rows'];
		$page_data['search_params'] = $get_data;
		$page_data['status_options'] = get_enum_list('booking_status_options');

		$user_cond = [];
		$user_cond [] = array('U.domain_list_fk' , '=' ,get_domain_auth_id());

		//$agent_info['data'] = $this->user_model->b2b_user_list($user_cond,false);

		// $agent_info = $this->custom_db->single_table_records('user','*',array('user_type'=>B2B_USER,'domain_list_fk'=>get_domain_auth_id()));

		// $page_data['agent_details'] = magical_converter(array('k' => 'user_id', 'v' => 'agency_name'), $agent_info);	
		// $page_data['agency_list'] = $this->domain_management_model->get_agent_list();	
		$page_data["supplier_list"] = $this->domain_management_model->get_all_suplliers(META_AIRLINE_COURSE);
		// debug($page_data);exit;
		$this->template->view('report/booking_queue_airline', $page_data);
	
	
	}

	function b2b_booking_report()
	{
		//echo "<pre>"; print_r($_POST);die;
		$from_date = $this->input->post('created_datetime_from');
		$to_date   = $this->input->post('created_datetime_to');
		$report_data = $this->transaction_model->b2b_booking_report($from_date,$to_date);
		$flight_data = array();
		$bus_data    = array();
		$details     = array();
		$flight_pnr_confirm_count      = 0;
		$flight_pnr_confirm_seat_count = 0;
		$flight_pnr_cancel_count       = 0;
		$flight_pnr_cancel_seat_count  = 0;
		$bus_pnr_confirm_count         = 0;
		$bus_pnr_cancel_count          = 0;
		foreach ($report_data['agent_data'] as $key => $value) {
				$flight_pnr_confirm_seat_count = 0;
				$flight_pnr_cancel_seat_count  = 0;
				$bus_pnr_confirm_seat_count = 0;
				$bus_pnr_cancel_seat_count  = 0;
			foreach ($report_data['flight_details'] as $f_key => $f_value) {
				
				$details = array();
				if($f_key == $value['user_id']){
					$flight_pnr_confirm_count = count($f_value['BOOKING_CONFIRMED']);
					$flight_pnr_cancel_count  = count($f_value['BOOKING_CANCELLED']);
					foreach ($f_value['BOOKING_CONFIRMED'] as $c_key => $c_value) {
						foreach ($c_value['seats'] as $s_key => $s_value) {
							if($s_value['status'] == 'BOOKING_CONFIRMED'){
								$flight_pnr_confirm_seat_count = $flight_pnr_confirm_seat_count + 1;
							}else if($s_value['status'] == 'BOOKING_CANCELLED' || $s_value['status'] == 'REJECTED' || $s_value['status'] == 'BOOKING_INPROGRESS'){
								$flight_pnr_cancel_seat_count = $flight_pnr_cancel_seat_count + 1;
							}
						}
					}
					foreach ($f_value['BOOKING_CANCELLED'] as $c_key => $c_value) {
						foreach ($c_value['seats'] as $s_key => $s_value) {
							if($s_value['status'] == 'BOOKING_CONFIRMED'){
								$flight_pnr_confirm_seat_count = $flight_pnr_confirm_seat_count + 1;
							}else if($s_value['status'] == 'BOOKING_CANCELLED' || $s_value['status'] == 'REJECTED' || $s_value['status'] == 'BOOKING_INPROGRESS'){
								$flight_pnr_cancel_seat_count = $flight_pnr_cancel_seat_count + 1;
							}
						}
					}
					$details['flight_pnr_confirm_count']      = $flight_pnr_confirm_count;
					$details['flight_pnr_cancel_count']       = $flight_pnr_cancel_count;
					$details['flight_pnr_cancel_seat_count']  = $flight_pnr_cancel_seat_count;
					$details['flight_pnr_confirm_seat_count'] = $flight_pnr_confirm_seat_count;
					$details['user_id']                       = $value['user_id'];
					$details['agent_id']                      = $value['uuid'];
					$details['agency_name']                   = $value['agency_name'];
					$details['email']                         = $value['email'];
					$details['address']                       = $value['address'];
					$details['phone']                         = $value['phone'];
					$flight_data[$value['user_id']]            = $details;
				}
			}			foreach ($report_data['bus_details'] as $b_key => $b_value) {
				
				$details = array();
				if($b_key == $value['user_id']){
					$bus_pnr_confirm_count = count($b_value['BOOKING_CONFIRMED']);
					$bus_pnr_cancel_count  = count($b_value['BOOKING_CANCELLED']);
					foreach ($b_value['BOOKING_CONFIRMED'] as $c_key => $c_value) {
						foreach ($c_value['seats'] as $s_key => $s_value) {
							if($s_value['status'] == 'BOOKING_CONFIRMED'){
								$bus_pnr_confirm_seat_count = $bus_pnr_confirm_seat_count + 1;
							}else if($s_value['status'] == 'BOOKING_CANCELLED' || $s_value['status'] == 'REJECTED' || $s_value['status'] == 'BOOKING_INPROGRESS'){
								$bus_pnr_cancel_seat_count = $bus_pnr_cancel_seat_count + 1;
							}
						}
					}
					foreach ($b_value['BOOKING_CANCELLED'] as $c_key => $c_value) {
						foreach ($c_value['seats'] as $s_key => $s_value) {
							if($s_value['status'] == 'BOOKING_CONFIRMED'){
								$bus_pnr_confirm_seat_count = $bus_pnr_confirm_seat_count + 1;
							}else if($s_value['status'] == 'BOOKING_CANCELLED' || $s_value['status'] == 'REJECTED' || $s_value['status'] == 'BOOKING_INPROGRESS'){
								$bus_pnr_cancel_seat_count = $bus_pnr_cancel_seat_count + 1;
							}
						}
					}
					$details['bus_pnr_confirm_count']      = $bus_pnr_confirm_count;
					$details['bus_pnr_cancel_count']       = $bus_pnr_cancel_count;
					$details['bus_pnr_cancel_seat_count']  = $bus_pnr_cancel_seat_count;
					$details['bus_pnr_confirm_seat_count'] = $bus_pnr_confirm_seat_count;
					$details['user_id']                       = $value['user_id'];
					$details['agent_id']                      = $value['uuid'];
					$details['agency_name']                   = $value['agency_name'];
					$details['email']                         = $value['email'];
					$details['address']                       = $value['address'];
					$details['phone']                         = $value['phone'];
					$bus_data[$value['user_id']]               = $details;
				}
			}
		}
		$arranged_data['bus_data']    = $bus_data;
		$arranged_data['flight_data'] = $flight_data;
		$arranged_data1['data']          = array_replace_recursive($arranged_data['bus_data'],$arranged_data['flight_data']);
		$this->template->view('report/agent_wise_booking_report', $arranged_data1);
	}

	function cancellation_queue($offset = 0) {
    	//echo "<pre>get-->>>"; print_r($this->input->get());die;
    	error_reporting(0);
    	$this->load->model('flight_model');
        $get_data = $this->input->get();
        $condition = array();
        $cancel_data=array();
        $CancelQueue=array();
        
        $from_date     = $get_data['created_datetime_from'];
        $to_date       = $get_data['created_datetime_to'];
        $app_reference = $get_data['app_reference'];
        $pnr           = $get_data['pnr'];
        
        $cancellation_details = $this->flight_model->flight_cancellation_queue($from_date,$to_date,$app_reference,$pnr);
        $cancel_data['CancelQueue']=$cancellation_details;
        //******************************cancellation notification*****************************************
		$cancel_queue_count = $this->flight_model->flight_cancel_notification_count();
		$this->session->set_userdata('cancel_queue_count',$cancel_queue_count['cancel_queue_count']);
       $this->template->view('report/cancellation_queue', $cancel_data);
    }

    public function export_agent_wise_booking_report() {
        $from_date = $this->input->post('from_date');
		$to_date   = $this->input->post('to_date');
		$report_data = $this->transaction_model->b2b_booking_report($from_date,$to_date);
		$flight_data = array();
		$bus_data    = array();
		$details     = array();
		$flight_pnr_confirm_count      = 0;
		$flight_pnr_confirm_seat_count = 0;
		$flight_pnr_cancel_count       = 0;
		$flight_pnr_cancel_seat_count  = 0;
		$bus_pnr_confirm_count         = 0;
		$bus_pnr_cancel_count          = 0;
		foreach ($report_data['agent_data'] as $key => $value) {
				$flight_pnr_confirm_seat_count = 0;
				$flight_pnr_cancel_seat_count  = 0;
				$bus_pnr_confirm_seat_count = 0;
				$bus_pnr_cancel_seat_count  = 0;
			foreach ($report_data['flight_details'] as $f_key => $f_value) {
				
				$details = array();
				if($f_key == $value['user_id']){
					$flight_pnr_confirm_count = count($f_value['BOOKING_CONFIRMED']);
					$flight_pnr_cancel_count  = count($f_value['BOOKING_CANCELLED']);
					foreach ($f_value['BOOKING_CONFIRMED'] as $c_key => $c_value) {
						foreach ($c_value['seats'] as $s_key => $s_value) {
							if($s_value['status'] == 'BOOKING_CONFIRMED'){
								$flight_pnr_confirm_seat_count = $flight_pnr_confirm_seat_count + 1;
							}else if($s_value['status'] == 'BOOKING_CANCELLED' || $s_value['status'] == 'REJECTED' || $s_value['status'] == 'BOOKING_INPROGRESS'){
								$flight_pnr_cancel_seat_count = $flight_pnr_cancel_seat_count + 1;
							}
						}
					}
					foreach ($f_value['BOOKING_CANCELLED'] as $c_key => $c_value) {
						foreach ($c_value['seats'] as $s_key => $s_value) {
							if($s_value['status'] == 'BOOKING_CONFIRMED'){
								$flight_pnr_confirm_seat_count = $flight_pnr_confirm_seat_count + 1;
							}else if($s_value['status'] == 'BOOKING_CANCELLED' || $s_value['status'] == 'REJECTED' || $s_value['status'] == 'BOOKING_INPROGRESS'){
								$flight_pnr_cancel_seat_count = $flight_pnr_cancel_seat_count + 1;
							}
						}
					}
					$details['flight_pnr_confirm_count']      = $flight_pnr_confirm_count;
					$details['flight_pnr_cancel_count']       = $flight_pnr_cancel_count;
					$details['flight_pnr_cancel_seat_count']  = $flight_pnr_cancel_seat_count;
					$details['flight_pnr_confirm_seat_count'] = $flight_pnr_confirm_seat_count;
					$details['user_id']                       = $value['user_id'];
					$details['agent_id']                      = $value['uuid'];
					$details['agency_name']                   = $value['agency_name'];
					$details['email']                         = $value['email'];
					$details['address']                       = $value['address'];
					$details['phone']                         = $value['phone'];
					$flight_data[$value['user_id']]            = $details;
				}
			}			foreach ($report_data['bus_details'] as $b_key => $b_value) {
				
				$details = array();
				if($b_key == $value['user_id']){
					$bus_pnr_confirm_count = count($b_value['BOOKING_CONFIRMED']);
					$bus_pnr_cancel_count  = count($b_value['BOOKING_CANCELLED']);
					foreach ($b_value['BOOKING_CONFIRMED'] as $c_key => $c_value) {
						foreach ($c_value['seats'] as $s_key => $s_value) {
							if($s_value['status'] == 'BOOKING_CONFIRMED'){
								$bus_pnr_confirm_seat_count = $bus_pnr_confirm_seat_count + 1;
							}else if($s_value['status'] == 'BOOKING_CANCELLED' || $s_value['status'] == 'REJECTED' || $s_value['status'] == 'BOOKING_INPROGRESS'){
								$bus_pnr_cancel_seat_count = $bus_pnr_cancel_seat_count + 1;
							}
						}
					}
					foreach ($b_value['BOOKING_CANCELLED'] as $c_key => $c_value) {
						foreach ($c_value['seats'] as $s_key => $s_value) {
							if($s_value['status'] == 'BOOKING_CONFIRMED'){
								$bus_pnr_confirm_seat_count = $bus_pnr_confirm_seat_count + 1;
							}else if($s_value['status'] == 'BOOKING_CANCELLED' || $s_value['status'] == 'REJECTED' || $s_value['status'] == 'BOOKING_INPROGRESS'){
								$bus_pnr_cancel_seat_count = $bus_pnr_cancel_seat_count + 1;
							}
						}
					}
					$details['bus_pnr_confirm_count']      = $bus_pnr_confirm_count;
					$details['bus_pnr_cancel_count']       = $bus_pnr_cancel_count;
					$details['bus_pnr_cancel_seat_count']  = $bus_pnr_cancel_seat_count;
					$details['bus_pnr_confirm_seat_count'] = $bus_pnr_confirm_seat_count;
					$details['user_id']                       = $value['user_id'];
					$details['agent_id']                      = $value['uuid'];
					$details['agency_name']                   = $value['agency_name'];
					$details['email']                         = $value['email'];
					$details['address']                       = $value['address'];
					$details['phone']                         = $value['phone'];
					$bus_data[$value['user_id']]               = $details;
				}
			}
		}
		$arranged_data['bus_data']    = $bus_data;
		$arranged_data['flight_data'] = $flight_data;
		$arranged_data1['data']          = array_replace_recursive($arranged_data['bus_data'],$arranged_data['flight_data']);



        $export_data = array();
        foreach ($arranged_data1['data'] as $k => $v) {
            $export_data[$k]['agent_id'] = provab_decrypt($v['agent_id']);
            $export_data[$k]['agency_name'] = $v['agency_name'];
            $export_data[$k]['email'] = provab_decrypt($v['email']);
			$export_data[$k]['bus_pnr_confirm_count'] = $v['bus_pnr_confirm_count'];
            $export_data[$k]['bus_pnr_cancel_count'] = $v['bus_pnr_cancel_count'];
            $export_data[$k]['bus_pnr_cancel_seat_count'] = $v['bus_pnr_cancel_seat_count'];
            $export_data[$k]['bus_pnr_confirm_seat_count'] = $v['bus_pnr_confirm_seat_count'];
           	$export_data[$k]['flight_pnr_confirm_count'] = $v['flight_pnr_confirm_count'];
           	$export_data[$k]['flight_pnr_cancel_count'] = $v['flight_pnr_cancel_count'];
           	$export_data[$k]['flight_pnr_cancel_seat_count'] = $v['flight_pnr_cancel_seat_count'];
           	$export_data[$k]['flight_pnr_confirm_seat_count'] = $v['flight_pnr_confirm_seat_count'];
           	$export_data[$k]['address'] = $v['address'];
           	$export_data[$k]['phone'] = $v['phone'];
           	
        }
       
           $headings = array('a1' => 'Sl. No.',
                    'b1' => 'Agent Id',
                    'c1' => 'Agency Name',
                    'd1' => 'Email',
                    'e1' => 'Bus Pnr Confirm Count',
                    'f1' => 'Bus Pnr Cancel Count',
                    'g1' => 'Bus Pnr Cancel Seat Count',
                    'h1' => 'Bus Pnr Confirm Seat Count',
                    'i1' => 'Flight Pnr Confirm Count',
                    'j1' => 'Flight Pnr Cancel Count',
                    'k1' => 'Flight Pnr Cancel Seat Count',
                    'l1' => 'Flight Pnr Confirm Seat Count',
                    'm1' => 'Address',
					'n1' => 'Phone',
                );
                // field names in data set 
                $fields = array('a' => '', // empty for sl. no.
                    'b' => 'agent_id',
                    'c' => 'agency_name',
                    'd' => 'email',
                    'e' => 'bus_pnr_confirm_count',
                    'f' => 'bus_pnr_cancel_count',
                    'g' => 'bus_pnr_cancel_seat_count',
                    'h' => 'bus_pnr_confirm_seat_count',
                    'i' => 'flight_pnr_confirm_count',
                    'j' => 'flight_pnr_cancel_count',
                    'k' => 'flight_pnr_cancel_seat_count',
                    'l' => 'flight_pnr_confirm_seat_count',
                    'm' => 'address',
                    'n' => 'phone',
                );
            $excel_sheet_properties = array(
                'title' => 'agent_wise_booking_report' . date('d-M-Y'),
                'creator' => 'Accentria Solutions',
                'description' => 'agent_wise_booking_report',
                'sheet_title' => 'agent_wise_booking_report'
            );
            $this->load->library('provab_excel'); // we need this provab_excel library to export excel.
            $this->provab_excel->excel_export($headings, $fields, $export_data, $excel_sheet_properties);
            echo 1;
    }
}



