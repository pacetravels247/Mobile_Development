<?php 
$booking_details = $data ['booking_details'] [0];
//debug($booking_details['booking_customer_details']);die('777');
extract($booking_details);
$status = array();
foreach ($booking_details['booking_customer_details'] as $key => $value) {
    array_push($status, $value['status']);
}
//$status == 'BOOKING_CANCELLED'
if(in_array('BOOKING_CANCELLED', $status)) {
	$status_message = 'Your Cancellation has been done..!';
} else {
	$status_message = 'Some thing went wrong Please Try Again !!!';
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
