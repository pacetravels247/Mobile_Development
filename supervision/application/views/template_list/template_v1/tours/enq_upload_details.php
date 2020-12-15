<!-- Mail - Voucher  starts-->
<div class="modal fade" id="upload_details_<?=$enquiry_reference_no?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><i class="fa fa-envelope-o"></i>
			Uploads
		</h4>
      </div>
	  <?php //debug($customer_details);  ?>
      <div class="modal-body">
			<form action="<?php echo base_url();?>index.php/tours/upload_tour_details" enctype="multipart/form-data" method="post" id="upload_<?=$enquiry_reference_no?>"> 
				<input type="hidden" name="app_reference" value="<?=$enquiry_reference_no?>">
				<input type="hidden" name="update_table" value="<?=$update_table?>">
				<input type="hidden" name="module" value="<?=$created_by?>">
			<?php 
			$total_pax = $attributes['adult_count']+$attributes['child_count']+$attributes['infant_count'];
			$adult_child_count = $attributes['adult_count']+$attributes['child_count'];
			
				
			?>
			<input type="hidden" name="app_ref" value="<?=$enquiry_reference_no?>">
			
        	<div>
        		
				
				<div class="col-sm-12" style="margin: 20px 0px;">
					
					<div class="col-sm-12 images_div"  id="upload_parameters">
						<div class="gallery_div col-sm-12" id="hotel_voucher_div_<?=$enquiry_reference_no?>">
							<?php 
							//debug($tours_itinerary_dw[$Dayno-1]['banner_image']);
								$image=explode(',',$hotel_voucher);
								if(!empty($hotel_voucher) || $hotel_voucher!=''){
									foreach($image as $im){
										$im=trim($im);
										if(!empty($im)){ 
											$doc_type=explode('.',$im);
											if(end($doc_type)=='pdf'){
											?>
												<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA?>/extras/custom/TMX1512291534825461/images/pdf_file_icon.png" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;"><a data-ref="<?=$enquiry_reference_no?>" data-ite_img_nam="<?=$im?>" data-img_type="hotel_voucher" class="delete_image_<?=$enquiry_reference_no?>"><i class="fas fa-trash"></i></a>
											<?php }else if(end($doc_type)=='docx' || end($doc_type)=='doc'){
											?>
												<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA?>/extras/custom/TMX1512291534825461/images/word_file_icon.png" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;"><a data-ref="<?=$enquiry_reference_no?>" data-ite_img_nam="<?=$im?>" data-img_type="hotel_voucher" class="delete_image_<?=$enquiry_reference_no?>"><i class="fas fa-trash"></i></a>
											<?php }else{ ?>
												<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA?>/extras/custom/TMX1512291534825461/images/tour_uploads/<?=$im?>" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;"><a data-ref="<?=$enquiry_reference_no?>" data-ite_img_nam="<?=$im?>" data-img_type="hotel_voucher" class="delete_image_<?=$enquiry_reference_no?>"><i class="fas fa-trash"></i></a>
									<?php 	}
										} 
									}
								}else { ?>
									<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA.$this->template->domain_images('no_image.png');?>" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;">
								<?php } ?>
							
						</div>
						
    
						<label for="hotel_voucher">Hotel Voucher</label>
						<input type="file" class="gallery form-control hotel_gallery" value="" accept="image/*,.doc,.docx,.pdf" name="hotel_voucher[]" multiple>
						<input type="hidden" value="<?=$hotel_voucher?>" name="old_hotel_voucher">
						
					</div>
					<div class="col-sm-12 images_div"  id="upload_parameters">
						<div class="gallery_div col-sm-offset-3" id="flight_ticket_div_<?=$enquiry_reference_no?>">
							<?php 
							//debug($tours_itinerary_dw[$Dayno-1]['banner_image']);
								$image=explode(',',$flight_ticket);
								if(!empty($flight_ticket) || $flight_ticket!=''){
									foreach($image as $im){
										$im=trim($im);
											if(!empty($im)){  
											$doc_type=explode('.',$im);
											if(end($doc_type)=='pdf'){
											?>
												<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA?>/extras/custom/TMX1512291534825461/images/pdf_file_icon.png" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;"><a data-ref="<?=$enquiry_reference_no?>" data-ite_img_nam="<?=$im?>" data-img_type="flight_ticket" class="delete_image_<?=$enquiry_reference_no?>"><i class="fas fa-trash"></i></a>
											<?php }else if(end($doc_type)=='docx' || end($doc_type)=='doc'){
											?>
												<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA?>/extras/custom/TMX1512291534825461/images/word_file_icon.png" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;"><a data-ref="<?=$enquiry_reference_no?>" data-ite_img_nam="<?=$im?>" data-img_type="flight_ticket" class="delete_image_<?=$enquiry_reference_no?>"><i class="fas fa-trash"></i></a>
											<?php }else{ ?>
											<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA?>/extras/custom/TMX1512291534825461/images/tour_uploads/<?=$im?>" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;"><a data-ref="<?=$enquiry_reference_no?>" data-ite_img_nam="<?=$im?>" data-img_type="flight_ticket" class="delete_image_<?=$enquiry_reference_no?>"><i class="fas fa-trash"></i></a>
									<?php 	}
										}
									}
								} else { ?>
									<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA.$this->template->domain_images('no_image.png');?>" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;">
								<?php } ?>
						</div>
						<label for="flight_ticket">Flight Ticket</label>
						<input type="file" class="gallery form-control flight_ticket_gallery" value="" accept="image/*,.doc,.docx,.pdf" name="flight_ticket[]" multiple>
						<input type="hidden" value="<?=$flight_ticket?>" name="old_flight_ticket">
						
					</div>
					<div class="col-sm-12 images_div"  id="upload_parameters">
						<div class="gallery_div col-sm-offset-3" id="visa_<?=$enquiry_reference_no?>">
							<?php 
							//debug($tours_itinerary_dw[$Dayno-1]['banner_image']);
								$image=explode(',',$visa);
								if(!empty($visa) || $visa!=''){
									foreach($image as $im){
										$im=trim($im);
											if(!empty($im)){ 
											$doc_type=explode('.',$im);
											if(end($doc_type)=='pdf'){
											?>
												<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA?>/extras/custom/TMX1512291534825461/images/pdf_file_icon.png" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;"><a data-ref="<?=$enquiry_reference_no?>" data-ite_img_nam="<?=$im?>" data-img_type="visa" class="delete_image_<?=$enquiry_reference_no?>"><i class="fas fa-trash"></i></a>
											<?php }else if(end($doc_type)=='docx' || end($doc_type)=='doc'){
											?>
												<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA?>/extras/custom/TMX1512291534825461/images/word_file_icon.png" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;"><a data-ref="<?=$enquiry_reference_no?>" data-ite_img_nam="<?=$im?>" data-img_type="visa" class="delete_image_<?=$enquiry_reference_no?>"><i class="fas fa-trash"></i></a>
											<?php }else{ ?>
											<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA?>/extras/custom/TMX1512291534825461/images/tour_uploads/<?=$im?>" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;"><a data-ref="<?=$enquiry_reference_no?>" data-ite_img_nam="<?=$im?>" data-img_type="visa" class="delete_image_<?=$enquiry_reference_no?>"><i class="fas fa-trash"></i></a>
									<?php 	}
										}
									}
								} else { ?>
									<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA.$this->template->domain_images('no_image.png');?>" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;">
								<?php } ?>
							
						</div>
						<label for="visa">visa</label>
						<input type="file" class="gallery form-control visa_gallery" accept="image/*,.doc,.docx,.pdf" value="" name="visa[]" multiple>
						<input type="hidden" class="form-control" value="<?=@$visa?>" name="old_visa">
					</div>
					<div class="col-sm-12 images_div"  id="upload_parameters">
						<div class="gallery_div col-sm-offset-3" id="final_itinary_<?=$enquiry_reference_no?>">
							<?php 
							//debug($tours_itinerary_dw[$Dayno-1]['banner_image']);
								$image=explode(',',$final_itinery);
								if(!empty($final_itinery) || $final_itinery!=''){
									foreach($image as $im){
										$im=trim($im);
										if(!empty($im)){ 
											$doc_type=explode('.',$im);
											if(end($doc_type)=='pdf'){
											?>
												<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA?>/extras/custom/TMX1512291534825461/images/pdf_file_icon.png" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;"><a data-ref="<?=$enquiry_reference_no?>" data-ite_img_nam="<?=$im?>" data-img_type="final_itinery" class="delete_image_<?=$enquiry_reference_no?>"><i class="fas fa-trash"></i></a>
											<?php }else if(end($doc_type)=='docx' || end($doc_type)=='doc'){
											?>
												<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA?>/extras/custom/TMX1512291534825461/images/word_file_icon.png" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;"><a data-ref="<?=$enquiry_reference_no?>" data-ite_img_nam="<?=$im?>" data-img_type="final_itinery" class="delete_image_<?=$enquiry_reference_no?>"><i class="fas fa-trash"></i></a>
											<?php }else{ ?>
												<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA?>/extras/custom/TMX1512291534825461/images/tour_uploads/<?=$im?>" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;"><a data-ref="<?=$enquiry_reference_no?>" data-ite_img_nam="<?=$im?>" data-img_type="final_itinery" class="delete_image_<?=$enquiry_reference_no?>"><i class="fas fa-trash"></i></a>
									<?php 	}
										} 
									}
								} else { ?>
									<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA.$this->template->domain_images('no_image.png');?>" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;">
								<?php } ?>
						</div>
						<label for="final_itinary">Final Itinerary</label>
						<input type="file" class="gallery form-control final_itinary_gallery" value="" accept="image/*,.doc,.docx,.pdf" name="final_itinary[]" multiple>
						<input type="hidden" value="<?=$final_itinery?>" name="old_final_itinery">
						
					</div>
				</div>

				
				
				
        	       
					
			</div>
		
			<div class="row">
				<div class="col-sm-12">
				 	<button type="submit" class="btn btn-danger id-enquiry-btn">Upload Files</button>
					<strong id="mail_voucher_error_message" class="text-danger"></strong> 
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

    $('.hotel_gallery').on('change', function() {
		var image_div=$(this).parents('.images_div').find('.gallery_div').attr('id');
		//  alert(image_div);
		$('#hotel_voucher_div_<?php echo $enquiry_reference_no;?>').html('');
		//console.log(hotel_voucher_div_<?php echo $enquiry_reference_no;?>);
		imagesPreview(this,'#hotel_voucher_div_<?php echo $enquiry_reference_no;?>'); 
    });
	$('.flight_ticket_gallery').on('change', function() {
		var image_div=$(this).parents('.images_div').find('.gallery_div').attr('id');
		//  alert(image_div);
		$('#flight_ticket_div_<?php echo $enquiry_reference_no;?>').html('');
		//console.log(hotel_voucher_div_<?php echo $enquiry_reference_no;?>);
		imagesPreview(this,'#flight_ticket_div_<?php echo $enquiry_reference_no;?>'); 
    });
	$('.visa_gallery').on('change', function() {
		var image_div=$(this).parents('.images_div').find('.gallery_div').attr('id');
		//  alert(image_div);
		$('#visa_<?php echo $enquiry_reference_no;?>').html('');
		//console.log(hotel_voucher_div_<?php echo $enquiry_reference_no;?>);
		imagesPreview(this,'#visa_<?php echo $enquiry_reference_no;?>'); 
    });
	$('.final_itinary_gallery').on('change', function() {
		var image_div=$(this).parents('.images_div').find('.gallery_div').attr('id');
		//  alert(image_div);
		$('#final_itinary_<?php echo $enquiry_reference_no;?>').html('');
		//console.log(hotel_voucher_div_<?php echo $enquiry_reference_no;?>);
		imagesPreview(this,'#final_itinary_<?php echo $enquiry_reference_no;?>'); 
    });
	$(document).on('click','.delete_image_<?php echo $enquiry_reference_no;?>',function(){
		var img=$(this).data('ite_img_nam');
		var id=$(this).data('ref');
		var img_type=$(this).data('img_type');
		var upload_for='<?php echo $update_table; ?>';
		//alert(img);alert(id);alert(img_type); 
		$.ajax({
			url: '<?=base_url();?>tours/unlink_tour_uploads/' + id + '/' + img + '/' + img_type + '/' + upload_for,
			success: function (data, textStatus, jqXHR) {                            
				//alert(data);   
				//$("#img_" + id).remove();
				window.location.reload();
			}
		 });
	});
});
</script>

<style type="text/css">
	div#upload_parameters {
    border: 1px solid #ccc;
    height: auto;
    margin: 10px;
    box-shadow: 0 0 5px #00000070;
    padding: 10px;
    overflow-y: scroll;
}
.gallery_div {
    display: flex;
    margin: 10px;
}
.gallery_div img
{
	margin-right: 10px;
}
i.fas.fa-trash
{
	padding: 10px;
    margin-top: 20px;
}
</style>