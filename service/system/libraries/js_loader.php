<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include 'page_configuration.php';
/**
 * provab
 *
 *
 * Portal Application
 *
 * @package		provab
 * @author		Arjun J<arjun.provab@gmail.com>
 * @copyright	Copyright (c) 2013 - 2014
 * @link		http://provab.com
 */

class Js_Loader extends Page_Configuration {
	/**
	 * Provab Javascript_Loader Class
	 *
	 * enable basic Javascript on Page
	 *
	 * @package		provab
	 * @subpackage	Libraries
	 * @category	Libraries
	 * @author		Arjun J<arjun.provab@gmail.com>
	 * @link		http://provab.com
	 */
	protected static $datepicker = array();
	protected static $auto_adjust_datepicker = array();
	protected static $auto_adjust_datepicker_report = array();
	protected static $popover = array();
	protected static $DT;
	protected static $load_header_files = true;
	protected static $load_shared_files = array();

	function __construct($page_name='')
	{
		parent::__construct($page_name);
	}

	/**
	 * load header files only once so using static
	 */
	static function load_header_files()
	{
		if (self::$load_header_files == true) {
			/** COMMON JS FUNCTIONALITY VALIDATION, POPUP **/
			include COMMON_UI_JS;
			self::$load_header_files = false;
		}
	}

	/**
	 * load header files only once so using static
	 */
	function load_shared_files($file_name)
	{
		include COMMON_SHARED_JS.$file_name;
	}

	/**
	 * generate basic javascript & jquery for page
	 * generates datapicker for ip elements, popovers, validations
	 */
	function enable_javascript($form_name='')
	{
		$popover = '';
		$datepicker = '';
		$form_name = (empty($form_name) ? $this->name : $form_name);
		if (valid_array($this->config['form'][$form_name]['sections']) == true) {
			foreach ($this->config['form'][$form_name]['sections'] as $sec_key => $sec_val) {
				foreach ($sec_val['elements'] as $i_k => $i_v) {
					if (isset($this->config['inputs'][$i_v]) == true and isset($this->config['inputs'][$i_v]['type']) == true) {
						if ($this->config['inputs'][$i_v]['type'] == 'date') {
							if(isset($this->config['inputs'][$i_v]['enable'])) {
								self::$datepicker[] = array($i_v,$this->config['inputs'][$i_v]['enable']);
							} else {
								self::$datepicker[]=$i_v;
							}
							self::$popover[] = $i_v;
						} elseif ($this->config['inputs'][$i_v]['type'] != 'hidden') {
							self::$popover[] = $i_v;
						}
					}
					if (isset($this->config['inputs'][$i_v]) == true and isset($this->config['inputs'][$i_v]['DT']) == true) {
						if (isset(self::$DT[$this->config['inputs'][$i_v]['DT']]) == false) {
							self::$DT[$this->config['inputs'][$i_v]['DT']] = provab_solid_regexp($this->config['inputs'][$i_v]['DT']);
						}
					}
					//$this->validation[$i_v] = empty($this->config['inputs'][$i_v]['DT']) ? false : $this->config['inputs'][$i_v]['DT'];
				}
			}
		} elseif ( valid_array($this->config['form'][$form_name]) == true && valid_array($this->config['form'][$form_name]['sections']) == false) {
			foreach ($sec_val['elements'] as $i_k => $i_v) {
				if (isset($this->config['inputs'][$i_v]) == true and isset($this->config['inputs'][$i_v]['type']) == true) {
					if ($this->config['inputs'][$i_v]['type'] == 'date') {
						if(isset($this->config['inputs'][$i_v]['enable'])) {
							$this->datepicker[] = array($i_v,$this->config['inputs'][$i_v]['enable']);
						} else {
							$this->datepicker[]=$i_v;
						}
						$this->popover[] = $i_v;
					}
				}
			}
		}
	}

	static function load_core_resource_files()
	{
		self::load_header_files();
		/** COMMON JS FUNCTIONALITY VALIDATION, POPUP **/
		include COMMON_JS;
		/** COMMON JS FUNCTIONALITY VALIDATION, POPUP **/
		/** START DATEPICKER **/
		if (valid_array(self::$datepicker) == true) {
			include DATEPICKER_JS;
		}

	}

	/**
	 * set datepicker value
	 * @param array $ip_list datepicker list
	 *
		$initial_datepicker = array(array('from_date', FUTURE_DATE_TIME));
		$this->current_page->set_datepicker($initial_datepicker);
	 */
	function set_datepicker($ip_list)
	{
		self::$datepicker = array_merge(self::$datepicker, $ip_list);
	}
	
	/**
	 * Arjun J Gowda
	 * @param array $ip_list 
	 */
	function auto_adjust_datepicker($ip_list)
	{
		self::$auto_adjust_datepicker = array_merge(self::$auto_adjust_datepicker, $ip_list);
	}
	function auto_adjust_datepicker_report($ip_list){
		self::$auto_adjust_datepicker_report = array_merge(self::$auto_adjust_datepicker_report, $ip_list);
	}
}