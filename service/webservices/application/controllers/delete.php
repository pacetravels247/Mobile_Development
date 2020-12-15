<?php
require_once BASEPATH . 'libraries/flight/Common_api_flight.php';
abstract class Navitaire extends Common_Api_Flight {
	protected $signature; // Used to store signature returned after logon
	protected $operator;
	protected $source_code;
	protected $run_round_trip_special = false;
	protected $is_special_request = false;
	protected $carrier_code = '';
	var $seat_assignment_mode = 'AutoDetermine';
	function __construct($booking_source_code) {
//		 if ($_SERVER ['REMOTE_ADDR'] != '192.168.0.26') {debug($_SERVER);exit;
//		 exit ();
//		 }
		parent::__construct ( META_AIRLINE_COURSE, $booking_source_code );
		$CI = &get_instance ();
		$CI->load->library ( 'converter' );
		if (empty ( $this->signature ) == true) {
			$this->signature = $this->read_session_id ( $this->source_code );
			if (empty ( $this->signature ) == TRUE) {
				$this->login ();
			}
		}
	}
	/**
	 *
	 * @ERROR!!!
	 *
	 * @see Common_Api_Flight::get_fare_details()
	 */
	function get_fare_details($access_key) {
		$response ['data'] = array ();
		
		$CI = & get_instance ();
		$data = $CI->custom_db->single_table_records ( 'flight_fare_rules', 'fare_rule', array (
				'operator' => $this->carrier_code 
		) );
		$response ['data'] = $data ['data'] [0] ['fare_rule'];
		$response ['status'] = SUCCESS_STATUS;
		return $response;
	}
	/**
	 * get create session RQ for api
	 */
	private function login_request() {
		$request ['payload'] = '
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:web="http://schemas.navitaire.com/WebServices" xmlns:ses="http://schemas.navitaire.com/WebServices/ServiceContracts/SessionService" xmlns:ses1="http://schemas.navitaire.com/WebServices/DataContracts/Session">
   <soapenv:Header>
      <web:ContractVersion>' . $this->config ['contract_version'] . '</web:ContractVersion>
   </soapenv:Header>
   <soapenv:Body>
      <ses:LogonRequest>
         <ses:logonRequestData>
            <ses1:DomainCode>' . $this->config ['agent_domain'] . '</ses1:DomainCode>
            <ses1:AgentName>' . $this->config ['agent_id'] . '</ses1:AgentName>
            <ses1:Password>' . $this->config ['password'] . '</ses1:Password>
            <ses1:LocationCode xsi:nil="true" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"/>
            <ses1:RoleCode xsi:nil="true" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"/>
            <ses1:TerminalInfo xsi:nil="true" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"/>
         </ses:logonRequestData>
      </ses:LogonRequest>
   </soapenv:Body>
</soapenv:Envelope>';
		
		$request ['url'] = $this->config ['end_point'] ['session'];
		$request ['soap_action'] = 'http://schemas.navitaire.com/WebServices/ISessionManager/Logon';
		// debug($request);exit;
		return $request;
	}
	
	/**
	 * Soap Header to be used
	 */
	private function soap_header() {
		$header = '';
		$header .= '<s:Header>
			<h:ContractVersion xmlns:h="http://schemas.navitaire.com/WebServices">' . $this->config ['contract_version'] . '</h:ContractVersion>
			<h:Signature xmlns:h="http://schemas.navitaire.com/WebServices">' . $this->signature . '</h:Signature>
		</s:Header>';
		return $header;
	}
	
	/**
	 * Fare type filter
	 *
	 * @param array $special_fare_request        	
	 * @return string
	 */
	protected function fare_request_type($special_fare_request) {
		$request = '';
		$request .= '<FareTypes	xmlns:a="http://schemas.microsoft.com/2003/10/Serialization/Arrays">
						<a:string>R</a:string>
						<a:string>F</a:string>
						<a:string>HB</a:string>
					</FareTypes>';
		if ($this->master_search_data ['domestic_round_trip'] == true && $this->run_round_trip_special == true) {
			$request .= '
			<ProductClasses	xmlns:a="http://schemas.microsoft.com/2003/10/Serialization/Arrays">
				<a:string>N</a:string>
				<a:string>R</a:string>
			</ProductClasses>';
		} else {
			$request .= '
			<ProductClasses i:nil="true" xmlns:a="http://schemas.microsoft.com/2003/10/Serialization/Arrays"></ProductClasses>';
		}
		
		$request .= '<FareClasses i:nil="true" xmlns:a="http://schemas.microsoft.com/2003/10/Serialization/Arrays"></FareClasses>';
		return $request;
	}
	
	/**
	 * Search RQ
	 */
	private function search_request($search_data, $special_fare_request = false) {
		$travels = array ();
		$locs = array ();
		$from = (! valid_array ( $search_data ['from_loc'] )) ? array (
				$search_data ['from_loc'] 
		) : $search_data ['from_loc'];
		$to = (! valid_array ( $search_data ['to_loc'] )) ? array (
				$search_data ['to_loc'] 
		) : $search_data ['to_loc'];
		$start = (! valid_array ( $search_data ['depature'] )) ? array (
				$search_data ['depature'] 
		) : $search_data ['depature'];
		if ($search_data ['trip_type'] == 'oneway') {
			$travels [] = array (
					'from' => $from [0],
					'to' => $to [0],
					'start' => $start [0] 
			);
		} elseif ($search_data ['trip_type'] == 'circle') {
			$travels [] = array (
					'from' => $from [0],
					'to' => $to [0],
					'start' => $start [0] 
			);
			$travels [] = array (
					'from' => $to [0],
					'to' => $from [0],
					'start' => $search_data ['return'] 
			);
		} else {
			// multicity
			
			foreach ( $from as $tk => $tv ) {
				$travels [] = array (
						'from' => $from [$tk],
						'to' => $to [$tk],
						'start' => $start [$tk] 
				);
			}
			// $dates = $search_data ['departure'];
		}
		$curr_code = get_application_transaction_currency_preference ();
		if (empty ( $curr_code )) {
			$curr_code = 'INR';
		}
		
		$advanced_filters = '
			<FlightType>All</FlightType>
			<PaxCount>' . $search_data ['ac_total_pax'] . '</PaxCount>
			<Dow>Daily</Dow>
			<CurrencyCode>' . $curr_code . '</CurrencyCode>
			<DisplayCurrencyCode i:nil="true"></DisplayCurrencyCode>
			<DiscountCode i:nil="true"></DiscountCode>
			<PromotionCode i:nil="true"></PromotionCode>
			<AvailabilityType>Default</AvailabilityType>
			<SourceOrganization i:nil="true"></SourceOrganization>
			<MaximumConnectingFlights>10</MaximumConnectingFlights>
			<AvailabilityFilter>ExcludeUnavailable</AvailabilityFilter>
			<FareClassControl>Default</FareClassControl>
			<MinimumFarePrice>0</MinimumFarePrice>
			<MaximumFarePrice>0</MaximumFarePrice>
			<ProductClassCode i:nil="true"></ProductClassCode>
			<SSRCollectionsMode>None</SSRCollectionsMode>
			<InboundOutbound>None</InboundOutbound>
			<NightsStay>0</NightsStay>
			<IncludeAllotments>false</IncludeAllotments>
			<BeginTime i:nil="true"></BeginTime>
			<EndTime i:nil="true"></EndTime>
			<DepartureStations i:nil="true" xmlns:a="http://schemas.microsoft.com/2003/10/Serialization/Arrays"></DepartureStations>
			<ArrivalStations i:nil="true" xmlns:a="http://schemas.microsoft.com/2003/10/Serialization/Arrays"></ArrivalStations>
			' . $this->fare_request_type ( $special_fare_request );
		
		$pax_config = '';
		
		if ($search_data ['adult_config'] > 0) {
			$pax_config .= '<PaxPriceType>
								<PaxType>ADT</PaxType>
								<PaxDiscountCode i:nil="true"></PaxDiscountCode>
							</PaxPriceType>';
		}
		
		if ($search_data ['child_config'] > 0) {
			$pax_config .= '<PaxPriceType>
								<PaxType>CHD</PaxType>
								<PaxDiscountCode i:nil="true"></PaxDiscountCode>
							</PaxPriceType>';
		}
		
		/*
		 * if ($search_data ['infant_config'] > 0) {
		 * $pax_config .= str_repeat ( '<PaxPriceType>
		 * <PaxType>INF</PaxType>
		 * <PaxDiscountCode i:nil="true"></PaxDiscountCode>
		 * </PaxPriceType>', $search_data ['infant_config'] );
		 * }
		 */
		
		$AvailabilityRequest = '';
		foreach ( $travels as $k => $v ) {
			
			$date = date ( 'Y-m-d', strtotime ( $v ['start'] ) ) . 'T00:00:00';
			$AvailabilityRequest .= '
						<AvailabilityRequest>
							<DepartureStation>' . $v ['from'] . '</DepartureStation>
							<ArrivalStation>' . $v ['to'] . '</ArrivalStation>
							<BeginDate>' . $date . '</BeginDate>
							<EndDate>' . $date . '</EndDate>
							<CarrierCode>' . $this->carrier_code . '</CarrierCode>
							<FlightNumber i:nil="true"></FlightNumber>
							' . $advanced_filters . '
							<PaxPriceTypes>
							' . $pax_config . '
							</PaxPriceTypes>
							<JourneySortKeys i:nil="true" xmlns:a="http://schemas.navitaire.com/WebServices/DataContracts/Common/Enumerations"></JourneySortKeys>
							<TravelClassCodes i:nil="true" xmlns:a="http://schemas.microsoft.com/2003/10/Serialization/Arrays"></TravelClassCodes>
							<IncludeTaxesAndFees>false</IncludeTaxesAndFees>
							<FareRuleFilter>Default</FareRuleFilter>
							<LoyaltyFilter>MonetaryOnly</LoyaltyFilter>
						</AvailabilityRequest>';
		}
		
		$request ['payload'] = '<?xml version="1.0" encoding="utf-8"?>
	<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
		' . $this->soap_header () . '
		<s:Body>
			<GetAvailabilityRequest xmlns="http://schemas.navitaire.com/WebServices/ServiceContracts/BookingService">
				<TripAvailabilityRequest xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Booking" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
					<AvailabilityRequests>
						' . $AvailabilityRequest . '
					</AvailabilityRequests>
				</TripAvailabilityRequest>
			</GetAvailabilityRequest>
		</s:Body>
	</s:Envelope>';
		$request ['url'] = $this->config ['end_point'] ['booking'];
		$request ['soap_action'] = 'http://schemas.navitaire.com/WebServices/IBookingManager/GetAvailability';
		$request ['status'] = SUCCESS_STATUS;
		return $request;
	}
	
	/**
	 * Itinerary Price RQ
	 */
	private function price_itinerary_request($search_data, $key_details) {
		$pax_config = '<a:PaxPriceType>';
		if ($search_data ['adult_config'] > 0) {
			$pax_config .= str_repeat ( '<PaxPriceType>
								<PaxType>ADT</PaxType>
								<PaxDiscountCode i:nil="true"></PaxDiscountCode>
							</PaxPriceType>', $search_data ['adult_config'] );
		}
		
		if ($search_data ['child_config'] > 0) {
			$pax_config .= str_repeat ( '<PaxPriceType>
								<PaxType>CHD</PaxType>
								<PaxDiscountCode i:nil="true"></PaxDiscountCode>
							</PaxPriceType>', $search_data ['child_config'] );
		}
		
		/*
		 * if ($search_data ['infant_config'] > 0) {
		 * $pax_config .= str_repeat ( '<PaxPriceType>
		 * <PaxType>INF</PaxType>
		 * <PaxDiscountCode i:nil="true"></PaxDiscountCode>
		 * </PaxPriceType>', $search_data ['infant_config'] );
		 * }
		 */
		
		$pax_config .= '</a:PaxPriceType>';
		
		$journey_sell_keys = '<a:JourneySellKeys>';
		
		// debug($key_details);
		// exit;
		//
		foreach ( $key_details as $k => $v ) {
			$journey_sell_key = $v ['journey_sell_key'];
			$fare_sell_key = '';
			foreach ( $v ['fare'] as $f_k => $f_v ) {
				$fare_sell_key .= $f_v ['fare_sell_key'] . '^';
			}
			$fare_sell_key = substr ( $fare_sell_key, 0, - 1 );
			$journey_sell_keys .= '<a:SellKeyList>
										<a:JourneySellKey>' . $journey_sell_key . '</a:JourneySellKey>
										<a:FareSellKey>' . $fare_sell_key . '</a:FareSellKey>
										<a:StandbyPriorityCode i:nil="true" />
									</a:SellKeyList>';
		}
		$journey_sell_keys .= '</a:JourneySellKeys>';
		
		$request ['payload'] = '<?xml version="1.0" encoding="UTF-8"?>
		<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
			' . $this->soap_header () . '
			<s:Body>
				<PriceItineraryRequest xmlns="http://schemas.navitaire.com/WebServices/ServiceContracts/BookingService">
					<ItineraryPriceRequest xmlns:a="http://schemas.navitaire.com/WebServices/DataContracts/Booking" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
						<a:PriceItineraryBy>JourneyBySellKey</a:PriceItineraryBy>
						<a:TypeOfSale i:nil="true"></a:TypeOfSale>
						<a:SSRRequest i:nil="true"></a:SSRRequest>
						<a:SellByKeyRequest>
							<a:ActionStatusCode>NN</a:ActionStatusCode>
								' . $journey_sell_keys . '
								' . $pax_config . '
							<a:CurrencyCode>' . get_application_default_currency () . '</a:CurrencyCode>
							<a:SourcePOS xmlns:b="http://schemas.navitaire.com/WebServices/DataContracts/Common">
								<b:State>New</b:State>
								<b:AgentCode>' . $this->config ['agent_id'] . '</b:AgentCode>
								<b:OrganizationCode>' . $this->config ['organization_code'] . '</b:OrganizationCode>
								<b:DomainCode>' . $this->config ['agent_domain'] . '</b:DomainCode>
								<b:LocationCode>www</b:LocationCode>
							</a:SourcePOS>
							<a:PaxCount>' . $search_data ['ac_total_pax'] . '</a:PaxCount>
							<a:TypeOfSale i:nil="true" />
							<a:LoyaltyFilter>MonetaryOnly</a:LoyaltyFilter>
							<a:IsAllotmentMarketFare>false</a:IsAllotmentMarketFare>
						</a:SellByKeyRequest>
						<a:PriceJourneyWithLegsRequest i:nil="true" />
					</ItineraryPriceRequest>
				</PriceItineraryRequest>
			</s:Body>
		</s:Envelope>';
		
		$request ['url'] = $this->config ['end_point'] ['booking'];
		$request ['soap_action'] = 'http://schemas.navitaire.com/WebServices/IBookingManager/GetItineraryPrice';
		$request ['status'] = SUCCESS_STATUS;
		return $request;
	}
	
	/**
	 * Sell RQ for flight
	 *
	 * @param string $book_id        	
	 * @param array $temp_booking        	
	 * @return string
	 */
	private function sell_request($book_id, $temp_booking) {
		$JourneySellKeys = '';
		if (isset ( $temp_booking ['flight_det'] ) && valid_array ( $temp_booking ['flight_det'] )) {
			foreach ( $temp_booking ['flight_det'] as $b_source => $keys__ ) { // debug($keys__);
				foreach ( $keys__ as $m_k => $token_k ) {
					$fare_sell_key = '';
					foreach ( $token_k ['FareSellKey'] as $f_k => $f_v ) {
						$fare_sell_key .= $f_v . '^';
					}
					$fare_sell_key = substr ( $fare_sell_key, 0, - 1 );
					
					$JourneySellKeys .= '<a:SellKeyList>
					  <a:JourneySellKey>' . $token_k ['JourneySellKey'] [0] . '</a:JourneySellKey>
					 <a:FareSellKey>' . $fare_sell_key . '</a:FareSellKey>
					  <a:StandbyPriorityCode i:nil="true"/>
					  </a:SellKeyList>';
					
					/*
					 * foreach ( $token_k['FareSellKey'] as $jk => $jv ) {
					 * $JourneySellKeys .= '<a:SellKeyList>
					 * <a:JourneySellKey>' . $token_k ['SegmentSellKey'] [$jk] . '</a:JourneySellKey>
					 * <a:FareSellKey>' . $jv . '</a:FareSellKey>
					 * <a:StandbyPriorityCode i:nil="true"/>
					 * </a:SellKeyList>';
					 * }
					 */
				}
			}
		}
		$pax_cnt = 0;
		$PaxPriceType = '';
		if (isset ( $temp_booking ['passenger_type'] ) && valid_array ( $temp_booking ['passenger_type'] )) {
			foreach ( $temp_booking ['passenger_type'] as $p_key => $passenger ) {
				if ($passenger == 'Infant') {
					$px_type = 'INF';
					continue;
				} else if ($passenger == 'Child') {
					$px_type = 'CHD';
				} else {
					$px_type = 'ADT';
				}
				$pax_cnt ++;
				$PaxPriceType .= '<a:PaxPriceType>
									<a:PaxType>' . $px_type . '</a:PaxType>
									<a:PaxDiscountCode i:nil="true"/>
								</a:PaxPriceType>';
			}
		}
		if (isset ( $this->source_code ) && ! empty ( $this->source_code )) {
			$booking_source = unserialized_data ( $temp_booking ['booking_source'] );
			if (valid_array ( $booking_source )) {
				$key = array_search ( $this->source_code, $booking_source );
				$token_arr = $temp_booking ['token'] [$key];
				if (valid_array ( $token_arr )) {
					foreach ( $token_arr as $t_k => $tkn ) {
						$this->signature = $tkn ['signature'];
					}
				}
			}
		}
		$request ['payload'] = '<?xml version="1.0" encoding="UTF-8"?>
			<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
				' . $this->soap_header () . '
				<s:Body>
					<SellRequest xmlns="http://schemas.navitaire.com/WebServices/ServiceContracts/BookingService">
						<SellRequestData xmlns:a="http://schemas.navitaire.com/WebServices/DataContracts/Booking" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
							<a:SellBy>JourneyBySellKey</a:SellBy>
							<a:SellJourneyByKeyRequest>
								<a:SellJourneyByKeyRequestData>
									<a:ActionStatusCode>NN</a:ActionStatusCode>
									<a:JourneySellKeys>
										' . $JourneySellKeys . '
									</a:JourneySellKeys>
									<a:PaxPriceType>
										' . $PaxPriceType . '
									</a:PaxPriceType>
									<a:CurrencyCode>' . get_application_default_currency () . '</a:CurrencyCode>
									<a:SourcePOS xmlns:b="http://schemas.navitaire.com/WebServices/DataContracts/Common">
										<b:State>New</b:State>
										<b:AgentCode>' . $this->config ['agent_id'] . '</b:AgentCode>
										<b:OrganizationCode>' . $this->config ['organization_code'] . '</b:OrganizationCode>
										<b:DomainCode>' . $this->config ['agent_domain'] . '</b:DomainCode>
										<b:LocationCode>www</b:LocationCode>
									</a:SourcePOS>
									<a:PaxCount>' . $pax_cnt . '</a:PaxCount>
									<a:TypeOfSale i:nil="true"/>
									<a:LoyaltyFilter>MonetaryOnly</a:LoyaltyFilter>
									<a:IsAllotmentMarketFare>false</a:IsAllotmentMarketFare>
									<a:SourceBookingPOS i:nil="true"/>
									<a:PreventOverLap>false</a:PreventOverLap>
								</a:SellJourneyByKeyRequestData>
							</a:SellJourneyByKeyRequest>
							<a:SellJourneyRequest i:nil="true"/>
							<a:SellSSR i:nil="true"/>
							<a:SellFee i:nil="true"/>
						</SellRequestData>
					</SellRequest>
				</s:Body>
			</s:Envelope>';
		$request ['url'] = $this->config ['end_point'] ['booking'];
		$request ['soap_action'] = 'http://schemas.navitaire.com/WebServices/IBookingManager/Sell';
		return $request;
	}
	
	/**
	 *
	 * @param array $flight_data        	
	 */
	function get_ssr_availability_details($flight_data, $temp_booking) {
		$response ['status'] = SUCCESS_STATUS;
		$departure_Date = date ( 'Y-m-d', strtotime ( $flight_data ['departure_datetime'] ) );
		
		if (isset ( $this->source_code ) && ! empty ( $this->source_code )) {
			$booking_source = unserialized_data ( $temp_booking ['book_attributes'] ['booking_source'] );
			if (valid_array ( $booking_source )) {
				$key = array_search ( $this->source_code, $booking_source );
				$token_arr = $temp_booking ['book_attributes'] ['token'] [$key];
				if (valid_array ( $token_arr )) {
					foreach ( $token_arr as $t_k => $tkn ) {
						$this->signature = $tkn ['signature'];
					}
				}
			}
		}
		$request = '﻿<?xml version="1.0" encoding="utf-8"?>
		<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
		   <s:Header>
		      <h:ContractVersion xmlns:h="http://schemas.navitaire.com/WebServices">340</h:ContractVersion>
		      <h:Signature xmlns:h="http://schemas.navitaire.com/WebServices">' . $this->signature . '</h:Signature>
		   </s:Header>
		   <s:Body>
		      <GetSSRAvailabilityForBookingRequest xmlns="http://schemas.navitaire.com/WebServices/ServiceContracts/BookingService">
		         <SSRAvailabilityForBookingRequest xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Booking" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
		            <SegmentKeyList>
		               <LegKey>
		                  <CarrierCode xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Common">' . $flight_data ['airline_code'] . '</CarrierCode>
		                  <FlightNumber xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Common">' . $flight_data ['flight_number'] . '</FlightNumber>
		                  <OpSuffix i:nil="true" xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Common"></OpSuffix>
		                  <DepartureDate>' . $departure_Date . '</DepartureDate>
		                  <DepartureStation>' . $flight_data ['from_airport_code'] . '</DepartureStation>
		                  <ArrivalStation>' . $flight_data ['to_airport_code'] . '</ArrivalStation>
		               </LegKey>
		            </SegmentKeyList>
		            <PassengerNumberList xmlns:a="http://schemas.microsoft.com/2003/10/Serialization/Arrays">
		               <a:short>0</a:short>
		            </PassengerNumberList>
		            <InventoryControlled>true</InventoryControlled>
		            <NonInventoryControlled>true</NonInventoryControlled>
		            <SeatDependent>true</SeatDependent>
		            <NonSeatDependent>true</NonSeatDependent>
		            <CurrencyCode>INR</CurrencyCode>
		         </SSRAvailabilityForBookingRequest>
		      </GetSSRAvailabilityForBookingRequest>
		   </s:Body>
		</s:Envelope>';
		$response ['soap_action'] = 'http://schemas.navitaire.com/WebServices/IBookingManager/GetSSRAvailabilityForBooking';
		$response ['payload'] = $request;
		$response ['url'] = $this->config ['end_point'] ['booking'];
		
		return $response;
	}
	function get_seat_availability_request($flight, $temp_booking) {
		$response = array ();
		$response ['status'] = FAILURE_STATUS;
		if (valid_array ( $flight )) {
			foreach ( $flight as $f_key => $flight_data ) {
				$departure_date = date ( 'Y-m-d', strtotime ( $flight_data ['departure_datetime'] ) );
				$departure_time = date ( 'H:i:s', strtotime ( $flight_data ['departure_datetime'] ) );
				$departure = $departure_date . 'T' . $departure_time;
				
				$origin = $flight_data ['from_airport_code'];
				$destination = $flight_data ['to_airport_code'];
				
				$flight_no = sprintf ( "% 4s", $flight_data ['flight_number'] );
				
				$request = '﻿<?xml version="1.0" encoding="utf-8"?>
					<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
					   <s:Header>
					      <h:ContractVersion xmlns:h="http://schemas.navitaire.com/WebServices">340</h:ContractVersion>
					      <h:Signature xmlns:h="http://schemas.navitaire.com/WebServices">' . $this->signature . '</h:Signature>
					   </s:Header>
					   <s:Body>
					      <GetSeatAvailabilityRequest xmlns="http://schemas.navitaire.com/WebServices/ServiceContracts/BookingService">
					         <SeatAvailabilityRequest xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Booking" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
					            <STD>' . $departure . '</STD>
					            <DepartureStation>' . $origin . '</DepartureStation>
					            <ArrivalStation>' . $destination . '</ArrivalStation>
					            <IncludeSeatFees>true</IncludeSeatFees>
					            <SeatAssignmentMode>' . $this->seat_assignment_mode . '</SeatAssignmentMode>
					            <FlightNumber>' . $flight_no . '</FlightNumber>
					            <CarrierCode>' . $flight_data ['airline_code'] . '</CarrierCode>
					            <OpSuffix i:nil="true"/>
					            <CompressProperties>false</CompressProperties>
					            <EnforceSeatGroupRestrictions>false</EnforceSeatGroupRestrictions>
					            <PassengerIDs xmlns:a="http://schemas.navitaire.com/WebServices/DataContracts/Booking">
					            	<a:long>0</a:long>
					            </PassengerIDs>
					            <PassengerSeatPreferences i:nil="true"/>
					            <SeatGroup>0</SeatGroup>
					            <SeatGroupSettings i:nil="true"/>
					            <EquipmentDeviations i:nil="true"/>
					            <IncludePropertyLookup>false</IncludePropertyLookup>
					            <OverrideCarrierCode i:nil="true"/>
					            <OverrideFlightNumber i:nil="true"/>
					            <OverrideOpSuffix i:nil="true"/>
					            <OverrideSTD>0001-01-01T00:00:00</OverrideSTD>
					            <OverrideDepartureStation i:nil="true"/>
					            <OverrideArrivalStation i:nil="true"/>
					            <CollectedCurrencyCode>INR</CollectedCurrencyCode>
					            <ExcludeEquipmentConfiguration>false</ExcludeEquipmentConfiguration>
					         </SeatAvailabilityRequest>
					      </GetSeatAvailabilityRequest>
					   </s:Body>
					</s:Envelope>';
				
				$response ['payload'] [$flight_data ['airline_code'] . ',' . $flight_data ['flight_number']] = $request;
			}
			$response ['soap_action'] = 'http://schemas.navitaire.com/WebServices/IBookingManager/GetSeatAvailability';
			$response ['url'] = $this->config ['end_point'] ['booking'];
			$response ['status'] = SUCCESS_STATUS;
		}
		return $response;
	}
	
	/**
	 * Build SSR RQ details for journey
	 */
	function ssr_request($book_id, $temp_booking) {
		$request ['status'] = FAILURE_STATUS;
		$segment_ssr_request = '';
		$pax_group = array_count_values ( $temp_booking ['passenger_type'] );
		$infant_count = isset ( $pax_group ['Infant'] ) ? $pax_group ['Infant'] : 0;
		
		$CI = & get_instance ();
		
		// Get Flight Segment details along with pax details
		$itenary_query = 'select ID.origin AS i_origin, ID.is_leg,passenger_type, ID.segment_indicator, ID.airline_code, ID.flight_number, ID.from_airport_code, ID.to_airport_code,
				ID.departure_datetime, ID.arrival_datetime, ID.attributes, PD.pax_index , PD.origin AS p_origin, PD.first_name, PD.last_name, PD.date_of_birth
				from flight_booking_itinerary_details AS ID, flight_booking_passenger_details AS PD
				WHERE ID.app_reference=PD.app_reference AND PD.segment_indicator=ID.segment_indicator AND ID.booking_source = ' . $CI->db->escape ( $this->source_code ) . '
				AND ID.app_reference = ' . $CI->db->escape ( $book_id ) . ' ORDER BY ID.origin, PD.origin';
		$itenary_output = $CI->db->query ( $itenary_query )->result_array ();
		$meal_query = 'SELECT fm.value,fi.attributes,fm.p_origin,i_origin FROM `flight_booking_passenger_details` as fp JOIN flight_booking_itinerary_details as fi ON fp.app_reference = fi.app_reference JOIN flight_booking_meals_details as fm WHERE fp.app_reference = "' . $book_id . '" AND fp.origin = fm.p_origin AND fi.origin = fm.i_origin ';
		$meal_output = $CI->db->query ( $meal_query )->result_array ();
		
		// debug($itenary_output);exit;
		$baggage_query = 'SELECT fm.value,fi.attributes,fm.p_origin,i_origin FROM `flight_booking_passenger_details` as fp JOIN flight_booking_itinerary_details as fi ON fp.app_reference = fi.app_reference JOIN flight_booking_baggage_details as fm WHERE fp.app_reference = "' . $book_id . '" AND fp.origin = fm.p_origin AND fi.origin = fm.i_origin ';
		$baggage_output = $CI->db->query ( $baggage_query )->result_array ();
		
		// meals
		$meal_arr = array ();
		if (isset ( $meal_output ) && valid_array ( $meal_output )) {
			foreach ( $meal_output as $_m_k => $__meal_Arr ) {
				$attributes = unserialize ( $__meal_Arr ['attributes'] );
				$__meal_Arr ['action_status_code'] = $attributes ['action_status_code'];
				$meal_arr [$__meal_Arr ['i_origin'] . '_' . $__meal_Arr ['p_origin']] = $__meal_Arr;
			}
		}
		
		// baggage
		$baggage_arr = array ();
		if (isset ( $baggage_output ) && valid_array ( $baggage_output )) {
			foreach ( $baggage_output as $_m_k => $__bag_Arr ) {
				$attributes = unserialize ( $__bag_Arr ['attributes'] );
				$__bag_Arr ['action_status_code'] = $attributes ['action_status_code'];
				$baggage_arr [$__bag_Arr ['i_origin'] . '_' . $__bag_Arr ['p_origin']] = $__bag_Arr;
			}
		}
		
		// merge with action status code
		$ssr_group = array ();
		$index = 0;
		$itinerary_details = array ();
		$inf_pax_details = array ();
		$p_origin = 0;
		
		// debug($itenary_output);
		foreach ( $itenary_output as $qk => $qv ) {
			$attributes = unserialize ( $qv ['attributes'] );
			$qv ['action_status_code'] = $attributes ['action_status_code'];
			if (intval ( $qv ['is_leg'] ) == 1 && isset ( $itinerary_details [$qv ['i_origin']] ) == false) {
				$itinerary_details [$qv ['i_origin']] = $qv;
				$p_origin = $qv ['i_origin'];
			} else {
				$itinerary_details [$p_origin] ['to_airport_code'] = $qv ['to_airport_code'];
				$itinerary_details [$p_origin] ['arrival_datetime'] = $qv ['arrival_datetime'];
			}
			// itinerary wise pax grouping
			if ($qv ['passenger_type'] == 'Infant' && isset ( $inf_pax_details [$qv ['pax_index']] ) == false) {
				$inf_pax_details [$qv ['pax_index']] = $qv;
			}
		}
		// debug($itinerary_details);exit;
		if (valid_array ( $meal_arr ) == false && valid_array ( $baggage_arr ) == false && valid_array ( $inf_pax_details ) == false) {
			$request ['status'] = FAILURE_STATUS;
		} else {
			$request ['status'] = SUCCESS_STATUS;
			$segment_ssr_request .= '<a:SegmentSSRRequests>';
			// segment has pax
			
			// debug($itinerary_details);
			foreach ( $itinerary_details as $flight_key => $flight_details ) {
				$segment_ssr_request .= '<a:SegmentSSRRequest>
							<a:FlightDesignator	xmlns:b="http://schemas.navitaire.com/WebServices/DataContracts/Common">
								<b:CarrierCode>' . $flight_details ['airline_code'] . '</b:CarrierCode>
								<b:FlightNumber>' . $flight_details ['flight_number'] . '</b:FlightNumber>
								<b:OpSuffix i:nil="true"></b:OpSuffix>
							</a:FlightDesignator>
							<a:STD>' . date ( 'Y-m-d', strtotime ( $flight_details ['departure_datetime'] ) ) . 'T' . date ( 'H:i:s', strtotime ( $flight_details ['departure_datetime'] ) ) . '</a:STD>
							<a:DepartureStation>' . $flight_details ['from_airport_code'] . '</a:DepartureStation>
							<a:ArrivalStation>' . $flight_details ['to_airport_code'] . '</a:ArrivalStation>';
				$segment_ssr_request .= '<a:PaxSSRs>';
				$pax_index = 0;
				foreach ( $inf_pax_details as $pax_key => $pax_value ) {
					$segment_ssr_request .= '<a:PaxSSR>
									<State xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Common">New</State>
									<a:ActionStatusCode>NN</a:ActionStatusCode>
									<a:ArrivalStation>' . $flight_details ['to_airport_code'] . '</a:ArrivalStation>
									<a:DepartureStation>' . $flight_details ['from_airport_code'] . '</a:DepartureStation>
									<a:PassengerNumber>' . ($pax_index ++) . '</a:PassengerNumber>
									<a:SSRCode>INFT</a:SSRCode>
									<a:SSRNumber>0</a:SSRNumber>
									<a:SSRDetail i:nil="true"></a:SSRDetail>
									<a:FeeCode i:nil="true"></a:FeeCode>
									<a:Note i:nil="true"></a:Note>
									<a:SSRValue>0</a:SSRValue>
								</a:PaxSSR>';
				}
				// per segment(flight change) - per pax create ssr for meals and pax dont include infant
				$meal_key = $flight_details ['i_origin'] . '_' . $flight_details ['p_origin'];
				$meal_pax_index = 0;
				foreach ( $itenary_output as $t_key => $iteny ) {
					$meal_key = $iteny ['i_origin'] . '_' . $iteny ['p_origin'];
					if (isset ( $meal_arr [$meal_key] ) && valid_array ( $meal_arr [$meal_key] ) && $flight_details ['i_origin'] == $iteny ['i_origin']) {
						$meal_details_val = $meal_arr [$meal_key];
						if (($iteny ['p_origin'] == $meal_details_val ['p_origin']) && ($iteny ['i_origin'] == $meal_details_val ['i_origin'])) {
							$meal_details_val ['from_airport_code'] = $iteny ['from_airport_code'];
							$meal_details_val ['to_airport_code'] = $iteny ['to_airport_code'];
						}
						
						$segment_ssr_request .= '<a:PaxSSR>
								<State xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Common">New</State>
								<a:ActionStatusCode>NN</a:ActionStatusCode>
								<a:ArrivalStation>' . $meal_details_val ['to_airport_code'] . '</a:ArrivalStation>
								<a:DepartureStation>' . $meal_details_val ['from_airport_code'] . '</a:DepartureStation>
								<a:PassengerNumber>' . ($meal_pax_index ++) . '</a:PassengerNumber>
								<a:SSRCode>' . str_replace ( "'", "", $meal_details_val ['value'] ) . '</a:SSRCode>
								<a:SSRNumber>0</a:SSRNumber>
								<a:SSRDetail i:nil="true"></a:SSRDetail>
								<a:FeeCode i:nil="true"></a:FeeCode>
								<a:Note i:nil="true"></a:Note>
								<a:SSRValue>0</a:SSRValue>
							</a:PaxSSR>';
					}
				}
				
				// baggage
				$baggage_key = $flight_details ['i_origin'] . '_' . $flight_details ['p_origin'];
				$baggage_pax_index = 0;
				if (isset ( $baggage_arr [$baggage_key] ) && valid_array ( $baggage_arr [$baggage_key] )) {
					$baggage_details_val = $baggage_arr [$baggage_key];
					$segment_ssr_request .= '<a:PaxSSR>
									<State xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Common">New</State>
									<a:ActionStatusCode>NN</a:ActionStatusCode>
									<a:ArrivalStation>' . $flight_details ['to_airport_code'] . '</a:ArrivalStation>
									<a:DepartureStation>' . $flight_details ['from_airport_code'] . '</a:DepartureStation>
									<a:PassengerNumber>' . ($baggage_pax_index ++) . '</a:PassengerNumber>
									<a:SSRCode>' . str_replace ( "'", "", $baggage_details_val ['value'] ) . '</a:SSRCode>
									<a:SSRNumber>0</a:SSRNumber>
									<a:SSRDetail i:nil="true"></a:SSRDetail>
									<a:FeeCode i:nil="true"></a:FeeCode>
									<a:Note i:nil="true"></a:Note>
									<a:SSRValue>0</a:SSRValue>
								</a:PaxSSR>';
				}
				
				$segment_ssr_request .= '</a:PaxSSRs>';
				$segment_ssr_request .= '</a:SegmentSSRRequest>';
			}
			$segment_ssr_request .= '</a:SegmentSSRRequests>';
			$segment_ssr_request;
			
			if (isset ( $this->source_code ) && ! empty ( $this->source_code )) {
				$booking_source = unserialized_data ( $temp_booking ['booking_source'] );
				if (valid_array ( $booking_source )) {
					$key = array_search ( $this->source_code, $booking_source );
					$token_arr = $temp_booking ['token'] [$key];
					if (valid_array ( $token_arr )) {
						foreach ( $token_arr as $t_k => $tkn ) {
							$this->signature = $tkn ['signature'];
						}
					}
				}
			}
			// debug($segment_ssr_request);exit;
			/*
			 * if ($infant_count > 0) {
			 *
			 * // Get Flight Segment details along with pax details
			 * $inf_query = 'select ID.origin AS i_origin, ID.is_leg, ID.segment_indicator, ID.airline_code, ID.flight_number, ID.from_airport_code, ID.to_airport_code,
			 * ID.departure_datetime, ID.arrival_datetime, ID.attributes, PD.pax_index , PD.origin AS p_origin, PD.first_name, PD.last_name, PD.date_of_birth
			 * from flight_booking_itinerary_details AS ID, flight_booking_passenger_details AS PD
			 * WHERE ID.app_reference=PD.app_reference AND PD.segment_indicator=ID.segment_indicator AND ID.booking_source = ' . $CI->db->escape ( $this->source_code ) . '
			 * AND ID.app_reference = ' . $CI->db->escape ( $book_id ) . ' AND PD.passenger_type = "Infant" ORDER BY ID.origin, PD.origin';
			 * $inf_output = $CI->db->query ( $inf_query )->result_array ();
			 * debug($inf_output);exit;
			 * // merge with action status code
			 * $ssr_group = array ();
			 * $index = 0;
			 * $itinerary_details = array ();
			 * $inf_pax_details = array ();
			 * $p_origin = 0;
			 * foreach ( $inf_output as $qk => $qv ) {
			 * $attributes = unserialize ( $qv ['attributes'] );
			 * $qv ['action_status_code'] = $attributes ['action_status_code'];
			 * if (intval ( $qv ['is_leg'] ) == 1 && isset ( $itinerary_details [$qv ['i_origin']] ) == false) {
			 * $itinerary_details [$qv ['i_origin']] = $qv;
			 * $p_origin = $qv ['i_origin'];
			 * } else {
			 * $itinerary_details [$p_origin] ['to_airport_code'] = $qv ['to_airport_code'];
			 * $itinerary_details [$p_origin] ['arrival_datetime'] = $qv ['arrival_datetime'];
			 * }
			 * // itinerary wise pax grouping
			 * if (isset ( $inf_pax_details [$qv ['pax_index']] ) == false) {
			 * $inf_pax_details [$qv ['pax_index']] = $qv;
			 * }
			 * }
			 * $request ['status'] = SUCCESS_STATUS;
			 * $segment_ssr_request .= '<a:SegmentSSRRequests>';
			 * // segment has pax
			 * foreach ( $itinerary_details as $flight_key => $flight_details ) {
			 * $segment_ssr_request .= '<a:SegmentSSRRequest>
			 * <a:FlightDesignator xmlns:b="http://schemas.navitaire.com/WebServices/DataContracts/Common">
			 * <b:CarrierCode>' . $flight_details ['airline_code'] . '</b:CarrierCode>
			 * <b:FlightNumber>' . $flight_details ['flight_number'] . '</b:FlightNumber>
			 * <b:OpSuffix i:nil="true"></b:OpSuffix>
			 * </a:FlightDesignator>
			 * <a:STD>' . date ( 'Y-m-d', strtotime ( $flight_details ['departure_datetime'] ) ) . 'T' . date ( 'H:i:s', strtotime ( $flight_details ['departure_datetime'] ) ) . '</a:STD>
			 * <a:DepartureStation>' . $flight_details ['from_airport_code'] . '</a:DepartureStation>
			 * <a:ArrivalStation>' . $flight_details ['to_airport_code'] . '</a:ArrivalStation>';
			 * $segment_ssr_request .= '<a:PaxSSRs>';
			 * $pax_index = 0;
			 * foreach ( $inf_pax_details as $pax_key => $pax_value ) {
			 * $segment_ssr_request .= '<a:PaxSSR>
			 * <State xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Common">New</State>
			 * <a:ActionStatusCode>' . $pax_value ['action_status_code'] . '</a:ActionStatusCode>
			 * <a:ArrivalStation>' . $flight_details ['to_airport_code'] . '</a:ArrivalStation>
			 * <a:DepartureStation>' . $flight_details ['from_airport_code'] . '</a:DepartureStation>
			 * <a:PassengerNumber>' . ($pax_index ++) . '</a:PassengerNumber>
			 * <a:SSRCode>INFT</a:SSRCode>
			 * <a:SSRNumber>0</a:SSRNumber>
			 * <a:SSRDetail i:nil="true"></a:SSRDetail>
			 * <a:FeeCode i:nil="true"></a:FeeCode>
			 * <a:Note i:nil="true"></a:Note>
			 * <a:SSRValue>0</a:SSRValue>
			 * </a:PaxSSR>';
			 * }
			 * // per segment(flight change) - per pax create ssr for meals and pax dont include infant
			 *
			 * //Himani FIXME
			 * $segment_ssr_request .= '</a:PaxSSRs>';
			 * $segment_ssr_request .= '</a:SegmentSSRRequest>';
			 * }
			 * $segment_ssr_request .= '</a:SegmentSSRRequests>';
			 * $segment_ssr_request;
			 * }
			 */
			$request ['payload'] = '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
		' . $this->soap_header () . '
		<s:Body>
			<SellRequest xmlns="http://schemas.navitaire.com/WebServices/ServiceContracts/BookingService">
				<SellRequestData xmlns:a="http://schemas.navitaire.com/WebServices/DataContracts/Booking" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
				<a:SellBy>SSR</a:SellBy>
				<a:SellJourneyByKeyRequest i:nil="true"></a:SellJourneyByKeyRequest>
					<a:SellJourneyRequest i:nil="true"></a:SellJourneyRequest>
					<a:SellSSR>
					<a:SSRRequest>
						' . $segment_ssr_request . '
						<a:CurrencyCode>INR</a:CurrencyCode>
						<a:CancelFirstSSR>false</a:CancelFirstSSR>
						<a:SSRFeeForceWaiveOnSell>false</a:SSRFeeForceWaiveOnSell>
					</a:SSRRequest>
				</a:SellSSR>
				<a:SellFee i:nil="true"></a:SellFee>
				</SellRequestData>
			</SellRequest>
		</s:Body>
	</s:Envelope>';
			
			// echo $request ['payload'];
			// exit ();
			
			$request ['url'] = $this->config ['end_point'] ['booking'];
			$request ['soap_action'] = 'http://schemas.navitaire.com/WebServices/IBookingManager/Sell';
		}
		
		return $request;
	}
	
	/**
	 * Update passenger RQ
	 *
	 * @param array $temp_booking        	
	 * @return string
	 */
	private function udpate_passenger_request($temp_booking) {
		$passengers = '';
		if (isset ( $temp_booking ['passenger_type'] ) && valid_array ( $temp_booking ['passenger_type'] )) {
			
			// Group passenger object if infant is found
			
			$p_obj = $this->form_passenger_object ( $temp_booking );
			foreach ( $p_obj as $pk => $person ) {
				$infant = '';
				if (isset ( $person ['infant'] ) == true) {
					$infant = '<a:Infant>
							<State
								xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Common">New</State>
							<a:DOB>' . $person ['infant'] ['dob'] . '</a:DOB>
							<a:Gender>' . $person ['infant'] ['gender'] . '</a:Gender>
							<a:Nationality>' . $person ['infant'] ['nationality'] . '</a:Nationality>
							<a:ResidentCountry i:nil="true" />
							<a:Names>
								<a:BookingName>
									<State
										xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Common">New</State>
									<a:FirstName>' . $person ['infant'] ['first_name'] . '</a:FirstName>
									<a:MiddleName i:nil="true" />
									<a:LastName>' . $person ['infant'] ['last_name'] . '</a:LastName>
									<a:Suffix i:nil="true" />
									<a:Title>' . $person ['infant'] ['title'] . '</a:Title>
								</a:BookingName>
							</a:Names>
						</a:Infant>';
				} else {
					$infant = '<a:Infant i:nil="true"/>';
				}
				$passengers .= '<a:Passenger>
								<State>New</State>
								<a:PassengerPrograms i:nil="true"/>
								<a:CustomerNumber i:nil="true"/>
								<a:PassengerNumber>' . $person ['passenger_number'] . '</a:PassengerNumber>
								<a:FamilyNumber>0</a:FamilyNumber>	
								<a:PaxDiscountCode i:nil="true"/>
								<a:Names>
									<a:BookingName>
										<State>New</State>
										<a:FirstName>' . $person ['first_name'] . '</a:FirstName>
										<a:MiddleName i:nil="true"/>
										<a:LastName>' . $person ['last_name'] . '</a:LastName>
										<a:Suffix i:nil="true"/>
										<a:Title>' . $person ['title'] . '</a:Title>
									</a:BookingName>
								</a:Names>
								' . $infant . '
								<a:PassengerInfo>
									<State>New</State>
									<a:Gender>' . $person ['gender'] . '</a:Gender>
									<a:Nationality>' . $person ['nationality'] . '</a:Nationality>
									<a:ResidentCountry i:nil="true"/>
									<a:WeightCategory>' . $person ['gender'] . '</a:WeightCategory>
								</a:PassengerInfo>
								<a:PassengerTypeInfos>
									<a:PassengerTypeInfo>
										<a:State>New</a:State>
										<a:DOB>' . $person ['dob'] . '</a:DOB>
										<a:PaxType>' . $person ['type'] . '</a:PaxType>
									</a:PassengerTypeInfo>
								</a:PassengerTypeInfos>
								<a:PassengerInfos i:nil="true"/>
								<a:PassengerInfants i:nil="true"/>
									<a:PseudoPassenger>false</a:PseudoPassenger>
									<a:PassengerTypeInfo i:nil="true"/>
							</a:Passenger>';
			}
		}
		
		if (isset ( $this->source_code ) && ! empty ( $this->source_code )) {
			$booking_source = unserialized_data ( $temp_booking ['booking_source'] );
			if (valid_array ( $booking_source )) {
				$key = array_search ( $this->source_code, $booking_source );
				$token_arr = $temp_booking ['token'] [$key];
				if (valid_array ( $token_arr )) {
					foreach ( $token_arr as $t_k => $tkn ) {
						$this->signature = $tkn ['signature'];
					}
				}
			}
		}
		$request ['payload'] = '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
			' . $this->soap_header () . '
			<s:Body>
				<UpdatePassengersRequest xmlns="http://schemas.navitaire.com/WebServices/ServiceContracts/BookingService">
					<updatePassengersRequestData xmlns:a="http://schemas.navitaire.com/WebServices/DataContracts/Booking" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
						<a:Passengers>
							' . $passengers . '
						</a:Passengers>
						<a:WaiveNameChangeFee>false</a:WaiveNameChangeFee>
					</updatePassengersRequestData>
				</UpdatePassengersRequest>
			</s:Body>
		</s:Envelope>';
		$request ['url'] = $this->config ['end_point'] ['booking'];
		$request ['soap_action'] = 'http://schemas.navitaire.com/WebServices/IBookingManager/UpdatePassengers';
		return $request;
	}
	
	/**
	 * create passneger object
	 *
	 * @param array $temp_booking        	
	 * @return boolean|p_obj[]
	 */
	private function form_passenger_object($temp_booking) {
		$p_obj = array ();
		$inf = array ();
		$p = 0;
		foreach ( $temp_booking ['passenger_type'] as $k => $v ) {
			$nationality = $temp_booking ['passenger_nationality'] [$k];
			$passenger_nationality = $GLOBALS ['CI']->db_cache_api->get_country_list ( array (
					'k' => 'origin',
					'v' => 'iso_country_code' 
			), array (
					'origin' => $nationality 
			) );
			$person = array (
					'title' => strtoupper ( get_enum_list ( 'title', $temp_booking ['name_title'] [$k] ) ),
					'first_name' => $temp_booking ['first_name'] [$k],
					'last_name' => $temp_booking ['last_name'] [$k],
					'gender' => ucfirst ( get_enum_list ( 'gender', $temp_booking ['gender'] [$k] ) ),
					'dob' => date ( 'Y-m-d', strtotime ( $temp_booking ['date_of_birth'] [$k] ) ) . 'T00:00:00+00:00' 
			);
			if (isset ( $passenger_nationality [$nationality] )) {
				$person ['nationality'] = $passenger_nationality [$nationality];
			} else {
				$person ['nationality'] = 'IN';
			}
			if ($v == 'Infant') {
				$person ['type'] = 'INF';
				$inf [] = $person;
				continue;
			} else if ($v == 'Adult') {
				$person ['type'] = 'ADT';
			} else {
				$person ['type'] = 'CHD';
			}
			$person ['passenger_number'] = $p ++;
			$p_obj [] = $person;
		}
		
		foreach ( $inf as $i => $infant ) {
			if (isset ( $p_obj [$i] ) == false) {
				return false;
			}
			$p_obj [$i] ['infant'] = $infant;
		}
		return $p_obj;
	}
	/**
	 *
	 * @param unknown $TotalCost        	
	 * @return string
	 */
	private function add_payment_to_booking_request($TotalCost) {
		$request ['payload'] = '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/" >
			' . $this->soap_header () . '
			<s:Body>
				<AddPaymentToBookingRequest xmlns="http://schemas.navitaire.com/WebServices/ServiceContracts/BookingService">
					<addPaymentToBookingReqData xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Booking" xmlns:i="http://www.w3.org/2001/XMLSchema-instance"> 
						<MessageState>New</MessageState>
						<WaiveFee>false</WaiveFee>
						<ReferenceType>Default</ReferenceType>
						<PaymentMethodType>AgencyAccount</PaymentMethodType>
						<PaymentMethodCode>AG</PaymentMethodCode>
						<QuotedCurrencyCode>' . get_application_default_currency () . '</QuotedCurrencyCode>
						<QuotedAmount>' . $TotalCost . '</QuotedAmount>
						<Status>New</Status>
						<AccountNumberID>0</AccountNumberID>
						<AccountNumber>' . $this->config ['organization_code'] . '</AccountNumber>
						<Expiration>2020-01-01T00:00:00</Expiration>
						<ParentPaymentID>0</ParentPaymentID>
						<Installments>1</Installments>
						<PaymentText i:nil="true"/>
						<Deposit>false</Deposit>
						<PaymentFields i:nil="true"/>
						<PaymentAddresses i:nil="true"/>
						<AgencyAccount i:nil="true"/>
						<CreditShell i:nil="true"/>
						<CreditFile i:nil="true"/>
						<PaymentVoucher i:nil="true"/>
						<ThreeDSecureRequest i:nil="true"/>
						<MCCRequest i:nil="true"/>
						<AuthorizationCode i:nil="true"/>
					</addPaymentToBookingReqData>
				</AddPaymentToBookingRequest>
			</s:Body>
		</s:Envelope>';
		$request ['url'] = $this->config ['end_point'] ['booking'];
		$request ['soap_action'] = 'http://schemas.navitaire.com/WebServices/IBookingManager/AddPaymentToBooking';
		return $request;
	}
	private function booking_commit_request($temp_booking, $payment = array()) {
		$CI = & get_instance ();
		if (isset ( $temp_booking ) && ! empty ( $temp_booking )) {
			$pax_cnt = 0;
			if (isset ( $temp_booking ['passenger_type'] ) && valid_array ( $temp_booking ['passenger_type'] )) {
				foreach ( $temp_booking ['passenger_type'] as $p_key => $passenger ) {
					if ($passenger == 'Infant') {
						continue;
					}
					$pax_cnt ++;
				}
			}
			
			$booking_holder_Details ['name_title'] = strtoupper ( get_enum_list ( 'title', $temp_booking ['name_title'] [0] ) );
			$booking_holder_Details ['first_name'] = $temp_booking ['first_name'] [0];
			$booking_holder_Details ['last_name'] = $temp_booking ['last_name'] [0];
			$booking_holder_Details ['billing_city'] = $temp_booking ['billing_city'];
			$booking_holder_Details ['billing_address_1'] = $temp_booking ['billing_address_1'];
			$booking_holder_Details ['contact'] = $temp_booking ['passenger_contact'];
			$booking_holder_Details ['billing_email'] = $temp_booking ['billing_email'];
			$booking_holder_Details ['billing_zipcode'] = $temp_booking ['billing_zipcode'];
			
			if (isset ( $this->source_code ) && ! empty ( $this->source_code )) {
				$booking_source = unserialized_data ( $temp_booking ['booking_source'] );
				if (valid_array ( $booking_source )) {
					$key = array_search ( $this->source_code, $booking_source );
					$token_arr = $temp_booking ['token'] [$key];
					if (valid_array ( $token_arr )) {
						foreach ( $token_arr as $t_k => $tkn ) {
							$this->signature = $tkn ['signature'];
						}
					}
				}
			}
			
			$request ['payload'] = '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
				' . $this->soap_header () . '
				<s:Body>
					<BookingCommitRequest xmlns="http://schemas.navitaire.com/WebServices/ServiceContracts/BookingService">
						<BookingCommitRequestData xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Booking" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
							<State>New</State>
							<RecordLocator i:nil="true"/>
							<CurrencyCode>' . get_application_default_currency () . '</CurrencyCode>
							<PaxCount>' . $pax_cnt . '</PaxCount>
							<SystemCode i:nil="true"/>
							<BookingID>0</BookingID>
							<BookingParentID>0</BookingParentID>
							<ParentRecordLocator i:nil="true"/>
							<BookingChangeCode i:nil="true"/>
							<GroupName i:nil="true"/>
							<SourcePOS xmlns:b="http://schemas.navitaire.com/WebServices/DataContracts/Common">
								<b:State>New</b:State>
								<b:AgentCode>' . $this->config ['agent_id'] . '</b:AgentCode>
								<b:OrganizationCode>' . $this->config ['organization_code'] . '</b:OrganizationCode>
								<b:DomainCode>' . $this->config ['agent_domain'] . '</b:DomainCode>
								<b:LocationCode>www</b:LocationCode>
							</SourcePOS>
							<BookingHold i:nil="true"/>
							<ReceivedBy i:nil="true"/>
							<RecordLocators i:nil="true"/>
							<Passengers i:nil="true"/>
							<BookingComments i:nil="true"/>
							<BookingContacts>
								<BookingContact>
									<State>New</State>
									<TypeCode>P</TypeCode>
									<Names>
										<BookingName>
											<State>New</State>
											<FirstName>' . $booking_holder_Details ['first_name'] . '</FirstName>
											<MiddleName i:nil="true"/>
											<LastName>' . $booking_holder_Details ['last_name'] . '</LastName>
											<Suffix i:nil="true"/>
											<Title>' . $booking_holder_Details ['name_title'] . '</Title>
										</BookingName>
									</Names>
									<EmailAddress>' . $booking_holder_Details ['billing_email'] . '</EmailAddress>
									<HomePhone>' . $booking_holder_Details ['contact'] . '</HomePhone>
									<WorkPhone i:nil="true"/>
									<OtherPhone i:nil="true"/>
									<Fax i:nil="true"/>
									<CompanyName>' . $CI->entity_domain_name . '</CompanyName>
									<AddressLine1>' . $booking_holder_Details ['billing_address_1'] . '</AddressLine1>
									<AddressLine2 i:nil="true"/>
									<AddressLine3 i:nil="true"/>
									<City>' . $CI->entity_city . '</City>
									<ProvinceState>' . $CI->entity_state . '</ProvinceState>
									<PostalCode>' . $booking_holder_Details ['billing_zipcode'] . '</PostalCode>
									<CountryCode>IN</CountryCode>
									<CultureCode>en-GB</CultureCode>
									<DistributionOption>Email</DistributionOption>
									<CustomerNumber i:nil="true"/>
									<NotificationPreference>None</NotificationPreference>
									<SourceOrganization>' . $this->config ['organization_code'] . '</SourceOrganization>
								</BookingContact>
							</BookingContacts>							
							<NumericRecordLocator i:nil="true"/>
							<RestrictionOverride>false</RestrictionOverride>
							<ChangeHoldDateTime>false</ChangeHoldDateTime>
							<WaiveNameChangeFee>false</WaiveNameChangeFee>
							<WaivePenaltyFee>false</WaivePenaltyFee>
							<WaiveSpoilageFee>false</WaiveSpoilageFee>
							<DistributeToContacts>true</DistributeToContacts>
						</BookingCommitRequestData>
					</BookingCommitRequest>
				</s:Body>
			</s:Envelope>';
		}
		
		$request ['url'] = $this->config ['end_point'] ['booking'];
		$request ['soap_action'] = 'http://schemas.navitaire.com/WebServices/IBookingManager/BookingCommit';
		return $request;
	}
	private function send_itinerary_request() {
		$request ['payload'] = '';
		$request ['url'] = $this->config ['end_point'] ['booking'];
		$request ['soap_action'] = 'http://schemas.navitaire.com/WebServices/IBookingManager/SendItinerary';
		return $request;
	}
	private function get_booking($pnr) {
		$request ['status'] = SUCCESS_STATUS;
		$request ['payload'] = '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
						' . $this->soap_header () . '
						<s:Body>
							<GetBookingRequest xmlns="http://schemas.navitaire.com/WebServices/ServiceContracts/BookingService">
								<GetBookingReqData xmlns:a="http://schemas.navitaire.com/WebServices/DataContracts/Booking" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
									<a:GetBookingBy>RecordLocator</a:GetBookingBy>
									<a:GetByRecordLocator>
										<a:RecordLocator>' . $pnr . '</a:RecordLocator>
									</a:GetByRecordLocator>
									<a:GetByThirdPartyRecordLocator i:nil="true"/>
									<a:GetByID i:nil="true"/>
								</GetBookingReqData>
							</GetBookingRequest>
						</s:Body>
					</s:Envelope>';
		$request ['url'] = $this->config ['end_point'] ['booking'];
		$request ['soap_action'] = 'http://schemas.navitaire.com/WebServices/IBookingManager/GetBooking';
		return $request;
	}
	function cancel_booking($master_booking_details, $transction_key) {
		$segment = array ();
		$response ['status'] = FAILURE_STATUS;
		$CI = &get_instance ();
		
		error_reporting ( E_ALL );
		if (isset ( $transction_key ) && ! empty ( $transction_key )) {
			$app_reference = $master_booking_details ['booking_details'] [0] ['app_reference'];
			$booking_source = $this->source_code;
			$booking_transction = $master_booking_details ['booking_details'] [0] ['booking_transaction_details'];
			
			foreach ( $booking_transction as $t_key => $transction ) {
				if ($transction ['source'] == $booking_source) {
					$pnr = $transction ['pnr'];
					$get_booking = $this->get_booking ( $pnr );
					$CI->custom_db->generate_static_response ( $get_booking ['payload'], $this->operator . ' get booking', $this->operator );
					
					if ($get_booking ['status'] == SUCCESS_STATUS) {
						$get_booking_response = $this->process_request ( $get_booking ['payload'], $get_booking ['url'], $get_booking ['soap_action'] );
						$CI->custom_db->generate_static_response ( $get_booking_response, $this->operator . ' get booking RS', $this->operator );
						$get_booking_response = Converter::createArray ( $get_booking_response );
						
						if ($this->valid_get_booking ( $get_booking_response )) {
							$cancellation_request = $this->cancel_request ( $master_booking_details, $transction_key );
							if ($cancellation_request ['status'] == SUCCESS_STATUS) {
								$CI = & get_instance ();
								$CI->custom_db->generate_static_response ( $cancellation_request ['data'] ['payload'], $this->operator . ' cancellation RQ', $this->operator );
								
								try {
									$cancellation_response = $this->process_request ( $cancellation_request ['data'] ['payload'], $cancellation_request ['data'] ['url'], $cancellation_request ['data'] ['soap_action'] );
									$CI->custom_db->generate_static_response ( $cancellation_response, $this->operator . ' cancellation RS', $this->operator );
									$cancellation_response = Converter::createArray ( $cancellation_response );
									debug ( $cancellation_response );
									exit ();
									if ($this->valid_cancellation_response ( $cancellation_response )) {
										$status_update = array (
												'status' => 'BOOKING_CANCELLED' 
										);
										$CI->db->where ( 'app_reference', $app_reference );
										$CI->db->where ( 'source', $booking_source );
										$CI->db->update ( 'flight_booking_transaction_details', $status_update );
										
										$api_refend_amt = $cancellation_response ['s:Envelope'] ['s:Body'] ['CancelResponse'] ['BookingUpdateResponseData'] ['Success'] ['PNRAmount'] ['BalanceDue'];
										$api_cancellation_charge = $cancellation_response ['s:Envelope'] ['s:Body'] ['CancelResponse'] ['BookingUpdateResponseData'] ['Success'] ['PNRAmount'] ['TotalCost'];
										// add entry in 'flight_cancellation' table
										$flight_cancellation = array (
												'app_reference' => $app_reference,
												'transaction_fk' => $transction_key,
												'API_RefundedAmount' => $api_refend_amt,
												'API_CancellationCharge' => $api_cancellation_charge,
												'cancellation_processed_on' => date ( 'Y-m-d H:i:s' ),
												'current_status' => 'BOOKING_CANCELLED',
												'created_by_id' => intval ( @$GLOBALS ['CI']->entity_user_id ),
												'created_datetime' => date ( 'Y-m-d H:i:s' ) 
										);
										$CI->db->insert ( 'flight_cancellation_details', $flight_cancellation );
										$response ['status'] = SUCCESS_STATUS;
									}
								} catch ( Exception $e ) {
								}
							}
						}
					}
					break;
				}
			}
		}
		return $response;
	}
	function valid_cancellation_response($cancellation_response) {
		if (isset ( $cancellation_response ['s:Envelope'] ['s:Body'] ['CancelResponse'] ['BookingUpdateResponseData'] ['Success'] )) {
			return true;
		}
		return false;
	}
	function valid_get_booking($booking) {
		if (isset ( $booking ['s:Envelope'] ['s:Body'] ['GetBookingResponse'] ['Booking'] ['BookingInfo'] ['BookingStatus'] )) {
			return true;
		}
		return false;
	}
	function cancel_request($master_booking_details, $transction_key) {
		$response ['data'] = array ();
		$response ['status'] = FAILURE_STATUS;
		
		if (isset ( $transction_key ) && ! empty ( $transction_key )) {
			$itenary_details = $master_booking_details ['booking_details'] [0] ['booking_itinerary_details'];
			$token = unserialized_data ( $master_booking_details ['booking_details'] [0] ['api_token'] );
			$flights = array ();
			foreach ( $itenary_details as $key => $details ) {
				$flights [$details ['segment_indicator']] [] = $details;
			}
			
			$itenary_array = array ();
			foreach ( $flights as $f_k => $flight ) {
				$first_arr = current ( $flight );
				$last_arr = end ( $flight );
				$key_it = $first_arr ['from_airport_code'] . '_' . $last_arr ['to_airport_code'];
				$itenary_array [$key_it] ['segment'] = $flight;
				$itenary_array [$key_it] ['journey_key'] = $token [$key_it];
			}
			
			$departure_index = '';
			if (valid_array ( $itenary_array )) {
				$journey = '';
				if (valid_array ( $itenary_array )) {
					foreach ( $itenary_array as $seg_key => $__seg ) {
						$segment_xml = '';
						foreach ( $__seg ['segment'] as $__sk => $segment_Details ) {
							$attributes = unserialize ( $segment_Details ['attributes'] );
							$action_Status_code = '';
							if (isset ( $attributes ['action_status_code'] )) {
								$action_Status_code = $attributes ['action_status_code'];
							}
							$sta = date ( 'Y-m-d', strtotime ( $segment_Details ['arrival_datetime'] ) ) . 'T00:00:00';
							$std = date ( 'Y-m-d', strtotime ( $segment_Details ['departure_datetime'] ) ) . 'T00:00:00';
							
							$segment_xml .= '<Segment>
												<State xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Common">New</State>
												<ActionStatusCode>' . $action_Status_code . '</ActionStatusCode>
												<ArrivalStation>' . $segment_Details ['to_airport_code'] . '</ArrivalStation>
												<CabinOfService i:nil="true"></CabinOfService>
												<ChangeReasonCode i:nil="true"></ChangeReasonCode>
												<DepartureStation>' . $segment_Details ['from_airport_code'] . '</DepartureStation>
												<PriorityCode i:nil="true"></PriorityCode>
												<SegmentType i:nil="true"></SegmentType>
												<STA>' . $sta . '</STA>
												<STD>' . $std . '</STD>
												<International>false</International>
												<FlightDesignator xmlns:a="http://schemas.navitaire.com/WebServices/DataContracts/Common">
													<a:CarrierCode>' . $segment_Details ['operating_carrier'] . '</a:CarrierCode>
													<a:FlightNumber>' . $segment_Details ['flight_number'] . '</a:FlightNumber>
													<a:OpSuffix i:nil="true"></a:OpSuffix>
												</FlightDesignator>
												<XrefFlightDesignator i:nil="true" xmlns:a="http://schemas.navitaire.com/WebServices/DataContracts/Common"></XrefFlightDesignator>
												<Fares i:nil="true"></Fares>
												<Legs i:nil="true"></Legs>
												<PaxBags i:nil="true"></PaxBags>
												<PaxSeats i:nil="true"></PaxSeats>
												<PaxSSRs i:nil="true"></PaxSSRs>
												<PaxSegments i:nil="true"></PaxSegments>
												<PaxTickets i:nil="true"></PaxTickets>
												<PaxSeatPreferences i:nil="true"></PaxSeatPreferences>
												<SalesDate>0001-01-01T00:00:00</SalesDate>
												<SegmentSellKey i:nil="true"></SegmentSellKey>
												<PaxScores i:nil="true"></PaxScores>
												<ChannelType>Default</ChannelType>
											</Segment>';
						}
						$journey_key = $__seg ['journey_key'] ['JourneySellKey'] [0];
						$journey .= '<Journeys>
											<Journey>
												<State xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Common">New</State>
												<NotForGeneralUse>false</NotForGeneralUse>
												<Segments>
													' . $segment_xml . '
												</Segments>
												<JourneySellKey>' . $journey_key . '</JourneySellKey>
											</Journey>
										</Journeys>';
					}
				}
			}
			$request = '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/" >
						' . $this->soap_header () . '
						<s:Body>
						<CancelRequest xmlns="http://schemas.navitaire.com/WebServices/ServiceContracts/BookingService">
							<CancelRequestData xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Booking"
							xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
								<CancelBy>Journey</CancelBy>
								<CancelJourney>
									<CancelJourneyRequest>
										' . $journey . '
									<WaivePenaltyFee>false</WaivePenaltyFee>
									<WaiveSpoilageFee>false</WaiveSpoilageFee>
									<PreventReprice>false</PreventReprice>
									</CancelJourneyRequest>
								</CancelJourney>
								<CancelFee i:nil="true"></CancelFee>
								<CancelSSR i:nil="true"></CancelSSR>
							</CancelRequestData>
						</CancelRequest>
						</s:Body>
						</s:Envelope>';
			
			$response ['data'] ['payload'] = $request;
			$response ['data'] ['url'] = $this->config ['end_point'] ['booking'];
			$response ['data'] ['soap_action'] = 'http://schemas.navitaire.com/WebServices/IBookingManager/Cancel';
			$response ['status'] = SUCCESS_STATUS;
		}
		return $response;
	}
	
	/**
	 * get destroy session RQ for api
	 */
	private function logout_request() {
		$request ['payload'] = '
				<?xml version="1.0" encoding="UTF-8"?>
				<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
					<s:Header>
						<h:ContractVersion xmlns:h="http://schemas.navitaire.com/WebServices">340</h:ContractVersion>
						<h:Signature xmlns:h="http://schemas.navitaire.com/WebServices">KFuVM6agIr8=|UG4GKsxCDjyBk9l5/S71QVsAmxiTZOE8ahC8gkwr8od2lSVAwqRW72TGi/tid1Xzi/986rEvbap6S/kz9Z5XC0qe1PPXm0hXIOOHwOoEkJvJ2U7LLQc0YqEhsBql+pPHFdPfI0Ewfm4=</h:Signature>
					</s:Header>
					<s:Body>
						<LogoutRequest xmlns="http://schemas.navitaire.com/WebServices/ServiceContracts/SessionService" />
					</s:Body>
				</s:Envelope>';
		$request ['url'] = $this->config ['end_point'] ['session'];
		$request ['soap_action'] = 'http://schemas.navitaire.com/WebServices/ISessionManager/Logon';
		return $request;
	}
	
	/**
	 * Process login
	 */
	public function login() {
		$status = false;
		$CI = & get_instance ();
		$signature = false;
		// $signature = $this->read_session_id ( $this->source_code );
		// Only in development needed
		/*
		 * if (isset ( $_GET ['ssi'] ) == false) {
		 * $signature = false;
		 * }
		 */
		if (empty ( $signature ) == true) {
			$request = $this->login_request ();
			$process_output = $this->process_request ( $request ['payload'], $request ['url'], $request ['soap_action'] );
			$process_output = Converter::createArray ( $process_output );
			/*
			 * debug($process_output);
			 * exit;
			 */
			if (empty ( $process_output ) == false && @empty ( $process_output ['s:Envelope'] ['s:Body'] ['LogonResponse'] ['Signature'] ) == false) {
				$signature = $process_output ['s:Envelope'] ['s:Body'] ['LogonResponse'] ['Signature'];
				// $this->save_session_id ( $signature, $this->source_code );
				$status = true;
			}
		} else {
			$status = true;
		}
		$this->signature = $signature;
		return $status;
	}
	
	/**
	 * Process Logout
	 */
	public function logout() {
		$request = $this->logout_request ();
		$this->process_request ( $request ['payload'], $request ['url'] );
	}
	
	/**
	 * flight list availability
	 */
	public function get_flight_list($search_id) {
		$response ['status'] = FAILURE_STATUS; // Status Of Operation
		$response ['message'] = ''; // Message to be returned
		$response ['data'] = array (); // Data to be returned
		$login_status = $this->login ();
		
		if ($login_status == false) {
			// return failure object as the login signature is not set
			return $response;
		}
		
		$CI = & get_instance ();
		$CI->load->driver ( 'cache' );
		$response ['data'] = array ();
		$response ['status'] = SUCCESS_STATUS;
		
		/* get search criteria based on search id */
		$search_data = $this->search_data ( $search_id );
		
		// generate unique searchid string to enable caching
		$cache_search = $CI->config->item ( 'cache_flight_search' );
		$search_hash = $this->search_hash;
		
		if ($cache_search) {
			$cache_contents = $CI->cache->file->get ( $search_hash );
		}
		if ($this->master_search_data ['domestic_round_trip'] == true && $this->run_round_trip_special == true) {
			$this->is_special_request = true;
		}
		if ($search_data ['status'] == SUCCESS_STATUS) {
			if ($cache_search == FALSE || ($cache_search === true && empty ( $cache_contents ) == true)) {
				// Flight search RQ
				$search_request = $this->search_request ( $search_data ['data'] );
				$CI->custom_db->generate_static_response ( $search_request ['payload'], $this->operator . ' search RQ', $this->operator );
				if ($search_request ['status'] = SUCCESS_STATUS) {
					/*
					 * $CI->custom_db->generate_static_response ( json_encode ( $search_request ) );
					 * $search_response = $this->process_request ( $search_request ['payload'], $search_request ['url'], $search_request ['soap_action'] );
					 * $search_response_array = Converter::createArray ( $search_response );
					 * $CI->custom_db->generate_static_response ( json_encode ( $search_response_array ) );
					 */
					
					if (isset ( $_GET ['ssi'] )) {
						$static_search_result_id = $_GET ['ssi'];
						$search_response = $GLOBALS ['CI']->flight_model->get_static_response ( $static_search_result_id );
						$search_response_array = Converter::createArray ( $search_response );
					} else {
						$search_response = $this->process_request ( $search_request ['payload'], $search_request ['url'], $search_request ['soap_action'] );
						$search_response_array = Converter::createArray ( $search_response );
						
						$CI->custom_db->generate_static_response ( $search_response, $this->operator . ' search RS', $this->operator );
					}
					// echo 'Request-----------';
					// echo $search_request ['payload'];
//					debug($search_response_array);exit;
					if ($this->valid_search_result ( $search_response_array ) == TRUE) {
						// SAVE RS in 'test' table
						$clean_format_data = $this->format_search_data_response ( $search_response_array, $search_data ['data'] );
						// echo 'fhsd';debug($clean_format_data);
						// exit;
						if ($clean_format_data) {
							$response ['status'] = SUCCESS_STATUS;
						} else {
							$response ['status'] = FAILURE_STATUS;
						}
					} else {
						$response ['status'] = FAILURE_STATUS;
					}
				} else {
					$response ['status'] = FAILURE_STATUS;
				}
				// Cache
				if ($response ['status'] == SUCCESS_STATUS) {
					$response ['data'] = $clean_format_data;
					if ($cache_search) {
						$cache_exp = $CI->config->item ( 'cache_flight_search_ttl' );
						$CI->cache->file->save ( $search_hash, $response ['data'], $cache_exp );
					}
				}
			} else {
				$response ['data'] = $cache_contents;
			}
		} else {
			$response ['status'] = FAILURE_STATUS;
		}
		return $response;
	}
	
	/**
	 * Create batch keys for RQs by grouping them - with unique combinations
	 *
	 * @param array $key_details        	
	 * @return array - batch key group array
	 */
	private function create_request_batch_keys($key_details) {
		$batch_flights = array ();
		$batch_number = 0;
		$request_batch = array ();
		foreach ( $key_details as $k => $flight_segs ) {
			if (count ( $flight_segs ['fare'] ) == 1) {
				// direct flights to first batch
				$batch_number = 1;
				// [Batch Number] [Stop Number]
				$batch_flights [0] [0] [] = $flight_segs ['fare'] [0] ['flight_number'];
			} else {
				$batch_number = $this->find_price_request_batch_number ( $flight_segs, $batch_flights );
				// $flight_segs, $batch_flights
			}
			$request_batch [$batch_number] [] = $flight_segs;
		}
		return $request_batch;
	}
	
	/**
	 * Find batch number
	 *
	 * @param array $flight_segs        	
	 * @param array $batch_flights        	
	 */
	private function find_price_request_batch_number($flight_segs, & $batch_flights) {
		$batch = - 1;
		$current_batch = 0;
		while ( $batch < 0 ) {
			if (isset ( $batch_flights [$current_batch] ) == true) {
				$batch_pointer = $batch_flights [$current_batch];
				// check occurance in current batch pointer
				$failure_count = 0;
				foreach ( $flight_segs ['fare'] as $fk => $fv ) {
					// check if current number of segments is matching
					if (isset ( $batch_pointer [$fk] ) == true) {
						// compare in current batch
						if (in_array ( $fv ['flight_number'], $batch_pointer [$fk] ) == true) {
							$failure_count ++;
							break;
						}
					} else {
						// segment count is more than batch pointer - so we found batch id
						// $batch = $current_batch;
						break;
					}
				}
			} else {
				$failure_count = 0;
				$batch = $current_batch;
				$batch_flights [$batch] = array ();
			}
			
			if ($failure_count > 0) {
				$current_batch ++;
			} else {
				$batch = $current_batch;
			}
		}
		// push flights inside batch
		if ($batch > - 1) {
			foreach ( $flight_segs ['fare'] as $k => $v ) {
				$batch_flights [$batch] [$k] [] = $v ['flight_number'];
			}
		}
		return $batch;
	}
	
	/**
	 * Itinerary pricing details RQ
	 *
	 * @param array $key_details
	 *        	- Key details received from availability RQ
	 * @return string
	 */
	private function get_itinerary_price($key_details, $trip_type = 'domestic') {
		$response ['status'] = FAILURE_STATUS;
		$response ['data'] = array ();
		$CI = & get_instance ();
		$key_list = $this->create_request_batch_keys ( $key_details );
		
		// get price details
		foreach ( $key_list as $k => $v ) {
			$price_request = $this->price_itinerary_request ( $this->master_search_data, $v );
			
			if ($price_request ['status'] = SUCCESS_STATUS) {
				
				if (isset ( $_GET ['sip'] ) == true) {
					$static_price_result_id = $_GET ['sip'];
					$price_response = $GLOBALS ['CI']->flight_model->get_static_response ( $static_price_result_id );
					$price_response_array = Converter::createArray ( $price_response );
				} else {
					// 68
					$CI->custom_db->generate_static_response ( $price_request ['payload'], $this->operator . ' Price itinerary RQ', $this->operator );
					$price_response = $this->process_request ( $price_request ['payload'], $price_request ['url'], $price_request ['soap_action'] );
					$price_response_array = Converter::createArray ( $price_response );
//					debug($price_request ['payload']); debug($price_response_array);exit;
					$CI->custom_db->generate_static_response ( $price_response, $this->operator . ' price itenary RS', $this->operator );
				}
				// exit;
				if ($this->valid_price_result ( $price_response_array ) == TRUE) {
					// SAVE RS in 'test' table
					$clean_format_data = $this->format_price_data_response ( $price_response_array, $key_details );
					$response ['data'] = array_merge ( $response ['data'], $clean_format_data ['data'] );
					$response ['status'] = SUCCESS_STATUS;
				} else {
					$response ['status'] = FAILURE_STATUS;
					break;
				}
			}
		}
		
		return $response;
	}
	
	/**
	 * Arjun J Gowda
	 * check if the search RS is valid or not
	 *
	 * @param array $search_result
	 *        	search result RS to be validated
	 */
	private function valid_search_result($search_result) {
		if (valid_array ( $search_result ) == true and isset ( $search_result ['s:Envelope'] ) == true and isset ( $search_result ['s:Envelope'] ['s:Body'] ) == true and isset ( $search_result ['s:Envelope'] ['s:Body'] ['GetAvailabilityByTripResponse'] ) == true and isset ( $search_result ['s:Envelope'] ['s:Body'] ['GetAvailabilityByTripResponse'] ['GetTripAvailabilityResponse'] ) == true && valid_array ( @$search_result ['s:Envelope'] ['s:Body'] ['GetAvailabilityByTripResponse'] ['GetTripAvailabilityResponse'] ['Schedules'] ['ArrayOfJourneyDateMarket'] )) {
			return true;
		} else {
			$this->clear_session ();
			return false;
		}
	}
	
	/**
	 * validate price RS details
	 *
	 * @param array $price_result        	
	 */
	private function valid_price_result($price_result) {
		if (valid_array ( $price_result ) == true and isset ( $price_result ['s:Envelope'] ) == true and isset ( $price_result ['s:Envelope'] ['s:Body'] ) == true and isset ( $price_result ['s:Envelope'] ['s:Body'] ['PriceItineraryResponse'] ) == true and isset ( $price_result ['s:Envelope'] ['s:Body'] ['PriceItineraryResponse'] ['Booking'] ) == true && valid_array ( @$price_result ['s:Envelope'] ['s:Body'] ['PriceItineraryResponse'] ['Booking'] ['Journeys'] )) {
			return true;
		} else {
			$this->clear_session ();
			return false;
		}
	}
	private function clear_session() {
		$ci = & get_instance ();
		$ci->session->unset_userdata ( $this->source_code );
	}
	
	/**
	 *
	 * @param array $search_result
	 *        	Search RS received from API
	 * @param array $search_data
	 *        	Search criteria submitted by user
	 */
	function format_search_data_response($search_result, $search_data) {
//		debug($search_result);
//		exit;
		// ArrayOfJourneyDateMarket - Journey1, Journey2, Journey3...
		$trip_type = isset ( $search_data ['is_domestic'] ) && ! empty ( $search_data ['is_domestic'] ) ? 'domestic' : 'international';
		
		$Schedules = $search_result ['s:Envelope'] ['s:Body'] ['GetAvailabilityByTripResponse'] ['GetTripAvailabilityResponse'] ['Schedules'];
		$ArrayOfJourneyDateMarket = force_multple_data_format ( $Schedules ['ArrayOfJourneyDateMarket'] );
		
		$flight_list = array ();
		foreach ( $ArrayOfJourneyDateMarket as $market_array_k => $market_array_v ) {
			// $JourneyDateMarket - Journey Date interval can be RQed - currently fixed to 1
			if (isset ( $market_array_v ['JourneyDateMarket'] ) == false) {
				/*
				 * debug($ArrayOfJourneyDateMarket);
				 * exit;
				 */
				return false;
			}
			$JourneyDateMarket = force_multple_data_format ( $market_array_v ['JourneyDateMarket'] );
			foreach ( $JourneyDateMarket as $journey_array_k => $journey_array_v ) {
				$journey_n_key = '';
				$DepartureDate = $journey_array_v ['DepartureDate'];
				$DepartureStation = $journey_array_v ['DepartureStation'];
				$ArrivalStation = $journey_array_v ['ArrivalStation'];
				$journey_n_key = $DepartureStation . '_' . $ArrivalStation;
				
				$Journeys = force_multple_data_format ( $journey_array_v ['Journeys'] ['Journey'] );
				foreach ( $Journeys as $journey_k => $journey_v ) {
					$Segments = force_multple_data_format ( $journey_v ['Segments'] ['Segment'] );
					if (COUNT ( $Segments ) == 1 || strcasecmp ( 'multicity', $search_data ['trip_type'] ) != 0) {
						if (COUNT ( $Segments ) > 1) {
							// debug($Segments);exit;
						}
//						$attr = $this->journey_class ( $Segments );
						//if (strcasecmp ( 'Sale Fare', $attr ['tag'] ) != 0) {
							$flight_details = array ();
							$key = array ();
							// take out and form necessary data for printing
							$key ['key'] [$journey_n_key] ['JourneySellKey'] [] = $journey_v ['JourneySellKey'];
							$key ['key'] [$journey_n_key] ['signature'] = $this->signature;
							$flight_details = $this->flight_segment_summary ( $Segments, $journey_n_key, $key );
							
							if(valid_array($flight_details)) {
								foreach($flight_details as $f__key => $flight_detail) {
									// itinerary_key_details
									$itinerary_key_details[$f__key][] = $this->itinerary_key_details ( $flight_detail ['key'], $flight_detail ['raw'] ['fare'] );
									$flight_list ['journey_list'] [$market_array_k] [] = $flight_detail;
								}
							}
							
							
//							$flight_details ['attr'] = $this->journey_class ( $Segments );
//							$flight_details ['attr']['isrefundable'] = 'Refundable'; // Except Sale fare, all fares are refundable
//							$flight_details ['attr'] ['operator'] = $this->operator;
//							$flight_details ['key'] = $key ['key'];
//							$flight_details ['token'] = serialized_data ( $flight_details ['key'] );
//							$flight_details ['token_key'] = md5 ( $flight_details ['token'] );
							// itinerary_key_details
//							$itinerary_key_details [] = $this->itinerary_key_details ( $flight_details ['key'], $flight_details ['raw'] ['fare'] );
							// $flight_details['itinerary_key_details'] = end($itinerary_key_details);
							
							// assign it in the end
//							$flight_list ['journey_list'] [$market_array_k] [] = $flight_details;
						//}
					}
				}
			}
		}
		
		if(valid_array($itinerary_key_details)) {
			foreach($itinerary_key_details as $___ky => $arr_key){
				$itenary_price_det = $this->get_itinerary_price ( $arr_key, $trip_type );
				if($itenary_price_det['status'] == SUCCESS_STATUS) {
					$itinerary_price_details[] = $itenary_price_det['data'];	
				}
			}
		}
		
		// FIXME : INFT
		$infant_price = array ();
		if ($this->master_search_data ['infant'] > 0) {
			$infant_price = $this->read_infant_price_details ();
		}
		
		if (valid_array($itinerary_price_details)) {
			$this->update_search_itinerary_price_details ( $flight_list, $itinerary_price_details, $infant_price, $trip_type );
//			echo 'himani';debug($flight_list);exit;
		} else {
			// FIXME - Arjun
			echo 'Update Price Details - Arjun';
			exit ();
			// /4f19b5ff77de902710834170632975d9
		}
		// If Round way international then make combinations else return as it is.
		if ((empty ( $search_data ['is_domestic'] ) == true && $search_data ['trip_type'] == 'circle') == true) {
			// Combine data//0,1,2,3,4,5
			$response ['flight_data_list'] ['journey_list'] [0] = Common_Api_Flight::form_flight_combination ( $flight_list ['journey_list'] [0], $flight_list ['journey_list'] [1] );
		} elseif ($search_data ['trip_type'] == 'multicity') {
			// Need to do dynamic combination
			if (valid_array ( $flight_list ['journey_list'] )) {
				$journey_cnt = COUNT ( $flight_list ['journey_list'] );
				$list = '';
				if (COUNT ( $search_data ['from'] ) == $journey_cnt) {
					for($i = 0; $i < $journey_cnt; $i ++) {
						if (valid_array ( $list )) {
							$onward = $list;
						} else {
							$onward = $flight_list ['journey_list'] [$i];
						}
						
						if (isset ( $flight_list ['journey_list'] [$i + 1] )) {
							$list = Common_Api_Flight::form_flight_combination ( $onward, $flight_list ['journey_list'] [$i + 1] );
						}
					}
				}
				
				$list = array_slice ( $list, 0, 41 );
				$response ['flight_data_list'] ['journey_list'] [0] = $list;
			}
			// $response ['flight_data_list'] ['journey_list'] [0] = Common_Api_Flight::form_flight_combination ( $flight_list ['journey_list'] [0], $flight_list ['journey_list'] [1] );
		} else {
			$response ['flight_data_list'] = $flight_list;
		}
		return $response;
	}
	
	/**
	 * check special fare
	 *
	 * @param array $segments        	
	 * @return boolean[]|string[]
	 */
	function journey_class($segments) {
		$res = array (
				'journey_class' => false, // special behaviour class
				'tag' => '' 
		); // Label for print only
		
		$product_class = '';
		if(valid_array($segments)) {
			foreach($segments as $s_k => $fare) {
				if(isset($fare['ProductClass'])) {
					$product_class = $fare['ProductClass'];
					break;
				}
			}
		}
		
//		if (isset ( $segments [0] ['Fares'] ['Fare'] ['ProductClass'] ) == true) {
//			$product_class = $segments [0] ['Fares'] ['Fare'] ['ProductClass'];
//		} else if (isset ( $segments [0] ['Fares'] ['Fare'] [0] ['ProductClass'] ) == true) {
//			$product_class = $segments [0] ['Fares'] ['Fare'] [0] ['ProductClass'];
//		}
		
		if ((($this->is_special_request == true && $this->carrier_code = '6E') || $this->carrier_code == 'SG') && $this->master_search_data ['domestic_round_trip'] == true) {
			if (strcasecmp ( $product_class, $this->SPECIAL_RETURN_CLASS ) == 0) {
				$res ['journey_class'] = $this->DRM;
				$res ['tag'] = 'RT-SPECIAL';
			}
		} else {
			// just add label - tag
			$res ['tag'] = $this->fare_type_class ( $product_class );
		}
		
		return $res;
	}
	function fare_type_class($product_class) {
		return '';
	}
	
	/**
	 * Format price data RS
	 */
	function format_price_data_response($price_result) {
		// debug($price_result);echo '$response';
		// ArrayOfJourneyDateMarket - Journey1, Journey2, Journey3...
		$Journey = $price_result ['s:Envelope'] ['s:Body'] ['PriceItineraryResponse'] ['Booking'] ['Journeys'] ['Journey'];
		$Journey = force_multple_data_format ( $Journey );
		$price_list = array ();
		foreach ( $Journey as $j_k => $j_v ) {
			/*
			 * debug($Journey[0]);
			 * exit;
			 */
			$journey_key = $j_v ['JourneySellKey'];
			$Segment = force_multple_data_format ( $j_v ['Segments'] ['Segment'] );
			foreach ( $Segment as $s_k => $s_v ) {
				$segment_sell_key = $s_v ['SegmentSellKey'];
				$Fare = force_multple_data_format ( $s_v ['Fares'] ['Fare'] );
				
				foreach ( $Fare as $f_k => $f_v ) {
					$f_k = $segment_sell_key . DB_SAFE_SEPARATOR . $f_v ['FareSellKey'];
					$price_list [$journey_key] ['fare'] [$f_k] = $f_v;
				}
			}
		}
		$response ['data'] = $price_list;
		// debug($response);
		// exit;
		return $response;
	}
	
	/**
	 *
	 * @param $flight_keys Custom
	 *        	key object
	 * @param $raw_fare_object Raw
	 *        	fare object returned by API
	 *        	
	 *        	$key_details = array(
	 *        	array('journey_sell_key' => '',
	 *        	'fare' => array(
	 *        	array('fare_sell_key' => '', 'is_allotment_market_fare' => '')
	 *        	)
	 *        	)
	 *        	);
	 */
	function itinerary_key_details($flight_keys, $raw_fare_object) {
		$key_details = '';
		foreach ( $flight_keys as $journey_number => $v ) {
			foreach ( $v ['JourneySellKey'] as $jk => $jv ) {
				$key_obj = array ();
				$key ['journey_sell_key'] = $jv;
				$key ['fare'] = array ();
				foreach ( $v ['FareSellKey'] as $fk => $fv ) {
					$fare = array ();
					$fare ['fare_sell_key'] = $fv;
					$fare ['segment_sell_key'] = $v ['SegmentSellKey'] [$fk];
					$fare ['is_allotment_market_fare'] = (@isset ( $raw_fare_object [$fk] ['IsAllotmentMarketFare'] ) ? $raw_fare_object [$fk] ['IsAllotmentMarketFare'] : false);
					$fare ['flight_number'] = $v ['FlightNumber'] [$fk];
					$fare ['is_intl'] = $v ['is_intl'] [$fk];
					$key ['fare'] [] = $fare;
				}
			}
		}
		return $key;
	}
	
	/**
	 * update price details
	 *
	 * @param array $flight_list        	
	 * @param array $price_list        	
	 */
	function update_search_itinerary_price_details(& $flight_list, $price_list, $infant_price = array(), $trip_type = 'domestic') {
		foreach ( $flight_list ['journey_list'] as $j_k => & $j_v ) {
			foreach ( $j_v as $f_k => &$f_v ) {
				$keys = $f_v ['key']; // summary of keys
				foreach ( $keys as $key ) {
					$journey_key = $key ['JourneySellKey'] [0];
					foreach ( $key ['FareSellKey'] as $k_k => & $k_v ) {
						$segment_key = $key ['SegmentSellKey'] [$k_k]; // need to read from segment and not journey directly
						$fare_sell_key = $k_v;
						$f_k = $segment_key . DB_SAFE_SEPARATOR . $fare_sell_key;
//						$f_v ['raw'] ['fare'] [$f_k] = $price_list [$journey_key] ['fare'] [$f_k];
						
						foreach($price_list as $p__k => $price_l) {
							if(isset($price_l [$journey_key] ['fare'] [$f_k])) {
								$f_v ['raw'] ['fare'] [$f_k] = $price_l [$journey_key] ['fare'] [$f_k];
								break;
							}
							
						}
						$this->get_price_details ( $f_v ['price'], $f_v ['raw'] ['fare'] [$f_k], false, array (), $trip_type );
					}
				}
				if ($this->master_search_data ['infant_config'] > 0) {
					$infant_price_value = 0;
					if ($this->master_search_data ['is_domestic'] == SUCCESS_STATUS) {
						$infant_price_value = $infant_price ['domestic'];
					} else {
						$infant_price_value = $infant_price ['international'];
					}
					$f_v ['price'] ['api_total_display_fare'] += $infant_price_value;
					$f_v ['price'] ['total_breakup'] ['api_total_fare'] += $infant_price_value;
					$f_v ['price'] ['passenger_breakup'] ['INF'] = array (
							'base_price' => 0,
							'total_price' => ($infant_price_value * $this->master_search_data ['infant_config']),
							'tax' => ($infant_price_value * $this->master_search_data ['infant_config']),
							'pass_no' => $this->master_search_data ['infant_config'] 
					);
				}
				$f_v ['passenger_breakup'] = $f_v ['price'] ['passenger_breakup'];
				$f_v ['fare'] [0] = $f_v ['price'];
				// Create api_price_attributes
				// Rename one tag for adding markup
			}
		}
	}
	
	/**
	 * Delete function
	 *
	 * @return string
	 */
	function trip_summary() {
		return '<br><h1>' . $this->master_search_data ['from_city'] . ' to ' . $this->master_search_data ['to_city'] . ', Type : ' . $this->master_search_data ['trip_type'] . '</h1></br>';
	}
	
	/**
	 * Get flight details only
	 *
	 * @param array $segment        	
	 */
	private function flight_segment_summary($segments, $journey_number, & $key) {
		
		$CI = & get_instance ();
		$summary = array ();
		$flight_details = array ();
		$price_details = array ();
		$final_flight = array();
		
		// Loop on data to form details array
		$details = array ();
		$SegmentSellKey = array ();
		$flightNumberList = array ();
		$segment_intl = array ();
		$FareSellKey = array ();
		$price = $this->get_price_object ();
		$fare_object = array ();
		$total_stop_count = - 1;
		foreach ( $segments as $k => $v ) {
			// legs Loop
			$legs = force_multple_data_format ( $v ['Legs'] ['Leg'] ); // leg
			$is_leg = true;
			$attr = array ();
			$attr ['action_status_code'] = $v ['ActionStatusCode'];
			foreach ( $legs as $l_k => $l_v ) {
				$total_stop_count ++;
				$origin_code = $l_v ['DepartureStation'];
				$destination_code = $l_v ['ArrivalStation'];
				$departure_dt = db_current_datetime ( $l_v ['STD'] );
				$arrival_dt = db_current_datetime ( $l_v ['STA'] );
				$no_of_stops = 0;
				
				$cabin_class = 'Economy';
				$operator_code = $l_v ['FlightDesignator'] ['a:CarrierCode'];
				$operator_name = $this->operator;
				$flight_number = $l_v ['FlightDesignator'] ['a:FlightNumber'];
				
				$details [] = $this->format_summary_array ( $journey_number, $origin_code, $destination_code, $departure_dt, $arrival_dt, $operator_code, $operator_name, $flight_number, $no_of_stops, $cabin_class, '', '', '', $is_leg, $attr );
				$SegmentSellKey [] = $v ['SegmentSellKey'];
				$flightNumberList [] = $l_v ['FlightDesignator'] ['a:FlightNumber'];
				$is_leg = false;
			}
			$segment_intl [] = $v ['International'];
			// Fare Loop
			$fares = force_multple_data_format ( $v ['Fares'] ['Fare'] );
//			if(COUNT($fares) > 1) {
//				debug($key);
//				
//			}
			
		
			foreach ( $fares as $f_k => $f_v ) {
				$fare[$f_k][$f_v ['FareSellKey']] = $f_v;
				$FareSellKey[$f_k][] = $f_v ['FareSellKey'];
			}
		}
		
		$first_flight = reset ( $segments );
		$last_flight = end ( $segments );
		
		$origin_code = $first_flight ['DepartureStation'];
		$destination_code = $last_flight ['ArrivalStation'];
		$departure_dt = db_current_datetime ( $first_flight ['STD'] );
		$arrival_dt = db_current_datetime ( $last_flight ['STA'] );
		$no_of_stops = $total_stop_count;
		$cabin_class = 'Economy';
		$operator_name = $this->operator;
		$operator_code = $first_flight ['FlightDesignator'] ['a:CarrierCode'];
		$flight_number = $first_flight ['FlightDesignator'] ['a:FlightNumber'];
		
		$summary = $this->format_summary_array ( $journey_number, $origin_code, $destination_code, $departure_dt, $arrival_dt, $operator_code, $operator_name, $flight_number, $no_of_stops, $cabin_class );
		 
		$flight_details ['summary'] [] = $summary; // forcing to be multiple format
		$flight_details ['details'] [0] = $details;
		$key ['key'] [$journey_number] ['SegmentSellKey'] = $SegmentSellKey;
//		$key ['key'] [$journey_number] ['FareSellKey'] = $FareSellKey;
		$key ['key'] [$journey_number] ['FlightNumber'] = $flightNumberList;
		$key ['key'] [$journey_number] ['is_intl'] = $segment_intl;
		
//		$response ['flight_details'] = $flight_details;
//		$response ['price'] = $price;
//		$response ['raw'] ['fare'] = $fare;
		
		foreach($fare as $f_key => $fare_flight) {
//			if($f_key == 0) {
				$flight_ar ['flight_details'] = $flight_details;
				$flight_ar ['price'] = $price;
				$flight_ar ['raw'] ['fare'] = $fare_flight;
				$flight_ar ['attr'] = $this->journey_class ( $fare_flight );
				
				$is_refundable = 'Refundable';
				if(strcasecmp ('Sale Fare', $flight_ar ['attr']['tag']) == 0) {
					$is_refundable = 'Non-Refundable';
				}
				$flight_ar ['attr']['isrefundable'] = $is_refundable; // Except Sale fare, all fares are refundable
				$flight_ar ['attr'] ['operator'] = $this->operator;
				$key ['key'] [$journey_number] ['FareSellKey'] = $FareSellKey[$f_key];
				$flight_ar ['key'] = $key ['key'];
				$flight_ar ['token'] = serialized_data ( $flight_ar ['key'] );
				$flight_ar ['token_key'] = md5 ( $flight_ar ['token'] );
				$final_flight[] = $flight_ar;
//			}
		}
		
//		if(COUNT($fares) > 1) {
//			debug($final_flight);
//			exit;
//		}
		$response = $final_flight;
		
		return $response;
	}
	
	/**
	 * Get price break up details
	 *
	 * @param array $price
	 *        	Price array containing flight price details
	 * @param array $fare_object
	 *        	Fare object returned in get itinerary price details
	 */
	function get_price_details(& $price, $fare_object, $is_intl = 'false', $ssr_infant_fare = array(), $trip_type = 'domestic') {
		// Get Total Fare
		$fare_value = array ();
		$tax_value = array ();
		$api_total_fare = $api_total_tax = 0;
		$base_fare = 0;
		$fuel_charge = 0;
		$raw_fare = array ();
		$passenger_price_breakup = array ();
		/*
		 * if (isset ( $fare_object ['PaxFares'] ) == false || isset ( $fare_object ['PaxFares'] ['paxFare'] ) == false) {
		 * return true;
		 * }
		 */
		if (is_array ( $fare_object ['PaxFares'] )) {
			$paxFare = force_multple_data_format ( $fare_object ['PaxFares'] ['PaxFare'] );
			$search ['ADT'] = 0; // $this->master_search_data['adult_config'];////Pass sum of adult and child if seperate breakup is not available for pax
			$search ['CHD'] = 0; // $this->master_search_data['child_config'];////Pass sum of adult and child if seperate breakup is not available for pax
			foreach ( $paxFare as $p_k => $p_v ) {
				
				// decrement pax count
				$pax_type = $p_v ['PaxType'];
				++ $search [$pax_type];
				$booking_charge = force_multple_data_format ( $p_v ['ServiceCharges'] ['BookingServiceCharge'] );
				$fare_break = array ();
				foreach ( $booking_charge as $bc_k => $bc_v ) {
					// seperate money
					// --ChargeType
					$fare_break [$bc_v ['ChargeType']] [] = $bc_v;
				}
				// FarePrice -- base fare
				$fare_value [$pax_type] = 0;
				if (isset ( $fare_break ['FarePrice'] )) {
					foreach ( $fare_break ['FarePrice'] as $f_k => $f_v ) {
						$fare_value [$pax_type] += $f_v ['Amount'];
						$base_fare += $f_v ['Amount'];
						@($raw_fare ['Base Fare'] += $f_v ['Amount']);
					}
					unset ( $fare_break ['FarePrice'] );
				}
				
				$tax_value [$pax_type] = 0;
				if (valid_array ( $fare_break )) {
					foreach ( $fare_break as $fb_k => $fb_v ) {
						foreach ( $fb_v as $tv ) {
							if (strcmp ( 'YQ', $tv ['ChargeCode'] ) == 0 || strcmp ( 'YQ', $tv ['TicketCode'] ) == 0) {
								$fuel_charge += $tv ['Amount'];
							}
							$tax_value [$pax_type] += $tv ['Amount'];
							if (empty ( $tv ['ChargeCode'] ) == true) {
								$raw_fare ['Other Charges'] += $tv ['Amount'];
							}
							$raw_fare [$tv ['ChargeCode']] += $tv ['Amount'];
						}
					}
				}
			}
			/*
			 * debug($fare_value);
			 * debug($tax_value);
			 */
			
			// adult
			$adult_fare = 0;
			$adult_tax = 0;
			// search has adults
			if ($this->master_search_data ['adult_config'] > 0) {
				$cal_adult_fare = isset ( $fare_value ['ADT'] ) ? ($fare_value ['ADT']) : 0;
				$cal_adult_tax = isset ( $tax_value ['ADT'] ) ? $tax_value ['ADT'] : 0;
				// convert to single pax price
				$this->convert_to_pax_price ( $cal_adult_fare, $cal_adult_tax, $search ['ADT'], $this->master_search_data ['adult_config'], $adult_fare, $adult_tax );
				
				$passenger_price_breakup ['ADT'] = array (
						'base_price' => $adult_fare,
						'total_price' => ($adult_fare + $adult_tax),
						'tax' => $adult_tax,
						'pass_no' => $this->master_search_data ['adult_config'] 
				);
			}
			
			// child
			$child_fare = 0;
			$child_tax = 0;
			// search has child
			if ($this->master_search_data ['child_config'] > 0) {
				$cal_child_fare = isset ( $fare_value ['CHD'] ) ? $fare_value ['CHD'] : $cal_adult_fare;
				$cal_child_tax = isset ( $tax_value ['CHD'] ) ? $tax_value ['CHD'] : $cal_adult_tax;
				
				// 75% of total price
				// if($trip_type == 'international') {
				// $cal_child_fare = round(($cal_child_fare * 75)/100);
				// $cal_child_tax = round(($cal_child_tax * 75)/100);
				// }
				// convert to single pax price
				$this->convert_to_pax_price ( $cal_child_fare, $cal_child_tax, ($search ['CHD'] > 0 ? $search ['CHD'] : $search ['ADT']), $this->master_search_data ['child_config'], $child_fare, $child_tax );
				$passenger_price_breakup ['CNN'] = array (
						'base_price' => $child_fare,
						'total_price' => ($child_fare + $child_tax),
						'tax' => $child_tax,
						'pass_no' => $this->master_search_data ['child_config'] 
				);
			}
			
			$api_total_fare = $adult_fare + $child_fare;
			$api_total_tax = $adult_tax + $child_tax;
		}
		
		$price ['api_currency'] = get_application_default_currency ();
		$price ['api_total_display_fare'] += ($api_total_fare + $api_total_tax);
		$price ['total_breakup'] ['api_total_fare'] += $api_total_fare;
		$price ['total_breakup'] ['api_total_tax'] += $api_total_tax;
		$price ['price_breakup'] ['basic_fare'] = $base_fare;
		$price ['price_breakup'] ['fuel_charge'] = $fuel_charge;
		$price ['passenger_breakup'] = $passenger_price_breakup;
		// This it only for tool tip on search result page
		$price ['screen_price_breakup'] = $raw_fare;
	}
	
	/**
	 *
	 * @param
	 *        	$fare
	 * @param
	 *        	$tax
	 * @param
	 *        	$price_for_pax_count
	 * @param
	 *        	$actual_pax_count
	 * @param
	 *        	$total_pax_fare
	 * @param
	 *        	$total_pax_tax
	 */
	private function convert_to_pax_price($fare, $tax, $price_for_pax_count, $actual_pax_count, & $total_pax_fare, & $total_pax_tax) {
		if ($fare > 0) {
			$total_pax_fare = ($fare / $price_for_pax_count) * $actual_pax_count;
		}
		
		if ($tax > 0) {
			$total_pax_tax = ($tax / $price_for_pax_count) * $actual_pax_count;
		}
	}
	
	/**
	 *
	 * @param unknown_type $search_id        	
	 */
	public function search_data($search_id) {
		$response ['status'] = true;
		$response ['data'] = array ();
		$CI = & get_instance ();
		if (empty ( $this->master_search_data ) == true and valid_array ( $this->master_search_data ) == false) {
			$clean_search_details = $CI->flight_model->get_safe_search_data ( $search_id );
			if ($clean_search_details ['status'] == true) {
				$response ['status'] = true;
				$response ['data'] = $clean_search_details ['data'];
				// 28/12/2014 00:00:00 - date format
				/*
				 * $response['data']['from'] = substr(chop(substr($clean_search_details['data']['from'], -5), ')'), -3);
				 * $response['data']['to'] = substr(chop(substr($clean_search_details['data']['to'], -5), ')'), -3);
				 */
				
				if ($clean_search_details ['data'] ['trip_type'] == 'multicity') {
					$response ['data'] ['from'] = $clean_search_details ['data'] ['from'];
					$response ['data'] ['to'] = $clean_search_details ['data'] ['to'];
					$response ['data'] ['from_city'] = $clean_search_details ['data'] ['from'];
					$response ['data'] ['to_city'] = $clean_search_details ['data'] ['to'];
					$response ['data'] ['depature'] = $clean_search_details ['data'] ['depature'];
					$response ['data'] ['return'] = $clean_search_details ['data'] ['depature'];
				} else {
					$response ['data'] ['from'] = substr ( chop ( substr ( $clean_search_details ['data'] ['from'], - 5 ), ')' ), - 3 );
					$response ['data'] ['to'] = substr ( chop ( substr ( $clean_search_details ['data'] ['to'], - 5 ), ')' ), - 3 );
					$response ['data'] ['from_city'] = $clean_search_details ['data'] ['from'];
					$response ['data'] ['to_city'] = $clean_search_details ['data'] ['to'];
					$response ['data'] ['depature'] = date ( "Y-m-d", strtotime ( $clean_search_details ['data'] ['depature'] ) ) . 'T00:00:00';
					$response ['data'] ['return'] = date ( "Y-m-d", strtotime ( $clean_search_details ['data'] ['depature'] ) ) . 'T00:00:00';
				}
				
				// $response ['data'] ['from_city'] = $clean_search_details ['data'] ['from'];
				// $response ['data'] ['to_city'] = $clean_search_details ['data'] ['to'];
				// $response ['data'] ['depature'] = date ( "Y-m-d", strtotime ( $clean_search_details ['data'] ['depature'] ) ) . 'T00:00:00';
				// $response ['data'] ['return'] = date ( "Y-m-d", strtotime ( $clean_search_details ['data'] ['depature'] ) ) . 'T00:00:00';
				switch ($clean_search_details ['data'] ['trip_type']) {
					
					case 'oneway' :
						$response ['data'] ['type'] = 'OneWay';
						break;
					
					case 'circle' :
						$response ['data'] ['type'] = 'Return';
						$response ['data'] ['return'] = date ( "Y-m-d", strtotime ( $clean_search_details ['data'] ['return'] ) ) . 'T00:00:00';
						break;
					case 'multicity' :
						$response ['data'] ['type'] = 'MultiCity';
						break;
					default :
						$response ['data'] ['type'] = 'OneWay';
				}
				
				if ($response ['data'] ['is_domestic'] == true and $response ['data'] ['trip_type'] == 'circle') {
					$response ['data'] ['domestic_round_trip'] = true;
				} else {
					$response ['data'] ['domestic_round_trip'] = false;
				}
				$response ['data'] ['adult'] = $clean_search_details ['data'] ['adult_config'];
				$response ['data'] ['child'] = $clean_search_details ['data'] ['child_config'];
				$response ['data'] ['infant'] = $clean_search_details ['data'] ['infant_config'];
				$response ['data'] ['ac_total_pax'] = ($clean_search_details ['data'] ['total_pax'] - $clean_search_details ['data'] ['infant_config']);
				$response ['data'] ['total_pax'] = $clean_search_details ['data'] ['total_pax'];
				$response ['data'] ['v_class'] = $clean_search_details ['data'] ['v_class'];
				$response ['data'] ['carrier'] = implode ( $clean_search_details ['data'] ['carrier'] );
				$this->master_search_data = $response ['data'];
			} else {
				$response ['status'] = false;
			}
		} else {
			$response ['data'] = $this->master_search_data;
		}
		$this->search_hash = md5 ( serialized_data ( $response ['data'] ) );
		return $response;
	}
	
	/**
	 * Update markup currency for price object of flight
	 *
	 * @param object $price_summary        	
	 * @param object $currency_obj        	
	 */
	function update_markup_currency(& $price_summary, & $currency_obj) {
	}
	
	/**
	 * get total price from summary object
	 *
	 * @param object $price_summary        	
	 */
	function total_price($price_summary) {
	}
	function post_passenger_booking($book_id, $temp_booking) {
		// debug($temp_booking['booking_step']);exit;
		$response ['status'] = FAILURE_STATUS;
		$response ['data'] = array ();
		$booking_id = $temp_booking ['tmp_flight_pre_booking_id'];
		$b_source = $this->source_code;
		
		// SellRequest
		$sell_request = $this->sell_request ( $book_id, $temp_booking );
		
		$CI = & get_instance ();
		$CI->custom_db->generate_static_response ( $sell_request ['payload'], $this->operator . ' sell RQ', $this->operator );
		
		// trigger exception in a "try" block
		try {
			$process_output = $this->process_request ( $sell_request ['payload'], $sell_request ['url'], $sell_request ['soap_action'] );
			
			$CI->custom_db->generate_static_response ( $process_output, $this->operator . ' Sell RS', $this->operator );
			$process_output = Converter::createArray ( $process_output );
			
			if ($this->valid_sell_response ( $process_output )) {
				$TotalCost = $process_output ['s:Envelope'] ['s:Body'] ['SellResponse'] ['BookingUpdateResponseData'] ['Success'] ['PNRAmount'] ['TotalCost'];
				
				$api_total_fare = $TotalCost;
				$data = array (
						'total_fare' => $TotalCost,
						'api_total_fare' => $api_total_fare 
				);
				$this->update_total_price ( $booking_id, $data, $b_source );
				$response ['status'] = SUCCESS_STATUS;
			}
		}		

		// catch exception
		catch ( Exception $e ) {
			$response ['status'] = FAILURE_STATUS;
		}
		return $response;
	}
	function seat_map_details($temp_booking, $passenger) {
		$response ['data'] = array ();
		$response ['status'] = FAILURE_STATUS;
		$formatted_seat_map_arr = array ();
		$pass = array ();
		$CI = &get_instance ();
		
		$seat_map_request = $this->get_seat_availability_request ( $passenger, $temp_booking );
		// debug($seat_map_request);
		if ($seat_map_request ['status'] == SUCCESS_STATUS) {
			if (valid_array ( $seat_map_request ['payload'] )) {
				foreach ( $seat_map_request ['payload'] as $p_k => $payload ) {
					
					$CI->custom_db->generate_static_response ( $payload, $this->operator . ' Seat Avilability RQ', $this->operator );
					$seat_map_response = $this->process_request ( $payload, $seat_map_request ['url'], $seat_map_request ['soap_action'] );
					$CI->custom_db->generate_static_response ( $seat_map_response, $this->operator . ' Seat Avilability RS', $this->operator );
					$seat_map_response = Converter::createArray ( $seat_map_response );
					// debug($seat_map_response);exit;
					if ($this->valid_seat_map_response ( $seat_map_response )) {
						$formatted_seat_map_details = $this->format_seat_map_response ( $seat_map_response );
						// debug($formatted_seat_map_details);exit;
						if (isset ( $formatted_seat_map_details )) {
							$formatted_seat_map_arr [$p_k] = $formatted_seat_map_details;
						}
					}
				}
				// $response['data'] = $formatted_seat_map_arr;
				$response ['data'] ['seat_map'] = $formatted_seat_map_arr;
				$response ['data'] ['booking_source'] = $this->source_code;
				$response ['status'] = SUCCESS_STATUS;
			}
		}
		return $response;
	}
	function valid_seat_map_response($seat_map_response) {
		if (isset ( $seat_map_response ['s:Envelope'] ['s:Body'] ['GetSeatAvailabilityResponse'] ['SeatAvailabilityResponse'] ['EquipmentInfos'] ['EquipmentInfo'] ['Compartments'] ['CompartmentInfo'] ['Seats'] ['SeatInfo'] ) && valid_array ( $seat_map_response ['s:Envelope'] ['s:Body'] ['GetSeatAvailabilityResponse'] ['SeatAvailabilityResponse'] ['EquipmentInfos'] ['EquipmentInfo'] ['Compartments'] ['CompartmentInfo'] ['Seats'] ['SeatInfo'] )) {
			return true;
		}
		return false;
	}
	
	/**
	 *
	 * @param array $passenger        	
	 * @param array $pax_iti_map        	
	 */
	function read_ssr($passenger, $temp_booking, & $pax_iti_map) {
		$response ['status'] = FAILURE_STATUS;
		$CI = & get_instance ();
		$passenger ['baggage'] = false;
		$passenger ['meals'] = false;
		
		$ssr_request = $this->get_ssr_availability_details ( $passenger, $temp_booking );
		$CI->custom_db->generate_static_response ( $ssr_request ['payload'], $this->operator . ' ssr availability', $this->operator );
		$meal_baggage_list = array ();
		
		// debug($seat_response);
		// exit;
		if ($ssr_request ['status'] == SUCCESS_STATUS) {
			try {
				$ssr_response = $this->process_request ( $ssr_request ['payload'], $ssr_request ['url'], $ssr_request ['soap_action'] );
				$ssr_response_array = Converter::createArray ( $ssr_response );
				
				$CI->custom_db->generate_static_response ( $ssr_response, $this->operator . ' ssr availability RS', $this->operator );
				
				if (isset ( $ssr_response_array ) && $this->valid_meal_availability ( $ssr_response_array )) {
					$available_ssr = $ssr_response_array ['s:Envelope'] ['s:Body'] ['GetSSRAvailabilityForBookingResponse'] ['SSRAvailabilityForBookingResponse'] ['SSRSegmentList'] ['SSRSegment'] ['AvailablePaxSSRList'] ['AvailablePaxSSR'];
					if (isset ( $available_ssr ) && valid_array ( $available_ssr )) {
						foreach ( $available_ssr as $__AK => $__ssr ) {
							if (isset ( $__ssr ['PaxSSRPriceList'] ['PaxSSRPrice'] ['PaxFee'] ['ServiceCharges'] ['BookingServiceCharge'] ) && valid_array ( $__ssr ['PaxSSRPriceList'] ['PaxSSRPrice'] ['PaxFee'] ['ServiceCharges'] ['BookingServiceCharge'] )) {
								$booking_service_charge = $__ssr ['PaxSSRPriceList'] ['PaxSSRPrice'] ['PaxFee'] ['ServiceCharges'] ['BookingServiceCharge'];
								$booking_service_charge = force_multple_data_format ( $booking_service_charge );
								$amount = 0;
								foreach ( $booking_service_charge as $f_k => $__charge ) {
									if ($__charge ['ChargeType'] == 'ServiceCharge') {
										$amount += @$__charge ['Amount'];
									}
								}
								$meal_baggage_list ["'" . $__ssr ['SSRCode'] . "'"] ['fare'] = $amount;
							}
							$meal_baggage_list ["'" . $__ssr ['SSRCode'] . "'"] ['code'] = "'" . $__ssr ['SSRCode'] . "'";
						}
					}
				}
				if ($passenger ['is_dom']) {
					$price_tag = 'domestic';
				} else {
					$price_tag = 'international';
				}
				
				$meal_array = array ();
				$baggage_array = array ();
				if (isset ( $meal_baggage_list ) && valid_array ( $meal_baggage_list )) {
					$meal_baggage_key = array_keys ( $meal_baggage_list );
					$meal_baggage_key = implode ( ',', $meal_baggage_key );
					$attributes = array (
							'meal_baggage_list' => $meal_baggage_key 
					);
					$meal_list = $this->read_meals ( $passenger, $attributes );
					
					if (isset ( $meal_list ['meal'] ) && valid_array ( $meal_list ['meal'] )) {
						foreach ( $meal_list ['meal'] as $n_k => $m_key ) {
							$meal_baggage_list ["'" . $m_key ['code'] . "'"] ['description'] = $m_key ['description'];
							$meal_array [$m_key ['code']] = $meal_baggage_list ["'" . $m_key ['code'] . "'"];
						}
					}
					$baggage_list = $this->read_baggage ( $passenger, $attributes );
					if (isset ( $baggage_list ['baggage'] ) && valid_array ( $baggage_list ['baggage'] )) {
						foreach ( $baggage_list ['baggage'] as $b_k => $baggeg ) {
							$meal_baggage_list ["'" . $baggeg ['code'] . "'"] ['description'] = $baggeg ['description'];
							$baggage_array [$baggeg ['code']] = $meal_baggage_list ["'" . $baggeg ['code'] . "'"];
						}
					}
					if ($baggage_list ['status'] == SUCCESS_STATUS) {
						$passenger ['baggage'] = $baggage_array;
					}
					if ($meal_list ['status'] == SUCCESS_STATUS) {
						$passenger ['meals'] = $meal_array;
					}
					
					$pax_iti_map [$passenger ['pax_index']] [] = $passenger;
				}
			} catch ( Exception $e ) {
				$response ['status'] = FAILURE_STATUS;
			}
		}
	}
	function sell_ssr($book_id, $temp_booking) {
		// error_reporting ( E_ALL );
		$response ['status'] = FAILURE_STATUS;
		$response ['data'] = array ();
		$booking_id = $temp_booking ['tmp_flight_pre_booking_id'];
		$b_source = $this->source_code;
		$CI = & get_instance ();
		
		$sell_ssr_request = $this->ssr_request ( $book_id, $temp_booking );
		if ($sell_ssr_request ['status'] == SUCCESS_STATUS) {
			$CI->custom_db->generate_static_response ( $sell_ssr_request ['payload'], $this->operator . ' sell ssr RQ', $this->operator );
			try {
				$sell_process_output = $this->process_request ( $sell_ssr_request ['payload'], $sell_ssr_request ['url'], $sell_ssr_request ['soap_action'] );
				$CI->custom_db->generate_static_response ( $sell_process_output, $this->operator . ' sell ssr RS', $this->operator );
				$sell_process_output = Converter::createArray ( $sell_process_output );
				if ($this->valid_sell_response ( $sell_process_output )) {
					// debug($sell_process_output);
					$TotalCost = $sell_process_output ['s:Envelope'] ['s:Body'] ['SellResponse'] ['BookingUpdateResponseData'] ['Success'] ['PNRAmount'] ['TotalCost'];
					$api_total_fare = $TotalCost;
					
					$fare_details = $CI->db->get_where ( 'flight_booking_transaction_details', array (
							'app_reference' => $booking_id,
							'booking_source' => $b_source 
					) )->row ();
					
					if (isset ( $fare_details->fare_attributes ) && ! empty ( $fare_details->fare_attributes )) {
						$fare_attributes = $fare_details->fare_attributes;
						$fare_attributes = json_decode ( $fare_attributes, TRUE );
						$CI->common_flight->update_markup_fare ( $booking_id, $b_source, $TotalCost, $fare_attributes );
						$data = array (
								'total_fare' => $TotalCost,
								'fare_attributes' => $fare_attributes,
								'api_total_fare' => $api_total_fare 
						);
						$response ['data'] = $data;
						$response ['status'] = SUCCESS_STATUS;
					}
				}
			} catch ( Exception $e ) {
				$response ['status'] = FAILURE_STATUS;
			}
		}
		return $response;
	}
	
	/**
	 * Process booking
	 *
	 * @param string $book_id        	
	 * @param array $booking_params
	 *        	Needed as token is not saved in database
	 */
	function process_booking($book_id, $temp_booking) {
		$response ['status'] = FAILURE_STATUS;
		$response ['data'] = array ();
		$booking_id = $temp_booking ['tmp_flight_pre_booking_id'];
		$b_source = $this->source_code;
		
		$CI = & get_instance ();
		$booking_commit = $this->booking_commit_request ( $temp_booking );
		$CI->custom_db->generate_static_response ( $booking_commit ['payload'], $this->operator . ' booking commit RQ', $this->operator );
		
		try {
			$booking_commit_output = $this->process_request ( $booking_commit ['payload'], $booking_commit ['url'], $booking_commit ['soap_action'] );
			$CI->custom_db->generate_static_response ( $booking_commit_output, $this->operator . ' booking commit RS', $this->operator );
			$booking_commit_output = Converter::createArray ( $booking_commit_output );
			
			if (isset ( $booking_commit_output ) && valid_array ( $booking_commit_output ) && isset ( $booking_commit_output ['s:Envelope'] ['s:Body'] ['BookingCommitResponse'] ['BookingUpdateResponseData'] ['Success'] )) {
				$booking_commit_response = $booking_commit_output ['s:Envelope'] ['s:Body'] ['BookingCommitResponse'] ['BookingUpdateResponseData'] ['Success'];
				$pnr = $booking_commit_output ['s:Envelope'] ['s:Body'] ['BookingCommitResponse'] ['BookingUpdateResponseData'] ['Success'] ['RecordLocator'];
				$TotalCost = $booking_commit_output ['s:Envelope'] ['s:Body'] ['BookingCommitResponse'] ['BookingUpdateResponseData'] ['Success'] ['PNRAmount'] ['TotalCost'];
				
				$api_total_fare = $TotalCost;
				
				if (isset ( $pnr ) && ! empty ( $pnr )) {
					$data = array (
							'status' => 'BOOKING_CONFIRMED',
							'pnr' => $pnr,
							'total_fare' => $TotalCost,
							'api_total_fare' => $api_total_fare 
					);
					$this->update_total_price ( $booking_id, $data, $b_source );
					
					$response ['data'] = $booking_commit_response;
					$response ['status'] = SUCCESS_STATUS;
				}
			} else {
				$data = array (
						'status' => 'BOOKING_ERROR' 
				);
				$this->update_total_price ( $booking_id, $data, $b_source );
			}
		} catch ( Exception $e ) {
			$response ['status'] = FAILURE_STATUS;
			$booking_commit_output = 'booking commit failed';
			$CI->custom_db->generate_static_response ( $booking_commit_output, $this->operator . ' Commit failed', $this->operator );
		}
		
		$this->remove_session ();
		return $response;
	}
	
	/**
	 * processing before going to paymwnt gateway
	 *
	 * @param
	 *        	$booking_id
	 * @param
	 *        	$temp_booking
	 * @param
	 *        	$b_source
	 */
	function pre_confirmation_process($booking_id, $temp_booking, $b_source, $b_s, $booking_src_array = array()) {
		$response ['status'] = FAILURE_STATUS;
		$response ['data'] = array ();
		
		if (count ( array_unique ( $booking_src_array ) ) == 1 && $b_s == 1) {
			$response ['status'] = SUCCESS_STATUS;
			return $response;
		}
		
		$request = $this->udpate_passenger_request ( $temp_booking );
		$CI = & get_instance ();
		$CI->custom_db->generate_static_response ( $request ['payload'], $this->operator . ' update passenger RQ' . $b_s, $this->operator );
		
		try {
			$process_output = $this->process_request ( $request ['payload'], $request ['url'], $request ['soap_action'] );
			$CI->custom_db->generate_static_response ( $process_output, $this->operator . ' update passenger RS', $this->operator );
			$process_output = Converter::createArray ( $process_output );
			
			if ($this->valid_update_passenger_response ( $process_output )) {
				$this->assign_seat ( $booking_id );
				
				$TotalCost = $process_output ['s:Envelope'] ['s:Body'] ['UpdatePassengerResponse'] ['BookingUpdateResponseData'] ['Success'] ['PNRAmount'] ['TotalCost'];
				$api_total_fare = $TotalCost;
				$data = array (
						'total_fare' => $TotalCost,
						'api_total_fare' => $api_total_fare 
				);
				$this->update_total_price ( $booking_id, $data, $b_source );
				$payment = $this->add_payment_to_booking ( $TotalCost );
				if ($payment ['status'] = SUCCESS_STATUS) {
					$response ['data'] = $payment;
					$response ['status'] = SUCCESS_STATUS;
				} else {
					$data = array (
							'status' => 'BOOKING_ERROR' 
					);
					$this->update_total_price ( $booking_id, $data, $b_source );
				}
			} else {
				$data = array (
						'status' => 'BOOKING_ERROR' 
				);
				$this->update_total_price ( $booking_id, $data, $b_source );
			}
		} catch ( Exception $e ) {
			$response ['status'] = FAILURE_STATUS;
		}
		return $response;
	}
	function assign_seat($booking_id) {
		// error_reporting(E_ALL);
		$seat_assignment_request = $this->assign_seats_request ( $booking_id );
		$CI = &get_instance ();
		// debug($seat_assignment_request);
		if ($seat_assignment_request ['status'] == SUCCESS_STATUS) {
			echo 'if';
			$CI->custom_db->generate_static_response ( $seat_assignment_request ['data'] ['pay_load'], $this->operator . ' Assign Seat RQ', $this->operator );
			$process_output = $this->process_request ( $seat_assignment_request ['data'] ['pay_load'], $seat_assignment_request ['data'] ['url'], $seat_assignment_request ['data'] ['soap_action'] );
			// debug($process_output);
			// exit;
			// try {
			$process_output = $this->process_request ( $seat_assignment_request ['data'] ['pay_load'], $seat_assignment_request ['data'] ['url'], $seat_assignment_request ['data'] ['soap_action'] );
			$CI->custom_db->generate_static_response ( $process_output, $this->operator . ' Assign Seat RS', $this->operator );
			echo 'try';
			debug ( $process_output );
			$process_output = Converter::createArray ( $process_output );
			debug ( $process_output );
			exit ();
			// }catch(Exception $e) {
			//
			// }
			// debug($seat_assignment_request);exit;
		}
	}
	function assign_seats_request($booking_id) {
		$response ['data'] = array ();
		$response ['status'] = FAILURE_STATUS;
		
		$CI = &get_instance ();
		// seat
		$seat_query = 'SELECT * FROM `flight_booking_passenger_details` as fp JOIN flight_booking_itinerary_details as fi ON fp.app_reference = fi.app_reference JOIN flight_booking_seat_details as fm WHERE fp.app_reference = "' . $booking_id . '" AND fp.origin = fm.p_origin AND fi.origin = fm.i_origin ';
		$seat_output = $CI->db->query ( $seat_query )->result_array ();
		
		if (isset ( $seat_output ) && valid_array ( $seat_output )) {
			$request = '';
			$segment = '';
			foreach ( $seat_output as $s_k => $seat ) {
				$std_date = date ( 'Y-m-d', strtotime ( $seat ['departure_datetime'] ) );
				$std_time = date ( 'H:i:s', strtotime ( $seat ['departure_datetime'] ) );
				$std = $std_date . 'T' . $std_time;
				$flight_no = sprintf ( "% 4s", $seat ['flight_number'] );
				
				$segment .= '<SegmentSeatRequest xmlns:a="http://schemas.microsoft.com/2003/10/Serialization/Arrays">
								<FlightDesignator>
									<a:CarrierCode>' . $seat ['airline_code'] . '</a:CarrierCode>
									<a:FlightNumber>' . $seat ['flight_number'] . '</a:FlightNumber>
									<a:OpSuffix i:nil="true"/>
								</FlightDesignator>
								<STD>' . $std . '</STD>
								<DepartureStation>' . $seat ['from_airport_code'] . '</DepartureStation>
								<ArrivalStation>' . $seat ['to_airport_code'] . '</ArrivalStation>
								<PassengerNumbers>
									<a:short>' . $s_k . '</a:short>
								</PassengerNumbers>
								<UnitDesignator>' . $seat ['seat'] . '</UnitDesignator>
								<CompartmentDesignator i:nil="true"/>
								<PassengerSeatPreferences i:nil="true"/>
								<PassengerIDs>
									<a:long>' . $s_k . '</a:long>
								</PassengerIDs>
								<RequestedSSRs i:nil="true"/>
							</SegmentSeatRequest>';
			}
			$segment_Seat_request = '<SegmentSeatRequests>
										' . $segment . '
									</SegmentSeatRequests>';
			
			$sell_Seat_Request = '<SellSeatRequest xmlns="http://schemas.navitaire.com/WebServices/DataContracts/Booking" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
									<BlockType>Session</BlockType>
									<SameSeatRequiredOnThruLegs>false</SameSeatRequiredOnThruLegs>
									<AssignNoSeatIfAlreadyTaken>false</AssignNoSeatIfAlreadyTaken>
									<AllowSeatSwappingInPNR>false</AllowSeatSwappingInPNR>
									<WaiveFee>false</WaiveFee>
									<ReplaceSpecificSeatRequest>false</ReplaceSpecificSeatRequest>
									<SeatAssignmentMode>' . $this->seat_assignment_mode . '</SeatAssignmentMode>
									<IgnoreSeatSSRs>false</IgnoreSeatSSRs>
									' . $segment_Seat_request . '
									<EquipmentDeviations i:nil="true"/>
									<CollectedCurrencyCode>INR</CollectedCurrencyCode>
								</SellSeatRequest>';
			
			$request .= '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/" >
							' . $this->soap_header () . '
							<s:Body>
								<AssignSeatsRequest xmlns="http://schemas.navitaire.com/WebServices/ServiceContracts/BookingService">
									' . $sell_Seat_Request . '
								</AssignSeatsRequest>
							</s:Body>
						</s:Envelope>';
			$response ['data'] ['pay_load'] = $request;
			$response ['data'] ['url'] = $this->config ['end_point'] ['booking'];
			$response ['data'] ['soap_action'] = 'http://schemas.navitaire.com/WebServices/IBookingManager/AssignSeats';
			$response ['status'] = SUCCESS_STATUS;
		}
		return $response;
	}
	function add_payment_to_booking($TotalCost) {
		$response ['status'] = FAILURE_STATUS;
		$response ['data'] = array ();
		
		$request = $this->add_payment_to_booking_request ( $TotalCost );
		$CI = & get_instance ();
		$CI->custom_db->generate_static_response ( $request ['payload'], $this->operator . ' add payment to booking RQ', $this->operator );
		
		try {
			$process_output = $this->process_request ( $request ['payload'], $request ['url'], $request ['soap_action'] );
			$CI->custom_db->generate_static_response ( $process_output, $this->operator . ' add payment to booking RS', $this->operator );
			$process_output = Converter::createArray ( $process_output );
			if (isset ( $process_output ) && valid_array ( $process_output ) && isset ( $process_output ['s:Envelope'] ['s:Body'] ['AddPaymentToBookingResponse'] ['BookingPaymentResponse'] )) {
				$response ['status'] = SUCCESS_STATUS;
				$response ['data'] = $process_output;
			}
		} catch ( Exception $e ) {
			$response ['status'] = FAILURE_STATUS;
		}
		return $response;
	}
	
	// Update price after sellrequest
	private function update_total_price($booking_id, $data, $b_source) {
		$CI = &get_instance ();
		$CI->db->where ( 'app_reference', $booking_id );
		$CI->db->where ( 'booking_source', $b_source );
		$CI->db->update ( 'flight_booking_transaction_details', $data );
		
		// update PNR
		return TRUE;
	}
	private function valid_sell_response($process_output) {
		if (isset ( $process_output ) && isset ( $process_output ['s:Envelope'] ['s:Body'] ['SellResponse'] ['BookingUpdateResponseData'] ['Success'] ['PNRAmount'] ) && valid_array ( $process_output ['s:Envelope'] ['s:Body'] ['SellResponse'] ['BookingUpdateResponseData'] ['Success'] ['PNRAmount'] )) {
			return true;
		} else {
			return false;
		}
	}
	private function valid_update_passenger_response($process_output) {
		// if(isset($process_output ['s:Envelope'] ['s:Body'] ['SellResponse'] ['BookingUpdateResponseData'] ['Success'] ['PNRAmount'] ['TotalCost']) && !empty($process_output ['s:Envelope'] ['s:Body'] ['SellResponse'] ['BookingUpdateResponseData'] ['Success'] ['PNRAmount'] ['TotalCost'])) {
		if (isset ( $process_output ) && isset ( $process_output ['s:Envelope'] ['s:Body'] ['UpdatePassengerResponse'] ['BookingUpdateResponseData'] ['Success'] ) && valid_array ( $process_output ['s:Envelope'] ['s:Body'] ['UpdatePassengerResponse'] ['BookingUpdateResponseData'] ['Success'] )) {
			return true;
		} else {
			return false;
		}
	}
	function format_seat_map_response($seat_map_details, $carrier, $flight_no) {
		$seat_group_arr = array ();
		$seat_group = $seat_map_details ['s:Envelope'] ['s:Body'] ['GetSeatAvailabilityResponse'] ['SeatAvailabilityResponse'] ['SeatGroupPassengerFees'] ['SeatGroupPassengerFee'];
		
		foreach ( $seat_group as $s_k => $seat ) {
			$seat_group_arr [$seat ['SeatGroup']] = $seat ['PassengerFee'];
		}
		// seats
		$new_Seat = array ();
		$seats = $seat_map_details ['s:Envelope'] ['s:Body'] ['GetSeatAvailabilityResponse'] ['SeatAvailabilityResponse'] ['EquipmentInfos'] ['EquipmentInfo'] ['Compartments'] ['CompartmentInfo'] ['Seats'] ['SeatInfo'];
		foreach ( $seats as $s_k => $seat_detail ) {
			$seat_detail ['seat_fees'] = $seat_group_arr [$seat_detail ['SeatGroup']];
			if ($seat_detail ['SeatAvailability'] == 'Open') {
				// sear charge total amount
				$amt = 0;
				if (isset ( $seat_detail ['seat_fees'] ) && valid_array ( $seat_detail ['seat_fees'] )) {
					$service_charge = $seat_detail ['seat_fees'] ['ServiceCharges'] ['BookingServiceCharge'];
					$service_charge = force_multple_data_format ( $service_charge );
					foreach ( $service_charge as $s_k => $service ) {
						$amt += $service ['Amount'];
						$currency = $service ['CurrencyCode'];
					}
				}
				$seat_detail ['seat_charge'] = $currency . ' ' . $amt;
				$seat_no = preg_replace ( "/[0-9]/", "", $seat_detail ['SeatDesignator'] );
				$new_Seat [( int ) $seat_detail ['SeatDesignator']] [$seat_no] = $seat_detail;
			}
		}
		ksort ( $new_Seat );
		
		$seat_pattern_array = array ();
		$seat_pattern = array (
				'A' => '',
				'B' => '',
				'C' => '',
				'=' => '',
				'D' => '',
				'E' => '',
				'F' => '' 
		);
		
		// debug($new_Seat);exit;
		$array_test = array ();
		foreach ( $new_Seat as $n_sk => $n_seat ) {
			$array_test [$n_sk] = array_merge ( $seat_pattern, $n_seat );
		}
		return $array_test;
	}
	/**
	 *
	 * @param object $request        	
	 * @param string $wsdl_url        	
	 * @param string $soap_action        	
	 *
	 */
	function process_request($request, $url, $soap_action) {
		$cs = curl_init ();
		curl_setopt ( $cs, CURLOPT_URL, $url );
		curl_setopt ( $cs, CURLOPT_TIMEOUT, 180 );
		// curl_setopt($cs, CURLOPT_HEADER, 0);
		curl_setopt ( $cs, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $cs, CURLOPT_POST, 1 );
		curl_setopt ( $cs, CURLOPT_POSTFIELDS, $request );
		curl_setopt ( $cs, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt ( $cs, CURLOPT_FOLLOWLOCATION, true );
		
		$header = array (
				"SOAPAction: {$soap_action}",
				"Content-Type: text/xml; charset=utf-8",
				"Accept-Encoding: gzip" 
		);
		curl_setopt ( $cs, CURLOPT_HTTPHEADER, $header );
		curl_setopt ( $cs, CURLOPT_ENCODING, "gzip" );
		
		// sending RQ to curl
		try {
			$response = curl_exec ( $cs );
		} catch ( Exception $e ) {
			$response = false;
		}
		$error = curl_error ( $cs );
		return $response;
	}
}
