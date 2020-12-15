<?php
	$booking_details = $data['booking_details'][0];
	$master_booking_status = $booking_details['status'];
	extract($booking_details);
	$default_view['default_view'] = $GLOBALS ['CI']->uri->segment (1);
?>
<div class="search-result">
	<div class="container">
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
                    	<div class="imge_can"><img class="airline-logo" src="<?=SYSTEM_IMAGE_DIR.'airline_logo/'.$airline_code.'.gif'?>" alt="Flight Image"></div>
                        <div class="can_flt_name"><?=$airline_name?><strong><?=$airline_code?> <?=$flight_number?></strong></div>
                    </div>
                </div>
                <div class="col-xs-4 nopad">
                	<div class="pad_evry">
                    	<span class="place_big_text"><?=$from_airport_code?></span>
                        <span class="place_smal_txt"><?=$from_airport_name?></span>
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
                    	<span class="place_big_text"><?=$to_airport_code?></span>
                        <span class="place_smal_txt"><?=$to_airport_name?></span>
                        <span class="date_mension"><?=app_friendly_datetime($arrival_datetime)?></span>
                    </div>
                </div>
                <?php } ?>
                <form name="passenger_ticket_form" id="passenger_ticket_form" class="passenger_ticket_form" method="post" action="<?=base_url().'index.php/flight/cancel_booking'?>"><!-- Passenger Form Starts -->
            		<input type="hidden" name="app_reference" value="<?=$app_reference?>">
            		<input type="hidden" name="booking_source" value="<?=$booking_source?>">
	            <?php foreach($booking_transaction_details as $transaction_k => $transaction_v) {
						$trip_direction_label = '';
						if($transaction_k == 0 && count($booking_transaction_details) == 2) {
							$trip_direction_label = 'Onward ';
						} else if($transaction_k == 1){
							$trip_direction_label = 'Return';
						}
						extract($transaction_v);
	            ?>
            		<input type="hidden" name="transaction_origin[]" value="<?=$transaction_v['origin']?>">
            	<?php
            	if(empty($trip_direction_label) == false){ ?>
            		<div class="clearfix"></div>
	            	<div class="row_can_table hed_table">
		            	<div class="col-xs-2 nopad">
		                	<div class="can_pads"><u><strong><?=$trip_direction_label?> Passenger(s)</strong></u></div>
		                </div>
		            </div>
            	<?php } ?>
            	<div class="clearfix"></div>
            	<div class="row_can_table hed_table">
	            	<div class="col-xs-1 nopad">
	                	<div class="can_pads">Slno</div>
	                </div>
	                <div class="col-xs-2 nopad">
	                	<div class="can_pads">Passenger Name</div>
	                </div>
	                <div class="col-xs-2 nopad">
	                	<div class="can_pads">Type</div>
	                </div>
	                <div class="col-xs-2 nopad">
	                	<div class="can_pads">PNR</div>
	                </div>
	                <div class="col-xs-2 nopad">
	                	<div class="can_pads">TicketNumber</div>
	                </div>
	                <div class="col-xs-2 nopad">
	                	<div class="can_pads">Status</div>
	                </div>
            	</div>
			    <?php foreach($booking_customer_details as $customer_k => $customer_v) {
	            	extract($customer_v);
	            	$pax_name = get_enum_list('title', $title).' '.$first_name.' '.$last_name;
	            	if($customer_v['status'] == 'BOOKING_CONFIRMED'){
	            		$pax_check_box = '<input type="checkbox" name="passenger_origin[]" class="passenger_fk" value="'.$customer_v['origin'].'">';
	            	} else {
	            		$pax_check_box = '';
	            	}
	            ?>
	            	<div class="row_can_table">
	            		<div class="col-xs-1 nopad">
		                	<div class="can_pads can_check"><?=($customer_k+1)?>. <?=$pax_check_box?></div>
		                </div>
		            	<div class="col-xs-2 nopad">
		                	<div class="can_pads"><?=$pax_name?></div>
		                </div>
		                <div class="col-xs-2 nopad">
		                	<div class="can_pads"><?=$passenger_type?></div>
		                </div>
		                <div class="col-xs-2 nopad">
		                	<div class="can_pads"><?=$pnr?></div>
		                </div>
		                <div class="col-xs-2 nopad">
		                	<div class="can_pads"><?=$TicketNumber?></div>
		                </div>
		                <div class="col-xs-2 nopad">
		                	<div class="can_pads"><span class="<?=booking_status_label($status)?>"><?=$status?></span></div>
		                </div>
		           	 </div>
		           <?php } ?><!-- Passenger Loop Ends -->
           		 <?php } ?><!-- Transaction Loop Ends -->
            </form><!-- Passenger Form Ends -->
            </div>
            <div class="clearfix"></div>
            <div class="ritside_can col-xs-4 nopad">
            <div class="col-xs-6 nopad">
            	<div class="btn_continue">
                	<div class="amnt_disply">
                    	Total Amount Paid:
                    	<div class="amnt_paid"><?php echo $booking_details['currency'];?> <?=$grand_total?></div>
                    </div>
                </div>
             </div>
             <?php if($master_booking_status == 'BOOKING_CONFIRMED'){ ?>
	             	<div class="col-xs-6 nopad">   
		                <div class="btn_continue">
		                	<button id="pre_cancel_button" class="b-btn bookallbtn" type="button">Confirm</button>
		                </div>
	             	</div>
            <?php } ?>
            </div>
            
            
        </div>
    </div>
    </div>
</div>

<!-- Confirm Cancealltion Starts-->
<div class="modal fade" tabindex="-1" role="dialog" id="confirm_cancel_modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Do you want to cancel the Booking?</h4>
      </div>
      <!--<div class="modal-body"></div>-->
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
        <button type="button" id="confirm_cancellation_button" class="btn btn-danger">Yes</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Confirm Cancealltion Ends-->
<!-- Cancealltion Error Message Starts-->
<div class="modal fade" tabindex="-1" role="dialog" id="cancel_error_modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body"><h4>Please Select Passenger(s) to Cancel !!</h4></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Cancealltion Error Message Ends-->

<script>
$(document).ready(function() {
	//After Pax Selection
	$('#pre_cancel_button').click(function () {
		var pax_count = $('input:checkbox[name="passenger_origin[]"]:checked').length;
		if(pax_count > 0) {
			$('#confirm_cancel_modal').modal();
		} else{
			$('#cancel_error_modal').modal();
		}
	});
	$('#confirm_cancellation_button').click(function(){
		$('form#passenger_ticket_form').submit();
	});
});
</script>