<?php

/**
 * Balu A
 * Custom Router for URL Rewriting
 * 
 */
class Custom_Router{
	private $db_object;
	function __construct()
	{
		require_once( BASEPATH .'database/DB'. EXT );
		$this->db_object =& DB();
		$this->db_object->db_debug = FALSE;

	}
	/**
	 * CMS Routes
	 */
	public static function cms_routes()
	{
		$router_obj = new Custom_Router();
		$route_query_data = $router_obj->db_object->query('select * from cms_pages where page_status = 1')->result_array();
		
		$route = array();
		if(is_array($route_query_data) == true && count($route_query_data) > 0){
			foreach ($route_query_data as $keys => $values ) {
					$page_title_id = strtolower(str_replace(' ', '', $values ['page_title'])).'/'. $values ['page_id'];
					$route['general/cms/'.$page_title_id] = 'general/cms/Bottom/'.$values ['page_id'];
					
			}
		}
		return $route;
	}
	public static function cms_routes_new()
	{
		$router_obj = new Custom_Router();
		$route_query_data = $router_obj->db_object->query('select * from cms_pages where page_status = 1')->result_array();
		
		$route = array();
		if(is_array($route_query_data) == true && count($route_query_data) > 0){
			foreach ($route_query_data as $keys => $values ) {
					$page_title_id = strtolower(str_replace(' ', '', $values ['page_title'])).'/'. $values ['page_id'];
					$route[$values ['page_label']] = 'general/cms/'.$values ['page_label'];
					
			}
		}
		return $route;
	}
	public static function domain_details(){
		$router_obj = new Custom_Router();
		$route_query_data = $router_obj->db_object->query('select * from domain_list')->result_array();
		$domain_key = $route_query_data[0]['domain_key'];
		return $domain_key;
	}
	/* getting meta tags for SEO purpose*/
	public static function set_meta_tags(){

		$router_obj = new Custom_Router();
		
		$route_query_data = $router_obj->db_object->query('select * from seo')->result_array();
		
		$route = array();
	} 
}
