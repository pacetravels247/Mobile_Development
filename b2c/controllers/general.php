<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *
 * @package    Provab
 * @subpackage General
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V1
 */
class General extends CI_Controller {
    /* function dxb_hotels()
      {
      $HotelSearchResult = (json_decode(file_get_contents('DXB.json'), true));
      $table = '<table border=1>';
      $table .= '<tr>';
      $table .= '<th>Sno</th>';
      $table .= '<th>Hotel Name</th>';
      $table .= '<th>Location</th>';
      $table .= '</tr>';
      $hotel_list = array();
      foreach ($HotelSearchResult['HotelSearchResult']['HotelResults'] as $k => $v) {
      $hotel_list[$v['HotelName']] = $v['HotelAddress'];
      }
      file_put_contents('dxb_hotel.json', json_encode($hotel_list));
      $index = 1;
      ksort($hotel_list);
      foreach ($hotel_list as $k => $v) {
      $table .= '<tr>';
      $table .= '<td>'.($index++).'</td>';
      $table .= '<td>'.$k.'</td>';
      $table .= '<td>'.$v.'</td>';
      $table .= '</tr>';
      }
      $table .= '</table>';
      } */
    /*
      function static_hotel()
      {
      $this->load->model('hotel_model');
      $search_response = $this->hotel_model->get_static_response(28);
      debug($search_response);
      exit;
      }

      function static_bus()
      {
      $this->load->model('bus_model');
      $search_response = $this->bus_model->get_static_response(1);
      debug($search_response);
      exit;
      }

      function static_bus_seats()
      {
      $this->load->model('bus_model');
      $search_response = $this->bus_model->get_static_response(2);
      debug($search_response);
      exit;
      }

      function static_flight()
      {
      $this->load->model('flight_model');
      $search_response = $this->flight_model->get_static_response(533);
      debug($search_response);
      exit;
      } */

    public function __construct() {
        parent::__construct();
        //$this->output->enable_profiler(TRUE);
        $this->load->model('user_model');
        $this->load->model('Package_Model');
        $this->load->model('custom_db');
    }

    function test1() {
        $post = $this->input->post();
        $post['lang'] = 'hi';
        $this->session->set_userdata('lang', $post['lang']);
        //echo $this->session->userdata('some_name');
    }

    /**
     * index page of application will be loaded here
     */
    function index($default_view = '') {

        /* Package Data */
        $data['caption'] = $this->Package_Model->getPageCaption('tours_packages')->row();
        $data['packages'] = $this->Package_Model->getAllPackages();
        $data['countries'] = $this->Package_Model->getPackageCountries_new();
        $data['package_types'] = $this->Package_Model->getPackageTypes();
        /* Banner_Images */
        $domain_origin = get_domain_auth_id();
        $page_data['banner_images'] = $this->custom_db->single_table_records('banner_images', '*', array('added_by' => $domain_origin, 'status' => '1'), '', '100000000', array('banner_order' => 'ASC'));
        /* Package Data */
        //echo $this->db->last_query();exit;
        //debug($page_data['banner_images']);exit;
        //debug($data);exit;
        $page_data['default_view'] = @$_GET['default_view'];
        $page_data['holiday_data'] = $data; //Package Data

        if (is_active_airline_module()) {
            $this->load->model('flight_model');
        }
        if (is_active_bus_module()) {
            $this->load->model('bus_model');
        }
        if (is_active_hotel_module()) {
            $this->load->model('hotel_model');
            $page_data['top_destination_hotel'] = $this->hotel_model->hotel_top_destinations();

            //$page_data['top_destination_flight'] = $this->flight_model->flight_top_destinations();
        }
         if (is_active_car_module()) {
          $this->load->model('car_model');
         }
        //debug($page_data);exit;
        if (is_active_package_module()) {
            $this->load->model('package_model');
            $top_package = $this->package_model->get_package_top_destination();
            $page_data['top_destination_package'] = $top_package['data'];
            // debug($page_data['top_destination_package']);
            // exit;
            $page_data['total'] = $top_package['total'];
        }
        $currency_obj = new Currency(array('module_type' => 'hotel', 'from' => get_api_data_currency(), 'to' => get_application_currency_preference()));
        $page_data['currency_obj'] = $currency_obj;
        $getSlideImages = $page_data['banner_images']['data'];
        //debug($getSlideImages);exit;
        $slideImageArray = array();

        foreach ($getSlideImages as $k) {
            $slideImageArray[] = array('image' => $GLOBALS['CI']->template->template_images() . $k['image'], 'title' => $k['title'], 'description' => $k['subtitle']);
        }
        $page_data['slideImageJson'] = $slideImageArray;
        $get_promocode_list = $this->get_promocode_list();
        // debug($get_promocode_list);exit;
        $page_data['promo_code_list'] = $get_promocode_list;

        //for getting the headings
        $headings = $this->custom_db->single_table_records('home_page_headings', '*', array('status' => '1'));
        //top airlines
        $top_airlines = $this->custom_db->single_table_records('top_airlines', '*', array('status' => '1'));
        //tour styles
        $tour_styles = $this->custom_db->single_table_records('tour_styles', '*', array('status' => '1'));
        //domain data
        $domain_data = $this->custom_db->single_table_records('domain_list', '*', array('status' => '1'));
     
        $headings_array = array();
        if($headings['status'] == true){
           foreach($headings['data'] as $heading){
            $headings_array[] = $heading['title'];
          }
        }
        $features = $this->custom_db->single_table_records('why_choose_us', '*', array('status' => '1'));
        $page_data['headings'] = $headings_array;
        $page_data['top_airlines'] = $top_airlines;
        $page_data['features'] = $features;
        $page_data['tour_styles'] = $tour_styles;
        $page_data['domain_data'] = $domain_data;
        //$slideImageJson = json_encode($slideImageArray);
        //$page_data['slideImageJson'] = $array_final = preg_replace('/"([a-zA-Z]+[a-zA-Z0-9_]*)":/','$1:',$slideImageJson);
        //debug($data['slideImageJson']);exit;
        /* header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
          header("Cache-Control: post-check=0, pre-check=0", false);
          header("Pragma: no-cache"); */
        // debug($page_data);exit;
        $this->template->view('general/index', $page_data);
    }

    /**
     * Set Search id in cookie
     */
    private function save_search_cookie($module, $search_id) {
        $sparam = array();
        $sparam = $this->input->cookie('sparam', TRUE);
        if (empty($sparam) == false) {
            $sparam = unserialize($sparam);
        }
        $sparam[$module] = $search_id;

        $cookie = array(
            'name' => 'sparam',
            'value' => serialize($sparam),
            'expire' => '86500',
            'path' => PROJECT_COOKIE_PATH
        );
        $this->input->set_cookie($cookie);
    }

    /**
     * Pre Search For Flight
     */
    function pre_flight_search($search_id = '') {
        $search_params = $this->input->get();

        //Global search Data
        $search_id = $this->save_pre_search(META_AIRLINE_COURSE);
        $this->save_search_cookie(META_AIRLINE_COURSE, $search_id);

        //Analytics
        $this->load->model('flight_model');
        $this->flight_model->save_search_data($search_params, META_AIRLINE_COURSE);
        redirect('flight/search/' . $search_id . '?' . $_SERVER['QUERY_STRING']);
    }
    /**
     * Pre Search For Car
     */
    function pre_car_search($search_id = '') {
        $search_params = $this->input->get();
        // debug($search_params);exit;
        //Global search Data
        $search_id = $this->save_pre_search(META_CAR_COURSE);
        $this->save_search_cookie(META_CAR_COURSE, $search_id);

        //Analytics
        $this->load->model('car_model');
        $this->car_model->save_search_data($search_params, META_CAR_COURSE);
        redirect('car/search/' . $search_id . '?' . $_SERVER['QUERY_STRING']);
    }
    /**
     * Pre Search For Hotel
     */
    function pre_hotel_search($search_id = '') {
        //Global search Data
        //debug($this->session);exit;
        $search_id = $this->save_pre_search(META_ACCOMODATION_COURSE);
        $this->save_search_cookie(META_ACCOMODATION_COURSE, $search_id);

        //Analytics
        $this->load->model('hotel_model');
        $search_params = $this->input->get();
         /*debug($search_params);
         exit('//');*/
        $this->hotel_model->save_search_data($search_params, META_ACCOMODATION_COURSE);

        redirect('hotel/search/' . $search_id . '?' . $_SERVER['QUERY_STRING']);
    }

    /**
     * Pre Search For Bus
     */
    function pre_bus_search($search_id = '') {
        //Global search Data
        $search_id = $this->save_pre_search(META_BUS_COURSE);
        $this->save_search_cookie(META_BUS_COURSE, $search_id);

        //Analytics
        $this->load->model('bus_model');
        $search_params = $this->input->get();
      
        $this->bus_model->save_search_data($search_params, META_BUS_COURSE);

        redirect('bus/search/' . $search_id . '?' . $_SERVER['QUERY_STRING']);
    }

    /**
     * Pre Search For Packages
     */
    function pre_package_search($search_id = '') {
        //Global search Data
        $search_id = $this->save_pre_search(META_PACKAGE_COURSE);
        redirect('tours/search' . $search_id . '?' . $_SERVER['QUERY_STRING']);
    }

    /**
  * Pre Search for SightSeen
  */
  function pre_sight_seen_search($search_id=''){

    $search_id = $this->save_pre_search(META_SIGHTSEEING_COURSE);
    $this->save_search_cookie(META_SIGHTSEEING_COURSE, $search_id);
    //Analytics
    $this->load->model('sightseeing_model');
    $search_params = $this->input->get();
    // debug($search_params);
    // exit;
    $this->sightseeing_model->save_search_data($search_params, META_SIGHTSEEING_COURSE);
    
    redirect('sightseeing/search/'.$search_id.'?'.$_SERVER['QUERY_STRING']);
  }

  /*
  *Pre Transfer Search
  */
  function pre_transferv1_search($search_id=''){
    $search_id = $this->save_pre_search(META_TRANSFERV1_COURSE);
    $this->save_search_cookie(META_TRANSFERV1_COURSE, $search_id);
    //Analytics
    $this->load->model('transferv1_model');
    $search_params = $this->input->get();
    
    $this->transferv1_model->save_search_data($search_params, META_TRANSFERV1_COURSE);
    
    redirect('transferv1/search/'.$search_id.'?'.$_SERVER['QUERY_STRING']);
  }

    /**
     * Pre Search For Transfer
     */
    function pre_transfer_search($search_id = '') {
        //Global search Data
        //debug($this->session);exit;
        $search_id = $this->save_pre_search(META_TRANSFER_COURSE);
        $this->save_search_cookie(META_TRANSFER_COURSE, $search_id);

        //Analytics
        $this->load->model('transfer_model');
        $search_params = $this->input->get();

       $this->transfer_model->save_search_data($search_params, META_TRANSFER_COURSE);

        redirect('transfer/search/' . $search_id . '?' . $_SERVER['QUERY_STRING']);
    }
    
    
    /**
     * Pre Search used to save the data
     *
     */
    private function save_pre_search($search_type) {
        //Save data
        $search_params = $this->input->get();
        //debug($search_params);exit('==');
        $search_data = json_encode($search_params);
        $insert_id = $this->custom_db->insert_record('search_history', array('search_type' => $search_type, 'search_data' => $search_data, 'created_datetime' => date('Y-m-d H:i:s')));
        return $insert_id['insert_id'];
    }

    /**
     * oops page of application will be loaded here
     */
    public function ooops() {
        $this->template->view('utilities/404.php');
    }

    /*
     * Activating User Account.
     * Account get activated only when the url is clicked from the account_activation_mail
     */

    function activate_account_status() {
        $origin = $this->input->get('origin');
        $unsecure = substr($origin, 3);
        $secure_id = base64_decode($unsecure);
        $status = ACTIVE;
        $this->user_model->activate_account_status($status, $secure_id);
        redirect(base_url());
    }

    /**
     * Email Subscribtion
     *
     */
    public function email_subscription() {
        $data = $this->input->post();

        $mail = $data['subEmail'];
        $domain_key = get_domain_auth_id();
        $inserted_id = $this->user_model->email_subscribtion($mail, $domain_key);
        if (isset($inserted_id) && $inserted_id != "already") {
            $this->application_logger->email_subscription($mail);
            $pdata['status'] = 1;
            echo json_encode($pdata);
        } elseif ($inserted_id == "already") {
            $pdata['status'] = 0;
            echo json_encode($pdata);
        } else {
            $pdata['status'] = 2;
            echo json_encode($pdata);
        }
    }

    function cms($page_label) {
        $page_position = 'Bottom';

        if (isset($page_label)) {
            $data = $this->custom_db->single_table_records('cms_pages', 'page_title,page_description,page_seo_title,page_seo_keyword,page_seo_description', array('page_label' => $page_label, 'page_position' => $page_position, 'page_status' => 1));
            $this->template->view('cms/cms', $data);
        } else {
            redirect('general/index');
        }
    }

    function offline_payment() {
        $params = $this->input->post();
        $gotback = $this->user_model->offline_payment_insert($params);
        $url = base_url() . 'index.php/general/offline_approve/' . $gotback['refernce_code'];
       

        print_r(json_encode($gotback['refernce_code']));
    }

    function offline_approve($code) {//apporval by mail
        $result['data'] = $this->user_model->offline_approval($code);
        $this->template->view('general/pay', $result);
    }

    /**
     * Booking Not Allowed Popup
     */
    function booking_not_allowed() {
        $this->template->view('general/booking_not_allowed');
    }

    function test() {
        echo 'test function';
    }

  function update_citylist()
  {
    $total= 80;
    for($num=0;$num<=$total;$num++){
      $city_response = file_get_contents(FCPATH."test-export-2017-2-27/destinations-".$num.".json");
     
      $city_list = json_decode($city_response,true);
      // debug($city_list);exit;
      foreach ($city_list as $key => $value) {
        $insert_list['country_code'] = $value['country'];
        $insert_list['city_name'] = html_entity_decode($value['name']);
        $insert_list['city_code'] = $value['code'];
        $insert_list['parent_code'] = $value['parent'];
        $insert_list['latitude']  = $value['latitude'];
        $insert_list['longitude'] = $value['longitude'];
        $this->custom_db->insert_record('hotelspro_citylist',$insert_list);
      }
    }
      
  }
    //get promocode
    private function get_promocode_list(){
      $promocode_arr = array();
      $date = date('Y-m-d');
      $list= $this->custom_db->single_table_records('promo_code_list','*',array('status'=>ACTIVE,'display_home_page' => 'Yes','expiry_date >=' => $date));
      if($list['status']==true){
        $promocode_arr = $list['data'];
      }
      return $promocode_arr;
      }
    public function insert_api_data(){

    // $this->dec_insert_api_data();
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $api_data = $this->custom_db->single_table_records('email_configuration', '*');
    $secret_iv = PROVAB_SECRET_IV;
    // debug($api_data);exit;
    if($api_data['status'] == true){
      foreach($api_data['data'] as $data){
        if(!empty($data['username'])){
          $md5_key = PROVAB_MD5_SECRET;
          $encrypt_key = PROVAB_ENC_KEY;
          $decrypt_password = $this->db->query("SELECT AES_DECRYPT($encrypt_key,SHA2('".$md5_key."',512)) AS decrypt_data");
          
          $db_data = $decrypt_password->row();
         
          $secret_key = trim($db_data->decrypt_data); 
          $key = hash('sha256', $secret_key);
          $iv = substr(hash('sha256', $secret_iv), 0, 16);
          $username = openssl_encrypt($data['username'], $encrypt_method, $key, 0, $iv);
          $username = base64_encode($username);

          $password = openssl_encrypt($data['password'], $encrypt_method, $key, 0, $iv);
          $password = base64_encode($password);

          $host = openssl_encrypt($data['host'], $encrypt_method, $key, 0, $iv);
          $host = base64_encode($host);

          $cc = openssl_encrypt($data['cc'], $encrypt_method, $key, 0, $iv);
          $cc = base64_encode($cc);

          $port = openssl_encrypt($data['port'], $encrypt_method, $key, 0, $iv);
          $port = base64_encode($port);

          $bcc = openssl_encrypt($data['bcc'], $encrypt_method, $key, 0, $iv);
          $bcc = base64_encode($bcc);
        
          $api_config_data['from'] = $data['from'];
          $api_config_data['domain_origin'] = $data['domain_origin'];
          $api_config_data['username'] = $username;
          $api_config_data['password'] = $password;
          $api_config_data['host'] = $host;
          $api_config_data['cc'] = $cc;
          $api_config_data['port'] = $port;
          $api_config_data['bcc'] = $bcc;
          $api_config_data['status'] = $data['status'];
          
          // debug($api_config_data);exit;
          $this->custom_db->insert_record('email_configuration_new',$api_config_data);
          
        }
      }
    }
    exit;
  }
  public function insert_api_urls(){
    // error_reporting(E_ALL);
    $output = false;
    $encrypt_method = "AES-256-CBC";
   // $api_urls = $this->custom_db->single_table_records('api_urls', '*');
   
    $secret_iv = PROVAB_SECRET_IV;
    
    //if($api_urls['status'] == true){
     // foreach($api_urls['data'] as $data){
    $data['system'] = 'Test';
    $data['urls'] = '{
  "flight_url": "http://13.235.166.22/service/webservices/index.php/flight/service/",
  "hotel_url": "http://test.services.travelomatix.com/webservices/hotel_v3/service/",
  "bus_url": "http://test.services.travelomatix.com/webservices/bus/service/",
  "activity_url": "http://test.services.travelomatix.com/webservices/sightseeing/service/",
  "transfer_url": "http://test.services.travelomatix.com/webservices/transferv1/service/",
  "car_url": "http://test.services.travelomatix.com/webservices/car/service/",
  "external_service": "http://test.services.travelomatix.com/webservices/index.php/rest/"
}';

        //if(!empty($data)){
          $md5_key = PROVAB_MD5_SECRET;
          $encrypt_key = PROVAB_ENC_KEY;
          $decrypt_password = $this->db->query("SELECT AES_DECRYPT($encrypt_key,SHA2('".$md5_key."',512)) AS decrypt_data");
          
          $db_data = $decrypt_password->row();
         
          $secret_key = trim($db_data->decrypt_data); 
          $key = hash('sha256', $secret_key);
          $iv = substr(hash('sha256', $secret_iv), 0, 16);
          $api_urls_data = openssl_encrypt($data['urls'], $encrypt_method, $key, 0, $iv);
          $urls_data = base64_encode($api_urls_data);
          $api_data['system'] = $data['system'];
          $api_data['urls'] = $urls_data;
          $api_data['status'] = '1';
          $this->custom_db->insert_record('api_urls_new',$api_data);
        //}
      //}
    //}
  }
  public function decrypt_api_urls(){
    // error_reporting(E_ALL);
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $api_urls = $this->custom_db->single_table_records('api_urls_new', '*');
   
    $secret_iv = PROVAB_SECRET_IV;
    
    if($api_urls['status'] == true){
      foreach($api_urls['data'] as $data){
        
        if(!empty($data)){
          $md5_key = PROVAB_MD5_SECRET;
          $encrypt_key = PROVAB_ENC_KEY;
          $decrypt_password = $this->db->query("SELECT AES_DECRYPT($encrypt_key,SHA2('".$md5_key."',512)) AS decrypt_data");
          
          $db_data = $decrypt_password->row();
         
          $secret_key = trim($db_data->decrypt_data); 
          $key = hash('sha256', $secret_key);
          $iv = substr(hash('sha256', $secret_iv), 0, 16);
          $urls = openssl_decrypt(base64_decode($data['urls']), $encrypt_method, $key, 0, $iv);
          debug($urls);exit;
        }
      }
    }
  }

  //removing cache data
  function remove_cache_data(){
    //removing files from extras folder
      $files = glob('extras/custom/TMX1512291534825461/tmp/*'); // get all file names
      foreach($files as $file){ // iterate files
          if(is_file($file)) {
            unlink($file); // delete file
          }
      }

      //removing files from b2c cache folder
      $files = glob('b2c/cache/*'); // get all file names
      foreach($files as $file){ // iterate files
          if(is_file($file)) {
            unlink($file); // delete file
          }
      }

      //removing files from agent cache folder
      $files = glob('agent/application/cache/*'); // get all file names
      foreach($files as $file){ // iterate files
          if(is_file($file)) {
            unlink($file); // delete file
          }
      }
  }
  //deleting the data from test(pace_travel_1) table older than 5 days data
  public function remove_cache_test_db(){
    $current_date = date('Y-m-d H:i:s');
    $date = date('Y-m-d H:i:s', strtotime($current_date . ' -2 days'));
    $delete_query = 'delete FROM `test` WHERE time < "'.$date.'"';
    $delete_query_exec = $this->db->query($delete_query);
  }
  
  
}
