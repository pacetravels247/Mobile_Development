<?php
$booking_details = $booking_data['booking_details'][0];
$booking_customer_details = $booking_data['booking_customer_details'][0];
$cancellation_details = $booking_data['cancellation_details'][0];
$ChangeRequestStatus = $cancellation_details['ChangeRequestStatus'];
$app_reference = $booking_details['app_reference'];
$booking_source = $booking_details['booking_source'];
$passenger_status = $booking_customer_details['status'];
$passenger_origin = $booking_customer_details['origin'];
//Cancellation Refund Details To Agent
$agent_refund_status = 					$cancellation_details['refund_status'];
$agent_refund_amount =						$cancellation_details['refund_amount'];
$agent_cancellation_charge = 				$cancellation_details['cancellation_charge'];
$agent_service_tax_on_refund_amount =		$cancellation_details['service_tax_on_refund_amount'];
$agent_swachh_bharat_cess = 				$cancellation_details['swachh_bharat_cess'];
$agent_refund_payment_mode = 				$cancellation_details['refund_payment_mode'];
$agent_refund_comments =					$cancellation_details['refund_comments'];
$agent_refund_date = 						$cancellation_details['refund_date'];
$cancellation_requested_on =				$cancellation_details['cancellation_requested_on'];
$cancellation_processed_on = 				$cancellation_details['cancellation_processed_on'];
$cancellation_currency = 					$cancellation_details['currency'];
$cancellation_currency_conversion_rate =	$cancellation_details['currency_conversion_rate'];
//ChangeRequestStatus: StatusCode:NotSet = 0,Unassigned = 1,Assigned = 2,Acknowledged = 3,Completed = 4,Rejected = 5,Closed = 6,Pending = 7,Other = 8
if($cancellation_details['API_refund_status'] != 'PROCESSED'){
	$button_data_attributes = ' data-app_reference="'.$app_reference.'" data-booking_source="'.$booking_source.'" data-passenger_status="'.$passenger_status.'" data-passenger_origin="'.$passenger_origin.'"';
	$get_supplier_refund_status_button = '<button '.$button_data_attributes.' class="btn btn-sm btn-success" id="get_change_request_status"><i class="fa fa-refresh" aria-hidden="true"></i> Update Supplier Refund Status&Details</button>';
} else {
	$get_supplier_refund_status_button = '';
}
//Added to hide the button
$get_supplier_refund_status_button = '';

$application_currency = get_application_default_currency();
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
				<form class="form-horizontal" role="form" method="POST" action="<?=base_url()?>index.php/flight/update_supplier_refund_details" autocomplete="off" name="supp_refund_details">
					<input class="hide" type="hidden" name="passenger_origin_for_supp_upd" value="<?=$passenger_origin?>">
					<input class="hide" type="hidden" name="app_reference_for_supp_upd" value="<?=$app_reference?>">
					<input class="hide" type="hidden" name="passenger_status_for_supp_upd" value="<?=$passenger_status?>">
					<input class="hide" type="hidden" name="refund_payment_mode_for_supp_upd" value="online">
				<!-- Refund Form Starts -->
					<fieldset form="refund_details">
					<div class="list-group">
					<legend class="form_legend">Supplier Cancellation Details</legend>
					<p class="list-group-item">AppReference				: <strong><?=$app_reference;?></strong></p>
					<p class="list-group-item">PNR						: <?=$booking_customer_details['pnr'];?></p>
					<p class="list-group-item">BookID					: <?=$booking_customer_details['book_id'];?></p>
					<p class="list-group-item">TicketId					: <?=$booking_customer_details['TicketId'];?></p>
					<p class="list-group-item">TicketNumber				: <?=$booking_customer_details['TicketNumber'];?></p>
					<p class="list-group-item">PassengerName			: <?=$booking_customer_details['first_name'].' '.$booking_customer_details['last_name'];?></p>
					<p class="list-group-item">CancellationRequestedOn	: <?=app_friendly_absolute_date($cancellation_requested_on);?></p>
					<?php
					if($cancellation_details['API_refund_status'] == 'PROCESSED'){ ?>
						<p class="list-group-item">CancellationProcessedOn : <?=app_friendly_absolute_date($cancellation_processed_on);?></p>
					<?php } ?>
					<p class="list-group-item"><strong><u>Supplier Refund Details</u></strong></p>
					<div class="form-group">
					<div class="col-sm-3 col-md-2"> 
						<p class="list-group-item">Refund Status :</p>
					</div>
					<div class="col-sm-3 col-md-2"> 
						<select class="form-control" name="api_refund_status" required="">
							<option value="">Please select</option>
							<?=generate_options(get_enum_list('refund_status'), (array)$cancellation_details['API_refund_status'])?>
						</select>
					</div></div>
					<?=$get_supplier_refund_status_button?>
						<img style="display:none" id="loading_refund_loader_img" src="<?=$GLOBALS['CI']->template->template_images(); ?>loader_v3.gif" alt="updating..." />
					</p>
					<p class="list-group-item">RefundAmount	: <strong><?=$application_currency?></strong> 
					<input type="text" name="api_refund_amount" value="<?=(float)$cancellation_details['API_RefundedAmount'];?>">
					</p>
					<p class="list-group-item">CancellationCharge	: <strong><?=$application_currency?></strong> 
					<input type="text" name="api_cancellation_charge" value="<?=(float)$cancellation_details['API_CancellationCharge'];?>">
					</p>
					<p class="list-group-item">ServiceTaxOnRefundAmount	: <strong><?=$application_currency?></strong> 
					<input type="text" name="api_service_tax" value="<?=(float)$cancellation_details['API_ServiceTaxOnRefundAmount'];?>">
					</p>
					<p class="list-group-item">SwachhBharatCess	: <strong><?=$application_currency?> </strong>
					<input type="text" name="api_swatch_bharat" value="<?=(float)$cancellation_details['API_SwachhBharatCess'];?>">
					</p>
					</div>
					<div class="form-group">
						<div class="col-sm-4"> 
							<input class="form-control  btn btn-success " type="submit" name="refund_payment_mode" value="Udate Supplier Refund Details">
						</div>
					</div>
				</fieldset>
				</form>
				<form class="form-horizontal" role="form" method="POST" action="<?=base_url()?>index.php/flight/update_ticket_refund_details" autocomplete="off" name="refund_details">
				<?php if($is_agent == true){ ?>
				<fieldset form="refund_details">
					<input class="hide" type="hidden" name="app_reference" value="<?=$app_reference?>">
					<input class="hide" type="hidden" name="passenger_origin" value="<?=$passenger_origin?>">
					<input class="hide" type="hidden" name="passenger_status" value="<?=$passenger_status?>">
					<input class="hide" type="hidden" name="refund_payment_mode" value="online">
					<?php if($is_agent == true){ ?>
						<legend class="form_legend">Refund to <?=$booked_user_details['agency_name'];?> Agent-<?=$booked_user_details['uuid']?></legend>
					<?php } else{ ?>
						<legend class="form_legend">Refund to B2C User</legend>
					<?php } ?>
					
				<?php if($agent_refund_status != 'PROCESSED'){ ?><!-- agent Refund Status Condition Starts -->
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
							<label form="refund_details" class="col-sm-3 control-label">ServiceTaxOnRefundAmount</label>
							<div class="col-sm-6">
								<input type="text" data-placement="bottom" class="form-control" placeholder="ServiceTaxOnRefundAmount" name="service_tax_on_refund_amount" value="<?=$agent_service_tax_on_refund_amount?>">
							</div>
						</div>
						<div class="form-group">
							<label form="refund_details" class="col-sm-3 control-label">SwachhBharatCess</label>
							<div class="col-sm-6">
								<input type="text" data-placement="bottom" class="form-control" placeholder="SwachhBharatCess" name="swachh_bharat_cess" value="<?=$agent_swachh_bharat_cess?>">
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
				<?php }else{ ?>
					<p class="list-group-item">Refund Status	: <strong><span class="text-info"><?=strtoupper($agent_refund_status);?></span></strong> <?=$get_supplier_refund_status_button?></p>
					<p class="list-group-item">RefundAmount	: <strong><?=$cancellation_currency?> <?=($agent_refund_amount*$cancellation_currency_conversion_rate);?></strong></p>
					<p class="list-group-item">CancellationCharge	: <strong><?=$cancellation_currency?> <?=($agent_cancellation_charge*$cancellation_currency_conversion_rate);?></strong></p>
					<p class="list-group-item">ServiceTaxOnRefundAmount	: <strong><?=$cancellation_currency?> <?=($agent_service_tax_on_refund_amount*$cancellation_currency_conversion_rate);?></strong></p>
					<p class="list-group-item">SwachhBharatCess	: <strong><?=$cancellation_currency?> <?=($agent_swachh_bharat_cess*$cancellation_currency_conversion_rate);?></strong></p>
					<p class="list-group-item">Refunded On	: <?=app_friendly_absolute_date($agent_refund_date);?></p>
					<p class="list-group-item">Comments	: <?=$agent_refund_comments;?></p>
				<?php }?><!-- agent Refund Status Condition Ends -->
				</fieldset>
				<?php } ?>
				</form>
				 <!-- Refund Form Ends -->
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
<script type="text/javascript">
$(document).ready(function(){
	//Get cancellation status and refund details from supplier
	$('#get_change_request_status').click(function(e){
		e.preventDefault();
		$('#loading_refund_loader_img').show();
		var app_reference =		$(this).data('app_reference');
		var booking_source = 	$(this).data('booking_source');
		var passenger_status = 	$(this).data('passenger_status');
		var passenger_origin = 	$(this).data('passenger_origin');
		var params = {'app_reference' : app_reference, 'booking_source': booking_source, 'passenger_status' : passenger_status, 'passenger_origin': passenger_origin};
		$.get('<?=base_url()?>flight/update_supplier_cancellation_status_details', params, function(response){
			$('#loading_refund_loader_img').hide();
			//location.reload();
		});
	});
});
</script>
