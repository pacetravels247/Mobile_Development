<?php
require_once 'transaction.php';
/**
 * Library which has generic functions to get data
 *
 * @package    Provab Application
 * @subpackage Car Model
 * @author     Anitha.G J<anitha.g.provab@gmail.com>
 * @version    V1
 */
Class Car_Model extends Transaction
{
	/*
	 *
	 * Get Airport List
	 *
	 */

	function get_airport_list($search_chars)
	{
		$raw_search_chars = $this->db->escape($search_chars);
		$r_search_chars = $this->db->escape($search_chars.'%');
		$search_chars = $this->db->escape('%'.$search_chars.'%');
		$query = 'Select * from Car_Airport where Airport_Name_EN like '.$search_chars.'
		OR Airport_IATA like '.$search_chars.' OR Country_ISO like '.$search_chars.'
		ORDER BY top_destination DESC,
		CASE
			WHEN	Airport_IATA	LIKE	'.$raw_search_chars.'	THEN 1
			WHEN	Airport_Name_EN	LIKE	'.$raw_search_chars.'	THEN 2
			WHEN	Country_ISO		LIKE	'.$raw_search_chars.'	THEN 3

			WHEN	Airport_IATA	LIKE	'.$r_search_chars.'	THEN 4
			WHEN	Airport_Name_EN	LIKE	'.$r_search_chars.'	THEN 5
			WHEN	Country_ISO		LIKE	'.$r_search_chars.'	THEN 6

			WHEN	Airport_IATA	LIKE	'.$search_chars.'	THEN 7
			WHEN	Airport_Name_EN	LIKE	'.$search_chars.'	THEN 8
			WHEN	Country_ISO		LIKE	'.$search_chars.'	THEN 9
			ELSE 10 END
		LIMIT 0, 20';
		// echo $query;exit;
		return $this->db->query($query);
	}
	/**
	 * Get city list
	 * 
	 */
	function get_city_list($search_chars)
	{
		$search_chars = $this->db->escape(''.$search_chars.'%');
		$query = 'Select Country_ISO, origin, Country_Name_EN, City_ID, City_Name_EN, City_IATA as Airport_IATA from Car_City where City_Name_EN like '.$search_chars.'
		OR Country_Name_EN like '.$search_chars.' AND City_IATA !="" LIMIT 0, 10';
		
		return $this->db->query($query);
		//return $data;
	}
	/**
	 * Save search data for future use - Analytics
	 * @param array $params
	 */
	function save_search_data($search_data, $type)
	{	
		// debug($search_data); exit;
		$data['domain_origin'] = get_domain_auth_id();
		$data['search_type'] = $type;
		$data['created_by_id'] = intval(@$this->entity_user_id);
		$data['created_datetime'] = date('Y-m-d H:i:s');
		$data['from_location'] = $search_data['car_from'];
		$data['from_loc_id'] = $search_data['from_loc_id'];
		$data['from_loc_code'] = $search_data['car_from_loc_code'];
		$data['to_location'] = $search_data['car_to'];
		$data['to_loc_id'] = $search_data['to_loc_id'];
		$data['to_loc_code'] = $search_data['car_to_loc_code'];
		$data['driver_age'] = $search_data['driver_age'];
		$depature=date('d-m-Y',strtotime($search_data['depature'])).' '.$search_data['depature_time'];
		$return=date('d-m-Y',strtotime($search_data['return'])).' '.$search_data['return_time'];

		$data['depature'] = date('Y-m-d H:i',strtotime($depature));
		$data['return'] = date('Y-m-d H:i',strtotime($return));
		
		$this->custom_db->insert_record('search_car_history', $data);

	}
	/**
	 * get search data and validate it
	 */
	function get_safe_search_data($search_id)
	{
		$search_data = $this->get_search_data($search_id);
		//debug($search_data);exit;
		$success = true;
		$clean_search = '';
		if ($search_data != false) {
			//validate
			$temp_search_data = json_decode($search_data['search_data'], true);
			$clean_search = $this->clean_search_data($temp_search_data);
			$success = $clean_search['status'];
			$clean_search = $clean_search['data'];
			return array('status' => $success, 'data' => $clean_search);
		}
	}
	/**
	 * get search data without doing any validation
	 * @param $search_id
	 */
	function get_search_data($search_id)
	{
		$search_data = $this->custom_db->single_table_records('search_history', '*', array('search_type' => META_CAR_COURSE, 'origin' => $search_id));
		if ($search_data['status'] == true) {
			return $search_data['data'][0];
		} else {
			return false;
		}
	}
	/**
	 * Clean up search data
	 */
	function clean_search_data($temp_search_data)
	{  
		$success = true;
		return array('data' => $temp_search_data, 'status' => $success);
	}
	 /**
	 * get all the booking source which are active for current domain
	 */
	function car_booking_source()
	{
		$query = 'select BS.source_id, BS.origin from meta_course_list AS MCL, booking_source AS BS, activity_source_map AS ASM WHERE
		MCL.origin=ASM.meta_course_list_fk and ASM.booking_source_fk=BS.origin and MCL.course_id='.$this->db->escape(META_CAR_COURSE).'
		and BS.booking_engine_status='.ACTIVE.' AND MCL.status='.ACTIVE.' AND ASM.status="active"';
		return $this->db->query($query)->result_array();
	}
	/**
	 * Get Vehicle Category list
	 * 
	 */
	function vehiclecategory(){
		$query = 'Select vehiclecategory_id, vehiclecategory_name from car_vehiclecategory';
		return $this->db->query($query)->result_array();	
		
	}
	/**
	 * Get Vehicle Size list
	 * 
	 */
	function vehiclesize(){
		$query = 'Select vehiclesize_id, vehiclesize_name from car_vehiclesize';
		return $this->db->query($query)->result_array();	
	}
	 /* save car booking data */
    function save_booking_details ( $domain_origin, $status, $app_reference, $booking_source, $currency, $phone_number, 
        $email, $payment_mode, $created_by_id,$currency_conversion_rate, $total_fare, $booking_id, 
        $booking_reference, $supplier_identifier, $car_name, $car_supplier_name, $car_model, $car_from_date,
        $car_to_date, $pickup_time, $drop_time, $car_pickup_lcation, $car_drop_location, $car_drop_address, $car_pickup_address, $final_cancel_date, $transfer_type,$oneway_fee){
        $data['domain_origin'] = $domain_origin;
        $data['status'] = $status;
        $data['app_reference'] = $app_reference;
        $data['booking_source'] = $booking_source;
        $data['booking_id'] = $booking_id;
        $data['booking_reference'] = $booking_reference;
        $data['total_fare'] = $total_fare;
        $data['currency'] = $currency;
        $data['car_name'] = $car_name;
        $data['car_supplier_name'] = $car_supplier_name;
        $data['car_model'] = $car_model;
        $data['phone_number'] = $phone_number;
        $data['email'] = $email;
        $data['car_to_date'] = $car_to_date;
        $data['car_from_date'] = $car_from_date;
        $data['payment_mode'] = $payment_mode;
        $data['supplier_identifier'] = $supplier_identifier;
       	$data['pickup_time'] = $pickup_time;
        $data['drop_time'] = $drop_time;
        $data['car_pickup_lcation'] = $car_pickup_lcation;
        $data['car_drop_location'] = $car_drop_location;
        $data['car_drop_address'] = $car_drop_address;
        $data['car_pickup_address'] = $car_pickup_address;
      	$data['final_cancel_date'] = $final_cancel_date;
        $data['transfer_type'] = $transfer_type;
        $data['oneway_fee'] = $oneway_fee;
        $data['created_by_id'] = $created_by_id;
        $data['created_datetime'] = date('Y-m-d H:i:s');
        $data['currency_conversion_rate'] = $currency_conversion_rate;
      	// debug($data);exit;
        $status = $this->custom_db->insert_record('car_booking_details', $data);
        return $status;
      }
      function save_booking_itinerary_details($app_reference, $car_from_date, $car_to_date, $pickup_time, $drop_time, $car_pickup_location,
                             $car_drop_location, $car_pickup_address, $car_drop_address, $car_name, $pricture_url, $priced_equip, $priced_coverage, $cancellation_poicy, $attributes1, $total_fare, $admin_markup, $agent_markup, $status){

        $data['status'] = $status;
        $data['app_reference'] = $app_reference;
        $data['car_from_date'] = $car_from_date;
        $data['car_to_date'] = $car_to_date;
        $data['pickup_time'] = $pickup_time;
        $data['drop_time'] = $drop_time;
        $data['car_pickup_loc'] = $car_pickup_location;
        $data['car_drop_loc'] = $car_drop_location;
        $data['car_pickup_add'] = $car_pickup_address;
        $data['car_drop_add'] = $car_drop_address;
        $data['car_name'] = $car_name;
        $data['pricture_url'] = $pricture_url;
        $data['priced_equip'] = $priced_equip;
        $data['priced_coverage'] = $priced_coverage;
        $data['cancellation_poicy'] = $cancellation_poicy;
        $data['admin_markup'] = $admin_markup;
        $data['agent_markup'] = $agent_markup;
        $data['total_fare'] = $total_fare; 
        $data['attributes'] = $attributes1;
      
        $status = $this->custom_db->insert_record('car_booking_itinerary_details', $data);
        return $status;

      }
      function save_booking_pax_details($app_reference, $title, $first_name, $last_name, $phone, $email, $dob, $country_code, $country_name, $city, $pincode, $adress1, $adress2, $status){

        $data['status'] = $status;
        $data['app_reference'] = $app_reference;
        $data['title'] = $title;
        $data['first_name'] = $first_name;
        $data['last_name'] = $last_name;
        $data['phone'] = $phone;
        $data['date_of_birth'] = $dob;
        $data['country_code'] = $country_code;
        $data['country_name'] = $country_name;
        $data['city'] = $city;
        $data['pincode'] = $pincode;
        $data['adress1'] = $adress1;
        $data['adress2'] = $adress2;
          // debug($data);exit;
        $status = $this->custom_db->insert_record('car_booking_pax_details', $data);
        return $status;
      }
      function get_booking_details($app_reference, $booking_source, $booking_status='')
	{
		
			$this->load->library('booking_data_formatter');
			$response['status'] = SUCCESS_STATUS;
			$response['data'] = array();
			$booking_itinerary_details	= array();
			$booking_customer_details	= array();
			$cancellation_details = array();
			
			
			$bd_query = 'select * from car_booking_details as BD where BD.app_reference="'.trim($app_reference).'"';
			if (empty($booking_source) == false) {
				$bd_query .= '	AND BD.booking_source = '.$this->db->escape($booking_source);
			}
			if (empty($booking_status) == false) {
				$bd_query .= ' AND BD.status = '.$this->db->escape($booking_status);
			}
			// echo $bd_query;exit;
			$booking_details = $this->db->query($bd_query)->result_array();
			// debug($booking_details); die;
			$app_reference_ids = $this->booking_data_formatter->implode_app_reference_ids($booking_details);
			if(empty($app_reference_ids) == false) {
				$id_query = 'select * from car_booking_itinerary_details AS ID 
							WHERE ID.app_reference IN ('.$app_reference_ids.')';
				$cd_query = 'select * from car_booking_pax_details AS CD 
							WHERE CD.app_reference IN ('.$app_reference_ids.')';
				$ex_query = 'select * from car_booking_extra_details AS CD 
							WHERE CD.app_reference IN ('.$app_reference_ids.')';
				$cancellation_details_query = 'select * from car_cancellation_details AS HCD 
							WHERE HCD.app_reference IN ('.$app_reference_ids.')';
				$booking_itinerary_details	= $this->db->query($id_query)->result_array();

				$booking_customer_details	= $this->db->query($cd_query)->result_array();

				$booking_extra_details	= $this->db->query($ex_query)->result_array();

				$cancellation_details	= $this->db->query($cancellation_details_query)->result_array();
				
			}
			
			$response['data']['booking_details']			= $booking_details;
			$response['data']['booking_itinerary_details']	= $booking_itinerary_details;
			$response['data']['booking_pax_details']	= $booking_customer_details;
			$response['data']['booking_extra_details']	= $booking_extra_details;
			$response['data']['cancellation_details']	= $cancellation_details;
			// debug($response);exit;
			return $response;
	}
	 function save_booking_extra_details($extra_service_details){
        foreach($extra_service_details as $service){
            $data['app_reference'] = $service['app_reference'];
            $data['amount'] = $service['amount'];
            $data['description'] = $service['description'];
            $data['equiptype'] = $service['equiptype'];
            $data['qunatity'] = $service['qunatity'];
            $status = $this->custom_db->insert_record('car_booking_extra_details', $data);
        }
        return $status;
      }
    /**
	 * return booking list
	 */
	function booking($condition=array(), $count=false, $offset=0, $limit=100000000000)
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
					from car_booking_details BD
					join car_booking_itinerary_details AS HBID on BD.app_reference=HBID.app_reference
					join payment_option_list AS POL on BD.payment_mode=POL.payment_category_code
					where BD.domain_origin='.get_domain_auth_id().' and BD.created_by_id ='.$GLOBALS['CI']->entity_user_id.''.$condition;

			$data = $this->db->query($query)->row_array();
			return $data['total_records'];
		} else {
			$this->load->library('booking_data_formatter');
			$response['status'] = SUCCESS_STATUS;
			$response['data'] = array();
			$booking_itinerary_details	= array();
			$booking_customer_details	= array();
			$booking_extra_details = array();
			$cancellation_details = array();
			$bd_query = 'select * from car_booking_details AS BD 
						WHERE BD.domain_origin='.get_domain_auth_id().' and BD.created_by_id ='.$GLOBALS['CI']->entity_user_id.''.$condition.'
						order by BD.origin desc limit '.$offset.', '.$limit;

			$booking_details = $this->db->query($bd_query)->result_array();
			$app_reference_ids = $this->booking_data_formatter->implode_app_reference_ids($booking_details);
			if(empty($app_reference_ids) == false) {
				$id_query = 'select * from car_booking_itinerary_details AS ID 
							WHERE ID.app_reference IN ('.$app_reference_ids.')';
				$cd_query = 'select * from  car_booking_pax_details AS CD 
							WHERE CD.app_reference IN ('.$app_reference_ids.')';
				$ex_query = 'select * from car_booking_extra_details AS CD 
							WHERE CD.app_reference IN ('.$app_reference_ids.')';
				$cancellation_details_query = 'select * from car_cancellation_details AS HCD 
							WHERE HCD.app_reference IN ('.$app_reference_ids.')';
				$booking_itinerary_details	= $this->db->query($id_query)->result_array();
				$booking_customer_details	= $this->db->query($cd_query)->result_array();
				$booking_extra_details	= $this->db->query($ex_query)->result_array();
				$cancellation_details	= $this->db->query($cancellation_details_query)->result_array();
			}
			$response['data']['booking_details']			= $booking_details;
			$response['data']['booking_itinerary_details']	= $booking_itinerary_details;
			$response['data']['booking_pax_details']	= $booking_customer_details;
			$response['data']['booking_extra_details']	= $booking_extra_details;
			$response['data']['cancellation_details']	= $cancellation_details;
			return $response;
		}

	}
	/**
	 * Anitha G
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
		$this->custom_db->update_record('car_booking_details', array('status' => $booking_status), array('app_reference' => $AppReference));//later
		//3.Update Itinerary Status
		$this->custom_db->update_record('car_booking_itinerary_details', array('status' => $booking_status), array('app_reference' => $AppReference));//later
	}
	/**
	 * Add Cancellation details
	 * @param unknown_type $AppReference
	 * @param unknown_type $cancellation_details
	 */
	public function update_cancellation_refund_details($AppReference, $cancellation_details)
	{
		$car_cancellation_details = array();
		$car_cancellation_details['app_reference'] = 				$AppReference;
		$car_cancellation_details['ChangeRequestId'] = 			$cancellation_details['ChangeRequestId'];
		$car_cancellation_details['ChangeRequestStatus'] = 		$cancellation_details['ChangeRequestStatus'];
		$car_cancellation_details['status_description'] = 		$cancellation_details['StatusDescription'];
		$car_cancellation_details['API_RefundedAmount'] = 		@$cancellation_details['RefundedAmount'];
		$car_cancellation_details['API_CancellationCharge'] = 	@$cancellation_details['CancellationCharge'];
		if($cancellation_details['ChangeRequestStatus'] == 3){
			$car_cancellation_details['cancellation_processed_on'] =	date('Y-m-d H:i:s');
		}
		$cancel_details_exists = $this->custom_db->single_table_records('car_cancellation_details', '*', array('app_reference' => $AppReference));
		if($cancel_details_exists['status'] == true) {
			//Update the Data
			unset($car_cancellation_details['app_reference']);
			$this->custom_db->update_record('car_cancellation_details', $car_cancellation_details, array('app_reference' => $AppReference));
		} else {
			//Insert Data
			$car_cancellation_details['created_by_id'] = 				(int)@$this->entity_user_id;
			$car_cancellation_details['created_datetime'] = 			date('Y-m-d H:i:s');
			$data['cancellation_requested_on'] = date('Y-m-d H:i:s');
			$this->custom_db->insert_record('car_cancellation_details',$car_cancellation_details);
		}
	}
}