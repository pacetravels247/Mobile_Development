<?php
$booking_details = $data ['booking_details'] [0];
$itinerary_details = $booking_details ['booking_itinerary_details'][0];
$customer_details = $booking_details ['booking_customer_details'];
$domain_details = $booking_details;
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Bus</title>
<style type="text/css">
div, p, a, li, td {
	-webkit-text-size-adjust: none;
}
html {
	width: 100%;
}
body {
	width: 100% !important;
	-webkit-text-size-adjust: 100%;
	-ms-text-size-adjust: 100%;
	margin: 0;
	padding: 0;
}
img {
	outline: none;
	text-decoration: none;
	border: none;
	-ms-interpolation-mode: bicubic;
}
a img {
	border: none;
}
p {
	margin: 0px 0px !important;
}
table td {
	border-collapse: collapse;
}
table {
	border-collapse: collapse;
	mso-table-lspace: 0pt;
	mso-table-rspace: 0pt;
}
td[class=grngv] p {
	font-size: 13px;
}
table[class=sgsmal] tr, table[class=sgsmal] td {
	line-height: 12px;
}
@media only screen and (max-width: 645px) {
	table[class=devicewdt] {
		width: 445px !important;
	}
	td[class=logocvr] {
		clear: both;
		display: block;
		overflow: hidden;
		text-align: center;
		width: 100% !important;
	}
	td[class=prstyle] {
		float: left;
		text-align: left;
		width: auto !important;
	}
	td[class=adstyle] {
		float: right;
		width: auto !important;
	}
	table[class=insidefull] .fullwdth {
		display: block;
		overflow: hidden;
		width: 100% !important;
		text-align: center;
		border-bottom: 1px solid #dddddd;
	}
	table[class=insidefull] .fullwdth:last-child {
		border-bottom: none
	}
}
@media only screen and (max-width: 480px) {
	table[class=devicewdt] {
		width: 290px !important;
	}
}
</style>
</head>
<body>
	<table
		style="max-width: 645px; margin: 10px auto; border: 1px solid #dddddd;"
		class="devicewdt">
		<tr>
			<td style="padding: 10px;">
				<table
					style="width: 100%; color: #666; font-size: 13px; font-family: arial, serif; line-height: 22px;">
					<tr>
						<td>
							<table style="width: 100%">
								<tr>
									<td style="width: 33.33%" class="logocvr"><img
										style="max-width: 150px; height: 40px;"
										src="<?=$GLOBALS['CI']->template->domain_images($GLOBALS['CI']->template->get_domain_logo())?>"
										alt="" /></td>
									<td style="width: 33.33%" class="prstyle"><img
										style="background-color: #eeeeee; border: 1px solid #dddddd; border-radius: 5px; 
										display: block; margin: 0 auto; padding: 10px; cursor: pointer;"
										onclick="javascript:window.print();return false;"
										src="<?=$GLOBALS ['CI']->template->domain_images('email_images/print.png');?>"
										alt="" /></td>
									<td style="width: 33.33%; text-align: right" class="adstyle"></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td
							style="color: #444444; display: block; font-size: 20px; overflow: hidden; padding: 10px 0; text-align: center;">
							Confirmation Letter</td>
					</tr>
					<tr>
						<td style="font-size: 15px; color: #444444;" class="grngv">Hello,
							<?=@$booking_details['lead_pax_name']?><br />
							<p>Please check on your dates of travel and take quick contact
								with your host to discuss all the details of your arrival.</p>
						</td>
					</tr>
					<tr>
						<td
							style="border-bottom: 1px solid #dddddd; color: #444444; display: block; font-size: 18px; 
							margin: 10px 0 15px; overflow: hidden; padding: 10px 0;">
							Itinerary</td>
					</tr>
					<tr>
						<td>
							<table style="width: 100%" class="insideone">
								<tr>
									<td style="width: 33.33%">Booking Status :</td>
									<td style="color:#333"><?=@$booking_details['status']?></td>
								</tr>
								<tr>
									<td>Confirmation No :</td>
									<td style="color: #333"><?=@$booking_details['app_reference']?></td>
								</tr>
								<tr>
									<td>Travellers :</td>
									<td style="color: #333"> <?=$booking_details['pax_count']?></td>
								</tr>
								<tr>
									<td>Seats :</td>
									<td style="color: #333"><?=$booking_details['seat_numbers']?></td>
								</tr>
								<tr>
									<td>Boarding Details :</td>
									<td style="color: #333"><?=$itinerary_details['boarding_from']?></td>
								</tr>
								<tr>
									<td>Total Amount :</td>
									<td style="color: #333"><?=@$booking_details['currency'].$booking_details['grand_total'];?></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table
								style="width: 100%; margin-top: 15px; border: 1px solid #ddd;"
								class="insideone">
								<tr style="border-bottom: 1px solid #ddd;">
									<td style="padding: 5px 0;">
										<table style="width: 100%;" class="insidefull">
											<tr>
												<td style="width: 25%" class="fullwdth">
													<table style="width: 100%;">
														<tr>
															<td style="text-align: center">
															<?=$booking_details['operator']?>
																</td>
															<td>
																<table class="sgsmal">
																	<tr>
																		<td style="padding-bottom: 5px;"><?=$booking_details['bus_type']?></td>
																	</tr>
																	<!-- 
																	<tr>
																		<td style="font-size: 10px; color: #999;"><?//=@$itinerary_details[$i]['airline_code']?></td>
																	</tr>
																	 -->
																</table>
															</td>
														</tr>
													</table>
												</td>
												<td style="width: 58.33%" class="fullwdth">
													<table style="width: 100%">
														<tr>
															<td style="width: 41.66%;">
																<table style="width: 100%">
																	<tr>
																		<td
																			style="font-size: 14px; color: #444; font-weight: bold;"><?=ucfirst($booking_details['departure_from'])?></td>
																	</tr>
																	<tr>
																		<td><?php echo date("d M",strtotime($booking_details['departure_datetime'])).", ".date("H:i",strtotime($booking_details['departure_datetime']));?></td>
																	</tr>
																</table>
															</td>
															<td style="width: 16.66%"><img style="width: 30px;"
																alt="" src="<?=$GLOBALS ['CI']->template->domain_images('email_images/right_arrow.png');?>"></td>
															<td style="width: 41.66%;">
																<table style="width: 100%">
																	<tr>
																		<td
																			style="font-size: 14px; color: #444; font-weight: bold;"><?=ucfirst($booking_details['arrival_to'])?></td>
																	</tr>
																	<tr>
																		<td><?php echo date("d M",strtotime($booking_details['arrival_datetime'])).", ".date("H:i",strtotime($booking_details['arrival_datetime']));?></td>
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
								<!-- <tr>
									<td style="padding: 5px 0;">
										<table style="width: 100%;" class="insidefull">
											<tr>
												<td style="width: 25%" class="fullwdth">
													<table style="width: 100%;">
														<tr>
															<td style="text-align: right"><img alt=""
																src="<?=$GLOBALS ['CI']->template->domain_images('email_images/smAI.gif');?>"></td>
															<td>
																<table class="sgsmal">
																	<tr>
																		<td style="padding-bottom: 5px;">SpiceJet</td>
																	</tr>
																	<tr>
																		<td style="font-size: 10px; color: #999;">SG-13 Boeing</td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>
												</td>
												<td style="width: 58.33%" class="fullwdth">
													<table style="width: 100%">
														<tr>
															<td style="width: 41.66%;">
																<table style="width: 100%">
																	<tr>
																		<td
																			style="font-size: 14px; color: #444; font-weight: bold;">Mumbai
																			(BOM)</td>
																	</tr>
																	<tr>
																		<td>26 Jun, 08:30</td>
																	</tr>
																</table>
															</td>
															<td style="width: 16.66%"><img style="width: 30px;"
																alt="" src="<?=$GLOBALS ['CI']->template->domain_images('email_images/right_arrow.png');?>"></td>
															<td style="width: 41.66%;">
																<table style="width: 100%">
																	<tr>
																		<td
																			style="font-size: 14px; color: #444; font-weight: bold;">Dubai
																			(DXB)</td>
																	</tr>
																	<tr>
																		<td>26 Jun, 08:30</td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>
												</td>
												<td style="width: 16.66%; text-align: center"
													class="fullwdth">3h 35m<br>Non Stop
												</td>
											</tr>
										</table>
									</td>
								</tr> -->
							</table>
						</td>
					</tr>
					<tr>
						<td
							style="border-bottom: 1px solid #dddddd; color: #444444; display: block; font-size: 18px; margin: 10px 0 15px; overflow: hidden; padding: 10px 0;">
							Customer Details</td>
					</tr>
					<tr>
						<td>
							<table style="width: 100%" class="insideone">
								<tr>
									<td style="width: 33.33%">Email ID :</td>
									<td style="color: #333"><?=@$booking_details['lead_pax_email']?></td>
								</tr>
								<tr>
									<td>Mobile Number :</td>
									<td style="color: #333"><?=@$booking_details['lead_pax_phone_number']?></td>
								</tr>
								<!-- 
								<tr>
									<td>Address :</td>
									<td style="color: #333"><?//=@$booking_details['cutomer_address'].",".$booking_details['cutomer_city'];?>
										560100</td>
								</tr>
								 -->
							</table>
						</td>
					</tr>
					<tr>
						<td
							style="border-bottom: 1px solid #dddddd; color: #444444; display: block; font-size: 18px; margin: 10px 0 15px; overflow: hidden; padding: 10px 0;">
							Terms & Condition</td>
					</tr>
					<tr>
						<td style="padding-bottom: 10px; text-align: justify;"><strong>Bus</strong>
							- We're here to help! If you need assistance with your
							reservation, please visit our Help Center. For urgent
							situations,: such as check-in troubles or arriving to something
							unexpected</td>
					</tr>
					<tr>
						<td style="padding-bottom: 10px; text-align: justify;"><strong>Cancellation
								Policies</strong> - We're here to help! If you need assistance
							with your reservation, please visit our Help Center. For urgent
							situations,: such as check-in troubles or arriving to something
							unexpected</td>
					</tr>
					<tr>
						<td style="padding-bottom: 10px; text-align: justify;"><strong>Amendment
								Policies</strong> - We're here to help! If you need assistance
							with your reservation, please visit our Help Center. For urgent
							situations,: such as check-in troubles or arriving to something
							unexpected</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>
<style>
@media print {
	.mediaContainer, .header, .footerbgcolor, .footercontent, .footerImage,
		#container, #mediaPrint {
		display: none;
	}
}
</style>
