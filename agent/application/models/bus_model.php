<?php
/**
 * Library which has generic functions to get data
 *
 * @package    Provab Application
 * @subpackage Bus Model
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V2
 */
Class Bus_Model extends CI_Model
{
	private $master_search_data;
	/*
	 *
	 * Get Bus Station List
	 *
	 */
	function get_bus_station_list($query)
	{
		$this->db->like('ets_city_name', $query);
		$this->db->limit(15);
		return $this->db->get('bus_stations_new1')->result_array();
	}
        function get_bus_station_data($id)
	{
		$query = 'Select * from bus_stations_new1 where origin ='.$id;
	
		return $this->db->query($query)->row();
	}

	function get_monthly_booking_summary($condition=array())
	{
		$condition = $this->custom_db->get_custom_condition($condition);
		$query = 'select count(distinct(BD.app_reference)) AS total_booking, sum(BBCD.fare+BBCD.admin_markup+BBCD.agent_markup) as monthly_payment,
		sum(BBCD.admin_commission) as monthly_earning, MONTH(BD.created_datetime) as month_number
		from bus_booking_details BD
		join bus_booking_customer_details BBCD on BD.app_reference=BBCD.app_reference
		where (YEAR(BD.created_datetime) BETWEEN '.date('Y').' AND '.date('Y', strtotime('+1 year')).')  and BD.domain_origin='.get_domain_auth_id().'  AND BD.created_by_id='.$this->entity_user_id.' '.$condition.' 
		GROUP BY YEAR(BD.created_datetime), MONTH(BD.created_datetime)';
		return $this->db->query($query)->result_array();
	}

	/**
	 * get all the booking source which are active for current domain
	 */
	function active_booking_source()
	{
		$query = 'select BS.source_id, BS.origin from meta_course_list AS MCL, booking_source AS BS, activity_source_map AS ASM WHERE
		MCL.origin=ASM.meta_course_list_fk and ASM.booking_source_fk=BS.origin and MCL.course_id='.$this->db->escape(META_BUS_COURSE).'
		and BS.booking_engine_status='.ACTIVE.' AND MCL.status='.ACTIVE.' AND ASM.status="active"';
		return $this->db->query($query)->result_array();
	}

	/**
	 * return booking list
	 */
	function booking($condition=array(), $count=false, $offset=0, $limit=100000000000)
	{
		$condition = $this->custom_db->get_custom_condition($condition);
		if ($count) {
			$query = 'select count(distinct(BD.app_reference)) as total_records from bus_booking_details BD
					join bus_booking_customer_details BBCD on BD.app_reference=BBCD.app_reference 
					join bus_booking_itinerary_details AS ID on BD.app_reference=ID.app_reference
					where domain_origin='.get_domain_auth_id().''.$condition.' AND BD.created_by_id ='.$GLOBALS['CI']->entity_user_id;
			$data = $this->db->query($query)->row_array();
			return $data['total_records'];
		} else {
			$this->load->library('booking_data_formatter');
			$response['status'] = SUCCESS_STATUS;
			$response['data'] = array();
			$booking_itinerary_details	= array();
			$booking_customer_details	= array();
			$bd_query = 'select * from bus_booking_details AS BD 
						WHERE BD.domain_origin='.get_domain_auth_id().''.$condition.' AND BD.created_by_id ='.$GLOBALS['CI']->entity_user_id.'
						order by BD.origin desc limit '.$offset.', '.$limit;
			$booking_details = $this->db->query($bd_query)->result_array();
			$app_reference_ids = $this->booking_data_formatter->implode_app_reference_ids($booking_details);
			if(empty($app_reference_ids) == false) {
				$id_query = 'select * from bus_booking_itinerary_details AS ID 
							WHERE ID.app_reference IN ('.$app_reference_ids.')';
				$cd_query = 'select * from bus_booking_customer_details AS CD 
							WHERE CD.app_reference IN ('.$app_reference_ids.')';
				//Transaction Details
				$tl_query = 'select * from  transaction_log AS TL
							WHERE TL.app_reference IN ('.$app_reference_ids.')';
				$booking_itinerary_details	= $this->db->query($id_query)->result_array();
				$booking_customer_details	= $this->db->query($cd_query)->result_array();
				$tl_details = $this->db->query($tl_query)->result_array();
			}
			$response['data']['booking_details']			= $booking_details;
			$response['data']['booking_itinerary_details']	= $booking_itinerary_details;
			$response['data']['booking_customer_details']	= $booking_customer_details;
			$response['data']['tl_details'] = $tl_details;
			return $response;
		}
	}
	function bookings_for_event_listing($condition=array(), $count=false, $offset=0, $limit=100000000000)
	{
		$condition = $this->custom_db->get_custom_condition($condition);
		$this->load->library('booking_data_formatter');
			$response['status'] = SUCCESS_STATUS;
			$response['data'] = array();
			$booking_itinerary_details	= array();
			$booking_customer_details	= array();
			$bd_query = 'select BD.* from bus_booking_details AS BD, bus_booking_itinerary_details AS ID 
						WHERE BD.domain_origin='.get_domain_auth_id().''.$condition.' AND BD.created_by_id ='.$GLOBALS['CI']->entity_user_id.'
						order by BD.origin desc limit '.$offset.', '.$limit;
			$booking_details = $this->db->query($bd_query)->result_array();
			$app_reference_ids = $this->booking_data_formatter->implode_app_reference_ids($booking_details);
			if(empty($app_reference_ids) == false) {
				$id_query = 'select * from bus_booking_itinerary_details AS ID 
							WHERE ID.app_reference IN ('.$app_reference_ids.')';
				$cd_query = 'select * from bus_booking_customer_details AS CD 
							WHERE CD.app_reference IN ('.$app_reference_ids.')';
				//Transaction Details
				$tl_query = 'select * from  transaction_log AS TL
							WHERE TL.app_reference IN ('.$app_reference_ids.')';
				$booking_itinerary_details	= $this->db->query($id_query)->result_array();
				$booking_customer_details	= $this->db->query($cd_query)->result_array();
				$tl_details = $this->db->query($tl_query)->result_array();
			}
			$response['data']['booking_details']			= $booking_details;
			$response['data']['booking_itinerary_details']	= $booking_itinerary_details;
			$response['data']['booking_customer_details']	= $booking_customer_details;
			$response['data']['tl_details'] = $tl_details;
			return $response;
	}
	/**
	 * return booking list
	 */
	function filter_booking_report($search_filter_condition = '', $count=false, $offset=0, $limit=100000000000)
	{
		if(empty($search_filter_condition) == false) {
			$search_filter_condition = ' and'.$search_filter_condition;
		}
		if ($count) {
			$query = 'select count(distinct(BD.app_reference)) as total_records from bus_booking_details BD
					join bus_booking_customer_details BBCD on BD.app_reference=BBCD.app_reference 
					join bus_booking_itinerary_details AS ID on BD.app_reference=ID.app_reference
				 	join payment_option_list as POL on POL.payment_category_code=BD.payment_mode 
					where domain_origin='.get_domain_auth_id().''.$search_filter_condition.' AND BD.created_by_id ='.$GLOBALS['CI']->entity_user_id;
			$data = $this->db->query($query)->row_array();
			return $data['total_records'];
		} else {
			$this->load->library('booking_data_formatter');
			$response['status'] = SUCCESS_STATUS;
			$response['data'] = array();
			$booking_itinerary_details	= array();
			$booking_customer_details	= array();
			$bd_query = 'select * from bus_booking_details AS BD 
						WHERE BD.domain_origin='.get_domain_auth_id().''.$search_filter_condition.' AND BD.created_by_id ='.$GLOBALS['CI']->entity_user_id.'
						order by BD.origin desc limit '.$offset.', '.$limit;
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
	 * get search data and validate it
	 */
	function get_safe_search_data($search_id)
	{
		$search_data = $this->get_search_data($search_id);
		$status = true;
		$clean_search = array();
		if ($search_data != false) {
			//validate
			$temp_search_data = json_decode($search_data['search_data'], true);
			if (strtotime($temp_search_data['bus_date_1']) > time() || date('Y-m-d') == date('Y-m-d', strtotime($temp_search_data['bus_date_1']))) {
				$clean_search['bus_date_1'] = $temp_search_data['bus_date_1'];
			} else {
				$status = false;
			}

			if (empty($temp_search_data['bus_date_2']) == true) {
				$clean_search['trip_type'] = 'One Way';
			} elseif (strtotime($temp_search_data['bus_date_2']) > time()) {
				$clean_search['trip_type'] = 'Round Way';
			} else {
				$status = false;
			}

			if (empty($temp_search_data['bus_station_from']) == true || empty($temp_search_data['bus_station_to']) == true) {
				$status = false;
			} else {
				$clean_search['bus_station_from'] = $temp_search_data['bus_station_from'];
				$clean_search['bus_station_to'] = $temp_search_data['bus_station_to'];
				$clean_search['from_station_id'] = @$temp_search_data['from_station_id'];
				$clean_search['to_station_id'] = @$temp_search_data['to_station_id'];
			}

			return array('status' => $status, 'data' => $clean_search);
		}
	}

	/**
	 * get search data without doing any validation
	 * @param $search_id
	 */
	function get_search_data($search_id)
	{
		if (empty($this->master_search_data)) {
			$search_data = $this->custom_db->single_table_records('search_history', '*', array('search_type' => META_BUS_COURSE, 'origin' => $search_id));
			if ($search_data['status'] == true) {
				$this->master_search_data = $search_data['data'][0];
			} else {
				return false;
			}
		}
		return $this->master_search_data;
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
	 * @param string $data - serialized data to be cached
	 */
	function set_auth_token($data)
	{
		$this->custom_db->insert_record('temp_cache', array('domain_list_fk' => get_domain_auth_id(), 'type' => 'travelyaari', 'data' => $data, 'created_datetime' => date('Y-m-d H:i:s')));
	}

	/**
	 *
	 * @param number $domain_origin
	 * @param number $status
	 * @param string $app_reference
	 * @param string $booking_source
	 * @param string $pnr
	 * @param string $ticket
	 * @param string $transaction
	 * @param number $total_fare
	 * @param number $domain_markup
	 * @param number $level_one_markup
	 * @param string $currency
	 * @param number $phone_number
	 * @param number $alternate_number
	 * @param number $created_by_id
	 */
	function save_booking_details($domain_origin, $status, $app_reference, $booking_source, $pnr, $ticket, $transaction, $phone_number, $alternate_number,$payment_mode, 
			$created_by_id, $email='', $transaction_currency, $currency_conversion_rate, $cancel_policy, $phone_code, $selected_pm, $travel_id = 0)
	{
		$data['domain_origin'] = $domain_origin;
		$data['status'] = $status;
		$data['app_reference'] = $app_reference;
		$data['booking_source'] = $booking_source;
		$data['travel_id'] = $travel_id;
		$data['pnr'] = $pnr;
		$data['ticket'] = $ticket;
		$data['transaction'] = $transaction;
		$data['phone_number'] = $phone_number;
		$data['alternate_number'] = $alternate_number;
		$data['payment_mode'] = $payment_mode;
		$data['created_by_id'] = $created_by_id;
		$data['created_datetime'] = date('Y-m-d H:i:s');
		$data['email'] = $email;
		
		$data['currency'] = $transaction_currency;
		$data['currency_conversion_rate'] = $currency_conversion_rate;
		$data['phone_code'] = $phone_code;
		$data['booking_billing_type'] = $selected_pm;
		$data['cancel_policy'] = $cancel_policy;
		//add default value for ADO
		$data['discount'] = 0;
		//$data['currency'] = $transaction_currency;
		//$data['currency'] = $transaction_currency;


		$status = $this->custom_db->insert_record('bus_booking_details', $data);
		return $status;
	}
	/**
	 * 
	 * @param string $app_reference
	 * @param string $name
	 * @param number $age
	 * @param string $gender
	 * @param string $seat_no
	 * @param number $fare
	 * @param string $status
	 * @param string $seat_type
	 * @param boolean $is_ac_seat
	 * @param number $admin_commission
	 * @param number $agent_commission
	 * @param number $admin_markup
	 * @param number $agent_markup
	 */
	function save_booking_customer_details($app_reference, $title, $name, $age, $gender, $seat_no, $fare, $status, $seat_type, $is_ac_seat,
	$admin_commission, $admin_markup, $agent_commission, $agent_markup, $currency, $attr=array(), $admin_tds, $agent_tds)
	{
		$data['app_reference'] = $app_reference;
		$data['name'] = $name;
		$data['title'] = $title;
		$data['age'] = $age;
		$data['gender'] = $gender;
		$data['seat_no'] = $seat_no;
		$data['fare'] = $fare;
		$data['status'] = $status;
		$data['seat_type'] = $seat_type;
		$data['is_ac_seat'] = $is_ac_seat;
		$data['admin_commission'] = $admin_commission;
		$data['admin_markup'] = $admin_markup;
		$data['agent_commission'] = $agent_commission;
		$data['agent_markup'] = $agent_markup;
		$data['currency'] = $currency;
		$data['attr'] = json_encode($attr);
		
		$data['admin_tds'] = $admin_tds;
		$data['agent_tds'] = $agent_tds;
		$data['updated_by_id'] = 0;
		$data['updated_datetime'] = date('Y-m-d h:i:s');
		$status = $this->custom_db->insert_record('bus_booking_customer_details', $data);
		return $status;
	}

	/**
	 *
	 * @param $bus_booking_origin
	 * @param $departure_datetime
	 * @param $arrival_datetime
	 * @param $departure_from
	 * @param $arrival_to
	 * @param $boarding_from
	 * @param $dropping_at
	 * @param $bus_type
	 * @param $operator
	 * @param $attributes
	 */
	function save_booking_itinerary_details($app_reference, $journey_datetime, $departure_datetime, $arrival_datetime, $departure_from, $arrival_to, $boarding_from, $dropping_at, $bus_type, $operator, $attributes)
	{
		$data['app_reference'] = $app_reference;
		$data['journey_datetime'] = $journey_datetime;
		$data['departure_datetime'] = $departure_datetime;
		$data['arrival_datetime'] = $arrival_datetime;
		$data['departure_from'] = $departure_from;
		$data['arrival_to'] = $arrival_to;
		$data['boarding_from'] = $boarding_from;
		$data['dropping_at'] = $dropping_at;
		$data['bus_type'] = $bus_type;
		$data['operator'] = $operator;
		$data['attributes'] = serialize($attributes);
		$status = $this->custom_db->insert_record('bus_booking_itinerary_details', $data);
		return $status;
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
		//debug($bd_query); exit;
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
		//debug($bd_query); exit;
		return $response;
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
	/**
	 *
	 */
	function get_static_response($token_id)
	{
		$static_response = $this->custom_db->single_table_records('test', '*', array('origin' => intval($token_id)));
		return json_decode($static_response['data'][0]['test'], true);
	}

	/**
	 * SAve search data for future use - Analytics
	 * @param array $params
	 */
	function save_search_data($search_data, $type)
	{
		$data['domain_origin'] = get_domain_auth_id();
		$data['search_type'] = $type;
		$data['created_by_id'] = intval(@$this->entity_user_id);
		$data['created_datetime'] = date('Y-m-d H:i:s');
		$data['from_station'] = $search_data['bus_station_from'];
		$data['to_station'] = $search_data['bus_station_to'];
		$data['from_station_id'] = $search_data['from_station_id'];
		$data['to_station_id'] = $search_data['to_station_id'];
		$data['trip_type'] = (empty($search_data['bus_date_2']) == true ? 'oneway' : 'round');
		$data['journey_date'] = date('Y-m-d', strtotime($search_data['bus_date_1']));
		// debug($data);exit;
		$this->custom_db->insert_record('search_bus_history', $data);
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
			$condition = 'AND (BD.status = '.$this->db->escape('BOOKING_CONFIRMED').' OR BD.status = '.$this->db->escape('BOOKING_CANCELLED').')';
		}

		if ($count) {

			$query = 'select count(distinct(BD.app_reference)) as total_records from bus_booking_details BD 
					join bus_booking_customer_details BBCD on BD.app_reference=BBCD.app_reference 
					join bus_booking_itinerary_details AS ID on BD.app_reference=ID.app_reference 
					join payment_option_list as POL on POL.payment_category_code=BD.payment_mode 
					left join user as U on BD.created_by_id = U.user_id where BD.created_by_id = '.$GLOBALS['CI']->entity_user_id.' AND  U.user_type='.B2B_USER.' 
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
						 WHERE BD.created_by_id = '.$GLOBALS['CI']->entity_user_id.' AND U.user_type='.B2B_USER.' AND BD.domain_origin='.get_domain_auth_id().' '.$condition.'
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

				//Transaction details
				$tl_query = 'select * from  transaction_log AS TL
							WHERE TL.app_reference IN ('.$app_reference_ids.')';
				$tl_details = $this->db->query($tl_query)->result_array();

			}
			$response['data']['booking_details']			= $booking_details;
			$response['data']['booking_itinerary_details']	= $booking_itinerary_details;
			$response['data']['booking_customer_details']	= $booking_customer_details;
			$response['data']['tl_details'] = $tl_details;
			return $response;
		}
	}
	/**
	 * Daily sales report
	 */
	public function daily_sales_report($from_date, $to_date)
	{
		$sql = "SELECT SUM(cd.fare+cd.admin_markup+bd.convinence_amount) AS total_fare, SUM(cd.agent_commission) AS agent_comm, SUM(cd.agent_tds) AS agent_tds, SUM(cd.agent_markup) AS agent_markup FROM bus_booking_customer_details cd, bus_booking_details bd WHERE bd.status = 'BOOKING_CONFIRMED' AND cd.app_reference = bd.app_reference AND bd.created_datetime >= ".$from_date." AND bd.created_datetime <= ".$to_date." AND bd.created_by_id = '".$GLOBALS["CI"]->entity_user_id."'";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	public function booking_count($from_date, $to_date, $status)
	{
		$sql = "SELECT count(distinct(bd.app_reference)) as total_records from bus_booking_details bd WHERE bd.created_datetime >= ".$from_date." AND bd.created_datetime <= ".$to_date." AND bd.status = ".$status." AND bd.created_by_id = '".$GLOBALS["CI"]->entity_user_id."'";
		$query = $this->db->query($sql);
		return $query->result_array()[0]["total_records"];
	}

	public function get_ets_display_buses()
	{
		$sql = 'SELECT * FROM tbl_ets_operator_id';
		$query = $this->db->query($sql);
		$result = $query->result_array();
		return $result;
	}

	public function get_bitla_direct_operators()
	{
		$query = "SELECT * FROM booking_source WHERE meta_course_list_id='".META_BUS_COURSE."' AND is_bitla_direct = 1";
		$result = $this->db->query($query)->result_array();
		return $result;
	}

	public function bus_booking_details_by_app_ref($app_reference)
	{
		$bd_query = 'select * from bus_booking_details AS BD WHERE BD.app_reference like '.$this->db->escape($app_reference);
		$booking_details = $this->db->query($bd_query)->result_array();
		return $booking_details;
	}
}
