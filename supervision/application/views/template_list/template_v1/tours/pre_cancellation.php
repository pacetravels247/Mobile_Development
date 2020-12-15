<?php
// debug($data['booking_vehicle_details'][0]['picture_url']);
$booking_details = $data ['booking_details'];
$tours_attribute = json_decode($booking_details['user_attributes'], true);
$tours_info = json_decode($booking_details['attributes'], true);
$tours_details = $data ['tours_details'];
extract ( $booking_details );
// debug($tours_info);die;
?>
<div class="search-result">
	<div class="bakrd_color">

		<div class="clearfix"></div>
		<div class="cancellation_page">
			<div class="head_can">
				<h3 class="canc_hed">Cancellation</h3>
				<div class="ref_number">
					<div class="rows_cancel">
						Reservation Code: <strong><?=$app_reference?></strong>
					</div>
					<div class="rows_cancel">Booking Date: <?=date("d-M-Y", strtotime($booked_datetime))?></div>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="cancel_bkd">
            <?php extract ( $tours_details ); ?>
            	<div class="col-xs-8 nopad">
					<div class="pad_evry">
						<div class="imge_can col-xs-4 nopad">
							<img class="airline-logo"
								src="<?=$this->template->domain_images($tours_details['banner_image'])?>"
								alt="Holiday Image">
						</div>
						<div class="col-xs-4 nopad"><?=$package_name?>
						</div>
					</div>
				</div>
				<div class="col-xs-4 nopad">
					<div class="pad_evry">
						<span class="place_big_text"><?=substr($pickup_location[0], 0, $stringlent);?></span> <span
							class="place_smal_txt"><?=substr($pickup_location[0], 0, $stringlent);?></span> <span
							class="date_mension"><?=app_friendly_absolute_date($tours_info['departure_date'])?></span>
					</div>
				</div>
				
            
            
            <div class="clearfix"></div>

				<div class="row_can_table hed_table">
					<div class="col-xs-4 nopad">
						<div class="can_pads">Passenger Name</div>
					</div>
					<div class="col-xs-4 nopad">
						<div class="can_pads">Supplier Confirmation Number</div>
					</div>
					<div class="col-xs-4 nopad">
						<div class="can_pads">Status</div>
					</div>
				</div>
            <?php
			// debug($data);									
	// debug($transaction_v);
													extract ( $transaction_v );
													?>
			    <?php
													
						$customer_v = $data['pax_details']; 
					foreach ($customer_v as $key => $value) {
														$pax_name = get_enum_list ( 'title', $value['pax_title'] ) . ' ' . $value['pax_first_name'] . ' ' . $value['pax_middle_name'] . ' ' . $value['pax_last_name'];
														?>        
	            	<div class="row_can_table">
					<div class="col-xs-4 nopad">
						<div class="can_pads"><?=$pax_name?></div>
					</div>
					<div class="col-xs-4 nopad">
						<div class="can_pads"><?=$app_reference?></div>
					</div>
					<div class="col-xs-4 nopad">
						<div class="can_pads"><?=get_flight_display_status($booking_details['status']);?></div>
					</div>
				</div>
				<?php } ?>
            
            </div>
			<div class="clearfix"></div>
			<div class="ritside_can col-xs-4 nopad">

				<div class="col-xs-6 nopad">
					<div class="btn_continue">
						<div class="amnt_disply">
							Total Amount Paid:
							<div class="amnt_paid"><?=$tours_attribute['currency_code']?> <?=$tours_attribute['total_amount']?></div>
						</div>
					</div>
				</div>
				<div class="col-xs-6 nopad">
					<div class="btn_continue">
						<button data-toggle="modal" data-target="#confirm_cancel"
							class="btn btn-warning" type="button">CONFIRM CANCELLATION</button>
					</div>
				</div>
			</div>


		</div>
	</div>
</div>

<!-- Confirm Cancealltion Starts-->
<div class="modal fade" tabindex="-1" role="dialog" id="confirm_cancel">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Do you want to cancel the Booking?</h4>
			</div>
			<!--<div class="modal-body"></div>-->
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">No</button>
				<a
					href="<?=base_url().'index.php/tours/cancel_full_booking/'.$app_reference?>"
					class="btn btn-danger">Yes</a>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<!-- Confirm Cancealltion Ends-->