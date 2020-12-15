<?php
Js_Loader::$css[] = array('href' => $GLOBALS['CI']->template->template_css_dir('owl.carousel.min.css'), 'media' => 'screen');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('owl.carousel.min.js'), 'defer' => 'defer');
?>
<link  href="<?php echo $GLOBALS['CI']->template->template_css_dir('custom_tour.css') ?>" rel="stylesheet">
<link  href="<?php echo $GLOBALS['CI']->template->template_css_dir('custom_sky.css') ?>" rel="stylesheet">
<script src="<?php echo $GLOBALS['CI']->template->template_js_dir('initialize-carousel-detailspage.js') ?>" type="text/javascript"></script> 
<script src="<?php echo $GLOBALS['CI']->template->template_js_dir('jquery.provabpopup.js') ?>" type="text/javascript"></script>
<script src="<?php echo $GLOBALS['CI']->template->template_js_dir('jquery.carouFredSel-6.2.1-packed.js') ?>" type="text/javascript"></script>

<div class="full witcontent  marintopcnt">
  <div class="container">
    	<div class="full">
        <div class="contentpad">

            <div class="fulldetab">

                <div class="col-md-4 fulatnine frtrit">
                <div class="alsidex">
                    <div class="sidebfs">
                        <div id="owl-demobaner" class="owl-carousel indexbaner">
                        <?php if(!empty($package)){ ?>
                           <div class="item">
                                <div class="smalbanr"><img src="<?php echo $GLOBALS['CI']->template->domain_images($package->image); ?>" alt="" /></div>
                          </div>
                        <?php } ?>
                        </div>
                    </div>

                    <div class="clear"></div>

                    <div class="siddwn">
                        <div class="col-xs-6">
                            <div class="bigpriced"> <?php echo isset($package->price)? $package->price:0; ?> Only</div>
                            <span class="snote">*Per Person</span>
                            <div class="sdfr"><?php echo isset($package->duration)?($package->duration-1):0; ?> Nights / <?php echo isset($package->duration)?$package->duration:0; ?> Days</div>
                        </div>
                        <div class="col-xs-6">
                            <input type="submit" class="booknowhtl" id="sendquery" value="Send Query" style="width: 150px;" />
                        </div>
                    </div>
                 </div>

                  
                     <div class="rating">
                         <label>Rate this package</label>
                        <span><input type="radio" name="rating" id="str5" value="5"><label for="str5"></label></span>
                        <span><input type="radio" name="rating" id="str4" value="4"><label for="str4"></label></span>
                        <span><input type="radio" name="rating" id="str3" value="3"><label for="str3"></label></span>
                        <span><input type="radio" name="rating" id="str2" value="2"><label for="str2"></label></span>
                        <span><input type="radio" name="rating" id="str1" value="1"><label for="str1"></label></span>
                     </div>
                    <input type="hidden" id="pkg_id" value="<?php echo $package->package_id?>">
                   
                    <div id="msg_pak"></div>


                </div>

                <div class="col-md-8 nopad fulatnine">
                    <div class="detailtab">
                        <ul class="nav nav-tabs trul">
                          <li class=""><a href="#ovrvw" data-toggle="tab">OverView</a></li>
                          <li class="active trooms"><a href="#itnery" data-toggle="tab">Detailed Itinerary</a></li>
                          <li class="tfacility"><a href="#tandc" data-toggle="tab">Terms & Conditions</a></li>
                          <li><a href="#gallery" data-toggle="tab">Gallery</a></li>
                           <li><a href="#rating" data-toggle="tab">Rating</a></li>
                        </ul>
                        <div class="tab-content5">

                       <!-- Over View-->
                        <div class="tab-pane" id="ovrvw">
                        	<div class="innertabsxl">
                            	<div class="comenhtlsum">
                                	<?php echo isset($package->package_description)?($package->package_description):"No Description"; ?>
                                </div>
                                <div class="linebrk"></div>

                            </div>
                        </div>
                        <!-- Over View End-->

                        <!-- Itinerary-->
                        <div class="tab-pane active" id="itnery">
                        	<div class="innertabsxl">
                            <?php $i=1; ?>
                            <?php foreach($package_itinerary as $pi){ ?>
                            <div class="htlrumrowxl">
                            	<div class="hotelistrowhtl">

									<div class="daytrip">
                                        	<strong>Day</strong>
                                            <b><?php echo $i; ?></b>
                                     </div>

                                    <div class="clear"></div>
                                    <div class="dayecd">
                                        <div class="hotelhed"><?php echo $pi->place; ?></div>
                                        <span class="singleadrspara"><?php echo $pi->itinerary_description; ?></span>
                                    </div>

                                </div>

                            </div>
                            <?php $i++; ?>
                            <?php } ?>
                           </div>
                        </div>
                        <!-- Itinerary End-->

                        <!-- Terms & Conditions-->
                        <div class="tab-pane" id="tandc">
                        	<div class="innertabs">
                            	<div class="comenhtlsum">
                                	<?php echo isset($package->package_description)?($package->package_description):"No Description"; ?>
                                </div>
                                <div class="linebrk"></div>
								<div class="col-md-12 nopad">
                                	<div class="col-md-12">
                                    <h3 class="hedft">Price Includes: </h3>
									<ul class="checklist checklistxl">
										<li><?php echo isset($package_price_policy->price_includes)?($package_price_policy->price_includes):"No Description"; ?></li>
									</ul>
								</div>


                                </div>
                                <div class="linebrk"></div>
								<div class="col-md-12 nopad">
                                	<div class="col-md-12">
                                    <h3 class="hedft">Price Excludes: </h3>
									<ul class="checklist checklistxl">
										<li><?php echo isset($package_price_policy->price_excludes)?($package_price_policy->price_excludes):""; ?></li>
									</ul>
								</div>


                                </div>
                                <div class="linebrk"></div>
								<div class="col-md-12 nopad">
                                	<div class="col-md-12">
                                    <h3 class="hedft">Cancellation Advance: </h3>
									<ul class="checklist checklistxl">
										<li><?php echo isset($package_cancel_policy->cancellation_advance)?($package_cancel_policy->cancellation_advance):""; ?></li>
									</ul>
								</div>
                                <div class="linebrk"></div>
                                <div class="col-md-12 nopad">
                                    <div class="col-md-12">
                                    <h3 class="hedft">Cancellation penality: </h3>
                                    <ul class="checklist checklistxl">
                                        <li><?php echo isset($package_cancel_policy->cancellation_penality)?($package_cancel_policy->cancellation_penality):""; ?></li>
                                    </ul>
                                </div>

								</div>
                                </div>
                            </div>
                        </div>
                        <!-- Terms & Conditions End-->

                        <!-- Map-->
                        <div class="tab-pane" id="gallery">
                        	<div class="innertabs">

                                <div id="owl-demobaner1" class="owl-carousel indexbaner">
                                <?php if(!empty($package_traveller_photos)){ ?>
                                    <?php foreach($package_traveller_photos as $ptp){ ?>
                                   <div class="item">
                                        <div class="xlimg"><img src="<?php echo $GLOBALS['CI']->template->domain_images($ptp->traveller_image); ?>" alt="" /></div>
                                  </div>
                                  <?php } ?>
                                <?php } ?>
                                </div>
							</div>
                        </div>
                        <!-- Map End-->

                        <!-- Reviews-->
                        <div class="tab-pane" id="rating">
                        	<div class="innertabs">
                            	<div class="ratingusr">
                                	<strong><?php echo $package->rating; ?></strong>
                                    <span class="ratingimg">Star<!-- <img src="/images/user-rating-<?php echo $package->rating; ?>.png" alt="" /> --></span>
                                    <b>User Rating</b>
                                </div>
                            </div>
                        </div>
                        <!-- Reviews End-->

                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
    </div>
</div>

<div id="subtqry" class="wellme minwnwidth" style="display: none;">
    <div class="popuperror" style="display:none;"></div>
    <div  class="pophed"> Send Enquiry For <?php echo $package->package_name; ?></div>
    <div class="signdiv">
      <form action="<?php echo base_url(); ?>index.php/tours/enquiry" method="post">
        <input type="hidden" class="fulwishxl" id="package_id"  name="package_id" value="<?php echo $package->package_id; ?>"/>
        <div class="rowlistwish">
            <div class="col-md-4">
            	<span class="qrylbl">Name<span class="dfman">*</span></span>
            </div>
            <div class="col-md-8">
            	<input type="text" class="fulwishxl" id="first_name"  name="first_name" required/>
            	<span id="verificationCodeErr" style="color:red; font-size: small"></span>
            </div>
        </div>
        <div class="rowlistwish">
            <div class="col-md-4">
            	<span class="qrylbl"> Contact Number<span class="dfman">*</span> </span>
            </div>
            <div class="col-md-8">
            	<input type="text" class="fulwishxl" id="phone" name="phone" required/>
            	<span id="verificationCodeErr" style="color:red; font-size: small"></span>
            </div>
        </div>
        <div class="rowlistwish">
            <div class="col-md-4">
            	<span class="qrylbl"> Email<span class="dfman">*</span> </span>
            </div>
            <div class="col-md-8">
            	<input type="text" class="fulwishxl" id="email" name="email" required/>
            	<span id="verificationCodeErr" style="color:red; font-size: small"></span>
            </div>
        </div>
        <div class="rowlistwish">
            <div class="col-md-4">
            	<span class="qrylbl">Departure Place<span class="dfman">*</span> </span>
            </div>
            <div class="col-md-8">
            	<input type="text" class="fulwishxl" id="place" name="place" required/>
            	<span id="verificationCodeErr" style="color:red; font-size: small"></span>
            </div>
        </div>
        <div class="rowlistwish">
            <div class="col-md-4">
            	<span class="qrylbl">Message<span class="dfman">*</span> </span>
            </div>
            <div class="col-md-8">
            	<input type="text" class="fulwishxl" id="message" name="message" required/>
            	<span id="verificationCodeErr" style="color:red; font-size: small"></span>
            </div>
        </div>


      <div class="clear"></div>
      <div class="downselfom">
        <input type="cancel"  value="Cancel" class="savewish colorcancel closequery" id="hideCancelPopup" />
        <input type="submit"  value="Send Enquiry" class="savewish colorsave" id="startCancelProc"/>
      </div>
      </form>
    </div>
</div>



<!--<script src="<?php echo $GLOBALS['CI']->template->template_js_dir('jquery.masonry.min.js') ?>" type="text/javascript"></script> -->
<script type="text/javascript">
$(document).ready(function(){
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

});


</script>

<script type="text/javascript">
$(document).ready(function(){
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
            url:WEB_URL+'/tours/package_user_rating',
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
});
</script>



</body></html>