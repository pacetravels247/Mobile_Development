<?php
 // debug($data ['booking_car_details'] [0]);die;
 $pstatus = $status;
if ($pstatus == 'CANCELLED') {
	$status_message = 'Cancellation Is Successfully Done !';
} else {
	$status_message = 'Some thing went wrong Please Try Again !';
}
?>
<div class="container">
	<div class="staffareadash">
		<div class="bakrd_color">

			<div class="search-result">
				<div class="container">
					<div class="confir_can">
						<div class="can_msg"><a class="close" href="<?=base_url().'report/flight'?>"><span aria-hidden="true">×</span></a> <?=$status_message?></div>
						<div class="col-xs-12 nopad">
							<div class="marg_cans">
								<div class="bookng_iddis">
									Booking ID <span class="down_can"><?=$app_reference?></span>
								</div>
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

					<!--<div class="para_cans">
						<p>Lorem Ipsum is simply dummy text of the printing and
							typesetting industry. Lorem Ipsum has been the industry's
							standard dummy text ever since the 1500s, when an unknown printer
							took a galley of type and scrambled it to make a type specimen
							book. It has survived not only five centuries, but also the leap
							into electronic typesetting, remaining essentially unchanged.</p>
					</div>-->

				</div>
			</div>
		</div>

	</div>
</div>
