<?=$GLOBALS['CI']->template->isolated_view('report/email_popup')?>
<?php
//debug($table_data); die;
if (is_array($search_params)) {
	extract($search_params);
}

// if(is_domain_user() == false){ 
// 	$show_attr = true;
// } else {
// 	$show_attr = false;
// }

$_datepicker = array(array('created_datetime_from', PAST_DATE), array('created_datetime_to', PAST_DATE));
$this->current_page->set_datepicker($_datepicker);
$this->current_page->auto_adjust_datepicker_report(array(array('created_datetime_from', 'created_datetime_to')));
?>
<div class="bodyContent col-md-12">
	<div class="panel panel-default clearfix"><!-- PANEL WRAP START -->
			<div class="panel-heading"><!-- PANEL HEAD START -->
				<?=$GLOBALS['CI']->template->isolated_view('report/report_tab_b2c')?>
		</div><!-- PANEL HEAD START -->
		<div class="panel-body">
			<div class="clearfix">
				<?php echo $GLOBALS['CI']->template->isolated_view('report/make_search_easy'); ?>
				
			</div>
			<hr class="ad_search_hr">
			<h4>Advanced Search Panel <button class="btn btn-primary btn-sm toggle-btn" data-toggle="collapse" data-target="#show-search">+
				</button> </h4>
			<hr class="ad_search_hr">
			<div id="show-search" class="collapse">
				<form method="GET" autocomplete="off" action="<?= base_url().'report/hotel?' ?>">
					<input type="hidden" name="created_by_id" value="<?=@$created_by_id?>" >
					<div class="clearfix form-group">
					
						<div class="col-xs-4">
							<label>
							Agent
							</label>
							<select class="form-control" name="domain_origin">
								<option>All</option>
								<?=generate_options($domain_list, array(@$domain_origin))?>
							</select>
						</div>
						
						<div class="col-xs-4">
							<label>
							Application Reference
							</label>
							<input type="text" class="form-control" name="app_reference" value="<?=@$app_reference?>" placeholder="Application Reference">
						</div>
						
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
					<a href="<?php echo base_url().'index.php/report/hotel?' ?>" id="clear-filter" class="btn btn-primary">Clear Filter</a>
					</div>
				</form>
			</div>
			<!-- EXCEL/PDF EXPORT STARTS -->
			<?php if($total_records > 0){ ?>
			<a href="<?php echo base_url(); ?>index.php/report/export_hotel_report/excel<?= !empty($_SERVER["QUERY_STRING"])?'?'.$_SERVER["QUERY_STRING"]:''?>">
					<button class="btn btn-info" type="button"><i class="far fa-download" aria-hidden="true"></i> Excel</button>
			</a> 
			<a href="<?php echo base_url(); ?>index.php/report/export_hotel_report/pdf<?= !empty($_SERVER["QUERY_STRING"])?'?'.$_SERVER["QUERY_STRING"]:''?>">
					<button class="btn btn-success" type="button"><i class="far fa-download" aria-hidden="true"></i> PDF</button>
			</a>
			<?php } ?>
			<!-- EXCEL/PDF EXPORT ENDS -->
		</div>
		
		<div class="clearfix" style="overflow: auto"><!-- PANEL BODY START -->
			<?php //echo get_table($table_data, $total_rows);?>
			<div class="pull-left">
					<?php echo $this->pagination->create_links();?> <span class="">Total <?php echo $total_rows ?> Bookings</span> 
					</div>
			<table class="table table-condensed table-bordered example3" id="b2c_report_airline_table">
				<thead>					
				<tr>
					<th>Sno</th>
					<th>Domain Name</th>
					<th>App Reference</th>
					<th>Status</th>
					<th>Confirm.Number</th>
					<th>Customer</th>
					<th>Car Name</th>
					<th>Suppier Name</th>
					<th>Suppier Identifier</th>
					<th>From</th>
					<th>To</th>
					<th>PickUp DateTime</th>
					<th>Drop DateTime</th>
					<th>Booked On</th>
					<th>Comm.Fare</th>
					<th>TDS</th>
					<th>Admin <br/>Markup</th>
					<th>Convn.Fee</th>
					<th>Discount</th>
					<th>Grand Total</th>	
					<th>Action</th>
				</tr>
				</thead>
				<tfoot>
				<tr>
					<th>Sno</th>
					<th>Domain Name</th>	
					<th>App Reference</th>
					<th>Status</th>
					<th>Confirm.Number</th>
					<th>Customer</th>
					<th>Car Name</th>
					<th>Suppier Name</th>
					<th>Suppier Identifier</th>
					<th>From</th>
					<th>To</th>
					<th>PickUp DateTime</th>
					<th>Drop DateTime</th>
					<th>Booked On</th>
					<th>Comm.Fare</th>
					<th>TDS</th>
					<th>Admin <br/>Markup</th>
					<th>Convn.Fee</th>
					<th>Discount</th>
					<th>Grand Total</th>	
					<th>Action</th>
				</tr>
				</tfoot><tbody>
				<?php
				// debug($table_data);exit;
				if (isset($table_data) == true and valid_array($table_data) == true) {
				$segment_3 = $GLOBALS['CI']->uri->segment(3);
				$current_record = (empty($segment_3) ? 0 : $segment_3);
				$booking_details = $table_data['booking_details'];
				// debug($booking_details); exit;
				$s=0;
			    foreach($booking_details as $parent_k => $parent_v) { 
			    	// debug($parent_v);exit;
			        	extract($parent_v);
			        	//debug($parent_v);die;
			       //$domain_markup = $admin_markup_gst;
			       	if($booking_source == 'PTBSID0000000017'){
			       		$booking_api = 'Carnet';
			       	}
			       
		        
				$action = '';
				$emails='';
				$tdy_date = date ( 'Y-m-d' );
				$diff = get_date_difference($tdy_date,$car_from_date);
				$action .= car_voucher($app_reference, $booking_source, $status);
				$action.='<br/>';
				$action .= car_pdf($app_reference, $booking_source, $status);
				$action.='<br/>';
				$action .= car_voucher_email($app_reference, $booking_source,$status,$parent_v['email']);
				$action.='<br/>';
				if($status == 'BOOKING_CONFIRMED' && $diff > 0) {
					$action .= cancel_car_booking($app_reference, $booking_source, $status);
				}
				if($status=='BOOKING_FAILED'){
					$action .=get_exception_log_button($app_reference,$booking_source,$status);
				}
				$emails = car_email_voucher($app_reference, $booking_source, $status);
				$action .= get_cancellation_details_button($parent_v['app_reference'], $parent_v['booking_source'], $parent_v['status']);

				//check hotel pending booking status

				$action .=get_booking_pending_status($parent_v['app_reference'], $parent_v['booking_source'], $parent_v['status']);
				
				
			
			?>
			<tr>
				<td><?php echo ($re = $current_record+$parent_k+1)?></td>
				<td><?php echo @$domain_name; ?></td>
				<td><?php echo $app_reference;?></td>
				<td><span class="<?php echo booking_status_label($status) ?>"><?php echo $status?></span></td>
				<td><?php echo $booking_reference;?></td>
				<td>
				<?php echo $lead_pax_name. '<br/>'.
				  $lead_pax_email."<br/>".
				  $lead_pax_phone_number;?>
				</td>									
				<td><?php echo $car_name ?></td>
				<td><?php echo $car_supplier_name ?></td>
				<td><?php echo $supplier_identifier ?></td>
				<td><?php echo $car_pickup_lcation?> </td>
				<td><?php echo $car_drop_location?> </td>
				<td><?php echo $car_from_date?> <?php echo $pickup_time; ?></td>
				<td><?php echo $car_to_date?> <?php echo $drop_time; ?></td>
				<td><?php echo date('d-m-Y H:i:s', strtotime($created_datetime))?></td>
				<td ><?php echo $total_fare; ?></td>
				<td ><?php echo @$admin_tds; ?></td>
				<td ><?php echo $admin_markup; ?></td>
				<td ><?php echo $convinence_amount; ?></td>
				<td ><?php echo $discount; ?></td>
				<td ><?php echo $grand_total; ?></td>
				<td><div class="" role="group"><?php echo $action; ?></div></td>
			</tr>
			<?php
			$s++;
			if(empty($segment_3 = $GLOBALS['CI']->uri->segment(3)))
			{if($re>=20){ break; }
			}else{ if($s>=20) { break; }
			}
			}
			// die;
		}
		else {

			echo '<tr><td colspan="15">No Data Found</td></tr>';
		}
	?>
				</tbody>
			</table>
			<div class="pull-left">
					<?php echo $this->pagination->create_links();?>  <span class="">Total <?php echo $total_rows ?> Bookings</span> 
					</div>
		</div>
	</div>
</div>

<?php

function get_accomodation_cancellation($courseType, $refId)
{
	return '<a href="'.base_url().'index.php/booking/accomodation_cancellation?courseType='.$courseType.'&refId='.$refId.'" class="col-md-12 btn btn-sm btn-danger "><i class="far fa-exclamation-triangle"></i> Cancel</a>';
}
function car_voucher_email($app_reference, $booking_source,$status,$recipient_email)
{

	return '<a class="btn btn-sm btn-primary send_email_voucher flight_e" data-app-status="'.$status.'"   data-app-reference="'.$app_reference.'" data-booking-source="'.$booking_source.'"data-recipient_email="'.$recipient_email.'"><i class="far fa-envelope"></i> Email Voucher</a>';
}

function get_cancellation_details_button($app_reference, $booking_source, $status)
{
	if($status == 'BOOKING_CANCELLED'){
		return '<a target="_blank" href="'.base_url().'car/cancellation_refund_details?app_reference='.$app_reference.'&booking_source='.$booking_source.'&status='.$status.'" class=" col-md-12 btn btn-sm btn-info flight_u"><i class="far fa-info"></i> Cancellation Details</a>';
		
		//return '<a target="_blank" href="'.base_url().'hotel/ticket_cancellation_details?app_reference='.$app_reference.'&booking_source='.$booking_source.'&status='.$status.'" class=" col-md-12 btn btn-sm btn-info flight_u"><i class="far fa-info"></i> Cancellation Details</a>';
	}
}
function get_booking_pending_status($app_reference, $booking_source, $status)
{
	if($status == 'BOOKING_HOLD'){
		return '<a class="get_car_status col-md-12 btn btn-sm btn-info flight_u" id="pending_status_'.$app_reference.'" data-booking-source="'.$booking_source.'"
			data-app-reference="'.$app_reference.'" data-status="'.$status.'"><i class="far fa-info"></i>Update Supplier Info</a>';
	}
}

function get_exception_log_button($app_reference,$booking_source, $master_booking_status) {
    if (is_domain_user() == false) {
        return '<a data-app_reference="' . $app_reference . '" data-booking_source="' . $booking_source . '" data-status="' . $master_booking_status . '" class="error_log btn btn-sm btn-danger "><i class="far fa-exclamation"></i> <small>ErroLog</small></a>';
    }
}
?>
<!-- Exception Log Modal starts -->
<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" id="exception_log_modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="gridSystemModalLabel">Error Log Details - <strong><i id="exception_app_reference"></i></strong></h4>
            </div>
            <div class="modal-body" id="exception_log_container">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Exception Log Modal ends -->
<script>

$(document).ready(function() {
   

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
					var _opp_url = app_base_url+'index.php/voucher/car/';
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
	  $(".get_car_status").on("click",function(e){
	  		
  		 	app_reference = $(this).data('app-reference');
	        book_reference = $(this).data('booking-source');
	        app_status = $(this).data('status');
	        var _opp_url = app_base_url+'index.php/car/get_pending_booking_status/';
			_opp_url = _opp_url+app_reference+'/'+book_reference+'/'+app_status;
			toastr.info('Please Wait!!!');
			$.get(_opp_url, function(res) {
				if(res==1){
					toastr.info('Status Updated Successfully!!!');	
					//location.reload(); 
				}else{
					toastr.info('Status not updated');
				}
				
				$("#mail_voucher_modal").modal('hide');
			});
	  });
	    $(document).on('click', '.error_log', function (e) {
            e.preventDefault();
            var app_reference = $(this).data('app_reference');
            var booking_source = $(this).data('booking_source');
            var status = $(this).data('status');
            $.get(app_base_url + 'index.php/car/exception_log_details?app_reference=' + app_reference + '&booking_source=' + booking_source + '&status=' + status, function (response) {
                $('#exception_app_reference').empty().text(app_reference);
                $('#exception_log_container').empty().html(response);
                $('#exception_log_modal').modal();

            });
        });
});
</script>
<script type="text/javascript">
$(document).ready(function(){
    $('[data-toggle="admin_net_fare"]').tooltip();   
});
</script>
