<?php 

  $booking_details = $data['booking_details'][0];
  $itinerary_details = $data['booking_details'][0]['itinerary_details'][0];
  //debug($itinerary_details);

  $attributes = json_decode($booking_details['attributes'],true);

  //debug($s_search_params);
  if($attributes['Additional_info']){
      $additional_info = json_decode($attributes['Additional_info']);
  }else{
    $additional_info =array();
  }
  if($attributes['Inclusions']){
    $inclustions =json_decode($attributes['Inclusions'],true);   
  }else{
    $inclustions = array();
  }
 
  if($attributes['Exclusions']){
    $exclustions = json_decode($attributes['Exclusions'],true);
  }else{
    $exclustions = array();
    
    
  }
  if($attributes['Duration']){
    $duration = $attributes['Duration'];
  }else{
    $duration = '';
  }
   if($attributes['ShortDesc']){
    $desc = $attributes['ShortDesc'];
  }else{
    $desc = $attributes['ShortDesc'];
  }

if(isset($booking_details)){
    $app_reference = $booking_details['app_reference'];
  }
  if(isset($booking_details)){
    $booking_source = $booking_details['booking_source'];
  }
  if(isset($booking_details)){
    $status = $booking_details['status'];
  }
  if(isset($booking_details)){
    $lead_pax_email = $booking_details['lead_pax_email'];
  }
  $customer_details = $booking_details['customer_details'];
  // debug($booking_details);
  // exit;

?>

<table style="border-collapse: collapse; background: #ffffff;font-size: 12pt; margin: 0 auto; font-family: arial;" width="100%" cellpadding="0" cellspacing="0" border="0">
  <tbody>
   <tr>
   <td style="border-collapse: collapse; padding:10px 20px 20px" ><table width="100%" style="border-collapse: collapse;" cellpadding="0" cellspacing="0" border="0">
			<tr><td style="font-size:15pt; line-height:30px; width:100%; display:block; font-weight:600; text-align:center">E-Ticket</td></tr>
			<tr>
					<td><table width="100%" style="border-collapse: collapse;" cellpadding="0" cellspacing="0" border="0">
			     <tr>
					<td style="padding: 10px;width:65%;"><img style="width:150px;" src="<?=$GLOBALS['CI']->template->domain_images($data['logo'])?>"></td>
					<td style="padding: 10px;width:35%"><table width="100%" style="border-collapse: collapse;text-align: right; font-size:10pt" cellpadding="0" cellspacing="0" border="0">         		
	                     <tr>
                          <td style="padding-bottom:10px;line-height:16px" align="right"><span>Travel Date: <?=date("l\, jS F Y",strtotime($booking_details['travel_date']));?></span><br><span>Booking Reference: <?=$booking_details['app_reference']?></span><br><span>Activity Code: <?=$booking_details['product_code']?></span></td>
                       </tr>
                         </table></td>
				  </tr>
				  
				  
				</table>
				</td>
			</tr>
            <tr><td style="line-height:6px;">&nbsp;</td></tr>    
           <tr>
              <td align="right" style="line-height:24px;font-size:10pt;border-top:1px solid #00a9d6;border-bottom:1px solid #00a9d6;padding: 5px;">Status: <strong class="<?php echo booking_status_label( $booking_details['status']);?>" style=" font-size:10pt;"><?php 
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
                      ?></strong>
              </td>
           </tr>
            <tr><td style="line-height:6px;">&nbsp;</td></tr>                  
                           <tr>
                              <td style="padding:0;"><table width="100%" cellpadding="5" style="padding: 10px;font-size: 10pt;padding:5px;">
                                    <tbody>
                                    <tr>
                                       <?php if($attributes['ProductImage']):?>
                                          <td width="26%" style="padding:10px 0"><img style="width:130px; height:107px;" src="<?=$attributes['ProductImage']?>" /></td>
                                       <?php else:?>
                                          <td style="padding:10px 0"><img style="width:130px; height:107px;" src="<?=$GLOBALS['CI']->template->template_images("no_image_available.jpg");?>" /></td>
                                       <?php endif;?>
                                        <td width="40%" valign="top" style="padding:10px;line-height:15px;"><span style="font-size:11pt;color:#00a9d6;vertical-align:middle;font-weight: 600;"><?=$booking_details['product_name']?></span><br><span style="display: block;font-size: 10pt;"><?=$attributes['Destination']?> </span><br><br><span style="display: block;font-size: 10pt;"><img style="width:50px;" src="<?php echo $GLOBALS['CI']->template->template_images('star_rating-'.$attributes["StarRating"].'.png'); ?>"></span></td>
                                       <td width="34%" style="padding:10px 0;text-align: center;line-height:25px;"><table style="border:2px solid #808080;">
                                       	<tbody>
                                       		<tr>
                                       			<td><span style="font-size:11pt; display:block"><span style="color:#00a9d6;padding:5px; display:block;text-transform:uppercase">Booking ID</span><br><span style="font-size:10pt;padding-bottom: 5px;display:block;font-weight: 600;"><?=$booking_details['booking_reference']?></span></span></td>
                                       		</tr>
                                       	</tbody>
                                       </table>
                                       </td>
                                    </tr>
                                    </tbody>
                                 </table>
                              </td>
                           </tr>
                           <tr><td style="line-height:2px;">&nbsp;</td></tr>   
					<tr>
					<td style="padding: 10px;"><table cellpadding="5" cellspacing="0" border="0" width="100%" style="border-collapse: collapse;">
                           <tr>
                              <td style="background-color:#00a9d6;border: 1px solid #00a9d6; color:#fff; font-size:10pt; padding:5px; line-height:normal;"><img width="12" src="<?=SYSTEM_IMAGE_DIR.'hotel_v.png'?>" /> <span style="font-size:10pt;color:#fff;line-height:12px;"> &nbsp;Activity Details</span></td>
                           </tr>
							<tr>
								<td width="100%" style="border: 1px solid #00a9d6;padding:0"><table width="100%" cellpadding="5" style="padding: 10px;font-size: 9pt;">	    
									    <tr>
											<td style="background-color:#d9d9d9;color: #333333;"><strong>Supplier</strong></td>
											<td style="background-color:#d9d9d9;color: #333333;"><strong>Supplier Phone No</strong></td>
											<td style="background-color:#d9d9d9;color: #333333;"><strong>Duration</strong></td>
											<td align="center" style="background-color:#d9d9d9;color: #333333;"><strong>Total Traveler(s)</strong></td>			   
									    </tr>									    
									    <tr>
											<td><?=$attributes['SupplierName']?></td>
											<td><?=$attributes['SupplierPhoneNumber']?></td>
                      <?php 
                $total_travell_count = $booking_details['adult_count']+$booking_details['child_count'] +$booking_details['senior_count']+$booking_details['youth_count']+$booking_details['infant_count'];
                ?>
											<td><?=$duration?></td>
											<td align="center"><?=$total_travell_count?></td>
									    </tr>
									</table>
									</td>
							</tr>
							<tr><td style="line-height:2px;">&nbsp;</td></tr>

                           <tr>
                              <td style="background-color:#666666;border: 1px solid #666666; color:#fff; font-size:9pt; padding:5px;"><img width="12" style="vertical-align:middle" src="<?=SYSTEM_IMAGE_DIR.'people_group.png'?>" /> <span style="font-size:9pt;color:#fff;vertical-align:middle;"> &nbsp;Traveller(s) Details</span></td>
                           </tr>
                           <tr>
								<td width="100%" style="border: 1px solid #666666;padding:0"><table width="100%" cellpadding="5" style="padding: 10px;font-size: 9pt;">	 
                                    <tr>
                                       <td style="background-color:#d9d9d9;padding:5px;color: #333333;">Sr No.</td>
                                       <td style="background-color:#d9d9d9;padding:5px;color: #333333;">Passenger(s) Name</td>
                                       <td style="background-color:#d9d9d9;padding:5px;color: #333333;">Type</td>
                                    </tr> 
                                     <?php $i=1;?>  
                                     <?php foreach($customer_details as $name): ?>                         
                                     <tr>
                                        <td style="padding:5px;"><?=$i?></td>
                                        <td style="padding:5px"><?=$name['title'].'.  '.$name['first_name'].' '.$name['last_name']?></td>
                                        <td style="padding:5px;"><?=$name['pax_type']?></td>                            
                                     </tr>
                                      <?php $i++;?>
                                      <?php endforeach;?>
                                 </table>
                              </td>
                           </tr>
							<tr>
                <td width="100%" style="padding:0"><table width="100%" cellpadding="5" style="padding: 0;font-size: 9pt;">   
                    <tbody>
                      <tr>
                        <td><table cellspacing="0" cellpadding="5" width="100%" style="font-size:9pt; padding:0;border:1px solid #9a9a9a;">
                            <tbody>
                              <tr>
                                <td style="border-bottom:1px solid #ccc;padding:5px;"><span style="font-size:10pt">Payment Details</span></td>
                                <td style="border-bottom:1px solid #ccc;padding:5px;"><span style="font-size:9pt">Amount (<?=$booking_details['currency']?>)</span></td>
                              </tr>                      
                                                        
                               <tr>
                                  <td style="padding:5px"><span>Base Fare</span></td>
                                  <td style="padding:5px"><span><?php echo roundoff_number($booking_details['product_total_price']); ?></span></td>
                               </tr>
                               <tr>
                                  <td style="padding:5px"><span>Taxes</span></td>
                                  <td style="padding:5px"><span><?=$booking_details['convinence_amount']?></span></td>
                               </tr>
                               <?php if($itinerary_details['gst'] > 0){?>
                                 <tr>
                                    <td style="padding:5px"><span>GST</span></td>
                                    <td style="padding:5px"><span><?php echo round($itinerary_details['gst']); ?></span></td>
                                 </tr>
                                <?php } ?>
                               <tr>
                                  <td style="padding:5px"><span>Discount</span></td>
                                  <td style="padding:5px"><span><?php echo roundoff_number($booking_details['discount']); ?></span></td>
                               </tr>
                               <tr>
                                  <td style="border-top:1px solid #ccc;padding:5px"><span style="font-size:13px">Total Fare</span></td>
                                  <td style="border-top:1px solid #ccc;padding:5px"><span style="font-size:13px"><?php echo roundoff_number($booking_details['product_total_price']+$booking_details['convinence_amount']-$booking_details['discount']); ?></span></td>
                               </tr>
                                                        
                            </tbody>
                          </table>
                        </td>
                        <td><table cellspacing="0" cellpadding="5" width="100%" style="border:1px solid #9a9a9a;font-size:10pt; padding:0;">

                            <tbody>
                              <tr>
                                <td style="background-color:#d9d9d9;border-bottom:1px solid #ccc;border-left:1px solid #9a9a9a;padding:5px; color:#333"><span style="font-size:10pt">Activity Inclusions</span></td>
                              </tr>
                                   <?php if($inclustions):?>
                                                          <?php foreach($inclustions as $incl):?>
                                                         <tr>
                                                            <td style="padding:5px"><span><?=$incl?></span></td>
                                                         </tr>
                                                       <?php endforeach;?>
                                                     <?php else:?>
                                                      <tr>
                                                        <td style="padding:5px">Acitity Only</td>
                                                      </tr>
                                                        <?php endif;?>
                            </tbody>
                          </table>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </td>
              </tr>
              <tr><td align="center" style="border-bottom:1px solid #999999;padding-bottom:15px"><span style="font-size:10pt; color:#555;">Customer Contact Details | E-mail : <?=$customer_details[0]['email']?> | Contact No : <?=$customer_details[0]['phone']?></span></td></tr>
              <tr>
              <td><span style="font-size: 11pt;line-height:16px;font-weight: 500;">Cancellation Policy</span><br><span style="font-size:9pt; color:#555; line-height:16px"><?=$attributes['TM_Cancellation_Policy']?></span></td>
              </tr>
             <tr>
                <td><span style="font-size: 11pt;line-height:12px;font-weight: 500;">Tour Information</span><br><span style="font-size: 10pt;line-height:12px;font-weight: 500;color:#333">Tour Description</span><br><span style="font-size:9pt; color:#555; line-height:14px"><?=$desc?></span><br><span style="font-size: 10pt;line-height:12px;font-weight: 500;color:#333">Information</span>
                  <ul style="font-size:9pt; color:#555">
                  <?php foreach($additional_info as $ad):?><li><?=$ad?></li><?php endforeach;?></ul>
                  <span style="font-size: 10pt;line-height:12px;font-weight: 500;color:#333">Exclusions</span>

                  <ul style="font-size:9pt; color:#555"><?php foreach($exclustions as $exclu):?><li><?=$exclu?></li><?php endforeach;?></ul>
                </td>
             </tr>
              <tr><td style="line-height:2px;">&nbsp;</td></tr> 
              <tr>
              <td><span style="font-size: 11pt;line-height:12px;font-weight: 500;color:#333">Terms and Conditions</span></td></tr>
              <tr>
                <td style="border-bottom:1px solid #999999; padding-bottom:15px; font-size:9pt; color:#555;line-height:16px"> <?php echo $data['terms_conditions']; ?></td>
              </tr>
              <tr><td style="line-height:2px;">&nbsp;</td></tr>
              <tr>
                <td align="right" style="padding-top:10px;font-size:9pt;line-height:16px"><?=$data['domainname']?><br>ContactNo : <?=$data['phone_code']?><?=$data['phone']?><br><?=$data['address']?></td>
              </tr>			
							
			         </table>
			         </td>
			</tr>
			
			</table>
    </td>
   </tr>
  </tbody>
</table>  