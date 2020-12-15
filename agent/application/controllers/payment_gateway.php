<?php
if (! defined ( 'BASEPATH' ))
exit ( 'No direct script access allowed' );
/**
 *
 * @package Provab
 * @subpackage Transaction
 * @author Balu A <balu.provab@gmail.com>
 * @version V1
 */
class Payment_Gateway extends CI_Controller {
	/**
	 *
	 */
	public function __construct() {
		parent::__construct ();
		// $this->output->enable_profiler(TRUE);
		$this->load->model ( 'module_model' );
		$this->load->library('provab_sms');
		$this->load->library('provab_mailer');
		$this->load->library('utility/notification', '', 'notification');
	}

	/**
	 * Redirection to payment gateway
	 * @param string $book_id		Unique string to identify every booking - app_reference
	 * @param number $book_origin	Unique origin of booking
	 * @param string $selected_pm	This is Payment Mode, depending on which we are choosing 
	 * payment gateways.
	 */
	public function payment($book_id,$book_origin, $selected_pm,$ref_record=0)
	{
		
		$this->load->model('transaction');
		//As we have selected_pm we are ignoring active_payment_gateway from config
		//$PG = $this->config->item('active_payment_gateway');
		//echo $selected_pm;exit;
		$PG = $selected_pm;
		if($PG!="WALLET")
			load_pg_lib ( $PG );
		else
			$this->config->set_item('enable_payment_gateway', false);
		if($ref_record==0){
			$pg_record = $this->transaction->read_payment_record($book_id);
		}else{
			$pg_record = $this->transaction->read_ref_payment_record($book_id,$ref_record);
		}
		//echo $this->db->last_query();
		$temp_booking = $this->custom_db->single_table_records ( 'temp_booking', '', array (
				'book_id' => $book_id 
		) );
		//debug($pg_record);exit;
		$book_origin = $temp_booking ['data'] ['0'] ['id'];

		if (empty($pg_record) == false and valid_array($pg_record) == true) {
			$params = json_decode($pg_record['request_params'], true);
			$pg_initialize_data = array (
				'txnid' => $params['txnid'].'_'.$ref_record,
				'pgi_amount' => ceil($pg_record['amount']),
				'firstname' => $params['firstname'],
				'email'=>$params['email'],
				'phone'=>$params['phone'],
				'pg_txnid'=>$params['txnid'],
				'req_type'=>'T',
				'productinfo'=> $params['productinfo']
			);
		} else {
			echo 'Under Construction :p';
			exit;
		}
		if($ref_record==0){
			$params['productinfo'] = $params['productinfo'];
		}else{
			$params['productinfo'] = 'PACKAGE_BALANCE_AMOUNT';
			$book_origin = $book_origin.'-'.$ref_record;
		}
		//defined in provab_config.php
		$payment_gateway_status = $this->config->item('enable_payment_gateway');
		//debug($payment_gateway_status);exit;
		if ($payment_gateway_status == true) {
			$pg_obj=$this->pg->initialize ( $pg_initialize_data );
			if (!is_object($pg_obj))
				$pg_obj="";
			$page_data['pay_data'] = $this->pg->process_payment($pg_obj);
			//debug($page_data['pay_data']); exit;
			
			//Not to show cache data in browser
			header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache");
			//debug($page_data);exit;
			echo $this->template->isolated_view('payment/'.$PG.'/pay', $page_data);
		} else {
			//directly going to process booking
			$this->redirect_booking($params['productinfo'], $params['txnid'], $book_origin);
		}
	}

	public function instant_recharge($txnid, $amount, $selected_pm)
	{
		$this->load->model('transaction');
		$pg_array = explode("_", $selected_pm);
		$PG = $pg_array[0];
		load_pg_lib ( $PG );

		$pg_record = $this->transaction->read_payment_record($txnid);


		if (empty($pg_record) == false and valid_array($pg_record) == true) {
			$params = json_decode($pg_record['request_params'], true);
			$pg_initialize_data = array (
				'txnid' => $params['txnid'],
				'pgi_amount' => ceil($pg_record['amount']),
				'firstname' => $params['firstname'],
				'email'=>$params['email'],
				'phone'=>$params['phone'],
				'pg_txnid'=>$params['txnid'],
				'req_type'=>'T',
				'productinfo'=> $params['productinfo'],
				'txn_type'=>'INSTANT_RECHARGE'
			);
		} else {
			echo 'Problem with the payment, please contact admin for manual recharge.';
			exit;
		}
		if($PG == "PAYTM"){
			$pg_initialize_data["payment_mode_only"] = "YES";
			$pg_initialize_data["payment_type_id"] = $pg_array[1];
		}
		//defined in provab_config.php
		$payment_gateway_status = $this->config->item('enable_payment_gateway');
		if ($payment_gateway_status == true) {
			$pg_obj=$this->pg->initialize ( $pg_initialize_data );
			if (!is_object($pg_obj))
				$pg_obj="";
			$page_data['pay_data'] = $this->pg->process_payment($pg_obj);
			//debug($page_data['pay_data']); exit;

			//Not to show cache data in browser
			header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache");
			echo $this->template->isolated_view('payment/'.$PG.'/pay', $page_data);
		} else {
			exit("Payment gateways are disableb by admin.");
		}
	}
	/*
	Shashikumar Misal
	*/
	public function refund($booking_id, $selected_pm)
	{
		//$this->provab_mailer->send_mail($email, $refund_response["txn_msg"], $mail_template);
		$this->load->model('transaction');
		$pg_record = $this->transaction->read_payment_record($booking_id);

		$PG = $selected_pm;
		$PG = explode("_", $PG)[0];
		if($PG == 'TECHP' || $PG == 'PAYTM')
			load_pg_lib ($PG);
		
		if (empty($pg_record) == false and valid_array($pg_record) == true) {
			$req_params = json_decode($pg_record['request_params'], true);
			$resp_params = json_decode($pg_record['response_params'], true);
			$pg_initialize_data = array (
				'txnid' => $resp_params['txnid'],
				'pgi_amount' => ceil($pg_record['amount']),
				'firstname' => $req_params['firstname'],
				'email'=>$req_params['email'],
				'phone'=>$req_params['phone'],
				'pg_txnid'=>$resp_params['pg_txn_id'],
				'req_type'=>'R',
				'txn_type'=>'REFUND',
				'refund_amt'=>ceil($pg_record['amount']),
				'productinfo'=> $resp_params['productinfo']
			);
		} else {
			echo 'Problem with the payment, please contact admin for manual recharge.';
			exit;
		}
		//defined in provab_config.php
		$payment_gateway_status = $this->config->item('enable_payment_gateway');
		if ($payment_gateway_status == true) {
			$pg_obj=$this->pg->initialize ( $pg_initialize_data );
			if (!is_object($pg_obj))
				$pg_obj="";

	if($PG == 'PAYTM')
	{
		$page_data['pay_data']['response'] = $this->pg->process_refund();
		$refund_response=$this->pg->read_refund_response($page_data['pay_data']['response']);
	}
	if($PG == 'TECHP')
	{
		$page_data['pay_data'] = $this->pg->process_payment($pg_obj);
		$refund_response = $this->pg->read_response($page_data['pay_data']['response'], 'refund');
	}
	$email = $GLOBALS["CI"]->entity_email;
	$phone = $GLOBALS["CI"]->entity_phone;
	$refund_response['product'] = $resp_params['productinfo'];
	if($refund_response["txn_status"] == "success")
	{
		$status = ACCEPTED;
		$msg="Booking failed due to some error from supplier. Auto refund status - ".$refund_response["txn_msg"];
		$msg_detail=$refund_response['txn_msg'];
		$this->provab_sms->send_msg($phone, $pg_initialize_data, "600270");
	}
	else
	{
		$status = "Pending or Failed";
		$msg="Booking failed due to some error from supplier. Auto refund status - ".$refund_response["txn_msg"];
		//$this->provab_sms->send_msg($phone, $pg_initialize_data, "600270");
		$msg_detail=$refund_response['txn_err_msg'];
	}
	$this->transaction->update_payment_record_status($booking_id, $status, $refund_response, 'refund');
	$app_reference = $booking_id;
	$email_body = $msg." - ".$msg_detail.". Booking Id: ".$app_reference;
	//debug($refund_response["txn_msg"]); debug($email_body); exit;
	$data = array();
	$data["email_body"] = $email_body;
	ob_start();
	$mail_template = $this->template->isolated_view("agent/refund_template", $data);
	ob_end_clean();
	//Throw mail to agent let him know the refund status
	$this->provab_mailer->send_mail($email, $refund_response["txn_msg"], $mail_template);
	$this->redirect_refund($refund_response['product'], $msg);
		} else {
			exit("Payment gateways are disableb by admin.");
		}
	}

	function redirect_refund($product, $msg)
	{
			switch ($product) {
				case META_AIRLINE_COURSE :
					redirect ( base_url () . 'index.php/flight/exception?op=booking_exception&notification=' . $msg );
					break;
				case META_BUS_COURSE :
					redirect ( base_url () . 'index.php/bus/exception?op=booking_exception&notification=' . $msg );
					break;
				case META_ACCOMODATION_COURSE :
					redirect ( base_url () . 'index.php/hotel/exception?op=booking_exception&notification=' . $msg );
					break;
				case "Instant_Recharge" :
					$this->session->set_flashdata("msg", $msg);
					redirect(base_url('index.php/management/b2b_credit_limit?transaction_type=Instant_Recharge') . '');
					break;
				case META_TRANSFERV1_COURSE :
					redirect(base_url().'index.php/transferv1/exception?op=booking_exception&notification='.$msg);
					break;
				case META_SIGHTSEEING_COURSE :
					redirect(base_url().'index.php/sightseeing/exception?op=booking_exception&notification='.$msg);
					break;
			}
	}

	/**
	 *
	 */
	function success($response=0) {
		$this->load->model('transaction');
		//debug($response);exit;
		if($response)
		{
			$response_id_array=explode('_',$response ['txnid']);
			$product = $response ['productinfo'];
			$book_id = $response_id_array[0];
			$pg_status = $response['status'];
		}
		else
		{
			$product = $_REQUEST ['productinfo'];
			$book_id = $_REQUEST ['txnid'];
			$pg_status = $_REQUEST['status'];
		}
		//debug($response_id_array);exit;
		if($product != "Instant_Recharge"){
			$temp_booking = $this->custom_db->single_table_records('temp_booking', '', array(
				'book_id' => $book_id));
		}
		if($product == "Instant_Recharge"){
			$book_id = $response['txnid'];
			$master_txn_id = explode("-", $book_id)[2];
			$temp_booking = $this->custom_db->single_table_records('master_transaction_details', '', array('origin' => $master_txn_id));
		}
		
		$pg_record = $this->transaction->read_payment_record($book_id);
		if ($pg_status == 'success' and empty($pg_record) == false and valid_array($pg_record) == true && valid_array ( $temp_booking ['data'] )) {
			//update payment gateway status
			if($response)
				$response_params = $response;
			else
				$response_params = $_REQUEST;
			if($product != "PACKAGE_BALANCE_AMOUNT"){
				$this->transaction->update_payment_record_status($book_id, ACCEPTED, $response_params);
			}
			if($product == "Instant_Recharge"){
				$msg = "Payment is successfull and your account is recharged.";
				$this->session->set_flashdata("msg", $msg);
				$details["status"] = "accepted";
				$details["update_remarks"] = "Credited Towards: Instant recharge";
				$details["remarks"] = "Amount credited to wallet";
				$condition["origin"] = $master_txn_id;
				$this->custom_db->update_record('master_transaction_details', $details, $condition);
				$this->load->model('domain_management_model');
				$req_params = json_decode($pg_record["request_params"], true);
				$amount = $pg_record["amount"] - $req_params["convenience_amount"];
				$remarks = "Amount credited to wallet";
                $crdit_towards = "Instant recharge";
				$this->notification->credit_balance($this->entity_user_id, $book_id, $crdit_towards, $amount, 0, $remarks);
				$this->custom_db->delete_record('master_transaction_details', array("system_transaction_id"=>$book_id));
				$this->custom_db->update_record('master_transaction_details', array("system_transaction_id"=>$book_id), $condition);
				redirect(base_url('index.php/management/b2b_credit_limit?transaction_type=Instant_Recharge'));
				exit;
			}
			
			$book_origin = $temp_booking ['data'] ['0'] ['id'];
			if($product == "PACKAGE_BALANCE_AMOUNT"){
				$book_origin = $book_origin.'-'.$response_id_array[1];
			}
			$is_paid_by_pg=1;
			$this->redirect_booking($product, $book_id, $book_origin, $is_paid_by_pg);

		}
	}

	private function redirect_booking($product, $book_id, $book_origin, $is_paid_by_pg=0)
	{
		//debug($product);exit;
		switch ($product) {
			case META_AIRLINE_COURSE :
				redirect (base_url () . 'index.php/flight/process_booking/' . $book_id . '/' . $book_origin.'/'.$is_paid_by_pg);
				break;
			case META_BUS_COURSE :
				redirect ( base_url () . 'index.php/bus/process_booking/' . $book_id . '/' . $book_origin.'/'.$is_paid_by_pg);
				break;
			case META_ACCOMODATION_COURSE :
				redirect ( base_url () . 'index.php/hotel/process_booking/' . $book_id . '/' . $book_origin.'/'.$is_paid_by_pg);
				break;
			case META_CAR_COURSE :
				redirect ( base_url () . 'index.php/car/process_booking/' . $book_id . '/' . $book_origin );
				break;
			case META_TRANSFERV1_COURSE:
				redirect(base_url().'index.php/transferv1/process_booking/'.$book_id.'/'.$book_origin.'/'.$is_paid_by_pg);
				break;
			case META_SIGHTSEEING_COURSE:
				redirect(base_url().'index.php/sightseeing/process_booking/'.$book_id.'/'.$book_origin.'/'.$is_paid_by_pg);
				break;
			case META_PACKAGE_COURSE:
				redirect(base_url().'index.php/tours/process_booking/'.$book_id.'/'.$book_origin.'/'.$is_paid_by_pg);
				break;
			case 'PACKAGE_BALANCE_AMOUNT':
				//debug($book_origin);exit;
				redirect(base_url().'index.php/tours/process_balance_pay/'.$book_id.'/'.$book_origin.'/'.$is_paid_by_pg);
				break;
			default :
				redirect ( base_url().'index.php/transaction/cancel' );
				break;
		}
	}

	/**
	 *
	 */
	function cancel($response=0) {
		$this->load->model('transaction');
		if($response)
		{
			$product = $response ['productinfo'];
			$book_id = $response ['txnid'];
		}
		else
		{
			$product = $_REQUEST ['productinfo'];
			$book_id = $_REQUEST ['txnid'];
		}
		if($product != "Instant_Recharge"){
			$temp_booking = $this->custom_db->single_table_records('temp_booking', '', array(
				'book_id' => $book_id));
		}
		if($product == "Instant_Recharge"){
			$master_txn_id = explode("-", $book_id)[2];
			$temp_booking = $this->custom_db->single_table_records('master_transaction_details', '', array('origin' => $master_txn_id));
		}
		$pg_record = $this->transaction->read_payment_record($book_id);
		if (empty($pg_record) == false and valid_array($pg_record) == true && valid_array ( $temp_booking ['data'] )) {
			if($response)
				$response_params = $response;
			else
				$response_params = $_REQUEST;
			$this->transaction->update_payment_record_status($book_id, DECLINED, $response_params);
			$msg = "Payment Unsuccessful, Please try again.";
			switch ($product) {
				case META_AIRLINE_COURSE :
					redirect ( base_url () . 'index.php/flight/exception?op=booking_exception&notification=' . $msg );
					break;
				case META_BUS_COURSE :
					redirect ( base_url () . 'index.php/bus/exception?op=booking_exception&notification=' . $msg );
					break;
				case META_ACCOMODATION_COURSE :
					redirect ( base_url () . 'index.php/hotel/exception?op=booking_exception&notification=' . $msg );
					break;
				case META_PACKAGE_COURSE :
					redirect ( base_url () . 'index.php/tours/exception?op=booking_exception&notification=' . $msg );
					break;
				case "Instant_Recharge" :
					$this->session->set_flashdata("msg", $msg);
					$details["status"] = "declined";
					$condition["origin"] = $book_id;
					$this->custom_db->update_record('master_transaction_details', $details, $condition);
					redirect(base_url('index.php/management/b2b_credit_limit?transaction_type=Instant_Recharge') . '');
					break;
			}
		}
	}


	function transaction_log(){
		load_pg_lib('PAYU');
		echo $this->template->isolated_view('payment/PAYU/pay');
	}

	/**
	 * Snippet to redirect Techprocess PG response to Success or Cancel Functions.
	 * Before Redirection we need to decrypt the response data & then redirect.
	 * This extra function is written because there is no two url concept available with 
	 * Techprocess.
	 * Snippet written by Shashikumar Misal <shashikumar.misal@provabmail.com>
	*/
	function techprocess_response()
	{
		if(empty($_POST))
			exit("Sorry, No response from the payment gateway.");

		$PG = "TECHP";
		load_pg_lib($PG);
		$response = $this->pg->read_response($_POST);
		if($response["status"]=="success")
			$this->success($response);
		else
			$this->cancel($response);
	}

	/**
	 * Snippet to redirect Paytm PG response to Success or Cancel Functions.
	 * Before Redirection we need to decrypt the response data & then redirect.
	 * This extra function is written because there is no two url concept available with 
	 * Techprocess.
	 * Snippet written by Shashikumar Misal <shashikumar.misal@provabmail.com>
	*/
	function paytm_response()
	{
		if(empty($_POST))
			exit("Sorry, No response from the payment gateway.");

		$PG = "PAYTM";
		load_pg_lib($PG);
		$response = $this->pg->read_response($_POST);
		if(!$response["is_valid_checksum"]){
			$this->cancel($response);
			exit;
		}
		///debug($response);exit;
		if($response["status"]=="success")
			$this->success($response);
		else
			$this->cancel($response);
	}
}