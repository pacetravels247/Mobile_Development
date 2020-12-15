<!-- HTML BEGIN -->
<?php
$booking_details = $booking_data['booking_details'][0];
$itinerary_details_details = $booking_data['booking_itinerary_details'][0];
$cancellation_details = $booking_data['cancellation_details'][0];
$app_reference = $booking_details['app_reference'];
$master_booking_status = $booking_details['status'];
$booking_source = $booking_details['booking_source'];
//Refund details to Agent/B2C User
$agent_refund_currency = $cancellation_details['currency'];
$agent_refund_currency_conversion_rate = $cancellation_details['currency_conversion_rate'];
$agent_refund_status = $cancellation_details['refund_status'];
$agent_refund_amount = ($cancellation_details['refund_amount']*$agent_refund_currency_conversion_rate);
$agent_cancellation_charge = ($cancellation_details['cancellation_charge']*$agent_refund_currency_conversion_rate);
$agent_refund_payment_mode = $cancellation_details['refund_payment_mode'];
$agent_refund_comments = $cancellation_details['refund_comments'];
$agent_refund_date = $cancellation_details['refund_date'];

?>
<div class="bodyContent">
<div class="panel panel-default"><!-- PANEL WRAP START -->
<div class="panel-heading"><!-- PANEL HEAD START -->
<div class="panel-title">
<ul class="nav nav-tabs nav-justified" role="tablist" id="myTab">
	<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE START-->
	<li role="presentation" class="active"><a
		id="fromListHead" href="#fromList" aria-controls="home" role="tab"
		data-toggle="tab"> <i class="fa fa-home"></i> Hotel Cancellation Details
	</a></li>
	<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE END -->
</ul>
</div>
</div>
<!-- PANEL HEAD START -->
<div class="panel-body"><!-- PANEL BODY START -->
<div class="tab-content">
<div role="tabpanel" class="tab-pane active" id="fromList">
<div class="panel-body">
<div class"col-md-12">
</div>
	<div class="panel-body">
		<div class="col-md-12">
		<div class="list-group">
			<p class="list-group-item"><strong><u>Refund Details</u></strong></p>
			<p class="list-group-item">AppReference: <strong><?=$app_reference;?></strong></p>
			<p class="list-group-item">BookingID: <?=$booking_details['booking_id'];?></p>
			<p class="list-group-item">ConfirmationReference: <?=$booking_details['booking_reference'];?></p>
			<p class="list-group-item">RefundStatus: <strong><?=$agent_refund_status;?></strong></p> 
			<p class="list-group-item">RefundAmount: <strong><?=$agent_refund_currency?> <?=$agent_refund_amount;?></strong></p>
			<p class="list-group-item">CancellationCharge: <strong><?=$agent_refund_currency?> <?=$agent_cancellation_charge;?></strong></p>
			<?php if($agent_refund_status == 'PROCESSED'){ ?>
			<p class="list-group-item">RefundMadeOn: <?=app_friendly_absolute_date($agent_refund_date);?></p>
			<?php } ?>
		</div>
		</div> 
	</div>
</div>
</div>

</div>
</div>
<!-- PANEL BODY END --></div>
<!-- PANEL WRAP END --></div>
<!-- HTML END -->