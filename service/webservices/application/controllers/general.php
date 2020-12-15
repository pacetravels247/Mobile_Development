<?php if (! defined ( 'BASEPATH' ))	exit ( 'No direct script access allowed' );
class General extends CI_Controller 
{
	
	function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{
		$this->load->view('general/index');
	}
	function ooops()
	{
		$this->load->view('general/ooops');
	}
	function get_flight_price_not_deducted_detail()
	{
		echo time();exit;
		$query = 'SELECT FB.* FROM flight_booking_details FB
					WHERE FB.status="BOOKING_CONFIRMED" and 
					date(FB.created_datetime)>"2016-11-14" AND FB.app_reference 
					NOT IN (SELECT BA.app_reference FROM booking_amount_logger BA)';
	}
	function test()
	{
		$this->redis_server->test_redis ();
	}
}
