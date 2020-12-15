<?php

$booking_details = $data ['booking_details'] [0];
// debug($booking_details);exit;
$itineray_details = $booking_details ['booking_itinerary_details'] [0];
$customer_details = $booking_details ['booking_customer_details'];
$grand_total  = $booking_details['fare']+$booking_details['admin_markup']+roundoff_number($booking_details['convinence_amount'])+roundoff_number($booking_details['gst'])-roundoff_number($booking_details['discount']);
//echo $grand_total;exit;
?>
<table id="tickect_bus" class="table" cellpadding="0" cellspacing="0" width="100%" style="font-size:13px; font-family: 'Open Sans', sans-serif; width:900px; margin:0px auto;background-color:#fff; padding:50px 45px;">
   <tbody>
      <tr>
         <td style="border-collapse: collapse; padding:50px 35px;">
            <table width="100%" style="font-family: 'Open Sans', sans-serif;border-collapse: collapse;" cellpadding="0" cellspacing="0" border="0">
               <tbody>
                  <tr>
                     <td style="padding: 0px;">
                        <table cellpadding="0" cellspacing="0" border="0" width="100%" style="font-family: 'Open Sans', sans-serif;border-collapse: collapse;">
                           <tbody><!-- 
                              <tr>
                                 <td style="font-size:22px; line-height:30px; width:100%; display:block; font-weight:600; text-align:center">E-Ticket</td>
                              </tr> -->
                              <tr>
                                 <td>
                                    <table width="100%" style="font-family: 'Open Sans', sans-serif;border-collapse: collapse;" cellpadding="0" cellspacing="0" border="0">
                                       <tbody>
                                          <tr>
                                             <td style="padding: 0px;"><img style="width:200px;" src="<?=$GLOBALS['CI']->template->domain_images($data['logo'])?>"></td>
                                             <td style="padding: 0px;">
                                                <table width="100%" style="font-size:13px; font-family: 'Open Sans', sans-serif;border-collapse: collapse;text-align: right; line-height:15px;" cellpadding="0" cellspacing="0" border="0">                                     
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
                              <tr>
                                 <td align="right" style="line-height:24px;font-size:13px;border-top:1px solid #00a9d6;border-bottom:1px solid #00a9d6;padding: 5px;">Status: <strong class="<?php echo booking_status_label( $booking_details['status']);?>" style=" font-size:14px;">           <?php
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
                              <tr>
                                 <td style="line-height:12px;">&nbsp;</td>
                              </tr>
                     <tr>
                        <td style="padding:0"><span style="font-size:16px;color:#00a9d6;vertical-align:middle;font-weight: 600;"><?php echo $booking_details['operator'];?></span></td>
                     </tr>
                     <tr>
                                 <td width="100%" style="padding:0px;">
                                 <tr>
                                 <table width="100%" cellpadding="5" style="padding: 10px;font-size: 13px;">
                           <td width="45%" style="padding:5px 0; line-height:25px;"><span style="display: block;"><span style="font-size:14px;font-weight: 600;">Boarding Point</span><br><span style="font-size:14px;vertical-align:middle;"><?php echo $itineray_details['boarding_from'];?></span></span></td>
                           <td width="30%" style="padding:5px 0; line-height:25px;vertical-align: top;"><span style="display: block;"><span style="font-size:14px;font-weight: 600;">Dropping at</span><br><span style="font-size:14px;vertical-align:middle;"><?php echo $itineray_details['dropping_at'];?></span></span></td>
                           <td width="25%" style="padding:5px 0;text-align: center;"><span style="font-size:14px; border:2px solid #808080; display:block"><span style="color:#00a9d6;padding:5px; display:block">PNR No</span><span style="font-size:22px;line-height:35px;padding-bottom: 5px;display:block;font-weight: 600;"><?php echo $booking_details['pnr'];?></span><span style="border-top:2px solid #808080;display:block; padding:5px;">Seat: <?=@$booking_details['seat_numbers']?></span></span></td>
                           </table>
                          </tr>
                         </td>
                     </tr>
                              <tr>
                                 <td style="line-height:12px;">&nbsp;</td>
                              </tr>
                              <tr>
                                 <td style="background-color:#00a9d6;border: 1px solid #00a9d6; color:#fff; font-size:14px; padding:5px;"><img style="vertical-align:middle" src="<?=SYSTEM_IMAGE_DIR.'bus_v.png'?>"> <span style="font-size:14px;color:#fff;vertical-align:middle;"> &nbsp;Reservation Ticket (<?php echo ucfirst($booking_details['departure_from']).' To '.ucfirst($booking_details['arrival_to']);?>)</span></td>
                              </tr>
                              <tr>
                                 <td width="100%" style="border: 1px solid #00a9d6; padding:0px;">
                                    <table width="100%" cellpadding="5" style="padding: 10px;font-size: 13px;padding:5px;">
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
                              <tr>
                                 <td style="line-height:12px;">&nbsp;</td>
                              </tr>
                              <tr>
                                 <td style="background-color:#666666;border: 1px solid #666666; color:#fff; font-size:14px; padding:5px;"><img style="vertical-align:middle" src="<?=SYSTEM_IMAGE_DIR.'people_group.png'?>"> <span style="font-size:14px;color:#fff;vertical-align:middle;"> &nbsp;Traveler(s) Information</span></td>
                              </tr>
                              <tr>
                                 <td width="100%" style="border: 1px solid #666666; padding:0px;">
                                    <table width="100%" cellpadding="5" style="padding: 10px;font-size: 13px;">
                                       <tbody>
                                          <tr>
                                             <td style="background-color:#d9d9d9;padding:5px;color: #333333;">Sr No.</td>
                                             <td style="background-color:#d9d9d9;padding:5px;color: #333333;">Passenger(s) Name</td>
                                             <td style="background-color:#d9d9d9;padding:5px;color: #333333;">Gender</td>
                                             <td style="background-color:#d9d9d9;padding:5px;color: #333333;">Seat No</td>
                                          </tr>  
                              <?php 
                              // debug($customer_details);exit;
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
                                 <td colspan="4" style="padding:0;">
                                    <table cellspacing="0" cellpadding="5" width="100%" style="font-size:12px; padding:0;">
                                       <tbody>
                                          <tr>
                                             <td width="50%" style="padding:0;padding-right:14px;">
                                                <table cellspacing="0" cellpadding="5" width="100%" style="font-size:12px; padding:0;border:1px solid #9a9a9a;">
                                                   <tbody>
                                                      <tr>
                                                         <td style="border-bottom:1px solid #ccc;padding:5px;"><span style="font-size:13px">Payment Details</span></td>
                                                         <td style="border-bottom:1px solid #ccc;padding:5px;"><span style="font-size:11px">Amount (<?=@$booking_details['currency']?>)</span></td>
                                                      </tr>
                                                      <?php //debug($booking_details);exit;?>
                                                      <tr>
                                                         <td style="padding:5px"><span>Base Fare</span></td>
                                                         <td style="padding:5px"><span><?=@$booking_details['fare']+$booking_details['admin_markup']?></span></td>
                                                      </tr>
                                                       <?php if($booking_details['convinence_amount'] > 0){?>
                                                     <tr>
                                                         <td style="padding:5px"><span>Tax</span></td>
                                                         <td style="padding:5px"><span><?=@roundoff_number($booking_details['convinence_amount'])?></span></td>
                                                      </tr>
                                                      <?php } ?>
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
                                                         <td style="border-top:1px solid #ccc;padding:5px"><span style="font-size:13px">Total Fare</span></td>
                                                         <td style="border-top:1px solid #ccc;padding:5px"><span style="font-size:13px"><?=@$grand_total?></span></td>
                                                      </tr>
                                                   </tbody>
                                                </table>
                                             </td>
                                             <td width="50%" style="padding:0;padding-left:14px; vertical-align:top">
                                                <table cellspacing="0" cellpadding="5" width="100%" style="border:1px solid #9a9a9a;font-size:12px; padding:0;">
                                                   <tbody>

                        <?php 
                        $cancellation_policy = $booking_details['cancel_policy'];
                        $cancellation_policy = json_decode(base64_decode($cancellation_policy));
                        // debug($cancellation_policy);exit;
                        ?>
                                                      <tr>
                                                         <td colspan="2" style="border-bottom:1px solid #ccc;padding:5px; color:#333"><span style="font-size:13px">Cancellation Policy</span></td>
                                                      </tr>
                                                      <tr>
                                                         <td style="background-color:#d9d9d9; color:#555555;padding:5px"><span>Cancellation Time</span></td>
                                                         <td style="background-color:#d9d9d9; color:#555555;padding:5px; white-space:nowrap"><span>Cancellation Charges</span></td>
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
                               </tr>
                              <tr>
                     <td colspan="4"><span style="line-height:26px;font-size: 14px;font-weight: 500;">Terms and Conditions</span></td></tr>
                     <tr>
                        <td colspan="4" style="line-height:20px; border-bottom:1px solid #999999; padding-bottom:15px; font-size:12px; color:#555"><?php echo $data['terms_conditions']; ?></td>
                     </tr>
                              <tr>
                                 <td align="center" colspan="4" style="border-bottom:1px solid #999999;padding-bottom:15px"><span style="font-size:13px; color:#555;">Customer Contact Details | E-mail : <?php echo $booking_details['email'];?> | Contact No : <?php echo $booking_details['phone_code']; echo " "; echo $booking_details['phone_number'];?></span></td>
                              </tr>
                              <tr>
                                 <td style="line-height:12px;">&nbsp;</td>
                              </tr>
                              <tr>
                                 <td colspan="4" align="right" style="padding-top:10px;font-size:13px;line-height:20px;"><?=$data['domainname']?> <br>ContactNo :<?=$data['phone_code']?> <?=$data['phone']?><br>
                                 <?=$data['address']?></td>
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
<table id="printOption"onclick="w=window.open();w.document.write(document.getElementById('tickect_bus').innerHTML);w.print();w.close(); return true;"
 style="border-collapse: collapse;font-size: 14px; margin: 10px auto; font-family: arial;" width="70%" cellpadding="0" cellspacing="0" border="0">
<tbody>
   <tr>
    <td align="center"><input style="background: #00a9d6;padding: 6px 20px;border-radius:4px;border:none;color:#fff;margin: 0;" type="button" value="Print" />
    
    </tr>
</tbody></table>
</div>