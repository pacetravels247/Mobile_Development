<?php
	$CI=&get_instance();
	$template_images = $GLOBALS['CI']->template->template_images();
	$mandatory_filed_marker = '<sup class="text-danger">*</sup>';
	$hotel_checkin_date = hotel_check_in_out_dates($search_data['from_date']);
	$hotel_checkin_date = explode('|', $hotel_checkin_date);
	$hotel_checkout_date = hotel_check_in_out_dates($search_data['to_date']);
	$hotel_checkout_date = explode('|', $hotel_checkout_date);
	if(is_logged_in_user()) {
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
	$passport_issuing_country = INDIA_CODE;
	$temp_passport_expiry_date = date('Y-m-d', strtotime('+5 years'));
	$static_passport_details = array();
	$static_passport_details['passenger_passport_expiry_day'] = date('d', strtotime($temp_passport_expiry_date));
	$static_passport_details['passenger_passport_expiry_month'] = date('m', strtotime($temp_passport_expiry_date));
	$static_passport_details['passenger_passport_expiry_year'] = date('Y', strtotime($temp_passport_expiry_date));
	$hotel_total_price = roundoff_number($this->hotel_lib->total_price($pre_booking_params['markup_price_summary']));
	
	/********************************* Convenience Fees *********************************/
	$pre_booking_params['convenience_fees'] = $convenience_fees;
	$hotel_total_price = roundoff_number($pre_booking_params['convenience_fees']+$hotel_total_price);
	/********************************* Convenience Fees *********************************/
	$LastCancellationDate = $pre_booking_params['LastCancellationDate'];
	$RoomTypeName = $pre_booking_params['RoomTypeName'];
	$Boardingdetails = $pre_booking_params['Boarding_details'];

	//calculating price
	$token = $pre_booking_params['price_token'];
	$tax_total = 0;
	$grand_total = 0;
	$grand_total = $pre_booking_params['markup_price_summary']['RoomPrice'];
	$tax_total += $pre_booking_params['convenience_fees'];
	$tax_total += $tax_service_sum;
	$grand_total += $pre_booking_params['convenience_fees'];
	$tax_total = ceil($tax_total);
	$grand_total = ceil($grand_total);
	$hotel_total_price = ceil($hotel_total_price);
	//calculate total room price without tax
	$total_room_price  = roundoff_number($pre_booking_params['markup_price_summary']['RoomPrice']-$pre_booking_params['markup_price_summary']['_GST']);
	$total_pax = array_sum($search_data['adult_config'])+array_sum($search_data['child_config']);

	$base_url=base_url().'index.php/hotel/image_details_cdn';
	$total_adult_count	= array_sum($search_data['adult_config']);
	$total_child_count	= array_sum($search_data['child_config']);
	$no_adult='No of Adults';
		$no_child = 'No of Childs';
		if($total_adult_count==1){
			$no_adult='No of Adult';			      			
		}
		if($total_child_count==1 || $total_child_count ==0){
			$no_child = 'No of Child';
		}

//echo generate_low_balance_popup($hotel_total_price);
$user_country_code = '+91';
$markup_limits = $markup_limits[0]['value'];
?>


<style>
	.topssec::after{display:none;}
</style>
<?php //Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('provablib.js'), 'defer' => 'defer');
?>
<input type="hidden" id="total_pax" value="<?=$total_pax?>">
<div class="fldealsec">
	<div class="col-xs-12">
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
					<a class="taba core_travellers_tab <?=$travellers_tab_class?>" id="stepbk2">
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
	<div class="col-xs-12">
		<div class="ovrgo">
			<div class="bktab1 xlbox <?=$review_tab_details_class?>">
				<div class="toprom">
					<div class="col-xs-8 nopad full_room_buk">
						<div class="bookcol">
							<div class="hotelistrowhtl">
								<div class="col-md-4 nopad xcel">
									 <div class="imagehotel">
								  		<?php if($pre_booking_params['HotelImage']!='/'):?>
								  			<?php
									  			$image = $base_url.'/'.base64_encode($pre_booking_params['HotelCode']).'/0';
									  		?>
								  		<img alt="<?=$pre_booking_params['HotelName']?>" src="<?php echo $image?>">
								  	<?php else:?>
								  		<img alt="Hotel_img" src="<?=$GLOBALS['CI']->template->template_images('default_hotel_img.jpg')?>" class="lazy h-img">
								  	<?php endif;?>	
								  </div>					
								</div>
								<div class="col-md-8 padall10 xcel">
									<div class="hotelhed"><?=$pre_booking_params['HotelName']?></div>
									<div class="clearfix"></div>
									<div class="bokratinghotl rating-no">                   
										<?=print_star_rating($pre_booking_params['StarRating'])?>
									</div>
									<div class="clearfix"></div>
									<div class="mensionspl"> <?=$pre_booking_params['HotelAddress']?> </div>
									<!--
										<div class="roomtyped">King Executive Room</div>
										<div class="mensionspl"> <strong>Extra Bed Available :</strong> <span class="menlbl">Crib</span> </div>
										<div class="clearfix"></div>
										<div class="refundpol"> <span class="fa fa-check-square"></span>Additional deals </div>
										-->
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-4 nopad full_room_buk">
						<div class="sckint">
							<div class="ffty">
								<div class="borddo brdrit">
									<span class="lblbk_book">
									<span class="fa fa-calendar"></span>
									Check-in</span>
									<div class="fuldate_book">
										<span class="bigdate_book"><?=$hotel_checkin_date[0]?></span>
										<div class="biginre_book"> <?=$hotel_checkin_date[1]?><br>
											<?=$hotel_checkin_date[2]?> 
										</div>
									</div>
								</div>
							</div>
							<div class="ffty">
								<div class="borddo">
									<span class="lblbk_book"> <span class="fa fa-calendar"></span> Check-out</span>
									<div class="fuldate_book">
										<span class="bigdate_book"><?=$hotel_checkout_date[0]?></span>
										<div class="biginre_book"> <?=$hotel_checkout_date[1]?><br>
											<?=$hotel_checkout_date[2]?> 
										</div>
									</div>
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="nigthcunt">Night(s) <?=$search_data['no_of_nights']?>, Room(s) <?=$search_data['room_count']?></div>
						</div>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="fullcard">
					<div class="price_htlin">
						<?php echo room_price_details($pre_booking_params, $search_data, $currency_obj);?>
					</div>
					<div class="clearfix"></div>
					<!--
						<div class="indiscount col-xs-8">
						    <div class="ftudiscnt">
						        <div class="cashdisc">Discount/Cashback Voucher</div>
						        <div class="col-xs-9 nopad">
						            <input type="text" class="discntcop" placeholder="Text input">
						        </div> 
						        <div class="col-xs-3">
						            <button class="booking-btn bookallbtn splhtlbku" type="submit">Apply</button>
						        </div>  
						    </div>
						</div>-->
				</div>
				<div class="clearfix"></div>
				<div class="col-xs-8 nopadding full_log_tab">
					<div class="fligthsdets">
						<div class="flitab1">
							<div class="clearfix"></div>
							<div class="sepertr"></div>
							<!--
								<div class="promocode">
									<div class="col-xs-6">
								    	<div class="mailsign">Have a discount / promo code to redeem</div>
								    </div>
								    <div class="col-xs-6">
								    	<div class="tablesign">
								          <div class="inputsign">
								            <input type="text" placeholder="Enter Coupon" class="newslterinput nputbrd">
								          </div>
								          <div class="submitsign">
								            <button class="promobtn">Apply</button>
								          </div>
								       </div>
								    </div>
								</div>
								-->
							<div class="sepertr"></div>
							<!-- LOGIN SECTION STARTS -->
							<?php if(is_logged_in_user() == false) { ?>
							<div class="loginspld">
								<div class="logininwrap">
									<div class="signinhde">
										Sign in now to Book Online
									</div>
									<div class="newloginsectn">
										<div class="col-xs-5 celoty nopad">
											<div class="insidechs">
												<div class="mailenter">
													<input type="text" name="booking_user_name" id="booking_user_name"  placeholder="Your mail id" class="newslterinput nputbrd _guest_validate" maxlength="80">
												</div>
												<div class="noteinote">Your booking details will be sent to this email address.</div>
												<div class="clearfix"></div>
												<div class="havealrdy">
													<div class="squaredThree">
														<input id="alreadyacnt" type="checkbox" name="check" value="None">
														<label for="alreadyacnt"></label>
													</div>
													<label for="alreadyacnt" class="haveacntd">I have an Account</label>
												</div>
												<div class="clearfix"></div>
												<div class="twotogle">
													<div class="cntgust">
														<div class="phoneumber">
															<div class="col-xs-2 nopadding">
															<!--<input type="text" placeholder="+91" class="newslterinput nputbrd">-->
															<select class="newslterinput nputbrd _numeric_only " >
																<?php //echo diaplay_phonecode($phone_code,$active_data);
																echo diaplay_phonecode($phone_code,$active_data, $user_country_code);
																 ?>
															</select>
															</div>
															<div class="col-xs-1 nopadding">
																<div class="sidepo">-</div>
															</div>
															<div class="col-xs-9 nopadding">
																<input type="text" id="booking_user_mobile" placeholder="Mobile Number" class="newslterinput nputbrd _numeric_only _guest_validate" maxlength="10">
															</div>
															<div class="clearfix"></div>
															<div class="noteinote">We'll use this number to send possible update alerts.</div>
														</div>
														<div class="clearfix"></div>
														<div class="continye col-xs-8 nopad">
															<button class="bookcont" id="continue_as_guest">Book as Guest</button>
														</div>
													</div>
													<div class="alrdyacnt">
														<div class="col-xs-12 nopad">
															<div class="relativemask"> 
																<input type="password" name="booking_user_password" id="booking_user_password" class="clainput" placeholder="Password" />
															</div>
															<div class="clearfix"></div>
															<a class="frgotpaswrd">Forgot Password?</a>
															<div style="" class="alert alert-danger"></div>
														</div>
														<div class="clearfix"></div>
														<div class="continye col-xs-8 nopad">
															<button class="bookcont" id="continue_as_user">Proceed to Book</button>
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
														?>
													<?php
														$GLOBALS['CI']->load->library('social_network/facebook');
														echo $GLOBALS['CI']->facebook->login_button ();?>
													<!-- <a class="logspecify facecolor"><span class="fa fa-facebook"></span><div class="mensionsoc">Login with Facebook</div></a> -->
													<?php } 
														$social_login2 = 'twitter';
														$social2 = is_active_social_login($social_login2);
														if($social2){
														?>
													<a class="logspecify tweetcolor">
														<span class="fa fa-twitter"></span>
														<div class="mensionsoc">Login with Twitter</div>
													</a>
													<?php } 
														$social_login3 = 'googleplus';
														$social3= is_active_social_login($social_login3);
														if($social3){
														?>
													<?php
														$GLOBALS['CI']->load->library('social_network/google');
														echo $GLOBALS['CI']->google->login_button ();?>
													<!-- <a class="logspecify gpluses"><span class="fa fa-google-plus"></span><div class="mensionsoc">Login with Google Plus</div></a> -->
													<?php } ?>
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
			  <div class="col-xs-12 col-md-8 nopad">
				<div class="col-xs-12 topalldesc">
					<div class="col-xs-12 nopad">
						<div class="bookcol">
							<div class="hotelistrowhtl agntigcs">
								<div class="col-md-4 nopad xcel">
									 <div class="imagehotel">
									  		<?php if($pre_booking_params['HotelImage']!='/'):?>
									  		<?php
									  			$image = $base_url.'/'.base64_encode($pre_booking_params['HotelCode']).'/0';
									  		?>
									  		<img alt="<?=$pre_booking_params['HotelName']?>" src="<?php echo $image;?>">
									  	<?php else:?>
									  		<img alt="Hotel_img" src="<?=$GLOBALS['CI']->template->template_images('default_hotel_img.jpg')?>" class="lazy h-img">
									  	<?php endif;?>
									  </div>
								</div>
								<div class="col-md-8 padall10 xcel agntcss">
									<div class="hotelhed"><?=$pre_booking_params['HotelName']?></div>
									<div class="clearfix"></div>
									<div class="bokratinghotl rating-no">                   
										<?=print_star_rating($pre_booking_params['StarRating'])?>
									</div>
									<div class="clearfix"></div>
									<div class="mensionspl"> <?=$pre_booking_params['HotelAddress']?> </div>
									<div class="bokkpricesml">
							<div class="travlrs">Travelers: <span class="fa fa-male"></span> <?=array_sum($search_data['adult_config'])?> |  <span class="fa fa-child"></span> <?=array_sum($search_data['child_config'])?></div>
							<div class="totlbkamnt hide"> Room Amount <?=$this->currency->get_currency_symbol($pre_booking_params['default_currency'])?> <?=($pre_booking_params['markup_price_summary']['RoomPrice'])?>/-</div>
							<div class="totlbkamnt hide"> Tax <?=$this->currency->get_currency_symbol($pre_booking_params['default_currency'])?> <?=(($convenience_fees+$tax_service_sum))?>/-</div>
							<div class="totlbkamnt hide"> Total Amount <?=$this->currency->get_currency_symbol($pre_booking_params['default_currency'])?> <?=($hotel_total_price)?>/-</div>
						</div>
								</div>
							</div>
						</div>
					</div>
					<!-- Outer Summary -->
					<div class="col-xs-4 nopadding celtbcel colrcelo hide">
						<div class="bokkpricesml">
							<div class="travlrs">Travelers: <span class="fa fa-male"></span> <?=array_sum($search_data['adult_config'])?> |  <span class="fa fa-child"></span> <?=array_sum($search_data['child_config'])?></div>
							<div class="totlbkamnt hide"> Room Amount <?=$this->currency->get_currency_symbol($pre_booking_params['default_currency'])?> <?=($pre_booking_params['markup_price_summary']['RoomPrice'])?>/-</div>
							<div class="totlbkamnt hide"> Tax <?=$this->currency->get_currency_symbol($pre_booking_params['default_currency'])?> <?=(($convenience_fees+$tax_service_sum))?>/-</div>
							<div class="totlbkamnt hide"> Total Amount <?=$this->currency->get_currency_symbol($pre_booking_params['default_currency'])?> <?=($hotel_total_price)?>/-</div>
						</div>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="col-xs-12 padpaspotr">
					<div class="col-xs-12 nopadding">
						<div class="fligthsdets">
							<?php
								/**
								 * Collection field name 
								 */
								//Title, Firstname, Middlename, Lastname, Phoneno, Email, PaxType, LeadPassenger, Age, PassportNo, PassportIssueDate, PassportExpDate
								

								//------------------------------ DATEPICKER START
								$i = 1;
								$datepicker_list = array();
								if ($total_adult_count > 0) {
									for ($i=1; $i<=$total_adult_count; $i++) {
										$datepicker_list[] = array('adult-date-picker-'.$i, ADULT_DATE_PICKER);
									}
								}
								
								if ($total_child_count > 0) {
									for ($i=$i; $i<=($total_child_count+$total_adult_count); $i++) {
										$datepicker_list[] = array('child-date-picker-'.$i, CHILD_DATE_PICKER);
									}
								}
								$GLOBALS['CI']->current_page->set_datepicker($datepicker_list);
								//------------------------------ DATEPICKER END
								//FIXME - Balu A to make nationality dynamic
								if (valid_array($pax_details) == true) {
									//pax details passed as page input
									
								} else {
									//lets default the values
									
								}
								$total_pax_count	= $total_adult_count+$total_child_count;
								//First Adult is Primary and and Lead Pax
								$adult_enum = $child_enum = get_enum_list('title');
								$gender_enum = get_enum_list('gender');
								unset($adult_enum[MASTER_TITLE]); // Master is for child so not required
								unset($child_enum[MASTER_TITLE]); // Master is not supported in TBO list
								unset($adult_enum[MISS_TITLE]); // Miss is not supported in GRN list
								unset($child_enum[MISS_TITLE]);
								unset($child_enum[C_MRS_TITLE]);
								unset($adult_enum[A_MASTER]);

								$adult_title_options = generate_options($adult_enum, false, true);
								$child_title_options = generate_options($child_enum, false, true);
								$gender_options	= generate_options($gender_enum);
								$nationality_options = generate_options($iso_country_list, array(INDIA_CODE));//FIXME get ISO CODE --- ISO_INDIA
								$passport_issuing_country_options = generate_options($country_list);
								
								//lowest year wanted
								$cutoff = date('Y', strtotime('+20 years'));
								//current year
								$now = date('Y');
								$day_options	= generate_options(get_day_numbers());
								$month_options	= generate_options(get_month_names());
								$year_options	= generate_options(get_years($now, $cutoff));
								/**
								 * check if current print index is of adult or child by taking adult and total pax count
								 * @param number $total_pax		total pax count
								 * @param number $total_adult	total adult count
								 */
								function is_adult($total_pax, $total_adult)
								{
									return ($total_pax>$total_adult ?	false : true);
								}
								
								/**
								 * check if current print index is of adult or child by taking adult and total pax count
								 * @param number $total_pax		total pax count
								 * @param number $total_adult	total adult count
								 */
								function is_lead_pax($pax_count)
								{
									return ($pax_count == 1 ? true : false);
								}
								$lead_pax_details = @$pax_details[0];
								 ?>
							<form action="<?=base_url().'index.php/hotel/pre_booking/'.$search_data['search_id']?>" method="POST" autocomplete="off" id="pre-booking-form">
								<div class="hide">
									<?php $dynamic_params_url = serialized_data($pre_booking_params);?>
									<input type="hidden" required="required" name="token"		value="<?=$dynamic_params_url;?>" />
									<input type="hidden" required="required" name="token_key"	value="<?=md5($dynamic_params_url);?>" />
									<input type="hidden" required="required" name="op"			value="book_flight">
									<input type="hidden" required="required" name="booking_source"		value="<?=$booking_source?>" readonly>
								</div>
								<div class="flitab1">
									<div class="moreflt boksectn">
										<div class="ontyp">
											<div class="labltowr arimobold">Please enter the customer names.</div>
											<?php
												$child_age = @$search_data['child_age'];
												if(is_logged_in_user()) {
													//$traveller_class = ' user_traveller_details ';
													$traveller_class = '';
												} else {
													$traveller_class = '';
												}
												for($pax_index=1; $pax_index <= $total_pax_count; $pax_index++) {//START FOR LOOP FOR PAX DETAILS
												$cur_pax_info = is_array($pax_details) ? array_shift($pax_details) : array();
												?>
											<div class="pasngrinput _passenger_hiiden_inputs">
												<div class="hide hidden_pax_details">
													<?php
														if(is_adult($pax_index, $total_adult_count) == true) {
														        $static_date_of_birth = date('Y-m-d', strtotime('-30 years'));;
														        } else {//child
														        	$static_date_of_birth = date('Y-m-d', strtotime('-'.intval(array_shift($child_age)).' years'));;
														        }
														        $passport_number = rand(1111111111,9999999999);
														     ?>
													<input type="hidden" name="passenger_type[]" value="<?=(is_adult($pax_index, $total_adult_count) ? 1 : 2)?>">
													<input type="hidden" name="lead_passenger[]" value="<?=(is_lead_pax($pax_index) ? true : false)?>">
													<input type="hidden" name="date_of_birth[]" value="<?=$static_date_of_birth?>">
													<input type="hidden" name="gender[]" value="1" class="pax_gender">
													<input type="hidden" required="required" name="passenger_nationality[]" id="passenger-nationality-<?=$pax_index?>" value="92">
													<!-- Static Passport Details -->
													<input type="hidden" name="passenger_passport_number[]" value="<?=$passport_number?>" id="passenger_passport_number_<?=$pax_index?>">
													<input type="hidden" name="passenger_passport_issuing_country[]" value="<?=$passport_issuing_country?>" id="passenger_passport_issuing_country_<?=$pax_index?>">
													<input type="hidden" name="passenger_passport_expiry_day[]" value="<?=$static_passport_details['passenger_passport_expiry_day']?>" id="passenger_passport_expiry_day_<?=$pax_index?>">
													<input type="hidden" name="passenger_passport_expiry_month[]" value="<?=$static_passport_details['passenger_passport_expiry_month']?>" id="passenger_passport_expiry_month_<?=$pax_index?>">
													<input type="hidden" name="passenger_passport_expiry_year[]" value="<?=$static_passport_details['passenger_passport_expiry_year']?>" id="passenger_passport_expiry_year_<?=$pax_index?>">
												</div>
												<div class="col-xs-2 nopadding">
													<div class="adltnom"><?=(is_adult($pax_index, $total_adult_count) ? 'Adult' : 'Child')?><?=(is_lead_pax($pax_index) ? '- Lead Pax' : '')?></div>
												</div>
												<div class="col-xs-10 nopadding">
													<div class="inptalbox">
														<div class="col-xs-3 spllty">
															<div class="selectedwrap">
																<select class="mySelectBoxClass flyinputsnor name_title" name="name_title[]" required="required">
																<?php echo (is_adult($pax_index, $total_adult_count) ? $adult_title_options : $child_title_options)?>
																</select>
															</div>
														</div>
														<div class="col-xs-4 spllty">
															<input value="<?=@$cur_pax_info['first_name']?>" required="required" type="text" name="first_name[]" id="passenger-first-name-<?=$pax_index?>" class="guest_name clainput alpha_space <?=$traveller_class?>" maxlength="45" placeholder="Enter First Name" data-row-id="<?=($pax_index);?>"/>
															<input type="hidden" class="hide" maxlength="45" name="middle_name[]">
														</div>
														<div class="col-xs-4 spllty">
															<input value="<?=@$cur_pax_info['last_name']?>" required="required" type="text" name="last_name[]" id="passenger-last-name-<?=$pax_index?>" class="clainput alpha_space" maxlength="45" placeholder="Enter Last Name" />
														</div>
													</div>
												</div>
											</div>
											<?php
												}//END FOR LOOP FOR PAX DETAILS
												?>	
										</div>
									</div>
									<div class="clearfix"></div>
									<div class="sepertr"></div>
									<div class="clearfix"></div>
									<div class="contbk">
										<div class="contcthdngs">CONTACT DETAILS</div>
										<div class="hide">
											<input type="hidden" name="billing_country" value="92">
											<input type="hidden" name="billing_city" value="test">
											<input type="hidden" name="billing_zipcode" value="test">
										</div>
										<div class="col-xs-12 nopad full_smal_forty">
										<div class="col-xs-12 nopad full_smal_forty">
											<div class="col-xs-5 nopadding">
												<!-- <input type="text" placeholder="+91" class="newslterinput nputbrd" readonly> -->
												<select name="country_code" class="newslterinput nputbrd _numeric_only " required>
													<?php echo diaplay_phonecode($phone_code,$active_data, $user_country_code); ?>
												</select>
											</div>
											<div class="col-xs-1">
												<div class="sidepo">-</div>
											</div>
											<div class="col-xs-6 nopadding">
												<input value="<?=@$lead_pax_details['phone'] == 0 ? '' : @$lead_pax_details['phone'];?>" type="text" name="passenger_contact" id="passenger-contact" placeholder="Mobile Number" class="newslterinput nputbrd _numeric_only" maxlength="10" required="required">
											</div>
											</div>
											
											<div class="emailperson col-xs-12 nopad full_smal_forty">
												<input value="<?=@$lead_pax_details['email']?>" type="text" maxlength="80" required="required" id="billing-email" class="newslterinput nputbrd" placeholder="Email" name="billing_email">
											</div>
											<div class="clearfix"></div>
											<div class="emailperson col-xs-12 nopad full_smal_forty">
												<textarea rows="2" name="billing_address_1" class="newsltertextarea nputbrd" placeholder="Address" required="required"><?=@$agent_address?></textarea>
											</div>
										</div>
										<div class="clearfix"></div>
										<div class="notese">Your Mobile number and Email ID will be used only for sending hotel related communication.</div>
									</div>
									<div class="clikdiv">
										<div class="squaredThree">
											<input id="terms_cond1" type="checkbox" name="tc" checked="checked" required="required">
											<label for="terms_cond1"></label>
										</div>
										<span class="clikagre">
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
											<div class="row">
<div class="contbk">
<div class="contcthdngs">Select Payment Mode</div>
<input type="radio" name="selected_pm" value="WALLET" checked/> Wallet
<input type="radio" name="selected_pm" value="PAYTM_CC" /> Credit Card
<input type="radio" name="selected_pm" value="PAYTM_DC" /> Debit Card
<input type="radio" name="selected_pm" value="PAYTM_PPI" /> PAYTM Wallet
<input type="radio" name="selected_pm" value="TECHP" /> Net Banking
</div>
<input type="hidden" name="markup" id="markup_copy" style="width:100px">
</div>
											<div class="continye col-xs-3">
												<button id="flip" class="bookcont" type="submit">Continue</button>
											</div>
											<div class="clearfix"></div>
											<div class="sepertr"></div>
											<div class="temsandcndtn">
												Most countries require travelers to have a passport valid for more than 3 to 6 months from the date of entry into or exit from the country. Please check the exact rules for your destination country before completing the booking.
											</div>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
					<?php echo $GLOBALS['CI']->template->isolated_view('share/passenger_confirm_popup'); ?>
					<div class="col-xs-12 nopadding">
						<!--
							<div class="insiefare">
							   	
							       <div class="farehd arimobold">Passenger List</div>
							       <div class="fredivs">
							           <div class="psngrnote">
							               
							               You do not have any passenger saved in your list, start adding passenger so that you do not have to type every time.
							               
							           </div>
							       </div>
							   </div>
							   -->
					</div>
				</div>
			  </div>
			  <div class="col-xs-4 full_room_buk rhttbepa">
		   <div id="slidebarscr">
		 	<table class="table table-condensed tblemd">
			 	<tbody>
			 	  <tr class="rmdtls">
			        <th colspan="2">Room Details</th>
			      </tr>
			      <tr>
			        <th>Room Type</th>
			        <td class="modal_room_type"><?=$RoomTypeName?></td>
			      </tr>
			      
                              <tr class="aminitdv">
			        <th>Board Type</th>
			        <td style="font-size:12px;"><?php if($Boardingdetails):?>			         		
		         		<?php  $am_arr = array();
		         			foreach ($Boardingdetails as $b_key => $b_value) {
		         				$am_arr[]=$b_value;
		         			}
                                              foreach($am_arr as $key_v=>$_val)
                                              {
                                                  echo 'Room'.($key_v+1).': '.$_val.'<br />';
                                              }
                                               //echo implode("<br />",$am_arr);
			         	?>
			        <?php else:?>
		         	<span>Room Only</span>
			        	<?php endif;?>
			        </td>
			      </tr>
			      <tr>
			       <?php
				         $total_pax = array_sum($search_data['adult_config'])+array_sum($search_data['child_config']);
				        ?>

			        <th>No of Guest</th>
			       <td class="modal_no_guest"><?=$total_pax?></td>
			      </tr>
			      <tr>
			        <th><?=$no_adult?></th>
			        <td><?=array_sum($search_data['adult_config'])?></td>
			      </tr>
			      <tr>
			        <th><?=$no_child?></th>
			        <td><?=array_sum($search_data['child_config'])?></td>
			      </tr>
			      <?php if($LastCancellationDate):?>
				      <tr class="frecanpy">
				        <th>Free Cancellation till:<br/><a  href="#" data-target="#roomCancelModal" data-toggle="modal">View Cancellation Policy</a></th>
				        <td><?=local_month_date($LastCancellationDate)?></td>
				      </tr>
				  <?php else:?>
				  		<tr class="frecanpy">
				        <th>Cancellation Policy:<br/><a  href="#" data-target="#roomCancelModal" data-toggle="modal">View Cancellation Policy</a></th>
				        <td>Non-Refundable</td>
				      </tr>
			 	 <?php endif;?>
			      <tr>
			        <th>Total Price</th>
			        <td><?=$this->currency->get_currency_symbol($pre_booking_params['default_currency'])?> <?=($total_room_price)?></td>
			      </tr>
			      <tr class="texdiv hide">
			        <th>Taxes & Service fee</th>
			        <td><?=$this->currency->get_currency_symbol($pre_booking_params['default_currency'])?> <?=$tax_total?></td>
			      </tr>
			      <?php if($pre_booking_params['markup_price_summary']['_GST'] > 0){?>
			      <tr class="texdiv">
			        <th>GST</th>
			        <td><?=$this->currency->get_currency_symbol($pre_booking_params['default_currency'])?> <span id="markup_gst"><?= roundoff_number($pre_booking_params['markup_price_summary']['_GST']);?></span>
			        	<input type="hidden" id="markup_gst_copy" value="<?= roundoff_number($pre_booking_params['markup_price_summary']['_GST']);?>">
			        </td>
			      </tr>
			      <?php } ?>
			      <tr class="textdiv">
					 <td colspan="2" align="left"><a style="float: left;" id="markup_show_hide">Show / Hide Markup</a></td>
				</tr>
				<tr class="textdiv" style="display: none;" id="add_custom_markup">
					<th>Markup</th>
					<td>
						<input type="text" id="markup" name="markup" 
						style="width:100px; height: 25px; ">
						<p id="err_msg"></p>
					</td>
					
				</tr>
				<tr class="textdiv">
					<th>Convenience Fees</th>
					<td>
						<span id="convenience_fees">0</span>
					</td>
				</tr>
			      <tr class="grd_tol">
			        <th>Grand Total</th>
			        <td><span class="grand_total_amount">
			        <?=$this->currency->get_currency_symbol($pre_booking_params['default_currency'])?> <?=($hotel_total_price)?></span>
			        	<input id="grand_total_amount_copy" type="hidden" 
						value="<?=($hotel_total_price)?>">
			        </td>
			      </tr>
			    </tbody>
			  </table>
		   </div>
		  </div>

			
			</div>
		</div>
	</div>
</div>
<div class="modal fade bs-example-modal-lg" id="roomCancelModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h5 class="modal-title" id="myModalLabel">Cancellation Policy</h5>
				
				<div class="imghtltrpadv hide">
				  <img src="" id="trip_adv_img">
				</div>
			</div>
			<div class="modal-body">
				
				<p class="policy_text"><?php echo array_shift($pre_booking_params['CancellationPolicy']); ?></p>

				
			</div>
			<div class="modal-footer">
	          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        </div>
		</div>
	</div>
</div>
<?php 
	function room_price_details($pre_booking_params, $search_data, $currency_obj)
	{
		$token = $pre_booking_params['token'];
		$adult_config = $search_data['adult_config'];
		$child_config = $search_data['child_config'];
		$no_of_nights = $search_data['no_of_nights'];
		$default_currency = $currency_obj->get_currency_symbol($pre_booking_params['default_currency']);
		$CancellationPolicy = $pre_booking_params['CancellationPolicy'];
		$grand_total = 0;
		$room_price_details = '';
		$room_price_details .= '<ul>';
	    $room_price_details .= '<li class="baseli hedli">
	                                    <ul>
	                                    	<li class="wid10">Room</li>
	                                        <li class="wid20">Guest</li>
	                                        <li class="wid20">Price/Night</li>
	                                        <li class="wid20">Extras</li>
	                                        <li class="wid10">Night(s)</li>
	                                        <li class="wid20 textrit">Total Price</li>
	                                    </ul>
	                                    </li>';
	    	$tax_total = 0;
	    	foreach($token as $token_k => $token_v) {
	    		$room_number = ($token_k+1);
	    		$adult_count = array_shift($adult_config);
	    		if(valid_array($child_config)) {
	    			$child_count = array_shift($child_config);
	    		} else {
	    			$child_count = 0;
	    		}
	    		$temp_price_details = $GLOBALS['CI']->hotel_lib->update_room_markup_currency($token_v, $currency_obj, $search_data['search_id'], true, true);
	    		$RoomPrice = $temp_price_details['RoomPrice'];
	    		$per_night_price = ($RoomPrice/$no_of_nights);
	    		$room_tax = $GLOBALS['CI']->hotel_lib->tax_service_sum($temp_price_details, $token_v);
	    		
	    		$grand_total += $RoomPrice+$room_tax;
	    		$tax_total += $room_tax;
			    $room_price_details .= '<li class="baseli secf">
			                                    <ul class="responsive_li">
			                                    	<li class="wid10"><span class="res_op">Room</span>'.$room_number.'</li>
			                                        <li class="wid20">
													<span class="res_op">Guest</span>
			                                        <div class="plusic">
			                                        	<div class="left adultic fa fa-male"></div>
			                                            <div class="left cunt">'.$adult_count.'</div>';
			    		  if(intval($child_count) >= 1) {
				              $room_price_details .= '<div class="left cunt">+</div>
				                                            <div class="left childic fa fa-child"></div>
				                                            <div class="left cunt">'.$child_count.'</div>
				                                            <div class="left cunt">=</div>
				                                            <div class="left cunt totl">'.($adult_count+$child_count).'</div>';
			    		  }
			              $room_price_details .= '</div>
			                                        </li>
			                                        <li class="wid20"><span class="res_op">Price/Night</span>'.$default_currency.' '.$per_night_price.'</li>
			                                        <li class="wid20"><span class="res_op">Extras</span><a href="#"class="" data-toggle="tooltip" data-placement="top" title="'.array_shift($CancellationPolicy).'">Cancellation Policy</a></li>
			                                        <li class="wid10"><span class="res_op">Night(s)</span>'.$no_of_nights.'</li>
			                                        <li class="wid20 textrit"><span class="res_op">Total Price</span>'.$default_currency.' '.$RoomPrice.'</li>
			                                    </ul>
			                                    </li>';
	    	}
			$tax_total += $pre_booking_params['convenience_fees'];
			$grand_total += $pre_booking_params['convenience_fees'];
	      $room_price_details .='<li class="baselicenter">
	                                    	<div class="wid80 left textrit">taxes &amp; service fee</div>
	                                        <div class="wid20 left textrit">'.$default_currency.' '.$tax_total.'</div>
	                                    </li>
	                            <li class="baselicenter">
	                              <!--<div class="wid80 left textrit green">Discount</div>
	                              <div class="wid20 left textrit green">---</div>-->
	                             </li>
	                             <li class="baseli price_cet">
	                               <div class="wid80 left textrit bigtext colrdark">Grand Total</div>
	                               <div class="wid20 left textrit">
		                               <div class="priceflights">
		                               <strong> '.$default_currency.' </strong> <span class="h-p">'.roundoff_number($grand_total).'</span>/- 
		                               </div>
	                               </div>
	                              </li>';
	           $room_price_details .= '</ul>';
		return $room_price_details;
	}
	/*function diaplay_phonecode($phone_code,$active_data)
	{
		$list='';
		foreach($phone_code as $code){
		if($active_data['api_country_list_fk']==$code['origin']){
				$selected ="selected";
			}
			else {
				$selected="";
			}
		
		$list .="<option value=".$code['name']." ".$code['country_code']."  ".$selected." >".$code['name']." ".$code['country_code']."</option>";
		}
		 return $list;
		
	}*/
	function diaplay_phonecode($phone_code,$active_data, $user_country_code)
{
	
	// debug($phone_code);exit;
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
	
	
		$list .="<option value=".$code['country_code']."  ".$selected." >".$code['name']." ".$code['country_code']."</option>";
	}   
	 return $list;
	
}
?>
<script type="text/javascript">
$(document).ready(function(){
	$("#markup_show_hide").click(function(){
        $("#add_custom_markup").toggle();
    });
    $('#markup').keyup(function(){
    var markup_limits = <?=$markup_limits?>;
    var markup_val = parseFloat($('#markup').val());	

    var grand_total = $('#grand_total_amount_copy').val();
    var markup_gst = $("#markup_gst_copy").val();
    
    if( markup_val > 0 && (parseFloat(markup_val) <= parseFloat(markup_limits))){
        $('#markup_copy').val(markup_val);
        var extra_markup_gst = 0;
        var new_markup_gst = parseFloat(parseFloat(markup_gst)+parseFloat(extra_markup_gst)).toFixed(2);
        var new_grand_total = parseFloat(parseFloat(grand_total)+parseFloat(markup_val)+parseFloat(extra_markup_gst)).toFixed(2);
        $("#markup_gst").text(new_markup_gst);
        $('.grand_total_amount').text(new_grand_total);
        $('#err_msg').text('');
    }
    else{
    	$('#err_msg').text('System accepts markup values under Rs.'+ markup_limits);
    	$('#err_msg').css('color','red');
    	$("#markup_gst").text(markup_gst);
        $('.grand_total_amount').text(grand_total);
    }
});

$("input[name='selected_pm']").change(function(){
    	shoConFees();
    });
     $(document).on("change", "#bank_code", function(){
    	shoConFees();
    });
    function showBankList(selected_radio)
    {
    	$.ajax({
				url: app_base_url+"index.php/utilities/get_bank_list_options",
				type: "POST",
				dataType: "html",
				async: false,
				success: function(bank_list)
				{
					$(bank_list).insertAfter(selected_radio);
				}
			});
    }
	function shoConFees()
	{
		var amount = $(".grand_total_amount:first").text();
		var amt_arr = amount.split(" ");
		amount = amt_arr[amt_arr.length-1];
		var selected_radio = $("input[name='selected_pm']:checked");
		var selected_pm = selected_radio.val();
		var bank_code = $("#bank_code").val();
		$(".con_fees_section").remove();
		if((amount.trim() > 0) && (selected_pm!=""))
		{
			if(selected_pm == "TECHP" && bank_code == undefined)
			{
				showBankList(selected_radio);
				return false;
			}
			if(selected_pm != "TECHP")
			{
				bank_code = 0;
				$("#bank_code").remove();
			}
			var pm_arr = selected_pm.split("_");
			var pm = pm_arr[0];
			var method = pm_arr[1];
			var data = "amount="+amount+"&selected_pm="+pm+"&method="+method+"&bank_code="+bank_code;
			$.ajax({
				url: app_base_url+"index.php/utilities/get_instant_recharge_convenience_fees/1",
				type: "POST",
				data: data,
				dataType: "JSON",
				async: false,
				success: function(data)
				{
					var con_fees = parseFloat(data["cf"]).toFixed(2);
					var new_grand_total = 0;
					var prev_con_fees = parseInt($("#convenience_fees").text());
					if(prev_con_fees > 0){
						new_grand_total = amount - prev_con_fees;
					}
					else{
						new_grand_total = data["total"];
					}
					if(isNaN(con_fees))
						con_fees = 0;
					new_grand_total = new_grand_total.toFixed(2);
					$(".grand_total_amount").text(new_grand_total);
					$("#convenience_fees").text(con_fees);
				}
			});
		}
	}
/* $(document).on('scroll', function(){
    if ($('#slidebarscr')[0].offsetTop < $(document).scrollTop()){
    	var top = $(document).scrollTop();
    	var height = $(window).height();
    	//alert(top);
    	if((top >= 243) || (height <300))
    	{
    		$("#slidebarscr").css({position: "fixed", top:0});  	
    	}   
    	else if ((top <243) || (height < 300))
    	{
    		$("#slidebarscr").css({position: "", top:0});  	
    	}         
        
    }
});  */
});
</script>

<?php
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/booking_script.js'), 'defer' => 'defer');
?>
