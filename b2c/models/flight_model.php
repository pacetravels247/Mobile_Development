<?php
require_once 'transaction.php';
/**
 * Library which has generic functions to get data
 *
 * @package    Provab Application
 * @subpackage Flight Model
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V2
 */
Class Flight_Model extends Transaction
{
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

       function top_flight_destination(){
            $query = 'Select CT.*, CN.name AS country from flight_airport_list CT, api_country_list CN where top_destination = '.ACTIVE.' GROUP BY CT.origin; ';
//            echo($query);die;   
            $data = $this->db->query($query)->result_array();
            return $data;
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
					where domain_origin='.get_domain_auth_id().' AND BD.created_by_id ='.$GLOBALS['CI']->entity_user_id.' '.$condition;
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
			//Booking Details
			$bd_query = 'select * from flight_booking_details AS BD
						WHERE BD.domain_origin='.get_domain_auth_id().' and BD.created_by_id ='.$GLOBALS['CI']->entity_user_id.''.$condition.'
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
					
				$booking_itinerary_details	= $this->db->query($id_query)->result_array();
				$booking_customer_details	= $this->db->query($cd_query)->result_array();
				$booking_transaction_details = $this->db->query($td_query)->result_array();
				$cancellation_details = $this->db->query($cancellation_details_query)->result_array();
			}
			$response['data']['booking_details']			= $booking_details;
			$response['data']['booking_itinerary_details']	= $booking_itinerary_details;
			$response['data']['booking_transaction_details']	= $booking_transaction_details;
			$response['data']['booking_customer_details']	= $booking_customer_details;
			$response['data']['cancellation_details']	= $cancellation_details;
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
		// echo 'hererre I am';exit;
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
		//echo $bd_query;exit;
		//Itinerary Details
		$id_query = 'select * from flight_booking_itinerary_details AS ID WHERE ID.app_reference='.$this->db->escape($app_reference).' order by origin asc';
		//Transaction Details
		$td_query = 'select * from flight_booking_transaction_details AS CD WHERE CD.app_reference='.$this->db->escape($app_reference).'  order by origin asc';
		//Customer and Ticket Details
		$cd_query = 'select distinct CD.*,FPTI.api_passenger_origin,FPTI.TicketId,FPTI.TicketNumber,FPTI.IssueDate,FPTI.Fare,FPTI.SegmentAdditionalInfo
						from flight_booking_passenger_details AS CD
						left join flight_passenger_ticket_info FPTI on CD.origin=FPTI.passenger_fk
						WHERE CD.flight_booking_transaction_details_fk IN 
						(select distinct TD.origin from flight_booking_transaction_details AS TD 
						WHERE TD.app_reference ='.$this->db->escape($app_reference).') order by origin asc';
		// echo $cd_query;exit;
		//Cancellation Details
		$cancellation_details_query = 'select FCD.*
						from flight_booking_passenger_details AS CD
						left join flight_cancellation_details AS FCD ON FCD.passenger_fk=CD.origin
						WHERE CD.flight_booking_transaction_details_fk IN 
						(select TD.origin from flight_booking_transaction_details AS TD 
						WHERE TD.app_reference ='.$this->db->escape($app_reference).') order by origin asc';
		
		//Baggage Details
		$baggage_query = 'select CD.flight_booking_transaction_details_fk,CD.passenger_type,
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
						// echo $meal_query;exit;
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
		// echo 'hgererre I am';
		// debug($response);exit;
		if (valid_array($response['data']['booking_details']) == true and valid_array($response['data']['booking_itinerary_details']) == true and valid_array($response['data']['booking_customer_details']) == true) {
			$response['status'] = SUCCESS_STATUS;
		}
		return $response;
	}
	/**
	 * Read Individual booking details - dont use it to generate table
	 * @param $app_reference
	 * @param $booking_source
	 * @param $booking_status
	 */
	function booking_guest_user($app_reference, $booking_source='', $booking_status='')
	{
		// echo 'hererre I am';exit;
		$response['status'] = FAILURE_STATUS;
		$response['data'] = array();
		//Booking Details
		$bd_query1 = 'select * from flight_booking_transaction_details AS FTD WHERE (FTD.app_reference like '.$this->db->escape($app_reference).' || FTD.pnr like '.$this->db->escape($app_reference).')';
		
		$booking_details1 = $this->db->query($bd_query1)->result_array();
		
		if(valid_array($booking_details1)){
			$bd_query = 'select * from flight_booking_details AS BD WHERE BD.app_reference like '.$this->db->escape($app_reference);
			if (empty($booking_source) == false) {
				$bd_query .= '	AND BD.booking_source = '.$this->db->escape($booking_source);
			}
			if (empty($booking_status) == false) {
				$bd_query .= ' AND BD.status = '.$this->db->escape($booking_status);
			}
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
					
				$booking_itinerary_details	= $this->db->query($id_query)->result_array();
				$booking_customer_details	= $this->db->query($cd_query)->result_array();
				$booking_transaction_details = $this->db->query($td_query)->result_array();
				$cancellation_details = $this->db->query($cancellation_details_query)->result_array();
			}
			$response['data']['booking_details']			= $booking_details;
			$response['data']['booking_itinerary_details']	= $booking_itinerary_details;
			$response['data']['booking_transaction_details']	= $booking_transaction_details;
			$response['data']['booking_customer_details']	= $booking_customer_details;
			$response['data']['cancellation_details']	= $cancellation_details;
			
			// debug($response);exit;
			if (valid_array($response['data']['booking_details']) == true and valid_array($response['data']['booking_itinerary_details']) == true and valid_array($response['data']['booking_customer_details']) == true) {
				$response['status'] = SUCCESS_STATUS;
			}
		}
		
		return $response;
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
		$this->custom_db->insert_record('flight_cancellation_details', $data);
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
	 * Safe Search data for calendar
	 * @param array $search_data
	 */
	function calendar_safe_search_data($search_data)
	{
		$safe_data = array();
		//Origin
		if (isset($search_data['from']) == true and empty($search_data['from']) == false) {
			$safe_data['from_loc'] = $safe_data['from'] = substr(chop(substr($search_data['from'], -5), ')'), -3);
			$safe_data['from_location'] = $search_data['from'];
		} else {
			$safe_data['from_location'] = $safe_data['from_loc'] = $safe_data['from'] = 'DEL';
		}

		//Destination
		if (isset($search_data['to']) == true and empty($search_data['to']) == false) {
			$safe_data['to_loc'] = $safe_data['to'] = substr(chop(substr($search_data['to'], -5), ')'), -3);
			$safe_data['to_location'] = $search_data['to'];
		} else {
			$safe_data['to_location'] = $safe_data['to_loc'] = $safe_data['to'] = 'BLR';
		}

		//PreferredCarrier
		if (isset($search_data['carrier']) == true and valid_array($search_data['carrier']) == true) {
			$safe_data['carrier'] = $search_data['carrier'];
		} else {
			$safe_data['carrier'] = '';
		}

		//AdultCount
		if (isset($search_data['adult']) == true and empty($search_data['adult']) == false and intval($search_data['adult']) > 0) {
			$safe_data['adult'] = intval($search_data['adult']);
		} else {
			$safe_data['adult'] = 1;
		}

		//DepartureDate
		if (isset($search_data['depature']) == true and empty($search_data['depature']) == false) {
			$safe_data['depature'] = date('Y-m', strtotime($search_data['depature'])).'-01';
		} else {
			$safe_data['depature'] = date('Y-m-d');
		}

		//Type
		$safe_data['trip_type'] = 'OneWay';
		//CabinClass
		$safe_data['cabin'] = 'Economy';
		//ReturnDate
		$safe_data['return'] = '';
		//PromotionalPlanType
		$safe_data['PromotionalPlanType'] = 'Normal';
		return $safe_data;
	}

	function clean_search_data($temp_search_data)
	{
		$success = true;
		//make sure dates are correct
		if(isset($temp_search_data['trip_type']) == true){

			$clean_search['trip_type'] = $temp_search_data['trip_type'];

			if($temp_search_data['trip_type'] != 'multicity'){
				//make sure departure date is correct
				if (strtotime($temp_search_data['depature']) > time() || date('Y-m-d', strtotime($temp_search_data['depature'])) == date('Y-m-d')) {
					$clean_search['depature'] = $temp_search_data['depature'];
				}
				else {
					$success = false;
				}
				//If round way make sure return date is correct;
				if($temp_search_data['trip_type'] == 'circle'){
					$clean_search['trip_type_label'] = 'Round Way';
					if(strtotime($temp_search_data['return']) > time() && strtotime($temp_search_data['return']) >= strtotime($temp_search_data['depature'])){
						$clean_search['return'] = $temp_search_data['return'];
					} else {
						$success = false;
					}
				} else {
					$clean_search['trip_type_label'] = 'One Way';
				}

				//departure airport
				if(isset($temp_search_data['from']) == true){
					$clean_search['from'] = $temp_search_data['from'];
					$clean_search['from_loc'] = substr(chop(substr($clean_search['from'], -5), ')'), -3);
					$clean_search['from_loc_id'] = @$temp_search_data['from_loc_id'];
				}else{
					$success = false;
				}

				//arrival airport
				if(isset($temp_search_data['to']) == true){
					$clean_search['to'] = $temp_search_data['to'];
					$clean_search['to_loc'] = substr(chop(substr($clean_search['to'], -5), ')'), -3);
					$clean_search['to_loc_id'] = @$temp_search_data['to_loc_id'];
				}else{
					$success = false;
				}
			$clean_search['is_domestic'] = $this->is_domestic_flight($clean_search['from_loc'], $clean_search['to_loc']);
			} else {
				//multicity
				$clean_search['trip_type_label'] = 'Multi City';
				for($i=0; $i<count($temp_search_data['depature']); $i++){
					//make sure departure date is correct
					if($success == true) {
						if (strtotime($temp_search_data['depature'][$i]) > time() || date('Y-m-d', strtotime($temp_search_data['depature'][$i])) == date('Y-m-d')
						&& (strtotime($temp_search_data['depature'][$i]) >= strtotime(date('Y-m-d', @$temp_search_data['depature'][$i-1])))) {
							$clean_search['depature'][$i] = $temp_search_data['depature'][$i];
						}
						else {
							$success = false;
						}
						//departure airport
						if(isset($temp_search_data['from'][$i]) == true){
							$clean_search['from'][$i] = $temp_search_data['from'][$i];
							$clean_search['from_loc'][$i] = substr(chop(substr($clean_search['from'][$i], -5), ')'), -3);
							$clean_search['from_loc_id'][$i] = @$temp_search_data['from_loc_id'][$i];
						}else{
							$success = false;
						}
						//arrival airport
						if(isset($temp_search_data['to'][$i]) == true){
							$clean_search['to'][$i] = $temp_search_data['to'][$i];
							$clean_search['to_loc'][$i] = substr(chop(substr($clean_search['to'][$i], -5), ')'), -3);
							$clean_search['to_loc_id'][$i] = @$temp_search_data['to_loc_id'][$i];
						}else{
							$success = false;
						}
					} else {
						break;
					}
				}
				$clean_search['is_domestic'] = $this->is_domestic_flight($clean_search['from_loc'], $clean_search['to_loc']);
			}
			if(isset($temp_search_data['adult']) == true){
				$clean_search['adult_config'] = $temp_search_data['adult'];
			}else{
				$success = false;
			}

			if(isset($temp_search_data['child']) == true){
				$clean_search['child_config'] = $temp_search_data['child'];
			}

			if(isset($temp_search_data['infant']) == true){
				$clean_search['infant_config'] = $temp_search_data['infant'];
			}

			if(isset($temp_search_data['v_class']) == true){
				$clean_search['v_class'] = $temp_search_data['v_class'];
			}
			if(isset($temp_search_data['lcc_gds']) == true){
				$clean_search['lcc_gds'] = $temp_search_data['lcc_gds'];
			}
			if(isset($temp_search_data['carrier']) == true){
				$clean_search['carrier'] = $temp_search_data['carrier'];
			} else {
				$clean_search['carrier'] = '';
			}
		}else{
			$success = false;
			$clean_search =  array();
		}
		return array('data' => $clean_search, 'status' => $success);
	}

	/**
	 * get search data and validate it
	 */
	function get_safe_search_data($search_id)
	{
		$search_data = $this->get_search_data($search_id);
		//debug($search_data);exit;
		$success = true;
		$clean_search = '';
		if ($search_data != false) {
			//validate
			$temp_search_data = json_decode($search_data['search_data'], true);
			$clean_search = $this->clean_search_data($temp_search_data);
			$success = $clean_search['status'];
			$clean_search = $clean_search['data'];
			return array('status' => $success, 'data' => $clean_search);
		}
	}

	/**
	 * get search data without doing any validation
	 * @param $search_id
	 */
	function get_search_data($search_id)
	{
		$search_data = $this->custom_db->single_table_records('search_history', '*', array('search_type' => META_AIRLINE_COURSE, 'origin' => $search_id));
		if ($search_data['status'] == true) {
			return $search_data['data'][0];
		} else {
			return false;
		}
	}
	/**
	 * get all the booking source which are active for current domain
	 */
	function active_booking_source()
	{
		$query = 'select BS.source_id, BS.origin from meta_course_list AS MCL, booking_source AS BS, activity_source_map AS ASM WHERE
		MCL.origin=ASM.meta_course_list_fk and ASM.booking_source_fk=BS.origin and MCL.course_id='.$this->db->escape(META_AIRLINE_COURSE).'
		and BS.booking_engine_status='.ACTIVE.' AND MCL.status='.ACTIVE.' AND ASM.status="active"';
		return $this->db->query($query)->result_array();
	}

	/**
	 *TEMPORARY FUNCTION NEEDS TO BE CLEANED UP IN PRODUCTION ENVIRONMENT
	 */
	function get_static_response($token_id)
	{
		$static_response = $this->custom_db->single_table_records('test', '*', array('origin' => intval($token_id)));
		return json_decode($static_response['data'][0]['test'], true);
	}

	/**
	 * Lock All the tables necessary for flight transaction to be processed
	 */
	static function lock_tables()
	{
		$CI = & get_instance();
		$CI->db->query(' LOCK TABLES domain_list AS DL WRITE, currency_converter AS CC WRITE ;');
	}

	/**
	 * Save complete master transaction details of flight
	 * @param $domain_origin
	 * @param $status
	 * @param $app_reference
	 * @param $booking_source
	 * @param $is_lcc
	 * @param $total_fare
	 * @param $domain_markup
	 * @param $level_one_markup
	 * @param $currency
	 * @param $phone
	 * @param $alternate_number
	 * @param $email
	 * @param $journey_start
	 * @param $journey_end
	 * @param $journey_from
	 * @param $journey_to
	 * @param $payment_mode
	 * @param $attributes
	 * @param $created_by_id
	 */
	function save_flight_booking_details(
	$domain_origin, $status, $app_reference, $cabin_class, $booking_source, $phone, $alternate_number, $email, $journey_start, $journey_end,
	$journey_from, $journey_to, $payment_mode,	$attributes, $created_by_id, $from_loc, $to_loc, $from_to_trip_type, 
	$transaction_currency, $currency_conversion_rate, $gst_details, $phone_country_code, $booking_billing_type, $price_attr = array())
	{
		$data['domain_origin'] = $domain_origin;
		$data['status'] = $status;
		$data['app_reference'] = $app_reference;
		$data['booking_source'] = $booking_source;
		$data['phone'] = $phone;
		$data['alternate_number'] = $alternate_number;
		$data['email'] = $email;
		$data['journey_start'] = $journey_start;
		$data['journey_end'] = $journey_end;
		$data['journey_from'] = $journey_from;
		$data['journey_to'] = $journey_to;
		$data['payment_mode'] = $payment_mode;
		$data['attributes'] = $attributes;
		$data['created_by_id'] = $created_by_id;
		$data['created_datetime'] = date('Y-m-d H:i:s');

		$data['from_loc'] = $from_loc;
		$data['to_loc'] = $to_loc;
		$data['trip_type'] = $from_to_trip_type;
		$data['cabin_class'] = $cabin_class;
		$data['currency'] = $transaction_currency;
		$data['currency_conversion_rate'] = $currency_conversion_rate;
		$data['gst_details'] = $gst_details;
		$data['phone_code'] = $phone_country_code;
		$data['booking_billing_type'] = $booking_billing_type;

		// Adding default value
		$data['booking_billing_type'] = '';
		$data['convinence_value'] = 0;
		//$data['convinence_value_type'] = '';
		$data['convinence_per_pax'] = 0;
		$data['convinence_amount'] = 0;
		$data['discount'] = 0;
		$data['promo_code'] = 0;
		$data['price_attr'] = $price_attr;
		// debug($data);exit;
		$this->custom_db->insert_record('flight_booking_details', $data);
	}

	/**
	 * Save Passenger details of Flight Booking
	 * @param $app_reference
	 * @param $passenger_type
	 * @param $is_lead
	 * @param $title
	 * @param $first_name
	 * @param $middle_name
	 * @param $last_name
	 * @param $date_of_birth
	 * @param $gender
	 * @param $passenger_nationality
	 * @param $passport_number
	 * @param $passport_issuing_country
	 * @param $passport_expiry_date
	 * @param $status
	 * @param $attributes
	 */
	function save_flight_booking_passenger_details(
	$app_reference, $passenger_type, $is_lead, $title, $first_name, $middle_name, $last_name, $date_of_birth,
	$gender, $passenger_nationality, $passport_number, $passport_issuing_country, $passport_expiry_date, $status,
	$attributes, $flight_booking_transaction_details_fk)
	{
             # Adding value for empty fields
            if(empty($date_of_birth)==true)
            {
                $date_of_birth='NULL';
            }
		$data['app_reference'] = $app_reference;
		$data['passenger_type'] = $passenger_type;
		$data['is_lead'] = $is_lead;
		$data['title'] = $title;
		$data['first_name'] = $first_name;
		$data['middle_name'] = $middle_name;
		$data['last_name'] = $last_name;
		$data['date_of_birth'] = $date_of_birth;
		$data['gender'] = $gender;
		$data['passenger_nationality'] = $passenger_nationality;
		$data['passport_number'] = $passport_number;
		$data['passport_issuing_country'] = $passport_issuing_country;
		$data['passport_expiry_date'] = $passport_expiry_date;
		$data['status'] = $status;
		$data['attributes'] = $attributes;
		$data['flight_booking_transaction_details_fk'] = $flight_booking_transaction_details_fk;
		
		return $this->custom_db->insert_record('flight_booking_passenger_details', $data);
	}
	/**
	 * Balu A
	 * Save Passenger Ticket Information
	 * @param $passenger_fk	
	 */
	function save_passenger_ticket_info($passenger_fk)
	{
		$data['passenger_fk'] = $passenger_fk;
		//Added empty value due to PDO connection for avoiding default null
		$data['TicketId'] = '';
		$data['TicketNumber'] = '';
		$data['IssueDate'] = date('Y-m-d H:i:s');
		$data['Fare'] = '';
		$data['SegmentAdditionalInfo'] = '';
		$data['ValidatingAirline'] = '';
		$data['CorporateCode'] = '';
		$data['TourCode'] = '';
		$data['Endorsement'] = '';
		$data['Remarks'] = '';
		$data['ServiceFeeDisplayType'] = '';
		
		$this->custom_db->insert_record('flight_passenger_ticket_info', $data);
	}

	/**	 
	 * Update Passenger Ticket Information
	 * @param $passenger_fk
	 * @param $TicketId
	 * @param $TicketNumber
	 * @param $IssueDate
	 * @param $Fare
	 * @param $SegmentAdditionalInfo
	 * @param $ValidatingAirline
	 * @param $CorporateCode
	 * @param $TourCode
	 * @param $Endorsement
	 * @param $Remarks
	 * @param $ServiceFeeDisplayType
	 */
	 function update_passenger_ticket_info($passenger_fk, $TicketId, $TicketNumber, $IssueDate, $Fare, $SegmentAdditionalInfo,
	$ValidatingAirline, $CorporateCode, $TourCode, $Endorsement, $Remarks, $ServiceFeeDisplayType, $api_passenger_origin){
		
		 $data['TicketId'] = $TicketId;
		 $data['api_passenger_origin'] = $api_passenger_origin;
		 $data['TicketNumber'] = $TicketNumber;
		 $data['IssueDate'] = $IssueDate;
		 $data['Fare'] = $Fare;
		 $data['SegmentAdditionalInfo'] = $SegmentAdditionalInfo;
		 $data['ValidatingAirline'] = $ValidatingAirline;
		 $data['CorporateCode'] = $CorporateCode;
		 $data['TourCode'] = $TourCode;
		 $data['Endorsement'] = $Endorsement;
		 $data['Remarks'] = $Remarks;
		 $data['ServiceFeeDisplayType'] = $ServiceFeeDisplayType;
		 $update_condtion['passenger_fk'] = $passenger_fk;
		 $this->custom_db->update_record('flight_passenger_ticket_info', $data, $update_condtion);
	 }
	/**
	 * Save Individual booking details of a transaction
	 * @param $app_reference
	 * @param $transaction_status
	 * @param $status_description
	 * @param $pnr
	 * @param $book_id
	 * @param $source
	 * @param $ref_id
	 * @param $attributes
	 * @param $sequence_number
	 */
	function save_flight_booking_transaction_details(
	$app_reference, $transaction_status, $status_description, $pnr, $book_id, $source, $ref_id, $attributes,
	$sequence_number, $currency, $total_fare, $admin_markup, $agent_markup, $admin_commission, $agent_commission,
	$getbooking_StatusCode, $getbooking_Description, $getbooking_Category, $admin_tds, $agent_tds, $gst)
	{
		$data['app_reference'] = $app_reference;
		$data['status'] = $transaction_status;
		$data['status_description'] = $status_description;
		$data['pnr'] = $pnr;
		$data['book_id'] = $book_id;
		$data['source'] = $source;
		$data['ref_id'] = $ref_id;
		$data['attributes'] = $attributes;
		$data['sequence_number'] = $sequence_number;

		$data['total_fare'] = $total_fare;
		$data['admin_commission'] = $admin_commission;
		$data['agent_commission'] = 0;//$agent_commission;
		$data['admin_markup'] = $admin_markup;
		$data['agent_markup'] = $agent_markup;
		$data['currency'] = $currency;

		$data['getbooking_StatusCode'] = $getbooking_StatusCode;
		$data['getbooking_Description'] = $getbooking_Description;
		$data['getbooking_Category'] = $getbooking_Category;
		
		$data['admin_tds'] = $admin_tds;
		$data['agent_tds'] = 0;//$agent_tds;
		$data['gst'] = $gst;


		
		return $this->custom_db->insert_record('flight_booking_transaction_details', $data);
	}

	/**
	 * Save Individual booking flight details
	 * @param $app_reference
	 * @param $segment_indicator
	 * @param $airline_code
	 * @param $airline_name
	 * @param $flight_number
	 * @param $fare_class
	 * @param $from_airport_code
	 * @param $from_airport_name
	 * @param $to_airport_code
	 * @param $to_airport_name
	 * @param $departure_datetime
	 * @param $arrival_datetime
	 * @param $status
	 * @param $operating_carrier
	 * @param $attributes
	 */
	function save_flight_booking_itinerary_details(
	$app_reference, $segment_indicator, $airline_code, $airline_name, $flight_number, $fare_class, $from_airport_code, $from_airport_name,
	$to_airport_code, $to_airport_name, $departure_datetime, $arrival_datetime, $status, $operating_carrier, $attributes,
	$FareRestriction, $FareBasisCode, $FareRuleDetail, $airline_pnr, $cabin_baggage, $checkin_baggage, $is_refundable)
	{
		$data['app_reference'] = $app_reference;
		$data['segment_indicator'] = $segment_indicator;
		$data['airline_code'] = $airline_code;
		$data['airline_name'] = $airline_name;
		$data['flight_number'] = $flight_number;
		$data['fare_class'] = $fare_class;
		$data['from_airport_code'] = $from_airport_code;
		$data['from_airport_name'] = $from_airport_name;
		$data['to_airport_code'] = $to_airport_code;
		$data['to_airport_name'] = $to_airport_name;
		$data['departure_datetime'] = $departure_datetime;
		$data['arrival_datetime'] = $arrival_datetime;
		$data['status'] = $status;
		$data['operating_carrier'] = $operating_carrier;
		$data['attributes'] = $attributes;
		$data['FareRestriction'] = $FareRestriction;
		$data['FareBasisCode'] = $FareBasisCode;
		$data['FareRuleDetail'] = $FareRuleDetail;
		$data['cabin_baggage'] = $cabin_baggage;
		$data['checkin_baggage'] = $checkin_baggage;
		$data['is_refundable'] = $is_refundable;
		$data['airline_pnr'] = $airline_pnr;
		
		$this->custom_db->insert_record('flight_booking_itinerary_details', $data);
	}
	/**
	 * Save Baggage Information
	 * @param unknown_type $passenger_fk
	 * @param unknown_type $from_airport_code
	 * @param unknown_type $to_airport_code
	 * @param unknown_type $description
	 * @param unknown_type $price
	 */
	public function save_passenger_baggage_info($passenger_fk, $from_airport_code, $to_airport_code, $description, $price, $code)
	{
		$data = array();
		$data['passenger_fk'] = $passenger_fk;
		$data['from_airport_code'] = $from_airport_code;
		$data['to_airport_code'] = $to_airport_code;
		$data['description'] = $description;
		$data['price'] = $price;
		$data['code'] = $code;
		
		$this->custom_db->insert_record('flight_booking_baggage_details', $data);
	}
	/**
	 * Save Baggage Information
	 * @param unknown_type $passenger_fk
	 * @param unknown_type $from_airport_code
	 * @param unknown_type $to_airport_code
	 * @param unknown_type $description
	 * @param unknown_type $price
	 */
	public function save_passenger_meals_info($passenger_fk, $from_airport_code, $to_airport_code, $description, $price, $code, $type='dynamic')
	{
		$data = array();
		$data['passenger_fk'] = $passenger_fk;
		$data['from_airport_code'] = $from_airport_code;
		$data['to_airport_code'] = $to_airport_code;
		$data['description'] = $description;
		$data['price'] = $price;
		$data['code'] = $code;
		$data['type'] = $type;
		
		$this->custom_db->insert_record('flight_booking_meal_details', $data);
	}
	/**
	 * Save Seat Information
	 * @param unknown_type $passenger_fk
	 * @param unknown_type $from_airport_code
	 * @param unknown_type $to_airport_code
	 * @param unknown_type $description
	 * @param unknown_type $price
	 */
	public function save_passenger_seat_info($passenger_fk, $from_airport_code, $to_airport_code, $description, $price, $code, $type='dynamic', $airline_code = '', $flight_number ='')
	{
		$data = array();
		$data['passenger_fk'] = $passenger_fk;
		$data['from_airport_code'] = $from_airport_code;
		$data['to_airport_code'] = $to_airport_code;
		$data['description'] = $description;
		$data['price'] = $price;
		$data['code'] = $code;
		$data['type'] = $type;
		$data['airline_code'] = $airline_code;
		$data['flight_number'] = $flight_number;
		$this->custom_db->insert_record('flight_booking_seat_details', $data);
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
		$data['total_pax'] = $search_data['adult'] + $search_data['child'] + $search_data['infant'];
		
		if($search_data['trip_type'] == 'oneway' || $search_data['trip_type'] == 'circle') {
			$data['trip_type'] = $search_data['trip_type'];
			$from_location = array($search_data['from']);
			$to_location = array($search_data['to']);
			$depature = array($search_data['depature']);
		} else if($search_data['trip_type'] == 'multicity') {
			$data['trip_type'] = 'multistop';//FIXME: change it from database
			$from_location = $search_data['from'];
			$to_location = $search_data['to'];
			$depature = $search_data['depature'];
		}
		for($i=0; $i<count($from_location); $i++){
			$temp_location = explode('(', $from_location[$i]);
			$data['from_location'] = trim($temp_location[0]);
			if (isset($temp_location[1]) == true) {
				$data['from_code'] = trim($temp_location[1], '() ');
			} else {
				$data['from_code'] = '';
			}
	
			$temp_location = explode('(', $to_location[$i]);
			$data['to_location'] = trim($temp_location[0]);
			if (isset($temp_location[1]) == true) {
				$data['to_code'] = trim($temp_location[1], '() ');
			} else {
				$data['to_code'] = '';
			}
			$data['journey_date'] = date('Y-m-d', strtotime($depature[$i]));
			//$this->custom_db->insert_record('search_flight_history', $data);
		}
	}
	/**
	 * Balu A
	 * Returns Airport timezone offset
	 * @param $airport_code
	 */
	public function get_airport_timezone_offset($airport_code,$journey_date)
	{
		//FIXME: cache the data
            
                $query_al='select timezonename, CountryCode from flight_airport_list where airport_code = "'.$airport_code.'"';
                $timezonename = $this->db->query($query_al)->result_array();
                
                if($timezonename[0]['timezonename']!='')
                {
                    
                     $target_time_zone = new DateTimeZone($timezonename[0]['timezonename']);
                     $date_time = new DateTime($journey_date, $target_time_zone);
                     $time_zone_value=substr($date_time->format(DateTime::ATOM), -6);
                     
                } 
                if(isset($time_zone_value))
                {
                   return $time_zone_value;  
                }
                else if(!isset($time_zone_value) && $timezonename[0]['CountryCode']=="IN")
                {
                     return "+5.30";
                }
                else {
                     $journey_month = date('m', strtotime($journey_date));
		     $query = 'select FAL.airport_code,FAT.start_month,FAT.end_month,FAT.timezone_offset from flight_airport_list FAL
					join flight_airport_timezone_offset FAT on FAT.flight_airport_list_fk=FAL.origin
					where airport_code = "'.$airport_code.'" and (start_month<='.$journey_month.' and end_month>='.$journey_month.')
					order by 
					CASE
					WHEN start_month	= '.$journey_month.' THEN 1
		            WHEN end_month	= '.$journey_month.' THEN 2
					ELSE 3 END';
		     $timezone_offset = $this->db->query($query)->result_array();
		     return $timezone_offset[0]['timezone_offset'];
              }
	}

		/**Promo code checking ***/
	function get_promo($promo){
		$query = "SELECT * FROM `promo_code_list` WHERE `promo_code` = '$promo' ";
		return($this->db->query($query));
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
	/**
	 * Updates the flight booking transaction and passenger status 
	 * @param unknown_type $app_reference
	 * @param unknown_type $flight_booking_transaction_details_origin
	 */
	public function update_flight_booking_transaction_failure_status($app_reference, $flight_booking_transaction_details_origin)
	{
		$failed_status = 'BOOKING_FAILED';
		//Update the Transaction failed status
		$transaction_failure_data['status'] = $failed_status;
		$transaction_failure_condition['origin'] = $flight_booking_transaction_details_origin;
		$GLOBALS['CI']->custom_db->update_record('flight_booking_transaction_details', $transaction_failure_data, $transaction_failure_condition);
		
		//Update Passenger failed status
		$passenger_failure_condition['flight_booking_transaction_details_fk'] = $flight_booking_transaction_details_origin;
		$passenger_failure_data['status'] = $failed_status;
		$GLOBALS['CI']->custom_db->update_record('flight_booking_passenger_details', $passenger_failure_data, $passenger_failure_condition);
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
	function flight_top_destinations()
	{
		$query = 'Select * from bus_stations where top_destination = '.ACTIVE;
		$data = $this->db->query($query)->result_array();
		return $data;
	}
    function save_insurance_details($response)
    {
        	//1.Adding Cancellation Details
	$data = array();
	$data['app_reference'] = $response['app_reference'];
	$data['status'] = $response['status'];
	$data['response'] = json_encode($response['response']);
	$data['travel_date'] =  $response['travel_date'];
	$data['created_datetime'] = date('Y-m-d H:i:s');
            
	$this->custom_db->insert_record('insurance_details', $data);
            
    }
    function get_county_code($country){

    	$country_data = $this->db->query('select iso_country_code from country_list where en_name ="'.$country.'"')->row_array();
   		if(valid_array($country_data)){
   			
   			return $country_data['iso_country_code'];
   		}
    }
    function update_pax_details($data, $id) {
		if ($this->custom_db->update_record("flight_booking_passenger_details", $data, array("origin" => $id))) {
			return true;
		} else {
			return false;
		}
	}

	function update_booking_details($data, $id) {
		if ($this->custom_db->update_record("flight_booking_details", $data, array("origin" => $id))) {
			return true;
		} else {
			return false;
		}
	}

	function manage_blocked_airlines($source=""){
			    $this->db->where(array('source_id' => $source));
		return $this->db->get('manage_airlines')->result_array();
	}

	//Commission related changes
	function master_commission($booking_source){
		$query = 'SELECT FCD.* FROM b2c_flight_commission_details AS FCD LEFT JOIN booking_source AS BS ON FCD.	booking_source_origin = BS.origin WHERE BS.source_id = "'.$booking_source.'" AND FCD.is_master="1"';
		return $this->db->query($query)->result_array();
	}
	function master_commission_generic_tp($booking_source){
		$query = 'SELECT FCD.* FROM b2c_flight_commission_details AS FCD LEFT JOIN booking_soure AS BS ON FCD.	booking_source_origin = BS.origin WHERE BS.source_id = "'.$booking_source.'" AND FCD.is_master="1" AND airline_id="0"';
		return $this->db->query($query)->result_array();
	}
	function master_general_commission_tp(){
		$query = 'SELECT * FROM b2c_flight_commission_details WHERE booking_source_origin="0" AND course_id = "VHCID1420613784" AND is_master="1"';
		return $this->db->query($query)->result_array();
	}
	function master_general_commission(){
		$query = 'SELECT * FROM b2c_flight_commission_details WHERE booking_source_origin = "0" AND is_master="1"';
		return $this->db->query($query)->result_array();	
	}
	function master_tp_commission($bs_origin, $class, $type){
		$query = 'SELECT * FROM b2c_flight_commission_details WHERE airline_class="'.$class.'" AND trip_type = "'.$type.'" AND booking_source_origin = "'.$bs_origin.'" AND is_master="1"';
		return $this->db->query($query)->result_array();
	}
}