<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Transfer extends CI_Controller {

    private $current_module;

    public function __construct() {
        parent::__construct();
        //we need to activate hotel api which are active for current domain and load those libraries
        $this->load->model('transfer_model');
        $this->load->library('social_network/facebook'); //Facebook Library to enable login button		
        $this->current_module = $this->config->item('current_module');
    }

    /**
     * index page of application will be loaded here
     */
    function index() {
        //	echo number_format(0, 2, '.', '');
    }

    /**
     *  Balu A
     * Load Hotel Search Result
     * @param number $search_id unique number which identifies search criteria given by user at the time of searching
     */
    function search($search_id) {
        $safe_search_data = $this->transfer_model->get_safe_search_data($search_id);
        // Get all the hotels bookings source which are active
        $active_booking_source = $this->transfer_model->active_booking_source();

        if ($safe_search_data['status'] == true and valid_array($active_booking_source) == true) {
            $safe_search_data['data']['search_id'] = abs($search_id);
            $this->template->view('transfer/search_result_page', array('hotel_search_params' => $safe_search_data['data'], 'active_booking_source' => $active_booking_source));
        } else {
            $this->template->view('general/popup_redirect');
        }
    }

 

}
