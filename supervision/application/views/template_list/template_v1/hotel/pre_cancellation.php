<?php
	$booking_details = $data['booking_details'][0];
	extract($booking_details);
	$default_view['default_view'] = $GLOBALS ['CI']->uri->segment (1);
	$hotel_checkin_date = hotel_check_in_out_dates($hotel_check_in);
	$hotel_checkin_date = explode('|', $hotel_checkin_date);
	$hotel_checkout_date = hotel_check_in_out_dates($hotel_check_out);
	$hotel_checkout_date = explode('|', $hotel_checkout_date);
?>
<div class="search-result">
	<div class="container">
	<div class="bakrd_color">
         <div class="clearfix"></div>
    	<div class="cancellation_page">
        	<div class="head_can">
            	<h3 class="canc_hed">Cancellation</h3>
                <div class="ref_number">
                	<div class="rows_cancel">Booking ID: <strong><?=$app_reference?></strong></div>
                    <div class="rows_cancel">Booking Date: <?=$voucher_date?></div>
                </div>
            </div>
            <div class="clearfix"></div>
            <!-- Hotel Booking Details starts-->
			<div class="toprom">
			    <div class="col-xs-8 nopad full_room_buk">
			        <div class="bookcol">
			            <div class="hotelistrowhtl">
			                <div class="col-md-4 nopad xcel">
			                    <div class="imagehotel"><img src="<?=$hotel_image?>" alt="HotelImage"></div>
			                </div>
			                <div class="col-md-8 padall10 xcel">
			                    <div class="hotelhed"><?=$hotel_name?></div>
			                    <div class="clearfix"></div>
			                    <div class="bokratinghotl rating-no">
			                    <?=print_star_rating($star_rating)?>
			                    </div>
			                    <div class="clearfix"></div>
			                    <div class="mensionspl"> <?=$hotel_address?> </div>
			                 </div>
			            </div>
			        </div>
			    </div>
			    <div class="col-xs-4 nopadR full_room_buk">
			        <div class="sckint">
			            <div class="ffty">
			                <div class="borddo brdrit"> <span class="lblbk_book">
			                <span class="fa fa-calendar"></span> Check-in</span>
			                    <div class="fuldate_book"> <span class="bigdate_book"><?=$hotel_checkin_date[0]?></span>
			                        <div class="biginre_book"> <?=$hotel_checkin_date[1]?><br> <?=$hotel_checkin_date[2]?> </div>
			                    </div>
			                </div>
			            </div>
			            <div class="ffty">
			                <div class="borddo"> <span class="lblbk_book"> <span class="fa fa-calendar"></span> Check-out</span>
			                    <div class="fuldate_book"> <span class="bigdate_book"><?=$hotel_checkout_date[0]?></span>
			                        <div class="biginre_book"> <?=$hotel_checkout_date[1]?><br> <?=$hotel_checkout_date[2]?> </div>
			                    </div>
			                </div>
			            </div>
			            <div class="clearfix"></div>
			            <div class="nigthcunt">Night(s) <?=$total_nights?>, Room(s) <?=$total_rooms?></div>
			        </div>
			    </div>
			</div>
            <!-- Hotel Booking Details Ends-->
            <div class="clearfix"></div>
            <div class="cancel_bkd">
            <div class="row_can_table hed_table">
            	<div class="col-xs-2 nopad">
                	<div class="can_pads">Passenger Name</div>
                </div>
                <div class="col-xs-2 nopad">
                	<div class="can_pads">Type</div>
                </div>
                <div class="col-xs-2 nopad">
                	<div class="can_pads">Confirmation RefNumber</div>
                </div>
                <div class="col-xs-2 nopad">
                	<div class="can_pads">Booking ID</div>
                </div>
            </div>
		    <?php foreach($customer_details as $customer_k => $customer_v) {
            	extract($customer_v);
            	$pax_name = $title.' '.$first_name.' '.$last_name;
            ?>        
            	<div class="row_can_table">
	            	<div class="col-xs-2 nopad">
	                	<div class="can_pads"><?=$pax_name?></div>
	                </div>
	                <div class="col-xs-2 nopad">
	                	<div class="can_pads"><?=$pax_type?></div>
	                </div>
	                <div class="col-xs-2 nopad">
	                	<div class="can_pads"><?=$confirmation_reference?></div>
	                </div>
	                <div class="col-xs-2 nopad">
	                	<div class="can_pads"><?=$booking_id?></div>
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
        <a href="<?=base_url().'index.php/hotel/cancel_booking/'.$app_reference.'/'.$booking_source?>" class="btn btn-danger">Yes</a>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Confirm Cancealltion Ends-->