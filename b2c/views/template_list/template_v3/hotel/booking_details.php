
<style>
	ul.drp_dwn {
	left: 76px;
	top: 44px;
}
</style>

<?php
	$booking_details = $data['booking_details'];
	$lead_pax_detail = $booking_details['lead_pax_details'];
	$proapp_booking_id = $booking_details['app_reference'];
	$booking_made_on = app_friendly_date($booking_details['created_datetime']);
	$hotel_city = $booking_details['hotel_location'];
	$hotel_image = "";
	$number_of_rooms = $booking_details['total_rooms'];
	$check_in = app_friendly_date($booking_details['hotel_check_in']);
	$check_out = app_friendly_date($booking_details['hotel_check_out']);
	$booking_status = $booking_details['status'];
	$hotel_name = $booking_details['hotel_name'];
	$header_check_in = explode('|',api_header_date($check_in));
	$header_check_out = explode('|', api_header_date($check_out));
	$hotel_total_nights = get_date_difference($check_in, $check_out);
	//$cancellation_policy = $hotel_booking_data['HotelCancelPolicy'];
	$hotel_address = $booking_details['hotel_location'];
	$hotel_rating = $booking_details['star_rating'];
	if(valid_array($booking_details['itinerary_details'])) {
		$room_types = implode('|', array_unique(group_array_column($booking_details['itinerary_details'], 'room_type_name')));
	} else {
		$room_types = 'Not Available';
	}
	$lead_pax_name = $lead_pax_detail['name'];
	$pax_contact_number = $lead_pax_detail['phone'];
	
	$rating = $booking_details['star_rating'];
	$star_rating = "";
	if(intval($rating) == true){
		$star_rating = '<img src="'.$GLOBALS['CI']->template->template_images('rating/s'.$rating.'.png').'">';		
	}
?>
<div class="bgcolor">
	<!-- navigation 1-3 strts-->
	<div class="container-fluid pad0">
		<div class="row">
		<?php if(is_logged_in_user()) { 
			$logged_in_user = true;
			$col_md = '9';
		} else {
			$logged_in_user = false;
			$col_md = '12';
		}?>
	<div class="col-sm-<?php echo $col_md;?>">
	<div class="row top20">
		<?php if(is_logged_in_user()) { ?> 
		<div class="col-md-11 blue bold mb10 get_hand_cursor f12">
			<a href="<?php echo base_url();?>hotel/manage_booking">< Back to My Bookings</a>
		</div>
		<?php } ?>
		<div class="col-sm-6 font12">
			<h1 class="details_bus_mb">View Booking Details</h1>
			<h5 class="mb10 mt30  colorgray font12">
				<span class="bold"><img src="<?php echo $GLOBALS['CI']->template->template_images('hotelb.png'); ?>" /> Hotel in <?php echo $hotel_city;?> </span> 
				<?php echo $check_in;?> - <?php echo $check_out;?>
			</h5>
		</div>
		<div class="col-sm-6">
			<h5 class="pull-right m0 font13"><span class="colorgray">Booking ID :</span> <span class="bold"><?php echo $proapp_booking_id;?></span></h5>
			<div class="clear-fix"></div>
			<h5 class="pull-right mb0 mt5 colorgray font12">Booking Date : <?php echo $booking_made_on;?> Hrs</h5>
			
				<div class="col-md-6 pull-right pad0">
				<div class="clearfix"></div>
				
				<?php echo hotel_menu($booking_details,$lead_pax_detail['email']); ?>
				</div>
				
			</div>
		</div>
		<!-- PASSENGER -->
		<div class="right_part_itry">
			<div class="col-md-12 pad0 f12">
				<div class="bs-example bg_top_itry_mbb">
					<div class="col-md-8 pad0">
						<div class="">
							<div class="col-md-4 pad0">
								<!-- <img src="<?php echo $hotel_image;?>" class="hotel_image">  -->
							</div>
							<div class="col-md-8 pad20">
								<div class="citiz">
									<?php echo $hotel_name; ?>
									<span class="star_img">
									<?php echo $star_rating; ?>
									</span>
							</div>
							<div class="seater"><?php echo $hotel_address;?> </div>
							<div class="citiz top20">Room Type </div>
							<div class="seater"><?php echo $room_types;?></div>
						</div>
						<div class="clearfix"></div>
						<!--
						<div class="img-btm-one">
							<div class="col-md-2">
								<i class="fa fa-file-image-o"></i> IMAGES
							</div>
							<div class="col-md-2">
								<i class="fa fa-map-marker"></i> MAP
							</div>
							<div class="col-md-2">
								<i class="fa fa-comment"></i> REVIEWS
							</div>
						</div>
						-->
					</div>
				</div>
				<div class="col-md-4 pad0">
					<div class="depart_hotel_rit11 pad30 black">
						<div class="col-md-6 pad0 ">
							<div class="bold ">CHECK IN</div>
							<div class="">
								<div class="d1">
									<img src="<?php echo $GLOBALS['CI']->template->template_images('calender.png'); ?>" class="callndr">
								</div>
								<div class="itenary_date">
									<span class="txt20 mar_r_3"><?php echo @$header_check_in[0]; ?></span>
								</div>
								<div class="itenary_date1 font12">
									<?php echo strtoupper(@$header_check_in[1]); ?></div>
								</div>
							</div>
							<div class="col-sm-6 col-md-6 pad0 ">
								<div class="bold left10">CHECK OUT</div>
								<div class="left10">
									<div class="d1">
										<img src="<?php echo $GLOBALS['CI']->template->template_images('calender.png'); ?>" class="callndr">
									</div>
									<div class="itenary_date">
										<span class="txt20 mar_r_3"><?php echo @$header_check_out[0]; ?></span>
									</div>
									<div class="itenary_date1 font12">
										<?php echo strtoupper(@$header_check_out[1]); ?></div>
									</div>
								</div>
								<div class="lin_text">
									<div class="line_hr"></div>
									<div class="line_hover_text"><?php echo $hotel_total_nights;?> NIGHT</div>
								</div>
								<div class="clear-fix"></div>	
								<div class="seater top10 bold1"><?php echo $number_of_rooms;?> Room for <?php echo $hotel_total_nights;?> Night</div>
								
							</div>
						</div>
					</div>
				</div>
				<div class="clear-fix"></div>
				<?php  echo get_booking_details($booking_details);  ?>
				<div class="clear-fix"></div>
				<div class="f12">
					<input type="hidden" name="login_modal_reload_status" value="1" id="login_modal_reload_status" class="hide">
					<h3 class="details_fly_h3">Guest </h3>
					<div class="clear-fix"></div>
					<div class="contact_info_mb">
						<div class="row">
							<div class="col-md-3">
								<p class="m0"><i class="fa fa-user"></i> <?php echo $lead_pax_name; ?> </p>
								<p class="m0"><i class="fa fa-phone"></i> <?php echo $pax_contact_number;  ?> </p>
							</div>
							
						</div>
					</div>
				</div>
				<div class="clear-fix"></div>
				
				<div class="clear-fix"></div>
				<?php /* <div class="col-md-12 pad0">
					<div class="refine-search-results f12">
						<dl>
							
							<dt class="" style="border-top:none;">HOTEL CANCELLATION POLICIES</dt>
							<dd style="height: auto;">
								<p><?php  echo $cancellation_policy;  ?></p>
							</dd>

							<!-- <dt class="mt20">FAQ</dt>
							<dd style="height: auto;">
							<?php //echo $faq;?>
							</dd> -->

						</dl>
					</div>
				</div>  */ ?>
				
				<!-- support details -->
				<div class="clear-fix"></div>
					<div class="col-md-12 pad0">
						<div class="refine-search-results f12">
							<dl>
							<dt class="mt20 active">FAQ</dt>
								<dd style="height: auto; display: block;">
									<p class="black bold m0">Q: dummy text come here dummy text come here dummy text come here dummy text come here </p>
									<p>dummy text come here dummy text come here dummy text come here dummy text come here dummy text come here dummy text come here dummy text come here dummy text come here ................... </p>
									<p class="black bold m0">Q: dummy text come here dummy text come here dummy text come here dummy text come here </p>
									<p>dummy text come here dummy text come here dummy text come here dummy text come here dummy text come here dummy text come here dummy text come here dummy text come here ................... </p>
								</dd>
							</dl>
						</div>
					</div>
				<!-- support details -->
				
			</div>
			<!-- PASSENGER -->

<?php 

/**
 * Booking Details Tab
 * @param unknown_type $booking_details
 */

function get_booking_details($booking_details)
{
	$currency = $booking_details['currency'];
	$api_booking_details = '<div class="f12">
					<h3 class="details_fly_h3">Booking Details</h3>
					<div class="clear-fix"></div>
					<div class="contact_info_mb">
						<div class="row">';
		if(valid_array($booking_details)) {
			$api_booking_details .= '<div class="col-md-12">
								<p class="m0"><b>HOTEL BOOKING-ID: '.$booking_details['booking_reference'].'</b> </p>
								<p class="m0"><b>CONFIRMATION-NUMBER: '.$booking_details['confirmation_reference'].'</b></p>
								<p class="m0"><b>HOTEL FARE: '.$currency.' '.$booking_details['grand_total'].'</b></p>
								
							</div>';
		} else {
			$api_booking_details .= '<div class="col-md-3">
								<p class="m0">Not Available</p>
							</div>';
		}
		$api_booking_details .= '</div>
					</div>
				</div>';
	return $api_booking_details;
}

?>

<script type="text/javascript">
$(document).ready(function () {
	$('dt').each(function() {
	  var tis = $(this), state = false, answer = tis.next('dd').hide().css('height','auto').slideUp();
	  tis.click(function() {
	  state = !state;
	  answer.slideToggle(state);
	  tis.toggleClass('active',state);
	  });
	});
});
</script>

<?php 

/** Menu **/
function hotel_menu($booking_details,$customer_email){
	$v = array();
	$v['app_reference'] = $booking_details['app_reference'];
	$v['booking_source'] = $booking_details['booking_source'];
	$v['status'] = $booking_details['status'];
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
						<div class="all-type-img1">Email Voucher</div>
				</li>
				<li class="print_ticket" data-reference_id="'.$v['booking_source'].'" data-app_reference="'.$v['app_reference'].'" data-status="'.$v['status'].'">
						<div class="img_mb1">
							<img src="'.$GLOBALS['CI']->template->template_images('icons/print.png').'" />
						</div>
						<div class="all-type-img1">Print Voucher</div>
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
				$.get(app_base_url+'index.php/'+controller_method+'/get_hotel_voucher?reference_id='+reference_id+'&app_reference='+app_reference+'&status='+status, function (response) {
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
				$.get(app_base_url+'index.php/'+controller_method+'/get_hotel_invoice?reference_id='+reference_id+'&app_reference='+app_reference+'&status='+status, function (response) {
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

			//Email Voucher
			$('#send_mail_btn').click(function (){
				var reference_id = $('#mail_ticket_reference_id').val().trim();
				var app_reference = $('#mail_ticket_app_reference').val().trim();
				var status = $('#mail_ticket_status').val().trim();
				var user_email_id = $('#ticket_email_id').val().trim();

				if(user_email_id !='') {
					$('#mail_ticket_error_message').empty();
					$('#mail_loader_image').show();
					
					var	controller_method = 'report/email_hotel_voucher';
						
					$.get(app_base_url+'index.php/'+controller_method+'/'+app_reference+'/'+reference_id+'/'+status+'/'+user_email_id, function (response) {
						$('#mail_ticket_modal').modal('toggle');
						var mail_status_message = '';
						if(response.status == true) {
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
