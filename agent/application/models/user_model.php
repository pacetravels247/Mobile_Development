<?php
/**
 * Library which has generic functions to get data
 *
 * @package    Provab Application
 * @subpackage Travel Portal
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V2
 */
Class User_Model extends CI_Model
{
	/**
	 *verify is the user credentials are valid
	 *
	 *@param string $email    email of the user
	 *@param string @password password of the user
	 *
	 *return boolean status of the user credentials
	 */
	public function get_user_details($condition=array(), $count=false, $offset=0, $limit=10000000000, $order_by=array(), $or_condition=array())
	{
		$filter_condition = ' and ';
		if (valid_array($condition) == true) {
			foreach ($condition as $k => $v) {
				$filter_condition .= implode($v).' and ';
			}
		}
		$or_filter_condition=" and (";
		if (valid_array($or_condition) == true) {
			foreach ($or_condition as $k => $v) {
				$or_filter_condition .= implode($v).' or ';
			}
			$or_filter_condition = rtrim($or_filter_condition, 'or ');
		}
		$or_filter_condition.=")";
		if (valid_array($order_by) == true) {
			$filter_order_by = 'ORDER BY';
			foreach ($order_by as $k => $v) {
				$filter_order_by .= implode($v).',';
			}
		} else {
			$filter_order_by = '';
		}
		if($or_filter_condition == " and ()")
			$or_filter_condition = "";

		$filter_condition = rtrim($filter_condition, 'and ');
		$filter_order_by = rtrim($filter_order_by, ',');
	
		if (!$count) {
		return $this->db->query('SELECT U.*, UT.user_type as user_profile_name, ACL.country_code as country_code_value
			FROM user AS U, user_type AS UT, api_country_list AS ACL
		 	WHERE U.user_type=UT.origin 
		 	AND ACL.origin=U.country_code'.$filter_condition.$or_filter_condition.' limit '.$limit.' offset '.$offset.' '.$filter_order_by)->result_array();
		} else {
		return $this->db->query('SELECT count(*) as total FROM user AS U, user_type AS UT, api_country_list AS ACL
		 WHERE U.user_type=UT.origin 
		  AND ACL.origin=U.country_code'.$filter_condition.' limit '.$limit.' offset '.$offset)->row();
		}
		// echo $this->db->last_query();exit;
	}

	/**
	 * get Domain user list in the system
	 */
	function get_domain_user_list($condition=array(), $count=false, $offset=0, $limit=10000000000, $order_by=array())
	{
		$filter_condition = ' and ';
		if (valid_array($condition) == true) {
			foreach ($condition as $k => $v) {
				$filter_condition .= implode($v).' and ';
			}
		}
		if(is_domain_user() == false) {
			//PROVAB ADMIN
			//GET ALL DOMAIN ADMINS DETAILS
			$filter_condition .= ' U.domain_list_fk > 0 and U.user_type = '.ADMIN.' and U.user_id != '.intval($this->entity_user_id).' and ';
		} else if(is_domain_user() == true) {
			//DOMAIN ADMIN
			//GET ALL DOMAIN USERS DETAILS
			$filter_condition .= ' U.domain_list_fk ='.get_domain_auth_id().' and U.user_type != '.ADMIN.' and U.user_id != '.intval($this->entity_user_id).' and ';
		}
		if (valid_array($order_by) == true) {
			$filter_order_by = 'ORDER BY';
			foreach ($order_by as $k => $v) {
				$filter_order_by .= implode($v).',';
			}
		} else {
			$filter_order_by = '';
		}
		$filter_condition = rtrim($filter_condition, 'and ');
		$filter_order_by = rtrim($filter_order_by, ',');
		if (!$count) {
			return $this->db->query('SELECT U.*, UT.user_type, ACL.country_code as country_code_value FROM user AS U, user_type AS UT, api_country_list AS ACL
		 WHERE U.user_type=UT.origin 
		 AND U.country_code=ACL.origin'.$filter_condition.' limit '.$limit.' offset '.$offset.' '.$filter_order_by)->result_array();
		} else {
			return $this->db->query('SELECT count(*) as total FROM user AS U, user_type AS UT, api_country_list AS ACL
		 WHERE U.user_type=UT.origin 
		 AND U.country_code=ACL.origin'.$filter_condition.' limit '.$limit.' offset '.$offset)->row();
		}
	}

	/**
	 * get Logged in Users
	 Balu A (25-05-2015) - 25-05-2015
	 */
	function get_logged_in_users($condition=array(), $count=false, $offset=0, $limit=10000000000)
	{
		$filter_condition = ' and ';
		if (valid_array($condition) == true) {
			foreach ($condition as $k => $v) {
				$filter_condition .= implode($v).' and ';
			}
		}
		if(is_domain_user() == false) {
			//PROVAB ADMIN
			//GET ALL DOMAIN ADMINS DETAILS
			$filter_condition .= ' U.domain_list_fk > 0 and U.user_type = '.ADMIN.' and U.user_id != '.intval($this->entity_user_id).' and ';
		} else if(is_domain_user() == true) {
			//DOMAIN ADMIN
			//GET ALL DOMAIN USERS DETAILS
			$filter_condition .= ' U.domain_list_fk ='.get_domain_auth_id().' and U.user_type != '.ADMIN.' and U.user_id != '.intval($this->entity_user_id).' and ';
		}
		$filter_condition = rtrim($filter_condition, 'and ');
		$current_date = date('Y-m-d', time());
		if (!$count) {
			return $this->db->query('SELECT U.*, UT.user_type, LM.login_date_time as login_time,LM.logout_date_time as logout_time,LM.login_ip
			FROM user AS U
			JOIN user_type AS UT ON U.user_type=UT.origin
			JOIN api_country_list AS ACL ON U.country_code=ACL.origin
			JOIN login_manager AS LM ON U.user_type=LM.user_type and U.user_id=LM.user_id
		    WHERE LM.login_date_time >="'.$current_date.' 00:00:00"
			and (LM.logout_date_time = "0000-00-00 00:00:00" or LM.logout_date_time >= "'.$current_date.' 00:00:00")
			 '.$filter_condition.' order by LM.logout_date_time asc limit '.$limit.' offset '.$offset)->result_array();
		} else {
			return $this->db->query('SELECT count(*) as total FROM user AS U
			JOIN user_type AS UT ON U.user_type=UT.origin
			JOIN api_country_list AS ACL ON U.country_code=ACL.origin
			JOIN login_manager AS LM ON U.user_type=LM.user_type and U.user_id=LM.user_id
		    WHERE LM.login_date_time >="'.$current_date.' 00:00:00"
			and (LM.logout_date_time = "0000-00-00 00:00:00" or LM.logout_date_time >= "'.$current_date.' 00:00:00")'.$filter_condition.' limit '.$limit.' offset '.$offset)->row();
		}
	}

	/**
	 * get Domain List present in the system
	 */
	function get_domain_details()
	{
		$query = 'select DL.*,CONCAT(U.first_name, " ", U.last_name) as created_user_name from domain_list DL join user U on DL.created_by_id=U.user_id';
		return $this->db->query($query)->result_array();
	}

	/**
	 *update logout time
	 *
	 *@param number $LID unique login id which has to be updated
	 *
	 *@return status;
	 */
	function update_login_manager($user_id, $login_id)
	{
		$condition = array(
				'user_id' => $user_id,
				'origin' => $login_id
		);
		//update all the logout session in login manager
		$this->custom_db->update_record('login_manager', array('logout_date_time' => date('Y-m-d H:i:s', time())), $condition);
	}


	/**
	 * Create Login Manager
	 */
	function create_login_auth_record($user_id, $user_type)
	{
		$login_details['browser'] = $_SERVER['HTTP_USER_AGENT'];
		$remote_ip = $_SERVER['REMOTE_ADDR'];
		$this->update_auth_record_expiry($user_id, $user_type, $remote_ip);
		//logout of same user from same ip
		$login_details['info'] = file_get_contents('https://tools.keycdn.com/geo.json');
		$data['user_id'] = $user_id;
		$data['user_type'] = $user_type;
		$data['login_date_time'] = date('Y-m-d H:i:s');
		$data['login_ip'] = $remote_ip;
		$data['attributes'] = json_encode($login_details);
		$login_id = $this->custom_db->insert_record('login_manager', $data);
		return $login_id['insert_id'];
	}

	/**
	 * Update logout
	 * @param $user_id
	 * @param $user_type
	 * @param $remote_ip
	 * @param $browser
	 */
	function update_auth_record_expiry($user_id, $user_type, $remote_ip)
	{
		$cond['user_id'] = $user_id;
		$cond['user_type'] = $user_type;
		$cond['login_ip'] = $remote_ip;
		$this->custom_db->update_record('login_manager', array('logout_date_time' => date('Y-m-d H:i:s')), $cond);
	}

	public function email_subscribtion($email,$domain_key)
	{
		$query = $this->db->get_where('email_subscribtion', array('email_id' => $email));
		if($query->num_rows() > 0){
			return "already";
		}else{
			$insert_id = $this->custom_db->insert_record('email_subscribtion',array('email_id' => $email,'domain_list_fk' => $domain_key));
			return $insert_id['insert_id'];
		}
	}
	/*
	 *@Pravinkumar
	 */
	//sms configuration
	function sms_configuration($sms)
	{
		$tmp_data = $this->db->select('*')->get_where('sms_configuration', array('domain_origin' => $sms));
		//echo $this->db->last_query();exit;
		return $tmp_data->row();
	}

	/*
	 *@Pravinkumar
	 */
	//sms templates
	function sms_template($sms_id)
	{
		$tmp_data = $this->db->select('*')->get_where('tbl_sms_templates', array('sms_id' => $sms_id));
		//echo $this->db->last_query();exit;
		return $tmp_data->row();
	}
	
	//Global SMS Checkpoint
	function sms_checkpoint($name)
	{
		$result = $this->db->select('status')->get_where('sms_checkpoint', array('condition' => $name))->row();
		//echo $this->db->last_query();exit;
		//echo $result->status;exit;
		return $result->status;
	}
	/**
	 * Return current user details who has logged in
	 */
	function get_current_user_details()
	{
		if (intval(@$this->entity_user_id) > 0) {
			$cond = array(array('U.user_id', '=', intval($this->entity_user_id)));
				
			$user = $this->get_user_details($cond);
                        $user[0]['uuid']=provab_decrypt($user[0]['uuid']);
                        $user[0]['email']=provab_decrypt($user[0]['email']);
                        $user[0]['user_name']=provab_decrypt($user[0]['user_name']);
                        $user[0]['password']=provab_decrypt($user[0]['password']);
			return $user;
		} else {
			return false;
		}
	}
	/**
	 * Balu A
	 */
	function get_admin_user_id()
	{
		$admin_user_id = array();
		$cond[] = array('U.user_type', '=', ADMIN);
		$cond[] = array('U.status', '=', ACTIVE);
		$cond[] = array('U.domain_list_fk', '=', get_domain_auth_id());
		$user_details = $this->get_user_details($cond);
		foreach($user_details as $k => $v){
			$admin_user_id[$k] = $v['user_id'];
		}
		return $admin_user_id;
	}
	/**
	 * get agent information
	 * @param unknown $user_id
	 */
	function get_agent_info($user_id){
		$query = 'select U.*,BU.logo,BU.balance from user AS U
					      join  b2b_user_details BU on U.user_id = BU.user_oid
				          join  currency_converter CUC on CUC.id = BU.currency_converter_fk
						  WHERE  U.user_type='.B2B_USER.' AND U.user_id='.$user_id;
						  // echo $query;exit;
		return $this->db->query($query)->result_array();
			
	}
	/**
	 * Balu A
	 */
	public function user_traveller_details($search_chars)
	{
		$raw_search_chars = $this->db->escape($search_chars);
		$r_search_chars = $this->db->escape($search_chars.'%');
		$search_chars = $this->db->escape('%'.$search_chars.'%');
		$query = 'select * from user_traveller_details where created_by_id='.intval($this->entity_user_id).' and (first_name like '.$search_chars.'
		OR 	last_name like '.$search_chars.')
		ORDER BY first_name ASC	LIMIT 0, 20';
		return $this->db->query($query);
	}
	//social network configuration
	function fb_network_configuration($id,$social)
	{
		//$tmp_data = $this->db->select('config')->get_where('social_login', array('domain_origin' => $id,'social_login_name' => $social));
		//echo $this->db->last_query();exit;
		$social_links = $this->db_cache_api->get_active_social_network_list();
		return isset($social_links[$social]) ? $social_links[$social]['config'] : false;
	}
		/**
	 * Get Active User Details - B2B Only
	 * @param string $username
	 * @param string $password
	 */
	function active_b2b_user($username, $password)
	{
		//$condition[] = array('U.status', '=', ACTIVE);
		$condition[] = array('U.domain_list_fk', '=', get_domain_auth_id());
		$condition[] = array('U.user_type', '=', B2B_USER);
		// ===================== Changes by Shrikant =====================
		if(strlen($password) > 25){
			$condition[] = array('U.password', '=', $this->db->escape($password));
		}else{
			$condition[] = array('U.password', '=', $this->db->escape(provab_encrypt(md5(trim($password)))));
		}
		// ===================== Changes end here =====================
		//$condition[] = array('U.password', '=', $this->db->escape(provab_encrypt(md5(trim($password)))));
		$or_condition[] = array('U.user_name', '=', $this->db->escape(provab_encrypt(trim($username))));
		$or_condition[] = array('U.email', '=', $this->db->escape(provab_encrypt(trim($username))));
		$or_condition[] = array('U.phone', '=', $this->db->escape(trim($username)));
		
		
		return $this->get_user_details($condition, false, 0, 10000000000, array(), $or_condition);

	}

	function get_sms_cc_user_list($sms_id)
	{
		$sql = "SELECT * FROM user u, sms_user_map sum WHERE u.user_id = sum.user_id AND 
				sum.sms_id = '".$sms_id."'";
		$data = $this->db->query($sql)->result_array();
		return $data;
	}
	public function get_all_sms_number_list($sms_id){
	$query = 'select * from sms_user_map AS SU
					     join user U on U.user_id = SU.user_id
				         WHERE SU.sms_id ='.$sms_id;
						  // echo $query;exit;
		return $this->db->query($query)->result_array();
	}
}
