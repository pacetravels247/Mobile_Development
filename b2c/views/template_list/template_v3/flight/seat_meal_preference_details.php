<?php if(isset($seat_meal_preference_details) == true && (isset($seat_meal_preference_details['MealPreference']) == true && valid_array($seat_meal_preference_details['MealPreference']) == true)
		OR (isset($seat_meal_preference_details['SeatPreference']) == true && valid_array($seat_meal_preference_details['SeatPreference']) == true)) {
			
		$MealPreference = @$seat_meal_preference_details['MealPreference'];
		$SeatPreference = @$seat_meal_preference_details['SeatPreference'];
?>
<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">	

<div class="panel panel-default flight_special_req">
            <div class="panel-heading" role="tab" id="headingOne">
                <h4 class="panel-title">
                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapsetwo" aria-expanded="true" aria-controls="collapseOne">
                        <div class="labltowr noxtra arimobold nopad">Seat,Meal Preference(Optional) <small>no extra charges are applicable</small> <i class="more-less glyphicon glyphicon-minus"></i>
                       </div> 
                    </a>
                </h4>
            </div>
            <div id="collapsetwo" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                <div class="panel-body">
                     <div class="baggage_meal_details">
	
	<ul class="nav nav-tabs extra_services_indicator_tab" role="tablist">
		<?php if(valid_array($MealPreference) == true){ ?>
	    	<li role="presentation">
	    		<a class="btn btn-sm btn-default" href="#meal_preference_tab" aria-controls="home" role="tab" data-toggle="tab">
	    			<img style="height: 19px; margin-right: 5px;" src="<?php echo $GLOBALS['CI']->template->template_images('meal_icon.png'); ?>" alt=""/> Meal Preference
	    		</a>
	    	</li>
	    <?php }
	    if(valid_array($SeatPreference) == true){ ?>
	    	<li role="presentation" class="seat_select_mob">
	    		<a class="btn btn-sm btn-default" href="#seat_preference_tab" aria-controls="profile" role="tab" data-toggle="tab">
	    			<img style="height: 19px; margin-right: 5px;" src="<?php echo $GLOBALS['CI']->template->template_images('seat_icon.png'); ?>" alt=""/> Seat Preference
	    		</a>
	    	</li>
	    <?php } ?>
  	</ul>
  	
  	
	<div class="tab-content">
		<!-- Meal Preference Starts -->
		<div role="tabpanel" class="pasngrinput tab-pane other_inp" id="meal_preference_tab">
		<?php
			//meal pref
			if(valid_array($MealPreference) == true){ ?>
					<div class="col-xs-12 nopad "><!-- meal pref div starts -->
					<div style="font-size: 15px; color: #666;">Choose Meal Preferences</div>
					<div class="col-xs-2 nopad">
					
					<div class="pt30"></div>
					<?php
							for($ex_pax_index=1; $ex_pax_index <= $total_pax_count; $ex_pax_index++) {//START FOR LOOP FOR PAX DETAILS
								$pax_type = pax_type($ex_pax_index, $total_adult_count, $total_child_count, $total_infant_count);
								$pax_type_count = pax_type_count($ex_pax_index, $total_adult_count, $total_child_count, $total_infant_count);
								if($pax_type != 'infant'){ ?>
									
										<div class="pref_pax_name meal_pref_pax_name"><?=ucfirst($pax_type)?> <?=($pax_type_count)?></div>
									
							<?php }
							}
					?>
					</div>
					
					<div class="addbaggage nopad">
						<?php
							$meal_p_input_counter = 0;
							foreach ($MealPreference as $meal_pok => $meal_pov){ ?>
								<div class="addtlbox col-xs-10 col-md-4 padfive">
								<?php
								for($ex_pax_index=1; $ex_pax_index <= $total_pax_count; $ex_pax_index++) {//START FOR LOOP FOR PAX DETAILS
									$pax_type = pax_type($ex_pax_index, $total_adult_count, $total_child_count, $total_infant_count);
									$pax_type_count = pax_type_count($ex_pax_index, $total_adult_count, $total_child_count, $total_infant_count);
									if($pax_type != 'infant'){
									
										if($ex_pax_index == 1){
											$meal_p_label =  $meal_pov[0]['Origin'].' <i class="fa fa-long-arrow-right" aria-hidden="true"></i> '.$meal_pov[0]['Destination'];
											?>
											<span class="formlabel"> <?=$meal_p_label?></span>
								<?php 	} ?>
									<div class="col-xs-12 nopad spllty">
										<div class="selectedwrap">
											<select name="meal_pref<?=$meal_p_input_counter?>[]" class="mySelectBoxClass flyinputsnor">
												<option value="">Meal Preference</option>
												<?php foreach($meal_pov as $meal_pk => $meal_pv){ ?>
													<option value="<?=$meal_pv['MealId']?>"><?=$meal_pv['Description']?></option>
												<?php }
												?>
											</select>
										</div> 
									</div>
								<?php }//not infant condition ends
						
								}//end pax loop
							?>
							<?php
								$meal_p_input_counter++;
							?>
							</div>
							<?php } //meal pref. loop ends
							?>
					</div>
				</div><!-- meal pref div ends -->
			<?php }//End of meal pref ?>
			
    	</div>
    	<!-- Meal Preference Ends -->
    	
    	<!-- Seat Preference Starts -->
    	<div role="tabpanel" class="tab-pane pasngrinput other_inp" id="seat_preference_tab">
    		<?php
				//Meals
				if(valid_array($SeatPreference) == true){ ?>
				<div class="col-xs-12 nopad"><!-- Seat Preference div starts -->
					<div style="font-size: 15px; color: #666;">Choose Seat Preferences</div>
					<div class="col-xs-2 nopad">
					
					<div class="pt30"></div>
					<?php
							for($ex_pax_index=1; $ex_pax_index <= $total_pax_count; $ex_pax_index++) {//START FOR LOOP FOR PAX DETAILS
								$pax_type = pax_type($ex_pax_index, $total_adult_count, $total_child_count, $total_infant_count);
								$pax_type_count = pax_type_count($ex_pax_index, $total_adult_count, $total_child_count, $total_infant_count);
								if($pax_type != 'infant'){ ?>
									
										<div class="pref_pax_name seat_pref_pax_name" ><?=ucfirst($pax_type)?> <?=($pax_type_count)?></div>
									
							<?php }
							}
					?>
					</div>
					<div class="col-xs-10 addbaggage nopad">
					<?php
					$seat_p_input_counter = 0;
					foreach ($SeatPreference as $seat_pok => $seat_pov){ ?>
						<div class="col-xs-12 padfive">
					<?php
						for($ex_pax_index=1; $ex_pax_index <= $total_pax_count; $ex_pax_index++) {//START FOR LOOP FOR PAX DETAILS
							$pax_type = pax_type($ex_pax_index, $total_adult_count, $total_child_count, $total_infant_count);
							$pax_type_count = pax_type_count($ex_pax_index, $total_adult_count, $total_child_count, $total_infant_count);
							if($pax_type != 'infant'){
							
								if($ex_pax_index == 1){
									$seat_pref_label =  $seat_pov[0]['Origin'].' <i class="fa fa-long-arrow-right" aria-hidden="true"></i> '.$seat_pov[0]['Destination'];
									?>
									<span class="formlabel"> <?=$seat_pref_label?></span>
						<?php 	} ?>
					
						<div class="col-xs-12 nopad spllty">
							<div class="selectedwrap">
								<select name="seat_pref<?=$seat_p_input_counter?>[]" class="mySelectBoxClass flyinputsnor">
									<option value="">Seat Preference</option>
									<?php foreach($seat_pov as $seat_k => $seat_v){ ?>
										<option value="<?=$seat_v['SeatId']?>"><?=$seat_v['Description']?></option>
									<?php }
									?>
								</select>
							</div>
						</div>
						<?php }//not infant condition ends
				
						}//end pax loop
					$seat_p_input_counter++;
					?>
					</div>
					<?php } ?>
						</div>
						
				</div>
				<?php }//End of Seat Pref.
				?>
    	</div>
    	<!-- Seat Preference Ends -->
    	
  	</div>
</div>
                </div>
            </div>
        </div>



<!-- <div class="moreflt boksectn">
   <div class="ontyp">
      
	

</div>
</div> -->
</div>
<?php  } ?>
