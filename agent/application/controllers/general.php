<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Provab
 * @subpackage General
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V1
 */

class General extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		//$this->output->enable_profiler(TRUE);
		$this->load->model('user_model');
		$this->load->model('Package_Model');
		$this->load->model('custom_db'); 
	}

	/**
	 * index page of application will be loaded here
	 */
	function index($default_view='')
	{
		//echo provab_encrypt(md5(trim("pace@pace247"))); exit;
		if (is_logged_in_user()) {
			//$this->load->view('dashboard/reminder');
			// redirect('menu/index');
			redirect('menu/dashboard/flight?default_view=VHCID1420613784');
		} else {
			//show login
			echo $this->template->view('general/login',$data = array());
		}
	}
	function cms($page_label, $static=0) {
        $page_position = 'Bottom';
        if($static){
        	$this->template->view('cms/'.$page_label, $data);
        	return true;
        }

        if (isset($page_label)) {
            $data = $this->custom_db->single_table_records('cms_pages', 'page_title,page_description,page_seo_title,page_seo_keyword,page_seo_description', array('page_label' => $page_label, 'page_position' => $page_position, 'page_status' => 1));
            $this->template->view('cms/cms', $data);
        } else {
            redirect('general/index');
        }
    }
	/**
	 * Set Search id in cookie
	 */
	private function save_search_cookie($module, $search_id)
	{
		$sparam = array();
		$sparam = $this->input->cookie('sparam', TRUE);
		if (empty($sparam) == false) {
			$sparam = unserialize($sparam);
		}
		$sparam[$module] = $search_id;

		$cookie = array(
			'name' => 'sparam',
			'value' => serialize($sparam),
			'expire' => '1',
			'path' => PROJECT_COOKIE_PATH
		);
		$this->input->set_cookie($cookie);
	}

	 /**
	 * Pre Search For Flight only for top flight destination
	 */
    public function pre_flight_search_ajax()
    {
      $search_params = $this->input->get();
      $redirect_url = base_url().'index.php/general/pre_flight_search?'.$_SERVER['QUERY_STRING'];
      echo $redirect_url;exit;
    }

	/**
	 * Pre Search For Flight
	 */
	function pre_flight_search($search_id='')
	{
		//Global search Data
		$search_id = $this->save_pre_search(META_AIRLINE_COURSE);
		$this->save_search_cookie(META_AIRLINE_COURSE, $search_id);
		//Analytics
		$this->load->model('flight_model');
		$search_params = $this->input->get();
		$this->flight_model->save_search_data($search_params, META_AIRLINE_COURSE);

		redirect('flight/search/'.$search_id.'?'.$_SERVER['QUERY_STRING']);
	}

	/**
	 * Pre Search For Hotel
	 */
	function pre_hotel_search($search_id='')
	{
		//debug($_GET);exit;
		//Global search Data
		$search_id = $this->save_pre_search(META_ACCOMODATION_COURSE);
		$this->save_search_cookie(META_ACCOMODATION_COURSE, $search_id);
		//Analytics
		$this->load->model('hotel_model');
		$search_params = $this->input->get();
		
		$this->hotel_model->save_search_data($search_params, META_ACCOMODATION_COURSE);

		redirect('hotel/search/'.$search_id.'?'.$_SERVER['QUERY_STRING']);
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
	 * Pre Search For Bus
	 */
	function pre_bus_search($search_id='')
	{
		//Global search Data
		$search_id = $this->save_pre_search(META_BUS_COURSE);
		$this->save_search_cookie(META_BUS_COURSE, $search_id);
		//Analytics
		$this->load->model('bus_model');
		$search_params = $this->input->get();
		$this->bus_model->save_search_data($search_params, META_BUS_COURSE);

		redirect('bus/search/'.$search_id.'?'.$_SERVER['QUERY_STRING']);
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
	 * Pre Search For Packages
	 */
	function pre_package_search($search_id='')
	{
		//Global search Data
		$search_id = $this->save_pre_search(META_PACKAGE_COURSE);
		redirect('tours/search'.$search_id.'?'.$_SERVER['QUERY_STRING']);
	}

	/**
	 * Pre Search used to save the data
	 *
	 */
	private function save_pre_search($search_type)
	{
		//Save data
		$search_params = $this->input->get();		
		$search_data = json_encode($search_params);
		$insert_id = $this->custom_db->insert_record('search_history', array('search_type' => $search_type, 'search_data' => $search_data, 'created_datetime' => date('Y-m-d H:i:s')));
		return $insert_id['insert_id'];
	}

	/**
	 * Logout function for logout from account and unset all the session variables
	 */
	function initilize_logout() {
		if (is_logged_in_user()) {
			$this->user_model->update_login_manager($this->session->userdata(LOGIN_POINTER));
			$this->session->unset_userdata(array(AUTH_USER_POINTER => '',LOGIN_POINTER => '', DOMAIN_AUTH_ID => '', DOMAIN_KEY => ''));
			redirect('general/index');
		}
	}
	/**
	 * oops page of application will be loaded here
	 */
	public function ooops()
	{
		$this->template->view('utilities/404.php');
	}

	/*
	 *
	 *Email Subscribtion
	 *
	 */

	public function email_subscription()
	{
		$data = $this->input->get();

		$mail = $data['email'];
		$domain_key = get_domain_auth_id();
		$inserted_id = $this->user_model->email_subscribtion($mail,$domain_key);
		if(isset($inserted_id) && $inserted_id != "already")
		{
			echo "success";
		}elseif($inserted_id=="already"){
			echo "already";
		}else{
			echo "failed";
		}


	}
	/**
	 * Booking Not Allowed Popup
	 */
	function booking_not_allowed()
	{
		$this->template->view('general/booking_not_allowed');
	}
	public function test($app_reference)
	{
		$this->load->model('flight_model');
		$this->load->library('booking_data_formatter');
		$booking_data = $this->flight_model->get_booking_details($app_reference, '');
		$booking_data = $this->booking_data_formatter->format_flight_booking_data($booking_data, 'b2b');
		$amount = $booking_data['data']['booking_details'][0]['agent_buying_price'];
		
	}

	//*--Group booking request & listing start--*//
	public function group_request() {
        $page_data=array();
        $this->template->view('flight/group_request', $page_data);
    }

    function number_foramt(){
        $rep = str_replace(',', '', $this->input->post('value'));
        echo number_format($rep);
    }

    public function save_group_request() {
       
        $this->load->library('booking_data_formatter');

        $x = $this->input->post();
        //debug($x);exit;
        if (empty($x['adult_num'])) {
            $adult = 0;
        } else {
            $adult = $x['adult_num'];
        }
        if (empty($x['child_num'])) {
            $child = 0;
        } else {
            $child = $x['child_num'];
        }
        if (empty($x['infant_num'])) {
            $infant = 0;
        } else {
            $infant = $x['infant_num'];
        }

        $curdate = db_current_datetime();

        $departure = date("Y-m-d", strtotime($x['depature']));
        $departure_dt = date("d-m-Y", strtotime($x['depature']));

        if (isset($x['return'])) {
            $return = date("Y-m-d", strtotime($x['return']));
            $rtn_dt = date("d-m-Y", strtotime($x['return']));
        } else {
            //echo 'xz';exit;
            $return = NULL;
        }
        $ref_no = generate_app_transaction_reference('FGR');

        $grouprequest = array(
            'refernce_no' => $ref_no,
            'airline_code' => $x['airline_code'],
            'trip_type' => $x['trip_type'],
            'class_type' => $x['v_class'],
            'from_loc' => $x['from'],
            'to_loc' => $x['to'],
            'departure' => $departure,
            'return_date' => $return,
            'adults' => $adult,
            'children' => $child,
            'infants' => $infant,
            'expected_fare' => $x['fare'],
            'name' => $x['name'],
            'remarks' => $x['remarks'],
            'contact_number' => $x['contact'],
            'email_id' => $x['email_id'],
            'requested_by' => $this->entity_user_id,
            'requested_on' => $curdate,
            'is_quoted' => 0,
            'quoted_by' => 0,
            'quoted_date' => NULL,
            'basefare_per_pax' => 0,
            'tax_per_pax' => 0,
        );
        $message = '';
        $message .='';
        $message .='';
        $email = $x['email_id']; 
        // debug($res); exit;

        $package = $this->Package_Model->add_group_request($grouprequest);

        //********************send mail**************************************************
        //***********************************************************************//ashwini
        $agency_name = $this->agency_name;
        $agency_id = $this->entity_uuid;
        $this->load->library ('provab_mailer');
        $to_email = 'deepashri@pacetravels.in,tabrez@pacetravels.in,sayyed@pacetravels.in';
        $mail_template = '<b>Dear Team</b>,<br/>Agent has requested for flight group quotation. Below are the flight details and agency details.<br/><br/><br/><b>Agency Details</b><br/>1) Agency Name : '.$agency_name.' ['.$agency_id.']<br/>2) Contact Number: '.$grouprequest['contact_number'].'<br/>3) Email: '.$grouprequest['email_id'].'<br/><br/><br/><b>Flight Details</b><br/>Trip Type: '.$grouprequest['trip_type'].'<br/>Sector: '.$grouprequest['from_loc'].' - '.$grouprequest['to_loc'].'<br/>Journey Date: '.$departure_dt.'<br/>Return Date: '.$rtn_dt.'<br/>Pax: ADT - '.$grouprequest['adults'].' / CHD - '.$grouprequest['children'].' / INF - '.$grouprequest['infants'].'';
		$pdf = ''; 
		$this->provab_mailer->send_mail($to_email, domain_name().' - Group Booking Request', $mail_template ,$pdf);
        //echo $this->Package_Model->custom_db->db->last_query(); exit;
        $this->session->set_flashdata("msg", "<div class='alert alert-success'>Group request submitted</div>");
        //$this->template->view('flight/group_request', $page_data);
        redirect('general/group_booking');
    }

    public function group_booking(){
        $this->template->view('flight/group_booking_list',$page_data);
    }

    function getLists(){
        $data = $row = array();
        $this->load->model('member');
        // Fetch member's records
        $table = 'group_request';
        $order = array('group_request_id' => 'desc');
        $requested_by = $GLOBALS['CI']->entity_user_id;
        $filter_date = $_GET['filter_date'];
        $condition = '';
        if(!empty($requested_by)){
            $condition = array('requested_by','=',$requested_by);
        }else if(!empty($filter_date)){
             $condition = $this->date_filter($filter_date);   
            //$condition = array('requested_on' => $filter_date);
        }else{
            //$condition = array('1'=>'1');
        }
           //debug($condition);die();
        $request_details = array(
                'table'=>$table,
                'order'=>$order,
                'condition'=> $condition
            );
        $this->member->request_details($request_details);
        $memData = $this->member->getRows($_POST);
        $i = $_POST['start'];
        foreach($memData as $member){
            $i++;
            $val['is_quoted'] = $member['is_quoted'];
            if(trim($member['airline_code'])=="0")
            		$member['airline_code'] = "Any Airline";
            if($member['is_quoted'])
            	{
                $val['bf_pp'] = $member['basefare_per_pax'];
                $val['tax_pp'] = $member['tax_per_pax'];
                $enc_val = json_encode($val);
                $member['action'] = "<a href='#' class='view_group_booking_quote' 
                data-val='".$enc_val."'>View Quote</a>";
            }
            else
            	$member['action'] = "Quote Awaited</a>";
            
            $data[] = array($i, $member['refernce_no'], $member['airline_code'], $member['trip_type'], $member['from_loc'], $member['to_loc'], $member['adults'], $member['children'], $member['infants'],$member['name'],$member['requested_on'], $member['action']);
        }
        
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->member->countAll(),
            "recordsFiltered" => $this->member->countFiltered($_POST),
            "data" => $data,
        );

        echo json_encode($output);
    }

    function date_filter($filter_date){
        $today_date = date('Y-m-d');

        if(!empty($filter_date)){
            if($filter_date == 'today_booking_data'){
                $condition = array('date(requested_on)', '=',"'$today_date'");
            }else if($filter_date == 'last_day_booking_data'){
                $condition = array('date(requested_on)', '=',"'$today_date'- INTERVAL 1 DAY");
            }else if($filter_date == 'week_booking_data'){
                $condition = array('date(requested_on)', '>=',"'$today_date'- INTERVAL 6 DAY");
            }else if($filter_date == 'month_booking_data'){
                $condition = array('date(requested_on)', '>=',"'$today_date'- INTERVAL 30 DAY");
            }
            
            return $condition;
        }


    }
    //*--Group booking request & listing end--*//
	
	
	
	//*sanchitha*//
	//*Quick enquiry form on footer*//
	public function quick_form(){
	
		$this->load->model('Package_Model');
		$page_data['agent_id']=$this->entity_user_id;
		$page_data['agent_name']=$this->entity_firstname;
		$page_data['country_list']=$this->Package_Model->tours_country_name();
		$page_data['city_list']=$this->Package_Model->tours_city_name();
		//debug($page_data);
        $this->template->view('holiday/quick_form',$page_data);
    }

    function fare_calender($offset=0)
    {
    	//debug($GLOBALS["CI"]); exit;
        $condition = "where pan_no = '".$GLOBALS["CI"]->entity_pan_number."' ";
        $this->load->library('pagination');
        $config['base_url'] = base_url().'index.php/management/tds_certificates/';
        $page_data['total_rows'] = $config['total_rows'] = 0;
        $config['per_page'] = RECORDS_RANGE_3;
        $this->pagination->initialize($config);
        /** TABLE PAGINATION */
        $this->template->view('management/fare_calender', $page_data);
    }
}
