<?php
/**
 * @package    Provab Application
 * @subpackage Travel Portal
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V2
 */
Class Module_Model extends CI_Model
{
	/**
	 * get complete domain details
	 */
	function domain_details($domain_origin)
	{
		$query = 'select DL.*, group_concat(DMM.meta_course_list_fk separator "'.DB_SAFE_SEPARATOR.'") as domain_modules from domain_list AS DL left join domain_module_map DMM ON DMM.domain_list_fk=DL.origin
		where DL.origin='.intval($domain_origin).' GROUP BY DL.origin';
		return $this->db->query($query)->result_array();
	}

	/**
	 * Domain Module Map Creation
	 * @param $domain_origin
	 * @param $module_origin
	 */
	function create_domain_module_map($domain_origin, $module_origin)
	{
		if (is_array($module_origin) == false) {
			$module_origin = (array)$module_origin;
		}
		$domain_module_map['domain_list_fk'] = intval($domain_origin);
		$domain_module_map['status'] = ACTIVE;
		$domain_module_map['created_by_id'] = intval($this->entity_user_id);
		$domain_module_map['created_datetime'] = date('Y-m-d H:i:s');
		foreach ($module_origin as $k => $v) {
			$domain_module_map['meta_course_list_fk'] = intval($v);
			$this->custom_db->insert_record('domain_module_map', $domain_module_map);
		}
	}
	/**
	 * Get Module List
	 * Balu A
	 */
	function module_management($pk,$course_id)
	{
		$tmp_data=$this->db->query("SELECT MCL.*,BS.origin as booking_source FROM meta_course_list MCL
		                            LEFT JOIN activity_source_map ASM ON ASM.meta_course_list_fk=MCL.origin
		                            LEFT JOIN booking_source BS ON BS.origin=ASM.booking_source_fk 
		                            where MCL.origin=$pk AND MCL.course_id='$course_id' 
		                            group by BS.origin ");
		if($tmp_data->num_rows()>0) {
			$tmp_data=$tmp_data->result_array();
			$data = array('status' => QUERY_SUCCESS, 'data' => $tmp_data);
		} else {
			$data = array('status' => QUERY_FAILURE);
		}
		return $data;
	}

	/**
	 * Balu A
	 * Booking source Details
	 */
	function get_course_list($condition='')
	{
		$filter = '';
		$filter_condition = ' ';
		if (valid_array($condition) == true) {
			$filter_condition = ' WHERE ';
			foreach ($condition as $k => $v) {
				$filter_condition .= implode($v).' and ';
			}
		}
		$filter_condition = rtrim($filter_condition, 'and ');
		$query = 'SELECT MCL.*, CONCAT(U.first_name, " ", U.last_name, "-", U.uuid) AS username, U.image as user_image,
		group_concat(BS.name separator ", ") as booking_source
		FROM meta_course_list AS MCL
		 JOIN user AS U ON MCL.created_by_id=U.user_id
		 LEFT JOIN activity_source_map ASM ON ASM.meta_course_list_fk=MCL.origin
		 LEFT JOIN booking_source BS ON BS.origin=ASM.booking_source_fk
		 '.$filter_condition.' GROUP BY MCL.course_id';
		return $this->db->query($query)->result_array();
	}

	/**
	 * Get active module list for domain
	 * @param $domain_key		unique origin key of domain
	 * @param $domain_auth_id	unique auth provab key for domain
	 */
	function get_active_module_list($domain_origin, $domain_key)
	{
		$active_module_list = array();
		$query = 'select group_concat(MCL.course_id separator "'.DB_SAFE_SEPARATOR.'") as domain_module from domain_module_map AS DMM, domain_list AS D, meta_course_list AS MCL
		WHERE DMM.domain_list_fk=D.origin AND DMM.meta_course_list_fk=MCL.origin AND D.origin='.intval($domain_origin).' AND
		D.domain_key='.$this->db->escape($domain_key).' AND DMM.status='.intval(ACTIVE).' AND D.status='.intval(ACTIVE).' AND MCL.status='.intval(ACTIVE).' GROUP BY D.origin';
		$active_module_list = $this->db->query($query)->row_array();
		// echo $query;exit;
		if (isset($active_module_list['domain_module'])) {
			$active_module_list = explode(DB_SAFE_SEPARATOR, $active_module_list['domain_module']);
		}
		return $active_module_list;
	}

	/**
	 * Get active module list for domain
	 * @param $domain_key		unique origin key of domain
	 * @param $domain_auth_id	unique auth provab key for domain
	 */
	function get_active_payment_module_list()
	{
		$active_payment_module = array();
		$query = 'select group_concat(payment_category_code separator "'.DB_SAFE_SEPARATOR.'") as payment_category
		from payment_option_list AS POL WHERE POL.status='.intval(ACTIVE);
		$active_payment_module = $this->db->query($query)->row_array();
		if (isset($active_payment_module['payment_category'])) {
			$active_payment_module = explode(DB_SAFE_SEPARATOR, $active_payment_module['payment_category']);
		}
		return $active_payment_module;
	}

	/**
	 * serialize temp booking details
	 * @param unknown_type $booking_params
	 */
	function serialize_temp_booking_record($booking_params, $module)
	{
		//debug($booking_params);exit;
		$book_id = $module.date('d-His').'-'.rand(1,1000000);
		$temp_booking['domain_list_fk']		= get_domain_auth_id();
		$temp_booking['book_id']			= $book_id;
		$temp_booking['booking_source']		= ($booking_params['booking_source']);
		$temp_booking['book_attributes']	= serialized_data($booking_params);
		$temp_booking['booking_ip']			= $_SERVER['REMOTE_ADDR'];
		$temp_booking['created_datetime']	= date('Y-m-d H:i:s');
		$temp_booking_origin = $this->custom_db->insert_record('temp_booking', $temp_booking);
		return array('book_id' => ($book_id), 'temp_booking_origin' => $temp_booking_origin['insert_id']);
	}
	
	/**
	 * get back unserialized data
	 */
	function unserialize_temp_booking_record($book_id, $temp_book_origin)
	{
		$temp_booking_details = $this->custom_db->single_table_records('temp_booking', 'domain_list_fk, booking_source, book_id, book_attributes', array('book_id' => $book_id, 'id' => $temp_book_origin));
		//echo $this->db->last_query();exit;
		//delete once accessed
		if ($temp_booking_details['status'] == SUCCESS_STATUS) {
			$temp_booking_details['data'][0]['book_attributes'] = unserialized_data($temp_booking_details['data'][0]['book_attributes']);
			$temp_booking_details['data'][0]['book_attributes']['token'] = unserialized_data($temp_booking_details['data'][0]['book_attributes']['token']);
			return $temp_booking_details['data'][0];
		} else {
			return false;
		}
	}
	/**
	 * Delete the temp booking record
	 * Enter description here ...
	 * @param unknown_type $book_id
	 * @param unknown_type $temp_book_origin
	 */
	function delete_temp_booking_record($book_id, $temp_book_origin)
	{
		$this->custom_db->delete_record('temp_booking', array('book_id' => $book_id, 'id' => $temp_book_origin));
	}
	/**
	 * 
	 * @param string $module
	 * @param string $op
	 * @param string $notification
	 */
	function log_exception($module, $op, $notification)
	{
		$data['exception_id'] = 'EID-'.time().'-'.rand(1, 100);
		$data['module'] = $module;
		$data['op'] = $op;
		$data['notification'] = $notification;
		$data['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
		$data['user_ip'] = $_SERVER['HTTP_HOST'];
		$data['domain_origin'] = get_domain_auth_id();
		$data['created_datetime'] = date('Y-m-d H:i:s');
		$this->custom_db->insert_record('exception_logger', $data);
		return $data['exception_id'];
	}
	/**
	 * 
	 * @param string $module
	 * @param string $op
	 * @param string $notification
	 */
	function flight_log_exception($module, $op, $notification)
	{
		$data['exception_id'] = 'EID-'.time().'-'.rand(1, 100);
		$data['module'] = $module;
		$data['op'] = $op;
		$data['notification'] = $notification;
		$data['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
		$data['user_ip'] = $_SERVER['HTTP_HOST'];
		$data['domain_origin'] = get_domain_auth_id();
		$data['created_datetime'] = date('Y-m-d H:i:s');
		$this->custom_db->insert_record('exception_logger', $data);
		$exception = array('message' => $notification,
							'op' => $op,
							'exception_id' => $data['exception_id']);
		return $exception;
	}
	function get_manage_buses($bs, $is_active){ 
		return $this->db->get_where('manage_buses', array("booking_source"=>$bs, "is_active"=>$is_active))->result_array();
	}
}