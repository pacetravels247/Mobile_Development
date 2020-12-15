<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
/**
 *
 * @package Provab - Provab Application
 * @subpackage Travel Portal
 * @author Balu A<balu.provab@gmail.com> on 01-06-2015
 * @version V2
 */
class Management extends CI_Controller {
	private $current_module;
	public function __construct() {
		parent::__construct ();
		$this->load->model ( 'domain_management_model' );
		$this->load->model ( 'bus_model' );
		$this->load->model ( 'hotel_model' );
		$this->load->model ( 'flight_model' );
		$this->load->model('sightseeing_model');

		$this->load->library('booking_data_formatter');
		$this->load->helper('custom/transaction_log');
		$this->current_module = $this->config->item('current_module');
	}
	public function index()
	{
		redirect(base_url());
	}
	function delete_ad()
	{
		$temp_profile_image = $this->template->domain_image_full_path($icon);
		$id = $this->input->post("id");
		$status = $this->custom_db->delete_record("advertisement", array("id" => $id));
		//echo $this->custom_db->db->last_query(); exit;
		if($status)
			echo "{'status': 1}";
		else
			echo "{'status': 0}";
		exit;
	}
	/**
	 * Balu A
	 * Manage domain markup for provab - Domain wise and module wise
	 */
	function b2b_airline_markup() {
		error_reporting(E_ALL);
		$markup_module_type = 'b2b_flight';

		$data_list = $this->domain_management_model->get_agent_airline_markup_details ();
		$airline_list = $this->db_cache_api->get_airline_list();
		$data_list['airline_list'] = $airline_list;
		$data_list['markup_limits'] = $this->db->where(array('module_type' =>'flight'))->get('agent_markup_limit')->result_array();

		$page_data ['form_data'] = $this->input->post ();
		// debug($page_data);exit;
		if (valid_array ( $page_data ['form_data'] ) == true) {
			switch ($page_data ['form_data'] ['form_values_origin']) {
				case 'generic' :
				if($data_list['markup_limits'][0]['value_type']==$page_data['form_data']['value_type'])
				{
					if($data_list['markup_limits'][0]['value'] >= $page_data['form_data']['generic_value']){
						$this->domain_management_model->save_markup_data ( $page_data ['form_data'] ['markup_origin'], $page_data ['form_data'] ['form_values_origin'], $markup_module_type, 0, $page_data ['form_data'] ['generic_value'], $page_data ['form_data'] ['value_type'], get_domain_auth_id (), $page_data['form_data']['generic_value_i'], $page_data['form_data']['value_type_i'] );
					}
					else{
						$this->session->set_flashdata("msg", "<div class='alert alert-danger'>System accepts markup values under Rs. ".$data_list['markup_limits'][0]['value']."/-</div>");
					}
				}
					break;
				case 'specific' :
					if (valid_array ( $page_data ['form_data'] ['airline_origin'] )) {
						foreach ( $page_data ['form_data'] ['airline_origin'] as $__k => $__domain_origin ) {

							if(($data_list['markup_limits'][0]['value'] >= $page_data['form_data']['specific_value'][$__k]) && ($data_list['markup_limits'][0]['value_type']==$page_data['form_data']['value_type_' . $__domain_origin]) || ($data_list['markup_limits'][0]['value'] >= $page_data['form_data']['specific_value_i'][$__k]) && ($data_list['markup_limits'][0]['value_type']==$page_data['form_data']['value_type_i_' . $__domain_origin]) ){

								if (($page_data ['form_data'] ['specific_value'] [$__k] != '' && intval ( $page_data ['form_data'] ['specific_value'] [$__k] ) > - 1 && empty ( $page_data ['form_data'] ['value_type_' . $__domain_origin] ) == false) || ($page_data ['form_data'] ['specific_value_i'] [$__k] != '' && intval ( $page_data ['form_data'] ['specific_value_i'] [$__k] ) > - 1 && empty ( $page_data ['form_data'] ['value_type_i_' . $__domain_origin] ) == false) ) {
									$this->domain_management_model->save_markup_data ( $page_data ['form_data'] ['markup_origin'] [$__k], $page_data ['form_data'] ['form_values_origin'], $markup_module_type, $page_data ['form_data'] ['airline_origin'] [$__k], $page_data ['form_data'] ['specific_value'] [$__k], $page_data ['form_data'] ['value_type_' . $__domain_origin], get_domain_auth_id (), $page_data['form_data']['specific_value_i'][$__k], $page_data['form_data']['value_type_i_'.$__domain_origin] );
									}
								if(trim($page_data ['form_data'] ['specific_value'] [$__k]) == '' && trim($page_data ['form_data'] ['specific_value_i'] [$__k]) == '' && isset($page_data ['form_data'] ['markup_origin'] [$__k]) && $page_data ['form_data'] ['markup_origin'] [$__k] > 0)
								{
									$this->custom_db->delete_record("markup_list", array("origin"=>$page_data ['form_data'] ['markup_origin'] [$__k]));
								}
							}
							else{
								$this->session->set_flashdata("msg", "<div class='alert alert-danger'>System accepts markup values under Rs. ".$data_list['markup_limits'][0]['value']."/-</div>");
							}
						}
					}
					break;
				case 'add_airline';//Balu A
				if(($data_list['markup_limits'][0]['value'] >= $page_data['form_data']['specific_value']) && ($data_list['markup_limits'][0]['value_type']==$page_data['form_data']['value_type']) || 
					($data_list['markup_limits'][0]['value'] >= $page_data['form_data']['specific_value_i']) && ($data_list['markup_limits'][0]['value_type_i']==$page_data['form_data']['value_type_i'])
					){
					if(isset($page_data['form_data']['airline_code']) == true && empty($page_data['form_data']['airline_code']) == false) {
						$airline_code = trim($page_data['form_data']['airline_code'] = $page_data['form_data']['airline_code']);
						$markup_details = $this->domain_management_model->individual_airline_markup_details($markup_module_type, $airline_code);
						$airline_list_origin= intval($markup_details['airline_list_origin']);
						if(intval($markup_details['markup_list_origin']) > 0) {
							$markup_list_origin = intval($markup_details['markup_list_origin']);
						} else {
							$markup_list_origin = 0;
						}
						$this->domain_management_model->save_markup_data(
								$markup_list_origin, 'specific', $markup_module_type, $airline_list_origin,
								$page_data['form_data']['specific_value'], $page_data['form_data']['value_type'], get_domain_auth_id(), $page_data['form_data']['specific_value_i'], $page_data['form_data']['value_type_i']
								);
						
					}
				}
				else{
						$this->session->set_flashdata("msg", "<div class='alert alert-danger'>System accepts markup values under Rs. ".$data_list['markup_limits'][0]['value']."/-</div>");
					}
			}
			redirect ( base_url () . 'index.php/management/' . __FUNCTION__ );
		}
		// Airline would have All - general and domain wise markup
		
		
		$this->template->view ( 'management/b2b_airline_markup', $data_list );
	}
	
	/**
	 * Balu A
	 * Manage domain markup for provab - Domain wise and module wise
	 */
	function b2b_hotel_markup() {
		// Hotel would have All - general and domain wise markup
		$markup_module_type = 'b2b_hotel';
		$data_list = $this->domain_management_model->hotel_markup ();
		$data_list['markup_limits'] = $this->db->where(array('module_type' =>'hotel'))->get('agent_markup_limit')->result_array();
		$page_data ['form_data'] = $this->input->post ();
		if($data_list['markup_limits'][0]['value'] < $page_data['form_data']['generic_value']){
			$this->session->set_flashdata("msg", "<div class='alert alert-danger'>System accepts markup values under Rs. ".$data_list['markup_limits'][0]['value']."/-</div>");
		}
		if (valid_array ( $page_data ['form_data'] ) == true) {
			switch ($page_data ['form_data'] ['form_values_origin']) {
				case 'generic' :
					if(($data_list['markup_limits'][0]['value'] >= $page_data['form_data']['generic_value']) && ($data_list['markup_limits'][0]['value_type']==$page_data['form_data']['value_type'])){
						$this->domain_management_model->save_markup_data ( $page_data ['form_data'] ['markup_origin'], $page_data ['form_data'] ['form_values_origin'], $markup_module_type, 0, $page_data ['form_data'] ['generic_value'], $page_data ['form_data'] ['value_type'], get_domain_auth_id () );
					}
				break;
			}
			redirect ( base_url () . 'index.php/management/' . __FUNCTION__ );
		}
		// Hotel would have All - general and domain wise markup
		
		$this->template->view ( 'management/b2b_hotel_markup', $data_list );
	}
	/**
	 * sanchitha
	 * Manage domain markup for provab - Domain wise and module wise
	 */
	function b2b_holiday_markup() {
		// Hotel would have All - general and domain wise markup
		
		$data_list = $this->domain_management_model->holiday_markup ();
		//exit;
		$data_list['domestic_markup_limits'] = $this->db->where(array('module_type' =>'domestic_package'))->get('agent_markup_limit')->result_array();
		$data_list['international_markup_limits'] = $this->db->where(array('module_type' =>'international_package'))->get('agent_markup_limit')->result_array();
		//echo $this->db->last_query();exit;
		$page_data ['form_data'] = $this->input->post ();
	
		//if(valid_array($page_data ['form_data'])){
			if($page_data['form_data']['module_type'] == 'international_pacakge' ){
				$data_list['markup_limits'][0]['value']=$data_list['international_markup_limits'][0]['value'];
				$data_list['markup_limits'][0]['value_type'] = $data_list['international_markup_limits'][0]['value_type'];
			
			}else{
				$data_list['markup_limits'][0]['value']=$data_list['domestic_markup_limits'][0]['value'];
				$data_list['markup_limits'][0]['value_type'] = $data_list['international_markup_limits'][0]['value_type'];
			}
			//echo $data_list['markup_limits'][0]['value'].'|'.$page_data['form_data']['generic_value'];
			if($data_list['markup_limits'][0]['value'] < $page_data['form_data']['generic_value']){
				$this->session->set_flashdata("msg", "<div class='alert alert-danger'>System accepts markup values under Rs. ".$data_list['markup_limits'][0]['value']."/-</div>");
			}
			if (valid_array ( $page_data ['form_data'] ) == true) {
				//debug($page_data['form_data']);exit;
				switch ($page_data ['form_data'] ['form_values_origin']) {
					case 'generic' :
						if(($data_list['markup_limits'][0]['value'] >= $page_data['form_data']['generic_value']) && ($data_list['markup_limits'][0]['value_type']==$page_data['form_data']['value_type'])){
							$this->domain_management_model->save_markup_data ( $page_data ['form_data'] ['markup_origin'], $page_data ['form_data'] ['form_values_origin'], $page_data['form_data']['module_type'], 0, $page_data ['form_data'] ['generic_value'], $page_data ['form_data'] ['value_type'], get_domain_auth_id () );
						}
					break;
				}
				redirect ( base_url () . 'index.php/management/' . __FUNCTION__ );
			}
		//}
		// Hotel would have All - general and domain wise markup
		//debug($data_list);
		$this->template->view ( 'management/b2b_holiday_markup', $data_list );
	}
	/**
	 * Elavarasi
	 * Manage domain markup for provab - Domain wise and module wise
	 */
	function b2b_sightseeing_markup() {
		// Sightseeing would have All - general and domain wise markup
		$markup_module_type = 'b2b_sightseeing';
		$page_data ['form_data'] = $this->input->post ();
		if (valid_array ( $page_data ['form_data'] ) == true) {
			switch ($page_data ['form_data'] ['form_values_origin']) {
				case 'generic' :
					$this->domain_management_model->save_markup_data ( $page_data ['form_data'] ['markup_origin'], $page_data ['form_data'] ['form_values_origin'], $markup_module_type, 0, $page_data ['form_data'] ['generic_value'], $page_data ['form_data'] ['value_type'], get_domain_auth_id () );
					break;
			}
			redirect ( base_url () . 'index.php/management/' . __FUNCTION__ );
		}
		// Sightseeing would have All - general and domain wise markup
		$data_list = $this->domain_management_model->sightseeing_markup ();
		$this->template->view ( 'management/b2b_sightseeing_markup', $data_list );
	}

	/**
	 * Elavarasi
	 * Manage domain markup for provab - Domain wise and module wise
	 */
	function b2b_transfer_markup() {
		// Transfers would have All - general and domain wise markup
		$markup_module_type = 'b2b_transferv1';
		$page_data ['form_data'] = $this->input->post ();
		if (valid_array ( $page_data ['form_data'] ) == true) {
			switch ($page_data ['form_data'] ['form_values_origin']) {
				case 'generic' :
					$this->domain_management_model->save_markup_data ( $page_data ['form_data'] ['markup_origin'], $page_data ['form_data'] ['form_values_origin'], $markup_module_type, 0, $page_data ['form_data'] ['generic_value'], $page_data ['form_data'] ['value_type'], get_domain_auth_id () );
					break;
			}
			redirect ( base_url () . 'index.php/management/' . __FUNCTION__ );
		}
		// Transfers would have All - general and domain wise markup
		$data_list = $this->domain_management_model->transfer_markup ();
		$this->template->view ( 'management/b2b_transfer_markup', $data_list );
	}

	/**
	 * Anitha G
	 * Manage domain markup for provab - Domain wise and module wise
	 */
	function b2b_car_markup() {
		// Car would have All - general and domain wise markup
		$markup_module_type = 'b2b_car';
		$page_data ['form_data'] = $this->input->post ();
		if (valid_array ( $page_data ['form_data'] ) == true) {
			switch ($page_data ['form_data'] ['form_values_origin']) {
				case 'generic' :
					$this->domain_management_model->save_markup_data ( $page_data ['form_data'] ['markup_origin'], $page_data ['form_data'] ['form_values_origin'], $markup_module_type, 0, $page_data ['form_data'] ['generic_value'], $page_data ['form_data'] ['value_type'], get_domain_auth_id () );
					break;
			}
			redirect ( base_url () . 'index.php/management/' . __FUNCTION__ );
		}
		// Hotel would have All - general and domain wise markup
		$data_list = $this->domain_management_model->car_markup ();
		$this->template->view ( 'management/b2b_car_markup', $data_list );
	}

	/**
	 * Balu A
	 * Manage domain markup for provab - Domain wise and module wise
	 */
	function b2b_bus_markup() {
		// Bus would have All - general and domain wise markup
		$page_data ['form_data'] = $this->input->post ();
		//debug($page_data ['form_data']); exit;
		$data_list = $this->domain_management_model->bus_markup ();
		$data_list['markup_limits'] = $this->db->where(array('module_type' =>'bus'))->get('agent_markup_limit')->result_array();
		//debug($page_data ['form_data']); exit;
		$markup_module_type = 'b2b_bus';
		if($data_list['markup_limits'][0]['value'] < $page_data['form_data']['generic_value']){
			$this->session->set_flashdata("msg", "<div class='alert alert-danger'>System accepts markup values under Rs. ".$data_list['markup_limits'][0]['value']."/-</div>");
		}
		if (valid_array ( $page_data ['form_data'] ) == true) {
			switch ($page_data ['form_data'] ['form_values_origin']) {
				case 'generic' :
				if($data_list['markup_limits'][0]['value_type']==$page_data['form_data']['value_type']){
					if($data_list['markup_limits'][0]['value'] >= $page_data['form_data']['generic_value']){
						$this->domain_management_model->save_markup_data ( $page_data ['form_data'] ['markup_origin'], $page_data ['form_data'] ['form_values_origin'], $markup_module_type, 0, $page_data ['form_data'] ['generic_value'], $page_data ['form_data'] ['value_type'], get_domain_auth_id () );
					}
				}
				else{
					$this->domain_management_model->save_markup_data ( $page_data ['form_data'] ['markup_origin'], $page_data ['form_data'] ['form_values_origin'], $markup_module_type, 0, $page_data ['form_data'] ['generic_value'], $page_data ['form_data'] ['value_type'], get_domain_auth_id () );
				}
				break;
			}
			redirect ( base_url () . 'index.php/management/' . __FUNCTION__ );
		}
		// Airline would have All - general and domain wise markup
		
		$this->template->view ( 'management/b2b_bus_markup', $data_list );
	}
	
	/**
	 * Balu A
	 * Manage Balance history and other details of domain with provab
	 */
	function b2b_balance_manager($balance_request_type = "Cash")
	{	
		$params = $this->input->get();
		$page_data ['form_data'] = $this->input->post ();
		switch (strtoupper ( $balance_request_type )) {
			case 'CHECK___DD' :
				$page_data ['balance_page_obj'] = new Provab_Page_Loader ( 'balance_request_check' );
				break;
			case 'ETRANSFER' :
				$page_data ['balance_page_obj'] = new Provab_Page_Loader ( 'balance_request_e_transfer' );
				break;
			case 'CASH' :
				$page_data ['balance_page_obj'] = new Provab_Page_Loader ( 'balance_request_cash' );
				break;
			default :
				redirect ( base_url () );
		}
		if (valid_array ( $page_data ['form_data'] ) == true) {
			$page_data ['balance_page_obj']->set_auto_validator ();
			if ($this->form_validation->run ()) {
				$page_data ['form_data'] ['transaction_type'] = ($page_data ['form_data'] ['transaction_type']);
				if ($page_data ['form_data'] ['origin'] == 0) {
						
					//get the conversion rate with respect to admin currency
						
					$agent_deposit_currency_details = $this->convert_agent_deposit_currency($page_data ['form_data']['amount']);
					$page_data ['form_data']['currency'] = $agent_deposit_currency_details['currency'];
					$page_data ['form_data']['currency_conversion_rate'] = $agent_deposit_currency_details['currency_conversion_rate'];
					$page_data ['form_data']['amount'] =$agent_deposit_currency_details['amount'];
					//echo debug($page_data ['form_data']);exit;
					// Insert
					$insert_id = $this->domain_management_model->save_master_transaction_details ( $page_data ['form_data'] );
					$this->session->set_flashdata("msg", "<div class='alert alert-success'>Deposit request sent successfully.</div>");
				} elseif (intval ( $page_data ['form_data'] ['origin'] ) > 0) {
					// FIXME :: Update Not Needed As Of Now
				}
				// Slip Upload
				$this->deposit_slip_upload($insert_id);
				redirect ( base_url () . 'index.php/management/' . __FUNCTION__ . '/' . $balance_request_type );
			}
		}
	
		$params = $this->input->get();
		if (isset($params['status']) == false) {
			//$params['status'] = 'PENDING';
		}
		$data_list_filt = array();
		if (isset($params['system_transaction_id']) == true and empty($params['system_transaction_id']) == false) {
			$data_list_filt[] = array('MTD.system_transaction_id', 'like', $this->db->escape('%'.$params['system_transaction_id'].'%'));
		}
		if (isset($params['status']) == true and empty($params['status']) == false && strtolower($params['status']) != 'all') {
			$data_list_filt[] = array('MTD.status', '=', $this->db->escape($params['status']));
		}
		if (isset($params['created_datetime_from']) == true and empty($params['created_datetime_from']) == false) {
			$data_list_filt[] = array('MTD.created_datetime', '>=', $this->db->escape(db_current_datetime($params['created_datetime_from'])));
		}
		if (isset($params['created_datetime_to']) == true and empty($params['created_datetime_to']) == false) {
			$data_list_filt[] = array('MTD.created_datetime', '<=', $this->db->escape(db_current_datetime($params['created_datetime_to'])));
		}
	
		// debug($data_list_filt);exit;
		$page_data ['table_data'] = $this->domain_management_model->master_transaction_request_list($data_list_filt);
		//formated table data
		$page_data ['table_data'] = $this->booking_data_formatter->format_master_transaction_balance($page_data ['table_data'],$this->current_module);
		$page_data ['balance_request_type'] = strtoupper ( $balance_request_type );
		$page_data ['provab_balance_requests'] = get_enum_list ( 'provab_balance_requests' );
		if (empty ( $page_data ['form_data'] ['currency_converter_origin'] ) == true) {
			$page_data ['form_data'] ['currency_converter_origin'] = COURSE_LIST_DEFAULT_CURRENCY;
			$page_data ['form_data'] ['conversion_value'] = 1;
		}
		$page_data['status_options'] = get_enum_list('provab_balance_status');
		$page_data['search_params'] = $params;
		$page_data ['form_data'] ['transaction_type'] = ($balance_request_type);
		//debug($page_data); exit;
		$this->template->view ( 'management/master_balance_manager', $page_data );
	}
	function verify_and_get_agent_balance($amount)
	{
		$currency = get_application_default_currency();
	 	$balance_status = $this->domain_management_model->verify_current_balance ( $amount, $currency );
	    $balance = agent_current_application_balance();
	    if (empty($balance_status) == true) {
	        $data["sufficient_balance"] = 0;
	        $data["balance"] = $balance["value"];
	        $data["currency"] = $currency;
	    }
	    else{
	    	$data["sufficient_balance"] = 1;
	    	$data["balance"] = $balance["value"];
	    	$data["currency"] = $currency;
	    }
	    echo json_encode($data);
	    exit;
	}
	/**Sagar Wakchaure
	 * get conversion rate w.r.t admin
	 * @return string[]|unknown[]
	 */
	function convert_agent_deposit_currency($deposit_amount)
	{
	
	    $response = array();		
		$currency_obj = new Currency ();
		$currency_conversion_rate = $currency_obj->transaction_currency_conversion_rate();
		$response['currency_conversion_rate'] = $currency_conversion_rate;
	    $response['currency'] = agent_base_currency();	
		$response['amount']  = $deposit_amount*$currency_obj->currency_conversion_value(false, agent_base_currency(), admin_base_currency());		
		return $response;
		
	}
	
	function deposit_slip_upload($origin)
	{
		//FILE UPLOAD
		if (valid_array($_FILES) == true and $_FILES['image']['error'] == 0 and $_FILES['image']['size'] > 0) {
			if( function_exists( "check_mime_image_type" ) ) {
			    if ( !check_mime_image_type( $_FILES['image']['tmp_name'] ) ) {
			    	echo "Please select the image files only (gif|jpg|png|jpeg)"; exit;
			    }
			}
			$config['upload_path'] = DEPOSITE_RECIEPT;
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$config['file_name'] = time();
			$config['max_size'] = '1000000';
			$config['max_width']  = '';
			$config['max_height']  = '';
			$config['remove_spaces']  = false;
			//UPDATE
			$temp_record = $this->custom_db->single_table_records('master_transaction_details', 'image', array('origin' => $origin));
			$icon = $temp_record['data'][0]['image'];
			//DELETE OLD FILES
			if (empty($icon) == false) {
				$temp_profile_image = $this->template->domain_image_full_path($icon);//GETTING FILE PATH
				if (file_exists($temp_profile_image)) {
					unlink($temp_profile_image);
				}
			}
			//UPLOAD IMAGE
			$this->load->library('upload', $config);
			if ( ! $this->upload->do_upload('image')) {
				echo $this->upload->display_errors();
			} else {
				$image_data =  $this->upload->data();
			}
			$this->custom_db->update_record('master_transaction_details', array('image' => $image_data['file_name']), array('origin' => $origin));
		}
	}
	/*
	 * Balu A
	 */
	function set_balance_alert() {
		$post_data = $this->input->post ();
		$page_data = array ();
		$page_data ['balance_alert_page_obj'] = new Provab_Page_Loader ( 'set_balance_alert' );
		if (valid_array ( $post_data ) == true) { // UPDATE OR ADD
			$page_data ['balance_alert_page_obj']->set_auto_validator ();
			if ($this->form_validation->run ()) {
				$origin = intval ( $post_data ['origin'] );
				$agent_balance_alert_details = array ();
				$agent_balance_alert_details ['threshold_amount'] = trim ( $post_data ['threshold_amount'] );
				$agent_balance_alert_details ['mobile_number'] = trim ( $post_data ['mobile_number'] );
				$agent_balance_alert_details ['email_id'] = trim ( $post_data ['email_id'] );
				$agent_balance_alert_details ['enable_sms_notification'] = trim ( @$post_data ['enable_sms_notification'] [0] );
				$agent_balance_alert_details ['enable_email_notification'] = trim ( @$post_data ['enable_email_notification'] [0] );
				$agent_balance_alert_details ['created_by_id'] = $this->entity_user_id;
				$agent_balance_alert_details ['created_datetime'] = date ( 'Y-m-d H:i:s' );
				if ($origin > 0) {
					// UPDATE
					$this->custom_db->update_record ( 'agent_balance_alert_details', $agent_balance_alert_details, array (
							'agent_fk' => $this->entity_user_id 
					) );
				} else {
					// ADD
					$agent_balance_alert_details ['agent_fk'] = $this->entity_user_id;
					$this->custom_db->insert_record ( 'agent_balance_alert_details', $agent_balance_alert_details );
				}
				redirect ( 'management/set_balance_alert' );
			}
		}
		$temp_alert_details = $this->custom_db->single_table_records ( 'agent_balance_alert_details', '*', array (
				'agent_fk' => $this->entity_user_id 
		) );
		if ($temp_alert_details ['status'] == true) {
			$page_data ['balance_alert_details'] = $temp_alert_details ['data'] [0];
			$form_data = $temp_alert_details ['data'] [0];
		} else {
			$page_data ['balance_alert_details'] = '';
			$form_data ['origin'] = 0;
		}
		$page_data ['form_data'] = $form_data;
		$this->template->view ( 'management/set_balance_alert', $page_data );
	}
	/**
	 * Sachin
	 * Account Ledger (transactions) search by date
	 */
	function account_ledger($offset=0)
	{
		$get_data = $this->input->get();
		$condition = array();
		$page_data = array();

		$user_id = $this->entity_user_id;

		//From-Date and To-Date
		$from_date = trim(@$get_data['created_datetime_from']);
		$to_date = trim(@$get_data['created_datetime_to']);
		//Auto swipe date
		$fil_data = array();
		if(empty($from_date) == false && empty($to_date) == false)
		{
			$valid_dates = auto_swipe_dates($from_date, $to_date);
			$from_date = $valid_dates['from_date'];
			$to_date = $valid_dates['to_date'];
			$fil_data['from_date'] = date('Y-m-d',strtotime($from_date));
			$fil_data['to_date'] =   date('Y-m-d',strtotime($to_date));
		}else{
			$fil_data['from_date'] = date('Y-m-d');
			$fil_data['to_date'] = date('Y-m-d');
		}
		if(empty($from_date) == false) {
			$ymd_from_date = date('Y-m-d', strtotime($from_date));
			$condition[] = array('date(TL.created_datetime)', '>=', $this->db->escape($ymd_from_date));
		}
		if(empty($to_date) == false) {
			$ymd_to_date = date('Y-m-d', strtotime($to_date));
			$condition[] = array('date(TL.created_datetime)', '<=', $this->db->escape($ymd_to_date));
		}
		if (empty($get_data['app_reference']) == false) {
			$condition[] = array('TL.app_reference', ' like ', $this->db->escape('%'.$get_data['app_reference'].'%'));
		}
		if (empty($get_data['transaction_type']) == false) {
			$condition[] = array('TL.transaction_type', ' like ', $this->db->escape('%'.$get_data['transaction_type'].'%'));
		}
		//Transaction Data
		$total_records = $this->domain_management_model->agent_account_ledger($fil_data,$condition, true);
		$total_records = $total_records['total_records'];
		$transaction_logs = $this->domain_management_model->agent_account_ledger($fil_data,$condition, false, $offset, RECORDS_RANGE_3);
		$transaction_logs = format_account_ledger($transaction_logs['data']);
		$page_data['table_data'] = $transaction_logs['data'];
		/** TABLE PAGINATION */
		$this->load->library('pagination');
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['base_url'] = base_url().'management/account_ledger/';
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$page_data['total_records'] = $config['total_rows'] = $total_records;
		$config['per_page'] = RECORDS_RANGE_2;
		$this->pagination->initialize($config);
		/** TABLE PAGINATION */
		$page_data['search_params'] = $get_data;

		$this->template->view ( 'management/account_ledger', $page_data );
	}

	/*Sachin
	*Export Account Ledger details to Excel Format
	*/
	
	//test
	public function export_account_ledger($op=''){
		
		$get_data = $this->input->GET();
		$condition = array();

		//From-Date and To-Date
		$from_date = trim(@$get_data['created_datetime_from']);
		$to_date = trim(@$get_data['created_datetime_to']);
		//Auto swipe date
		if(empty($from_date) == false && empty($to_date) == false)
		{
			$valid_dates = auto_swipe_dates($from_date, $to_date);
			$from_date = $valid_dates['from_date'];
			$to_date = $valid_dates['to_date'];
		}
		if(empty($from_date) == false) {
			$ymd_from_date = date('Y-m-d', strtotime($from_date));
			$condition[] = array('date(TL.created_datetime)', '>=', $this->db->escape($ymd_from_date));
		}
		if(empty($to_date) == false) {
			$ymd_to_date = date('Y-m-d', strtotime($to_date));
			$condition[] = array('date(TL.created_datetime)', '<=', $this->db->escape($ymd_to_date));
		}
		if (empty($get_data['app_reference']) == false) {
			$condition[] = array('TL.app_reference', ' like ', $this->db->escape('%'.$get_data['app_reference'].'%'));
		}
		if (empty($get_data['transaction_type']) == false) {
			$condition[] = array('TL.transaction_type', ' like ', $this->db->escape('%'.$get_data['transaction_type'].'%'));
		}

		//Transaction Data
		$transaction_logs = $this->domain_management_model->agent_account_ledger($condition, false);
		$transaction_logs = format_account_ledger($transaction_logs['data']);
		$export_data = $transaction_logs['data'];
//debug($export_data);die;

		if($op == 'excel'){ // excel export

			$headings = array( 'a1' => 'Sl. No.', 
        				   'b1' => 'Date', 
        				   'c1' => 'Reference Number',
        				   'd1' => 'Description',
        				   'e1' => 'Debit', 
        				   'f1' => 'Credit', 
        				   'g1' => 'Running Balance'
        				  );
	        // field names in data set 
	        $fields = array( 'a' => '', // empty for sl. no.
	        				 'b' => 'transaction_date', 
	        				 'c' => 'reference_number', 
	        				 'd' => 'full_description',
	        				 'e' => 'debit_amount',
	        				 'f' => 'credit_amount',
	        				 'g' => 'closing_balance');    

	        $excel_sheet_properties = array(
	        				'title' => 'Account_Ledger_'.date('d-M-Y'), 
	        				'creator' => 'Provab', 
	        				'description' => 'Account Ledger', 
	        				'sheet_title' => 'Account Ledger'
	        	);   
	       		
	        $this->load->library ( 'provab_excel' ); // we need this provab_excel library to export excel.
	        $this->provab_excel->excel_export ( $headings, $fields, $export_data, $excel_sheet_properties);

		}else{ // pdf export 

			//debug($export_data); die();

			$col =array(
					'transaction_date'=>'Date',
					'reference_number'=>'Reference Number',
					'full_description'=>'Description',
					'debit_amount'=>'Debit',
					'credit_amount'=>'Credit',
					'closing_balance'=>'Running Balance'
			);

			$pdf_data = format_pdf_data($export_data, $col);
			$this->load->library ( 'provab_pdf' );			
			$get_view = $this->template->isolated_view ('report/table', $pdf_data);
			$this->provab_pdf->create_pdf ( $get_view, 'D' , 'Account_Ledger');			
			exit();
		}
       
	}

	/**
	 * Pravinkumar
	 * PNR/Transaction Search
	 */
	
	function pnr_search() 
	{
		$get_data = $this->input->get ();
		if ($get_data['filter_report_data'] != '' && $get_data['module'] !='') {
			$filter_report_data = $get_data ['filter_report_data'];
			$module = $get_data['module'];
			//Based on the Module data are loaded to page_data
			switch($module){
				case PROVAB_FLIGHT_BOOKING_SOURCE:
					redirect('report/flight?module='.$module.'&filter_report_data='.$filter_report_data);
					break;
				case PROVAB_HOTEL_BOOKING_SOURCE:
					redirect('report/hotel?module='.$module.'&filter_report_data='.$filter_report_data);
					break;
				case PROVAB_BUS_BOOKING_SOURCE:
					redirect('report/bus?module='.$module.'&filter_report_data='.$filter_report_data);
					break;
				case PROVAB_TRANSFERV1_BOOKING_SOURCE:
					redirect('report/transfers?module='.$module.'&filter_report_data='.$filter_report_data);
					break;
				case PROVAB_SIGHTSEEN_BOOKING_SOURCE:
					redirect('report/activities?module='.$module.'&filter_report_data='.$filter_report_data);
					break;

				default:
					refresh();
			}
			//$page_data depends on Module
			$this->template->view ( 'management/pnr_search', $page_data );
		}else{
		$page_data = array();
		$this->template->view ('management/pnr_search', $page_data);
		}
	}
	/*
	 * Balu A
	 * Flight Commission for Agent
	 */
	function flight_commission() 
	{
		$flight_commission_details = $this->domain_management_model->flight_commission_details ();
		$page_data ['commission_details'] = $flight_commission_details ['data'];
		$this->template->view ( 'management/flight_commission', $page_data );
	}
	/*
	 * Balu A
	 * Bus Commission for Agent
	 */
	function bus_commission() 
	{
		$bus_commission_details = $this->domain_management_model->bus_commission_details ();
		$page_data ['commission_details'] = $bus_commission_details ['data'];
		$this->template->view ( 'management/bus_commission', $page_data );
	}
	/*
	 * Elavarasi
	 * Sightseeing Commission for Agent
	 */
	function sightseeing_commission() 
	{
		$sightseeing_commission_details = $this->domain_management_model->sightseeing_commission_details ();
		$page_data ['commission_details'] = $sightseeing_commission_details ['data'];
		$this->template->view ( 'management/sightseeing_commission', $page_data );
	}

	/*
	*Elavarasi
	*Transfers Commission for Agent
	*/
	function transfer_commission(){
		$transfer_commission_details = $this->domain_management_model->transfer_commission_details ();
		$page_data ['commission_details'] = $transfer_commission_details ['data'];
		$this->template->view ( 'management/transfer_commission', $page_data );
	}

	/**
	 * Balu A
	 * Bank Account Details
	 */
	function bank_account_details()
	{
		$temp_data=$this->domain_management_model->bank_account_details();
		if($temp_data['status']) {
			$page_data['table_data'] = $temp_data['data'];
		} else {
			$page_data['table_data'] = '';
		}
		$this->template->view('management/bank_account_details',$page_data);
	}

	function bus_operator_cancellation(){
		// $this->load->library('xlpro');
		// $temp_booking['book_id'] = 'FB08-152702-237173';
		// $temp_booking['booking_source'] = 'PTBSID0000000002';
		// $this->xlpro->get_flight_booking_details('', $temp_booking);
		$page_data ['form_data'] = $this->input->post ();
		if (valid_array ( $page_data ['form_data'] ) == true) {
			$this->form_validation->set_rules('bus_pnr', 'Bus PNR', 'required|is_unique[bus_operator_cancellation.bus_pnr]');
			if ($this->form_validation->run() == TRUE){
				$page_data ['form_data'] ['agent_id'] = $this->entity_user_id;
				$page_data ['form_data'] ['request_status'] = PENDING;
				$page_data ['form_data'] ['created_date'] = date("Y-m-d");
				$page_data ['form_data'] ['status_updated_by'] = 0;
				$page_data ['form_data'] ['updated_date'] = NULL;
				// Insert
				$insert_id = $this->custom_db->insert_record("bus_operator_cancellation", 
					$page_data ['form_data']);
				$this->session->set_flashdata("msg", "<div class='alert alert-success'>Operater cancellation request sent.</div>");
				redirect ( base_url () . 'index.php/management/' . __FUNCTION__);
			}
		}
		$params = $this->input->get();
		$data_list_filt = array();
		$data_list_filt[] = array('BOC.agent_id', '=', $this->entity_user_id);
		if (isset($params['bus_pnr']) == true and empty($params['bus_pnr']) == false) {
			$data_list_filt[] = array('BOC.bus_pnr', 'like', $this->db->escape('%'.$params['bus_pnr'].'%'));
		}
		if (isset($params['created_date_from']) == true and empty($params['created_datetime_from']) == false) {
			$data_list_filt[] = array('BOC.created_date', '>=', $this->db->escape(db_current_datetime($params['created_date_from'])));
		}
		if (isset($params['created_date_to']) == true and empty($params['created_date_to']) == false) {
			$data_list_filt[] = array('BOC.created_date', '<=', $this->db->escape(db_current_datetime($params['created_date_to'])));
		}
		$page_data['search_params'] = $params;
		$page_data ['table_data'] = $this->domain_management_model->boc_request_list($data_list_filt);
		$this->template->view ('management/bus_operator_cancellation', $page_data);
	}

	function tds_certificates($offset=0)
    {
    	//debug($GLOBALS["CI"]); exit;
        $condition = "where pan_no = '".$GLOBALS["CI"]->entity_pan_number."' ";
        $page_data['table_data'] = $this->domain_management_model->tds_certificates($condition, false, $offset, RECORDS_RANGE_3);
        $total_records = $this->domain_management_model->tds_certificates($condition, true);
        $this->load->library('pagination');
        $config['base_url'] = base_url().'index.php/management/tds_certificates/';
        $page_data['total_rows'] = $config['total_rows'] = $total_records;
        $config['per_page'] = RECORDS_RANGE_3;
        $this->pagination->initialize($config);
        /** TABLE PAGINATION */
        $page_data['total_records'] = $config['total_rows'];
        $this->template->view('management/tds_certificates', $page_data);
    }

	/**
	 * Anitha G
	 * Credit Limit
	 */
	function b2b_credit_limit(){

		$page_data ['form_data'] = $this->input->post ();
		$page_data ['balance_page_obj'] = new Provab_Page_Loader ( 'credit_manager' );
		if(isset($_GET["transaction_type"]))
			$t_type = $_GET["transaction_type"];
		// debug($page_data ['balance_page_obj']);exit;
		if (valid_array ( $page_data ['form_data'] ) == true) {
			$page_data ['balance_page_obj']->set_auto_validator ();
			if ($this->form_validation->run ()) {
				$page_data ['form_data'] ['transaction_type'] = $t_type;
				$page_data ['form_data'] ['bank'] = $t_type;
				$page_data ['form_data'] ['branch'] = $t_type;
				$page_data ['form_data'] ['date_of_transaction'] = date('Y-m-d');
				$page_data ['form_data'] ['deposited_branch'] = $t_type;
				//get the conversion rate with respect to admin currency
				$agent_deposit_currency_details = $this->convert_agent_deposit_currency($page_data ['form_data']['amount']);
				$page_data ['form_data']['currency'] = $agent_deposit_currency_details['currency'];
				$page_data ['form_data']['currency_conversion_rate'] = $agent_deposit_currency_details['currency_conversion_rate'];
				$page_data ['form_data']['amount'] =$agent_deposit_currency_details['amount'];

				$selected_pm = $page_data ['form_data']['selected_pm'];
				$temp_pm = explode("_", $selected_pm);
				if($temp_pm[0] == "PAYTM")
				{
					$pg_name = $temp_pm[0];
					if($temp_pm[1] == "CC")
						$payment_through  = "credit_card";
					if($temp_pm[1] == "DC")
						$payment_through  = "debit_card";
					if($temp_pm[1] == "PPI")
						$payment_through  = "paytm_wallet";
				}
				else if($temp_pm[0] == "TECHP")
				{
					$pg_name = $temp_pm[0];
					$payment_through  = "net_banking";
				}
				else
				{
					$pg_name = "WALLET";
					$payment_through  = "wallet";
				}

				$page_data ['form_data']['pg_name'] = $pg_name;
				$page_data ['form_data']['payment_through'] = $payment_through;
				
				// Insert
				$insert_id = $this->domain_management_model->save_master_transaction_details ( $page_data ['form_data'], $t_type);
				//After Saving Master transaction redirect to PG if transaction type is Instant Recharge
				if($t_type=="Instant_Recharge")
				{	
					$amount = $page_data ['form_data']['amount'];
					$selected_pm = $page_data ['form_data']['selected_pm'];
					$txnid = $selected_pm."-IR-".$insert_id;
					$product_info = "Instant_Recharge";
					$conn_fees = $page_data ['form_data']['convenience_fees'];
					$name = $GLOBALS["CI"]->entity_firstname;
					$email = $GLOBALS["CI"]->entity_email;
					$phone = $GLOBALS["CI"]->entity_phone;
					$curr_con_rate = $page_data ['form_data']['currency_conversion_rate'];

					$this->load->model('transaction');
					$this->transaction->create_payment_record($txnid, $amount, $first_name,
					$email, $phone,  $product_info, $conn_fees, 0, $curr_con_rate, $pg_name, $payment_through);

redirect (base_url ().'index.php/payment_gateway/instant_recharge/'.$txnid.'/'.$amount.'/'.$selected_pm);
exit;
				}

				redirect ( base_url () . 'index.php/management/' . __FUNCTION__ . '?transaction_type=' .$t_type);
			}
		}
		// debug($page_data);exit;
		$params = $this->input->get();
		// debug($params);exit;
		if (isset($params['status']) == false) {
			//$params['status'] = 'PENDING';
		}
		$data_list_filt = array();
		if (isset($params['system_transaction_id']) == true and empty($params['system_transaction_id']) == false) {
			$data_list_filt[] = array('MTD.system_transaction_id', 'like', $this->db->escape('%'.$params['system_transaction_id'].'%'));
		}
		if (isset($params['status']) == true and empty($params['status']) == false && strtolower($params['status']) != 'all') {
			$data_list_filt[] = array('MTD.status', '=', $this->db->escape($params['status']));
		}
		if (isset($params['created_datetime_from']) == true and empty($params['created_datetime_from']) == false) {
			$data_list_filt[] = array('MTD.created_datetime', '>=', $this->db->escape(db_current_datetime($params['created_datetime_from'])));
		}
		if (isset($params['created_datetime_to']) == true and empty($params['created_datetime_to']) == false) {
			$data_list_filt[] = array('MTD.created_datetime', '<=', $this->db->escape(db_current_datetime($params['created_datetime_to'])));
		}
		if (empty ( $page_data ['form_data'] ['currency_converter_origin'] ) == true) {
			$page_data ['form_data'] ['currency_converter_origin'] = COURSE_LIST_DEFAULT_CURRENCY;
			$page_data ['form_data'] ['conversion_value'] = 1;
		}
		$page_data ['table_data'] = $this->domain_management_model->master_transaction_request_list($data_list_filt, $t_type);
		//formated table data
		$page_data ['table_data'] = $this->booking_data_formatter->format_master_transaction_balance($page_data ['table_data'],$this->current_module);
		$page_data['search_params'] = $params;
		$page_data['status_options'] = get_enum_list('provab_balance_status');
	
		$this->template->view ( 'management/b2b_credit_limit', $page_data );
	}


	function set_advertisement(){

		$post_data = $this->input->post();
		if($post_data){
			//debug($post_data);die();
			$data ['title'] = $post_data ['title'];
			$data ['created_date'] = date('Y-m-d h:i:s');
	
			if(isset($post_data ['link'])){
				$data ['link'] = $post_data ['link'];
			}
			/*if(isset($post_data ['image'])){
				$data ['image'] = $post_data ['image'];
			}*/
			
			//$data ['module_type'] = $post_data ['module_type'];
			$data ['module_type'] = '';
			$date = str_replace('/', '-', $post_data ['expiry_date']);
			$data ['expiry_date'] = date('Y-m-d',strtotime( $date ));
			$data ['status'] = 0;

			if (valid_array ( $_FILES ) == true and $_FILES ['offer_image'] ['error'] == 0 and $_FILES ['offer_image'] ['size'] > 0) {
                
                $config ['upload_path'] = $this->template->domain_image_upload_path () . 'offer_images';

			    $temp_file_name = $_FILES ['offer_image'] ['name'];
				$config ['allowed_types'] = '*';
				$config ['file_name'] = 'offer_image_'.rand() . $data['image'];
				$config ['max_size'] = '1000000';
				$config ['max_width'] = '';
				$config ['max_height'] = '';
				$config ['remove_spaces'] = false;

				$this->load->library ( 'upload', $config );
				$this->upload->initialize ( $config );
				if (! $this->upload->do_upload ( 'offer_image' )) {
					echo $this->upload->display_errors ();
				} else {
					$image_data = $this->upload->data ();
				}
			}

			//$data ['image'] = '';
			$data ['image'] = $image_data ['file_name'];
			$data ['created_by'] = $this->entity_user_id;

			$this->custom_db->insert_record('advertisement' , $data); 
			redirect(base_url().'index.php/management/set_advertisement');
		}
        else{
			$data = array();
			$condition = array("created_by" =>$this->entity_user_id);
			$data['data'] = $this->domain_management_model->get_add($condition);
			$this->template->view('management/banner_add',$data);
	    }
	}

	function add_status(){
			$post_values = $this->input->post();
		
			if($post_values['status'] == 'active'){
				$status = 1;
			}else{
				$status = 0;
			}
			
			echo $this->domain_management_model->modify_adv_status($post_values['id'],$status);
    }

	//================================ Pacetravels =======================================
	function booking_calender($offset=0)
	{
	    $this->load->library('pagination');
	    $config['base_url'] = base_url().'index.php/management/booking_calender/';
	    $this->pagination->initialize($config);
	    $page_data['total_records'] = "";
	    $this->template->view('management/booking_calender', $page_data);
	}

	function cancel_ticket($offset=0)
	{
		$data = $_POST;
		if(isset($data) && !empty($data)){
			$app_reference = $data['app-ref'];
			$module = $data['module'];
			$flag = 0;
			if($module == 'bus'){
				$booking_data = $this->bus_model->bus_booking_details_by_app_ref($app_reference);
				if(isset($booking_data) && !empty($booking_data)){
					$flag = 1;
					$booking_source = $booking_data[0]['booking_source'];
					$status = $booking_data[0]['status'];
					redirect(base_url().'bus/pre_cancellation/'.$app_reference.'/'.$booking_source.'/'.$status.'');
				}
				
			}else if($module == 'Flight'){
				$booking_data = $this->flight_model->flight_booking_details_by_app_ref($app_reference);
				if(isset($booking_data) && !empty($booking_data)){	
					$flag = 1;
					$booking_source = $booking_data[0]['booking_source'];
					$status = $booking_data[0]['status'];
					redirect(base_url().'flight/pre_cancellation/'.$app_reference.'/'.$booking_source.'/'.$status.'');
				}
			}else if($module == 'Hotel'){
				$flag = 1;
			}else if($module == 'Package'){
				$flag = 1;
			}
			if($flag == 0){
				$this->load->library('pagination');
		    	$config['base_url'] = base_url().'index.php/management/cancel_ticket/';
		    	$this->pagination->initialize($config);
		    	$page_data['total_records'] = "";
		    	$page_data['err'] = "Please Enter Correct Details.....";
		    	$this->template->view('management/cancel_ticket', $page_data);
			}
		}else{
			$this->load->library('pagination');
	    	$config['base_url'] = base_url().'index.php/management/cancel_ticket/';
	    	$this->pagination->initialize($config);
	    	$page_data['total_records'] = "";
	    	$this->template->view('management/cancel_ticket', $page_data);
		}
	    
	}
	//====================================================================================	
}
