<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Activity extends CI_Controller {

    private $current_module;

    public function __construct() {
        parent::__construct();
        //we need to activate hotel api which are active for current domain and load those libraries
       // $this->load->model('transfer_model');
      //  $this->load->library('social_network/facebook'); //Facebook Library to enable login button		
        $this->current_module = $this->config->item('current_module');
    }

    /**
     * index page of application will be loaded here
     */
    function index() {
        //	echo number_format(0, 2, '.', '');
    }

    
    //function search($search_id) {
    function search() {
        
        
         $this->template->view('activity/search_result_page');
    }

}
