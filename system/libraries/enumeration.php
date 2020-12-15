<?php
/**
 * Provab Enumeration Class
 *
 * Handle all the Enumeration conversion in the application
 *
 * @package	Provab
 * @subpackage	provab
 * @category	Libraries
 * @author		Balu A<balu.provab@gmail.com>
 * @link		http://www.provab.com
 */
class Enumeration {
	private $enum_list;
	public function __construct()
	{
		if (empty($enum_list)) {
			require_once(ENUM_DATA_DIR.''.$GLOBALS['CI']->language_preference.'.php');
			$this->enum_list = $enums;
		}
	}

	/**
	 * get enumeration list
	 * @param $enum_key name of enumeration
	 */
	public function getEnumerationList($enum_key)
	{
		return (isset($this->enum_list[$enum_key]) ? $this->enum_list[$enum_key] : '');
	}
}
?>
