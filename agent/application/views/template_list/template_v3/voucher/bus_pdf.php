<?php
   $booking_details = $data ['booking_details'] [0];
   
   $itineray_details = $booking_details ['booking_itinerary_details'] [0];
   $customer_details = $booking_details ['booking_customer_details'];
   $logo = '';
   if(isset($data['logo'])){
      $logo = $data['logo'];
   }else{
      $logo = $booking_details['domain_logo'];
   }
   $pnrs = explode(',', $booking_details['pnr']);
   $agent_id = $booking_details["created_by_id"];
$agent_det = $GLOBALS["CI"]->custom_db->single_table_records("b2b_user_details", "*", array("user_oid" => $agent_id));
$agent_logo = $agent_det["data"][0]["logo"];

$ticket_num = $booking_details['ticket'];
if($booking_details["booking_source"] == VRL_BUS_BOOKING_SOURCE || $booking_details["booking_billing_type"] == "offline")
{
	$temp = $booking_details['pnr'];
	$booking_details['pnr'] = 	$booking_details['ticket'];
	$booking_details['ticket'] = $temp;
	$ticket_num = $booking_details['pnr'];
}
   ?>
<table cellpadding="0" border-collapse cellspacing="0" width="100%" style="font-size:9pt;font-family: 'Open Sans', sans-serif; max-width:850px; margin:0px auto;background-color:#fff; padding:45px;border-collapse:separate; color: #000;">
   <tbody>
      <tr>
         <td style="border-collapse: collapse; padding:50px 35px;"><table width="100%" style="border-collapse: collapse;" cellpadding="0" cellspacing="0" border="0">
               <tbody>
                  <tr>
                     <td style="padding: 0px;"><table cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse: collapse;">
                           <tbody>
                              <tr>
                                 <td>
                                    <table width="100%" style="border-collapse: collapse;" cellpadding="0" cellspacing="0" border="0">
                                       <tbody>
                                          <tr>
                                             <td colspan="2" valign="bottom" style="padding-bottom:10px;" class="voucher_logo"><img style="width:80px;" src="<?=$GLOBALS['CI']->template->domain_full_images($agent_logo)?>" /></td>
                                             <td style="padding: 0px;">
                                                <table width="100%" style="font-size:9pt; font-family: 'Open Sans', sans-serif;border-collapse: collapse;text-align: right; line-height:15px;" cellpadding="0" cellspacing="0" border="0">
                                                   <tbody>
                                                      <tr>
                                                         <td style="padding-bottom:10px;line-height:20px" align="right">
                                                         <span><?=$data['domainname']?></span><br>
                                                         <span><?=$data['phone']?></span><br>
                                                         <span><?=$data['address']?></span><br><br>   
                                                         <!-- <span>Booking Reference:<?=$booking_details['app_reference']?></span> --><span>Booked Date : <?php echo $booking_details['booked_date'];?></span></td>
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
                                 <td style="line-height:24px;font-size:13px;border-top:1px solid #00a9d6;border-bottom:1px solid #00a9d6;padding: 5px;font-size:9pt;"> 
                     <table width="100%">
                        <tr>
                           <td><span align="left">Booking Reference:<?=$booking_details['app_reference']?></span></td>
                           <td align="right">Status: <strong class="<?php echo booking_status_label( $booking_details['status']);?>" style=" font-size:14px;">           <?php
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
                           ?></strong>        
                           </td>
                        </tr>
                     </table>
                  </td>
                              </tr>
                              
                              <tr>
                                 <td style="line-height:7px;padding:0;">&nbsp;</td>
                              </tr>
                              <tr>
                                 <td style="padding:0"><span style="font-size:12pt;color:#00a9d6;vertical-align:middle;font-weight: 600; line-height:18px;"><?php echo $booking_details['operator'];?></span></td>
                              </tr>
                              <tr>
                                 <td style="padding:0;">
                                    <table cellspacing="0" cellpadding="0" style="font-size:9pt; padding:0;line-height:normal;" width="100%">
                                       <tbody>
                                          <tr>
                                             <td style="width: 42%; padding:11px 0px 0px; vertical-align:middle;"><span style="display: block;"><span style="font-size:11pt;font-weight: 600;">Boarding Point</span><br><span style="font-size:10pt;vertical-align:middle; "><?php echo $itineray_details['boarding_from'];?></span></span></td>
                                             <td width="3%">&nbsp;</td>
                                             <td style="width: 30%; padding: 5px 14px 0px 30px; vertical-align:middle;" colspan="2"><span style="font-size:11pt;font-weight: 600; display: block;">Dropping at</span><br><span style="font-size:10pt;vertical-align:middle; display: block;"><?php echo $itineray_details['dropping_at'];?></span></td>
                                             <td style="width:25%; padding:0;line-height:normal;">
                                                <table cellspacing="0" cellpadding="0" style="font-size:9pt; padding:0;line-height:normal;">
                                                   <tbody>
                                                      <tr>
                                                         <td style="padding:10px 0; vertical-align:top; text-align: center;border:1px solid #808080"><span style="font-size:10pt;line-height:20px;"><span style="color:#00a9d6;padding:5px; display:block">PNR No</span><br><span style="font-size:12pt;padding-bottom: 5px;font-weight: 600;"><?php echo $booking_details['pnr'];?></span><br><span style="padding:5px;border-top:2px solid #808080;display:block; ">Seat: <?=@$booking_details['seat_numbers']?></span></span>
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
                              <tr>
                                 <td style="line-height:20px;padding:0;">&nbsp;</td>
                              </tr>
                              <tr>
                                 <td style="padding:0;"><table border="0" cellspacing="0" cellpadding="1" style="border:1px solid #00a9d6;font-size:9pt; padding:0;">
                                       <tbody>
                                          <tr>
                                             <td style="padding:0;border: 1px solid #00a9d6;"><table border="0" cellspacing="0" cellpadding="5" style="font-size:9pt;background-color:#00a9d6;border: 1px solid #00a9d6; padding:0;">
                                                   <tbody>
                                                      <tr>
                                                         <td style=" color:#fff"><img width="16" src="<?=FULL_SYSTEM_IMAGE_DIR.'bus_v.png'?>"> &nbsp;<span style="vertical-align:middle;font-size:10pt">Reservation Ticket (<?php echo ucfirst($booking_details['departure_from']).' To '.ucfirst($booking_details['arrival_to']);?>)</span></td>
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
                                                         <td style="background-color:#d9d9d9;padding:5px;color: #333333;">Bus Type</td>
                                                         <td style="background-color:#d9d9d9;padding:5px;color: #333333;">Ticket Booking</td>
                                                         <td style="background-color:#d9d9d9;padding:5px;color: #333333;">Booking ID</td>
                                                         <td style="background-color:#d9d9d9;padding:5px;color: #333333;">Boarding Pickup Time</td>
                                                      </tr>
                                                      <tr>
                                                         <td style="padding:5px"><span style="width:100%; float:left"><?php echo $booking_details['bus_type'];?></span></td>
                                                         <td style="padding:5px"><span style="width:100%; float:left"><?php echo ucfirst($booking_details['departure_from']).' To '.ucfirst($booking_details['arrival_to']);?></span></td>
                                                         <td style="padding:5px"><?=@$ticket_num?></td>
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
                                 <td style="padding:0;"><table border="0" cellspacing="0" cellpadding="5" style="font-size:9pt;background-color:#00a9d6;border: 1px solid #00a9d6; padding:0;">
                                       <tbody>
                                          <tr>
                                             <td style=" color:#fff"><img width="16" src="<?=FULL_SYSTEM_IMAGE_DIR.'people_group.png'?>"> &nbsp;<span style="vertical-align:middle;font-size:10pt">Traveler(s) Information</span></td>
                                          </tr>
                                       </tbody>
                                    </table>
                                 </td>
                              </tr>
                              <tr>
                                 <td width="100%" style="border: 1px solid #666666; padding:0px;"><table width="100%" cellpadding="5" style="padding: 10px;font-size: 10pt;">
                                       <tbody>
                                          <tr>
                                             <td style="background-color:#d9d9d9;padding:3px;color: #333333;">Sr No.</td>
                                             <td style="background-color:#d9d9d9;padding:3px;color: #333333;">Passenger(s) Name</td>
                                             <td style="background-color:#d9d9d9;padding:3px;color: #333333;">Gender</td>
                                             <td style="background-color:#d9d9d9;padding:3px;color: #333333;">Seat No</td>
                                             <td style="background-color:#d9d9d9;padding:3px;color: #333333;">Pnr No</td>
                                             <td style="background-color:#d9d9d9;padding:3px;color: #333333;">Status</td>
                                          </tr>
                                          <?php 
                                             $i=1;
                                             ?>
                                          <?php foreach ($customer_details as $key => $value) { ?>
                                          <tr>
                                             <td style="padding:3px;"><?=$i;?></td>
                                             <td style="padding:3px"><?php echo $value['name'];?></td>
                                             <td style="padding:3px;"><?php echo $value['gender'];?></td>
                                             <td style="padding:3px;"><?php echo $value['seat_no'];?></td>
                                             <td style="padding:3px;"><?php 
                                 if(count($customer_details) > 1 && count($pnrs) > 1){
                                    echo $pnrs[$key];
                                 }else{
                                    echo $pnrs[0];
                                 }
                                 ?></td>
                                 <td style="padding:5px;"><?php echo get_bus_status_label($value['status']);?></td>
                                          </tr>
                                          <?php $i++; } ?>
                                       </tbody>
                                    </table>
                                 </td>
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
                                                         <td style="padding:5px"><span><?=@$booking_details['grand_total']-$booking_details['gst']-$booking_details['total_api_tax']-$booking_details['convinence_amount']?></span></td>
                                                      </tr>
                                                     
                                                       <?php if($booking_details['gst'] > 0){?>
                                                     <tr>
                                                         <td style="padding:5px"><span>GST</span></td>
                                                         <td style="padding:5px"><span><?=@roundoff_number($booking_details['gst'])?></span></td>
                                                      </tr>
                                                      <?php } ?>
													  <?php if($booking_details['total_api_tax'] > 0){ ?>
														   <tr>
															   <td style="padding:5px"><span>AC Service Charge</span></td>
															   <td style="padding:5px"><span><?=@roundoff_number($booking_details['total_api_tax'])?></span></td>
															</tr>
														<?php } ?>
													  <?php if($booking_details['convinence_amount'] > 0){ ?>
													   <tr>
														   <td style="padding:5px"><span>Convenience</span></td>
														   <td style="padding:5px"><span><?=@roundoff_number($booking_details['convinence_amount'])?></span></td>
														</tr>
														<?php } ?>
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
                                 <td align="center" style="border-bottom:1px solid #999999;padding-bottom:15px"><span style="font-size:10pt; line-height: 25px; color:#555;">Customer Contact Details | E-mail : <?php echo $booking_details['email'];?> | Contact No : <?php echo $booking_details['phone_number'];?></span></td>
                              </tr>
                              <tr>
                                 <td style="line-height:12px;">&nbsp;</td>
                              </tr>
                              <tr>
                                 <td align="right" style="padding-top:10px;font-size:10pt;line-height:18px;"><!-- <?=$data['domainname']?> <br>ContactNo : <?=$data['phone']?><br><?=$data['address']?> --></td>
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