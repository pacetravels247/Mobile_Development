<link href="<?php echo $GLOBALS['CI']->template->template_css_dir('bootstrap-toastr/toastr.min.css');?>" rel="stylesheet" defer>
<script src="<?php echo $GLOBALS['CI']->template->template_js_dir('bootstrap-toastr/toastr.min.js'); ?>"></script>
<?php 
	$package_user=array();
	foreach($package_manager as $pa_val){
		$package_user[$pa_val['user_id']]=$pa_val['first_name'].' '.$pa_val['last_name'];
	}
//debug($package_user);


?>
<div class="bodyContent">
	<div class="table_outer_wrper"><!-- PANEL WRAP START -->
		<div class="panel_custom_heading"><!-- PANEL HEAD START -->
			
			<div class="panel-title">
				<ul class="nav nav-tabs nav-justified" role="tablist" id="myTab">
					<li role="presentation" class="active" id="add_package_li"><a
						href="#add_package" aria-controls="home" role="tab"
						data-toggle="tab">Package  Bookings</a></li>
     </ul>
    </div>
		</div><!-- PANEL HEAD START -->
		<div class="panel_bdy"><!-- PANEL BODY START -->
		
				<div class="clearfix"></div>
               

			<div class="tab-content">
				<div id="tableList" class="rigid_actions">
					<div class="pull-right">
					 <span class="">Total <?php echo $total_rows ?> Bookings</span>
					</div>
					<table class="table table-condensed table-bordered rigid_actions">
						<tr>
						  <th>SN</th>
						  <th>Action</th>
						  <!-- <th>Status</th>  
						  <th>Inquiry By</th>   -->
						  <th>Reference No</th>
						  <th>Agency</th>
						  <th>Contact No.</th>
						  <th>Package Type</th>
						  <th>Tour Code</th>
						  <th>Package Name</th>
						  <th>Inquiry Date</th>
						  <th>Departure Date</th>
						  <th>Status</th>
						 
						 <!-- <th>Phone</th>
						  <th>Email</th> -->
						  
						 </tr>
						<?php
						$current_record=1;
						//debug($table_data);
							if(!empty($table_data)){
							foreach($table_data['booking_details'] as $parent_k => $parent_v) {
								echo $GLOBALS['CI']->template->isolated_view('tours/passanger_data_form',$parent_v);
								echo $GLOBALS['CI']->template->isolated_view('tours/optional_tour_form',$parent_v);
								echo $GLOBALS['CI']->template->isolated_view('tours/room_details_pop_up',$parent_v);
								echo $GLOBALS['CI']->template->isolated_view('tours/payment_details',$parent_v);
								//debug($parent_v);
								extract($parent_v);
								if($booking_package_details['package_type']=='fit'){
									$package_type="FIT / Customised";
								}else{
									$package_type="Grouped / Fixed";
								}
						?>
							<tr class="enq_tr">
								<input type="hidden" value="<?=$enquiry_reference_no?>" class="is_tour_id">
								<td><?php echo ($current_record++)?></td>
								<td>
									<a href="<?=base_url()?>index.php/tours/b2b_voucher/<?=$booking_package_details['id']?>" target="_blank" >Selected Itinerary</a><br>
									<a data-toggle="modal" data-target="#passanger_data_form_<?=$enquiry_reference_no?>" target="_blank" >Passanger Details</a><br>
									<a data-toggle="modal" data-target="#optional_tour_<?=$enquiry_reference_no?>" target="_blank" >Selected Optional Tours</a><br>
									<a data-toggle="modal" data-target="#room_details_pop_up_<?=$enquiry_reference_no?>" target="_blank" >Selected Room Details</a><br>
									<a data-toggle="modal" data-target="#payment_details_<?=$enquiry_reference_no?>" target="_blank" >Payment Details</a><br>
								</td>
								<td><?php echo $enquiry_reference_no;?></td>
								<td><?php echo $booked_user_details['agency_name'];?></td>
								<td><?php echo $booked_user_details['phone'];?></td>
								<td><?php echo $package_type;?></td>
								<td><?php echo $booking_package_details['tour_code'];?></td>
								<td><?php echo $booking_package_details['package_name'];?></td>
								
								<td><?php echo $voucher_date;?></td>
								<td><?php echo $attributes['departure_date']; ?></td>
								<td><?php echo $status; ?></td>
								</tr>
								<?php	
								}
							}
							else {
								echo '<tr><td>No Data Found</td></tr>';
							}
						?>
						
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">

	$(document).ready(function()
	{
		$(document).on('change','.alloted_to',function(e){
			var sel_val = $(this).val();
			var sel_user ='<?php echo $package_user['+sel_val+']; ?>';
			var enquiry_id =$(this).parents('.enq_tr').find('.is_tour_id').val();
			$response = confirm("Are you sure to assign this booking to "+sel_user+" ?");
			if($response==true){ 
				window.location='<?=base_url()?>index.php/tours/assign_package_bookinga/'+sel_val+'/'+enquiry_id; 
			} else{
				
			}
		});
	});
</script>