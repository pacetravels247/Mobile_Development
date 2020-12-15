<?php
$module_value = md5('hotel');
$currency_symbol = $this->currency->get_currency_symbol($pre_booking_params['default_currency']);
//debug($pre_booking_params);exit;
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
$subtotal = $hotel_total_price;
$pre_booking_params['convenience_fees'] = $convenience_fees;
$hotel_total_price = roundoff_number($pre_booking_params['convenience_fees']+$hotel_total_price);
/********************************* Convenience Fees *********************************/
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('provablib.js'), 'defer' => 'defer');

$book_login_auth_loading_image	 = '<div class="text-center loader-image"><img src="'.$GLOBALS['CI']->template->template_images('loader_v3.gif').'" alt="please wait"/></div>';
$CancellationPolicy = $pre_booking_params['CancellationPolicy'];
//$CancellationPolicy = $pre_booking_params['CancellationPolicy'];
//debug($CancellationPolicy);

?>
<style>
	.topssec::after{display:none;}
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
	<div class="container">
  <div class="ovrgo">
	<div class="bktab1 xlbox <?=$review_tab_details_class?>">
		<div class="col-xs-8 toprom nopad">
		  <div class="col-xs-12 nopad full_room_buk">
			<div class="bookcol">
			  <div class="hotelistrowhtl">
				<div class="col-md-4 nopad xcel">
				  <div class="imagehotel">
				   	<?php if($pre_booking_params['HotelImage']):?>
				  		<img alt="HotelImage" src="<?=$pre_booking_params['HotelImage']?>">
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
				    <div class="sckint">
			  <div class="ffty">
				<div class="borddo brdrit"> <span class="lblbk_book">
				<span class="fa fa-calendar"></span>
				Check-in</span>
				  <div class="fuldate_book"> <span class="bigdate_book"><?=$hotel_checkin_date[0]?></span>
					<div class="biginre_book"> <?=$hotel_checkin_date[1]?><br>
					  <?=$hotel_checkin_date[2]?> </div>
				  </div>
				</div>
			  </div>
			  <div class="ffty">
				<div class="borddo"> <span class="lblbk_book"> <span class="fa fa-calendar"></span> Check-out</span>
				  <div class="fuldate_book"> <span class="bigdate_book"><?=$hotel_checkout_date[0]?></span>
					<div class="biginre_book"> <?=$hotel_checkout_date[1]?><br>
					  <?=$hotel_checkout_date[2]?> </div>
				  </div>
				</div>
			  </div>
			  <div class="clearfix"></div>
			  <div class="nigthcunt">Night(s) <?=$search_data['no_of_nights']?>, Room(s) <?=$search_data['room_count']?></div>
			</div>
				</div>

			  </div>
			
			</div>
		  </div>
		</div>
		<div class="clearfix"></div>
		<div class="col-xs-8 nopad">
		 <div class="canpalidv">
          <span class="canpoliycont">Cancellation Policy</span>
          <div class="canpoly_cont">
          		<?php echo array_shift($CancellationPolicy); ?>
          </div>
		</div>
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
											<button class="b-btn bookallbtn splhtlbku" type="submit">Apply</button>
										</div>  
									</div>
								</div>-->
	    </div>
		  <div class="col-xs-12 nopadding full_log_tab">
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
						<div class="col-xs-8 celoty">
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
										<!-- <div class="col-xs-2 nopadding"><input type="text" placeholder="+91" class="newslterinput nputbrd"></div>
										<div class="col-xs-1 nopadding"><div class="sidepo">-</div></div>
										<div class="col-xs-9 nopadding">
											<input type="text" id="booking_user_mobile" placeholder="Mobile Number" class="newslterinput nputbrd _numeric_only _guest_validate" maxlength="10">
										</div> -->
										<div class="col-xs-2 nopadding">
											<select name="country_code" class="newslterinput nputbrd _numeric_only " required>
																	<?php echo diaplay_phonecode($phone_code,$active_data, $user_country_code); ?>
																</select> 
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
										 <div style="" class="hide alert alert-danger"></div>
									</div>
									
									<div id="book_login_auth_loading_image" style="display: none">
										<?=$book_login_auth_loading_image?>
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
						<div class="col-xs-4 celoty nopad">
							<div class="insidechs booklogin">
								<div class="leftpul">
			<?php 
				$social_login1 = 'facebook';
				$social1 = is_active_social_login($social_login1);
				if($social1){
					$GLOBALS['CI']->load->library('social_network/facebook');
					echo $GLOBALS['CI']->facebook->login_button ();
				} 
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
				} ?>
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
		  <div class="col-xs-4 full_room_buk hide">
			<div class="sckint hide">
			  <div class="ffty">
				<div class="borddo brdrit"> <span class="lblbk_book">
				<span class="fa fa-calendar"></span>
				Check-in</span>
				  <div class="fuldate_book"> <span class="bigdate_book"><?=$hotel_checkin_date[0]?></span>
					<div class="biginre_book"> <?=$hotel_checkin_date[1]?><br>
					  <?=$hotel_checkin_date[2]?> </div>
				  </div>
				</div>
			  </div>
			  <div class="ffty">
				<div class="borddo"> <span class="lblbk_book"> <span class="fa fa-calendar"></span> Check-out</span>
				  <div class="fuldate_book"> <span class="bigdate_book"><?=$hotel_checkout_date[0]?></span>
					<div class="biginre_book"> <?=$hotel_checkout_date[1]?><br>
					  <?=$hotel_checkout_date[2]?> </div>
				  </div>
				</div>
			  </div>
			  <div class="clearfix"></div>
			  <div class="nigthcunt">Night(s) <?=$search_data['no_of_nights']?>, Room(s) <?=$search_data['room_count']?></div>
			</div>
			<div class="bokrom_details">
			  <table class="table table-condensed bokdtsx">
			    <thead>
			      <tr>
			        <th>Room</th>
			        <td>1</td>
			      </tr>
			      <tr>
			        <th>Guest</th>
			        <td><i class="fa fa-male" aria-hidden="true"></i>&nbsp;2</td>
			      </tr>
			      <tr>
			        <th>Price/Night</th>
			        <td>RS.755</td>
			      </tr>
			      <tr>
			        <th>Nights</th>
			        <td>10</td>
			      </tr>
			      <tr>
			        <th>Total Price</th>
			        <td>RS.7555</td>
			      </tr>
			      <tr class="taxserfs">
			        <th>Taxes & Service Fee</th>
			        <td>RS.0</td>
			      </tr>
			      <tr class="totlamt">
			        <th>Grand Total</th>
			        <td>RS.7555/-</td>
			      </tr>
			    </thead>
			  </table>

			</div>
		  </div>
		</div>
		<div class="clearfix"></div>
		
		<div class="clearfix"></div>

	
	<div class="bktab2 xlbox <?=$travellers_tab_details_class?>">
		<div class="topalldesc">
			<div class="col-xs-12 nopad">
				<div class="bookcol">
			  <div class="hotelistrowhtl">
				<div class="col-md-4 nopad xcel">
				  <div class="imagehotel">
				  
				  	<?php if($pre_booking_params['HotelImage']):?>
				  		<img alt="HotelImage" src="<?=$pre_booking_params['HotelImage']?>">
				  	<?php else:?>
				  		<img src="" alt="Hotel_img" src="<?=$GLOBALS['CI']->template->template_images('default_hotel_img.jpg') ?>" class="lazy h-img">
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
				</div>
			  </div>
			</div>
			</div><!-- Outer Summary -->
			<div class="col-xs-4 nopadding celtbcel colrcelo">
				<div class="bokkpricesml">
					<div class="travlrs">Travelers: <span class="fa fa-male"></span> <?=array_sum($search_data['adult_config'])?> |  <span class="fa fa-child"></span> <?=array_sum($search_data['child_config'])?></div>
					<div class="totlbkamnt grandtotal"> Total Amount <?=$this->currency->get_currency_symbol($pre_booking_params['default_currency'])?> <?=($hotel_total_price)?>/-</div>
					
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
		<div class="padpaspotr">
		<div class="col-xs-8 nopadding">
		<div class="fligthsdets">
		<?php
		
/**
 * Collection field name 
 */
//Title, Firstname, Middlename, Lastname, Phoneno, Email, PaxType, LeadPassenger, Age, PassportNo, PassportIssueDate, PassportExpDate
$total_adult_count	= array_sum($search_data['adult_config']);
$total_child_count	= array_sum($search_data['child_config']);
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
$total_pax_count	= $total_adult_count+$total_child_count;
//First Adult is Primary and and Lead Pax
$adult_enum = $child_enum = get_enum_list('title');
$gender_enum = get_enum_list('gender');
unset($adult_enum[MASTER_TITLE]); // Master is for child so not required
unset($child_enum[MASTER_TITLE]); // Master is not supported in TBO list
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
		<form action="<?=base_url().'index.php/hotel/pre_booking/'.$search_data['search_id']?>" method="POST" autocomplete="off">
		<div class="hide">
			<?php $dynamic_params_url = serialized_data($pre_booking_params);?>
			<input type="hidden" required="required" name="token"		value="<?=$dynamic_params_url;?>" />
			<input type="hidden" required="required" name="token_key"	value="<?=md5($dynamic_params_url);?>" />
			<input type="hidden" required="required" name="op"			value="book_flight">
			<input type="hidden" required="required" name="booking_source"		value="<?=$booking_source?>" readonly>
			<input type="hidden" required="required" name="promo_code_discount_val" id="promo_code_discount_val" value="0.00" readonly>
			<input type="hidden" required="required" name="promo_code" id="promocode_val" value="" readonly>
		</div>
			 <div class="flitab1">
			<div class="moreflt boksectn">
					<div class="ontyp">
						<div class="labltowr arimobold">Please enter names as on passport. </div>

<?php
	$child_age = @$search_data['child_age'];
	if(is_logged_in_user()) {
		$traveller_class = ' user_traveller_details ';
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
			<select class="mySelectBoxClass flyinputsnor name_title" name="name_title[]" required>
			<?php echo (is_adult($pax_index, $total_adult_count) ? $adult_title_options : $child_title_options)?>
			</select>
			</div>
			</div>
			<div class="col-xs-4 spllty">
				  <input value="<?=@$cur_pax_info['first_name']?>" required="required" type="text" name="first_name[]" id="passenger-first-name-<?=$pax_index?>" class="clainput alpha_space <?=$traveller_class?>" maxlength="45" placeholder="Enter First Name" data-row-id="<?=($pax_index);?>"/>
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
									                    <div class="col-md-8 col-xs-8 nopadding_right">
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
									                    <div class="col-md-4 col-xs-4 nopadding_left">
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
					<div class="hide">
					<input type="hidden" name="billing_country" value="92">
					<input type="hidden" name="billing_city" value="test">
					<input type="hidden" name="billing_zipcode" value="test">
					<input type="hidden" name="billing_address_1" value="test">
					</div>
					<div class="col-xs-8 nopad">
					<div class="col-xs-2 nopadding">
					<select name="country_code" class="newslterinput nputbrd _numeric_only " required>
											<?php echo diaplay_phonecode($phone_code,$active_data, $user_country_code); ?>
										</select> 
					</div>
					<div class="col-xs-1"><div class="sidepo">-</div></div>
					<div class="col-xs-6 nopadding">
					<input value="<?=@$lead_pax_details['phone'] == 0 ? '' : @$lead_pax_details['phone'];?>" type="text" name="passenger_contact" id="passenger-contact" placeholder="Mobile Number" class="newslterinput nputbrd _numeric_only" maxlength="10" required="required">
					</div>
					<div class="clearfix"></div>
					<div class="emailperson col-xs-9 nopad">
					<input value="<?=@$lead_pax_details['email']?>" type="text" maxlength="80" required="required" id="billing-email" class="newslterinput nputbrd" placeholder="Email" name="billing_email">
					</div>
					</div>
					<div class="clearfix"></div>
					<div class="notese">Your mobile number will be used only for sending hotel related communication.</div>
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
	 	<?php if(is_logged_in_user() == true) { ?>
			<div class="col-xs-4 nopadding">
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
			</div>
		<?php } ?>
		</div>
	</div>
 </div>
	</div>
</div>
<span class="hide">
	<input type="hidden" id="pri_journey_date" value='<?=date('Y-m-d',strtotime($search_data['from_date']))?>'>
</span>
<?php 
function room_price_details($pre_booking_params, $search_data, $currency_obj)
{
	$token = $pre_booking_params['price_token'];
	$adult_config = $search_data['adult_config'];
	$child_config = $search_data['child_config'];
	$no_of_nights = $search_data['no_of_nights'];
	$default_currency = $currency_obj->get_currency_symbol($pre_booking_params['default_currency']);
	
	//echo $CancellationPolicy;
	$grand_total = 0;
	$room_price_details = '';
	$room_price_details .= '<ul>';
	$room_price_details .= '<li class="baseli hedli">
									<ul>
										<li class="wid10">Room</li>
										<li class="wid20">Guest</li>
										<li class="wid20">Price/Night</li>
										
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
			$temp_price_details = $GLOBALS['CI']->hotel_lib->update_room_markup_currency($token_v, $currency_obj, $search_data['search_id']);
			$RoomPrice = $temp_price_details['RoomPrice'];//getting price whole night price, not by per_night_wise
			$per_night_price = ($RoomPrice/$no_of_nights);
			$room_tax = $GLOBALS['CI']->hotel_lib->tax_service_sum($temp_price_details, $token_v);
			$subtotal = $RoomPrice+$room_tax; 
			//echo 'herre'.$subtotal;exit;
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
function diaplay_phonecode($phone_code,$active_data, $user_country_code)
								{
									
									//debug($phone_code);exit;
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
									
									
										$list .="<option value=".$code['country_code']."  ".$selected." >".$code['country_code']."</option>";
									}
									 return $list;
									
								}
?>
<?php
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/hotel_booking.js'), 'defer' => 'defer');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/booking_script.js'), 'defer' => 'defer');
