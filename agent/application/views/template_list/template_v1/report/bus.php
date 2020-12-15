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
				<input type="hidden" id="module" value="<?=PROVAB_BUS_BOOKING_SOURCE?>">
				<input type="text" autocomplete="off" data-search_category="search_query" placeholder="AppReference/PNR" class="form-control auto_suggest_booking_id ui-autocomplete-input" id="auto_suggest_booking_id" name="filter_report_data" value="<?=@$_GET['filter_report_data']?>">
				</div>
				<button title="Search" class="btn btn-default" type="submit"><i class="far fa-search"></i></button>
				<a title="Clear Search" class="btn btn-default" href="<?=base_url().'index.php/report/bus'?>"><i class="far fa-history"></i></a>
		</form>
        		</div>
			</div>
		</div><!-- PANEL HEAD START -->
		<div class="panel_bdy"><!-- PANEL BODY START -->
		
				<div class="clearfix"></div>
                <div class="dropdown col-xs-3">
                    <button class="btn btn-info dropdown-toggle" type="button" id="excel_imp_drop" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <i class="fa fa-download" aria-hidden="true"></i> Excel
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="excel_imp_drop">
                    	<li>
                            <a href="<?php echo base_url(); ?>index.php/report/export_confirmed_booking_bus_report_b2b/excel/confirmed_cancelled<?= !empty($_SERVER["QUERY_STRING"])?'?'.$_SERVER["QUERY_STRING"]:''?>">Confirmed & Cancelled Booking</a>
                        </li>
                        <li >
                            <a href="<?php echo base_url(); ?>index.php/report/export_confirmed_booking_bus_report_b2b/excel<?= !empty($_SERVER["QUERY_STRING"])?'?'.$_SERVER["QUERY_STRING"]:''?>">Confirmed Booking</a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>index.php/report/export_cancelled_booking_bus_report_b2b/excel<?= !empty($_SERVER["QUERY_STRING"])?'?'.$_SERVER["QUERY_STRING"]:''?>">Cancelled Booking</a>
                        </li>
                    </ul>
                </div>

			<div class="tab-content">
				<div id="tableList" class="rigid_actions table-responsive">
					<div class="pull-right">
					<?php echo $this->pagination->create_links();?> <span class="">Total <?php echo $total_rows ?> Bookings</span>
					</div>
					<table class="table table-condensed table-bordered rigid_actions">
						<tr>
							<th>Sno</th>
							<th>Application Reference</th>
							<th>PNR</th>
							<th>Customer</br>Name</th>
							<th>From</th>
							<th>To</th>
							<th>Agent Net Fare</th>
							<th>Commission</th>
							<th>TDS</th>
							<th>Agent Markup</th>
							<th>Payment Gateway</th>
							<th>Payment Mode</th>
							<th>Convinence Fee</th>
							<th>GST</th>
							<th>TotalFare</th>
							<th>Opening Balance</th>
							<th>Closing Balance</th>
							<th>TravelDate</th>
							<th>BookedOn</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
						<?php
							if (isset($table_data) == true and valid_array($table_data['booking_details']) == true) {
								$booking_details = $table_data['booking_details'];
								$segment_3 = $GLOBALS['CI']->uri->segment(3);
								$current_record = (empty($segment_3) ? 1 : $segment_3);
								foreach($booking_details as $parent_k => $parent_v) {
									extract($parent_v);
									$tdy_date = date ( 'Y-m-d' );
									$jrny_date = date('Y-m-d', strtotime($journey_datetime));
									$diff = get_date_difference($tdy_date,$jrny_date);
									$action = '';
									$action .= bus_voucher($app_reference, $booking_source, $status);
									$action .= ' ';
									$action .= bus_pdf($app_reference, $booking_source, $status);
									$action .= ' ';
									$action .= bus_voucher_email($app_reference, $booking_source, $status,$parent_v['email']);
									$action .= bus_voucher_sms($app_reference, $booking_source, $status,$parent_v['lead_pax_phone_number']);
									$action.=' ';
									if($diff > 0 || $diff <= 0){
										$action .= bus_cancel($app_reference, $booking_source, $status);
									}
									$action.=" ".get_cancellation_details_button($app_reference, $booking_source, $status, $parent_v["booking_customer_details"]);
								?>
									<tr>
										<td><?php echo ($current_record++)?></td>
										<td><?php echo $app_reference;?></td>
										<td><?php echo $pnr?></td>
										<td><?php echo $booking_customer_details[0]['name']?></td>
										<td><?php echo $departure_from?></td>
										<td><?php echo $arrival_to?></td>
										<td><?php echo $agent_buying_price;?></td>
										<td><?php echo $agent_commission?></td>
										<td><?php echo $agent_tds?></td>
										<td><?php echo $agent_markup?></td>
										<td><?php echo $booking_billing_type?></td>
										<td><?php echo $payment_mode ?></td>
										<td><?php echo $convinence_amount?></td>
										<td><?php echo roundoff_number($gst)?></td>
										<td><?php echo $grand_total?></td>
										<td><?php echo $opening_balance; ?></td>
										<td><?php echo $closing_balance; ?></td>
										<td><?php echo app_friendly_absolute_date($journey_datetime)?></td>
										<td><?php echo $booked_date?></td>
										<td><span class="<?php echo booking_status_label($status) ?>"><?php echo $status?></span></td>
										<td><div class="" role="group"><?php echo $action; ?></div></td>
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

<?php
function get_accomodation_cancellation($courseType, $refId)
{
	return '<a href="'.base_url().'index.php/booking/accomodation_cancellation?courseType='.$courseType.'&refId='.$refId.'" class="col-md-12 btn btn-sm btn-danger "><i class="far fa-exclamation-triangle"></i> Cancel</a>';
}
function bus_voucher_email($app_reference, $booking_source,$status,$recipient_email)
{

	return '<a class="btn btn-sm btn-primary send_email_voucher" data-app-status="'.$status.'"   data-app-reference="'.$app_reference.'" data-booking-source="'.$booking_source.'"data-recipient_email="'.$recipient_email.'" title="Email Voucher"><i class=" far fa-envelope"></i></a>';
}
function bus_voucher_sms($app_reference, $booking_source, $status, $recipient_phone)
{

	return '<a class="btn btn-sm btn-primary send_sms_voucher" data-app-status="'.$status.'"   data-app-reference="'.$app_reference.'" data-booking-source="'.$booking_source.'"data-recipient_phone="'.$recipient_phone.'" title="SMS Voucher"><i class=" far fa-comment"></i></a>';
}
function get_cancellation_details_button($app_reference, $booking_source, $status, $bcds)
{
	if($status == 'BOOKING_CANCELLED'){
		return '<a target="_blank" href="'.base_url().'bus/ticket_cancellation_details?app_reference='.$app_reference.'&booking_source='.$booking_source.'&status='.$status.'" class="btn btn-sm btn-primary"
			title="Cancellation Details"><i class="fa fa-info"></i></a>';
	}
	foreach($bcds AS $bcd)
	{
		if($bcd["status"] == "BOOKING_CANCELLED")
			return '<a target="_blank" href="'.base_url().'bus/ticket_cancellation_details?app_reference='.$app_reference.'&booking_source='.$booking_source.'&status='.$bcd["status"].'" class="btn btn-sm btn-primary"
			title="Cancellation Details"><i class="fa fa-info"></i></a>';
	}
}
?>
<script>
$(document).ready(function () {
	<?php if(valid_array($_GET) == true){ ?>
		$('#advance_search_btn_label').trigger('click');
		$('#advance_search_btn_label').removeClass('show_form');
		$('#advance_search_btn_label').addClass('hide_form');
		$('#advance_search_btn_label').empty().text('-');
	<?php }?>
	$('#advance_search_btn_label').click(function () {
		if($(this).hasClass('show_form')) {
			$(this).removeClass('show_form');
			$(this).addClass('hide_form');
			$(this).empty().text('-');
		} else if($(this).hasClass('hide_form')) {
			$(this).removeClass('hide_form');
			$(this).addClass('show_form');
			$(this).empty().text('+');
		}
	});
	$("#from_date").change(function() {
		//validate_from_to_dates($(this), 'from_date', 'to_date');
	});
	//Balu A
	function validate_from_to_dates(object_ref, from_date, to_date)
	{
		//manage date validation
		$("#"+from_date).trigger("click");
		var selectedDate=object_ref.datepicker('getDate');
		//set dates to user view
		var nextdayDate=dateADD(selectedDate);
		var nextDateStr = (nextdayDate.getFullYear())+"-"+zeroPad((nextdayDate.getMonth()+1),2)+"-"+zeroPad(nextdayDate.getDate(),2);
		$("#"+to_date).datepicker({minDate:nextDateStr});
		//setting to_date based on from_date
		$("#"+to_date).datepicker('option','minDate',nextdayDate);
		$("#"+to_date).val(nextDateStr);
	}

	//send the email voucher
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
		    
				var _opp_url = app_base_url+'index.php/voucher/bus/';
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
	function phonenumber(input_val)
	{
		var phoneno = /^\d{10}$/;
		if(input_val.match(phoneno))
		{
			return true;
		}
		else
		{
			$('#sms_voucher_error_message').empty().text('This is Mandatory & System accepts only 10 digits.');
			return false;
		}
	}
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
			 if(phonenumber(phone)){ 		    
				var _opp_url = app_base_url+'index.php/voucher/bus/';
				_opp_url = _opp_url+app_reference+'/'+book_reference+'/'+app_status+'/sms_voucher/'+phone;
				toastr.info('Please Wait!!!');
				$.get(_opp_url, function() {
					toastr.info('SMS sent  Successfully!');
					$("#sms_voucher_modal").modal('hide');
				});
		  }
	  });

	});
});
</script>
