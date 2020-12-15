<?php
if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' );
/**
 *
 * @package Provab
 * @subpackage API
 * @author Sneha Wakode <sneha.provab@gmail.com>
 * @version V1
 */
class Xlpro
{
	
	public function __construct()
	{

		$this->CI = &get_instance ();
		$this->CI->load->model('flight_model');
		$this->CI->load->model('user_model');
		$this->CI->load->model('bus_model');
		
		error_reporting(E_ALL);
	}

	function get_data_for_xlpro_flight ($record, $ticket_nos= array())
	{
		
		$result = array();
		// $record['api_token'] = 

		$i = 0;
		$round_one = array();
		// loop for whole booking data
		foreach ($record['data']['booking_details'] as $bkk => $book_d) {
			// Loop for the paxes if more than one
			$booking_detail = $this->CI->flight_model->get_booking_details_for_xl($book_d['app_reference']);
			$booking_detail = $booking_detail['data'];
			// debug($bkk);
			// debug($booking_detail);//exit();

			// For domestic data in amaudeus
			$domm = 0;
			if($booking_detail['booking_details'] [0] ['booking_source'] == 'PTBSID0000000002') {
				$domm = 1; 
			}

			$booking_token = unserialized_data($booking_detail ['booking_details'] [0] ['api_token'], md5 ( $booking_detail ['booking_details'] [0] ['api_token']));
			// debug($bkk);
			// debug($booking_detail);//exit();
			// debug($booking_token);
			// $api = $booking_detail[0]['booking_source'];
			$total_pax = count($booking_detail['booking_customer_details']);

			$prefx_val = '';
			$invoiceno = 1;
			if(!empty($booking_detail['booking_details'][0]['xl_invoice_no'])) {
				$invoiceno = $booking_detail['booking_details'][0]['xl_invoice_no'];
			}
			$booking_date = date('m/d/Y', strtotime($booking_detail['booking_details'][0]['created_datetime']));

			$client_code = ''; // C + 5char clint code as per account client master
			$client_state = '';
			$client_state_gst_code = 0;
			$disc_paidm1 = 'VL';
			$disc_paidv1 = $booking_detail['booking_details'][0]['discount'];
			$first_source = ''; // to check the duplicate airline in circle
			$first_pnr = ''; // flag to check the duplicate pnr circle

			// added new
			$flight_dtls1 = '                  ';
			$flight_dtls2 = '                  ';
			$flight_dtls3 = '                  ';
			$flight_dtls4 = '                  ';

			foreach ($booking_detail['booking_transaction_details'] as $txk => $book_trans) {
				$is_dom = ((($book_trans['is_dom'] == true) && ($domm == 0)) || (($book_trans['is_dom'] == false) && ($domm == 1))) ? 'D' : 'I';

				// parameter only for sales
				if( ($book_trans['status'] != 'BOOKING_CANCELLED') || (empty($booking_detail['cancellation_details']))) {
					$prefx_val = ((($book_trans['is_dom'] == true) && ($domm == 0)) || (($book_trans['is_dom'] == false) && ($domm == 1))) ? 'DW' : 'IW';				
				}
				$pnr = $book_trans['pnr'];

				$crs_id = 'GA';
				$client_currency = $book_trans['currency'];
				$bb ='';
				// get booking type  XO_REF and SCODE
				if($booking_detail['booking_details'][0]['booking_source'] == 'API') {
					$bb = $this->get_book_type($book_trans['booking_source']);				
				} else {
					$bb = $this->get_book_type($booking_detail['booking_details'][0]['booking_source']);
				}

				$book_type = $bb['xo'];
				$scode = $bb['scod'];
				$corp_id = 0;
				$srv_chrg1c = 0;
				$srv_chrg2c = 0;
				$srv_chrg3c = 0;
				$serv_taxc = 0;
				$serv_educ = 0;
				$serv_cs1c = 0;
				$serv1_taxc = 0;
				$serv1_educ = 0;
				$serv1_cs1c = 0;
				$serv3_taxc = 0;
				$serv3_educ = 0;
				$serv3_cs1c = 0;
				if(!empty($book_trans['fare_attributes'])) {
					$atr = json_decode($book_trans['fare_attributes'], true);
					if( $atr['selcected_corporate_id'] > 0 ) {
						$corp_id = $atr['selcected_corporate_id'];
						// get the agent client code
						$agent_info = $this->CI->user_model->get_agent_info($atr['selcected_corporate_id']);
						if(!empty($agent_info)) {
							$client_state = $agent_info['company_state'];
							$client_state_gst_code = $agent_info['company_state_gst_code'];
							$agent_code = $agent_info['xl_code'];
							if(strlen($agent_info['xl_code']) < 5){
								$agent_code = str_pad($agent_info['xl_code'], 5, "0", STR_PAD_LEFT);
							}
							$client_code = 'C'.$agent_code;					
						} else {
							//dummy client code
							$client_code = 'C'.'U0003';
						}
					}
					$supplier_currency = isset($atr['api_currency'])? $atr['api_currency']: 'INR';
					$basic_fare = 0; $tax_1 = 0; $tax_2 = 0; $tax_3 = 0; $tax_4 = 0; $tax_5 = 0; $tax_6 = 0;				
					$basic_fare = $book_trans['base_fare'];
					$tax_1 = $book_trans['YQ'];
					$tax_2 = floatval($book_trans['K3']);
					$tax_3 = floatval($book_trans['other_tax']) - floatval($book_trans['api_dis']);
					$tax_5 = floatval($book_trans['YR']);
					/*if(isset($atr['booking_base_fare'])) {
						$basic_fare = $atr['booking_base_fare'];
						$tax_1 = $atr['Fare'] - $atr['booking_base_fare'];
					} else {
						$basic_fare = $atr['total_breakup']['api_total_fare'];
						$tax_1 = $atr['total_breakup']['api_total_tax'];
						
					}*/
				} else {
					$atr = json_decode($book_trans['attributes'], true);

					if( $booking_detail['booking_details'][0]['created_by_id'] > 0 ) {
						$corp_id = $booking_detail['booking_details'][0]['created_by_id'];
						// get the agent client code
						$agent_info = $this->CI->user_model->get_agent_info($booking_detail['booking_details'][0]['created_by_id']);
						if(!empty($agent_info)) {
							$client_state = $agent_info['company_state'];
							$client_state_gst_code = $agent_info['company_state_gst_code'];
							$agent_code = $agent_info['xl_code'];
							if(strlen($agent_info['xl_code']) < 5){
								$agent_code = str_pad($agent_info['xl_code'], 5, "0", STR_PAD_LEFT);
							}
							$client_code = 'C'.$agent_code;								
						} else {
							//dummy client code
							$client_code = 'C'.'U0003';
						}
					}


					$supplier_currency = isset($atr['Fare']['Currency']) ? $atr['Fare']['Currency'] : 'INR';
					$basic_fare = 0; $tax_1 = 0; $tax_2 = 0; $tax_3 = 0; $tax_4 = 0; $tax_5 = 0; $tax_6 = 0;
					$basic_fare = $book_trans['base_fare'];
					$tax_1 = $book_trans['YQ'];
					$tax_2 = floatval($book_trans['K3']);
					$tax_3 = floatval($book_trans['other_tax']) - floatval($book_trans['api_dis']);
					$tax_5 = floatval($book_trans['YR']);
					/*$basic_fare = isset($atr['Fare']['BaseFare'])?$atr['Fare']['BaseFare']: $atr['Fare'];
					$tax_1 = isset($atr['Fare']['YQTax'])?$atr['Fare']['YQTax']:$book_trans['YQ'];
					$tax_3 = $atr['Fare']['Tax'] + $atr['Fare']['AdditionalTxnFeePub'] + $atr['Fare']['OtherCharges'] + $atr['Fare']['ServiceFee'];*/
				}
				$sec = '';
				$temp_base_fare = 0;
				$temp_mrk_fee_srv1 = 0;
				$temp_mrk_fee_srvT1 = 0;
				$temp_mrk_fee_srvE1 = 0;
				$temp_mrk_fee_srvCS1 = 0;
				$temp_mrk_fee_srv2 = 0;
				$temp_mrk_fee_srv3 = 0;
				$temp_tax_1 = 0;
				$temp_tax_2 = 0;
				$temp_tax_3 = 0;
				$temp_tax_5 = 0;
				// $airline_code = '';itenry new
				$chk = 0;
				foreach ($booking_detail['booking_itinerary_details'] as $it => $itval) {

					// markup and management fee start
					if(($corp_id > 0 )&& ($chk == 0) ) {
						// get service fees details start
						// only dcb employee
						// $fare_type = 1;
						$fare_type = 2;
						if($book_trans['fare_type'] == 'Corporate') {
							$fare_type = 2;
						}
						$stmt = "SELECT * FROM specific_markup where corporate_id = ". $corp_id . " AND  airline_code = '".$itval['airline_code'] ."' AND  fare_type = ".$fare_type."";
						$query = $this->CI->db->query($stmt);
						$res = $query->result_array();
						if(!empty($res) == true) {
							$total_tax = $tax_1 + $tax_2 + $tax_3 + $tax_4 + $tax_5 + $tax_6;
							// $this->calculate_gst_markup_fee();
							foreach ($res as $dk => $mrkdt) {
								// markup
								if($mrkdt['policy_type'] == 1) {
									switch ($mrkdt['policy_type_cat']) {
										case 1:
											// management fee
											if($mrkdt['markup_type'] == 'plus') {
												$srv_chrg1c = $mrkdt['markup_value'];
											} else {
												$srv_chrg1c = $book_trans['total_fare'] * ($mrkdt['markup_value']/100);
											}

											$dt = array('management_fee' => $srv_chrg1c);
											$cond = array('app_reference' => $book_trans['app_reference'], 'origin' => $book_trans['origin']);
											$this->CI->custom_db->update_record('flight_booking_transaction_details', $dt, $cond);

											// gst calulation
											$serv1_educ = $srv_chrg1c * 0.09;
											$serv1_cs1c = $srv_chrg1c * 0.09;
											$serv1_taxc = $srv_chrg1c * 0.18;
												
											/*// to check gst state code available
											if(!empty($client_state_gst_code)) {
												// gst calulation
												if(intval($client_state_gst_code) == 27) {
													$serv1_educ = $srv_chrg1c * 0.09;
													$serv1_cs1c = $srv_chrg1c * 0.09;
													
												} else {
													$serv1_taxc = $srv_chrg1c * 0.18;
												}
											} else {
												similar_text ( $client_state, 'maharashtra', $perc);
												// gst calulation
												if($perc > 50) {
													$serv1_educ = $srv_chrg1c * 0.09;
													$serv1_cs1c = $srv_chrg1c * 0.09;
													
												} else {
													$serv1_taxc = $srv_chrg1c * 0.18;
												}
												
											}*/

											break;
										case 2:
											// Markup on Basic											
											if($mrkdt['markup_type'] == 'plus') {
												$srv_chrg2c = $mrkdt['markup_value'];
											} else {
												$srv_chrg2c = $basic_fare * ($mrkdt['markup_value']/100);	
											}

											/*similar_text ( $client_state, 'maharashtra', $perc);
											// gst calulation
											if($perc > 50) {
												$serv_educ = $srv_chrg1c * 0.09;
												$serv_cs1c = $srv_chrg1c * 0.09;
												
											} else {
												$serv_taxc = $srv_chrg1c * 0.18;
											}*/

											break;
										case 3:
											// Markup on Tax											
											if($mrkdt['markup_type'] == 'plus') {
												$srv_chrg3c = $mrkdt['markup_value'];
											} else {
												$srv_chrg3c = $total_tax * ($mrkdt['markup_value']/100);
											}

											/*similar_text ( $client_state, 'maharashtra', $perc);
											// gst calulation
											if($perc > 50) {
												$serv1_educ = $srv_chrg1c * 0.09;
												$serv1_cs1c = $srv_chrg1c * 0.09;
												
											} else {
												$serv1_taxc = $srv_chrg1c * 0.18;
											}*/

											break;
										
										default:
											$srv_chrg1c = 0;
											$srv_chrg2c = 0;
											$srv_chrg3c = 0;
											break;
									}
								} else {
									// discount
								}
							}
						}
						// get service fees details end
					}
					// markup and management fee end

					// if(($booking_detail['booking_details'][0]['trip_type'] == 'circle') && (($itval['segment_indicator'] == 0 )||(( $itval['segment_indicator'] == 1) && ($domm == 1) && ($it ==0) ))) {
					if((($itval['segment_indicator'] == 0 )||(( $itval['segment_indicator'] >= 1) && ($domm == 1) && ($it ==0) ))) {
						$first_source = $itval['airline_code'];
						$first_pnr = $pnr;
						$cnt = count($booking_detail['booking_customer_details']);
						$temp_mrk_fee_srv1 = (!empty($srv_chrg1c)) ? ($srv_chrg1c/ $cnt) : $srv_chrg1c;
						$temp_mrk_fee_srv2 = (!empty($srv_chrg2c)) ? ($srv_chrg2c/ $cnt) : $srv_chrg2c;
						$temp_mrk_fee_srv3 = (!empty($srv_chrg3c)) ? ($srv_chrg3c/ $cnt) : $srv_chrg3c;
						$temp_mrk_fee_srvT1 = (!empty($serv1_taxc)) ? ($serv1_taxc/ $cnt) : $serv1_taxc;
						$temp_mrk_fee_srvE1 = (!empty($serv1_educ)) ? ($serv1_educ/ $cnt) : $serv1_educ;
						$temp_mrk_fee_srvCS1 = (!empty($serv1_cs1c)) ? ($serv1_cs1c/ $cnt) : $serv1_cs1c;
						$temp_base_fare = (!empty($basic_fare)) ? ($basic_fare/ $cnt) : $basic_fare;
						$temp_tax_1 = (!empty($tax_1)) ? ($tax_1/ $cnt) : $tax_1;
						$temp_tax_2 = (!empty($tax_2)) ? ($tax_2/ $cnt) : $tax_2;
						$temp_tax_3 = (!empty($tax_3)) ? ($tax_3/ $cnt) : $tax_3;
						$temp_tax_5 = (!empty($tax_5)) ? ($tax_5/ $cnt) : $tax_5;
					}
					// if(($booking_detail['booking_details'][0]['trip_type'] == 'circle') && ($itval['segment_indicator'] == 1 || $itval['segment_indicator'] == 0)) {
						if(($it == 0) || (($first_source == $itval['airline_code']) && ($first_pnr == $pnr)) ) {
							// echo "dfggd";
							$sec .= $itval['from_airport_code'].'/'.$itval['to_airport_code'].'/';

							$airline_code = $this->get_airlinecode($itval['airline_code']);

							$flt = '';
							if(strlen($itval['flight_number']) == 4) {
								$flt = $itval['flight_number'];
							} elseif (strlen($itval['flight_number']) == 3) {
								$flt = ' '.$itval['flight_number'];
							} elseif (strlen($itval['flight_number']) == 2) {
								$flt = '  '.$itval['flight_number'];
							} elseif (strlen($itval['flight_number']) == 1) {
								$flt = '   '.$itval['flight_number'];
							} else {
								$flt = '    ';
							}

							$class = ' ';
							$OK = ' ';
							$date = date('d/m/Y', strtotime($itval['departure_datetime']));
							// debug($date);
							if($it == 0){
								$flight_dtls1 = $itval['airline_code'].$flt.$class.$OK.$date;
							}
							if($it == 1) {
								$flight_dtls2 = $itval['airline_code'].$flt.$class.$OK.$date;
							}
							if($it == 2) {
								$flight_dtls3 = $itval['airline_code'].$flt.$class.$OK.$date;
							}
							if($it == 3) {
								$flight_dtls4 = $itval['airline_code'].$flt.$class.$OK.$date;
							}

							if(!empty($basic_fare)) {
								$basic_fare = round($temp_base_fare);
							}
							if(!empty($srv_chrg1c)) {
								$srv_chrg1c = round($temp_mrk_fee_srv1);
							}
							if(!empty($serv1_taxc)) {
								$serv1_taxc = round($temp_mrk_fee_srvT1);
							}
							if(!empty($serv1_educ)) {
								$serv1_educ = round($temp_mrk_fee_srvE1);
							}
							if(!empty($serv1_cs1c)) {
								$serv1_cs1c = round($temp_mrk_fee_srvCS1);
							}
							if(!empty($srv_chrg2c)) {
								$srv_chrg2c = round($temp_mrk_fee_srv2);
							}
							if(!empty($srv_chrg3c)) {
								$srv_chrg3c = round($temp_mrk_fee_srv3);
							}
							if(!empty($tax_1)) {
								$tax_1 = round($temp_tax_1);
							}
							if(!empty($tax_3)) {
								$tax_3 = round($temp_tax_3);
							}
							if(!empty($tax_2)) {
								$tax_2 = round($temp_tax_2);
							}
							if(!empty($tax_5)) {
								$tax_5 = round($temp_tax_5);
							}
						} else {

						}
					// }

					// For OneWay 
					/*if(($txk == 0 ) && ($itval['segment_indicator'] == 0)) {

						$sec .= $itval['from_airport_code'].'/'.$itval['to_airport_code'].'/';
						$airline_code = $this->get_airlinecode($itval['airline_code']);
						$flt = '';
						if(count($itval['flight_number']) == 4) {
							$flt = $itval['flight_number'];
						} elseif (count($itval['flight_number']) == 3) {
							$flt = ' '.$itval['flight_number'];
						} elseif (count($itval['flight_number']) == 2) {
							$flt = '  '.$itval['flight_number'];
						} elseif (count($itval['flight_number']) == 1) {
							$flt = '   '.$itval['flight_number'];
						} else {
							$flt = '    ';
						}
						// $class = (empty($itval['cabin_class'])) ? ' ' : $itval['cabin_class'];
						$class = ' ';
						$date = date('d/m/Y', strtotime($itval['departure_datetime']));
						// debug($date);
						if($it == 0){
							$flight_dtls1 = $itval['airline_code'].$flt.$class.$date;
						}
						if($it == 1) {
							$flight_dtls2 = $itval['airline_code'].$flt.$class.$date;
						}
						if($it == 2) {
							$flight_dtls3 = $itval['airline_code'].$flt.$class.$date;
						}
						if($it == 3) {
							$flight_dtls4 = $itval['airline_code'].$flt.$class.$date;
						}
					} elseif(($txk == 1 ) && ($itval['segment_indicator'] == 1)) {
						$sec .= $itval['from_airport_code'].'/'.$itval['to_airport_code'].'/';
						
						// $airline_code = $itval['airline_code'].$itval['flight_number'];
						$airline_code = $this->get_airlinecode($itval['airline_code']);
						$flt = '';
						if(count($itval['flight_number']) == 4) {
							$flt = $itval['flight_number'];
						} elseif (count($itval['flight_number']) == 3) {
							$flt = ' '.$itval['flight_number'];
						} elseif (count($itval['flight_number']) == 2) {
							$flt = '  '.$itval['flight_number'];
						} elseif (count($itval['flight_number']) == 1) {
							$flt = '   '.$itval['flight_number'];
						} else {
							$flt = '    ';
						}
						// $class = (empty($itval['cabin_class'])) ? ' ' : $itval['cabin_class'];
						$class = ' ';
						$date = date('d/m/Y', strtotime($itval['departure_datetime']));
						// debug($date);
						if($it == 0){
							$flight_dtls1 = $itval['airline_code'].$flt.$class.$date;
						}
						if($it == 1) {
							$flight_dtls2 = $itval['airline_code'].$flt.$class.$date;
						}
						if($it == 2) {
							$flight_dtls3 = $itval['airline_code'].$flt.$class.$date;
						}
						if($it == 3) {
							$flight_dtls4 = $itval['airline_code'].$flt.$class.$date;
						}
					}*/

					// to avoid recalculation of markup
					$chk = 1;
				} //ititnry new
				// exit();

				$sector = rtrim($sec,'/');
					foreach ($booking_detail['booking_customer_details'] as $key => $pax_v) {

						// to check gst state code available and gst params to send based on corporate state
						if(!empty($pax_v['emp_id'])) {
							$emp_dat = $this->get_employee($pax_v['emp_id']);
							// if employee does not exist use default corporate state for gst
							if(empty($emp_dat)) {

								if(!empty($client_state_gst_code)) {
									// gst calulation
									if(intval($client_state_gst_code) == 27) {
										$serv1_educ = $srv_chrg1c * 0.09;
										$serv1_cs1c = $srv_chrg1c * 0.09;
										$serv1_taxc = 0;
									} else {
										$serv1_educ = 0;
										$serv1_cs1c = 0;
										$serv1_taxc = $srv_chrg1c * 0.18;
									}
								} else {
									similar_text ( $client_state, 'maharashtra', $perc);
									// gst calulation
									if($perc > 50) {
										$serv1_educ = $srv_chrg1c * 0.09;
										$serv1_cs1c = $srv_chrg1c * 0.09;
										$serv1_taxc = 0;
									} else {
										$serv1_educ = 0;
										$serv1_cs1c = 0;
										$serv1_taxc = $srv_chrg1c * 0.18;
									}
									
								}

							} else {
								if(!empty($emp_dat[0]['company_state_gst_code'])) {
									// gst calulation
									if(intval($emp_dat[0]['company_state_gst_code']) == 27) {
										$serv1_educ = $srv_chrg1c * 0.09;
										$serv1_cs1c = $srv_chrg1c * 0.09;
										$serv1_taxc = 0;
									} else {
										$serv1_educ = 0;
										$serv1_cs1c = 0;
										$serv1_taxc = $srv_chrg1c * 0.18;
									}
								} else {
									similar_text ( $emp_dat[0]['company_state'], 'maharashtra', $perc);
									// gst calulation
									if($perc > 50) {
										$serv1_educ = $srv_chrg1c * 0.09;
										$serv1_cs1c = $srv_chrg1c * 0.09;
										$serv1_taxc = 0;
									} else {
										$serv1_educ = 0;
										$serv1_cs1c = 0;
										$serv1_taxc = $srv_chrg1c * 0.18;
									}
									
								}

							}
							
						} else {
							if(!empty($client_state_gst_code)) {
								// gst calulation
								if(intval($client_state_gst_code) == 27) {
									$serv1_educ = $srv_chrg1c * 0.09;
									$serv1_cs1c = $srv_chrg1c * 0.09;
									$serv1_taxc = 0;
								} else {
									$serv1_educ = 0;
									$serv1_cs1c = 0;
									$serv1_taxc = $srv_chrg1c * 0.18;
								}
							} else {
								similar_text ( $client_state, 'maharashtra', $perc);
								// gst calulation
								if($perc > 50) {
									$serv1_educ = $srv_chrg1c * 0.09;
									$serv1_cs1c = $srv_chrg1c * 0.09;
									$serv1_taxc = 0;
								} else {
									$serv1_educ = 0;
									$serv1_cs1c = 0;
									$serv1_taxc = $srv_chrg1c * 0.18;
								}
								
							}
						}

						// $ticket_no = (!empty($pax_v['ticket_no']))? $pax_v['ticket_no'] : $pnr.($key+1);
						$ticket_no = '';
						if((!empty($pax_v['ticket_no'])) || (!empty($pax_v['TicketId']))) {
							if(!empty($pax_v['TicketId'])) {
								$ticket_no = $pax_v['TicketId'];
							} else {
								$ticket_no = $pax_v['ticket_no'];								
							}
						} elseif (!empty($ticket_nos)) {
							$ticket_no = explode('-', $ticket_nos[$key])[1];
						} else {
							if($key == 0) {
								$ticket_no = $pnr;
							} else {
								$ticket_no = $pnr.$key;		
							}
						}
						// $ticket_no = ()?  : $pnr.($key+1);
						$pax_index = $pax_v['pax_index'] + 1;
						$pax = $pax_v['first_name'] . ' ' . $pax_v['last_name'];
						$is_adult = 0;
						$is_child = 0;
						$is_infant = 0;
						// debug($pax_v);exit;
						if(@$pax_v['passenger_type'] == 'Adult') {
							$is_adult = 1;
							$is_child = 0;
							$is_infant = 0;
						} elseif(@$pax_v['passenger_type'] == 'Child') {
							$is_adult = 0;
							$is_child = 1;
							$is_infant = 0;
						} elseif (@$pax_v['passenger_type'] == 'Infant') {
							$is_adult = 0;
							$is_child = 0;
							$is_infant = 1;
						}
						// remove comment
						$result[] = $this->format_for_xlwebpro($prefx_val, $invoiceno, $pax_index, $is_dom, $booking_date, $client_code, $airline_code, $pnr, $crs_id, $ticket_no, $pax, $is_adult, $is_child, $is_infant, $sector, $client_currency, $supplier_currency, $book_type, $flight_dtls1, $basic_fare, $tax_1, $tax_2, $tax_3, $tax_4, $tax_5, $tax_6, $flight_dtls2, $flight_dtls3, $flight_dtls4,$scode, $disc_paidm1, $disc_paidv1, $srv_chrg1c, $srv_chrg2c, $srv_chrg3c, $serv_taxc, $serv_educ, $serv_cs1c, $serv1_taxc, $serv1_educ, $serv1_cs1c, $serv3_taxc, $serv3_educ, $serv3_cs1c);
						// $this->format_for_xlwebpro($prefx_val, $invoiceno, $pax_index, $is_dom, $booking_date, $client_code, $airline_code, $pnr, $crs_id, $ticket_no, $pax, $is_adult, $is_child, $is_infant, $sector, $client_currency, $supplier_currency, $book_type, $flight_dtls1, $basic_fare, $tax_1, $tax_2, $tax_3, $tax_4, $tax_5, $tax_6, $flight_dtls2, $flight_dtls3, $flight_dtls4, $r_o_e_c, $r_o_e_s, $basic_pbl, $scode, $loc_code, $cst_code, $refr_key, $fare_basis, $deal_code);
						// debug($result);//exit;
					}
			// }
				
			} // End Loop 2
		// }
		} // End Loop 1
		// debug($result);
		// exit();
		$result['cols'] = array(
					'doc_prf' => 'DOC_PRF', // constant
					'doc_nos' => 'DOC_NOS', // invoice no 7 char num
					'doc_srno' => 'DOC_SRNO', // invoice line no upto 3 char num
					'idm_flag' => 'IDM_FLAG', // i char inter/dom
					'il_ref' => 'IL_REF', 
					'vd_ref' => 'VD_REF',
					'idate' => 'IDATE',
					'ccode' => 'CCODE',
					'dcode' => 'DCODE',
					'ecode' => 'ECODE',
					'bcode' => 'BCODE',
					'narration' => 'NARRATION', // any value 35 char
					'loc_code' => 'LOC_CODE',
					'cst_code' => 'CST_CODE',
					'curcode_c' => 'Curcode_C',
					'curcode_s' => 'Curcode_S',
					'refr_key' => 'REFR_KEY', // any refernce 10 char
					'xo_ref' => 'XO_REF', // Purchase Type : GDS, LCC Or Third Party Purchase
					'acode' => 'ACODE', //
					'scode' => 'SCODE', // SupplierCode in case of Third Party Purchase
					'xo_nos' => 'XO_NOS',
					'pnr_no' => 'PNR_NO',
					'ticketno' => 'TICKETNO',
					'pax' => 'PAX',
					'sector' => 'SECTOR',
					'crs_id' => 'CRS_ID',
					'fare_basis' => 'FARE_BASIS',
					'deal_code' => 'DEAL_CODE',
					'nos_pax_a' => 'NOS_PAX_A',
					'nos_pax_c' => 'NOS_PAX_C',
					'nos_pax_i' => 'NOS_PAX_I',
					'flt_dtls1' => 'FLT_DTLS1',
					'flt_dtls2' => 'FLT_DTLS2',
					'flt_dtls3' => 'FLT_DTLS3',
					'flt_dtls4' => 'FLT_DTLS4',
					'r_o_e_c' => 'R_O_E_C',
					'r_o_e_s' => 'R_O_E_S',
					'basic_pbl' => 'BASIC_PBL',
					'basic_fare' => 'BASIC_FARE',
					'tax_1' => 'TAX_1',
					'tax_2' => 'TAX_2',
					'tax_3' => 'TAX_3',
					'tax_4' => 'TAX_4',
					'tax_5' => 'TAX_5',
					'tax_6' => 'TAX_6',
					'disc_paidm1' => 'DISC_PAIDM1',
					'disc_paidv1' => 'DISC_PAIDV1',
					'disc_paid1' => 'DISC_PAID1',
					'disc_paidm2' => 'DISC_PAIDM2',
					'disc_paidv2' => 'DISC_PAIDV2',
					'disc_paid2' => 'DISC_PAID2',
					'disc_paidm3' => 'DISC_PAIDM3',
					'disc_paidv3' => 'DISC_PAIDV3',
					'disc_paid3' => 'DISC_PAID3',
					'disc_paidm5' => 'DISC_PAIDM5',
					'disc_paidv5' => 'DISC_PAIDV5',
					'disc_paid5' => 'DISC_PAID5',
					'tdc_paidv1' => 'TDC_PAIDV1',
					'tds_c' => 'TDS_C',
					'disc_recdm1' => 'DISC_RECDM1',
					'disc_recdv1' => 'DISC_RECDV1',
					'disc_recd1' => 'DISC_RECD1',
					'disc_recdm2' => 'DISC_RECDM2',
					'disc_recdv2' => 'DISC_RECDV2',
					'disc_recd2' => 'DISC_RECD2',
					'disc_recdm3' => 'DISC_RECDM3',
					'disc_recdv3' => 'DISC_RECDV3',
					'disc_recd3' => 'DISC_RECD3',
					'disc_recdm5' => 'DISC_RECDM5',
					'disc_recdv5' => 'DISC_RECDV5',
					'disc_recd5' => 'DISC_RECD5',
					'tds_paidv1' => 'TDS_PAIDV1',
					'tds_p' => 'TDS_P',
					'brok_paidm1' => 'BROK_PAIDM1',
					'brok_paidv1' => 'BROK_PAIDV1',
					'brok_paid1' => 'BROK_PAID1',
					'tdb_paidv1' => 'TDB_PAIDV1',
					'tds_b' => 'TDS_B',
					'srv_chrg1c' => 'SRV_CHRG1C', // Service Charges Collected From Client
					'srv_chrg1_h' => 'SRV_CHRG1_H', // Service Charges Show/Hide Flag
					'srv_chrg2c' => 'SRV_CHRG2C', // Service Charges Collected From Client
					'srv_chrg2_h' => 'SRV_CHRG2_H', // Service Charges Show/Hide Flag
					'srv_chrg3c' => 'SRV_CHRG3C', // Service Charges Collected From Client
					'srv_chrg3_h' => 'SRV_CHRG3_H', // Service Charges Show/Hide Flag
					'srv_chrg4c' => 'SRV_CHRG4C',
					'srv_chrg5c' => 'SRV_CHRG5C',
					'srv_chrg1p' => 'SRV_CHRG1P', // Service Charges Paid To Supplier
					'srv_chrg2p' => 'SRV_CHRG2P', // Service Charges Paid To Supplier
					'srv_chrg3p' => 'SRV_CHRG3P', // Service Charges Paid To Supplier
					'srv_chrg4p' => 'SRV_CHRG4P',
					'srv_chrg5p' => 'SRV_CHRG5P',
					'created_by' => 'Created_By',
					'created_on' => 'Created_On',
					'pay_type' => 'Pay_Type', // How Ticket is Purchased - Credit Purchase or using Agency CC
					'scode_b' => 'SCODE_B', // SupplierCode in case of Agency CC Purchase

					//   in case of Ticket Refund
					'xxl_c' => 'XXL_C', // Airline Cancellation Charges Collected from Client
					'raf_c' => 'RAF_C', // Extra Amount Collected from Client  on Refund
					'xxl_p' => 'XXL_P', // Airline Cancellation Charges Paid
					'raf_p' => 'RAF_P', // Extra Amount Paid To Supplier on Refund

					'serv_taxc' => 'SERV_TAXC', // IGST on Basic Colleced from Client
					'serv_educ' => 'SERV_EDUC', // CGST on Basic Colleced from Client
					'serv_cs1c' => 'SERV_CS1C', // SGST on Basic Colleced from Client
					'serv_taxp' => 'SERV_TAXP', // IGST Paid To Supplier
					'serv_edup' => 'SERV_EDUP', // CGST Paid To Supplier
					'serv_cs1p' => 'SERV_CS1P', // SGST Paid To Supplier
					'serv1_taxc' => 'SERV1_TAXC', // IGST on Service Charges Colleced  from Client
					'serv1_educ' => 'SERV1_EDUC', // CGST on Service Charges Colleced  from Client
					'serv1_cs1c' => 'SERV1_CS1C', // SGST on Service Charges Colleced  from Client
					'serv3_taxc' => 'SERV3_TAXC', // IGST on Cancellation Colleced from Client
					'serv3_educ' => 'SERV3_EDUC', // CGST on Cancellation Colleced from Client
					'serv3_cs1c' => 'SERV3_CS1C', // SGST on Cancellation Colleced from Client
					'serv3_taxp' => 'SERV3_TAXP', // IGST on Cancellation Colleced from Client
					'serv3_edup' => 'SERV3_EDUP', // CGST on Cancellation Colleced from Client
					'serv3_cs1p' => 'SERV3_CS1P', // SGST on Cancellation Colleced from Client
					'stax_payby' => 'Stax_PayBy',
					'tdc_index' => 'TDC_Index',
					'serv_cs2c' => 'SERV_CS2C',
					'serv1_cs2c' => 'SERV1_CS2C',
					'serv2_cs1c' => 'SERV2_CS1C',
					'serv2_cs2c' => 'SERV2_CS2C',
					'serv3_cs2c' => 'SERV3_CS2C',
					'serv_cs2p' => 'SERV_CS2P',
					'serv1_cs2p' => 'SERV1_CS2P',
					'serv3_cs2p' => 'SERV3_CS2P',
					'narration_5' => 'Narration_5',
					'narration_6' => 'Narration_6',
					// 'gst_type' => 'GST_TYPE',
					// 'sac_code1' => 'SAC_CODE1'
					);
		return $result;
	}

	function format_for_xlwebpro($prefx_val = '', $invoiceno, $pax_index, $is_dom, $booking_date, $client_code, $airline_code, $pnr, $crs_id, $ticket_no, $pax, $is_adult, $is_child, $is_infant, $sector, $client_currency = 'INR', $supplier_currency = 'INR', $book_type = 'E', $flight_dtls1, $basic_fare = 0, $tax_1 = 0, $tax_2 = 0, $tax_3 = 0, $tax_4 = 0, $tax_5 = 0, $tax_6 = 0, $flight_dtls2 = '', $flight_dtls3 = '', $flight_dtls4 = '', $scode ='SU0004', $disc_paidm1 = 'VL', $disc_paidv1 = 0, $srv_chrg1c = 0, $srv_chrg2c = 0, $srv_chrg3c = 0, $serv_taxc = 0, $serv_educ = 0, $serv_cs1c = 0, $serv1_taxc = 0, $serv1_educ = 0, $serv1_cs1c = 0, $serv3_taxc = 0, $serv3_educ = 0, $serv3_cs1c = 0, $r_o_e_c = 1, $r_o_e_s = 1, $basic_pbl = 0, $refr_key = '', $fare_basis = '', $deal_code = '',$loc_code = '000', $cst_code = '000' )
	{
		$result['doc_prf'] = $prefx_val; // (confirm) set as default for now change later
		$result['doc_nos'] = $invoiceno; // (confirm)
		$result['doc_srno'] = $pax_index; // invoice line no upto 3 char num
		$result['idm_flag'] = $is_dom; // i char inter/dom
		$result['il_ref'] = ''; // keep blank
		$result['vd_ref'] = ''; // Keep blank
		$result['idate'] = $booking_date; // Booking date
		$result['ccode'] = $client_code; // (confirm) C + 5char clint code as per account client master
		$result['dcode'] = ''; // keep Blank
		$result['ecode'] = ''; // keep Blank
		$result['bcode'] = ''; // keep Blank
		$result['narration'] = ''; // any value 35 char
		$result['loc_code'] = $loc_code; // (confirm) If Location is activated else 000
		$result['cst_code'] = $cst_code; // (confirm) If CostCentre is activated else 000
		$result['curcode_c'] = $client_currency; // client currency
		$result['curcode_s'] = $supplier_currency; // supplier currency
		$result['refr_key'] = ''; // any refernce 10 char
		$result['xo_ref'] = $book_type; // (Confirm) Purchase Type $result[: gds=]  ''; LCC Or Third Party Purchase
		
		$result['acode'] = $airline_code; //
		$result['scode'] = $scode; //  (confirm) SupplierCode in case of Third Party Purchase If XO_REF = C  then SCODE=Supplier Code Else Keep it Blank

		$result['xo_nos'] = ''; // Keep Blank
		$result['pnr_no'] = $pnr; // Pnr no
		$result['ticketno'] = $ticket_no;
		$result['pax'] = $pax;
		$result['sector'] = $sector;
		$result['crs_id'] = $crs_id; // AM  for Amadeus AB  for Abacus GA  for Galileo SA  for Sabre G8  for GoAir 6E  for Indigo SG  for SpiceJet

		$result['fare_basis'] = $fare_basis;
		$result['deal_code'] = $deal_code;
		$result['nos_pax_a'] = $is_adult;
		$result['nos_pax_c'] = $is_child;
		$result['nos_pax_i'] = $is_infant;
		$result['flt_dtls1'] = $flight_dtls1;
		$result['flt_dtls2'] = $flight_dtls2;
		$result['flt_dtls3'] = $flight_dtls3;
		$result['flt_dtls4'] = $flight_dtls4;
		$result['r_o_e_c'] = $r_o_e_c;
		$result['r_o_e_s'] = $r_o_e_s;
		$result['basic_pbl'] = $basic_pbl;
		$result['basic_fare'] = $basic_fare;
		$result['tax_1'] = $tax_1; // YQ Tax
		$result['tax_2'] = $tax_2; // JN Tax / K3
		$result['tax_3'] = $tax_3; // Other Taxes
		$result['tax_4'] = $tax_4; // OC Tax
		$result['tax_5'] = $tax_5; // YR Tax
		$result['tax_6'] = $tax_6;
		$result['disc_paidm1'] = $disc_paidm1; // 
		$result['disc_paidv1'] = $disc_paidv1;
		$result['disc_paid1'] = $disc_paidv1;
		$result['disc_paidm2'] = 'RBN';
		$result['disc_paidv2'] = 0;
		$result['disc_paid2'] = 0;
		$result['disc_paidm3'] = 'RBN';
		$result['disc_paidv3'] = 0;
		$result['disc_paid3'] = 0;
		$result['disc_paidm5'] = 'RDG';
		$result['disc_paidv5'] = 0;
		$result['disc_paid5'] = 0;
		$result['tdc_paidv1'] = 0;
		$result['tds_c'] = 0;
		$result['disc_recdm1'] = 'RB';
		$result['disc_recdv1'] = 0;
		$result['disc_recd1'] = 0;
		$result['disc_recdm2'] = 'RBN';
		$result['disc_recdv2'] = 0;
		$result['disc_recd2'] = 0;
		$result['disc_recdm3'] = 'RBN';
		$result['disc_recdv3'] = 0;
		$result['disc_recd3'] = 0;
		$result['disc_recdm5'] = 'RDG';
		$result['disc_recdv5'] = 0;
		$result['disc_recd5'] = 0;
		$result['tds_paidv1'] = 0;
		$result['tds_p'] = 0;
		$result['brok_paidm1'] = 'RB';
		$result['brok_paidv1'] = 0;
		$result['brok_paid1'] = 0;
		$result['tdb_paidv1'] = 0;
		$result['tds_b'] = 0;
		$result['srv_chrg1c'] = $srv_chrg1c; // Service Charges Collected From Client
		$result['srv_chrg1_h'] = 'N'; // Service Charges Show/Hide Flag
		$result['srv_chrg2c'] = $srv_chrg2c; // Service Charges Collected From Client
		$result['srv_chrg2_h'] = 'B'; // Service Charges Show/Hide Flag
		$result['srv_chrg3c'] = $srv_chrg3c; // Service Charges Collected From Client
		$result['srv_chrg3_h'] = 'T'; // Service Charges Show/Hide Flag
		$result['srv_chrg4c'] = 0;
		$result['srv_chrg5c'] = 0;
		$result['srv_chrg1p'] = 0; // Service Charges Paid To Supplier
		$result['srv_chrg2p'] = 0; // Service Charges Paid To Supplier
		$result['srv_chrg3p'] = 0; // Service Charges Paid To Supplier
		$result['srv_chrg4p'] = 0;
		$result['srv_chrg5p'] = 0;
		$result['created_by'] = '';
		$result['created_on'] = date('d/m/Y');
		$result['pay_type'] = ''; // How Ticket is Purchased - Credit Purchase or using Agency CC
		$result['scode_b'] = ''; // SupplierCode in case of Agency CC Purchase
		$result['xxl_c'] = 0; // Airline Cancellation Charges Collected from Client
		$result['raf_c'] = 0; // Extra Amount Collected from Client  on Refund
		$result['xxl_p'] = 0; // Airline Cancellation Charges Paid
		$result['raf_p'] = 0; // Extra Amount Paid To Supplier on Refund
		$result['serv_taxc'] = $serv_taxc; // IGST on Basic Colleced from Client
		$result['serv_educ'] = $serv_educ; // CGST on Basic Colleced from Client
		$result['serv_cs1c'] = $serv_cs1c; // SGST on Basic Colleced from Client
		$result['serv_taxp'] = 0; // IGST Paid To Supplier
		$result['serv_edup'] = 0; // CGST Paid To Supplier
		$result['serv_cs1p'] = 0; // SGST Paid To Supplier
		$result['serv1_taxc'] = $serv1_taxc; // IGST on Service Charges Colleced  from Client
		$result['serv1_educ'] = $serv1_educ; // CGST on Service Charges Colleced  from Client
		$result['serv1_cs1c'] = $serv1_cs1c; // SGST on Service Charges Colleced  from Client
		$result['serv3_taxc'] = $serv3_taxc; // IGST on Cancellation Colleced from Client
		$result['serv3_educ'] = $serv3_educ; // CGST on Cancellation Colleced from Client
		$result['serv3_cs1c'] = $serv3_cs1c; // SGST on Cancellation Colleced from Client
		$result['serv3_taxp'] = 0; // IGST on Cancellation Colleced from Client
		$result['serv3_edup'] = 0; // CGST on Cancellation Colleced from Client
		$result['serv3_cs1p'] = 0; // SGST on Cancellation Colleced from Client
		$result['stax_payby'] = '';
		$result['tdc_index'] = -1;
		$result['serv_cs2c'] = 0;
		$result['serv1_cs2c'] = 0;
		$result['serv2_cs1c'] = 0;
		$result['serv2_cs2c'] = 0;
		$result['serv3_cs2c'] = 0;
		$result['serv_cs2p'] = 0;
		$result['serv1_cs2p'] = 0;
		$result['serv3_cs2p'] = 0;
		$result['narration_5'] = '';
		$result['narration_6'] = '';
		// $result['gst_type'] = 'G';
		// $result['sac_code1'] = '';
		return $result;
	}

	function get_book_type($bsource)
	{
		// $dat = $this->CI->custom_db->single_table_records('booking_source', '*', array('source_id' => $bsource));
		// debug($dat);exit();
		// $name = $dat['data'][0]['name'];
		$name = $bsource;
		$ret = array();
		switch ($name) {
			case 'PTBSID00000000024': // SPICEJET_FLIGHT
			case 'PTBSID00000000023'://GOAIR_FLIGHT
			case 'PTBSID0000000014': // Indigo
			case 'PTBSID0000000022': // Indigo scrap
				$ret['xo'] = 'X';
				$ret['scod'] = '';
				break;
			case 'PTBSID0000000002': //case AMADUES- Flight
			case 'PTBSID0000000004': //AMADEUS - Flight - Crs hotel booking
				$ret['xo'] = 'B';
				$ret['scod'] = '';
				break;
			case 'PTBSID0000000007': //PROVAB_FLIGHT_BOOKING_SOURCE tbo
				$ret['xo'] = 'C';
				$ret['scod'] = 'T000O';
				break;
			default:
				$ret['xo'] = 'E';
				$ret['scod'] = '';
				break;
		}
		return $ret;
	}

	function get_airlinecode($airlinecode)
	{
		$dat = $this->CI->custom_db->single_table_records('xlpro_airlinecode', '*', array('a_code' => $airlinecode));
		if($dat['status'] == TRUE) {
			return $dat['data'][0]['a_num'];
		}
		return '';
	}

	function get_airlinecode_old($airlinecode)
	{
		$dat = $this->CI->custom_db->single_table_records('xlpro_airlinecode', '*', array('a_code' => $airlinecode));
		$air_num = '';
		if($dat['status'] == TRUE) {
			if (strlen($dat['data'][0]['a_num']) == 1) {
				$air_num = '00'.$dat['data'][0]['a_num'];
			} elseif (strlen($dat['data'][0]['a_num']) == 2) {
				$air_num = '0'.$dat['data'][0]['a_num'];
			} else {
				$air_num = $dat['data'][0]['a_num'];
			}

			return $dat['data'][0]['a_code'].$air_num;
		}
		return '';
	}

	function mssqldb_connect()
	{
		
	
		$DB2 = $GLOBALS['CI']->load->database('second_db', TRUE);
		//$DB2->insert('test',$data);
    	$details = $DB2->get('test')->result_array();
    	debug($details);
    	die();
	}

	function add_flight_booking($data)
	{
		$dbhandle = $this->mssqldb_connect();
		if($dbhandle != false) {
			$myDB = "PortalDB";
			 $cols = implode(',', $data['cols']);
			 unset($data['cols']);
			 
			 //select a database to work with
			$selected = mssql_select_db($myDB, $dbhandle)
			  or die("Couldn't open database $myDB"); 
			
			// insert record in xlpro
			  foreach ($data as $k => $xldata) {
			  	// $val = implode(',', $xldata);
			  	$st = '';
			  	$ss = array('doc_nos', 'doc_srno', 'tdc_index', 'nos_pax_a', 'nos_pax_c', 'nos_pax_i', 'r_o_e_c', 'r_o_e_s', 'basic_pbl', 'basic_fare', 'tax_1', 'tax_2', 'tax_3', 'tax_4', 'tax_5', 'tax_6', 'disc_paidv1', 'disc_paidv2', 'disc_paidv3', 'disc_recdv1', 'disc_recdv2', 'disc_recdv3', 'brok_paidv1', 'disc_paid1', 'disc_paid2', 'disc_paid3', 'disc_recd1', 'disc_recd1', 'disc_recd2', 'disc_recd3', 'brok_paid1', 'srv_chrg1c', 'srv_chrg2c', 'srv_chrg3c', 'srv_chrg4c', 'srv_chrg5c', 'raf_c', 'srv_chrg1p', 'srv_chrg2p', 'srv_chrg3p', 'srv_chrg4p', 'srv_chrg5p', 'raf_p', 'serv_taxc', 'serv_educ', 'tdc_paidv1', 'tds_c', 'serv_taxp', 'serv_edup', 'tds_paidv1', 'tds_p', 'tdb_paidv1', 'tds_b', 'xxl_c', 'xxl_p', 'serv1_taxc', 'serv1_educ', 'serv3_taxc', 'serv3_educ', 'serv3_taxp', 'serv3_edup', 'disc_paidv5', 'disc_paid5', 'disc_recdv5', 'disc_recd5', 'tax_7', 'tax_8', 'serv_cs1c', 'serv_cs2c', 'serv1_cs1c', 'serv1_cs2c', 'serv2_cs1c', 'serv2_cs2c', 'serv3_cs1c', 'serv3_cs2c', 'serv_cs1p', 'serv_cs2p', 'serv1_cs1p', 'serv1_cs2p', 'serv3_cs1p', 'serv3_cs2p');
			  	foreach ($xldata as $j => $xlda) {
			  		if(!in_array($j, $ss)) {
			  			$st .= $this->test_data($xlda).',';			  			
			  		} else {
			  			$st .= $xlda.',';
			  		}
			  	}
			  	$val = rtrim($st,',');
				  $query = "INSERT INTO xlwp6_IT 
				  			(".$cols.")
					VALUES (".$val.")";
					// echo $query;exit();
				$result = mssql_query($query,$dbhandle);
				// var_dump(mssql_get_last_message());
					// var_dump($result);exit();
			  }

			// $query .= "WHERE name='BMW'"; 

			/*// to truncate table
			$query = "TRUNCATE TABLE xlwp6_IT";
			$result = mssql_query($query,$dbhandle);
			$numRows = mssql_num_rows($result);
			var_dump(mssql_get_last_message());
			echo "<h1>" . $numRows . " Row" . ($numRows == 1 ? "" : "s") . " Returned </h1>"; */
			//execute the SQL query and return records
			// to select the data
			/*$query = "SELECT * FROM xlwp6_IT";
			// $query = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'xlwp6_IT'";
			$result = mssql_query($query,$dbhandle);
			$numRows = mssql_num_rows($result); 
			 var_dump(mssql_get_last_message());
			echo "<h1>" . $numRows . " Row" . ($numRows == 1 ? "" : "s") . " Returned </h1>"; 
			//display the results 
			while($row = mssql_fetch_array($result))
			{
			  debug($row);
			}*/
			//close the connection
			mssql_close($dbhandle);
			
		}
	}

	function delete_flight_record($nos)
	{
		$dbhandle = $this->mssqldb_connect();
		if($dbhandle != false) {
			$myDB = "PortalDB";
			 $cols = implode(',', $dat['cols']);
			 unset($dat['cols']);
			 
			 //select a database to work with
			$selected = mssql_select_db($myDB, $dbhandle)
			  or die("Couldn't open database $myDB"); 
			
			$query = "DELETE FROM xlwp6_IT WHERE DOC_NOS = ".$nos;
			$result = mssql_query($query,$dbhandle);
			// insert record in xlpro
			  
			//close the connection
			mssql_close($dbhandle);
		}
	}

	function truncate_flight_data()
	{
		$dbhandle = $this->mssqldb_connect();
		if($dbhandle != false) {
			$myDB = "PortalDB";
			 
			 //select a database to work with
			$selected = mssql_select_db($myDB, $dbhandle)
			  or die("Couldn't open database $myDB"); 
			
			$query = "TRUNCATE TABLE xlwp6_IT";
			$result = mssql_query($query,$dbhandle);
			// insert record in xlpro
			  
			//close the connection
			mssql_close($dbhandle);
		}
	}

	// get list of bookings in xlpromssql
	function show_flight_booking()
	{	
		$dbhandle = $this->mssqldb_connect();
		if($dbhandle != false) {
			$myDB = "PortalDB";
			 
			 //select a database to work with
			$selected = mssql_select_db($myDB, $dbhandle)
			  or die("Couldn't open database $myDB"); 
			$query = "SELECT * FROM xlwp6_IT";
			// $query = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'xlwp6_IT'";
			$result = mssql_query($query,$dbhandle);
			$numRows = mssql_num_rows($result); 
			 var_dump(mssql_get_last_message());
			echo "<h1>" . $numRows . " Row" . ($numRows == 1 ? "" : "s") . " Returned </h1>"; 
			//display the results 
			while($row = mssql_fetch_array($result))
			{
			  debug($row);
			}
			mssql_close($dbhandle);
		}
	}

	// get list of hotel bookings in xlpromssql
	function show_hotel_booking()
	{	
		$dbhandle = $this->mssqldb_connect();
		if($dbhandle != false) {
			$myDB = "PortalDB";
			 
			 //select a database to work with
			$selected = mssql_select_db($myDB, $dbhandle)
			  or die("Couldn't open database $myDB"); 
			$query = "SELECT * FROM xlwp6_HS";
			// $query = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'xlwp6_IT'";
			$result = mssql_query($query,$dbhandle);
			$numRows = mssql_num_rows($result); 
			 var_dump(mssql_get_last_message());
			echo "<h1>" . $numRows . " Row" . ($numRows == 1 ? "" : "s") . " Returned </h1>"; 
			//display the results 
			while($row = mssql_fetch_array($result))
			{
			  debug($row);
			}
			mssql_close($dbhandle);
		}
	}

	// get list of bookings in xlpromssql
	function show_railway_booking()
	{	
		$dbhandle = $this->mssqldb_connect();
		if($dbhandle != false) {
			$myDB = "PortalDB";
			 
			 //select a database to work with
			$selected = mssql_select_db($myDB, $dbhandle)
			  or die("Couldn't open database $myDB"); 
			// $query = "SELECT * FROM xlwp6_RS";
			$query = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'xlwp6_RS'";
			$result = mssql_query($query,$dbhandle);
			$numRows = mssql_num_rows($result); 
			 var_dump(mssql_get_last_message());
			echo "<h1>" . $numRows . " Row" . ($numRows == 1 ? "" : "s") . " Returned </h1>"; 
			//display the results 
			while($row = mssql_fetch_array($result))
			{
			  debug($row);
			}
			mssql_close($dbhandle);
		}
	}

	// get list of bookings in xlpromssql
	function get_flight_booking($invoiceno)
	{	
		$response['status']  = false;
		$response['data'] = array();
		if(!empty($invoiceno)) {

			$dbhandle = $this->mssqldb_connect();
			if($dbhandle != false) {
				$myDB = "PortalDB";
				 
				 //select a database to work with
				$selected = mssql_select_db($myDB, $dbhandle)
				  or die("Couldn't open database $myDB"); 
				$query = "SELECT * FROM xlwp6_IT WHERE DOC_NOS = ".$invoiceno;
				// $query = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'xlwp6_IT'";
				$result = mssql_query($query,$dbhandle);
				$numRows = mssql_num_rows($result);
				 // var_dump(mssql_get_last_message());
				if($numRows > 0) {
					$response['data'] = mssql_fetch_assoc($result);
				}
				mssql_close($dbhandle);
			}
		}
		return $response;
	}

	// get list of bookings in xlpromssql
	function get_flight_booking_individual($invoiceno, $name)
	{	
		$response['status']  = false;
		$response['data'] = array();
		if(!empty($invoiceno)) {

			$dbhandle = $this->mssqldb_connect();
			if($dbhandle != false) {
				$myDB = "PortalDB";
				 
				 //select a database to work with
				$selected = mssql_select_db($myDB, $dbhandle)
				  or die("Couldn't open database $myDB"); 
				$query = "SELECT * FROM xlwp6_IT WHERE DOC_NOS = " . $invoiceno . " AND PAX LIKE '" . $name . "' ";
				// $query = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'xlwp6_IT'";
				$result = mssql_query($query,$dbhandle);
				$numRows = mssql_num_rows($result);
				 // var_dump(mssql_get_last_message());
				if($numRows > 0) {
					$response['data'] = mssql_fetch_assoc($result);
				}
				mssql_close($dbhandle);
			}
		}
		return $response;
	}

	public function test_data($str)
	{
		$str = "'$str'";
		return $str;
	}

	/*generating and  formating  data for  hotel */
	public function get_data_for_xlpro_hotel($htl_data)
	{
		$booking = $htl_data['data'];
		// debug($booking);exit();
		$no_of_pax = count($booking['booking_customer_details']);

		$doc_srno = 1;
		// loop for each pax
		for($i = 0; $i < $no_of_pax; $i++) {
			$customer = $booking['booking_customer_details'][$i];
			// invouce no
			$doc_no = '';

			$agent_info = false;
			// pax sr no 
			$doc_srno = $i+1;
			$idate = date('d/m/Y', strtotime($booking['booking_details'][0]['created_datetime']));
			
			$ccode = ''; // C + 5char clint code as per account client master
			// if( $booking['booking_details'][0]['created_by_id'] > 0 ) {
			if( $customer['employee_id'] > 0 ) {
				// get the agent client code
				$agent_info = $this->get_employee_xlcode($customer['employee_id']);
				// debug($agent_info);exit();
				$agent_info = $agent_info[0];

				if(!empty($agent_info)) {
					//  remove this condition later
					if(empty($agent_info['e_xl_code'])) {
						if(empty($agent_info['c_xl_code'])) {
							$ccode = 'C'.'U0003';
						} else {
							$ccode = 'C'.$agent_info['c_xl_code'];
						}
					} else {
						$ccode = 'C'.$agent_info['e_xl_code'];
					}

				} else {
					$ccode = 'C'.'U0003';
				}
			} else {
				$ccode = 'C'.'U0003';
			}
			$doc_nos = $booking['booking_details'][0]['xlpro_invoice_no'];
			$hcode = 'H00000';
			$hcode = $this->get_hotel_code($booking['booking_details'][0]['hotel_name']);

			// if hotel paid for booking
			$scode = $hcode;

			$hotel_name = '';
			if($hcode == 'H00000') {
				$hotel_name = $booking['booking_details'][0]['hotel_name'] ;
			} elseif (empty($hcode)) {
				$hotel_name = $booking['booking_details'][0]['hotel_name'] ;
			}

			$ticketno = 'HW'.$doc_nos.$doc_srno;
			$pax = $customer['first_name'] . ' ' . $customer['last_name'];
			$check_in_date = date('d/m/Y', strtotime($booking['booking_details'][0]['hotel_check_in']));
			$check_out_date = date('d/m/Y', strtotime($booking['booking_details'][0]['hotel_check_out']));

			// as per city master tabel in xlpro
			$city_code = '000';
			$city_code = $this->get_hotel_city_code($booking['booking_details'][0]['location']);
			$room_name = $booking['booking_itinerary_details'][0]['room_type_name'];
			$room_type = $booking['booking_itinerary_details'][0]['room_type'];
			$roomtype = '000';
			

			$total_supplier = 0;
			$total_client = 0;

			// echo $room_cnt;
			$room_sgl_nos = 0;
			$room_sgl_pax = 0;
			$room_sgl_rate = 0;
			
			$room_dbl_nos = 0;
			$room_dbl_pax = 0;
			$room_dbl_rate = 0;

			$room_twn_nos = 0;
			$room_twn_pax = 0;
			$room_twn_rate = 0;
			
			$room_trp_nos = 0;
			$room_trp_pax = 0;
			$room_trp_rate = 0;
			
			$room_qad_nos = 0;
			$room_qad_pax = 0;
			$room_qad_rate = 0;

			$count = count($booking['booking_customer_details']);

			// if excel room code is not selected while update
			if(empty($room_type)) {
				$roomtype = $this->get_room_type($room_name);

				if(strcasecmp($room_name,'triple') == 0) {
					$room_trp_nos = 1;
					$room_trp_pax = 1;
					$room_trp_rate = round($booking['booking_itinerary_details'][0]['total_fare']/$count);
					$total_supplier = $room_trp_rate;
					$total_client = $room_trp_rate;
					$dat = $this->calc_hotel_management_fee($total_client, $agent_info, $booking['booking_details'][0]['hotel_code']);
					$srv_chrg1c = $dat['data']['srv_chrg1c'];
					$serv_educ = $dat['data']['serv_educ'];
					$serv_taxc = $dat['data']['serv_taxc'];
				} elseif (strcasecmp($room_name,'double') == 0) {
					$room_twn_nos = 1;
					$room_twn_pax = 1;
					$room_twn_rate = round($booking['booking_itinerary_details'][0]['total_fare']/$count);
					$total_supplier = $room_twn_rate;
					$total_client = $room_twn_rate;
					$dat = $this->calc_hotel_management_fee($total_client, $agent_info, $booking['booking_details'][0]['hotel_code']);
					$srv_chrg1c = $dat['data']['srv_chrg1c'];
					$serv_educ = $dat['data']['serv_educ'];
					$serv_taxc = $dat['data']['serv_taxc'];
				} else {
					$room_sgl_nos = 1;
					$room_sgl_pax = 1;
					$room_sgl_rate = round($booking['booking_itinerary_details'][0]['total_fare']/$count);
					$total_supplier = $room_sgl_rate;
					$total_client = $room_sgl_rate;
					$dat = $this->calc_hotel_management_fee($total_client, $agent_info, $booking['booking_details'][0]['hotel_code']);
					$srv_chrg1c = $dat['data']['srv_chrg1c'];
					$serv_educ = $dat['data']['serv_educ'];
					$serv_taxc = $dat['data']['serv_taxc'];
				}
			} else {
				$roomtype = $room_type;
				switch ($room_type) {
					case 'D01':
						$room_dbl_nos = 1;
						$room_dbl_pax = 1;
						$room_dbl_rate = round($booking['booking_itinerary_details'][0]['total_fare']/$count);
						$total_supplier = $room_dbl_rate;
						$total_client = $room_dbl_rate;
						break;
					case 'Q01':
						$room_qad_nos = 1;
						$room_qad_pax = 1;
						$room_qad_rate = round($booking['booking_itinerary_details'][0]['total_fare']/$count);
						$total_supplier = $room_qad_rate;
						$total_client = $room_qad_rate;
						break;
					case 'S01':
						$room_sgl_nos = 1;
						$room_sgl_pax = 1;
						$room_sgl_rate = round($booking['booking_itinerary_details'][0]['total_fare']/$count);
						$total_supplier = $room_sgl_rate;
						$total_client = $room_sgl_rate;
						break;
					case 'T00':
						$room_trp_nos = 1;
						$room_trp_pax = 1;
						$room_trp_rate = round($booking['booking_itinerary_details'][0]['total_fare']/$count);
						$total_supplier = $room_trp_rate;
						$total_client = $room_trp_rate;
						break;
					case 'T01':
						$room_twn_nos = 1;
						$room_twn_pax = 1;
						$room_twn_rate = round($booking['booking_itinerary_details'][0]['total_fare']/$count);
						$total_supplier = $room_twn_rate;
						$total_client = $room_twn_rate;
						break;
					default:
						// use single room code
						$room_sgl_nos = 1;
						$room_sgl_pax = 1;
						$room_sgl_rate = round($booking['booking_itinerary_details'][0]['total_fare']/$count);
						$total_supplier = $room_sgl_rate;
						$total_client = $room_sgl_rate;
						break;
				}
				// $dat = $this->calc_hotel_management_fee($total_client, $agent_info, $booking['booking_details'][0]['hotel_code']);
				$info['c_user_id'] = $agent_info['c_user_id'];
				$query = $this->CI->db->query('SELECT state_name, state_gst from crs_hotel_details where origin = '. $booking['booking_details'][0]['hotel_code']);
				$htl = $query->result_array();
				$info['gst_state_name'] = $htl[0]['state_name'];
				$info['gst_state'] = $htl[0]['state_gst'];
				$dat = $this->calc_hotel_management_fee_gst ($total_client, $info, $booking['booking_details'][0]['hotel_code']);
				$srv_chrg1c = $dat['data']['srv_chrg1c'];
				$serv_educ = $dat['data']['serv_educ'];
				$serv_taxc = $dat['data']['serv_taxc'];

			}

			$nos_pax_a = 0;
			$nos_pax_c = 0;
			if($customer['pax_type'] == 'Adult') {
				$nos_pax_a = 1;
			} elseif ($customer['pax_type'] == 'Child') {
				$nos_pax_c = 1;
			}

			$at = array('management_fee' => $srv_chrg1c);
			$condition = array('app_reference' => $booking['booking_itinerary_details'][0]['app_reference']);
			$dst =  $this->CI->custom_db->update_record('hotel_booking_itinerary_details', $at, $condition);

			$result[] = $this->format_for_xlwebpro_htl($doc_nos, $doc_srno, $idate, $ccode, $scode, $hotel_name, $ticketno, $pax, $check_in_date, $check_out_date, $city_code, $total_client, $total_supplier, $nos_pax_a, $nos_pax_c, $room_sgl_nos, $room_sgl_pax, $room_sgl_rate, $room_trp_nos, $room_trp_pax, $room_trp_rate, $room_qad_nos, $room_qad_pax, $room_qad_rate, $room_twn_nos, $room_twn_pax, $room_twn_rate, $room_dbl_nos, $room_dbl_pax, $room_dbl_rate, $roomtype, $srv_chrg1c, $serv_educ, $serv_taxc, $hcode );
		} // END LOOP
		// debug($result);
		// exit();
		$result['cols'] = array(
			'doc_prf' => 'DOC_PRF',
			'doc_nos' => 'DOC_NOS',
			'doc_srno' => 'DOC_SRNO',
			'idm_flag' => 'IDM_FLAG',
			'il_ref' => 'IL_REF',
			'vd_ref' => 'VD_REF',
			'idate' => 'IDATE',
			'ccode' => 'CCODE',
			'dcode' => 'DCODE',
			'ecode' => 'ECODE',
			'bcode' => 'BCODE',
			'narration' => 'NARRATION',
			'xo_ref' => 'XO_REF',
			'loc_code' => 'LOC_CODE',
			'cst_code' => 'CST_CODE',
			'curcode_c' => 'CURCODE_C',
			'curcode_s' => 'CURCODE_S',
			'refr_key' => 'REFR_KEY',
			'gcode' => 'GCODE',
			'hcode' => 'HCODE',
			'scode' => 'SCODE',
			'hotel_name' => 'HOTEL_NAME',
			'xo_nos' => 'XO_NOS',
			'ticketno' => 'TICKETNO',
			'pax' => 'PAX',
			'check_in_date' => 'CHECK_IN_DATE',
			'check_out_date' => 'CHECK_OUT_DATE',
			'roomview' => 'ROOMVIEW',
			'mealplan ' => 'MEALPLAN ',
			'roomtype' => 'ROOMTYPE',
			'tarifftype' => 'TARIFFTYPE',
			'pkg_code' => 'PKG_CODE',
			'city' => 'CITY',
			'room_sgl_nos' => 'ROOM_SGL_NOS',
			'room_sgl_pax' => 'ROOM_SGL_PAX',
			'room_sgl_rate' => 'ROOM_SGL_RATE',
			'room_sgl_purmth' => 'ROOM_SGL_PURMTH',
			'room_sgl_purval' => 'ROOM_SGL_PURVAL',
			'room_dbl_nos' => 'ROOM_DBL_NOS',
			'room_dbl_pax' => 'ROOM_DBL_PAX',
			'room_dbl_rate' => 'ROOM_DBL_RATE',
			'room_dbl_purmth' => 'ROOM_DBL_PURMTH',
			'room_dbl_purval' => 'ROOM_DBL_PURVAL',
			'room_twn_nos' => 'ROOM_TWN_NOS',
			'room_twn_pax' => 'ROOM_TWN_PAX',
			'room_twn_rate' => 'ROOM_TWN_RATE',
			'room_twn_purmth' => 'ROOM_TWN_PURMTH',
			'room_twn_purval' => 'ROOM_TWN_PURVAL',
			'room_trp_nos' => 'ROOM_TRP_NOS',
			'room_trp_pax' => 'ROOM_TRP_PAX',
			'room_trp_rate' => 'ROOM_TRP_RATE',
			'room_trp_purmth' => 'ROOM_TRP_PURMTH',
			'room_trp_purval' => 'ROOM_TRP_PURVAL',
			'room_qad_nos' => 'ROOM_QAD_NOS',
			'room_qad_pax' => 'ROOM_QAD_PAX',
			'room_qad_rate' => 'ROOM_QAD_RATE',
			'room_qad_purmth' => 'ROOM_QAD_PURMTH',
			'room_qad_purval' => 'ROOM_QAD_PURVAL',
			'room_adt_nos' => 'ROOM_ADT_NOS',
			'room_adt_pax' => 'ROOM_ADT_PAX',
			'room_adt_rate' => 'ROOM_ADT_RATE',
			'room_adt_purmth' => 'ROOM_ADT_PURMTH',
			'room_adt_purval' => 'ROOM_ADT_PURVAL',
			'room_chd_nos' => 'ROOM_CHD_NOS',
			'room_chd_pax' => 'ROOM_CHD_PAX',
			'room_chd_rate' => 'ROOM_CHD_RATE',
			'room_chd_purmth' => 'ROOM_CHD_PURMTH',
			'room_chd_purval' => 'ROOM_CHD_PURVAL',
			'room_cwb_nos' => 'ROOM_CWB_NOS',
			'room_cwb_pax' => 'ROOM_CWB_PAX',
			'room_cwb_rate' => 'ROOM_CWB_RATE',
			'room_cwb_purmth' => 'ROOM_CWB_PURMTH',
			'room_cwb_purval' => 'ROOM_CWB_PURVAL',
			'room_foc_nos' => 'ROOM_FOC_NOS',
			'room_foc_pax' => 'ROOM_FOC_PAX',
			'room_foc_rate' => 'ROOM_FOC_RATE',
			'room_foc_purmth' => 'ROOM_FOC_PURMTH',
			'room_foc_purval' => 'ROOM_FOC_PURVAL',
			'stx_cenvat' => 'STX_CENVAT',
			'stx_method' => 'STX_METHOD',
			'nos_pax_a' => 'NOS_PAX_A',
			'nos_pax_c' => 'NOS_PAX_C',
			'nos_pax_i' => 'NOS_PAX_I',
			'narr_1' => 'NARR_1',
			'narr_2' => 'NARR_2',
			'narr_3' => 'NARR_3',
			'narr_4' => 'NARR_4',
			'narr_5' => 'NARR_5',
			'narr_6' => 'NARR_6',
			'r_o_e_c' => 'R_O_E_C',
			'r_o_e_s' => 'R_O_E_S',
			'basic_c' => 'BASIC_C',
			'basic_s' => 'BASIC_S',
			'tax_c' => 'TAX_C',
			'tax_s' => 'TAX_S',
			'disc_paidm1' => 'DISC_PAIDM1',
			'disc_paidm2' => 'DISC_PAIDM2',
			'disc_recdm1' => 'DISC_RECDM1',
			'brok_paidm1' => 'BROK_PAIDM1',
			'disc_paidv1' => 'DISC_PAIDV1',
			'disc_recdv1' => 'DISC_RECDV1',
			'brok_paidv1' => 'BROK_PAIDV1',
			'disc_paid1' => 'DISC_PAID1',
			'disc_recd1' => 'DISC_RECD1',
			'brok_paid1' => 'BROK_PAID1',
			'srv_paidm2' => 'SRV_PAIDM2',
			'srv_chrg1c' => 'SRV_CHRG1C',
			'srv_chrg2c' => 'SRV_CHRG2C',
			'srv_chrg3c' => 'SRV_CHRG3C',
			'raf_c' => 'RAF_C',
			'srv_chrg1p' => 'SRV_CHRG1P',
			'srv_chrg2p' => 'SRV_CHRG2P',
			'srv_chrg3p' => 'SRV_CHRG3P',
			'raf_p' => 'RAF_P',
			'serv_taxc' => 'SERV_TAXC',
			'serv_educ' => 'SERV_EDUC',
			'tdc_paidv1' => 'TDC_PAIDV1',
			'tds_c' => 'TDS_C',
			'serv_taxp' => 'SERV_TAXP',
			'serv_edup' => 'SERV_EDUP',
			'tds_paidv1' => 'TDS_PAIDV1',
			'tds_p' => 'TDS_P',
			'tdb_paidv1' => 'TDB_PAIDV1',
			'tds_b' => 'TDS_B',
			'created_by' => 'CREATED_BY',
			'created_on' => 'CREATED_ON',
			);
		return $result;
	}

	public function get_data_for_xlpro_hotel_guesthouse($htl_data)
	{
		debug($htl_data);exit;
		$result = array();
		$doc_srno = 1;
		foreach ($htl_data as $ht_k => $htl_dat) {
			$doc_srno = $ht_k + 1;
			$doc_nos = $htl_dat['xlpro_invoice_no'];
			$idate = date('d-M-Y', strtotime($htl_dat['created_datetime']));
			$ccode = 'C'.'U0003';

			$emp_dat = $this->get_corporate_xlcode($htl_dat['corporate_id']);

			if(!empty($emp_dat)) {
				if(!empty($emp_dat[0]['xl_code'])) {
					$ccode = 'C' . $emp_dat[0]['xl_code'];
				}
			}

			$hcode = 'H00000';
			if(!empty( $htl_dat['xl_hotel_code'])) {
				$hcode = $htl_dat['xl_hotel_code'];
			} else {
				$hcode = $this->get_hotel_code($htl_dat['hotel_name']);
			}

			// if hotel paid for booking
			$scode = $hcode;

			$hotel_name = '';
			if($hcode == 'H00000') {
				$hotel_name = $htl_dat['hotel_name'] ;
			} elseif (empty($hcode)) {
				$hotel_name = $htl_dat['hotel_name'] ;
			}

			$ticketno = 'HW'.$doc_nos.$doc_srno;
			$pax = $htl_dat['first_name'] . ' ' . $htl_dat['last_name'];
			$check_in_date = date('d-M-Y', strtotime($htl_dat['hotel_checkin']));
			$check_out_date = date('d-M-Y', strtotime($htl_dat['hotel_checkout']));

			// as per city master tabel in xlpro
			$city_code = '000';
			$city_code = $this->get_hotel_city_code($htl_dat['city']);
			$room_type = '000';
			if(!empty($htl_dat['hotel_xl_room_type'])) {
				$room_type = $htl_dat['hotel_xl_room_type'];
			}
			$roomtype = $room_type;

			$total_supplier = 0;
			$total_client = 0;

			// echo $room_cnt;
			$room_sgl_nos = 0;
			$room_sgl_pax = 0;
			$room_sgl_rate = 0;
			
			$room_dbl_nos = 0;
			$room_dbl_pax = 0;
			$room_dbl_rate = 0;

			$room_twn_nos = 0;
			$room_twn_pax = 0;
			$room_twn_rate = 0;
			
			$room_trp_nos = 0;
			$room_trp_pax = 0;
			$room_trp_rate = 0;
			
			$room_qad_nos = 0;
			$room_qad_pax = 0;
			$room_qad_rate = 0;

			$count = count($htl_data);

			switch ($room_type) {
				case 'D01':
					$room_dbl_nos = 1;
					$room_dbl_pax = 1;
					$room_dbl_rate = round($htl_dat['total_fare']/$count);
					$total_supplier = $room_dbl_rate;
					$total_client = $room_dbl_rate;
					break;
				case 'Q01':
					$room_qad_nos = 1;
					$room_qad_pax = 1;
					$room_qad_rate = round($htl_dat['total_fare']/$count);
					$total_supplier = $room_qad_rate;
					$total_client = $room_qad_rate;
					break;
				case 'S01':
					$room_sgl_nos = 1;
					$room_sgl_pax = 1;
					$room_sgl_rate = round($htl_dat['total_fare']/$count);
					$total_supplier = $room_sgl_rate;
					$total_client = $room_sgl_rate;
					break;
				case 'T00':
					$room_trp_nos = 1;
					$room_trp_pax = 1;
					$room_trp_rate = round($htl_dat['total_fare']/$count);
					$total_supplier = $room_trp_rate;
					$total_client = $room_trp_rate;
					break;
				case 'T01':
					$room_twn_nos = 1;
					$room_twn_pax = 1;
					$room_twn_rate = round($htl_dat['total_fare']/$count);
					$total_supplier = $room_twn_rate;
					$total_client = $room_twn_rate;
					break;
				default:
					// use single room code
					$room_sgl_nos = 1;
					$room_sgl_pax = 1;
					$room_sgl_rate = round($htl_dat['total_fare']/$count);
					$total_supplier = $room_sgl_rate;
					$total_client = $room_sgl_rate;
					break;
			}
			$info['gst_state'] = $htl_dat['hotel_state_gst_no'];
			$info['c_user_id'] = $htl_dat['corporate_id'];
			$info['basic'] = $htl_dat['actual_tariff'];
			$dat = $this->calc_hotel_management_fee_gst($total_client, $info, $htl_dat['hotel_id']);
			$srv_chrg1c = $dat['data']['srv_chrg1c'];
			$serv_educ = $dat['data']['serv_educ'];
			$serv_taxc = $dat['data']['serv_taxc'];

			$at = array('management_fee' => $srv_chrg1c);
			$condition = array('request_id' => $htl_dat['app_reference'], 'id' => $htl_dat['id'] );
			$dst =  $this->CI->custom_db->update_record('subadmin_bookings', $at, $condition);
			$nos_pax_a = 1;
			$nos_pax_c = 0;
			$result[] = $this->format_for_xlwebpro_htl($doc_nos, $doc_srno, $idate, $ccode, $scode, $hotel_name, $ticketno, $pax, $check_in_date, $check_out_date, $city_code, $total_client, $total_supplier, $nos_pax_a, $nos_pax_c, $room_sgl_nos, $room_sgl_pax, $room_sgl_rate, $room_trp_nos, $room_trp_pax, $room_trp_rate, $room_qad_nos, $room_qad_pax, $room_qad_rate, $room_twn_nos, $room_twn_pax, $room_twn_rate, $room_dbl_nos, $room_dbl_pax, $room_dbl_rate, $roomtype, $srv_chrg1c, $serv_educ, $serv_taxc, $hcode );
			//debug($result);exit();
		}

		$result['cols'] = array(
			'doc_prf' => 'DOC_PRF',
			'doc_nos' => 'DOC_NOS',
			'doc_srno' => 'DOC_SRNO',
			'idm_flag' => 'IDM_FLAG',
			'il_ref' => 'IL_REF',
			'vd_ref' => 'VD_REF',
			'idate' => 'IDATE',
			'ccode' => 'CCODE',
			'dcode' => 'DCODE',
			'ecode' => 'ECODE',
			'bcode' => 'BCODE',
			'narration' => 'NARRATION',
			'xo_ref' => 'XO_REF',
			'loc_code' => 'LOC_CODE',
			'cst_code' => 'CST_CODE',
			'curcode_c' => 'CURCODE_C',
			'curcode_s' => 'CURCODE_S',
			'refr_key' => 'REFR_KEY',
			'gcode' => 'GCODE',
			'hcode' => 'HCODE',
			'scode' => 'SCODE',
			'hotel_name' => 'HOTEL_NAME',
			'xo_nos' => 'XO_NOS',
			'ticketno' => 'TICKETNO',
			'pax' => 'PAX',
			'check_in_date' => 'CHECK_IN_DATE',
			'check_out_date' => 'CHECK_OUT_DATE',
			'roomview' => 'ROOMVIEW',
			'mealplan ' => 'MEALPLAN ',
			'roomtype' => 'ROOMTYPE',
			'tarifftype' => 'TARIFFTYPE',
			'pkg_code' => 'PKG_CODE',
			'city' => 'CITY',
			'room_sgl_nos' => 'ROOM_SGL_NOS',
			'room_sgl_pax' => 'ROOM_SGL_PAX',
			'room_sgl_rate' => 'ROOM_SGL_RATE',
			'room_sgl_purmth' => 'ROOM_SGL_PURMTH',
			'room_sgl_purval' => 'ROOM_SGL_PURVAL',
			'room_dbl_nos' => 'ROOM_DBL_NOS',
			'room_dbl_pax' => 'ROOM_DBL_PAX',
			'room_dbl_rate' => 'ROOM_DBL_RATE',
			'room_dbl_purmth' => 'ROOM_DBL_PURMTH',
			'room_dbl_purval' => 'ROOM_DBL_PURVAL',
			'room_twn_nos' => 'ROOM_TWN_NOS',
			'room_twn_pax' => 'ROOM_TWN_PAX',
			'room_twn_rate' => 'ROOM_TWN_RATE',
			'room_twn_purmth' => 'ROOM_TWN_PURMTH',
			'room_twn_purval' => 'ROOM_TWN_PURVAL',
			'room_trp_nos' => 'ROOM_TRP_NOS',
			'room_trp_pax' => 'ROOM_TRP_PAX',
			'room_trp_rate' => 'ROOM_TRP_RATE',
			'room_trp_purmth' => 'ROOM_TRP_PURMTH',
			'room_trp_purval' => 'ROOM_TRP_PURVAL',
			'room_qad_nos' => 'ROOM_QAD_NOS',
			'room_qad_pax' => 'ROOM_QAD_PAX',
			'room_qad_rate' => 'ROOM_QAD_RATE',
			'room_qad_purmth' => 'ROOM_QAD_PURMTH',
			'room_qad_purval' => 'ROOM_QAD_PURVAL',
			'room_adt_nos' => 'ROOM_ADT_NOS',
			'room_adt_pax' => 'ROOM_ADT_PAX',
			'room_adt_rate' => 'ROOM_ADT_RATE',
			'room_adt_purmth' => 'ROOM_ADT_PURMTH',
			'room_adt_purval' => 'ROOM_ADT_PURVAL',
			'room_chd_nos' => 'ROOM_CHD_NOS',
			'room_chd_pax' => 'ROOM_CHD_PAX',
			'room_chd_rate' => 'ROOM_CHD_RATE',
			'room_chd_purmth' => 'ROOM_CHD_PURMTH',
			'room_chd_purval' => 'ROOM_CHD_PURVAL',
			'room_cwb_nos' => 'ROOM_CWB_NOS',
			'room_cwb_pax' => 'ROOM_CWB_PAX',
			'room_cwb_rate' => 'ROOM_CWB_RATE',
			'room_cwb_purmth' => 'ROOM_CWB_PURMTH',
			'room_cwb_purval' => 'ROOM_CWB_PURVAL',
			'room_foc_nos' => 'ROOM_FOC_NOS',
			'room_foc_pax' => 'ROOM_FOC_PAX',
			'room_foc_rate' => 'ROOM_FOC_RATE',
			'room_foc_purmth' => 'ROOM_FOC_PURMTH',
			'room_foc_purval' => 'ROOM_FOC_PURVAL',
			'stx_cenvat' => 'STX_CENVAT',
			'stx_method' => 'STX_METHOD',
			'nos_pax_a' => 'NOS_PAX_A',
			'nos_pax_c' => 'NOS_PAX_C',
			'nos_pax_i' => 'NOS_PAX_I',
			'narr_1' => 'NARR_1',
			'narr_2' => 'NARR_2',
			'narr_3' => 'NARR_3',
			'narr_4' => 'NARR_4',
			'narr_5' => 'NARR_5',
			'narr_6' => 'NARR_6',
			'r_o_e_c' => 'R_O_E_C',
			'r_o_e_s' => 'R_O_E_S',
			'basic_c' => 'BASIC_C',
			'basic_s' => 'BASIC_S',
			'tax_c' => 'TAX_C',
			'tax_s' => 'TAX_S',
			'disc_paidm1' => 'DISC_PAIDM1',
			'disc_paidm2' => 'DISC_PAIDM2',
			'disc_recdm1' => 'DISC_RECDM1',
			'brok_paidm1' => 'BROK_PAIDM1',
			'disc_paidv1' => 'DISC_PAIDV1',
			'disc_recdv1' => 'DISC_RECDV1',
			'brok_paidv1' => 'BROK_PAIDV1',
			'disc_paid1' => 'DISC_PAID1',
			'disc_recd1' => 'DISC_RECD1',
			'brok_paid1' => 'BROK_PAID1',
			'srv_paidm2' => 'SRV_PAIDM2',
			'srv_chrg1c' => 'SRV_CHRG1C',
			'srv_chrg2c' => 'SRV_CHRG2C',
			'srv_chrg3c' => 'SRV_CHRG3C',
			'raf_c' => 'RAF_C',
			'srv_chrg1p' => 'SRV_CHRG1P',
			'srv_chrg2p' => 'SRV_CHRG2P',
			'srv_chrg3p' => 'SRV_CHRG3P',
			'raf_p' => 'RAF_P',
			'serv_taxc' => 'SERV_TAXC',
			'serv_educ' => 'SERV_EDUC',
			'tdc_paidv1' => 'TDC_PAIDV1',
			'tds_c' => 'TDS_C',
			'serv_taxp' => 'SERV_TAXP',
			'serv_edup' => 'SERV_EDUP',
			'tds_paidv1' => 'TDS_PAIDV1',
			'tds_p' => 'TDS_P',
			'tdb_paidv1' => 'TDB_PAIDV1',
			'tds_b' => 'TDS_B',
			'created_by' => 'CREATED_BY',
			'created_on' => 'CREATED_ON',
			);
		return $result;
	}

	// format for hotel
	/*public function format_for_xlwebpro_htl ($doc_nos,$doc_srno,$idate,$ccode,$scode,$hotel_name,$ticketno,$pax,$check_in_date,$check_out_date,$city_code,$gcode='',$hcode='H00000',$doc_prf = 'HW',$roomview='000',$mealplan='000',$roomtype='000',$tarifftype='R') */
	public function format_for_xlwebpro_htl ($doc_nos, $doc_srno, $idate, $ccode, $scode, $hotel_name, $ticketno, $pax, $check_in_date, $check_out_date, $city_code, $basic_c, $basic_s, $nos_pax_a, $nos_pax_c, $room_sgl_nos=0, $room_sgl_pax=0, $room_sgl_rate=0, $room_trp_nos=0, $room_trp_pax=0, $room_trp_rate=0, $room_qad_nos=0, $room_qad_pax=0, $room_qad_rate=0, $room_twn_nos=0, $room_twn_pax=0, $room_twn_rate=0, $room_dbl_nos=0, $room_dbl_pax=0, $room_dbl_rate=0, $roomtype='000', $srv_chrg1c=0, $serv_educ=0, $serv_taxc=0, $hcode='H00000',$gcode='GI03HT', $doc_prf = 'HW', $roomview='000', $mealplan='000', $tarifftype='R') 
	{
		$result['doc_prf'] = $doc_prf;
		$result['doc_nos'] = $doc_nos;
		$result['doc_srno'] = $doc_srno;
		$result['idm_flag'] = 'H';
		$result['il_ref'] = '';
		$result['vd_ref'] = '';
		$result['idate'] = $idate;
		$result['ccode'] = $ccode;
		$result['dcode'] = '';
		$result['ecode'] = '';
		$result['bcode'] = '';
		$result['narration'] = '';
		$result['xo_ref'] = 'H';
		$result['loc_code'] = '000';
		$result['cst_code'] = '000';
		$result['curcode_c'] = 'INR';
		$result['curcode_s'] = 'INR';
		$result['refr_key'] = '';
		$result['gcode'] = $gcode;
		$result['hcode'] = $hcode;
		$result['scode'] = $scode;
		$result['hotel_name'] = $hotel_name;
		$result['xo_nos'] = '';
		$result['ticketno'] = $ticketno; // concatenate first 3 columns
		$result['pax'] = $pax;
		$result['check_in_date'] = $check_in_date;
		$result['check_out_date'] = $check_in_date;
		$result['roomview'] = $roomview;
		$result['mealplan'] = $mealplan;
		$result['roomtype'] = $roomtype;
		$result['tarifftype'] = $tarifftype;
		$result['pkg_code'] = '';
		$result['city'] = $city_code;
		$result['room_sgl_nos'] = $room_sgl_nos;
		$result['room_sgl_pax'] = $room_sgl_pax;
		$result['room_sgl_rate'] = $room_sgl_rate;
		$result['room_sgl_purmth'] = 'RB';
		$result['room_sgl_purval'] = 0;
		$result['room_dbl_nos'] = $room_dbl_nos;
		$result['room_dbl_pax'] = $room_dbl_pax;
		$result['room_dbl_rate'] = $room_dbl_rate;
		$result['room_dbl_purmth'] = 'RB';
		$result['room_dbl_purval'] = 0;
		$result['room_twn_nos'] = $room_twn_nos;
		$result['room_twn_pax'] = $room_twn_pax;
		$result['room_twn_rate'] = $room_twn_rate;
		$result['room_twn_purmth'] = 'RB';
		$result['room_twn_purval'] = 0;
		$result['room_trp_nos'] = $room_trp_nos;
		$result['room_trp_pax'] = $room_trp_pax;
		$result['room_trp_rate'] = $room_trp_rate;
		$result['room_trp_purmth'] = 'RB';
		$result['room_trp_purval'] = 0;
		$result['room_qad_nos'] = $room_qad_nos;
		$result['room_qad_pax'] = $room_qad_pax;
		$result['room_qad_rate'] = $room_qad_rate;
		$result['room_qad_purmth'] = 'RB';
		$result['room_qad_purval'] = 0;
		$result['room_adt_nos'] = 0;
		$result['room_adt_pax'] = 0;
		$result['room_adt_rate'] = 0;
		$result['room_adt_purmth'] = 'RB';
		$result['room_adt_purval'] = 0;
		$result['room_chd_nos'] = 0;
		$result['room_chd_pax'] = 0;
		$result['room_chd_rate'] = 0;
		$result['room_chd_purmth'] = 'RB';
		$result['room_chd_purval'] = 0;
		$result['room_cwb_nos'] = 0;
		$result['room_cwb_pax'] = 0;
		$result['room_cwb_rate'] = 0;
		$result['room_cwb_purmth'] = 'RB';
		$result['room_cwb_purval'] = 0;
		$result['room_foc_nos'] = 0;
		$result['room_foc_pax'] = 0;
		$result['room_foc_rate'] = 0;
		$result['room_foc_purmth'] = 'RB';
		$result['room_foc_purval'] = 0;
		$result['stx_cenvat'] = 'C';
		$result['stx_method'] = 'S';
		$result['nos_pax_a'] = $nos_pax_a;
		$result['nos_pax_c'] = $nos_pax_c;
		$result['nos_pax_i'] = 0;
		$result['narr_1'] = '';
		$result['narr_2'] = '';
		$result['narr_3'] = '';
		$result['narr_4'] = '';
		$result['narr_5'] = '';
		$result['narr_6'] = '';
		$result['r_o_e_c'] = 0;
		$result['r_o_e_s'] = 0;
		$result['basic_c'] = $basic_c;
		$result['basic_s'] = $basic_s;
		$result['tax_c'] = 0;
		$result['tax_s'] = 0;
		$result['disc_paidm1'] = 'RB';
		$result['disc_paidm2'] = 'N';
		$result['disc_recdm1'] = 'RB';
		$result['brok_paidm1'] = 'RB';
		$result['disc_paidv1'] = 0;
		$result['disc_recdv1'] = 0;
		$result['brok_paidv1'] = 0;
		$result['disc_paid1'] = 0;
		$result['disc_recd1'] = 0;
		$result['brok_paid1'] = 0;
		$result['srv_paidm2'] = 'N';
		$result['srv_chrg1c'] = $srv_chrg1c;
		$result['srv_chrg2c'] = 0;
		$result['srv_chrg3c'] = 0;
		$result['raf_c'] = 0;
		$result['srv_chrg1p'] = 0;
		$result['srv_chrg2p'] = 0;
		$result['srv_chrg3p'] = 0;
		$result['raf_p'] = 0;
		$result['serv_taxc'] = $serv_taxc;
		$result['serv_educ'] = $serv_educ;
		$result['tdc_paidv1'] = 0;
		$result['tds_c'] = 0;
		$result['serv_taxp'] = 0;
		$result['serv_edup'] = 0;
		$result['tds_paidv1'] = 0;
		$result['tds_p'] = 0;
		$result['tdb_paidv1'] = 0;
		$result['tds_b'] = 0;
		$result['created_by'] = '';
		$result['created_on'] = '';

		return $result;
	}

	//  calculated based on hotel state
	public function calc_hotel_management_fee_gst($price, $info, $hotel_code) 
	{
		$dat = array();
		// $dat['status'] = false;
		$dat['data']['srv_chrg1c'] = 0;
		$dat['data']['serv_taxc'] = 0;
		$dat['data']['serv_educ'] = 0;
		$dat['status'] = true;
		if(!empty($info['c_user_id'] )) {
			$stmt = "SELECT * FROM specific_markup_hotel where corporate_id = ". $info['c_user_id'] . " AND  hotel_id = ".$hotel_code ." ";
			$query = $this->CI->db->query($stmt);
			$result = $query->result_array();
		} else {
			
		}
		if($query->num_rows > 0) {
			$result = $result[0];
			if($result['management_fee_type'] == 'plus') {
				$dat['data']['srv_chrg1c'] = $result['management_fee_value'];
			} else {
				// if percentage
				$m_fee = ($price * $result['management_fee_value'])/100;
				$dat['data']['srv_chrg1c'] = $m_fee;

			} // end if

		} 
		if ( !empty($info['gst_state'])  ) {
			if($info['gst_state'] == 27) {
				$dat['data']['serv_educ'] = $info['basic'] * 0.18;
				$dat['data']['serv_taxc'] = 0;
				$dat['status'] = true;
			} else {
				$dat['data']['serv_taxc'] = $info['basic'] * 0.18;
				$dat['data']['serv_educ'] = 0;
				$dat['status'] = true;
			}

		} else {
			if( !empty($info['gst_state_name']) ) {
				similar_text ( $info['gst_state_name'], 'maharashtra', $perc);
				if($perc > 50 ) {
					$dat['data']['serv_educ'] = $info['basic'] * 0.18;
					$dat['data']['serv_taxc'] = 0;
					$dat['status'] = true;
				} else {
					$dat['data']['serv_taxc'] = $info['basic'] * 0.18;
					$dat['data']['serv_educ'] = 0;
					$dat['status'] = true;
				}
			} 
		}

		return $dat;

	}

	//  calculated based on corporate employee state 
	public function calc_hotel_management_fee ($price, $info, $hotel_code)
	{
		$dat = array();
		$dat['status'] = false;
		if(!empty($info['c_user_id'] )) {
			$stmt = "SELECT * FROM specific_markup_hotel where corporate_id = ". $info['c_user_id'] . " AND  hotel_id = ".$hotel_code ." ";
			$query = $this->CI->db->query($stmt);
			$result = $query->result_array();
		} else {
			
		}
		if($query->num_rows > 0) {
			$result = $result[0];
			if($result['management_fee_type'] == 'plus') {
				$dat['data']['srv_chrg1c'] = $result['management_fee_value'];
				if ( !empty($info['e_company_state_gst_code'])  ) {
					if($info['e_company_state_gst_code'] == 27) {
						$dat['data']['serv_educ'] = $result['management_fee_value'] * 0.18;
						$dat['data']['serv_taxc'] = 0;
						$dat['status'] = true;
					} else {
						$dat['data']['serv_taxc'] = $result['management_fee_value'] * 0.18;
						$dat['data']['serv_educ'] = 0;
						$dat['status'] = true;
					}

				} else {
					if( !empty($info['e_company_state']) ) {
						similar_text ( $info['e_company_state'], 'maharashtra', $perc);
						if($perc > 50 ) {
							$dat['data']['serv_educ'] = $result['management_fee_value'] * 0.18;
							$dat['data']['serv_taxc'] = 0;
							$dat['status'] = true;
						} else {
							$dat['data']['serv_taxc'] = $result['management_fee_value'] * 0.18;
							$dat['data']['serv_educ'] = 0;
							$dat['status'] = true;
						}
					} else {
						if(!empty($info['c_company_state_gst_code'])) {
							if($info['c_company_state_gst_code'] == 27) {
								$dat['data']['serv_educ'] = $result['management_fee_value'] * 0.18;
								$dat['data']['serv_taxc'] = 0;
								$dat['status'] = true;
							} else {
								$dat['data']['serv_taxc'] = $result['management_fee_value'] * 0.18;
								$dat['data']['serv_educ'] = 0;
								$dat['status'] = true;
							}

						} else {
							similar_text ( $info['c_company_state'], 'maharashtra', $perc);
							if($perc > 50 ) {
								$dat['data']['serv_educ'] = $result['management_fee_value'] * 0.18;
								$dat['data']['serv_taxc'] = 0;
								$dat['status'] = true;
							} else {
								$dat['data']['serv_taxc'] = $result['management_fee_value'] * 0.18;
								$dat['data']['serv_educ'] = 0;
								$dat['status'] = true;
							}
						}

					}

				}

			} else {
				// if percentage
				$m_fee = ($price * $result['management_fee_value'])/100;
				$dat['data']['srv_chrg1c'] = $m_fee;
				if ( !empty($info['e_company_state_gst_code'])  ) {
					if($info['e_company_state_gst_code'] == 27) {
						$dat['data']['serv_educ'] = $m_fee * 0.18;
						$dat['data']['serv_taxc'] = 0;
						$dat['status'] = true;
					} else {
						$dat['data']['serv_taxc'] = $m_fee * 0.18;
						$dat['data']['serv_educ'] = 0;
						$dat['status'] = true;
					}

				} else {
					if( !empty($info['e_company_state']) ) {
						similar_text ( $info['e_company_state'], 'maharashtra', $perc);
						if($perc > 50 ) {
							$dat['data']['serv_educ'] = $m_fee * 0.18;
							$dat['data']['serv_taxc'] = 0;
							$dat['status'] = true;
						} else {
							$dat['data']['serv_taxc'] = $m_fee * 0.18;
							$dat['data']['serv_educ'] = 0;
							$dat['status'] = true;
						}
					} else {
						if(!empty($info['c_company_state_gst_code'])) {
							if($info['c_company_state_gst_code'] == 27) {
								$dat['data']['serv_educ'] = $m_fee * 0.18;
								$dat['data']['serv_taxc'] = 0;
								$dat['status'] = true;
							} else {
								$dat['data']['serv_taxc'] = $m_fee * 0.18;
								$dat['data']['serv_educ'] = 0;
								$dat['status'] = true;
							}

						} else {
							similar_text ( $info['c_company_state'], 'maharashtra', $perc);
							if($perc > 50 ) {
								$dat['data']['serv_educ'] = $m_fee * 0.18;
								$dat['data']['serv_taxc'] = 0;
								$dat['status'] = true;
							} else {
								$dat['data']['serv_taxc'] = $m_fee * 0.18;
								$dat['data']['serv_educ'] = 0;
								$dat['status'] = true;
							}
						}

					}

				}

			} // end if

		} else {
			$dat['data']['srv_chrg1c'] = 0;
			$dat['data']['serv_taxc'] = 0;
			$dat['data']['serv_educ'] = 0;
			$dat['status'] = true;
		}

		return $dat;
		
	}

	private function get_hotel_code($hotel_name)
	{
		$code = 'H00000';
		$stmt = "SELECT * FROM  xlpro_hotel_master where hotel_name LIKE ". $this->CI->db->escape($hotel_name.'%');
		$query = $this->CI->db->query($stmt);
		if($query->num_rows() >0 ) {
			$code = 'H'.$query->result_array()[0]['hotel_code'];
		}
		return $code;
	}

	private function get_hotel_city_code($hotel_city)
	{
		$code = '000';
		$hotel_city = trim($hotel_city);
		if( preg_match('/.\(/', $hotel_city) > 0) {
			$hotel_city = trim(explode('(', $hotel_city)[0]);
		}
		$stmt = "SELECT * FROM xlpro_hotel_city where name LIKE ". $this->CI->db->escape('%'.$hotel_city);
		$query = $this->CI->db->query($stmt);

		if($query->num_rows() >0 ) {
			$code = $query->result_array()[0]['code'];
		}
		return $code;
	}

	private function get_room_type($room_name) 
	{
		$code = '000';
		$room_name = trim($room_name);
		$stmt = "SELECT * FROM xlpro_hotel_room_type where name LIKE ". $this->CI->db->escape($room_name);
		$query = $this->CI->db->query($stmt);

		if($query->num_rows() >0 ) {
			$code = $query->result_array()[0]['code'];
		}
		return $code;
	}

	public function  add_hotel_booking($data)
	{
		$dbhandle = $this->mssqldb_connect();
		if($dbhandle != false) {
			$myDB = "PortalDB";
			 $cols = implode(',', $data['cols']);
			 unset($data['cols']);
			 
			 //select a database to work with
			$selected = mssql_select_db($myDB, $dbhandle)
			  or die("Couldn't open database $myDB"); 
			// debug($selected);exit();
			// insert record in xlpro
			  foreach ($data as $k => $xldata) {
			  	debug($xldata);
			  	// $val = implode(',', $xldata);
			  	$st = '';
			  	$ss = array('doc_prf', 'idm_flag', 'il_ref', 'vd_ref', 'idate', 'ccode', 'dcode', 'ecode', 'bcode', 'narration', 'xo_ref', 'loc_code', 'cst_code', 'curcode_c', 'curcode_s', 'refr_key', 'gcode', 'hcode', 'scode', 'hotel_name', 'xo_nos', 'ticketno', 'pax', 'check_in_date', 'check_out_date', 'roomview', 'mealplan ', 'roomtype', 'tarifftype', 'pkg_code', 'city','room_sgl_purmth', 'room_dbl_purmth', 'room_twn_purmth', 'room_trp_purmth', 'room_qad_purmth', 'room_adt_purmth', 'room_chd_purmth', 'room_cwb_purmth', 'room_foc_purmth', 'stx_cenvat', 'stx_method', 'narr_1', 'narr_2', 'narr_3', 'narr_4', 'narr_5', 'narr_6', 'disc_paidm1', 'disc_paidm2', 'disc_recdm1', 'brok_paidm1', 'srv_paidm2', 'created_by', 'created_on');
			  	foreach ($xldata as $j => $xlda) {
			  		if(in_array($j, $ss)) {
			  			$st .= $this->test_data($xlda).',';			  			
			  		} else {
			  			$st .= $xlda.',';
			  		}
			  	}
			  	$val = rtrim($st,',');
				  $query = "INSERT INTO xlwp6_HS 
				  			(".$cols.")
					VALUES (".$val.")";
					echo $query;//exit();
				$result = mssql_query($query,$dbhandle);

echo "<br/>";
				var_dump(mssql_get_last_message());
echo "<br/>";
					var_dump($result);exit();
			  } // end loop

			
				/*// execute to get the list of records
				$query = 'select * from xlwp6_HS';
				$result = mssql_query($query, $dbhandle);

				$numRows = mssql_num_rows($result); 
				 var_dump(mssql_get_last_message());
				echo "<h1>" . $numRows . " Row" . ($numRows == 1 ? "" : "s") . " Returned </h1>";

				while($row = mssql_fetch_array($result))
				{
				  debug($row);
				} 
				exit;*/

				
				/*// Truncate table
				$query = "TRUNCATE TABLE xlwp6_HS";
				$result = mssql_query($query,$dbhandle);
				$numRows = mssql_num_rows($result);
				var_dump(mssql_get_last_message());
				echo "<h1>" . $numRows . " Row" . ($numRows == 1 ? "" : "s") . " Returned </h1>";*/
			//close the connection
			mssql_close($dbhandle);
			
		}

		/*
		to get hotel structure
		$dbhandle = $this->mssqldb_connect();
		if($dbhandle != false) {
			$myDB = "PortalDB";
			 //select a database to work with
			$selected = mssql_select_db($myDB, $dbhandle)
			  or die("Couldn't open database $myDB");

			$query = 'select * from INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = "xlwp6_HS"';
			$result = mssql_query($query, $dbhandle);

			$numRows = mssql_num_rows($result); 
			 var_dump(mssql_get_last_message());
			echo "<h1>" . $numRows . " Row" . ($numRows == 1 ? "" : "s") . " Returned </h1>";

			while($row = mssql_fetch_array($result))
			{
			  debug($row);
			} 

				
		}*/

	}

	public function add_railway_booking($data)
	{
		$dbhandle = $this->mssqldb_connect();
		if($dbhandle != false) {
			$myDB = "PortalDB";
			 $cols = implode(',', $data['cols']);
			 unset($data['cols']);
			 
			 //select a database to work with
			$selected = mssql_select_db($myDB, $dbhandle)
			  or die("Couldn't open database $myDB"); 
			// debug($selected);exit();
			// insert record in xlpro
			  foreach ($data as $k => $xldata) {
			  	// $val = implode(',', $xldata);
			  	$st = '';
			  	// non numeric data
			  	$ss = array('doc_prf', 'doc_nos', 'doc_srno', 'idm_flag', 'il_ref', 'vd_ref', 'idate', 'ccode', 'dcode', 'ecode', 'bcode', 'narration', 'xo_ref', 'loc_code', 'cst_code', 'curcode_c', 'curcode_s', 'refr_key', 'gcode', 'hcode', 'scode', 'xo_nos', 'ticketno', 'pnr_no', 'pax', 'sector', 'book_class', 'train_no', 'journey_date', 'brok_paidm1', 'created_by', 'created_on', 'narration_5', 'narration_6');
			  	foreach ($xldata as $j => $xlda) {
			  		if(in_array($j, $ss)) {
			  			$st .= $this->test_data($xlda).',';			  			
			  		} else {
			  			$st .= $xlda.',';
			  		}
			  	}
			  	$val = rtrim($st,',');
				  $query = "INSERT INTO xlwp6_RS 
				  			(".$cols.")
					VALUES (".$val.")";
					// echo $query;exit();
				$result = mssql_query($query,$dbhandle);


				// var_dump(mssql_get_last_message());
					// var_dump($result);exit();
			  } // end loop

			
				/*// execute to get the list of records
				$query = 'select * from xlwp6_RS';
				$result = mssql_query($query, $dbhandle);

				$numRows = mssql_num_rows($result); 
				 var_dump(mssql_get_last_message());
				echo "<h1>" . $numRows . " Row" . ($numRows == 1 ? "" : "s") . " Returned </h1>";

				while($row = mssql_fetch_array($result))
				{
				  debug($row);
				} 
				exit;*/

				
				/*// Truncate table
				$query = "TRUNCATE TABLE xlwp6_RS";
				$result = mssql_query($query,$dbhandle);
				$numRows = mssql_num_rows($result);
				var_dump(mssql_get_last_message());
				echo "<h1>" . $numRows . " Row" . ($numRows == 1 ? "" : "s") . " Returned </h1>";*/
			//close the connection
			mssql_close($dbhandle);
			
		}

		/*
		to get hotel structure
		$dbhandle = $this->mssqldb_connect();
		if($dbhandle != false) {
			$myDB = "PortalDB";
			 //select a database to work with
			$selected = mssql_select_db($myDB, $dbhandle)
			  or die("Couldn't open database $myDB");

			$query = 'select * from INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = "xlwp6_HS"';
			$result = mssql_query($query, $dbhandle);

			$numRows = mssql_num_rows($result); 
			 var_dump(mssql_get_last_message());
			echo "<h1>" . $numRows . " Row" . ($numRows == 1 ? "" : "s") . " Returned </h1>";

			while($row = mssql_fetch_array($result))
			{
			  debug($row);
			} 

				
		}*/

	}

	public function add_bus_booking()
	{
		// bus
		/*$dbhandle = $this->mssqldb_connect();
		if($dbhandle != false) {
			$myDB = "PortalDB";
			 //select a database to work with
			$selected = mssql_select_db($myDB, $dbhandle)
			  or die("Couldn't open database $myDB");

			$query = 'select * from INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = "xlwp6_MS"';
			$result = mssql_query($query, $dbhandle);

			$numRows = mssql_num_rows($result); 
			 var_dump(mssql_get_last_message());
			echo "<h1>" . $numRows . " Row" . ($numRows == 1 ? "" : "s") . " Returned </h1>";

			while($row = mssql_fetch_array($result))
			{
			  debug($row);
			} 

				
		}*/
	}

	function get_data_for_xlpro_misc ($booking, $module = 'bus')
	{
		$result = array();
		// $bs_data = $bs_data['data'];
		if(!empty($booking)) {
			// foreach ($bs_data as $bk => $booking) {
				// debug($booking);//exit();
				$doc_prf = 'MW';

				// remove condition later
				$doc_nos = ($booking['booking_details']['xl_invoice_no'])?$booking['booking_details']['xl_invoice_no']:1;
				$idm_flag = 'M';
				$idate = date('d/m/Y', strtotime($booking['booking_details']['created_datetime']));
				$xo_ref = '';
				$gcode = '';
				$srv_date = '';
				$ticket_no = '';
				$ccode = ''; // C + 5char clint code as per account client master
				$booking_created_by = 0;

				 // change supplier code later for bus
				$scode = 'SU0004';

				if($module == 'bus') {
					$xo_ref = 'B';
					$gcode = 'GI03BS';
					$srv_date = date('d/m/Y', strtotime($booking['booking_itinerary_details'][0]['journey_datetime']));
					$ticket_no = $booking['booking_details']['pnr'];
					$booking_created_by = $booking['booking_details']['created_by_id'];
				} elseif ($module == 'hotel') {
					$xo_ref = 'H';
					$gcode = 'GI03HT';
					$srv_date = date('d/m/Y', strtotime($booking['booking_details'][0]['hotel_check_in']));
					$ticket_no = $booking['booking_details'][0]['booking_id'];
					$booking_created_by = $booking['booking_details'][0]['booked_by_id'];
					$scd = '';
					$scd = $this->get_supplier_code($booking['booking_details'][0]['booking_source']);
					$scode = 'S'.$scd;
				}

				if( $booking_created_by > 0 ) {
					// get the agent client code
					$agent_info = $this->CI->user_model->get_agent_info($booking_created_by);				
					if(!empty($agent_info)) {
						//  remove this condition later
						if(empty($agent_info['xl_code'])) {
							$client_code = 'C'.'U0003';
						} else {
							$ccode = 'C'.$agent_info['xl_code'];
						}

					}
				} else {
					// b2c get from client
					$client_code = 'C'.'U0003';
				}

				if($module == 'bus') {
					foreach ($booking['booking_customer_details'] as $ck => $cust) {
						$doc_srno = $ck+1;
						$pax = $cust['name'];
						$basic_c = $cust['fare'];
						$basic_s = $cust['fare'];
						$tax_c = 0;
						$tax_s = 0;

						$nos_pax_a = 0;
						$nos_pax_c = 0;
						$nos_pax_i = 0;

						if($cust['age'] > 11) {
							$nos_pax_a = 1;
						} elseif ($cust['age'] > 2) {
							$nos_pax_c = 1;
						} else {
							$nos_pax_i = 1;
						}

						if(!empty($agent_info['corporate_id'])) {
							$corp_id = $agent_info['corporate_id'];
						} else {
							$corp_id = $agent_info['created_by_id'];
						}

						// calculate and add management fee
						$stmt = "SELECT * FROM specific_markup_bus where corporate_id = ". $corp_id ;
						$query = $this->CI->db->query($stmt);
						$res = $query->result_array();
						$srv_chrg1c = 0;
						$serv_taxc = 0;

						$serv_educ = 0;
						$serv_cs1c = 0;

						$crp_dat = $this->get_corporate_xlcode($corp_id);
						if(!empty($res)) {
							$res = $res[0];
							if( $res['management_fee_type'] == 'plus') {

								$srv_chrg1c = $res['management_fee_value'];
							} else {
								//  for percentage
								$srv_chrg1c = ( $record['total_fare'] * $res['management_fee_value'] ) / 100;
							}
							if(!empty($emp_dat[0]['company_state_gst_code'])) {
								if($emp_dat[0]['company_state_gst_code'] == 27) {
									$serv_educ = $srv_chrg1c * 0.09;
									$serv_cs1c = $srv_chrg1c * 0.09;
								} else {
									$serv_taxc = $srv_chrg1c * 0.18;
								}
							} else {
								similar_text ( $emp_dat[0]['company_state'], 'maharashtra', $perc);
								if($perc > 50 ) {
									$serv_educ = $srv_chrg1c * 0.09;
									$serv_cs1c = $srv_chrg1c * 0.09;
								} else {
									$serv_taxc = $srv_chrg1c * 0.18;
								}
							}
						}

						$dt = array('management_fee' => $srv_chrg1c);
						$cond = array('origin' => $cust['origin'], 'app_reference' => $cust['app_reference']);
						$this->CI->custom_db->update_record('bus_booking_customer_details', $dt, $cond);
						// calculate and add management fee end

						$result [] = $this->format_for_xlwebpro_MISC($doc_nos, $doc_srno, $idate, $ccode, $srv_date, $ticket_no, $pax, $basic_c, $basic_s, $tax_c, $tax_s, $scode, $nos_pax_a, $nos_pax_c, $nos_pax_i, $srv_chrg1c, $serv_educ, $serv_taxc, $serv_cs1c, $xo_ref, $gcode, $doc_prf, $idm_flag);
					}
				} elseif ($module == 'hotel') {
					$total_basic_c = 0;
					$total_basic_s = 0;
					foreach ($booking['booking_itinerary_details'] as $bk => $book_iter) {
						$total_basic_c += $book_iter['total_fare'];
						$total_basic_s += $book_iter['total_fare'];
					}
					$nos_pax_a = 0;
					$nos_pax_c = 0;
					$nos_pax_i = 0;
					foreach ($booking['booking_customer_details'] as $bck => $book_cst) {
						if($book_cst['pax_type'] == 'Adult') {
							$nos_pax_a +=1;
						}
						if($book_cst['pax_type'] == 'Child') {
							$nos_pax_c +=1;
						}
						if($book_cst['pax_type'] == 'Infant') {
							$nos_pax_i +=1;
						}
					}
					// foreach ($booking['booking_customer_details'] as $ck => $cust) {
						$doc_srno = 1;
						$pax = $booking['booking_customer_details'][0]['first_name'].' '.$booking['booking_customer_details'][0]['last_name'];
						$basic_c = $total_basic_c;
						$basic_s = $total_basic_s;
						$tax_c = 0;
						$tax_s = 0;
						$srv_chrg1c = 0; 
						$serv_educ =  0;
						$serv_taxc = 0;
						$serv_cs1c = 0;
						$result [] = $this->format_for_xlwebpro_MISC($doc_nos, $doc_srno, $idate, $ccode, $srv_date, $ticket_no, $pax, $basic_c, $basic_s, $tax_c, $tax_s, $scode, $nos_pax_a, $nos_pax_c, $nos_pax_i, $srv_chrg1c, $serv_educ, $serv_taxc, $serv_cs1c, $xo_ref, $gcode, $doc_prf, $idm_flag);
					// }
				}
				// $pax = 
			// } // end loop	
		} // end if 
		$result['cols'] = array(
				'doc_prf' => 'DOC_PRF',
				'doc_nos' => 'DOC_NOS',
				'doc_srno' => 'DOC_SRNO',
				'idm_flag' => 'IDM_FLAG',
				'il_ref' => 'IL_REF',
				'vd_ref' => 'VD_REF',
				'idate' => 'IDATE',
				'ccode' => 'CCODE',
				'dcode' => 'DCODE',
				'ecode' => 'ECODE',
				'bcode' => 'BCODE',
				'narration' => 'NARRATION',
				'xo_ref' => 'XO_REF',
				'loc_code' => 'LOC_CODE',
				'cst_code' => 'CST_CODE',
				'curcode_c' => 'Curcode_C',
				'curcode_s' => 'Curcode_S',
				'refr_key' => 'REFR_KEY',
				'gcode' => 'GCODE',
				'scode' => 'SCODE',
				'xo_nos' => 'XO_NOS',
				'srv_date' => 'SRV_DATE',
				'ticketno' => 'TICKETNO',
				'pax' => 'PAX',
				'stx_cenvat' => 'STX_CENVAT',
				'stx_method' => 'STX_METHOD',
				'nos_pax_a' => 'NOS_PAX_A',
				'nos_pax_c' => 'NOS_PAX_C',
				'nos_pax_i' => 'NOS_PAX_I',
				'narr_1' => 'NARR_1',
				'narr_2' => 'NARR_2',
				'narr_3' => 'NARR_3',
				'narr_4' => 'NARR_4',
				'narr_5' => 'NARR_5',
				'narr_6' => 'NARR_6',
				'r_o_e_c' => 'R_O_E_C',
				'r_o_e_s' => 'R_O_E_S',
				'basic_c' => 'BASIC_C',
				'basic_s' => 'BASIC_S',
				'tax_c' => 'TAX_C',
				'tax_s' => 'TAX_S',
				'disc_paidm1' => 'DISC_PAIDM1',
				'disc_paidm2' => 'DISC_PAIDM2',
				'disc_recdm1' => 'DISC_RECDM1',
				'brok_paidm1' => 'BROK_PAIDM1',
				'disc_paidv1' => 'DISC_PAIDV1',
				'disc_recdv1' => 'DISC_RECDV1',
				'brok_paidv1' => 'BROK_PAIDV1',
				'disc_paid1' => 'DISC_PAID1',
				'disc_recd1' => 'DISC_RECD1',
				'brok_paid1' => 'BROK_PAID1',
				'srv_paidm2' => 'SRV_PAIDM2',
				'srv_chrg1c' => 'SRV_CHRG1C',
				'srv_chrg2c' => 'SRV_CHRG2C',
				'srv_chrg3c' => 'SRV_CHRG3C',
				'raf_c' => 'RAF_C',
				'srv_chrg1p' => 'SRV_CHRG1P',
				'srv_chrg2p' => 'SRV_CHRG2P',
				'srv_chrg3p' => 'SRV_CHRG3P',
				'raf_p' => 'RAF_P',
				'serv_taxc' => 'SERV_TAXC',
				'serv_educ' => 'SERV_EDUC',
				'tdc_paidv1' => 'TDC_PAIDV1',
				'tds_c' => 'TDS_C',
				'serv_taxp' => 'SERV_TAXP',
				'serv_edup' => 'SERV_EDUP',
				'tds_paidv1' => 'TDS_PAIDV1',
				'tds_p' => 'TDS_P',
				'tdb_paidv1' => 'TDB_PAIDV1',
				'tds_b' => 'TDS_B',
				'narration_5' => 'NARRATION_5',
				'narration_6' => 'NARRATION_6',
				'tax_7' => 'TAX_7',
				'tax_8' => 'TAX_8',
				'serv_cs1c' => 'SERV_CS1C',
				'serv_cs2c' => 'SERV_CS2C',
				'serv1_cs1c' => 'SERV1_CS1C',
				'serv1_cs2c' => 'SERV1_CS2C',
				'serv2_cs1c' => 'SERV2_CS1C',
				'serv2_cs2c' => 'SERV2_CS2C',
				'serv3_cs1c' => 'SERV3_CS1C',
				'serv3_cs2c' => 'SERV3_CS2C',
				'serv_cs1p' => 'SERV_CS1P',
				'serv_cs2p' => 'SERV_CS2P',
				'serv1_cs1p' => 'SERV1_CS1P',
				'serv1_cs2p' => 'SERV1_CS2P',
				'serv3_cs1p' => 'SERV3_CS1P',
				'serv3_cs2p' => 'SERV3_CS2P',
				'created_by' => 'Created_By',
				'created_on' => 'Created_On'
			);
		return $result;
	}

	public function format_for_xlwebpro_MISC($doc_nos, $doc_srno, $idate, $ccode, $srv_date, $ticket_no, $pax, $basic_c, $basic_s, $tax_c, $tax_s, $scode, $nos_pax_a, $nos_pax_c, $nos_pax_i, $srv_chrg1c, $serv_educ, $serv_taxc, $serv_cs1c, $xo_ref='B', $gcode='GI03BS', $doc_prf = 'MW', $idm_flag = 'M')
	{
		$result['doc_prf'] = $doc_prf;
		$result['doc_nos'] = $doc_nos;
		$result['doc_srno'] = $doc_srno;
		$result['idm_flag'] = $idm_flag;
		$result['il_ref'] = '';
		$result['vd_ref'] = '';
		$result['idate'] = $idate;
		$result['ccode'] = $ccode;
		$result['dcode'] = '';
		$result['ecode'] = '';
		$result['bcode'] = '';
		$result['narration'] = '';
		$result['xo_ref'] = $xo_ref;
		$result['loc_code'] = '000';
		$result['cst_code'] = '000';
		$result['curcode_c'] = 'INR';
		$result['curcode_s'] = 'INR';
		$result['refr_key'] = '';
		$result['gcode'] = $gcode;
		$result['scode'] = $scode;
		$result['xo_nos'] = '';
		$result['srv_date'] = $srv_date;
		$result['ticketno'] = $ticket_no;
		$result['pax'] = $pax;
		$result['stx_cenvat'] = 'C';
		$result['stx_method'] = 'N';
		$result['nos_pax_a'] = $nos_pax_a;
		$result['nos_pax_c'] = $nos_pax_c;
		$result['nos_pax_i'] = $nos_pax_i;
		$result['narr_1'] = '';
		$result['narr_2'] = '';
		$result['narr_3'] = '';
		$result['narr_4'] = '';
		$result['narr_5'] = '';
		$result['narr_6'] = '';
		$result['r_o_e_c'] = 1;
		$result['r_o_e_s'] = 1;
		$result['basic_c'] = round($basic_c);
		$result['basic_s'] = round($basic_s);
		$result['tax_c'] = round($tax_c);
		$result['tax_s'] = round($tax_s);
		$result['disc_paidm1'] = 'RB';
		$result['disc_paidm2'] = 'N';
		$result['disc_recdm1'] = 'RB';
		$result['brok_paidm1'] = 'RB';
		$result['disc_paidv1'] = 0;
		$result['disc_recdv1'] = 0;
		$result['brok_paidv1'] = 0;
		$result['disc_paid1'] = 0;
		$result['disc_recd1'] = 0;
		$result['brok_paid1'] = 0;
		$result['srv_paidm2'] = 'B';
		$result['srv_chrg1c'] = $srv_chrg1c;
		$result['srv_chrg2c'] = 0;
		$result['srv_chrg3c'] = 0;
		$result['raf_c'] = 0;
		$result['srv_chrg1p'] = 0;
		$result['srv_chrg2p'] = 0;
		$result['srv_chrg3p'] = 0;
		$result['raf_p'] = 0;
		$result['serv_taxc'] = $serv_taxc;
		$result['serv_educ'] = $serv_educ;
		$result['tdc_paidv1'] = 0;
		$result['tds_c'] = 0;
		$result['serv_taxp'] = 0;
		$result['serv_edup'] = 0;
		$result['tds_paidv1'] = 0;
		$result['tds_p'] = 0;
		$result['tdb_paidv1'] = 0;
		$result['tds_b'] = 0;
		$result['narration_5'] = '';
		$result['narration_6'] = '';
		$result['tax_7'] = 0;
		$result['tax_8'] = 0;
		$result['serv_cs1c'] = $serv_cs1c;
		$result['serv_cs2c'] = 0;
		$result['serv1_cs1c'] = 0;
		$result['serv1_cs2c'] = 0;
		$result['serv2_cs1c'] = 0;
		$result['serv2_cs2c'] = 0;
		$result['serv3_cs1c'] = 0;
		$result['serv3_cs2c'] = 0;
		$result['serv_cs1p'] = 0;
		$result['serv_cs2p'] = 0;
		$result['serv1_cs1p'] = 0;
		$result['serv1_cs2p'] = 0;
		$result['serv3_cs1p'] = 0;
		$result['serv3_cs2p'] = 0;
		$result['created_by'] = '';
		$result['created_on'] = '';

		return $result;
	}


	public function get_data_for_xlpro_railway($record)
	{
		$record['total_fare'] = 1000;
		// debug($record);//exit();
		$doc_prf = 'LW'; // for sales
		$doc_nos = $record['xlpro_invoice_no'];
		$doc_srno = 1;
		$idate = date('d/m/Y', strtotime($record['date']));

		$emp_dat = $this->get_employee_xlcode($record['employee_orgin']);
// debug($emp_dat);//exit();
		//dummy client code
		$ccode = 'C'.'U0003';

		if(!empty($emp_dat)) {
			if(!empty($emp_dat[0]['e_xl_code'])) {
				$ccode = 'C' . $emp_dat[0]['e_xl_code'];
			} else {
				if(!empty($emp_dat[0]['c_xl_code'])){
					$ccode = 'C'.$emp_dat[0]['c_xl_code'];
				}
			}
		}

		$pnr = $record['pnr'];
		$ticket_no = $pnr . $doc_srno;
		$pax = $record['employee_name'];
		$frm = substr($record['train_from'], (strpos($record['train_from'], '(')+1) , 3);
		$to = substr($record['train_to'], (strpos($record['train_to'], '(')+1) , 3);
		$sector = $frm . '/' . $to;


		$book_class = $record['train_class']; // eg 3E, CC
		$train_no = $record['train_number'];
		$journey_date = date('d/m/Y', strtotime($record['train_date']));

		$nos_pax_a = 1;
		$nos_pax_c = 0;
		$nos_pax_i = 0;

		$basic_fare = $record['total_fare'];

		// calculate and add management fee
		$stmt = "SELECT * FROM specific_markup_train where corporate_id = ". $record['company_orgin'] ;
		$query = $this->CI->db->query($stmt);
		$res = $query->result_array();
		$srv_chrg1c = 0;
		$serv_taxc = 0;

		$serv_educ = 0;
		$serv_cs1c = 0;
		if(!empty($res)) {
			$res = $res[0];
			if( $res['management_fee_type'] == 'plus') {

				$srv_chrg1c = $res['management_fee_value'];
			} else {
				//  for percentage
				$srv_chrg1c = ( $record['total_fare'] * $res['management_fee_value'] ) / 100;
			}

			if(!empty($emp_dat[0]['e_company_state_gst_code'])) {
				if($emp_dat[0]['e_company_state_gst_code'] == 27) {
					$serv_educ = $srv_chrg1c * 0.09;
					$serv_cs1c = $srv_chrg1c * 0.09;
				} else {
					$serv_taxc = $srv_chrg1c * 0.18;
				}
			} else {
				if(!empty($emp_dat[0]['e_company_state'])) {
					similar_text ( $emp_dat[0]['e_company_state'], 'maharashtra', $perc);
					if($perc > 50 ) {
						$serv_educ = $srv_chrg1c * 0.09;
						$serv_cs1c = $srv_chrg1c * 0.09;
					} else {
						$serv_taxc = $srv_chrg1c * 0.18;
					}

				} else {
					if(!empty($emp_dat[0]['c_company_state_gst_code'])) {
						if($emp_dat[0]['c_company_state_gst_code'] == 27) {
							$serv_educ = $srv_chrg1c * 0.09;
							$serv_cs1c = $srv_chrg1c * 0.09;
						} else {
							$serv_taxc = $srv_chrg1c * 0.18;
						}
					} else {
						similar_text ( $emp_dat[0]['c_company_state'], 'maharashtra', $perc);
						if($perc > 50 ) {
							$serv_educ = $srv_chrg1c * 0.09;
							$serv_cs1c = $srv_chrg1c * 0.09;
						} else {
							$serv_taxc = $srv_chrg1c * 0.18;
						}

					}
				}
			}

		}

		$at = array('management_fee' => $srv_chrg1c);
		$cond = array('origin' => $record['origin']);
		$this->CI->custom_db->update_record('train_details', $at, $cond);
		// calculate and add management fee end

		$result[] = $this->format_for_xlwebpro_rail ($doc_prf, $doc_nos, $doc_srno, $idate, $ccode, $pnr, $ticket_no, $pax, $sector, $book_class, $train_no, $journey_date, $nos_pax_a, $nos_pax_c, $nos_pax_i, $basic_fare, $srv_chrg1c, $serv_taxc, $serv_educ, $serv_cs1c);

		$result['cols'] = array(
				'doc_prf' => 'DOC_PRF',
				'doc_nos' => 'DOC_NOS',
				'doc_srno' => 'DOC_SRNO',
				'idm_flag' => 'IDM_FLAG',
				'il_ref' => 'IL_REF',
				'vd_ref' => 'VD_REF',
				'idate' => 'IDATE',
				'ccode' => 'CCODE',
				'dcode' => 'DCODE',
				'ecode' => 'ECODE',
				'bcode' => 'BCODE',
				'narration' => 'NARRATION',
				'xo_ref' => 'XO_REF',
				'loc_code' => 'LOC_CODE',
				'cst_code' => 'CST_CODE',
				'curcode_c' => 'Curcode_C',
				'curcode_s' => 'Curcode_S',
				'refr_key' => 'REFR_KEY',
				'gcode' => 'GCODE',
				'scode' => 'SCODE',
				'xo_nos' => 'XO_NOS',
				'ticketno' => 'TICKETNO',
				'pnr_no' => 'PNR_NO',
				'pax' => 'PAX',
				'sector' => 'SECTOR',
				'book_class' => 'BOOK_CLASS',
				'train_no' => 'TRAIN_NO',
				'journey_date' => 'JOURNEY_DATE',
				'nos_pax_a' => 'NOS_PAX_A',
				'nos_pax_c' => 'NOS_PAX_C',
				'nos_pax_i' => 'NOS_PAX_I',
				'r_o_e_c' => 'R_O_E_C',
				'r_o_e_s' => 'R_O_E_S',
				'basic_fare' => 'BASIC_FARE',
				'brok_paidm1' => 'BROK_PAIDM1',
				'brok_paidv1' => 'BROK_PAIDV1',
				'brok_paid1' => 'BROK_PAID1',
				'srv_chrg1c' => 'SRV_CHRG1C',
				'srv_chrg2c' => 'SRV_CHRG2C',
				'srv_chrg3c' => 'SRV_CHRG3C',
				'raf_c' => 'RAF_C',
				'srv_chrg1p' => 'SRV_CHRG1P',
				'srv_chrg2p' => 'SRV_CHRG2P',
				'srv_chrg3p' => 'SRV_CHRG3P',
				'raf_p' => 'RAF_P',
				'serv_taxc' => 'SERV_TAXC',
				'serv_educ' => 'SERV_EDUC',
				'serv_taxp' => 'SERV_TAXP',
				'serv_edup' => 'SERV_EDUP',
				'created_by' => 'Created_By',
				'created_on' => 'Created_On',
				'xxl_c' => 'XXL_C',
				'xxl_p' => 'XXL_P',
				'serv3_taxc' => 'SERV3_TAXC',
				'serv3_educ' => 'SERV3_EDUC',
				'serv3_taxp' => 'SERV3_TAXP',
				'serv3_edup' => 'SERV3_EDUP',
				'narration_5' => 'NARRATION_5',
				'narration_6' => 'NARRATION_6',
				'tax_7' => 'TAX_7',
				'tax_8' => 'TAX_8',
				'serv_cs1c' => 'SERV_CS1C',
				'serv_cs2c' => 'SERV_CS2C',
				'serv1_cs1c' => 'SERV1_CS1C',
				'serv1_cs2c' => 'SERV1_CS2C',
				'serv2_cs1c' => 'SERV2_CS1C',
				'serv2_cs2c' => 'SERV2_CS2C',
				'serv3_cs1c' => 'SERV3_CS1C',
				'serv3_cs2c' => 'SERV3_CS2C',
				'serv_cs1p' => 'SERV_CS1P',
				'serv_cs2p' => 'SERV_CS2P',
				'serv1_cs1p' => 'SERV1_CS1P',
				'serv1_cs2p' => 'SERV1_CS2P',
				'serv3_cs1p' => 'SERV3_CS1P',
				'serv3_cs2p' => 'SERV3_CS2P',
				'tdb_paidv1' => 'TDB_PAIDV1',
				'tds_b' => 'TDS_B',
				'gst_type' => 'GST_TYPE',
				'sac_code1' => 'SAC_CODE1'
			);
		return $result;

	}

	public function format_for_xlwebpro_rail ($doc_prf, $doc_nos, $doc_srno, $idate, $ccode, $pnr, $ticket_no, $pax, $sector, $book_class, $train_no, $journey_date, $nos_pax_a, $nos_pax_c, $nos_pax_i, $basic_fare, $srv_chrg1c, $serv_taxc, $serv_educ, $serv_cs1c)
	{
		$result ['doc_prf'] = $doc_prf;
		$result ['doc_nos'] = $doc_nos;
		$result ['doc_srno'] = $doc_srno;
		$result ['idm_flag'] = 'R';
		$result ['il_ref'] = '';
		$result ['vd_ref'] = '';
		$result ['idate'] = $idate;
		$result ['ccode'] = $ccode;
		$result ['dcode'] = '';
		$result ['ecode'] = '';
		$result ['bcode'] = '';
		$result ['narration'] = '';
		$result ['xo_ref'] = 'R';
		$result ['loc_code'] = '000';
		$result ['cst_code'] = '000';
		$result ['curcode_c'] = 'INR';
		$result ['curcode_s'] = 'INR';
		$result ['refr_key'] = '';
		$result ['gcode'] = 'GI03RL';
		$result ['scode'] = '';
		$result ['xo_nos'] = '';
		$result ['ticketno'] = $ticket_no;
		$result ['pnr_no'] = $pnr;
		$result ['pax'] = $pax;
		$result ['sector'] = $sector;
		$result ['book_class'] = $book_class;
		$result ['train_no'] = $train_no;
		$result ['journey_date'] = $journey_date;
		$result ['nos_pax_a'] = $nos_pax_a;
		$result ['nos_pax_c'] = $nos_pax_c;
		$result ['nos_pax_i'] = $nos_pax_i;
		$result ['r_o_e_c'] = 1;
		$result ['r_o_e_s'] = 1;
		$result ['basic_fare'] = $basic_fare;
		$result ['brok_paidm1'] = '';
		$result ['brok_paidv1'] = 0;
		$result ['brok_paid1'] = 0;
		$result ['srv_chrg1c'] = $srv_chrg1c;
		$result ['srv_chrg2c'] = 0;
		$result ['srv_chrg3c'] = 0;
		$result ['raf_c'] = 0;
		$result ['srv_chrg1p'] = 0;
		$result ['srv_chrg2p'] = 0;
		$result ['srv_chrg3p'] = 0;
		$result ['raf_p'] = 0;
		$result ['serv_taxc'] = $serv_taxc;
		$result ['serv_educ'] = $serv_educ;
		$result ['serv_taxp'] = 0;
		$result ['serv_edup'] = 0;
		$result ['created_by'] = '';
		$result ['created_on'] = '';
		$result ['xxl_c'] = 0;
		$result ['xxl_p'] = 0;
		$result ['serv3_taxc'] = 0;
		$result ['serv3_educ'] = 0;
		$result ['serv3_taxp'] = 0;
		$result ['serv3_edup'] = 0;
		$result ['narration_5'] = '';
		$result ['narration_6'] = '';
		$result ['tax_7'] = 0;
		$result ['tax_8'] = 0;
		$result ['serv_cs1c'] = $serv_cs1c;
		$result ['serv_cs2c'] = 0;
		$result ['serv1_cs1c'] = 0;
		$result ['serv1_cs2c'] = 0;
		$result ['serv2_cs1c'] = 0;
		$result ['serv2_cs2c'] = 0;
		$result ['serv3_cs1c'] = 0;
		$result ['serv3_cs2c'] = 0;
		$result ['serv_cs1p'] = 0;
		$result ['serv_cs2p'] = 0;
		$result ['serv1_cs1p'] = 0;
		$result ['serv1_cs2p'] = 0;
		$result ['serv3_cs1p'] = 0;
		$result ['serv3_cs2p'] = 0;
		$result ['tdb_paidv1'] = 0;
		$result ['tds_b'] = 0;
		$result ['gst_type'] = 0;
		$result ['sac_code1'] = 0;
		return $result;
	}

	public function get_employee($emp_id)
	{
		$stmt = "SELECT company_state, company_state_gst_code FROM  user WHERE user_id = ". $emp_id ;
		$query = $this->CI->db->query($stmt);
		if(empty($query->result_array())) {
			return false;
		}
		return $query->result_array();
	}

	public function get_corporate_xlcode($corp_id)
	{
		$stmt = "SELECT company_state, company_state_gst_code, xl_code FROM  user WHERE user_id = ". $corp_id ;
		$query = $this->CI->db->query($stmt);
		if(empty($query->result_array())) {
			return false;
		}
		return $query->result_array();
	}

	public function get_employee_xlcode($id)
	{
		// $stmt = "SELECT * FROM  user WHERE user_id = ". $id ;
		$stmt = "SELECT eu.xl_code as e_xl_code, eu.company_state as e_company_state, eu.company_state_gst_code as e_company_state_gst_code, cu.xl_code as c_xl_code, cu.company_state as c_company_state, cu.company_state_gst_code as c_company_state_gst_code, cu.user_id as c_user_id  
		FROM  user as eu 
		LEFT JOIN user as cu ON (eu.corporate_id = cu.user_id OR eu.created_by_id = cu.user_id) 
		WHERE eu.user_id = ". $id ;
		$query = $this->CI->db->query($stmt);
		if(empty($query->result_array())) {
			return false;
		}
		return $query->result_array();
	}


	/////////Pace travel/////////
	/*$DB2 = $GLOBALS['CI']->load->database('second_db', TRUE);
	$DB2->insert('test',$data);
	$details = $DB2->get('test')->result_array();
	debug($details);
	die();*/

	///**Sales Start**///

	public function get_flight_booking_details($booking = '',$temp_booking = ''){
		$status = 'BOOKING_CONFIRMED';
		//$temp_booking['book_id'] = 'FB07-105101-777097';
		//$temp_booking['booking_source'] = 'PTBSID0000000002';
		$flight_details = $this->CI->flight_model->get_booking_details($temp_booking['book_id'],$temp_booking['booking_source'],$status);
		//debug($flight_details);die();//gstgst
		$is_domestic = '';
		if($temp_booking['book_attributes']['token']['is_domestic'] == 1){
			$is_domestic = 1;
		}else{
			$is_domestic = 2;
		}
		
			if($flight_details['status'] == SUCCESS_STATUS){
				$clean_data = $this->insert_flight_booking_details($flight_details['data'],$is_domestic);
			}	
	}

	public function insert_flight_booking_details($booking_data='',$is_domestic = ''){
		//error_reporting(0);
		$response = array();
		$response['status'] = FAILURE_STATUS;
		$DB2 = $GLOBALS['CI']->load->database('second_db', TRUE);
		//debug($booking_data);die();
		if(!empty($booking_data)){
			$data = array();
			$booking_details = $booking_data['booking_details'][0];
			$total_fare = 0;
			$admin_markup = 0;
			$agent_markup = 0;
			$admin_tds = 0;
			$agent_tds = 0;
			$admin_commission = 0;
			$agent_commission = 0;
			$gst = 0;
			foreach ($booking_data['booking_transaction_details'] as $key => $value) {
					
					$total_fare = $value['total_fare'];
					$admin_markup = $value['admin_markup'];
					$agent_markup = $value['agent_markup'];
					$admin_tds = $value['admin_tds'];
					$agent_tds = $value['agent_tds'];
					$admin_commission = $value['admin_commission'];
					$agent_commission = $value['agent_commission'];
					$gst = $value['gst'];

					$tblSalesHead_data = array(

						'strBookingReferenceNo' => $booking_details['app_reference'],
						'strSalesHeadPrefix' => 'PFB',
						'dtSalesHeadDate' => date('Y-m-d',strtotime($booking_details['created_datetime'])),
						'dtSalesHeadDueDate' => date('Y-m-d',strtotime($booking_details['created_datetime'])),
						//'strIncomeAccountID' => '',
						'strCustomerAccountID' => $booking_details['created_by_id'],
						//'strSalesHeadPlaceofSupplyID' => '',
						'tintSalesHeadType' => 0,
						'intSalesHeadServiceID' =>  $is_domestic,
						//'intSalesDetailServiceID' => NULL,
						/*'tintSalesHeadDiscount' => 0,
						'strSalesHeadDiscountAon' => '',
						'decSalesHeadDiscountAon' => 0,
						'decSalesHeadDiscountAPrc' => 0,
						'decSalesHeadDiscountAAmt' => 0,
						'strSalesHeadDiscountBon' => '',
						'decSalesHeadDiscountBon' => 0,
						'decSalesHeadDiscountBPrc' => 0,
						'decSalesHeadDiscountBAmt' => 0,
						'decSalesHeadRefundablePrc' => 0,
						'decSalesHeadRefundableAmt' => 0,
						'decSalesHeadTDSPrc' => 0,
						'decSalesHeadTDSAmt' => 0,*/
						'strSalesHeadServiceTaxIon' => '4',
						/*'decSalesHeadServiceTaxIon' => 0,
						'decSalesHeadServiceTaxITaxPrc' => 0,
						'decSalesHeadServiceTaxICessPrc' => 0,
						'decSalesHeadServiceTaxIPrc' => 0,
						'decSalesHeadServiceTaxIAmt' => 0,*/
						//'tintSalesHeadServiceRentACabTaxon' => '',
						//'bitSalesHeadReverseCharge' => '',
						/*'decSalesHeadIGSTTaxPrc' => '',
						'decSalesHeadIGSTTaxAmt' => '',
						'decSalesHeadCGSTTaxPrc' => '',
						'decSalesHeadCGSTTaxAmt' => '',
						'decSalesHeadSGSTTaxPrc' => '',
						'decSalesHeadSGSTTaxAmt' => '',
						'decSalesHeadGSTCessPrc' => '',
						'decSalesHeadGSTCessAmt' => '',
						'decSalesHeadGSTTaxPrc' => '',*/
						'decSalesHeadGSTAmt' => $gst,
						//'decSalesHeadRoundOffAmt' => '',
						'decSalesHeadNetAmt' => $total_fare,
						//'decCustomerCardBasicAmt' => '',
						//'strSalesHeadNarration' => '',
						//'intSalesHeadAdult' => '',
						//'intSalesHeadChild' => '',
						//'intSalesHeadInfant' => '',
						'strPurchaseReferenceNumber' => $booking_details['app_reference'],
						//'strExpensesAccountID' => '',
						'strSupplierAccountID' => $newstring = substr($booking_details['booking_source'], -3),
						'strPurchasePlaceofSupplyID' => 29,
						//'strPurchaseNarration' => '',
						//'decPurchaseProcessingAmt' => '',
						//'decPurchaseOtherAmt' => '',
						'tintPurchaseTAC' => 0,
						'strPurchaseTACAon' => '1,2',
						'decPurchaseTACAon' => $admin_commission,
						/*'decPurchaseTACAPrc' => '',
						'decPurchaseTACAAmt' => '',
						'strPurchaseTACBon' => '',
						'decPurchaseTACBon' => '',
						'decPurchaseTACBPrc' => '',
						'decPurchaseTACBAmt' => '',*/
						/*'decPurchaseRefundablePrc' => '',
						'decPurchaseRefundableAmt' => '',*/
						//'decPurchaseTDSPrc' => '',
						'decPurchaseTDSAmt' => $admin_tds,
						/*'strPurchaseServiceTaxon' => '',
						'decPurchaseServiceTaxon' => '',
						'decPurchaseServiceTaxTaxPrc' => '',
						'decPurchaseServiceTaxCessPrc' => '',
						'decPurchaseServiceTaxPrc' => '',
						'decPurchaseServiceTaxAmt' => '',
						'bitPurchaseReverseCharge' => '',
						'decPurchaseIGSTTaxPrc' => '',
						'decPurchaseIGSTTaxAmt' => '',
						'decPurchaseCGSTTaxPrc' => '',
						'decPurchaseCGSTTaxAmt' => '',
						'decPurchaseSGSTTaxPrc' => '',
						'decPurchaseSGSTTaxAmt' => '',
						'decPurchaseGSTCessPrc' => '',
						'decPurchaseGSTCessAmt' => '',*/
						//'bitPurchaseCenvatCredit' => '',
						//'decPurchaseRoundOffAmt' => '',
						'decPurchaseNetAmt' => $total_fare,
						/*'tintPurchaseCard' => '',
						'strPurchaseCardAccountID' => '',
						'strPurchaseCardNumber' => '',
						'decPurchaseCardBasicAmt' => '',
						'strPurchaseCardChargesAccountID' => '',
						'decPurchaseCardChargesAmt' => '',
						'strDatabaseName' => '',
						'intBranchID' => '',
						'strAction' => '',
						'strImportStatus' => '',
						'strMerchantID' => '',*/
					);

					// insert into 'tblSalesHead';
					$DB2->insert('tblSalesHead',$tblSalesHead_data);
					$last_insert_id = $DB2->insert_id();
					//debug($tblSalesHead_data);die('-----');
					//debug($last_insert_id);die('8');
					//echo "===========";


					// insert customer details data
					if($booking_details['booking_source'] == TRAVELPORT_GDS_BOOKING_SOURCE){
						$_air_type = 3;
						$_is_bsp = 1;
					}else{
						$_air_type = 4;
						$_is_bsp = 0;
					}

					$count = 1;
					$no_of_pax = count($booking_data['booking_customer_details']);
					foreach ($booking_data['booking_customer_details'] as $cust_k => $cust_v) {
						
						if($cust_v['flight_booking_transaction_details_fk'] == $value['origin']){
							
							
							$pnr = '';
							$base_f = 0;
							$tax = 0;
							foreach ($booking_data['booking_transaction_details'] as $trnx_k => $trnx_v) {
								 if($cust_v['flight_booking_transaction_details_fk'] == $trnx_v['origin']){
								 	//$ticket_no = $trnx_v['book_id'];
								 	$pnr = $trnx_v['pnr'];

								 	$fare = json_decode($trnx_v['attributes'],1);
								 	$base_f += $fare['Fare']['BaseFare'];
								 	$tax += $fare['Fare']['Tax'];
								 }
							}

							$airline_code = '';
							$jrny = '';
							$flight_number ='';
							$fare_class ='';
							$jr_details ='';

							if($booking_details['trip_type'] == 'circle' && $is_domestic == '1'){
								if($key == 0){
									foreach ($booking_data['booking_itinerary_details'] as $iter_k => $iter_v) {
										if(($iter_k > 0) && ($iter_v['segment_indicator'] == 1)){
											break;
										}
										$airline_code = $iter_v['airline_code'];
										$jrny .= $iter_v['from_airport_code'].'/';
										
										$s_date = date('d/m/Y',strtotime($iter_v['departure_datetime']));
										$s_time = date('h:i',strtotime($iter_v['departure_datetime']));
										$e_time = date('H:i',strtotime($iter_v['arrival_datetime']));
										$flight_number = $iter_v['airline_code'].' '.$iter_v['flight_number'];
										$class = $iter_v['fare_class'];

										$jr_details .= $s_date.'#'.$s_time.'#'.$e_time.'#'.$flight_number.'#'.$class.'$';
										$dest = $iter_v['to_airport_code'];
										//debug($dest);
									}
								}else{
									$dkn = 0;
									foreach ($booking_data['booking_itinerary_details'] as $iter_k => $iter_v) {
										if((($iter_k > 0) && ($iter_v['segment_indicator'] == 1)) || ($dkn > 0)){
											$airline_code = $iter_v['airline_code'];
											$jrny .= $iter_v['from_airport_code'].'/';
											
											$s_date = date('d/m/Y',strtotime($iter_v['departure_datetime']));
											$s_time = date('h:i',strtotime($iter_v['departure_datetime']));
											$e_time = date('H:i',strtotime($iter_v['arrival_datetime']));
											$flight_number = $iter_v['airline_code'].' '.$iter_v['flight_number'];
											$class = $iter_v['fare_class'];

											$jr_details .= $s_date.'#'.$s_time.'#'.$e_time.'#'.$flight_number.'#'.$class.'$';
											$dest = $iter_v['to_airport_code'];
											$dkn++;
										}	
									}
								}
							}else{
								foreach ($booking_data['booking_itinerary_details'] as $iter_k => $iter_v) {
										/*if(($iter_k > 0) && ($iter_v['segment_indicator'] == 1)){
											break;
										}*/
										$airline_code = $iter_v['airline_code'];
										$jrny .= $iter_v['from_airport_code'].'/';
										
										$s_date = date('d/m/Y',strtotime($iter_v['departure_datetime']));
										$s_time = date('h:i',strtotime($iter_v['departure_datetime']));
										$e_time = date('H:i',strtotime($iter_v['arrival_datetime']));
										$flight_number = $iter_v['airline_code'].' '.$iter_v['flight_number'];
										$class = $iter_v['fare_class'];

										$jr_details .= $s_date.'#'.$s_time.'#'.$e_time.'#'.$flight_number.'#'.$class.'$';
										$dest = $iter_v['to_airport_code'];
										//debug($dest);
									}
							}
							

							
							$fare_attr = json_decode($cust_v['attributes'],1);
							$p_tax = $fare_attr['Tax']/$fare_attr['PassengerCount'];
							$p_base_fare = $fare_attr['BaseFare']/$fare_attr['PassengerCount'];
							$p_total_fare = $fare_attr['TotalPrice']/$fare_attr['PassengerCount'];
							$ticket_no = $cust_v['TicketNumber'];
							//$ticket_no = '5592300119647';
							$pre_ticket_no = substr($ticket_no,0,3);
							$post_ticket_no = substr($ticket_no,3);
							if(!is_numeric($post_ticket_no)){
								$post_ticket_no = 000000;
							}
							$tblSalesDetails_data = array(
								'intBookingReferenceID' => $last_insert_id,
								'intBookingReferenceDetailID' => $count++,
								'strSalesDetailPassenger' => $cust_v['title'].' '.$cust_v['first_name'].' '.$cust_v['last_name'],
								//'strSalesDetailNotes' => '',
								'strAccountAirPrefix' => rtrim($airline_code,'/'),
								//'strAccountAirCode' => '',
								//'strAirlineAccountID' =>'',
								'tintSalesDetailFromStock' => $_is_bsp,
								'intSalesDetailPNRFromID' => $_air_type,
								'strSalesDetailTicketPrefix' => $pre_ticket_no,
								'intSalesDetailTicketNo' =>$post_ticket_no,
								'strSalesDetailAirlinePNRNo' => $pnr,
								//'strSalesDetailCRSPNRNo' =>'',
								'strSalesDetailSectorDetail'=> $jrny.$dest,
								'strSalesDetailJRNDetail' => rtrim($jr_details,'$'),
								'strSalesDetailFareBasis' => $class,
								'decSalesDetailBasicAmt'=>$p_base_fare,
								'decSalesDetailBasicMarkupAmt' =>$agent_markup/$no_of_pax,
								/*'decSalesDetailTAXIAmt'=>'',
								'decSalesDetailTAXIMarkupAmt' =>'',*/
								'decSalesDetailTAXIIAmt' => $p_tax,
								/*'decSalesDetailTAXIIMarkupAmt' =>'',
								'decSalesDetailTAXIIIAmt' =>'',
								'decSalesDetailTAXIVAmt' => '',
								'decSalesDetailProcessingAmt' => '',
								'decSalesDetailOtherAmt' => ''*/
							);

							//debug($tblSalesDetails_data);
							$DB2->insert('tblSalesDetail',$tblSalesDetails_data);
						}
					}
			}
			//die('++++');
			$response['status'] = SUCCESS_STATUS;
		}else{
			$response['status'] = FAILURE_STATUS;
		}

		return $response;
	}

	public function get_bus_booking_details($booking = '',$temp_booking = ''){

		$status = 'BOOKING_CONFIRMED';
		//ets
		/*$temp_booking['book_id'] = 'BB07-114530-375687';
		$temp_booking['booking_source'] = 'PTBSID0000000033';*/
		//bitla
		/*$temp_booking['book_id'] = 'BB03-114921-312059';
		$temp_booking['booking_source'] = 'PTBSID0000000031';*/
		//vrl
		/*$temp_booking['book_id'] = 'BB27-163317-33090';
		$temp_booking['booking_source'] = 'PTBSID0000000032';*/


		$bus_details = $this->CI->bus_model->get_booking_details($temp_booking['book_id'],$temp_booking['booking_source'],$status);
		//debug($bus_details);die();
		if($bus_details['status'] == SUCCESS_STATUS){
			$clean_data = $this->insert_bus_booking_details($bus_details['data']);
		}
	}

	public function insert_bus_booking_details($booking_data=''){
		$response = array();
		$response['status'] = FAILURE_STATUS;
		$DB2 = $GLOBALS['CI']->load->database('second_db', TRUE);

		if(!empty($booking_data)){
			$data = array();
			$booking_details = $booking_data['booking_details'][0];
			
			$total_fare = 0;
			$admin_markup = 0;
			$agent_markup = 0;
			$admin_tds = 0;
			$agent_tds = 0;
			$admin_commission = 0;
			$agent_commission = 0;
			foreach ($booking_data['booking_customer_details'] as $key => $value) {
				$total_fare += $value['fare'];
				$admin_markup += $value['admin_markup'];
				$agent_markup += $value['agent_markup'];
				$admin_tds += $value['admin_tds'];
				$agent_tds += $value['agent_tds'];
				$admin_commission += $value['admin_commission'];
				$agent_commission += $value['agent_commission'];
				
			}

			$tblSalesHead_data = array(

				'strBookingReferenceNo' => $booking_details['app_reference'],
				'strSalesHeadPrefix' => 'PBB',
				'dtSalesHeadDate' => date('Y-m-d',strtotime($booking_details['created_datetime'])),
				'dtSalesHeadDueDate' => date('Y-m-d',strtotime($booking_details['created_datetime'])),
				//'strIncomeAccountID' => '',
				'strCustomerAccountID' => $booking_details['created_by_id'],
				//'strSalesHeadPlaceofSupplyID' => '',
				'tintSalesHeadType' => 0,
				'intSalesHeadServiceID' =>  4,
				//'intSalesDetailServiceID' => NULL,
				/*'tintSalesHeadDiscount' => 0,
				'strSalesHeadDiscountAon' => '',
				'decSalesHeadDiscountAon' => 0,
				'decSalesHeadDiscountAPrc' => 0,
				'decSalesHeadDiscountAAmt' => 0,
				'strSalesHeadDiscountBon' => '',
				'decSalesHeadDiscountBon' => 0,
				'decSalesHeadDiscountBPrc' => 0,
				'decSalesHeadDiscountBAmt' => 0,
				'decSalesHeadRefundablePrc' => 0,
				'decSalesHeadRefundableAmt' => 0,
				'decSalesHeadTDSPrc' => 0,
				'decSalesHeadTDSAmt' => 0,*/
				'strSalesHeadServiceTaxIon' => '4',
				/*'decSalesHeadServiceTaxIon' => 0,
				'decSalesHeadServiceTaxITaxPrc' => 0,
				'decSalesHeadServiceTaxICessPrc' => 0,
				'decSalesHeadServiceTaxIPrc' => 0,
				'decSalesHeadServiceTaxIAmt' => 0,*/
				//'tintSalesHeadServiceRentACabTaxon' => '',
				//'bitSalesHeadReverseCharge' => '',
				/*'decSalesHeadIGSTTaxPrc' => '',
				'decSalesHeadIGSTTaxAmt' => '',
				'decSalesHeadCGSTTaxPrc' => '',
				'decSalesHeadCGSTTaxAmt' => '',
				'decSalesHeadSGSTTaxPrc' => '',
				'decSalesHeadSGSTTaxAmt' => '',
				'decSalesHeadGSTCessPrc' => '',
				'decSalesHeadGSTCessAmt' => '',
				'decSalesHeadGSTTaxPrc' => '',
				'decSalesHeadGSTAmt' => '',*/
				//'decSalesHeadRoundOffAmt' => '',
				'decSalesHeadNetAmt' => $total_fare,
				//'decCustomerCardBasicAmt' => '',
				//'strSalesHeadNarration' => '',
				//'intSalesHeadAdult' => '',
				//'intSalesHeadChild' => '',
				//'intSalesHeadInfant' => '',
				'strPurchaseReferenceNumber' => $booking_details['app_reference'],
				//'strExpensesAccountID' => '',
				'strSupplierAccountID' => $newstring = substr($booking_details['booking_source'], -3),
				'strPurchasePlaceofSupplyID' => 29,
				//'strPurchaseNarration' => '',
				//'decPurchaseProcessingAmt' => '',
				//'decPurchaseOtherAmt' => '',
				'tintPurchaseTAC' => 0,
				'strPurchaseTACAon' => '1,2',
				'decPurchaseTACAon' => $admin_commission,
				/*'decPurchaseTACAPrc' => '',
				'decPurchaseTACAAmt' => '',
				'strPurchaseTACBon' => '',
				'decPurchaseTACBon' => '',
				'decPurchaseTACBPrc' => '',
				'decPurchaseTACBAmt' => '',*/
				/*'decPurchaseRefundablePrc' => '',
				'decPurchaseRefundableAmt' => '',*/
				//'decPurchaseTDSPrc' => '',
				'decPurchaseTDSAmt' => $admin_tds,
				/*'strPurchaseServiceTaxon' => '',
				'decPurchaseServiceTaxon' => '',
				'decPurchaseServiceTaxTaxPrc' => '',
				'decPurchaseServiceTaxCessPrc' => '',
				'decPurchaseServiceTaxPrc' => '',
				'decPurchaseServiceTaxAmt' => '',
				'bitPurchaseReverseCharge' => '',
				'decPurchaseIGSTTaxPrc' => '',
				'decPurchaseIGSTTaxAmt' => '',
				'decPurchaseCGSTTaxPrc' => '',
				'decPurchaseCGSTTaxAmt' => '',
				'decPurchaseSGSTTaxPrc' => '',
				'decPurchaseSGSTTaxAmt' => '',
				'decPurchaseGSTCessPrc' => '',
				'decPurchaseGSTCessAmt' => '',*/
				//'bitPurchaseCenvatCredit' => '',
				//'decPurchaseRoundOffAmt' => '',
				'decPurchaseNetAmt' => $total_fare,
				/*s'tintPurchaseCard' => '',
				'strPurchaseCardAccountID' => '',
				'strPurchaseCardNumber' => '',
				'decPurchaseCardBasicAmt' => '',
				'strPurchaseCardChargesAccountID' => '',
				'decPurchaseCardChargesAmt' => '',
				'strDatabaseName' => '',
				'intBranchID' => '',
				'strAction' => '',
				'strImportStatus' => '',
				'strMerchantID' => '',*/
			);
			// insert into 'tblSalesHead';
			$DB2->insert('tblSalesHead',$tblSalesHead_data);
			$last_insert_id = $DB2->insert_id();
			//$last_insert_id = '123';
			//debug($tblSalesHead_data);//die('8');
			//echo "===========";


			// insert customer details data
			$count = 1;
			foreach ($booking_data['booking_customer_details'] as $cust_k => $cust_v) {
					
					$ticket_no = $booking_details['ticket'];
					$pnr_explode = explode(',', $booking_details['pnr']);
					$pnr = '';
					if(count($pnr_explode) > 1){
						$pnr = $pnr_explode[$cust_k];
					}else{
						$pnr = $pnr_explode[0];
					}
					$cust_attr = json_decode($cust_v['attr'],1);
					$base_f = $cust_attr['Fare']-$cust_attr['_ServiceTax'];
					$tax = $cust_attr['_ServiceTax'];

					$bus_code = '';
					$jrny = '';
					$flight_number ='';
					$fare_class ='';
					$jr_details ='';
					foreach ($booking_data['booking_itinerary_details'] as $iter_k => $iter_v) {
						$bus_code = $iter_v['operator'];
						$s_date = date('d/m/Y',strtotime($iter_v['departure_datetime']));
						$s_time = date('h:i',strtotime($iter_v['departure_datetime']));
						$e_time = date('H:i',strtotime($iter_v['arrival_datetime']));
						$bus_number = $iter_v['operator'];
						$class = '';//$iter_v['fare_class'];
						$jr_details .= $s_date.'#'.$s_time.'#'.$e_time.'#'.$bus_number.'#'.$class.'$';
					}

					$tblSalesDetails_data = array(
						'intBookingReferenceID' => $last_insert_id,
						'intBookingReferenceDetailID' => $count++,
						'strSalesDetailPassenger' => $cust_v['title'].' '.$cust_v['name'],
						//'strSalesDetailNotes' => '',
						'strAccountAirPrefix' => $bus_code,
						//'strAccountAirCode' => '',
						//'strAirlineAccountID' =>'',
						//'tintSalesDetailFromStock' => $_is_bsp,
						//'intSalesDetailPNRFromID' => $_air_type,
						//'strSalesDetailTicketPrefix' => '',
						//'intSalesDetailTicketNo' =>$ticket_no,
						'strSalesDetailAirlinePNRNo' => $pnr,
						//'strSalesDetailCRSPNRNo' =>'',
						//'strSalesDetailSectorDetail'=> $jrny.$dest['to_airport_code'],
						'strSalesDetailJRNDetail' => rtrim($jr_details,'$'),
						//'strSalesDetailFareBasis' => $class,
						'decSalesDetailBasicAmt'=>$base_f,
						/*'decSalesDetailBasicMarkupAmt' =>'',
						'decSalesDetailTAXIAmt'=>'',
						'decSalesDetailTAXIMarkupAmt' =>'',*/
						'decSalesDetailTAXIIAmt' => $tax,
						/*'decSalesDetailTAXIIMarkupAmt' =>'',
						'decSalesDetailTAXIIIAmt' =>'',
						'decSalesDetailTAXIVAmt' => '',
						'decSalesDetailProcessingAmt' => '',
						'decSalesDetailOtherAmt' => ''*/
					);
					//debug($tblSalesDetails_data);//die();
					$DB2->insert('tblSalesDetail',$tblSalesDetails_data);
			}
			
			//die('==========');
			$response['status'] = SUCCESS_STATUS;
			// insert customer details
		}else{
			$response['status'] = FAILURE_STATUS;
		}
	}

	///**Sales End**///

	public function get_bus_sales_return_details(){
		//tblSalesReturnHead

	}
	

	///**Account Start**///
	public function insert_account_details(){
		
		$tblAccountMaster_data = array(
				//'intAccountID' => 'Primary key Auto generate',
				'strAccountName' => '',
				'strAccountAlias' => '',		
				'intGroupID' => '',
				'bitAccountActiveStatus' => '',
				'bitAccountBlackListed' => '',
				'strAccountRemark' => '',
				'intAccountCreditLimit' => '',
				'bitMaintainBalanceBillwise' => '',
				'intAccountCreditDays' => '',
				'strAccountAddress' => '',
				'strCityName' => '',
				'strAccountPostalCode' => '',
				'strAccountPhone' => '',
				'strAccountPhoneRes' => '',

				'strAccountMobile' => '',
				'strAccountFax' => '',
				'strAccountEmail' => '',
				'strAccountWebsite' => '',
				'strAccountLocation' => '',
				'strAccountAddressAlternate' => '',
				'intNatureofSupplyID' => '',
				'tintAccountGSTINType' => '',
				'strAccountGSTIN' => '',
				'dtAccountGSTINDate' => '',
				'bitAccounteCOMOperator' => '',
				'strAccounteCOMMerchantID' => '',
				'strAccountReverseChargesPurchase' => '',
				'strAccountReverseChargesSales' => '',
				'strAccountCenvatCredit' => '',
				'strAccountTaxNo' => '',
				'strAccountDoNotCalTax' => '',
				'decAccountRefundableTaxPer' => '',

				'tintAccountTaxRoundMethod' => '',
				'strAccountNameofResponsible' => '',
				'strAccountPAN' => '',
				'strAccountTAN' => '',
				'bitAccountTDSExempt' => '',
				'intAccountTDSLimit' => '',
				'decAccountTDSPer' => '',
				'tintAccountTDSRoundMethod' => '',
				'tintAccountTACRoundMethod' => '',
				'strAccountBank' => '',
				'strAccountPerson' => '',
				'tintAccountAgencyType' => '',
				'strAccountAgencyCode' => '',
				'strDatabaseName' => '',
			);
		

		$tblAccountBalance_data =array(
				'intAccountID' => '',
				'decAccountOpening' => '',
				'strAccountOpeningType' => '',
				'intBranchID' => '',
				'strAction' => '',
				'strImportStatus' => '',
				'strMerchantID' => '',
			);
	}	
	///**Account End**///

	///**Voucher Start**///

	public function insert_voucher_details(){
		
		$tblVoucherHead_data = array(
				//'intVoucherReferenceID' => 'Primary key Auto generate',
				'strVoucherReferenceNo' => '',
				'strVoucherHeadPrefix' => '',
				'dtVoucerHeadDate' => '',
				'strVoucherHeadAccountID' => '',
				'strVoucherHeadAgainstAccountID' => '',
				'strVoucherHeadBankName' => '',
				'strVoucherHeadBankDrawnBranch' => '',
				'tintVoucherHeadBankChequeType' => '',
				'strVoucherHeadBankChequeNumber' => '',
				'dtVoucherHeadBankChequeDate' => '',
				'strVoucherHeadNarration' => '',
				'intObjectID' => '',
				'strDatabaseName' => '',
				'intBranchID' => '',
				'strAction' => '',
				'strImportStatus' => '',
				'strMerchantID' => '',

			);

		$tblVoucherDetail_data = array(
				'intVoucherReferenceID' => '',
				'intVoucherReferenceDetailID' => '',
				'tintVoucherDetailEntryfor' => '',
				'intVoucherNewReferenceID' => '',
				'strVoucherDetailAccountID' => '',
				'strVoucherDetailAgainstAccountID' => '',
				'strVoucherDetailNarration' => '',
				'decVoucherDetailTotalAmt' => '',
				'strVoucherDetailTotalAmtType' => '',
				'decVoucherDetailTDSon' => '',
			);
	}

	///**Voucher End**///
	
}