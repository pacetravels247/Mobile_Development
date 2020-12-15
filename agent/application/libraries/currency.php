<?php

/**
 * Provab Currency Class
 *
 * Handle all the currency conversion in the application
 *
 * @package	Provab
 * @subpackage	provab
 * @category	Libraries
 * @author		Balu A<balu.provab@gmail.com>
 * @link		http://www.provab.com
 */

class Currency extends Master_currency {

	public function __construct($params=array())
	{
		parent::__construct($params);
	}

	/**
	 * Set Commission
	 */
	function set_commission($fd, $override=true, $user_id, $group_id, $booking_source, $type)
	{	
		$CI = &get_instance();
		if ($override === true) {
			$this->commission_fees_row = $CI->private_management_model->get_commission($this->module_name, $fd, $user_id, $group_id, $booking_source, $type);
			
			//Convert if plus to preferred curr
			if ($this->commission_fees_row['admin_commission_list']['value'] > 0 && $this->commission_fees_row['admin_commission_list']['value_type'] == 'plus') {
				//check preferred currency and markup currency
				$from_cur = $this->commission_fees_row['admin_commission_list']['def_currency'];
				$to_cur = $this->from_currency;
				$this->commission_fees_row['admin_commission_list']['commission_currency'] = $to_cur;
				$this->commission_fees_row['admin_commission_list']['value'] = $this->conversion_cache[$from_cur.$to_cur];
			}
		}
	}

	/**
	 * Get Commission
	 */
	function get_commission($fd=0,$user_id=0,$group_id=0, $booking_source='', $type='')
	{
		$this->set_commission($fd, true, $user_id, $group_id, $booking_source, $type);
		return $this->commission_fees_row;
	}
	/**
	 * Balu A
	 * Converts booking amount to agents currency 
	 * @param unknown_type $amount
	 */
	public function get_agent_paybleamount($amount)
	{
		$details = array();
		$currency_conversion_rate = $this->currency_conversion_value(false, admin_base_currency(), agent_base_currency());
		$details['currency'] = agent_base_currency();
		$details['amount'] = roundoff_number($amount*$currency_conversion_rate);
		return $details;
	}
}