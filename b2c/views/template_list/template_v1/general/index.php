<?php
$image = $banner_images['data'][0]['image'];
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
?>
<!-- main -->
<main class="main-main header" id="top" style="background-image: url('<?=$GLOBALS['CI']->template->domain_images().$image?>');">
	<div class="container theme1banner">
		<hgroup class="mobHide text-center clearfix" style="width:100%;">
			<h1 class="text-uppercase">Live the great experience</h1>
			<h2>Flat 30% cash back</h2>
		</hgroup>
		<div role="tabpanel" style="width:100% !important;">
			<div class="row">
				<div class="col-lg-6 col-lg-offset-3">
					<!-- Nav pills -->
					<ul class="nav nav-pills nav-justified theme2SrhUl" role="tablist">
						<?php if (in_array(META_AIRLINE_COURSE, $active_domain_modules)) { ?>
           <li role="presentation" class="<?php echo set_default_active_tab(META_AIRLINE_COURSE, $default_active_tab)?>"><a href="#flight" aria-controls="flight" role="pill" data-toggle="pill"><img src="<?php echo $GLOBALS['CI']->template->template_images('icons/flight-icon.png'); ?>" alt="Flight Icon"> <span class="hidden-xs">Flight</span></a></li>
           <?php } ?>
           <?php if (in_array(META_ACCOMODATION_COURSE, $active_domain_modules)) { ?>
           <li role="presentation" class="<?php echo set_default_active_tab(META_ACCOMODATION_COURSE, $default_active_tab)?>"><a href="#hotel" aria-controls="hotel" role="pill" data-toggle="pill"><img src="<?php echo $GLOBALS['CI']->template->template_images('icons/hotel-icon.png'); ?>" alt="Hotel Icon"> <span class="hidden-xs">Hotel</span></a></li>
           <?php } ?>
           <?php if (in_array(META_BUS_COURSE, $active_domain_modules)) { ?>
           <li role="presentation" class="<?php echo set_default_active_tab(META_BUS_COURSE, $default_active_tab)?>"><a href="#bus" aria-controls="bus" role="pill" data-toggle="pill"><img src="<?php echo $GLOBALS['CI']->template->template_images('icons/bus-icon.png'); ?>" alt="Bus Icon"> <span class="hidden-xs">Bus</span></a></li>
           <?php } ?>
           <?php if (in_array(META_PACKAGE_COURSE, $active_domain_modules)) { ?>
           <li role="presentation" class="<?php echo set_default_active_tab(META_PACKAGE_COURSE, $default_active_tab)?>"><a href="#holiday" aria-controls="holiday" role="pill" data-toggle="pill"><img src="<?php echo $GLOBALS['CI']->template->template_images('icons/holiday-icon.png'); ?>" alt="Holiday Icon"> <span class="hidden-xs">Holiday</span></a></li>
           <?php } ?>
         </ul>
       </div>
     </div>
     <!-- Tab panes -->
     <div class="tab-content highlight">
      <?php if (in_array(META_AIRLINE_COURSE, $active_domain_modules)) { ?>
      <div role="tabpanel" class="tab-pane fade in <?php echo set_default_active_tab(META_AIRLINE_COURSE, $default_active_tab)?>" id="flight">
        <?php echo $GLOBALS['CI']->template->isolated_view('share/flight_search') ?>
      </div>
      <?php } ?>
      <?php if (in_array(META_ACCOMODATION_COURSE, $active_domain_modules)) { ?>
      <div role="tabpanel" class="tab-pane fade in <?php echo set_default_active_tab(META_ACCOMODATION_COURSE, $default_active_tab)?>" id="hotel">
       <?php echo $GLOBALS['CI']->template->isolated_view('share/hotel_search') ?>
     </div>
     <?php } ?>
     <?php if (in_array(META_PACKAGE_COURSE, $active_domain_modules)) { ?>
     <div role="tabpanel" class="tab-pane fade in <?php echo set_default_active_tab(META_PACKAGE_COURSE, $default_active_tab)?>" id="holiday">
       <?php echo $GLOBALS['CI']->template->isolated_view('share/holiday_search',$holiday_data) ?>
     </div>
     <?php } ?>
     <?php if (in_array(META_BUS_COURSE, $active_domain_modules)) { ?>
     <div role="tabpanel" class="tab-pane fade in <?php echo set_default_active_tab(META_BUS_COURSE, $default_active_tab)?>" id="bus">
     	<?php echo $GLOBALS['CI']->template->isolated_view('share/bus_search',$holiday_data) ?>
     </div>
     <?php } ?>
   </div>
 </div>
</div>
</main>
<!-- end main -->

<!-- recommended hotels -->
<?php if (in_array(META_ACCOMODATION_COURSE, $active_domain_modules)) { ?>
<section class="home-hotel">
	<div class="heading-highlight text-center">
		<h2 class="i-b text-uppercase h4">Recommended Hotels</h2>
	</div>
	<div class="highlight-section">
		<div class="container">
			<div class="row">
				<div class="col-lg-3 col-sm-6 col-md-4">
					<img src="http://placehold.it/500x400" alt="Hotel" class="img-responsive center-block">
					<div class="thumbnail b-r-0">
						<div class="caption txtwrapRow">
							<div class="clearfix">
								<div class="pull-left">
									<h3 class="h4 m-0 text-success name">Hotel in USA</h3>
									<p class="text-capitalize m-0"><small>Paris France</small></p>
								</div>
								<div class="pull-right text-right">
									<p class="text-uppercase m-0"><small>Avg/Night</small></p>
									<p class="text-warning h4 m-0 price">
										<strong><i class="fa fa-usd fa-fw"></i>100</strong>
									</p>
								</div>
							</div>
						</div>
						<hr class="m-0">
						<div class="caption">
							<div class="clearfix">
								<div class="pull-left">
									<span class="rating">
										<span class="star"></span>
										<span class="star"></span>
										<span class="star"></span>
										<span class="star"></span>
										<span class="star"></span>
									</span>
								</div>
								<div class="pull-right text-uppercase">
									<span><small>270 Reviews</small></span>
								</div>
							</div>
						</div>
						<hr class="m-0">
						<div class="caption txtwrapRow txtHighLite">
							<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Qui animi neque suscipit provident accusamus ipsum cupiditate deleniti, illum.
							</p>
							<div class="clearfix">
								<div class="pull-left">
									<a href="#" class="btn btn-sm btn-i b-r-0" role="button">Select</a>
								</div>
								<div class="pull-right">
									<a href="#" class="btn btn-sm btn-p b-r-0" role="button">View</a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3 col-sm-6 col-md-4">
					<img src="http://placehold.it/500x400" alt="Hotel" class="img-responsive center-block">
					<div class="thumbnail b-r-0">
						<div class="caption">
							<div class="clearfix">
								<div class="pull-left">
									<h3 class="h4 m-0 text-success name">Hotel in USA</h3>
									<p class="text-capitalize m-0"><small>Paris France</small></p>
								</div>
								<div class="pull-right text-right">
									<p class="text-uppercase m-0"><small>Avg/Night</small></p>
									<p class="text-warning h4 m-0 price">
										<strong><i class="fa fa-usd fa-fw"></i>100</strong>
									</p>
								</div>
							</div>
						</div>
						<hr class="m-0">
						<div class="caption">
							<div class="clearfix">
								<div class="pull-left">
									<span class="rating">
										<span class="star"></span>
										<span class="star"></span>
										<span class="star"></span>
										<span class="star"></span>
										<span class="star"></span>
									</span>
								</div>
								<div class="pull-right text-uppercase">
									<span><small>270 Reviews</small></span>
								</div>
							</div>
						</div>
						<hr class="m-0">
						<div class="caption txtwrapRow txtHighLite">
							<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Qui animi neque suscipit provident accusamus ipsum cupiditate deleniti, illum.
							</p>
							<div class="clearfix">
								<div class="pull-left">
									<a href="#" class="btn btn-sm btn-i b-r-0" role="button">Select</a>
								</div>
								<div class="pull-right">
									<a href="#" class="btn btn-sm btn-p b-r-0" role="button">View</a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3 col-sm-6 col-md-4">
					<img src="http://placehold.it/500x400" alt="Hotel" class="img-responsive center-block">
					<div class="thumbnail b-r-0">
						<div class="caption">
							<div class="clearfix">
								<div class="pull-left">
									<h3 class="h4 m-0 text-success name">Hotel in USA</h3>
									<p class="text-capitalize m-0"><small>Paris France</small></p>
								</div>
								<div class="pull-right text-right">
									<p class="text-uppercase m-0"><small>Avg/Night</small></p>
									<p class="text-warning h4 m-0 price">
										<strong><i class="fa fa-usd fa-fw"></i>100</strong>
									</p>
								</div>
							</div>
						</div>
						<hr class="m-0">
						<div class="caption">
							<div class="clearfix">
								<div class="pull-left">
									<span class="rating">
										<span class="star"></span>
										<span class="star"></span>
										<span class="star"></span>
										<span class="star"></span>
										<span class="star"></span>
									</span>
								</div>
								<div class="pull-right text-uppercase">
									<span><small>270 Reviews</small></span>
								</div>
							</div>
						</div>
						<hr class="m-0">
						<div class="caption txtwrapRow txtHighLite">
							<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Qui animi neque suscipit provident accusamus ipsum cupiditate deleniti, illum.
							</p>
							<div class="clearfix">
								<div class="pull-left">
									<a href="#" class="btn btn-sm btn-i b-r-0" role="button">Select</a>
								</div>
								<div class="pull-right">
									<a href="#" class="btn btn-sm btn-p b-r-0" role="button">View</a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3 col-sm-6 col-md-4">
					<img src="http://placehold.it/500x400" alt="Hotel" class="img-responsive center-block">
					<div class="thumbnail b-r-0">
						<div class="caption">
							<div class="clearfix">
								<div class="pull-left">
									<h3 class="h4 m-0 text-success name">Hotel in USA</h3>
									<p class="text-capitalize m-0"><small>Paris France</small></p>
								</div>
								<div class="pull-right text-right">
									<p class="text-uppercase m-0"><small>Avg/Night</small></p>
									<p class="text-warning h4 m-0 price">
										<strong><i class="fa fa-usd fa-fw"></i>100</strong>
									</p>
								</div>
							</div>
						</div>
						<hr class="m-0">
						<div class="caption">
							<div class="clearfix">
								<div class="pull-left">
									<span class="rating">
										<span class="star"></span>
										<span class="star"></span>
										<span class="star"></span>
										<span class="star"></span>
										<span class="star"></span>
									</span>
								</div>
								<div class="pull-right text-uppercase">
									<span><small>270 Reviews</small></span>
								</div>
							</div>
						</div>
						<hr class="m-0">
						<div class="caption txtwrapRow txtHighLite">
							<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Qui animi neque suscipit provident accusamus ipsum cupiditate deleniti, illum.
							</p>
							<div class="clearfix">
								<div class="pull-left">
									<a href="#" class="btn btn-sm btn-i b-r-0" role="button">Select</a>
								</div>
								<div class="pull-right">
									<a href="#" class="btn btn-sm btn-p b-r-0" role="button">View</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<?php } ?>
<!-- end recommended hotels -->

<!-- explore more -->
<section class="home-explore-more">
	<div class="container">
		<div class="row">
			<div class="col-lg-4 col-md-6">
				<h2 class="h3"><b>Flight Deals</b></h2>
				<hr class="hr-10">
				<figure>
					<img src="http://placehold.it/800x300" alt="Flight Deals" class="img-responsive center-block">
					<figcaption class="flight-deals">
						<div class="media m-0">
							<div class="media-left">
								<a href="#">
									<img class="media-object" src="http://placehold.it/75x50" alt="Flight Image">
								</a>
							</div>
							<div class="media-body text-center">
								<h4 class="media-heading text-uppercase"><b>Aus Mex</b></h4>
								<p class="m-0"><small>Oct 16 - Oct 25</small></p>
							</div>
							<div class="media-right">
								<p class="text-primary h2 m-0"><b>$450</b></p>
							</div>
						</div>
						<div class="media m-0">
							<div class="media-left">
								<a href="#">
									<img class="media-object" src="http://placehold.it/75x50" alt="Flight Image">
								</a>
							</div>
							<div class="media-body text-center">
								<h4 class="media-heading text-uppercase"><b>Aus Mex</b></h4>
								<p class="m-0"><small>Oct 16 - Oct 25</small></p>
							</div>
							<div class="media-right">
								<p class="text-primary h2 m-0"><b>$450</b></p>
							</div>
						</div>
						<div class="media m-0">
							<div class="media-left">
								<a href="#">
									<img class="media-object" src="http://placehold.it/75x50" alt="Flight Image">
								</a>
							</div>
							<div class="media-body text-center">
								<h4 class="media-heading text-uppercase"><b>Aus Mex</b></h4>
								<p class="m-0"><small>Oct 16 - Oct 25</small></p>
							</div>
							<div class="media-right">
								<p class="text-primary h2 m-0"><b>$450</b></p>
							</div>
						</div>
						<div class="media m-0">
							<div class="media-left">
								<a href="#">
									<img class="media-object" src="http://placehold.it/75x50" alt="Flight Image">
								</a>
							</div>
							<div class="media-body text-center">
								<h4 class="media-heading text-uppercase"><b>Aus Mex</b></h4>
								<p class="m-0"><small>Oct 16 - Oct 25</small></p>
							</div>
							<div class="media-right">
								<p class="text-primary h2 m-0"><b>$450</b></p>
							</div>
						</div>
					</figcaption>
				</figure>
			</div>
			<div class="col-lg-8 col-md-6">
				<h2 class="h3 text-capitalize"><b>Explore More</b></h2>
				<hr class="hr-10">
				<div class="row">
					<!-- TOP Destinations START -->
					<?php
						if (in_array(META_ACCOMODATION_COURSE, $active_domain_modules) AND valid_array($top_destination_hotel) == true) : //TOP DESTINATION
							foreach ($top_destination_hotel as $tk => $tv) :?>
								<div class="col-lg-4 col-sm-6 htd-wrap">
									<figure class="explore-more">
										<img src="<?php echo $GLOBALS['CI']->template->domain_images($tv['image']); ?>" alt="<?=$tv['destination']?>" class="img-responsive center-block">
										<figcaption>
											<div class="clearfix">
												<div class="pull-left">
													<span class="h4"><?=$tv['destination']?></span>
												</div>
												<div class="pull-right">
													<span class=""><?=$tv['country']?></span>
												</div>
											</div>
										</figcaption>
									</figure>
									<input type="hidden" class="top-des-val hand-cursor" value="<?=hotel_suggestion_value($tv['destination'], $tv['country'])?>">
								</div>
							<?php
							endforeach;
						endif; //TOP DESTINATION
					?>
					<!-- TOP Destinations END -->
				</div>
			</div>
		</div>
		<div class="well">
			<div class="row">
				<div class="col-sm-6 col-md-8 txtwrapRow">
					<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Minima officia ea velit maxime debitis vel cupiditate, numquam perspiciatis, provident quaerat?</p>
				</div>
				<div class="col-sm-6 col-md-4">
					<a href="#" role="button" class="btn btn-lg btn-default btn-block">
						<b>Call to Action</b>
					</a>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- end explore more -->

<link rel="stylesheet" href="<?php echo  $GLOBALS['CI']->template->template_css_dir('backslider.css'); ?>" />
<!--<link rel="stylesheet" href="?php echo  $GLOBALS['CI']->template->template_css_dir('backslider2.css');?>" />
-->
<script src="<?php echo $GLOBALS['CI']->template->template_js_dir('backslider.js'); ?>"></script>
<script>
 //homepage slide show
 $(function($){
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

		//all scripts loaded
		var url = '<?=$GLOBALS['CI']->template->template_images(); ?>';
		$.supersized({
	          // Functionality
	          slide_interval          :   5000,   // Length between transitions
	          transition              :   1,      // 0-None, 1-Fade, 2-Slide Top, 3-Slide Right, 4-Slide Bottom, 5-Slide Left, 6-Carousel Right, 7-Carousel Left
	          transition_speed    : 700,    // Speed of transition
	          // Components
	          slide_links       : 'blank',  // Individual links for each slide (Options: false, 'num', 'name', 'blank')
	          slides :    [{"image":"<?php echo $GLOBALS['CI']->template->domain_images('bg1.jpg'); ?>"}, {"image":"<?php echo $GLOBALS['CI']->template->domain_images('bg2.jpg'); ?>'"}, {"image":"<?php echo $GLOBALS['CI']->template->domain_images('bg.jpg')?>"}, {"image":"<?php echo $GLOBALS['CI']->template->domain_images('bg3.jpg')?>"}, {"image":"<?php echo $GLOBALS['CI']->template->domain_images('bg4.jpg')?>'"}, {"image":"<?php echo $GLOBALS['CI']->template->domain_images('bg5.jpg');?>'"} ]
	        });
      });
//homepage slide show end
</script>