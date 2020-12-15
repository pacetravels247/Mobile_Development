<?php
/**
 * author: Balu A
 * FORM START
 */
$form_configuration ['inputs'] = array (
		'amount' => array (
				'type' => 'number',
				'label_line_code' => 208,
				'class' => array (
						'numeric' 
				) 
		),
		'currency_converter_origin' => array (
				'type' => 'hidden',
				'label_line_code' => - 1 
		),
		'conversion_value' => array (
				'type' => 'hidden',
				'label_line_code' => - 1 
		),
		'remarks' => array (
				'type' => 'textarea',
				'label_line_code' => 211,
				'mandatory' => false 
		)
		
);

if($_GET["transaction_type"] == "Instant_Recharge"){
	$form_configuration ['inputs']['amount']['class'][]='ins_amt';
	$form_configuration ['inputs']['selected_pm'] = array('type' => 'radio', 'label_line_code' => 221, 'source' => 'enum', 'source_id' => 'pay_modes', 'DT' => 'PROVAB_SOLID_B01', 'mandatory' => true );
	$form_configuration ['inputs']['convenience_fees'] = array('type' => 'hidden', 'label_line_code' => 1);
}

/**
 * Add FORM
 */
$form_configuration ['form'] ['credit_request_form'] = array (
		'form_header' => '',
		'sections' => array (
				array (
						'elements' => array (
								'amount',
								'currency_converter_origin',
								'conversion_value',
								'remarks'
						),
						'fieldset' => 'FFL0050' 
				) 
		),
		'form_footer' => array (
				'submit',
				'reset' 
		) 
);
if($_GET["transaction_type"] == "Instant_Recharge"){
	$form_configuration ['form'] ['credit_request_form']['sections'][0]['elements'][4]='selected_pm';
	$form_configuration ['form'] ['credit_request_form']['sections'][0]['elements'][5]='convenience_fees';
}
/**
 * * Form End **
 */
/**
 * FORM VALIDATION SETTINGS
 */
$auto_validator ['amount'] = 'trim|numeric';
$auto_validator ['remarks'] = 'trim';
