<?php
require_once 'abstract_management_model.php';
/**
 * @package    Provab Application
 * @subpackage Travel Portal
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V2
 */
Class Transaction_Model extends CI_Model
{
	/**
	 * Return Convenience Fees
	 */
	function get_convenience_fees()
	{
		$data = $this->custom_db->single_table_records('convenience_fees');
		return $data['data'];
	}
	/**
	 * Return Instant Recharge Convenience Fees
	 */
	function get_instant_recharge_convenience_fees()
	{
		$data = $this->custom_db->single_table_records('instant_recharge_con_fees');
		return $data['data'];
	}
	/**
	 * Update Convinence Fees
	 * @param $data array having list of data to be updated
	 */
	function update_convenience_fees($data)
	{
		foreach ($data['origin'] as $k => $v) {
			if (intval($v) > 0) {
				$cond['origin'] = intval($v);
			} else {
				continue;
			}
			$row['value'] = $data['value'][$k];
			$row['value_type'] = $data['value_type_'.$v];
			$row['per_pax'] = $data['per_pax_'.$v];
			$row['convenience_fee_currency'] = get_application_default_currency();
			$this->custom_db->update_record('convenience_fees', $row, $cond);
		}
	}

	/**
	 *
	 */

	public function active_agent_count(){
		$query1 = "SELECT t.*
				  FROM transaction_log as t
				  WHERE t.transaction_type != 'transaction' AND DATE(t.created_datetime) = CURDATE()
				  GROUP BY t.transaction_owner_id";
		$transaction_data1 = $this->db->query($query1)->result_array();
		if(isset($transaction_data1) && !empty($transaction_data1)){
			return count($transaction_data1);
		}else{
			return 0;
		}
	}

	public function monthly_active_agent_count(){
		$from_date = date('Y-m-01');
		$to_date = date('Y-m-t');

		while ($from_date <= $to_date) 
	        {
	        	$start_date = $from_date.' '.'00:00:00';
	        	$end_date = $from_date.' '.'23:59:59';

	    		$query1 = "SELECT t.*,U.uuid AS agent_id,U.agency_name
				  FROM transaction_log as t
				  LEFT JOIN user U ON t.transaction_owner_id = U.user_id
				  WHERE t.transaction_type != 'transaction' AND DATE(t.created_datetime)>= '".$start_date."' AND  DATE(t.created_datetime)<='".$end_date."' GROUP BY t.transaction_owner_id";
				$result = $this->db->query($query1)->result_array();
				if(isset($result[0]) && !empty($result[0])){
					foreach ($result as $key1 => $value1) {
						$result[$key1]['agent_id'] = provab_decrypt($result[$key1]['agent_id']);
					}
				}
				$data[$from_date] = $result;
	      		$from_date = date ('Y-m-d', strtotime('+1 days', strtotime($from_date)));
			}
		if(isset($data) && !empty($data)){
			return $data;
		}else{
			return 0;
		}
	}

	function recent_report(){
		$query = "SELECT t.*
				  FROM transaction_log as t
				  WHERE t.transaction_type != 'transaction' AND DATE(t.created_datetime) = CURDATE()
				  GROUP BY t.origin DESC";
		$transaction_data = $this->db->query($query)->result_array();

		$query = "SELECT u.*
				  FROM user as u";
		$user_data = $this->db->query($query)->result_array();

		$query = "SELECT b.*
				  FROM bus_booking_itinerary_details as b 
				  GROUP BY b.origin DESC LIMIT 50";
		$bus_data = $this->db->query($query)->result_array();

		$query = "SELECT f.*
				  FROM flight_booking_itinerary_details as f
				  GROUP BY f.origin DESC LIMIT 30";
		$flight_data = $this->db->query($query)->result_array();

		$recent_data = array();
		$count = 0;
		foreach ($transaction_data as $key => $value) {
			$recent_data[$count]['product'] = $value['transaction_type'];
			$recent_data[$count]['fare'] = $value['fare'];
			$recent_data[$count]['datetime'] = $value['created_datetime'];
			foreach ($user_data as $user_key => $user_value) {
				if($value['transaction_owner_id'] == $user_value['user_id']){
					$recent_data[$count]['Agency_name'] = $user_value['agency_name'];
					$recent_data[$count]['Agent_id'] = provab_decrypt($user_value['uuid']);
				}
				
			}
			if($value['transaction_type'] == 'bus'){
				
				foreach ($bus_data as $bus_key => $bus_value) {
					if($value['app_reference'] == $bus_value['app_reference']){
						$recent_data[$count]['operator'] = $bus_value['operator'];
					}
				}
			}
			if(trim($value['transaction_type']) == 'flight'){
				foreach ($flight_data as $flight_key => $flight_value) {
					if($value['app_reference'] == $flight_value['app_reference']){
						$recent_data[$count]['airline_name'] = $flight_value['airline_name'];
					}
				}
			}
			$count = $count+1;
		}
		return $recent_data;
		
	}

	function logs($condition=array(), $count=false, $offset=0, $limit=100000000000, $extra_tbl = '', $extra_cond = '')
	{	

		$condition = $this->custom_db->get_custom_condition($condition);
		//BT, CD, ID
		if (is_domain_user()) {
			if ($count) {
				$query = 'select count(*) as total_records from transaction_log t LEFT JOIN user U ON t.transaction_owner_id = U.user_id '.$extra_tbl.' where t.domain_origin = '.get_domain_auth_id().' '.$condition.' '.$extra_cond;
				$data = $this->db->query($query)->row_array();
				return $data['total_records'];
			} else {
				$query = 'select "INR" as currency, t.system_transaction_id,t.transaction_type,t.domain_origin,t.app_reference,
				t.fare,t.domain_markup as admin_markup,t.domain_markup as profit,t.level_one_markup as agent_markup, t.credit_limit as credit_limit, t.convinence_fees as convinence_amount,t.promocode_discount as discount,
				t.remarks,t.created_datetime,t.transaction_owner_id, concat(U.first_name, " ", U.last_name) as username, agency_name as agent_name, U.uuid from transaction_log t, user U '.$extra_tbl.' where t.transaction_owner_id=U.user_id AND t.domain_origin='.get_domain_auth_id().' '.$condition.' '.$extra_cond.' 
				order by t.origin desc limit '.$offset.', '.$limit;
				//debug($query);die();
				return $this->db->query($query)->result_array();
			}
		} else {
			if ($count) {
				$query = 'select count(*) as total_records from transaction_log t LEFT JOIN user U ON t.transaction_owner_id=U.user_id where 1 = 1 '.$condition;
				$data = $this->db->query($query)->row_array();
				return $data['total_records'];
			} else {
				$query = 'select t.system_transaction_id,t.transaction_type,t.domain_origin,t.app_reference,
				t.fare,t.domain_markup as admin_markup,t.domain_markup as profit,t.level_one_markup as agent_markup, t.credit_limit as credit_limit, TL.convinence_fees as convinence_amount,TL.promocode_discount as discount,
				t.remarks,t.created_datetime, concat(U.first_name, " ", U.last_name) as username, agency_name as agent_name,TB.booking_source,(SELECT name FROM booking_source where source_id =TB.booking_source) as supplier from transaction_log t, user U, temp_booking as TB on where 1=1 '.$condition.' AND t.transaction_owner_id=U.user_id AND TB.book_id=t.app_reference order by t.origin desc limit '.$offset.', '.$limit;

				//debug($query);die();
				return $this->db->query($query)->result_array();
			}
		}
	}

	function gateway_logs($condition='', $count=false, $offset=0, $extra_tbl='',$limit=100000000000)
	{
		if(strlen($condition) > 40){
			if($count)
			{
				$query = "SELECT COUNT(*) AS total_records FROM user u, payment_gateway_details p, 
				transaction_log t ".$extra_tbl." WHERE u.user_id = p.transaction_owner_id AND 
				p.app_reference = t.app_reference AND u.user_type = ".B2B_USER." AND ".$condition;
				//exit($query);
				$data = $this->db->query($query)->row_array()['total_records'];
			}
			else
			{
				$query = "SELECT u.user_id AS user_id, u.uuid AS uuid, u.user_type AS user_type, u.email AS
				email, u.user_name AS user_name, u.agency_name AS agency_name, p.app_reference AS 
				app_reference, p.amount AS amount, p.currency AS currency, p.status AS status, p.request_params AS request_params, p.response_params AS response_params,
				p.refund_params AS refund_params, p.created_datetime AS created_datetime, p.pg_name AS pg_name, p.payment_mode AS payment_mode FROM 
				user u, payment_gateway_details p".$extra_tbl." WHERE 
				u.user_id = p.transaction_owner_id AND 
				u.user_type = ".B2B_USER." AND pg_name!='WALLET' AND ".$condition." order by p.origin desc limit ".$offset.", 
				".$limit;
				$data = $this->db->query($query)->result_array();
			}
		}else{
			if($count)
			{
				$query = "SELECT COUNT(*) AS total_records FROM user u, payment_gateway_details p, 
				transaction_log t ".$extra_tbl." WHERE u.user_id = p.transaction_owner_id AND DATE(p.created_datetime) = CURDATE() AND 
				p.app_reference = t.app_reference AND u.user_type = ".B2B_USER." AND ".$condition;
				//exit($query);
				$data = $this->db->query($query)->row_array()['total_records'];
			}
			else
			{
				$query = "SELECT u.user_id AS user_id, u.uuid AS uuid, u.user_type AS user_type, u.email AS
				email, u.user_name AS user_name, u.agency_name AS agency_name, p.app_reference AS 
				app_reference, p.amount AS amount, p.currency AS currency, p.status AS status, p.request_params AS request_params, p.response_params AS response_params,
				p.refund_params AS refund_params, p.created_datetime AS created_datetime, p.pg_name AS pg_name, p.payment_mode AS payment_mode FROM 
				user u, payment_gateway_details p".$extra_tbl." WHERE 
				u.user_id = p.transaction_owner_id AND DATE(p.created_datetime) = CURDATE() AND 
				u.user_type = ".B2B_USER." AND pg_name!='WALLET' AND ".$condition." order by p.origin desc limit ".$offset.", 
				".$limit;
				$data = $this->db->query($query)->result_array();
			}
		}
		
		
		return $data;
	}
	function flight_tds_gst_report($condition)
	{
		$condition = $this->custom_db->get_custom_condition($condition);
		$sql = "SELECT u.agency_name As agency_name, u.uuid AS uuid, u.first_name AS first_name, u.last_name AS last_name, SUM(admin_commission) AS admin_commission, 
				SUM(agent_commission) AS agent_commission, SUM(admin_tds) AS 
				admin_tds, SUM(agent_tds) AS agent_tds, SUM(gst) AS gst FROM 
				flight_booking_transaction_details fbtd, 
				flight_booking_details bd, user u WHERE fbtd.app_reference = 
				bd.app_reference AND bd.created_by_id = u.user_id ".$condition." GROUP BY u.agency_name, u.uuid, u.first_name, u.last_name";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	function bus_tds_gst_report($condition)
	{
		$condition = $this->custom_db->get_custom_condition($condition);
		$sql = "SELECT u.agency_name As agency_name, u.uuid AS uuid, u.first_name AS first_name, u.last_name AS last_name, SUM(admin_commission) AS admin_commission, 
				SUM(agent_commission) AS agent_commission, SUM(admin_tds) AS 
				admin_tds, SUM(agent_tds) AS agent_tds, SUM(admin_markup) AS admin_markup FROM 
				bus_booking_customer_details bbcd, 
				bus_booking_details bd, user u WHERE bbcd.app_reference = 
				bd.app_reference AND bd.created_by_id = u.user_id ".$condition."GROUP BY u.agency_name, u.uuid, u.first_name, u.last_name";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	//*************************************Pace Travels*************************************************
	function b2b_booking_report($from_date,$to_date)
	{
		if(isset($from_date) && !empty($from_date)){
			$from_date1 = date('Y-m-d',strtotime($from_date));
			$from_date  = $from_date1.' 00:00:00';
		}else{
			$from_date1 = date('Y-m-d');
			$from_date  = $from_date1.' 00:00:00';
		}
		if(isset($to_date) && !empty($to_date)){
			$to_date1 = date('Y-m-d',strtotime($to_date));
			$to_date  = $to_date1.' 23:59:59';
		}else{
			$to_date1 = date('Y-m-d');
			$to_date  = $to_date1.' 23:59:59';
		}
		$agent_query = 'SELECT user_id,uuid,agency_name,email,address,phone FROM user';
		$agent_data = $this->db->query($agent_query)->result_array();

		$flight_query = 'SELECT app_reference,created_by_id,status
				FROM flight_booking_details
				WHERE created_datetime >= "'.$from_date.'" AND created_datetime <= "'.$to_date.'"';
		$flight_data = $this->db->query($flight_query)->result_array();

		$bus_query = 'SELECT app_reference,created_by_id,status
				FROM bus_booking_details
				WHERE created_datetime >= "'.$from_date.'" AND created_datetime <= "'.$to_date.'"';
		$bus_data = $this->db->query($bus_query)->result_array();

		$flight_details = array();
		//foreach ($agent_data as $a_key => $a_value) {
			foreach ($flight_data as $f_key => $f_value) {
				if($f_value['status'] == 'BOOKING_CANCELLED'){
					$query = 'SELECT app_reference,status
					FROM flight_booking_passenger_details
					WHERE app_reference = "'.$f_value['app_reference'].'"';
					$data['seats'] = $this->db->query($query)->result_array();
					$result = array_merge($f_value, $data);
					$flight_details[$f_value['created_by_id']]['BOOKING_CANCELLED'][] = $result;
				}else if($f_value['status'] == 'BOOKING_CONFIRMED'){
					$query = 'SELECT app_reference,status
					FROM flight_booking_passenger_details
					WHERE app_reference = "'.$f_value['app_reference'].'"';
					$data['seats'] = $this->db->query($query)->result_array();
					$result = array_merge($f_value, $data);
					$flight_details[$f_value['created_by_id']]['BOOKING_CONFIRMED'][] = $result;
				}		
			}

			$bus_details = array();
			foreach ($bus_data as $b_key => $b_value) {
				if($b_value['status'] == 'BOOKING_CANCELLED'){
					$query = 'SELECT app_reference,status
					FROM bus_booking_customer_details
					WHERE app_reference = "'.$b_value['app_reference'].'"';
					$data['seats'] = $this->db->query($query)->result_array();
					$result = array_merge($b_value, $data);
					$bus_details[$b_value['created_by_id']]['BOOKING_CANCELLED'][] = $result;
				}else if($f_value['status'] == 'BOOKING_CONFIRMED'){
					$query = 'SELECT app_reference,status
					FROM flight_booking_passenger_details
					WHERE app_reference = "'.$b_value['app_reference'].'"';
					$data['seats'] = $this->db->query($query)->result_array();
					$result = array_merge($b_value, $data);
					$bus_details[$b_value['created_by_id']]['BOOKING_CONFIRMED'][] = $result;
				}		
			}

			$response['agent_data']     = $agent_data;
			$response['flight_details'] = $flight_details;
			$response['bus_details']    = $bus_details;
			return $response;
	}

	//****************failed payment updation**********************
	public function get_details_by_order_id($order_id){

		$query = 'SELECT p.*,U.due_amount,U.credit_limit
				FROM payment_gateway_details p
				LEFT JOIN b2b_user_details U ON p.transaction_owner_id = U.user_oid
				WHERE app_reference = "'.$order_id.'"';
		$data = $this->db->query($query)->result_array();
		return $data;
	}

	public function update_agent_balance($data,$id){
		$this->db->where('user_oid', $id);
		$this->db->update('b2b_user_details', $data);
	}

	public function update_payment_deatils($data,$order_id){
		$this->db->where('app_reference', $order_id);
		$this->db->update('payment_gateway_details', $data);
	}

	public function update_master_transaction_details($master_transaction_details,$id){
		$this->db->where('origin', $id);
		$this->db->update('master_transaction_details', $master_transaction_details);
	}

	public function insert_transaction_log_details($data){
		  $this->db->insert('transaction_log',$data);  
	}
}
