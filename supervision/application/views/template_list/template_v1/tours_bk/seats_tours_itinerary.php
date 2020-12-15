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
						data-toggle="tab">Tour Itinerary Seats </a></li>
					
				</ul>
			</div>
		</div>
		<!-- PANEL HEAD START -->
		<div class="panel-body">
			<!-- PANEL BODY START -->
			<form
				action="<?php echo base_url(); ?>index.php/tours/seats_tours_itinerary_save"
				method="post" enctype="multipart/form-data" id="form form-horizontal validate-form"
				class='form form-horizontal validate-form'>
				<div class="tab-content">
					<!-- Add Package Starts -->
					<div role="tabpanel" class="tab-pane active" id="add_package">
						   <div class="col-md-12">
                           
						    <div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Tour Code
								</label>
								<div class='col-sm-4 controls'>
									<input type="text" value="<?=$tours_itinerary['tour_code']?>" placeholder="Tour Code" class='form-control' disabled>									
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Total no of Seats
								</label>
								<div class='col-sm-4 controls'>
									<input type="text" name="no_of_seats" id="no_of_seats" value="<?=$tours_itinerary['no_of_seats']?>" placeholder="Enter Total no of Seats"  data-rule-required='true' class='form-control' required>					
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Total Booked
								</label>
								<div class='col-sm-4 controls'>
									<input type="text" name="total_booked" id="total_booked" value="<?=$tours_itinerary['total_booked']?>" placeholder="Enter Total Booked" data-rule-required='true' class='form-control' required>					
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Available Seats
								</label>
								<div class='col-sm-4 controls'>
									<input type="text" name="available_seats" id="available_seats" value="<?=$tours_itinerary['available_seats']?>"
										placeholder="Enter Available Seats" data-rule-required='true'
										class='form-control add_pckg_elements' required>								
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Group Booking Hold
								</label>
								<div class='col-sm-4 controls'>
								<select name="booking_hold" id="booking_hold" class='select2 form-control'>
                                <option value="">Booking Hold Status</option>
                                <option value="1" <?php if($tours_itinerary['booking_hold']==1){echo 'selected';}?> >Yes</option>
                                <option value="0" <?php if($tours_itinerary['booking_hold']==0){echo 'selected';}?> >No</option>
								</select>				
								</div>
							</div>
							<div class='' style='margin-bottom: 0'>
								<div class='row'>
									<div class='col-sm-9 col-sm-offset-3'>		
									    <input type="hidden" name="id" value="<?=$id?>">
									    <input type="hidden" name="tour_id" value="<?=$tours_itinerary['tour_id']?>">
									    <input type="hidden" name="dep_date" value="<?=$tours_itinerary['dep_date']?>">	
										<button class='btn btn-primary' type="submit">Save</button>
										<a class='btn btn-primary' href="<?php echo base_url(); ?>index.php/tours/tour_date_list">Confirmed Departures</a>
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
 
<!--<script src="<?=JAVASCRIPT_LIBRARY_DIR?>common.js" type="text/javascript"></script>
<script	src="<?=SYSTEM_RESOURCE_LIBRARY?>/validate/jquery.validate.min.js" type="text/javascript"></script>
<script	src="<?=SYSTEM_RESOURCE_LIBRARY?>/validate/additional-methods.js" type="text/javascript"></script>
<script src="<?=SYSTEM_RESOURCE_LIBRARY?>/validate/custom.js"></script>-->
