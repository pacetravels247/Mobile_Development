<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once BASEPATH . 'libraries/hotel/Common_api_hotel.php';
/**
 *
 * @package Provab
 * @subpackage API
 * @author Arjun J<arjunjgowda260389@gmail.com>
 * @version V1
 */
class Rezlive extends Common_Api_Hotel {

    private $ClientId;
    private $UserName;
    private $Password;
    private $Url;
    private $LoginType = '2'; // (For API Users, value should be ‘2’)
    private $EndUserIp = '127.0.0.1';
    private $TokenId; // Token ID that needs to be echoed back in every subsequent request
    public $master_search_data;
    public $search_hash;
    protected $ins_token_file;
    protected $medium_image_base_url;
    protected $small_image_base_url;
    protected $service_url;
    protected $agent_id;
    protected $username;
    protected $password;
    protected $signature;
    // protected $api_online_key;
    protected $json_api_header;
    protected $xml_api_header;
    protected $api_data_currency = 'INR';
    private $aminity_count = -1;
    public $booking_source_code = REZLIVE_HOTEL;

    public function __construct() {
        parent::__construct(META_ACCOMODATION_COURSE, $this->booking_source_code);
        $this->CI = &get_instance();
        $GLOBALS ['CI']->load->library('Api_Interface');
        $GLOBALS ['CI']->load->library('converter');
        $GLOBALS ['CI']->load->model('hotel_model');
        $this->set_api_credentials();
    }

    private function set_api_credentials() {
        //$hotel_engine_system = $this->CI->config->item('hotel_engine_system');
//		$this->system = $hotel_engine_system;
        $this->details = $this->CI->config->item ('rezlive_hotel_test');

        /*$this->service_url = $this->config['endpoint_url'];
        $this->agent_id = $this->config['user_code'];
        $this->username = $this->config['username'];
        $this->password = $this->config['password'];*/

        $this->service_url = $this->details['endpoint_url'];
        $this->agent_id = $this->details['user_code'];
        $this->username = $this->details['username'];
        $this->password = $this->details['password'];

        // $this->signature = hash("sha256", $this->config['key'] . $this->config['secret'] . time());
        // $this->api_online_key = $this->config['key'];
        $this->small_image_base_url = $this->config['small_image_base_url'];
        $this->medium_image_base_url = $this->config['medium_image_base_url'];
        // $this->api_content_url = $this->config['api_content_url'];
        $this->currency = $this->config['currency'];
        //debug($this->config);exit;

        /*debug($this->service_url);
        debug($this->agent_id);
        debug($this->username);
        debug($this->password);
        die();*/
    }

    /**
     * Header to be used for hotebeds - JSON API Version
     */
    private function json_header() {
        $this->json_api_header = array('Api-Key: ' . $this->api_online_key,
            'X-Signature: ' . $this->signature,
            'X-Originating-Ip: 14.141.47.106',
            'Content-Type: application/json',
            'Accept: application/json',
            'Accept-Encoding: gzip'
        );
        return $this->json_api_header;
    }

    /**
     * Header to be used for hotebeds - XML API Version
     */
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

    /**
     * Arjun J Gowda
     *
     * get hotel search request details
     *
     * @param array $search_params
     *        	data to be used while searching of hotels
     */
    private function hotel_search_request($search_params) {
        // error_reporting(E_ALL);
        // form request for hb
        $request = '';
        if (isset($search_params) && !empty($search_params)) {
             // $url = json_decode(file_get_contents('https://tools.keycdn.com/geo.json'));
             // '.$url->data->geo->country_code.'
            // get country code and city code and guest country code from db;
            // old
            /*$cond['country'] = $search_params['country_name'];
            $county_code = $GLOBALS ['CI']->hotel_model->get_rz_country_code($cond);

            $condition['city'] = $search_params['city_name'];
            $condition['country_code'] = $county_code[0]['code'];
            // debug($condition);exit;
            $city_code = $GLOBALS ['CI']->hotel_model->get_rz_city_code($condition);
            $city_code[0]['city_code']
            $city_code[0]['country_code']*/
            // new
            if(!empty($search_params['rz_country_code']) && !empty($search_params['rz_city_code'])) {

            $request .= '<?xml version="1.0"?><HotelFindRequest><Authentication><AgentCode>'.$this->agent_id.'</AgentCode><UserName>'.$this->username.'</UserName><Password>'.$this->password.'</Password></Authentication>';

            $request .= '<Booking><ArrivalDate>'. (date('d/m/Y', strtotime($search_params['from_date']))) .'</ArrivalDate><DepartureDate>'. (date('d/m/Y', strtotime($search_params['to_date']))) .'</DepartureDate><CountryCode>'.$search_params['rz_country_code'].'</CountryCode><City>'.$search_params['rz_city_code'].'</City><GuestNationality>IN</GuestNationality><HotelRatings><HotelRating>1</HotelRating><HotelRating>2</HotelRating><HotelRating>3</HotelRating><HotelRating>4</HotelRating><HotelRating>5</HotelRating></HotelRatings>';

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
        return $response;
    }

    private function combined_pax_hotel_search_request($search_params) {
        // form request for hb
        $request = array();
        //debug($search_params);exit;
        if (isset($search_params) && !empty($search_params)) {
            $request ['stay'] ['checkIn'] = $search_params ['from_date'];
            $request ['stay'] ['checkOut'] = $search_params ['to_date'];

            $request ['keywords']['allIncluded'] = TRUE;
            $request ['filter']['paymentType'] = 'AT_WEB';
            $request ['destination'] ['code'] = $search_params['location_id'];

            $roomCount = $search_params ['room_count'];
            $child_count = 0;

            $request ['occupancies'] [0] ['rooms'] = $roomCount;
            $request ['occupancies'] [0] ['adults'] = array_sum($search_params ['adult_config']);
            $request ['occupancies'] [0] ['children'] = array_sum($search_params['child_config']);

            $no_of_child = array_sum($search_params['child_config']);
            for ($j = 0; $j < $no_of_child; $j++) {
                $request['occupancies'][0] ['paxes'][$j]['type'] = 'CH';
                $request['occupancies'][0] ['paxes'][$j]['age'] = (isset($search_params['child_age'][$j]) ? $search_params['child_age'][$j] : 5);
            }
        }
        $response ['data'] ['request'] = json_encode($request);
        $response ['data'] ['service_url'] = $this->service_url . 'hotels';
        $response ['status'] = SUCCESS_STATUS;
        //debug($response);exit();
        return $response;
    }

    /**
     * Arjun J Gowda
     *
     * Hotel Details Request
     *
     * @param string $TraceId
     * @param string $ResultIndex
     * @param string $HotelCode
     */
    private function hotel_details_request($search_params, $hotel_code) {
        $response ['status'] = true;
        $response ['data'] = array();
        $request = '';
     
        // form request for rz
        if ((isset($search_params) && !empty($search_params)) && (isset($hotel_code) && !empty($hotel_code))) {

             // $url = json_decode(file_get_contents('https://tools.keycdn.com/geo.json'));
            // '.$url->data->geo->country_code.'

          /*  // get country code and city code and guest country code from db;
            $cond['country'] = $search_params['country_name'];
            $county_code = $GLOBALS ['CI']->hotel_model->get_rz_country_code($cond);

            $condition['city'] = $search_params['city_name'];
            $condition['country_code'] = $county_code[0]['code'];
            // debug($condition);exit;
            $city_code = $GLOBALS ['CI']->hotel_model->get_rz_city_code($condition);*/
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

    private function combined_pax_hotel_details_request($search_params, $HotelCode) {
        $response ['status'] = true;
        $response ['data'] = array();
        $request = array();
        $request['hotelCode'] = $HotelCode;

        if (isset($search_params) && !empty($search_params)) {
            $request['checkIn'] = date('Y-m-d', strtotime($search_params['from_date']));
            $request['checkOut'] = date('Y-m-d', strtotime($search_params['to_date']));
            $request['occupancies'] = $search_params['room_count'];

            $roomCount = $search_params ['room_count'];

            $child_count = 0;

            $roomNo = 1;
            $str = '';
            $tex = '';
            //debug($search_params);
            //for($i = 0; $i < $roomCount; $i ++) {
            $str = $roomCount . '~' . array_sum($search_params['adult_config']) . '~' . array_sum($search_params['child_config']);
            $adultTxt = '';
            for ($n = 0; $n < array_sum($search_params['adult_config']); $n++) {
                $adultTxt = isset($adultTxt) && !empty($adultTxt) ? $adultTxt . ';AD-30' : '~AD-30';
            }
            $str .= $adultTxt;

            /* string for child */
            $no_of_child = array_sum($search_params['child_config']);
            for ($j = 0; $j < $no_of_child; $j++) {
                $sep = '';
                /* if ($j == 0) { */
                $sep = ';';
                /* } else {
                  $sep = '~';
                  } */
                $str .= $sep . 'CH' . '-' . (isset($search_params['child_age'][$j]) ? $search_params['child_age'][$j] : 5);
            }
        }
        $response ['data'] ['params'] = '?checkIn=' . $request['checkIn'] . '&checkOut=' . $request['checkOut'] . '&occupancies=' . $str;
        $response ['data'] ['service_url'] = $this->service_url . 'hotels/' . $HotelCode . $response ['data'] ['params'];
        $response ['status'] = SUCCESS_STATUS;
        return $response;
    }

    /**
     * Jaganath
     * Check Rate Request With GET Method
     * FIXME: Not implemented
     * @param unknown_type $rate_keys
     */
    private function check_room_rate_request_get($rate_keys) {
        echo 'under implementation';
        $response ['status'] = SUCCESS_STATUS;
        $response ['data'] = array();
        $request = array();
        //$params = 'rateKey='.$rate_keys[0];
        $params['rateKey'] = array();
        foreach ($rate_keys as $r_k => $r_v) {
            $params['rateKey'][$r_k] = $r_v;
        }
        $params = http_build_query($params);
        $response ['data'] ['service_url'] = $this->service_url . 'checkrates?' . $params;
        return $response;
    }

    /**
     * Jaganath
     * Check Rate Request With POST Method
     * @param unknown_type $rate_keys
     */
    private function check_room_rate_request_post($rate_keys) {
        $response ['status'] = SUCCESS_STATUS;
        $response ['data'] = array();
        $request = array();
        foreach ($rate_keys as $r_k => $r_v) {
            $request['rooms'][$r_k]['rateKey'] = $r_v;
        }
        $response ['data'] ['request'] = json_encode($request);
        $response ['data'] ['service_url'] = $this->service_url . 'checkrates';
        return $response;
    }

    /**
     *
     */
    function get_rate_comment_details_request($rateCommentsId, $from_date) {
        $response ['status'] = SUCCESS_STATUS;
        $response ['data'] = array();
        $request = array();
        $response ['data'] ['service_url'] = $this->api_content_url . 'ratecommentdetail?code=' . $rateCommentsId . '&fields=all&date=' . $from_date . '&language=ENG';
        return $response;
    }

    /**
     * Arjun J Gowda
     *
     * Room Details Request
     *
     * @param string $TraceId
     * @param string $ResultIndex
     * @param string $HotelCode
     */
    private function room_list_request($TraceId, $ResultIndex, $HotelCode) {
        
    }

    /**
     * Arjun J Gowda
     *
     * get room block request
     *
     * @param array $booking_parameters
     */
    private function get_block_room_request($booking_params) {
        
    }

    /**
     * Form Book Request
     */
    function get_book_request($booking_params, $booking_id) {
        $search_id = $booking_params ['token'] ['search_id'];
        $rate_keys = $booking_params ['token'] ['rateKey'];
        $response ['status'] = true;
        $safe_search_data = $GLOBALS ['CI']->hotel_model->get_search_data($search_id);
        $search_data = json_decode($safe_search_data ['search_data'], true);
        $number_of_nights = get_date_difference(date('Y-m-d', strtotime($search_data ['hotel_checkin'])), date('Y-m-d', strtotime($search_data ['hotel_checkout'])));
        $NO_OF_ROOMS = $search_data ['rooms'];
        for ($i = 0; $i < $NO_OF_ROOMS; $i++) {
            $booking_params ['token'] ['token'] [$i] ['no_of_pax'] = $search_data ['adult'] [$i] + $search_data ['child'] [$i];
        }
        /* Forming Request */
        $request = array();
        $response ['data'] = array();
        $k = 0;
        for ($i = 0; $i < $NO_OF_ROOMS; $i++) {
            //Passeengers
            $paxes = array();
            $no_of_pax = $booking_params ['token'] ['token'] [$i] ['no_of_pax'];
            $room_paxes = array();
            for ($j = 0; $j < $no_of_pax; $j++) {
                $pax_list['type'] = $booking_params ['passenger_type'][$k];
                $pax_list['name'] = $booking_params ['first_name'] [$k];
                $pax_list['surname'] = $booking_params ['last_name'] [$k];
                $pax_list['age'] = "25"; //FIXME: Age needed paxtype is Child
                $pax_list['roomId'] = "1"; //FIXME
                $k++;
                if ($no_of_pax == 1) {
                    $room_paxes['pax'] = $pax_list;
                } else {
                    $room_paxes['pax'][$j] = $pax_list; //Multi-Pax
                }
            }
            //Room
            if ($NO_OF_ROOMS == 1) {
                $room['rateKey'] = array_shift($rate_keys);
                $room['paxes'] = $room_paxes;
            } else {
                $room[$i]['rateKey'] = array_shift($rate_keys); //Mutiple Rooms
                $room[$i]['paxes'] = $room_paxes;
            }
        }
        //Rooms
        //Holder's Name--Lead Pax
        $request['holder']['name'] = $booking_params ['first_name'][0];
        $request['holder']['surname'] = $booking_params ['last_name'][0];
        $request['rooms']['room'] = $room;
        $request['clientReference'] = 'Horizons Travel';
        //paymentData and Contact Details
        /* $payment_details['cardType'] = 'VI';
          $payment_details['cardNumber'] = '4444333322221111';
          $payment_details['expiryDate'] = '0615';
          $payment_details['cardCVC'] = '123';
          $payment_contact_details['email'] = $booking_params['billing_email'];
          $payment_contact_details['phoneNumber'] = $booking_params['passenger_contact'];
          $request['paymentData']['paymentCard'] = $payment_details;
          $request['paymentData']['contactData'] = $payment_contact_details; */

        //debug($request);exit;
        $response ['data'] ['request'] = json_encode($request);
        $response ['data'] ['service_url'] = $this->service_url . 'bookings';
        return $response;
    }

    function booking_xml_request($booking_params, $booking_id) {
        $response ['status'] = true;
        $response ['data'] = array();
        $request = '';
        $search_id = $booking_params ['token'] ['search_id'];
        $search_params = $this->search_data($search_id);
        if( isset($search_params) && !empty($search_params['data']) ) {
            
            $city_code[0]['city_code'] = $search_params['data']['rz_city_code'];
            $city_code[0]['country_code'] = $search_params['data']['rz_country_code'];
            $request .= '<?xml version="1.0"?><BookingRequest><Authentication><AgentCode>'.$this->agent_id.'</AgentCode><UserName>'.$this->username.'</UserName><Password>'.$this->password.'</Password></Authentication>';
            $request .= '<Booking>
            <SearchSessionId>'.$booking_params['token']['token_data']['search_session_id'].'</SearchSessionId>
            <AgentRefNo>'.$booking_id.'</AgentRefNo>
            <ArrivalDate>'. (date('d/m/Y', strtotime($search_params['data']['from_date']))) .'</ArrivalDate>
            <DepartureDate>'. (date('d/m/Y', strtotime($search_params['data']['to_date']))) .'</DepartureDate>
            <GuestNationality>'.$booking_params['token']['token_data']['guest_nationality'].'</GuestNationality>
            <CountryCode>'.$city_code[0]['country_code'].'</CountryCode>
            <City>'.$city_code[0]['city_code'].'</City>
            <HotelId>'.$booking_params['token']['hotel_code'].'</HotelId>
            <Name>'.$booking_params['token']['token_data']['hotel_name'].'</Name>
            <Address>'.$booking_params['token']['token_data']['address'].'</Address>
            <Currency>'.$booking_params['token']['token_data']['currency'].'</Currency>
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
                $room_type .= $booking_params['token']['token_data']['rooms'][$booking_params['token']['rateKey'][0]]['rates']['name'];
            // }
            // $room_type .=  $booking_params['token']['token_data']['rooms'][$booking_params['token']['rateKey'][0]]['rates']['room_description'];

            $request .= '<Type>'.$room_type.'</Type>
           <BookingKey>'.$booking_params['token']['token_updated_data']['data']['pre_booking_request_data']['RoomDetails']['RoomDetail']['BookingKey'].'</BookingKey>
            <Adults>'.$adult_count.'</Adults>
            <Children>'.$child_count.'</Children>';
// <BookingKey>'.$booking_params['token']['token_data']['rooms'][$booking_params['token']['rateKey'][0]]['rates']['booking_key'].'</BookingKey>
            // <BookingKey>'.$booking_params['token']['token_updated_data']['data']['pre_booking_request_data']['RoomDetails']['RoomDetail']['BookingKey'].'</BookingKey>
            if ($booking_params['token']['token_data']['rooms'][$booking_params['token']['rateKey'][0]]['children'] > 0)   
            {
                // $request .= '<ChildrenAges>'.$child_ages.'</ChildrenAges>';
                $request .= '<ChildrenAges>'.$booking_params['token']['token_updated_data']['data']['pre_booking_request_data']['RoomDetails']['RoomDetail']['ChildrenAges'].'</ChildrenAges>';
            }
            $request .= '<TotalRooms>'.$booking_params['token']['token_data']['rooms'][$booking_params['token']['rateKey'][0]]['rates']['rooms'].'</TotalRooms>
            <TotalRate>'.$booking_params['token']['token_data']['rooms'][$booking_params['token']['rateKey'][0]]['rates']['net'].'</TotalRate>';

            $request .= $guest_list .'
            </RoomDetail>
            </RoomDetails>
            </Booking>
            </BookingRequest>';
        }
        $response ['data'] ['service_url'] = $this->service_url . '/bookhotel';
        $response ['data'] ['request'] = $request;
        $response ['status'] = SUCCESS_STATUS;
        return $response;
/*



        $search_id = $booking_params ['token'] ['search_id'];
        $rate_keys = $booking_params ['token'] ['rateKey'];

        $room_config = $booking_params['token']['room_configuration']['rooms'];
        $adult_config = $booking_params['token']['room_configuration']['adults'];
        $child_config = $booking_params['token']['room_configuration']['childs'];
        $room_config_count = count($room_config);

        $response ['status'] = true;
        $safe_search_data = $GLOBALS ['CI']->hotel_model->get_search_data($search_id);
        $search_data = json_decode($safe_search_data ['search_data'], true);

         // child ages 
        $child_age_arr = array();
        if (isset($search_data['child']) && valid_array($search_data['child'])) {
            foreach ($search_data['child'] as $c_key => $child) {
                if ($child != 0 && !empty($child)) {
                    $child_age_key = 'childAge_' . ($c_key + 1);
                    if (isset($search_data[$child_age_key]) && valid_array($search_data[$child_age_key])) {
                        foreach ($search_data[$child_age_key] as $chi_key => $child_age) {
                            $child_age_arr[] = $child_age;
                        }
                    }
                }
            }
        }

         // rooms 
        $room_paxes_detials = array();
        $room_paxes_cnt = 0;
        if (isset($booking_params['token']['token']['data']['rooms']) && valid_array($booking_params['token']['token']['data']['rooms'])) {
            foreach ($booking_params['token']['token']['data']['rooms'] as $room_key_d => $room_detials) {
                if (isset($room_detials['rates']) && valid_array($room_detials['rates'])) {
                    foreach ($room_detials['rates'] as $rate_key => $rates_details) {
                        $room_paxes_detials[$room_paxes_cnt]['room_name'] = $room_detials['room_name'];
                        $room_paxes_detials[$room_paxes_cnt]['rateKey'] = $rates_details['rateKey'];

                        $no_of_adults = $rates_details['adults'] / $rates_details['rooms'];
                        $no_of_childs = $rates_details['children'] / $rates_details['rooms'];
                        $total_pax_cnt = $no_of_adults + $no_of_childs;

                        $room_paxes_detials[$room_paxes_cnt]['no_of_rooms'] = $rates_details['rooms'];
                        $room_paxes_detials[$room_paxes_cnt]['no_of_adults'] = $rates_details['adults'] * $rates_details['rooms'];
                        $room_paxes_detials[$room_paxes_cnt]['no_of_children'] = $rates_details['children'] * $rates_details['rooms'];
                        $room_paxes_cnt++;
                    }
                }
            }
        }

        $number_of_nights = get_date_difference(date('Y-m-d', strtotime($search_data ['hotel_checkin'])), date('Y-m-d', strtotime($search_data ['hotel_checkout'])));
        $no_of_rooms = $search_data ['rooms'];
        for ($i = 0; $i < $no_of_rooms; $i++) {
            $booking_params ['token'] ['token'] [$i] ['no_of_pax'] = $search_data ['adult'] [$i] + $search_data ['child'] [$i];
        }
        $response ['data'] = array();
        $k = 0;

         // Forming Request 
        $request = '<bookingRQ xmlns="http://www.hotelbeds.com/schemas/messages" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">';
        $request .= '<holder name="' . $booking_params ['first_name'][0] . '" surname="' . $booking_params ['last_name'][0] . '"/>';
        $rooms_tag_request = '<rooms>';
        $child_age_cnt_index = 0;
        $total_pax = 0;

        foreach ($room_paxes_detials as $rpd_key => $rooms_pax) {

            $rate_key = $rooms_pax['rateKey'];
            $rooms_tag_request .= '<room rateKey="' . $rate_key . '">';
            $rooms_tag_request .= '<paxes>';
            $roomId = 1;
            for ($j = 0; $j < $rooms_pax['no_of_rooms']; $j++) {
                $no_of_adults = $rooms_pax['no_of_adults'] / $rooms_pax['no_of_rooms'];
                $no_of_childs = $rooms_pax['no_of_children'] / $rooms_pax['no_of_rooms'];
                $total_pax_cnt = $no_of_adults + $no_of_childs;

                for ($a = 0; $a < $total_pax_cnt; $a++) {
                    $pass_rate_key = $booking_params['rateKey'][$total_pax];
                    if ($rate_key == $pass_rate_key) {
                        $type = $booking_params ['passenger_type'][$total_pax];
                        $name = $booking_params ['first_name'] [$total_pax];
                        $surname = $booking_params ['last_name'] [$total_pax];
                        if ($type == 'CH') {
                            $age = $child_age_arr[$child_age_cnt_index]; //FIXME: Age needed paxtype is Child-make it dynamic
                            $child_age_cnt_index++;
                        } else {
                            $age = 30; //FIXME: Age needed paxtype is Child
                        }
                        $rooms_tag_request .= '<pax roomId="' . $roomId . '" type="' . $type . '" age="' . $age . '" name="' . $name . '" surname="' . $surname . '"></pax>';
                    }
                    //exit;
                    $total_pax++;
                }
                $roomId++;
            }

            $rooms_tag_request .= '</paxes>';
            $rooms_tag_request .= '</room>';
        }

        $rooms_tag_request .= '</rooms>';
        $request .= $rooms_tag_request;
        $request .= '<clientReference>CHARIOT TRAVEL</clientReference>';
        //$request .= '<clientReference>HORIZONS TRAVEL</clientReference>';
        $request .= '</bookingRQ>';

         // Forming Request 
         // $request = '<bookingRQ xmlns="http://www.hotelbeds.com/schemas/messages" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">';
         //  $request .= '<holder name="'.$booking_params ['first_name'][0].'" surname="'.$booking_params ['last_name'][0].'"/>';
         //  $rooms_tag_request = '<rooms>';
         //  $child_age_cnt_index = 0;

         //  for($i = 0; $i < $room_config_count; $i ++) {//Room Config 3
         //  $numer_of_rooms = array_shift($room_config);
         //  $no_of_pax = array_shift($adult_config)+array_shift($child_config);

         //  $current_rate_key = array_shift($rate_keys);
         //  $rooms_tag_request .= '<room rateKey="'.$current_rate_key.'">';
         //  for($r=0; $r<$numer_of_rooms; $r++){//Rooms Loop
         //  $rooms_tag_request .= '<paxes>';
         //  //Pax request
         //  for($p=0; $p<$no_of_pax; $p++) {
         //  $type = $booking_params ['passenger_type'][$k];
         //  $name = $booking_params ['first_name'] [$k];
         //  $surname = $booking_params ['last_name'] [$k];
         //  if($type == 'CH') {
         //  $age = $child_age_arr[$child_age_cnt_index];//FIXME: Age needed paxtype is Child-make it dynamic
         //  $child_age_cnt_index++;
         //  } else {
         //  $age = 30;//FIXME: Age needed paxtype is Child
         //  }
         //  $rooms_tag_request .= '<pax roomId="'.$roomId.'" type="'.$type.'" age="'.$age.'" name="'.$name.'" surname="'.$surname.'"/>';
         //  $k++;
         //  }
         //  $rooms_tag_request .= '</paxes>';
         //  }
         //  $rooms_tag_request .= '</room>';
         //  }
         //  $rooms_tag_request .= '</rooms>';
         //  $request .= $rooms_tag_request;
         //  $request .= '<clientReference>HORIZONS TRAVEL</clientReference>';
         //  $request .= '</bookingRQ>'; 

        $response ['data'] ['request'] = $request;
        $response ['data'] ['service_url'] = $this->service_url . 'bookings';

        return $response;*/
    }

    /**
     * Jagnath
     * Cancellation Request:SendChangeRequest
     */
    private function send_change_request_params($BookingId, $BookingCode) {
        $request ='';

        if(!(empty($BookingId) && empty($BookingId))) {
            $request .= '<?xml version=”1.0” ?><CancellationRequest><Authentication><AgentCode>'.$this->agent_id.'</AgentCode><UserName>'.$this->username.'</UserName><Password>'.$this->password.'</Password></Authentication><Cancellation><BookingId>'.$BookingId.'</BookingId><BookingCode>'.$BookingCode.'</BookingCode></Cancellation></CancellationRequest>' ;
        }

        $response['service_url'] = '';
        $response['status'] = SUCCESS_STATUS;
        $delete_url = $this->service_url . '/cancelhotel';
        $response['data']['request'] = $request;
        $response['data']['service_url'] = $delete_url;
        return $response;
    }

    /**
     * Jagnath
     * Cancellation Status:GetChangeRequestStatus
     */
    private function get_change_request_status_params($ChangeRequestId) {
        
    }

    /**
     * Arjun J Gowda
     * get search result from tbo
     *
     * @param number $search_id
     *        	unique id which identifies search details
     */
    /* function get_hotel_list($search_id = '', & $timeline = '',$module='b2c') {
      $this->CI->load->driver ( 'cache' );
      $response ['data'] = array ();
      $response ['status'] = true;
      $search_data = $this->search_data ( $search_id );
      //		debug($search_data);exit;

      $cache_search = $this->CI->config->item ( 'cache_hotel_search' );
      $search_hash = $this->search_hash;
      if ($cache_search) {
      $cache_contents = $this->CI->cache->file->get ( $search_hash );
      }

      if ($search_data ['status'] == true) {
      $city_id = $search_data ['data']['location_origin'];
      if ($cache_search === false || ($cache_search === true && empty ( $cache_contents ) == true)) {
      // form request
      $search_request = $this->hotel_search_request ( $search_data ['data'] );
      //$search_request = $this->combined_pax_hotel_search_request ( $search_data ['data'] );
      debug($search_request);
      if ($search_request ['status']) {error_reporting(E_ALL);
      // get response using curl
      $timeline['api'] = microtime(true);
      $search_response = $GLOBALS ['CI']->api_interface->post_rest_service ( $search_request ['data'] ['service_url'], $search_request ['data'] ['request'], $this->json_header() );
      $timeline['api'] = microtime(true)-$timeline['api'];
      $tmp_response = json_decode ( $search_response, TRUE );
      debug($search_response);exit;

      if ($this->valid_search_result ( $tmp_response )) {
      $hotel_response = $tmp_response ['hotels'] ['hotels'];
      $timeline['format'] = microtime(true);
      $this->aminity_count = 6;
      $response ['data']['HotelSearchResult'] = $this->get_formatted_hotel($hotel_response, $search_id,$module);
      //debug($response);exit;
      $timeline['format'] = microtime(true) - $timeline['format'];
      $total_result_count = count($response ['data']['HotelSearchResult']);
      //$response ['data'] = $hotel_array;
      if ($total_result_count > 0) {
      //cache only if results are found
      if ($cache_search) {
      $cache_exp = $this->CI->config->item ( 'cache_hotel_search_ttl' );
      $this->CI->cache->file->save ( $search_hash, $response ['data'], $cache_exp );
      }
      // Log Hotels Count
      $this->cache_result_hotel_count ( $city_id, $total_result_count );
      }
      } else {
      $response ['status'] = false;
      }
      } else {
      $response ['status'] = false;
      }
      } else {
      // read from cache
      $response ['data'] = $cache_contents;
      }
      } else {
      $response ['status'] = false;
      }
      return $response;
      } */


    function get_hotel_list($search_id = '', $module) {//debug($search_id);exit;
        $this->CI->load->driver('cache');
        $response ['data'] = array();
        $response ['status'] = true;
        $search_data = $this->search_data($search_id);
        $search_data ['status'] = true;

        $cache_search = $this->CI->config->item('cache_hotel_search');
        $search_hash = $this->search_hash;
        if ($cache_search) {
            $cache_contents = $this->CI->cache->file->get($search_hash);
        }

        if ($search_data ['status'] == true) {
            //$city_id = $search_data ['data']['location_origin'];
            //if ($cache_search === false || ($cache_search === true && empty ( $cache_contents ) == true)) {
            // form request
            // send id to request to update request data 
            $search_data['data']['search_id'] = $search_id;
            $search_request = $this->hotel_search_request($search_data ['data']);
            if ($search_request ['status']) {
                // get response using curl
            	$GLOBALS ['CI']->custom_db->generate_static_response ($search_request ['data'] ['request'], 'REZLIVE hotel search request' );

                $timeline['api'] = microtime(true);
                $search_response = $GLOBALS ['CI']->api_interface->xml_post_request($search_request ['data'] ['service_url'], ('XML='.urlencode($search_request ['data'] ['request'])), $this->xml_header());
                 /*debug($search_response);
                 die('7');*/
                // echo 'res'; 
                 $GLOBALS ['CI']->custom_db->generate_static_response ($search_response, 'REZLIVE hotel search response' );
                 if(!empty($search_response))
                 $search_response = Converter::createArray($search_response);
                 // debug($search_response);exit;

                $timeline['api'] = microtime(true) - $timeline['api'];
                $tmp_response = $search_response;
                if ($this->valid_search_result($tmp_response)) {
                    $hotel_response = $tmp_response['HotelFindResponse'];
                    // debug($hotel_response);exit;
                    $timeline['format'] = microtime(true);
                    $this->aminity_count = 6;
                    //debug($module);exit;

                    $response ['data']['hotel_list'] = $this->get_formatted_hotel($hotel_response, $search_id, $module);

                    $timeline['format'] = microtime(true) - $timeline['format'];
                    $total_result_count = count($response ['data']['hotel_list']);
                    if ($total_result_count > 0) {
                        //cache only if results are found
                        if ($cache_search) {
                            $cache_exp = $this->CI->config->item('cache_hotel_search_ttl');
                            $this->CI->cache->file->save($search_hash, $response ['data'], $cache_exp);
                        }
                        // Log Hotels Count
                        $this->cache_result_hotel_count(@$city_id, $total_result_count);
                    }
                } else {
                    $response ['status'] = false;
                }
            } else {
                $response ['status'] = false;
            }
            /* } else {
              // read from cache
              $response ['data'] = $cache_contents;
              } */
        } else {
            $response ['status'] = false;
        }
//		debug($response);
//		exit;
        return $response;
    }

    /**
     * Get Cache list of hotels
     *
     * @param number $search_id
     */
    private function cache_result_hotel_count($city_id, $hotel_count) {
        $CI = & get_instance();
        if ($hotel_count > 0 && $city_id > 0) {
            $CI->custom_db->update_record('hotels_city', array(
                'cache_hotelbeds_count' => $hotel_count
                    ), array(
                'origin' => $city_id
            ));
        }
    }

    /**
     * Arjun J Gowda
     * get Room List for selected hotel
     *
     * @param string $TraceId
     * @param number $ResultIndex
     * @param string $HotelCode
     */
    function get_room_list($TraceId, $ResultIndex, $HotelCode) {

        return false;
        //Not used
        $response ['data'] = array();
        $response ['status'] = false;
        $hotel_room_request = $this->room_list_request($TraceId, $ResultIndex, $HotelCode);

        if ($hotel_room_request ['status']) {
            // get the response for hotel details
            $hotel_room_list_response = $GLOBALS ['CI']->api_interface->get_json_response($hotel_room_request ['data'] ['service_url'], $hotel_room_request ['data'] ['request'], $header);
            $GLOBALS ['CI']->custom_db->generate_static_response(json_encode($hotel_room_list_response));

            /*
             * $static_search_result_id = 813;//106;//68;//52;
             * $hotel_room_list_response = $GLOBALS['CI']->hotel_model->get_static_response($static_search_result_id);
             */
            if ($this->valid_room_details_details($hotel_room_list_response)) {
                $response ['data'] = $hotel_room_list_response;
                $response ['status'] = true;
            } else {
                // Need the complete data so that later we can use it for redirection
                $response ['data'] = $hotel_room_list_response;
            }
        }
        return $response;
    }

    /**
     * Arjun J Gowda
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
    function get_hotel_details($search_params, $HotelCode, $module = 'b2c') {
        $response ['data'] = array();
        $response ['status'] = false;
        $search_id = $search_params['search_id'];
        $hotel_details_request = $this->hotel_details_request($search_params, $HotelCode);
        
        // $this->CI->custom_db->generate_static_response($hotel_details_request ['data'] ['service_url'], 'HB details');
        //$hotel_details_request = $this->combined_pax_hotel_details_request ( $search_params, $HotelCode);

        if ($hotel_details_request ['status']) {

            $GLOBALS ['CI']->custom_db->generate_static_response ($hotel_details_request ['data'] ['request'], 'REZLIVE hotel detail request' );
            // get the response for hotel details
            // $hotel_details_response = $GLOBALS ['CI']->api_interface->get_json_response_getrequest($hotel_details_request ['data'] ['service_url'], $this->json_header());
             $hotel_details_response = $GLOBALS ['CI']->api_interface->xml_post_request($hotel_details_request ['data'] ['service_url'], ('XML='.urlencode($hotel_details_request ['data'] ['request'])), $this->xml_header());
            
            $GLOBALS ['CI']->custom_db->generate_static_response ($hotel_details_response, 'REZLIVE hotel detail response' );
            // $this->CI->custom_db->generate_static_response($hotel_details_response, 'HB details response');
            // $GLOBALS ['CI']->custom_db->generate_static_response($hotel_details_response);
            // $hotel_details_response = json_decode($hotel_details_response, true);
            $hotel_details_response = Converter::createArray($hotel_details_response);

            if ($this->valid_search_result($hotel_details_response)) {

                $hotel_details_response = $this->get_formatted_hotel_detail($hotel_details_response, $search_id,$module);
                $response ['data'] = $hotel_details_response;

                /*//Combination
                $hotel_details_array = $this->get_hotel_rooms_combinations($hotel_details_response, $search_id, $module);

                $formatted_rooms = $this->format_room_combination_list($hotel_details_array['rooms'], $module);

                $hotel_details_array['rooms'] = $formatted_rooms['list'];
                $hotel_details_array['min_price'] = $formatted_rooms['min_price'];
                $response ['data'] = $hotel_details_array;*/
                $response ['status'] = true;
            } else {
                // Need the complete data so that later we can use it for redirection
                $response ['data'] = $hotel_details_response;
            }
        }
        //debug($response);exit;
        return $response;
    }

    /**
     * formate hotel details and form room combinations
     * 
     * @param array $hotel_details_response
     * @param number $search_id
     * @param string $module
     * 
     * @return array hotel details 
     */
    public function get_hotel_rooms_combinations($hotel_details_response, $search_id, $module) {
        $hotelcode = $_GET['hotel_id'];
        $hotel_array = array();
        $new_arr = array();
        $hotel_detail_responce_arr = $hotel_details_response;
        $currency_obj = new Currency(array('module_type' => 'hotel', 'from' => get_application_default_currency(), 'to' => get_application_display_currency_preference()));

        if (isset($hotel_details_response['hotels']['hotels'][0]) && valid_array($hotel_details_response['hotels']['hotels'][0])) {
            $hotel_details = $hotel_details_response['hotels']['hotels'][0];
            $hotel_array['hotel_code'] = $hotel_details['code'];
            $hotel_array['hotel_name'] = $hotel_details['name'];
            $hotel_array['star_rating'] = $hotel_details['categoryName'];
            $hotel_array['destination'] = $hotel_details['destinationName'];
            $hotel_array['destination_code'] = $hotel_details['destinationCode'];
            $hotel_array['zone_code'] = $hotel_details['zoneCode'];
            $hotel_array['zone'] = $hotel_details['zoneName'];
            $hotel_array['latitude'] = isset($hotel_details['latitude']) && !empty($hotel_details['latitude']) ? $hotel_details['latitude'] : '';
            $hotel_array['longitude'] = isset($hotel_details['longitude']) && !empty($hotel_details['longitude']) ? $hotel_details['longitude'] : '';

            //convert currency to default currency
            $currency = $hotel_details['currency'];
            $currency_obj->getConversionRate(false, $currency, get_application_display_currency_preference());

            $min_rate = $hotel_details['minRate'];
            //debug($hotel_details);exit;
            $tax = $GLOBALS ['CI']->hotel_model->get_tax();
            $tax = $tax['data'];

            if ($module == 'b2c') {

                $converted_min_rate = $currency_obj->get_currency($min_rate, true, false, true, false, 1);





                if (isset($converted_min_rate['default_value'])) {
                    $min_rate = $converted_min_rate['default_value'];
                    $service_tax = (($min_rate / 100) * $tax);
                    $min_rate = $min_rate + $service_tax;
                    $currency = $converted_min_rate['default_currency'];
                }

                $max_rate = $hotel_details['maxRate'];
                $converted_max_rate = $currency_obj->get_currency($max_rate, false, false, false, 1);
                if (isset($converted_max_rate['default_value'])) {
                    $max_rate = $converted_max_rate['default_value'];
                    $service_tax = (($max_rate / 100) * $tax);
                    $max_rate = $max_rate + $service_tax;
                }
            } else {

                $converted_min_rate = $currency_obj->get_currency($min_rate, false, false, false, 1);

                $markup_total_fare_p = $converted_min_rate['default_value'];
                $adminmarkup = $GLOBALS ['CI']->hotel_model->getadmin_markup();


                # Admin Markup Calculations
                if ($adminmarkup['status'] == true) {
                    //debug($adminmarkup);exit;
                    $data = $adminmarkup['data'];
                    foreach ($data as $k => $v) {
                        $value_type = $v['value_type'];
                        $admin_mark_value = $v['value'];

                        if ($value_type == 'percentage') {
                            $markup_total_fare_ps = (($markup_total_fare_p / 100) * $admin_mark_value);
                            $markup_total_fare_p = ($markup_total_fare_p + $markup_total_fare_ps);
                        } else if ($value_type == 'plus') {
                            $markup_total_fare_ps = ($markup_total_fare_p + $admin_mark_value);
                            $markup_total_fare_p = ($markup_total_fare_ps);
                        } else {
                            $markup_total_fare_p = $markup_total_fare_p;
                        }
                    }
                }

                # Agent Markup Calculations






                $min_rate = $markup_total_fare_p;
                $service_tax = (($min_rate / 100) * $tax);
                $min_rate = $min_rate + $service_tax;
                $currency = $converted_min_rate['default_currency'];

                $max_rate = $hotel_details['maxRate'];
                $converted_max_rate = $currency_obj->get_currency($max_rate, false, false, false, 1);

                $markup_total_fare_max = $converted_max_rate['default_value'];

                $max_rate = $markup_total_fare_max;
                $service_tax = (($max_rate / 100) * $tax);
                $max_rate = $max_rate + $service_tax;
            }


            $hotel_array['minRate'] = $min_rate;
            $hotel_array['maxRate'] = $max_rate;
            $hotel_array['checkIn'] = $hotel_detail_responce_arr['hotels']['checkIn'];
            $hotel_array['checkOut'] = $hotel_detail_responce_arr['hotels']['checkOut'];
            $hotel_array['currency'] = $currency;
            //	$currency_obj = new Currency(array('module_type' => 'hotel', 'from' => get_application_default_currency(), 'to' => get_application_display_currency_preference()));
            $Admin_B2C_Markup = $GLOBALS ['CI']->domain_management_model->get_markup('hotel');

            if (isset($hotel_details['rooms']) && valid_array($hotel_details['rooms'])) {
                foreach ($hotel_details['rooms'] as $r_key => $rooms) {


                    if (isset($rooms['rates']) && valid_array($rooms['rates'])) {
                        foreach ($rooms['rates'] as $rate_key => $rates) {



                            $rates['code'] = $rooms['code'];
                            $rates['name'] = $rooms['name'];

                            $net_rate = $rates['net'];

                            $converted_net_rate = $currency_obj->get_currency($net_rate, false, false, false, 1);





                            if ($module == 'b2c') {

                                $net_rate = $converted_net_rate['default_value'];
                                $converted_net_rate = $converted_net_rate['default_value'];
                                if (isset($Admin_B2C_Markup['generic_markup_list'][0]['markup_origin'])) {
                                    $admin__b2cmark_value = $Admin_B2C_Markup['generic_markup_list'][0]['value'];
                                    if ($Admin_B2C_Markup['generic_markup_list'][0]['value_type'] == "percentage") {
                                        $markup_total_fare_ps = (($net_rate / 100) * $admin__b2cmark_value);
                                        $TotalFareWithAdminMarkup = ($net_rate + $markup_total_fare_ps);
                                        $markup_total_fare_ps_1 = $markup_total_fare_ps;
                                    } else {
                                        $markup_total_fare_ps = $admin__b2cmark_value;
                                        $TotalFareWithAdminMarkup = ($net_rate + $markup_total_fare_ps);
                                    }
                                } else {
                                    $TotalFareWithAdminMarkup = $net_rate;
                                }

                                $service_tax = (($TotalFareWithAdminMarkup / 100) * $tax);
                                $net_rate = $TotalFareWithAdminMarkup + $service_tax;
                            } else {

                                $converted_net_rate = $converted_net_rate['default_value'];
                                $markup_total_fare_p = $converted_net_rate;
                                $adminmarkup = $GLOBALS ['CI']->hotel_model->getadmin_markup();

                                # Admin Markup Calculations For Rooms
                                if ($adminmarkup['status'] == true) {
                                    //debug($adminmarkup);exit;
                                    $data = $adminmarkup['data'];
                                    foreach ($data as $k => $v) {
                                        $value_type = $v['value_type'];
                                        $admin_mark_value = $v['value'];

                                        if ($value_type == 'percentage') {
                                            $markup_total_fare_ps_1 = (($markup_total_fare_p / 100) * $admin_mark_value);
                                            $markup_total_fare_p = ($markup_total_fare_p + $markup_total_fare_ps_1);
                                        } else if ($value_type == 'plus') {
                                            $markup_total_fare_ps_1 = ($markup_total_fare_p + $admin_mark_value);
                                            $markup_total_fare_p = ($markup_total_fare_ps_1);
                                        } else {
                                            $markup_total_fare_p = $markup_total_fare_p;
                                        }
                                    }
                                }


                                #Agent Markup Calculations for Rooms
                                # Agent Markup Calculation
                                $AgentMarkup = $GLOBALS ['CI']->hotel_model->get_hotel_user_markup_details();
                                if (isset($AgentMarkup['markup'][0])) {
                                    $AgentMarkup_Type = $AgentMarkup['markup'][0];
                                    if ($AgentMarkup_Type['value_type'] == "percentage") {
                                        $AgentMarkupAmount = (($markup_total_fare_p * $AgentMarkup_Type['value']) / 100);
                                        $markup_total_fare_p+=$AgentMarkupAmount;
                                    } else {
                                        $AgentMarkupAmount = $AgentMarkup_Type['value'];
                                        $markup_total_fare_p+=$AgentMarkupAmount;
                                    }
                                }
                                $service_tax = (($markup_total_fare_p / 100) * $tax);
                                $markup_total_fare_p+=$service_tax;

                                $net_rate = $markup_total_fare_p;
                            }

                            # Markup and Serive Tax values for Induvidual Rooms


                            $rates['XMLPrice'] = @$converted_net_rate;
                            $rates['AdminMarkup'] = @$markup_total_fare_ps_1;
                            $rates['Agentmarkup'] = @$AgentMarkupAmount;
                            $rates['ServiceTax'] = @$service_tax;
                            $rates['net'] = $net_rate;

                            if (isset($rates['cancellationPolicies']) && valid_array($rates['cancellationPolicies'])) {
                                foreach ($rates['cancellationPolicies'] as $cel_key => $cancel_policy) {
                                    $cncel_rate = $cancel_policy['amount'];

                                    $converted_cancel_rate = $currency_obj->get_currency($cncel_rate, false, false, false, 1);

                                    if ($module == 'b2c') {

                                        $converted_min_rate_can_rate = $currency_obj->get_currency($cncel_rate, true, false, false, true, 1);

                                        $converted_cancel_rate = $converted_min_rate_can_rate['default_value'];
                                        $service_tax = (($converted_cancel_rate / 100) * $tax);
                                        $converted_cancel_rate = $converted_cancel_rate + $service_tax;
                                    } else {

                                        $converted_cancel_rate = $converted_cancel_rate['default_value'];
                                        $markup_total_fare_p = $converted_cancel_rate;
                                        $adminmarkup = $GLOBALS ['CI']->hotel_model->getadmin_markup();
                                        //debug($markup_total_fare_p);
                                        if ($adminmarkup['status'] == true) {
                                            //debug($adminmarkup);exit;
                                            $data = $adminmarkup['data'];
                                            foreach ($data as $k => $v) {
                                                $value_type = $v['value_type'];
                                                $admin_mark_value = $v['value'];

                                                if ($value_type == 'percentage') {
                                                    $markup_total_fare_ps = (($markup_total_fare_p / 100) * $admin_mark_value);
                                                    $markup_total_fare_p = ($markup_total_fare_p + $markup_total_fare_ps);
                                                } else if ($value_type == 'plus') {
                                                    $markup_total_fare_ps = ($markup_total_fare_p + $admin_mark_value);
                                                    $markup_total_fare_p = ($markup_total_fare_ps);
                                                } else {
                                                    $markup_total_fare_p = $markup_total_fare_p;
                                                }
                                            }
                                        }


                                        $AgentMarkupCan = $GLOBALS ['CI']->hotel_model->get_hotel_user_markup_details();
                                        if (isset($AgentMarkupCan['markup'][0])) {
                                            $AgentMarkup_TypeCan = $AgentMarkupCan['markup'][0];
                                            if ($AgentMarkup_TypeCan['value_type'] == "percentage") {
                                                $AgentMarkupAmount = (($markup_total_fare_p * $AgentMarkup_TypeCan['value']) / 100);
                                                $markup_total_fare_p+=$AgentMarkupAmount;
                                            } else {
                                                $AgentMarkupAmount = $AgentMarkup_TypeCan['value'];
                                                $markup_total_fare_p+=$AgentMarkupAmount;
                                            }
                                        }


                                        $converted_cancel_rate = $markup_total_fare_p;
                                        $service_tax = (($converted_cancel_rate / 100) * $tax);
                                        $converted_cancel_rate = $converted_cancel_rate + $service_tax;
                                    }

                                    $cncel_rate = $converted_cancel_rate;

                                    /* 	if(isset($converted_cancel_rate['default_value'])) {
                                      $cncel_rate = $converted_cancel_rate ;//$converted_cancel_rate['default_value'];
                                      } */
                                    //$markup_total_price = $currency_obj->get_currency ( $updated_room_details['data']['total_price'], true, true, true, 1 );

                                    $rates['cancellationPolicies'][$cel_key]['amount'] = $cncel_rate;
                                    //debug($rates['cancellationPolicies'][$cel_key]['amount']);
                                }
                            }
                            $new_arr[$rates['rooms'] . '_' . $rates['adults'] . '_' . $rates['children'] . '_' . @($rates['childrenAges'])][] = $rates;
                        }
                    }
                }
            }

            foreach ($new_arr as $k_key => $room_Detail_k) {
                $sorted_room_details[$k_key] = $this->array_sort($room_Detail_k, 'boardCode', SORT_DESC);
            }
            $rooms_list = $this->combintaion($sorted_room_details);
            $hotel_array['rooms'] = $rooms_list;

            /* credit cart payment option details */
            if (isset($hotel_details['creditCards']) && COUNT($hotel_details['creditCards']) > 0) {
                foreach ($hotel_details['creditCards'] as $crKey => $credit_card) {
                    $hotel_array['credit_card'][$crKey]['code'] = $credit_card['code'];
                    $hotel_array['credit_card'][$crKey]['name'] = $credit_card['name'];
                    $hotel_array['credit_card'][$crKey]['paymentType'] = $credit_card['paymentType'];
                }
            }

            /* ------------- get hotel static data from db ------------------- */
            $hotel_static_detils_arr = $this->get_static_hotel_info($hotelcode, '');

            if (isset($hotel_static_detils_arr['info'][$hotelcode]) && !empty($hotel_static_detils_arr['info'][$hotelcode])) {
                $hotel_array['chain'] = $hotel_static_detils_arr['info'][$hotelcode]['chain'];
                $hotel_array['category_code'] = $hotel_static_detils_arr['info'][$hotelcode]['category_code'];
                $hotel_array['address'] = $hotel_static_detils_arr['info'][$hotelcode]['address'];
                $hotel_array['postal'] = $hotel_static_detils_arr['info'][$hotelcode]['postal'];
                $hotel_array['email'] = $hotel_static_detils_arr['info'][$hotelcode]['email'];
                $hotel_array['website'] = $hotel_static_detils_arr['info'][$hotelcode]['website'];
                $hotel_array['description'] = $hotel_static_detils_arr['info'][$hotelcode]['description'];
            }

            /* get hotel images */
            $hotel_static_images_arr = $this->get_static_hotel_images($hotelcode);
             // 89458
            $hotel_array['image_arr'] = isset($hotel_static_images_arr) && COUNT($hotel_static_images_arr) > 0 ? $hotel_static_images_arr : '';
            $hotel_array['medium_image_baseUrl'] = 'http://photos.hotelbeds.com/giata/';
            $hotel_array['small_image_baseUrl'] = 'http://photos.hotelbeds.com/giata/small/';

            /* get hotel facilities with desc */
            $hotel_static_facilities_arr = $this->get_static_hotel_facility_info($hotelcode);
           // debug($hotel_static_facilities_arr);exit;
            $hotel_array['hotel_static_facilities_arr'] = isset($hotel_static_facilities_arr['facility'][$hotelcode]) && COUNT($hotel_static_facilities_arr['facility'][$hotelcode]) > 0 ? $hotel_static_facilities_arr['facility'][$hotelcode] : '';

            /* get hotel contact details */
            $hotel_static_contact_num = $this->get_static_hotel_contact_info($hotelcode);
            $hotel_array['hotel_static_contact_num'] = isset($hotel_static_contact_num) && COUNT($hotel_static_contact_num) > 0 ? $hotel_static_contact_num : '';

            /* get nearest terminal distance */
            $terminal = $this->get_nearest_terminal($hotelcode);
            if (isset($terminal) && !empty($terminal)) {
                $hotel_array['hotel_terminalCode'] = $terminal->hotel_terminalCode;
                $hotel_array['hotel_distance'] = $terminal->hotel_distance;
            }
        }
        //debug($hotel_array);exit;
        return $hotel_array;
    }

    /* sort array alphabetically */

    private function array_sort($array, $on, $order = SORT_ASC) {
        $new_array = array();
        $sortable_array = array();

        if (count($array) > 0) {
            foreach ($array as $k => $v) {

                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                    break;
                case SORT_DESC:
                    arsort($sortable_array);
                    break;
            }

            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }

        return $new_array;
    }

    private function combintaion($room_list) {
        $array = $room_list;
        $total_room_count = count($array);

        function expand($sofar, $rest, &$result, $total_room_count) {

            if (empty($rest))
                return;

            // get tag name and possible values
            reset($rest);
            $tag = key($rest);
            $values = array_shift($rest);

            // loop through tag's values
            foreach ($values as $value) {

                // prepare new result using $sofar with new tag value added
                $subresult = $sofar;
                $subresult[$tag] = $value;
                if (count($subresult) == $total_room_count) {
                    $result[] = $subresult;
                }

                // continue expansion of next tag
                expand($subresult, $rest, $result, $total_room_count);
            }
        }

        $result = array();
        expand(array(), $array, $result, $total_room_count);

        return $result;
    }

    /**
     * Jaganath
     * Check the Room Rates, If rateType is RECHECK
     */
    public function check_room_rate($hotel_code, $rate_keys, $currency, $search_id = '', $module) {
        $response['status'] = false;
        $response['data'] = array();
        $check_room_rate_request = $this->check_room_rate_request_post($rate_keys);
        //debug($check_room_rate_request);exit;
        $this->CI->custom_db->generate_static_response($check_room_rate_request ['data'] ['request'], 'HB check room rate');

        if ($check_room_rate_request ['status']) {
            $check_room_rate_response = $GLOBALS ['CI']->api_interface->post_rest_service($check_room_rate_request ['data'] ['service_url'], $check_room_rate_request ['data'] ['request'], $this->json_header());
            $GLOBALS ['CI']->custom_db->generate_static_response(json_encode($check_room_rate_response), 'HB check room rate response');

//			$check_room_rate_response = json_decode($check_room_rate_response, true);
            //$check_room_rate_response = $GLOBALS ['CI']->custom_db->get_static_response (1607);//1607
            //20160518|20160520|W|164|91394|APT.B2|GR-ALL|CB||2~3~0||N@811722027

            if ($this->valid_check_rate_details($check_room_rate_response)) {

                $hotel_details_array = $this->get_formatted_checkprice_roomdetails($check_room_rate_response, $currency, $search_id = '', $module);

                $response ['data'] = $hotel_details_array;
                $response ['status'] = true;
            } else {
                // Need the complete data so that later we can use it for redirection
                $response ['data'] = $check_room_rate_response;
            }
        }
//		debug($response);exit;
        return $response;
    }

    /**
     * get hotel room rate comment
     * 
     * @param array $rooms
     * @param string $room_rate_Keys
     * @param date $from_date
     * @param date $to_date
     * 
     * @return array 
     */
    function get_rate_comment_details($rooms, $room_rate_Keys, $from_date, $to_date) {
        $roomCommentId = array();
        $roomCommentId_arr = array();
        /* get ratecomment id of selected rooms */
        foreach ($rooms as $k => $val) {
            foreach ($val as $rKey => $rate) {
                if (!empty($rate['rateCommentsId']) && in_array($rate['rateKey'], $room_rate_Keys) && !in_array($rate['rateCommentsId'], $roomCommentId_arr)) {
                    $roomCommentId_arr[] = $rate['rateCommentsId'];
                    $roomCommentId[] = array(
                        'rateKey' => $rate['rateKey'],
                        'rateCommentsId' => $rate['rateCommentsId']
                    );
                }
            }
        }
        $commentArr = '';
        if (isset($roomCommentId) && valid_array($roomCommentId)) {

            $from_date = date('Y-m-d', strtotime($from_date));

            foreach ($roomCommentId as $rKey => $comment) {
                $check_room_rate_comment_request = $this->get_rate_comment_details_request($comment['rateCommentsId'], $from_date);
                $this->CI->custom_db->generate_static_response($check_room_rate_comment_request ['data'] ['service_url'], 'HB ratecomment request');
                $details = $GLOBALS ['CI']->api_interface->get_json_response_getrequest($check_room_rate_comment_request ['data'] ['service_url'], $this->json_header());
                $this->CI->custom_db->generate_static_response($details, 'HB ratecomment response');
                $facilities_array = json_decode($details, TRUE);
                if (isset($facilities_array['rateComments'])) {
                    $incoming = isset($facilities_array['incoming']) && !empty($facilities_array['incoming']) ? $facilities_array['incoming'] : '';
                    $hotel = isset($facilities_array['hotel']) && !empty($facilities_array['hotel']) ? $facilities_array['hotel'] : '';
                    $rateComments = isset($facilities_array['rateComments']) && !empty($facilities_array['rateComments']) ? $facilities_array['rateComments'] : '';
                    $code = isset($facilities_array['code']) && !empty($facilities_array['code']) ? $facilities_array['code'] : '';
                    $date = isset($facilities_array['date']) && !empty($facilities_array['date']) ? $facilities_array['date'] : '';
                    $commentArr[] = array(
                        'incoming' => $incoming,
                        'hotel' => $hotel,
                        'rateComments' => $rateComments,
                        'code' => $code,
                        'date' => $date
                    );
                }
            }
        }

        return $commentArr;
    }

    /**
     * Arjun J Gowda
     * Block Room Before Going for payment and showing final booking page to user - TBO rule
     *
     * @param array $pre_booking_params
     *        	All the necessary data required in block room request - fetched from roomList and hotelDetails Request
     */
    function block_room($pre_booking_params) {

        $response ['status'] = false;
        $response ['data'] = array();
        $search_data = $this->search_data($pre_booking_params ['search_id']);
        $run_block_room_request = true;
        $block_room_request_count = 0;
        $pre_booking_params ['search_data'] = $search_data ['data'];
        $block_room_request = $this->get_block_room_request($pre_booking_params);
        // debug($pre_booking_params);exit;
        $application_default_currency = get_application_default_currency();
        if ($block_room_request ['status'] == ACTIVE) {
            while ($run_block_room_request) {
                $GLOBALS ['CI']->custom_db->generate_static_response(json_encode($block_room_request ['data'] ['request']));
                $block_room_response = $GLOBALS ['CI']->api_interface->get_json_response($block_room_request ['data'] ['service_url'], $block_room_request ['data'] ['request'], $header);
                $GLOBALS ['CI']->custom_db->generate_static_response(json_encode($block_room_response)); // release this

                /* $static_search_result_id = 309;//161;//184;//169;//197; -- SINGLE */
                /*
                 * $static_search_result_id = 815;//202;// -- MULTIPLE
                 * $block_room_response = $GLOBALS['CI']->hotel_model->get_static_response($static_search_result_id); //cmt this
                 */

                if ($this->valid_response($block_room_response ['BlockRoomResult'] ['ResponseStatus']) == false) {
                    $run_block_room_request = false;
                    $response ['status'] = false; // Indication for room block
                    $response ['data'] ['msg'] = 'Some Problem Occured. Please Search Again to continue';
                } elseif ($this->is_room_blocked($block_room_response) == true) {
                    $run_block_room_request = false;
                    $response ['status'] = true; // Indication for room block
                } else {
                    // UPDATE RECURSSION
                    $_HotelRoomsDetails = get_room_index_list($block_room_response ['BlockRoomResult'] ['HotelRoomsDetails']);
                    // Reset pre booking params token and get new values
                    $dynamic_params_url = '';
                    foreach ($_HotelRoomsDetails as $___tk => $___tv) {
                        $dynamic_params_url [] = get_dynamic_booking_parameters($___tk, $___tv, $application_default_currency);
                    }
                    // update token key
                    $pre_booking_params ['token'] = $dynamic_params_url;
                    $pre_booking_params ['token_key'] = md5(serialized_data($dynamic_params_url));
                    $block_room_request = $this->get_block_room_request($pre_booking_params);
                }
                $block_room_request_count++; // Increment number of times request is run
                if ($block_room_request_count == 3 && $run_block_room_request == true) {
                    // try max 3times to block the room
                    $run_block_room_request = false;
                }
            }
            $response ['data'] ['response'] = $block_room_response;
        }
        return $response;
    }

    /**
     *
     * @param array $booking_params
     */
    function process_booking($book_id, $booking_params) {

        $response ['status'] = FAILURE_STATUS;
        $response ['data'] = array();
        //debug($book_id);exit;
        //$book_request = $this->get_book_request ( $booking_params, $book_id );
        $book_request = $this->booking_xml_request($booking_params, $book_id);
        $block_data_array = $book_request ['data'] ['request'];
        if ($book_request['status'] == true) {

            $GLOBALS ['CI']->custom_db->generate_static_response ($book_request ['data'] ['request'], 'REZLIVE hotel booking request' );
            // get the response for hotel bookings
             $book_response = $GLOBALS ['CI']->api_interface->xml_post_request($book_request ['data'] ['service_url'], ('XML='.urlencode($book_request ['data'] ['request'])), $this->xml_header());
            
            $GLOBALS ['CI']->custom_db->generate_static_response ($book_response, 'REZLIVE hotel booking response' );

            
            $book_response = utf8_encode($book_response);

            $book_response = Converter::createArray($book_response);

// 			debug($book_response);exit;
            /**    PROVAB LOGGER * */
            // $GLOBALS['CI']->private_management_model->provab_xml_logger('Book_Room', $book_id, 'hotel', $block_data_array, json_encode($book_response));
            if (isset($book_response['BookingResponse']['BookingDetails']) == true && valid_array($book_response['BookingResponse']['BookingDetails']) == true && $book_response['BookingResponse']['BookingDetails']['BookingStatus'] == 'Confirmed') {
                $response['status'] = SUCCESS_STATUS;
                // $book_response = $this->format_booking_response($book_response);
                $response['data']['book_response'] = $book_response;
                $response['data']['booking_params'] = $booking_params;
                $response['data']['room_book_data'] = ''; //FIXME:its an XML
            }
        }
        $response['data']['booking_billing_type'] = $booking_params["selected_pm"];
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
    function save_booking($app_booking_id, $params, $module = '') {
        // Need to return following data as this is needed to save the booking fare in the transaction details
        error_reporting(0);
        $response ['fare'] = $response ['domain_markup'] = $response ['level_one_markup'] = 0;
        $book_response = $params['book_response'];

        $booking_params = $params['booking_params'];

        // echo "=====================================================================";

        $room_details = $booking_params['token']['token_data']['rooms'][$booking_params['token']['rateKey'][0]];



        // debug($booking_params['token']['token']['data']['convenience_fee']);exit;


        if (isset($params['booking_params']['token']['token']['data']['convenience_fee'])) {
            $ConCharge = $params['booking_params']['token']['token']['data']['convenience_fee'];
        } else {
            $ConCharge = 0;
        }

        $rate_details = $room_details['rates'];

       /* $meal_type = array();
        foreach ($rate_details as $room => $v) {
            $meal_type[] = $v['boardName'];
        }*/





        // $hotel_details = $book_response['hotel'];
        $hotel_details = $booking_params['token']['token_data'];
        //$room_details = $hotel_details['rooms'];
        //debug($params['booking_params']['token']['token']['data']['total_price']);exit;
        $domain_origin = get_domain_auth_id();
        $master_search_id = $params ['booking_params'] ['token'] ['search_id'];
        $search_data = $this->search_data($master_search_id);
        $status = $this->get_booking_status($book_response['BookingResponse']['BookingDetails']['BookingStatus']);
        // debug($status);
        $app_reference = $app_booking_id;
        $booking_source = $params ['booking_params'] ['token'] ['booking_source'];
        /*$booking_id = $book_response['BookingResponse']['BookingDetails']['BookingId'];
        $confirmation_reference = $book_response['BookingResponse']['BookingDetails']['BookingCode']; //Not Getting In Hotelbeds
        // $booking_reference = $book_response['reference'];
        $booking_reference = $confirmation_reference;*/
        $booking_reference = $book_response['BookingResponse']['BookingDetails']['BookingId'];
        $booking_id = $book_response['BookingResponse']['BookingDetails']['BookingCode']; //Not Getting In Hotelbeds
        // $booking_reference = $book_response['reference'];
        $confirmation_reference = $booking_id;
        $no_of_nights = intval($search_data ['data'] ['no_of_nights']);
        // $HotelRoomsDetails = force_multple_data_format($booking_params['token']['token_data']['rooms'][$booking_params['token']['rateKey'][0]]);
        $HotelRoomsDetails = $booking_params['token']['token_data']['rooms'][$booking_params['token']['rateKey'][0]];
        // debug($HotelRoomsDetails);
         // echo 'hotel';debug($booking_params['token']['token_updated_data']['data']['pre_booking_request_data']['RoomDetails']['RoomDetail']['TermsAndConditions']['@cdata']);exit();
        $total_room_count = intval($book_response['BookingResponse']['BookingRequest']['Booking']['RoomDetails']['RoomDetail']['TotalRooms']);
// debug($booking_params['token']['token_data']['cancellationString']);
        // add updated data to array
        $HotelRoomsDetails['rates']['cancellationPolicies'] = $booking_params['token']['token_data']['cancellationString'];
        $HotelRoomsDetails['net'] = $book_response['BookingResponse']['BookingDetails']['BookingPrice'];
/*debug($HotelRoomsDetails);
debug($params);exit();*/
        $book_total_fare = $book_response['BookingResponse']['BookingDetails']['BookingPrice'];
        $totalNet = $book_total_fare;
        //convert price to inr
        //$book_total_fare = $book_response['totalNet'];
        //debug($book_total_fare);exit;

        $currency = $book_response['BookingResponse']['BookingDetails']['BookingCurrency'];
        $currency_obj = $params ['currency_obj'];

        $currency_obj = new Currency(array('module_type' => 'hotel', 'from' => $currency, 'to' => get_application_transaction_currency_preference()));
        $deduction_cur_obj = clone $currency_obj;
        if ($module == 'b2c') {
            $markup_total_fare = $currency_obj->get_currency ( $book_total_fare, true, false, true, $no_of_nights * $total_room_count ); // (ON Total PRICE ONLY)
            $ded_total_fare = $deduction_cur_obj->get_currency ( $book_total_fare, true, true, false, $no_of_nights * $total_room_count ); // (ON Total PRICE ONLY)
            
            $admin_markup = sprintf ( "%.2f", $markup_total_fare ['default_value'] - $ded_total_fare ['default_value'] );
            $book_total_fare = $deduction_cur_obj->get_currency ( $book_total_fare, false, false, false, 1 );
            $agent_markup = sprintf ( "%.2f", $ded_total_fare ['default_value'] - $book_total_fare['default_value'] );
        } else {
            // B2B Calculation
            $markup_total_fare = $currency_obj->get_currency ( $book_total_fare, true, true, true, $no_of_nights * $total_room_count ); // (ON Total PRICE ONLY)
            $ded_total_fare = $deduction_cur_obj->get_currency ( $book_total_fare, true, false, true, $no_of_nights * $total_room_count ); // (ON Total PRICE ONLY)
            $admin_markup = sprintf ( "%.2f", $markup_total_fare ['default_value'] - $ded_total_fare ['default_value'] );
            $book_total_fare = $deduction_cur_obj->get_currency ( $book_total_fare, false, false, false, 1 );
            $agent_markup = sprintf ( "%.2f", $ded_total_fare ['default_value'] - $book_total_fare['default_value'] );
        }

        // old
        /*$currency_obj->getConversionRate(false, $currency, get_application_transaction_currency_preference());
        $currency_conversion_rate = $currency_obj->conversion_rate;*/
        $currency_conversion_rate = $currency_obj->transaction_currency_conversion_rate ();

        $xd = $params['booking_params']['token']['token']['data']['total_price'];
        $service_tax_amt = round($params['booking_params']['token']['token']['data']['service_tax']);
        $markup_amt = @round($params['booking_params']['token']['token']['data']['AdminMarkup']);
        $AgentMarkup = @round($params['booking_params']['token']['token']['data']['Agentmarkup']);
        $AgentPrice = @round($params['booking_params']['token']['token']['data']['AgentPrice']);
        $XMLPrice = round($params['booking_params']['token']['token']['data']['XMLPrice']);
        $customer_payable = number_format(($params['booking_params']['token']['token']['data']['customer_payable']),2);
        $convenience_fee = $ConCharge;


        // $totalNet = round($params['booking_params']['token']['token']['data']['fare']);

        //  $book_total_fare = $currency_obj->get_currency($totalNet, false, false, false, 1); // (ON Total PRICE ONLY)
        //  $book_total_fare['default_currency']=$totalNet;



        $currency_val = $currency_obj->to_currency;
        $admin_markup = 0;
        $agent_markup = 0;

        $terminal = $params ['booking_params'] ['token']['hterminal'];
        $postal = $params ['booking_params'] ['token']['postal'];
        $hemail = $params ['booking_params'] ['token']['hemail'];
        $haddress = $params ['booking_params'] ['token']['haddress'] . '' . $postal;

        $hotel_name = $hotel_details['hotel_name'];
        $star_rating = intval($hotel_details['star_rating']);
        $hotel_code = $hotel_details['hotel_code'];
        $phone_number = $params ['booking_params'] ['passenger_contact'];
        $alternate_number = 'NA';
        $email = $params ['booking_params'] ['billing_email'];
        $hotel_check_in = db_current_datetime(str_replace('/', '-', $search_data ['data'] ['from_date']));
        $hotel_check_out = db_current_datetime(str_replace('/', '-', $search_data ['data'] ['to_date']));
        $payment_mode = $params ['booking_params'] ['payment_method']; //debug($book_total_fare['default_value']);exit;
        //$total_amount = $amt;
        $convinence_value = @$params['booking_params']['token']['token']['convenience_fees'];


       /* $country_name = $GLOBALS ['CI']->db_cache_api->get_country_list(array(
            'k' => 'origin',
            'v' => 'name'
                ), array(
            'origin' => $params ['booking_params'] ['billing_country']
                ));*/
         $country_name = $GLOBALS ['CI']->db_cache_api->get_country_list ( array (
                'k' => 'origin',
                'v' => 'name' 
                ), array (
                'country_code' => $params ['booking_params'] ['billing_country'] 
                ) );

        $attributes = array(
            'address' => @$params ['booking_params'] ['billing_address_1'],
            'billing_country' => @$country_name [key($country_name)],
            // 'billing_country' => @$country_name [$params ['booking_params'] ['billing_country']],
            // 'billing_city' => $city_name[$params['booking_params']['billing_city']],
            'billing_city' => @$params ['booking_params'] ['billing_city'],
            'billing_zipcode' => @$params ['booking_params'] ['billing_zipcode'],
            'HotelCode' => $hotel_details['hotel_code'],
            'search_id' => @$params ['booking_params'] ['token'] ['search_id'],
            'HotelName' => $hotel_details['hotel_name'],
            'StarRating' => intval($hotel_details['star_rating']),
            'categoryCode' => @$hotel_details['categoryCode'],
            'totalSellingRate' => @$book_response['BookingResponse']['BookingDetails']['BookingPrice'],
            'totalNet' => $totalNet,
            'pendingAmount' => @$book_response['pendingAmount'],
            'currency' => $currency_val,
            'supplier' => @$hotel_details['supplier'],
            'latitude' => $hotel_details['latitude'],
            'longitude' => $hotel_details['longitude'],
            'HotelAddress' => $book_response['BookingResponse']['BookingRequest']['Booking']['Address'],
            'HotelImage' => @$params ['booking_params'] ['token']['token_data'] ['imagePath'],
            'hotel_image' => @$params ['booking_params'] ['token']['token_data'] ['imagePath']
        );
        // debug($attributes);exit;
        $created_by_id = intval(@$GLOBALS ['CI']->entity_user_id);
        $transaction_currency = get_application_currency_preference ();
        // SAVE Booking details
        // $GLOBALS ['CI']->hotel_model->save_booking_details($domain_origin, $status, $app_reference, $booking_source, $booking_id, $booking_reference, $confirmation_reference, $hotel_name, $star_rating, $hotel_code, $phone_number, $alternate_number, $email, $hotel_check_in, $hotel_check_out, $payment_mode, json_encode($attributes), $created_by_id, $currency_val, $currency_conversion_rate, $haddress, $hemail, $convenience_fee);
        $booking_billing_type = $booking_params['selected_pm'];
        // SAVE Booking details
        $GLOBALS ['CI']->hotel_model->save_booking_details ( $domain_origin, $status, $app_reference, $booking_source, $booking_id, $booking_reference, $confirmation_reference, $hotel_name, $star_rating, $hotel_code, $phone_number, $alternate_number, $email, $hotel_check_in, $hotel_check_out, $payment_mode, json_encode ( $attributes ), $created_by_id, $transaction_currency, $currency_conversion_rate, $booking_billing_type);
        
        $check_in = db_current_datetime(str_replace('/', '-', $search_data ['data'] ['from_date']));
        $check_out = db_current_datetime(str_replace('/', '-', $search_data ['data'] ['to_date']));

        $location = $search_data ['data'] ['location'];
        //debug($location);exit;
        // loop token of token
        array_column($this->format_room_list($room_details), 'net');


        
        $total_rooms = intval($book_response['BookingResponse']['BookingRequest']['Booking']['RoomDetails']['RoomDetail']['TotalRooms']);
        $room_type_name = $booking_params['token']['token_data']['rooms'][$booking_params['token']['rateKey'][0]]['room_name'];
        // old
        // $bed_type_code = explode('|', $book_response['BookingResponse']['BookingRequest']['Booking']['RoomDetails']['RoomDetail']['Type']);
        // new
        $bed_type_code = (!empty(@$HotelRoomsDetails['rates']['boardName']) == true ) ? $HotelRoomsDetails['rates']['boardName']: 'ROOM ONLY';
        $attributes = array(
                'rates' => $HotelRoomsDetails['rates'],
                'meal_type' => $HotelRoomsDetails['rates']['boardName'],
                'rate_comment' => $booking_params['token']['token_updated_data']['data']['pre_booking_request_data']['RoomDetails']['RoomDetail']['TermsAndConditions']['@cdata']
            );


        $attributes = json_encode($attributes);
        $room_price = $total_fare = $totalNet;
        // $room_type_name = $v ['name'];
        for ($rm=0; $rm < $total_rooms; $rm++) { 
        $smoking_preference = 'N/A';
        // $prc = explode('|', $booking_params['token']['token_data']['rooms'][$booking_params['token']['rateKey'][0]]['rates']['net']);
        // $total_fare = $prc[$rm];
        // $room_price =  $prc[$rm];
        $prc =  $booking_params['token']['token_updated_data']['data']['pre_booking_raw_data']['BookingAfterPrice']/ $booking_params['token']['token_updated_data']['data']['pre_booking_request_data']['RoomDetails']['RoomDetail']['TotalRooms'];
        $total_fare = $prc;
        $room_price =  $prc;
            
            if ($module == 'b2c') {
                $markup_total_fare = $currency_obj->get_currency ( $total_fare, true, false, true, $no_of_nights ); // (ON Total PRICE ONLY)
                $ded_total_fare = $deduction_cur_obj->get_currency ( $total_fare, true, true, false, $no_of_nights ); // (ON Total PRICE ONLY)
                $admin_markup = sprintf ( "%.2f", $markup_total_fare ['default_value'] - $ded_total_fare ['default_value'] );
                $agent_markup = sprintf ( "%.2f", $ded_total_fare ['default_value'] - $total_fare );
            } else {
                // B2B Calculation - Room wise price
                $markup_total_fare = $currency_obj->get_currency ( $total_fare, true, true, true, $no_of_nights ); // (ON Total PRICE ONLY)
                $ded_total_fare = $deduction_cur_obj->get_currency ( $total_fare, true, false, true, $no_of_nights ); // (ON Total PRICE ONLY)
                $admin_markup = sprintf ( "%.2f", $markup_total_fare ['default_value'] - $ded_total_fare ['default_value'] );
                $agent_markup = sprintf ( "%.2f", $ded_total_fare ['default_value'] - $total_fare );
            }

             /*$GLOBALS ['CI']->hotel_model->save_booking_itinerary_details ( $app_reference, $location, $check_in, $check_out, $room_type_name, $bed_type_code[$rm], $status, $smoking_preference, $book_total_fare['default_value'], $admin_markup, $agent_markup, $currency, $attributes, $room_price, @$HotelRoomsDetails ['Tax'], @$HotelRoomsDetails ['ExtraGuestCharge'], @$HotelRoomsDetails ['ChildCharge'], @$HotelRoomsDetails ['OtherCharges'], @$HotelRoomsDetails ['Discount'], @$HotelRoomsDetails ['ServiceTax'], @$HotelRoomsDetails ['AgentCommission'], @$HotelRoomsDetails ['AgentMarkUp'], @$HotelRoomsDetails ['TDS'] );*/
             $GLOBALS ['CI']->hotel_model->save_booking_itinerary_details ( $app_reference, $location, $check_in, $check_out, $room_type_name, $bed_type_code[$rm], $status, $smoking_preference, $total_fare, $admin_markup, $agent_markup, $currency, $attributes, $room_price, @$HotelRoomsDetails ['Tax'], @$HotelRoomsDetails ['ExtraGuestCharge'], @$HotelRoomsDetails ['ChildCharge'], @$HotelRoomsDetails ['OtherCharges'], @$HotelRoomsDetails ['Discount'], @$HotelRoomsDetails ['ServiceTax'], @$HotelRoomsDetails ['AgentCommission'], @$HotelRoomsDetails ['AgentMarkUp'], @$HotelRoomsDetails ['TDS'] );
        }


        $paxes = $book_response['BookingResponse']['BookingRequest']['Booking']['RoomDetails']['RoomDetail']['Guests'] ;
        /*if($total_rooms > 1){
            
        } else {

        }*/
        // debug($paxes);
         $passengers = force_multple_data_format($paxes);
            if (valid_array($passengers) == true ) {
                foreach ($passengers as $p_k => $p_v) {
                    $guests = force_multple_data_format($p_v['Guest']);
                    // debug($p_v);exit;
                    foreach ($guests as $key => $pxv) {

                        // $id = $v['id'];
                        // $room_id = $p_v['roomId'];
                        $title = $pxv['Salutation'];
                        $first_name = $pxv ['FirstName'];
                        $middle_name = '';
                        $last_name = $pxv ['LastName'];
                        $phone = $params['booking_params']['passenger_contact'];
                        $email = $params['booking_params']['billing_email'];
                        $pax_type = array_shift($params['booking_params']['passenger_type']);
                        $pax_type = ($pax_type == 'CH' ? 'Child' : 'Adult');
                        $age = 0;
                        if(isset($pxv['IsChild']) && ($pxv['IsChild'] == 1)) {
                            $age = intval($pxv['Age']);
                        }                        
                        $date_of_birth = '';
                        $passenger_nationality = 'Indian';
                        $passport_issuing_country = 'India';
                        $passport_number = '';
                        $passport_expiry_date = '';
                        $attributes = array();
                        // SAVE Booking Pax details
                        $GLOBALS ['CI']->hotel_model->save_booking_pax_details(
                                $app_reference, $title, $first_name, $middle_name, $last_name, $phone, $email, $pax_type, $date_of_birth, $passenger_nationality, $passport_number, $passport_issuing_country, $passport_expiry_date, $status, json_encode($attributes), $age);
                    }

                }                
            }
           

        /*foreach ($HotelRoomsDetails as $k => $v) {

            $currency_obj = new Currency(array('module_type' => 'hotel', 'from' => $currency, 'to' => get_application_transaction_currency_preference()));

            //  echo 'Keys',$k;
}
//echo 'INSD',debug($IndividualRoomPriceInfo['rates'][$k]);

            $room_type_name = $v ['name'];
            $id = $v['id'];
            $bed_type_code = $v ['code'];
            $smoking_preference = 'N/A';
            $status = get_booking_status($v['status']);
            $room_price = $total_fare = array_shift($room_price_details);

            $admin_room_markup = 0;
            $agent_markup_r = 0;
            $total_fare_r = $currency_obj->get_currency($total_fare, false, false, false, 1);



            $attributes = array(
                'rates' => $v['rates'],
                'meal_type' => $meal_type
            );


            $attributes = json_encode($attributes);


            if (count($booking_params['token']['token']['data']['rooms']) > 1) {

                $IndividualRoomPriceInfo = $booking_params['token']['token']['data']['rooms'];
                $save_booking_itinerary_details = array(
                    'app_reference' => $app_reference,
                    'location' => $location,
                    'check_in' => $check_in,
                    'check_out' => $check_out,
                    'room_type_name' => $room_type_name,
                    'bed_type_code' => $bed_type_code,
                    'status' => $status,
                    'smoking_preference' => $smoking_preference,
                    'total_fare' => @$IndividualRoomPriceInfo[$k]['rates'][0]['net'],
                    'customer_payable' => @$IndividualRoomPriceInfo[$k]['rates'][0]['net'],
                    'admin_markup' => @$IndividualRoomPriceInfo[$k]['rates'][0]['IndividualRoomAdminMarkup'],
                    'agent_markup' => @$IndividualRoomPriceInfo[$k]['rates'][0]['IndividualRoomAgentMarkup'],
                    'XMLPrice' => @$IndividualRoomPriceInfo[$k]['rates'][0]['IndividualRoomXMLRate'],
                    'AgentPrice' =>@$IndividualRoomPriceInfo[$k]['rates'][0]['IndividualRoomAgentPrice'],
                    'currency' => $currency_val,
                    'attributes' => $attributes,
                    'RoomPrice' => $room_price,
                    'Tax' => @$v ['Tax'],
                    'ExtraGuestCharge' => @$v ['ExtraGuestCharge'],
                    'ChildCharge' => @$v ['ChildCharge'],
                    'OtherCharges' => @$v ['OtherCharges'],
                    'Discount' => @$v ['Discount'],
                    'ServiceTax' => @$IndividualRoomPriceInfo[$k]['rates'][0]['IndividualRoomServiceTax'],
                    'AgentServiceTax' => @$IndividualRoomPriceInfo[$k]['rates'][0]['IndividualRoomAgentServiceTax'],
                    'AgentCommission' => @$v ['AgentCommission'],
                    'AgentMarkUp' => @$IndividualRoomPriceInfo[$k]['rates'][0]['IndividualRoomAgentMarkup'],
                    'TDS' => @$v ['TDS'],
                    'convenience_fee' => $booking_params['token']['token']['data']['convenience_fee'],
                    'id' => $id
                );
            } else {

                $IndividualRoomPriceInfo = $booking_params['token']['token']['data']['rooms'][0];
                //   debug($IndividualRoomPriceInfo);exit;
                $save_booking_itinerary_details = array(
                    'app_reference' => $app_reference,
                    'location' => $location,
                    'check_in' => $check_in,
                    'check_out' => $check_out,
                    'room_type_name' => $room_type_name,
                    'bed_type_code' => $bed_type_code,
                    'status' => $status,
                    'smoking_preference' => $smoking_preference,
                    'total_fare' => @$IndividualRoomPriceInfo['rates'][$k]['net'],
                    'customer_payable' => @$IndividualRoomPriceInfo['rates'][$k]['net'],
                    'admin_markup' => @$IndividualRoomPriceInfo['rates'][$k]['IndividualRoomAdminMarkup'],
                    'agent_markup' => @$IndividualRoomPriceInfo['rates'][$k]['IndividualRoomAgentMarkup'],
                    'XMLPrice' => @$IndividualRoomPriceInfo['rates'][$k]['IndividualRoomXMLRate'],
                    'AgentPrice' => @$IndividualRoomPriceInfo['rates'][$k]['IndividualRoomAgentPrice'],
                    'currency' => $currency_val,
                    'attributes' => $attributes,
                    'RoomPrice' => $room_price,
                    'Tax' => @$v ['Tax'],
                    'ExtraGuestCharge' => @$v ['ExtraGuestCharge'],
                    'ChildCharge' => @$v ['ChildCharge'],
                    'OtherCharges' => @$v ['OtherCharges'],
                    'Discount' => @$v ['Discount'],
                    'ServiceTax' => @$IndividualRoomPriceInfo['rates'][$k]['IndividualRoomServiceTax'],
                    'AgentServiceTax' => @$IndividualRoomPriceInfo['rates'][$k]['IndividualRoomAgentServiceTax'],
                    'AgentCommission' => @$v ['AgentCommission'],
                    'AgentMarkUp' => @$IndividualRoomPriceInfo['rates'][$k]['IndividualRoomAgentMarkup'],
                    'TDS' => @$v ['TDS'],
                    'convenience_fee' => $booking_params['token']['token']['data']['convenience_fee'],
                    'id' => $id
                );
            }




            // old
            // $GLOBALS ['CI']->hotel_model->save_booking_itinerary_details($save_booking_itinerary_details);

            // new
            // $app_reference, $location, $check_in, $check_out, $room_type_name, $bed_type_code, $status, $smoking_preference, $total_fare, $admin_markup, $agent_markup, $currency, $attributes, $RoomPrice, $Tax, $ExtraGuestCharge, $ChildCharge, $OtherCharges, $Discount, $ServiceTax, $AgentCommission, $AgentMarkUp, $TDS
            // $GLOBALS ['CI']->hotel_model->save_booking_itinerary_details ( $app_reference, $location, $check_in, $check_out, $room_type_name, $bed_type_code, $status, $smoking_preference, $book_total_fare['default_value'], $admin_markup, $agent_markup, $currency, $attributes, $room_price, @$HotelRoomsDetails ['Tax'], @$HotelRoomsDetails ['ExtraGuestCharge'], @$HotelRoomsDetails ['ChildCharge'], @$HotelRoomsDetails ['OtherCharges'], @$HotelRoomsDetails ['Discount'], @$HotelRoomsDetails ['ServiceTax'], @$HotelRoomsDetails ['AgentCommission'], @$HotelRoomsDetails ['AgentMarkUp'], @$HotelRoomsDetails ['TDS'] );


            $passengers = force_multple_data_format($v ['paxes']);
            if (valid_array($passengers) == true) {
                foreach ($passengers as $p_k => $p_v) {
                    $id = $v['id'];
                    $room_id = $p_v['roomId'];
                    $title = get_enum_list('title', array_shift($params ['booking_params'] ['name_title']));
                    $first_name = $p_v ['name'];
                    $middle_name = '';
                    $last_name = $p_v ['surname'];
                    $phone = $params['booking_params']['passenger_contact'];
                    $email = $params['booking_params']['billing_email'];
                    $pax_type = array_shift($params['booking_params']['passenger_type']);
                    $pax_type = ($pax_type == 'CH' ? 'Child' : 'Adult');
                    $age = @$p_v['age'];
                    $date_of_birth = '';
                    $passenger_nationality = 'Indian';
                    $passport_issuing_country = 'India';
                    $passport_number = '';
                    $passport_expiry_date = '';
                    $attributes = array();
                    // SAVE Booking Pax details
                    $GLOBALS ['CI']->hotel_model->save_booking_pax_details(
                            $app_reference, $title, $first_name, $middle_name, $last_name, $phone, $email, $pax_type, $date_of_birth, $passenger_nationality, $passport_number, $passport_issuing_country, $passport_expiry_date, $status, json_encode($attributes), $age);
                }
            }

            
            
            
            $cobj = new Currency(array('module_type' => 'hotel', 'from' => get_application_default_currency(), 'to' => get_application_display_currency_preference()));
            if (isset($v['rates']['cancellationPolicies']['cancellationPolicy']) && valid_array($v['rates']['cancellationPolicies']['cancellationPolicy'])) {
                
                

                // debug($v['rates']['cancellationPolicies']['cancellationPolicy'][0]);
                //  foreach ($v['rates']['cancellationPolicies']['cancellationPolicy'] as $cn_k => $cncel_p) {

                $markup_cncel_fare_r = $currency_obj->get_currency($v['rates']['cancellationPolicies']['cancellationPolicy'][0]['amount'], false, false, false, 1);

                $markup_cncel_fare_r_Amount = $markup_cncel_fare_r['default_value'];

                $cancellationPolicy=$v['rates']['cancellationPolicies']['cancellationPolicy'];




                $adminmarkupCANA = $GLOBALS ['CI']->hotel_model->getadmin_markup();

                # Admin Markup Calculations For Rooms
                if ($adminmarkupCANA['status'] == true) {
                    //debug($adminmarkup);exit;
                    $data = $adminmarkupCANA['data'];
                    foreach ($data as $k => $v) {
                        $value_type = $v['value_type'];
                        $admin_mark_value = $v['value'];

                        if ($value_type == 'percentage') {
                            $markup_total_fare_ps_1 = (($markup_cncel_fare_r_Amount / 100) * $admin_mark_value);
                            $markup_cncel_fare_r_Amount = round($markup_cncel_fare_r_Amount + $markup_total_fare_ps_1);
                        } else if ($value_type == 'plus') {
                            $markup_total_fare_ps_1 = ($markup_cncel_fare_r_Amount + $admin_mark_value);
                            $markup_cncel_fare_r_Amount = round($markup_total_fare_ps_1);
                        } else {
                            $markup_cncel_fare_r_Amount = $markup_cncel_fare_r_Amount;
                        }
                    }
                }

                # Agent Markup Calculation
                if ($module == 'b2b') {
                $AgentMarkup_CANA = $GLOBALS ['CI']->hotel_model->get_hotel_user_markup_details();
                if (isset($AgentMarkup_CANA['markup'][0])) {
                    $AgentMarkup_TypeCANA = $AgentMarkup_CANA['markup'][0];
                    if ($AgentMarkup_TypeCANA['value_type'] == "percentage") {
                        $AgentMarkupAmountCANA = (($markup_cncel_fare_r_Amount * $AgentMarkup_TypeCANA['value']) / 100);
                        $markup_cncel_fare_r_Amount = $markup_cncel_fare_r_Amount + $AgentMarkupAmountCANA;
                    } else {
                        $AgentMarkupAmountCANA = $AgentMarkup_TypeCANA['value'];
                        $markup_cncel_fare_r_Amount = $markup_cncel_fare_r_Amount + $AgentMarkupAmountCANA;
                    }
                }
                }


                $tax = $GLOBALS ['CI']->hotel_model->get_tax();
                $tax = $tax['data'];
                $service_tax = ((($markup_cncel_fare_r_Amount) / 100) * $tax);


                $cancel_convi = 0;
              
                
                 $m = date('Y-m-d H:i:s', strtotime($cancellationPolicy[0]['from'])); //debug($m);
                
                $current_date = db_current_datetime();
                if ($m > $current_date) {
                    $cancellationPolicy[0]['from'] = $m;
                } else {
                   $cancellationPolicy[0]['from'] = $current_date;
                }


                $hotel_cncel_policy = array(
                    'app_reference' => $app_reference,
                    'booking_reference' => $booking_reference,
                    'amount' => round($markup_cncel_fare_r_Amount + $service_tax),
                    'date' => $cancellationPolicy[0]['from'],
                    'currency' => $currency_val
                );
           
                $GLOBALS ['CI']->db->insert('hotel_cancelation_policy', $hotel_cncel_policy);
                // }
            }
        }*/ // end of room loop


        
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
            /* $convinence = $currency_obj->convenience_fees($book_total_fare['default_value'], $master_search_id);
              $convinence_row = $currency_obj->get_convenience_fees();
              $convinence_value = $convinence_row ['value'];
              $convinence_type = $convinence_row ['type'];
              $convinence_per_pax = $convinence_row ['per_pax']; */
        } elseif ($module == 'b2b') {
            $discount = 0;
        }

        //convience fee 
        if ($module == 'b2c') {

            if (isset($params['booking_params']['token']['token']['convenience_fees']) && !empty($params['booking_params']['token']['token']['convenience_fees'])) {
                $convinence = $params['booking_params']['token']['token']['convenience_fees'];
            } else {
                $currency_obj->getConversionRate(false, get_application_transaction_currency_preference(), get_application_transaction_currency_preference());
                $convinence = $currency_obj->convenience_fees($book_total_fare['default_value'], $master_search_id);
            }
            $GLOBALS ['CI']->load->model ( 'transaction' );
              $GLOBALS['CI']->transaction->update_convinence_discount_details('hotel_booking_details', $app_reference, $discount, $convinence, $convinence_value, $convinence_type, $convinence_per_pax);
        }


        $response ['fare'] = $book_total_fare['default_value'];
        $response ['admin_markup'] = $admin_markup;
        $response ['agent_markup'] = $agent_markup;
        $response ['convinence'] = $convinence;
        $response ['discount'] = $discount;
        return $response;
    }

    /**
     * Jaganath
     * Cancel Booking
     */
    // function cancel_booking($booking_details, $cncel_amt, $currency, $total_amount, $module = 'b2c') {
    function cancel_booking($booking_details, $module = 'b2c') {
        $cancelpolicy = $booking_details['hotel_cancelation_policy'];
        $response ['data'] = array();
        $response ['status'] = FAILURE_STATUS;
        $resposne ['msg'] = 'Remote IO Error';
        /*$BookingId = $booking_details ['booking_id'];
        $BookingCode = $booking_details ['booking_reference'];*/
        $BookingId = $booking_details ['booking_reference'];
        $BookingCode = $booking_details ['booking_id'];
        $app_reference = $booking_details ['app_reference'];
        $send_change_request = $this->send_change_request_params($BookingId, $BookingCode);
        if ($send_change_request ['status']) {

            $GLOBALS ['CI']->custom_db->generate_static_response ($send_change_request ['data'] ['request'], 'REZLIVE Cancel booking request' );
            
            // 1.SendChangeRequest
             $send_change_request_response = $GLOBALS ['CI']->api_interface->xml_post_request($send_change_request ['data'] ['service_url'], ('XML='.urlencode($send_change_request ['data'] ['request'])), $this->xml_header());
            // var_dump($send_change_request_response);exit();
            
            $GLOBALS ['CI']->custom_db->generate_static_response ($send_change_request_response, 'REZLIVE Cancel booking response' );
            
            
            // $response = $send_change_request_response;
             /*$send_change_request_response = utf8_encode($send_change_request_response);

            $send_change_request_response = Converter::createArray($send_change_request_response);*/
            $send_change_request_response = utf8_encode($send_change_request_response);
            if(!empty($send_change_request_response)) {
                $send_change_request_response = Converter::createArray($send_change_request_response);
            } else {
                $send_change_request_response = $send_change_request_response;
            }
            /*
            $send_change_request_response = $GLOBALS ['CI']->api_interface->delete_rest_service($send_change_request['service_url'], $this->json_header());

            $GLOBALS ['CI']->custom_db->generate_static_response($send_change_request_response);
            $send_change_request_response = json_decode($send_change_request_response, TRUE);*/
            //debug($send_change_request_response);exit;
            /* $send_change_request_response = $GLOBALS['CI']->hotel_model->get_static_response(1755);//1624 //(pocessed-1783, 1755) */
            if (valid_array($send_change_request_response['CancellationResponse']) == true && $send_change_request_response['CancellationResponse']['Status'] == true ) {

                $cncel_amt = 0;
                $currency = $booking_details['itinerary_details'][0]['currency'];
                $total_amount = $booking_details['grand_total'];
                if(isset($send_change_request_response['CancellationResponse']['CancellationCharges']) == true) {
                    $cncel_amt = $send_change_request_response['CancellationResponse']['CancellationCharges'];
                    $currency = $send_change_request_response['CancellationResponse']['Currency'];
                }

                $this->save_cancellation_data($BookingId, $app_reference, $send_change_request_response, $cncel_amt, $currency, $total_amount, $module);
                $response['status'] = SUCCESS_STATUS;
                $response['data'] = $send_change_request_response;
                return $response;
                //return $this->get_change_request_status ($send_change_request_response['booking'], $app_reference );
            } else {
                $response ['msg'] = $send_change_request_response['error']['message'];
            }
        }//debug($response);exit;
        return $response;
    }

    function get_change_request_status($ChangeRequestId, $app_reference) {

        $response ['data'] = array();
        $response ['status'] = FAILURE_STATUS;
        $resposne ['msg'] = 'Remote IO Error';

        return $response;
    }

    /*
     * Jaganath
     * Save the Cancellation Details into Database
     */

    function save_cancellation_data($booking_reference, $app_reference, $send_change_request_response, $cancel_amt, $currency, $total_amount, $module) {//echo 'save_fun';
        if (isset($send_change_request_response['CancellationResponse']['Status']) && !empty($send_change_request_response['CancellationResponse']['Status'])) {
            $status = $send_change_request_response['CancellationResponse']['Status'];
            $cancellation_reference = '';
           /* if (isset($send_change_request_response['CancellationResponse']['BookingCode']) && !empty($send_change_request_response['CancellationResponse']['BookingCode'])) {
                $cancellation_reference = $send_change_request_response['CancellationResponse']['BookingCode'];
            }*/
             if (isset($send_change_request_response['CancellationResponse']['BookingId']) && !empty($send_change_request_response['CancellationResponse']['BookingId'])) {
                $cancellation_reference = $send_change_request_response['CancellationResponse']['BookingId'];
            }
            //cancellation Amount
            $api_cncel_amt = '';
            if (isset($send_change_request_response['CancellationResponse']['CancellationCharges']) && valid_array($send_change_request_response['CancellationResponse']['CancellationCharges'])) {
                $cancellation_amount = @$send_change_request_response['CancellationResponse']['CancellationCharges'];
                $currency = @$send_change_request_response['CancellationResponse']['Currency'];
                $cncel_amt = $currency . ' ' . $cancellation_amount;
            }

            // $HotelChangeRequestStatusResult = $send_change_request_response;
            $app_reference = trim($app_reference);
            $ChangeRequestId = @$cancellation_reference;
            $ChangeRequestStatus = $status;
            $status_description = $status; //$this->ChangeRequestStatusDescription ( $ChangeRequestStatus );
            $TraceId = @$cancellation_reference;
            $API_RefundedAmount = $total_amount - $cancel_amt;
            $API_CancellationCharge = @$cancel_amt;
            $attr = json_encode($send_change_request_response);
            // Update Booking Status
            if ($ChangeRequestStatus == true) {
                $booking_status = 'BOOKING_CANCELLED';
                $ChangeRequestStatus = 'Cancelled';
                // Update Master Booking Status
                $GLOBALS ['CI']->hotel_model->update_master_booking_status($app_reference, $booking_status);
                // Update Pax Booing Status
                $GLOBALS ['CI']->hotel_model->update_pax_booking_status($app_reference, $booking_status);
            }
            //REFUND TO AGENT IN CASE OF B2B FIXXX
            $GLOBALS ['CI']->hotel_model->save_cancellation_data($app_reference, $ChangeRequestId, $ChangeRequestStatus, $status_description, $API_RefundedAmount, $API_CancellationCharge, $attr);
            //echo 'hi';exit;
            /* $hotel_booking_details = array(
              'status' => $status,
              'cancellation_reference' => $cancellation_reference,
              'cancellation_amount' => $cncel_amt
              );
              $CI->db->where('booking_reference',$booking_reference);
              $CI->db->update('hotel_booking_details',$hotel_booking_details);

              $hotel_booking_itinerary_details = array(
              'status' => $status
              );

              $CI->db->where('app_reference',$app_reference);
              $CI->db->update('hotel_booking_itinerary_details',$hotel_booking_itinerary_details);

              $CI->db->where('app_reference',$app_reference);
              $CI->db->update('hotel_booking_pax_details',$hotel_booking_itinerary_details); */
        }

        // Update Transaction Details
     //  $this->domain_management_model->update_transaction_details('hotel', $app_reference, $API_RefundedAmount, $data['admin_markup'], $data['agent_markup']);
    }

    /**
     * Sawood
     * check and return status is success or not
     *
     * @param unknown_type $response_status
     */
    function valid_book_response($response_status) {
        $status = false;
        if (is_array($response_status) and !empty($response_status) and is_array($response_status ['BookResult']) and !empty($response_status ['BookResult']) and $response_status ['BookResult'] ['ResponseStatus'] == SUCCESS_STATUS and isset($response_status ['BookResult'] ['HotelBookingStatus']) and $response_status ['BookResult'] ['HotelBookingStatus'] != '' and ($response_status ['BookResult'] ['HotelBookingStatus'] != 'Pending' || $response_status ['BookResult'] ['HotelBookingStatus'] != 'Vouchered' || $response_status ['BookResult'] ['HotelBookingStatus'] != 'Confirmed')) {
            $status = true;
        }
        return $status;
    }

    /**
     * Arjun J Gowda
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
     * Arjun J Gowda
     *
     * Check if the room was blocked successfully
     *
     * @param array $block_room_response
     *        	block room response
     */
    private function is_room_blocked($block_room_response) {
        $room_blocked = false;
        if (isset($block_room_response ['BlockRoomResult']) == true and $block_room_response ['BlockRoomResult'] ['IsPriceChanged'] == false and $block_room_response ['BlockRoomResult'] ['IsCancellationPolicyChanged'] == false) {
            $room_blocked = true;
        }
        return $room_blocked;
    }

    /**
     * Arjun J Gowda
     * check if the room list is valid or not
     *
     * @param
     *        	$room_list
     */
    private function valid_room_details_details($room_list) {
        $status = false;
        if (isset($room_list ['GetHotelRoomResult']) == true and $room_list ['GetHotelRoomResult'] ['ResponseStatus'] == ACTIVE) {
            $status = true;
        }
        return $status;
    }

    /**
     * Arjun J Gowda
     * check if the hotel response which is received from server is valid or not
     *
     * @param
     *        	$hotel_details
     */
    private function valid_hotel_details($hotel_details) {
        $status = false;
        if (isset($hotel_details ['HotelDetailsResponse']['Hotels']) == true and !empty($hotel_details ['HotelDetailsResponse']['Hotels']) == true) {
            $status = true;
        }
        return $status;
    }

    /**
     * Validates Check Rate Response is Valid or not
     */
    private function valid_check_rate_details($hotel_details) {
        $status = false;
        if (isset($hotel_details ['error']) == false && isset($hotel_details ['hotel']) == true and valid_array($hotel_details ['hotel']) == true) {
            $status = true;
        }
        return $status;
    }

    /**
     * Arjun J Gowda
     * check if the search response is valid or not
     *
     * @param array $search_result
     *        	search result response to be validated
     */
    private function valid_search_result($search_result) {
        /* $search_result = json_decode ( $search_result, true ); */
        if (valid_array($search_result) == true and isset($search_result ['HotelFindResponse']['error']) == false && (isset($search_result['HotelFindResponse']['Hotels']) == false || count($search_result['HotelFindResponse']['Hotels']['Hotel']) > 0)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Arjun J Gowda
     * Update and return price details
     */
    public function update_block_details($room_details, $booking_parameters) {
        $booking_parameters ['TraceId'] = $room_details ['TraceId'];
        $room_details ['HotelRoomsDetails'] = get_room_index_list($room_details ['HotelRoomsDetails']);
        $application_default_currency = get_application_default_currency();
        $booking_parameters ['token'] = ''; // Remove all the token details
        $total_OfferedPriceRoundedOff = $Tax = '';
        foreach ($room_details ['HotelRoomsDetails'] as $__rc_key => $__rc_value) {
            $booking_parameters ['token'] [] = get_dynamic_booking_parameters($__rc_key, $__rc_value, $application_default_currency);
        }
        $booking_parameters ['price_summary'] = tbo_summary_room_combination($room_details ['HotelRoomsDetails']);

        return $booking_parameters;
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
     * Arjun J Gowda
     * convert search params to format
     * validate and format request params
     */
    public function search_data($search_id) {
        $response ['status'] = true;
        $response ['data'] = array();
        if (empty($this->master_search_data) == true and valid_array($this->master_search_data) == false) {
            $clean_search_details = $GLOBALS ['CI']->hotel_model->get_safe_search_data($search_id);
            //debug($clean_search_details);exit;
            if ($clean_search_details ['status'] == true) {
                $response ['status'] = true;
                $response ['data'] = $clean_search_details ['data'];

                // 28/12/2014 00:00:00 - date format
                $response ['data'] ['from_date'] = date('Y-m-d', strtotime($clean_search_details ['data'] ['from_date']));
                $response ['data'] ['to_date'] = date('Y-m-d', strtotime($clean_search_details ['data'] ['to_date']));
                // get city id based
                //debug($clean_search_details ['data'] ['hotel_destination']);exit;
                $location_details = $GLOBALS ['CI']->hotel_model->get_hotels_city_info($clean_search_details ['data'] ['hotel_destination']);
//debug($location_details);exit;

                if ($location_details ['status']) {
                    $response ['data'] ['country_code'] = $location_details ['data'] [0]['country_name'];
                    // $response ['data'] ['location_id'] = $location_details ['data'] [0]['hotelsbed'];
                    // $response ['data'] ['location_origin'] = $location_details ['data'] [0]['city_id'];
                } else {
                    $response ['status'] = false;
                }

                $this->master_search_data = $response ['data'];
            } else {
                $response ['status'] = false;
            }
        } else {
            $response ['data'] = $this->master_search_data;
        }
        $this->search_hash = md5(serialized_data($response ['data']));

        return $response;
    }

    /**
     * Markup for search result
     *
     * @param array $price_summary
     * @param object $currency_obj
     * @param number $search_id
     */
    /* function update_search_markup_currency(& $price_summary, & $currency_obj, $search_id, $level_one_markup = false, $current_domain_markup = true) {
      $search_data = $this->search_data ( $search_id );
      $no_of_nights = $this->master_search_data ['no_of_nights'];
      $no_of_rooms = $this->master_search_data ['room_count'];
      $multiplier = ($no_of_nights * $no_of_rooms);
      return $this->update_markup_currency ( $price_summary, $currency_obj, $multiplier, $level_one_markup, $current_domain_markup );
      } */

    /**
     * Markup for Room List
     *
     * @param array $price_summary
     * @param object $currency_obj
     * @param number $search_id
     */
    function update_room_markup_currency(& $price_summary, & $currency_obj, $search_id, $level_one_markup = false, $current_domain_markup = true) {
        $search_data = $this->search_data($search_id);
        $no_of_nights = $this->master_search_data ['no_of_nights'];
        $no_of_rooms = 1;
        $multiplier = ($no_of_nights * $no_of_rooms);
        return $this->update_markup_currency($price_summary, $currency_obj, $multiplier, $level_one_markup, $current_domain_markup);
    }

    /**
     * Markup for Booking Page List
     *
     * @param array $price_summary
     * @param object $currency_obj
     * @param number $search_id
     */
    function update_booking_markup_currency(& $price_summary, & $currency_obj, $search_id, $level_one_markup = false, $current_domain_markup = true) {
        return $this->update_search_markup_currency($price_summary, $currency_obj, $search_id, $level_one_markup, $current_domain_markup);
    }

    /**
     * update markup currency and return summary
     * $attr needed to calculate number of nights markup when its plus based markup
     */
    function update_markup_currency(& $price_summary, & $currency_obj, $no_of_nights = 1, $level_one_markup = false, $current_domain_markup = true) {
        $tax_service_sum = 0;
        $markup_summary = array();
        $temp_price = $currency_obj->get_currency($price_summary, true, $level_one_markup, $current_domain_markup, $no_of_nights);
        return array('value' => $temp_price ['default_value'], 'currency' => $temp_price['default_currency']);
    }

    /**
     * Tax price is the price for which markup should not be added
     */
    function tax_service_sum($markup_price_summary, $api_price_summary) {
        // sum of tax and service ;
        return abs($api_price_summary ['ServiceTax'] + $api_price_summary ['Tax'] + ($markup_price_summary ['PublishedPrice'] - $api_price_summary ['PublishedPrice']));
    }

    /**
     * calculate and return total price details
     */
    function total_price($price_summary) {
        return ($price_summary ['OfferedPriceRoundedOff']);
    }

    function booking_url($search_id) {
        return base_url() . 'index.php/hotel/booking/' . intval($search_id);
    }

    /**
     * Jaganath
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

    /* format hotels list data */

    private function get_formatted_hotel($hl, $search_id, $module) {
        // error_reporting(E_ALL);
        // debug($hl);exit;
        $this->api_data_currency = $hl['Currency'];
        $currency_for_response = $hl['Currency'];
        $hl = $hl['Hotels'] ['Hotel'];
        $CI = & get_instance();
        $hl_tmp = force_multple_data_format($hl);
        // debug($hl_tmp);exit;
        //get static data from database
        
        $hotel_codes = array();
        if (COUNT($hl_tmp) > 0) {
            foreach ($hl_tmp as $v) {
                $hotel_codes[] = $v['Id'];
            }
            // debug($hotel_codes);exit;
            /* store hotel list in temperory table */
            $hotel_list['hotel_code'] = $hotel_codes;
            $codes = json_encode($hotel_list);

            if (isset($codes) && !empty($codes)) {
                $inser_arr = array(
                    'hotel_list' => $codes
                );
                $CI->db->where('search_id', $search_id);
                $query = $CI->db->get('rz_hotel_list_temp');
                if ($query->num_rows() > 0) {
                    $CI->db->where('search_id', $search_id);
                    $CI->db->update('rz_hotel_list_temp', $inser_arr);
                } else {
                    $inser_arr['search_id'] = $search_id;
                    $CI->db->insert('rz_hotel_list_temp', $inser_arr);
                }
            }
        }

        //Currency Preference
        $currency_obj = new Currency(array('module_type' => 'hotel', 'from' => get_application_default_currency(), 'to' => get_application_display_currency_preference()));
        //get static data for hotels
     // debug($hotel_codes);exit();
        $cache_list = $this->get_hotel_info($hotel_codes);
// debug($cache_list);

        /* build hotels array with required fields */
        $hotel_array = array();
        $search_data = $this->search_data($search_id);

        
        foreach ($hl_tmp as $key => $hotel_detail) {
            $currency = $currency_for_response;
            $currency_obj->getConversionRate(false, $currency, get_application_display_currency_preference());
            $hkey = $hotel_detail ['Id'];
         
           if (isset($cache_list['info'][$hkey]) == true) {
                
                $cache_info = $cache_list['info'][$hkey];
                // $cache_facility = isset($cache_list['facility'][$hkey]) ? $cache_list['facility'][$hkey] : false;
                $cache_facility = isset($cache_info['amenities']) ? $cache_info['amenities'] : false;

                $hotel_code = $hotel_detail['Id'];
                $hotel_name = $cache_info['hotel_name'];
                $destination_code = $search_data['data']['rz_city_code'];
                $destination_name = isset($cache_info ['hotel_city']) ? $cache_info ['hotel_city'] : 'Others';
                
                $star_rating = $hotel_detail['Rating'];
                $description = $cache_info['description'];
                $address = $cache_info['address'];
                $postal = $cache_info['postal_code'];
                $primary_image = $hotel_detail['ThumbImages']['@cdata'];
                $image = array();
                $image = $cache_info['image_list'];
                $email = '';
                $website = '';
                $contact = '';
                /*$zone_code = '';
                $zone_name = '';*/
                $lat = $cache_info['latitude'];
                $lon = $cache_info['longitude'];
                $min_rate = $hotel_detail['Price'];
                $price = $hotel_detail['Price'];
                $currency = $currency_for_response;
                  // Add Markup
                if($module == 'b2c') {
                    $m_r = $currency_obj->get_currency($min_rate,true,false,true, 1);
                    $min_rate = ceil($m_r['default_value']);
                    $prc = $currency_obj->get_currency($price,true,false,true, 1);
                    $price = ceil($prc['default_value']);
                    $currency = $prc['default_currency'];
                } elseif ($module == 'b2b') {
                    // B2B Calculation - Room wise price
                    $markup_total_fare = $currency_obj->get_currency ( $min_rate, true, true, true, 1 ); // (ON Total PRICE ONLY)
                    $prc = $currency_obj->get_currency ( $price, true, true, true, 1 ); // (ON Total PRICE ONLY)
                    /*$ded_total_fare = $currency_obj->get_currency ( $min_rate, true, false, true, 1 ); // (ON Total PRICE ONLY)
                    $admin_markup = sprintf ( "%.2f", $markup_total_fare ['default_value'] - $ded_total_fare ['default_value'] );
                    $agent_markup = sprintf( "%.2f", $ded_total_fare ['default_value'] - $min_rate );*/
                    
                    $min_rate = ceil($markup_total_fare['default_value']);
                    $price = ceil($prc['default_value']);
                    $currency = $prc['default_currency'];
                }

                /*$min_rate = ceil($hotel_detail['Price']);
                $price = ceil($hotel_detail['Price']);*/
                $max_rate = '';

                // $currency = $hotel_detail['currency'];
                
                // $accomodation_type = empty($cache_info['accomodation_type']) ? 'Others' : $cache_info['accomodation_type'];
                $accomodation_type = 'Others';
                $facilities = $cache_facility;
                // (is_array($cache_facility) ? array_slice($cache_facility, 0, $f_limit) : false);
                //	debug($min_rate);exit;

                // tax commision related calcualtion
                /*$tax = $GLOBALS ['CI']->hotel_model->get_tax();
                $tax = $tax['data'];
                if ($module == 'b2c') {

                    $converted_rate_before_admin_amrkup = $currency_obj->get_currency($min_rate, false, false, false, 1);

                    $converted_min_rate = $currency_obj->get_currency($min_rate, true, false, false, true, 1);

                    $markup_total_fare_ps = $converted_min_rate['default_value'] - $converted_rate_before_admin_amrkup['default_value'];

                    if (isset($converted_min_rate['default_value'])) {
                        $min_rate = $converted_min_rate['default_value'];
                        $service_tax = (($min_rate / 100) * $tax);
                        $min_rate = $min_rate + $service_tax;
                        $currency = $converted_min_rate['default_currency'];
                    }
                    $service_tax_min = $service_tax;
                    $converted_max_rate = $currency_obj->get_currency($max_rate, false, false, false, 1);
                    if (isset($converted_max_rate['default_value'])) {
                        $max_rate = $converted_max_rate['default_value'];
                        $service_tax = (($max_rate / 100) * $tax);
                        $max_rate = $min_rate + $service_tax;
                    }

                    $price = $min_rate;
                } else {

                    $converted_min_rate = $currency_obj->get_currency($min_rate, false, false, false, 1);
//debug($converted_min_rate);exit;
                    $markup_total_fare_p = $converted_min_rate['default_value'];
                    $adminmarkup = $GLOBALS ['CI']->hotel_model->getadmin_markup();
                    //debug($markup_total_fare_p);
                    # Admin Markup Calculation

                    if ($adminmarkup['status'] == true) {
                        //debug($adminmarkup);exit;
                        $data = $adminmarkup['data'];
                        foreach ($data as $k => $v) {
                            $value_type = $v['value_type'];
                            $admin_mark_value = $v['value'];

                            if ($value_type == 'percentage') {  
                                $markup_total_fare_ps = (($markup_total_fare_p / 100) * $admin_mark_value);
                                $markup_total_fare_p = round($markup_total_fare_p + $markup_total_fare_ps);
                            } else if ($value_type == 'plus') {
                                $markup_total_fare_ps = ($markup_total_fare_p + $admin_mark_value);
                                $markup_total_fare_p = round($markup_total_fare_ps);
                            } else {
                                $markup_total_fare_p = $markup_total_fare_p;
                            }
                        }
                    }

                    # Agent Markup Calculation
                    $AgentMarkup = $GLOBALS ['CI']->hotel_model->get_hotel_user_markup_details();
                    $AgentMarkup_Type = $AgentMarkup['markup'][0];
                    if ($AgentMarkup_Type['value_type'] == "percentage") {
                        $AgentMarkupAmount = (($markup_total_fare_p * $AgentMarkup_Type['value']) / 100);
                        $markup_total_fare_p+=$AgentMarkupAmount;
                    } else {
                        $AgentMarkupAmount = $AgentMarkup_Type['value'];
                        $markup_total_fare_p+=$AgentMarkupAmount;
                    }



                    $service_tax_min = (($markup_total_fare_p / 100) * $tax);
                    $markup_total_fare_p+=$service_tax_min;

                    $min_rate = $markup_total_fare_p;
                    $currency = $converted_min_rate['default_currency'];

                    $converted_max_rate = $currency_obj->get_currency($max_rate, false, false, false, 1);
                    $markup_total_fare_max = $converted_max_rate['default_value'];
                    $service_tax_max = (($markup_total_fare_max / 100) * $tax);
                    $max_rate = $markup_total_fare_max + $service_tax_max;
                    $price = $min_rate;
                }*/


                $hotel_array [$hkey] = $this->format_hotel_summary($hotel_code, $hotel_name, $destination_code, $destination_name, $star_rating, $lat, $lon, $min_rate, $max_rate, $price, $currency, $facilities, $description, $address, $image, $primary_image, $email, $website, $contact, $postal, $accomodation_type);

                /*$hotel_array [$hkey]['facility_cstr'] = '';
                $fi = 0;
                $f_limit = 0;
                if ($cache_facility) {
                    if ($this->aminity_count != -1) {
                        $f_limit = $this->aminity_count;
                    } else {
                        $f_limit = count($cache_facility);
                    }
                    foreach ($cache_facility as $fk => $fv) {
                        $hotel_array [$hkey]['facility_cstr'] .= $fv['cstr'];
                    }
                }*/
           } else {
                //FIXME
                //data not in cache so remove from list and insert to table to fetch the result and update later
            // debug($hotel_detail);
                if(!empty($hotel_detail)) {
                    $hotel_code =  $hotel_detail['Id'];
                     //  removed because of performance
                    // $static_data = $this->get_hotel_static_detail($search_data, $hotel_code);
                    $static_data['status'] = false;
                    if($static_data['status'] == true){
                        $lat = $static_data['data']['Latitude'];
                        $lon = $static_data['data']['Longitude'];
                        $facilities = false;
                        $description = $static_data['data']['Description']['@cdata'];
                        $address = $static_data['data']['HotelAddress'];
                        $email = $static_data['data']['Email'];
                        $website = $static_data['data']['Website'];
                        $contact = $static_data['data']['Phone'];
                        $postal = $static_data['data']['HotelPostalCode'];
                        $image = array();
                        if(isset($static_data['data']['Images'])){
                            $image = $static_data['data']['Images']['Image'];
                        } else {
                            $image = array($hotel_detail['ThumbImages']['@cdata']); 
                        }
                    } else {
                        $lat = '';
                        $lon = '';
                        $facilities = false;
                        $description = '';
                        $address = '';
                        $image = array();
                        $image = array($hotel_detail['ThumbImages']['@cdata']);
                        $email = '';
                        $website = '';
                        $contact = '';
                        $postal = '';
                    }
                    
                    $hotel_name =  $hotel_detail['Name']['@cdata'];
                    $destination_code = $search_data['data']['rz_city_code'];
                    $destination_name = isset($search_data['data']['city_name']) ? $search_data['data']['city_name'] : 'Others';
                    $star_rating = $hotel_detail['Rating'];
                    $min_rate = $hotel_detail['Price'];
                    $price = $hotel_detail['Price'];
                    $max_rate = '';
                    $currency = $currency_for_response;
                     // Add Markup
                    if($module == 'b2c') {
                        $m_r = $currency_obj->get_currency($min_rate,true,false,true, 1);
                        $min_rate = ceil($m_r['default_value']);
                        $prc = $currency_obj->get_currency($price,true,false,true, 1);
                        $price = ceil($prc['default_value']);
                        $currency = $prc['default_currency'];
                    } elseif ($module == 'b2b') {
                        // B2B Calculation - Room wise price
                        $markup_total_fare = $currency_obj->get_currency ( $min_rate, true, true, true, 1 ); // (ON Total PRICE ONLY)
                        $prc = $currency_obj->get_currency ( $price, true, true, true, 1 ); // (ON Total PRICE ONLY)
                        // $ded_total_fare = $currency_obj->get_currency ( $min_rate, true, false, true, 1 ); // (ON Total PRICE ONLY)
                        // $admin_markup = sprintf ( "%.2f", $markup_total_fare ['default_value'] - $ded_total_fare ['default_value'] );
                        // $agent_markup = sprintf( "%.2f", $ded_total_fare ['default_value'] - $min_rate );
                        
                        $min_rate = ceil($markup_total_fare['default_value']);
                        $price = ceil($prc['default_value']);
                        $currency = $prc['default_currency'];
                    }
                    $primary_image = @$hotel_detail['ThumbImages']['@cdata'];
                    $accomodation_type = 'Others';
                 $hotel_array [$hkey] = $this->format_hotel_summary($hotel_code, $hotel_name, $destination_code, $destination_name, $star_rating, $lat, $lon, $min_rate, $max_rate, $price, $currency, $facilities, $description, $address, $image, $primary_image, $email, $website, $contact, $postal, $accomodation_type);
                }
           }
        }//echo 'x';
      // debug($hotel_array);exit;
        return $hotel_array;
    }

    /**
     * return all the hotel information
     * @param mixed $hotel_code number of array
     */
    function get_hotel_info($hotel_code) {
        $h_cond_val = '';
        if (empty($hotel_code) == false) {
          
            $static_hotel_info = $this->get_static_hotel_info($hotel_code);
       
            // $static_hotel_facility_info = $this->get_static_hotel_facility_info($hotel_code, '', 'icon');
           // debug($static_hotel_facility_info);exit;
            // return array_merge($static_hotel_info, $static_hotel_facility_info);
            return $static_hotel_info;
        }
    }

    /**
     * static data to be read for hotels from db
     * @param string $h_cond
     */
    function get_static_hotel_info($hotel_code, $cols = '') {
        
        $CI = & get_instance();
       /* if (is_array($hotel_code)) {
            $h_cond = 'h.hotel_code IN (' . implode(',', $hotel_code) . ')';
        } else {
            $h_cond = 'h.hotel_code = ' . $CI->db->escape($hotel_code);
        }
        if ($cols == '') {
            $cols = 'hi.image_path AS img, h.hotel_code AS hc, h.category_code AS cc, h.address AS address, h.postal_code AS postal,
			h.chain_code as chain, h.hotel_email as email, h.website as website, h.description as description,
			hcl.description hotel_chain, cl.accommodationType as acc_type, cl.description as cat_desc';
        }
        $query = ' SELECT ' . $cols . '  FROM hb_hotel_details AS h LEFT JOIN hb_hotel_images AS hi ON h.origin=hi.hotel_id
		LEFT JOIN hb_hotel_chain_list hcl ON h.chain_code=hcl.code
		LEFT JOIN hb_categorie_list cl ON h.category_code=cl.code WHERE (hi.order=1 || hi.order IS NULL) AND ' . $h_cond . ' GROUP BY h.origin';
      
        $data = $CI->db->query($query)->result_array();*/

         if (is_array($hotel_code)) {
            $h_cond = 'h.hotel_code IN (' . implode(',', $hotel_code) . ')';
        } else {
            $h_cond = 'h.hotel_code = ' . $CI->db->escape($hotel_code);
        }
        if ($cols == '') {  
            $cols = 'h.origin as origin, h.hotel_code, h.hotel_name, h.hotel_city as hotel_city, h.country_code as hotel_country, h.address as address, h.postal_code as postal_code, h.latitude , h.longitude , h.description as description,  GROUP_CONCAT(DISTINCT hi.image_path SEPARATOR " $ ") as image_list, ha.amenities as amenities';
            /*$cols = 'hi.image_path AS img, h.hotel_code AS hc, h.category_code AS cc, h.address AS address, h.postal_code AS postal,
            h.chain_code as chain, h.hotel_email as email, h.website as website, h.description as description,
            hcl.description hotel_chain, cl.accommodationType as acc_type, cl.description as cat_desc';

            LEFT JOIN hb_hotel_images AS hi ON h.origin=hi.hotel_id
        LEFT JOIN hb_hotel_chain_list hcl ON h.chain_code=hcl.code
        LEFT JOIN hb_categorie_list cl ON h.category_code=cl.code WHERE (hi.order=1 || hi.order IS NULL) AND ' . $h_cond . ' GROUP BY h.origin*/    
        }
        $query = ' SELECT ' . $cols . '  FROM rz_hotel_details AS h 
        LEFT JOIN rz_hotel_images AS hi ON h.hotel_code=hi.hotel_code 
        LEFT JOIN rz_hotel_amenities AS ha ON h.hotel_code=ha.hotel_code  WHERE ' . $h_cond . ' GROUP BY h.origin';
      
        $data = $CI->db->query($query)->result_array();
        // debug($data);exit();
      
        //group data with hotel code index
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
        // debug($resp);exit();
        return $resp;
    }

    /*
     * static hotel images
     * */

    function get_static_hotel_images($hotel_code) {
        $CI = & get_instance();
        if (is_array($hotel_code)) {
            $h_cond = 'hotel_code IN (' . implode(',', $hotel_code) . ')';
        } else {
            $h_cond = 'hotel_code = ' . $CI->db->escape($hotel_code);
        }
        $cols = 'image_type_code AS imageCode, image_path as imagePath, room_code as roomCode, room_type as roomType';

        $query = ' SELECT ' . $cols . ' FROM hb_hotel_images AS h WHERE ' . $h_cond . ' ORDER BY `order` ASC';
        $data = $CI->db->query($query)->result_array();
        return $data;
    }

    /*
     * static hotel contact details
     * */

    function get_static_hotel_contact_info($hotel_code, $cols = '') {
        $CI = & get_instance();
        if (is_array($hotel_code)) {
            $h_cond = 'hotel_code IN (' . implode(',', $hotel_code) . ')';
        } else {
            $h_cond = 'hotel_code = ' . $CI->db->escape($hotel_code);
        }

        if ($cols == '') {
            $cols = 'phone_type as pType, phone_number as contactN';
            //$cols = 'hf.hotel_code AS hc, hf.CODE AS fc, hfd.DISPLAYCLASS AS dis_class, hfd.NAME AS name, hfd.CLASSICON AS icon_class, CONCAT("_", hfd.CLASSICON, hf.CODE, "_") AS cstr, NUMBER_ as facility_number';
        }

        $query = ' SELECT ' . $cols . '
		FROM hb_hotel_contacts WHERE ' . $h_cond;
        $data = $CI->db->query($query)->result_array();
        return $data;
    }

    /**
     * static facility information
     * @param $h_cond
     * @param $cols
     */
    
  /*   SELECT hfg.description,hf.indFee AS additional_cost, hf.indLogic AS not_at_hotel, 
   hf.hotel_code AS hc, hf.facility_code as fc, 
    hfd.display_class AS dis_class, hf.facility_group_code as fgc,hfg.code as oo,
    hfd.description as name, hfd.icon_class, hfd.origin as facility_number,
    CONCAT("_", hfd.display_class, hf.facility_code, "_") AS cstr
    FROM hb_hotel_facilities AS hf 
    JOIN hb_hotel_facilities_description AS hfd ON hf.facility_description_id=hfd.origin
    JOIN hb_facilities_group AS hfg ON hfg.code=hf. facility_code 
    WHERE hf.hotel_code = '89458' order by hfg.code */
    
    function get_static_hotel_facility_info($hotel_code, $cols = '', $filter = '') {
        $CI = & get_instance();
        if (is_array($hotel_code)) {
            $h_cond = 'hf.hotel_code IN (' . implode(',', $hotel_code) . ')';
        } else {
            $h_cond = 'hf.hotel_code = ' . $CI->db->escape($hotel_code);
        }
        if (isset($filter) && !empty($filter)) {
            $h_cond = isset($h_cond) && !empty($h_cond) ? $h_cond . ' AND hfd.icon_class != ""' : ' hfd.icon_class != ""';
        }
        if ($cols == '') {
            $cols = 'hfg.description,hf.indFee AS additional_cost, hf.indLogic AS not_at_hotel, hf.hotel_code AS hc, hf.facility_code as fc, hfd.display_class AS dis_class, hf.facility_group_code as fgc,
			hfd.description as name, hfd.icon_class, hfd.origin as facility_number,
			CONCAT("_", hfd.display_class, hf.facility_code, "_") AS cstr';
        }
        $query = ' SELECT ' . $cols . '
		FROM hb_hotel_facilities AS hf 
        		JOIN hb_hotel_facilities_description AS hfd ON hf.facility_description_id=hfd.origin
				JOIN hb_facilities_group AS hfg ON hfg.code=hfd.facility_group_code 
        		WHERE ' . $h_cond;
      
        //.' AND hfd.icon_class != ""'

        $data = $CI->db->query($query)->result_array();
	
        $facil_array = array();
        //group data with hotel code index and facility and desc
        if (valid_array($data) == true) {
            foreach ($data as $k => $v) {
                $resp['facility'][$v['hc']][] = $v;
                $facil_array['facility'][$v['hc']][$v['description']][] = $v;
            }
        }
//debug($facil_array);
//debug($facil_array);exit;
        return $facil_array;
    }

    /**
     * Get room facilities
     * @param array $room_codes
     */
    function get_room_facilities($hotel_code, $room_codes) {
        $CI = & get_instance();
        $room_list_cond = '';
        if (is_array($room_codes) == true) {
            foreach ($room_codes as $v) {
                $room_list_cond .= $CI->db->escape($v) . ',';
            }
            $room_list_cond = 'hhr.room_code IN (' . substr($room_list_cond, 0, -1) . ')';
        } else {
            $room_list_cond = 'hhr.room_code = ' . $CI->db->escape($room_codes);
        }

        $query = 'SELECT hhr.room_code AS room_code, hrf.indFee AS additional_cost, hrf.indLogic AS not_at_hotel, hfd.description, hfd.icon_class
		FROM hb_hotels_room AS hhr JOIN hb_hotel_room_facilities hrf ON
		hhr.hotel_code = ' . $CI->db->escape($hotel_code) . ' AND ' . $room_list_cond . ' AND hhr.origin=hrf.hotel_room_id JOIN
		hb_hotel_facilities_description AS hfd ON
		hrf.room_facility_code=hfd.facility_code AND hrf.room_facility_group_code=hfd.facility_group_code';

        $data = $CI->db->query($query)->result_array();
        /* $room_facilities = '';
          if (valid_array($data) == true) {
          foreach ($data as $k => $v) {

          }
          } */
        return $data;
    }

    /**
     *
     * @param number $hotel_code
     * @param array $room_codes
     */
    function get_room_images($hotel_code, $room_codes) {
        $CI = & get_instance();
        $room_list_cond = '';
        if (is_array($room_codes) == true) {
            foreach ($room_codes as $v) {
                $room_list_cond .= $CI->db->escape($v) . ',';
            }
            $room_list_cond = 'hi.room_code IN (' . substr($room_list_cond, 0, -1) . ')';
        } else {
            $room_list_cond = 'hi.room_code = ' . $CI->db->escape($room_codes);
        }

        $query = 'SELECT hi.room_code AS room_code, hi.image_path as image, hi.image_type_code
		FROM hb_hotel_images AS hi WHERE hi.hotel_code = ' . $CI->db->escape($hotel_code) . ' AND ' . $room_list_cond . '';

        $data = $CI->db->query($query)->result_array();
        /* $room_facilities = '';
          if (valid_array($data) == true) {
          foreach ($data as $k => $v) {

          }
          } */
        return $data;
    }

    /**
     * getting near by hotels from db
     * @param $latitude
     * @param $longitude
     */
    function get_nearby_hotels($latitude, $longitude, $hotel_codes = array(), $currentHotelCode = '') {
        $CI = & get_instance();
        $CI->db->select("hd.origin,hd.hotel_code,hd.hotel_name,hi.image_path,hd.address, ( 3959 * acos( cos( radians($latitude) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( latitude ) ) ) ) AS distance");
        $CI->db->from('hb_hotel_details as hd');
        $CI->db->join('hb_hotel_images as hi', 'hi.hotel_code = hd.hotel_code', 'left');
        $CI->db->having('distance <= 3');
        if (isset($hotel_codes) && !empty($hotel_codes)) {
            $CI->db->where_in('hd.hotel_code', $hotel_codes);
        }
        if (isset($currentHotelCode) && !empty($currentHotelCode)) {
            $CI->db->where('hd.hotel_code !=', $currentHotelCode);
        }
        //$CI->db->where('hi.order = 1');
        $CI->db->where('latitude !=', $latitude);
        $CI->db->where('longitude !=', $longitude);

        $CI->db->group_by('hd.hotel_code');
        $CI->db->order_by('hd.hotel_name', 'ASC');
        $CI->db->order_by('order', 'ASC');
        $CI->db->limit(3);
        $query = $CI->db->get();

        if ($query->num_rows() == '') {
            return '';
        } else {
            return $query->result();
        }
    }

    /*
     * get temp hotel code list based on search id
     * */

    function get_temp_hotelcode_list($search_id) {
        $CI = & get_instance();
        $details = $CI->db->get_where('hb_hotel_list_temp', array('search_id' => $search_id))->row();
        $hotel_list_str = '';

        if (isset($details) && !empty($details)) {
            $hotel_list = $details->hotel_list;

            $hotel_list = json_decode($hotel_list);
            if (isset($hotel_list->hotel_code) && COUNT($hotel_list->hotel_code) > 0) {
                $hotel_list_str = implode(',', $hotel_list->hotel_code);
            }

            return $hotel_list->hotel_code;
        }
    }

    /*
     * get nearest terminal distance
     * */

    function get_nearest_terminal($hotelcode) {
        $CI = & get_instance();
        $query = 'SELECT `hotel_terminalCode`,`hotel_distance` FROM `hb_hotel_terminals` WHERE `hotel_code`=' . $hotelcode . ' ORDER BY `hotel_distance` ASC LIMIT 1';
        $data = $CI->db->query($query)->row();

        return $data;
    }

    /* built hotel detail array from responce */

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

               /* $tmp_room_image_list = $this->get_room_images($hotel_array['hotel_code'], $room_codes);
                $room_img_list = array();
                if (valid_array($tmp_room_image_list) == true) {
                    foreach ($tmp_room_image_list as $tfk => $tfv) {
                        $room_img_list[$tfv['room_code']][] = $tfv;
                    }
                }*/

                /*$tmp_room_facility_list = $this->get_room_facilities($hotel_array['hotel_code'], $room_codes);
                $room_facility_list = array();
                if (valid_array($tmp_room_facility_list) == true) {
                    foreach ($tmp_room_facility_list as $tfk => $tfv) {
                        $room_facility_list[$tfv['room_code']][] = $tfv;
                    }
                }*/

                /*$tmp_room_facility_list = explode(',', $api_static_data['data']['RoomAmenities']);
                $room_facility_list = array();
                if (valid_array($tmp_room_facility_list) == true) {
                    foreach ($tmp_room_facility_list as $tfk => $tfv) {
                        $room_facility_list[$tfv['room_code']][] = $tfv;
                    }
                }*/ 
                //get images and facilities for rooms
                // debug($hotel_detail_responce);
                // debug($hotel_detail_responce['RoomDetails']['RoomDetail']);
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
                    // }comm
                    //$room[$hrrv->paymentType][$hrrv->boardName][$hrrv->adults.'-'.$hrrv->children]
                    /*foreach ($room['rates'] as $rkey => $rate) {
                        //$testDFD[$rate['boardName']][$rate['adults'].'-'.$rate['children']][] = $rate;

                        $hotel_array['rooms'][$key]['rates'][$rkey]['rateKey'] = $rate['rateKey'];
                        $hotel_array['rooms'][$key]['rates'][$rkey]['rateClass'] = $rate['rateClass'];
                        $hotel_array['rooms'][$key]['rates'][$rkey]['rateType'] = $rate['rateType'];
                        $hotel_array['rooms'][$key]['rates'][$rkey]['net'] = $rate['net'];
                        $hotel_array['rooms'][$key]['rates'][$rkey]['allotment'] = isset($rate['allotment']) && !empty($rate['allotment']) ? $rate['allotment'] : '';
                        $hotel_array['rooms'][$key]['rates'][$rkey]['rateCommentsId'] = isset($rate['rateCommentsId']) && !empty($rate['rateCommentsId']) ? $rate['rateCommentsId'] : '';
                        $hotel_array['rooms'][$key]['rates'][$rkey]['rateComments'] = isset($rate['rateComments']) && !empty($rate['rateComments']) ? $rate['rateComments'] : '';
                        $hotel_array['rooms'][$key]['rates'][$rkey]['paymentType'] = $rate['paymentType'];
                        $hotel_array['rooms'][$key]['rates'][$rkey]['boardCode'] = $rate['boardCode'];
                        $hotel_array['rooms'][$key]['rates'][$rkey]['boardName'] = $rate['boardName'];

                         // cancellation policies 
                        if (isset($rate['cancellationPolicies']) && !empty($rate['cancellationPolicies'])) {
                            foreach ($rate['cancellationPolicies'] as $cKey => $policy) {
                                $hotel_array['rooms'][$key]['rates'][$rkey]['cancellationPolicies'][$cKey]['amount'] = $policy['amount'];
                                $hotel_array['rooms'][$key]['rates'][$rkey]['cancellationPolicies'][$cKey]['from'] = $policy['from'];
                            }
                        }

                         // texes 
                        if (isset($room['taxes']['taxes']) && !empty($room['taxes']['taxes'])) {
                            foreach ($room['taxes']['taxes'] as $tKey => $tax) {
                                $hotel_array['rooms'][$key]['rates'][$rkey]['tax'][$tKey]['included'] = $tax['included'];
                                $hotel_array['rooms'][$key]['rates'][$rkey]['tax'][$tKey]['percent'] = $tax['percent'];
                            }
                        }

                         // offers 
                        if (isset($room['offers']) && !empty($room['offers'])) {
                            foreach ($room['offers'] as $oKey => $offer) {
                                $hotel_array['rooms'][$key]['rates'][$rkey]['offer'][$oKey]['code'] = $offer['code'];
                                $hotel_array['rooms'][$key]['rates'][$rkey]['offer'][$oKey]['offer_name'] = $offer['name'];
                                $hotel_array['rooms'][$key]['rates'][$rkey]['offer'][$oKey]['offer_amount'] = $offer['amount'];
                            }
                        }
                        $hotel_array['rooms'][$key]['rates'][$rkey]['rooms'] = $rate['rooms'];
                        $hotel_array['rooms'][$key]['rates'][$rkey]['adults'] = $rate['adults'];
                        $hotel_array['rooms'][$key]['rates'][$rkey]['children'] = $rate['children'];
                    }*/
                    //debug($testDFD);exit;
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

            /* ------------- get hotel static data from db ------------------- */
            // $hotel_static_detils_arr = $this->get_static_hotel_info($hotelcode, '');

         /*   if(isset($static_data) && !empty($static_data)) {
                // $hotel_array['chain'] = $hotel_static_detils_arr['info'][$hotelcode]['chain'];
                $hotel_array['category_code'] = 'Others';
                $hotel_array['address'] = $static_data['address'];
                $hotel_array['postal'] = $static_data['postal_code'];
                $hotel_array['email'] = $api_static_data['data']['Email'];
                $hotel_array['website'] = $api_static_data['data']['Website'];
                $hotel_array['description'] = $static_data['description'];
            }*/
            
            /*$hotel_array['hotel_static_facilities_arr'] = array();
            // debug($static_data['amenities']);
            foreach ($static_data['amenities'] as $key => $value) {
                $hotel_array['hotel_static_facilities_arr'][$key]['name'] = $value ;
                $hotel_array['hotel_static_facilities_arr'][$key]['additional_cost'] = 0 ;
            }*/
            /* get hotel facilities with desc */
            /*$hotel_static_facilities_arr = $this->get_static_hotel_facility_info($hotelcode);
            $hotel_array['hotel_static_facilities_arr'] = isset($hotel_static_facilities_arr['facility'][$hotelcode]) && COUNT($hotel_static_facilities_arr['facility'][$hotelcode]) > 0 ? $hotel_static_facilities_arr['facility'][$hotelcode] : '';*/

            /* get temp table hotel code list and get nearby hotels */
            /* $currentHotelCode = $hotel_array['hotel_code'];
              $hotel_list_code_tmp = $this->get_temp_hotelcode_list($search_id);
              $hotel_nearby = $this->get_nearby_hotels($hotel_array['latitude'],$hotel_array['longitude'],$hotel_list_code_tmp,$currentHotelCode);
              $hotel_array['hotel_nearby'] = $hotel_nearby; */

            /* get nearest terminal distance */
            // $terminal = $this->get_nearest_terminal($hotelcode);
            if (isset($terminal) && !empty($terminal)) {
                $hotel_array['hotel_terminalCode'] = $terminal->hotel_terminalCode;
                $hotel_array['hotel_distance'] = $terminal->hotel_distance;
            }
        }
        // debug($hotel_array);exit();
        return $hotel_array;
    }

    function get_nearby_hotels_ajax($search = '') {
        $currentHotelCode = $search['hotel_code'];
        $hotel_list_code_tmp = $this->get_temp_hotelcode_list($search['search_id']);
        $hotel_nearby = $this->get_nearby_hotels($search['latitude'], $search['longitude'], $hotel_list_code_tmp, $currentHotelCode);

        return $hotel_nearby;
    }

    /**
     * Formate Room details
     * @param $hotel_detail
     */
    function get_formatted_checkprice_roomdetails($hotel_detail, $currency_obj, $search_id = '', $module) {
        $hotel_detail_responce_arr = $hotel_detail;
        $hotel_array = array();
        $total_price = 0;
        if (isset($hotel_detail_responce_arr['hotel'])) {
            $cancellStr = '';
            $hotel_detail_responce = $hotel_detail_responce_arr['hotel'];
            $hotel_array['hotel_code'] = $hotel_detail_responce['code'];
            $hotel_array['hotel_name'] = $hotel_detail_responce['name'];
            $hotel_array['star_rating'] = $hotel_detail_responce['categoryName'];
            $hotel_array['destination'] = $hotel_detail_responce['destinationName'];
            $hotel_array['destination_code'] = $hotel_detail_responce['destinationCode'];
            $hotel_array['zone_code'] = $hotel_detail_responce['zoneCode'];
            $hotel_array['zone'] = $hotel_detail_responce['zoneName'];
            $hotel_array['latitude'] = $hotel_detail_responce['latitude'];
            $hotel_array['longitude'] = $hotel_detail_responce['longitude'];
            $hotel_array['currency'] = $hotel_detail_responce['currency'];
            $hotel_array['checkIn'] = $hotel_detail_responce['checkIn'];
            $hotel_array['checkOut'] = $hotel_detail_responce['checkOut'];
            $cobj = new Currency(array(
                        'module_type' => 'hotel',
                        'from' => get_application_default_currency(),
                        'to' => get_application_default_currency()
                    ));
            $currency = $hotel_detail_responce_arr['hotel']['currency'];
            $currency_obj->getConversionRate(false, $currency, get_application_display_currency_preference());

            if (isset($hotel_detail_responce['rooms']) && COUNT($hotel_detail_responce['rooms']) > 0) {
                foreach ($hotel_detail_responce['rooms'] as $key => $room) {
                    $hotel_array['rooms'][$key]['room_code'] = $room['code'];
                    $hotel_array['rooms'][$key]['room_name'] = $room['name'];
                    $converted_net_original_rate = 0;
                    $AdminMarkupFare = 0;
                    $AgentMarkupFare = 0;
                    $ServiceTaxFare = 0;
                    $AgentMarkupTotalServiceTax=0;
                    foreach ($room['rates'] as $rkey => $rate) {

                        $hotel_array['rooms'][$key]['rates'][$rkey]['rateKey'] = $rate['rateKey'];
                        $hotel_array['rooms'][$key]['rates'][$rkey]['rateClass'] = $rate['rateClass'];
                        $hotel_array['rooms'][$key]['rates'][$rkey]['rateType'] = $rate['rateType'];

                        $hotel_array['rooms'][$key]['rates'][$rkey]['allotment'] = isset($rate['allotment']) && !empty($rate['allotment']) ? $rate['allotment'] : '';
                        $hotel_array['rooms'][$key]['rates'][$rkey]['rateCommentsId'] = isset($rate['rateCommentsId']) && !empty($rate['rateCommentsId']) ? $rate['rateCommentsId'] : '';
                        $hotel_array['rooms'][$key]['rates'][$rkey]['rateComments'] = isset($rate['rateComments']) && !empty($rate['rateComments']) ? $rate['rateComments'] : '';
                        $hotel_array['rooms'][$key]['rates'][$rkey]['paymentType'] = $rate['paymentType'];
                        $hotel_array['rooms'][$key]['rates'][$rkey]['boardCode'] = $rate['boardCode'];
                        $hotel_array['rooms'][$key]['rates'][$rkey]['boardName'] = $rate['boardName'];

                        $net_rate = $rate['net'];
                        $tax = $GLOBALS ['CI']->hotel_model->get_tax();
                        $tax = $tax['data'];


                        $converted_net_rate = $currency_obj->get_currency($net_rate, false, false, false, 1);

                        $currency = $converted_net_rate['default_currency'];
                        if ($module == 'b2c') {
                            $converted_min_rate_with_admin_amrkup = $currency_obj->get_currency($net_rate, true, false, false, true, 1);
                            $markup_total_fare_ps = $converted_min_rate_with_admin_amrkup['default_value'] - $converted_net_rate['default_value'];


                            $service_tax_with_rate = (($converted_min_rate_with_admin_amrkup['default_value'] / 100) * $tax);
                            $net_rate = $converted_min_rate_with_admin_amrkup['default_value'] + $service_tax_with_rate;
                            $AgentMarkupAmount = 0;
                        } else {

                            $converted_net_original_rate+= $converted_net_rate['default_value'];
                            $markup_total_fare_p = $converted_net_rate['default_value'];
                            $adminmarkup = $GLOBALS ['CI']->hotel_model->getadmin_markup();
                            //debug($markup_total_fare_p);
                            if ($adminmarkup['status'] == true) {
                                //debug($adminmarkup);exit;
                                $data = $adminmarkup['data'];

                                foreach ($data as $k => $v) {
                                    $value_type = $v['value_type'];
                                    $admin_mark_value = $v['value'];

                                    if ($value_type == 'percentage') {
                                        $markup_total_fare_ps = (($markup_total_fare_p / 100) * $admin_mark_value);
                                        $AdminMarkupFare+= $markup_total_fare_ps;
                                        $markup_total_fare_p = round($markup_total_fare_p + $markup_total_fare_ps);
                                    } else if ($value_type == 'plus') {
                                        $markup_total_fare_ps = ($admin_mark_value);
                                        $AdminMarkupFare+= $markup_total_fare_ps;
                                        # Sending the details to down
                                        //  $AdminMarkupFare+=$markup_total_fare_ps;
                                        $markup_total_fare_p = round($markup_total_fare_p + $markup_total_fare_ps);
                                    } else {
                                        $markup_total_fare_p = $markup_total_fare_p;
                                        $AdminMarkupFare = 0;
                                    }
                                }
                            }
                            
                            # Admin Service Tax Calculation
                            
                            
                            $service_tax_with_rate = (($markup_total_fare_p / 100) * $tax);
                            $ServiceTaxFare+=$service_tax_with_rate;
                           
                            

                            # Agent Markup Calculation
                            $AgentMarkup = $GLOBALS ['CI']->hotel_model->get_hotel_user_markup_details();
                            if (isset($AgentMarkup['markup'][0])) {
                                $AgentMarkup_Type = $AgentMarkup['markup'][0];
                                if ($AgentMarkup_Type['value_type'] == "percentage") {
                                    $AgentMarkupAmount = (($markup_total_fare_p * $AgentMarkup_Type['value']) / 100);
                                    $AgentMarkupFare+=$AgentMarkupAmount;
                                    $markup_total_fare_p+=$AgentMarkupAmount;
                                } else {
                                    $AgentMarkupAmount = $AgentMarkup_Type['value'];
                                    $AgentMarkupFare+=$AgentMarkupAmount;
                                    $markup_total_fare_p+=$AgentMarkupAmount;
                                }
                            }
                          
                           
                            
                           
                            
                            
                            # Calculation for Agent service Tax
                            
                            $AgentServiceTax=(($AgentMarkupAmount * $tax)/100);
                            $AgentMarkupTotalServiceTax+=$AgentServiceTax;
                            
                              $net_rate = $markup_total_fare_p + $service_tax_with_rate + $AgentServiceTax;
                            
                        }


                        $hotel_array['currency'] = $currency;
                        $hotel_array['rooms'][$key]['rates'][$rkey]['IndividualRoomXMLRate'] = $converted_net_rate['default_value'];
                        $hotel_array['rooms'][$key]['rates'][$rkey]['net'] = $net_rate;
                        $hotel_array['rooms'][$key]['rates'][$rkey]['IndividualRoomAdminMarkup'] = @$markup_total_fare_ps;
                        $hotel_array['rooms'][$key]['rates'][$rkey]['IndividualRoomAgentMarkup'] = @$AgentMarkupAmount;
                        $hotel_array['rooms'][$key]['rates'][$rkey]['IndividualRoomAgentPrice'] = (($converted_net_rate['default_value']+@$markup_total_fare_ps +@$service_tax_with_rate));
                        $hotel_array['rooms'][$key]['rates'][$rkey]['IndividualRoomAgentServiceTax'] = @$AgentServiceTax;
                        $hotel_array['rooms'][$key]['rates'][$rkey]['IndividualRoomServiceTax'] = @$service_tax_with_rate;





                        $total_price = $total_price + $net_rate;

                        /* cancellation policies */

                        if (isset($rate['cancellationPolicies']) && !empty($rate['cancellationPolicies'])) {
                            $prevDate = '';
                            $cntcncle = 1;
                            $CancelAmountFare = array();
                            foreach ($rate['cancellationPolicies'] as $cKey => $policy) {
                                $fromDate = date('d-m-Y A', strtotime($policy['from']));

                                $cncel_rate = $policy['amount'];
                                $converted_cncel_rate = $currency_obj->get_currency($cncel_rate, false, false, false, 1);
                                if ($module == 'b2c') {
                                    $converted_min_rate_with_admin_amrkup_can = $currency_obj->get_currency($cncel_rate, true, false, false, true, 1);
                                    $converted_cncel_rate = $converted_min_rate_with_admin_amrkup_can['default_value'];
                                    $service_tax = (($converted_cncel_rate / 100) * $tax);
                                    $converted_cncel_rate = $converted_cncel_rate + $service_tax;
                                } else {
                                    $converted_cancel_rate = $converted_cncel_rate['default_value'];
                                    $markup_total_fare_p = $converted_cancel_rate;
                                    $adminmarkup = $GLOBALS ['CI']->hotel_model->getadmin_markup();

                                    if ($adminmarkup['status'] == true) {
                                        //debug($adminmarkup);exit;
                                        $data = $adminmarkup['data'];
                                        foreach ($data as $k => $v) {
                                            $value_type = $v['value_type'];
                                            $admin_mark_value = $v['value'];

                                            if ($value_type == 'percentage') {
                                                $markup_total_fare_ps = (($markup_total_fare_p / 100) * $admin_mark_value);
                                                $markup_total_fare_p = ($markup_total_fare_p + $markup_total_fare_ps);
                                            } else if ($value_type == 'plus') {
                                                $markup_total_fare_ps = ($markup_total_fare_p + $admin_mark_value);
                                                $markup_total_fare_p = ($markup_total_fare_ps);
                                            } else {
                                                $markup_total_fare_p = $markup_total_fare_p;
                                            }
                                        }
                                    }

                                    $converted_cncel_rate = $markup_total_fare_p;
                                    $service_tax = ((($converted_cncel_rate + @$AgentMarkupAmount) / 100) * $tax);
                                    $converted_cncel_rate = $converted_cncel_rate + $service_tax;
                                  
                                }

                                $cncel_rate = number_format(($converted_cncel_rate + $AgentMarkupAmount),2);
                                $myArray = explode('PM', $fromDate);
                                $m = date('Y-m-d H:i:s', strtotime($myArray[0])); //debug($m);
                                $current_date = db_current_datetime();
                                if ($m > $current_date) {
                                    $fromDate = app_friendly_absolute_date($m);
                                    $str = ($cntcncle == 1) ? $room['name'] . ' : </br> From ' . $fromDate . ', ' . $hotel_array['currency'] . ' ' . $cncel_rate . ' will be charged as cancellation penalty.' : 'From ' . $fromDate . ', ' . $hotel_array['currency'] . ' ' . $cncel_rate . ' will be charged as cancellation penalty.';
                                } else {
                                    //$fromDate = app_friendly_absolute_date($current_date);
                                    $str = 'Non Refundable';
                                }//debug($fromDate);
                                //$str = ($cntcncle == 1) ? $room['name'] . ' : </br> From ' . $fromDate . ', ' .$hotel_array['currency'].' '. $cncel_rate . ' will be charged as cancellation penalty.' : 'From ' . $fromDate . ', ' .$hotel_array['currency'].' '. $cncel_rate . ' will be charged as cancellation penalty.';


                                $cancellStr = '<li>' . $cancellStr . $str . '<li>';
                                $prevDate = $fromDate;
                                $CancelAmountFare[] = $cncel_rate;
                                $cntcncle++;
                            }
                        }
                        //debug($cancellStr);
                        //exit;
                        /* texes */
                        if (isset($room['taxes']['taxes']) && !empty($room['taxes']['taxes'])) {
                            foreach ($room['taxes']['taxes'] as $tKey => $tax) {
                                $hotel_array['rooms'][$key]['rates'][$rkey]['tax'][$tKey]['included'] = $tax['included'];
                                $hotel_array['rooms'][$key]['rates'][$rkey]['tax'][$tKey]['percent'] = $tax['percent'];
                            }
                        }

                        /* offers */
                        if (isset($room['offers']) && !empty($room['offers'])) {
                            foreach ($room['offers'] as $oKey => $offer) {
                                $offer_rate = $offer['amount'];
                                $converted_offer_rate = $currency_obj->get_currency($offer_rate, false, false, false, 1);
                                if (isset($converted_offer_rate['default_value'])) {
                                    $offer_rate = $converted_offer_rate['default_value'];
                                }

                                $hotel_array['rooms'][$key]['rates'][$rkey]['offer'][$oKey]['code'] = $offer['code'];
                                $hotel_array['rooms'][$key]['rates'][$rkey]['offer'][$oKey]['offer_name'] = $offer['name'];
                                $hotel_array['rooms'][$key]['rates'][$rkey]['offer'][$oKey]['offer_amount'] = $offer_rate;
                            }
                        }
                        $hotel_array['rooms'][$key]['rates'][$rkey]['rooms'] = $rate['rooms'];
                        $hotel_array['rooms'][$key]['rates'][$rkey]['adults'] = $rate['adults'];
                        $hotel_array['rooms'][$key]['rates'][$rkey]['children'] = $rate['children'];
                        $hotel_array['rooms'][$key]['rates'][$rkey]['CustomCanPolicy'] = $CancelAmountFare;
                    }
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
            /* get hotel static images */
            $hotel_static_images_arr = $this->get_static_hotel_singleimage($hotel_array['hotel_code']);
            $hotel_array['imageCode'] = isset($hotel_static_images_arr) && !empty($hotel_static_images_arr) > 0 ? $hotel_static_images_arr->imageCode : '';
            $hotel_array['imagePath'] = isset($hotel_static_images_arr) && !empty($hotel_static_images_arr) > 0 ? $hotel_static_images_arr->imagePath : '';
            $hotel_array['small_image_baseUrl'] = 'http://photos.hotelbeds.com/giata/';


            $hotel_array['cancellationString'] = '<ul>' . $cancellStr . '</ul>';

            $hotel_array['XMLPrice'] = $converted_net_original_rate;
            if (isset($markup_total_fare_ps)) {
                $hotel_array['AdminMarkup'] = $AdminMarkupFare;
            }
            if (isset($AgentMarkupAmount)) {
                $hotel_array['Agentmarkup'] = $AgentMarkupFare;
            }
            if (isset($service_tax_with_rate)) {
                $hotel_array['ServiceTax'] = $ServiceTaxFare;
            }
            if(isset($AgentMarkupTotalServiceTax))
            {
                $hotel_array['AgentMarkupTotalServiceTax']=$AgentMarkupTotalServiceTax;
            }
            $hotel_array['total_price'] = $total_price;

            // $hotel_array['CanPolicyAmountAll'] = $CancelAmountFare;
        }
        // debug($hotel_array);exit;
        return $hotel_array;
    }

    /**/

    function get_static_hotel_singleimage($hotel_code) {
        $CI = & get_instance();
        $h_cond = 'hotel_code = ' . $CI->db->escape($hotel_code);
        $cols = 'image_type_code AS imageCode, image_path as imagePath, room_code as roomCode, room_type as roomType';

        $query = ' SELECT ' . $cols . ' FROM hb_hotel_images AS h WHERE ' . $h_cond . ' ORDER BY `order` ASC LIMIT 1';
        $data = $CI->db->query($query)->row();
        return $data;
    }

    /**
     * Get Filter Params - fliter_params
     */
    function format_search_response($hl, $cobj, $sid, $module = 'b2c', $fltr = array()) {

        //debug($hl);exit;
        $level_one = true;
        $current_domain = true;
        if ($module == 'b2c') {
            $level_one = false;
            $current_domain = true;
        } else if ($module == 'b2b') {
            $level_one = true;
            $current_domain = true;
        }
        $h_count = 0;
        $HotelResults = array();
        if (isset($fltr ['hl']) == true) {
            foreach ($fltr ['hl'] as $tk => $tv) {
                $fltr ['hl'] [urldecode($tk)] = strtolower(urldecode($tv));
            }
        }

        // Creating closures to filter data
        $check_filters = function ($hd) use($fltr) {

                    //_acc type
                    $any_facility = function ($cstr, $c_list) {
                                foreach ($c_list as $k => $v) {
                                    if (stripos(($cstr), ($v)) > -1) {
                                        return true;
                                    }
                                }
                            };

                    if (
                            (valid_array(@$fltr ['hl']) == false ||
                            (valid_array(@$fltr ['hl']) == true && in_array(strtolower($hd ['location']), $fltr ['hl']))) &&
                            (valid_array(@$fltr ['_sf']) == false || (valid_array(@$fltr ['_sf']) == true && in_array($hd ['star_rating'], $fltr ['_sf']))) &&
                            (@$fltr ['min_price'] <= ceil($hd ['price']) && (@$fltr ['max_price'] != 0 && @$fltr ['max_price'] >= floor($hd ['price']))) &&
                            ((string) $fltr ['dealf'] == 'false' || empty($hd ['HotelPromotion']) == false) &&
                            (empty($fltr ['hn_val']) == true || (empty($fltr ['hn_val']) == false &&
                            stripos(strtolower($hd ['name']), (urldecode($fltr ['hn_val']))) > - 1)) &&
                            (valid_array(@$fltr ['_fac']) == false ||
                            (valid_array(@$fltr ['_fac']) == true && $any_facility($hd ['facility_cstr'], $fltr ['_fac']))
                            ) &&
                            (valid_array(@$fltr ['at']) == false ||
                            (valid_array(@$fltr ['at']) == true && in_array(( $hd ['accomodation_cstr']), $fltr ['at'])))
                    ) {
                        return true;
                    } else {
                        return false;
                    }
                };
        //debug($check_filters);
        $hc = 0;
        $frc = 0;

        $currency_obj = new Currency(array('module_type' => 'hotel', 'from' => get_application_default_currency(), 'to' => get_application_display_currency_preference()));

        foreach ($hl['hotel_list'] as $hr => $hd) {
            $TotalAmount = 0;
            $ServiceTax = 0;
            $hc++;


            $price = $currency_obj->get_currency($hd ['price'], true, true, false, true, 1);

            if ($module == 'b2c') {

                $hd ['price'] = $hd['price'];
                $hd ['currency'] = $price['default_currency'];
            } else {

                $hd ['price'] = $hd['price'];
                $hd ['currency'] = $price['default_currency'];
            }

            // filter after initializing default data and adding markup
            if (valid_array($fltr) == true && $check_filters($hd) == false) {
                continue;
                //debug($check_filters);exit;
            }
            $HotelResults [$hr] = $hd;
            $frc++;
        }
        //   echo 'sxsx', debug($HotelResults);exit;

        $hl ['hotel_list'] = $HotelResults;
        $hl ['source_result_count'] = $hc;
        $hl ['filter_result_count'] = $frc;
        return $hl;
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
        $hl ['HotelSearchResult'] = array_slice($hl ['HotelSearchResult'], $offset, $limit);

        return $hl;
    }

    /* Himani
     * get_all_hotel_details to get all static data of hotel
     * */

    function get_all_hotel_static_data($url) {
       
        $details = $GLOBALS ['CI']->api_interface->get_json_response_getrequest($url,$this->json_header());
        $fileName = BASEPATH . '../b2c/custom/temp/responceSec.json';
        
        file_put_contents($fileName,$details);
        //$details = json_decode($details, true);
        
       return $details;
      
    }
   

    /**
     * Jaganath
     * Formats the Room List
     */
    function format_room_list($room_list) {
        $formatted_room_list = array();
        foreach ($room_list as $k => $v) {
            foreach ($v['rates'] as $rate_k => $rate_v) {
                $formatted_room_list[$rate_v['rateKey']] = $rate_v;
                $formatted_room_list[$rate_v['rateKey']]['room_code'] = $v['room_code'];
                $formatted_room_list[$rate_v['rateKey']]['room_name'] = $v['room_name'];
                $formatted_room_list[$rate_v['rateKey']]['facilities'] = @$v['facilities'];
                $formatted_room_list[$rate_v['rateKey']]['img'] = @$v['img'];
            }
        }
        return $formatted_room_list;
    }

    /*
     * Himani
     * Formate room combination list
     * */

    function format_room_combination_list($room_list, $module) {
        $formatted_room_list = array();
        $min_price = 0;
        foreach ($room_list as $rate_k => $rate_v) {
            $roomNo = 0;
            $no_of_adults = 0;
            $no_of_child = 0;
            $net_price = 0;
            $currency_obj = new Currency(array('module_type' => 'hotel', 'from' => get_application_default_currency(), 'to' => get_application_display_currency_preference()));
            foreach ($rate_v as $key => $details) {
                $formatted_room_list[$rate_k][$key]['rateKey'] = $details['rateKey'];
                $formatted_room_list[$rate_k][$key]['rateClass'] = $details['rateClass'];
                $formatted_room_list[$rate_k][$key]['rateType'] = $details['rateType'];
                $formatted_room_list[$rate_k][$key]['allotment'] = isset($details['allotment']) && !empty($details['allotment']) ? $details['allotment'] : '';
                $formatted_room_list[$rate_k][$key]['rateCommentsId'] = isset($details['rateCommentsId']) && !empty($details['rateCommentsId']) ? $details['rateCommentsId'] : '';
                $formatted_room_list[$rate_k][$key]['boardCode'] = $details['boardCode'];
                $formatted_room_list[$rate_k][$key]['boardName'] = $details['boardName'];
                $formatted_room_list[$rate_k][$key]['cancellationPolicies'] = isset($details['cancellationPolicies']) && !empty($details['cancellationPolicies']) ? $details['cancellationPolicies'] : '';
                $formatted_room_list[$rate_k][$key]['rooms'] = $details['rooms'];
                $formatted_room_list[$rate_k][$key]['adults'] = $details['adults'];
                $formatted_room_list[$rate_k][$key]['children'] = $details['children'];
                $formatted_room_list[$rate_k][$key]['code'] = $details['code'];
                $formatted_room_list[$rate_k][$key]['name'] = $details['name'];
                //debug($details['net']);
                //$tax= $GLOBALS ['CI']->hotel_model->get_tax();
                //$tax=$tax['data'];


                $net = $details['net']; //$currency_obj->get_currency ( $details['net'], true, false, true, 1 );
                //$service_tax = (($net / 100) * $tax);
                //$net = $net + $service_tax;
                $formatted_room_list[$rate_k][$key]['net'] = $net; // $net['default_value'];

                $roomNo += $details['rooms'];
                $no_of_adults += $details['adults'];
                $no_of_child += $details['children'];
                $net_price = $net; //$net['default_value'];
                //$formatted_room_list[$rate_v['rateKey']]['facilities'] = $v['facilities'];
                //$formatted_room_list[$rate_v['rateKey']]['img'] = $v['img'];
            }
            $formatted_room_list[$rate_k]['adults'] = $no_of_adults;
            $formatted_room_list[$rate_k]['children'] = $no_of_child;
            $formatted_room_list[$rate_k]['room'] = $roomNo;
            $formatted_room_list[$rate_k]['net'] = $net_price;
            $min_price = ($min_price == 0) ? $net_price : ($net_price < $min_price) ? $net_price : $min_price;
        }//exit;
        foreach ($formatted_room_list as $f_key => $room_list_array) {
            $sorted_room_list['list'] = $this->order_array_num($formatted_room_list, 'net');
        }
        $sorted_room_list['min_price'] = $min_price;
        return $sorted_room_list;
    }

    /*
     * sort array with numeric value
     * */

    private function order_array_num($array, $key, $order = "ASC") {
        $tmp = array();
        foreach ($array as $akey => $array2) {
            $tmp[$akey] = $array2[$key];
        }

        if ($order == "DESC") {
            arsort($tmp, SORT_NUMERIC);
        } else {
            asort($tmp, SORT_NUMERIC);
        }

        $tmp2 = array();
        foreach ($tmp as $key => $value) {
            $tmp2[$key] = $array[$key];
        }

        return $tmp2;
    }

    public function format_booking_response($book_response) {
        $book_response = $book_response['bookingRS']['booking'];
        $data = array();
        $data['status'] = SUCCESS_STATUS;
        $data['data'] = array();
        $data['message'] = array();
        $booking_details = array();
        $hotel_details = array();
        $room_details = array();
        $holder = $book_response['holder']['@attributes'];
        $room_details = $this->format_booked_room_details($book_response['hotel']['rooms']['room']);
        $hotel_details = $book_response['hotel']['@attributes']; //Hotel Details
        $hotel_details['supplier'] = $book_response['hotel']['supplier']['@attributes']; //Supplier Details
        $hotel_details['rooms'] = $room_details; //Room Details
        //*** Assigning All details to booking details Array ***//
        $booking_details = $book_response['@attributes'];
        $booking_details['lead_pax'] = $holder;
        $booking_details['hotel'] = $hotel_details;
        $data['data']['booking_details'] = $booking_details;
        return $data;
    }

    function format_booked_room_details($room_data) {
        $core_room_details = force_multple_data_format($room_data);
        $room_details = array();
        foreach ($core_room_details as $r_k => $r_v) {
            //Passenger Details
            $paxes = force_multple_data_format($r_v['paxes']['pax']);
            foreach ($paxes as $p_k => $p_v) {//Running Loop To Remove the "@attributes"
                $paxes[$p_k] = $p_v['@attributes'];
            }
            //Rate Details
            $temp_rate = $r_v['rates']['rate'];
            $temp_rate['cancellationPolicies'] = force_multple_data_format($temp_rate['cancellationPolicies']['cancellationPolicy']);
            $temp_rate['rateBreakDown'] = force_multple_data_format(@$temp_rate['rateBreakDown']['rateSupplements']['rateSupplement']);
            $rate = $temp_rate['@attributes'];
            foreach ($temp_rate['cancellationPolicies'] as $c_k => $c_v) {//Removing "@attributes"
                $rate['cancellationPolicies']['cancellationPolicy'][$c_k] = $c_v['@attributes'];
            }
            $rateBreakDown = array();
            if (isseT($temp_rate['rateBreakDown']) && valid_array($temp_rate['rateBreakDown'])) {
                foreach ($temp_rate['rateBreakDown'] as $fb_k => $fb_v) {//Removing "@attributes"
                    $rateBreakDown[$fb_k] = $fb_v['@attributes'];
                }
            }

            $rates = $rate;
            $rates['rateBreakDown'] = $rateBreakDown;
            //*** Assigning Room Details **//
            $room_details[$r_k] = $r_v['@attributes'];
            $room_details[$r_k]['rates'] = $rates;
            $room_details[$r_k]['paxes'] = $paxes;
        }
        return $room_details;
    }

    /*
    * Sneha
    * to get the static information for the hotel 
    */
    function get_hotel_static_detail($search_data, $hotel_code)
    {
        $response ['data'] = array();
        $response ['status'] = false;
        $hotel_static_request = $this->get_hotel_static_detail_request($hotel_code);

        if ($hotel_static_request ['status']) {

            $GLOBALS ['CI']->custom_db->generate_static_response ($hotel_static_request ['data'] ['request'], 'REZLIVE hotel static detail request' );

             $hotel_details_response = $GLOBALS ['CI']->api_interface->xml_post_request($hotel_static_request ['data'] ['service_url'], ('XML='.urlencode($hotel_static_request ['data'] ['request'])), $this->xml_header());
            
            $GLOBALS ['CI']->custom_db->generate_static_response ($hotel_details_response, 'REZLIVE hotel static detail response' );

            $hotel_details_response = Converter::createArray($hotel_details_response);
            if ($this->valid_hotel_details($hotel_details_response)) {
                $response ['data'] = $hotel_details_response['HotelDetailsResponse']['Hotels'];
                $response ['status'] = true;
            } else {
                // Need the complete data so that later we can use it for redirection
                $response ['data'] = $hotel_details_response;
            }
        }
        return $response;
    }

    private function get_hotel_static_detail_request($hotel_code) {
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

    /*
    * Sneha
    *  to get the room cancellation  policy detaiils
    */
    function get_room_cancellation_policy($search_params, $hotel_code, $room_details, $module)
    {   
        $response ['data'] = array();
        $response ['status'] = false;
        $cancellation_policy_request = $this->get_room_cancellation_policy_request($search_params['data'], $hotel_code, $room_details);
        if ($cancellation_policy_request ['status']) {

            $GLOBALS ['CI']->custom_db->generate_static_response ($cancellation_policy_request ['data'] ['request'], 'REZLIVE cancellation policy request' );

             $cancellation_policy_response = $GLOBALS ['CI']->api_interface->xml_post_request($cancellation_policy_request ['data'] ['service_url'], ('XML='.urlencode($cancellation_policy_request ['data'] ['request'])), $this->xml_header());
            
            $GLOBALS ['CI']->custom_db->generate_static_response ($cancellation_policy_response, 'REZLIVE cancellation policy response' );
            $cancellation_policy_response = Converter::createArray($cancellation_policy_response);
            if (isset($cancellation_policy_response['CancellationPolicyResponse']) 
                && !empty($cancellation_policy_response['CancellationPolicyResponse']) 
                && isset($cancellation_policy_response['CancellationPolicyResponse']['CancellationInformations'])
                && !isset($cancellation_policy_response['CancellationPolicyResponse']['error'])) {
                $cancellation_policy_resp = $this->format_room_cancellation_policy($cancellation_policy_response['CancellationPolicyResponse'], $module);
              /*  $cancellation_policy_resp = array();
                if(!empty($cancellation_policy_response['CancellationPolicyResponse']['CancellationInformations']['CancellationInformation']) && count($cancellation_policy_response['CancellationPolicyResponse']['CancellationInformations']['CancellationInformation']) > 0) {
                    $cancelinformation =force_multple_data_format($cancellation_policy_response['CancellationPolicyResponse']['CancellationInformations']['CancellationInformation']);
                    foreach ($cancelinformation as $cpk => $cpinfo) {
                        $policy = 'For cancellation between ' . $cpinfo['StartDate'] . ' to ' . $cpinfo['EndDate'];
                        if($cpinfo['ChargeType'] == 'Percentage') {
                            $policy .= ', ' .ceil($cpinfo['ChargeAmount']) . '% ' . ' will be charged.';
                        } else {

                            $policy .= ', ' .$cpinfo['Currency']. ' ' . ceil($cpinfo['ChargeAmount']) . ' will be charged.';
                        }
                        $cancellation_policy_resp[] = $policy;
                    }
                    if(!empty($cancellation_policy_response['CancellationPolicyResponse']['CancellationInformations']['Info'])) {

                    $cancellation_policy_resp[] = $cancellation_policy_response['CancellationPolicyResponse']['CancellationInformations']['Info'];
                    }

                }*/

                $response ['data'] = $cancellation_policy_resp;
                $response ['status'] = true;
            } else {
                $response ['data'] = $cancellation_policy_response;
            }
        }

        return $response;
    }

    /*
    Sneha 
    Get formatted room cancellation_policy
    */
    public function format_room_cancellation_policy($cancellation_policy_response, $module)
    {
        //Currency Preference
        $currency_obj = new Currency(array('module_type' => 'hotel', 'from' => get_application_default_currency(), 'to' => get_application_display_currency_preference()));

         $cancellation_policy_resp = array();
                if(!empty($cancellation_policy_response['CancellationInformations']['CancellationInformation']) && count($cancellation_policy_response['CancellationInformations']['CancellationInformation']) > 0) {
                    $cancelinformation =force_multple_data_format($cancellation_policy_response['CancellationInformations']['CancellationInformation']);
                    foreach ($cancelinformation as $cpk => $cpinfo) {
                        $policy = 'For cancellation between ' . $cpinfo['StartDate'] . ' to ' . $cpinfo['EndDate'];
                        if($cpinfo['ChargeType'] == 'Percentage') {
                            $policy .= ', ' .ceil($cpinfo['ChargeAmount']) . '% ' . ' will be charged.';
                        } else {
                            $amount = $cpinfo['ChargeAmount'];
                            $currency = $cpinfo['Currency'];

                            if($module == 'b2c') {
                                $prc = $currency_obj->get_currency($amount,true,false,true, 1);
                                $amount = ceil($prc['default_value']);
                                $currency = $prc['default_currency'];
                            } elseif ($module == 'b2b') {
                                $prc = $currency_obj->get_currency($amount,true,true,true, 1);
                                $amount = ceil($prc['default_value']);
                                $currency = $prc['default_currency'];
                            }

                            $policy .= ', ' .$currency. ' ' . ceil($amount) . ' will be charged.';
                        }
                        $cancellation_policy_resp[] = $policy;
                    }
                    if(!empty($cancellation_policy_response['CancellationInformations']['Info'])) {

                    $cancellation_policy_resp[] = $cancellation_policy_response['CancellationInformations']['Info'];
                    }

                }
        return $cancellation_policy_resp;
    }

    private function get_room_cancellation_policy_request($search_params, $hotel_code, $room_details)
    {
        $response ['status'] = true;
        $response ['data'] = array();
        $request = '';

        // request to get the cancellation policy information
        if( isset($hotel_code) && !empty($hotel_code) ) {
            // $url = json_decode(file_get_contents('https://tools.keycdn.com/geo.json'));
            // '.$url->data->geo->country_code.'
            $city_code[0]['city_code'] = $search_params['rz_city_code'];
            $city_code[0]['country_code'] = $search_params['rz_country_code'];
            $request .= '<?xml version="1.0"?><CancellationPolicyRequest><Authentication><AgentCode>'.$this->agent_id.'</AgentCode><UserName>'.$this->username.'</UserName><Password>'.$this->password.'</Password></Authentication>';

            $request .= '<ArrivalDate>'. (date('d/m/Y', strtotime($search_params['from_date']))) .'</ArrivalDate><DepartureDate>'. (date('d/m/Y', strtotime($search_params['to_date']))) .'</DepartureDate><HotelId>'.$hotel_code.'</HotelId><CountryCode>'.$city_code[0]['country_code'].'</CountryCode><City>'.$city_code[0]['city_code'].'</City><GuestNationality>IN</GuestNationality><RoomDetails><RoomDetail><BookingKey>'.$room_details['BookingKey'].'</BookingKey><Adults>'.$room_details['Adults'].'</Adults><Children>'.$room_details['Children'].'</Children>';

            if (count($room_details['Children']) > 0)   
            {
                $request .= '<ChildrenAges>'.$room_details['ChildrenAges'].'</ChildrenAges>';
            }

            $request .= '<Type>'.$room_details['Type']['@cdata'].'</Type></RoomDetail></RoomDetails></CancellationPolicyRequest>';
        }

        $response ['data'] ['service_url'] = $this->service_url . '/getcancellationpolicy';
        $response ['data'] ['request'] = $request;
        $response ['status'] = SUCCESS_STATUS;
        return $response;
    }


    /*
    * To get the rate and cancellatiopn policy changes if any
    * before going to booking page
    */
    function pre_booking_check($search_params, $hotel_details)
    {
        $response ['data'] = array();
        $response ['status'] = false;
        // debug($search_params);
        // debug($hotel_details);
       $pre_booking_request = $this->pre_booking_check_request($search_params['data'], $hotel_details);
       if ($pre_booking_request ['status']) {
            $GLOBALS ['CI']->custom_db->generate_static_response ($pre_booking_request ['data'] ['request'], 'REZLIVE pre-booking check request' );

             $pre_booking_response = $GLOBALS ['CI']->api_interface->xml_post_request($pre_booking_request ['data'] ['service_url'], ('XML='.urlencode($pre_booking_request ['data'] ['request'])), $this->xml_header());
            
            $GLOBALS ['CI']->custom_db->generate_static_response ($pre_booking_response, 'REZLIVE pre-booking check response' );
            $pre_booking_response = Converter::createArray($pre_booking_response);

            // debug($pre_booking_response);exit;

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

       return $response;

    }

    private function pre_booking_check_request($search_params, $hotel_details)
    {
        $response ['status'] = true;
        $response ['data'] = array();
        $request = '';

        if( isset($search_params) && !empty($search_params) ) {
            
            $city_code[0]['city_code'] = $search_params['rz_city_code'];
            $city_code[0]['country_code'] = $search_params['rz_country_code'];
            $request .= '<?xml version="1.0"?><PreBookingRequest><Authentication><AgentCode>'.$this->agent_id.'</AgentCode><UserName>'.$this->username.'</UserName><Password>'.$this->password.'</Password></Authentication>';
            $request .= '<PreBooking>
            <SearchSessionId>'.$hotel_details['token_data']['search_session_id'].'</SearchSessionId>
            <ArrivalDate>'. (date('d/m/Y', strtotime($search_params['from_date']))) .'</ArrivalDate>
            <DepartureDate>'. (date('d/m/Y', strtotime($search_params['to_date']))) .'</DepartureDate>
            <GuestNationality>'.$hotel_details['token_data']['guest_nationality'].'</GuestNationality>
            <CountryCode>'.$city_code[0]['country_code'].'</CountryCode>
            <City>'.$city_code[0]['city_code'].'</City>
            <HotelId>'.$hotel_details['hotel_code'].'</HotelId>
            <Currency>'.$hotel_details['token_data']['currency'].'</Currency>
            <RoomDetails>
            <RoomDetail>';
            // if(!empty($hotel_details['token_data']['rooms'][$hotel_details['rateKey'][0]]['rates']['room_description'])) {

                // $request .= '<Type>'.$hotel_details['token_data']['rooms'][$hotel_details['rateKey'][0]]['rates']['room_description'].'</Type>';
            // } else {
                $request .= '<Type>'.$hotel_details['token_data']['rooms'][$hotel_details['rateKey'][0]]['rates']['name'].'</Type>';
            // }
            $request .= '<BookingKey>'.$hotel_details['token_data']['rooms'][$hotel_details['rateKey'][0]]['rates']['booking_key'].'</BookingKey>
            <Adults>'.$hotel_details['token_data']['rooms'][$hotel_details['rateKey'][0]]['rates']['adults_r'].'</Adults>
            <Children>'.$hotel_details['token_data']['rooms'][$hotel_details['rateKey'][0]]['rates']['children_r'].'</Children>';

            if (count($hotel_details['token_data']['rooms'][$hotel_details['rateKey'][0]]['rates']['children']) > 0)   
            {
                $request .= '<ChildrenAges>'.$hotel_details['token_data']['rooms'][$hotel_details['rateKey'][0]]['rates']['children_ages'].'</ChildrenAges>';
            }
            $request .= '<TotalRooms>'.$hotel_details['token_data']['rooms'][$hotel_details['rateKey'][0]]['rates']['rooms'].'</TotalRooms><TotalRate>'.$hotel_details['token_data']['rooms'][$hotel_details['rateKey'][0]]['rates']['net'].'</TotalRate></RoomDetail></RoomDetails></PreBooking></PreBookingRequest>';
        }

        $response ['data'] ['service_url'] = $this->service_url . '/prebook';
        $response ['data'] ['request'] = $request;
        $response ['status'] = SUCCESS_STATUS;
        return $response;
    }

    function get_booking_status($tcr_status)
    {
        $booking_status = '';
        switch ($tcr_status) {
           /* case 'Rejected':
                $booking_status = 'BOOKING_HOLD';               
                break;*/
            case 'Confirmed':
                $booking_status = 'BOOKING_CONFIRMED';
                break;
            default:
                $booking_status = 'BOOKING_FAILED';
                break;
        }
        return $booking_status;
    }
    function filter_summary($hl) {
        // debug($hl);
        $h_count = 0;
        $filt ['p'] ['max'] = false;
        $filt ['p'] ['min'] = false;
        $filt ['loc'] = array ();
        $filt ['star'] = array ();
        $filt ['currency'] = '';
        $filters = array ();
        foreach ( $hl ['hotel_list'] as $hr => $hd ) {
            $filt ['currency'] = $hd['currency'];
            // filters
            $StarRating = intval ( @$hd ['star_rating'] );
            $HotelLocation = $hd ['destination_name'];
            $AccomodationType = $hd['accomodation_type'];

            if (isset ( $filt ['star'] [$StarRating] ) == false) {
                $filt ['star'] [$StarRating] ['c'] = 1;
                $filt ['star'] [$StarRating] ['v'] = $StarRating;
            } else {
                $filt ['star'] [$StarRating] ['c'] ++;
            }

            if (($filt ['p'] ['max'] != false && $filt ['p'] ['max'] < $hd ['price']) || $filt ['p'] ['max'] == false) {
                $filt ['p'] ['max'] = roundoff_number ( $hd ['price']);
            }

            if (($filt ['p'] ['min'] != false && $filt ['p'] ['min'] > $hd ['price']) || $filt ['p'] ['min'] == false) {
                $filt ['p'] ['min'] = floor($hd ['price']);
            }
            $hloc = ucfirst ( strtolower ( $HotelLocation ) );
            if (isset ( $filt ['loc'] [$hloc] ) == false) {
                $filt ['loc'] [$hloc] ['c'] = 1;
                $filt ['loc'] [$hloc] ['v'] = $hloc;
            } else {
                $filt ['loc'] [$hloc] ['c'] ++;
            }

            $a_type =  $AccomodationType ;
            if (isset ( $filt ['a_type'] [$a_type] ) == false) {
                $filt ['a_type'] [$a_type] ['c'] = 1;
                $filt ['a_type'] [$a_type] ['v'] = $a_type;
            } else {
                $filt ['a_type'] [$a_type] ['c'] ++;
            }

            
            if (empty($hd['facility']) == false) {
                // debug($hd['facility']);exit;
               /* foreach ($hd['facility'] as $fk => $fv) {
                    if (isset($filt['facility'][$fv['fc']]) == false) {
                        $filt ['facility'] [$fv['fc']] ['c'] = 1;
                        $filt ['facility'] [$fv['fc']] ['v'] = $fv['name'];
                        $filt ['facility'] [$fv['fc']] ['icon'] = $fv['icon_class'];
                        $filt ['facility'] [$fv['fc']] ['cstr'] = $fv['cstr'];
                    } else {
                        $filt ['facility'] [$fv['fc']] ['c'] ++;
                    }
                }*/

                /*$fd = explode('#', $fv);
                        if(boolval($fd[1]) == true) {
                            $filt ['facility'] [$fv['fc']] ['c'] = 1;
                            $filt ['facility'] [$fv['fc']] ['v'] = $fd[0];
                            $filt ['facility'] [$fv['fc']] ['icon'] = $fv['icon_class'];
                            $filt ['facility'] [$fv['fc']] ['cstr'] = $fv['cstr'];
                            
                        }*/
            }

            $filters ['data'] = $filt;
            $h_count ++;
        }

        ksort ( $filters ['data'] ['loc'] );
        $filters ['hotel_count'] = $h_count;
        return $filters;
    }

}
