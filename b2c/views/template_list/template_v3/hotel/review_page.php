<?php
  // debug($guest_data);exit;
 ?>
 <div id="editPax" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit</h4>
      </div>
      <div class="modal-body">
     
      </div>
    </div>
  </div>
</div>
<div style="position:relative" class="container table-responsive" id="tickect_hotel">
   <table cellpadding="0" cellspacing="0" width="100%" style="font-size:13px; font-family: 'Open Sans', sans-serif; margin:0px auto;background-color:#fff; padding:50px 45px;">
      <tbody>
         <tr>
            <td style="border-collapse: collapse; padding:30px 35px;" >
               <table width="100%" style="border-collapse: collapse;" cellpadding="0" cellspacing="0" border="0">
                  <tr>
                     <td style="padding: 0px;">
                        <table cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse: collapse;">
                           <tr><td style="line-height:12px;">&nbsp;</td></tr>      
                           <tr>
                              <td style="background-color:#003f6a;border: 1px solid #003f6a; color:#fff; font-size:15px; padding:5px;"><img style="vertical-align:middle" src="<?='http://'.$_SERVER['HTTP_HOST'].SYSTEM_IMAGE_DIR.'hotel_v.png'?>" /> <span style="font-size:15px;color:#fff;vertical-align:middle;"> &nbsp;Hotel Details</span></td>
                           </tr>
                           <tr>
                              <td  width="100%" style="border: 1px solid #003f6a; padding:0px;">
                                 <table width="100%" cellpadding="5" style="padding: 10px;font-size: 14px;padding:5px;">
                                    <tr>
                                       <!-- <td>Phone</td> -->
                                   <td style="background-color:#d9d9d9;padding:5px;color: #333333;">Check-In</td>
                                       <td style="background-color:#d9d9d9;padding:5px;color: #333333;">Check-Out</td>
                                       <td style="background-color:#d9d9d9;padding:5px;color: #333333;text-align:center">No of Room's</td>
                                       <td style="background-color:#d9d9d9;color: #333333;">Hotel Name</td>
                                       <td style="background-color:#d9d9d9;padding:5px;color: #333333;">Room Type</td>
                                       <td style="background-color:#d9d9d9;padding:5px;color: #333333;text-align:center">Adult's</td>
                                       <td style="background-color:#d9d9d9;padding:5px;color: #333333;text-align:center">Children</td>
                                    </tr>
                                    <tr>
                                       <td style="padding:5px"><span style="width:100%; float:left"> <?=@date("d M Y",strtotime($hotel_data['checkin_date']))?></span></td>
                                       <td style="padding:5px"><span style="width:100%; float:left"> 	<?=@date("d M Y",strtotime($hotel_data['checkin_date']))?></span></td>
                                       <td style="padding:5px" align="center"><?php echo $hotel_data['room_count']; ?></span></td>
                                       <td><?php echo $hotel_data['HotelName']; ?></span></td>
                                       <td style="padding:5px"  width="13%"><?php echo $hotel_data['RoomTypeName']; ?></td>
                                       <td style="padding:5px" align="center"><?php echo $hotel_data['adult_config']; ?></td>
                                       <td  style="padding:5px" align="center"><?php echo $hotel_data['child_config']; ?></td>
                                    </tr>
                                 </table>
                              </td>
                           </tr>
                           <tr><td style="line-height:12px;">&nbsp;</td></tr>
                           <tr>
                              <td style="background-color:#003f6a;border: 1px solid #003f6a; color:#fff; font-size:15px; padding:5px;"><img style="vertical-align:middle" src="<?='http://'.$_SERVER['HTTP_HOST'].SYSTEM_IMAGE_DIR.'people_group.png'?>" /> <span style="font-size:15px;color:#fff;vertical-align:middle;"> &nbsp;Guest(s) Details</span></td>
                           </tr>
                           <tr>
                              <td  width="50%" style="border: 1px solid #003f6a; padding:0px;">
                                 <table width="100%" cellpadding="5" style="padding: 10px;font-size: 14px;" id="guest_details">
                                    <tr>
                                       <td style="background-color:#d9d9d9;padding:5px;color: #333333;">Sr No.</td>
                                       <td style="background-color:#d9d9d9;padding:5px;color: #333333;">Passenger(s) Name</td>
                                       <td style="background-color:#d9d9d9; color:#555555"><span>Edit</span><button type="button" class="edit_pax" ><i class="fa fa-pencil"></i></button></td>
                                       <td style="background-color:#d9d9d9; color:#555555" class="save hide"><span>Save</span><button type="button" class="edit_done" ><i class="fa fa-pencil"></i></button></td>
                                    </tr>
                                    
                                    
                                   <?php 
                                   
                                 // debug($guest_data);exit;
                                   for ($i=0; $i < count($guest_data['first_name']); $i++) {
                                    $name = $guest_data['first_name'][$i].' '.$guest_data['last_name'][$i];
                                    ?>
                                    <tr class="td_non_edit">
                                                        
                                       <td style="padding:5px;"><?=$i+1?></td>
                                       <td style="padding:5px" class="pass_input_<?=$i+1?>"><?php echo $guest_data['title'][$i].' '.$name?></td>
                                       
                                       
                                    </tr>
                                    <?php } for ($i=0; $i < count($guest_data['first_name']); $i++) {?>
                                     <tr class="td_edit hide">
                                       <td>
                                          <input type="text" name="first_name_<?php echo $i ?>" class="pass_input_first_txt_<?=$i+1?>" input_val="<?=$i+1?>" value="<?php echo $guest_data["first_name"][$i] ?>"> 
                                          <input type="text" name="last_name_<?php echo $i ?>" class="pass_input_last_txt_<?=$i+1?>" input_val="<?=$i+1?>" value="<?php echo $guest_data["last_name"][$i] ?>"><br>
                                      </td>
                                    </tr>
                                    <?php } ?>
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
												<td width="50%" style="padding:0;">
													<table cellspacing="0" cellpadding="5" width="100%" style="font-size:12px; padding:0;border:1px solid #9a9a9a;">
														<tbody>
															<tr>
																<td style="border-bottom:1px solid #003f6a;padding:5px;background:#003f6a;"><span style="font-size:15px;color: #fff;">Payment Details</span></td>
																<td style="border-bottom:1px solid #003f6a;padding:5px;background:#003f6a;"><span style="font-size:15px;color: #fff;">Amount (<?=$currency_symbol?>)</span></td>
															</tr>
															<tr>
																<td style="padding:5px;font-size: 14px;"><span>Base Fare</span></td>
																<td style="padding:5px;font-size: 14px;"><span><?php echo number_format($price_details['total_amount_val'],2); ?></span></td>
															</tr>
															<tr>
																<td style="padding:5px;font-size: 14px;"><span>Taxes</span></td>
																<td style="padding:5px;font-size: 14px;"><span><?php echo number_format($price_details['convenience_amount'], 2); ?></span></td>
															</tr>
                                             
															<tr>
																<td style="padding:5px;font-size: 14px;"><span>Discount</span></td>
																<td style="padding:5px;font-size: 14px;"><span><?php echo number_format($price_details['discount'],2); ?></span></td>
															</tr>
															
															<tr>
																<td style="border-top:1px solid #d9d9d9;padding:5px;background: #d9d9d9;"><span style="font-size:15px;font-weight: bold;color: #003f6a;">Total Fare</span></td>
																<td style="border-top:1px solid #d9d9d9;padding:5px;background: #d9d9d9;"><span style="font-size:15px"><?= number_format($price_details['grand_total'],2) ?></span></td>
															</tr>
														</tbody>
													</table>
												</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
							<tr><td style="line-height:20px;">&nbsp;</td></tr>
							<tr><td colspan="4" style="border-bottom:1px solid #999999;padding-bottom:15px">Customer Contact Details : </td>
                <td><button type="button" class="edit_customer" ><i class="fa fa-pencil"></i></button></td>
              </tr>
              <tr class="custom_nonedit"><td> <?=$lead_pax['email']?> </td></tr>
              <tr class="custom_nonedit"><td><?=$lead_pax['phone_country_code']?><?=$lead_pax['contact']?></td></tr>
              <tr class="custom_edit hide"><td><input type="text" value="<?=$lead_pax['email']?>" name="customer_email"/></td></tr>
               <tr class="custom_edit hide"><td><input type="text" value="<?=$lead_pax['contact']?>" name="customer_contact"/></td></tr>
            
                     <tr><td style="line-height:30px;">&nbsp;</td></tr>
                     <tr>
                     <?php //debug($lead_pax);exit;?>
                        <td>
                        <a href="<?php echo base_url().'index.php/payment_gateway/payment/'.$book_id.'/'.$book_origin?>">
                        <input type="submit" class="searchsbmt b-proceed-pay" value="Proceed to pay">
                        </a>
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
</div>
<script type="text/javascript">
$(".edit_pax").on("click", function() {
      $('.td_non_edit').addClass('hide');
      $('.td_edit').removeClass('hide');
      $('.save').removeClass('hide');
   });
  $(".edit_done").on("click", function() {
      $('.td_non_edit').removeClass('hide');
      $('.td_edit').addClass('hide');
      $('.save').addClass('hide');
   });
  $(".edit_customer").on("click", function() {
      $('.custom_edit').addClass('hide');
      
      
   });
  
   $(document).on("input", ".td_edit input", function(){
    var input_val = $(this).attr("input_val");
    
    var fname = $(".pass_input_first_txt_"+input_val).val();    
    var lname = $(".pass_input_last_txt_"+input_val).val();
    
    $(".pass_input_"+input_val).text(fname+" "+lname);
   });
   

</script>