<?php
/**
 * @package    Provab Application
 * @subpackage Travel Portal
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V2
 */
interface Report_Model
{
	public function booking($condition, $count, $offset, $limit);
}