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
	private $airline_markup;
	private $hotel_markup;
	private $bus_markup;
	private $airline_commission;
	private $bus_commission;
	private $sightseeing_commission;
	private $sightseeing_markup;
	private $transfer_markup;
	private $transfer_commission;
	function __construct() {
		parent::__construct('level_3');
	}

	/**
	 * Get Convinence fees of module
	 */
	function get_convinence_fees($module_name, $search_id)
	{
		$convinence_fees = array('value' => 0, 'type' => '', 'per_pax' => true);

		return $convinence_fees;
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
			case 'flight' : $markup_data = $this->airline_markup();
			break;
			case 'hotel' : $markup_data = $this->hotel_markup();
			break;
			case 'bus' : $markup_data = $this->bus_markup();
			break;
			case 'car' : $markup_data = $this->car_markup();
			break;
			case 'sightseeing' : $markup_data = $this->sightseeing_markup();
			break;
			case 'transferv1' : $markup_data = $this->transfer_markup();
			break;

			default : $markup_data = array('value' => 0, 'type' => '');
			break;
		}
		return $markup_data;
	}

	/**
	 * Balu A
	 * Manage domain markup for provab - Domain wise and module wise
	 */
	function airline_markup()
	{
		//get generic only if specific is not available
		if (empty($this->airline_markup) == true) {
			/*$response['specific_markup_list'] = $this->specific_domain_markup('b2b_flight');//FIXME:Agent-wise Markup Check the Query--Balu A
			if (valid_array($response['specific_markup_list']) == false) {
				$response['generic_markup_list'] = $this->generic_domain_markup('b2b_flight');
			}*/
			$response['specific_markup_list'] = $this->specific_airline_markup('b2b_flight');//Airline-Wise Markup
			$response['generic_markup_list'] = $this->generic_domain_markup('b2b_flight');
			$this->airline_markup = $response;
		} else {
			$response = $this->airline_markup;
		}
		return $response;
	}

	/**
	 * Balu A
	 * Manage domain markup for provab - Domain wise and module wise
	 */
	function hotel_markup()
	{
		if (empty($this->hotel_markup) == true) {
			$response['specific_markup_list'] = $this->specific_domain_markup('b2b_hotel');
			if (valid_array($response['specific_markup_list']) == false) {
				$response['generic_markup_list'] = $this->generic_domain_markup('b2b_hotel');
			}
			$this->hotel_markup = $response;
		} else {
			$response = $this->hotel_markup;
		}
		return $response;
	}

	/**
	 * Balu A
	 * Manage domain markup for provab - Domain wise and module wise
	 */
	function bus_markup()
	{
		if (empty($this->bus_markup) == true) {
			$response['specific_markup_list'] = $this->specific_domain_markup('b2b_bus');
			if (valid_array($response['specific_markup_list']) == false) {
				$response['generic_markup_list'] = $this->generic_domain_markup('b2b_bus');
			}
			$this->bus_markup = $response;
		} else {
			$response = $this->bus_markup;
		}
		return $response;
	}
	/**
	 * Anitha G
	 * Manage domain markup for provab - Domain wise and module wise
	 */
	function car_markup()
	{
		if (empty($this->car_markup) == true) {
			$response['specific_markup_list'] = $this->specific_domain_markup('b2b_car');
			if (valid_array($response['specific_markup_list']) == false) {
				$response['generic_markup_list'] = $this->generic_domain_markup('b2b_car');
			}
			$this->car_markup = $response;
		} else {
			$response = $this->car_markup;
		}
		return $response;
	}
	/**
	 * Elavarasi
	 * Manage domain markup for provab - Domain wise and module wise
	 */

	function sightseeing_markup(){
		if (empty($this->sightseeing_markup) == true) {
			$response['specific_markup_list'] = $this->specific_domain_markup('b2b_sightseeing');
			if (valid_array($response['specific_markup_list']) == false) {
				$response['generic_markup_list'] = $this->generic_domain_markup('b2b_sightseeing');
			}
			$this->sightseeing_markup = $response;
		} else {
			$response = $this->sightseeing_markup;
		}
		return $response;
	}
	/**
	 * Elavarasi
	 * Manage domain markup for provab - Domain wise and module wise
	 */

	function transfer_markup(){
		if (empty($this->transfer_markup) == true) {
			$response['specific_markup_list'] = $this->specific_domain_markup('b2b_transferv1');
			if (valid_array($response['specific_markup_list']) == false) {
				$response['generic_markup_list'] = $this->generic_domain_markup('b2b_transferv1');
			}
			$this->transfer_markup = $response;
		} else {
			$response = $this->transfer_markup;
		}
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
		$query = 'SELECT ML.origin AS markup_origin, ML.type AS markup_type, ML.reference_id, ML.value, ML.value_type,  ML.markup_currency AS markup_currency
		FROM markup_list AS ML where ML.value != "" and ML.module_type = "'.$module_type.'" and
		ML.markup_level = "'.$this->markup_level.'" and ML.type="generic" and ML.domain_list_fk='.get_domain_auth_id();
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
		$query = 'SELECT
		ML.origin AS markup_origin, ML.value, ML.value_type,  ML.markup_currency AS markup_currency
		FROM domain_list AS DL JOIN markup_list AS ML where ML.value != "" and
		ML.module_type = "'.$module_type.'" and ML.markup_level = "'.$this->markup_level.'" and DL.origin=ML.domain_list_fk and ML.type="specific"
		and ML.domain_list_fk != 0 and ML.reference_id='.get_domain_auth_id().' and ML.domain_list_fk = '.get_domain_auth_id().' order by DL.created_datetime DESC';
		$specific_data_list = $this->db->query($query)->result_array();
		return $specific_data_list;
	}
	/**
	 *  Balu A
	 * Get specific markup based on module type
	 * @param string $module_type	Name of the module for which the markup has to be returned
	 * @param string $markup_level	Level of markup
	 */
	function specific_airline_markup($module_type)
	{
		$markup_list = array();
		$query = 'SELECT AL.origin AS airline_origin, AL.name AS airline_name, AL.code AS airline_code,
		ML.origin AS markup_origin, ML.type AS markup_type, ML.reference_id, ML.value, ML.value_type, ML.markup_currency AS markup_currency
		FROM airline_list AS AL JOIN markup_list AS ML where ML.value != "" and
		ML.module_type = "'.$module_type.'" and ML.markup_level = "'.$this->markup_level.'" and AL.origin=ML.reference_id and ML.type="specific"
		and ML.domain_list_fk != 0  and ML.domain_list_fk='.get_domain_auth_id().' order by AL.name ASC';
		$specific_data_list = $this->db->query($query)->result_array();
		if (valid_array($specific_data_list)) {
			foreach ($specific_data_list as $__k => $__v) {
				$markup_list[$__v['airline_code']] = $__v;
			}
		}
		return $markup_list;
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
	 * Log XML For Provab Security
	 * @param string $operation_name
	 * @param string $app_reference
	 * @param string $module
	 * @param json 	 $request
	 * @param json	 $response
	 */
	public function provab_xml_logger($operation_name, $app_reference, $module, $request, $response)
	{
		$data['operation_name'] = $operation_name;
		$data['app_reference'] = $app_reference;
		$data['module'] = $module;
		if (is_array($request)) {
			$request = json_encode($request);
		}
		if (is_array($response)) {
			$response = json_encode($response);
		}
		$data['request'] = $request;
		$data['response'] = $response;
		$data['ip_address'] = $_SERVER['REMOTE_ADDR'];
		$data['created_datetime'] = date('Y-m-d H:i:s');

		$this->custom_db->insert_record('provab_xml_logger', $data);
	}


	/**
	 * Balu A
	 * Get Commission based on different modules
	 * @return array('value' => 0, 'type' => '')
	 */
	function get_commission($module_name, $fd, $user_id=0, $group_id=0, $booking_source='', $type='')
	{
		
		$commission_data = '';
		switch ($module_name) {
			case 'flight' : $commission_data = $this->airline_commission($fd, $user_id, $group_id, $booking_source, $type);
			break;
			case 'bus' : $commission_data = $this->bus_commission($fd,$user_id, $group_id, $booking_source);
			break;
			case 'sightseeing':$commission_data = $this->sightseeing_commission();
			break;
			case 'transferv1':$commission_data = $this->transfer_commission();
			break;
		}
		return $commission_data;
	}

	/**
	 * Balu A
	 * Manage domain Commission for current domain
	 */
	function airline_commission($fd, $user_id, $group_id, $booking_source, $type)
	{
		if (empty($this->airline_commission) == true) {
			$response['admin_commission_list'] = $this->admin_b2b_airline_commission_list($fd, $user_id, $group_id, $booking_source,$type);
			$this->airline_commission = $response;
		} else {
			$response = $this->airline_commission;
		}
		return $response;
	}

	/**
	 * Balu A
	 * Manage domain Commission for current domain
	 */
	function bus_commission($fd,$user_id, $group_id, $booking_source)
	{
		if (empty($this->bus_commission) == true) {
			$response['admin_commission_list'] = $this->admin_b2b_bus_commission_list($fd,$user_id, $group_id, $booking_source);
			$this->bus_commission = $response;
		} else {
			$response = $this->bus_commission;
		}
		return $response;
	}
	/**
	 * Elavarasi
	 * Manage domain Commission for current domain
	 */

	function sightseeing_commission(){
		if (empty($this->sightseeing_commission) == true) {
			$response['admin_commission_list'] = $this->admin_b2b_sightseeing_commission_list();
			$this->sightseeing_commission = $response;
		} else {
			$response = $this->sightseeing_commission;
		}
		return $response;
	}
	function transfer_commission(){
		if (empty($this->transfer_commission) == true) {
			$response['admin_commission_list'] = $this->admin_b2b_transfer_commission_list();
			$this->transfer_commission = $response;
		} else {
			$response = $this->transfer_commission;
		}
		return $response;
	}
	/**
	 * Get commission list data for admin
	 */
	function admin_b2b_airline_commission_list($fd, $user_id, $group_id, $booking_source, $type)
	{
		//debug($fd); exit;
        $commission=array(); 
		$domain_origin = get_domain_auth_id();
		$com = array();
		$airline_code = $fd["FlightDetails"]["Details"][0][0]["OperatorCode"];

		$get_booking_source_id = $this->custom_db->single_table_records('booking_source', 'origin', array('source_id'=>$booking_source));

		if($get_booking_source_id['status'] == SUCCESS_STATUS){
			$booking_source_origin = $get_booking_source_id['data'][0]['origin'];
		}else{
			$booking_source_origin = 0;
		}
		if($booking_source == TRAVELPORT_GDS_BOOKING_SOURCE || $booking_source == TRAVELPORT_ACH_BOOKING_SOURCE){
			$cabin_class = $fd['FlightDetails']['Details'][0][0]['CabinClass'];
			$operator_code = $fd['FlightDetails']['Details'][0][0]['OperatorCode'];
			$airline_origin_details = $this->custom_db->single_table_records('airline_list','origin',array('code' => $operator_code));
			$airline_origin = $airline_origin_details['data'][0]['origin'];
			
			$query = 'select value,value_type, commission_currency From b2b_flight_commission_details where	agent_fk='.$user_id.' AND domain_list_fk = '.$domain_origin.' AND booking_source_origin = '.$booking_source_origin.' AND airline_class = "'.$cabin_class.'" AND airline_id="'.$airline_origin.'" AND type="specific"';
			$com = $this->db->query($query)->row_array();
		}

		if(empty($com) || ($com['value'] == '0.00')){
			
			$query = 'select value,value_type, commission_currency From b2b_flight_commission_details where	agent_fk='.$user_id.' AND domain_list_fk = '.$domain_origin.' AND booking_source_origin = '.$booking_source_origin.' AND type="specific"';
			$com = $this->db->query($query)->row_array();
			
			if(empty($com) || ($com['value'] == '0.00')){
				$query = 'select value,value_type, commission_currency From b2b_flight_commission_details where	agent_fk='.$user_id.' AND domain_list_fk = '.$domain_origin.' AND course_id = "VHCID1420613784" AND type="generic"';
				$com = $this->db->query($query)->row_array();
			}
			 
			if(empty($com) || ($com['value'] == '0.00')){
				
				$query = 'select value,value_type, commission_currency From b2b_flight_commission_details where	group_fk ='.$group_id.' AND domain_list_fk = '.$domain_origin.' AND booking_source_origin = '.$booking_source_origin.' AND type="specific"';
				$com = $this->db->query($query)->row_array();
			}
			if(empty($com) || ($com['value'] == '0.00')){
				$query = 'select value,value_type, commission_currency From b2b_flight_commission_details where	group_fk='.$group_id.' AND domain_list_fk = '.$domain_origin.' AND course_id = "VHCID1420613784" AND type="generic"';
				$com = $this->db->query($query)->row_array();
			}
			
		}
		/*$query = 'SELECT *, BFCD.origin as comm_origin, AL.origin as airline_origin 
		FROM airline_list AS AL left JOIN b2b_flight_commission_details AS BFCD ON
		AL.origin=BFCD.airline_id and BFCD.type="'.FLIGHT_SPECIFIC.'"
		and BFCD.domain_list_fk != 0  and BFCD.domain_list_fk='.get_domain_auth_id().' where AL.code="'.$airline_code.'"';

		$com = $this->db->query($query)->row_array();
		
		if($comm["comm_origin"]>0)
			return $com;

		$query = 'select value,value_type, commission_currency From b2b_flight_commission_details where
		agent_fk IN (0, '.intval($this->entity_user_id).') AND domain_list_fk = '.$domain_origin.' ORDER BY type DESC';
        $com = $this->db->query($query)->row_array();
       
		if($com['value']==0)
		{
                   $query_gen = 'select value,value_type, commission_currency From b2b_flight_commission_details where
		   agent_fk IN (0) AND domain_list_fk = '.$domain_origin.' and type="generic" ORDER BY type DESC';
                   $com = $this->db->query($query_gen)->row_array();
		}*/
        $this->value_type_to_lower_case($com);
       
		return $com;
	}


	/**
	 * Get commission list data for admin
	 */
	function admin_b2b_bus_commission_list($fd,$user_id, $group_id, $booking_source)
	{
		//debug($fd);die('=====');
		$get_booking_source_id = $this->custom_db->single_table_records('booking_source', 'origin', array('source_id'=>$booking_source));

		if($get_booking_source_id['status'] == SUCCESS_STATUS){
			$booking_source_origin = $get_booking_source_id['data'][0]['origin'];
		}else{
			$booking_source_origin = 0;
		}
	
		//die('99');
		$domain_origin = get_domain_auth_id();

		// Get Master Commission
		if(!empty($fd)){
			$query = 'select value, value_type, commission_currency, commission_currency as def_currency, value as def_value,is_master,tds,operator_name From b2b_bus_commission_details where
		agent_fk IN (0, '.intval($this->entity_user_id).') AND domain_list_fk = '.$domain_origin.' AND booking_source_origin = '.$booking_source_origin.' AND type="specific" AND is_master= "1" AND operator_name = "'.$fd.'" ORDER BY type DESC';
			
			$com['master_commission'] = $this->db->query($query)->result_array();
				
		}

		if((empty($fd) || $com['master_commission'][0]['value'] == 0)){
			$query = 'select value, value_type, commission_currency, commission_currency as def_currency, value as def_value,is_master,tds,operator_name From b2b_bus_commission_details where
		agent_fk IN (0, '.intval($this->entity_user_id).') AND domain_list_fk = '.$domain_origin.' AND booking_source_origin = '.$booking_source_origin.' AND type="specific" AND is_master= "1" AND ISNULL(operator_name) ORDER BY type DESC';
		
			$com['master_commission'] = $this->db->query($query)->result_array();
		}
		
		
		if($com['master_commission'][0]['value'] == 0){
			$query = 'select value, value_type, commission_currency, commission_currency as def_currency, value as def_value,is_master,tds,operator_name From b2b_bus_commission_details where
		agent_fk IN (0, '.intval($this->entity_user_id).') AND domain_list_fk = '.$domain_origin.' AND type="generic" AND is_master= "1" ORDER BY type DESC';

			$com['master_commission'] = $this->db->query($query)->result_array();
		}

		//----------------------------------------------------------------------

		// Get Commission Value
		if(!empty($fd)){
			$query1 = 'select value, value_type, commission_currency, commission_currency as def_currency, value as def_value,is_master,tds,operator_name From b2b_bus_commission_details where
		agent_fk IN (0, '.intval($this->entity_user_id).') AND domain_list_fk = '.$domain_origin.' AND booking_source_origin = '.$booking_source_origin.' AND type="specific" AND is_master= "0" AND operator_name = "'.$fd.'" ORDER BY type DESC';
		
			$com['commission_value'] = $this->db->query($query1)->result_array();
		}

		if((empty($fd) || $com['commission_value'][0]['value'] == 0)){
			$query1 = 'select value, value_type, commission_currency, commission_currency as def_currency, value as def_value,is_master,tds,operator_name From b2b_bus_commission_details where
		agent_fk IN (0, '.intval($this->entity_user_id).') AND domain_list_fk = '.$domain_origin.' AND booking_source_origin = '.$booking_source_origin.' AND type="specific" AND is_master= "0" AND ISNULL(operator_name) ORDER BY type DESC';
		
			$com['commission_value'] = $this->db->query($query1)->result_array();
		}

		//for groupwise SPECIFIC start
		if($com['commission_value'][0]['value'] == 0){
			$query1 = 'select value, value_type, commission_currency, commission_currency as def_currency, value as def_value,is_master,tds,operator_name From b2b_bus_commission_details where
		group_fk IN (0, '.intval($group_id).') AND domain_list_fk = '.$domain_origin.' AND booking_source_origin = '.$booking_source_origin.' AND type="specific" AND is_master= "0" AND operator_name = "'.$fd.'" ORDER BY type DESC';
		
			$com['commission_value'] = $this->db->query($query1)->result_array();
		}
		if($com['commission_value'][0]['value'] == 0){
			$query1 = 'select value, value_type, commission_currency, commission_currency as def_currency, value as def_value,is_master,tds,operator_name From b2b_bus_commission_details where
		group_fk IN (0, '.intval($group_id).') AND domain_list_fk = '.$domain_origin.' AND booking_source_origin = '.$booking_source_origin.' AND type="specific" AND is_master= "0" AND ISNULL(operator_name) ORDER BY type DESC';
		
			$com['commission_value'] = $this->db->query($query1)->result_array();
		}

		//for groupwise END

		if($com['commission_value'][0]['value'] == 0){
			$query1 = 'select value, value_type, commission_currency, commission_currency as def_currency, value as def_value,is_master,tds,operator_name From b2b_bus_commission_details where
		agent_fk IN (0, '.intval($this->entity_user_id).') AND domain_list_fk = '.$domain_origin.' AND type="generic" AND is_master= "0" ORDER BY type DESC';

			$com['commission_value'] = $this->db->query($query1)->result_array();
		}

		//for groupwise GENERIC start
		if($com['commission_value'][0]['value'] == 0){
			$query1 = 'select value, value_type, commission_currency, commission_currency as def_currency, value as def_value,is_master,tds,operator_name From b2b_bus_commission_details where
		group_fk IN (0, '.intval($group_id).') AND domain_list_fk = '.$domain_origin.' AND type="generic" AND is_master= "0" ORDER BY type DESC';

			$com['commission_value'] = $this->db->query($query1)->result_array();
		}

		//for groupwise generic END

		//----------------------------------------------------------------------



		//debug($com['master_commission']);die('55555');
		//$this->value_type_to_lower_case($com);
		//debug($com);die("======");
		return $com;
	}
	/**
	 * Get commission list data for admin
	 */
	function admin_b2b_sightseeing_commission_list()
	{
		$domain_origin = get_domain_auth_id();
		$query = 'select value, value_type, commission_currency, commission_currency as def_currency, value as def_value From b2b_sightseeing_commission_details where
		agent_fk IN (0, '.intval($this->entity_user_id).') AND domain_list_fk = '.$domain_origin.' ORDER BY type DESC';
		$com = $this->db->query($query)->row_array();
		$this->value_type_to_lower_case($com);
		return $com;
	}

	/**
	 * Get commission list data for admin
	 */
	function admin_b2b_transfer_commission_list()
	{
		$domain_origin = get_domain_auth_id();
		$query = 'select value, value_type, commission_currency, commission_currency as def_currency, value as def_value From b2b_transfer_commission_details where
		agent_fk IN (0, '.intval($this->entity_user_id).') AND domain_list_fk = '.$domain_origin.' ORDER BY type DESC';
		$com = $this->db->query($query)->row_array();
		$this->value_type_to_lower_case($com);
		return $com;
	}


	/**
	 * Used only to lower the value type field
	 * @param array $com
	 */
	private function value_type_to_lower_case(& $row)
	{
		if (isset($row['value_type']) == true) {
			$row['value_type'] = strtolower($row['value_type']);
		} else {
			$row['value'] = 0;
			$row['value_type'] = 'plus';
			$row['commission_currency'] = MARKUP_CURRENCY;
		}
	}

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

}
