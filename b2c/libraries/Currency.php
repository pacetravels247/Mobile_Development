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
		//call parent
		parent::__construct($params);
	}
	/**
	 * Set Commission
	 */
	function set_commission($override=true)
	{
		$CI = &get_instance();
		$this->commission_fees_row['admin_commission_list']['commission_currency'] = get_application_currency_preference();
		$this->commission_fees_row['admin_commission_list']['value'] = 0;
	}

	/**
	 * Get Commission
	 */
	public function get_commission()
	{
		$this->set_commission(true);
		return $this->commission_fees_row;
	}
}
