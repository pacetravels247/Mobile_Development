<?php
Js_Loader::$css[] = array('href' => $GLOBALS['CI']->template->template_css_dir('owl.carousel.min.css'), 'media' => 'screen');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('owl.carousel.min.js'), 'defer' => 'defer');
?>
<link  href="<?php echo $GLOBALS['CI']->template->template_css_dir('page_resource/jquery.scrolling-tabs.css') ?>" rel="stylesheet">
<link  href="<?php echo $GLOBALS['CI']->template->template_css_dir('custom_tour.css') ?>" rel="stylesheet">
<link  href="<?php echo $GLOBALS['CI']->template->template_css_dir('custom_sky.css') ?>" rel="stylesheet">
<script src="<?php echo $GLOBALS['CI']->template->template_js_dir('initialize-carousel-detailspage.js') ?>" type="text/javascript"></script> 
<script src="<?php echo $GLOBALS['CI']->template->template_js_dir('jquery.provabpopup.js') ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CI']->template->template_js_dir('jquery.carouFredSel-6.2.1-packed.js') ?>" type="text/javascript"></script>

<?php
$image_array=array();
$image_data=array();
$inclusions_checks = json_decode($package_details[0]['inclusions_checks']);
foreach($tours_itinerary_dw as $ite_image){
	$photos=explode(',',$ite_image['banner_image']);
	$photos=array_reverse($photos);
	foreach($photos as $val_pho){
		if($val_pho!=''){
			array_push($image_array,$val_pho);
			array_push($image_data,$ite_image['visited_city_name']);
		}
	}
	
}

foreach ($b2c_tour_price as $tour_price_fly) {
	$per_person_price[$tour_price_fly['occupancy']]=$tour_price_fly['netprice_price'];
	$per_person_market_price[$tour_price_fly['occupancy']]=$tour_price_fly['market_price'];
} 
 
?>
<div class="full witcontent  marintopcnt">
<?php echo $this->session->flashdata("msg"); ?>
<div class="container mt-2 m-mt-0">
    <div class="row mt-1">
        <div class="col-md-9 mt-2">
            <div class="row">
                <div class="col-sm-12 nopad shadow-1 radius-10">
                    <div class="row">
                     
                        <div id="owl-demobaner" class="owl-carousel indexbaner id-image-slider-div">
                            
                            <?php 
							if(!empty($package_details)){
								foreach($image_array as $pic_key => $pic){ 
							?>
								<div class="item">
									<img src="<?php echo $GLOBALS['CI']->template->domain_images($pic)?>" alt="<?=$image_data[$pic_key]?>"> 
									<div class="img-caption"><?=$image_data[$pic_key]?></div>
								</div>
								  
							<?php 
								}
							} 
							?>
                          
                        </div>
                    </div>
                    <div class="row id-pack-div1">
                        
                        <div class="row m-mb-2">
                            <div class="col-sm-9 col-xs-7 nopad">
                                <h3 class="mt-2"><?=$package_details[0]['package_name']?></h3>
                            </div>
                            <div class="col-sm-3 col-xs-5 nopad">
                                <p class="id-pac-label1">Tour Code: <strong><?=$package_details[0]['tour_code']?></strong></p>
                            </div>
                        </div>
                        <div class="col-sm-2 nopad col-xs-12">
                            <p class="p-inline-nights blue-n"><?=$package_details[0]['duration']?>  <br><span>Nights</span></p>
                            <p class="p-inline-nights orange-n"><?=$package_details[0]['duration']+1?>  <br><span>Days</span></p>
                            <div class="triangle-right"></div>
                        </div>
                        <div class="col-sm-10 nopad">
                            
                            <!-- <hr class="m-05"> -->
                            <div class="row id-label-div">
                                <?php 
								//debug($inclusions_checks);
								foreach($inclusions_checks as $inc_val){
									if($inc_val=='Hotel'){
										$icon="fa fa-bed";
									}else if($inc_val=='Car'){
										$icon="fa fa-car";
									}else if($inc_val=='Meals'){
										$icon="fas fa-utensils";
									}else if($inc_val=='Sightseeing'){
										$icon="fa fa-camera";
									}else if($inc_val=='Transfers'){
										$icon="fas fa-exchange-alt";
									}else if($inc_val=='Visa'){
										$icon="fa fa-book";
									}else if($inc_val=='Train'){
										$icon="fa fa-train";
									}else if($inc_val=='Cruise'){
										$icon="fa fa-ship";
									}else if($inc_val=='Insurance'){
										$icon="fa fa-shield";
									}else if($inc_val=='Flight'){
										$icon="fa fa-plane";
									}
									
								?>
									<label>
										<?=$inc_val?>
										<p class="id-pack-fa">
											<i class="<?=$icon?>" aria-hidden="true"></i>
										</p>
									</label>
								<?php
									}
								?>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 padfive mt-2">
            <div class="row shadow-1 id-fare-div radius-10">
                <h4>&#8377; &nbsp;Fare Details</h4>
                <div class="col-sm-12 radius-10 mt-1">
                    <div class="row">
						<?php  
							//debug($date_range);
				if($package_details[0]['package_type'] == "fit"){ 
					$from_range=explode(',',$package_details[0]['valid_frm']);
					$to_range=explode(',',$package_details[0]['valid_to']);
					$group_departure=array();
					$date1 =  date("Y-m-d");
					foreach($from_range as $d_key => $d_val){
						$start_date =$d_val;
						$end_date = $to_range[$d_key];
						
						while (strtotime($start_date) <= strtotime($end_date)) {
							$date2 = date('Y-m-d', strtotime($start_date));
							if ($date1 > $date2) {
								//echo "less than today".$date2."<br>";
							}else{
								//echo "more than today";
								$group_departure[]=date('j-n-Y', strtotime($start_date));
							}
								
							
							$start_date = date ("Y-m-d", strtotime("+1 days",strtotime($start_date)));
						}
					}
					if(!empty($group_departure)){
						
						$last_item=end($group_departure);
						$last_item=date('Y-n-j', strtotime($last_item));
						$first_item=date('Y-n-j', strtotime($group_departure[0]));
						$group_departure=implode(',',$group_departure); 
					}else{
						$last_item=date('Y-n-j');
						$first_item=date('Y-n-j');
						$group_departure=date('Y-n-j');
					}
					//debug($group_departure);
			?>
					<!--<input  id="datepicker_dat_group" type="text" class="form-control id-inputfield" value="dd/mm/yyyy" readonly>-->
					<input type="text" name="" id="datepicker_dat_group" class="input-date" placeholder="Select Departure Date" required></div>
			<?php 
				}else{
					$group_departure=array();
					$date1 =  date("Y-m-d");
					foreach($dep_dates as $dep_key => $dep_val){ 
						$date2 = date('Y-m-d', strtotime($dep_val['dep_date']));
						if ($date1 > $date2) {
								//echo "less than today".$date2."<br>";
						}else{
								//echo "more than today";
								$group_departure[]=date('j-n-Y', strtotime($dep_val['dep_date']));
						}
						//$group_departure[]=date('j-n-Y', strtotime($dep_val['dep_date']));
					}
					//debug($group_departure);
					if(!empty($group_departure)){
						$last_item=end($group_departure);
						$last_item=date('Y-n-j', strtotime($last_item));
						
						$first_item=date('Y-n-j', strtotime($group_departure[0]));
						$group_departure=implode(',',$group_departure); 
					}else{
						$last_item=date('Y-n-j');
						$first_item=date('Y-n-j');
						$group_departure=date('Y-n-j');
					}
			?>
				<!--<input   type="text" class="form-control id-inputfield" value="dd/mm/yyyy" readonly>-->
				<input type="text" name="" id="datepicker_dat_group" class="input-date" placeholder="Select Departure Date" required></div>
			<?php
				} 
			?>

                    <div class="row id-support">
                        <p class="p-inline-city"><strong>City: </strong> <?=$city?></p>
                        <p class="p-inline-city"><strong>Country: </strong> <?=$country?></p>
                        <p class="p-inline-city"><strong>Holiday Type: </strong>
						<?php 
						  $types=array();
						  foreach($tour_types as $type_val){
							 $types[]=$type_val['tour_type_name'];
						  } 
						  echo implode(',',$types);
						?></p></p>
                    </div>
                    <div class="row text-center">
                        <p><span><?php echo $currency_obj->get_currency_symbol($currency_obj->to_currency); ?>  <?php echo isset($per_person_price[10])?get_converted_currency_value ( $currency_obj->force_currency_conversion ( $per_person_price[10] ) ):0; ?>/- </span><br> Starting price per adult</p>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-2 col-xs-6 padfive">
                            <button class="btn btn-default book-btn" title="Download Itinerary"><a href="<?php echo base_url () . 'index.php/tours/b2c_voucher/'.$package_details[0]['id'].'/show_download_pf';?>" ><i class="fa fa-download" aria-hidden="true"></i></a></button>
                        </div>
                        <div class="col-sm-2 col-xs-6 padfive">
                            <button class="btn btn-default book-btn" title="Email Itinerary " data-toggle="collapse" data-target="#emailmodel" aria-expanded="true"><i class="fa fa-envelope" aria-hidden="true"></i></button>
                        </div>
                        
                        <div class="col-sm-8 padfive">
                            <button class="btn btn-danger book-btn" data-toggle="modal" data-target="#optional_form">Book Now</button>
                        </div>
                        <div class="collapse" id="emailmodel" aria-expanded="true" style="">  
                            <div class="well max_wd20 mt-2">
                                <h4>Send Email</h4>
                                <form name="agent_email" method="post" action="<?php echo base_url () . 'index.php/tours/b2c_voucher/'.$package_details[0]['id'].'/show_broucher/mail';?>">
                                 <input id="inc_sddress" value="1" type="hidden" name="inc_sddress">
                                   <input id="inc_fare" value="1" type="hidden" name="inc_fare"> 
                                    <div class="row mt-1">
                                        <label>Email Id </label>
                                        <input id="email" placeholder="Email Id" class="form-control" type="text" checked="" name="email">  
                                        <br>       
                                        <label>Subject</label>
                                        <textarea id="subject" placeholder="Subject" class="form-control" type="text" checked="" name="email_body"> </textarea>
                                   </div>   
                      <div class="modal-footer"><button type="submit" class="btn btn-primary" value="Submit">Send Email</button>  </div>
                  </form>  </div>
              </div>
                    </div>
                    <!-- <div class="row id-support">
                        <p class="c-text">Customer Support Number</p>
                        <p class="n-text">+91 9880682211<br>+91 9511917383</p>
                    </div> -->
                </div>
            </div>
            <div class="row shadow-1 id-support mt-2 id-need-div">
                <p class="c-text">Need Pace Help?</p>
                <p class="n-text">We are more than happy to help you, Kindly call or email us and we will assist you with best of our services.</p>
                <div class="row mt-1">
                    <div class="col-sm-7 nopad">
                        <p class="n-text">+91 9880682211 <br> support@pacetravels.in</p>
                    </div>
                    <div class="col-sm-5 nopad">
                        <button class="btn btn-default col-sm-12" data-toggle="modal" data-target="#enquiry_form_out">Enquiry</button>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    
    <div class="row fulldetab">
        <div class="col-md-9">
            <div class="detailtab">
				<ul class="nav nav-tabs trul">
					<li class="active"><a href="#itinerary" data-toggle="tab">Itinerary</a></li>
					<li class="trooms"><a href="#overview" data-toggle="tab">Overview</a></li>
					<li><a href="#pricing" data-toggle="tab">Pricing Details</a></li>
					<li><a href="#optionaltours" data-toggle="tab">Optional Tours</a></li>
					<li class="tfacility"><a href="#tandc" data-toggle="tab">Terms &amp; Conditions</a></li>

				</ul>
                <div class="tab-content5">

					<!-- Over View-->
                    <div class="tab-pane active" id="itinerary">
                        <div class="innertabsxl">
                            <div class="comenhtlsum">
								<div class="innertabsxl">
                                   <?php
									foreach ($tours_itinerary_dw as $key => $itinary) {
									  $accommodation = $itinary['accomodation'];
									  $accommodation = json_decode($accommodation);
									  $visited_city=json_decode($itinary['visited_city'],1);?>
                                    <div class="htlrumrowxl">
                                        <div class="hotelistrowhtl">

                                            <div class="daytrip">
                                                    <strong>Day</strong>
                                                    <b><?php echo $key+1; ?></b>
                                             </div>

                                            <div class="clear"></div>
                                            <div class="dayecd">
                                                <div class="hotelhed"><?php echo  $itinary['visited_city_name']; ?></div>
                                                <span class="singleadrspara"><?php echo  htmlspecialchars_decode($itinary['itinerary_des']);   ?></span>
                                            </div>
                                            <div class="dayecd">
                                                <div class="hotelhed">Meal Plan:</div>
                                                <span class="singleadrspara"><?php foreach ($accommodation as  $accom) {
											if ($accom === end($accommodation)){
												 echo $accom;
											  }else{
												 echo $accom.'|';
											  }
										} ?></span>
                                            </div>

                                        </div>

                                    </div>
                                   <?php } ?>
                                   </div>
                            </div>
                        </div>
                    </div>
                    <!-- Over View End-->

                    <!-- Itinerary-->
                    <div class="tab-pane" id="overview">
                        <div class="innertabsxl">
                            <div class="comenhtlsum">
                                <div class="innertabsxl">
                                    <div class="htlrumrowxl">
                                        <div class="dayecd">
                                            <div class="hotelhed">Hotel Details</div>
											<span class="singleadrspara">
												<p style="margin:0;white-space: normal;">
													<ul>
													<?php 
														foreach($tours_hotel_det as $hotel_det_key => $hotel_val){
													?>
														<li>
														<?php if($hotel_val['no_of_night']['hotel_id']!='') {?>
															<?=$hotel_val['no_of_night']?> Nights Accommodation in <?=$hotel_val['hotel_name']?>
														<?php }else{ ?>
															<?=$hotel_val['no_of_night']?> Nights in <?=$hotel_val['city']?>
														<?php } ?>
														</li>
													<?php
														}
													?>
													</ul>
												</p>
											</span>
                                        </div>
                                    </div>
                                    <div class="htlrumrowxl">
                                            <div class="dayecd">
                                                <div class="hotelhed">Inclusions</div>
                                                <span class="singleadrspara">
                                                    <p style="margin:0;white-space: normal; padding-left: 10px;">
													<?php 
														$package_details[0]['inclusions'] = str_replace('\n', '', $package_details[0]['inclusions']);
														echo htmlspecialchars_decode($package_details[0]['inclusions']); 
													?>
													</p>
                                                </span>
                                            </div>
                                    </div>
                                    <div class="htlrumrowxl">
                                            <div class="dayecd">
                                                <div class="hotelhed">Exclusions</div>
                                                <span class="singleadrspara">
                                                    <p style="margin:0;white-space: normal;font-weight: normal;">
													<?php 
													$package_details[0]['exclusions'] = str_replace('\n', '', $package_details[0]['exclusions']);
													echo htmlspecialchars_decode($package_details[0]['exclusions']); 
													?>
													</p>
                                                </span>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="pricing">
                        <div class="innertabsxl">
                            <div class="comenhtlsum">
                                <div class="innertabsxl">
                                    <div class="htlrumrowxl">
                                        <div class="dayecd">
                                            <div class="hotelhed">Price Details</div>
                                            <span class="singleadrspara">
												<ul class="price1">
													<table class="table mt-1" style="margin-right: auto;margin-left: auto;width: 100%;text-align: center;">
														<?php 
															foreach ($b2c_tour_price as $tour_price_fly) { 
																$occ=$tour_price_fly['occupancy'];
																$query_x = "select * from occupancy_managment where id='$occ'"; 
																$exe   = $this->db->query ( $query_x )->result_array ();
																$fetch_x = $exe[0];
														?>
																		
															<tr>
																<th><?=$fetch_x['occupancy_name']?></th>
																<td><del>₹ <?=number_format($tour_price_fly['market_price'],2)?></del>  <br/> ₹ <?=number_format($tour_price_fly['netprice_price']+$markup_val[0]['value'],2)?></td>
															</tr>
														<?php } ?> 
													</table>
												</ul>
											
											</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="optionaltours">
                        <div class="innertabsxl">
                            <div class="comenhtlsum">
                                <div class="innertabsxl">
                                    <div class="htlrumrowxl">
                                        <div class="dayecd">
                                            <div class="hotelhed">Optional Tour Details</div>
                                            <span class="singleadrspara">
												<div class="id-overview-row"> 
													
													
													<?php
														$city_wise_opt_tour=array();
														foreach($optional_tour_details as $opt_key => $opt_val){
															$city_wise_opt_tour[$opt_val['city_name']][$opt_key]=$opt_val;
														}
														if(!empty($optional_tour_details)) {
															
																foreach($city_wise_opt_tour as $opt_key => $copt_val){
															?>
																<table class="table mt-1">
																<tr>
																	<th style="text-align:left;"><?=$opt_key?></th>
																	<th>Price Per Adult</th>
																	<th>Price Per Child</th>
																	<th>Price Per Infant</th>
																</tr>
																<?php 
																foreach($copt_val as $copt_key => $opt_val){
																?>
																<tr>
																	<td style="text-align:left;">
																		<label for="op3">
																		<span><?=$opt_val['tour_name']?></span></label>
																	</td>
																	<td>INR <?=$opt_val['adult_price']?></td>
																	<td>INR <?=$opt_val['child_price']?></td>
																	<td>INR <?=$opt_val['infant_price']?></td>
																</tr>
																
																<?php } 
																?>
																</table>
															<?php
															} 
													
														}else{ ?>
														<table class="table mt-1">
														<tr>
															<th>No Optional Services Available</th>
														</tr>
														</table>
														<?php } ?>
												  </div>
											
											</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Itinerary End-->

                    <!-- Terms & Conditions-->
                    <div class="tab-pane" id="tandc">
                        <div class="innertabsxl">
                            <div class="comenhtlsum">
                                <div class="innertabsxl">
                                    <div class="htlrumrowxl">
                                        <div class="dayecd">
                                            <div class="hotelhed">Terms & Conditions</div>
                                            <span class="singleadrspara"><p><?=$package_details[0]['terms']?></p></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="innertabsxl">
                                    <div class="htlrumrowxl">
                                        <div class="dayecd">
                                            <div class="hotelhed">Cancellation Policy</div>
                                            <span class="singleadrspara"> <p><?=$package_details[0]['canc_policy']?></p></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="innertabsxl">
                                    <div class="htlrumrowxl">
                                        <div class="dayecd">
                                            <div class="hotelhed">Payment Policy</div>
                                            <span class="singleadrspara"><p><?=$package_details[0]['b2c_payment_policy']?></p></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="innertabsxl">
                                    <div class="htlrumrowxl">
                                        <div class="dayecd">
                                            <div class="hotelhed">Visa Documentation</div>
                                            <span class="singleadrspara"><p><?=$package_details[0]['visa_procedures']?></p></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- <div class="innertabs"> -->
                        
                    </div>

                    <!-- <div class="tab-pane" id="documentation">
                        <div class="comenhtlsum">
                            lorem ipsum
                        </div>
                    </div>

                    <div class="tab-pane" id="paymentpolicy">
                        <div class="comenhtlsum">
                            lorem ipsum
                        </div>
                    </div> -->
                    <!-- Terms & Conditions End-->

                    <!-- Map-->
                    <!-- <div class="tab-pane" id="gallery">
                        <div class="innertabs p-0 mt-1">

                            <div id="owl-demobaner1" class="owl-carousel indexbaner">
                            <?php if(!empty($package_traveller_photos)){ ?>
                                <?php foreach($package_traveller_photos as $ptp){ ?>
                               <div class="item">
                                    <div class="xlimg"><img src="<?php echo $GLOBALS['CI']->template->domain_upload_pckg_images($ptp->traveller_image); ?>" alt="" /></div>
                              </div>
                              <?php } ?>
                            <?php } ?>
                            </div>
                        </div>
                    </div> -->
                    <!-- Map End-->


                    </div>
                </div>
        </div>
        <div class="col-md-3 nopad">
            <h4 class="h4">Related Packages</h4>
				<?php 
					//debug($related_packages);
					$package_count=1;
					foreach($related_packages as $related_val){
			
					if($package_count>=5){
						break 1;
					}
				?>
                <div class="id-selected-package-d">
                    <div class="id-image-d">
                      <div class="id-content-d">
                        <div class="id-content-overlay-d"></div>
                        <img src="<?php echo $GLOBALS['CI']->template->domain_images($related_val['pack_banner'])?>" alt="image">
                        <div class="id-content-details-d id-fadeIn-bottom-d">
                          <div class="price_package-d">
                            <div>
                              <span class="id-head-cost">Package cost</span><br>
                              <span><del>₹ <?=number_format($related_val['market_price'],0)?>/-<del></span>&nbsp;&nbsp;
                              <span>₹ <?=number_format($related_val['netprice_price'],0)?>/-</span>
                            </div>
                            <div class="btns">
                              <a href="<?php echo base_url().'index.php/tours/details/'.$related_val['pack_id']?>" class="btn btn-default">View more</a>
                              <a class="btn btn-danger rel_enq" data-pack_name="<?=$related_val['package_name']?>" data-pack_id="<?=$related_val['pack_id']?>" data-pack_code="<?=$related_val['tour_code']?>" data-toggle="modal" data-target="#enquiry_form_rel">Quick enquiry</a>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="caption text-center">
                      <h4><?=$related_val['package_name']?></h4>
                    </div>
                </div>
				<?php
					$package_count++;
					}
				?>
               
        </div>
    </div>

</div>

</div>


 <!------ enquiry form for related package ------------->

	
    <div class="modal fade" id="enquiry_form_out" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Package Enquiry</h4>
          </div>
          <div class="modal-body">
		  <form action="<?php echo base_url();?>index.php/tours/send_enquiry/detail-<?=$prev_page?>" method="post" id="send_enquiry">
            <input type="hidden"  name="pack_id"  class="pack_id" value="<?=$package_details[0]['id']?>">
            <input type="hidden"  name="pack_name"  class="pack_name" value="<?=$package_details[0]['package_name']?>">
			<input type="hidden"  name="pack_code"  class="pack_code" value="<?=$package_details[0]['tour_code']?>">
			<input type="hidden"  name="agent_id" class="agent_id"  value="<?=$this->entity_user_id?>">
            <div class="row id-body-div">
				<div class="col-sm-6">
					<label class="id-label">Tours Code</label><p><strong class="pack_id_text"><?=$package_details[0]['tour_code']?></strong></p>
				</div>
				<div class="col-sm-6">
					<label class="id-label">Package name</label><p><strong class="pack_name_text"><?=$package_details[0]['package_name']?></strong></p>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-4 padfive">
					<label>Name<span class="dfman">*</span></label>
					<input type="text" class="fulwishxl alpha" id="first_name"  name="name" placeholder="Name" value="<?=$this->entity_first_name?>" required/>
				
					<span id="verificationCodeErr" style="color:red; font-size: small"></span>
				</div>
				<div class="col-sm-4 padfive">
					<label>Contact Number<span class="dfman">*</span></label>
					<input type="text" class="fulwishxl numeric" value="<?=$this->entity_phone?>" maxlength="12" id="phone" name="phone" placeholder="Contact Number" required/>
					<span id="verificationCodeErr" style="color:red; font-size: small"></span>
				</div>
				<div class="col-sm-4 padfive">
					<label>Email<span class="dfman">*</span></label>
					<input type="email" class="fulwishxl" id="email" name="Email" value="<?=$this->entity_email?>" maxlength="45" placeholder="Email" required/>
					<span id="verificationCodeErr" style="color:red; font-size: small"></span>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-2 padfive">
					<label class="id-label">Adult</label>
					<input type="number"  name="adult" class="form-control" placeholder="Adult" required min="1">
				  </div>
				  <div class="col-sm-2 padfive">
					<label class="id-label">Child</label>
					<input type="number"  name="child" class="form-control" placeholder="Child" required min="0">
				  </div>
				  <div class="col-sm-2 padfive">
					<label class="id-label">Infant</label>
					<input type="number"  name="infant" class="form-control" placeholder="Infant" required min="0">
				  </div>
				<div class="col-sm-6 padfive">
					<label class="id-label">Departure Date</label>
					<input  id="enquiry_date_rel" min='<?=date('Y-m-d')?>' name="dep_date" type="date" class="form-control id-inputfield enquiry_datepicker_rel"  required>
				</div>
			</div>
			<div class="row">
			  <div class="col-sm-12 padfive">
				<label class="id-label">Messenger</label>
				<textarea  name="message" aria-hidden="true" class="form-control" placeholder="Enter details if any" maxlength="200"></textarea>
			  </div>
			</div>
   
			<div class="row mt-1">
				<div class="col-sm-offset-8 col-sm-4 nopad">
					<button type="submit" class="btn btn-danger id-enquiry-btn send_enquiry_btn">Send Enquiry</button>
				</div>
			</div>
          </form>
          
           </div>
        </div>
        </div>
    </div>
	<div class="modal fade" id="enquiry_form_in" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Package Enquiry</h4>
				</div>
				<div class="modal-body">
					<form action="<?php echo base_url();?>index.php/tours/send_enquiry/detail-<?=$prev_page?>" method="post" id="send_enquiry">
						<input type="hidden"  name="pack_id"  class="pack_id" value="<?=$package_details[0]['id']?>">
						<input type="hidden"  name="pack_name"  class="pack_name" value="<?=$package_details[0]['package_name']?>">
						<input type="hidden"  name="pack_code"  class="pack_code" value="<?=$package_details[0]['tour_code']?>">
						<input type="hidden"  name="agent_id" class="agent_id"  value="<?=$this->entity_user_id?>">
				
				
						<div class="row id-body-div">
							<div class="col-sm-6">
								<label class="id-label">Tours Code</label><p><strong class="pack_id_text"><?=$package_details[0]['tour_code']?></strong></p>
							</div>
							<div class="col-sm-6">
								<label class="id-label">Package name</label><p><strong class="pack_name_text"><?=$package_details[0]['package_name']?></strong></p>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-4 padfive">
								<label>Name<span class="dfman">*</span></label>
								<input type="text" class="fulwishxl alpha" id="first_name"  name="name" placeholder="Name" value="<?=$this->entity_first_name?>" required/>
							
								<span id="verificationCodeErr" style="color:red; font-size: small"></span>
							</div>
							<div class="col-sm-4 padfive">
								<label>Contact Number<span class="dfman">*</span></label>
								<input type="text" class="fulwishxl numeric" value="<?=$this->entity_phone?>" maxlength="12" id="phone" name="phone" placeholder="Contact Number" required/>
								<span id="verificationCodeErr" style="color:red; font-size: small"></span>
							</div>
							<div class="col-sm-4 padfive">
								<label>Email<span class="dfman">*</span></label>
								<input type="email" class="fulwishxl" id="email" name="Email" value="<?=$this->entity_email?>" maxlength="45" placeholder="Email" required/>
								<span id="verificationCodeErr" style="color:red; font-size: small"></span>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-2 padfive">
								<label class="id-label">Adult</label>
								<input type="number"  name="adult" class="form-control" placeholder="Adult" required min="1">
							  </div>
							  <div class="col-sm-2 padfive">
								<label class="id-label">Child</label>
								<input type="number"  name="child" class="form-control" placeholder="Child" required min="0">
							  </div>
							  <div class="col-sm-2 padfive">
								<label class="id-label">Infant</label>
								<input type="number"  name="infant" class="form-control" placeholder="Infant" required min="0">
							  </div>
							<div class="col-sm-6 padfive">
								<label class="id-label">Departure Date</label>
								<input  id="enquiry_date_in" min='<?=date('Y-m-d')?>' name="dep_date" type="date" class="form-control id-inputfield enquiry_datepicker_rel" >
							</div>
						</div>
						<div class="row">
						  <div class="col-sm-12 padfive">
							<label class="id-label">Messenger</label>
							<textarea  name="message" aria-hidden="true" class="form-control" placeholder="Enter details if any" maxlength="200"></textarea>
						  </div>
						</div>
			   
						<div class="row">
							<div class="col-sm-offset-8 col-sm-4 nopad">
								<button type="submit" class="btn btn-danger id-enquiry-btn send_enquiry_btn">Send Enquiry</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

<div class="modal fade" id="optional_form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Package Details</h4>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
					<form action="<?php echo base_url();?>index.php/tours/optional_tour_details/<?=$package_details[0]['id']?>" method="post" id="send_booking">	
				
						<div class="hide">
							<input type="hidden" class="pack_id" name="pack_id" value="<?=$package_details[0]['id']?>">
							<input type="hidden" class="sel_departure_date" name="sel_departure_date" value="">
							<input type="hidden" class="prev_page" name="prev_page" value="<?=$prev_page?>">
						</div>
						<table class="table">
							<tr>
								<th>Adult on single sharing</th>
								<th>Adult on twin sharing</th>
								<th>Triple Sharing</th>
								<th>Child with bed</th>
								<th>Child without bed</th>
								<th>Infant</th>
							</tr>
							
							<tr> 
								<td><del>₹ <?php echo isset($per_person_market_price[8])?number_format($per_person_market_price[8],2) : "-NA-"; ?></del><br>₹ <?php echo isset($per_person_price[8])?number_format($per_person_price[8],2) : "-NA-"; ?></td>
								<td><del>₹ <?php echo isset($per_person_market_price[10])?number_format($per_person_market_price[10],2) : "-NA-"; ?></del><br>₹ <?php echo isset($per_person_price[10])?number_format($per_person_price[10],2) : "-NA-"; ?></td>
								<td><del>₹ <?php echo isset($per_person_market_price[14])?number_format($per_person_market_price[14],2) : "-NA-";?></del><br>₹ <?php echo isset($per_person_price[14])?number_format($per_person_price[14],2) : "-NA-"; ?></td>
								<td><del>₹ <?php echo isset($per_person_market_price[11])?number_format($per_person_market_price[11],2) : "-NA-";?></del><br>₹ <?php echo isset($per_person_price[11])?number_format($per_person_price[11],2) : "-NA-"; ?></td>
								<td><del>₹ <?php echo isset($per_person_market_price[12])?number_format($per_person_market_price[12],2) : "-NA-";?></del><br>₹ <?php echo isset($per_person_price[12])?number_format($per_person_price[12],2) : "-NA-"; ?></td>
								<td><del>₹ <?php echo isset($per_person_market_price[13])?number_format($per_person_market_price[13],2) : "-NA-";?></del><br>₹ <?php echo isset($per_person_price[13])?number_format($per_person_price[13],2) : "-NA-"; ?></td>
							</tr>
							
						</table>
						<ul>
							<?php echo $package_details[0]['terms']; ?>
						</ul>
						
						<div class="row modal-btn">
							<div class="col-sm-offset-6 col-sm-3 padfive">
								<button type="button" data-dismiss="modal" class="btn btn-primary" data-toggle="modal" data-target="#enquiry_form_in">Enquiry</button>
							</div>
							
							<div class="col-sm-3 padfive">
								<button type="submit" class="btn btn-danger id-enquiry-btn">Next</button>
							</div>
						</div>
					</form>
                </div>
            </div>
        </div>
	</div>
</div>
      


         <!------ enquiry form for related package ------------->

	
    <div class="modal fade" id="enquiry_form_rel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Package Enquiry</h4>
          </div>
          <div class="modal-body">
          <form action="<?php echo base_url();?>index.php/tours/send_enquiry/detail-<?=$prev_page?>" method="post" id="send_enquiry">
            <div class="container-fluid id-enquiry-modal nopad">
				<input type="hidden"  name="pack_id"  class="pack_id" value="<?=$package_details[0]['id']?>">
				<input type="hidden"  name="pack_name"  class="pack_name" value="<?=$package_details[0]['package_name']?>">
				<input type="hidden"  name="pack_code"  class="pack_code" value="<?=$package_details[0]['tour_code']?>">
				<input type="hidden"  name="agent_id" class="agent_id"  value="<?=$this->entity_user_id?>">
			
            
			 <div class="row id-body-div">
                  <div class="col-sm-6">
                    <label class="id-label">Tours Code</label><p><strong class="pack_id_text">hfdfbh</strong></p>
                  </div>
                  <div class="col-sm-6">
                    <label class="id-label">Package name</label><p><strong class="pack_name_text">hfdfbh</strong></p>
                  </div>
                  
                  
              </div>
			<div class="row">
                <div class="col-sm-4 padfive">
                    <label>Name<span class="dfman">*</span></label>
                    <input type="text" class="fulwishxl alpha" id="first_name"  name="name" placeholder="Name" value="<?=$this->entity_first_name?>" required/>
				
                    <span id="verificationCodeErr" style="color:red; font-size: small"></span>
                </div>
                <div class="col-sm-4 padfive">
                    <label>Contact Number<span class="dfman">*</span></label>
                    <input type="text" class="fulwishxl numeric" value="<?=$this->entity_phone?>" maxlength="12" id="phone" name="phone" placeholder="Contact Number" required/>
                    <span id="verificationCodeErr" style="color:red; font-size: small"></span>
                </div>
				<div class="col-sm-4 padfive">
                    <label>Email<span class="dfman">*</span></label>
                    <input type="email" class="fulwishxl" id="email" name="Email" value="<?=$this->entity_email?>" maxlength="45" placeholder="Email" required/>
                    <span id="verificationCodeErr" style="color:red; font-size: small"></span>
                </div>
            </div>
            <div class="row">
				<div class="col-sm-2 padfive">
					<label class="id-label">Adult</label>
					<input type="number"  name="adult" class="form-control" placeholder="Adult" required>
				  </div>
				  <div class="col-sm-2 padfive">
					<label class="id-label">Child</label>
					<input type="number"  name="child" class="form-control" placeholder="Child" required>
				  </div>
				  <div class="col-sm-2 padfive">
					<label class="id-label">Infant</label>
					<input type="number"  name="infant" class="form-control" placeholder="Infant" required>
				  </div>
				<div class="col-sm-6 padfive">
					<label class="id-label">Departure Date</label>
					<input  id="enquiry_date_out" min='<?=date('Y-m-d')?>' name="dep_date" type="date" class="form-control id-inputfield enquiry_datepicker_rel" value="dd/mm/yyyy" >
				</div>
            </div>
			<div class="row">
			  <div class="col-sm-12 padfive">
				<label class="id-label">Messenger</label>
				<textarea  name="message" aria-hidden="true" class="form-control" placeholder="Enter details if any" maxlength="200"></textarea>
			  </div>
			</div>
           
          <div class="row">
                <div class="col-sm-offset-8 col-sm-4 nopad">
                    <button type="submit" class="btn btn-danger id-enquiry-btn send_enquiry_btn">Send Enquiry</button>
                </div>
            </div>
          
          </div>
          </form>
           </div>
        </div>
        </div>
    </div>



<script src="<?php echo $GLOBALS['CI']->template->template_js_dir('page_resource/jquery.scrolling-tabs.js') ?>" type="text/javascript"></script>

<script>   
  $(document).ready(function() {
    //alert("gyjhg");
     $(".owl-carousel").owlCarousel({
        
       items:1,
       itemsDesktop: [1000, 1],
       itemsDesktopSmall: [900, 1],
       itemsTablet: [600,1],
       loop:true,
       margin:10,
       autoplay:true,
       navigation: true,

       pagination: false,
       autoplayTimeout:1000,
       autoplayHoverPause:true
   });
     $( ".owl-prev").html('<i class="fa fa-chevron-left"></i>');
     $( ".owl-next").html('<i class="fa fa-chevron-right"></i>');

    $("#owl-demobaner").owlCarousel({
        items : 1,
        itemsDesktop : [1000,1],
        itemsDesktopSmall : [900,1],
        itemsTablet: [600,1],
        itemsMobile : [479,1],
        navigation : false,
        pagination : true,
        autoPlay : true
      });

    $("#owl-demobaner1").owlCarousel({
        items : 1,
        itemsDesktop : [1000,1],
        itemsDesktopSmall : [900,1],
        itemsTablet: [600,1],
        itemsMobile : [479,1],
        navigation : false,
        pagination : true,
        autoHeight : true,
        autoPlay : true
      });

    $('#sendquery').on('click', function(e) {
        e.preventDefault();

        $('#subtqry').provabPopup({
                    modalClose: true,
                    closeClass: 'closequery',
                    zIndex: 100005
        });
    });

});</script>
<script type="text/javascript">
	var curday = function(sp){
		today = new Date();
		var dd = today.getDate();
		var mm = today.getMonth()+1; //As January is 0.
		var yyyy = today.getFullYear();

		//if(dd<10) dd='0'+dd;
		//if(mm<10) mm='0'+mm;
		//return (mm+sp+dd+sp+yyyy);
		return (yyyy+sp+mm+sp+dd);
	};
	jQuery(function(){
		var e_day="<?php echo $group_departure; ?>"; 
		//console.log(e_day);
		var enableDays=e_day.split(',');
		var lastItem = "<?php echo $last_item; ?>";
		var firstItem = "<?php echo $first_item; ?>";
		if(firstItem<curday('-')){
			var firstItem = curday('-');
		}
		function enableAllTheseDays(date) {
			var sdate = $.datepicker.formatDate( 'd-m-yy', date)
			
			if($.inArray(sdate, enableDays) != -1) {
			
				return [true];
			}
		
			return [false];
		}
    //console.log(curday('/'));
	//console.log(curday('-'));
	//console.log(firstItem);
	
		$('#datepicker_dat_group,#enquiry_datepicker_in,#enquiry_datepicker_out').datepicker({dateFormat: 'dd-mm-yy', beforeShowDay: enableAllTheseDays,minDate: new Date(firstItem), maxDate: new Date(lastItem)});
		//$('#enquiry_datepicker_in,#enquiry_datepicker_out').datepicker({dateFormat: 'dd-mm-yy', beforeShowDay: enableAllTheseDays,minDate: new Date(firstItem), maxDate: new Date(lastItem)});
		
	})
	$(document).ready(function() {
		$( "#datepicker_dat" ).datepicker();
		//$('#enquiry_date_in,#enquiry_date_rel,#enquiry_date_out').datepicker({ minDate: new Date()});
	});
</script>
<script type="text/javascript">
$(document).ready(function(){
    $( ".detailtab" ).tabs();
//  Check Radio-box
    $(".rating input:radio").attr("checked", false);
    $('.rating input').click(function () {
        $(".rating span").removeClass('checked');
        $(this).parent().addClass('checked');
    });

    $('input:radio').change(
    function(){

        var userRating = this.value;
        var pkg_id=$('#pkg_id').val();

        //alert(userRating);
        //alert(pkg_id);
       var str=pkg_id+','+userRating;
        $.ajax({
            url:app_base_url+'tours/package_user_rating',
            type:'POST',
            data:'rate='+str,
            success:function(msg){
                //alert(msg);
               $('#msg_pak').show();
               $('#msg_pak').text('Thank you for rating this package').css('color','green').fadeOut(3000);
            },
            error:function(){
            }
         }) ;

    });
    
	
	$(document).on('click','.rel_enq',function() {
		var sel_pack_id=$(this).data('pack_id');
		var sel_pack_name=$(this).data('pack_name');
		var sel_pack_code=$(this).data('pack_code');
		//alert(sel_pack_id);
		$('#enquiry_form_rel').find('.pack_id').val(sel_pack_id);
		$('#enquiry_form_rel').find('.pack_id_text').text(sel_pack_code);
		$('#enquiry_form_rel').find('.pack_name_text').text(sel_pack_name);
		$('#enquiry_form_rel').find('.pack_name').val(sel_pack_name);
		$('#enquiry_form_rel').find('.pack_code').val(sel_pack_code);
	});
    $('.nav-tabs').scrollingTabs();
	$('#optional_form').on('show.bs.modal', function (e) {
		var sel_dep_date=$('#datepicker_dat_group').val();
		var is_login='<?=$this->entity_email?>';
		
		$('.sel_departure_date').val(sel_dep_date);
		if(sel_dep_date==''){
			
			alert("Please select departure date.");
			e.preventDefault();
		}else if(is_login==''){
			$(".logindown").trigger("click");

			e.preventDefault();
		}
	});
});
</script>
</body></html>

<style type="text/css">
    .htlrumrowxl, .comenhtlsum, .id-price-pac, .ratingusr{
        padding: 10px;
        box-shadow: 1px 1px 10px #ccc;
        /*border: 1px solid #f2f2f2;*/
        margin: 10px 0;
    }
    .innertabsxl{
        padding: 2px;
        margin-top: 5px;
    }
    .hotelhed {
        font-weight: bold;
    }
    .hedft{
        font-weight: bold;
        color: #0095ce;
    }
    #tandc ul.checklistxl.checklist {
        padding: 0;
    }
    #tandc ul.checklistxl.checklist li{
        padding: 0;
        margin: 0;
    }
    .p-0{
        padding: 0!important;
    }
    .m-0{
        margin: 0!important;
    }
    .mt-1{
        margin-top: 10px!important;
    }
    .mt-2{
        margin-top: 20px!important;
    }
    .mb-1{
        margin-bottom: 10px!important;
    }
    .mb-2{
        margin-bottom: 20px!important;
    }
    .xlimg img{
        border-radius: 15px;
    }
    .indexbaner.owl-theme .owl-controls {
        /*border-bottom-right-radius: 15px;
        border-bottom-left-radius: 15px;*/
    }
    .fulldetab {
        padding: 30px 0;
    }
    .pophed {
        background: none repeat scroll 0 0 #EEEEEE;
        border-bottom: 1px solid #DDDDDD;
        color: #444444;
        display: block;
        font-size: 16px;
        margin: -15px -15px 15px;
        overflow: hidden;
        padding: 10px;
    }
    #tourenquiry label{
        margin-top: 10px;
    }
    #tourenquiry button{
        margin-top: 20px;
        width: 100%;
        height: 40px;
    }
    .modal-header{
        background-color: #0095ce;
        color: #fff;
    }
    .id-pac-label1{
      padding: 10px 5px;
      /*background: linear-gradient(96deg,#9c5d00,#f99800);*/
      background: #f99800;
      color: #fff;
      width: 100%;
      border-top-left-radius: 20px;
      border-bottom-left-radius: 20px;
      text-align: center;
      margin-top: 10px;
    }

    .id-download-itenirary{
      padding: 5px 0 5px 0!important; 
      margin-bottom: 10px!important;
    }
    .id-download-itenirary a{
      color: #337ab7;
      cursor: pointer;
    }
    .pack_b2c .p-price{
        font-size: 25px;
        color: #16AAE2;
        margin-top: 10px;
        margin-bottom: 0;
    }
    .pack-div{
        padding: 10px;
        background-color: #f2f2f2;
        /*margin-top: 10px;*/
    }
    .pack-div p{
        margin: 5px;
    }
    .btn-div{
            /*background: linear-gradient(0deg,#000000,#613700);*/
        background: #36465d;
        padding: 20px;
        border-bottom-right-radius: 10px;
        border-bottom-left-radius: 10px;
    }
    .btn-div button{
        width: 100%;
        margin: 4px;
    }
    .padfive{
        padding: 0 2px!important;
    }
    .mb-0{
        margin-bottom: 0;
    }
    .pack_b2c{
        border-radius: 10px;
    }
    .id-image-slider-div img{
        width: 100%;
        height: 400px;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }
  #optional_form table, th, td {
    border: 1px solid #ccc;
    text-align: center;
    vertical-align: middle !important;
}
   #optional_form table, th {
    background: #f2f2f2;
}
    .modal-btn button{
        width: 100%;
    }
    .mt-5{
        margin-top: 50px;
    }
    .id-pack-div1 .p-inline-nights{
        display: inline-block;
        text-align: center;
    }
    .id-pack-div1 .p-inline-nights{
        /*margin-right: 5px; */
        padding: 8px;
        /* background-color: #ccc; */
        width: 40%;
        height: 80px;
        padding-top: 15px;
        font-size: 25px;
        border-radius: 8px;
        line-height: 1;
    }
    .p-inline-nights span{
        font-size: 14px;
    }
    .id-pack-div1 .p-inline-city{
        margin-right: 10px;
        font-size: 15px;
        margin-bottom: 0;
    }
    .id-pack-div1{
        padding: 0 0 10px 20px;
    }
    .shadow-1{
        box-shadow: 0 5px 11px 0 rgba(0,0,0,0.18),0 4px 15px 0 rgba(0,0,0,0.15) !important;
    }
    .radius-10{
        border-radius: 10px;
    }
    .id-pack-fa{
        background-color: #fff;
        border: 1px solid #ccc;
        /* padding: 20px; */
        border-radius: 100%;
        height: 50px;
        width: 50px;
        font-size: 20px;
        line-height: 2.5;
        color: #0095ce;
    }
    .id-label-div label{
        text-align: center;
        margin-right: 15px;
    }
    .id-label-div p{
        margin: 5px 0 5px 0!important;
    }
    .blue-n{
        background-color: #2c95ce;
        color: #fff;
    }
    .orange-n{
        background-color: #f99800;
        color: #fff;
    }
    .m-05{
        margin: 10px 0;
    }
    .id-fare-div p span{
        font-size: 35px;
        font-weight: 600;
        color: #d9534f;
    }
    .id-fare-div p{
        font-size: 12px;
        color: #777;
            word-break: break-all;
    }
    .id-fare-div h4{
        background-color: #2c95ce;
        color:#fff;
        text-align: center;
        margin: 0;
        padding: 15px;
        border-top-right-radius: 10px;
        border-top-left-radius: 10px;
    }
    .id-fare-div .down-btn{
        
        color: #666;
    }
    .book-btn{
        width: 100%;
        height: 40px;
        margin-bottom: 5px;
    }
    .id-support .c-text{
        text-align: center;
        font-size: 20px;
        /*margin-bottom: 0;*/
        color: #2c95ce;
        font-weight: 600;
        border-bottom: 1px solid #ccc;
    }
    .id-support .n-text{
        /*text-align: center;*/
        font-size: 13px;
        margin-bottom: 0;
        margin-top: 10px;
    }
    .id-support{
        background-color: #f2f2f2;
        border-radius: 10px;
        padding: 15px;
    }
    .triangle-left,
    .triangle-right,
    .triangle-top,
    .triangle-bottom{
      width: 0;
      height: 0;
      margin: 5px auto;
    }
    .triangle-left,
    .triangle-right{
      border-top: 38px solid transparent;
      border-bottom: 38px solid transparent;
    }
    .triangle-right{
      border-left: 10px solid #f99800;
      position: absolute;
        top: -3px;
        right: 15px;
    }
    .id-need-div{
        height: 176px;
    }
    .id-need-div button{
        padding: 8px 0;
        margin-top: 6px;
    }
    .fulldetab .nav-tabs.trul > li > a {
        /* height: 49px; */
        /* width: auto; */
        position: relative;
        display: block;
        padding: 10px 18px;
         border:none; 
        border-radius: 4px 4px 0px 0px;
        background: #ffffff;
        font-size: 14px;
        color: #666;
        margin: 0 1px;
    }



    .id-head-h span{
    color: #337ab7;
    font-size: 18px!important;
  }
  .id-head-h small{
    color: #666;
    font-size: 12px!important;
  }
  .id-content-d img{
    width: 100%!important;
    height: 200px;
  }
.daytrip strong {
    color: #222;
    float: right;
    font-size: 20px;
    font-weight: 600;
    line-height: 30px;
    padding: 0 10px;
}
.daytrip {
    background: none repeat scroll 0 0 #ffffff;
    float: left;
    margin-bottom: 10px;
}
.daytrip b {
    background: none repeat scroll 0 0 #ff0000;
    color: #fff;
    float: left;
    font-size: 20px;
    line-height: 30px;
    padding: 0 10px;
    border-radius: 50%;
}


    .id-image-d{
      margin: 0 auto;
      overflow: hidden;
    }
    .id-content-d {
      position: relative;
      margin: auto;
      overflow: hidden;
    }
    .id-selected-package-d:hover .id-content-overlay-d{
      opacity: 1;
    }
    .id-content-details-d {
      background: rgba(0,0,0,0.7)!important;
      position: absolute;
      text-align: center;
      padding-left: 1em;
      padding-right: 1em;
      width: 100%;
      height: 100%;
      opacity: 0;
      -webkit-transform: translate(-50%, -50%);
      -moz-transform: translate(-50%, -50%);
      transform: translate(-50%, -50%);
      -webkit-transition: all 0.3s ease-in-out 0s;
      -moz-transition: all 0.3s ease-in-out 0s;
      transition: all 0.3s ease-in-out 0s;
    }
    .id-selected-package-d:hover .id-content-details-d{
      top: 50%;
      left: 50%;
      opacity: 1;
    }
    .id-fadeIn-bottom-d{
      top: 50%;
      left: 50%;
    }
    .price_package-d span{
      color: #fff;
      font-size: 18px;
    }
    .price_package-d{
      margin-top: 60px;
    }

    .price_package-d .btns
    {
      display: flex;
    }
    .price_package-d a{
      flex: 1 1 0;
    margin: 5px 2px;
    }
    .price_package-d .id-head-cost{
      color: #ffe000;
    }
    .id-selected-package-d h4{
      /*background: #ffc800;*/
      background: rgb(0, 0 ,0 ,0.6);
      padding: 12px;
      margin: 0;
      color: #fff;
      font-weight: 600;
      text-transform: capitalize;
    }
    .id-selected-package-d{
      /*border: 4px solid #ffc800;*/
      border: 4px solid #fff;
      box-shadow: 2px 2px 6px #ccc;
      margin-bottom: 10px;
    }
    @media only screen and (max-width: 767px){
        .m-mb-2{
            margin-bottom: 20px;
        }
        .triangle-right{
            display: none;
        }
        .p-inline-nights{
            margin-bottom: 10px;
        }
        .id-pack-div1 .p-inline-nights {
            width: 47%;
        }
        .id-need-div button {
            width: 100%;
            margin-top: 10px;
        }
        .id-need-div {
            height: 225px;
        }
        .modal-btn button{
            margin: 3px 0;
        }
        .m-mt-0{
            margin-top: 0!important;
        }
    }
.fulldetab .nav-tabs > li.active a {
    font-weight: 500;
    border-radius: 4px 4px 0px 0px;
    background: #fff;
    font-size: 14px;
    padding: 10px 18px;
    color: #000;
    border: 1px solid #ddd !important;
    border-bottom: 1px solid #fff !important;
    border-top: 3px solid #ff0000 !important;
}
.input-date{
        width: 100%;
        border: none;
        border-bottom: 1px solid #ccc;
        padding-bottom: 10px;
        padding-top: 6px;
        border-bottom-left-radius: 5px;
        border-bottom-right-radius: 5px;
        padding-left: 10px;
        margin-bottom: 10px;
    }
   div#emailmodel button.btn.btn-primary {
    width: 100%;
    background: #2c95ce;
    border: 1px solid #2c95ce;
    padding: 10px 20px;
}
.id-enquiry-modal .id-body-div {
    border: 1px solid #ccc;
    padding: 5px;
    margin-bottom: 10px;
    border-radius: 4px;
}
.id-label {
    color: #777!important;
    font-size: 12px;
    margin-top: 5px;
}
.id-enquiry-modal button {
    margin-top: 20px;
    border-radius: 4px!important;
    height: 45px;
}
input.form-control {
    background: #fff;
    color: #333;
    box-shadow: none;
    -webkit-box-shadow: none;
    border: 1px solid #ccc;
    display: block;
    font-size: 14px;
    border-radius: 0;
}
textarea.form-control {
    height: auto;
    border-radius: 0;
    box-shadow: none;
}
div#enquiry_form_rel button.btn.btn-danger.id-enquiry-btn {
    width: 100%;
}
.row.id-body-div {
    border: 1px solid #ccc;
    padding: 5px;
    margin-bottom: 10px;
    border-radius: 4px;
}
div#enquiry_form_out button.btn.btn-danger.id-enquiry-btn {
    width: 100%;
    height: 45px;
}
div#enquiry_form_in button.btn.btn-danger.id-enquiry-btn {
    width: 100%;
    height: 45px;
}
</style>
<script type="text/javascript">
	
	$(document).ready( function() {
		$( "#place_date" ).datepicker({minDate: 0});
		
		$(document).on('click','.send_enquiry_btn',function(e){
			var is_login='<?=$this->entity_email?>';
			
			if(is_login==''){
				//alert("GSdf");
				e.preventDefault();
				$(".close").trigger("click");
				$(".logindown").trigger("click");
				
			}
		});
	});
  </script>
