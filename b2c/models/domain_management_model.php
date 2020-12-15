<?php
require_once 'abstract_management_model.php';
/**
 * @package    current domain Application
 * @subpackage Travel Portal
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V2
 */
Class Domain_Management_Model extends Abstract_Management_Model
{
	private $airline_markup;
	private $hotel_markup;
	private $bus_markup;
	private $sightseeing_markup;
	private $transferv1_markup;
	var $verify_domain_balance;

	function __construct() {
		parent::__construct('level_2');
		$this->verify_domain_balance = $this->config->item('verify_domain_balance');
	}

	/**
	 * Balu A
	 * Get markup based on different modules
	 * @return array('value' => 0, 'type' => '')
	 */
	function get_markup($module_name, $is_domestic='')
	{
	
		$markup_data = '';
		switch ($module_name) {
			case 'flight' : $markup_data = $this->airline_markup($is_domestic);
			break;
			case 'hotel' : $markup_data = $this->hotel_markup();
			break;
			case 'bus' : $markup_data = $this->bus_markup();
			break;
			case 'sightseeing' : $markup_data = $this->sightseeing_markup();
			break;
			case 'car' : $markup_data = $this->car_markup();
			break;
			case 'transferv1':$markup_data = $this->transferv1_markup();
			break;
		}
		return $markup_data;
	}

	/**
	 * 
	 * Balu A
	 * Manage domain markup for current domain - Domain wise and module wise
	 */
	function airline_markup($is_domestic='')
	{
		if (empty($this->airline_markup) == true) {
			$response['specific_markup_list'] = array();
			$specific_ailine_markup_list = $this->specific_airline_markup('b2c_flight', $is_domestic);
			$response['specific_markup_list'] = $specific_ailine_markup_list;
			$response['generic_markup_list'] = $this->generic_domain_markup('b2c_flight', $is_domestic);
			$this->airline_markup = $response;
		} else {
			$response = $this->airline_markup;
		}
		return $response;
	}

	/**
	 * Balu A
	 * Manage domain markup for current domain - Domain wise and module wise
	 */
	function hotel_markup()
	{
		if (empty($this->hotel_markup) == true) {
			$response['specific_markup_list'] = '';
			$response['generic_markup_list'] = $this->generic_domain_markup('b2c_hotel');
			$this->hotel_markup = $response;
		} else {
			$response = $this->hotel_markup;
		}
		return $response;
	}
	

	/**
	 * Elavarasi
	 * Manage domain markup for current domain - Domain wise and module wise
	 */
	function sightseeing_markup()
	{
		if (empty($this->sightseeing_markup) == true) {
			$response['specific_markup_list'] = '';
			$response['generic_markup_list'] = $this->generic_domain_markup('b2c_sightseeing');
			
			$this->sightseeing_markup = $response;
		} else {
			$response = $this->sightseeing_markup;
		}
		return $response;
	}

	/**
	 * Elavarasi
	 * Manage domain markup for current domain - Domain wise and module wise
	 */
	function transferv1_markup(){
		if (empty($this->transferv1_markup) == true) {
			$response['specific_markup_list'] = '';
			$response['generic_markup_list'] = $this->generic_domain_markup('b2c_transferv1');
			
			$this->transferv1_markup = $response;
		} else {
			$response = $this->transferv1_markup;
		}
		return $response;
	}

	/**
	 * Balu A
	 * Manage domain markup for current domain - Domain wise and module wise
	 */
	function bus_markup()
	{
		if (empty($this->bus_markup) == true) {
			$response['specific_markup_list'] = '';
			$response['generic_markup_list'] = $this->generic_domain_markup('b2c_bus');
			$this->bus_markup = $response;
		} else {
			$response = $this->bus_markup;
		}
		return $response;
	}
	/**
	 * Anitha G
	 * Manage domain markup for current domain - Domain wise and module wise
	 */
	function car_markup()
	{
		if (empty($this->car_markup) == true) {
			$response['specific_markup_list'] = '';
			$response['generic_markup_list'] = $this->generic_domain_markup('b2c_car');
			$this->car_markup = $response;
		} else {
			$response = $this->car_markup;
		}
		return $response;
	}

	/**
	 * Balu A
	 * Get generic markup based on the module type
	 * @param $module_type
	 * @param $markup_level
	 */
	function generic_domain_markup($module_type, $is_domestic='')
	{
		if($is_domestic == 1){
			$query = 'SELECT ML.origin AS markup_origin, ML.type AS markup_type, ML.reference_id, ML.value, ML.value_type, ML.markup_currency AS markup_currency
			FROM markup_list AS ML where ML.value != "" and ML.module_type = "'.$module_type.'" and
			ML.markup_level = "'.$this->markup_level.'" and ML.type="generic" and ML.domain_list_fk='.get_domain_auth_id();
			// echo $query;exit;
		}
		else{
			$query = 'SELECT ML.origin AS markup_origin, ML.type AS markup_type, ML.reference_id, ML.int_value, ML.int_value_type, ML.markup_currency AS markup_currency
			FROM markup_list AS ML where ML.value != "" and ML.module_type = "'.$module_type.'" and
			ML.markup_level = "'.$this->markup_level.'" and ML.type="generic" and ML.domain_list_fk='.get_domain_auth_id();
		}
		// echo $query;exit;
		$generic_data_list = $this->db->query($query)->result_array();
		return $generic_data_list;
	}

	/**
	 *  Balu A
	 * Get specific markup based on module type
	 * @param string $module_type	Name of the module for which the markup has to be returned
	 * @param string $markup_level	Level of markup
	 */
	function specific_airline_markup($module_type, $is_domestic='')
	{
		$markup_list = array();
		if($is_domestic == 1){
			$query = 'SELECT AL.origin AS airline_origin, AL.name AS airline_name, AL.code AS airline_code,
			ML.origin AS markup_origin, ML.type AS markup_type, ML.reference_id, ML.value, ML.value_type, ML.markup_currency AS markup_currency
			FROM airline_list AS AL JOIN markup_list AS ML where ML.value != "" and
			ML.module_type = "'.$module_type.'" and ML.markup_level = "'.$this->markup_level.'" and AL.origin=ML.reference_id and ML.type="specific"
			and ML.domain_list_fk != 0  and ML.domain_list_fk='.get_domain_auth_id().' order by AL.name ASC';
		}
		else{
			$query = 'SELECT AL.origin AS airline_origin, AL.name AS airline_name, AL.code AS airline_code,
			ML.origin AS markup_origin, ML.type AS markup_type, ML.reference_id, ML.int_value, ML.int_value_type, ML.markup_currency AS markup_currency
			FROM airline_list AS AL JOIN markup_list AS ML where ML.int_value != "" and
			ML.module_type = "'.$module_type.'" and ML.markup_level = "'.$this->markup_level.'" and AL.origin=ML.reference_id and ML.type="specific"
			and ML.domain_list_fk != 0  and ML.domain_list_fk='.get_domain_auth_id().' order by AL.name ASC';
		}	
		$specific_data_list = $this->db->query($query)->result_array();
		if (valid_array($specific_data_list) == true) {
			foreach ($specific_data_list as $__k => $__v) {
				$markup_list[$__v['airline_code']] = $__v;
			}
		}
		return $markup_list;
	}

	/**
	 * Check if the Booking Amount is allowed on Client Domain
	 */
	function verify_current_balance($amount, $currency)
	{
		$status = FAILURE_STATUS;
		if ($this->verify_domain_balance == true) {
			//OBSELETE - NO USE OF THIS
			if ($amount > 0) {
				$query = 'SELECT DL.balance, CC.country as currency, CC.value as conversion_value from domain_list as DL, currency_converter AS CC where CC.id=DL.currency_converter_fk
			AND DL.status='.ACTIVE.' and DL.origin='.$this->db->escape(get_domain_auth_id()).' and DL.domain_key = '.$this->db->escape(get_domain_key());
				$balance_record = $this->db->query($query)->row_array();
				if ($currency == $balance_record['currency']) {
					$balance = $balance_record['balance'];
					if ($balance >= $amount) {
						$status = SUCCESS_STATUS;
					} else {
						//Notify User about current balance problem
						//FIXME - send email, notification for less balance to domain admin and current domain admin
						$this->application_logger->balance_status('Your Balance Is Very Low To Make Booking Of '.$amount.' '.$currency);
					}
				} else {
					echo 'Under Construction';
					exit;
				}
			}
		} else {
			$status = SUCCESS_STATUS;
		}
		return $status;
	}

	/**
	 *
	 * @param $fare
	 * @param $domain_markup
	 * @param $level_one_markup this is 0 as default as it is not mandatory to keep level one markup
	 */
	function update_transaction_details($transaction_type, $app_reference, $fare, $domain_markup, $level_one_markup=0, $convinence=0, $discount=0, $currency='INR', $currency_conversion_rate=1, $gst=0)
	{
		$this->load->model('user_model');
		$currency = empty($currency) == true ? get_application_currency_preference(): $currency; 
		$status = FAILURE_STATUS;
		$amount = floatval($fare+$level_one_markup+$convinence-$discount);
		$remarks = $transaction_type.' Transaction was Successfully done';
		$notification_users = $this->user_model->get_admin_user_id();
		$action_query_string = array('app_reference' => $app_reference, 'type' => $transaction_type, 'module' => $this->config->item('current_module'));
		if ($this->verify_domain_balance == true) {
			echo 'We Dont Support This';
			exit;
			if ($amount > 0) {
				//deduct balance and continue
				$this->private_management_model->update_domain_balance(get_domain_auth_id(), (-$amount));
				//Log transaction details

				$this->save_transaction_details($transaction_type, $app_reference, $fare, $domain_markup, $level_one_markup, $convinence, $discount, $remarks, $currency, $currency_conversion_rate, $gst);
				$this->application_logger->transaction_status($remarks.'('.$amount.')', $action_query_string, $notification_users);
			}
		} else {
			$this->save_transaction_details($transaction_type, $app_reference, $fare, $domain_markup, $level_one_markup, $convinence, $discount, $remarks, $currency, $currency_conversion_rate, $gst);
			$this->application_logger->transaction_status($remarks.'('.$amount.')', $action_query_string, $notification_users);
			$status = SUCCESS_STATUS;
		}
		return $status;
	}

	/**
	 * Save transaction logging for security purpose
	 * @param string $transaction_type
	 * @param string $app_reference
	 * @param number $fare
	 * @param number $domain_markup
	 * @param number $level_one_markup
	 * @param string $remarks
	 */
	function save_transaction_details($transaction_type, $app_reference, $fare, $domain_markup, $level_one_markup, $convinence, $discount, $remarks, $currency='', $currency_conversion_rate=1, $gst)
	{
		$currency = empty($currency) == true ? get_application_currency_preference(): $currency;
		$transaction_log['system_transaction_id']	= date('Ymd-His').'-S-'.rand(1, 10000);
		$transaction_log['transaction_type']		= $transaction_type;
		$transaction_log['domain_origin']			= get_domain_auth_id();
		$transaction_log['app_reference']			= $app_reference;
		$transaction_log['fare']					= $fare;
		$transaction_log['level_one_markup']		= $level_one_markup;
		$transaction_log['domain_markup']			= $domain_markup;
		$transaction_log['convinence_fees']			= $convinence;
		$transaction_log['promocode_discount']		= $discount;
		$transaction_log['remarks']					= $remarks;
		$transaction_log['created_by_id']			= intval(@$this->entity_user_id) ;
		$transaction_log['gst']		= $gst;
		
		$transaction_log['currency']				= $currency;
		$transaction_log['currency_conversion_rate']= $currency_conversion_rate;
		$transaction_log['opening_balance'] = 0;
		$transaction_log['closing_balance'] = 0;
		$transaction_log['remarks'] = 0;
		$transaction_log['transaction_owner_id'] = intval(@$this->entity_user_id);
		$transaction_log['created_datetime'] = date('Y-m-d h:i:s');
		
		$this->custom_db->insert_record('transaction_log', $transaction_log);
	}
	//Added by Anitha checking supplier balance
	function verify_api_balance($booking_amount, $booking_source){
		$balance = 0;
		// $booking_source = PROVAB_FLIGHT_BOOKING_SOURCE;
		if($booking_source == PROVAB_FLIGHT_BOOKING_SOURCE){
			load_flight_lib ($booking_source);
			$balance = $this->flight_lib->GettBalance();
			$balance = 10000000;

		}
		else{
			
			$balance_data = $this->custom_db->single_table_records('booking_source', 'balance, due_amount, credit_limit',array('source_id' => $booking_source));
			// debug($balance_data);exit;
			$balance_record = $balance_data['data'][0];
			$balance = $balance_record['balance'] + floatval($balance_record ['credit_limit']) + floatval($balance_record ['due_amount']);
		}
		
		return $balance;
		
		
	}
}
