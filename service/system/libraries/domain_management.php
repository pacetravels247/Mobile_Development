<?php
/**
 * Manages Domain
 * @package	Provab
 * @subpackage	provab
 * @category	Libraries
 * @author		Jaganath N<jaganath.provab@gmail.com>
 * @link		http://www.provab.com
 */
class Domain_Management 
{
	var $is_domain_user;
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->model('user_model');
		$this->CI->load->model('domain_management_model');
		$this->is_domain_user = $this->CI->config->item('domain_user');
	}
	/**
	 * Checks Domain User is valid or not
	 * @param $_header
	 */
	public function validate_domain($domain_user_details) 
	{
		$data['status'] = FAILURE_STATUS;
		$data['data'] = array();
		$data['message'] = '';
		$DomainKey = trim($domain_user_details['DomainKey']);
		$UserName = trim($domain_user_details['UserName']);
		$Password = trim($domain_user_details['Password']);
		$system = trim($domain_user_details['System']);
        $serverIp=trim($domain_user_details['SERVER_ADDR']);
		$domain_login = $this->CI->user_model->domain_login ($DomainKey, $UserName, $Password, $system, $serverIp);
		if ($domain_login['status'] == SUCCESS_STATUS && valid_array($domain_login['data']) == true) {
			$data['status'] = SUCCESS_STATUS;
			$complete_domain_details = $domain_login['data'];
			//SETTING DOMAIN SESSION
			$domain_session_data = array ();
			$domain_session_data[DOMAIN_AUTH_ID] = intval($complete_domain_details['origin']);
			$domain_key = trim($complete_domain_details ['domain_key']);
			$domain_session_data[DOMAIN_KEY]     = base64_encode($domain_key);
			$this->CI->session->set_userdata($domain_session_data);
			//SETTING DOMAIN DETAILS
			$domain_details = array();
			$domain_details['domain_name']   = 	$complete_domain_details['domain_name'];
			$domain_details['domain_origin'] = 	$complete_domain_details['origin'];
			$domain_details['domain_id'] 	 = 		$complete_domain_details['domain_id'];
			$domain_details['domain_key'] 	 = 	$complete_domain_details['domain_key'];
			$domain_details['status'] 		 = 		$complete_domain_details['status'];
			$domain_details['flight_version']=	$complete_domain_details['flight_version'];
			$domain_details['hotel_version'] = 	$complete_domain_details['hotel_version'];
			$domain_details['bus_version'] 	 = 	$complete_domain_details['bus_version'];
			$domain_details['sightseeing_version'] 		   = $complete_domain_details['sightseeing_version'];
				$domain_details['viator_transfer_version'] = $complete_domain_details['viator_transfer_version'];
			$domain_details['agent_name'] 	 = 	$complete_domain_details['agent_name'];
			$domain_details['agent_email'] 	 = 	$complete_domain_details['agent_email'];
			$domain_details['agent_mobile']  = 	$complete_domain_details['agent_mobile'];
			$data['data'] = $domain_details;
		} else {
			$data['message'] = 'Invalid Domain User Or Invalid IP Address';
		}
		return $data;
	}
	/*
	 * Validates Domain Course Version
	 */
	public function validate_domain_course_version($course, $course_version, $domain_details)
	{
		$data['status'] = SUCCESS_STATUS;
		$data['message'] = '';
		$version_status = false;
		$domain_course_version = '';
		switch($course){
			case META_AIRLINE_COURSE:
				$domain_course_version = $domain_details['flight_version'];
				break;
			case META_ACCOMODATION_COURSE:
				$domain_course_version = $domain_details['hotel_version'];
				break;
			case META_BUS_COURSE:
				$domain_course_version = $domain_details['bus_version'];
				break;
			case META_SIGHTSEEING_COURSE:
				$domain_course_version = $domain_details['sightseeing_version'];
				break;
			case META_VIATOR_TRANSFER_COURSE:
				$domain_course_version = $domain_details['viator_transfer_version'];
				break;
		}
		if($course_version == $domain_course_version){
			$version_status = true;
		}
		if($version_status == false){
			$data['status'] = FAILURE_STATUS;
			$data['message'] = 'Invalid Version';
		}
		return $data;
	}
	/**
	 * Flight Commission Details
	 */
	public function get_flight_commission($domain_origin)
	{
		if($this->is_domain_user == true) {
			$flight_commission_query = 'select BFCD.* from b2b_flight_commission_details as BFCD
									where ((BFCD.domain_list_fk ='.intval($domain_origin).' and BFCD.type="specific")	OR BFCD.type="generic")
									group by BFCD.domain_list_fk
									order by BFCD.domain_list_fk desc';
			$flight_commission_details = $this->CI->db->query($flight_commission_query)->row_array();
			$commission = $flight_commission_details['value'];
			return $commission;
		} else {
			return 100;//Giving Full commission
		}
	}
		/**
	 * Sightseeing Commission Details
	 */
	public function get_sightseeing_commission($domain_origin)
	{
		if($this->is_domain_user == true) {
			$ss_commission_query = 'select BSCD.* from b2b_sightseeing_commission_details as BSCD
									where ((BSCD.domain_list_fk ='.intval($domain_origin).' and BSCD.type="specific")	OR BSCD.type="generic")
									group by BSCD.domain_list_fk
									order by BSCD.domain_list_fk desc';
			$sightseeing_commission_details = $this->CI->db->query($ss_commission_query)->result_array();
			$commission=0.00;
			//debug($sightseeing_commission_details);

			if(valid_array($sightseeing_commission_details)){
				foreach ($sightseeing_commission_details as $key => $value) {
					if($value['type']=='specific' && ceil($value['api_value']) !=0){
						//echo "sdfdf";
						return $commission = $value['value'];
					}elseif ($value['type']=='generic'){
						//echo "ela";
						return $commission = $value['value'];
					}
				}
			}else{
				return $commission;
			}
			
			
			//$commission = $sightseeing_commission_details['value'];
			//          return $commission;
		} else {
			return 100;//Giving Full commission
		}
	}
	/**
	 * Viator Transfer Commission Details
	 */
	public function get_viator_transfer_commission($domain_origin)
	{
		if($this->is_domain_user == true) {
			$vt_commission_query = 'select BTCD.* from b2b_viator_transfer_commission_details as BTCD
									where ((BTCD.domain_list_fk ='.intval($domain_origin).' and BTCD.type="specific")	OR BTCD.type="generic")
									group by BTCD.domain_list_fk
									order by BTCD.domain_list_fk desc';
			$viatransfer_commission_details = $this->CI->db->query($vt_commission_query)->result_array();
			$commission=0.00;
			//debug($sightseeing_commission_details);
			if($viatransfer_commission_details){
				foreach ($viatransfer_commission_details as $key => $value) {
					if($value['type']=='specific' && ceil($value['api_value']) !=0){
						//echo "sdfdf";
						return $commission = $value['value'];
					}elseif ($value['type']=='generic'){
						//echo "ela";
						return $commission = $value['value'];
					}
				}
			}else{
				return $commission;
			}
			
			
			//$commission = $sightseeing_commission_details['value'];
			//          return $commission;
		} else {
			return 100;//Giving Full commission
		}
	}
	/**
	 Verifies the Domain Balance with the Booking transaction amount and Environment
	 */
	public function verify_domain_balance($transaction_amount, $credential_type)
	{
		
		if($this->is_domain_user == true) {
			//Converting Transaction amount to Client Base Currency
			$currency_obj = new Currency(array('from' => get_application_default_currency() , 'to' => domain_base_currency()));
			$amount_details = $currency_obj->force_currency_conversion($transaction_amount);
			$transaction_amount = $amount_details['default_value'];
			$currency = $amount_details['default_currency'];
			$status = $this->CI->user_model->get_balance($transaction_amount, $credential_type, $currency);
		} else {
			$status = true;
		}
		return $status;
	}
        

	/**
	 Verifies the Domain Balance with the Booking transaction amount and Environment only for transfer and sightseeing  because for transfer and sightseeing we are passing amount direct amount not converting 
	 */
	public function verify_domain_balance_viator($transaction_amount, $credential_type)
	{		
		if($this->is_domain_user == true) {
			//No need to convert base currency			
			$transaction_amount = $transaction_amount;
			$currency = domain_base_currency();
			$status = $this->CI->user_model->get_balance($transaction_amount, $credential_type, $currency);
			
		} else {
			$status = true;
		}
		
		return $status;
	}

	
     /**
	 Verifies the Domain Balance with the Booking transaction amount and Environment for offline bookings
	 */
	public function verify_domain_balance_offline_bookings($transaction_amount, $credential_type, $origin, $get_currency)
	{
            
            
		if($this->is_domain_user == true) {
			//Converting Transaction amount to Client Base Currency
                    
			$currency_obj = new Currency(array('from' => get_application_default_currency() , 'to' => $get_currency));
                        
			$amount_details = $currency_obj->force_currency_conversion($transaction_amount);

			$transaction_amount = $amount_details['default_value'];
			$currency = $amount_details['default_currency'];
                    
			$status = $this->CI->user_model->get_balance($transaction_amount, $credential_type, $currency,'', $origin);
		} else {
			$status = true;
		}
		return $status;
	}
	/**
	 * Debit the Domain's Balance
	 * @param unknown_type $transaction_amount
	 * @param unknown_type $credential_type
	 */
	public function debit_domain_balance($transaction_amount, $credential_type, $domain_origin=0, $attr=array())
	{
		if(intval($domain_origin) > 0){
			$domain_details = $this->CI->domain_management_model->get_domain_details($domain_origin);
			$domain_base_currency = $domain_details['domain_base_currency'];
		} else{
			$domain_base_currency = domain_base_currency();
		}
		//Converting Transaction amount to Client Base Currency
		$core_booking_amount = $transaction_amount;
		$currency_obj = new Currency(array('from' => get_application_default_currency() , 'to' => $domain_base_currency));
		//$amount_details = $currency_obj->force_currency_conversion($transaction_amount);
		//$transaction_amount = $amount_details['default_value'];

		if(valid_array($attr)){
			if($attr['transaction_type']=='viator_transfer' || $attr['transaction_type'] == 'sightseeing'){

				$transaction_amount = $transaction_amount;

			}else{
				
				$amount_details = $currency_obj->force_currency_conversion($transaction_amount);
				$transaction_amount = $amount_details['default_value'];
			}
		}else{
			
			$amount_details = $currency_obj->force_currency_conversion($transaction_amount);
			$transaction_amount = $amount_details['default_value'];
		}
		//Add status
		if($this->is_domain_user == true) {
			//Debiiting the Booking Amount
			$debit_amount = -($transaction_amount);
			$this->CI->user_model->update_balance($debit_amount, $credential_type,$domain_origin);
			//Logs Booking Amount
			if(isset($attr['app_reference']) == true && empty($attr['app_reference']) == false && isset($attr['transaction_type']) == true && empty($attr['transaction_type']) == false){
				$app_reference = trim($attr['app_reference']);
				$transaction_type = trim($attr['transaction_type']);
				$currency_conversion_rate = $currency_obj->get_domain_currency_conversion_rate();
				$this->CI->domain_management_model->booking_amount_logger($transaction_type, $app_reference, $core_booking_amount, $domain_base_currency, $currency_conversion_rate);
			}
		}
	}
        
        
        /**
	 * Debit the Domain's Balance when updating the PNR by Admin panel (HOLD Booking)
	 * @param unknown_type $transaction_amount
	 * @param unknown_type $credential_type
	 */
        public function debit_domain_balance_hold_booking($transaction_amount, $credential_type, $domain_origin=0, $attr=array())
	{
            
		if(intval($domain_origin) > 0){
			$domain_details = $this->CI->domain_management_model->get_domain_details($domain_origin);
			$domain_base_currency = $domain_details['domain_base_currency'];
		} else{
			$domain_base_currency = domain_base_currency();
		}
                
                
                $core_booking_amount = $transaction_amount;
                if($attr['currency_conversion_rate']!=1)
                {
                       $amount_details['default_value']= ($transaction_amount * $attr['currency_conversion_rate']);
                       $amount_details['default_currency']=$domain_base_currency;
                       $transaction_amount = number_format($amount_details['default_value'], 3, '.', '');
                }
                
		
		
                
		//Add status
		if($this->is_domain_user == true) {
                    
			//Debiiting the Booking Amount
			 $debit_amount = -($transaction_amount);
			
			$this->CI->user_model->update_balance($debit_amount, $credential_type,$domain_origin);
                        
			//Logs Booking Amount
			if(isset($attr['app_reference']) == true && empty($attr['app_reference']) == false && isset($attr['transaction_type']) == true && empty($attr['transaction_type']) == false){
                            
				$app_reference = trim($attr['app_reference']);
				$transaction_type = trim($attr['transaction_type']);
				$currency_conversion_rate = $attr['currency_conversion_rate'];
				$this->CI->domain_management_model->booking_amount_logger($transaction_type, $app_reference, $core_booking_amount, $domain_base_currency, $currency_conversion_rate);
			}
		}
                 
	}
        
        
	/**
	 * Credit the Domain's Balance
	 * @param unknown_type $transaction_amount
	 * @param unknown_type $credential_type
	 * $domain_origin => In Supervision module $domain_origin will be passed
	 */
	public function credit_domain_balance($transaction_amount, $credential_type, $domain_origin=0, $attr=array())
	{	
		$core_credit_amount = $transaction_amount;
		if(intval($domain_origin) > 0){
			$domain_details = $this->CI->domain_management_model->get_domain_details($domain_origin);
			$domain_base_currency = $domain_details['domain_base_currency'];
		} else{
			$domain_base_currency = domain_base_currency();
		}
		//Converting Transaction amount to Client Base Currency
		$currency_obj = new Currency(array('from' => get_application_default_currency() , 'to' => $domain_base_currency));
		$amount_details = $currency_obj->force_currency_conversion($transaction_amount);
		$transaction_amount = $amount_details['default_value'];
		//Add status
		if($this->is_domain_user == true) {
			$credit_amount = floatval($transaction_amount);
			$this->CI->user_model->update_balance($credit_amount, $credential_type, $domain_origin);
			//Logs Booking Amount
			if(isset($attr['app_reference']) == true && empty($attr['app_reference']) == false && isset($attr['transaction_type']) == true && empty($attr['transaction_type']) == false){
				$core_credit_amount = -($core_credit_amount);
				$app_reference = trim($attr['app_reference']);
				$transaction_type = trim($attr['transaction_type']);
				$currency_conversion_rate = $currency_obj->get_domain_currency_conversion_rate();
				$this->CI->domain_management_model->booking_amount_logger($transaction_type, $app_reference, $core_credit_amount, $domain_base_currency, $currency_conversion_rate, $domain_origin);
			}
		}
	}
	/****
	** Get Agent bying Price 
	** Jeeva 
	****/
	function agent_buying_price($fare_details)
	{
		$fare_details = force_multple_data_format($fare_details);
		$agent_buying_price = array();
		foreach($fare_details as $k => $v) {
			$fare = (isset($v['fare']) ? $v['fare'] : $v['total_fare']);
			$agent_commission = (isset($v['agent_commission']) ? $v['agent_commission'] : 0);
			$tds_on_commission = (isset($v['agent_tds']) ? $v['agent_tds'] : 0);
			$agent_buying_price[$k] = $fare+$v['domain_markup']+$tds_on_commission-$agent_commission;
		}
		return $agent_buying_price;
	}
}
