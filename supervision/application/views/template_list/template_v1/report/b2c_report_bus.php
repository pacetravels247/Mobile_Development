<!--  <script src="<?php echo SYSTEM_RESOURCE_LIBRARY?>/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo SYSTEM_RESOURCE_LIBRARY?>/datatables/dataTables.bootstrap.min.js"></script> -->
<?=$GLOBALS['CI']->template->isolated_view('report/email_popup')?>
<?php
if (is_array($search_params)) {
	extract($search_params);
}
$_datepicker = array(array('created_datetime_from', PAST_DATE), array('created_datetime_to', PAST_DATE));
$this->current_page->set_datepicker($_datepicker);
$this->current_page->auto_adjust_datepicker_report(array(array('created_datetime_from', 'created_datetime_to')));
?>
<div class="modal fade" id="pax_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">  
    <div class="modal-dialog" role="document" style="width: 880px;">    <div class="modal-content">    <div class="modal-header">        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>        <h4 class="modal-title" id="myModalLabel"><i class="fa fa-users"></i> 
                    Customer Details</h4> 
            </div>   
            <div class="modal-body">  
                <div id="customer_parameters">    	
                </div>   
            </div>  
        </div> 
    </div>
</div>
<div class="bodyContent col-md-12">
	<div class="panel panel-default clearfix"><!-- PANEL WRAP START -->
			<div class="panel-heading"><!-- PANEL HEAD START -->
				<?=$GLOBALS['CI']->template->isolated_view('report/report_tab_b2c')?>
		</div><!-- PANEL HEAD START -->
			<?php //echo advanced_search_form(@$from_date, @$to_date);?>
			<div class="panel-body">
				<div class="clearfix">
					<?php echo $GLOBALS['CI']->template->isolated_view('report/make_search_easy'); ?>
					
				</div>
				<hr>			
				<h4>Advanced Search Panel <button class="btn btn-primary btn-sm toggle-btn" data-toggle="collapse" data-target="#show-search">+
					</button> </h4>
				<hr>
			<div id="show-search" class="collapse">
				<form method="GET" autocomplete="off">
					<input type="hidden" name="created_by_id" value="<?=@$created_by_id?>" >
					<div class="clearfix form-group">
						<div class="col-xs-4">
							<label>
							Application Reference
							</label>
							<input type="text" class="form-control" name="app_reference" value="<?=@$app_reference?>" placeholder="Application Reference">
						</div>
						<div class="col-xs-4">
							<label>
							PNR
							</label>
							<input type="text" class="form-control" name="pnr" value="<?= @$pnr?>" placeholder="PNR">
						</div>
						<!-- <div class="col-xs-4">
							<label>
							Phone
							</label>
							<input type="text" class="form-control numeric" name="phone" value="<?=@$phone?>" placeholder="Phone">
						</div>
						<div class="col-xs-4">
							<label>
							Email
							</label>
							<input type="text" class="form-control" name="email" value="<?=@$email?>" placeholder="Email">
						</div> -->
						<div class="col-xs-4">
							<label>
							Status
							</label>
							<select class="form-control" name="status">
								<option>All</option>
								<?=generate_options($status_options, array(@$status))?>
							</select>
						</div>
						<div class="col-xs-4">
							<label>
							Booked From Date
							</label>
							<input type="text" readonly id="created_datetime_from" class="form-control" name="created_datetime_from" value="<?=@$created_datetime_from?>" placeholder="Request Date">
						</div>
						<div class="col-xs-4">
							<label>
							Booked To Date
							</label>
							<input type="text" readonly id="created_datetime_to" class="form-control disable-date-auto-update" name="created_datetime_to" value="<?=@$created_datetime_to?>" placeholder="Request Date">
						</div>
					</div>
					<div class="col-sm-12 well well-sm">
					<button type="submit" class="btn btn-primary">Search</button> 
					<button type="reset" class="btn btn-warning">Reset</button>
					<a href="<?php echo base_url().'index.php/report/b2c_bus_report?' ?>" id="clear-filter" class="btn btn-primary">Clear Filter</a>
					</div>
				</form>
			</div>
			<!-- EXCEL/PDF EXPORT STARTS -->
            <?php if($total_records > 0){ ?>
            <div class="clearfix"></div>
                <div class="dropdown col-xs-3">
                    <button class="btn btn-info dropdown-toggle" type="button" id="excel_imp_drop" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <i class="fa fa-download" aria-hidden="true"></i> Excel
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="excel_imp_drop">
                        <li >
                            <a href="<?php echo base_url(); ?>index.php/report/export_confirmed_booking_bus_report/excel<?= !empty($_SERVER["QUERY_STRING"])?'?'.$_SERVER["QUERY_STRING"]:''?>">Confirmed Booking</a>
                        </li>
                        <li>
                            <a href="<?php echo base_url(); ?>index.php/report/export_cancelled_booking_bus_report/excel<?= !empty($_SERVER["QUERY_STRING"])?'?'.$_SERVER["QUERY_STRING"]:''?>">Cancelled Booking</a>
                        </li>
                    </ul>
                </div>
            
            <?php } ?>
            <!-- EXCEL/PDF EXPORT ENDS -->
		</div>
		
		<div class="clearfix" style="overflow: auto;"><!-- PANEL BODY START -->
					<div class="pull-left">
					<?php echo $this->pagination->create_links();?> <span class="">Total <?php echo $total_rows ?> Bookings</span>
					</div>
					<table class="table table-condensed table-bordered" id="b2c_report_bus_table">
					<thead>
						<tr>
							<th>Sno</th>
							<th>Application Reference</th>
							<th>Customer details</th>
							<th>PNR</th>
							<th>Operator</th>
							<th>from</th>
							<th>to</th>
							<th>Seat type</th>
							<th>Comm.Fare</th>
							<th>Commission</th>
							<th>TDS</th>
							<th>NetFare</th>
							<th>Conv.Fee</th>
							<th>Markup</th>
							<th>GST</th>
							<th>Discount</th>
							<th>TotalFare</th>
							<th>BookedOn</th>
							<th>Travel date</th>
							<th>Status</th>
							<th>Payment Status</th>
							<th>Action</th>
						</tr>
						</thead>
						<tfoot>
						<tr>
							<th>Sno</th>
							<th>Application Reference</th>
							<th>Customer details</th>
							<th>PNR</th>
							<th>Operator</th>
							<th>from</th>
							<th>to</th>
							<th>Seat type</th>
							<th>Comm.Fare</th>
							<th>Commission</th>
							<th>TDS</th>
							<th>NetFare</th>
							<th>Conv.Fee</th>
							<th>Markup</th>
							<th>GST</th>
							<th>Discount</th>
							<th>TotalFare</th>
							<th>BookedOn</th>
							<th>Travel date</th>
							<th>Status</th>
							<th>Payment Status</th>
							<th>Action</th>
						</tr>
						</tfoot>
						<tbody>
						<?php

							if (isset($table_data) == true and valid_array($table_data['booking_details']) == true) {
								$booking_details = $table_data['booking_details'];
								// debug($booking_details);exit;
								$segment_3 = $GLOBALS['CI']->uri->segment(3);
								$current_record = (empty($segment_3) ? 1 : $segment_3);
								foreach($booking_details as $parent_k => $parent_v) {

									extract($parent_v);
									
									$action = '';
									$tdy_date = date ( 'Y-m-d' );
									$jrny_date = date('Y-m-d', strtotime($journey_datetime));
									$diff = get_date_difference($tdy_date,$jrny_date);
									$customer_details = customer_details($app_reference, $booking_source, $status);
									$action .= bus_voucher($app_reference, $booking_source, $status);
									$action .= '<br/>';
									$action .= bus_pdf($app_reference, $booking_source, $status);
									$action.='<br/>';
									$action .= bus_voucher_email($app_reference, $booking_source, $status,$parent_v['email']);
									$action .= '<br />' . $customer_details;
                                                                        $action .= bus_GST_Invoice($app_reference, $booking_source, $status, 'b2c');
									$action.='<br/>';

									if($diff > 0){
										$action .= bus_cancel($app_reference, $booking_source, $status);
									}
								?>
									<tr>
										<td><?php echo ($current_record++)?></td>
										<td><?php echo $app_reference;?></td>
										<td>
										<?php echo $lead_pax_name. '<br/>'.
										  $lead_pax_email."<br/>".
										  $lead_pax_phone_number;?>
										</td>									
										<td class=""><?php echo $pnr?></td>
										<td><?php echo $operator?></td>
										<td><?php echo ucfirst($departure_from)?></td>
										<td><?php echo ucfirst($arrival_to)?></td>
										<td><?php echo $bus_type?></td>
										<td><?php echo $fare?></td>
										<td><?php echo $admin_commission?></td>
										<td><?php echo ($admin_commission*5)/100?></td>
										<td><?php echo $fare-$admin_commission+($admin_commission*5)/100?></td>
										<td><?php echo $convinence_amount?></td>
										<td><?php echo $admin_markup?></td>
										<td><?php echo roundoff_number($gst)?></td>
										<td><?php echo $discount?></td>
										<td><?php echo $grand_total?></td>
										<td><?php echo date('d-m-Y', strtotime($booked_date))?></td>
										<td><?php echo date('d-m-Y', strtotime($journey_datetime))?></td>
										<td><span class="<?php echo booking_status_label($status) ?>"><?php echo $status?></span></td>	
										<td><span class="<?php echo booking_status_label($booking_payment_details[0]['status']) ?>"><?php echo $booking_payment_details[0]['status']?></span></td>
										<td><div class="" role="group"><?php echo $action; ?></div></td>
								</tr>
								<?php
								}
							} else {
								echo '<tr><td>---</td><td>---</td><td>---</td><td>---</td><td>---</td><td>---</td>
								 		  <td>---</td><td>---</td><td>---</td><td>---</td><td>---</td><td>---</td>
										  <td>---</td><td>---</td><td>---</td><td>---</td><td>---</td></tr>';
							}
						?>
						</tbody>
					</table>
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

	return '<a class="btn btn-sm btn-primary send_email_voucher" data-app-status="'.$status.'"   data-app-reference="'.$app_reference.'" data-booking-source="'.$booking_source.'"data-recipient_email="'.$recipient_email.'"><i class="far fa-envelope"></i> Email Ticket</a>';
}
/*
 * Advanced Search Form
 */
function advanced_search_form($from_date, $to_date)
{
	$uri_function = $GLOBALS['CI']->uri->segment(2);
	$advanced_search_form = '<div class="tab-pane active clearfix" role="tabpanel">
							 <div class="panel panel-default">';
	$advanced_search_form .= '<div class="panel-heading">Advanced Search'; 
	$advanced_search_form .= ' <button class="btn btn-sm btn-info collapsed show_form" id="advance_search_btn_label" data-toggle="collapse" href="#advance_search_form_container" aria-expanded="false" aria-controls="advance_search_form_container">+</button>';
	$advanced_search_form .= '</div>';
	$advanced_search_form .= '<div class="panel-body panel-collapse collapse" id="advance_search_form_container"  role="tabpanel" >';
	$advanced_search_form .= '<form role="form" class="form-horizontal" autocomplete="off" method="get" action="'.base_url().'report/'.$uri_function.'">
							        <div class="form-group">
							            <label class="col-sm-1 control-label"> From </label>
							            <div class="col-sm-2">
							                <input type="text" value="'.$from_date.'" placeholder="From Date" name="from_date" class="form-control" id="from_date">
							            </div>
							            <label class="col-sm-1 control-label"> To </label>
							            <div class="col-sm-2">
							                <input type="text" value="'.$to_date.'" placeholder="To Date" name="to_date" class="form-control" id="to_date">
							            </div>
							            <div class="col-sm-1">
							                <button type="submit" class="btn btn-success ">Search</button>
							            </div>
							            <div class="col-sm-1">
							                <a href="'.base_url().'report/'.$uri_function.'" class="btn btn-warning ">Reset</a>
							            </div>
							        </div>
							    </form>';
	$advanced_search_form .= '</div>
							</div>
							</div>';
	return $advanced_search_form;
}
function customer_details($app_reference, $booking_source = '', $status = '') {
        return '<a  target="_blank" data-app-reference="' . $app_reference . '" data-booking-status="' . $status . '" data-booking-source="' . $booking_source . '" class="btn btn-sm btn-primary flight_u customer_details"><i class="fa fa-file-o"></i> <small>Pax profile</small></a>';
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
	// $('#b2c_report_bus_table').DataTable({
 //        // Disable initial sort 
 //        "aaSorting": []
 //    });
	$("#from_date").change(function() {
		//validate_from_to_dates($(this), 'from_date', 'to_date');
	});
  /*
  *Sagar Wakchaure
  *Email voucher
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
});

        $(document).on('click', '.customer_details', function (e) {
            
            e.preventDefault();
            //$(this).attr('disabled', 'disabled');//disable button
            var app_ref = $(this).data('app-reference');
            var booking_src = $(this).data('booking-source');
            var status = $(this).data('booking-status');
            var module = 'bus';

            jQuery.ajax({
                type: "GET",
                url: app_base_url + 'index.php/report/get_customer_details/' + app_ref + '/' + booking_src + '/' + status + '/' + module + '/',
                dataType: 'json',
                success: function (res) {

                    $('#customer_parameters').html(res.data);
                    $('#pax_modal').modal('show');
                }
            });
        });

</script>
<?php
$this->current_page->set_datepicker ( array (array ('from_date',PAST_DATE),array ('to_date',PAST_DATE)));
$this->current_page->enable_javascript ();
?>