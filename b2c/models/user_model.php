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

	function create_user($email, $password, $first_name='Customer', $country_code,$phone='', $creation_source='portal', $user_type='')
	{
		$data['email'] = provab_encrypt(trim($email));
		$data['user_name'] = provab_encrypt(trim($email));
		
		if (empty($password) == false and strlen($password) > 3) {
			$data['password'] = provab_encrypt(md5(trim($password)));
		}
		if(empty($user_type) &&($user_type!=0) || $user_type ==''){
			$data['user_type'] = B2C_USER;
			$action_query_string['user_type'] = B2C_USER;
		}
		else{
			$data['user_type'] = $user_type;
			$action_query_string['user_type'] = $user_type;
		}
		
		//$data['user_type'] = B2C_USER;
		$data['title'] = 1;
		$data['last_name'] = '';
		$data['agency_name'] = '';
		$data['gst_number'] = '';
		$data['address'] = '';
		$data['city'] = 1;
		$data['pin_code'] = '';
		$data['country_name'] = 1;
		$data['office_phone'] = 1234567890;
		$data['terms_conditions'] = 1;
		$data['first_name'] = $first_name;
		$data['phone'] = $phone;
		$data['domain_list_fk'] = get_domain_auth_id();
		$data['uuid'] = provab_encrypt(time().rand(1, 1000));
		$data['creation_source'] = $creation_source;
		$data['country_code'] = $country_code;
		if ($creation_source == 'portal') {
			$data['status'] = INACTIVE;
		} else {
			$data['status'] = ACTIVE;
		}
		$data['created_datetime'] = date('Y-m-d H:i:s');
		$data['created_by_id'] = intval(@$GLOBALS['CI']->entity_user_id);
		$data['language_preference'] = 'english';
		$insert_id = $this->custom_db->insert_record('user', $data);
		$insert_id = $insert_id['insert_id'];
		$user_data = $this->custom_db->single_table_records('user', '*', array('user_id' => $insert_id));
		$remarks = $email.' Has Registered From B2C Portal';
		$notification_users = $this->get_admin_user_id();
		$action_query_string = array();
		$action_query_string['user_id'] = $insert_id;
		$action_query_string['uuid'] = provab_decrypt($data['uuid']);
		$action_query_string['user_type'] = B2C_USER;
		$this->application_logger->registration($email, $remarks, $insert_id, $action_query_string, array(), $notification_users);
		return $user_data;
	}

	//sms configuration
	function sms_configuration($sms)
	{
		$tmp_data = $this->db->select('*')->get_where('sms_configuration', array('domain_origin' => $sms));
		//echo $this->db->last_query();exit;
		return $tmp_data->row();
	}
	//social network configuration
	function fb_network_configuration($id,$social)
	{
		//$tmp_data = $this->db->select('config')->get_where('social_login', array('domain_origin' => $id,'social_login_name' => $social));
		//echo $this->db->last_query();exit;
		$social_links = $this->db_cache_api->get_active_social_network_list();
		return isset($social_links[$social]) ? $social_links[$social]['config'] : false;
	}
	
	function google_network_configuration($id,$social)
	{
		$social_links = $this->db_cache_api->get_active_social_network_list();
		return isset($social_links[$social]) ? $social_links[$social]['config'] : false;
	}

	//Global SMS Checkpoint
	function sms_checkpoint($name)
	{
		$result = $this->db->select('status')->get_where('sms_checkpoint', array('condition' => $name))->row();
		//echo $this->db->last_query();exit;
		//echo $result->status;exit;
		return $result->status;
	}
	/***
	 * Registered new user Activation Point
	 */
	function activate_account_status($status, $user_id)
	{
		$data = array(
				'status' => $status
		);
		$this->db->where('user_id', $user_id);
		$this->db->update('user', $data);
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
	 * Get Active User Details - B2C Only
	 * @param string $username
	 * @param string $password
	 */
	function active_b2c_user($username, $password)
	{
		//$condition[] = array('U.status', '=', ACTIVE);
		$condition[] = array('U.domain_list_fk', '=', get_domain_auth_id());
		$condition[] = array('U.user_type', '=', B2C_USER);
		$condition[] = array('U.user_name', '=', $this->db->escape(provab_encrypt($username)));
		//$condition[] = array('U.phone', '=', $this->db->escape($username));
		$condition[] = array('U.password', '=', $this->db->escape(provab_encrypt(md5(trim($password)))));
		
		return $this->get_user_details($condition);

	}
	

	/**
	 *verify is the user credentials are valid
	 *
	 *@param string $email    email of the user
	 *@param string @password password of the user
	 *
	 *return boolean status of the user credentials
	 */
	public function get_user_details($condition=array(), $count=false, $offset=0, $limit=10000000000, $order_by=array())
	{
		//debug($condition);exit;

		$filter_condition = ' and ';
		if (valid_array($condition) == true) {
			foreach ($condition as $k => $v) {
				//debug($v);exit;
				// if($v[0] == 'U.user_name'){
				// 	$filter_condition .= '('.implode($v).' or ';
				// }
				// else if($v[0] == 'U.phone'){
				// 	$filter_condition .= implode($v).' ) and ';
				// }
				// else{
					$filter_condition .= implode($v).' and ';	
				// }
				
			}
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

			return $this->db->query('SELECT U.*, UT.user_type as user_profile_name, ACL.country_code as country_code_value
			FROM user AS U, user_type AS UT, api_country_list AS ACL
		 	WHERE U.user_type=UT.origin 
		 	AND U.country_code=ACL.country_code'.$filter_condition.' limit '.$limit.' offset '.$offset.' '.$filter_order_by)->result_array();
		} else {
			
		  return $this->db->query('SELECT count(*) as total FROM user AS U, user_type AS UT, api_country_list AS ACL
		 WHERE U.user_type=UT.origin 
		 AND U.country_code=ACL.country_code'.$filter_condition.' limit '.$limit.' offset '.$offset)->row();
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
		$this->application_logger->logout($this->entity_name, $this->entity_user_id, array('user_id' => $this->entity_user_id, 'uuid' => $this->entity_uuid));
	}


	/**
	 * Create Login Manager
	 */
	function create_login_auth_record($user_id, $user_type, $user_origin=0, $username='customer')
	{
		$login_details['browser'] = $_SERVER['HTTP_USER_AGENT'];
		$remote_ip = $_SERVER['REMOTE_ADDR'];
		$this->update_auth_record_expiry($user_id, $user_type, $remote_ip, $user_origin, $username);
		//logout of same user from same ip
		$login_details['info'] = file_get_contents('https://tools.keycdn.com/geo.json');
		$data['user_id'] = $user_id;
		$data['user_type'] = $user_type;
		$data['login_date_time'] = date('Y-m-d H:i:s');
		$data['login_ip'] = $remote_ip;
		$data['attributes'] = json_encode($login_details);

		$login_id = $this->custom_db->insert_record('login_manager', $data);
		$this->application_logger->login($username, $user_origin, array('user_id' => $user_origin, 'uuid' => $user_id));
		return $login_id['insert_id'];
	}

	/**
	 * Update logout
	 * @param $user_id
	 * @param $user_type
	 * @param $remote_ip
	 * @param $browser
	 */
	function update_auth_record_expiry($user_id, $user_type, $remote_ip, $user_origin, $username)
	{
		$cond['user_id'] = $user_id;
		$cond['user_type'] = $user_type;
		$cond['login_ip'] = $remote_ip;
		$auth_exp = $this->custom_db->update_record('login_manager', array('logout_date_time' => date('Y-m-d H:i:s')), $cond);
		if ($auth_exp == true) {
			//update application logger
			$this->application_logger->logout($username, $user_origin, array('user_id' => $user_origin, 'uuid' => $user_id));
		}
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
	/**
	 * Balu A
	 */
	function get_user_traveller_details()
	{
		$query = 'select * from user_traveller_details 
		where created_by_id='.intval(@$this->entity_user_id).' ORDER BY first_name ASC';
		return $this->db->query($query)->result_array();
	}
	
	/**
	sudheep offline payment
	**/
	function offline_payment_insert($params){
		//$query = "INSERT INTO `offline_payment`(`id`, `company_name`, `name`, `email`, `phone`, `amount`, `remarks`, `created_date`, `refernce_code`) VALUES ([value-1],[value-2],[value-3],[value-4],[value-5],[value-6],[value-7],[value-8],[value-9])";

		$created_date = date('Y-m-d H:i:s');
		$coded = str_shuffle($params['data'][0]['value'].$params['data'][3]['value']);

		$insert_id = $this->custom_db->insert_record('offline_payment', array($params['data'][0]['name']=> $params['data'][0]['value'], $params['data'][1]['name']=> $params['data'][1]['value'], $params['data'][2]['name']=> $params['data'][2]['value'], $params['data'][3]['name']=> $params['data'][3]['value'], $params['data'][4]['name']=> $params['data'][4]['value'], $params['data'][5]['name']=> $params['data'][5]['value'],'created_date' =>$created_date,'refernce_code'=>$coded	));

			return array('db'=>$insert_id, 'refernce_code'=>$coded);

	}
	function offline_approval($cd){
		$query = "SELECT * FROM `offline_payment` WHERE `refernce_code` = '$cd' ";

	$ret = $this->db->query($query)->result_array();
	return $ret;
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
	public function get_all_sms_number_list($sms_id){
		$query = 'select * from sms_user_map AS SU
					     join user U on U.user_id = SU.user_id
				         WHERE SU.sms_id ='.$sms_id;
						  // echo $query;exit;
		return $this->db->query($query)->result_array();
	}
	//sms templates
	function sms_template($sms_id)
	{
		$tmp_data = $this->db->select('*')->get_where('tbl_sms_templates', array('sms_id' => $sms_id));
		//echo $this->db->last_query();exit;
		return $tmp_data->row();
	}
	function get_customer_info($user_id){
		$query = 'select U.* from user AS U WHERE  U.user_type='.B2C_USER.' AND U.user_id='.$user_id;
						  // echo $query;exit;
		return $this->db->query($query)->result_array();
			
	}
}// main class end*************
