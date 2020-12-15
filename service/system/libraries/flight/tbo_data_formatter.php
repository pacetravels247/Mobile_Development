<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Provab
 * @subpackage TBO API Data Formatter
 * @author     Jaganath<jaganath.provab@gmail.com>
 * @version    V1
 */

class Tbo_Data_Formatter
{
	// ****API Credentail Variables*****//
	protected $api_SiteName = '';
	protected $api_AccountCode = '';
	protected $api_UserName = '';
	protected $api_Password = '';
	protected $api_url = '';
	protected $api_ClientId = '';
	protected $api_LoginType = '';
	protected $api_EndUserIp = '127.0.0.1';
	protected $api_authentication_url = ''; // Only for Authentication
	public $credential_type;
	protected $cache_directory = '';
	protected $BookingSource = TBO_FLIGHT_BOOKING_SOURCE;
	// ****API Credentail Variables*****//
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->library('Api_Interface');
		$this->CI->load->model('flight_model');
		$this->cache_directory = realpath('../temp').'/flight_cache/tbo/';
	}
	function test()
	{
		$this->CI->exception_logger->log_exception('TBO-Data', 'TESTMODE', 'Data Formatter', 'TBO Data Formatter');
	}
	/**
	 * TEST API Credentails
	 */
	public function set_test_api_credentails()
	{
		$this->api_ClientId = 'ApiIntegrationNew';
		$this->api_LoginType = '2';
		$this->api_EndUserIp = '127.0.0.1';
		$this->api_SiteName = '';
		$this->api_AccountCode = '';
		
		//$this->api_UserName = 'latienterp';
		//$this->api_Password = 'latienterp@123';
		
		$this->api_UserName = 'accentria';
		$this->api_Password = 'accentria@123';
		
		$this->api_url = 'http://api.tektravels.com/BookingEngineService_Air/AirService.svc/rest/';
		$this->api_authentication_url = 'http://api.tektravels.com/SharedServices/SharedData.svc/rest/';
	}
	/**
	 * LIVE API Credentails
	 */
	protected function set_live_api_credentails()
	{
		$this->api_ClientId = 'tboprod';
		$this->api_LoginType = '2';
		$this->api_EndUserIp = '127.0.0.1';
		$this->api_SiteName = '';
		$this->api_AccountCode = '';
		//OLD LIVE CREDENTIALS
		//$this->api_UserName = 'BLRA177';
		//$this->api_Password = 'travel@*';
		
		//NEW LIVE CREDENTIALS
		$this->api_UserName = 'BLRA362';
		$this->api_Password = 'travel@362@';
		
		$this->api_url = 'http://tboapi.travelboutiqueonline.com/AirAPI_V10/AirService.svc/rest/';
		$this->api_authentication_url = 'http://api.travelboutiqueonline.com/SharedAPI/SharedData.svc/rest/';
	}
	/*
	 * Set API Credenatials Based on Environment(Test/Live)
	 */
	protected function set_api_credentials()
	{
		if (Flight_V10::get_credential_type() == 'test') {
			$this->set_test_api_credentails();
		} else if (Flight_V10::get_credential_type() == 'live') {
			$this->set_live_api_credentails();
		}
	}
	/**
	 * Generates Cache File name
	 */
	private function generate_cache_file_name()
	{
		return time().rand().rand().rand().rand();
	}
	/**
	 * Generates Authentication Key
	 */
	private function generate_provab_auth_key($cache_file_name, $cache_index, $TraceId)
	{
		return base64_encode($cache_file_name .AUTH_KEY_SEPARATOR.$cache_index.AUTH_KEY_SEPARATOR.$TraceId.AUTH_KEY_SEPARATOR.$this->BookingSource);
	}
	/**
	 * Extact the Proavb Auth Key details
	 */
	private function extract_provab_auth_key($provabAuthKey)
	{
		$auth_details = array();
		$provabAuthKey = explode(AUTH_KEY_SEPARATOR, $provabAuthKey);
		$cache_file_name = $provabAuthKey[0];
		$cache_index = $provabAuthKey[1];
		$trace_id = @$provabAuthKey[2];
		$booking_source = $provabAuthKey[3];
		
		$auth_details['cache_file_name'] = $cache_file_name;
		$auth_details['cache_index'] = $cache_index;
		$auth_details['trace_id'] = $trace_id;
		$auth_details['booking_source'] = $booking_source;
		return $auth_details;
	}
	/**
	 * Extract Booking Fare Details
	 * @param unknown_type $ProvabAuthKey
	 */
	public function extract_booking_flight_details($ProvabAuthKey)
	{
		$data['Status'] = FAILURE_STATUS;
		$data['data'] = '';
		$data['Message'] = '';
		$booking_data = $this->read_cache_data($ProvabAuthKey);
		if($booking_data['Status'] == SUCCESS_STATUS) {
			$temp_result[] = array($booking_data['data']);
			$journey_attributes = $this->get_journry_type_attributes($temp_result);
			$data['Status'] = SUCCESS_STATUS;
			$fare = $this->format_fare_object($booking_data ['data']['Fare']);
			$fare_breakdown = $this->farmat_pax_fare_object($booking_data ['data']['FareBreakdown']);
			$segments = $this->format_segment_object($booking_data ['data']['Segments']);
			
			$data['data']['ResultIndex'] = $booking_data['data']['ResultIndex'];
			$data['data']['Source'] = $booking_data['data']['Source'];
			$data['data']['IsLCC'] = $booking_data['data']['IsLCC'];
			$data['data']['IsRefundable'] = $booking_data['data']['IsRefundable'];
			$data['data']['AirlineRemark'] = $booking_data['data']['AirlineRemark'];
			$data['data']['JourneyAttributes'] = $journey_attributes;
			$data['data']['FareDetails'] = $fare;
			$data['data']['PassengerFareBreakdown'] = $fare_breakdown;
			$data['data']['SegmentDetails'] = $segments;
		} else {
			$data['Message'] = 'Invalid ProvabAuthKey';
		}
		return $data;
	}
	/**
	 * Extract Booking Fare Details
	 * @param unknown_type $ProvabAuthKey
	 */
	public function extract_booking_fare_details($ProvabAuthKey)
	{
		$data['Status'] = FAILURE_STATUS;
		$data['data'] = '';
		$data['Message'] = '';
		$booking_data = $this->read_cache_data($ProvabAuthKey);
		if($booking_data['Status'] == SUCCESS_STATUS) {
			//debug($booking_data);exit;
			$result[] = array($booking_data['data']);
			$journey_attributes = $this->get_journry_type_attributes($result);
			$data['Status'] = SUCCESS_STATUS;
			$fare = $this->format_fare_object($booking_data ['data']['Fare']);
			$fare_breakdown = $this->farmat_pax_fare_object($booking_data ['data']['FareBreakdown']);
			$data['data']['JourneyAttributes'] = $journey_attributes;
			$data['data']['Fare'] = $fare;
			$data['data']['FareBreakdown'] = $fare_breakdown;
		} else {
			$data['Message'] = 'Invalid ProvabAuthKey';
		}
		return $data;
	}
	/**
	 * Format Fare Rule Request
	 * @param $request_params
	 */
	protected function format_authenticate_request($request_params)
	{
		$data['Status'] = SUCCESS_STATUS;
		$data['request'] = '';
		$data['Message'] = '';
		$this->set_api_credentials();
		$request_data = array();
		$request_data ['ClientId'] = $this->api_ClientId;
		$request_data ['UserName'] = $this->api_UserName;
		$request_data ['Password'] = $this->api_Password;
		$request_data ['EndUserIp'] = $this->api_EndUserIp;
		$data['request'] = json_encode($request_data);
		return $data;
	}
	/**
	 * Format Search Request
	 * @param unknown_type $request
	 */
	protected function format_search_request($request_params)
	{
		$data['Status'] = SUCCESS_STATUS;
		$data['request'] = '';
		$data['Message'] = '';
		$this->set_api_credentials();
		$request_data ['EndUserIp'] = $this->api_EndUserIp;
		$request_data ['TokenId'] = $request_params['ApiToken']['TokenId'];
		$request_data ['AdultCount'] = $request_params['AdultCount'];
		$request_data ['ChildCount'] = $request_params['ChildCount'];
		$request_data ['InfantCount'] = $request_params['InfantCount'];
		$request_data ['DirectFlight'] = false;
		$request_data ['OneStopFlight'] = false;
		//SEGMENT DATA
		$segment_data = $this->get_search_request_segments($request_params);
		$request_data ['JourneyType'] = $this->get_journey_type($request_params['JourneyType']);
		if(isset($request_params['Sources']) == true && empty($request_params['Sources']) == false) {
			$request_data ['Sources'] = $request_params['Sources'];
		} else {
			$request_data ['Sources'] = null;
		}
		if(valid_array($request_params['PreferredAirlines']) == true && empty($request_params['PreferredAirlines'][0]) == false){
			//Overriding the Sources if PreferrefAIrlines are set
			//PreferredAirlnes will work only on GDS
			$request_data ['Sources'] = array('GDS');
			$PreferredAirlines = $request_params ['PreferredAirlines'];
		} else {
			$PreferredAirlines = null;
		}
		$request_data ['PreferredAirlines'] = $PreferredAirlines;
		$request_data ['Segments'] = $segment_data;
		$data['request'] = json_encode($request_data);
		return $data;
	}
	/*
	 * Formats Search Request Segments For Oneway/Roundway and Multi-Way
	 */
	protected function get_search_request_segments($request_params)
	{
		$segment_data = array();
		$segment_details = $request_params['Segments'];
		foreach ($segment_details as $k => $v) {
			$segments ['Origin'] = $v ['Origin'];
			$segments ['Destination'] = $v ['Destination'];
			$segments ['FlightCabinClass'] = $this->get_cabin_class_id($v['CabinClass']);
			$segments ['PreferredDepartureTime'] = date('Y-m-d', strtotime($v ['DepartureDate'])).'T00:00:00';
			$segment_data[$k] = $segments;
		}
		if($request_params['JourneyType'] == 'Return' || strtoupper($request_params['JourneyType']) == 'SPECIALRETURN') {//For Roundway
			$segment_data[1]['Origin'] = $segment_data[0]['Destination'];
			$segment_data[1]['Destination'] = $segment_data[0]['Origin'];
			$segment_data[1]['FlightCabinClass'] = $segment_data[0]['FlightCabinClass'];
			$segment_data[1]['PreferredDepartureTime'] = date('Y-m-d', strtotime($request_params['Segments'][0]['ReturnDate'])).'T00:00:00';;
		}
		return $segment_data;
	}
	/**
	 * Format Fare Rule Request
	 * @param $request_params
	 */
	protected function format_fare_rule_request($request_params)
	{
		
		$data['Status'] = SUCCESS_STATUS;
		$data['request'] = '';
		$data['Message'] = '';
		$cache_data = $this->read_cache_data($request_params['ProvabAuthKey']);
		if($cache_data['Status'] == true) {
			$cache_data = $cache_data['data'];
			$this->set_api_credentials();
			$request_data = array();
			$request_data ['EndUserIp'] = $this->api_EndUserIp;
			$request_data ['TokenId'] = $request_params['ApiToken']['TokenId'];
			$request_data ['TraceId'] = $cache_data['TraceId'];
			$request_data ['ResultIndex'] = $cache_data['ResultIndex'];
			$data['request'] = json_encode($request_data);
		} else {
			$data['Status'] = FAILURE_STATUS;
			$data['Message'] = 'Invalid Authentication Key';
		}
		return $data;
	}
	/**
	 * Format Fare Quote Request
	 * @param $request_params
	 */
	protected function format_fare_quote_request($request_params)
	{
		$data['Status'] = SUCCESS_STATUS;
		$data['request'] = '';
		$data['Message'] = '';
		$cache_data = $this->read_cache_data($request_params['ProvabAuthKey']);
		if($cache_data['Status'] == true) {
			$cache_data = $cache_data['data'];
			$this->set_api_credentials();
			$request_data = array();
			$request_data ['EndUserIp'] = $this->api_EndUserIp;
			$request_data ['TokenId'] = $request_params['ApiToken']['TokenId'];
			$request_data ['TraceId'] = $cache_data['TraceId'];
			$request_data ['ResultIndex'] = $cache_data['ResultIndex'];
			$data['request'] = json_encode($request_data);
		} else {
			$data['Status'] = FAILURE_STATUS;
			$data['Message'] = 'Invalid Authentication Key';
		}
		
		return $data;
	}
	/**
	 * Format SSR Request: Not Implemented
	 * @param $request_params
	 */
	protected  function format_ssr_request($request_params)
	{
		$data['Status'] = SUCCESS_STATUS;
		$data['request'] = '';
		$data['Message'] = '';
		$this->set_api_credentials();
		$request_data = array();
		$request_data ['EndUserIp'] = $this->api_EndUserIp;
		$request_data ['TokenId'] = $request_params['ApiToken']['TokenId'];
		$request_data ['TraceId'] = $request_params['ApiToken']['TraceId'];
		$request_data ['ResultIndex'] = $request_params['ResultIndex'];
		$data['request'] = json_encode($request_data);
		return $data;
	}

	/**
	 * Format Book Request
	 * @param $request_params
	 */
	protected function format_book_request($request_params)
	{
		$data['Status'] = SUCCESS_STATUS;
		$data['request'] = '';
		$data['Message'] = '';
		$cache_data = $this->read_cache_data($request_params['ProvabAuthKey']);
		if($cache_data['Status'] == true) {
			$cache_data = $cache_data['data'];
			$request_params = array_merge($request_params, $cache_data);
			$this->set_api_credentials();
			$request_data = array();
			$request_data ['EndUserIp'] = $this->api_EndUserIp;
			$request_data ['TokenId'] = $request_params['ApiToken']['TokenId'];
			$request_data ['TraceId'] = $cache_data['TraceId'];
			$request_data['ResultIndex'] = $cache_data['ResultIndex'];
			$request_data ['Passengers'] = $this->format_passenger_data($request_params);
			$data['request'] = json_encode($request_data);
		} else {
			$data['Status'] = FAILURE_STATUS;
			$data['Message'] = 'Invalid Authentication Key';
		}
		return $data;
	}
	protected function format_ticket_request($request_params)
	{
		$data['Status'] = SUCCESS_STATUS;
		$data['request'] = '';
		$data['Message'] = '';
		$cache_data = $this->read_cache_data($request_params['ProvabAuthKey']);
		if($cache_data['Status'] == true) {
			$cache_data = $cache_data['data'];
			$request_params = array_merge($request_params, $cache_data);
			$IsLCC = $request_params['IsLCC'];
			if($IsLCC == true) {
				//LCC Ticket Request
				$request_data = $this->format_lcc_ticket_request($request_params);
			} else if($IsLCC == false) {
				//NON-LCC Ticket Request
				$request_data = $this->format_non_lcc_ticket_request($request_params);
			}
			//debug($request_data);exit;
			$data['request'] = json_encode($request_data);
		} else {
			$data['Status'] = FAILURE_STATUS;
			$data['Message'] = 'Invalid Authentication Key';
		}
		return $data;
	}
	/**
	 * Format Ticket Request For LCC FLIGHTS
	 * @param $request_params
	 */
	protected function format_lcc_ticket_request($request_params)
	{
		$this->set_api_credentials();
		$request_data = array();
		$request_data ['EndUserIp'] = $this->api_EndUserIp;
		$request_data ['TokenId'] = $request_params['ApiToken']['TokenId'];
		$request_data ['TraceId'] = $request_params['TraceId'];
		$request_data['ResultIndex'] = $request_params['ResultIndex'];
		$request_data ['Passengers'] = $this->format_passenger_data($request_params);
		return $request_data;
	}
	/**
	 * Format Ticket Request For Non-LCC
	 * @param $request_params
	 */
	protected function format_non_lcc_ticket_request($request_params)
	{
		$this->set_api_credentials();
		$request_data = array();
		$request_data ['EndUserIp'] = $this->api_EndUserIp;
		$request_data ['TokenId'] = $request_params['ApiToken']['TokenId'];
		$request_data ['TraceId'] = $request_params['TraceId'];
		$request_data ['PNR'] = $request_params['PNR'];
		$request_data ['BookingId'] = $request_params['BookingId'];
		return $request_data;
	}
	/**
	 * Formats Passenger Details
	 * Assign the FareBreakdown for Each Passenger
	 */
	protected function format_passenger_data($request_params)
	{
		$fare_break_down = ($request_params['FareBreakdown']);//FareBreakdown
		$passengers = $request_params['Passengers'];
		$passenger_fare_breakdown = $this->assign_passenger_fare_breakdown ( $fare_break_down );
		$passenger_data = array();
		$IsLCC = $request_params['IsLCC'];
		//Default Passport Expiry
		$default_passport_expiry_date = date('Y-m-d', strtotime('+5 years'));
		foreach ( $passengers as $k => $v ) {
			//DOB
			if(isset($v['DateOfBirth']) == true && empty($v['DateOfBirth']) == false) {
				$pax_dob = $v['DateOfBirth'];
			} else {
				$pax_dob = $this->get_pax_default_dob($v['PaxType']);
			}
			//Passport
			if(isset($v['PassportExpiry']) == true && empty($v['PassportExpiry']) == false) {
				$passport_expiry = $v['PassportExpiry'];
			} else {
				$passport_expiry = $default_passport_expiry_date;
			}
			if(isset($v['PassportNumber']) == true && empty($v['PassportNumber']) == false) {
				$passport_number = $v['PassportNumber'];
			} else {
				$passport_number = rand(1111111111,9999999999);
			}
			//AddressLine2
			if(isset($v['AddressLine2']) == true && empty($v['AddressLine2']) == false) {
				$address_line2 = $v['AddressLine2'];
			} else {
				$address_line2 = 'Bangalore';
			}
			$passenger_data [$k] ['Title'] = $v['Title'];
			$passenger_data [$k] ['FirstName'] = $v['FirstName'];
			$passenger_data [$k] ['LastName'] = $v['LastName'];
			$passenger_data [$k] ['PaxType'] = $v['PaxType'];
			$passenger_data [$k] ['DateOfBirth'] = $pax_dob.'T00:00:00'; // optional
			$passenger_data [$k] ['Gender'] = $v['Gender'];
			//$passenger_data [$k] ['PassportNo'] = $passport_number;
			$passenger_data [$k] ['PassportNo'] = preg_replace('/\s+/', '', $passport_number);// optional
			
			$passenger_data [$k] ['PassportExpiry'] = $passport_expiry.'T00:00:00'; // optional
			$passenger_data [$k] ['AddressLine1'] = substr($v['AddressLine1'], 0, 31);
			$passenger_data [$k] ['AddressLine2'] = ''; // optional
			$passenger_data [$k] ['City'] = $v['City'];
			$passenger_data [$k] ['CountryCode'] = $v['CountryCode'];
			$passenger_data [$k] ['CountryName'] = $v['CountryName'];
			$passenger_data [$k] ['ContactNo'] = $this->validate_mobile_number($v['ContactNo']);
			$passenger_data [$k] ['Email'] = $v['Email'];
			if($v['IsLeadPax'] == 1) {
				$v['IsLeadPax'] = true;
			} else {
				$v['IsLeadPax'] = false;
			}
			$passenger_data [$k] ['IsLeadPax'] = $v['IsLeadPax'];
			$passenger_data [$k] ['FFAirline'] = null; // optional
			$passenger_data [$k] ['FFNumber'] = null; // optional
                        
                        
              $passenger_data [$k] ['GSTCompanyAddress'] = "Bangalore";
              $passenger_data [$k] ['GSTCompanyContactNumber'] = "9916100864";
              $passenger_data [$k] ['GSTCompanyName'] = "ACCENTRIA SOLUTIONS PRIVATE LIMITED"; 
              $passenger_data [$k] ['GSTNumber'] = "29AANCA8324M2Z0";
              $passenger_data [$k] ['GSTCompanyEmail'] = "vinay@travelomatix.com"; 
                        
			$passenger_data [$k] ['Fare'] = $passenger_fare_breakdown [$v ['PaxType']];
			if($IsLCC == true) {//'LCC FLIGHT
				$BaggageDetails = '';
				$MealsDetails = '';
				//Baggage Details
				$passenger_data [$k] ['Baggage'] = $this->formate_baggage_request_details($BaggageDetails);
				//Meals Details
				$passenger_data [$k] ['MealDynamic'] = $this->formate_meal_request_details($MealsDetails);
			} else if($IsLCC == false){//Non-LCC FLIGHT
				$meal ['Code'] = null; // optional
				$meal ['Description'] = null; // optional
				$seat ['Code'] = null; // optional
				$seat ['Description'] = null; // optional
				//$passenger_data [$k] ['Meal'] = $meal;
				//$passenger_data [$k] ['Seat'] = $seat;
			}
		}
		return $passenger_data;
	}
	/**
	 * Validates the mobile number
	 * @param unknown_type $mobile_number
	 */
	private function validate_mobile_number($mobile_number)
	{
		$mobile_number = trim($mobile_number);
		$mobile_number = ltrim($mobile_number, '0');
		if(strlen($mobile_number) < 10){
			$mobile_number_length = strlen($mobile_number);
			$required_extra_number_lengths = (10)-$mobile_number_length;
			$extra_numbers = str_repeat('0', $required_extra_number_lengths);
			$mobile_number =  $mobile_number.''.$extra_numbers;
		}
		return $mobile_number;
	
	}
	/**
	 * Returns Default Pax DOB
	 */
	private function get_pax_default_dob($PaxType)
	{
		$pax_type_label = $this->get_passenger_type($PaxType);
		switch($pax_type_label){
			case 'Adult':
				$dob = date('Y-m-d', strtotime('-30 years'));
				break;
			case 'Child':
				$dob = date('Y-m-d', strtotime('-8 years'));
				break;
			case 'Infant':
				$dob = date('Y-m-d', strtotime('-1 years'));
				break;
			default:
				$dob = date('Y-m-d', strtotime('-30 years'));
		}
		return $dob;
	}
	/**
	 * Baggage Details For LCC Flights
	 */
	protected function formate_baggage_request_details($BaggageDetails)
	{
		$baggage = array();
		$baggage ['WayType'] = 0;//NotSet = 0;Segment = 1;FullJourney = 2
		$baggage ['Code'] = '';//Baggage code
		$baggage ['Description'] = '';//[NotSet = 0;Included = 1;Direct = 2;Imported = 3;UpGrade = 4;ImportedUpgrade = 5
		$baggage ['Weight'] = '';
		$baggage ['Currency'] = '';
		$baggage ['Price'] = '';
		$baggage ['Origin'] = '';
		$baggage ['Destination'] = '';
		return $baggage;
	}
	/**
	 * Baggage Details For LCC Flights
	 */
	protected function formate_meal_request_details($MealDetails)
	{
		$meal = array();
		$meal ['WayType'] = 0;//NotSet = 0;Segment = 1;FullJourney = 2
		$meal ['Code'] = '';//Baggage code
		$meal ['Description'] = '';//[NotSet = 0;Included = 1;Direct = 2;Imported = 3;UpGrade = 4;ImportedUpgrade = 5
		$meal ['AirlineDescription'] = '';
		$meal ['Quantity'] = '';
		$meal ['Price'] = '';
		$meal ['Currency'] = '';
		$meal ['Origin'] = '';
		$meal ['Destination'] = '';
		return $meal;
	}
	/**
	 * Formats the Fare Request For Passenger-wise
	 *
	 * @param unknown_type $passenger_token
	 */
	protected function assign_passenger_fare_breakdown($passenger_token)
	{
		$passenger_token = force_multple_data_format ( $passenger_token );
		$Fare = array ();
		foreach ( $passenger_token as $k => $v ) {
			$Fare [$v ['PassengerType']] ['BaseFare'] = ($v ['BaseFare'] / $v ['PassengerCount']);
			$Fare [$v ['PassengerType']] ['Tax'] = ($v ['Tax'] / $v ['PassengerCount']);
			//$Fare [$v ['PassengerType']] ['TransactionFee'] = ($v ['TransactionFee'] / $v ['PassengerCount']);
			$Fare [$v ['PassengerType']] ['TransactionFee'] = 0;
			$Fare [$v ['PassengerType']] ['YQTax'] = ($v ['YQTax'] / $v ['PassengerCount']);
			$Fare [$v ['PassengerType']] ['AdditionalTxnFeeOfrd'] = ($v ['AdditionalTxnFeeOfrd'] / $v ['PassengerCount']);
			$Fare [$v ['PassengerType']] ['AdditionalTxnFeePub'] = ($v ['AdditionalTxnFeePub'] / $v ['PassengerCount']);
			//$Fare [$v ['PassengerType']] ['AirTransFee'] = ($v ['AirTransFee'] / $v ['PassengerCount']);
			$Fare [$v ['PassengerType']] ['AirTransFee'] = 0;
		}
		return $Fare;
	}
	/**
	 * Format GetBookingDetails Request
	 * @param $request_params
	 */
	protected function format_get_booking_details_request($request_params)
	{
		$data['Status'] = SUCCESS_STATUS;
		$data['request'] = '';
		$data['Message'] = '';
		$this->set_api_credentials();
		$request_data = array();
		$request_data ['EndUserIp'] = $this->api_EndUserIp;
		$request_data ['TokenId'] = $request_params['ApiToken']['TokenId'];
		$request_data ['BookingId'] = $request_params['BookingId'];
		$request_data['PNR'] = $request_params['PNR'];
		//$request_data ['TraceId'] = $request_params['ApiToken']['TraceId'];
		$data['request'] = json_encode($request_data);
		return $data;
	}
	/**
	 * NOT IMPLEMENTED
	 * Format ReleasePNR Request
	 * @param $request_params
	 */
	protected function format_release_pnr_request($request_params)
	{
		$data['Status'] = SUCCESS_STATUS;
		$data['request'] = '';
		$data['Message'] = '';
		$this->set_api_credentials();
		$request_data = array();
		$request_data ['EndUserIp'] = $this->api_EndUserIp;
		$request_data ['TokenId'] = $request_params['ApiToken']['TokenId'];
		$request_data ['BookingId'] = $request_params['BookingId'];
		$request_data['Source'] = $request_params['Source'];
		$data['request'] = json_encode($request_data);
		return $data;
	}
	/**
	 * FIXME: check Cancellation Method
	 * Format SendChange Request
	 * @param $request_params
	 */
	protected function format_send_change_request($request_params)
	{
		$data['Status'] = SUCCESS_STATUS;
		$data['request'] = '';
		$data['Message'] = '';
		$this->set_api_credentials();
		$request_data = array();
		$request_data ['EndUserIp'] = $this->api_EndUserIp;
		$request_data ['TokenId'] = $request_params['ApiToken']['TokenId'];
		$request_data ['BookingId'] = $request_params['BookingId'];
		if($request_params['IsFullBookingCancel'] == true) {
			$RequestType = 1;
			$TicketIds = null;
			$Sectors = null;
		} else if($request_params['IsFullBookingCancel'] == false) {
			$RequestType = 2;
			$TicketIds = $request_params['TicketId'];
			$Sectors = $request_params['Sectors'];
		}
		$request_data ['RequestType'] = 	$RequestType;//NotSet = 0;FullCancellation = 1;PartialCancellation = 2;Reissuance = 3
		$request_data ['CancellationType']= 0;//NotSet = 0;NoShow = 1;FlightCancelled = 2;Others = 3
		$request_data ['Sectors'] = 		$Sectors;//Mandatory only in case of partial cancellation
		$request_data ['TicketId'] = 		$TicketIds;//send multiple comma separated ticket id;Mandatory only in case of partial cancellations
		$request_data ['Remarks'] = 		trim($request_params['Remarks']);
		$data['request'] = json_encode($request_data);
		return $data;
	}
	/**
	 *
	 * Format GetChangeRequestStatus Request
	 * @param $request_params
	 */
	protected function format_get_change_request_status_request($request_params)
	{
		$data['Status'] = SUCCESS_STATUS;
		$data['request'] = '';
		$data['Message'] = '';
		$this->set_api_credentials();
		$request_data = array();
		$request_data ['EndUserIp'] = $this->api_EndUserIp;
		$request_data ['TokenId'] = $request_params['ApiToken']['TokenId'];
		$request_data ['ChangeRequestId'] = $request_params['ChangeRequestId'];
		$data['request'] = json_encode($request_data);
		return $data;
	}
	/**
	 * GetCalendarFare Request
	 * @param unknown_type $request_params
	 */
	protected function format_get_calendar_fare_request($request_params)
	{
		$data['Status'] = SUCCESS_STATUS;
		$data['request'] = '';
		$data['Message'] = '';
		$this->set_api_credentials();
		$request_data = array();
		$request_data ['EndUserIp'] = $this->api_EndUserIp;
		$request_data ['TokenId'] = $request_params['ApiToken']['TokenId'];
		$request_data ['JourneyType'] = $this->get_journey_type($request_params['JourneyType']);//Supports one way 1 only
		$request_data ['PreferredAirlines'] = $request_params['PreferredAirlines'];
		$segment_details = $request_params['Segments'];
		$segments ['Origin'] = $segment_details['Origin'];
		$segments ['Destination'] = $segment_details['Destination'];
		$segments ['FlightCabinClass'] = $this->get_cabin_class_id($segment_details['CabinClass']);
		$segments ['PreferredDepartureTime'] = date('Y-m-d', strtotime($segment_details['DepartureDate'])).'T00:00:00';
		$request_data ['Segments'][] = $segments;
		$request_data ['Sources'] = null;//Optinal
		$data['request'] = json_encode($request_data);
		return $data;
	}
	/**
	 * FIXME: same as GetCalendarFare
	 * UpdateCalendarFareOfDay Request
	 * @param unknown_type $request_params
	 */
	protected function format_get_update_calendar_fare_request($request_params)
	{
		$data['Status'] = SUCCESS_STATUS;
		$data['request'] = '';
		$data['Message'] = '';
		$this->set_api_credentials();
		$request_data = array();
		$request_data ['EndUserIp'] = $this->api_EndUserIp;
		$request_data ['TokenId'] = $request_params['ApiToken']['TokenId'];
		$request_data ['JourneyType'] = $this->get_journey_type($request_params['JourneyType']);//Supports one way 1 only
		$request_data ['PreferredAirlines'] = $request_params['PreferredAirlines'];
		$segment_details = $request_params['Segments'];
		$segments ['Origin'] = $segment_details['Origin'];
		$segments ['Destination'] = $segment_details['Destination'];
		$segments ['FlightCabinClass'] = $this->get_cabin_class_id($segment_details['CabinClass']);
		$segments ['PreferredDepartureTime'] = date('Y-m-d', strtotime($segment_details['DepartureDate'])).'T00:00:00';
		$request_data ['Segments'][] = $segments;
		$request_data ['Sources'] = null;//Optinal
		$data['request'] = json_encode($request_data);
		return $data;
	}
	/*
	 * Returns Journey Type
	 */
	private function get_journey_type($journey_type)
	{
		$type = '';
		switch(strtoupper($journey_type)) {
			case 'ONEWAY';
				$type = 1;
				break;
			case 'RETURN';
				$type = 2;
				break;
			case 'MULTICITY';
				$type = 3;
				break;
			case 'ADVANCEDSEARCH';
				$type = 4;
				break;
			case 'SPECIALRETURN';
				$type = 5;
				break;
		}
		return $type;
	}
	/**
	 * Get Cabin Class Search ID
	 * @param unknown_type $CabinClass
	 */
	private function get_cabin_class_id($CabinClass)
	{
		switch($CabinClass){//
			case 'All';
			$FlightCabinClass = 1;
			break;
			case 'Economy';
			$FlightCabinClass = 2;
			break;
			case 'PremiumEconomy';
			$FlightCabinClass = 3;
			break;
			case 'Business';
			$FlightCabinClass = 4;
			break;
			case 'PremiumBusiness';
			$FlightCabinClass = 5;
			break;
			case 'First';
			$FlightCabinClass = 6;
			break;
		}
		return $FlightCabinClass;
	}
	/*
	 * Get Passenger Type Label
	 */
	private function get_passenger_type($PassengerType)
	{
		$type = '';
		switch($PassengerType) {
			case 1;
			$type = 'Adult';
			break;
			case 2;
			$type = 'Child';
			break;
			case 3;
			$type = 'Infant';
			break;
		}
		return $type;
	}
	/********************FORMAT RESPONSE***********************************************/
	/**
	 * Journey Deetails
	 * @param unknown_type $Results
	 */
	protected function get_journry_type_attributes($Results)
	{
		$journey_attributes = array();
		$segments = $Results[0][0]['Segments'];
		$RoundTrip = false;
		$MultiCity = false;
		$MultiCitySegmentCount = 0;
		if(count($Results) == 1){
			$temp_segments = $Results[0][0]['Segments'];
			$last_temp_segment = end(end($temp_segments));
			$TripIndicator = $last_temp_segment['TripIndicator'];
			if(count($temp_segments) >= 2 && $TripIndicator == 1) {
				$MultiCity = true;
				$MultiCitySegmentCount = count($temp_segments);
			} else if(count($temp_segments) == 2 && $TripIndicator == 2) {
				$RoundTrip = true;//International Roundway
			}
		}else if(count($Results) == 2) {//Domestic Roundway
			$RoundTrip = true;
		}
		//Passenger Count
		$passenger_config = array();
		$TotalPassenger = 0;
		$passenger_config['Adult'] = 0;
		$passenger_config['Child'] = 0;
		$passenger_config['Infant'] = 0;
		$passenger_dtails = $Results[0][0]['FareBreakdown'];
		foreach($passenger_dtails as $k => $v){
			$passenger_config[$this->get_passenger_type($v['PassengerType'])] = $v['PassengerCount'];
			$TotalPassenger += $v['PassengerCount'];
		}
		$IsDomestic = $this->is_domestic_journey($segments);
		$passenger_config['TotalPassenger'] = $TotalPassenger;
		$journey_attributes['IsDomestic'] = $IsDomestic;
		$journey_attributes['RoundTrip'] = $RoundTrip;
		$journey_attributes['MultiCity'] = $MultiCity;
		$journey_attributes['MultiCitySegmentCount'] = $MultiCitySegmentCount;
		$journey_attributes['PassengerConfig'] = $passenger_config;
		return $journey_attributes;
	}
	/**
	 * Checks Is Domestic Journey
	 * Enter description here ...
	 */
	private function is_domestic_journey($segments)
	{
		$IsDomestic = true;
		foreach ($segments as $fp_k => $fp_v){
			foreach ($fp_v as $sg_k => $sg_v){
				$from_loc =	$sg_v['Origin']['Airport']['AirportCode'];
				$to_loc = 	$sg_v['Destination']['Airport']['AirportCode'];
				$is_domestic = $this->CI->flight_model->is_domestic_flight($from_loc, $to_loc);
				if($is_domestic == false){//If International
					$IsDomestic = false;
					return $IsDomestic;
				}
			}
		}
		return $IsDomestic;
	}
	/**
	 * Authentication Response
	 * @param unknown_type $response
	 */
	protected function format_authenticate_response($response)
	{
		$data['Status'] = SUCCESS_STATUS;
		$data['Message'] = '';
		$data['data'] = array();
		//Validate the Response
		if($response['Status'] == true && valid_array($response['data']) == true && isset($response['data']['Status']) == true && $response['data']['Status'] == SUCCESS_STATUS) {
			$response = $response['data'];
			$data['Status'] = SUCCESS_STATUS;
			$data['data']['TokenId'] = $response['TokenId'];
		} else {
			//Error Details
			$data['Status'] = FAILURE_STATUS;
			$data['Message'] = 'Invalid Authentication';
		}
		return $this->output_data($data);
	}
	/**
	 * Search Response
	 * @param unknown_type $response
	 */
	protected function format_search_response($response)
	{
		$data['Status'] = SUCCESS_STATUS;
		$data['Message'] = '';
		$data['data'] = array();
		//Validate the Response
		if($response['Status'] == true && valid_array($response['data']) == true && isset($response['data']['Response']) == true && $response['data']['Response']['ResponseStatus'] == SUCCESS_STATUS) {
			//debug($response);exit;
			$response = $response['data']['Response'];
			$Results = $response['Results'];
			$SearchResult =array();
			$FlightsData = array();
			$TraceId = $response['TraceId'];
			$Origin = $response['Origin'];
			$Destination = $response['Destination'];
			$cache_file_name = $this->generate_cache_file_name();
			$api_cache_data = array();
			//Journey Summary
			$IsDomestic = false;
			$RoundTrip = false;
			$MultiCity = false;
			$MultiCitySegmentCount = 0;
			$PassengerConfig = array();
			$IsDomestic = $this->CI->flight_model->is_domestic_flight($Origin, $Destination);
			$journry_type_attributes = $this->get_journry_type_attributes($Results);
			$RoundTrip = $journry_type_attributes['RoundTrip'];
			$MultiCity = $journry_type_attributes['MultiCity'];
			$MultiCitySegmentCount = $journry_type_attributes['MultiCitySegmentCount'];
			$PassengerConfig = $journry_type_attributes['PassengerConfig'];
			foreach($Results as $result_k => $result_v){
				$FlightList = array();
				foreach($result_v as $list_k => $list_v) {
					$cache_index = $list_k.'R'.$result_k;
					$ProvabAuthKey = $this->generate_provab_auth_key($cache_file_name, $cache_index, $TraceId);
					$api_cache_data[$cache_index] = $list_v;
					$api_cache_data[$cache_index]['ProvabAuthKey'] = $ProvabAuthKey;
					$api_cache_data[$cache_index]['TraceId'] = $response['TraceId'];
					$FlightDetails =array();
					$FlightDetails['ProvabAuthKey'] = $ProvabAuthKey;
					$FlightDetails['ResultIndex'] = $list_v['ResultIndex'];
					$FlightDetails['Source'] = $list_v['Source'];
					$FlightDetails['IsLCC'] = $list_v['IsLCC'];
					$FlightDetails['AirlineRemark'] = $list_v['AirlineRemark'];
					$FlightDetails['FareDetails'] = $this->format_fare_object($list_v['Fare']);
					$FlightDetails['PassengerFareBreakdown'] = $this->farmat_pax_fare_object($list_v['FareBreakdown']);//Passenger Fare Break down
					$FlightDetails['SegmentDetails'] = $this->format_segment_object($list_v['Segments']);
					$FlightDetails['IsRefundable'] = $list_v['IsRefundable'];
					$FlightList[$list_k] = $FlightDetails;
				}
				$FlightsData[$result_k] = $FlightList;
			}
			$SearchResult['Origin'] = $Origin;
			$SearchResult['Destination'] = $Destination;
			$SearchResult['IsDomestic'] = $IsDomestic;
			$SearchResult['RoundTrip'] = $RoundTrip;
			$SearchResult['MultiCity'] = $MultiCity;
			$SearchResult['PassengerConfig'] = $PassengerConfig;
			if($MultiCity) {
				$SearchResult['MultiCitySegmentCount'] = $MultiCitySegmentCount;
			}
			$SearchResult['Flights'] = $FlightsData;
			$data['Status'] = SUCCESS_STATUS;
			$data['data']['SearchResult'] = $SearchResult;
			$this->cache_api_response($cache_file_name, $api_cache_data);//Cache Search Data
		} else {
			//Error Details
			$data['Status'] = FAILURE_STATUS;
			$data['Message'] = 'Remote IO Error';
		}
		return $this->output_data($data);
	}
	/**
	 * Format Fare Details
	 */
	private function format_fare_object($Fare)
	{
		$FareDetails = array();
		unset($Fare['ChargeBU']);
		
		//Fare Summary
		$fare_summary = array();
		$fare_summary['Currency'] = $Fare['Currency'];
		$fare_summary['BaseFare'] = $Fare['BaseFare'];
		$fare_summary['Tax'] = $Fare['Tax'];
		$fare_summary['YQTax'] = $Fare['YQTax'];
		$fare_summary['AdditionalTxnFeeOfrd'] = $Fare['AdditionalTxnFeeOfrd'];
		$fare_summary['AdditionalTxnFeePub'] = $Fare['AdditionalTxnFeePub'];
		$fare_summary['OtherCharges'] = $Fare['OtherCharges'];
		$fare_summary['Discount'] = $Fare['Discount'];
		$fare_summary['PublishedFare'] = $Fare['PublishedFare'];
		$fare_summary['AgentCommission'] = $Fare['CommissionEarned'];
		$fare_summary['PLBEarned'] = $Fare['PLBEarned'];
		$fare_summary['IncentiveEarned'] = $Fare['IncentiveEarned'];
		$fare_summary['OfferedFare'] = $Fare['OfferedFare'];
		$fare_summary['TdsOnCommission'] = $Fare['TdsOnCommission'];
		$fare_summary['TdsOnPLB'] = $Fare['TdsOnPLB'];
		$fare_summary['TdsOnIncentive'] = $Fare['TdsOnIncentive'];
		$fare_summary['ServiceFee'] = $Fare['ServiceFee'];
		$fare_summary['TotalBaggageCharges'] = @$Fare['TotalBaggageCharges'];
		$fare_summary['TotalMealCharges'] = @$Fare['TotalMealCharges'];
		$fare_summary['TotalSeatCharges'] = @$Fare['TotalSeatCharges'];
		$FareDetails = $fare_summary;
		return $FareDetails;
	}
	/**
	 * Formate Fare Rule Object
	 * @param unknown_type $FareRules
	 */
	private function format_fare_rule_object($FareRules)
	{
		$flight_fare_rules = array();
		foreach($FareRules as $k => $v){
			$flight_fare_rules[$k]['Origin'] = $v['Origin'];
			$flight_fare_rules[$k]['Destination'] = $v['Destination'];
			$flight_fare_rules[$k]['Airline'] = $v['Airline'];
			$flight_fare_rules[$k]['FareRuleDetail'] = $v['FareRuleDetail'];
			$flight_fare_rules[$k]['FareBasisCode'] = $v['FareBasisCode'];
			$flight_fare_rules[$k]['FareRestriction'] = $v['FareRestriction'];
		}
		return $flight_fare_rules;
	}
	/**
	 * Format Pax wise Fare Breakups(Adult/Child/Infant)
	 * @param $FareBreakdown
	 */
	function farmat_pax_fare_object($FareBreakdown)
	{
		$pax_fare_break_details = array();
		foreach($FareBreakdown as $k => $v){
			unset($v['Tax'], $v['YQTax']);
			$fare_breakup = array();
			$passenger_type = $this->get_passenger_type($v['PassengerType']);
			$fare_breakup['PassengerType'] = $passenger_type;
			$fare_breakup['Count'] = $v['PassengerCount'];
			$fare_breakup['BaseFare'] = $v['BaseFare'];
			//$fare_breakup['Tax'] = $v['Tax'];
			//$fare_breakup['YQTax'] = $v['YQTax'];
			$pax_fare_break_details[$k] = $fare_breakup;
		}
		return $pax_fare_break_details;
	}
	/**
	 * Format Fare Details
	 */
	private function format_segment_object($Segments)
	{
		$SegmentDetails = array();
		if(isset($Segments[0][0]) == false){//FIXME: Check for better solution
			$temp_segments = $Segments;
			unset($Segments);
			$Segments[0] = $temp_segments;
		}
		foreach($Segments as $segment_k => $segment_v){
			$segment_v = force_multple_data_format($segment_v);
			$temp_segment_data = array();
			foreach($segment_v as $k => $v) {
				$segment_data = array();
				$segment_data['Baggage'] = @$v['Baggage'];
				$segment_data['CabinBaggage'] = @$v['CabinBaggage'];
				if(isset($v['NoOfSeatAvailable']) == true){
					$segment_data['AvailableSeats'] = $v['NoOfSeatAvailable'];
				}
				$segment_data['TripIndicator'] = $v['TripIndicator'];
				if(isset($v['AirlinePNR']) == true) {
					$segment_data['AirlinePNR'] = $v['AirlinePNR'];//In Ticket Method and GetBooking Details We will get AirlinePNR
				}
				$segment_data['SegmentIndicator'] = $v['SegmentIndicator'];
				//Segment Airline Details
				$segment_data['AirlineDetails'] = $v['Airline'];
				//Segment Origin Details
				$OriginDetails['AirportCode'] = $v['Origin']['Airport']['AirportCode'];
				$OriginDetails['AirportName'] = $v['Origin']['Airport']['AirportName'];
				$OriginDetails['Terminal'] = $v['Origin']['Airport']['Terminal'];
				$OriginDetails['CityCode'] = $v['Origin']['Airport']['CityCode'];
				$OriginDetails['CityName'] = $v['Origin']['Airport']['CityName'];
				$OriginDetails['CountryCode'] = $v['Origin']['Airport']['CountryCode'];
				$OriginDetails['CountryName'] = $v['Origin']['Airport']['CountryName'];
				$OriginDetails['DepartureTime'] = $v['Origin']['DepTime'];
				$segment_data['OriginDetails'] = $OriginDetails;
				//Segment Destination Details
				$DestinationDetails['AirportCode'] = $v['Destination']['Airport']['AirportCode'];
				$DestinationDetails['AirportName'] = $v['Destination']['Airport']['AirportName'];
				$DestinationDetails['Terminal'] = $v['Destination']['Airport']['Terminal'];
				$DestinationDetails['CityCode'] = $v['Destination']['Airport']['CityCode'];
				$DestinationDetails['CityName'] = $v['Destination']['Airport']['CityName'];
				$DestinationDetails['CountryCode'] = $v['Destination']['Airport']['CountryCode'];
				$DestinationDetails['CountryName'] = $v['Destination']['Airport']['CountryName'];
				$DestinationDetails['ArrivalTime'] = $v['Destination']['ArrTime'];
				$segment_data['DestinationDetails'] = $DestinationDetails;
				//Other Details
				//$SegmentDuration = (calculate_duration($OriginDetails['DepartureTime'],$DestinationDetails['ArrivalTime']))/60;//Converting int minutes
				$SegmentDuration = $this->flight_segment_duration($v['Origin']['Airport']['CityCode'], $v['Destination']['Airport']['CityCode'], $OriginDetails['DepartureTime'], $DestinationDetails['ArrivalTime']);
				$segment_data['SegmentDuration'] = $SegmentDuration;
				
				$segment_data['Status'] = $v['Status'];
				$segment_data['Craft'] = $v['Craft'];
				$segment_data['IsETicketEligible'] = $v['IsETicketEligible'];
				$temp_segment_data[$k] = $segment_data;
			}
			$SegmentDetails[$segment_k] = $temp_segment_data;
		}
		return $SegmentDetails;
	}
	/**
	 * Jaganath
	 * Calculates the flight segment duration based on airport time zone offset
	 * @param $departure_airport_code
	 * @param $arrival_airport_code
	 * @param $departure_datetime
	 * @param $arrival_datetime
	 */
	private function flight_segment_duration($departure_airport_code, $arrival_airport_code, $departure_datetime, $arrival_datetime)
	{
		$departure_datetime = date('Y-m-d H:i:s', strtotime($departure_datetime));
		$arrival_datetime = date('Y-m-d H:i:s', strtotime($arrival_datetime));
		//Get TimeZone of Departure and Arrival Airport
		$departure_timezone_offset = $this->get_airport_timezone_offset($departure_airport_code, $departure_datetime);
		$arrival_timezone_offset = $this->get_airport_timezone_offset($arrival_airport_code, $arrival_datetime);
		//Converting TimeZone to Minutes
		$departure_timezone_offset = $this->convert_timezone_offset_to_minutes($departure_timezone_offset);
		$arrival_timezone_offset = $this->convert_timezone_offset_to_minutes($arrival_timezone_offset);
		//Getting Total time difference between 2 airports
		$timezone_offset = ($departure_timezone_offset-$arrival_timezone_offset);
		//Calculating Total Duration Time
		$segment_duration = calculate_duration($departure_datetime,$arrival_datetime);
		//Converting into minutes
		$segment_duration = ($segment_duration)/60;//Converting int minutes
		//Updating the total duration with time zone offset difference
		$segment_duration = ($segment_duration+$timezone_offset);
		return $segment_duration;
	}
	/**
	 * Jaganath
	 * Returns Airport timezone offset
	 * @param $airport_code
	 */
	private function get_airport_timezone_offset($airport_code,$journey_date)
	{
		//FIXME: cache the data
		$journey_month = date('m', strtotime($journey_date));
		$query = 'select FAL.airport_code,FAT.start_month,FAT.end_month,FAT.timezone_offset from flight_airport_list FAL
					join flight_airport_timezone_offset FAT on FAT.flight_airport_list_fk=FAL.origin
					where airport_code = "'.$airport_code.'" and (start_month<='.$journey_month.' or end_month>='.$journey_month.')
					order by 
					CASE
					WHEN start_month	= '.$journey_month.' THEN 1
		            WHEN end_month	= '.$journey_month.' THEN 2
					ELSE 3 END';
		$timezone_offset = $this->CI->db->query($query)->result_array();
		return $timezone_offset[0]['timezone_offset'];
	}
	/**
	 * Converts the time zone offset to minutes
	 * @param unknown_type $timezone_offset
	 */
	private function convert_timezone_offset_to_minutes($timezone_offset)
	{
		$add_mode_sign = $timezone_offset[0];
		$time_zone_details = explode(':', $timezone_offset);
		$hours = abs(intval($time_zone_details[0]));
		$minutes = abs(intval($time_zone_details[1]));
		$minutes = $hours * 60  + $minutes;
		$minutes = ($add_mode_sign.$minutes);
		return $minutes;
	}
	/**
	 * Search Rule Response
	 * @param $response
	 */
	protected function format_fare_rule_response($response)
	{
		$data['Status'] = SUCCESS_STATUS;
		$data['Message'] = '';
		$data['data'] = array();
		//Validate the Response
		if($response['Status'] == true && valid_array($response['data']) == true && isset($response['data']['Response']) == true && $response['data']['Response']['ResponseStatus'] == SUCCESS_STATUS) {
			$response = $response['data']['Response'];
			$FareRules = $response['FareRules'];
			$flight_fare_rules = $this->format_fare_rule_object($FareRules);
			$data['Status'] = SUCCESS_STATUS;
			$data['data']['FareRules'] = $flight_fare_rules;
		} else {
			//Error Details
			$data['Status'] = FAILURE_STATUS;
			$data['Message'] = 'Remote IO Error';
		}
		return $this->output_data($data);
	}
	/**
	 * FareQuote Response
	 * @param unknown_type $response
	 */
	protected function format_fare_quote_response($response)
	{
		$data['Status'] = SUCCESS_STATUS;
		$data['Message'] = '';
		$data['data'] = array();
		//Validate the Response
		if($response['Status'] == true && valid_array($response['data']) == true && isset($response['data']['Response']) == true && $response['data']['Response']['ResponseStatus'] == SUCCESS_STATUS) {
			$response = $response['data']['Response'];
			$TraceId = $response['TraceId'];
			$cache_file_name = $this->generate_cache_file_name();
			$ProvabAuthKey = base64_encode($cache_file_name.'___0');
			$cache_index = 0;
			$ProvabAuthKey = $this->generate_provab_auth_key($cache_file_name, $cache_index, $TraceId);
			$api_cache_data = array();
			$api_cache_data['0'] = $response['Results'];
			$api_cache_data['0']['ProvabAuthKey'] = $ProvabAuthKey;
			$api_cache_data['0']['TraceId'] = $response['TraceId'];
			$api_cache_data['0']['IsPriceChanged'] = $response['IsPriceChanged'];
			
			$Results = $response['Results'];
			$FlightDetails =array();
			$Results = force_multple_data_format($Results);
			$journey_attributes = $this->get_journry_type_attributes(array($Results));
			foreach($Results as $list_k => $list_v){
				
				$FlightDetails[$list_k]['ProvabAuthKey'] = $ProvabAuthKey;
				$FlightDetails[$list_k]['ResultIndex'] = $list_v['ResultIndex'];
				$FlightDetails[$list_k]['Source'] = $list_v['Source'];
				$FlightDetails[$list_k]['IsLCC'] = $list_v['IsLCC'];
				$FlightDetails[$list_k]['FareDetails'] = $this->format_fare_object($list_v['Fare']);
				$FlightDetails[$list_k]['PassengerFareBreakdown'] = $this->farmat_pax_fare_object($list_v['FareBreakdown']);//Passenger Fare Break down
				$FlightDetails[$list_k]['SegmentDetails'] = $this->format_segment_object($list_v['Segments']);
				$FlightDetails[$list_k]['IsRefundable'] = $list_v['IsRefundable'];
			}
			$UpdatedFlightFareDetails['IsPriceChanged'] = $response['IsPriceChanged'];
			$UpdatedFlightFareDetails['FlightDetails'] = $FlightDetails;
			$data['Status'] = SUCCESS_STATUS;
			$data['data']['UpdatedFlightFareDetails'] = $UpdatedFlightFareDetails;
			$data['ProvabData']['JourneyAttributes'] = $journey_attributes;
			$this->cache_api_response($cache_file_name, $api_cache_data);//Cache Fare Quote Data
		} else {
			//Error Details
			$data['Status'] = FAILURE_STATUS;
			$data['Message'] = '';
		}
		return $this->output_data($data);
	}
	/*
	 * SSR Response
	 */
	protected function format_ssr_response($response)
	{
		$data['Status'] = SUCCESS_STATUS;
		$data['Message'] = '';
		$data['data'] = array();
		//Validate the Response
		if($response) {
			$response = $response['data'];
			$data['Status'] = SUCCESS_STATUS;
		} else {
			//Error Details
			$data['Status'] = FAILURE_STATUS;
			$data['Message'] = '';
		}
		return $this->output_data($data);
	}
	/**
	 * Book Response
	 * @param $response
	 */
	function format_book_response($response)
	{
		$data['Status'] = SUCCESS_STATUS;
		$data['Message'] = '';
		$data['data'] = array();
		//Validate the Response
		if($response['Status'] == true && valid_array($response['data']) == true && isset($response['data']['Response']['Response']) == true && $response['data']['Response']['Response']['Status'] == SUCCESS_STATUS) {
			$response = $response['data']['Response'];
			$flight_itinerary = $response['Response']['FlightItinerary'];
			$TraceId = $response['TraceId'];
			$BookingDetails = array();
			$ApiToken = array();
			$FareDetails = array();
			$FareRule = array();
			$SegmentDetails = array();
			$FareDetails = $this->format_fare_object($flight_itinerary['Fare']);
			$FareRule = $this->format_fare_rule_object($flight_itinerary['FareRules']);
			$SegmentDetails = $this->format_segment_object($flight_itinerary['Segments']);
			$PassengerDetails = $this->format_passenger_ticket_details($flight_itinerary['Passenger']);
			$ApiToken['TraceId'] = $TraceId;
			$ApiToken['Status'] = $flight_itinerary['Status'];//FIXME:check the purpose of the status
			$BookingDetails['BookingId'] = $flight_itinerary['BookingId'];
			$BookingDetails['PNR'] = $flight_itinerary['PNR'];
			$BookingDetails['IsDomestic'] = $flight_itinerary['IsDomestic'];
			$BookingDetails['Source'] = $flight_itinerary['Source'];
			$BookingDetails['Origin'] = $flight_itinerary['Origin'];
			$BookingDetails['Destination'] = $flight_itinerary['Destination'];
			$BookingDetails['IsLCC'] = $flight_itinerary['IsLCC'];
			$BookingDetails['NonRefundable'] = $flight_itinerary['NonRefundable'];
			$BookingDetails['FareDetails'] = $FareDetails;
			$BookingDetails['PassengerDetails'] = $PassengerDetails;
			$BookingDetails['SegmentDetails'] = $SegmentDetails;
			$BookingDetails['FareRule'] = $FareRule;
			$BookingDetails['ApiToken'] = $ApiToken;
			$data['Status'] = SUCCESS_STATUS;
			$data['data']['BookingDetails'] = $BookingDetails;
		} else {
			//Error Details
			$data['Status'] = FAILURE_STATUS;
			$message = $response['data']['Response']['Error']['ErrorMessage'];
			$message = (empty($message) == true ? 'Booking Failed': $message);
			$data['Message'] = $message;
			//Log Exception
			$exception_log_message = '';
			$this->CI->exception_logger->log_exception(Flight_V10::get_app_reference(), 'BOOK', $exception_log_message, $response['data']);
		}
		return $this->output_data($data);
	}
	/**
	 * Ticket Response
	 * @param $response
	 */
	 protected function format_ticket_response($response)
	{
		$data['Status'] = SUCCESS_STATUS;
		$data['Message'] = '';
		$data['data'] = array();
		//Validate the Response
		if($this->validate_ticket_response($response) == true) {
			$response = $response['data']['Response'];
			$flight_itinerary = $response['Response']['FlightItinerary'];
			$TraceId = $response['TraceId'];
			$TicketDetails = array();
			$ApiToken = array();
			$FareDetails = array();
			$FareRule = array();
			$SegmentDetails = array();
			$FareDetails = $this->format_fare_object($flight_itinerary['Fare']);
			$FareRule = $this->format_fare_rule_object($flight_itinerary['FareRules']);
			$SegmentDetails = $this->format_segment_object($flight_itinerary['Segments']);
			$PassengerDetails = $this->format_passenger_ticket_details($flight_itinerary['Passenger']);
			$ApiToken['TraceId'] = $TraceId;
			$ApiToken['Status'] = $flight_itinerary['Status'];//FIXME:check the purpose of the status
			$ApiToken['InvoiceNo'] = $flight_itinerary['InvoiceNo'];
			$ApiToken['InvoiceCreatedOn'] = $flight_itinerary['InvoiceCreatedOn'];
			$TicketDetails['BookingId'] = $flight_itinerary['BookingId'];
			$TicketDetails['PNR'] = $flight_itinerary['PNR'];
			$TicketDetails['IsDomestic'] = $flight_itinerary['IsDomestic'];
			$TicketDetails['Source'] = $flight_itinerary['Source'];
			$TicketDetails['Origin'] = $flight_itinerary['Origin'];
			$TicketDetails['Destination'] = $flight_itinerary['Destination'];
			$TicketDetails['IsLCC'] = $flight_itinerary['IsLCC'];
			$TicketDetails['NonRefundable'] = $flight_itinerary['NonRefundable'];
			
			$TicketDetails['FareDetails'] = $FareDetails;
			$TicketDetails['PassengerDetails'] = $PassengerDetails;
			$TicketDetails['SegmentDetails'] = $SegmentDetails;
			$TicketDetails['FareRule'] = $FareRule;
			//$TicketDetails['CancellationCharges'] = $flight_itinerary['CancellationCharges'];//FIXME: check this 
			$TicketDetails['ApiToken'] = $ApiToken;
			$data['Status'] = SUCCESS_STATUS;
			$data['data']['TicketDetails'] = $TicketDetails;
		} else {
			//Error Details
			$data['Status'] = FAILURE_STATUS;
			$message = $response['data']['Response']['Error']['ErrorMessage'];
			$message = (empty($message) == true ? 'Booking Failed': $message);
			$data['Message'] = $message;
			//Log Exception
			$exception_log_message = '';
			$this->CI->exception_logger->log_exception(Flight_V10::get_app_reference(), 'TICKET', $exception_log_message, $response['data']);
		}
		return $this->output_data($data);
	}
	/**
	 *Validates the Ticket Resposne 
	 * //TicketStatus
		Failed = 0,
		Successful = 1,
		NotSaved = 2,
		NotCreated = 3,
		NotAllowed = 4,
		InProgress = 5,
		TicketeAlreadyCreated= 6,
		PriceChanged = 8,
		OtherError = 9*
	 */
	private function validate_ticket_response($response)
	{
		/*if($response['Status'] == true && valid_array($response['data']) == true && isset($response['data']['Response']['Response']['TicketStatus']) == true 
		&& in_array($response['data']['Response']['Response']['TicketStatus'], array(1, 5))) {
			return true;
		} else {
			return false;
		}*/
		
		if($response['Status'] == true && valid_array($response['data']) == true && $this->is_ticketing_error($response) == false && 
		isset($response['data']['Response']['Response']['TicketStatus']) == true && in_array($response['data']['Response']['Response']['TicketStatus'], array(1, 5))) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Checks Ticketing Error
	 */
	private function is_ticketing_error($response)
	{
		//14 => Duplicate Booking Error
		$ticket_error_status_codes = array(14);
		if(isset($response['data']['Response']['Error']['ErrorCode']) == true && in_array($response['data']['Response']['Error']['ErrorCode'], $ticket_error_status_codes)) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * GetBookingDetails Response
	 * @param $response
	 */
	 protected function format_get_booking_details_response($response)
	{
		$data['Status'] = SUCCESS_STATUS;
		$data['Message'] = '';
		$data['data'] = array();
		//Validate the Response
		if($response['Status'] == true && valid_array($response['data']) == true && isset($response['data']['Response']) == true && $response['data']['Response']['ResponseStatus'] == SUCCESS_STATUS) {
			$response = $response['data']['Response'];
			$booking_details = $response['FlightItinerary'];
			$TraceId = $response['TraceId'];
			$FlightItinerary = array();
			$ApiToken = array();
			$FareDetails = array();
			$FareRule = array();
			$SegmentDetails = array();
			$FareDetails = $this->format_fare_object($booking_details['Fare']);
			$FareRule = $this->format_fare_rule_object($booking_details['FareRules']);
			$SegmentDetails = $this->format_segment_object($booking_details['Segments']);
			$PassengerDetails = $this->format_passenger_ticket_details($booking_details['Passenger']);
			$ApiToken['TraceId'] = $TraceId;
			$ApiToken['Status'] = $booking_details['Status'];//FIXME:check the purpose of the status
			$ApiToken['InvoiceNo'] = $booking_details['InvoiceNo'];
			$ApiToken['InvoiceCreatedOn'] = $booking_details['InvoiceCreatedOn'];
			
			$FlightItinerary['BookingId'] = $booking_details['BookingId'];
			$FlightItinerary['PNR'] = $booking_details['PNR'];
			$FlightItinerary['IsDomestic'] = $booking_details['IsDomestic'];
			$FlightItinerary['Source'] = $booking_details['Source'];
			$FlightItinerary['Origin'] = $booking_details['Origin'];
			$FlightItinerary['Destination'] = $booking_details['Destination'];
			$FlightItinerary['IsLCC'] = $booking_details['IsLCC'];
			$FlightItinerary['NonRefundable'] = $booking_details['NonRefundable'];
			
			$FlightItinerary['FareDetails'] = $FareDetails;
			$FlightItinerary['PassengerDetails'] = $PassengerDetails;
			$FlightItinerary['SegmentDetails'] = $SegmentDetails;
			$FlightItinerary['FareRule'] = $FareRule;
			//$FlightItinerary['CancellationCharges'] = $booking_details['CancellationCharges'];//FIXME: check this 
			$FlightItinerary['ApiToken'] = $ApiToken;
			$data['Status'] = SUCCESS_STATUS;
			$data['data']['FlightItinerary'] = $FlightItinerary;
		} else {
			//Error Details
			$data['Status'] = FAILURE_STATUS;
			$data['Message'] = 'Remote IO Error';
		}
		return $this->output_data($data);
	}	
	/**
	 * Formates Passenger Ticket Details
	 */
	private function format_passenger_ticket_details($Passenger)
	{
		$passenger_details = array();
		foreach($Passenger as $pax_k => $pax_v){
			$passenger_details[$pax_k]['PaxId'] = $pax_v['PaxId'];
			$passenger_details[$pax_k]['Title'] = $pax_v['Title'];
			$passenger_details[$pax_k]['FirstName'] = $pax_v['FirstName'];
			$passenger_details[$pax_k]['LastName'] = $pax_v['LastName'];
			$passenger_details[$pax_k]['PaxType'] = $this->get_passenger_type($pax_v['PaxType']);
			$passenger_details[$pax_k]['DateOfBirth'] = $pax_v['DateOfBirth'];
			$passenger_details[$pax_k]['Gender'] = $pax_v['Gender'];
			$passenger_details[$pax_k]['PassportNo'] = $pax_v['PassportNo'];
			$passenger_details[$pax_k]['AddressLine1'] = @$pax_v['AddressLine1'];//we will get only for lead pax
			$passenger_details[$pax_k]['AddressLine2'] = @$pax_v['AddressLine2'];//we will get only for lead pax
			$passenger_details[$pax_k]['FareDetails'] = $this->format_fare_object($pax_v['Fare']);
			$passenger_details[$pax_k]['City'] = @$pax_v['City'];//we will get only for lead pax
			$passenger_details[$pax_k]['CountryCode'] = $pax_v['CountryCode'];
			$passenger_details[$pax_k]['Nationality'] = $pax_v['Nationality'];
			$passenger_details[$pax_k]['ContactNo'] = @$pax_v['ContactNo'];//we will get only for lead pax
			$passenger_details[$pax_k]['Email'] = @$pax_v['Email'];//we will get only for lead pax
			$passenger_details[$pax_k]['IsLeadPax'] = $pax_v['IsLeadPax'];
			//$passenger_details[$pax_k]['Meal'] = $pax_v['Meal'];//Not Implemented
			$passenger_details[$pax_k]['Ticket'] = $this->format_ticket_object(@$pax_v['Ticket']);//In Booking method, we wont get Ticket Object
			$passenger_details[$pax_k]['SegmentAdditionalInfo'] = $this->format_segment_additional_info_object(@$pax_v['SegmentAdditionalInfo']);//In Booking method, we wont get SegmentAdditionalInfo Object
		}
		return $passenger_details;
	}
	/**
	 * Format Ticket Object
	 * @param unknown_type $Ticket
	 */
	private function format_ticket_object($Ticket)
	{
		$ticket_details = array();
		if(valid_array($Ticket) == true) {
			$ticket_details['TicketId'] = $Ticket['TicketId'];
			$ticket_details['TicketNumber'] = $Ticket['TicketNumber'];
			$ticket_details['IssueDate'] = $Ticket['IssueDate'];
			$ticket_details['ValidatingAirline'] = $Ticket['ValidatingAirline'];
			$ticket_details['ServiceFeeDisplayType'] = $Ticket['ServiceFeeDisplayType'];
			$ticket_details['Remarks'] = $Ticket['Remarks'];
			$ticket_details['Status'] = $Ticket['Status'];//FIXME: check the purpose of the status
		}
		return $ticket_details;
	}
	/**
	 * Format Ticket Object
	 * @param unknown_type $Ticket
	 */
	private function format_segment_additional_info_object($SegmentAdditionalInfo)
	{
		$additional_details = array();
		if(valid_array($SegmentAdditionalInfo) == true) {
			foreach($SegmentAdditionalInfo as $k => $v){
				$additional_details[$k]['Baggage'] =$v['Baggage'];
				$additional_details[$k]['Meal'] = 	$v['Meal'];
				$additional_details[$k]['Seat'] = 	$v['Seat'];
			}
		}
		return $additional_details;
	}
	/**
	 * Release PNR Response
	 * @param unknown_type $response
	 */
	 protected function format_release_pnr_response($response)
	{
		$data['Status'] = SUCCESS_STATUS;
		$data['Message'] = '';
		$data['data'] = array();
		//Validate the Response
		if($response) {
			$response = $response['data'];
			$data['Status'] = SUCCESS_STATUS;
		} else {
			//Error Details
			$data['Status'] = FAILURE_STATUS;
			$data['Message'] = '';
		}
		return $data;
	}
	/**
	 * SendChangeRequest Response
	 * @param $response
	 */
	 protected function format_send_change_request_response($response)
	{
		$data['Status'] = SUCCESS_STATUS;
		$data['Message'] = '';
		$data['data'] = array();
		//Validate the Response
		if($response['Status'] == true && valid_array($response['data']) == true && isset($response['data']['Response']) == true && $response['data']['Response']['ResponseStatus'] == SUCCESS_STATUS) {
			$response = $response['data']['Response'];
			$TicketCRInfo = $response['TicketCRInfo'];
			$TicketChangeRequestDetails = array();
			foreach($TicketCRInfo as $k => $v) {
				$TicketChangeRequestDetails[$k]['ChangeRequestId'] = $v['ChangeRequestId'];
				$TicketChangeRequestDetails[$k]['ChangeRequestStatus'] = $v['ChangeRequestStatus'];
			}
			$data['Status'] = SUCCESS_STATUS;
			$data['data']['TicketChangeRequestDetails'] = $TicketChangeRequestDetails;
		} else {
			//Error Details
			$data['Status'] = FAILURE_STATUS;
			$message = @$response['data']['Response']['Error']['ErrorMessage'];
			$message = (empty($message) == true ? 'Remote IO Error': $message);
			$data['Message'] = $message;
		}
		return $this->output_data($data);
	}
	/**
	 * GetChangeRequestStatus Response
	 * @param unknown_type $response
	 */
	 protected function format_get_change_request_status_response($response)
	{
		$data['Status'] = SUCCESS_STATUS;
		$data['Message'] = '';
		$data['data'] = array();
		//Validate the Response
		if($response['Status'] == true && valid_array($response['data']) == true && isset($response['data']['Response']) == true && $response['data']['Response']['ResponseStatus'] == SUCCESS_STATUS) {
			$response = $response['data']['Response'];
			$TicketCancellationtDetails = array();
			$TicketCancellationtDetails['ChangeRequestId'] = $response['ChangeRequestId'];
			$TicketCancellationtDetails['ChangeRequestStatus'] = $response['ChangeRequestStatus'];
			$TicketCancellationtDetails['RefundedAmount'] = floatval(@$response['RefundedAmount']);
			$TicketCancellationtDetails['CancellationCharge'] = floatval(@$response['CancellationCharge']);
			$TicketCancellationtDetails['ServiceTaxOnRefundAmount'] = floatval(@$response['ServiceTaxOnRAF']);
			$TicketCancellationtDetails['SwachhBharatCess'] = floatval(@$response['SwachhBharatCess']);
			$data['Status'] = SUCCESS_STATUS;
			$data['data']['TicketCancellationtDetails'] = $TicketCancellationtDetails;
		} else {
			//Error Details
			$data['Status'] = FAILURE_STATUS;
			$message = $response['data']['Response']['Error']['ErrorMessage'];
			$message = (empty($message) == true ? 'Remote IO Error': $message);
			$data['Message'] = $message;
		}
		return $this->output_data($data);
	}
	/**
	 * GetCalendarFare Response
	 */
	 protected function format_get_calendar_fare_response($response)
	{
		$data['Status'] = SUCCESS_STATUS;
		$data['Message'] = '';
		$data['data'] = array();
		//Validate the Response
		if($response['Status'] == true && valid_array($response['data']) == true && isset($response['data']['Response']) == true && $response['data']['Response']['ResponseStatus'] == SUCCESS_STATUS) {
			$response = $response['data']['Response'];
			$calendar_fare_details = array();
			$calendar_fare_details['TraceId'] = $response['TraceId'];
			$calendar_fare_details['Origin'] = $response['Origin'];
			$calendar_fare_details['Destination'] = $response['Destination'];
			$calendar_fare_details['CalendarFareDetails'] = $response['SearchResults'];
			$data['Status'] = SUCCESS_STATUS;
			$data['data'] = $calendar_fare_details;
		} else {
			//Error Details
			$data['Status'] = FAILURE_STATUS;
			$data['Message'] = '';
		}
		return $data;
	}
	/**
	 * UpdateCalendarFare Response
	 */
	 protected function format_get_update_calendar_fare_response($response)
	{
		$data['Status'] = SUCCESS_STATUS;
		$data['Message'] = '';
		$data['data'] = array();
		//Validate the Response
		if($response['Status'] == true && valid_array($response['data']) == true && isset($response['data']['Response']) == true && $response['data']['Response']['ResponseStatus'] == SUCCESS_STATUS) {
			$response = $response['data']['Response'];
			$calendar_fare_details = array();
			$calendar_fare_details['TraceId'] = $response['TraceId'];
			$calendar_fare_details['Origin'] = $response['Origin'];
			$calendar_fare_details['Destination'] = $response['Destination'];
			$calendar_fare_details['CalendarFareDetails'] = $response['SearchResults'];
			$data['Status'] = SUCCESS_STATUS;
			$data['data'] = $calendar_fare_details;
		} else {
			//Error Details
			$data['Status'] = FAILURE_STATUS;
			$data['Message'] = '';
		}
		return $data;
	}
	/**
	 * Returns the Data
	 * @param unknown_type $data
	 */
	protected function output_data($data)
	{
		return $data;
	}
	/**
	 * Stores the API response in File
	 */
	public function cache_api_response($file_name, $data)
	{
		$file_name = trim($file_name);
		if(empty($file_name) == false && valid_array($data) == true) {
			$file = fopen($this->cache_directory. $file_name . '.json', "w" );
			fwrite($file, json_encode($data));
			fclose($file);
		}
	}
	/**
	 * Read Cache Data from file
	 */
	public function read_cache_data($ProvabAuthKey)
	{
		$data['Status'] = FAILURE_STATUS;
		$data['data'] = array();
		$data['Message'] = '';
		$ProvabAuthKey = trim($ProvabAuthKey);
		if(empty($ProvabAuthKey) == false) {
			$ProvabAuthKey = base64_decode($ProvabAuthKey);
			$cache_data = '';
			$auth_details = $this->extract_provab_auth_key($ProvabAuthKey);
			$file_name = $auth_details['cache_file_name'];
			$auth_key = $auth_details['cache_index'];
			$file_pointer = @fopen($this->cache_directory.$file_name.'.json', "r" );
			if($file_pointer) {
				$data['Status'] = SUCCESS_STATUS;
				$cache_data = fread($file_pointer, filesize($this->cache_directory.$file_name.'.json'));
				fclose($file_pointer);
				//Convert it into an array
				$cache_data = json_decode($cache_data, true);
				$cache_data = $cache_data[$auth_key];
			} else {
				$data['Message'] = 'File Not Found';
			}
			$data['data'] = $cache_data;
		}
		return $data;
	}
	/**
	 * Stores API Requests
	 */
	protected  function store_api_request($request_type='', $request='')
	{
		if($request_type !='' && $this->inactive_cache_services($request_type) == false){
			if(is_array($request)) {
				$response = json_encode($request);
			}
			$provab_api_response_history = array();
			$provab_api_response_history['request_type'] = $request_type;
			$provab_api_response_history['request'] = $request;
			$provab_api_response_history['created_datetime'] = date('Y-m-d H:i:s');
			return $this->CI->custom_db->insert_record('provab_api_response_history',$provab_api_response_history);
		}
	}
	/**
	 * Stores API Requests
	 */
	protected  function update_api_response($request_type='', $response='', $origin = 0)
	{
		if(($request_type !='' || $this->inactive_cache_services($request_type) == false) && intval($origin) > 0){
			if(is_array($response)) {
				$response = json_encode($response);
			}
			$provab_api_response_history = array();
			$provab_api_response_history['response'] = $response;
			return $this->CI->custom_db->update_record('provab_api_response_history',$provab_api_response_history, array('origin' => intval($origin)));
		}
	}
	/**
	 * Checks Cache is enabled for Service 
	 * Enter description here ...
	 */
	private function inactive_cache_services($service_name)
	{
		$inactive_cache = array('SEARCH', 'AUTHENTICATE', 'GETCALENDARFARE', 'FARERULE');
		//$inactive_cache = array();
		if(in_array(strtoupper($service_name), $inactive_cache) == true){
			return true;
		} else {
			return false;
		}
	}
	protected function add_test_data($data, $enable_json = false) 
	{
		if ($enable_json == true) {
			$data = json_encode ( $data );
		}
		$this->CI->custom_db->insert_record ( 'test', array (
				'test' => $data 
		) );
	}
	protected function get_test_data($origin = 0, $enable_json = false) 
	{
		$data = $this->CI->custom_db->single_table_records ( 'test', '*', array (
				'origin' => intval ( $origin ) 
		) );
		if ($enable_json == true) {
			return json_decode ( $data ['data'] [0] ['test'], true );
		} else {
			return $data ['data'] [0] ['test'];
		}
	}
}
