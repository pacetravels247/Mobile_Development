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

    public function __construct() {
        parent::__construct();
        //$this->output->enable_profiler(TRUE);
        $this->load->model('user_model');
        $this->load->model('db_cache_api');
        $this->load->model('flight_model');
        $this->load->model('bus_model');
        $this->load->model('hotel_model');
        $this->load->model('domain_management_model');
		$this->load->library('booking_data_formatter');
    }

    /**
     * index page of application will be loaded here
     */
    function index() {

        if (is_logged_in_user()) {
		// $this->load->view('dashboard/reminder');	
		redirect(base_url() . 'index.php/menu/bi_reports');
        } else {
            //show login
            echo $this->template->view('general/login', $data = array());
        }
    }

    /**
     * Logout function for logout from account and unset all the session variables
     */
    function initilize_logout() {
        if (is_logged_in_user()) {
            $this->user_model->update_login_manager($this->session->userdata(LOGIN_POINTER));
            $this->session->unset_userdata(array(AUTH_USER_POINTER => '', LOGIN_POINTER => '', DOMAIN_AUTH_ID => '', DOMAIN_KEY => ''));
            redirect('general/index');
        }
    }

    /**
     * oops page of application will be loaded here
     */
    public function ooops() {
        $this->template->view('utilities/404.php');
    }

    /*
     * @domain Key
     */

    public function view_subscribed_emails() {
        $params = $this->input->get();

        $domain_key = get_domain_auth_id();
        if (intval($domain_key) > 0) {
            $data['domain_admin_exists'] = true;
        } else {
            $data['domain_admin_exists'] = false;
        }
        $data['subscriber_list'] = $this->user_model->get_subscribed_emails($domain_key, $params['email']);
        //debug($data['subscriber_list']);exit;
        $this->template->view('user/subscribed_email', $data);
    }

    public function export_subscribed_emails_report($op = '') {
        $params = $this->input->get();
        $domain_key = get_domain_auth_id();
        $data = $this->user_model->get_subscribed_emails($domain_key, $params['email']);

        $export_data = array();
        $data1 = array();
        foreach ($data as $obj) {
            $export_data['email_id'] = $obj->email_id;
            $export_data['status'] = $obj->status;
            $export_data['subscribed_date'] = $obj->subscribed_date;
            $data1[] = $export_data;
        }


        if ($op == 'excel') { // excel export
            $headings = array('a1' => 'Sl. No.',
                'b1' => 'Email_id',
                'c1' => 'Status',
                'd1' => 'Subscribed_date'
            );
            // field names in data set 
            $fields = array('a' => '', // empty for sl. no.
                'b' => 'email_id',
                'c' => 'status',
                'd' => 'subscribed_date'
            );

            $excel_sheet_properties = array(
                'title' => 'Email_subscribition_report' . date('d-M-Y'),
                'creator' => 'Accentria Solutions',
                'description' => 'Email_subscribition_report',
                'sheet_title' => 'Email_subscribition_report'
            );

            $this->load->library('provab_excel'); // we need this provab_excel library to export excel.
            $this->provab_excel->excel_export($headings, $fields, $data1, $excel_sheet_properties);
        }
    }

    public function active_emails($id) {
        $cond['id'] = intval($id);
        $data['status'] = ACTIVE;
        $info = $this->user_model->update_subscribed_emails($data, $cond);

        exit;
    }

    public function deactive_emails($id) {
        $cond['id'] = intval($id);
        $data['status'] = INACTIVE;
        $info = $this->user_model->update_subscribed_emails($data, $cond);

        exit;
    }

    function email_delete($id) {
        if ($id) {
            $this->custom_db->delete_record('email_subscribtion', array('id' => $id));
        }
        redirect('general/view_subscribed_emails');
    }

    function event_location_map() {
        $details = $this->input->get();
        $geo_codes['data']['latitude'] = $details['latitude'];
        $geo_codes['data']['longtitude'] = $details['longtitude'];
        $geo_codes['data']['name'] = 'Event Log Location';
        $geo_codes['data']['ip'] = $details['ip'];
        echo $this->template->isolated_view('general/event_location_map', $geo_codes);
    }

    function test($app_reference) {
        $this->load->model('flight_model');
        echo $this->flight_model->get_extra_services_total_price($app_reference);

        /* $query = 'select * from flight_booking_transaction_details where app_reference="'.$app_reference.'"  order by origin asc';
          $transaction_details = $this->db->query($query)->result_array();
          foreach($transaction_details as $tk => $tv){
          $query = 'select FP.origin, FP.first_name,FP.last_name,concat(FB.description, "-", FB.price) as Baggage
          from flight_booking_passenger_details FP
          left join flight_booking_baggage_details FB on FP.origin=FB.passenger_fk
          where FP.flight_booking_transaction_details_fk='.$tv['origin'].' order by FP.origin';
          $baggae_details = $this->db->query($query)->result_array();

          $query = 'select FP.origin, FP.first_name,FP.last_name,concat(FM.description, "-", FM.price) as Meal
          from flight_booking_passenger_details FP
          left join flight_booking_meal_details FM on FP.origin=FM.passenger_fk
          where FP.flight_booking_transaction_details_fk='.$tv['origin'].' order by FP.origin';
          $meal_details = $this->db->query($query)->result_array();
          echo '<br/>Baggage: ';
          debug($baggae_details);
          echo '<br/>MEALS: ';
          debug($meal_details);
          }
          echo 'DONE'; */
    }

    /* sending the OTP */

    function send_otp($opt = '') {
        $post_data = $this->input->post();
        $data = array();

        $data['user_name'] = $this->db->escape_str(isset($post_data ['email']) ? $post_data ['email'] : '');
        $data['password'] = $this->db->escape_str(isset($post_data ['password']) ? $post_data ['password'] : '');
        $data['status'] = true;
        $data['user_name'] = provab_encrypt($data['user_name']);
        $data['password'] = provab_encrypt(md5($data['password']));
        $user_details = $this->user_model->get_admin_details($data);
        if (!isset($user_details['uuid'])) {
            echo "false";
            return false;
        }
        $this->load->library('provab_mailer');
        $email = $post_data ['email'];
        $random_number = rand(100000, 100000000);
        $mail_template = 'Hello Admin, <br />Please enter the OTP to Login Admin Dashboard:- ' . $random_number;
        $otp_data['OTP'] = $random_number;
        $this->session->set_userdata($otp_data);
        $res = $this->provab_mailer->send_mail($email, domain_name() . ' - Login OTP', $mail_template);
        // debug($res);exit;
        if ($res['status'] == true) {
            echo true;
        } else {
            echo false;
        }
        exit;
    }

    public function change_admin_password() {
        $user_details = $this->custom_db->single_table_records('user', '*', array('user_type' => 1));

        exit;
        foreach ($user_details['data'] as $key => $value) {


            $update = array();
            $condition = array();
            //$update['uuid'] = provab_encrypt($value['uuid']);
            //$update['email'] = provab_encrypt($value['email']);
            //$update['user_name'] = $update['email'];
            $update['password'] = provab_encrypt(md5('Provab@123'));
            $condition['user_id'] = $value['user_id'];

            if ($this->custom_db->update_record('user', $update, $condition)) {
                echo 'ss' . $value['email'] . '<br/>';
            } else {
                echo 'dfal';
            }
        }

        exit;
    }

    public function email_configuration() {
        $encrypt_method = "AES-256-CBC";
        $secret_iv = PROVAB_SECRET_IV;
        $md5_key = PROVAB_MD5_SECRET;
        $encrypt_key = PROVAB_ENC_KEY;
        $page_data = array();
        $email_data = $this->custom_db->single_table_records('email_configuration', '*', array('origin' => 1));

        if ($email_data['status'] == SUCCESS_STATUS) {
            $email_data = $email_data['data'][0];
            $page_data['user_name'] = provab_decrypt($email_data['username']);
            
            $page_data['from'] = $email_data['from'];
            $page_data['host'] = provab_decrypt($email_data['host']);
            $page_data['port'] = provab_decrypt($email_data['port']);
            $page_data['cc'] = provab_decrypt($email_data['cc']);
            $page_data['bcc'] = provab_decrypt($email_data['bcc']);
        }
        if (empty($_POST) == false) {
            $data['username'] = $_POST['username'];
            $data['password'] = $_POST['password'];
            $data['from'] = $_POST['from'];
            $data['host'] = $_POST['host'];
            $data['port'] = $_POST['port'];
            $data['cc'] = $_POST['cc_email'];
            $data['bcc'] = $_POST['bcc_email'];

            $decrypt_password = $this->db->query("SELECT AES_DECRYPT($encrypt_key,SHA2('" . $md5_key . "',512)) AS decrypt_data");

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

            $data1['username'] = $username;
            $data1['password'] = $password;
            $data1['from_name'] = $data['from'];
            $data1['host'] = $host;
            $data1['port'] = $port;
            $data1['cc'] = $cc;
            $data1['bcc'] = $bcc;
            $condition['origin'] = 1;
            // $page_data['message'] = 'Updated Successfully';
            $this->custom_db->update_record('email_configuration', $data1, $condition);
            redirect('general/email_configuration');
        }

        $this->template->view('user/email_configuration.php', $page_data);
    }
	public function show_baggage_meal_seat_form($app_reference, $booking_source, $booking_status, $url_params) {
		$master_booking_details = $this->flight_model->get_booking_details($app_reference, $booking_source, $booking_status);
		//debug($master_booking_details); exit;
		$booking_details = $this->booking_data_formatter->format_flight_booking_data($master_booking_details, 'b2b');
		//debug($booking_details); exit;
		$page_data["booking_details"] = $booking_details["data"]["booking_details"];
		$page_data["app_reference"] = $app_reference;
		$page_data["url_params"] = $url_params;
		$this->template->view('general/offline_baggage_meal_seat', $page_data);
	}
	
	public function save_offline_baggage_meal_seat($app_reference, $url_params)
	{
		$response = json_decode(base64_decode($url_params), true);
		//debug($response); exit;
		$bmss = $this->input->post();
		/* START Baggage, Meals & Seat Entry */
		$bgg_data = array();
		$meal_data = array();
		$seat_data = array();
		$extra_price = 0;
		foreach($bmss AS $bmsk=>$bms)
		{
			$type_seg_pass_arr = explode("_", $bmsk);
			if($type_seg_pass_arr[0]=="bgg")
			{
				$bggamt_index = str_replace("bgg", "bggamt", $bmsk);
				if($bmss[$bggamt_index] > 0){
					$bgg_data["passenger_fk"] = $type_seg_pass_arr[1];
					$bgg_data["from_airport_code"] = $type_seg_pass_arr[2];
					$bgg_data["to_airport_code"] = $type_seg_pass_arr[3];
					$bgg_data["description"] = $bms."KG";
					$bgg_data["price"] = $bmss[$bggamt_index];
					$extra_price += $bgg_data["price"];
					$this->custom_db->insert_record ('flight_booking_baggage_details', $bgg_data);
				}
			}
			if($type_seg_pass_arr[0]=="mlpf")
			{
				$mlamt_index = str_replace("mlpf", "mlamt", $bmsk);
				if($bmss[$mlamt_index] > 0){
					$meal_data["passenger_fk"] = $type_seg_pass_arr[1];
					$meal_data["from_airport_code"] = $type_seg_pass_arr[2];
					$meal_data["to_airport_code"] = $type_seg_pass_arr[3];
					$meal_data["description"] = $bms;
					$meal_data["code"] = "NA";
					$meal_data["price"] = $bmss[$mlamt_index];
					$extra_price += $meal_data["price"];
					$this->custom_db->insert_record ('flight_booking_meal_details', $meal_data);
				}
			}
			if($type_seg_pass_arr[0]=="stno")
			{
				$stamt_index = str_replace("stno", "stamt", $bmsk);
				if($bmss[$stamt_index] > 0){
					$seat_data["passenger_fk"] = $type_seg_pass_arr[1];
					$seat_data["from_airport_code"] = $type_seg_pass_arr[2];
					$seat_data["to_airport_code"] = $type_seg_pass_arr[3];
					$seat_data["description"] = $bms;
					$seat_data["flight_number"] = "NA";
					$seat_data["code"] = "NA";
					$seat_data["price"] = $bmss[$stamt_index];
					$extra_price += $seat_data["price"];
					$this->custom_db->insert_record ('flight_booking_seat_details', $seat_data);
				}
			}
		}
		// Update Agent Balance
		$response["agent_buying"] += $extra_price;
		if(($response['agent_id'] > 0) && ($response['status'] == 'BOOKING_CONFIRMED')){
			$agent_buying = '-'.$response["agent_buying"];
			$transaction_type = 'flight';
			//$fare = $response["agent_buying"]-$admin_markup;
			$fare = $response["agent_buying"] - $response["admin_markup"]; // Shrikant
			$domain_markup = $response["admin_markup"];
			$level_one_markup = $response["agent_markup"];
			$remarks = 'Offline flight Transaction was Successfully done';
			$convinence = 0;
			$discount = 0;
			$currency = 'INR';
			$currency_conversion_rate = 1;
			$transaction_owner_id = $response['agent_id'];
			//=============== Shrikant ==================
			if($extra_price > 0){
				$get_flight_trans_det = $this->domain_management_model->get_flight_transaction_details($app_reference, $extra_price);
			}
			//===========================================
			$this->domain_management_model->save_transaction_details($transaction_type, $app_reference, $fare, $domain_markup, $level_one_markup, $remarks, $convinence, $discount, $currency,$currency_conversion_rate,$transaction_owner_id);
			//$this->domain_management_model->update_agent_balance($agent_buying, $flight_data['agent_id']);
			$this->domain_management_model->update_agent_balance($agent_buying, $transaction_owner_id);
		}
		$status = $response['status'];
		$booking_source = $response["booking_source"];
		redirect('voucher/flight/'.$app_reference.'/'.$booking_source.'/'.$status.'/show_voucher');
		 exit;
		/* END Baggage, Meals & Seat Entry */
	}
	
    //Offline flight booking
    public function offline_flight_book() {
        $page_data = $this->input->post();
        if (valid_array($page_data)) {
            //debug($page_data);die('here');
            $this->load->model ( 'domain_management_model' );
            $total_amount = $page_data['agent_buying_price'];
             $agent_id = $GLOBALS['CI']->entity_user_id;
                     
            if ($total_amount >0) {     
                              
                $app_reference = generate_app_transaction_reference ( FLIGHT_BOOKING );
                $this->domain_management_model->create_track_log($app_reference,'Offline Booking Start- Flight');
                $store_data = $this->flight_model->offline_flight_book($page_data,$app_reference);
                $status = $store_data['status'];
                //debug($store_data); exit;
                $booking_source = $store_data['booking_source'];
				$url_params = base64_encode(json_encode($store_data));
				redirect('general/show_baggage_meal_seat_form/'.$app_reference.'/'.$booking_source.'/'.$status.'/'.$url_params);
                //redirect('voucher/flight/'.$app_reference.'/'.$booking_source.'/'.$status.'/show_voucher');
            } else {
                 echo "error"         ;die;
                $page_data ['low_balance_alert'] = get_message ( 'Balance Is Low. Can Not Proceed.', ERROR_MESSAGE, true, true );
            }
        } else {
            $page_data['sect_num_onward'] = 1;
            $page_data['sect_num_return'] = 0;
            $page_data['adult_count'] = 1;
            $page_data['child_count'] = 0;
            $page_data['infant_count'] = 0;
            $page_data['pax_type_count_onward'][0] = $page_data['adult_count'];
            $page_data['pax_type_count_return'][0] = $page_data['adult_count'];
            $page_data['trip_type'] = 'oneway';
        }

        $page_data['supliers_list'] = $this->domain_management_model->get_flight_suplier_source();
        $page_data['agent_list'] = $this->domain_management_model->agent_list();
        $page_data['airline_list'] = $this->db_cache_api->get_airline_list($from = array('k' => 'code','v' => 'name'));
        //debug($page_data['airline_list']);die();
        $page_data['currency_list'] = $this->db_cache_api->get_currency(array('k' => 'country', 'v' => array('country', 'country')), array('status' => ACTIVE));
        //debug($page_data);die();
		$page_data ['state_list'] = $this->custom_db->get_state_list();
		//exit("Done");
        $this->template->view('general/offline_flight_book', $page_data);
    }

    function get_agent_balance(){
        $agent_id = $_POST['aid'];
        $agent_balance = $this->domain_management_model->get_agent_details($agent_id);
        $agent_balance["email"] = provab_decrypt($agent_balance["email"]);
        if(empty($agent_balance)){
            echo json_encode('b2c');
        }else{
            echo json_encode($agent_balance);
        }
    }

    /** Offline Get Flight Leg **/

    public function get_offline_flight_row($type) {
        $page_data = $this->input->get();
        $page_data['trip_type'] = $type;
        $page_data['airline_list'] = $this->db_cache_api->get_airline_list($from = array('k' => 'code','v' => 'name'));
        echo $this->template->isolated_view('general/offline_flight_row', $page_data);
    }

    /**  Offline Pax row  **/

    public function get_offline_pax_row($type) {
        $page_data = $this->input->get();
        $page_data['pax_type'] = $type;
        echo $this->template->isolated_view('general/offline_pax_row', $page_data);
    }

    /** Offline Fare breakup Calculation * */

    public function offline_fare_calculate() {

        $flight_data = $this->input->post();
        $pax_fare = array();
        $c = 0;
        $agent_id = "0";

        if ($flight_data['booking_type'] == "international") {
            $ModuleType = "flight_int";
        } else {
            $ModuleType = "flight";
        }
        $cm = 0;
        foreach ($flight_data['pax_basic_fare_onward'] as $fk => $fv) {
            $pax_fare['onward']['basic'] = @$pax_fare['onward']['basic'] + ($fv * $flight_data['pax_type_count_onward'][$fk]);
            $pax_fare['onward']['yq'] = @$pax_fare['onward']['yq'] + ($flight_data['pax_yq_onward'][$fk] * $flight_data['pax_type_count_onward'][$fk]);
            $pax_fare['onward']['others'] = @$pax_fare['onward']['others'] + ($flight_data['pax_other_tax_onward'][$fk] * $flight_data['pax_type_count_onward'][$fk]);
            if ($flight_data['trip_type'] == 'circle' && isset($flight_data['pax_basic_fare_return'][$fk])) {
                $pax_fare['return']['basic'] = @$pax_fare['return']['basic'] + ($flight_data['pax_basic_fare_return'][$fk] * $flight_data['pax_type_count_return'][$fk]);
                $pax_fare['return']['yq'] = @$pax_fare['return']['yq'] + ($flight_data['pax_yq_return'][$fk] * $flight_data['pax_type_count_return'][$fk]);
                $pax_fare['return']['others'] = @$pax_fare['return']['others'] + ($flight_data['pax_other_tax_return'][$fk] * $flight_data['pax_type_count_return'][$fk]);
            }
        }
        //debug($pax_fare);
        $t['onward']['career'] = @$flight_data['career_onward'];
        $t['onward']['pax_count'] = array_sum($flight_data['pax_type_count_onward']);
        $f['onward']['basic'] = $pax_fare['onward']['basic'];
        $f['onward']['yq'] = $pax_fare['onward']['yq'];
        $f['onward']['others'] = $pax_fare['onward']['others'];
        if ($flight_data['trip_type'] == 'circle' && valid_array(@$flight_data['career_return'])) {
            $t['return']['pax_count'] = array_sum($flight_data['pax_type_count_onward']);
            $t['return']['career'] = @$flight_data['career_return'];
            $f['return']['basic'] = $pax_fare['return']['basic'];
            $f['return']['yq'] = $pax_fare['return']['yq'];
            $f['return']['others'] = $pax_fare['return']['others'];
        }
        //debug($t);exit;
        $price['api_total_tax'] = 0;
        $price['api_total_basic_fare'] = 0;
        $price['api_total_yq'] = 0;
        $price['service_tax'] = 0;
        $price['meal_and_baggage_fare'] = 0;
        $price['commission'] = 0;
        $price['tds'] = 0;
        $price['admin_commission'] = 0;
        $price['admin_tds'] = 0;
        $price['agent_buying_price'] = 0;
        $price['api_total_selling_price'] = 0;
        $agent_comm = 0;
        $admin_comm = 0;
        foreach ($t as $trp => $tv) {

            $trpc = $trp;
           
            if(@$flight_data['add_admin_markup'] > 0){
                $service_tax = @$flight_data['add_admin_markup'] ;
            }
            else{
                $service_tax = 0;   
            }
            $base_fare = $f[$trpc]['basic'];
            $adm_comm_perc = $flight_data['admin_comm_perc'];
            $admin_comm = ($base_fare/100)*$adm_comm_perc;
            $admin_tds_on_commission = ($admin_comm/100)*5;
            $agt_comm_perc = $flight_data['basic_comm'];
            $agent_comm = ($admin_comm/100)*$agt_comm_perc;
            $agent_tds_on_commission = $agent_comm * 5 / 100;

            $dist_comm = 0;
            $dist_tds_on_commission = 0;
            $total = $f[$trpc]['basic'] + $f[$trpc]['yq'] + $f[$trpc]['others'];
            $tot_markup = ( @$flight_data['add_admin_markup'] + @$flight_data['add_admin_markup'] );
            $buying_price = $total + $service_tax + $tot_markup;
            $agent_buying_price = $f[$trp]['basic'] + $agent_tds_on_commission + $f[$trp]['others'] + $service_tax - $agent_comm;
            $api_total_selling_price = $f[$trp]['basic']+$f[$trp]['yq']+ $f[$trp]['others'] + $service_tax-$admin_comm+$admin_tds_on_commission;


            $price['api_total_tax'] = $price['api_total_tax'] + ($f[$trp]['others']) + $f[$trp]['yq'];
            $price['api_total_basic_fare'] = $price['api_total_basic_fare'] + $f[$trp]['basic'];
            $price['api_total_yq'] = $price['api_total_yq'] + $f[$trp]['yq'];
            $price['service_tax'] = 0;
            $price['meal_and_baggage_fare'] = 0;
            $price['admin_commission'] +=  $admin_comm;
            $price['commission'] +=  $agent_comm;
            $price['tds'] += $agent_tds_on_commission;
            $price['admin_tds'] += $admin_tds_on_commission;
            $price['agent_buying_price'] = $price['api_total_tax'] + $price['api_total_basic_fare']+$price['service_tax']+$price['tds']-$price['commission'];
           
            $price['api_total_selling_price'] +=  $api_total_selling_price;
        }
		$price['meal_and_baggage_fare'] = $flight_data['baggage_amount']+$flight_data['meal_amount'];;
        $price['admin_markup'] = $flight_data['add_admin_markup'];
        $price['commission'] = $price['commission'];
        // $price['tds'] = number_format((float)$price['tds'], 2, '.', '');
        // $price['tds'] = number_format((float)$price['tds'], 2, '.', '');
        $price['agent_buying_price'] = $price['agent_buying_price']+$price['admin_markup']+@$price['meal_and_baggage_fare'];
        $price['agent_buying_price'] = round($price['agent_buying_price']);
        $price['api_total_selling_price'] = round($price['api_total_selling_price']+@$flight_data['add_admin_markup'])+@$price['meal_and_baggage_fare'];

        $gst_details = $GLOBALS['CI']->custom_db->single_table_records('gst_master', '*', array('module' => 'flight'));
        if($gst_details["status"])
        {
            $gst_perc = $gst_details["data"][0]["gst"];
            $service_tax = ($price['admin_markup']/100)*$gst_perc;
        }
        else
            $service_tax = 0;
        
        $price['service_tax'] = $service_tax;
		$price['agent_buying_price'] += $service_tax;
        echo json_encode($price);
        //debug($page_data);
    }

    public function group_booking(){

        //$page_data['request_details'] = $this->domain_management_model->get_group_booking_request();
        $page_data['agency_list'] = $this->domain_management_model->get_agent_list();

        $this->template->view('general/group_booking',$page_data);
    }

    function getLists(){
        $data = $row = array();
        $this->load->model('member');
        // Fetch member's records
        $table = 'group_request';
        $order = array('group_request_id' => 'desc');
        $requested_by = $_GET['requested_by'];
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
            $val["agent_id"] = $member['requested_by'];
            $val['is_quoted'] = $member['is_quoted'];
            if(trim($member['airline_code'])=="0")
                    $member['airline_code'] = "Any Airline";
            if(!$member['is_quoted']){
                $enc_val = json_encode($val);
                $member['action'] = "<a href='#' class='quote_group_booking' 
                data-val='".$enc_val."'>Quote</a>";
            }
            else{
                $val['bf_pp'] = $member['basefare_per_pax'];
                $val['tax_pp'] = $member['tax_per_pax'];
                $enc_val = json_encode($val);
                $member['action'] = "<a href='#' class='quote_group_booking' 
                data-val='".$enc_val."'>Update Quote</a>";
            }
            
            $agent_details = $this->custom_db->single_table_records("user", "*", array("user_id"=> $member["requested_by"]));
            //debug($agent_details); exit;
            $agency_name = $agent_details["data"][0]["agency_name"];
            $agency_id = provab_decrypt($agent_details["data"][0]["uuid"]);
            $agency = $agency_name." - ".$agency_id;

            $data[] = array($i, $member['refernce_no'], $agency, $member['departure'], $member['airline_code'], $member['trip_type'], $member['from_loc'], $member['to_loc'], $member['adults'], $member['children'], $member['infants'],$member['name'],$member['requested_on'], $member['action']);
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

    public function save_group_booking_quote(){
        $form_data = $this->input->post();

        $agent_id = $form_data["agent_id"];
        $refno = $form_data["refno"];
        $post_data["is_quoted"] = 1;
        $post_data["quoted_by"] = $GLOBALS["CI"]->entity_user_id;
        $post_data["quoted_date"] = date("Y-m-d H:i:s");

        $post_data["basefare_per_pax"] = $form_data["basefare_per_pax"];
        $post_data["tax_per_pax"] = $form_data["tax_per_pax"];

        $status = $this->custom_db->update_record("group_request", $post_data, array("refernce_no"=>$refno));
        
        if($status){
            $data["status"]=1;
            $details = "Admin have updated quote for your group booking requested.";
            $user_ids[0]=$agent_id;
            $data["msg"]="Quote saved successfully";
            $this->application_logger->group_booking_quote_update($details, $user_ids, array('na'));
        }
        else{
            $data["status"]=0;
            $data["msg"]="Something went wrong, please try again.";
        }
        echo json_encode($data);
        exit;
    }


    //**Offline Bus Booking**//

    public function offline_bus_book(){
        $page_data = $this->input->post();
        if (valid_array($page_data)) {
            //debug($page_data);die('here');
            $this->load->model ( 'domain_management_model' );
            $total_amount = $page_data['agent_buying_price'];
             $agent_id = $GLOBALS['CI']->entity_user_id;
                     
            if ($total_amount >0) {     
                              
                $app_reference = generate_app_transaction_reference ( BUS_BOOKING );
                $this->domain_management_model->create_track_log($app_reference,'Offline Booking Start- Bus');
                $store_data = $this->bus_model->offline_bus_book($page_data,$app_reference);
                $status = $store_data['status'];
                $booking_source = $store_data['booking_source'];
                redirect('voucher/bus/'.$app_reference.'/'.$booking_source.'/'.$status.'/show_voucher');
            } else {
                 echo "error"         ;die;
                $page_data ['low_balance_alert'] = get_message ( 'Balance Is Low. Can Not Proceed.', ERROR_MESSAGE, true, true );
            }
        } else {
            $page_data['sect_num_onward'] = 1;
            $page_data['sect_num_return'] = 0;
            $page_data['adult_count'] = 1;
            $page_data['child_count'] = 0;
            $page_data['infant_count'] = 0;
            $page_data['pax_type_count_onward'][0] = $page_data['adult_count'];
            $page_data['pax_type_count_return'][0] = $page_data['adult_count'];
            $page_data['trip_type'] = 'oneway';
        }

        $page_data['supliers_list'] = $this->domain_management_model->get_bus_suplier_source();
        $page_data['agent_list'] = $this->domain_management_model->agent_list();
        //debug($page_data['agent_list']);die();
		$page_data["bitla_direct_api_list"] = $this->bus_model->bitla_direct_api_list();
        $page_data['currency_list'] = $this->db_cache_api->get_currency(array('k' => 'country', 'v' => array('country', 'country')), array('status' => ACTIVE));
        $this->template->view('general/offline_bus_book', $page_data);
    }

    /**  Offline Pax row  **/
    public function get_offline_bus_pax_row($type) {
        $page_data = $this->input->get();
        $page_data['pax_type'] = $type;
        echo $this->template->isolated_view('general/offline_bus_pax_row', $page_data);
    }

    public function offline_hotel_book(){
        $page_data = $this->input->post();
        if (valid_array($page_data)) {
            //debug($page_data);die('here');
            $this->load->model ( 'domain_management_model' );
            $total_amount = $page_data['agent_buying_price'];
             $agent_id = $GLOBALS['CI']->entity_user_id;
                     
            if ($total_amount >0) {     
                              
                $app_reference = generate_app_transaction_reference ( HOTEL_BOOKING );
                $this->domain_management_model->create_track_log($app_reference,'Offline Booking Start- Hotel');
                $store_data = $this->hotel_model->offline_hotel_book($page_data,$app_reference);
                $status = $store_data['status'];
                $booking_source = $store_data['booking_source'];
                redirect('voucher/hotel/'.$app_reference.'/'.$booking_source.'/'.$status.'/show_voucher');
            } else {
                 echo "error"         ;die;
                $page_data ['low_balance_alert'] = get_message ( 'Balance Is Low. Can Not Proceed.', ERROR_MESSAGE, true, true );
            }
        } else {
            $page_data['sect_num_onward'] = 1;
            $page_data['sect_num_return'] = 0;
            $page_data['adult_count'] = 1;
            $page_data['child_count'] = 0;
            $page_data['infant_count'] = 0;
            $page_data['pax_type_count_onward'][0] = $page_data['adult_count'];
            $page_data['pax_type_count_return'][0] = $page_data['adult_count'];
            $page_data['trip_type'] = 'oneway';
        }

        $page_data['supliers_list'] = $this->domain_management_model->get_hotel_suplier_source();
        $page_data['agent_list'] = $this->domain_management_model->formatted_agent_list();

        $page_data['currency_list'] = $this->db_cache_api->get_currency(array('k' => 'country', 'v' => array('country', 'country')), array('status' => ACTIVE));
        $this->template->view('general/offline_hotel_book', $page_data);
       /* debug('Coming Soon');
        die();*/
    }

    /**  Offline Pax row  **/
    public function get_offline_hotel_pax_row($type) {
        $page_data = $this->input->get();
        $page_data['pax_type'] = $type;
        echo $this->template->isolated_view('general/offline_hotel_pax_row', $page_data);
    }
}
