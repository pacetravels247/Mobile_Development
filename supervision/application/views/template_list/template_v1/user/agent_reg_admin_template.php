<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	</head>
    <body>
	<?php 
	//debug($agent);
		extract($form_data);
	?>

		<table width="100%">
			<tr>
				<td>
					<table width="80%" border="0" style="border-collapse: collapse; border: 1px solid #dddddd; box-shadow:0px 0px 13px #dddddd;" cellspacing="0" cellpadding="0" align="center">
						<tbody>
							<tr>
								<td style="padding: 15px;">
									<table width="100%">
										<tr>
											<td><img src="<?php echo 'https://admin.pacetravels.net/extras/custom/TMX1512291534825461/images/pace_logo1.png'; ?>" style="height:100px;" alt="<?php echo domain_name();?>"></td>
										</tr>
										<tr>
											<td style="font-size: 15px; text-align:left;line-height:1.4;font-family:Arial,Helvetica,sans-serif;color:#656565;">Dear Pace Team,<br/>
												New agent has been registed on portal, below are the agency details<br/>
												Agency Name:<?=ucwords(@$agency_name)?><br/>
												Contact Number: <?=$office_phone?><br/>
												Email Id: <?=$email?><br/>
												Contact Person: <?=ucwords(@$first_name).' '.ucwords($last_name)?><br/>
											</td>
										</tr>
										<tr>
											<td></td>
										</tr>
										<tr>
											<td>
				  
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</table>
	<!-- End of postfooter -->
	</body>
</html>