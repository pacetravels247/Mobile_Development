<?php
/**
 * @package    Provab Application
 * @subpackage Travel Portal
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V2
 */
abstract Class Abstract_Management_Model extends CI_Model
{
	var $markup_level;
	function __construct($markup_level) {
		$this->markup_level = $markup_level;
	}
}