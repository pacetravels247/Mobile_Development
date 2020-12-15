<?php
require_once 'abstract_management_model.php';
/**
 * @package    Provab Application
 * @subpackage Travel Portal
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V2
 */
Class Domain_Management_Model extends Abstract_Management_Model
{
	function __construct() {
		parent::__construct('level_2');
	}
	
/**
	 * Balu A
	 * Get markup based on different modules
	 * @return array('value' => 0, 'type' => '')
	 */
	function get_markup($module_name)
	{
		$markup_data = '';
		switch ($module_name) {
			case 'flight' : $markup_data = $this->b2b_airline_markup();
			break;
			case 'hotel' : $markup_data = $this->b2b_hotel_markup();
			break;
			case 'bus' : $markup_data = $this->b2b_bus_markup();
			break;
			case 'sightseeing': $markup_data = $this->b2b_sightseeing_markup();
			break;
			case 'transferv1': $markup_data = $this->b2b_transferv1_markup();
			break;

		}	
		return $markup_data;
	}

	/**
	 * Balu A
	 * Manage domain markup for b2b domain
	 */
	function b2b_airline_markup()
	{
		if (empty($this->airline_markup) == true) {
			$response['specific_markup_list'] = $this->specific_airline_markup('b2b_flight');
			$response['generic_markup_list'] = $this->generic_domain_markup('b2b_flight');
			$this->airline_markup = $response;
		} else {
			$response = $this->airline_markup;
		}
		return $response;
	}

	/**
	 * Balu A
	 * Manage domain markup for b2b domain
	 */
	function b2b_hotel_markup()
	{
		if (empty($this->hotel_markup) == true) {
			$response['specific_markup_list'] = '';
			$response['generic_markup_list'] = $this->generic_domain_markup('b2b_hotel');
			$this->hotel_markup = $response;
		} else {
			$response = $this->hotel_markup;
		}
		return $response;
	}

	/**
	 * Elavarasi
	 * Manage domain markup for b2b domain
	 */
	function b2b_sightseeing_markup()
	{
		if (empty($this->sightseeing_markup) == true) {
			$response['specific_markup_list'] = '';
			$response['generic_markup_list'] = $this->generic_domain_markup('b2b_sightseeing');
			$this->sightseeing_markup = $response;
		} else {
			$response = $this->sightseeing_markup;
		}
		return $response;
	}
	function b2b_transferv1_markup(){
		if (empty($this->transferv1_markup) == true) {
			$response['specific_markup_list'] = '';
			$response['generic_markup_list'] = $this->generic_domain_markup('b2b_transferv1');
			$this->transferv1_markup = $response;
		} else {
			$response = $this->transferv1_markup;
		}
		return $response;
	}
	/**
	 * Anitha G
	 * Manage domain markup for b2b domain
	 */
	function b2b_car_markup()
	{
		if (empty($this->hotel_markup) == true) {
			$response['specific_markup_list'] = '';
			$response['generic_markup_list'] = $this->generic_domain_markup('b2b_car');
			$this->hotel_markup = $response;
		} else {
			$response = $this->hotel_markup;
		}
		return $response;
	}
	/**
	 * Balu A
	 * Manage domain markup for b2b domain
	 */
	function b2b_bus_markup()
	{
		if (empty($this->bus_markup) == true) {
			$response['specific_markup_list'] = '';
			$response['generic_markup_list'] = $this->generic_domain_markup('b2b_bus');
			$this->bus_markup = $response;
		} else {
			$response = $this->bus_markup;
		}
		return $response;
	}

	/**
	 * Balu A
	 * Manage domain markup for provab - Domain wise and module wise
	 */
	function b2c_airline_markup()
	{
		$response['data'] = array();
		$response['data']['specific_markup_list'] = $this->specific_airline_markup('b2c_flight');
		$response['data']['generic_markup_list'] = $this->generic_domain_markup('b2c_flight');
		return $response;
	}

	/**
	 * Balu A
	 * Manage domain markup for provab - Domain wise and module wise
	 */
	function b2c_hotel_markup()
	{
		$response['data'] = array();
		$response['data']['specific_markup_list'] = '';
		$response['data']['generic_markup_list'] = $this->generic_domain_markup('b2c_hotel');
		return $response;
	}

	/**
	 * Elavarasi
	 * Manage domain markup for provab - Domain wise and module wise
	 */
	function b2c_sightseeing_markup()
	{
		$response['data'] = array();
		$response['data']['specific_markup_list'] = '';
		$response['data']['generic_markup_list'] = $this->generic_domain_markup('b2c_sightseeing');
		return $response;
	}
	/**
	 * Elavarasi
	 * Manage domain markup for provab - Domain wise and module wise
	 */
	function b2c_transferv1_markup()
	{
		$response['data'] = array();
		$response['data']['specific_markup_list'] = '';
		$response['data']['generic_markup_list'] = $this->generic_domain_markup('b2c_transferv1');
		return $response;
	}
	/**
	 * Balu A
	 * Manage domain markup for provab - Domain wise and module wise
	 */
	function b2c_bus_markup()
	{
		$response['data'] = array();
		$response['data']['specific_markup_list'] = '';
		$response['data']['generic_markup_list'] = $this->generic_domain_markup('b2c_bus');
		return $response;
	}
	/**
	 * Anitha G
	 * Manage domain markup for provab - Domain wise and module wise
	 */
	function b2c_car_markup()
	{
		$response['data'] = array();
		$response['data']['specific_markup_list'] = '';
		$response['data']['generic_markup_list'] = $this->generic_domain_markup('b2c_car');
		return $response;
	}
	/**
	 * Balu A
	 * Get generic markup based on the module type
	 * @param $module_type
	 * @param $markup_level
	 */
	function generic_domain_markup($module_type)
	{
		$query = 'SELECT ML.origin AS markup_origin, ML.type AS markup_type, ML.reference_id, ML.value, ML.value_type, ML.int_value, ML.int_value_type
		FROM markup_list AS ML where ML.module_type = "'.$module_type.'" and
		ML.markup_level = "'.$this->markup_level.'" and ML.type="generic" and ML.domain_list_fk='.get_domain_auth_id();
		$generic_data_list = $this->db->query($query)->result_array();
		return $generic_data_list;
	}

	/**
	 * Get specific markup based on module type
	 * @param string $module_type	Name of the module for which the markup has to be returned
	 * @param string $markup_level	Level of markup
	 */
	function specific_airline_markup($module_type)
	{
		$sub_query = 'SELECT AL.origin 
		FROM airline_list AS AL 
		JOIN markup_list AS ML ON
		ML.module_type = "'.$module_type.'" and ML.markup_level = "'.$this->markup_level.'" and AL.origin=ML.reference_id and ML.type="specific"
		and ML.domain_list_fk != 0  and ML.domain_list_fk='.get_domain_auth_id();
		
		$query = 'SELECT AL.origin AS airline_origin, AL.name AS airline_name, AL.code AS airline_code,
		ML.origin AS markup_origin, ML.type AS markup_type, ML.reference_id, ML.value, ML.value_type, ML.int_value, ML.int_value_type
		FROM airline_list AS AL LEFT JOIN markup_list AS ML ON
		ML.module_type = "'.$module_type.'" and ML.markup_level = "'.$this->markup_level.'" and AL.origin=ML.reference_id and ML.type="specific"
		and ML.domain_list_fk != 0  and ML.domain_list_fk='.get_domain_auth_id().' 
		where (AL.has_specific_markup='.ACTIVE.' OR AL.origin in ('.$sub_query.')) order by AL.name ASC';
		$specific_data_list = $this->db->query($query)->result_array();
		return $specific_data_list;
	}
	/**
	 * Get Details based on Airline Code
	 * @param string $module_type	Name of the module for which the markup has to be returned
	 * @param string $markup_level	Level of markup
	 */
	function individual_airline_markup_details($module_type, $airline_code)
	{
		$query = 'SELECT ML.origin as markup_list_origin,AL.origin as airline_list_origin 
		FROM airline_list AS AL 
		left JOIN markup_list AS ML ON
		ML.module_type = "'.$module_type.'" and ML.markup_level = "'.$this->markup_level.'" and AL.origin=ML.reference_id and ML.type="specific"
		and ML.domain_list_fk != 0  and ML.domain_list_fk='.get_domain_auth_id().' where AL.code="'.$airline_code.'"';
		$specific_data_list = $this->db->query($query)->row_array();
		return $specific_data_list;
	}

	/**
	 * Get Commission Details based on Airline Code
	 */
	function individual_airline_commission_details($airline_code)
	{
		$query = 'SELECT BFCD.origin as comm_origin, AL.origin as airline_origin 
		FROM airline_list AS AL left JOIN b2b_flight_commission_details AS BFCD ON
		AL.origin=BFCD.airline_id and BFCD.type="'.FLIGHT_SPECIFIC.'"
		and BFCD.domain_list_fk != 0  and BFCD.domain_list_fk='.get_domain_auth_id().' where AL.code="'.$airline_code.'"';
		$specific_data_list = $this->db->query($query)->row_array();
		return $specific_data_list;
	}
	
	/**
	 * Get specific markup based on module type
	 * @param string $module_type	Name of the module for which the markup has to be returned
	 * @param string $markup_level	Level of markup
	 */
	function specific_agent_markup($module_type)
	{
		//FIXME
		$query = 'SELECT AL.origin AS airline_origin, AL.name AS airline_name, AL.code AS airline_code,
		ML.origin AS markup_origin, ML.type AS markup_type, ML.reference_id, ML.value, ML.value_type
		FROM airline_list AS AL LEFT JOIN markup_list AS ML ON
		ML.module_type = "'.$module_type.'" and ML.markup_level = "'.$this->markup_level.'" and AL.origin=ML.reference_id and ML.type="specific"
		and ML.domain_list_fk != 0  and ML.domain_list_fk='.get_domain_auth_id().' order by AL.name ASC';
		$specific_data_list = $this->db->query($query)->result_array();
		return $specific_data_list;
	}

	/**
	 * save master transaction details request
	 * @param array $details
	 */
	function save_master_transaction_details($details)
	{
		$master_transaction_details['system_transaction_id'] = 'DEP-'.$this->entity_user_id.time();
		$master_transaction_details['domain_list_fk'] = get_domain_auth_id();
		$master_transaction_details['transaction_type'] = $details['transaction_type'];
		$master_transaction_details['amount'] = $details['amount'];
		$master_transaction_details['currency_converter_origin'] = $details['currency_converter_origin'];
		$master_transaction_details['conversion_value'] = $details['conversion_value'];
		$master_transaction_details['date_of_transaction'] = valid_date_value($details['date_of_transaction']);
		$master_transaction_details['bank'] = $details['bank'];
		$master_transaction_details['branch'] = $details['branch'];
		$master_transaction_details['transaction_number'] = isset($details['transaction_number']) ? $details['transaction_number'] : 'N/A';
		$master_transaction_details['status'] = 'pending';
		$master_transaction_details['remarks'] = $details['remarks'];
		$master_transaction_details['created_datetime'] = db_current_datetime();
		$master_transaction_details['created_by_id'] = $this->entity_user_id;
		$master_transaction_details['user_oid'] = $this->entity_user_id;
		$insert_id = $this->custom_db->insert_record('master_transaction_details', $master_transaction_details);
		return $insert_id['insert_id'];
	}

	/**
	 * Master Transaction Request List
	 */
	function master_transaction_request_list($type='provab', $data_list_filt=array(), $credit='')
	{
		$data_list_cond = '';
		if (valid_array($data_list_filt) == true) {
			$data_list_cond = $this->custom_db->get_custom_condition($data_list_filt);
		}
		if(empty($data_list_filt)){
			$query = 'select MTD.*, CONCAT(U.first_name, " ", U.last_name) request_user, U.email,U.phone, U.agency_name AS requested_from from master_transaction_details MTD, user U
			where MTD.created_by_id=U.user_id AND DATE(MTD.created_datetime) = CURDATE() AND MTD.type="'.$type.'" and MTD.domain_list_fk = '.get_domain_auth_id().' '.$data_list_cond.'
			and MTD.transaction_type !="Credit" order by MTD.updated_datetime DESC, MTD.created_datetime DESC';
		}else{

			if(!empty($credit)){
				$query = 'select MTD.*, CONCAT(U.first_name, " ", U.last_name) request_user, U.email,U.phone, U.agency_name AS requested_from from master_transaction_details MTD, user U
				where MTD.created_by_id=U.user_id AND MTD.type="'.$type.'" and MTD.domain_list_fk = '.get_domain_auth_id().' '.$data_list_cond.'
				and MTD.transaction_type ="Credit" order by MTD.updated_datetime DESC, MTD.created_datetime DESC';
			}
			else{
				$query = 'select MTD.*, CONCAT(U.first_name, " ", U.last_name) request_user, U.email,U.phone,U.agency_name AS requested_from from master_transaction_details MTD, user U
				where MTD.created_by_id=U.user_id AND MTD.type="'.$type.'" and MTD.domain_list_fk = '.get_domain_auth_id().' '.$data_list_cond.'
				and MTD.transaction_type !="Credit" order by MTD.updated_datetime DESC, MTD.created_datetime DESC';
			}
		}
		$data['list'] = $this->db->query($query)->result_array();
		$query1 = 'select MTD.*, CONCAT(U.first_name, " ", U.last_name) request_user, U.email,U.phone, U.agency_name AS requested_from from master_transaction_details MTD, user U
				where MTD.created_by_id=U.user_id AND MTD.type="'.$type.'" and MTD.status = "pending" and MTD.domain_list_fk = '.get_domain_auth_id().' and MTD.transaction_type !="Credit" order by MTD.created_datetime DESC';
		$data['pending_list'] = $this->db->query($query1)->result_array();
		return $data;
	}

	/**
	 * Bus Operator cancel Request List
	 */
	function boc_request_list($data_list_filt = '')
	{
		// echo $type;exit;
		$data_list_cond = '';
		if (valid_array($data_list_filt) == true) {
			$data_list_cond = $this->custom_db->get_custom_condition($data_list_filt);
		}
		$query = "select * from bus_operator_cancellation BOC, user U where 
		BOC.agent_id=U.user_id ".$data_list_cond." order by BOC.id DESC";
		//debug($data_list_cond);exit;
		//echo $query;exit;
		return $this->db->query($query)->result_array();
	}
	
	/**
	 *
	 */
	function event_logs($condition=array(), $count=false, $offset=0, $limit=100000000000)
	{
		//BT, CD, ID
		if ($count) {
			$query = 'select count(*) as total_records from exception_logger where domain_origin='.get_domain_auth_id();
			$data = $this->db->query($query)->row_array();
			return $data['total_records'];
		} else {
			$query = 'select * from exception_logger where domain_origin='.get_domain_auth_id().' order by origin desc limit '.$offset.', '.$limit;
			return $this->db->query($query)->result_array();
		}
	}
	function xml_flight_logs($condition=array(), $count=false, $offset=0, $limit=100000000000)
	{
		//BT, CD, ID
		if ($count) {
			$query = 'select count(*) as total_records from provab_api_response_history';
			$data = $this->db->query($query)->row_array();
			return $data['total_records'];
		} else {
			$query = 'select origin, request, response AS test, created_datetime AS time from provab_api_response_history order by origin desc limit '.$offset.', '.$limit;
			return $this->db->query($query)->result_array();
		}
	}
	function xml_logs($condition=array(), $count=false, $offset=0, $limit=100000000000)
	{
		//BT, CD, ID
		if ($count) {
			$query = 'select count(*) as total_records from test';
			$data = $this->db->query($query)->row_array();
			return $data['total_records'];
		} else {
			$query = 'select * from test order by origin desc limit '.$offset.', '.$limit;
			return $this->db->query($query)->result_array();
		}
	}

	function tds_certificates($condition=array(), $count=false, $offset=0, $limit=100000000000)
	{
		//BT, CD, ID
		if ($count) {
			$query = 'select count(*) as total_records from tds_certificates tds, user u 
			WHERE u.pan_number = tds.pan_no';
			$data = $this->db->query($query)->row_array();
			return $data['total_records'];
		} else {
			$query = 'select * from tds_certificates tds, user u WHERE u.pan_number = tds.pan_no 
			order by id desc limit '.$offset.', '.$limit;
			return $this->db->query($query)->result_array();
		}
	}

	/**
	 * Process Update Request
	 * @param number $origin
	 * @param string $system_request_id
	 * @param string $status_id
	 * @param string $update_remarks
	 *
	 * @return $response status of the update operation
	 */
	function process_balance_request($origin, $system_request_id, $status_id, $update_remarks, $type='')
	{

		$response['status']	= SUCCESS_STATUS;
		$response['data']	= array();
		//get amount details to process - safety
		$transaction_details_cond = array('origin' => intval($origin), 'system_transaction_id' => $system_request_id, 'type' => 'b2b');
		//Depending on status update
		$transaction_details = $this->custom_db->single_table_records('master_transaction_details', '*', $transaction_details_cond);
		if (valid_array($transaction_details['data']) == true && strtoupper($transaction_details['data'][0]['status']) == 'PENDING') {
			$response['data'] = $transaction_details['data'][0];
			//data to be updated
			$transaction_data = array(
							'update_remarks' => $update_remarks, 'status' => strtolower($status_id),
							'updated_datetime' => db_current_datetime(), 'updated_by_id' => intval($this->entity_user_id)
			);
			$amount = ($transaction_details['data'][0]['amount']*$transaction_details['data'][0]['currency_conversion_rate']);//FORCE TO INR
			if (strtoupper($status_id) == 'ACCEPTED') {
				//Add to current balance and continue
				$domain_origin = $transaction_details['data'][0]['domain_list_fk'];
				//update balance details and notification
				$this->load->model('private_management_model');
				//passing negative so balance gets deducted before processing
				$transaction_owner_id = $transaction_details['data'][0]['user_oid'];
				
				//Saving to Transaction Log
				$currency = $transaction_details['data'][0]['currency'];
				$currency_conversion_rate = $transaction_details['data'][0]['currency_conversion_rate'];
				$tr_remarks = (empty($update_remarks) == false ? trim($update_remarks) : 'Amount Deposited');
				$agent_transaction_amount = -($amount);//Dont Change
				$this->save_transaction_details ( 'transaction', $system_request_id, $agent_transaction_amount, 0, 0, $tr_remarks, 0,0,$currency, $currency_conversion_rate, $transaction_owner_id);
				
				//Application Logger
				$user_id = $transaction_owner_id;
				$user_condition[] = array('user_id' ,'=', $user_id);
				$user_details = $this->user_model->get_user_details($user_condition);
				$agency_name = $user_details[0]['agency_name'];
				
				
				//Updating Agent Balnce with Debit Note
				if((!empty($type) == true) && ($type == 'Debit')){
					$response['data']['agent_balance'] = $this->private_management_model->update_b2b_debit_balance($transaction_owner_id, $amount);
					$remarks = 'Debit Request <span class="label label-success">'.strtoupper($status_id).'</span>:'.$amount.' '.get_application_default_currency().'('.$agency_name.')';
					$admin_user_id = $this->user_model->get_admin_user_id();
					$notification_users = array_merge($admin_user_id, array($user_id));
					$this->application_logger->balance_debit_request($remarks, array('system_transaction_id' => $system_request_id), $notification_users);
				}
				else{
					//Updating Agent Balance
					$response['data']['agent_balance'] = $this->private_management_model->update_b2b_balance($transaction_owner_id, $amount);
					$remarks = 'Deposit Request <span class="label label-success">'.strtoupper($status_id).'</span>:'.$amount.' '.get_application_default_currency().'('.$agency_name.')';
					$admin_user_id = $this->user_model->get_admin_user_id();
					$notification_users = array_merge($admin_user_id, array($user_id));
					$this->application_logger->balance_deposit_request($remarks, array('system_transaction_id' => $system_request_id), $notification_users);
				}
				$this->custom_db->update_record('master_transaction_details', $transaction_data, $transaction_details_cond);
				
				
				
			} elseif (strtoupper($status_id) != 'ACCEPTED') {
				$this->custom_db->update_record('master_transaction_details', $transaction_data, $transaction_details_cond);
			}
		} else {
			$response['status']	= FAILURE_STATUS;
		}
		return  $response;
	}
	/**
	 * Process Update Request
	 * @param number $origin
	 * @param string $system_request_id
	 * @param string $status_id
	 * @param string $update_remarks
	 *
	 * @return $response status of the update operation
	 */
	function process_credit_limit_request($origin, $system_request_id, $status_id, $update_remarks)
	{
		$response['status']	= SUCCESS_STATUS;
		$response['data']	= array();
		//get amount details to process - safety
		$transaction_details_cond = array('origin' => intval($origin), 'system_transaction_id' => $system_request_id, 'type' => 'b2b');
		//Depending on status update
		$transaction_details = $this->custom_db->single_table_records('master_transaction_details', '*', $transaction_details_cond);
		if (valid_array($transaction_details['data']) == true && strtoupper($transaction_details['data'][0]['status']) == 'PENDING') {
			$response['data'] = $transaction_details['data'][0];
			//data to be updated
			$transaction_data = array(
							'update_remarks' => $update_remarks, 'status' => strtolower($status_id),
							'updated_datetime' => db_current_datetime(), 'updated_by_id' => intval($this->entity_user_id)
			);
			$amount = ($transaction_details['data'][0]['amount']*$transaction_details['data'][0]['currency_conversion_rate']);//FORCE TO INR
			if (strtoupper($status_id) == 'ACCEPTED') {
				//Add to current balance and continue
				$domain_origin = $transaction_details['data'][0]['domain_list_fk'];
				//update balance details and notification
				$this->load->model('private_management_model');
				//passing negative so balance gets deducted before processing
				$transaction_owner_id = $transaction_details['data'][0]['user_oid'];
				
				//Saving to Transaction Log
				$currency = $transaction_details['data'][0]['currency'];
				$currency_conversion_rate = $transaction_details['data'][0]['currency_conversion_rate'];
				$tr_remarks = (empty($update_remarks) == false ? trim($update_remarks) : 'Amount Deposited');
				$agent_transaction_amount = ($amount);//Dont Change
				$this->save_transaction_details ( 'transaction', $system_request_id, $agent_transaction_amount, 0, 0, $tr_remarks, 0,0,$currency, $currency_conversion_rate, $transaction_owner_id);
				
				//Updating Agent Balance
				$response['data']['agent_balance'] = $this->private_management_model->update_b2b_credit_limit($transaction_owner_id, $amount);
				$this->custom_db->update_record('master_transaction_details', $transaction_data, $transaction_details_cond);
				
				//Application Logger
				$user_id = $transaction_owner_id;
				$user_condition[] = array('user_id' ,'=', $user_id);
				$user_details = $this->user_model->get_user_details($user_condition);
				$agency_name = $user_details[0]['agency_name'];
				$remarks = 'Credit Limit <span class="label label-success">'.strtoupper($status_id).'</span>:'.$amount.' '.get_application_default_currency().'('.$agency_name.')';
				$admin_user_id = $this->user_model->get_admin_user_id();
				$notification_users = array_merge($admin_user_id, array($user_id));
				$this->application_logger->credit_limit_request($remarks, array('system_transaction_id' => $system_request_id), $notification_users);
			} elseif (strtoupper($status_id) != 'ACCEPTED') {
				$this->custom_db->update_record('master_transaction_details', $transaction_data, $transaction_details_cond);
			}
		} else {
			$response['status']	= FAILURE_STATUS;
		}
		return  $response;
	}
	/**
	 * update domain balance details
	 * @param number $domain_origin	doamin unique key
	 * @param number $amount		amount to be added or deducted(-100 or +100)
	 */
	function update_domain_balance($origin, $amount)
	{
		$current_balance = 0;
		$cond = array('origin' => intval($origin));
              
		$details = $this->custom_db->single_table_records('b2b_user_details', 'balance', $cond);
                
		if ($details['status'] == true) {
			$details['data'][0]['balance'] = $current_balance = ($details['data'][0]['balance'] + $amount);
                        
			$this->custom_db->update_record('b2b_user_details', $details['data'][0], $cond);
		}
		return $current_balance;
	}
	/**
	 * Balu A
	 * B2B Agent Commission Details: Flight, Hotel, Bus
	 * @param $agent_fk
	 */
	function get_commission_details($agent_fk)
	{
		$response['status'] = FAILURE_STATUS;
		$response['data'] = array();
		
		$agent_details_query = 'select U.*,BUD.logo as agent_logo, UT.user_type
								FROM user AS U 
								INNER JOIN user_type AS UT ON U.user_type=UT.origin
								left JOIN b2b_user_details AS BUD ON U.user_id=BUD.user_oid 
								WHERE U.domain_list_fk ='.get_domain_auth_id().' and U.user_id = '.intval($agent_fk);
		$flight_commission_query = 'select BFCD.* from b2b_flight_commission_details as BFCD 
								inner join user as U on BFCD.agent_fk = U.user_id
								where BFCD.domain_list_fk ='.get_domain_auth_id().' and BFCD.agent_fk='.intval($agent_fk).' and BFCD.type="specific"'; 
		$bus_commission_query = 'select BBCD.* from b2b_bus_commission_details as BBCD 
								inner join user as U on BBCD.agent_fk = U.user_id
								where BBCD.domain_list_fk ='.get_domain_auth_id().' and BBCD.agent_fk='.intval($agent_fk).' and BBCD.type="specific"';

		$sightseeing_commission_query = 'select BSCD.* from b2b_sightseeing_commission_details as BSCD 
								inner join user as U on BSCD.agent_fk = U.user_id
								where BSCD.domain_list_fk ='.get_domain_auth_id().' and BSCD.agent_fk='.intval($agent_fk).' and BSCD.type="specific"';
						
		$transfer_commission_query = 'select BTCD.* from b2b_transfer_commission_details as BTCD 
								inner join user as U on BTCD.agent_fk = U.user_id
								where BTCD.domain_list_fk ='.get_domain_auth_id().' and BTCD.agent_fk='.intval($agent_fk).' and BTCD.type="specific"';


		$response['data']['agent_details']				= $this->db->query($agent_details_query)->row_array();
		$response['data']['flight_commission_details']	= $this->db->query($flight_commission_query)->row_array();;
		$response['data']['hotel_commission_details']	= '';
		$response['data']['bus_commission_details']		= $this->db->query($bus_commission_query)->row_array();

		$response['data']['sightseeing_commission_details'] = $this->db->query($sightseeing_commission_query)->row_array();

		$response['data']['transfer_commission_details'] = $this->db->query($transfer_commission_query)->row_array();

		
		if (valid_array($response['data']['agent_details']) == true) {
			$response['status'] = SUCCESS_STATUS;
		}
		return $response;
	}
	/**
	 * Balu A
	 * B2B Agent Commission Details: Flight, Bus
	 */
	function agent_commission_details($condition=array(), $count=false, $offset=0, $limit=100000000000)
	{
		$condition = $this->custom_db->get_custom_condition($condition);
		$response['status'] = FAILURE_STATUS;
		$response['data'] = array();
		if (!$count) {
			$agent_details_query = 'select U.*,BFCD.value as flight_commission_value,BFCD.api_value as flight_api_value,BFCD.value_type as flight_commission_type,
									BBCD.value as bus_commission_value,BBCD.api_value as bus_api_value,BBCD.value_type as bus_commission_type,

									BSCD.value as sightseeing_commission_value,BSCD.api_value as sightseeing_api_value,BSCD.value_type as sightseeing_commission_type,
									
									BTCD.value as transfer_commission_value,BTCD.api_value as transfer_api_value,BTCD.value_type as transfer_commission_type

									FROM user AS U 
									INNER JOIN user_type AS UT ON U.user_type=UT.origin
									left join b2b_flight_commission_details as BFCD on U.user_id=BFCD.agent_fk and BFCD.type="specific"
									left join b2b_bus_commission_details as BBCD on U.user_id=BBCD.agent_fk and BBCD.type="specific"
									left join b2b_sightseeing_commission_details as BSCD on U.user_id=BSCD.agent_fk and BSCD.type="specific"

									left join b2b_transfer_commission_details as BTCD on U.user_id=BTCD.agent_fk and BTCD.type="specific"



									WHERE U.user_type = '.B2B_USER.' and U.domain_list_fk ='.get_domain_auth_id().$condition.'
									ORDER BY U.user_id DESC limit '.$offset.', '.$limit;
			$response['data']['agent_commission_details']				= $this->db->query($agent_details_query)->result_array();
			return $response;
		} else {
			return $this->db->query('SELECT count(*) as total FROM user AS U, user_type AS UT, api_country_list AS ACL
				 WHERE U.user_type=UT.origin 
				 AND U.country_code=ACL.origin and U.domain_list_fk ='.get_domain_auth_id().$condition.' limit '.$limit.' offset '.$offset)->row();
		}
	}
	
	/**
	 * Balu A
	 * B2B Agent Commission Details: Flight, Bus,Sightseeing
	 * @param $search_filter_condition => (condition)
	 */
	function filter_agent_commission_details($search_filter_condition = '', $count=false, $offset=0, $limit=100000000000)
	{
		$response['status'] = FAILURE_STATUS;
		$response['data'] = array();
		if(empty($search_filter_condition) == false) {
			$search_filter_condition = ' and'.$search_filter_condition;
		}
		if (!$count) {
			$agent_details_query = 'select U.*,BFCD.value as flight_commission_value,BFCD.api_value as flight_api_value,BFCD.value_type as flight_commission_type,
									BBCD.value as bus_commission_value,BBCD.api_value as bus_api_value,BBCD.value_type as bus_commission_type,

									BSCD.value as sightseeing_commission_value,BSCD.api_value as sightseeing_api_value,BSCD.value_type as sightseeing_commission_type,

									BTCD.value as transfer_commission_value,BTCD.api_value as transfer_api_value,BTCD.value_type as transfer_commission_type


									FROM user AS U 
									INNER JOIN user_type AS UT ON U.user_type=UT.origin
									left join b2b_flight_commission_details as BFCD on U.user_id=BFCD.agent_fk and BFCD.type="specific"
									left join b2b_bus_commission_details as BBCD on U.user_id=BBCD.agent_fk and BBCD.type="specific"

									left join b2b_sightseeing_commission_details as BSCD on U.user_id=BSCD.agent_fk and BSCD.type="specific"

									left join b2b_transfer_commission_details as BTCD on U.user_id=BTCD.agent_fk and BTCD.type="specific"



									WHERE U.user_type = '.B2B_USER.' and U.domain_list_fk ='.get_domain_auth_id().$search_filter_condition.'
									ORDER BY U.user_id DESC limit '.$offset.', '.$limit;
			$response['data']['agent_commission_details']				= $this->db->query($agent_details_query)->result_array();
			return $response;
		} else {
			return $this->db->query('SELECT count(*) as total FROM user AS U, user_type AS UT, api_country_list AS ACL
				 WHERE U.user_type=UT.origin AND U.user_type = '.B2B_USER.' and U.domain_list_fk ='.get_domain_auth_id().' and  U.country_code=ACL.origin'.$search_filter_condition.' limit '.$limit.' offset '.$offset)->row();
		}
	}
	/**
	 * Balu A
	 * B2B Default Commission Details: Flight, Hotel, Bus,Sightseeing
	 * @param $agent_fk
	 */
	function default_commission_details()
	{
		$response['status'] = SUCCESS_STATUS;
		$response['data'] = array();
		
		$flight_commission_query = 'select BFCD.* from b2b_flight_commission_details as BFCD
								where BFCD.domain_list_fk ='.get_domain_auth_id().' and BFCD.type="generic"';
		$bus_commission_query = 'select BBCD.* from b2b_bus_commission_details as BBCD
								where BBCD.domain_list_fk ='.get_domain_auth_id().' and BBCD.type="generic"';

		$sightseeing_commission_query = 'select BSCD.* from b2b_sightseeing_commission_details as BSCD
								where BSCD.domain_list_fk ='.get_domain_auth_id().' and BSCD.type="generic"';

		$transfer_commission_query = 'select BTCD.* from b2b_transfer_commission_details as BTCD
								where BTCD.domain_list_fk ='.get_domain_auth_id().' and BTCD.type="generic"';
													

		$response['data']['flight_commission_details']	= $this->db->query($flight_commission_query)->row_array();;
		$response['data']['hotel_commission_details']	= '';
		$response['data']['bus_commission_details']		= $this->db->query($bus_commission_query)->row_array();
		$response['data']['sightseeing_commission_details'] = $this->db->query($sightseeing_commission_query)->row_array();
		$response['data']['transfer_commission_details'] = $this->db->query($transfer_commission_query)->row_array();

		return $response;
	}

	/**
	 * Shashikumar Misal
	 * Manage Commission for b2b domain
	 */
	function b2b_airline_commission()
	{
		$response['specific_markup_list'] = $this->specific_airline_commission();
		//$response['generic_markup_list'] = $this->generic_domain_markup('b2b_flight');
		$this->airline_markup = $response;
		return $response;
	}

	function  specific_airline_commission()
	{
		$sub_query = 'SELECT AL.origin 
		FROM airline_list AS AL 
		JOIN b2b_flight_commission_details AS BFCD ON
		AL.origin=BFCD.airline_id and BFCD.type="flight_specific"
		and BFCD.domain_list_fk != 0  and BFCD.domain_list_fk='.get_domain_auth_id();
		
		$query = 'SELECT AL.origin AS airline_origin, AL.name AS airline_name, AL.code AS 
		airline_code,	BFCD.origin AS com_origin, BFCD.type AS com_type, BFCD.airline_id, 
		BFCD.value, BFCD.api_value, BFCD.value_type FROM airline_list AS AL LEFT JOIN 
		b2b_flight_commission_details AS BFCD ON AL.origin=BFCD.airline_id and 
		BFCD.type="flight_specific" and BFCD.domain_list_fk != 0  and 
		BFCD.domain_list_fk='.get_domain_auth_id().' where (AL.has_specific_commission='.ACTIVE.'
		OR AL.origin in ('.$sub_query.')) order by AL.name ASC';
		$specific_data_list = $this->db->query($query)->result_array();
		//debug($query); exit;
		return $specific_data_list;
	}

	/**
	 * Balu A 
	 */
	function auto_suggest_agency_name($chars, $limit=15)
	{
		$query = 'select U.*
					FROM user AS U 
					INNER JOIN user_type AS UT ON U.user_type=UT.origin 
					WHERE U.agency_name!="" and U.domain_list_fk ='.get_domain_auth_id().' and 
					(U.uuid like "%'.$chars.'%" OR U.agency_name like "%'.$chars.'%" OR U.first_name like "%'.$chars.'%" OR U.last_name like "%'.$chars.'%" OR U.email like "%'.$chars.'%" OR U.phone like "%'.$chars.'%" )
					order by U.agency_name asc limit 0, '.$limit;
		return $this->db->query($query)->result_array();
	}
	/**
	 * Balu A
	 * Bank Account Details
	 */
	function bank_account_details() 
	{
		$query='SELECT BAD.*,CONCAT(U.first_name," ",U.last_name) as created_by_name 
		        FROM bank_account_details BAD
		        JOIN user U on U.user_id=BAD.created_by_id
		        where BAD.domain_list_fk='.get_domain_auth_id();
		$tmp_data = $this->db->query($query);
		if($tmp_data->num_rows()>0) {
			$tmp_data=$tmp_data->result_array();
			$data = array('status' => QUERY_SUCCESS, 'data' => $tmp_data);
		} else {
			$data = array('status' => QUERY_FAILURE);
		 }
		 return $data;
	}
	/**
	 * Update Balance of Agent
	 * @param number $amount Amount to be added or deducted
	 */
	function update_agent_balance($amount, $agent_user_id)
	{
		$current_balance = 0;
		$cond = array('user_oid' => intval($agent_user_id));
		$details = $this->custom_db->single_table_records('b2b_user_details', 'balance, due_amount', $cond);
		if ($details['status'] == true) {
			if($amount > 0 && $details ['data'] [0] ['due_amount'] < 0)
			{
				if(abs($details ['data'] [0] ['due_amount']) > $amount)
				{
					$details ['data'] [0] ['due_amount'] += $amount;
					$amount = 0;
				}
				if(abs($details ['data'] [0] ['due_amount']) < $amount)
				{
					$amount += $details ['data'] [0] ['due_amount'];
					$details ['data'] [0] ['due_amount'] = 0;
				}
			}
			$details ['data'] [0] ['balance'] = $current_balance = ($details ['data'] [0] ['balance'] + $amount);
                if ($details ['data'] [0] ['balance'] < 0) {
					$details ['data'] [0] ['due_amount'] += $details ['data'] [0] ['balance'];
                    $details ['data'] [0] ['balance'] = 0;
                }
                // debug($details);exit;
			// $details['data'][0]['balance'] = $current_balance = ($details['data'][0]['balance'] + $amount);
            $details ['data'][0]['updated_datetime'] = date('Y-m-d H:i:s');
			$this->custom_db->update_record('b2b_user_details', $details['data'][0], $cond);
			$this->balance_notification($current_balance);
		}
		return $current_balance;
	}
	/**
	 * Balu A
	 * if less than limit then send notification
	 */
	function balance_notification($current_balance)
	{
		$condition = array('agent_fk' => intval($this->entity_user_id));
		$details = $this->custom_db->single_table_records('agent_balance_alert_details', '*', $condition);
		if ($details['status'] == true) {
			$threshold_amount = $details['data'][0]['threshold_amount'];
			$mobile_number = trim($details['data'][0]['mobile_number']);
			$email_id = trim($details['data'][0]['email_id']);
			$enable_sms_notification = $details['data'][0]['enable_sms_notification'];
			$enable_email_notification = $details['data'][0]['enable_email_notification'];
			if($current_balance <= $threshold_amount) {
				//FIXME:Send Notification
				//SMS ALERT
				if($enable_sms_notification == ACTIVE && empty($mobile_number) == false) {
					//Send SMS Alert for Low Balance
				}
				//EMAIL NOTIFICATION
				if($enable_email_notification == ACTIVE && empty($email_id) == false) {
					//Send Email Notification for Low Balance
					$subject = $this->agency_name.'- Low Balance Alert';
					$message = 'Dear '.$this->entity_name.'<br/> <h1>Your Agent Balance is Low.</h1><br/><h2>Agent Balance as on '.date("Y-m-d h:i:sa").'is : '.COURSE_LIST_DEFAULT_CURRENCY_VALUE.' '.$threshold_amount.'/-</h2><h3>Please Recharge Your Account to enjoy UnInterrupted Bookings. :)</h3>';
					$this->load->library('provab_mailer');
					$mail_status = $this->provab_mailer->send_mail($email_id, $subject, $message);
				}
			}
		}
	}
	/**
	 * Save transaction logging for security purpose
	 * @param string $transaction_type
	 * @param string $app_reference
	 * @param number $fare
	 * @param number $domain_markup
	 * @param number $level_one_markup
	 * @param string $remarks
	 */
	function save_transaction_details($transaction_type, $app_reference, $fare, $domain_markup, $level_one_markup, $remarks, $convinence=0, $discount=0, $currency='INR', $currency_conversion_rate=1, $transaction_owner_id = 0, $is_paid_by_pg = 0, $credit_limit = 0)
	{	
		$transaction_owner_id = intval ( intval($transaction_owner_id) > 0 ? $transaction_owner_id : $this->entity_user_id);

		$transaction_log['system_transaction_id']	= date('Ymd-His').'-S-'.rand(1, 10000);
		$transaction_log['transaction_type']		= $transaction_type;
		$transaction_log['domain_origin']			= get_domain_auth_id();
		$transaction_log['app_reference']			= $app_reference;
		$transaction_log['fare']					= $fare;
		$transaction_log['level_one_markup']		= $level_one_markup;
		$transaction_log['domain_markup']			= $domain_markup;
		$transaction_log['remarks']					= $remarks;
		$transaction_log ['transaction_owner_id'] 	= $transaction_owner_id;
		$transaction_log['created_by_id']			= intval($this->entity_user_id) ;
		$transaction_log['created_datetime']		= date('Y-m-d H:i:s', time());
		
		$transaction_log['convinence_fees']			= $convinence;
		$transaction_log['promocode_discount']		= $discount;
		$transaction_log['currency']				= $currency;
		$transaction_log['currency_conversion_rate']= $currency_conversion_rate;
		$transaction_log['credit_limit'] 		= $credit_limit;

		//Opening and Closing Balance
		$total_transaction_amount = ($fare+$level_one_markup+$domain_markup);
		$opening_closing_balance_details = $this->get_opening_closing_balance($transaction_owner_id, $total_transaction_amount, $transaction_type);
		$transaction_log['opening_balance'] = $opening_closing_balance_details['opening_balance'];
		$transaction_log['closing_balance'] = $opening_closing_balance_details['closing_balance'];
		if($transaction_type == "credit_limit")
		{
			$transaction_log['credit_limit'] = $opening_closing_balance_details['total_transaction_amount'];
			$transaction_log['fare'] = 0;
		}
        $transaction_log['current_credit_limit'] = $opening_closing_balance_details['current_credit_limit'];
		
		$this->custom_db->insert_record('transaction_log', $transaction_log);
	}

	/**
	 * Get Opening and Closing Balance Details
	 */
	function get_opening_closing_balance($agent_id, $total_transaction_amount, $transaction_type="")
	{
		$total_transaction_amount = floatval($total_transaction_amount);
		//Get current agent balance
		$query = 'SELECT balance AS closing_balance, credit_limit, due_amount FROM b2b_user_details WHERE user_oid = '.intval($agent_id);
		$current_balance_details = $this->db->query($query)->row_array();

		if($transaction_type == "credit_limit")
		{
			$total_transaction_amount += $current_balance_details['credit_limit'];
		}

		$opening_balance = $current_balance_details['closing_balance']+$current_balance_details['due_amount'];
		$total_transaction_amount =	($total_transaction_amount) < 0 ? abs($total_transaction_amount) : -($total_transaction_amount);//if -Ve, convert to +Ve and ViceVersa
		$closing_balance = ($opening_balance+$total_transaction_amount);//Closing Balance
		if($transaction_type == "credit_limit")
		{
			$closing_balance = $opening_balance;
		}

		$current_credit_limit = $current_balance_details['credit_limit'];

		$data['opening_balance'] = round(floatval($opening_balance), 4);
		$data['closing_balance'] = round(floatval($closing_balance), 4);
		$data['total_transaction_amount'] = round(floatval($total_transaction_amount), 4);
		$data['current_credit_limit'] = round(floatval($current_credit_limit), 4);
		return $data;
	}

	/*
	 Get Agent Details 
	*/
	function get_agent_details($agent_id){

		$query = 'SELECT 
					B2B.balance AS balance,B2B.credit_limit, B2B.due_amount,
					U.agency_name AS agency_name,
					U.title AS title, 
					U.first_name AS first_name,
					U.last_name AS last_name,
					U.email AS email,
					U.phone AS phone,
					U.address AS address,
					CC.country AS agent_base_currency, 
					CC.id AS agent_base_currency_fk 
				FROM 
					b2b_user_details AS B2B 
				JOIN currency_converter CC ON CC.id = B2B.currency_converter_fk 
				JOIN user AS U ON U.user_id = B2B.user_oid 
				WHERE B2B.user_oid = '.intval($agent_id);

		$agent_details = $this->db->query($query)->row_array();
		return $agent_details;

	}

	/**
	 * Agent Transaction Log
	 * @param unknown_type $condition
	 * @param unknown_type $count
	 * @param unknown_type $offset
	 * @param unknown_type $limit
	 */
	public function agent_account_ledger($condition=array(), $count=false, $offset=0, $limit=100000000000)
	{
		$data = array();
		$condition = $this->custom_db->get_custom_condition($condition);
		$agent_filter = '';
		$transaction_activated_from_date = '2017-01-10';//DONT REMOVE THIS CONDITION
		$agent_filter = ' AND U.user_type ='.B2B_USER;	
		
		if($count){
			$query = 'select count(*) as total_records from transaction_log TL 
						join user U on U.user_id=TL.transaction_owner_id
						where TL.origin>0 and date(TL.created_datetime)>= "'.$transaction_activated_from_date.'" '.$agent_filter.' '.$condition;
			$total_records = $this->db->query($query)->row_array();
			$data['total_records'] = $total_records['total_records'];
		} else{
			// $query = 'SELECT U.agency_name,TL.*,
			//  CASE TL.transaction_type
			//    WHEN "flight" THEN 
			//    					(select concat("LeadPax:", PD.first_name," ",PD.last_name, " PNR: ",group_concat(distinct(FTD. pnr))) as REF from flight_booking_transaction_details FTD,flight_booking_passenger_details PD
			//    						WHERE FTD.app_reference = TL.app_reference and PD.app_reference = TL.app_reference 
			//    						group by FTD.app_reference)
			//    WHEN "hotel" THEN 
			//    					(select concat("LeadPax:", PD.first_name," ",PD.last_name, " Booking ID: ",HTD.booking_id," Booking Ref.: ",HTD.booking_reference) as REF from hotel_booking_details HTD,hotel_booking_pax_details PD 
			//    						WHERE HTD.app_reference = TL.app_reference and PD.app_reference = TL.app_reference 
			//    						group by HTD.app_reference)
			//    WHEN "bus" THEN 
			//    					(select concat("LeadPax:", PD.name, " PNR.: ",BTD.pnr) as REF from bus_booking_details BTD,bus_booking_customer_details PD 
			//    					WHERE BTD.app_reference = TL.app_reference and PD.app_reference = TL.app_reference 
			//    					group by BTD.app_reference)
			//    WHEN "transaction" THEN 
			//    					(SELECT concat("Amount ",MTD.amount) as REF FROM `master_transaction_details` MTD WHERE MTD.`system_transaction_id` = TL.app_reference group by MTD.system_transaction_id)
			//   END as "REF"
			// FROM
			// transaction_log TL 
			// join user U on U.user_id = TL.transaction_owner_id 
			// where 1=1 and date(TL.created_datetime)>= "'.$transaction_activated_from_date.'" '.$agent_filter.' '.$condition.' order by TL.created_datetime desc limit '.$offset.', '.$limit;
		 //  //echo $query;
		 // //exit;
			// $data['data'] = $this->db->query($query)->result_array();
			if(isset($condition) && !empty($condition)){
				$query1 = "SELECT t.*
				  FROM transaction_log as t
				  WHERE transaction_type != '' ".$condition." ORDER BY origin DESC" ;
			}else{
				$query1 = "SELECT t.*
				  FROM transaction_log as t
				  WHERE DATE(t.created_datetime) = CURDATE() ORDER BY origin DESC";
			}
		 $data = $this->db->query($query1)->result_array();
		}
		return $data;
	}

	/*
		Get All Active Agent List
	*/
	function agent_list(){

		$query = 'select user_id, agency_name, uuid FROM user WHERE status = '.ACTIVE.' AND user_type = '.B2B_USER.'
					order by user_id asc';
		$data = $this->db->query($query)->result_array();
		return $data;
	}

	/**
	 * Returns Agent Currency Conversion Rate
	 * @param string $agent_currency;EX: USD, INR
	 */
	public function get_currency_conversion_rate($currency)
	{
		$query = 'select value as conversion_rate from currency_converter where country="'.trim($currency).'"';
		return $this->db->query($query)->row_array();
	}

	/**
	 * save master transaction details request
	 * @param array $details
	 */
	function process_direct_credit_debit_transaction($details, $type='')
	{
		//SAVE TRANSACTION DETAILS
		$app_reference = trim($details['app_reference']);
		if(strlen($app_reference) >= 5 && strlen($app_reference) <= 20){
			$system_transaction_id = $app_reference;
		} else {
			$system_transaction_id = 'DEP-'.$this->entity_user_id.time();
		}
		$remarks = trim($details['remarks']);
		$master_transaction_details['system_transaction_id'] = $system_transaction_id;
		$master_transaction_details['domain_list_fk'] = get_domain_auth_id();
		$master_transaction_details['transaction_type'] = 'Wallet';
		$master_transaction_details['amount'] = $details['amount'];
		$master_transaction_details['currency'] = $details['currency'];
		$master_transaction_details['currency_conversion_rate'] = $details['currency_conversion_rate'];
		$master_transaction_details['date_of_transaction'] = db_current_datetime();
		$master_transaction_details['bank'] = 'N/A';
		$master_transaction_details['branch'] = 'N/A';
		$master_transaction_details['transaction_number'] = isset($details['transaction_number']) ? $details['transaction_number'] : 'N/A';
		$master_transaction_details['status'] = 'pending';
		$master_transaction_details['type'] = 'b2b';
		$master_transaction_details['user_oid'] = $details['agent_list_fk'];
		$master_transaction_details['remarks'] = $remarks;
		$master_transaction_details['created_datetime'] = db_current_datetime();
		$master_transaction_details['created_by_id'] = $this->entity_user_id;
		$master_transaction_details['image'] = '';

		//Set Default value due PDO connection
		$master_transaction_details['deposited_branch'] = '';
		
		$insert_id = $this->custom_db->insert_record('master_transaction_details', $master_transaction_details);
		
		//UPDATE AGENT BALANCE AND SAVE INTO TRANSACTION LOG
		$insert_id = $insert_id['insert_id'];
		$status_id = 'accepted';
		$update_remarks = '';
		$update_remarks .= $details['issued_for'].'<br/>';
		$update_remarks .='Reference: '.trim($details['app_reference']).'<br/>';
		$update_remarks .=$remarks;
		$this->process_balance_request($insert_id, $system_transaction_id, $status_id, $update_remarks, $type);
	}

	/** Offline Booking Form Start Here **/
	public function get_flight_suplier_source() {
        $query = 'select source_id,description from booking_source where meta_course_list_id="' . META_AIRLINE_COURSE . '"';
        $details = $this->db->query($query)->result();
        $source = array();
        foreach ($details as $val) {
            $source[] = array($val->source_id => $val->description);
        }
        $out = array();
        foreach ($source as $arr)
            foreach ($arr as $key => $val)
                $out[$key] = $val;
        return $out;
    }
    /**
	 * log access
	 *
	 * @param unknown $app_reference        	
	 * @param string $comment        	
	 */
	function create_track_log($app_reference, $comment = '', $log_data = array()) {
		$track_log ['app_reference'] = $app_reference;
		$track_log ['domain_origin'] = get_domain_auth_id ();
		$track_log ['http_host'] = @$_SERVER ['HTTP_HOST'];
		$track_log ['remote_address'] = @$_SERVER ['REMOTE_ADDR'];
		$track_log ['browser'] = @$_SERVER ['HTTP_USER_AGENT'];
		$track_log ['request_url'] = @$_SERVER ['REQUEST_URI'];
		$track_log ['request_method'] = @$_SERVER ['REQUEST_METHOD'];
		$track_log ['server_ip'] = @$_SERVER ['SERVER_ADDR'];
		$track_log ['server_name'] = @$_SERVER ['SERVER_NAME'];
		$track_log ['comments'] = $this->entity_uuid . ' - ' . $comment . ' - executed in ' . ENVIRONMENT;
		$track_log ['created_datetime'] = date ( 'Y-m-d H:i:s' );
		$track_log ['created_by_id'] = $this->entity_user_id;
		$track_log ['attr'] = serialize ( $_SERVER );
		$track_log ['log_data'] = json_encode ( $log_data );
		$this->custom_db->insert_record ( 'track_log', $track_log );
	}

	/**
	 * Phaneesh
	 */
	function update_transaction_payment_status($transaction_type, $app_reference, $status, $amount = 0) {
		$data = array (
				'payment_status' => $status 
		);
		$condition = array (
				'app_reference' => $app_reference 
		);
		switch ($transaction_type) {
			case 'flight' :
				$this->db->update ( 'flight_booking_transaction_details', $data, $condition );
				break;
			case 'hotel' :
				$this->db->update ( 'hotel_booking_itinerary_details', $data, $condition );
				break;
			case 'bus' :
				$this->db->update ( 'bus_booking_itinerary_details', $data, $condition );
				break;
			case 'train' :
				$this->db->update ( 'train_booking_details', $data, $condition );
				break;
			case 'visa' :
				$this->db->update ( 'visa_form_details', $data, $condition );
				break;
			case 'passport' :
				$this->db->update ( 'passport_form_details', $data, $condition );
				break;
			case 'cab' :
				$this->db->update ( 'cab_booking_form_details', $data, $condition );
				break;
			default :
				exit ( $transaction_type );
		}
		return $app_reference;
	}
    /** Offline Booking Form End Here **/
    function get_group_booking_request(){
    	$query = 'select * FROM group_request order by group_request_id desc';
		$data = $this->db->query($query)->result_array();
		return $data;
    }

    function get_agent_list(){
    	$query = 'select * FROM user WHERE user_type = '.B2B_USER;
		$data = $this->db->query($query)->result_array();
		return $data;
    }

    function formatted_agent_list(){

		$query = 'select user_id, agency_name FROM user WHERE status = '.ACTIVE.' AND user_type = '.B2B_USER.'
					order by user_id asc';
		$data = $this->db->query($query)->result_array();

		$source = array();
        foreach ($data as $val) {
            $source[] = array($val['user_id'] => $val['agency_name']);
        }
        $out = array();
        foreach ($source as $arr)
            foreach ($arr as $key => $val)
                $out[$key] = $val;
        return $out;
	}

	//
	/** Bus Offline Booking Form Start Here **/
	public function get_bus_suplier_source() {
        $query = 'select source_id,description from booking_source where meta_course_list_id="' . META_BUS_COURSE .'" AND booking_engine_status = 1';
        $details = $this->db->query($query)->result();
        $source = array();
        foreach ($details as $val) {
            $source[] = array($val->source_id => $val->description);
        }
        $out = array();
        foreach ($source as $arr)
            foreach ($arr as $key => $val)
                $out[$key] = $val;
        return $out;
    }
    public function get_active_modules(){
    	$query = 'SELECT MCL.*,GROUP_CONCAT(BS.name) AS supplier FROM meta_course_list AS MCL LEFT JOIN
    	booking_source AS BS ON BS.meta_course_list_id = MCL.course_id WHERE MCL.status=1 AND BS.booking_engine_status=1 GROUP BY MCL.course_id';
    	$details = $this->db->query($query)->result_array();
    	return $details;
    }

    /** Bus Offline Booking Form Start Here **/
	public function get_hotel_suplier_source() {
        $query = 'select source_id,description from booking_source where meta_course_list_id="' . META_ACCOMODATION_COURSE .'" AND booking_engine_status = 1';
        $details = $this->db->query($query)->result();
        $source = array();
        foreach ($details as $val) {
            $source[] = array($val->source_id => $val->description);
        }
        $out = array();
        foreach ($source as $arr)
            foreach ($arr as $key => $val)
                $out[$key] = $val;
        return $out;
    }
    public function get_groups(){
    	$query = 'SELECT * FROM user_groups';
    	$result = $this->db->query($query)->result_array();
    	return $result;
    }
    public function get_supllier_list($module,$group_fk=0){
    	$query = 'SELECT * FROM booking_source AS BS where BS.meta_course_list_id = "'.$module.'" AND BS.booking_engine_status = 1';
    	$result = $this->db->query($query)->result_array();
    	return $result;
    }
    public function get_bitla_operator_group($module, $group_fk){
    	$query = 'SELECT * FROM booking_source WHERE meta_course_list_id="VHCID1433498307" AND travel_id>0';
    	$result = $this->db->query($query)->result_array();
    	$bitla_operator = array();
    	foreach($result as $res => $val){
    		$bitla_operator[$res]['operator_details'] = $val;
    		$bquery = 'SELECT * FROM b2b_bus_commission_details WHERE group_fk="'.$group_fk.'" AND booking_source_origin = "'.$val['origin'].'"';
    		$bresult = $this->db->query($bquery)->result_array();
    		$bitla_operator[$res]['commission_details'] = $bresult;
    	}
    	return $bitla_operator;
    }
    public function get_bitla_operator_agent($module, $user_oid){
    	$query = 'SELECT * FROM booking_source WHERE meta_course_list_id="VHCID1433498307" AND travel_id>0';
    	$result = $this->db->query($query)->result_array();
    	$bitla_operator = array();
    	foreach($result as $res => $val){
    		$bitla_operator[$res]['operator_details'] = $val;
    		$bquery = 'SELECT * FROM b2b_bus_commission_details WHERE agent_fk="'.$user_oid.'" AND booking_source_origin = "'.$val['origin'].'"';
    		$bresult = $this->db->query($bquery)->result_array();
    		$bitla_operator[$res]['commission_details'] = $bresult;
    	}
    	return $bitla_operator;
    }
    public function get_bitla_operator(){
    	$query = 'SELECT * FROM booking_source WHERE meta_course_list_id="VHCID1433498307" AND travel_id>0 AND is_bitla_direct = 0';
    	$result = $this->db->query($query)->result_array();
    	$bitla_operator = array();
    	foreach($result as $res => $val){
    		$bitla_operator[$res]['operator_details'] = $val;
    		$bquery = 'SELECT * FROM b2b_bus_commission_details WHERE is_master="1" AND booking_source_origin = "'.$val['origin'].'"';
    		$bresult = $this->db->query($bquery)->result_array();
    		$bitla_operator[$res]['commission_details'] = $bresult;
    	}
    	return $bitla_operator;
    }
    public function get_commission($table_name, $booking_source_origin, $group_fk, $for_supp=0){
    	if($for_supp){
    		$extra_cond = " AND operator_name is NULL";
    	}
    	else{
    		$extra_cond = "";
    	}

    	$query = 'SELECT * FROM '.$table_name.' WHERE booking_source_origin='.$booking_source_origin.' AND type="specific" AND group_fk = '.$group_fk.$extra_cond;
    	$result = $this->db->query($query)->result_array();
    	return $result;	
    }
    public function get_master_commission($table_name, $booking_source_origin){
    	if($table_name == 'b2b_bus_commission_details'){
    		$query = 'SELECT * FROM '.$table_name.' WHERE booking_source_origin='.$booking_source_origin.' AND type="specific" AND is_master = "1" AND operator_name IS NULL';	
    	}else{
    		$query = 'SELECT * FROM '.$table_name.' WHERE booking_source_origin='.$booking_source_origin.' AND type="specific" AND is_master = "1"';	
    	}
    	
    	$result = $this->db->query($query)->result_array();
    	return $result;	
    }

    public function get_supplier_commission($table_name, $booking_source_origin, $user_oid){
    	$query = 'SELECT * FROM '.$table_name.' WHERE booking_source_origin='.$booking_source_origin.' AND type="specific" AND agent_fk = '.$user_oid.'';
    	$result = $this->db->query($query)->result_array();
    	return $result;	
    }


    function get_airport_list(){
    	$query = 'SELECT * FROM  flight_airport_list';
    	$result = $this->db->query($query)->result_array();
    	return $result;	
    }

    function get_airline_list(){
    	$query = 'SELECT * FROM  airline_list';
    	$result = $this->db->query($query)->result_array();
    	return $result;	
    }
    
    function get_busstation_list(){
    	$query = 'SELECT origin,ets_city_name FROM  bus_stations_new1';
    	$result = $this->db->query($query)->result_array();
    	return $result;	
    }
    function get_hotel_city_list(){
    	$query = 'SELECT * FROM  all_api_hotel_cities';
    	$result = $this->db->query($query)->result_array();
    	return $result;	
    }

    function get_active_agent(){
    	$query = 'SELECT * FROM  user WHERE user_type = 3 AND status = 1';
    	$result = $this->db->query($query)->result_array();
    	return $result;		
    }
    function get_commissioned_airline($group_fk, $trip_type, $bs_origin){

    	$query = 'SELECT FCD.*, AL.origin AS al_origin,AL.name,AL.code FROM b2b_flight_commission_details AS FCD LEFT JOIN airline_list AS AL ON FCD.airline_id = AL.origin WHERE FCD.group_fk='.$group_fk.' AND (FCD.airline_id > 0 ) AND trip_type = "'.$trip_type.'" AND booking_source_origin = '.$bs_origin.'';
    	$result = $this->db->query($query)->result_array();
    	return $result;		
    }
    function get_commissioned_airline_master($trip_type, $bs_origin){
    	$query = 'SELECT FCD.*, AL.origin AS al_origin,AL.name,AL.code FROM b2b_flight_commission_details AS FCD LEFT JOIN airline_list AS AL ON FCD.airline_id = AL.origin WHERE FCD.is_master = "1" AND (FCD.airline_id > 0 ) AND trip_type = "'.$trip_type.'" AND booking_source_origin = '.$bs_origin.'';
    	$result = $this->db->query($query)->result_array();
    	
    	return $result;		
    }
    function get_commissioned_airline_agent($agent_fk, $trip_type){

    	$query = 'SELECT FCD.*, AL.origin AS al_origin,AL.name,AL.code FROM b2b_flight_commission_details AS FCD LEFT JOIN airline_list AS AL ON FCD.airline_id = AL.origin WHERE FCD.agent_fk='.$agent_fk.' AND (FCD.airline_id > 0 ) AND trip_type = "'.$trip_type.'"';
    	$result = $this->db->query($query)->result_array();
    	return $result;		
    }
    function get_commissioned_operator($group_fk){
    	$query = 'SELECT * FROM b2b_bus_commission_details WHERE group_fk ='.$group_fk.' AND operator_name != ""';
    	$result = $this->db->query($query)->result_array();
    	return $result;
    }
    function get_commissioned_operator_master(){
    	$query = 'SELECT * FROM b2b_bus_commission_details WHERE is_master = "1" AND operator_name != ""';
    	$result = $this->db->query($query)->result_array();
    	return $result;
    }
    function get_master_commissioned_operator(){
    	$query = 'SELECT * FROM b2b_bus_commission_details WHERE is_master = "1" AND operator_name != ""';
    	$result = $this->db->query($query)->result_array();
    	return $result;
    }
    function get_commissioned_operator_agent($agent_fk){
    	$query = 'SELECT * FROM b2b_bus_commission_details WHERE agent_fk ='.$agent_fk.' AND operator_name != ""';
    	$result = $this->db->query($query)->result_array();
    	return $result;
    }


    //Executive - Agent amount collection

    function amount_collection_list($data_list_filt = '')
	{
		if(empty($data_list_filt)){
			$data_list_filt = array();
			$data_list_filt[] = array('date(AAC.created_date)', '>=', $this->db->escape(db_current_datetime(date('Y-m-d 00:00:00'))));
			$data_list_filt[] = array('date(AAC.created_date)', '<=', $this->db->escape(db_current_datetime(date('Y-m-d 23:59:59'))));
		}
		$data_list_cond = '';
		if (valid_array($data_list_filt) == true) {
			$data_list_cond = $this->custom_db->get_custom_condition($data_list_filt);
		}
		$query = "select *,AAC.status as doc_status,(select first_name from user as us where us.user_id = AAC.created_by) as collected_by, (select CONCAT(first_name, ' ', last_name) from user as us where us.user_id = AAC.updated_by) as updated_by from agent_amount_collection AAC, user U where 
		AAC.agent_id=U.user_id ".$data_list_cond." order by AAC.origin DESC";
		return $this->db->query($query)->result_array();
	}

	//All active supplier list
	public function get_all_suplliers($mcl_id = 0, $all = 0){
		if($mcl_id)
    	{
    		$query = "SELECT * FROM booking_source AS BS where BS.booking_engine_status = 1 AND BS.meta_course_list_id = '".$mcl_id."' ORDER BY BS.meta_course_list_id ASC";
    	}
    	else
    	{
    		$query = "SELECT * FROM booking_source AS BS where BS.booking_engine_status = 1 ORDER BY BS.meta_course_list_id ASC";
    	}
    	$result = $this->db->query($query);
    	//echo $this->db->last_query(); exit;
    	if($all)
    		return $result->result_array();
    	else
    		$result = $result->result();
    	$suppliers = array();
        foreach ($result as $val) {
            $suppliers[] = array($val->source_id => $val->description);
        }

        $out = array();
        foreach ($suppliers as $arr)
            foreach ($arr as $key => $val)
                $out[$key] = $val;
        return $out;
        
        return $out;
    }
	
	//All supplier list
	public function get_all_active_inactive_suplliers($mcl_id = 0, $all = 0){
		if($mcl_id)
    	{
    		$query = "SELECT * FROM booking_source AS BS where BS.meta_course_list_id = '".$mcl_id."' ORDER BY BS.meta_course_list_id ASC";
    	}
    	else
    	{
    		$query = "SELECT * FROM booking_source AS BS ORDER BY BS.meta_course_list_id ASC";
    	}
    	$result = $this->db->query($query);
    	//echo $this->db->last_query(); exit;
    	if($all)
    		return $result->result_array();
    	else
    		$result = $result->result();
    	$suppliers = array();
        foreach ($result as $val) {
            $suppliers[] = array($val->source_id => $val->description);
        }

        $out = array();
        foreach ($suppliers as $arr)
            foreach ($arr as $key => $val)
                $out[$key] = $val;
        return $out;
        
        return $out;
    }
	
    function get_common_booking_details($get_data)
    {
    	$from_date 		= $get_data['created_datetime_from'];
    	$to_date   		= $get_data['created_datetime_to'];
    	$pnr   			= $get_data['pnr'];
    	$app_reference  = $get_data['app_reference'];
    	$agent_id   	= $get_data['created_by_id'];
    	$module   		= isset($get_data['module'])?$get_data['module']:'All';
    	$from_date_query_flight = '';
    	$from_date_query_bus = '';
    	$from_date_query_hotel = '';
    	$to_date_query_flight = '';
    	$to_date_query_bus = '';
    	$to_date_query_hotel = '';
    	$pnr_query_flight = '';
    	$pnr_query_bus = '';
    	$pnr_query_hotel = '';
    	$app_reference_query_flight = '';
    	$app_reference_query_bus = '';
    	$app_reference_query_hotel = '';
    	if(isset($from_date) && !empty($from_date)){
    		$from_date = date('Y-m-d 00:00:00',strtotime($from_date));
    		$from_date_query_flight = "AND fbd.created_datetime>='".$from_date."'";
    		$from_date_query_bus = "AND bbd.created_datetime>='".$from_date."'";
    		$from_date_query_hotel = "AND hbd.created_datetime>='".$from_date."'";
    		$from_date_query_acc = "AND mtd.created_datetime>='".$from_date."'";
    	}
    	if(isset($to_date) && !empty($to_date)){
    		$to_date = date('Y-m-d 23:59:59',strtotime($to_date));
    		$to_date_query_flight = " AND fbd.created_datetime<='".$to_date."'";
    		$to_date_query_bus = " AND bbd.created_datetime<='".$to_date."'";
    		$to_date_query_hotel = " AND hbd.created_datetime<='".$to_date."'";
    		$to_date_query_acc = " AND mtd.created_datetime<='".$to_date."'";
    	}
    	if(isset($pnr) && !empty($pnr)){
    		$pnr_query_flight = " AND fbtd.pnr LIKE '%".$pnr."%'";
    		$pnr_query_bus = " AND bbd.pnr LIKE '%".$pnr."%'";
    		$pnr_query_hotel = " AND hbd.pnr LIKE '%".$pnr."%'";
    		$pnr_query_acc = " AND mtd.system_transaction_id LIKE '%".$pnr."%'";
    	}
    	if(isset($app_reference) && !empty($app_reference)){
    		$app_reference_query_flight = " AND fbd.app_reference='".$app_reference."'";
    		$app_reference_query_bus = " AND bbd.app_reference='".$app_reference."'";
    		$app_reference_query_hotel = " AND hbd.app_reference='".$app_reference."'";
    		$app_reference_query_acc = " AND mtd.system_transaction_id='".$app_reference."'";
    	}
    	if(isset($agent_id) && !empty($agent_id)){
    		$agent_id_query_flight = " AND fbd.created_by_id='".$agent_id."'";
    		$agent_id_query_bus = " AND bbd.created_by_id='".$agent_id."'";
    		$agent_id_query_hotel = " AND hbd.created_by_id='".$agent_id."'";
    		$agent_id_query_acc = " AND mtd.user_oid='".$agent_id."'";
    	}
    	$mr_broaker = " UNION ";
    	$flag = 0;
    	if($module == 'All' || $module == 'flight'){
    		$flight_sql = "SELECT fbd.app_reference,fbd.booking_billing_type AS booking_billing_type,fbtd.pnr AS sup_pnr,fbtd.total_fare AS total_fare, agency_name, uuid, fbd.created_datetime, fbd.journey_start, SUM(admin_commission) AS admin_commission, SUM(admin_tds) AS admin_tds, SUM(agent_commission) AS agent_commission, SUM(agent_tds) AS agent_tds, SUM(admin_markup) AS admin_markup, SUM(agent_markup) AS agent_markup,'Flight',fbd.status,fbd.booking_source FROM flight_booking_transaction_details fbtd, flight_booking_details fbd, user u WHERE fbd.app_reference=fbtd.app_reference AND u.user_id = fbd.created_by_id ".$from_date_query_flight."".$to_date_query_flight."".$pnr_query_flight."".$app_reference_query_flight."".$agent_id_query_flight." GROUP BY app_reference";
    		$sql = $flight_sql;
    		$flag = 1;
    	}
    	if($module == 'All' || $module == 'Bus'){
	    	$bus_sql = "SELECT bbd.app_reference,bbd.booking_billing_type AS booking_billing_type,bbd.pnr AS sup_pnr,SUM(bbcd.fare) AS total_fare, agency_name, uuid, bbd.created_datetime, bbid.journey_datetime, SUM(admin_commission), SUM(admin_tds), SUM(agent_commission), SUM(agent_tds), SUM(admin_markup), SUM(agent_markup),'Bus',bbd.status,bbd.booking_source FROM bus_booking_customer_details bbcd, bus_booking_details bbd, user u, bus_booking_itinerary_details bbid WHERE bbd.app_reference=bbcd.app_reference AND bbd.app_reference=bbid.app_reference AND u.user_id = bbd.created_by_id ".$from_date_query_bus."".$to_date_query_bus."".$pnr_query_bus."".$app_reference_query_bus."".$agent_id_query_bus." GROUP BY app_reference";
	    	if($flag == 1){
	    		$sql .= $mr_broaker.$bus_sql;
	    	}else{
	    		$sql = $bus_sql;
	    	}
	    	$flag = 1;
	    }
	    if($module == 'All' || $module == 'Accounting'){
	    	$acc_sql = "SELECT mtd.system_transaction_id as app_reference,mtd.transaction_type AS booking_billing_type,mtd.system_transaction_id AS sup_pnr,SUM(mtd.amount) AS total_fare, agency_name, uuid, mtd.created_datetime, mtd.updated_datetime AS journey_datetime, 'admin_commission', 'admin_tds', 'agent_commission', 'agent_tds','admin_markup', 'agent_markup','Recharge' AS Flight,mtd.status,mtd.pg_name AS booking_source FROM master_transaction_details mtd, user u WHERE u.user_id = mtd.user_oid AND mtd.status = 'accepted' AND mtd.transaction_type != 'Wallet' AND mtd.transaction_type != 'Instant_Recharge'  ".$from_date_query_acc."".$to_date_query_acc."".$pnr_query_acc."".$app_reference_query_acc."".$agent_id_query_acc." GROUP BY system_transaction_id";
	    	if($flag == 1){
	    		$sql .= $mr_broaker.$acc_sql;
	    	}else{
	    		$sql = $acc_sql;
	    	}
	    	$flag = 1;
	    }
    	// $hotel_sql = "SELECT hbd.app_reference, agency_name, uuid, hbd.created_datetime, hbd.hotel_check_in, '', SUM(convinence_amount) AS convinence_amount, '', '', '', '', SUM(admin_markup), SUM(agent_markup), SUM(gst) AS gst,'Hotel',hbd.status,hbd.booking_source FROM hotel_booking_details hbd, user u, hotel_booking_itinerary_details hbid WHERE hbd.app_reference = hbid.app_reference AND u.user_id = hbd.created_by_id ".$from_date_query_hotel." ".$to_date_query_hotel." ".$pnr_query_hotel." ".$app_reference_query_hotel." GROUP BY app_reference";

    	//$sql = $flight_sql.$mr_broaker.$bus_sql.$mr_broaker.$hotel_sql;
    	// if(isset($flight_sql) && !empty($flight_sql)){
    		
    	// }
    	// $sql = $flight_sql.$mr_broaker.$bus_sql;
    	$query = $this->db->query($sql);
    	return $query->result_array();
    }

    public function get_update_deposite_notification()
    {       
    	$SqlInfo= "UPDATE master_transaction_details SET notification= 1 WHERE notification= 0";
    	$this->db->query($SqlInfo);
    }

    //================= Shrikant =====================
    public function get_flight_transaction_details($app_reference, $extra_price)
    {
    	$sql = 'SELECT * FROM flight_booking_transaction_details where app_reference="'.$app_reference.'"';
    	$query = $this->db->query($sql);
    	$resp = $query->result_array();

    	if(!empty($resp)){
    		$total_fare = $resp[0]['total_fare'];
    		$updated_amt = $total_fare + $extra_price;

    		$update_sql = 'UPDATE flight_booking_transaction_details SET total_fare = '.$updated_amt.' WHERE app_reference="'.$app_reference.'"';
    		$this->db->query($update_sql);
    	}
    }
    //================================================
    function get_user_list(){
    	$query = 'select * FROM user';
		$data = $this->db->query($query)->result_array();
		return $data;
    }

    function master_transaction_request_list1($type='provab', $data_list_filt=array(), $credit='')
	{
		$data_list_cond = '';
		if (valid_array($data_list_filt) == true) {
			$data_list_cond = $this->custom_db->get_custom_condition($data_list_filt);
		}
		if(empty($data_list_filt)){
			$query = 'select MTD.*, CONCAT(U.first_name, " ", U.last_name) request_user, U.email,U.phone, U.agency_name AS requested_from from master_transaction_details MTD, user U
			where MTD.user_oid=U.user_id AND DATE(MTD.created_datetime) = CURDATE() AND MTD.type="'.$type.'" and MTD.domain_list_fk = '.get_domain_auth_id().' '.$data_list_cond.'
			and MTD.transaction_type !="Credit" order by MTD.updated_datetime DESC, MTD.created_datetime DESC';
		}else{
				$query = 'select MTD.*, CONCAT(U.first_name, " ", U.last_name) request_user, U.email,U.phone, U.agency_name AS requested_from from master_transaction_details MTD, user U
			where MTD.user_oid=U.user_id AND MTD.type="'.$type.'" and MTD.domain_list_fk = '.get_domain_auth_id().' '.$data_list_cond.'
			and MTD.transaction_type !="Credit" order by MTD.updated_datetime DESC, MTD.created_datetime DESC';
		}
		$data['list'] = $this->db->query($query)->result_array();
		return $data;
	}
}
