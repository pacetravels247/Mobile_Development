<?php
/** 
 * FORM START
 */
$form_configuration['inputs'] = array(
	'agent_id' 		  => array('type' => 'select',   'label_line_code' => 245, 'source' => 'db', 'source_location'  => 'db_cache_api::get_agent_list_with_id'),
	'app_reference'   => array('type' => 'text', 'label_line_code' => 247),
	'amount'    	  => array('type' => 'text', 'label_line_code' => 251, 'class' => array('numeric')),
	'issued_for' 	  => array('type' => 'select', 'label_line_code' => 250, 'source' => 'enum', 'source_id' => 'balance_debit_credit_reasons'),
	'remarks' => array('type' => 'textarea', 'label_line_code' => 249)
);

/**
 * Add FORM
 */
$form_configuration['form']['credit_balance_agent_list'] = array(
'form_header' => '',
		'sections' => array(
			array('elements' => array('agent_id'),
				'fieldset' => 'FFL0052'
			)
		),
		'form_footer' => array()
	);
	$form_configuration['form']['credit_balance'] = array(
'form_header' => '',
		'sections' => array(
			array('elements' => array('agent_id', 'issued_for', 'app_reference','amount', 'remarks'),
				'fieldset' => 'FFL0052'
			)
		),
		'form_footer' => array('update', 'reset')
	);
/*** Form End ***/
// LETS CLEAN DISABLED LABEL DATA
/**
* adding to disabled to make sure that no validation is done for update
*/
$disabled['credit_balance'] = array();

/*** Form End ***/
/**
 * FORM VALIDATION SETTINGS
 */
$auto_validator['agent_id'] = 'trim|required|min_length[1]|xss_clean';
$auto_validator['app_reference'] = 'trim|required|xss_clean';
$auto_validator['amount'] = 'trim|required|numeric';
$auto_validator['issued_for'] = 'trim|required|xss_clean';
$auto_validator['remarks'] = 'trim|required|xss_clean';
