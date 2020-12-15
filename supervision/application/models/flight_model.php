<?php
/**
 * Library which has generic functions to get data
 *
 * @package    Provab Application
 * @subpackage Flight Model
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V2
 */
Class Flight_Model extends CI_Model
{
	/**
	 *TEMPORARY FUNCTION NEEDS TO BE CLEANED UP IN PRODUCTION ENVIRONMENT
	 */
	function get_static_response($token_id)
	{
		$static_response = $this->custom_db->single_table_records('test', '*', array('origin' => intval($token_id)));
		return json_decode($static_response['data'][0]['test'], true);
	}
	/**
	 * Flight booking report
	 *
	 */
	function booking($condition=array(), $count=false, $offset=0, $limit=100000000000)
	{
		$condition = $this->custom_db->get_custom_condition($condition);
		//BT, CD, ID
		if ($count) {
			$query = 'select count(distinct(BD.app_reference)) AS total_records from flight_booking_details BD
					where domain_origin='.get_domain_auth_id().''.$condition;
			$data = $this->db->query($query)->row_array();
			return $data['total_records'];
		} else {
			$this->load->library('booking_data_formatter');
			$response['status'] = SUCCESS_STATUS;
			$response['data'] = array();
			$booking_itinerary_details	= array();
			$booking_customer_details	= array();
			$booking_transaction_details = array();
			$cancellation_details = array();
			$payment_details = array();
			//Booking Details
			$bd_query = 'select * from flight_booking_details AS BD
						WHERE BD.domain_origin='.get_domain_auth_id().' '.$condition.'
						order by BD.created_datetime desc, BD.origin desc limit '.$offset.', '.$limit;
			$booking_details	= $this->db->query($bd_query)->result_array();
			$app_reference_ids = $this->booking_data_formatter->implode_app_reference_ids($booking_details);
			if(empty($app_reference_ids) == false) {
				//Itinerary Details
				$id_query = 'select * from flight_booking_itinerary_details AS ID
							WHERE ID.app_reference IN ('.$app_reference_ids.')';
				//Transaction Details
				$td_query = 'select * from flight_booking_transaction_details AS TD
							WHERE TD.app_reference IN ('.$app_reference_ids.')';
				//Customer and Ticket Details
				$cd_query = 'select CD.*,FPTI.TicketId,FPTI.TicketNumber,FPTI.IssueDate,FPTI.Fare,FPTI.SegmentAdditionalInfo
							from flight_booking_passenger_details AS CD
							left join flight_passenger_ticket_info FPTI on CD.origin=FPTI.passenger_fk
							WHERE CD.flight_booking_transaction_details_fk IN 
							(select TD.origin from flight_booking_transaction_details AS TD 
							WHERE TD.app_reference IN ('.$app_reference_ids.'))';
				//Cancellation Details
				$cancellation_details_query = 'select FCD.*
						from flight_booking_passenger_details AS CD
						left join flight_cancellation_details AS FCD ON FCD.passenger_fk=CD.origin
						WHERE CD.flight_booking_transaction_details_fk IN 
						(select TD.origin from flight_booking_transaction_details AS TD 
						WHERE TD.app_reference IN ('.$app_reference_ids.'))';
				//$payment_details_query = '';
				$booking_itinerary_details	= $this->db->query($id_query)->result_array();
				$booking_customer_details	= $this->db->query($cd_query)->result_array();
				$booking_transaction_details = $this->db->query($td_query)->result_array();
				$cancellation_details = $this->db->query($cancellation_details_query)->result_array();
				//$payment_details = $this->db->query($payment_details_query)->result_array();
			}
				
			$response['data']['booking_details']			= $booking_details;
			$response['data']['booking_itinerary_details']	= $booking_itinerary_details;
			$response['data']['booking_transaction_details']	= $booking_transaction_details;
			$response['data']['booking_customer_details']	= $booking_customer_details;
			$response['data']['cancellation_details']	= $cancellation_details;
			//$response['data']['payment_details']	= $payment_details;
			return $response;
		}
	}
	/**
	 * Read Individual booking details - dont use it to generate table
	 * @param $app_reference
	 * @param $booking_source
	 * @param $booking_status
	 */
	function get_booking_details($app_reference, $booking_source='', $booking_status='')
	{
		$response['status'] = FAILURE_STATUS;
		$response['data'] = array();
		//Booking Details
		$bd_query = 'select * from flight_booking_details AS BD WHERE BD.app_reference like '.$this->db->escape($app_reference);
		if (empty($booking_source) == false) {
			$bd_query .= '	AND BD.booking_source = '.$this->db->escape($booking_source);
		}
		if (empty($booking_status) == false) {
			$bd_query .= ' AND BD.status = '.$this->db->escape($booking_status);
		}
		//Itinerary Details
		$id_query = 'select * from flight_booking_itinerary_details AS ID WHERE ID.app_reference='.$this->db->escape($app_reference).' order by origin ASC';
		//Transaction Details
		$td_query = 'select * from flight_booking_transaction_details AS CD WHERE CD.app_reference='.$this->db->escape($app_reference).' order by origin ASC';
		//Customer and Ticket Details
		$cd_query = 'select distinct CD.*,FPTI.api_passenger_origin,FPTI.TicketId,FPTI.TicketNumber,FPTI.IssueDate,FPTI.Fare,FPTI.SegmentAdditionalInfo
						from flight_booking_passenger_details AS CD
						left join flight_passenger_ticket_info FPTI on CD.origin=FPTI.passenger_fk
						WHERE CD.flight_booking_transaction_details_fk IN 
						(select TD.origin from flight_booking_transaction_details AS TD 
						WHERE TD.app_reference ='.$this->db->escape($app_reference).')';
		//Cancellation Details
		$cancellation_details_query = 'select FCD.*
						from flight_booking_passenger_details AS CD
						left join flight_cancellation_details AS FCD ON FCD.passenger_fk=CD.origin
						WHERE CD.flight_booking_transaction_details_fk IN 
						(select TD.origin from flight_booking_transaction_details AS TD 
						WHERE TD.app_reference ='.$this->db->escape($app_reference).')';
		
		//Baggage Details
		$baggage_query = 'select CD.flight_booking_transaction_details_fk,
						concat(CD.first_name," ", CD.last_name) as pax_name,FBG.*
						from flight_booking_passenger_details AS CD
						join flight_booking_baggage_details FBG on CD.origin=FBG.passenger_fk
						WHERE CD.flight_booking_transaction_details_fk IN 
						(select TD.origin from flight_booking_transaction_details AS TD 
						WHERE TD.app_reference ='.$this->db->escape($app_reference).')';
		//Meal Details
		$meal_query = 'select CD.flight_booking_transaction_details_fk,
						concat(CD.first_name," ", CD.last_name) as pax_name,FML.*
						from flight_booking_passenger_details AS CD
						join flight_booking_meal_details FML on CD.origin=FML.passenger_fk
						WHERE CD.flight_booking_transaction_details_fk IN 
						(select TD.origin from flight_booking_transaction_details AS TD 
						WHERE TD.app_reference ='.$this->db->escape($app_reference).')';
		//Seat Details
		$seat_query = 'select CD.flight_booking_transaction_details_fk,
						concat(CD.first_name," ", CD.last_name) as pax_name,FST.*
						from flight_booking_passenger_details AS CD
						join flight_booking_seat_details FST on CD.origin=FST.passenger_fk
						WHERE CD.flight_booking_transaction_details_fk IN 
						(select TD.origin from flight_booking_transaction_details AS TD 
						WHERE TD.app_reference ='.$this->db->escape($app_reference).')';

		$response['data']['booking_details']			= $this->db->query($bd_query)->result_array();
		$response['data']['booking_itinerary_details']	= $this->db->query($id_query)->result_array();
		$response['data']['booking_transaction_details']	= $this->db->query($td_query)->result_array();
		$response['data']['booking_customer_details']	= $this->db->query($cd_query)->result_array();
		$response['data']['cancellation_details']	= $this->db->query($cancellation_details_query)->result_array();
		$response['data']['baggage_details']	= $this->db->query($baggage_query)->result_array();
		$response['data']['meal_details']	= $this->db->query($meal_query)->result_array();
		$response['data']['seat_details']	= $this->db->query($seat_query)->result_array();
		
		if (valid_array($response['data']['booking_details']) == true and valid_array($response['data']['booking_itinerary_details']) == true and valid_array($response['data']['booking_customer_details']) == true) {
			$response['status'] = SUCCESS_STATUS;
		}
		return $response;
	}
	/**
	 * Sagar Wakchaure
	 * B2C Flight Report
	 * @param unknown $condition
	 * @param unknown $count
	 * @param unknown $offset
	 * @param unknown $limit
	 * $condition[] = array('U.user_typ', '=', B2C_USER, ' OR ', 'BD.created_by_i', '=', 0);
	 */
	function b2c_flight_report($condition=array(), $count=false, $offset=0, $limit=100000000000)
	{
		$condition = $this->custom_db->get_custom_condition($condition);
		//$b2c_condition_array = array('U.user_type', '=', B2C_USER, ' OR ', 'BD.created_by_id', '=', 0);
		
		//BT, CD, ID

		// if(isset($condition) == true)
		// {
		// 	$offset = 0;
		// }else{
			
		// 	$offset = $offset;
		// }


		if ($count) {
			
			//echo debug($condition);exit;
			$query = 'select count(distinct(BD.app_reference)) AS total_records from flight_booking_details BD
					left join user U on U.user_id = BD.created_by_id
					left join user_type UT on UT.origin = U.user_type
					join flight_booking_transaction_details as BT on BD.app_reference = BT.app_reference	
					where (U.user_type='.B2C_USER.' OR BD.created_by_id = 0) AND BD.domain_origin='.get_domain_auth_id().''.$condition;
			//echo debug($query);exit;
			
			$data = $this->db->query($query)->row_array();
			
			return $data['total_records'];

		} else {
			$this->load->library('booking_data_formatter');
			$response['status'] = SUCCESS_STATUS;
			$response['data'] = array();
			$booking_itinerary_details	= array();
			$booking_customer_details	= array();
			$booking_transaction_details = array();
			$cancellation_details = array();
			$payment_details = array();
			//Booking Details
			$bd_query = 'select BD.* ,U.user_name,U.first_name,U.last_name from flight_booking_details AS BD
					     left join user U on U.user_id = BD.created_by_id
					     left join user_type UT on UT.origin = U.user_type
					     join flight_booking_transaction_details as BT on BD.app_reference = BT.app_reference		
						 WHERE  (U.user_type='.B2C_USER.' OR BD.created_by_id = 0) AND BD.domain_origin='.get_domain_auth_id().' '.$condition.'
						 order by BD.created_datetime desc, BD.origin desc limit '.$offset.', '.$limit;		

						 
						 
			$booking_details	= $this->db->query($bd_query)->result_array();
			//echo debug($bd_query); 			exit;
			$app_reference_ids = $this->booking_data_formatter->implode_app_reference_ids($booking_details);
			if(empty($app_reference_ids) == false) {
				//Itinerary Details
				$id_query = 'select * from flight_booking_itinerary_details AS ID
							WHERE ID.app_reference IN ('.$app_reference_ids.')';
				//Transaction Details
				$td_query = 'select * from flight_booking_transaction_details AS TD
							WHERE TD.app_reference IN ('.$app_reference_ids.')';
				//Customer and Ticket Details
				$cd_query = 'select CD.*,FPTI.TicketId,FPTI.TicketNumber,FPTI.IssueDate,FPTI.Fare,FPTI.SegmentAdditionalInfo
							from flight_booking_passenger_details AS CD
							left join flight_passenger_ticket_info FPTI on CD.origin=FPTI.passenger_fk
							WHERE CD.flight_booking_transaction_details_fk IN
							(select TD.origin from flight_booking_transaction_details AS TD
							WHERE TD.app_reference IN ('.$app_reference_ids.'))';
				//Cancellation Details
				$cancellation_details_query = 'select FCD.*
						from flight_booking_passenger_details AS CD
						left join flight_cancellation_details AS FCD ON FCD.passenger_fk=CD.origin
						WHERE CD.flight_booking_transaction_details_fk IN
						(select TD.origin from flight_booking_transaction_details AS TD
						WHERE TD.app_reference IN ('.$app_reference_ids.'))';
				$payment_details_query = 'select * from  payment_gateway_details AS PD
							WHERE PD.app_reference IN ('.$app_reference_ids.')';
				$booking_itinerary_details	= $this->db->query($id_query)->result_array();
				$booking_customer_details	= $this->db->query($cd_query)->result_array();
				$booking_transaction_details = $this->db->query($td_query)->result_array();
				$cancellation_details = $this->db->query($cancellation_details_query)->result_array();
				$payment_details = $this->db->query($payment_details_query)->result_array();
			}
	
			$response['data']['booking_details']			= $booking_details;
			$response['data']['booking_itinerary_details']	= $booking_itinerary_details;
			$response['data']['booking_transaction_details']	= $booking_transaction_details;
			$response['data']['booking_customer_details']	= $booking_customer_details;
			$response['data']['cancellation_details']	= $cancellation_details;
			$response['data']['payment_details']	= $payment_details;
			return $response;
		}
	}	
	
	
	/**
	 * Sagar Wakchaure
	 * B2C Flight Report
	 * @param unknown $condition
	 * @param unknown $count
	 * @param unknown $offset
	 * @param unknown $limit
	 * $condition[] = array('U.user_typ', '=', B2C_USER, ' OR ', 'BD.created_by_i', '=', 0);
	 */
	function b2b_flight_report($condition=array(), $count=false, $offset=0, $limit=100000000000, $b_status = '')
	{
		$condition = $this->custom_db->get_custom_condition($condition);
		//$b2c_condition_array = array('U.user_type', '=', B2C_USER, ' OR ', 'BD.created_by_id', '=', 0);
	
		//BT, CD, ID

		// if(isset($condition) == true)
		// {
		// 	$offset = 0;
		// }else{
		// 	$offset = $offset;
		// }
		if($b_status == "confirmed_cancelled"){
			$condition = 'AND (BD.status = '.$this->db->escape('BOOKING_CONFIRMED').' OR BD.status = '.$this->db->escape('BOOKING_CANCELLED').')';
		}

		if ($count) {
				
			//echo debug($condition);exit;
			$query = 'select count(distinct(BD.app_reference)) AS total_records from flight_booking_details BD
					  join user U on U.user_id = BD.created_by_id
					  join flight_booking_transaction_details as BT on BD.app_reference = BT.app_reference						
					  where U.user_type='.B2B_USER.' AND BD.domain_origin='.get_domain_auth_id().''.$condition;
			
				
		
			$data = $this->db->query($query)->row_array();
			//echo debug($data);exit;
			return $data['total_records'];
		} else {
			$this->load->library('booking_data_formatter');
			$response['status'] = SUCCESS_STATUS;
			$response['data'] = array();
			$booking_itinerary_details	= array();
			$booking_customer_details	= array();
			$booking_transaction_details = array();
			$cancellation_details = array();
			$payment_details = array();
			//Booking Details
			$bd_query = 'select BD.*,U.agency_name, BS.name AS supp_name, U.uuid AS agency_id, U.first_name,U.last_name from flight_booking_details AS BD
					      join user U on U.user_id = BD.created_by_id join flight_booking_transaction_details as BT on BD.app_reference = BT.app_reference				
					      join booking_source as BS on BD.booking_source = BS.source_id		      
						  WHERE U.user_type='.B2B_USER.' AND BD.domain_origin='.get_domain_auth_id().' '.$condition.'
						  order by BD.created_datetime desc, BD.origin desc limit '.$offset.', '.$limit;
						  
			
			$booking_details	= $this->db->query($bd_query)->result_array();
			//debug($bd_query);exit;
			$app_reference_ids = $this->booking_data_formatter->implode_app_reference_ids($booking_details);
			if(empty($app_reference_ids) == false) {
				//Itinerary Details
				$id_query = 'select * from flight_booking_itinerary_details AS ID
							WHERE ID.app_reference IN ('.$app_reference_ids.')';
				//Transaction Details
				$td_query = 'select * from flight_booking_transaction_details AS TD
							WHERE TD.app_reference IN ('.$app_reference_ids.')';
				
				//Customer and Ticket Details
				$cd_query = 'select CD.*,FPTI.TicketId,FPTI.TicketNumber,FPTI.IssueDate,FPTI.Fare,FPTI.SegmentAdditionalInfo
							from flight_booking_passenger_details AS CD
							left join flight_passenger_ticket_info FPTI on CD.origin=FPTI.passenger_fk
							WHERE CD.flight_booking_transaction_details_fk IN
							(select TD.origin from flight_booking_transaction_details AS TD
							WHERE TD.app_reference IN ('.$app_reference_ids.'))';
				//Cancellation Details
				$cancellation_details_query = 'select FCD.*
						from flight_booking_passenger_details AS CD
						left join flight_cancellation_details AS FCD ON FCD.passenger_fk=CD.origin
						WHERE CD.flight_booking_transaction_details_fk IN
						(select TD.origin from flight_booking_transaction_details AS TD
						WHERE TD.app_reference IN ('.$app_reference_ids.'))';
				//$payment_details_query = '';
				$booking_itinerary_details	= $this->db->query($id_query)->result_array();
				$booking_customer_details	= $this->db->query($cd_query)->result_array();
				$booking_transaction_details = $this->db->query($td_query)->result_array();
				$cancellation_details = $this->db->query($cancellation_details_query)->result_array();
				//$payment_details = $this->db->query($payment_details_query)->result_array();
			}
	
			$response['data']['booking_details']			= $booking_details;
			$response['data']['booking_itinerary_details']	= $booking_itinerary_details;
			$response['data']['booking_transaction_details']	= $booking_transaction_details;
			$response['data']['booking_customer_details']	= $booking_customer_details;
			$response['data']['cancellation_details']	= $cancellation_details;
			//$response['data']['payment_details']	= $payment_details;
			return $response;
		}
	}
	

	/**
	 * return all booking events
	 */
	function booking_events()
	{
		//BT, CD, ID
		$query = 'select * from flight_booking_details where domain_origin='.get_domain_auth_id();
		return $this->db->query($query)->result_array();
	}

	function get_monthly_booking_summary()
	{
		$query = 'select count(distinct(BD.app_reference)) AS total_booking, sum(TD.total_fare+TD.admin_markup+BD.convinence_amount) as monthly_payment, sum(TD.admin_markup+BD.convinence_amount) as monthly_earning, 
		MONTH(BD.created_datetime) as month_number 
		from flight_booking_details AS BD
		join flight_booking_transaction_details as TD on BD.app_reference=TD.app_reference
		where (YEAR(BD.created_datetime) BETWEEN '.date('Y').' AND '.date('Y', strtotime('+1 year')).') AND BD.domain_origin='.get_domain_auth_id().'
		GROUP BY YEAR(BD.created_datetime), MONTH(BD.created_datetime)';
		return $this->db->query($query)->result_array();
	}

	function monthly_search_history($year_start, $year_end)
	{
		$query = 'select count(*) AS total_search, MONTH(created_datetime) as month_number from search_flight_history where
		(YEAR(created_datetime) BETWEEN '.$year_start.' AND '.$year_end.') AND domain_origin='.get_domain_auth_id().' 
		AND search_type="'.META_AIRLINE_COURSE.'"
		GROUP BY YEAR(created_datetime), MONTH(created_datetime)';
		return $this->db->query($query)->result_array();
	}

	function top_search($year_start, $year_end)
	{
		$query = 'select count(*) AS total_search, concat(from_code, "-",to_code) label from search_flight_history where
		(YEAR(created_datetime) BETWEEN '.$year_start.' AND '.$year_end.') AND domain_origin='.get_domain_auth_id().' 
		AND search_type="'.META_AIRLINE_COURSE.'"
		GROUP BY CONCAT(from_code, to_code) order by count(*) desc, created_datetime desc limit 0, 15';
		return $this->db->query($query)->result_array();
	}
	/*
	 * Balu A
	 * Update the Cancellation Details of the Passenger
	 */
	function update_pax_ticket_cancellation_details($ticket_cancellation_details, $pax_origin)
	{
		//1.Updating Passenger Status
		$booking_status = 'BOOKING_CANCELLED';
		$passenger_update_data = array();
		$passenger_update_data['status'] = $booking_status;
		$passenger_update_condition = array();
		$passenger_update_condition['origin'] = $pax_origin;
		$this->custom_db->update_record('flight_booking_passenger_details', $passenger_update_data, $passenger_update_condition);
		//2.Adding Cancellation Details
		$data = array();
		$cancellation_details = $ticket_cancellation_details['cancellation_details'];
		$data['RequestId'] = $cancellation_details['ChangeRequestId'];
		$data['ChangeRequestStatus'] = $cancellation_details['ChangeRequestStatus'];
		$data['statusDescription'] = $cancellation_details['StatusDescription'];
		$pax_details_exists = $this->custom_db->single_table_records('flight_cancellation_details', '*', array('passenger_fk' => $pax_origin));
		if($pax_details_exists['status'] == true) {
			//Update the Data
			$this->custom_db->update_record('flight_cancellation_details', $data, array('passenger_fk' => $pax_origin));
		} else {
			//Insert Data
			$data['passenger_fk'] = $pax_origin;
			$data['created_by_id'] = intval(@$this->entity_user_id);
			$data['created_datetime'] = date('Y-m-d H:i:s');
			$data['cancellation_requested_on'] = date('Y-m-d H:i:s');
			$this->custom_db->insert_record('flight_cancellation_details', $data);
		}
	}
	/**
	 * Update Flight Booking Transaction Status based on Passenger Ticket status
	 * @param unknown_type $transaction_origin
	 */
	public function update_flight_booking_transaction_cancel_status($transaction_origin)
	{
		$confirmed_passenger_exists = $this->custom_db->single_table_records('flight_booking_passenger_details', '*', array('flight_booking_transaction_details_fk' => $transaction_origin, 'status' => 'BOOKING_CONFIRMED'));
		if($confirmed_passenger_exists['status'] == false){
			//If all passenger cancelled the ticket for that particular transaction, then set the transaction status to  BOOKING_CANCELLED
			$transaction_update_data = array();
			$booking_status = 'BOOKING_CANCELLED';
			$transaction_update_data['status'] = $booking_status;
			$transaction_update_condition = array();
			$transaction_update_condition['origin'] = $transaction_origin;
			$this->custom_db->update_record('flight_booking_transaction_details', $transaction_update_data, $transaction_update_condition);
		}
	}
	/**
	 * Update Flight Booking Transaction Status based on Passenger Ticket status
	 * @param unknown_type $transaction_origin
	 */
	public function update_flight_booking_cancel_status($app_reference)
	{
		$confirmed_passenger_exists = $this->custom_db->single_table_records('flight_booking_passenger_details', '*', array('app_reference' => $app_reference, 'status' => 'BOOKING_CONFIRMED'));
		if($confirmed_passenger_exists['status'] == false){
			//If all passenger cancelled the ticket, then set the booking status to  BOOKING_CANCELLED
			$booking_update_data = array();
			$booking_status = 'BOOKING_CANCELLED';
			$booking_update_data['status'] = $booking_status;
			$booking_update_condition = array();
			$booking_update_condition['app_reference'] = $app_reference;
			$this->custom_db->update_record('flight_booking_details', $booking_update_data, $booking_update_condition);
		}
	}
	
	/**
	 * Check if destination are domestic
	 * @param string $from_loc Unique location code
	 * @param string $to_loc   Unique location code
	 */
	function is_domestic_flight($from_loc, $to_loc)
	{
		if(valid_array($from_loc) == true || valid_array($to_loc)) {//Multicity
			$airport_cities = array_merge($from_loc, $to_loc);
			$airport_cities = array_unique($airport_cities);
			$airport_city_codes = '';
			foreach($airport_cities as $k => $v){
				$airport_city_codes .= '"'.$v.'",';
			}
			$airport_city_codes = rtrim($airport_city_codes, ',');
			$query = 'SELECT count(*) total FROM flight_airport_list WHERE airport_code IN ('.$airport_city_codes.') AND country != "India"';
		} else {//Oneway/RoundWay
			$query = 'SELECT count(*) total FROM flight_airport_list WHERE airport_code IN ('.$this->db->escape($from_loc).','.$this->db->escape($to_loc).') AND country != "India"';
		}
		$data = $this->db->query($query)->row_array();
		if (intval($data['total']) > 0){
			return false;
		} else {
			return true;
		}
	
	}
	
	/**
	 * Sagar Wakchaure
	 * update the pnr details
	 * @param unknown $response
	 * @param unknown $app_reference
	 * @param unknown $booking_source
	 * @param unknown $booking_status
	 * @return string
	 */
	function update_pnr_details($response,$app_reference, $booking_source='',$booking_status=''){
		
		$return_response = FAILURE_STATUS;		
		$booking_details = $this->get_booking_details($app_reference, $booking_source, $booking_status);
		$table_data = $this->booking_data_formatter->format_flight_booking_data($booking_details, 'admin');	
		$booking_transaction_details = $table_data['data']['booking_details'][0]['booking_transaction_details'];
		$update_pnr_array = array();
		$update_itinerary_details = array();
		$update_ticket_info = array();
		
		//update flight_booking_transaction_details table and flight_passenger_ticket_info
		
		if ($booking_details['status'] == SUCCESS_STATUS && $response['status'] == SUCCESS_STATUS) {
			$i=0;
			foreach($booking_transaction_details as $key=>$transaction_detail_sub_data){				
				$update_pnr_array['pnr'] = $response['data']['BoookingTransaction'][$i]['PNR'];
				$update_pnr_array['book_id'] =$response['data']['BoookingTransaction'][$i]['BookingID'];
				$update_pnr_array['status'] =$response['data']['BoookingTransaction'][$i]['Status'];
				$sequence_no = $response['data']['BoookingTransaction'][$i]['SequenceNumber'];
				
				//update flight_booking_transaction_details
				$this->custom_db->update_record('flight_booking_transaction_details', $update_pnr_array, array('app_reference' =>$app_reference,'sequence_number'=>trim($sequence_no)));			  			  
			
				foreach($transaction_detail_sub_data['booking_customer_details'] as $k=>$booking_customer_data){
					$update_ticket_info['TicketId'] = $response['data']['BoookingTransaction'][$i]['BookingCustomer'][$k]['TicketId'];
					$update_ticket_info['TicketNumber'] = $response['data']['BoookingTransaction'][$i]['BookingCustomer'][$k]['TicketNumber'];			   	     
					
					//update flight_passenger_ticket_info
					$this->custom_db->update_record('flight_passenger_ticket_info', $update_ticket_info,array('passenger_fk' => $booking_customer_data['origin']));			    	
				}
				$i++;
				
				//update  status in flight_booking_passenger_details
				$this->custom_db->update_record('flight_booking_passenger_details',array('status'=>$update_pnr_array['status']) ,array('app_reference' => trim($app_reference)));
			}
			
			//update status in flight_booking_details
			if(isset($response['data']['MasterBookingStatus']) && !empty($response['data']['MasterBookingStatus'])){
				
				$this->custom_db->update_record('flight_booking_details', array('status'=>$response['data']['MasterBookingStatus']),array('app_reference' => $app_reference));			
			}
			
			//update flight_booking_itinerary_details table		
			foreach($booking_details['data']['booking_itinerary_details'] as $key=>$transaction_detail_sub_data){
					$update_itinerary_details['airline_pnr'] = $response['data']['BookingItineraryDetails'][$key]['AirlinePNR'];
					$from = $response['data']['BookingItineraryDetails'][$key]['FromAirlineCode'];
					$to = $response['data']['BookingItineraryDetails'][$key]['ToAirlineCode'];
					$departure_datetime = $response['data']['BookingItineraryDetails'][$key]['DepartureDatetime'];
					
					$this->custom_db->update_record('flight_booking_itinerary_details', $update_itinerary_details, 
					array('app_reference' =>$app_reference,'from_airport_code'=>trim($from),'to_airport_code'=>trim($to),'departure_datetime'=>trim($departure_datetime)));
			}
			
			$return_response = SUCCESS_STATUS;
		}
		return $return_response;
	}
	/**
	 Balu A
	 * Returns Passenger Ticket Details based on the following parameteres
	 * @param $app_reference
	 * @param $passenger_origin
	 * @param $passenger_booking_status
	 */
	function get_passenger_ticket_info($app_reference, $passenger_origin, $passenger_booking_status='')
	{
		$response['status'] = FAILURE_STATUS;
		$response['data'] = array();
		$bd_query = 'select BD.*,DL.domain_name,DL.origin as domain_id,CC.country as domain_base_currency from flight_booking_details AS BD,domain_list AS DL
						join currency_converter CC on CC.id=DL.currency_converter_fk 
						WHERE DL.origin = BD.domain_origin AND BD.app_reference like ' . $this->db->escape ( $app_reference );
		//Customer and Ticket Details
		$cd_query = 'select FBTD.book_id,FBTD.pnr,FBTD.sequence_number,CD.*,FPTI.TicketId,FPTI.TicketNumber,FPTI.IssueDate,FPTI.Fare,FPTI.SegmentAdditionalInfo
						from flight_booking_passenger_details AS CD
						join flight_booking_transaction_details FBTD on CD.flight_booking_transaction_details_fk=FBTD.origin
						left join flight_passenger_ticket_info FPTI on CD.origin=FPTI.passenger_fk
						WHERE CD.app_reference="'.$app_reference.'" and CD.origin='.intval($passenger_origin).' and CD.status="'.$passenger_booking_status.'"';
		//Cancellation Details
		$cancellation_details_query = 'select FCD.*
						from flight_booking_passenger_details AS CD
						left join flight_passenger_ticket_info FPTI on CD.origin=FPTI.passenger_fk
						left join flight_cancellation_details AS FCD ON FCD.passenger_fk=CD.origin
						WHERE CD.app_reference="'.$app_reference.'" and CD.origin='.intval($passenger_origin).' and CD.status="'.$passenger_booking_status.'"';
		$response['data']['booking_details']			= $this->db->query($bd_query)->result_array();
		$response['data']['booking_customer_details']	= $this->db->query($cd_query)->result_array();
		$response['data']['cancellation_details']	= $this->db->query($cancellation_details_query)->result_array();
		if (valid_array($response['data']['booking_details']) == true && valid_array($response['data']['booking_customer_details']) == true) {
			$response['status'] = SUCCESS_STATUS;
		}
		return $response;
	}

	function get_cancellation_details($app_reference)
	{
		$query = 'select FCD.* from flight_booking_passenger_details 
				AS FBPD	left join flight_cancellation_details AS FCD 
				ON FCD.passenger_fk=FBPD.origin	WHERE 
				FBPD.app_reference = "'.$app_reference.'"';

		$response = $this->db->query($query)->result_array();
		return $response;
	}

	/**
	 * Balu A
	 * Update Supplier Ticket Refund Details
	 * @param unknown_type $supplier_ticket_refund_details
	 */
	public function update_supplier_ticket_refund_details($passenger_origin, $supplier_ticket_refund_details)
	{
		$update_refund_details = array();
		$supplier_ticket_refund_details = $supplier_ticket_refund_details['RefundDetails'];
		$update_refund_details['ChangeRequestStatus'] = 			$supplier_ticket_refund_details['ChangeRequestStatus'];
		$update_refund_details['statusDescription'] = 				$supplier_ticket_refund_details['StatusDescription'];
		$update_refund_details['API_refund_status'] = 				$supplier_ticket_refund_details['RefundStatus'];
		$update_refund_details['API_RefundedAmount'] = 				floatval($supplier_ticket_refund_details['RefundedAmount']);
		$update_refund_details['API_CancellationCharge'] = 			floatval($supplier_ticket_refund_details['CancellationCharge']);
		$update_refund_details['API_ServiceTaxOnRefundAmount'] =	floatval($supplier_ticket_refund_details['ServiceTaxOnRefundAmount']);
		$update_refund_details['API_SwachhBharatCess'] = 			floatval($supplier_ticket_refund_details['SwachhBharatCess']);
		
		if($supplier_ticket_refund_details['RefundStatus'] == 'PROCESSED') {
			$update_refund_details['cancellation_processed_on'] = date('Y-m-d H:i:s');
		}
		$this->custom_db->update_record('flight_cancellation_details', $update_refund_details, array('passenger_fk' => intval($passenger_origin)));
	}
	function get_booked_user_details($app_reference)
	{
		$query = "select  BD.created_by_id,U.user_type from flight_booking_details as BD join user as U on U.user_id = BD.created_by_id where app_reference = '".$app_reference."'";
		return $this->db->query($query)->result_array();
	}
	/**
	 * Extraservices(Baggage,Meal and Seats) Price
	 * @param unknown_type $app_reference
	 */
	public function get_extra_services_total_price($app_reference)
	{
		$extra_service_total_price = 0;
		
		//get baggage price
		$baggage_total_price = $this->get_baggage_total_price($app_reference);
		
		//get meal price
		$meal_total_price = $this->get_meal_total_price($app_reference);
		
		//get seat price
		$seat_total_price = $this->get_seat_total_price($app_reference);
		
		//Addig all services price
		$extra_service_total_price = round(($baggage_total_price+$meal_total_price+$seat_total_price), 2);
		
		return $extra_service_total_price;
	}
	function get_extra_service_details($app_reference)
	{
		//Baggage Details
		$baggage_query = 'select CD.flight_booking_transaction_details_fk,
						concat(CD.first_name," ", CD.last_name) as pax_name,FBG.*
						from flight_booking_passenger_details AS CD
						join flight_booking_baggage_details FBG on CD.origin=FBG.passenger_fk
						WHERE CD.flight_booking_transaction_details_fk IN 
						(select TD.origin from flight_booking_transaction_details AS TD 
						WHERE TD.app_reference ='.$this->db->escape($app_reference).')';
		//Meal Details
		$meal_query = 'select CD.flight_booking_transaction_details_fk,
						concat(CD.first_name," ", CD.last_name) as pax_name,FML.*
						from flight_booking_passenger_details AS CD
						join flight_booking_meal_details FML on CD.origin=FML.passenger_fk
						WHERE CD.flight_booking_transaction_details_fk IN 
						(select TD.origin from flight_booking_transaction_details AS TD 
						WHERE TD.app_reference ='.$this->db->escape($app_reference).')';
		//Seat Details
		$seat_query = 'select CD.flight_booking_transaction_details_fk,
						concat(CD.first_name," ", CD.last_name) as pax_name,FST.*
						from flight_booking_passenger_details AS CD
						join flight_booking_seat_details FST on CD.origin=FST.passenger_fk
						WHERE CD.flight_booking_transaction_details_fk IN 
						(select TD.origin from flight_booking_transaction_details AS TD 
						WHERE TD.app_reference ='.$this->db->escape($app_reference).')';
						
		$response['baggage_details']	= $this->db->query($baggage_query)->result_array();
		$response['meal_details']	= $this->db->query($meal_query)->result_array();
		$response['seat_details']	= $this->db->query($seat_query)->result_array();
		return $response;
	}
	function get_extra_service_charges($es_dets)
	{
		$baggage_price = 0; $seat_price = 0; $meal_price = 0;
		$baggage_details = $es_dets["baggage_details"];
		if(!empty($es_dets["baggage_details"])){
			foreach($baggage_details AS $bg_det){
				$baggage_price += $bg_det["price"];
			}
		}
		$seat_details = $es_dets["seat_details"];
		if(!empty($es_dets["seat_details"])){
			foreach($seat_details AS $st_det){
				$seat_price += $st_det["price"];
			}
		}
		$meal_details = $es_dets["meal_details"];
		if(!empty($es_dets["meal_details"])){
			foreach($meal_details AS $ml_det){
				$meal_price += $ml_det["price"];
			}
		}
		$esc["meal"] = $meal_price;
		$esc["seat"] = $seat_price;
		$esc["baggage"] = $baggage_price;
		//debug($esc);
		return $esc;
	}
	/**
	 * 
	 * Returns Baggage Total Price
	 * @param unknown_type $app_reference
	 */
	public function get_baggage_total_price($app_reference)
	{
		$query = 'select sum(FBG.price) as baggage_total_price
			from flight_booking_passenger_details FP
			left join flight_booking_baggage_details FBG on FP.origin=FBG.passenger_fk
			where FP.app_reference="'.$app_reference.'" group by FP.app_reference';
		$data = $this->db->query($query)->row_array();
		return floatval(@$data['baggage_total_price']);
	}
	/**
	 * 
	 * Returns Meal Total Price
	 * @param unknown_type $app_reference
	 */
	public function get_meal_total_price($app_reference)
	{
		$query = 'select sum(FML.price) as meal_total_price
			from flight_booking_passenger_details FP
			left join flight_booking_meal_details FML on FP.origin=FML.passenger_fk
			where FP.app_reference="'.$app_reference.'" group by FP.app_reference';
		$data = $this->db->query($query)->row_array();
		
		return floatval(@$data['meal_total_price']);
	}
	/**
	 * 
	 * Returns Seat Total Price
	 * @param unknown_type $app_reference
	 */
	public function get_seat_total_price($app_reference)
	{
		$query = 'select sum(FST.price) as seat_total_price
			from flight_booking_passenger_details FP
			left join flight_booking_seat_details FST on FP.origin=FST.passenger_fk
			where FP.app_reference="'.$app_reference.'" group by FP.app_reference';
		$data = $this->db->query($query)->row_array();
		
		return floatval(@$data['seat_total_price']);
	}
	/**
	 * Extraservices(Baggage,Meal and Seats) Price
	 * @param unknown_type $app_reference
	 */
	public function add_extra_service_price_to_published_fare($app_reference)
	{
		$transaction_data = $this->db->query('select * from flight_booking_transaction_details where app_reference="'.$app_reference.'" order by origin asc')->result_array();
		if(valid_array($transaction_data) == true){
			foreach ($transaction_data as $tr_k => $tr_v){
				$transaction_origin = $tr_v['origin'];
				$extra_service_totla_price = $this->transaction_wise_extra_service_total_price($transaction_origin);
				
				$update_data = array();
				$update_condition = array();
				$update_data['total_fare'] = $tr_v['total_fare']+$extra_service_totla_price;
				$update_condition['origin'] = $transaction_origin;
				$this->custom_db->update_record('flight_booking_transaction_details', $update_data, $update_condition);
			}
		}
	}
	/**
	 * Transaction-wise extra service total price
	 * @param unknown_type $transaction_origin
	 */
	public function transaction_wise_extra_service_total_price($transaction_origin)
	{
		$extra_service_totla_price = 0;
		//Baggage
		$baggage_price = $this->db->query('select sum(FBG.price) as baggage_total_price
											from flight_booking_passenger_details FP
											left join flight_booking_baggage_details FBG on FP.origin=FBG.passenger_fk
											where FP.flight_booking_transaction_details_fk='.$transaction_origin.' group by FP.flight_booking_transaction_details_fk')->row_array();
				
		//Meal
		$meal_price = $this->db->query('select sum(FML.price) as meal_total_price
											from flight_booking_passenger_details FP
											left join flight_booking_meal_details FML on FP.origin=FML.passenger_fk
											where FP.flight_booking_transaction_details_fk='.$transaction_origin.' group by FP.flight_booking_transaction_details_fk')->row_array();
		//Seat
		$seat_price = $this->db->query('select sum(FST.price) as seat_total_price
											from flight_booking_passenger_details FP
											left join flight_booking_seat_details FST on FP.origin=FST.passenger_fk
											where FP.flight_booking_transaction_details_fk='.$transaction_origin.' group by FP.flight_booking_transaction_details_fk')->row_array();
		
		$extra_service_totla_price = floatval(@$baggage_price['baggage_total_price']+@$meal_price['meal_total_price']+@$seat_price['seat_total_price']);
		
		return $extra_service_totla_price;
	}
	function booking_cancel($condition=array(), $count=false, $offset=0, $limit=100000000000)
	{
		$condition = $this->custom_db->get_custom_condition($condition);
		//$b2c_condition_array = array('U.user_type', '=', B2C_USER, ' OR ', 'BD.created_by_id', '=', 0);
		
		//BT, CD, ID

		// if(isset($condition) == true)
		// {
		// 	$offset = 0;
		// }else{
			
		// 	$offset = $offset;
		// }


		if ($count) {
			
			//echo debug($condition);exit;
			$query = 'select count(distinct(BD.app_reference)) AS total_records from flight_booking_details BD
					left join user U on U.user_id = BD.created_by_id
					left join user_type UT on UT.origin = U.user_type
					join flight_booking_transaction_details as BT on BD.app_reference = BT.app_reference	
					where (U.user_type='.B2C_USER.' OR BD.created_by_id = 0) AND BD.domain_origin='.get_domain_auth_id().''.$condition;
			//echo debug($query);exit;
			
			$data = $this->db->query($query)->row_array();
			
			return $data['total_records'];

		} else {
			$this->load->library('booking_data_formatter');
			$response['status'] = SUCCESS_STATUS;
			$response['data'] = array();
			$booking_itinerary_details	= array();
			$booking_customer_details	= array();
			$booking_transaction_details = array();
			$cancellation_details = array();
			$payment_details = array();
			//Booking Details
			$bd_query = 'select BD.* ,U.user_name,U.first_name,U.last_name from flight_booking_details AS BD
					     left join user U on U.user_id = BD.created_by_id
					     left join user_type UT on UT.origin = U.user_type
					     join flight_booking_transaction_details as BT on BD.app_reference = BT.app_reference		
						 WHERE  (U.user_type='.B2B_USER.' OR U.user_type='.B2C_USER.' OR BD.created_by_id = 0) AND BD.domain_origin='.get_domain_auth_id().' '.$condition.'
						 order by BD.created_datetime ASC limit '.$offset.', '.$limit;		

						 
						 
			$booking_details	= $this->db->query($bd_query)->result_array();
			
			$app_reference_ids = $this->booking_data_formatter->implode_app_reference_ids($booking_details);
			if(empty($app_reference_ids) == false) {
				//Itinerary Details
				$id_query = 'select * from flight_booking_itinerary_details AS ID
							WHERE ID.app_reference IN ('.$app_reference_ids.')';
				//Transaction Details
				$td_query = 'select * from flight_booking_transaction_details AS TD
							WHERE TD.app_reference IN ('.$app_reference_ids.')';
				//Customer and Ticket Details
				$cd_query = 'select CD.*,FPTI.TicketId,FPTI.TicketNumber,FPTI.IssueDate,FPTI.Fare,FPTI.SegmentAdditionalInfo
							from flight_booking_passenger_details AS CD
							left join flight_passenger_ticket_info FPTI on CD.origin=FPTI.passenger_fk
							WHERE CD.flight_booking_transaction_details_fk IN
							(select TD.origin from flight_booking_transaction_details AS TD
							WHERE TD.app_reference IN ('.$app_reference_ids.'))';
				//Cancellation Details
				$cancellation_details_query = 'select FCD.*
						from flight_booking_passenger_details AS CD
						left join flight_cancellation_details AS FCD ON FCD.passenger_fk=CD.origin
						WHERE CD.flight_booking_transaction_details_fk IN
						(select TD.origin from flight_booking_transaction_details AS TD
						WHERE TD.app_reference IN ('.$app_reference_ids.'))';
				// echo $cancellation_details_query;exit;
				//$payment_details_query = '';
				$booking_itinerary_details	= $this->db->query($id_query)->result_array();
				$booking_customer_details	= $this->db->query($cd_query)->result_array();
				$booking_transaction_details = $this->db->query($td_query)->result_array();
				$cancellation_details = $this->db->query($cancellation_details_query)->result_array();
				//$payment_details = $this->db->query($payment_details_query)->result_array();
			}
	
			$response['data']['booking_details']			= $booking_details;
			$response['data']['booking_itinerary_details']	= $booking_itinerary_details;
			$response['data']['booking_transaction_details']	= $booking_transaction_details;
			$response['data']['booking_customer_details']	= $booking_customer_details;
			$response['data']['cancellation_details']	= $cancellation_details;
			//$response['data']['payment_details']	= $payment_details;
			return $response;
		}
	}
	// added for flight_cancellation_details
	function add_flight_cancellation_details($pax_origin)
	{
		//1.Adding Cancellation Details
		$data = array();
		$data['RequestId'] = 1;
		$data['ChangeRequestStatus'] = 1;
		$data['statusDescription'] = 'Unassigned';
		//Insert Data
		$data['passenger_fk'] = $pax_origin;
		$data['created_by_id'] = intval(@$this->entity_user_id);
		$data['created_datetime'] = date('Y-m-d H:i:s');
		$data['cancellation_requested_on'] = date('Y-m-d H:i:s');
		$data['API_RefundedAmount'] = '';
		$data['API_CancellationCharge'] = '';
		$data['API_ServiceTaxOnRefundAmount'] = '0.00';
		$data['API_SwachhBharatCess'] = '0.00';
		$data['cancellation_processed_on'] = date('Y-m-d H:i:s');
		$data['refund_amount'] = date('Y-m-d H:i:s');
		$data['refund_amount'] = '0.00';
        $data['cancellation_charge'] = '0.00';
        $data['service_tax_on_refund_amount'] = '0.00';
        $data['swachh_bharat_cess'] = '0.00';
        $data['refund_comments'] = '';
        $data['refund_date'] = date('Y-m-d h:i:s');
		$this->custom_db->insert_record('flight_cancellation_details', $data);
	}	
        function exception_log_details($details)
        {
            $response = $this->custom_db->single_table_records('provab_xml_logger', '*', array('app_reference' =>($details['app_reference'])));
            return $response['data'][0]['response'];		
        }
    /*
	 *
	 * Get Airport List
	 *
	 */

	function get_airport_list($search_chars)
	{
		$raw_search_chars = $this->db->escape($search_chars);
		if(empty($search_chars)==false){
			$r_search_chars = $this->db->escape($search_chars.'%');
			$search_chars = $this->db->escape('%'.$search_chars.'%');
		}else{
			$r_search_chars = $this->db->escape($search_chars);
			$search_chars = $this->db->escape($search_chars);
		}
		
		$query = 'Select * from flight_airport_list where airport_city like '.$search_chars.'
		OR airport_code like '.$search_chars.' OR country like '.$search_chars.'
		ORDER BY top_destination DESC,
		CASE
			WHEN	airport_code	LIKE	'.$raw_search_chars.'	THEN 1
			WHEN	airport_city	LIKE	'.$raw_search_chars.'	THEN 2
			WHEN	country			LIKE	'.$raw_search_chars.'	THEN 3

			WHEN	airport_code	LIKE	'.$r_search_chars.'	THEN 4
			WHEN	airport_city	LIKE	'.$r_search_chars.'	THEN 5
			WHEN	country			LIKE	'.$r_search_chars.'	THEN 6

			WHEN	airport_code	LIKE	'.$search_chars.'	THEN 7
			WHEN	airport_city	LIKE	'.$search_chars.'	THEN 8
			WHEN	country			LIKE	'.$search_chars.'	THEN 9
			ELSE 10 END
		LIMIT 0, 20';
		// echo $query;
		// exit;
		return $this->db->query($query);
	}

	//Cancellation Report
	function cancellation_report($page){
		$response = array();
		if($page == 'dashboard'){
			$response = $this->custom_db->single_table_records('flight_booking_details', 'COUNT(origin) as cancel_tickets', array('status' => 'BOOKING_CANCELLED'));
		}else{
			$response = $this->custom_db->single_table_records('flight_booking_details', '*', array('status' => 'BOOKING_CANCELLED'));
		}
		
		return $response;
	}

	function flight_supplier(){
		$response = $this->custom_db->single_table_records('booking_source', '*', array('meta_course_list_id' => META_AIRLINE_COURSE,'booking_engine_status' => 1));

		return $response;
	}

	function b2b_flight_cancel_report($condition=array(), $count=false, $offset=0, $limit=100000000000)
	{
		$condition = $this->custom_db->get_custom_condition($condition);
		//$b2c_condition_array = array('U.user_type', '=', B2C_USER, ' OR ', 'BD.created_by_id', '=', 0);
	
		//BT, CD, ID

		// if(isset($condition) == true)
		// {
		// 	$offset = 0;
		// }else{
		// 	$offset = $offset;
		// }

		if ($count) {
				
			//echo debug($condition);exit;
			$query = 'select count(distinct(BD.app_reference)) AS total_records from flight_booking_details BD
					  join user U on U.user_id = BD.created_by_id
					  join flight_booking_transaction_details as BT on BD.app_reference = BT.app_reference						
					  where U.user_type='.B2B_USER.' AND BD.status = "BOOKING_CANCELLED" AND BD.domain_origin='.get_domain_auth_id().''.$condition;
			
				
		
			$data = $this->db->query($query)->row_array();
			//echo debug($data);exit;
			return $data['total_records'];
		} else {
			$this->load->library('booking_data_formatter');
			$response['status'] = SUCCESS_STATUS;
			$response['data'] = array();
			$booking_itinerary_details	= array();
			$booking_customer_details	= array();
			$booking_transaction_details = array();
			$cancellation_details = array();
			$payment_details = array();
			//Booking Details
			$bd_query = 'select BD.*,U.agency_name,U.first_name,U.last_name from flight_booking_details AS BD
					      join user U on U.user_id = BD.created_by_id join flight_booking_transaction_details as BT on BD.app_reference = BT.app_reference					      
						  WHERE  U.user_type='.B2B_USER.' AND BD.status = "BOOKING_CANCELLED" AND BD.domain_origin='.get_domain_auth_id().' '.$condition.'
						  order by BD.created_datetime desc, BD.origin desc limit '.$offset.', '.$limit;
						  
			//echo debug($bd_query);			
			//exit;
			
			$booking_details	= $this->db->query($bd_query)->result_array();
			//echo debug($booking_details);exit;
			$app_reference_ids = $this->booking_data_formatter->implode_app_reference_ids($booking_details);
			if(empty($app_reference_ids) == false) {
				//Itinerary Details
				$id_query = 'select * from flight_booking_itinerary_details AS ID
							WHERE ID.app_reference IN ('.$app_reference_ids.')';
				//Transaction Details
				$td_query = 'select * from flight_booking_transaction_details AS TD
							WHERE TD.app_reference IN ('.$app_reference_ids.')';
				
				//Customer and Ticket Details
				$cd_query = 'select CD.*,FPTI.TicketId,FPTI.TicketNumber,FPTI.IssueDate,FPTI.Fare,FPTI.SegmentAdditionalInfo
							from flight_booking_passenger_details AS CD
							left join flight_passenger_ticket_info FPTI on CD.origin=FPTI.passenger_fk
							WHERE CD.flight_booking_transaction_details_fk IN
							(select TD.origin from flight_booking_transaction_details AS TD
							WHERE TD.app_reference IN ('.$app_reference_ids.'))';
				//Cancellation Details
				$cancellation_details_query = 'select FCD.*
						from flight_booking_passenger_details AS CD
						left join flight_cancellation_details AS FCD ON FCD.passenger_fk=CD.origin
						WHERE CD.flight_booking_transaction_details_fk IN
						(select TD.origin from flight_booking_transaction_details AS TD
						WHERE TD.app_reference IN ('.$app_reference_ids.'))';
				//$payment_details_query = '';
				$booking_itinerary_details	= $this->db->query($id_query)->result_array();
				$booking_customer_details	= $this->db->query($cd_query)->result_array();
				$booking_transaction_details = $this->db->query($td_query)->result_array();
				$cancellation_details = $this->db->query($cancellation_details_query)->result_array();
				//$payment_details = $this->db->query($payment_details_query)->result_array();
			}
	
			$response['data']['booking_details']			= $booking_details;
			$response['data']['booking_itinerary_details']	= $booking_itinerary_details;
			$response['data']['booking_transaction_details']	= $booking_transaction_details;
			$response['data']['booking_customer_details']	= $booking_customer_details;
			$response['data']['cancellation_details']	= $cancellation_details;
			//$response['data']['payment_details']	= $payment_details;
			return $response;
		}
	}

	function b2c_flight_cancel_report($condition=array(), $count=false, $offset=0, $limit=100000000000)
	{
		$condition = $this->custom_db->get_custom_condition($condition);
		//$b2c_condition_array = array('U.user_type', '=', B2C_USER, ' OR ', 'BD.created_by_id', '=', 0);
		
		//BT, CD, ID

		// if(isset($condition) == true)
		// {
		// 	$offset = 0;
		// }else{
			
		// 	$offset = $offset;
		// }


		if ($count) {
			
			//echo debug($condition);exit;
			$query = 'select count(distinct(BD.app_reference)) AS total_records from flight_booking_details BD
					left join user U on U.user_id = BD.created_by_id
					left join user_type UT on UT.origin = U.user_type
					join flight_booking_transaction_details as BT on BD.app_reference = BT.app_reference	
					where (U.user_type='.B2C_USER.' OR BD.created_by_id = 0) AND BD.status = "BOOKING_CANCELLED" AND BD.domain_origin='.get_domain_auth_id().''.$condition;
			//echo debug($query);exit;
			
			$data = $this->db->query($query)->row_array();
			
			return $data['total_records'];

		} else {
			$this->load->library('booking_data_formatter');
			$response['status'] = SUCCESS_STATUS;
			$response['data'] = array();
			$booking_itinerary_details	= array();
			$booking_customer_details	= array();
			$booking_transaction_details = array();
			$cancellation_details = array();
			$payment_details = array();
			//Booking Details
			$bd_query = 'select BD.* ,U.user_name,U.first_name,U.last_name from flight_booking_details AS BD
					     left join user U on U.user_id = BD.created_by_id
					     left join user_type UT on UT.origin = U.user_type
					     join flight_booking_transaction_details as BT on BD.app_reference = BT.app_reference		
						 WHERE  (U.user_type='.B2C_USER.' OR BD.created_by_id = 0) AND BD.status = "BOOKING_CANCELLED" AND BD.domain_origin='.get_domain_auth_id().' '.$condition.'
						 order by BD.created_datetime desc, BD.origin desc limit '.$offset.', '.$limit;		

						 
						 
			$booking_details	= $this->db->query($bd_query)->result_array();
			//echo debug($bd_query); 			exit;
			$app_reference_ids = $this->booking_data_formatter->implode_app_reference_ids($booking_details);
			if(empty($app_reference_ids) == false) {
				//Itinerary Details
				$id_query = 'select * from flight_booking_itinerary_details AS ID
							WHERE ID.app_reference IN ('.$app_reference_ids.')';
				//Transaction Details
				$td_query = 'select * from flight_booking_transaction_details AS TD
							WHERE TD.app_reference IN ('.$app_reference_ids.')';
				//Customer and Ticket Details
				$cd_query = 'select CD.*,FPTI.TicketId,FPTI.TicketNumber,FPTI.IssueDate,FPTI.Fare,FPTI.SegmentAdditionalInfo
							from flight_booking_passenger_details AS CD
							left join flight_passenger_ticket_info FPTI on CD.origin=FPTI.passenger_fk
							WHERE CD.flight_booking_transaction_details_fk IN
							(select TD.origin from flight_booking_transaction_details AS TD
							WHERE TD.app_reference IN ('.$app_reference_ids.'))';
				//Cancellation Details
				$cancellation_details_query = 'select FCD.*
						from flight_booking_passenger_details AS CD
						left join flight_cancellation_details AS FCD ON FCD.passenger_fk=CD.origin
						WHERE CD.flight_booking_transaction_details_fk IN
						(select TD.origin from flight_booking_transaction_details AS TD
						WHERE TD.app_reference IN ('.$app_reference_ids.'))';
				$payment_details_query = 'select * from  payment_gateway_details AS PD
							WHERE PD.app_reference IN ('.$app_reference_ids.')';
				$booking_itinerary_details	= $this->db->query($id_query)->result_array();
				$booking_customer_details	= $this->db->query($cd_query)->result_array();
				$booking_transaction_details = $this->db->query($td_query)->result_array();
				$cancellation_details = $this->db->query($cancellation_details_query)->result_array();
				$payment_details = $this->db->query($payment_details_query)->result_array();
			}
	
			$response['data']['booking_details']			= $booking_details;
			$response['data']['booking_itinerary_details']	= $booking_itinerary_details;
			$response['data']['booking_transaction_details']	= $booking_transaction_details;
			$response['data']['booking_customer_details']	= $booking_customer_details;
			$response['data']['cancellation_details']	= $cancellation_details;
			$response['data']['payment_details']	= $payment_details;
			return $response;
		}
	}
	function get_airport_code_by_full_name($city_plus_code)
	{
		$arr = explode("(", $city_plus_code);
		$airport_code = rtrim(trim($arr[1]), ")");
		return $airport_code;
	}
	//offline flight book
	function offline_flight_book($flight_data, $app_reference){
		$final_debit_val = $flight_data["agent_buying_price"];
		/* booking details */
		$status = empty($flight_data['status'])?'BOOKING_PENDING':$flight_data['status'];
		$agent_id = $GLOBALS['CI']->entity_user_id;

		if($flight_data['booking_type']=="international"){
            $ModuleType="flight_int";
		}else{
             $ModuleType="flight";
		}

		$cm = 0;
		$airline = $this->db_cache_api->get_airline_list($from = array('k' => 'code','v' => 'name'));
		$first_flight = 0;
		$last_flight = ($flight_data ['sect_num_onward'] - 1);
		$trp = 'onward';
		$trp1 = $flight_data['trip_type'] == 'circle'?'return':'onward';
		$trp_last = $flight_data['trip_type'] == 'circle'?($flight_data ['sect_num_return'] - 1):$last_flight;
		$booking_details['domain_origin'] = 1;
		$booking_details['status'] = $flight_data['status'];
		$booking_details['app_reference'] = $app_reference;
		$booking_details['booking_source'] = $flight_data['suplier_id'];
		$booking_details['trip_type'] = $flight_data['trip_type'];
		$booking_details['phone'] = $flight_data ['passenger_phone'];
		$booking_details['alternate_number'] = $flight_data ['passenger_phone'];
		$booking_details['email'] = $flight_data ['passenger_email'];
		$booking_details['journey_start'] = db_current_datetime(trim($flight_data['dep_date_onward'][$first_flight].' '.$flight_data['dep_time_onward'][$first_flight]));
		$booking_details['journey_end'] = db_current_datetime(trim($flight_data ['arr_date_'.$trp1][$trp_last].' '.$flight_data['arr_time_'.$trp1][$trp_last]));

		$dep_ac = $this->get_airport_code_by_full_name($flight_data ['dep_loc_onward'][$first_flight]);
		$booking_details['journey_from'] = strtoupper($dep_ac);

		$arr_ac = $this->get_airport_code_by_full_name($flight_data ['arr_loc_onward'][$last_flight]);
		$booking_details['journey_to'] = strtoupper($arr_ac);

		$booking_details['from_loc'] = strtoupper($dep_ac);
		$booking_details['to_loc'] = strtoupper($arr_ac);

		$booking_details['payment_mode'] = 'offline';
		$booking_details['convinence_value'] = 0.0;
		$booking_details['convinence_value_type'] = 'plus';
		$booking_details['convinence_per_pax'] = 0;
		$booking_details['convinence_amount'] = 0;
		$booking_details['discount'] = 0;
		$flight_attr = [];
		$flight_attr['country'] = 'India';
		$flight_attr['city'] = '';
		$flight_attr['zipcode'] = '';
		$flight_attr['address'] = $flight_data['passenger_address'];		
		$booking_details['attributes'] = json_encode($flight_attr);
		//$booking_details['attributes'] = '';
		$booking_details['booking_billing_type'] = 'offline';
		
		$booking_details['created_datetime'] = date ( 'Y-m-d H:i:s' );
		$booking_details['currency'] = admin_base_currency();
		
		$gst_details_array = array();
		$gst_details_array["gst_number"] = @$flight_data['gst_number'];
		$gst_details_array["gst_company_name"] = @$flight_data['gst_company_name'];
		$gst_details_array["gst_phone"] = @$flight_data['gst_phone'];
		$gst_details_array["gst_email"] = @$flight_data['gst_email'];
		$gst_details_array["gst_address"] = @$flight_data['gst_address'];
		$gst_details_array["gst_state"] = @$flight_data['gst_state'];
		
		$booking_details['gst_details'] = json_encode($gst_details_array);
		
		//==================== Shrikant ==========================
		$pass_count = 0; $adt_encode = []; $chd_encode = []; $inf_encode = []; $price_attr = [];
		if(isset($flight_data['adult_count']) && !empty($flight_data['adult_count'] > 0)){
			$adt_base_price = 0; $adt_tax = 0; $adt_yq = 0;
			for ($adt_cnt = 0; $adt_cnt < $flight_data['adult_count']; $adt_cnt++) { 
				$adt_base_price = $flight_data['pax_basic_fare_onward'][$pass_count];				
				$adt_tax = $flight_data['pax_other_tax_onward'][$pass_count];				
				$adt_yq = $flight_data['pax_yq_onward'][$pass_count];				
				$pass_count++;		
			}	
			$price_attr['ADT']['BaseFare'] = $adt_base_price;		
			$price_attr['ADT']['Tax'] = $adt_tax + $adt_yq;		
			$price_attr['ADT']['TotalPrice'] = $adt_base_price + $adt_tax + $adt_yq;		
			$price_attr['ADT']['PassengerCount'] = $flight_data['adult_count'];		
		}
		if(isset($flight_data['child_count']) && !empty($flight_data['child_count'] > 0)){
			$chd_base_price = 0; $chd_tax = 0; $chd_yq = 0;
			for ($chd_cnt = 0; $chd_cnt < $flight_data['adult_count']; $chd_cnt++) { 
				$chd_base_price = $flight_data['pax_basic_fare_onward'][$pass_count];				
				$chd_tax = $flight_data['pax_other_tax_onward'][$pass_count];				
				$chd_yq = $flight_data['pax_yq_onward'][$pass_count];				
				$pass_count++;		
			}	
			$price_attr['CHD']['BaseFare'] = $chd_base_price;		
			$price_attr['CHD']['Tax'] = $chd_tax + $chd_yq;		
			$price_attr['CHD']['TotalPrice'] = $chd_base_price + $chd_tax + $chd_yq;		
			$price_attr['CHD']['PassengerCount'] = $flight_data['child_count'];					
		}
		if(isset($flight_data['infant_count']) && !empty($flight_data['infant_count'] > 0)){
			$inf_base_price = 0; $inf_tax = 0; $inf_yq = 0;
			for ($inf_cnt = 0; $inf_cnt < $flight_data['adult_count']; $inf_cnt++) { 
				$inf_base_price = $flight_data['pax_basic_fare_onward'][$pass_count];				
				$inf_tax = $flight_data['pax_other_tax_onward'][$pass_count];				
				$inf_yq = $flight_data['pax_yq_onward'][$pass_count];				
				$pass_count++;		
			}	
			$price_attr['INF']['BaseFare'] = $inf_base_price;		
			$price_attr['INF']['Tax'] = $inf_tax + $inf_yq;		
			$price_attr['INF']['TotalPrice'] = $inf_base_price + $inf_tax + $inf_yq;		
			$price_attr['INF']['PassengerCount'] = $flight_data['infant_count'];	
		}
		$booking_details['price_attr'] = json_encode($price_attr);
		//====================================================

		//As Per client demand - agent wise offline booking
		if($flight_data['agent_id'] > 0){
			$booking_details['created_by_id'] = $flight_data['agent_id'];
			$booking_details['booked_by'] = $agent_id;
		}else{
			$booking_details['created_by_id'] = $agent_id;
			$booking_details['booked_by'] = $agent_id;
		}

		$book_id = $this->custom_db->insert_record ( 'flight_booking_details', $booking_details );
		$book_id = @$book_id['insert_id'];
		$pax_fare = array();
		$c=0;
		foreach($flight_data['pax_basic_fare_onward'] as $fk => $fv){
			$pax_fare['onward']['basic'] = @$pax_fare['onward']['basic'] + ($fv * $flight_data['pax_type_count_onward'][$fk]);
			$pax_fare['onward']['yq'] = @$pax_fare['onward']['yq'] + ($flight_data['pax_yq_onward'][$fk] * $flight_data['pax_type_count_onward'][$fk]);
			$pax_fare['onward']['others'] = @$pax_fare['onward']['others'] + ($flight_data['pax_other_tax_onward'][$fk] * $flight_data['pax_type_count_onward'][$fk]);
			if($flight_data['trip_type'] == 'circle' && isset($flight_data['pax_basic_fare_return'][$fk])){
				$pax_fare['return']['basic'] = @$pax_fare['return']['basic'] + ($flight_data['pax_basic_fare_return'][$fk] * $flight_data['pax_type_count_return'][$fk]);
				$pax_fare['return']['yq'] = @$pax_fare['return']['yq'] + ($flight_data['pax_yq_return'][$fk] * $flight_data['pax_type_count_return'][$fk]);
				$pax_fare['return']['others'] = @$pax_fare['return']['others'] + ($flight_data['pax_other_tax_return'][$fk] * $flight_data['pax_type_count_return'][$fk]);
			}
		}
		
		$c_on = strtoupper(@$flight_data['career_onward'][0]);
		$t[$c_on][0]['career'] = @$flight_data['career_onward'];
		$t[$c_on][0]['pax_count'] = array_sum($flight_data['pax_type_count_onward']);
		$f[$c_on]['basic'] = $pax_fare['onward']['basic'];
		$f[$c_on]['yq'] = $pax_fare['onward']['yq'];
		$f[$c_on]['others'] = $pax_fare['onward']['others'];
		if($flight_data['trip_type'] == 'circle' && valid_array(@$flight_data['career_return'])){
			$c_rt = strtoupper(@$flight_data['career_return'][0]);
			$t[$c_rt][1]['career'] = @$flight_data['career_return'];
			$t[$c_rt][1]['pax_count'] = array_sum($flight_data['pax_type_count_return']);
			$f[$c_rt]['basic'] =  intval(@$f[$c_rt]['basic']) + $pax_fare['return']['basic'];
			$f[$c_rt]['yq'] = intval(@$f[$c_rt]['yq']) + $pax_fare['return']['yq'];
			$f[$c_rt]['others'] = intval(@$f[$c_rt]['others']) + $pax_fare['return']['others'];
		}

		$api_total_display_fare = 0;
		$api_total_tax = 0;
		$api_total_fare = 0;
		$meal_and_baggage_fare = 0;
		$other_fare = 0;
		$basic_fare = 0;
		$fuel_charge = 0;
		$handling_charge = 0;
		$api_service_tax = 0;
		$dist_commission = 0;
		$api_dist_tds_on_commision = 0;
		$admin_commission = 0;
		$admin_tds_on_commission = 0;
		$agent_tds_on_commission = 0;
		$agent_comm = 0;
		$agent_markup = 0;
		$admin_markup = 0;
		$dist_markup = 0;
		$app_user_buying_price = 0;

		$price1 = array();
		$tot_agent_buying_price = 0;
		$for_count = 0;
		$val_divider = count($t);
		
		$from_ac_array = array();
		$to_ac_array = array();
		$pass_fk_array = array();
		
		foreach($t as $tk => $tv ){
			$trpc = $tk = strtoupper($tk);
			$hc = 0;
			$service_tax = ($f[$trpc]['basic'] *  $cm['tds_tax_details']['service_tax'])/100;
			$dist_comm = 0;
			$dist_tds_on_commission = 0;
			$base_fare = $f[$trpc]['basic'];

			$adm_comm_perc = $flight_data['admin_comm_perc'];
            $admin_comm += ($base_fare/100)*$adm_comm_perc;
            $admin_tds = ($admin_comm/100)*5;
            $agt_comm_perc = $flight_data['basic_comm'];
            $agent_comm += ($admin_comm/100)*$agt_comm_perc;
            $agent_tds = $agent_comm * 5 / 100;
			$gst_tmp = $flight_data['service_tax']/$val_divider;
			
			$total = $f[$trpc]['basic'] + $f[$trpc]['yq'] + $f[$trpc]['others'];
			$tot_markup = ( @$flight_data['agent_markup'] +  @$flight_data['admin_markup'] );
			$buying_price = $total + $hc + $service_tax + $tot_markup;
			$agent_buying_price = $buying_price - $agent_comm + $agent_tds - (@$flight_data['agent_markup']) + $gst_tmp;
			$tot_agent_buying_price += $agent_buying_price;

			$price['api_total_display_fare'] = $buying_price;;
			$price['total_breakup'] = array(
				    'api_total_tax'=> $f[$trpc]['others'] + $f[$trpc]['yq'],
					'api_total_fare'=> $f[$trpc]['basic'],
					'meal_and_baggage_fare'=>0
			);
			$price['Fare'] = array(
					'BaseFare'=> $f[$trpc]['basic'],
					'Tax'=> $f[$trpc]['yq']+$f[$trpc]['others'] + $hc + $service_tax,
					'fuel_charge'=> $f[$trpc]['yq'],
					'handling_charge'=>$hc,
					'service_tax' => $service_tax,
					'meal_and_baggage_fare'=>0,
					'AgentCommission' =>$agent_comm,
					'AgentTdsOnCommision'=>$agent_tds,
					'dist_commission' => $dist_comm,
					'dist_tds_on_commision' =>$dist_tds_on_commission,
					'admin_commission'=>$admin_comm,
					'admin_tds_on_commission'=>$admin_tds,
					'agent_markup'=>@$flight_data['agent_markup'],
					'admin_markup'=>@$flight_data['admin_markup'],
					'dist_markup'=>0,
					'PublishedFare'=>$agent_buying_price
			);
			$Fare_Details['Fare'] = $price['Fare'];
			//debug($Fare_Details['Fare']); exit;
			$api_total_display_fare += $buying_price;
			$api_total_tax += $f[$trpc]['others'] + $f[$trpc]['yq'];
			$api_total_fare += $f[$trpc]['basic'];
			$meal_and_baggage_fare += 0;
			$other_fare += $f[$trpc]['others'];
			$basic_fare += $f[$trpc]['basic'];
			$fuel_charge += $f[$trpc]['yq'];
			$handling_charge += $hc;
			$api_service_tax += $service_tax;
			$agent_commission += $agent_comm;
			$dist_commission += $dist_comm;
			$api_dist_tds_on_commision += $dist_tds_on_commission;
			$admin_commission += $admin_comm;
			$admin_tds_on_commission += $admin_tds;
			$agent_tds_on_commission += $agent_tds;
			$agent_markup += @$flight_data['agent_markup'];
			$admin_markup += @$flight_data['admin_markup'];
			$dist_markup += 0;
			$app_user_buying_price += $agent_buying_price;
			//================== Shrikant ================
			$flight_gds_pnr = NULL;
			if(isset($flight_data['gds_pnr_'.$trp][$first_flight]) && !empty($flight_data['gds_pnr_'.$trp][$first_flight])){
				$flight_gds_pnr = $flight_data['gds_pnr_'.$trp][$first_flight];
				$book_id = $flight_data['gds_pnr_'.$trp][$first_flight];
			}
			//============================================	
			$transaction_details['app_reference'] = $app_reference;
			$transaction_details['source'] = $flight_data['suplier_id'];
			$transaction_details['pnr'] = strtoupper(!$flight_data ['is_lcc']?$flight_data['gds_pnr_'.$trp][$first_flight]:$flight_data['airline_pnr_'.$trp][$first_flight]);
			$transaction_details['status'] = $status;
			$transaction_details['status_description'] = 'In Payment';
			$transaction_details['book_id'] = $book_id;
			$transaction_details['gds_pnr'] = $flight_gds_pnr; // Shrikant
			$transaction_details['ref_id'] = '';
			$transaction_details['admin_commission'] = $admin_comm;
			$transaction_details['agent_commission'] = $agent_comm;
			//$transaction_details['admin_markup'] = @$flight_data['admin_markup'] * $tv[$for_count]['pax_count'];
			$transaction_details['admin_markup'] = @$flight_data['admin_markup'];
			$transaction_details['total_fare'] = $buying_price-$transaction_details['admin_markup'];
			$transaction_details['agent_markup'] = @$flight_data['agent_markup'] * $tv[$for_count]['pax_count'];
			$transaction_details['currency'] = admin_base_currency();
			$transaction_details['attributes'] = json_encode($Fare_Details);
			$transaction_details['booking_currency'] = admin_base_currency();
			$transaction_details['booking_amount'] = $total;
			$transaction_details['payment_method'] = "Offline";
			$transaction_details['getbooking_StatusCode'] ="";
			$transaction_details['getbooking_Description'] = "";
			$transaction_details['getbooking_Category'] = "";
			$transaction_details['gst'] = $gst_tmp;
			$transaction_details['admin_tds'] = $admin_tds;
			$transaction_details['agent_tds'] = $agent_tds;
			//debug($transaction_details); exit;
			$flg = $this->custom_db->insert_record ( 'flight_booking_transaction_details', $transaction_details );
			
			$flight_booking_transaction_details_fk = @$flg['insert_id'];
			//debug($tv); exit;
			$segment_indicator=1;
			foreach($tv as $sk => $sv){
	
				foreach( $sv['career'] as $ik=>$iv){
					$from_ac = $this->get_airport_code_by_full_name($flight_data['dep_loc_'.$trp][$ik]);
					$to_ac = $this->get_airport_code_by_full_name($flight_data['arr_loc_'.$trp][$ik]);

					$from_airport_name = $this->db_cache_api->get_airport_city_name ( array (
							'airport_code' => $from_ac
					) );
					$to_airport_name = $this->db_cache_api->get_airport_city_name ( array (
							'airport_code' => $to_ac
					));
					$itenery_details['app_reference'] = $app_reference;
					$itenery_details['airline_pnr'] = strtoupper($flight_data['airline_pnr_'.$trp][$ik]);
					$itenery_details['segment_indicator'] = $segment_indicator;
					$itenery_details['airline_code'] = strtoupper($flight_data['career_'.$trp][$ik]);
					$itenery_details['airline_name'] = isset($airline[strtoupper($flight_data['career_'.$trp][$ik])])?$airline[strtoupper($flight_data['career_'.$trp][$ik])]:strtoupper($flight_data['career_'.$trp][$ik]);
					$itenery_details['flight_number'] = $flight_data['flight_num_'.$trp][$ik];
					$itenery_details['fare_class'] = strtoupper($flight_data['booking_class_'.$trp][$ik]);
					$itenery_details['from_airport_code'] = strtoupper($from_ac);
					$itenery_details['from_airport_name'] = $from_airport_name['airport_city'];
					$itenery_details['to_airport_code'] = strtoupper($to_ac);
					$itenery_details['to_airport_name'] = $to_airport_name['airport_city'];
					$itenery_details['departure_datetime'] = db_current_datetime(trim($flight_data['dep_date_'.$trp][$ik].' '.$flight_data['dep_time_'.$trp][$ik]));
					$itenery_details['arrival_datetime'] = db_current_datetime(trim($flight_data['arr_date_'.$trp][$ik].' '.$flight_data['arr_time_'.$trp][$ik]));
					$itenery_details['status'] = "";
					$itenery_details['operating_carrier'] = strtoupper($flight_data['career_'.$trp][$ik]);
					$itenery_details['cabin_baggage'] = strtoupper($flight_data['cab_bagg_'.$trp][$ik]);
					$itenery_details['checkin_baggage'] = strtoupper($flight_data['checkin_bagg_'.$trp][$ik]);
					//
					$itenery_details['FareRestriction'] ='';
					$itenery_details['FareBasisCode'] ='';
					$itenery_details['FareRuleDetail'] ='';
					$itenery_details['attributes'] ='';
					$segment_indicator++;
					$flg = $this->custom_db->insert_record ( 'flight_booking_itinerary_details', $itenery_details );
				}
				foreach($flight_data['pax_title'] as $pk => $pv){
						
					$customer_details['app_reference'] = $app_reference;
					$customer_details['passenger_type'] = $flight_data['pax_type'][$pk];
					$customer_details['is_lead'] = ($pk == 0)?1:0;
					$customer_details['title'] = get_enum_list('title',$pv);
					$customer_details['first_name'] = $flight_data['pax_first_name'][$pk];
					$customer_details['middle_name'] = '';
					$customer_details['last_name'] = $flight_data['pax_last_name'][$pk];
					$customer_details['date_of_birth'] = '0000-00-00';
					if($pv == 1 || $pv == 4){
						$customer_details['gender'] = 'Male';
					} else {
						$customer_details['gender'] = 'Female';
					}
					$customer_details['passenger_nationality'] = 'IN';
					$customer_details['passport_number'] = $flight_data['pax_passport_num'][$pk];
					$customer_details['passport_issuing_country'] = 'india';
					$customer_details['passport_expiry_date'] = date('Y-m-d',strtotime(db_current_datetime($flight_data['pax_pp_expiry'][$pk])));
				
					$customer_details['status'] = $status;
					$k = 0;
					if($flight_data['pax_type'][$pk] == 'Child') {
						$k==1;
					} else if($flight_data['pax_type'][$pk] == 'Infant'){
						$k=2;
					}
					$pax_basic = $flight_data['pax_basic_fare_'.$trp][$k];
					$pax_yq = $flight_data['pax_yq_'.$trp][$k];
					$pax_other = $flight_data['pax_other_tax_'.$trp][$k];
					$pax_total = $pax_basic + $pax_yq + $pax_other;
						
					$attr['price_breakup'] = array('base_price'=>$pax_basic,"yq"=>$pax_yq,'tax'=>$pax_other,'total_price'=>$pax_total);
					$customer_details['attributes'] = json_encode($attr);
					$customer_details['flight_booking_transaction_details_fk'] = $flight_booking_transaction_details_fk;
					
					$flg = $this->custom_db->insert_record ( 'flight_booking_passenger_details', $customer_details );
				}
	
				$trp = 'return';
			}
			$for_count ++;
		}	
		$price1['api_total_display_fare'] = $api_total_display_fare;
		$price1['total_breakup'] = array(
				'api_total_tax'=> $api_total_tax,
				'api_total_fare'=> $api_total_fare,
				'meal_and_baggage_fare'=>$meal_and_baggage_fare
		);
		$price1['price_breakup'] = array(
				'other_fare'=> $other_fare,
				'basic_fare'=> $basic_fare,
				'fuel_charge'=> $fuel_charge,
				'handling_charge'=>$handling_charge,
				'service_tax' => $api_service_tax,
				'meal_and_baggage_fare'=>$meal_and_baggage_fare,
				'agent_commission' =>$agent_commission,
				'agent_tds_on_commision'=>$api_agent_tds_on_commission,
				'dist_commission' => $dist_commission,
				'dist_tds_on_commision' =>$api_dist_tds_on_commision,
				'admin_commission'=>$admin_commission,
				'admin_tds_on_commission'=>$admin_tds_on_commission,
				'agent_markup'=>$agent_markup,
				'admin_markup'=>$admin_markup,
				'dist_markup' =>$dist_markup,
				'app_user_buying_price'=>$app_user_buying_price
		);
		$this->load->model ( 'domain_management_model' );
		$this->domain_management_model->create_track_log ( $app_reference, 'Offline Booking - Flight' );

		$return_response['app_reference'] = $app_reference;
		$return_response['status'] = $flight_data['status'];
		$return_response['booking_source'] = $flight_data['suplier_id'];
		$return_response['agent_buying'] = $final_debit_val;
		$return_response['admin_markup'] = $admin_markup;
		$return_response['agent_markup'] = $agent_markup;
		$return_response['agent_id'] = $flight_data['agent_id'];
		$return_response['status'] = $flight_data['status'];
		//exit("Done");
		return $return_response;
	
	}

	function offline_flight_report($condition=array(), $count=false, $offset=0, $limit=100000000000, $booking_source="")
	{
		//$booking_source = 'BD.booking_source="'.$booking_source.'"';
		$condition = $this->custom_db->get_custom_condition($condition);
		//$b2c_condition_array = array('U.user_type', '=', B2C_USER, ' OR ', 'BD.created_by_id', '=', 0);
		
		//BT, CD, ID
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
			
			//echo debug($condition);exit;
			$query = 'select count(distinct(BD.app_reference)) AS total_records from flight_booking_details BD
					left join user U on U.user_id = BD.created_by_id
					left join user_type UT on UT.origin = U.user_type
					join flight_booking_transaction_details as BT on BD.app_reference = BT.app_reference	
					where BD.booking_billing_type="offline" AND BD.domain_origin='.get_domain_auth_id().$booking_src_cond.' '.$condition;
			//echo debug($query);exit;
			
			$data = $this->db->query($query)->row_array();
			
			return $data['total_records'];

		} else {
			$this->load->library('booking_data_formatter');
			$response['status'] = SUCCESS_STATUS;
			$response['data'] = array();
			$booking_itinerary_details	= array();
			$booking_customer_details	= array();
			$booking_transaction_details = array();
			$cancellation_details = array();
			$payment_details = array();
			//Booking Details
			$bd_query = 'select BD.* ,U.user_name,U.first_name,U.last_name from flight_booking_details AS BD
					     left join user U on U.user_id = BD.created_by_id
					     left join user_type UT on UT.origin = U.user_type
					     join flight_booking_transaction_details as BT on BD.app_reference = BT.app_reference		
						 WHERE BD.booking_billing_type="offline" AND BD.domain_origin='.get_domain_auth_id().$booking_src_cond.' '.$condition.'
						 order by BD.created_datetime desc, BD.origin desc limit '.$offset.', '.$limit;		

						 
			$booking_details	= $this->db->query($bd_query)->result_array();
			//echo debug($bd_query); 			exit;
			$app_reference_ids = $this->booking_data_formatter->implode_app_reference_ids($booking_details);
			if(empty($app_reference_ids) == false) {
				//Itinerary Details
				$id_query = 'select * from flight_booking_itinerary_details AS ID
							WHERE ID.app_reference IN ('.$app_reference_ids.')';
				//Transaction Details
				$td_query = 'select * from flight_booking_transaction_details AS TD
							WHERE TD.app_reference IN ('.$app_reference_ids.')';
				//Credid Card Details
				$cc_query = 'select * from creditcard_information AS CC
							WHERE CC.app_reference IN ('.$app_reference_ids.')';
				//Billing Address Details
				$bill_query = 'select * from billing_information AS BI
							WHERE BI.app_reference IN ('.$app_reference_ids.')';
				//Customer and Ticket Details
				$cd_query = 'select CD.*,FPTI.TicketId,FPTI.TicketNumber,FPTI.IssueDate,FPTI.Fare,FPTI.SegmentAdditionalInfo
							from flight_booking_passenger_details AS CD
							left join flight_passenger_ticket_info FPTI on CD.origin=FPTI.passenger_fk
							WHERE CD.flight_booking_transaction_details_fk IN
							(select TD.origin from flight_booking_transaction_details AS TD
							WHERE TD.app_reference IN ('.$app_reference_ids.'))';
				//Cancellation Details
				$cancellation_details_query = 'select FCD.*
						from flight_booking_passenger_details AS CD
						left join flight_cancellation_details AS FCD ON FCD.passenger_fk=CD.origin
						WHERE CD.flight_booking_transaction_details_fk IN
						(select TD.origin from flight_booking_transaction_details AS TD
						WHERE TD.app_reference IN ('.$app_reference_ids.'))';
				//$payment_details_query = '';
				$booking_itinerary_details	= $this->db->query($id_query)->result_array();
				$booking_customer_details	= $this->db->query($cd_query)->result_array();
				$booking_transaction_details = $this->db->query($td_query)->result_array();
				$cancellation_details = $this->db->query($cancellation_details_query)->result_array();
				$booking_creditcard_details = $this->db->query($cc_query)->result_array();
				$booking_billing_details = $this->db->query($bill_query)->result_array();
				//$payment_details = $this->db->query($payment_details_query)->result_array();
			}
	
			$response['data']['booking_details']			= $booking_details;
			$response['data']['booking_itinerary_details']	= $booking_itinerary_details;
			$response['data']['booking_transaction_details']	= $booking_transaction_details;
			$response['data']['booking_customer_details']	= $booking_customer_details;
			$response['data']['cancellation_details']	= $cancellation_details;
			$response['data']['creditcard_details']	= $booking_creditcard_details;
			$response['data']['billing_details']	= $booking_billing_details;
			//$response['data']['payment_details']	= $payment_details;
			return $response;
		}
	}


	//// Bi Reports

	public function bi_get_graph_details($tp='',$s_date='',$e_date=''){


		$cond = 'YEARWEEK(fbd.created_datetime, 1) = YEARWEEK(CURDATE(), 1)';
		$cond1 = 'YEARWEEK(bbd.created_datetime, 1) = YEARWEEK(CURDATE(), 1)';
		$cond2 = 'YEARWEEK(hbd.created_datetime, 1) = YEARWEEK(CURDATE(), 1)';
		$cond3 = 'YEARWEEK(abd.created_datetime, 1) = YEARWEEK(CURDATE(), 1)';
		$cond4 = 'YEARWEEK(tbd.created_datetime, 1) = YEARWEEK(CURDATE(), 1)';
		$gp_by = 'date(fbd.created_datetime)';
		$gp_by1 = 'date(bbd.created_datetime)';
		$gp_by2 = 'date(hbd.created_datetime)';
		$gp_by3 = 'date(abd.created_datetime)';
		$gp_by4 = 'date(tbd.created_datetime)';
		$fn = 'DAYNAME';

		if($tp == 'month'){
			$cond = 'YEAR(fbd.created_datetime) = YEAR(CURDATE())';
			$cond1 = 'YEAR(bbd.created_datetime) = YEAR(CURDATE())';
			$cond2 = 'YEAR(hbd.created_datetime) = YEAR(CURDATE())';
			$cond3 = 'YEAR(abd.created_datetime) = YEAR(CURDATE())';
			$cond4 = 'YEAR(tbd.created_datetime) = YEAR(CURDATE())';
			$gp_by = 'Day_Name';
			$gp_by1 = 'Day_Name';
			$gp_by2 = 'Day_Name';
			$gp_by3 = 'Day_Name';
			$gp_by4 = 'Day_Name';
			$fn = 'MONTHNAME';
		}
		if(!empty($s_date)){
			$cond = ' date(fbd.created_datetime) >= "'.$s_date.'"';
			$cond1 = ' date(bbd.created_datetime) >= "'.$s_date.'"';
			$cond2 = ' date(hbd.created_datetime) >= "'.$s_date.'"';
			$cond3 = ' date(abd.created_datetime) >= "'.$s_date.'"';
			$cond4 = ' date(tbd.created_datetime) >= "'.$s_date.'"';
		}
		else if((!empty($s_date) && !empty($e_date))){
			$cond .= ' AND date(fbd.created_datetime) <= "'.$e_date.'"';
			$cond1 .= ' AND date(bbd.created_datetime) <= "'.$e_date.'"';
			$cond2 .= ' AND date(hbd.created_datetime) <= "'.$e_date.'"';
			$cond3 .= ' AND date(abd.created_datetime) <= "'.$e_date.'"';
			$cond4 .= ' AND date(tbd.created_datetime) <= "'.$e_date.'"';
		}
		

		$bi_flight_report_query = 'SELECT DATE(fbd.created_datetime) as Date, '.$fn.'(fbd.created_datetime) as Day_Name, COUNT(fbd.origin) as Count 
			FROM flight_booking_details as fbd join user U on U.user_id = fbd.created_by_id WHERE U.user_type='.B2B_USER.' AND '.$cond.' AND fbd.status ="BOOKING_CONFIRMED" GROUP BY '.$gp_by.'';


		$bi_bus_report_query = 'SELECT DATE(bbd.created_datetime) as Date, '.$fn.'(bbd.created_datetime) as Day_Name, COUNT(bbd.origin) as Count 
			FROM bus_booking_details as bbd join user U on U.user_id = bbd.created_by_id WHERE U.user_type='.B2B_USER.' AND  '.$cond1.' AND bbd.status ="BOOKING_CONFIRMED" GROUP BY '.$gp_by1.'';
			//debug($bi_bus_report_query);die();

		$bi_hotel_report_query = 'SELECT DATE(hbd.created_datetime) as Date, '.$fn.'(hbd.created_datetime) as Day_Name, COUNT(hbd.origin) as Count 
			FROM hotel_booking_details as hbd join user U on U.user_id = hbd.created_by_id WHERE U.user_type='.B2B_USER.' AND  '.$cond2.' AND hbd.status ="BOOKING_CONFIRMED" GROUP BY '.$gp_by2.'';

		$bi_activities_report_query = 'SELECT DATE(sbd.created_datetime) as Date, '.$fn.'(sbd.created_datetime) as Day_Name, COUNT(sbd.origin) as Count 
			FROM sightseeing_booking_details as sbd join user U on U.user_id = sbd.created_by_id WHERE U.user_type='.B2B_USER.' AND  '.$cond3.' AND sbd.status ="BOOKING_CONFIRMED" GROUP BY '.$gp_by3.'';

		$bi_transfer_report_query = 'SELECT DATE(tbd.created_datetime) as Date, '.$fn.'(tbd.created_datetime) as Day_Name, COUNT(tbd.origin) as Count 
			FROM transferv1_booking_details as tbd join user U on U.user_id = tbd.created_by_id WHERE U.user_type='.B2B_USER.' AND  '.$cond4.' AND tbd.status ="BOOKING_CONFIRMED" GROUP BY '.$gp_by4.'';
			//debug($bi_flight_report_query);die();

		$response['flight'] = $this->db->query($bi_flight_report_query)->result_array();
		$response['bus'] = $this->db->query($bi_bus_report_query)->result_array();
		$response['hotel'] = $this->db->query($bi_hotel_report_query)->result_array();
		$response['activities'] = $this->db->query($bi_activities_report_query)->result_array();
		$response['transfer'] = $this->db->query($bi_transfer_report_query)->result_array();

		 return $response;
	}

	public function bi_get_flight_details_monthly(){

		$prev_mnth_query = 'SELECT (select count(fbd.origin) FROM flight_booking_details as fbd join user U on U.user_id = fbd.created_by_id WHERE U.user_type='.B2B_USER.' AND YEAR(fbd.created_datetime) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH(fbd.created_datetime) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) AND fbd.status = "BOOKING_CONFIRMED") as prev_mnth_f_b,(select count(bbd.origin) FROM bus_booking_details as bbd join user U on U.user_id = bbd.created_by_id WHERE U.user_type='.B2B_USER.' AND YEAR(bbd.created_datetime) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH(bbd.created_datetime) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) AND bbd.status = "BOOKING_CONFIRMED") as prev_mnth_b_b,(select count(hbd.origin) FROM hotel_booking_details as hbd join user U on U.user_id = hbd.created_by_id WHERE U.user_type='.B2B_USER.' AND YEAR(hbd.created_datetime) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH(hbd.created_datetime) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) AND hbd.status = "BOOKING_CONFIRMED") as prev_mnth_h_b,(select count(sbd.origin) FROM sightseeing_booking_details as sbd join user U on U.user_id = sbd.created_by_id WHERE U.user_type='.B2B_USER.' AND YEAR(sbd.created_datetime) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH(sbd.created_datetime) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) AND sbd.status = "BOOKING_CONFIRMED") as prev_mnth_a_b,(select count(tbd.origin) FROM transferv1_booking_details as tbd join user U on U.user_id = tbd.created_by_id WHERE U.user_type='.B2B_USER.' AND YEAR(tbd.created_datetime) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH(tbd.created_datetime) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH) AND tbd.status = "BOOKING_CONFIRMED") as prev_mnth_t_b';

		$current_mnth_query = 'SELECT(select count(fbd.origin) FROM flight_booking_details as fbd join user U on U.user_id = fbd.created_by_id WHERE U.user_type='.B2B_USER.' AND YEAR(fbd.created_datetime) = YEAR(CURRENT_DATE ) AND MONTH(fbd.created_datetime) = MONTH(CURRENT_DATE) AND fbd.status = "BOOKING_CONFIRMED") as curnt_mnth_f_b,(select count(bbd.origin) FROM bus_booking_details as bbd join user U on U.user_id = bbd.created_by_id WHERE U.user_type='.B2B_USER.' AND YEAR(bbd.created_datetime) = YEAR(CURRENT_DATE) AND MONTH(bbd.created_datetime) = MONTH(CURRENT_DATE) AND bbd.status = "BOOKING_CONFIRMED") as curnt_mnth_b_b,(select count(hbd.origin) FROM hotel_booking_details as hbd join user U on U.user_id = hbd.created_by_id WHERE U.user_type='.B2B_USER.' AND YEAR(hbd.created_datetime) = YEAR(CURRENT_DATE) AND MONTH(hbd.created_datetime) = MONTH(CURRENT_DATE) AND hbd.status = "BOOKING_CONFIRMED") as curnt_mnth_h_b,(select count(sbd.origin) FROM sightseeing_booking_details as sbd join user U on U.user_id = sbd.created_by_id WHERE U.user_type='.B2B_USER.' AND YEAR(sbd.created_datetime) = YEAR(CURRENT_DATE) AND MONTH(sbd.created_datetime) = MONTH(CURRENT_DATE) AND sbd.status = "BOOKING_CONFIRMED") as curnt_mnth_a_b,(select count(tbd.origin) FROM transferv1_booking_details as tbd join user U on U.user_id = tbd.created_by_id WHERE U.user_type='.B2B_USER.' AND YEAR(tbd.created_datetime) = YEAR(CURRENT_DATE) AND MONTH(tbd.created_datetime) = MONTH(CURRENT_DATE) AND tbd.status = "BOOKING_CONFIRMED") as curnt_mnth_t_b';



		$response['previous_month'] = $this->db->query($prev_mnth_query)->result_array();
		$response['current_month'] = $this->db->query($current_mnth_query)->result_array();

		return $response;
	}

	public function bi_get_today_booking_details(){

		$f_query = 'SELECT (SELECT count(fbd.origin) FROM flight_booking_details as fbd  LEFT JOIN user U on U.user_id = fbd.created_by_id WHERE U.user_type = '.B2B_USER.' AND date(fbd.created_datetime) = CURDATE() AND fbd.status = "BOOKING_CONFIRMED") as count_cnf_book,(SELECT count(fbd.origin) FROM flight_booking_details as fbd LEFT JOIN user U on U.user_id = fbd.created_by_id WHERE U.user_type = '.B2B_USER.' AND date(fbd.created_datetime) = CURDATE() AND fbd.status = "BOOKING_CANCELLED") as count_cnl_book,(SELECT count(fbpd.origin) FROM flight_booking_details as fbd LEFT JOIN flight_booking_passenger_details as fbpd ON fbpd.app_reference=fbd.app_reference LEFT JOIN user U on U.user_id = fbd.created_by_id WHERE U.user_type = '.B2B_USER.' AND date(fbd.created_datetime) = CURDATE() AND fbd.status = "BOOKING_CONFIRMED") AS no_of_seats';

		$h_query = 'SELECT (SELECT count(hbd.origin) FROM hotel_booking_details as hbd LEFT JOIN user U on U.user_id = hbd.created_by_id WHERE U.user_type='.B2B_USER.' AND date(hbd.created_datetime) = CURDATE() AND hbd.status = "BOOKING_CONFIRMED") as count_cnf_book,(SELECT count(hbd.origin) FROM hotel_booking_details as hbd LEFT JOIN user U on U.user_id = hbd.created_by_id WHERE U.user_type='.B2B_USER.' AND date(hbd.created_datetime) = CURDATE() AND hbd.status = "BOOKING_CANCELLED") as count_cnl_book,
			(SELECT count(hbid.origin) FROM hotel_booking_details as hbd LEFT JOIN hotel_booking_itinerary_details as hbid ON hbid.app_reference=hbd.app_reference LEFT JOIN user U on U.user_id = hbd.created_by_id WHERE U.user_type='.B2B_USER.' AND date(hbd.created_datetime) = CURDATE() AND hbd.status = "BOOKING_CONFIRMED") AS no_of_rooms';

		$b_query ='SELECT (SELECT count(bbd.origin) FROM bus_booking_details as bbd LEFT JOIN user U on U.user_id = bbd.created_by_id WHERE U.user_type='.B2B_USER.' AND date(bbd.created_datetime) = CURDATE() AND bbd.status = "BOOKING_CONFIRMED") as count_cnf_book,(SELECT count(bbd.origin) FROM bus_booking_details as bbd LEFT JOIN user U on U.user_id = bbd.created_by_id WHERE U.user_type='.B2B_USER.' AND date(bbd.created_datetime) = CURDATE() AND bbd.status = "BOOKING_CANCELLED") as count_cnl_book,(SELECT count(bbcd.origin) FROM bus_booking_details as bbd LEFT JOIN bus_booking_customer_details as bbcd ON bbcd.app_reference=bbd.app_reference LEFT JOIN user U on U.user_id = bbd.created_by_id WHERE U.user_type='.B2B_USER.' AND date(bbd.created_datetime) = CURDATE() AND bbd.status = "BOOKING_CONFIRMED") AS no_of_seats';
		
		$t_query = 'SELECT (SELECT count(tbd.origin) FROM transferv1_booking_details as tbd LEFT JOIN user U on U.user_id = tbd.created_by_id WHERE U.user_type='.B2B_USER.' AND date(tbd.created_datetime) = CURDATE() AND tbd.status = "BOOKING_CONFIRMED") as count_cnf_book,(SELECT count(tbd.origin) FROM transferv1_booking_details as tbd LEFT JOIN user U on U.user_id = tbd.created_by_id WHERE U.user_type='.B2B_USER.' AND date(tbd.created_datetime) = CURDATE() AND tbd.status = "BOOKING_CANCELLED") as count_cnl_book,(SELECT count(tbid.origin) FROM transferv1_booking_details as tbd LEFT JOIN transferv1_booking_itinerary_details as tbid ON tbid.app_reference=tbd.app_reference LEFT JOIN user U on U.user_id = tbd.created_by_id WHERE U.user_type='.B2B_USER.' AND date(tbd.created_datetime) = CURDATE() AND tbd.status = "BOOKING_CONFIRMED") AS no_of_seats';	

		$a_query = 'SELECT (SELECT count(sbd.origin) FROM sightseeing_booking_details as sbd LEFT JOIN user U on U.user_id = sbd.created_by_id WHERE U.user_type='.B2B_USER.' AND date(sbd.created_datetime) = CURDATE() AND sbd.status = "BOOKING_CONFIRMED") as count_cnf_book,(SELECT count(sbd.origin) FROM sightseeing_booking_details as sbd LEFT JOIN user U on U.user_id = sbd.created_by_id WHERE U.user_type='.B2B_USER.' AND date(sbd.created_datetime) = CURDATE() AND sbd.status = "BOOKING_CANCELLED") as count_cnl_book,(SELECT count(sbid.origin) FROM sightseeing_booking_details as sbd LEFT JOIN sightseeing_booking_itinerary_details as sbid ON sbid.app_reference=sbd.app_reference LEFT JOIN user U on U.user_id = sbd.created_by_id WHERE U.user_type='.B2B_USER.' AND date(sbd.created_datetime) = CURDATE() AND sbd.status = "BOOKING_CONFIRMED") AS no_of_seats';


			$response['flight_daily_rp'] = $this->db->query($f_query)->result_array();
			$response['hotel_daily_rp'] = $this->db->query($h_query)->result_array();
			$response['bus_daily_rp'] = $this->db->query($b_query)->result_array();
			$response['transfer_daily_rp'] = $this->db->query($t_query)->result_array();
			$response['activities_daily_rp'] = $this->db->query($a_query)->result_array();

			return $response;
	}

		public function bi_get_active_mod(){

		$query = 'SELECT * from active_modules';

		//debug($query);die();
		$response = $this->db->query($query)->result_array();

		return $response;	
	}

	public function bi_get_agent_details(){

		$query = 'SELECT U.*,b.* FROM user U 
		JOIN b2b_user_details b ON U.user_id = b.user_oid
		WHERE user_type = '.B2B_USER.'';
		$response = $this->db->query($query)->result_array();
		return $response;
	}

	public function bi_transaction_details(){

		$f_query = 'SELECT fbd.origin,fbd.app_reference,fbd.created_datetime,fbtd.attributes, fbtd.total_fare, fbtd.admin_commission, fbtd.admin_tds, fbtd.agent_commission, fbtd.agent_tds, fbtd.admin_markup, fbtd.gst FROM flight_booking_details as fbd LEFT JOIN flight_booking_transaction_details as fbtd ON fbtd.app_reference = fbd.app_reference LEFT JOIN user U on U.user_id = fbd.created_by_id WHERE U.user_type = '.B2B_USER.' AND date(fbd.created_datetime) = CURDATE() AND fbd.status="BOOKING_CONFIRMED"';

		$b_query = 'SELECT bbd.origin,bbd.app_reference,bbd.created_datetime,bbcd.attr, bbcd.fare, bbcd.admin_commission, bbcd.admin_tds, bbcd.agent_commission, bbcd.agent_tds, bbcd.admin_markup FROM bus_booking_details as bbd LEFT JOIN bus_booking_customer_details as bbcd ON bbcd.app_reference = bbd.app_reference LEFT JOIN user U on U.user_id = bbd.created_by_id WHERE U.user_type = '.B2B_USER.' AND date(bbd.created_datetime) = CURDATE() AND bbd.status="BOOKING_CONFIRMED"';

		$h_query = 'SELECT hbd.origin,hbd.app_reference,hbd.created_datetime,hbid.total_fare,hbid.admin_markup FROM hotel_booking_details as hbd LEFT JOIN hotel_booking_itinerary_details as hbid ON hbid.app_reference = hbd.app_reference LEFT JOIN user U on U.user_id = hbd.created_by_id WHERE U.user_type = '.B2B_USER.' AND date(hbd.created_datetime) = CURDATE() AND hbd.status="BOOKING_CONFIRMED"';

		$t_query = 'SELECT tbd.origin,tbd.app_reference,tbd.created_datetime,tbid.api_raw_fare,tbid.agent_buying_price FROM transferv1_booking_details as tbd LEFT JOIN transferv1_booking_itinerary_details as tbid ON tbid.app_reference = tbd.app_reference LEFT JOIN user U on U.user_id = tbd.created_by_id WHERE U.user_type = '.B2B_USER.' AND date(tbd.created_datetime) = CURDATE() AND tbd.status="BOOKING_CONFIRMED"';

		$a_query = 'SELECT sbd.origin,sbd.app_reference,sbd.created_datetime,sbid.api_raw_fare,sbid.agent_buying_price FROM sightseeing_booking_details as sbd LEFT JOIN sightseeing_booking_itinerary_details as sbid ON sbid.app_reference = sbd.app_reference LEFT JOIN user U on U.user_id = sbd.created_by_id WHERE U.user_type = '.B2B_USER.' AND date(sbd.created_datetime) = CURDATE() AND sbd.status="BOOKING_CONFIRMED"';

		$response['flight'] = $this->db->query($f_query)->result_array();
		$response['bus'] = $this->db->query($b_query)->result_array();
		$response['hotel'] = $this->db->query($h_query)->result_array();
		$response['transfer'] = $this->db->query($t_query)->result_array();
		$response['activities'] = $this->db->query($a_query)->result_array();

		return $response;
	}

	public function bi_refund_details(){
		$f_query = 'SELECT origin,refund_amount FROM flight_cancellation_details WHERE date(refund_date) = CURDATE() AND refund_status=2';
		$h_query = 'SELECT origin,refund_amount FROM hotel_cancellation_details WHERE date(refund_date) = CURDATE() AND refund_status=2';
		$b_query = 'SELECT origin,refund_amount FROM bus_cancellation_details WHERE date(refund_date) = CURDATE() AND refund_status=2';
		$t_query = 'SELECT origin,refund_amount FROM transferv1_cancellation_details WHERE date(refund_date) = CURDATE() AND refund_status=2';
		$a_query = 'SELECT origin,refund_amount FROM sightseeing_cancellation_details WHERE date(refund_date) = CURDATE() AND refund_status=2';

		$response['flight'] = $this->db->query($f_query)->result_array();
		$response['bus'] = $this->db->query($b_query)->result_array();
		$response['hotel'] = $this->db->query($h_query)->result_array();
		$response['transfer'] = $this->db->query($t_query)->result_array();
		$response['activities'] = $this->db->query($a_query)->result_array();

		return $response;
	}

	public function bi_deposit_report(){
		$query = 'SELECT count(origin) as no_of_dep,transaction_type,SUM(amount) as amt FROM master_transaction_details WHERE date(date_of_transaction) = CURDATE() AND type = "B2B" AND status="accepted" GROUP BY transaction_type';

		$response = $this->db->query($query)->result_array();
		return $response;
	}

	public function notification_count(){
		$d_query = 'SELECT count(origin) as count FROM master_transaction_details WHERE status = "pending"';
		$response = $this->db->query($d_query)->result_array();
		$response['deposite_count'] = isset($response['deposite_count'][0]['count'])?$response['deposite_count'][0]['count']:0;
		return $response;
	}
		/**
	 * Sagar Wakchaure
	 * Booking Queue Flight Report
	 * @param unknown $condition
	 * @param unknown $count
	 * @param unknown $offset
	 * @param unknown $limit
	 * $condition[] = array('U.user_typ', '=', B2C_USER, ' OR ', 'BD.created_by_i', '=', 0);
	 */
	function flight_queue_report($condition=array(), $count=false, $offset=0, $limit=100000000000, $b_status = '')
	{
		$condition = $this->custom_db->get_custom_condition($condition);
		//$b2c_condition_array = array('U.user_type', '=', B2C_USER, ' OR ', 'BD.created_by_id', '=', 0);
	
		//BT, CD, ID

		// if(isset($condition) == true)
		// {
		// 	$offset = 0;
		// }else{
		// 	$offset = $offset;
		// }
		
			$condition .= ' AND BD.status = '.$this->db->escape('BOOKING_HOLD');
	

		if ($count) {
				
			//echo debug($condition);exit;
			$query = 'select count(distinct(BD.app_reference)) AS total_records from flight_booking_details BD
					  join flight_booking_transaction_details as BT on BD.app_reference = BT.app_reference						
					  where BD.domain_origin='.get_domain_auth_id().''.$condition;
			
			$data = $this->db->query($query)->row_array();
			
			return $data['total_records'];
		} else {
			$this->load->library('booking_data_formatter');
			$response['status'] = SUCCESS_STATUS;
			$response['data'] = array();
			$booking_itinerary_details	= array();
			$booking_customer_details	= array();
			$booking_transaction_details = array();
			$cancellation_details = array();
			$payment_details = array();
			//Booking Details
			$bd_query = 'select BD.*, BS.name AS supp_name from flight_booking_details AS BD
					      join flight_booking_transaction_details as BT on BD.app_reference = BT.app_reference				
					      join booking_source as BS on BD.booking_source = BS.source_id		      
						  WHERE BD.domain_origin='.get_domain_auth_id().$condition.'
						  order by BD.created_datetime desc, BD.origin desc limit '.$offset.', '.$limit;
						  
				// echo $bd_query;exit;
			$booking_details	= $this->db->query($bd_query)->result_array();
			//debug($bd_query);exit;
			$app_reference_ids = $this->booking_data_formatter->implode_app_reference_ids($booking_details);
			if(empty($app_reference_ids) == false) {
				//Itinerary Details
				$id_query = 'select * from flight_booking_itinerary_details AS ID
							WHERE ID.app_reference IN ('.$app_reference_ids.')';
				//Transaction Details
				$td_query = 'select * from flight_booking_transaction_details AS TD
							WHERE TD.app_reference IN ('.$app_reference_ids.')';
				
				//Customer and Ticket Details
				$cd_query = 'select CD.*,FPTI.TicketId,FPTI.TicketNumber,FPTI.IssueDate,FPTI.Fare,FPTI.SegmentAdditionalInfo
							from flight_booking_passenger_details AS CD
							left join flight_passenger_ticket_info FPTI on CD.origin=FPTI.passenger_fk
							WHERE CD.flight_booking_transaction_details_fk IN
							(select TD.origin from flight_booking_transaction_details AS TD
							WHERE TD.app_reference IN ('.$app_reference_ids.'))';
				//Cancellation Details
				$cancellation_details_query = 'select FCD.*
						from flight_booking_passenger_details AS CD
						left join flight_cancellation_details AS FCD ON FCD.passenger_fk=CD.origin
						WHERE CD.flight_booking_transaction_details_fk IN
						(select TD.origin from flight_booking_transaction_details AS TD
						WHERE TD.app_reference IN ('.$app_reference_ids.'))';
				//$payment_details_query = '';
				$booking_itinerary_details	= $this->db->query($id_query)->result_array();
				$booking_customer_details	= $this->db->query($cd_query)->result_array();
				$booking_transaction_details = $this->db->query($td_query)->result_array();
				$cancellation_details = $this->db->query($cancellation_details_query)->result_array();
				//$payment_details = $this->db->query($payment_details_query)->result_array();
			}
	
			$response['data']['booking_details']			= $booking_details;
			$response['data']['booking_itinerary_details']	= $booking_itinerary_details;
			$response['data']['booking_transaction_details']	= $booking_transaction_details;
			$response['data']['booking_customer_details']	= $booking_customer_details;
			$response['data']['cancellation_details']	= $cancellation_details;
			//$response['data']['payment_details']	= $payment_details;
			// debug($response);exit;
			return $response;
		}
	}

	//************************************PACE TRAVELS*****************************************************
	function flight_cancellation_queue($from_date,$to_date,$app_reference,$pnr)
	{
		//***************************Processing List***************************************
		if(isset($app_reference) && !empty($app_reference)){
			$query = 'SELECT b.*,p.origin as pass_origin,p.*,t.*,bt.*
						FROM flight_booking_details b
						JOIN flight_booking_passenger_details p
		  				ON b.app_reference = p.app_reference
		  				JOIN flight_booking_itinerary_details t
		  				ON b.app_reference = t.app_reference
		 				JOIN flight_booking_transaction_details bt
		  				ON b.app_reference = bt.app_reference
						WHERE b.app_reference = "'.$app_reference.'"
						GROUP BY b.origin DESC';
						$data = $this->db->query($query)->result_array();
		}else if(isset($pnr) && !empty($pnr)){
			$query = 'SELECT b.*,p.origin as pass_origin,p.*,t.*,bt.*
						FROM flight_booking_details b
						JOIN flight_booking_passenger_details p
		  				ON b.app_reference = p.app_reference
		  				JOIN flight_booking_itinerary_details t
		  				ON b.app_reference = t.app_reference
		 				JOIN flight_booking_transaction_details bt
		  				ON b.app_reference = bt.app_reference
						WHERE bt.book_id = "'.$pnr.'"
						GROUP BY b.origin DESC';
						$data = $this->db->query($query)->result_array();
		}else{
			$query = 'SELECT b.*,p.origin as pass_origin,p.*,t.*,bt.*
						FROM flight_booking_details b
						JOIN flight_booking_passenger_details p
		  				ON b.app_reference = p.app_reference
		  				JOIN flight_booking_itinerary_details t
		  				ON b.app_reference = t.app_reference
		 				JOIN flight_booking_transaction_details bt
		  				ON b.app_reference = bt.app_reference
						WHERE p.status != "BOOKING_CONFIRMED"
						GROUP BY b.origin DESC';
						$data = $this->db->query($query)->result_array();
		}
			//***************************Processing List***************************************
			$cancellation_details = array();
			foreach ($data as $key => $value) {
				//echo "<pre>value==>>"; print_r($value);die;
				$query_pass = 'SELECT pass.*
				FROM flight_booking_passenger_details pass
				WHERE pass.app_reference = "'.$value['app_reference'].'" AND pass.status = "BOOKING_INPROGRESS"';
			    $pass_data = $this->db->query($query_pass)->result_array();
			    foreach ($pass_data as $k => $v) {
			    	$query = 'SELECT c.*
					FROM flight_cancellation_details c
					WHERE c.passenger_fk = '.$v['origin'].' AND c.refund_status = "INPROGRESS"';
				    $data1 = $this->db->query($query)->result_array();
				    if(isset($data1) && !empty($data1)){
				    	$cancellation_details[$value['app_reference']]['details'] = $value; 
				        $cancellation_details[$value['app_reference']]['cancel_data'] = $data1[0];
			    	}	
			    }	    
			}
			//***************************Cancelled List***************************************
			if(isset($from_date) && !empty($from_date) && isset($to_date) && !empty($to_date)){
				$from_date = date('Y-m-d 00:00:00',strtotime($from_date));
				$to_date = date('Y-m-d 23:59:59',strtotime($to_date));
				$cancelled_details = array();
				foreach ($data as $key => $value) {
					$query = 'SELECT c.*
					FROM flight_cancellation_details c
					WHERE c.passenger_fk = '.$value['pass_origin'].' AND c.cancellation_processed_on >="'.$from_date.'" AND c.cancellation_processed_on <="'.$to_date.'" AND c.refund_status = "PROCESSED"';
				    $data2 = $this->db->query($query)->result_array();
				    if(isset($data2) && !empty($data2)){
				    	$cancelled_details[$value['app_reference']]['details'] = $value; 
				        $cancelled_details[$value['app_reference']]['cancel_data'] = $data2[0];
				    }		    
				}
			}else{
				$from_date = date('Y-m-d 00:00:00');
				$to_date = date('Y-m-d 23:59:59');
				$cancelled_details = array();
				foreach ($data as $key => $value) {
					$query = 'SELECT c.*
					FROM flight_cancellation_details c
					WHERE c.passenger_fk = '.$value['pass_origin'].' AND c.cancellation_processed_on >="'.$from_date.'" AND c.cancellation_processed_on <="'.$to_date.'" AND c.refund_status = "PROCESSED"';
				    $data2 = $this->db->query($query)->result_array();
				    if(isset($data2) && !empty($data2)){
				    	$cancelled_details[$value['app_reference']]['details'] = $value; 
				        $cancelled_details[$value['app_reference']]['cancel_data'] = $data2[0];
				    }		    
				}
			}
			//***************************Rejected List***************************************
			if(isset($from_date) && !empty($from_date) && isset($from_date) && !empty($from_date)){
				$from_date = date('Y-m-d 00:00:00',strtotime($from_date));
				$to_date = date('Y-m-d 23:59:59',strtotime($to_date));
				$rejected_details = array();
				foreach ($data as $key => $value) {
					$query = 'SELECT c.*
					FROM flight_cancellation_details c
					WHERE c.passenger_fk = '.$value['pass_origin'].' AND c.cancellation_processed_on >="'.$from_date.'" AND c.cancellation_processed_on <="'.$to_date.'" AND c.refund_status = "REJECTED"';
				    $data3 = $this->db->query($query)->result_array();
				    if(isset($data3) && !empty($data3)){
				    	$rejected_details[$value['app_reference']]['details'] = $value; 
				        $rejected_details[$value['app_reference']]['cancel_data'] = $data3[0];
				    }		    
				}
			}else{
				$from_date = date('Y-m-d 00:00:00');
				$to_date = date('Y-m-d 23:59:59');
				$rejected_details = array();
				foreach ($data as $key => $value) {
					$query = 'SELECT c.*
					FROM flight_cancellation_details c
					WHERE c.passenger_fk = '.$value['pass_origin'].' AND c.cancellation_processed_on >="'.$from_date.'" AND c.cancellation_processed_on <="'.$to_date.'" AND c.refund_status = "REJECTED"';
				    $data3 = $this->db->query($query)->result_array();
				    if(isset($data3) && !empty($data3)){
				    	$rejected_details[$value['app_reference']]['details'] = $value; 
				        $rejected_details[$value['app_reference']]['cancel_data'] = $data3[0];
				    }		    
				}
			}

			
		    $details['processing_list'] = $cancellation_details;
		    $details['cancelled_details'] = $cancelled_details;
		    $details['rejected_details'] = $rejected_details;
			return $details;
	}

	function get_passenger_ticket_info_by_app_reference($app_reference)
	{
		$response['status'] = FAILURE_STATUS;
		$response['data'] = array();
		$bd_query = 'select BD.*,DL.domain_name,DL.origin as domain_id,CC.country as domain_base_currency from flight_booking_details AS BD,domain_list AS DL
						join currency_converter CC on CC.id=DL.currency_converter_fk 
						WHERE DL.origin = BD.domain_origin AND BD.app_reference like '.$this->db->escape ( $app_reference );
		//Customer and Ticket Details
		$cd_query = 'select FBTD.book_id,FBTD.pnr,FBTD.sequence_number,CD.*,FPTI.TicketId,FPTI.TicketNumber,FPTI.IssueDate,FPTI.Fare,FPTI.SegmentAdditionalInfo
						from flight_booking_passenger_details AS CD
						join flight_booking_transaction_details FBTD on CD.flight_booking_transaction_details_fk=FBTD.origin
						left join flight_passenger_ticket_info FPTI on CD.origin=FPTI.passenger_fk
						WHERE CD.app_reference="'.$app_reference.'"';
		$response['data']['booking_details']			= $this->db->query($bd_query)->result_array();
		$response['data']['booking_customer_details']	= $this->db->query($cd_query)->result_array();
		foreach ($response['data']['booking_customer_details'] as $key => $value) {
			$cancellation_details_query = 'select FCD.*
						from flight_cancellation_details AS FCD
						WHERE FCD.passenger_fk="'.$value['origin'].'"';
			$cancel_data[] = $this->db->query($cancellation_details_query)->result_array();
		}
		//Cancellation Details
		$response['data']['cancellation_details']	= $cancel_data;
		if (valid_array($response['data']['booking_details']) == true && valid_array($response['data']['booking_customer_details']) == true) {
			$response['status'] = SUCCESS_STATUS;
		}
		return $response;
	}
	public function flight_cancel_notification_count(){
		$d_query = 'SELECT count(origin) as count FROM flight_cancellation_details WHERE refund_status = "INPROGRESS"';
		$response = $this->db->query($d_query)->result_array();
		$data['cancel_queue_count'] = isset($response[0]['count'])?$response[0]['count']:0;
		return $data;
	}
	public function get_custom_menu_search()
	{
		echo $d_query = 'SELECT * FROM custom_menu_search';
		$response = $this->db->query($d_query)->result_array();
		echo 'BALU',debug($response);exit;
	}

	public function flight_group_booking_count_notification_count(){
		$d_query = 'SELECT count(group_request_id) as count FROM group_request WHERE is_quoted = 0';
		$response = $this->db->query($d_query)->result_array();
		$data['group_booking_count'] = isset($response[0]['count'])?$response[0]['count']:0;
		return $data;
	}
	 

}
