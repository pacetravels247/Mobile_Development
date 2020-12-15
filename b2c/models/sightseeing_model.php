<?php

//require_once APPPATH.'libraries/checkuser.php';
class Sightseeing_Model extends CI_Model {
    

    
    function __construct() {
        // Call the Model constructor
        parent::__construct();
     
    }   
    /*
     *
     * Get Destination List
     *
     */
    function get_sightseen_city_list($search_chars)
    {
        $raw_search_chars = $this->db->escape($search_chars);
        if(empty($search_chars)==false){
            $r_search_chars = $this->db->escape($search_chars.'%');
            $search_chars = $this->db->escape($search_chars.'%');
        }else{
            $r_search_chars = $this->db->escape($search_chars);
            $search_chars = $this->db->escape($search_chars);
        }
        
        $query = 'Select cm.origin,cm.destination_name as city_name from api_sightseeing_destination_list as cm where  cm.destination_name like '.$search_chars.' 
                ORDER BY cm.destination_name asc, CASE
            WHEN    cm.destination_name    LIKE    '.$raw_search_chars.'   THEN 1
            WHEN    cm.destination_name    LIKE    '.$r_search_chars.' THEN 2  
            WHEN    cm.destination_name    LIKE    '.$search_chars.'   THEN 3
            ELSE 4 END LIMIT 0, 30
        ';
        
       
        return $this->db->query($query)->result_array();
    }
    function booking_old($condition = array(), $count = false, $offset = 0, $limit = 100000000000) {
        $condition = $this->custom_db->get_custom_condition($condition);
        //BT, CD, ID
        if ($count) {
                
            //echo debug($condition);exit;
            $query = 'select count(distinct(BD.activityid)) AS total_records from sightseen_booking_info BD
                    left join user U on U.user_id = BD.customer_contact_details_id 
                    left join user_type UT on UT.origin = U.user_type where BD.created_by_id =' . $GLOBALS['CI']->entity_user_id .'
                    '.$condition;
            
                
            $data = $this->db->query($query)->row_array();
                
            return $data['total_records'];
        } else {

            $response['status'] = SUCCESS_STATUS;
            $response['data'] = array();
            $booking_itinerary_details  = array();
            $booking_customer_details   = array();
            $booking_transaction_details = array();
            $cancellation_details = array();
            $payment_details = array();
            //Booking Details
                            
            $bd_query = 'select *,TD.app_reference as app_reference from transaction_details AS TD
                         left join customer_contact_details CCD on CCD.customer_contact_details_id = TD.customer_contact_details_id
                         left join sightseen_booking_info SBI on SBI.customer_contact_details_id  = TD.customer_contact_details_id
                         where SBI.created_by_id =' . $GLOBALS['CI']->entity_user_id .'
                         order by SBI.voucher_date desc limit '.$offset.', '.$limit;
            $booking_details    = $this->db->query($bd_query)->result_array();
            
            
        
            $response['data']['booking_details']            = $booking_details;

            //$response['data']['payment_details']  = $payment_details;
            return $response;
        }
    }
        /********SightSeenRelated Start***********/

    /**
     * SAve search data for future use - Analytics
     * @param array $params
     */
    function save_search_data($search_data, $type)
    {
      
        $data['domain_origin'] = get_domain_auth_id();
        $data['search_type'] = $type;
        $data['created_by_id'] = intval(@$this->entity_user_id);
        $data['created_datetime'] = date('Y-m-d H:i:s');
        $data['destination_name '] =trim($search_data['from']);
        $data['destination_id'] = $search_data['destination_id'];       
        // if($search_data['from_date']){
        //      $data['from_date'] = $search_data['from_date'];     
        // }else{
        //      $data['from_date'] = date('Y-m-d');
        // }
        // if($search_data['to_date']){
        //     $data['to_date'] = $search_data['to_date'];    
        // }else{
        //     $data['to_date'] = date('Y-m-d');
        // }        
        if(isset($search_data['category_id'])){
            $data['category_id'] = $search_data['category_id'];    
        }
        
        $this->custom_db->insert_record('search_sightseen_history', $data);
    }
    /**
     * get search data and validate it
     */
    function get_safe_search_data($search_id,$module_id)
    {
        //echo $search_id;
        $search_data = $this->get_search_data($search_id,$module_id);

        $success = true;
        $clean_search = array();
        if ($search_data != false) {
            //validate
            $temp_search_data = json_decode($search_data['search_data'], true);         

            $clean_search['from_date'] = @$temp_search_data['from_date'];
            $clean_search['to_date'] = @$temp_search_data['to_date'];
           
            $clean_search['destination'] = $temp_search_data['from'];
           
            $clean_search['destination_id'] = $temp_search_data['destination_id'];
           if(empty($temp_search_data['category_id'])==true){
                $clean_search['category_id'] = 0;
           }else{
            $clean_search['category_id'] = $temp_search_data['category_id'];
           }
            
            //debug($clean_search);
            //exit;
        } else {
            $success = false;
        }
       
        return array('status' => $success, 'data' => $clean_search);
    }
    /**
     * get search data without doing any validation
     * @param $search_id
     */
    function get_search_data($search_id,$module_id=META_SIGHTSEEING_COURSE)
    {
        if (empty($this->master_search_data)) {
            $search_data = $this->custom_db->single_table_records('search_history', '*', array('search_type' => $module_id, 'origin' => $search_id));
            if ($search_data['status'] == true) {
                $this->master_search_data = $search_data['data'][0];
            } else {
                return false;
            }
        }
        return $this->master_search_data;
    }
    /**
     * get all the booking source which are active for current domain
     */
    function active_booking_source($course_id=META_SIGHTSEEING_COURSE)
    {
        $query = 'select BS.source_id, BS.origin from meta_course_list AS MCL, booking_source AS BS, activity_source_map AS ASM WHERE
        MCL.origin=ASM.meta_course_list_fk and ASM.booking_source_fk=BS.origin and MCL.course_id='.$this->db->escape($course_id).'
        and BS.booking_engine_status='.ACTIVE.' AND MCL.status='.ACTIVE.' AND ASM.status="active"';
        return $this->db->query($query)->result_array();
    }
    /*
    *Elavarasi B2B markup (level4,level3)
    */
    public function get_admin_agent_markup($markup_level='',$user_id=0,$reference_id=0,$type='generic',$module_type='b2b_sightseen'){

        $str = "select * from markup_list where type='".$type."' and module_type='$module_type' and  markup_level='$markup_level' and reference_id=".$reference_id." and user_oid=".$user_id;
       // echo $str.'<br/>';
        $execute = $this->db->query($str);
        if($execute->num_rows()!=''){
            return $execute->result_array();
        }else{
            return array();
        }
    }
   public  function save_booking_details($domain_origin, $status, $app_reference, $booking_source, $booking_id, $booking_reference, $confirmation_reference, $product_name, $star_rating, $product_code,$grade_code,$grade_desc,$phone_number, $alternate_number, $email, $travel_date,$payment_mode,$attributes,$created_by_id, $transaction_currency, $currency_conversion_rate,$phone_code )
    {
        $data['domain_origin'] = $domain_origin;
        $data['status'] = $status;
        $data['app_reference'] = $app_reference;
        $data['booking_source'] = $booking_source;
        $data['booking_id'] = $booking_id;
        $data['booking_reference'] = $booking_reference;
        $data['confirmation_reference'] = $confirmation_reference;
        $data['product_name'] = $product_name;
        $data['star_rating'] = $star_rating;
        $data['product_code'] = $product_code;
        $data['phone_number'] = $phone_number;
        $data['phone_code'] = $phone_code;
        $data['alternate_number'] = $alternate_number;
        $data['email'] = $email;
        $data['grade_code'] = $grade_code;
        $data['travel_date'] = $travel_date;
        $data['grade_desc'] = $grade_desc;
        $data['payment_mode'] = $payment_mode;
        $data['attributes'] = serialized_data($attributes);
        $data['created_by_id'] = $created_by_id;
        $data['created_datetime'] = date('Y-m-d H:i:s');        
        $data['currency'] = $transaction_currency;
        $data['currency_conversion_rate'] = $currency_conversion_rate;

        //Set default value for PDO connection
        $data['promo_code'] = '';
        $data['discount'] = 0;        
        $data['booking_billing_type'] = ''; 
        
        $status = $this->custom_db->insert_record('sightseeing_booking_details', $data);
        return $status;
    }   

   /**
     *
     * @param string $app_reference
     * @param string $location
     
     * @param date   $travel_date
     * @param string $grade_desc
     * @param string $grade_code
     * @param string $status
     * 
     * @param string $attributes
     */
     function save_booking_itinerary_details($app_reference, $location, $travel_date,$grade_code, $grade_desc, $status, $total_fare,$admin_net_fare_markup,$admin_markup, $agent_markup, $currency, $attributes,$book_total_fare,$agent_commission,$agent_tds,$admin_com,$admin_tds,$api_raw_fare,$agent_buying_price, $gst='')
    {
        $data['app_reference'] = $app_reference;
        $data['location'] = $location;
        $data['travel_date'] = $travel_date;
        $data['grade_desc'] = $grade_desc;
        $data['grade_code'] = $grade_code;
        $data['status'] = $status;        
        $data['total_fare'] = $total_fare;
        $data['admin_net_markup'] = $admin_net_fare_markup;
        $data['admin_markup    '] = $admin_markup;
        $data['agent_markup'] = $agent_markup;
        $data['currency'] = $currency;
        $data['attributes'] = $attributes;
        $data['agent_commission'] = $agent_commission;
        $data['agent_tds'] = $agent_tds;
        $data['admin_commission'] = $admin_com;
        $data['admin_tds'] = $admin_tds;
        $data['api_raw_fare'] = $api_raw_fare;
        $data['agent_buying_price'] =$agent_buying_price;
        $data['gst'] = $gst;

        //Set default value for PDO connection
        $data['ProductPrice'] = 0;
        $data['Discount'] = 0;
        $data['Tax'] = 0;

        $status = $this->custom_db->insert_record('sightseeing_booking_itinerary_details', $data);
        return $status;
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
        $bd_query = 'select * from sightseeing_booking_details AS BD WHERE BD.app_reference like '.$this->db->escape($app_reference);
        if (empty($booking_source) == false) {
            $bd_query .= '  AND BD.booking_source = '.$this->db->escape($booking_source);
        }
        if (empty($booking_status) == false) {
            $bd_query .= ' AND BD.status = '.$this->db->escape($booking_status);
        }
        $id_query = 'select * from sightseeing_booking_itinerary_details AS ID WHERE ID.app_reference='.$this->db->escape($app_reference);
        $cd_query = 'select * from sightseeing_booking_pax_details AS CD WHERE CD.app_reference='.$this->db->escape($app_reference);
        $cancellation_details_query = 'select HCD.* from sightseeing_cancellation_details AS HCD WHERE HCD.app_reference='.$this->db->escape($app_reference);
        $response['data']['booking_details']            = $this->db->query($bd_query)->result_array();
        $response['data']['booking_itinerary_details']  = $this->db->query($id_query)->result_array();
        $response['data']['booking_customer_details']   = $this->db->query($cd_query)->result_array();
        $response['data']['cancellation_details']   = $this->db->query($cancellation_details_query)->result_array();
        if (valid_array($response['data']['booking_details']) == true and valid_array($response['data']['booking_itinerary_details']) == true and valid_array($response['data']['booking_customer_details']) == true) {
            $response['status'] = SUCCESS_STATUS;
        }
        return $response;
    }
    /**
     * return booking list
     */
    function filter_booking_report($search_filter_condition = '', $count=false, $offset=0, $limit=100000000000)
    {
        if(empty($search_filter_condition) == false) {
            $search_filter_condition = ' and'.$search_filter_condition;
        }
        //BT, CD, ID
        if ($count) {
            $query = 'select count(distinct(BD.app_reference)) as total_records 
                    from transfer_booking_details BD
                    join transfer_booking_itinerary_details AS HBID on BD.app_reference=HBID.app_reference
                    join payment_option_list AS POL on BD.payment_mode=POL.payment_category_code 
                    where BD.domain_origin='.get_domain_auth_id().' and BD.created_by_id ='.$GLOBALS['CI']->entity_user_id.' '.$search_filter_condition;
            $data = $this->db->query($query)->row_array();
            return $data['total_records'];
        } else {
            $this->load->library('booking_data_formatter');
            $response['status'] = SUCCESS_STATUS;
            $response['data'] = array();
            $booking_itinerary_details  = array();
            $booking_customer_details   = array();
            $bd_query = 'select * from transfer_booking_details AS BD 
                        WHERE BD.domain_origin='.get_domain_auth_id().' and BD.created_by_id ='.$GLOBALS['CI']->entity_user_id.' '.$search_filter_condition.'
                        order by BD.origin desc limit '.$offset.', '.$limit;
            $booking_details = $this->db->query($bd_query)->result_array();
            $app_reference_ids = $this->booking_data_formatter->implode_app_reference_ids($booking_details);
            if(empty($app_reference_ids) == false) {
                $id_query = 'select * from transfer_booking_itinerary_details AS ID 
                            WHERE ID.app_reference IN ('.$app_reference_ids.')';
                $cd_query = 'select * from transfer_booking_pax_details AS CD 
                            WHERE  CD.app_reference IN ('.$app_reference_ids.') ';
                $booking_itinerary_details  = $this->db->query($id_query)->result_array();
                $booking_customer_details   = $this->db->query($cd_query)->result_array();
            }
            $response['data']['booking_details']            = $booking_details;
            $response['data']['booking_itinerary_details']  = $booking_itinerary_details;
            $response['data']['booking_customer_details']   = $booking_customer_details;
            return $response;
        }
    }
      /**
     * return booking list
     */
    function filter_booking_report_sightseen($search_filter_condition = '', $count=false, $offset=0, $limit=100000000000)
    {
        if(empty($search_filter_condition) == false) {
            $search_filter_condition = ' and'.$search_filter_condition;
        }
        //BT, CD, ID
        if ($count) {
            $query = 'select count(distinct(BD.app_reference)) as total_records 
                    from sight_seen_booking_details BD
                    join sight_seen_booking_itinerary_details AS HBID on BD.app_reference=HBID.app_reference
                    join payment_option_list AS POL on BD.payment_mode=POL.payment_category_code 
                    where BD.domain_origin='.get_domain_auth_id().' and BD.created_by_id ='.$GLOBALS['CI']->entity_user_id.' '.$search_filter_condition;
            $data = $this->db->query($query)->row_array();
            return $data['total_records'];
        } else {
            $this->load->library('booking_data_formatter');
            $response['status'] = SUCCESS_STATUS;
            $response['data'] = array();
            $booking_itinerary_details  = array();
            $booking_customer_details   = array();
            $bd_query = 'select * from sight_seen_booking_details AS BD 
                        WHERE BD.domain_origin='.get_domain_auth_id().' and BD.created_by_id ='.$GLOBALS['CI']->entity_user_id.' '.$search_filter_condition.'
                        order by BD.origin desc limit '.$offset.', '.$limit;
            $booking_details = $this->db->query($bd_query)->result_array();
            $app_reference_ids = $this->booking_data_formatter->implode_app_reference_ids($booking_details);
            if(empty($app_reference_ids) == false) {
                $id_query = 'select * from sight_seen_booking_itinerary_details AS ID 
                            WHERE ID.app_reference IN ('.$app_reference_ids.')';
                $cd_query = 'select * from sight_seen_booking_pax_details AS CD 
                            WHERE  CD.app_reference IN ('.$app_reference_ids.') ';
                $booking_itinerary_details  = $this->db->query($id_query)->result_array();
                $booking_customer_details   = $this->db->query($cd_query)->result_array();
            }
            $response['data']['booking_details']            = $booking_details;
            $response['data']['booking_itinerary_details']  = $booking_itinerary_details;
            $response['data']['booking_customer_details']   = $booking_customer_details;
            return $response;
        }
    }
    function save_booking_pax_details($title,$app_reference,$first_name,$last_name, $phone, $email, $pax_type,$status)
    {        
        $data['app_reference'] = $app_reference;
        $data['title'] = $title;
        $data['first_name'] = $first_name;      
        $data['last_name'] = $last_name;
        $data['phone'] = $phone;
        $data['email'] = $email;
        $data['pax_type'] = $pax_type;        
        $data['status'] = $status;
        //
        $data['middle_name'] = '';
        $data['age'] = 0;
        $data['date_of_birth'] = date('Y-m-d');
        $data['attributes'] = '';
      
        $status = $this->custom_db->insert_record('sightseeing_booking_pax_details', $data);
        
        return $status;
    }


    /**
     * return booking list
     */
    function booking($condition=array(), $count=false, $offset=0, $limit=100000000000)
    {
        
    
        $condition = $this->custom_db->get_custom_condition($condition);
        //BT, CD, ID
        if ($count) {
            $query = 'select count(distinct(BD.app_reference)) as total_records 
                    from sightseeing_booking_details BD
                    join sightseeing_booking_itinerary_details AS HBID on BD.app_reference=HBID.app_reference
                    join payment_option_list AS POL on BD.payment_mode=POL.payment_category_code 
                    where BD.domain_origin='.get_domain_auth_id().' and BD.created_by_id ='.$GLOBALS['CI']->entity_user_id.' '.$condition;
            //echo $query;exit;
            $data = $this->db->query($query)->row_array();
            return $data['total_records'];
        } else {
            $this->load->library('booking_data_formatter');
            $response['status'] = SUCCESS_STATUS;
            $response['data'] = array();
            $booking_itinerary_details  = array();
            $booking_customer_details   = array();
            $cancellation_details = array();
            $bd_query = 'select * from sightseeing_booking_details AS BD 
                        WHERE BD.domain_origin='.get_domain_auth_id().' and BD.created_by_id ='.$GLOBALS['CI']->entity_user_id.' '.$condition.'
                        order by BD.origin desc limit '.$offset.', '.$limit;
            $booking_details = $this->db->query($bd_query)->result_array();
            $app_reference_ids = $this->booking_data_formatter->implode_app_reference_ids($booking_details);
            if(empty($app_reference_ids) == false) {
                $id_query = 'select * from sightseeing_booking_itinerary_details AS ID 
                            WHERE ID.app_reference IN ('.$app_reference_ids.')';
                $cd_query = 'select * from  sightseeing_booking_pax_details AS CD 
                            WHERE  CD.app_reference IN ('.$app_reference_ids.') ';
                $cancellation_details_query = 'select * from sightseeing_cancellation_details AS HCD 
                            WHERE HCD.app_reference IN ('.$app_reference_ids.')';
                $booking_itinerary_details  = $this->db->query($id_query)->result_array();
                $booking_customer_details   = $this->db->query($cd_query)->result_array();
                $cancellation_details   = $this->db->query($cancellation_details_query)->result_array();
            }
            $response['data']['booking_details']            = $booking_details;
            $response['data']['booking_itinerary_details']  = $booking_itinerary_details;
            $response['data']['booking_customer_details']   = $booking_customer_details;
            $response['data']['cancellation_details']   = $cancellation_details;
            return $response;
        }
    }    
    function booking_guest_user($app_reference, $booking_source, $booking_status){
        $response['status'] = SUCCESS_STATUS;
        $response['data'] = array();
        $booking_itinerary_details  = array();
        $booking_customer_details   = array();
        $cancellation_details = array();
        $bd_query = 'select * from sightseeing_booking_details AS BD WHERE BD.app_reference like '.$this->db->escape($app_reference).' or BD.booking_reference like '.$this->db->escape($app_reference);
        if (empty($booking_source) == false) {
            $bd_query .= '  AND BD.booking_source = '.$this->db->escape($booking_source);
        }
        if (empty($booking_status) == false) {
            $bd_query .= ' AND BD.status = '.$this->db->escape($booking_status);
        }
        
        $booking_details = $this->db->query($bd_query)->result_array();
        $app_reference_ids = $this->booking_data_formatter->implode_app_reference_ids($booking_details);
        if(empty($app_reference_ids) == false) {
            $id_query = 'select * from sightseeing_booking_itinerary_details AS ID 
                        WHERE ID.app_reference IN ('.$app_reference_ids.')';
            $cd_query = 'select * from  sightseeing_booking_pax_details AS CD 
                        WHERE  CD.app_reference IN ('.$app_reference_ids.') ';
            $cancellation_details_query = 'select * from sightseeing_cancellation_details AS HCD 
                        WHERE HCD.app_reference IN ('.$app_reference_ids.')';
            $booking_itinerary_details  = $this->db->query($id_query)->result_array();
            $booking_customer_details   = $this->db->query($cd_query)->result_array();
            $cancellation_details   = $this->db->query($cancellation_details_query)->result_array();
        }
        $response['data']['booking_details']            = $booking_details;
        $response['data']['booking_itinerary_details']  = $booking_itinerary_details;
        $response['data']['booking_customer_details']   = $booking_customer_details;
        $response['data']['cancellation_details']   = $cancellation_details;
        return $response;
    }
    /**
     * Elavarasi (Viator)
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
        $this->custom_db->update_record('sightseeing_booking_details', array('status' => $booking_status), array('app_reference' => $AppReference));//later
        //3.Update Itinerary Status
        $this->custom_db->update_record('sightseeing_booking_itinerary_details', array('status' => $booking_status), array('app_reference' => $AppReference));//later
    }
    /**
     * Add Cancellation details(Viator)
     * @param unknown_type $AppReference
     * @param unknown_type $cancellation_details
     */
   
    private function update_cancellation_refund_details($AppReference, $cancellation_details)
    {
        $sightseeing_cancellation_details = array();
        $sightseeing_cancellation_details['app_reference'] =              $AppReference;
        $sightseeing_cancellation_details['ChangeRequestId'] =            $cancellation_details['ChangeRequestId'];
        $sightseeing_cancellation_details['ChangeRequestStatus'] =        $cancellation_details['ChangeRequestStatus'];
        $sightseeing_cancellation_details['status_description'] =         $cancellation_details['StatusDescription'];
        $sightseeing_cancellation_details['API_RefundedAmount'] =         @$cancellation_details['RefundedAmount'];
        $sightseeing_cancellation_details['API_CancellationCharge'] =     @$cancellation_details['CancellationCharge'];
        $sightseeing_cancellation_details['currency'] = admin_base_currency();

        if($cancellation_details['ChangeRequestStatus'] == 3){
            $sightseeing_cancellation_details['cancellation_processed_on'] =  date('Y-m-d H:i:s');
        }
        $cancel_details_exists = $this->custom_db->single_table_records('sightseeing_cancellation_details', '*', array('app_reference' => $AppReference));
        if($cancel_details_exists['status'] == true) {
            //Update the Data
            unset($sightseeing_cancellation_details['app_reference']);
            $this->custom_db->update_record('sightseeing_cancellation_details', $sightseeing_cancellation_details, array('app_reference' => $AppReference));
        } else {
            //Insert Data
            $sightseeing_cancellation_details['created_by_id'] =              (int)@$this->entity_user_id;
            $sightseeing_cancellation_details['created_datetime'] =           date('Y-m-d H:i:s');
            $data['cancellation_requested_on'] = date('Y-m-d H:i:s');
            $this->custom_db->insert_record('sightseeing_cancellation_details',$sightseeing_cancellation_details);
        }
    }

}
