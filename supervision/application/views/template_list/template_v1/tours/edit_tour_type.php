<?php error_reporting(0); //debug($tour_destination_details); //exit; ?>
<div id="Package" class="bodyContent col-md-12">
    <div class="panel panel-default">
        <!-- PANEL WRAP START -->
        <div class="panel-heading">
            <!-- PANEL HEAD START -->
            <div class="panel-title">
                <ul class="nav nav-tabs nav-justified" role="tablist" id="myTab">
                    <!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE START-->
                    <li role="presentation" class="active" id="add_package_li"><a
                            href="#add_package" aria-controls="home" role="tab"
                            data-toggle="tab">Edit Package Category </a></li>			
                </ul>
            </div>
        </div>
        <!-- PANEL HEAD START -->
        <div class="panel-body">
            <!-- PANEL BODY START -->
            <form
                action="<?php echo base_url(); ?>index.php/tours/edit_tour_type_save"
                method="post" enctype="multipart/form-data" id="form form-horizontal validate-form"
                class='form form-horizontal validate-form'>
                <div class="tab-content">
                    <!-- Add Package Starts -->
                    <div role="tabpanel" class="tab-pane active" id="add_package">
                        <div class="col-md-12">
<input type="hidden" class="is_unique" value="1">
                            						
                            <div class='form-group'>
                                <label class='control-label col-sm-3' for='validation_current'>Package Category
                                </label>
                                <div class='col-sm-8 controls'>
                                    <input type="text" name="tour_type_name" id="tour_type_name"
                                           placeholder="" data-rule-required='true'
                                           class='form-control add_pckg_elements' required value="<?php echo string_replace_encode($tour_type_details['tour_type_name']);?>">
                                </div>
                            </div>
                            <div class='form-group ini_banner_img'>
                                <label class='control-label col-sm-3' for='validation_current'>Banner Image
                                </label>
								<div class='col-sm-8 controls'>
									<?php
									if($tour_type_details['banner_image']){
									echo '<img class="banner_imgs" src="'.$this->template->domain_images( $tour_type_details['banner_image']) . '" style="width:200px;height:130px;">';
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
                                        <input type="hidden" name="id" value="<?= $tour_type_details['id'] ?>">							
                                        <button class='btn btn-primary sub_form' type='submit'>Update</button>
                                        <a href="<?php echo base_url(); ?>index.php/tours/tour_type" class='btn btn-primary' style="color:white;">Cancel</a>

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </form>
        </div>
        <!-- PANEL BODY END -->
    </div>
    <!-- PANEL WRAP END -->
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
          
		});
		$(document).on('keyup','#tour_type_name',function(){
			var name=$('#tour_type_name').val();
			$.post('<?=base_url();?>tours/check_unique_data',{'name':name,'table':'tour_type'},function(data)
			{
					//alert(data);
		        	if(data>0){
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
		$(document).on('click','.sub_form',function(e){
			
			if($('input').hasClass('invalid-ip')){
				alert("Please enter all the fields");
					$("#cke_description").css("border","1px solid red");
					
			}
				
		});
		
		/* function blurFunction() {
			  var name=$('#tour_type_name').val();
			  // alert(name);
			   $.post('<?=base_url();?>tours/check_unique_data',{'name':name,'table':'tour_type'},function(data)
		        {
					alert(data);
		        	if(data>0){
					
						alert("Data already exist");
						return false;
					}
		        });
			} */
        </script>
<?php
       $HTTP_HOST = '192.168.0.63';
       if(($_SERVER['HTTP_HOST']==$HTTP_HOST) || ($_SERVER['HTTP_HOST']=='localhost'))
	   {
				$airliners_weburl = '/airliners/';	 
	   }
	   else
	   {
				$airliners_weburl = '/~development/airliners_v1/';
       } 
	   
       /*<?=$airliners_weburl?>*/          
       ?> 
<script type="text/javascript" src="<?=$airliners_weburl?>extras/system/template_list/template_v1/javascript/js/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" src="<?=$airliners_weburl?>extras/system/template_list/template_v1/javascript/js/tiny_mce/tiny_mce_call.js"></script> 
<!--
<script type="text/javascript" src="/chariot/extras/system/template_list/template_v1/javascript/js/nicEdit-latest.js"></script> 
<script type="text/javascript">
bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
</script>-->
