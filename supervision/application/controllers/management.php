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
		$this->load->library('provab_sms');
		$this->load->library('api_balance_manager');
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
					$page_data['form_data']['generic_value'], $page_data['form_data']['value_type'], get_domain_auth_id(), $page_data['form_data']['generic_value_i'], $page_data['form_data']['value_type_i']
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
					$page_data['form_data']['generic_value'], $page_data['form_data']['value_type'], get_domain_auth_id(), $user_oid, $page_data['form_data']['generic_value'], $page_data['form_data']['value_type']
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
				
				if(!empty($data) && isset($data['list'][0])){


					$master_transaction['master_transaction'] = $data['list'][0];
					$email = $page_data['form_data']['request_user_email'];
					$phone = $page_data['form_data']['request_user_phone'];
					//$email=  "sagar@mailinator.com";

					$mail_template = $this->template->isolated_view('user/deposit_confirmation_template',$master_transaction);
					$this->load->library('provab_mailer');
 					$status = $this->provab_mailer->send_mail($email,'Account Deposit', $mail_template);


				    $msg_data=array();
                    $msg_data['amount']=$master_transaction['master_transaction']['amount'];
                    $msg_data['agency']=$master_transaction['master_transaction']['requested_from'];
                    
				    $this->provab_sms->send_msg($phone ,$msg_data, $sms_id=599904);

				   // $this->provab_sms->send_msg($phone1 ,$msg);
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

		$page_data['provab_balance_requests'] = get_enum_list('provab_balance_requests');
		$page_data['provab_balance_status'] = get_enum_list('provab_balance_status');
		if (empty($page_data['form_data']['currency_converter_origin']) == true) {
			$page_data['form_data']['currency_converter_origin']	= COURSE_LIST_DEFAULT_CURRENCY;
			$page_data['form_data']['conversion_value']				= 1;
		}
		$page_data['status_options'] = get_enum_list('provab_balance_status');
		$page_data['agency_list'] = $this->domain_management_model->get_agent_list();
		$page_data['user_list'] = $this->domain_management_model->get_user_list();
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
					$comm_origin=0;

				$this->domain_management_model->save_supplier_specific_comm_data(
				$comm_origin, SUPPLIER_SPECIFIC, $bs_origin, $value, 
				$form_data['specific_value'][$k], $value_type, get_domain_auth_id(), 
				$_GET["agent_id"]);
			}
		}

		$page_data["booking_sources"]="";
		$page_data["booking_sources"] = $this->module_model->get_booking_sources(array("meta_course_list_id = '".META_AIRLINE_COURSE."'"), true);
		}
		else
		{
			//Agent's List
			/** TABLE PAGINATION */
			$condition[] = array('U.user_type', ' IN', '('.B2B_USER.')');
			//princess added (agent list only active)
			$condition[]= array('U.status', 'IN', '(1)');
			//$page_data['agent_list'] = $this->user_model->get_domain_user_list($condition, false, $offset, RECORDS_RANGE_1);

			$total_records = $this->user_model->b2b_user_list('', true);
			$agent_list = $this->user_model->b2b_user_list('', false, $offset, RECORDS_RANGE_1);
			//debug($agent_list); exit;
			$page_data['agent_list'] = $agent_list;

			$this->load->library('pagination');
			if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
			$config['base_url'] = base_url().'index.php/management/b2b_supplier_wise_commission/';
			$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
			$config['total_rows'] = $total_records->total;
			$config['per_page'] = RECORDS_RANGE_1;
			$this->pagination->initialize($config);
			/** TABLE PAGINATION */
		}
		$page_data["mgmt"]=$this; //$this->module_model->get_booking_source_with_comm() is called from view in loop
		$this->template->view('management/b2b_supplier_commission', $page_data);
	}

	/**
	 * Balu A
	 * Update Flight Commission Details
	 * @param $commission_details
	 */
	function update_b2b_flight_commission($commission_details)
	{
		if(isset($commission_details['module']) == true && empty($commission_details['module']) == false &&
		isset($commission_details['agent_ref_id']) == true && empty($commission_details['agent_ref_id']) == false &&
		isset($commission_details['flight_commission_origin']) == true && isset($commission_details['flight_commission']) == true) {
			$origin = trim($commission_details['flight_commission_origin']);
			$agent_ref_id = base64_decode(trim($commission_details['agent_ref_id']));
			$commission_value = floatval(trim($commission_details['flight_commission']));
			$api_value = floatval(trim($commission_details['api_value']));
			$b2b_flight_commission_details = array();
			if(intval($agent_ref_id) > 0) {
				$b2b_flight_commission_details['type'] = SPECIFIC;
			} else {
				$b2b_flight_commission_details['type'] = GENERIC;
			}
			$b2b_flight_commission_details['value'] = $commission_value;
			$b2b_flight_commission_details['api_value'] = $api_value;
			$b2b_flight_commission_details['value_type'] = MARKUP_VALUE_PERCENTAGE;
			$b2b_flight_commission_details['commission_currency'] = MARKUP_CURRENCY;
			$b2b_flight_commission_details['created_by_id'] = $this->entity_user_id;
			$b2b_flight_commission_details['created_datetime'] = date('Y-m-d H:i:s');
			if($origin >0) {
				//UPDATE
				if(intval($agent_ref_id) > 0) {//Specific Agent Commission
					$update_condition['agent_fk'] = $agent_ref_id;
				} else {//Default Commission
					$update_condition['type'] = GENERIC;
				}
				$this->custom_db->update_record('b2b_flight_commission_details', $b2b_flight_commission_details, $update_condition);
			} else {
				//ADD
				$b2b_flight_commission_details['agent_fk'] = $agent_ref_id;
				$b2b_flight_commission_details['domain_list_fk'] = get_domain_auth_id();
				if(intval($agent_ref_id) > 0) {//Specific Agent Commission
					$delete_condition['agent_fk'] = $agent_ref_id;
				} else {//Default Commission
					$delete_condition['type'] = GENERIC;
				}
				$this->custom_db->delete_record('b2b_flight_commission_details', $delete_condition);
				$this->custom_db->insert_record('b2b_flight_commission_details', $b2b_flight_commission_details);
			}
		} else {
			redirect('security/log_event?event=InvalidFlightCommissionDetails');
		}
	}
	/**
	 * Balu A
	 * Update Bus Commission Details
	 * @param $commission_details
	 */
	function update_b2b_bus_commission($commission_details)
	{
		if(isset($commission_details['module']) == true && empty($commission_details['module']) == false &&
		isset($commission_details['agent_ref_id']) == true && empty($commission_details['agent_ref_id']) == false &&
		isset($commission_details['bus_commission_origin']) == true && isset($commission_details['bus_commission']) == true) {
			$origin = trim($commission_details['bus_commission_origin']);
			$agent_ref_id = base64_decode(trim($commission_details['agent_ref_id']));
			$commission_value = floatval(trim($commission_details['bus_commission']));
			$api_value = floatval(trim($commission_details['api_value']));
			$b2b_bus_commission_details = array();
			if(intval($agent_ref_id) > 0) {
				$b2b_bus_commission_details['type'] = SPECIFIC;
			} else {
				$b2b_bus_commission_details['type'] = GENERIC;
			}
			$b2b_bus_commission_details['value'] = $commission_value;
			$b2b_bus_commission_details['api_value'] = $api_value;
			$b2b_bus_commission_details['value_type'] = MARKUP_VALUE_PERCENTAGE;
			$b2b_bus_commission_details['commission_currency'] = MARKUP_CURRENCY;
			$b2b_bus_commission_details['created_by_id'] = $this->entity_user_id;
			$b2b_bus_commission_details['created_datetime'] = date('Y-m-d H:i:s');
			if($origin >0) {
				//UPDATE
				if(intval($agent_ref_id) > 0) {//Specific Agent Commission
					$update_condition['agent_fk'] = $agent_ref_id;
				} else {//Default Commission
					$update_condition['type'] = GENERIC;
				}
				$this->custom_db->update_record('b2b_bus_commission_details', $b2b_bus_commission_details, $update_condition);
			} else {
				//ADD
				$b2b_bus_commission_details['agent_fk'] = $agent_ref_id;
				$b2b_bus_commission_details['domain_list_fk'] = get_domain_auth_id();
				if(intval($agent_ref_id) > 0) {//Specific Agent Commission
					$delete_condition['agent_fk'] = $agent_ref_id;
				} else {//Default Commission
					$delete_condition['type'] = GENERIC;
				}
				$this->custom_db->delete_record('b2b_bus_commission_details', $delete_condition);
				$this->custom_db->insert_record('b2b_bus_commission_details', $b2b_bus_commission_details);
			}
		} else {
			redirect('security/log_event?event=InvalidBusCommissionDetails');
		}
	}
	/**
	 * Elavarasi
	 * Update Sightseeing Commission Details
	 * @param $commission_details
	 */
	function update_b2b_sightseeing_commission($commission_details)
	{
		if(isset($commission_details['module']) == true && empty($commission_details['module']) == false &&
		isset($commission_details['agent_ref_id']) == true && empty($commission_details['agent_ref_id']) == false &&
		isset($commission_details['sightseeing_commission_origin']) == true && isset($commission_details['sightseeing_commission']) == true) {
			$origin = trim($commission_details['sightseeing_commission_origin']);
			$agent_ref_id = base64_decode(trim($commission_details['agent_ref_id']));
			$commission_value = floatval(trim($commission_details['sightseeing_commission']));
			$api_value = floatval(trim($commission_details['api_value']));
			$b2b_sightseeing_commission_details = array();
			if(intval($agent_ref_id) > 0) {
				$b2b_sightseeing_commission_details['type'] = SPECIFIC;
			} else {
				$b2b_sightseeing_commission_details['type'] = GENERIC;
			}
			$b2b_sightseeing_commission_details['value'] = $commission_value;
			$b2b_sightseeing_commission_details['api_value'] = $api_value;
			$b2b_sightseeing_commission_details['value_type'] = MARKUP_VALUE_PERCENTAGE;
			$b2b_sightseeing_commission_details['commission_currency'] = MARKUP_CURRENCY;
			$b2b_sightseeing_commission_details['created_by_id'] = $this->entity_user_id;
			$b2b_sightseeing_commission_details['created_datetime'] = date('Y-m-d H:i:s');
			if($origin >0) {
				//UPDATE
				if(intval($agent_ref_id) > 0) {//Specific Agent Commission
					$update_condition['agent_fk'] = $agent_ref_id;
				} else {//Default Commission
					$update_condition['type'] = GENERIC;
				}
				$this->custom_db->update_record('b2b_sightseeing_commission_details', $b2b_sightseeing_commission_details, $update_condition);
			} else {
				//ADD
				$b2b_sightseeing_commission_details['agent_fk'] = $agent_ref_id;
				$b2b_sightseeing_commission_details['domain_list_fk'] = get_domain_auth_id();
				if(intval($agent_ref_id) > 0) {//Specific Agent Commission
					$delete_condition['agent_fk'] = $agent_ref_id;
				} else {//Default Commission
					$delete_condition['type'] = GENERIC;
				}
				$this->custom_db->delete_record('b2b_sightseeing_commission_details', $delete_condition);
				$this->custom_db->insert_record('b2b_sightseeing_commission_details', $b2b_sightseeing_commission_details);
			}
		} else {
			redirect('security/log_event?event=InvalidBusCommissionDetails');
		}
	}
	function user_group($group_id = 0, $action = '') {
        if (!check_user_previlege('p14')) {
            set_update_message("You Don't have permission to do this action.", WARNING_MESSAGE, array(
                'override_app_msg' => true
            ));
            redirect(base_url());
        }
        $post_data = $this->input->post();
        $condition = array();
        $page_data ['action'] = 'create';
        if ($action != '' && valid_array($post_data)) {
        	
            if ($action == 'create') {
                $post_data ['created_by_id'] = $this->entity_user_id;
                $post_data ['created_datetime'] = date('Y-m-d H:i:s', time());
                $post_data ['type'] = 'custom';
                $group_data = $this->db->insert('user_groups', $post_data);
            } else if ($action == 'update') {
                $condition ['type'] = 'custom';
                $condition ['origin'] = $group_id;
                $group_data = $this->db->update('user_groups', $post_data, $condition);
            } 
            redirect(base_url() . 'index.php/management/' . __FUNCTION__);
        }else if($action != '' && $action == 'delete'){
        	if ($action == 'delete') {
        		$user_data['group_fk']="1";
        		$con['group_fk'] = $group_id;
        		$group_data = $this->db->update('user', $user_data, $con);
        		
        		$condition ['type'] = 'custom';
        		$condition ['origin'] = $group_id;
        		$this->db->delete('user_groups', $condition);
        	
        		redirect(base_url('index.php/management/user_group'));
        	}
        }
        $page_data ['group_id'] = $group_id;
        $page_data ['table_data'] = $this->db->get('user_groups')->result_array();
        if ($group_id > 0) {
            $page_data ['current_group'] = $page_data ['table_data'] [array_search($group_id, array_column($page_data ['table_data'], 'origin'))];
            $page_data ['action'] = 'update';
        }
        $this->template->view('management/user_group', $page_data);
    }
	/**
	 * Elavarasi
	 * Update Transfer Commission Details
	 * @param $commission_details
	 */
	function update_b2b_transfer_commission($commission_details)
	{
		if(isset($commission_details['module']) == true && empty($commission_details['module']) == false &&
		isset($commission_details['agent_ref_id']) == true && empty($commission_details['agent_ref_id']) == false &&
		isset($commission_details['transfer_commission_origin']) == true && isset($commission_details['transfer_commission']) == true) {
			$origin = trim($commission_details['transfer_commission_origin']);
			$agent_ref_id = base64_decode(trim($commission_details['agent_ref_id']));
			$commission_value = floatval(trim($commission_details['transfer_commission']));
			$api_value = floatval(trim($commission_details['api_value']));
			$b2b_transfer_commission_details = array();
			if(intval($agent_ref_id) > 0) {
				$b2b_transfer_commission_details['type'] = SPECIFIC;
			} else {
				$b2b_transfer_commission_details['type'] = GENERIC;
			}
			$b2b_transfer_commission_details['value'] = $commission_value;
			$b2b_transfer_commission_details['api_value'] = $api_value;
			$b2b_transfer_commission_details['value_type'] = MARKUP_VALUE_PERCENTAGE;
			$b2b_transfer_commission_details['commission_currency'] = MARKUP_CURRENCY;
			$b2b_transfer_commission_details['created_by_id'] = $this->entity_user_id;
			$b2b_transfer_commission_details['created_datetime'] = date('Y-m-d H:i:s');
			if($origin >0) {
				//UPDATE
				if(intval($agent_ref_id) > 0) {//Specific Agent Commission
					$update_condition['agent_fk'] = $agent_ref_id;
				} else {//Default Commission
					$update_condition['type'] = GENERIC;
				}
				$this->custom_db->update_record('b2b_transfer_commission_details', $b2b_transfer_commission_details, $update_condition);
			} else {
				//ADD
				$b2b_transfer_commission_details['agent_fk'] = $agent_ref_id;
				$b2b_transfer_commission_details['domain_list_fk'] = get_domain_auth_id();
				if(intval($agent_ref_id) > 0) {//Specific Agent Commission
					$delete_condition['agent_fk'] = $agent_ref_id;
				} else {//Default Commission
					$delete_condition['type'] = GENERIC;
				}
				$this->custom_db->delete_record('b2b_transfer_commission_details', $delete_condition);
				$this->custom_db->insert_record('b2b_transfer_commission_details', $b2b_transfer_commission_details);
			}
		} else {
			redirect('security/log_event?event=InvalidBusCommissionDetails');
		}
	}
	/**
	 * Balu A
	 * Manages Bank Account Details
	 */
	function bank_account_details()
	{
		$post_data['form_data'] = $this->input->post();
		$get_data = $this->input->get();
		$page_data['form_data'] = '';
		if(valid_array($post_data['form_data']) == false && isset($get_data['eid']) && intval($get_data['eid'])>0) {
			$temp_data=$this->custom_db->single_table_records('bank_account_details', '*', array('origin' => $get_data['eid']));
			$page_data['form_data'] = array();
			$page_data['form_data']['origin']=$temp_data['data'][0]['origin'];
			$page_data['form_data']['en_account_name']=$temp_data['data'][0]['en_account_name'];
			$page_data['form_data']['en_bank_name']=$temp_data['data'][0]['en_bank_name'];
			$page_data['form_data']['en_branch_name']=$temp_data['data'][0]['en_branch_name'];
			$page_data['form_data']['bank_icon']=$temp_data['data'][0]['bank_icon'];
			$page_data['form_data']['account_number']=$temp_data['data'][0]['account_number'];
			$page_data['form_data']['ifsc_code']=$temp_data['data'][0]['ifsc_code'];
			$page_data['form_data']['pan_number']=$temp_data['data'][0]['pan_number'];
			$page_data['form_data']['status']=$temp_data['data'][0]['status'];
			//debug($page_data); exit;
		} else if( valid_array($post_data['form_data']) ) {
			//$post_data['form_data']["origin"] = 0;
			if($post_data['form_data']['origin'] == ''){
				$page_data['form_data']['origin'] = 0;
			}
			$this->current_page->set_auto_validator();
			if ($this->form_validation->run()) {
				$origin = intval($post_data['form_data']['origin']);
				unset($post_data['form_data']['FID']);
				unset($post_data['form_data']['origin']);
				if($origin > 0) {
					/** UPDATE **/

					$post_data['form_data']['updated_by_id'] = $this->entity_user_id;
					$post_data['form_data']['updated_datetime'] = date('Y-m-d H:i:s');
					$this->custom_db->update_record('bank_account_details', $post_data['form_data'],array('origin' => $origin) );
					set_update_message();
				} elseif($origin == 0){
					/** INSERT **/
					$post_data['form_data']['domain_list_fk'] = get_domain_auth_id();;
					$post_data['form_data']['created_by_id'] = $this->entity_user_id;
					$post_data['form_data']['created_datetime'] = date('Y-m-d H:i:s');
					$post_data['form_data']['bank_icon'] = "";
					$post_data['form_data']['updated_by_id'] = $this->entity_user_id;
					$post_data['form_data']['updated_datetime'] = date('Y-m-d H:i:s');
					$insert_id=$this->custom_db->insert_record('bank_account_details',$post_data['form_data']);
					set_insert_message();
				}
				//FILE UPLOAD
				if (valid_array($_FILES) == true and $_FILES['bank_icon']['error'] == 0 and $_FILES['bank_icon']['size'] > 0) {
					if( function_exists( "check_mime_image_type" ) ) {
						    if ( !check_mime_image_type( $_FILES['bank_icon']['tmp_name'] ) ) {
						    	echo "Please select the image files only (gif|jpg|png|jpeg)"; exit;
						    }
					}
					$config['upload_path'] = $this->template->domain_image_full_path ().'bank_logo/';
					$config['allowed_types'] = 'gif|jpg|png|jpeg';
					$config['file_name'] = time();
					$config['max_size'] = MAX_DOMAIN_LOGO_SIZE;
					$config['max_width']  = MAX_DOMAIN_LOGO_WIDTH;
					$config['max_height']  = MAX_DOMAIN_LOGO_HEIGHT;
					$config['remove_spaces']  = false;
					if (empty($insert_id) == true) {
						//UPDATE
						$temp_record = $this->custom_db->single_table_records('bank_account_details', 'bank_icon', array('origin' => $origin));
						$icon = $temp_record['data'][0]['bank_icon'];
						//DELETE OLD FILES
						if (empty($icon) == false) {
							if (file_exists($config['upload_path'].$icon)) {
								unlink($config['upload_path'].$icon);
							}
						}
					} else {
						$origin = $insert_id['insert_id'];
					}
					//UPLOAD IMAGE
					$this->load->library('upload', $config);
					if ( ! $this->upload->do_upload('bank_icon')) {
						//echo $this->upload->display_errors();
					} else {
						$image_data =  $this->upload->data();
					}
					$this->custom_db->update_record('bank_account_details', array('bank_icon' => $image_data['file_name']), array('origin' => $origin));
				}
				redirect('management/bank_account_details');
			}
			echo validation_errors(); exit;
		} else {
			$page_data['form_data']['origin']=0;
		}
		/** Table Data **/
		$temp_data=$this->domain_management_model->bank_account_details();
		if($temp_data['status']) {
			$page_data['table_data'] = $temp_data['data'];
		} else {
			$page_data['table_data'] = "";
		}
		$this->template->view('management/bank_account_details',$page_data);
	}

	function check_user_group(){
      $group_name = $this->input->post('name');
      $action = $this->input->post('action');
      $group_id = $this->input->post('group_id');
      $group_status = $this->custom_db->single_table_records('user_groups','*',array('name'=>$group_name));
      if($action == 'update'){
        if($group_status['status'] == TRUE){
          $current_group_id = $group_status['data'][0]['origin'];
          if($current_group_id == $group_id){
            $condition ['type'] = 'custom';
            $condition ['origin'] = $group_id;
            $post_data ['name'] = $group_name;
            $group_data = $this->db->update('user_groups', $post_data, $condition);
            set_update_message();
            echo TRUE;
            exit;
          }else{
            echo FALSE;
            exit;
          } 
        }else{
          $condition ['type'] = 'custom';
          $condition ['origin'] = $group_id;
          $post_data ['name'] = $group_name;
          $group_data = $this->db->update('user_groups', $post_data, $condition);
          set_update_message();
          echo TRUE;
          exit;
        }
      }
      if($group_status['status'] == TRUE){
        debug($group_id);
        debug($group_status);
        exit;  
        echo FALSE;
        exit;
      }else{
        if($action == 'create'){
          $post_data ['created_by_id'] = $this->entity_user_id;
          $post_data ['created_datetime'] = date('Y-m-d H:i:s', time());
          $post_data ['type'] = 'custom';
          $post_data ['name'] = $group_name;
          $group_data = $this->db->insert('user_groups', $post_data);
          set_insert_message();
        }
        echo TRUE;
        exit;
      }
    }
	/*
	 *Admin Account Ledger
	 *
	*/

	public function account_ledger($offset=0)
	{
		$get_data = $this->input->get();
		$condition = array();
		$page_data = array();
		
		$agent_details = array();

		if(isset($get_data['agent_id']) == true && intval($get_data['agent_id']) >0 ){
			$agent_id = intval($get_data['agent_id']);
		} else{
			$agent_id = 0;
		}

		//$condition[] = array('U.user_id', '=', $agent_id);
		$complete_agent_details = $this->domain_management_model->get_agent_details($agent_id);
		if(valid_array($complete_agent_details) == true){
			$agent_details['agency_name'] = $complete_agent_details['agency_name'];
			$agent_details['agent_balance'] = $complete_agent_details['balance'];
			$agent_details['current_credit_limit'] = $complete_agent_details['credit_limit'];
			$agent_details['due_amount'] = $complete_agent_details['credit_limit'];
			$agent_details['agent_currency'] = $complete_agent_details['agent_base_currency'];
		}
		$page_data['agent_details'] = $agent_details;

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
			$condition[] = array('date(t.created_datetime)', '>=', $this->db->escape($ymd_from_date));
		}
		if(empty($to_date) == false) {
			$ymd_to_date = date('Y-m-d', strtotime($to_date));
			$condition[] = array('date(t.created_datetime)', '<=', $this->db->escape($ymd_to_date));
		}
		if (empty($get_data['app_reference']) == false) {
			$condition[] = array('t.app_reference', ' like ', $this->db->escape('%'.$get_data['app_reference'].'%'));
		}
		// if (empty($get_data['transaction_type']) == false) {
		// 	$condition[] = array('t.transaction_type', ' like ', $this->db->escape('%'.$get_data['transaction_type'].'%'));
		// }
		//Transaction Data
		$total_records = $this->domain_management_model->agent_account_ledger($condition, true);
		$total_records = $total_records['total_records'];
		$transaction_logs = $this->domain_management_model->agent_account_ledger($condition, false, $offset, RECORDS_RANGE_3);
		//$transaction_logs = format_account_ledger($transaction_logs['data']);
		$page_data['table_data'] = $transaction_logs;
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
		// get active agent list
		$agent_list['data'] = $this->domain_management_model->agent_list();
		$page_data['agent_list'] = magical_converter(array('k' => 'user_id', 'v' => 'agency_name'), $agent_list);
		$page_data['agency_list'] = $this->domain_management_model->get_agent_list();
		$this->template->view ( 'management/account_ledger', $page_data );
	}

	/*
	*Export Account Ledger details to Excel Format
	*/
	public function export_account_ledger($op=''){
		
		$get_data = $this->input->GET();
		$condition = array();

		if(isset($get_data['agent_id']) == true && intval($get_data['agent_id']) >0 ){
			$agent_id = intval($get_data['agent_id']);
		} else{
			$agent_id = 0;
		}
		$condition[] = array('U.user_id', '=', $agent_id);
		
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
	        				 'g' => 'closing_balance');    

	        $excel_sheet_properties = array(
	        				'title' => 'Account_Ledger_'.date('d-M-Y'), 
	        				'creator' => 'Provab', 
	        				'description' => 'Account Ledger of All Clients', 
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

	/*
	*Getting Admin Balance
	*/
	public function get_travelomatix_balance()
	{

		$balance = current_application_balance(); 
                
		$json_arr = array('face_value'=>$balance['face_value'],'credit_limit'=>$balance['credit_limit'],'due_amount'=>$balance['due_amount']);				
		echo json_encode($json_arr);
		exit;
	}
	//GST Details
	function gst_master(){
		$post_data = $this->input->post ();
	    $condition = array(); 
	    if (isset($post_data) && valid_array($post_data)) {
 			for( $i=0;$i < COUNT($post_data['gst_origin']); $i++) {
	            $data = array(
	                'tds' => 0,//$post_data['tds'][$i],
	                'gst' => $post_data['gst'][$i],
	                'modified_date' => date('Y-m-d H:i:s')
	            );
	            
	            $update_origin = $post_data['gst_origin'][$i];
	            $condition ['origin'] = $update_origin;
	            $group_data = $this->db->update ( 'gst_master', $data, $condition );
	        }
	        redirect ( base_url () . 'index.php/management/' . __FUNCTION__ );
	    }
		$page_data['details'] = $this->db->get ( 'gst_master' )->result_array ();
		$this->template->view('management/gst_master', $page_data);
	}
	 /** Anitha G Update Credit Limit ** /
     * 
     */
    public function credit_balance_show() {

        $get_data = $this->input->get();

    	// debug($get_data);exit;
        if (valid_array($get_data) == true && empty($get_data['agent_id']) == false ) {
			$user_details = $this->user_model->get_agent_info($get_data['agent_id']);
			// debug($user_details);exit;
            if (valid_array($user_details) == false) {//Invalid Domain ID
                redirect(base_url());
            }
            $user_details = $user_details[0];
            $page_data['user_details'] = $user_details;
        } 
		$this->template->view('management/credit_limit', $page_data);
    }

     public function credit_balance_update() {
        $get_data = $this->input->post();
        // debug($get_data);exit;
        if (valid_array($get_data) == true && empty($get_data['origin']) == false) {
            $page_data['credit_limit']=$get_data['amount'];
            $page_data['user_id']=$get_data['user_id'];
            $page_data['origin']=$get_data['origin'];
         	$transaction_owner_id = $get_data['user_id'];
         	$app_reference = "CL-".$get_data['user_id']."-".time();
         	$fare = 0-$page_data['credit_limit']; $convinence=0;
         	$domain_markup = 0; $discount=0;
         	$level_one_markup = 0; $currency_conversion_rate=1;
         	$remarks = "Credit Limit Updated"; $is_paid_by_pg = 0;
         	$discount=0; $currency='INR'; $is_paid_by_pg = 0;
         	$this->domain_management_model->save_transaction_details("credit_limit", $app_reference, $fare, $domain_markup, $level_one_markup, $remarks, $convinence, $discount, $currency, $currency_conversion_rate, $transaction_owner_id, $is_paid_by_pg, $page_data['credit_limit']);

         	$Update_info = $this->user_model->update_credit_limit($page_data);
            $user_details = $this->user_model->get_agent_info($get_data['user_id']);
            $user_details = $user_details[0];
         	$page_data['user_details'] = $user_details;
	    }
        
        $this->template->view('management/credit_limit', $page_data);
    }

    //Limit markup for agent
    function b2b_markup_limit(){
		$post_data = $this->input->post ();
	    $condition = array(); 
	    if (isset($post_data) && valid_array($post_data)) {
	    	//debug($post_data);die();
 			for( $i=0;$i < COUNT($post_data['origin']); $i++) {
	            $data = array(
	                'module_type' => $post_data['module_type'][$i],
	                'value' => $post_data['markup'][$i],
	                'value_type' => $post_data['value_type'][$i],
	                'created_by' => 0,
	                'updated_when' => date('Y-m-d H:i:s')
	            );
	            
	            $update_origin = $post_data['origin'][$i];
	            $condition ['origin'] = $update_origin;
	            $group_data = $this->db->update ( 'agent_markup_limit', $data, $condition );
	        }
	        redirect ( base_url () . 'index.php/management/' . __FUNCTION__ );
	    }
		$page_data['details'] = $this->db->get ( 'agent_markup_limit' )->result_array ();
		//debug($page_data);die();
		$this->template->view('management/b2b_markup_limit', $page_data);
	}

	function bus_operator_cancellation($id = ''){

		$page_data ['form_data'] = $this->input->post ();
		if (valid_array ( $page_data ['form_data'] ) == true) {
			$page_data ['form_data'] ['status_updated_by'] = $this->entity_user_id;
			$page_data ['form_data'] ['updated_date'] = date("Y-m-d");
			//debug($page_data ['form_data']); exit;
			// Insert
			$this->custom_db->update_record("bus_operator_cancellation", 
				$page_data ['form_data'], array("id"=>$id));
			//echo $this->custom_db->db->last_query(); exit;
			redirect ( base_url () . 'index.php/management/' . __FUNCTION__);
		}
		$params = $this->input->get();
		$data_list_filt = array();
		if (isset($params['bus_pnr']) == true and empty($params['bus_pnr']) == false) {
			$data_list_filt[] = array('BOC.bus_pnr', 'like', $this->db->escape('%'.trim($params['bus_pnr']).'%'));
		}
		if (isset($params['created_date_from']) == true and empty($params['created_datetime_from']) == false) {
			$data_list_filt[] = array('BOC.created_date', '>=', $this->db->escape(db_current_datetime($params['created_date_from'])));
		}
		if (isset($params['created_date_to']) == true and empty($params['created_date_to']) == false) {
			$data_list_filt[] = array('BOC.created_date', '<=', $this->db->escape(db_current_datetime($params['created_date_to'])));
		}
		$page_data['search_params'] = $params;
		$page_data ['table_data'] = $this->domain_management_model->boc_request_list($data_list_filt);
		//debug($page_data);
		$this->template->view ('management/bus_operator_cancellation', $page_data);
	}
	public function master_commission(){
		$page_data = array();
		$post_params = $this->input->post();
		if(valid_array($post_params)){
			if(isset($post_params['group_generic_course_id'])){
				$course_id = $post_params['group_generic_course_id'];
			}else{
				$course_id = $post_params['specific_course_id'][0];
			}
			switch ($course_id) {
				case META_AIRLINE_COURSE:
					$table_name = 'b2b_flight_commission_details';
					break;
				case META_SIGHTSEEING_COURSE:
					$table_name = 'b2b_sightseeing_commission_details';
					break;
				case META_TRANSFERV1_COURSE:
					$table_name = 'b2b_transfer_commission_details';
					break;
				case META_BUS_COURSE:
					$table_name = 'b2b_bus_commission_details';
					break;
				default:
					$table_name = 'b2b_flight_commission_details';
					break;
			}
			switch ($post_params['method']) {
				case 'generic':
					if(empty($post_params['origin'][0])){
						$insert_data = array();
						$insert_data['value'] = $post_params['group_generic_value'];
						$insert_data['tds'] = $post_params['group_generic_tds'];
						$insert_data['value_type'] = 'percentage';
						$insert_data['is_master'] = "1";
						$insert_data['course_id'] = $post_params['group_generic_course_id'];
						$insert_data['domain_list_fk'] = 1;
						$insert_data['commission_currency'] = 'INR';
						$insert_data['created_by_id'] = $this->entity_user_id;

						$this->custom_db->insert_record($table_name, $insert_data);
					}else{
						$update_data = array();
						$update_data['value'] = $post_params['group_generic_value'];
						$update_data['tds'] = $post_params['group_generic_tds'];
						$update_data['value_type'] = $post_params['group_generic_value_type'];
						$update_condition = array('origin' => $post_params['origin'][0],'is_master' => '1');
						$this->custom_db->update_record($table_name, $update_data, $update_condition);
					}
				break;
				case 'specific':
					for($i=0; $i<count($post_params['specific_value']);$i++){
						if(empty($post_params['origin'][$i])){
							$insert_data = array();
							$insert_data['value'] = $post_params['specific_value'][$i];
							$insert_data['tds'] = $post_params['specific_tds'][$i];
							$insert_data['type'] = 'specific';
							$insert_data['value_type'] = 'percentage';
							$insert_data['is_master'] = "1";
							$insert_data['course_id'] = $post_params['specific_course_id'][$i];
							$insert_data['booking_source_origin'] = $post_params['specific_booking_source'][$i];
							$insert_data['domain_list_fk'] = 1;
							$insert_data['commission_currency'] = 'INR';
							$insert_data['created_by_id'] = $this->entity_user_id;
							$this->custom_db->insert_record($table_name, $insert_data);
						}else{
							$update_data = array();
							$update_data['value'] = $post_params['specific_value'][$i];
							$update_data['tds'] = $post_params['specific_tds'][$i];
							$update_condition = array('origin' => $post_params['origin'][$i],'is_master' => '1');
							$this->custom_db->update_record($table_name, $update_data, $update_condition);
						}
					}
				break;
			}
			$page_data['post_params'] = $post_params;
			set_update_message();
			redirect(base_url().'index.php/management/master_commission');
		}
		$active_modules = $this->domain_management_model->get_active_modules();
		$page_data['active_modules'] = $active_modules;
		$this->template->view ('management/master_commission', $page_data);	
	}
	public function group_commission(){
		$page_data = array();
		$post_params = $this->input->post();
		if(valid_array($post_params)){
			if($_SERVER['REMOTE_ADDR'] == '182.156.244.142'){
				//debug($post_params);die();
			}
			if(isset($post_params['group_generic_course_id'])){
				$course_id = $post_params['group_generic_course_id'];
			}else{
				$course_id = $post_params['specific_course_id'][0];
			}
			switch ($course_id) {
				case 'VHCID1420613784':
					$table_name = 'b2b_flight_commission_details';
					break;
				case 'TMCID1524458882':
					$table_name = 'b2b_sightseeing_commission_details';
					break;
				case 'TMVIATID1527240212':
					$table_name = 'b2b_transfer_commission_details';
					break;
				case 'VHCID1433498307':
					$table_name = 'b2b_bus_commission_details';
					break;
				default:
					$table_name = 'b2b_flight_commission_details';
					break;
			}
			switch ($post_params['method']) {
				case 'generic':
					if(empty($post_params['origin'][0])){
						$insert_data = array();
						$insert_data['value'] = $post_params['group_generic_value'];
						$insert_data['value_type'] = 'percentage';
						$insert_data['group_fk'] = $post_params['group_fk'];
						$insert_data['course_id'] = $post_params['group_generic_course_id'];
						$insert_data['domain_list_fk'] = 1;
						$insert_data['commission_currency'] = 'INR';
						$insert_data['created_by_id'] = 31;
						$this->custom_db->insert_record($table_name, $insert_data);
					}else{
						$update_data = array();
						$update_data['value'] = $post_params['group_generic_value'];
						$update_data['value_type'] = $post_params['group_generic_value_type'];
						$update_condition = array('origin' => $post_params['origin'][0],'is_master' => '0');
						$this->custom_db->update_record($table_name, $update_data, $update_condition);
					}
				break;
				case 'specific':
					for($i=0; $i<count($post_params['specific_value']);$i++){
						if(empty($post_params['origin'][$i])){
							$insert_data = array();
							$insert_data['value'] = $post_params['specific_value'][$i];
							$insert_data['type'] = 'specific';
							$insert_data['value_type'] = 'percentage';
							$insert_data['group_fk'] = $post_params['group_fk'];
							$insert_data['course_id'] = $post_params['specific_course_id'][$i];
							$insert_data['booking_source_origin'] = $post_params['specific_booking_source'][$i];
							$insert_data['domain_list_fk'] = 1;
							$insert_data['commission_currency'] = 'INR';
							$insert_data['created_by_id'] = 31;
							$this->custom_db->insert_record($table_name, $insert_data);
						}else{
							$update_data = array();
							$update_data['value'] = $post_params['specific_value'][$i];
							$update_condition = array('origin' => $post_params['origin'][$i],'is_master' => '0');
							$this->custom_db->update_record($table_name, $update_data, $update_condition);
							if($_SERVER['REMOTE_ADDR'] == '182.156.244.142'){
								//debug($this->db->last_query());die();
							}
						}
					}
				break;
			}
			$page_data['post_params'] = $post_params;
			set_update_message();
			redirect(base_url().'index.php/management/group_commission');
		}
		$groups = $this->domain_management_model->get_groups();
		$active_modules = $this->domain_management_model->get_active_modules();
		$page_data['active_modules'] = $active_modules;
		$page_data['groups'] = $groups;
		$this->template->view ('management/group_commission', $page_data);	
	}

	public function agent_commissions(){
		$page_data = array();
		$post_params = $this->input->post();
		if(valid_array($post_params)){
			$airline_list = $this->custom_db->single_table_records('airline_list','*');
			if($airline_list['status'] == SUCCESS_STATUS){
				$page_data['airline_list'] = $airline_list['data'];
			}
			if(isset($post_params['group_generic_course_id'])){
				$course_id = $post_params['group_generic_course_id'];
			}else{
				$course_id = $post_params['specific_course_id'][0];
			}
			switch ($course_id) {
				case 'VHCID1420613784':
					$table_name = 'b2b_flight_commission_details';
					break;
				case 'TMCID1524458882':
					$table_name = 'b2b_sightseeing_commission_details';
					break;
				case 'TMVIATID1527240212':
					$table_name = 'b2b_transfer_commission_details';
					break;
				case 'VHCID1433498307':
					$table_name = 'b2b_bus_commission_details';
					break;
				default:
					$table_name = 'b2b_flight_commission_details';
					break;
			}
			
			switch ($post_params['method']) {
				case 'generic':
					if(empty($post_params['origin'][0])){
						$insert_data = array();
						$insert_data['value'] = $post_params['group_generic_value'];
						$insert_data['value_type'] = 'percentage';
						$insert_data['agent_fk'] = $post_params['user_oid'];
						$insert_data['course_id'] = $post_params['group_generic_course_id'];
						$insert_data['domain_list_fk'] = 1;
						$insert_data['commission_currency'] = 'INR';
						$insert_data['created_by_id'] = 31;
						$this->custom_db->insert_record($table_name, $insert_data);
					}else{
						$update_data = array();
						$update_data['value'] = $post_params['group_generic_value'];
						$update_data['value_type'] = $post_params['group_generic_value_type'];
						$update_condition = array('origin' => $post_params['origin'][0],'is_master' => '0');
						$this->custom_db->update_record($table_name, $update_data, $update_condition);
					}
				break;
				case 'specific':
					for($i=0; $i<count($post_params['specific_value']);$i++){
						if(empty($post_params['origin'][$i])){
							$insert_data = array();
							$insert_data['value'] = $post_params['specific_value'][$i];
							$insert_data['type'] = 'specific';
							$insert_data['value_type'] = 'percentage';
							$insert_data['agent_fk'] = $post_params['user_oid'];
							$insert_data['course_id'] = $post_params['specific_course_id'][$i];
							$insert_data['booking_source_origin'] = $post_params['specific_booking_source'][$i];
							$insert_data['domain_list_fk'] = 1;
							$insert_data['commission_currency'] = 'INR';
							$insert_data['created_by_id'] = 31;
							$this->custom_db->insert_record($table_name, $insert_data);
						}else{
							$update_data = array();
							$update_data['value'] = $post_params['specific_value'][$i];
							$update_condition = array('origin' => $post_params['origin'][$i],'is_master' => '0');
							$this->custom_db->update_record($table_name, $update_data, $update_condition);
						}
					}
				break;
			}
			set_update_message();
			redirect(base_url().'index.php/management/agent_commissions');
		}
		$agent = $this->domain_management_model->get_active_agent();
		$active_modules = $this->domain_management_model->get_active_modules();
		$page_data['active_modules'] = $active_modules;
		$page_data['agent'] = $agent;
		$this->template->view ('management/agent_commissions', $page_data);	
	}
	public function get_master_suppliers(){
		$post_params = $this->input->post();
		$module = $post_params['module'];
		$trip_type = $post_params['trip_type'];
		$supplier = $post_params['tp_supplier'];
		if($module == 'VHCID1433498307'){
			$get_bitla_operator = $this->domain_management_model->get_bitla_operator($module);
		}
		$supplier_list = $this->domain_management_model->get_supllier_list($module);
		foreach($supplier_list as $supp_key => $supp_value){
			if($supp_value['source_id'] == $supplier){
				$supp_origin = $supp_value['origin'];
			}	
		}
		$commission = array();
		switch ($post_params['module']) {
				case 'VHCID1420613784':
					$table_name = 'b2b_flight_commission_details';
					break;
				case 'TMCID1524458882':
					$table_name = 'b2b_sightseeing_commission_details';
					break;
				case 'TMVIATID1527240212':
					$table_name = 'b2b_transfer_commission_details';
					break;
				case 'VHCID1433498307':
					$table_name = 'b2b_bus_commission_details';
					break;
				default:
					$table_name = 'b2b_flight_commission_details';
					break;
		}
		$general_module_commission = array();
		$generic_datas = $this->custom_db->single_table_records($table_name,'*',array('course_id' => $post_params['module'], 'type' => 'generic', 'is_master' => "1"));
		if($generic_datas['status'] == SUCCESS_STATUS){
			$general_module_commission['value'] = $generic_datas['data'][0]['value'];
			$general_module_commission['tds'] = $generic_datas['data'][0]['tds'];
			$general_module_commission['value_type'] = 'percentage';
			$general_module_commission['booking_source'] = '';
			$general_module_commission['course_id'] = $module;
			$general_module_commission['origin'] = $generic_datas['data'][0]['origin'];	
		}else{
			$general_module_commission['value'] = 0;
			$general_module_commission['tds'] = 0;
			$general_module_commission['value_type'] = 'percentage';
			$general_module_commission['booking_source'] = '';
			$general_module_commission['course_id'] = $module;	
			$general_module_commission['origin'] = '';
		}
		$commission['generic'] = $general_module_commission;
		foreach($supplier_list as $sl_key => $sl_val){
			$get_commission = $this->domain_management_model->get_master_commission($table_name, $sl_val['origin']);
			if(empty($get_commission)){
				$commission['specific'][$sl_key]['value'] = 0;
				$commission['specific'][$sl_key]['tds'] = 0;
				$commission['specific'][$sl_key]['value_type'] = 'percentage';
				$commission['specific'][$sl_key]['course_id'] = $sl_val['meta_course_list_id'];
				$commission['specific'][$sl_key]['name'] = $sl_val['name'];
				$commission['specific'][$sl_key]['booking_source'] = $sl_val['origin'];
				$commission['specific'][$sl_key]['origin'] = '';
			}else{

				$commission['specific'][$sl_key]['value'] = $get_commission[0]['value'];
				$commission['specific'][$sl_key]['tds'] = $get_commission[0]['tds'];
				$commission['specific'][$sl_key]['origin'] = $get_commission[0]['origin'];
				$commission['specific'][$sl_key]['value_type'] = 'percentage';
				$commission['specific'][$sl_key]['course_id'] = $sl_val['meta_course_list_id'];
				$commission['specific'][$sl_key]['name'] = $sl_val['name'];
				$commission['specific'][$sl_key]['booking_source'] = $sl_val['origin'];
			}
		}

		$airline_list = $this->custom_db->single_table_records('airline_list','*');
		if($airline_list['status'] == SUCCESS_STATUS){
			$commission['airline_list'] = $airline_list['data'];
		}
		$commission_airline_list = $this->domain_management_model->get_commissioned_airline_master($trip_type, $supp_origin);
		if(!empty($commission_airline_list)){
			$commission['comm_airline_list'] = $commission_airline_list;
		}else{
			$commission['comm_airline_list'] = array();
		}
		$commission_operator_list = $this->domain_management_model->get_commissioned_operator_master($group_fk);		
		if(!empty($commission_operator_list)){
			$commission['comm_operator_list'] = $commission_operator_list;
		}else{
			$commission['comm_operator_list'] = array();
		}
		$commission['trip_type'] = $trip_type;
		$commission['supplier'] = $supplier;
		if($module == 'VHCID1433498307'){
			$commission['bitla_operators'] = $get_bitla_operator;
		}
		$supplier_view = $this->template->isolated_view('management/supplier_master_commission',$commission);
		echo $supplier_view;
	}
	public function get_suppliers(){
		$post_params = $this->input->post();
		$module = $post_params['module'];
		if($module == 'VHCID1433498307'){
			$get_bitla_operator = $this->domain_management_model->get_bitla_operator_group($module, $post_params['group_fk']);
		}
		$group_fk = $post_params['group_fk'];
		$trip_type = $post_params['trip_type'];
		$supplier = $post_params['tp_supplier'];
		$supplier_list = $this->domain_management_model->get_supllier_list($module);
		foreach($supplier_list as $supp_key => $supp_value){
			if($supp_value['source_id'] == $supplier){
				$supp_origin = $supp_value['origin'];
			}	
		}
		$commission = array();
		switch ($post_params['module']) {
				case 'VHCID1420613784':
					$table_name = 'b2b_flight_commission_details';
					break;
				case 'TMCID1524458882':
					$table_name = 'b2b_sightseeing_commission_details';
					break;
				case 'TMVIATID1527240212':
					$table_name = 'b2b_transfer_commission_details';
					break;
				case 'VHCID1433498307':
					$table_name = 'b2b_bus_commission_details';
					break;
				default:
					$table_name = 'b2b_flight_commission_details';
					break;
		}
		$general_module_commission = array();
		$generic_datas = $this->custom_db->single_table_records($table_name,'*',array('course_id' => $post_params['module'], 'type' => 'generic','group_fk' => $group_fk));
		if($generic_datas['status'] == SUCCESS_STATUS){
			$general_module_commission['value'] = $generic_datas['data'][0]['value'];
			$general_module_commission['value_type'] = 'percentage';
			$general_module_commission['booking_source'] = '';
			$general_module_commission['course_id'] = $module;
			$general_module_commission['origin'] = $generic_datas['data'][0]['origin'];	
		}else{
			$general_module_commission['value'] = 0;
			$general_module_commission['value_type'] = 'percentage';
			$general_module_commission['booking_source'] = '';
			$general_module_commission['course_id'] = $module;	
			$general_module_commission['origin'] = '';
		}
		
		$commission['group_fk'] = $group_fk;
		$commission['generic'] = $general_module_commission;
		foreach($supplier_list as $sl_key => $sl_val){
			if($table_name == 'b2b_bus_commission_details'){
				$for_supp = 1;
			}
			else
				$for_supp = 0;
			$get_commission = $this->domain_management_model->get_commission($table_name, $sl_val['origin'], $group_fk, $for_supp);
			//echo $this->domain_management_model->db->last_query(); exit;
			if(empty($get_commission)){
				$commission['specific'][$sl_key]['value'] = 0;
				$commission['specific'][$sl_key]['value_type'] = 'percentage';
				$commission['specific'][$sl_key]['course_id'] = $sl_val['meta_course_list_id'];
				$commission['specific'][$sl_key]['name'] = $sl_val['name'];
				$commission['specific'][$sl_key]['booking_source'] = $sl_val['origin'];
				$commission['specific'][$sl_key]['origin'] = '';
			}else{

				$commission['specific'][$sl_key]['value'] = $get_commission[0]['value'];
				$commission['specific'][$sl_key]['origin'] = $get_commission[0]['origin'];
				$commission['specific'][$sl_key]['value_type'] = 'percentage';
				$commission['specific'][$sl_key]['course_id'] = $sl_val['meta_course_list_id'];
				$commission['specific'][$sl_key]['name'] = $sl_val['name'];
				$commission['specific'][$sl_key]['booking_source'] = $sl_val['origin'];
			}
		}
		$airline_list = $this->custom_db->single_table_records('airline_list','*');
		if($airline_list['status'] == SUCCESS_STATUS){
			$commission['airline_list'] = $airline_list['data'];
		}
		$commission_airline_list = $this->domain_management_model->get_commissioned_airline($group_fk, $trip_type, $supp_origin);

		if(!empty($commission_airline_list)){
			$commission['comm_airline_list'] = $commission_airline_list;
		}else{
			$commission['comm_airline_list'] = array();
		}
		$commission_operator_list = $this->domain_management_model->get_commissioned_operator(@$group_fk);
		if(!empty($commission_operator_list)){
			$commission['comm_operator_list'] = $commission_operator_list;
		}else{
			$commission['comm_operator_list'] = array();
		}
		$commission['trip_type'] = $trip_type;
		$commission['supplier'] = $supplier;
		if($module == 'VHCID1433498307'){
			$commission['bitla_operators'] = $get_bitla_operator;
		}
		$supplier_view = $this->template->isolated_view('management/supplier_commission',$commission);
		echo $supplier_view;
	}
	public function get_suppliers_for_agent(){
		$post_params = $this->input->post();
		$module = $post_params['module'];
		$user_oid = $post_params['user_oid'];
		if($module == 'VHCID1433498307'){
			$get_bitla_operator = $this->domain_management_model->get_bitla_operator_agent($module, $user_oid);
		}
		$trip_type = $post_params['trip_type'];
		$supplier = $post_params['tp_supplier'];
		$supplier_list = $this->domain_management_model->get_supllier_list($module);
		foreach($supplier_list as $supp_key => $supp_value){
			if($supp_value['source_id'] == $supplier){
				$supp_origin = $supp_value['origin'];
			}	
		}
		$commission = array();
		switch ($post_params['module']) {
				case 'VHCID1420613784':
					$table_name = 'b2b_flight_commission_details';
					break;
				case 'TMCID1524458882':
					$table_name = 'b2b_sightseeing_commission_details';
					break;
				case 'TMVIATID1527240212':
					$table_name = 'b2b_transfer_commission_details';
					break;
				case 'VHCID1433498307':
					$table_name = 'b2b_bus_commission_details';
					break;
				default:
					$table_name = 'b2b_flight_commission_details';
					break;
		}
		$general_module_commission = array();
		$generic_datas = $this->custom_db->single_table_records($table_name,'*',array('course_id' => $post_params['module'], 'type' => 'generic','agent_fk' => $user_oid,'is_master'=>'0'));

		if($generic_datas['status'] == SUCCESS_STATUS){
			$general_module_commission['value'] = $generic_datas['data'][0]['value'];
			$general_module_commission['value_type'] = 'percentage';
			$general_module_commission['booking_source'] = '';
			$general_module_commission['course_id'] = $module;
			$general_module_commission['origin'] = $generic_datas['data'][0]['origin'];	
		}else{
			$general_module_commission['value'] = 0;
			$general_module_commission['value_type'] = 'percentage';
			$general_module_commission['booking_source'] = '';
			$general_module_commission['course_id'] = $module;	
			$general_module_commission['origin'] = '';
		}
		
		$commission['agent_fk'] = $user_oid;
		$commission['generic'] = $general_module_commission;
		foreach($supplier_list as $sl_key => $sl_val){
			$get_commission = $this->domain_management_model->get_supplier_commission($table_name, $sl_val['origin'], $user_oid);
			if(empty($get_commission)){
				$commission['specific'][$sl_key]['value'] = 0;
				$commission['specific'][$sl_key]['value_type'] = 'percentage';
				$commission['specific'][$sl_key]['course_id'] = $sl_val['meta_course_list_id'];
				$commission['specific'][$sl_key]['name'] = $sl_val['name'];
				$commission['specific'][$sl_key]['booking_source'] = $sl_val['origin'];
				$commission['specific'][$sl_key]['origin'] = '';
			}else{

				$commission['specific'][$sl_key]['value'] = $get_commission[0]['value'];
				$commission['specific'][$sl_key]['origin'] = $get_commission[0]['origin'];
				$commission['specific'][$sl_key]['value_type'] = 'percentage';
				$commission['specific'][$sl_key]['course_id'] = $sl_val['meta_course_list_id'];
				$commission['specific'][$sl_key]['name'] = $sl_val['name'];
				$commission['specific'][$sl_key]['booking_source'] = $sl_val['origin'];
			}
		}

		$airline_list = $this->custom_db->single_table_records('airline_list','*');
		if($airline_list['status'] == SUCCESS_STATUS){
			$commission['airline_list'] = $airline_list['data'];
		}
		$commission_airline_list = $this->domain_management_model->get_commissioned_airline_agent($user_oid, $trip_type);
		if(!empty($commission_airline_list)){
			$commission['comm_airline_list'] = $commission_airline_list;
		}else{
			$commission['comm_airline_list'] = array();
		}
		$commission_operator_list = $this->domain_management_model->get_commissioned_operator_agent($user_oid);
		if(!empty($commission_operator_list)){
			$commission['comm_operator_list'] = $commission_operator_list;
		}else{
			$commission['comm_operator_list'] = array();
		}
		$commission['trip_type'] = $trip_type;
		$commission['supplier'] = $supplier;
		if($module == 'VHCID1433498307'){
			$commission['bitla_operators'] = $get_bitla_operator;
		}
		$supplier_view = $this->template->isolated_view('management/supplier_agent_commission',$commission);
		echo $supplier_view;
	}
	function add_master_wise_commission(){
		$post_params = $this->input->post();
		
		if(valid_array($post_params)){
			if(isset($post_params['add'])){
				if($post_params['selected_supplier'] == TRAVELPORT_ACH_BOOKING_SOURCE){
					$gds_origin = $this->custom_db->single_table_records('booking_source','origin',array('source_id' => TRAVELPORT_ACH_BOOKING_SOURCE));
				}else{
					$gds_origin = $this->custom_db->single_table_records('booking_source','origin',array('source_id' => TRAVELPORT_GDS_BOOKING_SOURCE));	
				}
				$gds_origin = $gds_origin['data'][0]['origin'];
				$insert_data = array();
				$insert_data['value'] = $post_params['basic'];
				$insert_data['plb'] = $post_params['plb'];
				$insert_data['yq'] = $post_params['yq'];
				$insert_data['iata'] = $post_params['iata'];
				$insert_data['tds'] = $post_params['tds'];
				$insert_data['type'] = 'specific';
				$insert_data['value_type'] = 'percentage';
				$insert_data['is_master'] = "1";
				$insert_data['course_id'] = META_AIRLINE_COURSE;
				$insert_data['booking_source_origin'] = $gds_origin;
				$insert_data['domain_list_fk'] = 1;
				$insert_data['commission_currency'] = 'INR';
				$insert_data['created_by_id'] = 31;
				$insert_data['trip_type'] = $post_params['selected_trip_type'];
				$insert_data['airline_class'] = $post_params['class'];
				$insert_data['airline_id'] = $post_params['airline_origin'];
				$this->custom_db->insert_record('b2b_flight_commission_details', $insert_data);
			}else{
				for($i=0;$i<count($post_params['origin']);$i++){
					$update_data = array();
					$update_data['airline_class'] = $post_params['airline_class'][$i];
					$update_data['value'] = $post_params['value'][$i];
					$update_data['plb'] = $post_params['plb'][$i];
					$update_data['yq'] = $post_params['yq'][$i];
					$update_data['iata'] = $post_params['iata'][$i];
					$update_data['tds'] = $post_params['tds'][$i];

					$update_condition = array('origin' => $post_params['origin'][$i]);
					$this->custom_db->update_record('b2b_flight_commission_details', $update_data, $update_condition);
				}
			}
			set_update_message();
		}
		redirect(base_url().'index.php/management/master_commission');
	}
	function add_airline_wise_commission(){
		$post_params = $this->input->post();
		if(valid_array($post_params)){
			if(isset($post_params['add'])){
				if($post_params['selected_supplier'] == TRAVELPORT_ACH_BOOKING_SOURCE){
					$gds_origin = $this->custom_db->single_table_records('booking_source','origin',array('source_id' => TRAVELPORT_ACH_BOOKING_SOURCE));
				}else{
					$gds_origin = $this->custom_db->single_table_records('booking_source','origin',array('source_id' => TRAVELPORT_GDS_BOOKING_SOURCE));	
				}
				$gds_origin = $gds_origin['data'][0]['origin'];
				$insert_data = array();
				$insert_data['value'] = $post_params['basic'];
				$insert_data['plb'] = $post_params['plb'];
				$insert_data['yq'] = $post_params['yq'];
				$insert_data['iata'] = $post_params['iata'];
				$insert_data['tds'] = $post_params['tds'];
				$insert_data['type'] = 'specific';
				$insert_data['value_type'] = 'percentage';
				$insert_data['group_fk'] = $post_params['group_fk'];
				$insert_data['course_id'] = META_AIRLINE_COURSE;
				$insert_data['booking_source_origin'] = $gds_origin;
				$insert_data['domain_list_fk'] = 1;
				$insert_data['commission_currency'] = 'INR';
				$insert_data['created_by_id'] = 31;
				$insert_data['trip_type'] = $post_params['selected_trip_type'];
				$insert_data['airline_class'] = $post_params['class'];
				$insert_data['airline_id'] = $post_params['airline_origin'];
				$this->custom_db->insert_record('b2b_flight_commission_details', $insert_data);
			}else{
				for($i=0;$i<count($post_params['origin']);$i++){
					$update_data = array();
					$update_data['airline_class'] = $post_params['airline_class'][$i];
					$update_data['value'] = $post_params['value'][$i];
					$update_data['plb'] = $post_params['plb'][$i];
					$update_data['yq'] = $post_params['yq'][$i];
					$update_data['iata'] = $post_params['iata'][$i];
					$update_data['tds'] = $post_params['tds'][$i];

					$update_condition = array('origin' => $post_params['origin'][$i]);
					$this->custom_db->update_record('b2b_flight_commission_details', $update_data, $update_condition);
				}
			}
			set_update_message();
		}
		redirect(base_url().'index.php/management/group_commission');
	}
	function add_airline_airline_wise_commission(){
		$post_params = $this->input->post();
		
		if(valid_array($post_params)){
			if(isset($post_params['add'])){
				if($post_params['selected_supplier'] == TRAVELPORT_ACH_BOOKING_SOURCE){
					$gds_origin = $this->custom_db->single_table_records('booking_source','origin',array('source_id' => TRAVELPORT_ACH_BOOKING_SOURCE));
				}else{
					$gds_origin = $this->custom_db->single_table_records('booking_source','origin',array('source_id' => TRAVELPORT_GDS_BOOKING_SOURCE));	
				}
				$gds_origin = $gds_origin['data'][0]['origin'];
				$insert_data = array();
				$insert_data['value'] = $post_params['basic'];
				$insert_data['plb'] = $post_params['plb'];
				$insert_data['yq'] = $post_params['yq'];
				$insert_data['iata'] = $post_params['iata'];
				$insert_data['tds'] = $post_params['tds'];
				$insert_data['type'] = 'specific';
				$insert_data['value_type'] = 'percentage';
				$insert_data['agent_fk'] = $post_params['agent_fk'];
				$insert_data['course_id'] = META_AIRLINE_COURSE;
				$insert_data['booking_source_origin'] = $gds_origin;
				$insert_data['domain_list_fk'] = 1;
				$insert_data['commission_currency'] = 'INR';
				$insert_data['created_by_id'] = 31;
				$insert_data['trip_type'] = $post_params['selected_trip_type'];
				$insert_data['airline_class'] = $post_params['class'];
				$insert_data['airline_id'] = $post_params['airline_origin'];
				$this->custom_db->insert_record('b2b_flight_commission_details', $insert_data);
			}else{
				for($i=0;$i<count($post_params['origin']);$i++){
					$update_data = array();
					$update_data['airline_class'] = $post_params['airline_class'][$i];
					$update_data['value'] = $post_params['value'][$i];
					$update_data['plb'] = $post_params['plb'][$i];
					$update_data['yq'] = $post_params['yq'][$i];
					$update_data['iata'] = $post_params['iata'][$i];
					$update_data['tds'] = $post_params['tds'][$i];
					$update_condition = array('origin' => $post_params['origin'][$i]);
					$this->custom_db->update_record('b2b_flight_commission_details', $update_data, $update_condition);
				}
			}
			set_update_message();
		}
		redirect(base_url().'index.php/management/agent_commissions');
	}
	
	public function add_operator_wise_commission(){
		$post_params = $this->input->post();
		if(valid_array($post_params)){
			if(isset($post_params['add'])){
				foreach ($post_params['booking_source'] as $b_key => $b_value) {
					$bitla_bus_com = $this->custom_db->single_table_records('b2b_bus_commission_details','*',array('booking_source_origin' => $b_value, 'group_fk' => $post_params['group_fk']));
					if($bitla_bus_com['status'] == SUCCESS_STATUS){
						$bitla_origin = $b_value;
						$insert_data = array();
						if(empty($post_params['commission_value'][$b_key])){
							$com_val = 0;
						}else{
							$com_val = $post_params['commission_value'][$b_key];
						}
						if(empty($post_params['tds'][$b_key])){
							$com_tds = 0;
						}else{
							$com_tds = $post_params['tds'][$b_key];
						}
						$insert_data['value'] = $com_val;
						$insert_data['tds'] = $com_tds;
						$insert_data['is_master'] = "0";
						$update_condition = array('booking_source_origin' => $bitla_origin, 'group_fk' => $post_params['group_fk']);
						$this->custom_db->update_record('b2b_bus_commission_details', $insert_data, $update_condition);
					}else{
						$bitla_origin = $b_value;
						$insert_data = array();
						if(empty($post_params['commission_value'][$b_key])){
							$com_val = 0;
						}else{
							$com_val = $post_params['commission_value'][$b_key];
						}
						if(empty($post_params['tds'][$b_key])){
							$com_tds = 0;
						}else{
							$com_tds = $post_params['tds'][$b_key];
						}
						$insert_data['value'] = $com_val;
						$insert_data['type'] = 'specific';
						$insert_data['value_type'] = 'percentage';
						$insert_data['is_master'] = "0";
						$insert_data['group_fk'] = $post_params['group_fk'];
						$insert_data['course_id'] = META_BUS_COURSE;
						$insert_data['booking_source_origin'] = $bitla_origin;
						$insert_data['domain_list_fk'] = 1;
						$insert_data['commission_currency'] = 'INR';
						$insert_data['created_by_id'] = 31;
						$insert_data['tds'] = $com_tds;
						$this->custom_db->insert_record('b2b_bus_commission_details', $insert_data);
					}	
				}


				/*$bitla_origin = $this->custom_db->single_table_records('booking_source','origin',array('source_id' => BITLA_BUS_BOOKING_SOURCE));
				$bitla_origin = $bitla_origin['data'][0]['origin'];
				$insert_data = array();
				$insert_data['value'] = $post_params['commission_value'];
				$insert_data['type'] = 'specific';
				$insert_data['value_type'] = 'percentage';
				$insert_data['group_fk'] = $post_params['group_fk'];
				$insert_data['course_id'] = META_BUS_COURSE;
				$insert_data['booking_source_origin'] = $bitla_origin;
				$insert_data['domain_list_fk'] = 1;
				$insert_data['commission_currency'] = 'INR';
				$insert_data['created_by_id'] = 31;
				$insert_data['operator_name'] = $post_params['operator'];
				$this->custom_db->insert_record('b2b_bus_commission_details', $insert_data);*/
			}else{
				for($i=0;$i<count($post_params['origin']);$i++){
					$update_data = array();
					$update_data['operator_name'] = $post_params['operator_name'][$i];
					$update_data['value'] = $post_params['value'][$i];
					$update_condition = array('origin' => $post_params['origin'][$i],'is_master'=>'0','operator_name'=>$post_params['operator_name'][$i]);
					$this->custom_db->update_record('b2b_bus_commission_details', $update_data, $update_condition);
				}
			}
			set_update_message();
		}
		redirect(base_url().'index.php/management/group_commission');
	}
	public function add_master_operator_wise_commission(){
		$post_params = $this->input->post();
		if(valid_array($post_params)){
			if(isset($post_params['add'])){
				foreach ($post_params['booking_source'] as $b_key => $b_value) {
					$bitla_bus_com = $this->custom_db->single_table_records('b2b_bus_commission_details','*',array('booking_source_origin' => $b_value, 'is_master' => '1'));
					if($bitla_bus_com['status'] == SUCCESS_STATUS){
						$bitla_origin = $b_value;
						$insert_data = array();
						if(empty($post_params['commission_value'][$b_key])){
							$com_val = 0;
						}else{
							$com_val = $post_params['commission_value'][$b_key];
						}
						if(empty($post_params['tds'][$b_key])){
							$com_tds = 0;
						}else{
							$com_tds = $post_params['tds'][$b_key];
						}
						$insert_data['value'] = $com_val;
						$insert_data['tds'] = $com_tds;
						$update_condition = array('booking_source_origin' => $bitla_origin, 'is_master' => '1');
						$this->custom_db->update_record('b2b_bus_commission_details', $insert_data, $update_condition);
					}else{
						$bitla_origin = $b_value;
						$insert_data = array();
						if(empty($post_params['commission_value'][$b_key])){
							$com_val = 0;
						}else{
							$com_val = $post_params['commission_value'][$b_key];
						}
						if(empty($post_params['tds'][$b_key])){
							$com_tds = 0;
						}else{
							$com_tds = $post_params['tds'][$b_key];
						}
						$insert_data['value'] = $com_val;
						$insert_data['type'] = 'specific';
						$insert_data['value_type'] = 'percentage';
						$insert_data['is_master'] = "1";
						$insert_data['course_id'] = META_BUS_COURSE;
						$insert_data['booking_source_origin'] = $bitla_origin;
						$insert_data['domain_list_fk'] = 1;
						$insert_data['commission_currency'] = 'INR';
						$insert_data['created_by_id'] = 31;
						$insert_data['tds'] = $com_tds;
						$this->custom_db->insert_record('b2b_bus_commission_details', $insert_data);
					}	
				}
				
			}else{
				for($i=0;$i<count($post_params['origin']);$i++){
					$update_data = array();
					$update_data['operator_name'] = $post_params['operator_name'][$i];
					$update_data['value'] = $post_params['value'][$i];
					$update_condition = array('origin' => $post_params['origin'][$i]);
					$this->custom_db->update_record('b2b_bus_commission_details', $update_data, $update_condition);
				}
			}
			set_update_message();
		}
		redirect(base_url().'index.php/management/master_commission');
	}
	public function add_agent_operator_wise_commission(){
		$post_params = $this->input->post();
		if(valid_array($post_params)){
			if(isset($post_params['add'])){
				foreach ($post_params['booking_source'] as $b_key => $b_value) {
					$bitla_bus_com = $this->custom_db->single_table_records('b2b_bus_commission_details','*',array('booking_source_origin' => $b_value, 'agent_fk' => $post_params['agent_fk']));
					if($bitla_bus_com['status'] == SUCCESS_STATUS){
						$bitla_origin = $b_value;
						$insert_data = array();
						if(empty($post_params['commission_value'][$b_key])){
							$com_val = 0;
						}else{
							$com_val = $post_params['commission_value'][$b_key];
						}
						if(empty($post_params['tds'][$b_key])){
							$com_tds = 0;
						}else{
							$com_tds = $post_params['tds'][$b_key];
						}
						$insert_data['value'] = $com_val;
						$insert_data['tds'] = $com_tds;
						$update_condition = array('booking_source_origin' => $bitla_origin, 'agent_fk' => $post_params['agent_fk']);
						$this->custom_db->update_record('b2b_bus_commission_details', $insert_data, $update_condition);
					}else{
						$bitla_origin = $b_value;
						$insert_data = array();
						if(empty($post_params['commission_value'][$b_key])){
							$com_val = 0;
						}else{
							$com_val = $post_params['commission_value'][$b_key];
						}
						if(empty($post_params['tds'][$b_key])){
							$com_tds = 0;
						}else{
							$com_tds = $post_params['tds'][$b_key];
						}
						$insert_data['value'] = $com_val;
						$insert_data['type'] = 'specific';
						$insert_data['value_type'] = 'percentage';
						$insert_data['agent_fk'] = $post_params['agent_fk'];
						$insert_data['course_id'] = META_BUS_COURSE;
						$insert_data['booking_source_origin'] = $bitla_origin;
						$insert_data['domain_list_fk'] = 1;
						$insert_data['commission_currency'] = 'INR';
						$insert_data['created_by_id'] = 31;
						$insert_data['tds'] = $com_tds;
						$this->custom_db->insert_record('b2b_bus_commission_details', $insert_data);
					}	
				}
				/*$bitla_origin = $this->custom_db->single_table_records('booking_source','origin',array('source_id' => BITLA_BUS_BOOKING_SOURCE));
				$bitla_origin = $bitla_origin['data'][0]['origin'];
				$insert_data = array();
				$insert_data['value'] = $post_params['commission_value'];
				$insert_data['type'] = 'specific';
				$insert_data['value_type'] = 'percentage';
				$insert_data['agent_fk'] = $post_params['agent_fk'];
				$insert_data['course_id'] = META_BUS_COURSE;
				$insert_data['booking_source_origin'] = $bitla_origin;
				$insert_data['domain_list_fk'] = 1;
				$insert_data['commission_currency'] = 'INR';
				$insert_data['created_by_id'] = 31;
				$insert_data['operator_name'] = $post_params['operator'];
				$this->custom_db->insert_record('b2b_bus_commission_details', $insert_data);*/
			}else{
				for($i=0;$i<count($post_params['origin']);$i++){
					$update_data = array();
					$update_data['operator_name'] = $post_params['operator_name'][$i];
					$update_data['value'] = $post_params['value'][$i];
					$update_condition = array('origin' => $post_params['origin'][$i]);
					$this->custom_db->update_record('b2b_bus_commission_details', $update_data, $update_condition);
				}
			}
			set_update_message();
		}
		redirect(base_url().'index.php/management/agent_commissions');
	}
	
	function get_low_balance(){
		
		$data["suppliers"] = $this->domain_management_model->get_all_suplliers(0, 1);


		$data["balance_details"][BITLA_BUS_BOOKING_SOURCE]["balance"] = $bitla["result"]["balance_amount"];
		$data["balance_details"][BITLA_BUS_BOOKING_SOURCE]["credit_limit"] = $bitla["result"]["credit_limit"];
		$data["balance_details"][BITLA_BUS_BOOKING_SOURCE]["minimum_balance"] = 0;
		$data["balance_details"][BITLA_BUS_BOOKING_SOURCE]["due_amount"] = 0;
        # Please enable when its required #BALU
		//$ets = $this->api_balance_manager->getEtsBalance();

		$data["balance_details"][ETS_BUS_BOOKING_SOURCE]["balance"] = $ets["balanceAmount"];
		$data["balance_details"][ETS_BUS_BOOKING_SOURCE]["credit_limit"] = 0;
		$data["balance_details"][ETS_BUS_BOOKING_SOURCE]["minimum_balance"] = $ets["lowBalanceAmount"];
		$data["balance_details"][ETS_BUS_BOOKING_SOURCE]["due_amount"] = 0;
        # Please enable when its required #BALU
		//$tbo = $this->api_balance_manager->getTboBalance();
		//debug($tbo); exit; 
		$data["balance_details"][PROVAB_FLIGHT_BOOKING_SOURCE]["balance"] = $tbo["CashBalance"];
		$data["balance_details"][PROVAB_FLIGHT_BOOKING_SOURCE]["credit_limit"] = $tbo["CreditBalance"];
		$data["balance_details"][PROVAB_FLIGHT_BOOKING_SOURCE]["minimum_balance"] = 0;
		$data["balance_details"][PROVAB_FLIGHT_BOOKING_SOURCE]["due_amount"] = 0;
		
		foreach($data["suppliers"] AS $key => $supp){
			if($supp["travel_id"] > 0){




				$direct_api = $this->api_balance_manager->getBitlaDOBalance($supp["travel_id"]);
				$data["balance_details"][$supp["source_id"]]["balance"] = 0; //$direct_api["result"]["balance_amount"];
				$data["balance_details"][$supp["source_id"]]["credit_limit"] = 0; //$direct_api$bitla["result"]["credit_limit"]
				$data["balance_details"][$supp["source_id"]]["minimum_balance"] = 0;
				$data["balance_details"][$supp["source_id"]]["due_amount"] = 0;
			}
			//if($_SERVER['HTTP_X_FORWARDED_FOR']=="157.51.125.165")
		      //  {

		        	if($supp['balance']!=0) {
				  if($supp['balance']<=$supp['minimum_balance'])
				  {

                    
                   $this->application_logger->check_api_minimum_balance($this->entity_name, $this->entity_user_id, array('Balance' => $supp['balance'], 'minimum_balance' =>  $supp['minimum_balance']), $supp);
				  }
				 }

		     	//}
		}
		 
		//debug($data);
		$low_bal_text='';
		$count=1;
		foreach($data['suppliers'] as $d_key =>$d_val){
			if($d_val['minimum_balance']>=$d_val['balance']){
				//echo "GSdfg";
				$low_bal_text.='<tr><td>'.$count++.'</td><td>'.$d_val['name'].'</td><td>'.$d_val['balance'].'</td><td style="color:red";>Low Balance</td></tr>';
			}
		}
		//echo $low_bal_text;exit;
		if($low_bal_text!=''){
			echo  $low_bal_text;
		}else{
			echo "No Data Found.";
		}exit;
	}
}
