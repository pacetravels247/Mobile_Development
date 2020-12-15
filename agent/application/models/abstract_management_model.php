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
		if( get_client_ip() == '27.59.206.114'){
		// echo $this->markup_level;exit;
		}
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
	function save_markup_data($markup_origin, $type, $module_type, $reference_id, $value, $value_type, $domain_origin, $int_value='', $int_value_type='')
	{
		$markup_data['origin']			= intval($markup_origin);
		$markup_data['markup_level']	= $this->markup_level;
		$markup_data['type']			= strtolower($type);
		$markup_data['module_type']		= strtolower($module_type);
		$markup_data['reference_id']	= intval($reference_id);
		$markup_data['value']			= floatval($value);
		$markup_data['value_type']		= strtolower($value_type);
		$markup_data['int_value']			= floatval($int_value);
		if(empty($int_value_type) == true){
			$markup_data['int_value_type']		= 'plus';
		}
		else{
			$markup_data['int_value_type']		= strtolower($int_value_type);
		}
		

		$markup_data['domain_list_fk']	= intval($domain_origin);
		$markup_data['user_oid']		= $this->entity_user_id;
		$markup_data['markup_currency']	= get_application_currency_preference();
		// debug($markup_origin);exit;
		if (empty($markup_data['type']) == false && empty($markup_data['value_type']) == false) {
			if (intval($markup_origin) > 0) {
				//update
				$this->custom_db->update_record('markup_list', $markup_data, array('origin' => intval($markup_origin)));
				// echo $this->db->last_query();exit;

			} else {
				//insert
				$this->custom_db->insert_record('markup_list', $markup_data);
			}
		}
	}
}