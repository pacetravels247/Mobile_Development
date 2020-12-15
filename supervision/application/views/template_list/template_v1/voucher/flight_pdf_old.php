<style>
th,td{padding:5px;}
</style>

<?php
$booking_details = $data ['booking_details'] [0];
//debug($booking_details);exit;
$itinerary_details = $booking_details ['booking_itinerary_details'];

$attributes = $booking_details ['attributes'];
//debug($itinerary_details);exit;
$customer_details = $booking_details ['booking_transaction_details'] [0] ['booking_customer_details'];
$domain_details = $booking_details;
$lead_pax_details = $customer_details;
$booking_transaction_details = $booking_details ['booking_transaction_details'];
//debug($booking_transaction_details);exit;
$adult_count = 0;
$infant_count = 0;
//debug($customer_details);exit;
foreach ($customer_details as $k => $v) {
	if (strtolower($v['passenger_type']) == 'infant') {
		$infant_count++;
	} else {
		$adult_count++;
	}
}

                            
                        
		                              $Onward = '';
		                              $return = '';
		                    if(count($booking_transaction_details) == 2) {
		                                 $Onward = '(Onward)';                             
		                                 $Return =  '(Return)';		                             
	                               }       
                           
	                        //generate onword and return        
                           if($booking_details['is_domestic'] == true && count($booking_transaction_details) == 2)
                           {
			                              $onward_segment_details = array();
			                           	  $return_segment_details = array();
			                           	  $segment_indicator_arr = array();
			                           	  $segment_indicator_sort= array();
			                           	  
			                           	  foreach ($itinerary_details as $key => $key_sort_data) {
			                           	  	$segment_indicator_sort[$key]  = $key_sort_data['origin'];
			                           	  	
			                           	  }
			                               array_multisort($segment_indicator_sort, SORT_ASC,$itinerary_details);
			                           	   //debug($itinerary_details);exit;
			                           	  
			                           	 foreach($itinerary_details as $k=>$sub_details ){
			                           	 	    $segment_indicator_arr[] = $sub_details['segment_indicator'];
			                           	 	    $count_value = array_count_values($segment_indicator_arr);
			                           	 		
			                           	 		if($count_value[1] == 1){
			                           	 	      $onward_segment_details[] =$sub_details;
			                           	 		}else{
			                           	 			$return_segment_details[] = $sub_details;
			                           	 		}
			                           	 	}
			                           	   
			                           	}
                           	
                                              
			                    ?>
			                    
<table style="border-collapse: collapse; background: #ffffff;font-size: 12pt; margin: 0 auto; font-family: arial;" width="100%" cellpadding="0" cellspacing="0" border="0">
<tbody>
	<tr> 
	  <td style="border-collapse: collapse; padding:10px 20px 20px" >
	      <table width="100%" style="border-collapse: collapse;" cellpadding="0" cellspacing="0" border="0">
	      	<tr><td style="font-size:15pt; line-height:30px; width:100%; display:block; font-weight:600; text-align:center">E-Ticket<?php echo $Onward;?></td></tr>
	       <tr>
					<td>
			       <table width="100%" style="border-collapse: collapse;" cellpadding="0" cellspacing="0" border="0">
			       <tr>
					<td style="padding: 10px; width:65%"><img width="150px;" src="<?=$GLOBALS['CI']->template->domain_images($data['logo'])?>"></td>
					<td style="padding: 10px; width:35%">
                    	<table width="100%" style="border-collapse: collapse;text-align: right; line-height:15px;" cellpadding="0" cellspacing="0" border="0">
                    		
                    		<tr><td style="font-size:12pt;"><span style="width:100%; float:left"><?php echo $data['address'];?></span>
                    		</td></tr>
                         </table></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
			 <td style="padding: 10px;">
			  <table cellpadding="5" cellspacing="0" border="0" width="100%" style="border-collapse: collapse;">
							<tr>
								<td width="100%" style="padding: 10px;border: 1px solid #cccccc; font-size: 11pt; font-weight: bold;">Reservation Lookup</td>
								
							</tr>
							<tr>
								<td style="border: 1px solid #cccccc;">
									<table width="100%" cellpadding="5" style="padding: 10px;font-size: 11pt;">
										<tr>
											<td><strong>Booking Reference</strong></td>
											<td><strong>Booking ID</strong></td>
											<td><strong>PNR</strong></td>
											<td><strong>Booking Date</strong></td>
											<td><strong>Status</strong></td>
										</tr>
										<tr>
										 
											<td><?=@$booking_details['app_reference']?></td>
											<td><?=@$booking_transaction_details[0]['book_id']?></td>
											<td><?=@$booking_transaction_details[0]['pnr']?></td>
											<td><?=app_friendly_absolute_date(@$booking_details['booked_date'])?></td>
											<td><strong class="<?php echo booking_status_label( $booking_transaction_details[0]['status']);?>" style=" font-size:11pt;">
											<?php 
											switch($booking_transaction_details[0]['status']){
												case 'BOOKING_CONFIRMED': echo 'CONFIRMED';break;
												case 'BOOKING_CANCELLED': echo 'CANCELLED';break;
												case 'BOOKING_FAILED': echo 'FAILED';break;
												case 'BOOKING_INPROGRESS': echo 'INPROGRESS';break;
												case 'BOOKING_INCOMPLETE': echo 'INCOMPLETE';break;
												case 'BOOKING_HOLD': echo 'HOLD';break;
												case 'BOOKING_PENDING': echo 'PENDING';break;
												case 'BOOKING_ERROR': echo 'ERROR';break;
												
											}
											
																				
											?>
											</strong></td>
										</tr>
									</table>
								</td>
							</tr>
							 <tr><td>&nbsp;</td></tr>
							<tr>
								<td style="padding: 10px;border: 1px solid #cccccc; font-size: 11pt; font-weight: bold;">Journey Information</td>
								
							</tr>
							<tr>
								<td  width="100%" style="border: 1px solid #cccccc;">
									<table width="100%" cellpadding="5" style="padding: 10px;font-size: 11pt;">
										<tr>
											<td><strong>Flight</strong></td>
											<td><strong>Departure</strong></td>
											<td><strong>Arrival</strong></td>
											<td><strong>Journey Time</strong></td>
										</tr>
										<?php 
						if(isset($booking_transaction_details) && $booking_transaction_details != ""){
                          
							if($booking_details['is_domestic'] == true && count($booking_transaction_details) == 2){
								$itinerary_details = array();
								$itinerary_details = $onward_segment_details;
							}
								foreach($itinerary_details as $segment_details_k => $segment_details_v){
                              $airline_craft = '';
                              $itinerary_details_attributes = json_decode ( $segment_details_v ['attributes'], true);
							$airline_terminal_origin = @$itinerary_details_attributes['departure_terminal'];
							$airline_terminal_destination = @$itinerary_details_attributes['arrival_terminal'];
                              $origin_terminal='';
                              $destination_terminal='';
                              if($airline_terminal_origin != ''){
                              	$origin_terminal = 'Terminal '.$airline_terminal_origin;
                              }
                              if($airline_terminal_destination != ''){
                              	$destination_terminal = 'Terminal '.$airline_terminal_destination;
                              }
                              
                              if(valid_array($segment_details_v) == true) { ?>
                                     <tr>
                               
									 <td><img width="30px;" src="<?=SYSTEM_IMAGE_DIR.'airline_logo/'.$segment_details_v['airline_code'].'.gif'?>" alt="flight-logo" /><br/>
                                        <span style="width:100%; float:left"><?=@$segment_details_v['airline_name']?></span>
                                        <span style="width:100%; float:left; font-size:13px; font-weight:bold"><?php echo $segment_details_v['airline_code'].' '.$segment_details_v['flight_number'];?></span></td>
                                        <td style="line-height:16px; text-align: left"><span width="100%" style="width:100%; float:left; font-size:13px; font-weight:bold"><?=@$segment_details_v['from_airport_name'] ?>(<?=@$segment_details_v['from_airport_code']?>)</span><br/>
                                        <span width="100%" style="width:100%; float:left"><?php echo $origin_terminal;?></span><br/>
                                        <span width="100%" style="width:100%; float:left; font-weight:bold"><?php echo date("d M Y",strtotime($segment_details_v['departure_datetime'])).", ".date("H:i",strtotime($segment_details_v['departure_datetime']));?></span></td>
                                        <td style="line-height:16px;text-align: left"><span style="width:100%; float:left; font-size:13px; font-weight:bold"><?=@$segment_details_v['to_airport_name']?>(<?=@$segment_details_v['to_airport_code']?>)</span><br/>
                                        <span style="width:100%; float:left"><?php echo $destination_terminal;?></span><br/>
                                        <span style="width:100%; float:left; font-weight:bold"><?php echo date("d M Y",strtotime($segment_details_v['arrival_datetime'])).", ".date("H:i",strtotime($segment_details_v['arrival_datetime']));?></span></td>
                                        <td><!-- <span style="width:100%; float:left">Non-Stop</span> -->
                                        <span style="width:100%; float:left"><?php echo $segment_details_v['total_duration'];?></span>
                                        </td>
										</tr>
									 <?php }
                              }
                              }?>		
									</table>
									</td>
							</tr>
							   <tr>
                            <td>&nbsp;</td></tr>
							<tr>
								<td style="padding: 10px;border: 1px solid #cccccc; font-size: 11pt; font-weight: bold;">Travelers Information</td>
								
							</tr>
							<tr>
								<td style="border: 1px solid #cccccc;">
									<table width="100%" cellpadding="5" style="padding: 10px;font-size: 11pt;">
									    <tr>
											<td><strong>Passenger Name</strong></td>
											<td><strong>Ticket No</strong></td>
											<td><strong>Status</strong></td>
										</tr>
										 <?php 
									
									    $booking_transaction_details_value = $booking_transaction_details[0];
									
									 	if(isset($booking_transaction_details_value['booking_customer_details'])){
									 		foreach($booking_transaction_details_value['booking_customer_details'] as $cus_k => $cus_v){
									 	
									 ?>
									 <tr>
										<?php if (strtolower($cus_v['passenger_type']) == 'infant') { ?>
		                                   <td><?php echo $cus_v['first_name'].'  '.$cus_v['last_name'];?>(Infant)</td>
		                                 <?php }else{?>
		                                 <td><?php echo $cus_v['title'].'.'.$cus_v['first_name'].'  '.$cus_v['last_name'];?></td>
		                                 <?php } ?>
										 <td><?=@$cus_v['TicketNumber'];?></td>
										<td>
										<strong class="<?php echo booking_status_label($cus_v['status'])?>">
										<?php 
										switch($cus_v['status']){
												case 'BOOKING_CONFIRMED': echo 'CONFIRMED';break;
												case 'BOOKING_CANCELLED': echo 'CANCELLED';break;
												case 'BOOKING_FAILED': echo 'FAILED';break;
												case 'BOOKING_INPROGRESS': echo 'INPROGRESS';break;
												case 'BOOKING_INCOMPLETE': echo 'INCOMPLETE';break;
												case 'BOOKING_HOLD': echo 'HOLD';break;
												case 'BOOKING_PENDING': echo 'PENDING';break;
												case 'BOOKING_ERROR': echo 'ERROR';break;
												
											}
											?>
										</strong>
										</td>
										</tr>
										<?php } }?>
									</table>
						       </td>
					 </tr>
					  <tr><td>&nbsp;</td></tr>
					  			<!-- Extra Service Starts -->
								<?php if(isset($booking_transaction_details_value['extra_service_details']['baggage_details']) == true && valid_array($booking_transaction_details_value['extra_service_details']['baggage_details']) == true){
											$baggage_details = $booking_transaction_details_value['extra_service_details']['baggage_details'];
									?>
									<tr>
										<td style="padding: 10px; border: 1px solid #cccccc; font-size: 14px; font-weight: bold;">
											Extra Baggage Information
										</td>
									</tr>

									<tr>
										<td>
											<table style="width:100%;">
												<tr>
													<th style="border:1px solid #ddd; padding:5px; font-size: 14px;">Passenger</th>
													<?php foreach ($baggage_details['baggage_source_destination_label'] as $bag_lk => $bag_lv){ ?>
															<th style="border:1px solid #ddd; padding:5px; font-size: 14px;">
																<?=$bag_lv?>
															</th>
													<?php } ?>
												</tr>
													<?php
														
														foreach ($baggage_details['details'] as $bag_k => $bag_v){?>
															<tr>
															<td style="border:1px solid #ddd; padding:5px; font-size: 14px;"><?=$bag_v[0]['pax_name']?></td>
																<?php
																foreach ($bag_v as $bd_k => $bd_v){ ?>
																		<td style="border:1px solid #ddd; padding:5px; font-size: 14px;">
																			<?=$bd_v['description']?><small>(<?=$booking_details['currency'].' '.$bd_v['price']?>)</small>
																		</td>
																	
																<?php }?>
															</tr>
													<?php } ?>
											</table>
										</td>
									</tr>
									
									<tr>
										<td>&nbsp;</td>
									</tr>
								<?php }?>
								<?php if(isset($booking_transaction_details_value['extra_service_details']['meal_details']) == true && valid_array($booking_transaction_details_value['extra_service_details']['meal_details']) == true){
											$meal_details = $booking_transaction_details_value['extra_service_details']['meal_details'];
											$meal_type = end($meal_details['details']);
											
											$meal_type = $meal_type[0]['type'];
											if($meal_type == 'static'){
												$meal_type_label = 'Meal Preference';
											} else{
												$meal_type_label = 'Meal Information';
											}
									?>
									<tr>
										<td style="padding: 10px; border: 1px solid #cccccc; font-size: 14px; font-weight: bold;">
											<?=$meal_type_label?>
										</td>
									</tr>
									
									<tr>	
										<td>
											<table style="width: 100%;">
												<tr>
													<th style="border:1px solid #ddd; padding:5px; font-size: 14px;">Passenger</th>
													<?php foreach ($meal_details['meal_source_destination_label'] as $meal_lk => $meal_lv){ ?>
															<th style="border:1px solid #ddd; padding:5px; font-size: 14px;">
																<?=$meal_lv?>
															</th>
													<?php } ?>
												</tr>
													<?php
														
														foreach ($meal_details['details'] as $meal_k => $meal_v){?>
															<tr>
															<td style="border:1px solid #ddd; padding:5px; font-size: 14px;"><?=$meal_v[0]['pax_name']?></td>
																<?php
																foreach ($meal_v as $md_k => $md_v){ ?>
																		<td style="border:1px solid #ddd; padding:5px; font-size: 14px;">
																			<?=$md_v['description']?><small>(<?=$booking_details['currency'].' '.$md_v['price']?>)</small>
																		</td>
																	
																<?php }?>
															</tr>
													<?php } ?>
											</table>
										</td>
									</tr>
									
									<tr>
										<td>&nbsp;</td>
									</tr>
								<?php }?>
								<?php if(isset($booking_transaction_details_value['extra_service_details']['seat_details']) == true && valid_array($booking_transaction_details_value['extra_service_details']['seat_details']) == true){
											$seat_details = $booking_transaction_details_value['extra_service_details']['seat_details'];
											$seat_type = end($seat_details['details']);
											$seat_type =  $seat_type[0]['type'];
											if($seat_type == 'static'){
												$seat_type_label = 'Seat Preference';
											} else{
												$seat_type_label = 'Seat Information';
											}
									?>
									<tr>
										<td style="padding: 10px; border: 1px solid #cccccc; font-size: 14px; font-weight: bold;">
											<?=$seat_type_label?>
										</td>
									</tr>
									
									<tr>
										<td>
											<table style="width: 100%;">
												<tr>
													<th style="border:1px solid #ddd; padding:5px; font-size: 14px;">Passenger</th>
													<?php foreach ($seat_details['seat_source_destination_label'] as $seat_lk => $seat_lv){ ?>
															<th style="border:1px solid #ddd; padding:5px; font-size: 14px;">
																<?=$seat_lv?>
															</th>
													<?php } ?>
												</tr>
													<?php
														
														foreach ($seat_details['details'] as $seat_k => $seat_v){?>
															<tr>
															<td style="border:1px solid #ddd; padding:5px; font-size: 14px;"><?=$seat_v[0]['pax_name']?></td>
																<?php
																foreach ($seat_v as $sd_k => $sd_v){ 
																	$seat_description = trim($sd_v['description']);
																	if(empty($seat_description) == true){
																		$seat_description = trim($sd_v['code']);
																	}
																	?>
																		<td style="border:1px solid #ddd; padding:5px; font-size: 14px;">
																			<?=$seat_description?> <small>(<?=$booking_details['currency'].' '.$sd_v['price']?>)</small>
																		</td>
																	
																<?php }?>
															</tr>
													<?php } ?>
											</table>
										</td>
									</tr>
									
									<tr>
										<td>&nbsp;</td>
									</tr>
								<?php }?>
								<!-- Extra Service Ends -->
					  <?php  if(count($booking_transaction_details) == 1) { ?>
					  <tr>
								<td style="padding: 10px;border: 1px solid #cccccc; font-size: 11pt; font-weight: bold;">Price Summary</td>
								
							</tr>
						<tr>
								<td style="border: 1px solid #cccccc;">
									<table width="100%" cellpadding="5" style="padding: 10px;font-size: 11pt;">
									<tr>
											<td><strong>Total Fare</strong></td>
										</tr>
									 <tr>
											<td><?=@$booking_details['currency']?>  <?=@$booking_details['grand_total']?></td>
										</tr>
											
									</table>
								 </td>
							</tr>
							<?php } ?>
						<tr><td>&nbsp;</td></tr>
							<tr>
								<td width="100%" style="padding: 10px;border: 1px solid #cccccc; font-size: 11pt; font-weight: bold;">Terms and Conditions</td>
							</tr>
					  <tr>
								<td  width="100%" style="border: 1px solid #cccccc;">
									<table width="100%" cellpadding="5" style="padding: 10px 20px;font-size: 11pt;">
										<tr>
                                        <td>1.A printed copy of this e-ticket display on laptop,lablet or phone mast be presented at a time of checking.</td>
										</tr>
										<tr>
                                        <td>2.Check-in starts 2 hours before scheduled departure, and closes 60 minutes prior to departure time.We recommend you report at check-in counter
                                        at least 2 hours prior to departure time.
                                        </td>
										</tr>
										<tr>
                                        <td>3.It is mandatory to carry Government recognised photo identification along with your E-ticket.This can
                                        be include:Driving License,Passport,Pan Card,Voter Id Card or any other ID issued by Government of India.
                                        for infant passengers it is mandatory to carry the Date Of Birth certificate.
                                       </td>
										</tr>
									</table>
								</td>
							</tr>								
			  </table>				
			 </td>
			</tr> 
	      </table>
	 </td>
	</tr>  
</tbody>
</table>

<?php if(count($booking_transaction_details) == 2) {?>

<table style="border-collapse: collapse; background: #ffffff;font-size: 12pt; margin: 0 auto; font-family: arial;" width="100%" cellpadding="0" cellspacing="0" border="0">
<tbody>
	<tr> 
	  <td style="border-collapse: collapse; padding:10px 20px 20px" >
	      <table width="100%" style="border-collapse: collapse;" cellpadding="0" cellspacing="0" border="0">
	      	<tr><td style="font-size:15pt; line-height:30px; width:100%; display:block; font-weight:600; text-align:center">E-Ticket<?php echo $Return;?></td></tr>
	       <tr>
					<td>
			       <table width="100%" style="border-collapse: collapse;" cellpadding="0" cellspacing="0" border="0">
			       <tr>
					<td style="padding: 10px; width:65%"></td>
					<td style="padding: 10px; width:35%">
                    	<table width="100%" style="border-collapse: collapse;text-align: right; line-height:15px;" cellpadding="0" cellspacing="0" border="0">
                    		
                    		<tr><td style="font-size:12pt;"><span style="width:100%; float:left"><?php echo $data['address'];?></span>
                    		</td></tr>
                         </table></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
			 <td style="padding: 10px;">
			  <table cellpadding="5" cellspacing="0" border="0" width="100%" style="border-collapse: collapse;">
							<tr>
								<td width="100%" style="padding: 10px;border: 1px solid #cccccc; font-size: 11pt; font-weight: bold;">Reservation Lookup</td>
								
							</tr>
							<tr>
								<td style="border: 1px solid #cccccc;">
									<table width="100%" cellpadding="5" style="padding: 10px;font-size: 11pt;">
										<tr>
											<td><strong>Booking Reference</strong></td>
											<td><strong>Booking ID</strong></td>
											<td><strong>PNR</strong></td>
											<td><strong>Booking Date</strong></td>
											<td><strong>Status</strong></td>
										</tr>
										<tr>
										 
											<td><?=@$booking_details['app_reference']?></td>
											<td><?=@$booking_transaction_details[1]['book_id']?></td>
											<td><?=@$booking_transaction_details[1]['pnr']?></td>
											<td><?=app_friendly_absolute_date(@$booking_details['booked_date'])?></td>
											<td><strong class="<?php echo booking_status_label( $booking_transaction_details[1]['status']);?>" style=" font-size:14px;color:#28b700">
											<?php 
											switch($booking_transaction_details[1]['status']){
												case 'BOOKING_CONFIRMED': echo 'CONFIRMED';break;
												case 'BOOKING_CANCELLED': echo 'CANCELLED';break;
												case 'BOOKING_FAILED': echo 'FAILED';break;
												case 'BOOKING_INPROGRESS': echo 'INPROGRESS';break;
												case 'BOOKING_INCOMPLETE': echo 'INCOMPLETE';break;
												case 'BOOKING_HOLD': echo 'HOLD';break;
												case 'BOOKING_PENDING': echo 'PENDING';break;
												case 'BOOKING_ERROR': echo 'ERROR';break;
												
											}
											
																				
											?>
											</strong></td>
										</tr>
									</table>
								</td>
							</tr>
							 <tr><td>&nbsp;</td></tr>
							<tr>
								<td style="padding: 10px;border: 1px solid #cccccc; font-size: 11pt; font-weight: bold;">Journey Information</td>
								
							</tr>
							<tr>
								<td  width="100%" style="border: 1px solid #cccccc;">
									<table width="100%" cellpadding="5" style="padding: 10px;font-size: 11pt;">
										<tr>
											<td><strong>Flight</strong></td>
											<td><strong>Departure</strong></td>
											<td><strong>Arrival</strong></td>
											<td><strong>Journey Time</strong></td>
										</tr>
										<?php 
						if(isset($booking_transaction_details) && $booking_transaction_details != ""){
                          
							if($booking_details['is_domestic'] == true && count($booking_transaction_details) == 2){
								$itinerary_details = array();
								$itinerary_details = $onward_segment_details;
							}
								foreach($return_segment_details as $segment_details_k => $segment_details_v){
                              $airline_craft = '';
                              $itinerary_details_attributes = json_decode ( $segment_details_v ['attributes'], true);
							$airline_terminal_origin = @$itinerary_details_attributes['departure_terminal'];
							$airline_terminal_destination = @$itinerary_details_attributes['arrival_terminal'];
                              $origin_terminal='';
                              $destination_terminal='';
                              if($airline_terminal_origin != ''){
                              	$origin_terminal = 'Terminal '.$airline_terminal_origin;
                              }
                              if($airline_terminal_destination != ''){
                              	$destination_terminal = 'Terminal '.$airline_terminal_destination;
                              }
                              
                              if(valid_array($segment_details_v) == true) { ?>
                                     <tr>
                               
									 <td><img style="max-height:30px" src="<?=SYSTEM_IMAGE_DIR.'airline_logo/'.$segment_details_v['airline_code'].'.gif'?>" alt="flight-logo" />
                                       <br/>
                                        <span style="width:100%; float:left"><?=@$segment_details_v['airline_name']?></span>
                                        <span style="width:100%; float:left; font-size:13px; font-weight:bold"><?php echo $airline_craft;?></span></td>
                                        <td style="line-height:16px;text-align: left"><span style="width:100%; float:left; font-size:13px; font-weight:bold"><?=@$segment_details_v['from_airport_name'] ?>(<?=@$segment_details_v['from_airport_code']?>)</span><br/>
                                        <span style="width:100%; float:left"><?php echo $origin_terminal;?></span><br/>
                                        <span style="width:100%; float:left; font-weight:bold"><?php echo date("d M Y",strtotime($segment_details_v['departure_datetime'])).", ".date("H:i",strtotime($segment_details_v['departure_datetime']));?></span></td>
                                        <td style="line-height:16px;text-align: left"><span style="width:100%; float:left; font-size:13px; font-weight:bold"><?=@$segment_details_v['to_airport_name']?>(<?=@$segment_details_v['to_airport_code']?>)</span><br/>
                                        <span style="width:100%; float:left"><?php echo $destination_terminal;?></span><br/>
                                        <span style="width:100%; float:left; font-weight:bold"><?php echo date("d M Y",strtotime($segment_details_v['arrival_datetime'])).", ".date("H:i",strtotime($segment_details_v['arrival_datetime']));?></span></td>
                                        <td><!-- <span style="width:100%; float:left">Non-Stop</span> -->
                                        <span style="width:100%; float:left"><?php echo $segment_details_v['total_duration'];?></span>
                                        </td>
										</tr>
									 <?php }
                              }
                              }?>		
									</table>
									</td>
							</tr>
							   <tr>
                            <td>&nbsp;</td></tr>
							<tr>
								<td style="padding: 10px;border: 1px solid #cccccc; font-size: 11pt; font-weight: bold;">Travelers Information</td>
								
							</tr>
							<tr>
								<td style="border: 1px solid #cccccc;">
									<table width="100%" cellpadding="5" style="padding: 10px;font-size: 11pt;">
									    <tr>
											<td><strong>Passenger Name</strong></td>
											<td><strong>Ticket No</strong></td>
											<td><strong>Status</strong></td>
										</tr>
										 <?php 
									
									    $booking_transaction_details_value = $booking_transaction_details[1];
									
									 	if(isset($booking_transaction_details_value['booking_customer_details'])){
									 		foreach($booking_transaction_details_value['booking_customer_details'] as $cus_k => $cus_v){
									 	
									 ?>
									 <tr>
										<?php if (strtolower($cus_v['passenger_type']) == 'infant') { ?>
		                                   <td><?php echo $cus_v['first_name'].'  '.$cus_v['last_name'];?>(Infant)</td>
		                                 <?php }else{?>
		                                 <td><?php echo $cus_v['title'].'.'.$cus_v['first_name'].'  '.$cus_v['last_name'];?></td>
		                                 <?php } ?>
										 <td><?=@$cus_v['TicketNumber'];?></td>
										<td>
										<strong class="<?php echo booking_status_label($cus_v['status'])?>">
										<?php 
										switch($cus_v['status']){
												case 'BOOKING_CONFIRMED': echo 'CONFIRMED';break;
												case 'BOOKING_CANCELLED': echo 'CANCELLED';break;
												case 'BOOKING_FAILED': echo 'FAILED';break;
												case 'BOOKING_INPROGRESS': echo 'INPROGRESS';break;
												case 'BOOKING_INCOMPLETE': echo 'INCOMPLETE';break;
												case 'BOOKING_HOLD': echo 'HOLD';break;
												case 'BOOKING_PENDING': echo 'PENDING';break;
												case 'BOOKING_ERROR': echo 'ERROR';break;
												
											}
											?>
										</strong>
										</td>
										</tr>
										<?php } }?>
									</table>
						       </td>
					 </tr>
					  <tr><td>&nbsp;</td></tr>
					  <!-- Extra Service Starts -->
								<?php if(isset($booking_transaction_details_value['extra_service_details']['baggage_details']) == true && valid_array($booking_transaction_details_value['extra_service_details']['baggage_details']) == true){
											$baggage_details = $booking_transaction_details_value['extra_service_details']['baggage_details'];
									?>
									<tr>
										<td style="padding: 10px; border: 1px solid #cccccc; font-size: 14px; font-weight: bold;">
											Extra Baggage Information
										</td>
									</tr>

									<tr>
										<td>
											<table style="width:100%;">
												<tr>
													<th style="border:1px solid #ddd; padding:5px; font-size: 14px;">Passenger</th>
													<?php foreach ($baggage_details['baggage_source_destination_label'] as $bag_lk => $bag_lv){ ?>
															<th style="border:1px solid #ddd; padding:5px; font-size: 14px;">
																<?=$bag_lv?>
															</th>
													<?php } ?>
												</tr>
													<?php
														
														foreach ($baggage_details['details'] as $bag_k => $bag_v){?>
															<tr>
															<td style="border:1px solid #ddd; padding:5px; font-size: 14px;"><?=$bag_v[0]['pax_name']?></td>
																<?php
																foreach ($bag_v as $bd_k => $bd_v){ ?>
																		<td style="border:1px solid #ddd; padding:5px; font-size: 14px;">
																			<?=$bd_v['description']?><small>(<?=$booking_details['currency'].' '.$bd_v['price']?>)</small>
																		</td>
																	
																<?php }?>
															</tr>
													<?php } ?>
											</table>
										</td>
									</tr>
									
									<tr>
										<td>&nbsp;</td>
									</tr>
								<?php }?>
								<?php if(isset($booking_transaction_details_value['extra_service_details']['meal_details']) == true && valid_array($booking_transaction_details_value['extra_service_details']['meal_details']) == true){
											$meal_details = $booking_transaction_details_value['extra_service_details']['meal_details'];
											$meal_type = end($meal_details['details']);
											
											$meal_type = $meal_type[0]['type'];
											if($meal_type == 'static'){
												$meal_type_label = 'Meal Preference';
											} else{
												$meal_type_label = 'Meal Information';
											}
									?>
									<tr>
										<td style="padding: 10px; border: 1px solid #cccccc; font-size: 14px; font-weight: bold;">
											<?=$meal_type_label?>
										</td>
									</tr>
									
									<tr>	
										<td>
											<table style="width: 100%;">
												<tr>
													<th style="border:1px solid #ddd; padding:5px; font-size: 14px;">Passenger</th>
													<?php foreach ($meal_details['meal_source_destination_label'] as $meal_lk => $meal_lv){ ?>
															<th style="border:1px solid #ddd; padding:5px; font-size: 14px;">
																<?=$meal_lv?>
															</th>
													<?php } ?>
												</tr>
													<?php
														
														foreach ($meal_details['details'] as $meal_k => $meal_v){?>
															<tr>
															<td style="border:1px solid #ddd; padding:5px; font-size: 14px;"><?=$meal_v[0]['pax_name']?></td>
																<?php
																foreach ($meal_v as $md_k => $md_v){ ?>
																		<td style="border:1px solid #ddd; padding:5px; font-size: 14px;">
																			<?=$md_v['description']?><small>(<?=$booking_details['currency'].' '.$md_v['price']?>)</small>
																		</td>
																	
																<?php }?>
															</tr>
													<?php } ?>
											</table>
										</td>
									</tr>
									
									<tr>
										<td>&nbsp;</td>
									</tr>
								<?php }?>
								<?php if(isset($booking_transaction_details_value['extra_service_details']['seat_details']) == true && valid_array($booking_transaction_details_value['extra_service_details']['seat_details']) == true){
											$seat_details = $booking_transaction_details_value['extra_service_details']['seat_details'];
											$seat_type = end($seat_details['details']);
											$seat_type =  $seat_type[0]['type'];
											if($seat_type == 'static'){
												$seat_type_label = 'Seat Preference';
											} else{
												$seat_type_label = 'Seat Information';
											}
									?>
									<tr>
										<td style="padding: 10px; border: 1px solid #cccccc; font-size: 14px; font-weight: bold;">
											<?=$seat_type_label?>
										</td>
									</tr>
									
									<tr>
										<td>
											<table style="width: 100%;">
												<tr>
													<th style="border:1px solid #ddd; padding:5px; font-size: 14px;">Passenger</th>
													<?php foreach ($seat_details['seat_source_destination_label'] as $seat_lk => $seat_lv){ ?>
															<th style="border:1px solid #ddd; padding:5px; font-size: 14px;">
																<?=$seat_lv?>
															</th>
													<?php } ?>
												</tr>
													<?php
														
														foreach ($seat_details['details'] as $seat_k => $seat_v){?>
															<tr>
															<td style="border:1px solid #ddd; padding:5px; font-size: 14px;"><?=$seat_v[0]['pax_name']?></td>
																<?php
																foreach ($seat_v as $sd_k => $sd_v){
																	$seat_description = trim($sd_v['description']);
																	if(empty($seat_description) == true){
																		$seat_description = trim($sd_v['code']);
																	} 
																?>
																		<td style="border:1px solid #ddd; padding:5px; font-size: 14px;">
																			<?=$seat_description?><small>(<?=$booking_details['currency'].' '.$sd_v['price']?>)</small>
																		</td>
																	
																<?php }?>
															</tr>
													<?php } ?>
											</table>
										</td>
									</tr>
									
									<tr>
										<td>&nbsp;</td>
									</tr>
								<?php }?>
								<!-- Extra Service Ends -->
					  <tr>
								<td style="padding: 10px;border: 1px solid #cccccc; font-size: 11pt; font-weight: bold;">Price Summary</td>
								
							</tr>
						<tr>
								<td style="border: 1px solid #cccccc;">
									<table width="100%" cellpadding="5" style="padding: 10px;font-size: 11pt;">
									<tr>
											<td><strong>Total Fare</strong></td>
										</tr>
									 <tr>
											<td><?=@$booking_details['currency']?>  <?=@$booking_details['grand_total']?></td>
										</tr>
											
									</table>
								 </td>
							</tr>
						<tr><td>&nbsp;</td></tr>
							<tr>
								<td width="100%" style="padding: 10px;border: 1px solid #cccccc; font-size: 11pt; font-weight: bold;">Terms and Conditions</td>
							</tr>
					  <tr>
								<td  width="100%" style="border: 1px solid #cccccc;">
									<table width="100%" cellpadding="5" style="padding: 10px 20px;font-size: 11pt;">
										<tr>
                                        <td>1.A printed copy of this e-ticket display on laptop,lablet or phone mast be presented at a time of checking.</td>
										</tr>
										<tr>
                                        <td>2.Check-in starts 2 hours before scheduled departure, and closes 60 minutes prior to departure time.We recommend you report at check-in counter
                                        at least 2 hours prior to departure time.
                                        </td>
										</tr>
										<tr>
                                        <td>3.It is mandatory to carry Government recognised photo identification along with your E-ticket.This can
                                        be include:Driving License,Passport,Pan Card,Voter Id Card or any other ID issued by Government of India.
                                        for infant passengers it is mandatory to carry the Date Of Birth certificate.
                                       </td>
										</tr>
									</table>
								</td>
							</tr>								
			  </table>				
			 </td>
			</tr> 
	      </table>
	 </td>
	</tr>  
</tbody>
</table>


<?php } ?>	
<table id="printOption"  onclick="document.getElementById('printOption').style.visibility = ''; print(); return true;" style="border-collapse: collapse;font-size: 14px; margin: 10px auto; font-family: arial;" width="70%" cellpadding="0" cellspacing="0" border="0">
<tbody>
	<tr>
    <td align="center"><input style="background:#418bca; height:34px; padding:10px; border-radius:4px; border:none; color:#fff; margin:0 2px;" type="button" value="Print" /><input style="background:#418bca; height:34px; padding:10px; border-radius:4px; border:none; color:#fff; margin:0 2px;" type="button" value="Home" /></td>
    </tr>
</tbody></table>
		                    