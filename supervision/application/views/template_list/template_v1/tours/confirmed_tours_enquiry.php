
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
							  <th>Sr. No</th>
							 
							  <!-- <th>Status</th>  
							  <th>Inquiry By</th>   -->
							  <th>Reference No.</th>
							  <th>Agency Name with ID</th>
							  <th>Contact No.</th>
							  <th>Package Type</th>
							  <th>Tour Code</th>
							  <th>Package Name</th>
							  <th>Enquiry Date</th>
							  <th>Departure Date</th>
							  <th>Status</th>
							  <th>Action</th>
							  <!--<th>Email</th> -->
							  
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
								if($package_details[0]['package_type']=='fit'){
									$package_type="FIT / Customised";
								}else if($package_details[0]['package_type']=='group'){
									$package_type="Grouped / Fixed";
								}else{
									$package_type="";
								}
								$agency_details = $this->custom_db->single_table_records("user",'*',array('user_id'=>$created_by_id))['data'][0];
						?>
							<tbody>
							<tr class="enq_tr">
								<input type="hidden" value="<?=$enquiry_reference_no?>" class="is_tour_id">
								<td><?php echo ($current_record++)?></td>
								
								<td><?php echo $enquiry_reference_no;?></td>
								<td><?php echo $created_by_name.' '.provab_decrypt($agency_details['uuid']) ;?></td>
								<td><?php echo $phone;?></td>
								<td><?php echo $package_type;?></td>
								<td><?php echo $tour_code;?></td>
								<td><?php echo $p_name;?></td>
								
								<td><?php echo date('d/m/Y',strtotime($date));?></td>
								<td><?php echo date('d/m/Y',strtotime($departure_date)); ?></td>
								<td><?php echo $status; ?></td>
								<td>
									<a class="btn btn_sm" data-toggle="modal" data-target="#quot_<?=$id?>" >Selected Itinerary</a><br>
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
											<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA?>/extras/custom/TMX1512291534825461/images/pdf_file_icon.png" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;"><a data-ref="<?=$data['id']?>" data-ite_img_nam="<?=$data['quotation']?>" data-img_type="hotel_voucher" class="delete_imag"><i class="fas fa-trash"></i></a>
									<?php }else if(end($doc_type)=='docx' || end($doc_type)=='doc'){
											?>
												<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA?>/extras/custom/TMX1512291534825461/images/word_file_icon.png" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;"><a data-ref="<?=$data['id']?>" data-ite_img_nam="<?=$data['quotation']?>" data-img_type="hotel_voucher" class="delete_imag"><i class="fas fa-trash"></i></a>
											<?php }else{ ?>
												<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA?>/extras/custom/TMX1512291534825461/images/tour_uploads/<?=$data['quotation']?>" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;"><a data-ref="<?=$data['id']?>" data-ite_img_nam="<?=$data['quotation']?>" data-img_type="hotel_voucher" class="delete_imag"><i class="fas fa-trash"></i></a>
									<?php 	}
										} 
									
								}else { ?>
									<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA.$this->template->domain_images('no_image.png');?>" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;">
								<?php } ?>
							
									</div>
								
									<input type="file" class="gallery form-control hotel_gallery" value="" name="enquiry_quotation">
									<input type="hidden" value="<?=$data['quotation']?>" name="old_enquiry_quotation">
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
