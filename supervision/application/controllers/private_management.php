<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Provab - Provab Application
 * @subpackage Travel Portal
 * @author     Balu A<balu.provab@gmail.com> on 01-06-2015
 * @version    V2
 */

class Private_Management extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('private_management_model');
		$this->load->model('domain_management_model');
		$this->load->library('api_balance_manager');
	}

	/**
	* Managing Api Balance
	**/
	function api_balances()
	{
		$data["suppliers"] = $this->domain_management_model->get_all_active_inactive_suplliers(0, 1);
		
		$bitla = $this->api_balance_manager->getBitlaBalance();
		$data["balance_details"][BITLA_BUS_BOOKING_SOURCE]["balance"] = $bitla["result"]["balance_amount"];
		$data["balance_details"][BITLA_BUS_BOOKING_SOURCE]["credit_limit"] = $bitla["result"]["credit_limit"];
		$data["balance_details"][BITLA_BUS_BOOKING_SOURCE]["minimum_balance"] = 0;
		$data["balance_details"][BITLA_BUS_BOOKING_SOURCE]["due_amount"] = 0;

		$ets = $this->api_balance_manager->getEtsBalance();
		$data["balance_details"][ETS_BUS_BOOKING_SOURCE]["balance"] = $ets["balanceAmount"];
		$data["balance_details"][ETS_BUS_BOOKING_SOURCE]["credit_limit"] = 0;
		$data["balance_details"][ETS_BUS_BOOKING_SOURCE]["minimum_balance"] = $ets["lowBalanceAmount"];
		$data["balance_details"][ETS_BUS_BOOKING_SOURCE]["due_amount"] = 0;

		$tbo = $this->api_balance_manager->getTboBalance();
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
		}
		$this->template->view("private_management/api_balances", $data);
	}

	function save_supplier_balance()
	{
		$source_id = $this->input->post("source_id");
		$data["balance"] = 0;
		$data["minimum_balance"] = $this->input->post("minimum_balance");
		$data["credit_limit"] = $this->input->post("credit_limit");
		$status = $this->custom_db->update_record("booking_source", $data, array("source_id" => $source_id));

		$balance = $this->input->post("balance");
		$this->api_balance_manager->update_api_balance($source_id, $balance);
		$response["status"] = 1;
		$response["msg"] = "<div class='alert alert-success'>Updated successfully</div>";

		$cond = array('source_id' => $source_id);
		$details = $this->custom_db->single_table_records('booking_source', 'balance, minimum_balance, credit_limit, due_amount', $cond);
		$response["data"] = $details["data"][0];

		echo json_encode($response);
		exit;
	}

	function supplier_credentials($module)
	{
		$mcl = $this->custom_db->single_table_records("meta_course_list", "course_id", array("name"=>$module));
		$course_id = $mcl["data"][0]["course_id"];
		$apis = $this->custom_db->single_table_records("booking_source", "source_id, description", array("meta_course_list_id"=>$course_id, "booking_engine_status"=>ACTIVE));
		$data["apis"] = $apis["data"];
		$data["module"] = $module;
		$this->template->view("private_management/supplier_credentials", $data);
	}
	function get_supplier_credentials($booking_source, $mode, $module)
	{
		$tbl_name = "supplier_credentials";
		$fields = "config";
		$keys = array("booking_source"=>$booking_source, "mode" => $mode);

		if($module == "flight")
		{
			$this->load->database("services", "service_db");
			$tbl_name = "api_config";
			$fields = "config";
			$keys = array("pace_bs_id"=>$booking_source, "system" => $mode);
		}

		$credentials = $this->custom_db->single_table_records($tbl_name, $fields, $keys);
		$config = json_decode($credentials["data"][0]["config"], true);
		$fields = "";
		foreach($config AS $key => $val)
		{
			if(is_array($val)){
				foreach($val AS $key1 => $val1)
				{
					$fields .= '<div class="form-group">
						<label class="col-sm-3 control-label">'.$key1.'</label>
						<div class="col-sm-6">
						<input type="text" class="form-control" placeholder="'.$key1.'" name="'.$key1.'" value="'.$val1.'">
						</div></div>';
				}
			}
			else
			{
			$fields .= '<div class="form-group">
						<label class="col-sm-3 control-label">'.$key.'</label>
						<div class="col-sm-6">
						<input type="text" class="form-control" placeholder="'.$key.'" name="'.$key.'" value="'.$val.'">
						</div></div>';
			}
		}
		echo $fields; 
		exit;
	}
	function save_supplier_credentials($booking_source, $mode, $module)
	{
		$tbl_name = "supplier_credentials";
		$keys = array("booking_source"=>$booking_source, "mode" => $mode);

		if($module == "flight")
		{
			$this->load->database("services", "service_db");
			$tbl_name = "api_config";
			$keys = array("pace_bs_id"=>$booking_source, "system" => $mode);
		}
		$post_data = $this->input->post();
		if($booking_source == SPICEJET_BOOKING_SOURCE)
		{
			$post_data["end_point"] = array("session" => $post_data["session"],
									"booking" => $post_data["booking"],
									"content" => $post_data["content"]);
			unset($post_data["session"]);
			unset($post_data["booking"]);
			unset($post_data["content"]);
		}
		$config = json_encode($post_data);
		$data["config"] = $config;
		$this->custom_db->update_record($tbl_name, $data, $keys);
		echo '<div class="alert alert-success">Credentials updated successfully</div>';
		exit;
	}
	/**
	 * Manage domain markup for provab - Domain wise and module wise
	 */
	function airline_domain_markup()
	{
		$page_data['form_data'] = $this->input->post();
		if (valid_array($page_data['form_data']) == true) {
			switch($page_data['form_data']['form_values_origin']) {
				case 'generic':
					$this->private_management_model->save_markup_data(
					$page_data['form_data']['markup_origin'], $page_data['form_data']['form_values_origin'], 'b2c_flight', 0,
					$page_data['form_data']['generic_value'], $page_data['form_data']['value_type'], $page_data['form_data']['domain_origin']
					);
					break;
				case 'specific':
					if (valid_array($page_data['form_data']['domain_origin'])) {
						foreach($page_data['form_data']['domain_origin'] as $__k => $__domain_origin) {
							if ($page_data['form_data']['specific_value'][$__k] != '' && intval($page_data['form_data']['specific_value'][$__k]) > -1
							&& empty($page_data['form_data']['value_type_'.$__domain_origin]) == false
							) {
								$this->private_management_model->save_markup_data(
								$page_data['form_data']['markup_origin'][$__k], $page_data['form_data']['form_values_origin'], 'b2c_flight', $page_data['form_data']['domain_origin'][$__k],
								$page_data['form_data']['specific_value'][$__k], $page_data['form_data']['value_type_'.$__domain_origin], $page_data['form_data']['domain_origin'][$__k]
								);
							}
						}
					}
					break;
			}
			redirect(base_url().'index.php/private_management/'.__FUNCTION__);
		}
		//Airline would have All - general and domain wise markup
		$data_list = $this->private_management_model->airline_domain_markup();
		$this->template->view('private_management/airline_domain_markup', $data_list['data']);
	}

	/**
	 * Manage domain markup for provab - Domain wise and module wise
	 */
	function hotel_domain_markup()
	{
		//Hotel would have All - general and domain wise markup
		$page_data['form_data'] = $this->input->post();
		if (valid_array($page_data['form_data']) == true) {
			switch($page_data['form_data']['form_values_origin']) {
				case 'generic':
					$this->private_management_model->save_markup_data(
					$page_data['form_data']['markup_origin'], $page_data['form_data']['form_values_origin'], 'b2c_hotel', 0,
					$page_data['form_data']['generic_value'], $page_data['form_data']['value_type'], $page_data['form_data']['domain_origin']
					);
					break;
				case 'specific':
					if (valid_array($page_data['form_data']['domain_origin'])) {
						foreach($page_data['form_data']['domain_origin'] as $__k => $__domain_origin) {
							if ($page_data['form_data']['specific_value'][$__k] != '' && intval($page_data['form_data']['specific_value'][$__k]) > -1
							&& empty($page_data['form_data']['value_type_'.$__domain_origin]) == false
							) {
								$this->private_management_model->save_markup_data(
								$page_data['form_data']['markup_origin'][$__k], $page_data['form_data']['form_values_origin'], 'b2c_hotel', $page_data['form_data']['domain_origin'][$__k],
								$page_data['form_data']['specific_value'][$__k], $page_data['form_data']['value_type_'.$__domain_origin], $page_data['form_data']['domain_origin'][$__k]
								);
							}
						}
					}
					break;
			}
			redirect(base_url().'index.php/private_management/'.__FUNCTION__);
		}
		//Airline would have All - general and domain wise markup
		$data_list = $this->private_management_model->hotel_domain_markup();
		$this->template->view('private_management/hotel_domain_markup', $data_list['data']);
	}
	/**
	 * Manage domain markup for provab - Domain wise and module wise
	 */
	function transfer_domain_markup()
	{
		//Hotel would have All - general and domain wise markup
		$page_data['form_data'] = $this->input->post();
		if (valid_array($page_data['form_data']) == true) {
			switch($page_data['form_data']['form_values_origin']) {
				case 'generic':
					$this->private_management_model->save_markup_data(
					$page_data['form_data']['markup_origin'], $page_data['form_data']['form_values_origin'], 'b2c_transferv1', 0,
					$page_data['form_data']['generic_value'], $page_data['form_data']['value_type'], $page_data['form_data']['domain_origin']
					);
					break;
				case 'specific':
					if (valid_array($page_data['form_data']['domain_origin'])) {
						foreach($page_data['form_data']['domain_origin'] as $__k => $__domain_origin) {
							if ($page_data['form_data']['specific_value'][$__k] != '' && intval($page_data['form_data']['specific_value'][$__k]) > -1
							&& empty($page_data['form_data']['value_type_'.$__domain_origin]) == false
							) {
								$this->private_management_model->save_markup_data(
								$page_data['form_data']['markup_origin'][$__k], $page_data['form_data']['form_values_origin'], 'b2c_hotel', $page_data['form_data']['domain_origin'][$__k],
								$page_data['form_data']['specific_value'][$__k], $page_data['form_data']['value_type_'.$__domain_origin], $page_data['form_data']['domain_origin'][$__k]
								);
							}
						}
					}
					break;
			}
			redirect(base_url().'index.php/private_management/'.__FUNCTION__);
		}
		//Airline would have All - general and domain wise markup
		$data_list = $this->private_management_model->transfer_domain_markup();
		$this->template->view('private_management/transfer_domain_markup', $data_list['data']);
	}
	/**
	 * Manage domain markup for provab - Domain wise and module wise
	 */
	function sightseeing_domain_markup()
	{
		//Hotel would have All - general and domain wise markup
		$page_data['form_data'] = $this->input->post();
		if (valid_array($page_data['form_data']) == true) {
			switch($page_data['form_data']['form_values_origin']) {
				case 'generic':
					$this->private_management_model->save_markup_data(
					$page_data['form_data']['markup_origin'], $page_data['form_data']['form_values_origin'], 'b2c_sightseeing', 0,
					$page_data['form_data']['generic_value'], $page_data['form_data']['value_type'], $page_data['form_data']['domain_origin']
					);
					break;
				case 'specific':
					if (valid_array($page_data['form_data']['domain_origin'])) {
						foreach($page_data['form_data']['domain_origin'] as $__k => $__domain_origin) {
							if ($page_data['form_data']['specific_value'][$__k] != '' && intval($page_data['form_data']['specific_value'][$__k]) > -1
							&& empty($page_data['form_data']['value_type_'.$__domain_origin]) == false
							) {
								$this->private_management_model->save_markup_data(
								$page_data['form_data']['markup_origin'][$__k], $page_data['form_data']['form_values_origin'], 'b2c_hotel', $page_data['form_data']['domain_origin'][$__k],
								$page_data['form_data']['specific_value'][$__k], $page_data['form_data']['value_type_'.$__domain_origin], $page_data['form_data']['domain_origin'][$__k]
								);
							}
						}
					}
					break;
			}
			redirect(base_url().'index.php/private_management/'.__FUNCTION__);
		}
		//Airline would have All - general and domain wise markup
		$data_list = $this->private_management_model->sightseeing_domain_markup();
		$this->template->view('private_management/sightseeing_domain_markup', $data_list['data']);
	}

	/**
	 * Manage domain markup for provab - Domain wise and module wise
	 */
	function bus_domain_markup()
	{
		//Bus would have All - general and domain wise markup
		$page_data['form_data'] = $this->input->post();
		if (valid_array($page_data['form_data']) == true) {
			switch($page_data['form_data']['form_values_origin']) {
				case 'generic':
					$this->private_management_model->save_markup_data(
					$page_data['form_data']['markup_origin'], $page_data['form_data']['form_values_origin'], 'b2c_bus', 0,
					$page_data['form_data']['generic_value'], $page_data['form_data']['value_type'], $page_data['form_data']['domain_origin']
					);
					break;
				case 'specific':
					if (valid_array($page_data['form_data']['domain_origin'])) {
						foreach($page_data['form_data']['domain_origin'] as $__k => $__domain_origin) {
							if ($page_data['form_data']['specific_value'][$__k] != '' && intval($page_data['form_data']['specific_value'][$__k]) > -1
							&& empty($page_data['form_data']['value_type_'.$__domain_origin]) == false
							) {
								$this->private_management_model->save_markup_data(
								$page_data['form_data']['markup_origin'][$__k], $page_data['form_data']['form_values_origin'], 'b2c_bus', $page_data['form_data']['domain_origin'][$__k],
								$page_data['form_data']['specific_value'][$__k], $page_data['form_data']['value_type_'.$__domain_origin], $page_data['form_data']['domain_origin'][$__k]
								);
							}
						}
					}
					break;
			}
			redirect(base_url().'index.php/private_management/'.__FUNCTION__);
		}
		//Airline would have All - general and domain wise markup
		$data_list = $this->private_management_model->bus_domain_markup();
		$this->template->view('private_management/bus_domain_markup', $data_list['data']);
	}

	/**
	 * Balu A
	 * Process Balance Request with provab
	 */
	function process_balance_manager()
	{
		
		if (!is_domain_user()) {
			$page_data['form_data'] = $this->input->post();
			
			if (valid_array($page_data['form_data']) == true) {
				
				$process_details = $this->private_management_model->process_balance_request($page_data['form_data']['request_origin'], $page_data['form_data']['system_request_id'], $page_data['form_data']['status_id'], $page_data['form_data']['update_remarks']);
				redirect(base_url().'index.php/private_management/'.__FUNCTION__);
			}
			$page_data['provab_balance_requests'] = get_enum_list('provab_balance_status');
			$page_data['table_data'] = $this->private_management_model->master_transaction_request_list();
                        
			$this->template->view('private_management/process_balance_manager', $page_data);
		}
	}

	/**
	 * Event logging
	 * @param number $offset
	 */
	function event_logs($offset=0)
	{
		$condition = array();
		$page_data['table_data'] = $this->private_management_model->event_logs($condition, false, $offset, RECORDS_RANGE_2);
		$total_records = $this->private_management_model->event_logs($condition, true);
		$this->load->library('pagination');
		$config['base_url'] = base_url().'index.php/private_management/event_logs/';
		$page_data['total_rows'] = $config['total_rows'] = $total_records;
		$config['per_page'] = RECORDS_RANGE_2;
		$this->pagination->initialize($config);
		/** TABLE PAGINATION */
		$page_data['total_records'] = $config['total_rows'];
		$this->template->view('private_management/event_logs', $page_data);
	}

	/**
	 * Admin Credits Balance to Agent Directly
	 * 
	 */
	public function credit_balance()
	{
		$get_data = $this->input->get();
		$post_data = $this->input->post();
		$page_data = array();
		$form_data = array();
		$page_data['form_data'] = $form_data;
		if(valid_array($get_data) == true && isset($get_data['agent_id']) == true && empty($get_data['agent_id']) == false){

			$agent_id = $get_data['agent_id'];
			$agent_details = $this->domain_management_model->get_agent_details($agent_id);
			if(valid_array($agent_details) == false){//Invalid Agent ID
				redirect(base_url());
			}
			$page_data['agent_details'] = $agent_details;
			$page_data['agent_id'] = $agent_id;
		}
		//echo 'inprocess'; die();
		if (valid_array($post_data) == true) {

			$this->current_page->set_auto_validator();
			
			if ($this->form_validation->run()) {
				unset($post_data['FID']);
				$post_data['amount'] = abs($post_data['amount']);//CREDTING AMOUNT
				$agent_base_currency = $agent_details['agent_base_currency'];
				$agent_currency_convsesrion_rate = $this->domain_management_model->get_currency_conversion_rate($agent_base_currency);//TODO: find better solution, place it in currency library
				$deposit_amount = ($post_data['amount']*$agent_currency_convsesrion_rate['conversion_rate']);//Converting to INR
				$currency_obj = new Currency();
				$currency_conversion_rate = $currency_obj->getConversionRate(false, get_application_default_currency(), $agent_base_currency);//Currency conversion rate of the domain currency
				$post_data['agent_list_fk'] = $agent_id;
				$post_data['remarks'] = $post_data['remarks'];
				$post_data['amount'] = $deposit_amount;
				$post_data['currency'] = $agent_details['agent_base_currency'];
				$post_data['currency_conversion_rate'] = $currency_conversion_rate;
				$post_data['issued_for'] = 'Credited Towards: '.$post_data['issued_for'];
				$this->domain_management_model->process_direct_credit_debit_transaction($post_data);
				//set_update_message('Amount Credited Successfully', SUCCESS_MESSAGE, array('override_app_msg' => true));
				refresh();
			}
		}
		$this->template->view('private_management/credit_balance', $page_data);
	}

	/**
	 * Admin Debits Balance from Agent Directly
	 * 
	 */
	public function debit_balance()
	{	
		error_reporting(0);
		$get_data = $this->input->get();
		$post_data = $this->input->post();
		$page_data = array();
		$form_data = array();
		$page_data['form_data'] = $form_data;
		if(valid_array($get_data) == true && isset($get_data['agent_id']) == true && empty($get_data['agent_id']) == false){
			$agent_id = $get_data['agent_id'];
			$agent_details = $this->domain_management_model->get_agent_details($agent_id);
			if(valid_array($agent_details) == false){//Invalid Agent ID
				redirect(base_url());
			}
			$page_data['agent_details'] = $agent_details;
			$page_data['agent_id'] = $agent_id;
		}
		if (valid_array($post_data) == true) {
			$this->current_page->set_auto_validator();
			if ($this->form_validation->run()) {
				unset($post_data['FID']);
				$post_data['amount'] = -abs($post_data['amount']);//DEBITING AMOUNT
				$agent_base_currency = $agent_details['agent_base_currency'];
				$agent_currency_convsesrion_rate = $this->domain_management_model->get_currency_conversion_rate($agent_base_currency);//TODO: find better solution, place it in currency library
				$debit_amount = ($post_data['amount']*$agent_currency_convsesrion_rate['conversion_rate']);//Converting to INR
				$currency_obj = new Currency();
				$currency_conversion_rate = $currency_obj->getConversionRate(false, get_application_default_currency(), $agent_base_currency);//Currency conversion rate of the domain currency
				$post_data['agent_list_fk'] = $agent_id;
				$post_data['remarks'] = $post_data['remarks'];
				$post_data['amount'] = $debit_amount;
				$post_data['currency'] = $agent_details['agent_base_currency'];
				$post_data['currency_conversion_rate'] = $currency_conversion_rate;
				$post_data['issued_for'] = 'Debited Towards: '.$post_data['issued_for'];
				$this->domain_management_model->process_direct_credit_debit_transaction($post_data,'Debit');
				//set_update_message('Amount Debited Successfully', SUCCESS_MESSAGE, array('override_app_msg' => true));
				redirect(base_url().'management/account_ledger');
			}
		}
		$this->template->view('private_management/debit_balance', $page_data);
	}
}
