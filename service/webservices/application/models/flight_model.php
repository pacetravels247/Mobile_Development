<?php
/**
 * Library which has generic functions to get data
 *
 * @package    Provab Application
 * @subpackage Flight Model
 * @author     Arjun J<arjunjgowda260389@gmail.com>
 * @version    V2
 */
Class Flight_Model extends CI_Model
{
	/*
	 *
	 * Get Airport List
	 *
	 */

	function get_airport_list($query)
	{

		$this->db->like('airport_city', $query);
		$this->db->or_like('airport_code', $query);
		$this->db->or_like('country', $query);
		$this->db->limit(20);
		return $this->db->get('flight_airport_list');
	}

	/**
	 * get search data and validate it
	 */
	function get_safe_search_data($search_id)
	{
		$search_data_condition = array();
		$search_data_condition[] = array('origin', '=', intval($search_id));
		$search_data = $this->get_search_data($search_data_condition);

		$success = true;
		$clean_search = array();
		if ($search_data != false) {
			//validate
			$search_data = json_decode($search_data['search_data'], true);
			if(@!empty($search_data['search_booking_source'])){
				$clean_search['SearchBookingSource'] = $search_data['search_booking_source'];
			}
			$clean_search['trip_type'] = $search_data['JourneyType'];
			$clean_search['carrier'] = $search_data['PreferredAirlines'];
			$clean_search['cabin_class'] = $search_data['CabinClass'];
			$clean_search['adult_config'] = $search_data['AdultCount'];
			$clean_search['child_config'] = $search_data['ChildCount'];
			$clean_search['infant_config'] = $search_data['InfantCount'];
			$clean_search['is_domestic'] = $search_data['IsDomestic'];
			
			$clean_search['total_pax'] = intval($clean_search['adult_config'])+intval($clean_search['child_config'])+intval($clean_search['infant_config']);
			
			if($clean_search['trip_type'] == 'multicity'){
				$Segments = $search_data['Segments'];
				$clean_search['from'] = array_column($Segments, 'Origin');
				$clean_search['to'] = array_column($Segments, 'Destination');
				$clean_search['depature'] = array_column($Segments,'DepartureDate');
				$clean_search['from_country'] = array_column($Segments, 'Origin_Country');
				$clean_search['to_country'] = array_column($Segments, 'Dest_Country');
			} else {
				$Segments = $search_data['Segments'][0];
				$clean_search['from'] = $Segments['Origin'];
				$clean_search['to'] = $Segments['Destination'];
				$clean_search['depature'] = $Segments['DepartureDate'];
				$clean_search['from_country'] = $Segments['Origin_Country'];
				$clean_search['to_country'] = $Segments['Dest_Country'];
				if($clean_search['trip_type'] == 'return'){
					$clean_search['return'] = $Segments['ReturnDate'];
				}
			}
				
		} else {
			$success = false;
		}
		return array('status' => $success, 'data' => $clean_search);
	}
	/**
	 * Save Seaech Data
	 * Enter description here ...
	 * @param array $request
	 */
	function save_search_data($request)
	{
		$data['status'] = SUCCESS_STATUS;
		$cache_key = $this->redis_server->generate_cache_key();
		foreach($request['Segments'] as $s_key => $segment){
			$org_airport_data = $this->get_airport_city_name($segment['Origin']);
			$dest_airport_data = $this->get_airport_city_name($segment['Destination']);
			$request['Segments'][$s_key]['Origin_Country'] = $org_airport_data->country;
			$request['Segments'][$s_key]['Dest_Country'] = $dest_airport_data->country;
			
		}
		//Checking is domest flight
		$from_loc = array_column($request['Segments'], 'Origin');
		$to_loc = array_column($request['Segments'], 'Destination');
		$is_domestic = $this->is_domestic_flight($from_loc, $to_loc);
		$request['IsDomestic'] = $is_domestic;
		$request['JourneyType'] = strtolower($request['JourneyType']);
		$request['CabinClass'] = strtolower($request['CabinClass']);
		//$request['SearchBookingSource'] = $request['search_booking_source'];
		$search_history_data = array();
		$search_history_data['domain_origin'] = 	get_domain_auth_id();
		$search_history_data['cache_key'] = 		$cache_key;
		$search_history_data['search_type'] = 		META_AIRLINE_COURSE;
		$search_history_data['search_data'] = 		json_encode($request);
		$search_history_data['created_datetime'] =	db_current_datetime();
		$insert_data = $this->custom_db->insert_record('search_history', $search_history_data);
		if($insert_data['status'] == QUERY_SUCCESS){
			$data['cache_key'] = $cache_key;
			$data['search_id'] = $insert_data['insert_id'];
		} else {
			$data['status'] = FAILURE_STATUS;
		}
		return $data;
	}
	 public function get_airport_city_name($airport_code){
        $query1 = 'select * from flight_airport_list WHERE airport_code="'.$airport_code.'"';
        // echo $query1;exit;
        $data = $this->db->query($query1)->row();
        return $data;
    }
	/**
	 * get search data without doing any validation
	 * @param $search_id
	 */
	function get_search_data($condition=array(), $count=false, $offset=0, $limit=100000000000)
	{
		$condition = $this->custom_db->get_custom_condition($condition );
		$query = 'select SH.* from search_history SH where search_type="'.META_AIRLINE_COURSE.'"'.$condition;
		$search_data = $this->db->query($query)->row_array();
		
		if(valid_array($search_data) == true){
			return $search_data;
		} else {
			return false;
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
	 * Flight booking report
	 * 
	 */
	
	function booking($condition=array(), $count=false, $offset=0, $limit=100000000000)
	{
		//$condition = $this->custom_db->get_custom_condition($condition);
		//BT, CD, ID
		if ($count) {
			$query = 'select count(*) as total_records from flight_booking_details BD where domain_origin='.get_domain_auth_id().' AND BD.created_by_id ='.$GLOBALS['CI']->entity_user_id;
			$data = $this->db->query($query)->row_array();
			return $data['total_records'];
		} else {
			$cols = '
				BD.status, BD.app_reference, BD.booking_source, BD.total_fare,
				BD.domain_markup, BD.level_one_markup, BD.currency, BD.journey_from, BD.journey_to, BD.journey_start, BD.journey_end,
				BD.phone, BD.payment_mode, BD.created_by_id, BD.created_datetime, BD.email, BD.phone AS phone_number,
				count(distinct(CD.origin)) as total_passengers,
				concat(CD.title, " ", CD.first_name, " ", CD.middle_name, " ",CD.last_name) name,
				POL.name as payment_name';
			$query = 'select '.$cols.' from flight_booking_details AS BD, flight_booking_passenger_details AS CD, flight_booking_itinerary_details AS ID, flight_booking_transaction_details AS TD
				,payment_option_list as POL where BD.app_reference=TD.app_reference AND POL.payment_category_code=BD.payment_mode AND BD.app_reference=CD.app_reference AND BD.app_reference=ID.app_reference AND BD.domain_origin='.get_domain_auth_id().'  
				AND BD.created_by_id ='.$GLOBALS['CI']->entity_user_id.' GROUP BY BD.app_reference, CD.app_reference, ID.app_reference ORDER BY BD.origin desc, ID.origin, CD.origin limit '.$offset.', '.$limit;;
			return $this->db->query($query)->result_array();
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
	$domain_origin, $status, $app_reference, $booking_source, $is_lcc, $currency, $phone, $alternate_number, $email, $journey_start, $journey_end,
	$journey_from, $journey_to, $payment_mode,	$attributes, $created_by_id, $currency_conversion_rate, $flight_version)
	{
		$app_reference_exists = $this->is_app_reference_exists($app_reference);
		if($app_reference_exists == false){//If AppReference Not Exists, then insert the data
			$data['domain_origin'] = $domain_origin;
			$data['status'] = $status;
			$data['app_reference'] = $app_reference;
			$data['booking_source'] = $booking_source;
			if(empty($is_lcc)){
				$is_lcc = 0;
			}
			$data['is_lcc'] = $is_lcc;
			$data['currency'] = $currency;
			$data['phone'] = $phone;
			if(empty($alternate_number)){
				$alternate_number = 7010847855;
			}
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
			$data['currency_conversion_rate'] = $currency_conversion_rate;
			$data['version'] = $flight_version;
			if(!isset($data['total_fare'])){
				$data['total_fare'] = 0;
			}
			if(!isset($data['domain_markup'])){
				$data['domain_markup'] = 0;
			}
			if(!isset($data['level_one_markup'])){
				$data['level_one_markup'] = 0;
			}
			$this->custom_db->insert_record('flight_booking_details', $data);
		}
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
	$gender, $passenger_nationality, $passport_number, $passport_issuing_country, $passport_expiry_date, $status, $attributes, $flight_booking_transaction_details_fk)
	{
		$data['app_reference'] = $app_reference;
		$data['passenger_type'] = $passenger_type;
		$data['is_lead'] = 1;//$is_lead;
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
		
		
		$insert_id = $this->custom_db->insert_record('flight_booking_passenger_details', $data);
		return $insert_id;
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
	function save_flight_booking_transaction_details($app_reference, $transaction_status, $status_description, $pnr, $book_id, $source, $ref_id, 
						$attributes, $sequence_number,$total_fare, $domain_markup, $admin_commission, $agent_commission, $admin_tds, $agent_tds, $booking_source=TBO_FLIGHT_BOOKING_SOURCE, $fare_breakup='')
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
		$data['domain_markup'] = $domain_markup;
		
		$data['admin_commission'] = $admin_commission;
		$data['agent_commission'] = $agent_commission;
		$data['admin_tds'] = $admin_tds;
		$data['agent_tds'] = $agent_tds;
		
		$data['booking_source'] = $booking_source;
		$data['fare_breakup'] = $fare_breakup;
		$data['	offline_booking_source'] = '';
		$data['airline_pnr'] = '';
		$data['level_one_markup'] = 0;
		
		$insert_id = $this->custom_db->insert_record('flight_booking_transaction_details', $data);
		return $insert_id;
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
	$to_airport_code, $to_airport_name, $departure_datetime, $arrival_datetime, $status, $operating_carrier, $attributes,$flight_booking_transaction_details_fk = 0)
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
		$data['flight_booking_transaction_details_fk'] = $flight_booking_transaction_details_fk;
		
		
		$this->custom_db->insert_record('flight_booking_itinerary_details', $data);
	}
	/**
	 * Jaganath
	 * Adding Pax Details
	 * @param $passenger_fk
	 */
	function save_passenger_ticket_info($passenger_fk)
	{
		$data['passenger_fk'] = $passenger_fk;
		$data['TicketId'] = '';
		$data['TicketNumber'] = '';
		$data['IssueDate'] = date('Y-m-d H:i:s');
		$data['Fare'] = 0;
		$data['SegmentAdditionalInfo'] = '';
		$data['ValidatingAirline'] = '';
		$data['CorporateCode'] = '';
		$data['TourCode'] = '';
		$data['Endorsement'] = '';
		$data['Remarks'] = '';
		$data['ServiceFeeDisplayType'] = '';
		$insert_id = $this->custom_db->insert_record('flight_passenger_ticket_info', $data);
		return $insert_id;
	}
	/**
	 * Updates Passenger Ticket Details
	 * @param unknown_type $passenger_fk
	 * @param unknown_type $TicketId
	 * @param unknown_type $TicketNumber
	 * @param unknown_type $IssueDate
	 * @param unknown_type $Fare
	 * @param unknown_type $SegmentAdditionalInfo
	 * @param unknown_type $ValidatingAirline
	 * @param unknown_type $CorporateCode
	 * @param unknown_type $TourCode
	 * @param unknown_type $Endorsement
	 * @param unknown_type $Remarks
	 * @param unknown_type $ServiceFeeDisplayType
	 */
	function update_passenger_ticket_info($passenger_fk, $TicketId, $TicketNumber, $IssueDate, $Fare, $SegmentAdditionalInfo,
	$ValidatingAirline, $CorporateCode, $TourCode, $Endorsement, $Remarks, $ServiceFeeDisplayType)
	{
		$data['TicketId'] = $TicketId;
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
		$update_condition = array();
		$update_condition['passenger_fk'] = intval($passenger_fk);
		
		
		$this->custom_db->update_record('flight_passenger_ticket_info', $data, $update_condition);
	}
		/**
	 * Save Baggage Information
	 * @param unknown_type $passenger_fk
	 * @param unknown_type $from_airport_code
	 * @param unknown_type $to_airport_code
	 * @param unknown_type $description
	 * @param unknown_type $price
	 */
	public function save_passenger_baggage_info($passenger_fk, $from_airport_code, $to_airport_code, $description, $price, $baggage_id)
	{
		$data = array();
		$data['passenger_fk'] = $passenger_fk;
		$data['from_airport_code'] = $from_airport_code;
		$data['to_airport_code'] = $to_airport_code;
		$data['description'] = $description;
		$data['price'] = $price;
		$data['baggage_id'] = $baggage_id;
		
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
	public function save_passenger_meals_info($passenger_fk, $from_airport_code, $to_airport_code, $description, $price, $meal_id, $type)
	{
		$data = array();
		$data['passenger_fk'] = $passenger_fk;
		$data['from_airport_code'] = $from_airport_code;
		$data['to_airport_code'] = $to_airport_code;
		$data['description'] = $description;
		$data['price'] = $price;
		$data['meal_id'] = $meal_id;
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
	public function save_passenger_seat_info($passenger_fk, $from_airport_code, $to_airport_code, $description, $price, $seat_id, $type, $airline_code, $flight_number)
	{
		$data = array();
		$data['passenger_fk'] = $passenger_fk;
		$data['from_airport_code'] = $from_airport_code;
		$data['to_airport_code'] = $to_airport_code;
		$data['description'] = $description;
		$data['price'] = $price;
		$data['seat_id'] = $seat_id;
		$data['type'] = $type;
		$data['airline_code'] = $airline_code;
		$data['flight_number'] = $flight_number;
		
		$this->custom_db->insert_record('flight_booking_seat_details', $data);
	}
	function airline_deals($id){
		$cols = 'AL.name,AL.code,ADS.business,ADS.economy,ADS.import_fee';
		$query = 'select '.$cols.' from airline_deal_sheet as ADS 
				left join airline_list As AL on ADS.airline_origin = AL.origin 
				where ADS.domain_id ='.$id.' AND ADS.business >0 AND ADS.economy >0 AND ADS.import_fee >0 order by AL.name ASC ';
			
		//echo $query;exit;
		return $this->db->query($query)->result_array();
	}
	/**
	 * Jaganath
	 * @param $id
	 */
	function airline_commission_details($id){
		$cols = 'DL.domain_name,BFCD.value, BFCD.api_value,BFCD.value_type';
		$query = 'select '.$cols.' from domain_list DL
					left join b2b_flight_commission_details as BFCD on DL.origin = BFCD.domain_list_fk 
				where DL.origin ='.intval($id);
		return $this->db->query($query)->result_array();
	}
	/**
	 * 
	 * @param unknown $app_reference
	 * @return string
	 */
	function check_details_update_pnr($app_reference){
		$response ['status'] = FAILURE_STATUS;
		$response ['data'] = array ();
		
		$bd_query = 'select BD.*,DL.domain_name,DL.origin as domain_id from flight_booking_details AS BD,domain_list AS DL WHERE DL.origin = BD.domain_origin AND BD.app_reference like ' . $this->db->escape ( $app_reference );
		
		//Itinerary Details
		$id_query = 'select * from flight_booking_itinerary_details AS ID WHERE ID.app_reference='.$this->db->escape($app_reference);
		//Transaction Details
		$td_query = 'select * from flight_booking_transaction_details AS CD WHERE CD.app_reference='.$this->db->escape($app_reference);
		//Customer and Ticket Details
		$cd_query = 'select CD.*,FPTI.TicketId,FBTD.sequence_number,FPTI.TicketNumber,FPTI.IssueDate,FPTI.Fare,FPTI.SegmentAdditionalInfo
						from flight_booking_passenger_details AS CD
						left join flight_passenger_ticket_info FPTI on CD.origin=FPTI.passenger_fk				
				        left join flight_booking_transaction_details FBTD on FBTD.origin=CD.flight_booking_transaction_details_fk						
				        WHERE CD.flight_booking_transaction_details_fk IN
						(select TD.origin from flight_booking_transaction_details AS TD
						WHERE TD.app_reference ='.$this->db->escape($app_reference).' order by TD.sequence_number desc)';
		//Cancellation Details
		$cancellation_details_query = 'select FCD.*
						from flight_booking_passenger_details AS CD
						left join flight_cancellation_details AS FCD ON FCD.passenger_fk=CD.origin
						WHERE CD.flight_booking_transaction_details_fk IN
						(select TD.origin from flight_booking_transaction_details AS TD
						WHERE TD.app_reference ='.$this->db->escape($app_reference).')';
		
		$response['data']['booking_details']			= $this->db->query($bd_query)->result_array();
		
		$response['data']['booking_itinerary_details']	= $this->db->query($id_query)->result_array();
		$response['data']['booking_transaction_details']	= $this->db->query($td_query)->result_array();
		$response['data']['booking_customer_details']	= $this->db->query($cd_query)->result_array();
		$response['data']['cancellation_details']	= $this->db->query($cancellation_details_query)->result_array();
		if (valid_array($response['data']['booking_details']) == true and valid_array($response['data']['booking_itinerary_details']) == true and valid_array($response['data']['booking_customer_details']) == true) {
			$response['status'] = SUCCESS_STATUS;
		}
		return $response;
	}
	/**
	 * Jaganath
	 * Checks AppReference Exists or Not? 
	 * @param $app_reference
	 */
	function is_app_reference_exists($app_reference)
	{
		$booking_details = $this->custom_db->single_table_records('flight_booking_details', '*', array('app_reference' => trim($app_reference)));
		if($booking_details['status'] == true){
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Jaganath
	 * Returns Passenger Ticket Details based on the following parameteres
	 * @param unknown_type $app_reference
	 * @param unknown_type $sequence_number
	 * @param unknown_type $booking_id
	 * @param unknown_type $pnr
	 * @param unknown_type $ticket_id
	 */
	function get_passenger_ticket_info($app_reference, $sequence_number, $booking_id, $pnr, $ticket_id)
	{
		$response['status'] = FAILURE_STATUS;
		$response['data'] = array();
		//Booking Details
		$bd_query = 'select BD.*,DL.domain_name,DL.origin as domain_id from flight_booking_details AS BD,domain_list AS DL 
						WHERE DL.origin = BD.domain_origin AND BD.app_reference like ' . $this->db->escape ( $app_reference );
		//Itinerary Details
		$id_query = 'select * from flight_booking_itinerary_details AS ID 
						WHERE ID.app_reference='.$this->db->escape($app_reference);
		//Transaction Details
		$td_query = 'select TD.* from flight_booking_transaction_details AS TD 
					WHERE TD.app_reference='.$this->db->escape($app_reference).' and TD.sequence_number='.trim($sequence_number).'
					and TD.pnr='.$this->db->escape($pnr).' and TD.book_id='.$this->db->escape($booking_id);
		//Customer and Ticket Details
		$cd_query = 'select CD.*,FPTI.TicketId,FPTI.TicketNumber,FPTI.IssueDate,FPTI.Fare,FPTI.SegmentAdditionalInfo
						from flight_booking_passenger_details AS CD
						left join flight_passenger_ticket_info FPTI on CD.origin=FPTI.passenger_fk
						WHERE FPTI.TicketId='.trim($ticket_id).' and CD.flight_booking_transaction_details_fk IN
						(select TD.origin from flight_booking_transaction_details AS TD
						WHERE TD.app_reference ='.$this->db->escape($app_reference).'
						 and TD.sequence_number='.trim($sequence_number).'
						and TD.pnr='.$this->db->escape($pnr).' and TD.book_id='.$this->db->escape($booking_id).')';
		//Cancellation Details
		$cancellation_details_query = 'select FCD.*
						from flight_booking_passenger_details AS CD
						left join flight_passenger_ticket_info FPTI on CD.origin=FPTI.passenger_fk
						left join flight_cancellation_details AS FCD ON FCD.passenger_fk=CD.origin
						WHERE  FPTI.TicketId='.trim($ticket_id).' and CD.flight_booking_transaction_details_fk IN
						(select TD.origin from flight_booking_transaction_details AS TD
						WHERE TD.app_reference ='.$this->db->escape($app_reference).' 
						and TD.sequence_number='.trim($sequence_number).'
						and TD.pnr='.$this->db->escape($pnr).' and TD.book_id='.$this->db->escape($booking_id).')';
                
	
		$response['data']['booking_details']			= $this->db->query($bd_query)->result_array();
		$response['data']['booking_itinerary_details']	= $this->db->query($id_query)->result_array();
		$response['data']['booking_transaction_details']	= $this->db->query($td_query)->result_array();
		$response['data']['booking_customer_details']	= $this->db->query($cd_query)->result_array();
		$response['data']['cancellation_details']	= $this->db->query($cancellation_details_query)->result_array();
		if (valid_array($response['data']['booking_details']) == true and valid_array($response['data']['booking_itinerary_details']) == true and 
			valid_array($response['data']['booking_customer_details']) == true and valid_array($response['data']['booking_transaction_details']) == true) {
			$response['status'] = SUCCESS_STATUS;
		}
		return $response;
	}
	/**
	 * Jganath
	 * @param unknown_type $AppReference
	 * @param unknown_type $SequenceNumber
	 * @param unknown_type $BookingId
	 * @param unknown_type $PNR
	 * @param unknown_type $TicketId
	 * @param unknown_type $ChangeRequestId
	 * @param unknown_type $cancellation_details
	 */
	public function update_cancellation_details($AppReference, $SequenceNumber, $BookingId, $PNR, $TicketId, $ChangeRequestId, $cancellation_details)
	{
		//Get Passenger Ticket Details
		$passenger_ticket_info = $this->get_passenger_ticket_info($AppReference, $SequenceNumber, $BookingId, $PNR, $TicketId);
		if($passenger_ticket_info['status'] == true){
			$passenger_ticket_info = $passenger_ticket_info['data'];
			$booking_details = $passenger_ticket_info['booking_details'][0];
			$booking_itinerary_details = $passenger_ticket_info['booking_itinerary_details'];
			$booking_transaction_details = $passenger_ticket_info['booking_transaction_details'][0];
			$booking_customer_details = $booking_customer_details = $passenger_ticket_info['booking_customer_details'][0];
			//1.Update Passenger Status and Add Cancellation details
			$passenger_origin = $booking_customer_details['origin'];
			$this->update_pax_ticket_cancellation_details($cancellation_details, $passenger_origin);
			//2.Update Transaction details
			$transaction_origin = $booking_transaction_details['origin'];
			$this->update_flight_booking_transaction_cancel_status($transaction_origin);
			//3.Update the Master Booking Status
			$this->update_flight_booking_cancel_status($AppReference);
		}
	}
	/*
	 * Jaganath
	 * Update the Cancellation Details of the Passenger
	 */
	function update_pax_ticket_cancellation_details($cancellation_details, $passenger_origin)
	{
		$data = array();
		//1.Updating Passenger Status
		$booking_status = 'BOOKING_CANCELLED';
		$passenger_update_data = array();
		$passenger_update_data['status'] = $booking_status;
		$passenger_update_condition = array();
		$passenger_update_condition['origin'] = $passenger_origin;
		$this->custom_db->update_record('flight_booking_passenger_details', $passenger_update_data, $passenger_update_condition);
		//2.Adding Cancellation Details
		$cancellation_details = $cancellation_details['data']['TicketCancellationtDetails'];
		//Add/Update Pax Cancellation Details
		//StatusCode:NotSet = 0,Unassigned = 1,Assigned = 2,Acknowledged = 3,Completed = 4,Rejected = 5,Closed = 6,Pending = 7,Other = 8
		if($cancellation_details['ChangeRequestStatus'] == 4) {
			$data['cancellation_processed_on'] = date('Y-m-d H:i:s');
		}
		$data['RequestId'] = 				$cancellation_details['ChangeRequestId'];
		$data['API_RefundedAmount'] = 		$cancellation_details['RefundedAmount'];
		$data['API_CancellationCharge'] = 	$cancellation_details['CancellationCharge'];
		$data['API_ServiceTaxOnRefundAmount'] =	$cancellation_details['ServiceTaxOnRefundAmount'];
		$data['API_SwachhBharatCess'] = 		$cancellation_details['SwachhBharatCess'];
		$data['API_KrishiKalyanCess'] = 		(isset($cancellation_details['KrishiKalyanCess']) ? $cancellation_details['KrishiKalyanCess'] : 0);
		
		$data['ChangeRequestStatus'] = 		$cancellation_details['ChangeRequestStatus'];
		$data['statusDescription'] = 		$cancellation_details['StatusDescription'];
		$data['current_status'] = 			$cancellation_details['ChangeRequestStatus'];
		$pax_cancel_details_exists = $this->custom_db->single_table_records('flight_cancellation_details', '*', array('passenger_fk' => $passenger_origin));
		if($pax_cancel_details_exists['status'] == true) {
			//Update the Data
			$this->custom_db->update_record('flight_cancellation_details', $data, array('passenger_fk' => $passenger_origin));
		} else {
			//Insert Data
			$data['passenger_fk'] = $passenger_origin;
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
	 * Update the Old Booking App Reference
	 */
	public function update_old_booking_app_reference($new_app_reference, $BookingId, $PNR, $domain_origin)
	{
		return true;
		//TODO: Need to check For Domestic RoundWay
		$new_booking_data = $this->db->query('select * from flight_booking_details where app_reference='.$this->db->escape($new_app_reference))->row_array();
		if(valid_array($new_booking_data)==true){
			$update_app_reference = false;
		} else{
			$update_app_reference = true;
		}
		if($update_app_reference == true){//If its old booking update AppReference
			//Get master Details
			$master_booking_details = $this->db->query('select FBD.app_reference,FBD.booking_source from flight_booking_details FBD 
								join flight_booking_transaction_details FBTD on FBTD.app_reference=FBD.app_reference
								where FBD.domain_origin='.intval($domain_origin).' and FBTD.book_id='.$this->db->escape($BookingId).' 
									and FBTD.pnr='.$this->db->escape($PNR))->row_array();
			$old_app_reference = trim($master_booking_details['app_reference']);
			$booking_source = trim($master_booking_details['booking_source']);
			//$booking_details = $this->get_booking_details($master_app_reference, $booking_source);
			//UPDATE DATA
			$update_data['app_reference'] = $new_app_reference;
			//UPDATE CONDITIOn
			$update_condition['app_reference'] = $old_app_reference;
			//1.update flight_booking_details
			$this->custom_db->update_record('flight_booking_details', $update_data, $update_condition);
			//2.update flight_booking_itinerary_details
			$this->custom_db->update_record('flight_booking_itinerary_details', $update_data, $update_condition);
			//3.update flight_booking_transaction_details
			$this->custom_db->update_record('flight_booking_transaction_details', $update_data, $update_condition);
			//4.update flight_booking_passenger_details
			$this->custom_db->update_record('flight_booking_passenger_details', $update_data, $update_condition);
			//5.update transaction_log
			$this->custom_db->update_record('transaction_log', $update_data, $update_condition);
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
		$bd_query = 'select BD.*,DL.domain_name,DL.origin as domain_id from flight_booking_details AS BD,domain_list AS DL WHERE DL.origin = BD.domain_origin AND BD.app_reference like ' . $this->db->escape ( $app_reference );
		if (empty($booking_source) == false) {
			$bd_query .= '	AND BD.booking_source = '.$this->db->escape($booking_source);
		}
		if (empty($booking_status) == false) {
			$bd_query .= ' AND BD.status = '.$this->db->escape($booking_status);
		}
		//Itinerary Details
		$id_query = 'select * from flight_booking_itinerary_details AS ID WHERE ID.app_reference='.$this->db->escape($app_reference);
		//Transaction Details
		$td_query = 'select TD.*,BS.name as booking_api_name from flight_booking_transaction_details AS TD
					left join booking_source BS on BS.source_id=TD.booking_source 
					WHERE TD.app_reference='.$this->db->escape($app_reference);
		//Customer and Ticket Details
		$cd_query = 'select CD.*,FPTI.TicketId,FPTI.TicketNumber,FPTI.IssueDate,FPTI.Fare,FPTI.SegmentAdditionalInfo
						from flight_booking_passenger_details AS CD
						left join flight_passenger_ticket_info FPTI on CD.origin=FPTI.passenger_fk
						WHERE CD.flight_booking_transaction_details_fk IN
						(select TD.origin from flight_booking_transaction_details AS TD
						WHERE TD.app_reference ='.$this->db->escape($app_reference).' order by TD.sequence_number desc)';
		//Cancellation Details
		$cancellation_details_query = 'select FCD.*
						from flight_booking_passenger_details AS CD
						left join flight_cancellation_details AS FCD ON FCD.passenger_fk=CD.origin
						WHERE CD.flight_booking_transaction_details_fk IN
						(select TD.origin from flight_booking_transaction_details AS TD
						WHERE TD.app_reference ='.$this->db->escape($app_reference).')';
	
		$response['data']['booking_details']			= $this->db->query($bd_query)->result_array();
		
		$response['data']['booking_itinerary_details']	= $this->db->query($id_query)->result_array();
		$response['data']['booking_transaction_details']	= $this->db->query($td_query)->result_array();
		$response['data']['booking_customer_details']	= $this->db->query($cd_query)->result_array();
		$response['data']['cancellation_details']	= $this->db->query($cancellation_details_query)->result_array();
		if (valid_array($response['data']['booking_details']) == true and valid_array($response['data']['booking_itinerary_details']) == true and valid_array($response['data']['booking_customer_details']) == true) {
			$response['status'] = SUCCESS_STATUS;
		}
		//echo debug($response['data']['booking_customer_details']);exit;
		return $response;
	}
	/**
	 * For Updated Version
	 * Read Individual booking details - dont use it to generate table
	 * @param $app_reference
	 * @param $booking_source
	 * @param $booking_status
	 */
	function get_flight_booking_transaction_details($app_reference, $sequence_number, $booking_source='', $booking_status='')
	{
		$response['status'] = FAILURE_STATUS;
		$response['data'] = array();
		//Booking Details
		$bd_query = 'select BD.*,DL.domain_name,DL.origin as domain_id from flight_booking_details AS BD,domain_list AS DL WHERE DL.origin = BD.domain_origin AND BD.app_reference like ' . $this->db->escape ( $app_reference );
		if (empty($booking_status) == false) {
			$bd_query .= ' AND BD.status = '.$this->db->escape($booking_status);
		}
		//Transaction Details
		$td_query = 'select TD.*,CAST(TD.status AS UNSIGNED) as status_code,BS.name as booking_api_name from flight_booking_transaction_details AS TD 
					left join booking_source BS on BS.source_id=TD.booking_source
					WHERE TD.app_reference='.$this->db->escape($app_reference).' AND TD.sequence_number='.intval($sequence_number);
		if (empty($booking_source) == false) {
			$td_query .= '	AND TD.booking_source = '.$this->db->escape($booking_source);
		}
		$booking_transaction_details	= $this->db->query($td_query)->result_array();
		$flight_booking_transaction_details_origin = intval(@$booking_transaction_details[0]['origin']);
                
		//Itinerary Details
		$id_query = 'select ID.* from flight_booking_itinerary_details AS ID WHERE ID.app_reference='.$this->db->escape($app_reference).' AND ID.flight_booking_transaction_details_fk='.$flight_booking_transaction_details_origin;
		
		//Customer and Ticket Details
		$cd_query = 'select CD.*,FPTI.TicketId,FPTI.TicketNumber,FPTI.IssueDate,FPTI.Fare,FPTI.SegmentAdditionalInfo
						from flight_booking_passenger_details AS CD
						left join flight_passenger_ticket_info FPTI on CD.origin=FPTI.passenger_fk
						WHERE CD.flight_booking_transaction_details_fk='.$flight_booking_transaction_details_origin;
		//Cancellation Details
		$cancellation_details_query = 'select FCD.*
						from flight_booking_passenger_details AS CD
						left join flight_cancellation_details AS FCD ON FCD.passenger_fk=CD.origin
						WHERE CD.flight_booking_transaction_details_fk='.$flight_booking_transaction_details_origin;
	
		$response['data']['booking_details']			= $this->db->query($bd_query)->result_array();
		$response['data']['booking_transaction_details']	= $booking_transaction_details;
		$response['data']['booking_itinerary_details']	= $this->db->query($id_query)->result_array();
		$response['data']['booking_customer_details']	= $this->db->query($cd_query)->result_array();
		$response['data']['cancellation_details']	= $this->db->query($cancellation_details_query)->result_array();
		if (valid_array($response['data']['booking_details']) == true and valid_array($response['data']['booking_transaction_details']) == true and valid_array($response['data']['booking_itinerary_details']) == true and valid_array($response['data']['booking_customer_details']) == true) {
			$response['status'] = SUCCESS_STATUS;
		}
		return $response;
	}
		/**
	 * Extraservices(Baggage,Meal and Seats) Price
	 * @param unknown_type $app_reference
	 */
	public function add_extra_service_price_to_published_fare($app_reference, $sequence_number)
	{
		$transaction_data = $this->db->query('select * from flight_booking_transaction_details where app_reference="'.$app_reference.'" and sequence_number='.$sequence_number)->row_array();
		if(valid_array($transaction_data) == true){
			$transaction_origin = $transaction_data['origin'];
			$extra_service_totla_price = $this->transaction_wise_extra_service_total_price($transaction_origin);
			
			$update_data = array();
			$update_condition = array();
			$update_data['total_fare'] = $transaction_data['total_fare']+$extra_service_totla_price;
			$update_condition['origin'] = $transaction_origin;
			$this->custom_db->update_record('flight_booking_transaction_details', $update_data, $update_condition);
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
        
        
        /*
         * Domain Commission with basic fare and YQ
         *  Only for Traveloprt API
         */
	public function check_commision($carrier, $is_domestic, $booking_source){
		if($is_domestic == 1){
			$module_type = 'domestic';
		}
		else{
			$module_type = 'international';
		}
		$query = 'select* from b2b_flight_commission_details_new WHERE module_type="'.$module_type.'" and booking_source="'.$booking_source.'" and airline_code="'.$carrier.'"';
		
		$data  = $this->db->query($query)->row();
		
		if(!empty($data)){
			return $data;
		}
		else{
			$query1 = 'select* from b2b_flight_commission_details_new WHERE type="generic" ';
			$data  = $this->db->query($query1)->row();
			return $data;
		}
		
		// echo $td_query;exit;
	}
	 function get_tp_active_airline_list($domain_origin, $is_domestic){
    	$active_airline_list = 'SELECT * FROM TP_active_airlinelist where domain_origin = "'.$domain_origin.'" and is_domestic ="'.$is_domestic.'"';
    	
    	return $active_airline_list = $this->db->query($active_airline_list)->result_array();
    }
    public function get_airport_timezone_offset($airport_code,$journey_date)
	{
		//FIXME: cache the data
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
	public function check_commision_goair($carrier, $is_domestic, $booking_source, $domain_origin){

		if($is_domestic == 1){
			$module_type = 'domestic';
		}
		else{
			$module_type = 'international';
		}
		$query = 'select* from b2b_flight_commission_details_new WHERE module_type="'.$module_type.'" and booking_source="'.$booking_source.'" and airline_code="'.$carrier.'" and domain_list_fk="'.$domain_origin.'"';
		
		
		$data  = $this->db->query($query)->row();
		
		if(valid_array($data) && ($data->e_basic_plus_yq_value != '0.00' || $data->b_basic_plus_yq_value != '0.00')){
			return $data;
		}
		else{
			$query1 = 'select* from b2b_flight_commission_details_new WHERE type="generic" ';
			
			$data  = $this->db->query($query1)->row();
			return $data;
		}
		
		// echo $td_query;exit;
	}
	function get_goair_pice_keys($search_id){
		$query = "select * from  goair_pricekeys where search_id LIKE '".$search_id."%'" ;
		
		$data  = $this->db->query($query)->result_array();
		return $data;
		//debug($data);exit;
	}
}
