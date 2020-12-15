<div id="Package" class="bodyContent col-md-12">
	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="panel-title">
				<ul class="nav nav-tabs nav-justified" role="tablist" id="myTab">
					<li role="presentation" class="active" id="add_package_li"><a
						href="#add_package" aria-controls="home" role="tab"
						data-toggle="tab">Tours Inquiry </a></li>
     </ul>
    </div>
   </div>
   <?php /* ?><div class="panel-body">
    <button class="btn btn-primary" type="button" data-toggle="collapse"
    data-target="#advanced_search" aria-expanded="false"
    aria-controls="advanced_search">Advanced Search</button>
    <hr>
    <div class="collapse in" id="advanced_search">
     <form method="GET" autocomplete="off"
     action="<?=base_url().'index.php/tours/tours_enquiry';?>">
     <div class="clearfix form-group">
      <div class="col-xs-4">
       <label> Package Name </label> <input type="text"
       class="form-control" name="package_name"
       value="<?=@$package_name?>" placeholder="Package Name">
      </div>
      <div class="col-xs-4">
       <label> Phone </label> <input type="text" class="form-control"
       name="phone" value="<?=@$phone?>" placeholder="Phone">
      </div> 
      <div class="col-xs-4">
       <label> Email </label> <input type="text" class="form-control"
       name="email" value="<?=@$email?>" placeholder="Email">
      </div>
     </div>
     <div class="col-sm-12 well well-sm">
      <button type="submit" class="btn btn-primary">Search</button>
      <button type="reset" class="btn btn-warning">Reset</button>
     </div>
    </form>
   </div>
  </div><?php */ ?>
  <div class="panel-body">
  </div>
<div class="table-responsive scroll_main">
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>Sr.No</th>
				<th>Reference No.</th>
				<th>Agency Name with ID</th>
				<th>Contact No.</th>
				<th>Package Type</th>
				<th>Tour Code</th>
				<th>Package Name</th>
				<th>Inquiry Date</th>
				<th>Departure Date</th>
				<th>Comments</th>
				<th>Action</th>
				<th>Amount</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
		<?php
			//debug($tours_enquiry);
			foreach($tours_enquiry AS $key=>$te){
				if($te['te_status']==1)
					$status = '<span style="color:green;">Replied</span>';
				else
					$status = '<span style="color:red;">Pending</span>';
		?>
			<tr>
				<td><?=++$key?></td>
				<td><?=$te["enquiry_reference_no"]?></td>
				<td><?=$te["u_fname"]?> <?=$te["created_by_id"]?></td>
				<td><?=$te["phone"]?></td>
				<td><?=$te["email"]?></td>
				<td><?=$te["tour_code"]?></a></td>
				<td><?=$te["p_name"]?></a></td>
				<td><?=date('d-m-Y' ,strtotime($te["date"]))?></td>
				<td><?=date('d-m-Y' ,strtotime($te["departure_date"]))?></td>
				<td><a class="btn btn_sm" data-toggle="modal" data-target="#note_<?=$te["id"]?>" >Notes</a></td>
				<td><a class="btn btn_sm" data-toggle="modal" data-target="#quot_<?=$te["id"]?>" >Received Quote</a></td>
				<td><?=$te["amount"]?></td> 
				<td><?=$te["status"]?></td>
			</tr>
	  <?php } ?>
		</tbody>
	</table>
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
				<div class="modal-body">
					<?php echo $data['message']; ?>
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
											<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA?>/extras/custom/TMX1512291534825461/images/pdf_file_icon.png" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;"><a href="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA?>/extras/custom/TMX1512291534825461/images/tour_uploads/<?=$data['quotation']?>" download="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA?>/extras/custom/TMX1512291534825461/images/tour_uploads/<?=$data['quotation']?>" style="color: #5c5fff; font-weight: 600;"><i class="fa fa-download" aria-hidden="true"></i> </a>
									<?php }else if(end($doc_type)=='docx' || end($doc_type)=='doc'){
											?>
											<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA?>/extras/custom/TMX1512291534825461/images/word_file_icon.png" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;"><a href="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA?>/extras/custom/TMX1512291534825461/images/tour_uploads/<?=$data['quotation']?>" download="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA?>/extras/custom/TMX1512291534825461/images/tour_uploads/<?=$data['quotation']?>" style="color: #5c5fff; font-weight: 600;"><i class="fa fa-download" aria-hidden="true"></i> </a>
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