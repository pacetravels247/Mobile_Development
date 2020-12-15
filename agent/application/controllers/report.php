<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Provab - Provab Application
 * @subpackage Travel Portal
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V2
 */

class Report extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('bus_model');
		$this->load->model('hotel_model');
		$this->load->model('flight_model');
		$this->load->model('car_model');
		$this->load->library('booking_data_formatter');
		$this->load->model('sightseeing_model');
		$this->load->model('transferv1_model');
		$this->load->model('Package_Model');	
	}

	function monthly_booking_report()
	{
		$this->template->view('report/monthly_booking_report');
	}
	function index(){
		$this->flight($offset=0);
	}

function bus($offset=0)
{
		$get_data = $this->input->get();
		$page_data = array();
		$condition = array();
		$filter_data = $this->format_basic_search_filters();
		$page_data['from_date'] = $filter_data['from_date'];
		$page_data['to_date'] = $filter_data['to_date'];
		$condition = $filter_data['filter_condition'];		
		if(isset($get_data['filter_report_data']) == true && empty($get_data['filter_report_data']) == false) {
			$filter_report_data = trim($get_data['filter_report_data']);
			$search_filter_condition = '(BD.app_reference like "%'.$filter_report_data.'%" OR BD.pnr like "%'.$filter_report_data.'%")';
			$total_records = $this->bus_model->filter_booking_report($search_filter_condition, true);
			$table_data = $this->bus_model->filter_booking_report($search_filter_condition, false, $offset, RECORDS_RANGE_2);
		} else {
			$total_records = $this->bus_model->booking($condition, true);
			$table_data = $this->bus_model->booking($condition, false, $offset, RECORDS_RANGE_2);
		}
		$table_data = $this->booking_data_formatter->format_bus_booking_data($table_data, 'b2b');
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
		$this->template->view('report/bus', $page_data);
	}

	function hotel($offset=0)
	{
		$get_data = $this->input->get();
		$page_data = array();
		$condition = array();
		$filter_data = $this->format_basic_search_filters();
		$page_data['from_date'] = $filter_data['from_date'];
		$page_data['to_date'] = $filter_data['to_date'];
		$condition = $filter_data['filter_condition'];
		if(isset($get_data['filter_report_data']) == true && empty($get_data['filter_report_data']) == false) {
			$filter_report_data = trim($get_data['filter_report_data']);
			$search_filter_condition = '(BD.app_reference like "%'.$filter_report_data.'%" OR BD.confirmation_reference like "%'.$filter_report_data.'%")';
			$total_records = $this->hotel_model->filter_booking_report($search_filter_condition, true);
			$table_data = $this->hotel_model->filter_booking_report($search_filter_condition, false, $offset, RECORDS_RANGE_2);
		} else {
			$total_records = $this->hotel_model->booking($condition, true);
			$table_data = $this->hotel_model->booking($condition, false, $offset, RECORDS_RANGE_2);
		}

		$table_data = $this->booking_data_formatter->format_hotel_booking_data($table_data, 'b2b');
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
		$this->template->view('report/hotel', $page_data);
	}

	/**
	 * Flight Report
	 * @param $offset
	 */
	function flight($offset=0)
	{
		$get_data = $this->input->get();
		$page_data = array();
		$condition = array();
		$filter_data = $this->format_basic_search_filters();
		// debug($filter_data);exit;
		$page_data['from_date'] = $filter_data['from_date'];
		$page_data['to_date'] = $filter_data['to_date'];
		$condition = $filter_data['filter_condition'];
		if(isset($get_data['filter_report_data']) == true && empty($get_data['filter_report_data']) == false) {
			$filter_report_data = trim($get_data['filter_report_data']);
			$search_filter_condition = '(BD.in_suff_bal=0 AND TD.app_reference like "%'.$filter_report_data.'%" OR TD.pnr like "%'.$filter_report_data.'%")';
			$total_records = $this->flight_model->filter_booking_report($search_filter_condition, true);
			$table_data = $this->flight_model->filter_booking_report($search_filter_condition);
		} else {
			$total_records = $this->flight_model->booking($condition, true);
			$table_data = $this->flight_model->booking($condition, false, $offset, RECORDS_RANGE_2);
		}
		$table_data = $this->booking_data_formatter->format_flight_booking_data($table_data, 'b2b');
		//debug($table_data); exit;
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
	 * Sightseeing Report
	 * @param $offset
	 */
	function activities($offset=0)
	{
		$get_data = $this->input->get();
		$page_data = array();
		$condition = array();
		$filter_data = $this->format_basic_search_filters();
		$page_data['from_date'] = $filter_data['from_date'];
		$page_data['to_date'] = $filter_data['to_date'];
		$condition = $filter_data['filter_condition'];
		if(isset($get_data['filter_report_data']) == true && empty($get_data['filter_report_data']) == false) {
			$filter_report_data = trim($get_data['filter_report_data']);
			$search_filter_condition = '(BD.app_reference like "%'.$filter_report_data.'%" OR BD.booking_reference like "%'.$filter_report_data.'%")';
			$total_records = $this->sightseeing_model->filter_booking_report($search_filter_condition, true);
			$table_data = $this->sightseeing_model->filter_booking_report($search_filter_condition);
		} else {
			$total_records = $this->sightseeing_model->booking($condition, true);
			$table_data = $this->sightseeing_model->booking($condition, false, $offset, RECORDS_RANGE_2);
		}
		$table_data = $this->booking_data_formatter->format_sightseeing_booking_data($table_data, 'b2b');

		$page_data['table_data'] = $table_data['data'];
		$this->load->library('pagination');
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['base_url'] = base_url().'index.php/report/sightseeing/';
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$page_data['total_rows'] = $config['total_rows'] = $total_records;
		$config['per_page'] = RECORDS_RANGE_2;
		$this->pagination->initialize($config);
		/** TABLE PAGINATION */
		$page_data['total_records'] = $config['total_rows'];
		$this->template->view('report/sightseeing', $page_data);
	}
	/**
	 * Transfers Report
	 * @param $offset
	 */
	function transfers($offset=0)
	{
		$get_data = $this->input->get();
		$page_data = array();
		$condition = array();
		$filter_data = $this->format_basic_search_filters();
		$page_data['from_date'] = $filter_data['from_date'];
		$page_data['to_date'] = $filter_data['to_date'];
		$condition = $filter_data['filter_condition'];
		if(isset($get_data['filter_report_data']) == true && empty($get_data['filter_report_data']) == false) {
			$filter_report_data = trim($get_data['filter_report_data']);
			$search_filter_condition = '(BD.app_reference like "%'.$filter_report_data.'%" OR BD.booking_reference like "%'.$filter_report_data.'%")';
			$total_records = $this->transferv1_model->filter_booking_report($search_filter_condition, true);
			$table_data = $this->transferv1_model->filter_booking_report($search_filter_condition);
		} else {
			$total_records = $this->transferv1_model->booking($condition, true);
			$table_data = $this->transferv1_model->booking($condition, false, $offset, RECORDS_RANGE_2);
		}

		$table_data = $this->booking_data_formatter->format_transferv1_booking_data($table_data, 'b2b');

		$page_data['table_data'] = $table_data['data'];
		$this->load->library('pagination');
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['base_url'] = base_url().'index.php/report/transfers/';
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$page_data['total_rows'] = $config['total_rows'] = $total_records;
		$config['per_page'] = RECORDS_RANGE_2;
		$this->pagination->initialize($config);
		/** TABLE PAGINATION */
		$page_data['total_records'] = $config['total_rows'];
		$this->template->view('report/transfers', $page_data);
	}

	function package($offset=0)
	{
		redirect(base_url());
	}
	/**
	 * Balu A
	 */
	private function format_basic_search_filters()
	{
		$get_data = $this->input->get();
		if(valid_array($get_data) == true) {
			$filter_condition = array();
			//From-Date and To-Date
			$from_date = trim(@$get_data['from_date']);
			$to_date = trim(@$get_data['to_date']);
			//Auto swipe date
			if(empty($from_date) == false && empty($to_date) == false)
			{
				$valid_dates = auto_swipe_dates($from_date, $to_date);
				$from_date = $valid_dates['from_date'];
				$to_date = $valid_dates['to_date'];
			}
			if(empty($from_date) == false) {
				$filter_condition[] = array('DATE(BD.created_datetime)', '>=', '"'.date('Y-m-d', strtotime($from_date)).'"');
			}
			if(empty($to_date) == false) {
				$filter_condition[] = array('DATE(BD.created_datetime)', '<=', '"'.date('Y-m-d', strtotime($to_date)).'"');
			}
			//App reference
			if(isset($get_data['app_reference']) == true && empty($get_data['app_reference']) == false) {
				$filter_condition[] = array('BD.app_reference', '=', '"'.trim($get_data['app_reference']).'"');
			}
			//Booking-Status
			if(isset($get_data['filter_booking_status']) == true && $get_data['filter_booking_status'] == 'BOOKING_CONFIRMED') {
				//Confirmed Booking
				$filter_condition[] = array('BD.status', '=', '"BOOKING_CONFIRMED"');
			} elseif (isset($get_data['filter_booking_status']) == true && $get_data['filter_booking_status'] == 'BOOKING_PENDING') {
				//Pending Booking
				$filter_condition[] = array('BD.status', '=', '"BOOKING_PENDING"');	
			} elseif (isset($get_data['filter_booking_status']) == true && $get_data['filter_booking_status'] == 'BOOKING_CANCELLED') {
				//Cancelled Booking
				$filter_condition[] = array('BD.status', '=', '"BOOKING_CANCELLED"');
			}
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
			if(isset($get_data['daily_sales_report']) == true && $get_data['daily_sales_report'] == ACTIVE) {
				$from_date = date('d-m-Y', strtotime('-1 day'));
				$to_date = date('d-m-Y');
				$filter_condition[] = array('DATE(BD.created_datetime)', '>=', '"'.date('Y-m-d', strtotime($from_date)).'"');
				$filter_condition[] = array('DATE(BD.created_datetime)', '<=', '"'.date('Y-m-d', strtotime($to_date)).'"');
			}
			return array('filter_condition' => $filter_condition, 'from_date' => $from_date, 'to_date' => $to_date);
		}
	}
	public function package_enquiries(){
		$this->load->model('Package_Model');
		$data ['enquiries'] = $this->Package_Model->gerEnquiryPackages ($this->entity_user_id);
		// debug($data);exit;
		$this->template->view ( 'report/package_enquiries', $data );
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
		
		if(empty($from_date) == false) {
			$ymd_from_date = date('Y-m-d', strtotime($from_date));
			$condition[] = array('BD.created_datetime', '>=', $this->db->escape($ymd_from_date));
		}

		if(empty($to_date) == false) {
			$ymd_to_date = date('Y-m-d', strtotime($to_date));
			$condition[] = array('BD.created_datetime', '<=', $this->db->escape($ymd_to_date));
		}

		if (trim(@$get_data['supplier']) != '') {
			$condition[] = array('BD.booking_source', '=', $this->db->escape($get_data['supplier']));
		}

		if (trim(@$get_data['status']) != '') {
			$condition[] = array('BD.status', '=', $this->db->escape($get_data['status']));
		}
		// debug($condition); exit;

		$page_data['table_data'] = $this->module_model->bookings_count($condition, false, $module );
		$page_data['search_params'] = $get_data;
		$page_data['list_data'] = $this->module_model->get_flight_suppliers($module);

		$this->template->view('report/booking_list', $page_data);
	}

	// Excel export
	public function export_confirmed_booking_bus_report_b2b($op = '', $b_status = '') {

        $this->load->model('bus_model');
        $get_data = $this->input->get();
        $condition = array();
        //From-Date and To-Date
        $from_date = trim(@$get_data['created_datetime_from']);
        $to_date = trim(@$get_data['created_datetime_to']);

        $filter_data = $this->format_basic_search_filters('bus');
        //die('888');
        $condition = $filter_data['filter_condition'];
        //debug($condition);die('44');
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
        }
         //Maximum 500 Data Can be exported at time
        $bus_booking_data = $this->booking_data_formatter->format_bus_booking_data($bus_booking_data, 'b2b');
        $bus_booking_data = $bus_booking_data['data']['booking_details'];

        //debug($bus_booking_data);die('======');
        $total_profit = 0;
        $export_data = array();
        //debug($bus_booking_data);exit;
        foreach ($bus_booking_data as $k => $v) {
            $profit = $v['admin_markup']-$v['agent_commission'];
            $total_profit += $profit;
			$export_data[$k]['app_reference'] = $v['app_reference'];
			$export_data[$k]['lead_pax_name'] = $v['lead_pax_name'];
            $export_data[$k]['lead_pax_email'] = $v['lead_pax_email'];
            $export_data[$k]['lead_pax_phone_number'] = $v['lead_pax_phone_number'];
            $export_data[$k]['Pnr'] = $v['pnr'];
            $export_data[$k]['operator'] = $v['operator'];
            $export_data[$k]['from'] = $v['departure_from'];
            $export_data[$k]['to'] = $v['arrival_to'];

            $export_data[$k]['agent_net_fare'] = roundoff_number($v['agent_buying_price'] - $v['gst']);
            $export_data[$k]['commission'] = $v['agent_commission'];
            $export_data[$k]['tds'] = $v['agent_tds'];
            $export_data[$k]['agent_markup'] = $v['agent_markup'];
            $export_data[$k]['gst'] = $v['gst'];
            $export_data[$k]['total_fare'] = $v['grand_total'];
            $export_data[$k]['opening_balance'] = $v['opening_balance'];
            $export_data[$k]['closing_balance'] = $v['closing_balance'];
            $export_data[$k]['travel_date'] = app_friendly_absolute_date($v['journey_datetime']);
            $export_data[$k]['booked_on'] = $v['booked_date'];
			$export_data[$k]['con_amt'] = $v['convinence_amount'];
			$export_data[$k]['payment_gateway'] = $v['booking_billing_type'];
			$export_data[$k]['payment_mode'] = $v['payment_mode'];
           	$export_data[$k]['status'] = $v['status'];        		
           	
        }
        if(!empty($export_data))
        {
        	$export_data['last_row']['app_reference'] = "Total";
        	$export_data['last_row']['profit'] = $total_profit;
        }

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
                    'j1' => 'Agent Net Fare',
                    'k1' => 'Commision',
                    'l1' => 'TDS',
                    'm1' => 'Agent Markup',
					'n1' => 'GST',
                    'o1' => 'Total Fare',
                    'p1' => 'opening_balance',
                    'q1' => 'closing_balance',
                    'r1' => 'travel_date',
                    's1' => 'booked_on',
					't1' => 'Convenience',
					'u1' => 'Payment Gateway',
					'v1' => 'Payment Mode',
                    'w1' => 'Booking Status',
                    
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
                    'j' => 'agent_net_fare',
                    'k' => 'commission',
                    'l' => 'tds',
                    'm' => 'agent_markup',
                    'n' => 'gst',
                    'o' => 'total_fare',
                    'p' => 'opening_balance',
                    'q' => 'closing_balance',
                    'r' => 'travel_date',
                    's' => 'booked_on',
					't' => 'con_amt',
					'u' => 'payment_gateway',
					'v' => 'payment_mode',
                  	'w' => 'status',               	    
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

    public function export_cancelled_booking_bus_report_b2b($op = '') {
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

        $bus_booking_data = $this->bus_model->b2b_bus_report($condition, false, 0, 2000);
         //Maximum 500 Data Can be exported at time
        $bus_booking_data = $this->booking_data_formatter->format_bus_booking_data($bus_booking_data,'b2b');
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
           

           	$export_data[$k]['agent_net_fare'] = roundoff_number($v['agent_buying_price'] - $v['gst']);
            $export_data[$k]['commission'] = $v['agent_commission'];
            $export_data[$k]['tds'] = $v['agent_tds'];
            $export_data[$k]['agent_markup'] = $v['agent_markup'];
            $export_data[$k]['gst'] = $v['gst'];
            $export_data[$k]['total_fare'] = $v['grand_total'];
            $export_data[$k]['opening_balance'] = $v['opening_balance'];
            $export_data[$k]['closing_balance'] = $v['closing_balance'];
            $export_data[$k]['travel_date'] = app_friendly_absolute_date($v['journey_datetime']);
            $export_data[$k]['booked_on'] = $v['booked_date'];
			$export_data[$k]['con_amt'] = $v['convinence_amount'];
			$export_data[$k]['payment_gateway'] = $v['booking_billing_type'];
			$export_data[$k]['payment_mode'] = $v['payment_mode'];
           	$export_data[$k]['status'] = $v['status'];
           	        		
           	
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
                    'j1' => 'Agent Net Fare',
                    'k1' => 'Commision',
                    'l1' => 'TDS',
                    'm1' => 'Agent Markup',
					'n1' => 'GST',
                    'o1' => 'Total Fare',
                    'p1' => 'opening_balance',
                    'q1' => 'closing_balance',
                    'r1' => 'travel_date',
                    's1' => 'booked_on',
                    't1' => 'Convenience',
					'u1' => 'Payment Gateway',
					'v1' => 'Payment Mode',
                    'w1' => 'Booking Status',
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
                    'j' => 'agent_net_fare',
                    'k' => 'commission',
                    'l' => 'tds',
                    'm' => 'agent_markup',
                    'n' => 'gst',
                    'o' => 'total_fare',
                    'p' => 'opening_balance',
                    'q' => 'closing_balance',
                    'r' => 'travel_date',
                    's' => 'booked_on',
					't' => 'con_amt',
					'u' => 'payment_gateway',
					'v' => 'payment_mode',
                  	'w' => 'status',
                                        
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
	function daily_sales_report()
	{
		$get_data = $this->input->get();
		$today = 1; //Just to let the data fetching function know the date range
		$page_data = array();
		$date = date("Y-m-d");
		$from_date = $date." 00:00:00";
		$to_date = $date." 23:59:59";
		if(valid_array($get_data))
		{
			$today = 0;
			$date1 = $get_data["created_datetime_from"]." 00:00:00";
			$date2 = $get_data["created_datetime_to"]." 23:59:00";
			$from_date = DateTime::createFromFormat('d-m-Y H:i:s', $date1)->format('Y-m-d h:i:s');
			$to_date = DateTime::createFromFormat('d-m-Y H:i:s', $date2)->format('Y-m-d h:i:s');
		}
		$from_date = $this->db->escape($from_date);
		$to_date = $this->db->escape($to_date);
		
		$flight_dsr = $this->flight_model->daily_sales_report($from_date, $to_date);
		$bus_dsr = $this->bus_model->daily_sales_report($from_date, $to_date);
		//echo $this->bus_model->db->last_query(); exit;
		$hotel_dsr = $this->hotel_model->daily_sales_report($from_date, $to_date);
		
		$page_data["fdsr"] = $flight_dsr[0];
		$page_data["bdsr"] = $bus_dsr[0];
		$page_data["hdsr"] = $hotel_dsr[0];
		
		$page_data["fbooked"]  = $this->flight_model->booking_count($from_date, $to_date, BOOKING_CONFIRMED);
		$page_data["bbooked"]  = $this->bus_model->booking_count($from_date, $to_date, BOOKING_CONFIRMED);
		$page_data["hbooked"]  = $this->hotel_model->booking_count($from_date, $to_date, BOOKING_CONFIRMED);
		
		$page_data["fcancelled"]  = $this->flight_model->booking_count($from_date, $to_date, BOOKING_CANCELLED);
		$page_data["bcancelled"]  = $this->bus_model->booking_count($from_date, $to_date, BOOKING_CANCELLED);
		$page_data["hcancelled"]  = $this->hotel_model->booking_count($from_date, $to_date, BOOKING_CANCELLED);
		
		$page_data["search_params"] = @$get_data;
		$this->template->view("report/daily_sales_report", $page_data);
	}
	function package_enquiry_report($offset=0)
	{
		//ini_set('display_errors', 1);
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
	
	function custom_enquiry_report(){
		
		$page_data['table_data'] = $this->custom_db->single_table_records('custom_package_enquiry','*',array('agent_id'=>$this->entity_user_id))['data']; 
		$country_list=$this->Package_Model->tours_country_name();
		$city_list=$this->Package_Model->tours_city_name();
		
		foreach($page_data['table_data'] as $enq_key =>$enq_val){
			$page_data['table_data'][$enq_key]['city'] = $city_list[$enq_val['departure_city']]; 
			$country_array=explode(',',$enq_val['destination']);
			$page_data['table_data'][$enq_key]['country_name']=''; 
			$page_data['table_data'][$enq_key]['agent_details'] = $this->custom_db->single_table_records('user','agency_name,phone,user_id',array('user_id'=>$enq_val['agent_id']))['data'][0]; 
			foreach($country_array as $c_arr){
				$page_data['table_data'][$enq_key]['country_name'].=$country_list[$c_arr].' ,';
			}
			
		}
		
		//debug($page_data);exit;
		$page_data['total_rows']=count($page_data['table_data']);
		$this->template->view('report/custom_enquiry_report', $page_data);
	}
}
