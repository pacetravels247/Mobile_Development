<?php
require_once 'abstract_management_model.php';
/**
 * @package    Provab Application
 * @subpackage Travel Portal
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V2
 */
Class Private_Management_Model extends Abstract_Management_Model
{
	function __construct() {
		parent::__construct('level_1');
	}

	/**
	 * Balu A
	 * Manage domain markup for provab - Domain wise and module wise
	 */
	function airline_domain_markup()
	{
		$response['data'] = '';
		$response['data']['specific_markup_list'] = $this->specific_domain_markup('b2c_flight');
		$response['data']['generic_markup_list'] = $this->generic_domain_markup('b2c_flight');
		return $response;
	}

	/**
	 * Balu A
	 * Manage domain markup for provab - Domain wise and module wise
	 */
	function hotel_domain_markup()
	{
		$response['data'] = '';
		$response['data']['specific_markup_list'] = $this->specific_domain_markup('b2c_hotel');
		$response['data']['generic_markup_list'] = $this->generic_domain_markup('b2c_hotel');
		return $response;
	}

	/**
	 * Elavarasi
	 * Manage domain markup for provab - Domain wise and module wise
	 */
	function transfer_domain_markup()
	{
		$response['data'] = '';
		$response['data']['specific_markup_list'] = $this->specific_domain_markup('b2c_transferv1');
		$response['data']['generic_markup_list'] = $this->generic_domain_markup('b2c_transferv1');
		return $response;
	}
	/**
	 * Elavarasi
	 * Manage domain markup for provab - Domain wise and module wise
	 */
	function sightseeing_domain_markup()
	{
		$response['data'] = '';
		$response['data']['specific_markup_list'] = $this->specific_domain_markup('b2c_sightseeing');
		$response['data']['generic_markup_list'] = $this->generic_domain_markup('b2c_sightseeing');
		return $response;
	}
	/**
	 * Balu A
	 * Manage domain markup for provab - Domain wise and module wise
	 */
	function bus_domain_markup()
	{
		$response['data'] = '';
		$response['data']['specific_markup_list'] = $this->specific_domain_markup('b2c_bus');
		$response['data']['generic_markup_list'] = $this->generic_domain_markup('b2c_bus');
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
		$query = 'SELECT ML.origin AS markup_origin, ML.type AS markup_type, ML.reference_id, ML.value, ML.value_type
		FROM markup_list AS ML where ML.module_type = "'.$module_type.'" and
		ML.markup_level = "'.$this->markup_level.'" and ML.type="generic" and ML.domain_list_fk=0';
		$generic_data_list = $this->db->query($query)->result_array();
		return $generic_data_list;
	}

	/**
	 * Balu A
	 * Get specific markup based on module type
	 * @param string $module_type	Name of the module for which the markup has to be returned
	 * @param string $markup_level	Level of markup
	 */
	function specific_domain_markup($module_type)
	{
		$query = 'SELECT DL.domain_logo AS domain_logo, DL.origin AS domain_origin, DL.domain_name, DL.domain_key, DL.status AS domain_status,
		ML.origin AS markup_origin, ML.type AS markup_type, ML.reference_id, ML.value, ML.value_type
		FROM domain_list AS DL LEFT JOIN markup_list AS ML ON
		ML.module_type = "'.$module_type.'" and ML.markup_level = "'.$this->markup_level.'" and DL.origin=ML.domain_list_fk and ML.type="specific"
		and ML.domain_list_fk != 0 order by DL.created_datetime DESC';
		$specific_data_list = $this->db->query($query)->result_array();
		return $specific_data_list;
	}

	/**
	 * Master Transaction Request List
	 */
	function master_transaction_request_list($type='provab')
	{
		$query = 'select DL.domain_name AS domain_name, DL.domain_logo AS domain_logo, MTD.system_transaction_id, MTD.transaction_type,
		MTD.amount,MTD.image, MTD.status, MTD.created_datetime, MTD.conversion_value, MTD.origin, MTD.update_remarks, MTD.updated_datetime,
		U.first_name, U.email, CONCAT(U.country_code,U.phone) as phone,
		CC.country 
		from master_transaction_details AS MTD, domain_list AS DL, user AS U, currency_converter AS CC
		where MTD.type='.$this->db->escape($type).' AND MTD.domain_list_fk=DL.origin and MTD.currency_converter_origin=CC.id and U.user_id=MTD.created_by_id
		order by MTD.status DESC, MTD.origin DESC'; 
		
	
		return $this->db->query($query)->result_array();
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
	function process_balance_request($origin, $system_request_id, $status_id, $update_remarks)
	{
		$response['status']	= SUCCESS_STATUS;
		$response['data']	= array();
		//get amount details to process - safety
		$transaction_details_cond = array('origin' => intval($origin), 'system_transaction_id' => $system_request_id, 'type' => 'provab');
		//Depending on status update
		$transaction_details = $this->custom_db->single_table_records('master_transaction_details', '*', $transaction_details_cond);
		if (valid_array($transaction_details['data']) == true && strtoupper($transaction_details['data'][0]['status']) == 'PENDING') {
			$response['data'] = $transaction_details['data'][0];
			if (strtoupper($status_id) == 'ACCEPTED') {
				//Add to current balance and continue
				$amount = ($transaction_details['data'][0]['amount']*$transaction_details['data'][0]['conversion_value']);//FORCE TO INR
				$domain_origin = $transaction_details['data'][0]['domain_list_fk'];
				//update balance details and notification
				$response['data']['current_balance'] = $this->update_domain_balance($domain_origin, $amount);
			}
			//data to be updated
			$transaction_data = array(
							'update_remarks' => $update_remarks, 'status' => strtolower($status_id),
							'updated_datetime' => db_current_datetime(), 'updated_by_id' => intval($this->entity_user_id)
			);
			$this->custom_db->update_record('master_transaction_details', $transaction_data, $transaction_details_cond);
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
	function update_domain_balance($domain_origin, $amount)
	{
		$current_balance = 0;
		$cond = array('origin' => intval($domain_origin));
		$details = $this->custom_db->single_table_records('domain_list', 'balance', $cond);
		if ($details['status'] == true) {
			$details['data'][0]['balance'] = $current_balance = ($details['data'][0]['balance'] + $amount);
			$this->custom_db->update_record('domain_list', $details['data'][0], $cond);
		}
		return $current_balance;
	}
	
	/**
	 * update domain balance details
	 * @param number $domain_origin	doamin unique key
	 * @param number $amount		amount to be added or deducted(-100 or +100)
	 */
	function update_b2b_balance($origin, $amount)
	{
		$current_balance = 0;
		$cond = array('user_oid' => intval($origin));
		$details = $this->custom_db->single_table_records('b2b_user_details', 'balance,due_amount,credit_limit', $cond);
		if ($details['status'] == true) {
			// $details['data'][0]['balance'] = $current_balance = ($details['data'][0]['balance'] + $amount);
			
			if ($details['data'][0]['due_amount'] < 0) {
                $TotalDueAmount = $details ['data'] [0] ['due_amount'] + $details ['data'] [0] ['balance'] + $amount;
               
                if ($TotalDueAmount < 0) {
                    $details ['data'] [0] ['due_amount'] = $TotalDueAmount;
                    $BalanceToAdded = 0;
                    $TotalDueAmount = 0;
                } else {
                    $details ['data'] [0] ['due_amount'] = 0;
                    $BalanceToAdded = $TotalDueAmount;
                }
            } else {

                $BalanceToAdded = $amount + $details ['data'] [0] ['balance'];
            }
           	$details ['data'] [0] ['balance'] = $current_balance = ($BalanceToAdded);
			$this->custom_db->update_record('b2b_user_details', $details['data'][0], $cond);
		}
		return $current_balance;
	}
	/**
	 * update domain balance details
	 * @param number $domain_origin	doamin unique key
	 * @param number $amount		amount to be added or deducted(-100 or +100)
	 */
	function update_b2b_debit_balance($origin, $amount)
	{
		// echo $amount;exit;
		$current_balance = 0;
		$cond = array('user_oid' => intval($origin));
		$details = $this->custom_db->single_table_records('b2b_user_details', 'balance,due_amount,credit_limit', $cond);
		if ($details['status'] == true) {
			// $details['data'][0]['balance'] = $current_balance = ($details['data'][0]['balance'] + $amount);
			
			if ($details['data'][0]['due_amount'] < 0) {
               $details ['data'] [0] ['due_amount'] += $amount;
               $details ['data'] [0] ['balance'] = 0;
            } else {
            	if($details ['data'] [0] ['balance'] <= 0){
            		$details ['data'] [0] ['due_amount'] += $amount;
            	}
            	else{
            		$TotalDueAmount = $details ['data'] [0] ['balance'] + $amount;
            		if($TotalDueAmount > 0){
            			$details ['data'] [0] ['balance'] = $TotalDueAmount;
            		}
            		else{
            			$details ['data'] [0] ['balance'] = 0;
            			$details ['data'] [0] ['due_amount'] = $TotalDueAmount;
            		}
            	}
				$BalanceToAdded = $amount + $details ['data'] [0] ['balance'];
            }
            // debug($details);exit;
           
			$this->custom_db->update_record('b2b_user_details', $details['data'][0], $cond);
		}
		return $current_balance;
	}
	/**
	 * update domain credit limit details
	 * @param number $domain_origin	doamin unique key
	 * @param number $amount    amount to be added
	 */
	function update_b2b_credit_limit($origin, $amount)
	{
		$current_balance = 0;
		$cond = array('user_oid' => intval($origin));
		$details = $this->custom_db->single_table_records('b2b_user_details', 'credit_limit', $cond);
		if ($details['status'] == true) {
			$details['data'][0]['credit_limit'] = $current_credit_limit = ($details['data'][0]['credit_limit'] + $amount);
			$this->custom_db->update_record('b2b_user_details', $details['data'][0], $cond);
		}
		return $current_credit_limit;
	}

	/**
	 *
	 */
	function event_logs($condition=array(), $count=false, $offset=0, $limit=100000000000)
	{
		//BT, CD, ID
		if ($count) {
			$query = 'select count(*) as total_records from exception_logger';
			$data = $this->db->query($query)->row_array();
			return $data['total_records'];
		} else {
			$query = 'select * from exception_logger order by origin desc limit '.$offset.', '.$limit;
			return $this->db->query($query)->result_array();
		}
	}
	
}