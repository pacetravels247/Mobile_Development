<?php
/** 
 *
 * Formates the Booking Data in the application
 *
 * @package	Provab
 * @subpackage	provab
 * @category	Libraries
 * @author		Balu A<balu.provab@gmail.com>
 * @link		http://www.provab.com
 */
class Booking_Data_Formatter {
	public function __construct()
	{
		error_reporting(0);
	}
	/**
	 * Fomat Bus Data
	 * Balu A
	 * @param array $booking_details
	 */
	function format_bus_booking_data($complete_booking_details, $module)
	{
		//debug($complete_booking_details); die;
		$response['status']	= SUCCESS_STATUS;
		$response['data']	= array();
		$booking_details = array();
		$currency_obj = new Currency();
		$master_booking_details = $complete_booking_details['data']['booking_details'];
		$itinerary_details = $this->format_itinerary_details($complete_booking_details['data']['booking_itinerary_details']);		
		$payment_details = @$this->format_payment_details($complete_booking_details['data']['payment_details']);
		$customer_details = $this->format_customer_details($complete_booking_details['data']['booking_customer_details']);
		$tl_details = $this->format_tl_details($complete_booking_details['data']['tl_details']);
		$booking_payment_details = array();
		//echo debug($customer_details);exit;
		foreach($master_booking_details as $book_k => $book_v) {
			
			$core_booking_details = $book_v;
			$app_reference = $core_booking_details['app_reference'];
			$booking_itinerary_details = $itinerary_details[$app_reference];
			$booking_customer_details = $customer_details[$app_reference];
			$booking_payment_details = @$payment_details[$app_reference];
			if($module == 'b2b' || $module == 'b2c' ){
				//get the converted currency rate for booking transaction
				$booking_customer_details = $this->display_currency_for_bus($book_v,$booking_customer_details);
				//get the converted currency rate for convenience amount
				$core_booking_details = $this->display_convenience_amount($book_v);
			}
			
			//Calculating Price
			$fare = 0;
			$admin_commission = 0;
			$agent_commission = 0;
			$admin_tds = 0;
			$agent_tds = 0;
			$admin_markup = 0;
			$agent_markup = 0;
			$seat_numbers = '';
			$gst = 0;
			$total_api_base_fare = 0;
			$total_api_tax = 0;
			foreach($booking_customer_details as $customer_k => $customer_v) {
				$fare 				+= floatval($customer_v['fare']);
				$admin_commission 	+= floatval($customer_v['admin_commission']);
				$agent_commission 	+= floatval($customer_v['agent_commission']);
				$admin_tds 			+= floatval($customer_v['admin_tds']);
				$agent_tds 			+= floatval($customer_v['agent_tds']);
				$admin_markup 		+= floatval($customer_v['admin_markup']);
				$agent_markup 		+= floatval($customer_v['agent_markup']);
				$seat_numbers .= trim($customer_v['seat_no']).',';
				$ea = json_decode($customer_v['attr']);
				//debug($ea); exit;
				$total_api_base_fare += $ea->base_fare; 
				$total_api_tax += $ea->_ServiceTax;
			}
			$gst = $core_booking_details['gst'];
			if($module == 'admin' ){
				$core_booking_details['fare'] = roundoff_number($fare);
			}
			else if($module == 'b2b'){
				$core_booking_details['fare'] = roundoff_number($fare);
			}
			else{
				$core_booking_details['fare'] = roundoff_number($fare);
			}
			// echo "dsfsdfgd";
			
			$core_booking_details['admin_commission'] = roundoff_number($admin_commission);
			$core_booking_details['agent_commission'] = roundoff_number($agent_commission);
			$core_booking_details['admin_tds'] = roundoff_number($admin_tds);
			$core_booking_details['agent_tds'] = roundoff_number($agent_tds);
			$core_booking_details['admin_markup'] = roundoff_number($admin_markup);
			$core_booking_details['agent_markup'] = roundoff_number($agent_markup);
			$admin_buying_price = $this->admin_buying_price($core_booking_details);
			$core_booking_details['gst'] = roundoff_number($gst);
			$core_booking_details['admin_buying_price'] = roundoff_number($admin_buying_price[0]);
			$core_booking_details['total_api_base_fare'] = roundoff_number($total_api_base_fare);
			$core_booking_details['total_api_tax'] = roundoff_number($total_api_tax);
		
			if($module == 'b2b') {
				$agent_buying_price = $this->agent_buying_price($core_booking_details);
				$core_booking_details['agent_buying_price'] = roundoff_number($agent_buying_price[0]);
			} else {
				$core_booking_details['agent_buying_price'] = 0;
			}
			// $core_booking_details['fare'] = $core_booking_details['fare']+$core_booking_details['admin_commission']-$core_booking_details['admin_tds'];
			// debug($core_booking_details);exit;
			$grand_total = $this->total_fare($core_booking_details, $module);
			// debug($grand_total);exit;
			$core_booking_details['grand_total'] = $grand_total[0];
			// $core_booking_details['fare'] = $fare;
			//currency
			$currency = admin_base_currency();
			if($module == 'b2b' || $module == 'b2c' ){
				$currency = $book_v['currency'];			
			}
			$core_booking_details['currency'] = $currency_obj->get_currency_symbol($currency);			
			$core_booking_details['departure_from'] = $booking_itinerary_details[0]['departure_from'];
			
			$core_booking_details['arrival_to'] = $booking_itinerary_details[0]['arrival_to'];
			$core_booking_details['journey_datetime'] = $booking_itinerary_details[0]['journey_datetime'];
			$core_booking_details['departure_datetime'] = $booking_itinerary_details[0]['departure_datetime'];
			$core_booking_details['arrival_datetime'] = $booking_itinerary_details[0]['arrival_datetime'];
			$core_booking_details['pax_count'] = count($booking_customer_details);
			$core_booking_details['operator'] = $booking_itinerary_details[0]['operator'];
			$core_booking_details['bus_type'] = $booking_itinerary_details[0]['bus_type'];
			$core_booking_details['seat_numbers'] = rtrim($seat_numbers, ',');
			//Lead Pax Details
			$core_booking_details['lead_pax_name'] = $booking_customer_details[0]['name'];
			$core_booking_details['lead_pax_phone_number'] = $core_booking_details['phone_number'];
			$core_booking_details['lead_pax_email'] = $core_booking_details['email']; 
			$core_booking_details['opening_balance'] = $tl_details[$app_reference][0]["opening_balance"];
			$core_booking_details['closing_balance'] = $tl_details[$app_reference][0]["closing_balance"];
			//Domain Details
			$domain_details = $this->domain_details($core_booking_details['domain_origin']);
			$core_booking_details['domain_name'] = $domain_details['domain_name'];
			$core_booking_details['domain_ip'] = $domain_details['domain_ip'];
			$core_booking_details['domain_key'] = $domain_details['domain_key'];
			$core_booking_details['theme_id'] = $domain_details['theme_id'];
			$core_booking_details['domain_logo'] = $domain_details['domain_logo'];
			$core_booking_details['booked_date'] = app_friendly_absolute_date($core_booking_details['created_datetime']);
			//Formating the data
			//Booking Details	
			$booking_details[$app_reference] = $core_booking_details;
			
			//Itinerary Details
			$booking_details[$app_reference]['booking_itinerary_details'] = $booking_itinerary_details;
			
			//Customer Details
			$booking_details[$app_reference]['booking_customer_details'] = $booking_customer_details;

			//Payment Details
			$booking_details[$app_reference]['booking_payment_details'] = $booking_payment_details;
			
		}
		$booking_details = $this->convert_as_array($booking_details);
		// debug($booking_details);die;
		$response['data']['booking_details'] = $booking_details;
		return $response;
	}
	/**Sagar Wakchaure
	 * 
	 * @param array $currency_rate
	 * @param unknown $booking_customer_details
	 * @return number
	 */
	function display_currency_for_bus($currency_rate = array(),$booking_customer_details = array()){
		//echo debug($booking_customer_details);exit;
				$conversion_rate = $currency_rate['currency_conversion_rate'];
				foreach($booking_customer_details as $key => $sub_booking_customer_details){
					$booking_customer_details[$key]['fare'] =  $this->add_currencyrate($sub_booking_customer_details['fare'],$conversion_rate);
					$booking_customer_details[$key]['admin_commission'] =  $this->add_currencyrate($sub_booking_customer_details['admin_commission'],$conversion_rate);
					$booking_customer_details[$key]['agent_commission'] =  $this->add_currencyrate($sub_booking_customer_details['agent_commission'],$conversion_rate);
					$booking_customer_details[$key]['admin_tds'] =  $this->add_currencyrate($sub_booking_customer_details['admin_tds'],$conversion_rate);
					$booking_customer_details[$key]['agent_tds'] =  $this->add_currencyrate($sub_booking_customer_details['agent_tds'],$conversion_rate);
					// $booking_customer_details[$key]['gst'] =  $this->add_currencyrate($sub_booking_customer_details['gst'],$conversion_rate);
					$booking_customer_details[$key]['admin_markup'] =  $this->add_currencyrate($sub_booking_customer_details['admin_markup'],$conversion_rate);
					$booking_customer_details[$key]['agent_markup'] =  $this->add_currencyrate($sub_booking_customer_details['agent_markup'],$conversion_rate);
				}
				return $booking_customer_details;
	}
	
	/**
	 * Balu A
	 * @param array $booking_details
	 */
	function format_hotel_booking_data($complete_booking_details, $module)
	{
		$response['status']	= SUCCESS_STATUS;
		$response['data']	= array();
		$booking_details = array();
		$currency_obj = new Currency();
		$master_booking_details = $complete_booking_details['data']['booking_details'];

	
		$itinerary_details = $this->format_itinerary_details($complete_booking_details['data']['booking_itinerary_details']);
	    //echo debug($itinerary_details);exit;
		$payment_details = @$this->format_payment_details($complete_booking_details['data']['payment_details']);
		$customer_details = $this->format_customer_details($complete_booking_details['data']['booking_customer_details']);
		if(isset($complete_booking_details['data']['cancellation_details'])){
			$cancellation_details = $this->format_hotel_cancellation_details($complete_booking_details['data']['cancellation_details']);	
		}
		$tl_details = $this->format_tl_details($complete_booking_details['data']['tl_details']);
		$booking_payment_details = array();
		foreach($master_booking_details as $book_k => $book_v) {
			$core_booking_details = $book_v;
			
			$app_reference = $core_booking_details['app_reference'];
			$booking_itinerary_details = $itinerary_details[$app_reference];
			$booking_payment_details = @$payment_details[$app_reference];
			$booking_customer_details = $customer_details[$app_reference];
			$booking_cancellation_details = @$cancellation_details[$app_reference];		
			if($module == 'b2b' || $module == 'b2c' ){
				//get the converted currency rate for booking transaction
				$booking_itinerary_details = $this->display_currency_for_hotel($book_v,$itinerary_details[$app_reference]);
				//get the converted currency rate for convenience amount
				$core_booking_details = $this->display_convenience_amount($book_v);
			}
			//Calculating Price
			$fare = 0;
			$admin_markup = 0;
			$agent_markup = 0;
			$gst = 0;
			foreach($booking_itinerary_details as $itinerary_k => $itinerary_v) {
				$fare += $itinerary_v['total_fare'];
				$admin_markup += floatval($itinerary_v['admin_markup']);
				$agent_markup += floatval($itinerary_v['agent_markup']);
				$gst 		  += floatval($itinerary_v['gst']);
			}
			//PaxCount
			$adult_count = 0;
			$child_count = 0;
			foreach($booking_customer_details as $customer_k => $customer_v) {
				$pax_type = $customer_v['pax_type'];
				if($pax_type == 'Adult') {
					$adult_count++;
				} else if($pax_type == 'Child'){
					$child_count++;
				}
			}
			//Formatiing the data
			//Booking Details
			$attributes = json_decode($core_booking_details['attributes'], true);
			$total_nights = get_date_difference($core_booking_details['hotel_check_in'], $core_booking_details['hotel_check_out']);
			$core_booking_details['hotel_image'] = $attributes['HotelImage'];
			$core_booking_details['hotel_location'] = $booking_itinerary_details[0]['location'];
			$core_booking_details['hotel_address'] = $attributes['HotelAddress'];
			$core_booking_details['cancellation_policy'] = $attributes['CancellationPolicy'];
			$core_booking_details['total_nights'] = $total_nights;
			$core_booking_details['total_rooms'] = count($booking_itinerary_details);
			$core_booking_details['adult_count'] = $adult_count;
			$core_booking_details['child_count'] = $child_count;
			$core_booking_details['fare'] = roundoff_number($fare);
			$core_booking_details['admin_markup'] = roundoff_number($admin_markup);
			$core_booking_details['agent_markup'] = roundoff_number($agent_markup);
			$core_booking_details['gst'] = roundoff_number($gst);
			$admin_buying_price = $this->admin_buying_price($core_booking_details);
			$core_booking_details['admin_buying_price'] = roundoff_number($admin_buying_price[0]);
			$core_booking_details['convinence_amount'] = $core_booking_details['convinence_amount'];
			// debug($core_booking_details);exit;
			if($module == 'b2b') {
				$agent_buying_price = $this->agent_buying_price($core_booking_details);
				$core_booking_details['agent_buying_price'] = roundoff_number($agent_buying_price[0]);
			} else {
				$core_booking_details['agent_buying_price'] = 0;
			}
			

			$grand_total = $this->total_fare($core_booking_details, $module);
			$core_booking_details['grand_total'] = roundoff_number($grand_total[0]);
			// debug($core_booking_details);exit;
			//get currency
			$currency = admin_base_currency();
			if($module == 'b2b' || $module == 'b2c' ){
			    $currency = $book_v['currency'];
			}
			$core_booking_details['currency'] = $currency_obj->get_currency_symbol($currency);
			
			$core_booking_details['voucher_date'] = app_friendly_absolute_date($core_booking_details['created_datetime']);
			//Lead Pax Details
			$core_booking_details['cutomer_city'] = $attributes['billing_city'];
			$core_booking_details['cutomer_zipcode'] = $attributes['billing_zipcode'];
			$core_booking_details['cutomer_address'] = $attributes['address'];
			$core_booking_details['cutomer_country'] = $attributes['billing_country'];
			$core_booking_details['lead_pax_name'] = $booking_customer_details[0]['title'].' '.$booking_customer_details[0]['first_name'].' '.$booking_customer_details[0]['last_name'];
			$core_booking_details['lead_pax_phone_number'] = $core_booking_details['phone_number'];
			$core_booking_details['lead_pax_email'] = $core_booking_details['email']; 
			//Domain Details
			$domain_details = $this->domain_details($core_booking_details['domain_origin']);
			$core_booking_details['domain_name'] = $domain_details['domain_name'];
			$core_booking_details['domain_ip'] = $domain_details['domain_ip'];
			$core_booking_details['domain_key'] = $domain_details['domain_key'];
			$core_booking_details['theme_id'] = $domain_details['theme_id'];
			$core_booking_details['domain_logo'] = $domain_details['domain_logo'];
			$core_booking_details['opening_balance'] = $tl_details[$app_reference][0]["opening_balance"];
			$core_booking_details['closing_balance'] = $tl_details[$app_reference][0]["closing_balance"];
			//Formating the data
			//Booking Details
			$booking_details[$app_reference] = $core_booking_details;
			//Itenary Details
			$booking_details[$app_reference]['itinerary_details'] = $booking_itinerary_details;
			//Customer Details
			$booking_details[$app_reference]['customer_details'] = $booking_customer_details;
			$booking_details[$app_reference]['cancellation_details'] = $booking_cancellation_details;
			$booking_details[$app_reference]['payment_details'] = $booking_payment_details;
			
		}
	
		$booking_details = $this->convert_as_array($booking_details);
		$response['data']['booking_details'] = $booking_details;
		return $response;
	}

	/**
	 * Convert price details  into respective currency rate for hotel
	 * @param array $currency_rate
	 * @param unknown $itinerary_details
	 * @return number
	 */
	function display_currency_for_hotel($currency_rate = array(),$itinerary_details){
		$conversion_rate = $currency_rate['currency_conversion_rate'];
		foreach($itinerary_details as $key => $sub_itinerary_details){
			$itinerary_details[$key]['total_fare'] =  $this->add_currencyrate($sub_itinerary_details['total_fare'],$conversion_rate);
			$itinerary_details[$key]['admin_markup'] =  $this->add_currencyrate($sub_itinerary_details['admin_markup'],$conversion_rate);
			$itinerary_details[$key]['agent_markup'] =  $this->add_currencyrate($sub_itinerary_details['agent_markup'],$conversion_rate);
			$itinerary_details[$key]['RoomPrice'] =  $this->add_currencyrate($sub_itinerary_details['RoomPrice'],$conversion_rate);
			$itinerary_details[$key]['gst'] =  $this->add_currencyrate($sub_itinerary_details['gst'],$conversion_rate);
			$itinerary_details[$key]['Discount'] =  $this->add_currencyrate($sub_itinerary_details['Discount'],$conversion_rate);
		}
		return $itinerary_details;
	}

	/**
	 * Convert price details  into respective currency rate for sightseeing
	 * @param array $currency_rate
	 * @param unknown $itinerary_details
	 * @return number
	 */
	function display_currency_for_sightseeing($currency_rate = array(),$itinerary_details){
		$conversion_rate = $currency_rate['currency_conversion_rate'];

		foreach($itinerary_details as $key => $sub_itinerary_details){
			// $itinerary_details[$key]['total_fare'] =  $this->add_currencyrate($sub_itinerary_details['total_fare'],$conversion_rate);
			// $itinerary_details[$key]['admin_markup'] =  $this->add_currencyrate($sub_itinerary_details['admin_markup'],$conversion_rate);
			// $itinerary_details[$key]['agent_markup'] =  $this->add_currencyrate($sub_itinerary_details['agent_markup'],$conversion_rate);
		
			// $itinerary_details[$key]['Discount'] =  $this->add_currencyrate($sub_itinerary_details['Discount'],$conversion_rate);
			$itinerary_details[$key]['total_fare'] =  $this->add_currencyrate($sub_itinerary_details['total_fare'],$conversion_rate);
			$itinerary_details[$key]['admin_commission'] =  $this->add_currencyrate($sub_itinerary_details['admin_commission'],$conversion_rate);
			$itinerary_details[$key]['agent_commission'] =  $this->add_currencyrate($sub_itinerary_details['agent_commission'],$conversion_rate);
			$itinerary_details[$key]['admin_tds'] =  $this->add_currencyrate($sub_itinerary_details['admin_tds'],$conversion_rate);
			$itinerary_details[$key]['agent_tds'] =  $this->add_currencyrate($sub_itinerary_details['agent_tds'],$conversion_rate);
			$itinerary_details[$key]['admin_markup'] =  $this->add_currencyrate($sub_itinerary_details['admin_markup'],$conversion_rate);
			$itinerary_details[$key]['gst'] =  $this->add_currencyrate($sub_itinerary_details['gst'],$conversion_rate);
			$itinerary_details[$key]['agent_markup'] =  $this->add_currencyrate($sub_itinerary_details['agent_markup'],$conversion_rate);

		}
		return $itinerary_details;
	}

	
	/**
	 * Elavarasi
	 * @param array $booking_details
	 */
	function format_sightseeing_booking_data($complete_booking_details, $module)
	{
		$response['status']	= SUCCESS_STATUS;
		$response['data']	= array();
		$booking_details = array();
		$currency_obj = new Currency();
		$master_booking_details = $complete_booking_details['data']['booking_details'];
	   //echo debug($master_booking_details);exit;
		$itinerary_details = $this->format_itinerary_details($complete_booking_details['data']['booking_itinerary_details']);
	    //echo debug($itinerary_details);exit;
		$payment_details = @$this->format_payment_details($complete_booking_details['data']['payment_details']);
		$customer_details = $this->format_customer_details($complete_booking_details['data']['booking_customer_details']);
		$cancellation_details = $this->format_sightseeing_cancellation_details($complete_booking_details['data']['cancellation_details']);
		$booking_payment_details = array();
		foreach($master_booking_details as $book_k => $book_v) {
			$core_booking_details = $book_v;
			// debug($book_v);
			// exit;
			$app_reference = $core_booking_details['app_reference'];
			$booking_itinerary_details = $itinerary_details[$app_reference];
			$booking_customer_details = $customer_details[$app_reference];
			$booking_cancellation_details = @$cancellation_details[$app_reference];	
			$booking_payment_details = @$payment_details[$app_reference];	
			if($module == 'b2b' || $module == 'b2c' ){
				//get the converted currency rate for booking transaction
				$booking_itinerary_details = $this->display_currency_for_sightseeing($book_v,$itinerary_details[$app_reference]);
				//get the converted currency rate for convenience amount
				$core_booking_details = $this->display_convenience_amount($book_v);
			}

			// debug($core_booking_details);
			// exit;
			//Calculating Price
			$fare = 0;
			$admin_markup = 0;
			$agent_markup = 0;
			$agent_commission = 0;
			$admin_commission = 0;
			$admin_tds = 0;
			$agent_tds = 0;
			$admin_net_fare_markup = 0;
			$gst = 0;
			foreach($booking_itinerary_details as $itinerary_k => $itinerary_v) {

				$fare += $itinerary_v['total_fare'];
				$admin_markup += floatval($itinerary_v['admin_markup']);
				$agent_markup += floatval($itinerary_v['agent_markup']);
				$agent_commission +=floatval($itinerary_v['agent_commission']);
				$admin_commission +=floatval($itinerary_v['admin_commission']);
				$admin_tds +=floatval($itinerary_v['admin_tds']);
				$agent_tds +=floatval($itinerary_v['agent_tds']);
				$gst += floatval($itinerary_v['gst']);
				//$admin_net_fare_markup +=floatval($itinerary_v['admin_net_markup']);

			}
			//PaxCount
			$adult_count = 0;
			$child_count = 0;
			$infant_count =0;
			$youth_count = 0;
			$senior_count = 0;
			foreach($booking_customer_details as $customer_k => $customer_v) {
				$pax_type = $customer_v['pax_type'];
				if($pax_type == 'Adult') {
					$adult_count++;
				} else if($pax_type == 'Child'){
					$child_count++;
				}
				elseif($pax_type=='Infant'){
					$infant_count++;
				}
				elseif($pax_type=='Senior'){
					$senior_count++;
				}
				elseif ($pax_type=='Youth') {
					$youth_count++;
				}
			}
			//Formatiing the data
			//Booking Details
			$attributes = json_decode($core_booking_details['attributes'], true);
			
			$core_booking_details['ProductImage'] = $attributes['ProductImage'];
			$core_booking_details['Destination'] = $attributes['Destination'];
			$core_booking_details['DeparturePointAddress'] = $attributes['DeparturePointAddress'];
			$core_booking_details['TM_Cancellation_Policy'] = $attributes['TM_Cancellation_Policy'];
			
			$core_booking_details['adult_count'] = $adult_count;
			$core_booking_details['child_count'] = $child_count;
			$core_booking_details['youth_count'] = $youth_count;
			$core_booking_details['senior_count'] = $senior_count;
			$core_booking_details['infant_count'] = $infant_count;

			$core_booking_details['fare'] = roundoff_number($fare);
			$core_booking_details['admin_markup'] = roundoff_number($admin_markup);
			$core_booking_details['gst'] = roundoff_number($gst);
			//$core_booking_details['admin_net_markup'] =roundoff_number($admin_net_fare_markup);
			$core_booking_details['agent_markup'] = roundoff_number($agent_markup);
			$core_booking_details['admin_commission'] = roundoff_number($admin_commission);
			$core_booking_details['agent_commission'] = roundoff_number($agent_commission);
			$core_booking_details['admin_tds'] = roundoff_number($admin_tds);
			$core_booking_details['agent_tds'] = roundoff_number($agent_tds);
			$net_commission = ($admin_commission+$agent_commission);
			$net_commission_tds = ($admin_tds+$agent_tds);
			$core_booking_details['net_commission'] = roundoff_number($net_commission);
			$core_booking_details['net_commission_tds'] = roundoff_number($net_commission_tds);
			$core_booking_details['net_fare'] = roundoff_number($fare-$net_commission);

			$core_booking_details['admin_net_fare'] = roundoff_number($fare-$admin_commission+$admin_tds);
			// debug($core_booking_details);
			// exit;

			$core_booking_details['product_total_price'] =roundoff_number($core_booking_details['admin_net_fare']+$admin_markup +$agent_markup);//without convience fee

			$admin_buying_price = $this->admin_buying_price($core_booking_details);
			$core_booking_details['admin_buying_price'] = roundoff_number($admin_buying_price[0]);	
			
			// debug($core_booking_details);
			// exit;
			if($module == 'b2b' || $module=='admin') {

				$agent_buying_price = $this->agent_buying_price($core_booking_details);
				$core_booking_details['agent_buying_price'] = roundoff_number($agent_buying_price[0]);
			} else {
				//$core_booking_details['fare']=$core_booking_details['admin_net_fare'];
				$core_booking_details['agent_buying_price'] = 0;
			}
			//debug($core_booking_details);
			$grand_total = $this->total_fare($core_booking_details, $module);
			// debug($grand_total);
			// exit;
			$core_booking_details['grand_total'] = roundoff_number($grand_total[0]);
			
			//get currency
			$currency = admin_base_currency();
			if($module == 'b2b' || $module == 'b2c' ){
			    $currency = $book_v['currency'];
			}
			$core_booking_details['currency'] = $currency_obj->get_currency_symbol($currency);
			
			$core_booking_details['voucher_date'] = app_friendly_absolute_date($core_booking_details['created_datetime']);
			//Lead Pax Details
			$core_booking_details['cutomer_city'] = $attributes['billing_city'];
			$core_booking_details['cutomer_zipcode'] = $attributes['billing_zipcode'];
			$core_booking_details['cutomer_address'] = $attributes['address'];
			$core_booking_details['cutomer_country'] = $attributes['billing_country'];
			$core_booking_details['destination_name'] = $attributes['Destination'];
			$core_booking_details['lead_pax_name'] = $booking_customer_details[0]['first_name'].' '.$booking_customer_details[0]['last_name'];
			$core_booking_details['lead_pax_phone_number'] = $core_booking_details['phone_number'];
			$core_booking_details['lead_pax_email'] = $core_booking_details['email']; 
			//Domain Details
			$domain_details = $this->domain_details($core_booking_details['domain_origin']);
			$core_booking_details['domain_name'] = $domain_details['domain_name'];
			$core_booking_details['domain_ip'] = $domain_details['domain_ip'];
			$core_booking_details['domain_key'] = $domain_details['domain_key'];
			$core_booking_details['theme_id'] = $domain_details['theme_id'];
			$core_booking_details['domain_logo'] = $domain_details['domain_logo'];
			//Formating the data
			//Booking Details
			$booking_details[$app_reference] = $core_booking_details;
			//Itenary Details
			$booking_details[$app_reference]['itinerary_details'] = $booking_itinerary_details;
			//Customer Details
			$booking_details[$app_reference]['customer_details'] = $booking_customer_details;
			$booking_details[$app_reference]['cancellation_details'] = $booking_cancellation_details;
			//Payment Details
			$booking_details[$app_reference]['booking_payment_details'] = $booking_payment_details;
		}
		// debug($booking_details);
		// exit;
		$booking_details = $this->convert_as_array($booking_details);
		$response['data']['booking_details'] = $booking_details;
		return $response;
	}



	/**
	 * Balu A
	 * @param array $booking_details
	 */
	function format_flight_booking_data($complete_booking_details, $module, $convert_currency = true)
	{
		
		$response['status']	= SUCCESS_STATUS;
		$response['data']	= array();
		$application_default_currency = admin_base_currency();
		//$currency_obj = new Currency ( array ('module_type' => 'flight','from' => admin_base_currency (),'to' => $all_post['currency']));
		$booking_details = array();
		$currency_obj = new Currency();
		$master_booking_details = $complete_booking_details['data']['booking_details'];
		/*extra_amount_after_ticket_edit will be zero for all bookings except for the bookings where voucher is manually edited*/
		$extra_amount_after_ticket_edit = $master_booking_details[0]["extra_amount"];
		//debug($master_booking_details);
		// debug($complete_booking_details['data']['booking_itinerary_details']);exit;
		$itinerary_details = $this->format_itinerary_details($complete_booking_details['data']['booking_itinerary_details']);
		$payment_details = @$this->format_payment_details($complete_booking_details['data']['payment_details']);
		$segment_details = $this->format_segment_details($complete_booking_details['data']['booking_itinerary_details']);
		$transaction_details = $this->format_flight_transaction_details($complete_booking_details['data']['booking_transaction_details']);		
		$customer_details = $this->format_flight_customer_details($complete_booking_details['data']['booking_customer_details'], $complete_booking_details['data']['cancellation_details']);
		$extra_service_details = $this->format_flight_extra_service_details($module, $convert_currency,@$master_booking_details[0]['currency_conversion_rate'], @$complete_booking_details['data']['baggage_details'], @$complete_booking_details['data']['meal_details'], @$complete_booking_details['data']['seat_details']);
		$admin_user = $GLOBALS["CI"]->custom_db->single_table_records ( 'user', '*',array ('user_type' => ADMIN))["data"][0];
		$tl_details = $this->format_tl_details($complete_booking_details['data']['tl_details']);
		
		$booking_payment_details = array();
		foreach($master_booking_details as $book_k => $book_v) {
			$is_domestic = $GLOBALS['CI']->flight_model->is_domestic_flight($book_v['from_loc'], $book_v['to_loc']);
			$core_booking_details = $book_v;
			$app_reference = $core_booking_details['app_reference'];
			$booking_itinerary_details = $itinerary_details[$app_reference];
			$booking_payment_details = @$payment_details[$app_reference];
			
			if(isset($transaction_details[$app_reference])) {
				$booking_transaction_details = $transaction_details[$app_reference];
				//currency
				$currency = admin_base_currency();
				//check the module
				if(($module == 'b2b' || $module == 'b2c') && $convert_currency == true){
					$currency = $book_v['currency'];
					//get the converted currency rate for booking transaction  
					$booking_transaction_details = $this->display_currency_for_flight($book_v,$transaction_details[$app_reference]);
					//get the converted currency rate for convenience amount
					$book_v = $this->display_convenience_amount($book_v);
				}
				
			} else {
				$booking_transaction_details = array();//Remove Later
			}
			$core_booking_details['currency'] = $currency_obj->get_currency_symbol($currency);
			//$currency_obj = new Currency ( array ('module_type' => 'flight','from' => admin_base_currency (),'to' => $currency));
			//echo 'herre'.$core_booking_details['currency'];exit;
			$fare = 0;
			$admin_commission = 0;
			$agent_commission = 0;
			$admin_tds = 0;
			$agent_tds = 0;
			$admin_markup = 0;
			$agent_markup = 0;
			$pnr = '';
			$trip_type_label = '';
			$gst =0;
			$total_api_base_fare = 0;
			$total_api_tax = 0;
			foreach($booking_transaction_details as $transaction_k => $transaction_v) {
				//Assign Segment Details for the Transaction
				if($core_booking_details['trip_type'] == 'circle' && $is_domestic == true) {
					$booking_transaction_details[$transaction_k]['segment_details'] = @$segment_details[($transaction_k+1)];
				} else {
					$booking_transaction_details[$transaction_k]['segment_details'] = $segment_details;
				}
				//Assign Pax Details for the Transaction
				$booking_transaction_details[$transaction_k]['booking_customer_details'] = @$customer_details[$transaction_v['origin']];
				
				$total_extra_service_price = 0; 
				//Assign Baggage Details for the Transaction
				if(isset($extra_service_details['baggage_details'][$transaction_v['origin']]) == true && valid_array($extra_service_details['baggage_details'][$transaction_v['origin']]) == true){
					$booking_transaction_details[$transaction_k]['extra_service_details']['baggage_details'] = $extra_service_details['baggage_details'][$transaction_v['origin']];
					$total_extra_service_price += $extra_service_details['baggage_details']["total_baggage_price"];
				}
				//Assign Meal Details for the Transaction
				if(isset($extra_service_details['meal_details'][$transaction_v['origin']]) == true && valid_array($extra_service_details['meal_details'][$transaction_v['origin']]) == true){
					$booking_transaction_details[$transaction_k]['extra_service_details']['meal_details'] = $extra_service_details['meal_details'][$transaction_v['origin']];
					$total_extra_service_price += $extra_service_details['meal_details']["total_meal_price"];
				}
				//Assign Seat Details for the Transaction
				if(isset($extra_service_details['seat_details'][$transaction_v['origin']]) == true && valid_array($extra_service_details['seat_details'][$transaction_v['origin']]) == true){
					$booking_transaction_details[$transaction_k]['extra_service_details']['seat_details'] = $extra_service_details['seat_details'][$transaction_v['origin']];
					$total_extra_service_price += $extra_service_details['seat_details']["total_seat_price"];
				}
				
				//Calculating Price
				$fare 				+= $transaction_v['total_fare'];
				$admin_commission	+=$transaction_v['admin_commission'];
				$agent_commission 	+=$transaction_v['agent_commission'];
				$admin_tds 			+= floatval($transaction_v['admin_tds']);
				$agent_tds 			+= floatval($transaction_v['agent_tds']);
				$admin_markup +=$transaction_v['admin_markup'];
				$agent_markup +=$transaction_v['agent_markup'];
				$gst 				+= @$transaction_v['gst'];
				$pnr .= $transaction_v['pnr'].'/';
				
				$extra_attributes = json_decode($transaction_v['attributes']);
				$total_api_base_fare += $extra_attributes->Fare->BaseFare;
				$total_api_tax += $extra_attributes->Fare->Tax;
			}
			if($core_booking_details['trip_type'] == 'oneway') {
				$trip_type_label = 'Oneway';
			} else if($core_booking_details['trip_type'] == 'circle') {
				$trip_type_label = 'Roundway';
			} else if($core_booking_details['trip_type'] == 'multicity') {//FIXME:Miltiway
				$trip_type_label = 'MutiCity';
			}
			$core_booking_details['trip_type_label'] = $trip_type_label;
			$core_booking_details['is_domestic'] = @$is_domestic;
			$core_booking_details['pnr'] = rtrim($pnr, '/');
			$core_booking_details['fare'] = roundoff_number($fare);
			$core_booking_details['admin_commission'] = roundoff_number($admin_commission);
			$core_booking_details['agent_commission'] = roundoff_number($agent_commission);
			$core_booking_details['admin_tds'] = roundoff_number($admin_tds);
			$core_booking_details['agent_tds'] = roundoff_number($agent_tds);
			
			$net_commission = ($admin_commission+$agent_commission);
			$net_commission_tds = ($admin_tds+$agent_tds);
			$core_booking_details['net_commission'] = roundoff_number($net_commission);
			$core_booking_details['net_commission_tds'] = roundoff_number($net_commission_tds);
			$core_booking_details['net_fare'] = roundoff_number($fare-$admin_commission+$admin_tds)+roundoff_number($extra_amount_after_ticket_edit);
			$core_booking_details['admin_markup'] = roundoff_number($admin_markup);
			$core_booking_details['agent_markup'] = roundoff_number($agent_markup);
			$core_booking_details['gst'] = roundoff_number($gst);

			// debug($core_booking_details);
			// exit;
			$admin_buying_price = $this->admin_buying_price($core_booking_details);
			$core_booking_details['admin_buying_price'] = roundoff_number($admin_buying_price[0])+roundoff_number($extra_amount_after_ticket_edit);
			$agent_buying_price = $this->agent_buying_price($core_booking_details);
			$core_booking_details['agent_buying_price'] = roundoff_number($agent_buying_price[0]+$extra_amount_after_ticket_edit);
			//echo debug($core_booking_details);exit;
			$grand_total = $this->total_fare($core_booking_details, $module, $currency);
			// debug($extra_amount_after_ticket_edit); exit;
			$core_booking_details['grand_total'] = roundoff_number($grand_total[0])+roundoff_number($extra_amount_after_ticket_edit);
			// debug($core_booking_details);exit;
			//Re-valued comm. fare just to adjust ticket & manage agent netfare value
			$core_booking_details['fare'] = roundoff_number($fare)+roundoff_number($extra_amount_after_ticket_edit);
			
			$gst += $core_booking_details['extra_gst'];
			$admin_markup += $core_booking_details['extra_admin_markup'];
			$core_booking_details['admin_markup'] = roundoff_number($admin_markup);
			$core_booking_details['gst'] = roundoff_number($gst);
			
			//Lead Pax Details
			$attributes = json_decode($core_booking_details['attributes'], true);
			$core_booking_details['cutomer_city'] = $attributes['city'];
			$core_booking_details['cutomer_zipcode'] = $attributes['zipcode'];
			$core_booking_details['cutomer_address'] = $attributes['address'];
			$core_booking_details['cutomer_country'] = $attributes['country'];
			$lead_pax_details = @$booking_transaction_details[0]['booking_customer_details'][0];
			$core_booking_details['lead_pax_name'] = $lead_pax_details['title'].' '.$lead_pax_details['first_name'].' '.$lead_pax_details['last_name'];
			$core_booking_details['lead_pax_phone_number'] = $core_booking_details['phone'];
			$core_booking_details['lead_pax_email'] = $core_booking_details['email']; 
			//Domain Details
			$domain_details = $this->domain_details($core_booking_details['domain_origin']);
			$core_booking_details['domain_name'] = $domain_details['domain_name'];
			$core_booking_details['domain_ip'] = $domain_details['domain_ip'];
			$core_booking_details['domain_key'] = $domain_details['domain_key'];
			$core_booking_details['theme_id'] = $domain_details['theme_id'];
			$core_booking_details['domain_logo'] = $domain_details['domain_logo'];
			$core_booking_details['booked_date'] = app_friendly_absolute_date($core_booking_details['created_datetime']);
			$core_booking_details['total_api_base_fare'] = $total_api_base_fare;
			$core_booking_details['total_api_tax'] = $total_api_tax;
			$core_booking_details['total_extra_service_price'] = $total_extra_service_price;
			$core_booking_details['opening_balance'] = $tl_details[$app_reference][0]["opening_balance"];
			$core_booking_details['closing_balance'] = $tl_details[$app_reference][0]["closing_balance"];
			$core_booking_details['is_void'] = $book_v['is_void'];
			$gst_details = $core_booking_details['gst_details'];
			$gst_number = "NA";
			if(!empty(trim($gst_details)))
			{
				$dc_gst_details = json_decode($gst_details, true);
				$gst_number = $dc_gst_details["gst_number"];
				if($admin_user["gst_number"] == $gst_number)
					$gst_number = "NA";
			}
			$core_booking_details['gst_number'] = $gst_number;
			
			//Formating the data
			//Booking Details	
			$booking_details[$app_reference] = $core_booking_details;
			//Itinerary Details
			$booking_details[$app_reference]['booking_itinerary_details'] = $booking_itinerary_details;
			//Customer Details
			$booking_details[$app_reference]['booking_transaction_details'] = $booking_transaction_details;
			//Payment Details
			$booking_details[$app_reference]['booking_payment_details'] = $booking_payment_details;
		}
		$response['data']['booking_details_app'] = $booking_details;
		$booking_details = $this->convert_as_array($booking_details);
		$response['data']['booking_details'] = $booking_details;
		//debug($response['data']['booking_details']); exit;
		return $response;
	}
	 /**
	 *Convert price details  into respective currency rate for flight  
	 * Sagar Wakchaure
	 * @param array $currency_rate
	 * @param array $transaction_details
	 * @return unknown
	 */
	function display_currency_for_flight($currency_rate = array(),$transaction_details = array()){
			$conversion_rate = $currency_rate['currency_conversion_rate']; 
			foreach($transaction_details as $key => $sub_transaction_details){
					$transaction_details[$key]['total_fare'] =  $this->add_currencyrate($sub_transaction_details['total_fare'],$conversion_rate);
					$transaction_details[$key]['admin_commission'] =  $this->add_currencyrate($sub_transaction_details['admin_commission'],$conversion_rate);
					$transaction_details[$key]['agent_commission'] =  $this->add_currencyrate($sub_transaction_details['agent_commission'],$conversion_rate);
					$transaction_details[$key]['admin_tds'] =  $this->add_currencyrate($sub_transaction_details['admin_tds'],$conversion_rate);
					$transaction_details[$key]['agent_tds'] =  $this->add_currencyrate($sub_transaction_details['agent_tds'],$conversion_rate);
					$transaction_details[$key]['admin_markup'] =  $this->add_currencyrate($sub_transaction_details['admin_markup'],$conversion_rate);
					$transaction_details[$key]['agent_markup'] =  $this->add_currencyrate($sub_transaction_details['agent_markup'],$conversion_rate);
					$transaction_details[$key]['gst'] =  $this->add_currencyrate($sub_transaction_details['gst'],$conversion_rate);
			}		
			return $transaction_details;
	}
	
	 /**
	  * calculate total price with currency rate
	 *Sagar Wakchaure
	 * @param unknown $value
	 * @param unknown $conversion_rate
	 */
	function add_currencyrate($value,$conversion_rate){
		return ($value*$conversion_rate);
	}
	
	/**
	 * convert convenience amount into respective currency rate
	 * Sagar Wakchaure
	 * @param array $v_book
	 * @return unknown
	 */
	function display_convenience_amount($v_book = array()){	
	
		$v_book['convinence_amount'] =  $this->add_currencyrate($v_book['convinence_amount'],$v_book['currency_conversion_rate']);
		$v_book['discount'] =  $this->add_currencyrate($v_book['discount'],$v_book['currency_conversion_rate']);
		$v_book['gst'] =  $this->add_currencyrate(@$v_book['gst'],$v_book['currency_conversion_rate']);
		return $v_book;
	}
	
	/**Balu A
	 * Get Domain Details
	 * @param unknown_type $domain_origin
	 */
	private function domain_details($domain_origin)
	{
		$domain_details = $GLOBALS['CI']->custom_db->single_table_records('domain_list', '*', array('origin' => intval($domain_origin)));
		return $domain_details['data'][0];
	}
	/**
	 * Balu A
	 * Returns Total Fare
	 * @param array $fare_details
	 */
	function total_fare($fare_details, $module, $currecny='')
	{
		$fare_details = force_multple_data_format($fare_details);
		// /debug($fare_details);exit;
		$total_fare = array();
		if($module == 'b2c') {//B2C Total Fare
			foreach($fare_details as $k => $v) {
				$fare = (isset($v['fare']) == true ? $v['fare'] : $v['total_fare']);
				if(isset($v['admin_commission'])){
					$admin_commission = $v['admin_commission'];
				} else {
					$admin_commission = 0;
				}
				if(isset($v['admin_tds'])){
					$admin_tds = $v['admin_tds'];
				} else {
					$admin_tds = 0;
				}
				if(!empty($currecny)){
					$currency_obj = new Currency ( array ('module_type' => 'flight','from' => admin_base_currency (),'to' => $currecny));
					$convinence_amount = get_converted_currency_value($currency_obj->force_currency_conversion($v['convinence_amount']));
					$discount = get_converted_currency_value($currency_obj->force_currency_conversion($v['discount']));
				}
				else{	
					$convinence_amount = $v['convinence_amount'];
					$discount = $v['discount'];

				}
				

				// $total_fare[$k] = roundoff_number($fare+$v['admin_markup']+$convinence_amount-$discount-$admin_commission+$admin_tds+@$v['gst']);
				$total_fare[$k] = roundoff_number($fare+$v['admin_markup']+$convinence_amount-$discount+@$v['gst']);
				
			}
			
		} else if($module == 'b2b') {//B2B Total Fare
			foreach($fare_details as $k => $v) {
				$fare = (isset($v['fare']) ? $v['fare'] : $v['total_fare']);
				
				$total_fare[$k] = roundoff_number($fare+$v['admin_markup']+$v['convinence_amount']+$v['agent_markup']+@$v['gst']);
			}
		}else if($module == 'admin') {

			foreach($fare_details as $k => $v) {
				$fare = (isset($v['fare']) ? $v['fare'] : $v['total_fare']);
				// echo $fare;exit;
				$total_fare[$k] = roundoff_number($fare+$v['admin_markup']+$v['convinence_amount']+$v['agent_markup']-$v['discount']+@$v['gst']);
			}
		}
		
		// debug($total_fare);exit;
		return $total_fare;
	}
	/**
	 * Balu A
	 * Returns Agent Buying Fare
	 * @param array $fare_details
	 */
	function admin_buying_price($fare_details)
	{
		$fare_details = force_multple_data_format($fare_details);
		//debug($fare_details);exit;
		$admin_buying_price = array();
		foreach($fare_details as $k => $v) {
			$fare = (isset($v['fare']) ? $v['fare'] : $v['total_fare']);
			$admin_commission	= floatval(@$v['admin_commission']);
			$tds_on_commission	= floatval(@$v['admin_tds']);
			$admin_buying_price[$k] = ($fare-$admin_commission);//FIXME:Check Calculation -- Balu A
		}
		return $admin_buying_price;
	}
	/**
	 * Balu A
	 * Returns Agent Buying Fare
	 * @param array $fare_details
	 * @param module flight,hotel,bus,transfers,sightseeing
	 */
	function agent_buying_price($fare_details)
	{
		$fare_details = force_multple_data_format($fare_details);
		$agent_buying_price = array();		
		
		foreach($fare_details as $k => $v) {
			$fare = (isset($v['fare']) ? $v['fare'] : $v['total_fare']);
			$agent_commission = (isset($v['agent_commission']) ? $v['agent_commission'] : 0);
			$tds_on_commission = (isset($v['agent_tds']) ? $v['agent_tds'] : 0);
			$gst = (isset($v['gst']) ? $v['gst'] : 0);

			$agent_buying_price[$k] = $fare+$v['admin_markup']+$tds_on_commission-$agent_commission+$gst;
			
		}
	
		return $agent_buying_price;
	}
	/**
	 * Format Itinerary Details
	 * @param unknown_type $itinerary_details
	 */
	private function format_itinerary_details($itinerary_details)
	{
		
		$booking_itinerary_details = array();
		foreach($itinerary_details as $itinerary_k => $itinerary_v) {
			//debug($itinerary_v);exit;
			$duration = 0;
			if(isset($itinerary_v['from_airport_code'])&&isset($itinerary_v['to_airport_code'])&&isset($itinerary_v['arrival_datetime'])&&isset($itinerary_v['departure_datetime'])){
				$duration = $this->flight_segment_duration($itinerary_v['from_airport_code'], $itinerary_v['to_airport_code'], $itinerary_v['arrival_datetime'], $itinerary_v['departure_datetime']);
			}
			
			$duration = $duration*60;
            $itinerary_v['total_duration'] =get_time_duration_label($duration);
			// $itinerary_v['total_duration'] = get_duration_label(calculate_duration(@$itinerary_v['arrival_datetime'], @$itinerary_v['departure_datetime']));
			$booking_itinerary_details[$itinerary_v['app_reference']][] = $itinerary_v;
		}
		return $booking_itinerary_details;
	}
	/**
	 * Format Payment Details
	 * @param unknown_type $itinerary_details
	 */
	private function format_payment_details($payment_details)
	{
		
		$booking_payment_details = array();
		foreach($payment_details as $payment_k => $payment_v) {
			
			$booking_payment_details[$payment_v['app_reference']][] = $payment_v;
		}
		return $booking_payment_details;
	}
	/**
	 * Format Payment Details
	 * @param unknown_type $itinerary_details
	 */
	private function format_tl_details($tl_details)
	{
		
		$booking_tl_details = array();
		foreach($tl_details as $tl_k => $tl_v) {
			
			$booking_tl_details[$tl_v['app_reference']][] = $tl_v;
		}
		return $booking_tl_details;
	}
	/**
	 * Formatting the Segment details based on Segment Indicator(Onward/Return)
	 */
	private function format_segment_details($itinerary_details)
	{
		$booking_segment_details = array();
		foreach($itinerary_details as $itinerary_k => $itinerary_v) {
			$duration =  $this->flight_segment_duration($itinerary_v['from_airport_code'], $itinerary_v['to_airport_code'], $itinerary_v['arrival_datetime'], $itinerary_v['departure_datetime']);			// $itinerary_v['total_duration'] = get_duration_label(calculate_duration($itinerary_v['arrival_datetime'], $itinerary_v['departure_datetime']));
			$duration=$duration*60;
            $itinerary_v['total_duration'] =get_time_duration_label($duration);
			$booking_segment_details[$itinerary_v['segment_indicator']][] = $itinerary_v;
			//$booking_segment_details[$itinerary_k] = $itinerary_v;
		}
		return $booking_segment_details;
	}
	private function format_customer_details($customer_details)
	{
		$booking_customer_details = array();
		foreach($customer_details as $customer_k => $customer_v) {
			$booking_customer_details[$customer_v['app_reference']][] = $customer_v;
		}
		return $booking_customer_details;
	}
	private function format_temp_booking_details($booking_details)
	{
		$temp_booking_details = array();
		foreach($booking_details as $customer_k => $customer_v) {
			$temp_booking_details[$customer_v['book_id']][] = $customer_v;
		}
		return $temp_booking_details;
	}
	private function format_hotel_cancellation_details($cancellation_details)
	{
		$booking_cancellation_details = array();
		foreach($cancellation_details as $cancel_k => $cancel_v) {
			$booking_cancellation_details[$cancel_v['app_reference']][] = $cancel_v;
		}
		return $booking_cancellation_details;
	}
	private function format_sightseeing_cancellation_details($cancellation_details)
	{
		$booking_cancellation_details = array();
		foreach($cancellation_details as $cancel_k => $cancel_v) {
			$booking_cancellation_details[$cancel_v['app_reference']][] = $cancel_v;
		}
		return $booking_cancellation_details;
	}
	function format_car_booking_datas($complete_booking_details, $module)
	{
		// debug($complete_booking_details); die;
		$response['status']	= SUCCESS_STATUS;
		$response['data']	= array();
		$booking_details = array();
		$currency_obj = new Currency();
		$master_booking_details = $complete_booking_details['data']['booking_details'];
	    // debug($master_booking_details);exit;
		$itinerary_details = $this->format_itinerary_details($complete_booking_details['data']['booking_itinerary_details']);
	    // echo debug($itinerary_details);exit;
		// debug($complete_booking_details['data']['booking_extra_details']);exit;
		$customer_details = $this->format_customer_details($complete_booking_details['data']['booking_pax_details']);
		$cancellation_details = $this->format_car_cancellation_details($complete_booking_details['data']['cancellation_details']);
		// echo debug($customer_details);exit;
		foreach($master_booking_details as $book_k => $book_v) {
			//debug($book_v); die;
			$core_booking_details = $book_v;
			
			$app_reference = $core_booking_details['app_reference'];
			$booking_itinerary_details = $itinerary_details[$app_reference];
			$booking_customer_details = $customer_details[$app_reference];
			$booking_cancellation_details = @$cancellation_details[$app_reference];		
			

			//Calculating Price
			$fare = $master_booking_details[0]['total_fare'];
			$admin_markup = 0;
			$agent_markup = 0;
			
			
			//Formatiing the data
			//Booking Details
			$core_booking_details['car_image'] = @$itinerary_details[$app_reference][0]['pricture_url'];
			$core_booking_details['pickup_from'] = $master_booking_details[0]['car_pickup_lcation'];
			$core_booking_details['drop_to'] = $master_booking_details[0]['car_drop_location'];
			$core_booking_details['journey_datetime'] = $master_booking_details[0]['car_from_date']." ".$master_booking_details[0]['pickup_time'];
			$core_booking_details['departure_datetime'] = $master_booking_details[0]['car_from_date']." ".$master_booking_details[0]['pickup_time'];
			$core_booking_details['arrival_datetime'] = $master_booking_details[0]['car_to_date']." ".$master_booking_details[0]['drop_time'];
			$core_booking_details['car_name'] = $master_booking_details[0]['car_name'];
			$core_booking_details['car_supplier_name'] = $master_booking_details[0]['car_supplier_name'];
			$core_booking_details['supplier_identifier'] = $master_booking_details[0]['supplier_identifier'];
			$fare = 0;
			$admin_markup = 0;
			$agent_markup = 0;
			foreach($booking_itinerary_details as $itinerary_k => $itinerary_v) {
				$fare += $itinerary_v['total_fare'];
				$admin_markup += floatval($itinerary_v['admin_markup']);
				$agent_markup += floatval($itinerary_v['agent_markup']);
			}
			// echo $fare;exit;
			$core_booking_details['fare'] = roundoff_number($fare);
			$core_booking_details['admin_markup'] = roundoff_number($admin_markup);
			$core_booking_details['agent_markup'] = roundoff_number($agent_markup);
			$admin_buying_price = $this->admin_buying_price($core_booking_details);
			$core_booking_details['admin_buying_price'] = roundoff_number($admin_buying_price[0]);
			if($module == 'b2b') {
				$agent_buying_price = $this->agent_buying_price($core_booking_details);
				$core_booking_details['agent_buying_price'] = roundoff_number($agent_buying_price[0]);
			} else {
				$core_booking_details['agent_buying_price'] = 0;
			}
			// debug($core_booking_details);exit;
			//debug($core_booking_details['total_fare']); die;
			$grand_total = $this->total_fare($core_booking_details, $module);
			$core_booking_details['grand_total'] = roundoff_number($grand_total[0]);
			$core_booking_details['total_fare'] = $fare;
			
			//get currency
			$currency = get_application_default_currency();
			
			$core_booking_details['currency'] = $currency_obj->get_currency_symbol($currency);
			
			$core_booking_details['voucher_date'] = app_friendly_absolute_date($core_booking_details['created_datetime']);
			//Lead Pax Details
			$core_booking_details['cutomer_city'] = @$attributes['billing_city'];
			$core_booking_details['cutomer_zipcode'] = @$attributes['billing_zipcode'];
			$core_booking_details['cutomer_address'] = @$attributes['address'];
			$core_booking_details['cutomer_country'] = @$attributes['billing_country'];
			$core_booking_details['lead_pax_name'] = $booking_customer_details[0]['title'].' '.$booking_customer_details[0]['first_name'].' '.$booking_customer_details[0]['last_name'];
			$core_booking_details['lead_pax_phone_number'] = $core_booking_details['phone_number'];
			$core_booking_details['lead_pax_email'] = $core_booking_details['email']; 
			//Domain Details
			$domain_details = $this->domain_details($core_booking_details['domain_origin']);

			$core_booking_details['domain_name'] = $domain_details['domain_name'];
			$core_booking_details['domain_ip'] = $domain_details['domain_ip'];
			$core_booking_details['domain_key'] = $domain_details['domain_key'];
			$core_booking_details['theme_id'] = $domain_details['theme_id'];
			$core_booking_details['domain_logo'] = $domain_details['domain_logo'];
			//Formating the data
			//Booking Details
			$booking_details[$app_reference] = $core_booking_details;

			//Itenary Details
			$booking_details[$app_reference]['itinerary_details'] = $booking_itinerary_details;
			//Itenary Details
			$booking_details[$app_reference]['extra_service_details'] = $complete_booking_details['data']['booking_extra_details'];
			//Customer Details
			$booking_details[$app_reference]['customer_details'] = $booking_customer_details;
			//Cancellation Details
			$booking_details[$app_reference]['cancellation_details'] = $booking_cancellation_details;
			
			

		}
		// debug($booking_details);exit;
	
		$booking_details = $this->convert_as_array($booking_details);
		// debug($booking_details);exit;
		$response['data']['booking_details'] = $booking_details;
		
		return $response;
	}
	private function format_car_cancellation_details($cancellation_details)
	{
		$booking_cancellation_details = array();
		foreach($cancellation_details as $cancel_k => $cancel_v) {
			$booking_cancellation_details[$cancel_v['app_reference']][] = $cancel_v;
		}
		return $booking_cancellation_details;
	}
	/**
	 * Format Flight Transaction Details
	 * @param unknown_type $itinerary_details
	 */
	private function format_flight_transaction_details($transaction_details)
	{
		$booking_transaction_details = array();
		foreach($transaction_details as $transaction_k => $transaction_v) {
			$booking_transaction_details[$transaction_v['app_reference']][] = $transaction_v;
		}
		return $booking_transaction_details;
	}
	/**
	 * Format Flight Customer Details
	 * @param unknown_type $itinerary_details
	 */
	private function format_flight_customer_details($customer_details, $cancellation_details)
	{
		$cancellation_details = $this->format_flight_cancellation_details($cancellation_details);
		
		$booking_customer_details = array();
		foreach($customer_details as $customer_k => $customer_v) {
			$temp_customer_details = $customer_v;
			//Assigning cancellation Details
			if(isset($cancellation_details[$temp_customer_details['origin']]) == true && valid_array($cancellation_details[$temp_customer_details['origin']]) == true) {
				$temp_customer_details['cancellation_details'] = $cancellation_details[$temp_customer_details['origin']];
			}
			$booking_customer_details[$customer_v['flight_booking_transaction_details_fk']][] = $temp_customer_details;
		}
		return $booking_customer_details;
	}
	/**
	 * 
	 */
	private function format_flight_cancellation_details($cancellation_details)
	{
		$customer_cancellation_details = array();
		foreach($cancellation_details as $cancel_k => $cancel_v) {
			if(intval($cancel_v['passenger_fk']) > 0) {
				$customer_cancellation_details[$cancel_v['passenger_fk']] = $cancel_v;
			}
		}
		return $customer_cancellation_details;
	}
	/**
	 * Format Flight Extra Service Details
	 * @param unknown_type $itinerary_details
	 */
	private function format_flight_extra_service_details($module, $convert_currency, $currency_conversion_rate, $baggage_details, $meal_details, $seat_details)
	{
		$extra_service_details = array();
		if(valid_array($baggage_details)){
			$extra_service_details['baggage_details'] = $this->format_baggage_details($baggage_details, $module, $convert_currency, $currency_conversion_rate);
		}
		if(valid_array($meal_details)){
			$extra_service_details['meal_details'] = $this->format_meal_details($meal_details, $module, $convert_currency, $currency_conversion_rate);
		}
		if(valid_array($seat_details)){
			$extra_service_details['seat_details'] = $this->format_seat_details($seat_details, $module, $convert_currency, $currency_conversion_rate);
		}
		return $extra_service_details;
	}
	/**
	 * Formates Baggage Details
	 * @param unknown_type $baggage_details
	 */
	private function format_baggage_details($baggage_details, $module, $convert_currency, $currency_conversion_rate)
	{		
		$formatted_baggage_details = array();
		foreach($baggage_details as $k => $v) {
			if(($module == 'b2b' || $module == 'b2c') && $convert_currency == true){
				$price = roundoff_number($v['price']*$currency_conversion_rate);
			} else {
				$price = roundoff_number($v['price']);
			}
			$v['price'] = $price;
			$formatted_baggage_details[$v['flight_booking_transaction_details_fk']]['details'][$v['passenger_fk']][] = $v;
			
			if(!isset($formatted_baggage_details[$v['flight_booking_transaction_details_fk']]['baggage_source_destination_label'])){
				$formatted_baggage_details[$v['flight_booking_transaction_details_fk']]['baggage_source_destination_label'] = array();
			}
			
			$baggage_source_destination_label = $v['from_airport_code'].'-'.$v['to_airport_code'];
			if(in_array($baggage_source_destination_label, $formatted_baggage_details[$v['flight_booking_transaction_details_fk']]['baggage_source_destination_label']) == false){
				$formatted_baggage_details[$v['flight_booking_transaction_details_fk']]['baggage_source_destination_label'][] = $baggage_source_destination_label;
			}
		}
		$total_baggage_price = 0;
		foreach($baggage_details AS $bgd)
		{
			$total_baggage_price += $bgd["price"];
		}
		$formatted_baggage_details["total_baggage_price"] = $total_baggage_price;

		return $formatted_baggage_details;
	}
	/**
	 * Formates Meal Details
	 * @param unknown_type $meal_details
	 */
	private function format_meal_details($meal_details, $module, $convert_currency, $currency_conversion_rate)
	{
		$formatted_meal_details = array();
		foreach($meal_details as $k => $v) {
			if(($module == 'b2b' || $module == 'b2c') && $convert_currency == true){
				$price = roundoff_number($v['price']*$currency_conversion_rate);
			} else {
				$price = roundoff_number($v['price']);
			}
			$v['price'] = $price;
			$formatted_meal_details[$v['flight_booking_transaction_details_fk']]['details'][$v['passenger_fk']][] = $v;
			
			
			if(!isset($formatted_meal_details[$v['flight_booking_transaction_details_fk']]['meal_source_destination_label'])){
				$formatted_meal_details[$v['flight_booking_transaction_details_fk']]['meal_source_destination_label'] = array();
			}
			
			$meal_source_destination_label = $v['from_airport_code'].'-'.$v['to_airport_code'];
			if(in_array($meal_source_destination_label, $formatted_meal_details[$v['flight_booking_transaction_details_fk']]['meal_source_destination_label']) == false){
				$formatted_meal_details[$v['flight_booking_transaction_details_fk']]['meal_source_destination_label'][] = $meal_source_destination_label;
			}
		}
		$total_meal_price = 0;
		foreach($meal_details AS $md)
		{
			$total_meal_price += $md["price"];
		}
		$formatted_meal_details["total_meal_price"] = $total_meal_price;

		return $formatted_meal_details;
	}
	/**
	 * Formates Seat Details
	 * @param unknown_type $seat_details
	 */
	private function format_seat_details($seat_details, $module, $convert_currency, $currency_conversion_rate)
	{
		$formatted_seat_details = array();
		foreach($seat_details as $k => $v) {
			if(($module == 'b2b' || $module == 'b2c') && $convert_currency == true){
				$price = roundoff_number($v['price']*$currency_conversion_rate);
			} else {
				$price = roundoff_number($v['price']);
			}
			$v['price'] = $price;
			$formatted_seat_details[$v['flight_booking_transaction_details_fk']]['details'][$v['passenger_fk']][] = $v;
			
			if(!isset($formatted_seat_details[$v['flight_booking_transaction_details_fk']]['seat_source_destination_label'])){
				$formatted_seat_details[$v['flight_booking_transaction_details_fk']]['seat_source_destination_label'] = array();
			}
			
			$seat_source_destination_label = $v['from_airport_code'].'-'.$v['to_airport_code'];
			if(in_array($seat_source_destination_label, $formatted_seat_details[$v['flight_booking_transaction_details_fk']]['seat_source_destination_label']) == false){
				$formatted_seat_details[$v['flight_booking_transaction_details_fk']]['seat_source_destination_label'][] = $seat_source_destination_label;
			}
		}

		$total_seat_price = 0;
		foreach($seat_details AS $sd)
		{
			$total_seat_price += $sd["price"];
		}
		$formatted_seat_details["total_seat_price"] = $total_seat_price;

		return $formatted_seat_details;
	}
	/*
	 * Total Booking Count
	 */
	function get_booking_counts()
	{
		$response['status']	= SUCCESS_STATUS;
		$response['data']	= array();
		$data = array();
		$condition = array();
		$GLOBALS['CI']->load->model('bus_model');
		$GLOBALS['CI']->load->model('hotel_model');
		$GLOBALS['CI']->load->model('flight_model');
		$GLOBALS['CI']->load->model('sightseeing_model');
		$GLOBALS['CI']->load->model('car_model');
		$GLOBALS['CI']->load->model('transferv1_model');
		$data['bus_booking_count'] = $GLOBALS['CI']->bus_model->booking($condition, true);
		$data['hotel_booking_count'] = $GLOBALS['CI']->hotel_model->booking($condition, true);
		$data['flight_booking_count'] = $GLOBALS['CI']->flight_model->booking($condition, true);
		$data['car_booking_count'] = $GLOBALS['CI']->car_model->booking($condition, true);
		$data['sightseeing_booking_count'] = $GLOBALS['CI']->sightseeing_model->booking($condition, true);

		$data['transfer_booking_count'] = $GLOBALS['CI']->transferv1_model->booking($condition, true);

		$response['data'] = $data;	
		return $response;
	}
	/*
	 * Balu A
	 * Recent Activities
	 */
	function format_recent_transactions($transaction_details, $module)
	{
		//debug($transaction_details);exit;
		$response['status']	= SUCCESS_STATUS;
		$response['data']	= array();
		$data = array();
		$details = array();
		
		foreach($transaction_details as $transaction_k => $transaction_v) {
			$admin_buying_price = $this->admin_buying_price($transaction_v);
			$core_booking_details['admin_buying_price'] = $admin_buying_price[0];
			if($module == 'b2b') {
				$agent_buying_price = $this->agent_buying_price($transaction_v);
				$core_booking_details['agent_buying_price'] = $agent_buying_price[0];
			} else {
				$core_booking_details['agent_buying_price'] = 0;
			}
			$total_fare = $this->total_fare($transaction_v, $module);
			$details[$transaction_k] = $transaction_v;
			$details[$transaction_k]['grand_total'] = $total_fare[0];
		}
		$data['transaction_details'] = $details;
		$response['data'] = $data;
		return $response;
	}
	/*
	 * Balu A
	 * Implode Reference Ids
	 */
	function implode_app_reference_ids($booking_details)
	{
		$app_reference_ids = '';
		foreach($booking_details as $k => $v) {
			$app_reference_ids .= '"'.$v['app_reference'].'",';
		}
		$app_reference_ids = rtrim($app_reference_ids, ',');
		return $app_reference_ids;
	}
	/**
	 * Balu A
	 * @param $booking_details
	 */
	function convert_as_array($booking_details)
	{
		if(count($booking_details) == 1) {
			$booking_details = array_values($booking_details);//FIXME: Balu A
		}
		return $booking_details;
	}
	
	/**Sagar Wakchaure
	 * format master  transaction balance
	 */
	function format_master_transaction_balance($transacion_data = array(),$module){
		
		foreach ($transacion_data as $key => $transacion_sub_data){
			$transacion_data[$key]['amount'] = round($transacion_sub_data['amount']);
			if($module == 'b2b' || $module == 'b2c'){
				$transacion_data[$key]['amount'] = round($this->add_currencyrate($transacion_sub_data['amount'],$transacion_sub_data['currency_conversion_rate']));
			}
			
		}
		return $transacion_data;
	}
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
     * Balu A
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
                    WHEN start_month    = '.$journey_month.' THEN 1
                    WHEN end_month  = '.$journey_month.' THEN 2
                    ELSE 3 END';
        $timezone_offset = $GLOBALS['CI']->db->query($query)->result_array();
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
    /*Transfers*/
    /**
	 * Elavarasi
	 * @param array $booking_details
	 */
  	  function format_transferv1_booking_data($complete_booking_details, $module)
	{
		$response['status']	= SUCCESS_STATUS;
		$response['data']	= array();
		$booking_details = array();
		$currency_obj = new Currency();
		$master_booking_details = $complete_booking_details['data']['booking_details'];
	   //echo debug($master_booking_details);exit;
		$itinerary_details = $this->format_itinerary_details($complete_booking_details['data']['booking_itinerary_details']);
	    //echo debug($itinerary_details);exit;
		$payment_details = @$this->format_payment_details($complete_booking_details['data']['payment_details']);
		$customer_details = $this->format_customer_details($complete_booking_details['data']['booking_customer_details']);		
		$cancellation_details = $this->format_transferv1_cancellation_details($complete_booking_details['data']['cancellation_details']);
		$booking_payment_details = array();
		foreach($master_booking_details as $book_k => $book_v) {
			$core_booking_details = $book_v;

			$app_reference = $core_booking_details['app_reference'];
			$booking_itinerary_details = $itinerary_details[$app_reference];
			$booking_customer_details = $customer_details[$app_reference];
			$booking_cancellation_details = @$cancellation_details[$app_reference];		
			$booking_payment_details = @$payment_details[$app_reference];	
			if($module == 'b2b' || $module == 'b2c' ){
				//get the converted currency rate for booking transaction
				$booking_itinerary_details = $this->display_currency_for_transferv1($book_v,$itinerary_details[$app_reference]);
				//get the converted currency rate for convenience amount
				$core_booking_details = $this->display_convenience_amount($book_v);
			}
			//Calculating Price
			$fare = 0;
			$admin_markup = 0;
			$agent_markup = 0;
			$agent_commission = 0;
			$admin_commission = 0;
			$admin_tds = 0;
			$agent_tds = 0;
			$admin_net_fare_markup = 0;
			$gst = 0;

			foreach($booking_itinerary_details as $itinerary_k => $itinerary_v) {

				$fare += $itinerary_v['total_fare'];
				$admin_markup += floatval($itinerary_v['admin_markup']);
				$agent_markup += floatval($itinerary_v['agent_markup']);
				$agent_commission +=floatval($itinerary_v['agent_commission']);
				$admin_commission +=floatval($itinerary_v['admin_commission']);
				$admin_tds +=floatval($itinerary_v['admin_tds']);
				$agent_tds +=floatval($itinerary_v['agent_tds']);
				$gst += floatval($itinerary_v['gst']);
				//$admin_net_fare_markup +=floatval($itinerary_v['admin_net_markup']);

			}
			//PaxCount
			$adult_count = 0;
			$child_count = 0;
			$infant_count =0;
			$youth_count = 0;
			$senior_count = 0;
			foreach($booking_customer_details as $customer_k => $customer_v) {
				$pax_type = $customer_v['pax_type'];
				if($pax_type == 'Adult') {
					$adult_count++;
				} else if($pax_type == 'Child'){
					$child_count++;
				}
				elseif($pax_type=='Infant'){
					$infant_count++;
				}
				elseif($pax_type=='Senior'){
					$senior_count++;
				}
				elseif ($pax_type=='Youth') {
					$youth_count++;
				}
			}
			//Formatiing the data
			//Booking Details
			$attributes = json_decode($core_booking_details['attributes'], true);
			
			$core_booking_details['ProductImage'] = $attributes['ProductImage'];
			$core_booking_details['Destination'] = $attributes['Destination'];
			$core_booking_details['DeparturePointAddress'] = $attributes['DeparturePointAddress'];
			$core_booking_details['TM_Cancellation_Policy'] = $attributes['TM_Cancellation_Policy'];
			
			$core_booking_details['adult_count'] = $adult_count;
			$core_booking_details['child_count'] = $child_count;
			$core_booking_details['youth_count'] = $youth_count;
			$core_booking_details['senior_count'] = $senior_count;
			$core_booking_details['infant_count'] = $infant_count;

			$core_booking_details['fare'] = roundoff_number($fare);
			$core_booking_details['admin_markup'] = roundoff_number($admin_markup);
			$core_booking_details['gst'] = roundoff_number($gst);
			//$core_booking_details['admin_net_markup'] =roundoff_number($admin_net_fare_markup);
			$core_booking_details['agent_markup'] = roundoff_number($agent_markup);
			$core_booking_details['admin_commission'] = roundoff_number($admin_commission);
			$core_booking_details['agent_commission'] = roundoff_number($agent_commission);
			$core_booking_details['admin_tds'] = roundoff_number($admin_tds);
			$core_booking_details['agent_tds'] = roundoff_number($agent_tds);
			$net_commission = ($admin_commission+$agent_commission);
			$net_commission_tds = ($admin_tds+$agent_tds);
			$core_booking_details['net_commission'] = roundoff_number($net_commission);
			$core_booking_details['net_commission_tds'] = roundoff_number($net_commission_tds);
			$core_booking_details['net_fare'] = roundoff_number($fare-$net_commission);

			$core_booking_details['admin_net_fare'] = roundoff_number($fare-$admin_commission+$admin_tds);

			// debug($core_booking_details);
			// echo $admin_markup;
			// exit;
			$core_booking_details['product_total_price'] =roundoff_number($core_booking_details['admin_net_fare']+$admin_markup +$agent_markup);//without convience fee

			// debug($core_booking_details);
			// exit;
			$admin_buying_price = $this->admin_buying_price($core_booking_details);
			$core_booking_details['admin_buying_price'] = roundoff_number($admin_buying_price[0]);
			
			if($module == 'b2b' || $module=='admin') {

				$agent_buying_price = $this->agent_buying_price($core_booking_details);
				$core_booking_details['agent_buying_price'] = roundoff_number($agent_buying_price[0]);
			} else {
				//$core_booking_details['fare']=$core_booking_details['admin_net_fare'];
				$core_booking_details['agent_buying_price'] = 0;
			}
			//debug($core_booking_details);

			$grand_total = $this->total_fare($core_booking_details, $module);
			$core_booking_details['grand_total'] = roundoff_number($grand_total[0]);
			
			
			//get currency
			$currency = admin_base_currency();
			if($module == 'b2b' || $module == 'b2c' ){
			    $currency = $book_v['currency'];
			}
			$core_booking_details['currency'] = $currency_obj->get_currency_symbol($currency);
			
			$core_booking_details['voucher_date'] = app_friendly_absolute_date($core_booking_details['created_datetime']);
			//Lead Pax Details
			$core_booking_details['cutomer_city'] = $attributes['billing_city'];
			$core_booking_details['cutomer_zipcode'] = $attributes['billing_zipcode'];
			$core_booking_details['cutomer_address'] = $attributes['address'];
			$core_booking_details['cutomer_country'] = $attributes['billing_country'];
			$core_booking_details['destination_name'] = $attributes['Destination'];
			$core_booking_details['lead_pax_name'] = $booking_customer_details[0]['first_name'].' '.$booking_customer_details[0]['last_name'];
			$core_booking_details['lead_pax_phone_number'] = $core_booking_details['phone_number'];
			$core_booking_details['lead_pax_email'] = $core_booking_details['email']; 
			//Domain Details
			$domain_details = $this->domain_details($core_booking_details['domain_origin']);
			$core_booking_details['domain_name'] = $domain_details['domain_name'];
			$core_booking_details['domain_ip'] = $domain_details['domain_ip'];
			$core_booking_details['domain_key'] = $domain_details['domain_key'];
			$core_booking_details['theme_id'] = $domain_details['theme_id'];
			$core_booking_details['domain_logo'] = $domain_details['domain_logo'];
			//Formating the data
			//Booking Details
			$booking_details[$app_reference] = $core_booking_details;
			//Itenary Details
			$booking_details[$app_reference]['itinerary_details'] = $booking_itinerary_details;
			//Customer Details
			$booking_details[$app_reference]['customer_details'] = $booking_customer_details;
			$booking_details[$app_reference]['cancellation_details'] = $booking_cancellation_details;
			//Payment Details
			$booking_details[$app_reference]['booking_payment_details'] = $booking_payment_details;
		}
		// debug($booking_details);
		// exit;
		$booking_details = $this->convert_as_array($booking_details);
		$response['data']['booking_details'] = $booking_details;
		return $response;
	}
	private function format_transferv1_cancellation_details($cancellation_details)
	{
		$booking_cancellation_details = array();
		foreach($cancellation_details as $cancel_k => $cancel_v) {
			$booking_cancellation_details[$cancel_v['app_reference']][] = $cancel_v;
		}
		return $booking_cancellation_details;
	}
		/**
	 * Convert price details  into respective currency rate for sightseeing
	 * @param array $currency_rate
	 * @param unknown $itinerary_details
	 * @return number
	 */
	function display_currency_for_transferv1($currency_rate = array(),$itinerary_details){
		$conversion_rate = $currency_rate['currency_conversion_rate'];

		foreach($itinerary_details as $key => $sub_itinerary_details){
			//debug($sub_itinerary_details);

			$itinerary_details[$key]['total_fare'] =  $this->add_currencyrate($sub_itinerary_details['total_fare'],$conversion_rate);
			$itinerary_details[$key]['admin_commission'] =  $this->add_currencyrate($sub_itinerary_details['admin_commission'],$conversion_rate);
			$itinerary_details[$key]['agent_commission'] =  $this->add_currencyrate($sub_itinerary_details['agent_commission'],$conversion_rate);
			$itinerary_details[$key]['admin_tds'] =  $this->add_currencyrate($sub_itinerary_details['admin_tds'],$conversion_rate);
			$itinerary_details[$key]['agent_tds'] =  $this->add_currencyrate($sub_itinerary_details['agent_tds'],$conversion_rate);
			$itinerary_details[$key]['gst'] =  $this->add_currencyrate($sub_itinerary_details['gst'],$conversion_rate);
			$itinerary_details[$key]['admin_markup'] =  $this->add_currencyrate($sub_itinerary_details['admin_markup'],$conversion_rate);
			$itinerary_details[$key]['agent_markup'] =  $this->add_currencyrate($sub_itinerary_details['agent_markup'],$conversion_rate);

		}
		// debug($itinerary_details);
		// exit;
		return $itinerary_details;
	}
	private function format_payment_history_details($payment_details)
	{
		
		$payment_history_details = array();
		foreach($payment_details as $payment_k => $payment_v) {
			
			$payment_history_details[$payment_v['enquiry_reference_no']][] = $payment_v;
		}
		return $payment_history_details;
	}
	function format_package_booking_data($complete_booking_details, $module)
	{
		$response['status']	= SUCCESS_STATUS;
		$response['data']	= array();
		$booking_details = array();
		$currency_obj = new Currency();
		$master_booking_details = $complete_booking_details['data']['booking_details'];
		
	 // debug($complete_booking_details);exit;
		$itinerary_details = $this->format_itinerary_details($complete_booking_details['data']['booking_itinerary_details']);
	    //echo debug($itinerary_details);exit;
		$payment_details = @$this->format_payment_details($complete_booking_details['data']['payment_details']);
		$customer_details = $this->format_customer_details($complete_booking_details['data']['booking_customer_details']);
		$temp_booking_details = $this->format_temp_booking_details($complete_booking_details['data']['temp_booking_details']);
		$payment_history_details = $this->format_payment_history_details($complete_booking_details['data']['payment_history_details']);
		//$cancellation_details = $this->format_sightseeing_cancellation_details($complete_booking_details['data']['cancellation_details']);
		//debug($complete_booking_details);exit("Fasdf");
		$booking_payment_details = array();
		foreach($master_booking_details as $book_k => $book_v) {
			$core_booking_details = $book_v;
			$attributes = (array)json_decode($book_v['attributes']);
			
			$app_reference = $core_booking_details['app_reference'];
			
			$booking_itinerary_details = $itinerary_details[$app_reference];
			$booking_customer_details = $customer_details[$app_reference];
			$booking_temp_details = $temp_booking_details[$app_reference];
			$booking_payment_history_details = $payment_history_details[$app_reference];
			$booking_cancellation_details = @$cancellation_details[$app_reference];	
			$booking_payment_details = @$payment_details[$app_reference];	
			$package_details = $GLOBALS['CI']->custom_db->single_table_records('tours', '*', array('id' => $attributes['tour_id']))['data'][0];
			$booked_user_details = $GLOBALS['CI']->custom_db->single_table_records('user', 'agency_name,phone,first_name,user_id,uuid', array('user_id' => $book_v['created_by_id']))['data'][0];
			
			//debug($booking_temp_details);exit;
		//	$temp_booking_details = $GLOBALS['CI']->custom_db->single_table_records('temp_booking', '*', array('id' => $attributes['tour_id']))['data'][0];
			if($module == 'b2b' || $module == 'b2c' ){
				//get the converted currency rate for booking transaction
				$booking_itinerary_details = $this->display_currency_for_sightseeing($book_v,$itinerary_details[$app_reference]);
				//get the converted currency rate for convenience amount
				$core_booking_details = $this->display_convenience_amount($book_v);
			}

			// debug($core_booking_details);
			// exit;
			//Calculating Price
			$fare = 0;
			$admin_markup = 0;
			$agent_markup = 0;
			$agent_commission = 0;
			$admin_commission = 0;
			$admin_tds = 0;
			$agent_tds = 0;
			$admin_net_fare_markup = 0;
			$gst = 0;
			foreach($booking_itinerary_details as $itinerary_k => $itinerary_v) {

				$fare += $itinerary_v['total_fare'];
				$admin_markup += floatval($itinerary_v['admin_markup']);
				$agent_markup += floatval($itinerary_v['agent_markup']);
				$agent_commission +=floatval($itinerary_v['agent_commission']);
				$admin_commission +=floatval($itinerary_v['admin_commission']);
				$admin_tds +=floatval($itinerary_v['admin_tds']);
				$agent_tds +=floatval($itinerary_v['agent_tds']);
				$gst += floatval($itinerary_v['gst']);
				//$admin_net_fare_markup +=floatval($itinerary_v['admin_net_markup']);

			}
			//PaxCount
			$adult_count = 0;
			$child_count = 0;
			$infant_count =0;
			$youth_count = 0;
			$senior_count = 0;
			foreach($booking_customer_details as $customer_k => $customer_v) {
				$pax_type = $customer_v['pax_type'];
				if($pax_type == 'Adult') {
					$adult_count++;
				} else if($pax_type == 'Child'){
					$child_count++;
				}
				elseif($pax_type=='Infant'){
					$infant_count++;
				}
				elseif($pax_type=='Senior'){
					$senior_count++;
				}
				elseif ($pax_type=='Youth') {
					$youth_count++;
				}
			}
			foreach($booking_temp_details as $temp_k => $temp_v) {
				$booking_temp_details[$temp_k]['book_attributes']=unserialized_data($temp_v['book_attributes']);
				$booking_temp_details[$temp_k]['pre_booking_params'] = json_decode(base64_decode($booking_temp_details[$temp_k]['pre_booking_params']));
			}
				//debug($booking_temp_details);exit;
			//Formatiing the data
			//Booking Details
			$attributes = json_decode($core_booking_details['attributes'], true);
			
			$core_booking_details['ProductImage'] = $attributes['ProductImage'];
			$core_booking_details['Destination'] = $attributes['Destination'];
			$core_booking_details['DeparturePointAddress'] = $attributes['DeparturePointAddress'];
			$core_booking_details['TM_Cancellation_Policy'] = $attributes['TM_Cancellation_Policy'];
			
			$core_booking_details['adult_count'] = $adult_count;
			$core_booking_details['child_count'] = $child_count;
			$core_booking_details['youth_count'] = $youth_count;
			$core_booking_details['senior_count'] = $senior_count;
			$core_booking_details['infant_count'] = $infant_count;

			$core_booking_details['fare'] = roundoff_number($fare);
			$core_booking_details['admin_markup'] = roundoff_number($admin_markup);
			$core_booking_details['gst'] = roundoff_number($gst);
			//$core_booking_details['admin_net_markup'] =roundoff_number($admin_net_fare_markup);
			$core_booking_details['agent_markup'] = roundoff_number($agent_markup);
			$core_booking_details['admin_commission'] = roundoff_number($admin_commission);
			$core_booking_details['agent_commission'] = roundoff_number($agent_commission);
			$core_booking_details['admin_tds'] = roundoff_number($admin_tds);
			$core_booking_details['agent_tds'] = roundoff_number($agent_tds);
			$net_commission = ($admin_commission+$agent_commission);
			$net_commission_tds = ($admin_tds+$agent_tds);
			$core_booking_details['net_commission'] = roundoff_number($net_commission);
			$core_booking_details['net_commission_tds'] = roundoff_number($net_commission_tds);
			$core_booking_details['net_fare'] = roundoff_number($fare-$net_commission);

			$core_booking_details['admin_net_fare'] = roundoff_number($fare-$admin_commission+$admin_tds);
			// debug($core_booking_details);
			// exit;

			$core_booking_details['product_total_price'] =roundoff_number($core_booking_details['admin_net_fare']+$admin_markup +$agent_markup);//without convience fee

			$admin_buying_price = $this->admin_buying_price($core_booking_details);
			$core_booking_details['admin_buying_price'] = roundoff_number($admin_buying_price[0]);	
			
			//debug($core_booking_details);
			//exit;
			if($module == 'b2b' || $module=='admin') {

				//$agent_buying_price = $this->agent_buying_price($core_booking_details);
				$agent_buying_price = $core_booking_details['basic_fare'] - $booking_temp_details[0]['book_attributes']['total_agent_markup'];
				$core_booking_details['agent_buying_price'] = roundoff_number($agent_buying_price);
				

			} else {
				//$core_booking_details['fare']=$core_booking_details['admin_net_fare'];
				$core_booking_details['agent_buying_price'] = 0;
				
			
			}
			$grand_total = $this->total_fare($core_booking_details, $module);
			// debug($grand_total);
			// exit;
			$core_booking_details['grand_total'] = roundoff_number($grand_total[0]);
			
			//get currency
			$currency = admin_base_currency();
			if($module == 'b2b' || $module == 'b2c' ){
			    $currency = $book_v['currency'];
			}
			$core_booking_details['currency'] = $currency_obj->get_currency_symbol($currency);
			
			$core_booking_details['voucher_date'] = app_friendly_absolute_date($core_booking_details['created_datetime']);
			//Lead Pax Details
			$core_booking_details['cutomer_city'] = $attributes['billing_city'];
			$core_booking_details['cutomer_zipcode'] = $attributes['billing_zipcode'];
			$core_booking_details['cutomer_address'] = $attributes['address'];
			$core_booking_details['cutomer_country'] = $attributes['billing_country'];
			$core_booking_details['destination_name'] = $attributes['Destination'];
			$core_booking_details['lead_pax_name'] = $booking_customer_details[0]['first_name'].' '.$booking_customer_details[0]['last_name'];
			$core_booking_details['lead_pax_phone_number'] = $core_booking_details['phone_number'];
			$core_booking_details['lead_pax_email'] = $core_booking_details['email']; 
			//Domain Details
			$domain_details = $this->domain_details($core_booking_details['domain_origin']);
			$core_booking_details['domain_name'] = $domain_details['domain_name'];
			$core_booking_details['domain_ip'] = $domain_details['domain_ip'];
			$core_booking_details['domain_key'] = $domain_details['domain_key'];
			$core_booking_details['theme_id'] = $domain_details['theme_id'];
			$core_booking_details['domain_logo'] = $domain_details['domain_logo'];
			//Formating the data
			//Booking Details
			$booking_details[$app_reference] = $core_booking_details;
			//Itenary Details
			$booking_details[$app_reference]['itinerary_details'] = $booking_itinerary_details;
			//Customer Details
			$booking_details[$app_reference]['customer_details'] = $booking_customer_details;
			$booking_details[$app_reference]['temp_booking_details'] = $booking_temp_details;
			$booking_details[$app_reference]['cancellation_details'] = $booking_cancellation_details;
			//Payment Details
			$booking_details[$app_reference]['booking_payment_details'] = $booking_payment_details;
			$booking_details[$app_reference]['booking_payment_history_details'] = $booking_payment_history_details;
			$booking_details[$app_reference]['booking_package_details'] = $package_details;
			$booking_details[$app_reference]['booked_user_details'] = $booked_user_details;
			$booking_details[$app_reference]['attributes'] = $attributes;
		}
	//	debug($booking_details);
	//	 exit;
		$booking_details = $this->convert_as_array($booking_details);
		$response['data']['booking_details'] = $booking_details;
		return $response;
	}
}
