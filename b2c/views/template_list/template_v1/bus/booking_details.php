<?php
	$booking_id = $booking_details['data']['booking_details']['app_reference'];
	
	$booking_made_on = app_friendly_date($booking_details['data']['booking_details']['created_datetime']);
	
	$choosen_parameters = $booking_details['data']['booking_itinerary_details'][0];
	$booking_status = $booking_details['status'];
	$total_duration = get_total_duration($choosen_parameters['arrival_datetime'], $choosen_parameters['departure_datetime']);
	$source_name = $choosen_parameters['departure_from'];
	$destination_name = $choosen_parameters['arrival_to'];
	$journey_date = explode(" ",$choosen_parameters['journey_datetime']);
	$journey_date = $journey_date[0];
?>
<div class="clear"></div>
<div class="bgcolor">
	<!-- navigation 1-3 strts-->
	<div class="container-fluid pad0">
		<div class="row">
			<!-- left part -->
			
			<!--left part end-->

			<!--right part-->
			<?php if(is_logged_in_user()) { 
				$logged_in_user = true;
				$col_md = '9';
			} else {
				$logged_in_user = false;
				$col_md = '12';
			}?>
			<div class="col-sm-<?php echo $col_md;?>">
				<div class="row top20">
				<?php if(is_logged_in_user() == true) {?>
					<div class="col-md-11 blue bold mb10 get_hand_cursor f12">
						<a href="<?php echo base_url();?>index.php/report/bus">< Back to My Bookings</a>
					</div>
				<?php } ?>
					<div class="col-sm-6 font12">
						<h1 class="details_bus">View Booking Details</h1>
						<div class="seater mt20"><?php echo $source_name;?> &nbsp; <i class="fa fa-long-arrow-right"></i> &nbsp; <?php echo $destination_name;?></div>
						<div class="seater">Booked On: <?php echo $booking_made_on;?>hrs</div>
					</div>
					<div class="col-sm-6">
						<h5 class="pull-right m0 font13"><span class="colorgray">Booking ID :</span> <?php echo $booking_id;?></h5>
						<div class="clear-fix"></div>
						<br />
						<h5 class="pull-right mb0 mt5 colorgray font12">Booking Date : <?php echo $booking_made_on;?></h5>
					</div>
				</div>
				<div class="row mt20 mb10 font12 bold">
						<div class="col-md-9"><img src="<?php echo $GLOBALS['CI']->template->template_images('icons/bus.png'); ?>">  <?php echo $source_name; ?> <i class="fa fa-long-arrow-right"></i> <?php echo $destination_name;?>: <span class="fn seater"><?php echo $journey_date;?> (<?php echo $total_duration['hours'].'hr '.$total_duration['minutes'].'m'; ?> )</span></div>
						<div class="col-md-3" style=" text-align: right; ">
							<div class="dropdown menu_action_holder" id="dLabel2" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<?php echo menu($booking_details,$customer_email); ?>
							</div> 
							
						</div>
				</div>
				<!-- PASSENGER -->
				<div class="row bs-example bg_top_itry font12 p15-0">
					<div class="col-md-6">
						<div class="row">
							<div class="col-md-6">
								<div class="row">
									<div class="col-md-3">
										<img src="<?php echo $GLOBALS['CI']->template->template_images('icons/bus.png'); ?>">
									</div>
									<div class="col-md-9 pad0">
										<div class="seater"><?php echo $choosen_parameters['operator'];?></div>
										<div class="seater">- <?php echo $choosen_parameters['bus_type'];?></div>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="txt_center font12">
									<div class="citiz color3"><?php echo $source_name; ?></div>
									<div class="seater"><?php echo $source_name; ?></div>
									<div class="seater"><?php echo $choosen_parameters['departure_datetime']; ?>hrs</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="row">
							<div class="col-md-1">
								<i class="fa fa-long-arrow-right arrow_right"></i>
							</div>
							<div class="col-md-6">
								<div class="txt_center font12">
									<div class="citiz color3"><?php echo $destination_name;?></div>
									<div class="seater"><?php echo $destination_name;?></div>
									<div class="seater"><?php echo $choosen_parameters['arrival_datetime']; ?>hrs</div>
								</div>
							</div>
							<div class="col-md-5">
								<div class="txt_center font12">
									<div class="citiz color3">Duration</div>
									<div class="citiz"><?php echo $total_duration['hours'].'hr '.$total_duration['minutes'].'m'; ?></div>
								</div>
							</div>
						</div>
					</div>
					<?php echo get_bus_pax_details($booking_details);  ?>
				</div>
				<!-- PASSENGER -->
			<div class="clear-fix"></div>
				<!-- payment summary -->
				<div class="row pay_summary_details font12">
					<h5 class="pay_summary">payment summary</h5>
					<div class="col-sm-6 pad0 detail_right_border">
						<div class="passenger_name_oned font12">
					</div>
						<div class="passenger_border f18 color369">
							<div class="row">
								<div class="col-sm-6">Total Fare</div>
								<div class="col-sm-6 tr"><i class="fa fa-inr"></i> <?=$booking_details['grand_total']?></div>
							</div>
						</div>
					</div>
	
				</div>
				<!-- payment summary -->
				<!-- support details -->
				<div class="clear-fix"></div>
				<?php  echo get_support_details(); ?>
				<!-- support details -->
			</div>
			<!--right part end-->
		</div>
	</div>
	<div class="clear"></div>
</div>

<?php 
/** Get Bus Pax details **/

function get_bus_pax_details($booking_details)
{
	$label	= "FARE";
	$pax_details = '<div class="col-sm-12 pad0">
					<div class="passenger_name">
						<div class="col-sm-4 pad0">
							<span class="fa fa-user"></span>
							<span class="ml10 bold">PASSENGER NAME</span>
						</div>
						<div class="col-sm-4 pad0 bold">
							<div class="row">
								<div class="col-sm-2">AGE</div>
								<div class="col-sm-7">PNR</div>
								<div class="col-sm-3">SEAT</div>
							</div>
						</div>
						<div class="col-sm-4 pad0 bold">
							<div class="row">
								<div class="col-sm-2">&nbsp;</div>
								<div class="col-sm-3">'.$label.'</div>
								<div class="col-sm-3">STATUS</div>
							</div>
						</div>
					</div>
				</div>';
	
	if(count($booking_details['data']['booking_customer_details']) > 0){
		
		$booking_status = $booking_details['data']['booking_details']['status'];
		$pnr = $booking_details['data']['booking_details']['pnr'];
		$pax_wise_total_fare = $booking_details['pax_wise_total_fare']; 
		
		foreach($booking_details['data']['booking_customer_details'] as $k => $v){
		$pax_full_name = $v['name'];
		$seat_numbers = $v['seat_no'];
		$pax_age = $v['age'];
		$paid_amount = array_shift($pax_wise_total_fare);
		
		$pax_details .= '<div class="col-sm-12 pad0">
						<div class="passenger_name_one">
						<div class="col-sm-4 pad0">
							'.($k+1).'
							<span class="ml10">'.$pax_full_name.'</span>
						</div>
						<div class="col-sm-4 pad0 ">
							<div class="row">
						<div class="col-sm-2">'.$pax_age.'</div>';
		
		$pax_details .= '<div class="col-sm-7">'.$pnr.'</div>
							<div class="col-sm-3 ">'.$seat_numbers.'</div>
							</div>
							</div>
							<div class="col-sm-4 pad0 ">
								<div class="row">
									<div class="col-sm-2">&nbsp;</div>
									<div class="col-sm-3">'.$paid_amount.'</div>
									<div class="col-sm-3 blue">'.$booking_status.'</div>
								</div>
							</div>';
	$pax_details .= '</div>
					</div>';
		}
	}
    return $pax_details;
}
/** Get support details **/

function get_support_details(){
	
	$supports = '<div class="row pay_summary_details font12">
						<h5 class="pay_summary">support details</h5>
						<div class="col-sm-12 p20 blue bold lh35">
							<div class="col-sm-6">
								<img class="support-icons" src="'.$GLOBALS['CI']->template->template_images('icons/service.png').'">
								<span> customercare@proapp.com</span>
								<br>
								<img class="support-icons" src="'.$GLOBALS['CI']->template->template_images('icons/call-proapp.png').'">
								<span> 011 11111111</span>
							</div>
							<div class="col-sm-6">
								<img class="support-icons" src="'.$GLOBALS['CI']->template->template_images('icons/faq.png').'">
								<a href="#" target="_balnk"> /faq</a>
							</div>
						</div>
						</div>';
	return $supports;
}

/** Menu **/
function menu($booking_details,$customer_email){
	$v = array();
	$v['app_reference'] = $booking_details['data']['booking_details']['app_reference'];
	$v['booking_source'] = $booking_details['data']['booking_details']['booking_source'];
	$v['status'] = $booking_details['data']['booking_details']['status'];
	$action ='<div class="col-md-2 pull-right pad0">
												<div class="dropdown" id="dLabel1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
													<a href="">Actions</a>
													<span class="caret"></span>
												</div>
												<ul class="dropdown-menu booking_list1" aria-labelledby="dLabel1">
													
													<li class="load_mail_ticket_modal" data-reference_id="'.$v['booking_source'].'" data-app_reference="'.$v['app_reference'].'" data-status="'.$v['status'].'" data-customer_email="'.$customer_email.'">
															<div class="img_mb1">
																<img src="'.$GLOBALS['CI']->template->template_images('icons/email.png').'" />
															</div>
															<div class="all-type-img1">Email Ticket</div>
													</li>
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
	return $action;
}
?>



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
				$.get(app_base_url+'index.php/'+controller_method+'/get_bus_ticket?reference_id='+reference_id+'&app_reference='+app_reference+'&status='+status, function (response) {
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
				$.get(app_base_url+'index.php/'+controller_method+'/get_bus_invoice?reference_id='+reference_id+'&app_reference='+app_reference+'&status='+status, function (response) {
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
					
					var	controller_method = 'report/email_bus_ticket';
						
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