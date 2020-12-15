<?php
class Payment_model extends CI_Model
{

	function __construct()
	{
      parent::__construct();
      $ci = &get_instance();
      $ci->load->database();
	}

	function create_pay_record($app_reference, $booking_fare, $firstname, $email, $phone, $productinfo, $convenience_fees=0, $promocode_discount=0, $currency_conversion_rate)
	{
		$duplicate_pg = $ci->read_pay_record($app_reference);
		if($duplicate_pg) {
		$payment_currency = $ci->config->item('payment_gateway_currency');
		$request_params = array('txnid' => $app_reference,
				'booking_fare' => $booking_fare,
				'convenience_amount' => $convenience_fees,
				'promocode_discount' => $promocode_discount,
				'firstname' => $firstname,
				'email'=> $email,
				'phone'=> $phone,
				'productinfo'=> $productinfo);
			$data['amount'] = roundoff_number($booking_fare+$convenience_fees-$promocode_discount);
			$data['domain_origin'] = get_domain_auth_id();
			$data['app_reference'] = $app_reference;
			$data['request_params'] = json_encode($request_params);
			$data['currency'] = $payment_gateway_currency;
			$data['currency_conversion_rate'] = $currency_conversion_rate;
			$ci->custom_db->insert_record('payment_gateway_details', $data);
			return true;
		} else {
			return false;
		}
	}

	function read_pay_record($app_reference)
	{
		$cond['app_reference'] = $app_reference;
		$data = $ci->custom_db->single_table_records('payment_gateway_details', '*', $cond);
		if ($data['status'] == SUCCESS_STATUS) {
			return $data['data'][0];
		} else {
			return false;
		}
	}

	function update_pay_record($app_reference, $status, $response_params=array())
	{
			$cond['app_reference'] = $app_reference;
		$data['status'] = $status;
		if (valid_array($response_params) == true) {
			$data['response_params'] = json_encode($response_params);
		}
		$ci->custom_db->update_record('payment_gateway_details', $data, $cond);
	}
}
?>