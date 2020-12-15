<!-- HTML BEGIN -->
<?php
$booking_details = $booking_data['booking_details'][0];
$itinerary_details_details = $booking_data['booking_itinerary_details'][0];
$cancellation_details = $booking_data['cancellation_details'][0];
$app_reference = $booking_details['app_reference'];
$master_booking_status = $booking_details['status'];
$booking_source = $booking_details['booking_source'];
//Refund details to Agent/B2C User
$agent_refund_status = $cancellation_details['refund_status'];
$agent_refund_amount = $cancellation_details['refund_amount'];
$agent_cancellation_charge_percentage = $cancellation_details['cancel_charge_percentage'];
$agent_refund_payment_mode = $cancellation_details['refund_payment_mode'];
$agent_refund_comments = $cancellation_details['refund_comments'];
$agent_refund_date = $cancellation_details['refund_date'];
$agent_refund_currency = $cancellation_details['currency'];
$agent_refund_currency_conversion_rate = $cancellation_details['currency_conversion_rate'];
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
<form class="form-horizontal" role="form" method="POST" action="<?=base_url()?>index.php/bus/update_refund_details" autocomplete="off" name="refund_details">
	<div class="panel-body">
		<div class="col-md-12">
		<fieldset form="refund_details">
		<legend class="form_legend">Supplier Cancellation Details</legend>
		<div class="list-group">
		<p class="list-group-item">AppReference: <strong><?=$booking_details['app_reference'];?></strong></p>
		<p class="list-group-item">PNR: <?=$booking_details['pnr'];?></p>
		<p class="list-group-item">TicketNumber: <?=$booking_details['ticket'];?></p>
		<p class="list-group-item">Supplier Refund Amount: <strong><?=get_application_default_currency()?><?=$cancellation_details['api_refund_amount'];?></strong></p>
		<p class="list-group-item">Supplier Cancellation Charge Percentage: <strong><?=$cancellation_details['api_cancel_charge_percentage'];?>%</strong></p>
		<p class="list-group-item">Cancellation Processed On: <?=app_friendly_absolute_date($cancellation_details['created_datetime']);?></p>
		</div>
		</fieldset>
		<fieldset form="refund_details">
		<?php if($is_agent == true){ ?>
			<legend class="form_legend">Refund to <?=$booked_user_details['agency_name'];?> Agent-<?=$booked_user_details['uuid']?></legend>
		<?php } else{ ?>
			<legend class="form_legend">Refund to B2C User</legend>
		<?php } ?>
		<?php if($agent_refund_status == 'PROCESSED'){ ?>
		<div class="list-group">
			<p class="list-group-item">RefundStatus: <strong><?=$agent_refund_status;?></strong></p> 
			<p class="list-group-item">RefundAmount: <strong><?=get_application_default_currency()?> <?=$agent_refund_amount;?></strong></p>
			<p class="list-group-item">CancellationChargePercentage: <strong><?=$agent_cancellation_charge_percentage;?>%</strong></p>
			<p class="list-group-item">RefundMadeOn: <?=app_friendly_absolute_date($agent_refund_date);?></p>
			<p class="list-group-item">RefundComments: <?=$agent_refund_comments;?></p>
		</div>
		<?php } else { ?>
				<input class="hide" type="hidden" name="app_reference" value="<?=$app_reference?>">
				<input class="hide" type="hidden" name="booking_source" value="<?=$booking_source?>">
				<input class="hide" type="hidden" name="status" value="<?=$master_booking_status?>">
				<input class="hide" type="hidden" name="refund_payment_mode" value="online">
				<div class="form-group">
					<label form="refund_details" class="col-sm-3 control-label">RefundAmount</label>
					<div class="col-sm-6">
						<input type="text" data-placement="bottom" class="form-control" placeholder="Refund Amount" name="refund_amount" value="<?=$agent_refund_amount?>">
					</div>
				</div>
				<div class="form-group">
					<label form="refund_details" class="col-sm-3 control-label">CancellationChargePercentage(%)</label>
					<div class="col-sm-6">
						<input type="text" data-placement="bottom" class="form-control" placeholder="CancellationCharge" name="cancel_charge_percentage" value="<?=$agent_cancellation_charge_percentage?>">
					</div>
				</div>
				<div class="form-group">
					<label form="refund_details" class="col-sm-3 control-label">Refund Status<span class="text-danger">*</span></label>
					<div class="col-sm-6">
						<select class="form-control" name="refund_status" required="">
							<option value="">Please select</option>
							<?=generate_options(get_enum_list('refund_status'), (array)$agent_refund_status)?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label form="refund_details" class="col-sm-3 control-label">Comments<span class="text-danger">*</span></label>
					<div class="col-sm-6">
						<textarea type="text" class="form-control" placeholder="Comments" name="refund_comments" value="" required=""><?=$agent_refund_comments?></textarea>
					</div>
				</div>
			<div class="form-group">
					<div class="col-sm-8 col-sm-offset-4"> 
						<button class=" btn btn-success "type="submit">Update Refund Details</button> 
						<button class=" btn btn-warning " type="reset">Reset</button>
					</div>
			</div>
		<?php }?>
		</fieldset>
		</div> 
	</div>
</form>
</div>
</div>

</div>
</div>
<!-- PANEL BODY END --></div>
<!-- PANEL WRAP END --></div>
<!-- HTML END -->