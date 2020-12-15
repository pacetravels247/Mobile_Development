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
		$this->load->model('domain_management_model');
	}

	/**
	 * Show Transaction Logs to user
	 * @param number $offset
	 */
	function logs($offset=0)
	{	

		$get_data = $this->input->get();
		$condition = array();
		$extra_tbl = '';
		$extra_cond = '';
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
		if(!empty($get_data["supplier_id"]))
		{
			$sup_tbl_cond = $this->get_booking_table_name_by_supplier_id($get_data["supplier_id"]);
			$extra_tbl = ", booking_source bs, ".$sup_tbl_cond["table_name_n_shortname"];
			$extra_cond = " AND ".$sup_tbl_cond["condition"]." AND bs.source_id = '".$get_data["supplier_id"]."'";
		}
		if (intval(@$get_data['agent_id']) > 0) {
			$condition[] = array('U.user_id', '=', intval($get_data['agent_id']));
		}
		
		if(empty($from_date) == false) {
			$ymd_from_date = date('Y-m-d', strtotime($from_date));
			$condition[] = array('t.created_datetime', '>=', $this->db->escape($ymd_from_date));
		}

		if(empty($to_date) == false) {
			$ymd_to_date = date('Y-m-d', strtotime($to_date));
			$condition[] = array('t.created_datetime', '<=', $this->db->escape($ymd_to_date));
		}

		if (trim(@$get_data['transaction_type']) != '') {
			$condition[] = array('t.transaction_type', '=', $this->db->escape($get_data['transaction_type']));
		}

		if (trim(@$get_data['app_reference']) != '') {
			$condition[] = array('t.app_reference', '=', $this->db->escape($get_data['app_reference']));
		}

		$this->load->library('booking_data_formatter');
		$total_records = $this->transaction_model->logs($condition, true, 0, 100000000000, $extra_tbl, $extra_cond);
		$transaction_details = $this->transaction_model->logs($condition, false, $offset, RECORDS_RANGE_3, $extra_tbl, $extra_cond);

		$transaction_details = $this->booking_data_formatter->format_recent_transactions($transaction_details, 'b2c');

		$page_data['table_data'] = $transaction_details['data']['transaction_details'];
		$this->load->library('pagination');
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['base_url'] = base_url().'index.php/transaction/logs/';
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$page_data['total_rows'] = $config['total_rows'] = $total_records;
		$config['per_page'] = RECORDS_RANGE_3;
		$this->pagination->initialize($config);
		/** TABLE PAGINATION */
		$page_data['total_records'] = $config['total_rows'];

		$page_data['search_params'] = $get_data;

		// get active agent list
		$agent_list['data'] = $this->domain_management_model->agent_list();
		$page_data['agent_list'] = magical_converter(array('k' => 'user_id', 'v' => 'agency_name'), $agent_list);
		$page_data['agency_list'] = $this->domain_management_model->get_agent_list();
		$page_data["supplier_list"] = $this->domain_management_model->get_all_suplliers();
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

		if(is_active_sightseeing_module()){
			array_push($monthly_series_data, $this->monthly_sightseeing_search_history_log($year_start, $year_end));
			$page_data['activities_top_search'] = json_encode($this->sightseeing_top_search($year_start, $year_end));
		}

		if(is_active_transferv1_module()){
			array_push($monthly_series_data, $this->monthly_transfer_search_history_log($year_start, $year_end));
			$page_data['transfers_top_search'] = json_encode($this->transfer_top_search($year_start, $year_end));
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
		if(is_active_sightseeing_module()){
			$page_data['sightseeing_top_search'] = json_encode($this->sightseeing_top_search($year_start, $year_end));
		}
		if(is_active_transferv1_module()){
			$page_data['transfer_top_search'] = json_encode($this->transfer_top_search($year_start, $year_end));
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

	private function sightseeing_top_search($year_start,$year_end){
		$this->load->model('sightseeing_model');
		$temp_data = $this->sightseeing_model->top_search($year_start, $year_end);
		return $this->group_top_search_data($temp_data);

	}
	private function transfer_top_search($year_start,$year_end){
		$this->load->model('transferv1_model');
		$temp_data = $this->transferv1_model->top_search($year_start, $year_end);
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

	private function monthly_sightseeing_search_history_log($year_start, $year_end)
	{
		$this->load->model('sightseeing_model');
		$data['name'] = 'Activities';
		$temp_data = $this->sightseeing_model->monthly_search_history($year_start, $year_end);
		$data['data'] = $this->distribute_monthly_values($temp_data);
		$data['color'] = '#ff9800';
		return $data;
	}

	private function monthly_transfer_search_history_log($year_start, $year_end)
	{
		$this->load->model('transferv1_model');
		$data['name'] = 'Transfers';
		$temp_data = $this->transferv1_model->monthly_search_history($year_start, $year_end);
		$data['data'] = $this->distribute_monthly_values($temp_data);
		$data['color'] = '#456F13';
		return $data;
	}

	private function distribute_monthly_values($m_fill)
	{
		$m_fill = index_month_number($m_fill);
		$i = 0;
		$data = array();
		for ($i = 0; $i <= 11; $i++) {
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
	
	function get_booking_table_name_by_supplier_id($source_id)
	{
		$tbl_name_n_shortname = "";
		$condition = "";
		if(($source_id == PROVAB_FLIGHT_BOOKING_SOURCE) || 
			($source_id == TRAVELPORT_ACH_BOOKING_SOURCE) || 
			($source_id == TRAVELPORT_GDS_BOOKING_SOURCE) || 
			($source_id == SPICEJET_BOOKING_SOURCE) || 
			($source_id == STAR_BOOKING_SOURCE) || 
			($source_id == INDIGO_BOOKING_SOURCE))
		{
			$tbl_name_n_shortname = "flight_booking_details fbd";
			$condition = "bs.source_id = fbd.booking_source AND p.app_reference = fbd.app_reference";
		}
		if(($source_id == BITLA_BUS_BOOKING_SOURCE) || 
			($source_id == VRL_BUS_BOOKING_SOURCE) || 
			($source_id == ETS_BUS_BOOKING_SOURCE))
		{
			$tbl_name_n_shortname = "bus_booking_details bbd";
			$condition = "bs.source_id = bbd.booking_source AND p.app_reference = bbd.app_reference";
		}
		if(($source_id == REZLIVE_HOTEL))
		{
			$tbl_name_n_shortname = "hotel_booking_details hbd";
			$condition = "bs.source_id = hbd.booking_source AND p.app_reference = hbd.app_reference";
		}
		if(($source_id == PROVAB_SIGHTSEEN_BOOKING_SOURCE))
		{
			$tbl_name_n_shortname = "sightseeing_booking_details sbd";
			$condition = "bs.source_id = sbd.booking_source AND p.app_reference = sbd.app_reference";
		}
		if(($source_id == PROVAB_TRANSFERV1_BOOKING_SOURCE))
		{
			$tbl_name_n_shortname = "transferv1_booking_details tbd";
			$condition = "bs.source_id = tbd.booking_source AND p.app_reference = tbd.app_reference";
		}
		return array("table_name_n_shortname" => $tbl_name_n_shortname, "condition" => $condition);
	}

	/**
		Payment Gateway Transaction (Paid / Refund) report
		Shashikumar Misal
	**/

	function gateway_logs($offset=0)
	{	
		$get_data = $this->input->get();
		$condition = "";
		$page_data = array();
		$extra_tbl = "";
		if(isset($get_data["refund_list"]) && $get_data["refund_list"] == 1)
			$is_refund = 1;
		else
			$is_refund = 0;

		if($is_refund)
			$condition .= "p.refund_params != 'No refund initiated' AND";
		else
			$condition .= "p.refund_params = 'No refund initiated' AND";

		if(!empty($get_data["supplier_id"]))
		{
			$sup_tbl_cond = $this->get_booking_table_name_by_supplier_id($get_data["supplier_id"]);
			$extra_tbl = ", booking_source bs, ".$sup_tbl_cond["table_name_n_shortname"];
			$condition .= " ".$sup_tbl_cond["condition"]." AND bs.source_id = '".$get_data["supplier_id"]."' AND";
			//debug($condition); exit;
		}
		if(!empty($get_data["pg_name"]))
		{
			$condition .= ' p.pg_name = "'.$get_data['pg_name'].'" AND';
		}
		//From-Date and To-Date
		$from_date = trim(@$get_data['created_datetime_from']);
		$to_date = trim(@$get_data['created_datetime_to']);
		//Auto swipe date
		if(empty($from_date) == false && empty($to_date) == false){

			$valid_dates = auto_swipe_dates($from_date, $to_date);
			$from_date = $valid_dates['from_date'];
			$to_date = $valid_dates['to_date'];
		}
		
		if (intval(@$get_data['agent_id']) > 0) {
			$condition .= ' u.user_id = '.intval($get_data['agent_id']).' AND';
		}
		
		if(empty($from_date) == false) {
			$ymd_from_date = date('Y-m-d', strtotime($from_date));
			$condition .= ' p.created_datetime >= '.$this->db->escape($ymd_from_date).' AND';
		}

		if(empty($to_date) == false) {
			$ymd_to_date = date('Y-m-d', strtotime($to_date));
			$condition .= ' p.created_datetime <= '.$this->db->escape($ymd_to_date).' AND';
		}

		if (trim(@$get_data['transaction_type']) != '') {
			$extra_tbl .= ", transaction_log t";
			$condition .= ' t.transaction_type = '.$this->db->escape($get_data['transaction_type']).' AND t.app_reference=p.app_reference';
		}

		if (trim(@$get_data['app_reference']) != '') {
			$condition .= ' p.app_reference = '.$this->db->escape($get_data['app_reference']).' AND';
		}

		$condition = trim($condition, "AND");

		$total_records = $this->transaction_model->gateway_logs($condition, true, 0, 100000000000, $extra_tbl);
		$transaction_details = $this->transaction_model->gateway_logs($condition, false, $offset, $extra_tbl);
		//echo $this->transaction_model->db->last_query(); exit;
		$page_data['table_data'] = $transaction_details;

		if(@isset($get_data['excel_export'])){
			if($get_data["refund_list"]==1)
				$is_refund_excel = 1;
			else
				$is_refund_excel = 0;

			$this->generate_transaction_excel_report($page_data['table_data'], $is_refund_excel);
			exit;
		}

		$this->load->library('pagination');
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['base_url'] = base_url().'index.php/transaction/gateway_logs/';
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$page_data['total_rows'] = $config['total_rows'] = $total_records;
		$config['per_page'] = RECORDS_RANGE_3;
		$this->pagination->initialize($config);
		/** TABLE PAGINATION */
		$page_data['total_records'] = $config['total_rows'];

		$page_data['search_params'] = $get_data;
		// get active agent list
		$agent_list['data'] = $this->domain_management_model->agent_list();
		$page_data['agency_list'] = $this->domain_management_model->get_agent_list();
		$page_data['agent_list'] = magical_converter(array('k' => 'user_id', 'v' => 'agency_name'), $agent_list);
		$page_data["supplier_list"] = $this->domain_management_model->get_all_suplliers();
		//debug($page_data["supplier_list"]); exit;
		//debug($_SERVER['QUERY_STRING']); exit;
		$this->template->view('transaction/gateway_logs', $page_data);
	}
	function generate_transaction_excel_report($data, $is_refund)
	{
		$export_data = array();
        foreach ($data as $k => $v) {
           	if(!$is_refund){
				$rp = json_decode($v['response_params'], true);
				$ttype = "Inward";
			}
			
			if($is_refund){
				$rp = json_decode($v['refund_params'], true);
				$ttype = "Refund";
			}
			$rqp = json_decode($v['request_params'], true);
			$export_data[$k]['si_no'] = $k+1;
			$export_data[$k]['ttype'] = $ttype;
			$export_data[$k]['user_id'] = provab_decrypt($v['uuid']);
            $export_data[$k]['email'] = provab_decrypt($v['email']);
            $export_data[$k]['agency_name'] = $v['agency_name'];
            $export_data[$k]['amount'] = $v['amount'];
            $export_data[$k]['con_amt'] = $rqp["convenience_amount"];
            $export_data[$k]['currency'] = $v['currency'];
            $export_data[$k]['txn_status'] = $rp["txn_status"];
            $export_data[$k]['pg_txn_id'] = $rp["pg_txn_id"];
            if($is_refund){
            	$export_data[$k]['pg_refund_id'] = $rp["pg_refund_id"];
        	}
        	else
        		$export_data[$k]['pg_refund_id'] = "NA";
            if(!$is_refund){
	            $export_data[$k]['bank_name'] = $rp["bank_name"];
	            $export_data[$k]['bank_txn_id'] = $rp["bank_txn_id"];
        	}
        	else
        	{
        		$export_data[$k]['bank_name'] = "NA";
	            $export_data[$k]['bank_txn_id'] = "NA";
        	}
            $export_data[$k]['pg_name'] = $v["pg_name"];
            $export_data[$k]['created_datetime'] = $v["created_datetime"];

        }

           $headings = array('a1' => 'Sl. No.',
           			'b1' => 'Transaction Date',
           			'c1' => 'Type',
                    'd1' => 'User Id',
                    'e1' => 'Email',
                    'f1' => 'Agency Name',
                    'g1' => 'Currency',
                    'h1' => 'Amount',
                    'i1' => 'Admin Charges',
                    'j1' => 'Status',
                    'k1' => 'PG Tranx Id',
                    'l1' => 'PG Refund ID',
                    'm1' => 'Bank Name',
                    'n1' => 'Bank Txn Id',
                    'o1' => 'Gateway',
                );
                // field names in data set 
                $fields = array('a' => 'si_no', // empty for sl. no.
                	'b' => 'created_datetime',
                	'c' => 'ttype',
                    'd' => 'user_id',
                    'e' => 'email',
                    'f' => 'agency_name',
                    'g' => 'currency',
                    'h' => 'amount',
                    'i' => 'con_amt',
                    'j' => 'txn_status',
                    'k' => 'pg_txn_id',
                    'l' => 'pg_refund_id',
                    'm' => 'bank_name',
                    'n' => 'bank_txn_id',
                    'o' => 'pg_name',
                );
           
            $excel_sheet_properties = array(
                'title' => $ttype.'_Transaction_Report' . date('d-M-Y'),
                'creator' => 'Provab Technosoft',
                'description' => $ttype.' Transaction Report',
                'sheet_title' => $ttype.'_Transaction_Report'
            );

            $this->load->library('provab_excel'); // we need this provab_excel library to export excel.
            $this->provab_excel->custom_excel_export($headings, $fields, $export_data, $excel_sheet_properties);
	}

	public function Check_payment_status(){
		ini_set('display_errors', '1');
		ini_set('display_startup_errors', '1');
		error_reporting(E_ALL);
		require_once '/var/www/html/system/libraries/payment_gateway/paytm_deps/encdec_paytm.php';
		$paytmParams = array();
		
		$orderId = trim($this->input->post('ORDERID'));
		$MID = MID;
		$M_KEY = M_KEY;
		 $url = RECHARGE_URL; 
		$paytmParams["body"] = array(
		    "mid" => $MID,
		    "orderId" => $orderId,
		);
		$checksum = getChecksumFromString(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), $M_KEY);
		$paytmParams["head"] = array(
		    "signature"	=> $checksum
		);
		$post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));  
		$response = curl_exec($ch); 
		$response_data = json_decode($response,1);
		if($response_data['body']['resultInfo']['resultStatus'] == 'TXN_SUCCESS'){
			$payment_data = $this->transaction_model->get_details_by_order_id($orderId);
			//*******************transaction log******************************
			if($payment_data[0]['status'] != 'accepted'){
			$agent_bal = $payment_data[0]['due_amount'] + $response_data['body']['txnAmount'];
			
			$transaction_log['system_transaction_id'] = date('Ymd-His').'-S-'.rand(1, 10000);
			$transaction_log['transaction_type'] = 'transaction';
			$transaction_log['domain_origin'] = get_domain_auth_id();
			$transaction_log['app_reference'] = $orderId;
			$transaction_log['fare'] = -$response_data['body']['txnAmount'];
			$transaction_log['domain_markup'] = 0;
			$transaction_log['level_one_markup'] = 0;
			$transaction_log['convinence_fees'] = 0;
			$transaction_log['gst'] = 0;
			$transaction_log['promocode_discount'] = 0;
			$transaction_log['currency'] = $payment_data[0]['currency'];
			$transaction_log['currency_conversion_rate'] = $payment_data[0]['currency_conversion_rate'];
			$transaction_log['opening_balance'] = $payment_data[0]['due_amount'];
			$transaction_log['closing_balance'] = $agent_bal;
			$transaction_log['credit_limit'] = 0;
			$transaction_log['current_credit_limit'] = $payment_data[0]['credit_limit'];
			$transaction_log['remarks'] = 'Credited Towards: Instant recharge Reference: '.$orderId.' Amount credited to wallet';
			$transaction_log['transaction_owner_id'] = $payment_data[0]['transaction_owner_id'];
			$transaction_log['created_by_id'] = $payment_data[0]['transaction_owner_id'];
			$transaction_log['created_datetime'] = date('Y-m-d H:i:s', time());

			$this->transaction_model->insert_transaction_log_details($transaction_log);
			//*************************payment gateway details************************
			$response_params['txnid'] = $orderId;
			$response_params['productinfo'] = 'Instant_Recharge';
			$response_params['status'] = 'success';
			$response_params['pg_txn_id'] = $response_data['body']['txnId'];
			$response_params['is_valid_checksum'] = $response_data['head']['signature'];
			$response_params['bank_txn_id'] = $response_data['body']['bankTxnId'];
			$response_params['bank_name'] = $response_data['body']['bankName'];
			$response_params['txn_status'] = $response_data['body']['resultInfo']['resultStatus'];
			$attr['ORDERID'] = $response_data['body']['orderId'];
			$attr['MID'] = $response_data['body']['mid'];
			$attr['TXNID'] = $response_data['body']['txnId'];
			$attr['TXNAMOUNT'] = $response_data['body']['txnAmount'];
			$attr['PAYMENTMODE'] = $response_data['body']['paymentMode'];
			$attr['CURRENCY'] = $payment_data[0]['currency'];
			$attr['TXNDATE'] = $response_data['body']['txnDate'];
			$attr['STATUS'] = $response_data['body']['resultInfo']['resultStatus'];
			$attr['RESPCODE'] = $response_data['body']['resultInfo']['resultCode'];
			$attr['RESPMSG'] = $response_data['body']['resultInfo']['resultMsg'];
			$attr['GATEWAYNAME'] = $response_data['body']['gatewayName'];
			$attr['BANKTXNID'] = $response_data['body']['bankTxnId'];
			$attr['BANKNAME'] = $response_data['body']['bankName'];
			$attr['CHECKSUMHASH'] = $response_data['head']['signature'];
			$attr['pg_name'] = 'PAYTM';
			$response_params['attr'] = $attr;


			$payment_deatils['status'] = 'accepted';
			$payment_deatils['response_params'] = json_encode($response_params);
			$this->transaction_model->update_payment_deatils($payment_deatils,$orderId);
			//****************master transaction details******************************
			$origin_id1 = explode('-',$orderId);
			$origin_id = $origin_id1[2];
			$master_transaction_details['system_transaction_id'] = $orderId;
			$master_transaction_details['status'] = 'accepted';
			$this->transaction_model->update_master_transaction_details($master_transaction_details,$origin_id);
			//*****************update agent balance b2b user details******************
			$agent_bal_details['due_amount'] = $agent_bal;
			$this->transaction_model->update_agent_balance($agent_bal_details,$payment_data[0]['transaction_owner_id']);
			}
		}
		echo $response_data['body']['resultInfo']['resultStatus'];
	}
}
