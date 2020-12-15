<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Provab
 * @subpackage Notification
 * @author     Balu A<balu.provab@gmail.com>
 * @version    V1
 */
class Notification {
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->model('private_management_model');
		$this->CI->load->model('custom_db');
		$this->CI->load->model('domain_management_model');
		$this->CI->load->model('user_model');
	}
	function process_balance_request($origin, $system_request_id, $status_id, $update_remarks, $type='')
	{
		$response['status']	= SUCCESS_STATUS;
		$response['data']	= array();
		//get amount details to process - safety
		$transaction_details_cond = array('origin' => intval($origin), 'system_transaction_id' => $system_request_id, 'type' => 'b2b');
		//Depending on status update
		$transaction_details = $this->CI->custom_db->single_table_records('master_transaction_details', '*', $transaction_details_cond);
		if (valid_array($transaction_details['data']) == true && strtoupper($transaction_details['data'][0]['status']) == 'PENDING') {
			$response['data'] = $transaction_details['data'][0];
			//data to be updated
			$transaction_data = array(
							'update_remarks' => $update_remarks, 'status' => strtolower($status_id),
							'updated_datetime' => db_current_datetime(), 'updated_by_id' => intval($this->CI->entity_user_id)
			);
			$amount = ($transaction_details['data'][0]['amount']*$transaction_details['data'][0]['currency_conversion_rate']);//FORCE TO INR
			if (strtoupper($status_id) == 'ACCEPTED') {
				//Add to current balance and continue
				$domain_origin = $transaction_details['data'][0]['domain_list_fk'];
				//update balance details and notification
				
				//passing negative so balance gets deducted before processing
				$transaction_owner_id = $transaction_details['data'][0]['user_oid'];
				
				//Saving to Transaction Log
				$currency = $transaction_details['data'][0]['currency'];
				$currency_conversion_rate = $transaction_details['data'][0]['currency_conversion_rate'];
				$tr_remarks = (empty($update_remarks) == false ? trim($update_remarks) : 'Amount Deposited');
				$agent_transaction_amount = -($amount);//Dont Change
				$this->CI->domain_management_model->save_transaction_details ( 'transaction', $system_request_id, $agent_transaction_amount, 0, 0, $tr_remarks, 0,0,$currency, $currency_conversion_rate, $transaction_owner_id);
				
				//Application Logger
				$user_id = $transaction_owner_id;
				$user_condition[] = array('user_id' ,'=', $user_id);
				$user_details = $this->CI->user_model->get_user_details($user_condition);
				$agency_name = $user_details[0]['agency_name'];
				
				
				//Updating Agent Balnce with Debit Note
				if((!empty($type) == true) && ($type == 'Debit')){
					$response['data']['agent_balance'] = $this->CI->private_management_model->update_b2b_debit_balance($transaction_owner_id, $amount);
					$remarks = 'Debit Request <span class="label label-success">'.strtoupper($status_id).'</span>:'.$amount.' '.get_application_default_currency().'('.$agency_name.')';
					$admin_user_id = $this->CI->user_model->get_admin_user_id();
					$notification_users = array_merge($admin_user_id, array($user_id));
					$this->CI->application_logger->balance_debit_request($remarks, array('system_transaction_id' => $system_request_id), $notification_users);
				}
				else{
					//Updating Agent Balance
					$response['data']['agent_balance'] = $this->CI->private_management_model->update_b2b_balance($transaction_owner_id, $amount);
					$remarks = 'Deposit Request <span class="label label-success">'.strtoupper($status_id).'</span>:'.$amount.' '.get_application_default_currency().'('.$agency_name.')';
					$admin_user_id = $this->CI->user_model->get_admin_user_id();
					$notification_users = array_merge($admin_user_id, array($user_id));
					$this->CI->application_logger->balance_deposit_request($remarks, array('system_transaction_id' => $system_request_id), $notification_users);
				}
				$this->CI->custom_db->update_record('master_transaction_details', $transaction_data, $transaction_details_cond);
				
				
				
			} elseif (strtoupper($status_id) != 'ACCEPTED') {
				$this->CI->custom_db->update_record('master_transaction_details', $transaction_data, $transaction_details_cond);
			}
		} else {
			$response['status']	= FAILURE_STATUS;
		}
		return  $response;
	}
	function process_direct_credit_debit_transaction($details, $type='')
	{
		//SAVE TRANSACTION DETAILS
		$app_reference = trim($details['app_reference']);
		if(strlen($app_reference) >= 5 && strlen($app_reference) <= 20){
			$system_transaction_id = $app_reference;
		} else {
			$system_transaction_id = 'DEP-'.$this->entity_user_id.time();
		}
		$remarks = trim($details['remarks']);
		$master_transaction_details['system_transaction_id'] = $system_transaction_id;
		$master_transaction_details['domain_list_fk'] = get_domain_auth_id();
		$master_transaction_details['transaction_type'] = 'Wallet';
		$master_transaction_details['amount'] = $details['amount'];
		$master_transaction_details['currency'] = $details['currency'];
		$master_transaction_details['currency_conversion_rate'] = $details['currency_conversion_rate'];
		$master_transaction_details['date_of_transaction'] = db_current_datetime();
		$master_transaction_details['bank'] = 'N/A';
		$master_transaction_details['branch'] = 'N/A';
		$master_transaction_details['transaction_number'] = isset($details['transaction_number']) ? $details['transaction_number'] : 'N/A';
		$master_transaction_details['status'] = 'pending';
		$master_transaction_details['type'] = 'b2b';
		$master_transaction_details['user_oid'] = $details['agent_list_fk'];
		$master_transaction_details['remarks'] = $remarks;
		$master_transaction_details['created_datetime'] = db_current_datetime();
		$master_transaction_details['created_by_id'] = 1204;
		$master_transaction_details['image'] = '';

		//Set Default value due PDO connection
		$master_transaction_details['deposited_branch'] = '';
		
		$insert_id = $this->CI->custom_db->insert_record('master_transaction_details', $master_transaction_details);
		
		//UPDATE AGENT BALANCE AND SAVE INTO TRANSACTION LOG
		$insert_id = $insert_id['insert_id'];
		$status_id = 'accepted';
		$update_remarks = '';
		$update_remarks .= $details['issued_for'].'<br/>';
		$update_remarks .='Reference: '.trim($details['app_reference']).'<br/>';
		$update_remarks .=$remarks;
		$this->process_balance_request($insert_id, $system_transaction_id, $status_id, $update_remarks, $type);
	}
	public function credit_balance($agent_id, $app_reference, $credit_towards, $amount, $cancel_charge, $comments)
	{
		$post_data['app_reference'] = $app_reference;
		$post_data['amount'] = abs($amount);
		$agent_base_currency = "INR";
		$post_data['agent_list_fk'] = $agent_id;
		$post_data['remarks'] = $comments;
		$post_data['amount'] = $post_data['amount'];
		$post_data['currency'] = $agent_base_currency;
		$post_data['currency_conversion_rate'] = 1;
		$post_data['issued_for'] = 'Credited Towards: '.$credit_towards;
		//debug($post_data); exit;
		$this->process_direct_credit_debit_transaction($post_data, 'Credit');
	}
	public function package_balance($agent_id, $app_reference, $credit_towards, $amount, $cancel_charge, $comments)	
	{	
		$post_data['app_reference'] = $app_reference;	
		$post_data['amount'] = abs($amount);	
		$agent_base_currency = "INR";	
		$post_data['agent_list_fk'] = $agent_id;	
		$post_data['remarks'] = $comments;	
		$post_data['amount'] = $post_data['amount'];	
		$post_data['currency'] = $agent_base_currency;	
		$post_data['currency_conversion_rate'] = 1;	
		$post_data['issued_for'] = 'Request Towards: '.$credit_towards;	
		//debug($post_data); exit;	
		//$this->process_direct_credit_debit_transaction($post_data, 'Credit');	
		$user_condition[] = array('user_id' ,'=', $agent_id);	
		$user_details = $this->CI->user_model->get_user_details($user_condition);	
		$agency_name = $user_details[0]['agency_name'];	
					
		$remarks = 'Package amount request <span class="label label-info">'.strtoupper(Requested).'</span>:'.$amount.' '.get_application_default_currency().'('.$agency_name.')';	
		$admin_user_id = $this->CI->user_model->get_admin_user_id();	
		$notification_users = array_merge($admin_user_id, array($agent_id));	
		$this->CI->application_logger->package_balance_request($remarks, array('system_transaction_id' => $app_reference), $notification_users);	
	}	
}