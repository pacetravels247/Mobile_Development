<!-- ==============.net -->

<?php
   /*$image = $banner_images ['data'] [0] ['image'];*/
   $active_domain_modules = $this->active_domain_modules;
   $default_active_tab = $default_view;
   /**
    * set default active tab
    *
    * @param string $module_name
    *        	name of current module being output
    * @param string $default_active_tab
    *        	default tab name if already its selected otherwise its empty
    */
   function set_default_active_tab($module_name, &$default_active_tab) {
   	if (empty ( $default_active_tab ) == true || $module_name == $default_active_tab) {
   		if (empty ( $default_active_tab ) == true) {
   			$default_active_tab = $module_name; // Set default module as current active module
   		}
   		return 'active';
   	}
   }
   
   //add to js of loader
   Js_Loader::$css[] = array('href' => $GLOBALS['CI']->template->template_css_dir('backslider.css'), 'media' => 'screen');
   Js_Loader::$css[] = array('href' => $GLOBALS['CI']->template->template_css_dir('owl.carousel.min.css'), 'media' => 'screen');
   Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('owl.carousel.min.js'), 'defer' => 'defer');
    Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('backslider.js'), 'defer' => 'defer');
   Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/index.js'), 'defer' => 'defer');
   Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/pax_count.js'), 'defer' => 'defer');
   ?>
<!-- <div class="homepage-video">
   <div class="video-container">
       <video autoplay loop class="fillWidth">
           <source src="<?php echo $GLOBALS['CI']->template->template_images('video/video2.mp4')?>" type="video/mp4" />Your browser does not support the video tag. I suggest you upgrade your browser.
           <source src="<?php echo $GLOBALS['CI']->template->template_images('video/video2.mp4')?>" type="video/webm" />Your browser does not support the video tag. I suggest you upgrade your browser.
       </video>
   </div>
   </div> -->
<style type="text/css">
   .topssec { background: rgba(0, 0, 0, 0.4); }
   .carousel-control.right {
    background-image: none !important;
    right: 0%;
    top: -50%;
}
.carousel-control.left {
    background-image: none !important;
    left: 0;
    top: -50%;
}
.carousel-control .glyphicon-chevron-left, .carousel-control .glyphicon-chevron-right, .carousel-control .icon-next, .carousel-control .icon-prev {
    width: 20px;
    height: 20px;
    font-size: 24px;
}
.carousel
{
   padding: 0;
}
</style>

<div class="id-b-img-div carousel slide" id="myCarousel"  data-ride="carousel">
   <div class="carousel-inner">
    <div class="item active">
      <img src="<?php echo $GLOBALS['CI']->template->template_images('hotel_banner1.jpg')?>" style="width: 100%; height: 300px;">
    </div>

    <div class="item">
     <img src="<?php echo $GLOBALS['CI']->template->template_images('hotel_banner1.jpg')?>" style="width: 100%; height: 300px;">
    </div>

    <div class="item">
     <img src="<?php echo $GLOBALS['CI']->template->template_images('hotel_banner1.jpg')?>" style="width: 100%; height: 300px;">
    </div>
  </div>

  <!-- Left and right controls -->
  <a class="left carousel-control" href="#myCarousel" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="right carousel-control" href="#myCarousel" data-slide="next">
    <span class="glyphicon glyphicon-chevron-right"></span>
    <span class="sr-only">Next</span>
  </a>
   
   <div class="allformst">
         <!-- Tab panes -->
         <div class="container inspad">
            <div class="secndblak search_box">
               <div class="tab-content custmtab">
                  <?php if (is_active_airline_module()) { ?>
                  <div
                     class="tab-pane <?php echo set_default_active_tab(META_AIRLINE_COURSE, $default_active_tab)?>"
                     id="flight">
                     <?php echo $GLOBALS['CI']->template->isolated_view('share/flight_search')?>
                  </div>
                  <?php } ?>
                  <?php if (is_active_hotel_module()) { ?>
                  <div
                     class="tab-pane <?php echo set_default_active_tab(META_ACCOMODATION_COURSE, $default_active_tab)?>"
                     id="hotel">
                     <?php echo $GLOBALS['CI']->template->isolated_view('share/hotel_search')?>
                  </div>
                  <?php } ?>
                  <?php if (is_active_bus_module()) { ?>
                  <div
                     class="tab-pane <?php echo set_default_active_tab(META_BUS_COURSE, $default_active_tab)?>"
                     id="bus">
                     <?php echo $GLOBALS['CI']->template->isolated_view('share/bus_search')?>
                  </div>
                  <?php } ?>
                  <?php if (is_active_transferv1_module()) { ?>
                  <div
                     class="tab-pane <?php echo set_default_active_tab(META_TRANSFERV1_COURSE, $default_active_tab)?>"
                     id="transferv1">
                     <?php echo $GLOBALS['CI']->template->isolated_view('share/transferv1_search')?>
                  </div>
                  <?php } ?>
                  <?php if (is_active_car_module()) { ?>
                  <div
                     class="tab-pane <?php echo set_default_active_tab(META_CAR_COURSE, $default_active_tab)?>"
                     id="car">
                     <?php echo $GLOBALS['CI']->template->isolated_view('share/car_search')?>
                  </div>
                  <?php } ?>
                  <?php if (is_active_package_module()) { ?>
                  <div
                     class="tab-pane <?php echo set_default_active_tab(META_PACKAGE_COURSE, $default_active_tab)?>"
                     id="holiday">
                     <?php echo $GLOBALS['CI']->template->isolated_view('share/holiday_search',$holiday_data)?>
                  </div>
                  <?php } ?>
                  <?php if (is_active_sightseeing_module()) { ?>
                  <div
                     class="tab-pane <?php echo set_default_active_tab(META_SIGHTSEEING_COURSE, $default_active_tab)?>"
                     id="sightseeing">
                     <?php echo $GLOBALS['CI']->template->isolated_view('share/sightseeing_search',$holiday_data)?>
                  </div>
                  <?php } ?>
               </div>
            </div>
         </div>
         <div class="tab_border elipbord">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs tabstab navtb">
               <?php if (is_active_airline_module()) { ?>
               <li
                  class="<?php echo set_default_active_tab(META_AIRLINE_COURSE, $default_active_tab)?>"><a
                  href="#flight" role="tab" data-toggle="tab" data-tooltip="Flight">
                  <span class="sprte spritee iconcmn iconcmn1"><i class="fal fa-plane"></i></span><label>Flights</label></a>
               </li>
               <?php } ?>
               <?php if (is_active_hotel_module()) { ?>
               <li
                  class="<?php echo set_default_active_tab(META_ACCOMODATION_COURSE, $default_active_tab)?>">
                  <a href="#hotel" role="tab" data-toggle="tab" data-tooltip="Hotels">
                  <span class="sprte spritee iconcmn iconcmn1"><i class="fal fa-building"></i></span><label>Hotels</label></a>
               </li>
               <?php } ?>
             <!--   <li class="deals">
                  <a  target="_blank" href="http://travelomatix.com/special-fares-june2018.html"><span class="sprte spritee iconcmn iconcmn1"><strong class="new_deal">New!</strong><i class="fal fa-tags"></i></span><label>Hot Deals!</label></a>
               </li> -->
               <?php if (is_active_bus_module()) { ?>
               <li
                  class="<?php echo set_default_active_tab(META_BUS_COURSE, $default_active_tab)?>"><a
                  href="#bus" role="tab" data-toggle="tab" data-tooltip="Buses">
                  <span class="sprte spritee iconcmn iconcmn1"><i class="fal fa-bus"></i></span><label>Buses</label></a>
               </li>
               <?php } ?>
               <?php if (is_active_transferv1_module()) { ?>
               <li
                  class="<?php echo set_default_active_tab(META_TRANSFERV1_COURSE, $default_active_tab)?>"><a
                  href="#transferv1" role="tab" data-toggle="tab" data-tooltip="Transfers">
                  <span class="sprte spritee iconcmn iconcmn1"><i class="fal fa-taxi"></i></span><label>Transfers</label></a>
               </li>
               <?php } ?>
               <?php if (is_active_car_module()) { ?>
               <li
                  class="<?php echo set_default_active_tab(META_CAR_COURSE, $default_active_tab)?>"><a
                  href="#car" role="tab" data-toggle="tab" data-tooltip="Cars">
                  <span class="sprte spritee iconcmn iconcmn1"><i class="fal fa-car"></i></span><label>Cars</label></a>
               </li>
               <?php } ?>              
               <?php if (is_active_sightseeing_module()) { ?>
               <li
                  class="<?php echo set_default_active_tab(META_SIGHTSEEING_COURSE, $default_active_tab)?>"><a
                  href="#sightseeing" role="tab"
                  data-toggle="tab" data-tooltip="Activities">
                  <span class="sprte spritee iconcmn iconcmn1"><i class="fal fa-binoculars"></i></span><label>Activities</label></a>
               </li>
               <?php } ?>
               <?php if (is_active_package_module()) { ?>
               <li
                  class="<?php echo set_default_active_tab(META_PACKAGE_COURSE, $default_active_tab)?>"><a
                  href="#holiday" role="tab"
                  data-toggle="tab" data-tooltip="Holidays">
                  <span class="sprte spritee iconcmn iconcmn1"><i class="fal fa-tree"></i></span><label>Holidays</label></a>
               </li>
               <?php } ?>
            </ul>
         </div>
      </div>
</div>

<?php
   if(in_array('Top Deals',$headings) && valid_array($promo_code_list)){?>
<div class="clearfix"></div>
<div class="top_airline">
   <div class="container">
      <div class="org_row">
         <div class="pagehdwrap">
            <h2 class="pagehding">Top Deals</h2>
            <span><i class="fal fa-star"></i></span>
         </div>
         <div id="all_deal" class="owl-carousel owlindex3 owl-theme" >
            <?php 
               // debug($promo_code_list);exit;
               if($promo_code_list){?>
            <?php foreach($promo_code_list as $p_key=>$p_val){
               ?>
            <?php 
               $current_date = date('Y-m-d');
               $expire_date = $p_val['expiry_date'];
               ?>
            <?php if(strtotime($current_date) < strtotime($expire_date) || $expire_date == '0000-00-00'){
               ?>
            <div class="gridItems">
               <div class="outerfullfuture">
                  <div class="thumbnail_deal thumbnail_small_img">
                     <div class="lazyOwl"></div>
                     <img class="" src="<?php echo base_url(); ?>extras/system/template_list/template_v3/images/promocode/<?php echo $p_val['promo_code_image']?>" alt="Lazy Owl Image">
                  </div>
                  <div class="caption carousel-flight-info deals_info">
                     <div class="deals_info_heading">
                        <h1><?=$p_val['description']?></h1>
                     </div>
                     <div class="deals_info_subheading">
                        <h3>Use Coupon: <?=$p_val['promo_code']?>.</h3>
                     </div>
                     <div class="deals_info_footer">
                        <div class="pull-left validDate">Valid till : <?=date('M d, Y',strtotime($p_val['expiry_date']))?></div>
                        <div class="pull-right viewLink">
                           <a class="" href="" target="_blank">View Details</a>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <?php } ?>
            <?php } ?>
            <?php } ?>
         </div>
      </div>
   </div>
</div>
<?php } ?>
<div class="clearfix"></div>
<?php 
   if(in_array( 'Top Hotel Destinations', $headings )) : ?>
<?php 
   if (in_array ( META_ACCOMODATION_COURSE, $active_domain_modules ) and valid_array ( $top_destination_hotel ) == true) : // TOP DESTINATION
   ?>
<div class="htldeals">
   <div class="">
      <div class="pagehdwrap">
         <h2 class="pagehding">Top Hotel Destinations</h2>
         <span><i class="fal fa-star"></i></span>
      </div>
      <div class="tophtls">
         <div class="grid">
            <div id="owl-demo2" class="owl-carousel owlindex2">
               <?php
                  //debug($top_destination_hotel);exit;
                  	// if (in_array ( META_ACCOMODATION_COURSE, $active_domain_modules ) and valid_array ( $top_destination_hotel ) == true) : // TOP DESTINATION
                  		foreach ( $top_destination_hotel as $tk => $tv ) :
                  			?>
               <?php if(($tk-0)%10 == 0){?>
               <div class="item">
                  <div class="col-sm-12 col-xs-12 nopad htd-wrap">
                     <div class="effect-marley figure">
                        <img
                           class="lazy lazy_loader"
                           src="<?php echo $GLOBALS['CI']->template->domain_images($tv['image']); ?>"
                           data-src="<?php echo $GLOBALS['CI']->template->domain_images($tv['image']); ?>"
                           alt="<?=$tv['city_name']?>" />
                        <div class="figcaption">
                           <div class="width_70">
                              <h3 class="clasdstntion"><?=$tv['city_name']?></h3>
                              <p>(<?=$tv['cache_hotels_count']?> Hotels)</p>
                              <input type="hidden" class="top_des_id" value="<?php echo $tv['origin']?>">
                              <input type="hidden"
                                 class="top-des-val hand-cursor"
                                 value="<?=hotel_suggestion_value($tv['city_name'], $tv['country_name'])?>">
                              <a href="#">View more</a>
                           </div>
                        </div>
                        <div class="slider-feature">
                           <ul class="hotel-feature">
                              <li>
                                 <div class="tbl-wrp">
                                    <div class="text-middle">
                                       <div class="tbl-cell">
                                          <i class="fal fa-car"></i> <span>CAR PARK</span> 
                                       </div>
                                    </div>
                                 </div>
                              </li>
                              <li>
                                 <div class="tbl-wrp">
                                    <div class="text-middle">
                                       <div class="tbl-cell">
                                          <i class="fal fa-wifi"></i> <span>INTERNET</span>
                                       </div>
                                    </div>
                                 </div>
                              </li>
                              <li>
                                 <div class="tbl-wrp">
                                    <div class="text-middle">
                                       <div class="tbl-cell">
                                          <i class="fal fa-utensils"></i> <span>BREAKFAST</span> 
                                       </div>
                                    </div>
                                 </div>
                              </li>
                              <li>
                                 <div class="tbl-wrp">
                                    <div class="text-middle">
                                       <div class="tbl-cell">
                                          <i class="fal fa-dumbbell"></i> <span>FITNESS CENTER</span> 
                                       </div>
                                    </div>
                                 </div>
                              </li>
                           </ul>
                        </div>
                     </div>
                  </div>
               </div>
               <?php } elseif (($tk-6)%10 == 0){ ?>
               <div class="item">
                  <div class="col-sm-12 col-xs-12 nopad htd-wrap">
                     <div class="effect-marley figure">
                        <img
                           class="lazy lazy_loader"
                           src="<?php echo $GLOBALS['CI']->template->domain_images($tv['image']); ?>"
                           data-src="<?php echo $GLOBALS['CI']->template->domain_images($tv['image']); ?>"
                           alt="<?=$tv['city_name']?>" />
                        <div class="figcaption">
                           <div class="width_70">
                              <h3 class="clasdstntion"><?=$tv['city_name']?></h3>
                              <p>(<?=rand(99, 500)?> Hotels)</p>
                              <input type="hidden" class="top_des_id" value="<?php echo $tv['origin']?>">
                              <input type="hidden"
                                 class="top-des-val hand-cursor"
                                 value="<?=hotel_suggestion_value($tv['city_name'], $tv['country_name'])?>">
                              <a href="#">View more</a>
                           </div>
                        </div>
                        <div class="slider-feature">
                           <ul class="hotel-feature">
                              <li>
                                 <div class="tbl-wrp">
                                    <div class="text-middle">
                                       <div class="tbl-cell">
                                          <i class="fal fa-car"></i> <span>CAR PARK</span> 
                                       </div>
                                    </div>
                                 </div>
                              </li>
                              <li>
                                 <div class="tbl-wrp">
                                    <div class="text-middle">
                                       <div class="tbl-cell">
                                          <i class="fal fa-wifi"></i> <span>INTERNET</span>
                                       </div>
                                    </div>
                                 </div>
                              </li>
                              <li>
                                 <div class="tbl-wrp">
                                    <div class="text-middle">
                                       <div class="tbl-cell">
                                          <i class="fal fa-utensils"></i> <span>BREAKFAST</span> 
                                       </div>
                                    </div>
                                 </div>
                              </li>
                              <li>
                                 <div class="tbl-wrp">
                                    <div class="text-middle">
                                       <div class="tbl-cell">
                                          <i class="fal fa-dumbbell"></i> <span>FITNESS CENTER</span> 
                                       </div>
                                    </div>
                                 </div>
                              </li>
                           </ul>
                        </div>
                     </div>
                  </div>
               </div>
               <?php } else {?>
               <div class="item">
                  <div class="col-sm-12 col-xs-12 nopad htd-wrap">
                     <div class="effect-marley figure">
                        <img
                           class="lazy lazy_loader"
                           src="<?php echo $GLOBALS['CI']->template->domain_images($tv['image']); ?>"
                           data-src="<?php echo $GLOBALS['CI']->template->domain_images($tv['image']); ?>"
                           alt="<?=$tv['city_name']?>" />
                        <div class="figcaption">
                           <div class="width_70">
                              <h3 class="clasdstntion"><?=$tv['city_name']?></h3>
                              <p>(<?=rand(99, 500)?> Hotels)</p>
                              <input type="hidden" class="top_des_id" value="<?php echo $tv['origin']?>">
                              <input type="hidden" class="top-des-val hand-cursor"
                                 value="<?=hotel_suggestion_value($tv['city_name'], $tv['country_name'])?>">
                              <a href="#">View more</a>
                           </div>
                        </div>
                        <div class="slider-feature">
                           <ul class="hotel-feature">
                              <li>
                                 <div class="tbl-wrp">
                                    <div class="text-middle">
                                       <div class="tbl-cell">
                                          <i class="fal fa-car"></i> <span>CAR PARK</span> 
                                       </div>
                                    </div>
                                 </div>
                              </li>
                              <li>
                                 <div class="tbl-wrp">
                                    <div class="text-middle">
                                       <div class="tbl-cell">
                                          <i class="fal fa-wifi"></i> <span>INTERNET</span>
                                       </div>
                                    </div>
                                 </div>
                              </li>
                              <li>
                                 <div class="tbl-wrp">
                                    <div class="text-middle">
                                       <div class="tbl-cell">
                                          <i class="fal fa-utensils"></i> <span>BREAKFAST</span> 
                                       </div>
                                    </div>
                                 </div>
                              </li>
                              <li>
                                 <div class="tbl-wrp">
                                    <div class="text-middle">
                                       <div class="tbl-cell">
                                          <i class="fal fa-dumbbell"></i> <span>FITNESS CENTER</span> 
                                       </div>
                                    </div>
                                 </div>
                              </li>
                           </ul>
                        </div>
                     </div>
                  </div>
               </div>
               <?php
                  }
                  endforeach;
                  endif; // TOP DESTINATION
                  ?>
            </div>
         </div>
      </div>
   </div>
</div>
<?php endif; ?>
<div class="clearfix"></div>
<!-- <?php 
   if(in_array( 'Perfect Holidays', $headings )) : ?>
   <div class="perhldys">
   <div class="container">
   		<div class="pagehdwrap">
   			<h2 class="pagehding">Perfect Holidays</h2>
   			<span><i class="fal fa-star"></i></span>
   		</div>
   
   		<div class="retmnus">
   		
   				<?php
      // debug($top_destination_package);exit;
      #debug($top_destination_package[0]);exit; 
      $k = 0;
      // echo "total".$total;exit;
      while ( @$total > 0 ) {
      	?>	
   				<div class="col-xs-4 nopad">
   					<div class="col-xs-12 nopad">
   						<?php for($i=1;$i<=1;$i++) { 
      // echo "total".$total;
      // echo "kk".$i.'<br/>';
      if(isset($top_destination_package[$k])){
      $package_country = $this->package_model->getCountryName($top_destination_package[$k]->package_country);
      #debug($package_country);
      #echo "sdfsdf ".($k % 2).'<br/>';
      ?>
   						<div class="topone">
   							<div class="inspd2 effect-lexi">
   								<div class="imgeht2">
   								<div class="dealimg">
   									<img
   										class="lazy lazy_loader"
   										data-src="<? echo $GLOBALS['CI']->template->domain_upload_pckg_images(basename($top_destination_package[$k]->image)); ?>"
   										alt="<?php echo $top_destination_package[$k]->package_name; ?>"
   										src="<? echo $GLOBALS['CI']->template->domain_upload_pckg_images(basename($top_destination_package[$k]->image)); ?>"
   										/>
   								</div>
   									<?php if(($k % 2) == 0) {?>
   									<div class="absint2 absintcol1 ">
   										<?php } else {?>
   										<div class="absint2 absintcol2 ">
   											<?php } ?>
   											<div class="absinn">
   												<div class="smilebig2">
   												
   													<h3><?php echo $top_destination_package[$k]->package_name; ?> </h3>
   													
   													<h4><?php echo $top_destination_package[$k]->package_city;?>, <?php echo $package_country->name; ?></h4>
   													
   												</div>
   												<div class="clearfix"></div>
   												
   											</div>
   										</div>
   									</div>
   
   									<figcaption>	
   									<div class="deal_txt">
                                          
                                          <div class="col-xs-6 nopad">
                                            <img class="star_rat" src="<?php echo $GLOBALS['CI']->template->template_images('star_rating.png')?>" alt="">
                                            <h4 class="deal_price"><span>Starting at</span> <strong> <?php echo $currency_obj->get_currency_symbol($currency_obj->to_currency); ?> </strong> <?php echo isset($top_destination_package[$k]->price)?get_converted_currency_value ( $currency_obj->force_currency_conversion ( $top_destination_package[$k]->price ) ):0; ?></h4>
                                            </div>
   
                                             <div class="col-xs-6 nopad">
                                             <h4><?php echo isset($top_destination_package[$k]->duration)?($top_destination_package[$k]->duration-1):0; ?> Nights / <?php echo isset($top_destination_package[$k]->duration)?$top_destination_package[$k]->duration:0; ?> Days</h4> 
                                          <a class="package_dets_btn" href="<?=base_url().'index.php/tours/details/'.$top_destination_package[$k]->package_id?>">
   												View details
   												</a>  
   									      </div>			
                                            </div>
                                        </figcaption>
   
   								</div>
   							</div>
   							<?php } $k++ ;	} $total = $total-1;
      ?>
   						</div>
   					</div>
   					<?php }?>
   				</div>
   		</div>
   </div>
   </div>
   <?php endif; ?> -->
<div class="topAirlineOut best_deals">
   <div class="container">
      <div class="pagehdwrap">
         <h2 class="pagehding">Best <span>Deals</span> & Offers</h2>
         <!-- <span><i class="fal fa-star"></i></span> -->
      </div>
      <div class="airlinecosmic">
         <div id="TopAirLine" class="owlindex2 owl-carousel owl-theme">
         	  <div class="item">
                        <div class="offerpart" style="line-height: 100px !important;">
                           <img src="<?php echo $GLOBALS['CI']->template->template_images('best_deals/offer1.jpg')?>" alt="" class="img-responsive">
                           <div  class="offer_text hide">
                              <p>Credit card Offer</p>
                              <h3>Earn 50.000 Bonus miles</h3>
                              <a href="#">Learn More</a>
                           </div>
                        </div>
                     </div>
                       <div class="item">
                        <div class="offerpart" style="line-height: 100px !important;">
                           <img src="<?php echo $GLOBALS['CI']->template->template_images('best_deals/offer2.jpg')?>" alt="" class="img-responsive">
                           <div  class="offer_text hide">
                              <p>Credit card Offer</p>
                              <h3>Earn 50.000 Bonus miles</h3>
                              <a href="#">Learn More</a>
                           </div>
                        </div>
                     </div>
                       <div class="item">
                        <div class="offerpart" style="line-height: 100px !important;">
                           <img src="<?php echo $GLOBALS['CI']->template->template_images('best_deals/offer3.jpg')?>" alt="" class="img-responsive">
                           <div  class="offer_text hide">
                              <p>Credit card Offer</p>
                              <h3>Earn 50.000 Bonus miles</h3>
                              <a href="#">Learn More</a>
                           </div>
                        </div>
                     </div>
                     <div class="item">
                        <div class="offerpart" style="line-height: 100px !important;">
                           <img src="<?php echo $GLOBALS['CI']->template->template_images('best_deals/offer1.jpg')?>" alt="" class="img-responsive">
                           <div  class="offer_text hide">
                              <p>Credit card Offer</p>
                              <h3>Earn 50.000 Bonus miles</h3>
                              <a href="#">Learn More</a>
                           </div>
                        </div>
                     </div>
                       <div class="item">
                        <div class="offerpart" style="line-height: 100px !important;">
                           <img src="<?php echo $GLOBALS['CI']->template->template_images('best_deals/offer2.jpg')?>" alt="" class="img-responsive">
                           <div  class="offer_text hide">
                              <p>Credit card Offer</p>
                              <h3>Earn 50.000 Bonus miles</h3>
                              <a href="#">Learn More</a>
                           </div>
                        </div>
                     </div>
                       <div class="item">
                        <div class="offerpart" style="line-height: 100px !important;">
                           <img src="<?php echo $GLOBALS['CI']->template->template_images('best_deals/offer3.jpg')?>" alt="" class="img-responsive">
                           <div  class="offer_text hide">
                              <p>Credit card Offer</p>
                              <h3>Earn 50.000 Bonus miles</h3>
                              <a href="#">Learn More</a>
                           </div>
                        </div>
                     </div>
                      
            
           
         </div>
      </div>
   </div>
</div>
<div class="clearfix"></div>
<div class="holidays">
   <div class="container ">
      <div class="holidaysbg">
         <div class="pagehdwrap">
            <h2 class="pagehding">Get the Best <span>Holidays </span>Around World</h2>
            <!-- <span><i class="fal fa-star"></i></span> -->
         </div>
         <div class="col-xs-12 nopad">
            <div class="org_row">
            <div class="col-xs-3 pad10">
               <div class="grid_image">
                  <img src="<?php echo $GLOBALS['CI']->template->template_images('best_deals/grid2.jpg')?>" alt="grid img" class="img-responsive">
                  <div class="grid_text">china</div>
               </div>
               <br>
               <div class="grid_image">
                  <img src="<?php echo $GLOBALS['CI']->template->template_images('best_deals/grid3.jpg')?>" alt="grid img" class="img-responsive">
                  <div class="grid_text">malayasia</div>
               </div>
            </div>
            <div class="col-xs-3 pad10">
               <div class="grid_image">
                  <img src="<?php echo $GLOBALS['CI']->template->template_images('best_deals/grid4.jpg')?>" alt="grid img" class="img-responsive">
                  <div class="grid_text">japan</div>
               </div>
               <br>
               <div class="grid_image">
                  <img src="<?php echo $GLOBALS['CI']->template->template_images('best_deals/grid1.jpg')?>" alt="grid img" class="img-responsive">
                  <div class="grid_text">singapore</div>
               </div>
            </div>
            <div class="col-xs-3 pad10">
               <div class="grid_image">
                  <img src="<?php echo $GLOBALS['CI']->template->template_images('best_deals/grid6.jpg')?>" alt="grid img" class="img-responsive">
                  <div class="grid_text">mexico</div>
               </div>
               <br>
               <div class="grid_image">
                  <img src="<?php echo $GLOBALS['CI']->template->template_images('best_deals/grid7.jpg')?>" alt="grid img" class="img-responsive">
                  <div class="grid_text">vietnam</div>
               </div>
            </div>
            <div class="col-xs-3 pad10">
               <div class="grid_image">
                  <img  src="<?php echo $GLOBALS['CI']->template->template_images('best_deals/grid8.jpg')?>" alt="grid img" class="img-responsive">
                  <div class="grid_text">canada</div>
               </div>
               <br>
               <div class="grid_image">
                  <img  src="<?php echo $GLOBALS['CI']->template->template_images('best_deals/grid7.jpg')?>" alt="grid img" class="img-responsive">
                  <div class="grid_text">london</div>
               </div>
            </div>
         </div>
         <!-- <div class="con_img">
            <img  src="/pace_travel/extras/system/template_list/template_v3/images/grid_bak.jpg" alt="grid img" class="img-responsive">
            </div> -->
      </div>
   </div>
   </div>
</div>
<div class="clearfix"></div>
<div class="sec_img">
   <div class="container">
      <div class="col-md-12 col-xs-12 nopad">
         <div class="col-md-4 col-sm-12 col-xs-12 f_box">
            <div class="features text-right">
                <div class="f_ico_main">
               <div class="f-text">
                  <h3>Handpicked Hotels</h3>
                  <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit</p>
               </div>
                <div class="f_ico_wt30">
               <div class="f_ico">
                  <img  src="<?php echo $GLOBALS['CI']->template->template_images('f_ico1.png')?>" alt="f_img" >
               </div>
            </div>
            </div>
            </div>
            <br>
            <div class="features text-right">
                <div class="f_ico_main">
               <div class="f-text">
                  <h3>World Class Service</h3>
                  <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit
                  </p>
               </div>
               <div class="f_ico_wt30">
               <div class="f_ico">
                    <img  src="<?php echo $GLOBALS['CI']->template->template_images('f_ico2.png')?>" alt="f_img" >
               </div>
            </div>
            </div>
            </div>
         </div>
         <div class="col-md-4 col-sm-12 col-xs-12 f_box_img">
            <div class="center_section">
               <div class="nexs_div">
                  <div id="mobCarousel" class="carousel slide mob_car" data-ride="carousel">
                     <!-- Indicators -->
                     <ol class="carousel-indicators">
                        <li data-target="#mobCarousel" data-slide-to="0" class="active"></li>
                        <li data-target="#mobCarousel" data-slide-to="1"></li>
                        <li data-target="#mobCarousel" data-slide-to="2"></li>
                     </ol>
                     <div class="carousel-inner mob_inner">
                        <div class="item active mob_item">
                           <div class="f_img">
                              <img src="<?php echo $GLOBALS['CI']->template->template_images('mob_slider1.jpg')?>" alt="images" />
                           </div>
                        </div>
                        <div class="item mob_item">
                           <div class="f_img">
                              <img src="<?php echo $GLOBALS['CI']->template->template_images('mob_slider1.jpg')?>" alt="images" />
                           </div>
                        </div>
                        <div class="item mob_item">
                           <div class="f_img">
                              <img src="<?php echo $GLOBALS['CI']->template->template_images('mob_slider1.jpg')?>" alt="images" />
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-md-4 col-sm-12 col-xs-12 f_box1">
            <div class="features text-left">
               <div class="f_ico_main">
                   <div class="f_ico_wt30">
               <div class="f_ico">
                    <img  src="<?php echo $GLOBALS['CI']->template->template_images('f_ico3.png')?>" alt="f_img" >
               </div>
            </div>
               <div class="f-text">
                  <h3>Best Price Guarantee</h3>
                  <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit
                  </p>
               </div>
            </div>
            </div>
            <br>
            <div class="features text-left">
                <div class="f_ico_main">
                   <div class="f_ico_wt30">
               <div class="f_ico">
                   <img  src="<?php echo $GLOBALS['CI']->template->template_images('f_ico4.png')?>" alt="f_img" >
               </div>
            </div>
               <div class="f-text">
                  <h3>Secure Travel</h3>
                  <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit</p>
               </div>
            </div>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="clearfix"></div>
<div class="perhldys">
   <div class="container">
      <div class="pagehdwrap">
         <h2 class="pagehding">Our the Best <span>Hotel</span> Destinations</h2>
      </div>
      <div class="retmnus">
         <div class="col-md-4 col-xs-12 col-sm-6 price_card nopad">
            <div class="col-xs-12 nopad">
               <div class="topone">
                  <div class="inspd2 effect-lexi">
                     <div class="imgeht2">
                        <div class="dealimg">
                           <img src="<?php echo $GLOBALS['CI']->template->template_images('card1.jpg')?>" alt="images" class="lazy lazy_loader"/>
                        </div>
                        <div class="price_txt">
                           <p> <strike><i class="fas fa-dollar-sign"></i> 975</strike>  <i class="fas fa-dollar-sign"></i> 550</p>
                        </div>
                        <div class="absint2 absintcol1 ">
                           <div class="absinn">
                              <div class="smilebig2">
                                 <h3>Brisbane to cairns experience</h3>
                                 <h4>Culinary,History,Nautical</h4>
                              </div>
                              <div class="clearfix"></div>
                           </div>
                        </div>
                     </div>
                     <figcaption>
                        <div class="deal_txt">
                           <div class="col-xs-6 nopad">
                              <h4 class="deal_price"><i class="far fa-calendar-alt"></i> 5 Days  4 Nights</h4>
                           </div>
                           <div class="col-xs-6 nopad">
                              <a class="package_dets_btn" href="/tours/details/141">View All</a>  
                           </div>
                        </div>
                     </figcaption>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-md-4 col-xs-12 col-sm-6 price_card nopad">
            <div class="col-xs-12 nopad">
               <div class="topone">
                  <div class="inspd2 effect-lexi">
                     <div class="imgeht2">
                        <div class="dealimg">
                           <img src="<?php echo $GLOBALS['CI']->template->template_images('card2.jpg')?>" alt="images" class="lazy lazy_loader"/>
                        </div>
                        <div class="price_txt">
                           <p> <strike><i class="fas fa-dollar-sign"></i> 975</strike>  <i class="fas fa-dollar-sign"></i> 550</p>
                        </div>
                        <div class="absint2 absintcol2 ">
                           <div class="absinn">
                              <div class="smilebig2">
                                 <h3>7 Days South -central to vietnam</h3>
                                 <h4>Culinary,History,Nautical</h4>
                              </div>
                              <div class="clearfix"></div>
                           </div>
                        </div>
                     </div>
                     <figcaption>
                        <div class="deal_txt">
                           <div class="col-xs-6 nopad">
                              <h4 class="deal_price"><i class="far fa-calendar-alt"></i> 5 Days  4 Nights</h4>
                           </div>
                           <div class="col-xs-6 nopad">
                              <a class="package_dets_btn" href="#">View All</a>  
                           </div>
                        </div>
                     </figcaption>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-md-4 col-xs-12 col-sm-6 price_card nopad">
            <div class="col-xs-12 nopad">
               <div class="topone">
                  <div class="inspd2 effect-lexi">
                     <div class="imgeht2">
                        <div class="dealimg">
                           <img src="<?php echo $GLOBALS['CI']->template->template_images('card3.jpg')?>" alt="images" class="lazy lazy_loader"/>
                        </div>
                        <div class="price_txt">
                           <p> <strike><i class="fas fa-dollar-sign"></i> 975</strike>  <i class="fas fa-dollar-sign"></i> 550</p>
                        </div>
                        <div class="absint2 absintcol1 ">
                           <div class="absinn">
                              <div class="smilebig2">
                                 <h3>Polynesian Sprit</h3>
                                 <h4>Agritourism,Nautical</h4>
                              </div>
                              <div class="clearfix"></div>
                           </div>
                        </div>
                     </div>
                     <figcaption>
                        <div class="deal_txt">
                           <div class="col-xs-6 nopad">
                              <h4 class="deal_price"><i class="far fa-calendar-alt"></i> 5 Days  4 Nights</h4>
                           </div>
                           <div class="col-xs-6 nopad">
                              <a class="package_dets_btn" href="#">View All</a>  
                           </div>
                        </div>
                     </figcaption>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-md-4 col-xs-12 col-sm-6 price_card nopad">
            <div class="col-xs-12 nopad">
               <div class="topone">
                  <div class="inspd2 effect-lexi">
                     <div class="imgeht2">
                        <div class="dealimg">
                           <img src="<?php echo $GLOBALS['CI']->template->template_images('card4.jpg')?>" alt="images" class="lazy lazy_loader"/>
                        </div>
                        <div class="price_txt">
                           <p> <strike><i class="fas fa-dollar-sign"></i> 975</strike>  <i class="fas fa-dollar-sign"></i> 550</p>
                        </div>
                        <div class="absint2 absintcol2 ">
                           <div class="absinn">
                              <div class="smilebig2">
                                 <h3>Surfers Private Charters</h3>
                                 <h4>Culinary,History,Nautical</h4>
                              </div>
                              <div class="clearfix"></div>
                           </div>
                        </div>
                     </div>
                     <figcaption>
                        <div class="deal_txt">
                           <div class="col-xs-6 nopad">
                              <h4 class="deal_price"><i class="far fa-calendar-alt"></i> 5 Days  4 Nights</h4>
                           </div>
                           <div class="col-xs-6 nopad">
                              <a class="package_dets_btn" href="#">View All</a>  
                           </div>
                        </div>
                     </figcaption>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-md-4 col-xs-12 col-sm-6 price_card nopad">
            <div class="col-xs-12 nopad">
               <div class="topone">
                  <div class="inspd2 effect-lexi">
                     <div class="imgeht2">
                        <div class="dealimg">
                           <img src="<?php echo $GLOBALS['CI']->template->template_images('card5.jpg')?>" alt="images" class="lazy lazy_loader"/>
                        </div>
                        <div class="price_txt">
                           <p> <strike><i class="fas fa-dollar-sign"></i> 975</strike>  <i class="fas fa-dollar-sign"></i> 550</p>
                        </div>
                        <div class="absint2 absintcol1 ">
                           <div class="absinn">
                              <div class="smilebig2">
                                 <h3>Japan On a Shoestring</h3>
                                 <h4>Culinary,History,Nautical</h4>
                              </div>
                              <div class="clearfix"></div>
                           </div>
                        </div>
                     </div>
                     <figcaption>
                        <div class="deal_txt">
                           <div class="col-xs-6 nopad">
                              <h4 class="deal_price"><i class="far fa-calendar-alt"></i> 5 Days  4 Nights</h4>
                           </div>
                           <div class="col-xs-6 nopad">
                              <a class="package_dets_btn" href="/tours/details/141">View All</a>  
                           </div>
                        </div>
                     </figcaption>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-md-4 col-xs-12 col-sm-6 price_card nopad">
            <div class="col-xs-12 nopad">
               <div class="topone">
                  <div class="inspd2 effect-lexi">
                     <div class="imgeht2">
                        <div class="dealimg">
                           <img src="<?php echo $GLOBALS['CI']->template->template_images('card6.jpg')?>" alt="images" class="lazy lazy_loader"/>
                        </div>
                        <div class="price_txt">
                           <p> <strike><i class="fas fa-dollar-sign"></i> 975</strike>  <i class="fas fa-dollar-sign"></i> 550</p>
                        </div>
                        <div class="absint2 absintcol2 ">
                           <div class="absinn">
                              <div class="smilebig2">
                                 <h3>Maldives Adventure Tour</h3>
                                 <h4>Culinary,History,Nautical</h4>
                              </div>
                              <div class="clearfix"></div>
                           </div>
                        </div>
                     </div>
                     <figcaption>
                        <div class="deal_txt">
                           <div class="col-xs-6 nopad">
                              <h4 class="deal_price"><i class="far fa-calendar-alt"></i> 5 Days  4 Nights</h4>
                           </div>
                           <div class="col-xs-6 nopad">
                              <a class="package_dets_btn" href="/tours/details/141">View All</a>  
                           </div>
                        </div>
                     </figcaption>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="clearfix"></div>
<div class="testimonials">
   <div class="container">
      <div class="pagehdwrap">
         <h2 class="pagehding">What <span>Client</span> Say ?</h2>
      </div>
   <div id="testimonials" class="owlindex2 owl-reponsive owl-carousel owl-theme testimonial">
              <div class="item">
                          <div class="testi_grp test1">
                              <div class="testi-icon">
                                 <i class="fa fa-quote-right"></i>
                              </div>
                              <img src="<?php echo $GLOBALS['CI']->template->template_images('testi1.jpg')?>" alt="images" />
                              <div class="testi_text">
                                 <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas sed diam eget risus varius blandit sit amet non magna. Etiam porta sem malesuada magna mollis euismod.</p>
                                 <small><strong>Vulputate M., Dolor</strong></small>
                              </div>
                           </div>
                     </div>
                       <div class="item">
                        <div class="testi_grp">
                              <div class="testi-icon">
                                 <i class="fa fa-quote-right"></i>
                              </div>
                              <img src="<?php echo $GLOBALS['CI']->template->template_images('testi2.jpg')?>" alt="images" />
                              <div class="testi_text">
                                 <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas sed diam eget risus varius blandit sit amet non magna. Etiam porta sem malesuada magna mollis euismod.</p>
                                 <small><strong>Vulputate M., Dolor</strong></small>
                              </div>
                           </div>
                     </div>
                       <div class="item">
                         <div class="testi_grp test1">
                              <div class="testi-icon">
                                 <i class="fa fa-quote-right"></i>
                              </div>
                              <img src="<?php echo $GLOBALS['CI']->template->template_images('testi1.jpg')?>" alt="images" />
                              <div class="testi_text">
                                 <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas sed diam eget risus varius blandit sit amet non magna. Etiam porta sem malesuada magna mollis euismod.</p>
                                 <small><strong>Vulputate M., Dolor</strong></small>
                              </div>
                           </div>
                     </div>
                     <div class="item">
                         <div class="testi_grp">
                              <div class="testi-icon">
                                 <i class="fa fa-quote-right"></i>
                              </div>
                              <img src="<?php echo $GLOBALS['CI']->template->template_images('testi2.jpg')?>" alt="images" />
                              <div class="testi_text">
                                 <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas sed diam eget risus varius blandit sit amet non magna. Etiam porta sem malesuada magna mollis euismod. </p>
                                 <small><strong>Vulputate M., Dolor</strong></small>
                              </div>
                           </div>
                     </div>
                      
            
           
         </div>
   </div>
</div>
<div class="clearfix"></div>
<?=$this->template->isolated_view('share/js/lazy_loader')?>
 <script>
   $(document).ready(function() {
     $("#TopAirLine").owlCarousel({
       items:3,
       itemsDesktop: [1000, 3],
       itemsDesktopSmall: [900, 3],
       itemsTablet: [600,2],
       loop:true,
       margin:10,
       autoplay:true,
       navigation: true,
       pagination: false,
       autoplayTimeout:1000,
       autoplayHoverPause:true
   });
    //    $("#testimonials").owlCarousel({
    //     items:2,
    //     loop:true,
    //     margin:10,
    //     autoplay:true,
    //     navigation: true,
    //     pagination: false,
    //    autoplayTimeout:1000,
    //    autoplayHoverPause:true
    // });
  
       
   });
</script> 
<script type="text/javascript">
      $(document).ready(function() {
     var owl = $("#testimonials");
     owl.owlCarousel({
       items: 2,
       itemsDesktop: [1000, 2],
       itemsDesktopSmall: [900, 2],
       itemsTablet: [600, 1],
       itemsMobile: false,
       navigation: true,
       pagination: false
     });
    
   });
</script>
<!-- <script type="text/javascript">
      $(document).ready(function() {
     var owl = $("#TopAirLine");
     owl.owlCarousel({
       items: 3,
       itemsDesktop: [1000, 3],
       itemsDesktopSmall: [900, 3],
       itemsTablet: [600, 1],
       itemsMobile: false,
       navigation: true,
       pagination: false
     });
    
   });
</script> -->

<style type="text/css">
   span.remngwd {
      position: unset;
   }
   .alladvnce {
       background: transparent;
       border-bottom: none;
       border: 1px solid #00a1ff;
       color: #000;
       width: 100%;
   }
   .roomrow .btn {
       border: 1px solid #00a1ff !important;
       background: #00a1ff !important;
   }
   .roomrow .input-number{
      height: 32px;
   }
   /*.log_bg {
      border:1px solid #ccc;
   }*/
   /*.topssec{
      box-shadow: 1px 1px 5px #ccc!important;
   }*/
   .searcharea.srching{
      margin: 0;
   }
   /*.logo_img {
      margin: 0;
   }*/

   .allformst{
      position: relative;
      top: -50px;
      margin: 0;
   }
   .elipbord {
      margin: 0;
      top: -60px;
   }
   .allpagewrp{
      background-color: #fff;
   }
   /*.fstfooter {
       background-color: #253040;
   }*/
   /*.copyrit {
      background: #151d29!important;
   }*/
</style>