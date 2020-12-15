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
						data-toggle="tab"> Visited City List : [ <?php echo 'Package Name : '.string_replace_encode($tour_data['package_name']);?>]</a></li>
			        <li aria-controls="home"> &nbsp;&nbsp;
					<button class='btn btn-primary'><a href="<?php echo base_url(); ?>index.php/tours/tour_visited_cities/<?=$tour_id;?>" style="color:white;">City List</a></button>
					<button onclick="location.href='<?php echo base_url(); ?>index.php/tours/tour_list';"  class='btn btn-primary'><a style="color:white;">Package List</a></button>
				    </li>			
					
				</ul>
			</div>
		</div>
		<!-- PANEL HEAD START -->
		<div class="panel-body">
			<!-- PANEL BODY START -->
		<form
				action="<?php echo base_url(); ?>index.php/tours/edit_tour_visited_cities_save"
				method="post" enctype="multipart/form-data" id="form form-horizontal validate-form"
				class='form form-horizontal validate-form'>
				<div class="tab-content">
					<!-- Add Package Starts -->
					<div role="tabpanel" class="tab-pane active" id="add_package">
						    <div class="col-md-12">

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
							
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>No Of Nights
								</label>
								<div class='col-sm-4 controls'>
								<input type="hidden" name="no_of_nights" value="<?=$tour_visited_cities_details['no_of_nights']?>">	
								<select name='no_of_nights' id="no_of_nights" class='select2 form-control' data-rule-required='true' data-rule-required='true' required disabled>
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
							
							<hr>
							
							<div class='' style='margin-bottom: 0'>
								<div class='row'>
									<div class='col-sm-9 col-sm-offset-3'>	
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
          $no_of_nights = $(this).val();  //alert($no_of_nights);
          $.post('<?php echo base_url();?>index.php/tours/no_of_nights2/'+$no_of_nights+'/'+<?=$id?>+'/'+<?=$tour_id?>,{'no_of_nights':$no_of_nights},function(data)
          {
          	  //alert(data);
              $('#itinerary_list').html(data);
          });
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
<script type="text/javascript" src="/airliners/extras/system/template_list/template_v1/javascript/js/nicEdit-latest.js"></script> 
<script type="text/javascript">
bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
</script>-->
