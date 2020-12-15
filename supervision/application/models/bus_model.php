<?php
include_once 'report_model.php';
/**
 * @package    Provab Application
 * @subpackage Travel Portal
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V2
 */
Class Bus_Model extends CI_Model implements report_model
{
	/**
	 *
	 * @param array $condition EX : array(array('booking_id', '=', 123))
	 * @param number $count
	 * @param number $offset
	 * @param number $limit
	 */
	function booking($condition=array(), $count=false, $offset=0, $limit=100000000000)
	{
		$condition = $this->custom_db->get_custom_condition($condition);
		if ($count) {
			$query = 'select count(distinct(BD.app_reference)) as total_records from bus_booking_details BD
					join bus_booking_customer_details BBCD on BD.app_reference=BBCD.app_reference 
					join bus_booking_itinerary_details AS ID on BD.app_reference=ID.app_reference
				 	join payment_option_list as POL on POL.payment_category_code=BD.payment_mode 
					where domain_origin='.get_domain_auth_id().''.$condition;
			$data = $this->db->query($query)->row_array();
			return $data['total_records'];
		} else {
			$this->load->library('booking_data_formatter');
			$response['status'] = SUCCESS_STATUS;
			$response['data'] = array();
			$booking_itinerary_details	= array();
			$booking_customer_details	= array();
			$bd_query = 'select * from bus_booking_details AS BD 
						WHERE BD.domain_origin='.get_domain_auth_id().'
						order by BD.origin desc limit '.$offset.', '.$limit;
			//'.$condition.'
			$booking_details = $this->db->query($bd_query)->result_array();
			$app_reference_ids = $this->booking_data_formatter->implode_app_reference_ids($booking_details);
			if(empty($app_reference_ids) == false) {
				$id_query = 'select * from bus_booking_itinerary_details AS ID 
							WHERE ID.app_reference IN ('.$app_reference_ids.')';
				$cd_query = 'select * from bus_booking_customer_details AS CD 
							WHERE CD.app_reference IN ('.$app_reference_ids.')';
				$booking_itinerary_details	= $this->db->query($id_query)->result_array();
				$booking_customer_details	= $this->db->query($cd_query)->result_array();
			}
			$response['data']['booking_details']			= $booking_details;
			$response['data']['booking_itinerary_details']	= $booking_itinerary_details;
			$response['data']['booking_customer_details']	= $booking_customer_details;
			return $response;
		}
	}
//------------------sudheep------------
	function b2c_bus_report($condition=array(), $count=false, $offset=0, $limit=100000000000)
	{
		$condition = $this->custom_db->get_custom_condition($condition);

		if(isset($condition) == true)
		{
			$offset = 0;
		}else{
			$offset = $offset;
		}

		if ($count) {

			$query = 'select count(distinct(BD.app_reference)) as total_records from bus_booking_details BD
					join bus_booking_customer_details BBCD on BD.app_reference=BBCD.app_reference
					join bus_booking_itinerary_details AS ID on BD.app_reference=ID.app_reference
					join payment_option_list as POL on POL.payment_category_code=BD.payment_mode
					left join user as U on BD.created_by_id = U.user_id  where (U.user_type='.B2C_USER.'
					OR BD.created_by_id = 0) AND  domain_origin='.get_domain_auth_id().''.$condition;

			$data = $this->db->query($query)->row_array();
		//	debug($data); die;
			return $data['total_records'];
		} else {
			$this->load->library('booking_data_formatter');
			$response['status'] = SUCCESS_STATUS;
			$response['data'] = array();
			$booking_itinerary_details	= array();
			$booking_customer_details	= array();

			$bd_query = 'select BD.*,U.user_name,U.first_name,U.last_name from bus_booking_details AS BD
						 left join user U on BD.created_by_id =U.user_id
						WHERE  (U.user_type='.B2C_USER.' OR BD.created_by_id = 0) AND BD.domain_origin='.get_domain_auth_id().' '.$condition.'
						 order by BD.created_datetime desc, BD.origin desc limit '.$offset.', '.$limit;
			
			$booking_details = $this->db->query($bd_query)->result_array();
			$app_reference_ids = $this->booking_data_formatter->implode_app_reference_ids($booking_details);
			if(empty($app_reference_ids) == false) {
				$id_query = 'select * from bus_booking_itinerary_details AS ID 
							WHERE ID.app_reference IN ('.$app_reference_ids.')';
				$cd_query = 'select * from bus_booking_customer_details AS CD 
							WHERE CD.app_reference IN ('.$app_reference_ids.')';
				$pd_query = 'select * from  payment_gateway_details AS PD
							WHERE PD.app_reference IN ('.$app_reference_ids.')';
				$booking_itinerary_details	= $this->db->query($id_query)->result_array();
				$booking_customer_details	= $this->db->query($cd_query)->result_array();
				$payment_details = $this->db->query($pd_query)->result_array();
			}
			$response['data']['booking_details']			= $booking_details;
			$response['data']['booking_itinerary_details']	= $booking_itinerary_details;
			$response['data']['booking_customer_details']	= $booking_customer_details;
			$response['data']['payment_details']	= $payment_details;
			// debug($response);exit;
			return $response;
		}
	}
	function b2b_bus_report($condition=array(), $count=false, $offset=0, $limit=100000000000, $b_status = '')
	{
		$condition = $this->custom_db->get_custom_condition($condition);
		
		if(isset($condition) == true)
		{
			$offset = 0;
		}else{
			$offset = $offset;
		}

		if($b_status == "confirmed_cancelled"){
			$condition .= 'AND (BD.status = '.$this->db->escape('BOOKING_CONFIRMED').' OR BD.status = '.$this->db->escape('BOOKING_CANCELLED').')';
		}

		if ($count) {

			$query = 'select count(distinct(BD.app_reference)) as total_records from bus_booking_details BD 
					join bus_booking_customer_details BBCD on BD.app_reference=BBCD.app_reference 
					join bus_booking_itinerary_details AS ID on BD.app_reference=ID.app_reference 
					left join user as U on BD.created_by_id = U.user_id where U.user_type='.B2B_USER.' 
					AND  domain_origin='.get_domain_auth_id().''.$condition;
			//debug($query); die;

			$data = $this->db->query($query)->row_array();
			return $data['total_records'];
		} else {
			$this->load->library('booking_data_formatter');
			$response['status'] = SUCCESS_STATUS;
			$response['data'] = array();
			$booking_itinerary_details	= array();
			$booking_customer_details	= array();
			$bd_query = 'select BD.* ,U.agency_name,U.first_name,U.last_name, U.uuid AS agency_id, BS.name As supp_name from bus_booking_details AS BD
					     left join user U on U.user_id = BD.created_by_id left join booking_source BS on BS.source_id = BD.booking_source
						 WHERE  U.user_type='.B2B_USER.' AND BD.domain_origin='.get_domain_auth_id().' '.$condition.'
						 order by BD.created_datetime desc, BD.origin desc limit '.$offset.', '.$limit;
			//debug($bd_query); die;
			$booking_details = $this->db->query($bd_query)->result_array();
			$app_reference_ids = $this->booking_data_formatter->implode_app_reference_ids($booking_details);
			if(empty($app_reference_ids) == false) {
				$id_query = 'select * from bus_booking_itinerary_details AS ID 
							WHERE ID.app_reference IN ('.$app_reference_ids.')';
				$cd_query = 'select * from bus_booking_customer_details AS CD 
							WHERE CD.app_reference IN ('.$app_reference_ids.')';
				$booking_itinerary_details	= $this->db->query($id_query)->result_array();
				$booking_customer_details	= $this->db->query($cd_query)->result_array();
			}
			$response['data']['booking_details']			= $booking_details;
			$response['data']['booking_itinerary_details']	= $booking_itinerary_details;
			$response['data']['booking_customer_details']	= $booking_customer_details;
			return $response;
		}
	}
	
	/**
	 * Read Individual booking details - dont use it to generate table
	 * @param $app_reference
	 * @param $booking_source
	 * @param $booking_status
	 */
	function get_booking_details($app_reference, $booking_source, $booking_status='', $call_to_show='')
	{
		//bus_booking_details
		//bus_booking_itinerary_details
		//bus_booking_customer_details
		$response['status'] = FAILURE_STATUS;
		$response['data'] = array();
		$bd_query = 'select * from bus_booking_details AS BD WHERE BD.app_reference like '.$this->db->escape($app_reference);
		if (empty($booking_source) == false) {
			$bd_query .= '	AND BD.booking_source = '.$this->db->escape($booking_source);
		}
		if (empty($booking_status) == false && empty($call_to_show) == true) {
			$bd_query .= ' AND BD.status = '.$this->db->escape($booking_status);
		}
		if($call_to_show == 'partial_cancellation_also')
		{
			$bd_query .= ' AND (BD.status = '.$this->db->escape($booking_status).' OR BD.status = "BOOKING_CONFIRMED")';
		}
		$id_query = 'select * from bus_booking_itinerary_details AS ID WHERE ID.app_reference='.$this->db->escape($app_reference);
		$cd_query = 'select * from bus_booking_customer_details AS CD WHERE CD.app_reference='.$this->db->escape($app_reference);
		$cancellation_details_query = 'select BCD.* from bus_cancellation_details AS BCD WHERE BCD.app_reference='.$this->db->escape($app_reference);
		$response['data']['booking_details']			= $this->db->query($bd_query)->result_array();
		$response['data']['booking_itinerary_details']	= $this->db->query($id_query)->result_array();
		$response['data']['booking_customer_details']	= $this->db->query($cd_query)->result_array();
		$response['data']['cancellation_details']	= $this->db->query($cancellation_details_query)->result_array();
		if (valid_array($response['data']['booking_details']) == true and valid_array($response['data']['booking_itinerary_details']) == true and valid_array($response['data']['booking_customer_details']) == true) {
			$response['status'] = SUCCESS_STATUS;
		}
		return $response;
	}

	function get_cancellation_details($app_reference)
	{
		$cancellation_details_query = 'select BCD.* from bus_cancellation_details AS BCD WHERE BCD.app_reference='.$this->db->escape($app_reference);
		return $this->db->query($cancellation_details_query)->result_array();
	}

	/**
	 * Get auth token for bus - only for travel yaari
	 */
	function get_auth_token()
	{
		//get_auth_token
		$data = $this->custom_db->single_table_records('temp_cache', '*', array('domain_list_fk' => get_domain_auth_id(), 'type' => 'travelyaari'));
		if ($data['status']== SUCCESS_STATUS) {
			return $data['data'][0];
		} else {
			return false;
		}
	}

	/**
	 * Set auth token cache for travel yaari
	 * @param unknown_type $data
	 */
	function set_auth_token($data)
	{
		$this->custom_db->insert_record('temp_cache', array('domain_list_fk' => get_domain_auth_id(), 'type' => 'travelyaari', 'data' => $data, 'created_datetime' => date('Y-m-d H:i:s')));
	}

	/**
	 * return all booking events
	 */
	function booking_events()
	{
		//BT, CD, ID
		$query = 'select * from bus_booking_details where domain_origin='.get_domain_auth_id();
		return $this->db->query($query)->result_array();
	}

	function get_monthly_booking_summary($condition=array())
	{
		$condition = $this->custom_db->get_custom_condition($condition);
		$query = 'select count(distinct(BD.app_reference)) AS total_booking, sum(BBCD.fare+BBCD.admin_markup+BBCD.agent_markup) as monthly_payment, 
		sum(BBCD.admin_commission) as monthly_earning, MONTH(BD.created_datetime) as month_number
		from bus_booking_details BD
		join bus_booking_customer_details BBCD on BD.app_reference=BBCD.app_reference
		where (YEAR(BD.created_datetime) BETWEEN '.date('Y').' AND '.date('Y', strtotime('+1 year')).')  and BD.domain_origin='.get_domain_auth_id().' '.$condition.' 
		GROUP BY YEAR(BD.created_datetime), MONTH(BD.created_datetime)';
		return $this->db->query($query)->result_array();
	}
	function get_daily_booking_summary()
	{
		$query = 'select count(*) AS total_booking, sum(BD.total_fare+BD.level_one_markup) as daily_payment, sum(BD.domain_markup) as daily_earning from bus_booking_details AS BD
		where BD.created_datetime>= "'.date("Y-m-d 00:00:00").'" AND BD.created_datetime<= "'.date("Y-m-d 23:59:59").'"  AND BD.domain_origin='.get_domain_auth_id();
		return $this->db->query($query)->result_array();
	}

	function monthly_search_history($year_start, $year_end)
	{
		$query = 'select count(*) AS total_search, MONTH(created_datetime) as month_number from search_bus_history where
		(YEAR(created_datetime) BETWEEN '.$year_start.' AND '.$year_end.') AND domain_origin='.get_domain_auth_id().' 
		AND search_type="'.META_BUS_COURSE.'"
		GROUP BY YEAR(created_datetime), MONTH(created_datetime)';
		return $this->db->query($query)->result_array();
	}

	function top_search($year_start, $year_end)
	{
		$query = 'select count(*) AS total_search, concat(from_station, "-",to_station) label from search_bus_history where
		(YEAR(created_datetime) BETWEEN '.$year_start.' AND '.$year_end.') AND domain_origin='.get_domain_auth_id().' 
		AND search_type="'.META_BUS_COURSE.'"
		GROUP BY CONCAT(from_station, to_station) order by count(*) desc, created_datetime desc limit 0, 15';
		return $this->db->query($query)->result_array();
	}
/*
	 * Balu A
	 * Update cancellation details
	 */
	function update_cancellation_details($app_reference, $booking_status, $cancellation_details, 
		$refund_status="INPROGRESS",$cancel_type ="",$seat_to_cancel=array(),$commission_to_deduct='')
	{
		
		//1. Update Master Booking Status
		if($cancel_type == 'partial'){
			foreach ($seat_to_cancel as $key => $value) {
				$update_condition['app_reference'] = trim($app_reference);
				$update_condition['seat_no'] = $value;
				$update_data['status'] = trim($booking_status);

				$GLOBALS['CI']->custom_db->update_record('bus_booking_customer_details', $update_data, $update_condition);
			}
				
		}else{
			$update_condition['app_reference'] = trim($app_reference);
			$update_data['status'] = trim($booking_status);
			$GLOBALS['CI']->custom_db->update_record('bus_booking_details', $update_data, $update_condition);
			//2. Update Customer Ticket Status
			$GLOBALS['CI']->custom_db->update_record('bus_booking_customer_details', $update_data, $update_condition);
		}
		
		//3.Adding cancellationde details
		$bus_cancellation_details = array();
		$CancelTicket2Result  = $cancellation_details['data']['CancelSeats'];
		$ApiRefundAmount = $CancelTicket2Result['ApiRefundAmount'];
		$RefundAmount = $CancelTicket2Result['RefundAmount'];
		$ChargePct = $CancelTicket2Result['ChargePct'];
		$cancel_charge = $CancelTicket2Result['ChargeAmt'];
		$supp_commission_reversed = $CancelTicket2Result["supp_commission_reversed"];
		
		$bus_cancellation_details['app_reference'] = 				$app_reference;
		$bus_cancellation_details['cancellation_status'] = 			$booking_status;
		$bus_cancellation_details['refund_status'] = $refund_status;
		$bus_cancellation_details['api_refund_amount'] = 			$RefundAmount;
		$bus_cancellation_details['api_cancel_charge_percentage'] =	$ChargePct;
		$bus_cancellation_details['created_by_id'] = 				intval(@$this->entity_user_id);
		$bus_cancellation_details['created_datetime'] = 			db_current_datetime();
		$bus_cancellation_details['attributes'] = 					json_encode($cancellation_details);
		//for pdo not taking empty value
		$bus_cancellation_details['refund_amount'] = $RefundAmount;
		$bus_cancellation_details['api_refund_amount'] = $ApiRefundAmount;
		$bus_cancellation_details['cancel_charge_percentage'] = $ChargePct;
		$bus_cancellation_details['cancel_charge'] = $cancel_charge;
		$bus_cancellation_details['api_cancel_charge'] =	$cancel_charge;
		$bus_cancellation_details['refund_comments'] ='';
		$bus_cancellation_details['refund_date'] = db_current_datetime();
		$bus_cancellation_details['commission_reversed'] = $commission_to_deduct;
		$bus_cancellation_details['supp_commission_reversed'] = $supp_commission_reversed;
		return $this->custom_db->insert_record('bus_cancellation_details', $bus_cancellation_details);
	}
	function get_static_response($token_id)
	{
		$static_response = $this->custom_db->single_table_records('test', '*', array('origin' => intval($token_id)));
		return json_decode($static_response['data'][0]['test'], true);
	}

	//Cancellation Report
	function cancellation_report($page){
		$response = array();
		if($page == 'dashboard'){
			$response = $this->custom_db->single_table_records('bus_booking_details', 'COUNT(origin) as cancel_tickets', array('status' => 'BOOKING_CANCELLED'));
		}else{
			$response = $this->custom_db->single_table_records('bus_booking_details', '*', array('status' => 'BOOKING_CANCELLED'));
		}
		
		return $response;
	}

	function bus_supplier(){
		$response = $this->custom_db->single_table_records('booking_source', '*', array('meta_course_list_id' => META_BUS_COURSE,'booking_engine_status' => 1));

		return $response;
	}
	
	function bitla_direct_api_list(){
		$response = $this->custom_db->single_table_records('booking_source', '*', array('meta_course_list_id' => META_BUS_COURSE,'travel_id !=' => 0))["data"];
		$list = array();
		foreach($response AS $do){
			$list[$do["travel_id"]] = $do["name"];
		}
		return $list;
	}

	function b2b_cancel_bus_report($condition=array(), $count=false, $offset=0, $limit=100000000000)
	{
		$condition = $this->custom_db->get_custom_condition($condition);
		//debug($condition);die();
		if(isset($condition) == true)
		{
			$offset = 0;
		}else{
			$offset = $offset;
		}

		if ($count) {

			$query = 'select count(distinct(BD.app_reference)) as total_records from bus_booking_details BD 
					join bus_booking_customer_details BBCD on BD.app_reference=BBCD.app_reference 
					join bus_booking_itinerary_details AS ID on BD.app_reference=ID.app_reference 
					join payment_option_list as POL on POL.payment_category_code=BD.payment_mode 
					left join user as U on BD.created_by_id = U.user_id where U.user_type='.B2B_USER.' 
					AND BD.status = "BOOKING_CANCELLED" AND domain_origin='.get_domain_auth_id().''.$condition;
			$data = $this->db->query($query)->row_array();
			//debug($data['total_records']);die('=');
			return $data['total_records'];
		} else {
			$this->load->library('booking_data_formatter');
			$response['status'] = SUCCESS_STATUS;
			$response['data'] = array();
			$booking_itinerary_details	= array();
			$booking_customer_details	= array();
			$bd_query = 'select BD.* ,U.agency_name,U.first_name,U.last_name from bus_booking_details AS BD
					     left join user U on U.user_id = BD.created_by_id
						 WHERE  U.user_type='.B2B_USER.' AND BD.status = "BOOKING_CANCELLED" AND BD.domain_origin='.get_domain_auth_id().' '.$condition.'
						 order by BD.created_datetime desc, BD.origin desc limit '.$offset.', '.$limit;

			$booking_details = $this->db->query($bd_query)->result_array();
			$app_reference_ids = $this->booking_data_formatter->implode_app_reference_ids($booking_details);
			if(empty($app_reference_ids) == false) {
				$id_query = 'select * from bus_booking_itinerary_details AS ID 
							WHERE ID.app_reference IN ('.$app_reference_ids.')';
				$cd_query = 'select * from bus_booking_customer_details AS CD 
							WHERE CD.app_reference IN ('.$app_reference_ids.')';
				$booking_itinerary_details	= $this->db->query($id_query)->result_array();
				$booking_customer_details	= $this->db->query($cd_query)->result_array();
			}
			$response['data']['booking_details']			= $booking_details;
			$response['data']['booking_itinerary_details']	= $booking_itinerary_details;
			$response['data']['booking_customer_details']	= $booking_customer_details;
			return $response;
		}
	}

	function b2c_cancel_bus_report($condition=array(), $count=false, $offset=0, $limit=100000000000)
	{
		$condition = $this->custom_db->get_custom_condition($condition);

		if(isset($condition) == true)
		{
			$offset = 0;
		}else{
			$offset = $offset;
		}

		if ($count) {

			$query = 'select count(distinct(BD.app_reference)) as total_records from bus_booking_details BD
					join bus_booking_customer_details BBCD on BD.app_reference=BBCD.app_reference
					join bus_booking_itinerary_details AS ID on BD.app_reference=ID.app_reference
					join payment_option_list as POL on POL.payment_category_code=BD.payment_mode
					left join user as U on BD.created_by_id = U.user_id  where (U.user_type='.B2C_USER.'
					OR BD.created_by_id = 0) AND BD.status = "BOOKING_CANCELLED" AND  domain_origin='.get_domain_auth_id().''.$condition;

			$data = $this->db->query($query)->row_array();
		//	debug($data); die;
			return $data['total_records'];
		} else {
			$this->load->library('booking_data_formatter');
			$response['status'] = SUCCESS_STATUS;
			$response['data'] = array();
			$booking_itinerary_details	= array();
			$booking_customer_details	= array();

			$bd_query = 'select BD.*,U.user_name,U.first_name,U.last_name from bus_booking_details AS BD
						 left join user U on BD.created_by_id =U.user_id
						WHERE  (U.user_type='.B2C_USER.' OR BD.created_by_id = 0) AND BD.status = "BOOKING_CANCELLED" AND BD.domain_origin='.get_domain_auth_id().' '.$condition.'
						 order by BD.created_datetime desc, BD.origin desc limit '.$offset.', '.$limit;
			
			$booking_details = $this->db->query($bd_query)->result_array();
			$app_reference_ids = $this->booking_data_formatter->implode_app_reference_ids($booking_details);
			if(empty($app_reference_ids) == false) {
				$id_query = 'select * from bus_booking_itinerary_details AS ID 
							WHERE ID.app_reference IN ('.$app_reference_ids.')';
				$cd_query = 'select * from bus_booking_customer_details AS CD 
							WHERE CD.app_reference IN ('.$app_reference_ids.')';
				$pd_query = 'select * from  payment_gateway_details AS PD
							WHERE PD.app_reference IN ('.$app_reference_ids.')';
				$booking_itinerary_details	= $this->db->query($id_query)->result_array();
				$booking_customer_details	= $this->db->query($cd_query)->result_array();
				$payment_details = $this->db->query($pd_query)->result_array();
			}
			$response['data']['booking_details']			= $booking_details;
			$response['data']['booking_itinerary_details']	= $booking_itinerary_details;
			$response['data']['booking_customer_details']	= $booking_customer_details;
			$response['data']['payment_details']	= $payment_details;
			// debug($response);exit;
			return $response;
		}
	}

	///** Offline booking **///

	public function offline_bus_book($bus_data,$app_reference){
		
		$status = empty($bus_data['status'])?'BOOKING_PENDING':$bus_data['status'];
		$agent_id = $GLOBALS['CI']->entity_user_id;
		//exit("Here");
		/**Bus booking details start**/
		$booking_details['domain_origin'] = 1;
		$booking_details['status'] = $bus_data['status'];
		$booking_details['app_reference'] = $app_reference;
		$booking_details['booking_source'] = $bus_data['suplier_id'];
		$booking_details['pnr'] = implode(',',$bus_data['pax_ticket_num_onward']);
		$booking_details['ticket'] = $bus_data['airline_pnr_onward'][0];
		$booking_details['transaction'] = $booking_details['ticket'];
		$booking_details['phone_number'] = $bus_data ['passenger_phone'];
		$booking_details['alternate_number'] = $bus_data ['passenger_phone'];
		$booking_details['email'] = $bus_data ['passenger_email'];
		$booking_details['payment_mode'] = 'offline';
		$booking_details['convinence_value'] = 0.0;
		$booking_details['convinence_value_type'] = 'plus';
		$booking_details['convinence_per_pax'] = 0;
		$booking_details['convinence_amount'] = 0;
		$booking_details['promo_code'] = 0;
		$booking_details['discount'] = 0;
		$booking_details['booking_billing_type'] = 'offline';
		$booking_details['created_datetime'] = date ( 'Y-m-d H:i:s' );
		$booking_details['currency'] = admin_base_currency();
		$booking_details['gst'] = $bus_data['gst'];
		
		$booking_details['travel_id'] = $bus_data ['travel_id'];
		//As Per client demand - agent wise offline booking
		if($bus_data['agent_id'] > 0){
			$booking_details['created_by_id'] = $bus_data['agent_id'];
			$booking_details['booked_by'] = $agent_id;
		}else{
			$booking_details['created_by_id'] = $agent_id;
			$booking_details['booked_by'] = $agent_id;
		}
		$cp_arr = array();
		foreach($bus_data["c_perc"] AS $cpk=>$cpv){
			$cp_arr[$cpk]["Mins"] = $bus_data["c_to_hours"][$cpk]*60;
			$cp_arr[$cpk]["Pct"] = $bus_data["c_perc"][$cpk];
			$cp_arr[$cpk]["Amt"] = 0;
		}
		$cp_b64 = base64_encode(json_encode($cp_arr));
		
		$booking_details['cancel_policy'] = $cp_b64;
		
		$book_id = $this->custom_db->insert_record ( 'bus_booking_details', $booking_details );
		$book_id = @$book_id['insert_id'];

		/**Bus booking details End**/
		//$book_id = 279;
		//$app_reference = 'BB09-123430-387717';
		/**Bus Itinerary details start**/
		$dep_date = date('Y-m-d',strtotime($bus_data['dep_date_onward'][0]));
		$arr_date = date('Y-m-d',strtotime($bus_data['arr_date_onward'][0]));
		$__dep_date_time = $dep_date.' '.$bus_data['dep_time_onward'][0];
		$__arr_date_time = $arr_date.' '.$bus_data['arr_time_onward'][0];

		$itinerary_details['app_reference'] = $app_reference;
		$itinerary_details['journey_datetime'] = $__dep_date_time;
		$itinerary_details['departure_datetime'] = $__dep_date_time;
		$itinerary_details['arrival_datetime'] = $__arr_date_time;
		$itinerary_details['departure_from'] = $bus_data['bus_from'][0];
		$itinerary_details['arrival_to'] = $bus_data['bus_to'][0];
		$itinerary_details['boarding_from'] = $bus_data['dep_loc_onward'][0];
		$itinerary_details['dropping_at'] = $bus_data['arr_loc_onward'][0];
		$itinerary_details['bus_type'] = $bus_data['carrier_type'][0];
		$itinerary_details['operator'] = $bus_data['career_onward'][0];
		$itinerary_details['attributes'] = '';

		$bus_iter_id = $this->custom_db->insert_record('bus_booking_itinerary_details',$itinerary_details);
		
		/**Bus Itinerary details END**/

		/**Bus Customer details START**/
		$pax_count = $bus_data['adult_count'];
		$arr = array();

		for($i = 0;$i < $pax_count;$i++){

			$customer_details['app_reference'] = $app_reference;
			$customer_details['title'] = $bus_data['pax_title'][$i];
			$customer_details['name'] = $bus_data['pax_first_name'][$i];
			$customer_details['age'] = $bus_data['age'][$i];
			$customer_details['gender'] = $bus_data['pax_title'][$i];
			$customer_details['seat_no'] = $bus_data['seat_no'][$i];

			$customer_details['status'] = $bus_data['status'];
			$customer_details['seat_type'] = '';
			$customer_details['is_ac_seat'] = '';
			//debug($bus_data); exit;
			$customer_details['fare'] = ($bus_data['pax_basic_fare_onward'][0] + $bus_data['pax_other_tax_onward'][0]);
			$admin_commission = ($bus_data['pax_basic_fare_onward'][0]/100)*$bus_data['admin_comm_perc'];
			$customer_details['admin_commission'] = $admin_commission;
			$admin_tds = ($admin_commission/100)*5;
			$agent_commission = ($admin_commission/100)*$bus_data['basic_comm'];
			$agent_tds = ($agent_commission/100)*5;
			$customer_details['agent_commission'] = $agent_commission;
			$customer_details['admin_tds'] = $admin_tds;
			$customer_details['agent_tds'] = $agent_tds;
			$customer_details['admin_markup'] = ($bus_data['admin_markup'] / $pax_count);
			$customer_details['agent_markup'] = '0.00';
			$customer_details['currency'] = 'INR';
			$customer_details['updated_by_id'] = $agent_id;
			$customer_details['updated_datetime'] = date('Y-m-d h:i:s');

			$admin_buying = $customer_details['fare']-$admin_commission+$admin_tds;
			$agent_buying = $customer_details['fare']+$customer_details['admin_markup']-$agent_commission+$agent_tds;
			$customer_buying = $customer_details['fare']+$customer_details['admin_markup'];
			$agent_earning = $agent_commission-$agent_tds;
			$attr = array (
				  'Fare' => $customer_details['fare'],
				  '_CustomerBuying' => $customer_buying,
				  '_AdminBuying' => $admin_buying,
				  '_AgentMarkup' => 0,
				  '_AgentBuying' => $agent_buying,
				  '_AdminCommission' => $admin_commission,
				  '_AdminTds' => $admin_tds,
				  '_Commission' => $agent_commission,
				  '_tdsCommission' => $agent_tds,
				  '_AgentEarning' => $agent_earning,
				  '_TotalPayable' => $agent_buying,
				  '_GST' => 0,
				  '_ServiceTax' => $bus_data['pax_other_tax_onward'][0],
				  'seq_no' => 0,
				  'decks' => 0,
				  'SeatType' => 0,
				  'IsAcSeat' => 0,
				  'base_fare' => $bus_data['pax_basic_fare_onward'][0]
				);
			$customer_details['attr'] = json_encode($attr); 
			$bus_customer_id = $this->custom_db->insert_record('bus_booking_customer_details',$customer_details);
		}
		//debug($bus_data); exit;
		// Update Agent Balance
		if(($bus_data['agent_id'] > 0) && ($bus_data['status'] == 'BOOKING_CONFIRMED')){

			$agent_buying_price = $bus_data['agent_buying_price'];
			$api_total_tax = $bus_data['api_total_tax'];
			$api_total_basic_fare = $bus_data['api_total_basic_fare'];
			$admin_markup = $bus_data['admin_markup'];
			$agent_markup = 0;
			//debug($agent_buying_price); exit;
			$agent_buying = '-'.$agent_buying_price;
			$agent_debit_amount = $agent_buying;
			$transaction_type = 'bus';
			$fare = (-1*$agent_buying)-$admin_markup; 
			//debug($agent_buying." == ".$admin_markup); exit;
			$domain_markup = $admin_markup;
			$level_one_markup = $agent_markup;
			$remarks = 'Offline Bus Transaction was Successfully done';
			$convinence = 0;
			$discount = 0;
			$currency = 'INR';
			$currency_conversion_rate = 1;
			$transaction_owner_id = $bus_data['agent_id'];
			$this->domain_management_model->save_transaction_details($transaction_type, $app_reference, $fare, $domain_markup, $level_one_markup, $remarks, $convinence, $discount, $currency,$currency_conversion_rate,$transaction_owner_id);
			$this->domain_management_model->update_agent_balance($agent_debit_amount,$bus_data['agent_id']);
		}
		

		$this->load->model('domain_management_model');
		$this->domain_management_model->create_track_log ( $app_reference, 'Offline Booking - Bus' );

		$return_response['app_reference'] = $app_reference;
		$return_response['status'] = $bus_data['status'];
		$return_response['booking_source'] = $bus_data['suplier_id'];
		return $return_response;
	}

	function offline_bus_report($condition=array(), $count=false, $offset=0, $limit=100000000000, $b_status = '')
	{
		$condition = $this->custom_db->get_custom_condition($condition);
		
		$booking_src_cond = "";
		if(empty($booking_source) ==false){
			$booking_source = 'BD.booking_source="'.$booking_source.'"';
			$booking_src_cond = ' AND '.$booking_source;
		}
		if(isset($condition) == true)
		{
			$offset = 0;
		}else{
			
			$offset = $offset;
		}


		if ($count) {

			$query = 'select count(distinct(BD.app_reference)) as total_records from bus_booking_details BD 
					join bus_booking_customer_details BBCD on BD.app_reference=BBCD.app_reference 
					join bus_booking_itinerary_details AS ID on BD.app_reference=ID.app_reference 
					left join user as U on BD.created_by_id = U.user_id where BD.booking_billing_type= "offline" 
					AND  domain_origin='.get_domain_auth_id().''.$condition;
			//debug($query); die;

			$data = $this->db->query($query)->row_array();
			return $data['total_records'];
		} else {
			$this->load->library('booking_data_formatter');
			$response['status'] = SUCCESS_STATUS;
			$response['data'] = array();
			$booking_itinerary_details	= array();
			$booking_customer_details	= array();
			$bd_query = 'select BD.* ,U.agency_name,U.first_name,U.last_name from bus_booking_details AS BD
					     left join user U on U.user_id = BD.created_by_id
						 WHERE  BD.booking_billing_type= "offline"  AND BD.domain_origin='.get_domain_auth_id().' '.$condition.'
						 order by BD.created_datetime desc, BD.origin desc limit '.$offset.', '.$limit;

			$booking_details = $this->db->query($bd_query)->result_array();
			$app_reference_ids = $this->booking_data_formatter->implode_app_reference_ids($booking_details);
			if(empty($app_reference_ids) == false) {
				$id_query = 'select * from bus_booking_itinerary_details AS ID 
							WHERE ID.app_reference IN ('.$app_reference_ids.')';
				$cd_query = 'select * from bus_booking_customer_details AS CD 
							WHERE CD.app_reference IN ('.$app_reference_ids.')';
				$booking_itinerary_details	= $this->db->query($id_query)->result_array();
				$booking_customer_details	= $this->db->query($cd_query)->result_array();
			}
			$response['data']['booking_details']			= $booking_details;
			$response['data']['booking_itinerary_details']	= $booking_itinerary_details;
			$response['data']['booking_customer_details']	= $booking_customer_details;
			return $response;
		}
	}

}
