<!-- Mail - Voucher  starts-->
<div class="modal fade modal-pass-details" id="passanger_data_form_<?=$app_reference?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
			<form action="<?php echo base_url();?>index.php/tours/add_tour_pax_details" enctype="multipart/form-data" method="post" id="holiday_search"> 
				<input type="hidden" name="app_reference" value="<?=$app_reference?>">
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
        	<div class="container-fluid id-passenger-details" id="passanger_parameters">
        		<div class="col-sm-12 nopad">
        			<div class="col-sm-3 padd">
        				<p class="lbl_2"><?=$pax_text?></p>
        			</div>
        			<div class="col-sm-3 padd">
        				<label for="first_name">First Name</label>
				<input type="text" class="form-control" value="<?=@$customer_details[$i-1]['first_name']?>" required="required" name="first_name[]" placeholder="Enter First Name">
			</div>
        			<div class="col-sm-3 padd">
        				<label for="middle_name">Middle Name</label>
				<input type="text" class="form-control" value="<?=@$customer_details[$i-1]['middle_name']?>" name="middle_name[]" placeholder="Enter Middle Name">
			</div>
        			<div class="col-sm-3 padd">
        				<label for="last_name">Last Name</label>
				<input type="text" class="form-control" value="<?=@$customer_details[$i-1]['last_name']?>" required="required" name="last_name[]" placeholder="Enter last Name">
				</div>
        		</div>
				
				<div class="col-sm-12 nopad mt-2">
					<div class="col-sm-3 padd">
						<p class="lbl_2">Uploads</p>
					</div>
					<div class="col-sm-3 padd">
						<div class="gallery_div col-sm-offset-3" id="pass_first_page_<?=$i.$app_reference?>">
							<?php 
								if($customer_details[$i-1]['passport_first_page']!=''){
									$img_name=explode('.',$customer_details[$i-1]['passport_first_page']);
									if(end($img_name)=='pdf'){
							?>			
										<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA?>/extras/custom/TMX1512291534825461/images/pdf_file_icon.png" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;">
							<?php 			
									}else if(end($img_name)=='docx' || end($img_name)=='doc'){
							?>		
										<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA?>/extras/custom/TMX1512291534825461/images/word_file_icon.png" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;">
							<?php 			
									}else{
							?>	
										<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA.$this->template->domain_images($customer_details[$i-1]['passport_first_page']);?>" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;">
							<?php 			
									}
							?>
							
							<?php }else{
								?>
							<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA.$this->template->domain_images('no_image.png');?>" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;">
							<?php
								
							} ?>
						</div>
						<label for="pass_first_page">Passport First Page</label>
						<input type="file" class="form-control pass_first_page_<?=$app_reference?>" value="" name="pass_first_page[]">
						<input type="hidden" class="form-control" value="<?=@$customer_details[$i-1]['passport_first_page']?>" name="old_pass_first_page[]">
					</div>
					<div class="col-sm-3 padd">
						<div class="gallery_div col-sm-offset-3" id="pass_second_page_<?=$i.$app_reference?>">
							<?php if($customer_details[$i-1]['passport_second_page']!=''){
									$img_name=explode('.',$customer_details[$i-1]['passport_second_page']);
									if(end($img_name)=='pdf'){
							?>			
										<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA?>/extras/custom/TMX1512291534825461/images/pdf_file_icon.png" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;">
							<?php 			
									}else if(end($img_name)=='docx' || end($img_name)=='doc'){
							?>		
										<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA?>/extras/custom/TMX1512291534825461/images/word_file_icon.png" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;">
							<?php 			
									}else{
								?>
								<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA.$this->template->domain_images($customer_details[$i-1]['passport_second_page']);?>" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;">
							<?php
								
							} }else{?>
								<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA.$this->template->domain_images('no_image.png');?>" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;">
							<?php 			
									}
								?>
						</div>
						<label for="pass_second_page">Passport Second Page</label>
						<input type="file" class="form-control pass_second_page_<?=$app_reference?>" value="" name="pass_second_page[]">
						<input type="hidden" class="form-control" value="<?=@$customer_details[$i-1]['passport_second_page']?>" name="old_pass_second_page[]">
					</div>
					<div class="col-sm-3 padd">
						<div class="gallery_div col-sm-offset-3" id="photo_as_pass<?=$i.$app_reference?>">
							<?php 
								if($customer_details[$i-1]['visa_photo']!=''){
									$img_name=explode('.',$customer_details[$i-1]['visa_photo']);
									if(end($img_name)=='pdf'){
							?>			
										<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA?>/extras/custom/TMX1512291534825461/images/pdf_file_icon.png" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;">
							<?php 			
									}else if(end($img_name)=='docx' || end($img_name)=='doc'){
							?>		
										<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA?>/extras/custom/TMX1512291534825461/images/word_file_icon.png" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;">
							<?php 			
									}else{
							?>			<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA.$this->template->domain_images($customer_details[$i-1]['visa_photo']);?>" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;">
										<?php
									} 
								}else{
							?>	
								<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA.$this->template->domain_images('no_image.png');?>" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;">
							<?php
								} 	
							?>
						</div>
						<label for="photo_as_pass">Photo as per visa specification</label>
						<input type="file" class="form-control photo_as_pass_<?=$app_reference?>" value="" name="photo_as_pass[]">
						<input type="hidden" class="form-control" value="<?=@$customer_details[$i-1]['visa_photo']?>" name="old_visa_photo[]">
					</div>
				</div>

				<div class="col-sm-12 nopad mt-2">
					<div class="col-sm-3 padd">
						<p for="comments" class="lbl_2">Comments</p>
					</div>
					<div class="col-sm-9 padd">
						<textarea class="form-control" placeholder="Comment" rows="3" name="comments[]"><?=@$customer_details[$i-1]['comments']?>
						</textarea>
					</div>
				</div>
				
        	       
					
			</div>
			<?php } ?>
			<div class="row">
				<div class="col-sm-12">
					<button type="submit" class="btn btn-danger id-enquiry-btn">Add Pax Details</button>
					<!-- <strong id="mail_voucher_error_message" class="text-danger"></strong> -->
				</div>
			</div>

			</form>
      </div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div>
<!-- Mail - Voucher  ends-->
<script>
$(function() {
    // Multiple images preview in browser
    var imagesPreview = function(input, placeToInsertImagePreview) {
		
        if (input.files) {
            var filesAmount = input.files.length;
		
            for (i = 0; i < filesAmount; i++) {
                var reader = new FileReader();

                var str=input.files[i]['name'];
				var str = str.split(".");
                reader.onload = function(event) {
					if (str[str.length - 1] == 'pdf' ) {
						$($.parseHTML('<img src="<?php echo SYSTEM_IMAGE_FULL_PATH_TO_EXTRA;?>/extras/custom/TMX1512291534825461/images/pdf_file_icon.png" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;">')).appendTo(placeToInsertImagePreview);
					}else if(str[str.length - 1]=='docx' || str[str.length - 1]=='doc') {
						$($.parseHTML('<img src="<?php echo SYSTEM_IMAGE_FULL_PATH_TO_EXTRA;?>/extras/custom/TMX1512291534825461/images/word_file_icon.png" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;">')).appendTo(placeToInsertImagePreview);	
					}else{
						$($.parseHTML('<img width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);						
					}
                }

                reader.readAsDataURL(input.files[i]);

            }
        }

    };

    $('.pass_first_page_<?php echo $app_reference; ?>').on('change', function() {
		var image_div=$(this).parents('.images_div').find('.gallery_div').attr('id');
     //  alert(image_div);
	  $('#'+image_div).html('');
		imagesPreview(this,'#'+image_div); 
    });
	$('.pass_second_page_<?php echo $app_reference; ?>').on('change', function() {
		var image_div=$(this).parents('.images_div').find('.gallery_div').attr('id');
      //  alert(image_div);
	  $('#'+image_div).html('');
		imagesPreview(this,'#'+image_div); 
    });
	$('.photo_as_pass_<?php echo $app_reference; ?>').on('change', function() {
		var image_div=$(this).parents('.images_div').find('.gallery_div').attr('id');
      //  alert(image_div);
	  $('#'+image_div).html('');
		imagesPreview(this,'#'+image_div); 
    });
	
	
});
</script>
<style type="text/css">
	div#passanger_parameters {
    border: 1px solid #ccc;
    height: 400px;
    margin: 10px;
    box-shadow:none;
    padding: 10px;
}
.modal-body {
    position: relative;
    padding: 20px;
}
</style>
<style type="text/css">
	/*div#passanger_parameters {
	    border: 1px solid #ccc;
	    min-height: 330px;
	    box-shadow: 0 0 5px #ccc;
	    border-radius: 15px;
    	padding: 30px;
	}*/
	.id-passenger-details{
		border: 1px solid #ccc;
		padding: 20px;
		border-radius: 8px;
		margin-bottom: 20px;
	}
	.modal-header{
		background-color: #006a94;
		color: #fff;
		font-weight: bold;
		text-align: center;
	}
	.id-passenger-details img{
		display: block;
	    width: 100%;
	    height: 100px;
	    border: 4px solid #fff;
	    /*border-radius: 15px;*/
	    border-top-left-radius: 10px!important;
    	border-top-right-radius: 10px!important;
	    box-shadow: 0 0 3px #ccc;
	}
	.id-passenger-details label {
    font-size: 13px;
    color: #666;
    font-weight: normal;
    white-space: nowrap;
}
	.id-passenger-details p{
		font-size: 15px;
		font-weight: 600;
	}
	.padd {
	    padding: 0 5px;
	}
	.mt-2{
		margin-top: 20px;
	}
	.modal-pass-details form{
		margin: 0;
	}
	.modal-pass-details .modal-body {
    	padding: 20px 20px 0!important;
    }
    .id-passenger-details input{
    	border-radius: 4px!important;
    }
    .id-passenger-details textarea{
    	border-radius: 4px!important;
    	max-width: 100%;
    }
    .id-download-btn{
    	border-bottom-left-radius: 10px!important;
    	border-bottom-right-radius: 10px!important;
    }
i.fa.fa-download {
    padding: 0;
    margin-top: 0;
}
.modal-footer {
    padding: 15px;
    border-top: none !important;
    text-align: center;
}
</style>