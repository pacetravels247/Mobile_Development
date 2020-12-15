<?php
/**
 * Formates Transaction Log
 */
function format_transaction_log($logs)
{
		$data = array();
	$data['status'] = FAILURE_STATUS;
	$data['message'] = '';
	$data['data'] = array();
	if(valid_array($logs) == true){
		$transaction_log_data = array();
		$data['status'] = SUCCESS_STATUS;
		foreach($logs as $k => $v){
			$domain_name= $v['domain_name'];
			$transaction_date = $v['created_datetime'];
			$reference_number= $v['app_reference'];
			$transaction_type= $v['transaction_type'];
			$currency = $v['currency'];
			$debit_amount = '';
			$credit_amount = '';
			$description = $v['remarks'];
			$opening_balance = floatval($v['opening_balance']);
			$closing_balance = floatval($v['closing_balance']);
			if(is_domain_user() == true){
				$currency_conversion_rate = $v['currency_conversion_rate'];
			}
			if($transaction_type =='viator_transfer' || $transaction_type=='sightseeing'){
				
				$currency_conversion_rate =1;
				$v['currency_conversion_rate'] = 1;
			}
			$fare = floatval($v['fare']);
			$level_one_markup = floatval($v['level_one_markup']);
			$domain_markup = floatval($v['domain_markup']);
			
			//$level_one_markup = ($level_one_markup) < 0 ? abs($level_one_markup) : $level_one_markup;
			//$domain_markup = ($domain_markup) < 0 ? abs($domain_markup) : $domain_markup;
			
			$level_one_markup = ($level_one_markup);
			$domain_markup = ($domain_markup);
			
			$amount_in_admin_currency = $transaction_amount = abs($fare+$level_one_markup+$domain_markup);			
			$currency_conversion_rate = floatval($v['currency_conversion_rate']);
			//$transaction_amount = round($transaction_amount*$currency_conversion_rate, 3);
                        $transaction_amount = round($transaction_amount, 3);
                        
                        
                        
                         $mydate = '2018-01-19 15:02:00'; // DON'T REMOVE (THIS IS FOR LEDGER CONVERSTION )
                       
                        $curdate =$transaction_date;
                        if($curdate < $mydate)
                        {
                            $currency_conversion_rate = floatval($v['currency_conversion_rate']);
                            $transaction_amount = round(($transaction_amount*$currency_conversion_rate), 3);
                            $opening_balance = round(($opening_balance*$currency_conversion_rate), 3);
                            $closing_balance = round(($closing_balance*$currency_conversion_rate), 3);
                         }
                         
                         if($currency_conversion_rate==0)
                        {
                            $transaction_amount= abs($fare+$level_one_markup+$domain_markup);
                            $opening_balance=floatval($v['opening_balance']);
                            $closing_balance =floatval($v['closing_balance']);
                        }
                        /*  END */
                        
			
			$transaction_log_data[$k]['domain_name'] =				$domain_name;
			$transaction_log_data[$k]['transaction_date'] =			$transaction_date;
			$transaction_log_data[$k]['reference_number'] =			$reference_number;
			$transaction_log_data[$k]['transaction_type'] =			$transaction_type;
			$transaction_log_data[$k]['currency'] =					$currency;
			$transaction_log_data[$k]['transaction_amount'] =		$transaction_amount;
			$transaction_log_data[$k]['amount_in_admin_currency'] =	$amount_in_admin_currency;
			$transaction_log_data[$k]['description'] =				$description;
                        $transaction_log_data[$k]['created_by_id'] =				$v['created_by_id'];
		}
		$data['data'] = $transaction_log_data;
	}
	return $data;
}
/**
 * 
 * Formates Account Ledger
 * @param unknown_type $logs
 */
function format_account_ledger($logs)
{
	$data = array();
	$data['status'] = FAILURE_STATUS;
	$data['message'] = '';
	$data['data'] = array();
	if(valid_array($logs) == true){
		$transaction_log_data = array();
		$data['status'] = SUCCESS_STATUS;
		foreach($logs as $k => $v){
			//debug($v);
			$domain_name= $v['domain_name'];
			$transaction_date = $v['created_datetime'];
			$reference_number= $v['app_reference'];
			$currency = $v['currency'];
			$debit_amount = '';
			$credit_amount = '';
			$description = $v['remarks'];
			$transaction_details = $v['REF'];
			$full_description = $v['remarks']." - ".$v['REF'];
			$opening_balance = floatval($v['opening_balance']);
			$closing_balance = floatval($v['closing_balance']);
			$fare = floatval($v['fare']);
			$level_one_markup = floatval($v['level_one_markup']);
			$domain_markup = floatval($v['domain_markup']);
			
			$transaction_amount = abs($fare+$level_one_markup+$domain_markup);
            
			if($v['transaction_type']=='viator_transfer' || $v['transaction_type']=='sightseeing'){
            	
				$v['currency_conversion_rate'] = 1;
			}
			
            $currency_conversion_rate = floatval($v['currency_conversion_rate']);



			/*
 			if(is_domain_user() == true){
 				$currency_conversion_rate = floatval($v['currency_conversion_rate']);
 				$transaction_amount = round(($transaction_amount*$currency_conversion_rate), 3);
 				$opening_balance = round(($opening_balance*$currency_conversion_rate), 3);
 				$closing_balance = round(($closing_balance*$currency_conversion_rate), 3);
			}*/

			//$currency_conversion_rate = floatval($v['currency_conversion_rate']);
			//$transaction_amount = round(($transaction_amount*$currency_conversion_rate), 3);
			//$opening_balance = round(($opening_balance*$currency_conversion_rate), 3);
			//$closing_balance = round(($closing_balance*$currency_conversion_rate), 3);
                        
                        $mydate = '2018-01-19 15:02:00'; // DON'T REMOVE (THIS IS FOR LEDGER CONVERSTION )
                       
                        $curdate =$transaction_date;
                        if($curdate < $mydate)
                        {
                            $currency_conversion_rate = floatval($v['currency_conversion_rate']);
                            $transaction_amount = round(($transaction_amount*$currency_conversion_rate), 3);
                            $opening_balance = round(($opening_balance*$currency_conversion_rate), 3);
                            $closing_balance = round(($closing_balance*$currency_conversion_rate), 3);
                         }
                         
                         if($currency_conversion_rate==0)
                        {
                            $transaction_amount= abs($fare+$level_one_markup+$domain_markup);
                            $opening_balance=floatval($v['opening_balance']);
                            $closing_balance =floatval($v['closing_balance']);
                        }
                        /*  END */
			
			
			
			
			if($fare > 0){
				$debit_amount = $transaction_amount;
			} else {
				$credit_amount = $transaction_amount;
			}
			$transaction_log_data[$k]['domain_name'] =			$domain_name;
			$transaction_log_data[$k]['reference_number'] =		$reference_number;
			$transaction_log_data[$k]['transaction_date'] =		$transaction_date;
			$transaction_log_data[$k]['reference_number'] =		$reference_number;
			$transaction_log_data[$k]['currency'] =				$currency;
			$transaction_log_data[$k]['debit_amount'] =			$debit_amount;
			$transaction_log_data[$k]['credit_amount'] =		$credit_amount;
			$transaction_log_data[$k]['description'] =			$description;
			$transaction_log_data[$k]['full_description'] = $full_description;
			$transaction_log_data[$k]['transaction_details'] =	$transaction_details;
			$transaction_log_data[$k]['opening_balance'] =		$opening_balance;
			$transaction_log_data[$k]['closing_balance'] =		$closing_balance;
                        
                        
                        # Adding INR for Account Purpose
                        
                        $transaction_log_data[$k]['debit_amount_INR'] =			($debit_amount/$currency_conversion_rate);
                        $transaction_log_data[$k]['credit_amount_INR'] =		($credit_amount/$currency_conversion_rate);
                        $transaction_log_data[$k]['opening_balance_INR'] =		($opening_balance/$currency_conversion_rate);
			$transaction_log_data[$k]['closing_balance_INR'] =		($closing_balance/$currency_conversion_rate);
                    
		}
		$data['data'] = $transaction_log_data;
	}
	return $data;
}