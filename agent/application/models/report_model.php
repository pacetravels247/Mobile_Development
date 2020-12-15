<?php
/**
 * @package    Provab Application
 * @subpackage Travel Portal
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V2
 */
Class Report_Model extends CI_Model
{
	/**
	 * Balu A 
	 */
	function auto_suggest_flight_booking_id($chars, $limit=15)
	{
		$query = 'select distinct(BD.app_reference)  from flight_booking_details AS BD
						join flight_booking_transaction_details AS TD on BD.app_reference=TD.app_reference
						WHERE BD.domain_origin='.get_domain_auth_id().' and BD.created_by_id ='.$GLOBALS['CI']->entity_user_id.' 
						and BD.app_reference!="" and (BD.app_reference like "%'.$chars.'%" OR TD.pnr like "%'.$chars.'%")
						order by BD.origin desc limit 0, '.$limit;
		return $this->db->query($query)->result_array();
	}
	/**
	 * Balu A 
	 */
	function auto_suggest_hotel_booking_id($chars, $limit=15)
	{
		$query = 'select distinct(BD.app_reference)  from hotel_booking_details AS BD
						WHERE BD.domain_origin='.get_domain_auth_id().' and BD.created_by_id ='.$GLOBALS['CI']->entity_user_id.' 
						and BD.app_reference!="" and (BD.app_reference like "%'.$chars.'%" OR BD.confirmation_reference	like "%'.$chars.'%" OR BD.booking_reference like "%'.$chars.'%")
						order by BD.origin desc limit 0, '.$limit;
		return $this->db->query($query)->result_array();
	}
	/**
	 * Balu A 
	 */
	function auto_suggest_bus_booking_id($chars, $limit=15)
	{
		$query = 'select distinct(BD.app_reference)  from bus_booking_details AS BD
						WHERE BD.domain_origin='.get_domain_auth_id().' and BD.created_by_id ='.$GLOBALS['CI']->entity_user_id.' 
						and BD.app_reference!="" and (BD.app_reference like "%'.$chars.'%" OR BD.pnr like "%'.$chars.'%")
						order by BD.origin desc limit 0, '.$limit;
		return $this->db->query($query)->result_array();
	}
}