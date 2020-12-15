<?php
/**
 * @package    Provab Application
 * @subpackage Travel Portal
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V2
 */
abstract Class Abstract_Management_Model extends CI_Model
{
	var $markup_level;
	function __construct($markup_level) {
		$this->markup_level = $markup_level;
	}
	/**
	 * Balu A
	 * @param number $markup_origin - Markup origin
	 * @param string $type			- generic/specific
	 * @param string $module_type	- module name - b2c_hotel/b2c_flight/b2c_bus
	 * @param number $reference_id	- reference id in case of specific markup
	 * @param number $value			- value of markup
	 * @param string $value_type	- precentage/plus
	 * @param number $domain_origin	- domain to which markup is applicable
	 */
	function save_markup_data($markup_origin, $type, $module_type, $reference_id, $value, $value_type, $domain_origin, $user_oid=0, $int_value='0.00', $int_value_type='plus')
	{
		// echo $int_value;exit;
		$markup_data['origin']			= intval($markup_origin);
		$markup_data['markup_level']	= $this->markup_level;
		$markup_data['type']			= strtolower($type);
		$markup_data['module_type']		= strtolower($module_type);
		$markup_data['reference_id']	= intval($reference_id);
		$markup_data['value']			= floatval($value);
		$markup_data['value_type']		= strtolower($value_type);
		$markup_data['int_value']			= floatval($int_value);
		$markup_data['int_value_type']		= strtolower($int_value_type);
		$markup_data['domain_list_fk']	= intval($domain_origin);
		$markup_data['user_oid']		= $user_oid;
		$markup_data['markup_currency']	= get_application_default_currency();
		// debug($markup_data);exit;
		if (empty($markup_data['type']) == false && empty($markup_data['value_type']) == false) {
			if (intval($markup_origin) > 0) {
				//update
				$this->custom_db->update_record('markup_list', $markup_data, array('origin' => intval($markup_origin)));
			} else {
				//insert
				$this->custom_db->insert_record('markup_list', $markup_data);
			}	
		}
	}

	function save_comm_data($comm_origin, $type, $airline_id, $value, $api_value, $value_type,
		$domain_origin, $agent_fk=0)
	{
		$comm_data['origin']			= intval($comm_origin);
		$comm_data['type']				= strtolower($type);
		$comm_data['airline_id']		= intval($airline_id);
		$comm_data['booking_source_origin'] = 0;
		$comm_data['value']				= floatval($value);
		$comm_data['api_value']			= floatval($api_value);
		$comm_data['value_type']		= strtolower($value_type);
		$comm_data['domain_list_fk']	= intval($domain_origin);
		$comm_data['agent_fk']			= $agent_fk;
		$comm_data['created_by_id']		= $this->entity_user_id;
		$comm_data['created_datetime']	= date("Y-m-d H:i:s");
		$comm_data['commission_currency']	= get_application_default_currency();
		if (empty($comm_data['type']) == false && empty($comm_data['value_type']) == false) {
			if (intval($comm_origin) > 0) {
				//update
				$this->custom_db->update_record('b2b_flight_commission_details', $comm_data, array('origin' => intval($comm_origin)));
			} else {
				
				$al_update_condition["origin"]=$comm_data['airline_id'];
				$al_update_data["has_specific_commission"]=1;
				//Update Flight record to set specific commission
				$this->custom_db->update_record('airline_list', $al_update_data, 
				$al_update_condition);
				//insert commission record
				unset($comm_data['origin']);
				$this->custom_db->insert_record('b2b_flight_commission_details', $comm_data);
			}
		}
	}

	function save_supplier_specific_comm_data($comm_origin, $type, $bs_origin, $value, 
		$api_value, $value_type, $domain_origin, $agent_fk=0)
	{
		$comm_data['origin']			= intval($comm_origin);
		$comm_data['type']				= strtolower($type);
		$comm_data['booking_source_origin']	= intval($bs_origin);
		$comm_data['airline_id']		= 0;
		$comm_data['value']				= floatval($value);
		$comm_data['api_value']			= floatval($api_value);
		$comm_data['value_type']		= strtolower($value_type);
		$comm_data['domain_list_fk']	= intval($domain_origin);
		$comm_data['agent_fk']			= $agent_fk;
		$comm_data['created_by_id']		= $this->entity_user_id;
		$comm_data['created_datetime']	= date("Y-m-d H:i:s");
		$comm_data['commission_currency']	= get_application_default_currency();
		//debug($comm_data); exit;
		if (empty($comm_data['type']) == false && empty($comm_data['value_type']) == false && $api_value!='') {
			if (intval($comm_origin) > 0) {
				//update
				$this->custom_db->update_record('b2b_flight_commission_details', $comm_data, array('origin' => intval($comm_origin)));
			} else {
				
				$bs_update_condition["origin"]=$comm_data['booking_source_origin'];
				$bs_update_data["has_specific_commission"]=1;
				//Update Flight record to set specific commission
				$this->custom_db->update_record('booking_source', $bs_update_data, 
				$bs_update_condition);
				//insert commission record
				unset($comm_data['origin']);
				$this->custom_db->insert_record('b2b_flight_commission_details', $comm_data);
			}
		}
	}

	/**
	 * Categorize Airline Markup List - generic and specific

	 private function categorize_markup_list($markup_list)
	 {
		$data_list['generic'] = $data_list['specific'] = '';
		foreach($markup_list as $__k => $__v) {
		if ($__v['markup_type'] == 'generic') {
		$data_list['generic'][] = $__v;
		} elseif ($__v['markup_type'] == 'specific') {
		$data_list['specific'][$__v['domain_key']] = $__v;
		}
		}
		return $data_list;
		}*/

	/**
	 * @param array $data_list

	 private function extract_markup_domain_list($data_list)
	 {
		$data = '';
		if (valid_array($data_list)) {
		foreach ($data_list as $__k => $__v) {
		if (isset($data[$__v['domain_key']]) == false) {
		$data[$__v['domain_key']]['domain_origin']	= $__v['domain_origin'];
		$data[$__v['domain_key']]['domain_name']	= $__v['domain_name'];
		$data[$__v['domain_key']]['domain_key']		= $__v['domain_key'];
		$data[$__v['domain_key']]['domain_status']	= $__v['domain_status'];
		}
		}
		}
		return $data;
		} */
}
