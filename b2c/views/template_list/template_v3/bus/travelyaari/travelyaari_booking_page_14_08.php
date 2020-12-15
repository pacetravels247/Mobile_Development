<?php
$module_value = md5('bus');
$currency_symbol = $this->currency->get_currency_symbol($display_currency);
$template_images = $GLOBALS ['CI']->template->template_images ();
$CUR_Route = ($details ['Route']);
$bus_seats = $details ['Layout'] ['SeatDetails'] ['clsSeat'];
$bus_pickup = $details ['Pickup'] ['clsPickup'];
$bus_drop = $details ['Drop'] ['clsDrop'];
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
if (is_logged_in_user ()) {
	$review_active_class = ' success ';
	$review_tab_details_class = '';
	$review_tab_class = ' inactive_review_tab_marker ';
	$travellers_active_class = ' active ';
	$travellers_tab_details_class = ' gohel ';
	$travellers_tab_class = ' travellers_tab_marker ';
} else {
	$review_active_class = ' active ';
	$review_tab_details_class = ' gohel ';
	$review_tab_class = ' review_tab_marker ';
	$travellers_active_class = '';
	$travellers_tab_details_class = '';
	$travellers_tab_class = ' inactive_travellers_tab_marker ';
}
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/bus_booking.js'), 'defer' => 'defer');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/booking_script.js'), 'defer' => 'defer');
$book_login_auth_loading_image	 = '<div class="text-center loader-image"><img src="'.$GLOBALS['CI']->template->template_images('loader_v3.gif').'" alt="please wait"/></div>';
$subtotal = $markup_total_fare;
$convenience_fees = 0;
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
				<div class="rondsts <?=$review_active_class?>">
					<a class="taba core_review_tab <?=$review_tab_class?>" id="stepbk1">
						<div class="iconstatus fa fa-eye"></div>
						<div class="stausline">Review</div>
					</a>
				</div>
			</div>
			<div class="col-xs-4 nopadding">
				<div class="rondsts <?=$travellers_active_class?>">
					<a class="taba core_travellers_tab <?=$travellers_tab_class?>"
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
			<div class="bktab1 xlbox <?=$review_tab_details_class?>">
				<!-- Fare Summary  Starts-->
					<div class="col-xs-4 nopadding rit_summery">
						<div class="insiefare">
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
										<div class="amnterbig arimobold grandtotal"><?=$default_currency_symbol?> <?=roundoff_number($markup_total_fare)?> </div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- Fare Summary  Ends-->
				<div class="col-xs-8 nopadding full_summery_tab">
					<div class="fligthsdets only_bus_book">
						<div class="flitab1">
<?php //debug($CUR_Route);exit;?>
							<div class="moreflt boksectn">
							  <div class="ontyp">
								<div class="labltowr arimobold"><?=ucfirst($CUR_Route['From'])?> to <?=ucfirst($CUR_Route['To'])?><strong>(<?=get_time_duration_label(calculate_duration($CUR_Route['DepartureTime'], $CUR_Route['ArrivalTime']))?>)</strong></div>
								<div class="allboxflt">
								  <div class="col-xs-4 nopadding full_fiftys">
									<div class="alldiscrpo"><?=$CUR_Route['CompanyName']?>
									<div class="clearfix"></div>
									<div class="sgsmalbus col-xs-12 col-md-12 nopad"><strong>Pickup : </strong>
									<div class="pikuplokndt">
									<span class="pikuptm">
										<?php
										$pickup = $bus_pickup[$pre_booking_params['pickup_id']]; 
										echo $pickup['PickupName'];
										$pickup_string = ($pickup['PickupName'].' - '.get_time($pickup['PickupTime']));
										$pickup_string .= ', Address : '.$pickup['Address'].', Landmark : '.$pickup['Landmark'].', Phone : '.$pickup['Contact'];
										?>, 
										
										<?php echo get_time($pickup['PickupTime']);	?>
										</span>
									</div>
									
									</div>
									<div class="sgsmalbus col-xs-12 col-md-12 nopad"><strong>Drop : </strong>
									<div class="droplokndt">
									<span class="pikuptm">
										<?php
										$drop = $bus_drop[$pre_booking_params['drop_id']]; 
									
										echo $drop['DropoffName'];
										$drop_string = ($drop['DropoffName'].' - '.get_time($drop['DropoffTime']));
										
										?>, 
										
										<?php echo get_time($drop['DropoffTime']);	?>
										</span>
									</div>
									</div>
									</div>
								  </div>
								  <div class="col-xs-6 nopadding qurter_wdth">
									<div class="col-xs-5 nopad">
										<!-- <span class="airlblxl"><?=local_date1($CUR_Route['DepartureTime'])." ".get_time($CUR_Route['DeptTime'])?></span> -->
										<span class="airlblxl"><?=($CUR_Route['DepartureTime'])." ".get_time($CUR_Route['DeptTime'])?></span>
										<span class="portnme"><?=ucfirst($CUR_Route['From'])?></span>	
									</div>
									<div class="col-xs-2 nopad">
										<span class="fadr fa fa-long-arrow-right textcntr"></span>
									</div>
									<div class="col-xs-5 nopad">
										<!-- <span class="airlblxl"><?=local_date1($CUR_Route['ArrivalTime'])." ".get_time($CUR_Route['ArrTime'])?></span> -->
										<span class="airlblxl"><?=($CUR_Route['ArrivalTime'])." ".get_time($CUR_Route['ArrTime'])?></span>
										<span class="portnme"><?=ucfirst($CUR_Route['To'])?></span></div>
									</div>
									<div class="col-xs-2 nopadding smal_width_hr"> 
									<span class="portnme textcntr"><?=get_time_duration_label(calculate_duration($CUR_Route['DepartureTime'], $CUR_Route['ArrivalTime']))?></span> 
									<span data-stop-number="0" class="portnme textcntr">Seat(<?=$seat_count?>) : <?=implode(',', $pre_booking_params['seat'])?></span> 
								</div>
								</div>
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="sepertr"></div>
							<!-- LOGIN SECTION STARTS -->
				<?php if(is_logged_in_user() == false) { ?>
				<div class="loginspld">
								<div class="logininwrap">
									<div class="signinhde">Sign in now to Book Online</div>
									<div class="newloginsectn">
										<div class="col-xs-7 celoty nopad">
											<div class="insidechs">
												<div class="mailenter">
													<input type="text" name="booking_user_name"
														id="booking_user_name" placeholder="Your mail id"
														class="newslterinput nputbrd _guest_validate" maxlength="80" required="required">
												</div>
												<div class="noteinote">Your booking details will be sent to
													this email address.</div>
												<div class="clearfix"></div>
												<div class="havealrdy">
													<div class="squaredThree">
														<input id="alreadyacnt" type="checkbox" name="check"
															value="None"> <label for="alreadyacnt"></label>
													</div>
													<label for="alreadyacnt" class="haveacntd">I have an Account</label>
												</div>
												<div class="clearfix"></div>
												<div class="twotogle">
													<div class="cntgust">
														<div class="phoneumber">
															<div class="col-xs-5 nopadding">
																<!--<input type="text" placeholder="+91" class="newslterinput nputbrd" readonly>-->
																<!-- //FIXME: insert the country code to DB -->
																<select class="newslterinput nputbrd _numeric_only" id="before_country_code">
																<?php echo diaplay_phonecode($phone_code,$active_data, $user_country_code); ?>
															</select> 
															</div>
															<div class="col-xs-1 nopadding">
																<div class="sidepo">-</div>
															</div>
															<div class="col-xs-6 nopadding">
																<input type="text" id="booking_user_mobile"
																	placeholder="Mobile Number"
																	class="newslterinput nputbrd _numeric_only numeric _guest_validate" maxlength="10">
															</div>
															<div class="clearfix"></div>
															<div class="noteinote">We'll use this number to send
																possible update alerts.</div>
														</div>
														<div class="clearfix"></div>
														<div class="continye col-xs-8 nopad">
															<button class="bookcont" id="continue_as_guest">Book as
																Guest</button>
														</div>
													</div>
													<div class="alrdyacnt">
														<div class="col-xs-12 nopad">
															<div class="relativemask">
																<input type="password" name="booking_user_password"
																	id="booking_user_password" class="clainput"
																	placeholder="Password" required="required"/>
															</div>
															<div class="clearfix"></div>
															<a class="frgotpaswrd">Forgot Password?</a>
															<div style="" class="hide alert alert-danger"></div>
														</div>
														
														<div id="book_login_auth_loading_image" style="display: none">
															<?=$book_login_auth_loading_image?>
														</div>
														
														<div class="clearfix"></div>
														<div class="continye col-xs-8 nopad">
															<button class="bookcont" id="continue_as_user">Proceed to
																Book</button>
														</div>
													</div>
												</div>
											</div>
										</div>
						 <?php $no_social=no_social(); if($no_social != 0) {?>
						<div class="col-xs-2 celoty nopad linetopbtm">
								<div class="orround">OR</div>
						</div>
						<?php } ?>
						<div class="col-xs-5 celoty nopad">
							<div class="insidechs booklogin">
								<div class="leftpul">
<?php
$social_login1 = 'facebook';
$social1 = is_active_social_login($social_login1);
if($social1){
	$GLOBALS['CI']->load->library('social_network/facebook');
	echo $GLOBALS['CI']->facebook->login_button ();?>
			<?php } 
				$social_login2 = 'twitter';
				$social2 = is_active_social_login($social_login2);
				if($social2){
				?>
	<a class="logspecify tweetcolor"><span class="fa fa-twitter"></span><div class="mensionsoc">Login with Twitter</div></a>
			<?php } 
				$social_login3 = 'googleplus';
				$social3= is_active_social_login($social_login3);
				if($social3){
				$GLOBALS['CI']->load->library('social_network/google');
				echo $GLOBALS['CI']->google->login_button ();
				}
			?>
								</div>
							</div>
						</div>
					</div>
					</div>
				</div>
				<?php } ?>
				<!-- LOGIN SECTION ENDS -->
						</div>
					</div>
				</div>
			</div>
			<div class="bktab2 xlbox <?=$travellers_tab_details_class?>">
				<div class="clearfix"></div>
				<!-- Segment Details Starts-->
				<div class="collapse splbukdets" id="fligtdetails">
					<div class="moreflt insideagain">
			</div>
				</div>
				<!-- Segment Details Ends-->
				<div class="clearfix"></div>
				<div class="padpaspotr">
					<!-- Fare Summary  Starts-->
					<div class="col-xs-4 nopadding rit_summery">
						<div class="insiefare">
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
										<div class="amnterbig arimobold grandtotal"><?=$default_currency_symbol?> <?=roundoff_number($markup_total_fare)?> </div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- Fare Summary  Ends-->
					<div class="col-xs-8 nopadding full_summery_tab">
						<div class="fligthsdets only_bus_book">
			<form action="<?=base_url().'index.php/bus/pre_booking/'.$search_data['search_id']?>" method="POST" autocomplete="off" id="pre-booking-form">
			 <input type="hidden" required="required" name="promo_code_discount_val" id="promo_code_discount_val" value="0.00" readonly>
			<input type="hidden" required="required" name="promo_code" id="promocode_val" value="" readonly>
			<input type="hidden" required="required" name="promo_actual_value" id="promo_actual_value" value="" readonly>
			 <div class="flitab1">
				<div class="moreflt boksectn">
					<div class="ontyp">
					  <div class="labltowr arimobold"><?=ucfirst($CUR_Route['From'])?> to <?=ucfirst($CUR_Route['To'])?><strong>(<?=get_time_duration_label(calculate_duration($CUR_Route['DepartureTime'], $CUR_Route['ArrivalTime']))?>)</strong></div>
								<div class="allboxflt">
								<div class="col-xs-3 nopadding full_fiftys">
									<div class="alldiscrpo"><?=$CUR_Route['CompanyName']?>
									<div class="sgsmalbus"><strong>Pickup :</strong>
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
										<span class="airlblxl"><?=local_date($CUR_Route['DepartureTime'])." ".get_time($CUR_Route['DeptTime'])?></span>
										<span class="portnme"><?=ucfirst($CUR_Route['From'])?></span>	
									</div>
									<div class="col-xs-2">
										<span class="fadr fa fa-long-arrow-right textcntr"></span>
									</div>
									<div class="col-xs-5">
										<span class="airlblxl"><?=local_date($CUR_Route['ArrivalTime'])." ".get_time($CUR_Route['ArrTime'])?></span>
										<span class="portnme"><?=ucfirst($CUR_Route['To'])?></span></div>
								</div>
								<div class="col-xs-2 nopadding smal_width_hr"> 
									<span class="portnme textcntr"><?=get_time_duration_label(calculate_duration($CUR_Route['DepartureTime'], $CUR_Route['ArrivalTime']))?></span> 
									<span data-stop-number="0" class="portnme textcntr">Seat(<?=$seat_count?>) : <?=implode(',', $pre_booking_params['seat'])?></span> 
								</div>
								</div><br>
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
								$traveller_class = ' user_traveller_details ';
							} else {
								$traveller_class = '';
							}
							for($i=0; $i<$seat_count; $i++) {
								$cur_pax_info = is_array($pax_details) ? array_shift($pax_details) : array();
								$i_seat = $bus_seats[$pre_booking_params['seat'][$i]];
								// debug($i_seat);exit;
								$attr['Fare']		= $i_seat['Fare'];
								// $attr['API_Raw_Fare'] = $i_seat['api_total_fare'];
								$attr['Markup_Fare']= $i_seat['Markup_Fare'];
							?>
							<div class="pasngrinput_secnrews _passenger_hiiden_inputs">
							<div class="hide hidden_pax_details">
								<input type="hidden" name="gender[]" value="1" class="pax_gender">
							</div>
								<div class="col-xs-2 nopad">
								<div class="pad_psger">
									<?php
									// debug($i_seat);exit;
									$i_seat_title = array();
									if ($i_seat['IsAvailable'] != '0') {
										$attr['IsAcSeat']	= false;
										$i_seat_title[] = 'NON_AC';
									} else {
										$attr['IsAcSeat']	= true;
										$i_seat_title[] = 'AC';
									}
									if (intval($i_seat['decks']) == 'Lower') {
										$attr['decks'] = 'Lower';
										$i_seat_title[] = $i_seat['seat_no'].' Lower Deck';
									} else {
										$i_seat_title[] = $i_seat['seat_no'].' Upper Deck';
										$attr['decks'] = 'Upper';
									}
									$i_seat_title[] = ($i_seat['status'] != '2' ? '' : 'Reserved For Ladies');
									if ($i_seat['seat_type'] == '2') {
										$i_seat_type = 'sleeper-A.png';
										$attr['SeatType']	= 'sleeper';
									} else {
										$attr['SeatType']	= 'seat';
										$i_seat_type = 'seat-A.png';
									}
									$attr['seq_no'] = $i_seat['seq_no'];
									$i_seat_title[] = ' @ '.$domain_currency_obj->get_currency_symbol($pre_booking_params['default_currency']).' '.$i_seat['Markup_Fare'];
									//echo '<img src="'.$template_images.'seats/'.$i_seat_type.'" title="'.implode(' ', $i_seat_title).'" class="hand-cursor">';
									?> 
									<span class="seat_number"> Seat <strong><?=$i_seat['seat_no']?></strong></span>
									</div>
								</div>
										<div class="col-xs-10 nopad flling_name">
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
							
							$seat_attr['seats'][$i_seat['seat_no']] = $attr;
							}
							?>
							<div class="hide">
							<?php 
							// debug($seat_attr);exit;
							/**
							 * Data for booking
							 */
							$dynamic_params_url['RouteScheduleId'] = $pre_booking_params['route_schedule_id'];
							$dynamic_params_url['JourneyDate'] = $pre_booking_params['journey_date'];
							$dynamic_params_url['PickUpID'] = $pre_booking_params['pickup_id'];
							$dynamic_params_url['DropID'] = $pre_booking_params['drop_id'];
							$dynamic_params_url['seat_attr'] = $seat_attr;
							$dynamic_params_url['DepartureTime'] = $CUR_Route['DepartureTime'];
							$dynamic_params_url['ArrivalTime'] = $CUR_Route['ArrivalTime'];
							$dynamic_params_url['departure_from'] = $CUR_Route['From'];
							$dynamic_params_url['arrival_to'] = $CUR_Route['To'];
							$dynamic_params_url['Form_id'] = $CUR_Route['Form_id'];
							$dynamic_params_url['To_id'] = $CUR_Route['To_id'];
							$dynamic_params_url['boarding_from'] = $pickup_string;//
							$dynamic_params_url['dropping_to'] = $drop_string;//
							$dynamic_params_url['bus_type'] = $CUR_Route['BusLabel'];
							$dynamic_params_url['operator'] = $CUR_Route['CompanyName'];
							$dynamic_params_url['CommPCT'] = $CUR_Route['CommPCT'];
							$dynamic_params_url['CommAmount'] = $CUR_Route['CommAmount'];
							$dynamic_params_url['CancPolicy'] = base64_encode(json_encode($pre_booking_params['token']['result']['Canc']));
							// debug($dynamic_params_url);exit;
							$dynamic_params_url = serialized_data($dynamic_params_url);
							?>
							<input type="hidden" required="required" name="token"		value="<?=$dynamic_params_url;?>" />
							<input type="hidden" required="required" name="token_key"	value="<?=md5($dynamic_params_url);?>" />
							<input type="hidden" required="ResultToken" name="ResultToken"	value="<?=$pre_booking_params['ResultToken'];?>" />
							<input type="hidden" required="required" name="op"			value="book_bus">
							<input type="hidden" required="required" name="booking_source"	value="<?=$pre_booking_params['booking_source']?>" >
							<!-- FIXME -->
			 			</div>
						<!-- template_v1 code -->
				   </div>
				</div>
				<div class="clearfix"></div>
				<div class="sepertr"></div>
				<div class="ontyp">
					<div class="kindrest">
					<div class="cartlistingbuk">
								<div class="cartitembuk">
									<div class="col-md-12">
										<div class="payblnhmxm">Have an e-coupon or a deal-code ? (Optional)</div>
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="cartitembuk prompform">
									<form name="promocode" id="promocode" novalidate>
				                    <div class="col-md-4 col-xs-6 nopadding_right">
				                      <div class="cartprc">
				                        <div class="payblnhm singecartpricebuk ritaln">
				                    	 <input type="text" placeholder="Enter Promo" name="code" id="code" class="promocode" aria-required="true" />
				                          <input type="hidden" name="module_type" id="module_type" class="promocode" value="<?=@$module_value;?>" />
				                          <input type="hidden" name="total_amount_val" id="total_amount_val" class="promocode" value="<?=@$subtotal;?>" />
				                          <input type="hidden" name="convenience_fee" id="convenience_fee" class="promocode" value="<?=@$convenience_fees;?>" />
				                          <input type="hidden" name="currency_symbol" id="currency_symbol" value="<?=@$currency_symbol;?>" />
				                          <input type="hidden" name="currency" id="currency" value="<?=@$pre_booking_params['default_currency'];?>" />
				                         
				                          <p class="error_promocode text-danger" style="font-weight:bold"></p>                                          
				                        </div>
				                      </div>
				                    </div>
				                    <div class="col-md-2 col-xs-3 nopadding_left">
				                      <input type="button" value="Apply" name="apply" id="apply" class="promosubmit">
				                    </div>
				                  </form>
								</div>
								<div class="loading hide" id="loading"><img src="<?php echo $GLOBALS['CI']->template->template_images('loader_v3.gif')?>"></div>
								<div class="clearfix"></div>
								<div class="savemessage"></div>
							</div>
						</div>
						</div>
				<div class="clearfix"></div>
				<div class="contbk">
					<div class="contcthdngs">CONTACT DETAILS</div>
					<div class="col-xs-6 nopad full_smal_forty">
					<div class="col-xs-3 nopadding">
					<!-- <input type="text" placeholder="+91" class="newslterinput nputbrd" readonly> -->
					<select class="newslterinput nputbrd _numeric_only " id="after_country_code">
						<?php echo diaplay_phonecode($phone_code,$active_data, $user_country_code); ?>
					</select> 
					</div>
					<div class="col-xs-2"><div class="sidepo">-</div></div>
					<div class="col-xs-7 nopadding">
					<input value="<?=@$lead_pax_details['phone'] == 0 ? '' : @$lead_pax_details['phone'];?>" type="text" name="passenger_contact" id="passenger-contact" placeholder="Mobile Number" class="newslterinput nputbrd _numeric_only numeric" maxlength="10" required="required">
					<input type="hidden" name="alternate_contact" value="">
					</div>
					<div class="clearfix"></div>
					<div class="emailperson mt10 col-xs-12 nopad full_smal_forty">
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
							<button type="submit" id="flip" class="bookcont" name="bus">Continue</button>
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
			<?php if(is_logged_in_user() == true) { ?>
			<!-- <div class="col-xs-4 nopadding">
				<div class="insiefare">
					<div class="farehd arimobold">Passenger List</div>
					<div class="fredivs">
						<div class="psngrnote">
							<?php
								if(valid_array($traveller_details)) {
									$traveller_tab_content = 'You have saved passenger details in your list,on typing, passenger details will auto populate.';
								} else {
									$traveller_tab_content = 'You do not have any passenger saved in your list, start adding passenger so that you do not have to type every time. <a href="'.base_url().'index.php/user/profile?active=traveller" target="_blank">Add Now</a>';
								}
							?>
							<?=$traveller_tab_content;?>
						</div>
					</div>
				</div>
			</div> -->
		<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php echo $GLOBALS['CI']->template->isolated_view('share/passenger_confirm_popup');?>

<?php 
function diaplay_phonecode($phone_code,$active_data, $user_country_code)
{
	$list='';
	foreach($phone_code as $code){
		if(!empty($user_country_code)){
			if($user_country_code==$code['country_code']){
				$selected ="selected";
			}
			else {
				$selected="";
			}
		}
		else{
				
			if($active_data['api_country_list_fk']==$code['origin']){
				$selected ="selected";
			}
			else {
				$selected="";
			}
		}
		$list .="<option value=".$code['name']." ".$code['country_code']."  ".$selected." >".$code['name']." ".$code['country_code']."</option>";
			
	}
	return $list;
			
}
	?>
