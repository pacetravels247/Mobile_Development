<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
error_reporting(0);
/**
 *
 * @package    Provab - Provab Application
 * @subpackage Travel Portal
 * @author     Balu A<balu.provab@gmail.com> on 01-06-2015
 * @version    V2
 */
class Management extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('domain_management_model');
		$this->load->helper('custom/transaction_log');
		$this->load->helper('url');
		$this->load->model('flight_model');
		$this->load->library('session');
		//$this->load->helper('download');
		//$this->load->library('excel');
		//$this->output->enable_profiler(TRUE);
	}

	/**
	 * Balu A
	 * Manage domain markup for provab - Domain wise and module wise
	 */
	function b2c_airline_markup()
	{
		$markup_module_type = 'b2c_flight';
		$page_data['form_data'] = $this->input->post();
		if (valid_array($page_data['form_data']) == true) {
			switch($page_data['form_data']['form_values_origin']) {
				case 'generic':
					$this->domain_management_model->save_markup_data(
					$page_data['form_data']['markup_origin'], $page_data['form_data']['form_values_origin'], $markup_module_type, 0,
					$page_data['form_data']['generic_value'], $page_data['form_data']['value_type'], get_domain_auth_id()
					);
					break;
				case 'specific':
					if (valid_array($page_data['form_data']['airline_origin'])) {
						foreach($page_data['form_data']['airline_origin'] as $__k => $__domain_origin) {
							if ($page_data['form_data']['specific_value'][$__k] != '' && intval($page_data['form_data']['specific_value'][$__k]) > -1
							&& empty($page_data['form_data']['value_type_'.$__domain_origin]) == false
							) {
								$this->domain_management_model->save_markup_data(
								$page_data['form_data']['markup_origin'][$__k], $page_data['form_data']['form_values_origin'], $markup_module_type, $page_data['form_data']['airline_origin'][$__k],
								$page_data['form_data']['specific_value'][$__k], $page_data['form_data']['value_type_'.$__domain_origin], get_domain_auth_id()
								);
							}
						}
					}
					break;
				case 'add_airline';//Balu A
					if(isset($page_data['form_data']['airline_code']) == true && empty($page_data['form_data']['airline_code']) == false) {
						$airline_code = trim($page_data['form_data']['airline_code'] = $page_data['form_data']['airline_code']);
						$markup_details = $this->domain_management_model->individual_airline_markup_details($markup_module_type, $airline_code);
						$airline_list_origin= intval($markup_details['airline_list_origin']);
						if(intval($markup_details['markup_list_origin']) > 0) {
							$markup_list_origin = intval($markup_details['markup_list_origin']);
						} else {
							$markup_list_origin = 0;
						}
						//debug($markup_details);exit;
						$this->domain_management_model->save_markup_data(
								$markup_list_origin, 'specific', $markup_module_type, $airline_list_origin,
								$page_data['form_data']['specific_value'], $page_data['form_data']['value_type'], get_domain_auth_id()
								);
						
					}
					break;
			}
			set_update_message();
			redirect(base_url().'index.php/management/'.__FUNCTION__);
		}
		$view_data = array();
		//Airline would have All - general and domain wise markup
		$data_list = $this->domain_management_model->b2c_airline_markup();
		$airline_list = $this->db_cache_api->get_airline_list();
		$data_list['data']['airline_list'] = $airline_list;
		$this->template->view('management/b2c_airline_markup', $data_list['data']);
	}

	/**
	 * Balu A
	 * Manage domain markup for provab - Domain wise and module wise
	 */
	function b2c_hotel_markup()
	{
		
		//Hotel would have All - general and domain wise markup
		$markup_module_type = 'b2c_hotel';
		$page_data['form_data'] = $this->input->post();
		if (valid_array($page_data['form_data']) == true) {
			switch($page_data['form_data']['form_values_origin']) {
				case 'generic':
					$this->domain_management_model->save_markup_data(
					$page_data['form_data']['markup_origin'], $page_data['form_data']['form_values_origin'], $markup_module_type, 0,
					$page_data['form_data']['generic_value'], $page_data['form_data']['value_type'], get_domain_auth_id()
					);
					break;
			}
			set_update_message();
			redirect(base_url().'index.php/management/'.__FUNCTION__);
		}
		//Airline would have All - general and domain wise markup
		$data_list = $this->domain_management_model->b2c_hotel_markup();
		$this->template->view('management/b2c_hotel_markup', $data_list['data']);
	}
	/**
	 * Anitha G
	 * Manage domain markup for provab - Domain wise and module wise
	 */
	function b2c_car_markup()
	{
		
		//Car would have All - general and domain wise markup
		$markup_module_type = 'b2c_car';
		$page_data['form_data'] = $this->input->post();
		if (valid_array($page_data['form_data']) == true) {
			// debug($page_data);exit;
			switch($page_data['form_data']['form_values_origin']) {
				case 'generic':
					$this->domain_management_model->save_markup_data(
					$page_data['form_data']['markup_origin'], $page_data['form_data']['form_values_origin'], $markup_module_type, 0,
					$page_data['form_data']['generic_value'], $page_data['form_data']['value_type'], get_domain_auth_id()
					);
					break;
			}
			set_update_message();
			redirect(base_url().'index.php/management/'.__FUNCTION__);
		}
		//Airline would have All - general and domain wise markup
		$data_list = $this->domain_management_model->b2c_car_markup();
		$this->template->view('management/b2c_car_markup', $data_list['data']);
	}

	/**
	 * Elavarasi
	 * Manage domain markup for provab - Domain wise and module wise
	 */
	function b2c_sightseeing_markup()
	{
		
		//Hotel would have All - general and domain wise markup
		$markup_module_type = 'b2c_sightseeing';
		$page_data['form_data'] = $this->input->post();
		if (valid_array($page_data['form_data']) == true) {
			switch($page_data['form_data']['form_values_origin']) {
				case 'generic':
					$this->domain_management_model->save_markup_data(
					$page_data['form_data']['markup_origin'], $page_data['form_data']['form_values_origin'], $markup_module_type, 0,
					$page_data['form_data']['generic_value'], $page_data['form_data']['value_type'], get_domain_auth_id()
					);
					break;
			}
			set_update_message();
			redirect(base_url().'index.php/management/'.__FUNCTION__);
		}
		//Airline would have All - general and domain wise markup
		$data_list = $this->domain_management_model->b2c_sightseeing_markup();
		$this->template->view('management/b2c_sightseeing_markup', $data_list['data']);
	}
		/**
	 * Elavarasi
	 * Manage domain markup for provab - Domain wise and module wise
	 */
	function b2c_transfer_markup()
	{
		
		//Hotel would have All - general and domain wise markup
		$markup_module_type = 'b2c_transferv1';
		$page_data['form_data'] = $this->input->post();
		if (valid_array($page_data['form_data']) == true) {
			switch($page_data['form_data']['form_values_origin']) {
				case 'generic':
					$this->domain_management_model->save_markup_data(
					$page_data['form_data']['markup_origin'], $page_data['form_data']['form_values_origin'], $markup_module_type, 0,
					$page_data['form_data']['generic_value'], $page_data['form_data']['value_type'], get_domain_auth_id()
					);
					break;
			}
			set_update_message();
			redirect(base_url().'index.php/management/'.__FUNCTION__);
		}
		//Airline would have All - general and domain wise markup
		$data_list = $this->domain_management_model->b2c_transferv1_markup();
		$this->template->view('management/b2c_transferv1_markup', $data_list['data']);
	}
	/**
	 * Balu A
	 * Manage domain markup for provab - Domain wise and module wise
	 */
	function b2c_bus_markup()
	{
		//Bus would have All - general and domain wise markup
		$page_data['form_data'] = $this->input->post();
		$markup_module_type = 'b2c_bus';
		if (valid_array($page_data['form_data']) == true) {
			switch($page_data['form_data']['form_values_origin']) {
				case 'generic':
					$this->domain_management_model->save_markup_data(
					$page_data['form_data']['markup_origin'], $page_data['form_data']['form_values_origin'], $markup_module_type, 0,
					$page_data['form_data']['generic_value'], $page_data['form_data']['value_type'], get_domain_auth_id()
					);
					break;
			}
			set_update_message();
			redirect(base_url().'index.php/management/'.__FUNCTION__);
		}
		//Airline would have All - general and domain wise markup
		$data_list = $this->domain_management_model->b2c_bus_markup();
		$this->template->view('management/b2c_bus_markup', $data_list['data']);
	}

	/**
	 * Balu A
	 * Manage domain markup for B2B - Domain wise and module wise
	 */
	function b2b_airline_markup()
	{
		$user_oid = 0;//defining general only as of now
		$this->domain_management_model->markup_level = 'level_3';
		//FIXME : Airline Markup - agent wise and general markup
		$markup_module_type = 'b2b_flight';
		$page_data['form_data'] = $this->input->post();
		// debug($page_data);exit;
		if (valid_array($page_data['form_data']) == true) {
			switch($page_data['form_data']['form_values_origin']) {
				case 'generic':
					$this->domain_management_model->save_markup_data(
					$page_data['form_data']['markup_origin'], $page_data['form_data']['form_values_origin'], $markup_module_type, 0,
					$page_data['form_data']['generic_value'], $page_data['form_data']['value_type'], get_domain_auth_id(), $user_oid, $page_data['form_data']['generic_value_i'], $page_data['form_data']['value_type_i']
					);
					break;
				case 'specific':
					if (valid_array($page_data['form_data']['airline_origin'])) {
						foreach($page_data['form_data']['airline_origin'] as $__k => $__domain_origin) {
							if (($page_data['form_data']['specific_value'][$__k] != '' && intval($page_data['form_data']['specific_value'][$__k]) > -1
							&& empty($page_data['form_data']['value_type_'.$__domain_origin]) == false) || ($page_data['form_data']['specific_value_i'][$__k] != '' && intval($page_data['form_data']['specific_value_i'][$__k]) > -1
							&& empty($page_data['form_data']['value_type_i_'.$__domain_origin]) == false) 
							) {
								$this->domain_management_model->save_markup_data(
								$page_data['form_data']['markup_origin'][$__k], $page_data['form_data']['form_values_origin'], $markup_module_type, $page_data['form_data']['airline_origin'][$__k],
								$page_data['form_data']['specific_value'][$__k], $page_data['form_data']['value_type_'.$__domain_origin], get_domain_auth_id(), $user_oid, $page_data['form_data']['specific_value_i'][$__k], $page_data['form_data']['value_type_i_'.$__domain_origin]
								);
							}
							if((trim($page_data ['form_data'] ['specific_value'] [$__k]) == '' && trim($page_data ['form_data'] ['specific_value_i'] [$__k]) == '')&& isset($page_data ['form_data'] ['markup_origin'] [$__k]) && $page_data ['form_data'] ['markup_origin'] [$__k] > 0)
							{
								$this->custom_db->delete_record("markup_list", array("origin"=>$page_data ['form_data'] ['markup_origin'] [$__k]));
							}
						}
					}
					break;
					case 'add_airline';//Balu A
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
								$page_data['form_data']['specific_value'], $page_data['form_data']['value_type'], get_domain_auth_id(), 0, $page_data['form_data']['specific_value_i'], $page_data['form_data']['value_type_i']
								);
						
					}
					break;
			}
			set_update_message();
			redirect(base_url().'index.php/management/'.__FUNCTION__);
		}
		//Airline would have All - general and Agent wise markup
		$data_list = $this->domain_management_model->b2b_airline_markup();
		$airline_list = $this->db_cache_api->get_airline_list();
		$data_list['airline_list'] = $airline_list;
		$this->template->view('management/b2b_airline_markup', $data_list);
	}

	/**
	 * Balu A
	 * Manage domain markup for B2B - Domain wise and module wise
	 */
	function b2b_hotel_markup()
	{
		$user_oid = 0;//defining general only as of now
		$this->domain_management_model->markup_level = 'level_3';
		//Hotel would have All - general and domain wise markup
		$markup_module_type = 'b2b_hotel';
		$page_data['form_data'] = $this->input->post();
		if (valid_array($page_data['form_data']) == true) {
			switch($page_data['form_data']['form_values_origin']) {
				case 'generic':
					$this->domain_management_model->save_markup_data(
					$page_data['form_data']['markup_origin'], $page_data['form_data']['form_values_origin'], $markup_module_type, 0,
					$page_data['form_data']['generic_value'], $page_data['form_data']['value_type'], get_domain_auth_id(), $user_oid
					);
					break;
			}
			set_update_message();
			redirect(base_url().'index.php/management/'.__FUNCTION__);
		}
		//Hotel would have All - general and domain wise markup
		$data_list = $this->domain_management_model->b2b_hotel_markup();
		$this->template->view('management/b2b_hotel_markup', $data_list);
	}
	/**
	 * Elavarasi
	 * Manage domain markup for B2B - Domain wise and module wise
	 */
	function b2b_sightseeing_markup(){
		$user_oid = 0;//defining general only as of now
		$this->domain_management_model->markup_level = 'level_3';
		//Sightseeing would have All - general and domain wise markup
		$markup_module_type = 'b2b_sightseeing';
		$page_data['form_data'] = $this->input->post();
		if (valid_array($page_data['form_data']) == true) {
			switch($page_data['form_data']['form_values_origin']) {
				case 'generic':
					$this->domain_management_model->save_markup_data(
					$page_data['form_data']['markup_origin'], $page_data['form_data']['form_values_origin'], $markup_module_type, 0,
					$page_data['form_data']['generic_value'], $page_data['form_data']['value_type'], get_domain_auth_id(), $user_oid
					);
					break;
			}
			set_update_message();
			redirect(base_url().'index.php/management/'.__FUNCTION__);
		}
		//Sightseeing would have All - general and domain wise markup
		$data_list = $this->domain_management_model->b2b_sightseeing_markup();
		$this->template->view('management/b2b_sightseeing_markup', $data_list);
	}
	/**
	 * Elavarasi
	 * Manage domain markup for B2B - Domain wise and module wise
	 */
	function b2b_transfer_markup(){
		$user_oid = 0;//defining general only as of now
		$this->domain_management_model->markup_level = 'level_3';
		//Sightseeing would have All - general and domain wise markup
		$markup_module_type = 'b2b_transferv1';
		$page_data['form_data'] = $this->input->post();
		if (valid_array($page_data['form_data']) == true) {
			switch($page_data['form_data']['form_values_origin']) {
				case 'generic':
					$this->domain_management_model->save_markup_data(
					$page_data['form_data']['markup_origin'], $page_data['form_data']['form_values_origin'], $markup_module_type, 0,
					$page_data['form_data']['generic_value'], $page_data['form_data']['value_type'], get_domain_auth_id(), $user_oid
					);
					break;
			}
			set_update_message();
			redirect(base_url().'index.php/management/'.__FUNCTION__);
		}
		//Sightseeing would have All - general and domain wise markup
		$data_list = $this->domain_management_model->b2b_transferv1_markup();
		$this->template->view('management/b2b_transfers_markup', $data_list);
	}

	/**
	 * Anitha G
	 * Manage domain markup for B2B - Domain wise and module wise
	 */
	function b2b_car_markup(){
		$user_oid = 0;//defining general only as of now
		$this->domain_management_model->markup_level = 'level_3';
		//Sightseeing would have All - general and domain wise markup
		$markup_module_type = 'b2b_car';
		$page_data['form_data'] = $this->input->post();
		if (valid_array($page_data['form_data']) == true) {
			// debug($page_data);exit;
			switch($page_data['form_data']['form_values_origin']) {
				case 'generic':
					$this->domain_management_model->save_markup_data(
					$page_data['form_data']['markup_origin'], $page_data['form_data']['form_values_origin'], $markup_module_type, 0,
					$page_data['form_data']['generic_value'], $page_data['form_data']['value_type'], get_domain_auth_id(), $user_oid
					);
					break;
			}
			set_update_message();
			redirect(base_url().'index.php/management/'.__FUNCTION__);
		}
		//Sightseeing would have All - general and domain wise markup
		$data_list = $this->domain_management_model->b2b_car_markup();
		$this->template->view('management/b2b_car_markup', $data_list);
	}
	
	/**
	 * Balu A
	 * Manage domain markup for B2B - Domain wise and module wise
	 */
	function b2b_bus_markup()
	{
		$user_oid = 0;//defining general only as of now
		$this->domain_management_model->markup_level = 'level_3';
		//Bus would have All - general and domain wise markup
		$page_data['form_data'] = $this->input->post();
		$markup_module_type = 'b2b_bus';
		if (valid_array($page_data['form_data']) == true) {
			switch($page_data['form_data']['form_values_origin']) {
				case 'generic':
					$this->domain_management_model->save_markup_data(
					$page_data['form_data']['markup_origin'], $page_data['form_data']['form_values_origin'], $markup_module_type, 0,
					$page_data['form_data']['generic_value'], $page_data['form_data']['value_type'], get_domain_auth_id(), $user_oid
					);
					break;
			}
			set_update_message();
			redirect(base_url().'index.php/management/'.__FUNCTION__);
		}
		//Airline would have All - general and domain wise markup
		$data_list = $this->domain_management_model->b2b_bus_markup();
		$this->template->view('management/b2b_bus_markup', $data_list);
	}

	/**
	 * Balu A
	 * Manage Balance history and other details of domain with provab
	 */
	function master_balance_manager($balance_request_type="Cash")
	{
		echo 'Under Construction';
		exit;
		$page_data['form_data'] = $this->input->post();

		switch(strtoupper($balance_request_type)) {
			case 'CHECK___DD'	:
				$page_data['balance_page_obj'] = new Provab_Page_Loader('balance_request_check');
				break;
			case 'ETRANSFER'	:
				$page_data['balance_page_obj'] = new Provab_Page_Loader('balance_request_e_transfer');
				break;
			case 'CASH'			:
				$page_data['balance_page_obj'] = new Provab_Page_Loader('balance_request_cash');
				break;
			default : redirect(base_url());
		}
		if (valid_array($page_data['form_data']) == true) {
			$page_data['balance_page_obj']->set_auto_validator();
			if ($this->form_validation->run()) {
				$page_data['form_data']['transaction_type'] = unserialized_data($page_data['form_data']['transaction_type']);
				if ($page_data['form_data']['origin'] == 0) {
					//Insert
					$status = $this->domain_management_model->save_master_transaction_details($page_data['form_data']);
				} elseif (intval($page_data['form_data']['origin']) > 0) {
					//FIXME :: Update Not Needed As Of Now
				}
				if ($status['status'] == SUCCESS_STATUS) {
					set_update_message();
				} else {
					set_error_message();
				}
				redirect(base_url().'index.php/management/'.__FUNCTION__.'/'.$balance_request_type);
			}

		}
		$page_data['table_data'] = $this->domain_management_model->master_transaction_request_list();
		$page_data['balance_request_type'] = strtoupper($balance_request_type);
		$page_data['provab_balance_requests'] = get_enum_list('provab_balance_requests');
		if (empty($page_data['form_data']['currency_converter_origin']) == true) {
			$page_data['form_data']['currency_converter_origin']	= COURSE_LIST_DEFAULT_CURRENCY;
			$page_data['form_data']['conversion_value']				= 1;
		}
		$page_data['form_data']['transaction_type'] = serialized_data($balance_request_type);
		$this->template->view('management/master_balance_manager', $page_data);
	}

	// Managing Balance of B2B users.
	public function b2b_balance_manager($balance_request_type="Cash"){
		$page_data['form_data'] = $this->input->post();
		
		if (valid_array($page_data['form_data']) == true) {
			if (intval($page_data['form_data']['request_origin']) > 0) {
				//echo debug($page_data['form_data']);exit;

				$process_details = $this->domain_management_model->process_balance_request($page_data['form_data']['request_origin'], $page_data['form_data']['system_request_id'], $page_data['form_data']['status_id'], $page_data['form_data']['update_remarks']);
				// debug($page_data);exit;
			} else {
				
				$page_data['balance_page_obj']->set_auto_validator();
				if ($this->form_validation->run()) {
					$page_data['form_data']['transaction_type'] = unserialized_data($page_data['form_data']['transaction_type']);
					if ($page_data['form_data']['request_origin'] == 0) {
						//Insert
						//$this->domain_management_model->save_master_transaction_details($page_data['form_data']);
					}
				}
			}
			// echo 'herrere';exit;

			if ($process_details['status'] == SUCCESS_STATUS) {
				$data_list_filt = array();
				$data_list_filt[] = array('MTD.origin', '=',trim($page_data['form_data']['request_origin']));
				$data = $this->domain_management_model->master_transaction_request_list('b2b', $data_list_filt);
				if(!empty($data) && isset($data[0])){
					$master_transaction['master_transaction'] = $data[0];
					$email = $page_data['form_data']['request_user_email'];
					//$email=  "sagar@mailinator.com";
					$mail_template = $this->template->isolated_view('user/deposit_confirmation_template',$master_transaction);
					$this->load->library('provab_mailer');
					$status = $this->provab_mailer->send_mail($email,'Account Deposit', $mail_template);
				}
				//set_update_message();
			} else {
				//set_error_message();
			}
			redirect(base_url().'index.php/management/'.__FUNCTION__.'?'.$_SERVER['QUERY_STRING']);
		}
		$params = $this->input->get();
		if (isset($params['status']) == false) {
			//$params['status'] = 'PENDING';
		}
		$data_list_filt = array();
		if (isset($params['uuid']) == true and empty($params['uuid']) == false) {
			$data_list_filt[] = array('U.uuid', 'like', $this->db->escape('%'.$params['uuid'].'%'));
		}
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
			$date = db_current_datetime($params['created_datetime_to']);
			$date1 = explode(' ',$date);
			$date2 = $date1[0].' 23:59:59';
			$data_list_filt[] = array('MTD.created_datetime', '<=', $this->db->escape($date2));
		}
		$page_data['table_data'] = $this->domain_management_model->master_transaction_request_list('b2b', $data_list_filt);
		//echo debug($page_data['table_data']);exit;
		$page_data['provab_balance_requests'] = get_enum_list('provab_balance_requests');
		$page_data['provab_balance_status'] = get_enum_list('provab_balance_status');
		if (empty($page_data['form_data']['currency_converter_origin']) == true) {
			$page_data['form_data']['currency_converter_origin']	= COURSE_LIST_DEFAULT_CURRENCY;
			$page_data['form_data']['conversion_value']				= 1;
		}
		$page_data['status_options'] = get_enum_list('provab_balance_status');
		$page_data['agency_list'] = $this->domain_management_model->get_agent_list();
		$page_data['heading'] = 'B2B Balance Request';
		$page_data['search_params'] = $params;
		$page_data['notification_count'] = $this->flight_model->notification_count();
		$this->session->set_userdata('notification_count',$page_data['notification_count'][0]['count']);
		$this->template->view('management/b2b_balance_manager', $page_data);

	}
	//**********************************Notification*******************************************
	public function update_notification()
	{
		$this->domain_management_model->get_update_deposite_notification();
	}
	public function b2b_credit_request($balance_request_type="Cash"){
		$page_data['form_data'] = $this->input->post();
		
		if (valid_array($page_data['form_data']) == true) {
			if (intval($page_data['form_data']['request_origin']) > 0) {
				//echo debug($page_data['form_data']);exit;
				$process_details = $this->domain_management_model->process_credit_limit_request($page_data['form_data']['request_origin'], $page_data['form_data']['system_request_id'], $page_data['form_data']['status_id'], $page_data['form_data']['update_remarks']);
			} else {
				$page_data['balance_page_obj']->set_auto_validator();
				if ($this->form_validation->run()) {
					$page_data['form_data']['transaction_type'] = unserialized_data($page_data['form_data']['transaction_type']);
					if ($page_data['form_data']['request_origin'] == 0) {
						//Insert
						//$this->domain_management_model->save_master_transaction_details($page_data['form_data']);
					}
				}
			}
			if ($process_details['status'] == SUCCESS_STATUS) {
				$data_list_filt = array();
				$data_list_filt[] = array('MTD.origin', '=',trim($page_data['form_data']['request_origin']));
				$data = $this->domain_management_model->master_transaction_request_list('b2b', $data_list_filt);
				if(!empty($data) && isset($data[0])){
					$master_transaction['master_transaction'] = $data[0];
					$email = $page_data['form_data']['request_user_email'];
					//$email=  "sagar@mailinator.com";
					$mail_template = $this->template->isolated_view('user/deposit_confirmation_template',$master_transaction);
					$this->load->library('provab_mailer');
					$status = $this->provab_mailer->send_mail($email,'Account Deposit', $mail_template);
				}
				set_update_message();
			} else {
				set_error_message();
			}
			redirect(base_url().'index.php/management/'.__FUNCTION__.'?'.$_SERVER['QUERY_STRING']);
		}
		$params = $this->input->get();
		if (isset($params['status']) == false) {
			//$params['status'] = 'PENDING';
		}
		$data_list_filt = array();
		if (isset($params['agency_name']) == true and empty($params['agency_name']) == false) {
			$data_list_filt[] = array('U.agency_name', 'like', $this->db->escape('%'.$params['agency_name'].'%'));
		}
		if (isset($params['uuid']) == true and empty($params['uuid']) == false) {
			$data_list_filt[] = array('U.uuid', 'like', $this->db->escape('%'.$params['uuid'].'%'));
		}
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
		$page_data['table_data'] = $this->domain_management_model->master_transaction_request_list('b2b', $data_list_filt,'Credit');
		//echo debug($page_data['table_data']);exit;
		$page_data['provab_balance_requests'] = get_enum_list('provab_balance_requests');
		$page_data['provab_balance_status'] = get_enum_list('provab_balance_status');
		if (empty($page_data['form_data']['currency_converter_origin']) == true) {
			$page_data['form_data']['currency_converter_origin']	= COURSE_LIST_DEFAULT_CURRENCY;
			$page_data['form_data']['conversion_value']				= 1;
		}
		$page_data['heading'] = 'B2B Credit Limit Request';
		$page_data['status_options'] = get_enum_list('provab_balance_status');
		$page_data['search_params'] = $params;
		$this->template->view('management/b2b_balance_manager', $page_data);

	}

	/**
	 * Event logging
	 * @param number $offset
	 */
	function event_logs($offset=0)
	{
		$condition = array();
		$page_data['table_data'] = $this->domain_management_model->event_logs($condition, false, $offset, RECORDS_RANGE_3);
		$total_records = $this->domain_management_model->event_logs($condition, true);
		$this->load->library('pagination');
		$config['base_url'] = base_url().'index.php/management/event_logs/';
		$page_data['total_rows'] = $config['total_rows'] = $total_records;
		$config['per_page'] = RECORDS_RANGE_3;
		$this->pagination->initialize($config);
		/** TABLE PAGINATION */
		$page_data['total_records'] = $config['total_rows'];
		$this->template->view('management/event_logs', $page_data);
	}
	function flush_xml_flight_logs()
    {
		$this->load->database("services", "service_db");
        $today = new DateTime(date("Y-m-d H:i:s"));
        $today->modify("-30 day");
        $previous_date = $today->format("Y-m-d H:i:s");

        $this->custom_db->delete_record("provab_api_response_history", array("created_datetime <" => $previous_date));
        set_update_message();
        redirect(base_url().'index.php/management/xml_flight_logs');
    }
	function xml_flight_logs($offset=0)
    {
        $condition = array();
		$this->load->database("services", "service_db");
        $page_data['table_data'] = $this->domain_management_model->xml_flight_logs($condition, false, $offset, RECORDS_RANGE_3);
        $total_records = $this->domain_management_model->xml_flight_logs($condition, true);
        $this->load->library('pagination');
        $config['base_url'] = base_url().'index.php/management/xml_flight_logs/';
        $page_data['total_rows'] = $config['total_rows'] = $total_records;
        $config['per_page'] = RECORDS_RANGE_3;
        $this->pagination->initialize($config);
        /** TABLE PAGINATION */
        $page_data['total_records'] = $config['total_rows'];
		//debug($page_data); exit;
		$page_data["db"] = "service";
        $this->template->view('management/xml_logs', $page_data);
    }
	function download_xml_flight_log($log_id)
    {
		$this->load->database("services", "service_db");
    	$xml_log = $this->custom_db->single_table_records("provab_api_response_history", "*", array("origin" => $log_id));
		
		$down_str = "==============REQUEST START===============<br>";
		$down_str .= $xml_log["data"][0]["request"]."<br>";
		$down_str .= "==============REQUEST END===============<br><br>";
		$down_str .= "==============RESPONSE START===============<br>";
		$down_str .= $xml_log["data"][0]["response"]."<br>";
		$down_str .= "==============RESPONSE END===============";
    	
		header("Content-type: text/plain");
		header("Content-Disposition: attachment; filename=response_log.txt");
		echo $down_str;
    }

	function xml_logs($offset=0)
    {
        $condition = array();
        $page_data['table_data'] = $this->domain_management_model->xml_logs($condition, false, $offset, RECORDS_RANGE_3);
        $total_records = $this->domain_management_model->xml_logs($condition, true);
        $this->load->library('pagination');
        $config['base_url'] = base_url().'index.php/management/xml_logs/';
        $page_data['total_rows'] = $config['total_rows'] = $total_records;
        $config['per_page'] = RECORDS_RANGE_3;
        $this->pagination->initialize($config);
        /** TABLE PAGINATION */
        $page_data['total_records'] = $config['total_rows'];
		$page_data["db"] = "normal";
        $this->template->view('management/xml_logs', $page_data);
    }
    function download_xml_log($log_id)
    {
    	$xml_log = $this->custom_db->single_table_records("test", "*", array("origin" => $log_id));
    	header("Content-type: text/plain");
		header("Content-Disposition: attachment; filename=response_log.txt");
		echo $xml_log["data"][0]["test"];
    }
    function flush_xml_logs()
    {
        $today = new DateTime(date("Y-m-d H:i:s"));
        $today->modify("-30 day");
        $previous_date = $today->format("Y-m-d H:i:s");

        $this->custom_db->delete_record("test", array("time <" => $previous_date));
        set_update_message();
        redirect(base_url().'index.php/management/xml_logs');
    }

    function upload_tds_certificates()
    {
    	//debug($_FILES["tds_certificates"]); exit;
    	$error = ""; $err_count = 0; $size = 0; $stop_loop = 0; $db_data=array(); $temp_paths = array();
    	foreach($_FILES["tds_certificates"] as $key => $vals)
    	{
    		foreach ($vals as $index => $val) {
    			switch($key)
    			{
    				case "name":
    					if(empty($val))
						{
							$err_count = 1;
							$error = "No files uploaded.<br>";
							$stop_loop = 1;
						}
						$file_name = rtrim(strtolower($val), ".pdf");
						$fn_array = explode("_", $file_name);
						if(count($fn_array) != 3)
						{
							$err_count = 1;
    						$error .= $file_name." - Wrong name format";
						}
						$file_data[$index]["upload_date"] = date("Y-m-d h:i:s");
						$file_data[$index]["name"] = $file_name;
						$file_data[$index]["pan_no"] = $fn_array[0];
						$file_data[$index]["quater"] = $fn_array[1];
						$file_data[$index]["year"] = $fn_array[2];
						$file_data[$index]["file_name"] = $file_name."_".time().".pdf";
    					break;

    				case "tmp_name":
    					$temp_paths[$index] = $val;
    					break;

    				case "size":
    					$size += $val;
    					break;

    				case "type":
    					if($val!="application/pdf")
    					{
    						$err_count = 1;
    						$error = "System accepts only PDF files. One or more of your files are not of type PDF.<br>";
    						$stop_loop = 1;
    					}
    					break;

    				case "error":
    					if($val!=0)
    					{
    						$err_count = 1;
    						$error .= $val."<br>";
    						$stop_loop = 1;
    					}
    					break;
    			}
    			if($stop_loop)
    				break;
    		}
    		if($stop_loop)
    			break;
    	}
    	if($size > 600000000)
    	{
    		$err_count = 1;
    		$error .= "Total files size must not exceed 6MB";
    	}
    	if($err_count)
    	{
    		$this->session->set_flashdata("msg", "<div class='alert alert-danger'>".$error."</div>");
    		redirect(base_url("index.php/management/tds_certificates"));
    	}
    	$this->custom_db->db->trans_begin();
    	foreach ($file_data as $key => $value) {
    		$temp_path = $temp_paths[$key];
			$upload_path = $_SERVER["DOCUMENT_ROOT"].$this->template->domain_uploads().'/tds_certificates/'.$value["file_name"];
			$insert_data = array("name" => $value["name"], "pan_no" => $value["pan_no"],
			"quater" => $value["quater"], "year" => $value["year"], "file_name" => $value["file_name"], "upload_date" => $value["upload_date"]);
			$this->custom_db->insert_record('tds_certificates', $insert_data);
			if(!move_uploaded_file($temp_path, $upload_path)){
				$this->custom_db->db->trans_rollback();
				$this->session->set_flashdata("msg", "Something went wrong, please try again.");
				redirect(base_url("index.php/management/tds_certificates"));
			}
		}
		$this->custom_db->db->trans_commit();
		$this->session->set_flashdata("msg", "<div class='alert alert-success'>Files uploaded sucessfully.</div>");
		redirect(base_url("index.php/management/tds_certificates"));
    }

    function tds_certificates($offset=0)
    {
        $condition = array();
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
	 * Balu A
	 * Update B2B Agent Commission
	 */
	function agent_commission($offset=0)
	{
		$get_data = $this->input->get();
		$post_data = $this->input->post();
		// debug($post_data);exit;
		$page_data = array();
		$this->load->library('Api_Interface');
		if(isset($get_data['agent_ref_id']) == true && empty($get_data['agent_ref_id']) == false && valid_array($post_data) == false) {
			//Get Data
			$agent_ref_id = base64_decode(trim($get_data['agent_ref_id']));
			$page_data['agent_ref_id'] = $agent_ref_id;
			$agent_commission_details = $this->domain_management_model->get_commission_details($agent_ref_id);
			if($agent_commission_details['status'] == true) {
				$page_data['commission_details'] = $agent_commission_details['data'];
			} else {
				//Invalid CRUD
				redirect('security/log_event?event=InvalidAgent');
			}
		} else if(valid_array($post_data) == true && isset($post_data['module']) == true && empty($post_data['module']) == false) {
			foreach($post_data['module'] as $module_k => $module_v) {
				$module = trim($module_v);
				$module = trim($module_v);
				switch ($module) {
					case META_AIRLINE_COURSE://Airline Commission
						$update_flight_commission_data['module'] = $post_data['module'][$module_k];
						$update_flight_commission_data['agent_ref_id'] = $post_data['agent_ref_id'][$module_k];
						$update_flight_commission_data['flight_commission_origin'] = $post_data['commission_origin'][$module_k];
						$update_flight_commission_data['flight_commission'] = $post_data['commission'][$module_k];
						$update_flight_commission_data['api_value'] = $post_data['api_value'][$module_k];
						$this->update_b2b_flight_commission($update_flight_commission_data);
						break;
					case META_BUS_COURSE://Bus Commission
						$update_bus_commission_data['module'] = $post_data['module'][$module_k];
						$update_bus_commission_data['agent_ref_id'] = $post_data['agent_ref_id'][$module_k];
						$update_bus_commission_data['bus_commission_origin'] = $post_data['commission_origin'][$module_k];
						$update_bus_commission_data['bus_commission'] = $post_data['commission'][$module_k];
						$update_bus_commission_data['api_value'] = $post_data['api_value'][$module_k];
						$this->update_b2b_bus_commission($update_bus_commission_data);
						break;
					case META_SIGHTSEEING_COURSE://Sightseeing Commission

						$update_sightseeing_commission_data['module'] = $post_data['module'][$module_k];
						$update_sightseeing_commission_data['agent_ref_id'] = $post_data['agent_ref_id'][$module_k];
						$update_sightseeing_commission_data['sightseeing_commission_origin'] = $post_data['commission_origin'][$module_k];
						$update_sightseeing_commission_data['sightseeing_commission'] = $post_data['commission'][$module_k];
						$update_sightseeing_commission_data['api_value'] = $post_data['api_value'][$module_k];
						$this->update_b2b_sightseeing_commission($update_sightseeing_commission_data);
						break;
					case META_TRANSFERV1_COURSE://Sightseeing Commission

						$update_transfer_commission_data['module'] = $post_data['module'][$module_k];
						$update_transfer_commission_data['agent_ref_id'] = $post_data['agent_ref_id'][$module_k];
						$update_transfer_commission_data['transfer_commission_origin'] = $post_data['commission_origin'][$module_k];
						$update_transfer_commission_data['transfer_commission'] = $post_data['commission'][$module_k];
						$update_transfer_commission_data['api_value'] = $post_data['api_value'][$module_k];
						$this->update_b2b_transfer_commission($update_transfer_commission_data);
						break;

				}
			}
			set_update_message();
			if(empty($_SERVER['QUERY_STRING']) == false) {
				$query_string = '?'.$_SERVER['QUERY_STRING'];
			} else {
				$query_string = '';
			}
			redirect('management/agent_commission'.$query_string);
		}
		if(isset($get_data['default_commission']) == true && $get_data['default_commission'] == ACTIVE) {
			//Default Commission
			$page_data['default_commission'] = ACTIVE;
			$commission_details = $this->domain_management_model->default_commission_details();//Default Commission Details
			$page_data['commission_details'] = $commission_details['data'];
		} else {
			
			//Agent's List
			if(isset($get_data['filter']) == true && $get_data['filter'] == 'search_agent' &&
			isset($get_data['filter_agency']) == true && empty($get_data['filter_agency']) == false) {
				$filter_agency = trim($get_data['filter_agency']);
				//Search Filter
				$search_filter_condition = '(U.uuid like "%'.$filter_agency.'%" OR U.agency_name like "%'.$filter_agency.'%" OR U.first_name like "%'.$filter_agency.'%" OR U.last_name like "%'.$filter_agency.'%" OR U.email like "%'.$filter_agency.'%" OR U.phone like "%'.$filter_agency.'%")';
				$total_records = $this->domain_management_model->filter_agent_commission_details($search_filter_condition, true);
				$agent_list = $this->domain_management_model->filter_agent_commission_details($search_filter_condition, false, $offset, RECORDS_RANGE_1);
			} else {
				/** TABLE PAGINATION */
				$condition[] = array('U.user_type', ' IN', '('.B2B_USER.')');
				//princess added (agent list only active)
				$condition[]= array('U.status', 'IN', '(1)');
				//$page_data['agent_list'] = $this->user_model->get_domain_user_list($condition, false, $offset, RECORDS_RANGE_1);
	
				$total_records = $this->domain_management_model->agent_commission_details($condition, true);
				$agent_list = $this->domain_management_model->agent_commission_details($condition, false, $offset, RECORDS_RANGE_1);
			}
			$page_data['agent_list'] = $agent_list['data']['agent_commission_details'] ;
			$this->load->library('pagination');
			if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
			$config['base_url'] = base_url().'index.php/management/agent_commission/';
			$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
			$config['total_rows'] = $total_records->total;
			$config['per_page'] = RECORDS_RANGE_1;
			$this->pagination->initialize($config);
			/** TABLE PAGINATION */
		}
		/****************Super Admin Commission**********************/
		$sa_bus_commission = json_decode($this->api_interface->rest_service('bus_commission_details'),true);
		//Bus
		if($sa_bus_commission['status'] == true) {
			$super_admin_bus_commission['value'] = $sa_bus_commission['data'][0]['value'];
			$super_admin_bus_commission['api_value'] = $sa_bus_commission['data'][0]['api_value'];
		} else {
			$super_admin_bus_commission['value'] = 0;
			$super_admin_bus_commission['api_value'] = 0;
		}
		//Flight
		$sa_admin_flight_commission = json_decode($this->api_interface->rest_service('airline_commission_details'),true);
		//debug($sa_admin_flight_commission); die;
		if($sa_admin_flight_commission['status'] == true) {
			$super_admin_flight_commission['value'] = $sa_admin_flight_commission['data'][0]['value'];
			$super_admin_flight_commission['api_value'] = $sa_admin_flight_commission['data'][0]['api_value'];
		} else {
			$super_admin_flight_commission['value'] = 0;
			$super_admin_flight_commission['api_value'] = 0;
		}

		$sa_sightseeing_commission = json_decode($this->api_interface->rest_service('sightseeing_commission_details'),true);
		//Bus
		if($sa_sightseeing_commission['status'] == true) {
			$super_admin_sightseeing_commission['value'] = $sa_sightseeing_commission['data'][0]['value'];
			$super_admin_sightseeing_commission['api_value'] = $sa_sightseeing_commission['data'][0]['api_value'];
		} else {
			$super_admin_sightseeing_commission['value'] = 0;
			$super_admin_sightseeing_commission['api_value'] = 0;
		}
		$sa_transfer_commission = json_decode($this->api_interface->rest_service('transfer_commission_details'),true);
		//Transfers
		if($sa_transfer_commission['status'] == true) {
			$super_admin_transfer_commission['value'] = $sa_transfer_commission['data'][0]['value'];
			$super_admin_transfer_commission['api_value'] = $sa_transfer_commission['data'][0]['api_value'];
		} else {
			$super_admin_transfer_commission['value'] = 0;
			$super_admin_transfer_commission['api_value'] = 0;
		}

		$page_data['super_admin_bus_commission'] = $super_admin_bus_commission;
		$page_data['super_admin_flight_commission'] = $super_admin_flight_commission;
		$page_data['super_admin_sightseeing_commission'] = $super_admin_sightseeing_commission;
		$page_data['super_admin_transfer_commission'] = $super_admin_transfer_commission;
		
		// debug($page_data);
		// exit;
		/****************Super Admin Commission**********************/

		$this->template->view('management/agent_commission', $page_data);
	}

	 /**
	 * Shashikumar Misal
	 * Update B2B Agent Commission Airline Wise
	 */
	function b2b_airline_wise_commission($offset=0)
	{
		$form_data = $this->input->post();
		//debug($form_data); exit;
		switch($form_data["form_values_origin"])
		{
			case 'add_airline';
			if(isset($form_data['airline_code']) == true && empty($form_data['airline_code']) == false) {
				$airline_code = trim($form_data['airline_code']);
				$comm_details = $this->domain_management_model->individual_airline_commission_details($airline_code); //Yet to Define
				$airline_origin= intval($comm_details['airline_origin']);
				if(intval($comm_details['comm_origin']) > 0) {
					$comm_origin = intval($comm_details['comm_origin']);
				} else {
					$comm_origin = 0;
				}
				$value=$form_data['specific_value']*10;
				$value_type="percentage";
				$this->domain_management_model->save_comm_data(
						$comm_origin, FLIGHT_SPECIFIC, $airline_origin, 
						$value, $form_data['specific_value'], 
						$value_type, get_domain_auth_id()
						);
				
			}
			break;

			case 'update_existing_airline_commissions':
			foreach($form_data['airline_origin'] as $__k => $__domain_origin) {
				if ($form_data['specific_value'][$__k] != '' && intval($form_data['specific_value'][$__k]) > -1) {
					$value=$form_data['specific_value'][$__k]*10;
					$value_type="percentage";
					$this->domain_management_model->save_comm_data(
					$form_data['com_origin'][$__k], FLIGHT_SPECIFIC, 
					$form_data['airline_origin'][$__k],	$value, 
					$form_data['specific_value'][$__k], $value_type, get_domain_auth_id());
				}
			}
			break;
		}
		$page_data = $this->domain_management_model->b2b_airline_commission();
		//debug($page_data ); exit;
		$airline_list = $this->db_cache_api->get_airline_list();
		$page_data['airline_list'] = $airline_list;
		$this->template->view('management/b2b_airline_commission', $page_data);
	}

	function b2b_supplier_wise_commission($offset=0)
	{
		$form_data = $this->input->post();
		//debug($form_data); exit;
		if(isset($_GET["agent_id"])){
		foreach($form_data['booking_source_origin'] as $k => $bs_origin) {
			if ($form_data['specific_value'][$k] != '' && intval($form_data['specific_value'][$k]) > -1) {
				$bs_origin=$form_data['booking_source_origin'][$k];
				$value=$form_data['specific_value'][$k]*10;
				$value_type="percentage";
				
			if($form_data['comm_origin'][$k]!='' && intval($form_data['comm_origin'][$k]) > 0)
					$comm_origin=$form_data['comm_origin'][$k];
				else