<?php
	$booking_details = $data['booking_details'][0];
	extract($booking_details);
	$attributes= json_decode($attributes,true);
	$default_view['default_view'] = $GLOBALS ['CI']->uri->segment (1);
    $travel_date = sightseeing_travel_date($travel_date);
    $travel_date = explode("|",$travel_date);
	//debug ($travel_date); exit;

?>

<style type="text/css">
	
.fuldate_book {
    display: block;
    height: 64px;
    overflow: hidden;
    text-align: left;
    float: left;
    background: #f5f5f5;
    margin-bottom: 10px;
    padding: 0px 10px;
    border-radius: 5px;
}

.nigthcunt { line-height: 33px; font-size: 18px; }

</style>

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
				<div class="col-xs-12 nopad full_room_buk">
					<div class="bookcol">
						<div class="hotelistrowhtl">
							<div class="col-md-4 nopad">
								<div class="imagehotel"><img src="<?=$attributes['ProductImage']?>" alt="ProductImage"></div>
							</div>
							<div class="col-md-8 padall10 ">
								<div class="hotelhed"><?=$product_name?></div>
								<div class="clearfix"></div>
								<div class="bokratinghotl rating-no">
								<?=print_star_rating($star_rating)?>
								</div>
								<div class="clearfix"></div>
								<div class="mensionspl"> <?=$Destination?> </div>

								<div class="sckint">
						<div class="">
							<div class="borddo brdrit"> <span class="lblbk_book">
							<span class="fa fa-calendar"></span> Travel Date</span>
								<div class="fuldate_book"> <span class="bigdate_book"><?=$travel_date[0]?></span>
									<div class="biginre_book"> <?=$travel_date[1]?><br> <?=$travel_date[2]?> </div>
								</div>
							</div>
						</div>
						
						<div class="clearfix"></div>
						<div class="nigthcunt">Total Pax:<?=$adult_count+$child_count+$infant_count+$youth_count+$senior_count?></div>
					</div>

							 </div>
							 <div class="clearfix"></div>
							 
						</div>
					</div>

				</div>
				<div class="col-xs-4 nopad full_room_buk">
					
				</div>
			</div>
			<div class="clearfix"></div>
			<?php if($attributes['Cancellation_available']):?>
			<div>
				<h5>Cancellation Policy</h5>
				<p><?php echo $attributes['TM_Cancellation_Policy']?></p>
			</div>
			<div class="clearfix"></div>
			<!-- Cancellation Start-->
				 <div class="cancel-desc " style="margin-bottom: 10px; float: left; width: 100%;">
                <div class="col-md-6">
                    <label>Cancellation Code</label>
                    <select name="" class="form-control" id="cancel_code">
                      <option value="00">Testing</option>
                      <option value="51">Airline Flight Cancellation - Affects Customer/Traveller</option>
                     <option value="52">Airline Schedule Change - Unacceptable to Customer/Traveller</option>
                    <option value="53">Death - Customer/Traveller or Immediate Family</option>
                    <option value="54">Jury Duty/Court Summons - Affects Customer/Traveller</option>
                    <option value="55">Discretionary Cancellation (Viator Use Only)r</option>
                    <option value="56">Medical Emergency/Hospitalization - Customer/Traveller or Immediate Family</option>
                    <option value="57">Military Service - Affects Customer/Traveller</option>
                    <option value="58">National Disaster (Insurrection, Terrorism, War) -Affects Customer/Traveller</option>
                    <option value="59">Natural Disaster (Earthquake, Fire, Flood) - Affects Customer/Traveller</option>
                    <option value="62">Service Complaint - Denied Trip Add On Service</option>
                    <option value="63">Transport Strike/Labor Dispute - Affects Customer/Traveller</option>
                    <option value="66">Trip Add On Supplier Cancellation</option>
                    <option value="71">Credit Card Fraud</option>
                    <option value="72">Car Segment Cancellation - Affects Customer/Traveller</option>
                    <option value="73">Package Segment Cancellation - Affects Customer/Traveller</option>
                    <option value="74">Hotel Segment Cancellation - Affects Customer/Traveller</option>
                    <option value="77">Re-book</option>
                    <option value="78">Duplicate Purchase</option>
                    <option value="82">Honest Mistake - Incorrect Purchase</option>
                    <option value="87">Non-Refundable Cancellation (Outside 2 Days of Travel/Not Cencellation Event)</option>
                    <option value="88">Non-Refundable Cancellation (Within 2 Days of Travel)</option>
                    <option value="98">Customer Service/Technical Support Response Outside Time Limit</option>
                     <option value="99">Duplicate Processing</option>
                    </select>
                </div>
                <div class="col-md-6">
                     <label>Cancellation Description:</label><input type="text" class="form-control" name="" id="cancel_desc">
                </div>
            </div>
        <?php else:?>
        		<h4 style="color:red">This Booking not allowed for cancellation</h4>
        	<?php endif;?>
			<!-- Cancellation End-->
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
				$pax_name =$first_name.' '.$last_name;
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
			 <?php 
			 	$attributes= json_decode($booking_details['attributes'],true);
			 ?>
			 <?php if($attributes['Cancellation_available']):?>
			 <div class="col-xs-6 nopad">   
				<div class="btn_continue">
				<button data-toggle="modal" id="can-btn" data-target="#confirm_cancel" class="b-btn bookallbtn" type="button">Confirm</button>
				</div>
			 </div>
			<?php endif;?>
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
		
		<form method="get" action="<?=base_url().'index.php/transferv1/cancel_booking/'.$app_reference.'/'.$booking_source?>">

	        <button type="button" class="btn btn-default"  data-dismiss="modal">No</button>
	        <input type="hidden" name="cancel_code" id="c_cancel_code">
	        <input type="hidden" name="cancel_desc" id="c_cancel_desc">

	        <button class="btn btn-danger">Yes</button>

        </form>
		
	  </div>
	</div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Confirm Cancealltion Ends-->
<script type="text/javascript">
    $("#can-btn").click(function(){
        var cancel_code = $("#cancel_code option:selected").val();
         var cance_desc = $("#cancel_desc").val();
        $("#c_cancel_code").val(cancel_code);
        $("#c_cancel_desc").val(cance_desc);
        if(parseInt(cancel_code)==62){
            if(cance_desc==''){
              alert("Please fille the cancellation description");
              return false;
            }else{
              return true;
            }
        }else if(parseInt(cancel_code)==66){
          if(cance_desc==''){
            alert("Please fille the cancellation description")
              return false;
            } else{
              return true;
            }
        }else{
          return true;
        }
    });
</script>