<?php

/**
 * author: Balu A
 * FORM START
 */
$form_configuration['inputs'] = array(
	'origin' 					=> array('type' => 'hidden',	'label_line_code' => -1, 'mandatory' => false),
	'threshold_amount'					=> array('type' => 'select',	'label_line_code' => 213, 'source' => 'enum', 'source_id' => 'threshold_amount_range'),
	'mobile_number'	=> array('type' => 'text',	'label_line_code' => 214, 'mandatory' => false),
	'email_id'			=> array('type' => 'email',	'label_line_code' => 215, 'mandatory' => false),
	'enable_sms_notification'		=> array('type' => 'checkbox',	'label_line_code' => 216, 'source' => 'enum', 'source_id' => 'agree_sms_alert', 'mandatory' => false),
	'enable_email_notification'		=> array('type' => 'checkbox',	'label_line_code' => 216, 'source' => 'enum', 'source_id' => 'agree_email_alert', 'mandatory' => false)
);

/**
 * Add FORM
 */
$form_configuration['form']['set_balance_alert_form'] = array(
'form_header' => '',
		'sections' => array(
			array('elements' => array(
					'origin', 'threshold_amount',
					'mobile_number', 'email_id',
					 'enable_sms_notification', 'enable_email_notification'
				),
				'fieldset' => 'FFL0049'
			)
		),
		'form_footer' => array('update', 'reset')
	);
/*** Form End ***/
/**
 * FORM VALIDATION SETTINGS
 */
$auto_validator['origin'] = 'trim|numeric|required';
$auto_validator['threshold_amount'] = 'trim|required';
$auto_validator['mobile_number'] = 'trim|min_length[7]|max_length[10]|numeric';
$auto_validator['email_id'] = 'trim';