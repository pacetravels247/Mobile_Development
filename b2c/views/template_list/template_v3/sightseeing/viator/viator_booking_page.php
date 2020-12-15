<?php
$time_arr=array(
    '' => 'Please select',
    '00:00' => '12:00 AM',
    '00:30' => '12:30 AM',
    '01:00' => '1:00 AM',
    '01:30' => '1:30 AM',
    '02:00' => '2:00 AM',
    '02:30' => '2:30 AM',
    '03:00' => '3:00 AM',
    '03:30' => '3:30 AM',
    '04:00' => '4:00 AM',
    '04:30' => '4:30 AM',
    '05:00' => '5:00 AM',
    '05:30' => '5:30 AM',
    '06:00' => '6:00 AM',
    '06:30' => '6:30 AM',
    '07:00' => '7:00 AM',
    '07:30' => '7:30 AM',
    '08:00' => '8:00 AM',
    '08:30' => '8:30 AM',
    '09:00' => '9:00 AM',
    '09:30' => '9:30 AM',
    '10:00' => '10:00 AM',
    '10:30' => '10:30 AM',
    '11:00' => '11:00 AM',
    '11:30' => '11:30 AM',
    '12:00' => '12:00 PM',
    '12:30' => '12:30 PM',
    '13:00' => '1:00 PM',
    '13:30' => '1:30 PM',
    '14:00' => '2:00 PM',
    '14:30' => '2:30 PM',
    '15:00' => '3:00 PM',
    '15:30' => '3:30 PM',
    '16:00' => '4:00 PM',
    '16:30' => '4:30 PM',   
    '17:00' => '5:00 PM',
    '17:30' => '5:30 PM',
    '18:00' => '6:00 PM',
    '18:30' => '6:30 PM',
    '19:00' => '7:00 PM',
    '19:30' => '7:30 PM',
    '20:00' => '8:00 PM',
    '20:30' => '8:30 PM',
    '21:00' => '9:00 PM',
    '21:30' => '9:30 PM',
    '22:00' => '10:00 PM',
    '22:30' => '10:30 PM',
    '23:00' => '11:00 PM',
    '23:30' => '11:30 PM',
    
);
// debug($pre_booking_params['BookingQuestions']);
// exit;
$module_value = md5('activities');
// debug($pre_booking_params);
// exit;
$currency_symbol = $this->currency->get_currency_symbol($pre_booking_params['default_currency']);
//debug($pre_booking_params);exit;
$CI=&get_instance();
$template_images = $GLOBALS['CI']->template->template_images();
$mandatory_filed_marker = '<sup class="text-danger">*</sup>';

$travel_date = sightseeing_travel_date($pre_booking_params['booking_date']);
$travel_date = explode('|', $travel_date);
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



if($pre_booking_params['default_currency']=='INR'){
	$trip_total_price = roundoff_number($this->sightseeing_lib->total_price($pre_booking_params['markup_price_summary']));	
}else{
	$trip_total_price = $this->sightseeing_lib->total_price($pre_booking_params['markup_price_summary']);
}



/********************************* Convenience Fees *********************************/
$subtotal = $trip_total_price;
$pre_booking_params['convenience_fees'] = $convenience_fees;
//$trip_total_price = roundoff_number($pre_booking_params['convenience_fees']+$trip_total_price);
/********************************* Convenience Fees *********************************/
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('provablib.js'), 'defer' => 'defer');

$book_login_auth_loading_image	 = '<div class="text-center loader-image"><img src="'.$GLOBALS['CI']->template->template_images('loader_v3.gif').'" alt="please wait"/></div>';
//debug($pre_booking_params);
$LastCancellationDate = $pre_booking_params['TM_LastCancellation_date'];
$current_date = date('Y-m-d');
if($current_date <$LastCancellationDate ){
	$LastCancellationDate = $LastCancellationDate;
}else{
	$LastCancellationDate = '';
}

//calculating price
$tax_total = 0;
$grand_total = 0;


$grand_total = $pre_booking_params['markup_price_summary']['TotalDisplayFare'];

$tax_total += $pre_booking_params['convenience_fees'];
$grand_total += $pre_booking_params['convenience_fees'];

if($pre_booking_params['default_currency']=='INR'){
	$grand_total = ceil($grand_total);
	//echo $grand_total;exit;
	$trip_total_price = ceil($trip_total_price);	

	$trip_total_price  = ceil($trip_total_price);	
}else{
	$grand_total = $grand_total;
	//echo $grand_total;exit;
	$trip_total_price = $trip_total_price;

	$trip_total_price  = $trip_total_price;
}
$trip_total_price = $trip_total_price - $pre_booking_params['markup_price_summary']['_GST'];

//echo $total_room_price;exit;
$total_pax = 0;

//$search_params  = json_decode(base64_decode($pre_booking_params['search_params']),true);

//$age_bd = json_decode(base64_decode($search_params['age_band']),true);

$total_adult_count = 0;
$total_child_count = 0;
$total_infant_count =0;
$total_youth_count = 0;
$total_senior_count = 0;
$age_band_details_arr = array('1'=>'Adult','2'=>'Child','3'=>'Infant','4'=>'Youth','5'=>'Senior');
foreach ($pre_booking_params['AgeBands'] as $p_key => $p_value) {
	$total_pax += $p_value['count'];
	if($age_band_details_arr[$p_value['bandId']]=='Adult'){
		$total_adult_count +=$p_value['count'];
	}elseif($age_band_details_arr[$p_value['bandId']]=='Child'){
		$total_child_count += $p_value['count'];
	}elseif($age_band_details_arr[$p_value['bandId']]=='Infant'){
		$total_infant_count += $p_value['count'];
	}elseif($age_band_details_arr[$p_value['bandId']]=='Youth'){
		$total_youth_count += $p_value['count'];
	}elseif($age_band_details_arr[$p_value['bandId']]=='Senior'){
		$total_senior_count += $p_value['count'];
	}
}


?>


<style>
   /* .fixed {
	position: fixed;
	top:60px;
	width: 100%;
	bottom: 0;
}*/
	.topssec::after{display:none;}
</style>
<input type="hidden" id="total_pax" value="<?=$total_pax?>">
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
  <div class="ovrgo sight_book_page">
	<div class="bktab1 xlbox <?=$review_tab_details_class?>">
		<div class="col-xs-12 col-md-8 toprom nopad">
		  <div class="col-xs-12 nopad full_room_buk">
			<div class="bookcol">
			  <div class="hotelistrowhtl">
				<div class="col-md-4 col-sm-4 col-xs-4 nopad xcel">
				  <div class="imagehotel">

				  		<?php if($pre_booking_params['ProductImage']!=''):?>
				  		<?php
				  			//$image = $base_url.'/'.base64_encode($pre_booking_params['HotelCode']).'/0';
				  			$image = $pre_booking_params['ProductImage'];
				  		?>
				  		<img alt="<?=$pre_booking_params['ProductName']?>" src="<?=$image?>">
				  	<?php else:?>
				  		<img alt="Hotel_img" src="<?=$GLOBALS['CI']->template->template_images('no_image_available.jpg')?>" class="lazy h-img">
				  	<?php endif;?>
				  </div>
				</div>
				<div class="col-md-12 col-sm-8 col-xs-8 padall10 xcel">
				  <div class="hotelhed">Title:<?=$pre_booking_params['ProductName']?></div>
				  <div class="hotelhed1">Tour Code:<?=$pre_booking_params['GradeCode']?></div>
				  <?php if($pre_booking_params['GradeDescription']):?>
				  	 <div class="hotelhed2">Tour Code:<?=$pre_booking_params['GradeDescription']?></div>
				  <?php endif;?>

				  <div class="clearfix"></div>
				  <div class="bokratinghotl rating-no">				   
					  <?=print_star_rating($pre_booking_params['StarRating'])?>
				  </div>
				  <div class="clearfix"></div>
				  <div class="mensionspl"> <?
				  if($pre_booking_params['DeparturePointAddress']){
				  	echo $pre_booking_params['DeparturePointAddress'];
				  }elseif($pre_booking_params['DeparturePoint']){
				  	echo $pre_booking_params['DeparturePoint'];
				  }else{
				  	echo $pre_booking_params['Destination'];
				  }
				  ?> </div>

				  <div class="sckint">
			  <div class="ffty">
				<div class="borddo brdrit"> <span class="lblbk_book">
				<span class="fa fa-calendar"></span>
				Travel Date</span>
				  <div class="fuldate_book"> <span class="bigdate_book"><?=$travel_date[0]?></span>
					<div class="biginre_book"> <?=$travel_date[1]?><br>
					  <?=$travel_date[2]?> </div>
				  </div>
				</div>
			  </div>
			
			  <div class="clearfix"></div>
			  <div class="nigthcunt">Total Pax:<?=$total_pax?></div>
			</div>
				</div>
			  </div>
			</div>
		  </div>
		  
		  	<div class="col-xs-12 nopadding full_log_tab">
	  <div class="fligthsdets">
		<div class="flitab1">
				<div class="clearfix"></div>
				<div class="sepertr"></div>
				
				<div class="sepertr"></div>
				<!-- LOGIN SECTION STARTS -->
				<?php if(is_logged_in_user() == false) { ?>
				<div class="loginspld">
					<div class="logininwrap">
					<div class="signinhde">
						Sign in now to Book Online
					</div>
					<div class="newloginsectn">
						<div class="col-xs-7 celoty">
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
										<div class="col-xs-5 nopadding">
												<!-- <input type="text" placeholder="+91" class="newslterinput nputbrd"> -->
											<select name="" class="newslterinput nputbrd _numeric_only" id="before_country_code"  required>
												<?php 
												//debug($phone_code);exit;
												echo diaplay_phonecode($phone_code,$active_data, $user_country_code); ?>
											</select> 
										</div>
										<div class="col-xs-1 nopadding"><div class="sidepo">-</div></div>
										<div class="col-xs-6 nopadding">
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
						<div class="col-xs-5 celoty">
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
				<a class="logspecify tweetcolor"><span class="fa fa-twitter"></span>
				<div class="mensionsoc">Login with Twitter</div>
				</a>
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
		  <div class="col-xs-4 nopad full_room_buk hide">
			<div class="sckint">
			  <div class="ffty">
				<div class="borddo brdrit"> <span class="lblbk_book">
				<span class="fa fa-calendar"></span>
				Travel Date</span>
				  <div class="fuldate_book"> <span class="bigdate_book"><?=$travel_date[0]?></span>
					<div class="biginre_book"> <?=$travel_date[1]?><br>
					  <?=$travel_date[2]?> </div>
				  </div>
				</div>
			  </div>			 
			  <div class="clearfix"></div>
			  <div class="nigthcunt">Total Pax:<?=$total_pax?></div>
			</div>
		  </div>
		</div>
		<div class="col-xs-4 full_room_buk rhttbepa">
		   <div id="slidebarscr" style="width:370px;">
		 	<table class="table table-condensed tblemd">
			 	<tbody>
			 	  <tr class="rmdtls">
			        <th>Activity Details</th>
			        <td></td>
			      </tr>
			    
			      <tr>
			        <th>No of Pax</th>
			        <td><?=$total_pax?></td>
			      </tr>			     
			      <?php if($LastCancellationDate):?>
				      <tr class="frecanpy">
				        <th>Free Cancellation till:<br/><a  href="#" data-target="#roomCancelModal"  data-toggle="modal" >View Cancellation Policy</a></th>
				        <td><?=local_month_date($LastCancellationDate)?></td>
				        
				      </tr>
				  <?php else:?>
				  		<tr class="frecanpy">
				        <th>Cancellation Policy:<br/><a  href="#" data-target="#roomCancelModal"  data-toggle="modal" >View Cancellation Policy</a></th>
				        <td>Non-Refundable</td>
				      </tr>
			 	 <?php endif;?>
			 	<!--   <tr class="frecanpy">
				        <th><a  href="#" data-target="#roomCancelModal" data-toggle="modal" >View Cancellation Policy:</a></th>
			      </tr> -->
			      <tr>
			        <th>Total Price</th>
			        <td><?=$this->currency->get_currency_symbol($pre_booking_params['default_currency'])?> <?=($trip_total_price)?></td>
			      </tr>
			      <tr class="texdiv">
			        <th>Taxes & Service fee</th>
			        <td><?=$this->currency->get_currency_symbol($pre_booking_params['default_currency'])?> <?=$tax_total?></td>
			      </tr>
			      <?php if($pre_booking_params['markup_price_summary']['_GST'] > 0){ ?>
			      <tr class="texdiv">
			        <th>GST</th>
			        <td><?=$this->currency->get_currency_symbol($pre_booking_params['default_currency'])?> <?=$pre_booking_params['markup_price_summary']['_GST'];?></td>
			      </tr>
			      <?php } ?>
			      
			      <tr class="grd_tol">
			        <th>Grand Total</th>
			        <td><?=$this->currency->get_currency_symbol($pre_booking_params['default_currency'])?> <?=($grand_total)?></td>
			      </tr>
			    </tbody>
			  </table>
			 
		   </div>
		  </div>
		
	
		<div class="clearfix"></div>
	
	</div>
	<div class="bktab2 xlbox <?=$travellers_tab_details_class?>">
	  <div class="col-xs-12 col-md-8 nopad">
		<div class="col-xs-12 topalldesc">
			<div class="col-xs-12 nopad">
				<div class="bookcol">
			  <div class="hotelistrowhtl">
				<div class="col-md-4 nopad xcel">
				  <div class="imagehotel">
				  		<?php if($pre_booking_params['ProductImage']!=''):?>

				  		<?php
				  			//$image = $base_url.'/'.base64_encode($pre_booking_params['HotelCode']).'/0';
				  			$image= $pre_booking_params['ProductImage'];
				  		?>

				  		<img alt="<?=$pre_booking_params['ProductName']?>" src="<?=$image?>">
				  	<?php else:?>
				  		<img alt="Hotel_img" src="<?=$GLOBALS['CI']->template->template_images('no_image_available.jpg')?>" class="lazy h-img">
				  	<?php endif;?>
				  </div>
				</div>
				<div class="col-md-8 padall10 xcel">
				  <div class="hotelhed"><?=$pre_booking_params['ProductName']?></div>
				  <div class="clearfix"></div>
				  <div class="bokratinghotl rating-no">				   
					  <?=print_star_rating($pre_booking_params['StarRating'])?>
				  </div>
				  <div class="clearfix"></div>
				  	<div class="mensionspl"> <?php
				  		 if($pre_booking_params['DeparturePointAddress']){
						  	echo $pre_booking_params['DeparturePointAddress'];
						  }elseif($pre_booking_params['DeparturePoint']){
						  	echo $pre_booking_params['DeparturePoint'];
						  }else{
						  	echo $pre_booking_params['Destination'];
						  } 


				  ?> </div>
				  <div class="bokkpricesml">
					<div class="travlrs"><span class="travlrsnms">Travelers:</span><span class="fa fa-male"></span> <?=$total_pax?></div>
					<div class="totlbkamnt grandtotal"> <span class="ttlamtdvot">Total Amount</span><?=$this->currency->get_currency_symbol($pre_booking_params['default_currency'])?> <?=($trip_total_price)?>/-</div>
					</div>
				</div>
			  </div>
			</div>
			</div><!-- Outer Summary -->
			<div class="col-xs-4 nopadding celtbcel colrcelo hide">
				
			</div>
		</div>
		<div class="clearfix"></div>
		
		</div>
		
		<div class="col-xs-4 rhttbepa">
			   <div id="nxtbarslider" style="width:370px;">
			 	<table class="table table-condensed tblemd">
				 	<tbody>
				 	  <tr class="rmdtls">
				        <th>Activity Details</th>
				        <td></td>
				      </tr>
				  		
				      <tr>
				        <th>No of Pax</th>				        
				        <td><?=$total_pax?></td>
				      </tr>
				    
				       <?php if($LastCancellationDate):?>
					      <tr class="frecanpy">
					        <th>Free Cancellation till:<br/><a  href="#" data-target="#roomCancelModal"  data-toggle="modal" >View Cancellation Policy</a></th>
					        <td><?=local_month_date($LastCancellationDate)?></td>
					        
					      </tr>
					  <?php else:?>
					  		<tr class="frecanpy">
					        <th>Cancellation Policy:<br/><a  href="#" data-target="#roomCancelModal" data-toggle="modal">View Cancellation Policy</a></th>
					        <td>Non-Refundable</td>
					      </tr>

				 	 <?php endif;?>
				 	<!--   <tr class="frecanpy">
				        <th><a  href="#" data-target="#roomCancelModal" data-toggle="modal" >View Cancellation Policy:</a></th>
			      		</tr>
				 	   -->
				      <tr>
				        <th>Total Price</th>
				        <td><?=$this->currency->get_currency_symbol($pre_booking_params['default_currency'])?> <?=$trip_total_price?></td>
				      </tr>
				      <tr class="texdiv">
				        <th>Taxes & Service fee</th>
				        <td><?=$this->currency->get_currency_symbol($pre_booking_params['default_currency'])?> <?=$tax_total?></td>
				      </tr>
				      <?php if($pre_booking_params['markup_price_summary']['_GST'] > 0){ ?>
				      <tr class="texdiv">
				        <th>GST</th>
				        <td><?=$this->currency->get_currency_symbol($pre_booking_params['default_currency'])?> <?=$pre_booking_params['markup_price_summary']['_GST'];?></td>
				      </tr>
				      <?php } ?>
				      <tr class="promo_code_discount hide">
				        <th>Promo Code Discount</th>
				        <td class="promo_discount_val"></td>
				      </tr>
					<tr class="grd_tol">
				        <th>Grand Total</th>
				        <td class="grandtotal"><?=$this->currency->get_currency_symbol($pre_booking_params['default_currency'])?> <?=$grand_total?>/-</td>
				      </tr>
				      				    </tbody>
				  </table>
			   </div>
		</div>

		<div class="col-xs-12 padpaspotr">
		<div class="col-xs-12 nopadding">
		<div class="fligthsdets">
		<?php
/**
 * Collection field name 
 */
//Title, Firstname, Middlename, Lastname, Phoneno, Email, PaxType, LeadPassenger, Age, PassportNo, PassportIssueDate, PassportExpDate


$total_adult_count	= $total_adult_count;
$total_child_count	= $total_child_count;
$total_infant_count =$total_infant_count;
$total_youth_count = $total_youth_count;
$total_senior_count = $total_senior_count;


//------------------------------ DATEPICKER END
$total_pax_count	= $total_adult_count+$total_child_count+$total_infant_count+$total_youth_count+$total_senior_count;
//First Adult is Primary and and Lead Pax
$adult_enum = $child_enum = get_enum_list('viator_title');
$gender_enum = get_enum_list('gender');
// unset($adult_enum[MASTER_TITLE]); // Master is for child so not required
// unset($child_enum[MASTER_TITLE]); // Master is not supported in TBO list
// unset($adult_enum[MISS_TITLE]); // Miss is not supported in GRN list
// unset($child_enum[MISS_TITLE]);
// unset($child_enum[C_MRS_TITLE]);
// unset($adult_enum[A_MASTER]);
unset($adult_enum[MISS_TITLE]);
unset($child_enum[MASTER_TITLE]);
$adult_title_options = generate_options($adult_enum, false, true);
$child_title_options = generate_options($child_enum, false, true);

$youth_title_options = $adult_title_options;
$senior_title_options = $adult_title_options;
$infant_title_options  = $child_title_options;

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
function is_lead_pax($pax_count)
{
	return ($pax_count == 1 ? true : false);
}
$lead_pax_details = @$pax_details[0];
 ?>
<form action="<?=base_url().'index.php/sightseeing/pre_booking/'.$search_id?>" method="POST" autocomplete="off">
	

		<div class="hide">
			<?php $dynamic_params_url = serialized_data($pre_booking_params);?>
			<input type="hidden" name="BlockTourId" value="<?=$pre_booking_params['BlockTourId']?>">
			<input type="hidden" required="required" name="token"		value="<?=$dynamic_params_url;?>" />
			<input type="hidden" required="required" name="token_key"	value="<?=md5($dynamic_params_url);?>" />
			<input type="hidden" required="required" name="op"			value="book_flight">
			<input type="hidden" required="required" name="booking_source"		value="<?=$booking_source?>" readonly>
			<input type="hidden" required="required" name="promo_code_discount_val" id="promo_code_discount_val" value="0.00" readonly>
			<input type="hidden" required="required" name="promo_code" id="promocode_val" value="" readonly>

			<input type="hidden" required="required" name="promo_actual_value" id="promo_actual_value" value="" readonly>

		</div>
			 <div class="flitab1">
			<div class="moreflt boksectn">
					<div class="ontyp">
						<div class="labltowr arimobold">Please enter the customer names.</div>
<div class="pasngrinput _passenger_hiiden_inputs" style="padding: 5px !important;">
<?php
	
	if(is_logged_in_user()) {
		$traveller_class = ' user_traveller_details ';
	} else {
		$traveller_class = '';
	}
	$pax_index=1;

	//debug($pre_booking_params['AgeBands']);
	//echo $total_pax;
	foreach ($pre_booking_params['AgeBands'] as $p_key => $p_value) {
	 
	 $name_index = 1;
	//for($pax_index=1; $pax_index <= $total_pax_count; $pax_index++) {//START FOR LOOP FOR PAX DETAILS
	//$cur_pax_info = is_array($pax_details) ? array_shift($pax_details) : array();
		for($pax_index=1;$pax_index<=$p_value['count'];$pax_index++){
?>
			
				<div class="hide hidden_pax_details">
				  <?php 
				  	$passenger_type =1; //adult
				  	if($p_value['bandId']==1){
				  		$passenger_type = 1;
				  	}elseif ($p_value['bandId']==2) {
				  		$passenger_type = 2;//child
				  	}elseif ($p_value['bandId']==3) {
				  		$passenger_type = 3;//infant
				  	}
				  	elseif ($p_value['bandId']==4) {
				  		$passenger_type = 4;//youth
				  	}elseif ($p_value['bandId']==5) {
				  		$passenger_type = 5; //Senior
				  	}
				 	
				  ?>
					<input type="hidden" name="passenger_type[]" value="<?=$passenger_type?>">
					<input type="hidden" name="lead_passenger[]" value="<?=(is_lead_pax($p_key) ? true : false)?>">
					
				</div>
				<div class="col-xs-12 col-sm-2 col-md-1 nopadding">
					
				   <div class="adltnom">
				   		<?php
				   			$title_select_options = $adult_title_options;
				   			$passenger_title =1; //adult
						  	if($p_value['bandId']==1){
						  		$passenger_title = 'Adult';
						  	}elseif ($p_value['bandId']==2) {
						  		$passenger_title = 'Child';//child
						  		$title_select_options  = $child_title_options;
						  	}elseif ($p_value['bandId']==3) {
						  		$passenger_title = 'Infant';//infant
						  		$title_select_options  = $child_title_options;
						  	}
						  	elseif ($p_value['bandId']==4) {
						  		$passenger_title = 'Youth';//youth
						  	}elseif($p_value['bandId']==5) {
						  		$passenger_title = 'Senior'; //Senior
						  	}
						  	echo $passenger_title;
				   		?><?=(is_lead_pax($p_key+$pax_index) ? '- Lead Pax' : '')?></div>
				
				 </div>
				 <div class="col-xs-12 col-sm-10 col-md-11 nopadding">
				 <div class="inptalbox">
					<div class="col-xs-2 col-sm-2 col-md-1 spllty">
						<select class="mySelectBoxClass flyinputsnor name_title" name="name_title[]" required>
						<?php echo $title_select_options;?>
						</select>
					</div>
					<div class="col-xs-3 col-md-2 spllty">
						  <input value="<?=@$cur_pax_info['first_name']?>" required="required" type="text" name="first_name[]" id="passenger-first-name-<?=$name_index?>" class="clainput alpha_space <?=$traveller_class?>"  minlength="2" maxlength="45" placeholder="Enter First Name" data-row-id="<?=($pax_index);?>"/>
						  
					</div>
					<div class="col-xs-3 col-md-2 spllty">
					 	<input value="<?=@$cur_pax_info['last_name']?>" required="required" type="text" name="last_name[]" id="passenger-last-name-<?=$name_index?>" class="clainput alpha_space last_name" minlength="2" maxlength="45" placeholder="Enter Last Name" />
					 </div>
					 <?php 
					 	// debug($pre_booking_params['BookingQuestions']);
					 	// exit;
					 ?>
					  <div class="col-md-8 nopad">
					 <?php if(isset($pre_booking_params['BookingQuestions'])):?>

					 		<?php foreach($pre_booking_params['BookingQuestions'] as $bq_key=>$bq_value):  ?><!-- start-->
                            	<?php if($bq_value['stringQuestionId']=='weights_passengerWeights'):?>
                            		<?php if($bq_value['required']==1){?>
                            				<div class="col-md-5 nopad">
		                                      	 <div class="col-md-6 spllty">
		                                      	
		                                         <input class="form-control numeric clainput" type="text" id="weight" placeholder="<?=$bq_value['title']?>" maxlength="3" name="pax_question[<?=$bq_value["questionId"]?>][]" required="required">
		                                        </div>		                          
		                                    <div class="col-md-6 spllty">
		                                          <div class="mr-slt-div">
		                                            <?php 
		                                              $disable ='disabled';
		                                            	//$disable='';
		                                              if($pax_index==1){
		                                                $disable='';                    
		                                              }
		                                              ?>
		                                          <select class="form-control p-weight weight_measure_<?=$bq_key?>" name="weight" 
		                                          data-key = "<?=$bq_key?>"
		                                          <?=$disable?> id="">
		                                                    <option value="kgs">Kg</option>
		                                                    <option value="pounds">lb</option>
		                                                </select>
		                                          </div>
		                                   </div> 
		                                   <span class="spllty" style="font-size: 11px; display: block;"><?=$bq_value['message'].''.$bq_value['subTitle']?></span>
	                                   </div>
                            		<?php } ?>
                            	<?php elseif($bq_value['stringQuestionId']=='heights_passengerHeights'):?>
                            		<?php if($bq_value['required']==1):?>
                            			<div class="col-md-5 nopad">
	                                      	<div class="col-md-6 spllty">
	                                         	<input class="form-control clainput numeric" type="text" id="weight" placeholder="Heights" maxlength="3" name="pax_question[<?=$bq_value["questionId"]?>][]" required="required">
	                                        </div>
			                                    <div class="col-md-6 spllty">
			                                          <div class="mr-slt-div">
			                                            <?php 
			                                              $disable ='disabled';
			                                            	//$disable='';
			                                              if($pax_index==1){
			                                                $disable='';                    
			                                              }
			                                              ?>
			                                          <select class="form-control p-height height_measure_<?=$bq_key?>" name="height" 
			                                          data-key = "<?=$bq_key?>"
			                                          <?=$disable?> id="">
			                                                    <option value="cm">cm</option>
			                                                    <option value="ft/in">ft/in</option>
			                                                </select>
			                                          </div>
			                                   </div> 
			                                  <span class="spllty" style="font-size: 11px; display: block;"><?=$bq_value['message'].''.$bq_value['subTitle']?></span>
	                                   </div>
                            		<?php endif;?>
                            	<?php elseif($bq_value['stringQuestionId']=='dateOfBirth_dob'):?>
                            		<?php if($bq_value['required']==1):?>
                            			<div class="col-md-2 nopad">
	                                      	<div class="col-md-12 spllty">
                            		
                            			 <input type="text" class="form-control clainput dobdatepickerbook" id=""  name="pax_question[<?=$bq_value["questionId"]?>][]" placeholder="<?=$bq_value['title']?>" required="required" readonly >
                                        </div>
                                        </div>
                            	    <?php endif;?>
                            	<?php elseif($bq_value['stringQuestionId']=='passport_expiry'):?>
                            		<?php if($bq_value['required']==1):?>
                            			<div class="col-md-3 nopad">
	                                      	<div class="col-md-12 spllty">
                            			 <input type="text" class="form-control clainput expiraydatepickerbook" id=""  name="pax_question[<?=$bq_value["questionId"]?>][]" placeholder="<?=$bq_value['title']?>" required="required" readonly >
                            			   </div>
                                        </div>
                            	    <?php endif;?>
                            	<?php elseif($bq_value['stringQuestionId']=='passport_passportNo'):?>
                            		<?php if($bq_value['required']==1):?>
                            			<div class="col-md-3 nopad">
	                                      	<div class="col-md-12 spllty">
                            			 <input type="text" class="form-control clainput " id=""  name="pax_question[<?=$bq_value["questionId"]?>][]" placeholder="<?=$bq_value['title']?>" required="required"  onkeypress="return alphanumeric(event)">
                            			   </div>
                                        </div>
                            	    <?php endif;?>
                            	<?php elseif($bq_value['stringQuestionId']=='passport_nationality'):?>
                            		<?php if($bq_value['required']==1):?>
                            			<div class="col-md-3 nopad">
	                                      	<div class="col-md-12 spllty">
	                                      	<select class="form-control" name="pax_question[<?=$bq_value["questionId"]?>][]" required="required">
	                                      		<?php foreach($phone_code as $phone_val):?>
	                                      			<option value="<?=$phone_val['name']?>"><?=$phone_val['name']?></option>
	                                      		<?php endforeach;?>
	                                      	</select>
                            			 	<span><?=$bq_value['title']?></span>
                            			   </div>
                                        </div>
                            	    <?php endif;?>
                            	<?php endif;?>

                            <?php endforeach;?><!-- end -->                             
                    <?php endif;?>
                                 
				</div>
				</div>
			</div>
			 <div class="clearfix"></div>
<?php
		$name_index++;
		}//for loop
	}//foreach

//}//END FOR LOOP FOR PAX DETAILS
?>
					</div>
				</div>	

<!-- Start Hotel Pickup -->
 <div class="clearfix"></div>
<?php
 //debug($pre_booking_params);exit;
?>
<?php if($pre_booking_params['HotelPickup']):?>
	<div class="pasngrinput _passenger_hiiden_inputs" style="margin-top: 25px;">
		<div class="col-md-8 nopad">
		 	 <div class="col-md-3 nopad">
		 	 	<div class="labbkdiv">
                         <label>Hotel Pickup<span class="starclr">*</span></label>
                  </div>
		 	 </div>
		 	 <?php if($pre_booking_params['HotelList']):?>
		 	  <div class="col-md-7 nopad">
		 	  		<select class="form-control hotelPickup" name="hotelPickupId">
		 	  			<?php foreach($pre_booking_params['HotelList'] as $h_key=>$h_val):?>
		 	  				<option value="<?=$h_val['hotel_id']?>"><?=$h_val['hotel_name']?></option>
		 	  			<?php endforeach;?>
		 	  		</select>
		 	  </div>
		 	  	<div class="col-md-7 nopad hide col-sm-offset-3" id="hotelPickup_name" style="margin-top: 10px;">
                      <input type="text" class="form-control " name="hotelPickup_name" placeholder="Hotel Pickup">
              	</div>

		 	<?php else: ?>
		 		<div class="col-md-7 nopad" id="hotel_pickup_id">
                      <input type="text" class="form-control" name="hotelPickup_name" placeholder="Hotel Pickup">
              	</div>
		 	<?php endif;?>
		</div>
		</div>
<?php endif;?>
<input type="hidden" name="hotel_pickup_list_name" id="hotel_pickup_list_name" value="">
<div class="clearfix"></div>

<!--End -->			
<!-- Booking Question  Start-->
		 <?php if(valid_array($pre_booking_params['BookingQuestions'])):?>
                    <?php foreach($pre_booking_params['BookingQuestions'] as $q_key=>$q_value): $requierd='';?>
                      <?php //debug($q_value); ?>
                        <?php if(trim($q_value['stringQuestionId']) !='dateOfBirth_dob' && trim($q_value['stringQuestionId']) !='passport_expiry' && $q_value['stringQuestionId'] !='weights_passengerWeights' && $q_value['stringQuestionId'] !='heights_passengerHeights' && $q_value['stringQuestionId'] !='passport_passportNo' && $q_value['stringQuestionId'] !='passport_nationality' ):?>
                        <div class="pasngrinput _passenger_hiiden_inputs">
                             <div class="col-md-8 nopad">
                                   <div class="col-md-3 nopad">
                                       <div class="labbkdiv">
                                        <label><?=$q_value['title']?></label>
                                        <?php if($q_value['required']==1){
                                            $requierd='required';
                                            echo '<span class="starclr">*</span>';
                                        }?>
                                            
                                      
                                       </div> 
                                   </div>                                 
                                   <div class="col-md-7 nopad">
                                    <div class="mr-inp-div">
                                        <input type="hidden" name="question_Id[<?=$q_key?>][]" value="<?=$q_value['questionId']?>">

                                   <?php if(trim($q_value['stringQuestionId']=='transfer_departure_date') || trim($q_value['stringQuestionId'])=='transfer_arrival_date' ):?>

                                      <?php
                                        $class='datepickerbook';
                                       
                                      if(trim($q_value['stringQuestionId'])=='transfer_departure_date'){

                                      	$class='transferdeparturedate';
                                      }
                                      if(trim($q_value['stringQuestionId'])=='transfer_arrival_date'){

										$class='transferarrivaldate';
                                      }

                                      ?>
                                      <input type="text" class="form-control <?=$class?> " id=""  name="question[<?=$q_key?>][]" placeholder="<?=trim($q_value['subTitle'])?>" readonly <?=$requierd?>>

                                    <?php elseif((trim($q_value['stringQuestionId']=='transfer_arrival_time')) || (trim($q_value['stringQuestionId']=='transfer_departure_time')) || (trim($q_value['stringQuestionId']=='boarding_time'))|| (trim($q_value['stringQuestionId']=='disembarkation time'))):?>
                                    
                                      <select class="form-control" name="question[<?=$q_key?>][]"  <?=$requierd?>>
                                          <?php foreach($time_arr as $t_key=>$t_value):?>
                                              <option value="<?=$t_key?>"><?=$t_value?></option>
                                          <?php endforeach;?>
                                          
                                      </select>
                                  <?php else:?>
                                      <input class="form-control" type="text" id="" <?=$requierd?> name="question[<?=$q_key?>][]" placeholder="<?=$q_value['subTitle']?>">
                                  <?php endif;?>
                                        <p><?=$q_value['message']?></p>
                                    </div>  
                                   </div>
                                
                                   
                          </div>
                          </div>
                      <?php endif;?>
                    <?php endforeach;?>
            <?php endif;?>
<!-- End-->
<div class="clearfix"></div>

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
					<div class="hide">
					<input type="hidden" name="billing_country" value="92">
					<input type="hidden" name="billing_city" value="test">
					<input type="hidden" name="billing_zipcode" value="test">
					<input type="hidden" name="billing_address_1" value="test">
					</div>
					<div class="col-xs-12 col-md-8 nopad">
					<div class="col-xs-4 nopadding">
					<select name="phone_country_code" class="newslterinput nputbrd _numeric_only " id="after_country_code" required>
											<?php echo diaplay_phonecode($phone_code,$active_data, $user_country_code); ?>
										</select> 
					</div>
					<div class="col-xs-1"><div class="sidepo">-</div></div>
					<div class="col-xs-4 nopadding">
					<input value="<?=@$lead_pax_details['phone'] == 0 ? '' : @$lead_pax_details['phone'];?>" type="text" name="passenger_contact" id="passenger-contact" placeholder="Mobile Number" class="newslterinput nputbrd _numeric_only" maxlength="10" required="required">
					</div>
					<div class="clearfix"></div>
					<div class="emailperson col-xs-9 nopad">
					<input value="<?=@$lead_pax_details['email']?>" type="text" maxlength="80" required="required" id="billing-email" class="newslterinput nputbrd" placeholder="Email" name="billing_email">
					</div>
					</div>
					<div class="clearfix"></div>
					<div class="notese">Your mobile number will be used only for sending activity related communication.</div>
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
<div class="row">
<div class="contbk">
<div class="contcthdngs">Select Payment Mode</div>
<input type="radio" name="selected_pm" value="PAYPAL" checked/> Paypal
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
									$traveller_tab_content = 'You do not have any passenger saved in your list, start adding passenger so that you fvdo not have to type every time. <a href="'.base_url().'index.php/user/profile?active=traveller" target="_blank">Add Now</a>';
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
</div>
<span class="hide">
	<input type="hidden" id="pri_journey_date" value='<?=date('Y-m-d',strtotime($search_data['from_date']))?>'>
</span>
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
				
				<p class="policy_text"><?php echo $pre_booking_params['TM_Cancellation_Policy']; ?></p>

				
			</div>
			<div class="modal-footer">
	          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        </div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
    $(document).on('scroll', function(){
        if ($('#slidebarscr')[0].offsetTop < $(document).scrollTop()){
        	var top = $(document).scrollTop();
        	var height = $(window).height();
        	//alert(height);
        	if((top >= 150) || (top >= 285))  // || (height > 300))
        	{
        		//alert('top'+top);
        		$("#slidebarscr").css({position: "fixed", top:0});  	
        	}   
        	/*else if((top >= 285) && (top < 300))
        	{
        		$("#slidebarscr").css({position: "fixed", top:0});  	
        	}*/
        	else 
        	{
        		//alert('bottom '+top);
        		$("#slidebarscr").css({position: "",top:0});  	
        	}                     
        }
    });  
});
</script>
<!-- <script type="text/javascript">
	$(document).ready(function(){
    $(document).on('scroll', function(){
        if ($('#nxtbarslider')[0].offsetTop < $(document).scrollTop()){
        	var top = $(document).scrollTop();
        	var height = $(window).height();
        	//alert(top);
        	if((top >= 243) || (height <300))
        	{
        		$("#nxtbarslider").css({position: "fixed", top:0});  	
        	}   
        	else if ((top <243) || (height < 300))
        	{
        		$("#nxtbarslider").css({position: "", top:0});  	
        	}         
            
        }
    });  
});
</script> -->

<?php 

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
	$(function(){		

		var start = new Date();
	    start.setFullYear(start.getFullYear() - 70);
	    var end = new Date();
	    end.setFullYear(end.getFullYear() - 1);

	    $(".datepickerbook" ).datepicker(
	      {
	        dateFormat: 'dd MM yy',
	        changeMonth: true,
	        changeYear: true,
	        yearRange: '1970:'+(new Date).getFullYear()    
	      });


	    $(".dobdatepickerbook").datepicker({

	    	dateFormat: 'dd MM yy',
	        changeMonth: true,
	        changeYear: true,
	        yearRange: start.getFullYear()+':'+end.getFullYear()  
	    });
	    $(".expiraydatepickerbook").datepicker({
	        dateFormat:'dd MM yy',
	        changeMonth:true,
	        changeYear:true,
	        minDate:0,
	        yearRange:'2000:'+((new Date).getFullYear()+10)
	    });
	     $(".transferdeparturedate").datepicker({
	        dateFormat:'dd MM yy',
	        changeMonth:true,
	        changeYear:true,
	      	minDate:0
	    });
	     $(".transferarrivaldate").datepicker({
	        dateFormat:'dd MM yy',
	        changeMonth:true,
	        changeYear:true,
	        minDate:0
	    });

	      $(".p-weight").on("change",function(){
	        
	        var product_key = $(this).data('key');
	        var selected_weight = $(this).val();
	        $(".weight_measure_"+product_key).val(selected_weight);
	    });
	    $(".p-height").on("change",function(){
	        
	        var product_key = $(this).data('key');
	        var selected_height = $(this).val();
	        $(".height_measure_"+product_key).val(selected_height);
	    });
	     

	 var selected_text  = $(".hotelPickup option:selected").text();
      $("#hotel_pickup_list_name").val(selected_text);

   $(".hotelPickup").on("change",function(){
        var selected_value = $(this).val();
        var selected_text  = $(".hotelPickup option:selected").text();
        $("#hotel_pickup_list_name").val(selected_text);
        if(selected_value =='notListed'){         
          $("#hotelPickup_name").removeClass('hide');
        }else{         
          $("#hotelPickup_name").addClass('hide');
        }

    });
	});
</script>
<?php
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/sightseeing_booking.js'), 'defer' => 'defer');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/booking_script.js'), 'defer' => 'defer');?>

<script type="text/javascript">
	$(document).ready(function () {
		$("input[name='selected_pm']").click(function(){
    		//shoConFees();\
    		var selected_radio = $("input[name='selected_pm']:checked");
			var selected_pm = selected_radio.val();
			var bank_code = $("#bank_code").val();

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
    	});
	})

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
</script>
