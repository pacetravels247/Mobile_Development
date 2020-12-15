<?php
/**
 * FORM INPUT ELEMENTS
 */
$form_configuration['inputs'] =array(
    'origin' => array('type' => 'hidden',  'label_line_code' => -1),
    'en_account_name' => array('type' => 'text', 'label_line_code' => 218),
	'en_bank_name' => array('type' => 'text',  'label_line_code' =>220),
	'en_branch_name' => array('type' => 'text',  'label_line_code' =>225),
    'account_number' => array('type' => 'number',  'label_line_code' => 219, 'class' => array('numeric')),
    'ifsc_code' => array('type' => 'text',  'label_line_code' => 223),
    'pan_number' => array('type' => 'text',  'label_line_code' => 224),
	'bank_icon' => array('type' => 'file',  'label_line_code' => 221, 'mandatory' => false),
    'status'	  => array('type' => 'radio', 	 'label_line_code' => 222, 'source' => 'enum', 'source_id' => 'status'));
/**
 * FORM ATTRIBUTES AND SECTIONS
 */
$form_attributes = array('method' => 'POST', 'action' => '');
$form_configuration['form']['bank_account_details'] = array(
	'form_header' => $form_attributes,
	'sections' => array(array('elements' => array('en_account_name', 'account_number', 'ifsc_code', 'pan_number', 'en_bank_name', 'en_branch_name', 'bank_icon', 'status'), 'fieldset' => 'FFL0050')),
	'form_footer' => array(array('type' => 'submit', 'lang_line_code' => 13), 'reset')
);
$form_configuration['form']['bank_account_details_edit'] = array(
	'form_header' => $form_attributes,
	'sections' => array(array('elements' => array('origin','en_account_name', 'account_number', 'ifsc_code', 'pan_number', 'en_bank_name', 'en_branch_name', 'bank_icon', 'status'), 'fieldset' => 'FFL0050')),
	'form_footer' => array(array('type' => 'submit', 'lang_line_code' => 13), 'reset')
);
/**
 * FORM VALIDATION SETTINGS
 */

$auto_validator['origin'] = 'trim|required|min_length[1]|numeric';
$auto_validator['en_account_name'] = 'trim|required|min_length[3]|max_length[100]';
$auto_validator['en_bank_name'] = 'trim|required|min_length[3]|max_length[80]';
$auto_validator['account_number'] = 'trim|required|min_length[10]|numeric';
$auto_validator['status'] = 'trim|required|min_length[1]|max_length[1]';
$auto_validator['ifsc_code'] = 'trim|required|min_length[3]';
$auto_validator['pan_number'] = 'trim|required|min_length[3]';