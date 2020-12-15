<!-- SEAT SELECTION -->
<div>
      <div class="seat-summary"> </div>
      <div class="clearfix"></div>
      <div class="seatmapicon">
            
               	<div class="rowicon">
                     <span><img src="<?=SYSTEM_IMAGE_DIR?>available.png"></span>
                     <span>Available</span>
                  </div>
                  <div class="rowicon">
                     <span><img src="<?=SYSTEM_IMAGE_DIR?>blocked.png"></span>
                     <span>Selected</span>
                  </div>
                  <div class="rowicon">
                     <span><img src="<?=SYSTEM_IMAGE_DIR?>occupied.png"></span>
                     <span>Occupied</span>
                  </div>
                  <div class="rowicon">
                     <span><img src="<?=SYSTEM_IMAGE_DIR?>not_exist.png"></span>
                     <span>Not Exist</span>
                  </div>
              
         </div>
      <div class="clearfix"></div>
      <div class="col-md-7 col-xs-12 nopad">
         <ul class="nav nav-tabs flight-tab">
         <?php foreach ($seat_data as $seg_seat_k => $seg_seat_v){?>
         		<?php
         		$flight_segment_label = $seg_seat_v[0][0]['Origin'].'-'.$seg_seat_v[0][0]['Destination'].'('.$seg_seat_v[0][0]['AirlineCode'].','.$seg_seat_v[0][0]['FlightNumber'].')';
         		if($seg_seat_k == 0){
		         	$active_tab_fade_cls = 'active';
		        } else {
		         	$active_tab_fade_cls = '';
		        }
         		?>
		         <li class="<?=$active_tab_fade_cls?>">
		         	<a href="#seat_map<?=$seg_seat_k?>" data-toggle="tab" class="seat_segment_map <?=$active_tab_fade_cls?>"><?=$flight_segment_label?></a>
		         </li>
         <?php } ?>
            
         </ul>
         <div class="tab-content">
         
         <?php foreach ($seat_data as $seg_seat_k => $seg_seat_v){ ?>
		         <?php
		         	if($seg_seat_k == 0){
		         		$active_tab_fade_cls = 'active in';
		         	} else {
		         		$active_tab_fade_cls = '';
		         	}
		         ?>
		         <!-- Flight Seat Map Starts -->
		            <div id="seat_map<?=$seg_seat_k?>" class="tab-pane fade <?=$active_tab_fade_cls?>">
		               <div class="flight_d">
		                  <div class="flight-mw">
		                     <div class="flight-con">
		                        <table class="table table-striped">
		                           <tbody>
		                              <tr></tr>
		                              <tr class="difbgble">
		                                 <td></td>
		                                 <td>A</td>
		                                 <td>B</td>
		                                 <td>C</td>
		                                 <td> </td>
		                                 <td>D</td>
		                                 <td>E</td>
		                                 <td>F</td>
		                              </tr>
		                              <?php foreach($seg_seat_v as $seat_row_k => $seat_row_v){?>
		                              		
		                              			<tr>
		                              			<td><?=$seat_row_v[0]['RowNumber']?></td>
		                              			<?php
		                              				foreach ($seat_row_v as $seat_index => $seat_value) {?>
			                              				<?php
			                              					if(intval($seat_value['AvailablityType']) === 1){
			                              						$seat_image =SYSTEM_IMAGE_DIR.'available.png';
			                              						
			                              						$seat_availability_class = ' choose_seat ';
			                              						
			                              						$seat_data_attributes = '	title ="'.$seat_value['SeatNumber'].','.$seat_value['Price'].'"
						                                 									data-seat_number="'.$seat_value['SeatNumber'].'"
						                                 									data-seat_price="'.$seat_value['Price'].'"
						                                 									data-seat_id="'.$seat_value['SeatId'].'" ';
			                              					} else {
			                              						$seat_image =SYSTEM_IMAGE_DIR.'occupied.png';
			                              						$seat_availability_class = '';
			                              						$seat_data_attributes = '';
			                              					}
			                              				?>
						                                 <td>
						                                 	<img class="<?=$seat_availability_class?>" src="<?=$seat_image?>" data-toggle="tooltip" <?=$seat_data_attributes?>>
						                                 </td>
						                                 <?php if($seat_index == 2){ ?>
						                                 	<td width="20px"></td>
						                                 <?php }?>
		                              			<?php } ?>
		                              			
				                              </tr>
		                              <?php } ?>
		                              
		                           </tbody>
		                        </table>
		                     </div>
		                  </div>
		               </div>
		            </div>
		            <!-- Flight Seat Map Ends -->
            
            <?php }?>
            
         </div>
      </div>
      <!-- Showing Selected Seats and Price Details -- STARTS -->
      <div class="col-md-5 col-xs-12 nopad">
         <div class="table-responsive">
            <table width="100%">
               <tbody>
               <?php foreach ($seat_data as $seg_seat_k => $seg_seat_v){ ?>
	         		<?php
	         		$flight_segment_label = $seg_seat_v[0][0]['Origin'].'-'.$seg_seat_v[0][0]['Destination'].'('.$seg_seat_v[0][0]['AirlineCode'].','.$seg_seat_v[0][0]['FlightNumber'].')';
	         		
	         		?>
	                  <tr>
	                     <td>
	                        <table class="table table-bordered seat_pax_details seat_segment_pax">
	                           <tbody>
	                              <tr class="">
	                                 <th class="seat_segment_pax_label" colspan="3"><?=$flight_segment_label?></th>
	                              </tr>
	                              <tr class="nethed">
	                              	<th>Pax</th>
	                              	<th>Seat</th>
	                              	<th>Price</th>
	                              </tr>
	                              <?php
										for($ex_seat_pax_index=1; $ex_seat_pax_index <= $total_pax_count; $ex_seat_pax_index++) {
											$pax_type = pax_type($ex_seat_pax_index, $total_adult_count, $total_child_count, $total_infant_count);
											$pax_type_count = pax_type_count($ex_seat_pax_index, $total_adult_count, $total_child_count, $total_infant_count);
											if($pax_type != 'infant'){ ?>
												
													<tr class="seat_segment_pax_tr">
														<input type="hidden" name="seat_<?=$seg_seat_k?>[]" class="choosen_seat" data-seat_price="">
						                                 <td class="seat_pax_name"><?=ucfirst($pax_type)?> <?=($pax_type_count)?></td>
						                                 <td class="seat_pax_number"></td>
						                                 <td class="seat_pax_price"></td>
						                             </tr>
												
										<?php }
										}
								?>
	                           </tbody>
	                        </table>
	                     </td>
	                  </tr>
                  <?php } ?>
                  
               </tbody>
            </table>
         </div>
         
      </div>
      <!-- Showing Selected Seats and Price Details -- ENDS -->
   <div class="mybtnc">
   		<button name="flight" type="submit" class="btn btn-lg btn-warning continue_booking_button">Continue</button>
   	</div>
</div>
<script>
var system_image_dir_url = '<?=SYSTEM_IMAGE_DIR?>';
</script>