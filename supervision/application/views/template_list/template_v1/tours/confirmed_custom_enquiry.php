
<?php 
	$package_user=array();
	foreach($package_manager as $pa_val){
		$package_user[$pa_val['user_id']]=$pa_val['first_name'].' '.$pa_val['last_name'];
	}
//debug($package_user);


?>
<?php echo $this->session->flashdata('msg'); ?>
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
				<div id="tableList" class="clearfix table-responsive">
					
					<div class="pull-left"><span class="">Total <?=$total_rows?> Bookings</span></div>
					<table class="table table-condensed table-bordered rigid_actions" id="b2b_report_hotel_table">
						<thead>
							<tr>
								<th>SN</th>
								<th>Reference No</th>
								<th>Agency Name with ID</th>
								<th>Contact No.</th>
								<th>Travel</th>
								<th>Destination</th>
								<th>Enquiry Date</th>
								<th>Date of Travel</th>
								<th>No of night</th>
								<th>No of pax</th>
								<th>Requests</th>
								<th>Amount</th>
								<th>Status</th>
								<th>Action</th>
								<!-- <th>Phone</th>
								<th>Email</th> -->
							  
							 </tr>
						</thead>
						<?php
						
						//$current_record=1;
						//debug($tours_enquiry);
							if(!empty($tours_enquiry)){
							$segment_3 = $GLOBALS['CI']->uri->segment(3);
							$current_record = (empty($segment_3) ? 1 : $segment_3);
							foreach($tours_enquiry as $parent_k => $parent_v) {
								echo $GLOBALS['CI']->template->isolated_view('tours/enq_passanger_data_form',$parent_v);
								echo $GLOBALS['CI']->template->isolated_view('tours/enq_payment_details',$parent_v);
								echo $GLOBALS['CI']->template->isolated_view('tours/enq_upload_details',$parent_v);
								//debug($parent_v);
								extract($parent_v);
								//if($booking_package_details['package_type']=='fit'){
								//	$package_type="FIT / Customised";
								//}else{
								//	$package_type="Grouped / Fixed";
								//}
						?>
							<tbody>
							<tr class="enq_tr">
								<input type="hidden" value="<?=$enquiry_reference_no?>" class="is_tour_id">
								<td><?php echo ($current_record++)?></td>
								
								<td><?php echo $enquiry_reference_no;?></td>
								<td><?php echo $agent_name.' '.$agent_id ;?></td>
								<td><?php echo $agent_details['phone'];?></td>
								<td><?php echo ucfirst($travel_type);?></td>
								<td><?php echo $country_name;?></td>
								<td><?php echo $created_date;?></td>
						
								<td><?php echo date('d/M',strtotime($fr_date)).' - '. date('d/M',strtotime($to_date));?></td>
								<td><?php echo $night; ?></td>
								<td><?php echo $adult.'|'.$child.'|'.$infant;?></td>
								<td><a class="btn btn_sm" data-toggle="modal" data-target="#note_<?=$id?>" >Notes</a></td>
								<td><input type="text" class="quot_amount" data-ref="<?=$id?>" value="<?=$amount?>"></td>
								<td>
									<select class="enq_status">
										<option value="PENDING" <?php if($status=='PENDING'){ echo "selected";} ?> >PENDING</option>
										<option value="INPROGRESS" <?php if($status=='INPROGRESS'){ echo "selected";} ?>>INPROGRESS</option>
										<option value="QUOTED" <?php if($status=='QUOTED'){ echo "selected";} ?>>QUOTED</option>
										<option value="CONFIRMED" <?php if($status=='CONFIRMED'){ echo "selected";} ?>>CONFIRMED</option>
										<option value="CANCELLED" <?php if($status=='CANCELLED'){ echo "selected";} ?>>CANCELLED</option>
									</select>
								</td>
								<td>
									<a class="btn btn_sm" data-toggle="modal" data-target="#quot_<?=$id?>" >Upload Itinerary</a><br>
									<a class="btn btn_sm" data-toggle="modal" data-target="#passanger_data_form_<?=$enquiry_reference_no?>" target="_blank" >Passanger Details</a><br>
									<a class="btn btn_sm" data-toggle="modal" data-target="#payment_details_<?=$enquiry_reference_no?>" target="_blank" >Payment Details</a><br>
									<a class="btn btn_sm" data-toggle="modal" data-target="#upload_details_<?=$enquiry_reference_no?>" target="_blank" >Uploads</a><br>  
								</td>
								</tr>
								<?php	
								}
							}
							else {
								echo '<tr><td>No Data Found</td></tr>';
							}
						?>
								</tbody>
						
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
	$sn = 1;
	//debug($tours_enquiry);
	foreach ($tours_enquiry as $key => $data) { 
?>
	<div class="modal" id="note_<?=$data['id']?>" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Notes</h4>
				</div>
				<div class="modal-body bot">
		
					<div class="row">
<div class="qf_heading"> Agent Contact</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">Agent Id: </label>
	</div>
	<div class="col-sm-6">
	<span class="tab"><?=$data['agent_details']['user_id']?></span>
	</div>
</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">Agent Name: </label>
	</div>
	<div class="col-sm-6">
	<span class="tab"><?=$data['agent_details']['agency_name']?></span>
	</div>
</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">Agent Phone: </label>
	</div>
	<div class="col-sm-6">
	<span class="tab"><?=$data['agent_details']['phone']?></span>
	</div>
</div>
</div>
<div class="row">
<div class="qf_heading">Type of travel</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">Travel Type: </label>
	</div>
	<div class="col-sm-6">
	<span class="tab"><?=ucfirst($data['travel_type'])?></span>
	</div>
</div>
</div>
<div class="row">
<div class="qf_heading">WHERE YOU WANT TO TRAVEL</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">Destination: </label>
	</div>
	<div class="col-sm-6">
	<span class="tab"><?=$data['country_name']?></span>
	</div>
</div>
</div>
<div class="row">
<div class="qf_heading">DEPARTURE CITY</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">Departure: </label>
	</div>
	<div class="col-sm-6">
	<span class="tab"><?=$data['city']?></span>
	</div>
</div>
</div>
<div class="row">
<div class="qf_heading">More Details</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">From Date:  </label>
	</div>
	<div class="col-sm-6">
	<span class="tab"><?=$data['fr_date']?></span>
	</div>
</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">To Date:  </label>
	</div>
	<div class="col-sm-6">
	<span class="tab"><?=$data['to_date']?></span>
	</div>
</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">No. of Night: </label>
	</div>
	<div class="col-sm-6">
	<span class="tab"><?=$data['night']?></span>
	</div>
</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">Adult: </label>
	</div>
	<div class="col-sm-6">
	<span class="tab"><?=$data['adult']?></span>
	</div>
</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">Child: </label>
	</div>
	<div class="col-sm-6">
	<span class="tab"><?=$data['child']?></span>
	</div>
</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">Infant: </label>
	</div>
	<div class="col-sm-6">
	<span class="tab"><?=$data['infant']?></span>
	</div>
</div>
</div>

<div class="row">
<div class="qf_heading">ANY SPECIAL REQUESTS</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">Requests: </label>
	</div>
	<div class="col-sm-6">
	<span class="tab"><?=$data['remark']?></span>
	</div>
</div>
</div>					
					
					
					
					
					
					
					
				</div>

			</div>
		</div>
	</div>
	<div class="modal" id="quot_<?=$data['id']?>" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Upload Quotation</h4>
				</div>
				<div class="modal-body">
					<form action="<?php echo base_url();?>index.php/tours/upload_enq_quot" enctype="multipart/form-data" method="post" id="upload_<?=$data['id']?>"> 
						<input type="hidden" name="app_ref" value="<?=$data['id']?>">
						<div>
							<div class="col-sm-12" style="margin: 20px 0px;">
								<div class="col-sm-12 images_div"  id="upload_parameters">
									<div class="gallery_div col-sm-12" id="hotel_voucher_div_<?=$data['id']?>">
							<?php 
							//debug($data['quotation']);
								
								if(!empty($data['quotation'])){
									if($data['quotation']!=' '){ 
										$doc_type=explode('.',$data['quotation']);
										if($doc_type[1]=='pdf'){
							?>
											<embed src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA?>/extras/custom/TMX1512291534825461/images/tour_uploads/<?=$data['quotation']?>" width="100px" height="80px" /><a data-ref="<?=$data['id']?>" data-ite_img_nam="<?=$data['quotation']?>" data-img_type="quotation" class="delete_image"><i class="fas fa-trash"></i></a>
									<?php }else{ ?>
												<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA?>/extras/custom/TMX1512291534825461/images/tour_uploads/<?=$data['quotation']?>" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;"><a data-ref="<?=$data['id']?>" data-ite_img_nam="<?=$data['quotation']?>" data-img_type="hotel_voucher" class="delete_imag"><i class="fas fa-trash"></i></a>
									<?php 	}
										} 
									
								}else { ?>
									<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA.$this->template->domain_images('no_image.png');?>" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;">
								<?php } ?>
							
									</div>
								
									<input type="file" class="gallery form-control hotel_gallery" value="" name="enquiry_quotation">
									<input type="hidden" value="<?=$hotel_voucher?>" name="old_enquiry_quotation">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12">
								<button type="submit" class="btn btn-danger id-enquiry-btn">Send Quotation</button>
								<strong id="mail_voucher_error_message" class="text-danger"></strong> 
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
<?php
$sn++;
   }
?>
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
		$(document).on('click','.delete_image',function(){
		var img=$(this).data('ite_img_nam');
		var id=$(this).data('ref');
		var img_type=$(this).data('img_type');
		//alert(img);alert(id);alert(img_type); 
		$.ajax({
			url: '<?=base_url();?>tours/unlink_enq_quot/' + id + '/' + img + '/' + img_type,
			success: function (data, textStatus, jqXHR) {                            
				//alert(data);   
				//$("#img_" + id).remove();
				window.location.reload();
			}
		});
	});
	});
</script>
<style>

span.tab {
    display: block;
    border: 1px solid #ccc;
    padding: 2px 10px;
}
.qf_heading {
    text-align: center;
    text-transform: uppercase;
    font-size: 16px;
    padding: 5px 0px;
    background: #f1f1f1;
    width: 100%;
    display: block;
    margin: 15px auto;
}
.modal-body.bot .col-sm-12 {
    margin-bottom: 10px;
}
</style>