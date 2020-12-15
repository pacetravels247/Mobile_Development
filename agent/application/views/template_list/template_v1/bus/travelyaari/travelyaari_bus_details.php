<?php
$CUR_Route = ($details['Route']);
$CUR_Layout = $details['Layout'];
$CUR_Pickup = force_multple_data_format(@$details['Pickup']['clsPickup']);
$CUR_Dropoff = force_multple_data_format(@$details['Dropoff']['clsDropoff']);
$CUR_CancellationCharges = force_multple_data_format($details['CancellationCharges']['clsCancellationCharge']);
require_once 'travelyaari_seat_layout.php';
echo '<hr>';
?>
<div class="row details-wrapper">
	<div class="col-md-6">
		<div class="form-group">
			<label class="control-label">Choose boarding point<span class="text-danger">*</span></label>	
			<select class="form-control boarding-point">
			<option value="INVALIDIP">--- boarding points ---</option>
			<?php
				if (valid_array($CUR_Pickup) == true) {
					foreach ($CUR_Pickup as $__pk => $__pv) {
						echo '<option value="'.$__pv['PickupId'].'">'.get_time($__pv['PickupTime']).' - '.$__pv['PickupName'].'</option>';
					}
				}
			?>
			</select>
		</div>
	</div>
	<div class="col-md-offset-2 col-md-2">
		<form class="booking-form" action="<?=base_url().'index.php/bus/booking/'.$search_id?>" method="POST" target="">
			<div class="booking-attr">
			<!--
			//SeatNo
			//Fare
			//SeatType
			//IsAcSeat
			-->
			<input type="hidden" name="route_schedule_id" class="route-schedule-id" value="<?=$CUR_Route['RouteScheduleId']?>">
			<input type="hidden" name="journey_date" class="journey-date" value="<?=$CUR_Route['JourneyDate']?>">
			<input type="hidden" name="pickup_id" class="pickup-id" value="">
				<div class="seat-summary">
				</div>
			<input type="hidden" name="booking_source" class="booking-source" value="<?=PROVAB_BUS_BOOKING_SOURCE?>">
			<input type="hidden" name="token" class="token" value="<?php echo $seat_no_summary = serialized_data($details)?>">
			<input type="hidden" name="token_key" class="token_key" value="<?=md5($seat_no_summary)?>">
			</div>
			<input type="button" class="btn btn-p b-r-0 b-btn" value="Continue">
		</form>
	</div>
</div>