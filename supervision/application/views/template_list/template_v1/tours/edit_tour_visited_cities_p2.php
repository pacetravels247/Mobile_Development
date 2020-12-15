<?php error_reporting(0);?>
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
						data-toggle="tab"> Visited City List : [ <?php echo 'Package Name : '.string_replace_encode($tour_data['package_name']);?>]</a>
					</li>			        
				</ul>
			</div>
		</div>
		<!-- PANEL HEAD START -->
		<div class="panel-body">
			<!-- PANEL BODY START -->
		<form
				action="<?php echo base_url(); ?>index.php/tours/edit_tour_visited_cities_p2_save"
				method="post" enctype="multipart/form-data" id="form form-horizontal validate-form"
				class='form form-horizontal validate-form'>
				<div class="tab-content">
					<!-- Add Package Starts -->
					<div role="tabpanel" class="tab-pane active" id="add_package">
						    <div class="col-md-12">

						    <div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Duration
								</label>
								<div class='col-sm-4 controls'>
									<input type="hidden" name="duration" id="duration" value="<?=$tour_data['duration']?>">
									<input type="text" value="<?=($tour_data['duration']).' Nights / '.($tour_data['duration']+1).' Days';?>" class='form-control' disabled>									
								</div>
							</div>

							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>City
								</label>
								<div class='col-sm-4 controls'>
							    <?php
                                $tours_city = $tour_data['tours_city'];
                                $tours_city = json_decode($tours_city,1);
                                //debug($tours_city);exit;

                                $city_in_record = $tour_visited_cities_details['city'];
                                $city_in_record = json_decode($city_in_record,1);


                                ?>
                                <select class='select2 form-control' name='city[]' id="city" multiple data-rule-required='true' required>                               
                                <?php
                                foreach($tours_city as $key => $value)
                                {
                                    if(in_array($value,$city_in_record))
                                    {$selected = 'selected';}else{ $selected='';}
                                	echo '<option value="'.$value.'" '.$selected.'>'.$tours_city_name[$value].' </option>';
                                }
                                ?>                              
								</select>									
								</div>
							</div>
							<!--<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Sightseeing for which entrance fee is included
								</label> 
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>&nbsp; </label> 
								<div class='col-sm-8 controls'>
								<textarea name="sightseeing" id="sightseeing" data-rule-required='true' class="form-control" cols="70" rows="5" placeholder="Sightseeing"><?=string_replace_encode($tour_visited_cities_details['sightseeing']);?></textarea>
								</div>
							</div> -->
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>No Of Nights
								</label>
								<div class='col-sm-4 controls'>
								<select name='no_of_nights' id="no_of_nights" class='select2 form-control' data-rule-required='true' data-rule-required='true' required>
                                <option value="">Choose No Of Nights</option>
                                <?php
                                for($non=1;$non<=31;$non++)
                                {
                                	if($tour_visited_cities_details['no_of_nights']==$non)
                                	{ $selected = 'selected'; } else{ $selected = ''; }
                                	echo '<option value="'.$non.'" '.$selected.'>'.$non.' Nights</option>';
                                }
                                ?>
								</select> &nbsp;	
											
								</div>
							</div>
							<!--<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>&nbsp; </label> 
								<div class='col-sm-4 controls'>
								<input type="checkbox" name="includes_city_tours" id="includes_city_tours" value="1" <?php if($tour_visited_cities_details['includes_city_tours']){echo 'checked';} ?> > Includes City Tours
								</div>
							</div>-->
							
							<!--
							<div id="itinerary_list" style="display:none;">
							<?php
							$itinerary = $tour_visited_cities_details['itinerary']; 
							$itinerary = json_decode($itinerary,1);
							//echo '<pre>'; print_r($itinerary);
                            foreach($itinerary as $key => $value)
                            {
                            	$accomodation = $value['accomodation'];
                            	if(in_array('Breakfast',$accomodation))
                            	{$Breakfast='checked';}else{$Breakfast='';}
                                if(in_array('Lunch',$accomodation))
                            	{$Lunch='checked';}else{$Lunch='';}
                                if(in_array('Dinner',$accomodation))
                            	{$Dinner='checked';}else{$Dinner='';}
            echo '<hr>';
            echo    '<div class="form-group">
								<label class="control-label col-sm-3" for="validation_current">Day '.($key+1).' </label>
								</div>';
		    echo    '<div class="form-group">
								<label class="control-label col-sm-3" for="validation_current">Day Program Title </label>
								<div class="col-sm-4 controls">
									<input type="text" name="program_title[]" value="'.$value['program_title'].'"
										placeholder="Enter Program Title" data-rule-required="true"
										class="form-control" required>									
								</div>
							</div>';			
			echo    '<div class="form-group">
								<label class="control-label col-sm-3" for="validation_current">Program Description
								</label>
								<div class="col-sm-8 controls">
								<textarea name="program_des[]" data-rule-required="true" class="form-control" data-rule-required="true" cols="70" rows="10" placeholder="Description">'.$value['program_des'].'</textarea>
								</div>
							</div>';
			echo '<div class="form-group">
								<label class="control-label col-sm-3" for="validation_current">Hotel Name </label>
								<div class="col-sm-4 controls">
									<input type="text" name="hotel_name[]" value="'.$value['hotel_name'].'"
										placeholder="Enter hotel name" data-rule-required="true"
										class="form-control" required>									
								</div>
							</div>';
			echo '<div class="form-group">
								<label class="control-label col-sm-3" for="validation_current">Star Rating </label>
								<div class="col-sm-4 controls">
								<select name="rating[]" data-rule-required="true" class="form-control" required>';
								for($s=1;$s<=5;$s++)
		                        {
		                        	echo '<option value="'.$s.'">'.$s.' Star</option>';
		                        }	
							echo '</select>								
								</div>
							</div>';	    					
			echo  '<div class="form-group">
								<label class="control-label col-sm-3" for="validation_current">Accomodation </label>
								<div class="col-sm-4 controls">
									<input type="checkbox" name="accomodation['.($key).'][]" value="Breakfast" '.$Breakfast.'> Breakfast <br>									
									<input type="checkbox" name="accomodation['.($key).'][]" value="Lunch" '.$Lunch.'> Lunch <br>									
									<input type="checkbox" name="accomodation['.($key).'][]" value="Dinner" '.$Dinner.'> Dinner <br>									
								</div>
							</div>';
                            }
							?>						
							</div>-->	

							<div class='' style='margin-bottom: 0'>
								<div class='row'>
									<div class='col-sm-9 col-sm-offset-3'>	
										<input type="hidden" id="no_of_nights_db" value="<?=$tour_visited_cities_details['no_of_nights']?>">
										<input type="hidden" id="total_no_of_nights" value="<?=$total_no_of_nights?>">
									    <input type="hidden" name="id" value="<?=$id?>">
									    <input type="hidden" name="tour_id" value="<?=$tour_id?>">							
										<button class='btn btn-primary' type='submit'>Save</button>
									</div>
								</div>
							</div>
						</div>																	
					</div>					
				</div>
			</form>				
		</div>
		<!-- PANEL BODY END -->	
	    <!-- PANEL WRAP END -->						
		</div>
		</div>
<script type="text/javascript">
     $(document).ready(function()
     {         
          $('#no_of_nights').on('change', function() { 
          $no_of_nights = parseInt($(this).val());  
          $total_no_of_nights = parseInt($('#total_no_of_nights').val());
          $duration = parseInt($('#duration').val());
          $no_of_nights_db = parseInt($('#no_of_nights_db').val());
          //alert($no_of_nights);alert($total_no_of_nights);alert($duration);
          
          if(($no_of_nights+$total_no_of_nights-$no_of_nights_db)>$duration)
          {
          	 $msg = $duration+' Nights / '+($duration+1)+' Days'
             alert('Sorry! This tour is designed for '+$msg+'. You are exceeding the limit.');
             $('#no_of_nights').val($no_of_nights_db);
             return false;
          }
          /*
          $.post('<?php echo base_url();?>tours/no_of_nights2/'+$no_of_nights+'/'+<?=$id?>+'/'+<?=$tour_id?>,{'no_of_nights':$no_of_nights},function(data)
          {
          	  //alert(data);
              $('#itinerary_list').html(data);
          });*/
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
