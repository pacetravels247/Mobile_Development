<div id="Package" class="bodyContent col-md-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                <ul class="nav nav-tabs nav-justified" role="tablist" id="myTab">
                    <li role="presentation" class="active" id="add_package_li"><a
                        href="#add_package" aria-controls="home" role="tab"
                        data-toggle="tab">Edit Package City </a></li>          
                    </ul>
                </div>
            </div>
            <div class="panel-body">
                <form
                action="<?php echo base_url(); ?>index.php/tours/edit_tour_city/<?=$id?>"
                method="post" enctype="multipart/form-data" id="form form-horizontal validate-form"
                class='form form-horizontal validate-form'>
				<input type="hidden" class="is_unique" value="1">
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="add_package">
                        <div class="col-md-12">
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Choose Country</label>
								<div class='col-sm-8 controls'>        
									<select class='select2 form-control' data-rule-required='true' name='country_id' id="country_id" data-rule-required='true' required>
										<option value="">Choose Country</option>
										<?php
											foreach($tour_country as $k => $v)
											{
												if($v['id'] == $data['country_id']){
													$sel="selected";
												}else{
													$sel="";
												}
												echo '<option value="'.$v['id'].'" '.$sel.'>'.$v['name'].' </option>';
											}
										?>
									</select>				
								</div>
							</div>
                            <div class='form-group'>
                                <label class='control-label col-sm-3' for='validation_current'>Package City
                                </label>
                                <div class='col-sm-8 controls'>
                                    <input type="text" name="CityName" id="CityName"
                                    placeholder="Enter Country" data-rule-required='true'
                                    class='form-control add_pckg_elements' required value="<?=$data['CityName'];?>">
                                </div>
                            </div>
							<div class='form-group ini_banner_img'>
                                <label class='control-label col-sm-3' for='validation_current'>Banner Image
                                </label>
								<div class='col-sm-8 controls'>
									<?php
									if($data['banner_image']){
									echo '<img class="banner_imgs" src="'.$this->template->domain_images( $data['banner_image']) . '" style="width:200px;height:130px;">';
									}
									?>										
								</div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-sm-3' for='validation_current'>Change Banner Image
                                </label>
                                <div class='col-sm-8 controls'>
                                    <input type="file" name="banner_image" id="banner_image" class='form-control'>	
                                    <?=img_size_msg(200,300)?>								
                                </div>
                            </div>
                            <div class='' style='margin-bottom: 0'>
                                <div class='row'>
                                    <div class='col-sm-9 col-sm-offset-3'>  
                                        <button class='btn btn-primary form_subm' type='submit'>Save</button>
                                        <a href="<?php echo base_url(); ?>index.php/tours/tour_city" class='btn btn-primary' style="color:white;">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">  
 $(document).ready(function()
 { 
	 function readURL(input) { 
			if (input.files && input.files[0]) {
				var reader = new FileReader();
			
				reader.onload = function(e) {
					$('.ini_banner_img').removeClass('hide');
					$('.banner_imgs').attr('src', e.target.result);
				}
			
				reader.readAsDataURL(input.files[0]); // convert to base64 string
			}
		}

		$("#banner_image").change(function() {
		  readURL(this);
		});
	$(document).on('keyup','#CityName',function(){
		var name=$('#CityName').val();
		var country=$('#country_id').val();
		$.post('<?=base_url();?>tours/check_unique_data',{'name':name,'country':country,'table':'tours_city'},function(data)
		{
			//	alert(data);
				if(data>1){
					$('.is_unique').val('0');
					//alert("Data already exist");
					//return false;
				}else{
					$('.is_unique').val('1');
				}
		});
	});
	$("form").submit(function(e){
		var name=$('.is_unique').val();
		
		if(name==0){
	
			alert("Data already exist");
			e.preventDefault();
		}
			
		
	});
	$(document).on('click','.form_subm',function(e){
		if($('input').hasClass('invalid-ip')){
			alert("Please enter all the fields");
		}
		
	});
});