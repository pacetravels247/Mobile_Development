<?php 
	/* $a = json_encode($trip_details);
	$b = json_decode($a);
	/debug($b); */
//debug($trip_details);
?>
<div class="result-pre-loader-wrapper result-pre-loader">
	<div class="result-pre-loader-container">
        <div class="preloader_logo"><img src="<?php echo $GLOBALS['CI']->template->domain_images($GLOBALS['CI']->template->get_domain_logo()); ?>"></div>
        <div class="clearfix"></div>
		<h3><b>Please wait. Searching for the best Hotels !</b></h3>
		<div class="progress progress-striped active">
			<div style="width: 0%;" role="progressbar" id="bar" class="progress-bar progress-bar-color"><span>Still working ... 100%</span></div>
		</div>
		<div class="row">
			<div class="col-lg-5 col-md-5 col-sm-5 col-xs-4 ">
				<div class="pull-left hide">
					<i class="icn ldep_f_icn"><img alt="" src="<?php //echo $GLOBALS['CI']->template->template_images('check.png'); ?>"></i>
				</div>
				<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 ">
					<b><i>Check In</i></b><br>
					<?php echo date("dMy",strtotime($result['from_date']));?>
				</div>
			</div>
			<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 ">
				<font color="Blue"><b><?php echo ucfirst($result['location']); ?></b></font>
				</div>
			<div class="col-lg-5 col-md-5 col-sm-5 col-xs-4 ">
				<div class="pull-right hide">
					<i class="icn larr_f_icn"><img src="<?php //echo $GLOBALS['CI']->template->template_images('check.png'); ?>"></i>
				</div>
				<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 pull-right arr_ldr_otr">
					<b><i>Check Out</i></b><br>
					<?php echo date("dMy",strtotime($result['to_date']));?>
				</div>
			</div>
		</div>
	</div>
</div>