<?php error_reporting(0);?>
<script src="/chariot/extras/system/library/ckeditor/ckeditor.js"></script>
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
						data-toggle="tab">Tour Package Price Manager [ Package Name : <?=$tour_data['package_name']?> ] [ Package ID : <?=$tour_data['package_id']?> ]</a></li>
					
				</ul>
			</div>
		</div>
		<!-- PANEL HEAD START -->
		<div class="panel-body">
			<!-- PANEL BODY START -->
			<form
				action="<?php echo base_url(); ?>index.php/tours/tour_pricing_save"
				method="post" enctype="multipart/form-data" id="form form-horizontal validate-form"
				class='form form-horizontal validate-form'>
				<div class="tab-content">
					<!-- Add Package Starts -->
					<div role="tabpanel" class="tab-pane active" id="add_package">
						   <div class="col-md-12">
                           
						   <div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Adult on twin sharing
								</label>
								<div class='col-sm-4 controls'>
									<input type="text" name="adult_twin_sharing" id="adult_twin_sharing" value="<?=$tour_data['adult_twin_sharing']?>"
										placeholder="Adult on twin sharing" data-rule-required='true'
										class='form-control add_pckg_elements' required>									
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Adult on tripple sharing
								</label>
								<div class='col-sm-4 controls'>
									<input type="text" name="adult_tripple_sharing" id="adult_tripple_sharing" value="<?=$tour_data['adult_tripple_sharing']?>"
										placeholder="Adult on tripple sharing" data-rule-required='true'
										class='form-control' required>									
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Child with bed
								</label>
								<div class='col-sm-4 controls'>
									<input type="text" name="child_with_bed" id="child_with_bed"  value="<?=$tour_data['child_with_bed']?>"
										placeholder="Child with bed" data-rule-required='true'
										class='form-control' required>									
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Child without bed
								</label>
								<div class='col-sm-4 controls'>
									<input type="text" name="child_without_bed" id="child_without_bed" value="<?=$tour_data['child_without_bed']?>"
										placeholder="Child without bed" data-rule-required='true'
										class='form-control' required>									
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Joining directly
								</label>
								<div class='col-sm-4 controls'>
									<input type="text" name="joining_directly" id="joining_directly" value="<?=$tour_data['joining_directly']?>"
										placeholder="Joining directly" data-rule-required='true'
										class='form-control' required>									
								</div>
							</div>			    						
						    <div class='' style='margin-bottom: 0'>
								<div class='row'>
									<div class='col-sm-9 col-sm-offset-3'>	
									    <input type="hidden" name="tour_id" value="<?=$tour_id?>">						
										<button class='btn btn-primary' type="submit">Save</button>
										<a class='btn btn-primary' type="button" href="<?php echo base_url(); ?>index.php/tours/tour_list">Tour List</a>
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
          $('#tour_addition_save').on('click', function() { //alert('tour_addition_save');
          $package_name = $('#package_name').val();
          $destination  = $('#destination').val(); 
          $duration     = $('#duration').val();
          $.post('add_tour_pre/'+$package_name+'/'+$destination+'/'+$duration,{'duration':$duration},function(data)
          {
          	  //alert(data);
              $('#tour_addition').html(data);
          });
          });

          $('#no_of_days').on('change', function() { //alert('no_of_days');
          $no_of_days = $(this).val(); 
          $.post('no_of_days/'+$no_of_days,{'no_of_days':$no_of_days},function(data)
          {
          	  //alert(data);
              $('#itinerary_contents').html(data);
          });
          });

          $('#no_of_hotels').on('change', function() { //alert('no_of_hotels');
          $no_of_hotels = $(this).val(); 
          $.post('no_of_hotels/'+$no_of_hotels,{'no_of_hotels':$no_of_hotels},function(data)
          {
          	  //alert(data);
              $('#hotel_contents').html(data);
          });
          });

          $('#no_of_weather').on('change', function() { //alert('no_of_weather');
          $no_of_weather = $(this).val(); 
          $.post('no_of_weather/'+$no_of_weather,{'no_of_weather':$no_of_weather},function(data)
          {
          	  //alert(data);
              $('#weather_contents').html(data);
          });
          });

          $('#tour_dep_date').on('change', function() { //alert('tour_dep_date');
          $tour_dep_date = $(this).val(); 
          $.post('tour_dep_date/'+$tour_dep_date,{'tour_dep_date':$tour_dep_date},function(data)
          {
          	  //alert(data);
              $('#tour_dep_date_list').html(data);
              $('#tour_dep_date').val(''); 
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
<link href="<?=$airliners_weburl?>extras/system/template_list/template_v1/javascript/page_resource/datepicker/datepicker.css" rel="stylesheet"> 
<script src="<?=$airliners_weburl?>extras/system/template_list/template_v1/javascript/page_resource/datepicker/datepicker.js"> </script>  
<script src="<?=$airliners_weburl?>extras/system/template_list/template_v1/javascript/page_resource/datepicker/jquery.blueberry.js"> </script>  
<!--<script src="<?=JAVASCRIPT_LIBRARY_DIR?>common.js" type="text/javascript"></script>
<script	src="<?=SYSTEM_RESOURCE_LIBRARY?>/validate/jquery.validate.min.js" type="text/javascript"></script>
<script	src="<?=SYSTEM_RESOURCE_LIBRARY?>/validate/additional-methods.js" type="text/javascript"></script>
<script src="<?=SYSTEM_RESOURCE_LIBRARY?>/validate/custom.js"></script>-->
