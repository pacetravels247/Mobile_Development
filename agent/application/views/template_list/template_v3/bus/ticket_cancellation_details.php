<?php
$booking_details = $booking_data['booking_details'][0];
$booking_customer_details = $booking_details['booking_customer_details'];

$app_reference = $booking_details['app_reference'];
$booking_source = $booking_details['booking_source'];
$attributes = $booking_details ['attributes'];
//debug($booking_details); exit;
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

	<table class="table table-condensed table-bordered table-striped">
		<tr>
			<td colspan="8" align="center">
				<strong>Passenger(s)- PNR: <?=@$booking_details['pnr'] ?> </strong>
			</td>
		</tr>
		<br>
		<tr>
			<td><strong>SlNo</strong></td>
			<td><strong>Passenger Name</strong></td>
			<td><strong>AppReference</strong></td>
			<td><strong>Seat No.</strong></td>
			<td><strong>Booking Id</strong></td>
			<td><strong>Refund Status</strong></td>
			<td><strong>Status</strong></td>
			<td><strong>Action</strong></td>
		</tr>
		<?php foreach($booking_customer_details as $key => $value){
		 	$action = '';
		 	$action .= get_cancellation_details_button($app_reference, $booking_source, $value['status'], $value['origin']);
		 	$refund_status = "Processed"; //$cus_v['cancellation_details']['refund_status'];
			 ?>  
		<tr>
			<td><?=($key+1)?></td>
			<td><?=$value['title'].' '.$value['name'];?></td>
			<td><?=$value['app_reference'];?></td>
			<td><?=$value['seat_no']?></td>
			<td><?=$booking_details['ticket'];?></td>
			<td><span class="<?=refund_status_label($refund_status)?>"><?=$refund_status?></span></td>
			<td><span class="<?=booking_status_label($value['status']) ?>"><?=$value['status']?></span></td>
			<td><?=$action;?></td>
		</tr>
		<?php } ?>
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
		return '<a target="_blank" href="'.base_url().'bus/cancellation_refund_details?app_reference='.$app_reference.'&booking_source='.$booking_source.'&status='.$passenger_status.'&passenger_origin='.$passenger_origin.'" class="btn btn-sm btn-info "><i class="fa fa-info"></i> Cancellation Details</a>';
	}
}
?>
