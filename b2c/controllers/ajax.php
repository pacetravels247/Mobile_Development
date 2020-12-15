<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// ------------------------------------------------------------------------
/**
 * Controller for all ajax activities
 *
 * @package    Provab
 * @subpackage ajax loaders
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V1
 */
// ------------------------------------------------------------------------

class Ajax extends CI_Controller {

    private $current_module;

    public function __construct() {
        parent::__construct();
        if (is_ajax() == false) {
            //$this->index();
        }
        ob_start();
        $this->load->model('flight_model');
        $this->load->model('car_model');        
        $this->load->model('sightseeing_model');
        $this->load->model('transferv1_model');
        $this->load->library('Converter');
        $this->current_module = $this->config->item('current_module');
    }

    /**
     * index page of application will be loaded here
     */
    function index() {
        
    }

    /**
     * get city list based on country
     * @param $country_id
     * @param $default_select
     */
    function get_city_list($country_id = 0, $default_select = 0) {
        if (intval($country_id) != 0) {
            $condition = array('country' => $country_id);
            $order_by = array('destination' => 'asc');
            $option_list = $this->custom_db->single_table_records('api_city_list', 'origin as k, destination as v', $condition, 0, 1000000, $order_by);
            if (valid_array($option_list['data'])) {
                echo get_compressed_output(generate_options($option_list['data'], array($default_select)));
                exit;
            }
        }
    }

    /**
     *
     * @param $continent_id
     * @param $default_select
     * @param $zone_id
     */
    function get_country_list($continent_id = array(), $default_select = 0, $zone_id = 0) {
        $this->load->model('general_model');
        $continent_id = urldecode($continent_id);
        if (intval($continent_id) != 0) {
            $option_list = $this->general_model->get_country_list($continent_id, $zone_id);
            if (valid_array($option_list['data'])) {
                echo get_compressed_output(generate_options($option_list['data'], array($default_select)));
            }
        }
    }

    /**
     * Get Location List
     */
    function location_list($limit = AUTO_SUGGESTION_LIMIT) {
        $chars = $_GET['term'];
        $list = $this->general_model->get_location_list($chars, $limit);
        $temp_list = '';
        if (valid_array($list) == true) {
            foreach ($list as $k => $v) {
                $temp_list[] = array('id' => $k, 'label' => $v['name'], 'value' => $v['origin']);
            }
        }
        $this->output_compressed_data($temp_list);
    }
    /**
     * Get Location List
     */
    function city_list($limit = AUTO_SUGGESTION_LIMIT) {
        $chars = $_GET['term'];
        $list = $this->general_model->get_city_list($chars, $limit);
        $temp_list = '';
        if (valid_array($list) == true) {
            foreach ($list as $k => $v) {
                $temp_list[] = array('id' => $k, 'label' => $v['name'], 'value' => $v['origin']);
            }
        }
        $this->output_compressed_data($temp_list);
    }
    /**
     * Balu A
     * @param unknown_type $currency_origin origin of currency - default to USD
     */
    function get_currency_value($currency_origin = 0) {
        $data = $this->custom_db->single_table_records('currency_converter', 'value', array('id' => intval($currency_origin)));
        if (valid_array($data['data'])) {
            $response = $data['data'][0]['value'];
        } else {
            $response = 1;
        }
        header('Content-type:application/json');
        echo json_encode(array('value' => $response));
        exit;
    }
    function get_currency_details($select_currency) {
        // error_reporting(E_ALL);
        //Display 
        $currency_obj = new Currency(array('module_type' => 'flight', 'from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
       
        $currency_value = $currency_obj->conversion_cache[get_api_data_currency().get_application_currency_preference()];
        
        $currency_obj1 = new Currency(array('module_type' => 'flight', 'from' => get_application_default_currency(), 'to' => get_application_currency_preference()));
       
        $currency_value1 = $currency_obj1->conversion_cache[get_application_default_currency().get_application_currency_preference()];

        // debug($currency_value);exit;
        //$response = 1;
        header('Content-type:application/json');
        echo json_encode(array('value' => $currency_value,'currency' => $currency_obj->to_currency,'default_cur_conv_rate' => $currency_value1));
        exit;
    }

    /*
     *
     * Flight(Airport) auto suggest
     *
     */

    function get_airport_code_list() {

        $term = $this->input->get('term'); //retrieve the search term that autocomplete sends
        $term = trim(strip_tags($term));
        $result = array();
        $flagpath = base_url().'extras/custom/'.CURRENT_DOMAIN_KEY.'/images/flags/'; 
        $__airports = $this->flight_model->get_airport_list($term)->result();
        if (valid_array($__airports) == false) {
            $__airports = $this->flight_model->get_airport_list('')->result();
        }
        // debug($__airports);exit;
        $airports = array();
        foreach ($__airports as $airport) {
         // debug($airport);exit;
            // $county_code = $this->flight_model->get_county_code($airport->country);
            $airports['label'] = $airport->airport_city . ', ' . $airport->country . ' (' . $airport->airport_code . ')';
            $airports['value'] = $airport->airport_city . ' (' . $airport->airport_code . ')';
            $airports['id'] = $airport->origin;
            $airports['country_code'] = $flagpath.strtolower($airport->CountryCode).'.png'; 
            if (empty($airport->top_destination) == false) {
                $airports['category'] = 'Top cities';
                $airports['type'] = 'Top cities';
            } else {
                $airports['category'] = 'Search Results';
                $airports['type'] = 'Search Results';
            }

            array_push($result, $airports);
        }
        $this->output_compressed_data($result);
    }
     /*
     *
     * Car(Airport) auto suggest
     *
     */
    function get_airport_city_list(){
        $term = $this->input->get('term'); //retrieve the search term that autocomplete sends
        $term = trim(strip_tags($term));
        $result = array();

        $__airports = $this->car_model->get_airport_list($term)->result();
        if (valid_array($__airports) == false) {
            $__airports = $this->car_model->get_airport_list('')->result();
        }
        // debug($__airports);exit;
        $airports = array();
        foreach ($__airports as $airport) {
            $airports['label'] = $airport->Airport_Name_EN.','.$airport->Country_Name_EN;
            // $airports['value'] = $airport->airport_city . ' (' . $airport->airport_code . ')';
            $airports['id'] = $airport->origin;
            $airports['airport_code'] = $airport->Airport_IATA;
            $airports['category'] = 'Search Results';
            $airports['type'] = 'Search Results';
            array_push($result, $airports);
        }
              
        $city_list = $this->car_model->get_city_list($term)->result();
        if (valid_array($city_list) == false) {
            $city_list = $this->car_model->get_city_list('')->result();
        }
        foreach($city_list as $city){ //debug($city_list);exit;
            if($city->City_ID != ""){
                $city_result['label'] = $city->City_Name_EN.' City/Downtown,'.$city->Country_Name_EN;
                $city_result['id'] = $city->origin;
                $city_result['airport_code'] = $city->Airport_IATA;
                $city_result['country_id'] = $city->Country_ISO;
                if (empty($city->top_destination) == false) {
                    $city_result['category'] = 'Top cities';
                    $city_result['type'] = 'Top cities';
                } else {
                    $city_result['category'] = 'Search Results';
                    $city_result['type'] = 'Search Results';
                }
                array_push($result,$city_result);
            }
        }
           
        $this->output_compressed_data($result);
    }
    /*
     *
     * Hotels City auto suggest
     *
     */

    function get_hotel_city_list() {
        $this->load->model('hotel_model');
        $term = $this->input->get('term'); //retrieve the search term that autocomplete sends
        $term = trim(strip_tags($term));
        $data_list = $this->hotel_model->get_hotel_city_list($term);
        if (valid_array($data_list) == false) {
            $data_list = $this->hotel_model->get_hotel_city_list('');
        }
        //debug($data_list);die('??');
        $suggestion_list = array();
        $result = array();
        foreach ($data_list as $city_list) {
            $suggestion_list['label'] = $city_list['city_name'] . ', ' . $city_list['country_name'] . '';
            $suggestion_list['value'] = hotel_suggestion_value($city_list['city_name'], $city_list['country_name']);
            $suggestion_list['id'] = $city_list['origin'];
            if (empty($city_list['top_destination']) == false) {
                $suggestion_list['category'] = 'Top cities';
                $suggestion_list['type'] = 'Top cities';
            } else {
                $suggestion_list['category'] = 'Search Results';
                $suggestion_list['type'] = 'Search Results';
            }
            if (intval($city_list['cache_hotels_count']) > 0) {
                $suggestion_list['count'] = $city_list['cache_hotels_count'];
            } else {
                $suggestion_list['count'] = 0;
            }
            $suggestion_list ['rz_city'] = $city_list ['rz_city_id'];
            $suggestion_list ['rz_country'] = $city_list ['country_code'];
            $result[] = $suggestion_list;
        }
        $this->output_compressed_data($result);
    }

    /**
    * Get Sightsseeing Category List
    */
    function get_ss_category_list(){
       $get_params = $this->input->get();
       
        if($get_params){
             if($get_params['city_id']){          

                   load_sightseen_lib(PROVAB_SIGHTSEEN_BOOKING_SOURCE); 
                   $select_cate_id = 0;
                   if(isset($get_params['Select_cate_id'])){
                     $select_cate_id = $get_params['Select_cate_id'];
                   }else{
                    $get_params['Select_cate_id'] =0;
                   }
                   $category_list = $this->sightseeing_lib->get_category_list($get_params);

                  if($category_list['status']==SUCCESS_STATUS){

                        $cate_response = $this->sightseeing_lib->format_category_response($category_list['data']['CategoryList'],$select_cate_id);

                       if($cate_response['status']==SUCCESS_STATUS){
                            echo json_encode($cate_response['data']);
                            exit;
                       }
                  }else{
                    echo "0";
                    exit;
                  }
                         
             }else{
                echo "0";
                exit;
             }

       }else{
        echo "0";
        exit;
       }
    }
    /**
    *Elavarasi Get Sightseeing product list
    */
    public function sightseeing_list($offset=0){      
        $search_params = $this->input->get();
        // debug($search_params);
        // exit;
        $safe_search_data = $this->sightseeing_model->get_safe_search_data($search_params['search_id'],META_SIGHTSEEING_COURSE);

        $limit = $this->config->item('sightseeing_page_limit');

        if ($search_params['op'] == 'load' && intval($search_params['search_id']) > 0 && isset($search_params['booking_source']) == true) {
            load_sightseen_lib($search_params['booking_source']);
            switch($search_params['booking_source']) {

                case PROVAB_SIGHTSEEN_BOOKING_SOURCE :                  
                    $raw_sightseeing_result = $this->sightseeing_lib->get_sightseeing_list($safe_search_data);
                    if ($raw_sightseeing_result['status']) {
                        //Converting API currency data to preferred currency
                        $currency_obj = new Currency(array('module_type' => 'sightseeing', 'from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
                        $raw_sightseeing_result = $this->sightseeing_lib->search_data_in_preferred_currency($raw_sightseeing_result, $currency_obj,'b2c');
                        //Display 
                        $currency_obj = new Currency(array('module_type' => 'sightseeing', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));

                        $filters = array();                       

                        //Update currency and filter summary appended
                        if (isset($search_params['filters']) == true and valid_array($search_params['filters']) == true) {
                            $filters = $search_params['filters'];
                        } else {
                            $filters = array();
                        }
                      
                        $raw_sightseeing_result['data'] = $this->sightseeing_lib->format_search_response($raw_sightseeing_result['data'], $currency_obj, $search_params['search_id'], 'b2c', $filters);

                        
                        $source_result_count = $raw_sightseeing_result['data']['source_result_count'];
                        $filter_result_count = $raw_sightseeing_result['data']['filter_result_count'];
                       
                        //debug($raw_hotel_list);exit;
                        if (intval($offset) == 0) {
                            //Need filters only if the data is being loaded first time
                            $filters = $this->sightseeing_lib->filter_summary($raw_sightseeing_result['data']);
                            $response['filters'] = $filters['data'];
                        }
                        $attr['search_id'] = abs($search_params['search_id']);
                       // debug($raw_sightseeing_result);exit;
                        $response['data'] = get_compressed_output(
                                $this->template->isolated_view('sightseeing/viator/viator_search_result', array('currency_obj' => $currency_obj, 'raw_sightseeing_list' => $raw_sightseeing_result['data'],
                                    'search_id' => $search_params['search_id'], 'booking_source' => $search_params['booking_source'],
                                    'attr' => $attr,
                                    'search_params' => $safe_search_data['data']
                        )));
                        $response['status'] = SUCCESS_STATUS;
                        $response['total_result_count'] = $source_result_count;
                        $response['filter_result_count'] = $filter_result_count;
                        $response['offset'] = $offset + $limit;
                    }else{
                        $response['status'] = FAILURE_STATUS;
                        
                    }
                break;
            }
        }
        $this->output_compressed_data($response);
    }


    /**
    *Elavarasi Get Transfer product list
    */
    public function transferv1_list($offset=0){      
        $search_params = $this->input->get();
        // debug($search_params);
        // exit;
        $safe_search_data = $this->transferv1_model->get_safe_search_data($search_params['search_id'],META_TRANSFERV1_COURSE);

        $limit = $this->config->item('transferv1_page_limit');

        if ($search_params['op'] == 'load' && intval($search_params['search_id']) > 0 && isset($search_params['booking_source']) == true) {
            load_transferv1_lib($search_params['booking_source']);

            switch($search_params['booking_source']) {

                case PROVAB_TRANSFERV1_BOOKING_SOURCE :
               
                    $raw_sightseeing_result = $this->transferv1_lib->get_transfer_list($safe_search_data);
                   # debug($raw_sightseeing_result);
                    if ($raw_sightseeing_result['status']) {
                        //Converting API currency data to preferred currency
                        $currency_obj = new Currency(array('module_type' => 'transferv1', 'from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
                        $raw_sightseeing_result = $this->transferv1_lib->search_data_in_preferred_currency($raw_sightseeing_result, $currency_obj,'b2c');
                       
                        //Display 
                        $currency_obj = new Currency(array('module_type' => 'transferv1', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));

                        $filters = array();
                       //Update currency and filter summary appended
                        if (isset($search_params['filters']) == true and valid_array($search_params['filters']) == true) {
                            $filters = $search_params['filters'];
                        } else {
                            $filters = array();
                        }
                        //debug($raw_hotel_list);exit;
                        $raw_sightseeing_result['data'] = $this->transferv1_lib->format_search_response($raw_sightseeing_result['data'], $currency_obj, $search_params['search_id'], 'b2c', $filters);
                        
                        $source_result_count = $raw_sightseeing_result['data']['source_result_count'];
                        $filter_result_count = $raw_sightseeing_result['data']['filter_result_count'];
                        //debug($raw_hotel_list);exit;
                        if (intval($offset) == 0) {
                            //Need filters only if the data is being loaded first time
                            $filters = $this->transferv1_lib->filter_summary($raw_sightseeing_result['data']);
                            $response['filters'] = $filters['data'];
                        }
                       
                        $attr['search_id'] = abs($search_params['search_id']);
                       // debug($raw_sightseeing_result);exit;
                        $response['data'] = get_compressed_output(
                                $this->template->isolated_view('transferv1/viator/viator_search_result', array('currency_obj' => $currency_obj, 'raw_sightseeing_list' => $raw_sightseeing_result['data'],
                                    'search_id' => $search_params['search_id'], 'booking_source' => $search_params['booking_source'],
                                    'attr' => $attr,
                                    'search_params' => $safe_search_data['data']
                        )));
                        $response['status'] = SUCCESS_STATUS;
                        $response['total_result_count'] = $source_result_count;
                        $response['filter_result_count'] = $filter_result_count;
                        $response['offset'] = $offset + $limit;
                    }else{
                        $response['status'] = FAILURE_STATUS;
                        
                    }
                break;
            }
        }
        $this->output_compressed_data($response);
    }

     /*
     *
     * Sightseeing AutoSuggest List
     *
     */

    function get_sightseen_city_list() {

        $this->load->model('sightseeing_model');
        $term = $this->input->get('term'); //retrieve the search term that autocomplete sends
        $term = trim(strip_tags($term));
        $data_list = $this->sightseeing_model->get_sightseen_city_list($term);
        if (valid_array($data_list) == false) {
            $data_list = $this->sightseeing_model->get_sightseen_city_list('');
        }
        $suggestion_list = array();
        $result = array();
        foreach ($data_list as $city_list) {
            $suggestion_list['label'] = $city_list['city_name'];

            $suggestion_list['value'] = $city_list['city_name'];

          //  $suggestion_list['value'] = hotel_suggestion_value($city_list['city_name'], $city_list['country_name']);
            $suggestion_list['id'] = $city_list['origin'];
            if (empty($city_list['top_destination']) == false) {
                $suggestion_list['category'] = 'Top cities';
                $suggestion_list['type'] = 'Top cities';
            } else {
                $suggestion_list['category'] = 'Location list';
                $suggestion_list['type'] = 'Location list';
            }
           
            $suggestion_list['count'] = 0;
            $result[] = $suggestion_list;
        }
        $this->output_compressed_data($result);
    }
   

    /**
     * Auto Suggestion for bus stations
     */
    function bus_stations() {
        $this->load->model('bus_model');
        $term = $this->input->get('term'); //retrieve the search term that autocomplete sends
        $term = trim(strip_tags($term));
        $data_list = $this->bus_model->get_bus_station_list($term);
        if (valid_array($data_list) == false) {
            $data_list = $this->bus_model->get_bus_station_list('');
        }
        $suggestion_list = array();
        $result = array();
        foreach ($data_list as $city_list) {
            $suggestion_list['label'] = $city_list['ets_city_name'];
            $suggestion_list['value'] = $city_list['ets_city_name'];
            $suggestion_list['id'] = $city_list['origin'];
            if (empty($city_list['top_destination']) == false) {
                $suggestion_list['category'] = 'Top cities';
                $suggestion_list['type'] = 'Top cities';
            } else {
                $suggestion_list['category'] = 'Search Results';
                $suggestion_list['type'] = 'Search Results';
            }
            $result[] = $suggestion_list;
        }
        $this->output_compressed_data($result);
    }

    /**
     * Load hotels for map
     */
    function get_all_hotel_list() {
        $response['data'] = '';
        $response['msg'] = '';
        $response['status'] = FAILURE_STATUS;
        $search_params = $this->input->get();
        $limit = $this->config->item('hotel_per_page_limit');
        if ($search_params['op'] == 'load' && intval($search_params['search_id']) > 0 && isset($search_params['booking_source']) == true) {
            load_hotel_lib($search_params['booking_source']);
            switch ($search_params['booking_source']) {
                case PROVAB_HOTEL_BOOKING_SOURCE :
                    //getting search params from table
                    $safe_search_data = $this->hotel_model->get_safe_search_data($search_params['search_id']);
                    //Meaning hotels are loaded first time
                    $raw_hotel_list = $this->hotel_lib->get_hotel_list(abs($search_params['search_id']));
                    //debug($raw_hotel_list);exit;
                    if ($raw_hotel_list['status']) {
                        //Converting API currency data to preferred currency
                        $currency_obj = new Currency(array('module_type' => 'hotel', 'from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
                        $raw_hotel_list = $this->hotel_lib->search_data_in_preferred_currency($raw_hotel_list, $currency_obj);
                        //Display 
                        $currency_obj = new Currency(array('module_type' => 'hotel', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
                        //Update currency and filter summary appended
                        if (isset($search_params['filters']) == true and valid_array($search_params['filters']) == true) {
                            $filters = $search_params['filters'];
                        } else {
                            $filters = array();
                        }
                        $attr['search_id'] = abs($search_params['search_id']);
                        $raw_hotel_search_result = array();
                        $i = 0;
                        $counter = 0;
                        if ($max_lat == 0) {
                            $max_lat = $min_lat = 0;
                        }

                        if ($max_lon == 0) {
                            $max_lon = $min_lon = 0;
                        }
                        if ($raw_hotel_list['data']['HotelSearchResult']) {
                            foreach ($raw_hotel_list['data']['HotelSearchResult']['HotelResults'] as $key => $value) {
                                $raw_hotel_search_result[$i] = $value;
                                $raw_hotel_search_result[$i]['MResultToken'] = urlencode($value['ResultToken']);
                                $lat = $value['Latitude'];
                                $lon = $value['Longitude'];
                                if (($lat != '') && ($counter < 1)) {
                                    $max_lat = $min_lat = $lat;
                                }
                                if (($lon != '')) {
                                    $counter++;
                                    $max_lon = $min_lon = $lon;
                                }

                                $i++;
                            }
                            $raw_hotel_list['data']['HotelSearchResult']['max_lat'] = $max_lat;
                            $raw_hotel_list['data']['HotelSearchResult']['max_lon'] = $max_lon;
                        }
                        $raw_hotel_list['data']['HotelSearchResult']['HotelResults'] = $raw_hotel_search_result;
                        //debug($raw_hotel_list);exit;
                        $response['data'] = $raw_hotel_list['data'];
                        $response['status'] = SUCCESS_STATUS;
                    }
                    break;
            }
        }
        $this->output_compressed_data($response);
    }

    /**
     * Get Cancellation Policy based on Cancellation policy code
     *
     */
    function get_cancellation_policy_old() {
        $get_params = $this->input->get();

        $application_preferred_currency = get_application_currency_preference();
        $application_default_currency = get_application_currency_preference();
        $currency_obj = new Currency(array('module_type' => 'hotel', 'from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
        $room_price = $get_params['room_price'];
        //debug($get_params);exit;
        if (isset($get_params['booking_source']) && !empty($get_params['booking_source'])) {
            load_hotel_lib($get_params['booking_source']);

            if ($get_params['today_cancel_date'] == false) {
                if (isset($get_params['policy_code']) && !empty($get_params['policy_code'])) {
                    $safe_search_data = $this->hotel_model->get_safe_search_data($get_params['tb_search_id']);
                    $get_params['no_of_nights'] = $safe_search_data['data']['no_of_nights'];
                    $get_params['room_count'] = $safe_search_data['data']['room_count'];
                    $get_params['check_in'] = $safe_search_data['data']['from_date'];
                    $cancellation_details = $this->hotel_lib->get_cancellation_details($get_params);


                    $cancellatio_details = $cancellation_details['GetCancellationPolicy']['policy'][0]['policy'];
                    $policy_string = '';
                    $cancel_string = '';
                    $cancel_count = count($cancellatio_details);
                    //$cancel_reverse = $cancellatio_details; 
                    $cancel_reverse = $this->hotel_lib->php_arrayUnique($cancellatio_details, 'Charge');
                    //debug($cancellatio_details);exit;
                    $cancellatio_details = $this->hotel_lib->php_arrayUnique($cancellatio_details, 'Charge');
                    foreach ($cancellatio_details as $key => $value) {
                        $amount = 0;
                        $policy_string = '';
                        if ($value['Charge'] == 0) {
                            $policy_string .= 'No cancellation charges, if cancelled before ' . date('d M Y', strtotime($value['ToDate']));
                        } else {
                            if ($value['Charge'] != 0) {
                                if (isset($cancel_reverse[$key + 1])) {
                                    if ($value['ChargeType'] == 1) {
                                        $amount = $currency_obj->get_currency_symbol($currency_obj->to_currency) . " " . get_converted_currency_value($currency_obj->force_currency_conversion(round($value['Charge'])));
                                    } elseif ($value['ChargeType'] == 2) {
                                        $amount = $currency_obj->get_currency_symbol($currency_obj->to_currency) . " " . $room_price;
                                    }
                                    $current_date = date('Y-m-d');
                                    $cancell_date = date('Y-m-d', strtotime($value['FromDate']));
                                    if ($cancell_date > $current_date) {
                                        //$value['FromDate'] = date('Y-m-d');
                                        $policy_string .= 'Cancellations made after ' . date('d M Y', strtotime($value['FromDate'])) . ' to ' . date('d M Y', strtotime($value['ToDate'])) . ', would be charged ' . $amount;
                                    }
                                    //$policy_string .='Cancellations made after '.date('d M Y',strtotime($value['FromDate'])).' to '.date('d M Y',strtotime($value['ToDate'])).', would be charged '.$amount;
                                } else {
                                    if ($value['ChargeType'] == 1) {
                                        $amount = $currency_obj->get_currency_symbol($currency_obj->to_currency) . " " . get_converted_currency_value($currency_obj->force_currency_conversion(round($value['Charge'])));
                                    } elseif ($value['ChargeType'] == 2) {
                                        $amount = $currency_obj->get_currency_symbol($currency_obj->to_currency) . " " . $room_price;
                                    }
                                    $current_date = date('Y-m-d');
                                    $cancell_date = date('Y-m-d', strtotime($value['FromDate']));
                                    if ($cancell_date > $current_date) {
                                        $value['FromDate'] = $value['FromDate'];
                                    } else {
                                        $value['FromDate'] = date('Y-m-d');
                                    }
                                    $policy_string .= 'Cancellations made after ' . date('d M Y', strtotime($value['FromDate'])) . ', or no-show, would be charged ' . $amount;
                                }
                            }
                        }
                        $cancel_string .= $policy_string . '<br/> ';
                    }

                    echo $cancel_string;
                    //echo $cancellation_details['GetCancellationPolicy']['policy'][0];
                } else {
                    $cancel_string = '';
                    $cancellation_policy_details = json_decode(base64_decode($get_params['policy_details']));
                    //debug($cancellation_policy_details);
                    $cancel_count = count($cancellation_policy_details);
                    $cancellation_policy_details = json_decode(json_encode($cancellation_policy_details), True);
                    $cancel_reverse = $this->hotel_lib->php_arrayUnique(array_reverse($cancellation_policy_details), 'Charge');
                    //$cancel_reverse = array_reverse($cancellation_policy_details);
                    //debug($cancel_reverse);						
                    $cancellation_policy_details = $this->hotel_lib->php_arrayUnique(array_reverse($cancellation_policy_details), 'Charge');

                    if ($cancellation_policy_details) {
                        //$cancellation_policy_details = array_reverse($cancellation_policy_details);
                        foreach ($cancellation_policy_details as $key => $value) {
                            $policy_string = '';
                            if ($value['Charge'] == 0) {
                                $policy_string .= 'No cancellation charges, if cancelled before ' . date('d M Y', strtotime($value['ToDate']));
                            } else {
                                if (isset($cancel_reverse[$key + 1])) {
                                    if ($value['ChargeType'] == 1) {
                                        $amount = $currency_obj->get_currency_symbol($currency_obj->to_currency) . "  " . $value['Charge'];
                                    } elseif ($value['ChargeType'] == 2) {
                                        $amount = $currency_obj->get_currency_symbol($currency_obj->to_currency) . "  " . $room_price;
                                    }
                                    $current_date = date('Y-m-d');
                                    $cancell_date = date('Y-m-d', strtotime($value['FromDate']));
                                    if ($cancell_date > $current_date) {
                                        $policy_string .= 'Cancellations made after ' . date('d M Y', strtotime($value['FromDate'])) . ' to ' . date('d M Y', strtotime($value['ToDate'])) . ', would be charged ' . $amount;
                                    }
                                } else {
                                    if ($value['ChargeType'] == 1) {
                                        $amount = $currency_obj->get_currency_symbol($currency_obj->to_currency) . "  " . $value['Charge'];
                                    } elseif ($value['ChargeType'] == 2) {
                                        $amount = $currency_obj->get_currency_symbol($currency_obj->to_currency) . "  " . $room_price;
                                    }
                                    $current_date = date('Y-m-d');
                                    $cancell_date = date('Y-m-d', strtotime($value['FromDate']));
                                    if ($cancell_date > $current_date) {
                                        $value['FromDate'] = $value['FromDate'];
                                    } else {
                                        $value['FromDate'] = date('Y-m-d');
                                    }
                                    $policy_string .= 'Cancellations made after ' . date('d M Y', strtotime($value['FromDate'])) . ', or no-show, would be charged ' . $amount;
                                }
                            }

                            $cancel_string .= $policy_string . '<br/>';
                        }
                    } else {
                        $cancel_string = 'This rate is non-refundable. If you cancel this booking you will not be refunded any of the payment.';
                    }


                    echo $cancel_string;
                }
            } else {
                echo "This rate is non-refundable. If you cancel this booking you will not be refunded any of the payment.";
            }
        } else {
            echo "This rate is non-refundable. If you cancel this booking you will not be refunded any of the payment.";
        }
        exit;
    }

    function get_cancellation_policy() {
        $get_params = $this->input->get();
        //debug($get_params);exit;
        $application_preferred_currency = get_application_currency_preference();
        $application_default_currency = get_application_currency_preference();
        $currency_obj = new Currency(array('module_type' => 'hotel', 'from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
        $room_price = $get_params['room_price'];
        //debug($get_params);exit;
        if (isset($get_params['booking_source']) && !empty($get_params['booking_source'])) {
            load_hotel_lib($get_params['booking_source']);

            if ($get_params['today_cancel_date'] == false) {
                if (isset($get_params['policy_code']) && !empty($get_params['policy_code'])) {
                    $safe_search_data = $this->hotel_model->get_safe_search_data($get_params['tb_search_id']);
                    $get_params['no_of_nights'] = $safe_search_data['data']['no_of_nights'];
                    $get_params['room_count'] = $safe_search_data['data']['room_count'];
                    $get_params['check_in'] = $safe_search_data['data']['from_date'];
                    $cancellation_details = $this->hotel_lib->get_cancellation_details($get_params);
                    $cancellatio_details = $cancellation_details['GetCancellationPolicy']['policy'][0]['policy'];

                    $policy_string = '';
                    $cancel_string = '';
                    $cancel_count = count($cancellatio_details);
                    //$cancel_reverse = $cancellatio_details; 
                    $cancel_reverse = $this->hotel_lib->php_arrayUnique($cancellatio_details, 'Charge');
                    //debug($cancellatio_details);exit;
                    $cancellatio_details = $this->hotel_lib->php_arrayUnique($cancellatio_details, 'Charge');
                    foreach ($cancellatio_details as $key => $value) {

                        $value['Charge'] = $this->hotel_lib->update_cancellation_markup_currency($value['Charge'], $currency_obj, $get_params['search_id']);
                        $amount = 0;
                        $policy_string = '';
                        if ($value['Charge'] == 0) {
                            $policy_string .= 'No cancellation charges, if cancelled before ' . date('d M Y', strtotime($value['ToDate']));
                        } else {
                            if ($value['Charge'] != 0) {
                                if (isset($cancel_reverse[$key + 1])) {
                                    if ($value['ChargeType'] == 1) {
                                        $amount = $currency_obj->get_currency_symbol($currency_obj->to_currency) . " " . round($value['Charge']);
                                    } elseif ($value['ChargeType'] == 2) {
                                        $amount = $currency_obj->get_currency_symbol($currency_obj->to_currency) . " " . $room_price;
                                    }
                                    $current_date = date('Y-m-d');
                                    $cancell_date = date('Y-m-d', strtotime($value['FromDate']));
                                    if ($cancell_date > $current_date) {
                                        //$value['FromDate'] = date('Y-m-d');
                                        $policy_string .= 'Cancellations made after ' . date('d M Y', strtotime($value['FromDate'])) . ' to ' . date('d M Y', strtotime($value['ToDate'])) . ', would be charged ' . $amount;
                                    }
                                    //$policy_string .='Cancellations made after '.date('d M Y',strtotime($value['FromDate'])).' to '.date('d M Y',strtotime($value['ToDate'])).', would be charged '.$amount;
                                } else {
                                    if ($value['ChargeType'] == 1) {
                                        $amount = $currency_obj->get_currency_symbol($currency_obj->to_currency) . " " . round($value['Charge']);
                                    } elseif ($value['ChargeType'] == 2) {
                                        $amount = $currency_obj->get_currency_symbol($currency_obj->to_currency) . " " . $room_price;
                                    }
                                    $current_date = date('Y-m-d');
                                    $cancell_date = date('Y-m-d', strtotime($value['FromDate']));
                                    if ($cancell_date > $current_date) {
                                        $value['FromDate'] = $value['FromDate'];
                                    } else {
                                        $value['FromDate'] = date('Y-m-d');
                                    }
                                    $policy_string .= 'Cancellations made after ' . date('d M Y', strtotime($value['FromDate'])) . ', or no-show, would be charged ' . $amount;
                                }
                            }
                        }
                        $cancel_string .= $policy_string . '<br/> ';
                    }

                    echo $cancel_string;
                    //echo $cancellation_details['GetCancellationPolicy']['policy'][0];
                } else {
                    $cancel_string = '';
                    $cancellation_policy_details = json_decode(base64_decode($get_params['policy_details']));
                    //debug($cancellation_policy_details);
                    $cancel_count = count($cancellation_policy_details);
                    $cancellation_policy_details = json_decode(json_encode($cancellation_policy_details), True);
                    $cancel_reverse = $this->hotel_lib->php_arrayUnique(array_reverse($cancellation_policy_details), 'Charge');
                    //$cancel_reverse = array_reverse($cancellation_policy_details);
                    //debug($cancel_reverse);						
                    $cancellation_policy_details = $this->hotel_lib->php_arrayUnique(array_reverse($cancellation_policy_details), 'Charge');
                    // debug($cancellation_policy_details);exit;
                    if ($cancellation_policy_details) {
                        //$cancellation_policy_details = array_reverse($cancellation_policy_details);
                        foreach ($cancellation_policy_details as $key => $value) {
                            $policy_string = '';
                            if ($value['Charge'] == 0) {
                                $policy_string .= 'No cancellation charges, if cancelled before ' . date('d M Y', strtotime($value['ToDate']));
                            } else {
                                if (isset($cancel_reverse[$key + 1])) {
                                    if ($value['ChargeType'] == 1) {
                                        $amount = $currency_obj->get_currency_symbol($currency_obj->to_currency) . "  " . $value['Charge'];
                                    } elseif ($value['ChargeType'] == 2) {
                                        $amount = $currency_obj->get_currency_symbol($currency_obj->to_currency) . "  " . $room_price;
                                    }
                                    $current_date = date('Y-m-d');
                                    $cancell_date = date('Y-m-d', strtotime($value['FromDate']));
                                    if ($cancell_date > $current_date) {
                                        $policy_string .= 'Cancellations made after ' . date('d M Y', strtotime($value['FromDate'])) . ' to ' . date('d M Y', strtotime($value['ToDate'])) . ', would be charged ' . $amount;
                                    }
                                } else {
                                    if ($value['ChargeType'] == 1) {
                                        $amount = $currency_obj->get_currency_symbol($currency_obj->to_currency) . "  " . $value['Charge'];
                                    } elseif ($value['ChargeType'] == 2) {
                                        $amount = $currency_obj->get_currency_symbol($currency_obj->to_currency) . "  " . $room_price;
                                    }
                                    $current_date = date('Y-m-d');
                                    $cancell_date = date('Y-m-d', strtotime($value['FromDate']));
                                    if ($cancell_date > $current_date) {
                                        $value['FromDate'] = $value['FromDate'];
                                    } else {
                                        $value['FromDate'] = date('Y-m-d');
                                    }
                                    $policy_string .= 'Cancellations made after ' . date('d M Y', strtotime($value['FromDate'])) . ', or no-show, would be charged ' . $amount;
                                }
                            }

                            $cancel_string .= $policy_string . '<br/>';
                        }
                    } else {
                        $cancel_string = 'This rate is non-refundable. If you cancel this booking you will not be refunded any of the payment.';
                    }


                    echo $cancel_string;
                }
            } else {
                echo "This rate is non-refundable. If you cancel this booking you will not be refunded any of the payment.";
            }
        } else {
            echo "This rate is non-refundable. If you cancel this booking you will not be refunded any of the payment.";
        }
        exit;
    }

    /**
     * Load hotels from different source
     */
    function hotel_list($offset = 0) {
        $response['data'] = '';
        $response['msg'] = '';
        $response['status'] = FAILURE_STATUS;
        $search_params = $this->input->get();

        //debug($search_params);exit('list');
        $limit = $this->config->item('hotel_per_page_limit');

        if ($search_params['op'] == 'load' && intval($search_params['search_id']) > 0 && isset($search_params['booking_source']) == true) {
            load_hotel_lib($search_params['booking_source']);
            switch ($search_params['booking_source']) {
                case PROVAB_HOTEL_BOOKING_SOURCE :
                    //getting search params from table
                    $safe_search_data = $this->hotel_model->get_safe_search_data($search_params['search_id']);

                    //Meaning hotels are loaded first time
                    $raw_hotel_list = $this->hotel_lib->get_hotel_list(abs($search_params['search_id']), $search_params);
                    //debug($search_params);exit;
                    if ($raw_hotel_list['status']) {
                        //Converting API currency data to preferred currency
                        $currency_obj = new Currency(array('module_type' => 'hotel', 'from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
                        $raw_hotel_list = $this->hotel_lib->search_data_in_preferred_currency($raw_hotel_list, $currency_obj, $search_params['search_id']);
                        //debug($raw_hotel_list);exit;
                        //Display 
                        $currency_obj = new Currency(array('module_type' => 'hotel', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
                        //Update currency and filter summary appended
                        if (isset($search_params['filters']) == true and valid_array($search_params['filters']) == true) {
                            $filters = $search_params['filters'];
                        } else {
                            $filters = array();
                        }
                        //debug($raw_hotel_list);exit;
                        $raw_hotel_list['data'] = $this->hotel_lib->format_search_response($raw_hotel_list['data'], $currency_obj, $search_params['search_id'], 'b2c', $filters);


                    


                        $source_result_count = $raw_hotel_list['data']['source_result_count'];
                        $filter_result_count = $raw_hotel_list['data']['filter_result_count'];
                        //debug($raw_hotel_list);exit;
                        if (intval($offset) == 0) {
                            //Need filters only if the data is being loaded first time
                            $filters = $this->hotel_lib->filter_summary($raw_hotel_list['data']);
                            $response['filters'] = $filters['data'];
                        }
                        //debug($raw_hotel_list['data']);exit;
                        $raw_hotel_list['data'] = $this->hotel_lib->get_page_data($raw_hotel_list['data'], $offset, $limit);

                        $attr['search_id'] = abs($search_params['search_id']);

                        //debug($raw_hotel_list);exit;
                        $response['data'] = get_compressed_output(
                                $this->template->isolated_view('hotel/tbo/tbo_search_result', array('currency_obj' => $currency_obj, 'raw_hotel_list' => $raw_hotel_list['data'],
                                    'search_id' => $search_params['search_id'], 'booking_source' => $search_params['booking_source'],
                                    'attr' => $attr,
                                    'search_params' => $safe_search_data
                        )));
                       
                        $response['page_reload'] = $raw_hotel_list['page_reload'];
                        $response['total_result_count'] = $source_result_count;
                        $response['filter_result_count'] = $filter_result_count;
                        $response['offset'] = $offset + $limit;
                    }
                    if(($raw_hotel_list['status'] == false && $raw_hotel_list['data']['refresh_flag']==1) ){
                        $response['status'] = FAILURE_STATUS;
                        $response['request_count'] = $raw_hotel_list['data']['refresh_flag'];
                    }
                    else{
                        $response['status'] = SUCCESS_STATUS;
                        $response['request_count'] = $raw_hotel_list['data']['refresh_flag'];
                    }
                break;

                case REZLIVE_HOTEL :
                    //Meaning hotels are loaded first time
                    $raw_hotel_list = $this->hotel_lib->get_hotel_list(abs($search_params['search_id']),'b2c');
                   // debug($raw_hotel_list);die('ajax-rez');
                    if ($raw_hotel_list['status']) {
                        //Converting API currency data to preferred currency
                        $currency_obj = new Currency(array('module_type' => 'hotel','from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
                        //$raw_hotel_list = $this->hotel_lib->search_data_in_preferred_currency($raw_hotel_list, $currency_obj,$search_params['search_id']);
                        //Display 
                        $currency_obj = new Currency(array('module_type' => 'hotel', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
                        //Update currency and filter summary appended
                        if (isset($search_params['filters']) == true and valid_array($search_params['filters']) == true) {
                            $filters = $search_params['filters'];
                        } else { 
                            $filters = array();
                        }

                        $raw_hotel_list['data'] = $this->hotel_lib->format_search_response($raw_hotel_list['data'], $currency_obj, $search_params['search_id'], 'b2c', $filters);
                        
                        $source_result_count = $raw_hotel_list['data']['source_result_count'];
                        $filter_result_count = $raw_hotel_list['data']['filter_result_count'];
                        if (intval($offset) == 0) {
                            //Need filters only if the data is being loaded first time
                            $filters = $this->hotel_lib->filter_summary($raw_hotel_list['data']);
                            $response['filters'] = $filters['data'];
                        }
                        $raw_hotel_list['data'] = $this->hotel_lib->get_page_data($raw_hotel_list['data'], $offset, $limit);

                        $attr['search_id'] = abs($search_params['search_id']);
                        //debug($raw_hotel_list);exit('rez');

                        $response['data'] = get_compressed_output(
                        $this->template->isolated_view('hotel/tbo/tbo_search_result',
                        array('currency_obj' => $currency_obj, 'raw_hotel_list' => $raw_hotel_list['data'],
                                    'search_id' => $search_params['search_id'], 'booking_source' => $search_params['booking_source'],
                                    'attr' => $attr,
                                    'search_params' => $safe_search_data
                        )));
                        $response['status'] = SUCCESS_STATUS;
                        $response['total_result_count'] = $source_result_count;
                        $response['filter_result_count'] = $filter_result_count;
                        $response['offset'] = $offset+$limit;
                    }
                break;  
            }
        }
        $this->output_compressed_data($response);
    }

   
    
    
    /**
     * Load hotels from different source
     */
    function hotel_image_list($offset = 0) {
        $response['data'] = '';
        $response['msg'] = '';
        $response['status'] = FAILURE_STATUS;

        if (PROVAB_HOTEL_BOOKING_SOURCE) {
            load_hotel_lib(PROVAB_HOTEL_BOOKING_SOURCE);
            switch (PROVAB_HOTEL_BOOKING_SOURCE) {
                case PROVAB_HOTEL_BOOKING_SOURCE :
                    //getting alll_api_city_master
                    $api_city_master = $this->custom_db->single_table_records('all_api_city_master', '*', array('status' => 0, 'priority' => 1), 0, 1);
                    if ($api_city_master['status'] == 1) {


                        $api_city_master = $api_city_master['data'][0];
                        $city_id = $api_city_master['grn_city_id'];
                        $country_code = $api_city_master['country_code'];
                        $destination_code = $api_city_master['grn_destination_id'];
                        //Meaning hotels are loaded first time
                        $raw_hotel_list = $this->hotel_lib->get_hotel_image_list($city_id, $country_code);

                        $update_condition = array();
                        if ($raw_hotel_list['status'] == true) {
                            $raw_hotel_list = $raw_hotel_list['data'];
                            $update_condition['status'] = 1;

                            foreach ($raw_hotel_list as $key => $value) {
                                if ($value['images']) {
                                    if ($value['images']['main_image']) {

                                        $image = 'https://cdn.grnconnect.com/' . $value['images']['main_image'];
                                        $path_info = pathinfo($value['images']['main_image']);
                                        $path_info_base_name = $path_info['basename'];
                                        $insert_data['hotel_code'] = $value['hotel_code'];
                                        $insert_data['city_code'] = $value['city_code'];
                                        $insert_data['destination_code'] = $destination_code;
                                        $insert_data['country_code'] = $value['country'];
                                        $image = file_get_contents($image);
                                        $hotel_code = preg_replace("/[^a-zA-Z0-9]/", "", $value['hotel_code']);
                                        $image_file_name = $hotel_code . time() . $path_info_base_name;
                                        $image_folder_path = './cdn/images/' . $image_file_name;
                                        file_put_contents($image_folder_path, $image); //Where to save the image on your server
                                        $insert_data['path_name'] = $image_file_name;
                                        $check_if_exists = $this->custom_db->single_table_records('api_grn_image_main_image_path', '*', array('hotel_code' => $insert_data['hotel_code'], 'city_code' => $insert_data['city_code'], 'country_code' => $insert_data['country_code']));
                                        if ($check_if_exists['status'] == 1) {
                                            $update_data['path_name'] = $image_file_name;
                                            $condition['hotel_code'] = $insert_data['hotel_code'];
                                            $condition['city_code'] = $insert_data['CITY_CODE'];
                                            $this->custom_db->update_record('api_grn_image_main_image_path', $update_data, $condition);
                                        } else {
                                            $this->custom_db->insert_record('api_grn_image_main_image_path', $insert_data);
                                        }
                                    }
                                }
                            }
                        } else {
                            $update_condition['status'] = 1;
                            $update_condition['error'] = json_encode($raw_hotel_list);
                        }

                        //update in all_api_city_master
                        $this->custom_db->update_record('all_api_city_master', $update_condition, array('grn_city_id' => $city_id, 'country_code' => $country_code, 'grn_destination_id' => $destination_code));
                    }
                    echo "successs";
            }
        }
    }

    /**
     * Compress and output data
     * @param array $data
     */
    private function output_compressed_data($data) {


        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        ob_start("ob_gzhandler");
        header('Content-type:application/json');
        echo json_encode($data);
        ob_end_flush();
        exit;
    }

    /**
     * Compress and output data
     * @param array $data
     */
    private function output_compressed_data_flight($data) {

        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        ob_start("ob_gzhandler");
        ini_set("memory_limit", "-1");
        set_time_limit(0);
        header('Content-type:application/json');
        echo json_encode($data, JSON_UNESCAPED_SLASHES);
        ob_end_flush();
        exit;
    }
	
		function removeSpecificOperators($response, $bs)
	{
		//debug($response); exit;
		$block_opts = $this->module_model->get_manage_buses($bs, 0);
		$op_id_arr = array();
		foreach($block_opts AS $block_opt)
		{
			$op_id_arr[]=$block_opt["travel_id"];
		}
		
		$op_id_arr = array_unique($op_id_arr);
		$new_response = array();
		foreach ($response as $key => $value) {
			if(in_array($value['operator_id'], $op_id_arr)){
				continue;
			}
			else{
				$new_response[] = $value; 
			}
		}
		//debug($new_response); exit;
		return $new_response;
	}
	function showSpecificOperators($response, $bs)
	{
		$show_opts = $this->module_model->get_manage_buses($bs, 1);
		$op_id_arr = array();
		$op_id_arr = array();
		foreach($show_opts AS $show_opt)
		{
			$op_id_arr[]=$show_opt["travel_id"];
		}
		
		$new_response = array();
		foreach ($response as $key => $value) {
			if(in_array($value['operator_id'], $op_id_arr)){
				$new_response[] = $value; 
			}
			else{
				continue;
			}
		}
		//debug($new_response); exit;
		return $new_response;
	}
	public function display_ets_opertors($formatted_search_data, $ets_op_id)
	{		
		//debug($ets_op_id); exit("Yes");
		$ets_resp = [];						
		if(!empty($formatted_search_data)){
			foreach ($ets_op_id as $etkey => $etvalue) {
				foreach ($formatted_search_data as $fkey => $fvalue) {
					if(($fvalue['operator_id'] == $etvalue['operator_id']) && ($etvalue["status"]==1)){
						$ets_resp[] = $fvalue;						 	
					}
				}
			}			
		}
		//debug($ets_resp); exit;
		return $ets_resp;		
	}

    /**
     * Load hotels from different source
     */
    function bus_list() {
        $this->load->model('bus_model');
        $response['data'] = '';
        $response['msg'] = '';
        $response['status'] = FAILURE_STATUS;
        $search_params = $this->input->get();
        //debug($search_params);die();
        $search_data = $this->bus_model->get_search_data($search_params['search_id']);
        //debug($search_data);die('++');

        $search_data = json_decode($search_data['search_data'], true);
        $form_data = '';
        $to_data = '';
        // debug($search_data);

        if ($search_params['op'] == 'load' && intval($search_params['search_id']) > 0 && isset($search_params['booking_source']) == true) {
            load_bus_lib($search_params['booking_source']);
            switch ($search_params['booking_source']) {
                case PROVAB_BUS_BOOKING_SOURCE :
                    $raw_bus_list = $this->bus_lib->get_bus_list(abs($search_params['search_id']));
                    // debug($raw_bus_list);exit;
                    $from_id = @$raw_bus_list['data']['result'][0]['From'];
                    $to_id = @$raw_bus_list['data']['result'][0]['To'];

                    $form_data = $this->bus_model->get_bus_station_data($search_data['from_station_id']);
                    //debug($form_data);
                    $to_data = $this->bus_model->get_bus_station_data($search_data['to_station_id']);
                   // debug($search_data);
                    $search_data_city = array('from_id' => $form_data->station_id,
                        'to_id' => $to_data->station_id);
                   // debug($search_data_city);
                    //exit;
                    if ($raw_bus_list['status']) {
                        //Converting API currency data to preferred currency
                        // echo 'herre'.get_api_data_currency();exit;
                        $currency_obj = new Currency(array('module_type' => 'bus', 'from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
                        $raw_bus_list = $this->bus_lib->search_data_in_preferred_currency($raw_bus_list, $currency_obj);
                        $currency_obj = new Currency(array('module_type' => 'bus', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
                        $formatted_search_data = $this->bus_lib->format_search_response($raw_bus_list, $currency_obj, $search_params['search_id'], 'bus','B2C');
                       // debug($formatted_search_data);exit;
                        //Display Bus List
                        $currency_obj = new Currency(array('module_type' => 'bus', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
                        $response['data'] = get_compressed_output(
                                $this->template->isolated_view('bus/travelyaari/travelyaari_search_result', array('currency_obj' => $currency_obj, 'raw_bus_list' => $formatted_search_data, 'search_id' => $search_params['search_id'], 'booking_source' => $search_params['booking_source'], 'search_data_city' => $search_data_city))
                        );
                        $response['status'] = SUCCESS_STATUS;
                    }
                break;

                case VRL_BUS_BOOKING_SOURCE :
                    $raw_bus_list = $this->bus_lib->get_bus_list(abs($search_params['search_id']));
                    //debug($raw_bus_list);exit('vrl'); 
                    $from_id = @$raw_bus_list['data']['result'][0]['From'];
                    $to_id = @$raw_bus_list['data']['result'][0]['To'];
                    $form_data = $this->bus_model->get_bus_station_data($search_data['from_station_id']);
                        $to_data = $this->bus_model->get_bus_station_data($search_data['to_station_id']);
                    $search_data_city = array('from_id' => $form_data->station_id,
                                               'to_id' => $to_data->station_id);
                    
                    if ($raw_bus_list['status']) {
                        //Converting API currency data to preferred currency
                        $currency_obj = new Currency(array('module_type' => 'bus','from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
                        $raw_bus_list = $this->bus_lib->search_data_in_preferred_currency($raw_bus_list, $currency_obj);
                         // debug($raw_bus_list);exit; 
                        $formatted_search_data = $this->bus_lib->format_search_response($raw_bus_list, $currency_obj, $search_params['search_id'], 'bus','B2C');
                        // debug($formatted_search_data);exit;
                        //Display Bus List
                        $currency_obj = new Currency(array('module_type' => 'bus', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
                        $raw_bus_list = force_multple_data_format($raw_bus_list['data']['result']);
                        
                        //Update commission
                        // $raw_bus_list = $this->bus_lib->update_bus_search_commission($raw_bus_list, $currency_obj);
                        // echo 'herre';
                        //debug($formatted_search_data);die('77');
                        
                        $response['segment']['data'] = get_compressed_output(
                        $this->template->isolated_view('bus/travelyaari/travelyaari_search_result',
                        array('currency_obj' => $currency_obj, 'raw_bus_list' => $formatted_search_data, 'search_id' => $search_params['search_id'], 'booking_source' => $search_params['booking_source'],'search_data_city' => $search_data_city))
                        );
                        $response['status'] = SUCCESS_STATUS;
                    }
                break;

                case BITLA_BUS_BOOKING_SOURCE :
                    $raw_bus_list = $this->bus_lib->get_bus_list(abs($search_params['search_id']));
                    //debug($raw_bus_list);exit('vrl'); 
                    $from_id = @$raw_bus_list['data']['result'][0]['From'];
                    $to_id = @$raw_bus_list['data']['result'][0]['To'];
                    $form_data = $this->bus_model->get_bus_station_data($search_data['from_station_id']);
                        $to_data = $this->bus_model->get_bus_station_data($search_data['to_station_id']);
                    $search_data_city = array('from_id' => $form_data->station_id,
                                               'to_id' => $to_data->station_id);
                    
                    if ($raw_bus_list['status']) {
                        //Converting API currency data to preferred currency
                        $currency_obj = new Currency(array('module_type' => 'bus','from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
                        $raw_bus_list = $this->bus_lib->search_data_in_preferred_currency($raw_bus_list, $currency_obj);
                         // debug($raw_bus_list);exit; 
                        $formatted_search_data = $this->bus_lib->format_search_response($raw_bus_list, $currency_obj, $search_params['search_id'], 'bus','B2C');
                        // debug($formatted_search_data);exit;
                        //Display Bus List
						$formatted_search_data = $this->removeSpecificOperators($formatted_search_data, BITLA_BUS_BOOKING_SOURCE);
                        $currency_obj = new Currency(array('module_type' => 'bus', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
                        $raw_bus_list = force_multple_data_format($raw_bus_list['data']['result']);
                        
                        //Update commission
                        // $raw_bus_list = $this->bus_lib->update_bus_search_commission($raw_bus_list, $currency_obj);
                        // echo 'herre';
                        //debug($formatted_search_data);die('77');
                        
                        $response['segment']['data'] = get_compressed_output(
                        $this->template->isolated_view('bus/travelyaari/travelyaari_search_result',
                        array('currency_obj' => $currency_obj, 'raw_bus_list' => $formatted_search_data, 'search_id' => $search_params['search_id'], 'booking_source' => $search_params['booking_source'],'search_data_city' => $search_data_city))
                        );
                        $response['status'] = SUCCESS_STATUS;
                    }
                break;
				
				case SRS_BUS_BOOKING_SOURCE :
                    $raw_bus_list = $this->bus_lib->get_bus_list(abs($search_params['search_id']));
                    //debug($raw_bus_list);exit('vrl'); 
                    $from_id = @$raw_bus_list['data']['result'][0]['From'];
                    $to_id = @$raw_bus_list['data']['result'][0]['To'];
                    $form_data = $this->bus_model->get_bus_station_data($search_data['from_station_id']);
                        $to_data = $this->bus_model->get_bus_station_data($search_data['to_station_id']);
                    $search_data_city = array('from_id' => $form_data->station_id,
                                               'to_id' => $to_data->station_id);
                    
                    if ($raw_bus_list['status']) {
                        //Converting API currency data to preferred currency
                        $currency_obj = new Currency(array('module_type' => 'bus','from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
                        $raw_bus_list = $this->bus_lib->search_data_in_preferred_currency($raw_bus_list, $currency_obj);
                         // debug($raw_bus_list);exit; 
                        $formatted_search_data = $this->bus_lib->format_search_response($raw_bus_list, $currency_obj, $search_params['search_id'], 'bus','B2C');
                        // debug($formatted_search_data);exit;
                        //Display Bus List
						$formatted_search_data = $this->showSpecificOperators($formatted_search_data, SRS_BUS_BOOKING_SOURCE);
						
                        $currency_obj = new Currency(array('module_type' => 'bus', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
                        $raw_bus_list = force_multple_data_format($raw_bus_list['data']['result']);
                        
                        //Update commission
                        // $raw_bus_list = $this->bus_lib->update_bus_search_commission($raw_bus_list, $currency_obj);
                        // echo 'herre';
                        //debug($formatted_search_data);die('77');
                        
                        $response['segment']['data'] = get_compressed_output(
                        $this->template->isolated_view('bus/travelyaari/travelyaari_search_result',
                        array('currency_obj' => $currency_obj, 'raw_bus_list' => $formatted_search_data, 'search_id' => $search_params['search_id'], 'booking_source' => $search_params['booking_source'],'search_data_city' => $search_data_city))
                        );
                        $response['status'] = SUCCESS_STATUS;
                    }
                break;

                case KUKKESHREE_BUS_BOOKING_SOURCE :
                    $raw_bus_list = $this->bus_lib->get_bus_list(abs($search_params['search_id']));
                    //debug($raw_bus_list);exit('vrl'); 
                    $from_id = @$raw_bus_list['data']['result'][0]['From'];
                    $to_id = @$raw_bus_list['data']['result'][0]['To'];
                    $form_data = $this->bus_model->get_bus_station_data($search_data['from_station_id']);
                        $to_data = $this->bus_model->get_bus_station_data($search_data['to_station_id']);
                    $search_data_city = array('from_id' => $form_data->station_id,
                                               'to_id' => $to_data->station_id);
                    
                    if ($raw_bus_list['status']) {
                        //Converting API currency data to preferred currency
                        $currency_obj = new Currency(array('module_type' => 'bus','from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
                        $raw_bus_list = $this->bus_lib->search_data_in_preferred_currency($raw_bus_list, $currency_obj);
                         // debug($raw_bus_list);exit; 
                        $formatted_search_data = $this->bus_lib->format_search_response($raw_bus_list, $currency_obj, $search_params['search_id'], 'bus','B2C');
                        // debug($formatted_search_data);exit;
                        //Display Bus List
                        $currency_obj = new Currency(array('module_type' => 'bus', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
                        $raw_bus_list = force_multple_data_format($raw_bus_list['data']['result']);
                        
                        //Update commission
                        // $raw_bus_list = $this->bus_lib->update_bus_search_commission($raw_bus_list, $currency_obj);
                        // echo 'herre';
                        //debug($formatted_search_data);die('77');
                        
                        $response['segment']['data'] = get_compressed_output(
                        $this->template->isolated_view('bus/travelyaari/travelyaari_search_result',
                        array('currency_obj' => $currency_obj, 'raw_bus_list' => $formatted_search_data, 'search_id' => $search_params['search_id'], 'booking_source' => $search_params['booking_source'],'search_data_city' => $search_data_city))
                        );
                        $response['status'] = SUCCESS_STATUS;
                    }
                break;

                case KRL_BUS_BOOKING_SOURCE :
                    $raw_bus_list = $this->bus_lib->get_bus_list(abs($search_params['search_id']));
                    //debug($raw_bus_list);exit('vrl'); 
                    $from_id = @$raw_bus_list['data']['result'][0]['From'];
                    $to_id = @$raw_bus_list['data']['result'][0]['To'];
                    $form_data = $this->bus_model->get_bus_station_data($search_data['from_station_id']);
                        $to_data = $this->bus_model->get_bus_station_data($search_data['to_station_id']);
                    $search_data_city = array('from_id' => $form_data->station_id,
                                               'to_id' => $to_data->station_id);
                    
                    if ($raw_bus_list['status']) {
                        //Converting API currency data to preferred currency
                        $currency_obj = new Currency(array('module_type' => 'bus','from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
                        $raw_bus_list = $this->bus_lib->search_data_in_preferred_currency($raw_bus_list, $currency_obj);
                         // debug($raw_bus_list);exit; 
                        $formatted_search_data = $this->bus_lib->format_search_response($raw_bus_list, $currency_obj, $search_params['search_id'], 'bus','B2C');
                        // debug($formatted_search_data);exit;
                        //Display Bus List
                        $currency_obj = new Currency(array('module_type' => 'bus', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
                        $raw_bus_list = force_multple_data_format($raw_bus_list['data']['result']);
                        
                        //Update commission
                        // $raw_bus_list = $this->bus_lib->update_bus_search_commission($raw_bus_list, $currency_obj);
                        // echo 'herre';
                        //debug($formatted_search_data);die('77');
                        
                        $response['segment']['data'] = get_compressed_output(
                        $this->template->isolated_view('bus/travelyaari/travelyaari_search_result',
                        array('currency_obj' => $currency_obj, 'raw_bus_list' => $formatted_search_data, 'search_id' => $search_params['search_id'], 'booking_source' => $search_params['booking_source'],'search_data_city' => $search_data_city))
                        );
                        $response['status'] = SUCCESS_STATUS;
                    }
                break;

                case ETS_BUS_BOOKING_SOURCE :
                    $raw_bus_list = $this->bus_lib->get_bus_list(abs($search_params['search_id']));
                    //debug($raw_bus_list);exit('vrl'); 
                    $from_id = @$raw_bus_list['data']['result'][0]['From'];
                    $to_id = @$raw_bus_list['data']['result'][0]['To'];
                    $form_data = $this->bus_model->get_bus_station_data($search_data['from_station_id']);
                        $to_data = $this->bus_model->get_bus_station_data($search_data['to_station_id']);
                    $search_data_city = array('from_id' => $form_data->station_id,
                                               'to_id' => $to_data->station_id);
                    
                    if ($raw_bus_list['status']) {
                        //Converting API currency data to preferred currency
                        $currency_obj = new Currency(array('module_type' => 'bus','from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
                        $raw_bus_list = $this->bus_lib->search_data_in_preferred_currency($raw_bus_list, $currency_obj);
                         // debug($raw_bus_list);exit; 
                        $formatted_search_data = $this->bus_lib->format_search_response($raw_bus_list, $currency_obj, $search_params['search_id'], 'bus','B2C');
                        // debug($formatted_search_data);exit;
                        //Display Bus List
						/*=========================== Shrikant ===============================*/
						$ets_block_buses = $this->bus_model->get_ets_display_buses();
						$formatted_search_data = $this->display_ets_opertors($formatted_search_data, $ets_block_buses);
						/*====================================================================*/
                        $currency_obj = new Currency(array('module_type' => 'bus', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
                        $raw_bus_list = force_multple_data_format($raw_bus_list['data']['result']);
                        
                        //Update commission
                        // $raw_bus_list = $this->bus_lib->update_bus_search_commission($raw_bus_list, $currency_obj);
                        // echo 'herre';
                        //debug($formatted_search_data);die('77');
                        
                        $response['segment']['data'] = get_compressed_output(
                        $this->template->isolated_view('bus/travelyaari/travelyaari_search_result',
                        array('currency_obj' => $currency_obj, 'raw_bus_list' => $formatted_search_data, 'search_id' => $search_params['search_id'], 'booking_source' => $search_params['booking_source'],'search_data_city' => $search_data_city))
                        );
                        $response['status'] = SUCCESS_STATUS;
                    }
                break;

            }
        }
        $this->output_compressed_data($response);
    }

    function get_bus_information() {
        $response['data'] = 'No Details Found';
        $response['status'] = false;
        //check params
        $params = $this->input->post();
        if (empty($params['booking_source']) == false and empty($params['search_id']) == false and intval($params['search_id']) > 0) {
            load_bus_lib($params['booking_source']);
            switch ($params['booking_source']) {
                case PROVAB_BUS_BOOKING_SOURCE :
                    $currency_obj = new Currency(array('module_type' => 'bus', 'from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
                    $details = $this->bus_lib->get_bus_information($params['route_schedule_id'], $params['journey_date']);
                    if ($details['status'] == SUCCESS_STATUS) {
                        $response['stauts'] = SUCCESS_STATUS;
                        $page_data['search_id'] = $params['search_id'];
                        $page_data['details'] = @$details['data']['result'];
                        $page_data['currency_obj'] = $currency_obj;
                        $response['data'] = get_compressed_output($this->template->isolated_view('bus/travelyaari/travelyaari_bus_info', $page_data));
                        $response['status'] = SUCCESS_STATUS;
                    }
                    break;
            }
        }
        $this->output_compressed_data($response);
    }

    /**
     * Get Bus Booking List
     */
    function get_bus_details($filter_boarding_points = false) {
        $this->load->model('bus_model');
        $response['data'] = 'No Details Found !! Try Later';
        $response['status'] = false;
        //check params
        $params = $this->input->post();
       
        $params = explode('*', $params['route_schedule_id']);
      
        $params['booking_source'] = $params[3];
        $params['search_id'] = $params[1];
        $params['route_schedule_id'] = $params[0];
        $params['route_code'] =  $params[2];
        $search_data = $this->bus_model->get_search_data($params['search_id']);
        $search_data = json_decode($search_data['search_data'], true);
        $form_data = $this->bus_model->get_bus_station_data($search_data['from_station_id']);
        $to_data = $this->bus_model->get_bus_station_data($search_data['to_station_id']);
        if (empty($params['booking_source']) == false and empty($params['search_id']) == false and intval($params['search_id']) > 0) {

            load_bus_lib($params['booking_source']);

            switch ($params['booking_source']) {
                case PROVAB_BUS_BOOKING_SOURCE :
                    $bus_info_data = $this->bus_lib->get_route_details($params['search_id'], $params['route_schedule_id'], $params[2]);
                    $bus_info_data['bus_data']['Form_id'] = $form_data->station_id;
                    $bus_info_data['bus_data']['To_id'] = $to_data->station_id;
                    
                    $params['journey_date'] = $bus_info_data['bus_data']['DepartureTime'];
                    $params['ResultToken'] = $bus_info_data['bus_data']['ResultToken'];
                    $details = $this->bus_lib->get_bus_details($params['route_schedule_id'], $params['journey_date'], $params['route_code'], $params['ResultToken'], $params['booking_source']);
                    
                    // debug($details);exit;
                    if ($details['status'] == SUCCESS_STATUS) {
                        //Converting API currency data to preferred currency
                        $currency_obj = new Currency(array(
                            'module_type' => 'bus',
                            'from' => get_api_data_currency(),
                            'to' => get_application_currency_preference()
                        ));
                        $details = $this->bus_lib->seatdetails_in_preferred_currency($details, $bus_info_data['bus_data'], $currency_obj);
                        // debug($details);exit;
                        //Display Bus Details
                        $currency_obj = new Currency(array('module_type' => 'bus', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
                        $response['stauts'] = SUCCESS_STATUS;
                        $page_data['search_id'] = $params['search_id'];
                        $page_data['ResultToken'] = $params['ResultToken'];
                        $page_data['details'] = $details['data']['result'];
                        $page_data['currency_obj'] = $currency_obj;
                        // debug($page_data);exit;
                        if ($filter_boarding_points == false) {
                            $response['data'] = get_compressed_output($this->template->isolated_view('bus/travelyaari/travelyaari_bus_details', $page_data));
                        } else {
                            $response['data'] = get_compressed_output($this->template->isolated_view('bus/travelyaari/travelyaari_boarding_details', $page_data));
                        }
                        $response['status'] = SUCCESS_STATUS;
                    }
                break;

                case VRL_BUS_BOOKING_SOURCE :
                    $bus_info_data = $this->bus_lib->get_route_details($params['search_id'], $params['route_schedule_id'], $params[2]);
                    $bus_info_data['bus_data']['Form_id'] = $form_data->station_id;
                    $bus_info_data['bus_data']['To_id'] = $to_data->station_id;
                    
                    $params['journey_date'] = $bus_info_data['bus_data']['DepartureTime'];
                    $params['ResultToken'] = $bus_info_data['bus_data']['ResultToken'];
                    $params['SeatFare'] = $bus_info_data['bus_data']['SeatFare'];
                    $params['BookedSeat'] = $bus_info_data['bus_data']['BookedSeat'];

                    $details = $this->bus_lib->get_bus_details($params['route_schedule_id'], $params['journey_date'],$params['route_code'],$params['ResultToken'],$params['booking_source'],$params['SeatFare'],$params['BookedSeat']);

                    /*debug($details);
                    die('456');*/
                    
                    if ($details['status'] == SUCCESS_STATUS) {
                        //Converting API currency data to preferred currency
                        $currency_obj = new Currency(array(
                                'module_type' => 'bus',
                                'from' => get_api_data_currency(), 
                                'to' => get_application_currency_preference()
                            ));
                        $details = $this->bus_lib->seatdetails_in_preferred_currency($details, $bus_info_data['bus_data'], $currency_obj);
                        
                        $formatted_seat = $this->bus_lib->seat_layout_format($details['data']['result']['result']['value'], $currency_obj,'bus','B2C');
                        $details['data']['result']['result']['value'] = $formatted_seat;
						$details['data']['result']['result']['Canc'] = $this->bus_lib->format_cancel_policy($details['data']['result']['result']['Canc']);
                        // debug($details);exit;
                        //Display Bus Details
                        $currency_obj = new Currency(array('module_type' => 'bus', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
                        $response['stauts'] = SUCCESS_STATUS;
                        $page_data['search_id'] = $params['search_id'];
                        $page_data['ResultToken'] = $params['ResultToken'];
                        
                        $page_data['details'] = $details['data']['result'];
                        $page_data['currency_obj'] = $currency_obj;

                        $page_data['details']['result']['Pickups']=$bus_info_data['bus_data']['Pickups'];
                        $page_data['details']['result']['Dropoffs']=$bus_info_data['bus_data']['Dropoffs'];
                        //$page_data['details']['result']['Canc']=$bus_info_data['bus_data']['CancPolicy'];


                        //debug($page_data);exit('789');                        
                        if ($filter_boarding_points == false) {
                            $response['data'] = get_compressed_output($this->template->isolated_view('bus/vrl/vrl_bus_details', $page_data));
                        } else {
                            $response['data'] = get_compressed_output($this->template->isolated_view('bus/travelyaari/travelyaari_boarding_details', $page_data));
                        }
                        $response['status'] = SUCCESS_STATUS;
                    }
                break;

                case BITLA_BUS_BOOKING_SOURCE :
                    $bus_info_data = $this->bus_lib->get_route_details($params['search_id'], $params['route_schedule_id'], $params[2]);
                    //debug($bus_info_data['bus_data']['Pickups']);die();
                    $bus_info_data['bus_data']['Form_id'] = $form_data->station_id;
                    $bus_info_data['bus_data']['To_id'] = $to_data->station_id;
                    
                    $params['journey_date'] = $bus_info_data['bus_data']['DepartureTime'];
                    $params['ResultToken'] = $bus_info_data['bus_data']['ResultToken'];
                    $params['SeatFare'] = $bus_info_data['bus_data']['SeatFare'];
                    $params['BookedSeat'] = $bus_info_data['bus_data']['BookedSeat'];

                    $details = $this->bus_lib->get_bus_details($params['route_schedule_id'], $params['journey_date'],$params['route_code'],$params['ResultToken'],$params['booking_source'],$params['SeatFare'],$params['BookedSeat']);

                    /*debug($details);
                    die('456');*/
                    
                    if ($details['status'] == SUCCESS_STATUS) {
                        //Converting API currency data to preferred currency
                        $currency_obj = new Currency(array(
                                'module_type' => 'bus',
                                'from' => get_api_data_currency(), 
                                'to' => get_application_currency_preference()
                            ));
                        $details = $this->bus_lib->seatdetails_in_preferred_currency($details, $bus_info_data['bus_data'], $currency_obj);
                        
                        $formatted_seat = $this->bus_lib->seat_layout_format($details['data']['result']['result']['value'], $currency_obj,'bus','B2C');
                        $details['data']['result']['result']['value'] = $formatted_seat;
                        // debug($details);exit;
                        //Display Bus Details
                        $currency_obj = new Currency(array('module_type' => 'bus', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
                        $response['stauts'] = SUCCESS_STATUS;
                        $page_data['search_id'] = $params['search_id'];
                        $page_data['ResultToken'] = $params['ResultToken'];
                        
                        $page_data['details'] = $details['data']['result'];
                        $page_data['currency_obj'] = $currency_obj;

                        //$page_data['details']['result']['Pickups']=$bus_info_data['bus_data']['Pickups'];
                        //$page_data['details']['result']['Dropoffs']=$bus_info_data['bus_data']['Dropoffs'];
                        //$page_data['details']['result']['Canc']=$bus_info_data['bus_data']['CancPolicy'];


                        //debug($page_data);exit('789');                        
                        if ($filter_boarding_points == false) {
                            $response['data'] = get_compressed_output($this->template->isolated_view('bus/bitla/bitla_bus_details', $page_data));
                        } else {
							$page_data['details']['result']['Canc']=$bus_info_data['bus_data']['CancPolicy'];
                            $response['data'] = get_compressed_output($this->template->isolated_view('bus/travelyaari/travelyaari_boarding_details', $page_data));
                        }
                        $response['status'] = SUCCESS_STATUS;
                    }
                break;
				
				case SRS_BUS_BOOKING_SOURCE :
                    $bus_info_data = $this->bus_lib->get_route_details($params['search_id'], $params['route_schedule_id'], $params[2]);
                    //debug($bus_info_data['bus_data']['Pickups']);die();
                    $bus_info_data['bus_data']['Form_id'] = $form_data->station_id;
                    $bus_info_data['bus_data']['To_id'] = $to_data->station_id;
                    
                    $params['journey_date'] = $bus_info_data['bus_data']['DepartureTime'];
                    $params['ResultToken'] = $bus_info_data['bus_data']['ResultToken'];
                    $params['SeatFare'] = $bus_info_data['bus_data']['SeatFare'];
                    $params['BookedSeat'] = $bus_info_data['bus_data']['BookedSeat'];

                    $details = $this->bus_lib->get_bus_details($params['route_schedule_id'], $params['journey_date'],$params['route_code'],$params['ResultToken'],$params['booking_source'],$params['SeatFare'],$params['BookedSeat']);

                    /*debug($details);
                    die('456');*/
                    
                    if ($details['status'] == SUCCESS_STATUS) {
                        //Converting API currency data to preferred currency
                        $currency_obj = new Currency(array(
                                'module_type' => 'bus',
                                'from' => get_api_data_currency(), 
                                'to' => get_application_currency_preference()
                            ));
                        $details = $this->bus_lib->seatdetails_in_preferred_currency($details, $bus_info_data['bus_data'], $currency_obj);
                        
                        $formatted_seat = $this->bus_lib->seat_layout_format($details['data']['result']['result']['value'], $currency_obj,'bus','B2C');
                        $details['data']['result']['result']['value'] = $formatted_seat;
                        // debug($details);exit;
                        //Display Bus Details
                        $currency_obj = new Currency(array('module_type' => 'bus', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
                        $response['stauts'] = SUCCESS_STATUS;
                        $page_data['search_id'] = $params['search_id'];
                        $page_data['ResultToken'] = $params['ResultToken'];
                        
                        $page_data['details'] = $details['data']['result'];
                        $page_data['currency_obj'] = $currency_obj;

                        //$page_data['details']['result']['Pickups']=$bus_info_data['bus_data']['Pickups'];
                        //$page_data['details']['result']['Dropoffs']=$bus_info_data['bus_data']['Dropoffs'];
                        //$page_data['details']['result']['Canc']=$bus_info_data['bus_data']['CancPolicy'];


                        //debug($page_data);exit('789');                        
                        if ($filter_boarding_points == false) {
                            $response['data'] = get_compressed_output($this->template->isolated_view('bus/srs/srs_bus_details', $page_data)); 
                        } else {
							$page_data['details']['result']['Canc']=$bus_info_data['bus_data']['CancPolicy'];
                            $response['data'] = get_compressed_output($this->template->isolated_view('bus/travelyaari/travelyaari_boarding_details', $page_data));
                        }
                        $response['status'] = SUCCESS_STATUS;
                    }
                break;
					
                case KUKKESHREE_BUS_BOOKING_SOURCE :
                    $bus_info_data = $this->bus_lib->get_route_details($params['search_id'], $params['route_schedule_id'], $params[2]);
                    $bus_info_data['bus_data']['Form_id'] = $form_data->station_id;
                    $bus_info_data['bus_data']['To_id'] = $to_data->station_id;
                    
                    $params['journey_date'] = $bus_info_data['bus_data']['DepartureTime'];
                    $params['ResultToken'] = $bus_info_data['bus_data']['ResultToken'];
                    $params['SeatFare'] = $bus_info_data['bus_data']['SeatFare'];
                    $params['BookedSeat'] = $bus_info_data['bus_data']['BookedSeat'];

                    $details = $this->bus_lib->get_bus_details($params['route_schedule_id'], $params['journey_date'],$params['route_code'],$params['ResultToken'],$params['booking_source'],$params['SeatFare'],$params['BookedSeat']);

                    /*debug($details);
                    die('456');*/
                    
                    if ($details['status'] == SUCCESS_STATUS) {
                        //Converting API currency data to preferred currency
                        $currency_obj = new Currency(array(
                                'module_type' => 'bus',
                                'from' => get_api_data_currency(), 
                                'to' => get_application_currency_preference()
                            ));
                        $details = $this->bus_lib->seatdetails_in_preferred_currency($details, $bus_info_data['bus_data'], $currency_obj);
                        
                        $formatted_seat = $this->bus_lib->seat_layout_format($details['data']['result']['result']['value'], $currency_obj,'bus','B2C');
                        $details['data']['result']['result']['value'] = $formatted_seat;
                        // debug($details);exit;
                        //Display Bus Details
                        $currency_obj = new Currency(array('module_type' => 'bus', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
                        $response['stauts'] = SUCCESS_STATUS;
                        $page_data['search_id'] = $params['search_id'];
                        $page_data['ResultToken'] = $params['ResultToken'];
                        
                        $page_data['details'] = $details['data']['result'];
                        $page_data['currency_obj'] = $currency_obj;

                        //$page_data['details']['result']['Pickups']=$bus_info_data['bus_data']['Pickups'];
                        //$page_data['details']['result']['Dropoffs']=$bus_info_data['bus_data']['Dropoffs'];
                        //$page_data['details']['result']['Canc']=$bus_info_data['bus_data']['CancPolicy'];


                        //debug($page_data);exit;       
                        if ($filter_boarding_points == false) {
                            $response['data'] = get_compressed_output($this->template->isolated_view('bus/kukkeshree/kukkeshree_bus_details', $page_data));
                        } else {
							$page_data['details']['result']['Canc']=$bus_info_data['bus_data']['CancPolicy'];
                            $response['data'] = get_compressed_output($this->template->isolated_view('bus/travelyaari/travelyaari_boarding_details', $page_data));
                        }
                        $response['status'] = SUCCESS_STATUS;
                    }
                break;

                case KRL_BUS_BOOKING_SOURCE :
                    $bus_info_data = $this->bus_lib->get_route_details($params['search_id'], $params['route_schedule_id'], $params[2]);
                    $bus_info_data['bus_data']['Form_id'] = $form_data->station_id;
                    $bus_info_data['bus_data']['To_id'] = $to_data->station_id;
                    
                    $params['journey_date'] = $bus_info_data['bus_data']['DepartureTime'];
                    $params['ResultToken'] = $bus_info_data['bus_data']['ResultToken'];
                    $params['SeatFare'] = $bus_info_data['bus_data']['SeatFare'];
                    $params['BookedSeat'] = $bus_info_data['bus_data']['BookedSeat'];

                    $details = $this->bus_lib->get_bus_details($params['route_schedule_id'], $params['journey_date'],$params['route_code'],$params['ResultToken'],$params['booking_source'],$params['SeatFare'],$params['BookedSeat']);

                    /*debug($details);
                    die('456');*/
                    
                    if ($details['status'] == SUCCESS_STATUS) {
                        //Converting API currency data to preferred currency
                        $currency_obj = new Currency(array(
                                'module_type' => 'bus',
                                'from' => get_api_data_currency(), 
                                'to' => get_application_currency_preference()
                            ));
                        $details = $this->bus_lib->seatdetails_in_preferred_currency($details, $bus_info_data['bus_data'], $currency_obj);
                        
                        $formatted_seat = $this->bus_lib->seat_layout_format($details['data']['result']['result']['value'], $currency_obj,'bus','B2C');
                        $details['data']['result']['result']['value'] = $formatted_seat;
                        // debug($details);exit;
                        //Display Bus Details
                        $currency_obj = new Currency(array('module_type' => 'bus', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
                        $response['stauts'] = SUCCESS_STATUS;
                        $page_data['search_id'] = $params['search_id'];
                        $page_data['ResultToken'] = $params['ResultToken'];
                        
                        $page_data['details'] = $details['data']['result'];
                        $page_data['currency_obj'] = $currency_obj;

                        $page_data['details']['result']['Pickups']=$bus_info_data['bus_data']['Pickups'];
                        $page_data['details']['result']['Dropoffs']=$bus_info_data['bus_data']['Dropoffs'];
                        //$page_data['details']['result']['Canc']=$bus_info_data['bus_data']['CancPolicy'];


                        //debug($page_data);exit('789');                        
                        if ($filter_boarding_points == false) {
                            $response['data'] = get_compressed_output($this->template->isolated_view('bus/krl/krl_bus_details', $page_data));
                        } else {
                            $response['data'] = get_compressed_output($this->template->isolated_view('bus/travelyaari/travelyaari_boarding_details', $page_data));
                        }
                        $response['status'] = SUCCESS_STATUS;
                    }
                break;

                case ETS_BUS_BOOKING_SOURCE :

                    $bus_info_data = $this->bus_lib->get_route_details($params['search_id'], $params['route_schedule_id'], $params[2]);
                    $bus_info_data['bus_data']['Form_id'] = $form_data->station_id;
                    $bus_info_data['bus_data']['To_id'] = $to_data->station_id;
                    $params['journey_date'] = $bus_info_data['bus_data']['DepartureTime'];
                    $params['ResultToken'] = $bus_info_data['bus_data']['ResultToken'];
                    $params['inventoryType'] = $bus_info_data['bus_data']['inventoryType'];

                    $details = $this->bus_lib->get_bus_details($params['route_schedule_id'], $params['journey_date'],$params['route_code'],$params['ResultToken'],$params['booking_source'],$params['inventoryType'],$search_data);
                    //debug($details);die('456456');
                    
                    if ($details['status'] == SUCCESS_STATUS) {
                        //Converting API currency data to preferred currency
                        $currency_obj = new Currency(array(
                                'module_type' => 'bus',
                                'from' => get_api_data_currency(), 
                                'to' => get_application_currency_preference()
                            ));
                        $details = $this->bus_lib->seatdetails_in_preferred_currency($details, $bus_info_data['bus_data'], $currency_obj);
                        //debug($details);die('45645611');
                        $formatted_seat = $this->bus_lib->seat_layout_format($details['data']['result']['result']['value'], $currency_obj,'bus','B2C');

                        $details['data']['result']['result']['value'] = $formatted_seat;
                        // debug($details);exit;
                        //Display Bus Details
                        $currency_obj = new Currency(array('module_type' => 'bus', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
                        $response['stauts'] = SUCCESS_STATUS;
                        $page_data['search_id'] = $params['search_id'];
                        $page_data['ResultToken'] = $params['ResultToken'];
                        
                        $page_data['details'] = $details['data']['result'];
                        $page_data['currency_obj'] = $currency_obj;
                        //debug($page_data['details']['result']['Pickups']);exit('77');
                        if($params['inventoryType'] != 2 || $params['inventoryType'] != 4 || $params['inventoryType'] != 6){
                            $page_data['details']['result']['Pickups']=$bus_info_data['bus_data']['Pickups'];
                            $page_data['details']['result']['Dropoffs']=$bus_info_data['bus_data']['Dropoffs'];
                            $page_data['details']['result']['Canc']=$bus_info_data['bus_data']['CancPolicy'];
                        }

                        if ($filter_boarding_points == false) {
                            $response['data'] = get_compressed_output($this->template->isolated_view('bus/ets/ets_bus_details', $page_data));
                        } else {
                            $response['data'] = get_compressed_output($this->template->isolated_view('bus/travelyaari/travelyaari_boarding_details', $page_data));
                        }
                        $response['status'] = SUCCESS_STATUS;
                    }
                break;


            }
        }
        // debug($response);exit;
        $this->output_compressed_data($response);
    }

    /**
     * Load hotels from different source
     */
    function get_room_details() {
        $response['data'] = '';
        $response['msg'] = '';
        $response['status'] = FAILURE_STATUS;
        $params = $this->input->post();
       
        ini_set('memory_limit', '250M');
        if ($params['op'] == 'get_room_details' && intval($params['search_id']) > 0 && isset($params['booking_source']) == true) {
            $application_preferred_currency = get_application_currency_preference();
            $application_default_currency = get_application_currency_preference();
            load_hotel_lib($params['booking_source']);
            $this->hotel_lib->search_data($params['search_id']);
            $attr['search_id'] = intval($params['search_id']);
            switch ($params['booking_source']) {
                case PROVAB_HOTEL_BOOKING_SOURCE :
                    $raw_room_list = $this->hotel_lib->get_room_list(urldecode($params['ResultIndex']));
                    $safe_search_data = $this->hotel_model->get_safe_search_data($params['search_id']);
                    // debug($raw_room_list);exit;
                    if ($raw_room_list['status']) {
                        //Converting API currency data to preferred currency
                        $currency_obj = new Currency(array('module_type' => 'hotel', 'from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
                        $raw_room_list = $this->hotel_lib->roomlist_in_preferred_currency($raw_room_list, $currency_obj, $params['search_id']);

                        //Display
                        $currency_obj = new Currency(array('module_type' => 'hotel', 'from' => $application_default_currency, 'to' => $application_preferred_currency));
                        //debug($raw_room_list);exit;
                        $response['data'] = get_compressed_output($this->template->isolated_view('hotel/tbo/tbo_room_list', array('currency_obj' => $currency_obj,
                                    'params' => $params, 'raw_room_list' => $raw_room_list['data'],
                                    'hotel_search_params' => $safe_search_data['data'],
                                    'application_preferred_currency' => $application_preferred_currency,
                                    'application_default_currency' => $application_default_currency,
                                    'attr' => $attr
                                        )
                                )
                        );
                        $response['status'] = SUCCESS_STATUS;
                    }
                break;

                case REZLIVE_HOTEL :

                   //debug($params);die();
                    $raw_room_list = $this->hotel_lib->get_room_list($params);

                    //debug($raw_room_list);die('123');
                    $safe_search_data = $this->hotel_model->get_safe_search_data($params['search_id']);
                    if ($raw_room_list['status']) {
                        //Converting API currency data to preferred currency
                        $currency_obj = new Currency(array('module_type' => 'hotel','from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
                        //debug($currency_obj);die('+++');
                        $raw_room_list = $this->hotel_lib->roomlist_in_preferred_currency($raw_room_list, $currency_obj,$params['search_id'],'b2c');
                        //Display
                        $currency_obj = new Currency(array('module_type' => 'hotel','from' => $application_default_currency, 'to' => $application_preferred_currency));
                        //debug($raw_room_list);die('+++');
                        $response['data'] = get_compressed_output($this->template->isolated_view('hotel/tbo/tbo_room_list',
                        array('currency_obj' => $currency_obj,
                                'params' => $params, 'raw_room_list' => $raw_room_list['data'],
                                'hotel_search_params'=>$safe_search_data['data'],
                                'application_preferred_currency' => $application_preferred_currency,
                                'application_default_currency' => $application_default_currency,
                                'attr' => $attr
                        )
                        )
                        );
                        $response['status'] = SUCCESS_STATUS;
                    }
                break;
            }
        }
        $this->output_compressed_data($response);
    }

    /**
     * Get Hotel Images by HotelCode
     */
    function get_hotel_images() {
        $post_params = $this->input->post();
        if ($post_params['hotel_code']) {
            //debug($post_params['hotel_code']);exit;
            switch ($post_params['booking_source']) {

                case PROVAB_HOTEL_BOOKING_SOURCE:
                    load_hotel_lib($post_params['booking_source']);
                    $raw_hotel_images = $this->hotel_lib->get_hotel_images($post_params['hotel_code']);

                    if ($raw_hotel_images['status'] == true) {

                        $this->hotel_model->add_hotel_images($post_params['search_id'], $raw_hotel_images['data'], $post_params['hotel_code']);

                        $response['data'] = get_compressed_output(
                                $this->template->isolated_view('hotel/tbo/tbo_hotel_images', array('hotel_images' => $raw_hotel_images, 'HotelCode' => $post_params['hotel_code'], 'HotelName' => $post_params['Hotel_name']
                        )));
                    }

                    break;
            }
            $this->output_compressed_data($response);
        }
        exit;
    }
    
    
    
     /**
     * Load hotels from different source
     */
    function transfer_list($search_id = '') {
        //echo $search_id; die;
        $response['data'] = '';
        $response['msg'] = '';
        $response['status'] = FAILURE_STATUS;
        $search_params = $this->input->get();
        
        $page_params['search_id'] = $search_params['search_id'];
        if ($search_params['op'] == 'load' && intval($search_params['search_id']) > 0 && isset($search_params['booking_source']) == true) {
             
            load_transfer_lib($search_params['booking_source']);
         
              switch ($search_params['booking_source']) {
                   case HOTELBED_TRANSFER_BOOKING_SOURCE :
                      
                         $raw_tranfer_list = $this->transfer_lib->get_tranfer_list(abs($search_params['search_id']));
                         break;
              }
         }
    }
    


    /**
     * Load Flight from different source
     */
    function flight_list($search_id = '') {
        //echo $search_id; die;
        $response['data'] = '';
        $response['msg'] = '';
        $response['status'] = FAILURE_STATUS;
        $search_params = $this->input->get();
        $lcc_or_gds = $search_params["lcc_gds"];
      
        $allow = TRUE;
        if($lcc_or_gds != "0"){
        if(in_array(trim($search_params['booking_source']), $this->config->item($lcc_or_gds)))
            $allow = TRUE;
        else
            $allow = FALSE;
        }
        if(!$allow)
        {
            $this->output_compressed_data_flight($response);
            exit;
        }
        $page_params['search_id'] = $search_params['search_id'];
        if ($search_params['op'] == 'load' && intval($search_params['search_id']) > 0 && isset($search_params['booking_source']) == true) {
            load_flight_lib($search_params['booking_source']);
            switch ($search_params['booking_source']) {
                case PROVAB_FLIGHT_BOOKING_SOURCE :
                    $raw_flight_list = $this->flight_lib->get_flight_list(abs($search_params['search_id']),$search_params['booking_source']);
                    //debug($raw_flight_list);die();
                    if ($raw_flight_list['status']) {
                        //View Data
                        $raw_search_result = $raw_flight_list['data']['Search']['FlightDataList'];
                      
                        // echo get_application_currency_preference();exit;
                        //Converting API currency data to preferred currency
                        $currency_obj = new Currency(array('module_type' => 'flight', 'from' => get_api_data_currency(), 'to' => get_application_currency_preference()));

                        $raw_search_result = $this->flight_lib->search_data_in_preferred_currency($raw_search_result, $currency_obj);
                       
                        //Display
                        $currency_obj = new Currency(array('module_type' => 'flight', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
                        $formatted_search_data = $this->flight_lib->format_search_response($raw_search_result, $currency_obj, $search_params['search_id'], $this->current_module, $raw_flight_list['from_cache'], $raw_flight_list['search_hash']);
                        
                      
                        $raw_flight_list['data'] = $formatted_search_data['data'];
                        $route_count = count($raw_flight_list['data']['Flights']);
                        $domestic_round_way_flight = $raw_flight_list['data']['JourneySummary']['IsDomesticRoundway'];

                        //if (($route_count > 0  && $domestic_round_way_flight == false) || ($route_count == 2 && $domestic_round_way_flight == true)) {
                        //debug($raw_flight_list);exit;
                        if (($route_count > 0)) {//later enable above condition
                            $attr['search_id'] = abs($search_params['search_id']);
                            $page_params = array(
                                'raw_flight_list' => $raw_flight_list['data'],
                                'search_id' => $search_params['search_id'],
                                'booking_url' => $formatted_search_data['booking_url'],
                                'booking_source' => $search_params['booking_source'],
                                'cabin_class' => $raw_flight_list['cabin_class'],
                                'trip_type' => $this->flight_lib->master_search_data['trip_type'],
                                'attr' => $attr,
                                'route_count' => $route_count,
                                'IsDomestic' => $raw_flight_list['data']['JourneySummary']['IsDomestic']
                            );
                            $page_params['domestic_round_way_flight'] = $domestic_round_way_flight;
                            // debug($page_params);exit;
                            $page_view_data = $this->template->isolated_view('flight/tbo/tbo_col2x_search_result', $page_params);
                            $response['data'] = get_compressed_output($page_view_data);
                            $response['status'] = SUCCESS_STATUS;

                            /*
                              session expiry start time and search hash
                             */
                            $response['session_expiry_details'] = $formatted_search_data['session_expiry_details'];
                        }
                    }
                break;
                case TRAVELPORT_ACH_BOOKING_SOURCE :
                    $raw_flight_list = $this->flight_lib->get_flight_list(abs($search_params['search_id']),$search_params['booking_source']);
                    //debug($raw_flight_list);die();
                    if ($raw_flight_list['status']) {
                        //View Data
                        $raw_search_result = $raw_flight_list['data']['Search']['FlightDataList'];
                      
                        // echo get_application_currency_preference();exit;
                        //Converting API currency data to preferred currency
                        $currency_obj = new Currency(array('module_type' => 'flight', 'from' => get_api_data_currency(), 'to' => get_application_currency_preference()));

                        $raw_search_result = $this->flight_lib->search_data_in_preferred_currency($raw_search_result, $currency_obj,$search_params['search_id']);
                       
                        //Display
                        $currency_obj = new Currency(array('module_type' => 'flight', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
                        $formatted_search_data = $this->flight_lib->format_search_response($raw_search_result, $currency_obj, $search_params['search_id'], $this->current_module, $raw_flight_list['from_cache'], $raw_flight_list['search_hash']);
                        
                      
                        $raw_flight_list['data'] = $formatted_search_data['data'];
                        $route_count = count($raw_flight_list['data']['Flights']);
                        $domestic_round_way_flight = $raw_flight_list['data']['JourneySummary']['IsDomesticRoundway'];
                        // debug($route_count);exit;
                        //if (($route_count > 0  && $domestic_round_way_flight == false) || ($route_count == 2 && $domestic_round_way_flight == true)) {
                        //debug($raw_flight_list);exit;
                        if(valid_array($raw_flight_list['data']['Flights'][0])){//later enable above condition
                            $attr['search_id'] = abs($search_params['search_id']);
                            $page_params = array(
                                'raw_flight_list' => $raw_flight_list['data'],
                                'search_id' => $search_params['search_id'],
                                'booking_url' => $formatted_search_data['booking_url'],
                                'booking_source' => $search_params['booking_source'],
                                'cabin_class' => $raw_flight_list['cabin_class'],
                                'trip_type' => $this->flight_lib->master_search_data['trip_type'],
                                'attr' => $attr,
                                'route_count' => $route_count,
                                'IsDomestic' => $raw_flight_list['data']['JourneySummary']['IsDomestic']
                            );
                            $page_params['domestic_round_way_flight'] = $domestic_round_way_flight;
                             //debug($page_params);exit;
                            $page_view_data = $this->template->isolated_view('flight/tbo/tbo_col2x_search_result', $page_params);
                            $response['data'] = get_compressed_output($page_view_data);
                            $response['status'] = SUCCESS_STATUS;

                            /*
                              session expiry start time and search hash
                             */
                            $response['session_expiry_details'] = $formatted_search_data['session_expiry_details'];
                        }
                    }
                break;
                case TRAVELPORT_GDS_BOOKING_SOURCE :
                    $raw_flight_list = $this->flight_lib->get_flight_list(abs($search_params['search_id']),$search_params['booking_source']);
                    //debug($raw_flight_list);die();
                    if ($raw_flight_list['status']) {
                        //View Data
                        $raw_search_result = $raw_flight_list['data']['Search']['FlightDataList'];
                      
                        // echo get_application_currency_preference();exit;
                        //Converting API currency data to preferred currency
                        $currency_obj = new Currency(array('module_type' => 'flight', 'from' => get_api_data_currency(), 'to' => get_application_currency_preference()));

                        $raw_search_result = $this->flight_lib->search_data_in_preferred_currency($raw_search_result, $currency_obj,$search_params['search_id']);
                       
                        //Display
                        $currency_obj = new Currency(array('module_type' => 'flight', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
                        $formatted_search_data = $this->flight_lib->format_search_response($raw_search_result, $currency_obj, $search_params['search_id'], $this->current_module, $raw_flight_list['from_cache'], $raw_flight_list['search_hash']);
                        
                      
                        $raw_flight_list['data'] = $formatted_search_data['data'];
                        $route_count = count($raw_flight_list['data']['Flights']);
                        $domestic_round_way_flight = $raw_flight_list['data']['JourneySummary']['IsDomesticRoundway'];

                        //if (($route_count > 0  && $domestic_round_way_flight == false) || ($route_count == 2 && $domestic_round_way_flight == true)) {
                        //debug($raw_flight_list);exit;
                        if (($route_count > 0)) {//later enable above condition
                            $attr['search_id'] = abs($search_params['search_id']);
                            $page_params = array(
                                'raw_flight_list' => $raw_flight_list['data'],
                                'search_id' => $search_params['search_id'],
                                'booking_url' => $formatted_search_data['booking_url'],
                                'booking_source' => $search_params['booking_source'],
                                'cabin_class' => $raw_flight_list['cabin_class'],
                                'trip_type' => $this->flight_lib->master_search_data['trip_type'],
                                'attr' => $attr,
                                'route_count' => $route_count,
                                'IsDomestic' => $raw_flight_list['data']['JourneySummary']['IsDomestic']
                            );
                            $page_params['domestic_round_way_flight'] = $domestic_round_way_flight;
                             //debug($page_params);exit;
                            $page_view_data = $this->template->isolated_view('flight/tbo/tbo_col2x_search_result', $page_params);
                            $response['data'] = get_compressed_output($page_view_data);
                            $response['status'] = SUCCESS_STATUS;

                            /*
                              session expiry start time and search hash
                             */
                            $response['session_expiry_details'] = $formatted_search_data['session_expiry_details'];
                        }
                    }
                break;
                case SPICEJET_BOOKING_SOURCE :
                    $raw_flight_list = $this->flight_lib->get_flight_list(abs($search_params['search_id']),$search_params['booking_source']);
                    //debug($raw_flight_list);die();
                    if ($raw_flight_list['status']) {
                        //View Data
                        $raw_search_result = $raw_flight_list['data']['Search']['FlightDataList'];
                      
                        // echo get_application_currency_preference();exit;
                        //Converting API currency data to preferred currency
                        $currency_obj = new Currency(array('module_type' => 'flight', 'from' => get_api_data_currency(), 'to' => get_application_currency_preference()));

                        $raw_search_result = $this->flight_lib->search_data_in_preferred_currency($raw_search_result, $currency_obj,$search_params['search_id']);
                       
                        //Display
                        $currency_obj = new Currency(array('module_type' => 'flight', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
                        $formatted_search_data = $this->flight_lib->format_search_response($raw_search_result, $currency_obj, $search_params['search_id'], $this->current_module, $raw_flight_list['from_cache'], $raw_flight_list['search_hash']);
                        
                      
                        $raw_flight_list['data'] = $formatted_search_data['data'];
                        $route_count = count($raw_flight_list['data']['Flights']);
                        $domestic_round_way_flight = $raw_flight_list['data']['JourneySummary']['IsDomesticRoundway'];

                        //if (($route_count > 0  && $domestic_round_way_flight == false) || ($route_count == 2 && $domestic_round_way_flight == true)) {
                        //debug($raw_flight_list);exit;
                        if (($route_count > 0)) {//later enable above condition
                            $attr['search_id'] = abs($search_params['search_id']);
                            $page_params = array(
                                'raw_flight_list' => $raw_flight_list['data'],
                                'search_id' => $search_params['search_id'],
                                'booking_url' => $formatted_search_data['booking_url'],
                                'booking_source' => $search_params['booking_source'],
                                'cabin_class' => $raw_flight_list['cabin_class'],
                                'trip_type' => $this->flight_lib->master_search_data['trip_type'],
                                'attr' => $attr,
                                'route_count' => $route_count,
                                'IsDomestic' => $raw_flight_list['data']['JourneySummary']['IsDomestic']
                            );
                            $page_params['domestic_round_way_flight'] = $domestic_round_way_flight;
                             //debug($page_params);exit;
                            $page_view_data = $this->template->isolated_view('flight/tbo/tbo_col2x_search_result', $page_params);
                            $response['data'] = get_compressed_output($page_view_data);
                            $response['status'] = SUCCESS_STATUS;

                            /*
                              session expiry start time and search hash
                             */
                            $response['session_expiry_details'] = $formatted_search_data['session_expiry_details'];
                        }
                    }
                break;
                case STAR_BOOKING_SOURCE :
                    $raw_flight_list = $this->flight_lib->get_flight_list(abs($search_params['search_id']),$search_params['booking_source']);
                    //debug($raw_flight_list);die();
                    if ($raw_flight_list['status']) {
                        //View Data
                        $raw_search_result = $raw_flight_list['data']['Search']['FlightDataList'];
                      
                        // echo get_application_currency_preference();exit;
                        //Converting API currency data to preferred currency
                        $currency_obj = new Currency(array('module_type' => 'flight', 'from' => get_api_data_currency(), 'to' => get_application_currency_preference()));

                        $raw_search_result = $this->flight_lib->search_data_in_preferred_currency($raw_search_result, $currency_obj,$search_params['search_id']);
                       
                        //Display
                        $currency_obj = new Currency(array('module_type' => 'flight', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
                        $formatted_search_data = $this->flight_lib->format_search_response($raw_search_result, $currency_obj, $search_params['search_id'], $this->current_module, $raw_flight_list['from_cache'], $raw_flight_list['search_hash']);
                        
                      
                        $raw_flight_list['data'] = $formatted_search_data['data'];
                        $route_count = count($raw_flight_list['data']['Flights']);
                        $domestic_round_way_flight = $raw_flight_list['data']['JourneySummary']['IsDomesticRoundway'];

                        //if (($route_count > 0  && $domestic_round_way_flight == false) || ($route_count == 2 && $domestic_round_way_flight == true)) {
                        //debug($raw_flight_list);exit;
                        if (($route_count > 0)) {//later enable above condition
                            $attr['search_id'] = abs($search_params['search_id']);
                            $page_params = array(
                                'raw_flight_list' => $raw_flight_list['data'],
                                'search_id' => $search_params['search_id'],
                                'booking_url' => $formatted_search_data['booking_url'],
                                'booking_source' => $search_params['booking_source'],
                                'cabin_class' => $raw_flight_list['cabin_class'],
                                'trip_type' => $this->flight_lib->master_search_data['trip_type'],
                                'attr' => $attr,
                                'route_count' => $route_count,
                                'IsDomestic' => $raw_flight_list['data']['JourneySummary']['IsDomestic']
                            );
                            $page_params['domestic_round_way_flight'] = $domestic_round_way_flight;
                             //debug($page_params);exit;
                            $page_view_data = $this->template->isolated_view('flight/tbo/tbo_col2x_search_result', $page_params);
                            $response['data'] = get_compressed_output($page_view_data);
                            $response['status'] = SUCCESS_STATUS;

                            /*
                              session expiry start time and search hash
                             */
                            $response['session_expiry_details'] = $formatted_search_data['session_expiry_details'];
                        }
                    }
                break;   
            }
        }
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        $this->output_compressed_data_flight($response);
    }
    /** Anitha G
     * Load car from different source
    */
    
    
    /**
     * Load hotels from different source
     */
    function car_list($offset = 0) {
        $response['data'] = '';
        $response['msg'] = '';
        $response['status'] = FAILURE_STATUS;
        $search_params = $this->input->get();
        $limit = $this->config->item('car_per_page_limit');
        
        if ($search_params['op'] == 'load' && intval($search_params['search_id']) > 0 && isset($search_params['booking_source']) == true) {
            load_car_lib($search_params['booking_source']);
            switch ($search_params['booking_source']) {
                case PROVAB_CAR_BOOKING_SOURCE :
                    //getting search params from table
                    $safe_search_data = $this->car_model->get_safe_search_data($search_params['search_id']);
                    //Meaning hotels are loaded first time
                    $raw_car_list = $this->car_lib->get_car_list(abs($search_params['search_id']));
                    // debug($raw_car_list);exit;
                    if ($raw_car_list['status']) {
                        //Converting API currency data to preferred currency
                       
                        $currency_obj = new Currency(array('module_type' => 'car', 'from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
                        $raw_car_list = $this->car_lib->search_data_in_preferred_currency($raw_car_list, $currency_obj, $search_params['search_id']);
                        
                        //Display 
                        $currency_obj = new Currency(array('module_type' => 'car', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
                        // debug($currency_obj);exit;
                        //Update currency and filter summary appended
                        if (isset($search_params['filters']) == true and valid_array($search_params['filters']) == true) {
                            $filters = $search_params['filters'];
                        } else {
                            $filters = array();
                        }
                        //debug($raw_hotel_list);exit;
                        $raw_car_list['data'] = $this->car_lib->format_search_response($raw_car_list['data'], $currency_obj, $search_params['search_id'], 'b2c', $filters);

                        $source_result_count = $raw_car_list['data']['source_result_count'];
                        $filter_result_count = $raw_car_list['data']['filter_result_count'];
                        //debug($raw_hotel_list);exit;
                        if (intval($offset) == 0) {
                            //Need filters only if the data is being loaded first time
                            $filters = $this->car_lib->filter_summary($raw_car_list['data']);
                            $response['filters'] = $filters['data'];
                        }
                        // debug($raw_car_list['data']);exit;
                        $raw_car_list['data'] = $this->car_lib->get_page_data($raw_car_list['data'], $offset, $limit);

                        $attr['search_id'] = abs($search_params['search_id']);

                        
                        $response['data'] = get_compressed_output(
                                $this->template->isolated_view('car/car_search_result_page', array('currency_obj' => $currency_obj, 'raw_car_list' => $raw_car_list['data'],
                                    'search_id' => $search_params['search_id'], 'booking_source' => $search_params['booking_source'],
                                    'attr' => $attr,
                                    'search_params' => $safe_search_data
                        )));
                        $response['status'] = SUCCESS_STATUS;
                        $response['total_result_count'] = $source_result_count;
                        $response['filter_result_count'] = $filter_result_count;
                        $response['offset'] = $offset + $limit;
                    }
                    break;
            }
        }
        $this->output_compressed_data($response);
    }

   
    function transfer_tem() {
        $this->template->view('transfer/search_result_page');
    }

    /**
     * Get Data For Fare Calendar
     * @param string $booking_source
     */
    function puls_minus_days_fare_list($booking_source) {
        $response['data'] = array();
        $response['status'] = FAILURE_STATUS;

        $params = $this->input->get();
        load_flight_lib($booking_source);
        $search_data = $this->flight_lib->search_data(intval($params['search_id']));
        if ($search_data['status'] == SUCCESS_STATUS) {
            $date_array = array();
            $departure_date = $search_data['data']['depature'];
            $departure_date = strtotime(subtract_days_from_date(3, $departure_date));
            if (time() >= $departure_date) {
                $date_array[] = date('Y-m-d', strtotime(add_days_to_date(1)));
            } else {
                $date_array[] = date('Y-m-d', $departure_date);
            }
            $date_array[] = date('Y-m', strtotime($departure_date[0] . ' +1 month')) . '-1';
            //Get Current Month And Next Month
            $day_fare_list = array();
            foreach ($date_array as $k => $v) {
                $search_data['data']['depature'] = $v;
                $search = $this->flight_lib->calendar_safe_search_data($search_data['data']);
                if (valid_array($search) == true) {
                    switch ($booking_source) {
                        case PROVAB_FLIGHT_BOOKING_SOURCE :
                            $raw_fare_list = $this->flight_lib->get_fare_list($search);
                            if ($raw_fare_list['status']) {
                                $fare_calendar_list = $this->flight_lib->format_cheap_fare_list($raw_fare_list['data']);
                                if ($fare_calendar_list['status'] == SUCCESS_STATUS) {
                                    $response['data']['departure'] = $search['depature'];
                                    $calendar_events = $this->get_fare_calendar_events($fare_calendar_list['data'], $raw_fare_list['data']['TraceId']);
                                    $day_fare_list = array_merge($day_fare_list, $calendar_events);
                                    $response['status'] = SUCCESS_STATUS;
                                } else {
                                    $response['msg'] = 'Not Available!!! Please Try Later!!!!';
                                }
                            }
                            break;
                    }
                }
            }
            $response['data']['day_fare_list'] = $day_fare_list;
        }
        $this->output_compressed_data($response);
    }

    /**
     * get fare list for calendar search - FLIGHT
     */
    function fare_list($booking_source) {
        $response['data'] = '';
        $response['msg'] = '';
        $response['status'] = FAILURE_STATUS;
        $search_params = $this->input->get();
     //   debug($search_params); exit;
        load_flight_lib($booking_source);
        $search_params = $this->flight_lib->calendar_safe_search_data($search_params);
        if (valid_array($search_params) == true) {
            switch ($booking_source) {
                case PROVAB_FLIGHT_BOOKING_SOURCE :
                    $raw_fare_list = $this->flight_lib->get_fare_list($search_params);
                    if ($raw_fare_list['status']) {
                        $fare_calendar_list = $this->flight_lib->format_cheap_fare_list($raw_fare_list['data']);
                        if ($fare_calendar_list['status'] == SUCCESS_STATUS) {
                            $response['data']['departure'] = $search_params['depature'];
                            $calendar_events = $this->get_fare_calendar_events($fare_calendar_list['data'], $raw_fare_list['data']['TraceId']);
                            $response['data']['day_fare_list'] = $calendar_events;
                            $response['status'] = SUCCESS_STATUS;
                        } else {
                            $response['msg'] = 'Not Available!!! Please Try Later!!!!';
                        }
                    }
                    break;
            }
        }
        $this->output_compressed_data($response);
    }

    /**
     * Calendar Event Object
     * @param $title
     * @param $start
     * @param $tip
     * @param $href
     * @param $event_date
     * @param $session_id
     * @param $add_class
     */
    private function get_calendar_event_obj($title = '', $start = '', $tip = '', $add_class = '', $href = '', $event_date = '', $session_id = '', $data_id = '') {
        $event_obj = array();
        if (empty($data_id) == false) {
            $event_obj['data_id'] = $data_id;
        } else {
            $event_obj['data_id'] = '';
        }

        if (empty($title) == false) {
            $event_obj['title'] = $title;
        } else {
            $event_obj['title'] = '';
        }
        //start
        if (empty($start) == false) {
            $event_obj['start'] = $start;
            $event_obj['start_label'] = date('M d', strtotime($start));
        } else {
            $event_obj['start'] = '';
        }
        //tip
        if (empty($tip) == false) {
            $event_obj['tip'] = $tip;
        } else {
            $event_obj['tip'] = '';
        }
        //href
        if (empty($href) == false) {
            $event_obj['href'] = $href;
        } else {
            $event_obj['href'] = '';
        }
        //event_date
        if (empty($event_date) == false) {
            $event_obj['event_date'] = $event_date;
        }
        //session_id
        if (empty($session_id) == false) {
            $event_obj['session_id'] = $session_id;
        }
        //add_class
        if (empty($add_class) == false) {
            $event_obj['add_class'] = $add_class;
        } else {
            $event_obj['add_class'] = '';
        }
        return $event_obj;
    }

    function day_fare_list($booking_source) {
        $response['data'] = '';
        $response['msg'] = '';
        $response['status'] = FAILURE_STATUS;
        $search_params = $this->input->get();
        load_flight_lib($booking_source);
        $safe_search_params = $this->flight_lib->calendar_day_fare_safe_search_data($search_params);
        if ($safe_search_params['status'] == SUCCESS_STATUS) {
            switch ($booking_source) {
                case PROVAB_FLIGHT_BOOKING_SOURCE :
                    $raw_day_fare_list = $this->flight_lib->get_day_fare($search_params);
                    if ($raw_day_fare_list['status']) {
                        $fare_calendar_list = $this->flight_lib->format_day_fare_list($raw_day_fare_list['data']);
                        if ($fare_calendar_list['status'] == SUCCESS_STATUS) {
                            $calendar_events = $this->get_fare_calendar_events($fare_calendar_list['data'], '');
                            $response['data']['day_fare_list'] = $calendar_events;
                            $response['data']['departure'] = $search_params['depature'];
                            $response['status'] = SUCCESS_STATUS;
                        } else {
                            $response['msg'] = 'Not Available!!! Please Try Later!!!!';
                        }
                    }
                    break;
            }
        }
        $this->output_compressed_data($response);
    }

    private function get_fare_calendar_events($events, $session_id = '') {
        $currency_obj = new Currency(array('module_type' => 'flight', 'from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
        $index = 0;
        $calendar_events = array();
        foreach ($events as $k => $day_fare) {
            if (valid_array($day_fare) == true) {
                $fare_object = array('BaseFare' => $day_fare['BaseFare']);
                $BaseFare = $this->flight_lib->update_markup_currency($fare_object, $currency_obj);
                $tax = $currency_obj->get_currency($day_fare['tax'], false);
                $day_fare['price'] = floor($BaseFare['BaseFare'] + $tax['default_value']);
                $event_obj = $this->get_calendar_event_obj(
                        $currency_obj->get_currency_symbol(get_application_currency_preference()) . ' ' . $day_fare['price'], $k, $day_fare['airline_name'] . '-' . $day_fare['airline_code'], 'search-day-fare', '', $day_fare['departure'], '', $day_fare['airline_code']);
                $calendar_events[$index] = $event_obj;
            } else {
                $event_obj = $this->get_calendar_event_obj('Update', $k, 'Current Cheapest Fare Not Available. Click To Get Latest Fare.', 'update-day-fare', '', $k, $session_id, '');
                $calendar_events[$index] = $event_obj;
            }
            $index++;
        }
        return $calendar_events;
    }

    /**
     * Get Fare Details
     */
    function get_fare_details() {
        $response['status'] = false;
        $response['data'] = '';
        $response['msg'] = '<i class="fa fa-warning text-danger"></i> Fare Details Not Available';
        $params = $this->input->post();

        load_flight_lib($params['booking_source']);
        $data_access_key = $params['data_access_key'];
        $params['data_access_key'] = unserialized_data($params['data_access_key']);
        if (empty($params['data_access_key']) == false) {
            switch ($params['booking_source']) {
                case PROVAB_FLIGHT_BOOKING_SOURCE :
                    $params['data_access_key'] = $this->flight_lib->read_token($data_access_key);
                    $data = $this->flight_lib->get_fare_details($params['data_access_key'], $params['search_access_key']);
                    if ($data['status'] == SUCCESS_STATUS) {
                        $response['status'] = SUCCESS_STATUS;
                        $response['data'] = $this->template->isolated_view('flight/tbo/fare_details', array('fare_rules' => $data['data']));
                        $response['msg'] = 'Fare Details Available';
                    }
            }
        }
        $this->output_compressed_data($response);
    }

    function get_combined_booking_from() {
        $response['status'] = FAILURE_STATUS;
        $response['data'] = array();
        $params = $this->input->post();
        if (empty($params['search_id']) == false && empty($params['trip_way_1']) == false && empty($params['trip_way_2']) == false) {
            $tmp_trip_way_1 = json_decode($params['trip_way_1'], true);
            $tmp_trip_way_2 = json_decode($params['trip_way_2'], true);
            $search_id = $params['search_id'];
            foreach ($tmp_trip_way_1 as $___v) {
                $trip_way_1[$___v['name']] = $___v['value'];
            }
            foreach ($tmp_trip_way_2 as $___v) {
                $trip_way_2[$___v['name']] = $___v['value'];
            }
            $booking_source = $trip_way_1['booking_source'];
            switch ($booking_source) {
                case PROVAB_FLIGHT_BOOKING_SOURCE : load_flight_lib(PROVAB_FLIGHT_BOOKING_SOURCE);
                    $response['data']['booking_url'] = $this->flight_lib->booking_url(intval($params['search_id']));
                    $response['data']['form_content'] = $this->flight_lib->get_form_content($trip_way_1, $trip_way_2);
                    $response['status'] = SUCCESS_STATUS;
                break;
                case STAR_BOOKING_SOURCE : load_flight_lib(STAR_BOOKING_SOURCE);
                    $response['data']['booking_url'] = $this->flight_lib->booking_url(intval($params['search_id']));
                    $response['data']['form_content'] = $this->flight_lib->get_form_content($trip_way_1, $trip_way_2);
                    $response['status'] = SUCCESS_STATUS;
                break;
                case SPICEJET_BOOKING_SOURCE : load_flight_lib(SPICEJET_BOOKING_SOURCE);
                    $response['data']['booking_url'] = $this->flight_lib->booking_url(intval($params['search_id']));
                    $response['data']['form_content'] = $this->flight_lib->get_form_content($trip_way_1, $trip_way_2);
                    $response['status'] = SUCCESS_STATUS;
                break;
                case TRAVELPORT_GDS_BOOKING_SOURCE : load_flight_lib(TRAVELPORT_GDS_BOOKING_SOURCE);
                    $response['data']['booking_url'] = $this->flight_lib->booking_url(intval($params['search_id']));
                    $response['data']['form_content'] = $this->flight_lib->get_form_content($trip_way_1, $trip_way_2);
                    $response['status'] = SUCCESS_STATUS;
                break;
                case TRAVELPORT_ACH_BOOKING_SOURCE : load_flight_lib(TRAVELPORT_ACH_BOOKING_SOURCE);
                    $response['data']['booking_url'] = $this->flight_lib->booking_url(intval($params['search_id']));
                    $response['data']['form_content'] = $this->flight_lib->get_form_content($trip_way_1, $trip_way_2);
                    $response['status'] = SUCCESS_STATUS;
                break;
            }
        }
        $this->output_compressed_data($response);
    }

    /**
     * Balu A
     * Get Traveller Details in Booking Page
     */
    function user_traveller_details() {
        $term = $this->input->get('term'); //retrieve the search term that autocomplete sends
        $term = trim($term);
        $result = array();
        $this->load->model('user_model');
        $traveller_details = $this->user_model->user_traveller_details($term)->result();
        $travllers_data = array();
        foreach ($traveller_details as $traveller) {
            $travllers_data['category'] = 'Travellers';
            $travllers_data['id'] = $traveller->origin;
            $travllers_data['label'] = trim($traveller->first_name . ' ' . $traveller->last_name);
            $travllers_data['value'] = trim($traveller->first_name);
            $travllers_data['first_name'] = trim($traveller->first_name);
            $travllers_data['last_name'] = trim($traveller->last_name);
            $travllers_data['date_of_birth'] = date('Y-m-d', strtotime(trim($traveller->date_of_birth)));
            $travllers_data['email'] = trim($traveller->email);
            $travllers_data['passport_user_name'] = trim($traveller->passport_user_name);
            $travllers_data['passport_nationality'] = trim($traveller->passport_nationality);
            $travllers_data['passport_expiry_day'] = trim($traveller->passport_expiry_day);
            $travllers_data['passport_expiry_month'] = trim($traveller->passport_expiry_month);
            $travllers_data['passport_expiry_year'] = trim($traveller->passport_expiry_year);
            $travllers_data['passport_number'] = trim($traveller->passport_number);
            $travllers_data['passport_issuing_country'] = trim($traveller->passport_issuing_country);
            array_push($result, $travllers_data);
        }
        $this->output_compressed_data($result);
    }

    /**
     *
     */
    function log_event_ip_info($eid) {
        $params = $this->input->post();
        if (empty($eid) == false) {
            $this->custom_db->update_record('exception_logger', array('client_info' => serialize($params)), array('exception_id' => $eid));
        }
    }

    /**
     * Load hotels from different source
     */
    function hotel_image_list_desc($offset = 0) {
        $response['data'] = '';
        $response['msg'] = '';
        $response['status'] = FAILURE_STATUS;

        if (PROVAB_HOTEL_BOOKING_SOURCE) {
            load_hotel_lib(PROVAB_HOTEL_BOOKING_SOURCE);
            switch (PROVAB_HOTEL_BOOKING_SOURCE) {
                case PROVAB_HOTEL_BOOKING_SOURCE :
                    //getting alll_api_city_master
                    $api_city_master = $this->custom_db->single_table_records('all_api_city_master', '*', array('status' => 0, 'priority' => 1), 0, 1, array('origin' => 'desc'));
                    debug($api_city_master);
                    exit;
                    if ($api_city_master['status'] == 1) {


                        $api_city_master = $api_city_master['data'][0];
                        $city_id = $api_city_master['grn_city_id'];
                        $country_code = $api_city_master['country_code'];
                        $destination_code = $api_city_master['grn_destination_id'];
                        //Meaning hotels are loaded first time
                        $raw_hotel_list = $this->hotel_lib->get_hotel_image_list($city_id, $country_code);

                        $update_condition = array();
                        if ($raw_hotel_list['status'] == true) {
                            $raw_hotel_list = $raw_hotel_list['data'];
                            $update_condition['status'] = 1;

                            foreach ($raw_hotel_list as $key => $value) {
                                if ($value['images']) {
                                    if ($value['images']['main_image']) {

                                        $image = 'https://cdn.grnconnect.com/' . $value['images']['main_image'];
                                        $path_info = pathinfo($value['images']['main_image']);
                                        $path_info_base_name = $path_info['basename'];
                                        $insert_data['hotel_code'] = $value['hotel_code'];
                                        $insert_data['city_code'] = $value['city_code'];
                                        $insert_data['destination_code'] = $destination_code;
                                        $insert_data['country_code'] = $value['country'];
                                        $image = file_get_contents($image);
                                        $hotel_code = preg_replace("/[^a-zA-Z0-9]/", "", $value['hotel_code']);
                                        $image_file_name = $hotel_code . time() . $path_info_base_name;
                                        $image_folder_path = './cdn/images/' . $image_file_name;
                                        file_put_contents($image_folder_path, $image); //Where to save the image on your server
                                        $insert_data['path_name'] = $image_file_name;
                                        $check_if_exists = $this->custom_db->single_table_records('api_grn_image_main_image_path', '*', array('hotel_code' => $insert_data['hotel_code'], 'city_code' => $insert_data['city_code'], 'country_code' => $insert_data['country_code']));
                                        if ($check_if_exists['status'] == 1) {
                                            $update_data['path_name'] = $image_file_name;
                                            $condition['hotel_code'] = $insert_data['hotel_code'];
                                            $condition['city_code'] = $insert_data['CITY_CODE'];
                                            $this->custom_db->update_record('api_grn_image_main_image_path', $update_data, $condition);
                                        } else {
                                            $this->custom_db->insert_record('api_grn_image_main_image_path', $insert_data);
                                        }
                                    }
                                }
                            }
                        } else {
                            $update_condition['status'] = 1;
                            $update_condition['error'] = json_encode($raw_hotel_list);
                        }

                        //update in all_api_city_master
                        $this->custom_db->update_record('all_api_city_master', $update_condition, array('grn_city_id' => $city_id, 'country_code' => $country_code, 'grn_destination_id' => $destination_code));
                    }
                    echo "successs";
            }
        }
    }

    function download_api_grn_exterior_image_part1() {
        error_reporting(0);
        //$static_image = $this->custom_db->single_table_records('api_grn_static_images','*',array('status'=>0),0,10,array('origin'=>'desc'));
        $str = "select * from api_grn_static_images where status=0 and origin <=200000  limit 0,10";
        $execute_query = $this->db->query($str);
        $static_image = [];
        if ($execute_query->num_rows()) {
            $static_image = $execute_query->result_array();
        }
        //debug($static_image);exit;
        if ($static_image) {
            foreach ($static_image as $key => $value) {
                $image = $value['image_url'];
                $path_info = pathinfo($image);
                $path_info_base_name = $path_info['basename'];
                if (file_get_contents($image)) {
                    $image = file_get_contents($image);
                    $hotel_code = preg_replace("/[^a-zA-Z0-9]/", "", $value['hotel_code']);
                    $image_file_name = $hotel_code . time() . $path_info_base_name;
                    $image_folder_path = './cdn/grn/images/' . $image_file_name;
                    file_put_contents($image_folder_path, $image); //Where to save the image on your server

                    $insert_data['path_name'] = $image_file_name;
                    $insert_data['hotel_code'] = $value['hotel_code'];
                    $check_if_exists = $this->custom_db->single_table_records('api_grn_master_image', '*', array('hotel_code' => $insert_data['hotel_code']));
                    if ($check_if_exists['status'] == 1) {
                        $update_data['path_name'] = $image_file_name;
                        $condition['hotel_code'] = $insert_data['hotel_code'];
                        //if exists unlink the file
                        $file_name = $check_if_exists['data'][0][''];
                        $this->custom_db->update_record('api_grn_master_image', $update_data, $condition);
                    } else {
                        $this->custom_db->insert_record('api_grn_master_image', $insert_data);
                    }
                } else {
                    $image_update_data['image_found'] = 'N';
                }
                $image_update_data['status'] = 1;
                $update_condition['origin'] = $value['origin'];
                //update in all_api_city_master
                $this->custom_db->update_record('api_grn_static_images', $image_update_data, $update_condition);
            }
        }
        echo "successs";
    }

    function download_api_grn_exterior_image_part2() {
        error_reporting(0);
        //$static_image = $this->custom_db->single_table_records('api_grn_static_images','*',array('status'=>0),0,10,array('origin'=>'desc'));
        $str = "select * from api_grn_static_images where status=0 and origin  >200000 limit 0,10";
        $execute_query = $this->db->query($str);
        $static_image = [];
        if ($execute_query->num_rows()) {
            $static_image = $execute_query->result_array();
        }
        if ($static_image) {
            foreach ($static_image as $key => $value) {
                $image = $value['image_url'];
                $path_info = pathinfo($image);
                $path_info_base_name = $path_info['basename'];
                if (file_get_contents($image)) {
                    $image = file_get_contents($image);
                    $hotel_code = preg_replace("/[^a-zA-Z0-9]/", "", $value['hotel_code']);
                    $image_file_name = $hotel_code . time() . $path_info_base_name;
                    $image_folder_path = './cdn/grn/images/' . $image_file_name;
                    file_put_contents($image_folder_path, $image); //Where to save the image on your server

                    $insert_data['path_name'] = $image_file_name;
                    $insert_data['hotel_code'] = $value['hotel_code'];
                    $check_if_exists = $this->custom_db->single_table_records('api_grn_master_image', '*', array('hotel_code' => $insert_data['hotel_code']));
                    if ($check_if_exists['status'] == 1) {
                        $update_data['path_name'] = $image_file_name;
                        $condition['hotel_code'] = $insert_data['hotel_code'];
                        $this->custom_db->update_record('api_grn_master_image', $update_data, $condition);
                    } else {
                        $this->custom_db->insert_record('api_grn_master_image', $insert_data);
                    }
                } else {
                    $image_update_data['image_found'] = 'N';
                }
                $image_update_data['status'] = 1;
                $update_condition['origin'] = $value['origin'];
                //update in all_api_city_master
                $this->custom_db->update_record('api_grn_static_images', $image_update_data, $update_condition);
            }
        }
        echo "successs";
    }

    function download_api_grn_exterior_image_part3() {
        error_reporting(0);
        //$static_image = $this->custom_db->single_table_records('api_grn_static_images','*',array('status'=>0),0,10,array('origin'=>'desc'));
        $str = "select * from api_grn_static_images where status=0 and origin >400000 limit 0,10";
        $execute_query = $this->db->query($str);
        $static_image = [];
        if ($execute_query->num_rows()) {
            $static_image = $execute_query->result_array();
        }
        if ($static_image) {
            foreach ($static_image as $key => $value) {
                $image = $value['image_url'];
                $path_info = pathinfo($image);
                $path_info_base_name = $path_info['basename'];
                if (file_get_contents($image)) {
                    $image = file_get_contents($image);
                    $hotel_code = preg_replace("/[^a-zA-Z0-9]/", "", $value['hotel_code']);
                    $image_file_name = $hotel_code . time() . $path_info_base_name;
                    $image_folder_path = './cdn/grn/images/' . $image_file_name;
                    file_put_contents($image_folder_path, $image); //Where to save the image on your server

                    $insert_data['path_name'] = $image_file_name;
                    $insert_data['hotel_code'] = $value['hotel_code'];
                    $check_if_exists = $this->custom_db->single_table_records('api_grn_master_image', '*', array('hotel_code' => $insert_data['hotel_code']));
                    if ($check_if_exists['status'] == 1) {
                        $update_data['path_name'] = $image_file_name;
                        $condition['hotel_code'] = $insert_data['hotel_code'];
                        $this->custom_db->update_record('api_grn_master_image', $update_data, $condition);
                    } else {
                        $this->custom_db->insert_record('api_grn_master_image', $insert_data);
                    }
                } else {
                    $image_update_data['image_found'] = 'N';
                }
                $image_update_data['status'] = 1;
                $update_condition['origin'] = $value['origin'];
                //update in all_api_city_master
                $this->custom_db->update_record('api_grn_static_images', $image_update_data, $update_condition);
            }
        }
        echo "successs";
    }

    function download_api_grn_exterior_image_part4() {
        error_reporting(0);
        //$static_image = $this->custom_db->single_table_records('api_grn_static_images','*',array('status'=>0),0,10,array('origin'=>'desc'));
        $str = "select * from api_grn_static_images where status=0 and origin >=600000 limit 0,10";
        $execute_query = $this->db->query($str);
        $static_image = [];
        if ($execute_query->num_rows()) {
            $static_image = $execute_query->result_array();
        }
        if ($static_image) {
            foreach ($static_image as $key => $value) {
                $image = $value['image_url'];
                $path_info = pathinfo($image);
                $path_info_base_name = $path_info['basename'];
                if (file_get_contents($image)) {
                    $image = file_get_contents($image);
                    $hotel_code = preg_replace("/[^a-zA-Z0-9]/", "", $value['hotel_code']);
                    $image_file_name = $hotel_code . time() . $path_info_base_name;
                    $image_folder_path = './cdn/grn/images/' . $image_file_name;
                    file_put_contents($image_folder_path, $image); //Where to save the image on your server

                    $insert_data['path_name'] = $image_file_name;
                    $insert_data['hotel_code'] = $value['hotel_code'];
                    $check_if_exists = $this->custom_db->single_table_records('api_grn_master_image', '*', array('hotel_code' => $insert_data['hotel_code']));
                    if ($check_if_exists['status'] == 1) {
                        $update_data['path_name'] = $image_file_name;
                        $condition['hotel_code'] = $insert_data['hotel_code'];
                        $this->custom_db->update_record('api_grn_master_image', $update_data, $condition);
                    } else {
                        $this->custom_db->insert_record('api_grn_master_image', $insert_data);
                    }
                } else {
                    $image_update_data['image_found'] = 'N';
                }
                $image_update_data['status'] = 1;
                $update_condition['origin'] = $value['origin'];
                //update in all_api_city_master
                $this->custom_db->update_record('api_grn_static_images', $image_update_data, $update_condition);
            }
        }
        echo "successs";
    }

    function download_api_grn_exterior_image_part5() {
        error_reporting(0);
        //$static_image = $this->custom_db->single_table_records('api_grn_static_images','*',array('status'=>0),0,10,array('origin'=>'desc'));
        $str = "select * from api_grn_static_images where status=0 and origin >=800000 limit 0,10";
        $execute_query = $this->db->query($str);
        $static_image = [];
        if ($execute_query->num_rows()) {
            $static_image = $execute_query->result_array();
        }
        if ($static_image) {
            foreach ($static_image as $key => $value) {
                $image = $value['image_url'];
                $path_info = pathinfo($image);
                $path_info_base_name = $path_info['basename'];
                if (file_get_contents($image)) {
                    $image = file_get_contents($image);
                    $hotel_code = preg_replace("/[^a-zA-Z0-9]/", "", $value['hotel_code']);
                    $image_file_name = $hotel_code . time() . $path_info_base_name;
                    $image_folder_path = './cdn/grn/images/' . $image_file_name;
                    file_put_contents($image_folder_path, $image); //Where to save the image on your server

                    $insert_data['path_name'] = $image_file_name;
                    $insert_data['hotel_code'] = $value['hotel_code'];
                    $check_if_exists = $this->custom_db->single_table_records('api_grn_master_image', '*', array('hotel_code' => $insert_data['hotel_code']));
                    if ($check_if_exists['status'] == 1) {
                        $update_data['path_name'] = $image_file_name;
                        $condition['hotel_code'] = $insert_data['hotel_code'];
                        $this->custom_db->update_record('api_grn_master_image', $update_data, $condition);
                    } else {
                        $this->custom_db->insert_record('api_grn_master_image', $insert_data);
                    }
                } else {
                    $image_update_data['image_found'] = 'N';
                }
                $image_update_data['status'] = 1;
                $update_condition['origin'] = $value['origin'];
                //update in all_api_city_master
                $this->custom_db->update_record('api_grn_static_images', $image_update_data, $update_condition);
            }
        }
        echo "successs";
    }

    function download_api_grn_exterior_image_part6() {
        error_reporting(0);
        //$static_image = $this->custom_db->single_table_records('api_grn_static_images','*',array('status'=>0),0,10,array('origin'=>'desc'));
        $str = "select * from api_grn_static_images where status=0 and origin >=950000 limit 0,10";
        $execute_query = $this->db->query($str);
        $static_image = [];
        if ($execute_query->num_rows()) {
            $static_image = $execute_query->result_array();
        }
        if ($static_image) {
            foreach ($static_image as $key => $value) {
                $image = $value['image_url'];
                $path_info = pathinfo($image);
                $path_info_base_name = $path_info['basename'];
                if (file_get_contents($image)) {
                    $image = file_get_contents($image);
                    $hotel_code = preg_replace("/[^a-zA-Z0-9]/", "", $value['hotel_code']);
                    $image_file_name = $hotel_code . time() . $path_info_base_name;
                    $image_folder_path = './cdn/grn/images/' . $image_file_name;
                    file_put_contents($image_folder_path, $image); //Where to save the image on your server

                    $insert_data['path_name'] = $image_file_name;
                    $insert_data['hotel_code'] = $value['hotel_code'];
                    $check_if_exists = $this->custom_db->single_table_records('api_grn_master_image', '*', array('hotel_code' => $insert_data['hotel_code']));
                    if ($check_if_exists['status'] == 1) {
                        $update_data['path_name'] = $image_file_name;
                        $condition['hotel_code'] = $insert_data['hotel_code'];
                        $this->custom_db->update_record('api_grn_master_image', $update_data, $condition);
                    } else {
                        $this->custom_db->insert_record('api_grn_master_image', $insert_data);
                    }
                } else {
                    $image_update_data['image_found'] = 'N';
                }
                $image_update_data['status'] = 1;
                $update_condition['origin'] = $value['origin'];
                //update in all_api_city_master
                $this->custom_db->update_record('api_grn_static_images', $image_update_data, $update_condition);
            }
        }
        echo "successs";
    }

    function get_country_master() {
        $country_master = $this->custom_db->single_table_records('api_country_master', '*', array('status' => 0), 0, 30);
        if ($country_master['status'] == 1) {
            foreach ($country_master['data'] as $key => $i_value) {
                $city_master_data = [];
                $city_master_data = $this->process_request($i_value['iso_country_code']);

                if ($city_master_data) {
                    if (isset($city_master_data['cities'])) {
                        foreach ($city_master_data['cities'] as $c_value) {

                            $insert_data['grn_city_id'] = $c_value['code'];
                            $explode = explode(",", $c_value['name']);

                            $insert_data['city_name'] = $explode[0];
                            $insert_data['full_city_name'] = $c_value['name'];
                            $insert_data['country_name'] = $i_value['country_name'];
                            $insert_data['country_code'] = $i_value['iso_country_code'];

                            $check_if_data_exists = $this->custom_db->single_table_records('all_api_city_master_update', 'grn_city_id', array('grn_city_id' => $insert_data['grn_city_id'], 'country_code' => $i_value['iso_country_code']), 0, 1);

                            if ($check_if_data_exists['status'] == 1) {
                                $city_code = $check_if_data_exists['data'][0]['grn_city_id'];

                                if ($insert_data['grn_city_id'] != $city_code) {

                                    $update_city_data['grn_city_id'] = $c_value['code'];
                                    $this->custom_db->update_record('all_api_city_master_update', $update_city_data, array('grn_city_id' => $insert_data['grn_city_id'], 'country_code' => $insert_data['country_code']));
                                } else {

                                    $this->custom_db->insert_record('grn_api_new_city_list', $insert_data);
                                }
                            } else {
                                $this->custom_db->insert_record('all_api_city_master_update', $insert_data);
                            }
                        }

                        $update_date['status'] = 1;
                        $update_date['city_count'] = $city_master_data['total'];
                        $update_date['error'] = json_encode($city_master_data['cities']);
                        $this->custom_db->update_record('api_country_master', $update_date, array('iso_country_code' => $i_value['iso_country_code']));
                    } else {
                        $update_date['status'] = 1;
                        $update_date['city_count'] = 0;
                        $update_date['error'] = json_encode($city_master_data);
                        $this->custom_db->update_record('api_country_master', $update_date, array('iso_country_code' => $i_value['iso_country_code']));
                    }
                }
            }
            echo "successs";
        }
    }

    function get_country_master_desc() {
        $country_master = $this->custom_db->single_table_records('api_country_master', '*', array('status' => 0), 0, 30, array('origin' => 'desc'));
        if ($country_master['status'] == 1) {
            foreach ($country_master['data'] as $key => $i_value) {
                $city_master_data = [];
                $city_master_data = $this->process_request($i_value['iso_country_code']);

                if ($city_master_data) {
                    if (isset($city_master_data['cities'])) {
                        foreach ($city_master_data['cities'] as $c_value) {

                            $insert_data['grn_city_id'] = $c_value['code'];
                            $explode = explode(",", $c_value['name']);

                            $insert_data['city_name'] = $explode[0];
                            $insert_data['full_city_name'] = $c_value['name'];
                            $insert_data['country_name'] = $i_value['country_name'];
                            $insert_data['country_code'] = $i_value['iso_country_code'];

                            $check_if_data_exists = $this->custom_db->single_table_records('all_api_city_master_update', 'grn_city_id', array('grn_city_id' => $insert_data['grn_city_id'], 'country_code' => $i_value['iso_country_code']), 0, 1);

                            if ($check_if_data_exists['status'] == 1) {
                                $city_code = $check_if_data_exists['data'][0]['grn_city_id'];

                                if ($insert_data['grn_city_id'] != $city_code) {

                                    $update_city_data['grn_city_id'] = $c_value['code'];
                                    $this->custom_db->update_record('all_api_city_master_update', $update_city_data, array('grn_city_id' => $insert_data['grn_city_id'], 'country_code' => $insert_data['country_code']));
                                } else {

                                    $this->custom_db->insert_record('grn_api_new_city_list', $insert_data);
                                }
                            } else {
                                $this->custom_db->insert_record('all_api_city_master_update', $insert_data);
                            }
                        }

                        $update_date['status'] = 1;
                        $update_date['city_count'] = $city_master_data['total'];
                        $update_date['error'] = json_encode($city_master_data['cities']);
                        $this->custom_db->update_record('api_country_master', $update_date, array('iso_country_code' => $i_value['iso_country_code']));
                    } else {
                        $update_date['status'] = 1;
                        $update_date['city_count'] = 0;
                        $update_date['error'] = json_encode($city_master_data);
                        $this->custom_db->update_record('api_country_master', $update_date, array('iso_country_code' => $i_value['iso_country_code']));
                    }
                }
            }
            echo "successs";
        }
    }

    function check_grn_image_found() {
        error_reporting(0);
        //$static_image = $this->custom_db->single_table_records('api_grn_static_images','*',array('status'=>0),0,10,array('origin'=>'desc'));
        $str = "select * from api_grn_static_images where status=0 and origin <=200000   limit 0,50";
        $execute_query = $this->db->query($str);
        $static_image = [];
        if ($execute_query->num_rows()) {
            $static_image = $execute_query->result_array();
        }
        //debug($static_image);exit;
        if ($static_image) {
            foreach ($static_image as $key => $value) {
                $image = $value['image_url'];
                $path_info = pathinfo($image);
                $path_info_base_name = $path_info['basename'];
                if (file_get_contents($image)) {
                    $image_update_data['image_found'] = 'Y';
                } else {
                    $image_update_data['image_found'] = 'N';
                }
                $image_update_data['status'] = 1;
                $update_condition['origin'] = $value['origin'];
                //update in all_api_city_master
                $this->custom_db->update_record('api_grn_static_images', $image_update_data, $update_condition);
            }
        }
        echo "successs";
    }

    function update_json_data() {
        $path = FCPATH . '/hb_static/groupcategories/groupcategories_1.json';
        if (file_exists($path)) {
            $details = json_decode(file_get_contents($path), TRUE);

            $hotelArray = $details['groupCategories'];
            foreach ($hotelArray as $key => $value) {
                $insert_data['code'] = $value['code'];
                $insert_data['group_order'] = $value['order'];
                $insert_data['name'] = $value['name']['content'];
                $insert_data['description'] = $value['description']['content'];
                $this->custom_db->insert_record('hb_group_categories', $insert_data);
            }
        } else {
            
        }
        exit;
    }

    function process_request($feed_id, $city_id = '', $country_id = '', $hotel_id = '', $area_id = '', $brand_id = '') {

        if ($feed_id == 1) {//region
            $url = 'http://xml.agoda.com/datafeeds/Feed.asmx/GetFeed?feed_id=1&apikey=10b13bb6-67a2-4311-9d6b-62420157e394';
        } elseif ($feed_id == 2) {//country
            $url = 'http://xml.agoda.com/datafeeds/Feed.asmx/GetFeed?feed_id=2&apikey=10b13bb6-67a2-4311-9d6b-62420157e394';
        } elseif ($feed_id == 3) {//city
            $url = 'http://xml.agoda.com/datafeeds/Feed.asmx/GetFeed?feed_id=3&apikey=10b13bb6-67a2-4311-9d6b-62420157e394&ocountry_id=' . $country_id;
        } elseif ($feed_id == 4) {//area
            $url = 'xml.agoda.com/datafeeds/Feed.asmx/GetFeed?feed_id=4&apikey=10b13bb6-67a2-4311-9d6b-62420157e394';
        } elseif ($feed_id == 5) {//hotel
            $url = 'http://xml.agoda.com/datafeeds/Feed.asmx/GetFeed?feed_id=5&apikey=10b13bb6-67a2-4311-9d6b-62420157e394&mCity_id=' . $city_id . '&olanguage_id=1&ocurrency=INR';
        } elseif ($feed_id == 6) {//room type
            $url = 'http://xml.agoda.com/datafeeds/Feed.asmx/GetFeed?feed_id=6&apikey=10b13bb6-67a2-4311-9d6b-62420157e394&mHotel_id=' . $hotel_id;
        } elseif ($feed_id == 7) {//hotel picture
            $url = 'http://xml.agoda.com/datafeeds/Feed.asmx/GetFeed?feed_id=7&apikey=10b13bb6-67a2-4311-9d6b-62420157e394&mhotel_id=' . $hotel_id;
        } elseif ($feed_id == 9) {//facility
            $url = 'http://xml.agoda.com/datafeeds/Feed.asmx/GetFeed?feed_id=9&apikey=10b13bb6-67a2-4311-9d6b-62420157e394&mhotel_id=' . $hotel_id;
        } elseif ($feed_id == 10) { //hotel other info
            $url = 'http://xml.agoda.com/datafeeds/Feed.asmx/GetFeed?feed_id=10&apikey=10b13bb6-67a2-4311-9d6b-62420157e394&mhotel_id=' . $hotel_id;
        } elseif ($feed_id == 11) { //cahnged hotel
            $url = 'http://xml.agoda.com/datafeeds/Feed.asmx/GetFeed?feed_id=11&apikey=10b13bb6-67a2-4311-9d6b-62420157e394&mSinceDate=20111101';
        } elseif ($feed_id == 13) { //lang
            $url = 'http://xml.agoda.com/datafeeds/Feed.asmx/GetFeed?feed_id=13&apikey=10b13bb6-67a2-4311-9d6b-62420157e394';
        } elseif ($feed_id == 14) { // facilities per roomtype per hotel
            $url = 'http://xml.agoda.com/datafeeds/Feed.asmx/GetFeed?feed_id=14&apikey=10b13bb6-67a2-4311-9d6b-62420157e394&mhotel_id=' . $hotel_id;
        } elseif ($feed_id == 15) { //hotel chains
            $url = 'http://xml.agoda.com/datafeeds/Feed.asmx/GetFeed?feed_id=15&apikey=10b13bb6-67a2-4311-9d6b-62420157e394';
        } elseif ($feed_id == 16) {//hotels per hotel brand
            $url = 'http://xml.agoda.com/datafeeds/Feed.asmx/GetFeed?feed_id=16&apikey=10b13bb6-67a2-4311-9d6b-62420157e394&mbrand_id=' . $brand_id;
        } elseif ($feed_id == 17) {//hotel description
            $url = 'xml.agoda.com/datafeeds/Feed.asmx/GetFeed?feed_id=17&apikey=10b13bb6-67a2-4311-9d6b-62420157e394&mcity_id=' . $city_id . '&ohotel_id=' . $hotel_id;
        } elseif ($feed_id == 18) { //hotel address
            $url = 'http://xml.agoda.com/datafeeds/Feed.asmx/GetFeed?feed_id=18&apikey=10b13bb6-67a2-4311-9d6b-62420157e394&mcity_id=' . $city_id . '&ohotel_id=' . $hotel_id;
        } elseif ($feed_id == 19) {//full hotel details
            $url = 'http://xml.agoda.com/datafeeds/Feed.asmx/GetFeed?feed_id=19&apikey=10b13bb6-67a2-4311-9d6b-62420157e394&mhotel_id=' . $hotel_id;
        } elseif ($feed_id == 20) { //brand
            $url = 'http://xml.agoda.com/datafeeds/Feed.asmx/GetFeed?feed_id=20&apikey=10b13bb6-67a2-4311-9d6b-62420157e394';
        } elseif ($feed_id == 21) {//states/provinces
            $url = 'http://xml.agoda.com/datafeeds/Feed.asmx/GetFeed?feed_id=21&apikey=10b13bb6-67a2-4311-9d6b-62420157e394';
        } elseif ($feed_id == 22) {//XML feed with information of benefits
            $url = 'http://xml.agoda.com/datafeeds/Feed.asmx/GetFeed?feed_id=22&apikey=10b13bb6-67a2-4311-9d6b-62420157e394';
        }
        echo $url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        $headers = curl_getinfo($ch);

        if ($headers['http_code'] != '200') {

            return false;
        } else {
            //$response = json_decode($response, true);
            $response = utf8_encode($response);

            $response = Converter::createArray($response);

            curl_close($ch);
            return $response;
        }
    }

    function download_agoda_region() {

        //$feed_id,$city_id='',$country_id='',$hotel_id='',$area_id='',$brand_id=''
        $api_data = $this->process_request(1);

        if ($api_data['Continent_feed']['continents']) {
            foreach ($api_data['Continent_feed']['continents']['continent'] as $key => $value) {

                $data['continent_id'] = $value['continent_id'];
                $data['continent_name'] = $value['continent_name'];
                $data['continent_translated'] = $value['continent_translated'];
                $data['active_hotels'] = $value['active_hotels'];
                $check_if_exists = $this->custom_db->single_table_records('api_agoda_continents', '*', array('continent_id' => $data['continent_id']), 0, 1);
                if ($check_if_exists['status'] == 0) {
                    //insert
                    $this->custom_db->insert_record('api_agoda_continents', $data);
                } else {
                    //update
                    $this->custom_db->update_record('api_agoda_continents', $data, array('continent_id' => $data['continent_id']));
                }
            }
            echo "sucess";
            exit;
        }
    }

    function download_agoda_country() {

        //$feed_id,$city_id='',$country_id='',$hotel_id='',$area_id='',$brand_id=''
        $api_data = $this->process_request(2);
        // debug($api_data);
        // echo "------";
        if ($api_data['Country_feed']['countries']) {
            foreach ($api_data['Country_feed']['countries']['country'] as $value) {
                $data = array();
                $data['country_id'] = $value['country_id'];
                $data['continent_id'] = $value['continent_id'];
                $data['country_name'] = $value['country_name'];
                $data['country_translated'] = $value['country_translated'];
                $data['active_hotels'] = $value['active_hotels'];
                $data['country_iso'] = $value['country_iso'];
                $data['country_iso2'] = $value['country_iso2'];
                $data['longitude'] = $value['longitude'];
                $data['latitude'] = $value['latitude'];

                $check_if_exists = $this->custom_db->single_table_records('api_agoda_country_list', '*', array('country_id' => $value['country_id']), 0, 1);

                if ($check_if_exists['status'] == 0) {
                    //insert
                    $this->custom_db->insert_record('api_agoda_country_list', $data);
                } else {
                    $data = array();
                    $data['continent_id'] = $value['continent_id'];
                    $data['country_name'] = $value['country_name'];
                    $data['country_translated'] = $value['country_translated'];
                    $data['active_hotels'] = $value['active_hotels'];
                    $data['country_iso'] = $value['country_iso'];
                    $data['country_iso2'] = $value['country_iso2'];
                    $data['longitude'] = $value['longitude'];
                    $data['latitude'] = $value['latitude'];
                    //update
                    $this->custom_db->update_record('api_agoda_country_list', $data, array('country_id' => $value['country_id']));
                    //echo $this->db->last_query().'<br/>';
                }
            }
            echo "sucess";
            exit;
        }
    }

    function download_agoda_city() {

        //$feed_id,$city_id='',$country_id='',$hotel_id='',$area_id='',$brand_id=''
        //get country list data
        $get_country_list = $this->custom_db->single_table_records('api_agoda_country_list', '*', array('status' => 0), 0, 1);

        if ($get_country_list['data'][0]) {
            //echo $get_country_list['data'][0]['country_id'];
            //foreach ($get_country_list['data'][0] as $key => $value) {
            $api_data = $this->process_request(3, '', $get_country_list['data'][0]['country_id']);

            $city_count = 0;
            if (isset($api_data['City_feed']['cities']['city'])) {
                if ($api_data['City_feed']['cities']['city']) {
                    $city_count = count($api_data['City_feed']['cities']['city']);
                    foreach ($api_data['City_feed']['cities']['city'] as $key => $value) {
                        $data = array();
                        $data['city_id'] = $value['city_id'];
                        $data['country_id'] = $value['country_id'];
                        $data['city_name'] = $value['city_name'];
                        $data['city_translated'] = $value['city_translated'];
                        $data['active_hotels'] = $value['active_hotels'];
                        $data['longitude'] = $value['longitude'];
                        $data['latitude'] = $value['latitude'];
                        $data['no_area'] = $value['no_area'];
                        $check_if_exists = $this->custom_db->single_table_records('api_agoda_city_list', '*', array('city_id' => $data['city_id']), 0, 1);
                        if ($check_if_exists['status'] == 0) {
                            //insert
                            $this->custom_db->insert_record('api_agoda_city_list', $data);
                        } else {
                            //update
                            $condition_arr = array();
                            $condition_arr['city_id'] = $data['city_id'];
                            $this->custom_db->update_record('api_agoda_city_list', $data, $condition_arr);
                        }
                    }
                }
            }
            $city_data['status'] = 1;
            $city_data['city_count'] = $city_count;
            $this->custom_db->update_record('api_agoda_country_list', $city_data, array('country_id' => $get_country_list['data'][0]['country_id']));

            //}
            echo "sucess";
            exit;
        }
    }

    function update_agoda_hotel_1() {
        //$feed_id,$city_id='',$country_id='',$hotel_id='',$area_id='',$brand_id=''
        //  $get_city_list = $this->custom_db->single_table_records('api_agoda_city_list','*',array('status'=>0),0,1);   

        $str = "SELECT * FROM (`api_agoda_city_list`) WHERE origin <=2000 and `status` = 0 LIMIT 5";

        $execute_query = $this->db->query($str);
        $get_city_list = $execute_query->result_array();

        if ($get_city_list) {
            foreach ($get_city_list as $c_key => $c_value) {

                $api_data = $this->process_request(5, $c_value['city_id']);
                $hotel_count = 0;

                if (isset($api_data['Hotel_feed']['hotels']['hotel'][0])) {
                    if (isset($api_data['Hotel_feed']['hotels']['hotel'])) {
                        $hotel_count = count($api_data['Hotel_feed']['hotels']['hotel']);
                        foreach ($api_data['Hotel_feed']['hotels']['hotel'] as $key => $value) {
                            $data = array();
                            $data['hotel_id'] = $value['hotel_id'];
                            $data['hotel_name'] = $value['hotel_name'];
                            $data['hotel_formerly_name'] = $value['hotel_formerly_name'];
                            $data['star_rating'] = $value['star_rating'];
                            $data['continent_id'] = $value['continent_id'];
                            $data['country_id'] = $value['country_id'];
                            $data['city_id'] = $value['city_id'];
                            $data['area_id'] = $value['area_id'];
                            $data['longitude'] = $value['longitude'];
                            $data['latitude'] = $value['latitude'];
                            $data['hotel_url'] = $value['hotel_url'];
                            $data['rates_from'] = $value['rates_from'];
                            $data['rates_currency'] = $value['rates_currency'];
                            $data['popularity_score'] = $value['popularity_score'];
                            $data['remark'] = $value['remark'];
                            $data['number_of_reviews'] = $value['number_of_reviews'];
                            $data['rating_average'] = $value['rating_average'];
                            $data['rates_from_exclusive'] = $value['rates_from_exclusive'];
                            $data['child_and_extra_bed_policy'] = json_encode($value['child_and_extra_bed_policy']);
                            $data['accommodation_type'] = $value['accommodation_type'];
                            $data['nationality_restrictions'] = $value['nationality_restrictions'];

                            $check_if_exists = $this->custom_db->single_table_records('agoda_hotel_master', '*', array('hotel_id' => $data['hotel_id']), 0, 1);

                            if ($check_if_exists['status'] == 0) {
                                //insert
                                $this->custom_db->insert_record('agoda_hotel_master', $data);
                            } else {
                                //update
                                $condition = array();
                                $condition['hotel_id'] = $data['hotel_id'];
                                $this->custom_db->update_record('agoda_hotel_master', $data, $condition);
                            }
                        }
                    }
                } else {
                    $hotel_count = 1;
                    $data = array();
                    $data['hotel_id'] = $api_data['Hotel_feed']['hotels']['hotel']['hotel_id'];
                    $data['hotel_name'] = $api_data['Hotel_feed']['hotels']['hotel']['hotel_name'];
                    $data['hotel_formerly_name'] = $api_data['Hotel_feed']['hotels']['hotel']['hotel_formerly_name'];
                    $data['star_rating'] = $api_data['Hotel_feed']['hotels']['hotel']['star_rating'];
                    $data['continent_id'] = $api_data['Hotel_feed']['hotels']['hotel']['continent_id'];
                    $data['country_id'] = $api_data['Hotel_feed']['hotels']['hotel']['country_id'];
                    $data['city_id'] = $api_data['Hotel_feed']['hotels']['hotel']['city_id'];
                    $data['area_id'] = $api_data['Hotel_feed']['hotels']['hotel']['area_id'];
                    $data['longitude'] = $api_data['Hotel_feed']['hotels']['hotel']['longitude'];
                    $data['latitude'] = $api_data['Hotel_feed']['hotels']['hotel']['latitude'];
                    $data['hotel_url'] = $api_data['Hotel_feed']['hotels']['hotel']['hotel_url'];
                    $data['rates_from'] = $api_data['Hotel_feed']['hotels']['hotel']['rates_from'];
                    $data['rates_currency'] = $api_data['Hotel_feed']['hotels']['hotel']['rates_currency'];
                    $data['popularity_score'] = $api_data['Hotel_feed']['hotels']['hotel']['popularity_score'];
                    $data['remark'] = $api_data['Hotel_feed']['hotels']['hotel']['remark'];
                    $data['number_of_reviews'] = $api_data['Hotel_feed']['hotels']['hotel']['number_of_reviews'];
                    $data['rating_average'] = $api_data['Hotel_feed']['hotels']['hotel']['rating_average'];
                    $data['rates_from_exclusive'] = $api_data['Hotel_feed']['hotels']['hotel']['rates_from_exclusive'];
                    $data['child_and_extra_bed_policy'] = json_encode($api_data['Hotel_feed']['hotels']['hotel']['child_and_extra_bed_policy']);
                    $data['accommodation_type'] = $api_data['Hotel_feed']['hotels']['hotel']['accommodation_type'];
                    $data['nationality_restrictions'] = $api_data['Hotel_feed']['hotels']['hotel']['nationality_restrictions'];

                    $check_if_exists = $this->custom_db->single_table_records('agoda_hotel_master', '*', array('hotel_id' => $data['hotel_id']), 0, 1);

                    if ($check_if_exists['status'] == 0) {
                        //insert
                        $this->custom_db->insert_record('agoda_hotel_master', $data);
                    } else {
                        //update
                        $condition = array();
                        $condition['hotel_id'] = $data['hotel_id'];
                        $this->custom_db->update_record('agoda_hotel_master', $data, $condition);
                    }
                }

                $hotel_data['status'] = 1;
                $hotel_data['hotel_count'] = $hotel_count;
                $this->custom_db->update_record('api_agoda_city_list', $hotel_data, array('origin' => $c_value['origin']));
            }
        }
        exit;
    }

    function update_agoda_hotel_2() {
        //$feed_id,$city_id='',$country_id='',$hotel_id='',$area_id='',$brand_id=''
        //  $get_city_list = $this->custom_db->single_table_records('api_agoda_city_list','*',array('status'=>0),0,1);   

        $str = "SELECT * FROM (`api_agoda_city_list`) WHERE origin >=2000 and `status` = 0 LIMIT 5";

        $execute_query = $this->db->query($str);
        $get_city_list = $execute_query->result_array();

        if ($get_city_list) {
            foreach ($get_city_list as $c_key => $c_value) {

                $api_data = $this->process_request(5, $c_value['city_id']);
                $hotel_count = 0;

                if (isset($api_data['Hotel_feed']['hotels']['hotel'][0])) {
                    if (isset($api_data['Hotel_feed']['hotels']['hotel'])) {
                        $hotel_count = count($api_data['Hotel_feed']['hotels']['hotel']);
                        foreach ($api_data['Hotel_feed']['hotels']['hotel'] as $key => $value) {
                            $data = array();
                            $data['hotel_id'] = $value['hotel_id'];
                            $data['hotel_name'] = $value['hotel_name'];
                            $data['hotel_formerly_name'] = $value['hotel_formerly_name'];
                            $data['star_rating'] = $value['star_rating'];
                            $data['continent_id'] = $value['continent_id'];
                            $data['country_id'] = $value['country_id'];
                            $data['city_id'] = $value['city_id'];
                            $data['area_id'] = $value['area_id'];
                            $data['longitude'] = $value['longitude'];
                            $data['latitude'] = $value['latitude'];
                            $data['hotel_url'] = $value['hotel_url'];
                            $data['rates_from'] = $value['rates_from'];
                            $data['rates_currency'] = $value['rates_currency'];
                            $data['popularity_score'] = $value['popularity_score'];
                            $data['remark'] = $value['remark'];
                            $data['number_of_reviews'] = $value['number_of_reviews'];
                            $data['rating_average'] = $value['rating_average'];
                            $data['rates_from_exclusive'] = $value['rates_from_exclusive'];
                            $data['child_and_extra_bed_policy'] = json_encode($value['child_and_extra_bed_policy']);
                            $data['accommodation_type'] = $value['accommodation_type'];
                            $data['nationality_restrictions'] = $value['nationality_restrictions'];

                            $check_if_exists = $this->custom_db->single_table_records('agoda_hotel_master', '*', array('hotel_id' => $data['hotel_id']), 0, 1);

                            if ($check_if_exists['status'] == 0) {
                                //insert
                                $this->custom_db->insert_record('agoda_hotel_master', $data);
                            } else {
                                //update
                                $condition = array();
                                $condition['hotel_id'] = $data['hotel_id'];
                                $this->custom_db->update_record('agoda_hotel_master', $data, $condition);
                            }
                        }
                    }
                } else {
                    $hotel_count = 1;
                    $data = array();
                    $data['hotel_id'] = $api_data['Hotel_feed']['hotels']['hotel']['hotel_id'];
                    $data['hotel_name'] = $api_data['Hotel_feed']['hotels']['hotel']['hotel_name'];
                    $data['hotel_formerly_name'] = $api_data['Hotel_feed']['hotels']['hotel']['hotel_formerly_name'];
                    $data['star_rating'] = $api_data['Hotel_feed']['hotels']['hotel']['star_rating'];
                    $data['continent_id'] = $api_data['Hotel_feed']['hotels']['hotel']['continent_id'];
                    $data['country_id'] = $api_data['Hotel_feed']['hotels']['hotel']['country_id'];
                    $data['city_id'] = $api_data['Hotel_feed']['hotels']['hotel']['city_id'];
                    $data['area_id'] = $api_data['Hotel_feed']['hotels']['hotel']['area_id'];
                    $data['longitude'] = $api_data['Hotel_feed']['hotels']['hotel']['longitude'];
                    $data['latitude'] = $api_data['Hotel_feed']['hotels']['hotel']['latitude'];
                    $data['hotel_url'] = $api_data['Hotel_feed']['hotels']['hotel']['hotel_url'];
                    $data['rates_from'] = $api_data['Hotel_feed']['hotels']['hotel']['rates_from'];
                    $data['rates_currency'] = $api_data['Hotel_feed']['hotels']['hotel']['rates_currency'];
                    $data['popularity_score'] = $api_data['Hotel_feed']['hotels']['hotel']['popularity_score'];
                    $data['remark'] = $api_data['Hotel_feed']['hotels']['hotel']['remark'];
                    $data['number_of_reviews'] = $api_data['Hotel_feed']['hotels']['hotel']['number_of_reviews'];
                    $data['rating_average'] = $api_data['Hotel_feed']['hotels']['hotel']['rating_average'];
                    $data['rates_from_exclusive'] = $api_data['Hotel_feed']['hotels']['hotel']['rates_from_exclusive'];
                    $data['child_and_extra_bed_policy'] = json_encode($api_data['Hotel_feed']['hotels']['hotel']['child_and_extra_bed_policy']);
                    $data['accommodation_type'] = $api_data['Hotel_feed']['hotels']['hotel']['accommodation_type'];
                    $data['nationality_restrictions'] = $api_data['Hotel_feed']['hotels']['hotel']['nationality_restrictions'];

                    $check_if_exists = $this->custom_db->single_table_records('agoda_hotel_master', '*', array('hotel_id' => $data['hotel_id']), 0, 1);

                    if ($check_if_exists['status'] == 0) {
                        //insert
                        $this->custom_db->insert_record('agoda_hotel_master', $data);
                    } else {
                        //update
                        $condition = array();
                        $condition['hotel_id'] = $data['hotel_id'];
                        $this->custom_db->update_record('agoda_hotel_master', $data, $condition);
                    }
                }

                $hotel_data['status'] = 1;
                $hotel_data['hotel_count'] = $hotel_count;
                $this->custom_db->update_record('api_agoda_city_list', $hotel_data, array('origin' => $c_value['origin']));
            }
        }
        exit;
    }

    function update_agoda_hotel_3() {
        //$feed_id,$city_id='',$country_id='',$hotel_id='',$area_id='',$brand_id=''
        //  $get_city_list = $this->custom_db->single_table_records('api_agoda_city_list','*',array('status'=>0),0,1);   

        $str = "SELECT * FROM (`api_agoda_city_list`) WHERE origin >=4000 and `status` = 0 LIMIT 5";

        $execute_query = $this->db->query($str);
        $get_city_list = $execute_query->result_array();

        if ($get_city_list) {
            foreach ($get_city_list as $c_key => $c_value) {

                $api_data = $this->process_request(5, $c_value['city_id']);
                $hotel_count = 0;

                if (isset($api_data['Hotel_feed']['hotels']['hotel'][0])) {
                    if (isset($api_data['Hotel_feed']['hotels']['hotel'])) {
                        $hotel_count = count($api_data['Hotel_feed']['hotels']['hotel']);
                        foreach ($api_data['Hotel_feed']['hotels']['hotel'] as $key => $value) {
                            $data = array();
                            $data['hotel_id'] = $value['hotel_id'];
                            $data['hotel_name'] = $value['hotel_name'];
                            $data['hotel_formerly_name'] = $value['hotel_formerly_name'];
                            $data['star_rating'] = $value['star_rating'];
                            $data['continent_id'] = $value['continent_id'];
                            $data['country_id'] = $value['country_id'];
                            $data['city_id'] = $value['city_id'];
                            $data['area_id'] = $value['area_id'];
                            $data['longitude'] = $value['longitude'];
                            $data['latitude'] = $value['latitude'];
                            $data['hotel_url'] = $value['hotel_url'];
                            $data['rates_from'] = $value['rates_from'];
                            $data['rates_currency'] = $value['rates_currency'];
                            $data['popularity_score'] = $value['popularity_score'];
                            $data['remark'] = $value['remark'];
                            $data['number_of_reviews'] = $value['number_of_reviews'];
                            $data['rating_average'] = $value['rating_average'];
                            $data['rates_from_exclusive'] = $value['rates_from_exclusive'];
                            $data['child_and_extra_bed_policy'] = json_encode($value['child_and_extra_bed_policy']);
                            $data['accommodation_type'] = $value['accommodation_type'];
                            $data['nationality_restrictions'] = $value['nationality_restrictions'];

                            $check_if_exists = $this->custom_db->single_table_records('agoda_hotel_master', '*', array('hotel_id' => $data['hotel_id']), 0, 1);

                            if ($check_if_exists['status'] == 0) {
                                //insert
                                $this->custom_db->insert_record('agoda_hotel_master', $data);
                            } else {
                                //update
                                $condition = array();
                                $condition['hotel_id'] = $data['hotel_id'];
                                $this->custom_db->update_record('agoda_hotel_master', $data, $condition);
                            }
                        }
                    }
                } else {
                    $hotel_count = 1;
                    $data = array();
                    $data['hotel_id'] = $api_data['Hotel_feed']['hotels']['hotel']['hotel_id'];
                    $data['hotel_name'] = $api_data['Hotel_feed']['hotels']['hotel']['hotel_name'];
                    $data['hotel_formerly_name'] = $api_data['Hotel_feed']['hotels']['hotel']['hotel_formerly_name'];
                    $data['star_rating'] = $api_data['Hotel_feed']['hotels']['hotel']['star_rating'];
                    $data['continent_id'] = $api_data['Hotel_feed']['hotels']['hotel']['continent_id'];
                    $data['country_id'] = $api_data['Hotel_feed']['hotels']['hotel']['country_id'];
                    $data['city_id'] = $api_data['Hotel_feed']['hotels']['hotel']['city_id'];
                    $data['area_id'] = $api_data['Hotel_feed']['hotels']['hotel']['area_id'];
                    $data['longitude'] = $api_data['Hotel_feed']['hotels']['hotel']['longitude'];
                    $data['latitude'] = $api_data['Hotel_feed']['hotels']['hotel']['latitude'];
                    $data['hotel_url'] = $api_data['Hotel_feed']['hotels']['hotel']['hotel_url'];
                    $data['rates_from'] = $api_data['Hotel_feed']['hotels']['hotel']['rates_from'];
                    $data['rates_currency'] = $api_data['Hotel_feed']['hotels']['hotel']['rates_currency'];
                    $data['popularity_score'] = $api_data['Hotel_feed']['hotels']['hotel']['popularity_score'];
                    $data['remark'] = $api_data['Hotel_feed']['hotels']['hotel']['remark'];
                    $data['number_of_reviews'] = $api_data['Hotel_feed']['hotels']['hotel']['number_of_reviews'];
                    $data['rating_average'] = $api_data['Hotel_feed']['hotels']['hotel']['rating_average'];
                    $data['rates_from_exclusive'] = $api_data['Hotel_feed']['hotels']['hotel']['rates_from_exclusive'];
                    $data['child_and_extra_bed_policy'] = json_encode($api_data['Hotel_feed']['hotels']['hotel']['child_and_extra_bed_policy']);
                    $data['accommodation_type'] = $api_data['Hotel_feed']['hotels']['hotel']['accommodation_type'];
                    $data['nationality_restrictions'] = $api_data['Hotel_feed']['hotels']['hotel']['nationality_restrictions'];

                    $check_if_exists = $this->custom_db->single_table_records('agoda_hotel_master', '*', array('hotel_id' => $data['hotel_id']), 0, 1);

                    if ($check_if_exists['status'] == 0) {
                        //insert
                        $this->custom_db->insert_record('agoda_hotel_master', $data);
                    } else {
                        //update
                        $condition = array();
                        $condition['hotel_id'] = $data['hotel_id'];
                        $this->custom_db->update_record('agoda_hotel_master', $data, $condition);
                    }
                }

                $hotel_data['status'] = 1;
                $hotel_data['hotel_count'] = $hotel_count;
                $this->custom_db->update_record('api_agoda_city_list', $hotel_data, array('origin' => $c_value['origin']));
            }
        }
        exit;
    }

    function update_agoda_hotel_4() {
        //$feed_id,$city_id='',$country_id='',$hotel_id='',$area_id='',$brand_id=''
        //  $get_city_list = $this->custom_db->single_table_records('api_agoda_city_list','*',array('status'=>0),0,1);   

        $str = "SELECT * FROM (`api_agoda_city_list`) WHERE origin >=6000 and `status` = 0 LIMIT 5";

        $execute_query = $this->db->query($str);
        $get_city_list = $execute_query->result_array();

        if ($get_city_list) {
            foreach ($get_city_list as $c_key => $c_value) {

                $api_data = $this->process_request(5, $c_value['city_id']);
                $hotel_count = 0;

                if (isset($api_data['Hotel_feed']['hotels']['hotel'][0])) {
                    if (isset($api_data['Hotel_feed']['hotels']['hotel'])) {
                        $hotel_count = count($api_data['Hotel_feed']['hotels']['hotel']);
                        foreach ($api_data['Hotel_feed']['hotels']['hotel'] as $key => $value) {
                            $data = array();
                            $data['hotel_id'] = $value['hotel_id'];
                            $data['hotel_name'] = $value['hotel_name'];
                            $data['hotel_formerly_name'] = $value['hotel_formerly_name'];
                            $data['star_rating'] = $value['star_rating'];
                            $data['continent_id'] = $value['continent_id'];
                            $data['country_id'] = $value['country_id'];
                            $data['city_id'] = $value['city_id'];
                            $data['area_id'] = $value['area_id'];
                            $data['longitude'] = $value['longitude'];
                            $data['latitude'] = $value['latitude'];
                            $data['hotel_url'] = $value['hotel_url'];
                            $data['rates_from'] = $value['rates_from'];
                            $data['rates_currency'] = $value['rates_currency'];
                            $data['popularity_score'] = $value['popularity_score'];
                            $data['remark'] = $value['remark'];
                            $data['number_of_reviews'] = $value['number_of_reviews'];
                            $data['rating_average'] = $value['rating_average'];
                            $data['rates_from_exclusive'] = $value['rates_from_exclusive'];
                            $data['child_and_extra_bed_policy'] = json_encode($value['child_and_extra_bed_policy']);
                            $data['accommodation_type'] = $value['accommodation_type'];
                            $data['nationality_restrictions'] = $value['nationality_restrictions'];

                            $check_if_exists = $this->custom_db->single_table_records('agoda_hotel_master', '*', array('hotel_id' => $data['hotel_id']), 0, 1);

                            if ($check_if_exists['status'] == 0) {
                                //insert
                                $this->custom_db->insert_record('agoda_hotel_master', $data);
                            } else {
                                //update
                                $condition = array();
                                $condition['hotel_id'] = $data['hotel_id'];
                                $this->custom_db->update_record('agoda_hotel_master', $data, $condition);
                            }
                        }
                    }
                } else {
                    $hotel_count = 1;
                    $data = array();
                    $data['hotel_id'] = $api_data['Hotel_feed']['hotels']['hotel']['hotel_id'];
                    $data['hotel_name'] = $api_data['Hotel_feed']['hotels']['hotel']['hotel_name'];
                    $data['hotel_formerly_name'] = $api_data['Hotel_feed']['hotels']['hotel']['hotel_formerly_name'];
                    $data['star_rating'] = $api_data['Hotel_feed']['hotels']['hotel']['star_rating'];
                    $data['continent_id'] = $api_data['Hotel_feed']['hotels']['hotel']['continent_id'];
                    $data['country_id'] = $api_data['Hotel_feed']['hotels']['hotel']['country_id'];
                    $data['city_id'] = $api_data['Hotel_feed']['hotels']['hotel']['city_id'];
                    $data['area_id'] = $api_data['Hotel_feed']['hotels']['hotel']['area_id'];
                    $data['longitude'] = $api_data['Hotel_feed']['hotels']['hotel']['longitude'];
                    $data['latitude'] = $api_data['Hotel_feed']['hotels']['hotel']['latitude'];
                    $data['hotel_url'] = $api_data['Hotel_feed']['hotels']['hotel']['hotel_url'];
                    $data['rates_from'] = $api_data['Hotel_feed']['hotels']['hotel']['rates_from'];
                    $data['rates_currency'] = $api_data['Hotel_feed']['hotels']['hotel']['rates_currency'];
                    $data['popularity_score'] = $api_data['Hotel_feed']['hotels']['hotel']['popularity_score'];
                    $data['remark'] = $api_data['Hotel_feed']['hotels']['hotel']['remark'];
                    $data['number_of_reviews'] = $api_data['Hotel_feed']['hotels']['hotel']['number_of_reviews'];
                    $data['rating_average'] = $api_data['Hotel_feed']['hotels']['hotel']['rating_average'];
                    $data['rates_from_exclusive'] = $api_data['Hotel_feed']['hotels']['hotel']['rates_from_exclusive'];
                    $data['child_and_extra_bed_policy'] = json_encode($api_data['Hotel_feed']['hotels']['hotel']['child_and_extra_bed_policy']);
                    $data['accommodation_type'] = $api_data['Hotel_feed']['hotels']['hotel']['accommodation_type'];
                    $data['nationality_restrictions'] = $api_data['Hotel_feed']['hotels']['hotel']['nationality_restrictions'];

                    $check_if_exists = $this->custom_db->single_table_records('agoda_hotel_master', '*', array('hotel_id' => $data['hotel_id']), 0, 1);

                    if ($check_if_exists['status'] == 0) {
                        //insert
                        $this->custom_db->insert_record('agoda_hotel_master', $data);
                    } else {
                        //update
                        $condition = array();
                        $condition['hotel_id'] = $data['hotel_id'];
                        $this->custom_db->update_record('agoda_hotel_master', $data, $condition);
                    }
                }

                $hotel_data['status'] = 1;
                $hotel_data['hotel_count'] = $hotel_count;
                $this->custom_db->update_record('api_agoda_city_list', $hotel_data, array('origin' => $c_value['origin']));
            }
        }
        exit;
    }

    function update_agoda_hotel_5() {
        //$feed_id,$city_id='',$country_id='',$hotel_id='',$area_id='',$brand_id=''
        //  $get_city_list = $this->custom_db->single_table_records('api_agoda_city_list','*',array('status'=>0),0,1);   

        $str = "SELECT * FROM (`api_agoda_city_list`) WHERE origin >=8000 and `status` = 0 LIMIT 5";

        $execute_query = $this->db->query($str);
        $get_city_list = $execute_query->result_array();

        if ($get_city_list) {
            foreach ($get_city_list as $c_key => $c_value) {

                $api_data = $this->process_request(5, $c_value['city_id']);
                $hotel_count = 0;

                if (isset($api_data['Hotel_feed']['hotels']['hotel'][0])) {
                    if (isset($api_data['Hotel_feed']['hotels']['hotel'])) {
                        $hotel_count = count($api_data['Hotel_feed']['hotels']['hotel']);
                        foreach ($api_data['Hotel_feed']['hotels']['hotel'] as $key => $value) {
                            $data = array();
                            $data['hotel_id'] = $value['hotel_id'];
                            $data['hotel_name'] = $value['hotel_name'];
                            $data['hotel_formerly_name'] = $value['hotel_formerly_name'];
                            $data['star_rating'] = $value['star_rating'];
                            $data['continent_id'] = $value['continent_id'];
                            $data['country_id'] = $value['country_id'];
                            $data['city_id'] = $value['city_id'];
                            $data['area_id'] = $value['area_id'];
                            $data['longitude'] = $value['longitude'];
                            $data['latitude'] = $value['latitude'];
                            $data['hotel_url'] = $value['hotel_url'];
                            $data['rates_from'] = $value['rates_from'];
                            $data['rates_currency'] = $value['rates_currency'];
                            $data['popularity_score'] = $value['popularity_score'];
                            $data['remark'] = $value['remark'];
                            $data['number_of_reviews'] = $value['number_of_reviews'];
                            $data['rating_average'] = $value['rating_average'];
                            $data['rates_from_exclusive'] = $value['rates_from_exclusive'];
                            $data['child_and_extra_bed_policy'] = json_encode($value['child_and_extra_bed_policy']);
                            $data['accommodation_type'] = $value['accommodation_type'];
                            $data['nationality_restrictions'] = $value['nationality_restrictions'];

                            $check_if_exists = $this->custom_db->single_table_records('agoda_hotel_master', '*', array('hotel_id' => $data['hotel_id']), 0, 1);

                            if ($check_if_exists['status'] == 0) {
                                //insert
                                $this->custom_db->insert_record('agoda_hotel_master', $data);
                            } else {
                                //update
                                $condition = array();
                                $condition['hotel_id'] = $data['hotel_id'];
                                $this->custom_db->update_record('agoda_hotel_master', $data, $condition);
                            }
                        }
                    }
                } else {
                    $hotel_count = 1;
                    $data = array();
                    $data['hotel_id'] = $api_data['Hotel_feed']['hotels']['hotel']['hotel_id'];
                    $data['hotel_name'] = $api_data['Hotel_feed']['hotels']['hotel']['hotel_name'];
                    $data['hotel_formerly_name'] = $api_data['Hotel_feed']['hotels']['hotel']['hotel_formerly_name'];
                    $data['star_rating'] = $api_data['Hotel_feed']['hotels']['hotel']['star_rating'];
                    $data['continent_id'] = $api_data['Hotel_feed']['hotels']['hotel']['continent_id'];
                    $data['country_id'] = $api_data['Hotel_feed']['hotels']['hotel']['country_id'];
                    $data['city_id'] = $api_data['Hotel_feed']['hotels']['hotel']['city_id'];
                    $data['area_id'] = $api_data['Hotel_feed']['hotels']['hotel']['area_id'];
                    $data['longitude'] = $api_data['Hotel_feed']['hotels']['hotel']['longitude'];
                    $data['latitude'] = $api_data['Hotel_feed']['hotels']['hotel']['latitude'];
                    $data['hotel_url'] = $api_data['Hotel_feed']['hotels']['hotel']['hotel_url'];
                    $data['rates_from'] = $api_data['Hotel_feed']['hotels']['hotel']['rates_from'];
                    $data['rates_currency'] = $api_data['Hotel_feed']['hotels']['hotel']['rates_currency'];
                    $data['popularity_score'] = $api_data['Hotel_feed']['hotels']['hotel']['popularity_score'];
                    $data['remark'] = $api_data['Hotel_feed']['hotels']['hotel']['remark'];
                    $data['number_of_reviews'] = $api_data['Hotel_feed']['hotels']['hotel']['number_of_reviews'];
                    $data['rating_average'] = $api_data['Hotel_feed']['hotels']['hotel']['rating_average'];
                    $data['rates_from_exclusive'] = $api_data['Hotel_feed']['hotels']['hotel']['rates_from_exclusive'];
                    $data['child_and_extra_bed_policy'] = json_encode($api_data['Hotel_feed']['hotels']['hotel']['child_and_extra_bed_policy']);
                    $data['accommodation_type'] = $api_data['Hotel_feed']['hotels']['hotel']['accommodation_type'];
                    $data['nationality_restrictions'] = $api_data['Hotel_feed']['hotels']['hotel']['nationality_restrictions'];

                    $check_if_exists = $this->custom_db->single_table_records('agoda_hotel_master', '*', array('hotel_id' => $data['hotel_id']), 0, 1);

                    if ($check_if_exists['status'] == 0) {
                        //insert
                        $this->custom_db->insert_record('agoda_hotel_master', $data);
                    } else {
                        //update
                        $condition = array();
                        $condition['hotel_id'] = $data['hotel_id'];
                        $this->custom_db->update_record('agoda_hotel_master', $data, $condition);
                    }
                }

                $hotel_data['status'] = 1;
                $hotel_data['hotel_count'] = $hotel_count;
                $this->custom_db->update_record('api_agoda_city_list', $hotel_data, array('origin' => $c_value['origin']));
            }
        }
        exit;
    }

    function update_agoda_hotel_6() {
        //$feed_id,$city_id='',$country_id='',$hotel_id='',$area_id='',$brand_id=''
        //  $get_city_list = $this->custom_db->single_table_records('api_agoda_city_list','*',array('status'=>0),0,1);   

        $str = "SELECT * FROM (`api_agoda_city_list`) WHERE origin >=10000 and `status` = 0 LIMIT 5";

        $execute_query = $this->db->query($str);
        $get_city_list = $execute_query->result_array();

        if ($get_city_list) {
            foreach ($get_city_list as $c_key => $c_value) {

                $api_data = $this->process_request(5, $c_value['city_id']);
                $hotel_count = 0;

                if (isset($api_data['Hotel_feed']['hotels']['hotel'][0])) {
                    if (isset($api_data['Hotel_feed']['hotels']['hotel'])) {
                        $hotel_count = count($api_data['Hotel_feed']['hotels']['hotel']);
                        foreach ($api_data['Hotel_feed']['hotels']['hotel'] as $key => $value) {
                            $data = array();
                            $data['hotel_id'] = $value['hotel_id'];
                            $data['hotel_name'] = $value['hotel_name'];
                            $data['hotel_formerly_name'] = $value['hotel_formerly_name'];
                            $data['star_rating'] = $value['star_rating'];
                            $data['continent_id'] = $value['continent_id'];
                            $data['country_id'] = $value['country_id'];
                            $data['city_id'] = $value['city_id'];
                            $data['area_id'] = $value['area_id'];
                            $data['longitude'] = $value['longitude'];
                            $data['latitude'] = $value['latitude'];
                            $data['hotel_url'] = $value['hotel_url'];
                            $data['rates_from'] = $value['rates_from'];
                            $data['rates_currency'] = $value['rates_currency'];
                            $data['popularity_score'] = $value['popularity_score'];
                            $data['remark'] = $value['remark'];
                            $data['number_of_reviews'] = $value['number_of_reviews'];
                            $data['rating_average'] = $value['rating_average'];
                            $data['rates_from_exclusive'] = $value['rates_from_exclusive'];
                            $data['child_and_extra_bed_policy'] = json_encode($value['child_and_extra_bed_policy']);
                            $data['accommodation_type'] = $value['accommodation_type'];
                            $data['nationality_restrictions'] = $value['nationality_restrictions'];

                            $check_if_exists = $this->custom_db->single_table_records('agoda_hotel_master', '*', array('hotel_id' => $data['hotel_id']), 0, 1);

                            if ($check_if_exists['status'] == 0) {
                                //insert
                                $this->custom_db->insert_record('agoda_hotel_master', $data);
                            } else {
                                //update
                                $condition = array();
                                $condition['hotel_id'] = $data['hotel_id'];
                                $this->custom_db->update_record('agoda_hotel_master', $data, $condition);
                            }
                        }
                    }
                } else {
                    $hotel_count = 1;
                    $data = array();
                    $data['hotel_id'] = $api_data['Hotel_feed']['hotels']['hotel']['hotel_id'];
                    $data['hotel_name'] = $api_data['Hotel_feed']['hotels']['hotel']['hotel_name'];
                    $data['hotel_formerly_name'] = $api_data['Hotel_feed']['hotels']['hotel']['hotel_formerly_name'];
                    $data['star_rating'] = $api_data['Hotel_feed']['hotels']['hotel']['star_rating'];
                    $data['continent_id'] = $api_data['Hotel_feed']['hotels']['hotel']['continent_id'];
                    $data['country_id'] = $api_data['Hotel_feed']['hotels']['hotel']['country_id'];
                    $data['city_id'] = $api_data['Hotel_feed']['hotels']['hotel']['city_id'];
                    $data['area_id'] = $api_data['Hotel_feed']['hotels']['hotel']['area_id'];
                    $data['longitude'] = $api_data['Hotel_feed']['hotels']['hotel']['longitude'];
                    $data['latitude'] = $api_data['Hotel_feed']['hotels']['hotel']['latitude'];
                    $data['hotel_url'] = $api_data['Hotel_feed']['hotels']['hotel']['hotel_url'];
                    $data['rates_from'] = $api_data['Hotel_feed']['hotels']['hotel']['rates_from'];
                    $data['rates_currency'] = $api_data['Hotel_feed']['hotels']['hotel']['rates_currency'];
                    $data['popularity_score'] = $api_data['Hotel_feed']['hotels']['hotel']['popularity_score'];
                    $data['remark'] = $api_data['Hotel_feed']['hotels']['hotel']['remark'];
                    $data['number_of_reviews'] = $api_data['Hotel_feed']['hotels']['hotel']['number_of_reviews'];
                    $data['rating_average'] = $api_data['Hotel_feed']['hotels']['hotel']['rating_average'];
                    $data['rates_from_exclusive'] = $api_data['Hotel_feed']['hotels']['hotel']['rates_from_exclusive'];
                    $data['child_and_extra_bed_policy'] = json_encode($api_data['Hotel_feed']['hotels']['hotel']['child_and_extra_bed_policy']);
                    $data['accommodation_type'] = $api_data['Hotel_feed']['hotels']['hotel']['accommodation_type'];
                    $data['nationality_restrictions'] = $api_data['Hotel_feed']['hotels']['hotel']['nationality_restrictions'];

                    $check_if_exists = $this->custom_db->single_table_records('agoda_hotel_master', '*', array('hotel_id' => $data['hotel_id']), 0, 1);

                    if ($check_if_exists['status'] == 0) {
                        //insert
                        $this->custom_db->insert_record('agoda_hotel_master', $data);
                    } else {
                        //update
                        $condition = array();
                        $condition['hotel_id'] = $data['hotel_id'];
                        $this->custom_db->update_record('agoda_hotel_master', $data, $condition);
                    }
                }

                $hotel_data['status'] = 1;
                $hotel_data['hotel_count'] = $hotel_count;
                $this->custom_db->update_record('api_agoda_city_list', $hotel_data, array('origin' => $c_value['origin']));
            }
        }
        exit;
    }
    //function agoda downloading the hotel images
    function donload_agoda_image(){
    	$time = date('h:i a');
       
        $current_time = date('h:i a');
        $sunrise = "12:00 am";
        $sunset = "5:30 am";
        $date1 = DateTime::createFromFormat('H:i a', $current_time);
        $date2 = DateTime::createFromFormat('H:i a', $sunrise);
        $date3 = DateTime::createFromFormat('H:i a', $sunset);
        // debug($date1);exit;
        // if ($date1 >= $date2 && $date1 < $date3){
      
          
       	$str = "SELECT * FROM (`agoda_hotel_master`) WHERE `image_status` = 0 and image_not_found = 0 LIMIT 10";

        $execute_query = $this->db->query($str);
        $get_hotel_list = $execute_query->result_array();

        if ($get_hotel_list) {
            foreach ($get_hotel_list as $h_key => $h_value) {
                
                $api_data = $this->process_request(7, '','',$h_value['hotel_id']);

                 // debug($api_data);
                 if (isset($api_data['Picture_feed']['pictures']['picture'][0])) {
                    if (isset($api_data['Picture_feed']['pictures']['picture'])) {
                        
                        foreach ($api_data['Picture_feed']['pictures']['picture'] as $key => $value) {
                            $condition['hotel_id'] = $value['hotel_id'];
                            $image_data[$key]['caption'] = $value['caption'];
                            $image_data[$key]['image_urls'] = $value['URL'];
                        }
                        $insert_image_data = json_encode($image_data);
                        $update_data['Hotel_ImageLinks'] = $insert_image_data;
                        $update_data['Image'] = $api_data['Picture_feed']['pictures']['picture'][0]['URL'];
                        $update_data['image_status'] = 1;
                        // debug($update_data);exit;
                        $this->custom_db->update_record('agoda_hotel_master', $update_data, $condition);


                    }
                }
                else{
                    if (isset($api_data['Picture_feed']['pictures']['picture'])) {
                        $condition['hotel_id'] = $api_data['Picture_feed']['pictures']['picture']['hotel_id'];
                        $image_data['caption'] = $api_data['Picture_feed']['pictures']['picture']['caption'];
                        $image_data['image_urls'] = $api_data['Picture_feed']['pictures']['picture']['URL'];
                        $insert_image_data = json_encode($image_data);
                        $update_data['Hotel_ImageLinks'] = $insert_image_data;
                        $update_data['image_status'] = 1;
                        $update_data['Image'] = $api_data['Picture_feed']['pictures']['picture']['URL'];
                        $this->custom_db->update_record('agoda_hotel_master', $update_data, $condition);

                    }
                    else{
                    	$condition['hotel_id'] = $h_value['hotel_id'];
                    	$update_data['image_not_found'] = 1;
                    	$this->custom_db->update_record('agoda_hotel_master', $update_data, $condition);
                    }
                }
               
               
            }
            exit;
            }
        // }
       
    }
     //function agoda downloading the hotel facilities
    function donload_agoda_facility(){
    	$str = "SELECT * FROM (`agoda_hotel_master`) WHERE origin > 28589 and `fac_status` = 0 and city_id= 4923 LIMIT 10 ";

        $execute_query = $this->db->query($str);
        $get_hotel_list = $execute_query->result_array();

        if ($get_hotel_list) {
            foreach ($get_hotel_list as $h_key => $h_value) {
            	
                $api_data = $this->process_request(14, '','',$h_value['hotel_id']);
                $prop_id = array();
                $facility_data = array();
                 if (isset($api_data['Roomtype_facility_feed']['roomtype_facilities']['roomtype_facility'][0])) {
                    if (isset($api_data['Roomtype_facility_feed']['roomtype_facilities']['roomtype_facility'])) {
                    	
                    	foreach ($api_data['Roomtype_facility_feed']['roomtype_facilities']['roomtype_facility'] as $key => $value) {
                    		$condition['hotel_id'] = $value['hotel_id'];
                    		if(!in_array($value['property_id'],$prop_id)){
                                
                                $facility_data[$key]['property_id'] = $value['property_id'];
                                $facility_data[$key]['property_name'] = $value['property_name'];
                                $facility_data[$key]['translated_name'] = $value['translated_name'];
                            }
                            $prop_id[] = $value['property_id'];
						}
						//$facility_data = array_map("unserialize", array_unique(array_map("serialize", $facility_data)));
						$facility_data = array_values($facility_data);
						$facility_data = json_encode($facility_data);
						$update_data['Hotel_facilities'] = $facility_data;
						$update_data['fac_status'] = 1;
						// debug($update_data);exit;
						$this->custom_db->update_record('agoda_hotel_master', $update_data, $condition);


                    }
                }
                else{
                	if (isset($api_data['Picture_feed']['pictures']['picture'])) {
                		$condition['hotel_id'] = $api_data['Roomtype_facility_feed']['roomtype_facilities']['roomtype_facility']['hotel_id'];
                        $facility_data['property_id'] = $api_data['Roomtype_facility_feed']['roomtype_facilities']['roomtype_facility']['property_id'];
                        $facility_data['property_name'] = $api_data['Roomtype_facility_feed']['roomtype_facilities']['roomtype_facility']['property_name'];
                        $facility_data['translated_name'] = $api_data['Roomtype_facility_feed']['roomtype_facilities']['roomtype_facility']['translated_name'];
                		
                		$facility_data = json_encode($facility_data);
                		$update_data['Hotel_facilities'] = $facility_data;
						$update_data['fac_status'] = 1;
						$this->custom_db->update_record('agoda_hotel_master', $update_data, $condition);

                	}
                }
               
               
            }
            exit;
        }
    }
     //function agoda downloading the hotel data
    function donload_agoda_hotel_info(){
    	$str = "SELECT * FROM (`agoda_hotel_master`) WHERE `hot_add_status` = 0 LIMIT 1";

        $execute_query = $this->db->query($str);
        $get_hotel_list = $execute_query->result_array();

        if ($get_hotel_list) {
            foreach ($get_hotel_list as $h_key => $h_value) {
            	
                $api_data = $this->process_request(18, '','','12134');
                // debug($api_data);exit;
                 if (isset($api_data['Hotel_address_feed']['hotel_addresses']['hotel_address'][0])) {
                    if (isset($api_data['Hotel_address_feed']['hotel_addresses']['hotel_address'])) {
                    	
                    	foreach ($api_data['Hotel_address_feed']['hotel_addresses']['hotel_address'] as $key => $value) {
                    		$condition['hotel_id'] = $value['hotel_id'];
                    		if($value['address_type'] == 'English address'){
                    			$address = $value['address_line_1'].','.$value['state'].','.$value['city'].','.$value['country'].','.$value['postal_code'];
                            	
                    		}
                    		
						}
						
						$update_data['address'] = $address;
						$update_data['hot_add_status'] = 1;
						debug($update_data);exit;
						$this->custom_db->update_record('agoda_hotel_master', $update_data, $condition);


                    }
                }
                else{
                	if (isset($api_data['Hotel_address_feed']['hotel_addresses']['hotel_address'])) {
                		$condition['hotel_id'] = $api_data['Hotel_address_feed']['hotel_addresses']['hotel_address']['hotel_id'];
                        $address = $api_data['Hotel_address_feed']['hotel_addresses']['hotel_address']['address_line_1'].','.$api_data['Hotel_address_feed']['hotel_addresses']['hotel_address']['state'].','.$api_data['Hotel_address_feed']['hotel_addresses']['hotel_address']['city'].','.$api_data['Hotel_address_feed']['hotel_addresses']['hotel_address']['country'].','.$api_data['Hotel_address_feed']['hotel_addresses']['hotel_address']['postal_code'];
						$update_data['address'] = $address;
						$update_data['hot_add_status'] = 1;
						// debug($update_data);exit;
						$this->custom_db->update_record('agoda_hotel_master', $update_data, $condition);

                	}
                }
               
               
            }
            exit;
        }
    }
    // downlaod hotel address
    function donload_agoda_hotel_desc(){
    	$str = "SELECT * FROM (`agoda_hotel_master`) WHERE `hot_desc_status` = 0 LIMIT 1";

        $execute_query = $this->db->query($str);
        $get_hotel_list = $execute_query->result_array();

        if ($get_hotel_list) {
            foreach ($get_hotel_list as $h_key => $h_value) {
            	
                $api_data = $this->process_request(17, '','',$h_value['hotel_id']);
                
              	if (isset($api_data['Hotel_description_feed']['hotel_descriptions']['hotel_description'])) {
            		$condition['hotel_id'] = $api_data['Hotel_description_feed']['hotel_descriptions']['hotel_description']['hotel_id'];
                    $desc = $api_data['Hotel_description_feed']['hotel_descriptions']['hotel_description']['overview'];
					$update_data['description'] = $desc;
					$update_data['hot_desc_status'] = 1;
					// debug($update_data);exit;
					$this->custom_db->update_record('agoda_hotel_master', $update_data, $condition);

                }
               
               
               
            }
            exit;
        }
    }
    /*
     * Transfer Airport, Hotel auto suggest
     */

    function get_airport_transfer_code_list() {

        $result = array();
        $this->load->model('hotel_model');
        $this->load->model('transfer_model');
        $term = $this->input->get('term'); //retrieve the search term that autocomplete sends
        $term = trim(strip_tags($term));
        
        $airport_data_list = $this->transfer_model->get_airport_list($term)->result();

        if (valid_array($airport_data_list) == false) {
            $airport_data_list = $this->transfer_model->get_airport_list('$term')->result();
        }

        foreach ($airport_data_list as $airport) {
            $airport_result['label'] = $airport->airport_name . ' (' . $airport->airport_city . ')';
            $airport_result['id'] = $airport->airport_code;

            $airport_result['transfer_type'] = "ProductTransferTerminal";

            $airport_result ['category'] = '';
            $airport_result ['type'] = '';
            array_push($result, $airport_result);
        }

       /* $data_list = $this->transfer_model->get_hotels_list($term)->result();
        
        if (valid_array($data_list) == false) {
            $data_list = $this->transfer_model->get_hotels_list('')->result();
        }

        foreach ($data_list as $hotel) {
            $transfer_result['label'] = $hotel->hotel_name . ' (' . $hotel->hotel_city . ')';
            $transfer_result['id'] = $hotel->hotel_code;

            $transfer_result['transfer_type'] = "ProductTransferHotel";

            $transfer_result['category'] = '';
            $transfer_result['type'] = '';
            array_push($result, $transfer_result);
        }*/

        $this->output_compressed_data($result);
    }
    function download_agoda_images(){
        $str = "SELECT hotel_id as hotel_code FROM `agoda_hotel_master` where Image!='' limit 0,10";
        // echo $str;exit;
        $execute_query = $this->db->query($str);
        $static_hotel = array();
        if ($execute_query->num_rows()) {
            $static_hotel = $execute_query->result_array();
        }        
        $arrContextOptions=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        );  
        if ($static_hotel) {
            foreach ($static_hotel as $key => $value) {
                if($value['hotel_code']){
                    $get_static_image_url = $this->custom_db->single_table_records('agoda_hotel_master','origin,Image,image_d_status',array('hotel_id'=>$value['hotel_code']),0,1);
                    //debug($get_static_image_url);exit;
                    if($get_static_image_url['status']==1 && empty($get_static_image_url['data'][0])==false){
                        if($get_static_image_url['data'][0]){
                            $image_url = $get_static_image_url['data'][0]['Image'];
                            $hotel_origin = $get_static_image_url['data'][0]['origin'];
                            $path_info = pathinfo($image_url);
                            $path_info_base_name1 = $path_info['basename'];
                            $path_info_base_name1 = explode('?', $path_info_base_name1);
                            $path_info_base_name = $path_info_base_name1[0];
                          
                            $update_condition['origin'] = $hotel_origin;
                            // $image_url ='http://photos.hotelbeds.com/giata/00/000001/000001a_hb_r_001.jpg';
                            $image_file_exists = file_get_contents($image_url,false,stream_context_create($arrContextOptions));
                            
                            if($image_file_exists){
                                $image = $image_file_exists;
                                if ($get_static_image_url['data'][0]['image_d_status'] == 1) {
                                    // no need to update
                                }
                                else{
                                    $hotel_code = preg_replace("/[^a-zA-Z0-9]/", "", $value['hotel_code']);
                                    $image_file_name = $hotel_code . time() . $path_info_base_name;
                                    $image_folder_path = './cdn/agoda/images/' . $image_file_name;
                                    file_put_contents($image_folder_path, $image); //Where to save the image on your server
                                    $image_data['travelomatix_path'] = $image_file_name;
                                    
                                    $this->custom_db->update_record('agoda_hotel_master', $image_data, $update_condition);
                                }
                                $image_update_data['image_found'] = 2;
                            }
                            else{
                                
                                $image_update_data['image_found'] = 1;
                            }
                            $this->custom_db->update_record('agoda_hotel_master', $image_update_data, $update_condition);
                        }
                    }
                } 
                $full_update['image_d_status'] = 1;
                $full_condition['hotel_id'] = $value['hotel_code'];
                            //update in all_api_city_master
                $this->custom_db->update_record('agoda_hotel_master', $full_update, $full_condition);  
            }
        }    
    }
    function test_time(){
       $time = date('H');
       if($time < 23 && $time > 5){

       }
    }
    
    
    
    
    
     /*
     *
     * Flight Search For Voice Search
     * Balu A
     *
     */

    
   function get_airport_code_list_for_voice_speach($term) {
 
       // $term = $this->input->get('term'); //retrieve the search term that autocomplete sends
        $term = trim(strip_tags($term));
       
        $result = array();

        $__airports = $this->flight_model->get_airport_list($term)->result();
        $airports = array();
        foreach ($__airports as $airport) {
            $airports['label'] = $airport->airport_city . ', ' . $airport->country . ' (' . $airport->airport_code . ')';
            $airports['value'] = $airport->airport_city . ' (' . $airport->airport_code . ')';
            $airports['id'] = $airport->origin;
            if (empty($airport->top_destination) == false) {
                $airports['category'] = 'Top cities';
                $airports['type'] = 'Top cities';
            } else {
                $airports['category'] = 'Search Results';
                $airports['type'] = 'Search Results';
            }

            array_push($result, $airports);
        }
       
        echo $result[0]['value'].'|'.$result[0]['id'];
      
    }
    
    
   
    
   function vocie_colleting($data)
    {
        
       $city=array();
       $data  =strtolower(urldecode($data));
       $data__1=explode(" ",$data);
       foreach($data__1 as $value)
       {
          if($value!="flight" && $value!="flights" && $value!="from" && $value!="to")
          {
             $city[]=$value;
          }
       }
       echo json_encode($city);
    
    }
    function get_booking_currency_details(){
        $api_price_details = $this->input->post('api_price_data');
        $api_markup_details = $this->input->post('api_markup_data'); 
       
        $convenience_fees_original = $this->input->post('convenience_fees_original');
       
        $api_price_details = json_decode(base64_decode($api_price_details), true);
        $api_markup_details = json_decode(base64_decode($api_markup_details), true);
         // debug($api_markup_details);exit;
        $convenience_fees_original = json_decode(base64_decode($convenience_fees_original), true);
       
        $offered_fare = $api_price_details['TotalDisplayFare']-$api_price_details['PriceBreakup']['AgentCommission']+$api_price_details['PriceBreakup']['AgentTdsOnCommision'];

        
        $markup_price = 0;
        $currency_obj = new Currency(array('module_type' => 'flight', 'from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
        $converted_tax = get_converted_currency_value($currency_obj->force_currency_conversion($api_price_details['PriceBreakup']['Tax']));


        
        $converted_offered_fare = get_converted_currency_value($currency_obj->force_currency_conversion($offered_fare));
        $converted_agent_commission = get_converted_currency_value($currency_obj->force_currency_conversion($api_price_details['PriceBreakup']['AgentCommission']));
        $converted_agent_tds = get_converted_currency_value($currency_obj->force_currency_conversion($api_price_details['PriceBreakup']['AgentTdsOnCommision']));
        $converted_agent_tds = get_converted_currency_value($currency_obj->force_currency_conversion($api_price_details['PriceBreakup']['AgentTdsOnCommision']));
       

        if(empty($api_markup_details['markup_type']) == false){
            if($api_markup_details['markup_type']=='plus'){
                $markup_value = $api_markup_details['original_markup'];
                $markup_price = get_converted_currency_value($currency_obj->force_currency_conversion($markup_value));
            }
            else{
                $markup_price = ($converted_offered_fare/100)*$api_markup_details['original_markup'];
                $markup_price = number_format($markup_price, 2, '.', '');
            }
        }
        $total_fare_before_commission = ((floatval($markup_price)+floatval(@$converted_offered_fare) + $converted_agent_commission + $converted_agent_tds));
       
        if(valid_array($convenience_fees_original)){
            if($convenience_fees_original['type'] == 'plus'){
                $converted_convience_fee = get_converted_currency_value($currency_obj->force_currency_conversion($convenience_fees_original['value']));
            }
            else{
             //percentage conience fee
                $converted_convience_fee = (($total_fare_before_commission / 100) * $convenience_fees_original['value']);
            }
            $converted_convience_fee = roundoff_number($converted_convience_fee);
        }
        $total_tax = ((floatval($markup_price) + floatval(@$converted_tax) - $converted_agent_commission + $converted_agent_tds));
        $total_fare = ((floatval($converted_convience_fee)+floatval($markup_price)+floatval(@$converted_offered_fare) + $converted_agent_commission + $converted_agent_tds));

        foreach($api_price_details['PassengerBreakup'] as $pass_key => $passenger_price){
            $base_price[$pass_key] = get_converted_currency_value($currency_obj->force_currency_conversion($passenger_price['BasePrice']));
        }
       
        $response['TotalTax'] = $total_tax;
        $response['TotalFare'] = $total_fare;
        $response['PassngerBasePrice'] = $base_price;
        $response['convience_fee'] = $converted_convience_fee;
        header('Content-type:application/json');
        echo json_encode($response);
      
    }
   
}
