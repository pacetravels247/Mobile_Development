<?php
/**
 * Provab APPLICATION Class
 *
 * Handle APPLICATION CUSTOM Details
 *
 * @package	Provab
 * @subpackage	provab
 * @category	Libraries
 * @author		Balu A<balu.provab@gmail.com>
 * @link		http://www.provab.com
 */
class Application_Logger {
	/**
	 *
	 * @param array $query_details - array having details of query
	 */
	private $CI;
	public function __construct()
	{
		$this->CI = &get_instance();
	}

	function registration($username, $details='', $user_id, $action_query_string=array(), $attr=array(), $user_ids = array())
	{
		$event_origin = 'EID001';
		if (empty($details) == true) {
			$details = $username.' Has Registered With Us';
		}
		$time_line_origin = $this->log_time_line($event_origin, $details, $action_query_string, $attr, $user_id);
		$this->map_user_notification($time_line_origin, $user_ids);
	}

	function profile_update($username, $details='', $action_query_string=array(), $attr=array(), $user_id=0, $user_ids = array())
	{
		$event_origin = 'EID002';
		if (empty($details) == true) {
			$details = $username.' Updated Profile Details';
		}
		$time_line_origin = $this->log_time_line($event_origin, $details, $action_query_string, $attr, $user_id);
		$this->map_user_notification($time_line_origin, $user_ids);
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
		//FIXME - This will be fixed by Balu A :)
		return '';
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

	function check_api_minimum_balance($username, $user_origin, $action_query_string, $details='')
	{
		$user_id=array();
        $event_origin = 'EID015';
		$details1 = $details['name'].' API Low Balance';
        $user_id[]=$user_origin;
		$insert_id=$this->log_time_line($event_origin, $details1, $action_query_string, array(), $user_origin);
		$this->map_user_notification($insert_id, $user_id);
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
	function transaction_status($details, $action_query_string, $user_ids = array())
	{
		$event_origin = 'EID008';
		$time_line_origin = $this->log_time_line($event_origin, $details, $action_query_string);
		$this->map_user_notification($time_line_origin, $user_ids);
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

	function balance_deposit_request($details, $action_query_string, $user_ids = array())
	{
		$event_origin = 'EID011';
		$time_line_origin = $this->log_time_line($event_origin, $details, $action_query_string);
		$this->map_user_notification($time_line_origin, $user_ids);
	}
	/* Credit Limit Request*/
	function credit_limit_request($details, $action_query_string, $user_ids = array()){
		$event_origin = 'EID012';
		$time_line_origin = $this->log_time_line($event_origin, $details, $action_query_string);
		$this->map_user_notification($time_line_origin, $user_ids);
	}
	function balance_debit_request($details, $action_query_string, $user_ids = array())
	{
		$event_origin = 'EID013';
		$time_line_origin = $this->log_time_line($event_origin, $details, $action_query_string);
		$this->map_user_notification($time_line_origin, $user_ids);
	}

	//Working here
	function group_booking_quote_update($details, $user_ids = array(), $action_query_string=array('na'))
	{
		$event_origin = 'EID014';
		$time_line_origin = $this->log_time_line($event_origin, $details, $action_query_string);
		$this->map_user_notification($time_line_origin, $user_ids);
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
		$details = json_decode(file_get_contents('https://tools.keycdn.com/geo.json'));	
		$data['domain_origin'] = get_domain_auth_id();		
		$data['event_origin'] = $event_origin;		
		$data['event_description'] = $event_details;	
		$data['location'] = @$details->data->geo->city.', '.@$details->data->geo->timezone;		
		$data['internal_ip'] = $_SERVER['REMOTE_ADDR'];		
		$data['external_ip'] = @$details->data->geo->ip;		
		$data['city'] = @$details->data->geo->city;
		$data['country'] = @$details->data->geo->country_name;
		$data['country_code'] = @$details->data->geo->country_code;
		$data['lat'] = @$details->data->geo->latitude;
		$data['lon'] = @$details->data->geo->longitude;
		if (empty($user_id) == true) {
			$data['created_by_id'] = intval(@$GLOBALS['CI']->entity_user_id);
		} else {
			$data['created_by_id'] = intval($user_id);
		}
		$data['created_datetime'] = date('Y-m-d H:i:s');
		if (valid_array($action_query_string) == true) {
			$data['action_query_string'] = json_encode(array('q_params' => array_merge($action_query_string, array('q_search_type' => 'wildcard'))));
		}
		else
		{
			$data['action_query_string'] = json_encode(array("last_request" => $_SERVER["REQUEST_URI"]));
		}
		$attributes = array('isp' => @$details->data->geo->isp, 'user_agent' => $_SERVER['HTTP_USER_AGENT']);		
		if (valid_array($attr) == true) {
			$attributes = array_merge($attributes, $attr);
		}
		$data['attributes'] = json_encode($attributes);		


		$insert_id = $this->CI->custom_db->insert_record('timeline', $data);
		/*if($_SERVER['HTTP_X_FORWARDED_FOR']=="157.51.125.165")
		        {
                 debug($insert_id);exit;
		        }*/
		
  
		return $insert_id['insert_id'];
	}
	/*
	function log_time_line($event_origin, $event_details, $action_query_string=array(), $attr=array(), $user_id=0)
	{
		$details = unserialize(file_get_contents('http://ip-api.com/php'));		
		$data['domain_origin'] = get_domain_auth_id();		
		$data['event_origin'] = $event_origin;		
		$data['event_description'] = $event_details;	
		$data['location'] = @$details['regionName'].', '.@$details['timezone'];		
		$data['internal_ip'] = $_SERVER['REMOTE_ADDR'];		
		$data['external_ip'] = $details['query'];		
		$data['city'] = @$details['city'];
		$data['country'] = @$details['country'];
		$data['country_code'] = @$details['countryCode'];
		$data['lat'] = @$details['lat'];
		$data['lon'] = @$details['lon'];
		if (empty($user_id) == true) {
			$data['created_by_id'] = intval(@$GLOBALS['CI']->entity_user_id);
		} else {
			$data['created_by_id'] = intval($user_id);
		}
		$data['created_datetime'] = date('Y-m-d H:i:s');
		if (valid_array($action_query_string) == true) {
			$data['action_query_string'] = json_encode(array('q_params' => array_merge($action_query_string, array('q_search_type' => 'wildcard'))));
		}
		$attributes = array('isp' => @$details['isp'], 'user_agent' => $_SERVER['HTTP_USER_AGENT']);		
		if (valid_array($attr) == true) {
			$attributes = array_merge($attributes, $attr);
		}
		$data['attributes'] = json_encode($attributes);		
		$insert_id = $this->CI->custom_db->insert_record('timeline', $data);		
		return $insert_id['insert_id'];
	}*/
	/**
	 * Balu A
	 * Add the Notifications to the Users
	 * @param unknown_type $time_line_origin
	 * @param unknown_type $user_ids
	 */
	private function map_user_notification($time_line_origin, $user_ids)
	{
		
		
		if(valid_array($user_ids) == true) {
			$time_line_origin = intval($time_line_origin);
			foreach($user_ids as $k => $v) {
				$insert_data = array();
				$insert_data['timeline_fk'] = $time_line_origin;
				$insert_data['user_id'] = intval($v);
				$this->CI->custom_db->insert_record('timeline_event_user_map', $insert_data);


			}
		}
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
		if (is_domain_user() == true) {
			$c_filter .= ' AND TL.domain_origin = '.get_domain_auth_id();
		}
		if (valid_array($cond)) {
			$c_filter .= $this->CI->custom_db->get_custom_condition($cond);
		}
		if (is_app_user()) {
			$c_filter .= ' AND TL.created_by_id = '.intval($this->CI->entity_user_id);
		}
		$query = 'select TL.*, TLE.event_title, TLE.event_icon 
				from timeline TL 
				JOIN timeline_master_event TLE 
				where TL.event_origin=TLE.origin '.$c_filter.' order by TL.origin desc limit '.$start.','.$event_limit;
		return $this->CI->db->query($query)->result_array();
	}
	/**
	 * Return all the event summary day wise
	 */
	function day_summary($cond=array())
	{
		if (is_logged_in_user() == true) {
			$c_filter = '';
			if (is_domain_user() == true) {
				$c_filter .= ' AND TL.domain_origin = '.get_domain_auth_id();
			}
			if (is_app_user()) {
				$c_filter .= ' AND TL.created_datetime = '.intval($this->CI->entity_user_id);
			}
			if (valid_array($cond)) {
				$c_filter .= $this->CI->custom_db->get_custom_condition($cond);
			}
			return $this->CI->db->query('select count(*) as total, TL.origin as event_origin, TLE.* from timeline_master_event TLE LEFT JOIN timeline TL ON TLE.origin=TL.event_origin '.$c_filter.' group by TLE.origin order by TLE.event_title')->result_array();
		}
	}
	/**
	 * Balu A
	 * Events Notifications
	 * @param date $start
	 * @param date $end
	 * Top Offset and Bottom Offset
	 */
	function get_events_notification($start, $event_limit, $cond=array(), $count=false)
	{
		$c_filter = '';
		if (is_domain_user() == true) {
			$c_filter .= ' AND TL.domain_origin = '.get_domain_auth_id();
		}
		if (valid_array($cond)) {
			$c_filter .= $this->CI->custom_db->get_custom_condition($cond);
		}
		if(!$count) {
			$query = 'select TL.*, TLE.event_title, TLE.event_icon 
					from timeline TL 
					JOIN timeline_master_event TLE
					JOIN timeline_event_user_map TEU ON TEU.timeline_fk=TL.origin
					JOIN user U on U.user_id=TEU.user_id and TEU.user_id='.intval($this->CI->entity_user_id).'
					where TL.event_origin=TLE.origin '.$c_filter.' order by TL.origin desc limit '.$start.','.$event_limit;
			return $this->CI->db->query($query)->result_array();
		} else {
			$query = 'select count(*) as total 
				from timeline TL
				JOIN timeline_master_event TLE
				JOIN timeline_event_user_map TEU ON TEU.timeline_fk=TL.origin
				JOIN user U on U.user_id=TEU.user_id and TEU.user_id='.intval($this->CI->entity_user_id).'
				where TL.event_origin=TLE.origin '. $c_filter.' group by TL.origin';
				return $this->CI->db->query($query)->row();
		}
	}
	/**
	 * Balu A
	 * Returns Active Notification Count
	 */
	function active_notifications_count($cond=array())
	{
		$c_filter = '';
		if (is_domain_user() == true) {
			$c_filter .= ' AND TL.domain_origin = '.get_domain_auth_id();
		}
		if (valid_array($cond)) {
			$c_filter .= $this->CI->custom_db->get_custom_condition($cond);
		}
		$query = 'select count(*) as active_notification_count 
				from timeline TL 
				JOIN timeline_master_event TLE
				JOIN timeline_event_user_map TEU ON TEU.timeline_fk=TL.origin
				JOIN user U on U.user_id=TEU.user_id and TEU.user_id='.intval($this->CI->entity_user_id).'
				where TL.event_origin=TLE.origin and TEU.viewed_datetime IS NULL '. $c_filter.' group by TEU.user_id';
		$total_records = $this->CI->db->query($query)->row_array();
		return intval(@$total_records['active_notification_count']);
	}
	/**
	 * Balu A
	 * Disables the Active Notification
	 * @param unknown_type $cond
	 */
	function disable_active_event_notification($cond=array())
	{
		$c_filter = '';
		if (is_domain_user() == true) {
			$c_filter .= ' AND TL.domain_origin = '.get_domain_auth_id();
		}
		if (valid_array($cond)) {
			$c_filter .= $this->CI->custom_db->get_custom_condition($cond);
		}
		$temp_query = '	update user u1
					inner join user u2
					on u2.user_id = u1.user_id
					set u1.user_name = u2.email';
		
		$query = 'update timeline_event_user_map TEU1
				inner join timeline_event_user_map TEU2 on TEU2.origin = TEU1.origin
				JOIN timeline TL
				JOIN timeline_master_event TLE
				JOIN timeline_event_user_map TEU3 ON TEU3.timeline_fk=TL.origin
				JOIN user U on U.user_id=TEU3.user_id and TEU3.user_id='.intval($this->CI->entity_user_id).'
				set TEU1.viewed_datetime="'.db_current_datetime().'"
				where TEU3.viewed_datetime IS NULL AND TEU2.origin=TEU3.origin AND TL.event_origin=TLE.origin '.$c_filter.' ';
		return $this->CI->db->query($query);
	}
	function package_balance_request($details, $action_query_string, $user_ids = array())	
	{	
		$event_origin = 'EID015';	
		$time_line_origin = $this->log_time_line($event_origin, $details, $action_query_string);	
		$this->map_user_notification($time_line_origin, $user_ids);	
	}
}
