<?php
/**
 * FORM INPUT ELEMENTS
 */
$form_configuration['inputs'] = array(	
    
	'current_password' => array('type' => 'password', 'label_line_code' => 31, 'DT' => 'PROVAB_SOLID_V50'),
    'new_password' => array('type' => 'password', 'label_line_code' => 32, 'DT' => 'PROVAB_SOLID_V50'),
	'confirm_password' => array('type' => 'password', 'label_line_code' => 14, 'DT' => 'PROVAB_SOLID_V50')
);
/**
 * FORM ATTRIBUTES AND SECTIONS
 */
$form_attributes = array('method' => 'POST', 'action' => '');
$form_configuration['form']['change_password'] = array(
	'form_header' => $form_attributes,
	'sections' => array(array('elements' => array('current_password','new_password', 'confirm_password'), 'fieldset' => 'FFL006')),
	'form_footer' => array(array('type' => 'submit', 'lang_line_code' => 13), 'reset')
);

/**
 * FORM VALIDATION SETTINGS
 */
$auto_validator['current_password'] = 'trim|required|min_length[5]|max_length[50]';
$auto_validator['new_password'] = 'trim|required|min_length[5]|max_length[50]|matches[confirm_password]';
$auto_validator['confirm_password'] = 'trim|required';