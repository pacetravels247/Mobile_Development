<style type="text/css">
th, td {
	padding: 5px
}
</style>
<?php
$booking_details = $data ['booking_details'] [0];

$itineray_details = $booking_details ['booking_itinerary_details'] [0];
$customer_details = $booking_details ['booking_customer_details'];

?>
<div class="table-responsive">
<table
	style="border-collapse: collapse; background: #ffffff; font-size: 14px; margin: 0 auto; font-family: arial;"
	width="100%" cellpadding="0" cellspacing="0" border="0">
	<tbody>
		<tr>
			<td style="border-collapse: collapse; padding: 10px 20px 20px">
				<table width="100%" style="border-collapse: collapse;"
					cellpadding="0" cellspacing="0" border="0">
					
					<tr>
						<td style="padding: 10px;">
							<table cellpadding="5" cellspacing="0" border="0" width="100%"
								style="border-collapse: collapse;">
								
								<tr>
						<td
							style="font-size: 22px; line-height: 30px; width: 100%; display: block; font-weight: 600; text-align: center">E-Ticket</td>
					</tr>
					<tr>
						<td>
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
													style="width: 100%; float: left"><?php echo $data['address'];?></span></td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
								<tr>
									<td width="50%"
										style="padding: 10px; border: 1px solid #cccccc; font-size: 14px; font-weight: bold;">Reservation Ticket (<?php echo ucfirst($booking_details['departure_from']).' To '.ucfirst($booking_details['arrival_to']);?>)</td>
								</tr>
								<tr>
									<td style="border: 1px solid #cccccc;">
										<table width="100%" cellpadding="5"
											style="padding: 10px; font-size: 14px;">
											<tr>
												<td width="20%"><strong>Travel name</strong></td>
												<td width="30%"><?php echo $booking_details['operator'];?></td>
												<td width="20%"><strong>Travel Type</strong></td>
												<td width="30%"><?php echo $booking_details['bus_type'];?></td>

											</tr>
											<tr>
												<td><strong>Ticket Booking</strong></td>
												<td><?php echo ucfirst($booking_details['departure_from']).' To '.ucfirst($booking_details['arrival_to']);?></td>
												<td><strong>Booking Status</strong></td>

												<td><strong
													class="<?php echo booking_status_label( $booking_details['status']);?>"
													style="font-size: 14px;">
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
												<td><strong>PNR No</strong></td>
												<td><?php echo $booking_details['pnr'];?></td>
												<td><strong>Booking ID</strong></td>
												<td><?=@$booking_details['ticket']?></td>

											</tr>
											<tr>
												<td><strong>Boarding Point</strong></td>
												<td><?php echo $itineray_details['boarding_from'];?></td>
												<td><strong>Landmark</strong></td>
												<td><?php echo $itineray_details['boarding_from'];?></td>

											</tr>
											<tr>
												<td><strong>Boarding Pickup Time</strong></td>
												<td><?=@$itineray_details['departure_datetime']?></td>
												<td><strong>Seat</strong></td>
												<td><?=@$booking_details['seat_numbers']?></td>

											</tr>
											<tr>
												<td><strong>Booking Date</strong></td>
												<td><?php echo $booking_details['booked_date'];?></td>
												<td><strong>Travel Date Time</strong></td>
												<td><?=@date("d M Y",strtotime($booking_details['journey_datetime']))?></td>

											</tr>

										</table>
									</td>
								</tr>


								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td
										style="padding: 10px; border: 1px solid #cccccc; font-size: 14px; font-weight: bold;">Travelers
										Information</td>

								</tr>
								<tr>
									<td style="border: 1px solid #cccccc;">
										<table width="100%" cellpadding="5"
											style="padding: 10px; font-size: 14px;">
											<tr>
												<td><strong>Passenger Name</strong></td>
												<td><strong>Phone</strong></td>
												<td><strong>Email ID</strong></td>
												<td><strong>Seat No</strong></td>
											</tr>
										 <?php foreach ($customer_details as $key => $value) { ?>
                                          <tr>

												<td><?php echo $value['name'];?></td>
												<td><?php echo $booking_details['phone_number'];?></td>
												<td><?php echo $booking_details['email'];?></td>
												<td><?php echo $value['seat_no'];?></td>
											</tr>
                            		  <?php } ?>
									</table>
									</td>
									<td></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td
										style="padding: 10px; border: 1px solid #cccccc; font-size: 14px; font-weight: bold;">Price
										Summary</td>

								</tr>
								<tr>
									<td style="border: 1px solid #cccccc;">
										<table width="100%" cellpadding="5"
											style="padding: 10px; font-size: 14px;">
											<tr>
												<td><strong>Base Fare</strong></td>

												<td><strong>Total Fare</strong></td>
											</tr>
											<tr>
												<td><?=@$booking_details['currency']?> <?=@$booking_details['grand_total']?></td>

												<td><?=@$booking_details['currency']?> <?=@$booking_details['grand_total']?></td>
											</tr>
											<tr style="font-size: 15px;">
												<td colspan="5" align="right"><strong>Total Fare</strong></td>
												<td><strong><?=@$booking_details['currency']?> <?=@$booking_details['grand_total']?></strong></td>
											</tr>
										</table>
									</td>
									<td></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td width="50%"
										style="padding: 10px; border: 1px solid #cccccc; font-size: 14px; font-weight: bold;">Terms
										and Conditions</td>
								</tr>
								<tr>
									<td width="100%" style="border: 1px solid #cccccc;">
										<table width="100%" cellpadding="5"
											style="padding: 10px 20px; font-size: 14px;">
											<tr>
												<td>1.Please ensure that operator PNR is filled, otherwise
													the ticket is not valid.</td>
											</tr>
											<tr>
												<td>2.Please ensure that operator PNR is filled, otherwise
													the ticket is not valid.</td>
											</tr>
											<tr>
												<td>3.Please ensure that operator PNR is filled, otherwise
													the ticket is not valid.</td>
											</tr>
											<tr>
												<td>4.Please ensure that operator PNR is filled, otherwise
													the ticket is not valid.</td>
											</tr>
											<tr>
												<td>5.Please ensure that operator PNR is filled, otherwise
													the ticket is not valid.</td>
											</tr>
											<tr>
												<td>6.Please ensure that operator PNR is filled, otherwise
													the ticket is not valid.</td>
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
<table id="printOption"
	onclick="document.getElementById('printOption').style.visibility = ''; print(); return true;"
	style="border-collapse: collapse; font-size: 14px; margin: 10px auto; font-family: arial;"
	width="70%" cellpadding="0" cellspacing="0" border="0">
	<tbody>
		<tr>
			<td align="center"><input
				style="background: #418bca; height: 34px; padding: 10px; border-radius: 4px; border: none; color: #fff; margin: 0 2px;"
				type="button" value="Print" />
		
		</tr>
	</tbody>
</table>
</div>