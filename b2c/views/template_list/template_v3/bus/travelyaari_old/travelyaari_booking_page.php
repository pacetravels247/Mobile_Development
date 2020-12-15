<?php
	$template_images = $GLOBALS ['CI']->template->template_images ();
	$CUR_Route = ($details ['Route']);
	$bus_selected_seats = $details ['Layout'] ['SeatDetails'] ['clsSeat'];
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
<style>
	.topssec::after {
	display: none;
	}
</style>
<div class="fldealsec">
	<div class="container">
		<div class="tabcontnue">
			<div class="col-xs-4 nopadding">
				<div class="rondsts success">
					<a class="taba core_review_tab review_tab_marker" id="stepbk1">
						<div class="iconstatus fa fa-eye"></div>
						<div class="stausline">Review</div>
					</a>
				</div>
			</div>
			<div class="col-xs-4 nopadding">
				<div class="rondsts">
					<a class="taba core_travellers_tab"
						id="stepbk2">
						<div class="iconstatus fa fa-group"></div>
						<div class="stausline">Travellers</div>
					</a>
				</div>
			</div>
			<div class="col-xs-4 nopadding">
				<div class="rondsts">
					<a class="taba" id="stepbk3">
						<div class="iconstatus fa fa-money"></div>
						<div class="stausline">Payments</div>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="clearfix"></div>
<div class="alldownsectn">
	<div class="container">
		<div class="ovrgo">
			<div class="bktab2">
				<div class="clearfix"></div>
				<!-- Segment Details Starts-->
				<div class="collapse splbukdets" id="fligtdetails">
					<div class="moreflt insideagain">
						<?php 	//echo $flight_segment_details['segment_full_details'];?>
					</div>
				</div>
				<!-- Segment Details Ends-->
				<div class="clearfix"></div>
				<div class="padpaspotr">
					<!-- Fare Summary  Starts-->
					<div class="col-xs-4 nopadding rit_summery">
						<div class="col-xs-12">
							<a class="pull-right btn btn-sm btn-primary hnf-btn">HNF</a>
							<a class="pull-right btn btn-sm btn-primary snf-btn" style="display:none">SNF</a>
						</div>
						<div id="total-fare-breakdown" class="insiefare">
							<div class="farehd arimobold">Fare Breakdown (<?=$default_currency_symbol?>)</div>
							<div class="fredivs">
								<div class="reptalltftr">
									<div class="reptallt">
										<div class="col-xs-8 nopadding">
											<div class="faresty freshd">Published Fare</div>
										</div>
										<div class="col-xs-4 nopadding">
											<div class="amnter freshd"><?=$summary['total_fare']?></div>
										</div>
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="reptalltftr">
									<div class="reptallt">
										<div class="col-xs-8 nopadding">
											<div class="">Commission</div>
										</div>
										<div class="col-xs-4 nopadding">
											<div class="amnterbig arimobold"><?=$summary['total_commission']?> </div>
										</div>
									</div>
								</div>
								<div class="reptalltftr">
									<div class="reptallt">
										<div class="col-xs-8 nopadding">
											<div class="">TDS On Commission</div>
										</div>
										<div class="col-xs-4 nopadding">
											<div class="amnter freshd"><?=$summary['tds_total_commission']?></div>
										</div>
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="reptalltftr">
									<div class="reptallt">
										<div class="col-xs-8 nopadding">
											<div class="">Markup</div>
										</div>
										<div class="col-xs-4 nopadding">
											<div class="amnterbig arimobold"><?=$summary['total_markup']?> </div>
										</div>
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="reptalltftr">
									<div class="reptallt">
										<div class="col-xs-8 nopadding">
											<div class="">Net Fare</div>
										</div>
										<div class="col-xs-4 nopadding">
											<div class="amnterbig arimobold"><?=$summary['total_netfare']?> </div>
										</div>
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="reptalltftr">
									<div class="reptallt">
										<div class="col-xs-8 nopadding">
											<div class="farestybig">Total Payable</div>
										</div>
										<div class="col-xs-4 nopadding">
											<div class="amnterbig arimobold"><?=$default_currency_symbol?> <?=$summary['total_payable']?> </div>
										</div>
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="reptalltftr text-primary">
									<div class="reptallt">
										<div class="col-xs-8 nopadding">
											<div class="">Total Earning</div>
										</div>
										<div class="col-xs-4 nopadding">
											<div class="amnterbig arimobold"><?=$summary['total_earning']?> </div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="insiefare" id="total-fare-summary">
							<div class="farehd arimobold">Fare Summary</div>
							<div class="fredivs">
								<div class="kindrest">
									<div class="reptallt">
										<div class="col-xs-8 nopadding">
											<div class="faresty freshd">Total Seat(s)</div>
										</div>
										<div class="col-xs-4 nopadding">
											<div class="amnter freshd"><?=$seat_count?></div>
										</div>
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="reptalltftr">
									<div class="col-xs-8 nopadding">
										<div class="farestybig">Grand Total</div>
									</div>
									<div class="col-xs-4 nopadding">
										<div class="amnterbig arimobold"><?=$default_currency_symbol?> <?=roundoff_number($markup_total_fare)?> </div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- Fare Summary  Ends-->
					<div class="col-xs-8 nopadding full_summery_tab">
						<div class="fligthsdets only_bus_book">
							<form action="<?=base_url().'index.php/bus/pre_booking/'.$search_data['search_id']?>" method="POST" autocomplete="off" id="pre-booking-form">
								<div class="flitab1">
									<div class="moreflt boksectn">
										<div class="ontyp">
											<div class="labltowr arimobold"><?=ucfirst($CUR_Route['FromCityName'])?> to <?=ucfirst($CUR_Route['ToCityName'])?><strong>(<?=get_time_duration_label(calculate_duration($CUR_Route['DepartureTime'], $CUR_Route['ArrivalTime']))?>)</strong></div>
											<div class="allboxflt">
												<div class="col-xs-3 nopadding full_fiftys">
													<div class="alldiscrpo">
														<?=$CUR_Route['CompanyName']?>
														<div class="sgsmalbus">
															<strong>Pickup :</strong>
															<div class="pikuplokndt">
																<?php $pickup = $bus_pickup[$pre_booking_params['pickup_id']]; 
																	echo $pickup['PickupName']; ?>
																<span class="pikuptm">
																<?php echo get_time($pickup['PickupTime']);	?>
																</span>
															</div>
														</div>
													</div>
												</div>
												<div class="col-xs-7 nopadding qurter_wdth">
													<div class="col-xs-5">
														<span class="airlblxl"><?=local_date($CUR_Route['DepartureTime'])." ".get_time($CUR_Route['DepTime'])?></span>
														<span class="portnme"><?=ucfirst($CUR_Route['FromCityName'])?></span>	
													</div>
													<div class="col-xs-2">
														<span class="fadr fa fa-long-arrow-right textcntr"></span>
													</div>
													<div class="col-xs-5">
														<span class="airlblxl"><?=local_date($CUR_Route['ArrivalTime'])." ".get_time($CUR_Route['ArrTime'])?></span>
														<span class="portnme"><?=ucfirst($CUR_Route['ToCityName'])?></span>
													</div>
												</div>
												<div class="col-xs-2 nopadding smal_width_hr"> 
													<span class="portnme textcntr"><?=get_time_duration_label(calculate_duration($CUR_Route['DepartureTime'], $CUR_Route['ArrivalTime']))?></span> 
													<span data-stop-number="0" class="portnme textcntr">Seat(<?=$seat_count?>) : <?=implode(',', $pre_booking_params['seat'])?></span> 
												</div>
											</div>
											<br>
											<div class="labltowr arimobold">Please enter Name(s) of the Passenger(s). </div>
											<!-- template_v1 code -->
											<div class="pasngrinput_enter">
												<div class="col-xs-3 nopad">
													<span class="labl_pasnger">Seat Details</span>
												</div>
												<div class="col-xs-9 nopad">
													<div class="col-xs-3 nopad">
														<span class="labl_pasnger">
														Title <sup class="text-danger">*</sup>
														</span>
													</div>
													<div class="col-xs-5 nopad">
														<span class="labl_pasnger">
														Name <sup class="text-danger">*</sup>
														</span>
													</div>
													<div class="col-md-4 nopad">
														<span class="labl_pasnger">
														Age <sup class="text-danger">*</sup>
														</span>
													</div>
												</div>
											</div>
											<?php $i = 0;
												$datepicker_list = array();
												$lead_pax_details = @$pax_details[0];
												if(is_logged_in_user()) {
													//$traveller_class = ' user_traveller_details ';
													$traveller_class = '';
												} else {
													$traveller_class = '';
												}
												for($i=0; $i<$seat_count; $i++) {
													$cur_pax_info = is_array($pax_details) ? array_shift($pax_details) : array();
													$i_seat = $bus_selected_seats[$pre_booking_params['seat'][$i]];
													/*$attr['Fare']		= $i_seat['Fare'];*/
													$attr['Fare']		= $i_seat['ORGFare'];//Assigned Orginal Fare
													$attr['API_Raw_Fare']= $i_seat['API_Raw_Fare'];
													$attr['Markup_Fare']= $i_seat['Markup_Fare'];
													//verify seat availability
												?>
											<div class="pasngrinput_secnrews _passenger_hiiden_inputs">
												<div class="hide hidden_pax_details">
													<input type="hidden" name="gender[]" value="1" class="pax_gender">
												</div>
												<div class="col-xs-3 nopad">
													<div class="pad_psger">
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
																$i_seat_type = 'sleeper-A.png';
																$attr['SeatType']	= 'sleeper';
															} else {
																$attr['SeatType']	= 'seat';
																$i_seat_type = 'seat-A.png';
															}
															$i_seat_title[] = ' @ '.$domain_currency_obj->get_currency_symbol($pre_booking_params['default_currency']).' '.$i_seat['Markup_Fare'];
															//echo '<img src="'.$template_images.'seats/'.$i_seat_type.'" title="'.implode(' ', $i_seat_title).'" class="hand-cursor">';
															?> 
														<span class="seat_number"> Seat <strong><?=$i_seat['SeatNo']?></strong></span>
													</div>
												</div>
												<div class="col-xs-9 nopad flling_name">
													<div class="col-xs-3 nopad">
														<div class="pad_psger">
															<div class="selectedwrap">
																<select class="name_title flyinputsnor " required name="pax_title[]">
																<?=$pax_title_options?>
																</select>
															</div>
														</div>
													</div>
													<div class="col-xs-5 nopad">
														<div class="pad_psger">
															<input value="<?=@$cur_pax_info['first_name']?>" type="text" maxlength="45" id="contact-name"  name="contact_name[]" class="clainput  alpha_space <?=$traveller_class?>" required placeholder="Name" data-row-id="<?=($i);?>">
														</div>
													</div>
													<div class="col-xs-4 nopad">
														<div class="pad_psger">
															<div class="selectedwrap">
																<select class="age flyinputsnor" name="age[]" id="age-<?=($i);?>" required>
																	<option value="INVALIDIP">Age</option>
																	<?php echo generate_options(numeric_dropdown(array('size' => 99))); ?>
																</select>
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
													
													//FIXME -- CONCLUDE
													$dynamic_params_url['CommPCT'] = $CUR_Route['CommPCT'];
													$dynamic_params_url['CommAmount'] = $CUR_Route['CommAmount'];
													
													$dynamic_params_url = serialized_data($dynamic_params_url);
													?>
												<input type="hidden" required="required" name="token"		value="<?=$dynamic_params_url;?>" />
												<input type="hidden" required="required" name="token_key"	value="<?=md5($dynamic_params_url);?>" />
												<input type="hidden" required="required" name="op"			value="book_bus">
												<input type="hidden" required="required" name="booking_source"	value="<?=$pre_booking_params['booking_source']?>" >
												<!-- FIXME -->
											</div>
											<!-- template_v1 code -->
										</div>
									</div>
									<div class="clearfix"></div>
									<div class="sepertr"></div>
									<div class="contbk">
										<div class="contcthdngs">CONTACT DETAILS</div>
										<div class="col-xs-6 nopad full_smal_forty">
											<div class="col-xs-2 nopadding">
												<!-- <input type="text" placeholder="+91" class="newslterinput nputbrd" readonly> -->
												<select class="newslterinput nputbrd _numeric_only " >
													<?php echo diaplay_phonecode($phone_code,$active_data); ?>
												</select> 
											</div>
											<div class="col-xs-2">
												<div class="sidepo">-</div>
											</div>
											<div class="col-xs-8 nopadding">
												<input value="<?=@$lead_pax_details['phone'] == 0 ? '' : @$lead_pax_details['phone'];?>" type="text" name="passenger_contact" id="passenger-contact" placeholder="Mobile Number" class="newslterinput nputbrd _numeric_only numeric" maxlength="10" required="required">
												<input type="hidden" name="alternate_contact" value="">
											</div>
											<div class="clearfix"></div>
											<div class="emailperson col-xs-12 nopad full_smal_forty">
												<input value="<?=@$lead_pax_details['email']?>" type="text" maxlength="80" required="required" id="billing-email" class="newslterinput nputbrd" placeholder="Email" name="billing_email">
											</div>
										</div>
										<div class="clearfix"></div>
										<div class="notese">Your mobile number will be used only for sending Bus related communication.</div>
									</div>
									<div class="clikdiv">
										<div class="squaredThree">
											<input id="terms_cond1" type="checkbox" name="tc" checked="checked" required="required">
											<label for="terms_cond1"></label>
										</div>
										<span class="clikagre" id="clikagre">
										Terms and Conditions
										</span>
									</div>
									<div class="clearfix"></div>
									<div class="loginspld">
										<div class="collogg">
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
											<div class="continye col-sm-3 col-xs-6 nopad">
												<button type="submit" id="flip" class="bookcont continue_booking_button" name="bus">Continue</button>
											</div>
											<div class="clearfix"></div>
											<div class="sepertr"></div>
											<div class="temsandcndtn">
												Most operators require travelers to have an ID valid for more than 3 to 6 months from the date of entry into or exit. Please check the exact rules for your destination before completing the booking.
											</div>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php echo $GLOBALS['CI']->template->isolated_view('share/passenger_confirm_popup');?>

<script type="text/javascript">
	$(document).ready(function(){
		$('.hnf-btn, .snf-btn').on('click', function() {
			$('.hnf-btn').toggle();
			$('.snf-btn').toggle();
			$('#total-fare-breakdown').toggle();
		});
	});
</script>
<?php
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/booking_script.js'), 'defer' => 'defer');
	function diaplay_phonecode($phone_code,$active_data)
	{
		$list='';
		foreach($phone_code as $code){
		if($active_data['api_country_list_fk']==$code['origin']){
				$selected ="selected";
			}
			else {
				$selected="";
			}
		
			$list .="<option value=".$code['country_code']."  ".$selected." >".$code['country_code']."</option>";
		}
		 return $list;
		
	}

