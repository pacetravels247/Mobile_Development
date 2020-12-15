<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Provab - Provab Application
 * @subpackage Travel Portal
 * @author     Balu A<balu.provab@gmail.com> on 01-06-2015
 * @version    V2
 */

class Transaction extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('transaction_model');
	}

	/**
	 * Show Transaction Logs to user
	 * @param number $offset
	 */
	function logs($offset=0)
	{
		$get_data = $this->input->get();
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
			$condition[] = array('TL.created_datetime', '>=', $this->db->escape($ymd_from_date));
		}

		if(empty($to_date) == false) {
			$ymd_to_date = date('Y-m-d', strtotime($to_date));
			$condition[] = array('TL.created_datetime', '<=', $this->db->escape($ymd_to_date));
		}

		if (trim(@$get_data['transaction_type']) != '') {
			$condition[] = array('TL.transaction_type', '=', $this->db->escape($get_data['transaction_type']));
		}

		if (trim(@$get_data['app_reference']) != '') {
			$condition[] = array('TL.app_reference', '=', $this->db->escape($get_data['app_reference']));
		}

		$page_data['table_data'] = $this->transaction_model->logs($condition, false, $offset, RECORDS_RANGE_3);
		$total_records = $this->transaction_model->logs($condition, true);
		$this->load->library('pagination');
		$config['base_url'] = base_url().'index.php/transaction/logs/';
		$page_data['total_rows'] = $config['total_rows'] = $total_records;
		$config['per_page'] = RECORDS_RANGE_3;
		$this->pagination->initialize($config);
		/** TABLE PAGINATION */
		$page_data['total_records'] = $config['total_rows'];
		$page_data['search_params'] = $get_data;

		$this->template->view('transaction/logs', $page_data);
	}

	/**
	 *
	 */
	function search_history()
	{
		$active_domain_modules = $this->active_domain_modules;

		/**
		 * Search History - Start
		 */
		$time_line_interval = get_month_names();
		$monthly_series_data = array();
		$page_data['year_start'] = $year_start = date('Y');
		$page_data['year_end'] = $year_end = date('Y', strtotime('+1 year'));
		if (is_active_airline_module()) {
			array_push($monthly_series_data, $this->monthly_flight_search_history_log($year_start, $year_end));
			$page_data['flight_top_search'] = json_encode($this->flight_top_search($year_start, $year_end));
		}
		if (is_active_hotel_module()) {
			array_push($monthly_series_data, $this->monthly_hotel_search_history_log($year_start, $year_end));
			$page_data['hotel_top_search'] = json_encode($this->hotel_top_search($year_start, $year_end));
		}

		if (is_active_bus_module()) {
			array_push($monthly_series_data, $this->monthly_bus_search_history_log($year_start, $year_end));
			$page_data['bus_top_search'] = json_encode($this->bus_top_search($year_start, $year_end));
		}

		$page_data['monthly_time_line_interval'] = json_encode($time_line_interval);
		$page_data['monthly_series_data'] = json_encode($monthly_series_data);
		/**
		 * Search History - End
		 */
		$this->template->view('transaction/search_history', $page_data);
	}

	function top_destinations()
	{
		$active_domain_modules = $this->active_domain_modules;

		/**
		 * Search History - Start
		 */
		$page_data['year_start'] = $year_start = date('Y');
		$page_data['year_end'] = $year_end = date('Y', strtotime('+1 year'));
		if (is_active_airline_module()) {
			$page_data['flight_top_search'] = json_encode($this->flight_top_search($year_start, $year_end));
		}
		if (is_active_hotel_module()) {
			$page_data['hotel_top_search'] = json_encode($this->hotel_top_search($year_start, $year_end));
		}

		if (is_active_bus_module()) {
			$page_data['bus_top_search'] = json_encode($this->bus_top_search($year_start, $year_end));
		}
		/**
		 * Search History - End
		 */
		$this->template->view('transaction/top_destinations', $page_data);
	}



	private function flight_top_search($year_start, $year_end)
	{
		$this->load->model('flight_model');
		$temp_data = $this->flight_model->top_search($year_start, $year_end);
		return $this->group_top_search_data($temp_data);
	}

	private function hotel_top_search($year_start, $year_end)
	{
		$this->load->model('hotel_model');
		$temp_data = $this->hotel_model->top_search($year_start, $year_end);
		return $this->group_top_search_data($temp_data);
	}

	private function bus_top_search($year_start, $year_end)
	{
		$this->load->model('bus_model');
		$temp_data = $this->bus_model->top_search($year_start, $year_end);
		return $this->group_top_search_data($temp_data);
	}

	private function monthly_flight_search_history_log($year_start, $year_end)
	{
		$this->load->model('flight_model');
		$data['name'] = 'Flight';
		$temp_data = $this->flight_model->monthly_search_history($year_start, $year_end);
		$data['data'] = $this->distribute_monthly_values($temp_data);
		$data['color'] = '#0073b7';
		return $data;
	}

	private function monthly_hotel_search_history_log($year_start, $year_end)
	{
		$this->load->model('hotel_model');
		$data['name'] = 'Hotel';
		$temp_data = $this->hotel_model->monthly_search_history($year_start, $year_end);
		$data['data'] = $this->distribute_monthly_values($temp_data);
		$data['color'] = '#00a65a';
		return $data;
	}

	private function monthly_bus_search_history_log($year_start, $year_end)
	{
		$this->load->model('bus_model');
		$data['name'] = 'Bus';
		$temp_data = $this->bus_model->monthly_search_history($year_start, $year_end);
		$data['data'] = $this->distribute_monthly_values($temp_data);
		$data['color'] = '#dd4b39';
		return $data;
	}

	private function distribute_monthly_values($m_fill)
	{
		$m_fill = index_month_number($m_fill);
		$i = 0;
		$data = array();
		for ($i = 1; $i <= 12; $i++) {
			if (isset($m_fill[$i]) == true) {
				$data[] = intval($m_fill[$i]['total_search']);
			} else {
				$data[] = 0;
			}
		}
		return $data;
	}

	private function group_top_search_data($data)
	{
		$result = array();
		if (valid_array($data)) {
			foreach ($data as $k => $v) {
				$result[] = array($v['label'], intval($v['total_search']));
			}
		}
		return $result;
	}
}