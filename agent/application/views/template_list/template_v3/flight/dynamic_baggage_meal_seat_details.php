<?php if(isset($baggage_meal_details) == true && (isset($baggage_meal_details['Baggage']) == true && valid_array($baggage_meal_details['Baggage']) == true)
		OR (isset($baggage_meal_details['Meals']) == true && valid_array($baggage_meal_details['Meals']) == true)
		OR (isset($baggage_meal_details['Seat']) == true && valid_array($baggage_meal_details['Seat']) == true)) {
			
		$Baggage = @$baggage_meal_details['Baggage'];
		$Meals = @$baggage_meal_details['Meals'];
		$Seat = @$baggage_meal_details['Seat'];
		$origins_arr = array();
		$seg_count = array();
    	foreach($pre_booking_summery['SegmentDetails'] as $seg_key => $sgment_details){
    		foreach($sgment_details as $seg_key1 => $seg_data){
    			$origins_arr[$seg_key]['origins'][$seg_key1] = $seg_data['OriginDetails']['AirportCode'];
    		}
    		$seg_count[$sgment_details[0]['OriginDetails']['AirportCode']] = count($sgment_details);
    	}
?>
		
<div class="moreflt boksectn">
   <div class="ontyp">
      <div class="labltowr arimobold">Service Requests (Optional)</div>
	<div class="baggage_meal_details">
	
	<ul class="nav nav-tabs extra_services_indicator_tab" role="tablist">
		<?php if(valid_array($Baggage) == true){ ?>
	    	<li role="presentation">
	    		<a class="btn btn-sm btn-default" href="#extra_services_tab_baggage" aria-controls="home" role="tab" data-toggle="tab">
	    			<img style="height: 19px; margin-right: 5px;" src="<?php echo $GLOBALS['CI']->template->template_images('baggage_icon.png'); ?>" alt=""/>Add Baggage +
	    		</a>
	    	</li>
	    <?php }
	    if(valid_array($Meals) == true){ ?>
	    	<li role="presentation">
	    		<a class="btn btn-sm btn-default" href="#extra_services_tab_meal" aria-controls="profile" role="tab" data-toggle="tab">
	    			<img style="height: 19px; margin-right: 5px;" src="<?php echo $GLOBALS['CI']->template->template_images('meal_icon.png'); ?>" alt=""/>Add Meal +
	    		</a>
	    	</li>
	    <?php } ?>
	    <?php
	    if(valid_array($Seat) == true && !empty($Seat[0])){ ?>
	    	<li role="presentation">
	    		<a class="btn btn-sm btn-default" href="#extra_services_tab_seat" aria-controls="profile" role="tab" data-toggle="tab">
	    			<img style="height: 19px; margin-right: 5px;" src="<?php echo $GLOBALS['CI']->template->template_images('seat_icon.png'); ?>" alt=""/>Seat Selection
	    		</a>
	    	</li>
	    <?php } ?>
  	</ul>
	<div class="tab-content">
		<!-- Baggage Starts -->
		<div role="tabpanel" class="pasngrinput tab-pane" id="extra_services_tab_baggage">
		<?php
			//Baggage
			if(valid_array($Baggage) == true){ ?>
					<div class="col-xs-12 nopad "><!-- Baggage div starts -->
					<div style="font-size: 15px; color: #666;">Choose Extra Baggage</div>
					<div class="col-xs-2 nopad">
					
					<div class="pt30"></div>
					<?php
							for($ex_pax_index=1; $ex_pax_index <= $total_pax_count; $ex_pax_index++) {//START FOR LOOP FOR PAX DETAILS
								$pax_type = pax_type($ex_pax_index, $total_adult_count, $total_child_count, $total_infant_count);
								$pax_type_count = pax_type_count($ex_pax_index, $total_adult_count, $total_child_count, $total_infant_count);
								if($pax_type != 'infant'){ ?>
									
										<div class="bag_pax_name"><?=ucfirst($pax_type)?> <?=($pax_type_count)?></div>
									
							<?php }
							}
					?>
					</div>
					
					<div class="addbaggage nopad">
						<?php
						// debug($search_data);exit;
							$bag_input_counter = 0;
							foreach ($Baggage as $bag_ok => $bag_ov){
								$bag_class = "hide";
								if($bag_ov[0]['Origin'] == $search_data['from'] || $bag_ov[0]['Origin'] == $search_data['to']){
									$bag_class = '';
								}
							 ?>
								<div class="addtlbox col-xs-4 padfive <?php echo $bag_class; ?>">
								<?php
								for($ex_pax_index=1; $ex_pax_index <= $total_pax_count; $ex_pax_index++) {//START FOR LOOP FOR PAX DETAILS
									$pax_type = pax_type($ex_pax_index, $total_adult_count, $total_child_count, $total_infant_count);
									$pax_type_count = pax_type_count($ex_pax_index, $total_adult_count, $total_child_count, $total_infant_count);
									if($pax_type != 'infant'){
									
										if($ex_pax_index == 1){
											$baggage_label =  $bag_ov[0]['Origin'].' <i class="fa fa-long-arrow-right" aria-hidden="true"></i> '.$bag_ov[0]['Destination'];
											if($bag_ov[0]['Origin'] == $search_data['from'] ){
												$baggage_label =  $bag_ov[0]['Origin'].' <i class="fa fa-long-arrow-right" aria-hidden="true"></i> '.$search_data['to'];
											}
											if($bag_ov[0]['Origin'] == $search_data['to']){
												$baggage_label =  $bag_ov[0]['Origin'].' <i class="fa fa-long-arrow-right" aria-hidden="true"></i> '.$search_data['from'];
											}
											foreach($origins_arr as $org_key => $org_value){
												if(in_array($bag_ov[0]['Origin'], $org_value['origins'])){
													$bagge_selection_key = $org_key;
												}
											}
											$seg_c = $seg_count[$bag_ov[0]['Origin']];
										
											?>
											<span class="formlabel"> <?=$baggage_label?></span>
								<?php 	}

								 ?>
									<div class="col-xs-12 nopad spllty">
										<div class="selectedwrap">
											<select name="baggage_<?=$bag_input_counter?>[]" id="baggage_<?=$bag_input_counter?>" data-bag-inc="<?=$bag_input_counter?>" data-seg-count= "<?php echo $seg_c; ?>" class="add_extra_service choosen_baggage mySelectBoxClass flyinputsnor">
												<option value="">Baggage</option>
												<?php foreach($bag_ov as $bag_k => $bag_v){ ?>
													<option data-choose-val= "<?=$bag_k;?>" data-choosen-baggage-price="<?=round($bag_v['Price'])?>" value="<?=$bag_v['BaggageId']?>"><?=$bag_v['Weight'].' - '.round($bag_v['Price']).' '.get_application_currency_preference()?></option>
												<?php }
												?>
											</select>
										</div> 
									</div>
								<?php }//not infant condition ends
						
								}//end pax loop
							?>
							<?php
								$bag_input_counter++;
							?>
							</div>
							<?php } //baggage loop ends
							?>
					</div>
				</div><!-- Baggage div ends -->
			<?php }//End of Bagage ?>
			
    	</div>
    	<!-- Baggage Ends -->
    	<?php 
    	
    	// debug($origins_arr);exit;
    	?>
    	<!-- Meal Starts -->
    	<div role="tabpanel" class="tab-pane pasngrinput" id="extra_services_tab_meal">
    		<?php
				//Meals
				if(valid_array($Meals) == true){ ?>
				<div class="col-xs-12 nopad"><!-- Meal div starts -->
					<div style="font-size: 15px; color: #666;">Choose Your Meal</div>
					<div class="col-xs-2 nopad">
					
					<div class="pt30"></div>
					<?php
							for($ex_pax_index=1; $ex_pax_index <= $total_pax_count; $ex_pax_index++) {//START FOR LOOP FOR PAX DETAILS
								$pax_type = pax_type($ex_pax_index, $total_adult_count, $total_child_count, $total_infant_count);
								$pax_type_count = pax_type_count($ex_pax_index, $total_adult_count, $total_child_count, $total_infant_count);
								if($pax_type != 'infant'){ ?>
									
										<div class="meal_pax_name"><?=ucfirst($pax_type)?> <?=($pax_type_count)?></div>
									
							<?php }
							}
					?>
					</div>
					<div class="col-xs-10 addbaggage nopad">
					<?php
					$meal_input_counter = 0;
					foreach ($Meals as $meal_ok => $meal_ov){ ?>
						<div class="col-xs-4 padfive">
					<?php
						for($ex_pax_index=1; $ex_pax_index <= $total_pax_count; $ex_pax_index++) {//START FOR LOOP FOR PAX DETAILS
							$pax_type = pax_type($ex_pax_index, $total_adult_count, $total_child_count, $total_infant_count);
							$pax_type_count = pax_type_count($ex_pax_index, $total_adult_count, $total_child_count, $total_infant_count);
							if($pax_type != 'infant'){
							
								if($ex_pax_index == 1){
									$meal_label =  $meal_ov[0]['Origin'].' <i class="fa fa-long-arrow-right" aria-hidden="true"></i> '.$meal_ov[0]['Destination'];
									?>
									<span class="formlabel"> <?=$meal_label?></span>
						<?php 	} ?>
					
						<div class="col-xs-12 nopad spllty">
							<div class="selectedwrap">
								<select name="meal_<?=$meal_input_counter?>[]" class="add_extra_service choosen_meal mySelectBoxClass flyinputsnor">
									<option value="">Meal</option>
									<?php foreach($meal_ov as $meal_k => $meal_v){ ?>
										<option data-choosen-meal-price="<?=round($meal_v['Price'])?>" value="<?=$meal_v['MealId']?>"><?=$meal_v['Description'].' - '.round($meal_v['Price']).' '.get_application_currency_preference()?></option>
									<?php }
									?>
								</select>
							</div>
						</div>
						<?php }//not infant condition ends
				
						}//end pax loop
					$meal_input_counter++;
					?>
					</div>
					<?php } ?>
						</div>
						
				</div>
				<?php }//End of Meals
				?>
    	</div>
    	<!-- Meal Ends -->
    	
    	
    	<!-- Seat Starts -->
		<div role="tabpanel" class="tab-pane pasngrinput" id="extra_services_tab_seat">
			<?php
			//Seat
			if(valid_array($Seat) == true){
	    		$seat_map_data['seat_data'] = $Seat;
	    		$seat_map_data['total_adult_count'] = $total_adult_count;
				$seat_map_data['total_child_count'] = $total_child_count;
				$seat_map_data['total_infant_count'] = $total_infant_count;
				$seat_map_data['total_pax_count'] = $total_pax_count;
	    		//echo $GLOBALS['CI']->template->isolated_view('flight/seat_map', $seat_map_data);
	    		if($booking_source == TRAVELPORT_GDS_BOOKING_SOURCE){
					echo $GLOBALS['CI']->template->isolated_view('flight/seat_map_tp', $seat_map_data);
				}else{
					echo $GLOBALS['CI']->template->isolated_view('flight/seat_map', $seat_map_data);
				}
	    	} ?>
    	</div>
    	<!-- Seat Ends -->
    	
  	</div>
</div>

</div>
</div>
<?php } ?>
