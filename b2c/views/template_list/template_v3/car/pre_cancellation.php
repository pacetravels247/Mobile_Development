<?php
	$booking_details = $data['booking_details'][0];
    // debug($booking_details);exit;
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
                    <div class="rows_cancel">Booking Date: <?=$created_datetime?></div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="cancel_bkd">
            <?php foreach($itinerary_details as $itinerary_k => $itinerary_v) {
            	extract($itinerary_v); 
                $pickup_datetime = $car_from_date.' '.$pickup_time;
                $drop_datetime = $car_to_date.' '.$drop_time;
            ?>
            	<div class="col-xs-3 nopad">
                	<div class="pad_evry">
                    	<div class="imge_can"><span class="fa fa-bus"></span></div>
                        <div class="can_flt_name"><?=$car_supplier_name?><strong><?=$car_name?>, <?=$car_model?></strong></div>
                    </div>
                </div>
                <div class="col-xs-4 nopad">
                	<div class="pad_evry">
                    	<span class="place_big_text"><?=ucfirst($car_pickup_lcation)?></span>
                        <span class="date_mension"><?=app_friendly_datetime($pickup_datetime)?></span>
                    </div>
                </div>
                <div class="col-xs-1 nopad">
                    <div class="pad_evry">
                        <div class="aroow_can fa fa-long-arrow-right"></div>
                    </div>
                </div>
                <div class="col-xs-4 nopad">
                	<div class="pad_evry">
                    	<span class="place_big_text"><?=ucfirst($car_drop_location)?></span>
                        <span class="date_mension"><?=app_friendly_datetime($drop_datetime)?></span>
                    </div>
                </div>
                <?php } ?>
            
            
            <div class="clearfix"></div>
            

            <div class="row_can_table hed_table">
            	<div class="col-xs-2 nopad">
                	<div class="can_pads">Passenger Frirst Name</div>
                </div>
                <div class="col-xs-2 nopad">
                	<div class="can_pads">Passenger Frirst Name</div>
                </div>
                <div class="col-xs-3 nopad">
                	<div class="can_pads">PNR</div>
                </div>
                <div class="col-xs-2 nopad">
                	<div class="can_pads">Phone number</div>
                </div>
                <div class="col-xs-2 nopad">
                	<div class="can_pads">Status</div>
                </div>
            </div>
		    <?php foreach($customer_details as $customer_k => $customer_v) {
            	extract($customer_v);
            	$pax_name = $first_name;
            ?>        
            	<div class="row_can_table">
	            	<div class="col-xs-2 nopad">
	                	<div class="can_pads"><?=$pax_name?></div>
	                </div>
	                <div class="col-xs-2 nopad">
	                	<div class="can_pads"><?=$last_name?></div>
	                </div>
	                <div class="col-xs-3 nopad">
	                	<div class="can_pads"><?=$booking_reference?></div>
	                </div>
	                <div class="col-xs-2 nopad">
	                	<div class="can_pads"><?=$phone?></div>
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
                    	<div class="amnt_paid"><?php echo $booking_details['currency'];?> <?=$grand_total?></div>
                    </div>
                </div>
             </div>
             <div class="col-xs-6 nopad">   
                <div class="btn_continue">
                	<button data-toggle="modal" data-target="#confirm_cancel" class="btn btn-warning" type="button">Confirm</button>
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
        <a href="<?=base_url().'index.php/car/cancel_booking/'.$app_reference.'/'.$booking_source?>" class="btn btn-danger">Yes</a>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Confirm Cancealltion Ends-->