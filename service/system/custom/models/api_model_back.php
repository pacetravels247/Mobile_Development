<?php
/**
 * Library which has generic functions to get data
 *
 * @package    Provab Application
 * @subpackage Api_Model
 * @author     Arjun J<arjunjgowda260389@gmail.com>
 * @version    V2
 */
class Api_Model extends CI_Model {
	/**
	 * Get active configuration
	 * 
	 * @param string $module
	 *        	- Code of module for which booking api config has to be loaded
	 * @param string $api
	 *        	- API for which config has to be browsed // Array or String
	 */
	function active_config($module, $api,$config_table_origin) {
		$source_filter = '';
		if (is_array ( $api ) == true) {
			// group to IN
			$tmp_api = '';
			foreach ( $api as $k => $v ) {
				$tmp_api .= $this->db->escape ( $v ) . ',';
			}
			$tmp_api = substr ( $tmp_api, 0, - 1 ); // remove last ,
			$source_filter = 'BS.source_id IN (' . $tmp_api . ')';
		} else {
			// Single value direct =
			$source_filter = 'BS.source_id = ' . $this->db->escape ( $api )  ;
			//$source_filter = 'BS.source_id = ' . $this->db->escape ( $api ) .' and AC.origin='. $this->db->escape ( $config_table_origin ) ;
		}
		// Meta_course_list, booking_source, activity_source_map, api_config
		$query = 'SELECT AC.config, BS.source_id AS api,AC.remarks FROM meta_course_list MCL, booking_source BS, activity_source_map ASM, api_config AC
		WHERE MCL.origin=ASM.meta_course_list_fk AND ASM.booking_source_fk=BS.origin AND ASM.status=' . ACTIVE . ' AND MCL.status=' . ACTIVE . '
		AND BS.origin=AC.booking_source_fk AND MCL.status=' . ACTIVE . ' AND MCL.course_id = ' . $this->db->escape ( $module ) . '
		AND ' . $source_filter . ' AND AC.status=' . ACTIVE;
		$result_arr = $this->db->query ( $query )->result_array ();

		if (valid_array ( $result_arr ) == true) {
			$resp = array ();
			foreach ( $result_arr as $k => $v ) {
				$resp [$v ['api']] = array (
						'config' => $v ['config'],
						'remarks' => $v ['remarks'], 
				);
			}
			// debug($resp);exit;
			return $resp;
		} else {
			return false;
		}
	}
	
	/**
	 * return active api config for one api only
	 * 
	 * @param string $module
	 *        	- Code of module for which booking api config has to be loaded
	 * @param string $api
	 *        	- API for which config has to be browsed // Array or String
	 */
	function active_api_config($module, $api,$config_table_origin) {
		$data = $this->active_config ( $module, $api,$config_table_origin );

		if ($data != FAILURE_STATUS) {
			return $data [$api];
		} else {
			return false;
		}
	}
	/**
	 * 
	 * Set API Session ID
	 * @param unknown_type $booking_source_fk
	 */
	public function update_api_session_id($booking_source, $session_id)
	{
		$booking_source_details = $this->db->query('select origin from booking_source where source_id="'.trim($booking_source).'"')->row_array();
		$booking_source_fk = $booking_source_details['origin'];
		$this->custom_db->update_record('api_session_id', array('session_id' => trim($session_id), 'last_updated_datetime' => db_current_datetime()), array('booking_source_fk' => intval($booking_source_fk)));
	}
	/**
	 * 
	 * Return API Session ID
	 * @param unknown_type $booking_source_fk
	 */
	public function get_api_session_id($booking_source, $session_expiry_time)
	{
		$session_id_details = $this->db->query('select ASI.session_id from api_session_id ASI
							join booking_source BS on BS.origin=ASI.booking_source_fk 
							where BS.source_id="'.$booking_source.'" and (ASI.last_updated_datetime + INTERVAL '.intval($session_expiry_time).' MINUTE) >= "'.db_current_datetime().'"')->row_array();
		if(isset($session_id_details['session_id']) == true && empty($session_id_details['session_id']) == false){
			return $session_id_details['session_id'];
		}
	}
	/**
	 * Stores Client Requests
	 */
	public  function store_client_request($request_type='', $request='')
	{
		//TODO:$this->inactive_cache_services
		if($request_type !=''){
			if(is_array($request)) {
				$request = json_encode($request);
			}
			$provab_api_request_history = array();
			$provab_api_request_history['request_type'] = $request_type;
			$provab_api_request_history['header'] = '';
			$provab_api_request_history['Tracelogs'] = '';
			$provab_api_request_history['request'] = $request;
			$provab_api_request_history['domain_origin'] = get_domain_auth_id();
			$provab_api_request_history['created_datetime'] = date('Y-m-d H:i:s');
			
			return $this->custom_db->insert_record('provab_api_request_history',$provab_api_request_history);
		}
	}
	/**
	 * Stores API Requests
	 */
	public  function store_api_request($request_type, $request, $remarks, $server_info='',$search_id=0)
	{
		//TODO:$this->inactive_cache_services
		if($request_type !=''){
			if(is_array($request)) {
				$response = json_encode($request);
			}
			$provab_api_response_history = array();
			$provab_api_response_history['request_type'] = $request_type;
			$provab_api_response_history['server_info'] = '';
			$provab_api_response_history['response'] = '';
			$provab_api_response_history['flight_api_response'] = 0;
			$provab_api_response_history['response_updated_time'] = date('Y');
			$provab_api_response_history['response_return_time'] = date('Y-m-d H:i:s');
			$provab_api_response_history['request'] = $request;
			$provab_api_response_history['remarks'] = $remarks;
            $provab_api_response_history['domain_origin'] = get_domain_auth_id();
            $provab_api_response_history['search_id'] = $search_id;
			$provab_api_response_history['created_datetime'] = date('Y-m-d H:i:s');

			return $this->custom_db->insert_record('provab_api_response_history',$provab_api_response_history);
		}
	}
	/**
	 * Stores Travelport API Requests
	 */
	public function store_api_request_booking($request_type, $request, $response, $remarks){
		//TODO:$this->inactive_cache_services
		if($request_type !=''){
			if(is_array($request)) {
				$response = json_encode($request);
			}
			$provab_api_response_history = array();
			$provab_api_response_history['request_type'] = $request_type;
			$provab_api_response_history['request'] = $request;
			$provab_api_response_history['response'] = $response;
			$provab_api_response_history['remarks'] = $remarks;
                         $provab_api_response_history['domain_origin'] = get_domain_auth_id();
			$provab_api_response_history['created_datetime'] = date('Y-m-d H:i:s');
			return $this->custom_db->insert_record('provab_api_response_history',$provab_api_response_history);
		}
	}
	/**
	 * Stores API Requests
	 */
	public  function update_api_response($response, $origin, $totaltime=0)
	{
		//TODO:$this->inactive_cache_services
		if(intval($origin) > 0){
			if(is_array($response)) {
				$response = json_encode($response);
			}
			$provab_api_response_history = array();
			$provab_api_response_history['response'] = $response;
            $provab_api_response_history['flight_api_response'] = $totaltime;
			$provab_api_response_history['response_updated_time'] = date('Y-m-d H:i:s');
			$this->custom_db->update_record('provab_api_response_history',$provab_api_response_history, array('origin' => intval($origin)));
		}
	}
	/**
	 * Checks Cache is enabled for Service 
	 * Enter description here ...
	 */
	public function inactive_client_cache_services($service_name)
	{
		$inactive_cache = array('SEARCH', 'GETCALENDARFARE', 'FARERULE');
		//$inactive_cache = array();
		if(in_array(strtoupper($service_name), $inactive_cache) == true){
			return true;
		} else {
			return false;
		}
	}
	
	public function inactive_api_cache_services()
	{
		
	}
}