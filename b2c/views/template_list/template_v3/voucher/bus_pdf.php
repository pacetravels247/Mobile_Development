<?php

$booking_details = $data ['booking_details'] [0];

$itineray_details = $booking_details ['booking_itinerary_details'] [0];
$customer_details = $booking_details ['booking_customer_details'];

?>
<table cellpadding="0" border-collapse cellspacing="0" width="100%" style="font-size:9pt;font-family: 'Open Sans', sans-serif; max-width:850px; margin:0px auto;background-color:#fff; padding:45px;border-collapse:separate; color: #000;">
   <tbody>
      <tr>
         <td style="border-collapse: collapse; padding:50px 35px;"><table width="100%" style="border-collapse: collapse;" cellpadding="0" cellspacing="0" border="0">
               <tbody>
                  <tr>
                     <td style="padding: 0px;"><table cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse: collapse;">
                           <tbody><!-- 
                              <tr>
                                 <td style="font-size:22px; line-height:30px; width:100%; display:block; font-weight:600; text-align:center">E-Ticket</td>
                              </tr> -->
                              <tr>
                                 <td>
                                    <table width="100%" style="border-collapse: collapse;" cellpadding="0" cellspacing="0" border="0">
                                       <tbody>
                                          <tr>
            								<td colspan="2" valign="bottom" style="padding-bottom:10px;"><img style="width:150px;" src="<?=$GLOBALS['CI']->template->domain_images($data['logo'])?>" /></td>
                                             <td style="padding: 0px;">
                                                <table width="100%" style="font-size:9pt; font-family: 'Open Sans', sans-serif;border-collapse: collapse;text-align: right; line-height:15px;" cellpadding="0" cellspacing="0" border="0">                                     
                                                   <tbody>
                                                      <tr>
                                                         <td style="padding-bottom:10px;line-height:20px" align="right"><span>Booking Reference:<?=$booking_details['app_reference']?></span><br><span>Booked Date : <?php echo $booking_details['booked_date'];?></span></td>
                                                      </tr>
                                                   </tbody>
                                                </table>
                                             </td>
                                          </tr>
                                       </tbody>
                                    </table>
                                 </td>
                              </tr>
         					<tr><td style="line-height:7px;padding:0;">&nbsp;</td></tr>
                              <tr>
            					<td align="right" colspan="4" style="line-height:25px;font-size:9pt; border-top:1px solid #00a9d6; border-bottom:1px solid #00a9d6;"><span style="font-size:9pt;">Status:</span> <strong class="<?php echo booking_status_label( $booking_details['status']);?>" style=" font-size:9pt;">           <?php
											switch ($booking_details ['status']) {
												case 'BOOKING_CONFIRMED' :
													echo 'CONFIRMED';
													break;
												case 'BOOKING_CANCELLED' :
													echo 'CANCELLED';
													break;
												case 'BOOKING_FAILED' :
													echo 'FAILED';
													break;
												case 'BOOKING_INPROGRESS' :
													echo 'INPROGRESS';
													break;
												case 'BOOKING_INCOMPLETE' :
													echo 'INCOMPLETE';
													break;
												case 'BOOKING_HOLD' :
													echo 'HOLD';
													break;
												case 'BOOKING_PENDING' :
													echo 'PENDING';
													break;
												case 'BOOKING_ERROR' :
													echo 'ERROR';
													break;
											}
											?>                                 </strong>                              </td>
                              </tr>
         					<tr><td style="line-height:7px;padding:0;">&nbsp;</td></tr>
							<tr>
							   <td style="padding:0"><span style="font-size:12pt;color:#00a9d6;vertical-align:middle;font-weight: 600; line-height:18px;"><?php echo $booking_details['operator'];?></span></td>
							</tr>
							<tr>
				            <td style="padding:0;"><table cellspacing="0" cellpadding="0" style="font-size:9pt; padding:0;line-height:normal;">
				                  <tbody>
				                     <tr>
				                     <td style="padding:10px 0; vertical-align:middle;"><span style="display: block;"><span style="font-size:11pt;font-weight: 600;">Boarding Point</span><br><span style="font-size:10pt;vertical-align:middle; "><?php echo $itineray_details['boarding_from'];?></span></span></td>
									 <td style="padding:10px 0; vertical-align:middle;"><span style="display: block;"><span style="font-size:11pt;font-weight: 600;">Dropping at</span><br><span style="font-size:10pt;vertical-align:middle;"><?php echo $itineray_details['dropping_at'];?></span></span></td>
				            		<td style="padding:0;line-height:normal;"><table cellspacing="0" cellpadding="0" style="font-size:9pt; padding:0;line-height:normal;">
				                  	 <tbody>
				                     <tr>
				            		 <td style="padding:10px 0; vertical-align:top; text-align: center;border:1px solid #808080"><span style="font-size:10pt;line-height:20px;"><span style="color:#00a9d6;padding:5px;">PNR No</span><br><span style="font-size:12pt;padding-bottom: 5px;font-weight: 600;"><?php echo $booking_details['pnr'];?></span><br><span style="padding:5px;">Seat: <?=@$booking_details['seat_numbers']?></span></span>
												 </td>
									 </tr>
									 </tbody>
									 </table>
									 </td>
									</tr>
								  </tbody>
								</table>
							  </td>
							</tr>
         					<tr><td style="line-height:20px;padding:0;">&nbsp;</td></tr>
                              <tr>
					            <td style="padding:0;"><table border="0" cellspacing="0" cellpadding="1" style="border:1px solid #00a9d6;font-size:9pt; padding:0;">
					                  <tbody>
					                     <tr>
					            			<td style="padding:0;border: 1px solid #00a9d6;"><table border="0" cellspacing="0" cellpadding="5" style="font-size:9pt;background-color:#00a9d6;border: 1px solid #00a9d6; padding:0;">
					                  		<tbody>
						                     <tr>
						                     <td style=" color:#fff"><img width="16" src="<?=SYSTEM_IMAGE_DIR.'bus_v.png'?>"> &nbsp;<span style="vertical-align:middle;font-size:10pt">Reservation Ticket (<?php echo ucfirst($booking_details['departure_from']).' To '.ucfirst($booking_details['arrival_to']);?>)</span></td> 
			                              </tr>
			                              </tbody>
			                              </table>
			                              </td>
			                              </tr>
			                              <tr>
			                                 <td style="border: 1px solid #00a9d6; padding:0px;"><table width="100%" cellpadding="5" style="padding: 10px;font-size: 10pt;padding:0;">
			                                       <tbody>
			                                          <tr>
			                                             <!-- <td>Phone</td> -->                                   
			                                             <td style="background-color:#d9d9d9;padding:5px;color: #333333;">Travel Type</td>
			                                             <td style="background-color:#d9d9d9;padding:5px;color: #333333;">Ticket Booking</td>
			                                             <td style="background-color:#d9d9d9;padding:5px;color: #333333;">Booking ID</td>
			                                             <td style="background-color:#d9d9d9;padding:5px;color: #333333;">Boarding Pickup Time</td>
			                                          </tr>
			                                          <tr>
			                                             <td style="padding:5px"><span style="width:100%; float:left"><?php echo $booking_details['bus_type'];?></span></td>
			                                             <td style="padding:5px"><span style="width:100%; float:left"><?php echo ucfirst($booking_details['departure_from']).' To '.ucfirst($booking_details['arrival_to']);?></span></td>
			                                             <td style="padding:5px"><?=@$booking_details['ticket']?></td>
			                                             <td style="padding:5px"><?=@date("d M Y",strtotime($booking_details['journey_datetime']))?> <?=get_time($booking_details['journey_datetime']);?></td>
			                                          </tr>
			                                       </tbody>
			                                    </table>
			                                 </td>
			                              </tr>
                              </tbody>
                              </table>
                              </td>
                              </tr>
                              <tr>
                                 <td style="line-height:12px;">&nbsp;</td>
                              </tr>
                              <tr>
		            			<td style="padding:0;"><table border="0" cellspacing="0" cellpadding="5" style="font-size:9pt;background-color:#666666;border: 1px solid #666666; padding:0;">
		                  		<tbody>
			                     <tr>
			                     <td style=" color:#fff"><img width="16" src="<?=SYSTEM_IMAGE_DIR.'people_group.png'?>"> &nbsp;<span style="vertical-align:middle;font-size:10pt">Traveler(s) Information</span></td> 
	                          </tr>
	                          </tbody>
	                          </table>
	                          </td>
	                          </tr>
                              <tr>
                                 <td width="100%" style="border: 1px solid #666666; padding:0px;"><table width="100%" cellpadding="5" style="padding: 10px;font-size: 10pt;">
                                       <tbody>
                                          <tr>
                                             <td style="background-color:#d9d9d9;padding:5px;color: #333333;">Sr No.</td>
                                             <td style="background-color:#d9d9d9;padding:5px;color: #333333;">Passenger(s) Name</td>
                                             <td style="background-color:#d9d9d9;padding:5px;color: #333333;">Gender</td>
                                             <td style="background-color:#d9d9d9;padding:5px;color: #333333;">Seat No</td>
                                          </tr>  
                              <?php 
                                    $i=1;
                                 ?>
										 <?php foreach ($customer_details as $key => $value) { ?>
                                          <tr>
                                             <td style="padding:5px;"><?=$i;?></td>
                                             <td style="padding:5px"><?php echo $value['title']." ".$value['name'];?></td>
                                             <td style="padding:5px;"><?php echo $value['gender'];?></td>
                                             <td style="padding:5px;"><?php echo $value['seat_no'];?></td>
                                          </tr>
                                          <?php $i++; } ?>
                                       </tbody>
                                    </table>
                                 </td>
                                 <td></td>
                              </tr>
                              <tr>
                                 <td style="line-height:12px;">&nbsp;</td>
                              </tr>
                              <tr>
                                 <td style="padding:0;"><table cellspacing="0" cellpadding="5" width="100%" style="font-size:9px; padding:0;">
                                       <tbody>
                                          <tr>
                                             <td width="50%" style="padding:0;padding-right:14px;"><table cellspacing="0" cellpadding="5" width="100%" style="font-size:9px; padding:0;border:1px solid #9a9a9a;">
                                                   <tbody>
                                                      <tr>
                                                         <td style="border-bottom:1px solid #ccc;padding:5px;"><span style="font-size:10pT">Payment Details</span></td>
                                                         <td style="border-bottom:1px solid #ccc;padding:5px;"><span style="font-size:9pt">Amount (<?=@$booking_details['currency']?>)</span></td>
                                                      </tr>
                                                      <tr>
                                                         <td style="padding:5px"><span>Base Fare</span></td>
                                                         <td style="padding:5px"><span><?=@$booking_details['fare']+$booking_details['admin_markup']?></span></td>
                                                      </tr>
                                                       <tr>
                                                         <td style="padding:5px"><span>Tax</span></td>
                                                         <td style="padding:5px"><span><?=@roundoff_number($booking_details['convinence_amount'])?></span></td>
                                                      </tr>
                                                     
                                                       <?php if($booking_details['gst'] > 0){?>
                                                     	<tr>
                                                         <td style="padding:5px"><span>GST</span></td>
                                                         <td style="padding:5px"><span><?=@roundoff_number($booking_details['gst'])?></span></td>
                                                      </tr>
                                                      <?php } ?>
                                                       <tr>
                                                         <td style="padding:5px"><span>Discount</span></td>
                                                         <td style="padding:5px"><span><?=@$booking_details['discount']?></span></td>
                                                      </tr>
                                                      <tr>
                                                         <td style="border-top:1px solid #ccc;padding:5px"><span style="font-size:10pt">Total Fare</span></td>
                                                         <td style="border-top:1px solid #ccc;padding:5px"><span style="font-size:10pt"><?=@$booking_details['grand_total']?></span></td>
                                                      </tr>
                                                   </tbody>
                                                </table>
                                             </td>
                                             <td width="50%" style="padding:0;padding-left:14px; vertical-align:top"><table cellspacing="0" cellpadding="5" width="100%" style="border:1px solid #9a9a9a;font-size:9pt; padding:0;">
                                                   <tbody>

								<?php 
								$cancellation_policy = $booking_details['cancel_policy'];
								$cancellation_policy = json_decode(base64_decode($cancellation_policy));
								// debug($cancellation_policy);exit;
								?>
                                                      <tr>
                                                         <td colspan="2" style="border-bottom:1px solid #ccc;padding:5px; color:#333"><span style="font-size:11pt">Cancellation Policy</span></td>
                                                      </tr>
                                                      <tr>
                                                         <td style="border-left:1px solid #9a9a9a;background-color:#d9d9d9; color:#333;padding:5px"><span>Cancellation Time</span></td>
                                                         <td style="background-color:#d9d9d9;border-right:1px solid #9a9a9a; color:#333;padding:5px; white-space:nowrap"><span>Cancellation Charges</span></td>
                                                      </tr>
             <?php                                         
		if (valid_array($cancellation_policy) == true) {
			
			foreach ($cancellation_policy as $__ck => $__cv) {
				$hour = floor($__cv->Mins/60);
				if($__ck !=0 && $__cv->Mins == $cancellation_policy[$__ck-1]->Mins){
					$min_label = ' Departure Time > '.$hour;
				} else {
					$min_label = $hour.' Hours Before Departure Time';
				}
				
			?>
				<tr>
					<td style="padding:5px"><?=$min_label?></td>
					<td style="padding:5px"><?=(empty($__cv->Amt) == false ? $__cv->ChargeFixed : $__cv->Pct.'%')?></td>
				</tr>
			<?php
			}
		}else {
		?>
			<tr>
				<td colspan="2">Not Available</td>
			</tr>
		<?php
		}
		?>
                                                   </tbody>
                                                </table>
                                             </td>
                                          </tr>
                                       </tbody>
                                    </table>
                                 </td>
                              </tr>
                              <tr>
                                 <td style="line-height:12px;">&nbsp;</td>
                              </tr>
                              <tr>
                                 <td align="center" style="border-bottom:1px solid #999999;padding-bottom:15px"><span style="font-size:10pt; line-height: 25px; color:#555;">Customer Contact Details | E-mail : <?php echo $booking_details['email'];?> | Contact No : <?php echo $booking_details['phone_code'];?><?php echo $booking_details['phone_number'];?></span></td>
                              </tr>
                              <tr>
                                 <td style="line-height:12px;">&nbsp;</td>
                              </tr>
                              <tr>
                                 <td align="right" style="padding-top:10px;font-size:10pt;line-height:18px;"><?=$data['domainname']?> <br>ContactNo : <?=$data['phone_code']?><?=$data['phone']?><br><?=$data['address']?></td>
                              </tr>
                           </tbody>
                        </table>
                     </td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table>


<!-- 
	<div class="table-responsive" id="tickect_bus">
	<table
		style="border-collapse: collapse; background: #ffffff; font-size: 13px; margin: 0 auto; font-family: arial;"
		width="100%" cellpadding="0" cellspacing="0" border="0">
		<tbody>
			<tr>
				<td style="border-collapse: collapse; padding: 10px 20px 20px">
					<table width="70%" style="border-collapse: collapse; margin: 30px auto; color: #766757; background: #fff8f2;"
						cellpadding="0" cellspacing="0" border="0">
						
						<tr>
							<td style="padding: 15px;">
								<table cellpadding="5" cellspacing="0" border="0" width="100%"
									style="border-collapse: collapse;">
									
						<tr>
					       <td style="font-size: 22px; line-height: 50px; width: 100%; display: block; font-weight: 600; text-align: center">E-Ticket</td>
						</tr>

						<tr>
							<td style="padding:0px; background: #fff8f2;">
								<table width="100%" style="border-collapse: collapse;"
									cellpadding="0" cellspacing="0" border="0">
									<tr>
										<td style="padding: 10px; width: 65%;"><img
											style="max-height: 56px;"
											src="<?=$GLOBALS['CI']->template->domain_images($data['logo'])?>"></td>
										<td style="padding: 10px; width: 35%;">
											<table width="100%"
												style="border-collapse: collapse; text-align: right; line-height: 15px;"
												cellpadding="0" cellspacing="0" border="0">

												<tr>
													<td style="font-size: 14px;"><span
														style="width: 100%; float: left; line-height: 35px; color: #2d3e52;"><?php echo $data['address'];?></span></td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
									<tr>
										<td width="100%"
											style="padding: 10px; border: 1px solid #f5e9de; background: #f5e9de; font-size: 13px; font-weight: bold;">Reservation Ticket (<?php echo ucfirst($booking_details['departure_from']).' To '.ucfirst($booking_details['arrival_to']);?>)</td>
									</tr>
									<tr>
										<td style="border: 1px solid #f5e9de;">
											<table width="100%" cellpadding="5"
												style="padding: 10px; font-size: 13px;">
												<tr>
													<td width="20%" style="padding:5px;"><strong>Travel name</strong></td>
													<td width="30%" style="padding:5px;"><?php echo $booking_details['operator'];?></td>
													<td width="20%" style="padding:5px;"><strong>Travel Type</strong></td>
													<td width="30%" style="padding:5px;"><?php echo $booking_details['bus_type'];?></td>

												</tr>
												<tr>
													<td style="padding:5px;"><strong>Ticket Booking</strong></td>
													<td style="padding:5px;"><?php echo ucfirst($booking_details['departure_from']).' To '.ucfirst($booking_details['arrival_to']);?></td>
													<td style="padding:5px;"><strong>Booking Status</strong></td>

													<td style="padding:5px;"><strong
														class="<?php echo booking_status_label( $booking_details['status']);?>"
														style="font-size: 13px;">
												<?php
												switch ($booking_details ['status']) {
													case 'BOOKING_CONFIRMED' :
														echo 'CONFIRMED';
														break;
													case 'BOOKING_CANCELLED' :
														echo 'CANCELLED';
														break;
													case 'BOOKING_FAILED' :
														echo 'FAILED';
														break;
													case 'BOOKING_INPROGRESS' :
														echo 'INPROGRESS';
														break;
													case 'BOOKING_INCOMPLETE' :
														echo 'INCOMPLETE';
														break;
													case 'BOOKING_HOLD' :
														echo 'HOLD';
														break;
													case 'BOOKING_PENDING' :
														echo 'PENDING';
														break;
													case 'BOOKING_ERROR' :
														echo 'ERROR';
														break;
												}
												
												?>
												</strong></td>



												</tr>
												<tr>
													<td style="padding:5px;"><strong>PNR No</strong></td>
													<td style="padding:5px;"><?php echo $booking_details['pnr'];?></td>
													<td style="padding:5px;"><strong>Booking ID</strong></td>
													<td style="padding:5px;"><?=@$booking_details['ticket']?></td>

												</tr>
												<tr>
													<td style="padding:5px; vertical-align: top;"><strong>Boarding Point</strong></td>
													<td style="padding:5px; vertical-align: top;"><?php echo $itineray_details['boarding_from'];?></td>
													<td style="padding:5px; vertical-align: top;"><strong>Landmark</strong></td>
													<td style="padding:5px; vertical-align: top;"><?php echo $itineray_details['boarding_from'];?></td>

												</tr>
												<tr>
													<td style="padding:5px;"><strong>Boarding Pickup Time</strong></td>
													<td style="padding:5px;"><?=@$itineray_details['departure_datetime']?></td>
													<td style="padding:5px;"><strong>Seat</strong></td>
													<td style="padding:5px;"><?=@$booking_details['seat_numbers']?></td>

												</tr>
												<tr>
													<td style="padding:5px;"><strong>Booking Date</strong></td>
													<td style="padding:5px;"><?php echo $booking_details['booked_date'];?></td>
													<td style="padding:5px;"><strong>Travel Date Time</strong></td>
													<td style="padding:5px;"><?=@date("d M Y",strtotime($booking_details['journey_datetime']))?> <?=get_time($booking_details['journey_datetime']);?></td>

												</tr>
												

											</table>
										</td>
									</tr>


									<tr>
										<td style="height: 9px;"></td>
									</tr>
									<tr>
										<td
											style="padding: 10px 5px; background: #f5e9de; border: 1px solid #f5e9de; font-size: 13px; font-weight: bold;">Travelers
											Information</td>

									</tr>
									<tr>
										<td style="border: 1px solid #f5e9de;">
											<table width="100%" cellpadding="5"
												style="padding: 10px; font-size: 13px;">
												<tr>
													<td style="padding:5px;"><strong>Passenger Name</strong></td>
													<td style="padding:5px;"><strong>Gender</strong></td>
													<td style="padding:5px;"><strong>Phone</strong></td>
													<td style="padding:5px;"><strong>Email ID</strong></td>
													<td style="padding:5px;"><strong>Seat No</strong></td>
												</tr>
											 <?php 
											
											 foreach ($customer_details as $key => $value) { ?>
	                                          <tr>

													<td style="padding:5px;"><?php echo $value['name'];?></td>
													<td style="padding:5px;"><?php echo $value['gender'];?></td>
													<td style="padding:5px;"><?php echo $booking_details['phone_number'];?></td>
													<td style="padding:5px;"><?php echo $booking_details['email'];?></td>
													<td style="padding:5px;"><?php echo $value['seat_no'];?></td>
												</tr>
	                            		  <?php } ?>
										</table>
										</td>
										<td></td>
									</tr>
									<tr>
										<td style="height: 9px;"></td>
									</tr>
									<tr>
										<td
											style="padding: 10px 5px; background: #f5e9de; border: 1px solid #f5e9de; font-size: 13px; font-weight: bold;">Price Summary</td>

									</tr>
									<tr>
										<td style="border: 1px solid #f5e9de;">
											<table width="100%" cellpadding="5"
												style="padding: 5px; font-size: 13px;">
												<tr>
													

													<td style="padding:5px;"><strong>Total Fare</strong></td>
												</tr>
												<tr>
													

													<td style="padding:5px;"><strong><?=@$booking_details['currency']?> <?=@$booking_details['fare']?></strong></td>
												</tr>
												
											</table>
										</td>
										<td></td>
									</tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
									<?php 
									$cancellation_policy = $booking_details['cancel_policy'];
									$cancellation_policy = json_decode(base64_decode($cancellation_policy));
									// debug($cancellation_policy);exit;
									?>	
									<tr>
										<td
											style="padding: 10px 5px; background: #f5e9de; border: 1px solid #f5e9de; font-size: 13px; font-weight: bold;">Cancellation Policy</td>

									</tr>
									<tr>
									<table class="table table-condensed table-bordered table-striped">
				<tr>
					<th>Cancellation Time</th>
					<th>Cancellation Charges</th>
				</tr>
			<?php
			// $cancellation_policy = json_decode($cancellation_policy, True);
			
			if (valid_array($cancellation_policy) == true) {
				
				foreach ($cancellation_policy as $__ck => $__cv) {
					$hour = $__cv->Mins/60;
					if($__ck !=0 && $__cv->Mins == $cancellation_policy[$__ck-1]->Mins){
						$min_label = ' Departure Time > '.$hour;
					} else {
						$min_label = $hour.' Hours Before Departure Time';
					}
					
				?>
					<tr>
						<td><?=$min_label?></td>
						<td><?=(empty($__cv->Amt) == false ? $__cv->ChargeFixed : $__cv->Pct.'%')?></td>
					</tr>
				<?php
				}
			}else {
			?>
				<tr>
					<td colspan="2">Not Available</td>
				</tr>
			<?php
			}
			?>
			</table>
									</tr>	
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table> -->
<table id="printOption"onclick="w=window.open();w.document.write(document.getElementById('tickect_bus').innerHTML);w.print();w.close(); return true;"
 style="border-collapse: collapse;font-size: 14px; margin: 10px auto; font-family: arial;" width="70%" cellpadding="0" cellspacing="0" border="0">
<tbody>
	<tr>
    <td align="center"><input style="background: #00a9d6;padding: 6px 20px;border-radius:4px;border:none;color:#fff;margin: 0;" type="button" value="Print" />
    
    </tr>
</tbody></table>
</div>