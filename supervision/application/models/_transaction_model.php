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
	 * Return Convenience Fees
	 */
	function get_convenience_fees()
	{
		$data = $this->custom_db->single_table_records('convenience_fees');
		return $data['data'];
	}
	/**
	 * Update Convinence Fees
	 * @param $data array having list of data to be updated
	 */
	function update_convenience_fees($data)
	{
		foreach ($data['origin'] as $k => $v) {
			if (intval($v) > 0) {
				$cond['origin'] = intval($v);
			} else {
				continue;
			}
			$row['value'] = $data['value'][$k];
			$row['value_type'] = $data['value_type_'.$v];
			$row['per_pax'] = $data['per_pax_'.$v];
			$this->custom_db->update_record('convenience_fees', $row, $cond);
		}
	}

	/**
	 *
	 */
	function logs($condition=array(), $count=false, $offset=0, $limit=100000000000)
	{
		//BT, CD, ID
		if (is_domain_user()) {
			if ($count) {
				$query = 'select count(*) as total_records from transaction_log where domain_origin='.get_domain_auth_id();
				$data = $this->db->query($query)->row_array();
				return $data['total_records'];
			} else {
				$query = 'select "INR" as currency, TL.system_transaction_id,TL.transaction_type,TL.domain_origin,TL.app_reference,
				TL.fare,TL.domain_markup as admin_markup,TL.domain_markup as profit,TL.level_one_markup as agent_markup,TL.convinence_fees as convinence_amount,TL.promocode_discount as discount,
				TL.remarks,TL.created_datetime,TL.created_by_id, concat(U.first_name, " ", U.last_name) as username 
				from transaction_log TL LEFT JOIN user U ON TL.created_by_id=U.user_id where TL.domain_origin='.get_domain_auth_id().'
				order by TL.origin desc limit '.$offset.', '.$limit;
				return $this->db->query($query)->result_array();
			}
		} else {
			if ($count) {
				$query = 'select count(*) as total_records from transaction_log';
				$data = $this->db->query($query)->row_array();
				return $data['total_records'];
			} else {
				$query = 'select TL.system_transaction_id,TL.transaction_type,TL.domain_origin,TL.app_reference,
				TL.fare,TL.domain_markup as admin_markup,TL.domain_markup as profit,TL.level_one_markup as agent_markup,TL.convinence_fees as convinence_amount,TL.promocode_discount as discount,
				TL.remarks,TL.created_datetime, concat(U.first_name, " ", U.last_name) as username 
				from transaction_log TL LEFT JOIN user U ON TL.created_by_id=U.user_id order by TL.origin desc limit '.$offset.', '.$limit;
				return $this->db->query($query)->result_array();
			}
		}
	}
}