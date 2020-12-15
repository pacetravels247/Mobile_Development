<?php  
$booking_details = $data['booking_details'];
$booking_id = $booking_details['app_reference'];
//$booked_date = $booking_details['booking_made_on'];
$booked_date = "";
$booking_made_on = $booking_details['created_datetime'];
$booking_status = $booking_details['status'];

if($booking_status == 'BOOKING_CONFIRMED' || $booking_status == 'BOOKING_CANCELLED') {
	
	$voucher_details = get_voucher_details($booking_details);
} else {
	$booking_status = get_booking_status($booking_status);
	$voucher_details = '<div class="row">';
	$voucher_details .= '<div style="width:100%; float:left; font-family:Arial, Helvetica, sans-serif;"><h4>Bookng Status: '.$booking_status.'</h4></div>';
	$voucher_details .= '<div style="width:100%; float:left; font-family:Arial, Helvetica, sans-serif;"><h4>Message: Voucher Not Available</h4></div>';
	$voucher_details .= '</div>';
}
//if(isset($booking_cancelled) == true && $booking_cancelled == true) {
//	$voucher_title = 'Hotel Cancel Confirmation';
//} else {
//	$voucher_title = 'Hotel Confirmation Voucher';
//}

$voucher_title = 'Hotel Confirmation Voucher';
?>
<div class="container-fluid pad0">
	<div style="width:100%; float:left;">
		<h3 style=" margin: 0 0 20px 0; text-align:center; font-size: 20px; color: #337ab7;"><?php echo $voucher_title;?></h3>
		<div class="hotel_voucher_main_tab" style="float:left; width:75%;">
			<h5 style="margin:0; font-size:13px;"><span style="color:#666; font-family:Arial, Helvetica, sans-serif;">Booking ID :</span> <?php echo $booking_id;?></h5>
			<div style="clear:both;"></div>
			<h5 class="mb0 mt5 colorgray font13" style="font-family:Arial, Helvetica, sans-serif;">Booking Date : <?php echo $booking_made_on;?> Hrs</h5>
		</div>
		<div style="float:left; width:25%;">
			<img style="float:right;" src="<?php echo $GLOBALS['CI']->template->domain_images($GLOBALS['CI']->template->get_domain_logo()); ?>">
		</div>
		<div class="clearfix"></div>

		<hr style="border-top: 1px solid #CCC;  padding: 0;  margin: 20px 0  5px !important;  width: 100%;  clear: both;">

		<?php echo $voucher_details;?>
	</div>
	<?php ?>
</div>
<?php 
		function get_voucher_details($booking_details)
		{
			$voucher_details = '';
			$voucher_details .= '
			<div class="">
				<div style="width:100%; float:left;">';
					$voucher_details .= ge_hotel_details($booking_details);
					$voucher_details .= get_booking_details($booking_details);
					$voucher_details .= get_rooms_details($booking_details);
					$voucher_details .= get_customer_details($booking_details);
					$voucher_details .= get_passenger_details($booking_details);
					return $voucher_details;
		}
		
		// Booking Details 
		function get_booking_details($booking_details)
		{
			$api_booking_details = '<div style="font-size:12px;">
			<input type="hidden" name="login_modal_reload_status" value="1" id="login_modal_reload_status" class="hide">
			<h3 style="margin-top: 0px !important; color: #000; margin-bottom: 10px; font-size: 20px;">Booking Details</h3>
			<div style="clear:both;"></div>
			<div style="box-shadow: 2px 2px 1px #d9d9d9;-o-box-shadow: 2px 2px 1px #d9d9d9;-ms-box-shadow: 2px 2px 1px #d9d9d9;-moz-box-shadow: 2px 2px 1px #d9d9d9;-webkit-box-shadow: 2px 2px 1px #d9d9d9;border: 1px solid #CCC;margin: 0px 10px 5px 0 !important; width: 100%;float: left;padding: 10px;background-color: #FFF;">
				<div>';
				if(valid_array($booking_details)){
						$api_booking_id = $booking_details['booking_reference'];
						$api_confirmation_number = $booking_details['confirmation_reference'];
						
						
						$lead_pax_detail = $booking_details['lead_pax_details'];
						$lead_pax_name = $lead_pax_detail['name'];

						$room_types = $booking_details['itinerary_details'][0]['room_type_name'];
						
						$check_in = $booking_details['hotel_check_in'];
						
						$check_out = $booking_details['hotel_check_out'];
						
						$room_count = count($booking_details['itinerary_details']);
						
						$booking_status = $booking_details['status'];
						
						$passengers = get_passenger_count_detail($booking_details,'count');
						$adult_count = $passengers['adult'];
						$child_count = $passengers['child'];
						
						$api_booking_details .= '<div style="width:100%; float:left;">
						<p style="    word-break: break-all;
						text-align: justify;
						margin: 0;
						color: #646464; margin:0;"><b>HOTEL BOOKING-ID:</b> '.$api_booking_id.' </p>
						<p style="    word-break: break-all;
						text-align: justify;
						margin: 0;
						color: #646464; margin:0;"><b>CONFIRMATION-NUMBER:</b> '.$api_confirmation_number.'</p>
						
						<p style="    word-break: break-all;
						text-align: justify;
						margin: 0;
						color: #646464; margin:0;"><b>BOOKING STATUS:</b> '.$booking_status.'</p>
						
						<p style="    word-break: break-all;
						text-align: justify;
						margin: 0;
						color: #646464; margin:0;"><b>GUEST NAME:</b> '.$lead_pax_name.'</p>
						<p style="    word-break: break-all;
						text-align: justify;
						margin: 0;
						color: #646464; margin:0;"><b>ADULT (S):</b> '.$adult_count.'</p>
						
						<p style="    word-break: break-all;
						text-align: justify;
						margin: 0;
						color: #646464; margin:0;"><b>CHILDERN (S):</b> '.$child_count.'</p>
						
						<p style="    word-break: break-all;
						text-align: justify;
						margin: 0;
						color: #646464; margin:0;"><b>CHECK IN:</b> '.$check_in.'</p>
						
						<p style="    word-break: break-all;
						text-align: justify;
						margin: 0;
						color: #646464; margin:0;"><b>CHECK OUT:</b> '.$check_out.'</p>
						
						<p style="    word-break: break-all;
						text-align: justify;
						margin: 0;
						color: #646464; margin:0;"><b>ROOMS:</b> '.$room_count.' Room(s)</p>
						
						<p style="    word-break: break-all;
						text-align: justify;
						margin: 0;
						color: #646464; margin:0;"><b>NIGHTS:</b> '.get_date_difference($check_in, $check_out).' Nights </p>
					</div>';
				} else {
					$api_booking_details .= '<div class="col-md-3">
					<p style="    word-break: break-all;
					text-align: justify;
					margin: 0;
					color: #646464; margin:0;">Not Available</p>
				</div>';
			}
			$api_booking_details .= '</div>
			</div>
			</div>';
			return $api_booking_details;
		}


	// Rooms Details 
	function get_rooms_details($booking_details)
	{
			$booking_itinerary_details = $booking_details['itinerary_details'];
			$room_booking_details = '<div style="font-size:12px;">
			<h3 style="margin-top: 0px !important; color: #000; margin-bottom: 10px; font-size: 20px;">Room Details</h3>
			<div style="clear:both;"></div>
			<div style="box-shadow: 2px 2px 1px #d9d9d9;-o-box-shadow: 2px 2px 1px #d9d9d9;-ms-box-shadow: 2px 2px 1px #d9d9d9;-moz-box-shadow: 2px 2px 1px #d9d9d9;-webkit-box-shadow: 2px 2px 1px #d9d9d9;border: 1px solid #CCC;margin: 0px 10px 5px 0 !important; width: 100%;float: left;padding: 10px;background-color: #FFF;">
				<div>';
				if(valid_array($booking_details)){
						$room_booking_details .= '<div style="width:100%; float:left;">';
					
						foreach($booking_itinerary_details as $k => $v) {
							$sp = get_smoking_preference($v['smoking_preference']);
							$room_booking_details .= '<p style="    word-break: break-all;
							text-align: justify;
							margin: 0;
							color: #646464; margin:0;">Room '.($k+1).') '.$v['room_type_name'].', Smoking Preference : '.$sp['label'].'</p>';
						}
						
					$room_booking_details .='</div>';
				} else {
					$room_booking_details .= '<div class="col-md-3">
					<p style="    word-break: break-all;
					text-align: justify;
					margin: 0;
					color: #646464; margin:0;">Not Available</p>
				</div>';
			}
			$room_booking_details .= '</div>
			</div>
			</div>';
			return $room_booking_details;
	}
	
	
	//Customer Details
	
	function get_customer_details($booking_details)
	{
			$cus_booking_details = '<div style="font-size:12px;">
			<input type="hidden" name="login_modal_reload_status" value="1" id="login_modal_reload_status" class="hide">
			<h3 style="margin-top: 0px !important; color: #000; margin-bottom: 10px; font-size: 20px;">Customer Details</h3>
			<div style="clear:both;"></div>
			<div style="box-shadow: 2px 2px 1px #d9d9d9;-o-box-shadow: 2px 2px 1px #d9d9d9;-ms-box-shadow: 2px 2px 1px #d9d9d9;-moz-box-shadow: 2px 2px 1px #d9d9d9;-webkit-box-shadow: 2px 2px 1px #d9d9d9;border: 1px solid #CCC;margin: 0px 10px 5px 0 !important; width: 100%;float: left;padding: 10px;background-color: #FFF;">
				<div>';
				if(valid_array($booking_details)){
						$api_booking_id = $booking_details['booking_reference'];
						$api_confirmation_number = $booking_details['confirmation_reference'];
						
						$lead_pax_detail = $booking_details['lead_pax_details'];
						
						$lead_pax_name = $lead_pax_detail['name'];

						$lead_pax_email = $lead_pax_detail['email'];
						
						$attributes = unserialize($booking_details['attributes']);
						
						$cus_booking_details .= '<div style="width:100%; float:left;">
						<p style="    word-break: break-all;
						text-align: justify;
						margin: 0;
						color: #646464; margin:0;"><b>User Name:</b> '.$lead_pax_name.' </p>
						<p style="    word-break: break-all;
						text-align: justify;
						margin: 0;
						color: #646464; margin:0;"><b>Email ID:</b> '.$lead_pax_email.'</p>
						<p style="    word-break: break-all;
						text-align: justify;
						margin: 0;
						color: #646464; margin:0;"><b>Address:</b> '.$attributes['address'].'</p>
						<p style="    word-break: break-all;
						text-align: justify;
						margin: 0;
						color: #646464; margin:0;"><b>City:</b> '.$attributes['billing_city'].'</p>
						
						<p style="    word-break: break-all;
						text-align: justify;
						margin: 0;
						color: #646464; margin:0;"><b>Country:</b> '.$attributes['billing_country'].'</p>
						
						<p style="    word-break: break-all;
						text-align: justify;
						margin: 0;
						color: #646464; margin:0;"><b>Zip Code:</b> '.$attributes['billing_zipcode'].'</p>
						
					</div>';
				} else {
					$cus_booking_details .= '<div class="col-md-3">
					<p style="    word-break: break-all;
					text-align: justify;
					margin: 0;
					color: #646464; margin:0;">Not Available</p>
				</div>';
			}
			$cus_booking_details .= '</div>
		</div>
	</div>';
	return $cus_booking_details;
	}
	
	
	// Passenger Details 
	function get_passenger_details($booking_details)
	{
		$passengers = get_passenger_count_detail($booking_details,'passenger');
			$passenger_details = '<div style="font-size:12px;">
			<h3 style="margin-top: 0px !important; color: #000; margin-bottom: 10px; font-size: 20px;">Passenger Details</h3>
			<div style="clear:both;"></div>
			<div style="box-shadow: 2px 2px 1px #d9d9d9;-o-box-shadow: 2px 2px 1px #d9d9d9;-ms-box-shadow: 2px 2px 1px #d9d9d9;-moz-box-shadow: 2px 2px 1px #d9d9d9;-webkit-box-shadow: 2px 2px 1px #d9d9d9;border: 1px solid #CCC;margin: 0px 10px 5px 0 !important; width: 100%;float: left;padding: 10px;background-color: #FFF;">
				<div>';
				
			$passenger_details .= '<div style="width:100%; float:left;">
			'.$passengers.'</div>';
				
			$passenger_details .= '</div>
			</div>
			</div>';
			return $passenger_details;
	}



function ge_hotel_details($booking_details)
{
	$details = '';
	//Hotel Details
	$details = '<div style="font-size:12px;">
	<input type="hidden" name="login_modal_reload_status" value="1" id="login_modal_reload_status" class="hide">
	<h3 style="margin-top: 0px !important; color: #000; margin-bottom: 0px; font-size: 20px; font-family:Arial, Helvetica, sans-serif;">Hotel Details</h3>
	<div style="clear:both;"></div>
	<div style="box-shadow: 2px 2px 1px #d9d9d9;-o-box-shadow: 2px 2px 1px #d9d9d9;-ms-box-shadow: 2px 2px 1px #d9d9d9;-moz-box-shadow: 2px 2px 1px #d9d9d9;-webkit-box-shadow: 2px 2px 1px #d9d9d9;border: 1px solid #CCC;margin: 0px 10px 5px 0 !important; width: 100%;float: left;padding: 10px;background-color: #FFF;">
		<div>';
			if(valid_array($booking_details)){

				
				$hotel_name = $booking_details['hotel_name'];
				$rating = $booking_details['star_rating'];
				$hotel_address = $booking_details['hotel_location'];
				
				$star_rating = "";
				if(intval($rating) == true){
					$star_rating = '<img src="'.$GLOBALS['CI']->template->template_images('rating/s'.$rating.'.png').'">';		
				}
	
				$details .= '<div style="width:100%; float:left;">
				<p style="    word-break: break-all;
				text-align: justify;
				margin: 0;
				color: #646464; margin:0; font-family:Arial, Helvetica, sans-serif;">
				<b>'.$hotel_name.'</b> 
				<span class="star_img">'.$star_rating.'</span>
				<br />'.$hotel_address.'
			</div>';
			
} else {
	$details .= '<div class="col-md-3">
	<p style="    word-break: break-all;
	text-align: justify;
	margin: 0;
	color: #646464; margin:0; font-family:Arial, Helvetica, sans-serif;">Not Available</p>
</div>';
}
$details .= '</div>
</div>
</div>';
return $details;
}


function get_passenger_count_detail($booking_details, $type = "passenger"){
	$adult_count = 0;
	$child_count = 0;
	$passengers = '';
	foreach ($booking_details['customer_details'] as $k => $v) {
		if (strtolower($v['pax_type']) == 'child') {
			$child_count++;
		} else {
			$adult_count++;
		}
		$passengers .= '<p style="    word-break: break-all;
				text-align: justify;
				margin: 0;
				color: #646464; margin:0;">';
			$passengers .= ($k+1).')'.$v['name'];
		$passengers .= '</p>';
	}
	
	if($type == 'count'){
		return array("adult" => $adult_count, "child" => $child_count);
	}else{
		return $passengers; 
	}
}

?>
