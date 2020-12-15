<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'tbo_data_formatter.php';
/**
 *
 * @package    Provab
 * @subpackage TBO
 * @author     Jaganath<jaganath.provab@gmail.com>
 * @version    V1
 */

class Tbo_private extends Tbo_Data_Formatter
{
	public function __construct()
	{
		parent::__construct();
		$this->CI = &get_instance();
		$this->CI->load->model('flight_model');
	}
	/**
	 * TBO Flight Response
	 * @send only json format
	 * @return only json format
	 */
	private function get_flight_response($metod, $request = array(), $url = '') 
	{
		if (Flight_V10::get_credential_type() == 'test') {
			$this->set_test_api_credentails ();
		} else if (Flight_V10::get_credential_type() == 'live') {
			$this->set_live_api_credentails ();
		}
		if (empty ( $url ) == false) {
			$url = $url . $metod;
		} else {
			$url = $this->api_url . $metod;
		}
		$insert_id = $this->store_api_request($metod, $request);
		$insert_id = intval(@$insert_id['insert_id']);
		try{
		$cs = curl_init ();
		curl_setopt ( $cs, CURLOPT_URL, $url );
		curl_setopt ( $cs, CURLOPT_TIMEOUT, 180 );
		curl_setopt ( $cs, CURLOPT_HEADER, 0 );
		curl_setopt ( $cs, CURLOPT_RETURNTRANSFER, 1 );
		if (empty ( $request ) == false) {
			curl_setopt ( $cs, CURLOPT_POST, 1 );
			curl_setopt ( $cs, CURLOPT_POSTFIELDS, $request );
		}
		curl_setopt ( $cs, CURLOPT_SSL_VERIFYHOST, 2 );
		curl_setopt ( $cs, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt ( $cs, CURLOPT_SSLVERSION, 3 );
		curl_setopt ( $cs, CURLOPT_FOLLOWLOCATION, true );
		
		$header = array (
				'Content-Type:application/json',
				'Accept-Encoding:gzip, deflate' 
		);
		curl_setopt ( $cs, CURLOPT_HTTPHEADER, $header );
		curl_setopt ( $cs, CURLOPT_ENCODING, "gzip" );
		$response = curl_exec ( $cs );
		} catch (Exception $e) {
			$response = 'No Response Recieved From API';
		}
		//Update the API Response
		$this->update_api_response($metod, $response, $insert_id);
		
		$response = json_decode ( $response, true );
		$error = curl_getinfo ( $cs, CURLINFO_HTTP_CODE );
		curl_close ( $cs );
		return $response;
	}
	/**
	 * Authenticate
	 * This method is used to authenticate TBO User
	 */
	public function Authenticate($request_params)
	{
		$response ['data'] = array ();
		$response ['Status'] = FAILURE_STATUS;
		$response ['Message'] = '';
		$request_data = $this->format_authenticate_request($request_params);
		if($request_data['Status'] == SUCCESS_STATUS) {
			$response ['data'] = $this->get_flight_response ('Authenticate', $request_data['request'], $this->api_authentication_url);
			//$this->CI->custom_db->generate_static_response(json_encode($response ['data']));
			//$response ['data'] = $this->CI->custom_db->get_static_response(1);
			$response ['Status'] = SUCCESS_STATUS;
		} else {
			$response ['Status'] = FAILURE_STATUS;
		}
		return $this->format_authenticate_response($response);
	}
	/**
	 * Search
	 * This method is used to Search Flights
	 */
	public function Search($request_params)
	{
		$response ['data'] = array ();
		$response ['Status'] = FAILURE_STATUS;
		$response ['Message'] = '';
		$request_data = $this->format_search_request($request_params);
		if($request_data['Status'] == SUCCESS_STATUS) {
			$response ['data'] = $this->get_flight_response ('Search', $request_data['request'], $this->api_url);
			//$this->CI->custom_db->generate_static_response(json_encode($response ['data']));
			//$response ['data'] = $this->CI->custom_db->get_static_response(220);//220
			$response ['Status'] = SUCCESS_STATUS;
			if(strtoupper($request_params['JourneyType']) == 'RETURN_DELETE'){//Enable it later
				$spcl_req = json_decode($request_data['request'],true);
				$spcl_req['JourneyType'] = 5;
				$spcl_req['Sources'] = array('6E', 'SG', 'G8');
				$spcl_request = json_encode($spcl_req);
				$special_data['data'] = $this->get_flight_response('Search', $spcl_request, $this->api_url);
				if(valid_array($special_data ['data']['Response']['Results'])) {
					foreach($special_data ['data']['Response']['Results'] as $k => $v){
						foreach ($v as $__key => $___value){
							$special_data['data']['Response']['Results'][$k][$__key]['SpecialReturn'] = true;
						}
					}
					$combined_data = array();
					if(valid_array($response ['data']['Response']['Results'])) {
						foreach ($response ['data']['Response']['Results'] as $k => $v){
							if(isset($special_data['data']['Response']['Results'][$k]) && valid_array($special_data['data']['Response']['Results'][$k])){
								$combined_data[$k] = array_merge($special_data['data']['Response']['Results'][$k], $v);//merge data, push special data first
							} else {
								$combined_data[$k] = $v;
							}
						}
					}
					$response ['data']['Response']['Results'] = $combined_data;
				}
				echo '************Special Data************';
				debug($response);exit;
			}
		} else {
			$response ['Message'] = 'Invalid Request';
		}
		//SPECIAL RETURN
		
		return $this->format_search_response($response);
	}
	/**
	 * FareRule
	 * This method is used to Get Flight Fare rules
	 */
	public function FareRule($request_params)
	{
		$response ['data'] = array ();
		$response ['Status'] = FAILURE_STATUS;
		$response ['Message'] = '';
		$request_data = $this->format_fare_rule_request($request_params);
		if($request_data['Status'] == SUCCESS_STATUS) {
			$response['data'] = $this->get_flight_response ( 'FareRule', $request_data['request'], $this->api_url );
			//$this->CI->custom_db->generate_static_response(json_encode($response ['data']));
			//$response ['data'] = $this->CI->custom_db->get_static_response(5);//5=>fare rules 6=> no data 
			$response['Status'] = SUCCESS_STATUS;
		} else {
			$response ['Message'] = 'Invalid Request';
		}
		return $this->format_fare_rule_response($response);
	}
	/**
	 * FareQuote
	 * This method is used to Get Updated Fare Details for LCC Flights
	 */
	public function FareQuote($request_params)
	{
		$response ['data'] = array ();
		$response ['Status'] = FAILURE_STATUS;
		$response ['Message'] = '';
		$request_data = $this->format_fare_quote_request($request_params);
		if($request_data['Status'] == SUCCESS_STATUS) {
			$response['data'] = $this->get_flight_response ( 'FareQuote', $request_data['request'], $this->api_url );
			//$this->CI->custom_db->generate_static_response(json_encode($response ['data']));
			//$response ['data'] = $this->CI->custom_db->get_static_response(7);//7
			$response['Status'] = SUCCESS_STATUS;
		}
		return $this->format_fare_quote_response($response);
	}
	/**
	 * SSR: Not Implemented
	 * This method is used to Get Meals/Baggage Details
	 */
	public function SSR($request_params)
	{
		$response ['data'] = array ();
		$response ['Status'] = FAILURE_STATUS;
		$response ['Message'] = '';
		$request_data = $this->format_ssr_request($request_params);
		if($request_data['Status'] == SUCCESS_STATUS) {
			$response['data'] = $this->get_flight_response ( 'SSR', $request_data['request'], $this->api_url );
			$response['Status'] = SUCCESS_STATUS;
		}
		return $this->format_ssr_response($response);
	}
	/**
	 * Book
	 * This method is used to Book Flight(Only For NON-LCC FLIGHTS)
	 */
	public function Book($request_params) 
	{
		$response ['data'] = array ();
		$response ['Status'] = FAILURE_STATUS;
		$response ['Message'] = '';
		$request_data = $this->format_book_request($request_params);
		if($request_data['Status'] == SUCCESS_STATUS) {
			$response['data'] = $this->get_flight_response ( 'Book', $request_data['request'], $this->api_url );
			$this->CI->custom_db->generate_static_response(json_encode($response ['data']));
			
			//$response ['data'] = $this->CI->custom_db->get_static_response(13127);//13127,13250
			
			//2738, 2739--Failed Booking
			$response['Status'] = SUCCESS_STATUS;
		}
		return $this->format_book_response($response);
	}
	
	/**
	 * Ticket
	 * This method is used to generate Ticket
	 */
	public function Ticket($request_params) 
	{
		$response ['data'] = array ();
		$response ['Status'] = FAILURE_STATUS;
		$response ['Message'] = '';
		$request_data = $this->format_ticket_request($request_params);
		if($request_data['Status'] == SUCCESS_STATUS) {
			$response['data'] = $this->get_flight_response ( 'Ticket', $request_data['request'], $this->api_url );
			$this->CI->custom_db->generate_static_response(json_encode($response ['data']));
			//$response ['data'] = $this->CI->custom_db->get_static_response(13128);//13128
			//9014--- Duplicate Booking
			$response['Status'] = SUCCESS_STATUS;
		}
		return $this->format_ticket_response($response);
	}
	/**
	 * GetBookingDetails
	 * This method is used to get Booking Details
	 */
	public function GetBookingDetails($request_params) 
	{
		$response ['data'] = array ();
		$response ['Status'] = FAILURE_STATUS;
		$response ['Message'] = '';
		$request_data = $this->format_get_booking_details_request($request_params);
		if($request_data['Status'] == SUCCESS_STATUS) {
			$response['data'] = $this->get_flight_response ( 'GetBookingDetails', $request_data['request'], $this->api_url );
			//$this->CI->custom_db->generate_static_response(json_encode($response ['data']));
			//$response ['data'] = $this->CI->custom_db->get_static_response(438);//438
			$response['Status'] = SUCCESS_STATUS;
		}
		return $this->format_get_booking_details_response($response);
	}
	/**
	 * NOT IMPLEMENTED
	 * ReleasePNRRequest
	 * This method is used to release Hold Bookings which you do not want to ticket.
	 */
	public function ReleasePNRRequest($request_params) 
	{
		$response ['data'] = array ();
		$response ['Status'] = FAILURE_STATUS;
		$response ['Message'] = '';
		$request_data = $this->format_release_pnr_request($request_params);
		if($request_data['Status'] == SUCCESS_STATUS) {
			$response['data'] = $this->get_flight_response ( 'ReleasePNRRequest', $request_data['request'], $this->api_url );
			$response['Status'] = SUCCESS_STATUS;
		}
		return $response;
	}
	/**
	 * SendChangeRequest
	 * This method is used to send cancellation request
	 */
	public function SendChangeRequest($request_params) 
	{
		$response ['data'] = array ();
		$response ['Status'] = FAILURE_STATUS;
		$response ['Message'] = '';
		$request_data = $this->format_send_change_request($request_params);
		if($request_data['Status'] == SUCCESS_STATUS) {
			$response['data'] = $this->get_flight_response ( 'SendChangeRequest', $request_data['request'], $this->api_url );
			//$this->CI->custom_db->generate_static_response(json_encode($response ['data']));
			//$response ['data'] = $this->CI->custom_db->get_static_response(3);//4
			$response['Status'] = SUCCESS_STATUS;
		}
		return $this->format_send_change_request_response($response);
	}
	/**
	 * GetChangeRequest Status
	 * This method is used to get cancellation request status
	 */
	public function GetChangeRequestStatus($request_params) 
	{
		$response ['data'] = array ();
		$response ['Status'] = FAILURE_STATUS;
		$response ['Message'] = '';
		$request_data = $this->format_get_change_request_status_request($request_params);
		if($request_data['Status'] == SUCCESS_STATUS) {
			$response['data'] = $this->get_flight_response ( 'GetChangeRequestStatus', $request_data['request'], $this->api_url );
			$this->CI->custom_db->generate_static_response(json_encode($response ['data']));
			//$response ['data'] = $this->CI->custom_db->get_static_response(756);//4
			$response['Status'] = SUCCESS_STATUS;
		}
		return $this->format_get_change_request_status_response($response);
	}
	
	/**
	 * GetCalendarFare
	 * This method is used to get Calendar Fare
	 */
	public function GetCalendarFare($request_params) 
	{
		$response ['data'] = array ();
		$response ['Status'] = FAILURE_STATUS;
		$response ['Message'] = '';
		$request_data = $this->format_get_calendar_fare_request($request_params);
		if($request_data['Status'] == SUCCESS_STATUS) {
			$response['data'] = $this->get_flight_response ( 'GetCalendarFare', $request_data['request'], $this->api_url );
			//$this->CI->custom_db->generate_static_response(json_encode($response ['data']));
			//$response ['data'] = $this->CI->custom_db->get_static_response(260);
			$response['Status'] = SUCCESS_STATUS;
		}
		return $this->format_get_calendar_fare_response($response);
	}
	/**
	 * UpdateCalendarFareOfDay
	 * This method is used to updae Calendar Fare of the Day
	 */
	public function UpdateCalendarFareOfDay($request_params) 
	{
		$response ['data'] = array ();
		$response ['Status'] = FAILURE_STATUS;
		$response ['Message'] = '';
		$request_data = $this->format_get_update_calendar_fare_request($request_params);
		if($request_data['Status'] == SUCCESS_STATUS) {
			$response['data'] = $this->get_flight_response ( 'UpdateCalendarFareOfDay', $request_data['request'], $this->api_url );
			$response['Status'] = SUCCESS_STATUS;
		}
		return $this->format_get_update_calendar_fare_response($response);
	}
}
