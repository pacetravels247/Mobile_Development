<link href="<?php echo $GLOBALS['CI']->template->template_css_dir('bootstrap-toastr/toastr.min.css');?>" rel="stylesheet" defer>
<script src="<?php echo $GLOBALS['CI']->template->template_js_dir('bootstrap-toastr/toastr.min.js'); ?>"></script>

<div class="bodyContent">
	<div class="table_outer_wrper"><!-- PANEL WRAP START -->
		<div class="panel_custom_heading"><!-- PANEL HEAD START -->
			
			<div class="panel-title">
				<ul class="nav nav-tabs nav-justified" role="tablist" id="myTab">
					<li role="presentation" class="active" id="add_package_li"><a
						href="#add_package" aria-controls="home" role="tab"
						data-toggle="tab">Custom Enquiries</a></li>
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
							<th>Action</th>
							<th>Amount</th>
							<th>Status</th>
							
						</tr>
						<?php
						$current_record=1;
							if(!empty($table_data)){
								//debug($table_data);
								$sn=1;
								//array_reverse($table_data);
							foreach($table_data as $parent_k => $parent_v) {
								extract($parent_v);
						?>
							<tr class="enq_tr">
								<input type="hidden" value="<?=$id?>" class="is_tour_id">
								<td><?php echo ($current_record++)?></td>
								<td><?php echo $agent_name;?></td>
								<td><?php echo $parent_v['agent_details']['agency_name'].' '.$parent_v['agent_details']['user_id'];?></td>
								<td><?php echo $parent_v['agent_details']['phone'];?></td>
								<!--<td><?php echo $country_name;?></td>-->
								<td><?php echo ucfirst($travel_type);?></td>
								<td><?php echo $country_name;?></td>
								<td><?php echo date('d-m-Y',strtotime($created_date));?></td>
								
								<td><?php echo date('d/M',strtotime($fr_date)).' - '. date('d/M',strtotime($to_date));?></td>
								<td><?php echo $night; ?></td>
								<td><?php echo $adult.'|'.$child.'|'.$infant;?></td>
								<td><a class="btn btn_sm" data-toggle="modal" data-target="#note_<?=$sn?>" >Notes</a></td>   
								<td><a class="btn btn_sm" data-toggle="modal" data-target="#quot_<?=$parent_v["id"]?>" >Received Quote</a></td>
								<td><?php echo number_format($amount,2);?></td>
								<td><?php echo $status;?></td>
								
							</tr>
						<?php
						$sn++;
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
<?php
	$sn = 1;
	//debug($table_data);
	foreach ($table_data as $key => $data) { 
?>
	<div class="modal" id="note_<?=$sn?>" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Notes</h4>
				</div>
				<div class="modal-body">
					<div>Agent ID :  <?=$data['agent_details']['user_id']?></div>
					<div>Agent Name: <?=$data['agent_details']['agency_name']?></div>
					<div>Agent Phone: <?=$data['agent_details']['phone']?></div>
					<div>Travel Type: <?=ucfirst($data['travel_type'])?></div>
					<div>Destination: <?=$data['country_name']?></div>
					<div>Departure: <?=$data['city']?></div>
					<div>From Date: <?=$data['fr_date']?> To Date: <?=$data['to_date']?></div>
					<div>No. of Night: <?=$data['night']?></div>
					<div>Adult: <?=$data['adult']?> Child: <?=$data['child']?> Infant: <?=$data['infant']?></div>
					<div>Requests: <br/><?=$data['remark']?></div>
				</div>

			</div>
		</div>
	</div>
	<div class="modal" id="quot_<?=$data['id']?>" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title"> Quotation</h4>
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
											<embed src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA?>/extras/custom/TMX1512291534825461/images/tour_uploads/<?=$data['quotation']?>" width="100px" height="80px" /><a href="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA?>/extras/custom/TMX1512291534825461/images/tour_uploads/<?=$data['quotation']?>" download="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA?>/extras/custom/TMX1512291534825461/images/tour_uploads/<?=$data['quotation']?>" style="color: #5c5fff; font-weight: 600;"><i class="fa fa-download" aria-hidden="true"></i> </a>
									<?php }else{ ?>
												<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA?>/extras/custom/TMX1512291534825461/images/tour_uploads/<?=$data['quotation']?>" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;"><a href="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA?>/extras/custom/TMX1512291534825461/images/tour_uploads/<?=$data['quotation']?>" download="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA?>/extras/custom/TMX1512291534825461/images/tour_uploads/<?=$data['quotation']?>" style="color: #5c5fff; font-weight: 600;"><i class="fa fa-download" aria-hidden="true"></i> </a>
									<?php 	}
										} 
									
								}else { ?>
									<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA.$this->template->domain_images('no_image.png');?>" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;">
								<?php } ?>
							
									</div>
								
									
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12">
							
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
		var sel_user =$(this).text();
		var enquiry_id =$(this).parents('.enq_tr').find('.is_tour_id').val();
		$response = confirm("Are you sure to assign this enquiry to Package Executive ?");
		if($response==true){ 
			window.location='<?=base_url()?>index.php/tours/assign_custom_enquiry/'+sel_val+'/'+enquiry_id; 
		} else{
			
		}
	});
	
 });
</script>