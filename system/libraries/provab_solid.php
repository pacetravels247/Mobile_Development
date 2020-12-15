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
class Provab_Solid {
	private $provab_solid;
	public function __construct()
	{
		if (empty($data_list)) {
			require_once(DATATYPE_DIR.'provab_data_type.php');
			$this->provab_solid = $provab_solid;
		}
	}
	/**
	 * get data list
	 * @param $data_type name of datatype
	 */
	public function provab_solid_regexp($data_type)
	{
		return $this->provab_solid[$data_type];
	}
}
?>
