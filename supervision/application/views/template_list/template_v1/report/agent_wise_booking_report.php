<?php
if (is_array($search_params)) {
	extract($search_params);
}
$_datepicker = array(array('created_datetime_from', PAST_DATE), array('created_datetime_to', PAST_DATE));
$this->current_page->set_datepicker($_datepicker);
$this->current_page->auto_adjust_datepicker_report(array(array('created_datetime_from', 'created_datetime_to')));
?>
<div class="container-fluid">
	<h3>Booking Report</h3>
	<div class="col-sm-12 sup-main-div mt-1">
		<form method="POST" autocomplete="off" id="search_form" action="<?php echo base_url("report/b2b_booking_report"); ?>">
		<div class="row">
			<div class="col-sm-offset-2 col-sm-2 padfive">
				<input type="text" readonly id="created_datetime_from" class="form-control input-field" name="created_datetime_from" value="<?=@$created_datetime_from?>" placeholder="Request Date">
				<!-- <input type="text" readonly id="created_datetime_from" class="form-control" name="created_datetime_from" value="<?= @$created_datetime_from ?>" placeholder="Request Date"> -->
			</div>
			<div class="col-sm-2 padfive">
				<input type="text" readonly id="created_datetime_to" class="form-control input-field disable-date-auto-update" name="created_datetime_to" value="<?=@$created_datetime_to?>" placeholder="Request Date">
				<!-- <input type="text" readonly id="created_datetime_to" class="form-control disable-date-auto-update" name="created_datetime_to" value="<?= @$created_datetime_to ?>" placeholder="Request Date"> -->
			</div>
			<div class="col-sm-2 padfive">
				<button class="btn btn-primary form-control radius-4" type="submit">Generate</button>
			</div>
			<div class="col-sm-2 padfive">
				<button class="btn btn-danger form-control radius-4" type="reset">Reset</button>
			</div>
			</form>
			<form method="POST" autocomplete="off" id="excel_export_form" action="<?php echo base_url("report/export_agent_wise_booking_report"); ?>">
				<div class="col-sm-2 padfive">
					<input type="hidden" name="from_date" id="from_date">
					<input type="hidden" name="to_date" id="to_date">
					<a class="btn btn-danger form-control radius-4" id="excel_export" target="_blank" name="excel_export" value="1">Export</a>
				</div>
			</form>
		</div>
	
		<div class="row mt-2">
			<div class="table-responsive ">
				<table class="table datatables-td">
					<thead>
				        <tr>
				            <th>Sl No.</th>
							<th>Agent Name</th>
							<th>Flight Confirm (PNR)</th>
							<th>Flight Confirm (Seats)</th>
							<th>Flight Cancel (PNR)</th>
							<th>Flight Cancel (Seats)</th>
							<th>Bus Confirm (PNR)</th>
							<th>Bus Confirm (Seats)</th>
							<th>Bus Cancel (PNR)</th>
							<th>Bus Cancel (Seats)</th>
							<th>Email</th>
							<th>Address</th>
							<th>Mobile</th>
				        </tr>
				    </thead>
					<tbody>
				    	<?php $sl = 0;  
								  $flight_pnr_confirm_count = 0;
								  $flight_pnr_confirm_seat_count = 0;
								  $flight_pnr_cancel_count = 0;
								  $flight_pnr_cancel_seat_count = 0;
								  $bus_pnr_confirm_count = 0;
								  $bus_pnr_confirm_seat_count = 0;
								  $bus_pnr_cancel_count = 0;
								  $bus_pnr_cancel_seat_count = 0;	
								  foreach ($data as $key => $value) { 
							 		$sl = $sl+1; ?>
								<tr>
								<td><?php echo $sl; ?></td>
								<td><?php echo $value['agency_name'].'['.provab_decrypt($value['agent_id']).']'; ?></td>
								<td><?php $flight_pnr_confirm_count += $value['flight_pnr_confirm_count'];  echo $value['flight_pnr_confirm_count']; ?></td>
								<td><?php $flight_pnr_confirm_seat_count += $value['flight_pnr_confirm_seat_count']; echo $value['flight_pnr_confirm_seat_count']; ?></td>
								<td><?php $flight_pnr_cancel_count += $value['flight_pnr_cancel_count']; echo $value['flight_pnr_cancel_count']; ?></td>
								<td><?php $flight_pnr_cancel_seat_count += $value['flight_pnr_cancel_seat_count']; echo $value['flight_pnr_cancel_seat_count']; ?></td>
								<td><?php $bus_pnr_confirm_count += $value['bus_pnr_confirm_count']; echo $value['bus_pnr_confirm_count']; ?></td>
								<td><?php $bus_pnr_confirm_seat_count += $value['bus_pnr_confirm_seat_count']; echo $value['bus_pnr_confirm_seat_count']; ?></td>
								<td><?php $bus_pnr_cancel_count += $value['bus_pnr_cancel_count']; echo $value['bus_pnr_cancel_count']; ?></td>
								<td><?php $bus_pnr_cancel_seat_count += $value['bus_pnr_cancel_seat_count']; echo $value['bus_pnr_cancel_seat_count']; ?></td>
								<td><?php echo provab_decrypt($value['email']); ?></td>
								<td><?php echo $value['address']; ?></td>
								<td><?php echo $value['phone']; ?></td>
							</tr>
							<?php } ?>
				    </tbody>
				</table>
			</div>

			<div class="table-responsive mt-2 re-details">
				<table class="table">

					<tr>
						<td>Total Flight Confirm (PNR): &nbsp;<strong><?php echo $flight_pnr_confirm_count; ?></strong></td>
						<td>Total Flight Confirm (Seats) : &nbsp;<strong><?php echo $flight_pnr_confirm_seat_count; ?></strong></td>
						<td>Total Bus Confirm (PNR): &nbsp;<strong><?php echo $bus_pnr_confirm_count; ?></strong></td>
						<td>Total Bus Confirm (Seats): &nbsp;<strong><?php echo $bus_pnr_confirm_seat_count; ?></strong></td>
					</tr>

					<tr>
						<td>Total Flight Cancel (PNR): &nbsp;<strong><?php echo $flight_pnr_cancel_count; ?></strong></td>
						<td>Total Flight Cancel (Seats) : &nbsp;<strong><?php echo $flight_pnr_cancel_seat_count; ?></strong></td>
						<td>Total Bus Cancel (PNR): &nbsp;<strong><?php echo $bus_pnr_cancel_count; ?></strong></td>
						<td>Total Bus Cancel (Seats): &nbsp;<strong><?php echo $bus_pnr_cancel_seat_count; ?></strong></td>
					</tr>
					

				</table>
			</div>

			
		</div>
	</div>
</div>
<input type="hidden" value="<?php echo base_url(); ?>" id="base_url">


<style type="text/css">
	.padfive{
		padding: 0 2px!important;
	}
	.content-wrapper{
	    padding: 0!important;
	  }
	.btn-common{
	    width: 48%;
	    margin-left: 5px;
	  }
	.sup-main-div{
	    box-shadow: 0 5px 11px 0 rgba(0,0,0,0.18),0 4px 15px 0 rgba(0,0,0,0.15) !important;
	    margin-bottom: 20px;
	    padding: 20px;
	}
	.input-field{
		border: none;
		border-bottom: 2px solid #ccc;
		border-radius: 4px!important;
		/*width: 100%;*/
	}
	.radius-4{
		border-radius: 4px!important;
	}
	.mt-2{
		margin-top: 20px;
	}
	.mt-1{
		margin-top: 10px;
	}
	.modal-header{
		background-color: #3C8DBC;
		color: #fff;
	}
	.width-150{
		width: 150px;
	}
	h3{
		margin: 0 0 5px;
	}
	.re-details td{
		font-size: 18px;
	}
	.re-details strong{
		font-size: 20px;
		color: #367fa9;
	}
	.re-details td{
		border: 1px solid #f4f4f4;
		text-align: center;
	}
	.width-16{
		width: 160px;
	}
	.width-9{
		width: 90px;
	}
</style>
<script type="text/javascript">
	$(document).ready( function () {
	    $('.datatables-td').DataTable();
	    $("#excel_export").on("click", function(evt){
	    	let from_date = $('#created_datetime_from').val();
	    	let to_date = $('#created_datetime_to').val();
	    	$('#from_date').val(from_date);
			$('#to_date').val(to_date);
			if(from_date != '' && to_date != ''){
				$('#excel_export_form').submit();
			}else{
				alert("Select From Date and To Date...");
			}
	    });
	} );
</script>
<style type="text/css">
	.dataTables_wrapper .col-sm-12{
		min-height: .01%!important;
    	overflow-x: auto!important;
	}
</style>