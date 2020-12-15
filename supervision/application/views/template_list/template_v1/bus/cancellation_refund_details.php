<!-- HTML BEGIN -->
<?php
$passengers = $booking_data["data"]["booking_customer_details"];
$agent_markup = 0;
foreach($passengers AS $passenger)
{
	if($passenger["status"]=="BOOKING_CANCELLED")
		$agent_markup += $passenger["agent_markup"];
}
//$booking_data = $booking_data['data'];
$booking_details = $booking_data['booking_details'][0];
$itinerary_details_details = $booking_data['booking_itinerary_details'][0];
$cancellation_details = $booking_data['cancellation_details'][0];
//debug($booking_data['cancellation_details']); exit;
$app_reference = $booking_details['app_reference'];
$master_booking_status = $booking_details['status'];
$booking_source = $booking_details['booking_source'];
//Refund details to Agent/B2C User
$agent_refund_status = $cancellation_details['refund_status'];
$agent_refund_amount = $cancellation_details['refund_amount'];
$agent_cancellation_charge_percentage = $cancellation_details['cancel_charge_percentage'];
$agent_cancellation_charge = $cancellation_details['cancel_charge'];
$agent_refund_payment_mode = $cancellation_details['refund_payment_mode'];
$agent_refund_comments = $cancellation_details['refund_comments'];
$agent_refund_date = $cancellation_details['refund_date'];
$agent_refund_currency = $cancellation_details['currency'];
$agent_refund_currency_conversion_rate = $cancellation_details['currency_conversion_rate'];
$customer_refund_amount = $agent_refund_amount+$agent_markup;
?>
<div class="bodyContent">
<div class="panel panel-default"><!-- PANEL WRAP START -->
<div class="panel-heading"><!-- PANEL HEAD START -->
<div class="panel-title">
<ul class="nav nav-tabs nav-justified" role="tablist" id="myTab">
	<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE START-->
	<li role="presentation" class="active"><a
		id="fromListHead" href="#fromList" aria-controls="home" role="tab"
		data-toggle="tab"> <i class="fa fa-bus"></i> Bus Cancellation Details
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
			<p class="list-group-item">PNR: <?=$booking_details['pnr'];?></p>
			<!--<p class="list-group-item">TicketNumber: <?=$booking_details['ticket'];?></p>
			<p class="list-group-item">RefundStatus: <strong><?=$agent_refund_status;?></strong></p> 
			<p class="list-group-item">RefundAmount: <strong><?=$agent_refund_currency?>  <?=(float)$agent_refund_amount;?></strong></p>
			<p class="list-group-item">CancellationChargePercentage: <strong><?=(float)$agent_cancellation_charge_percentage;?>%</strong></p>-->
			<?php if($agent_refund_status == 'PROCESSED'){ ?>
			<p class="list-group-item">RefundMadeOn: <?=app_friendly_absolute_date($agent_refund_date);?></p>
			<?php } ?>
			<p class="list-group-item">
			<a href="<?php echo base_url('voucher/bus/'.$app_reference . '/' . $booking_source.'/BOOKING_CANCELLED/send_credit_note/0/0/'.$agent_refund_amount.'/'.$agent_cancellation_charge.'?passenger_origin='.@$_GET["passenger_origin"]); ?>">View Agent Credit Note</a></p> 
			<p class="list-group-item">
			<a href="<?php echo base_url('voucher/bus/'.$app_reference . '/' . $booking_source.'/BOOKING_CANCELLED/send_credit_note/0/0/'.$customer_refund_amount.'/'.$agent_cancellation_charge.'/1?passenger_origin='.@$_GET["passenger_origin"]); ?>">View Customer Credit Note</a></p> 
			<p class="list-group-item">
			<a href="<?php echo base_url('voucher/bus/'.$app_reference . '/' . $booking_source.'/BOOKING_CANCELLED/send_credit_note/0/1/'.$agent_refund_amount.'/'.$agent_cancellation_charge.'?passenger_origin='.@$_GET["passenger_origin"]); ?>">Email & SMS Agent Credit Note</a></p>
			<p class="list-group-item">
			<a href="<?php echo base_url('voucher/bus/'.$app_reference . '/' . $booking_source.'/BOOKING_CANCELLED/send_credit_note/0/1/'.$customer_refund_amount.'/'.$agent_cancellation_charge.'/1?passenger_origin='.@$_GET["passenger_origin"]); ?>">Email & SMS Customer Credit Note</a></p>
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