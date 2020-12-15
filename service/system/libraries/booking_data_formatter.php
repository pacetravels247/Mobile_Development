<?php
/** 
 *
 * Formates the Booking Data in the application
 *
 * @package	Provab
 * @subpackage	provab
 * @category	Libraries
 * @author		Jaganath<jaganath.provab@gmail.com>
 * @link		http://www.provab.com
 */
class Booking_Data_Formatter {
	public function __construct()
	{
		
	}
	/**
	 * Jaganath
	 * @param array bus booking_details
	 */


	function format_bus_booking_data($complete_booking_details, $module)
	{
		$response['status']	= SUCCESS_STATUS;
		$response['data']	= array();
		$booking_details = array();
		$currency_obj = new Currency();
		//debug($complete_booking_details); die;
		$master_booking_details = $complete_booking_details['data']['booking_details'];
		$itinerary_details = $this->format_itinerary_details($complete_booking_details['data']['booking_itinerary_details']);		
		$customer_details = $this->format_customer_details($complete_booking_details['data']['booking_customer_details']);
		//echo debug($master_booking_details);
		//echo debug($customer_details);exit;
		$domain_markup = 0;
		foreach($master_booking_details as $book_k => $book_v) {
		
			$core_booking_details = $book_v;
			$app_reference = $core_booking_details['app_reference'];
			$booking_itinerary_details = $itinerary_details[$app_reference];
			$booking_customer_details = $customer_details[$app_reference];

			//Calculating Price
			$fare = 0;
			$admin_commission = 0;
			$agent_commission = 0;
			$admin_tds = 0;
			$agent_tds = 0;
			$agent_gst = 0;
			$admin_markup = 0;
			$agent_markup = 0;
			$seat_numbers = '';
			
			//Domain Markup
			
			$domain_markup += $book_v['domain_markup'];
			
			foreach($booking_customer_details as $customer_k => $customer_v) {
				
				$fare 				+= floatval($customer_v['fare']);
				$admin_commission 	+= floatval($customer_v['admin_commission']);
				$agent_commission 	+= floatval($customer_v['agent_commission']);
				$admin_tds 			+= floatval($customer_v['admin_tds']);
				$agent_tds 			+= floatval($customer_v['agent_tds']);
				$agent_gst 			+= floatval($customer_v['agent_gst']);
				$admin_markup 		+= floatval($customer_v['admin_markup']);
				$agent_markup 		+= floatval($customer_v['agent_markup']);
				$seat_numbers .= trim($customer_v['seat_no']).',';
			}
			$core_booking_details['fare'] = roundoff_number($fare);
			$core_booking_details['admin_commission'] = roundoff_number($admin_commission);
			$core_booking_details['agent_commission'] = roundoff_number($agent_commission);
			$core_booking_details['admin_tds'] = roundoff_number($admin_tds);
			$core_booking_details['agent_tds'] = roundoff_number($agent_tds);
			$core_booking_details['agent_gst'] = roundoff_number($agent_gst);
			$core_booking_details['admin_markup'] = roundoff_number($admin_markup);
			$core_booking_details['agent_markup'] = roundoff_number($agent_markup);
			$admin_buying_price = $this->admin_buying_price($core_booking_details);
			$core_booking_details['admin_buying_price'] = roundoff_number($admin_buying_price[0]);
			
			$grand_total =$core_booking_details['total_fare']; // $this->total_fare($core_booking_details, $module);
			$core_booking_details['grand_total'] = $grand_total;
			
			//currency
			$currency = get_application_default_currency();
			
			if(is_domain_user()  == true)
			{//Client
			$core_booking_details['agent_comm_fare'] = roundoff_number($fare + $domain_markup);
			$core_booking_details['agent_net_fare'] = roundoff_number($fare + $domain_markup- $agent_commission + $agent_tds + $agent_gst);
			}else{
				//Super Admin
				$core_booking_details['admin_comm_fare'] = roundoff_number($fare);
				$core_booking_details['admin_net_fare'] = roundoff_number($fare-$admin_commission-$agent_commission+$admin_tds+$agent_tds+$agent_gst);
				$core_booking_details['agent_comm_fare'] = roundoff_number($fare + $domain_markup);
				$core_booking_details['agent_net_fare'] = roundoff_number($fare + $domain_markup- $agent_commission + $agent_tds + $agent_gst);
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
			//Domain Details
			$domain_details = $this->domain_details($core_booking_details['domain_origin']);
			$core_booking_details['domain_name'] = $domain_details['domain_name'];
			$core_booking_details['domain_ip'] = $domain_details['domain_ip'];
			$core_booking_details['domain_key'] = $domain_details['domain_key'];
			$core_booking_details['theme_id'] = $domain_details['theme_id'];
			$core_booking_details['booked_date'] = app_friendly_absolute_date($core_booking_details['created_datetime']);
			//Formating the data
			//Booking Details 	
			$booking_details[$book_k] = $core_booking_details;
			//Itinerary Details
			$booking_details[$book_k]['booking_itinerary_details'] = $booking_itinerary_details;
			//Customer Details
			$booking_details[$book_k]['booking_customer_details'] = $booking_customer_details;
		}
		$booking_details = $this->convert_as_array($booking_details);
		$response['data']['booking_details'] = $booking_details;
		//debug($booking_details); die;
		return $response;
	}
	
	/**
	 * Jaganath
	 * @param array hotel booking_details
	 */
	function format_hotel_booking_data($complete_booking_details, $module)
	{
		$currency_obj = new Currency();
		foreach($complete_booking_details as $key => $book_v) {	
			$complete_booking_details[$key]['admin_currency'] = $book_v['currency'];
			    $complete_booking_details[$key]['total_fare'] = $book_v['total_fare'];
				$complete_booking_details[$key]['domain_markup'] = $book_v['domain_markup'];
				$currency = get_application_default_currency();
				
				$admin_net_fare = ($book_v['total_fare']);
				$agent_net_fare = ($book_v['total_fare']+$book_v['domain_markup']);
				
				if(is_domain_user() == true){
					$complete_booking_details[$key]['total_fare'] =  $this->add_currencyrate($book_v['total_fare'],$book_v['currency_conversion_rate']);
					$complete_booking_details[$key]['domain_markup'] =  $this->add_currencyrate($book_v['domain_markup'],$book_v['currency_conversion_rate']);
					$complete_booking_details[$key]['level_one_markup'] =  $this->add_currencyrate($book_v['level_one_markup'],$book_v['currency_conversion_rate']);
					
					$complete_booking_details[$key]['admin_net_fare'] = $this->add_currencyrate($admin_net_fare,$book_v['currency_conversion_rate']);
					$complete_booking_details[$key]['agent_net_fare'] = $this->add_currencyrate($agent_net_fare,$book_v['currency_conversion_rate']);
				
					$currency = domain_base_currency();
				} else {
					$complete_booking_details[$key]['admin_net_fare'] = $admin_net_fare;
					$complete_booking_details[$key]['agent_net_fare'] = $agent_net_fare;
				}
					
				$complete_booking_details[$key]['currency'] = $currency;// $currency_obj->get_currency_symbol($currency);//FIXME: Remove the hardcoded value "INR" Later
		}
		return $complete_booking_details;
	}

	function format_hotel_booking_datas($complete_booking_details, $module)
	{
		//debug($complete_booking_details); die;
		$response['status']	= SUCCESS_STATUS;
		$response['data']	= array();
		$booking_details = array();
		$currency_obj = new Currency();
		$master_booking_details = $complete_booking_details['data']['booking_details'];
	   //echo debug($master_booking_details);exit;
		$itinerary_details = $this->format_itinerary_details($complete_booking_details['data']['booking_itinerary_details']);
	    //echo debug($itinerary_details);exit;
	
		$customer_details = $this->format_customer_details($complete_booking_details['data']['booking_pax_details']);
		$cancellation_details = $this->format_hotel_cancellation_details($complete_booking_details['data']['cancellation_details']);
		foreach($master_booking_details as $book_k => $book_v) {
			//debug($book_v); die;
			$core_booking_details = $book_v;
			
			$app_reference = $core_booking_details['app_reference'];
			$booking_itinerary_details = $itinerary_details[$app_reference];
			$booking_customer_details = $customer_details[$app_reference];
			$booking_cancellation_details = @$cancellation_details[$app_reference];		
			if(is_domain_user() == true){
				//get the converted currency rate for booking transaction
				$booking_itinerary_details = $this->display_currency_for_hotel($book_v,$itinerary_details[$app_reference]);
				//get the converted currency rate for convenience amount
				$core_booking_details = $this->display_convenience_amount($book_v);
			}

			//Calculating Price
			$fare = 0;
			$admin_markup = 0;
			$agent_markup = 0;
			foreach($booking_itinerary_details as $itinerary_k => $itinerary_v) {
				$fare += $itinerary_v['total_fare'];
				$admin_markup += floatval($itinerary_v['domain_markup']);
				$agent_markup += floatval($itinerary_v['AgentMarkUp']);
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
			$core_booking_details['hotel_image'] = @$attributes['HotelImage'];
			$core_booking_details['hotel_location'] = $booking_itinerary_details[0]['location'];
			$core_booking_details['hotel_address'] = $attributes['Address'];
			$core_booking_details['cancellation_policy'] = $attributes['LastCancellationDate'];
			$core_booking_details['total_nights'] = $total_nights;
			$core_booking_details['total_rooms'] = count($booking_itinerary_details);
			$core_booking_details['adult_count'] = $adult_count;
			$core_booking_details['child_count'] = $child_count;
			$core_booking_details['fare'] = roundoff_number($fare);
			$core_booking_details['admin_markup'] = roundoff_number($admin_markup);
			$core_booking_details['agent_markup'] = roundoff_number($agent_markup);
			$admin_buying_price = $this->admin_buying_price($core_booking_details);
			$core_booking_details['admin_buying_price'] = roundoff_number($admin_buying_price[0]);
			if(is_domain_user() == true) {
				$agent_buying_price = $this->agent_buying_price($core_booking_details);
				$core_booking_details['agent_buying_price'] = roundoff_number($agent_buying_price[0]);
			} else {
				$core_booking_details['agent_buying_price'] = 0;
			}
			
			//debug($core_booking_details['total_fare']); die;
			$grand_total = $fare;//$this->total_fare($core_booking_details, $module);
			$core_booking_details['grand_total'] = roundoff_number($grand_total);
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
			$booking_details = $core_booking_details;

			//Itenary Details
			$booking_details['itinerary_details'] = $booking_itinerary_details;
			//Customer Details
			$booking_details['customer_details'] = $booking_customer_details;
			$booking_details['cancellation_details'] = $booking_cancellation_details;

		}
	
		$booking_details = $this->convert_as_array($booking_details);
		$response['data']['booking_details'] = $booking_details;
		
		return $response;
	}
	
	/**
	 * Jaganath
	 * @param array $booking_details
	 */
	function format_flight_booking_data($complete_booking_details, $module)
	{
		
		$response['status']	= SUCCESS_STATUS;
		$response['data']	= array();
		
		$booking_details = array();
		$currency_obj = new Currency();
		$master_booking_details = $complete_booking_details['data']['booking_details'];
               
                
		$itinerary_details = $this->format_itinerary_details($complete_booking_details['data']['booking_itinerary_details']);
                
		$segment_details = $this->format_segment_details($complete_booking_details['data']['booking_itinerary_details']);
                
		$transaction_details = $this->format_flight_transaction_details($complete_booking_details['data']['booking_transaction_details']);		
             
		$customer_details = $this->format_flight_customer_details($complete_booking_details['data']['booking_customer_details'], $complete_booking_details['data']['cancellation_details']);
          // debug($transaction_details);
          // debug($customer_details);
          // exit;      
		$extra_service_details = $this->format_flight_extra_service_details($module, is_domain_user(),@$master_booking_details[0]['currency_conversion_rate'], @$complete_booking_details['data']['baggage_details'], @$complete_booking_details['data']['meal_details'], @$complete_booking_details['data']['seat_details']);
		
		if(isset($complete_booking_details['data']['exception_log_details']) == true && valid_array($complete_booking_details['data']['exception_log_details']) == true){
			$exception_log_details = $this->format_exception_log_details($complete_booking_details['data']['exception_log_details']);
		} else {
			$exception_log_details = array();
		}
		//debug($master_booking_details);exit;
		foreach($master_booking_details as $book_k => $book_v) {
			$core_booking_details = $book_v;
			$currency_conversion_rate = $book_v['currency_conversion_rate'];
			$app_reference = $core_booking_details['app_reference'];
			if(isset($itinerary_details[$app_reference])){
				$booking_itinerary_details = $itinerary_details[$app_reference];
			} else {
				$booking_itinerary_details = array();
			}
			
			$booking_exception_log_details = (isset($exception_log_details[$app_reference]) == true ? $exception_log_details[$app_reference] : array());
			if(isset($transaction_details[$app_reference])) {
				$booking_transaction_details = $transaction_details[$app_reference];
                              //  if($_SERVER['REMOTE_ADDR']=="182.156.244.142")

                               
				//check the module
					if(is_domain_user() == true){
                                          
						$fare = ($book_v['currency_conversion_rate']*$book_v['total_fare']);
                                                 
							//get the converted currency rate for booking transaction  
							$booking_transaction_details = $this->display_currency_for_flight($book_v,$transaction_details[$app_reference]);
							//get the converted currency rate for convenience amount
							//debug($booking_transaction_details);
							$book_v = $this->display_convenience_amount($book_v);
                                                       
						}
					} 
					
		             $fare = 0;
					 $domain_markup = 0;
					 $level_one_markup = 0;
					 $admin_comm = 0;
					 $agent_comm = 0;
					 $agent_tds = 0;
					 $admin_tds = 0;
					 $pnr = '';
					 $booking_id = '';
					 $booking_api = '';
					 $fare_breakup = array();
					 // debug($booking_transaction_details);exit;
		          foreach($booking_transaction_details as $transaction_k => $transaction_v) {
		          		$pax_fares = @$customer_details[$transaction_v['origin']];
						$tr_admin_comm = $transaction_v['admin_commission'];
						$tr_admin_tds = $transaction_v['admin_tds']; 
						$tr_agent_comm = $transaction_v['agent_commission'];
						$tr_agent_tds = $transaction_v['agent_tds'];
						$tr_admin_markup = $transaction_v['domain_markup'];
						$tr_fare = $transaction_v['total_fare'];
						
						//Assign Segment Details for the Transaction
						//$booking_transaction_details[$transaction_k]['segment_details'] = $segment_details;
						$booking_transaction_details[$transaction_k]['segment_details'] = array();//FIXME: assign it properly
						$fare 				+= $tr_fare;
						$domain_markup	+=$tr_admin_markup;
						$level_one_markup 	+=$transaction_v['level_one_markup'];
						//Assign Pax Details for the Transaction
						 $booking_transaction_details[$transaction_k]['booking_customer_details'] = @$customer_details[$transaction_v['origin']];
						 
		          		//Assign Baggage Details for the Transaction
						if(isset($extra_service_details['baggage_details'][$transaction_v['origin']]) == true && valid_array($extra_service_details['baggage_details'][$transaction_v['origin']]) == true){
							$booking_transaction_details[$transaction_k]['extra_service_details']['baggage_details'] = $extra_service_details['baggage_details'][$transaction_v['origin']];
						}
						//Assign Meal Details for the Transaction
						if(isset($extra_service_details['meal_details'][$transaction_v['origin']]) == true && valid_array($extra_service_details['meal_details'][$transaction_v['origin']]) == true){
							$booking_transaction_details[$transaction_k]['extra_service_details']['meal_details'] = $extra_service_details['meal_details'][$transaction_v['origin']];
						}
						//Assign Seat Details for the Transaction
						if(isset($extra_service_details['seat_details'][$transaction_v['origin']]) == true && valid_array($extra_service_details['seat_details'][$transaction_v['origin']]) == true){
							$booking_transaction_details[$transaction_k]['extra_service_details']['seat_details'] = $extra_service_details['seat_details'][$transaction_v['origin']];
						}
						//Calculating Price
						$admin_comm += $tr_admin_comm;
						$agent_comm += $tr_agent_comm;
						$agent_tds += $tr_agent_tds;
						$admin_tds += $tr_admin_tds;
						$pnr .= $transaction_v['pnr'].'/';
						$booking_id .= $transaction_v['book_id'].'/';
						$booking_api_name = str_ireplace('- Flight', '', $transaction_v['booking_api_name']);
						//Booking API name
						if(count($booking_transaction_details) == 2 && empty($booking_api_name) == false){
							if($transaction_k == 0){
								$booking_api .= 'ONW: '.$booking_api_name.'<br/>';
							} else {
								$booking_api .= 'RET: '.$booking_api_name;
							}
						} else {
							$booking_api = $booking_api_name;
						}
						if($transaction_v['booking_source'] == 'PTBSID0000000007'){
							$created_time = strtotime($book_v['created_datetime']);
							$check_time = strtotime('2018-11-13 15:00:00');
							$fare_breakup_transaction = json_decode($transaction_v['fare_breakup'], true);
							$passenger_breakup = $fare_breakup_transaction['PassengerBreakup'];
							$total_pax_base_fare = 0;
							$total_pax_tax = 0;
							foreach($passenger_breakup as $pass_type => $p_fare){
								if($created_time > $check_time){
									// echo 'yes';exit;
									$pass_tax = $p_fare['Tax'];
								}
								else{
									// echo 'no';exit;
									$pass_tax = $p_fare['Tax']*$p_fare['PassengerCount'];
								}
								
								$total_pax_base_fare += $p_fare['BasePrice'];
								$total_pax_tax += $pass_tax;

							}
							$pax_fares_travelport[0]['Fare']['BasePrice'] = $total_pax_base_fare;
							$pax_fares_travelport[0]['Fare']['Tax'] = $total_pax_tax;
							$pax_fares_travelport[0]['Fare']['TotalPrice'] = $total_pax_base_fare+$total_pax_tax;
							
							$fare_breakup = $this->flight_fare_breakup($pax_fares_travelport, $tr_admin_markup, $tr_admin_comm, $tr_agent_comm, $tr_admin_tds, $tr_agent_tds, $currency_conversion_rate);

						}
						else{
							$fare_breakup = $this->flight_fare_breakup($pax_fares, $tr_admin_markup, $tr_admin_comm, $tr_agent_comm, $tr_admin_tds, $tr_agent_tds, $currency_conversion_rate);

						}
						// debug($fare_breakup);exit;
						//Fare Breakup
						// $fare_breakup = $this->flight_fare_breakup($pax_fares, $tr_admin_markup, $tr_admin_comm, $tr_agent_comm, $tr_admin_tds, $tr_agent_tds, $currency_conversion_rate);
						// debug($fare_breakup);exit;
						$booking_transaction_details[$transaction_k]['fare_breakup'] = $fare_breakup;
						$booking_transaction_details[$transaction_k]['fare_breakup']['TotalFare'] = roundoff_number($tr_fare + $tr_admin_markup- $tr_agent_comm + $tr_agent_tds);
					}
					
			$core_booking_details['admin_currency'] = $book_v['currency'];
			$core_booking_details['admin_commission'] = roundoff_number($admin_comm);
			$core_booking_details['agent_commission'] = roundoff_number($agent_comm);
			$core_booking_details['admin_tds'] = roundoff_number($admin_tds);
			$core_booking_details['agent_tds'] = roundoff_number($agent_tds);
                
			$core_booking_details['total_fare'] = roundoff_number($fare);
			$core_booking_details['domain_markup'] = roundoff_number($domain_markup);
			$core_booking_details['level_one_markup'] = roundoff_number($level_one_markup);	
			$core_booking_details['pnr'] = rtrim($pnr, '/');
			$core_booking_details['booking_id'] = rtrim($booking_id, '/');
			$core_booking_details['booking_api'] = $booking_api;
			
			$lead_pax_details = @$booking_transaction_details[0]['booking_customer_details'][0];
			// /echo debug($lead_pax_details);exit;
			$core_booking_details['lead_pax_name'] = $lead_pax_details['title'].' '.$lead_pax_details['first_name'].' '.$lead_pax_details['last_name'];
			$core_booking_details['lead_pax_phone_number'] = $core_booking_details['phone'];
			$core_booking_details['lead_pax_email'] = $core_booking_details['email'];
			if(is_domain_user()  == true)
			{//Client
				$core_booking_details['agent_comm_fare'] = roundoff_number($fare + $domain_markup); 
				$core_booking_details['agent_net_fare'] = roundoff_number($fare + $domain_markup- $agent_comm + $agent_tds); 
			}else{
				//Super Admin
				$core_booking_details['admin_comm_fare'] = roundoff_number($fare);
				$core_booking_details['admin_net_fare'] = roundoff_number($fare-$admin_comm-$agent_comm+$admin_tds+$agent_tds);
				$core_booking_details['agent_comm_fare'] = roundoff_number($fare + $domain_markup); 
				$core_booking_details['agent_net_fare'] = roundoff_number($fare + $domain_markup- $agent_comm + $agent_tds); 
			}
			
			$currency = get_application_default_currency();
			
			if(is_domain_user() == true){				
				
				$currency = domain_base_currency();
			}	
	

           	$core_booking_details['currency'] = $currency_obj->get_currency_symbol($currency);
           	//$core_booking_details['admin_currency'] = get_application_default_currency();
           //	$attributes = json_decode($core_booking_details['attributes'], true);
           	$booking_details[$app_reference] = $core_booking_details;
			$booking_details[$app_reference]['booking_itinerary_details'] = $booking_itinerary_details;
			//Customer Details
			$booking_details[$app_reference]['booking_transaction_details'] = $booking_transaction_details;
			$booking_details[$app_reference]['exception_log_details'] = $booking_exception_log_details;
		  
		}
		$booking_details = $this->convert_as_array($booking_details);
		$response['data']['booking_details'] = $booking_details;
                
                
                
		return $response;
		
	}
	
	/**
	 * 
	 * Fare Breakup
	 * @param unknown_type $pax_fares
	 * @param unknown_type $admin_markup
	 * @param unknown_type $admin_comm
	 * @param unknown_type $agent_comm
	 * @param unknown_type $admin_tds
	 * @param unknown_type $agent_tds
	 */
	private function flight_fare_breakup($pax_fares, $admin_markup, $admin_comm, $agent_comm, $admin_tds, $agent_tds, $currency_conversion_rate)
	{
		$fare_breakup = array();
		if(valid_array($pax_fares) == true){
			$BaseFare = 0;
			$Tax = 0;
			$OtherCharges = 0;
			$price_data_exists = true;
			foreach ($pax_fares as $k => $v) {
				if(valid_array($v['Fare']) == true){
					if(isset($v['Fare']['BaseFare'])){
						$BaseFare += 		$v['Fare']['BaseFare'];
					} else if(isset($v['Fare']['BasePrice'])){
						$BaseFare += 		$v['Fare']['BasePrice'];
					}
					$Tax += 			$v['Fare']['Tax'];
					$OtherCharges +=	@$v['Fare']['OtherCharges'];
				} else {
					$price_data_exists = false;
				}
			}
			//FIXME: for offline booking data is not storing
			if($price_data_exists == true){
				if(is_domain_user() == true){
					$BaseFare = 	round(($BaseFare*$currency_conversion_rate), 3);
					$Tax = 			round(($Tax*$currency_conversion_rate), 3);
					$OtherCharges =	round(($OtherCharges*$currency_conversion_rate), 3);
					$agent_comm = 	round(($agent_comm*$currency_conversion_rate), 3);
					$agent_tds = 	round(($agent_tds*$currency_conversion_rate), 3);
					$admin_markup = round(($admin_markup*$currency_conversion_rate), 3);
				}
				$fare_breakup['BaseFare'] = 		$BaseFare;
				$fare_breakup['Tax'] = 				$Tax+$admin_markup;
				$fare_breakup['OtherCharges'] = 	$OtherCharges;
				$fare_breakup['AgentCommission'] = 	$agent_comm;
				$fare_breakup['AgentTDS'] = 		$agent_tds;
				
				if(is_domain_user() == true){
					$fare_breakup['AdminTDS'] = 		$admin_tds;
					$fare_breakup['AdminCommission'] = 	$admin_comm;
				}
			}
		}
		return $fare_breakup;
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
				$transaction_details[$key]['domain_markup'] =  $this->add_currencyrate($sub_transaction_details['domain_markup'],$conversion_rate);
				$transaction_details[$key]['level_one_markup'] =  $this->add_currencyrate($sub_transaction_details['level_one_markup'],$conversion_rate);				
				$transaction_details[$key]['agent_commission'] =  $this->add_currencyrate($sub_transaction_details['agent_commission'],$conversion_rate);
				$transaction_details[$key]['agent_tds']  =  $this->add_currencyrate($sub_transaction_details['agent_tds'],$conversion_rate);
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
	//debug($v_book); die;
		$v_book['convinence_amount'] = "0"; //$this->add_currencyrate($v_book['convinence_amount'],$v_book['currency_conversion_rate']);
		$v_book['discount'] = "0" ;// $this->add_currencyrate($v_book['discount'],$v_book['currency_conversion_rate']);
		return $v_book;
	}
	
	/**Jaganath
	 * Get Domain Details
	 * @param unknown_type $domain_origin
	 */
	private function domain_details($domain_origin)
	{
		$domain_details = $GLOBALS['CI']->custom_db->single_table_records('domain_list', '*', array('origin' => intval($domain_origin)));
		return $domain_details['data'][0];
	}
	/**
	 * Jaganath
	 * Returns Total Fare
	 * @param array $fare_details
	 */
	function total_fare($fare_details, $module)
	{
		$fare_details = force_multple_data_format($fare_details);
		$total_fare = array();
		if($module == 'b2c') {//B2C Total Fare
			foreach($fare_details as $k => $v) {
				$fare = (isset($v['fare']) == true ? $v['fare'] : $v['total_fare']);
				$total_fare[$k] = roundoff_number($fare+$v['admin_markup']+$v['convinence_amount']-$v['discount']);
			}
		} else if($module == 'b2b') {//B2B Total Fare
			foreach($fare_details as $k => $v) {
				$fare = (isset($v['fare']) ? $v['fare'] : $v['total_fare']);
				$total_fare[$k] = roundoff_number($fare+$v['admin_markup']+$v['convinence_amount']+$v['agent_markup']);
			}
		}else if($module == 'admin') {//B2B Total Fare
			foreach($fare_details as $k => $v) {
				$fare = (isset($v['fare']) ? $v['fare'] : $v['total_fare']);
				$total_fare[$k] = roundoff_number($fare+$v['admin_markup']+$v['convinence_amount']+$v['agent_markup']-$v['discount']);
			}
		}
		return $total_fare;
	}
	/**
	 * Jaganath
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
			$admin_buying_price[$k] = ($fare-$admin_commission);//FIXME:Check Calculation -- Jaganath
		}
		return $admin_buying_price;
	}
	/**
	 * Jaganath
	 * Returns Agent Buying Fare
	 * @param array $fare_details
	 */
	function agent_buying_price($fare_details)
	{
		$fare_details = force_multple_data_format($fare_details);
		$agent_buying_price = array();
		foreach($fare_details as $k => $v) {
			$fare = ($v['total_fare']);
			$agent_commission	= floatval(@$v['agent_commission']);
			$tds_on_commission	= floatval(@$v['agent_tds']);
			$agent_buying_price[$k] = $fare+$v['domain_markup']+$tds_on_commission-$agent_commission;
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
			$duration = $this->flight_segment_duration($itinerary_v['from_airport_code'], $itinerary_v['to_airport_code'], $itinerary_v['arrival_datetime'], $itinerary_v['departure_datetime']);
			$duration=$duration*60;
            $itinerary_v['total_duration'] =get_time_duration_label($duration);
			//$itinerary_v['total_duration'] = get_duration_label(calculate_duration(@$itinerary_v['arrival_datetime'], @$itinerary_v['departure_datetime']));
			$booking_itinerary_details[$itinerary_v['app_reference']][] = $itinerary_v;
		}
		return $booking_itinerary_details;
	}
	/**
	 * Formatting the Segment details based on Segment Indicator(Onward/Return)
	 */
	private function format_segment_details($itinerary_details)
	{
		$booking_segment_details = array();
		foreach($itinerary_details as $itinerary_k => $itinerary_v) {
			$duration = $this->flight_segment_duration($itinerary_v['from_airport_code'], $itinerary_v['to_airport_code'], $itinerary_v['arrival_datetime'], $itinerary_v['departure_datetime']);
			$duration=$duration*60;
            $itinerary_v['total_duration'] =get_time_duration_label($duration);
			//$itinerary_v['total_duration'] = get_duration_label(calculate_duration($itinerary_v['arrival_datetime'], $itinerary_v['departure_datetime']));
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
	private function format_hotel_cancellation_details($cancellation_details)
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
	 * Format Exception Log Details
	 * @param unknown_type $itinerary_details
	 */
	private function format_exception_log_details($exception_log_details)
	{
		$exception_details = array();
		foreach($exception_log_details as $exception_k => $exception_v) {
			$exception_details[$exception_v['app_reference']][] = $exception_v;
		}
		return $exception_details;
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
			$Fare = json_decode($customer_v['Fare'], true);
			//Assigning cancellation Details
			$temp_customer_details['Fare'] = $Fare;
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
		$data['bus_booking_count'] = $GLOBALS['CI']->bus_model->booking($condition, true);
		$data['hotel_booking_count'] = $GLOBALS['CI']->hotel_model->booking($condition, true);
		$data['flight_booking_count'] = $GLOBALS['CI']->flight_model->booking($condition, true);
		$response['data'] = $data;
		return $response;
	}
	/*
	 * Jaganath
	 * Recent Activities
	 */
	function format_recent_transactions($transaction_details, $module)
	{
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
	 * Jaganath
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
	 * Jaganath
	 * @param $booking_details
	 */
	function convert_as_array($booking_details)
	{
		if(count($booking_details) == 1) {
			$booking_details = array_values($booking_details);//FIXME: Jaganath
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

	function display_currency_for_hotel($currency_rate = array(),$itinerary_details){
		$conversion_rate = $currency_rate['currency_conversion_rate'];
		foreach($itinerary_details as $key => $sub_itinerary_details){
			$itinerary_details[$key]['total_fare'] =  $this->add_currencyrate($sub_itinerary_details['total_fare'],$conversion_rate);
			$itinerary_details[$key]['domain_markup'] =  $this->add_currencyrate($sub_itinerary_details['domain_markup'],$conversion_rate);
			$itinerary_details[$key]['RoomPrice'] =  $this->add_currencyrate($sub_itinerary_details['RoomPrice'],$conversion_rate);
			$itinerary_details[$key]['Discount'] =  $this->add_currencyrate($sub_itinerary_details['Discount'],$conversion_rate);
		}

		return $itinerary_details;
	}
	/**
	 * Group Flight Cancellation Details
	 * @param unknown_type $booking_transaction_details
	 */
	public function group_flight_cancellation_price_details($booking_transaction_details)
	{
		$total_cancellation_price_details = array();
		
		$total_cancellation_price_details['API_RefundedAmount'] = 0;
		$total_cancellation_price_details['API_CancellationCharge'] = 0;
		$total_cancellation_price_details['API_ServiceTaxOnRefundAmount'] = 0;
		$total_cancellation_price_details['API_SwachhBharatCess'] = 0;
		$total_cancellation_price_details['API_KrishiKalyanCess'] = 0;
		
		$total_cancellation_price_details['refund_amount'] = 0;
		$total_cancellation_price_details['cancellation_charge'] = 0;
		$total_cancellation_price_details['service_tax_on_refund_amount'] = 0;
		$total_cancellation_price_details['swachh_bharat_cess'] = 0;
		$total_cancellation_price_details['krishi_Kalyan_cess'] = 0;
		
		foreach ($booking_transaction_details as $k => $v){
			foreach ($v['booking_customer_details'] as $cust_k => $cust_v){
				if(isset($cust_v['cancellation_details']) == true && valid_array($cust_v['cancellation_details']) == true){
					$cancellation_details = $cust_v['cancellation_details'];
					
					if($cancellation_details['refund_status'] == 'PROCESSED'){
						$total_cancellation_price_details['API_RefundedAmount'] += $cancellation_details['API_RefundedAmount'];
						$total_cancellation_price_details['API_CancellationCharge'] += $cancellation_details['API_CancellationCharge'];
						$total_cancellation_price_details['API_ServiceTaxOnRefundAmount'] += $cancellation_details['API_ServiceTaxOnRefundAmount'];
						$total_cancellation_price_details['API_SwachhBharatCess'] += $cancellation_details['API_SwachhBharatCess'];
						$total_cancellation_price_details['API_KrishiKalyanCess'] += $cancellation_details['API_KrishiKalyanCess'];
						
						$total_cancellation_price_details['refund_amount'] += $cancellation_details['refund_amount'];
						$total_cancellation_price_details['cancellation_charge'] += $cancellation_details['cancellation_charge'];
						$total_cancellation_price_details['service_tax_on_refund_amount'] += $cancellation_details['service_tax_on_refund_amount'];
						$total_cancellation_price_details['swachh_bharat_cess'] += $cancellation_details['swachh_bharat_cess'];
						$total_cancellation_price_details['krishi_Kalyan_cess'] += $cancellation_details['krishi_Kalyan_cess'];
					}
				}
			}
		}
		return $total_cancellation_price_details;
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
     * Jaganath
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
    /**
	 * Elavarasi
	 * @param array Sightseeing booking_details
	 */

	function format_sightseeing_booking_data($complete_booking_details, $module)
	{
		$currency_obj = new Currency();
		
		foreach($complete_booking_details as $key => $book_v) {	

			
			$complete_booking_details[$key]['admin_currency'] = $book_v['currency'];
			    $complete_booking_details[$key]['total_fare'] = $book_v['total_fare'];
				$complete_booking_details[$key]['domain_markup'] = $book_v['domain_markup'];
				$currency = get_application_default_currency();
				
				$admin_cmm_fare = $book_v['total_fare'];

				//$admin_net_fare = ($book_v['net_fare']);

				
				$admin_commission = $book_v['admin_commission'] ;
				$admin_tds = $book_v['admin_tds'];
				$agent_commission = $book_v['agent_commission'];
				$agent_tds = $book_v['agent_tds'];
				$domain_markup = $book_v['domain_markup'];

				$admin_net_fare = roundoff_number($admin_cmm_fare-$admin_commission+$admin_tds);

				$agent_net_fare = roundoff_number($admin_cmm_fare-$agent_commission+$agent_tds+$domain_markup);
				$agent_comm_fare  = roundoff_number($admin_cmm_fare+$domain_markup);

				if(is_domain_user() == true){
					$complete_booking_details[$key]['admin_net_fare'] = $admin_net_fare;
					$complete_booking_details[$key]['agent_net_fare'] = $agent_net_fare;
					
				} else {
					$complete_booking_details[$key]['total_fare'] = $agent_net_fare;
					$complete_booking_details[$key]['domain_markup'] = $book_v['domain_markup'];
					$complete_booking_details[$key]['level_one_markup'] =$book_v['level_one_markup'];					
					$complete_booking_details[$key]['admin_net_fare'] = $admin_net_fare;
					$complete_booking_details[$key]['admin_comm_fare'] = $admin_cmm_fare;
					$complete_booking_details[$key]['admin_commission'] = $admin_commission;
					$complete_booking_details[$key]['agent_commission'] = $agent_commission;
					$complete_booking_details[$key]['admin_tds'] = $admin_tds;
					$complete_booking_details[$key]['agent_tds'] = $agent_tds;
					$complete_booking_details[$key]['agent_net_fare'] = $agent_net_fare;					
					$complete_booking_details[$key]['agent_comm_fare'] = $agent_comm_fare;
					// $complete_booking_details[$key]['total_fare'] =  $this->add_currencyrate($agent_net_fare,$book_v['currency_conversion_rate']);
					// $complete_booking_details[$key]['domain_markup'] =  $this->add_currencyrate($book_v['domain_markup'],$book_v['currency_conversion_rate']);
					// $complete_booking_details[$key]['level_one_markup'] =  $this->add_currencyrate($book_v['level_one_markup'],$book_v['currency_conversion_rate']);
					
					// $complete_booking_details[$key]['admin_net_fare'] = $this->add_currencyrate($admin_net_fare,$book_v['currency_conversion_rate']);
					// $complete_booking_details[$key]['admin_comm_fare'] = $this->add_currencyrate($admin_cmm_fare,$book_v['currency_conversion_rate']);
					// $complete_booking_details[$key]['admin_commission'] = $this->add_currencyrate($admin_commission,$book_v['currency_conversion_rate']);

					// $complete_booking_details[$key]['agent_commission'] = $this->add_currencyrate($agent_commission,$book_v['currency_conversion_rate']);

					// $complete_booking_details[$key]['admin_tds'] = $this->add_currencyrate($admin_tds,$book_v['currency_conversion_rate']);

					// $complete_booking_details[$key]['agent_tds'] = $this->add_currencyrate($agent_tds,$book_v['currency_conversion_rate']);


					// $complete_booking_details[$key]['agent_net_fare'] = $this->add_currencyrate($agent_net_fare,$book_v['currency_conversion_rate']);
					
					// $complete_booking_details[$key]['agent_comm_fare'] = $this->add_currencyrate($agent_comm_fare,$book_v['currency_conversion_rate']);

					$currency = domain_base_currency();
				}
				
				$complete_booking_details[$key]['currency'] = $currency;// $currency_obj->get_currency_symbol($currency);//FIXME: Remove the hardcoded value "INR" Later
		}
		
		return $complete_booking_details;
	}
	/*Format Sightseeing data for voucher*/
	function format_sightseeing_booking_datas($complete_booking_details, $module)
	{
		//debug($complete_booking_details); die;
		$response['status']	= SUCCESS_STATUS;
		$response['data']	= array();
		$booking_details = array();
		$currency_obj = new Currency();
		$master_booking_details = $complete_booking_details['data']['booking_details'];
	   //echo debug($master_booking_details);exit;
		
		$customer_details = $this->format_customer_details($complete_booking_details['data']['booking_pax_details']);

		$cancellation_details = $this->format_sightseen_cancellation_details($complete_booking_details['data']['cancellation_details']);
		

		foreach($master_booking_details as $book_k => $book_v) {
			//debug($book_v); die;
			$core_booking_details = $book_v;
			
			$app_reference = $core_booking_details['app_reference'];
			
			$booking_customer_details = $customer_details[$app_reference];
			$booking_cancellation_details = @$cancellation_details[$app_reference];		
			$booking_itinerary_details = array();
			if(is_domain_user() == true){
				//get the converted currency rate for booking transaction
				$booking_itinerary_details = $this->display_currency_for_sightseen($book_v,$core_booking_details);
				//get the converted currency rate for convenience amount
				$core_booking_details = $this->display_convenience_amount($book_v);
			}
			
			//Calculating Price
			$fare = 0;
			$admin_markup = 0;
			$agent_markup = 0;
			$admin_commission = 0;
			$agent_commission = 0;
			$admin_tds = 0;
			$agent_tds = 0;
			$net_fare= 0;
			if($booking_itinerary_details){
				$net_fare +=$booking_itinerary_details['net_fare'];
				$fare += $booking_itinerary_details['total_fare']; //admin comm fare
				$admin_markup += floatval($booking_itinerary_details['domain_markup']);
				$admin_commission += $booking_itinerary_details['admin_commission'];
				$agent_commission +=$booking_itinerary_details['agent_commission'];
				$admin_tds +=$booking_itinerary_details['admin_tds'];
				$agent_tds +=$booking_itinerary_details['agent_tds'];

			}else{
				$fare +=$core_booking_details['total_fare'];
				$net_fare +=$core_booking_details['net_fare'];
				$admin_markup +=floatval($core_booking_details['domain_markup']);
				$admin_commission += $core_booking_details['admin_commission'];
				$agent_commission +=$core_booking_details['agent_commission'];
				$admin_tds +=$core_booking_details['admin_tds'];
				$agent_tds +=$core_booking_details['agent_tds'];

			}
			
			$admin_net_fare = roundoff_number($fare-$admin_commission+$admin_tds);
			$admin_comm_fare = roundoff_number($fare);

			//$agent_net_fare = roundoff_number($fare-$agent_commission+$agent_tds);

			$agent_net_fare = roundoff_number($fare-$agent_commission+$agent_tds+$admin_markup);
			
			$core_booking_details['admin_net_fare'] = $admin_net_fare;
			$core_booking_details['admin_comm_fare']  =$admin_comm_fare;
			$core_booking_details['agent_net_fare'] = $agent_net_fare;



			//PaxCount
			$adult_count = 0;
			$child_count = 0;
			$infant_count = 0;
			$senior_count = 0;
			$youth_count = 0;
			foreach($booking_customer_details as $customer_k => $customer_v) {
				$pax_type = $customer_v['pax_type'];
				if($pax_type == 'Adult') {
					$adult_count++;
				} else if($pax_type == 'Child'){
					$child_count++;
				}elseif ($pax_type=='Infant') {
					$infant_count++;
				}
				elseif ($pax_type=='Senior') {
					$senior_count++;
				}
				elseif ($pax_type=='Youth') {
					$youth_count++;
				}
			}
			//Formatiing the data
			//Booking Details

			$attributes = json_decode($core_booking_details['attributes'], true);
			
			$core_booking_details['product_image'] = @$attributes['ProductImage'];

			$core_booking_details['product_title'] = @$core_booking_details['product_name'];

			

			$core_booking_details['duration'] = $attributes['Duration'];
			
			$core_booking_details['cancellation_policy'] = $attributes['Cancellation_available'];
			

			$core_booking_details['total_pax'] = $core_booking_details['total_pax'];

			$core_booking_details['adult_count'] = $adult_count;
			$core_booking_details['child_count'] = $child_count;
			$core_booking_details['infant_count']= $infant_count;
			$core_booking_details['senior_count']= $senior_count;
			$core_booking_details['youth_count'] = $youth_count;

			$core_booking_details['fare'] = roundoff_number($fare);
			$core_booking_details['admin_markup'] = roundoff_number($admin_markup);
			$core_booking_details['agent_markup'] = roundoff_number($agent_markup);
			$admin_buying_price = $this->admin_buying_price($core_booking_details);
		
			
			$core_booking_details['admin_buying_price'] = roundoff_number($admin_buying_price[0]);
			if(is_domain_user() == true) {
				$agent_buying_price = $this->agent_buying_price($core_booking_details);
				$core_booking_details['agent_buying_price'] = roundoff_number($agent_buying_price[0]);
			} else {
				$core_booking_details['agent_buying_price'] = 0;
			}
			
			$core_booking_details['grand_total'] = roundoff_number($agent_net_fare);
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
			$booking_details = $core_booking_details;

			//Itenary Details
			$booking_details['itinerary_details'] = $booking_itinerary_details;
			//Customer Details
			$booking_details['customer_details'] = $booking_customer_details;
			$booking_details['cancellation_details'] = $booking_cancellation_details;

		}
		
		$booking_details = $this->convert_as_array($booking_details);
		$response['data']['booking_details'] = $booking_details;

		return $response;
	}
	private function format_sightseen_cancellation_details($cancellation_details)
	{
		$booking_cancellation_details = array();
		foreach($cancellation_details as $cancel_k => $cancel_v) {
			$booking_cancellation_details[$cancel_v['app_reference']][] = $cancel_v;
		}
		return $booking_cancellation_details;
	}
	/*Display Currency for Sightseeing*/
	function display_currency_for_sightseen($currency_rate = array(),$itinerary_details){
		
		$conversion_rate = $currency_rate['currency_conversion_rate'];
		$itinerary_details[$key]['total_fare'] =  $this->add_currencyrate($sub_itinerary_details['total_fare'],$conversion_rate);
		$itinerary_details[$key]['domain_markup'] =  $this->add_currencyrate($sub_itinerary_details['domain_markup'],$conversion_rate)
		;		
		
		return $itinerary_details;
	}
	  /*Transfers*/
    /**
	 * Elavarasi
	 * @param array Transfer booking_details
	 */

	function format_transferv1_booking_data($complete_booking_details, $module)
	{
		$currency_obj = new Currency();
		
		foreach($complete_booking_details as $key => $book_v) {	

			#debug($book_v);
			#exit;
			$complete_booking_details[$key]['admin_currency'] = $book_v['currency'];
			    $complete_booking_details[$key]['total_fare'] = $book_v['total_fare'];
				$complete_booking_details[$key]['domain_markup'] = $book_v['domain_markup'];
				$currency = get_application_default_currency();
				
				$admin_cmm_fare = $book_v['total_fare'];

				//$admin_net_fare = ($book_v['net_fare']);

				
				$admin_commission = $book_v['admin_commission'] ;
				$admin_tds = $book_v['admin_tds'];
				$agent_commission = $book_v['agent_commission'];
				$agent_tds = $book_v['agent_tds'];
				$domain_markup = $book_v['domain_markup'];

				$admin_net_fare = roundoff_number($admin_cmm_fare-$admin_commission+$admin_tds);

				$agent_net_fare = roundoff_number($admin_cmm_fare-$agent_commission+$agent_tds+$domain_markup);
				$agent_comm_fare  = roundoff_number($admin_cmm_fare+$domain_markup);

				if(is_domain_user() == true){
					$complete_booking_details[$key]['admin_net_fare'] = $admin_net_fare;
					$complete_booking_details[$key]['agent_net_fare'] = $agent_net_fare;
					
				} else {
					$complete_booking_details[$key]['total_fare'] = $agent_net_fare;
					$complete_booking_details[$key]['domain_markup'] = $book_v['domain_markup'];

					$complete_booking_details[$key]['level_one_markup'] =$book_v['level_one_markup'];
					
					$complete_booking_details[$key]['admin_net_fare'] = $admin_net_fare;

					$complete_booking_details[$key]['admin_comm_fare'] = $admin_cmm_fare;

					$complete_booking_details[$key]['admin_commission'] = $admin_commission;

					$complete_booking_details[$key]['agent_commission'] = $agent_commission;

					$complete_booking_details[$key]['admin_tds'] = $admin_tds;

					$complete_booking_details[$key]['agent_tds'] = $agent_tds;

					$complete_booking_details[$key]['agent_net_fare'] = $agent_net_fare;
					
					$complete_booking_details[$key]['agent_comm_fare'] = $agent_comm_fare;

					$currency = domain_base_currency();
				}
				
				$complete_booking_details[$key]['currency'] = $currency;// $currency_obj->get_currency_symbol($currency);//FIXME: Remove the hardcoded value "INR" Later
		}
		
		return $complete_booking_details;
	}
	/*Format Transferv1 data for voucher*/
	function format_transferv1_booking_datas($complete_booking_details, $module)
	{
		//debug($complete_booking_details); die;
		$response['status']	= SUCCESS_STATUS;
		$response['data']	= array();
		$booking_details = array();
		$currency_obj = new Currency();
		$master_booking_details = $complete_booking_details['data']['booking_details'];
	   //echo debug($master_booking_details);exit;
		
		$customer_details = $this->format_customer_details($complete_booking_details['data']['booking_pax_details']);

		$cancellation_details = $this->format_transferv1_cancellation_details($complete_booking_details['data']['cancellation_details']);
		

		foreach($master_booking_details as $book_k => $book_v) {
			//debug($book_v); die;
			$core_booking_details = $book_v;
			
			$app_reference = $core_booking_details['app_reference'];
			
			$booking_customer_details = $customer_details[$app_reference];
			$booking_cancellation_details = @$cancellation_details[$app_reference];		
			$booking_itinerary_details = array();
			if(is_domain_user() == true){
				//get the converted currency rate for booking transaction
				$booking_itinerary_details = $this->display_currency_for_transferv1($book_v,$core_booking_details);
				//get the converted currency rate for convenience amount
				$core_booking_details = $this->display_convenience_amount($book_v);
			}
			
			//Calculating Price
			$fare = 0;
			$admin_markup = 0;
			$agent_markup = 0;
			$admin_commission = 0;
			$agent_commission = 0;
			$admin_tds = 0;
			$agent_tds = 0;
			$net_fare= 0;
			if($booking_itinerary_details){
				$net_fare +=$booking_itinerary_details['net_fare'];
				$fare += $booking_itinerary_details['total_fare']; //admin comm fare
				$admin_markup += floatval($booking_itinerary_details['domain_markup']);
				$admin_commission += $booking_itinerary_details['admin_commission'];
				$agent_commission +=$booking_itinerary_details['agent_commission'];
				$admin_tds +=$booking_itinerary_details['admin_tds'];
				$agent_tds +=$booking_itinerary_details['agent_tds'];

			}else{
				$fare +=$core_booking_details['total_fare'];
				$net_fare +=$core_booking_details['net_fare'];
				$admin_markup +=floatval($core_booking_details['domain_markup']);
				$admin_commission += $core_booking_details['admin_commission'];
				$agent_commission +=$core_booking_details['agent_commission'];
				$admin_tds +=$core_booking_details['admin_tds'];
				$agent_tds +=$core_booking_details['agent_tds'];

			}
			
			$admin_net_fare = roundoff_number($fare-$admin_commission+$admin_tds);
			$admin_comm_fare = roundoff_number($fare);

			//$agent_net_fare = roundoff_number($fare-$agent_commission+$agent_tds);
			$agent_net_fare = roundoff_number($fare-$agent_commission+$agent_tds+$admin_markup);

			$core_booking_details['admin_net_fare'] = $admin_net_fare;
			$core_booking_details['admin_comm_fare']  =$admin_comm_fare;
			$core_booking_details['agent_net_fare'] = $agent_net_fare;



			//PaxCount
			$adult_count = 0;
			$child_count = 0;
			$infant_count = 0;
			$senior_count = 0;
			$youth_count = 0;
			foreach($booking_customer_details as $customer_k => $customer_v) {
				$pax_type = $customer_v['pax_type'];
				if($pax_type == 'Adult') {
					$adult_count++;
				} else if($pax_type == 'Child'){
					$child_count++;
				}elseif ($pax_type=='Infant') {
					$infant_count++;
				}
				elseif ($pax_type=='Senior') {
					$senior_count++;
				}
				elseif ($pax_type=='Youth') {
					$youth_count++;
				}
			}
			//Formatiing the data
			//Booking Details

			$attributes = json_decode($core_booking_details['attributes'], true);
			
			$core_booking_details['product_image'] = @$attributes['ProductImage'];

			$core_booking_details['product_title'] = @$core_booking_details['product_name'];

			

			$core_booking_details['duration'] = $attributes['Duration'];
			
			$core_booking_details['cancellation_policy'] = $attributes['Cancellation_available'];
			

			$core_booking_details['total_pax'] = $core_booking_details['total_pax'];

			$core_booking_details['adult_count'] = $adult_count;
			$core_booking_details['child_count'] = $child_count;
			$core_booking_details['infant_count']= $infant_count;
			$core_booking_details['senior_count']= $senior_count;
			$core_booking_details['youth_count'] = $youth_count;

			$core_booking_details['fare'] = roundoff_number($fare);
			$core_booking_details['admin_markup'] = roundoff_number($admin_markup);
			$core_booking_details['agent_markup'] = roundoff_number($agent_markup);
			$admin_buying_price = $this->admin_buying_price($core_booking_details);
		
			
			$core_booking_details['admin_buying_price'] = roundoff_number($admin_buying_price[0]);
			if(is_domain_user() == true) {
				$agent_buying_price = $this->agent_buying_price($core_booking_details);
				$core_booking_details['agent_buying_price'] = roundoff_number($agent_buying_price[0]);
			} else {
				$core_booking_details['agent_buying_price'] = 0;
			}
			
			$core_booking_details['grand_total'] = roundoff_number($agent_net_fare);
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
			$booking_details = $core_booking_details;

			//Itenary Details
			$booking_details['itinerary_details'] = $booking_itinerary_details;
			//Customer Details
			$booking_details['customer_details'] = $booking_customer_details;
			$booking_details['cancellation_details'] = $booking_cancellation_details;

		}
		
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

	function display_currency_for_transferv1($currency_rate = array(),$itinerary_details){
		
		$conversion_rate = $currency_rate['currency_conversion_rate'];
		$itinerary_details[$key]['total_fare'] =  $this->add_currencyrate($sub_itinerary_details['total_fare'],$conversion_rate);
		$itinerary_details[$key]['domain_markup'] =  $this->add_currencyrate($sub_itinerary_details['domain_markup'],$conversion_rate)
		;		
		
		return $itinerary_details;
	}
}
