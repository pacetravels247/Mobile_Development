<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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
	public function __construct()
	{
		parent::__construct();
		if (is_ajax() == false) {
			//$this->index();
		}
		ob_start();
		$this->load->model('flight_model');
		$this->load->model('car_model');  
		$this->load->model('module_model'); 
		$this->load->model('hotel_model');
		$this->load->model('sightseeing_model');
		$this->load->model('transferv1_model');
		$this->current_module = $this->config->item('current_module');
	}

	/**
	 * index page of application will be loaded here
	 */
	function index()
	{

	}

	/**
	 * get city list based on country
	 * @param $country_id
	 * @param $default_select
	 */
	function get_city_list($country_id=0, $default_select=0)
	{
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
	function get_country_list($continent_id=array(), $default_select=0,$zone_id=0)
	{
		$this->load->model('general_model');
		$continent_id=urldecode($continent_id);
		if (intval($continent_id) != 0) {
			$option_list = $this->general_model->get_country_list($continent_id,$zone_id);
			if (valid_array($option_list['data'])) {
				echo get_compressed_output(generate_options($option_list['data'], array($default_select)));
			}
		}
	}

	/**
	 *Get Location List
	 */
	function location_list($limit=AUTO_SUGGESTION_LIMIT)
	{
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
	 *Get Location List
	 */
	function city_list($limit=AUTO_SUGGESTION_LIMIT)
	{
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
	function get_currency_value($currency_origin=0)
	{
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

	/*
	 *
	 * Flight(Airport) auto suggest
	 *
	 */
	function get_airport_code_list()
	{
		$term = $this->input->get('term'); //retrieve the search term that autocomplete sends
		$term = trim(strip_tags($term));
		$result = array();
		$flagpath = DOMAIN_LAG_IMAGE_DIR.'/'; 
		
		$__airports = $this->flight_model->get_airport_list($term)->result();
		if (valid_array($__airports) == false) {
			$__airports = $this->flight_model->get_airport_list('')->result();
		}
		$airports = array();
		foreach($__airports as $airport){
			$airports['label'] = $airport->airport_city.', '.$airport->country.' ('.$airport->airport_code.')';
			$airports['value'] = $airport->airport_city.' ('.$airport->airport_code.')';
			$airports['id'] = $airport->origin;
			$airports['country_code'] = $flagpath.strtolower($airport->CountryCode).'.png'; 
			if (empty($airport->top_destination) == false) {
				$airports['category'] = 'Top cities';
				$airports['type'] = 'Top cities';
			} else {
				$airports['category'] = 'Search Results';
				$airports['type'] = 'Search Results';
			}

			array_push($result,$airports);
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
            $airports['country_id'] = $airport->Country_ISO;
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
        // debug($result);exit;   
        $this->output_compressed_data($result);
    }
	/*
	 *
	 * Hotels City auto suggest
	 *
	 */
	function get_hotel_city_list()
	{
		$this->load->model('hotel_model');
		$term = $this->input->get('term'); //retrieve the search term that autocomplete sends
		$term = trim(strip_tags($term));
		$data_list = $this->hotel_model->get_hotel_city_list($term);
		if (valid_array($data_list) == false) {
			$data_list = $this->hotel_model->get_hotel_city_list('');
		}

		$suggestion_list = array();
		$result = array();
		foreach ( $data_list as $city_list ) {
			// remove later when table done
			$suggestion_list ['label'] = $city_list ['city_name'] . ', ' . $city_list ['country_name'] . '';
			$suggestion_list ['value'] = hotel_suggestion_value ( $city_list ['city_name'], $city_list ['country_name'] );
			$suggestion_list ['id'] = $city_list ['origin'];
			$suggestion_list ['grn_city_id'] = $city_list ['grn_city_id'];
			$suggestion_list ['grn_destination_id'] = $city_list ['grn_destination_id'];
			// added
			$suggestion_list ['rz_city'] = $city_list ['rz_city_id'];
			$suggestion_list ['rz_country'] = $city_list ['country_code'];
			$suggestion_list ['oyo_city'] = $city_list ['oyo_city'];
			if (empty ( $city_list ['top_destination'] ) == false) {
				$suggestion_list ['category'] = 'Top cities';
				$suggestion_list ['type'] = 'Top cities';
			} else {
				$suggestion_list ['category'] = 'Search Results';
				$suggestion_list ['type'] = 'Search Results';
			}
			// if (intval ( $city_list ['cache_hotels_count'] ) > 0) {
			// 	$suggestion_list ['count'] = $city_list ['cache_hotels_count'];
			// } else {
			// 	$suggestion_list ['count'] = 0;
			// }
			$result [] = $suggestion_list;
		}
		$this->output_compressed_data($result);
	}

	/**
	 * Auto Suggestion for bus stations
	 */
	function bus_stations()
	{
		$this->load->model('bus_model');
		$term = $this->input->get('term'); //retrieve the search term that autocomplete sends
		$term = trim(strip_tags($term));
		$data_list = $this->bus_model->get_bus_station_list($term);
		if (valid_array($data_list) == false) {
			$data_list = $this->bus_model->get_bus_station_list('');
		}
		$suggestion_list = array();
		$result = array();
		foreach($data_list as $city_list){
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
	 * Load hotels from different source
	 */
	function hotel_list($offset=0)
	{
		/*error_reporting(E_ALL);
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);*/
		$response['data'] = '';
		$response['msg'] = '';
		$response['status'] = FAILURE_STATUS;
		$search_params = $this->input->get();
		$limit = $this->config->item('hotel_per_page_limit');
		//debug($search_params);die();
		if ($search_params['op'] == 'load' && intval($search_params['search_id']) > 0 && isset($search_params['booking_source']) == true) {
			load_hotel_lib($search_params['booking_source']);
			switch($search_params['booking_source']) {
				case PROVAB_HOTEL_BOOKING_SOURCE :
					//Meaning hotels are loaded first time
					$raw_hotel_list = $this->hotel_lib->get_hotel_list(abs($search_params['search_id']));
					//debug($raw_hotel_list);die('ajax-tmx');
					if ($raw_hotel_list['status']) {
						//Converting API currency data to preferred currency
						$currency_obj = new Currency(array('module_type' => 'hotel','from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
						$raw_hotel_list = $this->hotel_lib->search_data_in_preferred_currency($raw_hotel_list, $currency_obj,$search_params['search_id']);
						//Display 
						$currency_obj = new Currency(array('module_type' => 'hotel', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
						//Update currency and filter summary appended
						if (isset($search_params['filters']) == true and valid_array($search_params['filters']) == true) {
							$filters = $search_params['filters'];
						} else {
							$filters = array();
						}

						$raw_hotel_list['data'] = $this->hotel_lib->format_search_response($raw_hotel_list['data'], $currency_obj, $search_params['search_id'], 'b2b', $filters);
						
						$source_result_count = $raw_hotel_list['data']['source_result_count'];
						$filter_result_count = $raw_hotel_list['data']['filter_result_count'];
						if (intval($offset) == 0) {
							//Need filters only if the data is being loaded first time
							$filters = $this->hotel_lib->filter_summary($raw_hotel_list['data']);
							$response['filters'] = $filters['data'];
						}
						$raw_hotel_list['data'] = $this->hotel_lib->get_page_data($raw_hotel_list['data'], $offset, $limit);

						$attr['search_id'] = abs($search_params['search_id']);
						//debug($raw_hotel_list);exit;

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
				case REZLIVE_HOTEL :
					//Meaning hotels are loaded first time
					$raw_hotel_list = $this->hotel_lib->get_hotel_list(abs($search_params['search_id']),'b2b');
					//debug($raw_hotel_list);die('ajax-rez');
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

						$raw_hotel_list['data'] = $this->hotel_lib->format_search_response($raw_hotel_list['data'], $currency_obj, $search_params['search_id'], 'b2b', $filters);
						
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
				case REZLIVE_HOTEL1:
					$currency_obj = new Currency ( array (
							'module_type' => 'hotel',
							'from' => get_application_default_currency (),
							'to' => get_application_display_currency_preference () 
					) );
						// Meaning hotels are loaded first time
					$raw_hotel_list = $this->hotel_lib->get_hotel_list ( abs ( $search_params ['search_id'] ), 'b2b'  );
					//debug($raw_hotel_list);exit('ajax search');
					if ($raw_hotel_list ['status']) {
						// Update currency and filter summary appended
						if (isset ( $search_params ['filters'] ) == true and valid_array ( $search_params ['filters'] ) == true) {
							$filters = $search_params ['filters'];
						} else {
							$filters = array ();
						}
						$raw_hotel_list ['data'] = $this->hotel_lib->format_search_response ( $raw_hotel_list ['data'], $currency_obj, $search_params ['search_id'], 'b2b', $filters );
						// debug($raw_hotel_list);exit;
						$source_result_count = $raw_hotel_list ['data'] ['source_result_count'];
						$filter_result_count = $raw_hotel_list ['data'] ['filter_result_count'];
						if (intval ( $offset ) == 0) {
							// Need filters only if the data is being loaded first time
							$filters = $this->hotel_lib->filter_summary ( $raw_hotel_list ['data'] );
							$response ['filters'] = $filters ['data'];
						}
						
						$attr ['search_id'] = abs ( $search_params ['search_id'] );
						// debug($raw_hotel_list ['data']);exit;
						$attr ['search_id'] = abs($search_params ['search_id']);
		                $response ['data'] = $this->template->isolated_view('hotel/core_search_result', array(
		                    'currency_obj' => $currency_obj,
		                    'raw_hotel_list' => $raw_hotel_list ['data'],
		                    'search_id' => $search_params ['search_id'],
		                    'booking_source' => $search_params ['booking_source'],
		                    'attr' => $attr
		                        ));

		                $response ['status'] = SUCCESS_STATUS;
		                $response ['total_result_count'] = $source_result_count;
		                $response ['filter_result_count'] = $filter_result_count;
		                $response ['offset'] = $offset + $limit;
						
					}
					break;
			}
		}
		$this->output_compressed_data($response);
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
          // debug($search_params);exit;
        if ($search_params['op'] == 'load' && intval($search_params['search_id']) > 0 && isset($search_params['booking_source']) == true) {
          	load_car_lib($search_params['booking_source']);
			switch ($search_params['booking_source']) {
                case PROVAB_CAR_BOOKING_SOURCE :
                    //getting search params from table
                    $safe_search_data = $this->car_model->get_safe_search_data($search_params['search_id']);
                    //Meaning hotels are loaded first time
                    $raw_car_list = $this->car_lib->get_car_list(abs($search_params['search_id']));
                    // debug($raw_car_list);
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
                        $raw_car_list['data'] = $this->car_lib->format_search_response($raw_car_list['data'], $currency_obj, $search_params['search_id'], 'b2b', $filters);
                        // debug($raw_car_list);exit;
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
	/**
	 * Compress and output data
	 * @param array $data
	 */
	private function output_compressed_data($data)
	{
		while (ob_get_level() > 0) { ob_end_clean() ; }
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
	private function output_compressed_data_flight($data)
	{
	
	   while (ob_get_level() > 0) { ob_end_clean() ; }
	   ob_start("ob_gzhandler");
	   ini_set("memory_limit", "-1");set_time_limit(0);
	   header('Content-type:application/json');
           echo  json_encode($data, JSON_UNESCAPED_SLASHES);
	   ob_end_flush();
	   exit;
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
                    if(isset($search_params['cate_id'])){
                        $category_id = $search_params['cate_id'];
                    }else{
                        // if($safe_search_data['data']['category_id']){
                        //     $category_id = $safe_search_data['data']['category_id'];        
                        // }else{
                           
                        // }
                         $category_id = 0;
                    }
                    if(isset($search_params['sub_cate'])){
                        $sub_cate_id = $search_params['sub_cate'];
                    }else{
                        $sub_cate_id = 0;
                    }
                    if(isset($search_params['price_sort'])){
                        $price_sort = $search_params['price_sort'];
                    }else{
                        $price_sort = '';
                    }
                    if(isset($search_params['tour_name'])){
                        $tour_name = $search_params['tour_name'];
                    }else{
                        $tour_name = '';
                    }
                    if(isset($search_params['action'])){
                        if($search_params['action']=='reset'){
                            $category_id=0;
                            $sub_cate_id=0;
                            $price_sort='';
                            $tour_name='';
                          //  $safe_search_data['category_id'] = 0;
                        }
                    }

                    $search_data['category_id'] = $category_id;
                    $search_data['sub_cate_id'] = $sub_cate_id;
                    $search_data['price_sort'] = $price_sort;
                    $search_data['tour_name'] = $tour_name;
                    $raw_sightseeing_result = $this->sightseeing_lib->get_sightseeing_list($safe_search_data,$search_data);

                    if ($raw_sightseeing_result['status']) {
                        //Converting API currency data to preferred currency
                        $currency_obj = new Currency(array('module_type' => 'sightseeing', 'from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
                        $raw_sightseeing_result = $this->sightseeing_lib->search_data_in_preferred_currency($raw_sightseeing_result, $currency_obj,'b2b');
                        

                        //Display 
                        $currency_obj = new Currency(array('module_type' => 'sightseeing', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));

                       
                        //Update currency and filter summary appended
                        if (isset($search_params['filters']) == true and valid_array($search_params['filters']) == true) {
                            $filters = $search_params['filters'];
                        } else {
                            $filters = array();
                        }
                        //debug($raw_hotel_list);exit;
                        $raw_sightseeing_result['data'] = $this->sightseeing_lib->format_search_response($raw_sightseeing_result['data'], $currency_obj, $search_params['search_id'], 'b2b', $filters);

                        
                        $source_result_count = $raw_sightseeing_result['data']['source_result_count'];
                        $filter_result_count = $raw_sightseeing_result['data']['filter_result_count'];
                        //debug($raw_hotel_list);exit;
                        if (intval($offset) == 0) {
                            //Need filters only if the data is being loaded first time
                            $filters = $this->sightseeing_lib->filter_summary($raw_sightseeing_result['data']);
                            $response['filters'] = $filters['data'];
                        }
                        //debug($raw_hotel_list['data']);exit;
                      
                        // $raw_sightseeing_result['data'] = $this->sightseeing_lib->get_page_data($raw_sightseeing_result['data'], $offset, $limit);

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
         /*debug($search_params);
         exit;*/
        $safe_search_data = $this->transferv1_model->get_safe_search_data($search_params['search_id'],META_TRANSFERV1_COURSE);
        /*debug($safe_search_data);
         exit;*/
        $limit = $this->config->item('transferv1_page_limit');

        if ($search_params['op'] == 'load' && intval($search_params['search_id']) > 0 && isset($search_params['booking_source']) == true) {
            load_transferv1_lib($search_params['booking_source']);

            switch($search_params['booking_source']) {

                case PROVAB_TRANSFERV1_BOOKING_SOURCE :
               
                    $raw_sightseeing_result = $this->transferv1_lib->get_transfer_list($safe_search_data);

                    if ($raw_sightseeing_result['status']) {
                        //Converting API currency data to preferred currency
                        $currency_obj = new Currency(array('module_type' => 'transferv1', 'from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
                        $raw_sightseeing_result = $this->transferv1_lib->search_data_in_preferred_currency($raw_sightseeing_result, $currency_obj,'b2b');                       
                      	// debug($raw_sightseeing_result);
                      	// exit;
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
                        $raw_sightseeing_result['data'] = $this->transferv1_lib->format_search_response($raw_sightseeing_result['data'], $currency_obj, $search_params['search_id'], 'b2b', $filters);

                        
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
   
	function bus_list()
	{
		$this->load->driver('cache');
		$response['data'] = '';
		$response['msg'] = '';
		$response['status'] = FAILURE_STATUS;
		$search_params = $this->input->get();
        $this->load->model('bus_model');
        $search_data = $this->bus_model->get_search_data($search_params['search_id']);
        //debug($search_params);die();
        $api_count = $search_params['api_count'];
        $search_data = json_decode($search_data['search_data'], true);
		if ($search_params['op'] == 'load' && intval($search_params['search_id']) > 0 && isset($search_params['booking_source']) == true) {
			load_bus_lib($search_params['booking_source']);

			switch($search_params['booking_source']) {
				case PROVAB_BUS_BOOKING_SOURCE :
					$raw_bus_list = $this->bus_lib->get_bus_list(abs($search_params['search_id']));
  					  //debug($raw_bus_list);exit; 
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
						$formatted_search_data = $this->bus_lib->format_search_response($raw_bus_list, $currency_obj, $search_params['search_id'], 'bus','B2B');
						//debug($formatted_search_data);exit;
						// debug($formatted_search_data);exit;
						//Display Bus List
						$currency_obj = new Currency(array('module_type' => 'bus', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
						$raw_bus_list = force_multple_data_format($raw_bus_list['data']['result']);
						
						//Update commission
						// $raw_bus_list = $this->bus_lib->update_bus_search_commission($raw_bus_list, $currency_obj);
						// echo 'herre';
						
						$response['segment']['data'] = get_compressed_output(
						$this->template->isolated_view('bus/travelyaari/travelyaari_search_result',
						array('currency_obj' => $currency_obj, 'raw_bus_list' => $formatted_search_data, 'search_id' => $search_params['search_id'], 'booking_source' => $search_params['booking_source'],'search_data_city' => $search_data_city))
						);
						$response['status'] = SUCCESS_STATUS;
						
					}
					$response['api_count'] = $api_count;
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
						$formatted_search_data = $this->bus_lib->format_search_response($raw_bus_list, $currency_obj, $search_params['search_id'], 'bus','B2B');
						// debug($formatted_search_data);exit;
						//Display Bus List
						$currency_obj = new Currency(array('module_type' => 'bus', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
						$raw_bus_list = force_multple_data_format($raw_bus_list['data']['result']);
						
						//Update commission
						// $raw_bus_list = $this->bus_lib->update_bus_search_commission($raw_bus_list, $currency_obj);
						// echo 'herre';
						
						$response['segment']['data'] = get_compressed_output(
						$this->template->isolated_view('bus/travelyaari/travelyaari_search_result',
						array('currency_obj' => $currency_obj, 'raw_bus_list' => $formatted_search_data, 'search_id' => $search_params['search_id'], 'booking_source' => $search_params['booking_source'],'search_data_city' => $search_data_city))
						);
						$response['status'] = SUCCESS_STATUS;
						
					}
					$response['api_count'] = $api_count;
				break;

				case BITLA_BUS_BOOKING_SOURCE :
					$raw_bus_list = $this->bus_lib->get_bus_list(abs($search_params['search_id']));
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
						$formatted_search_data = $this->bus_lib->format_search_response($raw_bus_list, $currency_obj, $search_params['search_id'], 'bus','B2B');
						$formatted_search_data = $this->removeSpecificOperators($formatted_search_data, BITLA_BUS_BOOKING_SOURCE);
												
						//save to cache for compare with ets api
						$compare_data = json_encode($formatted_search_data);
						$unique_hash = $search_params['search_id'].'-'.BITLA_BUS_BOOKING_SOURCE; 
						$cache_exp = 300;
						$this->cache->file->save($unique_hash,$compare_data, $cache_exp);
						//Display Bus List
						$currency_obj = new Currency(array('module_type' => 'bus', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
						$raw_bus_list = force_multple_data_format($raw_bus_list['data']['result']);
						
						//Update commission
						// $raw_bus_list = $this->bus_lib->update_bus_search_commission($raw_bus_list, $currency_obj);
						// echo 'herre';
						
						$response['segment']['data'] = get_compressed_output(
						$this->template->isolated_view('bus/travelyaari/travelyaari_search_result',
						array('currency_obj' => $currency_obj, 'raw_bus_list' => $formatted_search_data, 'search_id' => $search_params['search_id'], 'booking_source' => $search_params['booking_source'],'search_data_city' => $search_data_city))
						);
						$response['status'] = SUCCESS_STATUS;
						
					}
					$response['api_count'] = $api_count;
				break;
				
				case SRS_BUS_BOOKING_SOURCE :
					$raw_bus_list = $this->bus_lib->get_bus_list(abs($search_params['search_id']));
					//debug($raw_bus_list); exit;
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
						$formatted_search_data = $this->bus_lib->format_search_response($raw_bus_list, $currency_obj, $search_params['search_id'], 'bus','B2B');
						
						//save to cache for compare with ets api
						$compare_data = json_encode($formatted_search_data);
						$unique_hash = $search_params['search_id'].'-'.SRS_BUS_BOOKING_SOURCE;
						$cache_exp = 300;
						$this->cache->file->save($unique_hash,$compare_data, $cache_exp);
						//Display Bus List
						$formatted_search_data = $this->showSpecificOperators($formatted_search_data, SRS_BUS_BOOKING_SOURCE);
						$currency_obj = new Currency(array('module_type' => 'bus', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
						$raw_bus_list = force_multple_data_format($raw_bus_list['data']['result']);
						//debug($formatted_search_data); exit;
						//Update commission
						// $raw_bus_list = $this->bus_lib->update_bus_search_commission($raw_bus_list, $currency_obj);
						// echo 'herre';
						
						$response['segment']['data'] = get_compressed_output(
						$this->template->isolated_view('bus/travelyaari/travelyaari_search_result',
						array('currency_obj' => $currency_obj, 'raw_bus_list' => $formatted_search_data, 'search_id' => $search_params['search_id'], 'booking_source' => $search_params['booking_source'],'search_data_city' => $search_data_city))
						);
						$response['status'] = SUCCESS_STATUS;
						
					}
					$response['api_count'] = $api_count;
				break;
				
				case ETS_BUS_BOOKING_SOURCE :
					$raw_bus_list = $this->bus_lib->get_bus_list(abs($search_params['search_id']));
  					//debug($raw_bus_list);exit('ajax'); 
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
						$formatted_search_data = $this->bus_lib->format_search_response($raw_bus_list, $currency_obj, $search_params['search_id'], 'bus','B2B');

						//save to cache for compare with bitla api
						/*  $path = $search_params['search_id'].'-'.BITLA_BUS_BOOKING_SOURCE;
						 if(file_exists('application/cache/'.$path)){
						 	$compare_data = $this->cache->file->get($path);
						 	$formatted_search_data = $this->bitla_priority($formatted_search_data,$compare_data);
						 }
						 $formatted_search_data = $this->removeSpecificOperators($formatted_search_data, ETS_BUS_BOOKING_SOURCE); */

						/*=========================== Shrikant ===============================*/
						$ets_block_buses = $this->bus_model->get_ets_display_buses();
						$formatted_search_data = $this->display_ets_opertors($formatted_search_data, $ets_block_buses);
						/*====================================================================*/


						//Display Bus List
						$currency_obj = new Currency(array('module_type' => 'bus', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
						$raw_bus_list = force_multple_data_format($raw_bus_list['data']['result']);
						
						//Update commission
						// $raw_bus_list = $this->bus_lib->update_bus_search_commission($raw_bus_list, $currency_obj);
						// echo 'herre';
						
						$response['segment']['data'] = get_compressed_output(
						$this->template->isolated_view('bus/travelyaari/travelyaari_search_result',
						array('currency_obj' => $currency_obj, 'raw_bus_list' => $formatted_search_data, 'search_id' => $search_params['search_id'], 'booking_source' => $search_params['booking_source'],'search_data_city' => $search_data_city))
						);
						$response['status'] = SUCCESS_STATUS;
						
					}
					$response['api_count'] = $api_count;
				break;

				case KUKKESHREE_BUS_BOOKING_SOURCE :
					$raw_bus_list = $this->bus_lib->get_bus_list(abs($search_params['search_id']));
  					$from_id = @$raw_bus_list['data']['result'][0]['From'];
					$to_id = @$raw_bus_list['data']['result'][0]['To'];
					$form_data = $this->bus_model->get_bus_station_data($search_data['from_station_id']);
				        $to_data = $this->bus_model->get_bus_station_data($search_data['to_station_id']);
					$search_data_city = array('from_id' => $form_data->station_id,'to_id' => $to_data->station_id);
                    
                    if ($raw_bus_list['status']) {
						//Converting API currency data to preferred currency
						$currency_obj = new Currency(array('module_type' => 'bus','from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
						$raw_bus_list = $this->bus_lib->search_data_in_preferred_currency($raw_bus_list, $currency_obj);
						 // debug($raw_bus_list);exit; 
						$formatted_search_data = $this->bus_lib->format_search_response($raw_bus_list, $currency_obj, $search_params['search_id'], 'bus','B2B');

						//save to cache for compare with bitla api
						/*$path = $search_params['search_id'].'-'.KUKKESHREE_BUS_BOOKING_SOURCE;
						if(file_exists('application/cache/'.$path)){
							$compare_data = $this->cache->file->get($path);
							$formatted_search_data = $this->bitla_priority($formatted_search_data,$compare_data);
						}*/
						//Display Bus List
						$currency_obj = new Currency(array('module_type' => 'bus', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
						$raw_bus_list = force_multple_data_format($raw_bus_list['data']['result']);
						
						
						$response['segment']['data'] = get_compressed_output(
						$this->template->isolated_view('bus/travelyaari/travelyaari_search_result',
						array('currency_obj' => $currency_obj, 'raw_bus_list' => $formatted_search_data, 'search_id' => $search_params['search_id'], 'booking_source' => $search_params['booking_source'],'search_data_city' => $search_data_city))
						);
						$response['status'] = SUCCESS_STATUS;
						
					}	
					$response['api_count'] = $api_count;
				break;

				case KRL_BUS_BOOKING_SOURCE :
					$raw_bus_list = $this->bus_lib->get_bus_list(abs($search_params['search_id']));
  					//debug($raw_bus_list);exit('Ajax'); 
  					$from_id = @$raw_bus_list['data']['result'][0]['From'];
					$to_id = @$raw_bus_list['data']['result'][0]['To'];
					$form_data = $this->bus_model->get_bus_station_data($search_data['from_station_id']);
				        $to_data = $this->bus_model->get_bus_station_data($search_data['to_station_id']);
					$search_data_city = array('from_id' => $form_data->station_id,'to_id' => $to_data->station_id);
                    
                    if ($raw_bus_list['status']) {
						//Converting API currency data to preferred currency
						$currency_obj = new Currency(array('module_type' => 'bus','from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
						$raw_bus_list = $this->bus_lib->search_data_in_preferred_currency($raw_bus_list, $currency_obj);
						 // debug($raw_bus_list);exit; 
						$formatted_search_data = $this->bus_lib->format_search_response($raw_bus_list, $currency_obj, $search_params['search_id'], 'bus','B2B');

						//Display Bus List
						$currency_obj = new Currency(array('module_type' => 'bus', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
						$raw_bus_list = force_multple_data_format($raw_bus_list['data']['result']);
						
						
						$response['segment']['data'] = get_compressed_output(
						$this->template->isolated_view('bus/travelyaari/travelyaari_search_result',
						array('currency_obj' => $currency_obj, 'raw_bus_list' => $formatted_search_data, 'search_id' => $search_params['search_id'], 'booking_source' => $search_params['booking_source'],'search_data_city' => $search_data_city))
						);
						$response['status'] = SUCCESS_STATUS;

					}	
					$response['api_count'] = $api_count;
				break;
				case INFINITY_BUS_BOOKING_SOURCE :				
					$raw_bus_list = $this->bus_lib->get_bus_list(abs($search_params['search_id']));
  					$from_id = @$raw_bus_list['data']['result'][0]['From'];
					$to_id = @$raw_bus_list['data']['result'][0]['To'];
					$form_data = $this->bus_model->get_bus_station_data($search_data['from_station_id']);
				        $to_data = $this->bus_model->get_bus_station_data($search_data['to_station_id']);
					$search_data_city = array('from_id' => $form_data->station_id,'to_id' => $to_data->station_id);
                    
                    if ($raw_bus_list['status']) {
						//Converting API currency data to preferred currency
						$currency_obj = new Currency(array('module_type' => 'bus','from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
						$raw_bus_list = $this->bus_lib->search_data_in_preferred_currency($raw_bus_list, $currency_obj);

						$formatted_search_data = $this->bus_lib->format_search_response($raw_bus_list, $currency_obj, $search_params['search_id'], 'bus','B2B');

						//Display Bus List
						$currency_obj = new Currency(array('module_type' => 'bus', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
						$raw_bus_list = force_multple_data_format($raw_bus_list['data']['result']);
						
						
						$response['segment']['data'] = get_compressed_output(
						$this->template->isolated_view('bus/travelyaari/travelyaari_search_result',
						array('currency_obj' => $currency_obj, 'raw_bus_list' => $formatted_search_data, 'search_id' => $search_params['search_id'], 'booking_source' => $search_params['booking_source'],'search_data_city' => $search_data_city))
						);
						$response['status'] = SUCCESS_STATUS;
					}	
				break;
				case BITLA_BUS_DIRECT_OPERATORS_BOOKING_SOURCE :	
					$raw_bus_list = array(); $raw_bus_list_gotour = ''; $raw_bus_list_array = array();
					$get_bitla_direct_operators = $this->bus_model->get_bitla_direct_operators(); // get bitla direct operators
					if(!empty($get_bitla_direct_operators)){
						foreach ($get_bitla_direct_operators as $btkey => $btvalue) {
							if($btvalue['source_id'] == KADRI_BUS_BOOKING_SOURCE && $btvalue['booking_engine_status'] == 1){						
								$raw_bus_list_array[] = $this->bus_lib->get_bus_list(abs($search_params['search_id']), KADRI_BUS_BOOKING_SOURCE);	
							}
							if($btvalue['source_id'] == GOTOUR_BUS_BOOKING_SOURCE && $btvalue['booking_engine_status'] == 1){						
								$raw_bus_list_array[] = $this->bus_lib->get_bus_list(abs($search_params['search_id']), GOTOUR_BUS_BOOKING_SOURCE);	
							}
							if($btvalue['source_id'] == KONDUSKAR_BUS_BOOKING_SOURCE && $btvalue['booking_engine_status'] == 1){						
								$raw_bus_list_array[] = $this->bus_lib->get_bus_list(abs($search_params['search_id']), KONDUSKAR_BUS_BOOKING_SOURCE);	
							}
							if($btvalue['source_id'] == BARDE_BUS_BOOKING_SOURCE && $btvalue['booking_engine_status'] == 1){						
								$raw_bus_list_array[] = $this->bus_lib->get_bus_list(abs($search_params['search_id']), BARDE_BUS_BOOKING_SOURCE);	
							}
						}
					}
					if(!empty($raw_bus_list_array)){
						foreach ($raw_bus_list_array as $rawkey => $rawvalue) {
							if($rawvalue['status'] == true){
								foreach ($rawvalue['data']['result'] as $rawkey1 => $rawvalue1) {
									$raw_bus_list['data']['result'][] = $rawvalue1;	
								}
							}
						}
					}
					if(!empty($raw_bus_list)){
						$raw_bus_list['status'] = true;
					}else{
						$raw_bus_list['data']['result'] = array();
						$raw_bus_list['status'] = false;
					}								
  					$from_id = @$raw_bus_list['data']['result'][0]['From'];
					$to_id = @$raw_bus_list['data']['result'][0]['To'];
					$form_data = $this->bus_model->get_bus_station_data($search_data['from_station_id']);
				    $to_data = $this->bus_model->get_bus_station_data($search_data['to_station_id']);
					$search_data_city = array('from_id' => $form_data->station_id,'to_id' => $to_data->station_id);

                    if ($raw_bus_list['status']) {
						//Converting API currency data to preferred currency
						$currency_obj = new Currency(array('module_type' => 'bus','from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
						$raw_bus_list = $this->bus_lib->search_data_in_preferred_currency($raw_bus_list, $currency_obj);

						$formatted_search_data = $this->bus_lib->format_search_response($raw_bus_list, $currency_obj, $search_params['search_id'], 'bus','B2B');

						//Display Bus List
						$currency_obj = new Currency(array('module_type' => 'bus', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
						$raw_bus_list = force_multple_data_format($raw_bus_list['data']['result']);
						
						
						$response['segment']['data'] = get_compressed_output(
						$this->template->isolated_view('bus/travelyaari/travelyaari_search_result',
						array('currency_obj' => $currency_obj, 'raw_bus_list' => $formatted_search_data, 'search_id' => $search_params['search_id'], 'booking_source' => $search_params['booking_source'],'search_data_city' => $search_data_city))
						);
						$response['status'] = SUCCESS_STATUS;
					}	
				break;
			}


			$response['status'] = SUCCESS_STATUS;
		}
		//debug($response);die('789');
		$this->output_compressed_data($response);
	}
	/**
	 * Load hotels from different source
	 */
	function bus_list_old()
	{
		$response['data'] = '';
		$response['msg'] = '';
		$response['status'] = FAILURE_STATUS;
		$search_params = $this->input->get();
		/*$search_params['op'] = 'load';
		 $search_params['search_id'] = 2461;
		 $search_params['booking_source'] = PROVAB_BUS_BOOKING_SOURCE;*/
		if ($search_params['op'] == 'load' && intval($search_params['search_id']) > 0 && isset($search_params['booking_source']) == true) {
			load_bus_lib($search_params['booking_source']);
			switch($search_params['booking_source']) {
				case PROVAB_BUS_BOOKING_SOURCE :
					$raw_bus_list = $this->bus_lib->get_bus_list(abs($search_params['search_id']));
					if ($raw_bus_list['status']) {
						//Converting API currency data to preferred currency
						$currency_obj = new Currency(array('module_type' => 'bus','from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
						$raw_bus_list = $this->bus_lib->search_data_in_preferred_currency($raw_bus_list, $currency_obj);
						//Display Bus List
						$currency_obj = new Currency(array('module_type' => 'bus', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
						$raw_bus_list = force_multple_data_format($raw_bus_list['data']['result']);
						//Update commission
						$raw_bus_list = $this->bus_lib->update_bus_search_commission($raw_bus_list, $currency_obj);
						$response['data'] = get_compressed_output(
						$this->template->isolated_view('bus/travelyaari/travelyaari_search_result',
						array(
									'currency_obj' => $currency_obj, 'raw_bus_list' => $raw_bus_list,
									'search_id' => $search_params['search_id'], 'booking_source' => $search_params['booking_source']
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

	function get_bus_information()
	{
		$response['data'] = 'No Details Found';
		$response['status'] = false;
		//check params
		$params = $this->input->post();
		/*$params['booking_source'] = 'PTBSID3377337777';
		 $params['journey_date'] = '2015-08-26T23:00:00';
		 $params['route_code'] = '215-9-3-10-23:00';
		 $params['route_schedule_id'] = '22579952';
		 $params['search_id'] = '2471';*/
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
	function get_bus_details($filter_boarding_points=false)
	{
		error_reporting(0);
		$this->load->model('bus_model');
		$response['data'] = 'No Details Found !! Try Later';
		$response['status'] = false;
		//check params
		$params = $this->input->post();
		$page_data["gst_value"] = $params['gst_value'];
		$params = explode('*', $params['route_schedule_id']);
      
        $params['booking_source'] = $params[3];
        $params['search_id'] = $params[1];
        $params['route_schedule_id'] = $params[0];
        $params['route_code'] =  $params[2];
        $search_data = $this->bus_model->get_search_data($params['search_id']);
        //debug($params);exit;
        $search_data = json_decode($search_data['search_data'], true);
        $form_data = $this->bus_model->get_bus_station_data($search_data['from_station_id']);
        $to_data = $this->bus_model->get_bus_station_data($search_data['to_station_id']);
       
		if (empty($params['booking_source']) == false and empty($params['search_id']) == false and intval($params['search_id']) > 0) {
			load_bus_lib($params['booking_source']);
			
			switch ($params['booking_source']) {
				case PROVAB_BUS_BOOKING_SOURCE :
                    $bus_info_data = $this->bus_lib->get_route_details($params['search_id'], $params['route_schedule_id'], $params[2]);
                    //debug($bus_info_data);die('provab');
                    $bus_info_data['bus_data']['Form_id'] = $form_data->station_id;
                    $bus_info_data['bus_data']['To_id'] = $to_data->station_id;
                    
                    $params['journey_date'] = $bus_info_data['bus_data']['DepartureTime'];
                    $params['ResultToken'] = $bus_info_data['bus_data']['ResultToken'];
					$details = $this->bus_lib->get_bus_details($params['route_schedule_id'], $params['journey_date'],$params['route_code'],$params['ResultToken'],$params['booking_source']);
					//debug($details);exit('5555');

					
					if ($details['status'] == SUCCESS_STATUS) {
						//Converting API currency data to preferred currency
						$currency_obj = new Currency(array(
								'module_type' => 'bus',
								'from' => get_api_data_currency(), 
								'to' => get_application_currency_preference()
							));
						$details = $this->bus_lib->seatdetails_in_preferred_currency($details, $bus_info_data['bus_data'], $currency_obj);
						
						$formatted_seat = $this->bus_lib->seat_layout_format($details['data']['result']['result']['value'], $currency_obj,'bus','B2B');
						$details['data']['result']['result']['value'] = $formatted_seat;
						// debug($details);exit;
						//Display Bus Details
						$currency_obj = new Currency(array('module_type' => 'bus', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
						$response['stauts'] = SUCCESS_STATUS;
						$page_data['search_id'] = $params['search_id'];
						$page_data['ResultToken'] = $params['ResultToken'];
						
						$page_data['details'] = $details['data']['result'];
						$page_data['currency_obj'] = $currency_obj;
                        
                        /*debug($filter_boarding_points); 
                        debug($page_data);exit('5555');*/
                        //debug($page_data);exit('pro-5555');                        
						if ($filter_boarding_points == false) {
							$response['data'] = get_compressed_output($this->template->isolated_view('bus/travelyaari/travelyaari_bus_details', $page_data));
						} else {
							$response['data'] = get_compressed_output($this->template->isolated_view('bus/travelyaari/travelyaari_boarding_details', $page_data));
						}
						$response['status'] = SUCCESS_STATUS;
					}
				break;

				case BITLA_BUS_BOOKING_SOURCE :
                    $bus_info_data = $this->bus_lib->get_route_details($params['search_id'], $params['route_schedule_id'], $params[2]);
                    //debug($bus_info_data);die();
                    $bus_info_data['bus_data']['Form_id'] = $form_data->station_id;
                    $bus_info_data['bus_data']['To_id'] = $to_data->station_id;
                    
                    $params['journey_date'] = $bus_info_data['bus_data']['DepartureTime'];
                    $params['ResultToken'] = $bus_info_data['bus_data']['ResultToken'];
					$details = $this->bus_lib->get_bus_details($params['route_schedule_id'], $params['journey_date'],$params['route_code'],$params['ResultToken'],$params['booking_source']);
					//debug($details);exit('Bitla-5555');

					
					if ($details['status'] == SUCCESS_STATUS) {
						//Converting API currency data to preferred currency
						$currency_obj = new Currency(array(
								'module_type' => 'bus',
								'from' => get_api_data_currency(), 
								'to' => get_application_currency_preference()
							));
						$details = $this->bus_lib->seatdetails_in_preferred_currency($details, $bus_info_data['bus_data'], $currency_obj);
						
						$formatted_seat = $this->bus_lib->seat_layout_format($details['data']['result']['result']['value'], $currency_obj,'bus','B2B');
						$details['data']['result']['result']['value'] = $formatted_seat;
						// debug($details);exit;
						//Display Bus Details
						$currency_obj = new Currency(array('module_type' => 'bus', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
						$response['stauts'] = SUCCESS_STATUS;
						$page_data['search_id'] = $params['search_id'];
						$page_data['ResultToken'] = $params['ResultToken'];
						
						$page_data['details'] = $details['data']['result'];
						$page_data['currency_obj'] = $currency_obj;
                        
                        /*debug($filter_boarding_points); 
                        debug($page_data);exit('5555');*/
                        //debug($page_data);exit('bit-5555');                        
						if ($filter_boarding_points == false) {
							$response['data'] = get_compressed_output($this->template->isolated_view('bus/bitla/bitla_bus_details', $page_data));
						} else {
							//debug($this->bus_lib->opts_cache[$bus_info_data['bus_data']['operator_id']."_cancel_policy"]);
							$page_data['details']['result']['Canc']=$bus_info_data['bus_data']['CancPolicy'];
							$response['data'] = get_compressed_output($this->template->isolated_view('bus/travelyaari/travelyaari_boarding_details', $page_data));
						}
						$response['status'] = SUCCESS_STATUS;
					}
				break;
				case SRS_BUS_BOOKING_SOURCE :
                    $bus_info_data = $this->bus_lib->get_route_details($params['search_id'], $params['route_schedule_id'], $params[2]);
                    //debug($bus_info_data);die();
                    $bus_info_data['bus_data']['Form_id'] = $form_data->station_id;
                    $bus_info_data['bus_data']['To_id'] = $to_data->station_id;
                    
                    $params['journey_date'] = $bus_info_data['bus_data']['DepartureTime'];
                    $params['ResultToken'] = $bus_info_data['bus_data']['ResultToken'];
					$details = $this->bus_lib->get_bus_details($params['route_schedule_id'], $params['journey_date'],$params['route_code'],$params['ResultToken'],$params['booking_source']);
					//debug($details);exit('Bitla-5555');

					
					if ($details['status'] == SUCCESS_STATUS) {
						//Converting API currency data to preferred currency
						$currency_obj = new Currency(array(
								'module_type' => 'bus',
								'from' => get_api_data_currency(), 
								'to' => get_application_currency_preference()
							));
						$details = $this->bus_lib->seatdetails_in_preferred_currency($details, $bus_info_data['bus_data'], $currency_obj);
						
						$formatted_seat = $this->bus_lib->seat_layout_format($details['data']['result']['result']['value'], $currency_obj,'bus','B2B');
						$details['data']['result']['result']['value'] = $formatted_seat;
						// debug($details);exit;
						//Display Bus Details
						$currency_obj = new Currency(array('module_type' => 'bus', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
						$response['stauts'] = SUCCESS_STATUS;
						$page_data['search_id'] = $params['search_id'];
						$page_data['ResultToken'] = $params['ResultToken'];
						
						$page_data['details'] = $details['data']['result'];
						$page_data['currency_obj'] = $currency_obj;
                        if( get_client_ip() == '27.59.206.114'){
							// echo debug($page_data);exit;
						}
                        /*debug($filter_boarding_points); 
                        debug($page_data);exit('5555');*/
                        //debug($page_data);exit('bit-5555');                         
						if ($filter_boarding_points == false) {
							$page_data['details']['result']['Canc']=$bus_info_data['bus_data']['CancPolicy'];
							$response['data'] = get_compressed_output($this->template->isolated_view('bus/srs/srs_bus_details', $page_data));
						} else {
							$page_data['details']['result']['Canc']=$bus_info_data['bus_data']['CancPolicy'];
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
						
						$formatted_seat = $this->bus_lib->seat_layout_format($details['data']['result']['result']['value'], $currency_obj,'bus','B2B');
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
						$formatted_seat = $this->bus_lib->seat_layout_format($details['data']['result']['result']['value'], $currency_obj,'bus','B2B');

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

				case KUKKESHREE_BUS_BOOKING_SOURCE :
                    $bus_info_data = $this->bus_lib->get_route_details($params['search_id'], $params['route_schedule_id'], $params[2]);
                    //debug($bus_info_data);die('777');
                    $bus_info_data['bus_data']['Form_id'] = $form_data->station_id;
                    $bus_info_data['bus_data']['To_id'] = $to_data->station_id;
                    
                    $params['journey_date'] = $bus_info_data['bus_data']['DepartureTime'];
                    $params['ResultToken'] = $bus_info_data['bus_data']['ResultToken'];
					$details = $this->bus_lib->get_bus_details($params['route_schedule_id'], $params['journey_date'],$params['route_code'],$params['ResultToken'],$params['booking_source']);
					//debug($details);exit('KUKKESHREE-5555');

					
					if ($details['status'] == SUCCESS_STATUS) {
						//Converting API currency data to preferred currency
						$currency_obj = new Currency(array(
								'module_type' => 'bus',
								'from' => get_api_data_currency(), 
								'to' => get_application_currency_preference()
							));
						$details = $this->bus_lib->seatdetails_in_preferred_currency($details, $bus_info_data['bus_data'], $currency_obj);
						
						$formatted_seat = $this->bus_lib->seat_layout_format($details['data']['result']['result']['value'], $currency_obj,'bus','B2B');
						$details['data']['result']['result']['value'] = $formatted_seat;
						// debug($details);exit;
						//Display Bus Details
						$currency_obj = new Currency(array('module_type' => 'bus', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
						$response['stauts'] = SUCCESS_STATUS;
						$page_data['search_id'] = $params['search_id'];
						$page_data['ResultToken'] = $params['ResultToken'];
						
						$page_data['details'] = $details['data']['result'];
						$page_data['currency_obj'] = $currency_obj;
                        
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
                    //debug($bus_info_data);die('777');
                    $bus_info_data['bus_data']['Form_id'] = $form_data->station_id;
                    $bus_info_data['bus_data']['To_id'] = $to_data->station_id;
                    
                    $params['journey_date'] = $bus_info_data['bus_data']['DepartureTime'];
                    $params['ResultToken'] = $bus_info_data['bus_data']['ResultToken'];
					$details = $this->bus_lib->get_bus_details($params['route_schedule_id'], $params['journey_date'],$params['route_code'],$params['ResultToken'],$params['booking_source']);
					//debug($details);exit('KUKKESHREE-5555');

					
					if ($details['status'] == SUCCESS_STATUS) {
						//Converting API currency data to preferred currency
						$currency_obj = new Currency(array(
								'module_type' => 'bus',
								'from' => get_api_data_currency(), 
								'to' => get_application_currency_preference()
							));
						$details = $this->bus_lib->seatdetails_in_preferred_currency($details, $bus_info_data['bus_data'], $currency_obj);
						
						$formatted_seat = $this->bus_lib->seat_layout_format($details['data']['result']['result']['value'], $currency_obj,'bus','B2B');
						$details['data']['result']['result']['value'] = $formatted_seat;
						// debug($details);exit;
						//Display Bus Details
						$currency_obj = new Currency(array('module_type' => 'bus', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
						$response['stauts'] = SUCCESS_STATUS;
						$page_data['search_id'] = $params['search_id'];
						$page_data['ResultToken'] = $params['ResultToken'];
						
						$page_data['details'] = $details['data']['result'];
						$page_data['currency_obj'] = $currency_obj;
                        
                                             
						if ($filter_boarding_points == false) {
							$response['data'] = get_compressed_output($this->template->isolated_view('bus/krl/krl_bus_details', $page_data));
						} else {
							$response['data'] = get_compressed_output($this->template->isolated_view('bus/travelyaari/travelyaari_boarding_details', $page_data));
						}
						$response['status'] = SUCCESS_STATUS;
					}
				break;
				case BITLA_BUS_DIRECT_OPERATORS_BOOKING_SOURCE :
					$bus_info_data = ''; $sub_booking_source = $params[4];								
					
					$bus_info_data = $this->bus_lib->get_route_details($params['search_id'], $params['route_schedule_id'], $params[2],$sub_booking_source);	                 					
                    
                    $bus_info_data['bus_data']['Form_id'] = $form_data->station_id;
                    $bus_info_data['bus_data']['To_id'] = $to_data->station_id;
                    
                    $params['journey_date'] = $bus_info_data['bus_data']['DepartureTime'];
                    $params['ResultToken'] = $bus_info_data['bus_data']['ResultToken'];

					$details = $this->bus_lib->get_bus_details($params['route_schedule_id'], $params['journey_date'],$params['route_code'],$params['ResultToken'],$sub_booking_source);

					if ($details['status'] == SUCCESS_STATUS) {
						//Converting API currency data to preferred currency
						$currency_obj = new Currency(array(
								'module_type' => 'bus',
								'from' => get_api_data_currency(), 
								'to' => get_application_currency_preference()
							));
						$details = $this->bus_lib->seatdetails_in_preferred_currency($details, $bus_info_data['bus_data'], $currency_obj);
						
						$formatted_seat = $this->bus_lib->seat_layout_format($details['data']['result']['result']['value'], $currency_obj,'bus','B2B');
						$details['data']['result']['result']['value'] = $formatted_seat;
						// debug($details);exit;
						//Display Bus Details
						$currency_obj = new Currency(array('module_type' => 'bus', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
						$response['stauts'] = SUCCESS_STATUS;
						$page_data['search_id'] = $params['search_id'];
						$page_data['ResultToken'] = $params['ResultToken'];
						
						$page_data['details'] = $details['data']['result'];
						$page_data['currency_obj'] = $currency_obj;
						$page_data['booking_s'] = $sub_booking_source;

                        if ($filter_boarding_points == false) {							
							$response['data'] = get_compressed_output($this->template->isolated_view('bus/bitla_direct/bitla_direct_bus_details', $page_data));
						} else {
							$response['data'] = get_compressed_output($this->template->isolated_view('bus/travelyaari/travelyaari_boarding_details', $page_data));
						}                  						
						$response['status'] = SUCCESS_STATUS;
					}
				break;
				case INFINITY_BUS_BOOKING_SOURCE :
                    $bus_info_data = $this->bus_lib->get_route_details($params['search_id'], $params['route_schedule_id'], $params[2]);

                    $bus_info_data['bus_data']['Form_id'] = $form_data->station_id;
                    $bus_info_data['bus_data']['To_id'] = $to_data->station_id;
                    
                    $params['journey_date'] = $bus_info_data['bus_data']['DepartureTime'];
                    $params['ResultToken'] = $bus_info_data['bus_data']['ResultToken'];
                    $params['ReferenceNumber'] = $bus_info_data['bus_data']['ReferenceNumber'];
                    $params['CompanyName'] = $bus_info_data['bus_data']['CompanyName'];                    

					$details = $this->bus_lib->get_bus_details($params['route_schedule_id'], $params['journey_date'],$params['route_code'],$params['ResultToken'],$params['booking_source'],$params['ReferenceNumber'], $params['CompanyName']);
				
					if ($details['status'] == SUCCESS_STATUS) {
						//Converting API currency data to preferred currency
						$currency_obj = new Currency(array(
								'module_type' => 'bus',
								'from' => get_api_data_currency(), 
								'to' => get_application_currency_preference()
							));
						$details = $this->bus_lib->seatdetails_in_preferred_currency($details, $bus_info_data['bus_data'], $currency_obj);
						
						$formatted_seat = $this->bus_lib->seat_layout_format($details['data']['result']['result']['value'], $currency_obj,'bus','B2B');
						$details['data']['result']['result']['value'] = $formatted_seat;

						//Display Bus Details
						$currency_obj = new Currency(array('module_type' => 'bus', 'from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
						$response['stauts'] = SUCCESS_STATUS;
						$page_data['search_id'] = $params['search_id'];
						$page_data['ResultToken'] = $params['ResultToken'];
						
						$page_data['details'] = $details['data']['result'];
						$page_data['currency_obj'] = $currency_obj;
                        
                                             
						if ($filter_boarding_points == false) {
							$response['data'] = get_compressed_output($this->template->isolated_view('bus/infinity/infinity_bus_details', $page_data));
						} else {
							$response['data'] = get_compressed_output($this->template->isolated_view('bus/travelyaari/travelyaari_boarding_details', $page_data));
						}
						$response['status'] = SUCCESS_STATUS;
					}
				break;
			}
		}
		$this->output_compressed_data($response);
	}


	/**
	 * Load hotels from different source
	 */
	function get_room_details()
	{
		$response['data'] = '';
		$response['msg'] = '';
		$response['status'] = FAILURE_STATUS;
		$params = $this->input->post();
		//debug($params);die('=====');
		/*$params['HotelCode'] = '1000002306';
		 $params['ResultIndex'] = 28;
		 $params['booking_source'] = PROVAB_HOTEL_BOOKING_SOURCE;
		 $params['TraceId'] = '	c064afbd-dc5b-43e0-909f-50b8d9efdd3d';
		 $params['op'] = 'get_room_details';
		 $params['search_id'] = 2290;*/

		if ($params['op'] == 'get_room_details' && intval($params['search_id']) > 0 && isset($params['booking_source']) == true) {
			$application_preferred_currency = get_application_currency_preference();
			$application_default_currency = get_application_currency_preference();
			load_hotel_lib($params['booking_source']);
			$this->hotel_lib->search_data($params['search_id']);
			$attr['search_id'] = intval($params['search_id']);
			switch($params['booking_source']) {
				case PROVAB_HOTEL_BOOKING_SOURCE :
					$raw_room_list = $this->hotel_lib->get_room_list(urldecode($params['ResultIndex']));

					//debug($raw_room_list);die('==================');
					
					$safe_search_data = $this->hotel_model->get_safe_search_data($params['search_id']);
					if ($raw_room_list['status']) {
						//Converting API currency data to preferred currency
						$currency_obj = new Currency(array('module_type' => 'hotel','from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
						//debug($currency_obj);die('+++');
						$raw_room_list = $this->hotel_lib->roomlist_in_preferred_currency($raw_room_list, $currency_obj,$params['search_id'],'b2b');
						//Display
						$currency_obj = new Currency(array('module_type' => 'hotel','from' => $application_default_currency, 'to' => $application_preferred_currency));
						//debug($currency_obj);die('+++');
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

				case REZLIVE_HOTEL :
					$raw_room_list = $this->hotel_lib->get_room_list($params);

					//debug($raw_room_list);die('123');

					$safe_search_data = $this->hotel_model->get_safe_search_data($params['search_id']);
					if ($raw_room_list['status']) {
						//Converting API currency data to preferred currency
						$currency_obj = new Currency(array('module_type' => 'hotel','from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
						//debug($currency_obj);die('+++');
						$raw_room_list = $this->hotel_lib->roomlist_in_preferred_currency($raw_room_list, $currency_obj,$params['search_id'],'b2b');
						//Display
						$currency_obj = new Currency(array('module_type' => 'hotel','from' => $application_default_currency, 'to' => $application_preferred_currency));
						//debug($currency_obj);die('+++');
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
		//debug($response);die();
		$this->output_compressed_data($response);
	}
	function filterFlightsOnPrefferedAirline($flight_data)
	{
		//debug($_GET["carrier"]); exit;
		//return $flight_data;
		$carrier = $_GET["carrier"];
		if($carrier == "0")
			return $flight_data;

		foreach($flight_data["data"]["Flights"] AS $fdsk => $fds)
		{
			foreach($fds AS $fdk => $fd)
			{	
				//debug($fd); exit;
				$airline_code = $fd["SegmentDetails"][0][0]["AirlineDetails"]["AirlineCode"];
				if($carrier != $airline_code)
				{
					unset($flight_data["data"]["Flights"][$fdsk][$fdk]);
				}
			}

			if(valid_array($flight_data["data"]["Flights"][$fdsk])){
				
				$flight_data["data"]["Flights"][$fdsk] = array_values($flight_data["data"]["Flights"][$fdsk]);
			}
		
		}
		if(!valid_array($flight_data["data"]["Flights"][0])){
			unset($flight_data["data"]["Flights"]);
		}
		
		//debug($flight_data); exit;
		return $flight_data;
	}
	function filterFlightsOnStopInputBasis($flight_data)
	{
		//return $flight_data;
		$conn_direct = $_GET["conn_direct"];
		$direct = 0; $connecting = 0;
		if($conn_direct == 0)
			return $flight_data;
		else if($conn_direct == 1)
			$direct = 1;
		else
			$connecting = 1;

		foreach($flight_data["data"]["Flights"] AS $fdsk => $fds)
		{
			foreach($fds AS $fdk => $fd)
			{
				$total_stops = $fd["SegmentSummary"][0]["TotalStops"];
				if($total_stops == 0 && $connecting == 1)
				{
					unset($flight_data["data"]["Flights"][$fdsk][$fdk]);
				}
				if($total_stops > 0 && $direct == 1)
				{
					unset($flight_data["data"]["Flights"][$fdsk][$fdk]);
				}
			}
			if(valid_array($flight_data)){
				$flight_data["data"]["Flights"][$fdsk] = array_values($flight_data["data"]["Flights"][$fdsk]);
			}
	
		}
		if(!valid_array($flight_data["data"]["Flights"][0])){
			unset($flight_data["data"]["Flights"]);
		}
		//debug($flight_data); exit;
		return $flight_data;
	}

	/**
	 * Load Flight from different source
	 * 2339 - one way - bangalore to goa
	 * 2341 - one way bangalore to dubai
	 */
	function flight_list($search_id='')
	{
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
			switch($search_params['booking_source']) {
				case PROVAB_FLIGHT_BOOKING_SOURCE :

					$raw_flight_list = $this->flight_lib->get_flight_list(abs($search_params['search_id']), $search_params['booking_source']);
					// debug($raw_flight_list); exit("I am her");
					if ($raw_flight_list['status']) {
						//View Data
						$raw_search_result = $raw_flight_list['data']['Search']['FlightDataList'];
						//Converting API currency data to preferred currency
						$currency_obj = new Currency(array('module_type' => 'flight','from' => get_api_data_currency(), 'to' => get_application_currency_preference()));

						$raw_search_result = $this->flight_lib->search_data_in_preferred_currency($raw_search_result, $currency_obj);

						//Display
						$currency_obj = new Currency(array('module_type' => 'flight','from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
                                              
						$formatted_search_data = $this->flight_lib->format_search_response($raw_search_result, $currency_obj, $search_params['search_id'], $this->current_module, $raw_flight_list['from_cache'], $raw_flight_list['search_hash'], PROVAB_FLIGHT_BOOKING_SOURCE);
      
                        $formatted_search_data = $this->filterFlightsOnStopInputBasis($formatted_search_data);
                        $formatted_search_data = $this->filterFlightsOnPrefferedAirline($formatted_search_data);
						$raw_flight_list['data'] = $formatted_search_data['data'];
						$route_count = count($raw_flight_list['data']['Flights']);
						$domestic_round_way_flight = $raw_flight_list['data']['JourneySummary']['IsDomesticRoundway'];
						if (($route_count > 0  && $domestic_round_way_flight == false) || ($route_count == 2 && $domestic_round_way_flight == true)) {
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
					$raw_flight_list = $this->flight_lib->get_flight_list(abs($search_params['search_id']), $search_params['booking_source']);
					if ($raw_flight_list['status']) {
						//View Data
						$raw_search_result = $raw_flight_list['data']['Search']['FlightDataList'];
						//Converting API currency data to preferred currency
						$currency_obj = new Currency(array('module_type' => 'flight','from' => get_api_data_currency(), 'to' => get_application_currency_preference()));

						$raw_search_result = $this->flight_lib->search_data_in_preferred_currency($raw_search_result, $currency_obj, $search_params['search_id']);
						//Display
						$currency_obj = new Currency(array('module_type' => 'flight','from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
                                              
						$formatted_search_data = $this->flight_lib->format_search_response($raw_search_result, $currency_obj, $search_params['search_id'], $this->current_module, $raw_flight_list['from_cache'], $raw_flight_list['search_hash']);
						$formatted_search_data = $this->filterFlightsOnStopInputBasis($formatted_search_data);
						$formatted_search_data = $this->filterFlightsOnPrefferedAirline($formatted_search_data);
						
						$raw_flight_list['data'] = $formatted_search_data['data'];
						$route_count = count($raw_flight_list['data']['Flights']);
						$domestic_round_way_flight = $raw_flight_list['data']['JourneySummary']['IsDomesticRoundway'];
						if (($route_count > 0  && $domestic_round_way_flight == false) || ($route_count == 2 && $domestic_round_way_flight == true)) {
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
					$raw_flight_list = $this->flight_lib->get_flight_list(abs($search_params['search_id']), $search_params['booking_source']);

					if ($raw_flight_list['status']) {
						//View Data
						$raw_search_result = $raw_flight_list['data']['Search']['FlightDataList'];
                        //debug($raw_search_result); exit;           
						//Converting API currency data to preferred currency
						$currency_obj = new Currency(array('module_type' => 'flight','from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
                                                 
						$raw_search_result = $this->flight_lib->search_data_in_preferred_currency($raw_search_result, $currency_obj, $search_params['search_id']);

						//Display
						$currency_obj = new Currency(array('module_type' => 'flight','from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
                                              
						$formatted_search_data = $this->flight_lib->format_search_response($raw_search_result, $currency_obj, $search_params['search_id'], $this->current_module, $raw_flight_list['from_cache'], $raw_flight_list['search_hash']);
                       	
                        $formatted_search_data = $this->filterFlightsOnStopInputBasis($formatted_search_data);
                        
                        $formatted_search_data = $this->filterFlightsOnPrefferedAirline($formatted_search_data);
						
						$raw_flight_list['data'] = $formatted_search_data['data'];
						
						$route_count = count($raw_flight_list['data']['Flights']);
						$domestic_round_way_flight = $raw_flight_list['data']['JourneySummary']['IsDomesticRoundway'];
						if (($route_count > 0  && $domestic_round_way_flight == false) || ($route_count == 2 && $domestic_round_way_flight == true)) {
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
					$raw_flight_list = $this->flight_lib->get_flight_list(abs($search_params['search_id']), $search_params['booking_source']);
					if ($raw_flight_list['status']) {
						//View Data
						$raw_search_result = $raw_flight_list['data']['Search']['FlightDataList'];
                                                
						//Converting API currency data to preferred currency
						$currency_obj = new Currency(array('module_type' => 'flight','from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
                                                 
						$raw_search_result = $this->flight_lib->search_data_in_preferred_currency($raw_search_result, $currency_obj);
						//Display
						$currency_obj = new Currency(array('module_type' => 'flight','from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
                                              
						$formatted_search_data = $this->flight_lib->format_search_response($raw_search_result, $currency_obj, $search_params['search_id'], $this->current_module, $raw_flight_list['from_cache'], $raw_flight_list['search_hash']);

						//$search_params['booking_source']
                                              // debug($formatted_search_data);exit;
						$formatted_search_data = $this->filterFlightsOnStopInputBasis($formatted_search_data);
						$formatted_search_data = $this->filterFlightsOnPrefferedAirline($formatted_search_data);
						$raw_flight_list['data'] = $formatted_search_data['data'];
						// debug($raw_flight_list['data']);exit;
						$route_count = count($raw_flight_list['data']['Flights']);
						$domestic_round_way_flight = $raw_flight_list['data']['JourneySummary']['IsDomesticRoundway'];
						if (($route_count > 0  && $domestic_round_way_flight == false) || ($route_count == 2 && $domestic_round_way_flight == true)) {
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
							//debug($page_params); exit;
							$page_params['domestic_round_way_flight'] = $domestic_round_way_flight;
							
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
					$raw_flight_list = $this->flight_lib->get_flight_list(abs($search_params['search_id']), $search_params['booking_source']);
					if ($raw_flight_list['status']) {
						//View Data
						$raw_search_result = $raw_flight_list['data']['Search']['FlightDataList'];
                                                
						//Converting API currency data to preferred currency
						$currency_obj = new Currency(array('module_type' => 'flight','from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
                                                 
						$raw_search_result = $this->flight_lib->search_data_in_preferred_currency($raw_search_result, $currency_obj);
						//Display
						$currency_obj = new Currency(array('module_type' => 'flight','from' => get_application_currency_preference(), 'to' => get_application_currency_preference()));
                                              
						$formatted_search_data = $this->flight_lib->format_search_response($raw_search_result, $currency_obj, $search_params['search_id'], $this->current_module, $raw_flight_list['from_cache'], $raw_flight_list['search_hash']);
                                              // debug($formatted_search_data);exit;
						$formatted_search_data = $this->filterFlightsOnStopInputBasis($formatted_search_data);

						$formatted_search_data = $this->filterFlightsOnPrefferedAirline($formatted_search_data);
						$raw_flight_list['data'] = $formatted_search_data['data'];
						$route_count = count($raw_flight_list['data']['Flights']);
						$domestic_round_way_flight = $raw_flight_list['data']['JourneySummary']['IsDomesticRoundway'];
						if (($route_count > 0  && $domestic_round_way_flight == false) || ($route_count == 2 && $domestic_round_way_flight == true)) {
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
		//debug($response); exit;
		header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		$this->output_compressed_data_flight($response);
	}

	/**
	 * Get Data For Fare Calendar
	 * @param string $booking_source
	 */
function puls_minus_days_fare_list($booking_source)
	{
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
			$date_array[] = date('Y-m', strtotime($departure_date[0].' +1 month')).'-1';
			//Get Current Month And Next Month
			$day_fare_list = array();
			foreach ($date_array as $k => $v) {
				$search_data['data']['depature'] = $v;
				$search = $this->flight_lib->calendar_safe_search_data($search_data['data']);
				if (valid_array($search) == true) {
					switch($booking_source) {
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
	function fare_list($booking_source)
	{
		/*$options = array('location' => 'http://192.168.0.63/soap/server1.php',
		 'uri' => 'http://192.168.0.63/soap/');
		 $api = new SoapClient(NULL, $options);
		 echo "<pre>"; print_r($api->hello()); exit;*/

		$response['data'] = '';
		$response['msg'] = '';
		$response['status'] = FAILURE_STATUS;
		$search_params = $this->input->get();
		load_flight_lib($booking_source);
		$search_params = $this->flight_lib->calendar_safe_search_data($search_params);
		if (valid_array($search_params) == true) {
			switch($booking_source) {
				case PROVAB_FLIGHT_BOOKING_SOURCE :
					$raw_fare_list = $this->flight_lib->get_fare_list($search_params);
					if ($raw_fare_list['status']) {
						$fare_calendar_list = $this->flight_lib->format_cheap_fare_list($raw_fare_list['data']);
						if ($fare_calendar_list['status'] == SUCCESS_STATUS) {
							$response['data']['departure'] = $search_params['depature'];
							$calendar_events = $this->get_fare_calendar_events($fare_calendar_list['data'], $raw_fare_list['data']['GetCalendarFareResult']['SessionId']);
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
	private function get_calendar_event_obj($title='', $start = '', $tip = '',$add_class = '', $href = '', $event_date = '', $session_id = '', $data_id='')
	{
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

	function day_fare_list($booking_source)
	{
		$response['data'] = '';
		$response['msg'] = '';
		$response['status'] = FAILURE_STATUS;
		$search_params = $this->input->get();
		load_flight_lib($booking_source);
		$safe_search_params = $this->flight_lib->calendar_day_fare_safe_search_data($search_params);
		if ($safe_search_params['status'] == SUCCESS_STATUS) {
			switch($booking_source) {
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

	private function get_fare_calendar_events($events, $session_id='')
	{
		$currency_obj = new Currency(array('module_type' => 'flight','from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
		$index = 0;
		$calendar_events = array();
		foreach ($events as $k => $day_fare) {
			if (valid_array($day_fare) == true) {
				$fare_object = array('BaseFare' => $day_fare['BaseFare']);
				$BaseFare = $this->flight_lib->update_markup_currency($fare_object, $currency_obj);
				$tax = $currency_obj->get_currency($day_fare['tax'], false);
				$day_fare['price'] = floor($BaseFare['BaseFare']+$day_fare['tax']);
				$event_obj = $this->get_calendar_event_obj(
				$currency_obj->get_currency_symbol(get_application_currency_preference()).' '.$day_fare['price'],
				$k, $day_fare['airline_name'].'-'.$day_fare['airline_code'], 'search-day-fare', '', $day_fare['departure'], '',
				$day_fare['airline_code']);
				$calendar_events[$index] = $event_obj;
			} else {
				$event_obj = $this->get_calendar_event_obj('Update', $k, 'Current Cheapest Fare Not Available. Click To Get Latest Fare.' ,
				'update-day-fare', '', $k, $session_id, '');
				$calendar_events[$index] = $event_obj;
			}
			$index++;
		}
		return $calendar_events;
	}

	/**
	 * Get Fare Details
	 */
	function get_fare_details()
	{
		$response['status'] = false;
		$response['data'] = '';
		$response['msg'] = '<i class="fa fa-warning text-danger"></i> Fare Details Not Available';
		$params = $this->input->post();

		load_flight_lib($params['booking_source']);
		$data_access_key = $params['data_access_key'];
		$params['data_access_key'] = unserialized_data($params['data_access_key']);
		if (empty($params['data_access_key']) == false) {
			switch($params['booking_source']) {
				case PROVAB_FLIGHT_BOOKING_SOURCE :
					$params['data_access_key'] = $this->flight_lib->read_token($data_access_key);
					$data = $this->flight_lib->get_fare_details($params['data_access_key'], $params['search_access_key']);
					if ($data['status'] == SUCCESS_STATUS) {
						$response['status']	= SUCCESS_STATUS;
						$response['data']	= $this->template->isolated_view('flight/tbo/fare_details', array('fare_rules' => $data['data']));
						$response['msg']	= 'Fare Details Available';
					}
				case TRAVELPORT_GDS_BOOKING_SOURCE :
					$params['data_access_key'] = $this->flight_lib->read_token($data_access_key);
					$data = $this->flight_lib->get_fare_details($params['data_access_key'], $params['search_access_key']);
					if ($data['status'] == SUCCESS_STATUS) {
						$response['status']	= SUCCESS_STATUS;
						$response['data']	= $this->template->isolated_view('flight/tbo/fare_details', array('fare_rules' => $data['data']));
						$response['msg']	= 'Fare Details Available';
					}
				case TRAVELPORT_ACH_BOOKING_SOURCE :
					$params['data_access_key'] = $this->flight_lib->read_token($data_access_key);
					$data = $this->flight_lib->get_fare_details($params['data_access_key'], $params['search_access_key']);
					if ($data['status'] == SUCCESS_STATUS) {
						$response['status']	= SUCCESS_STATUS;
						$response['data']	= $this->template->isolated_view('flight/tbo/fare_details', array('fare_rules' => $data['data']));
						$response['msg']	= 'Fare Details Available';
					}	
				case SPICEJET_BOOKING_SOURCE :
					$params['data_access_key'] = $this->flight_lib->read_token($data_access_key);
					$data = $this->flight_lib->get_fare_details($params['data_access_key'], $params['search_access_key']);
					if ($data['status'] == SUCCESS_STATUS) {
						$response['status']	= SUCCESS_STATUS;
						$response['data']	= $this->template->isolated_view('flight/tbo/fare_details', array('fare_rules' => $data['data']));
						$response['msg']	= 'Fare Details Available';
					}		
			}
		}
		$this->output_compressed_data($response);
	}

	function get_combined_booking_from()
	{
		$response['status']	= FAILURE_STATUS;
		$response['data']	= array();
		$params = $this->input->post();
		if (empty($params['search_id']) == false && empty($params['trip_way_1']) == false && empty($params['trip_way_2']) == false) {
			$tmp_trip_way_1	= json_decode($params['trip_way_1'], true);
			$tmp_trip_way_2	= json_decode($params['trip_way_2'], true);
			$search_id	= $params['search_id'];
			foreach($tmp_trip_way_1 as $___v) {
				$trip_way_1[$___v['name']] = $___v['value'];
			}
			foreach($tmp_trip_way_2 as $___v) {
				$trip_way_2[$___v['name']] = $___v['value'];
			}
			$booking_source = $trip_way_1['booking_source'];
			switch($booking_source) {
				case PROVAB_FLIGHT_BOOKING_SOURCE : load_flight_lib(PROVAB_FLIGHT_BOOKING_SOURCE);
					$response['data']['booking_url']	= $this->flight_lib->booking_url(intval($params['search_id']));
					$response['data']['form_content']	= $this->flight_lib->get_form_content($trip_way_1, $trip_way_2);
					$response['status']					= SUCCESS_STATUS;
				break;

				case SPICEJET_BOOKING_SOURCE : load_flight_lib(SPICEJET_BOOKING_SOURCE);
				$response['data']['booking_url']	= $this->flight_lib->booking_url(intval($params['search_id']));
				$response['data']['form_content']	= $this->flight_lib->get_form_content($trip_way_1, $trip_way_2);
				$response['status']					= SUCCESS_STATUS;
				break;

				case TRAVELPORT_GDS_BOOKING_SOURCE : load_flight_lib(TRAVELPORT_GDS_BOOKING_SOURCE);
				$response['data']['booking_url']	= $this->flight_lib->booking_url(intval($params['search_id']));
				$response['data']['form_content']	= $this->flight_lib->get_form_content($trip_way_1, $trip_way_2);
				$response['status']					= SUCCESS_STATUS;
				break;

				case TRAVELPORT_ACH_BOOKING_SOURCE : load_flight_lib(TRAVELPORT_ACH_BOOKING_SOURCE);
				$response['data']['booking_url']	= $this->flight_lib->booking_url(intval($params['search_id']));
				$response['data']['form_content']	= $this->flight_lib->get_form_content($trip_way_1, $trip_way_2);
				$response['status']					= SUCCESS_STATUS;
				break;

				case STAR_BOOKING_SOURCE : load_flight_lib(STAR_BOOKING_SOURCE);
				$response['data']['booking_url']	= $this->flight_lib->booking_url(intval($params['search_id']));
				$response['data']['form_content']	= $this->flight_lib->get_form_content($trip_way_1, $trip_way_2);
				$response['status']					= SUCCESS_STATUS;
				break;
			}
		}
		$this->output_compressed_data($response);
	}

	/**
	 *
	 */
	function log_event_ip_info($eid)
	{
		$params = $this->input->post();
		if (empty($eid) == false) {
			$this->custom_db->update_record('exception_logger', array('client_info' => serialize($params)), array('exception_id' => $eid));
		}
	}
	//---------------------------------------------------------------- Booking Events Starts
	/**
	* Load Booking Events of all the modules
	*/
	function booking_events()
	{
		$status = true;
		$data = array();
		$calendar_events = array();
		$condition = array(array('BD.created_datetime', '>=', $this->db->escape(date('Y-m-d', strtotime(subtract_days_from_date(90))))));//of last 30 days only
		if (is_active_bus_module()) {
			$condition = array(array('BD.app_reference', '=', 'ID.app_reference'), array('ID.journey_datetime', '>=', $this->db->escape(date('Y-m-d', strtotime(subtract_days_from_date(90))))));
			$calendar_events = array_merge($calendar_events, $this->bus_booking_events($condition));
                       
		}
		if (is_active_hotel_module()) {
			$condition = array(array('BD.hotel_check_in', '>=', $this->db->escape(date('Y-m-d', strtotime(subtract_days_from_date(90))))));
			$calendar_events = array_merge($calendar_events, $this->hotel_booking_events($condition));
		}
		if (is_active_airline_module()) {
			$condition = array(array('BD.journey_start', '>=', $this->db->escape(date('Y-m-d', strtotime(subtract_days_from_date(90))))));
			$calendar_events = array_merge($calendar_events, $this->flight_booking_events($condition));
		}

		if (is_active_sightseeing_module()) {
			$condition = array(array('BD.travel_date', '>=', $this->db->escape(date('Y-m-d', strtotime(subtract_days_from_date(90))))));
			$calendar_events = array_merge($calendar_events, $this->sightseeing_booking_events($condition));
		}
		if (is_active_transferv1_module()) {
			$condition = array(array('BD.travel_date', '>=', $this->db->escape(date('Y-m-d', strtotime(subtract_days_from_date(90))))));
			$calendar_events = array_merge($calendar_events, $this->transfers_booking_events($condition));
		}


               // debug($calendar_events);exit;
		header('content-type:application/json');
		echo json_encode(array('status' => $status, 'data' => $calendar_events));
		exit;
	}

	/**
	 * Hotel Booking Events Summary
	 * @param array $condition
	 */
	private function hotel_booking_events($condition)
	{
		$this->load->model('hotel_model');
		$data_list = $this->hotel_model->booking($condition);

		$this->load->library('booking_data_formatter');
		$table_data = $this->booking_data_formatter->format_hotel_booking_data($data_list, 'b2b');
		$booking_details = $table_data['data']['booking_details'];
		$calendar_events = array();
		if (valid_array($booking_details) == true) {
			$key = 0;
			foreach ($booking_details as $k => $v) {
				$calendar_events[$key]['title'] = $v['app_reference'].'-'.$v['status'];
				$calendar_events[$key]['start'] = $v['hotel_check_in'];
				$calendar_events[$key]['tip'] = $v['app_reference'].'-PNR:'.$v['confirmation_reference'].'-From:'.$v['hotel_check_in'].', To:'.$v['hotel_check_out'].'-'.$v['status'].'- Click To View More Details';
				$calendar_events[$key]['href'] = hotel_voucher_url($v['app_reference'], $v['booking_source'], $v['status']);
				$calendar_events[$key]['add_class'] = 'hand-cursor event-hand hotel-booking';
				$key++;
			}
		}
		return $calendar_events;
	}

	/**
	 * Flight Booking Events Summary
	 * @param array $condition
	 */
	private function flight_booking_events($condition)
	{
		$this->load->model('flight_model');
		$data_list = $this->flight_model->booking($condition);
		$this->load->library('booking_data_formatter');
		$table_data = $this->booking_data_formatter->format_flight_booking_data($data_list, 'b2b');
		$booking_details = $table_data['data']['booking_details'];
		$calendar_events = array();
		if (valid_array($booking_details) == true) {
			$key = 0;
			foreach ($booking_details as $k => $v) {
				$calendar_events[$key]['title'] = $v['app_reference'].'-'.$v['status'];
				$calendar_events[$key]['start'] = $v['journey_start'];
				$calendar_events[$key]['tip'] = $v['app_reference'].',From:'.$v['journey_from'].', To:'.$v['journey_to'].'-'.$v['status'].'- Click To View More Details';
				$calendar_events[$key]['href'] = flight_voucher_url($v['app_reference'], $v['booking_source'], $v['status']);
				$calendar_events[$key]['add_class'] = 'hand-cursor event-hand flight-booking';
				$key++;
			}
		}

		return $calendar_events;
	}

	/**
	 * Sightseeing Booking Events Summary
	 * @param array $condition
	 */
	private function sightseeing_booking_events($condition)
	{
		$this->load->model('sightseeing_model');
		$data_list = $this->sightseeing_model->booking($condition);
		$this->load->library('booking_data_formatter');
		$table_data = $this->booking_data_formatter->format_sightseeing_booking_data($data_list, 'b2b');
		$booking_details = $table_data['data']['booking_details'];
		$calendar_events = array();
		if (valid_array($booking_details) == true) {
			$key = 0;
			foreach ($booking_details as $k => $v) {
				$calendar_events[$key]['title'] = $v['app_reference'].'-'.$v['status'];
				$calendar_events[$key]['start'] = $v['travel_date'];
				$calendar_events[$key]['tip'] = $v['app_reference'].'-PNR:'.$v['confirmation_reference'].'-From:'.$v['destination_name'].', Travel Date:'.$v['travel_date'].'-'.$v['status'].'- Click To View More Details';
				$calendar_events[$key]['href'] = sightseeing_voucher_url($v['app_reference'], $v['booking_source'], $v['status']);
				$calendar_events[$key]['add_class'] = 'hand-cursor event-hand sightseeing-booking';
				$key++;
			}
		}

		return $calendar_events;
	}
	/**
	 * Transfers Booking Events Summary
	 * @param array $condition
	 */
	private function transfers_booking_events($condition){
		$this->load->model('transferv1_model');
		$data_list = $this->transferv1_model->booking($condition);
		$this->load->library('booking_data_formatter');
		$table_data = $this->booking_data_formatter->format_transferv1_booking_data($data_list, 'b2b');
		$booking_details = $table_data['data']['booking_details'];
		$calendar_events = array();

		if (valid_array($booking_details) == true) {
			$key = 0;
			foreach ($booking_details as $k => $v) {
				$calendar_events[$key]['title'] = $v['app_reference'].'-'.$v['status'];
				$calendar_events[$key]['start'] = $v['travel_date'];
				$calendar_events[$key]['tip'] = $v['app_reference'].'-PNR:'.$v['confirmation_reference'].'-From:'.$v['destination_name'].', Travel Date:'.$v['travel_date'].'-'.$v['status'].'- Click To View More Details';
				$calendar_events[$key]['href'] = transfers_voucher_url($v['app_reference'], $v['booking_source'], $v['status']);
				$calendar_events[$key]['add_class'] = 'hand-cursor event-hand transfers-booking';
				//$calendar_events[$k]['prepend_element'] = '<i class="fa fa-bus"></i>';
				$key++;
			}
		}
		return $calendar_events;
	}

	/**
	 * Bus Booking Events Summary
	 * @param array $condition
	 */
	private function bus_booking_events($condition)
	{
		$this->load->model('bus_model');
		$data_list = $this->bus_model->bookings_for_event_listing($condition);
		$this->load->library('booking_data_formatter');
		$table_data = $this->booking_data_formatter->format_bus_booking_data($data_list, 'b2b');
		$booking_details = $table_data['data']['booking_details'];
		$calendar_events = array();
		if (valid_array($booking_details) == true) {
			$key = 0;
			foreach ($booking_details as $k => $v) {
				$calendar_events[$key]['title'] = $v['app_reference'].'-'.$v['status'];
				$calendar_events[$key]['start'] = $v['departure_datetime'];
				$calendar_events[$key]['tip'] = $v['app_reference'].'-PNR:'.$v['pnr'].'-From:'.$v['departure_from'].', To:'.$v['arrival_to'].'-'.$v['status'].'- Click To View More Details';
				$calendar_events[$key]['href'] = bus_voucher_url($v['app_reference'], $v['booking_source'], $v['status']);
				$calendar_events[$key]['add_class'] = 'hand-cursor event-hand bus-booking';
				//$calendar_events[$k]['prepend_element'] = '<i class="fa fa-bus"></i>';
				$key++;
			}
		}
		return $calendar_events;
	}
	//---------------------------------------------------------------- Booking Events End
	/**
	* Balu A
	*
	*/
	function auto_suggest_booking_id()
	{
		$get_data = $this->input->get();
		if(valid_array($get_data) == true && empty($get_data['term']) == false && empty($get_data['module']) == false) {
			$this->load->model('report_model');
			$module = trim($get_data['module']);
			$chars = $get_data['term'];
			switch($module) {
				case PROVAB_FLIGHT_BOOKING_SOURCE:
					$list = $this->report_model->auto_suggest_flight_booking_id($chars);
					break;
				case PROVAB_HOTEL_BOOKING_SOURCE:
					$list = $this->report_model->auto_suggest_hotel_booking_id($chars);
					break;
				case PROVAB_BUS_BOOKING_SOURCE:
					$list = $this->report_model->auto_suggest_bus_booking_id($chars);
					break;
			}
			$temp_list = array();
			if (valid_array ( $list ) == true) {
				foreach ( $list as $k => $v ) {
					$temp_list [] = array (
							'id' => $k,
							'label' => $v ['app_reference'],
							'value' => $v ['app_reference']
					);
				}
			}
			$this->output_compressed_data($temp_list);
		}
	}
	/**
	 * Jagnaath
	 * Get Bank Branches
	 */
	function get_bank_branches($bank_origin)
	{
		if(intval($bank_origin) > 0) {
			$data['status'] = false;
			$data['branches'] = false;
			$branch_details = $this->custom_db->single_table_records('bank_account_details', 'origin, en_branch_name, account_number', array('origin' => intval($bank_origin), 'status' => ACTIVE));
			if($branch_details['status'] == true) {
				$data['status'] = true;
				$data['branch'] = $branch_details['data'][0]['en_branch_name'];
				$data['account_number'] = $branch_details['data'][0]['account_number'];
			}
		}
		$this->output_compressed_data($data);
	}
	/**
	* Get Hotel Images by HotelCode
	*/
	function get_hotel_images(){
		$post_params = $this->input->post();
		if($post_params['hotel_code']){
			//debug($post_params['hotel_code']);exit;
			switch ($post_params['booking_source']) {

				case PROVAB_HOTEL_BOOKING_SOURCE:
					load_hotel_lib($post_params['booking_source']);
					$raw_hotel_images = $this->hotel_lib->get_hotel_images($post_params['hotel_code']);	

					//debug($raw_hotel_images);exit;
					if($raw_hotel_images['status']==true){
							$this->hotel_model->add_hotel_images($post_params['search_id'],$raw_hotel_images['data'],$post_params['hotel_code']);
							$response['data'] = get_compressed_output(
							$this->template->isolated_view('hotel/tbo/tbo_hotel_images',
							array('hotel_images'=>$raw_hotel_images,'HotelCode'=>$post_params['hotel_code'],'HotelName'=>$post_params['Hotel_name']
							)));
					}
					
					break;
				case REZLIVE_HOTEL:
					load_hotel_lib($post_params['booking_source']);
					$raw_hotel_images = $this->hotel_lib->get_hotel_images($post_params['hotel_code']);	

					//debug($raw_hotel_images);exit;
					if($raw_hotel_images['status']==true){
							$this->hotel_model->add_hotel_images($post_params['search_id'],$raw_hotel_images['data'],$post_params['hotel_code']);
							$response['data'] = get_compressed_output(
							$this->template->isolated_view('hotel/tbo/tbo_hotel_images',
							array('hotel_images'=>$raw_hotel_images,'HotelCode'=>$post_params['hotel_code'],'HotelName'=>$post_params['Hotel_name']
							)));
					}
					
					break;	
			}
			 $this->output_compressed_data($response);
		
		}
		exit;
	}
		/**
	*Get Cancellation Policy based on Cancellation policy code
	*
	*/
	function get_cancellation_policy_old(){
		$get_params =$this->input->get();
		
		$application_preferred_currency = get_application_currency_preference();
		$application_default_currency = get_application_currency_preference();
		$currency_obj = new Currency(array('module_type' => 'hotel','from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
		$room_price = $get_params['room_price'];
		//debug($get_params);exit;
		if(isset($get_params['booking_source'])&&!empty($get_params['booking_source'])){
			load_hotel_lib($get_params['booking_source']);

			if($get_params['today_cancel_date']==false){
				if(isset($get_params['policy_code'])&&!empty($get_params['policy_code'])){
					$safe_search_data = $this->hotel_model->get_safe_search_data($get_params['tb_search_id']);				
					$get_params['no_of_nights'] = $safe_search_data['data']['no_of_nights'];
					$get_params['room_count'] = $safe_search_data['data']['room_count'];
					$get_params['check_in'] = $safe_search_data['data']['from_date'];
	 				$cancellation_details = $this->hotel_lib->get_cancellation_details($get_params);
	 				
	 				
	 				$cancellatio_details =$cancellation_details['GetCancellationPolicy']['policy'][0]['policy'];
	 				$policy_string ='';
	 				$cancel_string='';
	 				$cancel_count = count($cancellatio_details);
	 				//$cancel_reverse = $cancellatio_details; 
	 				$cancel_reverse = $this->hotel_lib->php_arrayUnique($cancellatio_details,'Charge');				
	 				//debug($cancellatio_details);exit;
	 				$cancellatio_details = $this->hotel_lib->php_arrayUnique($cancellatio_details,'Charge');
	 					foreach ($cancellatio_details as $key => $value) {
		 					$amount = 0;
		 					$policy_string ='';	 					
			 					if($value['Charge']==0){
			 						$policy_string .='No cancellation charges, if cancelled before '.date('d M Y',strtotime($value['ToDate']));
			 					}else{
			 						if($value['Charge']!=0){
			 							 if(isset($cancel_reverse[$key+1])){
			 							 		if($value['ChargeType']==1){ 					
							 						$amount =  $currency_obj->get_currency_symbol($currency_obj->to_currency)." ".get_converted_currency_value($currency_obj->force_currency_conversion(round($value['Charge'])));							 						
							 					}elseif($value['ChargeType']==2){
							 						$amount = $currency_obj->get_currency_symbol($currency_obj->to_currency)." ".$room_price;
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
							             		$amount =  $currency_obj->get_currency_symbol($currency_obj->to_currency)." ".get_converted_currency_value($currency_obj->force_currency_conversion(round($value['Charge'])));	
							          		}elseif ($value['ChargeType']==2) {
							             		$amount = $currency_obj->get_currency_symbol($currency_obj->to_currency)." ".$room_price;
							             	}
							             	$current_date = date('Y-m-d');
											$cancell_date = date('Y-m-d',strtotime($value['FromDate']));
											if($cancell_date > $current_date){
												$value['FromDate'] =$value['FromDate']; 
											}else{
												$value['FromDate'] = date('Y-m-d');
											}
							             	$policy_string .='Cancellations made after '.date('d M Y',strtotime($value['FromDate'])).', or no-show, would be charged '.$amount;
							             }
					 				
				 					}
			 					}		 					
			 				$cancel_string .= $policy_string.'<br/> ';
		 				}
	 				
	 				echo $cancel_string;
					//echo $cancellation_details['GetCancellationPolicy']['policy'][0];
				}else{
					$cancel_string ='';
						$cancellation_policy_details = json_decode(base64_decode($get_params['policy_details']));
						//debug($cancellation_policy_details);
						$cancel_count = count($cancellation_policy_details);					
						$cancellation_policy_details = json_decode(json_encode($cancellation_policy_details), True);
						$cancel_reverse = $this->hotel_lib->php_arrayUnique(array_reverse($cancellation_policy_details),'Charge');			
						//$cancel_reverse = array_reverse($cancellation_policy_details);
						
						//debug($cancel_reverse);						
						$cancellation_policy_details = $this->hotel_lib->php_arrayUnique(array_reverse($cancellation_policy_details),'Charge');
						
						if($cancellation_policy_details){
								//$cancellation_policy_details = array_reverse($cancellation_policy_details);
								foreach ($cancellation_policy_details as $key=>$value) {							
										$policy_string ='';								
											if($value['Charge']==0){
												$policy_string .='No cancellation charges, if cancelled before '.date('d M Y',strtotime($value['ToDate']));
											}else{
												if(isset($cancel_reverse[$key+1])){
													if($value['ChargeType']==1){
														$amount = $currency_obj->get_currency_symbol($currency_obj->to_currency)."  ".$value['Charge'];
														
													}elseif ($value['ChargeType']==2) {
														$amount = $currency_obj->get_currency_symbol($currency_obj->to_currency)."  ".$room_price;
													}
													$current_date = date('Y-m-d');
													$cancell_date = date('Y-m-d',strtotime($value['FromDate']));
													if($cancell_date >$current_date){
														$policy_string .='Cancellations made after '.date('d M Y',strtotime($value['FromDate'])).' to '.date('d M Y',strtotime($value['ToDate'])).', would be charged '.$amount;
													}
													
												}else{
													if($value['ChargeType']==1){
														$amount = $currency_obj->get_currency_symbol($currency_obj->to_currency)."  ".$value['Charge'];
														
													}elseif ($value['ChargeType']==2) {
														$amount = $currency_obj->get_currency_symbol($currency_obj->to_currency)."  ".$room_price;
													}
													$current_date = date('Y-m-d');
													$cancell_date = date('Y-m-d',strtotime($value['FromDate']));
													if($cancell_date >$current_date){
														$value['FromDate'] = $value['FromDate'];
													}else{
														$value['FromDate'] = date('Y-m-d');
													}
													$policy_string .='Cancellations made after '.date('d M Y',strtotime($value['FromDate'])).', or no-show, would be charged '.$amount;
												}
											}									
																		
										$cancel_string .= $policy_string.'<br/>';
										
								}
						}else{
							$cancel_string = 'This rate is non-refundable. If you cancel this booking you will not be refunded any of the payment.';
						}
						
					
					echo $cancel_string;
				}
			}else{
				echo "This rate is non-refundable. If you cancel this booking you will not be refunded any of the payment.";
			}
			
		}else{
			echo "This rate is non-refundable. If you cancel this booking you will not be refunded any of the payment.";
		}
		exit;
	}
	function get_cancellation_policy(){
		$get_params =$this->input->get();
		//debug($get_params);exit;
		$application_preferred_currency = get_application_currency_preference();
		$application_default_currency = get_application_currency_preference();
		$currency_obj = new Currency(array('module_type' => 'hotel','from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
		$room_price = $get_params['room_price'];
		//debug($get_params);exit;
		if(isset($get_params['booking_source'])&&!empty($get_params['booking_source'])){
			load_hotel_lib($get_params['booking_source']);

			if($get_params['today_cancel_date']==false){
				if(isset($get_params['policy_code'])&&!empty($get_params['policy_code'])){
					$safe_search_data = $this->hotel_model->get_safe_search_data($get_params['tb_search_id']);				
					$get_params['no_of_nights'] = $safe_search_data['data']['no_of_nights'];
					$get_params['room_count'] = $safe_search_data['data']['room_count'];
					$get_params['check_in'] = $safe_search_data['data']['from_date'];
	 				$cancellation_details = $this->hotel_lib->get_cancellation_details($get_params);
	 				$cancellatio_details =$cancellation_details['GetCancellationPolicy']['policy'][0]['policy'];
	 				
	 				$policy_string ='';
	 				$cancel_string='';
	 				$cancel_count = count($cancellatio_details);
	 				//$cancel_reverse = $cancellatio_details; 
	 				$cancel_reverse = $this->hotel_lib->php_arrayUnique($cancellatio_details,'Charge');				
	 				//debug($cancellatio_details);exit;
	 				$cancellatio_details = $this->hotel_lib->php_arrayUnique($cancellatio_details,'Charge');
	 					foreach ($cancellatio_details as $key => $value) {

	 						$value['Charge'] = $this->hotel_lib->update_cancellation_markup_currency($value['Charge'],$currency_obj,$get_params['search_id']);
		 					$amount = 0;
		 					$policy_string ='';	 					
			 					if($value['Charge']==0){
			 						$policy_string .='No cancellation charges, if cancelled before '.date('d M Y',strtotime($value['ToDate']));
			 					}else{
			 						if($value['Charge']!=0){
			 							 if(isset($cancel_reverse[$key+1])){
			 							 		if($value['ChargeType']==1){ 					
							 						$amount =  $currency_obj->get_currency_symbol($currency_obj->to_currency)." ".round($value['Charge']);							 						
							 					}elseif($value['ChargeType']==2){
							 						$amount = $currency_obj->get_currency_symbol($currency_obj->to_currency)." ".$room_price;
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
							             		$amount =  $currency_obj->get_currency_symbol($currency_obj->to_currency)." ".round($value['Charge']);	
							          		}elseif ($value['ChargeType']==2) {
							             		$amount = $currency_obj->get_currency_symbol($currency_obj->to_currency)." ".$room_price;
							             	}
							             	$current_date = date('Y-m-d');
											$cancell_date = date('Y-m-d',strtotime($value['FromDate']));
											if($cancell_date > $current_date){
												$value['FromDate'] =$value['FromDate']; 
											}else{
												$value['FromDate'] = date('Y-m-d');
											}
							             	$policy_string .='Cancellations made after '.date('d M Y',strtotime($value['FromDate'])).', or no-show, would be charged '.$amount;
							             }
					 				
				 					}
			 					}		 					
			 				$cancel_string .= $policy_string.'<br/> ';
		 				}
	 				
	 				echo $cancel_string;
					//echo $cancellation_details['GetCancellationPolicy']['policy'][0];
				}else{
					$cancel_string ='';
						$cancellation_policy_details = json_decode(base64_decode($get_params['policy_details']));
						//debug($cancellation_policy_details);
						$cancel_count = count($cancellation_policy_details);					
						$cancellation_policy_details = json_decode(json_encode($cancellation_policy_details), True);
						$cancel_reverse = $this->hotel_lib->php_arrayUnique(array_reverse($cancellation_policy_details),'Charge');			
						//$cancel_reverse = array_reverse($cancellation_policy_details);
						
						//debug($cancel_reverse);						
						$cancellation_policy_details = $this->hotel_lib->php_arrayUnique(array_reverse($cancellation_policy_details),'Charge');
						
						if($cancellation_policy_details){
								//$cancellation_policy_details = array_reverse($cancellation_policy_details);
								foreach ($cancellation_policy_details as $key=>$value) {							
										$policy_string ='';								
											if($value['Charge']==0){
												$policy_string .='No cancellation charges, if cancelled before '.date('d M Y',strtotime($value['ToDate']));
											}else{
												if(isset($cancel_reverse[$key+1])){
													if($value['ChargeType']==1){
														$amount = $currency_obj->get_currency_symbol($currency_obj->to_currency)."  ".$value['Charge'];
														
													}elseif ($value['ChargeType']==2) {
														$amount = $currency_obj->get_currency_symbol($currency_obj->to_currency)."  ".$room_price;
													}
													$current_date = date('Y-m-d');
													$cancell_date = date('Y-m-d',strtotime($value['FromDate']));
													if($cancell_date >$current_date){
														$policy_string .='Cancellations made after '.date('d M Y',strtotime($value['FromDate'])).' to '.date('d M Y',strtotime($value['ToDate'])).', would be charged '.$amount;
													}
													
												}else{
													if($value['ChargeType']==1){
														$amount = $currency_obj->get_currency_symbol($currency_obj->to_currency)."  ".$value['Charge'];
														
													}elseif ($value['ChargeType']==2) {
														$amount = $currency_obj->get_currency_symbol($currency_obj->to_currency)."  ".$room_price;
													}
													$current_date = date('Y-m-d');
													$cancell_date = date('Y-m-d',strtotime($value['FromDate']));
													if($cancell_date >$current_date){
														$value['FromDate'] = $value['FromDate'];
													}else{
														$value['FromDate'] = date('Y-m-d');
													}
													$policy_string .='Cancellations made after '.date('d M Y',strtotime($value['FromDate'])).', or no-show, would be charged '.$amount;
												}
											}									
																		
										$cancel_string .= $policy_string.'<br/>';
										
								}
						}else{
							$cancel_string = 'This rate is non-refundable. If you cancel this booking you will not be refunded any of the payment.';
						}
						
					
					echo $cancel_string;
				}
			}else{
				echo "This rate is non-refundable. If you cancel this booking you will not be refunded any of the payment.";
			}
			
		}else{
			echo "This rate is non-refundable. If you cancel this booking you will not be refunded any of the payment.";
		}
		exit;
	}
	/**
	*Load hotels for map
	*/
	function get_all_hotel_list(){
		$response['data'] = '';
		$response['msg'] = '';
		$response['status'] = FAILURE_STATUS;
		$search_params = $this->input->get();		
		$limit = $this->config->item('hotel_per_page_limit');
		if ($search_params['op'] == 'load' && intval($search_params['search_id']) > 0 && isset($search_params['booking_source']) == true) {
			load_hotel_lib($search_params['booking_source']);
			switch($search_params['booking_source']) {
				case PROVAB_HOTEL_BOOKING_SOURCE :
					//getting search params from table
					$safe_search_data = $this->hotel_model->get_safe_search_data($search_params['search_id']);
					//Meaning hotels are loaded first time
					$raw_hotel_list = $this->hotel_lib->get_hotel_list(abs($search_params['search_id']));
					//debug($raw_hotel_list);exit;
					if ($raw_hotel_list['status']) {
						//Converting API currency data to preferred currency
						$currency_obj = new Currency(array('module_type' => 'hotel','from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
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
						$i=0;
						$counter=0;
				    	if ($max_lat == 0) {
							$max_lat = $min_lat = 0;
						}

						if ($max_lon == 0) {
							$max_lon = $min_lon = 0;
						}  
						if($raw_hotel_list['data']['HotelSearchResult']){
							foreach ($raw_hotel_list['data']['HotelSearchResult']['HotelResults'] as $key => $value) {
								$raw_hotel_search_result[$i] =$value;
								$raw_hotel_search_result[$i]['MResultToken']=urlencode($value['ResultToken']);
								 $lat = $value['Latitude'];
								 $lon = $value['Longitude'];
								if(($lat!='')&& ($counter<1)){
									$max_lat = $min_lat = $lat;
								}
								if(($lon!='')){
									$counter++;					
									$max_lon = $min_lon = $lon;
								}

								$i++;
							}
							$raw_hotel_list['data']['HotelSearchResult']['max_lat']  = $max_lat;
							$raw_hotel_list['data']['HotelSearchResult']['max_lon']  = $max_lon;
						}
						$raw_hotel_list['data']['HotelSearchResult']['HotelResults'] =$raw_hotel_search_result; 
						//debug($raw_hotel_list);exit;
						$response['data'] =$raw_hotel_list['data'];					
						$response['status'] = SUCCESS_STATUS;
						
					}
					break;
			}
		}
		$this->output_compressed_data($response);
	}
 /**
	 * sagar 
	 * Get All Cities
	 */
 public function get_city_lists()
 {
     $country_id = $this->input->post('country_id');
          $get_resulted_data =  $this->custom_db->single_table_records('api_city_list', '*',array('country' => $country_id), 0, 100000000, array('destination' => 'asc'));
		   if(!empty($get_resulted_data['data'])){ 
		       $html = "<option value=''>Select City</option>";
		        foreach( $get_resulted_data['data'] as  $get_resulted_data_sub){
		  
		         $html= $html."<option value=".$get_resulted_data_sub['origin'].">".$get_resulted_data_sub['destination']."</option>";
		        } 
		    }else{
		         $html = "<option value=''>No City Found</option>";
		    }
		     echo $html;
		     exit;
		 }
public function get_state_lists()
 {
 	$status = 0;
     $country_id = $this->input->post('country_id');
          $get_resulted_data =  $this->custom_db->single_table_records('state_list', '*',array('country_oid' => $country_id), 0, 100000000, array('origin' => 'asc'));
		   if(!empty($get_resulted_data['data'])){ 
		   	$status = 1;
		       $html = "<option value='0'>Select State</option>";
		        foreach( $get_resulted_data['data'] as  $get_resulted_data_sub){
		  
		         $html= $html."<option value=".$get_resulted_data_sub['origin'].">".$get_resulted_data_sub['en_name']."</option>";
		        } 
		    }else{
		         $html = "<option value='0'>No States Found</option>";
		    }
		     echo $html;
		     exit;
 }
function user_traveller_details()
	{
		$term = $this->input->get('term'); //retrieve the search term that autocomplete sends
		$term = trim($term);
		$result = array();
		$this->load->model('user_model');
		$traveller_details = $this->user_model->user_traveller_details($term)->result();
		$travllers_data = array();
		foreach($traveller_details as $traveller){
			$travllers_data['category'] = 'Travellers';
			$travllers_data['id'] = $traveller->origin;
			$travllers_data['label'] = trim($traveller->first_name.' '.$traveller->last_name);
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
			array_push($result,$travllers_data);
		}
		$this->output_compressed_data($result);
	}
	function removeSpecificOperators($response, $bs)
	{
		//debug($response); exit;
		$block_opts = $this->module_model->manage_bitla_buses1($bs, 1);		
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
		$show_opts = $this->module_model->manage_bitla_buses($bs, 1);		
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
	//bitla_priority
	public function bitla_priority($ets_data,$bitla_data){
		$ets_result = $ets_data;
		$bital_result = json_decode($bitla_data,1);
		//debug($ets_result); exit;
		$k_b = array();
		foreach ($bital_result as $key_bitla => $value_bitla) {
			$k_b[$value_bitla['compare_id']] = $value_bitla;	
		}
		
		$response = array();
		foreach ($ets_result as $key_ets => $value_ets) {
			if (array_key_exists($value_ets['compare_id'],$k_b))
				{
			  		continue;
			  	}
			else
			  	{
			  		$response[] = $value_ets;
			  	}
		}
		//debug($response); exit;
		return $response;
	}
	function get_cancellation_policy_rzl(){
		error_reporting(E_ALL);
		$get_params =$this->input->get();
		//debug($get_params);exit;
		$application_preferred_currency = get_application_currency_preference();
		$application_default_currency = get_application_currency_preference();
		$currency_obj = new Currency(array('module_type' => 'hotel','from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
		

		//debug($get_params);exit;
		if(isset($get_params['booking_source_can'])&&!empty($get_params['booking_source_can'])){
			

			$safe_search_data = $this->hotel_model->get_safe_search_data($get_params['search_id']);	
			load_hotel_lib($get_params['booking_source_can']);
			//debug($safe_search_data);
			$get_params['check_in'] = $safe_search_data['data']['from_date'];
			$get_params['check_out'] = $safe_search_data['data']['to_date'];
			$get_params['country_code'] = $safe_search_data['data']['rz_country_code'];
			$get_params['city_code'] = $safe_search_data['data']['rz_city_code'];
			$cancellation_details = $this->hotel_lib->get_hotel_cancelation_detail_request($get_params);
			//debug($cancellation_details);exit;
			//$room_price = $get_params['room_price'];
			echo $cancellation_details;exit;
			
		}else{
			echo "This rate is non-refundable. If you cancel this booking you will not be refunded any of the payment.";
		}
		exit;
	}

	public function display_ets_opertors($formatted_search_data, $ets_op_id)
	{		
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
		return $ets_resp;		
	}
}
