<?php
$booking_details = $booking_data['booking_details'][0];
$booking_transaction_details = $booking_details['booking_transaction_details'];

$app_reference = $booking_details['app_reference'];
$booking_source = $booking_details['booking_source'];
$attributes = $booking_details ['attributes'];
?>
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

	<table class="table table-condensed table-bordered table-striped">
		<?php foreach($booking_transaction_details as $key => $value){
			$trip_direction_label = '';
			if($key == 0 && count($booking_transaction_details) == 2) {
			   $trip_direction_label = 'Onward ';
			   
			} else if($key == 1){
			   $trip_direction_label = 'Return';
			} ?>
		</tr> 
		<tr>
			<td colspan="7" align="center">
				<strong>Passenger(s)- PNR: <?=@$booking_transaction_details[$key]['pnr'] ?> </strong>
			</td>
		</tr>
		<?php if($key == 1){ ?>
		<br>
		<?php } ?>
		<tr>
			<td><strong>SlNo</strong></td>
			<td><strong>Passenger Name</strong></td>
			<td><strong>AppReference</strong></td>
			<td><strong>PNR</strong></td>
			<td><strong>Ticket Number</strong></td>
			<td><strong>Ticket Id</strong></td>
			<td><strong>TicketStatus</strong></td>
			<td><strong>RefundStatus</strong></td>
			<td><strong>Action</strong></td>
		</tr>
		<?php 
			if(isset($value['booking_customer_details'])){
			 foreach($value['booking_customer_details'] as $cus_k => $cus_v){
			 	$action = '';
			 	$action .= get_cancellation_details_button($app_reference, $booking_source, $cus_v['status'], $cus_v['origin']);
			 	$refund_status = $cus_v['cancellation_details']['refund_status'];
			 ?>  
		<tr>
			<td><?=($cus_k+1)?></td>
			<td><?=$cus_v['title'].' '.$cus_v['first_name'].' '.@$cus_v['last_name'];?></td>
			<td><?=$cus_v['app_reference'];?></td>
			<td><?=$booking_transaction_details[$key]['pnr']?></td>
			<td><?=$cus_v['TicketNumber'];?></td>
			<td><?=$cus_v['TicketId'];?></td>
			<td><span class="<?=booking_status_label($cus_v['status']) ?>"><?=$cus_v['status']?></span></td>
			<td><span class="<?=refund_status_label($refund_status)?>"><?=$refund_status?></span></td>
			<td><?=$action;?></td>
		</tr>
		<?php }
			}
			} ?>
	</table>
	</div>
	</div>
	</div>
	</div>
	</div>
</div>
<?php 
function get_cancellation_details_button($app_reference, $booking_source, $passenger_status, $passenger_origin)
{
	if($passenger_status == 'BOOKING_CANCELLED'){
		return '<a target="_blank" href="'.base_url().'flight/cancellation_refund_details?app_reference='.$app_reference.'&booking_source='.$booking_source.'&passenger_status='.$passenger_status.'&passenger_origin='.$passenger_origin.'" class="btn btn-sm btn-info "><i class="fa fa-info"></i> Cancellation Details</a>';
	}
}
?>
