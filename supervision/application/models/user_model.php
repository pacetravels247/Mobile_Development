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
	public function get_user_details($condition=array(), $count=false, $offset=0, $limit=10000000000, $order_by=array())
	{
		
		$filter_condition = ' and ';
		if (valid_array($condition) == true) {
			foreach ($condition as $k => $v) {
				$filter_condition .= implode($v).' and ';
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
		 	AND U.country_code=ACL.origin'.$filter_condition.' limit '.$limit.' offset '.$offset.' '.$filter_order_by)->result_array();
		} else {
			return $this->db->query('SELECT count(*) as total FROM user AS U, user_type AS UT, api_country_list AS ACL
		 WHERE U.user_type=UT.origin 
		 AND U.country_code=ACL.origin'.$filter_condition.' limit '.$limit.' offset '.$offset)->row();
		}
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
		$filter_order_by = 'ORDER BY U.user_id DESC, LM.origin desc';
		if (valid_array($order_by) == true) {
			foreach ($order_by as $k => $v) {
				$filter_order_by .= implode($v).',';
			}
		}
		$filter_condition = rtrim($filter_condition, 'and ');
		$filter_order_by = rtrim($filter_order_by, ',');
		if (!$count) {
			return $this->db->query('SELECT U.*, b2b_a.logo as agent_logo,b2b_a.comm_group_id as comm_group, b2b_a.balance as agent_balance,b2b_a.credit_limit as credit_limit,b2b_a.due_amount as due_amount, UT.user_type, ACL.country_code as country_code_value, b2b_a.updated_datetime as updated_datetime,
			MAX(LM.login_date_time) as last_login, min(LM.logout_date_time) as logout_date_time,
			CL.destination as city_name , ACL.name as country_name
			FROM user AS U INNER JOIN user_type AS UT ON U.user_type=UT.origin 
			LEFT JOIN api_country_list AS ACL ON U.country_name=ACL.origin
			LEFT JOIN b2b_user_details AS b2b_a ON U.user_id=b2b_a.user_oid LEFT JOIN login_manager LM ON LM.user_id=U.uuid
			LEFT JOIN api_city_list as CL on CL.origin=U.city
			WHERE 1=1 
		 '.$filter_condition.' group by U.user_id '.$filter_order_by.' limit '.$limit.' offset '.$offset)->result_array();
		} else {
			return $this->db->query('SELECT count(*) as total FROM user AS U INNER JOIN user_type AS UT ON U.user_type=UT.origin 
			LEFT JOIN api_country_list AS ACL ON U.country_name=ACL.origin
		 WHERE U.user_type=UT.origin
		  '.$filter_condition.' limit '.$limit.' offset '.$offset)->row();
		}
	}

	/**
	 * get Domain user list in the system
	 */
	function b2b_user_list($condition=array(), $count=false, $offset=0, $limit=10000000000, $order_by=array())
	{
		$user_list = $this->get_domain_user_list($condition, $count, $offset, $limit, $order_by);
		
		if ($count == false && valid_array($user_list) == true) {
			$tmp_user_list = array();
			$user_id_lst = array();
			foreach ($user_list as $k => $v) {
				$tmp_user_list[intval($v['user_id'])] = $v;
				$user_id_lst[] = $v['user_id'];
			}
			//Deposite Details
			//$deposit_summ = $this->user_deposit_summary($user_id_lst);
			//Get User Booking Details - Module Wise
			//$booking_summ = $this->user_booking_summary($user_id_lst);
			$user_list = array();
			foreach ($user_id_lst as $uk => $uv) {
				$user_list[$uv] = $tmp_user_list[$uv];
				// if (isset($deposit_summ[$uv])) {
				// 	$user_list[$uv]['dep_req'] = $deposit_summ[$uv];
				// } else {
				// 	$user_list[$uv]['dep_req'] = false;
				// }

				//Booking
				// if (isset($booking_summ[$uv])) {
				// 	$user_list[$uv]['booking_summ'] = $booking_summ[$uv];
				// } else {
				// 	$user_list[$uv]['booking_summ'] = false;
				// }
			}
		}
		
		return $user_list;
	}

	/**
	 * Get summary of user deposit request
	 * @param mixed $user_id_list
	 */
	function user_booking_summary($user_id_list)
	{

		$result = array();
		$data['flight'] = $this->flight_booking_summary($user_id_list);
		$data['hotel'] = $this->hotel_booking_summary($user_id_list);
		$data['bus'] = $this->bus_booking_summary($user_id_list);
		$data['transfer'] = $this->transfer_booking_summary($user_id_list);
		$data['sightseeing'] = $this->sightseeing_booking_summary($user_id_list);

		$data['account_ledger'] = $this->account_ledger_summary($user_id_list);

		foreach ($user_id_list as $k => $v) {
			$result[$v]['flight'] = @$data['flight'][$v];
			$result[$v]['hotel'] = @$data['hotel'][$v];
			$result[$v]['bus'] = @$data['bus'][$v];
			$result[$v]['transfer'] = @$data['transfer'][$v];
			$result[$v]['sightseeing'] = @$data['sightseeing'][$v];
			$result[$v]['account_ledger']=@$data['account_ledger'][$v];
		}
	

		return $result;
	}

	/**
	 * account ledger summary for user
	 * @param $user_id_list
	 */
	 function account_ledger_summary($user_id_list)
	 {
	 	$user_dep_summ = array();
	 
		$user_id_cond = '';
		if (valid_array($user_id_list) == true) {
			
			$user_id_cond = ' AND U.user_id IN ('. implode(',', $user_id_list).')';
		} else {
			
			$user_id_cond = ' AND U.user_id = '.intval($user_id_list);
		}
		
		//
		$agent_filter = ' AND U.user_type ='.B2B_USER;
		$transaction_activated_from_date = '2017-01-10';//DONT REMOVE THIS CONDITION

		$query = 'select TL.created_by_id, count(*) as total_records from transaction_log TL 
						join user U on U.user_id=TL.transaction_owner_id
						where TL.origin>0 and date(TL.created_datetime)>= "'.$transaction_activated_from_date.'" '.$agent_filter.' '.$user_id_cond;	


		$deposit_summ = $this->db->query($query)->result_array($query);
		if (valid_array($deposit_summ) == true) {
			$tmp_dep_summ = array();
			foreach ($deposit_summ as $k => $v) {
				$tmp_dep_summ[intval($v['created_by_id'])]['count'] = $v['total_records'];
			}
			$deposit_summ = $tmp_dep_summ;
		}

		
		return $deposit_summ;

	 }
	/**
	 * booking summary for user
	 * @param $user_id_list
	 */
	function flight_booking_summary($user_id_list)
	{
		$user_dep_summ = array();
		$user_id_cond = '';
		if (valid_array($user_id_list) == true) {
			$user_id_cond = ' created_by_id IN ('. implode(',', $user_id_list).')';
		} else {
			$user_id_cond = ' created_by_id = '.intval($user_id_list);
		}
		//
		$query = 'SELECT created_by_id, status, count(*) AS request_count from flight_booking_details Where '.$user_id_cond.' Group BY created_by_id, status ';

		

		$deposit_summ = $this->db->query($query)->result_array($query);
		if (valid_array($deposit_summ) == true) {
			$tmp_dep_summ = array();
			foreach ($deposit_summ as $k => $v) {
				$tmp_dep_summ[intval($v['created_by_id'])][$v['status']]['count'] = $v['request_count'];
			}
			$deposit_summ = $tmp_dep_summ;
		}


		return $deposit_summ;
	}

	/**
	 * booking summary for user
	 * @param $user_id_list
	 */
	function hotel_booking_summary($user_id_list)
	{
		$user_dep_summ = array();
		$user_id_cond = '';
		if (valid_array($user_id_list) == true) {
			$user_id_cond = ' created_by_id IN ('. implode(',', $user_id_list).')';
		} else {
			$user_id_cond = ' created_by_id = '.intval($user_id_list);
		}
		//
		$query = 'SELECT created_by_id, status, count(*) AS request_count from hotel_booking_details Where '.$user_id_cond.' Group BY created_by_id, status ';
		$deposit_summ = $this->db->query($query)->result_array($query);
		if (valid_array($deposit_summ) == true) {
			$tmp_dep_summ = array();
			foreach ($deposit_summ as $k => $v) {
				$tmp_dep_summ[intval($v['created_by_id'])][$v['status']]['count'] = $v['request_count'];
			}
			$deposit_summ = $tmp_dep_summ;
		}
		return $deposit_summ;
	}

	/**
	 * booking summary for user
	 * @param $user_id_list
	 */
	function transfer_booking_summary($user_id_list)
	{
		$user_dep_summ = array();
		$user_id_cond = '';
		if (valid_array($user_id_list) == true) {
			$user_id_cond = ' created_by_id IN ('. implode(',', $user_id_list).')';
		} else {
			$user_id_cond = ' created_by_id = '.intval($user_id_list);
		}
		//
		$query = 'SELECT created_by_id, status, count(*) AS request_count from transferv1_booking_details Where '.$user_id_cond.' Group BY created_by_id, status ';
		$deposit_summ = $this->db->query($query)->result_array($query);
		if (valid_array($deposit_summ) == true) {
			$tmp_dep_summ = array();
			foreach ($deposit_summ as $k => $v) {
				$tmp_dep_summ[intval($v['created_by_id'])][$v['status']]['count'] = $v['request_count'];
			}
			$deposit_summ = $tmp_dep_summ;
		}
		return $deposit_summ;
	}
	/**
	 * booking summary for user
	 * @param $user_id_list
	 */
	function sightseeing_booking_summary($user_id_list)
	{
		$user_dep_summ = array();
		$user_id_cond = '';
		if (valid_array($user_id_list) == true) {
			$user_id_cond = ' created_by_id IN ('. implode(',', $user_id_list).')';
		} else {
			$user_id_cond = ' created_by_id = '.intval($user_id_list);
		}
		//
		$query = 'SELECT created_by_id, status, count(*) AS request_count from sightseeing_booking_details Where '.$user_id_cond.' Group BY created_by_id, status ';
		$deposit_summ = $this->db->query($query)->result_array($query);
		if (valid_array($deposit_summ) == true) {
			$tmp_dep_summ = array();
			foreach ($deposit_summ as $k => $v) {
				$tmp_dep_summ[intval($v['created_by_id'])][$v['status']]['count'] = $v['request_count'];
			}
			$deposit_summ = $tmp_dep_summ;
		}
		return $deposit_summ;
	}
	/**
	 * booking summary for user
	 * @param $user_id_list
	 */
	function bus_booking_summary($user_id_list)
	{
		$user_dep_summ = array();
		$user_id_cond = '';
		if (valid_array($user_id_list) == true) {
			$user_id_cond = ' created_by_id IN ('. implode(',', $user_id_list).')';
		} else {
			$user_id_cond = ' created_by_id = '.intval($user_id_list);
		}
		//
		$query = 'SELECT created_by_id, status, count(*) AS request_count from bus_booking_details Where '.$user_id_cond.' Group BY created_by_id, status ';
		$deposit_summ = $this->db->query($query)->result_array($query);
		if (valid_array($deposit_summ) == true) {
			$tmp_dep_summ = array();
			foreach ($deposit_summ as $k => $v) {
				$tmp_dep_summ[intval($v['created_by_id'])][$v['status']]['count'] = $v['request_count'];
			}
			$deposit_summ = $tmp_dep_summ;
		}
		return $deposit_summ;
	}

	/**
	 * Get summary of user deposit request
	 * @param mixed $user_id_list
	 */
	function user_deposit_summary($user_id_list)
	{
		$user_dep_summ = array();
		$user_id_cond = '';
		if (valid_array($user_id_list) == true) {
			$user_id_cond = ' user_oid IN ('. implode(',', $user_id_list).')';
		} else {
			$user_id_cond = ' user_oid = '.intval($user_id_list);
		}
		$query = 'SELECT user_oid, status, count(*) AS request_count from master_transaction_details Where '.$user_id_cond.' Group BY user_oid, status ';
		$deposit_summ = $this->db->query($query)->result_array($query);
		if (valid_array($deposit_summ) == true) {
			$tmp_dep_summ = array();
			foreach ($deposit_summ as $k => $v) {
				$tmp_dep_summ[intval($v['user_oid'])][$v['status']]['count'] = $v['request_count'];
			}
			$deposit_summ = $tmp_dep_summ;
		}
		return $deposit_summ;
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
			JOIN login_manager AS LM ON U.user_type=LM.user_type and U.uuid=LM.user_id
			WHERE LM.logout_date_time = "0000-00-00 00:00:00"
			 '.$filter_condition.' group by U.user_id order by LM.logout_date_time asc limit '.$limit.' offset '.$offset)->result_array();
			
		} else {
			return $this->db->query('SELECT count(*) as total FROM user AS U
			JOIN user_type AS UT ON U.user_type=UT.origin
			JOIN api_country_list AS ACL ON U.country_code=ACL.origin
			JOIN login_manager AS LM ON U.user_type=LM.user_type and U.uuid=LM.user_id
		    WHERE LM.logout_date_time = "0000-00-00 00:00:00" '.$filter_condition.' group by U.user_id')->row();

		}
	}

	/**
	 * get Logged in Users
	 Balu A (25-05-2015) - 25-05-2015
	 */
	function get_today_logged_users($condition=array(), $count=false, $offset=0, $limit=10000000000)
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
			JOIN login_manager AS LM ON U.user_type=LM.user_type and U.uuid=LM.user_id
			WHERE DATE(LM.logout_date_time) = "'.$current_date.'"
			 '.$filter_condition.' group by U.user_id order by LM.logout_date_time asc limit '.$limit.' offset '.$offset)->result_array();
			
		} else {
			return $this->db->query('SELECT count(*) as total FROM user AS U
			JOIN user_type AS UT ON U.user_type=UT.origin
			JOIN api_country_list AS ACL ON U.country_code=ACL.origin
			JOIN login_manager AS LM ON U.user_type=LM.user_type and U.uuid=LM.user_id
		    WHERE DATE(LM.logout_date_time) = "'.$current_date.'" '.$filter_condition.' group by U.user_id')->row();

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
	function update_login_manager($S_LID=0)
	{
		$condition = array(
				'user_id' => intval($this->entity_uuid)
		);

		if (intval($S_LID) > 0) {
			$condition['origin'] = $S_LID;
		} else {
			$condition['logout_date_time'] = '0000-00-00 00:00:00';
		}
		//update all the logout session in login manager
		$this->custom_db->update_record(
			'login_manager', array('logout_date_time' => date('Y-m-d H:i:s', time())),
		$condition
		);
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
	/*^|^|
	 *SMS Configuration & Checkpoint is set here
	 */

	/*
	 *
	 *
	 *
	 */

	public function get_subscribed_emails($domain_key, $email='')
	{
		if(isset($email)){
			$query = $this->db->get_where('email_subscribtion',array('domain_list_fk'=>$domain_key,'email_id' => $email));
		}
		else{
			$query = $this->db->get_where('email_subscribtion',array('domain_list_fk'=>$domain_key));
		}
	
		return $query->result();
	}
	/***
	 * update user information
	 */
	function update_subscribed_emails($data, $cond){
		$this->custom_db->update_record('email_subscribtion', $data, $cond);
		$response['status'] = SUCCESS_STATUS;
		return $response;
	}
	/***
	 * update user information
	 */
	function update_user_data($data, $cond)
	{
		$response['status'] = FAILURE_STATUS;
		$response['data'] = array();
		$user_info = $this->custom_db->single_table_records('user', '*', $cond);
		if ($user_info['status'] == SUCCESS_STATUS and count($user_info['data']) == 1) {

			$this->custom_db->update_record('user', $data, $cond);

			$user_info = $this->custom_db->single_table_records('user', '*', $cond);
			$user_info = $user_info['data'][0];
			$response['status'] = SUCCESS_STATUS;
			$response['data'] = $user_info;
			if ($user_info['status']) {
				$details = ' Activated '.$user_info['first_name'].' Account';
			} else {
				$details = ' Deactivated '.$user_info['first_name'].' Account';
			}
			$this->application_logger->account_status($GLOBALS['CI']->entity_name.$details, array('user_id' => $user_info['user_id'], 'uuid' => $user_info['uuid']));

		}
		return $response;
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
		$query = 'select U.*,BU.logo,BU.origin,BU.balance, BU.credit_limit, BU.due_amount,BU.origin as Bid from user AS U
					      join  b2b_user_details BU on U.user_id = BU.user_oid
				          join  currency_converter CUC on CUC.id = BU.currency_converter_fk
						  WHERE  U.user_type='.B2B_USER.' AND U.user_id='.$user_id;
		return $this->db->query($query)->result_array();
			
	}
	
	function get_admin_details($data, $ut_cond = array()){
		$query = 'SELECT * FROM user U where U.password = "'.($data['password']).'" and U.user_name ="'.$data['user_name'].'" and U.status ='.$data['status'].' and ('.$ut_cond.')'; 

		$data = $this->db->query($query)->row_array();

		if(empty($data) == false){

			return $data;
		}
		else{
			return false;
		}
		

	} 

	function get_privilage_list($filter_text = '') {
		$filter_condition = $filter_text;
		// if ($filter_text !== '') {
		// 	$filter_condition .= ' WHERE privilege_key NOT IN( "p6",p8","p17","p21","p5","p24","p7","p53","p71","p55","p18","p20","p26","p22","p25","p27","p28","p29","p30","p43","p52","p56") AND PL.description like "%' . $filter_text . '%"';
		// }
		// else {
		// 	// $filter_condition .= ' WHERE privilege_key NOT IN( "p6","p8","p17","p21","p5","p24","p7","p53","p71","p55","p18","p20","p26","p22","p25","p27","p28","p29","p30","p43","p52","p56")';
		// 	// p22  	p25,p27, 	p28,p29,p30,p43, 	p52,p56
		// 	// p8,p59,p17,p21,p5,p24,p7,p53,p71,p55,p18,p20,p26
		// }
		// echo 'SELECT PL.*, P.origin AS pl FROM `privilege_list` AS PL LEFT JOIN `privileges` AS P ON P.p_no = PL.origin WHERE '.$filter_condition;exit;

		//$query = 'SELECT PL.*, P.origin AS pl FROM `privilege_list_new` AS PL LEFT JOIN `privileges` AS P ON P.p_no = PL.origin AND P.user_id = ' . $user_id . $filter_condition . ' ORDER BY PL.p_no';
		//$query = 'SELECT PL.*, P.origin AS pl FROM `privilege_list_new` AS PL LEFT JOIN `privileges` AS P ON P.p_no = PL.origin  ORDER BY PL.p_no';

		$query = 'SELECT PL.* FROM privilege_list_new AS PL ORDER BY PL.p_no';

		 //echo $query;exit;
		//ALTER TABLE `privilege_list` ADD `p_no` INT(12) NOT NULL DEFAULT '0' AFTER `description`;
		return $this->db->query ( $query )->result_array ();
	}

	function get_filtered_usertype_priprivileges($user_type){
		//debug($user_type);die();
		$query = 'SELECT * FROM privileges WHERE user_type = "'.$user_type.'"';
		//debug($query);die();
		$data = $this->db->query ( $query )->result_array ();

		$privileges = array();
		foreach ($data as $key => $value) {
			array_push($privileges, $value['p_no']);
		}

		return $privileges;
	}

	function edit_user_privileges($user_type, $privileges_origin) {
		if (is_array ( $privileges_origin ) == false) {
			$privileges_origin = ( array ) $privileges_origin;
		}
		
		$previlages ['user_id'] = 0;
		$previlages ['user_type'] = $user_type;
		if ($this->custom_db->delete_record ( 'privileges', array (
				'user_type' => $user_type 
		) ) == QUERY_SUCCESS) {
			foreach ( $privileges_origin as $k => $v ) {
				$previlages ['p_no'] = intval ( $v );
				$this->custom_db->insert_record ( 'privileges', $previlages );
			}
		}
	}
	function update_credit_limit($request){
		$response['status'] = SUCCESS_STATUS;
        $current_credit_limit = 0;
        $cond = array('origin' => intval($request['domain_origin']));

        $query = 'update b2b_user_details set credit_limit=' . $request['credit_limit'] . ' where origin=' . $request['origin'].' and user_oid='.$request['user_id'];
// echo $query;exit;
        $data = $this->db->query($query);
        return $response;
	}
	function get_sms_users_list()
	{
		$sql = "SELECT *, ut.user_type AS ut_name FROM user u, user_type ut  WHERE u.user_type = ut.origin AND u.user_type != ".B2C_USER." AND u.user_type != ".B2E_USER." AND u.user_type != ".B2B_USER;
		$data = $this->db->query($sql)->result_array();
		return $data;
	}
	function sms_template($sms_id)
	{
		$tmp_data = $this->db->select('*')->get_where('tbl_sms_templates', array('sms_id' => $sms_id));
		//echo $this->db->last_query();exit;
		return $tmp_data->row();
	}

	function b2b_user_by_id($agent_id){

		return $this->db->query('SELECT U.*,CL.destination as city_name , ACL.name as country_name
			FROM user AS U INNER JOIN user_type AS UT ON U.user_type=UT.origin 
			LEFT JOIN api_country_list AS ACL ON U.country_name=ACL.origin
			LEFT JOIN api_city_list as CL on CL.origin=U.city
			WHERE user_id = "'.$agent_id.'"')->result_array();
	}
}
