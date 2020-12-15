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
                            data-toggle="tab">Edit Hotel Details </a></li>			
                </ul>
            </div>
        </div>
        <!-- PANEL HEAD START -->
        <div class="panel-body">
            <!-- PANEL BODY START -->
            <form
                action="<?php echo base_url(); ?>index.php/tours/edit_hotel_save"
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
									  if($v['id']==$hotel_details['country']){
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
									  if($v['id']==$hotel_details['city']){
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
								<label class='control-label col-sm-3' for='validation_current'>Hotel Name
								</label>
								<div class='col-sm-8 controls'>
									<input type="text" name="hotel_name" id="hotel_name"
										placeholder="" data-rule-required='true'
										class='form-control' value="<?php echo string_replace_encode($hotel_details['hotel_name']);?>" required>									
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3'>Star Rating
								</label>
								<div class='col-sm-8 '>
									<select class='form-control'  name='star_rating' id="star_rating" data-rule-required='true' required>
										<option value="">Select Star Rating</option>
										<option value="1" <?php if($hotel_details['star_rating']==1) echo "selected";?>>1</option>
										<option value="2" <?php if($hotel_details['star_rating']==2) echo "selected";?>>2</option>
										<option value="3" <?php if($hotel_details['star_rating']==3) echo "selected";?>>3</option>
										<option value="4" <?php if($hotel_details['star_rating']==4) echo "selected";?>>4</option>
										<option value="5" <?php if($hotel_details['star_rating']==5) echo "selected";?>>5</option>
									</select>									
								</div>
							</div>			
                            <div class='' style='margin-bottom: 0'>
                                <div class='row'>
                                    <div class='col-sm-9 col-sm-offset-3'>	
                                       <input type="hidden" name="id" value="<?=$hotel_details['id']?>">				
                                        <button class='btn btn-primary form_subm' type='submit'>Update</button>
                                        <a href="<?php echo base_url(); ?>index.php/tours/hotel_list" class='btn btn-primary' style="color:white;">Cancel</a>

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
			});
			$(document).on('click','.form_subm',function(e){
				var country_name=$('#tour_country').val();
				var city_name=$('#tour_city').val();
				var hotel_name=$('#hotel_name').val();
				var star_rating=$('#star_rating').val();
				
				if(country_name=='' || city_name=='' || hotel_name=='' || star_rating==''){
					alert("Please enter all the fields");
				}
				
				//alert(country_name);
				
			});
		});
		
</script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script>
$( document ).ready(function (){
	
	$('.js-example-basic-single').select2();
				$('.js-example-basic-single2').select2();
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
