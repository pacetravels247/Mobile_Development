<?php
   $booking_details = $data['booking_details'][0];
   
   $itinerary_details = $booking_details['itinerary_details'][0];
   $attributes = json_decode($booking_details['attributes'],true);
    //debug($booking_details);
   // exit;
   $customer_details = $booking_details['customer_details'];
   
   $domain_details = $booking_details;
   $lead_pax_details = $booking_details['customer_details'];
   ?>
<div style="width:100%; position:relative" id="tickect_hotel">
   <table cellpadding="0" cellspacing="0" width="100%" style="font-size:13px; font-family: 'Open Sans', sans-serif; width:900px; margin:0px auto;background-color:#fff; padding:50px 45px;">
      <tbody>
         <tr>
            <td style="border-collapse: collapse; padding:50px 35px;" >
               <table width="100%" style="border-collapse: collapse;" cellpadding="0" cellspacing="0" border="0">
                  <tr>
                     <td style="padding: 0px;">
                        <table cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse: collapse;">
                           <tr>
                              <td style="font-size:22px; line-height:30px; width:100%; display:block; font-weight:600; text-align:center">Hotel Voucher</td>
                           </tr>
                           <tr>
                              <td>
                                 <table width="100%" style="border-collapse: collapse;" cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                       <td style="padding: 0px;"><img style="width:200px;" src="<?=$GLOBALS['CI']->template->domain_images($data['logo'])?>"></td>
                                       <td style="padding: 0px;">
                                          <table width="100%" style="font-size:13px; font-family: 'Open Sans', sans-serif;border-collapse: collapse;text-align: right; line-height:15px;" cellpadding="0" cellspacing="0" border="0">
                                             <!-- <tr>
                                                <td style="font-size:14px;"><span style="width:100%; float:left"><?php echo $data['address'];?></span></td>
                                                </tr> -->
                                             <tr>
                                                <td style="padding-bottom:10px;line-height:20px" align="right"><span>Booking Reference: <?php echo $booking_details['app_reference']; ?></span><br><span>Booked Date : <?php echo date("d M Y",strtotime($booking_details['created_datetime'])); ?></span></td>
                                             </tr>
                                          </table>
                                       </td>
                                    </tr>
                                 </table>
                              </td>
                           </tr>
                           <tr>
                              <td align="right" style="line-height:24px;font-size:13px;border-top:1px solid #00a9d6;border-bottom:1px solid #00a9d6;padding: 5px;">Status: <strong class="<?php echo booking_status_label( $booking_details['status']);?>" style=" font-size:14px;">
                                 <?php 
                                    switch($booking_details['status']){
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
                           <tr><td style="line-height:12px;">&nbsp;</td></tr>                           
                           <tr>
                              <td>
                                 <table width="100%" cellpadding="5" style="padding: 10px;font-size: 13px;padding:5px;">
                                    <tbody>
                                    <tr>
                                       <?php if($attributes['HotelImage'] != '/'):?>
                                          <td style="padding:10px 0"><img style="width:160px;" src="<?=$attributes['HotelImage'];?>" /></td>
                                       <?php else:?>
                                          <td style="padding:10px 0"><img style="width:160px;" src="<?=$GLOBALS['CI']->template->template_images("default_hotel_img.jpg");?>" /></td>
                                       <?php endif;?>
                                       <td valign="top" style="padding:10px;"><span style="line-height:30px;font-size:16px;color:#00a9d6;vertical-align:middle;font-weight: 600;"><?php echo $booking_details['hotel_name']; ?></span><br><span style="display: block;line-height:22px;font-size: 13px;"><?php echo $booking_details['hotel_address']; ?> </span><br><span style="display: block;line-height:22px;font-size: 13px;"><img style="width:70px;" src="<?php echo $GLOBALS['CI']->template->template_images('star_rating-'.$attributes["StarRating"].'.png'); ?>" /></span></td>

                                       <td width="32%" style="padding:10px 0;text-align: center;"><span style="font-size:14px; border:2px solid #808080; display:block"><span style="color:#00a9d6;padding:5px; display:block;text-transform:uppercase">Booking ID</span><span style="font-size:14px;line-height:35px;padding-bottom: 5px;display:block;font-weight: 600;"><?php echo $booking_details['booking_id']; ?></span></span></td>
                                    </tr>
                                    </tbody>
                                 </table>
                              </td>
                           </tr>
                           <tr><td style="line-height:12px;">&nbsp;</td></tr>      
                           <tr>
                              <td style="background-color:#00a9d6;border: 1px solid #00a9d6; color:#fff; font-size:14px; padding:5px;"><img style="vertical-align:middle" src="<?=SYSTEM_IMAGE_DIR.'hotel_v.png'?>" /> <span style="font-size:14px;color:#fff;vertical-align:middle;"> &nbsp;Hotel Details</span></td>
                           </tr>
                           <tr>
                              <td  width="100%" style="border: 1px solid #00a9d6; padding:0px;">
                                 <table width="100%" cellpadding="5" style="padding: 10px;font-size: 13px;padding:5px;">
                                    <tr>
                                       <!-- <td>Phone</td> -->
                                   <td style="background-color:#d9d9d9;padding:5px;color: #333333;">Check-In</td>
                                       <td style="background-color:#d9d9d9;padding:5px;color: #333333;">Check-Out</td>
                                       <td style="background-color:#d9d9d9;padding:5px;color: #333333;text-align:center">No of Room's</td>
                                       <td style="background-color:#d9d9d9;padding:5px;color: #333333;">Room Type</td>
                                       <td style="background-color:#d9d9d9;padding:5px;color: #333333;text-align:center">Adult's</td>
                                       <td style="background-color:#d9d9d9;padding:5px;color: #333333;text-align:center">Children</td>
                                    </tr>
                                    <tr>
                                       <td style="padding:5px"><span style="width:100%; float:left"> <?=@date("d M Y",strtotime($itinerary_details['check_in']))?></span></td>
                                       <td style="padding:5px"><span style="width:100%; float:left"> 	<?=@date("d M Y",strtotime($itinerary_details['check_out']))?></span></td>
                                       <td style="padding:5px" align="center"><?php echo $booking_details['total_rooms']; ?></span></td>
                                       <td style="padding:5px"  width="13%"><?php echo $itinerary_details['room_type_name']; ?></td>
                                       <td style="padding:5px" align="center"><?php echo $booking_details['adult_count']; ?></td>
                                       <td  style="padding:5px" align="center"><?php echo $booking_details['child_count']; ?></td>
                                    </tr>
                                 </table>
                              </td>
                           </tr>
                           <tr><td style="line-height:12px;">&nbsp;</td></tr>
                           <tr>
                              <td style="background-color:#666666;border: 1px solid #666666; color:#fff; font-size:14px; padding:5px;"><img style="vertical-align:middle" src="<?=SYSTEM_IMAGE_DIR.'people_group.png'?>" /> <span style="font-size:14px;color:#fff;vertical-align:middle;"> &nbsp;Guest(s) Details</span></td>
                           </tr>
                           <tr>
                              <td  width="100%" style="border: 1px solid #666666; padding:0px;">
                                 <table width="100%" cellpadding="5" style="padding: 10px;font-size: 13px;">
                                    <tr>
                                       <td style="background-color:#d9d9d9;padding:5px;color: #333333;">Sr No.</td>
                                       <td style="background-color:#d9d9d9;padding:5px;color: #333333;">Passenger(s) Name</td>
                                       <td style="background-color:#d9d9d9;padding:5px;color: #333333;">Type</td>
                                       <td style="background-color:#d9d9d9;padding:5px;color: #333333;">Age</td>
                                    </tr>
                                    <!-- <tr>
                                       <td><?php echo $customer_details['title'].' '.$customer_details['first_name'].' '.$customer_details['middle_name'].' '.$customer_details['last_name'];?></td>
                                                                        <td><?php echo $customer_details['phone'];?></td>
                                                                        <td><?php echo $customer_details['email'];?></td>
                                                                        <td><?php echo $booking_details['cutomer_city'];?></td>
                                                                         
                                                                     </tr>   -->
                                     <?php
                                          $i=1;
                                       ?> 
                                    <?php foreach($customer_details as $details):?>
                                    <tr>
                                      
                                       <td style="padding:5px;"><?=$i?></td>
                                       <td style="padding:5px"><?php echo $details['title'].' '.$details['first_name'].' '.$details['last_name']?></td>
                                       <td style="padding:5px;"><?=$details['pax_type']?></td>
                                       <?php
                                          
                                          $age = '';

                                         $current_date = date('Y-m-d');
                                          $date1 = date_create($current_date);
                                           $date2 = date_create($details['date_of_birth']);
                                          $date_obj = date_diff($date1,$date2);
                                          

                                          if($details['pax_type']=='Child'){
                                             $age = $date_obj->y;
                                          }
                                          $i++;
                                       ?>
                                       <td style="padding:5px;"><?=$age?></td>                                       
                                       <!-- <td style="padding:5px"><?php echo $details['phone'] ?></td>
                                       <td style="padding:5px"><?php echo $details['email']?></td> -->
                                    </tr>
                                    <?php endforeach;?>
                                 </table>
                              </td>
                              <td></td>
                           </tr>
                           <tr><td style="line-height:12px;">&nbsp;</td></tr>
							<tr>
								<td colspan="4" style="padding:0;">
									<table cellspacing="0" cellpadding="5" width="100%" style="font-size:12px; padding:0;">
										<tbody>
											<tr>
												<td width="50%" style="padding:0;padding-right:14px;">
													<table cellspacing="0" cellpadding="5" width="100%" style="font-size:12px; padding:0;border:1px solid #9a9a9a;">
														<tbody>
															<tr>
																<td style="border-bottom:1px solid #ccc;padding:5px;"><span style="font-size:13px">Payment Details</span></td>
																<td style="border-bottom:1px solid #ccc;padding:5px;"><span style="font-size:11px">Amount (<?=$booking_details['currency']?>)</span></td>
															</tr>
															<tr>
																<td style="padding:5px"><span>Base Fare</span></td>
																<td style="padding:5px"><span><?php echo round($booking_details['fare']+$booking_details['admin_markup']); ?></span></td>
															</tr>
															<tr>
																<td style="padding:5px"><span>Taxes</span></td>
																<td style="padding:5px"><span><?php echo round($booking_details['convinence_amount']); ?></span></td>
															</tr>
                                             <?php if($itinerary_details['gst'] > 0){?>
                                             <tr>
                                                <td style="padding:5px"><span>GST</span></td>
                                                <td style="padding:5px"><span><?php echo round($itinerary_details['gst']); ?></span></td>
                                             </tr>
                                            <?php } ?>
															<tr>
																<td style="padding:5px"><span>Discount</span></td>
																<td style="padding:5px"><span><?php echo $booking_details['discount']; ?></span></td>
															</tr>
															
															<tr>
																<td style="border-top:1px solid #ccc;padding:5px"><span style="font-size:13px">Total Fare</span></td>
																<td style="border-top:1px solid #ccc;padding:5px"><span style="font-size:13px"><?php echo round($booking_details['grand_total']); ?></span></td>
															</tr>
														</tbody>
													</table>
												</td>
												<td width="50%" style="padding:0;padding-left:14px; vertical-align:top">
													<table cellspacing="0" cellpadding="5" width="100%" style="border:1px solid #9a9a9a;font-size:12px; padding:0;">

														<tbody>
															<tr>
																<td style="background-color:#d9d9d9;border-bottom:1px solid #ccc;padding:5px; color:#333"><span style="font-size:13px">Room Inclusions</span></td>
															</tr>
                                             <?php if($attributes['Boarding_details']): ?>
                                                <?php foreach($attributes['Boarding_details'] as $b_value):?>
      															<tr>
      																<td style="padding:5px"><span><?=$b_value?></span></td>	
      															</tr>
   															
                                                <?php endforeach;?>
                                             <?php else:?>
                                                   <tr>
                                                      <td style="padding:5px"><span>Room Only</span></td>  
                                                   </tr>
                                             <?php endif;?>
															<tr>
																<td style="border-top:1px solid #ccc;padding:5px"><span style="font-size:10px; color:#666; line-height:20px;">* Room inclusions are subject to change with Hotels.</span></td>
															</tr>
														</tbody>
													</table>
												</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
							<tr><td style="line-height:12px;">&nbsp;</td></tr>
							<tr><td align="center" colspan="4" style="border-bottom:1px solid #999999;padding-bottom:15px"><span style="font-size:13px; color:#555;">Customer Contact Details | E-mail : <?=$customer_details[0]['email']?> | Contact No : <?=$booking_details['phone_code']." ".$customer_details[0]['phone']?></span></td></tr>
							<tr><td style="line-height:12px;">&nbsp;</td></tr>
							<tr>
							<td colspan="4"><span style="line-height:26px;font-size: 14px;font-weight: 500;">Cancellation Policy</span></td></tr>
							<tr>
								<td colspan="4" style="line-height:20px; font-size:12px; color:#555"><?=$booking_details['cancellation_policy'][0]?></td>
							</tr>
							<tr><td style="line-height:12px;">&nbsp;</td></tr>
							<tr>
							<td colspan="4"><span style="line-height:26px;font-size: 14px;font-weight: 500;">Terms and Conditions</span></td></tr>
							<tr>
								<td colspan="4" style="line-height:20px; border-bottom:1px solid #999999; padding-bottom:15px; font-size:12px; color:#555"><?php echo $data['terms_conditions']; ?>
                  </td>
							</tr>
							<!-- <tr><td style="line-height:12px;">&nbsp;</td></tr>
                     
                     <tr>
                     <td colspan="4"><span style="line-height:26px;font-size: 14px;font-weight: 500;">Important Information</span></td></tr>
                     <tr>
                        <td colspan="4" style="border-bottom:1px solid #999999; line-height:20px; font-size:12px; color:#555">
                           <ul>
                              <li>All Guests, including children and infants, must present valid identification at check-in.</li>
                              <li>Check-in begins 2 hours prior to the flight for seat assignment and closes 45 minutes prior to the scheduled departure.</li>
                              <li>Carriage and other services provided by the carrier are subject to conditions of carriage, which are hereby incorporated by reference. These conditions may be obtained from the issuing carrier.</li>
                              <li>In case of cancellations less than 6 hours before departure please cancel with the airlines directly. We are not responsible for any losses if the request is received less than 6 hours before departure.</li>
                              <li>Please contact airlines for Terminal Queries.</li>
                              <li>Free Baggage Allowance: Checked-in Baggage = 15kgs in Economy class.</li>
                              <li>Partial cancellations are not allowed for Round-trip Fares</li>
                              <li>Changes to the reservation will result in the above fee plus any difference in the fare between the original fare paid and the fare for the revised booking.</li>
                              <li>In case of cancellation of a booking, made by a Go channel partner, refund has to be collected from that respective Go Channel.</li>
                              <li>The No Show refund should be collected within 15 days from departure date.</li>
                              <li>If the basic fare is less than cancellation charges then only statutory taxes would be refunded.</li>
                              <li>We are not be responsible for any Flight delay/Cancellation from airline's end.</li>
                              <li>Kindly contact the airline at least 24 hrs before to reconfirm your flight detail giving reference of Airline PNR Number.</li>
                              <li>We are a travel agent and all reservations made through our website are as per the terms and conditions of the concerned airlines. All modifications,cancellations and refunds of the airline tickets shall be strictly in accordance with the policy of the concerned airlines and we disclaim all liability in connection thereof.</li>
                           </ul>
                        </td>
                     </tr> -->
							<tr>
								<td colspan="4" align="right" style="padding-top:10px;font-size:13px;line-height:20px;"><?=$data['domainname']?><br>ContactNo : <?=$data['phone_code']?><?=$data['phone']?><br><?=$data['address']?></td>
							</tr>
                           
                        </table>
                     </td>
                  </tr>
               </table>
            </td>
         </tr>
      </tbody>
   </table>
   <table id="printOption"onclick="w=window.open();w.document.write(document.getElementById('tickect_hotel').innerHTML);w.print();w.close(); return true;"
      style="border-collapse: collapse;font-size: 14px; margin: 10px auto; font-family: arial;" width="70%" cellpadding="0" cellspacing="0" border="0">
      <tbody>
         <tr>
            <td align="center"><input style="background:#418bca; height:34px; padding:10px; border-radius:4px; border:none; color:#fff; margin:0 2px;" type="button" value="Print" />
         </tr>
      </tbody>
   </table>
</div>