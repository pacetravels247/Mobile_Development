<?php
/**
 * Library which has cache functions to get data
 *
 * @package    Provab Application
 * @subpackage Travel Portal
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V2
 */
Class Db_Cache_Api extends CI_Model
{
	function __construct()
	{
		$this->load->helper('custom/db_api');
	}

	private $cache;
	/**
	 * Balu A
	 * get the country details
	 * @param array $from array('k' => 'id', 'v' => 'name')
	 * @param array $condition
	 */
	function get_country_list($from=array('k' => 'origin', 'v' => 'name'), $condition=array('name !=' => ''))
	{
		//Balu A
		return magical_converter($from, $this->cache[$this->set_country_list($condition)]);
	}

	/**
	 * set the country details
	 * @param array $condition
	 */
	function set_country_list($condition) {
		$hash_key = hash('md5', __CLASS__.'api_country_list'.json_encode($condition));
		if (isset($this->cache[$hash_key]) == false) {
			$this->cache[$hash_key] = $this->custom_db->single_table_records('api_country_list', '*', $condition, 0, 100000000, array('name' => 'ASC'));
		}
		return $hash_key;
	}

	/**
	 * set the comm group
	 * @param array $condition
	 */
	function set_comm_group_list($condition) {
		$hash_key = hash('md5', __CLASS__.'user_groups'.json_encode($condition));
		if (isset($this->cache[$hash_key]) == false) {
			$this->cache[$hash_key] = $this->custom_db->single_table_records('user_groups', '*', $condition, 0, 100000000, array('name' => 'ASC'));
		}
		//debug($this->cache[$hash_key]); exit;
		return $hash_key;
	}

	/**
	 * Balu A
	 * get the country details
	 * @param array $from array('k' => 'id', 'v' => 'name')
	 * @param array $condition
	 */
	function get_current_balance()
	{
		//Balu A
		return $this->cache[$this->set_current_balance()];
	}

	/**
	 * set the country details
	 * @param array $condition
	 */
	function set_current_balance() {
		$hash_key = hash('md5', __CLASS__.'domain_list_balance');
		if (isset($this->cache[$hash_key]) == false) {
			$domain_id = intval(get_domain_auth_id());
			$domain_details = $this->custom_db->single_table_records('domain_list', 'balance', array('origin' => $domain_id));
			$this->cache[$hash_key] = array('value' => $domain_details['data'][0]['balance']);
		}
		return $hash_key;
	}
	/**
	 * Balu A
	 * get the Admin Base Currency
	 */
	function get_admin_base_currency()
	{
		return $this->cache[$this->set_admin_base_currency()];
	}

	/**
	 * Set the Admin Base Currency
	 * @param array $condition
	 */
	function set_admin_base_currency() {
		$hash_key = hash('md5', __CLASS__.'admin_base_currency');
		if (isset($this->cache[$hash_key]) == false) {
			$domain_id = intval(get_domain_auth_id());
			$query = 'select CC.country as base_currency 
					from domain_list DL
					JOIN currency_converter CC on CC.id=DL.currency_converter_fk
					where origin='.$domain_id;
			$domain_details = $this->db->query($query)->row_array();
			$this->cache[$hash_key] = $domain_details['base_currency'];
		}
		return $hash_key;
	}

	/**
	 * get the postal code details
	 * @param array $from array('k' => 'id', 'v' => 'name')
	 * @param array $condition
	 */

	function get_postal_code_list($from=array('k' => 'origin', 'v' => array('name', 'country_code')), $condition=array('name !=' => ''))
	{
		return magical_converter($from, $this->cache[$this->set_country_list($condition)]);
	}

	/**
	 * get the user commission groups
	 * @param array $from array('k' => 'id', 'v' => 'name')
	 * @param array $condition
	 */

	function get_comm_groups($from=array('k' => 'origin', 'v' => array('name')), $condition=array('origin !=' => ''))
	{
		return magical_converter($from, $this->cache[$this->set_comm_group_list($condition)]);
	}
	/**
	 * get the postal code details
	 * @param array $from array('k' => 'id', 'v' => 'name')
	 * @param array $condition
	 */

	function get_country_code_list($from=array('k' => 'country_code', 'v' => array('name', 'country_code')), $condition=array('name !=' => ''))
	{
		return magical_converter($from, $this->cache[$this->set_country_list($condition)]);
	}

	/**
	 * get the user type details
	 * @param array $from array('k' => 'id', 'v' => 'name')
	 * @param array $condition
	 */
	function get_user_type($from=array('k' => 'origin', 'v' => array('user_type')), $condition=array('user_type !=' => '', 'origin !=' =>  ADMIN))
	{
		//FIXME
		if((isset($_GET['domain_origin']) == true && intval($_GET['domain_origin']) >0) ||
		(isset($_GET['uid']) == true && intval($_GET['uid']) >0) &&
		$this->entity_user_id == intval($_GET['uid'])) {
			//DOMAIN ADMIN CREATION BY PROVAB ADMIN (GET ONLY USER TYPE ADMIN) (OR) EDIT THEIR ACCOUNT
			//checking if it is Superadmin or Sub admin

			if($this->entity_user_type==ADMIN){
				$condition = array('user_type !=' => '', 'origin =' =>  ADMIN);
			}elseif ($this->entity_user_type==SUB_ADMIN) {
				$condition = array('user_type !=' => '', 'origin =' =>  SUB_ADMIN);
			}
			
		} else if(get_domain_auth_id() > 0) {
			//DOMAIN USERS CREATION BY DOMAIN ADMIN (GET ALL USER TYPES EXCEPT ADMIN USER TYPE)
			$condition = array('user_type !=' => '', 'origin !=' =>  ADMIN);
		}
		return magical_converter($from, $this->cache[$this->set_user_type($condition)]);
	}

	/**
	 * set the country details
	 * @param array $condition
	 */
	function set_user_type($condition) {
		$hash_key = hash('md5', __CLASS__.'user_type'.json_encode($condition));
		if (isset($this->cache[$hash_key]) == false) {
			$this->cache[$hash_key] = $this->custom_db->single_table_records('user_type', '*', $condition, 0, 100000000, array('user_type' => 'ASC'));
			
		}
		return $hash_key;
	}

	/**
	 * get the continent details
	 * @param array $from array('k' => 'origin', 'v' => 'name')
	 * @param array $condition
	 */
	function get_continent_list($from=array('k' => 'origin', 'v' => 'name'), $condition=array('name !=' => ''))
	{
		return magical_converter($from, $this->cache[$this->set_continent_list($condition)]);
	}

	/**
	 * set the continet details
	 * @param array $condition
	 */
	function set_continent_list($condition) {
		$hash_key = hash('md5', __CLASS__.'api_continent_list'.json_encode($condition));
		if (isset($this->cache[$hash_key]) == false) {
			$this->cache[$hash_key] = $this->custom_db->single_table_records('api_continent_list', '*', $condition);
		}
		return $hash_key;
	}

	/**
	 * get the city details
	 * @param array $from array('k' => 'id', 'v' => 'destination')
	 * @param array $condition
	 */
	function get_city_list($from=array('k' => 'origin', 'v' => 'destination'), $condition=array('destination !=' => ''))
	{
		return magical_converter($from, $this->cache[$this->set_city_list($condition)]);
	}

	/**
	 * set the city details
	 * @param array $condition
	 */
	function set_city_list($condition) {
		$hash_key = hash('md5', __CLASS__.'api_city_list'.json_encode($condition));
		if (isset($this->cache[$hash_key]) == false) {
			$this->cache[$hash_key] = $this->custom_db->single_table_records('api_city_list', '*', $condition);
		}
		return $hash_key;
	}
	/**
	 * 	Get Course Type
	 */
	function get_course_type($from=array('k' => 'origin', 'v' => 'name'), $condition=array('name !=' => ''))
	{
		return magical_converter($from, $this->cache[$this->set_course_type($condition)]);
	}

	/**
	 * get course type list
	 */
	function course_type_list($condition)
	{
		return $this->cache[$this->set_course_type($condition)];
	}

	/**
	 * set the course details
	 * @param array $condition
	 */
	function set_course_type($condition) {
		$hash_key = hash('md5', __CLASS__.'meta_course_list'.json_encode($condition));
		if (isset($this->cache[$hash_key]) == false) {
			$this->cache[$hash_key] = $this->custom_db->single_table_records('meta_course_list', '*', $condition, 0, 100000000, array('priority_number' => 'ASC'));
		}
		return $hash_key;
	}

	/**
	 * 	Get booking source Type
	 */
	function get_booking_source($from=array('k' => 'origin', 'v' => 'name'), $condition=array('name !=' => ''))
	{
		return magical_converter($from, $this->cache[$this->set_booking_source($condition)]);
	}

	/**
	 * set the booking source details
	 * @param array $condition
	 */
	function set_booking_source($condition) {
		$hash_key = hash('md5', __CLASS__.'booking_source'.json_encode($condition));
		if (isset($this->cache[$hash_key]) == false) {
			$this->cache[$hash_key] = $this->custom_db->single_table_records('booking_source', '*', $condition);
		}
		return $hash_key;
	}

	/**
	 * 	Get Currencies
	 */
	function get_currency($from=array('k' => 'id', 'v' => 'country'), $condition=array('country !=' => ''))
	{
		return magical_converter($from, $this->cache[$this->set_currency($condition)]);
	}

	/**
	 * set theCurrency details
	 * @param array $condition
	 */
	function set_currency($condition) {
		$hash_key = hash('md5', __CLASS__.'currency_converter'.json_encode($condition));
		if (isset($this->cache[$hash_key]) == false) {
			$this->cache[$hash_key] = $this->custom_db->single_table_records('currency_converter', '*', $condition, 0, 100000000, array('country' => 'ASC'));
		}
		return $hash_key;
	}

	/**
	 * 	Get Active Banks
	 */
	function get_active_bank_list($from=array('k' => 'origin', 'v' => array('en_bank_name', 'account_number')), $condition=array('status' => ACTIVE))
	{
		return magical_converter($from, $this->cache[$this->set_active_bank_list($condition)]);
	}

	/**
	 * set Active Banks details
	 * @param array $condition
	 */
	function set_active_bank_list($condition) {
		$hash_key = hash('md5', __CLASS__.'bank_payment_details'.json_encode($condition));
		if (isset($this->cache[$hash_key]) == false) {
			$this->cache[$hash_key] = $this->custom_db->single_table_records('bank_payment_details', '*', $condition, 0, 100000000, array('en_bank_name' => 'ASC'));
		}
		return $hash_key;
	}

	/**
	 * 	Get airport code
	 */
	function get_airport_code_list()
	{
		$airport_code_list = $this->cache[$this->set_airport_code_list()];
		$code_list = '';
		if (valid_array($airport_code_list['data'])) {
			foreach ($airport_code_list['data'] as $k => $v) {
				$code_list[$v['city'].':('.$v['code'].')'] = $v['city'].'('.$v['code'].')';
			}
		}
		return $code_list;
	}

	/**
	 * set airport details
	 * @param array $condition
	 */
	function set_airport_code_list() {
		$hash_key = hash('md5', __CLASS__.'city_code_list');
		if (isset($this->cache[$hash_key]) == false) {
			$this->cache[$hash_key] = $this->custom_db->single_table_records('city_code_list', '*', array(), 0, 100000000, array('priority_list' => 'DESC','city' => 'ASC'));
		}
		return $hash_key;
	}
	
	/**
	 * 	Get airport code
	 */
	function get_active_social_network_list()
	{
		$data_list = '';
		$raw_data = '';
		return $this->cache[$this->set_active_social_network_list()];
	}

	/**
	 * set airport details
	 * @param array $condition
	 */
	function set_active_social_network_list() {
		$hash_key = hash('md5', 'social_login');
		if (isset($this->cache[$hash_key]) == false) {
			$data = $this->custom_db->single_table_records('social_login', '*', array('domain_origin' => get_domain_auth_id()), 0, 10);
			$data_list = array();
			foreach ($data['data'] as $k => $v) {
				$data_list[$v['social_login_name']] = $v;
			}
			$this->cache[$hash_key] = $data_list;
		}
		return $hash_key;
	}
	/**
	 * Balu A
	 * get the airline details
	 * @param array $from array('k' => 'id', 'v' => 'name')
	 * @param array $condition
	 */
	function get_airline_list($from=array('k' => 'code', 'v' => 'name'), $condition=array('name !=' => ''))
	{
		//Balu A
		return magical_converter($from, $this->cache[$this->set_airline_list($condition)]);
	}

	/**
	 * set the Airline details
	 * @param array $condition
	 */
	function set_airline_list($condition) {
		$hash_key = hash('md5', __CLASS__.'airline_list'.json_encode($condition));
		if (isset($this->cache[$hash_key]) == false) {
			$this->cache[$hash_key] = $this->custom_db->single_table_records('airline_list', '*', $condition, 0, 100000000, array('name' => 'ASC'));
		}
		return $hash_key;
	}

	/*
	 * Return Agent List with Agent ID
	 */
	function get_agent_list_with_id_old($from=array('k' => 'user_id', 'v' => 'agency_name'), $condition=array('agency_name !=' => '', 'user_type' => B2B_USER))
	{
		return magical_converter($from, $this->cache[$this->set_agent_list($condition)]);
	}
	function get_agent_list_with_id($condition=array())
	{
		$agent_list = $this->cache[$this->set_agent_list($condition)];
		$code_list = array();
		if (valid_array($agent_list['data'])) {
			foreach ($agent_list['data'] as $k => $v) {
			$code_list[$v['user_id']] = ucfirst(strtolower($v['agency_name'])).' '.provab_decrypt($v['uuid']).'';
			}
		}
		return $code_list;
	}

	/**
	 * set the country details
	 * @param array $condition
	 */
	function set_agent_list($condition) {
		$hash_key = hash('md5', __CLASS__.'agent_list'.json_encode($condition));
		if (isset($this->cache[$hash_key]) == false) {
			$this->cache[$hash_key] = $this->custom_db->single_table_records('user', '*', $condition, 0, 100000000, array('agency_name' => 'ASC'));
		}
		return $hash_key;
	}

	/**
* Get airport code
*/

	/**
	 * get the continent details
	 *
	 * @param array $from
	 *        	array('k' => 'origin', 'v' => 'name')
	 * @param array $condition
	 */
	function get_airport_city_name($condition = array()) {
		return $this->cache [$this->set_airport_city_name ( $condition )];
	}
	
	/**
	 * set the continet details
	 *
	 * @param array $condition
	 */
	function set_airport_city_name($condition) {
		$hash_key = hash ( 'md5', __CLASS__ . 'flight_airport_list' . json_encode ( $condition ) );
		if (isset ( $this->cache [$hash_key] ) == false) {
			$data = $this->custom_db->single_table_records ( 'flight_airport_list', '*', $condition );
			if ($data ['status'] == SUCCESS_STATUS) {
				$this->cache [$hash_key] = (COUNT ( $data ['data'] ) > 1) ? $data ['data'] : $data ['data'] [0];
			} else {
				$this->cache [$hash_key] = FAILURE_STATUS;
			}
		}
		return $hash_key;
	}

}