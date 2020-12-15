<?php

/**
 * Provab Common Functionality For API Class
* @package	Provab
* @subpackage	provab
* @category	Libraries
* @author		Arjun J<arjun.provab@gmail.com>
* @link		http://www.provab.com
*/
abstract class Master_Api_Config {
	protected $DomainKey;	
	protected $config;
	protected $source_code;
	function __construct($module, $api) {
		$CI = &get_instance ();
		$CI->load->model ( 'api_model' );
		$CI->load->model ( 'db_cache_api' );
		$c = $CI->api_model->active_api_config ( $module, $api );
		if ($c != false && empty ( $c ['config'] ) == false) {
			$this->config = json_decode ( $c ['config'], true );
		} else {
			echo 'here exit';
			exit ();
		}
	}
}