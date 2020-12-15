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
	 *  Getting sms checkpoint details
	 */
	function get_sms_checkpoint()
	{
		$query = 'select * from sms_checkpoint';
		return $this->db->query($query)->result_array();
	}
	/**
	 * update sms_checkpoint_status
	 */
	function update_sms_checkpoint_status($status, $condition)
	{
		$data = array(
                   'status' => $status
            		);
			$this->db->where('condition', $condition);
			$this->db->update('sms_checkpoint', $data); 
	}
	/**
	 * Domain list
	 */
	function get_module_list()
	{
		$query = 'select * from active_modules';
		return $this->db->query($query)->result_array();
	}
	/**
	 * update module_status
	 */
	function update_module_status($status, $condition)
	{
		$data = array(
				'status' => $status
		);
		$this->db->where('origin', $condition);
		$this->db->update('active_modules', $data);
	}
	/**
	 * update social_link
	 */
	function update_social_link_status($status, $condition)
	{
		$data = array(
				'status' => $status
		);
		$this->db->where('origin', $condition);
		$this->db->update('social_links', $data);
	}
	function update_social_url($url, $id)
	{
		$data = array(
				'url_link' => $url
		);
		$this->db->where('origin', $id);
		$this->db->update('social_links', $data);
		//echo $this->db->last_query();exit;
	}
	function update_social_config($url, $id)
	{
		$data = array(
				'config' => $url
		);
		$this->db->where('origin', $id);
		$this->db->update('social_login', $data);
		echo $this->db->last_query();exit;
	}
	/**
	 * update social_login /status
	 */
	function update_social_login_status($status, $condition)
	{
		$data = array(
				'status' => $status
		);
		$this->db->where('origin', $condition);
		$this->db->update('social_login', $data);
	}
	function update_social_login_name($url, $id)
	{
		$data = array(
				'social_login_name' => $url
		);
		$this->db->where('origin', $id);
		$this->db->update('social_login', $data);
	}
	/**
	 * Update Hotel Top Destination
	 */
	function update_top_destination($status,$origin)
	{
		$data = array(
				'top_destination' => $status
		);
		$this->db->where('origin', $origin);
		$this->db->update('all_api_city_master', $data);
	}
	/**
	 * Update Bus Top Destination
	 */
	function update_bus_top_destination($status,$origin)
	{
		$data = array(
				'status' => $status
		);
		$this->db->where('origin', $origin);
		$this->db->update('bus_top_destination', $data);
	}
	/**
	 * Update Flight Top Destination
	 */
	function update_flight_top_destination($status,$origin)
	{
		$data = array(
				'status' => $status
		);
		$this->db->where('origin', $origin);
		$this->db->update('top_flight_destinations', $data);
	}
	/**
	 * Balu A
	 * Booking source Details
	 */
	function get_course_list($condition='', $special=false)
	{
		$filter = '';
		$filter_condition = ' WHERE 1=1 and ';
//	if (is_domain_user() == true) {
			$filter_condition .= ' ASM.domain_origin='.intval(get_domain_auth_id()).' and ';
//		}
		if (valid_array($condition) == true) {
			foreach ($condition as $k => $v) {
				if(!$special)
					$filter_condition .= implode($v).' and ';
				else
					$filter_condition .= $v.' and ';
			}
		}
		$filter_condition = rtrim($filter_condition, 'and ');
		$query = 'SELECT MCL.*, CONCAT(U.first_name, " ", U.last_name, "-", U.uuid) AS username, U.image as user_image,
		group_concat(BS.name separator "'.DB_SAFE_SEPARATOR.'") as booking_source, group_concat(BS.origin separator "'.DB_SAFE_SEPARATOR.'") as bs_origin,
		group_concat(concat(BS.name , " ", ASM.status) separator "'.DB_SAFE_SEPARATOR.'") as asm_status_label,
		group_concat(BS.source_id separator "'.DB_SAFE_SEPARATOR.'") as booking_source_id, group_concat(ASM.status separator "'.DB_SAFE_SEPARATOR.'") as asm_status
		FROM meta_course_list AS MCL
		 JOIN user AS U ON MCL.created_by_id=U.user_id
		 LEFT JOIN activity_source_map ASM ON ASM.meta_course_list_fk=MCL.origin
		 LEFT JOIN booking_source BS ON BS.origin=ASM.booking_source_fk
		'.$filter_condition.' GROUP BY MCL.course_id order by MCL.priority_number';
		//echo $query;exit;
		return $this->db->query($query)->result_array();
	}

	function get_booking_source_with_comm($condition='')
	{
		$filter_condition='';
		if (valid_array($condition) == true) {
			foreach ($condition as $k => $v) {
				$filter_condition .= $v.' and ';
			}
		}
		$filter_condition = rtrim($filter_condition, 'and ');
		$query = 'SELECT *, BS.origin AS bs_origin, BS.source_id AS bs_id, BFCD.origin AS 
		comm_origin FROM booking_source BS INNER JOIN b2b_flight_commission_details AS 
		BFCD on BFCD.booking_source_origin=BS.origin and BS.has_specific_commission=1 and '.
		$filter_condition;
		//exit($query);
		return $this->db->query($query)->result_array();
	}

	function get_booking_sources($condition='')
	{
		$filter_condition='WHERE ';
		if (valid_array($condition) == true) {
			foreach ($condition as $k => $v) {
				$filter_condition .= $v.' and ';
			}
		}
		$filter_condition = rtrim($filter_condition, 'and ');
		$query = 'SELECT * FROM booking_source '.$filter_condition;
		//exit($query);
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
		if (isset($active_module_list['domain_module'])) {
			$active_module_list = explode(DB_SAFE_SEPARATOR, $active_module_list['domain_module']);
		}
		
		return $active_module_list;
	}
	/**
	 * Balu A
	 */
	public function active_module_name_list($domain_origin, $domain_key)
	{
		$active_module_list = array();
		$active_module_names = array();
		$query = 'select group_concat(concat(MCL.course_id,"||",MCL.name) separator "'.DB_SAFE_SEPARATOR.'") as domain_module_name from domain_module_map AS DMM, domain_list AS D, meta_course_list AS MCL
		WHERE DMM.domain_list_fk=D.origin AND DMM.meta_course_list_fk=MCL.origin AND D.origin='.intval($domain_origin).' AND
		D.domain_key='.$this->db->escape($domain_key).' AND DMM.status='.intval(ACTIVE).' AND D.status='.intval(ACTIVE).' AND MCL.status='.intval(ACTIVE).' GROUP BY D.origin';
		$active_module_list = $this->db->query($query)->row_array();
		if (isset($active_module_list['domain_module_name'])) {
			$active_module_list = explode(DB_SAFE_SEPARATOR, $active_module_list['domain_module_name']);
			foreach($active_module_list as $k => $v) {
				$module_details = explode('||', $v);
				$active_module_names[$module_details[0]] = $module_details[1];
			}
		}
		return $active_module_names;
	}
	/**
	 * Balu A
	 */
	public function promocode_module_options()
	{
		$promocode_module_options = array();
		$domain_origin = $this->session->userdata ( DOMAIN_AUTH_ID );
		$domain_key = base64_decode ( $this->session->userdata ( DOMAIN_KEY ) );
		$active_module_names = $this->active_module_name_list($domain_origin, $domain_key);
		$inactive_module = array(META_PACKAGE_COURSE);
		
		foreach($active_module_names as $k => $v) {
			
			if(in_array($k, $inactive_module) == false) {
				switch($k){
					case META_AIRLINE_COURSE:
						$module_name_value = 'flight';
						break;
					case META_ACCOMODATION_COURSE:
					$module_name_value = 'hotel';
					break;
					case META_BUS_COURSE:
					$module_name_value = 'bus';
					break;
					case META_SIGHTSEEING_COURSE:
					$module_name_value = 'activities';					
					break;
					case META_TRANSFERV1_COURSE:
					$module_name_value = 'transfers';					
					break;

					case META_CAR_COURSE:
					$module_name_value = 'car';
					break;
				}
			
				$promocode_module_options[$module_name_value] = $v;
				;
			}
		}
		
		return $promocode_module_options;
	}
	/**
	 * Balu A
	 * @param $condition
	 * @param $count
	 * @param $offset
	 * @param $limit
	 */
	function promo_code_list($condition=array(), $count=false, $offset=0, $limit=100000000000)
	{
		$condition = $this->custom_db->get_custom_condition($condition);
		if(!$count) {
			$query = 'select * from promo_code_list where 1=1'.' '.$condition. ' order by status desc,origin desc limit '.$offset.', '.$limit;
			return $this->db->query($query)->result_array();
		} else {
			$query = 'select count(origin) as total_records from promo_code_list where 1=1'.' '.$condition;
			$data = $this->db->query($query)->row_array();
			return $data['total_records'];
		}
	}
	/**
	 * Balu A
	 * Check Promo Codes, is exists already or not?
	 */
	function is_unique_promocode($promo_code)
	{
		$query = 'select * from promo_code_list where promo_code="'.trim($promo_code).'"';
		return $this->db->query($query)->row_array();
	}
	/**
	 * Balu A
	 */
	function auto_suggest_promo_code($chars, $limit=15)
	{
		$query = 'select promo_code, module from promo_code_list where promo_code like "%'.trim($chars).'%"';
		return $this->db->query($query)->result_array();
	}
	public function delete_promo_code($origin) {
	
		$this->db->where ( 'origin', $origin );
		$this->db->delete ( 'promo_code_list' );
	}
	/**
	 * Anitha G
	 *
	 * Sub Admin Privillages
	 */
	
	function get_user_privillages(){
		$user = $this->entity_user_id;
		$user_type = $this->entity_user_type;
		$query = 'select PL.privilege_key,PL.url from privilege_list_new PL JOIN privileges AS P ON P.p_no = PL.origin where P.user_type = '.$user_type;
		 //echo $query;exit;
		$privileges = $this->db->query($query)->result_array($query);
		//foreach ()
			
		// debug($privileges);exit;		
		return $privileges;
	}

	function get_quota_counter_list()
	{
		$this->db->select("qc.id AS qc_id, qc.airline_code AS qc_a_code, qc.booking_source_id AS 
							qc_bs_id, a.name AS a_name, bs.name AS bs_name, qc.given_quota AS 
							qc_quota, qc.consumed_quota AS qc_consumed_quota, qc.type AS qc_type")
							->from("quota_counter qc")
							->join("airline_list a", "qc.airline_code = a.code")
							->join("booking_source bs", "qc.booking_source_id = bs.source_id");
		$query = $this->db->get();
		return $query->result_array();
	}

	function get_manage_airlines(){
		return $this->db->get('manage_airlines')->result_array();
	}
	
	function get_manage_buses(){
		return $this->db->get('manage_buses')->result_array();
	}

	function get_flight_suppliers($module='')
	{	
		switch ($module) {
			case 'flight':
				$course_id = META_AIRLINE_COURSE;
				break;
			case 'hotel':
				$course_id = META_ACCOMODATION_COURSE;
				break;
			case 'bus':
				$course_id = META_BUS_COURSE;
				break;
		}
		$this->db->where('meta_course_list_id', $course_id);
		$this->db->where('booking_engine_status', 1);
		return $this->db->get('booking_source')->result_array();
	}
	function get_pax_count($condition=array(), $module='')
	{	

		$condition = $this->custom_db->get_custom_condition($condition);
		//BT, CD, ID
		if (is_domain_user()) {
			//Booking Details for Flight
			if($module == 'flight'){
				$bd_query = 'select count(fbpd.app_reference) AS total_bookings from flight_booking_details AS BD left join booking_source BS on BS.source_id=BD.booking_source LEFT JOIN flight_booking_passenger_details AS fbpd ON fbpd.app_reference = BD.app_reference
			WHERE BS.booking_engine_status = 1 AND BD.domain_origin='.get_domain_auth_id().' '.$condition.' group by BD.booking_source';
			}
		}
		$response = $this->db->query($bd_query)->result_array();
		return $response;
	}
	function bookings_count($condition=array(), $count=false, $module='')
	{	

		$condition = $this->custom_db->get_custom_condition($condition);
		//BT, CD, ID
		if (is_domain_user()) {
			//Booking Details for Flight
			if($module == 'flight'){
				$table = 'flight_booking_details';
				$bd_query = 'select SUM(fbtd.total_fare) AS total_price, SUM(fbtd.admin_commission) AS admin_comm, SUM(fbtd.agent_commission) AS agent_comm, BS.name AS sup_name, BS.source_id AS sup_id from flight_booking_details AS BD left join booking_source BS on BS.source_id=BD.booking_source LEFT JOIN flight_booking_transaction_details AS fbtd ON fbtd.app_reference = BD.app_reference
			WHERE BS.booking_engine_status = 1 AND BD.domain_origin='.get_domain_auth_id().' '.$condition.' group by BD.booking_source';
			//debug($bd_query);
			}
			else if($module == 'hotel'){
				$table = 'hotel_booking_details';
				$bd_query = 'select count(BD.app_reference) AS total_bookings,SUM(hbid.total_fare) AS total_price, BS.name AS sup_name, BS.source_id AS sup_id from hotel_booking_details AS BD left join booking_source BS on BS.source_id=BD.booking_source LEFT JOIN hotel_booking_itinerary_details AS hbid ON hbid.app_reference = BD.app_reference
			WHERE BS.booking_engine_status = 1 AND BD.domain_origin='.get_domain_auth_id().' '.$condition.' group by BD.booking_source';
			}
			else if($module == 'bus'){
				$table = 'bus_booking_details';
				$bd_query = 'select count(bbcd.app_reference) AS total_bookings,SUM(bbcd.fare) AS total_price,SUM(admin_commission) AS admin_comm,SUM(agent_commission) AS agent_comm, BS.name AS sup_name, BS.source_id AS sup_id from bus_booking_details AS BD left join booking_source BS on BS.source_id=BD.booking_source LEFT JOIN bus_booking_customer_details as bbcd ON bbcd.app_reference=BD.app_reference
			WHERE BS.booking_engine_status = 1 AND BD.domain_origin='.get_domain_auth_id().' '.$condition.' group by BD.booking_source';
			}
			
			
			//echo $bd_query;die(); 
			$response = $this->db->query($bd_query)->result_array();

			return $response;
		}
	}

	function get_bitla_direct_buses(){
		$this->db->where('source_id', 'PTBSID0000000048');
		$this->db->or_where('source_id', 'PTBSID0000000049');
		$this->db->or_where('source_id', 'PTBSID0000000050');
		$this->db->or_where('source_id', 'PTBSID0000000051');
		return $this->db->get('booking_source')->result_array();
	}
}
