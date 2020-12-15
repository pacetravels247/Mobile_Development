<?php

/**
 * FORM START
 */
$form_configuration['inputs'] = array(
	'from'   => array('type' => 'text',     'label_line_code' => 217, 'mandatory' => false, 'enable' => PAST_DATE, 'readonly' => true, 'enable_dp' => true),
	'to'   => array('type' => 'text',     'label_line_code' => 218, 'mandatory' => false, 'enable' => PAST_DATE, 'readonly' => true, 'enable_dp' => true),
);

/**
 * Add FORM
 */
$form_attributes = array('method' => 'POST', 'action' => '');
$form_configuration['form']['account'] = array(
'form_header' => $form_attributes,
		'sections' => array(
			array('elements' => array('from', 'to'),
			)
		),
		'form_footer' => array('submit', 'reset')
	);


/*** Form End ***/
/**
 * FORM VALIDATION SETTINGS
 */
$auto_validator['from'] = 'trim|required|min_length[5]|xss_clean';
$auto_validator['to'] = 'trim|required|min_length[5]|xss_clean';
