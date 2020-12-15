<div class="bodyContent col-md-12">
	<div class="panel panel-default clearfix">
		<!-- PANEL WRAP START -->
		<div class="panel-heading">
			<!-- PANEL HEAD START -->
			<div class="panel-title">
				<?php include_once 'navigation.php';?>
			</div>
		</div>
		<!-- PANEL HEAD START -->
		<div class="panel-body">
			<!-- PANEL BODY START -->
			<div class="tab-content">
				<div id="tableList" class="table-responsive">
					<div class="pull-right"><?php echo $this->pagination->create_links();?>
						<span class="">Total <?php echo $total_rows ?> Bookings</span>
					</div>
					<table class="table table-condensed table-bordered">
						<tr>
							<th>Sno</th>
							<th>Reference</th>
							<th>Status</th>
							<th>Total Fare</th>
							<th>Payment Mode</th>
							<th>Customer</th>
							<th>Booking</th>
							<th>Total Ticket(s)</th>
							<th>Booked On</th>
							<th>Action</th>
						</tr>
						<?php
							if (isset ( $table_data ) == true and valid_array ( $table_data )) {
								$segment_3 = $GLOBALS ['CI']->uri->segment ( 3 );
								$current_record = (empty ( $segment_3 ) ? 0 : $segment_3);
								foreach ( $table_data as $k => $v ) {
									extract ( $v );
									$action = '';
									$cancellation_btn = '';
									$voucher_btn = '';
									$status_update_btn = '';
									$voucher_email_btn = '';
									if (strtotime ( $v ['journey_start'] ) > time () and ($v ['status'] == BOOKING_PENDING || $v ['status'] == BOOKING_VOUCHERED || $v ['status'] == BOOKING_CONFIRMED || $v ['status'] == BOOKING_HOLD)) {
										// $cancellation_btn = get_accomodation_cancellation($api_code, $v['reference']);
									}
							
									// Status Update Button
									if (in_array ( $v ['status'], array (
																'BOOKING_CONFIRMED' 
																) ) == false) {
																	switch ($v ['booking_source']) {
																		case PROVAB_FLIGHT_BOOKING_SOURCE :
																			$status_update_btn = '<button class="btn btn-success btn-sm update-source-status" data-app-reference="' . $v ['app_reference'] . '"><i class="fa fa-database"></i> Update Status</button>';
																			break;
																	}
																}
																
																//$voucher_btn = flight_voucher ( $v ['app_reference'], $booking_source, $v ['status'] );
																//$voucher_email_btn = flight_email_voucher($v ['app_reference'], $booking_source, $v ['status']);
																//$action = $voucher_btn . $voucher_email_btn . $status_update_btn . $cancellation_btn;
												$action = '<div class="col-md-2 pull-right pad0">
												<div class="dropdown" id="dLabel1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
													<a href="">Actions</a>
													<span class="caret"></span>
												</div>
												<ul class="dropdown-menu booking_list1" aria-labelledby="dLabel1">
													<li class="print_ticket" data-reference_id="'.$v['booking_source'].'" data-app_reference="'.$v['app_reference'].'" data-status="'.$v['status'].'">
															<div class="img_mb1">
																<img src="'.$GLOBALS['CI']->template->template_images('icons/print.png').'" />
															</div>
															<div class="all-type-img1">Print Ticket</div>
													</li>
													
													<li class="print_invoice" data-reference_id="'.$v['booking_source'].'" data-app_reference="'.$v['app_reference'].'" data-status="'.$v['status'].'">
															<div class="img_mb1">
																<img src="'.$GLOBALS['CI']->template->template_images('icons/print.png').'" />
															</div>
															<div class="all-type-img1">Print Invoice</div>
													</li>
												</ul>
											</div>';
																?>
						<tr>
							<td><?=($current_record+$k+1)?></td>
							<td><?=$app_reference;?></td>
							<td><span class="<?=booking_status_label($status) ?>"><?=$status?></span></td>
							<td><?php echo $currency.':'.($total_fare+$domain_markup)?></td>
							<td><?php echo $payment_name?></td>
							<td><?php echo $name.'<br>Email:'.$email?><br>
								<?php echo 'P:'.$phone_number?>
							</td>
							<td><strong><?php echo $journey_from.' to '.$journey_to?></strong><br>
								<?php echo app_friendly_datetime($journey_start)?>-to-<?php echo app_friendly_datetime($journey_end)?>
							</td>
							<td><?php echo $total_passengers?></td>
							<td><?php echo app_friendly_absolute_date($created_datetime)?></td>
							<td>
								<div class="" role="group"><?php echo $action; ?></div>
							</td>
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
	});
</script>
<?php
	function get_accomodation_cancellation($courseType, $refId) {
		return '<a href="' . base_url () . 'index.php/booking/accomodation_cancellation?courseType=' . $courseType . '&refId=' . $refId . '" class="btn btn-sm btn-danger "><i class="fa fa-exclamation-triangle"></i> Cancel</a>';
	} ?>
	
	
	
<!-- Mail - Ticket  starts-->
	<div class="modal fade" id="mail_ticket_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content-mb">
				<div class="modal-header_mb">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"></span></button>
					<h4 class="modal-title ycol" id="gridSystemModalLabel">
							<img src="<?php echo $GLOBALS['CI']->template->template_images('icons/email.png'); ?>">
							<span id="mail_ticket_module_label1"></span>
					</h4>
				</div>
				<div class="modal-body">
				<div id="email_ticket_parameters">
				
					<input type="hidden" id="mail_ticket_reference_id" class="hiddenIP">
					<input type="hidden" id="mail_ticket_app_reference" class="hiddenIP">
					<input type="hidden" id="mail_ticket_status" class="hiddenIP">
					<input type="text" id="ticket_email_id" class="form-control" value="" placeholder="Enater EmailID">
					<p id="mail_ticket_module_label2"></p>
					<div class="row">
						<div class="col-md-4">
							<input type="button" value="SEND >" class="btnfly" id="send_mail_btn">
						</div>
						<div class="col-md-8">
							<img src="<?php echo $GLOBALS['CI']->template->template_images('icons/default_loading.gif'); ?>" id="mail_loader_image" style="display:none">
							<strong id="mail_ticket_error_message" class="text-danger"></strong>
						</div>
					</div>
				</div>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<!-- Mail - Ticket  ends-->	
	
<!--Mail Status  starts-->
<div class="modal fade" id="mail_status_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content-mb">
			<div class="modal-header_mb">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"></span></button>
			</div>
			<div class="modal-body" id="mail_status_details"></div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Mail Status ends-->

<!-- print - invoice  starts-->
	<div class="modal fade" id="print_invoice" role="dialog" aria-labelledby="gridSystemModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content-mb">
				<div class="modal-header_mb">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"></span></button>
					<h4 class="get_invoice_printout modal-title ycol" id="gridSystemModalLabel">
						<span class="print_invoice_div_data">
							<img src="<?php echo $GLOBALS['CI']->template->template_images('icons/print.png'); ?>">
							Print Invoice
						</span>
					</h4>
				</div>
				<div class="modal-body" id="invoice_details">

				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
<!-- print - invoice  ends-->

<!-- print - Ticket  starts-->
	<div class="modal fade" id="print_ticket" role="dialog" aria-labelledby="gridSystemModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content-mb">
				<div class="modal-header_mb">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"></span></button>
					<h4 class="get_ticket_printout modal-title ycol" id="gridSystemModalLabel">
						<span class="print_book_div_data">
							<img src="<?php echo $GLOBALS['CI']->template->template_images('icons/print.png'); ?>">
							<span class="print_module_label"></span>
						</span>
					</h4>
				</div>
				<div class="modal-body" id="ticket_details">

				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
<!-- print - Ticket  ends-->
	

	<script>
		$(document).ready(function(){
			//Ticket Popup
			$('.print_ticket').click(function () {
				var reference_id = $(this).data('reference_id');
				var app_reference = $(this).data('app_reference');
				var status = $(this).data('status');
				var controller_method = 'report';
				$.get(app_base_url+'index.php/'+controller_method+'/flight_ticket?reference_id='+reference_id+'&app_reference='+app_reference+'&status='+status, function (response) {
					$('#ticket_details').empty().html(response.ticket);
					$('#print_ticket').modal();
				});
			});
			
			//Invoice Popup
			$('.print_invoice').click(function () {
				var reference_id = $(this).data('reference_id');
				var app_reference = $(this).data('app_reference');
				var status = $(this).data('status');
				var controller_method = 'report';
				$.get(app_base_url+'index.php/'+controller_method+'/flight_invoice?reference_id='+reference_id+'&app_reference='+app_reference+'&status='+status, function (response) {
					$('#invoice_details').empty().html(response.invoice);
					$('#print_invoice').modal();
				});
			});
			
			//Shows Send Ticket Modal
			$('.load_mail_ticket_modal').click(function (){
				var reference_id = $(this).data('reference_id');
				var app_reference = $(this).data('app_reference');
				var status = $(this).data('status');
				var user_email_id = $(this).data('customer_email');
				
				$('#mail_ticket_module_label1').empty().text('Email E-Ticket');
				$('#mail_ticket_module_label2').empty().text('Copy of E-Ticket will be sent to the above EmailId');
				
				$('#mail_ticket_reference_id').val(reference_id);
				$('#mail_ticket_app_reference').val(app_reference);
				$('#mail_ticket_status').val(status);
				$('#ticket_email_id').val(user_email_id);
				$('#mail_ticket_error_message').empty();
				$('#mail_loader_image').hide();
				$('#mail_ticket_modal').modal();
			});

			//Email Ticket
			$('#send_mail_btn').click(function (){
				var reference_id = $('#mail_ticket_reference_id').val().trim();
				var app_reference = $('#mail_ticket_app_reference').val().trim();
				var status = $('#mail_ticket_status').val().trim();
				var user_email_id = $('#ticket_email_id').val().trim();

				if(user_email_id !='') {
					$('#mail_ticket_error_message').empty();
					$('#mail_loader_image').show();
					
					var	controller_method = 'report/email_flight_ticket';
						
					$.get(app_base_url+'index.php/'+controller_method+'/'+app_reference+'/'+reference_id+'/'+status+'/'+user_email_id, function (response) {
						$('#mail_ticket_modal').modal('toggle');
						var mail_status_message = '';
						if(response.status == '<?php echo SUCCESS_STATUS?>') {
							mail_status_message = '<p>Sent Successfully</p>';
						} else {
							mail_status_message = '</p>Invalid Details</p>';
						}
						$('#mail_status_details').empty().html(mail_status_message);
						$('#mail_status_modal').modal();
					});
				} else {
					$('#mail_ticket_error_message').empty().text('Please Enter EmailID');
				}
			}); 

			//Print Invoice
			$('span.print_invoice_div_data').click(function (){
				get_print_out('invoice_details');
			});

			//Print Ticket
			$('span.print_book_div_data').click(function (){
				get_print_out('ticket_details');
			});
		});

		//Print Out of Ticket/Voucher/Invoice
		function get_print_out(core_content) 
		{
			 var print_data = document.getElementById(core_content);
		     var popupWin = window.open('', '_blank', 'width=600,height=600, scrollbars=1');
		     popupWin.document.open();
			 popupWin.document.write('<html><body onload="window.print()">' + print_data.innerHTML + '</body></html>');
		     popupWin.document.close();
		}
	</script>	