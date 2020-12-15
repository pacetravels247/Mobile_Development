<?php
//Booking Transaction Data (btd)
$tds = $whole_booking_data['booking_transaction_details'];
$agent_markup = 0;
foreach ($tds  as $td) {
	$agent_markup += $td["agent_markup"];
}
$total_pax = count($whole_booking_data['booking_customer_details']);
$booking_details = $booking_data['booking_details'][0];
$booking_customer_details = $booking_data['booking_customer_details'][0];
$cancellation_details = $booking_data['cancellation_details'][0];
$ChangeRequestStatus = $cancellation_details['ChangeRequestStatus'];
$app_reference = $booking_details['app_reference'];
$booking_source = $booking_details['booking_source'];
$passenger_status = $booking_customer_details['status'];
$passenger_origin = $booking_customer_details['origin'];
//Cancellation Refund Details To Agent
$agent_markup_single = $agent_markup/$total_pax;
$cancellation_currency = 					$cancellation_details['currency'];
$cancellation_currency_conversion_rate =	$cancellation_details['currency_conversion_rate'];
$agent_refund_status = 						$cancellation_details['refund_status'];
$agent_refund_amount =						($cancellation_details['refund_amount']*$cancellation_currency_conversion_rate);
$agent_cancellation_charge = 				($cancellation_details['cancellation_charge']*$cancellation_currency_conversion_rate);
$customer_cancellation_charge = $agent_cancellation_charge;
$agent_service_tax_on_refund_amount =		($cancellation_details['service_tax_on_refund_amount']*$cancellation_currency_conversion_rate);
$agent_swachh_bharat_cess = 				($cancellation_details['swachh_bharat_cess']*$cancellation_currency_conversion_rate);
$agent_refund_payment_mode = 				$cancellation_details['refund_payment_mode'];
$agent_refund_comments =					$cancellation_details['refund_comments'];
$agent_refund_date = 						$cancellation_details['refund_date'];
$cancellation_requested_on =				$cancellation_details['cancellation_requested_on'];
$cancellation_processed_on = 				$cancellation_details['cancellation_processed_on'];
$customer_refund_amount = $agent_refund_amount;
?>
<!-- HTML BEGIN -->
<div class="bodyContent">
<div class="panel panel-default"><!-- PANEL WRAP START -->
<div class="panel-heading"><!-- PANEL HEAD START -->
<div class="panel-title">
<ul class="nav nav-tabs nav-justified" role="tablist" id="myTab">
	<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE START-->
	<li role="presentation" class="active"><a
		id="fromListHead" href="#fromList" aria-controls="home" role="tab"
		data-toggle="tab"> <i class="fa fa-plane"></i> Flight Cancellation Details
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
	<div class="col-md-12">
			<div class="panel-body">
				<div class="col-md-12">
					<div class="list-group">
						<p class="list-group-item"><strong><u>Cancellation Details</u></strong></p>
						<p class="list-group-item">AppReference				: <strong><?=$app_reference;?></strong></p>
						<p class="list-group-item">PNR						: <?=$booking_customer_details['pnr'];?></p>
						<p class="list-group-item">BookID					: <?=$booking_customer_details['book_id'];?></p>
						<p class="list-group-item">TicketId					: <?=$booking_customer_details['TicketId'];?></p>
						<p class="list-group-item">TicketNumber				: <?=$booking_customer_details['TicketNumber'];?></p>
						<p class="list-group-item">PassengerName			: <?=$booking_customer_details['first_name'].' '.$booking_customer_details['last_name'];?></p>
						<p class="list-group-item">CancellationRequestedOn	: <?=app_friendly_absolute_date($cancellation_requested_on);?></p>
						<p class="list-group-item"><strong><u>Refund Details</u></strong></p>
						<p class="list-group-item">Refund Status	: <strong><span class="text-info"><?=strtoupper($agent_refund_status);?></span></strong></p>
						<p class="list-group-item">RefundAmount	: <strong><?=$cancellation_currency?> <?=($agent_refund_amount);?></strong></p>
						<p class="list-group-item">CancellationCharge	: <strong><?=$cancellation_currency?> <?=($agent_cancellation_charge);?></strong></p>
						<p class="list-group-item">ServiceTaxOnRefundAmount	: <strong><?=$cancellation_currency?> <?=($agent_service_tax_on_refund_amount);?></strong></p>
						<p class="list-group-item">SwachhBharatCess	: <strong><?=$cancellation_currency?> <?=($agent_swachh_bharat_cess);?></strong></p>
						<?php if($agent_refund_status == 'PROCESSED'){ ?>
							<p class="list-group-item">Refunded On	: <?=app_friendly_absolute_date($agent_refund_date);?></p>
			
						<p class="list-group-item">
						<a href="<?php echo base_url('voucher/flight/'.$app_reference . '/' . $booking_source.'/BOOKING_CANCELLED/send_credit_note/0/0/'.$agent_refund_amount.'/'.$agent_cancellation_charge.'?passenger_origin='.$_GET['passenger_origin']); ?>">View Your Credit Note</a></p> 
						<p class="list-group-item">
						<a href="<?php echo base_url('voucher/flight/'.$app_reference . '/' . $booking_source.'/BOOKING_CANCELLED/send_credit_note/0/0/'.$customer_refund_amount.'/'.$customer_cancellation_charge.'/1?passenger_origin='.$_GET['passenger_origin']); ?>">View Customer Credit Note</a></p> 
						<p class="list-group-item">
						<a href="<?php echo base_url('voucher/flight/'.$app_reference . '/' . $booking_source.'/BOOKING_CANCELLED/send_credit_note/0/1/'.$agent_refund_amount.'/'.$agent_cancellation_charge.'?passenger_origin='.$_GET['passenger_origin']); ?>">Email & SMS your Credit Note</a></p>
						<p class="list-group-item">
						<a href="<?php echo base_url('voucher/flight/'.$app_reference . '/' . $booking_source.'/BOOKING_CANCELLED/send_credit_note/0/1/'.$customer_refund_amount.'/'.$customer_cancellation_charge.'/1?passenger_origin='.$_GET['passenger_origin']); ?>">Email & SMS Customer Credit Note</a></p>
						<?php } ?>
					</div>
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