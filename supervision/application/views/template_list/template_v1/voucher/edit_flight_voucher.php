<style>
th, td {
	padding: 5px;
}

table {
	page-break-inside: auto
}

tr {
	page-break-inside: avoid;
	page-break-after: auto
}
</style>

<?php 
$booking_details = $data ['booking_details'] [0];
//debug();die();
$itinerary_details = $booking_details ['booking_itinerary_details'];

$attributes = $booking_details ['attributes'];
$customer_details = $booking_details ['booking_transaction_details'] [0] ['booking_customer_details'];
$domain_details = $booking_details;
$lead_pax_details = $customer_details;
$booking_transaction_details = $booking_details ['booking_transaction_details'];

$adult_count = 0;
$infant_count = 0;

foreach ( $customer_details as $k => $v ) {
	if (strtolower ( $v ['passenger_type'] ) == 'infant') {
		$infant_count ++;
	} else {
		$adult_count ++;
	}
}

$Onward = '';
$return = '';
if (count ( $booking_transaction_details ) == 2) {
	$Onward = '(Onward)';
	$Return = '(Return)';
}

// generate onword and return
if ($booking_details ['is_domestic'] == true && count($booking_transaction_details) == 2) {
	$onward_segment_details = array ();
	$return_segment_details = array ();
	$segment_indicator_arr = array ();
	$segment_indicator_sort = array ();
	
	foreach ( $itinerary_details as $key => $key_sort_data ) {
		$segment_indicator_sort [$key] = $key_sort_data ['origin'];
	}
	array_multisort ( $segment_indicator_sort, SORT_ASC, $itinerary_details );
	
	foreach ( $itinerary_details as $k => $sub_details ) {
		$segment_indicator_arr [] = $sub_details ['segment_indicator'];
		$count_value = array_count_values ( $segment_indicator_arr );
		
		if ($count_value [1] == 1) {
			$onward_segment_details [] = $sub_details;
		} else {
			$return_segment_details [] = $sub_details;
		}
	}
}
?>
<form action="" method="POST" />
<div class="table-responsive">
<table
	style="border-collapse: collapse; background: #ffffff; font-size: 14px; margin: 0 auto; font-family: arial;"
	width="100%" cellpadding="0" cellspacing="0" border="0">
	<tbody>
		<tr>
			<td style="border-collapse: collapse; padding: 10px 20px 20px">
				<table width="100%" style="border-collapse: collapse;"
					cellpadding="0" cellspacing="0" border="0">


					<tr>
						<td style="padding: 10px; width: 100%;">
							<table cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse: collapse;">
								<tr>
									<td
										style="font-size: 22px; line-height: 30px; width: 100%; display: block; font-weight: 600; text-align: center">
						        		  E-Ticket<?php echo $Onward;?>
						          </td>
								</tr>
								<tr>
									<td colspan="1">
										<table width="100%" style="border-collapse: collapse;"
											cellpadding="0" cellspacing="0" border="0">
											<tr>
												<td style="width: 60%"><img style="max-height: 56px"
													src="<?=$GLOBALS['CI']->template->domain_images($data['logo'])?>"></td>
												<td style="width: 40%">
													<table width="100%"
														style="border-collapse: collapse; text-align: right; line-height: 15px;"
														cellpadding="0" cellspacing="0" border="0">
														<tr>
															<td style="font-size: 14px;"><span
																style="width: 100%; float: left"><?php echo $data['address'];?></span>
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td colspan="2" 
										style="padding: 10px; border: 1px solid #cccccc; font-size: 14px; font-weight: bold;">Reservation
										Lookup</td>

								</tr>
								<tr>
									<td colspan="5" style="border: 1px solid #cccccc;">
										<table width="100%" cellpadding="5"
											style="padding: 10px; font-size: 14px;">
											<tr>
												<td><strong>Booking Reference</strong></td>
												<td><strong>Booking Source</strong></td>
												<td><strong>PNR</strong></td>

												<td><strong>Booking Date</strong></td>
												<td><strong>Status</strong></td>
											</tr>
											<tr>

												<td><?=@$booking_details['app_reference']?></td>
												<!-- <td><input type="text" name="booking_id" value="<?=@$booking_transaction_details[0]['book_id']?>"></td> -->
												
												<td> <select class="form-control" id ="booking_source" name="booking_source"required>
														<option value="">Select</option>            
														<?= generate_options($supliers_list, (array) @$booking_details['booking_source']) ?>                   
													</select></td>
												<td><input type="text" name="pnr" value="<?=@$booking_transaction_details[0]['gds_pnr']?>"></td>
												<td><?=app_friendly_absolute_date(@$booking_details['booked_date'])?></td>
												<td>
													
													<select class="form-control" name="status" required>
														<?= generate_options(get_enum_list('booking_status_options'), array(@$booking_transaction_details[0]['status'])) ?>
													</select>
													
											
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td colspan="2" 
										style="padding: 10px; border: 1px solid #cccccc; font-size: 14px; font-weight: bold;">Journey
										Information</td>

								</tr>
								<tr>
									<td colspan="5" width="100%" style="border: 1px solid #cccccc;">
										<table width="100%" cellpadding="5"
											style="padding: 10px; font-size: 14px;">
											<tr>
												<td><strong>Flight</strong></td>
												<td><strong>AirlinePNR</strong></td>
												<td><strong>Departure</strong></td>
												<td><strong>Arrival</strong></td>
												<td><strong>Journey Time</strong></td>
											</tr>
											<tr>
										<?php
										if (isset ( $booking_transaction_details ) && $booking_transaction_details != "") {
											
											if ($booking_details ['is_domestic'] == true && count ( $booking_transaction_details ) == 2) {
												$itinerary_details = array ();
												$itinerary_details = $onward_segment_details;
											}
											foreach ( $itinerary_details as $segment_details_k => $segment_details_v ) {
												
												$itinerary_details_attributes = json_decode ( $segment_details_v ['attributes'], true);
												$airline_terminal_origin = @$itinerary_details_attributes['departure_terminal'];
												$airline_terminal_destination = @$itinerary_details_attributes['arrival_terminal'];
												$origin_terminal = '';
												$destination_terminal = '';
												if ($airline_terminal_origin != '') {
													$origin_terminal = 'Terminal ' . $airline_terminal_origin;
												}
												if ($airline_terminal_destination != '') {
													$destination_terminal = 'Terminal ' . $airline_terminal_destination;
												}
												
												if (valid_array ( $segment_details_v ) == true) {
													?>
                              
                                 <?php if($booking_details['trip_type'] == 'circle' && $booking_details['is_domestic'] == true){?>
                                  <?php }?>
                                        <td><img
													style="max-height: 30px"
													src="<?=SYSTEM_IMAGE_DIR.'airline_logo/'.$segment_details_v['airline_code'].'.gif'?>"
													alt="flight-logo" /> <span style="width: 100%; float: left">
													<select class="form-control" name="carrier[<?=$segment_details_v['origin'];?>]" required>
														<?= generate_options($airline_list, (array) @$segment_details_v['airline_code']) ?>                               
													</select></span>
													<span
													style="width: 100%; float: left; font-size: 13px; font-weight: bold"><input type="text" name="flight_number[<?=$segment_details_v['origin'];?>]" value="<?php echo $segment_details_v['flight_number'];?>" /></span></td>
													<td><input type="text" name="airline_pnr[<?=$segment_details_v['origin'];?>]" value="<?=$segment_details_v['airline_pnr'];?>"></td>
													
												<td style="line-height: 16px"><span
													style="width: 100%; float: left; font-size: 13px; font-weight: bold"> <input type="text" class="fromflight" name="from_ac[<?=$segment_details_v['origin'];?>]" value="<?=@$segment_details_v['from_airport_name'] ?>(<?=@$segment_details_v['from_airport_code']?>)" /></span>
													<span style="width: 100%; float: left"><?php echo $origin_terminal;?></span>
													<span style="width: 100%; float: left; font-weight: bold"> <input type="text" name="dep_date[<?=$segment_details_v['origin'];?>]" value="<?php echo $segment_details_v['departure_datetime']; ?>" /></span></td>
												<td style="line-height: 16px"><span
													style="width: 100%; float: left; font-size: 13px; font-weight: bold"> <input type="text" class="departflight" name="to_ac[<?=$segment_details_v['origin'];?>]" value="<?=@$segment_details_v['to_airport_name']?>(<?=@$segment_details_v['to_airport_code']?>)" /></span>
													<span style="width: 100%; float: left"> <?php echo $destination_terminal;?></span>
													<span style="width: 100%; float: left; font-weight: bold">  <input type="text" name="arr_date[<?=$segment_details_v['origin'];?>]" value="<?php echo $segment_details_v['arrival_datetime']; ?>" /></span></td>
												<td>
													<!-- <span style="width:100%; float:left">Non-Stop</span> -->
													<span style="width: 100%; float: left"><?php echo $segment_details_v['total_duration'];?></span>
												</td>
											</tr>
									 <?php
												
}
											}
										}
										?>	
									</table>
									</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td colspan="2" 
										style="padding: 10px; border: 1px solid #cccccc; font-size: 14px; font-weight: bold;">Travellers
										Information <span style="padding: 2px" class="add-row btn-success">Add Extra Pax</span></td>

								</tr>
								<tr>
									<td colspan="5" style="border: 1px solid #cccccc;">
										<table id="pax_table" width="100%" cellpadding="5"
											style="padding: 10px; font-size: 14px;">
											<tr>
												<td><strong>Title</strong></td>
												<td><strong>First Name</strong></td>
												<td><strong>Last Name</strong></td>
												<td><strong>Ticket No</strong></td>
												<td><strong>DOB</strong></td>
												<?php if($booking_details['is_domestic'] =='' ){ ?>
												<td><strong>Passport No</strong></td>
												<td><strong>Issuing Country</strong></td>
												<td><strong>Dtae of Exp.</strong></td>
												<?php } ?>


												<td>
												<strong>Baggages/price</strong>
												</td>
												<td>
												<strong>Meals/price</strong>
												</td>
												<td>
												<strong>Seat No/price</strong>
												</td>
												
											</tr>
									 <?php
										
										$booking_transaction_details_value = $booking_transaction_details [0];
										//$booking_transaction_details_value_r = $booking_transaction_details [1];
										//debug($booking_transaction_details_value_r);die();
										
										if (isset ( $booking_transaction_details_value ['booking_customer_details'] )) {
											foreach ( $booking_transaction_details_value ['booking_customer_details'] as $cus_k => $cus_v ) {
												//debug($cus_v);die;
												?>
										<tr >
										<?php if (strtolower($cus_v['passenger_type']) == 'infant') { ?>
											<td><select class="form-control" name="pax_title[<?=$cus_v['origin'];?>]" required>
												<?= generate_options(get_enum_list('title'), (array) @$cus_v['title']) ?>                                                      
											</select></td>
		                                   <td>
		                                   <input type="text" name="pax_firstname[<?=$cus_v['origin'];?>]" value="<?=@$cus_v['first_name'];?>">	
		                                   </td>
		                                   <td>
		                                   <input type="text" name="pax_lastname[<?=$cus_v['origin'];?>]" value="<?=@$cus_v['last_name'];?>">	
		                                   </td>
		                                   <!-- <?php echo $cus_v['first_name'].'  '.($cus_v['middle_name']!==''? $cus_v['middle_name'].' ':'').$cus_v['last_name'];?>(Infant) --></td>
		                                 <?php }else{?>
		                                 <td><select class="form-control" name="pax_title[<?=$cus_v['origin'];?>]" required>
												<?= generate_options(get_enum_list('title'), (array) @$cus_v['title']) ?>                                                      
											</select></td>
		                                   <td>
		                                   <input type="text" name="pax_firstname[]" value="<?=@$cus_v['first_name']?>">
		                                   <input type="hidden" name="pax_origin[]" value="<?=@$cus_v['origin'];?>">	
		                                   </td><td>
		                                   <input type="text" name="pax_lastname[]" value="<?=@$cus_v['last_name'];?>">	
		                                   
		                                 </td>
		                                 <?php } ?>
										 <td><input type="text" name="ticket_number[]" value="<?=@$cus_v['TicketNumber'];?>" >
										 </td>
										 <td><input type="text" name="DOB[]" value="<?=@$cus_v['date_of_birth'];?>" >
										 </td>
										 <!--Passport details-->
										 <?php if($booking_details['is_domestic'] =='' ){ ?>

										 <td><input type="text" name="passport_No[]" value="<?=@$cus_v['passport_number'];?>" >
										 </td>
										 <td><input type="text" name="pass_iss_cont[]" value="<?=@$cus_v['passport_issuing_country'];?>" >
										 </td>
										 <td><input type="text" name="pass_exp_date[]" value="<?=@$cus_v['passport_expiry_date'];?>" >
										 </td>
										 <?php } ?>
										 <!--Extra services-->
										<td>
										<input type="text" name="ticket_bagg_q[]" value="<?=@$booking_transaction_details_value['extra_service_details']['baggage_details']['details'][$cus_v['origin']][0]['description'];?>" >
										<input type="text" name="ticket_bagg_p[]" value="<?=@$booking_transaction_details_value['extra_service_details']['baggage_details']['details'][$cus_v['origin']][0]['price'];?>" >
										<?php if($booking_details['trip_type'] == 'circle' && $booking_details['is_domestic'] =='') { ?>
											<br><br><lable>Return Baggages/price</lable><br><br>
											<input type="text" name="ticket_bagg_q_r[]" value="<?=@$booking_transaction_details_value['extra_service_details']['baggage_details']['details'][$cus_v['origin']][1]['description'];?>" >
										<input type="text" name="ticket_bagg_p_r[]" value="<?=@$booking_transaction_details_value['extra_service_details']['baggage_details']['details'][$cus_v['origin']][1]['price'];?>" >
										<?php } ?>
										 </td>

										 <td><input type="text" name="ticket_meals_q[]" value="<?=@$booking_transaction_details_value['extra_service_details']['meal_details']['details'][$cus_v['origin']][0]['description'];?>" >
										<input type="text" name="ticket_meals_p[]" value="<?=@$booking_transaction_details_value['extra_service_details']['meal_details']['details'][$cus_v['origin']][0]['price'];?>" >
										<?php if($booking_details['trip_type'] == 'circle' && $booking_details['is_domestic'] =='') { exit("I am Here"); ?>
											<br><br><lable>Return Meals/price</lable><br><br>

											<input type="text" name="ticket_meals_q_r[]" value="<?=@$booking_transaction_details_value['extra_service_details']['meal_details']['details'][$cus_v['origin']][1]['description'];?>" >
										<input type="text" name="ticket_meals_p_r[]" value="<?=@$booking_transaction_details_value['extra_service_details']['meal_details']['details'][$cus_v['origin']][1]['price'];?>" >
										<?php } ?>
										 </td>

										 <td><input type="text" name="ticket_seat_q[]" value="<?=@$booking_transaction_details_value['extra_service_details']['seat_details']['details'][$cus_v['origin']][0]['code'];?>" >
										<input type="text" name="ticket_seat_p[]" value="<?=@$booking_transaction_details_value['extra_service_details']['seat_details']['details'][$cus_v['origin']][0]['price'];?>">
										 <?php if($booking_details['trip_type'] == 'circle' && $booking_details['is_domestic'] =='') { ?>
											<br><br><lable>Return Seats/price</lable><br><br>
											<input type="text" name="ticket_seat_q_r[]" value="<?=@$booking_transaction_details_value['extra_service_details']['seat_details']['details'][$cus_v['origin']][1]['code'];?>" >
										<input type="text" name="ticket_seat_p_r[]" value="<?=@$booking_transaction_details_value['extra_service_details']['seat_details']['details'][$cus_v['origin']][1]['price'];?>">
											<?php } ?>
										 </td>
												
											</tr>
										 <?php
											
											}
										}
										// } ?>
									</table>
									</td>
									<td></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr><td colspan="2" style="padding: 10px; border: 1px solid #cccccc; font-size: 14px; font-weight: bold;">Extra Passenger Charges</td></tr>
								<tr>
								   <td colspan="5" style="border: 1px solid #cccccc;">
								      <table width="100%" cellpadding="5" style="padding: 10px; font-size: 14px;">
								         <tbody>
								         	<tr>
								               <td>Total Extra Pax. Fare</td>
								               <td><input type="text" value="<?php echo @$booking_details['booking_transaction_details'][0]['extra_pax_fare']?>" id="extra_pax_charge" name="extra_pax_charge"></td>
								            </tr>
								         </tbody>
								      </table>
								   </td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
                                
								<!-- Extra Service Starts -->
								<?php if(isset($booking_transaction_details_value['extra_service_details']['baggage_details']) == true && valid_array($booking_transaction_details_value['extra_service_details']['baggage_details']) == true){
											$baggage_details = $booking_transaction_details_value['extra_service_details']['baggage_details'];
									?>
									
									<tr>
										<td>&nbsp;</td>
									</tr>
								<?php }?>
								<?php if(isset($booking_transaction_details_value['extra_service_details']['meal_details']) == true && valid_array($booking_transaction_details_value['extra_service_details']['meal_details']) == true){
											$meal_details = $booking_transaction_details_value['extra_service_details']['meal_details'];
											$meal_type = end($meal_details['details']);
											
											$meal_type = $meal_type[0]['type'];
											if($meal_type == 'static'){
												$meal_type_label = 'Meal Preference';
											} else{
												$meal_type_label = 'Meal Information';
											}
									?>

									<tr>
										<td>&nbsp;</td>
									</tr>
								<?php }?>
								<?php if(isset($booking_transaction_details_value['extra_service_details']['seat_details']) == true && valid_array($booking_transaction_details_value['extra_service_details']['seat_details']) == true){
											$seat_details = $booking_transaction_details_value['extra_service_details']['seat_details'];
											$seat_type = end($seat_details['details']);
											$seat_type =  $seat_type[0]['type'];
											if($seat_type == 'static'){
												$seat_type_label = 'Seat Preference';
											} else{
												$seat_type_label = 'Seat Information';
											}
									?>

									<tr>
										<td>&nbsp;</td>
									</tr>
								<?php }?>
								<!-- Extra Service Ends -->

								
							<?php  if(count($booking_transaction_details)) { ?>
							<tr>
									<td colspan="3"
										style="padding: 10px; border: 1px solid #cccccc; font-size: 14px; font-weight: bold;">Price	Summary</td>

								</tr>
								<tr>
									<td style="border: 1px solid #cccccc;">
										<table width="100%" cellpadding="5"
											style="padding: 10px; font-size: 14px;">
											<tr style="font-size: 15px;">
												<td colspan="5" align="right"><strong>Extra Markup</strong></td>
												<td><strong><?=@$booking_details['currency']?>  <input type="text" name="extra_admin_markup" value="<?=@$booking_details['extra_admin_markup']?>" /></strong></td>
											</tr>
										</table>
									</td>
									<td style="border: 1px solid #cccccc;">
										<table width="100%" cellpadding="5"
											style="padding: 10px; font-size: 14px;">
											<tr style="font-size: 15px;">
												<td colspan="5" align="right"><strong>Extra GST</strong></td>
												<td><strong><?=@$booking_details['currency']?>  <input type="text" name="extra_gst" value="<?=@$booking_details['extra_gst']?>" /></strong></td>
											</tr>
										</table>
									</td>
									<td style="border: 1px solid #cccccc;">
										<table width="100%" cellpadding="5"
											style="padding: 10px; font-size: 14px;">
											<tr style="font-size: 15px;">
												<td colspan="5" align="right"><strong>Total Fare</strong></td>
												<td><strong><?=@$booking_details['currency']?>  <input type="text" name="grand_total" value="<?=@$booking_details['grand_total']?>"></strong></td>
											</tr>
										</table>
									</td>
									<td></td>
								</tr> 
								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td
										style="padding: 10px; border: 1px solid #cccccc; font-size: 14px; font-weight: bold;">Remarks</td>

								</tr>
								<tr>
									<td style="border: 1px solid #cccccc;">
										<table width="100%" cellpadding="5"
											style="padding: 10px; font-size: 14px;">
											<tr style="font-size: 15px;">
												<td colspan="5"><textarea cols="50" rows="5" name="admin_edir_remark"></textarea></td>
											</tr>
										</table>
									</td>
									
								</tr> 
							<?php } ?>                           
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</tbody>
</table>


<!-- Return Ticket -->
<?php if(count($booking_transaction_details) == 2) {?>
<table id="table_return" style="border-collapse: collapse; background: #ffffff; font-size: 14px; margin: 0 auto; font-family: arial;" width="100%" cellpadding="0" cellspacing="0" border="0">
	<tbody>
		<tr>
			<td style="border-collapse: collapse; padding: 10px 20px 20px">
				<table width="100%" style="border-collapse: collapse;"
					cellpadding="0" cellspacing="0" border="0">
					
					<tr>
						<td style="padding: 10px;">
							<table cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse: collapse;">
                            
								<tr>
						<td
							style="font-size: 22px; line-height: 30px; width: 100%; display: block; font-weight: 600; text-align: center">E-Ticket<?php echo $Return;?></td>
					</tr>
					<tr>
						<td>
							<table width="100%" style="border-collapse: collapse;"
								cellpadding="0" cellspacing="0" border="0">
								<tr>
									<td style="padding: 10px; width: 65%"><img
										style="max-height: 56px"
										src="<?=$GLOBALS['CI']->template->domain_images($data['logo'])?>"></td>
									<td style="padding: 10px; width: 35%;">
										<table width="100%"
											style="border-collapse: collapse; text-align: right; line-height: 15px;"
											cellpadding="0" cellspacing="0" border="0">

											<tr>
												<td style="font-size: 14px;"><span
													style="width: 100%; float: left"><?php echo $data['address'];?></span>

												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
								<tr>
									<td width="50%"
										style="padding: 10px; border: 1px solid #cccccc; font-size: 14px; font-weight: bold;">Reservation
										Lookup</td>
								</tr>
                                
								<tr>
									<td style="border: 1px solid #cccccc;">
										<table width="100%" cellpadding="5"
											style="padding: 10px; font-size: 14px;">
                                            
											<tr>
												<td><strong>Booking Reference</strong></td>
												<td><strong>Booking ID</strong></td>
												<td><strong>PNR</strong></td>
												<td><strong>Booking Date</strong></td>
												<td><strong>Status</strong></td>
											</tr>
											<tr>

												<td><?=@$booking_details['app_reference']?></td>
												<td><?=@$booking_transaction_details[1]['book_id']?></td>
												<td><input type="text" name="r_pnr" value="<?=@$booking_transaction_details[1]['book_id']?>"></td>
												<td><?=app_friendly_absolute_date(@$booking_details['booked_date'])?></td>
												<td>
													
													<select class="form-control" name="r_status" required>
														<?= generate_options(get_enum_list('booking_status_options'), array(@$booking_transaction_details[1]['status'])) ?>
													</select>
													
											
												</td>
											<?php
	switch (@$booking_transaction_details[1] ['status']) {
		case 'BOOKING_CONFIRMED' :
			echo 'CONFIRMED';
			break;
		case 'BOOKING_CANCELLED' :
			echo 'CANCELLED';
			break;
		case 'BOOKING_FAILED' :
			echo 'FAILED';
			break;
		case 'BOOKING_INPROGRESS' :
			echo 'INPROGRESS';
			break;
		case 'BOOKING_INCOMPLETE' :
			echo 'INCOMPLETE';
			break;
		case 'BOOKING_HOLD' :
			echo 'HOLD';
			break;
		case 'BOOKING_PENDING' :
			echo 'PENDING';
			break;
		case 'BOOKING_ERROR' :
			echo 'ERROR';
			break;
	}
	
	?>
											</strong></td>
										
										</table>
									</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td
										style="padding: 10px; border: 1px solid #cccccc; font-size: 14px; font-weight: bold;">Journey
										Information</td>

								</tr>
								<tr>
									<td width="100%" style="border: 1px solid #cccccc;">
										<table width="100%" cellpadding="5"
											style="padding: 10px; font-size: 14px;">
											<tr>
												<td><strong>Flight</strong></td>
												<td><strong>AirlinePNR</strong></td>
												<td><strong>Departure</strong></td>
												<td><strong>Arrival</strong></td>
												<td><strong>Journey Time</strong></td>
											</tr>
											<tr>
										<?php
	if (isset ( $booking_transaction_details ) && $booking_transaction_details != "") {
		// debug($return_segment_details);exit;
		foreach ( $return_segment_details as $segment_details_k => $segment_details_v ) {
			
			$itinerary_details_attributes = json_decode ( $segment_details_v ['attributes'], true);
			$airline_terminal_origin = @$itinerary_details_attributes['departure_terminal'];
			$airline_terminal_destination = @$itinerary_details_attributes['arrival_terminal'];
			$origin_terminal = '';
			$destination_terminal = '';
			if ($airline_terminal_origin != '') {
				$origin_terminal = 'Terminal ' . $airline_terminal_origin;
			}
			if ($airline_terminal_destination != '') {
				$destination_terminal = 'Terminal ' . $airline_terminal_destination;
			}
			
			if (valid_array ( $segment_details_v ) == true) {
				?>
                              
                                 <?php if($booking_details['trip_type'] == 'circle' && $booking_details['is_domestic'] == true){?>
                                  <?php }?>
                                        <td><img
													style="max-height: 30px"
													src="<?=SYSTEM_IMAGE_DIR.'airline_logo/'.$segment_details_v['airline_code'].'.gif'?>"
													alt="flight-logo" /> <span style="width: 100%; float: left">
													<select class="form-control" name="carrier[<?=$segment_details_v['origin'];?>]" required>
                                    <?= generate_options($airline_list, (array) @$segment_details_v['airline_code']) ?>                               
                                </select></span>
													<span
													style="width: 100%; float: left; font-size: 13px; font-weight: bold"><input type="text" name="flight_number[<?=$segment_details_v['origin'];?>]" value="<?php echo $segment_details_v['flight_number'];?>" /></span></td>
													<td><input type="text" name="airline_pnr[<?=$segment_details_v['origin'];?>]" value="<?=$segment_details_v['airline_pnr'];?>"></td>
												<td style="line-height: 16px"><span
													style="width: 100%; float: left; font-size: 13px; font-weight: bold"> <input type="text" class="fromflight" name="from_ac[<?=$segment_details_v['origin'];?>]" value="<?=@$segment_details_v['from_airport_name'] ?>(<?=@$segment_details_v['from_airport_code']?>)" /></span>
													<span style="width: 100%; float: left"><?php echo $origin_terminal;?></span>
													<span style="width: 100%; float: left; font-weight: bold"> <input type="text" name="dep_date[<?=$segment_details_v['origin'];?>]" value="<?php echo $segment_details_v['departure_datetime']; ?>" /></span></td>
												<td style="line-height: 16px"><span
													style="width: 100%; float: left; font-size: 13px; font-weight: bold"> <input type="text" class="departflight" name="to_ac[<?=$segment_details_v['origin'];?>]" value="<?=@$segment_details_v['to_airport_name']?>(<?=@$segment_details_v['to_airport_code']?>)" /></span>
													<span style="width: 100%; float: left"> <?php echo $destination_terminal;?></span>
													<span style="width: 100%; float: left; font-weight: bold">  <input type="text" name="arr_date[<?=$segment_details_v['origin'];?>]" value="<?php echo $segment_details_v['arrival_datetime']; ?>" /></span></td>
												<td>
													<!-- <span style="width:100%; float:left">Non-Stop</span> -->
													<span style="width: 100%; float: left"><?php echo $segment_details_v['total_duration'];?></span>
												</td>
											</tr>
									 <?php
			
}
		}
	}
	?>	
									</table>
									</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td
										style="padding: 10px; border: 1px solid #cccccc; font-size: 14px; font-weight: bold;">Travellers
										Information <span style="padding: 2px" class="add-row btn-success" data-type="dom_ret">Add Extra Pax</span></td>

								</tr>
								<tr>
									<td style="border: 1px solid #cccccc;">
										<table width="100%" cellpadding="5"
											style="padding: 10px; font-size: 14px;" id="pax_table_1">
											<tr>
												<td><strong>Title</strong></td>
												<td><strong>First Name</strong></td>
												<td><strong>Last Name</strong></td>
												<td><strong>Ticket No</strong></td>
												<td><strong>DOB</strong></td>
												<?php if($booking_details['is_domestic'] =='' ){ ?>
												<td><strong>Passport No</strong></td>
												<td><strong>Issuing Country</strong></td>
												<td><strong>Dtae of Exp.</strong></td>
												<?php } ?>
												<td>
												<strong>Baggages/price</strong>
												</td>
												<td>
												<strong>Meals/price</strong>
												</td>
												<td>
												<strong>Seat No/price</strong>
												</td>
											</tr>
									 <?php
	
	$booking_transaction_details_value = $booking_transaction_details [1];
	//echo debug($booking_transaction_details_value);exit;
	// foreach($booking_transaction_details as $key => $value){
	
	// echo debug($value['booking_customer_details']);exit;
	if (isset ( $booking_transaction_details_value ['booking_customer_details'] )) {
		foreach ( $booking_transaction_details_value ['booking_customer_details'] as $cus_k => $cus_v ) {
			
			?>
										<tr>
										<?php if (strtolower($cus_v['passenger_type']) == 'infant') { ?>
		                                   <td><select class="form-control" name="pax_title[<?=$cus_v['origin'];?>]" required>
												<?= generate_options(get_enum_list('title'), (array) @$cus_v['title']) ?>                                                      
											</select></td> 
		                                   <td>
		                                   <input type="text" name="r_pax_firstname[<?=$cus_v['origin'];?>]" value="<?=@$cus_v['first_name'];?>">	
		                                   </td>
		                                   <td>
		                                   <input type="text" name="r_pax_lastname[<?=$cus_v['origin'];?>]" value="<?=@$cus_v['last_name'];?>">	
		                                   </td>
		                                   <!-- <?php echo $cus_v['first_name'].'  '.($cus_v['middle_name']!==''? $cus_v['middle_name'].' ':'').$cus_v['last_name'];?>(Infant) --></td>
		                                 <?php }else{?>
											<td><select class="form-control" name="pax_title[<?=$cus_v['origin'];?>]" required>
												<?= generate_options(get_enum_list('title'), (array) @$cus_v['title']) ?>                                                      
											</select></td> 
		                                   <td>
		                                   <input type="text" name="r_pax_firstname[]" value="<?=@$cus_v['first_name']?>">
		                                   <input type="hidden" name="r_pax_origin[]" value="<?=@$cus_v['origin'];?>">	
		                                   </td><td>
		                                   <input type="text" name="r_pax_lastname[]" value="<?=@$cus_v['last_name'];?>">	
		                                   
		                                 </td>
		                                 <?php } ?>
										 <td><input type="text" name="r_ticket_number[]" value="<?=@$cus_v['TicketNumber'];?>" >
										 </td>
										 <td><input type="text" name="r_DOB[]" value="<?=@$cus_v['date_of_birth'];?>" >
										 </td>
										 <!--Passport details-->
										 <?php if($booking_details['is_domestic'] =='' ){ ?>

										 <td><input type="text" name="r_passport_No[]" value="<?=@$cus_v['passport_number'];?>" >
										 </td>
										 <td><input type="text" name="r_pass_iss_cont[]" value="<?=@$cus_v['passport_issuing_country'];?>" >
										 </td>
										 <td><input type="text" name="r_pass_exp_date[]" value="<?=@$cus_v['passport_expiry_date'];?>" >
										 </td>
										 <?php } ?>
										 <td>
										<input type="text" name="r_ticket_bagg_q[]" value="<?=@$booking_transaction_details_value['extra_service_details']['baggage_details']['details'][$cus_v['origin']][0]['description'];?>" >
										<input type="text" name="r_ticket_bagg_p[]" value="<?=@$booking_transaction_details_value['extra_service_details']['baggage_details']['details'][$cus_v['origin']][0]['price'];?>" >
										 </td>
										 <td><input type="text" name="r_ticket_meals_q[]" value="<?=@$booking_transaction_details_value['extra_service_details']['meal_details']['details'][$cus_v['origin']][0]['description'];?>" >
										<input type="text" name="r_ticket_meals_p[]" value="<?=@$booking_transaction_details_value['extra_service_details']['meal_details']['details'][$cus_v['origin']][0]['price'];?>" >
										 </td>
										 <td><input type="text" name="r_ticket_seat_q[]" value="<?=@$booking_transaction_details_value['extra_service_details']['seat_details']['details'][$cus_v['origin']][0]['code'];?>" >
										<input type="text" name="r_ticket_seat_p[]" value="<?=@$booking_transaction_details_value['extra_service_details']['seat_details']['details'][$cus_v['origin']][0]['price'];?>">
										 </td>
											</tr>
										 <?php
		
}
	}
	// } ?>
									</table>
									</td>
									<td></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
							
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</tbody>
</table>

<?php } ?>

<?php 
	$app_reference = $booking_details['app_reference'];
	$booking_source = $booking_details['booking_source'];
	$booking_status = $booking_details['status'];
?>
<input type="hidden" name="module" value="<?=$current_module;?>">
<table 	style="border-collapse: collapse; font-size: 14px; margin: 10px auto; font-family: arial;"
	width="70%" cellpadding="0" cellspacing="0" border="0">
	<tbody>
		<tr>
			<td align="center" >
			<input style="background: #418bca; height: 34px; padding: 10px; border-radius: 4px; border: none; color: #fff; margin: 0 2px;" type="submit" value="Update">
			</td>
		</tr>
	</tbody>
</table>
</div>
</form>
<!-- <div class="container" >
 <div class='element' id='div_1'>
  <input type='text' placeholder='Enter your skill' id='txt_1' >&nbsp;<span class='add'>Add Skill</span>
 </div>
</div> -->
<script type="text/javascript">
$(document).ready(function(){
	            $.widget("custom.catcomplete", $.ui.autocomplete, {
    _create: function() { this._super(), this.widget().menu("option", "items", "> :not(.ui-autocomplete-category)") },
    _renderMenu: function(t, e) {
        var r = this,
            a = "";
        $.each(e, function(e, o) {
            var n;
           o.category != a && (t.append("<li class='ui-autocomplete-category'>" + o.category + "</li>"), a = o.category), n = r._renderItemData(t, o), o.category && n.attr("aria-label", o.category + " : " + o.label)
        })
    }
});

 $(document).on("focus", ".fromflight, .departflight", function () {
    var cache = {};
     $(this).catcomplete({
            open: function(event, ui) {
            $('.ui-autocomplete').off('menufocus hover mouseover');
        },
            source: function(request, response) {
                var term = request.term;
                if (term in cache) {
                    response(cache[term]);
                    return
                } else {
                    $.getJSON(app_base_url + "index.php/flight/get_airport_code_list", request, function(data, status, xhr) {
                        if ($.isEmptyObject(data) == true && $.isEmptyObject(cache[""]) == false) {
                            data = cache[""]
                        } else {
                            cache[term] = data;
                            response(cache[term])
                        }
                    })
                }
            },
            minLength: 0,
            autoFocus: false,
            select: function(event, ui) {
                var label = ui.item.label;
                var category = ui.item.category;
                //$(this).siblings('.loc_id_holder').val(ui.item.id);
                auto_focus_input(this.id)
                $(this).val(ui.item.id);
            },
            change: function(ev, ui) {
                if (!ui.item) {
                    $(this).val("")
                }
            }
        }).bind('focus', function() {
            $(this).catcomplete("search")
        }).catcomplete("instance")._renderItem = function(ul, item) {
            var auto_suggest_value = highlight_search_text(this.term.trim(), item.value, item.label);
            var top = 'Top Searches';
            return $("<li class='custom-auto-complete'>").append('<a><img class="flag_image" src="' + '">' + auto_suggest_value + '</a>').appendTo(ul)
        };
		
});
        $(".add-row").click(function(){
			var fd_prx = "";
			<?php if($booking_details['trip_type'] == 'circle' && $booking_details['is_domestic'] ==1) { ?>
				if($(this).data("type")=="dom_ret")
					fd_prx = "r_";
			<?php } ?>
            var markup = '<tr><hr><td><select class="form-control" name="pax_title[]" required><?= generate_options(get_enum_list('title'), array('1')) ?></select></td>';
				markup += "<td><input type='text' name='"+fd_prx+"pax_firstname[]' value='' placeholder = 'Pax first Name'></td><td><input type='text' name='"+fd_prx+"pax_lastname[]' value='' placeholder = 'Pax last Name'></td><td><input type='text' name='"+fd_prx+"ticket_number[]' value='' placeholder = 'Ticket No.'></td><td><input type='text' name='"+fd_prx+"DOB[]' value='' placeholder = 'Dob'></td>";
            <?php if($booking_details['is_domestic'] == '') {?>

            markup +="<td><input type='text' name='passport_No[]' value='' placeholder = 'Passport no'></td><td><input type='text' name='pass_iss_cont[]' value='' placeholder = 'Passport iss. country'></td><td><input type='text' name='pass_exp_date[]' value='' placeholder = 'Passport exp date'></td>";

            <?php } ?>

            markup +="<td><input type='text' name='"+fd_prx+"ticket_bagg_q[]' value='' placeholder = 'Baggage Qunt.'><input type='text' name='"+fd_prx+"ticket_bagg_p[]' value='' placeholder = 'Baggage Price'>";

            <?php if($booking_details['trip_type'] == 'circle' && $booking_details['is_domestic'] =='') { ?>
        		markup +="<br><br><lable>Return Baggages/price</lable><br><br><input type='text' name='ticket_bagg_q_r[]' value='' placeholder = 'Return Baggage Qunt.'><input type='text' name='ticket_bagg_p_r[]'' value='' placeholder = 'Return Baggage Price'>";
        	<?php } ?>
        	markup += "</td>";

        	markup += "<td><input type='text' name='"+fd_prx+"ticket_meals_q[]' value='' placeholder = 'Meals'><input type='text' name='"+fd_prx+"ticket_meals_p[]' value='' placeholder = 'Maels Price'>";
        	 <?php if($booking_details['trip_type'] == 'circle' && $booking_details['is_domestic'] =='') { ?>
        	 	markup += "<br><br><lable>Return Meals/price</lable><br><br><input type='text' name='ticket_meals_q_r[]' value='' placeholder = 'Meals'><input type='text' name='ticket_meals_p_r[]' value='' placeholder = 'Return Maels Price'>";
        	 <?php } ?>	
        	 markup +="</td>";

        	 markup += "<td><input type='text' name='"+fd_prx+"ticket_seat_q[]' value='' placeholder = 'Seat No.'><input type='text' name='"+fd_prx+"ticket_seat_p[]' value='' placeholder = 'Seat Price'>";
        	<?php if($booking_details['trip_type'] == 'circle' && $booking_details['is_domestic'] =='') { ?>
    	 		
	 		markup += "<br><br><lable>Return Seats/price</lable><br><br><input type='text' name='ticket_seat_q_r[]' value='' placeholder = 'Seat No.'><input type='text' name='ticket_seat_p_r[]' value='' placeholder = 'Return Seat Price'>";

    	 	<?php } ?>
            markup += "<br><span style='padding: 2px' class='remove-row btn-warning'>Remove Pax</span></td></tr>";

            $("#pax_table tbody").append(markup);
			if($(this).data("type")=="dom_ret")
				$("#pax_table_1 tbody").append(markup);
        });
        
        $(document).on('click','.remove-row',function(){
        	$(this).closest('tr').remove();
        })
    }); 
</script>