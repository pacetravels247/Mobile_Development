<?php
/**
 * Library which has cache functions to get data
 *
 * @package    Provab Application
 * @subpackage Travel Portal
 * @author     Arjun J<arjunjgowda260389@gmail.com> :p :p :p
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
	 * get the country details
	 * @param array $from array('k' => 'id', 'v' => 'name')
	 * @param array $condition
	 */
	function get_country_list($from=array('k' => 'origin', 'v' => 'name'), $condition=array('name !=' => ''))
	{
		return magical_converter($from, $this->cache[$this->set_country_list($condition)]);
	}

	/**
	 * get the country details
	 * @param array $from array('k' => 'id', 'v' => 'name')
	 * @param array $condition
	 */
	function get_iso_country_list($from=array('k' => 'origin', 'v' => 'name'), $condition=array('name !=' => '', 'iso_country_code !=' => ''))
	{
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
	 * get the country details
	 * @param array $from array('k' => 'id', 'v' => 'name')
	 * @param array $condition
	 */
	function get_iso_country_code($from=array('k' => 'origin', 'v' => 'name'), $condition=array('iso_country_code !=' => '', 'country_code !=' => ''))
	{
		return magical_converter($from, $this->cache[$this->set_country_list($condition)]);
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
			$condition = array('user_type !=' => '', 'origin =' =>  ADMIN);
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
	 * get the bus station details
	 * @param array $from array('k' => 'origin', 'v' => 'name')
	 * @param array $condition
	 */
	function get_bus_station_list($from=array('k' => 'station_id', 'v' => 'name'), $condition=array('name !=' => ''))
	{
		return magical_converter($from, $this->cache[$this->set_bus_station_list($condition)]);
	}

	/**
	 * set the continet details
	 * @param array $condition
	 */
	function set_bus_station_list($condition) {
		$hash_key = hash('md5', __CLASS__.'bus_stations'.json_encode($condition));
		if (isset($this->cache[$hash_key]) == false) {
			$this->cache[$hash_key] = $this->custom_db->single_table_records('bus_stations', '*', $condition);
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
		return magical_converter($from, $this->set_city_list($condition));
	}

	/**
	 * set the city details
	 * @param array $condition
	 */
	function set_city_list($condition) {
		//$hash_key = hash('md5', __CLASS__.'api_city_list'.json_encode($condition));
		return $this->custom_db->single_table_records('api_city_list', '*', $condition);
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
	 * 	Get Active Booking Sources
	 */
	function get_active_api_booking_source($condition=array(array('name','!=','""')))
	{
/*		debug($this->cache[$this->set_active_api_booking_source($condition)]); exit;*/
		return $this->cache[$this->set_active_api_booking_source($condition)];
	}

	/**
	 * set the active booking source details
	 * @param array $condition
	 */
	function set_active_api_booking_source($condition) {
		$condition = $this->custom_db->get_custom_condition($condition );
		$hash_key = hash('md5', __CLASS__.'booking_source_api_config'.json_encode($condition)); 
		if (isset($this->cache[$hash_key]) == false) {
			$query = 'select AC.origin as config_table_origin,BS.name,BS.source_id as booking_source, BS.authentication as check_auth from booking_source BS
						join api_config AC on AC.booking_source_fk=BS.origin
						join domain_api_map DAM on DAM.booking_source_fk=BS.origin
						join domain_list DL on DL.origin=DAM.domain_list_fk
						where BS.booking_engine_status='.ACTIVE.' and AC.status='.ACTIVE.$condition.' order by BS.origin DESC';
            //echo $query;exit;  
			$this->cache[$hash_key] = $this->db->query($query)->result_array();
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
	 * 	Get Currencies
	 */
	function get_active_bank_list($from=array('k' => 'origin', 'v' => array('en_bank_name', 'account_number')), $condition=array('status' => ACTIVE))
	{
		return magical_converter($from, $this->cache[$this->set_active_bank_list($condition)]);
	}

	/**
	 * set the Active Bank details
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
	function get_airport_code_list($condition)
	{
		$airport_code_list = $this->cache[$this->set_airport_code_list($condition)];
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
	function set_airport_code_list($condition) {
		$hash_key = hash('md5', __CLASS__.'city_code_list'.json_encode($condition));
		if (isset($this->cache[$hash_key]) == false) {
			$this->cache[$hash_key] = $this->custom_db->single_table_records('city_code_list', '*', $condition, 0, 100000000, array('priority_list' => 'DESC','city' => 'ASC'));
		}
		return $hash_key;
	}

	/**
	 * 	Get airport code
	 */
	function get_airline_code_list($condition=array())
	{
		$airport_code_list = $this->cache[$this->set_airline_code_list($condition)];
		$code_list = array();
		if (valid_array($airport_code_list['data'])) {
			foreach ($airport_code_list['data'] as $k => $v) {
				$code_list[$v['code']] = ucfirst(strtolower($v['name']));
			}
		}
		return $code_list;
	}

	/**
	 * set airport details
	 * @param array $condition
	 */
	function set_airline_code_list($condition) {
		$hash_key = hash('md5', __CLASS__.'airline_list'.json_encode($condition));
		if (isset($this->cache[$hash_key]) == false) {
			$this->cache[$hash_key] = $this->custom_db->single_table_records('airline_list', '*', $condition, 0, 100000000, array('name' => 'ASC','code' => 'ASC'));
		}
		return $hash_key;
	}
/**
	 * Jaganath
	 * get the Admin Base Currency
	 */
	function get_domain_base_currency()
	{
		return $this->cache[$this->set_domain_base_currency()];
	}

	/**
	 * Set the Admin Base Currency
	 * @param array $condition
	 */
	function set_domain_base_currency() {
		$hash_key = hash('md5', __CLASS__.'domain_base_currency');
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
	 * get the continent details
	 *
	 * @param array $from
	 *        	array('k' => 'origin', 'v' => 'name')
	 * @param array $condition        	
	 */
	function get_airport_details($airport_code) {
		return $this->cache [$this->set_airport_details ( $airport_code )];
	}
	
	/**
	 * set the continet details
	 *
	 * @param array $condition        	
	 */
	function set_airport_details($airport_code) {
		$hash_key = hash ( 'md5', __CLASS__ . 'a_details' . $airport_code);
		if (isset ( $this->cache [$hash_key] ) == false) {
			$query = 'select FA.* from flight_airport_list FA 
					where FA.airport_code = "' . $airport_code . '"';
			$data = $this->db->query ( $query )->row_array ();
			if (valid_array ( $data )) {
				$this->cache [$hash_key] = $data;
			} else {
				$this->cache [$hash_key] = false;
			}
		}
		return $hash_key;
	}
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
		/**
	 * set the Airline Name details
	 * 
	 * @param array $condition        	
	 */
	function get_airline_name($condition = array()) {
		return $this->cache [$this->set_airline_name ( $condition )];
	}
	/**
	 * set the Airline Name details
	 * 
	 * @param array $condition        	
	 */
	function set_airline_name($condition) {
		$hash_key = hash ( 'md5', __CLASS__ . 'airline_list' . json_encode ( $condition ) );
		if (isset ( $this->cache [$hash_key] ) == false) {
			$data = $this->custom_db->single_table_records ( 'airline_list', '*', $condition );
			if ($data ['status'] == SUCCESS_STATUS) {
				$this->cache [$hash_key] = (COUNT ( $data ['data'] ) > 1) ? $data ['data'] : $data ['data'] [0];
			} else {
				$this->cache [$hash_key] = FAILURE_STATUS;
			}
		}
		return $hash_key;
	}
	function get_travelport_flight_price_xml($search_id){
		$query = "select * from  travelport_price_xml where serach_id='".$search_id."'";
		$data  = $this->db->query($query)->result_array();
		return $data;
		//debug($data);exit;
	}
	function get_travelport_flight_price_seat_xml($search_id){
		$query = "select * from  travelport_price_xml where serach_id LIKE '".$search_id."%' order by created_date desc limit 1" ;
		 // echo $query;exit;
		$data  = $this->db->query($query)->result_array();
		return $data;
		//debug($data);exit;
	}
	function update_price_xml($price_xml, $itinerary_xml, $search_id){
		$data['price_xml'] = $price_xml;
		$data['itinerary_xml'] = $itinerary_xml;
		$update_condition = array();
		$update_condition['serach_id'] = intval($search_id);
		$this->custom_db->update_record('travelport_price_xml', $data, $update_condition);
	}
	function get_meals_travelport(){
		$query = "select * from  travelport_meals_list" ;
		// echo $query;exit;
		$data  = $this->db->query($query)->result_array();
		return $data;
	}
	function get_meals_sabre(){
		$query = "select * from  sabre_meals_list" ;
		// echo $query;exit;
		$data  = $this->db->query($query)->result_array();
		return $data;
	}
}
