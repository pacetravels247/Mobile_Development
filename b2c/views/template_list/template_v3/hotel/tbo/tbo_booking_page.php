<?php
$module_value = md5('hotel');
$currency_symbol = $this->currency->get_currency_symbol($pre_booking_params['default_currency']);

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
$hotel_total_price = roundoff_number($hotel_total_price-$pre_booking_params['markup_price_summary']['_GST']);
/********************************* Convenience Fees *********************************/
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('provablib.js'), 'defer' => 'defer');

$book_login_auth_loading_image	 = '<div class="text-center loader-image"><img src="'.$GLOBALS['CI']->template->template_images('loader_v3.gif').'" alt="please wait"/></div>';
//debug($pre_booking_params);
$LastCancellationDate = $pre_booking_params['LastCancellationDate'];
$RoomTypeName = $pre_booking_params['RoomTypeName'];
$Boardingdetails = $pre_booking_params['Boarding_details'];

$RateComments = @$pre_booking_params['RateComments'];
//calculating price
$token = $pre_booking_params['price_token'];
$tax_total = 0;
$grand_total = 0;
foreach($token as $token_k => $token_v) {
	// debug($token_v);
	$temp_price_details = $GLOBALS['CI']->hotel_lib->update_room_markup_currency($token_v, $currency_obj, $search_data['search_id'], false, true, 'b2c');
	// debug($temp_price_details);exit;
	$RoomPrice = $temp_price_details['RoomPrice'];
	$room_tax = $GLOBALS['CI']->hotel_lib->tax_service_sum($temp_price_details, $token_v);
	//debug($room_tax);exit;
	//$grand_total += $RoomPrice+$room_tax;
	//echo $RoomPrice.'<br/>';
	$grand_total += $RoomPrice;
	//$tax_total += $room_tax;
}
// debug($temp_price_details);exit;
$tax_total += $pre_booking_params['convenience_fees'];
// $grand_total += $pre_booking_params['convenience_fees'];
$grand_total = $hotel_total_price+$tax_total;
// echo $tax_total;exit;

$grand_total = ceil($grand_total);
#echo $grand_total.'<br/>';
$hotel_total_price = ceil($hotel_total_price);
//calculate total room price without tax

#echo $hotel_total_price;exit;
$total_room_price  = ceil($hotel_total_price);
//echo $total_room_price;exit;
$total_pax = array_sum($search_data['adult_config'])+array_sum($search_data['child_config']);
$base_url=base_url().'index.php/hotel/image_details_cdn';
//check image exists or not in url
/*$file_header = @get_headers($pre_booking_params['HotelImage']);
$image_found=1;
if(!$file_header  || $file_header [0] =='HTTP/1.1 404 Not Found'){
	$image_found=0;

}*/

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
  <div class="ovrgo">
	<div class="bktab1 xlbox <?=$review_tab_details_class?>">
		<div class="col-xs-12 col-md-8 toprom nopad">
		  <div class="col-xs-12 nopad full_room_buk shadow-1">
			<div class="bookcol">
			  <div class="hotelistrowhtl">
				<div class="col-md-5 col-xs-5 nopad xcel">
				  <div class="imagehotel id-hotel-image">
				  		<?php if($pre_booking_params['HotelImage']!='/'):?>
				  		<?php
				  			$image = $base_url.'/'.base64_encode($pre_booking_params['HotelCode']).'/0';
				  			//$image = $pre_booking_params['HotelImage'];
				  		?>
				  		<img alt="<?=$pre_booking_params['HotelName']?>" src="<?=$image?>">
				  	<?php else:?>
				  		<img alt="Hotel_img" src="<?=$GLOBALS['CI']->template->template_images('default_hotel_img.jpg')?>" class="lazy h-img">
				  	<?php endif;?>
				  </div>
				</div>
				<div class="col-md-12 col-xs-12 padall10 xcel">
				  <div class="hotelhed"><?=$pre_booking_params['HotelName']?></div>
				  <div class="clearfix"></div>
				  <div class="bokratinghotl rating-no">				   
					  <?=print_star_rating($pre_booking_params['StarRating'])?>
				  </div>
				  <div class="clearfix"></div>
				  <div class="mensionspl"> <?=$pre_booking_params['HotelAddress']?> </div>
				   <hr class="m-1">
				    <div class="bokkpricesml">
						<div class="travlrs">Travelers: <span class="fa fa-male"></span> <?=array_sum($search_data['adult_config'])?>  <span class="fa fa-child"></span> <?=array_sum($search_data['child_config'])?></div>
						<div class="totlbkamnt hide"> Room Amount <!-- <?=$this->currency->get_currency_symbol($pre_booking_params['default_currency'])?> -->&#8377; <?=($pre_booking_params['markup_price_summary']['RoomPrice'])?>/-</div>
						<div class="totlbkamnt hide"> Tax <!-- <?=$this->currency->get_currency_symbol($pre_booking_params['default_currency'])?> -->&#8377; <?=(($convenience_fees+$tax_service_sum))?>/-</div>
						<div class="totlbkamnt hide"> Total Amount <!-- <?=$this->currency->get_currency_symbol($pre_booking_params['default_currency'])?> -->&#8377; <?=($hotel_total_price)?>/-</div>
					</div>

					  <div class="row id-check-ins">
						<div class="col-sm-4 nopad">
							<span class="id-check-in">Check-in Date</span><br><span class="id-check-in-date">30 Jul, 2020</span>
						</div>
						<div class="col-sm-4 nopad">
							<span class="id-check-in">Check-out Date</span><br><span class="id-check-in-date">31 Jul, 2020</span>
						</div>
						<div class="col-sm-2 nopad">
							<span class="id-check-in">Nights</span><br><span class="id-check-in-date">1</span>
						</div>
						<div class="col-sm-2 nopad">
							<span class="id-check-in">Rooms</span><br><span class="id-check-in-date">1</span>
						</div>
					</div>

				<!--   <div class="sckint">
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
			</div> -->
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
					<div class="newloginsectn shadow-1">
						<div class="col-xs-7 celoty nopad">
							<div class="insidechs">
								<div class="mailenter">
									<input type="text" name="booking_user_name" id="booking_user_name"  placeholder="Your mail id" class="newslterinput nputbrd _guest_validate" maxlength="80">
									 <!-- <span id="name_error"><div class="formerror">Please enter your mail id</div></span> -->
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
										<div class="col-xs-3 nopadding">
												<!-- <input type="text" placeholder="+91" class="newslterinput nputbrd"> -->
											<select name="" class="newslterinput nputbrd _numeric_only" id="before_country_code"  required>
												<?php 
												//debug($phone_code);exit;
												echo diaplay_phonecode($phone_code,$active_data, $user_country_code); ?>
											</select> 
										</div>
										<div class="col-xs-1 nopadding"><div class="sidepo">-</div></div>
										<div class="col-xs-8 nopadding">
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
						<div class="col-xs-5 celoty nopad">
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
		<div class="col-md-4 col-xs-12 full_room_buk rhttbepa">
		   <div id="slidebarscr" style="width:370px;">
		 	<table class="table table-condensed tblemd shadow-1">
			 	<tbody>
			 	  <tr class="rmdtls">
			        <th colspan="2" class="text-center">Room Details</th>
			      </tr>
			      <tr>
			        <th>Room Type</th>
			        <td><?=$RoomTypeName?></td>
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
			        <td><?=$total_pax?></td>
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
				        <th>Free Cancellation till:<br/><a  href="#" data-target="#roomCancelModal"  data-toggle="modal" >View Cancellation Policy</a></th>
				        <td><?=local_month_date($LastCancellationDate)?></td>
				        
				      </tr>
				  <?php else:?>
				  		<tr class="frecanpy">
				        <th>Cancellation Policy:<br/><a  href="#" data-target="#roomCancelModal"  data-toggle="modal" >View Cancellation Policy</a></th>
				        <td>Non-Refundable</td>
				      </tr>
			 	 <?php endif;?>
			 
			      <tr>
			        <th>Total Price</th>
			        <td><?=$this->currency->get_currency_symbol($pre_booking_params['default_currency'])?> <?=($total_room_price)?></td>
			      </tr>
			      <tr class="texdiv">
			        <th>Taxes & Service fee</th>
			        <td><?=$this->currency->get_currency_symbol($pre_booking_params['default_currency'])?> <?=$tax_total?></td>
			      </tr>
			      <?php if($pre_booking_params['markup_price_summary']['_GST'] > 0){?>
			      <tr class="texdiv">
			        <th>GST</th>
			        <td><?=$this->currency->get_currency_symbol($pre_booking_params['default_currency'])?> <?=$pre_booking_params['markup_price_summary']['_GST'];?></td>
			      </tr>
			      <?php } ?>
				  <tr class="textdiv">
					<th>Convenience Fees</th>
					<td>
						<span>0</span>
					</td>
				</tr>
			      <tr class="grd_tol">
			        <th>Grand Total</th>
			        <td><?=$this->currency->get_currency_symbol($pre_booking_params['default_currency'])?> <?=($grand_total)?></td>
			      </tr>
			    </tbody>
			  </table>
			  <?php if(isset($pre_booking_params['sur_Charge_exclude'])){ ?>
			      
			       <div>Not included in price (collected by the property): <?php echo $pre_booking_params['surCharge_exclude_name']; ?> Rs. <?php echo $pre_booking_params['sur_Charge_exclude']; ?>
			      </div>
			      	<?php }?>
		   </div>
		  </div>
		<div class="clearfix"></div>
		
		<div class="clearfix"></div>
	
	</div>
	<div class="bktab2 xlbox <?=$travellers_tab_details_class?>">
	  <div class="col-xs-12 col-md-8 nopad">
		<div class="col-xs-12 topalldesc shadow-1">
			<div class="col-xs-12 nopad">
				<div class="bookcol">
			  <div class="hotelistrowhtl">
				<div class="col-md-5 nopad xcel">
				  <div class="imagehotel">
				  		<?php if($pre_booking_params['HotelImage']!='/'):?>

				  		<?php
				  			$image = $base_url.'/'.base64_encode($pre_booking_params['HotelCode']).'/0';
				  			//$image= $pre_booking_params['HotelImage'];
				  		?>

				  		<img alt="<?=$pre_booking_params['HotelName']?>" src="<?=$image?>">
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
				  <div class="bokkpricesml">
					<div class="travlrs"><span class="travlrsnms">Travelers:</span><span class="fa fa-male"></span> <?=array_sum($search_data['adult_config'])?><span class="fa fa-child"></span> <?=array_sum($search_data['child_config'])?></div>
					<div class="totlbkamnt grandtotal"> <span class="ttlamtdvot">Total Amount</span><?=$this->currency->get_currency_symbol($pre_booking_params['default_currency'])?> <?=($hotel_total_price)?>/-</div>
					</div>
				</div>
			  </div>
			</div>
			</div><!-- Outer Summary -->
		
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
		<form action="<?=base_url().'index.php/hotel/pre_booking/'.$search_data['search_id']?>" method="POST" autocomplete="off">
		<div class="hide">
			<?php $dynamic_params_url = serialized_data($pre_booking_params);?>
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

<?php
	$child_age = @$search_data['child_age'];
	$search_child_age = @$search_data['child_age'];
	if(is_logged_in_user()) {
		$traveller_class = ' user_traveller_details ';
	} else {
		$traveller_class = '';
	}
	$child_age_index=0;
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
			 	$child_age_index++;
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
			<?php
				if($search_child_age){
					if($child_age_index>0){
						$child_yr = '';
						if(isset($search_child_age[$child_age_index-1])){
							if($search_child_age[$child_age_index-1]>1){
								$child_yr = '<br/>'.' '.$search_child_age[$child_age_index-1].' years old';
							}else{
								$child_yr = '<br/>'.$search_child_age[$child_age_index-1].' year old';
							}
						}
					}
					
					
				}
			?>
		   <div class="adltnom"><?=(is_adult($pax_index, $total_adult_count) ? 'Adult' : 'Child' .$child_yr )?><?=(is_lead_pax($pax_index) ? '- Lead Pax' : '')?></div>
		
		 </div>
		 <div class="col-xs-10 nopadding">
		 <div class="inptalbox">
			<div class="col-xs-2 spllty">
			<div class="selectedwrap">
			<select class="mySelectBoxClass flyinputsnor name_title" name="name_title[]" required>
			<?php echo (is_adult($pax_index, $total_adult_count) ? $adult_title_options : $child_title_options)?>
			</select>
			</div>
			</div>
			<div class="col-xs-5 spllty">
				  <input value="<?=@$cur_pax_info['first_name']?>" required="required" type="text" name="first_name[]" id="passenger-first-name-<?=$pax_index?>" class="clainput alpha_space <?=$traveller_class?>"  minlength="2" maxlength="45" placeholder="Enter First Name" data-row-id="<?=($pax_index);?>"/>
				  <input type="hidden" class="hide"  maxlength="45" name="middle_name[]">
			</div>
			<div class="col-xs-5 spllty">
			 	<input value="<?=@$cur_pax_info['last_name']?>" required="required" type="text" name="last_name[]" id="passenger-last-name-<?=$pax_index?>" class="clainput alpha_space last_name" minlength="2" maxlength="45" placeholder="Enter Last Name" />
			 </div>
		</div>
		</div>
	</div>
<?php
}//END FOR LOOP FOR PAX DETAILS
?>
					</div>
				</div>
				<!---Added by ela-->
				<div>
					<?php 
					   if($RateComments){
					     echo "<div class='labltowr'>RateComments </div>";
					   	 foreach ($RateComments as $r_key => $r_value) {
					   	 	echo "<p>".$r_value."</p>";
					   	 }
					   }
					?>
				</div>
				<!--End -->
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
					<div class="col-xs-12 col-md-12 nopad">
					<div class="col-xs-2 nopadding">
					<select name="phone_country_code" class="newslterinput nputbrd _numeric_only " id="after_country_code" required>
											<?php echo diaplay_phonecode($phone_code,$active_data, $user_country_code); ?>
										</select> 
					</div>
					<div class="col-xs-1 nopadding"><div class="sidepo">-</div></div>
					<div class="col-xs-4 pl-0">
					<input value="<?=@$lead_pax_details['phone'] == 0 ? '' : @$lead_pax_details['phone'];?>" type="text" name="passenger_contact" id="passenger-contact" placeholder="Mobile Number" class="newslterinput nputbrd _numeric_only" maxlength="10" required="required">
					</div>
					<!-- <div class="clearfix"></div> -->
					<div class="emailperson col-xs-5 nopad">
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
		<div class="col-xs-4 rhttbepa">
			   <div id="nxtbarslider" style="width:370px;">
			 	<table class="table table-condensed tblemd shadow-1">
				 	<tbody>
				 	  <tr class="rmdtls">
				        <th colspan="2" class="text-center">Room Details</th>
				        <td></td>
				      </tr>
				      <tr>
				        <th>Room Type</th>
				        <td><?=$RoomTypeName?></td>
				      </tr>
				      <tr class="aminitdv">
			        <th>Board Type</th>
			        <td>
			        	
			        	<?php if($Boardingdetails):?>				         		
		         		<?php  
		         			$am_arr = array();
		         			foreach ($Boardingdetails as $b1_key => $b1_value) {
		         				$am_arr[]=$b1_value;
		         			}
		         			echo implode(",",$am_arr);			         	
		         		?>
			        	
		         	<?php else:?>
		         			<span>Room Only</span>
			        	<?php endif;?>
			        	

			        </td>
			      </tr>
				      <tr>
				        <th>No of Guest</th>
				        <?php
				         $total_pax = array_sum($search_data['adult_config'])+array_sum($search_data['child_config']);
				        ?>
				        <td><?=$total_pax?></td>
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
				        <td><?=$this->currency->get_currency_symbol($pre_booking_params['default_currency'])?> <?=$total_room_price?></td>
				      </tr>
				      <tr class="texdiv">
				        <th>Taxes & Service fee</th>
				        <td><?=$this->currency->get_currency_symbol($pre_booking_params['default_currency'])?> <?=$tax_total?></td>
				      </tr>
				      <?php 

			      	if($pre_booking_params['markup_price_summary']['_GST'] > 0){

			      	?>
			      <tr class="texdiv">
			        <th>GST</th>
			        <td><?=$this->currency->get_currency_symbol($pre_booking_params['default_currency'])?> <?=$pre_booking_params['markup_price_summary']['_GST'];?></td>
			      </tr>
			      <?php } ?>
				      <tr class="promo_code_discount hide">
				        <th>Promo Code Discount</th>
				        <td class="promo_discount_val"></td>
				      </tr>
					<tr class="textdiv">
					<th>Convenience Fees</th>
					<td>
						<span id="convenience_fees">0</span>
					</td>
				</tr>
					<tr class="grd_tol">
				        <th>Grand Total</th>
				        <td class="grandtotal"><?=$this->currency->get_currency_symbol($pre_booking_params['default_currency'])?> <span class="grand_total_amount"><?=$grand_total?></span>/-</td>
				      </tr>
				      				    </tbody>
				  </table>
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
				
				<p class="policy_text"><?php echo array_shift($pre_booking_params['CancellationPolicy']); ?></p>

				
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
        	/*bottom height*/
        	var scrollHeight = $(document).height();
			var scrollPosition = $(window).height() + $(window).scrollTop();
			var bottom = (scrollHeight - scrollPosition) / scrollHeight;
			
			//console.log("bottom"+bottom);
			// if ((scrollHeight - scrollPosition) / scrollHeight === 0) {
			//     // when scroll to bottom of the page
			// }
        	//alert(height);
        	//alert("bottomsssss"+bottom);
        	if((top >= 150) && (bottom >=0.25))  // || (height > 300))
        	{
        		//alert(bottom+top);
        		$("#slidebarscr").css({position: "fixed", top:0});  	
        	}else 
        	{
        		//alert('bottom '+top);
        		$("#slidebarscr").css({position: "",top:0});  	
        	} 
        	/*else if((top >= 285) && (top < 300))
        	{
        		$("#slidebarscr").css({position: "fixed", top:0});  	
        	}*/
        	                     
        }
    });  
});
</script>
<script type="text/javascript">
	$(document).ready(function(){
    $(document).on('scroll', function(){
        if ($('#nxtbarslider')[0].offsetTop < $(document).scrollTop()){
        	var top = $(document).scrollTop();
        	var height = $(window).height();
        	var scrollHeight = $(document).height();
			var scrollPosition = $(window).height() + $(window).scrollTop();
			var bottom = (scrollHeight - scrollPosition) / scrollHeight;
        	//alert(top);
        	if(((top >= 243) || (height < 300)) && (bottom >=0.2))
        	{
        		//alert(top);
        		$("#nxtbarslider").css({position: "fixed", top:0});  	
        	}else  
        	{
        		$("#nxtbarslider").css({position: "", top:0});  	
        	}         
            
        }
    });  
});
</script>

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
<?php
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/hotel_booking.js'), 'defer' => 'defer');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/booking_script.js'), 'defer' => 'defer');?>


<script type="text/javascript">
$(document).ready(function () {
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
					//$(".grand_total_amount").text(new_grand_total);
					$("#convenience_fees").text(con_fees);
				}
			});
		}
	}
});
</script>

<style type="text/css">
	.id-hotel-image {
		box-shadow: 2px 2px 5px #ccc;
    	border: 5px solid #fff;
	}
	.id-check-ins{
		margin-top: 10px;
	}
	.id-check-ins .id-check-in{
		font-weight: bold;
		font-size: 15px;
	}
	.id-check-ins .id-check-in-date{
		color: #848484;
		font-size: 13px;
	}
	.m-1{
		margin: 10px!important;
	}
	.grd_tol th {
		font-size: 20px !important;
	}
	.tblemd th {
		padding: 8px!important;
	}
	.hotelistrowhtl .imagehotel{
		max-height: 260px;
		height: 260px;
	}
	.imagehotel img {
		max-height: 260px;
		height: 260px;
	}
	.shadow-1{
		box-shadow: 0px 0px 6px 0px #ccc;
	}
	.pl-0{
		padding-left: 0;
	}
	.ttlamtdvot{
		font-size: 18px;
	}
</style>