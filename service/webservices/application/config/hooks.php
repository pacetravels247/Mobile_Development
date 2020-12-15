<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 | -------------------------------------------------------------------------
 | Hooks
 | -------------------------------------------------------------------------
 | This file lets you define "hooks" to extend CI without hacking the core
 | files.  Please see the user guide for info:
 |
 |	http://codeigniter.com/user_guide/general/hooks.html
 |
 */

/*$hook['post_controller_constructor'][] = array(
                               'class'    => 'application',
                               'function' => 'validate_domain',
                               'filename' => 'hooks.php',
                               'filepath' => 'hooks',
                               'params'   => array()
);*/

$hook['post_controller_constructor'][] = array(
                               'class'    => 'application',
                               'function' => 'initialize_domain_modules',
                               'filename' => 'hooks.php',
                               'filepath' => 'hooks',
                               'params'   => array()
);

$hook['post_controller_constructor'][] = array(
                               'class'    => 'application',
                               'function' => 'initilize_multiple_login',
                               'filename' => 'hooks.php',
                               'filepath' => 'hooks',
                               'params'   => array()
);

$hook['post_controller_constructor'][] = array(
                               'class'    => 'application',
                               'function' => 'load_current_page_configuration',
                               'filename' => 'hooks.php',
                               'filepath' => 'hooks',
                               'params'   => array()
);

$hook['display_override'][] = array(
		'class' => '',
		'function' => 'compress',
		'filename' => 'compress.php',
		'filepath' => 'hooks'
);

/* End of file hooks.php */
/* Location: ./application/config/hooks.php */