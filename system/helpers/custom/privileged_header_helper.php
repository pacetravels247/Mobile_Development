<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function get_privilege_head_summary($role, $rule_type, $rule_type_duration)
{
	$GLOBALS['CI']->load->model('user_model');
	$privilege_header_summary = $GLOBALS['CI']->user_model->get_interval_target_details($role, $rule_type, $rule_type_duration);
	return $privilege_header_summary;
}

function percentage_color_band($value)
{
	$privilege_header_color_band = '';
	switch($value) {
		case ($value > TARGET_SUCCESS_INTERVAL_START):
			$privilege_header_color_band = 'green-shade-1';// #2f9233
			break;
		case ($value > TARGET_SAFE_INTERVAL_START):
			$privilege_header_color_band = 'orange-shade-n-1';//#ffc400
			break;
		default :
			$privilege_header_color_band = 'red-shade-3';//
			break;
	}
	return $privilege_header_color_band;
}