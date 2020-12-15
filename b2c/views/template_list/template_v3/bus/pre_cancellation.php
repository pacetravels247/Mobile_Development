<?php
	$booking_details = $data['booking_details'][0];
	extract($booking_details);
	$default_view['default_view'] = $GLOBALS ['CI']->uri->segment (1);
?>
<div class="search-result">
	<div class="container">
		<?php echo $GLOBALS['CI']->template->isolated_view('share/profile_navigator_tab') ?>
	<div class="bakrd_color">
		<div class="cetrel_all">
			<?php echo $GLOBALS['CI']->template->isolated_view('share/navigation', $default_view) ?>
		 </div>
		 <div class="clearfix"></div>
		<div class="cancellation_page">
			<div class="head_can">
				<h3 class="canc_hed">Cancellation</h3>
				<div class="ref_number">
					<div class="rows_cancel">Booking ID: <strong><?=$app_reference?></strong></div>
					<div class="rows_cancel">Booking Date: <?=$booked_date?></div>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="cancel_bkd">
			<?php foreach($booking_itinerary_details as $itinerary_k => $itinerary_v) {
				extract($itinerary_v); 
			?>
				<div class="col-xs-3 nopad">
					<div class="pad_evry">
						<div class="imge_can"><span class="fa fa-bus"></span></div>
						<div class="can_flt_name"><?=$operator?><strong><?=$bus_type?> </strong></div>
					</div>
				</div>
				<div class="col-xs-4 nopad">
					<div class="pad_evry">
						<span class="place_big_text"><?=ucfirst($departure_from)?></span>
						<span class="date_mension"><?=app_friendly_datetime($departure_datetime)?></span>
					</div>
				</div>
				<div class="col-xs-1 nopad">
					<div class="pad_evry">
						<div class="aroow_can fa fa-long-arrow-right"></div>
					</div>
				</div>
				<div class="col-xs-4 nopad">
					<div class="pad_evry">
						<span class="place_big_text"><?=ucfirst($arrival_to)?></span>
						<span class="date_mension"><?=app_friendly_datetime($arrival_datetime)?></span>
					</div>
				</div>
				<?php } ?>
			
			
			<div class="clearfix"></div>
			

			<div class="row_can_table hed_table">
				<div class="col-xs-2 nopad">
					<div class="can_pads">Passenger Name</div>
				</div>
				<div class="col-xs-2 nopad">
					<div class="can_pads">Age</div>
				</div>
				<div class="col-xs-3 nopad">
					<div class="can_pads">PNR</div>
				</div>
				<div class="col-xs-2 nopad">
					<div class="can_pads">SeatNumber</div>
				</div>
				<div class="col-xs-2 nopad">
					<div class="can_pads">Status</div>
				</div>
			</div>
			<?php foreach($booking_customer_details as $customer_k => $customer_v) {
				extract($customer_v);
				$pax_name = $name;
			?>		
				<div class="row_can_table">
					<div class="col-xs-2 nopad">
						<div class="can_pads"><?=$pax_name?></div>
					</div>
					<div class="col-xs-2 nopad">
						<div class="can_pads"><?=$age?></div>
					</div>
					<div class="col-xs-3 nopad">
						<div class="can_pads"><?=$pnr?></div>
					</div>
					<div class="col-xs-2 nopad">
						<div class="can_pads"><?=$seat_no?></div>
					</div>
					<div class="col-xs-2 nopad">
						<div class="can_pads"><?=$status?></div>
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
						<div class="amnt_paid"><?php echo $booking_details['currency'];?> <?=$booking_details['grand_total']?></div>
					</div>
				</div>
			 </div>
			 <div class="col-xs-6 nopad">   
				<div class="btn_continue">
					<button data-toggle="modal" data-target="#confirm_cancel" class="b-btn bookallbtn" type="button">Confirm</button>
				</div>
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
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title">Do you want to cancel the Booking?</h4>
	  </div>
	  <!--<div class="modal-body"></div>-->
	  <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">No</button>
		<a href="<?=base_url().'index.php/bus/cancel_booking/'.$app_reference.'/'.$booking_source?>" class="btn btn-danger">Yes</a>
	  </div>
	</div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Confirm Cancealltion Ends-->