<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once BASEPATH . 'libraries/Common_Api_Grind.php';

/**
 *
 * @package    Provab
 * @subpackage API
 * @author     Anitha J<anitha.g.provab@gmail.com>
 * @version    V1
 */
class Provab_private extends Common_Api_Grind {

    protected $ClientId;
    protected $UserName;
    protected $Password;
    protected $system;   //test/live   -   System to which we have to connect in web service
    protected $Url;
    private $service_url;
    private $TokenId; //	Token ID that needs to be echoed back in every subsequent request
    protected $ins_token_file;
    private $CI;
    private $commission = array();
    var $master_search_data;
    var $search_hash; //search

    public function __construct() {
        parent::__construct();
        $this->CI = &get_instance();
        $this->CI->load->library('Api_Interface');
        $this->CI->load->model('car_model');
        $this->set_api_credentials();
    }

    private function set_api_credentials() {

        $car_engine_system = $this->CI->config->item('car_engine_system');
        $this->system = $car_engine_system;
        $this->UserName = $this->CI->config->item($car_engine_system . '_username');
        $this->Password = $this->CI->config->item($car_engine_system . '_password');
        $this->Url = $this->CI->config->item('car_url');
        $this->ClientId = $this->CI->config->item('domain_key');
        //$this->UserName = 'test';
        //$this->Password = 'password'; // miles@123 for b2b
    }

    function credentials($service) {
        switch ($service) {
            case 'Search':
                $this->service_url = $this->Url . 'Search';
                break;
            case 'CarRule':
                $this->service_url = $this->Url . 'RateRule';
                break;
            case 'CommitBooking':
                $this->service_url = $this->Url . 'Booking';
                break;
            
            case 'CancelBooking':
                $this->service_url = $this->Url . 'CancelBooking';
                break;
        }
    }
    /**
     * request Header
     */
    private function get_header() {
        $response['UserName'] = $this->UserName;
        $response['Password'] = $this->Password;
        $response['DomainKey'] = $this->DomainKey;
        $response['system'] = $this->system;
        return $response;
    }
    /**
     * get search result from tbo
     * @param number $search_id unique id which identifies search details
     */
    function get_car_list($search_id = '') {
      
        $this->CI->load->driver('cache');
        $response['data'] = array();
        $response['status'] = true;
        $search_data = $this->search_data($search_id);

        $header_info = $this->get_header();
        //generate unique searchid string to enable caching
        $cache_search = $this->CI->config->item('cache_car_search');
        $search_hash = $this->search_hash;
        if ($cache_search) {
            $cache_contents = $this->CI->cache->file->get($search_hash);
        }
      
        if ($search_data['status'] == true) {
            if ($cache_search === false || ($cache_search === true && empty($cache_contents) == true)) {
                //get request
                $search_request = $this->car_search_request($search_data['data']);

                //get data
                if ($search_request['status']) {
                    $search_response = $this->CI->api_interface->get_json_response($search_request['data']['service_url'], $search_request['data']['request'], $header_info);
                   // echo 'herre I am';exit;
                    // debug($search_response);exit;
                    if ($this->valid_api_response($search_response)) {
                        $response['data'] = $search_response['Search'];
                        $response['search_hash'] = $search_hash;
                        $response['from_cache'] = false;
                        if ($cache_search) {
                            $cache_exp = $this->CI->config->item('cache_car_search_ttl');
                            $this->CI->cache->file->save($search_hash, $response['data'], $cache_exp);
                        }
                    } else {
                        $response['status'] = false;
                    }
                } else {
                    $response['status'] = false;
                }
            } else {
                //read from cache
                $response['data'] = $cache_contents;
                $response['search_hash'] = $search_hash;
                $response['from_cache'] = true;
            }
        } else {
            $response['status'] = false;
        }
        return $response;
    }

    /**
     * get Car search request details
     * @param array $search_params data to be used while searching of flight
     */
    function car_search_request($search_params) {

        $response['status'] = SUCCESS_STATUS;
        $response['data'] = array();
        /** Request to be formed for search * */
        $this->credentials('Search');
        $request_params = array();
        $request_params['pickup_location'] = $search_params['pickup_location'];
        $request_params['return_location'] = $search_params['return_location'];
        $request_params['pickup_loc_id'] = $search_params['pickup_loc_code'];
        $request_params['return_loc_id'] = $search_params['return_loc_code'];
        $request_params['pickup_datetime'] = $search_params['pickup_datetime'];
        $request_params['return_datetime'] = $search_params['return_datetime'];
        $request_params['driver_age'] = $search_params['driver_age'];
        $response['data']['request'] = json_encode($request_params);
        $response['data']['service_url'] = $this->service_url;
        return $response;
    }
     /**
     * update markup currency and return summary
     */
    function update_markup_currency(& $price_summary, & $currency_obj, $multiplier=1, $level_one_markup = false, $current_domain_markup = true) {
        $markup_list = array('EstimatedTotalAmount');
        $markup_summary = array();
        // debug($price_summary);exit;
        foreach ($price_summary as $__k => $__v) {
            if (is_numeric($__v) == true) {
                $ref_cur = $currency_obj->force_currency_conversion($__v); //Passing Value By Reference so dont remove it!!!
                $price_summary[$__k] = $ref_cur['default_value'];   //If you dont understand then go and study "Passing value by reference"

                if (in_array($__k, $markup_list)) {
                    $book_total_fare = $__v;
                    $temp_price = $currency_obj->get_currency($__v, true, $level_one_markup, $current_domain_markup, $multiplier);
                } elseif (is_array($__v) == false) {
                    $temp_price = $currency_obj->force_currency_conversion($__v);
                } else {
                    $temp_price['default_value'] = $__v;
                }
                $markup_summary[$__k] = $temp_price['default_value'];
            }
        }
       
        
        //Markup
        //PublishedFare
        $Markup = 0;
        $price_summary['_Markup'] = 0;
        if (isset($markup_summary['EstimatedTotalAmount'])) {
            $Markup = $markup_summary['EstimatedTotalAmount'] - $price_summary['EstimatedTotalAmount'];
            $markup_summary['EstimatedTotalAmount'] = round($markup_summary['EstimatedTotalAmount']);
            $markup_summary['OneWayFee'] = round($markup_summary['OneWayFee']);
        }
        
        $deduction_cur_obj = clone $currency_obj;
        $markup_total_fare = $currency_obj->get_currency ( $book_total_fare, true, true, true, $multiplier ); // (ON Total PRICE ONLY)
        // debug($markup_total_fare);exit;
        $ded_total_fare = $deduction_cur_obj->get_currency ( $book_total_fare, true, false, true, $multiplier ); // (ON Total PRICE ONLY)
        $admin_markup = sprintf ( "%.2f", $markup_total_fare ['default_value'] - $ded_total_fare ['default_value'] );
        $agent_markup = sprintf ( "%.2f", $ded_total_fare ['default_value'] - $book_total_fare );
        $markup_summary['_Markup'] = $Markup;
        $markup_summary['admin_markup'] = $admin_markup;
        $markup_summary['agent_markup'] = $agent_markup;
        // debug($markup_summary);exit;
        return $markup_summary;
        // debug($markup_summary);exit;
    }
     /**
     * update markup currency and return summary
     */
    function update_cancel_markup_currency(& $price_summary, & $currency_obj, $multiplier=1, $level_one_markup = false, $current_domain_markup = true) {
        $markup_list = array('Amount');
        $markup_summary = array();
       
        foreach ($price_summary as $__k => $__v) {
            if (is_numeric($__v) == true) {
                $ref_cur = $currency_obj->force_currency_conversion($__v); //Passing Value By Reference so dont remove it!!!
                $price_summary[$__k] = $ref_cur['default_value'];   //If you dont understand then go and study "Passing value by reference"

                if (in_array($__k, $markup_list)) {
                    $temp_price = $currency_obj->get_currency($__v, true, $level_one_markup, $current_domain_markup, $multiplier);
                } elseif (is_array($__v) == false) {
                    $temp_price = $currency_obj->force_currency_conversion($__v);
                } else {
                    $temp_price['default_value'] = $__v;
                }
                $markup_summary[$__k] = $temp_price['default_value'];
            }
        }
        // debug($markup_summary);
        // debug($price_summary);
        
        //Markup
        //PublishedFare
        $Markup = 0;
        $price_summary['_Markup'] = 0;
        if (isset($markup_summary['Amount'])) {
            $Markup = $markup_summary['Amount'] - $price_summary['Amount'];
            $markup_summary['Amount'] = round($markup_summary['Amount']);
            $markup_summary['ToDate'] = $price_summary['ToDate'];
            $markup_summary['CurrencyCode'] = $price_summary['CurrencyCode'];
            $markup_summary['FromDate'] = $price_summary['FromDate'];
        }
        $markup_summary['_Markup'] = $Markup;
       
        return $markup_summary;
        // debug($markup_summary);exit;
    }
     /**
     * calculate and return total price details
     */
    function total_price($price_summary, $retain_commission = false, $currency_obj = '') {

    }
    /**
     * Wrapper - 1 for booking
     * Anitha G
     * Process Booking
     * @param array $booking_params
     */
    public function process_booking($book_id, $booking_params) {
    	$response['data'] = array();
        $response['status'] = SUCCESS_STATUS;
        $resposne['msg'] = 'Remote IO Error';
        if (valid_array($booking_params)) {
        	$booking_request = $this->BookCar_request($booking_params, $booking_params['booking_source'], $book_id);
        	$header = $this->get_header();
        	if ($booking_request['status']) {
        		$car_book_response = $GLOBALS ['CI']->api_interface->get_json_response ( $booking_request ['data'] ['service_url'], $booking_request ['data'] ['request'], $header );
				$GLOBALS ['CI']->custom_db->generate_static_response ( json_encode ( $car_book_response ) );
        		/**    PROVAB LOGGER * */
                $GLOBALS['CI']->private_management_model->provab_xml_logger('Book_Car', $book_id, 'car', json_encode($booking_request['data']), json_encode($car_book_response));
                // debug($car_book_response);exit;
                if (valid_array($car_book_response) == true && $car_book_response['Status'] == true) {
                    $response['data']['result'] = $car_book_response['Booking'];
                } else {
                    $response['status'] = false;
                }
        	}
        	else {
                $response['status'] = false;
            }
        }
        else {
            $response['status'] = FAILURE_STATUS;
        }

        return $response;	
    }
      /**
     * Booking params
     */
    function BookCar_request($booking_params, $booking_source, $booking_id) {
    	$this->CI->load->model('custom_db');
    	// debug($booking_params);exit;
    	$response['status'] = SUCCESS_STATUS;
        $response['data'] = array();
    	$passenger['FirstName'] = $booking_params['first_name'];
    	$passenger['LastName'] = $booking_params['last_name'];
    	$passenger['City'] = $booking_params['city_name'];
    	$passenger['AddressLine1'] = $booking_params['address'];
    	$passenger['AddressLine1'] = $booking_params['address'];
    	$passenger['PinCode'] = $booking_params['postal_code'];
    	$passenger['Email'] = $booking_params['billing_email'];
    	$passenger['ContactNo'] = $booking_params['passenger_contact'];
    	$date_of_birth = $booking_params['date_of_birth'];
    	$date_of_birth = explode('-', $date_of_birth);
    	$passenger['DateOfBirth'] = $date_of_birth[2].'-'.$date_of_birth[1].'-'.$date_of_birth[0];
    	$country_data = $this->CI->custom_db->single_table_records('api_country_list', 'name, iso_country_code', array('origin' => intval($booking_params['country'])));
    	
    	$passenger['CountryCode'] = $country_data['data'][0]['iso_country_code'];
    	$passenger['CountryName'] = $country_data['data'][0]['name'];
    	$passenger['Title'] = $booking_params['pax_title'];
        if(isset($booking_params['gps']) || isset($booking_params['add_driver']) || $booking_params['Infant'] > 0 || $booking_params['Child'] > 0 || $booking_params['Booster'] > 0){
            if(isset($booking_params['gps'])){
                $extra_service['Gps'] = $booking_params['gps'];
            }
            if(isset($booking_params['add_driver'])){
                $extra_service['Addtionaldriver'] = $booking_params['add_driver'];
            }
            if(isset($booking_params['full_prot'])){
                $extra_service['FullProtection'] = $booking_params['full_prot'];
            }
            if(isset($booking_params['Infant']) && $booking_params['Infant'] > 0){
                $extra_service['Infant_equip_count'] = $booking_params['Infant'];
            }  
            if(isset($booking_params['Child']) && $booking_params['Child'] > 0){
                $extra_service['Child_equip_count'] = $booking_params['Child'];
            } 
            if(isset($booking_params['Booster']) && $booking_params['Booster'] > 0){
                $extra_service['Booster_equip_count'] = $booking_params['Booster'];
            } 
            $passenger['ExtraServices'] = $extra_service; 
        }
    	$request ['ResultToken'] = $booking_params['token']['ResultToken'];
    	$request ['Passengers'] = $passenger;
    	$request ['AppReference'] = $booking_id;
		$response ['data'] ['request'] = json_encode ( $request );
		$this->credentials ( 'CommitBooking' );
		$response ['data'] ['service_url'] = $this->service_url;
		return $response;
     }
     /**
	 * Reference number generated for booking from application
	 * 
	 * @param
	 *        	$app_booking_id
	 * @param
	 *        	$params
	 */
	function save_booking($app_booking_id, $params, $module = 'b2c') {
        // echo $module;exit;
		// debug($params);exit;
		// Need to return following data as this is needed to save the booking fare in the transaction details
		$response ['fare'] = $response ['domain_markup'] = $response ['level_one_markup'] = 0;

		$domain_origin = get_domain_auth_id ();
		$master_search_id = $params ['temp_booking'] ['book_attributes'] ['search_id'];
		$search_data = $this->search_data ( $master_search_id );
		//$status = BOOKING_CONFIRMED;
		$app_reference = $app_booking_id;
		$booking_source = $params ['temp_booking'] ['booking_source'];
		
		$currency_obj = $params ['currency_obj'];
		$deduction_cur_obj = clone $currency_obj;
		// PREFERRED TRANSACTION CURRENCY AND CURRENCY CONVERSION RATE
		$transaction_currency = get_application_currency_preference ();
		$application_currency = admin_base_currency ();
		$currency_conversion_rate = $currency_obj->transaction_currency_conversion_rate ();

		$search_data = $search_data['data'];
		$status = 'BOOKING_CONFIRMED';
		$domain_origin = get_domain_auth_id();
		$car_data = $params['temp_booking']['book_attributes']['token'];
		// debug($car_data);exit;
		//Car Details
		$car_name = $car_data['Name'];
		$car_supplier_name = $car_data['CompanyShortName'];
		$car_model = $car_data['VendorCarType'];
		$api_supplier_name = 'CarNet';
		$pickup_date_time = $search_data['pickup_datetime'];
		$pickup_date_time = explode(' ', $pickup_date_time);
		$retunr_date_time = $search_data['return_datetime'];
		$retunr_date_time = explode(' ', $retunr_date_time);
		$car_from_date = $pickup_date_time[0];
        $car_from_date = explode('-', $car_from_date);
        $car_from_date = $car_from_date[2].'-'.$car_from_date[1].'-'.$car_from_date[0];
		$car_to_date = $retunr_date_time[0];
        $car_to_date = explode('-', $car_to_date);
        $car_to_date = $car_to_date[2].'-'.$car_to_date[1].'-'.$car_to_date[0];
		$pickup_time = $pickup_date_time[1];
		$drop_time = $retunr_date_time[1];
		$car_pickup_location = $search_data['pickup_location'];
		$car_drop_location = $search_data['return_location'];
		$pick_up_phone = 'Telephone: '.$car_data['LocationDetails']['PickUpLocation']['Telephone'];
		$drop_phone = 'Telephone: '.$car_data['LocationDetails']['DropLocation']['Telephone'];
		$car_pickup_address = @$car_data['LocationDetails']['PickUpLocation']['Address']['StreetNmbr'].', '.$car_data['LocationDetails']['PickUpLocation']['Address']['CityName'].', '.$car_data['LocationDetails']['PickUpLocation']['Address']['PostalCode'].', '.$car_data['LocationDetails']['PickUpLocation']['Address']['CountryName'].', '.$pick_up_phone;
		$car_drop_address = @$car_data['LocationDetails']['DropLocation']['Address']['StreetNmbr'].', '.$car_data['LocationDetails']['DropLocation']['Address']['CityName'].', '.$car_data['LocationDetails']['DropLocation']['Address']['PostalCode'].', '.$car_data['LocationDetails']['DropLocation']['Address']['CountryName'].', '.$drop_phone;
		$oneway_fee = round(@$car_data['OneWayFee']['Amount']);
		$final_cancel_date ='';
		$transfer_type ='';
       // echo $car_from_date;exit;
		if(valid_array($car_data['CancellationPolicy'])){
			foreach($car_data['CancellationPolicy'] as $policy){
			if($policy['Amount'] == 0){
				$final_cancel_date .= $policy['ToDate'];
				}
			}
			$transfer_type .= 'Cancellation fee';
		}
		$created_by_id = intval ( @$GLOBALS ['CI']->entity_user_id );
		$payment_mode = $params ['temp_booking']['book_attributes']['payment_method'];
		$book_total_fare = round($params ['temp_booking'] ['book_attributes']['token']['TotalCharge']['EstimatedTotalAmount']);
		// debug($params);exit;
        if ($module == 'b2c') {
            $multiplier = 1;
            $markup_total_fare = $currency_obj->get_currency ( $book_total_fare, true, false, true, $multiplier); // (ON Total PRICE ONLY)
            $ded_total_fare = $deduction_cur_obj->get_currency ( $book_total_fare, true, true, false, $multiplier); // (ON Total PRICE ONLY)
            $admin_markup = sprintf ( "%.2f", $markup_total_fare ['default_value'] - $ded_total_fare ['default_value'] );
            $agent_markup = sprintf ( "%.2f", $ded_total_fare ['default_value'] - $book_total_fare );
        } else {
            // B2B Calculation
             $multiplier = 1;
            $markup_total_fare = $currency_obj->get_currency ( $book_total_fare, true, true, true, $multiplier ); // (ON Total PRICE ONLY)
            // debug($markup_total_fare);exit;
            $ded_total_fare = $deduction_cur_obj->get_currency ( $book_total_fare, true, false, true, $multiplier ); // (ON Total PRICE ONLY)
            $admin_markup = sprintf ( "%.2f", $markup_total_fare ['default_value'] - $ded_total_fare ['default_value'] );
            $agent_markup = sprintf ( "%.2f", $ded_total_fare ['default_value'] - $book_total_fare );
        }

// echo 'herrerre'.$admin_markup;exit;
        $booking_id = $params ['result'] ['BookingDetails']['BookingId'];
		$booking_reference = $params ['result'] ['BookingDetails']['BookingRefNo'];
		$email = $params['temp_booking']['book_attributes']['billing_email'];
		$phone_number = $params['temp_booking']['book_attributes']['passenger_contact'];
		$currency = $params ['temp_booking'] ['book_attributes']['token']['TotalCharge']['CurrencyCode'];
		$supplier_identifier = $params ['result'] ['BookingDetails']['SupplerIdentifier'];
		$this->CI->car_model->save_booking_details ( $domain_origin, $status, $app_reference, $booking_source, $currency, $phone_number, 
				$email, $payment_mode, $created_by_id,$currency_conversion_rate, $book_total_fare, $booking_id, 
				$booking_reference, $supplier_identifier, $car_name, $car_supplier_name, $car_model, $car_from_date,
				$car_to_date, $pickup_time, $drop_time, $car_pickup_location, $car_drop_location, $car_drop_address, $car_pickup_address, $final_cancel_date, $transfer_type,$oneway_fee);
		$priced_equip = json_encode(@$car_data['PricedEquip']);
		$priced_coverage = json_encode($car_data['PricedCoverage']);
		$cancellation_poicy = json_encode($car_data['CancellationPolicy']);

		$attributes['air_condition'] = $car_data['AirConditionInd'];
		$attributes['transmission_type'] = $car_data['TransmissionType'];
		$attributes['fuel_type'] = $car_data['FuelType'];
		$attributes['driver_type'] = $car_data['DriveType'];
		$attributes['pass_quantity'] = $car_data['PassengerQuantity'];
		$attributes['bagg_quantity'] = $car_data['BaggageQuantity'];
		$attributes['bagg_quantity'] = $car_data['BaggageQuantity'];
		$attributes['vendor_car_type'] = $car_data['VendorCarType'];
		$attributes['door_count'] = $car_data['DoorCount'];
		$attributes['vehicle_class_size'] = $car_data['VehClassSize'];
		$attributes['unlimited'] = $car_data['Unlimited'];
		$attributes['distanceunit'] = $car_data['DistUnitName'];
		$attributes['min_age'] = $car_data['RateRestrictions']['MinimumAge'];
		$attributes['max_age'] = $car_data['RateRestrictions']['MaximumAge'];
		$attributes['payment_rule'] = $car_data['PaymentRules']['PaymentRule'];
		$attributes['payment_type'] = $car_data['PaymentRules']['PaymentType'];
		$attributes['term_conditions'] = $car_data['TPA_Extensions']['TermsConditions'];
		$attributes['supplier_logo'] = $car_data['TPA_Extensions']['SupplierLogo'];
		$pickup_opening_hours = $car_data['LocationDetails']['PickUpLocation']['OperationSchedules']['Start'].'-'.$car_data['LocationDetails']['PickUpLocation']['OperationSchedules']['End'];
		$drop_opening_hours = $car_data['LocationDetails']['PickUpLocation']['OperationSchedules']['Start'].'-'.$car_data['LocationDetails']['PickUpLocation']['OperationSchedules']['End'];
		$car_pickup_lcation = $search_data['pickup_location'];
		$car_drop_location = $search_data['return_location'];
		$car_pickup_address = $car_data['LocationDetails']['PickUpLocation']['Address']['StreetNmbr'].', '.$car_data['LocationDetails']['PickUpLocation']['Address']['CityName'].', '.$car_data['LocationDetails']['PickUpLocation']['Address']['PostalCode'].', '.$car_data['LocationDetails']['PickUpLocation']['Address']['CountryName'].', '.$pick_up_phone;
		$car_drop_address = $car_data['LocationDetails']['DropLocation']['Address']['StreetNmbr'].', '.$car_data['LocationDetails']['DropLocation']['Address']['CityName'].', '.$car_data['LocationDetails']['DropLocation']['Address']['PostalCode'].', '.$car_data['LocationDetails']['DropLocation']['Address']['CountryName'].', '.$drop_phone;
		$attributes['pickup_opening_hours'] = $pickup_opening_hours;
		$attributes['drop_opening_hours'] = $drop_opening_hours;
		$pricture_url = $car_data['PictureURL'];
		$attrubutes1 = json_encode($attributes);


		$this->CI->car_model->save_booking_itinerary_details ( $app_reference, $car_from_date, $car_to_date, $pickup_time, $drop_time, $car_pickup_lcation,
													   $car_drop_location, $car_pickup_address, $car_drop_address, $car_name, $pricture_url, $priced_equip, $priced_coverage, $cancellation_poicy, $attrubutes1, $book_total_fare, $admin_markup, $agent_markup, $status);
		
		$email = $params['temp_booking']['book_attributes']['billing_email'];
		$phone_number = $params['temp_booking']['book_attributes']['passenger_contact'];
		$title = $params['temp_booking']['book_attributes']['pax_title'];
		$first_name = $params['temp_booking']['book_attributes']['first_name'];
		$last_name = $params['temp_booking']['book_attributes']['last_name'];
		if($title == 1){
			$gender = 'Male';
		}
		if($title == 1){
			$gender = 'FeMale';
		}
		$country = $params['temp_booking']['book_attributes']['country'];
		$country_data = $this->CI->custom_db->single_table_records('api_country_list', 'name, iso_country_code', array('origin' => intval($country)));
		$dob = $params['temp_booking']['book_attributes']['date_of_birth'];
		$country_code = $country_data['data'][0]['iso_country_code'];
		$country_name = $country_data['data'][0]['name'];
		$city = $params['temp_booking']['book_attributes']['city_name'];
		$pincode = $params['temp_booking']['book_attributes']['postal_code'];
		$adress1 = $params['temp_booking']['book_attributes']['address'];
		$adress2 = $params['temp_booking']['book_attributes']['address'];
		$this->CI->car_model->save_booking_pax_details ( $app_reference, $title, $first_name, $last_name, $phone_number, $email, $dob, $country_code, $country_name, $city, $pincode, $adress1, $adress2, $status);
		// debug($params);exit;
        if(isset($params ['result'] ['BookingDetails']['extra_services']) && valid_array($params ['result'] ['BookingDetails']['extra_services'])){
           $this->CI->car_model->save_booking_extra_details ($params ['result'] ['BookingDetails']['extra_services']);
        }
        /**
         * ************ Update Convinence Fees And Other Details Start *****************
         */
        // Convinence_fees to be stored and discount
        $convinence = 0;
        $discount = 0;
        $convinence_value = 0;
        $convinence_type = 0;
        $convinence_per_pax = 0;
        // echo $module;exit;
        if ($module == 'b2c') {
            $convinence = $currency_obj->convenience_fees ( $book_total_fare, $master_search_id );
            $convinence_row = $currency_obj->get_convenience_fees ();
            // debug($convinence_row);exit;
            $convinence_value = $convinence_row ['value'];
            $convinence_type = $convinence_row ['type'];
            $convinence_per_pax = $convinence_row ['per_pax']; 
            $discount = @$params  ['promo_code_discount_val'];
            $promo_code = @$params ['promo_code'];
        } elseif ($module == 'b2b') {
            $discount = 0;
            $promo_code ='';
        }
        $GLOBALS ['CI']->load->model ( 'transaction' );
        // SAVE Booking convinence_discount_details details
        $GLOBALS ['CI']->transaction->update_convinence_discount_details ( 'car_booking_details', $app_reference, $discount, $promo_code, $convinence, $convinence_value, $convinence_type, $convinence_per_pax );
        /**
         * ************ Update Convinence Fees And Other Details End *****************
         */
        
        //need to do
		$agent_markup = $agent_markup;
		$admin_markup = $admin_markup;
		$transaction_currency ='INR';
		$convinence =0;
		$discount = 0;
		$response['fare'] = $book_total_fare;
		$response ['admin_markup'] = $admin_markup;
		$response ['agent_markup'] = $agent_markup;
		$response ['convinence'] = $convinence;
		$response ['discount'] = $discount;
       	$response['phone'] = $phone_number;
        $response['transaction_currency'] = $transaction_currency;
        $response['currency_conversion_rate'] = $currency_conversion_rate;
        // echo 'herrer';exit;
        return $response;
	}
    /**
     * Anitha G
     * Cancel Booking
     */
    function cancel_booking($booking_details)
    {
        // debug($booking_details);exit;
        $header = $this->get_header ();
        $response ['data'] = array ();
        $response ['status'] = FAILURE_STATUS;
        $resposne ['msg'] = 'Remote IO Error';
        $app_reference = $booking_details ['app_reference'];
        $cancel_booking_request = $this->cancel_booking_request_params($app_reference );
        if ($cancel_booking_request ['status']) {
            // 1.SendChangeRequest
            // debug($cancel_booking_request);exit;
            $cancel_booking_response = $GLOBALS ['CI']->api_interface->get_json_response ( $cancel_booking_request ['data'] ['service_url'], $cancel_booking_request ['data'] ['request'], $header );
            // debug($cancel_booking_response);exit;
            $GLOBALS ['CI']->custom_db->generate_static_response ( json_encode ( $cancel_booking_response ) );
            
            // $cancel_booking_response = $GLOBALS['CI']->hotel_model->get_static_response(3317);
            if (valid_array ( $cancel_booking_response ) == true && $cancel_booking_response ['Status'] == SUCCESS_STATUS) {
                // debug($car_cancellation_details);exit;
                // Save Cancellation Details
                $car_cancellation_details = $cancel_booking_response ['CancelBooking']['CancellationDetails'];
                $GLOBALS ['CI']->car_model->update_cancellation_details ( $app_reference, $car_cancellation_details );
                $response ['status'] = SUCCESS_STATUS;
                
            } else {
                $response ['msg'] = $cancel_booking_response['Message'];
            }
        }
        return $response;
    }
    /**
     * Anitha G
     * Cancellation Request:SendChangeRequest
     */
    private function cancel_booking_request_params($app_reference) {
        $response ['status'] = true;
        $response ['data'] = array ();
        $request ['AppReference'] = trim ( $app_reference );
        

        $response ['data'] ['request'] = json_encode ( $request );
        $this->credentials ( 'CancelBooking' );
        $response ['data'] ['service_url'] = $this->service_url;
        // debug($response);
        // exit;
        return $response;
    }
    /**
     * convert search params to API frormat
     */
    public function search_data($search_id) {
        $response['status'] = true;
        $response['data'] = array();
        if (empty($this->master_search_data) == true and valid_array($this->master_search_data) == false) {
            $clean_search_details = $this->CI->car_model->get_safe_search_data($search_id);
            if ($clean_search_details['status'] == true) {
                $response['status'] = true;
                $response['data'] = $clean_search_details['data'];
                $response['data']['pickup_location'] = $clean_search_details['data']['car_from'];
                $response['data']['return_location'] = $clean_search_details['data']['car_to'];
                $response['data']['pickup_loc_code'] = $clean_search_details['data']['car_from_loc_code'];
                $response['data']['return_loc_code'] = $clean_search_details['data']['car_to_loc_code'];
                $response['data']['pickup_datetime'] = $clean_search_details['data']['depature'].' '.$clean_search_details['data']['depature_time'];
                $response['data']['return_datetime'] = $clean_search_details['data']['return'].' '.$clean_search_details['data']['return_time'];
                $this->master_search_data = $response['data'];
            }
            else {
                $response['status'] = false;
            }
        }
        else {
            $response['data'] = $this->master_search_data;
        }
        $this->search_hash = md5(serialized_data($response['data']));
        return $response;
    }
     /**
     * booking_url to be used
     */
    function booking_url($search_id) {
        return base_url() . 'index.php/car/booking/' . intval($search_id);
    }
    /**
     * Converts API data currency to preferred currency
     * Anitha G
     * 
     * @param unknown_type $search_result           
     * @param unknown_type $currency_obj            
     */
    public function search_data_in_preferred_currency($search_result, $currency_obj,$search_id) {
        
        $cars = $search_result ['data'] ['CarSearchResult'] ['CarResults'];
        $car_list = array ();
        foreach ( $cars as $hk => $hv ) {
            
            $car_list [$hk] = $hv;
            //Update Markup price in search result          
            $car_list [$hk] ['TotalCharge'] = $this->preferred_currency_fare_object ($hv ['TotalCharge'], $currency_obj ); 
            $car_list [$hk] ['CancellationPolicy'] = $this->preferred_currency_fare_object_cancel ($hv ['CancellationPolicy'], $currency_obj ); 
            $car_list [$hk] ['PricedCoverage'] = $this->preferred_currency_fare_object_coverage ($hv ['PricedCoverage'], $currency_obj );      
            
        }
        // debug($car_list);exit;
        $search_result ['data'] ['CarSearchResult'] ['PreferredCurrency'] = get_application_currency_preference ();
        $search_result ['data'] ['CarSearchResult'] ['CarResults'] = $car_list;
        
        return $search_result;
    }
    public function car_rule_in_preferred_currency($rate_rule_result, $module = 'b2c', $currency_obj,$search_id) {
        $level_one = true;
        $current_domain = true;
        if ($module == 'b2c') {
            $level_one = false;
            $current_domain = true;
        } else if ($module == 'b2b') {
            $level_one = true;
            $current_domain = true;
        }
        $cars = $rate_rule_result['RateRule'] ['CarRuleResult'];
        $rate_rule_result = array ();
        foreach ( $cars as $hk => $hv ) {
            $rate_rule_result [$hk] = $hv;
            //Update Markup price in search result          
            // $TotalCharge = $this->preferred_currency_fare_object ($hv ['TotalCharge'], $currency_obj );  
           // debug($TotalCharge);exit;
            $rate_rule_result [$hk] ['TotalCharge'] = $this->update_search_markup_currency ( $hv ['TotalCharge'], $currency_obj, $search_id, $level_one, $current_domain );
            // debug($rate_rule_result [$hk] ['TotalCharge']);exit;
            if(isset($hv ['PricedCoverage']) && valid_array($hv ['PricedCoverage'])){
                $rate_rule_result [$hk] ['PricedCoverage'] = $this->preferred_currency_fare_object_coverage ($hv ['PricedCoverage'], $currency_obj ); 
   
            }
            if(isset($hv ['PricedEquip']) && valid_array($hv ['PricedEquip'])){
                $rate_rule_result [$hk] ['PricedEquip'] = $this->preferred_currency_fare_object_equip($hv ['PricedEquip'], $currency_obj );           

            }
        }
        // debug($car_list);exit;
        $car_rule_result  ['RateRule'] ['PreferredCurrency'] = get_application_currency_preference ();
        $car_rule_result ['RateRule'] ['CarRuleResult'] = $rate_rule_result;
       
        return $car_rule_result;
    }
    

    /**
     * Get Filter Params - fliter_params
     */
    function format_search_response($hl, $cobj, $sid, $module = 'b2c', $fltr = array()) {
        // debug($fltr);exit;
        $level_one = true;
        $current_domain = true;
        if ($module == 'b2c') {
            $level_one = false;
            $current_domain = true;
        } else if ($module == 'b2b') {
            $level_one = true;
            $current_domain = true;
        }
        $c_count = 0;
        $CarResults = array ();
        if (isset ( $fltr ['hl'] ) == true) {
            foreach ( $fltr ['hl'] as $tk => $tv ) {
                $fltr ['hl'] [urldecode ( $tk )] = strtolower ( urldecode ( $tv ) );
            }
        }
        if(isset($fltr ['_car_Type']) && valid_array($fltr ['_car_Type'])) {
            $fltr ['_car_Type'] = array_map('strtolower', $fltr ['_car_Type']);
            $fltr ['_car_Type'] = array_map('trim', $fltr ['_car_Type']);
        }
        if(isset($fltr ['_vendor_List']) && valid_array($fltr ['_vendor_List'])) {
            $fltr ['_vendor_List'] = array_map('strtolower', $fltr ['_vendor_List']);
            $fltr ['_vendor_List'] = array_map('trim', $fltr ['_vendor_List']);
        }
        $hc = 0;
        $frc = 0;
        foreach ( $hl ['CarSearchResult'] ['CarResults'] as $cr => $cd ) {
            // debug($cd);exit;
            $hc ++;
            
            $check_filters = function ($cd) use($fltr) {
            //_acc type
            $any_facility = function ($cstr, $c_list)
            {
                foreach ($c_list as $k => $v) {
                    if (in_array($v, $cstr)) {
                        return true;
                    }
                }
            };          
           $ac_var = ($cd['AirConditionInd']=='true')? 'AC': 'Non AC';
            if(
                
                (valid_array(@$fltr['_vehicle_Package']) == false ||
                    (valid_array(@$fltr['_vehicle_Package']) == true && in_array($cd['RateComments'], $fltr['_vehicle_Package']))
                    )&&
                (valid_array($fltr['_door_Count']) == false ||
                    (valid_array($fltr['_door_Count']) == true && in_array(strtolower($cd['DoorCount']), $fltr['_door_Count']))
                    )&&
                (valid_array($fltr['_passenger_Quantity']) == false ||
                    (valid_array($fltr['_passenger_Quantity']) == true && in_array(strtolower($cd['PassengerQuantity']), $fltr['_passenger_Quantity']))
                    )&&
                (valid_array($fltr['_vendor_List']) == false ||
                    (valid_array($fltr['_vendor_List']) == true && in_array(strtolower($cd['Vendor']), $fltr['_vendor_List']))
                    )&&
                (valid_array(@$fltr['_car_Type']) == false ||
                    (valid_array(@$fltr['_car_Type']) == true && in_array(strtolower($cd['Name']), $fltr['_car_Type']))
                    )&&
                (valid_array(@$fltr['_vehicle_Category']) == false ||
                    (valid_array(@$fltr['_vehicle_Category']) == true && in_array(strtolower($cd['VehicleCategory']), $fltr['_vehicle_Category']))
                    )&&
                (valid_array(@$fltr['_vehicle_Size']) == false ||
                    (valid_array(@$fltr['_vehicle_Size']) == true && in_array(strtolower($cd['VehClassSize']), $fltr['_vehicle_Size']))
                    )&&
                (valid_array($fltr['_vehicle_Ac']) == false ||
                    (valid_array($fltr['_vehicle_Ac']) == true && in_array($ac_var, $fltr['_vehicle_Ac']))
                    )&&
                (valid_array($fltr['_vehicle_Manual']) == false ||
                    (valid_array($fltr['_vehicle_Manual']) == true && in_array($cd['TransmissionType'], $fltr['_vehicle_Manual']))
                    )
                
                )
            {
                // echo '1';
                return true;
            }else{
                // echo '2';
                return false;               
            }
        };
            // markup
            $cd ['TotalCharge'] = $this->update_search_markup_currency ( $cd ['TotalCharge'], $cobj, $sid, $level_one, $current_domain );
            $cd ['CancellationPolicy'] = $this->update_cancellation_markup_currency ( $cd ['CancellationPolicy'], $cobj, $sid, $level_one, $current_domain );
            // filter after initializing default data and adding markup
            if (valid_array ( $fltr ) == true && $check_filters ( $cd ) == false) {
                continue;
            }
            $CarResults [$cr] = $cd;
            $frc ++;
            //echo 'count'.$frc;
        }
        // SORTING STARTS
        if (isset ( $fltr ['sort_item'] ) == true && empty ( $fltr ['sort_item'] ) == false && isset ( $fltr ['sort_type'] ) == true && empty ( $fltr ['sort_type'] ) == false) {
            $sort_item = array ();
            foreach ( $CarResults as $key => $row ) {
                if ($fltr ['sort_item'] == 'price') {
                    $sort_item [$key] = floatval ( $row ['Price'] ['RoomPrice'] );
                } else if ($fltr ['sort_item'] == 'star') {
                    $sort_item [$key] = floatval ( $row ['StarRating'] );
                } else if ($fltr ['sort_item'] == 'name') {
                    $sort_item [$key] = trim ( $row ['HotelName'] );
                }
            }
            if ($fltr ['sort_type'] == 'asc') {
                $sort_type = SORT_ASC;
            } else if ($fltr ['sort_type'] == 'desc') {
                $sort_type = SORT_DESC;
            }
            if (valid_array ( $sort_item ) == true && empty ( $sort_type ) == false) {
                array_multisort ( $sort_item, $sort_type, $CarResults );
            }
        } // SORTING ENDS


        $hl ['CarSearchResult'] ['CarResults'] = $CarResults;
        $hl ['source_result_count'] = $hc;
        $hl ['filter_result_count'] = $frc;
        
        return $hl;
    }
    /**
     * Balu A
     * 
     * @param unknown_type $fare_details            
     * @param unknown_type $currency_obj            
     */
    private function preferred_currency_fare_object($fare_details, $currency_obj, $default_currency = '') {
      
        $price_details = array ();
        $fare_details ['CurrencyCode'] = empty ( $default_currency ) == false ? $default_currency : get_application_currency_preference ();
        
        $amount = get_converted_currency_value ( $currency_obj->force_currency_conversion ( $fare_details['EstimatedTotalAmount'] ) );
        $oneway_fee = get_converted_currency_value ( $currency_obj->force_currency_conversion ( $fare_details['OneWayFee'] ) );
        $fare_details ['EstimatedTotalAmount'] = round($amount); 
        $fare_details ['OneWayFee'] = round($oneway_fee); 
        return $fare_details;
    }
     /**
     * Balu A
     * 
     * @param unknown_type $fare_details            
     * @param unknown_type $currency_obj            
     */
    private function preferred_currency_fare_object_cancel($fare_details, $currency_obj, $default_currency = '') {
        $price_details = array();
        foreach($fare_details as $f_key => $f_val){
            $amount = get_converted_currency_value ( $currency_obj->force_currency_conversion ( $f_val['Amount'] ) );
            $f_val ['Amount'] = round($amount);
            $f_val ['CurrencyCode'] = empty ( $default_currency ) == false ? $default_currency : get_application_currency_preference ();
            $price_details[] = $f_val;
        }
        return $price_details;
    }
     /**
     * Balu A
     * 
     * @param unknown_type $fare_details            
     * @param unknown_type $currency_obj            
     */
    private function preferred_currency_fare_object_coverage($fare_details, $currency_obj, $default_currency = '') {
        
        foreach($fare_details as $key => $fare){
            $fare_details [$key]['Currency'] = $currecny = empty ( $default_currency ) == false ? $default_currency : get_application_currency_preference ();
            $amount = get_converted_currency_value ( $currency_obj->force_currency_conversion ( $fare['Amount'] ) );
            $amount = round($amount);

            $description = explode('with excess up to', @$fare['Desscription']);
            
            if(isset($description[1])){
                $desc_amount = explode(' ', $description[1]);
                $fare_amount = get_converted_currency_value ( $currency_obj->force_currency_conversion ( $desc_amount[1] ) );
                $fare_amount = round($fare_amount);
                $description = 'with excess up to '.$fare_amount.' '.$currecny;
            }  
            else{
                $description = $fare['Desscription']; 
            } 
           
            $fare_details [$key]['Desscription'] = $description;                        
            $fare_details [$key]['Amount'] = $amount;
        }
      
        return $fare_details;
        
    }
     /**
     * Balu A
     * 
     * @param unknown_type $fare_details            
     * @param unknown_type $currency_obj            
     */
    private function preferred_currency_fare_object_equip($fare_details, $currency_obj, $default_currency = '') {
        
        foreach($fare_details as $key => $fare){
            $fare_details [$key]['CurrencyCode'] = empty ( $default_currency ) == false ? $default_currency : get_application_currency_preference ();
            $amount = get_converted_currency_value ( $currency_obj->force_currency_conversion ( $fare['Amount'] ) );
            $amount = round($amount);
            $fare_details [$key]['Amount'] = $amount;
        }
        return $fare_details;
        
    }
    /**
     * Markup for search result
     * 
     * @param array $price_summary          
     * @param object $currency_obj          
     * @param number $search_id         
     */
    function update_search_markup_currency(& $price_summary, & $currency_obj, $search_id, $level_one_markup = false, $current_domain_markup = true) {
       
        $multiplier = 1;
        return $this->update_markup_currency ( $price_summary, $currency_obj, $multiplier, $level_one_markup, $current_domain_markup );
    }
      /**
     * Markup for search result
     * 
     * @param array $price_summary          
     * @param object $currency_obj          
     * @param number $search_id         
     */
    function update_cancellation_markup_currency(& $price_summary, & $currency_obj, $search_id, $level_one_markup = false, $current_domain_markup = true) {
       
        $multiplier = 1;
        foreach($price_summary as $p_key => $price){
            $cancellation_poicy[$p_key] = $this->update_cancel_markup_currency ( $price, $currency_obj, $multiplier, $level_one_markup, $current_domain_markup );
        }
        return $cancellation_poicy;
    }
        /**
     * Get Filter Summary of the data list
     *
     * @param array $hl
     */
    function filter_summary($hl) {
        $h_count = 0;
        $filt ['p'] ['max'] = false;
        $filt ['p'] ['min'] = false;
        $filt ['car_type'] = array ();
        
        $filters = array ();
        
        foreach ( $hl ['CarSearchResult'] ['CarResults'] as $hr => $hd ) { 
            // debug($hd);exit;
            if(isset($hd) && valid_array($hd)) {
                // filters

                //debug($hd); exit;
                $car_type = @$hd['VehicleCategory'];
                
                if(isset($car_type) && !empty($car_type)) {
                    if(isset($filt['car_type'][$car_type]) == false) {
                        $filt ['car_type'] [$car_type] ['c'] = 1;
                        $filt ['car_type'] [$car_type] ['v'] = $car_type;
                    }else {
                        $filt ['car_type'] [$car_type] ['c'] ++;
                    }
                }
                
                //Car Package
                $vehicle_package = @$hd['RateComments'];
                if(isset($vehicle_package) && !empty($vehicle_package)) {
                    if(isset($filt['vehicle_package'][$vehicle_package]) == false) {
                        $filt ['vehicle_package'] [$vehicle_package] ['c'] = 1;
                        $filt ['vehicle_package'] [$vehicle_package] ['v'] = $vehicle_package;
                    }else {
                        $filt ['vehicle_package'] [$vehicle_package] ['c'] ++;
                    }
                }
                // debug($filt['vehicle_package']); exit;
                //door count
                $door_count = @$hd['DoorCount'];
                if(isset($door_count) && !empty($door_count)) {
                    if(isset($filt['door_count'][$door_count]) == false) {
                        $filt ['door_count'] [$door_count] ['c'] = 1;
                        $filt ['door_count'] [$door_count] ['v'] = $door_count;
                    }else {
                        $filt ['door_count'] [$door_count] ['c'] ++;
                    }
                }
                
                $passenger_quantity = @$hd['PassengerQuantity'];
                if(isset($passenger_quantity) && !empty($passenger_quantity)) {
                    if(isset($filt['passenger_quantity'][$passenger_quantity]) == false) {
                        $filt ['passenger_quantity'] [$passenger_quantity] ['c'] = 1;
                        $filt ['passenger_quantity'] [$passenger_quantity] ['v'] = $passenger_quantity;
                    }else {
                        $filt ['passenger_quantity'] [$passenger_quantity] ['c'] ++;
                    }
                }
                
                //venter list
                $vendor_list = @$hd['Vendor'];
                if(isset($vendor_list) && !empty($vendor_list)) {
                    if(isset($filt['vendor_list'][$vendor_list]) == false) {
                        $filt ['vendor_list'] [$vendor_list] ['c'] = 1;
                        $filt ['vendor_list'] [$vendor_list] ['v'] = $vendor_list;
                    }else {
                        $filt ['vendor_list'] [$vendor_list] ['c'] ++;
                    }
                }
                //Vehicle Category
                $vehicle_category = @$hd['VehicleCategory'];
                $vehicle_name = '';
                $vehiclecategory = array();
                $category_list = '';
               // $vehicle_category = @$hd['details']['Vehicle']['VehType']['@attributes']['VehicleCategory'];
                if(isset($vehiclecategory) && empty($vehiclecategory)){
                    $vehiclecategory = $GLOBALS ['CI']->car_model->vehiclecategory();
//                  debug($vehiclecategory);
                    $category_list = array_column($vehiclecategory, 'vehiclecategory_name', 'vehiclecategory_id');
//                  debug($category_list); exit;
                }   

                if(isset($vehicle_category) && !empty($vehicle_category)) {
                    if(array_key_exists($vehicle_category,$category_list))
                    {
                        $vehicle_name = $category_list[$vehicle_category];
                    }else{
                        $vehicle_name = "No Name";
                    }   
                    if(isset($filt['vehicle_category'][$vehicle_category]) == false) {
                        $filt ['vehicle_category'] [$vehicle_category] ['c'] = 1;   
                        $filt ['vehicle_category'] [$vehicle_category] ['y'] = $vehicle_name;
                        $filt ['vehicle_category'] [$vehicle_category] ['v'] = $vehicle_category;
                    }else {
                        $filt ['vehicle_category'] [$vehicle_category] ['c'] ++;
                    }
                }
                
                //Vehicle Size
                $vehicle_size = @$hd['VehClassSize'];
                $vehicle_size_name = '';
                $vehiclesize = array();
                $size_list = '';
                //$vehicle_size = @$hd['details']['Vehicle']['VehType']['@attributes']['VehicleCategory'];
                if(isset($vehiclesize) && empty($vehiclesize)){
                    $vehiclesize = $GLOBALS ['CI']->car_model->vehiclesize();
                    $size_list = array_column($vehiclesize, 'vehiclesize_name', 'vehiclesize_id');
                }   

                if(isset($size_list) && !empty($size_list)) {
                    if(array_key_exists($vehicle_size,$size_list))
                    {
                        $vehicle_size_name = $size_list[$vehicle_size];
                    }else{
                        $vehicle_size_name = "Others";
                    }   
                    if(isset($filt['vehicle_size'][$vehicle_size]) == false) {
                        $filt ['vehicle_size'] [$vehicle_size] ['c'] = 1;   
                        $filt ['vehicle_size'] [$vehicle_size] ['y'] = $vehicle_size_name;
                        $filt ['vehicle_size'] [$vehicle_size] ['v'] = $vehicle_size;
                    }else {
                        $filt ['vehicle_size'] [$vehicle_size] ['c'] ++;
                    }
                }
                
                // for package type
                //debug($hd['details']); exit;
                $coverage = @$hd['PricedCoverage'];
                // debug($coverage); exit;
                foreach($coverage as $coverage_k => $coverage_v){
                    $coverage_type = $coverage_v['CoverageType'];
                    $coverage_code = $coverage_v['Code'];
                    if($coverage_code == 'CDW' || $coverage_code == 'TP' || $coverage_code == 'PAI'){
                        if(isset($coverage_type) && !empty($coverage_type)) {
                            if(isset($filt['coverage_type'][$coverage_type]) == false) {
                                $filt ['coverage_type'] [$coverage_type] ['c'] = 1;
                                $filt ['coverage_type'] [$coverage_type] ['v'] = $coverage_type;
                            }else {
                                $filt ['coverage_type'] [$coverage_type] ['c'] ++;
                            }
                        }
                    }
                    
                } 
                //exit;
                
                if(isset($package_type_list) && !empty($package_type_list)) {
                    if(isset($filt['package_type_list'][$package_type_list]) == false) {
                        
                        $filt ['package_type_list'] [$package_type_list] ['c'] = 1;
                        $filt ['package_type_list'] [$package_type_list] ['v'] = $package_type_list;
                    }else {
                        $filt ['package_type_list'] [$package_type_list] ['c'] ++;
                    }
                }



                // for ac/non ac vehicle
                $vehicle_ac = @$hd['AirConditionInd'];
                // var_dump($vehicle_ac); exit();
                $vehicle_ac = ($vehicle_ac=='true')? 'AC' : 'Non AC';
                if(isset($vehicle_ac) && !empty($vehicle_ac)) {
                    if(isset($filt['vehicle_ac'][$vehicle_ac]) == false) {
                        $filt ['vehicle_ac'] [$vehicle_ac] ['c'] = 1;
                        $filt ['vehicle_ac'] [$vehicle_ac] ['v'] = $vehicle_ac;
                    }else {
                        $filt ['vehicle_ac'] [$vehicle_ac] ['c'] ++;
                    }
                }

                // for auto/manual vehicle
                $vehicle_manual = @$hd['TransmissionType'];
                if(isset($vehicle_manual) && !empty($vehicle_manual)) {
                    if(isset($filt['vehicle_manual'][$vehicle_manual]) == false) {
                        $filt ['vehicle_manual'] [$vehicle_manual] ['c'] = 1;
                        $filt ['vehicle_manual'] [$vehicle_manual] ['v'] = $vehicle_manual;
                    }else {
                        $filt ['vehicle_manual'] [$vehicle_manual] ['c'] ++;
                    }
                }
                
                //debug($hd['details']['RateComments']); exit;
                //$amount2 = isset($hd['details']['TotalCharge']['EstimatedTotalAmount']) ? $hd['details']['TotalCharge']['EstimatedTotalAmount'] : 0;

                // $total_price = @$hd['details']['total_amount'];
                /*Bishnu*/
                $total_price = @$hd['total_amount'];
                // debug($total_price); exit;
                if (($filt ['p'] ['max'] != false && $filt ['p'] ['max'] < $total_price) || $filt ['p'] ['max'] == false) {
                    //$filt ['p'] ['max'] = roundoff_number ( $total_price );
                    $filt ['p'] ['max'] = $total_price;
                }
                /*if (($filt ['p'] ['min'] != false && $filt ['p'] ['min'] > $hd ['price']) || $filt ['p'] ['min'] == false) {
                 $filt ['p'] ['min'] = roundoff_number ( $hd ['price'] ['RoomPrice'] );
                }*/

                if (($filt ['p'] ['min'] != false && $filt ['p'] ['min'] > $total_price) || $filt ['p'] ['min'] == false) {
                    $filt ['p'] ['min'] = $total_price  ;
                }
                
                $filters ['data'] = $filt;
                $h_count ++;
            }
            
        }
        // debug($filters);exit;
        //array_unique($filters['data']['door_count']);
        //array_unique($filters['data']['vendor_list']);
        ksort ( $filters ['data'] ['car_type'] );
        $filters ['car_count'] = $h_count;
        // debug($filters); exit;
        return $filters;
    }
    /**
	 * Anitha G
	 * Load Car Details
	 *
	 * @return array having status of the operation and resulting data in case if operaiton is successfull
	 */
	function get_rate_rules($ResultIndex) {
		$header = $this->get_header ();
		$response ['data'] = array ();
		$response ['status'] = false;
		$car_rule_request = $this->car_rule_request ($ResultIndex);
		if ($car_rule_request ['status']) {
			// debug($car_rule_request);exit;
			$car_rule_response = $GLOBALS ['CI']->api_interface->get_json_response ( $car_rule_request ['data'] ['service_url'], $car_rule_request ['data'] ['request'], $header );
			$GLOBALS ['CI']->custom_db->generate_static_response ( json_encode ( $car_rule_response ) );
			// debug($car_rule_response);exit;
			if ($this->valid_car_rules ( $car_rule_response )) {
				$response ['data'] = $car_rule_response;
				$response ['status'] = true;
			}
			else {
				$response ['data'] = $car_rule_response;
			}
		}
		return $response;
	}
	/**
	 * Anitha G
	 * check if the Car Rule response which is received from server is valid or not
	 * 
	 * @param
	 *  $hotel_details
	 */
	private function valid_car_rules($car_rules) {
		$status = false;
		if (valid_array ( $car_rules ) == true and isset ( $car_rules ['Status'] ) == true and $car_rules ['Status']  == SUCCESS_STATUS) {
			$status = true;
		}
		return $status;
	}
	/**
	 * Anitha G
	 *
	 * Car Rules Request
	 * 
	 * @param string $TraceId        	
	 * @param string $ResultIndex        	
	 *    	
	 */
	private function car_rule_request($ResultToken) {
		$response ['status'] = true;
		$response ['data'] = array ();
		$request ['ResultToken'] = $ResultToken;
		$response ['data'] ['request'] = json_encode ( $request );
		$this->credentials ( 'CarRule' );
		$response ['data'] ['service_url'] = $this->service_url;
		return $response;
	}
     /**
     * Balu A
     * Converts Display currency to application currency
     * @param unknown_type $fare_details
     * @param unknown_type $currency_obj
     * @param unknown_type $module
     */
    public function convert_token_to_application_currency($token, $currency_obj, $module) {
        // debug($token);exit;
       
        $token['TotalCharge']['EstimatedTotalAmount'] = get_converted_currency_value($currency_obj->force_currency_conversion($token['TotalCharge']['EstimatedTotalAmount']));
        // debug($token);exit;
        $master_token = array();
        $seat_attr = array();
        $seats = array();
        //Converting to application Currency
        $temp_seat_attr = $token['seat_attr'];
        $seat_attr['markup_price_summary'] = get_converted_currency_value($currency_obj->force_currency_conversion($temp_seat_attr['markup_price_summary']));
        $seat_attr['total_price_summary'] = get_converted_currency_value($currency_obj->force_currency_conversion($temp_seat_attr['total_price_summary']));
        $seat_attr['domain_deduction_fare'] = get_converted_currency_value($currency_obj->force_currency_conversion($temp_seat_attr['domain_deduction_fare']));
        $seat_attr['default_currency'] = admin_base_currency();
        //Seats
        foreach ($temp_seat_attr['seats'] as $sk => $sv) {
            $seats[$sk] = $sv;
            $seats[$sk]['Fare'] = get_converted_currency_value($currency_obj->force_currency_conversion($sv['Fare']));
            $seats[$sk]['Markup_Fare'] = get_converted_currency_value($currency_obj->force_currency_conversion($sv['Markup_Fare']));
        }
        $seat_attr['seats'] = $seats;
        //Assigning the Converted Values
        $master_token = $token;
        $master_token['seat_attr'] = $seat_attr;
        return $master_token;
    }
    /**
     * Break data into pages
     * 
     * @param
     *          $data
     * @param
     *          $offset
     * @param
     *          $limit
     */
    function get_page_data($hl, $offset, $limit) {
        $hl ['CarSearchResult'] ['CarResults'] = array_slice ( $hl ['CarSearchResult'] ['CarResults'], $offset, $limit );
        return $hl;
    }
    
    /**
     * check the response is valid or not
     * @param array $api_response  response to be validated
     */
    function valid_api_response($api_response) {
        if (empty($api_response) == false && valid_array($api_response) == true and isset($api_response['Status']) == true and $api_response['Status'] == SUCCESS_STATUS) {
            return true;
        } else {
            return false;
        }
    }
}
