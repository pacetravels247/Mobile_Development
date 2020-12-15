<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// ------------------------------------------------------------------------
/**
 * Stratosphere
 *
 * A Travel portal
 *
 * @package		custom validation helper
 * @author		Balu A<balu.provab@gmail.com>
 * @copyright	provab
 * @link		http://www.provab.com
 * @since		Version 1.0
 * @filesource
 */
// ------------------------------------------------------------------------


/**
*check if the user has entered valid number
*
*@param number $number number which has to be validated
*
*@return status of validation
*/
function positive_number($number) {
	$status = '';
	if ($number >= 0) {
		$status = true;
	} else {
		$status = false;
	}
	return $status;
}


/**
*check if the user has entered valid number
*
*@param number $number number which has to be validated
*
*@return status of validation
*/
function valid_integer($number) {
	$status = '';
	if (positive_number($number)) {
		$status = true;
	} else {
		redirect('general/redirect_login');
	}
	return $status;
}

/**
*check if the valid array
*
*@param array $array array which has to be validated
*
*@return status of validation
*/
function valid_array($array='') {
	$status = '';
	if (is_array($array) == true and count($array) > 0) {
		$status = true;
	} else {
		$status = false;
	}
	return $status;
}
