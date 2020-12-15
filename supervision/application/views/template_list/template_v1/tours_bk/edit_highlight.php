<?php error_reporting(0); //debug($tour_destination_details); //exit; ?>
<script src="<?php echo SYSTEM_RESOURCE_LIBRARY?>/ckeditor/ckeditor.js"></script>
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
                            data-toggle="tab">Edit Highlight </a></li>			
                </ul>
            </div>
        </div>
        <!-- PANEL HEAD START -->
        <div class="panel-body">
            <!-- PANEL BODY START -->
            <form
                action="<?php echo base_url(); ?>index.php/tours/edit_highlight_save"
                method="post" enctype="multipart/form-data" id="form form-horizontal validate-form"
                class='form form-horizontal validate-form'>
                <div class="tab-content">
                    <!-- Add Package Starts -->
                    <div role="tabpanel" class="tab-pane active" id="add_package">
                        <div class="col-md-12">

                            						
								
                            <div class='form-group'>
								<label class='control-label col-sm-3'>Choose Country
								</label>
								<div class='col-sm-8 '>        
								 <select class='form-control js-example-basic-single'  name='tour_country' id="tour_country" data-rule-required='true' required>
								  <option value="">Choose Country</option>
								  <?php
								  foreach($tour_country as $k => $v)
								  {
									  if($v['id']==$highlight_details['country']){
										  $selected="selected";
									  }else{
										  $selected="";
									  }
								   echo '<option value="'.$v['id'].'" '.$selected.'>'.$v['name'].' </option>';
								  }
								  ?>
								 </select>				
								</div>
							</div>
							
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Description
								</label>
								<div class='col-sm-8 controls'>
									<textarea name="description" required id="editor"><?=$highlight_details['desctiption']?></textarea>									
								</div>
							</div>		
                            <div class='' style='margin-bottom: 0'>
                                <div class='row'>
                                    <div class='col-sm-9 col-sm-offset-3'>	
                                       <input type="hidden" name="id" value="<?=$highlight_details['id']?>">				
                                        <button class='btn btn-primary form_subm' type='submit'>Update</button>
                                        <a href="<?php echo base_url(); ?>index.php/tours/highlight_list" class='btn btn-primary' style="color:white;">Cancel</a>

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

<!-- <?php
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
<script type="text/javascript" src="<?=$airliners_weburl?>extras/system/template_list/template_v1/javascript/js/tiny_mce/tiny_mce_call.js"></script>  -->
	  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

<script src="<?php echo SYSTEM_RESOURCE_LIBRARY?>/ckeditor/sample.js"></script>
<script> initSample(); </script>
<script>
$( document ).ready(function (){
	//CKEDITOR.replace( 'flight_details' );
	//CKEDITOR.replace( 'description' );
	$('.js-example-basic-single').select2();
	$(document).on('click','.form_subm',function(e){
		var country_name=$('#tour_country').val();
		
		if($('#tour_country').hasClass('invalid-ip')){
			$('.select2-selection').addClass('invalid-ip');
		}
		if(country_name==''){
			alert("Please enter all the fields");
			
		}
		
	});		
});
</script>
<!--
<script type="text/javascript" src="/chariot/extras/system/template_list/template_v1/javascript/js/nicEdit-latest.js"></script> 
<script type="text/javascript">
bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
</script>-->
