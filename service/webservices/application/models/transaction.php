<?php
/**
 * Library which has generic functions to get data
 *
 * @package    Provab Application
 * @subpackage Flight Model
 * @author     Arjun J<arjunjgowda260389@gmail.com>
 * @version    V2
 */
abstract Class Transaction extends CI_Model
{
	/**
	 * Lock All the tables necessary for flight transaction to be processed
	 */
	public abstract function lock_tables();

	/**
	 * Unlock All The Tables
	 */
	public static function release_locked_tables()
	{
		$CI = & get_instance();
		$CI->db->query('UNLOCK TABLES');
	}
}