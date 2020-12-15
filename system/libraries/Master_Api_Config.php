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
		
		//$c = $CI->api_model->active_api_config ( $module, $api );
		
		//Live Credentials
		$c = array();
		$c["config"] = '{"user_code":"X911","username":"airlines@safarapna","password":"Golden@2020", "endpoint_url":"http://xmlhub.rezlive.com/simulator.php/action"}';
		//$c['config'] = my_crypt($c['config'],'d');

		if ($c != false && empty ( $c ['config'] ) == false) {
			$this->config = json_decode ( $c ['config'], true );
		} else {
			echo 'check your configuration in api config and entry in activity_source_map, booking_source...';
			exit ();
		}
	}
}