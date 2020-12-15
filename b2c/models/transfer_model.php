<?php

/**
 * Library which has generic functions to get data
 * @package    Provab Application
 * @subpackage Transfer Model
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V1
 */
Class Transfer_Hb_Model extends CI_Model {

    /**
     * get search data and validate it
     */
    function get_safe_search_data($search_id) {
        $search_data = $this->get_search_data($search_id);

        $success = true;
        $clean_search = '';
        if ($search_data != false) {
            //validate
            $temp_search_data = json_decode($search_data['search_data'], true);
            //debug($temp_search_data);exit;
            $clean_search = $this->clean_search_data($temp_search_data);
            $success = $clean_search['status'];
            $clean_search = $clean_search['data'];
        } else {
            $success = false;
        }
        return array('status' => $success,
            'data' => $clean_search
        );
    }

    /**
     * Badri
     * get service tax and TDs
     * 
     */
    function get_tax() {
        $response ['data'] = array();

        $q = $this->db->query('select tds,service_tax from commission_master where module_type="transfer"')->result_array();

        $response ['data']['tds'] = $q[0]['tds'];
        $response ['data']['service_tax'] = $q[0]['service_tax'];
        return $response;
    }

    function get_cfee() {

        $result = array();
        $qry = "select * from convenience_fees where module = 'transfer'";
        $query = $this->db->query($qry);

        foreach ($query->result_array() as $row) {
            $result[] = $row;
        }

        return $result;
    }

    /**
     * get search data without doing any validation
     * @param $search_id
     */
    function get_search_data($search_id) {
        if (empty($this->master_search_data)) {
            $search_data = $this->custom_db->single_table_records('search_history', '*', array('origin' => $search_id, 'search_type' => META_TRANSFER_COURSE));
            if ($search_data['status'] == true) {
                $this->master_search_data = $search_data['data'][0];
            } else {
                return false;
            }
        }
        return $this->master_search_data;
    }

    /**
     * hotel address
     * @param $hotel_id
     */
    function get_searched_hotel_address($hotel_id) {
        $query = 'Select hotel_name, hotel_city, hotel_code,address,postal_code, origin from hb_hotel_details where hotel_code=' . $hotel_id;

        return $this->db->query($query)->result_array();
    }

    /**
     * Save search data for future use - Analytics
     * @param array $params
     */
    function save_search_data($search_data, $type) {
        $data['domain_origin'] = get_domain_auth_id();
        $data['search_type'] = $type;
        $data['created_by_id'] = intval(@$this->entity_user_id);
        $data['created_datetime'] = date('Y-m-d H:i:s');
        $data['from_terminal'] = $search_data['from_transfer_type'];
        $data['to_terminal'] = $search_data['to_transfer_type'];
        $data['from_code'] = $search_data['from_loc_id'];
        $data['to_code'] = $search_data['to_loc_id'];
        $data['from_location_name'] = $search_data['transfer_from'];
        $data['to_location_name'] = $search_data['transfer_to'];
        $data['adult'] = $search_data['adult'];
        $data['child'] = $search_data['child'];
        $data['departure_date'] = date('Y-m-d', strtotime($search_data['depature']));

        if (isset($search_data['adult_ages']) && valid_array($search_data['adult_ages'])) {
            $data['adult_ages'] = json_encode($search_data['adult_ages']);
        }
        if (isset($search_data['child_ages']) && valid_array($search_data['child_ages'])) {
            $data['child_ages'] = json_encode($search_data['child_ages']);
        }
        if (isset($search_data['return'])) {
            $data['return_date'] = date('Y-m-d', strtotime($search_data['return']));
        }
        $data['trip_type'] = $search_data['transfer_type'];

        $this->custom_db->insert_record('search_transfer_history', $data);
    }

    /**
     * get all the booking source which are active for current domain
     */
    function active_booking_source() {
        $query = 'select BS.source_id, BS.origin from meta_course_list AS MCL, booking_source AS BS, activity_source_map AS ASM WHERE
		MCL.origin=ASM.meta_course_list_fk and ASM.booking_source_fk=BS.origin and MCL.course_id=' . $this->db->escape(META_TRANSFER_COURSE) . '
		and BS.booking_engine_status=' . ACTIVE . ' AND MCL.status=' . ACTIVE . ' AND ASM.status="active"';
        
        return $this->db->query($query)->result_array();
    }

    function top_transfer_location($Limit) {
//		$filter = array('status'=>1);
        $filter = array();
        $result = $this->custom_db->single_table_records('transfer_location_details', '*', $filter, 0, $Limit, array('origin' => 'desc',));
        return @$result['data'];
    }

    /**
     * Clean up search data
     */
    function clean_search_data($temp_search_data) {
        $success = true;
        $clean_search['from'] = $temp_search_data['transfer_from'];
        $clean_search['to'] = $temp_search_data['transfer_to'];

        if ((strtotime($temp_search_data['depature']) > time()) || date('Y-m-d', strtotime($temp_search_data['depature'])) == date('Y-m-d')) {
            $clean_search['from_date'] = $temp_search_data['depature'];
        } else {
            $success = false;
        }
        if (isset($temp_search_data['return']) && strtotime($temp_search_data['return']) > time()) {
            $clean_search['to_date'] = $temp_search_data['return'];
        }

        $clean_search['from_code'] = $temp_search_data['from_loc_id'];
        $clean_search['to_code'] = $temp_search_data['to_loc_id'];
        $clean_search['from_transfer_type'] = $temp_search_data['from_transfer_type'];
        $clean_search['to_transfer_type'] = $temp_search_data['to_transfer_type'];
        $clean_search['adult'] = $temp_search_data['adult'];
        $clean_search['child'] = $temp_search_data['child'];
        $depature_date_time = explode(" ", $temp_search_data['depature']);
        //debug($depature_date_time);exit;
        $clean_search['depature_time_flight'] = $depature_date_time[1];
        if (isset($depature_date_time[0]))
            $clean_search['depature'] = $depature_date_time[0];
        if (isset($depature_date_time[1]))
            $clean_search['depature_time'] = preg_replace('/[^A-Za-z0-9\-]/', '', $depature_date_time[1]);

        if (isset($temp_search_data['adult_ages']) && valid_array($temp_search_data['adult_ages'])) {
            $clean_search['adult_ages'] = $temp_search_data['adult_ages'];
        }

        if (isset($temp_search_data['child_ages']) && valid_array($temp_search_data['child_ages'])) {
            $clean_search['child_ages'] = $temp_search_data['child_ages'];
        }

        if (isset($temp_search_data['return'])) {
            $return_date_time = explode(" ", $temp_search_data['return']);
            $clean_search['return_time_flight'] = $return_date_time[1];
            if (isset($return_date_time[0]))
                $clean_search['return'] = $return_date_time[0];
            if (isset($return_date_time[1]))
                $clean_search['return_time'] = preg_replace('/[^A-Za-z0-9\-]/', '', $return_date_time[1]);
        }
        $clean_search['trip_type'] = $temp_search_data['transfer_type']; //debug($clean_search); exit;
        return array('data' => $clean_search, 'status' => $success);
    }

    /**
     * Get airport list
     * 
     */
    function get_airport_list($search_chars) {
        $raw_search_chars = $this->db->escape($search_chars);
        $r_search_chars = $this->db->escape($search_chars . '%');
        $search_chars = $this->db->escape('%' . $search_chars . '%');

        $query = 'Select * from flight_airport_list where airport_city like ' . $search_chars . '
		OR airport_code like ' . $search_chars . ' OR country like ' . $search_chars . '
		ORDER BY top_destination DESC,
		CASE
			WHEN	airport_code	LIKE	' . $raw_search_chars . '	THEN 1
			WHEN	airport_city	LIKE	' . $raw_search_chars . '	THEN 2
			WHEN	country			LIKE	' . $raw_search_chars . '	THEN 3

			WHEN	airport_code	LIKE	' . $r_search_chars . '	THEN 4
			WHEN	airport_city	LIKE	' . $r_search_chars . '	THEN 5
			WHEN	country			LIKE	' . $r_search_chars . '	THEN 6

			WHEN	airport_code	LIKE	' . $search_chars . '	THEN 7
			WHEN	airport_city	LIKE	' . $search_chars . '	THEN 8
			WHEN	country			LIKE	' . $search_chars . '	THEN 9
			ELSE 10 END
		LIMIT 0, 20';
        //debug($query);exit;
        return $this->db->query($query);
    }

    /**
     * Get hotel list
     * 
     */
    function get_hotels_list($search_chars) {
        $search_chars = $this->db->escape('%' . $search_chars . '%');
        $query = 'Select hotel_name, hotel_city, hotel_code, origin from hb_hotel_details where hotel_city like ' . $search_chars . '
		OR hotel_name like ' . $search_chars . ' OR hotel_code like ' . $search_chars . ' LIMIT 0, 20';

        return $this->db->query($query);
        //return $data;
    }

    /**
     * Get hotel list
     * 
     */
    function get_airline_list($search_chars) {
        $search_chars = $this->db->escape('%' . $search_chars . '%');
        $query = 'Select code, name from airline_list where name like ' . $search_chars . ' LIMIT 0, 20';

        return $this->db->query($query);
        //return $data;
    }

    /**
     * Return Booking Details based on the app_reference passed
     * @param $app_reference
     * @param $booking_source
     * @param $booking_status
     */
    function get_booking_details($app_reference, $booking_source, $booking_status = '') {
        $response['status'] = FAILURE_STATUS;
        $response['data'] = array();
        $booking_cancellation_details = array();
        //$transfer_query = 'select * from hb_transfer_booking_details BD WHERE BD.app_reference like '.$this->db->escape($app_reference);

        if (($booking_status == 'CONFIRMED') || ($booking_status == 'BOOKING_CONFIRMED')) {
            $booking_status = 'BOOKING_CONFIRMED';
        }

        $td_query = 'select * from hb_transfer_booking_transction_details TD WHERE TD.app_reference like ' . $this->db->escape($app_reference);
        if (empty($booking_source) == false) {
            $td_query .= '	AND TD.booking_source = ' . $this->db->escape($booking_source);
        }
        if (empty($booking_status) == false) {
            $td_query .= ' AND TD.status = ' . $this->db->escape($booking_status);
        }

        //$In_Out_id = "select booking_reference,transfer_type from hb_transfer_service_details WHERE app_reference=".$this->db->escape($app_reference);
        //debug($booking_id[0]['booking_reference']); exit;	

        $id_query = 'select * from hb_transfer_contact_details CD WHERE CD.app_reference=' . $this->db->escape($app_reference);
        $bd_query = 'select * from hb_transfer_booking_details BD WHERE BD.app_reference like ' . $this->db->escape($app_reference);
        $cd_query = 'select * from hb_transfer_paxes_details PD WHERE PD.app_reference=' . $this->db->escape($app_reference);
        $sd_query = 'select * from hb_transfer_service_details SD WHERE SD.app_reference=' . $this->db->escape($app_reference);

        //$td_query = 'select * from hb_transfer_booking_transction_details TD WHERE TD.app_reference='.$this->db->escape($app_reference);	
        $tcd_query = 'select TCD.* from hb_transfer_cancellation_policy TCD WHERE TCD.app_reference=' . $this->db->escape($app_reference);
        // debug($tcd_query);exit;
        $response['data']['booking_transction_details'] = $this->db->query($td_query)->result_array();
        $response['data']['booking_service_details'] = $this->db->query($sd_query)->result_array();
        $response['data']['booking_transfer_details'] = $this->db->query($bd_query)->result_array();
        $response['data']['booking_contact_details'] = $this->db->query($id_query)->result_array();
        $response['data']['booking_customer_details'] = $this->db->query($cd_query)->result_array();
        $booking_cancellation_details = $this->db->query($tcd_query)->result_array();
        if (isset($booking_cancellation_details) && !empty($booking_cancellation_details))
            $response['data']['booking_cancellation_details'] = $this->db->query($tcd_query)->result_array();

        if (valid_array($response['data']['booking_transction_details']) == true and valid_array($response['data']['booking_contact_details']) == true and valid_array($response['data']['booking_customer_details']) == true and valid_array($response['data']['booking_service_details']) == true) {
            $response['status'] = SUCCESS_STATUS;
        }
        //debug($response); exit;

        return $response;
    }

    /**
     * Save payment data for future use - Transfers
     * @param array $params
     */
    function save_payment_details($params, $book_id) {
        $request_params = array();
        $this->db->from('payment_gateway_details')
                ->where('app_reference', $book_id);
        $rs = $this->db->get();
        if ($rs->num_rows() == '0') {
            if (isset($params) && valid_array($params)) {
                $request_params['name'] = $params['first_name'] . " " . $params['last_name'];
                $request_params['billing_email'] = $params['billing_email'];
                $request_params['passenger_contact'] = $params['passenger_contact'];
                $request_params['book_id'] = $params['book_id'];
                $request_params['booking_source'] = $params['booking_source'];
                $request_params['creation_user'] = $params['creation_user'];
                $request_params['SPUI'] = $params['SPUI'];
                $request_params['adult_count'] = $params['adult_count'];
                $request_params['child_count'] = $params['child_count'];
                $request_params['book_origin_id'] = $params['book_origin'];
                $request_params['currency_code'] = $params['currency_code'];
                foreach ($params['transfer_type'] as $transfer_k => $transfer_v) {
                    $request_params[$transfer_v]['agency_code'] = $params['agency_code'][$transfer_k];
                    $request_params[$transfer_v]['transfer_amount'] = $params['total_amount'][$transfer_k];
                    $request_params[$transfer_v]['pickup_location_name'] = $params['pickup_location_name'][$transfer_k];
                    $request_params[$transfer_v]['destination_location_name'] = $params['destination_location_name'][$transfer_k];
                    $request_params[$transfer_v]['vehicle_type'] = $params['vehicle_type'][$transfer_k];
                    $request_params[$transfer_v]['from_date'] = $params['from_date'][$transfer_k];
                    $request_params[$transfer_v]['from_time'] = $params['from_time'][$transfer_k];
                }
            }
            $data['domain_origin'] = get_domain_auth_id();
            $data['app_reference'] = $book_id;
            $data['status'] = 'pending';
            $data['amount'] = $params['transfers_total_amount'];
            $data['currency'] = $params['currency_code'];
            $data['request_params'] = json_encode($request_params);
            $data['created_datetime'] = date("Y-m-d H:i:sa");
            if ($this->db->insert('payment_gateway_details', $data)) {
                return true;   // to the controller
            }
        }
    }

    /**
     * Update payment status for future use - Transfers
     * @param array $params
     */
    function update_payment_details($app_reference_id, $payment_params) {
        $data = array();
        $data['response_params'] = json_encode($payment_params);
        $data['status'] = 'accepted';
        $this->db->update('payment_gateway_details', $data, array('app_reference' => $app_reference_id));
        //return true;
    }

    /**
     * return booking list
     */
    function booking($condition = array(), $count = false, $offset = 0, $limit = 100000000000) {
        $condition = $this->custom_db->get_custom_condition($condition); //debug($condition); exit;
        //BTD
        if ($count) {
            $query = 'select count(*) as total_records from hb_transfer_booking_transction_details BTD where domain_origin=' . get_domain_auth_id() . ' AND BTD.created_by_id =' . $GLOBALS['CI']->entity_user_id;
            $data = $this->db->query($query)->row_array();
            return $data['total_records'];
        } else {
            $this->load->library('booking_data_formatter');
            $response['status'] = SUCCESS_STATUS;
            $booking_transction_details = array();
            $booking_service_details = array();
            $booking_transfer_details = array();
            $booking_contact_details = array();
            $booking_customer_details = array();
            $td_query = 'select * from hb_transfer_booking_transction_details AS BTD
						WHERE BTD.domain_origin=' . get_domain_auth_id() . ' and BTD.created_by_id =' . $GLOBALS['CI']->entity_user_id . '' . $condition . '
						order by BTD.origin desc limit ' . $offset . ', ' . $limit;
            //debug($td_query); exit;
            $booking_details = $this->db->query($td_query)->result_array();
            $app_reference_ids = $this->booking_data_formatter->implode_app_reference_ids($booking_details);
            if (empty($app_reference_ids) == false) {
                $id_query = 'select * from hb_transfer_contact_details CD WHERE CD.app_reference IN (' . $app_reference_ids . ')';
                $bd_query = 'select * from hb_transfer_booking_details BD WHERE BD.app_reference IN (' . $app_reference_ids . ')';
                $cd_query = 'select * from hb_transfer_paxes_details PD WHERE PD.app_reference IN (' . $app_reference_ids . ')';
                $sd_query = 'select * from hb_transfer_service_details SD WHERE SD.app_reference IN(' . $app_reference_ids . ')';
                $tcd_query = 'select TCD.* from hb_transfer_cancellation_policy TCD WHERE TCD.app_reference IN(' . $app_reference_ids . ')';

                //debug($id_query);
                $booking_contact_details = $this->db->query($id_query)->result_array();
                $booking_customer_details = $this->db->query($cd_query)->result_array();
                $booking_service_details = $this->db->query($sd_query)->result_array();
                $booking_transfer_details = $this->db->query($bd_query)->result_array();
                $booking_cancellation_details = $this->db->query($tcd_query)->result_array();
            }
            $response['data']['booking_transction_details'] = $booking_details;
            $response['data']['booking_service_details'] = $booking_service_details;
            $response['data']['booking_transfer_details'] = $booking_transfer_details;
            $response['data']['booking_contact_details'] = $booking_contact_details;
            $response['data']['booking_customer_details'] = $booking_customer_details;
            if (isset($booking_cancellation_details) && !empty($booking_cancellation_details))
                $response['data']['booking_cancellation_details'] = $booking_cancellation_details;
            //$response['data']['cancellation_details']	= $this->db->query($cancellation_details_query)->result_array();
            //debug($response); exit;

            return $response;
        }
    }

   

}
