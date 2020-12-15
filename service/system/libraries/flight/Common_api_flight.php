<?php
require_once BASEPATH . 'libraries/Common_Api_Grind.php';
/**
 * Provab Common Functionality For API Class
 *
 *
 * @package Provab
 * @subpackage provab
 * @category Libraries
 * @author Arjun J<arjun.provab@gmail.com>
 * @link http://www.provab.com
 */
abstract class Common_Api_Flight extends Common_Api_Grind {
	function __construct($module='', $api='',$config_table_origin='') {
		parent::__construct ( $module, $api ,$config_table_origin);
	}
	static $app_reference = false;
	
	/**
	 *
	 * @param string $journey_number
	 *        	//onward, return ,multi-city
	 * @param string $origin_code
	 *        	origin airport code
	 * @param string $destination_code
	 *        	destination airport code
	 * @param
	 *        	date string $departure_dt Y-m-d H:i:s
	 * @param
	 *        	date string $arrival_dt Y-m-d H:i:s
	 * @param string $operator_code
	 *        	//operator code like 9W for Jet Airways
	 * @param string $operator_name
	 *        	//flight operator name like Jet Airways
	 * @param string $flight_number
	 *        	//flight number
	 * @param number $no_of_stops
	 *        	// no of stops in journey
	 * @param string $cabin_class
	 *        	//class of journey like ECONOMY
	 * @param string $origine_name
	 *        	// origin city airport name
	 * @param string $destination_name
	 *        	// destination city airport name
	 * @param number $duration
	 *        	// journey duration in seconds
	 */
	protected function format_summary_array($journey_number, $origin_code, $destination_code, $departure_dt, $arrival_dt, $operator_code, $operator_name, $flight_number, $no_of_stops, $cabin_class = '', $origine_name = '', $destination_name = '', $duration = '', $is_leg = true, $attr = array(), $stop_over, $org_terminal = '', $des_terminal = '', $carrier_code = '') {
		$CI = & get_instance ();
		
		$dts = strtotime ( $departure_dt );
		$ats = strtotime ( $arrival_dt );
		
		$departure_time = date ( 'H:i', $dts );
		$arrival_time = date ( 'H:i', $ats );
		
		$org_loc_details = $CI->db_cache_api->get_airport_details ( $origin_code);
		$des_loc_details = $CI->db_cache_api->get_airport_details ( $destination_code);
		
		if (! isset ( $origine_name ) || empty ( $origine_name )) {
			$origine_name = (empty ( $org_loc_details ['airport_city'] ) ? $origin_code : ($org_loc_details ['airport_city']));
		}
		
		if (! isset ( $destination_name ) || empty ( $destination_name )) {
			$destination_name = (empty ( $des_loc_details ['airport_city'] ) ? $destination_code : ($des_loc_details ['airport_city']));
		}
		$summary_array = array ();
		
		$summary_array ['Origin'] = array (
				'AirportCode' => $origin_code,
				'CityName' => $origine_name,
				'AirportName' => $origine_name,
				'DateTime' => $departure_dt,
				'FDTV' => strtotime ( $departure_time ),
				'OriginTerminal' => $org_terminal, 
		); // Derive
		
		$summary_array ['Destination'] = array (
				'AirportCode' => $destination_code,
				'CityName' => $destination_name,
				'AirportName' => $destination_name,
				'DateTime' => $arrival_dt,
				'FATV' => strtotime ( $arrival_time ),
				'DestinationTerminal' => $des_terminal, 
		); // Derive
		$summary_array ['OperatorCode'] = $operator_code; // Airline code 9w
		$summary_array ['DisplayOperatorCode'] = $carrier_code;
		$summary_array ['OperatorName'] = $operator_name; // Airline name
		$summary_array ['FlightNumber'] = $flight_number;
		$summary_array ['CabinClass'] = $cabin_class;
                $summary_array ['Duration'] = $duration;
		$summary_array ['Attr'] = @$attr;
		$summary_array ['stop_over'] = $stop_over;
		return $summary_array;
	}
	
	/**
	 * Form combination
	 *
	 * @param array $onward        	
	 * @param array $return        	
	 */
	static function domestic_roundway_data($onward, $return) {
		return $this->form_flight_combination ( $onward, $return );
	}
	
		/**
	 * Generate Combination of flights
	 *
	 * @param array $onward        	
	 * @param array $return        	
	 */
	static function form_flight_combination($onward, $return, $trip_type = 'oneway', $search_id) {
		// debug($onward);
		// debug($return);exit;
		$CI = & get_instance ();
		$merge_array = array ();
		$combined_array = array ();
		$onward = force_multple_data_format ( $onward );
		// echo "hererer";

		$jj = 0;
		$return = force_multple_data_format ( $return );
		$flight_data = array();

		for($i = 0; $i < count ( $onward ); $i ++) {

			for($j = 0; $j < count ( $return ); $j ++) {
				$price_array = false;
				if (isset ( $onward [$i] ['Price'] ) == true && valid_array ( $onward [$i] ['Price'] ) == true) {
					$price_array = Common_Api_Flight::combine_price_arr ( $onward [$i]['Price'], $return [$j]['Price'] );
					$merge_array ['Price']['TotalDisplayFare'] = $onward [$i]['Price'] ['TotalDisplayFare']+ $return [$j]['Price']['TotalDisplayFare'];
				}
				
				$passenger_breakup = array ();
				if (isset ( $onward [$i]['Price'] ['PassengerBreakup'] ) && valid_array ( $onward [$i]['Price'] ['PassengerBreakup'] )) {
					$passenger_breakup = Common_Api_Flight::combine_passenger_brekup_arr ( $onward [$i]['Price'], $return [$j]['Price'] );
					// if ($_SERVER ['REMOTE_ADDR'] == '192.168.0.26') {
					// debug($passenger_breakup);debug($onward[$i]);debug($return[$j]);exit;
					// }
				}
				
				$merge_array ['Price']['PriceBreakup'] = $price_array;
				$merge_array ['Price']['PassengerBreakup'] = $passenger_breakup	;
				
				
				$merge_array ['FlightDetails'] ['Details'] = array_merge ( $onward [$i] ['FlightDetails'] ['Details'], $return [$j] ['FlightDetails'] ['Details'] );
				$onward_resul_token = unserialized_data($onward [$i] ['ResultToken']);
				$return_resul_token = unserialized_data($return [$j] ['ResultToken']);
				// $combine_journey_key = array_merge($onward_resul_token[0]['JourneySellKey'], $return_resul_token[0]['JourneySellKey']);
				// $combine_fare_key = array_merge($onward_resul_token[0]['FareSellKey'], $return_resul_token[0]['FareSellKey']);
				// $combine_seg_key = array_merge($onward_resul_token[0]['SegmentSellKey'], $return_resul_token[0]['SegmentSellKey']);
				// $combine_flight = array_merge($onward_resul_token[0]['FlightNumber'], $return_resul_token[0]['FlightNumber']);
				
				// $ResultTokenData[0]['booking_source'] = $onward_resul_token[0]['booking_source'];
				// $ResultTokenData[0]['Signature'] = $onward_resul_token[0]['Signature'];
				// $ResultTokenData[0]['IsLCC'] = $onward_resul_token[0]['IsLCC'];
				// $ResultTokenData[0]['JourneySellKey'] = $combine_journey_key;
				// $ResultTokenData[0]['FareSellKey'] = $combine_fare_key;
				// $ResultTokenData[0]['SegmentSellKey'] = $combine_seg_key;
				// $ResultTokenData[0]['FlightNumber'] = $combine_flight;
				$ResultTokenData = array_merge($onward_resul_token, $return_resul_token);
				
				$merge_array['ResultToken'] = serialized_data($ResultTokenData);
				$merge_array['Attr'] = $onward [$i] ['Attr'];
				// $array_name = '';
				//min price flight
				//One Row Data
                // $FlightDetails = $merge_array['FlightDetails']['Details']; //2 loops
                // $TotalNetFare = floatval($merge_array['Price']['TotalDisplayFare'] - @$merge_array['Price']['PriceBreakup']['AgentCommission'] + @$merge_array['Price']['PriceBreakup']['AgentTdsOnCommision']);
                
              	
                //echo 'eliminate_duplicate_flights',debug($FlightDetails);exit;
                // foreach ($FlightDetails as $fd_k => $fd_v) {
                //     foreach ($fd_v as $flight_k => $flight_v) {
                //         $array_name .= $flight_v['FlightNumber'];
                //         $array_name .= $flight_v['Origin']['AirportCode'];
                //         $array_name .= $flight_v['Destination']['AirportCode'];
                //         $array_name .= $flight_v['Origin']['FDTV'];
                //         $array_name .= $flight_v['Destination']['FATV'];
                //         // $array_name .= $flight_v['CabinClass'];
                //     }
                // }
                // if (isset($flight_data[$array_name]) == true && valid_array($flight_data[$array_name]) == true) {
                //     $Old_TotalNetFare = floatval($flight_data[$array_name]['Price']['TotalDisplayFare'] - @$flight_data[$array_name]['Price']['PriceBreakup']['AgentCommission'] + @$flight_data[$array_name]['Price']['PriceBreakup']['AgentTdsOnCommision']);
                //     if ($TotalNetFare < $Old_TotalNetFare) {//If fare is low, then assign the new flight
                //         $flight_data[$array_name] = $merge_array;
                //     }
                // } else {
                //     $flight_data[$array_name] = $merge_array;
                // }
                // debug($flight_data);
				$combined_array [] = $merge_array;
				// unset($jj);
				$jj++;
			}
		}
		
		$data['results'] = json_encode($combined_array);
		$data['search_id'] = $search_id;
		// $CI->custom_db->delete_record('goair_flight_results', array('search_id' => $search_id));
		// $CI->custom_db->insert_record('goair_flight_results', $data);
		
		return $combined_array;
	}
	static function combine_passenger_brekup_arr($onward, $return) {
		$price_array = array ();
		if (isset ( $onward  ['PassengerBreakup'] ) && isset ( $return ['PassengerBreakup'] )) {
			$price_breakup = array ();
			foreach ( $onward  ['PassengerBreakup'] as $pk => $pv ) {
				$price_breakup [$pk]['BasePrice'] = $pv['BasePrice'] + @$return  ['PassengerBreakup'] [$pk]['BasePrice'];
				$price_breakup [$pk]['Tax'] = $pv['Tax'] + @$return  ['PassengerBreakup'] [$pk]['Tax'];
				$price_breakup [$pk]['TotalPrice'] = $pv['TotalPrice'] + @$return  ['PassengerBreakup'] [$pk]['TotalPrice'];
				$price_breakup [$pk]['PassengerCount'] = $pv['PassengerCount'];
			}
			$price_array = $price_breakup;
		}
	
		return $price_array;
	}
	static function combine_price_arr ($onward, $return) {

		$passenger_array = array (
					'BasicFare' => ($onward ['PriceBreakup']  ['BasicFare'] + $return ['PriceBreakup'] ['BasicFare']),
					'Tax' => ($onward ['PriceBreakup']  ['Tax'] + $return ['PriceBreakup']  ['Tax']),
					'AgentCommission' => ($onward ['PriceBreakup']  ['AgentCommission'] + $return ['PriceBreakup'] ['AgentCommission']),
					'AgentTdsOnCommision' => $onward ['PriceBreakup']  ['AgentTdsOnCommision'] +$return ['PriceBreakup'] ['AgentCommission']
		);
	
		return $passenger_array;
	}
	/**
	 *
	 * @param string $key
	 *        	source_code
	 * @param string $value
	 *        	value of session - login id
	 * @param number $exp_in_secs
	 *        	time for session exp
	 */
	function save_session_id($value, $key = '', $exp_in_secs = 3600) {
		if (empty ( $key ) == true) {
			$key = $this->source_code;
		}
		
		$cookie = array (
				'name' => $key,
				'value' => $value,
				'expire' => '1200',
				'path' => PROJECT_COOKIE_PATH 
		);
		$ci = & get_instance ();
		$ci->input->set_cookie ( $cookie );
	}
	
	/**
	 * Read session
	 *
	 * @param string $key
	 *        	source_code
	 */
	function read_session_id($key = '') {
		if (empty ( $key ) == true) {
			$key = $this->source_code;
		}
		
		$ci = & get_instance ();
		$value = $ci->input->cookie ( $key, true );
		return $value;
	}
	
	/*
	 * Delete session
	 */
	function remove_session($key = '') {
		if (empty ( $key ) == true) {
			$key = $this->source_code;
		}
		delete_cookie ( $key );
	}
	
	/**
	 * Read train booking record
	 *
	 * @param string $app_reference        	
	 */
	function get_flight_book_record($app_reference, $train_filter = array(), $customer_filter = array()) {
		$ci = & get_instance ();
		
		if (valid_array ( $customer_filter ) == true) {
			$customer_filter = $ci->custom_db->get_custom_condition ( $customer_filter );
		} else {
			$customer_filter = '';
		}
		
		if (valid_array ( $train_filter ) == true) {
			$train_filter = $ci->custom_db->get_custom_condition ( $train_filter );
		} else {
			$train_filter = '';
		}
		$flight_query = 'select ID.* from flight_booking_itinerary_details ID
				WHERE ID.app_reference = ' . $ci->db->escape ( $app_reference );
		$flight_data = $ci->db->query ( $flight_query )->result_array ();
		
		$passenger_query = 'select CD.*,
				ACL.iso_country_code AS iso_country_code, ACL.country_code, ACL.name as country_name from flight_booking_passenger_details CD LEFT JOIN api_country_list AS ACL ON CD.passenger_nationality=ACL.iso_country_code where CD.app_reference = ' . $ci->db->escape ( $app_reference ) . ' ' . $customer_filter . ' GROUP BY pax_index';
		$passenger_data = $ci->db->query ( $passenger_query )->result_array ();
		
		$book_query = 'select * from flight_booking_details BD where BD.app_reference = ' . $ci->db->escape ( $app_reference );
		$book_data = $ci->db->query ( $book_query )->row_array ();
		
		$booking_data = '';
		return array (
				'passenger' => $passenger_data,
				'flight' => $flight_data,
				'booking' => $book_data 
		);
	}	
	/**
	 *
	 * @param string $access_key        	
	 * @return string[]
	 */
	function get_fare_details($access_key) {
		$response ['data'] = array ();
		$response ['status'] = FAILURE_STATUS;
		return $response;
	}
	
	/**
	 * update passenger booking status
	 *
	 * @param string $book_id        	
	 * @param number $sindex        	
	 * @param string $status        	
	 */
	function update_passenger_record($book_id, $sindex, $status) {
		$cond ['app_reference'] = $book_id;
		$cond ['segment_indicator'] = $sindex;
		
		$data ['status'] = $status;
		$CI = & get_instance ();
		
		$CI->custom_db->update_record ( 'flight_booking_passenger_details', $data, $cond );
	}
	/**
	 * Checks Booking Source is active or not
	 */
	protected  function is_active_booking_source()
	{
		$data['status'] = SUCCESS_STATUS;
		$data['message'] = '';
		if(valid_array($this->config) == false){
			$data['status'] = FAILURE_STATUS;
			$data['message'] = $this->booking_source.': Booking Source is not active';
		}
		return $data;
	}
}