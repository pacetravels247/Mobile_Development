<div class="bodyContent col-md-12">
	<div class="panel panel-default clearfix">
		<!-- PANEL WRAP START -->
		<div class="panel-heading">
			<!-- PANEL HEAD START -->
			<div class="panel-title">
				<ul id="myTab" role="tablist" class="nav nav-tabs  ">
					<h1>Transaction / PNR Search</h1>
				</ul>
			</div>
		</div>
		<!-- PANEL HEAD START -->
		<div class="panel-body">
			<!-- PANEL BODY START -->
			<div class="tab-content">
				<div role="" class="" id="fromList">
					<div class="col-md-12">
						<div class="panel">
							<form autocomplete="off" name="pnr_search" id="pnr_search"
								action="" method="GET" class="activeForm oneway_frm" style="">
								<div class="row">
									<div class="col-sm-3 date-wrapper">
										<div class="form-group">
											<label for="bus-date-1">PNR Number</label>
											<div class="input-group">
												<input type="text"
													class="auto-focus hand-cursor form-control b-r-0" id=""
													placeholder="PNR Number" value="" name="filter_report_data" required>
											</div>
										</div>
									</div>
									<div class="col-sm-3 date-wrapper">
										<div class="form-group">
											<label for="bus-date-1">Module</label>
											<div class="input-group">
											<?php if(is_active_airline_module()){?>
												<input type="radio" name="module"
													value="<?php echo PROVAB_FLIGHT_BOOKING_SOURCE ?>" checked> Flight
												<?php } if (is_active_bus_module()){?>
												<input type="radio" name="module"
													value="<?php echo PROVAB_BUS_BOOKING_SOURCE ?>"> Bus
												<?php } if (is_active_hotel_module()){?>
												<input type="radio" name="module"
													value="<?php echo PROVAB_HOTEL_BOOKING_SOURCE ?>"> Hotel
												<?php }?> 
												<?php if(is_active_transferv1_module()){ ?>
												<input type="radio" name="module"
													value="<?php echo PROVAB_TRANSFERV1_BOOKING_SOURCE ?>">Transfers
												<?php }?>
												<?php if(is_active_sightseeing_module()){ ?>
												<input type="radio" name="module"
													value="<?php echo PROVAB_SIGHTSEEN_BOOKING_SOURCE ?>">Activities
												<?php }?>


											</div>
										</div>
									</div>
									<div class="pull-left">
										<button type="submit" name="" id="form-submit"
											class="btn btn-lg btn-i b-r-0 bus_search_btn">Search</button>
									</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<?php
			//Only Visible if module is Flight 
			if(@$module == 'Flight'){?>
			<div class="tab-content">
				<div id="tableList" class="">
					<table class="table table-condensed table-bordered">
					<tr><th colspan="10"><center>Flight Details</center></th></tr>
						<tr>
							<th>Sno</th>
							<th>Application <br>Reference</th>
							<th>Status</th>
							<th>Base Fare</th>
							<th>Markup</th>
							<th>Customer</th>
							<th>Booking</th>
							<th>Total Ticket(s)</th>
							<th>Booked On</th>
							<th>Action</th>
						</tr>
						<?php
				if (isset ( $table_data ) == true and valid_array ( $table_data )) {
					//debug($table_data);exit;
					foreach ($table_data as $k => $v){
						$current_record = 0;
						extract ( $v );
						$action = '';
						$cancellation_btn = '';
						$voucher_btn = '';
						$status_update_btn = '';
						if (strtotime ( $journey_start ) > time () and (status == BOOKING_PENDING || $status == BOOKING_VOUCHERED || $status == BOOKING_CONFIRMED || $status == BOOKING_HOLD)) {
							// $cancellation_btn = get_accomodation_cancellation($api_code, $v['reference']);
						}
						// Status Update Button
						if (in_array ( $status, array (
								'BOOKING_CONFIRMED' 
						) ) == false) {
							switch ($booking_source) {
								case PROVAB_FLIGHT_BOOKING_SOURCE :
									$status_update_btn = '<button class="btn btn-success btn-sm update-source-status" data-app-reference="' . $app_reference . '"><i class="fa fa-database"></i> Update Status</button>';
									break;
							}
						}
						$voucher_btn = flight_voucher ( $app_reference, $booking_source, $status );
						$action = $voucher_btn . $status_update_btn . $cancellation_btn;
						?>
						<tr>
							<td><?=($current_record+$k+1)?></td>
							<td><?=$app_reference;?></td>
							<td><span class="<?=booking_status_label($status) ?>"><?=$status?></span></td>
							<td><?php echo $currency.':'.($total_fare+$domain_markup)?></td>
							<td><?php echo $level_one_markup?></td>
							<td><?php echo $name.'<br>Email:<br>'.$email?><br><?php echo 'P:'.$phone_number?></td>
							<td><strong><?php echo $journey_from.'</strong><br> @'.app_friendly_datetime($journey_start).' <br><strong><i> to </i><br> '.$journey_to?></strong><br><?php echo ' @'.app_friendly_datetime($journey_end)?></td>
							<td><?php echo $total_passengers?></td>
							<td><?php echo app_friendly_absolute_date($created_datetime)?></td>
							<td><div class="" role="group"><?php echo $action; ?></div></td>
						</tr>
								<?php
					}
				} else {
					echo '<tr><td colspan="12">No Data Found</td></tr>';
				}
				?>
					</table>
				</div>
			</div>
<script>
$(document).ready(function() {
	//update-source-status update status of the booking from api
	$(document).on('click', '.update-source-status', function(e) {
		e.preventDefault();
		$(this).attr('disabled', 'disabled');//disable button
		var app_ref = $(this).data('app-reference');
		$.get(app_base_url+'flight/get_booking_details/'+app_ref, function(response) {
			
		});
	});
});
</script>
<?php
function get_accomodation_cancellation($courseType, $refId) {
	return '<a href="' . base_url () . 'booking/accomodation_cancellation?courseType=' . $courseType . '&refId=' . $refId . '" class="btn btn-sm btn-danger "><i class="fa fa-exclamation-triangle"></i> Cancel</a>';
}
?>
			<?php } 
			// Only Visible if module is Bus			 
			if(@$module == 'Bus'){?>
			<div class="tab-content">
				<div id="tableList" class="">
					<table class="table table-condensed table-bordered ">
					<tr><th colspan="11"><center>Bus Details</center></th></tr>
						<tr>
							<th>Application <br> Reference</th>
							<th>Status</th>
							<th>PNR/Ticket</th>
							<th>Total Fare</th>
							<th>Customer</th>
							<th>Booking</th>
							<th>journey</th>
							<th>Operator</th>
							<th>Booked On</th>
							<th>Action</th>
						</tr>
						<?php
					if (isset ( $table_data ) == true and valid_array ( $table_data )) {
					foreach ( $table_data as $k => $v ) {
						// get cancel button only if check in date has not passed and api cancellation is active
						// ONLY AOT IS ACTIVE FOR CANCELLATION
						$api_code = '';
						$action = '';
						if ($v ['booking_source'] == PROVAB_BUS_BOOKING_SOURCE) {
							$api_code = PROVAB_BUS_BOOKING_SOURCE;
							if (strtotime ( $v ['departure_datetime'] ) > time () and ($v ['status'] == BOOKING_CONFIRMED || $v ['status'] == BOOKING_HOLD)) {
								// $action .= get_accomodation_cancellation($api_code, $v['reference']);
							}
						}
						if (empty ( $api_code ) == false) {
							$action .= bus_voucher ( $v ['app_reference'], $api_code, $v ['status'] );
						}
						$customer = explode ( DB_SAFE_SEPARATOR, $v ['name'] );
						?>
						<tr>
							<td><?php echo $v['app_reference'];?></td>
							<td><span
								class="<?php echo booking_status_label($v['status']) ?>"><?php echo $v['status']?></span></td>
							<td class=""><span><?php echo 'PNR:<br>'.$v['pnr']?></span><br>
							<span><?php echo 'Tick:<br>'.$v['ticket']?></span></td>
							<td><?php echo $v['currency'].':'.($v['total_fare']+$v['level_one_markup']+$v['domain_markup'])?></td>
							<td><?php echo $customer[0].'<br>Email:<br>'.$v['email']?><br><?php echo 'P:'.$v['phone_number']?><br><?php echo 'O:'.$v['alternate_number']?></td>
							<td><?php echo $v['departure_from']?><br>to<br><?php echo $v['arrival_to']?><br>(<?php echo $v['total_passengers']?> <?=(intval($v['total_passengers']) > 1 ? 'tickets' : 'ticket' )?>)</td>
							<td><?php echo app_friendly_datetime($v['departure_datetime'])?></td>
							<td><?php echo $v['operator']?></td>
							<td><?php echo app_friendly_absolute_date($v['created_datetime'])?></td>
							<td><div class="" role="group"><?php echo $action; ?></div></td>
						</tr>
								<?php
					}
				} else {
					echo '<tr><td colspan="12">No Data Found</td></tr>';
				}
				?>
					</table>
				</div>
			</div>
			<?php } 
			// Only Visible if module is Hotel
			if(@$module == 'Hotel') {?>
			<div class="tab-content">
				<div id="tableList" class="">
					<table class="table table-condensed table-bordered">
					`	<tr><th colspan="12"><center>Hotel Details</center></th></tr>
						<tr>
							<th>Application <br>Reference</th>
							<th>Status</th>
							<th>Confirmation/<br>Reference</th>
							<th>Total Fare</th>
							<th>Payment Mode</th>
							<th>Customer</th>
							<th>Booking</th>
							<th>Check-In</th>
							<th>Hotel</th>
							<th>Booked <br>On</th>
							<th>Action</th>
						</tr>
						<?php
				if (isset ( $table_data ) == true and valid_array ( $table_data )) {
					foreach ( $table_data as $k => $v ) {
						extract ( $v );
						// get cancel button only if check in date has not passed and api cancellation is active
						// ONLY AOT IS ACTIVE FOR CANCELLATION
						$action = '';
						if ($v ['booking_source'] == PROVAB_HOTEL_BOOKING_SOURCE) {
							if (strtotime ( $v ['hotel_check_in'] ) > time () and ($v ['status'] == BOOKING_PENDING || $v ['status'] == BOOKING_VOUCHERED || $v ['status'] == BOOKING_CONFIRMED || $v ['status'] == BOOKING_HOLD)) {
								// $action .= get_accomodation_cancellation($api_code, $v['reference']);
							}
						}
						$action .= hotel_voucher ( $v ['app_reference'], $booking_source, $v ['status'] );
						?>
						<tr>
							<td><?=$app_reference;?></td>
							<td><span class="<?=booking_status_label($status) ?>"><?=$status?></span></td>
							<td class=""><span><?php echo 'Conf:'.$confirmation_reference?></span><br>
							<span><?php echo 'Ref:'.$booking_reference?></span></td>
							<td><?php echo $currency.':'.($total_fare+$level_one_markup+$domain_markup)?></td>
							<td><?php echo $payment_name?></td>
							<td><?php echo $name.'<br>Email:<br>'.$email?><br><?php echo 'P:'.$phone_number?></td>
							<td><?php echo app_friendly_absolute_date($hotel_check_in)?> <br> to <br> <?php echo app_friendly_date($hotel_check_out)?></td>
							<td><?php echo $total_passengers?> Pax, <br><?php echo $v['total_rooms']?> <?=(intval($total_rooms) > 1 ? 'Rooms' : 'Room' )?></td>
							<td><?php echo $hotel_name?></td>
							<td><?php echo app_friendly_absolute_date($created_datetime)?></td>
							<td><div class="" role="group"><?php echo $action; ?></div></td>
						</tr>
								<?php
					}
				} else {
					echo '<tr><td colspan="12">No Data Found</td></tr>';
				}
				?>	
					</table>
				</div>
			</div>
			<?php
				function get_accomodation_cancellation($courseType, $refId) {
					return '<a href="' . base_url () . 'booking/accomodation_cancellation?courseType=' . $courseType . '&refId=' . $refId . '" class="col-md-12 btn btn-sm btn-danger "><i class="fa fa-exclamation-triangle"></i> Cancel</a>';
				}
				?>
			<?php } ?>
		</div>
	</div>
</div>