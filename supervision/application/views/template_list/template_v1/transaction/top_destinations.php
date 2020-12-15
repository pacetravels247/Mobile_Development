<?php
$active_domain_modules = $this->active_domain_modules;
$tiny_loader = $GLOBALS['CI']->template->template_images('tiny_loader_v1.gif');
$tiny_loader_img = '<img src="'.$tiny_loader.'" class="loader-img" alt="Loading">';
$booking_summary = array();
?>
<div class="panel panel-default">
	<div class="panel-body">
		<?php include_once 'flight_top_search.php';?>
		<?php include_once 'hotel_top_search.php';?>
		<?php include_once 'bus_top_search.php';?>
		<?php include_once 'sightseeing_top_search.php';?>
		<?php include_once 'transfer_top_search.php';?>
		<div class="row">
			<div class="col-md-6">
				<div id='today-highlight' class="col-md-12">
				//Today Search Summary - Module Wise
				</div>
			</div>
		</div>
	</div>
</div>
<hr>
<script src="<?php echo SYSTEM_RESOURCE_LIBRARY; ?>/Highcharts/js/highcharts.js"></script>
<script src="<?php echo SYSTEM_RESOURCE_LIBRARY; ?>/Highcharts/js/modules/exporting.js"></script>