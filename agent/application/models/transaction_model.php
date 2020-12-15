<?php
require_once 'abstract_management_model.php';
/**
 * @package    Provab Application
 * @subpackage Travel Portal
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V2
 */
Class Transaction_Model extends CI_Model
{
	/**
	 *
	 */
	function logs($condition=array(), $count=false, $offset=0, $limit=100000000000)
	{	

		$condition = $this->custom_db->get_custom_condition($condition);
		//BT, CD, ID
		if (is_domain_user()) {
			if ($count) {
				$query = 'select count(*) as total_records from transaction_log TL where TL.domain_origin='.get_domain_auth_id().' AND  TL.transaction_owner_id ='.$GLOBALS['CI']->entity_user_id.' '.$condition;
				$data = $this->db->query($query)->row_array();
				return $data['total_records'];
			} else {
				$query = 'select "INR" as currency, TL.system_transaction_id,TL.transaction_type,TL.domain_origin,TL.app_reference,
				TL.fare,TL.domain_markup as admin_markup,TL.domain_markup as profit,TL.level_one_markup as agent_markup,  TL.credit_limit as credit_limit, TL.convinence_fees as convinence_amount,TL.promocode_discount as discount,
				TL.remarks,TL.created_datetime,TL.transaction_owner_id, concat(U.first_name, " ", U.last_name) as username , agency_name as agent_name  
				from transaction_log TL LEFT JOIN user U ON TL.transaction_owner_id = U.user_id where TL.domain_origin='.get_domain_auth_id().' AND TL.transaction_owner_id ='.$GLOBALS['CI']->entity_user_id.' '.$condition.'
				order by TL.origin desc limit '.$offset.', '.$limit;
				return $this->db->query($query)->result_array();
			}
		} else {
			if ($count) {
				$query = 'select count(*) as total_records from transaction_log TL where TL.transaction_owner_id ='.$GLOBALS['CI']->entity_user_id.' '.$condition;
				$data = $this->db->query($query)->row_array();
				return $data['total_records'];
			} else {
				$query = 'select TL.system_transaction_id,TL.transaction_type,TL.domain_origin,TL.app_reference,
				TL.fare,TL.domain_markup as admin_markup,TL.domain_markup as profit,TL.level_one_markup as agent_markup, TL.credit_limit as credit_limit, TL.convinence_fees as convinence_amount,TL.promocode_discount as discount,
				TL.remarks,TL.created_datetime, concat(U.first_name, " ", U.last_name) as username , agency_name as agent_name  
				from transaction_log TL LEFT JOIN user U ON TL.transaction_owner_id=U.user_id
				WHERE TL.transaction_owner_id ='.$GLOBALS['CI']->entity_user_id.' '.$condition.' order by TL.origin desc limit '.$offset.', '.$limit;
				return $this->db->query($query)->result_array();
			}
		}
	}
}