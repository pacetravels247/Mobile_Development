<?php
$booking_details = $booking_data['booking_details'][0];
//Calculation of GST, Commission on the perticular customers got cancelled
$all_customers = $master_booking_details["data"]["booking_details"][0]["booking_transaction_details"][0]["booking_customer_details"];
$no_of_customers = count($all_customers);
$agent_comm = $master_booking_details["data"]["booking_details"][0]["agent_commission"];
$agent_tds = $master_booking_details["data"]["booking_details"][0]["agent_tds"];
$single_comm = $agent_comm/$no_of_customers;
$single_tds = $agent_tds/$no_of_customers;
$single_gst = $master_booking_details["data"]["booking_details"][0]["gst"]/$no_of_customers;

$booking_customer_details = $booking_data['booking_customer_details'][0];
//debug($booking_customer_details); exit;
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
$commission_reversed = 				$cancellation_details['commission_reversed'];
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
$pass_type = "";
if($booking_customer_details["passenger_type"] == "Adult")
	$pass_type="ADT";
if($booking_customer_details["passenger_type"] == "Child")
	$pass_type="CHD";
if($booking_customer_details["passenger_type"] == "Infant")
	$pass_type="INF";

$price_dets = json_decode($booking_details["price_attr"], true);
$req_price_det = $price_dets[$pass_type];
$booking_source = $booking_details['booking_source'];
if($booking_source == 'PTBSID0000000012'){ $booking_source_name = "Star Air"; }
if($booking_source == 'PTBSID0000000009'){ $booking_source_name = "ACH"; }
if($booking_source == 'PTBSID0000000010'){ $booking_source_name = "GDS"; }
if($booking_source == 'PTBSID0000000011'){ $booking_source_name = "SpiceJet"; }
if($booking_source == 'PTBSID0000000013'){ $booking_source_name = "Indigo"; }
if($booking_source == 'PTBSID0000000045'){ $booking_source_name = "TruJet"; }
?>
<!-- HTML BEGIN -->
<div class="bodyContent">
	
<div class="panel panel-default"><!-- PANEL WRAP START -->
<!-- <div class="panel-heading">
<div class="panel-title">
<ul class="nav nav-tabs nav-justified" role="tablist" id="myTab">
	<li role="presentation" class="active"><a
		id="fromListHead" href="#fromList" aria-controls="home" role="tab"
		data-toggle="tab"> <i class="fa fa-plane"></i> Flight Cancellation Details
	</a></li>
</ul>
</div>
</div> -->
<!-- PANEL HEAD START -->
<div class="tab-content">
<div role="tabpanel" class="tab-pane active" id="fromList">
	<h3>Flight Cancellation Details</h3>
<div class="panel-body" style="padding: 20px 0;">
	<div class="col-md-12 nopad">
		<input class="hide" type="hidden" name="passenger_origin_for_supp_upd" value="<?=$passenger_origin?>">
		<input class="hide" type="hidden" name="app_reference_for_supp_upd" value="<?=$app_reference?>">
		<input class="hide" type="hidden" name="passenger_status_for_supp_upd" value="<?=$passenger_status?>">
		<input class="hide" type="hidden" name="refund_payment_mode_for_supp_upd" value="online">
		<div class="row">
			<div class="col-sm-4">
                 <label>AppReference</label> <br>
                 <input class="form-control" type="text" name="" value="<?php echo $app_reference; ?>" >
            </div>
			<div class="col-sm-4">
                 <label>pnr</label> <br>
                 <input class="form-control" type="text" name="" value="<?php echo $booking_customer_details['pnr']; ?>">
            </div>
            <div class="col-sm-4">
                 <label>Agent ID</label> <br>
                 <input class="form-control" type="text" name="" value="<?php echo provab_decrypt($booked_user_details['uuid']); ?>">
            </div>
		</div>
		<div class="row">
            <div class="col-sm-4">
                 <label>Agency Name</label> <br>
                 <input class="form-control" type="text" name="" value="<?php echo $booked_user_details['agency_name']; ?>">
            </div>
            <div class="col-sm-4">
                 <label>API</label> <br>
                 <input class="form-control" type="text" name="" value="<?php echo $booking_source_name; ?>">
            </div>
            <div class="col-sm-4">
                 <label>Total Passenger</label> <br>
                 <input class="form-control" type="text" name="" value="<?php echo count($booking_data['booking_customer_details']); ?>">
            </div>
		</div>
	</div>
</div>

<h3>Passenger Details</h3>
<div class="panel-body">
	<div class="col-md-12 nopad">
			<form class="form-horizontal" id="refund_form" role="form" method="POST" action="<?=base_url()?>index.php/flight/update_ticket_refund_details" name="refund_details">
		<div class="datatables-res">
			<!-- <legend class="form_legend"> Passenger Details</legend> -->
			<input class="hide" type="hidden" name="agent_id" value="<?=$booked_user_details['user_id']?>">
			<input class="hide" type="hidden" name="commission_reversed" value="<?=$commission_reversed?>">
		<table class="table table-striped" >
		    <thead>
		        <tr>
		            <th>Sl</th>
					<th>Name</th>
					<th>Type</th>
					<th>Fare</th>
					<th>Tax</th>
					<th>Ticket Number</th>
					<th>Supplier cancellation</th>
					<th>Pace Charge</th>
					<th>Cancellation charge(with gst)</th>
					
		        </tr>
		    </thead>
		    <tbody>
		    	<?php
		    	foreach ($booking_data['booking_customer_details'] as $k => $val) {
		    		$attr = json_decode($val['attributes'],1);
		    		if($val['status'] == 'BOOKING_INPROGRESS'){ ?>
		    			<tr>
				    		<input type="hidden" class="pass_fk" name="pass_fk[]" value="<?php echo $val['origin']; ?>"/>
				    		<input class="" type="hidden" name="status[]" value="<?php echo $val['status']; ?>"/>
				    		<td><?php echo $k+1; ?></td>
				            <td><?php echo $val['title'].' '.$val['first_name'].' '.$val['last_name']; ?></td>
				            <td><?php echo $val['passenger_type']; ?></td>
				            <td><input class="TotalPrice input-field" type="text" name="TotalPrice[]" value="<?php echo $attr['TotalPrice']; ?>" readonly/></td>
				            <td><input class="Tax input-field" type="text" name="Tax[]" value="<?php echo $attr['Tax']; ?>" readonly/></td>
				            <td><?php echo $val['TicketNumber']; ?></td>
				            <td><input class="sup_cancel_charge input-field" id="sup-charge<?php echo $val['origin']; ?>" type="text" name="sup_cancel_charge[]" value="0"/></td>
				            <td><input class="pace_cancel_charge input-field" type="text" id="pace-charge<?php echo $val['origin']; ?>" name="pace_cancel_charge[]" value="0"/></td>
				            <td><input class="agent_can_charge input-field" id="agent-charge<?php echo $val['origin']; ?>" type="text" name="agent_cancel_charge[]" value="0"/></td>
				        </tr>
		    		<?php } ?>
		    	<?php } ?>
		    </tbody>
		</table>
	</div>

	<div class="row">
		<div class="col-sm-offset-8 col-sm-4 nopad text-right">
			<button type="button" id="refundAmt" class="btn btn-primary btn-common">Calculate Refund</button>
			<button type="button" class="btn btn-danger btn-common" data-toggle="modal" data-target="#exampleModal">Reject</button>
		</div>
	</div>
	<!-- <button type="button" id="refundAmt">Calculate Refund</button>
	Button trigger modal
	<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
  Launch demo modal
</button> -->
	<fieldset class="mt-2" form="refund_details" id="cal_refund_details" style="display:none">
		<?php if($is_agent == true){ ?>
		<input class="hide" id="app_reference" type="hidden" name="app_reference" value="<?=$app_reference?>">
		<input class="hide" type="hidden" name="passenger_origin" value="<?=$passenger_origin?>">
		<input class="hide" type="hidden" name="passenger_status" value="<?=$passenger_status?>">
		<input class="hide" type="hidden" name="refund_payment_mode" value="online">
		<?php if($is_agent == true){ ?>
			<legend class="form_legend">Refund to <?=$booked_user_details['agency_name'];?> Agent-<?=provab_decrypt($booked_user_details['uuid'])?></legend>
		<?php } else{ ?>
			<legend class="form_legend">Refund to B2C User</legend>
		<?php } ?>
		
	<?php if($agent_refund_status != 'PROCESSED'){ ?><!-- agent Refund Status Condition Starts -->
		<div class="row">
			<div class="col-sm-6">
			</div>
			<div class="col-sm-6">

				<div class="row mt-1">
					<div class="col-sm-5">
						<label form="refund_details" class="control-label">Supplier Cancellation charge</label>
						</div>
						<div class="col-sm-7">
							<input type="text" id="sup_cancel_charge1" data-placement="bottom" class="form-control" placeholder="Supplier Cancellation charge" name="Sup_can_charge" value="" readonly>
						</div>
				</div>
				<div class="row mt-1">
					<div class="col-sm-5">
						<label form="refund_details" class="control-label">Agent Cancellation Charge</label>
						</div>
						<div class="col-sm-7">
							<input type="text" id="agent_can_charge1" data-placement="bottom" class="form-control" placeholder="Agent Cancellation Charge" name="cancellation_charge" value="" readonly="">
						</div>
				</div>
				<div class="row mt-1">
					<div class="col-sm-5">
						<label form="refund_details" class="control-label">Pace Charges</label>
						</div>
						<div class="col-sm-7">
							<input type="text" id="pace_cancel_charge1" data-placement="bottom" class="form-control" placeholder="Pace charges" name="pace_charge" value="" readonly>
						</div>
				</div>
				<div class="row mt-1">
					<div class="col-sm-5">
						<label form="refund_details" class="control-label">Refund Amount</label>
						</div>
						<div class="col-sm-7">
							<input type="text" id="refund_amt" data-placement="bottom" class="form-control" placeholder="Refund Amount" name="refund_amount" value="" readonly>
						</div>
				</div>
				<div class="row mt-1">
					<div class="col-sm-5">
						<label form="refund_details" class="control-label">GST</label>
					</div>
					<div class="col-sm-7">
							<input type="text" id="GST" data-placement="bottom" class="form-control" placeholder="GST" name="GST" value="">
						</div>
				</div>
				
				<div class="row mt-1">
					<div class="col-sm-5">
						<label form="refund_details" class="control-label">Comments<span class="text-danger">*</span></label>
					</div>
					<div class="col-sm-7">
						<textarea type="text" class="form-control" id="comment" placeholder="Comments" name="refund_comments" value="" required=""><?=$agent_refund_comments?></textarea>
					</div>
				</div>

				<div class="row mt-2">
					<div class="col-sm-7 col-sm-offset-5" >
						<button class=" btn btn-primary btn-common" type="reset">Reset</button>
						<button class=" btn btn-success btn-common" id="update_refund" type="submit">Update Refund Details</button>
					</div>
					
				</div>

			</div>
		</div>
		
	<?php } ?><!-- agent Refund Status Condition Ends -->
	</fieldset>
	<?php } ?>
	</form>
 </div>
</div>

</div>

</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Reject Refund For App_reference <?php echo $app_reference; ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
       <textarea type="text" class="form-control" id="reject_comment" placeholder="Rejection Reason" name="refund_comments1" value="" required=""></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary width-150" data-dismiss="modal">Close</button>
        <button class=" btn btn-danger width-150" id="rejected" type="button">Reject</button>
      </div>
    </div>
  </div>
</div>
<input type="hidden" name="base_url" id="base_url" value="<?php echo base_url(); ?>">
<!-- PANEL BODY END --></div>
<!-- PANEL WRAP END --></div>
<!-- HTML END --> 
<script type="text/javascript">
$(document).ready(function(){
	//*******************Submit******************************
	$('#refundAmt').click(function(e){
		$('#cal_refund_details').removeAttr("style");
		refundAmt();
	});


	$("#rejected").click(function()
    {    
    var base_url = $('#base_url').val(); 
    var app_ref  = $("#app_reference").val();
    var pass_fk  = '';
    var comment  = $('#reject_comment').val();
    $(".pass_fk" ).each(function() {
		pass_fk = parseFloat($(this).val());
		$.ajax({
         type: "POST",
         url: base_url + "flight/reject_refund", 
         data: {app_ref: app_ref,pass_fk:pass_fk,comment:comment},
         dataType: "text",  
         cache:false,
         success: 
              function(data){
              console.log("updated....");  
              }
          });
	});
	window.location.href = base_url+'report/cancellation_queue';
 });



	function refundAmt(){
		var TotalPrice = 0;
		var Refund = 0;
		var GST = 0;
		var supCancelCharge = 0;
		var agentCanCharge = 0;
		var paceCancelCharge = 0;
		var Tax = 0;

		$( ".pass_fk" ).each(function() {
			var pass_fk = parseFloat($(this).val());
			var sup_charge_id = '#sup-charge'+pass_fk;
			var agent_charge_id = '#agent-charge'+pass_fk;
			var pace_charge_id = '#pace-charge'+pass_fk;
			var supCharge = $(sup_charge_id).val();
			var paceCharge = $(pace_charge_id).val();
			var GST1 = (18/100) * paceCharge;
			var amt = parseFloat(supCharge) + parseFloat(paceCharge) + parseFloat(GST1);
			$(agent_charge_id).val(amt);
		});

		$( ".TotalPrice" ).each(function() {
			TotalPrice = parseFloat(TotalPrice) + parseFloat($(this).val());
		});
		$( ".Tax" ).each(function() {
			Tax = parseFloat(Tax) + parseFloat($(this).val());
		});
		$( ".sup_cancel_charge" ).each(function() {
			supCancelCharge = parseFloat(supCancelCharge) + parseFloat($(this).val());
		});
		$( ".agent_can_charge" ).each(function() {
			agentCanCharge = parseFloat(agentCanCharge) + parseFloat($(this).val());
		});
		$( ".pace_cancel_charge" ).each(function() {
			paceCancelCharge = parseFloat(paceCancelCharge) + parseFloat($(this).val());
		});

		let Total_fare = parseFloat(TotalPrice) + parseFloat(Tax);
		if(paceCancelCharge > 0){
			GST = (18/100) * paceCancelCharge;
		}
		Refund = parseFloat(Total_fare) - parseFloat(supCancelCharge + paceCancelCharge + GST);

		$('#sup_cancel_charge1').val(supCancelCharge);
		$('#agent_can_charge1').val(agentCanCharge);
		$('#pace_cancel_charge1').val(paceCancelCharge);
		$('#refund_amt').val(Refund);
		$('#GST').val(GST);
		
	}



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
<style type="text/css">
	.content-wrapper{
	    padding: 0!important;
	  }
	.btn-common{
	    width: 48%;
	    margin-left: 5px;
	  }
	.panel-body{
	    box-shadow: 0 5px 11px 0 rgba(0,0,0,0.18),0 4px 15px 0 rgba(0,0,0,0.15) !important;
	    margin-bottom: 20px;
	}
	.panel{
		border: none;
	}
	#refund_form table, th, td{
		border: 1px solid #ccc!important;
	}
	.input-field{
		border: 1px solid #ccc;
		width: 100%;
	}
	.mt-2{
		margin-top: 20px;
	}
	.mt-1{
		margin-top: 10px;
	}
	.modal-header{
		background-color: #3c8dbc;
		color: #fff;
	}
	.width-150{
		width: 150px;
	}
	.modal-header .close {
	    margin-top: -20px;
	}
</style>
