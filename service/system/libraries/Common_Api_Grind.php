<?php

/**
 * Provab Common Functionality For API Class
 * @package	Provab
 * @subpackage	provab
 * @category	Libraries
 * @author		Arjun J<arjun.provab@gmail.com>
 * @link		http://www.provab.com
 */
abstract class Common_Api_Grind {
	protected $DomainKey;
	public $master_search_data;
	protected $search_hash;
	protected $config;
	protected $search_id;
	var $booking_source;
	var $booking_source_name;
	function __construct($module, $api,$config_table_origin) {
		$CI = &get_instance ();
		$CI->load->model ( 'api_model' );
		$c = $CI->api_model->active_api_config ( $module, $api,$config_table_origin );
		if ($c != false && empty ( $c ['config'] ) == false) {
			$this->config = json_decode ( $c ['config'], true );
			$this->booking_source = $api;
			$this->booking_source_name = $c['remarks'];
		} else {
			//echo 'exit';
			//exit ();
		}
	}
	
	/**
	 * Return master search details
	 *
	 * @param string $key
	 *        	key for which value has to be searched from master search data
	 */
	function get_master_search_data($key = false) {
		if (empty ( $key )) {
			return $this->master_search_data;
		} else {
			return $this->master_search_data [$key];
		}
	}
	
	/**
	 * Arjun J Gowda
	 * convert search params to format required by booking source
	 *
	 * @param number $search_id
	 *        	unique id which identifies search details
	 */
	abstract function search_data($search_id);
	
	/**
	 * Arjun J Gowda
	 * update markup currency and return summary
	 *
	 * @param array $price_summary        	
	 * @param object $currency_obj        	
	 */
	abstract function update_markup_currency(& $price_summary, & $currency_obj);
	
	/**
	 * Arjun J Gowda
	 * calculate and return total price details
	 *
	 * @param array $price_summary
	 *        	- price which has to be added
	 */
	abstract function total_price($price_summary);
	
	/**
	 * Arjun J Gowda
	 * Process Booking
	 *
	 * @param array $booking_params        	
	 */
	abstract function process_booking($booking_params, $app_reference, $sequence_number, $search_id);
	protected function time_filter_category($time_value) {
		$category = 1;
		$time_offset = intval ( date ( 'H', strtotime ( $time_value ) ) );
		if ($time_offset < 6) {
			$category = 1;
		} elseif ($time_offset < 12) {
			$category = 2;
		} elseif ($time_offset < 18) {
			$category = 3;
		} else {
			$category = 4;
		}
		return $category;
	}
	/**
	 * Generate Category For Stop
	 */
	protected function stop_filter_category($stop_count) {
		$category = 1;
		switch (intval ( $stop_count )) {
			case 0 :
				$category = 1;
				break;
			case 1 :
				$category = 2;
				break;
			default :
				$category = 3;
				break;
		}
		return $category;
	}
	
	/**
	 * Returns price default price object
	 */
	function get_price_object() {
		$price_obj = array (
				"Currency" => false,
				"TotalDisplayFare" => 0,
				"PriceBreakup" => array (
						'BasicFare' => 0,
						'Tax' => 0,
						'AgentCommission' => 0,
						'AgentTdsOnCommision' => 0
				) 
		);
		return $price_obj;
	}
}
