<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Provab - vibrant holidays
 * @subpackage Client
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V1
 */
class Menu extends CI_Controller {
	private $current_module;
	public function __construct()
	{
		parent::__construct();
		$this->load->model('hotel_model');
		$this->load->model('flight_model');
		$this->load->model('bus_model');
		$this->load->model('sightseeing_model');
		$this->load->model('car_model');
		$this->load->model('user_model');
		$this->load->model('transaction_model');
		$this->load->model('transferv1_model');	
		$this->load->library('booking_data_formatter');
		$this->load->model('custom_db');
		$this->current_module = $this->config->item('current_module');
		$this->load->library('api_balance_manager');
		$this->load->library('Application_Logger');
		$this->load->model('domain_management_model');
		$this->load->library('session');
		//$this->output->enable_profiler(TRUE);
	}
	/**
	* Managing Api Balance
	**/
	function api_balances($for_dash = 0)
	{

		/*if($_SERVER['HTTP_X_FORWARDED_FOR']=="157.51.136.210")
		        {

		        }*/
		
		$data["suppliers"] = $this->domain_management_model->get_all_suplliers(0, 1);

		//$bitla = $this->api_balance_manager->getBitlaBalance();

		$data["balance_details"][BITLA_BUS_BOOKING_SOURCE]["balance"] = $bitla["result"]["balance_amount"];
		$data["balance_details"][BITLA_BUS_BOOKING_SOURCE]["credit_limit"] = $bitla["result"]["credit_limit"];
		$data["balance_details"][BITLA_BUS_BOOKING_SOURCE]["minimum_balance"] = 0;
		$data["balance_details"][BITLA_BUS_BOOKING_SOURCE]["due_amount"] = 0;
        # Please enable when its required #BALU
		//$ets = $this->api_balance_manager->getEtsBalance();

		$data["balance_details"][ETS_BUS_BOOKING_SOURCE]["balance"] = $ets["balanceAmount"];
		$data["balance_details"][ETS_BUS_BOOKING_SOURCE]["credit_limit"] = 0;
		$data["balance_details"][ETS_BUS_BOOKING_SOURCE]["minimum_balance"] = $ets["lowBalanceAmount"];
		$data["balance_details"][ETS_BUS_BOOKING_SOURCE]["due_amount"] = 0;
        # Please enable when its required #BALU
		//$tbo = $this->api_balance_manager->getTboBalance();
		//debug($tbo); exit; 
		$data["balance_details"][PROVAB_FLIGHT_BOOKING_SOURCE]["balance"] = $tbo["CashBalance"];
		$data["balance_details"][PROVAB_FLIGHT_BOOKING_SOURCE]["credit_limit"] = $tbo["CreditBalance"];
		$data["balance_details"][PROVAB_FLIGHT_BOOKING_SOURCE]["minimum_balance"] = 0;
		$data["balance_details"][PROVAB_FLIGHT_BOOKING_SOURCE]["due_amount"] = 0;
		
		foreach($data["suppliers"] AS $key => $supp){
			if($supp["travel_id"] > 0){




				$direct_api = $this->api_balance_manager->getBitlaDOBalance($supp["travel_id"]);
				$data["balance_details"][$supp["source_id"]]["balance"] = 0; //$direct_api["result"]["balance_amount"];
				$data["balance_details"][$supp["source_id"]]["credit_limit"] = 0; //$direct_api$bitla["result"]["credit_limit"]
				$data["balance_details"][$supp["source_id"]]["minimum_balance"] = 0;
				$data["balance_details"][$supp["source_id"]]["due_amount"] = 0;
			}
			//if($_SERVER['HTTP_X_FORWARDED_FOR']=="157.51.125.165")
		      //  {

		        	if($supp['balance']!=0) {
				  if($supp['balance']<=$supp['minimum_balance'])
				  {

                    
                   $this->application_logger->check_api_minimum_balance($this->entity_name, $this->entity_user_id, array('Balance' => $supp['balance'], 'minimum_balance' =>  $supp['minimum_balance']), $supp);
				  }
				 }

		     	//}
		}
		
		
		if($for_dash)
			return $this->template->isolated_view("private_management/api_balances", $data);
		else
			$this->template->view("private_management/api_balances", $data);
	}
	function fresh()
	{
		exit("new");
	}
	/**
	 * index page of application will be loaded here
	 */
	function index()
	{
		if (web_page_access_privilege('p1')) {
			if (is_active_bus_module()) {
				$active_bus = true;
			}
			if (is_active_hotel_module()) {
				$active_hotel = true;
			}
			if (is_active_airline_module()) {
				$active_airline = true;
			}
			if(is_active_sightseeing_module()){
				$active_sightseeing = true;
			}
			if(is_active_car_module()){
				$active_car = true;
			}
			if(is_active_transferv1_module()){
				$active_transfers = true;
			}			
			$this->load->library('booking_data_formatter');
			$days_duration = -1; // ADD day count to filter result
			$condition = array();
			$condition[] = array('BD.status', ' IN ', '("BOOKING_CONFIRMED","BOOKING_PENDING")');
			if ($days_duration > 0) {
				$condition = array(
				array('BD.created_datetime', '>=', $this->db->escape(date('Y-m-d', strtotime(subtract_days_from_date($days_duration)))))
				
				);
			}
			//load for current year only
			$time_line_interval = get_month_names();
			$hotel_earning = $flight_earning = $bus_earning = $sightseeing_earning = $transfers_earning= array();
			$tha = array("total_price" => 0, "agent_markup" => 0, "agent_total" => 0);
			if (!empty($active_hotel)) {
				$module_total_earning[0]['name'] = 'Hotel';
				$module_total_earning[0]['y'] = 0;
				$time_line_report[0]['name'] = 'Hotel';
				$time_line_report[0]['data'] = array();
				$time_line_report[0]['color'] = '#00a65a';
				$tmp_hotel_booking = $this->hotel_model->get_monthly_booking_summary();
				$month_index_hotel = index_month_number($tmp_hotel_booking);

				//Daily Flight purchase amount snippet
				$dly_br_cnd[0] = array('BD.created_datetime', '>=', $this->db->escape(db_current_datetime(date("Y-m-d 00:00:00"))));
				$dly_br_cnd[1] = array('BD.created_datetime', '<=', $this->db->escape(db_current_datetime(date("Y-m-d H:i:s"))));
				$dly_br_cnd[2] = array('BD.status', '=', BOOKING_CONFIRMED);

				//Today flight bookings ($tfbs)
				$tbs = $this->hotel_model->b2b_hotel_report($dly_br_cnd);
				$ftbs = $this->booking_data_formatter->format_hotel_booking_data($tbs, 'b2b')["data"]["booking_details"];

				//Today total flight purchase amount
				$total_price = 0; $agent_markup = 0; $agent_total = 0;
				foreach ($ftbs as $ftb) {
				    $total_price += $ftb["grand_total"];
				    $agent_markup += $ftb["agent_markup"];
				    $agent_total += $ftb["agent_buying_price"];
				}
				$tha["total_price"] = $total_price;
				$tha["agent_markup"] = $agent_markup;
				$tha["agent_total"] = $agent_total;
			}
			$tfa = array("total_price" => 0, "agent_markup" => 0, "agent_total" => 0);
			if (!empty($active_airline)) {
				$module_total_earning[1]['name'] = 'Flight';
				$module_total_earning[1]['y'] = 0;
				$time_line_report[1]['name'] = 'Flight';
				$time_line_report[1]['data'] = array();
				$time_line_report[1]['color'] = '#0073b7';
				$tmp_flight_booking = $this->flight_model->get_monthly_booking_summary();
				$month_index_flight = index_month_number($tmp_flight_booking);

				//Daily Flight purchase amount snippet
				$dly_br_cnd[0] = array('BD.created_datetime', '>=', $this->db->escape(db_current_datetime(date("Y-m-d 00:00:00"))));
				$dly_br_cnd[1] = array('BD.created_datetime', '<=', $this->db->escape(db_current_datetime(date("Y-m-d H:i:s"))));
				$dly_br_cnd[2] = array('BD.status', '=', BOOKING_CONFIRMED);

				//Today flight bookings ($tfbs)
				$tbs = $this->flight_model->b2b_flight_report($dly_br_cnd);
				$ftbs = $this->booking_data_formatter->format_flight_booking_data($tbs, 'b2b')["data"]["booking_details"];

				//Today total flight purchase amount
				$total_price = 0; $agent_markup = 0; $agent_total = 0;
				foreach ($ftbs as $ftb) {
				    $total_price += $ftb["grand_total"];
				    $agent_markup += $ftb["agent_markup"];
				    $agent_total += $ftb["agent_buying_price"];
				}
				$tfa["total_price"] = $total_price;
				$tfa["agent_markup"] = $agent_markup;
				$tfa["agent_total"] = $agent_total;
			}
			$tba = array("total_price" => 0, "agent_markup" => 0, "agent_total" => 0);
			if (!empty($active_bus)) {
				$module_total_earning[2]['bus'] = 'Bus';
				$module_total_earning[2]['y'] = 0;
				$time_line_report[2]['name'] = 'Bus';
				$time_line_report[2]['data'] = array();
				$time_line_report[2]['color'] = '#dd4b39';
				$tmp_bus_booking = $this->bus_model->get_monthly_booking_summary();
				$month_index_bus = index_month_number($tmp_bus_booking);

				//Daily bus purchase amount snippet
				$dly_br_cnd[0] = array('BD.created_datetime', '>=', $this->db->escape(db_current_datetime(date("Y-m-d 00:00:00"))));
				$dly_br_cnd[1] = array('BD.created_datetime', '<=', $this->db->escape(db_current_datetime(date("Y-m-d H:i:s"))));
				$dly_br_cnd[2] = array('BD.status', '=', BOOKING_CONFIRMED);

				//Today bus bookings ($tbs)
				$tbs = $this->bus_model->b2b_bus_report($dly_br_cnd);
				$ftbs = $this->booking_data_formatter->format_bus_booking_data($tbs, 'b2b')["data"]["booking_details"];

				//Today total bus purchase amount
				$total_price = 0; $agent_markup = 0; $agent_total = 0;
				foreach ($ftbs as $ftb) {
				    $total_price += $ftb["grand_total"];
				    $agent_markup += $ftb["agent_markup"];
				    $agent_total += $ftb["agent_buying_price"];
				}
				$tba["total_price"] = $total_price;
				$tba["agent_markup"] = $agent_markup;
				$tba["agent_total"] = $agent_total;
			}
			$taa = array("total_price" => 0, "agent_markup" => 0, "agent_total" => 0);
			if (!empty($active_sightseeing)) {
				$module_total_earning[3]['sightseeing'] = 'Activities';
				$module_total_earning[3]['y'] = 0;
				$time_line_report[3]['name'] = 'Activities';
				$time_line_report[3]['data'] = array();
				$time_line_report[3]['color'] = '#ff9800';
				$tmp_sightseeing_booking = $this->sightseeing_model->get_monthly_booking_summary();

				$month_index_sightseeing = index_month_number($tmp_sightseeing_booking);
				//Daily bus purchase amount snippet
				$dly_br_cnd[0] = array('BD.created_datetime', '>=', $this->db->escape(db_current_datetime(date("Y-m-d 00:00:00"))));
				$dly_br_cnd[1] = array('BD.created_datetime', '<=', $this->db->escape(db_current_datetime(date("Y-m-d H:i:s"))));
				$dly_br_cnd[2] = array('BD.status', '=', BOOKING_CONFIRMED);

				//Today activity bookings ($tbs)
				$tbs = $this->sightseeing_model->b2b_sightseeing_report($dly_br_cnd);
				$ftbs = $this->booking_data_formatter->format_sightseeing_booking_data($tbs, 'b2b')["data"]["booking_details"];

				//Today total bus purchase amount
				$total_price = 0; $agent_markup = 0; $agent_total = 0;
				foreach ($ftbs as $ftb) {
				    $total_price += $ftb["grand_total"];
				    $agent_markup += $ftb["agent_markup"];
				    $agent_total += $ftb["agent_buying_price"];
				}
				$taa["total_price"] = $total_price;
				$taa["agent_markup"] = $agent_markup;
				$taa["agent_total"] = $agent_total;
			}
			$tca = array("total_price" => 0, "agent_markup" => 0, "agent_total" => 0);
			if (!empty($active_car)) {
				$module_total_earning[4]['car'] = 'Car';
				$module_total_earning[4]['y'] = 0;
				$time_line_report[4]['name'] = 'Car';
				$time_line_report[4]['data'] = array();
				$time_line_report[4]['color'] = '#dd4b39';
				$tmp_car_booking = $this->car_model->get_monthly_booking_summary();
				$month_index_car = index_month_number($tmp_bus_booking);

				//Daily bus purchase amount snippet
				$dly_br_cnd[0] = array('BD.created_datetime', '>=', $this->db->escape(db_current_datetime(date("Y-m-d 00:00:00"))));
				$dly_br_cnd[1] = array('BD.created_datetime', '<=', $this->db->escape(db_current_datetime(date("Y-m-d H:i:s"))));
				$dly_br_cnd[2] = array('BD.status', '=', BOOKING_CONFIRMED);

				//Today activity bookings ($tbs)
				$tbs = $this->car_model->b2b_car_report($dly_br_cnd);
				$ftbs = $this->booking_data_formatter->format_car_booking_data($tbs, 'b2b')["data"]["booking_details"];

				//Today total car purchase amount
				$total_price = 0; $agent_markup = 0; $agent_total = 0;
				foreach ($ftbs as $ftb) {
				    $total_price += $ftb["grand_total"];
				    $agent_markup += $ftb["agent_markup"];
				    $agent_total += $ftb["agent_buying_price"];
				}
				$tca["total_price"] = $total_price;
				$tca["agent_markup"] = $agent_markup;
				$tca["agent_total"] = $agent_total;
			}
			$tta = array("total_price" => 0, "agent_markup" => 0, "agent_total" => 0);
			if (!empty($active_transfers)) {
				$module_total_earning[5]['transfers'] = 'Transfers';
				$module_total_earning[5]['y'] = 0;
				$time_line_report[5]['name'] = 'Transfers';
				$time_line_report[5]['data'] = array();
				$time_line_report[5]['color'] = '#456F13';
				$tmp_transfers_booking = $this->transferv1_model->get_monthly_booking_summary();
				//debug($tmp_transfers_booking);

				$month_index_transfers = index_month_number($tmp_transfers_booking);

				//Daily bus purchase amount snippet
				$dly_br_cnd[0] = array('BD.created_datetime', '>=', $this->db->escape(db_current_datetime(date("Y-m-d 00:00:00"))));
				$dly_br_cnd[1] = array('BD.created_datetime', '<=', $this->db->escape(db_current_datetime(date("Y-m-d H:i:s"))));
				$dly_br_cnd[2] = array('BD.status', '=', BOOKING_CONFIRMED);

				//Today activity bookings ($tbs)
				$tbs = $this->transferv1_model->b2b_transferv1_report($dly_br_cnd);
				$ftbs = $this->booking_data_formatter->format_transferv1_booking_data($tbs, 'b2b')["data"]["booking_details"];

				//Today total car purchase amount
				$total_price = 0; $agent_markup = 0; $agent_total = 0;
				foreach ($ftbs as $ftb) {
				    $total_price += $ftb["grand_total"];
				    $agent_markup += $ftb["agent_markup"];
				    $agent_total += $ftb["agent_buying_price"];
				}
				$tta["total_price"] = $total_price;
				$tta["agent_markup"] = $agent_markup;
				$tta["agent_total"] = $agent_total;
			}

			$time_line_report_average = array();
			$monthly_hotel_booking = array();
			$monthly_flight_booking = array();
			$monthly_bus_booking = array();
			$monthly_sightseeing_booking = array();
			$monthly_car_booking = array();
			$monthly_transfers_booking = array();

			foreach ($time_line_interval as $k => $v) {
				if (!empty($active_hotel)) {
					if (isset($month_index_hotel[$k])) {
						//HOTEL
						$monthly_hotel_booking[$k] = intval($month_index_hotel[$k]['total_booking']);
						$hotel_earning[$k] = round($month_index_hotel[$k]['monthly_earning']);
					} else {
						$monthly_hotel_booking[$k] = 0;
						$hotel_earning[$k] = 0;
					}
					@($time_line_report_average[$k] += round($hotel_earning[$k]))/(intval($monthly_hotel_booking[$k]) > 0 ? $monthly_hotel_booking[$k] : 1);
					($module_total_earning[0]['y'] += round($hotel_earning[$k]));
				}
				if (!empty($active_airline)) {
					if (isset($month_index_flight[$k])) {
						//FLIGHT
						$monthly_flight_booking[$k] = intval($month_index_flight[$k]['total_booking']);
						$flight_earning[$k] = round($month_index_flight[$k]['monthly_earning']);
					} else {
						$monthly_flight_booking[$k] = 0;
						$flight_earning[$k] = 0;
					}
					@($time_line_report_average[$k] += round($flight_earning[$k]))/(intval($monthly_flight_booking[$k]) > 0 ? $monthly_flight_booking[$k] : 1);
					($module_total_earning[1]['y'] += round($flight_earning[$k]));
				}
				if (!empty($active_bus)) {
					if (isset($month_index_bus[$k])) {
						//BUS
						$monthly_bus_booking[$k] = intval($month_index_bus[$k]['total_booking']);
						$bus_earning[$k] = round($month_index_bus[$k]['monthly_earning']);
					} else {
						$monthly_bus_booking[$k] = 0;
						$bus_earning[$k] = 0;
					}
					@($time_line_report_average[$k] += round($bus_earning[$k]))/(intval($monthly_bus_booking[$k]) > 0 ? $monthly_bus_booking[$k] : 1);
					($module_total_earning[2]['y'] += round($bus_earning[$k]));
				}
				if (!empty($active_sightseeing)) {

					if (isset($month_index_sightseeing[$k])) {
						//Sightseeing
						$monthly_sightseeing_booking[$k] = intval($month_index_sightseeing[$k]['total_booking']);
						$sightseeing_earning[$k] = round($month_index_sightseeing[$k]['monthly_earning']);
					} else {
						$monthly_sightseeing_booking[$k] = 0;
						$sightseeing_earning[$k] = 0;
					}
					@($time_line_report_average[$k] += round($sightseeing_earning[$k]))/(intval($monthly_sightseeing_booking[$k]) > 0 ? $monthly_sightseeing_booking[$k] : 1);
					($module_total_earning[3]['y'] += round($sightseeing_earning[$k]));
				}

				if (!empty($active_transfers)) {

					if (isset($month_index_transfers[$k])) {
						//Transfers
						$monthly_transfers_booking[$k] = intval($month_index_transfers[$k]['total_booking']);
						$transfers_earning[$k] = round($month_index_transfers[$k]['monthly_earning']);
					} else {
						$monthly_transfers_booking[$k] = 0;
						$transfers_earning[$k] = 0;
					}
					@($time_line_report_average[$k] += round($transfers_earning[$k]))/(intval($monthly_transfers_booking[$k]) > 0 ? $monthly_transfers_booking[$k] : 1);
					($module_total_earning[5]['y'] += round($transfers_earning[$k]));
				}


			}
			// debug($module_total_earning);
			// exit;
			if (!empty($active_car)) {
					if (isset($month_index_car[$k])) {
						//BUS
						$monthly_car_booking[$k] = intval($month_index_car[$k]['total_booking']);
						$car_earning[$k] = round($month_index_car[$k]['monthly_earning']);
					} else {
						$monthly_car_booking[$k] = 0;
						$car_earning[$k] = 0;
					}
					@($time_line_report_average[$k] += round($car_earning[$k]))/(intval($monthly_car_booking[$k]) > 0 ? $monthly_car_booking[$k] : 1);
					($module_total_earning[4]['y'] += round($car_earning[$k]));
				}
			if (!empty($active_hotel)) {
				$time_line_report[0]['data'] = $monthly_hotel_booking;
				$module_total_earning[0]['color'] = $time_line_report[0]['color'];
				$group_time_line_report[] = array('type' => 'column', 'name' => 'Hotel', 'data' => $hotel_earning, 'color' => $time_line_report[0]['color']);
			}
			if (!empty($active_airline)) {
				$time_line_report[1]['data'] = $monthly_flight_booking;
				$module_total_earning[1]['color'] = $time_line_report[1]['color'];
				$group_time_line_report[] = array('type' => 'column','name' => 'Flight', 'data' => $flight_earning, 'color' => $time_line_report[1]['color']);
			}
			if (!empty($active_bus)) {
				$time_line_report[2]['data'] = $monthly_bus_booking;
				$module_total_earning[2]['color'] = $time_line_report[2]['color'];
				$group_time_line_report[] = array('type' => 'column','name' => 'Bus', 'data' => $bus_earning, 'color' => $time_line_report[2]['color']);
			}
			if (!empty($active_sightseeing)) {
				$time_line_report[3]['data'] = $monthly_sightseeing_booking;
				$module_total_earning[3]['color'] = $time_line_report[3]['color'];
				$group_time_line_report[] = array('type' => 'column','name' => 'Activities', 'data' => $sightseeing_earning, 'color' => $time_line_report[3]['color']);
			}
			if (!empty($active_transfers)) {
				$time_line_report[5]['data'] = $monthly_transfers_booking;
				$module_total_earning[5]['color'] = $time_line_report[5]['color'];
				$group_time_line_report[] = array('type' => 'column','name' => 'Transfers', 'data' => $transfers_earning, 'color' => $time_line_report[5]['color']);
			}

			if (!empty($active_car)) {
				$time_line_report[4]['data'] = $monthly_car_booking;
				$module_total_earning[4]['color'] = $time_line_report[4]['color'];
				$group_time_line_report[] = array('type' => 'column','name' => 'Car', 'data' => $bus_earning, 'color' => $time_line_report[4]['color']);
			}
		
			$max_count = max(array_merge($monthly_hotel_booking, $monthly_flight_booking, $monthly_bus_booking,$monthly_sightseeing_booking, $monthly_car_booking,$monthly_transfers_booking));
			foreach ($time_line_report_average as $k => $v) {
				if ($v > 0) {
					$time_line_report_average[$k] = round($v/3);
				}
			}
			$group_time_line_report[] = array(
				'type' => 'spline', 'name' => 'Average', 'data' => $time_line_report_average,
				'marker' => array('lineColor' => '#e65100', 'color' => '#ff5722', 'lineWidth' => 2, 'fillColor' => '#FFF')
			);


			/*debug($group_time_line_report);exit;*/
			$page_data = array('group_time_line_report' => $group_time_line_report,
			'module_total_earning' => $module_total_earning,
			'time_line_interval' => $time_line_interval,
			'max_count' => $max_count, 'time_line_report' => $time_line_report, 'time_line_report_average' => $time_line_report_average);

			
			if (!empty($active_hotel)) {
				$page_data['hotel_booking_count'] = $this->hotel_model->booking($condition, true);
			}
			if (!empty($active_airline)) {
				$page_data['flight_booking_count'] = $this->flight_model->booking($condition, true);
			}
			if (!empty($active_bus)) {
				$page_data['bus_booking_count'] = $this->bus_model->booking($condition, true);
			}

			if (!empty($active_sightseeing)) {
				$page_data['sightseeing_booking_count'] = $this->sightseeing_model->booking($condition, true);
			}
			if (!empty($active_transfers)) {
				$page_data['transfers_booking_count'] = $this->transferv1_model->booking($condition, true);
			}

			if (!empty($active_car)) {
				$page_data['car_booking_count'] = $this->car_model->booking($condition, true);
			}
			$condition = array();
			$latest_transaction = $this->transaction_model->logs($condition, false, 0, 5);
			$latest_transaction = $this->booking_data_formatter->format_recent_transactions($latest_transaction, 'b2c');
			$page_data['latest_transaction'] = $latest_transaction['data']['transaction_details'];

			$page_data['total_online_user'] = $this->user_model->get_logged_in_users(array(array('U.user_id', '!=', intval($this->entity_user_id))), true);
			$page_data['latest_user'] = $this->user_model->get_domain_user_list(array(), false, 0, 12);

			//Cacellation report
			$flight_canc = $this->flight_model->cancellation_report('dashboard');
			$bus_canc = $this->bus_model->cancellation_report('dashboard');
			
			$cancel_tickets = array(
					'flight_cancel' => $flight_canc['data'][0]['cancel_tickets'],
					'bus_cancel' => $bus_canc['data'][0]['cancel_tickets']
				);


			$page_data['tfa'] = $tfa;
			$page_data['tba'] = $tba;
			$page_data['taa'] = $taa;
			$page_data['tha'] = $tha;
			$page_data['tca'] = $tca;
			$page_data['tta'] = $tta;
			$page_data['cancel_report'] = $cancel_tickets;

			///custom menu search
			//$page_data['custom_menu_search'] = $this->custom_db->single_table_records ('custom_menu_search');

			$this->template->view('menu/dashboard', $page_data);
		}
	}


	public function bi_reports(){

		//die('here is bi report');
		$page_data = array();

		

		$month_compare_data = $this->flight_model->bi_get_flight_details_monthly();
		$daily_data = $this->flight_model->bi_get_today_booking_details();

		$agent_data = $this->flight_model->bi_get_agent_details();
		$mod_data = $this->flight_model->bi_get_active_mod();

		$latest_transaction = $this->transaction_model->logs($condition, false, 0, 5);
		$latest_transaction = $this->booking_data_formatter->format_recent_transactions($latest_transaction, 'b2c');
		$page_data['latest_transaction'] = $latest_transaction['data']['transaction_details'];

		$deposit_data = $this->flight_model->bi_deposit_report();
		//debug($deposit_data);die();

		$today_transaction = $this->flight_model->bi_transaction_details();
		//debug($today_transaction);die();

		///flight transaction
		$admin_purchase_f = 0;
		$admin_sales_f = 0;
		foreach ($today_transaction['flight'] as $key => $value) {
			//debug($value); exit;
			$fare = json_decode($value['attributes'],1);
			$admin_purchase_f += ($value['total_fare']-$value['admin_commission']+$value['admin_tds']);
			$admin_sales_f += ($value['total_fare']+$value['admin_markup']+$value['gst']-$value['agent_commission']+$value['agent_tds']);
		}

		$page_data['flight_transc'] = array(
				'admin_purchase' => $admin_purchase_f,
				'admin_sales' => $admin_sales_f,
				'admin_profit' => ($admin_sales_f - $admin_purchase_f),
			);

		///bus transaction
		$admin_purchase_b = 0;
		$admin_sales_b = 0;
		foreach ($today_transaction['bus'] as $key => $value) {
			$fare = json_decode($value['attr'],1);
			$admin_purchase_b += ($value['fare']-$value['admin_commission']+$value['admin_tds']);
			$admin_sales_b += ($value['fare']+$value['admin_markup']+$fare['_GST']-$value['agent_commission']+$value['agent_tds']);
		}

		$page_data['bus_transc'] = array(
				'admin_purchase' => $admin_purchase_b,
				'admin_sales' => $admin_sales_b,
				'admin_profit' => ($admin_sales_b - $admin_purchase_b),
			);
		

		///Hotel transaction
		$admin_purchase_h = 0;
		$admin_sales_h = 0;
		foreach ($today_transaction['hotel'] as $key => $value) {
			$admin_purchase_h += $value['total_fare'];
			$admin_sales_h += $value['total_fare']+$fare['admin_markup'];
		}
		$page_data['hotel_transc'] = array(
				'admin_purchase' => $admin_purchase_h,
				'admin_sales' => $admin_sales_h,
				'admin_profit' => ($admin_sales_h - $admin_purchase_h),
			);

		//Transfer transaction
		$admin_purchase_t = 0;
		$admin_sales_t = 0;
		foreach ($today_transaction['transfer'] as $key => $value) {
			$admin_purchase_t += $value['api_raw_fare'];
			$admin_sales_t += $value['agent_buying_price'];
		}
		$page_data['transfer_transc'] = array(
				'admin_purchase' => $admin_purchase_t,
				'admin_sales' => $admin_sales_t,
				'admin_profit' => ($admin_sales_t - $admin_purchase_t),
			);

		//Activities transaction
		$admin_purchase_a = 0;
		$admin_sales_a = 0;
		foreach ($today_transaction['activities'] as $key => $value) {
			$admin_purchase_a += $value['api_raw_fare'];
			$admin_sales_a += $value['agent_buying_price'];
		}
		$page_data['activities_transc'] = array(
				'admin_purchase' => $admin_purchase_a,
				'admin_sales' => $admin_sales_a,
				'admin_profit' => ($admin_sales_a - $admin_purchase_a),
			);

		
		$refunds = $this->flight_model->bi_refund_details();
		//debug($refunds);die();


		$page_data['total_purchase'] = ($admin_purchase_f + $admin_purchase_h + $admin_purchase_b + $admin_purchase_t + $admin_purchase_a);
		$page_data['total_sales'] = ($admin_sales_f + $admin_sales_h + $admin_sales_b + $admin_sales_t + $admin_sales_a);
		$page_data['total_profit'] = ($page_data['total_sales'] - $page_data['total_purchase']);

		$page_data['total_refund'] = ($refunds['flight'][0]['refund_amount'] + $refunds['bus'][0]['refund_amount'] + $refunds['hotel'][0]['refund_amount'] + $refunds['transfer'][0]['refund_amount'] + $refunds['activities'][0]['refund_amount']);

		//debug($page_data);die('+++');
		$page_data['deposit_data'] = $deposit_data;
		$page_data['daily_data'] = $daily_data;
		$page_data['month_compare'] = $month_compare_data;
		$active_agent = array();
		$lock_agent = array();
		$inactive_agent = array();
		$new_agent = array();
		$from_date = date('Y-m-01 00:00:00');
		foreach ($agent_data as $a_key => $a_value) {
			if($a_value['status'] == 0){ //inactive
				$inactive_agent[] = $a_value;
			}
			if($a_value['status'] == 1){ //active
				$active_agent[] = $a_value; 
			}
			if($a_value['status'] == 2){ //lock
				$lock_agent[] = $a_value;
			}
			
			if($a_value['created_datetime'] > $from_date){ //new agent
				$new_agent[] = $a_value;
			}
		}
		$agent_data1['inactive_agent'] = $inactive_agent;
		$agent_data1['active_agent'] = $active_agent;
		$agent_data1['lock_agent'] = $lock_agent;
		$agent_data1['new_agent'] = $new_agent;
		$page_data['agent_data'] = $agent_data1;
		$recent_report = $this->transaction_model->recent_report();
		$page_data['recent_report'] = $recent_report;;
		$page_data['monthly_agent_count'] = $this->transaction_model->monthly_active_agent_count();
		$page_data['active_agent_count'] = $this->transaction_model->active_agent_count();
		$page_data['active_model']=$mod_data;

		$page_data['api_balances'] = $this->api_balances(1);

		$page_data['notification_count'] = $this->flight_model->notification_count();
		$this->session->set_userdata('notification_count',$page_data['notification_count'][0]['count']);
		//******************************cancellation notification*****************************************
		$page_data['cancel_queue_count'] = $this->flight_model->flight_cancel_notification_count();
		$this->session->set_userdata('cancel_queue_count',$page_data['cancel_queue_count']['cancel_queue_count']);
		//******************************group booking notification*****************************************
		$page_data['group_booking_count'] = $this->flight_model->flight_group_booking_count_notification_count();
		$this->session->set_userdata('group_booking_count',$page_data['group_booking_count']['group_booking_count']);
		//exit("Bi Here");
        
		$this->template->view('menu/bi_reports', $page_data);
	}

	function bi_reports_graph(){
		//die('here is bi report');
		$page_data = array();

		//$fliter = '';
		$tp ='';
		$s_date = '';
		$e_date = '';
		if(isset($_GET['time_period'])){
			$tp = $_GET['time_period'];	
		}
		if(isset($_GET['s_date'])){
			$s_date = $_GET['s_date'];	
		}
		if(isset($_GET['e_date'])){
			$e_date = $_GET['e_date'];
		}
		

		//debug($fliter);die();
		
		$week_data = $this->flight_model->bi_get_graph_details($tp,$s_date,$e_date);

		//debug($week_data);die('===');

		
		$days = array();
		
		//debug();die();
		if($tp == '' || $tp == 'week'){
			$timestamp = strtotime('next Sunday');
			for ($i = 0; $i < 7; $i++) {
		    	$days[] = strftime('%A', $timestamp);
		    	$timestamp = strtotime('+1 day', $timestamp);
			}
		}else{
			for ($m=1; $m<=12; $m++) {
		     	$month = date('F', mktime(0,0,0,$m, 1, date('Y')));
		     	$days[] = $month;
	     	}
		}		

		//debug($days);
		$data = array();
		//debug($week_data['flight']);
		
		//flight
		$count_f = array();
		for($i = 0; $i<count($days);$i++){
			$found = 0;
			foreach ($week_data['flight'] as $key => $value) {
				if($days[$i] == $value['Day_Name']){
					$count_f[$i] = abs($value['Count']);
					$found = 1;
				}
			}
			if(!$found)
				$count_f[$i] =  0;
		}
		//hotel
		$count_h = array();
		for($i = 0; $i<count($days);$i++){
			$found = 0;
			foreach ($week_data['hotel'] as $key => $value) {
				if($days[$i] == $value['Day_Name']){
					$count_h[$i] = abs($value['Count']);
					$found = 1;
				}
			}
			if(!$found)
				$count_h[$i] =  0;
		}
		//bus
		$count_b = array();
		for($i = 0; $i<count($days);$i++){
			$found = 0;
			foreach ($week_data['bus'] as $key => $value) {
				if($days[$i] == $value['Day_Name']){
					$count_b[$i] = abs($value['Count']);
					$found = 1;
				}
			}
			if(!$found)
				$count_b[$i] =  0;
		}
		//transfer
		$count_t = array();
		for($i = 0; $i<count($days);$i++){
			$found = 0;
			foreach ($week_data['transfer'] as $key => $value) {
				if($days[$i] == $value['Day_Name']){
					$count_t[$i] = abs($value['Count']);
					$found = 1;
				}
			}
			if(!$found)
				$count_t[$i] =  0;
		}
		//activities
		$count_a = array();
		for($i = 0; $i<count($days);$i++){
			$found = 0;
			foreach ($week_data['activities'] as $key => $value) {
				if($days[$i] == $value['Day_Name']){
					$count_a[$i] = abs($value['Count']);
					$found = 1;
				}
			}
			if(!$found)
				$count_a[$i] =  0;
		}

		$graph_data[] = array(
				'name' => 'Flight',
				'data' => $count_f,
				'color' => '#0073b7'
			);
		$graph_data[] = array(
				'name' => 'Hotel',
				'data' => $count_h,
				'color' => '#00a65a'
			);
		$graph_data[] = array(
				'name' => 'Bus',
				'data' => $count_b,
				'color' => '#dd4b39'
			);
		$graph_data[] = array(
				'name' => 'Transfer',
				'data' => $count_t,
				'color' => '#00c0ef'
			);
		$graph_data[] = array(
				'name' => 'Activities',
				'data' => $count_a,
				'color' => '#ff9800'
			);

		/*debug($graph_data);
		die();*/

		$page_data['days'] = $days;
		$page_data['booking_data'] = $graph_data;

		echo json_encode($page_data);
		
	}
	
}
