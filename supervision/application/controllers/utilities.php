<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
/**
 *
 * @package Provab - Provab Application
 * @subpackage Travel Portal
 * @author Balu A<balu.provab@gmail.com>
 * @version V2
 */
class Utilities extends CI_Controller {
	public function __construct() {
		parent::__construct ();
		$this->load->library('Api_Interface');
		$this->load->model('module_model');
		$this->load->model('domain_management_model');
		$this->load->library('utility/notification','notification');
		$this->load->model ( 'transaction_model' );
	}
	/**
	 */
	function deal_sheets() {
		$bus_deals_result = json_decode($this->api_interface->rest_service('bus_deal_sheet'),true);
		$flight_deals_result = json_decode($this->api_interface->rest_service('airline_deal_sheet'),true);
		
		$page_data['bus_deals'] = $bus_deals_result ['data'];
		$page_data['flight_deals'] = $flight_deals_result ['data'];
		$this->template->view ( 'utilities/deal_sheets', $page_data );
	}
	
	/**
	 * Update Convenience Fees in application
	 */
	function convenience_fees() {
		$page_data ['post_data'] = $this->input->post ();
		$this->load->model ( 'transaction_model' );
		if (valid_array ( $page_data ['post_data'] ) == true) {
			$this->transaction_model->update_convenience_fees ( $page_data ['post_data'] );
			set_update_message ();
			redirect ( base_url () . 'index.php/utilities/convenience_fees' );
		}
		$convenience_fees = $this->transaction_model->get_convenience_fees ();
		$page_data ['convenience_fees'] = $this->format_convenience_fees ( $convenience_fees );
		$this->template->view ( 'utilities/convenience_fees', $page_data );
	}
	
	/**
	 * Update Convenience Fees in application
	 */
	function instant_recharge_convenience_fees() {
		$page_data ['post_data'] = $this->input->post ();
		if (valid_array ( $page_data ['post_data'] ) == true) {
			$this->transaction_model->instant_recharge_convenience_fees($page_data['post_data']);
			set_update_message();
			redirect (base_url().'index.php/utilities/instant_recharge_convenience_fees');
		}
		//icfs - Instant Recharge Convenience Fees
		$icfs = $this->transaction_model->get_instant_recharge_convenience_fees();
		$page_data["icfs"] = $icfs;
		$this->template->view('utilities/instant_recharge_convenience_fees', $page_data);
	}

	function update_instant_recharge_convenience_fees()
	{
		$post_data = $this->input->post();
		$icf["pace_fees"] = $post_data["pace_fees"];
		$status = $this->custom_db->update_record("instant_recharge_con_fees", $icf, array("id" => $post_data["id"]));
		$ret_data["status"] = 0;
		if($status==1){
			$ret_data["status"] = 1;
			$ret_data["msg"] = "<div class='alert alert-success'>Updated successfully.</div>";
		}
		else{
			$ret_data["status"] = 0;
			$ret_data["msg"] = "<div class='alert alert-warning'>Something Went wrong or you have made no changes</div>";
		}
		echo json_encode($ret_data); exit;
	}
	function add_bank_to_net_banking()
	{
		$post_data = $this->input->post();
		$icf["payment_mode"] = $post_data["payment_mode"];
		$icf["bank_name"] = $post_data["bank_name"];
		$icf["bank_code"] = $post_data["bank_code"];
		$icf["value"] = $post_data["value"];
		$icf["value_type"] = $post_data["value_type"];
		$icf["from_amount"] = $post_data["from_amount"];
		$icf["to_amount"]  = $post_data["to_amount"];
		$icf["pace_fees"] = $post_data["pace_fees"];
		$status = $this->custom_db->insert_record("instant_recharge_con_fees", $icf);
		$ret_data["status"] = 0;
		if($status["status"]==1){
			$ret_data["status"] = 1;
			$ret_data["msg"] = "<div class='alert alert-success'>Added successfully.</div>";
			$icfs = $this->transaction_model->get_instant_recharge_convenience_fees();
			$si_no = count($icfs);
			$new_tr = '<tr id="icf_'.$status["insert_id"].'">
						<td>'.$si_no.'</td>
						<td>'.$icf["payment_mode"].'</td>
						<td>'.$icf["bank_code"].'</td>
						<td>'.$icf["bank_name"].'</td>
						<td>'.$icf["value_type"].'</td>
						<td>'.$icf["value"].'</td>
						<td>'.$icf["from_amount"].'</td>
						<td>'.$icf["to_amount"].'</td>
						<td><input type="text" value="'.$icf["pace_fees"].'" id="pace_fees"></td>
						<td><a href="#" class="btn btn-primary update_fees_btn">Update</a></td>
						</tr>';
			$ret_data["new_tr"] = $new_tr;		
		}
		else{
			$ret_data["status"] = 0;
			$ret_data["msg"] = "<div class='alert alert-warning'>Something Went wrong, please refresh and try again</div>";
		}
		echo json_encode($ret_data); exit;
	}
	function delete_instant_convenience_fees()
	{
		$post_data = $this->input->post();
		$icf_id = $post_data["id"];
		$status = $this->custom_db->delete_record("instant_recharge_con_fees", array("id"=>$icf_id));

		$ret_data["status"] = 0;
		if($status==1){
			$ret_data["status"] = 1;
			$ret_data["msg"] = "<div class='alert alert-success'>Deleted successfully.</div>";
		}
		else{
			$ret_data["status"] = 0;
			$ret_data["msg"] = "<div class='alert alert-warning'>Something Went wrong, please refresh and try again.</div>";
		}
		echo json_encode($ret_data); exit;
	}

	/**
	 * Format Convenience Fees As Per View
	 */
	private function format_convenience_fees($convenience_fees) {
		$data = array ();
		foreach ( $convenience_fees as $k => $v ) {
			$data [$k] ['origin'] = $v ['origin'];
			$data [$k] ['module'] = strtoupper ( $v ['module'] );
			$fees = '';
			if ($v ['value_type'] == 'plus') {
				$fees = '+' . floatval ( $v ['value'] );
			} else {
				$fees = floatval ( $v ['value'] ) . '%';
			}
			$data [$k] ['fees'] = $fees;
			$data [$k] ['value'] = $v ['value'];
			$data [$k] ['value_type'] = $v ['value_type'];
			$data [$k] ['per_pax'] = $v ['per_pax'];
		}
		return $data;
	}
	
	/**
	 * Manage booking source in the application
	 */
	function manage_direct_buses() {
		$operator_state = $this->input->post('operator_state');
		$manage_data=array();
		$manage_cond=array();
		foreach ($operator_state as $osk => $osv) {
			$manage_data["is_bitla_direct"]=$osv;
			$manage_cond["origin"]=$osk;
			$this->custom_db->update_record('booking_source', $manage_data, $manage_cond);
		}
		redirect (base_url().'index.php/utilities/manage_source');
	}
	function manage_source() {
		$page_data ['list_data'] = $this->module_model->get_course_list ();
		$page_data ['manage_buses'] = $this->module_model->get_manage_buses();
		$page_data ['bitla_direct_buses'] = $this->module_model->get_bitla_direct_buses();
		$page_data ['manage_airlines'] = $this->module_model->get_manage_airlines();
		$this->template->view ( 'utilities/manage_source', $page_data );
	}
	function get_ets_operators()
	{
		$page_data = array();
		$page_data['ets_opts'] = $this->custom_db->single_table_records("tbl_ets_operator_id")["data"];
		$this->template->view('utilities/ets_operator_ids', $page_data);
	}
	function save_ets_operator($id)
	{
		$res = array();
		$res["status"] = 0;
		$res["msg"] = "No Change Or Something went wrong!";
		$data = $this->input->post();
		if($id==0){
			$status = $this->custom_db->insert_record("tbl_ets_operator_id", $data);
			if($status)
			{
				$res["status"] = 1;
				$res["msg"] = "Added Successfully";
				$res["data"] = $data;
			}
		}
		else{
			$status = $this->custom_db->update_record("tbl_ets_operator_id", $data, array("id"=>$id));
			if($status)
			{
				$res["status"] = 1;
				$res["msg"] = "Updated Successfully";
				$res["data"] = $data;
			}
		}
		echo json_encode($res); exit;
	}
	function manage_airlines(){
		$data = $this->input->post();
		
		if(isset($data['air_code_gds'])){
			$air_lines_gds['airline_attr'] = json_encode($data['air_code_gds']);
			$air_lines_gds['updated_when'] = date('Y-m-d h:i:s');
			$cond['source_id'] = TRAVELPORT_GDS_BOOKING_SOURCE;
			$this->custom_db->update_record ('manage_airlines',$air_lines_gds,$cond);
		}else{
			$air_lines_gds['airline_attr'] = '';
			$air_lines_gds['updated_when'] = date('Y-m-d h:i:s');
			$cond['source_id'] = TRAVELPORT_GDS_BOOKING_SOURCE;
			$this->custom_db->update_record ('manage_airlines',$air_lines_gds,$cond);
		}

		if(isset($data['air_code_tbo'])){
			$air_lines_tbo['airline_attr'] = json_encode($data['air_code_tbo']);
			$air_lines_tbo['updated_when'] = date('Y-m-d h:i:s');
			$cond['source_id'] = PROVAB_FLIGHT_BOOKING_SOURCE;
			$this->custom_db->update_record ('manage_airlines',$air_lines_tbo,$cond);
		}else{
			$air_lines_tbo['airline_attr'] = '';
			$air_lines_tbo['updated_when'] = date('Y-m-d h:i:s');
			$cond['source_id'] = PROVAB_FLIGHT_BOOKING_SOURCE;
			$this->custom_db->update_record ('manage_airlines',$air_lines_tbo,$cond);
		}

		redirect (base_url().'index.php/utilities/manage_source');
	}
	function manage_buses(){
		$data = $this->input->post();
		//Update the entire table 'is_active' field values to 0
		$manage_data=array();
		$manage_cond=array();
		$manage_data["is_active"]=0;
		$manage_cond["is_active"]=1;
		$this->custom_db->update_record ('manage_buses', $manage_data, $manage_cond);
		//Update only selected rows field 'is_active' value 
		foreach($data["operator_state"] AS $osk=>$osv){
			$manage_data=array();
			$manage_cond=array();
			$manage_data["is_active"]=$osv;
			$manage_cond["origin"]=$osk;
			$this->custom_db->update_record('manage_buses', $manage_data, $manage_cond);
		}
		redirect (base_url().'index.php/utilities/manage_source');
	}

	/**
	 * Manage sms templates & updates
	 */
	function sms_templates($id=0) {
		$post_data = $this->input->post();
		if(valid_array($post_data) && isset($post_data["submit_sms_template"]))
		{
			$update_data["sms_name"] = $post_data["sms_name"];
			$update_data["template"] = $post_data["template"];
			$update_data["category"] = $post_data["category"];
			$cond["template_id"] = $id;
			$this->custom_db->update_record("tbl_sms_templates", $update_data, $cond);
			set_update_message();
			redirect(base_url("utilities/sms_templates"));
		}

		if($id)
			$cond = array("status"=>ACTIVE, "template_id"=>$id);
		else
			$cond = array("status"=>ACTIVE);

		$data = $this->custom_db->single_table_records("tbl_sms_templates", "*", $cond);
		$page_data["sms_data"] = $data["data"];
		$page_data["id"] = $id;
		//debug($page_data); exit;
		$this->template->view ( 'utilities/sms_templates', $page_data);
	}
	function cc_sms($sms_id)
	{
		$data = $this->user_model->get_sms_users_list();

		$cond = array("status"=>ACTIVE, "sms_id"=>$sms_id);
		$sms_data = $this->custom_db->single_table_records("tbl_sms_templates", "*", $cond)["data"];
		// debug($data);exit;
		$page_data["cc_users"] = $data;
		$page_data["sms_data"] = $sms_data;

		$this->template->view ('utilities/sms_cc_users', $page_data);
	}
	function add_sms_cc_previleges($sms_id)
	{
		$post_data = $this->input->post();
		if(valid_array($post_data) && isset($post_data["sms_cc_user_submit"]))
		{
			$data_cond = array("sms_id" => $sms_id);
			$this->custom_db->delete_record("sms_user_map", $data_cond);

			$user_ids = $post_data["do_send"];
			if(!empty($user_ids))
			{
				foreach($user_ids AS $user_id)
				{
					$data_cond = array("user_id" => $user_id, "sms_id" => $sms_id);
					$this->custom_db->delete_record("sms_user_map", $data_cond);
					$this->custom_db->insert_record("sms_user_map", $data_cond);
				}
			}
		}
		set_update_message();
		redirect(base_url("utilities/cc_sms/".$sms_id));
	}
	function chek_sms_user_map_exists($user_id, $sms_id)
	{
		$cond = array("user_id" => $user_id, "sms_id" => $sms_id);
		$sms_data = $this->custom_db->single_table_records("sms_user_map", "*", $cond)["data"];
		return count($sms_data);
	}
	/**
	 * Manage sms status in sms_checkpoint table
	 */
	function sms_checkpoint() {
		$sms_checkpoint_data = $this->module_model->get_sms_checkpoint ();
		$data ['sms_data'] = $sms_checkpoint_data;
		$this->template->view ( 'utilities/sms_checkpoint', $data );
	}
	/**
	 * Activate sms_checkpoint
	 */
	function activate_sms_checkpoint($condition) {
		$status = ACTIVE;
		$this->module_model->update_sms_checkpoint_status ( $status, $condition );
		redirect ( base_url () . 'index.php/utilities/sms_checkpoint' );
	}
	
	/**
	 * Deactiavte sms_checkpoint
	 */
	function deactivate_sms_checkpoint($condition) {
		$status = INACTIVE;
		$info = $this->module_model->update_sms_checkpoint_status ( $status, $condition );
		redirect ( base_url () . 'index.php/utilities/sms_checkpoint' );
	}
	/**
	 * Module Activation
	 */
	function module() {
		$domain_list = $this->module_model->get_module_list ();
		$data ['domain_list'] = $domain_list;
		$this->template->view ( 'utilities/module_list', $data );
	}
	/**
	 * Activate sms_checkpoint
	 */
	function activate_module($condition) {
		$status = ACTIVE;
		$this->module_model->update_module_status ( $status, $condition );
		redirect ( base_url () . 'index.php/utilities/module' );
	}
	
	/**
	 * Deactiavte sms_checkpoint
	 */
	function deactivate_module($condition) {
		$status = INACTIVE;
		$info = $this->module_model->update_module_status ( $status, $condition );
		redirect ( base_url () . 'index.php/utilities/module' );
	}
	/**
	 * Activate social_link
	 */
	function activate_social_link($condition) {
		$status = ACTIVE;
		$this->module_model->update_social_link_status ( $status, $condition );
		redirect ( base_url () . 'index.php/utilities/social_network' );
	}
	
	/**
	 * Deactiavte social_link
	 */
	function deactivate_social_link($condition) {
		$status = INACTIVE;
		$info = $this->module_model->update_social_link_status ( $status, $condition );
		redirect ( base_url () . 'index.php/utilities/social_network' );
	}
	/*
	 * SOcial Network Url Management
	 */
	function social_network() {
		$temp = $this->custom_db->single_table_records ( 'social_links' );
		$data ['social_links'] = $temp ['data'];
		$this->template->view ( 'utilities/social_network', $data );
	}
	function social_network_status_toggle($id = 0, $status = ACTIVE) {
		if (intval ( $id ) > 0) {
			$data ['status'] = $status;
			$this->custom_db->update_record ( 'social_login', $data, array (
					'origin' => $id 
			) );
		}
	}
	function edit_social_login1($value, $id) {
		//echo $value;exit;
		$info = $this->module_model->update_social_config ( $value, $id );
		//redirect ( base_url () . 'index.php/utilities/social_network' );
	}
	/**
	 * Update_Social URL
	 */
	function edit_social_url() {
		$post_data = $this->input->post ();
		$id = $post_data['origin'];
		$url = $post_data ['social_url'];
		$info = $this->module_model->update_social_url ( $url, $id );
		redirect ( base_url () . 'index.php/utilities/social_network' );
	}
	
	/**
	 * Activate social_login
	 */
	function activate_social_login($condition) {
		$status = ACTIVE;
		$this->module_model->update_social_login_status ( $status, $condition );
		redirect ( base_url () . 'index.php/utilities/social_login' );
	}
	
	/**
	 * Deactiavte social_login
	 */
	function deactivate_social_login($condition) {
		$status = INACTIVE;
		$info = $this->module_model->update_social_login_status ( $status, $condition );
		redirect ( base_url () . 'index.php/utilities/social_login' );
	}
	/*
	 * SOcial Network Url Management
	 */
	function social_login() {
		$temp = $this->custom_db->single_table_records ( 'social_login' );
		$data ['social_login'] = $temp ['data'];
		$this->template->view ( 'utilities/social_login', $data );
	}
	/**
	 * Update social_login
	 */
	function edit_social_login($id) {
		$post_data = $this->input->post ();
		$url = $post_data ['social_login'];
		$info = $this->module_model->update_social_login_name ( $url, $id );
		redirect ( base_url () . 'index.php/utilities/social_login' );
	}
	function toggle_asm_status($bs_id, $mc_id, $status = false) {
		$list_data = $this->module_model->get_course_list ( array (
				array (
						'BS.origin',
						'=',
						$bs_id 
				),
				array (
						'MCL.origin',
						'=',
						$mc_id 
				) 
		) );
		if (valid_array ( $list_data ) == true) {
			$api_code = $list_data [0] ['booking_source_id'];
			$api_name = $list_data [0] ['booking_source'];
			$module_name = $list_data [0] ['name'];
			if ($status == 'false') {
				$status = 'inactive';
				$logger_msg = $this->entity_name . ' Deactivated ' . $module_name . ' (' . $api_code . '-' . $api_name . ') API';
			} else {
				$status = 'active';
				$logger_msg = $this->entity_name . ' Activated ' . $module_name . ' (' . $api_code . '-' . $api_name . ') API';
			}
			$this->custom_db->update_record ( 'activity_source_map', array (
					'status' => $status 
			), array (
					'booking_source_fk' => $bs_id,
					'meta_course_list_fk' => $mc_id,
					'domain_origin' => get_domain_auth_id () 
			) );
			$this->application_logger->api_status ( $logger_msg );
		}
	}
	
	/**
	 * Currency Converter Settings!!!
	 * 
	 * @param float $value        	
	 * @param int $id        	
	 */
	function currency_converter($value = 0, $id = 0) {
		if (intval ( $id ) > 0 && intval ( $value ) > - 1) {
			$data ['value'] = $value;
			$this->custom_db->update_record ( 'currency_converter', $data, array (
					'id' => $id 
			) );
		} else {
			$currency_data = $this->custom_db->single_table_records ( 'currency_converter' );
			$data ['converter'] = $currency_data ['data'];
			$this->template->view ( 'utilities/currency_converter', $data );
		}
	}
	
	/**
	 * Currency Converter Status Update!!!
	 * 
	 * @param float $value        	
	 * @param int $id        	
	 */
	function currency_status_toggle($id = 0, $status = ACTIVE) {
		if (intval ( $id ) > 0) {
			$data ['status'] = $status;
			$this->custom_db->update_record ( 'currency_converter', $data, array (
					'id' => $id 
			) );
		}
	}
	
	/**
	 * Update Currency Converter Values Automatically Using Live Rates
	 * Keeping COURSE_LIST_DEFAULT_CURRENCY_VALUE AS Base Currency
	 */
	function auto_currency_converter() 
	{
		$data_set = $this->custom_db->single_table_records ( 'currency_converter' );
		
		if ($data_set ['status'] == true)
		 {
			
			$to = urlencode(COURSE_LIST_DEFAULT_CURRENCY_VALUE);
			$data ['date_time'] = date ( 'Y-m-d H:i:s' );
			$encode_amount = 1;

			foreach ( $data_set ['data'] as $k => $v ) 
			{
				
                $from = urlencode($v['country']);
                $encode_amount = ($from != $to) ? $encode_amount:1;
                $encode_amount = urlencode($encode_amount);
				
             
				$get = file_get_contents("http://prod.services.travelomatix.com/webservices/index.php/rest/currecny_value_details?amount=$encode_amount&from=$from&to=$to");
				$get_currency = json_decode($get,true);	
                                
				$converted_currency = (isset($get_currency['currency_value'])? $get_currency['currency_value']:1);	
				 				
				$data ['value'] = $converted_currency;
                                
				$this->custom_db->update_record ( 'currency_converter', $data, array ('id' => $v ['id'] ) );
				
			}
		}
		redirect ( 'utilities/currency_converter' );
	}
	function auto_currency_converter_old() {
		$data_set = $this->custom_db->single_table_records ( 'currency_converter' );
		if ($data_set ['status'] == true) {
			$from = COURSE_LIST_DEFAULT_CURRENCY_VALUE;
			$data ['date_time'] = date ( 'Y-m-d H:i:s' );
			foreach ( $data_set ['data'] as $k => $v ) { 
				$url = 'http://download.finance.yahoo.com/d/quotes.csv?s=' . $v ['country'] . $from . '=X&f=nl1';
				$handle = fopen ( $url, 'r' );
				if ($handle) {
					$currency_data = fgetcsv ( $handle );
					fclose ( $handle );
				}
				if ($currency_data != '') {
					if (isset ( $currency_data [0] ) == true and empty ( $currency_data [0] ) == false and isset ( $currency_data [1] ) == true and empty ( $currency_data [1] ) == false) {
						$data ['value'] = $currency_data [1];
						$this->custom_db->update_record ( 'currency_converter', $data, array (
								'id' => $v ['id'] 
						) );
					}
				}
			}
		}
		redirect ( 'utilities/currency_converter' );
	}
	
	/**
	 * Load All Events Of Trip Calendar
	 */
	function trip_calendar() {
		$data["page_data"] = array();
		$this->template->view('utilities/trip_calendar', $data);
	}
	function app_settings() {
		$data["page_data"] = array();
		$this->template->view('utilities/app_settings', $data);
	}
	
	/**
	 * Show time line to user previous one month - Load Last one month by default
	 */
	function timeline() {
		$this->template->view ( 'utilities/timeline' );
	}
	
	/**
	 * Get All The Events Between Two Dates
	 */
	function timeline_rack() {
		$response ['status'] = FAILURE_STATUS;
		$response ['data'] = array ();
		$response ['msg'] = '';
		$params = $this->input->get ();
		$oe_start = intval ( $params ['oe_start'] );
		$event_limit = intval ( $params ['oe_limit'] );
		if ($oe_start > - 1 and $event_limit > - 1) {
			// Older Events
			$oe_list = $this->application_logger->get_events ( $oe_start, $event_limit );
			if (valid_array ( $oe_list ) == true) {
				$response ['oe_list'] = get_compressed_output ( $this->template->isolated_view ( 'utilities/core_timeline', array (
						'list' => $oe_list 
				) ) );
				$response ['status'] = SUCCESS_STATUS;
			}
		}
		header ( 'Content-type:application/json' );
		echo json_encode ( $response );
		exit ();
	}
	
	/**
	 * Get All The Events Between Two Dates
	 */
	function latest_timeline_events() {
		session_write_close (); // This is needed as it helps remove session locks
		$response ['status'] = FAILURE_STATUS;
		$response ['data'] = array ();
		$response ['msg'] = '';
		$waiting_for_new_event = true;
		$params = $this->input->get ();
		$last_event_id = intval ( $params ['last_event_id'] );
		if ($last_event_id > - 1) {
			$cond = array (
					array (
							'TL.origin',
							'>',
							$last_event_id 
					) 
			);
			// Older Events
			while ( $response ['status'] == false ) {
				$os_list = $this->application_logger->get_events ( 0, 10000000000, $cond );
				if (valid_array ( $os_list ) == true) {
					$response ['oa_list'] = get_compressed_output ( $this->template->isolated_view ( 'utilities/core_timeline', array (
							'list' => $os_list 
					) ) );
					$response ['status'] = SUCCESS_STATUS;
				} else {
					sleep ( 3 );
				}
			}
		}
		header ( 'Content-type:application/json' );
		echo json_encode ( $response );
		exit ();
	}
	/**
	 * Active Notification Count
	 */
	function active_notifications_count()
	{
		$get_data = $this->input->get();
		$response ['status'] = SUCCESS_STATUS;
		$response ['data'] = array ();
		$response ['msg'] = '';
		//DeActive the Notification
		if(isset($get_data['deactive_notification']) == true && $get_data['deactive_notification'] == 1){
			$this->application_logger->disable_active_event_notification();
		}
		$condition = array();
		$active_notifications_count = $this->application_logger->active_notifications_count ($condition);
		$response['data']['active_notifications_count'] = intval($active_notifications_count);
		header ( 'Content-type:application/json' );
		echo json_encode ( $response );
		exit ();
	}
	/**
	 * Balu A
	 * Notification Alerts
	 */
	function events_notification()
	{
		$response ['status'] = FAILURE_STATUS;
		$response ['data'] = array ();
		$response ['msg'] = '';
		$oe_start = 0;
		$event_limit = 10;
		$notification_list = $this->application_logger->get_events_notification ($oe_start, $event_limit);
		if (valid_array ( $notification_list ) == true) {
			$page_data = array();
			$page_data['list'] = $notification_list;
			$response['data']['notification_list'] = get_compressed_output ( $this->template->isolated_view ( 'utilities/events_notification',$page_data));
			$response['status'] = SUCCESS_STATUS;
			}
		header ( 'Content-type:application/json' );
		echo json_encode ( $response );
		exit ();
	}
	/**
	 * All Notification List
	 */
	function notification_list($offset=0)
	{
		$page_data = array();
		$condition = array();
		$total_records = $this->application_logger->get_events_notification($offset, RECORDS_RANGE_3, $condition,true);
		$page_data['list'] = $this->application_logger->get_events_notification($offset, RECORDS_RANGE_3, $condition, false);
		//--------PAGINATION-------------//
		$this->load->library('pagination');
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['base_url'] = base_url().'index.php/utilities/notification_list/';
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$config['total_rows'] = $total_records->total;
		$config['per_page'] = RECORDS_RANGE_3;
		$this->pagination->initialize($config);
		$page_data['total_rows'] = $total_records->total;
		$this->template->view('utilities/notification_list', $page_data);
	}
	/**
	 * Balu A
	 * Manage Promo Codes
	 */
	function manage_promo_code($offset = 0) 
	{
		$post_data = $this->input->post();
		// debug($post_data);exit;
		$get_data = $this->input->get();
		$page_data = array();
		$page_data['from_data']['origin'] = 0;
		$condition = array();
		$page_data ['promo_code_page_obj'] = new Provab_Page_Loader ('manage_promo_code');
		// debug($post_data);exit;
		if(isset($get_data['eid']) == true && intval($get_data['eid']) > 0 && valid_array($post_data ) == false) {
			
			$edit_data = $this->custom_db->single_table_records('promo_code_list', '*', array('origin' => intval($get_data['eid'])));
			
			if($edit_data['status'] == true) {
				if(strtotime($edit_data['data'][0]['expiry_date']) <= 0) {
					$edit_data['data'][0]['expiry_date'] = '';//If its Unlimited, setting the Expiry Date to empty
					
				}
				$edit_data['data'][0]['promo_code_image1'] = $edit_data['data'][0]['promo_code_image'];
				
				$page_data['from_data'] = $edit_data['data'][0];
			} else {
					redirect('security/log_event?event=InvalidID');
			}
		} else if (valid_array($post_data ) == true) {//ADD
			// debug($post_data);exit;
			$page_data['promo_code_page_obj']->set_auto_validator ();
			if ($this->form_validation->run ()) {
				
				$origin = intval($post_data['origin']);
				unset($post_data['FID']);
				unset($post_data['origin']);
				$promo_code_list = array();
				$promo_code_list['module'] = trim($post_data['module']);
				$promo_code_list['promo_code'] = trim($post_data['promo_code']);
				$promo_code_list['description'] = trim($post_data['description']);
				$promo_code_list['value_type'] = trim($post_data['value_type']);
				$promo_code_list['value'] = trim($post_data['value']);
				$promo_code_list['display_home_page'] = trim($post_data['display_home_page']);
				$promo_code_list['minimum_amount'] = trim($post_data['minimum_amount']);
				$expiry_date = trim($post_data['expiry_date']);
				if(empty($expiry_date) == false && valid_date_value($expiry_date)) {
					$promo_code_list['expiry_date'] = date('Y-m-d', strtotime($expiry_date));
				} else {
					$promo_code_list['expiry_date'] = date('0000-00-00');
				}
				if (valid_array($_FILES) == true and $_FILES['promo_code_image']['error'] == 0 and $_FILES['promo_code_image']['size'] > 0) {
					if( function_exists( "check_mime_image_type" ) ) {
					    if ( !check_mime_image_type( $_FILES['promo_code_image']['tmp_name'] ) ) {
					    	echo "Please select the image files only (gif|jpg|png|jpeg)"; exit;
					    }
					}
					$config['upload_path'] = $this->template->domain_promo_image_upload_path();
					$temp_file_name = $_FILES['promo_code_image']['name'];
					$config['allowed_types'] = 'gif|jpg|png|jpeg';
					$config['file_name'] = get_domain_key().$temp_file_name;
					$config['max_size'] = '1000000';
					$config['max_width']  = '';
					$config['max_height']  = '';
					$config['remove_spaces']  = false;
					// echo $config['upload_path'];exit;
					//UPLOAD IMAGE
					$this->load->library('upload', $config);
					$this->upload->initialize($config);
					if ( ! $this->upload->do_upload('promo_code_image')) {
						echo $this->upload->display_errors();
					} else {
						$image_data =  $this->upload->data();
					}
	                /*UPDATING IMAGE */
					$promo_code_list['promo_code_image'] = @$image_data['file_name'];
				}
				$promo_code_list['status'] = trim($post_data['status']);
				// debug($promo_code_list);exit;
				set_update_message();
				if($origin > 0) {//Update
					$this->custom_db->update_record('promo_code_list', $promo_code_list, array('origin' => $origin));
				} else if($origin == 0) {//Add
					$promo_code_list['created_by_id'] = $this->entity_user_id;
					$promo_code_list['created_datetime'] = db_current_datetime();
					$this->custom_db->insert_record('promo_code_list', $promo_code_list);
					set_insert_message();
				}
				redirect('utilities/manage_promo_code');
			}
		}
		//***********FILTERS***********//
		if(isset($get_data['promo_code']) == true) {
			$filter_promo_code = trim($get_data['promo_code']); 
			if(empty($filter_promo_code) == false) {
				$condition[] = array('promo_code', '=', '"'.$filter_promo_code.'"');
			}
		}
		if(isset($get_data['module']) == true) {
			$filter_module = trim($get_data['module']); 
			if(empty($filter_module) == false) {
				$condition[] = array('module', '=', '"'.$filter_module.'"');
			}
		}
		//***********FILTERS***********//
		$total_records = $this->module_model->promo_code_list($condition, true);
		$promo_code_list = $this->module_model->promo_code_list($condition, false, $offset, RECORDS_RANGE_2);
		/** TABLE PAGINATION */
		$this->load->library('pagination');
		if (count($_GET) > 0) $config['suffix'] = '?' . http_build_query($_GET, '', "&");
		$config['base_url'] = base_url().'index.php/utilities/manage_promo_code/';
		$config['first_url'] = $config['base_url'].'?'.http_build_query($_GET);
		$config['total_rows'] = $total_records;
		$config['per_page'] = RECORDS_RANGE_2;
		$this->pagination->initialize($config);
		/** TABLE PAGINATION */
		$page_data['promocode_module_options'] = $this->module_model->promocode_module_options(); 
		
		$page_data['promo_code_list'] = $promo_code_list;

		// debug($page_data['from_data']); die;
		$this->template->view ( 'utilities/manage_promo_code', $page_data );
	}
	public function delete_promo_code() {
		$get_data = $this->input->get();
		
		$this->module_model->delete_promo_code ( $get_data['eid'] );
		redirect ( 'utilities/manage_promo_code' );
	}
        
	/**
	 * Update Convenience Fees in application
	 */
	function insurance_fees() {
		$post_data = $this->input->post ();
                 $temp = $this->custom_db->single_table_records ( 'insurance' );
                if(valid_array($post_data)==true) {
                 if($temp['status']==true)
                 {
                     $insurance['amount'] = $post_data['insurance'];
                     $insurance['status'] = $post_data['status'];
                     $insurance['created_time'] =  date('Y-m-d H:i:s');
                     $origin=$temp['data'][0]['origin'];
                     $this->custom_db->update_record('insurance', $insurance, array('origin' => $origin));
                 }
                 else {
                    $insurance['status'] = $post_data['status'];
                    $insurance['amount'] = $post_data['insurance'];
                    $insurance['created_time'] =  date('Y-m-d H:i:s');
                    $this->custom_db->insert_record('insurance', $insurance);
                 }
                } 
                $temp = $this->custom_db->single_table_records ( 'insurance' );
		$page_data ['insurance'] = $temp ['data'];
                
                $this->template->view ( 'utilities/insurance_fees', $page_data );
	}

	function quota_counter_list()
	{
		$page_data["qcls"] = $this->module_model->get_quota_counter_list();
		$this->template->view ('utilities/quota_counter', $page_data );
	}

	function save_quota_counter($id)
	{
		$post_data = $this->input->post();
		$data['given_quota'] = $post_data['given_quota'];
        $data['consumed_quota'] = $post_data['consumed_quota'];
		$data['type'] = $post_data['type'];
        $this->custom_db->update_record('quota_counter', $data, array("id"=>$id));
        set_update_message();
        redirect(base_url("index.php/utilities/quota_counter_list"));
	}

	function not_access(){
		$this->template->view ( 'utilities/404');
	}

	/// Manage City list

	function get_airport_details(){
		$page_data['aliport_code'] = $this->domain_management_model->get_airport_list();
	
		//debug($page_data);die();
        $this->template->view('utilities/manage_airport_list',$page_data);
	}

	function getAirportLists(){
		$airport_list = $this->domain_management_model->get_airport_list();
		//debug($airport_list);die();

		$data = $row = array();
        $this->load->model('member');
        // Fetch member's records
        $table = 'flight_airport_list';
        $order = array('origin' => 'asc');
        $airport = $_GET['airport'];
        $condition = '';
        if(!empty($airport)){
            $condition = array('origin','=',$airport);
        }else{
           	$condition = '';
        }
        
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
            $member['action'] = "<a href='#' class='btn btn-info edit_details' data-id='".$member['origin']."' data-a_code='".$member['airport_code']."' data-a_name='".$member['airport_name']."' data-a_city='".$member['airport_city']."' data-country='".$member['country']."' data-c_code='".$member['CountryCode']."' data-tzone='".$member['timezonename']."'>Edit</a>&nbsp;<a href='#' class='btn btn-warning delete_details' data-id='".$member['origin']."'>Delete</a>";

            $data[] = array($i, $member['airport_code'], $member['airport_name'], $member['airport_city'], $member['country'], $member['CountryCode'], $member['timezonename'],$member['action']);
        }
        
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->member->countAll(),
            "recordsFiltered" => $this->member->countFiltered($_POST),
            "data" => $data,
        );

        echo json_encode($output);
	}

	function add_new_airport(){

		$data = $this->input->post();
		$airport_code = $data['a_code'];

		$temp = $this->custom_db->single_table_records ('flight_airport_list','airport_code',array('airport_code'=>$airport_code));
		
		if($temp['status'] == 0){
			$data_arr = array(
				'airport_code' => $airport_code,
				'airport_name' => $data['a_name'],
				'airport_city' => $data['a_city'],
				'country' => $data['country'],
				'CountryCode' => $data['c_code'],
				'timezonename' => $data['timezone'],
				'top_destination' =>0,
				'image' => ''
				);
			$inser_id = $this->custom_db->insert_record('flight_airport_list', $data_arr);
			redirect(base_url().'index.php/utilities/get_airport_details');
		}else{
			redirect(base_url().'index.php/utilities/get_airport_details');
		}
	}

	function update_new_airport(){
		
		$data = $this->input->post();
		$airport_code = $data['a_code'];
		$o_id = $data['o_id'];

		$data_arr = array(
			'airport_code' => $airport_code,
			'airport_name' => $data['a_name'],
			'airport_city' => $data['a_city'],
			'country' => $data['country'],
			'CountryCode' => $data['c_code'],
			'timezonename' => $data['timezone'],
			'top_destination' =>0,
			'image' => ''
			);

		$cond['origin'] = $o_id;
		$this->custom_db->update_record ('flight_airport_list',$data_arr,$cond);
		redirect(base_url().'index.php/utilities/get_airport_details');
	}

	function delete_airport(){
		$o_id = $_POST['id'];

		$data = $this->custom_db->delete_record ( 'flight_airport_list', array ('origin' => $o_id));
		echo $data;
	}
	function add_new_airline(){

		$data = $this->input->post();
		$airline_code = $data['code'];

		$temp = $this->custom_db->single_table_records('airline_list','code',array('code'=>$airline_code));
		$path =  $_SERVER["DOCUMENT_ROOT"].SYSTEM_RESOURCE_LIBRARY."/images/airline_logo/";
		if($temp['status'] == 0){
			$config['upload_path']    = $path;
            $config['allowed_types']  = 'gif';
            $config['max_size']       = 100;
            $config['max_width']      = 100;
            $config['max_height']     = 100;
            $config['file_name']      = $airline_code.".gif";
            $this->load->library('upload', $config);
            if(!$this->upload->do_upload('logo'))
	        {
	        	$errors = $this->upload->display_errors();
	        	$this->session->set_flashdata("msg", "<div class='alert alert-danger'>".$errors."</div>");
	        	redirect(base_url().'index.php/utilities/get_airline_details');
	        	exit;
	        }	
			$data_arr = array(
				'code' => $airline_code,
				'name' => $data['name'],
				'has_specific_markup' => 0,
				'has_specific_commission' => 0
				);
			$inser_id = $this->custom_db->insert_record('airline_list', $data_arr);
			$this->session->set_flashdata("msg", "<div class='alert alert-success'>Added details successfully</div>");
			redirect(base_url().'index.php/utilities/get_airline_details');
		}else{
			$this->session->set_flashdata("msg", "<div class='alert alert-danger'>Airline code already exists.</div>");
			redirect(base_url().'index.php/utilities/get_airline_details');
		}
	}
	function update_new_airline(){

		$data = $this->input->post();
		$airline_code = $data['code'];
		$airline_origin = $data["o_id"];
		if(isset($_FILES["logo"]["tmp_name"]) && !empty($_FILES["logo"]["tmp_name"])){
			$path =  $_SERVER["DOCUMENT_ROOT"].SYSTEM_RESOURCE_LIBRARY."/images/airline_logo/";
			$config['upload_path']    = $path;
	        $config['allowed_types']  = 'gif';
	        $config['max_size']       = 100;
	        $config['max_width']      = 100;
	        $config['max_height']     = 100;
	        $config['file_name']      = $airline_code.".gif";
	        $this->load->library('upload', $config);
	        if(!$this->upload->do_upload('logo'))
	        {
	        	$errors = $this->upload->display_errors();
	        	$this->session->set_flashdata("msg", "<div class='alert alert-danger'>".$errors."</div>");
	        	redirect(base_url().'index.php/utilities/get_airline_details');
	        	exit;
	        }	
    	}
		$data_arr = array(
			'code' => $airline_code,
			'name' => $data['name'],
			'has_specific_markup' => 0,
			'has_specific_commission' => 0
			);
		$inser_id = $this->custom_db->update_record('airline_list', $data_arr, array("origin"=>$airline_origin));
		$this->session->set_flashdata("msg", "<div class='alert alert-success'>Updated details successfully</div>");
		redirect(base_url().'index.php/utilities/get_airline_details');
	}
	function get_airline_details(){
		$page_data['airline_code'] = $this->domain_management_model->get_airline_list();
	
		//debug($page_data);die();
        $this->template->view('utilities/manage_airlines',$page_data);
	}
	function delete_airline(){
		$post_data = $this->input->post();
		$o_id = $post_data["id"];

		$data = $this->custom_db->delete_record ( 'airline_list', array ('origin' => $o_id));
		echo $data;
	}
	
	function getAirlineLists(){
		$airport_list = $this->domain_management_model->get_airline_list();
		//debug($airport_list);die();

		$data = $row = array();
        $this->load->model('member');
        // Fetch member's records
        $table = 'airline_list';
        $order = array('origin' => 'asc');
        $airline = $_GET['airline'];
        $condition = '';
        if(!empty($airline)){
            $condition = array('origin','=',$airline);
        }else{
           	$condition = '';
        }
        
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
            $member['action'] = "<a href='#' class='btn btn-info edit_details' data-id='".$member['origin']."' data-code='".$member['code']."' data-name='".$member['name']."'>Edit</a>&nbsp;<a href='#' class='btn btn-warning delete_details' data-id='".$member['origin']."'>Delete</a>";

            $data[] = array($i, $member['code'], $member['name'], $member['action']);
        }
        
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->member->countAll(),
            "recordsFiltered" => $this->member->countFiltered($_POST),
            "data" => $data,
        );

        echo json_encode($output);
	}

	//bus details
	function get_busstation_details(){
		$page_data['aliport_code'] = $this->domain_management_model->get_busstation_list();
	//	debug($page_data);die();

        $this->template->view('utilities/manage_bus_station',$page_data);
	}

	function get_bus_stations(){
		/*$station_list = $this->domain_management_model->get_busstation_list();
		debug($station_list);die();*/

		$data = $row = array();
        $this->load->model('member');
        // Fetch member's records
        $table = 'bus_stations_new1';
        $order = array('origin' => 'asc');
        $city_id = $_GET['city_id'];
        $condition = '';
        if(!empty($city_id)){
            $condition = array('origin','=',$city_id);
        }else{
           	$condition = '';
        }
        
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
            //debug($member); exit;
            $member['action'] = "<a href='#' class='btn btn-info edit_details' data-id='".$member['origin']."' data-c_name='".$member['ets_city_name']."' data-vrl_id='".$member['vrl_id']."' data-bitla_id='".$member['bitla_id']."' data-sr_city_id='".$member['sr_city_id']."' data-ganesh_city_id='".$member['ganesh_city_id']."' data-seabird_city_id='".$member['seabird_city_id']."' data-sugama_city_id='".$member['sugama_city_id']."' data-kukkeshree_city_id='".$member['kukkeshree_city_id']."' data-krl_city_id='".$member['krl_city_id']."'  data-sr_konduskar_id='".$member['sr_konduskar_id']."'  data-srs_city_id='".$member['srs_city_id']."' data-gotour_city_id='".$member['gotour_city_id']."' data-kadri_city_id='".$member['kadri_city_id']."' data-konduskar_city_id='".$member['konduskar_city_id']."' data-barde_city_id='".$member['barde_city_id']."' data-infinity_city_id='".$member['infinity_city_id']."' data-status='".$member['status']."'>Edit</a>&nbsp;<a href='#' class='btn btn-warning delete_details' data-id='".$member['origin']."'>Delete</a>";

            $data[] = array($i, $member['ets_city_name'], $member['vrl_id'], $member['bitla_id'],$member['sr_city_id'],$member['ganesh_city_id'],$member['seabird_city_id'],$member['sugama_city_id'],$member['kukkeshree_city_id'],$member['krl_city_id'],$member['sr_konduskar_id'],$member['srs_city_id'],$member['gotour_city_id'],$member['kadri_city_id'],$member['konduskar_city_id'],$member['barde_city_id'],$member['infinity_city_id'],$member['action']);
        }
        
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->member->countAll(),
            "recordsFiltered" => $this->member->countFiltered($_POST),
            "data" => $data,
        );

        echo json_encode($output);
	}

	function add_new_station(){
		$data = $this->input->post();
		$station_name = $data['s_name'];
		//debug($data);die();
		$temp = $this->custom_db->single_table_records ('bus_stations_new1','ets_city_name',array('ets_city_name'=>$station_name));
		
		if($temp['status'] == 0){
			$data_arr = array(
				'ets_city_name' => $data['s_name'],
				'vrl_id' => $data['vrl_id'],
				'bitla_id' => $data['bitla_id'],
				'sr_city_id' => $data['sr_city_id'],
				'ganesh_city_id' => $data['ganesh_city_id'],
				'seabird_city_id' => $data['seabird_city_id'],
				'sugama_city_id' => $data['sugama_city_id'],
				'kukkeshree_city_id' => $data['kukkeshree_city_id'],
				'krl_city_id' => $data['krl_city_id'],
				'sr_konduskar_id' => $data['sr_konduskar_id'],
				'srs_city_id' => $data['srs_city_id'],
				'gotour_city_id' => $data['gotour_city_id'],
				'kadri_city_id' => $data['kadri_city_id'],
				'konduskar_city_id' => $data['konduskar_city_id'],
				'barde_city_id' => $data['barde_city_id'],
				'infinity_city_id' => $data['infinity_city_id'],
				'status' => $data['status']
				);
			$inser_id = $this->custom_db->insert_record('bus_stations_new1', $data_arr);
			redirect(base_url().'index.php/utilities/get_busstation_details');
		}else{
			redirect(base_url().'index.php/utilities/get_busstation_details');
		}
	}

	function update_new_stations(){
		
		$data = $this->input->post();
		//$airport_code = $data['a_code'];
		$o_id = $data['o_id'];
		
		$data_arr = array(
			'ets_city_name' => $data['s_name'],
			'vrl_id' => $data['vrl_id'],
			'bitla_id' => $data['bitla_id'],
			'sr_city_id' => $data['sr_city_id'],
			'ganesh_city_id' => $data['ganesh_city_id'],
			'seabird_city_id' => $data['seabird_city_id'],
			'sugama_city_id' => $data['sugama_city_id'],
			'kukkeshree_city_id' => $data['kukkeshree_city_id'],
			'krl_city_id' => $data['krl_city_id'],
			'sr_konduskar_id' => $data['sr_konduskar_id'],
			'srs_city_id' => $data['srs_city_id'],
			'gotour_city_id' => $data['gotour_city_id'],
			'kadri_city_id' => $data['kadri_city_id'],
			'konduskar_city_id' => $data['konduskar_city_id'],
			'barde_city_id' => $data['barde_city_id'],
			'infinity_city_id' => $data['infinity_city_id'],
			'status' => $data['status']
			);
		//debug($data_arr); exit;
		$cond['origin'] = $o_id;
		$this->custom_db->update_record ('bus_stations_new1',$data_arr,$cond);
		redirect(base_url().'index.php/utilities/get_busstation_details');
	}

	function delete_station(){
		$o_id = $_POST['id'];

		$data = $this->custom_db->delete_record ( 'bus_stations_new1', array ('origin' => $o_id));
		echo $data;
	}

	// Hotel

	function get_hotel_city_details(){
		$page_data['city_code'] = $this->domain_management_model->get_hotel_city_list();
	
		//debug($page_data);die();
        $this->template->view('utilities/manage_hotel_cities',$page_data);
	}

	function get_hotel_cities(){
		$data = $row = array();
        $this->load->model('member');
        // Fetch member's records
        $table = 'all_api_hotel_cities';
        $order = array('origin' => 'asc');
        $city_id = $_GET['city_id'];
        $condition = '';
        if(!empty($city_id)){
            $condition = array('origin','=',$city_id);
        }else{
           	$condition = '';
        }
        
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
            $member['action'] = "<a href='#' class='btn btn-info edit_details' data-id='".$member['origin']."' data-c_name='".$member['city_name']."' data-c_code='".$member['country_code']."' data-rez_id='".$member['rz_city_id']."' data-country='".$member['country_name']."'>Edit</a>&nbsp;<a href='#' class='btn btn-warning delete_details' data-id='".$member['origin']."'>Delete</a>";

            $data[] = array($i, $member['city_name'], $member['country_code'], $member['rz_city_id'],$member['country_name'],$member['action']);
        }
        
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->member->countAll(),
            "recordsFiltered" => $this->member->countFiltered($_POST),
            "data" => $data,
        );

        echo json_encode($output);
	}

	function add_new_hotel_city(){
		$data = $this->input->post();
		//$station_name = $data['s_name'];

		$temp = $this->custom_db->single_table_records ('all_api_hotel_cities','city_name',array('city_name'=>$data['c_name']));
		
		if($temp['status'] == 0){
			$data_arr = array(
				'city_name' => $data['c_name'],
				'country_code' => $data['c_code'],
				'rz_city_id' => $data['rez_c_id'],
				'country_name' => $data['country'],
				);
			$inser_id = $this->custom_db->insert_record('all_api_hotel_cities', $data_arr);
			redirect(base_url().'index.php/utilities/get_hotel_city_details');
		}else{
			redirect(base_url().'index.php/utilities/get_hotel_city_details');
		}
	}

	function update_new_hotel_city(){
		$data = $this->input->post();
		$o_id = $data['o_id'];

		$data_arr = array(
			'city_name' => $data['c_name'],
			'country_code' => $data['c_code'],
			'rz_city_id' => $data['rez_c_id'],
			'country_name' => $data['country'],
			);

		$cond['origin'] = $o_id;
		$this->custom_db->update_record ('all_api_hotel_cities',$data_arr,$cond);
		redirect(base_url().'index.php/utilities/get_hotel_city_details');
	}

	function delete_hotel_city(){
		$o_id = $_POST['id'];

		$data = $this->custom_db->delete_record ('all_api_hotel_cities', array ('origin' => $o_id));
		echo $data;
	}


	function add_new_quota(){
		$data = $this->input->post();

		$data_arr = array(
			'booking_source_id' => $data['booking_source'],
			'type' => $data['type'],
			'airline_code' => $data['a_code'],
			'given_quota' => $data['quota'],
			'consumed_quota' => 0,
			'is_active'=>1
			);
		$inser_id = $this->custom_db->insert_record('quota_counter', $data_arr);
		redirect(base_url().'index.php/utilities/quota_counter_list');
	}

	///

	function agent_amount_collection1(){
		//die('123');
		$page_data['agency_list'] = $this->domain_management_model->get_agent_list();

        $this->template->view('utilities/agent_amount_collection',$page_data);
	}

	

	function agent_amount_collection($id = ''){

		$page_data ['form_data'] = $this->input->post ();
		//debug($page_data);die();
		if (valid_array ( $page_data ['form_data'] ) == true) {
			//debug($page_data ['form_data']);die();
			$cheque_issued_date = NULL;
			$payment_date = NULL;
			if($page_data['form_data']['cheque_issued_date'] != ''){
				$cheque_issued_date = $page_data['form_data']['cheque_issued_date'];
			}
			if($page_data['form_data']['payment_date'] != ''){
				$payment_date = $page_data['form_data']['payment_date'];
			}
			$upload_files = '';
			if($_FILES["upload_reciept"]['name'] != ''){
				$ext = end((explode(".", $_FILES["upload_reciept"]['name'])));
				$upload_files = md5($_FILES["upload_reciept"]['name']).'.'.$ext;
			}

			$system_transaction_id = 'DEP-'.$this->entity_user_id.time();
			$insert_form_data = array(
					'system_transaction_id' => $system_transaction_id,
					'domain_list_fk' => get_domain_auth_id(),
					'user_oid' => $page_data['form_data']['agency_name'],
					'transaction_type' => $page_data['form_data']['deposite_type'], 
					'amount' => $page_data['form_data']['collected_amount'],
					'currency' => 'INR',
					'currency_conversion_rate' => 1,
					'date_of_transaction' => $page_data['form_data']['collected_date'],
					'bank' => $page_data['form_data']['beneficiary_bank_name'],
					// 'branch' => '',
					// 'deposited_branch' => '',
					'transaction_number' => $system_transaction_id,
					'status' => 'pending',
					'type' => 'b2b',
					'image' => $upload_files,
					'remarks' => $page_data['form_data']['remarks'],
					'created_datetime' => date('Y-m-d h:i:s'),
					'created_by_id' => $this->entity_user_id,
				);
			$temp_name = $_FILES["upload_reciept"]['name'];
			if($temp_name != ''){
				$config['upload_path'] = DEPOSITE_RECIEPT;
			    $config['allowed_types'] = 'gif|jpg|png|pdf';
			    $config['file_name'] = md5($_FILES["upload_reciept"]['name']);
			    if ( !is_dir($config['upload_path']) ) die("THE UPLOAD DIRECTORY DOES NOT EXIST");
			    $this->load->library('upload', $config);
			    if ( ! $this->upload->do_upload('upload_reciept')) {
			        echo 'error';
			    } else {
			        array('upload_data' => $this->upload->data());
			    }
			}
			$this->custom_db->insert_record('master_transaction_details', $insert_form_data);
			$this->session->set_flashdata("msg", "Agent Collection Record Added Successfully....");
			redirect( base_url () .'index.php/utilities/agent_amount_collection');
		}
		$params = $this->input->get();
		$data_list_filt = array();
		if (isset($params['status']) == true and empty($params['status']) == false) {
			$data_list_filt[] = array('AAC.status','=','"'.$params['status'].'"');
		}
		if (isset($params['agent']) == true and empty($params['agent']) == false) {
			$data_list_filt[] = array('AAC.agent_id','=',$params['agent']);
		}
		if (isset($params['exe_id']) == true and empty($params['exe_id']) == false) {
			$data_list_filt[] = array('AAC.created_by','=',$params['exe_id']);
		}
		if (isset($params['created_date_from']) == true and empty($params['created_date_from']) == false) {
			$data_list_filt[] = array('date(MTD.created_datetime)', '>=', $this->db->escape(db_current_datetime(date('Y-m-d',strtotime($params['created_date_from'])))));
		}
		if (isset($params['created_date_to']) == true and empty($params['created_date_to']) == false) {
			$data_list_filt[] = array('date(MTD.created_datetime)', '<=', $this->db->escape(db_current_datetime(date('Y-m-d',strtotime($params['created_date_to'])))));
		}

		//debug($data_list_filt);die();
		$page_data['agency_list'] = $this->domain_management_model->get_agent_list();
		$page_data['search_params'] = $params;
		$page_data ['table_data'] = $this->domain_management_model->master_transaction_request_list1('b2b', $data_list_filt);
		$condition[] = array('U.user_type', ' IN (', Executive, ')');
		$page_data['executive_list'] = $this->user_model->get_domain_user_list($condition, false);
		//debug($page_data);exit;
		$this->template->view ('utilities/agent_amount_collection', $page_data);
	}

	//update from admin



	function update_agent_amount_collection(){
		$id = $_POST['id'];
		$status = $_POST['status'];
		$agent_id = $_POST['agent_id'];
		$amount = $_POST['amount'];
		$app_reference = $_POST['ref'];
		$rfr = $_POST['rfr'];

		
		if($status == 'Approve'){
			$update_data['status'] = 'Approved';
			$update_data['updated_by'] = $this->entity_user_id;
			$update_data['reason'] = "--";
		}else{
			$update_data['status'] = 'Rejected';
			$update_data['updated_by'] = $this->entity_user_id;
			$update_data['reason'] = $rfr;
		}
		
		$cond['origin'] = $id;
		$return_data = $this->custom_db->update_record('agent_amount_collection',$update_data,$cond);

		//Add to agent walet
		if($status == 'Approve' && $return_data == true){
			$credit_towards = 'Executive Collection';
			$comments = 'Collected Amount credited to agent wallet';

			$this->notification->credit_balance($agent_id, $app_reference, $credit_towards, $amount, 0, $comments);

		}

		echo $return_data;
	}
}
