<?php

/**
 * Library which has generic functions to get data
 *
 * @package    Provab Application
 * @subpackage Travel Portal
 * @author     Arjun J<arjunjgowda260389@gmail.com>
 * @version    V2
 */
class User_Model extends CI_Model {

    var $verify_domain_balance;

    function __construct() {
        parent::__construct();
        $this->verify_domain_balance = $this->config->item('verify_domain_balance');
    }

    function create_user($email, $password, $first_name = 'Customer', $phone = '', $creation_source = 'portal') {
        $data ['email'] = $email;
        $data ['password'] = md5($password);
        $data ['user_type'] = B2C_USER;
        $data ['first_name'] = $first_name;
        $data ['phone'] = $phone;
        $data ['domain_list_fk'] = get_domain_auth_id();
        $data ['uuid'] = time() . rand(1, 1000);
        $data ['creation_source'] = $creation_source;
        if ($creation_source == 'portal') {
            $data ['status'] = INACTIVE;
        } else {
            $data ['status'] = ACTIVE;
        }
        $data ['created_datetime'] = date('Y-m-d H:i:s');
        $data ['created_by_id'] = intval(@$GLOBALS ['CI']->entity_user_id);
        $data ['language_preference'] = 'english';
        $insert_id = $this->custom_db->insert_record('user', $data);
        $insert_id = $insert_id ['insert_id'];
        $user_data = $this->custom_db->single_table_records('user', '*', array(
            'user_id' => $insert_id
        ));
        $this->application_logger->registration($email, $email . ' Has Registered From B2C Portal', $insert_id, array(
            'user_id' => $insert_id,
            'uuid' => $data ['uuid']
        ));
        return $user_data;
    }

    // sms configuration
    function sms_configuration($sms) {
        $tmp_data = $this->db->select('*')->get_where('sms_configuration', array(
            'domain_origin' => $sms
        ));
        // echo $this->db->last_query();exit;
        return $tmp_data->row();
    }

    // Global SMS Checkpoint
    function sms_checkpoint($name) {
        $result = $this->db->select('status')->get_where('sms_checkpoint', array(
                    'condition' => $name
                ))->row();
        // echo $this->db->last_query();exit;
        // echo $result->status;exit;
        return $result->status;
    }

    /**
     * *
     * Registered new user Activation Point
     */
    function activate_account_status($status, $user_id) {
        $data = array(
            'status' => $status
        );
        $this->db->where('user_id', $user_id);
        $this->db->update('user', $data);
    }

    /**
     * verify is the user credentials are valid
     *
     * @param string $email
     *        	email of the user
     * @param
     *        	string @password password of the user
     *        	
     *        	return boolean status of the user credentials
     */
    public function get_user_details($condition = array(), $count = false, $offset = 0, $limit = 10000000000, $order_by = array()) {
        $filter_condition = ' and ';
        if (valid_array($condition) == true) {
            foreach ($condition as $k => $v) {
                $filter_condition .= implode($v) . ' and ';
            }
        }

        if (valid_array($order_by) == true) {
            $filter_order_by = 'ORDER BY';
            foreach ($order_by as $k => $v) {
                $filter_order_by .= implode($v) . ',';
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
		 	AND U.country_code=ACL.origin' . $filter_condition . ' limit ' . $limit . ' offset ' . $offset . ' ' . $filter_order_by)->result_array();
        } else {
            return $this->db->query('SELECT count(*) as total FROM user AS U, user_type AS UT, api_country_list AS ACL
		 WHERE U.user_type=UT.origin 
		 AND U.country_code=ACL.origin' . $filter_condition . ' limit ' . $limit . ' offset ' . $offset)->row();
        }
    }

    /**
     * get Domain user list in the system
     */
    function get_domain_user_list($condition = array(), $count = false, $offset = 0, $limit = 10000000000, $order_by = array()) {
        $filter_condition = ' and ';
        if (valid_array($condition) == true) {
            foreach ($condition as $k => $v) {
                $filter_condition .= implode($v) . ' and ';
            }
        }
        if (is_domain_user() == false) {
            // PROVAB ADMIN
            // GET ALL DOMAIN ADMINS DETAILS
            $filter_condition .= ' U.domain_list_fk > 0 and U.user_type = ' . ADMIN . ' and U.user_id != ' . intval($this->entity_user_id) . ' and ';
        } else if (is_domain_user() == true) {
            // DOMAIN ADMIN
            // GET ALL DOMAIN USERS DETAILS
            $filter_condition .= ' U.domain_list_fk =' . get_domain_auth_id() . ' and U.user_type != ' . ADMIN . ' and U.user_id != ' . intval($this->entity_user_id) . ' and ';
        }
        if (valid_array($order_by) == true) {
            $filter_order_by = 'ORDER BY';
            foreach ($order_by as $k => $v) {
                $filter_order_by .= implode($v) . ',';
            }
        } else {
            $filter_order_by = '';
        }
        $filter_condition = rtrim($filter_condition, 'and ');
        $filter_order_by = rtrim($filter_order_by, ',');
        if (!$count) {
            return $this->db->query('SELECT U.*, UT.user_type, ACL.country_code as country_code_value FROM user AS U, user_type AS UT, api_country_list AS ACL
		 WHERE U.user_type=UT.origin 
		 AND U.country_code=ACL.origin' . $filter_condition . ' limit ' . $limit . ' offset ' . $offset . ' ' . $filter_order_by)->result_array();
        } else {
            return $this->db->query('SELECT count(*) as total FROM user AS U, user_type AS UT, api_country_list AS ACL
		 WHERE U.user_type=UT.origin 
		 AND U.country_code=ACL.origin' . $filter_condition . ' limit ' . $limit . ' offset ' . $offset)->row();
        }
    }

    /**
     * get Logged in Users
     * Jaganath (25-05-2015) - 25-05-2015
     */
    function get_logged_in_users($condition = array(), $count = false, $offset = 0, $limit = 10000000000) {
        $filter_condition = ' and ';
        if (valid_array($condition) == true) {
            foreach ($condition as $k => $v) {
                $filter_condition .= implode($v) . ' and ';
            }
        }
        if (is_domain_user() == false) {
            // PROVAB ADMIN
            // GET ALL DOMAIN ADMINS DETAILS
            $filter_condition .= ' U.domain_list_fk > 0 and U.user_type = ' . ADMIN . ' and U.user_id != ' . intval($this->entity_user_id) . ' and ';
        } else if (is_domain_user() == true) {
            // DOMAIN ADMIN
            // GET ALL DOMAIN USERS DETAILS
            $filter_condition .= ' U.domain_list_fk =' . get_domain_auth_id() . ' and U.user_type != ' . ADMIN . ' and U.user_id != ' . intval($this->entity_user_id) . ' and ';
        }
        $filter_condition = rtrim($filter_condition, 'and ');
        $current_date = date('Y-m-d', time());
        if (!$count) {
            return $this->db->query('SELECT U.*, UT.user_type, LM.login_date_time as login_time,LM.logout_date_time as logout_time,LM.login_ip
			FROM user AS U
			JOIN user_type AS UT ON U.user_type=UT.origin
			JOIN api_country_list AS ACL ON U.country_code=ACL.origin
			JOIN login_manager AS LM ON U.user_type=LM.user_type and U.user_id=LM.user_id
		    WHERE LM.login_date_time >="' . $current_date . ' 00:00:00"
			and (LM.logout_date_time = "0000-00-00 00:00:00" or LM.logout_date_time >= "' . $current_date . ' 00:00:00")
			 ' . $filter_condition . ' order by LM.logout_date_time asc limit ' . $limit . ' offset ' . $offset)->result_array();
        } else {
            return $this->db->query('SELECT count(*) as total FROM user AS U
			JOIN user_type AS UT ON U.user_type=UT.origin
			JOIN api_country_list AS ACL ON U.country_code=ACL.origin
			JOIN login_manager AS LM ON U.user_type=LM.user_type and U.user_id=LM.user_id
		    WHERE LM.login_date_time >="' . $current_date . ' 00:00:00"
			and (LM.logout_date_time = "0000-00-00 00:00:00" or LM.logout_date_time >= "' . $current_date . ' 00:00:00")' . $filter_condition . ' limit ' . $limit . ' offset ' . $offset)->row();
        }
    }

    /**
     * get Domain List present in the system
     */
    function get_domain_details() {
        $query = 'select DL.*,CONCAT(U.first_name, " ", U.last_name) as created_user_name from domain_list DL join user U on DL.created_by_id=U.user_id';
        return $this->db->query($query)->result_array();
    }

    /**
     * update logout time
     *
     * @param number $LID
     *        	unique login id which has to be updated
     *        	
     * @return status;
     */
    function update_login_manager($user_id, $login_id) {
        $condition = array(
            'user_id' => $user_id,
            'origin' => $login_id
        );
        // update all the logout session in login manager
        $this->custom_db->update_record('login_manager', array(
            'logout_date_time' => date('Y-m-d H:i:s', time())
                ), $condition);
        $this->application_logger->logout($this->entity_name, $this->entity_user_id, array(
            'user_id' => $this->entity_user_id,
            'uuid' => $this->entity_uuid
        ));
    }

    /**
     * Create Login Manager
     */
    function create_login_auth_record($user_id, $user_type, $user_origin = 0, $username = 'customer') {
        $login_details ['browser'] = $_SERVER ['HTTP_USER_AGENT'];
        $remote_ip = $_SERVER ['REMOTE_ADDR'];
        $this->update_auth_record_expiry($user_id, $user_type, $remote_ip, $user_origin, $username);
        // logout of same user from same ip
        $login_details ['info'] = file_get_contents("http://ipinfo.io/" . $remote_ip . "/json");
        $data ['user_id'] = $user_id;
        $data ['user_type'] = $user_type;
        $data ['login_date_time'] = date('Y-m-d H:i:s');
        $data ['login_ip'] = $remote_ip;
        $data ['attributes'] = json_encode($login_details);

        $login_id = $this->custom_db->insert_record('login_manager', $data);
        $this->application_logger->login($username, $user_origin, array(
            'user_id' => $user_origin,
            'uuid' => $user_id
        ));
        return $login_id ['insert_id'];
    }

    /**
     * Update logout
     * 
     * @param
     *        	$user_id
     * @param
     *        	$user_type
     * @param
     *        	$remote_ip
     * @param
     *        	$browser
     */
    function update_auth_record_expiry($user_id, $user_type, $remote_ip, $user_origin, $username) {
        $cond ['user_id'] = $user_id;
        $cond ['user_type'] = $user_type;
        $cond ['login_ip'] = $remote_ip;
        $auth_exp = $this->custom_db->update_record('login_manager', array(
            'logout_date_time' => date('Y-m-d H:i:s')
                ), $cond);
        if ($auth_exp == true) {
            // update application logger
            $this->application_logger->logout($username, $user_origin, array(
                'user_id' => $user_origin,
                'uuid' => $user_id
            ));
        }
    }

    public function email_subscribtion($email, $domain_key) {
        $query = $this->db->get_where('email_subscribtion', array(
            'email_id' => $email
        ));
        if ($query->num_rows() > 0) {
            return "already";
        } else {
            $insert_id = $this->custom_db->insert_record('email_subscribtion', array(
                'email_id' => $email,
                'domain_list_fk' => $domain_key
            ));
            return $insert_id ['insert_id'];
        }
    }

    /*
      public function domain_login($domain_key, $username, $password, $system = 'test') {
      $resp ['status'] = false;
      $resp ['data'] = array ();

      $user_filter = '';
      if ($system == "test") {
      $user_filter .= ' and test_username = '.$this->db->escape($username);
      $user_filter .= ' and test_password = '.$this->db->escape($password);
      } else if ($system == "live") {
      $user_filter .= ' and live_username = '.$this->db->escape($username);
      $user_filter .= ' and live_password = '.$this->db->escape($password);
      } else {
      }
      //IP.ip_address = '.$this->db->escape($_SERVER['REMOTE_ADDR']).' and
      $query = 'select DL.* from domain_list DL LEFT JOIN domain_ip_list AS IP ON DL.origin=IP.domain_list_fk WHERE DL.domain_key = '.$this->db->escape($domain_key).' and DL.status = '.ACTIVE.' '.$user_filter;
      //$this->custom_db->insert_record('test', array('test' => $query));exit;
      $domain_details = $this->db->query($query)->row_array();

      if (valid_array($domain_details) == true) {


      define ( 'CURRENT_DOMAIN_KEY', $domain_details ['domain_key'] );
      define ( 'DOMAIN_IMAGE_DIR', CUSTOM_RESOURCE_DIR . '/' . CURRENT_DOMAIN_KEY . '/images/' );
      define ( 'DOMAIN_UPLOAD_DIR', CUSTOM_RESOURCE_DIR . '/' . CURRENT_DOMAIN_KEY . '/uploads/' );

      $domain_session_data = array ();
      // SETTING DOMAIN KEY
      $domain_session_data [DOMAIN_AUTH_ID] = intval ( $domain_details ['origin'] );
      // SETTING DOMAIN CONFIGURATION
      $domain_key = trim ( $domain_details ['domain_key'] );
      $domain_session_data [DOMAIN_KEY] = base64_encode ( $domain_key );
      $this->session->set_userdata ( $domain_session_data );
      $resp ['status'] = true;
      $resp ['data'] = $domain_details;
      }
      return $resp;
      } */

    /**
     * Validating Domain
     * @param unknown_type $domain_key
     * @param unknown_type $username
     * @param unknown_type $password
     * @param unknown_type $system
     */
    public function domain_login($domain_key, $username, $password, $system = 'test', $serverIp) {
        $resp ['status'] = false;
        $resp ['data'] = array();
        $user_filter = '';
        if ($system == "test") {
            $user_filter .= ' and test_username = ' . $this->db->escape($username);
            $user_filter .= ' and test_password = ' . $this->db->escape($password);
             //$user_filter .= ' and domain_ip = ' . $this->db->escape($serverIp);
              $user_filter .= ' and (domain_ip = ' . $this->db->escape($serverIp).' || IP.ip_address = ' . $this->db->escape($serverIp).')';
        } else if ($system == "live") {
            $user_filter .= ' and live_username = ' . $this->db->escape($username);
            $user_filter .= ' and live_password = ' . $this->db->escape($password);
             //$user_filter .= ' and domain_ip = ' . $this->db->escape($serverIp);
             $user_filter .= ' and (domain_ip = ' . $this->db->escape($serverIp).' || IP.ip_address = ' . $this->db->escape($serverIp).')';
            //$user_filter .= ' and domain_ip = ' . $this->db->escape($_SERVER['REMOTE_ADDR']);
        }
        //IP.ip_address = '.$this->db->escape($_SERVER['REMOTE_ADDR']).' and
        $query = 'select DL.* from domain_list DL 
					LEFT JOIN domain_ip_list AS IP ON DL.origin=IP.domain_list_fk 
					WHERE DL.domain_key = ' . $this->db->escape($domain_key) . ' and DL.status = ' . ACTIVE . ' ' . $user_filter;
        //debug($query); exit;
		$domain_details = $this->db->query($query)->row_array();
		//debug($domain_details); exit; 
        if (valid_array($domain_details) == true) {
            $resp ['status'] = true;
            $resp ['data'] = $domain_details;
        }
        return $resp;
    }

    public function add_test_data($data, $enable_json = false) {
        if ($enable_json == true) {
            $data = json_encode($data);
        }
        $this->custom_db->insert_record('test', array(
            'test' => $data
        ));
    }

    /*
     * Verify The Current Balance of Domain User
     */

    public function get_balance($amount, $system, $currency = 'INR', $consider_credit_balance = true) {
        $amount = floatval($amount);
        $status = FAILURE_STATUS;
        if ($this->verify_domain_balance == true) {
            if ($amount > 0) {

                if ($system == 'test') {

                    $query = 'SELECT DL.test_balance,DL.credit_limit,DL.due_amount, CC.country as currency, CC.value as conversion_value from domain_list as DL, currency_converter AS CC where CC.id=DL.currency_converter_fk AND DL.status=' . ACTIVE . ' and DL.origin=' . $this->db->escape(get_domain_auth_id()) . ' and DL.domain_key = ' . $this->db->escape(get_domain_key());
                  
                    $balance_record = $this->db->query($query)->row_array();

                    if ($currency == $balance_record['currency']) {
                        $balance = $balance_record['test_balance'];
                        if (empty($consider_credit_balance) == false) {
                            // Due is always stored with -ve symbol
                            $balance = $balance + floatval($balance_record ['credit_limit']) + floatval($balance_record ['due_amount']);
                        }

                        
                        $credit_limit = $balance_record['credit_limit'];
                        if ($balance >= $amount) {
                            $status = SUCCESS_STATUS;
                        } else {
                            //Notify User about current balance problem
                            //FIXME - send email, notification for less balance to domain admin and current domain admin
                            $this->application_logger->balance_status('Your Balance Is Very Low To Make Booking Of ' . $amount . ' ' . $currency);
                        }
                    } else {
                        echo 'Under Construction';
                        exit;
                    }
                } else if ($system == 'live') {

                    $query = 'SELECT DL.balance,DL.credit_limit,DL.due_amount, CC.country as currency, CC.value as conversion_value from domain_list as DL, currency_converter AS CC 	where CC.id=DL.currency_converter_fk AND DL.status=' . ACTIVE . ' and DL.origin=' . $this->db->escape(get_domain_auth_id()) . ' and DL.domain_key = ' . $this->db->escape(get_domain_key());
                    $balance_record = $this->db->query($query)->row_array();

                    if ($currency == $balance_record['currency']) {
                        $balance = $balance_record['balance'];
                        if (empty($consider_credit_balance) == false) {
                            // Due is always stored with -ve symbol
                            $balance = $balance + floatval($balance_record ['credit_limit']) + floatval($balance_record ['due_amount']);
                        }
                        if ($balance >= $amount) {
                            $status = SUCCESS_STATUS;
                        } else {
                            //Notify User about current balance problem
                            //FIXME - send email, notification for less balance to domain admin and current domain admin
                            $this->application_logger->balance_status('Your Balance Is Very Low To Make Booking Of ' . $amount . ' ' . $currency);
                        }
                    } else {
                        echo 'Under Construction';
                        exit;
                    }
                } else {
                    
                }
            }
        } else {
            $status = SUCCESS_STATUS;
        }
        return $status;
    }

    /*
     * Deduct The Amount of Domain User
     */

    public function update_balance($amount, $system) {

        $current_balance = 0;
        $cond = array('origin' => intval(get_domain_auth_id()));

        if ($system == 'test') {

            $details = $this->custom_db->single_table_records('domain_list', 'test_balance,credit_limit,due_amount', $cond);
            if ($details['status'] == true) {


                /*  Code for Adding due if booking happened with credit limit 
                 *  Balu A / Aug 2017
                 */
                $BalanceToAdded = 0;
                if ($details ['data'] [0] ['due_amount'] < 0) {
                    $TotalDueAmount = $details ['data'] [0] ['due_amount'] + $amount;
                    if ($TotalDueAmount > 0) {

                        $BalanceToAdded = $TotalDueAmount;
                        $TotalDueAmount = 0;
                    }
                }

                $details ['data'] [0] ['test_balance'] = $current_balance = ($details ['data'] [0] ['test_balance'] + $amount);
                if ($details ['data'] [0] ['test_balance'] < 0) {

                    $details ['data'] [0] ['due_amount'] += $details ['data'] [0] ['test_balance'];
                    $details ['data'] [0] ['test_balance'] = 0;
                }

                // Commanded By Balu  $details['data'][0]['test_balance'] = $current_balance = ($details['data'][0]['test_balance'] + $amount);
                $this->custom_db->update_record('domain_list', $details['data'][0], $cond);
            }
        } else if ($system == 'live') {

            $details = $this->custom_db->single_table_records('domain_list', 'balance,credit_limit,due_amount', $cond);
            if ($details['status'] == true) {


                /*  Code for Adding due if booking happened with credit limit 
                 *  Balu A / Aug 2017
                 */
                $BalanceToAdded = 0;
                if ($details ['data'] [0] ['due_amount'] < 0) {
                    $TotalDueAmount = $details ['data'] [0] ['due_amount'] + $amount;
                    if ($TotalDueAmount > 0) {

                        $BalanceToAdded = $TotalDueAmount;
                        $TotalDueAmount = 0;
                    }
                }

                $details ['data'] [0] ['balance'] = $current_balance = ($details ['data'] [0] ['balance'] + $amount);
                if ($details ['data'] [0] ['balance'] < 0) {

                    $details ['data'] [0] ['due_amount'] += $details ['data'] [0] ['balance'];
                    $details ['data'] [0] ['balance'] = 0;
                }

                // Commanded By Balu   $details['data'][0]['balance'] = $current_balance = ($details['data'][0]['balance'] + $amount);
                $this->custom_db->update_record('domain_list', $details['data'][0], $cond);
            }
        } else {
            echo "Under Consruction";
        }

        return $current_balance;
    }

    public function valid_payment_key($domain_key, $username, $password, $system = 'test', $travelomatix_payment_key) {

        $resp ['status'] = false;
        $resp ['data'] = array();

        $user_filter = '';
        if ($system == "test") {
            $user_filter .= ' and test_username = ' . $this->db->escape($username);
            $user_filter .= ' and test_password = ' . $this->db->escape($password);
        } else if ($system == "live") {
            $user_filter .= ' and live_username = ' . $this->db->escape($username);
            $user_filter .= ' and live_password = ' . $this->db->escape($password);
        } else {
            
        }
        //IP.ip_address = '.$this->db->escape($_SERVER['REMOTE_ADDR']).' and
        $query = 'select DL.* from domain_list DL LEFT JOIN domain_ip_list AS IP ON DL.origin=IP.domain_list_fk WHERE DL.domain_key = ' . $this->db->escape($domain_key) . ' and DL.travelomatix_payment_key = ' . $this->db->escape($travelomatix_payment_key) . ' and DL.status = ' . ACTIVE . ' ' . $user_filter;
        //$this->custom_db->insert_record('test', array('test' => $query));exit;
        $domain_details = $this->db->query($query)->row_array();
        //print_r($domain_details); die;
        if (valid_array($domain_details) == true) {
            $domain_session_data = array();
            // SETTING DOMAIN KEY
            $domain_session_data [DOMAIN_AUTH_ID] = intval($domain_details ['origin']);
            // SETTING DOMAIN CONFIGURATION
            $domain_key = trim($domain_details ['domain_key']);

            $domain_session_data [DOMAIN_KEY] = base64_encode($domain_key);
            $this->session->set_userdata($domain_session_data);
            $resp ['status'] = true;
            $resp ['data'] = $domain_details;
        }
        return $resp;
    }

}
