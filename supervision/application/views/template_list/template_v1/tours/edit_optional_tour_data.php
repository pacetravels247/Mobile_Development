<style>
	.ad_pkg .col-sm-1 { padding-right: 0px !important;}
</style>
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
                            data-toggle="tab">Edit Optional tour Details </a></li>			
                </ul>
            </div>
        </div>
        <!-- PANEL HEAD START -->
        <div class="panel-body">
            <!-- PANEL BODY START -->
            <form
                action="<?php echo base_url(); ?>index.php/tours/optional_tour_note_save"
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
									  if($v['id']==$trip_note_details['country']){
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
								<label class='control-label col-sm-3'>Choose City
								</label>
								<div class='col-sm-8 '>
									<select class='form-control js-example-basic-single2'  name='tour_city' id="tour_city" data-rule-required='true' required>
										<?php
								  foreach($tour_city as $k => $v)
								  {
									  if($v['id']==$trip_note_details['city']){
										  $selected="selected";
									  }else{
										  $selected="";
									  }
								   echo '<option value="'.$v['id'].'" '.$selected.'>'.$v['CityName'].' </option>';
								  }
								  ?>
									</select>									
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Tour Name
								</label>
								<div class='col-sm-8 controls'>
									<input type="text" name="tour_name" id="tour_name"
										placeholder="" data-rule-required='true'
										class='form-control' value="<?php echo string_replace_encode($trip_note_details['tour_name']);?>" required>									
								</div>
							</div>
							<div class='form-group ad_pkg'>
								<label class='control-label col-sm-3' for='validation_current'>Adult Price
								</label>
								<div class='col-sm-2 controls'>
									<input type="text" name="adult_price" id="adult_price"
										placeholder="" data-rule-required='true' value="<?=$trip_note_details['adult_price']?>"
										class='form-control' required>									
								</div>

								<label class='control-label col-sm-1' for='validation_current'>Child Price
								</label>
								<div class='col-sm-2 controls'>
									<input type="text" name="child_price" id="child_price" value="<?=$trip_note_details['child_price']?>"
										placeholder="" data-rule-required='true'
										class='form-control' required>									
								</div>
								<label class='control-label col-sm-1' for='validation_current'>Infant Price
								</label>
								<div class='col-sm-2 controls'>
									<input type="text" name="infant_price" id="infant_price" value="<?=$trip_note_details['infant_price']?>"
										placeholder="" data-rule-required='true'
										class='form-control' required>									
								</div>
							</div>
							<!-- <div class='form-group'>
								
							</div> -->
							<!-- <div class='form-group'>
								
							</div>	 -->			
									
                            <div class='' style='margin-bottom: 0'>
                                <div class='row'>
                                    <div class='col-sm-9 col-sm-offset-3'>	
                                       <input type="hidden" name="id" value="<?=$trip_note_details['id']?>">				
                                        <button class='btn btn-primary form_subm' type='submit'>Update</button>
                                        <a href="<?php echo base_url(); ?>index.php/tours/optional_tour_list" class='btn btn-primary' style="color:white;">Cancel</a>
 
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

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script>
$( document ).ready(function (){
	
	$('.js-example-basic-single').select2();
	$('.js-example-basic-single2').select2();	
});
</script>


<script type="text/javascript">  
		$(document).ready(function()
		{
           
        
			$("#tour_country").change(function() {
				var selections =$(this).val();
			
				$.ajax({
					url: '<?php echo base_url(); ?>index.php/tours/get_city_name/',
					type:'POST',
					data:{'tour_country':selections},
					success: function (data, textStatus, jqXHR) {                                   
					  
					 $('#tour_city').html('');
					  $('#tour_city').html(data);
					
					}
				});
				   if(selections!=''){
						$('.select2-selection').removeClass('invalid-ip');
					}
				
			});
			$(document).on('click','.form_subm',function(e){
				var country_name=$('#tour_country').val();
				var city_name=$('#tour_city').val();
				
				
				if(country_name=='' || city_name=='' || $('input').hasClass('invalid-ip')){
					if(country_name==''){
						$('.select2-selection').addClass('invalid-ip');
					}
					alert("Please enter all the fields");
				}
				
				//alert(country_name);
				
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
