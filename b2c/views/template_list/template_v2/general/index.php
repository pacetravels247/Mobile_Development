<link href="<?php echo $GLOBALS['CI']->template->template_css_dir('index_style.css'); ?>" rel="stylesheet">
<?php
$active_domain_modules = $this->active_domain_modules;
$default_active_tab = $default_view;
//echo "<pre>"; print_r($holiday_data);exit;

/**
 * set default active tab
 * @param string $module_name		 name of current module being output
 * @param string $default_active_tab default tab name if already its selected otherwise its empty
 */
function set_default_active_tab($module_name, &$default_active_tab)
{
	if (empty($default_active_tab) == true || $module_name == $default_active_tab) {
		if (empty($default_active_tab) == true) {
			$default_active_tab = $module_name; // Set default module as current active module
		}
		return 'active';
	}
}
Js_Loader::$css[] = array('href' => $GLOBALS['CI']->template->template_css_dir('owl.carousel.min.css'), 'media' => 'screen');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('owl.carousel.min.js'), 'defer' => 'defer');
?>

<link href="<?php echo $GLOBALS['CI']->template->template_css_dir('jquery-ui.css'); ?>" rel="stylesheet">
<link href="<?php echo $GLOBALS['CI']->template->template_css_dir('animate.css'); ?>" rel="stylesheet">
<script src="<?php echo JAVASCRIPT_LIBRARY_DIR.'wow.min.js'; ?>" defer ></script>

<section class="banner_outer">
  <div class="container pos_rel">
    <div class="banner_content">
      <div class="text_slider wow fadeInDown">
        <div id="banner_text" class="owl-carousel owl-theme">
          <div class="item">
            <h1>GET INSPIRED FOR YOUR NEXT VACATION WITH <?=domain_name()?></h1>
          </div>
          <div class="item">
            <h1>GET INSPIRED FOR YOUR NEXT VACATION WITH <?=domain_name()?></h1>
          </div>
          <div class="item">
            <h1>GET INSPIRED FOR YOUR NEXT VACATION WITH <?=domain_name()?></h1>
          </div>
        </div>
      </div>
      <div class="col-md-12 nopad search-engine">
        <ul class="nav nav-tabs col-md-6 nopad mar_auto">
            
          <?php if (in_array(META_AIRLINE_COURSE, $active_domain_modules)) { ?>
          <li class="<?php echo set_default_active_tab(META_AIRLINE_COURSE, $default_active_tab)?>"><a data-toggle="tab" href="#flight_tab">
          <img src="<?php echo $GLOBALS['CI']->template->template_images('icons/flight-icon.png'); ?>" alt="Flight Icon"><span> Flight</span></a></li>
          <?php }  ?>
          <?php if (in_array(META_ACCOMODATION_COURSE, $active_domain_modules)) { ?>  
          <li class="<?php echo set_default_active_tab(META_ACCOMODATION_COURSE, $default_active_tab)?>"><a data-toggle="tab" href="#hotel_tab">
          <img src="<?php echo $GLOBALS['CI']->template->template_images('icons/hotel-icon.png'); ?>" alt="Hotel Icon"><span> Hotel</span></a></li>
          <?php  }  ?>
          <?php if (in_array(META_PACKAGE_COURSE, $active_domain_modules)) { ?>
          <li  class="<?php echo set_default_active_tab(META_PACKAGE_COURSE, $default_active_tab)?>"><a data-toggle="tab" href="#sight_tab">
          <img src="<?php echo $GLOBALS['CI']->template->template_images('icons/holiday-icon.png'); ?>" alt="sight Icon"><span> Sightseeing</span></a></li>
          <?php } ?>
          <?php if (in_array(META_BUS_COURSE, $active_domain_modules)) { ?>
          <li class="<?php echo set_default_active_tab(META_BUS_COURSE, $default_active_tab)?>"><a data-toggle="tab" href="#bus_tab">
          <img src="<?php echo $GLOBALS['CI']->template->template_images('icons/bus-icon.png'); ?>" alt="bus Icon"><span> Bus</span></a></li>
          <?php } ?>
          
          
        </ul>
        <div class="tab-content">
          <div id="hotel_tab" class="tab-pane fade in <?php echo set_default_active_tab(META_ACCOMODATION_COURSE, $default_active_tab)?>">
            <div class="tab-pane">
              <?php if (in_array(META_ACCOMODATION_COURSE, $active_domain_modules)) { ?>
      
       <?php echo $GLOBALS['CI']->template->isolated_view('share/hotel_search') ?>
     
     <?php } ?>
            </div>
          </div>
          <div id="flight_tab" class="tab-pane fade in <?php echo set_default_active_tab(META_AIRLINE_COURSE, $default_active_tab)?>">
              <div class="tab-pane">
              <?php if (in_array(META_AIRLINE_COURSE, $active_domain_modules)) { ?>
     
        <?php echo $GLOBALS['CI']->template->isolated_view('share/flight_search') ?>

      <?php } ?>    
              </div>
              
          </div>
          <div id="sight_tab" class="tab-pane fade in <?php echo set_default_active_tab(META_PACKAGE_COURSE, $default_active_tab)?>">
            <div class="col-md-12 nopad">
                <div class="tab-pane">
                    <?php if (in_array(META_PACKAGE_COURSE, $active_domain_modules)) { ?>
     
       <?php echo $GLOBALS['CI']->template->isolated_view('share/holiday_search',$holiday_data) ?>
     
     <?php } ?>
                </div>
            </div>
          </div>
          
          <div id="bus_tab" class="tab-pane fade in <?php echo set_default_active_tab(META_BUS_COURSE, $default_active_tab)?>">
            <div class="col-md-12 noPL noPR">
                <div class="tab-pane">
                    <?php if (in_array(META_BUS_COURSE, $active_domain_modules)) { ?>
    
     	<?php echo $GLOBALS['CI']->template->isolated_view('share/bus_search',$holiday_data) ?>
    
     <?php } ?>
                </div>
            </div>
            
            
            
          </div>
          
        </div>
      </div>
      <div class="clearfix"></div>
    </div>
    
    
  </div>
</section>
<div class="clearfix"></div>

<section class="advc_section">
	<div class="container">
    	<!--<h2>Advice For Everyone</h2>
        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</p>
    --><div class="servc_boxes">
    	<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        	<i class="sprite rtslct_icn wow flipOutY"></i>
            <h3>Best Price Guarantee</h3>
            <p>We at <strong><?=domain_name()?></strong> are committed to provide you with the best fares in the market.</p>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        	<i class="sprite widechc_icn wow flipOutY"></i>
            <h3>Customer Satisfaction</h3>
            <p>100% Customer Satisfaction.</p>
        </div>
        <div class="clearfix visible-sm"></div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        	<i class="sprite suprt_icn wow flipOutY"></i>
            <h3>Why Chose Us</h3>
            <p>Get the best deals in flight specially to the Indian sub continent. With a strong hold in India, get the best fares for the flights within India as well.</p>
        </div>
        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        	<i class="sprite buytckt_icn wow flipOutY"></i>
            <h3>Need Help?</h3>
            <p>Decicated customer service 24/7 to help you with your travel requirements.</p>
        </div>
      </div> 
	</div>
</section>
<section class="top_dest_sectn">
	<div class="container">
    	<h4>Top Destinations</h4>
        <div class="clearfix"></div>
        <div class="top_dest_box">
			<?php if (in_array(META_ACCOMODATION_COURSE, $active_domain_modules) AND valid_array($top_destination_hotel) == true) : //TOP DESTINATION
				foreach ($top_destination_hotel as $tk => $tv) :?>
				<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 htd-wrap">
	            	<div class="dest_list_box">
		            	<figure class="dest_img_box">
		               	 <img src="<?php echo $GLOBALS['CI']->template->domain_images($tv['image']); ?>" alt="<?=$tv['destination']?>"/>
		                 	<i class="fa fa-plus-square-o dest_img_over"></i>
		                </figure>
		                <figcaption>
		               	  <h3><?=$tv['destination']?></h3>
		                    <p>(<?=rand(99, 500)?> Hotels)</p>
		                    <input type="hidden" class="top-des-val hand-cursor" value="<?=hotel_suggestion_value($tv['destination'], $tv['country'])?>">
		                  <button class="btn topd_btn"><i class="fa fa-chevron-right"></i></button>
		              </figcaption>
		          </div>
	          </div>
			<?php
				endforeach;
			endif; //TOP DESTINATION ?>
          <div class="clearfix"></div>
        </div>
	</div>
</section>
<!-- end explore more -->

<section class="newltr_section">
	<div class="container">
    	<div class="col-lg-8 col-md-8 col-sm-7 col-xs-12 nwsltr_outr">
        	<div class="col-lg-9"><input id="newsemail" type="email" class="form-control input-lg" placeholder="Enter your email..."></div>
            <div class="col-lg-3"><input id="newsemailbtn" type="submit" class="btn btn-primary btn-lg" value="Submit"></div>
        </div>
     
        <div class="col-lg-4 col-md-4 col-sm-5 col-xs-12 socialmedia_outr">
        	<ul class="list-unstyled socialmedia_icns">
            	   <?php 
				$temp=$this->custom_db->single_table_records('social_links');
				if ($temp['data']['0']['status'] == ACTIVE) {?>
            	<li><a href="<?php echo $temp['data']['0']['url_link'];?>"><i class="sprite fb_icn wow flipOutY"></i></a></li>
            	<?php } ?>
            	<?php if ($temp['data']['1']['status'] == ACTIVE) {?>
            	<li><a href="<?php echo $temp['data']['1']['url_link'];?>"><i class="sprite twtr_icn wow flipOutY"></i></a></li>
            	<?php } ?>
            	<?php if ($temp['data']['2']['status'] == ACTIVE) {?>
            	<li><a href="<?php echo $temp['data']['2']['url_link'];?>"><i class="sprite gpls_icn wow flipOutY"></i></a></li>
            	<?php } ?>
                <?php if ($temp['data']['3']['status'] == ACTIVE) {?>
                <li><a href="<?php echo $temp['data']['3']['url_link'];?>"><i class="sprite ytb_icn wow flipOutY"></i></a></li>
                <?php } ?>
           </ul>
        </div>
    </div>
</section>


<link rel="stylesheet" href="<?php echo  $GLOBALS['CI']->template->template_css_dir('backslider.css'); ?>" />
<!--<link rel="stylesheet" href="?php echo  $GLOBALS['CI']->template->template_css_dir('backslider2.css');?>" />
-->
<script src="<?php echo $GLOBALS['CI']->template->template_js_dir('backslider.js'); ?>"></script>
<script>
  //homepage slide show
$(function($){
	new WOW().init();
	
	//all scripts loaded
	var url = '<?=$GLOBALS['CI']->template->template_images(); ?>';
	$.supersized({
        
          // Functionality
          slide_interval          :   5000,   // Length between transitions
          transition              :   1,      // 0-None, 1-Fade, 2-Slide Top, 3-Slide Right, 4-Slide Bottom, 5-Slide Left, 6-Carousel Right, 7-Carousel Left
          transition_speed    : 700,    // Speed of transition
                                 
          // Components             
          slide_links       : 'blank',  // Individual links for each slide (Options: false, 'num', 'name', 'blank')
          slides :    [{"image":"<?php echo $GLOBALS['CI']->template->template_images('banner_1.png'); ?>"}, {"image":"<?php echo $GLOBALS['CI']->template->template_images('banner_2.png'); ?>"} ]          
        });
});

//homepage slide show end

$(document).ready(function() {

	
	$("#banner_text").owlCarousel({
		navigation : false, // Show next and prev buttons
		slideSpeed : 300,
		paginationSpeed : 400,
		singleItem:true
	});
	$("#trvl_drop_btn").click(function(){
		$("#trvl_drop_drp").html($("#trvl_drop_drp").text() == "-" ? "+" : "-");
		$("#trvl_drop_drp_cnts").slideToggle(400);
	});

	$("#onew-trp").click(function(){
		$(".rndw-outer").hide();
		$(".mutiw-outer").hide();
		$(".onw-outer").show();		
		$( "#onew-trp" ).addClass( "active" );
		$( "#mutl-trp" ).removeClass( "active" );
		$( "#rnd-trp" ).removeClass( "active" );
	});

	$("#rnd-trp").click(function(){
		$(".rndw-outer").show();
		$(".mutiw-outer").hide();
		$(".onw-outer").hide();
		$( "#onew-trp" ).removeClass( "active" );
		$( "#mutl-trp" ).removeClass( "active" );
		$( "#rnd-trp" ).addClass( "active" );
	});

	$("#mutl-trp").click(function(){
		$(".rndw-outer").hide();
		$(".mutiw-outer").show();
		$(".onw-outer").hide();
		$( "#onew-trp" ).removeClass( "active" );
		$( "#mutl-trp" ).addClass( "active" );
		$( "#rnd-trp" ).removeClass( "active" );
	});

	$("#intrn-src").click(function(){
		$( "#intrn-src" ).addClass( "active" );
		$( "#dmstc-src" ).removeClass( "active" );
	});

	$("#dmstc-src").click(function(){
		$( "#dmstc-src" ).addClass( "active" );
		$( "#intrn-src" ).removeClass( "active" );
	});

	//Top Destination Functionality
	$('.htd-wrap').on('click', function(e) {
		e.preventDefault();
		var curr_destination = $('.top-des-val', this).val();
		var check_in = "<?=add_days_to_date(7)?>";
		var check_out = "<?=add_days_to_date(10)?>";

		$('#hotel_destination_search_name').val(curr_destination);
		$('#hotel_checkin').val(check_in);
		$('#hotel_checkout').val(check_out);
		$('#hotel_search').submit();
	});
});
</script>
