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
	private $sightseeing_markup;
	private $transferv1_markup;
	function __construct() {
		parent::__construct('level_1');
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
			case 'sightseeing' :$markup_data = $this->sightseeing_markup();
			break;
			case 'car' : $markup_data = $this->car_markup();
			break;
			case 'transferv1' : $markup_data = $this->transferv1_markup();
			break;

			default : $markup_data = array('value' => 0, 'type' => '');
			break;
		}
		return $markup_data;
	}

	/**
	 * Get Convinence fees of module
	 */
	function get_convinence_fees($module_name, $search_id)
	{
		$convinence_fees = '';
		switch ($module_name) {
			case 'flight' : $convinence_fees = $this->airline_convinence_fees($search_id);
			//Calculate Convenience fees
			break;
			case 'hotel' : $convinence_fees = $this->hotel_convinence_fees($search_id);
			break;
			case 'sightseeing':$convinence_fees = $this->sightseeing_convinence_fees();
				break;

			case 'transferv1':$convinence_fees = $this->transfers_convinence_fees();
				break;

			case 'car':$convinence_fees = $this->car_convinence_fees();
				break;
			case 'bus':$convinence_fees = $this->bus_convinence_fees();
				break;
			default : $convinence_fees = array('value' => 0, 'type' => '', 'per_pax' => true);
			break;
		}
		return $convinence_fees;
	}
	/**
	 * Get Convinence fees for Airline
	 */
	function airline_convinence_fees($search_id)
	{
		//Based on destination we need to load convinence fees so need search id
		$this->load->model('flight_model');
		$search_data = $this->flight_model->get_safe_search_data($search_id);
		$is_domestic = @$search_data['data']['is_domestic'];
		$cond = '';
		if ($is_domestic == true) {
			$cond .= ' module = "domestic_flight" ';
		} else {
			$cond .= ' module = "international_flight" ';
		}
		$query = 'select value, value_type as type, per_pax,convenience_fee_currency from convenience_fees Where '.$cond;
		$convinence_fees = $this->db->query($query)->row_array();
		if (valid_array($convinence_fees) == true) {
			return $convinence_fees;
		}
	}

	function hotel_convinence_fees($search_id)
	{
		//Based on destination we need to load convinence fees so need search id
		$this->load->model('hotel_model');
		$search_data = $this->hotel_model->get_safe_search_data($search_id);//
		$is_domestic = @$search_data['data']['is_domestic'];
		$cond = '';
		if ($is_domestic == true) {
			$cond .= ' module = "domestic_hotel" ';
		} else {
			$cond .= ' module = "international_hotel" ';
		}
		$query = 'select value, value_type as type, per_pax from convenience_fees Where '.$cond;
		$convinence_fees = $this->db->query($query)->row_array();
		if (valid_array($convinence_fees) == true) {
			return $convinence_fees;
		}
	}

	/**
	*Elavarasi
	* Convience fees for Sightseeing based on traveller count
	*/
	private function sightseeing_convinence_fees(){
		$cond = ' module = "sightseeing" ';
		$query = 'select value, value_type as type, per_pax from convenience_fees Where '.$cond;
		$convinence_fees = $this->db->query($query)->row_array();
		if (valid_array($convinence_fees) == true) {
			return $convinence_fees;
		}
	}

	/**
	*Elavarasi
	* Convience fees for Transfers based on traveller count
	*/
	private function transfers_convinence_fees(){
		$cond = ' module = "transfers" ';
		$query = 'select value, value_type as type, per_pax from convenience_fees Where '.$cond;
		$convinence_fees = $this->db->query($query)->row_array();
	
		if (valid_array($convinence_fees) == true) {
			return $convinence_fees;
		}
	}

	/**
	*Anitha G
	* Convience fees for Car
	*/
	private function car_convinence_fees(){
		$cond = ' module = "car" ';
		$query = 'select value, value_type as type, per_pax from convenience_fees Where '.$cond;
		$convinence_fees = $this->db->query($query)->row_array();
		if (valid_array($convinence_fees) == true) {
			return $convinence_fees;
		}
	}
	/**
	*Anitha G
	* Convience fees for Car
	*/
	private function bus_convinence_fees(){
		$cond = ' module = "bus" ';
		$query = 'select value, value_type as type, per_pax from convenience_fees Where '.$cond;
		
		$convinence_fees = $this->db->query($query)->row_array();
		if (valid_array($convinence_fees) == true) {
			return $convinence_fees;
		}
	}
	/**
	 * Balu A
	 * Manage domain markup for provab - Domain wise and module wise
	 */
	function airline_markup()
	{
		//get generic only if specific is not available
		if (empty($this->airline_markup) == true) {
			$response['specific_markup_list'] = $this->specific_domain_markup('b2c_flight');
			if (valid_array($response['specific_markup_list']) == false) {
				$response['generic_markup_list'] = $this->generic_domain_markup('b2c_flight');
			}
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
			$response['specific_markup_list'] = $this->specific_domain_markup('b2c_hotel');
			if (valid_array($response['specific_markup_list']) == false) {
				$response['generic_markup_list'] = $this->generic_domain_markup('b2c_hotel');
			}
			$this->hotel_markup = $response;
		} else {
			$response = $this->hotel_markup;
		}
		return $response;
	}
	
	/**
	 * Elavarasi
	 * Manage domain markup for provab - Domain wise and module wise
	 */
	function sightseeing_markup()
	{
		if (empty($this->sightseeing_markup) == true) {
			$response['specific_markup_list'] = $this->specific_domain_markup('b2c_sightseeing');
			if (valid_array($response['specific_markup_list']) == false) {
				$response['generic_markup_list'] = $this->generic_domain_markup('b2c_sightseeing');
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
	function transferv1_markup()
	{
		if (empty($this->transferv1_markup) == true) {
			$response['specific_markup_list'] = $this->specific_domain_markup('b2c_transferv1');
			if (valid_array($response['specific_markup_list']) == false) {
				$response['generic_markup_list'] = $this->generic_domain_markup('b2c_transferv1');
			}
			$this->transferv1_markup = $response;
		} else {
			$response = $this->transferv1_markup;
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
			$response['specific_markup_list'] = $this->specific_domain_markup('b2c_car');
			if (valid_array($response['specific_markup_list']) == false) {
				$response['generic_markup_list'] = $this->generic_domain_markup('b2c_car');
			}
			$this->car_markup = $response;
		} else {
			$response = $this->car_markup;
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
			$response['specific_markup_list'] = $this->specific_domain_markup('b2c_bus');
			if (valid_array($response['specific_markup_list']) == false) {
				$response['generic_markup_list'] = $this->generic_domain_markup('b2c_bus');
			}
			$this->bus_markup = $response;
		} else {
			$response = $this->bus_markup;
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
		$query = 'SELECT
		ML.origin AS markup_origin, ML.value, ML.value_type,  ML.markup_currency AS markup_currency
		FROM domain_list AS DL JOIN markup_list AS ML where ML.value != "" and
		ML.module_type = "'.$module_type.'" and ML.markup_level = "'.$this->markup_level.'" and DL.origin=ML.domain_list_fk and ML.type="specific"
		and ML.domain_list_fk != 0 and ML.reference_id='.get_domain_auth_id().' and ML.domain_list_fk = '.get_domain_auth_id().' order by DL.created_datetime DESC';
		$specific_data_list = $this->db->query($query)->result_array();
		return $specific_data_list;
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
}
