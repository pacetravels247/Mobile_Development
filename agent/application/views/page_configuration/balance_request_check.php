<?php

/**
 * author: Balu A
 * FORM START
 */
$form_configuration['inputs'] = array(
	'origin' 					=> array('type' => 'hidden',	'label_line_code' => -1, 'mandatory' => false),
	'transaction_type'			=> array('type' => 'hidden',	'label_line_code' => -1),
	'amount'					=> array('type' => 'number',	'label_line_code' => 208, 'class' => array('numeric')),
	'currency_converter_origin'	=> array('type' => 'hidden', 	'label_line_code' => -1),
	'conversion_value'			=> array('type' => 'hidden',	'label_line_code' => -1),
	'date_of_transaction'		=> array('type' => 'text',		'label_line_code' => 206, 'readonly' => true,'enable_dp' => true),
	'bank'						=> array('type' => 'hidden',	'label_line_code' => -1),
	'bank_id'					=> array('type' => 'select',	'label_line_code' => 201, 'source' => 'db', 'source_location' => 'db_cache_api::get_bank_details'),
	'branch'					=> array('type' => 'text',		'label_line_code' => 202, 'readonly' => true),
	'transaction_number'		=> array('type' => 'text',		'label_line_code' => 203),
	'account_number'			=> array('type' => 'text',		'label_line_code' => 219, 'readonly' => true),
	'remarks'					=> array('type' => 'textarea',	'label_line_code' => 211, 'mandatory' => false)
);

/**
 * Add FORM
 */
$form_configuration['form']['request_form'] = array(
'form_header' => '',
		'sections' => array(
			array('elements' => array(
					'origin', 'transaction_type',
					'bank','bank_id', 'branch','account_number', 'transaction_number',
					'amount', 'currency_converter_origin', 'conversion_value',
					'date_of_transaction', 'remarks'
				),
				'fieldset' => 'FFL0048'
			)
		),
		'form_footer' => array('submit', 'reset')
	);
/*** Form End ***/
/**
 * FORM VALIDATION SETTINGS
 */
$auto_validator['origin'] = 'trim|numeric';
$auto_validator['transaction_type'] = 'trim|required';
$auto_validator['amount'] = 'trim|required|numeric';
$auto_validator['currency_converter_origin'] = 'trim|required|numeric';
$auto_validator['conversion_value'] = 'trim|required';
$auto_validator['date_of_transaction'] = 'trim|required';
$auto_validator['bank'] = 'trim|required';
$auto_validator['branch'] = 'trim|required';
$auto_validator['transaction_number'] = 'trim|required';
$auto_validator['remarks'] = 'trim';