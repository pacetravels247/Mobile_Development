<?php 
$booking_details = $data ['booking_details'] [0];
extract($booking_details);
if($status == 'BOOKING_CANCELLED') {
	$status_message = 'Your Cancellation Request has been sent, <br />our Representatives will process further';
} else {
	$status_message = 'Some thing went wrong Please Try Again !!!';
}
?>
<div class="content-wrapper dashboard_section">
	<div class="container">
	<div class="staffareadash">
		<?php echo $GLOBALS['CI']->template->isolated_view('share/profile_navigator_tab') ?>
		<div class="bakrd_color">
		
<div class="search-result">
	<div class="container">
		<div class="confir_can">
			<div class="can_msg"><?=$status_message?></div>
			<div class="col-xs-12 nopad">
				<div class="marg_cans">
					<div class="bookng_iddis">Booking ID
					<span class="down_can"><?=$app_reference?></span></div>
				</div>
			</div>
			<!--
			<div class="col-xs-6 nopad">
				<div class="marg_cans">
					<div class="bookng_iddis">Refund Amount 
					<span class="down_can"><span class="fa fa-rupee"></span>2,456</span></div>
				</div>
			</div>
			 -->
		</div>
		
		<div class="clearfix"></div>
		
		<div class="para_cans">
			
		</div>
		
	</div>
</div>
	</div>
		
	</div>
</div>
</div>
