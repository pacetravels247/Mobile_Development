<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
require_once BASEPATH . 'libraries/Common_Api_Grind.php';
/**
 *
 * @package Provab
 * @subpackage API
 * @author Balu A<balu.provab@gmail.com>
 * @version V1
 */
class Rezlive_tmx extends Common_Api_Grind {
	private $ClientId;
	private $UserName;
	private $Password;
	private $service_url;
	private $Url;
	public $master_search_data;
	public $search_hash;
	public function __construct() {
		$this->CI = &get_instance ();
		$GLOBALS ['CI']->load->library ( 'Api_Interface' );
		$GLOBALS ['CI']->load->model ( 'hotel_model' );
		$GLOBALS ['CI']->load->library('converter');
		$this->TokenId = $GLOBALS ['CI']->session->userdata ( 'tb_auth_token' );
		$this->set_api_credentials ();
	}
	private function get_header() {
		$hotel_engine_system = $this->CI->hotel_engine_system;
		$user_name = $this->CI->hotel_engine_system. '_username';
        $password = $this->CI->hotel_engine_system. '_password';
		$response ['UserName'] = $this->CI->$user_name;
		$response ['Password'] = $this->CI->$password;
		$response ['DomainKey'] = $this->CI->domain_key;
		$response ['system'] = $hotel_engine_system;
		// debug($response);exit;
		return $response;
	}
	private function set_api_credentials() {

        $this->details = $this->CI->config->item ('rezlive_hotel_test');
        $this->service_url = $this->details['endpoint_url'];
        $this->agent_id = $this->details['user_code'];
        $this->username = $this->details['username'];
        $this->password = $this->details['password'];

	}

	private function xml_header() {
        $this->xml_api_header = array(
            //'Api-Key: ' . $this->api_online_key,
            // 'X-Signature: ' . $this->signature,
            // 'X-Originating-Ip: 14.141.47.106',
            'Content-Type: application/x-www-form-urlencoded',
            'Accept:application/xhtml+xml,application/xml,text/xml,application/xml',
            //'Accept: application/xml',
            'Accept-Encoding: gzip'
        );
        return $this->xml_api_header;
    }

	function credentials($service) {
		switch ($service) {
			case 'GetHotelResult' :
				$this->service_url = $this->Url . 'Search';
				break;
			case 'GetHotelImages' :
				$this->service_url = $this->Url.'GetHotelImages';
				break;
			case 'GetHotelInfo' :
				$this->service_url = $this->Url . 'HotelDetails';
				break;
			case 'GetHotelRoom' :
				$this->service_url = $this->Url . 'RoomList';
				break;
			case 'BlockRoom' :
				$this->service_url = $this->Url . 'BlockRoom';
				break;
			case 'Book' :
				$this->service_url = $this->Url . 'CommitBooking';
				break;
			case 'GetCancellationCode':
				$this->service_url = $this->Url . 'GetCancellationPolicy';
				break;
			case 'CancelBooking' :
				$this->service_url = $this->Url . 'CancelBooking';
				break;
			case 'CancellationRefundDetails' :
				$this->service_url = $this->Url . 'CancellationRefundDetails';
				break;
			case 'UpdateHoldBooking':
			  $this->service_url = $this->Url .'UpdateHoldBooking';
			  break;
			case 'AgodaBookingList':
				$this->service_url = $this->Url .'AgodaBookingList';
			break;

		}
	}
	
	/**
	 * Balu A
	 *
	 * get hotel search request details
	 * 
	 * @param array $search_params
	 *        	data to be used while searching of hotels
	 */
	 private function hotel_search_request($search_params) {
        // error_reporting(E_ALL);
        // form request for hb
        //debug($search_params);die('12');
        $request = '';
        if (isset($search_params) && !empty($search_params)) {
            // new
            if(!empty($search_params['rz_country_code']) && !empty($search_params['rz_city_code'])) {

            $request .= '<?xml version="1.0"?><HotelFindRequest><Authentication><AgentCode>'.$this->agent_id.'</AgentCode><UserName>'.$this->username.'</UserName><Password>'.$this->password.'</Password></Authentication>';

            $request .= '<Booking><ArrivalDate>'. $search_params['from_date'] .'</ArrivalDate><DepartureDate>'. $search_params['to_date'] .'</DepartureDate><CountryCode>'.$search_params['rz_country_code'].'</CountryCode><City>'.$search_params['rz_city_code'].'</City><GuestNationality>IN</GuestNationality><HotelRatings><HotelRating>1</HotelRating><HotelRating>2</HotelRating><HotelRating>3</HotelRating><HotelRating>4</HotelRating><HotelRating>5</HotelRating></HotelRatings>';

            $request .= '<Rooms>';

            $room_count = $search_params ['room_count'];
            $child_count = 0;
            $adult_count = 0;
            $room_no = 1;
            $response_str = '';
            $k = 0;
            for ($i = 0; $i < $room_count; $i++) {
                $response_str .= '<Room>';
                $response_str .= '<Type>Room-'.($i+1).'</Type>';
                if (isset($search_params['adult_config'][$i]) && !empty($search_params['adult_config'][$i]) && $search_params['adult_config'][$i] != 0) {
                    $no_of_adult = $search_params['adult_config'][$i];
                    $response_str .= '<NoOfAdults>'.$no_of_adult.'</NoOfAdults>';
                }

                if (isset($search_params['child_config'][$i]) && !empty($search_params['child_config'][$i]) && $search_params['child_config'][$i] != 0) {
                    $no_of_child = $search_params['child_config'][$i];
                    $response_str .= '<NoOfChilds>'.$no_of_child.'</NoOfChilds>';
                    $childStr = '';
                    $response_str .= '<ChildrenAges>';

                    for ($j = 0; $j < $no_of_child; $j++) {
                        $response_str .= '<ChildAge>'.$search_params['child_age'][$k].'</ChildAge>';
                    $k++;
                    }
                    /*for ($j = 0; $j < $no_of_child; $j++) {
                        $response_str .= '<ChildAge>'.$search_params['child_age'][$j].'</ChildAge>';
                    }*/
                    
                    $response_str .= '</ChildrenAges>';
                } else {
                    $response_str .= '<NoOfChilds>0</NoOfChilds>';
                }
                $response_str .= '</Room>';

            }
                $request .= $response_str .'</Rooms></Booking></HotelFindRequest>';
                /*$search_params['rz_city_code'] = $city_code[0]['city_code'];
                $search_params['rz_country_code'] = $city_code[0]['country_code'];*/
        }
        
        // adding rezlive code in the search data;
       /* $data_search = $GLOBALS ['CI']->hotel_model->get_search_data($search_params['search_id']);
        $search_raw_data = json_decode($data_search['search_data'], true);
        $search_raw_data['rz_city_code'] = $search_params['rz_city_code'];
        $search_raw_data['rz_country_code'] = $search_params['rz_country_code'];
        $data = array ( 'search_data' => json_encode($search_raw_data));
        $cond = array ( 'origin' => $search_params['search_id']);
        $GLOBALS ['CI']->custom_db->update_record('search_history',$data, $cond);*/
        }
        $response ['data'] ['request'] = $request;
        $response ['data'] ['service_url'] = $this->service_url . '/findhotel';
        $response ['status'] = SUCCESS_STATUS;

        //debug($response);die('==');
        return $response;
    }
	
	/**
	 * Balu A
	 *
	 * Hotel Details Request
	 * 
	 * @param string $TraceId        	
	 * @param string $ResultIndex        	
	 * @param string $HotelCode        	
	 */
	private function hotel_details_request1($ResultToken) {
		$response ['status'] = true;
		$response ['data'] = array ();
		$request ['ResultToken'] = $ResultToken;
		$response ['data'] ['request'] = json_encode ( $request );
		$this->credentials ( 'GetHotelInfo' );
		$response ['data'] ['service_url'] = $this->service_url;
		return $response;
	}

	 private function hotel_details_request($search_params, $hotel_code) {
        $response ['status'] = true;
        $response ['data'] = array();
        $request = '';
     
        // form request for rz
        if ((isset($search_params) && !empty($search_params)) && (isset($hotel_code) && !empty($hotel_code))) {


            $city_code[0]['city_code'] = $search_params['rz_city_code'];
            $city_code[0]['country_code'] = $search_params['rz_country_code'];

            $request .= '<?xml version="1.0"?><HotelFindRequest><Authentication><AgentCode>'.$this->agent_id.'</AgentCode><UserName>'.$this->username.'</UserName><Password>'.$this->password.'</Password></Authentication>';

            $request .= '<Booking><ArrivalDate>'. (date('d/m/Y', strtotime($search_params['from_date']))) .'</ArrivalDate><DepartureDate>'. (date('d/m/Y', strtotime($search_params['to_date']))) .'</DepartureDate><CountryCode>'.$city_code[0]['country_code'].'</CountryCode><City>'.$city_code[0]['city_code'].'</City><HotelIDs><Int>'.$hotel_code.'</Int></HotelIDs><GuestNationality>IN</GuestNationality>';

            $request .= '<Rooms>';

            $room_count = $search_params ['room_count'];
            $child_count = 0;
            $adult_count = 0;
            $room_no = 1;
            $response_str = '';
            $k=0;
            for ($i = 0; $i < $room_count; $i++) {
                $response_str .= '<Room>';
                $response_str .= '<Type>Room-'.($i+1).'</Type>';
                if (isset($search_params['adult_config'][$i]) && !empty($search_params['adult_config'][$i]) && $search_params['adult_config'][$i] != 0) {
                    $no_of_adult = $search_params['adult_config'][$i];
                    $response_str .= '<NoOfAdults>'.$no_of_adult.'</NoOfAdults>';
                }

                if (isset($search_params['child_config'][$i]) && !empty($search_params['child_config'][$i]) && $search_params['child_config'][$i] != 0) {
                    $no_of_child = $search_params['child_config'][$i];
                    $response_str .= '<NoOfChilds>'.$no_of_child.'</NoOfChilds>';
                    $childStr = '';
                    $response_str .= '<ChildrenAges>';
                    for ($j = 0; $j < $no_of_child; $j++) {
                        $response_str .= '<ChildAge>'.$search_params['child_age'][$k].'</ChildAge>';
                    $k++;
                    }
                    /*for ($j = 0; $j < $no_of_child; $j++) {
                        $response_str .= '<ChildAge>'.$search_params['child_age'][$j].'</ChildAge>';
                    }*/
                    
                    $response_str .= '</ChildrenAges>';
                } else {
                    $response_str .= '<NoOfChilds>0</NoOfChilds>';
                }
                $response_str .= '</Room>';

            }
                
                $request .= $response_str .'</Rooms></Booking></HotelFindRequest>';
               
        }

       /* $request['hotelCode'] = $hotel_code;

        if (isset($search_params) && !empty($search_params)) {
            $request['checkIn'] = date('Y-m-d', strtotime($search_params['from_date']));
            $request['checkOut'] = date('Y-m-d', strtotime($search_params['to_date']));
            $request['occupancies'] = $search_params['room_count'];
            $room_count = $search_params ['room_count'];

            $child_count = 0;
            $adult_cnt = 0;
            $room_no = 1;

            $response_array = array();
            //debug($search_params);
            for ($i = 0; $i < $room_count; $i++) {
                $response_array[$i] = $room_no . '~' . $search_params['adult_config'][$i] . '~' . $search_params['child_config'][$i];

                if (isset($search_params['adult_config'][$i]) && !empty($search_params['adult_config'][$i]) && $search_params['adult_config'][$i] != 0) {
                    $no_of_adult = $search_params['adult_config'][$i];
                    $adultTxt = substr('~' . str_repeat('AD-30;', $no_of_adult), 0, -1);
                }
                $response_array[$i] .= $adultTxt;

                if (isset($search_params['child_config'][$i]) && !empty($search_params['child_config'][$i]) && $search_params['child_config'][$i] != 0) {
                    $no_of_child = $search_params['child_config'][$i];
                    $childStr = '';
                    for ($j = 0; $j < $no_of_child; $j++) {
                        $child_age = array_shift($search_params['child_age']);
                        $childStr .= ';' . 'CH' . '-' . $child_age;
                    }
                    $response_array[$i] .= $childStr;
                }
            }

            $count_array = array_count_values($response_array);
            //debug($count_array);exit;
            $response_array = array();
            foreach ($count_array as $cnt_key => $count_arr_val) {
                $count_multiplier = $count_arr_val;
                if ($count_arr_val > 1) {
                    $explode = explode('~', $cnt_key);
                    //end -
                    $explode_adult_child = explode(';', end($explode));
                    $child_Adult = '';
                    foreach ($explode_adult_child as $ac_k => $ac_v) {
                        //$explode_adult_child[$ac_k] = substr(str_repeat($ac_v.';', $count_multiplier), 0, -1);//AD-10;AD-30
                        $explode_adult_child[$ac_k] = $ac_v;
                    }
                    $child_Adult = implode(';', $explode_adult_child);
                    //total length - 1 ; exclude last value
                    //rest to be multiplied with $countArrVal
                    $pax_list = array_slice($explode, 0, -1);
                    foreach ($pax_list as $tk => $tv) {
                        if ($tk == 0) {
                            $pax_list[$tk] = $tv * $count_multiplier;
                        } else {
                            $pax_list[$tk] = $tv;
                        }
                    }
                    $response_array[] = implode('~', $pax_list) . '~' . $child_Adult;
                } else {
                    $response_array[] = $cnt_key;
                }
                //$roomNo++;
            }
            $response_array = implode(',', $response_array);
            //debug($stringArray);exit;
        }

        $params = '?checkIn=' . $request['checkIn'] . '&checkOut=' . $request['checkOut'] . '&occupancies=' . $response_array;*/
        $response ['data'] ['service_url'] = $this->service_url . '/findhotelbyid';
        $response ['data'] ['request'] = $request;
        $response ['status'] = SUCCESS_STATUS;

        return $response;
    }
	
	/**
	 * Balu A
	 *
	 * Room Details Request
	 * 
	 * @param string $TraceId        	
	 * @param string $ResultIndex        	
	 * @param string $HotelCode        	
	 */
	private function room_list_request($ResultToken) {
		/*$response ['status'] = true;
		$response ['data'] = array ();
		$request ['ResultToken'] = $ResultToken;
		$response ['data'] ['request'] = json_encode ( $request );
		$this->credentials ( 'GetHotelRoom' );
		$response ['data'] ['service_url'] = $this->service_url;
		return $response;*/
	}
	
	/**
	 * Balu A
	 *
	 * get room block request
	 * 
	 * @param array $booking_parameters        	
	 */
	private function get_block_room_request($booking_params)
	{
		
		$number_of_nights = $booking_params ['search_data'] ['no_of_nights'];
		$response ['status'] = true;
		$response ['data'] = array ();
		$request ['ResultToken'] = urldecode($booking_params['ResultIndex']);
		// debug($booking_params);
		// exit;
		foreach ($booking_params['token'] as $tk => $tv){
			$request ['RoomUniqueId'][] = $tv;
		}
		
		$response ['data'] ['request'] = json_encode ( $request );
		$this->credentials ( 'BlockRoom' );
		$response ['data'] ['service_url'] = $this->service_url;
		
		return $response;
	}
	/**
	 * Form Book Request
	 */
	function get_book_request($booking_params, $booking_id)
	{	

		$search_id = $booking_params ['token'] ['search_id'];
		$safe_search_data = $GLOBALS ['CI']->hotel_model->get_search_data ( $search_id );
		$search_data = json_decode ( $safe_search_data ['search_data'], true );
		$number_of_nights = get_date_difference ( date ( 'Y-m-d', strtotime ( $search_data ['hotel_checkin'] ) ), date ( 'Y-m-d', strtotime ( $search_data ['hotel_checkout'] ) ) );
		$NO_OF_ROOMS = $search_data ['rooms'];
		
		$search_params = $this->search_data($search_id);
		$search_params = $search_params['data'];
		
		/*************Re-Assign the Pax Room Wise Strats******************************/
		// debug($booking_params);
		// echo "-----";
		$room_wise_passenger_info = array();
		for($i = 0; $i < $NO_OF_ROOMS; $i ++) {
			
			$room_adult_count = $search_params['adult_config'][$i];
			$room_child_count = $search_params['child_config'][$i];
			
			foreach ($booking_params['name_title'] as $bk => $bv){
				$pax_type = trim($booking_params['passenger_type'][$bk]);
				
				$assigned_pax_type_count = $this->get_assigned_pax_type_count(@$room_wise_passenger_info[$i]['passenger_type'], $pax_type);
				
				if(intval($pax_type) == 1 && intval($assigned_pax_type_count) < intval($room_adult_count)){//Adult
					$room_wise_passenger_info[$i]['name_title'][]			= $booking_params ['name_title'][$bk];
					$room_wise_passenger_info[$i]['first_name'][]			= $booking_params ['first_name'][$bk];
					$room_wise_passenger_info[$i]['middle_name'][]		= $booking_params ['middle_name'][$bk];
					$room_wise_passenger_info[$i]['last_name'][]			= $booking_params ['last_name'][$bk];
					$room_wise_passenger_info[$i]['passenger_contact'][]	= $booking_params ['passenger_contact'];
					$room_wise_passenger_info[$i]['billing_email'][]		= $booking_params ['billing_email'];
					$room_wise_passenger_info[$i]['passenger_type'][]		= $booking_params ['passenger_type'][$bk];
					$room_wise_passenger_info[$i]['date_of_birth'][]		= $booking_params ['date_of_birth'][$bk];
					
					//Remove the pax data from array
					unset($booking_params['name_title'][$bk]);
				
				} else if(intval($pax_type) == 2 && intval($assigned_pax_type_count) < intval($room_child_count)){//Child
					$room_wise_passenger_info[$i]['name_title'][]			= $booking_params ['name_title'][$bk];
					$room_wise_passenger_info[$i]['first_name'][]			= $booking_params ['first_name'][$bk];
					$room_wise_passenger_info[$i]['middle_name'][]		= $booking_params ['middle_name'][$bk];
					$room_wise_passenger_info[$i]['last_name'][]			= $booking_params ['last_name'][$bk];
					$room_wise_passenger_info[$i]['passenger_contact'][]	= $booking_params ['passenger_contact'];
					$room_wise_passenger_info[$i]['billing_email'][]		= $booking_params ['billing_email'];
					$room_wise_passenger_info[$i]['passenger_type'][]		= $booking_params ['passenger_type'][$bk];
					$room_wise_passenger_info[$i]['date_of_birth'][]		= $booking_params ['date_of_birth'][$bk];
					
					//Remove the pax data from array
					unset($booking_params['name_title'][$bk]);
				}
			}
		}
		
		/*************Re-Assign the Pax Room Wise Ends******************************/
		
		
		/* Counting No of adults and childs per room wise */
		for($i = 0; $i < $NO_OF_ROOMS; $i ++) {
			$booking_params ['token'] ['token'] [$i] ['no_of_pax'] = $search_data ['adult'] [$i] + $search_data ['child'] [$i];
		}
		// echo "------";
		
		// echo "-------";
		/* Forming Request */
		$response ['status'] = true;
		$response ['data'] = array ();
		$request ['ResultToken'] = urldecode($booking_params ['token'] ['ResultIndex']);
		$request ['BlockRoomId'] = $booking_params ['token'] ['BlockRoomId'];
		$request ['AppReference'] = trim ( $booking_id ); // Balu A
		$room_details = array ();
		$k = 0;
		for($i = 0; $i < $NO_OF_ROOMS; $i ++) {
			for($j = 0; $j < $booking_params ['token'] ['token'] [$i] ['no_of_pax']; $j ++) {
				
				$pax_list = array (); // Reset Pax List Array
				$pax_title = get_enum_list ( 'title', $room_wise_passenger_info [$i]['name_title'] [$j] );
				$pax_list ['Title'] = $pax_title;
				$pax_list ['FirstName'] = $room_wise_passenger_info [$i] ['first_name'] [$j];
				$pax_list ['MiddleName'] = $room_wise_passenger_info [$i] ['middle_name'] [$j];
				$pax_list ['LastName'] = $room_wise_passenger_info [$i] ['last_name'] [$j];
				$pax_list ['Phoneno'] = $room_wise_passenger_info [$i] ['passenger_contact'][$j];
				$pax_list ['Email'] = $room_wise_passenger_info [$i] ['billing_email'][$j];
				$pax_list ['PaxType'] = $room_wise_passenger_info [$i] ['passenger_type'] [$j];
				
				$pax_lead = false;
				
				if ($j == 0) {
					$pax_lead = true;
				}
				$pax_list ['LeadPassenger'] = $pax_lead;
				/* Age Calculation of Pax */
				$from = new DateTime ( $room_wise_passenger_info [$i]['date_of_birth'] [$j] );
				$to = new DateTime ( 'today' );
				$pax_age = $from->diff ( $to )->y;
				$pax_list ['Age'] = $pax_age;
				$request['RoomDetails'][$i]['PassengerDetails'] [$j] = $pax_list;
				$k ++;
			}
		}
		
		// debug($request);
		// exit;
		$response ['data'] ['request'] = json_encode ( $request );
		$this->credentials ( 'Book' );
		$response ['data'] ['service_url'] = $this->service_url;
		return $response;
	}
	/**
	 * Jagnath
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
	 * Jagnath
	 * Cancellation Refund Details
	 */
	private function cancellation_refund_request_params($ChangeRequestId, $app_reference) {
		$response ['status'] = true;
		$response ['data'] = array ();
		$request ['AppReference'] = trim ( $app_reference );
		$request ['ChangeRequestId'] = $ChangeRequestId;
		$response ['data'] ['request'] = json_encode ( $request );
		$this->credentials ( 'CancellationRefundDetails' );
		$response ['data'] ['service_url'] = $this->service_url;
		return $response;
	}
	/**
	* Elavarasi
	*get hotel images
	* @param hotel_code
	*/
	function get_hotel_images($hotel_code){
		$header = $this->get_header ();
		$response ['data'] = array ();
		$response ['status'] = true;
		if($hotel_code!=''){
			
			$this->credentials ( 'GetHotelImages' );
			$url = $this->service_url;
			$request = json_encode(array('hotel_code'=>$hotel_code));

			$image_response = $GLOBALS ['CI']->api_interface->get_json_response ($url, $request, $header );
			if($image_response['Status']==true){
				$response['data'] = $image_response['GetHotelImages'];
			}else{
				$response['status'] = false;
			}

		}else{
			$response['status'] = false;
		}
		return $response;
	}
	/**
	 * Balu A
	 * get search result from tbo
	 * 
	 * @param number $search_id
	 *        	unique id which identifies search details
	 */
	function get_hotel_list($search_id = '') {
		$this->CI->load->driver ( 'cache' );
		$header = $this->get_header ();
		$response ['data'] = array ();
		$response ['status'] = true;
		$search_data = $this->search_data ( $search_id );

		$cache_search = $this->CI->config->item ( 'cache_hotel_search' );
		$search_hash = $this->search_hash;
		$cache_contents = '';
		if ($cache_search) {
			$cache_contents = $this->CI->cache->file->get ( $search_hash );
		}		
        $cache_contents = '';
		if ($search_data ['status'] == true) {
			if ($cache_search === false || ($cache_search === true && empty ( $cache_contents ) == true)) {
				//echo "not-cahce";
                //debug($search_data);
				$search_request = $this->hotel_search_request ( $search_data ['data'] );
				//debug($search_request);die('new');
				$GLOBALS ['CI']->custom_db->generate_static_response ($search_request ['data'] ['request'], 'REZLIVE hotel search request' );

				if ($search_request ['status']) {					

					$search_response = $GLOBALS ['CI']->api_interface->xml_post_request($search_request ['data'] ['service_url'], ('XML='.urlencode($search_request ['data'] ['request'])), $this->xml_header());
					//debug($search_response);exit('====');
					//$GLOBALS['CI']->custom_db->generate_static_response(json_encode($search_response));

					$search_response = Converter::createArray($search_response);
                 	//debug($search_response);exit;

                	//format as  tmx
                	$search_response = $this->hotel_result_tmx_format($search_response, $search_id, $module);
                	//debug($search_response);die('$tmx_foramt');

					if ($this->valid_search_result ( $search_response )) {
						$response ['data'] = $search_response['Search'];
						if ($cache_search) {
                            //debug($search_hash);die();
							$cache_exp = $this->CI->config->item ( 'cache_hotel_search_ttl' );
							$this->CI->cache->file->save ( $search_hash, $response ['data'], $cache_exp );
						}
						// Log Hotels Count
						$this->cache_result_hotel_count ( $search_response );
					} else {
						$response ['status'] = false;
					}
				} else {
					$response ['status'] = false;
				}
			} else {
				// read from cache
				//echo "cahce";
				$response ['data'] = $cache_contents;
			}
		} else {
			$response ['status'] = false;
		}
		//debug($response);exit;
		return $response;
	}
	/**
	 * Elavarasi
	 * get search result from tbo
	 * 
	 * @param number $search_id
	 *        	unique id which identifies search details
	 */
	function get_hotel_image_list($city_code,$country_code) {
		
		$header = $this->get_header ();
		$response ['data'] = array ();
		$response ['status'] = true;
		//$search_data = $this->search_data ( $search_id );
		
		//$search_request = $this->hotel_search_request ( $search_data ['data'] );
		$search_request['status'] = true;
		$request_arr = [];
		$request_arr['destination_code'] = $city_code;
		$request_arr['checkin'] = '2018-02-01';
		$request_arr['checkout'] = '2018-02-02';
		
		$request_arr['client_nationality'] = $country_code;
		$request_arr['hotel_info'] = false;
		$request_arr['rooms'] =array(array('adults'=>1,'children_ages'=>array()));
		
		$search_request['data']['request'] = json_encode($request_arr);
		
		$search_request['data']['service_url'] = 'https://v3-api.grnconnect.com/api/v3/hotels/availability';
		if ($search_request ['status']) {
			
			$search_response = $GLOBALS ['CI']->api_interface->get_json_image_response( $search_request ['data'] ['service_url'], $search_request ['data'] ['request'], $header ,'post');
			
			
			if (!isset($search_response['errors'])) {
				$response ['data'] = $search_response['hotels'];
				$response ['status'] = true;
				
			} else {
				$response ['status'] = false;
				$response ['data'] = $search_response['errors'];
			}
		
		}
		
		return $response;
	}
	/**
	*Get Hotel Booking Status
	*/
	public function get_hotel_booking_status($app_reference){
		$header = $this->get_header ();
		$response ['data'] = array ();
		$response ['status'] = true;
		//UpdateHoldBooking
		$this->credentials('UpdateHoldBooking');
		 $service_url = $this->service_url;
		 if($app_reference !=''){
		 	$get_hold_booking_request = array('app_reference'=>$app_reference);
		 	$request = json_encode($get_hold_booking_request);
		 	$GLOBALS ['CI']->custom_db->generate_static_response ($request); // release this

		 	$get_hb_status = $GLOBALS['CI']->api_interface->get_json_response ( $service_url,$request, $header );
		 	
		 	$GLOBALS ['CI']->custom_db->generate_static_response ( json_encode ( $get_hb_status ) );
		 	if($get_hb_status['Status']==true){
		 		
		 		//update booking status
		 		$booking_id = $get_hb_status['UpdateHoldBooking']['booking_id'];
		 		$update_data['status'] = 'BOOKING_CONFIRMED';
		 		$update_data['booking_id'] = $booking_id;
		 		$this->CI->custom_db->update_record('hotel_booking_details',$update_data,array('app_reference'=>$app_reference));
		 		$update_ite_data['status'] = 'BOOKING_CONFIRMED';
		 		$this->CI->custom_db->update_record('hotel_booking_itinerary_details',$update_ite_data,array('app_reference'=>$app_reference));
		 		$this->CI->custom_db->update_record('hotel_booking_pax_details',$update_ite_data,array('app_reference'=>$app_reference));
		 		$response ['data'] = array('booking_id'=>$booking_id);
		 		$response['status'] = true;
		 		
		 	}else{
		 		$response['status'] = false;
		 	}
		 }
		 return $response;

	}
	/**
	 * Converts API data currency to preferred currency
	 * Balu A
	 * 
	 * @param unknown_type $search_result        	
	 * @param unknown_type $currency_obj        	
	 */
	public function search_data_in_preferred_currency($search_result, $currency_obj,$search_id) {
		$hotels = $search_result ['data'] ['HotelSearchResult'] ['HotelResults'];
		$hotel_list = array ();
		foreach ( $hotels as $hk => $hv ) {
			$hotel_list [$hk] = $hv;
			//Update Markup price in search result			
			
			//$Price =  $this->update_search_markup_currency ($hv ['Price'], $currency_obj, $search_id, false, true );	

			$hotel_list [$hk] ['Price'] = $this->preferred_currency_fare_object ($hv ['Price'], $currency_obj );	
			

		}
		$search_result ['data'] ['HotelSearchResult'] ['PreferredCurrency'] = get_application_currency_preference ();
		$search_result ['data'] ['HotelSearchResult'] ['HotelResults'] = $hotel_list;
		
		return $search_result;
	}
	/**
	 * Balu A
	 * 
	 * @param unknown_type $fare_details        	
	 * @param unknown_type $currency_obj        	
	 */
	private function preferred_currency_fare_object($fare_details, $currency_obj, $default_currency = '') {
		$price_details = array ();
		

		$price_details ['CurrencyCode'] = empty ( $default_currency ) == false ? $default_currency : get_application_currency_preference ();

		$price_details ['RoomPrice'] = get_converted_currency_value ( $currency_obj->force_currency_conversion ( $fare_details ['RoomPrice'] ) );

		$price_details ['Tax'] = get_converted_currency_value ( $currency_obj->force_currency_conversion ( $fare_details ['Tax'] ) );

		$price_details ['ExtraGuestCharge'] = get_converted_currency_value ( $currency_obj->force_currency_conversion ( $fare_details ['ExtraGuestCharge'] ) );

		$price_details ['ChildCharge'] = get_converted_currency_value ( $currency_obj->force_currency_conversion ( $fare_details ['ChildCharge'] ) );
		$price_details ['OtherCharges'] = get_converted_currency_value ( $currency_obj->force_currency_conversion ( $fare_details ['OtherCharges'] ) );
		$price_details ['Discount'] = get_converted_currency_value ( $currency_obj->force_currency_conversion ( $fare_details ['Discount'] ) );
		$price_details ['PublishedPrice'] = get_converted_currency_value ( $currency_obj->force_currency_conversion ( $fare_details ['PublishedPrice'] ) );
		$price_details ['PublishedPriceRoundedOff'] = get_converted_currency_value ( $currency_obj->force_currency_conversion ( $fare_details ['PublishedPriceRoundedOff'] ) );
		$price_details ['OfferedPrice'] = get_converted_currency_value ( $currency_obj->force_currency_conversion ( $fare_details ['OfferedPrice'] ) );
		$price_details ['OfferedPriceRoundedOff'] = get_converted_currency_value ( $currency_obj->force_currency_conversion ( $fare_details ['OfferedPriceRoundedOff'] ) );
		$price_details ['AgentCommission'] = get_converted_currency_value ( $currency_obj->force_currency_conversion ( $fare_details ['AgentCommission'] ) );
		$price_details ['AgentMarkUp'] = get_converted_currency_value ( $currency_obj->force_currency_conversion ( $fare_details ['AgentMarkUp'] ) );
		$price_details ['ServiceTax'] = get_converted_currency_value ( $currency_obj->force_currency_conversion ( $fare_details ['ServiceTax'] ) );
		$price_details ['TDS'] = get_converted_currency_value ( $currency_obj->force_currency_conversion ( $fare_details ['TDS'] ) );
		
		return $price_details;
	}
	/**
	 * Balu A
	 * Converts Display currency to application currency
	 * 
	 * @param unknown_type $fare_details        	
	 * @param unknown_type $currency_obj        	
	 * @param unknown_type $module        	
	 */
	public function convert_token_to_application_currency($token, $currency_obj, $module) {
		$master_token = array ();
		$price_token = array ();
		$price_summary = array ();
		$markup_price_summary = array ();
		// Price Token
		foreach ( $token ['price_token'] as $ptk => $ptv ) {
			$price_token [$ptk] = $this->preferred_currency_fare_object ( $ptv, $currency_obj, admin_base_currency () );
		}
		// Price Summary
		$price_summary = $this->preferred_currency_price_summary ( $token ['price_summary'], $currency_obj );
		// Markup Price Summary
		$markup_price_summary = $this->preferred_currency_price_summary ( $token ['markup_price_summary'], $currency_obj );
		// Assigning the Converted Data
		$master_token = $token;
		$master_token ['price_token'] = $price_token;
		$master_token ['price_summary'] = $price_summary;
		$master_token ['markup_price_summary'] = $markup_price_summary;
		$master_token ['default_currency'] = admin_base_currency ();
		$master_token ['convenience_fees'] = get_converted_currency_value ( $currency_obj->force_currency_conversion ( $token ['convenience_fees'] ) ); // check this
		return $master_token;
	}
	/**
	 * Balu A
	 * Converts Price summary to application curency
	 * 
	 * @param unknown_type $fare_details        	
	 * @param unknown_type $currency_obj        	
	 */
	private function preferred_currency_price_summary($fare_details, $currency_obj) {
		$price_details = array ();
		$price_details ['RoomPrice'] = get_converted_currency_value ( $currency_obj->force_currency_conversion ( $fare_details ['RoomPrice'] ) );
		$price_details ['PublishedPrice'] = get_converted_currency_value ( $currency_obj->force_currency_conversion ( $fare_details ['PublishedPrice'] ) );
		$price_details ['PublishedPriceRoundedOff'] = get_converted_currency_value ( $currency_obj->force_currency_conversion ( $fare_details ['PublishedPriceRoundedOff'] ) );
		$price_details ['OfferedPrice'] = get_converted_currency_value ( $currency_obj->force_currency_conversion ( $fare_details ['OfferedPrice'] ) );
		$price_details ['OfferedPriceRoundedOff'] = get_converted_currency_value ( $currency_obj->force_currency_conversion ( $fare_details ['OfferedPriceRoundedOff'] ) );
		$price_details ['ServiceTax'] = get_converted_currency_value ( $currency_obj->force_currency_conversion ( $fare_details ['ServiceTax'] ) );
		$price_details ['Tax'] = get_converted_currency_value ( $currency_obj->force_currency_conversion ( $fare_details ['Tax'] ) );
		$price_details ['ExtraGuestCharge'] = get_converted_currency_value ( $currency_obj->force_currency_conversion ( $fare_details ['ExtraGuestCharge'] ) );
		$price_details ['ChildCharge'] = get_converted_currency_value ( $currency_obj->force_currency_conversion ( $fare_details ['ChildCharge'] ) );
		$price_details ['OtherCharges'] = get_converted_currency_value ( $currency_obj->force_currency_conversion ( $fare_details ['OtherCharges'] ) );
		$price_details ['TDS'] = get_converted_currency_value ( $currency_obj->force_currency_conversion ( $fare_details ['TDS'] ) );
		return $price_details;
	}

	function cache_result_hotel_count($response) {
		$CI = & get_instance ();
		$city_id = @$response['Search']['HotelSearchResult'] ['CityId'];
		$hotel_count = intval ( count ( @$response['Search']['HotelSearchResult'] ['HotelResults'] ) );
		if ($hotel_count > 0 && $city_id !='') {			
			$CI->custom_db->update_record ('all_api_city_master', array (
					'cache_hotels_count' => $hotel_count 
			), array (
					'origin' => $city_id 
			) );
		}
	}
	/**
	*Get cancellation details by cancellation policy code (GRN CONNECT)
	*/
	public function get_cancellation_details($get_params){
		if($get_params){
			
			$request_rate_data = json_encode($get_params);
			//echo $request_rate_data;
			$header = $this->get_header ();
			$this->credentials('GetCancellationCode');		
			$response['data'] = array();			
			$cancel_url = $this->service_url;			
			$cancel_data_arr = array();
			$cancellation_details = array();			
			$cancellation_policy = $GLOBALS ['CI']->api_interface->get_json_response ( $cancel_url, $request_rate_data, $header );
			return $cancellation_policy;
		}
	}
	/**
	 * Balu A
	 * get Room List for selected hotel
	 * 
	 * @param string $TraceId        	
	 * @param number $ResultIndex        	
	 * @param string $HotelCode        	
	 */
	function get_room_list($room_params) {
        /*debug($room_params);
        die('Rezlive lib');*/

        $response = array();
        $response['status'] = FAILURE_STATUS;

        if(!empty($room_params['room_details'])){

            $room_details = json_decode($room_params['room_details'],1);
            //debug($room_details);

            $hotel_static_data = $this->get_hotel_static_detail_new($room_params['hotel_code']);
            //debug($hotel_static_data['data']['RoomAmenities']);die('77777');

            $room_list = array();
            $RoomCombinations = array();
            if(isset($room_details[0])){
                //die('7');
                $count = 1;
                foreach ($room_details as $room_key => $room_value) {
                    $room_list_details = array();
                    $room_combinations = array();

                    $room_list_details['RoomIndex'] = $room_key;
                    $room_list_details['ChildCount'] = $room_value['Children'];
                    $room_list_details['RoomTypeName'] = $room_value['Type']['@cdata'];

                    $api_price = roundoff_number($room_value['TotalRate']);
                    $room_list_details['Price'] = array(
                        'TBO_RoomPrice' => $api_price,
                        'TBO_OfferedPriceRoundedOff' => $api_price,
                        'TBO_PublishedPrice' => $api_price,
                        'TBO_PublishedPriceRoundedOff' => $api_price,
                        'Tax' => 0,
                        'ExtraGuestCharge' => 0,
                        'ChildCharge' => 0,
                        'OtherCharges' => 0,
                        'Discount' => 0,
                        'PublishedPrice' => $api_price,
                        'RoomPrice' => $api_price,
                        'PublishedPriceRoundedOff' => $api_price,
                        'OfferedPrice' => $api_price,
                        'OfferedPriceRoundedOff' => $api_price,
                        'AgentCommission' => 0,
                        'AgentMarkUp' => 0,
                        'ServiceTax' => 0,
                        'TDS' => 0,
                        'ServiceCharge' => 0,
                        'TotalGSTAmount' => 0,
                        'RoomPriceWoGST' => $api_price,
                        'GSTPrice' => 0,
                        'CurrencyCode' => 'INR',
                        );
                    $room_list_details['SmokingPreference'] = '';
                    $room_list_details['RatePlanCode'] = '';
                    $room_list_details['RoomTypeCode'] = '';

                  
                    $room_list_details['Amenities'] = array();
                    if(isset($hotel_static_data['data']['RoomAmenities']['@cdata'])){
                        $room_list_details['Amenities'] = explode(',', $hotel_static_data['data']['RoomAmenities']['@cdata']);
                    }else{
                        $room_list_details['Amenities'] = explode(',', $hotel_static_data['data']['RoomAmenities']);
                    }


                    $room_list_details['OtherAmennities'] = array();
                    $room_list_details['room_only'] = $room_value['BoardBasis']['@cdata'];
                    $room_list_details['cancellation_policy_code'] = '';
                    $room_list_details['LastCancellationDate'] = '';
                    $room_list_details['CancellationPolicies'] = array();
                    $room_list_details['CancellationPolicy'] = '';
                    $room_list_details['rate_key'] = $count++;
                    $room_list_details['group_code'] = '';
                    $room_list_details['room_code'] = '';
                    $room_list_details['HOTEL_CODE'] = $room_params['hotel_code'];
                    $room_list_details['SEARCH_ID'] = $room_params['search_id'];
                    $room_list_details['RoomUniqueId'] = $room_value['BookingKey'];

                    $room_list[] = $room_list_details;

                    $RoomIndex['RoomIndex'] = array(
                            '0' => $room_key,
                        );
                    $RoomCombinations[] = $RoomIndex;   
                }
            }else{
                    //debug($room_details);die();
                    $room_list_details = array();
                    //$room_combinations = array();

                    $room_list_details['RoomIndex'] = 0;
                    $room_list_details['ChildCount'] = $room_details['Children'];
                    $room_list_details['RoomTypeName'] = $room_details['Type']['@cdata'];

                    $api_price = roundoff_number($room_details['TotalRate']);
                    $room_list_details['Price'] = array(
                        'TBO_RoomPrice' => $api_price,
                        'TBO_OfferedPriceRoundedOff' => $api_price,
                        'TBO_PublishedPrice' => $api_price,
                        'TBO_PublishedPriceRoundedOff' => $api_price,
                        'Tax' => 0,
                        'ExtraGuestCharge' => 0,
                        'ChildCharge' => 0,
                        'OtherCharges' => 0,
                        'Discount' => 0,
                        'PublishedPrice' => $api_price,
                        'RoomPrice' => $api_price,
                        'PublishedPriceRoundedOff' => $api_price,
                        'OfferedPrice' => $api_price,
                        'OfferedPriceRoundedOff' => $api_price,
                        'AgentCommission' => 0,
                        'AgentMarkUp' => 0,
                        'ServiceTax' => 0,
                        'TDS' => 0,
                        'ServiceCharge' => 0,
                        'TotalGSTAmount' => 0,
                        'RoomPriceWoGST' => $api_price,
                        'GSTPrice' => 0,
                        'CurrencyCode' => 'INR',
                        );
                    $room_list_details['SmokingPreference'] = '';
                    $room_list_details['RatePlanCode'] = '';
                    $room_list_details['RoomTypeCode'] = '';
                    $room_list_details['Amenities'] = array();
                    $room_list_details['OtherAmennities'] = array();
                    $room_list_details['room_only'] = $room_details['BoardBasis']['@cdata'];
                    $room_list_details['cancellation_policy_code'] = '';
                    $room_list_details['LastCancellationDate'] = '';
                    $room_list_details['CancellationPolicies'] = array();
                    $room_list_details['CancellationPolicy'] = '';
                    $room_list_details['rate_key'] = 1;
                    $room_list_details['group_code'] = '';
                    $room_list_details['room_code'] = '';
                    $room_list_details['HOTEL_CODE'] = $room_params['hotel_code'];
                    $room_list_details['SEARCH_ID'] = $room_params['search_id'];
                    $room_list_details['RoomUniqueId'] = $room_details['BookingKey'];

                    $room_list[] = $room_list_details;

                    $RoomIndex['RoomIndex'] = array(
                            '0' => 0,
                        );
                    $RoomCombinations[] = $RoomIndex;
            }

            
            $response['data']['GetHotelRoomResult']['HotelRoomsDetails'] = $room_list;

            $response['data']['GetHotelRoomResult']['RoomCombinations']['InfoSource'] = 'FixedCombination';
            $response['data']['GetHotelRoomResult']['RoomCombinations']['IsPolicyPerStay'] = '';
            $response['data']['GetHotelRoomResult']['RoomCombinations']['RoomCombination'] = $RoomCombinations;

            $response['status'] = SUCCESS_STATUS;
        }else{
            /////////////
        }
        //debug($RoomCombinations['RoomCombination']);
            //die('9999');
        //$response['HotelRoomsDetails'] = $room_list;
        //debug($response);
        //die('++++++++==');
        return $response;
        
	}
	/**
	 * Balu A
	 * 
	 * @param unknown_type $room_list        	
	 * @param unknown_type $currency_obj        	
	 */
	public function roomlist_in_preferred_currency($room_list, $currency_obj,$search_id,$module='b2c') {

		$level_one = true;
		$current_domain = true;
		if ($module == 'b2c') {
			$level_one = false;
			$current_domain = true;
		} else if ($module == 'b2b') {
			$level_one = true;
			$current_domain = true;
		}

		$application_currency_preference = get_application_currency_preference ();
		$hotel_room_details = $room_list ['data'] ['GetHotelRoomResult'] ['HotelRoomsDetails'];
		$hotel_room_result = array ();
		foreach ( $hotel_room_details as $hr_k => $hr_v ) {
			$hotel_room_result [$hr_k] = $hr_v;
			// Price
			$API_raw_price = $hr_v ['Price'];
			
			$Price = $this->preferred_currency_fare_object ( $hr_v ['Price'], $currency_obj );
			// CancellationPolicies
			$CancellationPolicies = array ();
			foreach ( $hr_v ['CancellationPolicies'] as $ck => $cv ) {
				//add cancellation charge in markup
				
				$Charge = $this->update_cancellation_markup_currency($cv['Charge'],$currency_obj,$search_id,$level_one,$current_domain);
				
				$CancellationPolicies [$ck] = $cv;
				$CancellationPolicies [$ck] ['Currency'] = $application_currency_preference;
				//$CancellationPolicies [$ck] ['Charge'] = get_converted_currency_value ( $currency_obj->force_currency_conversion ( $Charge ) );
				$CancellationPolicies [$ck] ['Charge'] = $Charge;

			}
			$hotel_room_result [$hr_k] ['API_raw_price'] = $API_raw_price;
			$hotel_room_result [$hr_k] ['Price'] = $Price;
			$hotel_room_result [$hr_k] ['CancellationPolicies'] = $CancellationPolicies;
			// CancellationPolicy:FIXME: convert the INR price to preferred currency
		}
		$room_list ['data'] ['GetHotelRoomResult'] ['HotelRoomsDetails'] = $hotel_room_result;
		return $room_list;
	}
	/**
	 * Balu A
	 * 
	 * @param unknown_type $block_room_data        	
	 * @param unknown_type $currency_obj        	
	 */
	public function roomblock_data_in_preferred_currency($block_room_data, $currency_obj,$search_id,$module='b2c') {
		$level_one = true;
		$current_domain = true;
		if ($module == 'b2c') {
			$level_one = false;
			$current_domain = true;
		} else if ($module == 'b2b') {
			$level_one = true;
			$current_domain = true;
		}
		$application_currency_preference = get_application_currency_preference ();
		$hotel_room_details = $block_room_data ['data'] ['response'] ['BlockRoomResult'] ['HotelRoomsDetails'];
		$hotel_room_result = array ();
		foreach ( $hotel_room_details as $hr_k => $hr_v ) {
			$hotel_room_result [$hr_k] = $hr_v;
			
			// Price
			$API_raw_price = $hr_v ['Price'];
			$Price = $this->preferred_currency_fare_object ( $hr_v ['Price'], $currency_obj );
			// CancellationPolicies
			$CancellationPolicies = array ();
			foreach ( $hr_v ['CancellationPolicies'] as $ck => $cv ) {

				$Charge = $this->update_cancellation_markup_currency($cv['Charge'],$currency_obj,$search_id,$level_one,$current_domain);
				

				$CancellationPolicies [$ck] = $cv;
				$CancellationPolicies [$ck] ['Currency'] = $application_currency_preference;
				$CancellationPolicies [$ck] ['Charge'] = $Charge ;
				//$CancellationPolicies [$ck] ['Charge'] = get_converted_currency_value ( $currency_obj->force_currency_conversion ( $Charge ) );
			}
			$hotel_room_result [$hr_k] ['API_raw_price'] = $API_raw_price;
			$hotel_room_result [$hr_k] ['Price'] = $Price;
			$hotel_room_result [$hr_k] ['CancellationPolicies'] = $CancellationPolicies;
			// CancellationPolicy:FIXME: convert the INR price to preferred currency
		}
		$block_room_data ['data'] ['response'] ['BlockRoomResult'] ['HotelRoomsDetails'] = $hotel_room_result;
		
		return $block_room_data;
	}
	/**
	 * Balu A
	 * Load Hotel Details
	 *
	 * @param string $TraceId
	 *        	Trace ID of hotel found in search result response
	 * @param number $ResultIndex
	 *        	Result index generated for each hotel by hotel search
	 * @param string $HotelCode
	 *        	unique id which identifies hotel
	 *        	
	 * @return array having status of the operation and resulting data in case if operaiton is successfull
	 */
	function get_hotel_details1($ResultIndex) {
		$header = $this->get_header ();
		$response ['data'] = array ();
		$response ['status'] = false;
		$hotel_details_request = $this->hotel_details_request ($ResultIndex);
		if ($hotel_details_request ['status']) {
			// get the response for hotel details
			$hotel_details_response = $GLOBALS ['CI']->api_interface->get_json_response ( $hotel_details_request ['data'] ['service_url'], $hotel_details_request ['data'] ['request'], $header );
			$GLOBALS ['CI']->custom_db->generate_static_response ( json_encode ( $hotel_details_response ) );
		
			/*
			 * $static_search_result_id = 812;//105;//67;//49;
			 * $hotel_details_response = $GLOBALS['CI']->hotel_model->get_static_response($static_search_result_id);
			 */
			if ($this->valid_hotel_details ( $hotel_details_response )) {
				$response ['data'] = $hotel_details_response['HotelDetails'];
				$response ['status'] = true;
			} else {
				$response ['data'] = $hotel_details_response;
			}
		}
		return $response;
	}

	function get_hotel_details($search_params, $HotelCode, $module = 'b2c') {
        $this->CI->load->driver('cache');
        $response ['data'] = array();
        $response ['status'] = false;
        $search_id = $search_params['search_id'];
        $hotel_details_request = $this->hotel_details_request($search_params, $HotelCode);

        $cache_search = $this->CI->config->item ( 'cache_hotel_search' );
        $search_hash = md5('Hotel_details'.REZLIVE_HOTEL);

        //debug($hotel_details_request);die();
        if ($hotel_details_request ['status']) {

            $GLOBALS ['CI']->custom_db->generate_static_response ($hotel_details_request ['data'] ['request'], 'REZLIVE hotel detail request' );
             $hotel_details_response = $GLOBALS ['CI']->api_interface->xml_post_request($hotel_details_request ['data'] ['service_url'], ('XML='.urlencode($hotel_details_request ['data'] ['request'])), $this->xml_header());
            
             //debug($hotel_details_response);die('===');
            $GLOBALS ['CI']->custom_db->generate_static_response ($hotel_details_response, 'REZLIVE hotel detail response' );
           
            $hotel_details_response = Converter::createArray($hotel_details_response);
            //debug($hotel_details_response);die('+++++++'); 
            if(isset($hotel_details_response['HotelFindResponse']['Hotels']['Hotel'])){
                ///format to Tmx format
                $hotel_details_response = $this->format_hotel_details($hotel_details_response);
                //debug($format_hotel_details_response);die();
                if ($this->valid_hotel_details ($hotel_details_response )) {
                    $response ['data'] = $hotel_details_response['HotelDetails'];

                    if ($cache_search) {
                            $cache_exp = $this->CI->config->item ( 'cache_hotel_details_ttl' );
                            $this->CI->cache->file->save ($search_hash, $response ['data'], $cache_exp );
                        }

                    $response ['status'] = true;
                } else {
                    $response ['data'] = $hotel_details_response;
                }
            }else{
                $response ['status'] = false;
            }
            
        }
        //debug($response);exit('--');
        return $response;
    }
	
	/**
	 * Balu A
	 * Block Room Before Going for payment and showing final booking page to user - TBO rule
	 * 
	 * @param array $pre_booking_params
	 *        	All the necessary data required in block room request - fetched from roomList and hotelDetails Request
	 */
	function block_room($pre_booking_params) {
		$header = $this->get_header ();
		$response ['status'] = false;
		$response ['data'] = array ();
		$search_data = $this->search_data ( $pre_booking_params ['search_id'] );
		$run_block_room_request = true;
		$block_room_request_count = 0;
		$pre_booking_params ['search_data'] = $search_data ['data'];
        debug($pre_booking_params);die('rezlive-prebook');
		$block_room_request = $this->get_block_room_request ( $pre_booking_params );
		$application_default_currency = admin_base_currency ();
		if ($block_room_request ['status'] == ACTIVE) {
			while ( $run_block_room_request ) {
				$GLOBALS ['CI']->custom_db->generate_static_response ( json_encode ( $block_room_request ['data'] ['request'] ) );
				$block_room_response = $GLOBALS ['CI']->api_interface->get_json_response ( $block_room_request ['data'] ['service_url'], $block_room_request ['data'] ['request'], $header );
				// debug($block_room_response);
				// exit;
				$GLOBALS ['CI']->custom_db->generate_static_response ( json_encode ( $block_room_response ) );
				
				$api_block_room_response_status = $block_room_response['Status'];
				$block_room_response = $block_room_response['BlockRoom'];
				
				if ($this->valid_response ($api_block_room_response_status) == false) {
					$run_block_room_request = false;
					$response ['status'] = false; // Indication for room block
					$response ['data'] ['msg'] = 'Some Problem Occured. Please Search Again to continue';
				} elseif ($this->is_room_blocked ( $block_room_response ) == true) {
					$run_block_room_request = false;
					$response ['status'] = true; // Indication for room block
				} else {
					
					// UPDATE RECURSSION
					// Reset pre booking params token and get new values
					$dynamic_params_url = '';
					// FIXME: Do All currency conversion after API call
					// Converting API currency data to preferred currency
					$temp_block_room_details ['data'] ['response'] = $block_room_response;
					$currency_obj = new Currency ( array (
							'module_type' => 'hotel',
							'from' => get_api_data_currency (),
							'to' => get_application_currency_preference () 
					) );

					$temp_block_room_details = $this->roomblock_data_in_preferred_currency ( $temp_block_room_details, $currency_obj );
					$temp_block_room_details = $temp_block_room_details['BlockRoomResult'] ['HotelRoomsDetails'];
					$_HotelRoomsDetails = get_room_index_list ( $temp_block_room_details );
					
					// $_HotelRoomsDetails = get_room_index_list($block_room_response['BlockRoomResult']['HotelRoomsDetails']);
					foreach ( $_HotelRoomsDetails as $___tk => $___tv ) {
						$dynamic_params_url [] = get_dynamic_booking_parameters ( $___tk, $___tv, $application_default_currency );
					}
					// update token key
					$pre_booking_params ['token'] = $dynamic_params_url;
					$pre_booking_params ['token_key'] = md5 ( serialized_data ( $dynamic_params_url ) );
					$block_room_request = $this->get_block_room_request ( $pre_booking_params );
				}
				$block_room_request_count ++; // Increment number of times request is run
				if ($block_room_request_count == 3 && $run_block_room_request == true) {
					// try max 3times to block the room
					$run_block_room_request = false;
				}
			}
			$response ['data'] ['response'] = $block_room_response;
		}
		//debug($response);exit;
		return $response;
	}
	
	/**
	 *
	 * @param array $booking_params        	
	 */
	function process_booking1($book_id, $booking_params)
	{
		// debug($booking_params);exit;
		$header = $this->get_header ();
		$response ['status'] = FAILURE_STATUS;
		$response ['data'] = array ();	
		$book_request = $this->get_book_request ( $booking_params, $book_id );		
		// booking request
		$GLOBALS ['CI']->custom_db->generate_static_response ( $book_request ['data'] ['request'] ); // release this
	

     	$book_response = $GLOBALS ['CI']->api_interface->get_json_response ( $book_request ['data'] ['service_url'], $book_request ['data'] ['request'], $header );
     

		$GLOBALS ['CI']->custom_db->generate_static_response ( json_encode ( $book_response ) );		
		
		$api_book_response_status = $book_response['Status'];
		$book_response['BookResult'] = @$book_response['CommitBooking']['BookingDetails'];
		/**
		 * PROVAB LOGGER *
		 */
		$GLOBALS ['CI']->private_management_model->provab_xml_logger ( 'Book_Room', $book_id, 'hotel', $book_request ['data'] ['request'], json_encode ( $book_response ) );
		// validate response
		if ($this->valid_response ( $api_book_response_status )) {
			$response ['status'] = SUCCESS_STATUS;
			$response ['data'] ['book_response'] = $book_response;
			$response ['data'] ['booking_params'] = $booking_params;
			// $response['data']['room_book_data'] = json_decode($block_data_array, true);
			// Convert Room Book Data in Application Currency
			$block_data_array = $book_request ['data'] ['request'];
			$room_book_data = json_decode ( $block_data_array, true );
			$room_book_data['HotelRoomsDetails'] = $this->formate_hotel_room_details($booking_params);
			
			$response ['data'] ['room_book_data'] = $this->convert_roombook_data_to_application_currency ( $room_book_data );
		}
		else{
			$response ['data']['message'] = $book_response['Message'];
		}
		// debug($response);exit;
		return $response;
	}
	/**
	 * Formates Hotel Room Details
	 * @param unknown_type $booking_params
	 */
	private function formate_hotel_room_details($booking_params)
	{
		 //debug($booking_params);exit('price check');
		$search_id = $booking_params ['token'] ['search_id'];
		$safe_search_data = $GLOBALS ['CI']->hotel_model->get_search_data ( $search_id );
		$search_data = json_decode ( $safe_search_data ['search_data'], true );
		$number_of_nights = get_date_difference ( date ( 'Y-m-d', strtotime ( $search_data ['hotel_checkin'] ) ), date ( 'Y-m-d', strtotime ( $search_data ['hotel_checkout'] ) ) );
		$NO_OF_ROOMS = $search_data ['rooms'];
		$k = 0;
	
		
		$HotelRoomsDetails = array();
		/* Counting No of adults and childs per room wise */
		for($i = 0; $i < $NO_OF_ROOMS; $i ++) {
			$booking_params ['token'] ['token'] [$i] ['no_of_pax'] = $search_data ['adult'] [$i] + $search_data ['child'] [$i];
		}
        //debug($booking_params['token']['extra_data']);
        $extra_data = json_decode($booking_params['token']['extra_data'],1);
		for($i = 0; $i < $NO_OF_ROOMS; $i ++) {
			$room_detail = array ();
			$room_detail ['RoomIndex'] = $booking_params ['token'] ['token'] [$i] ['RoomIndex'];
			$room_detail ['RatePlanCode'] = $booking_params ['token'] ['token'] [$i] ['RatePlanCode'];
			$room_detail ['RatePlanName'] = $booking_params ['token'] ['token'] [$i] ['RatePlanName'];
			$room_detail ['RoomTypeCode'] = $booking_params ['token'] ['token'] [$i] ['RoomTypeCode'];
			$room_detail ['RoomTypeName'] = $extra_data['data']['pre_booking_request_data'][
            'RoomDetails']['RoomDetail']['Type'];
			$room_detail ['SmokingPreference'] = 0;
			
			$room_detail ['Price'] ['CurrencyCode'] = $booking_params ['token'] ['price_token'] [$i] ['CurrencyCode'];
			$room_detail ['Price'] ['RoomPrice'] = $booking_params ['token'] ['price_token'] [$i] ['RoomPrice'];
			$room_detail ['Price'] ['Tax'] = $booking_params ['token'] ['price_token'] [$i] ['Tax'];
			$room_detail ['Price'] ['ExtraGuestCharge'] = $booking_params ['token'] ['price_token'] [$i] ['ExtraGuestCharge'];
			$room_detail ['Price'] ['ChildCharge'] = $booking_params ['token'] ['price_token'] [$i] ['ChildCharge'];
			$room_detail ['Price'] ['OtherCharges'] = $booking_params ['token'] ['price_token'] [$i] ['OtherCharges'];
			$room_detail ['Price'] ['Discount'] = $booking_params ['token'] ['price_token'] [$i] ['Discount'];
			$room_detail ['Price'] ['PublishedPrice'] = $booking_params ['token'] ['price_token'] [$i] ['PublishedPrice'];
			$room_detail ['Price'] ['PublishedPriceRoundedOff'] = $booking_params ['token'] ['price_token'] [$i] ['PublishedPriceRoundedOff'];
			$room_detail ['Price'] ['OfferedPrice'] = $booking_params ['token'] ['price_token'] [$i] ['OfferedPrice'];
			$room_detail ['Price'] ['OfferedPriceRoundedOff'] = $booking_params ['token'] ['price_token'] [$i] ['OfferedPriceRoundedOff'];
			//$room_detail ['Price'] ['SmokingPreference'] = $booking_params ['token'] ['token'] [$i] ['SmokingPreference'];
			$room_detail ['Price'] ['ServiceTax'] = $booking_params ['token'] ['price_token'] [$i] ['ServiceTax'];
			$room_detail ['Price'] ['Tax'] = $booking_params ['token'] ['price_token'] [$i] ['Tax'];
			$room_detail ['Price'] ['ExtraGuestCharge'] = $booking_params ['token'] ['price_token'] [$i] ['ExtraGuestCharge'];
			$room_detail ['Price'] ['ChildCharge'] = $booking_params ['token'] ['price_token'] [$i] ['ChildCharge'];
			$room_detail ['Price'] ['OtherCharges'] = $booking_params ['token'] ['price_token'] [$i] ['OtherCharges'];
			$room_detail ['Price'] ['Discount'] = $booking_params ['token'] ['price_token'] [$i] ['Discount'];
			$room_detail ['Price'] ['AgentCommission'] = $booking_params ['token'] ['price_token'] [$i] ['AgentCommission'];
			$room_detail ['Price'] ['AgentMarkUp'] = $booking_params ['token'] ['price_token'] [$i] ['AgentMarkUp'];
			$room_detail ['Price'] ['TDS'] = $booking_params ['token'] ['price_token'] [$i] ['TDS'];
			$HotelRoomsDetails[$i] = $room_detail;
			
			for($j = 0; $j < $booking_params ['token'] ['token'] [$i] ['no_of_pax']; $j ++) {
				$pax_list = array (); // Reset Pax List Array
				$pax_title = get_enum_list ( 'title', $booking_params ['name_title'] [$k] );
				$pax_list ['Title'] = $pax_title;
				$pax_list ['FirstName'] = $booking_params ['first_name'] [$k];
				$pax_list ['MiddleName'] = $booking_params ['middle_name'] [$k];
				$pax_list ['LastName'] = $booking_params ['last_name'] [$k];
				$pax_list ['Phoneno'] = $booking_params ['passenger_contact'];
				$pax_list ['Email'] = $booking_params ['billing_email'];
				$pax_list ['PaxType'] = $booking_params ['passenger_type'] [$k];
				
				$pax_lead = false;
				// temp
				if ($j == 0) {
					$pax_lead = true;
				}
				$pax_list ['LeadPassenger'] = $pax_lead;
				/* Age Calculation of Pax */
				$from = new DateTime ( $booking_params ['date_of_birth'] [$k] );
				$to = new DateTime ( 'today' );
				$pax_age = $from->diff ( $to )->y;
				$pax_list ['Age'] = $pax_age;
				$HotelRoomsDetails[$i] ['HotelPassenger'] [$j] = $pax_list;
				$k ++;
			}
		}

        //debug($HotelRoomsDetails);die('1111');
		return $HotelRoomsDetails;
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
            /*error_reporting(E_ALL);
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);*/
		//debug($params);
		// Need to return following data as this is needed to save the booking fare in the transaction details
		$response ['fare'] = $response ['domain_markup'] = $response ['level_one_markup'] = 0;

		$domain_origin = get_domain_auth_id ();
		$master_search_id = $params ['booking_params'] ['token'] ['search_id'];
		$search_data = $this->search_data ( $master_search_id );
		//$status = BOOKING_CONFIRMED;
		$app_reference = $app_booking_id;
		$booking_source = $params ['booking_params'] ['token'] ['booking_source'];
		
		$currency_obj = $params ['currency_obj'];
		$deduction_cur_obj = clone $currency_obj;
		$promo_currency_obj = $params['promo_currency_obj'];
		// PREFERRED TRANSACTION CURRENCY AND CURRENCY CONVERSION RATE
		$transaction_currency = get_application_currency_preference ();
		$application_currency = admin_base_currency ();
		$currency_conversion_rate = $currency_obj->transaction_currency_conversion_rate ();
		
		$booking_id = $params ['book_response'] ['BookResult'] ['BookingId'];
		$booking_reference = $params ['book_response'] ['BookResult'] ['BookingRefNo'];

		$confirmation_reference = $params ['book_response'] ['BookResult'] ['ConfirmationNo'];
		$status =  $params ['book_response'] ['BookResult'] ['booking_status'];
		$no_of_nights = intval ( $search_data ['data'] ['no_of_nights'] );
		$HotelRoomsDetails = force_multple_data_format ( $params ['room_book_data'] ['HotelRoomsDetails'] );
		$total_room_count = count ( $HotelRoomsDetails );
		$book_total_fare = $params ['booking_params'] ['token'] ['price_summary'] ['OfferedPriceRoundedOff']; // (TAX+ROOM PRICE)
		$room_price = $params ['booking_params'] ['token'] ['price_summary'] ['RoomPrice'];
		
		if ($module == 'b2c') {
			$markup_total_fare = $currency_obj->get_currency ( $book_total_fare, true, false, true, $no_of_nights * $total_room_count ); // (ON Total PRICE ONLY)
			$ded_total_fare = $deduction_cur_obj->get_currency ( $book_total_fare, true, true, false, $no_of_nights * $total_room_count ); // (ON Total PRICE ONLY)
			$admin_markup = sprintf ( "%.2f", $markup_total_fare ['default_value'] - $ded_total_fare ['default_value'] );
			$agent_markup = sprintf ( "%.2f", $ded_total_fare ['default_value'] - $book_total_fare );
		} else {
			// B2B Calculation
			$markup_total_fare = $currency_obj->get_currency ( $book_total_fare, true, true, false, $no_of_nights * $total_room_count ); // (ON Total PRICE ONLY)
			$ded_total_fare = $deduction_cur_obj->get_currency ( $book_total_fare, true, false, true, $no_of_nights * $total_room_count ); // (ON Total PRICE ONLY)
			$admin_markup = sprintf ( "%.2f", $markup_total_fare ['default_value'] - $ded_total_fare ['default_value'] );
			$agent_markup = sprintf ( "%.2f", $ded_total_fare ['default_value'] - $book_total_fare );
		}
		

		
		$currency = $params ['booking_params'] ['token'] ['default_currency'];
		$hotel_name = $params ['booking_params'] ['token'] ['HotelName'];
		if($params ['booking_params'] ['token'] ['StarRating'] ==''){
            $star_rating = 0;
        }else{
            $star_rating = $params ['booking_params'] ['token'] ['StarRating'];
        }
        
		$hotel_code = '';
		$phone_number = $params ['booking_params'] ['passenger_contact'];
		$phone_code = $params ['booking_params'] ['phone_country_code'];
		$alternate_number = 'NA';
		$email = $params ['booking_params'] ['billing_email'];
		$hotel_check_in = db_current_datetime ( str_replace ( '/', '-', $search_data ['data'] ['from_date'] ) );
		$hotel_check_out = db_current_datetime ( str_replace ( '/', '-', $search_data ['data'] ['to_date'] ) );
		$payment_mode = $params ['booking_params'] ['payment_method'];
		
		$country_name = $GLOBALS ['CI']->db_cache_api->get_country_list ( array (
				'k' => 'origin',
				'v' => 'name' 
		), array (
				'origin' => $params ['booking_params'] ['billing_country'] 
		) );
		// $city_name = $GLOBALS['CI']->db_cache_api->get_city_list(array('k' => 'origin', 'v' => 'destination'), array('origin' => $params['booking_params']['billing_city']));
		$attributes = array (
				'address' => @$params ['booking_params'] ['billing_address_1'],
				'billing_country' => @$country_name [$params ['booking_params'] ['billing_country']],
				// 'billing_city' => $city_name[$params['booking_params']['billing_city']],
				'billing_city' => @$params ['booking_params'] ['billing_city'],
				'billing_zipcode' => @$params ['booking_params'] ['billing_zipcode'],
				'HotelCode' => @$params ['booking_params'] ['token'] ['HotelCode'],
				'search_id' => @$params ['booking_params'] ['token'] ['search_id'],
				'TraceId' => @$params ['booking_params'] ['token'] ['TraceId'],
				'HotelName' => @$params ['booking_params'] ['token'] ['HotelName'],
				'StarRating' => @$params ['booking_params'] ['token'] ['StarRating'],
				'HotelImage' => @$params ['booking_params'] ['token'] ['HotelImage'],
				'HotelAddress' => @$params ['booking_params'] ['token'] ['HotelAddress'],
				'CancellationPolicy' => @$params ['booking_params'] ['token'] ['CancellationPolicy'],
				'Boarding_details' => @$params ['booking_params'] ['token'] ['Boarding_details']
		);
        $booking_billing_type = $params['booking_params']['selected_pm'];
		$created_by_id = intval ( @$GLOBALS ['CI']->entity_user_id );
		// SAVE Booking details
		$GLOBALS ['CI']->hotel_model->save_booking_details ( $domain_origin, $status, $app_reference, $booking_source, $booking_id, $booking_reference, $confirmation_reference, $hotel_name, $star_rating, $hotel_code, $phone_number, $alternate_number, $email, $hotel_check_in, $hotel_check_out, $payment_mode, json_encode ( $attributes ), $created_by_id, $transaction_currency, $currency_conversion_rate, $phone_code, $booking_billing_type);
		
		$check_in = db_current_datetime ( str_replace ( '/', '-', $search_data ['data'] ['from_date'] ) );
		$check_out = db_current_datetime ( str_replace ( '/', '-', $search_data ['data'] ['to_date'] ) );
		
		$location = $search_data ['data'] ['location'];
		// loop token of token
		foreach ( $HotelRoomsDetails as $k => $v ) {
			$room_type_name = $v ['RoomTypeName'];
			$bed_type_code = $v ['RoomTypeCode'];
			$smoking_preference = get_smoking_preference ( $v ['SmokingPreference'] );
			$smoking_preference = $smoking_preference ['label'];
			$total_fare = $v ['Price'] ['OfferedPriceRoundedOff'];
			$room_price = $v ['Price'] ['RoomPrice'];
			$gst_value = 0;
			if ($module == 'b2c') {
				$markup_total_fare = $currency_obj->get_currency ( $total_fare, true, false, true, $no_of_nights ); // (ON Total PRICE ONLY)
				$ded_total_fare = $deduction_cur_obj->get_currency ( $total_fare, true, true, false, $no_of_nights ); // (ON Total PRICE ONLY)
				$admin_markup = sprintf ( "%.2f", $markup_total_fare ['default_value'] - $ded_total_fare ['default_value'] );
				$agent_markup = sprintf ( "%.2f", $ded_total_fare ['default_value'] - $total_fare );
				//adding gst
		        if($admin_markup > 0 ){
		            $gst_details = $GLOBALS['CI']->custom_db->single_table_records('gst_master', '*', array('module' => 'hotel'));
		            if($gst_details['status'] == true){
		                if($gst_details['data'][0]['gst'] > 0){
		                    $gst_value = ($admin_markup/100) * $gst_details['data'][0]['gst'];
		                	$gst_value  = roundoff_number($gst_value);
		                }
		            }
		        }
			} else {
				// B2B Calculation - Room wise price
                            //echo 'total_fare',debug($total_fare);
				$markup_total_fare = $currency_obj->get_currency ( $total_fare, true, true, false, $no_of_nights ); // (ON Total PRICE ONLY)
                                $ded_total_fare = $deduction_cur_obj->get_currency(($markup_total_fare ['default_value']), true, false, true, $no_of_nights ); // (ON Total PRICE ONLY)
                $admin_markup = sprintf ( "%.2f", $markup_total_fare ['default_value'] -  $total_fare);
				$agent_markup = sprintf ( "%.2f", $ded_total_fare ['default_value'] - $markup_total_fare ['default_value']);
                $markup = $admin_markup+$agent_markup;             
				//adding gst
		        if($markup > 0 ){
		            $gst_details = $GLOBALS['CI']->custom_db->single_table_records('gst_master', '*', array('module' => 'hotel'));
		            if($gst_details['status'] == true){
		                if($gst_details['data'][0]['gst'] > 0){
		                    $gst_value = ($markup/100) * $gst_details['data'][0]['gst'];
		                	$gst_value  = roundoff_number($gst_value);
		                }
		            }
		        }
			}
			
			$total_fare_markup = round($book_total_fare+$admin_markup+$gst_value);
			$attributes = '';
			// SAVE Booking Itinerary details
			$GLOBALS ['CI']->hotel_model->save_booking_itinerary_details ( $app_reference, $location, $check_in, $check_out, $room_type_name, $bed_type_code, $status, $smoking_preference, $total_fare, $admin_markup, $agent_markup, $currency, $attributes, @$v ['RoomPrice'], @$v ['Tax'], @$v ['ExtraGuestCharge'], @$v ['ChildCharge'], @$v ['OtherCharges'], @$v ['Discount'], @$v ['ServiceTax'], @$v ['AgentCommission'], @$v ['AgentMarkUp'], @$v ['TDS'], $gst_value );
			$passengers = force_multple_data_format ( $v ['HotelPassenger'] );
			if (valid_array ( $passengers ) == true) {
				foreach ( $passengers as $passenger ) {
					$title = $passenger ['Title'];
					$first_name = $passenger ['FirstName'];
					$middle_name = $passenger ['MiddleName'];
					$last_name = $passenger ['LastName'];
					$phone = $passenger ['Phoneno'];
					$email = $passenger ['Email'];
					$pax_type = $passenger ['PaxType'];
					$age = '';//$passenger['Age'];
					$date_of_birth = array_shift ( $params ['booking_params'] ['date_of_birth'] ); //
					
					$passenger_nationality_id = array_shift ( $params ['booking_params'] ['passenger_nationality'] ); //
					$passport_issuing_country_id = array_shift ( $params ['booking_params'] ['passenger_passport_issuing_country'] ); //
					
					$passenger_nationality = $GLOBALS ['CI']->db_cache_api->get_country_list ( array (
							'k' => 'origin',
							'v' => 'name' 
					), array (
							'origin' => $passenger_nationality_id 
					) );
					$passport_issuing_country = $GLOBALS ['CI']->db_cache_api->get_country_list ( array (
							'k' => 'origin',
							'v' => 'name' 
					), array (
							'origin' => $passport_issuing_country_id 
					) );
					
					$passenger_nationality = $passenger_nationality [$passenger_nationality_id];
					$passport_issuing_country = $passport_issuing_country [$passport_issuing_country_id];
					$passport_number = array_shift ( $params ['booking_params'] ['passenger_passport_number'] ); //
					$passport_expiry_date = array_shift ( $params ['booking_params'] ['passenger_passport_expiry_year'] ) . '-' . array_shift ( $params ['booking_params'] ['passenger_passport_expiry_month'] ) . '-' . array_shift ( $params ['booking_params'] ['passenger_passport_expiry_day'] ); //
					$attributes = array ();
					
					// SAVE Booking Pax details
					$GLOBALS ['CI']->hotel_model->save_booking_pax_details ( $app_reference, $title, $first_name, $middle_name, $last_name,$phone, $email, $pax_type, $date_of_birth, $passenger_nationality, $passport_number, $passport_issuing_country, $passport_expiry_date, $status, serialize($attributes),$age);
				}
			}
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
		if ($module == 'b2c') {
			$convinence = $currency_obj->convenience_fees ( $total_fare_markup, $master_search_id );
			$convinence_row = $currency_obj->get_convenience_fees ();
			$convinence_value = $convinence_row ['value'];
			$convinence_type = $convinence_row ['type'];
			$convinence_per_pax = $convinence_row ['per_pax']; 
			if($params['booking_params']['promo_actual_value']){
				$discount = get_converted_currency_value ( $promo_currency_obj->force_currency_conversion ( $params['booking_params']['promo_actual_value']) );
			}			
			//$discount = @$params ['booking_params'] ['promo_code_discount_val'];
			$promo_code = @$params ['booking_params'] ['promo_code'];
		} elseif ($module == 'b2b') {
			$discount = 0;
		}
		$GLOBALS ['CI']->load->model ( 'transaction' );

		// SAVE Booking convinence_discount_details details
		$GLOBALS ['CI']->transaction->update_convinence_discount_details ( 'hotel_booking_details', $app_reference, $discount, $promo_code, $convinence, $convinence_value, $convinence_type, $convinence_per_pax );
		/**
		 * ************ Update Convinence Fees And Other Details End *****************
		 */
		
		$response ['fare'] = $book_total_fare;
		$response ['admin_markup'] = $admin_markup;
		$response ['agent_markup'] = $agent_markup;
		$response ['convinence'] = $convinence;
		$response ['discount'] = $discount;
		$response ['transaction_currency'] = $transaction_currency;
		$response ['currency_conversion_rate'] = $currency_conversion_rate;
		//booking_status
		$response['booking_status'] = $status;
		return $response;
	}
	/**
	 * Balu A
	 * Convert Room Book Data in Application Currency
	 * 
	 * @param
	 *        	$currency_obj
	 */
	private function convert_roombook_data_to_application_currency($room_book_data) {
		$application_default_currency = admin_base_currency ();
		$currency_obj = new Currency ( array (
				'module_type' => 'hotel',
				'from' => get_api_data_currency (),
				'to' => admin_base_currency () 
		) );
		$master_room_book_data = array ();
		$HotelRoomsDetails = array ();
		foreach ( $room_book_data ['HotelRoomsDetails'] as $hrk => $hrv ) {
			$HotelRoomsDetails [$hrk] = $hrv;
			$HotelRoomsDetails [$hrk] ['Price'] = $this->preferred_currency_fare_object ( $hrv ['Price'], $currency_obj, $application_default_currency );
		}
		$master_room_book_data = $room_book_data;
		$master_room_book_data ['HotelRoomsDetails'] = $HotelRoomsDetails;
		return $master_room_book_data;
	}
	/**
	 * Balu A
	 * Cancel Booking
	 */
	function cancel_booking($booking_details)
	{
		$header = $this->get_header ();
		$response ['data'] = array ();
		$response ['status'] = FAILURE_STATUS;
		$resposne ['msg'] = 'Remote IO Error';
		$BookingId = $booking_details ['booking_id'];
		$app_reference = $booking_details ['app_reference'];
		$cancel_booking_request = $this->cancel_booking_request_params($app_reference );
		if ($cancel_booking_request ['status']) {
			// 1.SendChangeRequest
			$GLOBALS ['CI']->custom_db->generate_static_response ( json_encode ( $cancel_booking_request ) );
			
			$cancel_booking_response = $GLOBALS ['CI']->api_interface->get_json_response ( $cancel_booking_request ['data'] ['service_url'], $cancel_booking_request ['data'] ['request'], $header );
			$GLOBALS ['CI']->custom_db->generate_static_response ( json_encode ( $cancel_booking_response ) );
			
			// $cancel_booking_response = $GLOBALS['CI']->hotel_model->get_static_response(3317);
			if (valid_array ( $cancel_booking_response ) == true && $cancel_booking_response ['Status'] == SUCCESS_STATUS) {
				
				// Save Cancellation Details
				$hotel_cancellation_details = $cancel_booking_response ['CancelBooking']['CancellationDetails'];
				$GLOBALS ['CI']->hotel_model->update_cancellation_details ( $app_reference, $hotel_cancellation_details );
				$response ['status'] = SUCCESS_STATUS;
				
			} else {
				$response ['msg'] = $cancel_booking_response['Message'];
			}
		}
		return $response;
	}
	/**
	 * Balu A
	 * Cancellation Request Status
	 */
	function get_cancellation_refund_details($ChangeRequestId, $app_reference) {
		$header = $this->get_header ();
		$response ['data'] = array ();
		$response ['status'] = FAILURE_STATUS;
		$resposne ['msg'] = 'Remote IO Error';
		$api_request = $this->cancellation_refund_request_params ( $ChangeRequestId, $app_reference );
		if ($api_request ['status']) {
			$api_response = $GLOBALS ['CI']->api_interface->get_json_response ( $api_request ['data'] ['service_url'], $api_request ['data'] ['request'], $header );
			if (valid_array ( $api_response ) == true && isset ( $api_response ['Status'] ) == true && $api_response ['Status'] == SUCCESS_STATUS) {
				$response ['data'] = $api_response ['RefundDetails'];
				$response ['status'] = SUCCESS_STATUS;
			} else {
				$resposne ['msg'] = @$api_response ['Message'];
			}
		}
		return $response;
	}
	/**
	 * Sawood
	 * check and return status is success or not
	 * 
	 * @param unknown_type $response_status        	
	 */
	function valid_book_response($response_status) {
		$status = false;
		if (is_array ( $response_status ) and ! empty ( $response_status ) and is_array ( $response_status ['BookResult'] ) and ! empty ( $response_status ['BookResult'] ) and $response_status ['BookResult'] ['ResponseStatus'] == SUCCESS_STATUS and isset ( $response_status ['BookResult'] ['HotelBookingStatus'] ) and $response_status ['BookResult'] ['HotelBookingStatus'] != '' and ($response_status ['BookResult'] ['HotelBookingStatus'] != 'Pending' || $response_status ['BookResult'] ['HotelBookingStatus'] != 'Vouchered' || $response_status ['BookResult'] ['HotelBookingStatus'] != 'Confirmed')) {
			$status = true;
		}
		return $status;
	}
	
	/**
	 * Balu A
	 * check and return status is success or not
	 * 
	 * @param unknown_type $response_status        	
	 */
	function valid_response($response_status) {
		$status = true;
		if ($response_status != SUCCESS_STATUS) {
			$status = false;
		}
		return $status;
	}
	
	/**
	 * Balu A
	 *
	 * Check if the room was blocked successfully
	 * 
	 * @param array $block_room_response
	 *        	block room response
	 */
	private function is_room_blocked($block_room_response) {
		$room_blocked = false;
		if (isset ( $block_room_response ['BlockRoomResult'] ) == true and $block_room_response ['BlockRoomResult'] ['IsPriceChanged'] == false and $block_room_response ['BlockRoomResult'] ['IsCancellationPolicyChanged'] == false) {
			$room_blocked = true;
		}
		
		return $room_blocked;
	}
	
	/**
	 * Balu A
	 * check if the room list is valid or not
	 * 
	 * @param
	 *        	$room_list
	 */
	private function valid_room_details_details($room_list) {
		$status = false;
		if (valid_array ( $room_list ) == true and isset ( $room_list ['Status'] ) == true and $room_list ['Status']  == SUCCESS_STATUS) {
			$status = true;
		}
		return $status;
	}
	
	/**
	 * Balu A
	 * check if the hotel response which is received from server is valid or not
	 * 
	 * @param
	 *        	$hotel_details
	 */
	private function valid_hotel_details($hotel_details) {
		$status = false;
		if (valid_array ( $hotel_details ) == true and isset ( $hotel_details ['Status'] ) == true and $hotel_details ['Status']  == SUCCESS_STATUS) {
			$status = true;
		}
		return $status;
	}
	
	/**
	 * Balu A
	 * check if the search response is valid or not
	 * 
	 * @param array $search_result
	 *        	search result response to be validated
	 */
	private function valid_search_result($search_result) {
		if (valid_array ( $search_result ) == true and isset ( $search_result ['Status'] ) == true and $search_result ['Status']  == SUCCESS_STATUS) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Balu A
	 * Update and return price details
	 */
	public function update_block_details($room_details, $booking_parameters,$cancel_currency_obj) {
		//debug();exit('9');
		$Surcharge_total ='';
		foreach ($room_details['HotelRoomsDetails'] as $key => $value) {
			$Surcharge_total += @$value['Surcharge_total'];
		}
		
        $extra_data = $room_details['HotelRoomsDetails'][0]['extra_data'];
		$booking_parameters ['BlockRoomId'] = $room_details ['BlockRoomId'];
		$room_details ['HotelRoomsDetails'] = get_room_index_list ( $room_details ['HotelRoomsDetails'] );
		//debug($room_details ['HotelRoomsDetails']);
		//echo "-----";
		$booking_parameters ['token'] = array(); // Remove all the token details
		$total_OfferedPriceRoundedOff = $Tax = '';
		
		foreach ( $room_details ['HotelRoomsDetails'] as $__rc_key => $__rc_value ) {
			
			$booking_parameters ['token'] [] = get_dynamic_booking_parameters ( $__rc_key, $__rc_value, get_application_currency_preference () );
			$booking_parameters ['price_token'] [] = $__rc_value ['Price'];
			$booking_parameters['HotelCode'] = $__rc_value['HotelCode'];
		}
		
		$policy_string ='';
		$cancel_string='';

		$last_cancellation_date=$room_details['HotelRoomsDetails'][0]['LastCancellationDate'];
		
		$cancellation_details = array_reverse($room_details['HotelRoomsDetails'][0]['CancellationPolicies']);
		
		$cancellation_rev_details =  array_reverse($room_details['HotelRoomsDetails'][0]['CancellationPolicies']);
		$room_price = 0;
		foreach ($room_details['HotelRoomsDetails'] as $p_key => $p_value) {
			$room_price +=$p_value['Price']['RoomPrice'];
		}
		
		$cancel_count = count($cancellation_details);
		$cancellation_rev_details = $this->php_arrayUnique($cancellation_rev_details,'Charge');			
		$cancellation_details =  $this->php_arrayUnique($cancellation_details,'Charge');
		//debug($room_details ['HotelRoomsDetails']);exit('5');			
		if($cancellation_details && !empty($last_cancellation_date)){
				foreach ($cancellation_details as $key => $value) {
					$amount = 0;
					$policy_string ='';

					if($value['Charge']==0){
						 $policy_string .='No cancellation charges, if cancelled before '.date('d M Y',strtotime($value['ToDate']));
						$last_cancellation_date = $value['ToDate'];
					}else{
						
						if(isset($cancellation_rev_details[$key+1])){
							if($value['ChargeType']==1){
								$amount =  $cancel_currency_obj->get_currency_symbol($cancel_currency_obj->to_currency)." ".$value['Charge'];
							}elseif($value['ChargeType']==2){
								$amount =  $cancel_currency_obj->get_currency_symbol($cancel_currency_obj->to_currency)." ".$room_price;
							}
							
							$current_date = date('Y-m-d');
							$cancell_date = date('Y-m-d',strtotime($value['FromDate']));
							if($cancell_date >$current_date){
								//$value['FromDate'] = date('Y-m-d');
									$policy_string .='Cancellations made after '.date('d M Y',strtotime($value['FromDate'])).' to '.date('d M Y',strtotime($value['ToDate'])).', would be charged '.$amount;
							}
							//$policy_string .='Cancellations made after '.date('d M Y',strtotime($value['FromDate'])).' to '.date('d M Y',strtotime($value['ToDate'])).', would be charged '.$amount;
						}else{
							if($value['ChargeType']==1){
								$amount =  $cancel_currency_obj->get_currency_symbol($cancel_currency_obj->to_currency)." ".$value['Charge'];
							}elseif ($value['ChargeType']==2) {
								$amount =  $cancel_currency_obj->get_currency_symbol($cancel_currency_obj->to_currency)." ".$room_price;
							}
							
							$current_date = date('Y-m-d');
							$cancell_date = date('Y-m-d',strtotime($value['FromDate']));
							if($cancell_date >$current_date){
								$value['FromDate'] = $value['FromDate'];
								$policy_string .='Cancellations made after '.date('d M Y',strtotime($value['FromDate'])).', or no-show, would be charged '.$amount;
							}else{
								$value['FromDate'] = date('Y-m-d');
								$policy_string .='This rate is non-refundable. If you cancel this booking you will not be refunded any of the payment.';
							}
							
						}
					}
					
					$cancel_string .= $policy_string.'<br/>';
					/*if($value['ChargeType']==1){
						if($value['Charge']!=0){
							$amount =  $cancel_currency_obj->get_currency_symbol($cancel_currency_obj->to_currency)." ".$value['Charge'];
						}else{
							$last_cancellation_date = $value['ToDate'];
						}
							
					}elseif($value['ChargeType']==2){
						$amount = '100%';
					}
					$policy_string = ' '.$amount.' will be charged, If cancelled between '.$value['FromDate'].' and '.$value['ToDate'];
					$cancel_string .= $policy_string.' #!# ';*/
						
				}
				
		}else{
			$cancel_string ='This rate is non-refundable. If you cancel this booking you will not be refunded any of the payment.';
		}		
		if(isset($room_details['HotelRoomsDetails'][0]['RoomTypeName'])){
			$booking_parameters['RoomTypeName'] = $room_details['HotelRoomsDetails'][0]['RoomTypeName'];
		}
                
		if(isset($room_details['HotelRoomsDetails'][0]['Boarding_details'])){
			$booking_parameters['Boarding_details1'][] = $room_details['HotelRoomsDetails'][0]['Boarding_details'];	
		}
                
                
                
		$booking_parameters['CancellationPolicy'] = array($cancel_string);
		$booking_parameters['LastCancellationDate'] = $last_cancellation_date;
		$booking_parameters['CancellationPolicy_API'] =  array($room_details['HotelRoomsDetails'][0]['CancellationPolicy']);
		$booking_parameters['TM_Cancellation_Charge'] = $cancellation_details;

		$booking_parameters['Boarding_details'] = $room_details['HotelRoomsDetails'][0]['Boarding_details'];
		$booking_parameters['Surcharge_total'] = @$Surcharge_total;
		$booking_parameters['sur_Charge_exclude'] = @$room_details['HotelRoomsDetails'][0]['surCharge_exclude'];
		$booking_parameters['surCharge_exclude_name'] = @$room_details['HotelRoomsDetails'][0]['surCharge_exclude_name'];
		$booking_parameters ['price_summary'] = tbo_summary_room_combination ( $room_details ['HotelRoomsDetails'] );
        $booking_parameters['extra_data'] = $extra_data;
		 //debug($booking_parameters);
		 //exit;
		return $booking_parameters;
	}
		/*php check array unique*/
	/**/
	function php_arrayUnique($array,$key){
		 $temp_array = array(); 
		    $i = 0; 
		    $key_array = array(); 
		    
		    foreach($array as $val) { 
		        if (!in_array($val[$key], $key_array)) { 
		            $key_array[$i] = $val[$key]; 
		            $temp_array[$i] = $val; 
		        } 
		        $i++; 
		    } 
		    return $temp_array; 
	}
	/**
	 * parse data according to voucher needs
	 * 
	 * @param array $data        	
	 */
	function parse_voucher_data($data) {
		$response = $data;
		return $response;
	}
	
	/**
	 * Balu A
	 * convert search params to format
	 */
	public function search_data($search_id) {
		$response ['status'] = true;
		$response ['data'] = array ();
		if (empty ( $this->master_search_data ) == true and valid_array ( $this->master_search_data ) == false) {
			$clean_search_details = $GLOBALS ['CI']->hotel_model->get_safe_search_data ( $search_id );
			
			if ($clean_search_details ['status'] == true) {
				$response ['status'] = true;
				$response ['data'] = $clean_search_details ['data'];
				// 28/12/2014 00:00:00 - date format
				$response ['data'] ['from_date'] = date ( 'd/m/Y', strtotime ( $clean_search_details ['data'] ['from_date'] ) );
				$response ['data'] ['to_date'] = date ( 'd/m/Y', strtotime ( $clean_search_details ['data'] ['to_date'] ) );
				
				$response ['data'] ['raw_from_date'] = $clean_search_details ['data'] ['from_date'];
				$response ['data'] ['raw_to_date'] = $clean_search_details ['data'] ['to_date'];
				$response ['data'] ['location_id'] = $clean_search_details ['data'] ['hotel_destination'];
				$response ['data'] ['CityId'] =  $clean_search_details ['data'] ['hotel_destination'];
				//get countrycode 
				$get_country_code = $GLOBALS['CI']->custom_db->single_table_records('all_api_city_master','*',array('origin'=>$clean_search_details ['data'] ['hotel_destination']));
				// debug($clean_search_details);exit;
				if($clean_search_details['data']['search_type'] == 'location_search'){
					$response ['data'] ['country_code'] = $clean_search_details['data']['countrycode'];
				}
				else{
					$response ['data'] ['country_code'] = $get_country_code['data'][0]['country_code'];
				}
				
				//debug($response);
				//$response ['data'] ['country_code']
				//below code comment by ela
				// get city id based
				// $location_details = $GLOBALS ['CI']->hotel_model->tbo_hotel_city_id ( $clean_search_details ['data'] ['city_name'], $clean_search_details ['data'] ['country_name'] );
				// if ($location_details ['status']) {
				// 	$response ['data'] ['country_code'] = $location_details ['data'] ['country_code'];
				// 	$response ['data'] ['location_id'] = $location_details ['data'] ['origin'];
				// } else {
				// 	$response ['data'] ['country_code'] = $response ['data'] ['location_id'] = '';
				// }
				$this->master_search_data = $response ['data'];
			} else {
				$response ['status'] = false;
			}
		} else {
			$response ['data'] = $this->master_search_data;
		}
		
		$this->search_hash = md5 ( serialized_data ( $response ['data'].REZLIVE_HOTEL ) );
		return $response;
	}
	
	/**
	 * Markup for search result
	 * 
	 * @param array $price_summary        	
	 * @param object $currency_obj        	
	 * @param number $search_id        	
	 */
	function update_search_markup_currency(& $price_summary, & $currency_obj, $search_id, $level_one_markup = false, $current_domain_markup = true) {
		$search_data = $this->search_data ( $search_id );
		$no_of_nights = $this->master_search_data ['no_of_nights'];
		$no_of_rooms = $this->master_search_data ['room_count'];
		//$multiplier = ($no_of_nights * $no_of_rooms);
		$multiplier = $no_of_nights;
		return $this->update_markup_currency ( $price_summary, $currency_obj, $multiplier, $level_one_markup, $current_domain_markup );
	}
	/**
	 * Markup for search result
	 * 
	 * @param array $price_summary        	
	 * @param object $currency_obj        	
	 * @param number $search_id        	
	 */
	function update_search_markup_currency_one_night(& $price_summary, & $currency_obj, $search_id, $level_one_markup = false, $current_domain_markup = true) {
		$search_data = $this->search_data ( $search_id );
		$no_of_nights = $this->master_search_data ['no_of_nights'];
		$no_of_rooms = $this->master_search_data ['room_count'];
		//$multiplier = ($no_of_nights * $no_of_rooms);
		$multiplier = 1;
		return $this->update_markup_currency ( $price_summary, $currency_obj, $multiplier, $level_one_markup, $current_domain_markup );
	}

	/**
	 * Markup for Room List
	 * 
	 * @param array $price_summary        	
	 * @param object $currency_obj        	
	 * @param number $search_id        	
	 */
	function update_room_markup_currency(& $price_summary, & $currency_obj, $search_id, $level_one_markup = false, $current_domain_markup = true) {
		
		$search_data = $this->search_data ( $search_id );
		$no_of_nights = $this->master_search_data ['no_of_nights'];
		$no_of_rooms = 1;
		//$multiplier = ($no_of_nights * $no_of_rooms);
		$multiplier = $no_of_nights;
		return $this->update_markup_currency ( $price_summary, $currency_obj, $multiplier, $level_one_markup, $current_domain_markup );
	}
	
	/**
	 * Markup for Booking Page List
	 * 
	 * @param array $price_summary        	
	 * @param object $currency_obj        	
	 * @param number $search_id        	
	 */
	function update_booking_markup_currency(& $price_summary, & $currency_obj, $search_id, $level_one_markup = false, $current_domain_markup = true) {
		
		return $this->update_search_markup_currency ( $price_summary, $currency_obj, $search_id, $level_one_markup, $current_domain_markup );
	}
	/**
	*Update Markup currency for Cancellation Charge
	*/
	function update_cancellation_markup_currency(&$cancel_charge,&$currency_obj,$search_id,$level_one_markup=false,$current_domain_markup=true){
		$search_data = $this->search_data ( $search_id );
		
		$no_of_nights = $this->master_search_data ['no_of_nights'];
		$temp_price = $currency_obj->get_currency ( $cancel_charge, true, $level_one_markup, $current_domain_markup, $no_of_nights );
				
		return round($temp_price['default_value']);
	}

	/**
	 * update markup currency and return summary
	 * $attr needed to calculate number of nights markup when its plus based markup
	 */
	function update_markup_currency(& $price_summary, & $currency_obj, $no_of_nights = 1, $level_one_markup = false, $current_domain_markup = true) {
		
		$tax_service_sum = 0;
		$tax_removal_list = array ();
		$markup_list = array (
				'RoomPrice',
				'PublishedPrice',
				'PublishedPriceRoundedOff',
				'OfferedPrice',
				'OfferedPriceRoundedOff' 
		);

		//debug($price_summary);die();
		$markup_summary = array ();
		foreach ( $price_summary as $__k => $__v ) {
			
			$ref_cur = $currency_obj->force_currency_conversion ( $__v ); // Passing Value By Reference so dont remove it!!!
			$price_summary [$__k] = $ref_cur ['default_value']; // If you dont understand then go and study "Passing value by reference"
			
			if (in_array ( $__k, $markup_list )) {
				$temp_price = $currency_obj->get_currency ( $__v, true, $level_one_markup, $current_domain_markup, $no_of_nights );
				
			} else {
				$temp_price = $currency_obj->force_currency_conversion ( $__v );
			}
			// echo 'herre';
			// debug($temp_price);exit;
			// adding service tax and tax to total

			if (in_array ( $__k, $tax_removal_list )) {
				$markup_summary [$__k] = round($temp_price ['default_value'] + $tax_service_sum);
			} else {

				$markup_summary [$__k] = round($temp_price ['default_value']);
			}
			
		}
		
		$Markup = 0;
		if (isset($markup_summary['PublishedPrice'])) {
            $Markup = $markup_summary['PublishedPrice'] - $price_summary['PublishedPrice'];
        }
        $gst_value = 0;
        //adding gst
	    if($Markup > 0 ){
	        $gst_details = $GLOBALS['CI']->custom_db->single_table_records('gst_master', '*', array('module' => 'hotel'));
	        if($gst_details['status'] == true){
	            if($gst_details['data'][0]['gst'] > 0){
	                $gst_value = ($Markup/100) * $gst_details['data'][0]['gst'];
	            }
	        }
	     }
	  	$markup_summary['_GST'] = $gst_value;
        $markup_summary['PublishedPrice'] =  round($markup_summary['PublishedPrice'] + $markup_summary['_GST']);
        $markup_summary['PublishedPriceRoundedOff'] =  round($markup_summary['PublishedPriceRoundedOff'] + $markup_summary['_GST']);
        $markup_summary['OfferedPrice'] =  round($markup_summary['OfferedPrice'] + $markup_summary['_GST']);
        $markup_summary['OfferedPriceRoundedOff'] =  round($markup_summary['OfferedPriceRoundedOff'] + $markup_summary['_GST']);
        $markup_summary['RoomPrice'] =  round($markup_summary['RoomPrice'] + $markup_summary['_GST']);
        $markup_summary['_Markup'] = $Markup;
       
      
		 //debug($markup_summary);exit('8');
		return $markup_summary;
	}
	
	/**
	 * Tax price is the price for which markup should not be added
	 */
	function tax_service_sum($markup_price_summary, $api_price_summary) {
		// sum of tax and service ;
		//return ($api_price_summary ['ServiceTax'] + $api_price_summary ['Tax'] + ($markup_price_summary ['PublishedPrice'] - $api_price_summary ['PublishedPrice']));
		return (($api_price_summary ['Tax']+$markup_price_summary ['PublishedPrice'] - $api_price_summary ['PublishedPrice']));
	}
	
	/**
	 * calculate and return total price details
	 */
	function total_price($price_summary) {
		return ($price_summary ['OfferedPriceRoundedOff']);
		
	}
	function booking_url($search_id) {
		return base_url () . 'index.php/hotel/booking/' . intval ( $search_id );
	}
	/**
	 * Balu A
	 * 
	 * @param
	 *        	$ChangeRequestStatus
	 */
	private function ChangeRequestStatusDescription($ChangeRequestStatus) {
		$status_description = '';
		switch ($ChangeRequestStatus) {
			case 0 :
				$status_description = 'NotSet';
				break;
			case 1 :
				$status_description = 'Pending';
				break;
			case 2 :
				$status_description = 'InProgress';
				break;
			case 3 :
				$status_description = 'Processed';
				break;
			case 4 :
				$status_description = 'Rejected';
				break;
		}
		return $status_description;
	}
        
        
        function display_image($HotelPicture,$ResultIndex)
        {
                            header("Content-type: image/gif");
                            echo file_get_contents($HotelPicture);
        }
	
	/**
	 * Get Filter Params - fliter_params
	 */
	function format_search_response($hl, $cobj, $sid, $module = 'b2c', $fltr = array()) {
            
		$level_one = true;
		$current_domain = true;
		if ($module == 'b2c') {
			$level_one = false;
			$current_domain = true;
		} else if ($module == 'b2b') {
			$level_one = true;
			$current_domain = true;
		}
		// debug($fltr);
		// exit;
		$h_count = 0;
		$HotelResults = array ();
		if (isset ( $fltr ['hl'] ) == true) {
			foreach ( $fltr ['hl'] as $tk => $tv ) {
				$fltr ['hl'] [urldecode ( $tk )] = strtolower ( urldecode ( $tv ) );
			}
		}
				// Creating closures to filter data
		$check_filters = function ($hd) use ($fltr) {

			$wifi_count = 0;
			if((string)$fltr['wifi']=='true'){
				if(isset($hd['HotelAmenities'])&&valid_array($hd['HotelAmenities'])){		
						 $wi_fi_searchparmas = 'Wi';
						 $wi_search = ucwords('wi-');
						 $wi_fi_small = 'wifi';
						if($this->searchParams($hd['HotelAmenities'],$wi_fi_searchparmas)){
							$wifi_count++;
						}elseif ($this->searchParams($hd['HotelAmenities'],$wi_search)) {
							$wifi_count++;
						}elseif ($this->searchParams($hd['HotelAmenities'],$wi_fi_small)) {
							$wifi_count++;
						}	 
				}
			}
			
			$break_fast_count = 0;
			if((string)$fltr['breakfast']=='true'){
				if(isset($hd['HotelAmenities'])&&valid_array($hd['HotelAmenities'])){		
						 $breakfast_smal = 'breakfast';						 
						 $breakfast = 'Breakfast';
						if($this->searchParams($hd['HotelAmenities'],$breakfast_smal)){
							$break_fast_count++;
						}elseif ($this->searchParams($hd['HotelAmenities'],$breakfast)) {
							$break_fast_count++;
						} 
				}
			}
			
			$parking_count = 0;
			if((string)$fltr['parking']=='true'){
				if(isset($hd['HotelAmenities'])&&valid_array($hd['HotelAmenities'])){		
						 $parking = 'parking';						 
						 $park = 'park';
						if($this->searchParams($hd['HotelAmenities'],$parking)){
							$parking_count++;
						}elseif ($this->searchParams($hd['HotelAmenities'],$park)) {
							$parking_count++;
						} 
				}
			}
			$swim_pool = 0;
			if((string)$fltr['swim_pool']=='true'){
				if(isset($hd['HotelAmenities'])&&valid_array($hd['HotelAmenities'])){		
						 $pool = 'pool';						 
						 $swim = 'Swim';
						if($this->searchParams($hd['HotelAmenities'],$pool)){
							$swim_pool++;
						}elseif ($this->searchParams($hd['HotelAmenities'],$swim)) {
							$swim_pool++;
						} 
				}
			} 
			
			//echo $swim_pool.'<br/>';

			if (($wifi_count >0 || (string)$fltr['wifi']=='false')&&($break_fast_count >0 || (string)$fltr['breakfast']=='false')&&($parking_count >0 || (string)$fltr['parking']=='false')&&($swim_pool >0 || (string)$fltr['swim_pool']=='false')&&(valid_array ( @$fltr ['hl'] ) == false || (valid_array ( @$fltr ['hl'] ) == true && in_array ( strtolower ( $hd ['HotelLocation'] ), $fltr ['hl'] ))) && (valid_array ( @$fltr ['_sf'] ) == false || (valid_array ( @$fltr ['_sf'] ) == true && in_array ( $hd ['StarRating'], $fltr ['_sf'] ))) && (@$fltr ['min_price'] <= ceil ( $hd ['Price'] ['RoomPrice'] ) && (@$fltr ['max_price'] != 0 && @$fltr ['max_price'] >= floor ( $hd ['Price'] ['RoomPrice'] ))) && (( string ) $fltr ['dealf'] == 'false' || empty ( $hd ['HotelPromotion'] ) == false)&& (( string ) $fltr ['free_cancel'] == 'false' || empty ( $hd ['Free_cancel_date'] ) == false)  && (empty ( $fltr ['hn_val'] ) == true || (empty ( $fltr ['hn_val'] ) == false && stripos ( strtolower ( $hd ['HotelName'] ), (urldecode ( $fltr ['hn_val'] )) ) > - 1))) {
				return true;
			} else {
				return false;
			}
		};
		$hc = 0;
		$frc = 0;

		//echo "filter".$frc.'<br>';
		foreach ( $hl ['HotelSearchResult'] ['HotelResults'] as $hr => $hd ) {
			$hc ++;
			// default values
			$hd ['StarRating'] = intval ( $hd ['StarRating'] );
			if (empty ( $hd ['HotelLocation'] ) == true) {
				$hd ['HotelLocation'] = 'Others';
			}
			if (isset ( $hd ['Latitude'] ) == false) {
				$hd ['Latitude'] = 0;
			}
			if (isset ( $hd ['Longitude'] ) == false) {
				$hd ['Longitude'] = 0;
			}
                        
            if(isset($hd ['HotelPicture']) == true)
            { 
                //comment by ela
              //$GLOBALS['CI']->hotel_model->add_hotel_images($sid,$hd ['HotelPicture'],$hd ['ResultIndex'],$hd ['HotelCode']);
            }
			// markup
			//debug( $hd ['Price']);
			$hd ['Price'] = $this->update_search_markup_currency_one_night ( $hd ['Price'], $cobj, $sid, $level_one, $current_domain );
			
			// filter after initializing default data and adding markup
			if (valid_array ( $fltr ) == true && $check_filters ( $hd ) == false) {
				continue;
			}
			$HotelResults [$hr] = $hd;
			$frc ++;
			//echo 'count'.$frc;
		}
		// SORTING STARTS
		if (isset ( $fltr ['sort_item'] ) == true && empty ( $fltr ['sort_item'] ) == false && isset ( $fltr ['sort_type'] ) == true && empty ( $fltr ['sort_type'] ) == false) {
			$sort_item = array ();
			foreach ( $HotelResults as $key => $row ) {
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
				array_multisort ( $sort_item, $sort_type, $HotelResults );
			}
		} // SORTING ENDS


		$hl ['HotelSearchResult'] ['HotelResults'] = $HotelResults;
		$hl ['source_result_count'] = $hc;
		$hl ['filter_result_count'] = $frc;
		
		return $hl;
	}
	/**
	*format Amenities search like mysql like query
	*/
	private function searchParams($array,$needle){
		$search_count = 0;
		if($array){
			foreach($array as $key => $question)
	        {		        	
	            if (strpos($question,"".$needle."" ) !== false) {
	               $search_count++;
	            }elseif (strpos($question,"".$needle."" ) !== false) {
	               $search_count++;
	            }elseif (strpos($question,"".$needle."" ) !== false) {
	               $search_count++;
	            }         
	        }
		}
		return $search_count;
	}
	/**
	 * Break data into pages
	 * 
	 * @param
	 *        	$data
	 * @param
	 *        	$offset
	 * @param
	 *        	$limit
	 */
	function get_page_data($hl, $offset, $limit) {
		$hl ['HotelSearchResult'] ['HotelResults'] = array_slice ( $hl ['HotelSearchResult'] ['HotelResults'], $offset, $limit );
		return $hl;
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
		$filt ['loc'] = array ();
		$filt ['star'] = array ();
		$filters = array ();
		foreach ( $hl ['HotelSearchResult'] ['HotelResults'] as $hr => $hd ) {
			// filters
			$StarRating = intval ( @$hd ['StarRating'] );
			$HotelLocation = empty ( $hd ['HotelLocation'] ) == true ? 'Others' : $hd ['HotelLocation'];
			
			if (isset ( $filt ['star'] [$StarRating] ) == false) {
				$filt ['star'] [$StarRating] ['c'] = 1;
				$filt ['star'] [$StarRating] ['v'] = $StarRating;
			} else {
				$filt ['star'] [$StarRating] ['c'] ++;
			}
			
			if (($filt ['p'] ['max'] != false && $filt ['p'] ['max'] < $hd ['Price'] ['RoomPrice']) || $filt ['p'] ['max'] == false) {
				$filt ['p'] ['max'] = roundoff_number ( $hd ['Price'] ['RoomPrice'] );
			}
			if (($filt ['p'] ['min'] != false && $filt ['p'] ['min'] > $hd ['Price'] ['RoomPrice']) || $filt ['p'] ['min'] == false) {
				$filt ['p'] ['min'] = roundoff_number ( $hd ['Price'] ['RoomPrice'] );
			}
			
			if (($filt ['p'] ['min'] != false && $filt ['p'] ['min'] > $hd ['Price'] ['RoomPrice']) || $filt ['p'] ['min'] == false) {
				$filt ['p'] ['min'] = $hd ['Price'] ['RoomPrice'];
			}
			$hloc = ucfirst ( strtolower ( $HotelLocation ) );
			if (isset ( $filt ['loc'] [$hloc] ) == false) {
				$filt ['loc'] [$hloc] ['c'] = 1;
				$filt ['loc'] [$hloc] ['v'] = $hloc;
			} else {
				$filt ['loc'] [$hloc] ['c'] ++;
			}
			
			$filters ['data'] = $filt;
			$h_count ++;
		}
		ksort ( $filters ['data'] ['loc'] );
		$filters ['hotel_count'] = $h_count;
		return $filters;
	}
	/**
	 * Roomwise Assigned Passenger Count
	 * @param unknown_type $pax_type_arr
	 * @param unknown_type $pax_type
	 */
	function get_assigned_pax_type_count($pax_type_arr, $pax_type)
	{
		$pax_type_count = 0;
		if(valid_array($pax_type_arr) == true){
			foreach ($pax_type_arr as $k => $v){
				if($pax_type == $v){
					$pax_type_count++;
				}
			}
		}
		return $pax_type_count;
	}
	function get_agoda_bookings_list(){
		$header = $this->get_header ();
		$this->credentials ( 'AgodaBookingList' );
		// echo $this->service_url;exit;
		$url = $this->service_url;
		$request1['from_date'] = '2018-03-10';
		$request1['to_date'] = '2018-05-20';
		$request = json_encode($request1);
		$get_hotel_list_response = $GLOBALS ['CI']->api_interface->get_json_response ($url, $request, $header );
		debug($get_hotel_list_response);exit;
		
		return $response;
	}


	public function hotel_result_tmx_format($hotel_response, $search_id, $module){
       /* debug($search_id);
        debug($module);*/
        /*debug($hotel_response);
        die('=================');*/
        $response = array();
        $response['Status'] = FAILURE_STATUS;
        $response['Message'] = '';

        $raw_hotel_lists = $hotel_response['HotelFindResponse']['Hotels']['Hotel']; 

        if(!empty($raw_hotel_lists)){
	        $hotel_codes = array();
	        foreach ($raw_hotel_lists as $v) {
	            $hotel_codes[] = $v['Id'];
	        }

	        $hotel_info = $this->get_hotel_info($hotel_codes);
            //debug($hotel_info);die('====');
	        $HotelResults = array();
	        $count = 1;
	        foreach ($raw_hotel_lists as $r_hl_k => $r_hl_v) {
	        	//debug($r_hl_v);die();
	            $list_data = array();
	            $hotel_code = $r_hl_v['Id'];
	            //$hotel_info = $this->get_hotel_info1($hotel_code);
	            
	           //debug($hotel_info);

	            $list_data['ResultIndex'] = $count++;
	            $list_data['HotelCode'] = $r_hl_v['Id'];
	            $list_data['HotelName'] = $r_hl_v['Name']['@cdata'];
	            $list_data['HotelCategory'] = '';
	            $list_data['StarRating'] = $r_hl_v['Rating'];
	            $list_data['HotelDescription'] = $hotel_info['info'][$hotel_code]['description'];
	            $list_data['HotelPromotion'] = '';
	            $list_data['HotelPolicy'] ='';

	            $api_price = roundoff_number($r_hl_v['Price']);
	           
	            $list_data['Price'] = array(
	            		'TBO_RoomPrice' => $api_price,
	            		'TBO_OfferedPriceRoundedOff' => $api_price,
	            		'TBO_PublishedPrice' => $api_price,
	            		'TBO_PublishedPriceRoundedOff' => $api_price,
	            		'Tax' => 0,
	            		'ExtraGuestCharge' => 0,
	            		'ChildCharge' => 0,
	            		'OtherCharges' => 0,
	            		'Discount' => 0,
	            		'PublishedPrice' => $api_price,
	            		'RoomPrice' => $api_price,
	            		'PublishedPriceRoundedOff' => $api_price,
	            		'OfferedPrice' => $api_price,
	            		'OfferedPriceRoundedOff' => $api_price,
	            		'AgentCommission' => 0,
	            		'AgentMarkUp' => 0,
	            		'ServiceTax' => 0,
	            		'TDS' => 0,
	            		'ServiceCharge' => 0,
	            		'TotalGSTAmount' => 0,
	            		'RoomPriceWoGST' => $api_price,
	            		'GSTPrice' => 0,
	            		'CurrencyCode' => 'INR',
	            	);

	            $list_data['HotelPicture'] = $r_hl_v['ThumbImages']['@cdata'];
	            $list_data['HotelAddress'] = $hotel_info['info'][$hotel_code]['address'];
	            $list_data['HotelContactNo'] ='';
	            $list_data['HotelMap'] ='';
	            $list_data['Latitude'] = $hotel_info['info'][$hotel_code]['latitude'];
	            $list_data['Longitude'] = $hotel_info['info'][$hotel_code]['longitude'];

	            $list_data['HotelLocation'] ='';
	            $list_data['SupplierPrice'] ='';

	            /*$RoomDetails = $r_hl_v['RoomDetails']['RoomDetail'];
	            $format_room_details = array();
	            foreach ($RoomDetails as $k => $v) {
	            	$list = array();
	            }*/

	            $list_data['RoomDetails'] = array();
	            $list_data['OrginalHotelCode'] = $r_hl_v['Id'];
	            $list_data['HotelPromotionContent'] ='';
	            $list_data['PhoneNumber'] ='';
	            $list_data['HotelAmenities'] = $hotel_info['info'][$hotel_code]['amenities'];
	            $list_data['Free_cancel_date'] ='';
	            $list_data['trip_adv_url'] ='';
	            $list_data['trip_rating'] ='';
	            $list_data['ResultToken'] = md5(json_encode($r_hl_v));


	            $HotelResults[] = $list_data; 

	        }

	        $response['Status'] = SUCCESS_STATUS;
	        $Results['HotelSearchResult']['HotelResults'] = $HotelResults;
    	}else{
    		$response['Status'] = FAILURE_STATUS;
        	$response['Message'] = '';
    	}

    	/*debug($Results);
        die('*=======*=*=========*');*/
    	$response['Search'] = $Results;
    	return $response;   
    }

    function get_hotel_info($hotel_code){
        //die($hotel_code);
    	$CI = & get_instance();
  
    	$cols = 'h.origin as origin, h.hotel_code, h.hotel_name, h.hotel_city as hotel_city, h.country_code as hotel_country, h.address as address, h.postal_code as postal_code, h.latitude , h.longitude , h.description as description,  GROUP_CONCAT(DISTINCT hi.image_path SEPARATOR " $ ") as image_list, ha.amenities as amenities';

    	//$h_cond = 'h.hotel_code  IN (' . implode(',', $hotel_code) . ')';

        if (is_array($hotel_code)) {
            $h_cond = 'h.hotel_code IN (' . implode(',', $hotel_code) . ')';
        } else {
            $h_cond = 'h.hotel_code = ' . $CI->db->escape($hotel_code);
        }

    	$query = 'SELECT '.$cols.' FROM rz_hotel_details AS h LEFT JOIN rz_hotel_images AS hi ON h.hotel_code = hi.hotel_code 
        LEFT JOIN rz_hotel_amenities AS ha ON h.hotel_code = ha.hotel_code WHERE '.$h_cond.'GROUP BY h.origin';

    	//debug($query);die();
    	$data = $CI->db->query($query)->result_array();
    	//debug($data);die();
    	$resp = array();
        if (valid_array($data) == true) {
            foreach ($data as $k => $v) {
                $resp['info'][$v['hotel_code']] = array(
                    'origin' => $v['origin'],
                    'hotel_name' => $v['hotel_name'],
                    'description' => $v['description'],
                    'address' => $v['address'],
                    'hotel_city' => $v['hotel_city'],
                    'hotel_country' => $v['hotel_country'],
                    'postal_code' => $v['postal_code'],
                    'latitude' => $v['latitude'],
                    'longitude' => $v['longitude'],
                    'image_list' => explode(' $ ', $v['image_list']),
                    'amenities' => explode(',', $v['amenities']),
                    );
            }
        }

    	//debug($resp);die();
    	return $resp;
    }



    public function get_formatted_hotel_detail($hotel_detail, $search_id, $module) {
        $hotel_detail_responce_arr = $hotel_detail['HotelFindResponse']['Hotels']['Hotel'];
        $hotelcode = $_GET['hotel_id'];
        $hotel_array = array();
        //Currency Preference
        $currency_obj = new Currency(array('module_type' => 'hotel', 'from' => get_application_default_currency(), 'to' => get_application_display_currency_preference()));
        if (isset($hotel_detail_responce_arr) && !empty($hotel_detail_responce_arr) && is_array($hotel_detail_responce_arr)) {
            $search_data = $this->search_data($search_id);
            $api_static_data = $this->get_hotel_static_detail($search_data, $hotel_detail_responce_arr['Id']);
            // if()
            $cache_list = $this->get_hotel_info($hotel_detail_responce_arr['Id']);
            $static_data = @$cache_list['info'][$hotel_detail_responce_arr['Id']];
            /*debug($search_data);
            debug($api_static_data);
            debug($static_data);
            debug($hotel_detail_responce_arr);*/
            // debug($api_static_data);
            // debug($hotel_detail);exit;
            // exit;
            if (!empty($cache_list)) {
                $hotel_array['hotel_name'] = $static_data['hotel_name'];
                $hotel_array['destination'] = $static_data['hotel_city'];
                $hotel_array['zone_code'] = $static_data['postal_code'];
                $hotel_array['latitude'] = $static_data['latitude'];
                $hotel_array['longitude'] = $static_data['longitude'];
                $hotel_array['category_code'] = 'Others';
                $hotel_array['address'] = $static_data['address'];
                $hotel_array['postal'] = $static_data['postal_code'];
                $hotel_array['email'] = $api_static_data['data']['Email'];
                $hotel_array['website'] = $api_static_data['data']['Website'];
                $hotel_array['description'] = $static_data['description'];

                $hotel_array['hotel_static_facilities_arr'] = array();
                // debug($static_data['amenities']);
                foreach ($static_data['amenities'] as $key => $value) {
                    $hotel_array['hotel_static_facilities_arr'][$key]['name'] = $value ;
                    $hotel_array['hotel_static_facilities_arr'][$key]['additional_cost'] = 0 ;
                }
                $hotel_array['imagePath'] = isset($static_data['image_list']) && count($static_data['image_list']) > 0 ? $static_data['image_list'][0] : '';

                $hotel_array['image_arr'] = isset($static_data['image_list']) && count($static_data['image_list']) > 0 ? $static_data['image_list'] : '';
            } elseif(!empty($api_static_data) && $api_static_data['status'] == true){
                $hotel_array['hotel_name'] = $api_static_data['data']['HotelName'];
                $hotel_array['destination'] = $api_static_data['data']['City'];
                $hotel_array['zone_code'] = $api_static_data['data']['HotelPostalCode'];
                $hotel_array['latitude'] = $api_static_data['data']['Latitude'];
                $hotel_array['longitude'] = $api_static_data['data']['Longitude'];
                $hotel_array['category_code'] = 'Others';
                $hotel_array['address'] = $api_static_data['data']['HotelAddress'];
                $hotel_array['postal'] = $api_static_data['data']['HotelPostalCode'];
                $hotel_array['email'] = $api_static_data['data']['Email'];
                $hotel_array['website'] = $api_static_data['data']['Website'];
                $hotel_array['description'] = $api_static_data['data']['Description']['@cdata'];

                $hotel_array['hotel_static_facilities_arr'] = array();
                // debug($api_static_data['data']['amenities']);
                if(!empty($api_static_data['data']['HotelAmenities'])) {
                    $api_static_data['data']['HotelAmenities'] = explode(',', $api_static_data['data']['HotelAmenities']);
                    foreach ($api_static_data['data']['HotelAmenities'] as $key => $value) {
                        $hotel_array['hotel_static_facilities_arr'][$key]['name'] = $value ;
                        $hotel_array['hotel_static_facilities_arr'][$key]['additional_cost'] = 0 ;
                    }
                }
                $hotel_array['imagePath'] = isset($api_static_data['data']['Images']['Image']) && count($api_static_data['data']['Images']['Image']) > 0 ? $api_static_data['data']['Images']['Image'] : $hotel_detail['HotelFindResponse']['Hotels']['Hotel']['ThumbImages']['@cdata'];

                $hotel_array['image_arr'] = isset($api_static_data['data']['Images']['Image']) && count($api_static_data['data']['Images']['Image']) > 0 ? $api_static_data['data']['Images']['Image'] : array($hotel_detail['HotelFindResponse']['Hotels']['Hotel']['ThumbImages']['@cdata']);
                // $hotel_array['image_arr'] = isset($static_data['image_list']) && count($static_data['image_list']) > 0 ? $static_data['image_list'] : '';
            }
            $hotel_detail_responce = $hotel_detail_responce_arr;
            $hotel_array['search_session_id'] = $hotel_detail['HotelFindResponse']['SearchSessionId'];
            $hotel_array['guest_nationality'] = $hotel_detail['HotelFindResponse']['GuestNationality'];
            $hotel_array['hotel_code'] = $hotel_detail_responce['Id'];
            $hotel_array['star_rating'] = $hotel_detail_responce['Rating'];
            
            $hotel_array['destination_code'] = $search_data['data']['rz_city_code'];
            
            // $hotel_array['zone'] = $hotel_detail_responce['zoneName'];
            $price_rate = $hotel_detail_responce['Price'];
            $currency = $hotel_detail['HotelFindResponse']['Currency'];
            if($module == 'b2c') {
                $prc = $currency_obj->get_currency($price_rate,true,false,true, 1);
                $price_rate = ceil($prc['default_value']);
                $currency = $prc['default_currency'];
            } elseif ($module == 'b2b') {
                $prc = $currency_obj->get_currency($price_rate,true,true,true, 1);
                $price_rate = ceil($prc['default_value']);
                $currency = $prc['default_currency'];
            }


            $hotel_array['minRate'] = ceil($price_rate);
            $hotel_array['min_price'] = ceil($price_rate);
            $hotel_array['maxRate'] = '';
            $hotel_array['currency'] = $currency;
            $hotel_array['checkIn'] = $search_data['data']['from_date'];
            $hotel_array['checkOut'] = $search_data['data']['to_date'];
           /* if($api_static_data['status']) {
               


            } else {

                
            }*/
           

            if (isset($hotel_detail_responce['RoomDetails']['RoomDetail']) && count($hotel_detail_responce['RoomDetails']['RoomDetail']) > 0) {
                //extract all room codes
                $room_codes = array();
                if(!empty($hotel_detail_responce['rooms'])) {
                    foreach ($hotel_detail_responce['rooms'] as $key => $room) {
                        $room_codes[] = $room['code'];
                    }
                }

                $rooms_setails = force_multple_data_format($hotel_detail_responce['RoomDetails']['RoomDetail']);
                // debug($rooms_setails);exit;
                foreach ($rooms_setails as $key => $room) {
                    // debug($room);exit;
                    $hotel_array['rooms'][$key]['room_code'] = @$room['code'];
                    $hotel_array['rooms'][$key]['room_name'] = @$room['Type']['@cdata'];
                    // $hotel_array['rooms'][$key]['facilities'] = isset($room_facility_list[$room['code']]) ? $room_facility_list[$room['code']] : false;
                    $hotel_array['rooms'][$key]['img'] = (!empty($room['code']) && isset($room_img_list[$room['code']])) ? $room_img_list[$room['code']] : false;
                    // foreach ($room['rates'] as $rkey => $rate) {
                        //$testDFD[$rate['boardName']][$rate['adults'].'-'.$rate['children']][] = $rate;
                    $adult_count = 0;
                    foreach ($search_data['data']['adult_config'] as $value) {
                        $adult_count += $value;
                    }
                    $child_count = 0;
                    foreach ($search_data['data']['child_config'] as $value) {
                        $child_count += $value;
                    }

                        $hotel_array['rooms'][$key]['room'] = $search_data['data']['room_count'];
                        $hotel_array['rooms'][$key]['adults'] = $adult_count;
                        $hotel_array['rooms'][$key]['children'] = $child_count;

                        if($room['TotalRooms'] > 1) {
                            $pric = explode('|', $room['TotalRate']);
                            $total = 0;
                            foreach ($pric as $p) {
                                $total += $p;
                            }

                            $price = $total;
                            // Add Markup
                            if($module == 'b2c') {
                                $prc = $currency_obj->get_currency($total,true,false,true, 1);
                                $price = ceil($prc['default_value']);
                            } elseif ($module == 'b2b') {
                                $prc = $currency_obj->get_currency($total,true,true,true, 1);
                                $price = ceil($prc['default_value']);
                            }

                            $hotel_array['rooms'][$key]['net'] = ceil($price);
                            // debug($hotel_array);exit;
                        } else {
                            $price = $room['TotalRate'];
                            // Add Markup
                            if($module == 'b2c') {
                                $prc = $currency_obj->get_currency($price,true,false,true, 1);
                                $price = ceil($prc['default_value']);
                            } elseif ($module == 'b2b') {
                                $prc = $currency_obj->get_currency($price,true,true,true, 1);
                                $price = ceil($prc['default_value']);
                            }
                            $hotel_array['rooms'][$key]['per_net'] = ceil($price);
                            $hotel_array['rooms'][$key]['net'] = ceil($price);
                        }
                        $hotel_array['total_price'] = ceil($price);
                        $hotel_array['rooms'][$key]['rates']['net'] = $room['TotalRate'];
                        // $hotel_array['rooms'][$key]['rates']['net'] = $price;
                        $hotel_array['rooms'][$key]['rates']['rateKey'] =  $key;
                        $hotel_array['rooms'][$key]['rates']['room_description'] =  $room['RoomDescription']['@cdata'];
                        $hotel_array['rooms'][$key]['rates']['name'] = $room['Type']['@cdata'];
                        $hotel_array['rooms'][$key]['rates']['booking_key'] = $room['BookingKey'];
                        $hotel_array['rooms'][$key]['rates']['allotment'] = isset($room['TotalRooms']) && !empty($room['TotalRooms']) ? $room['TotalRooms'] : '';
                        // $hotel_array['rooms'][$key]['rates']['rateCommentsId '] = isset($room['TermsAndConditions']['@cdata']) && !empty($room['TermsAndConditions']['@cdata']) ? $room['TermsAndConditions']['@cdata'] : '';
                        $hotel_array['rooms'][$key]['rates']['rateComments'] = isset($room['TermsAndConditions']['@cdata']) && !empty($room['TermsAndConditions']['@cdata']) ? $room['TermsAndConditions']['@cdata'] : '';
                        // $hotel_array['rooms'][$key]['rates']['paymentType'] = $room['paymentType'];
                        $hotel_array['rooms'][$key]['rates']['boardCode'] = (isset($room['BoardBasis']['@cdata']) == true && !empty($room['BoardBasis']['@cdata']) == true)? $room['BoardBasis']['@cdata']: '';
                        $hotel_array['rooms'][$key]['rates']['boardName'] = (isset($room['BoardBasis']['@cdata']) == true && !empty($room['BoardBasis']['@cdata']) == true)? $room['BoardBasis']['@cdata']: '';

                        /* cancellation policies */
                        // $hotel_array['rooms'][$key]['rates']['cancellationPolicies'][0] = $hotel_detail_responce['cancellation_policy'];
                        $cancellation_policy =  $this->get_room_cancellation_policy($search_data, $hotel_detail_responce['Id'], $room, $module);

                        $hotel_array['rooms'][$key]['rates']['cancellationPolicies'] = $cancellation_policy['data'];
                        
                        if (isset($room['cancellationPolicies']) && !empty($room['cancellationPolicies'])) {
                            foreach ($room['cancellationPolicies'] as $cKey => $policy) {
                                $hotel_array['rooms'][$key]['rates']['cancellationPolicies'][$cKey]['amount'] = $policy['amount'];
                                $hotel_array['rooms'][$key]['rates']['cancellationPolicies'][$cKey]['from'] = $policy['from'];
                            }
                        }

                        $hotel_array['rooms'][$key]['rates']['rooms'] = $room['TotalRooms'];
                        $hotel_array['rooms'][$key]['rates']['adults_r'] = $room['Adults'];
                        $hotel_array['rooms'][$key]['rates']['children_r'] = $room['Children'];
                        $hotel_array['rooms'][$key]['rates']['adults'] = $adult_count;
                        $hotel_array['rooms'][$key]['rates']['children'] = $child_count;
                        $hotel_array['rooms'][$key]['rates']['children_ages'] = $room['ChildrenAges'];
                        $hotel_array['rooms'][$key]['rates']['rateClass'] = '';

                }
            }

            /* credit cart payment option details */
            if (isset($hotel_detail_responce['creditCards']) && COUNT($hotel_detail_responce['creditCards']) > 0) {
                foreach ($hotel_detail_responce['creditCards'] as $crKey => $credit_card) {
                    $hotel_array['credit_card'][$crKey]['code'] = $credit_card['code'];
                    $hotel_array['credit_card'][$crKey]['name'] = $credit_card['name'];
                    $hotel_array['credit_card'][$crKey]['paymentType'] = $credit_card['paymentType'];
                }
            }

            if (isset($terminal) && !empty($terminal)) {
                $hotel_array['hotel_terminalCode'] = $terminal->hotel_terminalCode;
                $hotel_array['hotel_distance'] = $terminal->hotel_distance;
            }
        }
        // debug($hotel_array);exit();
        return $hotel_array;
    }

    ///////new addition//////

    function pre_booking_check($search_params, $hotel_details,$safe_search_data)
    {
        $response ['data'] = array();
        $response ['status'] = false;
        // debug($search_params);
        // debug($hotel_details);
       $pre_booking_request = $this->pre_booking_check_request($search_params['data'], $hotel_details,$safe_search_data);
       //debug($pre_booking_request);//die('2824');
       if ($pre_booking_request ['status']) {
            $GLOBALS ['CI']->custom_db->generate_static_response ($pre_booking_request ['data'] ['request'], 'REZLIVE pre-booking check request' );

             $pre_booking_response = $GLOBALS ['CI']->api_interface->xml_post_request($pre_booking_request ['data'] ['service_url'], ('XML='.urlencode($pre_booking_request ['data'] ['request'])), $this->xml_header());
            //debug($pre_booking_response);die('2829');
            //$GLOBALS ['CI']->custom_db->generate_static_response ($pre_booking_response, 'REZLIVE pre-booking check response' );
            $pre_booking_response = Converter::createArray($pre_booking_response);

            //debug($pre_booking_response);exit;

            if (isset($pre_booking_response['PreBookingResponse']) 
                && !empty($pre_booking_response['PreBookingResponse']) 
                && isset($pre_booking_response['PreBookingResponse']['PreBookingDetails'])
                && !isset($pre_booking_response['PreBookingResponse']['error'])) {

                $pre_booking_data = array();
                if(($pre_booking_response['PreBookingResponse']['PreBookingDetails']['Status'] == true) &&
                    ( (($pre_booking_response['PreBookingResponse']['PreBookingDetails']['Difference']) > 0) || (($pre_booking_response['PreBookingResponse']['PreBookingDetails']['Difference']) < 0))
                    ) {
                    /*($pre_booking_response['PreBookingResponse']['PreBookingDetails']['Difference']) > 0*/
                    $pre_booking_data['price_changed'] = TRUE;
                    $pre_booking_data['pre_booking_raw_data'] = $pre_booking_response['PreBookingResponse']['PreBookingDetails'];
                    $pre_booking_data['pre_booking_request_data'] = $pre_booking_response['PreBookingResponse']['PreBookingRequest']['PreBooking'];
                } else {
                    $pre_booking_data['price_changed'] = FALSE;
                    $pre_booking_data['pre_booking_raw_data'] = $pre_booking_response['PreBookingResponse']['PreBookingDetails'];
                    $pre_booking_data['pre_booking_request_data'] = $pre_booking_response['PreBookingResponse']['PreBookingRequest']['PreBooking'];
                }


                $response ['data'] = $pre_booking_data;
                $response ['status'] = true;
            } else {
                $response ['data'] = $pre_booking_response;
            }

       }
       //debug($response);die('789789');
       return $response;

    }

    private function pre_booking_check_request($search_params, $hotel_details,$safe_search_data)
    {
        //debug($hotel_details);die('[]');
        $response ['status'] = true;
        $response ['data'] = array();
        $request = '';

        $this->CI->load->driver('cache');
        $search_hash = md5('Hotel_details'.REZLIVE_HOTEL);
        $cache_contents = $this->CI->cache->file->get($search_hash);

        $search_hotel_details = $cache_contents['HotelInfoResult']['HotelDetails']['raw_room_data'];

        $hotel_info = '';
        foreach($search_hotel_details as $hotel_key => $hotel_value){
            foreach ($hotel_details['token'] as $room_key => $room_value) {
                if($hotel_value['BookingKey'] == $room_value){
                    $hotel_info = $hotel_value;
                }
            }
        } 
        //debug($hotel_info);die('[]');
        //debug($cache_contents);
        //debug($hotel_info);
        //debug($cache_contents['HotelInfoResult']['HotelDetails']['raw_room_data']);
        //debug($hotel_details);
        $hotel_code = $cache_contents['HotelInfoResult']['HotelDetails']['HotelCode'];
        $currency = $cache_contents['HotelInfoResult']['HotelDetails']['first_room_details']['Price']['CurrencyCode'];

        //die('88');

        if( isset($search_params) && !empty($search_params) ) {
            
            $city_code[0]['city_code'] = $search_params['rz_city_code'];
            $city_code[0]['country_code'] = $search_params['rz_country_code'];
            $request .= '<?xml version="1.0"?><PreBookingRequest><Authentication><AgentCode>'.$this->agent_id.'</AgentCode><UserName>'.$this->username.'</UserName><Password>'.$this->password.'</Password></Authentication>';
            $request .= '<PreBooking>
            <SearchSessionId>'.$hotel_details['token_data']['search_session_id'].'</SearchSessionId>
            <ArrivalDate>'. (date('d/m/Y', strtotime($search_params['from_date']))) .'</ArrivalDate>
            <DepartureDate>'. (date('d/m/Y', strtotime($search_params['to_date']))) .'</DepartureDate>
            <GuestNationality>'.$city_code[0]['country_code'].'</GuestNationality>
            <CountryCode>'.$city_code[0]['country_code'].'</CountryCode>
            <City>'.$city_code[0]['city_code'].'</City>
            <HotelId>'.$hotel_code.'</HotelId>
            <Currency>'.$currency.'</Currency>
            <RoomDetails>
            <RoomDetail>';
            // if(!empty($hotel_details['token_data']['rooms'][$hotel_details['rateKey'][0]]['rates']['room_description'])) {

                // $request .= '<Type>'.$hotel_details['token_data']['rooms'][$hotel_details['rateKey'][0]]['rates']['room_description'].'</Type>';
            // } else {
                $request .= '<Type>'.$hotel_info['Type']['@cdata'].'</Type>';
            // }
            $request .= '<BookingKey>'.$hotel_info['BookingKey'].'</BookingKey>
            <Adults>'.$hotel_info['Adults'].'</Adults>
            <Children>'.$hotel_info['Children'].'</Children>';

            if (count($hotel_info['Children']) > 0)   
            {
                $request .= '<ChildrenAges>'.$hotel_info['ChildrenAges'].'</ChildrenAges>';
            }
            $request .= '<TotalRooms>'.$hotel_info['TotalRooms'].'</TotalRooms><TotalRate>'.$hotel_info['TotalRate'].'</TotalRate></RoomDetail></RoomDetails></PreBooking></PreBookingRequest>';
        }

        $response ['data'] ['service_url'] = $this->service_url . '/prebook';
        $response ['data'] ['request'] = $request;
        $response ['status'] = SUCCESS_STATUS;
        return $response;
    }


    ///////////////////
    public function format_hotel_details($hotel_details_response){
        /*debug($hotel_details_response);
        die('0000000000000');*/

        $response = array();
        $response['Status'] = FAILURE_STATUS;

        if(!empty($hotel_details_response)){

            $response_details = $hotel_details_response['HotelFindResponse'];

            //debug($response_details);die();

            $hotel_code = $response_details['Hotels']['Hotel']['Id'];

            $api_static_data = $this->get_hotel_static_detail_new($hotel_code);
            //debug($api_static_data);echo '===================';
            //die('api_static_data');

            $hotel_info = $this->get_hotel_info($hotel_code);  
            //debug($hotel_info);die();
            
            $HotelDetails = array();

            if(!empty($api_static_data) && $api_static_data['data']['HotelId'] != ''){
                $HotelDetails['HotelCode'] = $api_static_data['data']['HotelId'];
                $HotelDetails['HotelName'] = $api_static_data['data']['HotelName'];
                $HotelDetails['StarRating'] = $api_static_data['data']['Rating'];
                $HotelDetails['HotelURL'] = $api_static_data['data']['Website']['@cdata'];
                $HotelDetails['Description'] = $api_static_data['data']['Description']['@cdata'];
                $HotelDetails['Attractions'] = array();


                $HotelDetails['HotelFacilities'] = '';
                if(isset($api_static_data['data']['HotelAmenities']['@cdata'])){
                    $HotelDetails['HotelFacilities'] = explode(',', $api_static_data['data']['HotelAmenities']['@cdata']);
                }else{
                    $HotelDetails['HotelFacilities'] = explode(',', $api_static_data['data']['HotelAmenities']);
                }


                $HotelDetails['HotelPolicy'] = '';
                $HotelDetails['SpecialInstructions'] = '';
                $HotelDetails['HotelPicture'] = $api_static_data['data']['MainImage'];
                $HotelDetails['Images'] = array();    
                if(isset($api_static_data['data']['Images'])){
                    $HotelDetails['Images'] = $api_static_data['data']['Images']['Image'];
                }else{
                    $HotelDetails['Images'] = array(
                        '0' => $api_static_data['data']['MainImage'],
                    );
                }    

                if(isset($api_static_data['data']['HotelAddress']['@cdata'])){
                    $HotelDetails['Address'] = $api_static_data['data']['HotelAddress']['@cdata'];
                }else{
                    $HotelDetails['Address'] = $api_static_data['data']['HotelAddress'];
                }    
                
                $HotelDetails['CountryName'] = $api_static_data['data']['Country'];
                $HotelDetails['PinCode'] = $api_static_data['data']['HotelPostalCode']; 
                $HotelDetails['HotelContactNo'] = $api_static_data['data']['Phone'];
                $HotelDetails['FaxNumber'] = $api_static_data['data']['Fax'];
                $HotelDetails['Email'] = $api_static_data['data']['Email'];
                $HotelDetails['Latitude'] = $api_static_data['data']['Latitude'];
                $HotelDetails['Longitude'] = $api_static_data['data']['Longitude'];
                $HotelDetails['RoomData'] = '';

                $HotelDetails['RoomFacilities'] = ''; 
                if(isset($api_static_data['data']['RoomAmenities']['@cdata'])){
                    $HotelDetails['RoomFacilities'] = explode(',', $api_static_data['data']['RoomAmenities']['@cdata']);
                }else{
                    $HotelDetails['RoomFacilities'] = explode(',', $api_static_data['data']['RoomAmenities']);
                }  

                $HotelDetails['Services'] = ''; 
                $HotelDetails['Amenities'] = '';
                if(isset($api_static_data['data']['HotelAmenities']['@cdata'])){
                    $HotelDetails['Amenities'] = explode(',', $api_static_data['data']['HotelAmenities']['@cdata']);
                }else{
                    $HotelDetails['Amenities'] = explode(',', $api_static_data['data']['HotelAmenities']);
                }

            }else{

                $HotelDetails['HotelCode'] = $response_details['Hotels']['Hotel']['Id'];
                $HotelDetails['HotelName'] = $response_details['Hotels']['Hotel']['Name']['@cdata'];
                $HotelDetails['StarRating'] = $response_details['Hotels']['Hotel']['Rating'];
                $HotelDetails['HotelURL'] = '';
                $HotelDetails['Description'] = $hotel_info['info'][$hotel_code]['description'];
                $HotelDetails['Attractions'] = array();

                $HotelDetails['HotelFacilities'] = $hotel_info['info'][$hotel_code]['amenities'];
                $HotelDetails['HotelPolicy'] = '';
                $HotelDetails['SpecialInstructions'] = '';
                $HotelDetails['HotelPicture'] = $hotel_info['info'][$hotel_code]['image_list'];;
                $HotelDetails['Images'] = array(
                        '0' => $response_details['Hotels']['Hotel']['ThumbImages']['@cdata'],
                    );
                $HotelDetails['Address'] = $hotel_info['info'][$hotel_code]['address'];

                $HotelDetails['CountryName'] = $hotel_info['info'][$hotel_code]['hotel_country'];
                $HotelDetails['PinCode'] = $hotel_info['info'][$hotel_code]['postal_code']; 
                $HotelDetails['HotelContactNo'] = '';
                $HotelDetails['FaxNumber'] = '';
                $HotelDetails['Email'] = '';
                $HotelDetails['Latitude'] = $hotel_info['info'][$hotel_code]['latitude'];
                $HotelDetails['Longitude'] = $hotel_info['info'][$hotel_code]['longitude'];
                $HotelDetails['RoomData'] = '';
                $HotelDetails['RoomFacilities'] = ''; 
                $HotelDetails['Services'] = ''; 
                $HotelDetails['Amenities'] = $hotel_info['info'][$hotel_code]['amenities'];
            }

            

            ///
            $checkin_date = $response_details['ArrivalDate'];
            $checkin_date = DateTime::createFromFormat("d/m/Y" , $checkin_date);
            $HotelDetails['checkin'] = $checkin_date->format('Y-m-d');
            $checkout_date = $response_details['DepartureDate'];
            $checkout_date = DateTime::createFromFormat("d/m/Y" , $checkout_date);
            $HotelDetails['checkout'] = $checkout_date->format('Y-m-d');
            
            $RoomDetails = $response_details['Hotels']['Hotel']['RoomDetails']['RoomDetail'];
            
            if(isset($RoomDetails[0])){
                $api_price = roundoff_number($RoomDetails[0]['TotalRate']);
                $price = array(
                    'TBO_RoomPrice' => $api_price,
                    'TBO_OfferedPriceRoundedOff' => $api_price,
                    'TBO_PublishedPrice' => $api_price,
                    'TBO_PublishedPriceRoundedOff' => $api_price,
                    'Tax' => 0,
                    'ExtraGuestCharge' => 0,
                    'ChildCharge' => 0,
                    'OtherCharges' => 0,
                    'Discount' => 0,
                    'PublishedPrice' => $api_price,
                    'RoomPrice' => $api_price,
                    'PublishedPriceRoundedOff' => $api_price,
                    'OfferedPrice' => $api_price,
                    'OfferedPriceRoundedOff' => $api_price,
                    'AgentCommission' => 0,
                    'AgentMarkUp' => 0,
                    'ServiceTax' => 0,
                    'TDS' => 0,
                    'ServiceCharge' => 0,
                    'TotalGSTAmount' => 0,
                    'RoomPriceWoGST' => $api_price,
                    'GSTPrice' => 0,
                    'CurrencyCode' => 'INR',
                );

                $room_name = $RoomDetails[0]['Type']['@cdata'];
                $Room_data = array(
                        'RoomUniqueId' => $RoomDetails[0]['BookingKey'],
                        'rate_key' => '1',
                        'group_code' => '',
                    );
            }else{
                $api_price = roundoff_number($RoomDetails['TotalRate']);
                $price = array(
                    'TBO_RoomPrice' => $api_price,
                    'TBO_OfferedPriceRoundedOff' => $api_price,
                    'TBO_PublishedPrice' => $api_price,
                    'TBO_PublishedPriceRoundedOff' => $api_price,
                    'Tax' => 0,
                    'ExtraGuestCharge' => 0,
                    'ChildCharge' => 0,
                    'OtherCharges' => 0,
                    'Discount' => 0,
                    'PublishedPrice' => $api_price,
                    'RoomPrice' => $api_price,
                    'PublishedPriceRoundedOff' => $api_price,
                    'OfferedPrice' => $api_price,
                    'OfferedPriceRoundedOff' => $api_price,
                    'AgentCommission' => 0,
                    'AgentMarkUp' => 0,
                    'ServiceTax' => 0,
                    'TDS' => 0,
                    'ServiceCharge' => 0,
                    'TotalGSTAmount' => 0,
                    'RoomPriceWoGST' => $api_price,
                    'GSTPrice' => 0,
                    'CurrencyCode' => 'INR',
                );

                $room_name = $RoomDetails['Type']['@cdata'];
                $Room_data = array(
                        'RoomUniqueId' => $RoomDetails['BookingKey'],
                        'rate_key' => '1',
                        'group_code' => '',
                    );
            }

            $HotelDetails['first_room_details']['Price'] = $price;
            $HotelDetails['first_room_details']['room_name'] = $room_name;
            $HotelDetails['first_room_details']['Room_data'] = $Room_data;

            $HotelDetails['first_rm_cancel_date'] = '';
            $HotelDetails['trip_adv_url'] = '';
            $HotelDetails['trip_rating'] = '';

            $HotelDetails['raw_room_data'] = $RoomDetails;


            $response['Status'] = SUCCESS_STATUS;
            $response['Message'] = 'success';
            $response['HotelDetails']['HotelInfoResult']['HotelDetails'] = $HotelDetails;

        }else{
            $response['Message'] = 'Formatting Failed';
             $response['Status'] = FAILURE_STATUS;
        }

        /*debug($response);
        die('format--');*/
        return $response;
    }


    public function format_room_block_details($room_block_details){
        /*debug($room_block_details);
        die('===========');*/
            /*error_reporting(E_ALL);
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);*/
        $response = array();
        $response['status'] = FAILURE_STATUS;
        $price = roundoff_number($room_block_details['data']['pre_booking_request_data']['RoomDetails']['RoomDetail']['TotalRate']);
        if(!empty($room_block_details) && $room_block_details['data']['pre_booking_raw_data']['Status'] == true){

                $HotelRoomsDetails = array();

                $HotelRoomsDetails['ChildCount'] = $room_block_details['data']['pre_booking_request_data']['RoomDetails']['RoomDetail']['Children'];
                $HotelRoomsDetails['RequireAllPaxDetails'] = '';
                $HotelRoomsDetails['RoomId'] = '';
                $HotelRoomsDetails['RoomStatus'] = '';
                $HotelRoomsDetails['RoomIndex'] = '';
                $HotelRoomsDetails['RoomTypeCode'] = '';
                $HotelRoomsDetails['RoomDescription'] = '';
                $HotelRoomsDetails['RoomTypeName'] = $room_block_details['data']['pre_booking_request_data']['RoomDetails']['RoomDetail']['Type'];
                $HotelRoomsDetails['RatePlanCode'] = '';
                $HotelRoomsDetails['RatePlan'] = '';
                $HotelRoomsDetails['InfoSource'] = 'FixedCombination';

                $HotelRoomsDetails['SequenceNo'] = '';
                $HotelRoomsDetails['IsPerStay'] = '';
                $HotelRoomsDetails['SupplierPrice'] = '';
                $HotelRoomsDetails['Price'] = array(
                        'TBO_RoomPrice' => $price,
                        'TBO_OfferedPriceRoundedOff' => $price,
                        'TBO_PublishedPrice' => $price,
                        'TBO_PublishedPriceRoundedOff' => $price,
                        'Tax' => '',
                        'ExtraGuestCharge' => '',
                        'ChildCharge' => '',
                        'OtherCharges' => '',
                        'Discount' => '',
                        'PublishedPrice' => $price,
                        'RoomPrice' => $price,
                        'PublishedPriceRoundedOff' => $price,
                        'OfferedPrice' => $price,
                        'OfferedPriceRoundedOff' => $price,
                        'AgentCommission' => '',
                        'AgentMarkUp' => '',
                        'ServiceTax' => '',

                        'TDS' => '',
                        'ServiceCharge' => '',
                        'TotalGSTAmount' => '',
                        'RoomPriceWoGST' => $price,
                        'GSTPrice' => '',
                        'CurrencyCode' => 'INR',
                    );

                $HotelRoomsDetails['RoomPromotion'] = '';
                $HotelRoomsDetails['Amenities'] = array();
                $HotelRoomsDetails['Amenity'] = array();
                $HotelRoomsDetails['SmokingPreference'] = '';
                $HotelRoomsDetails['BedTypes'] = array();
                $HotelRoomsDetails['HotelSupplements'] = array();
                $HotelRoomsDetails['LastCancellationDate'] = '';
                $HotelRoomsDetails['SupplierSpecificData'] = '';

                $canc_info = $room_block_details['data']['pre_booking_request_data']['CancellationInformations']['CancellationInformation'];
                $CancellationPolicies = array();
                foreach ($canc_info as $key => $value) {
                    $_arr = array();
                    $_arr['Charge'] = $value['ChargeAmount'];
                    $_arr['ChargeType'] = $value['ChargeType'];
                    $_arr['FromDate'] = date('Y-m-d',strtotime($value['StartDate']));
                    $_arr['ToDate'] = date('Y-m-d',strtotime($value['EndDate']));
                    $_arr['Currency'] = $value['Currency'];

                    $CancellationPolicies[] = $_arr;
                }
                $HotelRoomsDetails['CancellationPolicies'] = $CancellationPolicies;

                $HotelRoomsDetails['CancellationPolicy'] = $room_block_details['data']['pre_booking_request_data']['CancellationInformations']['Info'];

                $HotelRoomsDetails['Inclusion'] = array();
                $HotelRoomsDetails['TBO_RoomIndex'] = '';
                $HotelRoomsDetails['TBO_RoomTypeName'] = $room_block_details['data']['pre_booking_request_data']['RoomDetails']['RoomDetail']['Type'];
                $HotelRoomsDetails['HotelCode'] = $room_block_details['data']['pre_booking_request_data']['HotelId'];
                $HotelRoomsDetails['SEARCH_ID'] = '';
                $HotelRoomsDetails['API_raw_price'] = '1';
                $HotelRoomsDetails['AccessKey'] = '';
                $HotelRoomsDetails['Boarding_details'] = array();
                $HotelRoomsDetails['extra_data'] = json_encode($room_block_details);

            $response['data']['response']['BlockRoomResult']['BlockRoomId'] = $room_block_details['data']['pre_booking_request_data']['RoomDetails']['RoomDetail']['BookingKey'];
            $response['data']['response']['BlockRoomResult']['IsPriceChanged'] = '';
            $response['data']['response']['BlockRoomResult']['IsCancellationPolicyChanged'] = '';   
            $response['data']['response']['BlockRoomResult']['HotelRoomsDetails'][] = $HotelRoomsDetails;
            $response['status'] = SUCCESS_STATUS;
        }else{
            $response['status'] = FAILURE_STATUS;
        }

        /*debug($response);
        die('==');*/
        return $response;
    }


    ///booking
    function booking_xml_request($booking_params, $booking_id) {
        /*error_reporting(E_ALL);
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);*/
        //debug($booking_params);die();

        $response ['status'] = true;
        $response ['data'] = array();
        $request = '';

        $search_id = $booking_params ['token'] ['search_id'];
        $search_params = $this->search_data($search_id);

        $extra_data = json_decode($booking_params['token']['extra_data'],1);
        //debug($search_params);die();
        /*debug($extra_data);
        debug($search_params);
        debug($booking_params);die('55555');*/
        if( isset($search_params) && !empty($search_params['data']) ) {
            
            $city_code[0]['city_code'] = $search_params['data']['rz_city_code'];
            $city_code[0]['country_code'] = $search_params['data']['rz_country_code'];
            $request .= '<?xml version="1.0"?><BookingRequest><Authentication><AgentCode>'.$this->agent_id.'</AgentCode><UserName>'.$this->username.'</UserName><Password>'.$this->password.'</Password></Authentication>';
            $request .= '<Booking>
            <SearchSessionId>'.$booking_params['token']['search_session_id'].'</SearchSessionId>
            <AgentRefNo>'.$booking_id.'</AgentRefNo>
            <ArrivalDate>'. $search_params['data']['from_date'] .'</ArrivalDate>
            <DepartureDate>'. $search_params['data']['to_date'] .'</DepartureDate>
            <GuestNationality>'.$booking_params['token']['GuestNationality'].'</GuestNationality>
            <CountryCode>'.$city_code[0]['country_code'].'</CountryCode>
            <City>'.$city_code[0]['city_code'].'</City>
            <HotelId>'.$booking_params['token']['HotelCode'].'</HotelId>
            <Name>'.$booking_params['token']['HotelName'].'</Name>
            <Address>'.$booking_params['token']['HotelAddress'].'</Address>
            <Currency>'.$booking_params['token']['default_currency'].'</Currency>
            <RoomDetails>
            <RoomDetail>';
            $room_type = '';
            $adult_count = '';
            $child_count = '';
            // $child_ages = '';
            $cldg = 0;
            $cldg2 = 0;            
            $guest_list = '';
            $gst_count = 0;
            $r_tot = $search_params['data']['room_count'];
            if($search_params['data']['room_count'] >= 1 ) {
                for ($rt=0; $rt < $search_params['data']['room_count']; $rt++) { 
                    $total_count = 0;
                    // $room_type .=  $booking_params['token']['token_data']['rooms'][$booking_params['token']['rateKey'][0]]['rates']['room_description'];
                    $adult_count .= $search_params['data']['adult_config'][$rt];
                    $child_count .= $search_params['data']['child_config'][$rt];
                    $total_count = $search_params['data']['adult_config'][$rt] + $search_params['data']['child_config'][$rt];
                    /*if($search_params['data']['child_config'][$rt] > 0){

                        for ($cag=0; $cag < $search_params['data']['child_config'][$rt]; $cag++) { 
                            $child_ages .= $search_params['data']['child_age'][$cldg];
                            if($booking_params['token']['token_data']['rooms'][$booking_params['token']['rateKey'][0]]['children'] > 1){
                                $child_ages .= '|';
                            }
                            $cldg++;
                        }

                    }*/   
                    if ($r_tot > 1) {
                        // $room_type .= '|';
                        $adult_count .= '|';
                        $child_count .= '|';
                        $r_tot--;
                    }

                    // get guest list
                    $guest_list .= '<Guests>';
                    for ($agst = 0 ; $agst < $total_count; $agst ++){
                        $guest_list .= '<Guest>';
                        $gust_type = $booking_params['passenger_type'][$gst_count];
                        $gust_title =  get_enum_list('title', $booking_params['name_title'][$gst_count]);
                        $gust_fname = $booking_params['first_name'][$gst_count];
                        $gust_lname = $booking_params['last_name'][$gst_count];
                        if($gust_type == 'CH') {
                            $guest_list .=  '<Salutation>Child</Salutation>
                                            <FirstName>'.$gust_fname.'</FirstName>
                                            <LastName>'.$gust_lname.'</LastName>
                                            <IsChild>1</IsChild>
                                            <Age>'.$search_params['data']['child_age'][$cldg2].'</Age>';
                                                $cldg2++;
                        } else {
                            $guest_list .=  '<Salutation>'.$gust_title.'</Salutation>
                                            <FirstName>'.$gust_fname.'</FirstName>
                                            <LastName>'.$gust_lname.'</LastName>';
                        }

                        $guest_list .= '</Guest>';
                        $gst_count++;
                        
                    }
                    $guest_list .= '</Guests>';
                }
            }

            /* if(!empty($booking_params['token']['token_data']['rooms'][$booking_params['token']['rateKey'][0]]['rates']['room_description'])) {

                $room_type .= $booking_params['token']['token_data']['rooms'][$booking_params['token']['rateKey'][0]]['rates']['room_description'];
            } else {*/
                $room_type .= $extra_data['data']['pre_booking_request_data']['RoomDetails']['RoomDetail']['Type'];
            // }
            // $room_type .=  $booking_params['token']['token_data']['rooms'][$booking_params['token']['rateKey'][0]]['rates']['room_description'];

            $request .= '<Type>'.$extra_data['data']['pre_booking_request_data']['RoomDetails']['RoomDetail']['Type'].'</Type>
           <BookingKey>'.$booking_params['token']['BlockRoomId'].'</BookingKey>
            <Adults>'.$adult_count.'</Adults>
            <Children>'.$child_count.'</Children>';
            // <BookingKey>'.$booking_params['token']['token_data']['rooms'][$booking_params['token']['rateKey'][0]]['rates']['booking_key'].'</BookingKey>
            // <BookingKey>'.$booking_params['token']['token_updated_data']['data']['pre_booking_request_data']['RoomDetails']['RoomDetail']['BookingKey'].'</BookingKey>
            if ($extra_data['data']['pre_booking_request_data']['RoomDetails']['RoomDetail']['Children'] > 0)   
            {
                // $request .= '<ChildrenAges>'.$child_ages.'</ChildrenAges>';
                $request .= '<ChildrenAges>'.$extra_data['data']['pre_booking_request_data']['RoomDetails']['RoomDetail']['ChildrenAges'].'</ChildrenAges>';
            }
            $request .= '<TotalRooms>'.$extra_data['data']['pre_booking_request_data']['RoomDetails']['RoomDetail']['TotalRooms'].'</TotalRooms>
            <TotalRate>'.$extra_data['data']['pre_booking_request_data']['RoomDetails']['RoomDetail']['TotalRate'].'</TotalRate>';

            $request .= $guest_list .'
            </RoomDetail>
            </RoomDetails>
            </Booking>
            </BookingRequest>';
        }
        $response ['data'] ['service_url'] = $this->service_url . '/bookhotel';
        $response ['data'] ['request'] = $request;
        $response ['status'] = SUCCESS_STATUS;

        ///debug($response);die('789');
        return $response;
    }

    function process_booking($book_id, $booking_params) {

        $response ['status'] = FAILURE_STATUS;
        $response ['data'] = array();
        /*debug($book_id);
        debug($booking_params);
        exit('9');*/
        //$book_request = $this->get_book_request ( $booking_params, $book_id );
        $book_request = $this->booking_xml_request($booking_params, $book_id);
        //debug($book_request);die('===========');
        $block_data_array = $book_request ['data'] ['request'];
        if ($book_request['status'] == true) {

            //$GLOBALS ['CI']->custom_db->generate_static_response ($book_request ['data'] ['request'], 'REZLIVE hotel booking request' );
            // get the response for hotel bookings
             $book_response = $GLOBALS ['CI']->api_interface->xml_post_request($book_request ['data'] ['service_url'], ('XML='.urlencode($book_request ['data'] ['request'])), $this->xml_header());
            
            //$GLOBALS ['CI']->custom_db->generate_static_response ($book_response, 'REZLIVE hotel booking response' );
             //debug($book_response);die('book_response');
            
            $book_response = utf8_encode($book_response);

            $book_response = Converter::createArray($book_response);

            $book_response = $this->format_booking_details($book_response);
            //debug($book_response);die('book_response');
            

            /*if (isset($book_response['BookingResponse']['BookingDetails']) == true && valid_array($book_response['BookingResponse']['BookingDetails']) == true && $book_response['BookingResponse']['BookingDetails']['BookingStatus'] == 'Confirmed') {
                $response['status'] = SUCCESS_STATUS;
                $response['data']['book_response'] = $book_response;
                $response['data']['booking_params'] = $booking_params;
                $response['data']['room_book_data'] = ''; //FIXME:its an XML
            }*/
            $GLOBALS ['CI']->custom_db->generate_static_response ( json_encode ( $book_response ) );        
        
            $api_book_response_status = $book_response['Status'];
            $book_response['BookResult'] = @$book_response['CommitBooking']['BookingDetails'];
        
            //debug($booking_params);echo '------------------';
            $GLOBALS ['CI']->private_management_model->provab_xml_logger ( 'Book_Room', $book_id, 'hotel', $book_request ['data'] ['request'], json_encode ( $book_response ) );
            // validate response
            if ($this->valid_response ( $api_book_response_status )) {
                $response ['status'] = SUCCESS_STATUS;
                $response ['data'] ['book_response'] = $book_response;
                $response ['data'] ['booking_params'] = $booking_params;
                $block_data_array = $book_request ['data'] ['request'];
                $room_book_data = json_decode ( $block_data_array, true );
                $room_book_data['HotelRoomsDetails'] = $this->formate_hotel_room_details($booking_params);
                
                $response ['data'] ['room_book_data'] = $this->convert_roombook_data_to_application_currency ( $room_book_data );
            }
            else{
                $response ['data']['message'] = $book_response['Message'];
            }
        }
        //debug($response);exit('123-rez');
        return $response;
    }

    public function format_booking_details($book_response){
        //debug($book_response['BookingResponse']);echo '----';
        $response = array();
        $response['Status'] = FAILURE_STATUS;

        if(isset($book_response['BookingResponse']['BookingDetails']['BookingId'])){

            $boking_details = $book_response['BookingResponse']['BookingDetails'];
            //debug($boking_details);echo '---------s';//die();
            $BookingDetails = array (
                    'ConfirmationNo' => $boking_details['BookingCode'],
                    'BookingRefNo' => '',
                    'BookingId' => $boking_details['BookingId'],
                    'SupplierCode' => '',
                    'SupplierVatId' => '',
                    'booking_status' => 'BOOKING_CONFIRMED',
                    'booking_price' => $boking_details['BookingPrice'],
                );

            $response['Status'] = SUCCESS_STATUS;
            $response['Message'] = 'Succes';
            $response['CommitBooking']['BookingDetails'] = $BookingDetails;

        }else{
            $response['Message'] = $book_response['BookingResponse']['BookingDetails']['BookingReason'];
            $response['Status'] = FAILURE_STATUS;
        }
        //debug($response);die();
        return $response;
    }


    /////////////
    function get_hotel_static_detail_new($hotel_code)
    {
        
        $response ['data'] = array();
        $response ['status'] = false;
        $hotel_static_request = $this->get_hotel_static_detail_request_new($hotel_code);
        //debug($hotel_static_request);die('00');

        if ($hotel_static_request ['status']) {

            $GLOBALS ['CI']->custom_db->generate_static_response ($hotel_static_request ['data'] ['request'], 'REZLIVE hotel static detail request' );

             $hotel_details_response = $GLOBALS ['CI']->api_interface->xml_post_request($hotel_static_request ['data'] ['service_url'], ('XML='.urlencode($hotel_static_request ['data'] ['request'])), $this->xml_header());
             //debug($hotel_details_response);die('$hotel_details_response');
            
            //$GLOBALS ['CI']->custom_db->generate_static_response ($hotel_details_response, 'REZLIVE hotel static detail response' );

            $hotel_details_response = Converter::createArray($hotel_details_response);
            //debug($hotel_details_response);die('hotel_details_response123');
            if (isset($hotel_details_response['HotelDetailsResponse']['Hotels'])) {
                $response ['data'] = $hotel_details_response['HotelDetailsResponse']['Hotels'];
                $response ['status'] = true;
            } else {
                // Need the complete data so that later we can use it for redirection
                $response ['data'] = $hotel_details_response;
            }
        }
        //debug($response);die('123');
        return $response;
    }

    private function get_hotel_static_detail_request_new($hotel_code) {
        $response ['status'] = true;
        $response ['data'] = array();
        $request = '';

        // request to get the static hotel data
        if( isset($hotel_code) && !empty($hotel_code) ) {

            $request .= '<?xml version="1.0"?><HotelDetailsRequest><Authentication><AgentCode>'.$this->agent_id.'</AgentCode><UserName>'.$this->username.'</UserName><Password>'.$this->password.'</Password></Authentication>';

            $request .= '<Hotels><HotelId>'.$hotel_code.'</HotelId></Hotels></HotelDetailsRequest>';
        }

        $response ['data'] ['service_url'] = $this->service_url . '/gethoteldetails';
        $response ['data'] ['request'] = $request;
        $response ['status'] = SUCCESS_STATUS;

        return $response;
    }
}	

