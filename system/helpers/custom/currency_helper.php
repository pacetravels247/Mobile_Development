<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Convert Currency to usd
 * @param unknown_type $value
 * @param unknown_type $conversion
 */
function convert_to_course_default_currency($value, $conversion)
{
	if (floatval($value) > 0 && floatval($conversion) > 0) {
		return (floatval($value)*floatval($conversion));
	} else {
		return floatval($value);
	}
}

function calculate_percentage($buying, $selling)
{
	$data['total_markup'] = $selling-$buying;
	if (floatval($data['total_markup']) != 0) {
		$data['markup_percentage'] = round(($data['total_markup'] / ($selling - $data['total_markup']))*100, 2);
	} else {
		$data['markup_percentage'] = 0;
	}
	return $data;
}


function increment_percentage($value, $percentage)
{
	return floatval($value) + ((floatval($value)/100) * floatval($percentage));
}

/**
 * return default currency to be used in the application
 */
function get_application_default_currency()
{
	//return COURSE_LIST_DEFAULT_CURRENCY_VALUE;
	$CI = & get_instance();
	$current_module = strtolower(trim($CI->config->item('current_module')));
	if($current_module == 'b2b') {
		$application_default_currency = agent_base_currency();
	} else {
		$application_default_currency = admin_base_currency();
	}
	return $application_default_currency;
}

function get_application_currency_preference()
{
	$CI = & get_instance();
	$currency = $CI->session->userdata('currency');
	return (empty($currency) == false ? $currency : get_application_default_currency());
}
/**
 * Balu A
 * Returns Converted currency value
 * @param unknown_type $converted_currency
 */
function get_converted_currency_value($converted_currency)
{
	
	return  roundoff_number($converted_currency['default_value']);
}
function get_application_display_currency_preference() {
	$CI = & get_instance ();
	$currency = $CI->session->userdata ( 'currency' );
	return (empty ( $currency ) == false ? $currency : $CI->config->item ( 'app_display_currency_preference' ));
}