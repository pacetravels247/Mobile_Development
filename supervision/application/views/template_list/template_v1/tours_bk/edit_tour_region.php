<?php error_reporting(0); //debug($tour_region_details); //exit; ?>
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
                            data-toggle="tab">Edit Package Region </a></li>          
                </ul>
            </div>
        </div>
        <!-- PANEL HEAD START -->
        <div class="panel-body">
            <!-- PANEL BODY START -->
            <form
                action="<?php echo base_url(); ?>index.php/tours/edit_tour_region_save"
                method="post" enctype="multipart/form-data" id="form form-horizontal validate-form"
                class='form form-horizontal validate-form'>
                <div class="tab-content">
                    <!-- Add Package Starts -->
                    <div role="tabpanel" class="tab-pane active" id="add_package">
                        <div class="col-md-12">
                                                   <input type="hidden" class="is_unique" value="1">
                            <div class='form-group'>
                                <label class='control-label col-sm-3' for='validation_current'>Package Region
                                </label>
                                <div class='col-sm-8 controls'>
                                    <input type="text" name="tour_region" id="tour_region"
                                           placeholder="Enter Package Region" data-rule-required='true'
                                           class='form-control add_pckg_elements' required value="<?php echo string_replace_encode($tour_region_details['name']);?>">
                                </div>
                            </div>
                            
                            <div class='' style='margin-bottom: 0'>
                                <div class='row'>
                                    <div class='col-sm-9 col-sm-offset-3'>  
                                        <input type="hidden" name="id" value="<?= $tour_region_details['id'] ?>">                       
                                        <button class='btn btn-primary sub_form' type='submit'>Save</button>
                                        <a href="<?php echo base_url(); ?>index.php/tours/tour_region" class='btn btn-primary' style="color:white;">Cancel</a>

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
           
         
		   $(document).on('keyup','#tour_region',function(){
				var name=$('#tour_region').val();
				$.post('<?=base_url();?>tours/check_unique_data',{'name':name,'table':'tours_continent'},function(data)
				{
					//	alert(data);
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
				}
			});
		});
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
