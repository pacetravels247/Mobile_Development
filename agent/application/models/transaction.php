<?php
/**
 * Library which has generic functions to get data
 *
 * @package    Provab Application
 * @subpackage Flight Model
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V2
 */
Class Transaction extends CI_Model
{
	/**
	 * Lock All the tables necessary for flight transaction to be processed
	 */
	public static function lock_tables()
	{
		echo 'Under Construction';
	}
	/**
	 * Create Payment record for payment gateway used in the application
	 */
	public function create_payment_record($app_reference, $booking_fare, $firstname, $email, $phone, $productinfo, $convenience_fees=0, $promocode_discount=0,$currency_conversion_rate, $selected_pm="WALLET", $payment_mode="wallet")
	{	
		$duplicate_pg = $this->read_payment_record($app_reference);
		if ($duplicate_pg == false) {
			$payment_gateway_currency = $this->config->item('payment_gateway_currency');
			$request_params = array('txnid' => $app_reference,
				'booking_fare' => $booking_fare,
				'convenience_amount' => $convenience_fees,
				'promocode_discount' => $promocode_discount,
				'firstname' => $firstname,
				'email'=> $email,
				'phone'=> $phone,
				'productinfo'=> $productinfo);
			//Add total amount and remove discount from total amount
			$data['amount'] = ceil($booking_fare+$convenience_fees-$promocode_discount);
			$data['domain_origin'] = get_domain_auth_id();
			$data['app_reference'] = $app_reference;
			$data['request_params'] = json_encode($request_params);
			$data['currency'] = $payment_gateway_currency;
			$data['currency_conversion_rate'] = $currency_conversion_rate;
			$data['pg_name'] = $selected_pm;
			$data['payment_mode'] = $payment_mode;
			//debug($data);exit;
			//Add default value for PDO
			$data['response_params'] = '';
			$data['created_datetime'] = date('Y-m-d H:i:s');
			$data['transaction_owner_id'] = $GLOBALS['CI']->entity_user_id;

			$this->custom_db->insert_record('payment_gateway_details', $data);
			return true;
		} else {
			return false;
		}
	}
	/**	
	 * Create Payment record for payment gateway used in the application	
	 */	
	public function update_payment_record($app_reference,$selected_pm="WALLET",$payment_mode="wallet",$pay_ref)	
	{		
			$data['pg_name'] = $selected_pm;	
			$data['payment_mode'] = $payment_mode;	
			$cond['app_reference']=$app_reference;	
			$cond['payment_history_ref']=$pay_ref;	
			$this->custom_db->update_record('payment_gateway_details',$data, $cond);	
			return true;	
	}
	/**
	 * Read Payment record with payment gateway reference
	 * @param $app_reference
	 */
	function read_payment_record($app_reference)
	{
		$cond['app_reference'] = $app_reference;
		$data = $this->custom_db->single_table_records('payment_gateway_details', '*', $cond);
		if ($data['status'] == SUCCESS_STATUS) {
			return $data['data'][0];
		} else {
			return false;
		}
	}
/**
	*SANCHITHA
	 * Read Payment record with payment history reference
	 * @param $app_reference
	 */
	function read_ref_payment_record($app_reference,$ref_id)
	{
		$cond['app_reference'] = $app_reference;
		$cond['payment_history_ref'] = $ref_id;
		$data = $this->custom_db->single_table_records('payment_gateway_details', '*', $cond);
		if ($data['status'] == SUCCESS_STATUS) {
			return $data['data'][0];
		} else {
			return false;
		}
	}
	/**
	 * Update Payment record with payment gateway reference
	 * @param $app_reference
	 */
	function update_payment_record_status($app_reference, $status, $response_params=array(), $tried="pay")
	{
		$cond['app_reference'] = $app_reference;
		$data['status'] = $status;
		if (valid_array($response_params) == true) {
			if($tried=="pay")
				$data['response_params'] = json_encode($response_params);
			if($tried=="refund")
				$data['refund_params'] = json_encode($response_params);
		}
		$this->custom_db->update_record('payment_gateway_details', $data, $cond);
	}
/**	
	 * Update Payment record when paying package balance amount	
	 * @param $app_reference	
	 */	
	function update_payment_status($app_reference, $ref)	
	{	
		$cond['app_reference'] = $app_reference;	
		$cond['payment_history_ref'] = $ref;	
		$data['status'] = 'accepted';	
			
		$cond1['enquiry_reference_no'] = $app_reference;	
		$cond1['id'] = $ref;	
		$data1['status'] = 'SUCCESS';	
		$this->custom_db->update_record('payment_gateway_details', $data, $cond);	
		$this->custom_db->update_record('tour_payment_slab_details', $data1, $cond1);	
	}
		/**
	 * Update additional details of transaction
	 */
	function update_convinence_discount_details($book_detail_table, $app_reference, $discount=0, $promo_code ='',$convinence=0, $convinence_value=0, $convinence_type=0, $convinence_per_pax=0, $gst=0, $pace_fees=0, $supplier_fees=0)
	{
		
		$data = array();
		if (empty($discount) == false) {
			$data['discount'] = $discount;
		} else {
			$data['discount'] = 0;
		}

		if (empty($convinence) == false) {
			$data['convinence_amount'] = $convinence;
		}

		if (empty($convinence_value) == false) {
			$data['convinence_value'] = $convinence_value;
		}

		if (empty($convinence_type) == false) {
			$data['convinence_value_type'] = $convinence_type;
		}

		if (empty($convinence_per_pax) == false) {
			$data['convinence_per_pax'] = $convinence_per_pax;
		}
		if (empty($pace_fees) == false) {
			$data['pace_fees'] = $pace_fees;
		}
		if (empty($supplier_fees) == false) {
			$data['supplier_fees'] = $supplier_fees;
		}
		$data['convinence_per_pax'] = 0;
		if (empty($gst) == false) {
			$data['gst'] = $gst;
		}
	  	//debug($data);exit;
		$cond['app_reference'] = $app_reference;
		$this->custom_db->update_record($book_detail_table, $data, $cond);
	}


	/**
	 * Unlock All The Tables
	 */
	public static function release_locked_tables()
	{
		$CI = & get_instance();
		$CI->db->query('UNLOCK TABLES');
	}
}
