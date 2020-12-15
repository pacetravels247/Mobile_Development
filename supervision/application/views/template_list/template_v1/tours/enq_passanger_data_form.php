<!-- Mail - Voucher  starts-->
<div class="modal fade" id="passanger_data_form_<?=$enquiry_reference_no?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><i class="fa fa-envelope-o"></i>
			Customer Details
		</h4>
      </div>
	  <?php //debug($customer_details);  ?>
      <div class="modal-body">
			<form action="<?php echo base_url();?>index.php/tours/add_tour_pax_details" enctype="multipart/form-data" method="post" id="passanger_data_<?=$enquiry_reference_no?>"> 
				<input type="hidden" name="app_reference" value="<?=$enquiry_reference_no?>">
			<?php 
			$total_pax = $attributes['adult_count']+$attributes['child_count']+$attributes['infant_count'];
			$adult_child_count = $attributes['adult_count']+$attributes['child_count'];
			for($i=1;$i<=$total_pax;$i++){ 
				if($i<=$attributes['adult_count']){
					$pax_text = "Adult Passanger ".$i;
					$pax_type = "Adult";
				}else if($i<=$adult_child_count){
					$pax_text = "Child Passanger ".$i;
					$pax_type = "Child";
				}else{
					$pax_text = "Infant Passanger ".$i;
					$pax_type = "Infant";
				}
				
			?>
			<input type="hidden" name="pax_type[]" value="<?=$pax_type?>">
			<input type="hidden" name="update_id[]" value="<?=@$customer_details[$i-1]['origin']?>">
        	<div id="passanger_parameters">
        		<div class="col-sm-12">
        			<div class="col-sm-3"><label class="lbl_2"><?=$pax_text?></label></div>
        			<div class="col-sm-3"><label for="first_name">First Name</label>
				<input type="text" class="form-control" value="<?=@$customer_details[$i-1]['first_name']?>" required="required" name="first_name[]" placeholder="Enter First Name"></div>
        			<div class="col-sm-3"><label for="middle_name">Middle Name</label>
				<input type="text" class="form-control" value="<?=@$customer_details[$i-1]['middle_name']?>" required="required" name="middle_name[]" placeholder="Enter Middle Name"></div>
        			<div class="col-sm-3"><label for="last_name">Last Name</label>
				<input type="text" class="form-control" value="<?=@$customer_details[$i-1]['last_name']?>" required="required" name="last_name[]" placeholder="Enter last Name">
				</div>
        		</div>
				
				<div class="col-sm-12" style="margin: 20px 0px;">
					<div class="col-sm-3">
						<label class="lbl_2">Uploads</label>
					</div>
					<div class="col-sm-3">
						<?php if($customer_details[$i-1]['passport_first_page']!=''){
							?>
						<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA.$this->template->domain_images($customer_details[$i-1]['passport_first_page']);?>" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;">
						<?php }else{
							?>
						<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA.$this->template->domain_images('no_image.png');?>" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;">
						<?php
							
						} ?>
    
						<label for="pass_first_page">Passport First Page</label>
						
					</div>
					<div class="col-sm-3">
						
						<?php if($customer_details[$i-1]['passport_second_page']!=''){
						?>
						<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA.$this->template->domain_images($customer_details[$i-1]['passport_second_page']);?>" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;">
						<?php } else{
							?>
						<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA.$this->template->domain_images('no_image.png');?>" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;">
						<?php
							
						} ?>
						<label for="pass_second_page">Passport Second Page</label>
						
					</div>
					<div class="col-sm-3">
						<?php if($customer_details[$i-1]['visa_photo']!=''){
						?>
						<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA.$this->template->domain_images($customer_details[$i-1]['visa_photo']);?>" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;">
						<?php } else{
							?>
						<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA.$this->template->domain_images('no_image.png');?>" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;">
						<?php
							
						} ?>
						<label for="photo_as_pass">Photo as per visa specification</label>
						
					</div>
				</div>

				
				<div class="col-sm-12">
					<div class="col-sm-3">
				<label for="comments" class="lbl_2">Comments</label></div>
				<div class="col-sm-9">
				<textarea class="form-control" rows="3" name="comments[]"><?=@$customer_details[$i-1]['comments']?></textarea></div></div>
				
        	       
					
			</div>
			<?php } ?>
			<div class="row">
				<div class="col-sm-12">
				<!-- 	<button type="submit" class="btn btn-danger id-enquiry-btn">Add Pax Details</button>
					<strong id="mail_voucher_error_message" class="text-danger"></strong> -->
				</div>
			</div>
			</form>
      </div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div>
<!-- Mail - Voucher  ends-->


<style type="text/css">
	div#passanger_parameters {
    border: 1px solid #ccc;
    height: 350px;
    margin: 10px;
    box-shadow: 0 0 5px #00000070;
    padding: 10px;
}
</style>