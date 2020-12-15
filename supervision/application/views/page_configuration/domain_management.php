<?php

/**
 * FORM START
 */
$form_configuration['inputs'] = array(
	'origin' 		  => array('type' => 'hidden',   'label_line_code' => -1),
	'domain_name' 	  => array('type' => 'text',   'label_line_code' => 191),
	'domain_ip' 	  => array('type' => 'text',    'label_line_code' => 192),
	'domain_key' 	  => array('type' => 'text', 'label_line_code' => 193),
	'comment' 		  => array('type' => 'textarea', 'label_line_code' => 194, 'mandatory' => false),
	'theme_id' 		  => array('type' => 'select', 'label_line_code' => 196, 'mandatory' => true, 'source' => 'enum', 'source_id' => 'theme_list'),
	'status' 		  => array('type' => 'radio', 	  'label_line_code' => 195, 'source' => 'enum', 'source_id' => 'status'),
	'domain_modules'  => array('type' => 'checkbox', 'label_line_code' => 198, 'mandatory' => false, 'source' => 'db', 'source_location' => 'db_cache_api::get_course_type')
);

/**
 * Add FORM
 */
$form_attributes = array('method' => 'POST', 'action' => '');
$form_configuration['form']['domain'] = array(
'form_header' => $form_attributes,
	'sections' => array(
		array(
			'elements' => array('origin', 'domain_name', 'domain_ip', 'comment', 'status', 'theme_id'),
			'fieldset' => 'FFL0045'
		),
		array(
			'elements' => array('domain_modules'),
			'fieldset' => 'FFL0047')
	),
	'form_footer' => array('submit', 'reset')
);
/**
 * Update FORM
 */
$form_configuration['form']['domain_edit'] = array(
	'form_header' => $form_attributes,
	'sections' => array(
		array('elements' => array('origin', 'domain_key', 'domain_name', 'domain_ip', 'comment', 'status', 'theme_id'),
			'fieldset' => 'FFL0045'
		),
		array('elements' => array('domain_modules'),
			'fieldset' => 'FFL0047')
	),
	'form_footer' => array('update', 'reset')
);

/*** Form End ***/
// LETS CLEAN DISABLED LABEL DATA
/**
* adding to disabled and email make sure that no validation is done for update
*/
$disabled['domain_edit'] = array('domain_key');

/*** Form End ***/
/**
 * FORM VALIDATION SETTINGS
 */
$auto_validator['origin'] = 'trim|required|numeric|min_length[1]|max_length[11]';
$auto_validator['domain_name'] = 'trim|required|min_length[5]|max_length[500]';
$auto_validator['domain_ip'] = 'trim|required|min_length[10]|max_length[40]';
$auto_validator['comment'] = 'trim';
$auto_validator['status'] = 'trim|required|max_length[1]|numeric';