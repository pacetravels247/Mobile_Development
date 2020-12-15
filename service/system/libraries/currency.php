<?php

/**
 * Provab Currency Class
 *
 * Handle all the currency conversion in the application
 *
 * @package	Provab
 * @subpackage	provab
 * @category	Libraries
 * @author		Arjun J<arjun.provab@gmail.com>
 * @link		http://www.provab.com
 */
class Currency {

	public $to_currency;   //currency to which the conversion is done
	var $conversion_rate; // default conversion rate
	protected $from_currency;//currency from which the conversion is done
	//private $CI;		//access CI object
	protected $markup_type;//type of the markup
	protected $module_name;//name of the module for which the current object is used
	protected $level_one_markup;//Level one is Main root level markup
	protected $current_domain_markup;//This is current domain markup
	protected $conversion_cache;
	protected $currency_symbol;
	protected $convinence_fees_row;

	public function __construct($params=array())
	{
		$this->to_currency = isset($params['to']) ? $params['to'] : UNIVERSAL_DEFAULT_CURRENCY;
		$this->from_currency = isset($params['from']) ? $params['from'] : COURSE_LIST_DEFAULT_CURRENCY_VALUE;
		$this->markup_type = isset($params['markup_type']) ? $params['markup_type'] : MARKUP_VALUE_PERCENTAGE;
		$this->module_name = isset($params['module_type']) ? $params['module_type'] : 'flight';
		$this->conversion_cache[$this->from_currency.$this->to_currency] = (($this->from_currency == $this->to_currency) ? 1 : $this->getConversionRate());
		//$this->set_currency_symbol(array($this->from_currency));
		//$this->getConversionRate();
	}

	/**
	 * set currency conversion symbol
	 */
	function set_currency_symbol($currency_code)
	{
		$CI = &get_instance();
		$cond = '';
		if (is_array($currency_code) == true) {
			//set multiple
			$currency = '';
			foreach ($currency_code as $k => $v) {
				if (isset($this->currency_symbol[$v]) == false) {
					$currency .= $CI->db->escape($v).',';
				}
			}
			if (empty($currency) == false) {
				$cond = 'country IN ('.substr($currency, 0, -1).')';
			}
		} else {
			//single force to multiple
			if (isset($this->currency_symbol[$currency_code]) == false) {
				$cond = 'country = '.$CI->db->escape($currency_code);
			}
		}

		if (empty($cond) == false) {
			$currency_data = $CI->db->query($query = ' SELECT * FROM currency_converter WHERE '.$cond)->result_array();
			foreach ($currency_data as $k => $v) {
				if (empty($v['currency_symbol']) == false) {
					$this->currency_symbol[$v['country']] = $v['currency_symbol'];
				} else {
					$this->currency_symbol[$v['country']] = $v['country'];
				}
			}
		}
	}

	/**
	 * return currency symbol for currency
	 */
	function get_currency_symbol($currency)
	{
            
		if (is_string($currency)) {
			if (isset($this->currency_symbol[$currency]) == false) {
				$this->set_currency_symbol($currency);
			}
			return $this->currency_symbol[$currency];
		} else {
			return false;
		}
	}

	/**
	 *  Arjun J Gowda
	 * forcefully change conversion Rate
	 * @param unknown_type $conversion_rate
	 */
	public function setConversionRate($conversion_rate)
	{
		$this->conversion_cache[$this->from_currency.$this->to_currency] = $conversion_rate;
	}

	/**
	 *  Arjun J Gowda
	 * get the conversion rate
	 * @param $from from currency
	 * @param $to   to currency
	 */
	public function getConversionRate($override_conversion_rate=false, $from_currency='', $to_currency='')
	{
		$CI = &get_instance();
		if (empty($from_currency) == false) {
			$from = $from_currency;
		} else {
			$from = $this->from_currency;
		}

		if (empty($to_currency) == false) {
			$to = $to_currency;
		} else {
			$to = $this->to_currency;
		}

		if ($override_conversion_rate == true || empty($this->conversion_cache[$from.$to]) == true) {
			$CI->load->model('domain_management_model');
			//Get Currency Conversion Rate w.r.t INR
                        
			$currency_details = $CI->domain_management_model->get_currency_conversion_rate($to);
                        
			$conversion_rate = $currency_details['conversion_rate'];
			 $this->conversion_cache[$from.$to] = round(1/$conversion_rate, 7);
			
			/*$url = 'http://download.finance.yahoo.com/d/quotes.csv?s='.$from.$to.'=X&f=nl1';
			$handle = fopen($url, 'r');
			if ($handle) {
				$currency_data = fgetcsv($handle);
				fclose($handle);
			}
			if ($currency_data != '') {
				if (isset($currency_data[0]) == true and empty($currency_data[0]) == false and isset($currency_data[1]) == true and empty($currency_data[1]) == false) {
					$this->conversion_cache[$from.$to] = floatval($currency_data[1]);
				}
			} else {
				//get it from database
				$this->conversion_cache[$from.$to] = 1;
			}*/
		}
		return $this->conversion_cache[$from.$to];
	}

	/**
	 * Arjun J Gowda
	 * @param $value
	 */
	function force_currency_conversion($value)
	{
            
		return array('default_value' => ($value*$this->conversion_cache[$this->from_currency.$this->to_currency]), 'default_currency' => $this->to_currency);
	}

	/**
	 * Arjun J Gowda
	 *get currency
	 *
	 *@param number $value    money which has to be converted
	 *@param number $currency Currency to which we have to convert
	 *@param number $markup   percentage of markup which has to be added
	 *
	 *@return array having country list
	 */
	function get_currency($value, $add_markup=true, $level_one_markup=false, $current_domain_markup=true, $markup_multiplier=1, $booking_source = '', $version = FLIGHT_VERSION_1, $OperatorCode='', $is_domestic='')
	{
      
		$this->set_markup_details($level_one_markup, $current_domain_markup, $version, $OperatorCode, $is_domestic);
                
		$value = $value*$this->conversion_cache[$this->from_currency.$this->to_currency];
             
		if ($add_markup) {
                  
			$value = $this->add_markup($value, $markup_multiplier, $level_one_markup, $current_domain_markup, $booking_source, $OperatorCode);
			return array('default_value' => number_format($value['value'], 2, '.', ''), 'default_currency' => $this->to_currency,'markup_type'=>$value['markup_type']);
		}
                
		//add level one markup and then level two markup
		return array('default_value' => number_format($value, 2, '.', ''), 'default_currency' => $this->to_currency);
	}

	/**
	 * Add markup to the value passed
	 * @param number $value
	 */
	protected function add_markup($value, $markup_multiplier=1, $level_one_markup, $current_domain_markup, $booking_source,$OperatorCode='')
	{
		$markup_type_arr = array();
		$markup_type = array('');

		if (floatval($value) > 0) {
			$booking_source = trim($booking_source);
			$markup_list = array();
                        
			if ($level_one_markup == true && valid_array($this->level_one_markup) == true) {
				$markup_list[] = $this->level_one_markup;
                             
			}
			if ($current_domain_markup == true && valid_array($this->current_domain_markup) == true) {
				$markup_list[] = $this->current_domain_markup;
			}
                      
                       
			if (valid_array($markup_list) == true) {
				foreach ($markup_list as $__k => $__v) {
					$temp_markup_list = '';
                                        
                                         
                                        if (valid_array(@$__v['airline_wise_markup_list']) == true) {
						
						//Airline Wise  Specific Markup
						if(empty($booking_source) == false){
                                                   
							foreach (@$__v['airline_wise_markup_list'] as $sp_mk => $sp_mv){
                                 
								if(empty($gn_mv['source_id']) == true){
                                                                    
									$temp_markup_list = array($sp_mv);
									
									break;
								}
							}
						} else {
                                                  
							$temp_markup_list = $__v['airline_wise_markup_list'];
						}
					}
                                        
                                          if (valid_array($temp_markup_list) == false && valid_array($__v['specific_markup_list']) == true) {
					
						
						//Domain Specific Markup
						if(empty($booking_source) == false){
							foreach ($__v['specific_markup_list'] as $sp_mk => $sp_mv){
								if(isset($sp_mv['source_id']) == true && $sp_mv['source_id'] == $booking_source){
									$temp_markup_list = array($sp_mv);
									
									break;
								}
							}
						} else {
							$temp_markup_list = $__v['specific_markup_list'];
						}
					}
					if (valid_array($temp_markup_list) == false && valid_array($__v['generic_markup_list']) == true) {
						if(empty($booking_source) == false){
							
							//API Generic Markup
							foreach ($__v['generic_markup_list'] as $gn_mk => $gn_mv){
								if(isset($gn_mv['source_id']) == true && $gn_mv['source_id'] == $booking_source){
									$temp_markup_list = array($gn_mv);
									
									break;
								}
							}
							
							//Generic Markup, if there is no API Generic Markup
							if(valid_array($temp_markup_list) == false){
								foreach ($__v['generic_markup_list'] as $gn_mk => $gn_mv){
									if(empty($gn_mv['source_id']) == true){
										$temp_markup_list = array($gn_mv);
										
										break;
									}
								}
							}
							
						} else {
							$temp_markup_list = $__v['generic_markup_list'];
						}
					}
                                        
                                       
                                        
					if (valid_array($temp_markup_list)) {
						foreach ($temp_markup_list as $__ik => $__iv) {
							$markup_value = 0;
							switch ($__iv['value_type']) {
								case 'percentage' :
                                                                  
									//Just need to calculate percentage of the values
									$markup_value = (($value/100) * $__iv['value']);
                                         
                                    $markup_type[0]='percentage';                             
									break;
								case 'plus' :
									//convert value to required currency and then add the value
									$temp_conversion = $this->getConversionRate(false, $__iv['markup_currency'], $this->to_currency);
									$markup_value = (($__iv['value']*$temp_conversion) * $markup_multiplier);
									$markup_type[0] ='plus';
									break;
							}
                                                      
							$value = $value + $markup_value;
                                                        
                                                        // echo $value.'|'.$markup_value.'|'.$OperatorCode.'<br />';exit;
						}
					}
				}
			}
		}
		if(valid_array($markup_type)){
			$markup_type_arr['markup_type']=$markup_type[0];
		}else{
			$markup_type_arr['markup_type']='plus';
		}
		$markup_type_arr['value'] = $value;
		
		return $markup_type_arr;
	}


	/**
	 * Arjun J Gowda
	 * Set markup details for the current module
	 */
	protected function set_markup_details($level_one_markup, $current_domain_markup, $version, $OperatorCode='', $is_domestic='')
	{
           
		$CI = &get_instance();
               
                /*if ($OperatorCode != '' and empty($this->airline_wise_markup)) {
                    
                       $this->airline_wise_markup= $CI->private_management_model->get_markup($this->module_name, $version, $OperatorCode);
		}*/
		if ($level_one_markup == true and empty($this->level_one_markup)) {
			$this->level_one_markup			= $CI->private_management_model->get_markup($this->module_name, $version, $OperatorCode, $is_domestic);
                      
		}

		if ($current_domain_markup == true and empty($this->current_domain_markup)) {
			$this->current_domain_markup	= $CI->domain_management_model->get_markup($this->module_name);
                        
		}
             
	} 

	/**
	 * get entity markup
	 */
	function get_entity_markup()
	{
		$CI = &get_instance();
		//b2b_markup
		$markup_data = $CI->general_model->get_entity_markup();
		if (valid_array($markup_data['data']) == true) {
			foreach ($markup_data['data'] as $k => $v) {
				$this->user_markup[$v['type']] = array('value' => $v['value'], 'value_type' => $v['value_type']);
			}
		} else {
			$this->user_markup = false;
		}
	}

	/**
	 * Set Convenience Fees
	 */
	function set_convenience_fees($search_id)
	{
		$CI = &get_instance();
		$this->convinence_fees_row = $CI->private_management_model->get_convinence_fees($this->module_name, $search_id);
	}
	
/**
	 * Get Convenience Fees
	 */
	function get_convenience_fees()
	{
		return $this->convinence_fees_row;
	}

	/**
	 * Convenience Fees
	 * @param number $amount	Amount on which convinence fees should be added
	 * @param number $search_id	Search ID to be used
	 */
	function convenience_fees($amount, $search_id)
	{
		//Based on module name we have to add convenience fees
		$convinence_fees = 0;
		$this->set_convenience_fees($search_id);
		switch ($this->module_name) {
			case 'flight' : $convinence_fees = $this->airline_convinence_fees($amount, $this->convinence_fees_row, $search_id);
			//Calculate Convenience fees
			break;
			case 'hotel' : $convinence_fees = $this->hotel_convinence_fees($amount, $this->convinence_fees_row, $search_id);
			break;
		}
		return $convinence_fees;
	}

	/**
	 * Calculate convenience fees sum according to search and settings
	 * @param array $convinence_fees_row
	 * @param number $search_id
	 */
	protected function airline_convinence_fees($amount, $convinence_fees_row, $search_id)
	{
		$CI = &get_instance();
		$convinence_fees = 0;
		$search_data = $CI->flight_model->get_safe_search_data($search_id);

		if (valid_array($convinence_fees_row) == true && floatval($convinence_fees_row['value']) > 0) {
			$convenience_value_type = $convinence_fees_row['type'];
			$convenience_value		= $convinence_fees_row['value'];//
			$per_pax_charge			= $convinence_fees_row['per_pax'];//Used as final multiplier
			$pax_count = intval($search_data['data']['adult_config']+$search_data['data']['child_config']+$search_data['data']['infant_config']);
			
			$trip_type_multiplier = (strtolower($search_data['data']['trip_type']) == 'oneway' ? 1 : 2);
			$convinence_fees = $this->calculate_convenience_fees($amount, $convenience_value_type, $convenience_value, $per_pax_charge, $pax_count);
			$convinence_fees = $trip_type_multiplier*$convinence_fees;
		}
		return $convinence_fees;
	}

	/**
	 * Calculate convenience fees sum according to search and settings
	 * @param array $convinence_fees_row
	 * @param number $search_id
	 */
	protected function hotel_convinence_fees($amount, $convinence_fees_row, $search_id)
	{
		$CI = &get_instance();
		$convinence_fees = 0;
		$search_data = $CI->hotel_model->get_safe_search_data($search_id);
		if (valid_array($convinence_fees_row) == true && floatval($convinence_fees_row['value']) > 0) {
			$convenience_value_type = $convinence_fees_row['type'];
			$convenience_value		= $convinence_fees_row['value'];//
			$per_pax_charge			= $convinence_fees_row['per_pax'];//Used as final multiplier
			$pax_count = intval(array_sum($search_data['data']['adult_config'])+array_sum($search_data['data']['child_config']));
			$convinence_fees = $this->calculate_convenience_fees($amount, $convenience_value_type, $convenience_value, $per_pax_charge, $pax_count);
		}
		return $convinence_fees;
	}

	/**
	 * Calculate Convenience fees for all the modules
	 */
	protected function calculate_convenience_fees($amount, $convenience_value_type, $convenience_value, $per_pax_charge, $pax_count=1)
	{
		$convinence_fees = 0;
		if ($convenience_value_type == 'plus') {
			$convinence_fees = ($convenience_value * $this->getConversionRate(false, COURSE_LIST_DEFAULT_CURRENCY_VALUE, $this->to_currency));
		} elseif ($convenience_value_type == 'percentage') {
			$convinence_fees = (($amount/100)*$convenience_value);
		}

		if ($per_pax_charge == true) {
			$convinence_fees = $convinence_fees * $pax_count;
		}
		return $convinence_fees;
	}
	/**
	 * Jaganath
	 * Calculate TDS
	 * @param $commission
	 */
	public function calculate_tds($commission)
	{
		$CI = &get_instance();
		$commission = floatval($commission);
		//FIXME:Make it Dynamic: Admin will Manage
		$tds = 0;
		if($commission > 0) {
			$commission_tds_deduction_percentage = 5;
			$tds = ($commission*$commission_tds_deduction_percentage)/100;
			$tds = number_format($tds, 3, '.', '');
		}
		return $tds;
	}
	/**
	 * Jaganath
	 * Returns Domain Currency Conversion Rate
	 * @return unknown
	 */
	public function get_domain_currency_conversion_rate()
	{
		$currency_conversion_rate = $this->getConversionRate(false, get_application_default_currency(), domain_base_currency());
		return $currency_conversion_rate;
	}
        
        /**
	 * Anitha G
	 * Returns Specific Domain Currency Conversion Rate
	 * @return unknown
	 */
	public function get_specific_domain_currency_conversion_rate($domain_origin)
	{
		$currency_conversion_rate = $this->getConversionRateSpecificDomain(false, get_application_default_currency(), domain_base_currency(), $domain_origin);
		return $currency_conversion_rate;
	}
	
	public function getConversionRateSpecificDomain($override_conversion_rate=false, $from_currency='', $to_currency='', $domain_origin='')
	{

		$CI = &get_instance();
		if (empty($from_currency) == false) {
			$from = $from_currency;
		} else {
			$from = $this->from_currency;
		}

		if (empty($to_currency) == false) {
			$to = $to_currency;
		} else {
			$to = $this->to_currency;
		}

		if (isset($domain_origin)) {
			$CI->load->model('domain_management_model');
			//Get Currency Conversion Rate w.r.t INR
           	             
			$currency_details = $CI->domain_management_model->get_currency_specific_conversion_rate($to, $domain_origin);
                        
			$conversion_rate = $currency_details['conversion_rate'];
			 $this->conversion_cache[$from.$to] = round(1/$conversion_rate, 7);
			
		}
		return $this->conversion_cache[$from.$to];
	}
        
}
