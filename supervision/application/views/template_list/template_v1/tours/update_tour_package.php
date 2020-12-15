<style type="text/css">
	img.gal_img {
    width: 100px !important;
    height: 80px;
    margin-bottom: 10px;
}
</style>
<?php

//debug($tour_data); exit();

 error_reporting(0);?>
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
						data-toggle="tab">Update Holiday Package Manager </a></li>					
				</ul>
			</div>
		</div>
		<!-- PANEL HEAD START -->
		<div class="panel-body">
			<!-- PANEL BODY START -->
			<form
				action="<?php echo base_url(); ?>index.php/tours/edit_tour_package_save"
				method="post" enctype="multipart/form-data" id="form form-horizontal validate-form"
				class='form form-horizontal validate-form' onsubmit="return validation();">
				<div class="tab-content">
					<!-- Add Package Starts -->
					<div role="tabpanel" class="tab-pane active" id="add_package">
						   <div class="col-md-12">
                           <form
				action="<?php echo base_url(); ?>index.php/tours/add_tour_save"
				method="post" enctype="multipart/form-data" id="form form-horizontal validate-form"
				class='form form-horizontal validate-form'>
						    <div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Package ID
								</label>
								<div class='col-sm-4 controls'>
									<input type="text" value="<?=$tour_data['package_id']?>" class='form-control add_pckg_elements' disabled>									
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Package Name
								</label>
								<div class='col-sm-8 controls'>
									<input type="text" name="package_name" id="package_name" value="<?=string_replace_encode($tour_data['package_name'])?>"
										placeholder="Enter Package Name" data-rule-required='true'
										class='form-control add_pckg_elements' required>									
								</div>
							</div>

							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Supplier Name
								</label>
								<div class='col-sm-8 controls'>
									<input type="text" name="supplier_name" id="supplier_name" value="<?=string_replace_encode($tour_data['supplier_name'])?>"
										placeholder="Enter Supplier Name" data-rule-required='true'
										class='form-control add_pckg_elements' required>									
								</div>
							</div>

						<!-- 	<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Choose Theme
								</label>
								<div class='col-sm-4 controls'>
								<select class='select2 form-control' data-rule-required='true' name='tour_type' id="tour_type" data-rule-required='true' required>
                                <option value="">Choose Theme</option>
                                <?php
                                foreach($tour_type as $k => $v)
                                {
                                	if($tour_data['tour_type']==$v['id']){$selected='selected';}
                                	else{$selected='';}
                                	echo '<option value="'.$v['id'].'" '.$selected.'>'.$v['tour_type_name'].' </option>';
                                }
                                ?>
								</select>				
								</div>
							</div>
 -->

 						<!-- 	<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Choose Theme <span style = "color:red">*</span>
								</label>
								<div class='col-sm-4 controls'>
								<select class='select2 form-control' data-rule-required='true' name='tour_type[]' id="tour_type" multiple data-rule-required='true' required>
                                <option value="">Choose Theme</option>
                                <?php
                               foreach($tour_type as $k => $v)
                                {
                                	echo '<option value="'.$v['id'].'">'.$v['tour_type_name'].' </option>';
                                }
                                ?>
								</select>				
								</div>
							</div> -->

								<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Choose Theme
								</label>
								<div class='col-sm-4 controls'>
								<select class='select2 form-control' data-rule-required='true' name='tour_type[]' id="tour_type" multiple data-rule-required='true' required>
                                <option value="">Choose Theme</option>
                                <?php
                                  $tour_data['tour_type'] = explode(',', $tour_data['tour_type']);
                               	  $tour_type_data = $tour_data['tour_type'];
                                foreach($tour_type as $k => $v)
                                {

                                	if(in_array($v['id'],$tour_type_data)){$selected='selected';}
                                	else{$selected='';}
                                	echo '<option value="'.$v['id'].'" '.$selected.'>'.$v['tour_type_name'].' </option>';
                                }
                                ?>
								</select>				
								</div>
							</div>
							
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Choose Activity
								</label>
								<div class='col-sm-4 controls'>
								<select class='select2 form-control' data-rule-required='true' name='theme[]' id="theme" multiple data-rule-required='true' required>
                                <option value="">Choose Activity</option>
                                <?php
                                /*theme = $tour_data['theme'];
                                $theme = json_decode($theme,1); */
	                        	$tour_data['theme'] = explode(',', $tour_data['theme']);
	                            $theme = $tour_data['theme'];
                                foreach($tour_subtheme as $k => $v)
                                {
                                	if(in_array($v['id'],$theme)){$selected='selected';}
                                	else{$selected='';}
                                	echo '<option value="'.$v['id'].'" '.$selected.'>'.$v['tour_subtheme'].' </option>';
                                }
                                ?>
								</select>				
								</div>
							</div>

							<!--<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Choose Destination
								</label>
								<div class='col-sm-8 controls'>
								<select class='select2 form-control' data-rule-required='true' name='destination' id="destination" data-rule-required='true' required>
                                <option value="">Choose Destination</option>
                                <?php
                                foreach($tour_destinations as $tour_destinations_key => $tour_destinations_value)
                                {
                                	if($tour_data['destination']==$tour_destinations_value['id'])
                                    {$selected='selected';}else{$selected='';}
                                	echo '<option value="'.$tour_destinations_value['id'].'" '.$selected.'>'.string_replace_encode($tour_destinations_value['destination']).' [ '.$tour_destinations_value['type'].' ]</option>';
                                }
                                ?>
								</select>				
								</div>
							</div>-->
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Choose Region
								</label>
								<div class='col-sm-4 controls'>
								<select class='select2 form-control' data-rule-required='true' name='tours_continent' id="tours_continent" data-rule-required='true' required>
                                <option value="">Choose Region</option>
                                <?php
                                foreach($tours_continent as $tours_continent_key => $tours_continent_value)
                                {
                                	$tours_continent = $tour_data['tours_continent'];
                                	if($tours_continent_value['id']==$tours_continent){$selected = 'selected';}
                                	else{$selected = '';}

                                	echo '<option value="'.$tours_continent_value['id'].'" '.$selected.'>'.$tours_continent_value['name'].' </option>';
                                }
                                ?>
								</select>				
								</div>
							</div>
							<!-- <div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Choose Country
								</label>
								<div class='col-sm-4 controls'>
								<select class='select2 form-control' name='tours_country' id="tours_country" data-rule-required='true' required>
                                <option value="">Choose Country</option>
                                <?php
                                $tours_country = $tour_data['tours_country'];
                                foreach($tours_continent_country as $k => $v)
                                {
                                	if($v['id']==$tours_country){$selected = 'selected';}
                                	else{$selected = '';}

                                	echo '<option value="'.$v['id'].'" '.$selected.'>'.$v['name'].' </option>';
                                }
                                ?>                              
								</select>				
								</div>
							</div> -->

							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Choose Country
								</label>
								<div class='col-sm-4 controls'>
								<select class='select2 form-control' data-rule-required='true' name='tours_country[]' id="tours_country" multiple data-rule-required='true' required>
                                <option value="">Choose Country</option>
                                <?php
                               /* $tours_country_data = $tour_data['tours_country'];
                                $tours_country_data = json_decode($tours_country_data,1); */
                         		$tour_data['tours_country'] = explode(',', $tour_data['tours_country']);
	                            $tours_country_data = $tour_data['tours_country'];
                                foreach($tours_country_name as $k => $v)
                                {
                                	if(in_array($v['id'],$tours_country_data)){$selected='selected';}
                                	else{$selected='';}
                                	echo '<option value="'.$v['id'].'" '.$selected.'>'.$v['name'].' </option>';
                                }
                                ?>
								</select>				
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Choose City
								</label>

								<div class='col-sm-6 col-md-6 controls'>
								<div class="org_row">
								<div class='col-sm-6'>
								<select class='select2 form-control' name='tours_city[]' id="tours_city" multiple data-rule-required='true' >
                                <option value="">Choose City</option>                               
								<?php
                                /*$tours_city = $tour_data['tours_city'];
                                $tours_city = json_decode($tours_city,1);*/
                                $tour_data['tours_city'] = explode(',', $tour_data['tours_city']);
	                            $tours_country_city = $tour_data['tours_city'];
                                foreach($tours_country_city as $k => $v)
                                {
                                	if(in_array($v['id'],$tours_city)){$selected = 'selected';}
                                	else{$selected = '';}

                                	echo '<option value="'.$v['id'].'" '.$selected.'>'.$v['CityName'].' </option>';
                                }
                                ?>  
                               </select>	
                               </div>

                               	<div class='col-sm-6'>
								<select id="second" class="form-control" name="tours_city_new[]" multiple>
								<?php 

								foreach ($tour_data['tours_city'] as  $t_city) {

								$query_x = "select * from tours_city where id='$t_city'"; // echo $query; exit;
								$exe_x   = mysql_query($query_x);
								$fetch_x = mysql_fetch_assoc($exe_x);

								?>
								<option selected value="<?= $fetch_x['id'] ?>"><?= $fetch_x['CityName'] ?></option>
								<?php
								}
								?>
								</select>
								</div>
								</div>				
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Choose Duration
								</label>
								<div class='col-sm-4 controls'>
								<input type="hidden" name="duration" value="<?=$tour_data['duration']?>">	
								<select class='select2 form-control' data-rule-required='true' data-rule-required='true' required disabled>
                                <option value="">Choose Duration</option>
                                <?php
                                for($dno=1;$dno<=31;$dno++)
                                {
                                	if($dno==1) { $DayNight = ($dno).' Days | '.($dno).' Nights';}
                                    else { $DayNight = ($dno+1).' Days | '.($dno).' Nights';}  
                                    if($tour_data['duration']==$dno)
                                    {$selected='selected';}else{$selected='';}
                                	echo '<option value="'.$dno.'" '.$selected.'>'.$DayNight.'</option>';
                                }
                                ?>
								</select>				
								</div>
							</div>

							<input type="hidden" name="adult_twin_sharing" id="adult_twin_sharing" value="<?=$tour_data['adult_twin_sharing']?>"
										placeholder="Adult on twin sharing" data-rule-required='true'
										class='form-control add_pckg_elements' required>									

										<input type="hidden" name="adult_twin_sharing" id="adult_twin_sharing" value="2000"
										placeholder="Adult on twin sharing" data-rule-required='true'
										class='form-control add_pckg_elements' required>	

										<input type="hidden" name="adult_tripple_sharing" id="adult_tripple_sharing" value="3000"
										placeholder="Adult on twin sharing" data-rule-required='true'
										class='form-control add_pckg_elements' required>								

							<!--div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Per Person Double Occupancy
								</label>
								<div class='col-sm-4 controls'>
									<input type="hidden" name="adult_tripple_sharing" id="adult_tripple_sharing" value="<?=$tour_data['adult_twin_sharing']?>"
										placeholder="Adult on twin sharing" data-rule-required='true'
										class='form-control add_pckg_elements' required>									
								</div>
							</div>
							< <div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Adult on tripple sharing
								</label>
								<div class='col-sm-4 controls'>
									<input type="text" name="adult_tripple_sharing" id="adult_tripple_sharing" value="<?=$tour_data['adult_tripple_sharing']?>"
										placeholder="Adult on tripple sharing" class='form-control'>									
								</div>
							</div> 
							<!--<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Child with bed
								</label>
								<div class='col-sm-4 controls'>
									<input type="text" name="child_with_bed" id="child_with_bed"  value="<?=$tour_data['child_with_bed']?>"
										placeholder="Child with bed" class='form-control'>									
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Child without bed
								</label>
								<div class='col-sm-4 controls'>
									<input type="text" name="child_without_bed" id="child_without_bed" value="<?=$tour_data['child_without_bed']?>"
										placeholder="Child without bed" class='form-control'>									
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Joining directly
								</label>
								<div class='col-sm-4 controls'>
									<input type="text" name="joining_directly" id="joining_directly" value="<?=$tour_data['joining_directly']?>"
										placeholder="Joining directly" class='form-control'>									
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Single Suppliment
								</label>
								<div class='col-sm-4 controls'>
									<input type="text" name="single_suppliment" id="single_suppliment" value="<?=$tour_data['single_suppliment']?>"
										placeholder="Joining directly" class='form-control'>									
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Service Tax
								</label>
								<div class='col-sm-8 controls'>
									<input type="text" name="service_tax" id="service_tax" value="<?=string_replace_encode($tour_data['service_tax'])?>"
										placeholder="Service Tax" class='form-control'>									
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>TCS
								</label>
								<div class='col-sm-8 controls'>
									<input type="text" name="tcs" id="tcs" value="<?=string_replace_encode($tour_data['tcs'])?>"
										placeholder="TCS" class='form-control'>									
								</div>
							</div>
						    -->
						    <div class="form-group">
								<label class="control-label col-sm-3" for="validation_current">Tour Highlights
								</label>
								<div class="col-sm-8 controls">
								<textarea name="highlights" class="form-control" cols="70" rows="5" placeholder="Tour Highlights"><?=string_replace_encode($tour_data['highlights'])?></textarea>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3" for="validation_current">Tour Inclusions
								</label>
								<div class="col-sm-8 controls">
								<textarea name="inclusions" class="form-control" cols="70" rows="5" placeholder="Tour Inclusions"><?=string_replace_encode($tour_data['inclusions'])?></textarea>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3" for="validation_current">Tour Exclusions
								</label>
								<div class="col-sm-8 controls">
								<textarea name="exclusions" class="form-control" cols="70" rows="5" placeholder="Tour Exclusions"><?=string_replace_encode($tour_data['exclusions'])?></textarea>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3" for="validation_current">Terms & Conditions
								</label>
								<div class="col-sm-8 controls">
								<textarea name="terms" class="form-control" cols="70" rows="5" placeholder="Terms & Conditions"><?=string_replace_encode($tour_data['terms'])?></textarea>
								</div>
							</div>	
							<div class="form-group">
								<label class="control-label col-sm-3" for="validation_current">Cancellation Policy
								</label>
								<div class="col-sm-8 controls">
								<textarea name="canc_policy" class="form-control" cols="70" rows="5" placeholder="Cancellation Policy"><?=string_replace_encode($tour_data['canc_policy'])?></textarea>
								</div>
							</div>	

							<?php
							$inclusions_checks = $tour_data['inclusions_checks'];
							$inclusions_checks = json_decode($inclusions_checks,1);							
							?>

							<div class='form-group'>
                                <label class='control-label col-sm-3' for='validation_current'>Inclusions
                                </label>
                                <div class='col-sm-4 controls'>
                                    <input type="checkbox" name="inclusions_checks[]" value="Hotel" <?php if(in_array('Hotel',$inclusions_checks)){ echo 'checked';}?> > <!-- <i class="fa fa-hotel"> --> Hotel  <br>  
                                    <input type="checkbox" name="inclusions_checks[]" value="Car" <?php if(in_array('Car',$inclusions_checks)){ echo 'checked';}?> > <!-- <i class="fa fa-car"> --> Car  <br>
                                    <input type="checkbox" name="inclusions_checks[]" value="Meals" <?php if(in_array('Meals',$inclusions_checks)){ echo 'checked';}?> > <!-- <i class="fa fa-spoon"> --> Meals  <br>
                                    <input type="checkbox" name="inclusions_checks[]" value="Sightseeing" <?php if(in_array('Sightseeing',$inclusions_checks)){ echo 'checked';}?> > <!-- <i class="fa fa-binoculars"> --> Sightseeing  <br>     
                                	<input type="checkbox" name="inclusions_checks[]" value="Transfers" <?php if(in_array('Transfers',$inclusions_checks)){ echo 'checked';}?>> <!-- <i class="fa fa-binoculars"> --> Transfers  <br>     
                                </div>
                            </div>	

							<div class='form-group'>
                                <label class='control-label col-sm-3' for='validation_current'>Banner Image
                                </label>
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
                                       <div class='col-sm-4 controls'>
<?php
echo '<img class="banner_imgs" src="'.$airliners_weburl.'extras/custom/keWD7SNXhVwQmNRymfGN/images/' . $tour_data['banner_image'] . '" style="width:100%">';
?>										
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-sm-3' for='validation_current'>Change Banner Image
                                </label>
                                <div class='col-sm-4 controls'>
                                    <input type="file" name="banner_image" id="banner_image" class='form-control'>	
                                    <?=img_size_msg(200,300)?>								
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-sm-3' for='validation_current'>Gallery
                                </label>
                                <div class='col-sm-9 controls'>
                                    <?php
                                    $gallery = $tour_data['gallery'];
                                    $explode = explode(',', $gallery);
                                    $galleryImages = '';
                                    for ($g = 0; $explode[$g] != ''; $g++) {
                                        if ($g % 5 == 0) {
                                            $galleryImages .= '<tr id="">';
                                        }
                                      //  debug($tour_data); exit();
                                        //$path = $this->template->domain_image_upload_path().$explode[$g];
                                        $galleryImages .= '<td id = "gal_img'.$g.'"> <a href = "javascript:void(0)" onclick="deleteimage('.$tour_data['id'].',\''.$explode[$g].'\',\'gal_img'.$g.'\')">X</a> &nbsp;&nbsp; <img style="width:90%;" class="gal_img" src="'.$airliners_weburl.'extras/custom/keWD7SNXhVwQmNRymfGN/images/' . $explode[$g] . '"></td> ';
                                        //echo '<img src="'.$path.'" style="width:50%"> <input type="checkbox" name="gallery_previous[]" value="'.$explode[$g].'" checked><br><br>';
                                        if (($g + 1) % 5 == 0) {
                                            $galleryImages .= '</tr>';
                                        }
                                    }
                                    echo "<table style='width:100%;'>" . $galleryImages . "</table>";
                                    ?>										
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-sm-3' for='validation_current'>Upload Gallery
                                </label>
                                <div class='col-sm-4 controls'>
                                    <input type="file" name="gallery[]" id="gallery" multiple class='form-control'>	
                                    <?=img_size_msg(200,300)?>								
                                </div>
                            </div>	


							<div class='' style='margin-bottom: 0'>
								<div class='row'>
									<div class='col-sm-9 col-sm-offset-3'>		
									    <input type="hidden" name="tour_id" value="<?=$tour_id?>">						
										<button class='btn btn-primary' type="submit">Save</button>
										<a class='btn btn-primary' href="<?php echo base_url(); ?>index.php/tours/tour_list">Tour List</a>
									</div>
								</div>
							</div>						    						
						    <hr>
						    				
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
 function deleteimage(image_id,image_name,tag_id)
    {
      var image_id = image_id;
      var image_name = image_name;
  // alert(image_name);
  var answer = confirm ("Are you sure you want to delete from this post?");
  if (answer)
  {
    $.ajax({
      type: "POST",
      url: "<?php echo site_url('tours/tours_delete_image_id');?>",
      data: {"image_id" : image_id , "image_name" : image_name},
      success: function (response) {
        if (response == 1) {
                    // $(".imagelocation"+image_id).remove(".imagelocation"+image_id);
                    document.getElementById(tag_id).remove();
                    // console.log($('#'+image_name));
                  };
                  
                }
              });
  }
}


     $(document).ready(function()
     {


     	//var demo1 = $('select[name="tour_type[]"]').bootstrapDualListbox();
     	var demo2 = $('select[name="theme[]"]').bootstrapDualListbox();
     	//var tours_country = $('select[name="tours_country[]"]').bootstrapDualListbox();
     	var demo1 = $('select[name="tour_type[]"]').bootstrapDualListbox({
					  nonSelectedListLabel: 'Non-selected',
					  selectedListLabel: 'Selected',
					  preserveSelectionOnMove: false,
					  moveOnSelect: false
					});
     	var tours_country = $('select[name="tours_country[]"]').bootstrapDualListbox({
					  nonSelectedListLabel: 'Non-selected',
					  selectedListLabel: 'Selected',
					  preserveSelectionOnMove: false,
					  moveOnSelect: false
					});
        $('#tours_continent').on('click', function() { 
        $tours_continent = $('#tours_continent').val();
        	$.post('<?=base_url();?>tours/ajax_tours_continent',{'tours_continent':$tours_continent},function(data){
          	  //alert(data);
              $('#tours_country').html(data);
              $('#tours_city').html('');
              tours_country.bootstrapDualListbox('refresh', true);
         	});
        });  

        $('#tours_country').on('change', function() { 
        	var res = '';
        	var old_data = $('#tours_city').html();
         	var tours_countries = $('#tours_country').val();
         	if(tours_countries.length > 0 ){
				var tours_country_list = tours_countries;
         	}else{
	         	var tours_country_list = tours_countries.split(',');
	        }

	        // please wait
	        $('#tours_city').html('<option>Please wait....</option>');
         	$.each(tours_country_list, function(index, item) {
			    // do something with `item` (or `this` is also `item` if you like)
		        $.post('<?=base_url();?>tours/ajax_tours_country',{'tours_country':item},function(data)
		        {
		        	res += data;
		        });
	        });
        	if((res !== '') && (typeof(res) !== 'undefined')){
	            $('#tours_city').html(data);
	        }else{			        	
	            $('#tours_city').html(old_data);
	        }
		});
     });    
</script>
<script type="text/javascript">
     $(document).ready(function()
     {          
          $('#tours_continent').on('click', function() { 
          $tours_continent = $('#tours_continent').val();
          $.post('<?=base_url();?>tours/ajax_tours_continent',{'tours_continent':$tours_continent},function(data)
          {       	  
              $('#tours_country').html(data);
              $('#tours_city').html('');
          });
          });  
               
          $('#tours_country').on('change', function() { 
          $tours_country = $('#tours_country').val();
          $.post('<?=base_url();?>tours/ajax_tours_country',{'tours_country':$tours_country},function(data)
          {
          	  //alert(data);
              $('#tours_city').html(data);
          });
          });   

          $('#tours_city').click(function() {
		    var options = $("#tours_city").find(':selected').clone();
		    $('#second').append(options);
		    getSelectMultiple();
		});

		$('#second').click(function() {
		   $("#second").find(':selected').remove();
		   getSelectMultiple();
		}); 

		function getSelectMultiple(){
			$("#second option").prop('selected', true);
		}         
     });

     $(document).ready(function(){
		if($('#tours_country').length>0){
			var tours_country_list;
			var tours_country_list = $("#tours_country option:selected").map(function(){ return this.value }).get();
			$.each(tours_country_list, function(index, item) {
			    // do something with `item` (or `this` is also `item` if you like)
		        $.post('<?=base_url();?>tours/ajax_tours_country',{'tours_country':item},function(data)
		        {
		        	if(index>0){
			            $('#tours_city').append(data);
			        }else{			        	
			            $('#tours_city').html(data);
			        }
		        });
	        });
     	}
     })

   //  function validation()
    // {
/*          $adult_twin_sharing    = $('#adult_twin_sharing').val();
          $adult_tripple_sharing = $('#adult_tripple_sharing').val();
          /*$child_with_bed        = $('#child_with_bed').val();
          $child_without_bed     = $('#child_without_bed').val();
          $joining_directly      = $('#joining_directly').val();
          $single_suppliment     = $('#single_suppliment').val();*

          if($adult_twin_sharing=="")
          {
             $("#adult_twin_sharing").attr("placeholder","Adult Twin Sharing Required.");
             $("#adult_twin_sharing").focus();
             return false;
          }
          else if(isNaN($adult_twin_sharing))
          {
             $("#adult_twin_sharing").val('');
             $("#adult_twin_sharing").attr("placeholder","Valid Amount Required.");
             $("#adult_twin_sharing").focus();
             return false;
          }
          if($adult_tripple_sharing!="" && isNaN($adult_tripple_sharing))
          {
             $("#adult_tripple_sharing").val('');
             $("#adult_tripple_sharing").attr("placeholder","Valid Amount Required.");
             $("#adult_tripple_sharing").focus();
             return false;
          }*/
          /*if($child_with_bed!="" && isNaN($child_with_bed))
          {
             $("#child_with_bed").val('');
             $("#child_with_bed").attr("placeholder","Valid Amount Required.");
             $("#child_with_bed").focus();
             return false;
          }
          if($child_without_bed!="" && isNaN($child_without_bed))
          {
             $("#child_without_bed").val('');
             $("#child_without_bed").attr("placeholder","Valid Amount Required.");
             $("#child_without_bed").focus();
             return false;
          }
          if($joining_directly!="" && isNaN($joining_directly))
          {
             $("#joining_directly").val('');
             $("#joining_directly").attr("placeholder","Valid Amount Required.");
             $("#joining_directly").focus();
             return false;
          }
          if($single_suppliment!="" && isNaN($single_suppliment))
          {
             $("#single_suppliment").val('');
             $("#single_suppliment").attr("placeholder","Valid Amount Required.");
             $("#single_suppliment").focus();
             return false;
          }*/
    // }
     
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
