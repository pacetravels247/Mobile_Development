<?php if (! defined ( 'BASEPATH' ))	exit ( 'No direct script access allowed' );
class Temp extends CI_Controller 
{
	
	function __construct()
	{
		parent::__construct();
	}
	
	function delete_flight_cache_files()
	{
		$files = glob(realpath('../temp').'/flight_cache/tbo/*');
		foreach($files as $file){ // iterate files
		  if(is_file($file)){
			  if(filemtime($file) < time() - 86400) {
				 unlink($file); // delete file 
			  }
			}
		}
	}
	function delete_hotel_cache_files()
	{
		$files = glob(realpath('../temp').'/hotel_cache/tbo/*');
		foreach($files as $file){ // iterate files
		  if(is_file($file)){
			  if(filemtime($file) < time() - 86400) {
				 unlink($file); // delete file 
			  }
			}
		}
	}
	function delete_bus_cache_files()
	{
		$files = glob(realpath('../temp').'/bus_cache/tbo/*');
		foreach($files as $file){ // iterate files
		  if(is_file($file)){
			  if(filemtime($file) < time() - 86400) {
				 unlink($file); // delete file 
			  }
			}
		}
	}
	/******** TO TEST THE SERVICES**********/
	public function test_method($request_type='Authenticate')
	{
		$request = ''; 
		$_header = array();
		switch($request_type) {
			case 'Search':
				$request = $this->search_request_data();
				break;
			case 'Book':
				$request = $this->book_request_data();
				break;
			case 'Ticket':
				$request = $this->ticket_request_data();
				break;
			case 'GetBookingDetails':
			$request = $this->get_booking_details_request_data();
			break;
			case 'SendChangeRequest':
			$request = $this->test_send_change_request();
			break;
			case 'GetChangeRequestStatus':
			$request = $this->test_get_change_request_status();
			break;
		}
		$data= $this->provab_api($request_type, $request, $request_type);
		$data = json_decode($data, true);
		echo $request_type.' Response';
		debug($data);exit;
	}
	function search_request_data()
	{
		$data['url'] = '';
		$data['method'] = 'Search';
		$data['request'] = array();
		$request = array();
		$ApiToken = array();
		$ApiToken['TokenId'] = '101c5980-40fb-43c8-ba8a-00fad56d796b';
		$ApiToken['EndUserIp'] = '127.0.0.1';
		$request['ApiToken']= $ApiToken;
		$request['AdultCount'] = 1;
		$request['ChildCount'] = 0;
		$request['InfantCount'] = 0;
		$request['JourneyType'] = 'Return';
		//Segments
		$segments = array();
		$segments['Origin'] = 'BLR';
		$segments['Destination'] = 'DEL';
		$segments['CabinClass'] = 'All';
		$segments['DepartureDate'] = '10-08-2016';
		$segments['ReturnDate'] = '18-08-2016';
		$request['Segments'] = array($segments);
		$request['PreferredAirlines'] = '';//AI
		$request['Sources']=array('6E', 'SG', 'G8');
		$data['request'] = json_encode($request);
		return $data['request'];
	}
	function book_request_data()
	{
		$data['url'] = '';
		$data['method'] = 'Book';
		$data['request'] = array();
		$request = array();
		$ApiToken = array();
		$passenger = array();
		$pax_count = 2;
		for($i=0; $i<($pax_count); $i++) {
			$passenger[$i] ['Title'] = 'Mr';
			$passenger[$i] ['FirstName'] = 'Test';
			$passenger[$i] ['LastName'] = 'Test';
			if($i == 1) {
				$passenger[$i] ['LastName'] = 'Customer';
			}
			$passenger[$i] ['PaxType'] = 1;//1=>adult;2=>child;3=>infant
			$passenger[$i] ['DateOfBirth'] = ''; // optional
			$passenger[$i] ['Gender'] = 1;//1=>Male;2=>Female
			$passenger[$i] ['PassportNumber'] = ''; // optional
			$passenger[$i] ['PassportExpiry'] = ''; // optional
			$passenger[$i] ['AddressLine1'] = 'Bangalore,Electronic City';
			$passenger[$i] ['AddressLine2'] = ''; // optional
			$passenger[$i] ['City'] = 'Bangalore';
			$passenger[$i] ['CountryCode'] = 'IN';
			$passenger[$i] ['CountryName'] = 'India';
			$passenger[$i] ['ContactNo'] = '8888888888';
			$passenger[$i] ['Email'] = 'test@test.com';
			$passenger[$i] ['IsLeadPax'] = true;
		}
		$ProvabAuthKey = 'MTQ2NjU3ODc4MjE0ODc1MTI2NjA4MzA4NTAzNzkxODc3MDMyNjg2MTk4ODYxMzMwX19fMFIwX19fMTQwM2E2NTctNzk5OC00OWY1LTk0MWQtOTEzZTAzYWZhYmVlX19fUFRCU0lEMDAwMDAwMDAwMg==';
		$ApiToken['TokenId'] = '1e3af489-b743-49e7-98cb-a0629bb04305';
		$request['ProvabAuthKey'] = $ProvabAuthKey;
		$request['ApiToken']= $ApiToken;
		$request['Passengers'] = $passenger;
		$data['request'] = json_encode($request);
		return $data['request'];
	}
	function ticket_request_data()
	{
		$data['url'] = '';
		$data['method'] = 'Ticket';
		$data['request'] = array();
		$passenger = array();
		$pax_count = 5;
		for($i=0; $i<($pax_count); $i++) {
			$passenger[$i] ['Title'] = 'Mr';
			$passenger[$i] ['FirstName'] = 'Test';
			$passenger[$i] ['LastName'] = 'Test';
			$passenger[$i] ['PaxType'] = 1;//1=>adult;2=>child;3=>infant
			$passenger[$i] ['DateOfBirth'] = ''; // optional
			$passenger[$i] ['Gender'] = 1;//1=>Male;2=>Female
			$passenger[$i] ['PassportNo'] = ''; // optional
			$passenger[$i] ['PassportExpiry'] = ''; // optional
			$passenger[$i] ['AddressLine1'] = 'Bangalore,Electronic City';
			$passenger[$i] ['AddressLine2'] = ''; // optional
			$passenger[$i] ['City'] = 'Bangalore';
			$passenger[$i] ['CountryCode'] = 'IN';
			$passenger[$i] ['CountryName'] = 'India';
			$passenger[$i] ['ContactNo'] = '8888888888';
			$passenger[$i] ['Email'] = 'test@test.com';
			$passenger[$i] ['IsLeadPax'] = true;
		}
		//Non LCC Ticket Request
		$request = array();
		$ApiToken = array();
		$ProvabAuthKey = 'MTQ2NjU3ODc4MjE0ODc1MTI2NjA4MzA4NTAzNzkxODc3MDMyNjg2MTk4ODYxMzMwX19fMFIwX19fMTQwM2E2NTctNzk5OC00OWY1LTk0MWQtOTEzZTAzYWZhYmVlX19fUFRCU0lEMDAwMDAwMDAwMg==';
		$ApiToken['TokenId'] = '41b13ba9-b89b-4ada-885c-40b2ecdcc198';
		$request['ProvabAuthKey'] = $ProvabAuthKey;
		$request['ApiToken']= $ApiToken;
		$request['Passengers'] = $passenger;
		//BookingId and PNR only for NON-LCC FLIGHTS
		$request['BookingId']= '186068';
		$request['PNR']= '5NLMEB';
		$data['request'] = json_encode($request);
		return $data['request'];
	}
	function get_booking_details_request_data()
	{
		
		$data['url'] = '';
		$data['method'] = 'GetBookingDetails';
		$data['request'] = array();
		$request = array();
		$ApiToken = array();
		$ApiToken['EndUserIp'] = '127.0.0.1';
		$ApiToken['TokenId'] = '41b13ba9-b89b-4ada-885c-40b2ecdcc198';
		$request['ApiToken']= $ApiToken;
		$request['BookingId']= '186102';
		$request['PNR']= 'R73V5M';
		$data['request'] = json_encode($request);
		return $data['request'];
	}
	/**
	 * Request Type
	 * 0=>NotSet
	 * 1=>Full Cancellation
	 * 2=>Partial Cancellation
	 * 3=>Reissuance
	 *
	 */
	/** 
	 * Cancellation Type
	 * NotSet = 0,
	 * NoShow = 1,
	 * FlightCancelled = 2,
	 * Others = 3
	 *
	 */
	public function test_send_change_request()
	{
		self::$credential_type = 'test';
		$request = array();
		$ApiToken = array();
		$ApiToken['EndUserIp'] = '127.0.0.1';
		$ApiToken['TokenId'] = 'a19fcaa9-5de2-4472-8a2f-b17146c2d527';
		$request['ApiToken']= $ApiToken;
		$request['BookingId']='191887';
		$request['IsFullBookingCancel']='true';
		
		$sectors=array();
		$sector_count = 1;
		for($i=0; $i<$sector_count; $i++){
			$sectors[$i]['Origin'] = 'BLR';
			$sectors[$i]['Destination'] = 'DEL';
		}
		//$request['TicketId'] = array(174174);//Only in case of partial cancellation
		$request['TicketId'] = null;
		$request['Remarks'] = 'Test Remarks';
		//$request['Sectors'] = $sectors;//Only in case of partial cancellation
		$request['Sectors'] = null;
		$data['request'] = json_encode($request);
		return $data['request'];
	}
	public function test_get_change_request_status(){
		self::$credential_type = 'test';
		$request = array();
		$ApiToken = array();
		$ApiToken['EndUserIp'] = '127.0.0.1';
		$ApiToken['TokenId'] = '37ba9696-d11b-431d-b3e8-4ffdde7e3fbf';
		$request['ApiToken']= $ApiToken;
		$request['ChangeRequestId'] = 14095;
		$data['request'] = json_encode($request);
		return $data['request'];
	}
}
