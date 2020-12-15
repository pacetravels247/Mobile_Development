<?php
require_once 'abstract_management_model.php';
/**
 * @package    Provab Application
 * @subpackage Travel Portal
 * @author     Arjun J<arjunjgowda260389@gmail.com>
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
	 * Arjun J Gowda
	 * Get markup based on different modules
	 * @return array('value' => 0, 'type' => '')
	 */
	function get_markup($module_name, $version = FLIGHT_VERSION_1, $OperatorCode='', $is_domestic='')
	{
		$markup_data = '';
		switch ($module_name) {
			 case 'b2c_flight' :  $markup_data = $this->airline_markup($version, $OperatorCode, $is_domestic);
			break;
			case 'b2c_hotel' : $markup_data = $this->hotel_markup();
			break;
			case 'b2c_bus' : $markup_data = $this->bus_markup();
			break;
			case 'b2c_sightseeing' : $markup_data = $this->sightseeing_markup();
			break;
			case 'b2c_viator_transfer' : $markup_data = $this->transferv1_markup();
			break;
			default : $markup_data = array('value' => 0, 'type' => '');
			break;

		}
		return $markup_data;
	}

	/**
	 * Arjun J Gowda
	 * Manage domain markup for provab - Domain wise and module wise
	 */
	function airline_markup($version = FLIGHT_VERSION_1, $opcode='', $is_domestic='')
	{
		//get generic only if specific is not available
		if (empty($this->airline_markup) == true) {
                 if($opcode!='') {
                        $response['airline_wise_markup_list'] = $this->specific_ailrine_wise_markup('b2c_flight', $version, $opcode);
                 }
			$response['specific_markup_list'] = $this->specific_domain_markup('b2c_flight', $version, $is_domestic);
			
			$response['generic_markup_list'] = $this->generic_domain_markup('b2c_flight', $version, $is_domestic);
			# Diabled By Balu
			//$this->airline_markup = $response;
		} else {
			$response = $this->airline_markup;
		}
               
		return $response;
	}

	/**
	 * Arjun J Gowda
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
	 * Arjun J Gowda
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
	function transferv1_markup(){
		if (empty($this->transferv1_markup) == true) {
			$response['specific_markup_list'] = $this->specific_domain_markup('b2c_viator_transfer');
			if (valid_array($response['specific_markup_list']) == false) {
				$response['generic_markup_list'] = $this->generic_domain_markup('b2c_viator_transfer');
			}
			$this->transferv1_markup = $response;
		} else {
			$response = $this->transferv1_markup;
		}
		return $response;
	}
	/**
	 * Arjun J Gowda
	 * Get generic markup based on the module type
	 * @param $module_type
	 * @param $markup_level
	 */
	function generic_domain_markup($module_type, $version = FLIGHT_VERSION_1, $is_domestic='')
	{
		if($version == FLIGHT_VERSION_2){
			if(($is_domestic == 1) && ($module_type == 'b2c_flight')){
				$query = 'SELECT ML.origin AS markup_origin, ML.type AS markup_type, ML.reference_id, ML.value, ML.value_type,
				ML.markup_currency AS markup_currency,ML.booking_source_fk,BS.source_id
				FROM markup_list AS ML
				LEFT JOIN booking_source BS on BS.origin=ML.booking_source_fk
				where ML.value != "" and ML.module_type = "'.$module_type.'" and
				ML.markup_level = "'.$this->markup_level.'" and ML.type="generic" and ML.domain_list_fk=0
				order by ML.booking_source_fk desc';
				// echo $query;exit;
			}
			else if($module_type == 'b2c_flight'){
				$query = 'SELECT ML.origin AS markup_origin, ML.type AS markup_type, ML.reference_id, ML.int_value as value, ML.int_value_type as value_type,
				ML.markup_currency AS markup_currency,ML.booking_source_fk,BS.source_id
				FROM markup_list AS ML
				LEFT JOIN booking_source BS on BS.origin=ML.booking_source_fk
				where ML.int_value != "" and ML.module_type = "'.$module_type.'" and
				ML.markup_level = "'.$this->markup_level.'" and ML.type="generic" and ML.domain_list_fk=0
				order by ML.booking_source_fk desc';
				
			}
			else{
				$query = 'SELECT ML.origin AS markup_origin, ML.type AS markup_type, ML.reference_id, ML.value, ML.value_type,
				ML.markup_currency AS markup_currency,ML.booking_source_fk,BS.source_id
				FROM markup_list AS ML
				LEFT JOIN booking_source BS on BS.origin=ML.booking_source_fk
				where ML.value != "" and ML.module_type = "'.$module_type.'" and
				ML.markup_level = "'.$this->markup_level.'" and ML.type="generic" and ML.domain_list_fk=0
				order by ML.booking_source_fk desc';
				// echo $query;exit;
			}

			
		} else{
			 if(strtolower($module_type) == 'b2c_flight'){//For Older Version
				$booking_source_fk = $this->custom_db->single_table_records('booking_source', 'origin', array('source_id' => trim(TBO_FLIGHT_BOOKING_SOURCE)));
				$booking_source_fk = $booking_source_fk['data'][0]['origin'];
				
				$booking_source_filter = ' AND ((ML.booking_source_fk=0 OR ML.booking_source_fk is NULL) OR ML.booking_source_fk='.$booking_source_fk.') ';
			} else {
				$booking_source_filter = '';
			}
			$query = 'SELECT ML.origin AS markup_origin, ML.type AS markup_type, ML.reference_id, ML.value, ML.value_type,
			ML.markup_currency AS markup_currency,ML.booking_source_fk
			FROM markup_list AS ML where ML.value != "" and ML.module_type = "'.$module_type.'" and
			ML.markup_level = "'.$this->markup_level.'" and ML.type="generic" and ML.domain_list_fk=0 '.$booking_source_filter.'
			order by ML.booking_source_fk desc limit 1';
		}
		$generic_data_list = $this->db->query($query)->result_array();
		return $generic_data_list;
	}

	/**
	 * Arjun J Gowda
	 * Get specific markup based on module type
	 * @param string $module_type	Name of the module for which the markup has to be returned
	 * @param string $markup_level	Level of markup
	 */
	function specific_domain_markup($module_type, $version = FLIGHT_VERSION_1, $is_domestic='')
	{
		if($version == FLIGHT_VERSION_2){
			if($is_domestic == 1 && $module_type == 'b2c_flight'){
				$query = 'SELECT
				ML.origin AS markup_origin, ML.value, ML.value_type,  ML.markup_currency AS markup_currency,BS.source_id
				FROM domain_list AS DL JOIN markup_list AS ML
				JOIN booking_source BS on BS.origin=ML.booking_source_fk
				JOIN domain_api_map DAM on BS.origin=DAM.booking_source_fk and DL.origin=DAM.domain_list_fk
				where ML.value != "" and
				ML.module_type = "'.$module_type.'" and ML.markup_level = "'.$this->markup_level.'" and DL.origin=ML.domain_list_fk and ML.type="specific"
				and ML.domain_list_fk != 0 and ML.reference_id='.get_domain_auth_id().' 
				and ML.domain_list_fk = '.get_domain_auth_id().'
				order by DL.created_datetime DESC';
				// echo $query;exit;
			}
			else if($module_type == 'b2c_flight'){
				$query = 'SELECT
				ML.origin AS markup_origin, ML.int_value as value, ML.int_value_type as value_type,  ML.markup_currency AS markup_currency,BS.source_id
				FROM domain_list AS DL JOIN markup_list AS ML
				JOIN booking_source BS on BS.origin=ML.booking_source_fk
				JOIN domain_api_map DAM on BS.origin=DAM.booking_source_fk and DL.origin=DAM.domain_list_fk
				where ML.int_value != "" and
				ML.module_type = "'.$module_type.'" and ML.markup_level = "'.$this->markup_level.'" and DL.origin=ML.domain_list_fk and ML.type="specific"
				and ML.domain_list_fk != 0 and ML.reference_id='.get_domain_auth_id().' 
				and ML.domain_list_fk = '.get_domain_auth_id().'
				order by DL.created_datetime DESC';
				// echo $query;exit;
			}
			else{
				$query = 'SELECT
				ML.origin AS markup_origin, ML.value, ML.value_type,  ML.markup_currency AS markup_currency,BS.source_id
				FROM domain_list AS DL JOIN markup_list AS ML
				JOIN booking_source BS on BS.origin=ML.booking_source_fk
				JOIN domain_api_map DAM on BS.origin=DAM.booking_source_fk and DL.origin=DAM.domain_list_fk
				where ML.value != "" and
				ML.module_type = "'.$module_type.'" and ML.markup_level = "'.$this->markup_level.'" and DL.origin=ML.domain_list_fk and ML.type="specific"
				and ML.domain_list_fk != 0 and ML.reference_id='.get_domain_auth_id().' 
				and ML.domain_list_fk = '.get_domain_auth_id().'
				order by DL.created_datetime DESC';
			}
			
			
		}  else {
			if(strtolower($module_type) == 'b2c_flight'){//For Older Version
				$booking_source_fk = $this->custom_db->single_table_records('booking_source', 'origin', array('source_id' => trim(TBO_FLIGHT_BOOKING_SOURCE)));
				$booking_source_fk = $booking_source_fk['data'][0]['origin'];
			
				$booking_source_filter = ' AND ML.booking_source_fk='.$booking_source_fk.' ';
			} else {
				$booking_source_filter = '';
			}
			$query = 'SELECT
			ML.origin AS markup_origin, ML.value, ML.value_type,  ML.markup_currency AS markup_currency,BS.source_id
			FROM domain_list AS DL JOIN markup_list AS ML
			LEFT JOIN booking_source BS on BS.origin=ML.booking_source_fk 
			where ML.value != "" and
			ML.module_type = "'.$module_type.'" and ML.markup_level = "'.$this->markup_level.'" and DL.origin=ML.domain_list_fk and ML.type="specific"
			and ML.domain_list_fk != 0 and ML.reference_id='.get_domain_auth_id().' 
			and ML.domain_list_fk = '.get_domain_auth_id().''.$booking_source_filter.'
			order by DL.created_datetime DESC';
		}
		
		//$this->custom_db->insert_record('test', array('test' => $query));
		$specific_data_list = $this->db->query($query)->result_array();
		return $specific_data_list;
	}
        
           /**
	 * Balu A
	 * Get specific airline wise markup for flight
	 * @param string $module_type	Name of the module for which the markup has to be returned
	 * @param string $markup_level	Level of markup
	 */
	function specific_ailrine_wise_markup($module_type, $version = FLIGHT_VERSION_1, $opcode)
	{
              if($version == FLIGHT_VERSION_2){
                
              
			$query = 'SELECT ML.origin AS markup_origin, ML.type AS markup_type, ML.reference_id, ML.value, ML.value_type,
			ML.markup_currency AS markup_currency,ML.booking_source_fk,BS.source_id
			FROM markup_list_airline AS ML
                        LEFT JOIN airline_list AS AL on AL.origin=ML.reference_id
			LEFT JOIN booking_source BS on BS.origin=ML.booking_source_fk
			where ML.value != "" and ML.module_type = "'.$module_type.'" and
			ML.markup_level = "'.$this->markup_level.'" and ML.type="specific" and ML.domain_list_fk='.get_domain_auth_id().' and AL.code="'.$opcode.'"
			order by ML.booking_source_fk desc';
                  
                      
		} else{
			 if(strtolower($module_type) == 'b2c_flight'){//For Older Version
				$booking_source_fk = $this->custom_db->single_table_records('booking_source', 'origin', array('source_id' => trim(TBO_FLIGHT_BOOKING_SOURCE)));
				$booking_source_fk = $booking_source_fk['data'][0]['origin'];
				
				$booking_source_filter = ' AND ((ML.booking_source_fk=0 OR ML.booking_source_fk is NULL) OR ML.booking_source_fk='.$booking_source_fk.') ';
			} else {
				$booking_source_filter = '';
			}
			$query = 'SELECT ML.origin AS markup_origin, ML.type AS markup_type, ML.reference_id, ML.value, ML.value_type,
			ML.markup_currency AS markup_currency,ML.booking_source_fk
			FROM markup_list_airline AS ML 
                        LEFT JOIN airline_list AS AL on AL.origin=ML.reference_id
                        where ML.value != "" and ML.module_type = "'.$module_type.'" and
			ML.markup_level = "'.$this->markup_level.'" and ML.type="specific" and ML.domain_list_fk='.get_domain_auth_id().' and AL.code="'.$opcode.'"
			order by ML.booking_source_fk desc';
                      
                       
		}
		$generic_data_list = $this->db->query($query)->result_array();
           
		return $generic_data_list;
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
}
