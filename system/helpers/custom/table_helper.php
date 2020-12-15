<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
function get_table_magnifier()
{
	return '<div class="toggle-opitional-view pull-right handCursor"><span class="glyphicon glyphicon-align-justify"></span> TOGGLE</div>';
}

function get_page_magnifier()
{
	return '<div class="toggle-opitional-view handCursor"><span class="glyphicon glyphicon-th-list"></span></div>';
}

function get_account_link($datetime='', $id='', $name='')
{
	$link = '';
	if (empty($id) == true || empty($name) == true) {
		return '---';
	} else {
		if (empty($datetime) == false) {
			$datetime = explode(' ', $datetime);
			$link .= '<span class="text-warning">'.app_friendly_date($datetime[0]).' <span class="glyphicon glyphicon-time"></span></span>';
		}
		return $link.'<a role="button" href="'.base_url().'index.php/general/account?aid='.urlencode($GLOBALS['CI']->encrypt->encode($id)).'&uid='.$id.'" class="btn btn-sm">'.$name.' <span class="glyphicon glyphicon-zoom-in"></span></a>';
	}
}

/**
 * profile image
 * @param unknown_type $icon
 */
function get_profile_icon($icon, $size='', $alt='')
{
	if (empty($icon) == false) {
		switch ($size) {
			case THUMBNAIL : $image ='<img src="'.$GLOBALS['CI']->template->domain_images($icon).'" alt="'.$alt.'" width="35" height="35" class="img-rounded">';
			break;
			default : $image = '<img src="'.$GLOBALS['CI']->template->domain_images($icon).'" alt="'.$alt.'" width="75" height="75" class="img-rounded">';
			break;
		}
		return $image;
	}
}
