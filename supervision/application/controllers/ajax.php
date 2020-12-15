<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// ------------------------------------------------------------------------
/**
 * Controller for all ajax activities
 *
 * @package    Provab
 * @subpackage ajax loaders
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V1
 */
// ------------------------------------------------------------------------

class Ajax extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		if (is_ajax() == false) {
			//$this->index();
		}
		//$this->output->enable_profiler(TRUE);
	}

	/**
	 * index page of application will be loaded here
	 */
	function index()
	{

	}

	/**
	 * get city list based on country
	 * @param $country_id
	 * @param $default_select
	 */
	function get_city_list($country_id=0, $default_select=0)
	{
		$resp['data'] = array();
		if ($country_id!='') {
			$condition = array('country_code' => $country_id);
			$order_by = array('city_name' => 'asc');
			$option_list = $this->custom_db->single_table_records('all_api_hotel_cities', 'origin as k, city_name as v', $condition, 0, 1000000, $order_by);
			
			if (valid_array($option_list['data'])) {
				$resp['data'] = get_compressed_output(generate_options($option_list['data'], array($default_select)));
			}
		}
		header('Content-type:application/json');
		echo json_encode($resp);
		exit;
	}

	/**
	 *
	 * @param $continent_id
	 * @param $default_select
	 * @param $zone_id
	 */
	function get_country_list($continent_id=array(), $default_select=0,$zone_id=0)
	{
		$this->load->model('general_model');
		$continent_id=urldecode($continent_id);
		if (intval($continent_id) != 0) {
			$option_list = $this->general_model->get_country_list($continent_id,$zone_id);
			if (valid_array($option_list['data'])) {
				echo get_compressed_output(generate_options($option_list['data'], array($default_select)));
			}
		}
	}

	/**
	 *Get Location List
	 */
	function location_list($limit=AUTO_SUGGESTION_LIMIT)
	{
		$chars = $_GET['term'];
		$list = $this->general_model->get_location_list($chars, $limit);
		$temp_list = '';
		if (valid_array($list) == true) {
			foreach ($list as $k => $v) {
				$temp_list[] = array('id' => $k, 'label' => $v['name'], 'value' => $v['origin']);
			}
		}
		header('Content-type:application/json');
		echo json_encode($temp_list);
		exit;
	}

	/**
	 * Get City Based on Country id
	 */
	public function get_city_lists()
 	{
	     $country_id = $this->input->get('country_id');
	          $get_resulted_data =  $this->custom_db->single_table_records('api_city_list', '*',array('country' => $country_id), 0, 100000000, array('destination' => 'asc'));
			   if(!empty($get_resulted_data['data'])){ 
			       $html = "<option value=''>Select City</option>";
			        foreach( $get_resulted_data['data'] as  $get_resulted_data_sub){			  
			  			
			  			$html= $html."<option value=".$get_resulted_data_sub['origin'].">".$get_resulted_data_sub['destination']."</option>";
			  			
			        } 
			    }else{
			         $html = "<option value=''>No City Found</option>";
			    }
			     echo $html;
			     exit;
	}
	
	/**
	 *Get Location List
	 */
	function city_list($limit=AUTO_SUGGESTION_LIMIT)
	{
		$chars = $_GET['term'];
		$list = $this->general_model->get_city_list($chars, $limit);
		$temp_list = '';
		if (valid_array($list) == true) {
			foreach ($list as $k => $v) {
				$temp_list[] = array('id' => $k, 'label' => $v['name'], 'value' => $v['origin']);
			}
		}
		header('Content-type:application/json');
		echo json_encode($temp_list);
		exit;
	}

	/**
	 * Balu A
	 * @param unknown_type $currency_origin origin of currency - default to USD
	 */
	function get_currency_value($currency_origin=0)
	{
		$data = $this->custom_db->single_table_records('currency_converter', 'value', array('id' => intval($currency_origin)));
		header('Content-type:application/json');
		if (valid_array($data['data'])) {
			$response = $data['data'][0]['value'];
		} else {
			$response = 1;
		}
		echo json_encode(array('value' => $response));
		exit;
	}

	/**
	 * Balu A
	 */
	function forgot_password()
	{
		$post_data = $this->input->post();
		extract($post_data);
		//email, phone
		$condition['email'] = provab_encrypt($email);
		$condition['phone'] = $phone;
		$condition['status'] = ACTIVE;
		$condition['domain_list_fk'] = get_domain_auth_id();
		$user_record = $this->custom_db->single_table_records('user', 'email, password, user_id, first_name, last_name', $condition);
		if ($user_record['status'] == true and valid_array($user_record['data']) == true) {
			$user_record['data'][0]['password'] = time();
			//send email
			
			$mail_template = $this->template->isolated_view('general/forgot_password_template', $user_record['data'][0]);

			$user_record['data'][0]['password'] = provab_encrypt(md5(trim($user_record['data'][0]['password'])));
			
			$this->custom_db->update_record('user', $user_record['data'][0], array('user_id' => intval($user_record['data'][0]['user_id'])));
			$this->load->library('provab_mailer');
			$this->provab_mailer->send_mail($email, 'Password Reset : '.domain_name(), $mail_template);
			$data = 'Password Has Been Sent Reset Successfully and To Your Email ID';
			$status = true;
		} else {
			$data = 'Please Provide Correct Data To Identify Your Account';
			$status = false;
		}
		header('content-type:application/json');
		echo json_encode(array('status' => $status, 'data' => $data));
		exit;
	}

	//---------------------------------------------------------------- Booking Events Starts
	/**
	* Load Booking Events of all the modules
	*/
	function booking_events()
	{
		$status = true;
		$data = array();
		$calendar_events = array();
		$condition = array(array('BD.created_datetime', '>=', $this->db->escape(date('Y-m-d', strtotime(subtract_days_from_date(90))))));//of last 30 days only
		if (is_active_bus_module()) {
			$condition = array(array('BD.app_reference', '=', 'ID.app_reference'), array('ID.journey_datetime', '>=', $this->db->escape(date('Y-m-d', strtotime(subtract_days_from_date(90))))));
			$calendar_events = array_merge($calendar_events, $this->bus_booking_events($condition));
		}
		if (is_active_hotel_module()) {
			$condition = array(array('BD.hotel_check_in', '>=', $this->db->escape(date('Y-m-d', strtotime(subtract_days_from_date(90))))));
			$calendar_events = array_merge($calendar_events, $this->hotel_booking_events($condition));
		}
		if (is_active_airline_module()) {
			$condition = array(array('BD.journey_start', '>=', $this->db->escape(date('Y-m-d', strtotime(subtract_days_from_date(90))))));
			$calendar_events = array_merge($calendar_events, $this->flight_booking_events($condition));
		}
		if (is_active_transferv1_module()) {
			$condition = array(array('BD.travel_date', '>=', $this->db->escape(date('Y-m-d', strtotime(subtract_days_from_date(90))))));
			$calendar_events = array_merge($calendar_events, $this->transfers_booking_events($condition));
		}
		if (is_active_sightseeing_module()) {
			$condition = array(array('BD.travel_date', '>=', $this->db->escape(date('Y-m-d', strtotime(subtract_days_from_date(90))))));
			$calendar_events = array_merge($calendar_events, $this->sightseeing_booking_events($condition));
		}


		header('content-type:application/json');
		echo json_encode(array('status' => $status, 'data' => $calendar_events));
		exit;
	}

	/**
	 * Hotel Booking Events Summary
	 * @param array $condition
	 */
	private function hotel_booking_events($condition)
	{
		$this->load->model('hotel_model');
		$data_list = $this->hotel_model->booking($condition);
		$this->load->library('booking_data_formatter');
		$table_data = $this->booking_data_formatter->format_hotel_booking_data($data_list, 'b2c');
		$booking_details = $table_data['data']['booking_details'];
		$calendar_events = array();
		if (valid_array($booking_details) == true) {
			$key = 0;
			foreach ($booking_details as $k => $v) {
				$calendar_events[$key]['title'] = $v['app_reference'].'-'.$v['status'];
				$calendar_events[$key]['start'] = $v['hotel_check_in'];
				$calendar_events[$key]['tip'] = $v['app_reference'].'-PNR:'.$v['confirmation_reference'].'-From:'.$v['hotel_check_in'].', To:'.$v['hotel_check_out'].'-'.$v['status'].'- Click To View More Details';
				$calendar_events[$key]['href'] = hotel_voucher_url($v['app_reference'], $v['booking_source'], $v['status']);
				$calendar_events[$key]['add_class'] = 'hand-cursor event-hand hotel-booking';
				$key++;
			}
		}
		return $calendar_events;
	}

	/**
	 * Flight Booking Events Summary
	 * @param array $condition
	 */
	private function flight_booking_events($condition)
	{
		$this->load->model('flight_model');
		$data_list = $this->flight_model->booking($condition);
		$this->load->library('booking_data_formatter');
		$table_data = $this->booking_data_formatter->format_flight_booking_data($data_list, 'b2c');
		$booking_details = $table_data['data']['booking_details'];
		$calendar_events = array();
		if (valid_array($booking_details) == true) {
			$key = 0;
			foreach ($booking_details as $k => $v) {
				$calendar_events[$key]['title'] = $v['app_reference'].'-'.$v['status'];
				$calendar_events[$key]['start'] = $v['journey_start'];
				$calendar_events[$key]['tip'] = $v['app_reference'].',From:'.$v['journey_from'].', To:'.$v['journey_to'].'-'.$v['status'].'- Click To View More Details';
				$calendar_events[$key]['href'] = flight_voucher_url($v['app_reference'], $v['booking_source'], $v['status']);
				$calendar_events[$key]['add_class'] = 'hand-cursor event-hand flight-booking';
				$key++;
			}
		}

		return $calendar_events;
	}

	/**
	 * Bus Booking Events Summary
	 * @param array $condition
	 */
	private function bus_booking_events($condition)
	{
		$this->load->model('bus_model');
		$data_list = $this->bus_model->booking($condition);
		$this->load->library('booking_data_formatter');
		$table_data = $this->booking_data_formatter->format_bus_booking_data($data_list, 'b2c');
		$booking_details = $table_data['data']['booking_details'];
		$calendar_events = array();
		if (valid_array($booking_details) == true) {
			$key = 0;
			foreach ($booking_details as $k => $v) {
				$calendar_events[$key]['title'] = $v['app_reference'].'-'.$v['status'];
				$calendar_events[$key]['start'] = $v['departure_datetime'];
				$calendar_events[$key]['tip'] = $v['app_reference'].'-PNR:'.$v['pnr'].'-From:'.$v['departure_from'].', To:'.$v['arrival_to'].'-'.$v['status'].'- Click To View More Details';
				$calendar_events[$key]['href'] = bus_voucher_url($v['app_reference'], $v['booking_source'], $v['status']);
				$calendar_events[$key]['add_class'] = 'hand-cursor event-hand bus-booking';
				//$calendar_events[$k]['prepend_element'] = '<i class="fa fa-bus"></i>';
				$key++;
			}
		}
		return $calendar_events;
	}
	/**
	 * Sightseeing Booking Events Summary
	 * @param array $condition
	 */
	private function sightseeing_booking_events($condition){
		$this->load->model('sightseeing_model');
		$data_list = $this->sightseeing_model->booking($condition);
		$this->load->library('booking_data_formatter');
		$table_data = $this->booking_data_formatter->format_sightseeing_booking_data($data_list, 'b2c');
		$booking_details = $table_data['data']['booking_details'];
		$calendar_events = array();

		if (valid_array($booking_details) == true) {
			$key = 0;
			foreach ($booking_details as $k => $v) {
				$calendar_events[$key]['title'] = $v['app_reference'].'-'.$v['status'];
				$calendar_events[$key]['start'] = $v['travel_date'];
				$calendar_events[$key]['tip'] = $v['app_reference'].'-PNR:'.$v['confirmation_reference'].'-From:'.$v['destination_name'].', Travel Date:'.$v['travel_date'].'-'.$v['status'].'- Click To View More Details';
				$calendar_events[$key]['href'] = sightseeing_voucher_url($v['app_reference'], $v['booking_source'], $v['status']);
				$calendar_events[$key]['add_class'] = 'hand-cursor event-hand sightseeing-booking';
				//$calendar_events[$k]['prepend_element'] = '<i class="fa fa-bus"></i>';
				$key++;
			}
		}
		return $calendar_events;
	}
	/**
	 * Transfers Booking Events Summary
	 * @param array $condition
	 */
	private function transfers_booking_events($condition){
		$this->load->model('transferv1_model');
		$data_list = $this->transferv1_model->booking($condition);
		$this->load->library('booking_data_formatter');
		$table_data = $this->booking_data_formatter->format_transferv1_booking_data($data_list, 'b2c');
		$booking_details = $table_data['data']['booking_details'];
		$calendar_events = array();

		if (valid_array($booking_details) == true) {
			$key = 0;
			foreach ($booking_details as $k => $v) {
				$calendar_events[$key]['title'] = $v['app_reference'].'-'.$v['status'];
				$calendar_events[$key]['start'] = $v['travel_date'];
				$calendar_events[$key]['tip'] = $v['app_reference'].'-PNR:'.$v['confirmation_reference'].'-From:'.$v['destination_name'].', Travel Date:'.$v['travel_date'].'-'.$v['status'].'- Click To View More Details';
				$calendar_events[$key]['href'] = transfers_voucher_url($v['app_reference'], $v['booking_source'], $v['status']);
				$calendar_events[$key]['add_class'] = 'hand-cursor event-hand transfers-booking';
				//$calendar_events[$k]['prepend_element'] = '<i class="fa fa-bus"></i>';
				$key++;
			}
		}
		return $calendar_events;
	}

	
	//---------------------------------------------------------------- Booking Events End

	//---------------------------------------------------------------- Trip Events Start
	/**
	* Load Trip Events of all the modules
	*/
	function trip_events()
	{
		$status = true;
		$data = array();
		$trip_events = array();
		$start_date = date('Y-m-d', strtotime(subtract_days_from_date(30)));
		if (is_active_bus_module()) {
			$trip_events = array_merge($trip_events, $this->bus_trip_events($start_date));
		}
		if (is_active_hotel_module()) {
			$trip_events = array_merge($trip_events, $this->hotel_trip_events($start_date));
		}
		if (is_active_airline_module()) {
			$trip_events = array_merge($trip_events, $this->flight_trip_events($start_date));
		}
		header('content-type:application/json');
		echo json_encode(array('status' => $status, 'data' => $trip_events));
		exit;
	}

	/**
	 * Trip Event Details
	 * @param Date $start_date
	 */
	private function hotel_trip_events($start_date)
	{
		$this->load->model('hotel_model');
		$condition = array(array('BD.hotel_check_in', '>=', $this->db->escape($start_date)));
		$data_list = $this->hotel_model->booking($condition);
		$trip_events = array();
		if (valid_array($data_list) == true) {
			$current_date = db_current_datetime();
			foreach ($data_list as $k => $v) {
				$day_label = day_count_label(get_date_difference($current_date, $v['hotel_check_in']));
				$trip_events[$k]['title'] = $day_label.$v['name'].'-'.$v['status'];
				$trip_events[$k]['start'] = $v['hotel_check_in'];
				$trip_events[$k]['end'] = $v['hotel_check_out'];
				$trip_events[$k]['tip'] = $v['app_reference'].'-PNR:'.$v['confirmation_reference'].'-From:'.$v['hotel_check_in'].', To:'.$v['hotel_check_out'].'-'.$v['status'].'- Click To View More Details';
				$trip_events[$k]['href'] = hotel_voucher_url($v['app_reference'], $v['booking_source'], $v['status']);
				$trip_events[$k]['add_class'] = 'hand-cursor event-hand '.event_class($v['hotel_check_in']);
				$trip_events[$k]['prepend_element'] = '<i class="fa fa-bed hotel-booking"></i>';
			}
		}
		return $trip_events;
	}

	/**
	 * Trip Event Details
	 * @param Date $start_date
	 */
	private function flight_trip_events($start_date)
	{
		$this->load->model('flight_model');
		$condition = array(array('BD.journey_start', '>=', $this->db->escape($start_date)));
		$data_list = $this->flight_model->booking($condition);
		$trip_events = array();
		if (valid_array($data_list) == true) {
			$current_date = db_current_datetime();
			foreach ($data_list as $k => $v) {
				$day_label = day_count_label(get_date_difference($current_date, $v['journey_start']));
				$trip_events[$k]['title'] = $day_label.$v['name'].'-'.$v['status'];
				$trip_events[$k]['start'] = $v['journey_start'];
				$trip_events[$k]['end'] = $v['journey_end'];
				$trip_events[$k]['tip'] = $v['app_reference'].',From:'.$v['journey_from'].', To:'.$v['journey_to'].'-'.$v['status'].'- Click To View More Details';
				$trip_events[$k]['href'] = flight_voucher_url($v['app_reference'], $v['booking_source'], $v['status']);
				$trip_events[$k]['add_class'] = 'hand-cursor event-hand '.event_class($v['journey_start']);
				$trip_events[$k]['prepend_element'] = '<i class="fa fa-plane flight-booking"></i>';
			}
		}

		return $trip_events;
	}

	/**
	 * Get All Trip Events Scheduled between specific dates
	 * @param array $condition condition to fetch the records (ex: between dates)
	 * @return multitype: $trip_events array of data
	 */
	private function bus_trip_events($start_date)
	{
		$this->load->model('bus_model');
		$condition = array(array('ID.journey_datetime', '>=', $this->db->escape($start_date)));
		$data_list = $this->bus_model->booking($condition);
		$trip_events = array();
		if (valid_array($data_list) == true) {
			$current_date = db_current_datetime();
			foreach ($data_list as $k => $v) {
				$day_label = day_count_label(get_date_difference($current_date, $v['departure_datetime']));
				$trip_events[$k]['title'] = $day_label.$v['name'].'-'.$v['status'];
				$trip_events[$k]['start'] = $v['departure_datetime'];
				$trip_events[$k]['end'] = $v['arrival_datetime'];
				$trip_events[$k]['tip'] = $v['app_reference'].'-PNR:'.$v['pnr'].'-From:'.$v['departure_from'].', To:'.$v['arrival_to'].'-'.$v['status'].'- Click To View More Details';
				$trip_events[$k]['href'] = bus_voucher_url($v['app_reference'], $v['booking_source'], $v['status']);
				$trip_events[$k]['add_class'] = 'hand-cursor event-hand '.event_class($v['departure_datetime']);
				$trip_events[$k]['prepend_element'] = '<i class="fa fa-bus bus-booking"></i>';
			}
		}
		return $trip_events;
	}
	//---------------------------------------------------------------- Trip Events End

	public function itinerary_loop($duration)
	{
		$data['duration'] = $duration;
		$this->template->view('suppliers/duration_itinerary', $data);
	}
	/**
	 * Balu A
	 * Autosuggest Agency Name in Commisson Part
	 */
	public function auto_suggest_agency_name()
	{
		$term = $this->input->get('term'); //retrieve the search term that autocomplete sends
		$term = trim(strip_tags($term));
		$result = array();
		$this->load->model('domain_management_model');
		$core_agent_details = $this->domain_management_model->auto_suggest_agency_name($term);
		$agent_details = array();
		foreach($core_agent_details as $k => $v){
			$agent_details['label'] = $v['agency_name'].'-'.$v['uuid'];
			$agent_details['value'] = $v['agency_name'];
			array_push($result,$agent_details);
		}
		$this->output_compressed_data($result);
	}
	/**
	 * Balu A
	 * Autosuggest Promo Code
	 */
	public function auto_suggest_promo_code()
	{
		$term = $this->input->get('term'); //retrieve the search term that autocomplete sends
		$term = trim(strip_tags($term));
		$result = array();
		$this->load->model('module_model');
		$core_promocode_details = $this->module_model->auto_suggest_promo_code($term);
		$promocode_details = array();
		foreach($core_promocode_details as $k => $v){
			$promocode_details['label'] = $v['promo_code'].'-'.ucfirst($v['module_type']);
			$promocode_details['value'] = $v['promo_code'];
			array_push($result,$promocode_details);
		}
		$this->output_compressed_data($result);
	}
	/**
	 * Check Promo Codes, is exists already or not?
	 */
	function is_unique_promocode()
	{
		$get_data = $this->input->get();
		if(isset($get_data['promo_code']) == true && empty($get_data['promo_code']) == false) {
			$result['status'] = true;
			$promo_code = trim($get_data['promo_code']);
			$this->load->model('module_model');
			$data = $this->module_model->is_unique_promocode($promo_code);
			if(valid_array($data) == true) {
				$result['status'] = false;
				$result['promo_code'] = trim($data['promo_code']);
			}
			$this->output_compressed_data($result);
		}
	}
	/**
	 * Compress and output data
	 * @param array $data
	 */
	private function output_compressed_data($data)
	{
		while (ob_get_level() > 0) { ob_end_clean() ; }
		ob_start("ob_gzhandler");
		header('Content-type:application/json');
		echo json_encode($data);
		ob_end_flush();
		exit;
	}
}
