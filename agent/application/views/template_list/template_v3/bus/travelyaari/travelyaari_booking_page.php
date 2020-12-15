<?php
//debug($agent_data);exit;
$template_images = $GLOBALS ['CI']->template->template_images ();
$CUR_Route = ($details ['Route']);
$seat_count = count($pre_booking_params['seat']);
// debug($CUR_Route);exit;
$bus_seats = $details ['Layout'] ['SeatDetails'] ['clsSeat'];
$bus_pickup = $details ['Pickup'] ['clsPickup'];
$bus_drop = $details ['Drop'] ['clsDrop'];
$CUR_CancellationCharges = $details ['CancellationCharges'] ['clsCancellationCharge'];

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
$_AgentBuying = $details['Fare']['_AgentBuying'];
echo generate_low_balance_popup($_AgentBuying);

//echo $details['Fare']['_ServiceTax'];
//debug($details);exit;
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
<div class="col-xs-12">
		<div class="ovrgo container">
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
										<div class="farestybig">Base Fare</div>
									</div>
									<div class="col-xs-4 nopadding">
										<div class="text-right arimobold grandtotal"><?=$default_currency_symbol?> <?=number_format(roundoff_number($details['Fare']['_CustomerBuying']-($details['Fare']['_GST'] + $details['Fare']['_ServiceTax'])))?> </div>
									</div>
								</div>
								<div class="clearfix"></div>
								<?php if($details['Fare']['_ServiceTax'] > 0){?>
								<div class="reptalltftr">
									<div class="col-xs-8 nopadding">
										<div class="farestybig">Service Tax</div>
									</div>
									<div class="col-xs-4 nopadding">
										<div class="text-right arimobold grandtotal"><?=$default_currency_symbol?> <?=number_format(roundoff_number($details['Fare']['_ServiceTax']))?> </div>
									</div>
								</div>
								<?php } ?>
								<div class="clearfix"></div>
								<?php if($details['Fare']['_GST'] > 0){?>
								<div class="reptalltftr">
									<div class="col-xs-8 nopadding">
										<div class="farestybig">GST</div>
									</div>
									<div class="col-xs-4 nopadding">
										<div class="text-right arimobold grandtotal"><?=$default_currency_symbol?> <?=number_format(roundoff_number($details['Fare']['_GST']))?> </div>
									</div>
								</div>
								<?php } ?>
								<div class="reptalltftr">
									<div class="col-xs-8 nopadding">
										<div class="farestybig id-font-bold">Grand Total</div>
									</div>
									<div class="col-xs-4 nopadding">
										<div class="amnterbig arimobold id-font-bold">
										<?=$default_currency_symbol?> 
										<span id='sp_t' class="grand_total_amount"><?=number_format(roundoff_number($details['Fare']['_CustomerBuying']))?></span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- Fare Summary  Ends-->
				<div class="col-xs-8 nopadding full_summery_tab">
					<div class="fligthsdets only_bus_book id-margin-b-10">
						<div class="flitab1">
<?php //debug($CUR_Route);exit;?>
							<div class="moreflt boksectn">
							  <div class="ontyp">
								<div class="labltowr arimobold"><?=ucfirst($CUR_Route['From'])?> to <?=ucfirst($CUR_Route['To'])?><strong> ( <?=get_time_duration_label(calculate_duration($CUR_Route['DepartureTime'], $CUR_Route['ArrivalTime']))?>)</strong></div>
								<div class="allboxflt">
								  <div class="col-xs-4 nopadding full_fiftys">
									<div class="alldiscrpo"><?=$CUR_Route['CompanyName']?>
									<div class="sgsmalbus col-xs-12 col-md-12 nopad"><strong><b>Pickup : </b></strong>
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
									<div class="sgsmalbus col-xs-12 col-md-12 nopad"><strong>Drop : </strong>
									<div class="droplokndt">

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
								  </div>
								  <div class="col-xs-6 nopadding qurter_wdth">
									<div class="col-xs-5">
										<span class="airlblxl id-font-bold"><?=($CUR_Route['DepartureTime'])." ".get_time($CUR_Route['DeptTime'])?></span>
										<span class="portnme"><?=ucfirst($CUR_Route['From'])?></span>	
									</div>
									<div class="col-xs-2">
										<span class="fadr fa fa-long-arrow-right textcntr"></span>
									</div>
									<div class="col-xs-5">
										<span class="airlblxl id-font-bold"><?=($CUR_Route['ArrivalTime'])." ".get_time($CUR_Route['ArrTime'])?></span>
										<span class="portnme"><?=ucfirst($CUR_Route['To'])?></span></div>
									</div>
									<div class="col-xs-2 nopadding smal_width_hr"> 
									<span class="portnme textcntr id-font-bold"><?=get_time_duration_label(calculate_duration($CUR_Route['DepartureTime'], $CUR_Route['ArrivalTime']))?></span> 
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
										<div class="col-xs-5 celoty nopad">
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
															<div class="col-xs-3 nopadding">
																<!--<input type="text" placeholder="+91" class="newslterinput nputbrd" readonly>-->
																<!-- //FIXME: insert the country code to DB -->
																<select class="newslterinput nputbrd _numeric_only " >
																<?php echo diaplay_phonecode($phone_code,$agent_data,''); ?>
															</select> 
															</div>
															<div class="col-xs-1 nopadding">
																<div class="sidepo">-</div>
															</div>
															<div class="col-xs-8 nopadding">
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
								<a class="freshd show_details btn btn-sm pull-left" id="hide_show_net_fare" data-toggle="collapse" href="#net_fare_details" aria-expanded="false" aria-controls="net_fare_details">
	                                +SNF
	                        	</a>
                        	</div>


                            <!--Net Fare-->
                        	<div class="collapse" id="net_fare_details" aria-expanded="false" style="height: 0px;">
                                <div class="kindrest">
                                	<div class="freshd">Fare Details</div>
                                	<div class="reptallt">
                                	<div class="col-xs-8 nopadding">
                                	<div class="faresty">Total Pub. Fare</div>
                                	</div>
                                	<div class="col-xs-4 nopadding">
                                	<div class="amnter arimobold"><?=number_format(roundoff_number($details['Fare']['_CustomerBuying']))?></div>
                                	</div>
                                	</div>
                                	<div class="reptallt">
                                	<div class="col-xs-8 nopadding">
                                	<div class="faresty">Markup</div>
                                	</div>
                                	<div class="col-xs-4 nopadding">
                                	<div class="amnter arimobold">- <?=number_format($details['Fare']['_AgentMarkup'])?></div>
                                	</div>
                                	</div>
                                	<div class="reptallt">
                                	<div class="col-xs-8 nopadding">
                                	<div class="faresty">Comm. Earned</div>
                                	</div>
                                	<div class="col-xs-4 nopadding">
                                	<div class="amnter arimobold">-<?=number_format($details['Fare']['_Commission'])?></div>
                                	</div>
                                	</div>
	                                <div class="reptallt">
	                                <div class="col-xs-8 nopadding">
	                                <div class="faresty">TdsOnCommission</div>
	                                </div>
	                                <div class="col-xs-4 nopadding">
	                                <div class="amnter arimobold">+<?=number_format($details['Fare']['_tdsCommission'])?></div>
	                                </div>
	                                </div>
	                                <div class="reptallt">
	                                <div class="col-xs-8 nopadding">
	                                <div class="faresty">GST</div>
	                                </div>
	                                <div class="col-xs-4 nopadding">
	                                <div class="amnter arimobold">+<?=number_format($details['Fare']['_GST'])?>
	                                </div>
	                                </div>
	                                </div>
	                                <div class="reptallt_commisn">
	                                <div class="col-xs-6 nopadding">
	                                <div class="farestybig">Total Payable</div></div>
	                                <div class="col-xs-6 nopadding"><div class="amnterbig">Rs <span id="agent_payable_amount1"><?=number_format($details['Fare']['_AgentBuying'])?></span></div></div></div>
	                                <div class="reptallt_commisn">
		                                <div class="col-xs-8 nopadding">
		                                	<div class="farestybig">Total Earned</div>
		                                </div>
	                                	<div class="col-xs-4 nopadding"><div class="amnterbig ">Rs <?=number_format($details['Fare']['_AgentEarning'])?> </div>
	                                	</div>
	                                </div>
                                </div>
                            </div>

                                <!--Net Fare End-->
                                <!--Published Fare-->
                                <div id="published_fare_details">
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
											<div class="farestybig">Base Fare</div>
										</div>
										<div class="col-xs-4 nopadding">
											<div class="text-right arimobold grandtotal"><?=$default_currency_symbol?> <?=number_format(roundoff_number($details['Fare']['_CustomerBuying']-($details['Fare']['_GST'] + $details['Fare']['_ServiceTax'])))?> </div>
										</div>
									</div>
									<div class="clearfix"></div>
									<?php if($details['Fare']['_ServiceTax'] > 0){?>
									<div class="reptalltftr">
										<div class="col-xs-8 nopadding">
											<div class="farestybig">Service Tax</div>
										</div>
										<div class="col-xs-4 nopadding">
											<div class="text-right arimobold grandtotal"><?=$default_currency_symbol?> <?=number_format(roundoff_number($details['Fare']['_ServiceTax']))?> </div>
										</div>
									</div>
									<?php } ?>
									<div class="clearfix"></div>
									<?php if($details['Fare']['_GST'] > 0){?>
									<div class="reptalltftr">
										<div class="col-xs-8 nopadding">
											<div class="farestybig">GST</div>
										</div>
										<div class="col-xs-4 nopadding">
											<div class="text-right arimobold grandtotal"><?=$default_currency_symbol?> <span id="markup_gst"><?=roundoff_number($details['Fare']['_GST'])?> </span>
											<input type="hidden" id="markup_gst_copy" value="<?=roundoff_number($details['Fare']['_GST'])?>">
											</div>
										</div>
									</div>
									<?php } ?>
									<div class="reptalltftr">
										 <a id="markup_show_hide">Show / Hide Markup</a>
									</div>
									<div class="reptalltftr" style="display: none;" id="add_custom_markup">
										<div class="col-xs-8 nopadding">
											<div class="farestybig">Markup</div>
										</div>
										<div class="col-xs-4 nopadding">
											<div class="amnterbig arimobold">
											<input type="text" id="markup" name="markup" 
											style="width:100px; height: 25px;" >
											</div>
										</div>
										<div class="col-xs-12 nopadding er_msg" style="display: none;">
											<input type="hidden" name="markup_limit" id="markup_limit" value="<?php echo $markup_limits[0]['value']; ?>">
											<p style="color:red;">System accepts markup values under Rs.<?php echo $markup_limits[0]['value']; ?></p>
										</div>
									</div>
									<div class="reptalltftr">
										<div class="col-xs-8 nopadding">
											<div class="farestybig">Convenience Fees</div>
										</div>
										<div class="col-xs-4 nopadding">
											<div class="text-right arimobold">
											<span id="convenience_fees">0</span>
											</div>
										</div>
									</div>	
									<div class="reptalltftr">
										<div class="col-xs-8 nopadding">
											<div class="farestybig id-font-bold">Grand Total</div>
										</div>
										<div class="amnterbig arimobold id-font-bold">
											<?=$default_currency_symbol?> 
											<span class="grand_total_amount"><?=number_format(roundoff_number($details['Fare']['_CustomerBuying']))?></span>
											<input id="grand_total_amount_copy" type="hidden" 
											value="<?php echo $details['Fare']['_CustomerBuying']; ?>">
											</div>
									</div>
            					</div>
            					<!--Published Fare End-->
							</div>
						</div>
					</div>
					<!-- Fare Summary  Ends-->
					<div class="col-xs-8 nopadding full_summery_tab">
						<div class="fligthsdets only_bus_book id-margin-b-10">
			<form action="<?=base_url().'index.php/bus/pre_booking/'.$search_data['search_id']?>" method="POST" autocomplete="off" id="pre-booking-form">
			 <div class="flitab1">
				<div class="moreflt boksectn">
					<div class="ontyp">
					  <div class="labltowr arimobold"><?=$CUR_Route['CompanyName']?> | <?=ucfirst($CUR_Route['From'])?> to <?=ucfirst($CUR_Route['To'])?><strong> ( <?=get_time_duration_label(calculate_duration($CUR_Route['DepartureTime'], $CUR_Route['ArrivalTime']))?>)</strong></div>
								<div class="allboxflt">
								<div class="row" style="margin-bottom:15px;">
								<div class="col-xs-7 nopadding qurter_wdth">
									<div class="col-xs-5">
										<span class="airlblxl id-font-bold"><?=local_date($CUR_Route['DepartureTime'])." ".get_time($CUR_Route['DeptTime'])?></span>
										<span class="portnme"><?=ucfirst($CUR_Route['From'])?></span>	
									</div>
									<div class="col-xs-2">
										<span class="fadr fa fa-long-arrow-right textcntr"></span>
									</div>
									<div class="col-xs-5">
										<span class="airlblxl id-font-bold"><?=local_date($CUR_Route['ArrivalTime'])." ".get_time($CUR_Route['ArrTime'])?></span>
										<span class="portnme"><?=ucfirst($CUR_Route['To'])?></span></div>
								</div>
								<div class="col-xs-2 nopadding smal_width_hr"> 
									<span class="portnme textcntr id-font-bold"><?=get_time_duration_label(calculate_duration($CUR_Route['DepartureTime'], $CUR_Route['ArrivalTime']))?></span> 
									<span data-stop-number="0" class="portnme textcntr">Seat(<?=$seat_count?>) : <?=implode(',', $pre_booking_params['seat'])?></span> 
								</div>
								</div>
								<div class="row">
								<div class="col-xs-6">
									<strong><b>Pickup :&nbsp;</b></strong>
									<div class="pikuplokndt">
										<?php
										$pickup = $bus_pickup[$pre_booking_params['pickup_id']]; 
										echo $pickup['PickupName'];
										$pickup_string = ($pickup['PickupName'].' - '.get_time($pickup['PickupTime']));
										$pickup_string .= ', Address : '.$pickup['Address'].', Landmark : '.$pickup['Landmark'].', Phone : '.$pickup['Contact'];
										?>
										<span class="pikuptm">
										<?php echo get_time($pickup['PickupTime']);	?>
										</span>
									</div>
								</div>	
								<div class="col-xs-6">
									<strong><b>Drop :&nbsp; </b></strong>
									<div class="droplokndt">
										<?php
										$drop = $bus_drop[$pre_booking_params['drop_id']]; 
									
										echo $drop['DropoffName'];
										$drop_string = ($drop['DropoffName'].' - '.get_time($drop['DropoffTime']));
										
										?>
										<span class="pikuptm">
										<?php echo get_time($drop['DropoffTime']);	?>
										</span>
									</div>
								</div>	
								</div>
								</div>
						<div class="labltowr arimobold">Please enter Passenger details. </div>
						<div class="clikdiv float-right">
							<div class="squaredThree">
							<input id="copy_details" type="checkbox" name="" value="1">
							<label for="copy_details"></label>
							</div>
							<span class="clikagre" id="clikagre">
								Copy Details
							</span>
						</div>
						<!-- template_v1 code -->
						<div class="pasngrinput_enter">
								<div class="col-xs-2 nopad">
									<span class="labl_pasnger">Seat Details</span>
								</div>
								<div class="col-xs-10 nopad">
								<div class="col-xs-3 nopad">
									<span class="labl_pasnger">
										Gender <sup class="text-danger">*</sup>
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
							<?php 
							//debug($pre_booking_params);exit;
							$i = 0;
							$datepicker_list = array();
							$lead_pax_details = @$pax_details[0];
							if(is_logged_in_user()) {
								$traveller_class = ' user_traveller_details ';
							} else {
								$traveller_class = '';
							}
							for($i=0; $i<$seat_count; $i++) {
								
							?>
							<div class="pasngrinput_secnrews _passenger_hiiden_inputs">
							<div class="hide hidden_pax_details">
								<input type="hidden" name="gender[]" value="1" class="pax_gender">
							</div>
								<div class="col-xs-2 nopad">
								<div class="pad_psger">
									
									<span class="seat_number"> Seat <strong><?=$pre_booking_params['seat'][$i]?></strong></span>
									</div>
								</div>
										<div class="col-xs-10 nopad flling_name">
										<div class="col-xs-3 nopad">
										<div class="pad_psger">
											<!-- <div class="selectedwrap">
												<select class="name_title flyinputsnor " required name="pax_title[]">
												<?=$pax_title_options?>
												</select>
											</div> -->
											<div>
												<input class="name_title" type="radio" name="pax_title[<?=($i);?>]" value="1" id="pax_title-<?=($i);?>-1" checked> Male</br>
												<input class="name_title" type="radio" name="pax_title[<?=($i);?>]" value="2" id="pax_title-<?=($i);?>-2"> Female
											</div>
										</div>
										</div>
										<div class="col-xs-5 nopad">
										<div class="pad_psger">
												<input value="<?=@$cur_pax_info['first_name']?>" type="text" maxlength="45" id="contact-name-<?=($i);?>"  name="contact_name[]" class="clainput  alpha_space <?=$traveller_class?> contact-name" required placeholder="Name"  data-row-id="<?=($i);?>">
										</div>
										</div>
										<div class="col-xs-4 nopad">
										<div class="pad_psger">
											<!-- <div class="selectedwrap">
												<select class="age flyinputsnor" name="age[]" id="age-<?=($i);?>" required>
												<option value="INVALIDIP">Age</option>
												<?php echo generate_options(numeric_dropdown(array('size' => 99))); ?>
												</select>
											</div> -->
											<input type="text" maxlength="2" id="age-<?=($i);?>"  name="age[]" class="clainput age" placeholder="Age" onkeypress="return isNumber_1(event);" required>
										</div>
										</div>
										</div>
							</div>
							<?php
							
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
							$dynamic_params_url['from_id'] = @$pre_booking_params['token']['Route']['from_id'];
							$dynamic_params_url['to_id'] = @$pre_booking_params['token']['Route']['to_id'];
							$dynamic_params_url['PickUpID'] = $pre_booking_params['pickup_id'];
							$dynamic_params_url['DropID'] = $pre_booking_params['drop_id'];
							$dynamic_params_url['seat_attr']['seats'] = $details['seat_attr'];
							$dynamic_params_url['fare'] = $details['Fare'];
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
							$dynamic_params_url['CommPCT'] = 0;
							$dynamic_params_url['CommAmount'] = $CUR_Route['CommAmount'];
							$dynamic_params_url['CancPolicy'] = base64_encode(json_encode($pre_booking_params['token']['result']['Canc']));
							if(empty($pre_booking_params['token']['result']['Canc']))
							{
								$dynamic_params_url['CancPolicy'] = base64_encode(json_encode($pre_booking_params['token']['Route']['CancPolicy']));
							}
							//debug($dynamic_params_url); exit;
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
				<div class="clearfix"></div>
				<div class="contbk">
					<div class="contcthdngs">Contact Details</div>
					<div class="col-xs-12 nopad full_smal_forty">
					<div class="col-xs-3 nopadding">
					<!-- <input type="text" placeholder="+91" class="newslterinput nputbrd" readonly> -->
					<select class="newslterinput nputbrd _numeric_only " >
						<?php
					
						 echo diaplay_phonecode($phone_code,$agent_data,''); ?>
					</select> 
					</div>
					<div class="col-xs-1"><div class="sidepo">-</div></div>
					<div class="col-xs-8 nopadding">
					<input value="" type="text" name="passenger_contact" id="passenger-contact" placeholder="Mobile Number" class="newslterinput nputbrd _numeric_only numeric" maxlength="10" required="required">
					<input type="hidden" name="alternate_contact" value="">
					</div>
					<div class="clearfix"></div>
					<div class="emailperson col-xs-12 nopad full_smal_forty">
					<input value="<?=@provab_decrypt($agent_data['email'])?>" type="text" maxlength="80" required="required" id="billing-email" class="newslterinput nputbrd" placeholder="Email" name="billing_email">
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
						<div class="row">
<div class="contbk">
<div class="contcthdngs">Select Payment Mode</div>
<input type="radio" name="selected_pm" value="WALLET" checked/> Wallet
<input type="radio" name="selected_pm" value="PAYTM_CC" /> Credit Card
<input type="radio" name="selected_pm" value="PAYTM_DC" /> Debit Card
<input type="radio" name="selected_pm" value="PAYTM_PPI" /> PAYTM Wallet
<input type="radio" name="selected_pm" value="TECHP" /> Net Banking
</div>
<input type="hidden" name="markup" id="markup_copy" style="width:100px" value="0">
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
			<?php
			if($pre_booking_params['booking_source'] == ETS_BUS_BOOKING_SOURCE){
			?>
				<input type="hidden" name="inventory_type" value="<?=$details['Route']['inventoryType']?>">
			<?php } ?>
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
								if(valid_array(@$traveller_details)) {
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
				
			if($active_data['country_code']==$code['origin']){
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
<script>
$(document).ready(function () {
	$("#markup_show_hide").click(function(){
        $("#add_custom_markup").toggle();
    });
    $('#markup').keyup(function(){
    	var markup_val = parseFloat($('#markup').val());
    	var markup_limits = $('#markup_limit').val();
		
    var grand_total = $('#grand_total_amount_copy').val();
    var markup_gst = $("#markup_gst_copy").val();
    
    if( markup_val > 0 && (parseFloat(markup_val) <= parseFloat(markup_limits))){
        $('#markup_copy').val(markup_val);
        var extra_markup_gst = 0; //(parseFloat(markup_val)/100)*10;
        var new_markup_gst = parseFloat(parseFloat(markup_gst)+parseFloat(extra_markup_gst)).toFixed(2);
        var new_grand_total = parseFloat(parseFloat(grand_total)+parseFloat(markup_val)+parseFloat(extra_markup_gst)).toFixed(2);
        $("#markup_gst").text(new_markup_gst);
        $('.grand_total_amount').text(new_grand_total);
        $('.er_msg').css('display','none');
    }
    else{
		$('#markup_copy').val("0");
    	$('.er_msg').css('display','block');
    	$('#markup').val('');
    	$("#markup_gst").text(markup_gst);
        $('.grand_total_amount').text(grand_total);
    }
	shoConFees();
});
    $("input[name='selected_pm']").click(function(){
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
		var amount = $("#grand_total_amount_copy").val();
		var markup_val = $("#markup_copy").val();
		amount = parseFloat(amount)+parseFloat(markup_val);
		var selected_radio = $("input[name='selected_pm']:checked");
		var selected_pm = selected_radio.val();
		var bank_code = $("#bank_code").val();
		$(".con_fees_section").remove();
		if((amount > 0) && (selected_pm!=""))
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
					var grand_total = parseFloat(data["total"]).toFixed(2);
					if(isNaN(con_fees)){
						con_fees = 0;
						grand_total = amount;
					}
					$("#convenience_fees").text(con_fees);
					$(".grand_total_amount").text(grand_total);
				}
			});
		}
	}
});


function isNumber_1(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}
$('#hide_show_net_fare').click(function () {
if ($(this).hasClass('show_details') == true) {
    $(this).removeClass('show_details').addClass('hide_details');
    $(this).empty().html('-HNF');
    $('#published_fare_details').hide();
} else if ($(this).hasClass('hide_details') == true) {
    $(this).removeClass('hide_details').addClass('show_details');
    $(this).empty().html('+SNF');
    $('#published_fare_details').show();
}
});
/*function copy_details(){
	
}*/
$("#copy_details").change(function(){
  	if($(this).prop("checked") == true){

        var name = $('#contact-name-0').val();
        var age = $('#age-0').val();
        var gender = $('input[name="pax_title[0]"]:checked').val();

        $('.pasngrinput_secnrews').each(function(k,msg){
			if(k != 0){
				$('.pasngrinput_secnrews #contact-name-'+[k]+'').val(name);
				$('.pasngrinput_secnrews #age-'+[k]+'').val(age);
				$(this).find('.name_title').removeAttr("checked");
				$('.pasngrinput_secnrews #pax_title-'+[k]+'-'+gender).click();
			}
        });     
    }else{
    	$('.pasngrinput_secnrews').each(function(k,msg){
    		if(k != 0){
    			$('.pasngrinput_secnrews #contact-name-'+[k]+'').val('');
        		$('.pasngrinput_secnrews #age-'+[k]+'').val('');
        		$(this).find('.name_title').removeAttr("checked");
    		}
        }); 
    }
});
</script>
<style type="text/css">
	.labltowr, .contcthdngs{ /*double code*/
        color: #3a8bbb;
        font-size: 17px;
    }
	.labltowr strong{ /*double code*/
        color: #aaa;
    }
    .farestybig {
    	text-transform: none;
    }
    .id-font-bold{ /*double code*/
    	font-weight: bold;
    }
    .farehd{ /*double code*/
    	color: #3a8bbb;
        font-size: 17px;
        margin-bottom: 10px;
    }
    body{ /*double code*/
        margin-top: 0;
    }
	.amnterbig {
		font-size: 16px;
	}
	.seat_number{
		text-align: left;
	}
	.temsandcndtn {
		padding-left: 0;
	}
	.id-margin-b-10{
		margin-bottom: 10% !important;
	}
</style>
