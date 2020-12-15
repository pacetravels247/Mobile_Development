<?php
require_once BASEPATH . 'libraries/flight/Common_api_flight.php';
class Common_Flight {
	/**
	 * Url to be used for combined flight booking - only for domestic round way
	 *
	 * @param number $search_id        	
	 */
	static function combined_booking_url($search_id) {
		return Common_Api_Flight::pre_booking_url ( $search_id );
	}
	
	/**
	 * Data gets saved in list so remember to use correct source value
	 *
	 * @param string $source
	 *        	source of the data - will be used as key while saving
	 * @param string $value
	 *        	value which has to be cached - pass json
	 */
	static function insert_record($key, $value) {
		$ci = & get_instance ();
		
		$index = $ci->redis_server->store_list ( $key, $value );
		return array (
				'access_key' => $key . DB_SAFE_SEPARATOR . $index . DB_SAFE_SEPARATOR . random_string () . random_string (),
				'index' => $index 
		);
	}
	
	/**
	 */
	static function read_record($key, $offset = -1, $limit = -1) {
		$ci = & get_instance ();
		return $ci->redis_server->read_list ( $key, $offset, $limit );
	}
	
	/**
	 * Cache the data
	 *
	 * @param string $key        	
	 * @param value $value        	
	 * @return array[]
	 */
	static function insert_string($key, $value) {
		$ci = & get_instance ();
		$ci->redis_server->store_string ( $key, $value );
	}
	
	/**
	 * read data from cache
	 *
	 * @param string $key        	
	 * @param number $offset        	
	 * @param number $limit        	
	 */
	static function read_string($key) {
		$ci = & get_instance ();
		return $ci->redis_server->read_string ( $key );
	}
	static function domestic_roundway_data($onward, $return) {
		return Common_Api_Flight::form_flight_combination ( $onward, $return ) [0];
	}
	
	/**
	 *
	 * @param string $temp_booking_id        	
	 */
	public function locate_temp_booking_id($temp_booking_id) {
		$ci = & get_instance ();
		
		$data = $ci->custom_db->single_table_records ( 'tmp_flight_pre_booking_details', 'origin', array (
				'reference_id' => $ci->db->escape ( $temp_booking_id ) 
		) );
		if ($data ['status'] == FAILURE_STATUS) {
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 *
	 * @param
	 *        	$app_reference
	 * @param
	 *        	$booking_source
	 */
	public function validate_temp_booking_id($app_reference, $token, $booking_source, $search_id) {
		// check app reference creation and booking source combination
		$token = unserialized_data ( $token );
		$booking_source = unserialized_data ( $booking_source );
		echo $app_reference;
		exit ();
		debug ( $booking_source );
		debug ( $token );
		exit ();
	}
	/**
	 * Read data and create ssr listing
	 *
	 * @param string $app_reference
	 *        	unique application reference
	 */
	function read_ssr_details($app_reference) {
		$response ['status'] = FAILURE_STATUS;
		$response ['msg'] = '';
		$response ['data'] = array ();
		
		$ci = & get_instance ();
		$details = $ci->flight_model->get_pax_itinerary ( $app_reference );
		
		// can be used by all the api
		Common_Api_Flight::$app_reference = $app_reference;
		if (valid_array ( $details ) == true) {
			$response ['status'] = SUCCESS_STATUS;
			$temp_booking = $this->module_model->unserialize_temp_booking_record ( $app_reference );
			
			// get ssr details and baggage details
			$pax_iti_map = array ();
			foreach ( $details as $k => $passenger ) {
				$lib = load_flight_lib ( $passenger ['booking_source'], '', true );
				$ci->$lib->read_ssr ( $passenger, $temp_booking, $pax_iti_map );
			}
			
			$seat_map = $ci->$lib->seat_map_details ($temp_booking,$details);
//			debug($seat_map);exit;
			$response ['data'] = $pax_iti_map;
			$response ['seatmap'] = @$seat_map['data'];
		}
		return $response;
	}
	
	/*
	 * save flight details
	 */
	function save_flight_booking($flight_data, $passenger_details, $app_reference, $sequence_number, $booking_source, $search_id)
	{
		$ci = & get_instance();
		$data['status'] = SUCCESS_STATUS;
		$data['message'] = '';
		// $porceed_to_save = $this->is_duplicate_flight_booking($app_reference, $sequence_number);
		$porceed_to_save['status'] = SUCCESS_STATUS;
		if($porceed_to_save['status'] != SUCCESS_STATUS){
			$data['status'] = $porceed_to_save['status'];
			$data['message'] = $porceed_to_save['message'];
		} else {
			$search_data = $ci->flight_model->get_safe_search_data ( $search_id );
			$search_data = $search_data['data'];
			$segment_details = $flight_data['FlightDetails']['Details'];
			$fare_details = $flight_data['Price'];
			$fare_breakup = $flight_data['PriceBreakup'];
			$last_segment_details = end(end($segment_details));
			$master_booking_status = 'BOOKING_INPROGRESS';
			
			
			//Save to Master table
			$domain_origin = get_domain_auth_id();
			$flight_booking_status = $master_booking_status;
			$is_lcc = '';
			$currency = domain_base_currency();
			$currency_obj = new Currency(array('module_type' => 'b2c_flight'));
			$currency_conversion_rate = $currency_obj->get_domain_currency_conversion_rate();
			$phone = $passenger_details[0]['ContactNo'];
			$alternate_number = '';
			$email = $passenger_details[0]['Email'];
			$journey_from = is_array($search_data['from']) ? $search_data['from'][0] : $search_data['from'];
			$journey_to = is_array($search_data['to']) ? end($search_data['to']): $search_data['to'];
			$journey_start = $segment_details[0][0]['Origin']['DateTime'];
			$journey_end = $last_segment_details['Destination']['DateTime'];
			$payment_mode = 'PNHB1';
			$booking_details_attributes = array('JourneyAttributes' => $search_data);
			$created_by_id = 0;
			
			$ci->flight_model->save_flight_booking_details ( $domain_origin, $flight_booking_status, $app_reference, $booking_source, $is_lcc, $currency, $phone, $alternate_number, $email, $journey_start, $journey_end, $journey_from, $journey_to, $payment_mode, json_encode($booking_details_attributes), $created_by_id,$currency_conversion_rate, FLIGHT_VERSION_2);
			
			
			//Save to transaction details
			$transaction_status = $master_booking_status;
			$transaction_description = '';
			$pnr = '';
			$booking_id = '';
			$source = '';
			$ref_id = 0;
			$transaction_details_attributes = '';
			$total_fare = 		$fare_details['commissionable_fare'];
			$admin_commission =	$fare_details['admin_commission'];
			$agent_commission =	$fare_details['agent_commission'];
			$admin_tds = 		$fare_details['admin_tds'];
			$agent_tds = 		$fare_details['agent_tds'];
			$domain_markup = 	$fare_details['admin_markup'];
			$transaction_insert_id = $ci->flight_model->save_flight_booking_transaction_details ( $app_reference, $transaction_status, $transaction_description, $pnr, $booking_id, $source, $ref_id, 
									json_encode($transaction_details_attributes), $sequence_number, $total_fare, $domain_markup, $admin_commission, $agent_commission, $admin_tds, $agent_tds, $booking_source, json_encode($fare_breakup));
			$flight_booking_transaction_details_fk = $transaction_insert_id['insert_id'];
			
			//Save Passenger Details
			foreach($passenger_details as $pax_k => $pax_v) {
				$passenger_type = $pax_v['PaxType'];
				$is_lead = $pax_v['IsLeadPax'];
				$title = $pax_v['Title'];
				$first_name = $pax_v['FirstName'];
				$middle_name = '';
				$last_name = $pax_v['LastName'];
				$date_of_birth = $pax_v['DateOfBirth'];
				$gender = ($pax_v['Gender'] == 1 ? 'Male': 'Female');
				$passenger_nationality = $pax_v['CountryName'];
				$passport_number = $pax_v['PassportNumber'];
				$passport_issuing_country = '';
				$passport_expiry_date = $pax_v['PassportExpiry'];
				$status = $master_booking_status;
				//Attributes
				$passenger_attributes = array();
				//Attributes
				$passenger_attributes = array();
				$passenger_insert_id = $ci->flight_model->save_flight_booking_passenger_details($app_reference, $passenger_type, $is_lead, $title, $first_name, $middle_name, $last_name, $date_of_birth, $gender, $passenger_nationality, $passport_number, $passport_issuing_country, $passport_expiry_date, 
				$status, json_encode($passenger_attributes), $flight_booking_transaction_details_fk);
				
				//Passenger Ticket Details
				$passenger_fk = $passenger_insert_id['insert_id'];
				$ci->flight_model->save_passenger_ticket_info($passenger_fk);
				
				//Save ExtraService Details
				$this->save_extra_services($pax_v, $passenger_fk);
			}
			
			//Save Flight Segment Details
			foreach ($segment_details as $segment_k => $segment_v) {
				$curr_segment_indicator = 1;
				foreach($segment_v as $ws_key => $ws_val) {
					$OriginDetails = $ws_val['Origin'];
					$DestinationDetails = $ws_val['Destination'];
					$segment_indicator = ($curr_segment_indicator++);
					$airline_code = 		$ws_val['OperatorCode'];
					$airline_name = 		$ws_val['OperatorName'];
					$flight_number = 		$ws_val['FlightNumber'];
					$fare_class = 			$ws_val['CabinClass'];
					$operating_carrier =	$ws_val['OperatorCode'];
					$from_airport_code = 	$OriginDetails['AirportCode'];
					$from_airport_name = 	$OriginDetails['AirportName'];
					$to_airport_code = 		$DestinationDetails['AirportCode'];
					$to_airport_name = 		$DestinationDetails['AirportName'];
					$departure_datetime = 	$OriginDetails['DateTime'];
					$arrival_datetime = 	$DestinationDetails['DateTime'];
					$iti_status = 			'';
					//Attributes
					$itinerary_attributes['AirlinePNR'] = @$ws_val['AirlinePNR'];
					$itinerary_attributes['Attr'] = $ws_val['Attr'];
					$itinerary_attributes = $itinerary_attributes;
					$ci->flight_model->save_flight_booking_itinerary_details( $app_reference, $segment_indicator, $airline_code, $airline_name, $flight_number, $fare_class, $from_airport_code, $from_airport_name, $to_airport_code, $to_airport_name, $departure_datetime, $arrival_datetime, $iti_status, $operating_carrier, json_encode($itinerary_attributes), $flight_booking_transaction_details_fk);
				}
			}
			
			//Add Extra Service Price to published price
			$ci->flight_model->add_extra_service_price_to_published_fare($app_reference, $sequence_number);
		}
		return $data;
	}
	/**
	 * Save Extra Services
	 * @param unknown_type $passenger_details
	 * @param unknown_type $passenger_fk
	 */
	private function save_extra_services($passenger_details, $passenger_fk)
	{
		//Save Passenger Baggage
		$this->save_passenger_baggage_info($passenger_details, $passenger_fk);
		//Save Passenger Meal
		$this->save_passenger_meals_info($passenger_details, $passenger_fk);
		//Save Passenger Seat
		$this->save_passenger_seat_info($passenger_details, $passenger_fk);
	}
	/**
	 * 
	 * Save Baggage Details
	 * @param unknown_type $passenger_details
	 * @param unknown_type $passenger_fk
	 */
	private function save_passenger_baggage_info($passenger_details, $passenger_fk)
	{
		if(isset($passenger_details['BaggageId']) == true && valid_array($passenger_details['BaggageId']) == true){
			$ci = & get_instance();
			foreach ($passenger_details['BaggageId'] as $bag_k => $bag_v){
				$bag_v = trim($bag_v);
				if(empty($bag_v) == false){
					$baggage_data = Common_Flight::read_record($bag_v);
					if(valid_array($baggage_data) == true){
						$baggage_data = json_decode($baggage_data[0], true);
						$BaggageId = array_values(unserialized_data($baggage_data['BaggageId']));
						$baggage_data['BaggageId'] = $BaggageId[0]['Code'];
						
						//Save passenger baggage information
						$ci->flight_model->save_passenger_baggage_info($passenger_fk, $baggage_data['Origin'], $baggage_data['Destination'], $baggage_data['Weight'], $baggage_data['Price'], $baggage_data['BaggageId']);
					}
				}
			}
		}
	}
	/**
	 * 
	 * Save Meal Details
	 * @param unknown_type $passenger_details
	 * @param unknown_type $passenger_fk
	 */
	private function save_passenger_meals_info($passenger_details, $passenger_fk)
	{
		if(isset($passenger_details['MealId']) == true && valid_array($passenger_details['MealId']) == true){
			$ci = & get_instance();
			foreach ($passenger_details['MealId'] as $meal_k => $meal_v){
				$meal_v = trim($meal_v);
				if(empty($meal_v) == false){
					$meal_data = Common_Flight::read_record($meal_v);
					if(valid_array($meal_data) == true){
						$meal_data = json_decode($meal_data[0], true);
						$MealId = array_values(unserialized_data($meal_data['MealId']));
						$meal_data['MealId'] = $MealId[0]['Code'];
						$type = $MealId[0]['Type'];
						
						//Save passenger meal information
						$ci->flight_model->save_passenger_meals_info($passenger_fk, $meal_data['Origin'], $meal_data['Destination'], $meal_data['Description'], floatval(@$meal_data['Price']), $meal_data['MealId'], $type);
					}
				}
			}
		}
	}
	/**
	 * 
	 * Save Seat Details
	 * @param unknown_type $passenger_details
	 * @param unknown_type $passenger_fk
	 */
	private function save_passenger_seat_info($passenger_details, $passenger_fk)
	{
		if(isset($passenger_details['SeatId']) == true && valid_array($passenger_details['SeatId']) == true){
			$ci = & get_instance();
			foreach ($passenger_details['SeatId'] as $seat_k => $seat_v){
				$seat_v = trim($seat_v);
				if(empty($seat_v) == false){
					$seat_data = Common_Flight::read_record($seat_v);
					
					if(valid_array($seat_data) == true){
						$seat_data = json_decode($seat_data[0], true);
						$SeatId = array_values(unserialized_data($seat_data['SeatId']));
						$seat_data['SeatId'] = $SeatId[0]['Code'];
						$type = $SeatId[0]['Type'];
						if(isset($seat_data['Description'])){
							$sdescription = $seat_data['Description'];
						} else {
							$sdescription = '';
						}
						//Save passenger seat information
						$ci->flight_model->save_passenger_seat_info($passenger_fk, $seat_data['Origin'], $seat_data['Destination'], $sdescription, floatval(@$seat_data['Price']), $seat_data['SeatId'], $type, @$seat_data['AirlineCode'], @$seat_data['FlightNumber']);
					}
				}
			}
		}
	}
	/**
	 * Updates Flight Booking Status
	 * @param unknown_type $flight_booking_status
	 * @param unknown_type $app_reference
	 * @param unknown_type $sequence_number
	 * @param unknown_type $booking_source
	 */
	public function update_flight_booking_status($flight_booking_status, $app_reference, $sequence_number, $booking_source)
	{
		$ci = & get_instance();
		$flight_booking_status = trim($flight_booking_status);
		$app_reference = trim($app_reference); 
		$booking_source = trim($booking_source);
		$master_booking_details = $ci->flight_model->get_booking_details($app_reference);
		if($master_booking_details['status'] == true){
			//get flight_booking_transaction_details
			$flight_booking_transaction_details_condition['app_reference'] =	$app_reference;
			$flight_booking_transaction_details_condition['sequence_number'] =	$sequence_number;
			$flight_booking_transaction_details_condition['booking_source'] =	$booking_source;
			
			$flight_transaction_details = $ci->custom_db->single_table_records('flight_booking_transaction_details', 'origin', $flight_booking_transaction_details_condition);
			if($flight_transaction_details['status'] == true){
				$flight_booking_transaction_details_origin = $flight_transaction_details['data'][0]['origin'];
				//1.flight_booking_transaction_details
				$ci->custom_db->update_record('flight_booking_transaction_details', array('status' => $flight_booking_status), array('origin' => $flight_booking_transaction_details_origin));
				
				//2.flight_booking_passenger_details
				$ci->custom_db->update_record('flight_booking_passenger_details', array('status' => $flight_booking_status), array('flight_booking_transaction_details_fk' => $flight_booking_transaction_details_origin));
				
				//3.flight_booking_details--Master Table
				$master_booking_status = $flight_booking_status;
				//Running Again to get the latest Status
				$master_booking_details = $ci->flight_model->get_booking_details($app_reference);
				$booking_transaction_details = $master_booking_details['data']['booking_transaction_details'];
				
				if(count($booking_transaction_details) == 1){
					$master_booking_status = $booking_transaction_details[0]['status'];
				} else if(count($booking_transaction_details) == 2){
					$onward_booking_status = $booking_transaction_details[0]['status'];
					$return_booking_status = $booking_transaction_details[1]['status'];
					
					if($onward_booking_status == 'BOOKING_CONFIRMED' || $return_booking_status == 'BOOKING_CONFIRMED'){
						$master_booking_status = 'BOOKING_CONFIRMED';
					} else if($onward_booking_status == 'BOOKING_HOLD' || $return_booking_status == 'BOOKING_HOLD'){
						$master_booking_status = 'BOOKING_HOLD';
					} else if($onward_booking_status == 'BOOKING_FAILED' || $return_booking_status == 'BOOKING_FAILED'){
						$master_booking_status = 'BOOKING_FAILED';
					} else {
						$master_booking_status = 'BOOKING_INPROGRESS';
					}
				}
				$ci->custom_db->update_record('flight_booking_details', array('status' => $master_booking_status), array('app_reference' => $app_reference));
			}
		}
	}
	/**
	 * Update Flight Booking Price Details
	 * @param unknown_type $app_reference
	 * @param unknown_type $sequence_number
	 * @param unknown_type $commissionable_fare
	 * @param unknown_type $admin_commission
	 * @param unknown_type $agent_commission
	 * @param unknown_type $admin_tds
	 * @param unknown_type $agent_tds
	 * @param unknown_type $admin_markup
	 */
	public function update_flight_booking_tranaction_price_details($app_reference, $sequence_number, $commissionable_fare, $admin_commission, $agent_commission, $admin_tds, $agent_tds, $admin_markup, $fare_breakup)
	{
		$ci = & get_instance();
		$update_data = array();
		$update_data['total_fare'] = $commissionable_fare;
		$update_data['admin_commission']= $admin_commission;
		$update_data['agent_commission'] = $agent_commission;
		$update_data['admin_tds']= $admin_tds;
		$update_data['agent_tds']= $agent_tds;
		$update_data['domain_markup']= $admin_markup;
		//Fare Breakup
		$update_data['fare_breakup']= json_encode($fare_breakup);
		
		$update_condition = array();
		$update_condition['app_reference'] = $app_reference;
		$update_condition['sequence_number'] = $sequence_number;
		
		$ci->custom_db->update_record('flight_booking_transaction_details', $update_data, $update_condition);
	}
	/**
	 * Update Passenger Ticket Details
	 * @param unknown_type $passenger_fk
	 * @param unknown_type $ticket_number
	 * @param unknown_type $pax_break_down
	 * @param unknown_type $ticket_id
	 */
	public function update_passenger_ticket_info($passenger_fk, $ticket_id, $ticket_number, $pax_break_down = array())
	{
		$update_ticket_data = array();
		$update_ticket_data['TicketId'] = $ticket_id;
		$update_ticket_data['TicketNumber'] = $ticket_number;
		if(valid_array($pax_break_down) == true){
			$update_ticket_data['Fare'] = json_encode($pax_break_down);
		}
		$update_ticket_condition = array();
		$update_ticket_condition['passenger_fk'] = $passenger_fk;
		
		$GLOBALS['CI']->custom_db->update_record('flight_passenger_ticket_info', $update_ticket_data, $update_ticket_condition);
	}
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $app_reference
	 * @param unknown_type $sequence_number
	 */
	public function deduct_flight_booking_amount($app_reference, $sequence_number)
	{
		$ci = & get_instance();
		$condition = array();
		$condition['app_reference'] = $app_reference;
		$condition['sequence_number'] = $sequence_number;
		$data = $ci->db->query('select BD.currency,BD.currency_conversion_rate,FT.* from flight_booking_details BD
						join flight_booking_transaction_details FT on BD.app_reference=FT.app_reference
						where FT.app_reference="'.trim($app_reference).'" and FT.sequence_number='.$sequence_number
						)->row_array();
		if(valid_array($data) == true && in_array($data['status'], array('BOOKING_CONFIRMED')) == true){//Balance Deduction only on Confirmed Booking
			$ci->load->library('booking_data_formatter');
			$transaction_details = $data;
			$agent_buying_price = $ci->booking_data_formatter->agent_buying_price($transaction_details);
			$agent_buying_price = $agent_buying_price[0];
			$domain_booking_attr = array();
			$domain_booking_attr['app_reference'] = $app_reference;
			$domain_booking_attr['transaction_type'] = 'flight';
			//Deduct Domain Balance
			$ci->domain_management->debit_domain_balance($agent_buying_price, Flight::get_credential_type(), get_domain_auth_id(), $domain_booking_attr);//deduct the domain balance
			//Save to Transaction Log
			$domain_markup = $transaction_details['domain_markup'];
			$level_one_markup = 0;
			$agent_transaction_amount = $agent_buying_price-$domain_markup;
			$currency = $transaction_details['currency'];
			$currency_conversion_rate = $transaction_details['currency_conversion_rate'];
			$remarks = 'flight Transaction was Successfully done';
			$ci->domain_management_model->save_transaction_details ( 'flight', $app_reference, $agent_transaction_amount, $domain_markup, $level_one_markup, $remarks, $currency, $currency_conversion_rate);
		}
	}
	/**
	 * Jaganath
	 * Checks is it a duplcaite flight booking
	 */
        
        
         /*
         *  Deduct_flight_booking_amount for HOLD Booking 
         *  Online Hold Booking as we are not deducting amount
         *  Update By Balu
         */
        public function deduct_flight_booking_amount_hold($app_reference, $sequence_number)
	{
           
             //  echo $app_reference;exit;
		$ci = & get_instance();
		$condition = array();
		$condition['app_reference'] = $app_reference;
		$condition['sequence_number'] = $sequence_number;
		$data = $ci->db->query('select BD.currency,BD.domain_origin,BD.currency_conversion_rate,FT.* from flight_booking_details BD
						join flight_booking_transaction_details FT on BD.app_reference=FT.app_reference
						where FT.app_reference="'.trim($app_reference).'" and FT.sequence_number='.$sequence_number
						)->row_array();
                
		if(valid_array($data) == true && in_array($data['status'], array('BOOKING_CONFIRMED')) == true){//Balance Deduction for Hold Booking when Admin upload the PNR and Ticket
			$ci->load->library('booking_data_formatter');
			$transaction_details = $data;
			$agent_buying_price = $ci->booking_data_formatter->agent_buying_price($transaction_details);
                      
			$agent_buying_price = $agent_buying_price[0];
			$domain_booking_attr = array();
			$domain_booking_attr['app_reference'] = $app_reference;
			$domain_booking_attr['transaction_type'] = 'flight';
                        $domain_booking_attr['currency_conversion_rate'] = $transaction_details['currency_conversion_rate'];
			//Deduct Domain Balance
                        
			$ci->domain_management->debit_domain_balance_hold_booking($agent_buying_price, 'live', $data['domain_origin'], $domain_booking_attr);//deduct the domain balance
			//Save to Transaction Log
			$domain_markup = $transaction_details['domain_markup'];
			$level_one_markup = 0;
			$agent_transaction_amount = $agent_buying_price-$domain_markup;
			$currency = $transaction_details['currency'];
			$currency_conversion_rate = $transaction_details['currency_conversion_rate'];
			$remarks = 'flight Transaction was Successfully done';
			$ci->domain_management_model->save_transaction_details ( 'flight', $app_reference, $agent_transaction_amount, $domain_markup, $level_one_markup, $remarks, $currency, $currency_conversion_rate,$data['domain_origin']);
		}
        }
        
        
        
	private function is_duplicate_flight_booking($app_reference, $sequence_number)
	{
		$ci = & get_instance();
		$data['status'] = SUCCESS_STATUS;
		$data['message'] = '';
		$flight_booking_details = $ci->custom_db->single_table_records('flight_booking_transaction_details', '*', array('app_reference' => trim($app_reference), 'sequence_number' => intval($sequence_number)));
		
		if($flight_booking_details['status'] == true && valid_array($flight_booking_details['data'][0]) == true){
			$flight_booking_details = $flight_booking_details['data'][0];
			$pnr = trim($flight_booking_details['pnr']);
			if(empty($pnr) == false){
				$Message = 'Booking Already Done with PNR: '.$pnr;
			} else {
				$Message = 'Duplicate Booking Not Allowed';
			}
			$data['status'] = FAILURE_STATUS;
			$data['message'] = $Message;
		}
		return $data;
	}
	function get_passenger_type_code($type) {
		$code = '';
		switch(strtolower($type)) {
			case 'adult': 
				$code = 'ADT';
				break;
			case 'child':
				$code = 'CHD';
				break;
			case 'infant':
				$code = 'INF';
				break;
		}
		return $code;
	}
	
	/**
	 * Save
	 *
	 * @param number $app_refedrence        	
	 * @param array $data        	
	 */
	function save_ssr_details($app_refedrence, $data) {
		// flight_booking_baggage_details
		// flight_booking_meals_details
		
		// get flight itenary
		// $biorigin = $data['bi_origin'];
		// $biorigin = implode(',',$biorigin);
		// $query = "SELECT * FROM `flight_booking_itinerary_details` WHERE `origin` IN (".$biorigin.")";
		$ci = & get_instance ();
		
		
		//flight wise passenger
		$passenger_list = array();
		if(isset($data['passenger']) && valid_array($data['passenger'])) {
			foreach($data['passenger'] as $p_key => $pass) {
				$passenger_list[$pass['flight_no']][] = $pass;
			}
		}
		//save seat details
		if(isset($data['seat']) && valid_array($data['seat'])) {
			foreach($data['seat'] as $s_key => $seat_arr) {
				foreach($seat_arr as $st_k => $seat) {
					$pass_seat = $passenger_list[$s_key][$st_k];
					$seat_no = $seat;
					$p_origin = $pass_seat['p_origin'];
					$i_origin = $pass_seat['i_origin'];
				
					$seat_array = array(
						'p_origin' => $p_origin,
						'i_origin' => $i_origin,
						'seat' => $seat_no,
						'fare' => 0
					);
					$ci->custom_db->insert_record ( 'flight_booking_seat_details', $seat_array );
				}
			}
		}
		foreach ( $data ['meal_code'] as $k => $v ) {
			if ($v != 'INVALIDIP') {
				$meal ['i_origin'] = $data ['mi_origin'] [$k];
				$meal ['p_origin'] = $data ['mp_origin'] [$k];
				$value = str_replace ( "'", "", $v );
				
				$value = explode ( DB_SAFE_SEPARATOR, $value );
				$meal ['value'] = $value [0];
				$meal ['description'] = $value [1];
				$meal ['fare'] = $value [2];
				$ci->custom_db->insert_record ( 'flight_booking_meals_details', $meal );
			}
		}
		// $combine = array_combine($data['bi_origin'],$data['baggage_code']);
		$prev_code = '';
		foreach ( $data ['baggage_code'] as $k => $v ) {
			if ($v != 'INVALIDIP') {
				$baggage ['i_origin'] = $data ['bi_origin'] [$k];
				$baggage ['p_origin'] = $data ['bp_origin'] [$k];
				$value = str_replace ( "'", "", $v );
				$value = explode ( DB_SAFE_SEPARATOR, $value );
				$baggage ['value'] = $value [0];
				$baggage ['description'] = $value [1];
				$baggage ['fare'] = $value [2];
				$baggage ['is_selected'] = 1;
				
				$prev_code = $v;
				$ci->custom_db->insert_record ( 'flight_booking_baggage_details', $baggage );
			} else if ($prev_code != '') {
				$baggage ['i_origin'] = $data ['bi_origin'] [$k];
				$baggage ['p_origin'] = $data ['bp_origin'] [$k];
				
				$value = str_replace ( "'", "", $prev_code );
				$value = explode ( DB_SAFE_SEPARATOR, $value );
				$baggage ['value'] = $value [0];
				$baggage ['description'] = $value [1];
				$baggage ['fare'] = $value [2];
				$baggage ['is_selected'] = 0;
				
				$ci->custom_db->insert_record ( 'flight_booking_baggage_details', $baggage );
			}
		}
		
		
	}
	
	/**
	 * add price of multiple fare array
	 *
	 * @param array $flight_data        	
	 */
	static function add_fare_details(& $flight_data) {
		if (isset ( $flight_data ['fare'] ) && valid_array ( $flight_data ['fare'] )) {
			$api_total_display_fare = 0;
			$api_total_tax = 0;
			$api_total_fare = 0;
			$total_meal_and_baggage = 0;
			$price_breakup = array ();
			
			foreach ( $flight_data ['fare'] as $_f_key => $__fare ) {
				$api_total_display_fare += $__fare ['api_total_display_fare'];
				$api_total_tax += $__fare ['total_breakup'] ['api_total_tax'];
				$api_total_fare += $__fare ['total_breakup'] ['api_total_fare'];
				
				if (isset ( $__fare ['total_breakup'] ['meal_and_baggage_fare'] )) {
					$total_meal_and_baggage += $__fare ['total_breakup'] ['meal_and_baggage_fare'];
				}
				
				foreach ( $__fare ['price_breakup'] as $p_key => $breakup ) {
					if (isset ( $price_breakup [$p_key] ) == false) {
						$price_breakup [$p_key] = $breakup;
					} else {
						$price_breakup [$p_key] += $breakup;
					}
				}
			}
			$flight_data ['price'] ['api_currency'] = $__fare ['api_currency'];
			$flight_data ['price'] ['api_total_display_fare'] = $api_total_display_fare;
			$flight_data ['price'] ['total_breakup'] ['api_total_tax'] = $api_total_tax;
			$flight_data ['price'] ['total_breakup'] ['api_total_fare'] = $api_total_fare;
			$flight_data ['price'] ['total_breakup'] ['meal_and_baggage_fare'] = $total_meal_and_baggage;
			$flight_data ['price'] ['price_breakup'] = $price_breakup;
		}
	}
	
	/**
	 * update cache key by saving data in cache to be accessed in next page and get markup up update
	 *
	 * @param array $flight_list        	
	 */
	public function update_markup_and_insert_cache_key_to_token($flight_list, $carry_cache_key, $search_id) 
	{
		$ci = & get_instance ();
		$multiplier = $this->get_markup_multiplier($search_id);
                
                $search_data = $ci->flight_blender->search_data($search_id);
		$is_domestic = $search_data['data']['is_domestic'];
                
		$domain_id = get_domain_auth_id();
		$commission_percentage = $ci->domain_management->get_flight_commission($domain_id);
		foreach ( $flight_list as $j_flight => & $j_flight_list ) {
			foreach ( $j_flight_list as $k => & $v ) {
				
                $OperatorCod='';
                if(isset($v['FlightDetails']['Details'][0][0]['OperatorCode'])) {
                  $OperatorCode=$v['FlightDetails']['Details'][0][0];
                }
    			$temp_token = array_values(unserialized_data($v['ResultToken']));
				$booking_source = $temp_token[0]['booking_source'];
				
				//Cache the Data
				$access_data = Common_Flight::insert_record ( $carry_cache_key, json_encode ( $v ) );
				
				//Assiging the Cache Key
				$flight_list[$j_flight] [$k] ['ResultToken'] = $access_data ['access_key'];
				//Update the Markup and Commission
				if(!isset($v['Price'])){
					$v['Price'] = $v['flight_search_data']['Price'];
				}
				if(!isset($v['FlightDetails'])){
					$v['FlightDetails'] = $v['flight_search_data']['FlightDetails'];
				}
				if(!isset($v['ResultToken'])){
					$v['ResultToken'] = $v['flight_search_data']['ResultToken'];
				}
				if(!isset($v['Attr'])){
					$v['Attr'] = $v['flight_search_data']['Attr'];
				}
				//debug($v); exit;
				$this->update_fare_markup_commission($v['Price'], $multiplier, $commission_percentage, true, $booking_source ,$OperatorCode, $is_domestic);
			}
		}

		return $flight_list;
	}
	/**
	 *cache extra services
	 */
	public function cache_extra_services($extra_services, $carry_cache_key) 
	{
		$ci = & get_instance ();
		$currency_obj = new Currency(array('from' => get_application_default_currency() , 'to' => domain_base_currency()));
		//Cache Baggage Details
		if(isset($extra_services['Baggage']) == true && valid_array($extra_services['Baggage']) == true){
			foreach ($extra_services['Baggage'] as $bag_k => $bag_v){
				foreach ($bag_v as $bd_k => $bd_v){
					$access_data = Common_Flight::insert_record ( $carry_cache_key, json_encode ( $bd_v ) );
					$extra_services['Baggage'][$bag_k][$bd_k]['BaggageId'] = $access_data ['access_key'];
					
					//Convert the Price to Domain Currency
					$extra_services['Baggage'][$bag_k][$bd_k]['Price'] = get_converted_currency_value($currency_obj->force_currency_conversion($extra_services['Baggage'][$bag_k][$bd_k]['Price']));
				}
			}
		}
		
		//Cache Meal Details
		if(isset($extra_services['Meals']) == true && valid_array($extra_services['Meals']) == true){
			foreach ($extra_services['Meals'] as $meal_k => $meal_v){
				foreach ($meal_v as $md_k => $md_v){
					$access_data = Common_Flight::insert_record ( $carry_cache_key, json_encode ( $md_v ) );
					$extra_services['Meals'][$meal_k][$md_k]['MealId'] = $access_data ['access_key'];
					
					//Convert the Price to Domain Currency
					$extra_services['Meals'][$meal_k][$md_k]['Price'] = get_converted_currency_value($currency_obj->force_currency_conversion($extra_services['Meals'][$meal_k][$md_k]['Price']));
				}
			}
		}
		if($_SERVER['REMOTE_ADDR'] =='192.168.0.60'){
			if(isset($extra_services['Seat']) == true && valid_array($extra_services['Seat']) == true){
				foreach ($extra_services['Seat'] as $seat_k => $seat_v){
					foreach ($seat_v as $sd_k => $sd_v){
						$Seg_Info = $sd_v['Seat_Info'];
						foreach($Seg_Info as $seat_index => $seat_value){
							$Seat_Info_Details = $seat_value['Seat_Info'];
							foreach ($Seat_Info_Details as $s_key => $s_value) {
								$access_data = Common_Flight::insert_record ( $carry_cache_key, json_encode ( $s_value ) );
								$extra_services['Seat'][$seat_k][$sd_k]['Seat_Info'][$seat_index]['Seat_Info'][$s_key]['SeatId'] = $access_data ['access_key'];
							
								//Convert the Price to Domain Currency
								$extra_services['Seat'][$seat_k][$sd_k]['Seat_Info'][$seat_index]['Seat_Info'][$s_key]['Price'] = get_converted_currency_value($currency_obj->force_currency_conversion($seat_value['Price']));
							}
						}
					}
				}
			}
		}else{
			//Cache Seat Details
			if(isset($extra_services['Seat']) == true && valid_array($extra_services['Seat']) == true){

				foreach ($extra_services['Seat'] as $seat_k => $seat_v){
					foreach ($seat_v as $sd_k => $sd_v){
						foreach($sd_v as $seat_index => $seat_value){
							$access_data = Common_Flight::insert_record ( $carry_cache_key, json_encode ( $seat_value ) );
							$extra_services['Seat'][$seat_k][$sd_k][$seat_index]['SeatId'] = $access_data ['access_key'];
						
							//Convert the Price to Domain Currency
							$extra_services['Seat'][$seat_k][$sd_k][$seat_index]['Price'] = get_converted_currency_value($currency_obj->force_currency_conversion($extra_services['Seat'][$seat_k][$sd_k][$seat_index]['Price']));
						}
					}
				}
			}
		}
		if($_SERVER['REMOTE_ADDR'] =='192.168.0.60'){
			//Cache MealPreference Details
			if(isset($extra_services['MealPreference']) == true && valid_array($extra_services['MealPreference']) == true){
				foreach ($extra_services['MealPreference'] as $mp_key => $mp_value) {
					foreach ($mp_value as $meal_k => $meal_v){
						$meal_list = $meal_v['Meal_Info'];
						foreach ($meal_list as $md_k => $md_v){
							$access_data = Common_Flight::insert_record ( $carry_cache_key, json_encode ( $md_v ) );
							$extra_services['MealPreference'][$mp_key][$meal_k]['Meal_Info'][$md_k]['MealId'] = $access_data ['access_key'];
						}
					}
				}
				
			}
		}else{
			//Cache MealPreference Details
			if(isset($extra_services['MealPreference']) == true && valid_array($extra_services['MealPreference']) == true){
				foreach ($extra_services['MealPreference'] as $meal_k => $meal_v){
					foreach ($meal_v as $md_k => $md_v){
						$access_data = Common_Flight::insert_record ( $carry_cache_key, json_encode ( $md_v ) );
						$extra_services['MealPreference'][$meal_k][$md_k]['MealId'] = $access_data ['access_key'];
					}
				}
			}
		}
		//Cache SeatPreference Details
		if(isset($extra_services['SeatPreference']) == true && valid_array($extra_services['SeatPreference']) == true){
			foreach ($extra_services['SeatPreference'] as $seat_k => $seat_v){
				foreach ($seat_v as $sd_k => $sd_v){
					$access_data = Common_Flight::insert_record ( $carry_cache_key, json_encode ( $sd_v ) );
					$extra_services['SeatPreference'][$seat_k][$sd_k]['SeatId'] = $access_data ['access_key'];
				}
			}
		}
		
		return $extra_services;
	}
	/**
	 * Adding the Markup and Commission
	 */
	private function update_fare_markup_commission(& $FareDetails, $multiplier, $commission_percentage, $domain_currency_conversion, $booking_source, $OperatorCode='', $is_domestic='')
	{
		$ci = & get_instance ();
		
		//calculating Markup and commission
		//debug($FareDetails);
		$total_fare = ($FareDetails['TotalDisplayFare']-$FareDetails['PriceBreakup']['AgentCommission']+$FareDetails['PriceBreakup']['AgentTdsOnCommision']);
		//debug($total_fare);
		$currency_obj = new Currency ( array ('module_type' => 'b2c_flight','from' => get_application_default_currency (),'to' => get_application_default_currency ()) );
        //debug($currency_obj);   
                 # Get Opeartor Airline Code 
                 if(isset($OperatorCode['OperatorCode'])) {
                           $OperatorCode=$OperatorCode['OperatorCode'];
                        }
               
		$markup_price = $currency_obj->get_currency($total_fare, true, true, false, $multiplier, $booking_source, FLIGHT_VERSION_2,$OperatorCode, $is_domestic);
		/*debug($markup_price);
		debug($domain_currency_conversion);
		exit;*/
		$total_markup = ($markup_price['default_value']-$total_fare);

		//Updating Fare Details with Markup
		$FareDetails['TotalDisplayFare'] += 	$total_markup;
		if($total_markup > 0){
			$FareDetails['PriceBreakup']['Tax'] += $total_markup;
		}
		else{
			$FareDetails['PriceBreakup']['BasicFare'] += $total_markup;
		}
		// $FareDetails['PriceBreakup']['Tax'] += $total_markup;
		//$FareDetails['PriceBreakup']['AgentCommission'] = 		round($this->update_agent_commision($FareDetails['PriceBreakup']['AgentCommission'], $commission_percentage), 3);
		$FareDetails['PriceBreakup']['AgentCommission'] = round($FareDetails['PriceBreakup']['AgentCommission'], 2);
		//$FareDetails['PriceBreakup']['AgentTdsOnCommision'] = 		round($currency_obj->calculate_tds($FareDetails['PriceBreakup']['AgentCommission']), 3);
		$FareDetails['PriceBreakup']['AgentTdsOnCommision'] = round($FareDetails['PriceBreakup']['AgentTdsOnCommision'], 2);
		//Updating Passenger Breakdown details
		if(valid_array($FareDetails['PassengerBreakup']) == true) {
			$total_pax_count = array_sum(array_column($FareDetails['PassengerBreakup'], 'PassengerCount'));
			$single_pax_markup = ($total_markup/$total_pax_count);
			foreach($FareDetails['PassengerBreakup'] as $k => $v){
				if($single_pax_markup > 0){
					$FareDetails['PassengerBreakup'][$k]['Tax'] += ($single_pax_markup*$FareDetails['PassengerBreakup'][$k]['PassengerCount']);
				}
				else{
					$FareDetails['PassengerBreakup'][$k]['BasePrice'] += ($single_pax_markup*$FareDetails['PassengerBreakup'][$k]['PassengerCount']);
				}
				//$FareDetails['PassengerBreakup'][$k]['Tax'] += ($single_pax_markup*$FareDetails['PassengerBreakup'][$k]['PassengerCount']);
				$FareDetails['PassengerBreakup'][$k]['TotalPrice'] = ($FareDetails['PassengerBreakup'][$k]['BasePrice']+$FareDetails['PassengerBreakup'][$k]['Tax']);
			}
		}
		//Converting Fare Object to Domain Currency
		$this->convert_to_domain_currency_object($FareDetails, $domain_currency_conversion);
	}
	
	/**
	 * Convert Fare Object to Domain Currency
	 */
	private function convert_to_domain_currency_object(& $FareDetails, $domain_currency_conversion=true)
	{
		if($domain_currency_conversion == true){
			$domain_base_currency = domain_base_currency();
		} else {
			$domain_base_currency = get_application_default_currency();
		}
		// debug($FareDetails);exit;
		// echo $domain_base_currency;exit;
		// $domain_base_currency = 'USD';
		$TotalDisplayFare =	$FareDetails['TotalDisplayFare'];
		$PriceBreakup = 	$FareDetails['PriceBreakup'];
		$PassengerBreakup =	$FareDetails['PassengerBreakup'];
		$currency_obj = new Currency(array('from' => $FareDetails['Currency'] , 'to' => $domain_base_currency));
		// debug($currency_obj);exit;
		//Converting the API Fare Currency to Domain Currency
		//FARE DETAILS
		$FareDetails['Currency'] = 								$domain_base_currency;
		$FareDetails['TotalDisplayFare'] = 						get_converted_currency_value($currency_obj->force_currency_conversion($TotalDisplayFare));
		
		$FareDetails['PriceBreakup']['Tax'] = 				get_converted_currency_value($currency_obj->force_currency_conversion($PriceBreakup['Tax']));
		$FareDetails['PriceBreakup']['BasicFare'] =				get_converted_currency_value($currency_obj->force_currency_conversion($PriceBreakup['BasicFare']));
		$FareDetails['PriceBreakup']['AgentCommission'] = 		get_converted_currency_value($currency_obj->force_currency_conversion($PriceBreakup['AgentCommission']));
		$FareDetails['PriceBreakup']['AgentTdsOnCommision'] = 	get_converted_currency_value($currency_obj->force_currency_conversion($PriceBreakup['AgentTdsOnCommision']));
		
		//PASSENGER BREAKDOWN
		foreach($PassengerBreakup as $pk => $pv){
			$FareDetails['PassengerBreakup'][$pk] = 				$pv;
			$FareDetails['PassengerBreakup'][$pk]['BasePrice'] =	get_converted_currency_value($currency_obj->force_currency_conversion($pv['BasePrice']));
			$FareDetails['PassengerBreakup'][$pk]['Tax'] =			get_converted_currency_value($currency_obj->force_currency_conversion($pv['Tax']));
			$FareDetails['PassengerBreakup'][$pk]['TotalPrice'] =	get_converted_currency_value($currency_obj->force_currency_conversion($pv['TotalPrice']));
		}
		// debug($FareDetails);exit;
	}
	/**
	 * Returns Booking Transaction Amount Details
	 * @param unknown_type $core_price_details
	 */
	public function final_booking_transaction_fare_details($core_price_details, $search_id, $booking_source, $OperatorCode='')
	{
		$ci = & get_instance();
		$multiplier = $this->get_markup_multiplier($search_id);
                
                $search_data = $ci->flight_blender->search_data($search_id);
		$is_domestic = $search_data['data']['is_domestic'];
                
		$domain_id = get_domain_auth_id();
		$commission_percentage = $ci->domain_management->get_flight_commission($domain_id);
		$domain_currency_conversion = false;
		//Update the markup and commission
		$core_commissionable_fare = $core_price_details['TotalDisplayFare'];
		$core_commission = $core_price_details['PriceBreakup']['AgentCommission'];
		$core_commission_on_tds = $core_price_details['PriceBreakup']['AgentTdsOnCommision'];
		//$core_price_details[''] = 
		$this->update_fare_markup_commission($core_price_details, $multiplier, $commission_percentage, $domain_currency_conversion, $booking_source, $OperatorCode, $is_domestic);
		
		$commissionable_fare = $core_price_details['TotalDisplayFare'];
		$agent_commission = $core_price_details['PriceBreakup']['AgentCommission'];
		$agent_tds = $core_price_details['PriceBreakup']['AgentTdsOnCommision'];
		$admin_commission = $core_commission -$agent_commission;
		$admin_tds = $core_commission_on_tds-$agent_tds;
		$admin_markup = ($commissionable_fare - $core_commissionable_fare);
		//Fare Breakups
		$final_booking_transaction_fare_details['PriceBreakup'] = $core_price_details;
		$final_booking_transaction_fare_details['Price'] = array();
		$final_booking_transaction_fare_details['Price']['commissionable_fare'] = $core_commissionable_fare;
		$final_booking_transaction_fare_details['Price']['admin_commission'] = $admin_commission;
		$final_booking_transaction_fare_details['Price']['agent_commission'] = $agent_commission;
		$final_booking_transaction_fare_details['Price']['admin_tds'] = $admin_tds;
		$final_booking_transaction_fare_details['Price']['agent_tds'] = $agent_tds;
		$final_booking_transaction_fare_details['Price']['admin_markup'] = round($admin_markup, 1);
		$final_booking_transaction_fare_details['Price']['passenger_breakup'] = $core_price_details['PassengerBreakup'];
		
		//Client Buying Price
		$final_booking_transaction_fare_details['Price']['client_buying_price'] = floatval($commissionable_fare-$agent_commission+$agent_tds);//admin markup is already included in commissionable  fare
		
		return $final_booking_transaction_fare_details;
	}
	public function get_flight_booking_transaction_details($app_reference, $sequence_number, $booking_source='', $booking_status='')
	{
		$ci = & get_instance();
		$data['status'] = FAILURE_STATUS;
		$data['data'] = '';
		$data['message'] = '';
		$flight_booking_details = $ci->flight_model->get_flight_booking_transaction_details($app_reference, $sequence_number, $booking_source, $booking_status);
		if($flight_booking_details['status'] == SUCCESS_STATUS){
			$flight_booking_details = $flight_booking_details['data'];
			$booking_transaction_details = $flight_booking_details['booking_transaction_details'][0];
			$flight_booking_status_code = $booking_transaction_details['status_code'];
			
			if(in_array($flight_booking_status_code, array(BOOKING_CONFIRMED, BOOKING_HOLD))){
				$status = $flight_booking_status_code;
				$data['data'] = $this->format_flight_booking_details($flight_booking_details);
			} else if($flight_booking_status_code == BOOKING_FAILED){
				$status = $flight_booking_status_code;
				$data['message'] = 'Booking Failed';
			} else if($flight_booking_status_code == BOOKING_CANCELLED){
				$status = $flight_booking_status_code;
				$data['message'] = 'Booking Cancelled';
			} else {//TODO: For other status handle the status Code and Status Mesaages
				$status = FAILURE_STATUS;
			}
			$data['status'] = $status;
		} else {
			$data['message'] = 'Invalid Request';
		}
		return $data;
	}
	/**
	 * Formates Flight Booking Details
	 * @param unknown_type $flight_booking_details
	 */
	private function format_flight_booking_details($flight_booking_details)
	{
		$ci = & get_instance();
		$formatted_flight_booking_details = array();
		$JourneyList = array();
		$Price = array();
		
		//Segment Details
		$booking_itinerary_details = $flight_booking_details['booking_itinerary_details'];
		$booking_transaction_details = $flight_booking_details['booking_transaction_details'][0];
		$booking_customer_details = $flight_booking_details['booking_customer_details'];
		foreach ($booking_itinerary_details as $it_k => $it_v){
			$Origin = array();
			$Destination = array();
			
			$attributes = json_decode($it_v['attributes'], true);
			
			$Origin['AirportCode']= $it_v['from_airport_code'];
			$Origin['CityName']= $it_v['from_airport_name'];
			$Origin['AirportName']= $it_v['from_airport_name'];
			$Origin['DateTime']= $it_v['departure_datetime'];
			$Origin['FDTV']= strtotime($it_v['departure_datetime']);
			$Origin['Terminal']= @$attributes['departure_terminal'];
			
			$Destination['AirportCode']= $it_v['to_airport_code'];
			$Destination['CityName']= $it_v['to_airport_name'];
			$Destination['AirportName']= $it_v['to_airport_name'];
			$Destination['DateTime']= $it_v['arrival_datetime'];
			$Destination['FATV']= strtotime($it_v['arrival_datetime']);
			$Destination['Terminal']= @$attributes['arrival_terminal'];
			
			$JourneyList['FlightDetails']['Details'][0][$it_k]['Origin'] = $Origin;
			$JourneyList['FlightDetails']['Details'][0][$it_k]['Destination'] = $Destination;
			$JourneyList['FlightDetails']['Details'][0][$it_k]['AirlinePNR'] = $it_v['airline_pnr'];
			$JourneyList['FlightDetails']['Details'][0][$it_k]['OperatorCode'] = $it_v['airline_code'];
			$JourneyList['FlightDetails']['Details'][0][$it_k]['DisplayOperatorCode'] = $it_v['operating_carrier'];
			$JourneyList['FlightDetails']['Details'][0][$it_k]['OperatorName'] = $it_v['airline_name'];
			$JourneyList['FlightDetails']['Details'][0][$it_k]['FlightNumber'] = $it_v['flight_number'];
			$JourneyList['FlightDetails']['Details'][0][$it_k]['CabinClass'] = $it_v['fare_class'];
			$JourneyList['FlightDetails']['Details'][0][$it_k]['Attr'] = '';
		}
		//Price Details
		$Price = json_decode($booking_transaction_details['fare_breakup'], true);
		//Converting Fare Object to Domain Currency
		$this->convert_to_domain_currency_object($Price, true);
		
		//Passenger Details
		$passenger_details = array();
		foreach ($booking_customer_details as $pk => $pv){
			$passenger_details[$pk]['PassengerId'] = $pv['origin'];
			if(isset($pv['TicketId'])) {
                $passenger_details[$pk]['TicketId'] = $pv['TicketId'];
            }
			$passenger_details[$pk]['PassengerType'] = $this->get_passenger_type_code($pv['passenger_type']);
			$passenger_details[$pk]['Title'] = $pv['title'];
			$passenger_details[$pk]['FirstName'] = $pv['first_name'];
			$passenger_details[$pk]['LastName'] = $pv['last_name'];
			$passenger_details[$pk]['PassportNumber'] = $pv['passport_number'];
			$passenger_details[$pk]['TicketNumber'] = $pv['TicketNumber'];
		}
		//Assigning Details
		$formatted_flight_booking_details['BookingDetails']['BookingId'] = $booking_transaction_details['book_id'];
		$formatted_flight_booking_details['BookingDetails']['PNR'] = $booking_transaction_details['airline_pnr'];
                $formatted_flight_booking_details['BookingDetails']['GDSPNR'] = $booking_transaction_details['pnr'];
		$formatted_flight_booking_details['BookingDetails']['PassengerDetails'] = $passenger_details;
		$formatted_flight_booking_details['BookingDetails']['JourneyList'] = $JourneyList;
		$formatted_flight_booking_details['BookingDetails']['Price'] = $Price;
		$formatted_flight_booking_details['BookingDetails']['Attr'] = '';
		return $formatted_flight_booking_details;
	}
	/**
	 * Converts CalendarFare details to Domain Currency
	 * @param unknown_type $FareDetails
	 */
	function update_calendarfare_currency($FareDetails)
	{
		$CalendarFareDetails = array();
		$currency_obj = new Currency(array('from' => get_application_default_currency() , 'to' => domain_base_currency()));
		foreach($FareDetails as $k => $v){
			//Converting the API Fare Currency to Domain Currency
			$CalendarFareDetails[$k] = $v;
			$CalendarFareDetails[$k]['Fare'] = 			get_converted_currency_value($currency_obj->force_currency_conversion($v['Fare']));
			$CalendarFareDetails[$k]['BaseFare'] = 		get_converted_currency_value($currency_obj->force_currency_conversion($v['BaseFare']));
			$CalendarFareDetails[$k]['Tax'] = 			get_converted_currency_value($currency_obj->force_currency_conversion($v['Tax']));
			$CalendarFareDetails[$k]['OtherCharges'] = 	get_converted_currency_value($currency_obj->force_currency_conversion($v['OtherCharges']));
			$CalendarFareDetails[$k]['FuelSurcharge'] =	get_converted_currency_value($currency_obj->force_currency_conversion($v['FuelSurcharge']));
		}
		return $CalendarFareDetails;
	}
	/**
	 * FIXME: do it for plus and percentage
	 * Updates Agents Commission
	 * @param unknown_type $amount
	 */
	private function update_agent_commision($amount, $commission_percentage)
	{
		return (($amount * $commission_percentage) / 100);
	}
	/**
	 * Returns Markup Multiflier for Flight
	 * @param unknown_type $search_id
	 */
	private function get_markup_multiplier($search_id)
	{
		$ci = & get_instance ();
		$search_data = $ci->flight_model->get_safe_search_data ( $search_id );
		$search_data = $search_data ['data'];
		$multiplier = $search_data ['total_pax'];
		if ($search_data ['trip_type'] == 'return' && $search_data ['is_domestic'] === false) {//International Round Way
			$total_pax = $search_data ['total_pax'];
			$multiplier = $multiplier * 2; // 2 for roundway (2 trip)
		} else if($search_data ['trip_type'] == 'multicity'){//Multicity
			$way_count = intval(count($search_data['from']));
			$multiplier = $multiplier * $way_count;
		}
		return $multiplier;
	}
	
	/**
	 * calculate markup
	 *
	 * @param string $markup_type        	
	 * @param float $markup_val        	
	 * @param float $total_fare        	
	 * @param int $multiplier        	
	 */
	private function calculate_markup($markup_type, $markup_val, $total_fare, $multiplier) {
		if ($markup_type == 'percentage') {
			$markup_amount = (floatval ( $total_fare ) * floatval ( $markup_val )) / 100;
			$markup_amount = number_format ( $markup_amount, 3, '.', '' );
		} else {
			$markup_amount = ($multiplier * floatval ( $markup_val ));
			$markup_amount = number_format ( $markup_amount, 3, '.', '' );
		}
		return $markup_amount;
	}

	function get_airline_list() {
		$CI = & get_instance ();
		$code_list = $CI->db->get_where ( 'airline_list', array (
				'is_duplicate' => 0 
		) )->result_array ();
		return $code_list;
	}
	
	/**
	 * get airline class name based on class code
	 *
	 * @param
	 *        	$class_name
	 */
	function get_airline_class_label($class_name) {
		$label = '';
		if (isset ( $class_name )) {
			switch ($class_name) {
				case 'C' :
					$label = 'Business Class';
					break;
				case 'W' :
					$label = 'Premium Economy';
					break;
				case 'Y' :
					$label = 'Economy';
					break;
				case 'P' :
					$label = 'Premium First Class';
					break;
				case 'F' :
					$label = 'First Class';
					break;
				case 'Y' :
					$label = 'Coach class';
					break;
			}
		}
		return $label;
	}
	
	/*
	 * Returns the next highest integer value by rounding up value
	 */
	static function get_round_price($price) {
		$price_val = ceil ( $price );
		return $price_val;
	}
	
	/**
	 *
	 * @param array $active_booking_source_list        	
	 * @param array $safe_search_data        	
	 */
	function filter_booking_source($active_booking_source_list, $safe_search_data) {
		$active_source = array ();
		$booking_source = array ();
		
		if (valid_array ( $active_booking_source_list )) {
			foreach ( $active_booking_source_list as $a_key => $source ) {
				$active_source [] = $source ['source_id'];
			}
		}
		
		if (isset ( $safe_search_data ['carrier'] [0] ) && ! empty ( $safe_search_data ['carrier'] [0] )) {
			$carrier = $safe_search_data ['carrier'] [0];
			if (isset ( $safe_search_data ['i_class'] ) && $safe_search_data ['is_domestic'] == 1) {
				// domestic flight
				$class = $safe_search_data ['i_class'];
			} else {
				$class = @$safe_search_data ['v_class'];
			}
			
			/*
			 * if (strcasecmp ( $carrier, 'GDS' ) == 0) {
			 * // set only gds
			 * if (in_array ( TRAVELPORT_FLIGHT, $active_source )) {
			 * $active_source = array (
			 * TRAVELPORT_FLIGHT
			 * );
			 * }
			 * } else if (strcasecmp ( $carrier, 'lcc' ) == 0) {
			 * // remove gds
			 * if (in_array ( TRAVELPORT_FLIGHT, $active_source )) {
			 * $index = array_search ( TRAVELPORT_FLIGHT, $active_source );
			 * unset ( $active_source [$index] );
			 * }
			 * }
			 */
			if ((strcasecmp ( $carrier, 'lcc' ) == 0 || $carrier == '6E') && $class == 'All') {
				if (in_array ( INDIGO_FLIGHT, $active_source )) {
					$booking_source [] ['source_id'] = INDIGO_FLIGHT;
				}
				if (in_array ( INDIGO_SCRAP_2, $active_source )) {
					$booking_source [] ['source_id'] = INDIGO_SCRAP_2;
				}
			} else if ((strcasecmp ( $carrier, 'lcc' ) == 0 || $carrier == 'SG') && $class == 'All') {
				if (in_array ( SPICEJET_FLIGHT, $active_source )) {
					$booking_source [] ['source_id'] = SPICEJET_FLIGHT;
				}
				if (in_array ( SPICEJET_SCRAP_1, $active_source )) {
					$booking_source [] ['source_id'] = SPICEJET_SCRAP_1;
				}
			} else if ((strcasecmp ( $carrier, 'lcc' ) == 0 || $carrier == 'G8')) {
				if (in_array ( GOAIR_FLIGHT, $active_source )) {
					$booking_source [] ['source_id'] = GOAIR_FLIGHT;
				}
			} else if ((strcasecmp ( $carrier, 'lcc' ) == 0 || $carrier == 'G9') && $class == 'All') {
				if (in_array ( AIRARABIA_FLIGHT, $active_source )) {
					$booking_source [] ['source_id'] = AIRARABIA_FLIGHT;
				}
			} else if ($carrier == 'all' && $class == 'All') {
				$booking_source = $active_booking_source_list;
			} else if (($carrier != '6E' && $carrier != 'SG' && $carrier != 'G9' && $carrier != 'G8') || strcasecmp ( $carrier, 'gds' ) == 0) {
				if (in_array ( TRAVELPORT_FLIGHT, $active_source )) {
					$booking_source [] ['source_id'] = TRAVELPORT_FLIGHT;
				}
			} else {
			}
		} else {
			$booking_source = $active_booking_source_list;
		}
		return $booking_source;
	}
	/**
	 * Returns Single Pax Breakdown
	 * @param unknown_type $passenger_fare_breakdown
	 */
	public function get_single_pax_fare_breakup($passenger_fare_breakdown)
	{
		$single_pax_fare_breakup = array();
		foreach ($passenger_fare_breakdown as $k => $v){
			$PassengerCount = $v['PassengerCount'];
			$single_pax_fare_breakup[$k]['BasePrice'] = 	($v['BasePrice']/$PassengerCount);
			$single_pax_fare_breakup[$k]['Tax'] = 			($v['Tax']/$PassengerCount);
			$single_pax_fare_breakup[$k]['TotalPrice'] =	($v['TotalPrice']/$PassengerCount);
		}
		return $single_pax_fare_breakup;
	}
	/**
	 * Checks the ticket is elgible for cancellation
	 * @param unknown_type $app_reference
	 * @param unknown_type $sequence_number
	 * @param unknown_type $is_full_booking_cancel
	 * @param unknown_type $booking_source
	 */
	public function elgible_for_ticket_cancellation($app_reference, $sequence_number, $ticket_ids, $is_full_booking_cancel, $booking_source)
	{
		$ci = & get_instance ();
		$response  = array();
		$response ['status'] = FAILURE_STATUS;
		$response ['data'] = array();
		$response ['message'] = '';
		$booking_details = $ci->flight_model->get_flight_booking_transaction_details($app_reference, $sequence_number, $booking_source);
		if($booking_details['status'] == SUCCESS_STATUS){
			$booking_details = $booking_details['data'];
			$booking_transaction_details = $booking_details['booking_transaction_details'][0];
			$booking_itinerary_details = $booking_details['booking_itinerary_details'][0];
			$booking_customer_details = $booking_details['booking_customer_details'];
			
			$flight_booking_transaction_details_origin = $booking_transaction_details['origin'];
			//Checking Travel Date
			$travel_date = $booking_itinerary_details['departure_datetime'];
			$travel_date = strtotime($travel_date);
			
			// $is_full_booking_cancel = false;//remove later
			
			if($travel_date >= time()){
				if($is_full_booking_cancel == true){
					$temp_pax_data = $ci->custom_db->single_table_records('flight_booking_passenger_details', 'origin, status', array('flight_booking_transaction_details_fk' => $flight_booking_transaction_details_origin, 'status' => 'BOOKING_CANCELLED'));
					if($temp_pax_data['status'] == FAILURE_STATUS){
						$response ['status'] = SUCCESS_STATUS;
					} else {
						//Even if single ticket cancelled, return the failure status
						$response ['message'] = 'Cancellation Failed';
					}
				} else {
					//Indexing passenger origin with status
					$index_passenger_orign = array();
                                        
                                  if($booking_source==TRAVELPORT_FLIGHT_BOOKING_SOURCE)
                                  {
                                      foreach ($booking_customer_details as $pax_k => $pax_v){
						$index_passenger_orign[$pax_v['TicketId']] = $pax_v['status'];
					}
                                  }
                                  else
                                  {
                                      foreach ($booking_customer_details as $pax_k => $pax_v){
						$index_passenger_orign[$pax_v['origin']] = $pax_v['status'];
					}
                                  }
                                        
					/*foreach ($booking_customer_details as $pax_k => $pax_v){
						$index_passenger_orign[$pax_v['origin']] = $pax_v['status'];
					}*/
					//Checking the individual ticket status
					$ticket_status = SUCCESS_STATUS;
					foreach ($ticket_ids as $k => $v){
						if(isset($index_passenger_orign[$v]) == true && $index_passenger_orign[$v] == 'BOOKING_CONFIRMED'){
							$ticket_status = SUCCESS_STATUS;
						} else {
							$ticket_status = FAILURE_STATUS;
							break;
						}
					}
					if($ticket_status == SUCCESS_STATUS){
						$response ['status'] = SUCCESS_STATUS;
					} else {
						$response ['message'] = 'Cancellation Failed';
					}
				}
			} else {
				$response ['message'] = 'Cancellation Failed !! Journey Date is over';
			}
		} else {
			$response ['message'] = 'AppReference is Not Valid';
		}
		return $response;
	}
	/**
	 * Update Ticket Cancel Status
	 * @param unknown_type $app_reference
	 * @param unknown_type $sequence_number
	 * @param unknown_type $passenger_origin
	 */
	public function update_ticket_cancel_status($app_reference, $sequence_number, $passenger_origin)
	{
		$ci = & get_instance();
		//1.Updating Passenger Status
		$booking_status = 'BOOKING_CANCELLED';
		$passenger_update_data = array();
		$passenger_update_data['status'] = $booking_status;
		$passenger_update_condition = array();
		$passenger_update_condition['origin'] = $passenger_origin;
		$ci->custom_db->update_record('flight_booking_passenger_details', $passenger_update_data, $passenger_update_condition);
		
		//2.Update Transaction details
		$temp_data = $ci->custom_db->single_table_records('flight_booking_passenger_details', 'flight_booking_transaction_details_fk', array('origin' => $passenger_origin));
		$transaction_origin = $temp_data['data'][0]['flight_booking_transaction_details_fk'];
		$ci->flight_model->update_flight_booking_transaction_cancel_status($transaction_origin);
		//3.Update the Master Booking Status
		$ci->flight_model->update_flight_booking_cancel_status($app_reference);
	}
	/**
	 * 
	 * Cancellation Requested Passenger Details
	 * @param unknown_type $booking_customer_details
	 * @param unknown_type $passenger_origins
	 */
	public function get_cancellation_reequested_pax_details($booking_customer_details, $passenger_origins)
	{
		$cancellation_reequested_pax_details = array();
		//Indexing passenger origin with status
		$index_passenger_orign = array();
		foreach ($booking_customer_details as $pax_k => $pax_v){
			$index_passenger_orign[$pax_v['origin']] = $pax_v;
		}
		foreach ($passenger_origins as $k => $v){
			if(isset($index_passenger_orign[$v]) == true){
				$cancellation_reequested_pax_details[$k] = $index_passenger_orign[$v];
			}
		}
		return $cancellation_reequested_pax_details;
	}
}
