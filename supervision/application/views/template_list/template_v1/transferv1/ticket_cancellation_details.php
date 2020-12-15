<!-- HTML BEGIN -->
<?php
$booking_details = $booking_data['booking_details'][0];


$itinerary_details_details = $booking_data['booking_details'][0]['itinerary_details'][0];
$cancellation_details = $booking_data['booking_details'][0]['cancellation_details'][0];
$app_reference = $booking_details['app_reference'];
$master_booking_status = $booking_details['status'];
$booking_source = $booking_details['booking_source'];

$supplier_refund_status = (int)$cancellation_details['ChangeRequestStatus'];
//Refund details to Agent/B2C User
$agent_refund_status = $cancellation_details['refund_status'];

$agent_refund_amount = $cancellation_details['refund_amount'];
//debug($cancellation_details);
//exit;
$agent_cancellation_charge = $cancellation_details['cancellation_charge'];
$agent_refund_payment_mode = $cancellation_details['refund_payment_mode'];
$agent_refund_comments = $cancellation_details['refund_comments'];
$agent_refund_date = $cancellation_details['refund_date'];
$agent_refund_currency = $cancellation_details['currency'];
$agent_refund_currency_conversion_rate = $cancellation_details['currency_conversion_rate'];
if(in_array($supplier_refund_status, array(3,4)) == false){
	$button_data_attributes = ' data-app_reference="'.$app_reference.'" data-booking_source="'.$booking_source.'" data-status="'.$master_booking_status.'"';
	$get_supplier_refund_status_button = '<button '.$button_data_attributes.' class="btn btn-sm btn-success" id="get_change_request_status"><i class="fa fa-refresh" aria-hidden="true"></i> Update Supplier Refund Status&Details</button>';
} else {
	$get_supplier_refund_status_button = '';
}
?>
<div class="bodyContent">
<div class="panel panel-default"><!-- PANEL WRAP START -->
<div class="panel-heading"><!-- PANEL HEAD START -->
<div class="panel-title">
<ul class="nav nav-tabs nav-justified" role="tablist" id="myTab">
	<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE START-->
	<li role="presentation" class="active"><a
		id="fromListHead" href="#fromList" aria-controls="home" role="tab"
		data-toggle="tab"> <i class="fa fa-home"></i> Activity Cancellation Details
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
<form class="form-horizontal" role="form" method="POST" action="<?=base_url()?>index.php/transferv1/update_refund_details" autocomplete="off" name="refund_details">
	<div class="panel-body">
		<div class="col-md-12">
		<fieldset form="refund_details">
		<legend class="form_legend">Supplier Cancellation Details</legend>
		<div class="list-group">
		<p class="list-group-item">AppReference: <strong><?=$booking_details['app_reference'];?></strong></p>
		<p class="list-group-item">BookingID: <?=$booking_details['booking_id'];?></p>
		<p class="list-group-item">ConfirmationReference: <?=$booking_details['confirmation_reference'];?></p>
		<p class="list-group-item">Refund Status: <span class="text-info"><strong><?=strtoupper($cancellation_details['status_description']);?></strong></span> <?=$get_supplier_refund_status_button?>
			<img style="display:none" id="loading_refund_loader_img" src="<?=$GLOBALS['CI']->template->template_images(); ?>loader_v3.gif" alt="updating..." />
		</p>
		<p class="list-group-item">Supplier Refund Amount: <strong><?=get_application_default_currency()?> <?=(float)$cancellation_details['API_RefundedAmount'];?></strong></p>
		<p class="list-group-item">Supplier Cancellation Charge: <strong><?=get_application_default_currency()?> <?=(float)$cancellation_details['API_CancellationCharge'];?></strong></p>
		<?php 
		if($supplier_refund_status == 3){ ?>
			<p class="list-group-item">Cancellation Processed On: <?=app_friendly_absolute_date($cancellation_details['created_datetime']);?></p>
		<?php }	?>
		
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
			<p class="list-group-item">Refund Status: <strong><?=$agent_refund_status;?></strong></p> 
			<p class="list-group-item">Refund Amount: <strong><?=get_application_default_currency()?> <?=$agent_refund_amount;?></strong></p>
			<p class="list-group-item">Cancellation Charge: <strong><?=get_application_default_currency()?> <?=$agent_cancellation_charge;?></strong></p>
			<p class="list-group-item">Refund Made On: <?=app_friendly_absolute_date($agent_refund_date);?></p>
			<p class="list-group-item">Refund Comments: <?=$agent_refund_comments;?></p>
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
					<label form="refund_details" class="col-sm-3 control-label">CancellationCharge</label>
					<div class="col-sm-6">
						<input type="text" data-placement="bottom" class="form-control" placeholder="CancellationCharge" name="cancellation_charge" value="<?=$agent_cancellation_charge?>">
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
<script type="text/javascript">
$(document).ready(function(){
	//Get cancellation status and refund details from supplier
	$('#get_change_request_status').click(function(e){
		e.preventDefault();
		$('#loading_refund_loader_img').show();
		var app_reference =		$(this).data('app_reference');
		var booking_source = 	$(this).data('booking_source');
		var status = 	$(this).data('status');
		var params = {'app_reference' : app_reference, 'booking_source': booking_source, 'status' : status};
		$.get('<?=base_url()?>sightseeing/update_supplier_cancellation_status_details', params, function(response){
			$('#loading_refund_loader_img').hide();
			location.reload();
		});
	});
});
</script>