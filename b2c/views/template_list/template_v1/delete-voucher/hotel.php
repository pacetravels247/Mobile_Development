<?php
$adult_count = 0;
$child_count = 0;
$passengers = '';
foreach ($booking_customer_details as $k => $v) {
	if (strtolower($v['pax_type']) == 'child') {
		$child_count++;
	} else {
		$adult_count++;
	}
	$passengers .= '<li>';
		$passengers .= ($k+1).')'.$v['title'].' '.$v['first_name'].' '.$v['middle_name'].' '.$v['last_name'].'- '.($v['pax_type']).' - Passport Number : '.(empty($v['passport_number']) == false ? $v['passport_number'] : 'N/A');
	$passengers .= '</li>';
}
?>

<div class="mobVouch"><table width="90%" bordercolor="#dedcdc" border="1" cellspacing="0"
	cellpadding="5"
	style="font-family: Arial, Helvetica, sans-serif; margin: auto; font-size: 12px; color: #444;">
	<tbody>
		<tr style="font-size: 12px;">
			<td colspan="2">
				<table width="100%" border="0" cellspacing="0" cellpadding="1">
					<tbody>
						<tr>
							<th align="left" scope="col">
								<img src="<?=$GLOBALS['CI']->template->domain_images($domain_details['domain_logo'])?>" height="70" />
							</th>
						</tr>
						<tr>
							<th width="100%" align="left" scope="col">
								<table width="100%" cellspacing="0" cellpadding="5"
									style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #444;">
									<tbody>
										<tr>
											<td width="50%" align="left"><strong>Booking Reference : <?php echo $booking_details['booking_reference'];?></strong></td>
											<td width="50%" align="right"><strong>Booking Status : <?php echo $booking_details['status'];?></strong></td>
										</tr>
									</tbody>
								</table>
							</th>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td width="50%" valign="top">
				<div>
					<table width="99%" border="0" bordercolor="#f0eaea" cellspacing="0"
						cellpadding="3"
						style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #444;">
						<tbody>
							<tr>
								<td bgcolor="#F7941E" style="color: #fff;" colspan="2"><strong>Traveller
									Details</strong>
								</td>
							</tr>
							<tr>
								<td width="37%"><strong>Guest Name</strong></td>
								<td width="63%">: <?php echo $booking_pax_details[0]['title'].' '.$booking_pax_details[0]['first_name'].' '.$booking_pax_details[0]['middle_name'].' '.$booking_pax_details[0]['last_name'];?></td>
							</tr>
							<tr>
								<td class="fbold"><strong>Adult (s)</strong></td>
								<td>: <?php echo $adult_count;?></td>
							</tr>
							<tr>
								<td class="fbold"><strong>Childern (s)</strong></td>
								<td>: <?php echo $child_count;?></td>
							</tr>
							<tr>
								<td class="fbold"><strong>Voucher Date</strong></td>
								<td>: <?php echo app_friendly_date($booking_details['created_datetime']);?></td>
							</tr>
							<tr>
								<td class="fbold"><strong>Confirmation Ref No</strong></td>
								<td>: <?php echo $booking_details['confirmation_reference'];?></td>
							</tr>
						</tbody>
					</table>
				</div>
			</td>
			<td width="50%" valign="top">
				<table width="99%" border="0" bordercolor="#dddcdc" cellspacing="0"
					cellpadding="3"
					style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #444;">
					<tbody>
						<tr>
							<td bgcolor="#F7941E" style="color: #fff;" colspan="2">
								<strong>Stratosphere ref no : <?php echo $booking_details['app_reference'];?></strong>
							</td>
						</tr>
						<tr>
							<td class="fbold"><strong>Hotel</strong></td>
							<td>: <?=$booking_details['hotel_name']?></td>
						</tr>
						<tr>
							<td width="37%"><strong>Check - in</strong></td>
							<td width="63%">: <?php echo app_friendly_date($booking_details['hotel_check_in']);?></td>
						</tr>
						<tr>
							<td class="fbold"><strong>Check - out</strong></td>
							<td>: <?php echo app_friendly_date($booking_details['hotel_check_out']);?></td>
						</tr>
						<tr>
							<td class="fbold"><strong>Rooms</strong></td>
							<td>: <?=count($booking_itinerary_details)?> Room(s)</td>
						</tr>
						<tr>
							<td class="fbold"><strong>Nights</strong></td>
							<td>: <?php echo get_date_difference($booking_details['hotel_check_in'], $booking_details['hotel_check_out']);?> Nights</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<table width="100%"
					style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #444;">
					<tbody>
						<tr>
							<td height="22" bgcolor="#FFFFFF" style="color: #000;"
								class="vocher_headline"><strong>Hotel Details</strong></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<table width="100%"
					style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #444;">
					<tbody>
						<tr>
							<td class="fbold"><?=$booking_details['hotel_name']?>, <?=$booking_itinerary_details[0]['location']?></td>
						</tr>
						<tr>
							<td><?=@$booking_details['hotel_address']?></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<table width="100%"
					style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #444;">
					<tbody>
						<tr>
							<td bgcolor="#fff" style="color: #000;" class="vocher_headline"><strong>Room
								Details</strong>
							</td>
						</tr>
						<?php
						foreach($booking_itinerary_details as $k => $v) {
							$sp = get_smoking_preference($v['smoking_preference']);
							echo '<tr><td width="100%" class="room_details" colspan="2">';
								echo 'Room '.($k+1).') '.$v['room_type_name'].', Smoking Preference : '.$sp['label'];
							echo '</td></tr>';
						}
						?>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<table width="100%" cellpadding="5"
					style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #444;">
					<tbody>
						<tr>
							<td bgcolor="#F7941E" style="color: #fff;" class="vocher_headline"><strong>Customer
								Details</strong>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<?php
		$attributes = @unserialize($booking_details['attributes']);
		?>
		<tr>
			<td bgcolor="#EDEFED">
				<table width="100%" bordercolor="#999999" border="1" cellspacing="0"
					cellpadding="5"
					style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #444;">
					<tbody>
						<tr>
							<td width="30%" class="fbold">User Name</td>
							<td width="70%"><?php echo $booking_pax_details[0]['title'].' '.$booking_pax_details[0]['first_name'].' '.$booking_pax_details[0]['middle_name'].' '.$booking_pax_details[0]['last_name'];?></td>
						</tr>
						<tr>
							<td width="30%" class="fbold">Email ID</td>
							<td width="70%"><?php echo $booking_pax_details[0]['email'];?></td>
						</tr>
						<tr>
							<td width="30%" class="fbold">Address</td>
							<td width="70%"><?php echo $attributes['address'];?></td>
						</tr>
					</tbody>
				</table>
			</td>
			<td valign="top" bgcolor="#EDEFED">
				<table width="100%" bordercolor="#999999" border="1" cellspacing="0"
					cellpadding="5"
					style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #444;">
					<tbody>
						<tr>
							<td class="fbold">City</td>
							<td><?php echo $attributes['billing_city'];?></td>
						</tr>
						<tr>
							<td class="fbold">Country</td>
							<td><?php echo $attributes['billing_country'];?></td>
						</tr>
						<tr>
							<td class="fbold">Zip Code</td>
							<td><?php echo $attributes['billing_zipcode'];?></td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<table width="100%" cellpadding="5"
					style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #444;">
					<tbody>
						<tr>
							<td bgcolor="#F7941E" style="color: #fff;" class="vocher_headline"><strong>Passenger
								Details</strong>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<table width="100%" cellspacing="0" cellpadding="5"
					style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #444;">
					<tbody>
						<tr>
							<td class="fbold">
								<ul style="list-style: none">
									<?php
									echo $passengers;
									?>
								</ul>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<table width="100%" cellspacing="0" cellpadding="5"
					style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #444;">
				</table>
			</td>
		</tr>
		<tr id="mediaPrint">
			<td align="center" colspan="3"><a style="text-decoration: none"
				onclick="javascript:window.print();return false;" href="#"> <img
				width="35px;" src="<?php echo $GLOBALS['CI']->template->template_images('print.png'); ?>"
				title="Print"> </a></td>
		</tr>
	</tbody>
</table></div>
<style>
	@media print {
	.mediaContainer,.header,.footerbgcolor,.footercontent,.footerImage,#container,#mediaPrint
	{
	display: none;
	}
	}
</style>