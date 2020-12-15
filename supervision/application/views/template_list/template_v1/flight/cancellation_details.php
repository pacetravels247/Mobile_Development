<?php 
$booking_details = $data ['booking_details'] [0];
extract($booking_details);
if($cancellation_status == SUCCESS_STATUS) {
	$status_message = 'Your Cancellation has been done, <br />';
}  else {
    $status_message =  $cancellation_message;
}

?>
<div class="container">
    <div class="staffareadash">
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
