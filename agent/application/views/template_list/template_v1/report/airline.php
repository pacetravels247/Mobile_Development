<link href="<?php echo $GLOBALS['CI']->template->template_css_dir('bootstrap-toastr/toastr.min.css');?>" rel="stylesheet" defer>
<script src="<?php echo $GLOBALS['CI']->template->template_js_dir('bootstrap-toastr/toastr.min.js'); ?>"></script>
<?=$GLOBALS['CI']->template->isolated_view('report/email_popup')?>
<?=$GLOBALS['CI']->template->isolated_view('report/sms_popup')?>
<div class="bodyContent">
	<div class="table_outer_wrper"><!-- PANEL WRAP START -->
		<div class="panel_custom_heading"><!-- PANEL HEAD START -->
			<div class="panel_title">
				<?php echo $GLOBALS['CI']->template->isolated_view('share/report_navigator_tab') ?>
                <div class="clearfix"></div>
                <div class="search_fltr_section">
                <form method="GET" role="search" class="navbar-form" id="auto_suggest_booking_id_form">
				<div class="form-group">
				<input type="hidden" id="module" value="<?=PROVAB_FLIGHT_BOOKING_SOURCE?>">
				<input type="text" autocomplete="off" data-search_category="search_query" placeholder="AppReference/PNR" class="form-control auto_suggest_booking_id ui-autocomplete-input" id="auto_suggest_booking_id" name="filter_report_data" value="<?=@$_GET['filter_report_data']?>">
				</div>
				<button title="Search" class="btn btn-default" type="submit"><i class="far fa-search"></i></button>
				<a title="Clear Search" class="btn btn-default" href="<?=base_url().'index.php/report/flight'?>"><i class="far fa-history"></i></a>
		</form>
        		</div>
			</div>
		</div><!-- PANEL HEAD START -->
		<div class="panel_bdy"><!-- PANEL BODY START -->

        <div class="clearfix"></div>
			<div class="tab-content">
				<div id="tableList" class="table-responsive">
					<div class="pull-right">
						<?php echo $this->pagination->create_links();?> <span class="">Total <?php echo $total_rows ?> Bookings</span>
					</div>
					<table class="table table-condensed table-bordered rigid_actions">
						<tr>
							<th>Sno</th>
							<th>Application Reference</th>
							<th>PNR</th>
							<th>GDS PNR</th>
							<th>Customer<br/>Name</th>
							<th>From</th>
							<th>To</th>
							<th>Trip Type</th>
							<th>Payment Gateway</th>
							<th>Payment Mode</th>
							<th>Agent Net Fare</th>
							<th>Agent Commission</th>
							<th> Agent <br/>Markup</th>
							<th>TDS</th>
							<th>GST</th>
							<th>Convenience</th>
							<th>TotalFare</th>
							<th>Opening Balance</th>
							<th>Closing Balance</th>							
							<th>TravelDate</th>
							<th>BookedOn</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
						<?php
							if(valid_array($table_data['booking_details']) == true) {
				        		$booking_details = $table_data['booking_details'];
								$segment_3 = $GLOBALS['CI']->uri->segment(3);
								$current_record = (empty($segment_3) ? 1 : $segment_3);
					        	// debug($booking_details);exit;
					        	foreach($booking_details as $parent_k => $parent_v) {
									
					        		extract($parent_v);
					        		// debug($booking_itinerary_details); exit;
									$action = '';
									$cancellation_btn = '';
									$voucher_btn = '';
									$status_update_btn = '';
									$booked_by = '';
									
									//Status Update Button
									if (in_array($status, array('BOOKING_CONFIRMED')) == false) {
										switch ($booking_source) {
											case PROVAB_FLIGHT_BOOKING_SOURCE :
												$status_update_btn = '<button class="btn btn-success btn-sm update-source-status" data-app-reference="'.$app_reference.'"><i class="far fa-database"></i> Update Status</button>';
												break;
										}
									}
									$voucher_btn = flight_voucher($app_reference, $booking_source, $status);
									$pdf_btn = flight_pdf($app_reference, $booking_source, $status);
									$invoice = flight_invoice($app_reference, $booking_source, $status);
									$cancel_btn = flight_cancel($app_reference, $booking_source, $status);
									$email_btn = flight_voucher_email($app_reference, $booking_source,$status,$parent_v['email']);
									$multi_voucher_btn = multi_voucher_links($app_reference, $booking_source, $status);
									$action .= flight_voucher_sms($app_reference, $booking_source, $status,$parent_v['lead_pax_phone_number']);
									$jrny_date = date('Y-m-d', strtotime($journey_start));
									$tdy_date = date ( 'Y-m-d' );
									$diff = get_date_difference($tdy_date,$jrny_date);
									$action .= $voucher_btn;
									$action .=$pdf_btn;
									$action .=$email_btn;
									$action .=$multi_voucher_btn;
									if($diff > 0 || $diff <= 0){
										$action .= $cancel_btn;
									}
									$action .= get_cancellation_details_button($parent_v['app_reference'], $parent_v['booking_source'], $parent_v['status'], $parent_v['booking_transaction_details']);
									
									$es_dets = $GLOBALS["CI"]->flight_model->get_extra_service_details($app_reference);
									$esc = $GLOBALS["CI"]->flight_model->get_extra_service_charges($es_dets);
									$airline_pnr = '';
									if (!empty($booking_itinerary_details[0]['airline_pnr'])) {
										$airline_pnr_arr = array();
										foreach ($booking_itinerary_details as $key => $value) {
											if(!in_array($value['airline_pnr'], $airline_pnr_arr)){
												if(empty($value['airline_pnr']) == false){
													$airline_pnr .= $value['airline_pnr'].' / ';
													$airline_pnr_arr[] = $value['airline_pnr'];
												}
												
											}

										}
										$airline_pnr = substr($airline_pnr, 0, -2);
									}
									else{
										$airline_pnr = $pnr;
									}
									$gds_pnr = $booking_transaction_details[0]['gds_pnr'];
		                            if(empty($gds_pnr) == true){
		                                $gds_pnr = $pnr;
		                            }
								?>
									<tr>
										<td><?=($current_record++)?></td>
										<td><?php echo $app_reference;?></td>
										<td><?=@$airline_pnr?></td>
										<td><?=@$gds_pnr?></td>
										<td><?=$booking_transaction_details[0]['booking_customer_details'][0]['title'].' '.$booking_transaction_details[0]['booking_customer_details'][0]['first_name'].' '.$booking_transaction_details[0]['booking_customer_details'][0]['last_name']?></td>
										<td><?=@$from_loc?></td>
										<td><?=@$to_loc?></td>
										<?php if(@$trip_type == 'circle') {?>
										<td>Round-trip</td>
										<?php } else { ?>
										<td><?=@$trip_type?></td>
										<?php } ?>
										<td><?php echo $booking_billing_type?></td>
										<td><?php echo $payment_mode?></td>
										<td><?php echo $agent_buying_price;?></td>
										<td><?php echo $agent_commission?></td>
										<td><?php echo $agent_markup?></td>
										<td><?php echo ($agent_tds)?></td>
										<td><?php echo ($gst)?></td>
										<td><?php echo $convinence_amount; ?></td>
										<td><?php echo $grand_total; ?></td>
										<td><?php echo $opening_balance; ?></td>
										<td><?php echo $closing_balance; ?></td>
										<td><?php echo app_friendly_absolute_date($journey_start)?></td>
										<td><?php echo $booked_date?></td>
										<td><span class="<?php echo booking_status_label($status) ?>"><?php echo $status?></span></td>
										<td><div class="" role="group"><?php if($parent_v['status'] != 'BOOKING_FAILED'){ echo $action; } ?></div></td>
									</tr>
								<?php
								}
							} else {
								echo '<tr><td>No Data Found</td></tr>';
							}
						?>
						
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
$(document).ready(function() {
	//update-source-status update status of the booking from api
	$(document).on('click', '.update-source-status', function(e) {
		e.preventDefault();
		$(this).attr('disabled', 'disabled');//disable button
		var app_ref = $(this).data('app-reference');
		$.get(app_base_url+'index.php/flight/get_booking_details/'+app_ref, function(response) {
			
		});
	});

    /*
    *Sagar Wakchaure
    *send email voucher
    */
	  $('.send_email_voucher').on('click', function(e) {
			$("#mail_voucher_modal").modal('show');
			$('#mail_voucher_error_message').empty();
	        email = $(this).data('recipient_email');
			$("#voucher_recipient_email").val(email);
	        app_reference = $(this).data('app-reference');
	        book_reference = $(this).data('booking-source');
	        app_status = $(this).data('app-status');
		  $("#send_mail_btn").off('click').on('click',function(e){
			  email = $("#voucher_recipient_email").val();
			  var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
			  if(email != ''){
				  if(!emailReg.test(email)){
					  $('#mail_voucher_error_message').empty().text('Please Enter Correct Email Id');
	                     return false;    
					      }
			      
						var _opp_url = app_base_url+'index.php/voucher/flight/';
						_opp_url = _opp_url+app_reference+'/'+book_reference+'/'+app_status+'/email_voucher/'+email;
						toastr.info('Please Wait!!!');
						$.get(_opp_url, function() {
							
							toastr.info('Email sent  Successfully!!!');
							$("#mail_voucher_modal").modal('hide');
						});
			  }else{
				  $('#mail_voucher_error_message').empty().text('Please Enter Email ID');
			  }
		  });
	
	});
	//SMS Voucher
	$('.send_sms_voucher').on('click', function(e) {
		
		$("#sms_voucher_modal").modal('show');
		$('#sms_voucher_error_message').empty();
	    var phone = $(this).data('recipient_phone');
		$("#voucher_recipient_phone").val(phone);
	    app_reference = $(this).data('app-reference');
	    book_reference = $(this).data('booking-source');
	    app_status = $(this).data('app-status');
	  $("#send_sms_btn").off('click').on('click',function(e){
		  phone = $("#voucher_recipient_phone").val();		
			 if(phone != ''){ 		    
				var _opp_url = app_base_url+'index.php/voucher/flight/';
				_opp_url = _opp_url+app_reference+'/'+book_reference+'/'+app_status+'/sms_voucher/'+phone;
				toastr.info('Please Wait!!!');
				$.get(_opp_url, function() {
					toastr.info('SMS sent  Successfully!');
					$("#sms_voucher_modal").modal('hide');
				});
		  }else{
			  $('#sms_voucher_error_message').empty().text('Please Enter Phone Number');
		  }
	  });

	});
	
});
</script>
<?php
function multi_voucher_links($app_reference, $booking_source, $status)
{
	return '<a class="btn btn-sm btn-primary" title="Multi Voucher" href="'.base_url().'index.php/voucher/multivoucher/'.$app_reference.'/'.$booking_source.'/'.$status.'"><i class="far fa-list"></i></a>';
}
function get_accomodation_cancellation($courseType, $refId)
{
	return '<a href="'.base_url().'index.php/booking/accomodation_cancellation?courseType='.$courseType.'&refId='.$refId.'" class="btn btn-sm btn-danger "><i class="far fa-exclamation-triangle"></i> Cancel</a>';
}
function flight_voucher_email($app_reference, $booking_source,$status,$recipient_email)
{
	return '<a class="btn btn-sm btn-primary send_email_voucher" data-app-status="'.$status.'" title="Email Voucher"   data-app-reference="'.$app_reference.'" data-booking-source="'.$booking_source.'"data-recipient_email="'.$recipient_email.'"><i class="far fa-envelope"></i></a>';
}
function flight_voucher_sms($app_reference, $booking_source, $status, $recipient_phone)
{
	return '<a class="btn btn-sm btn-primary send_sms_voucher" data-app-status="'.$status.'"   data-app-reference="'.$app_reference.'" data-booking-source="'.$booking_source.'"data-recipient_phone="'.$recipient_phone.'" title="SMS Voucher"><i class=" far fa-comment"></i></a>';
}
function get_cancellation_details_button($app_reference, $booking_source, $master_booking_status, $booking_customer_details)
{
	$status = 'BOOKING_CONFIRMED';
	if($master_booking_status == 'BOOKING_CANCELLED'){
		$status = 'BOOKING_CANCELLED';
	} else{
		foreach($booking_customer_details as $tk => $tv){
			foreach($tv['booking_customer_details'] as $pk => $pv){
				if($pv['status'] == 'BOOKING_CANCELLED'){
					$status = 'BOOKING_CANCELLED';
					break;
				}
			}
		}
	}
	if($status == 'BOOKING_CANCELLED'){
		return '<a target="_blank" href="'.base_url().'index.php/flight/ticket_cancellation_details?app_reference='.$app_reference.'&booking_source='.$booking_source.'&status='.$master_booking_status.'" class="col-md-12 btn btn-sm btn-info "><i class="far fa-info"></i> Cancellation Details</a>';
	}
}
?>