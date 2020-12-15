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
	function active_config($module, $api) {
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
			$source_filter = 'BS.source_id = ' . $this->db->escape ( $api );
		}
		// Meta_course_list, booking_source, activity_source_map, api_config
		$query = 'SELECT AC.config, BS.source_id AS api FROM meta_course_list MCL, booking_source BS, activity_source_map ASM, api_config AC
		WHERE MCL.origin=ASM.meta_course_list_fk AND ASM.booking_source_fk=BS.origin AND ASM.status=' . ACTIVE . ' AND MCL.status=' . ACTIVE . '
		AND BS.origin=AC.booking_source_fk AND MCL.status=' . ACTIVE . ' AND MCL.course_id = ' . $this->db->escape ( $module ) . '
		AND ' . $source_filter . ' AND AC.status=' . ACTIVE;
		
		$result_arr = $this->db->query ( $query )->result_array ();
		if (valid_array ( $result_arr ) == true) {
			$resp = array ();
			foreach ( $result_arr as $k => $v ) {
				$resp [$v ['api']] = array (
						'config' => $v ['config'] 
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
	function active_api_config($module, $api) {
		// save in cache for a while
		$key = 'active_api_config' . DB_SAFE_SEPARATOR . $module . DB_SAFE_SEPARATOR . $api;
		
		$data = get_cache_data ( $key );
		if (valid_array ( $data ) == false) {
			$data = $this->active_config ( $module, $api );
		}
		if ($data != FAILURE_STATUS) {
			//FIXME - on live enabled below line
			//set_cache_data($key, $data, SCHEDULER_RELOAD_TIME_LIMIT);
			return $data [$api];
		} else {
			return false;
		}
	}
}
