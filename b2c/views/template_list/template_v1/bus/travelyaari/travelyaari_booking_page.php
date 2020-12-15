<?php

$template_images = $GLOBALS ['CI']->template->template_images ();
$CUR_Route = ($details ['Route']);
$bus_seats = $details ['Layout'] ['SeatDetails'] ['clsSeat'];
$bus_pickup = $details ['Pickup'] ['clsPickup'];
$CUR_CancellationCharges = $details ['CancellationCharges'] ['clsCancellationCharge'];
$seat_attr ['markup_price_summary'] = $markup_total_fare;
$seat_attr ['total_price_summary'] = $total_fare;
$seat_attr ['domain_deduction_fare'] = $domain_total_fare;
$seat_attr ['default_currency'] = $default_currency;
$seat_count = count ( $pre_booking_params ['seat'] );
$pax_title_options = generate_options ( $pax_title_enum, array (
		MR_TITLE 
), true );
$gender_options = generate_options ( $gender_enum, array (
		1 
) );
$mandatory_filed_marker = '<sup class="text-danger">*</sup>';
?>
<section class="passenger-details">
	<div class="container">
		<div class="row">
			<div class="col-md-3">
				<div class="panel panel-default b-r-0 m-0">
					<div class="panel-body p-b-0">
						<h1 class="h3 m-t-0">Travel Details</h1>
						<h2 class="h4 text-p">Bus Price</h2>
					</div>
					<table class="table">
						<tbody>
							<tr>
								<td>Total Seat(s)</td>
								<td class="text-right"><span class="h4 text-i"><?=$seat_count?></span></td>
							</tr>
							<tr>
								<td>Grand Total</td>
								<td class="text-right"><?=$default_currency_symbol?> <span class="h4 text-i"><?=$markup_total_fare?></span></td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="panel panel-default b-r-0 m-0">
					<div class="panel-body p-t-0 p-b-0 text-center">
						<h2 class="h4 text-p text-left">Bus Details</h2>
						<div class="clearfix">
							<div class="">
								<h3 class="h5"><b><?=$CUR_Route['CompanyName']?></b></h3>
							</div>
						</div>
					</div>
                    <div class="bus_cmpnyDtls table-responsive">
					<table class="table">
						<tbody>
							<tr>
								<td><?=$CUR_Route['FromCityName']?> -> <?=$CUR_Route['ToCityName']?></td>
							</tr>
							<tr>
								<td>Seat(<?=$seat_count?>) : <?=implode(',', $pre_booking_params['seat'])?></td>
							</tr>
							<tr>
								<?php $bus_details = explode(',', $CUR_Route['BusLabel']);?>
								<td><?=@$bus_details[0].",".@$bus_details[1].",<br>".@$bus_details[2].",".@$bus_details[3]?></td>
							</tr>
							<tr>
								<td>Departure : <?=local_date($CUR_Route['DepartureTime'])?>(<?=get_time_duration_label(calculate_duration($CUR_Route['DepartureTime'], $CUR_Route['ArrivalTime']))?> Journey)</td>
							</tr>
							<tr>
								<td>Pickup @ <?php $pickup = $bus_pickup[$pre_booking_params['pickup_id']]; $pickup_string = ($pickup['PickupName'].' - '.get_time($pickup['PickupTime'])); echo $pickup_string; $pickup_string .= ', Address : '.$pickup['Address'].', Landmark : '.$pickup['Landmark'].', Phone : '.$pickup['Phone'];?></td>
							</tr>
						</tbody>
					</table>
                    </div>
				</div>
			</div>
			<div class="col-md-9">
				<div class="panel panel-default b-r-0">
					<div class="panel-body">
						<form action="<?php echo base_url().'index.php/bus/pre_booking/'.$search_data['search_id'] ?>" autocomplete="off" method="POST">
							<div class="clearfix">
								<div class="pull-left">
									<h1 class="h3 m-t-0 txtwrapRow"><img src="<?=$template_images?>icons/angle-arrow-down.png" alt="Angle Arrow Down Icon"> Passenger Information</h1>
								</div>
								<!--<div class="pull-right">
									<a href="#" class="btn btn-p b-r-0">Login</a>
								</div>
							--></div>
							<hr>
							<!-- Start Dynamics -->
							<div class="row">
								<div class="col-md-2">
									Seat Details
								</div>
								<div class="col-md-10 mobHide">
									<div class="row">
										<div class="col-md-2">
											<div class="form-group">
												Title <sup class="text-danger">*</sup>
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												Name <sup class="text-danger">*</sup>
											</div>
										</div>
									<!-- 	<div class="col-md-3">
											<div class="form-group">
												Gender <sup class="text-danger">*</sup>
											</div>
										</div> -->
										<div class="col-md-3">
											<div class="form-group">
												Age <sup class="text-danger">*</sup>
											</div>
										</div>
									</div>
								</div>
							</div>
							<?php $i = 0;
							$datepicker_list = array();
							$lead_pax_details = @$pax_details[0];
							for($i=0; $i<$seat_count; $i++) {
								$cur_pax_info = is_array($pax_details) ? array_shift($pax_details) : array();
								$i_seat = $bus_seats[$pre_booking_params['seat'][$i]];
								$attr['Fare']		= $i_seat['Fare'];
								$attr['Markup_Fare']= $i_seat['Markup_Fare'];
								//verify seat availability
							?>
							<div class="row">
							<div class="hide hidden_pax_details">
                        		<input type="hidden" name="gender[]" value="1" class="pax_gender">
                        	</div>
								<div class="col-md-2">
									<?php
									$i_seat_title = array();
									if ($i_seat['IsAC'] != 'false') {
										$attr['IsAcSeat']	= false;
										$i_seat_title[] = 'NON_AC';
									} else {
										$attr['IsAcSeat']	= true;
										$i_seat_title[] = 'AC';
									}
									if (intval($i_seat['Deck']) == 1) {
										$i_seat_title[] = $i_seat['SeatNo'].' Lower Deck';
									} else {
										$i_seat_title[] = $i_seat['SeatNo'].' Upper Deck';
									}
									$i_seat_title[] = ($i_seat['Gender'] != 'F' ? '' : 'Reserved For Ladies');
									if ($i_seat['IsSleeper'] == 'true') {
										$i_seat_type = 'sleeper-A.jpg';
										$attr['SeatType']	= 'sleeper';
									} else {
										$attr['SeatType']	= 'seat';
										$i_seat_type = 'seat-A.jpg';
									}
									$i_seat_title[] = ' @ '.$domain_currency_obj->get_currency_symbol($pre_booking_params['default_currency']).' '.$i_seat['Markup_Fare'];
									echo '<img src="'.$template_images.'seats/'.$i_seat_type.'" title="'.implode(' ', $i_seat_title).'" class="hand-cursor">';
									?> - 
									Seat <?=$i_seat['SeatNo']?>
								</div>
								<div class="col-md-10 nopadMob">
									<div class="row">
										<div class="col-md-2">
											<div class="form-group">
												<select class="form-control" required name="pax_title[]">
												<?=$pax_title_options?>
												</select>
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<input value="<?=@$cur_pax_info['first_name']?>" type="text" maxlength="45" id="contact-name"  name="contact_name[]"class="form-control b-r-0" required placeholder="Name">
												
											</div>
										</div>
									<!--  class="col-md-3">
											<div class="form-group">
												<?php if ($i_seat['Gender'] == 'F') {
													echo 'Female <input type="hidden" name="gender[]" value="2">';
												} if ($i_seat['Gender'] == 'M') {
													echo 'Male <input type="hidden" name="gender[]" value="1">';
												} else {
													?>
													<select class="form-control" required name="gender[]">
													<?=$gender_options?>
													</select>
												<?php
												}?>
											</div>
										</div>  -->
										<div class="col-md-3">
											<div class="form-group">
												<input value="<?=intval(get_year_difference(@$cur_pax_info['date_of_birth']))?>" type="text" maxlength="3" max="100" id="age-<?=($i);?>" name="age[]" placeholder="Age" class="form-control b-r-0 numeric" required>
											</div>
										</div>
									</div>
								</div>
							</div>
							<?php
							$seat_attr['seats'][$i_seat['SeatNo']] = $attr;
							}
							?>
							<div class="hide">
								<?php
								/**
								 * Data for booking
								 */
								$dynamic_params_url['RouteScheduleId'] = $pre_booking_params['route_schedule_id'];
								$dynamic_params_url['JourneyDate'] = $pre_booking_params['journey_date'];
								$dynamic_params_url['PickUpID'] = $pre_booking_params['pickup_id'];
								$dynamic_params_url['seat_attr'] = $seat_attr;
								
								$dynamic_params_url['DepartureTime'] = $CUR_Route['DepartureTime'];
								$dynamic_params_url['ArrivalTime'] = $CUR_Route['ArrivalTime'];
								$dynamic_params_url['departure_from'] = $CUR_Route['FromCityName'];
								$dynamic_params_url['arrival_to'] = $CUR_Route['ToCityName'];
								$dynamic_params_url['boarding_from'] = $pickup_string;//
								$dynamic_params_url['dropping_to'] = '';//
								$dynamic_params_url['bus_type'] = $CUR_Route['BusLabel'];
								$dynamic_params_url['operator'] = $CUR_Route['CompanyName'];

								$dynamic_params_url['AdminCommPCT'] = $CUR_Route['CommPCT'];
								$dynamic_params_url['AdminCommAmount'] = $CUR_Route['CommAmount'];

								$dynamic_params_url = serialized_data($dynamic_params_url);
								?>
								<input type="hidden" required="required" name="token"			value="<?=$dynamic_params_url;?>" />
								<input type="hidden" required="required" name="token_key"		value="<?=md5($dynamic_params_url);?>" />
								<input type="hidden" required="required" name="op"				value="book_bus">
								<input type="hidden" required="required" name="booking_source"	value="<?=$pre_booking_params['booking_source']?>" readonly>
							</div>
							<hr>
							<h1 class="h3 m-t-0"><img alt="Angle Arrow Down Icon" src="<?=$template_images?>icons/angle-arrow-down.png"> Contact Details</h1>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="contact-email">Email <sup class="text-danger">*</sup></label>
										<input value="<?=@$lead_pax_details['email']?>" type="email" maxlength="45" name="billing_email" placeholder="To Send E-Ticket" class="form-control b-r-0" id="contact-email" required="required">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="passenger-contact">Mobile <sup class="text-danger">*</sup></label>
										<input value="<?=@$lead_pax_details['phone']?>" type="text" maxlength="10" name="passenger_contact" placeholder="To Send Confirmation Number" class="form-control b-r-0 numeric " id="passenger-contact" required="required">
										<input type="hidden" name="alternate_contact" value="9876543210">
									</div>
								</div>
							</div>
							<!-- <div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="alternate-contact">Alternate Number <sup class="text-danger">*</sup></label>
										<input type="text" maxlength="10" name="alternate_contact" placeholder="Alternate Number" class="form-control b-r-0 numeric " id="alternate-contact" required="required">
									</div>
								</div>
							</div> -->
							<?php
							//If single payment option then hide selection and select by default
							if (count($active_payment_options) == 1) {
								$payment_option_visibility = 'hide';
								$default_payment_option = 'checked="checked"';
							} else {
								$payment_option_visibility = 'show';
								$default_payment_option = '';
							}
							
							?>
							<div class="row <?=$payment_option_visibility?>">
							<?php if (in_array(PAY_NOW, $active_payment_options)) {?>
								<div class="col-md-3">
									<div class="form-group">
										<label for="payment-mode-<?=PAY_NOW?>">
											<input <?=$default_payment_option?> name="payment_method" type="radio" required="required" value="<?=PAY_NOW?>" id="payment-mode-<?=PAY_NOW?>" class="form-control b-r-0" placeholder="Payment Mode">
											Pay Now
										</label>
									</div>
								</div>
							<?php } ?>
							<?php if (in_array(PAY_AT_BANK, $active_payment_options)) {?>
								<div class="col-md-3">
									<div class="form-group">
										<label for="payment-mode-<?=PAY_AT_BANK?>">
											<input <?=$default_payment_option?> name="payment_method" type="radio" required="required" value="<?=PAY_AT_BANK?>" id="payment-mode-<?=PAY_AT_BANK?>" class="form-control b-r-0" placeholder="Payment Mode">
											Pay At Bank
										</label>
									</div>
								</div>
							<?php } ?>
							</div>
							<!-- End Dynamics -->
							<div class="clearfix">
								<div class="pull-right text-right txtwrapRow col-xs-12">
									<div class="checkbox">
										<label>
										
											<input type="checkbox" name="tc" required> I accept the <a href="#">Terms and Conditions</a>
										</label>
									</div>
									<button type="submit" class="btn btn-t b-r-0 confirmBTN">Confirm</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<?php
$GLOBALS['CI']->current_page->set_datepicker($datepicker_list);
?>
