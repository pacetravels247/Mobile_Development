<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 * @package    Provab - Provab Application
 * @subpackage Travel Portal
 * @author     Balu A<balu.provab@gmail.com> on 01-06-2015
 * @version    V2
 */

class Management extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model ( 'Custom_Db' );
	}
	public function promocode() {
		$all_post=$this->input->post();
		
		$application_default_currency = admin_base_currency();
		$currency_obj = new Currency ( array ('module_type' => 'flight','from' => admin_base_currency (),'to' => $all_post['currency']));
		
		// debug($all_post); exit();
		$condition['promo_code'] = $all_post['promocode'];
		$condition['status'] = 1;
		$promo_code_res=$this->Custom_Db->single_table_records('promo_code_list', '*', $condition );
		// debug($all_post);exit;
		if($promo_code_res['status']==1)
		{
			$promo_code=$promo_code_res['data'][0];
			if(md5($promo_code['module'])!=$all_post['moduletype'])
			{

				$result['status']=0;
				$result['error_msg']='Invalid Promo Code';
			}elseif($promo_code['expiry_date']<=date('Y-m-d') && $promo_code['expiry_date']!='0000-00-00'){
				
				$result['status']=0;
				$result['error_msg']='Promo Code Expired';
			}else{
				// echo $this->entity_user_id; exit();
				if($promo_code['module']=='car')
				{
					$booking_table = 'car_booking_details';
					
				}elseif($promo_code['module']=='hotel')
				{
					$booking_table = 'hotel_booking_details';
				}elseif($promo_code['module']=='flight')
				{
					$booking_table = 'flight_booking_details';
				}elseif ($promo_code['module']=='activities') {
					$booking_table = 'sightseeing_booking_details';
				}
				elseif ($promo_code['module']=='transfers') {
					$booking_table = 'transferv1_booking_details';
				}
				elseif ($promo_code['module']=='bus') {
					$booking_table = 'bus_booking_details';
				}

				###################################################################################
				if(is_logged_in_user()){
					$query = "SELECT BD.origin FROM payment_gateway_details AS PGD RIGHT JOIN ".$booking_table." AS BD ON PGD.app_reference = BD.app_reference WHERE BD.created_by_id='".$this->entity_user_id."' ";
				}else{
					$email = $all_post['email'];
					$query = "SELECT BD.origin FROM payment_gateway_details AS PGD RIGHT JOIN ".$booking_table." AS BD ON PGD.app_reference = BD.app_reference WHERE BD.email='".$email."' and PGD.status!='pending'";
					
				}
				###################################################################################
				
				$user_promocode_check=$this->Custom_Db->get_result_by_query($query);
				// debug($user_promocode_check);exit;
				$user_promocode_check = 0;
				if($user_promocode_check > 0){ 
				//if((($promo_code['use_type']=='single' && count($user_promocode_check)>0) || ($promo_code['use_type']=='multiple' && $promo_code['limitation']<=count($user_promocode_check))) && ($promo_code['use_type']=='multiple' && $promo_code['limitation']!=-1)){
					$result['status']=0;
					$result['error_msg']='Already used';
				}else{
					$minimum_amount = get_converted_currency_value($currency_obj->force_currency_conversion($promo_code['minimum_amount']));

					//debug($promo_code);
					// debug($all_post);exit;
					$total_amount_val_org = str_replace(',', '', $all_post['total_amount_val']);
					
					if($total_amount_val_org > $minimum_amount){
						
						if($promo_code['value_type']=='percentage'){
							$result['value']=($total_amount_val_org*round($promo_code['value']))/100;
							//$result['value'] = number_format($result['value'],2);
							$result['value'] = $result['value'];
							$result['actual_value']= number_format($result['value'],2);
						}else
						{
							$result['value']= $promo_code['value'];
							$result['actual_value']= number_format($promo_code['value'],2);
							$result['value'] = get_converted_currency_value($currency_obj->force_currency_conversion($result['value']));
							$result['value'] = $result['value'];
						}					
						if($result['value'] < $total_amount_val_org){
							$total_amount_val=($total_amount_val_org+$all_post['convenience_fee'])-$result['value'];
							
							if(isset($all_post['extra_baggage'])){
								$total_amount_val += $all_post['extra_baggage'];
							}
							if(isset($all_post['extra_meal'])){
								$total_amount_val += $all_post['extra_meal'];
							}		
							if(isset($all_post['extra_seat'])){
								$total_amount_val += $all_post['extra_seat'];
							}
							$total_amount_val=($total_amount_val>0)? $total_amount_val: 0;
							$result['total_amount_val'] = round($total_amount_val);
							// $result['value'] = sprintf("%.2f", ceil($result['value']));
							//$result['value'] = round($result['value']).'.00';
							$result['total_amount_data'] = $all_post['currency_symbol']." ".number_format($total_amount_val, 2);
							$result['convenience_fee']=$all_post['convenience_fee'];
							$result['promocode']=$all_post['promocode'];	
							$result['discount_value']= $all_post['currency_symbol']." ".number_format($result['value'],2);
							$result['module']=$all_post['moduletype'];
							$result['status']=1;
						}
						else{

							$result['status']= 0;
							$result['error_msg']='Invalid Promo Code';	
							
						}
					
					}
					else{
						// echo 'herree';exit;
						$result['status']= 0;
						$result['error_msg']='Invalid Promo Code';	
					}
				}

			}
		}
		else{
			$result['status']=$promo_code_res['status'];
			$result['error_msg']='Invalid Promo Code';
		}
		echo json_encode($result);
	}
}