<?php
/**
 * Library which has generic functions to get data
 *
 * @package    Provab Application
 * @subpackage Hotel Model
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V2
 */
Class Hotel_Model extends CI_Model
{
	private $master_search_data;
	/**
	 * return booking list
	 */
	function booking($condition=array(), $count=false, $offset=0, $limit=100000000000)
	{
		$condition = $this->custom_db->get_custom_condition($condition);
		//BT, CD, ID
		if ($count) {
			$query = 'select count(distinct(BD.app_reference)) as total_records 
					from hotel_booking_details BD
					join hotel_booking_itinerary_details AS HBID on BD.app_reference=HBID.app_reference
					join payment_option_list AS POL on BD.payment_mode=POL.payment_category_code 
					where BD.domain_origin='.get_domain_auth_id().' '.$condition;
			$data = $this->db->query($query)->row_array();
			return $data['total_records'];
		} else {
			$this->load->library('booking_data_formatter');
			$response['status'] = SUCCESS_STATUS;
			$response['data'] = array();
			$booking_itinerary_details	= array();
			$booking_customer_details	= array();
			$cancellation_details = array();
			$bd_query = 'select * from hotel_booking_details AS BD 
						WHERE BD.domain_origin='.get_domain_auth_id().''.$condition.'
						order by BD.origin desc limit '.$offset.', '.$limit;
			$booking_details = $this->db->query($bd_query)->result_array();
			$app_reference_ids = $this->booking_data_formatter->implode_app_reference_ids($booking_details);
			if(empty($app_reference_ids) == false) {
				$id_query = 'select * from hotel_booking_itinerary_details AS ID 
							WHERE ID.app_reference IN ('.$app_reference_ids.')';
				$cd_query = 'select * from hotel_booking_pax_details AS CD 
							WHERE CD.app_reference IN ('.$app_reference_ids.')';
				$cancellation_details_query = 'select * from hotel_cancellation_details AS HCD 
							WHERE HCD.app_reference IN ('.$app_reference_ids.')';
				$booking_itinerary_details	= $this->db->query($id_query)->result_array();
				$booking_customer_details	= $this->db->query($cd_query)->result_array();
				$cancellation_details	= $this->db->query($cancellation_details_query)->result_array();
			}
			$response['data']['booking_details']			= $booking_details;
			$response['data']['booking_itinerary_details']	= $booking_itinerary_details;
			$response['data']['booking_customer_details']	= $booking_customer_details;
			$response['data']['cancellation_details']	= $cancellation_details;
			return $response;
		}
	}
//----------------------sdp ------- 

function b2c_hotel_report($condition=array(), $count=false, $offset=0, $limit=100000000000)
	{
		$condition = $this->custom_db->get_custom_condition($condition);
		
		if(isset($condition) == true)
		{
			$offset = 0;
		}else{
			$offset = $offset;
		}

		//BT, CD, ID
		if ($count) {
			$query = 'select count(distinct(BD.app_reference)) as total_records
					from hotel_booking_details BD
					join hotel_booking_itinerary_details AS HBID on BD.app_reference=HBID.app_reference
					join payment_option_list AS POL on BD.payment_mode=POL.payment_category_code
					left join user as U on BD.created_by_id = U.user_id 
					where (U.user_type='.B2C_USER.' OR BD.created_by_id = 0) AND BD.domain_origin='.get_domain_auth_id().' '.$condition.'';

			$data = $this->db->query($query)->row_array();
			return $data['total_records'];
		} else {
			$this->load->library('booking_data_formatter');
			$response['status'] = SUCCESS_STATUS;
			$response['data'] = array();
			$booking_itinerary_details	= array();
			$booking_customer_details	= array();
			$cancellation_details = array();
			
			$bd_query = 'select BD.* ,U.user_name,U.first_name,U.last_name from hotel_booking_details AS BD
					     left join user U on BD.created_by_id =U.user_id 					     
						 WHERE  (U.user_type='.B2C_USER.' OR BD.created_by_id = 0) AND BD.domain_origin='.get_domain_auth_id().''.$condition.'						 
						 order by BD.created_datetime desc, BD.origin desc limit '.$offset.', '.$limit.'';

			$booking_details = $this->db->query($bd_query)->result_array();
			$app_reference_ids = $this->booking_data_formatter->implode_app_reference_ids($booking_details);
			if(empty($app_reference_ids) == false) {
				$id_query = 'select * from hotel_booking_itinerary_details AS ID 
							WHERE ID.app_reference IN ('.$app_reference_ids.')';
				$cd_query = 'select * from hotel_booking_pax_details AS CD 
							WHERE CD.app_reference IN ('.$app_reference_ids.')';
				$cancellation_details_query = 'select * from hotel_cancellation_details AS HCD 
							WHERE HCD.app_reference IN ('.$app_reference_ids.')';
				$payment_details_query = 'select * from  payment_gateway_details AS PD
							WHERE PD.app_reference IN ('.$app_reference_ids.')';

				$booking_itinerary_details	= $this->db->query($id_query)->result_array();
				$booking_customer_details	= $this->db->query($cd_query)->result_array();
				$cancellation_details	= $this->db->query($cancellation_details_query)->result_array();
				$payment_details = $this->db->query($payment_details_query)->result_array();
			}
			$response['data']['booking_details']			= $booking_details;
			$response['data']['booking_itinerary_details']	= $booking_itinerary_details;
			$response['data']['booking_customer_details']	= $booking_customer_details;
			$response['data']['cancellation_details']	= $cancellation_details;
			$response['data']['payment_details']	= $payment_details;
		
			return $response;
		}
	}

function b2b_hotel_report($condition=array(), $count=false, $offset=0, $limit=100000000000, $b_status='')
	{
		$condition = $this->custom_db->get_custom_condition($condition);

		if(isset($condition) == true)
		{
			$offset = 0;
		}else{
			$offset = $offset;
		}
		if($b_status == "confirmed_cancelled"){
			$condition = 'AND (BD.status = '.$this->db->escape('BOOKING_CONFIRMED').' OR BD.status = '.$this->db->escape('BOOKING_CANCELLED').')';
		}

		//BT, CD, ID
		if ($count) {
			$query = 'select count(distinct(BD.app_reference)) as total_records 
					from hotel_booking_details BD
					join hotel_booking_itinerary_details AS HBID on BD.app_reference=HBID.app_reference
					join payment_option_list AS POL on BD.payment_mode=POL.payment_category_code 
					left join user as U on BD.created_by_id = U.user_id  where U.user_type='.B2B_USER.' AND BD.domain_origin='.get_domain_auth_id().' '.$condition;
			$data = $this->db->query($query)->row_array();
			return $data['total_records'];
		} else {
			$this->load->library('booking_data_formatter');
			$response['status'] = SUCCESS_STATUS;
			$response['data'] = array();
			$booking_itinerary_details	= array();
			$booking_customer_details	= array();
			$cancellation_details = array();
			$bd_query = 'select BD.* ,U.agency_name,U.first_name,U.last_name, U.uuid AS agency_id, BS.name AS supp_name from hotel_booking_details AS BD
					     left join user U on BD.created_by_id =U.user_id
					     left join booking_source BS on BS.source_id = BD.booking_source 				     
						 WHERE  U.user_type='.B2B_USER.' AND BD.domain_origin='.get_domain_auth_id().' '.$condition.'
						 order by BD.created_datetime desc, BD.origin desc limit '.$offset.', '.$limit;
			$booking_details = $this->db->query($bd_query)->result_array();
			$app_reference_ids = $this->booking_data_formatter->implode_app_reference_ids($booking_details);
			if(empty($app_reference_ids) == false) {
				$id_query = 'select * from hotel_booking_itinerary_details AS ID 
							WHERE ID.app_reference IN ('.$app_reference_ids.')';
				$cd_query = 'select * from hotel_booking_pax_details AS CD 
							WHERE CD.app_reference IN ('.$app_reference_ids.')';
				$cancellation_details_query = 'select * from hotel_cancellation_details AS HCD 
							WHERE HCD.app_reference IN ('.$app_reference_ids.')';
				$booking_itinerary_details	= $this->db->query($id_query)->result_array();
				$booking_customer_details	= $this->db->query($cd_query)->result_array();
				$cancellation_details	= $this->db->query($cancellation_details_query)->result_array();
			}
			$response['data']['booking_details']			= $booking_details;
			$response['data']['booking_itinerary_details']	= $booking_itinerary_details;
			$response['data']['booking_customer_details']	= $booking_customer_details;
			$response['data']['cancellation_details']	= $cancellation_details;
			return $response;
		}
	}




	/**
	 * Return Booking Details based on the app_reference passed
	 * @param $app_reference
	 * @param $booking_source
	 * @param $booking_status
	 */
	function get_booking_details($app_reference, $booking_source, $booking_status='')
	{
		$response['status'] = FAILURE_STATUS;
		$response['data'] = array();
		$bd_query = 'select BD.*, ud.logo as user_image from hotel_booking_details AS BD INNER JOIN b2b_user_details as ud on BD.created_by_id=ud.user_oid WHERE BD.app_reference like '.$this->db->escape($app_reference);
		if (empty($booking_source) == false) {
			$bd_query .= '	AND BD.booking_source = '.$this->db->escape($booking_source);
		}
		if (empty($booking_status) == false) {
			$bd_query .= ' AND BD.status = '.$this->db->escape($booking_status);
		}
		$id_query = 'select * from hotel_booking_itinerary_details AS ID WHERE ID.app_reference='.$this->db->escape($app_reference);
		$cd_query = 'select * from hotel_booking_pax_details AS CD WHERE CD.app_reference='.$this->db->escape($app_reference);
		$cancellation_details_query = 'select HCD.* from hotel_cancellation_details AS HCD WHERE HCD.app_reference='.$this->db->escape($app_reference);
		$response['data']['booking_details']			= $this->db->query($bd_query)->result_array();
		$response['data']['booking_itinerary_details']	= $this->db->query($id_query)->result_array();
		$response['data']['booking_customer_details']	= $this->db->query($cd_query)->result_array();
		$response['data']['cancellation_details']	= $this->db->query($cancellation_details_query)->result_array();
		if (valid_array($response['data']['booking_details']) == true and valid_array($response['data']['booking_itinerary_details']) == true and valid_array($response['data']['booking_customer_details']) == true) {
			$response['status'] = SUCCESS_STATUS;
		}
		return $response;
	}

	/**
	 * return all booking events
	 */
	function booking_events()
	{
		//BT, CD, ID
		$query = 'select * from hotel_booking_details where domain_origin='.get_domain_auth_id();
		return $this->db->query($query)->result_array();
	}

	function get_monthly_booking_summary($condition=array())
	{
		//Balu A
		$condition = $this->custom_db->get_custom_condition($condition);
		$query = 'select count(distinct(BD.app_reference)) AS total_booking, 
				sum(HBID.total_fare+HBID.admin_markup+HBID.agent_markup) as monthly_payment, sum(HBID.admin_markup) as monthly_earning, 
				MONTH(BD.created_datetime) as month_number 
				from hotel_booking_details AS BD
				join hotel_booking_itinerary_details AS HBID on BD.app_reference=HBID.app_reference
				where (YEAR(BD.created_datetime) BETWEEN '.date('Y').' AND '.date('Y', strtotime('+1 year')).')  and BD.domain_origin='.get_domain_auth_id().' '.$condition.'
				GROUP BY YEAR(BD.created_datetime), 
				MONTH(BD.created_datetime)';
		return $this->db->query($query)->result_array();
	}

	function monthly_search_history($year_start, $year_end)
	{
		$query = 'select count(*) AS total_search, MONTH(created_datetime) as month_number from search_hotel_history where
		(YEAR(created_datetime) BETWEEN '.$year_start.' AND '.$year_end.') AND domain_origin='.get_domain_auth_id().' 
		AND search_type="'.META_ACCOMODATION_COURSE.'"
		GROUP BY YEAR(created_datetime), MONTH(created_datetime)';
		return $this->db->query($query)->result_array();
	}

	function top_search($year_start, $year_end)
	{
		$query = 'select count(*) AS total_search, CONCAT(country,"-",city) label from search_hotel_history where
		(YEAR(created_datetime) BETWEEN '.$year_start.' AND '.$year_end.') AND domain_origin='.get_domain_auth_id().' 
		AND search_type="'.META_ACCOMODATION_COURSE.'"
		GROUP BY CONCAT(country, city) order by count(*) desc, created_datetime desc limit 0, 15';
		return $this->db->query($query)->result_array();
	}

	/**
	 *
	 */
	function get_static_response($token_id)
	{
		$static_response = $this->custom_db->single_table_records('test', '*', array('origin' => intval($token_id)));
		return json_decode($static_response['data'][0]['test'], true);
	}
	/**
	 * Balu A
	 * Update Cancellation details and Status
	 * @param $AppReference
	 * @param $cancellation_details
	 */
	public function update_cancellation_details($AppReference, $cancellation_details)
	{
		$AppReference = trim($AppReference);
		$booking_status = 'BOOKING_CANCELLED';
		//1. Add Cancellation details
		$this->update_cancellation_refund_details($AppReference, $cancellation_details);
		//2. Update Master Booking Status
		$this->custom_db->update_record('hotel_booking_details', array('status' => $booking_status), array('app_reference' => $AppReference));//later
		//3.Update Itinerary Status
		$this->custom_db->update_record('hotel_booking_itinerary_details', array('status' => $booking_status), array('app_reference' => $AppReference));//later
		//4.Update Pax Status
		$this->custom_db->update_record('hotel_booking_pax_details', array('status' => $booking_status), array('app_reference' => $AppReference));//later
	}
	/**
	 * Add Cancellation details
	 * @param unknown_type $AppReference
	 * @param unknown_type $cancellation_details
	 */
	private function update_cancellation_refund_details($AppReference, $cancellation_details)
	{
		$hotel_cancellation_details = array();
		$hotel_cancellation_details['app_reference'] = 				$AppReference;
		$hotel_cancellation_details['ChangeRequestId'] = 			$cancellation_details['BookingId'];
		/*$hotel_cancellation_details['ChangeRequestStatus'] = 		$cancellation_details['ChangeRequestStatus'];
		$hotel_cancellation_details['status_description'] = 		@$cancellation_details['StatusDescription'];
		$hotel_cancellation_details['API_RefundedAmount'] = 		@$cancellation_details['RefundedAmount'];
		$hotel_cancellation_details['API_CancellationCharge'] = 	@$cancellation_details['CancellationCharge'];
		if($cancellation_details['ChangeRequestStatus'] == 3){
			$hotel_cancellation_details['cancellation_processed_on'] =	date('Y-m-d H:i:s');
		}*/
		$hotel_cancellation_details['ChangeRequestStatus'] = '';
		$hotel_cancellation_details['status_description'] = '';
		$hotel_cancellation_details['cancellation_processed_on'] = date('Y-m-d H:i:s');
		$hotel_cancellation_details['refund_amount'] = '0.00';
		$hotel_cancellation_details['cancellation_charge'] = '0.00';
		$hotel_cancellation_details['refund_status'] = 'INPROGRESS';
		$hotel_cancellation_details['refund_comments'] = '';
		$hotel_cancellation_details['refund_date'] = date('Y-m-d H:i:s');
		$hotel_cancellation_details['attributes'] = json_encode($cancellation_details);
		$hotel_cancellation_details['created_by_id'] = $this->entity_user_id;
		$hotel_cancellation_details['created_datetime'] = date('Y-m-d H:i:s');

		$cancel_details_exists = $this->custom_db->single_table_records('hotel_cancellation_details', '*', array('app_reference' => $AppReference));
		if($cancel_details_exists['status'] == true) {
			//Update the Data
			unset($hotel_cancellation_details['app_reference']);
			$this->custom_db->update_record('hotel_cancellation_details', $hotel_cancellation_details, array('app_reference' => $AppReference));
		} else {
			//Insert Data
			//$hotel_cancellation_details['created_by_id'] = 				(int)@$this->entity_user_id;
			//$hotel_cancellation_details['created_datetime'] = 			date('Y-m-d H:i:s');
			$data['cancellation_requested_on'] = date('Y-m-d H:i:s');
			$this->custom_db->insert_record('hotel_cancellation_details',$hotel_cancellation_details);
		}
	}


	///** Offline booking **///

	public function offline_hotel_book($hotel_data,$app_reference){
		
		$status = empty($hotel_data['status'])?'BOOKING_PENDING':$hotel_data['status'];
		$agent_id = $GLOBALS['CI']->entity_user_id;
		//debug($hotel_data);die('8888');
		/**Hotel booking details start**/

		$booking_details['domain_origin'] = 1;
		$booking_details['status'] = $hotel_data['status'];
		$booking_details['app_reference'] = $app_reference;
		$booking_details['booking_source'] = $hotel_data['suplier_id'];

		$booking_details['booking_id'] = $hotel_data['ref_no'][0];
		$booking_details['confirmation_reference'] = $hotel_data['ref_no'][0];
		$booking_details['hotel_name'] = $hotel_data['career_onward'][0];
		$booking_details['star_rating'] = $hotel_data['star_rate'][0];
		$booking_details['hotel_code'] = '';
		$booking_details['phone_code'] = '';
		$booking_details['phone_number'] = $hotel_data ['passenger_phone'];
		$booking_details['alternate_number'] = $hotel_data ['passenger_phone'];
		$booking_details['email'] = $hotel_data ['passenger_email'];
		$booking_details['hotel_check_in'] = date('Y-m-d',strtotime($hotel_data ['dep_date_onward'][0]));
		$booking_details['hotel_check_out'] = date('Y-m-d',strtotime($hotel_data ['arr_date_onward'][0]));
		$booking_details['payment_mode'] = 'offline';
		$booking_details['convinence_value'] = 0.0;
		$booking_details['convinence_value_type'] = 'plus';
		$booking_details['convinence_per_pax'] = 0;
		$booking_details['convinence_amount'] = 0;
		$booking_details['promo_code'] = 0;
		$booking_details['discount'] = 0;
		$booking_details['booking_billing_type'] = 'offline';
		$booking_details['created_datetime'] = date ( 'Y-m-d H:i:s' );
		$booking_details['currency'] = admin_base_currency();
		
		//Construct Attributes
		$attributes["HotelName"] = $hotel_data['career_onward'][0];
		$attributes["StarRating"] = $hotel_data['star_rate'][0];
		$attributes["HotelAddress"] = $hotel_data['hotel_address']." Phone: ".$hotel_data['hotel_phone']." Email: ".$hotel_data['hotel_email'];
		$attributes["CancellationPolicy"][0] = $hotel_data['hotel_cancel_policy'];
		$attributes["Boarding_details"][0] = $hotel_data['room_inclusions'];
		//Construct Attributes End
		
		//$booking_details['gst'] = '0.0';
		//$booking_details['cancel_policy'] = '';
		$booking_details['attributes'] = json_encode($attributes);
		$booking_details['hb_supplier_code'] = '';
		$booking_details['hb_vat_number'] = '';
		$booking_details['booking_billing_type'] = 'offline';
		//As Per client demand - agent wise offline booking
		if($hotel_data['agent_id'] > 0){
			$booking_details['created_by_id'] = $hotel_data['agent_id'];
			$booking_details['booked_by'] = $agent_id;
		}else{
			$booking_details['created_by_id'] = $agent_id;
			$booking_details['booked_by'] = $agent_id;
		}
		
		$book_id = $this->custom_db->insert_record ('hotel_booking_details', $booking_details );
		$book_id = @$book_id['insert_id'];
		//debug($book_id);die();
		/**Hotel booking details End**/
		//$book_id = 77;
		//$app_reference = 'HB12-153224-931597';
		/**Hotel Itinerary details start**/

		$room_count = $hotel_data['no_of_room'][0];
		//debug($room_count);die();
		for($i = 0;$i < $room_count; $i++){

			$dep_date = date('Y-m-d',strtotime($hotel_data['dep_date_onward'][0]));
			$arr_date = date('Y-m-d',strtotime($hotel_data['arr_date_onward'][0]));
			$__dep_date_time = $dep_date.' '.$hotel_data['dep_time_onward'][0];
			$__arr_date_time = $arr_date.' '.$hotel_data['arr_time_onward'][0];

			$itinerary_details['app_reference'] = $app_reference;
			$itinerary_details['location'] = $hotel_data['location'][0];
			$itinerary_details['check_in'] = $__dep_date_time;
			$itinerary_details['check_out'] = $__arr_date_time;
			$itinerary_details['room_type_name'] = $hotel_data['room_type'][0];
			$itinerary_details['bed_type_code'] = 2;
			$itinerary_details['status'] = $hotel_data['status'];
			$itinerary_details['smoking_preference'] = 'No Preference';
			$itinerary_details['total_fare'] = ($hotel_data['agent_buying_price']/$room_count);
			$itinerary_details['admin_markup'] = ($hotel_data['admin_markup']/$room_count);
			$itinerary_details['agent_markup'] = 0;
			$itinerary_details['currency'] = 'INR';
			$itinerary_details['attributes'] = '';
			$itinerary_details['RoomPrice'] = 0;
			$itinerary_details['Tax'] = ($hotel_data['api_total_tax']/$room_count);
			$itinerary_details['ExtraGuestCharge'] = 0;
			$itinerary_details['ChildCharge'] = 0;
			$itinerary_details['OtherCharges'] = 0;
			$itinerary_details['Discount'] = 0;
			$itinerary_details['ServiceTax'] = 0;
			$itinerary_details['AgentCommission'] = 0;
			$itinerary_details['AgentMarkUp'] = 0;
			$itinerary_details['TDS'] = 0;
			$itinerary_details['gst'] = 0;

			$hotel_iter_id = $this->custom_db->insert_record('hotel_booking_itinerary_details',$itinerary_details);
		}

		/**Hotel Itinerary details END**/

		/**Hotel Customer details START**/
		$pax_count = $hotel_data['adult_count']+$hotel_data['child_count']+$hotel_data['infant_count'];
		$arr = array();
		for($i = 0;$i < $pax_count;$i++){

			$customer_details['app_reference'] = $app_reference;
			$customer_details['title'] = $hotel_data['pax_title'][$i];
			$customer_details['first_name'] = $hotel_data['pax_first_name'][$i];
			$customer_details['middle_name'] = '';
			$customer_details['last_name'] = $hotel_data['pax_last_name'][$i];
			$customer_details['phone'] = $hotel_data ['passenger_phone'];
			$customer_details['email'] = $hotel_data ['passenger_email'];
			$customer_details['pax_type'] = $hotel_data['pax_type'][$i];
			$customer_details['age'] = $hotel_data['pax_age'][$i];
			$customer_details['date_of_birth'] = '1992-10-13';
			$customer_details['passenger_nationality'] = 'India';
			$customer_details['passport_number'] = '3281905686';
			$customer_details['passport_issuing_country'] = 'India';
			$customer_details['passport_expiry_date'] = '2024-10-15';
			$customer_details['status'] = $hotel_data['status'];
			$customer_details['attributes'] = '';
			
			
			$hotel_customer_id = $this->custom_db->insert_record('hotel_booking_pax_details',$customer_details);
		}

		// Update Agent Balance
		if(($hotel_data['agent_id'] > 0) && ($hotel_data['status'] == 'BOOKING_CONFIRMED')){

			$agent_buying_price = $hotel_data['agent_buying_price'];
			$api_total_tax = $hotel_data['api_total_tax'];
			$api_total_basic_fare = $hotel_data['api_total_basic_fare'];
			$admin_markup = $hotel_data['admin_markup'];
			$agent_markup = 0;

			$agent_buying = '-'.$agent_buying_price;
			$this->domain_management_model->update_agent_balance($agent_buying,$hotel_data['agent_id']);

			$transaction_type = 'Hotel';
			$fare = ($api_total_tax+$api_total_basic_fare);
			$domain_markup = $admin_markup;
			$level_one_markup = $agent_markup;
			$remarks = 'Offline Hotel Transaction was Successfully done';
			$convinence = 0;
			$discount = 0;
			$currency = 'INR';
			$currency_conversion_rate = 1;
			$transaction_owner_id = $hotel_data['agent_id'];
			
			$this->domain_management_model->save_transaction_details($transaction_type, $app_reference, $fare, $domain_markup, $level_one_markup, $remarks, $convinence, $discount, $currency,$currency_conversion_rate,$transaction_owner_id);

		}
		

		$this->load->model('domain_management_model');
		$this->domain_management_model->create_track_log ( $app_reference, 'Offline Booking - Hotel' );

		$return_response['app_reference'] = $app_reference;
		$return_response['status'] = $hotel_data['status'];
		$return_response['booking_source'] = $hotel_data['suplier_id'];
		return $return_response;
	}

	
	function offline_hotel_report($condition=array(), $count=false, $offset=0, $limit=100000000000, $b_status='')
	{
		$condition = $this->custom_db->get_custom_condition($condition);
		if(isset($condition) == true)
		{
			$offset = 0;
		}else{
			$offset = $offset;
		}
		if($b_status == "confirmed_cancelled"){
			$condition = 'AND (BD.status = '.$this->db->escape('BOOKING_CONFIRMED').' OR BD.status = '.$this->db->escape('BOOKING_CANCELLED').')';
		}

		//BT, CD, ID
		if ($count) {
			$query = 'select count(distinct(BD.app_reference)) as total_records 
					from hotel_booking_details BD
					join hotel_booking_itinerary_details AS HBID on BD.app_reference=HBID.app_reference
					join payment_option_list AS POL on BD.payment_mode=POL.payment_category_code 
					left join user as U on BD.created_by_id = U.user_id  where U.user_type='.ADMIN.' AND BD.domain_origin='.get_domain_auth_id().' '.$condition;
			$data = $this->db->query($query)->row_array();
			return $data['total_records'];
		} else {
			$this->load->library('booking_data_formatter');
			$response['status'] = SUCCESS_STATUS;
			$response['data'] = array();
			$booking_itinerary_details	= array();
			$booking_customer_details	= array();
			$cancellation_details = array();
			$bd_query = 'select BD.* ,U.agency_name, U.uuid AS agency_id, U.first_name,U.last_name from hotel_booking_details AS BD
					     left join user U on BD.created_by_id =U.user_id 					     
						 WHERE BD.domain_origin='.get_domain_auth_id().' '.$condition.'
						 order by BD.created_datetime desc, BD.origin desc limit '.$offset.', '.$limit;
			$booking_details = $this->db->query($bd_query)->result_array();
			$app_reference_ids = $this->booking_data_formatter->implode_app_reference_ids($booking_details);
			if(empty($app_reference_ids) == false) {
				$id_query = 'select * from hotel_booking_itinerary_details AS ID 
							WHERE ID.app_reference IN ('.$app_reference_ids.')';
				$cd_query = 'select * from hotel_booking_pax_details AS CD 
							WHERE CD.app_reference IN ('.$app_reference_ids.')';
				$cancellation_details_query = 'select * from hotel_cancellation_details AS HCD 
							WHERE HCD.app_reference IN ('.$app_reference_ids.')';
				$booking_itinerary_details	= $this->db->query($id_query)->result_array();
				$booking_customer_details	= $this->db->query($cd_query)->result_array();
				$cancellation_details	= $this->db->query($cancellation_details_query)->result_array();
			}
			$response['data']['booking_details']			= $booking_details;
			$response['data']['booking_itinerary_details']	= $booking_itinerary_details;
			$response['data']['booking_customer_details']	= $booking_customer_details;
			$response['data']['cancellation_details']	= $cancellation_details;
			return $response;
		}
	}
}
