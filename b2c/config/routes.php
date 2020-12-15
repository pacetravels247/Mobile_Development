<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 | -------------------------------------------------------------------------
 | URI ROUTING
 | -------------------------------------------------------------------------
 | This file lets you re-map URI requests to specific controller functions.
 |
 | Typically there is a one-to-one relationship between a URL string
 | and its corresponding controller class/method. The segments in a
 | URL normally follow this pattern:
 |
 |	example.com/class/method/id/
 |
 | In some instances, however, you may want to remap this relationship
 | so that a different class/function is called than the one
 | corresponding to the URL.
 |
 | Please see the user guide for complete details:
 |
 |	http://codeigniter.com/user_guide/general/routing.html
 |
 | -------------------------------------------------------------------------
 | RESERVED ROUTES
 | -------------------------------------------------------------------------
 |
 | There area two reserved routes:	
 |
 |	$route['default_controller'] = 'welcome';
 |
 | This route indicates which controller class should be loaded if the
 | URI contains no data. In the above example, the "welcome" class
 | would be loaded.
 |
 |	$route['404_override'] = 'errors/page_missing';
 |
 | This route will tell the Router what URI segments to use if those provided
 | in the URL cannot be matched to a valid route.
 |
 */
//Adding Custom Routes for CMS Url Rewriting
require_once APPPATH .'libraries/custom_router.php';
$route = Custom_Router::cms_routes();
$route = Custom_Router::cms_routes_new();
$meta_tags = Custom_Router::set_meta_tags();
$domain_key = Custom_Router::domain_details();
// echo "<pre>";print_r($domai_key);exit;

$route['default_controller'] = "general";

$route['404_override'] = 'general/ooops';
// define('CURRENT_DOMAIN_KEY', $domain_key);
// echo CURRENT_DOMAIN_KEY;exit;

/* End of file routes.php */
/* Location: ./application/config/routes.php */