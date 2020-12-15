<?php 
$pagination = '<div class="pull-right">'.$GLOBALS['CI']->pagination->create_links().'</div>';
?>
<div class="content-wrapper dashboard_section">
	<div class="container">
	<div class="staffareadash">
		<?php echo $GLOBALS['CI']->template->isolated_view('share/profile_navigator_tab') ?>

			<div class="bakrd_color">
			
			<div class="cetrel_all">
				<?php echo $GLOBALS['CI']->template->isolated_view('share/navigation') ?>
			</div>
			

			<div class="tab-content">
				<div role="tabpanel" class="tab-pane active" id="mybookings">
					<div class="trvlwrap">
						<div class="topbokshd_pagination">
							 <?=$pagination?>
						</div>
						<?php
						if(valid_array($table_data['booking_details']) == true) {
							$booking_details = $table_data['booking_details'];
							foreach($booking_details as $parent_k => $parent_v) { 
								// debug($booking_details);exit;
								extract($parent_v);
								$pickup_date = $car_from_date.' '.$pickup_time;
								$depart_date = $car_from_date.' '.$pickup_time;
							?>
								<div class="full_bkingg">
									<div class="bookrow">
										<div class="topbokro">
											<h4 class="bokrname">
															<span class="fa fa-car"></span>
																<?=$car_name?>, <?=$car_supplier_name?>
															</h4>
											<div class="pnrnum">
												RefID: <strong><?=$app_reference?></strong>
											</div>
										</div>
										<div class="clearfix"></div>
										<div class="remful">
											<div class="xlbook col-sm-2 col-xs-3 htlimage_xl">
												<div class="imghtlflt">
													<img src="<?=$car_image?>" alt="CarImage" />
												</div>
											</div>
											<div class="xlbook col-sm col-xs-9 htlimage_xl ">
												<div class="htlfltr">
													<h3 class="shtlname"><?=$car_name?></h3>
													<div class="shtlname"><?=app_friendly_absolute_date($pickup_date)?> to <?=app_friendly_absolute_date($depart_date)?></div>
													<div class="starrting">
														<?php //echo print_star_rating($star_rating);?>
													</div>
													<div class="clearfix"></div>
													<div class="bokdby">Lead Pax:<strong><?=$lead_pax_name?></strong></div>
													<div class="bokdby">Voucher date<strong><?=$voucher_date?></strong></div>
												</div>
											</div>
											<div class="xlbook col-xs-3 bordbor">
												<div class="sideprice">
													<?=$currency?><?=$grand_total?>
												</div>
												<div class="pxconf green"><?=$status?></div>
												<?=action_tab($app_reference, $booking_source, $status)?>
											</div>
										</div>
						
									</div>
								</div>
							<?php }
							} else { ?>
								<div class="noresultfnd">
									<div class="imagenofnd"><img src="<?=$GLOBALS['CI']->template->template_images()?>empty.jpg" alt="Empty" /></div>
									<div class="lablfnd">No Data Found!!!</div>
								</div>
						   <?php } ?>
					 <?=$pagination?>
					</div>
				</div>
			</div>

		</div>
	</div>
	</div>
</div>
<?php
/**
 * Action Tab
 * @param $app_reference
 * @param $booking_source
 */ 
function action_tab($app_reference, $booking_source, $status)
{
	$action_tab  = '';
	$ticket_details = '<a href="'.base_url().'index.php/voucher/car/'.$app_reference.'/'.$booking_source.'" class="viwedetsb">View</a>';
	if($status == 'BOOKING_CONFIRMED') {
		$cancel_booking = '<a href="'.base_url().'index.php/car/pre_cancellation/'.$app_reference.'/'.$booking_source.'" class="viwedetsb">Cancel</a>';
		$cancel_booking .= '<form action="" method="POST" name="shareemail-form">
														<input type="hidden" value="'.$app_reference.'" name="app_reference" />
														<input type="hidden" value="'.$booking_source.'" name="booking_source" />
														<input type="hidden" value="'.$status.'" name="status" />
														<input type="hidden" value="car" name="module" />
														<input type="submit" value="Send Email" class="btn btn-sm btn-warning share_sub viwedetsb" />
												</form>
												<div class="loading hide" id="loading"><img src="'.$GLOBALS['CI']->template->template_images('loader_v3.gif').'"></div>';
	} else {
		$cancel_booking = '';
	}
	$action_tab .= $ticket_details;
	$action_tab .= $cancel_booking;
	return $action_tab;
}
?>
<!-- Mail - Voucher  starts-->
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
	<!-- Mail - Voucher  ends-->	
	
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

<!-- print - Voucher  starts-->
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
<!-- print - Voucher  ends-->
	

	<script>
		$(document).ready(function(){
			//Voucher Popup
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
			
			//Shows Send Voucher Modal
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
		  $(document).on('submit', "form[name='shareemail-form']", function(e){
	         e.preventDefault();
		     $('.error').html('');
		     $("#loading").removeClass("hide");
		      $.ajax({
		          url: app_base_url+'index.php/voucher/email_ticket',
		      	  type: "POST",
		          dataType: 'json',
		          data:  new FormData(this),
		          contentType: false,
		          cache: false,
		          processData:false,
		      	success: function(data){
		      	$("#loading").addClass("hide");
		         alert("Thank you. Your request is being processed and you will receive ticket by email ");
		          $('#email').html('');
		                                                   
		      },error: function(){
		      
		      }           
		    });
		  return false;  
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
