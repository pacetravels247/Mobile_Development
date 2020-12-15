<?php
/**
 * Provab Provab_Page_Loader Class
 *
 * Handle all the Forms in the application
 *
 * @package	Provab
 * @subpackage	provab
 * @category	Libraries
 * @author		Balu A<balu.provab@gmail.com>
 * @link		http://www.provab.com
 */
abstract class Page_Configuration {

	protected $config; // page configurations
	protected $name;
	public $auto_validator;
	public $disabled;

	public function __construct($page_name='')
	{
		if (empty($page_name) == true) {
			$segment_two = $GLOBALS['CI']->uri->segment(2);
			$page_name = (empty($segment_two) == false) ? $segment_two : '';
		}
		$configuration_path = CORE_PAGE_CONFIGURATIONS.$page_name.'.php';
		if (file_exists($configuration_path) == true && empty($this->config)) {
			include $configuration_path;
			if (empty($form_configuration) == false and is_array($form_configuration) == true) {
				$this->config = $form_configuration;
				$this->name = $page_name;
				$this->auto_validator = isset($auto_validator) ? $auto_validator : false;
				$this->disabled		 = isset($disabled) ? $disabled : false;
			} else {
				echo get_message('UL002');
				exit;
			}
		} else {
			//echo get_message('UL002');
			//exit;
		}
	}
	
	public function override_configuration($input_element, $config_key, $config_value)
	{
		if ($this->config['inputs'][$input_element] && $this->config['inputs'][$input_element][$config_key]) {
			$this->config['inputs'][$input_element][$config_key] = $config_value;
		}
	}
}

