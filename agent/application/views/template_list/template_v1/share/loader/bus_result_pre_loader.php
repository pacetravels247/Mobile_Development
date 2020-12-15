<?php 
	/* $a = json_encode($trip_details);
	$b = json_decode($a);
	/debug($b); */
//debug($trip_details);
?>
<div class="result-pre-loader-wrapper result-pre-loader">
	<div class="result-pre-loader-container">
		<div class="preloader_logo">
			<img
				src="<?php echo $GLOBALS['CI']->template->domain_images($GLOBALS['CI']->template->get_domain_logo()); ?>">
		</div>
		<div class="clearfix"></div>
		<h3>
			<b>Please wait. Searching for the best fares !</b>
		</h3>

		<div class="progress progress-striped active">
			<!-- Vaishak Loader
<div style="width: 0%;" role="progressbar" id="bar" class="progress-bar progress-bar-color"><span>Still working ... 100%</span></div> 
-->
			<div id="noTrespassingOuterBarG">
				<div id="noTrespassingFrontBarG" class="noTrespassingAnimationG">
					<div class="noTrespassingBarLineG"></div>
					<div class="noTrespassingBarLineG"></div>
					<div class="noTrespassingBarLineG"></div>
					<div class="noTrespassingBarLineG"></div>
					<div class="noTrespassingBarLineG"></div>
					<div class="noTrespassingBarLineG"></div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-5 col-md-5 col-sm-5 col-xs-4 ">
				<div class="pull-left">
					<i class="icn ldep_f_icn"><img alt=""
						src="<?php //echo $GLOBALS['CI']->template->template_images('bus_from.png'); ?>"></i>
				</div>
				<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 ">
					<h2><?php echo ucfirst($result['bus_station_from']); ?></h2>
				</div>
			</div>
			<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 "><?php echo "<font color='Blue'><b>-". date("dMy",strtotime($result['bus_date_1']))."-></b></font>";?></div>
			<div class="col-lg-5 col-md-5 col-sm-5 col-xs-4 ">
				<div class="pull-right">
					<i class="icn larr_f_icn"><img
						src="<?php //echo $GLOBALS['CI']->template->template_images('bus_to.png'); ?>"></i>
				</div>
				<div
					class="col-lg-8 col-md-8 col-sm-8 col-xs-12 pull-right arr_ldr_otr">
					<h2><?php echo ucfirst($result['bus_station_to']); ?></h2>
				</div>
			</div>
		</div>
	</div>
</div>