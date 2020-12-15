<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Provab - Provab Application
 * @subpackage Travel Portal
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V2
 */

class Utilities extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model("transaction_model");
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
	 * Get bank list to aplly differet con fees for different bank 
	 */
	function get_bank_list_options()
	{
		$data = $this->custom_db->single_table_records('instant_recharge_con_fees', "*",  array("payment_mode" => "net_banking"));
		$banks = $data["data"];
		$bank_list = "<select id='bank_code'>";
		$bank_list .= "<option value='0'>Select Bank</option>";
		foreach($banks AS $bank)
		{
			$bank_list .= "<option value='".$bank["bank_code"]."'>".$bank["bank_name"]."<option>";
		}
		$bank_list .= "</select>";
		echo $bank_list;
		exit;
	}

	/**
	 * Update Convenience Fees in application
	 */
	function get_instant_recharge_convenience_fees($ajax=0) {
		$form_data = $this->input->post();
		$method = $form_data["method"];
		$amount = $form_data["amount"];
		$bank_code = $form_data["bank_code"];
		$condition = array("from_amount <=" => $amount, "to_amount >=" => $amount);
		if($method=="CC")
			$pm="credit_card";
		else if($method=="DC")
			$pm="debit_card";
		else if($method=="PPI")
			$pm="paytm_wallet";
		else{
			$pm="net_banking";
			$condition["bank_code"] = $bank_code;
		}
		$condition["payment_mode"] = $pm;

		$data = $this->custom_db->single_table_records('instant_recharge_con_fees', "*",  $condition);

		//echo $this->custom_db->db->last_query(); exit;

		if(isset($data["data"][0]))
		{
			$data = $data["data"][0];
			//Var @cf - convenience_fees
			if($data["value_type"] == "percentage"){
	          $sf = ($amount/100)*$data['value'];
	          $pf = ($amount/100)*$data['pace_fees'];
	          $cf = $sf+$pf;
	        }
        	else{
	          $sf = $data['value'];
	          $pf = ($amount/100)*$data['pace_fees'];
	          $cf = $sf+$pf;
	        }

			$total = $amount+$cf;
			$data["sf"] = $sf;
        	$data["pf"] = $pf;
			$data["cf"] = $cf;
			$data["total"] = $total;
		}

		if($ajax)
		{
			echo json_encode($data);
			exit;
		}
		else
		{
			return $data;
			exit;
		}
	}
}