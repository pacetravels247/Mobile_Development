<?php
/**
 * Provab APPLICATION Class
 *
 * Handle APPLICATION CUSTOM Details
 *
 * @package	Provab
 * @subpackage	provab
 * @category	Libraries
 * @author		Arjun J<arjun.provab@gmail.com>
 * @link		http://www.provab.com
 */
class Application_Logger {
	/**
	 *
	 * @param array $query_details - array having details of query
	 */
	public function __construct()
	{

	}

	function registration($username, $details='', $user_id, $action_query_string=array(), $attr=array())
	{
		$event_origin = 'EID001';
		if (empty($details) == true) {
			$details = $username.' Has Registered With Us';
		}
		$this->log_time_line($event_origin, $details, $action_query_string, $attr, $user_id);
	}

	function profile_update($username, $details='', $action_query_string=array(), $attr=array(), $user_id=0)
	{
		$event_origin = 'EID002';
		if (empty($details) == true) {
			$details = $username.' Updated Profile Details';
		}
		$this->log_time_line($event_origin, $details, $action_query_string, $attr, $user_id);
	}

	function change_password($username, $details='')
	{
		$event_origin = 'EID003';
		if (empty($details) == true) {
			$details = $username.' Changed Password';
		}
		$this->log_time_line($event_origin, $details);
	}

	function login($username, $user_origin, $action_query_string, $details='')
	{
		$event_origin = 'EID005';
		if (empty($details) == true) {
			$details = $username.' Login To System';
		}
		$this->log_time_line($event_origin, $details, $action_query_string, array(), $user_origin);
	}

	function logout($username, $user_origin, $action_query_string, $details='')
	{
		$event_origin = 'EID006';
		if (empty($details) == true) {
			$details = $username.' Logout Of System';
		}
		$this->log_time_line($event_origin, $details, $action_query_string, array(), $user_origin);
	}

	function email_subscription($username)
	{
		$event_origin = 'EID004';//'Email Subscription';
		if (empty($details) == true) {
			$details = $username.' Registered For News Letter';
		}
		$this->log_time_line($event_origin, $details);
	}
	//money balance
	function balance_status($details)
	{
		$event_origin = 'EID007';
		$this->log_time_line($event_origin, $details);
	}
	//booking
	function transaction_status($details, $action_query_string)
	{
		$event_origin = 'EID008';
		$this->log_time_line($event_origin, $details, $action_query_string);
	}

	function account_status($details, $action_query_string)
	{
		$event_origin = 'EID009';
		$this->log_time_line($event_origin, $details, $action_query_string);
	}

	function api_status($details)
	{
		$event_origin = 'EID010';
		$this->log_time_line($event_origin, $details);
	}

	/**
	 *
	 * @param unknown_type $title
	 * @param unknown_type $details
	 * @param unknown_type $action_query_string array('k1' => 'v1');
	 * @param unknown_type $attr
	 */
	function log_time_line($event_origin, $event_details, $action_query_string=array(), $attr=array(), $user_id=0)
	{
		
		return '';//check later
		$details = unserialize(file_get_contents('http://ip-api.com/php'));
		$data['domain_origin'] = get_domain_auth_id();
		$data['event_origin'] = $event_origin;
		$data['event_description'] = $event_details;
		$data['location'] = $details['regionName'].', '.$details['timezone'];
		$data['internal_ip'] = $_SERVER['REMOTE_ADDR'];
		$data['external_ip'] = $details['query'];
		$data['city'] = $details['city'];
		$data['country'] = $details['country'];
		$data['country_code'] = $details['countryCode'];
		$data['lat'] = $details['lat'];
		$data['lon'] = $details['lon'];
		if (empty($user_id) == true) {
			$data['created_by_id'] = intval(@$GLOBALS['CI']->entity_user_id);
		} else {
			$data['created_by_id'] = intval($user_id);
		}
		$data['created_datetime'] = date('Y-m-d H:i:s');
		if (valid_array($action_query_string) == true) {
			$data['action_query_string'] = json_encode(array('q_params' => array_merge($action_query_string, array('q_search_type' => 'wildcard'))));
		}
		$attributes = array('isp' => $details['isp'], 'user_agent' => $_SERVER['HTTP_USER_AGENT']);
		if (valid_array($attr) == true) {
			$attributes = array_merge($attributes, $attr);
		}
		$data['attributes'] = json_encode($attributes);
		$CI = get_instance();
		$CI->custom_db->insert_record('timeline', $data);
	}

	/**
	 * Get Events from time line
	 * @param date $start
	 * @param date $end
	 *
	 * NOTE : Start and End To Go Back In Reverse Order
	 *
	 * Top Offset and Bottom Offset
	 */
	function get_events($start, $event_limit, $cond=array())
	{
		$c_filter = '';
		$CI = get_instance();
		if (is_domain_user() == true) {
			$c_filter .= ' AND TL.domain_origin = '.get_domain_auth_id();
		}
		if (valid_array($cond)) {
			$c_filter .= $CI->custom_db->get_custom_condition($cond);
		}
		if (is_app_user()) {
			$c_filter .= ' AND TL.created_by_id = '.intval($CI->entity_user_id);
		}
		return $CI->db->query('select TL.*, TLE.event_title, TLE.event_icon from timeline TL JOIN timeline_master_event TLE where TL.event_origin=TLE.origin '.$c_filter.' order by TL.origin desc limit '.$start.','.$event_limit)->result_array();
	}

	/**
	 * Return all the event summary day wise
	 */
	function day_summary($cond=array())
	{
		if (is_logged_in_user() == true) {
			$c_filter = '';
			$CI = get_instance();
			if (is_domain_user() == true) {
				$c_filter .= ' AND TL.domain_origin = '.get_domain_auth_id();
			}
			if (is_app_user()) {
				$c_filter .= ' AND TL.created_datetime = '.intval($CI->entity_user_id);
			}
			if (valid_array($cond)) {
				$c_filter .= $CI->custom_db->get_custom_condition($cond);
			}
			return $CI->db->query('select count(*) as total, TL.origin as event_origin, TLE.* from timeline_master_event TLE LEFT JOIN timeline TL ON TLE.origin=TL.event_origin '.$c_filter.' group by TLE.origin order by TLE.event_title')->result_array();
		}
	}
}
