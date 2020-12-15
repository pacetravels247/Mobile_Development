<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once BASEPATH . 'libraries/Common_Api_Grind.php';

/**
 *
 * @package Provab
 * @subpackage API
 * @author Balu A<abaluvijay@gmail.com>
 * @version V1
 */
class Provab_private extends Common_Api_Grind {

    private $ClientId;
    private $UserName;
    private $Password;
    private $service_url;
    private $Url;
    public $master_search_data;
    public $search_hash;

    public function __construct() {
        $this->CI = &get_instance();
        $GLOBALS ['CI']->load->library('Api_Interface');
        $GLOBALS ['CI']->load->model('transfer_model');
        $this->TokenId = $GLOBALS ['CI']->session->userdata('tb_auth_token');
        $this->set_api_credentials();
    }

    private function get_header() {
        $hotel_engine_system = $this->CI->config->item('hotel_engine_system');
        $response ['UserName'] = $this->CI->config->item($hotel_engine_system . '_username');
        $response ['Password'] = $this->CI->config->item($hotel_engine_system . '_password');
        $response ['DomainKey'] = $this->CI->config->item('domain_key');
        $response ['system'] = $hotel_engine_system;
        return $response;
    }

    private function set_api_credentials() {
        $transfer_engine_system = $this->CI->config->item('transfer_engine_system');

        $this->system = $transfer_engine_system;
        $this->UserName = $this->CI->config->item($transfer_engine_system . '_username');
        $this->Password = $this->CI->config->item($transfer_engine_system . '_password');
        $this->Url = $this->CI->config->item('transfer_url');
        $this->ClientId = $this->CI->config->item('domain_key');
    }

    function credentials($service) {

        switch ($service) {
            case 'Search' :
                $this->service_url = $this->Url . 'Search';
                break;
            case 'GetHotelImages' :
                $this->service_url = $this->Url . 'GetHotelImages';
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
                $this->service_url = $this->Url . 'UpdateHoldBooking';
                break;
        }
    }

    function get_tranfer_list($search_id = '') {
        $this->CI->load->driver('cache');
        $response['data'] = array();
        $response['status'] = true;
        $search_data = $this->search_data($search_id);

        $header_info = $this->get_header();

        $cache_search = $this->CI->config->item('cache_flight_search');
        $search_hash = $this->search_hash;
        if ($cache_search) {
            $cache_contents = $this->CI->cache->file->get($search_hash);
        }
        if ($search_data['status'] == true) {
            if ($cache_search === false || ($cache_search === true && empty($cache_contents) == true)) {
                $search_request = $this->transfer_search_request($search_data['data']);

                if ($search_request['status']) {
                    $search_response = $this->CI->api_interface->get_json_response($search_request['data']['service_url'], $search_request['data']['request'], $header_info);

                    $GLOBALS['CI']->custom_db->generate_static_response(json_encode($search_response));
                    if ($this->valid_search_result($search_response)) {
                        $response ['data'] = $search_response['Search'];
                        if ($cache_search) {
                            $cache_exp = $this->CI->config->item('cache_transfer_search_ttl');
                            $this->CI->cache->file->save($search_hash, $response ['data'], $cache_exp);
                        }
                      
                    } else {
                        $response ['status'] = false;
                    }
                }
            } else {
                $response['data'] = $cache_contents;
                $response['search_hash'] = $search_hash;
                $response['from_cache'] = true;
            }
        } else {
            $response ['status'] = false;
        }
        return $response;
    }

    function transfer_search_request($search_params) {
        $response['status'] = SUCCESS_STATUS;
        $response['data'] = array();
        /** Request to be formed for search * */
        $this->credentials('Search');
        $request_params = array();


        $request_params['FromName'] = (is_array($search_params['FromName']) ? $search_params['FromName'] : array($search_params['FromName']));
        $request_params['ToName'] = (is_array($search_params['ToName']) ? $search_params['ToName'] : array($search_params['ToName']));
        $request_params['OriginCode'] = (is_array($search_params['OriginCode']) ? $search_params['OriginCode'] : array($search_params['OriginCode']));
        $request_params['DestinationCode'] = (is_array($search_params['DestinationCode']) ? $search_params['DestinationCode'] : array($search_params['DestinationCode']));
        $request_params['FromTerminal'] = (is_array($search_params['FromTerminal']) ? $search_params['FromTerminal'] : array($search_params['FromTerminal']));
        $request_params['ToTerminal'] = (is_array($search_params['ToTerminal']) ? $search_params['ToTerminal'] : array($search_params['ToTerminal']));

        $request_params['DepartureDate'] = $search_params['DepartureDate'];
        $request_params['DepartureTime'] = $search_params['DepartureTime'];
        if ($search_params['Type'] == "circle") {
            $request_params['ReturnDate'] = $search_params['ReturnDate'];
            $request_params['ReturnTime'] = $search_params['ReturnTime'];
        }

        $request_params['AdultCount'] = $search_params['Adult'];
        $request_params['ChildCount'] = $search_params['child'];
        $request_params['AdultAge'] = $search_params['AdultAge'];
        $request_params['JourneyType'] = $search_params['Type'];



        //Converting to an array
        $response['data']['request'] = json_encode($request_params);
        $response['data']['service_url'] = $this->service_url;

        return $response;
    }

    /**
     *
     * @param array $booking_params        	
     */
    function process_booking($book_id, $booking_params) {
        $header = $this->get_header();
        $response ['status'] = FAILURE_STATUS;
        $response ['data'] = array();
    }

    public function search_data($search_id) {
        $response['status'] = true;
        $response['data'] = array();
        if (empty($this->master_search_data) == true and valid_array($this->master_search_data) == false) {
            $clean_search_details = $this->CI->transfer_model->get_safe_search_data($search_id);

            if ($clean_search_details ['status'] == true) {

                $response['status'] = true;
                // $response['data'] = $clean_search_details['data'];

                switch ($clean_search_details['data']['trip_type']) {
                    case 'oneway':
                        $response['data']['Type'] = 'OneWay';
                        $response['data']['DepartureDate'] = $clean_search_details['data']['depature'];
                        $response['data']['DepartureTime'] = $clean_search_details['data']['depature_time'];
                        break;
                    case 'circle':
                        $response['data']['Type'] = 'circle';
                        $response['data']['DepartureDate'] = $clean_search_details['data']['depature'];
                        $response['data']['DepartureTime'] = $clean_search_details['data']['depature_time'];
                        $response['data']['ReturnDate'] = $clean_search_details['data']['depature'];
                        $response['data']['ReturnTime'] = $clean_search_details['data']['depature_time'];
                        break;
                }
                $response['data']['FromName'] = $clean_search_details['data']['from'];
                $response['data']['ToName'] = $clean_search_details['data']['to'];
                $response['data']['OriginCode'] = $clean_search_details['data']['from_code'];
                $response['data']['DestinationCode'] = $clean_search_details['data']['to_code'];
                $response['data']['FromTerminal'] = $clean_search_details['data']['from_transfer_type'];
                $response['data']['ToTerminal'] = $clean_search_details['data']['to_transfer_type'];
                $response['data']['Adult'] = $clean_search_details['data']['adult'];
                $response['data']['child'] = $clean_search_details['data']['child'];
                $response['data']['AdultAge'] = $clean_search_details['data']['adult_ages'];
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
     * update markup currency and return summary
     * $attr needed to calculate number of nights markup when its plus based markup
     */
    function update_markup_currency(& $price_summary, & $currency_obj, $no_of_nights = 1, $level_one_markup = false, $current_domain_markup = true) {
        $tax_service_sum = 0;
        $tax_removal_list = array();
        $markup_list = array(
            'RoomPrice',
            'PublishedPrice',
            'PublishedPriceRoundedOff',
            'OfferedPrice',
            'OfferedPriceRoundedOff'
        );
        $markup_summary = array();
//debug($currency_obj);
        foreach ($price_summary as $__k => $__v) {

            $ref_cur = $currency_obj->force_currency_conversion($__v); // Passing Value By Reference so dont remove it!!!
            $price_summary [$__k] = $ref_cur ['default_value']; // If you dont understand then go and study "Passing value by reference"
//debug($currency_obj);
            if (in_array($__k, $markup_list)) {
                $temp_price = $currency_obj->get_currency($__v, true, $level_one_markup, $current_domain_markup, $no_of_nights);
            } else {
                $temp_price = $currency_obj->force_currency_conversion($__v);
            }
//echo 'herre'.$tax_service_sum;
// adding service tax and tax to total
            if (in_array($__k, $tax_removal_list)) {
                $markup_summary [$__k] = round($temp_price ['default_value'] + $tax_service_sum);
            } else {
                $markup_summary [$__k] = round($temp_price ['default_value']);
            }
        }
//exit;
        return $markup_summary;
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
    private function valid_search_result($search_result) {
		if (valid_array ( $search_result ) == true and isset ( $search_result ['Status'] ) == true and $search_result ['Status']  == SUCCESS_STATUS) {
			return true;
		} else {
			return false;
		}
	}

}
