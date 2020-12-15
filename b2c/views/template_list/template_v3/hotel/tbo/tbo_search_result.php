<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

<?php

$template_images = $GLOBALS['CI']->template->template_images();
$mini_loading_image = '<div class="text-center loader-image">Please Wait</div>';
$loading_image		 = '<div class="spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div><div class="bounce4"></div></div>';
$no_of_nights = $search_params['data']['no_of_nights'];
//debug($raw_hotel_list['HotelSearchResult']['HotelResults']);exit;
foreach ($raw_hotel_list['HotelSearchResult']['HotelResults'] as $hd_key=> $hd) {
	$current_hotel_rate = $hd['StarRating'];
	$hotel_code = preg_replace("/[^a-zA-Z0-9]/", "",$hd['HotelCode']);
	//check image exists in that url or not
	//$file_headers = @get_headers($hd['HotelPicture']);
	$image_found=1;
	// if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
	// 	$image_found = 0;
	// }
?>
<div class="row mt-2 id-hotel-main-row p-0">
	<div class="row" id="result_<?=$hd_key?>" data-key="<?=$hd_key?>" data-hotel-code="<?=$hotel_code?>" data-access-key="<?=@$hd['Latitude'].'_'.@$hd['Longitude']?>">
		<div class="col-sm-3 p-0">
			<div class="id-img-div2">
				<?php 
                                $search_id = intval($attr['search_id']);	
                                if($hd['HotelPicture']&&$image_found==true):?>
<?php  ?>
<!-- <img src="" alt="Hotel img" data-src="<?php //echo base_url().'index.php/hotel/image_cdn/'.$hd['ResultIndex'].'/'.$search_id.'/'.base64_encode($hd['HotelCode'])?>" class="lazy h-img"> -->
					<img data-toggle="modal" data-target="#myGallary_<?=$hd['HotelCode']?>" src="<?=$hd['HotelPicture']?>" alt="Hotel Image" data-src="<?=$hd['HotelPicture']?>" class="lazy h-img load-image hide">

					<img src="<?=$GLOBALS['CI']->template->template_images('image_loader.gif')?>" class='loader-image'>

					<!-- <img src="" alt="Hotel img" data-src="<?php echo base_url().'index.php/hotel/image_hide/'.$hd['ResultIndex'].'/'.$search_id?>" class="lazy h-img"> -->

				<?php else:?>
					<img src="<?=$hd['HotelPicture']?>" alt="Hotel Image" data-src="<?=$GLOBALS['CI']->template->template_images('default_hotel_img.jpg') ?>" class="lazy h-img">
				<?php endif;?>
				<?php
					/**
					 * HOTEL PRICE SECTION With Markup price will be returned
					 * 
					 */
					 //Getting RoomPrice from API per night wise					
									
					//$RoomPrice					= round($hd['Price']['RoomPrice']/$no_of_nights);
					$RoomPrice					= $hd['Price']['RoomPrice'];
					//debug(getimagesize($hd['HotelPicture']));
					?>
				<!-- <img src="" alt="Hotel img" data-src="<?//=$hd['HotelPicture'] ?>" class="lazy h-img"> -->
				<?php if($hd['HotelPicture']&&$image_found==true):?>
				<!--<a data-target="map-box-modal" data-result-token="<?=urlencode($hd['ResultToken'])?>" data-booking-source="<?=urlencode($booking_source)?>" data-price="<?=$RoomPrice?>" data-star-rating="<?=$current_hotel_rate?>"  data-hotel-name="<?php echo $hd['HotelName']?>" id="map_id_<?=str_replace("!","H",$hd['HotelCode'])?>" data-trip-url="<?=$hd['trip_adv_url']?>" data-trip-rating="<?=$hd['trip_rating']?>" data-id="<?=str_replace("!","H",$hd['HotelCode'])?>" class="hotel-image-gal mapviewhtlhotl fal fa-image view-photo-btn" data-hotel-code="<?=$hd['HotelCode']?>"></a> -->
				<?php endif;?>
				<a class="hotel_location" data-lat="<?=@$hd['Latitude']?>" data-lon="<?=@$hd['Longitude']?>"></a>
				
			</div>
		</div>
		 <div class="col-sm-6">
							<div>
					<div class="innd">
					   <div class="imptpldz">
						<div class="property-type" data-property-type="hotel"></div>
						<div class="shtlnamehotl">
							 <h3><?php echo $hd['HotelName']?></h3> 
						</div>
						
						<p data-toggle="tooltip" data-placement="top" data-original-title="<?php echo $hd['HotelAddress']?>"><?php echo $hd['HotelAddress']?>
						</p>
						<div class="starrtinghotl rating-no">
								<span class="h-sr hide"><?php echo $current_hotel_rate?></span>
								<?php echo print_star_rating($current_hotel_rate);?>
						</div>
						<br>
                        <hr class="mt-1 mb-1">
                        <!--<i class="fa fa-wifi fa-meal-icons" aria-hidden="true" title="Wi-Fi"></i>
                        <i class="fa fa-plane fa-meal-icons" aria-hidden="true" title="Flight"></i>
                        <i class="fa fa-cutlery fa-meal-icons" aria-hidden="true" title="Meal"></i>
                        <i class="fa fa-coffee fa-meal-icons" aria-hidden="true" title="Coffee Shop"></i>
                        <i class="fa fa-credit-card fa-meal-icons" aria-hidden="true" title="Credit Card Accepted"></i>
                        <i class="fa fa-taxi fa-meal-icons" aria-hidden="true" title="Cab Service"></i>-->



						<span class="result_token hide"><?=urlencode($hd['ResultToken'])?></span>
						<div class="preclsdv">
							<?php if(isset($hd['Free_cancel_date'])):?>
								<?php if($hd['Free_cancel_date']):?>
						 		 <span class="canplyto"><i class="fa fa-check" aria-hidden="true"></i> Free Cancellation till:<b><?=local_month_date($hd['Free_cancel_date']);?></b></span>
						 		 <input type="hidden" class="free_cancel" type="text" value="1" data-free-cancel="1">
						 		<?php else:?>
						 			<input type="hidden" class="free_cancel" type="text" value="0" data-free-cancel="0">
						 		<?php endif;?>
						 	<?php else:?>
						 		<input type="hidden" data-free-cancel="0" class="free_cancel" type="text" value="0">
							<?php endif;?>

						</div>

						<div class="bothicntri">
						<div class="mwifdiv">
                           <ul class="htl_spr">                         
				         	<?php if(isset($hd['HotelAmenities'])):?>
				         		<?php if($hd['HotelAmenities']):?>
				         			<?php
				         				//debug($hd['HotelAmenities']);
				         			   	$in_search_params = "".strtolower('wireless')."";
										$in_input = preg_quote(@$in_search_params, '~'); // don't forget to quote input string!
										$internet_result = preg_grep('~' . $in_input . '~', $hd['HotelAmenities']);
										$inn_search_params = "Wi-Fi";
										$inn_input = preg_quote(@$inn_search_params, '~'); 
										$innternet_result = preg_grep('~' . $inn_input . '~', $hd['HotelAmenities']);

										//checking free wifi
										
										$wf_search_params = "Wi";
										$wf_input = preg_quote(@$wf_search_params, '~'); 
										$wf_result = preg_grep('~' . $wf_input . '~', $hd['HotelAmenities']);

										$b_search_params = "".strtolower('breakfast')."";
										$b_input = preg_quote(@$b_search_params, '~'); 
										$b_result = preg_grep('~' . $b_input . '~', $hd['HotelAmenities']);
										//checking breakfast 
										$bf_search_params = "Breakfast";
										$bf_input = preg_quote(@$bf_search_params, '~'); 
										$bf_result = preg_grep('~' . $bf_input . '~', $hd['HotelAmenities']);

										$p_search_params = "".strtolower('parking')."";
										$p_input = preg_quote(@$p_search_params, '~'); 
										$p_result = preg_grep('~' . $p_input . '~', $hd['HotelAmenities']);
										//car parking
										$cp_search_params = "".strtolower('park')."";
										$cp_input = preg_quote(@$cp_search_params, '~'); 
										$cp_result = preg_grep('~' . $cp_input . '~', $hd['HotelAmenities']);

										$s_search_params = "pool";
										$s_input = preg_quote(@$s_search_params, '~'); 
										$s_result = preg_grep('~' . $s_input . '~', $hd['HotelAmenities']);
										$swim = "Swim";
								
										$sw_input = preg_quote(@$swim, '~'); 
										$sw_result = preg_grep('~' . $sw_input . '~', $hd['HotelAmenities']);
				         			?>
				         				<?php if($internet_result||$innternet_result|| $wf_result):?>
				         					<li class="wf" data-toggle="tooltip" data-placement="top" title="Wifi"><span>Wifi</span></li>
				         					<input type="hidden" value="filter" id="wifi" class="wifi" data-wifi="1">
				         				<?php else:?>
				         					<input type="hidden" value="filter" id="wifi" class="wifi" data-wifi="0">
				         				<?php endif;?>
				         				<?php if($b_result||$bf_result):?>
				         					<li class="bf" data-toggle="tooltip" data-placement="top" title="Breakfast"><span>Breakfast</span></li>
				         					<input type="hidden" value="filter" id="breakfast" class="breakfast" data-breakfast="1">
				         				<?php else:?>
				         					<input type="hidden" value="filter" id="breakfast" class="breakfast" data-breakfast="0">
				         				<?php endif;?>
				         				<?php if($p_result || $cp_result):?>
				         						 <li class="pr" data-toggle="tooltip" data-placement="top" title="Parking"><span>Parking</span></li>
		         						 		<input type="hidden" value="filter" id="parking" data-parking ="1" class="parking">
		         						<?php else:?>
		         								<input type="hidden" value="filter" id="parking" class="parking" data-parking="0">
				         				<?php endif;?>
				         				<?php if($s_result||$sw_result):?>
				         						 <li class="sf" data-toggle="tooltip" data-placement="top" title="Swimming pool"><span>Swimming pool</span></li>
				         						 <input type="hidden" value="filter" id="pool" class="pool" data-pool="1">
				         				<?php else:?>
				         					 <input type="hidden" value="filter" id="pool" class="pool" data-pool="0">
				         				<?php endif;?>			         			 
				         			<?php else:?>
				         				<input type="hidden" value="filter" id="wifi" class="wifi" data-wifi="0">
						         		<input type="hidden" value="filter" id="breakfast" class="breakfast" data-breakfast="0">
						         		<input type="hidden" value="filter" id="parking" class="parking" data-parking="0">
						         		<input type="hidden" value="filter" id="pool" class="pool" data-pool="0">
				         		<?php endif;?>
				         	<?php else:?>
				         		<input type="hidden" value="filter" id="wifi" class="wifi" data-wifi="0">
				         		<input type="hidden" value="filter" id="breakfast" class="breakfast" data-breakfast="0">
				         		<input type="hidden" value="filter" id="parking" class="parking" data-parking="0">
				         		<input type="hidden" value="filter" id="pool" class="pool" data-pool="0">
				         	<?php endif;?>
                           
                           </ul>
						</div>

						<?php if(isset($hd['trip_adv_url'])&&empty($hd['trip_adv_url'])==false):?>
						  <div class="tripad">
						    <a href="#"><img src="<?=$hd['trip_adv_url']?>"></a>
						    <span>Rating <?=$hd['trip_rating']?></span>
						  </div>
						<?php endif;?>
						 </div>
						</div>
					
					</div>
				</div>
		</div>
		<div class="col-sm-3 text-center ">
				<div class="maprew">
						  <div class="hoteloctnf">
						  <a href="<?php echo base_url().'index.php/hotel/map?lat='.$hd['Latitude'].'&lon='.$hd['Longitude'].'&hn='.urlencode($hd['HotelName']).'&sr='.intval($hd['StarRating']).'&c='.urlencode($hd['HotelLocation']).'&price='.$RoomPrice.'&img='.urlencode($hd['HotelPicture'])?>" class="location-map  fa fa-map-marker" target="map_box_frame" data-key="<?=$hd_key?>" data-hotel-code="<?=$hotel_code?>" data-star-rating="<?=$hd['StarRating']?>" data-hotel-name="<?=$hd['HotelName']?>" id="location_<?=$hotel_code?>_<?=$hd_key?>" data-toggle="tooltip" data-placement="top" data-original-title="View Map"></a>
						   </div>
						  
						</div>
			<div class="sidenamedesc">
				<div class="celhtl width70 hide">
					<div class="innd">
					   <div class="imptpldz">
						<div class="property-type" data-property-type="hotel"></div>
						<div class="shtlnamehotl">
							 <h3><?php echo $hd['HotelName']?></h3> 
						</div>
						
						<p data-toggle="tooltip" data-placement="top" data-original-title="<?php echo $hd['HotelAddress']?>"><?php echo $hd['HotelAddress']?>
						</p>
						<div class="starrtinghotl rating-no">
								<span class="h-sr hide"><?php echo $current_hotel_rate?></span>
								<?php echo print_star_rating($current_hotel_rate);?>
						</div>
						<br>
                        <hr class="mt-1 mb-1">
                        <!--<i class="fa fa-wifi fa-meal-icons" aria-hidden="true" title="Wi-Fi"></i>
                        <i class="fa fa-plane fa-meal-icons" aria-hidden="true" title="Flight"></i>
                        <i class="fa fa-cutlery fa-meal-icons" aria-hidden="true" title="Meal"></i>
                        <i class="fa fa-coffee fa-meal-icons" aria-hidden="true" title="Coffee Shop"></i>
                        <i class="fa fa-credit-card fa-meal-icons" aria-hidden="true" title="Credit Card Accepted"></i>
                        <i class="fa fa-taxi fa-meal-icons" aria-hidden="true" title="Cab Service"></i>-->



						<span class="result_token hide"><?=urlencode($hd['ResultToken'])?></span>
						<div class="preclsdv">
							<?php if(isset($hd['Free_cancel_date'])):?>
								<?php if($hd['Free_cancel_date']):?>
						 		 <span class="canplyto"><i class="fa fa-check" aria-hidden="true"></i> Free Cancellation till:<b><?=local_month_date($hd['Free_cancel_date']);?></b></span>
						 		 <input type="hidden" class="free_cancel" type="text" value="1" data-free-cancel="1">
						 		<?php else:?>
						 			<input type="hidden" class="free_cancel" type="text" value="0" data-free-cancel="0">
						 		<?php endif;?>
						 	<?php else:?>
						 		<input type="hidden" data-free-cancel="0" class="free_cancel" type="text" value="0">
							<?php endif;?>

						</div>

						<div class="bothicntri">
						<div class="mwifdiv">
                           <ul class="htl_spr">                         
				         	<?php if(isset($hd['HotelAmenities'])):?>
				         		<?php if($hd['HotelAmenities']):?>
				         			<?php
				         				//debug($hd['HotelAmenities']);
				         			   	$in_search_params = "".strtolower('wireless')."";
										$in_input = preg_quote(@$in_search_params, '~'); // don't forget to quote input string!
										$internet_result = preg_grep('~' . $in_input . '~', $hd['HotelAmenities']);
										$inn_search_params = "Wi-Fi";
										$inn_input = preg_quote(@$inn_search_params, '~'); 
										$innternet_result = preg_grep('~' . $inn_input . '~', $hd['HotelAmenities']);

										//checking free wifi
										
										$wf_search_params = "Wi";
										$wf_input = preg_quote(@$wf_search_params, '~'); 
										$wf_result = preg_grep('~' . $wf_input . '~', $hd['HotelAmenities']);

										$b_search_params = "".strtolower('breakfast')."";
										$b_input = preg_quote(@$b_search_params, '~'); 
										$b_result = preg_grep('~' . $b_input . '~', $hd['HotelAmenities']);
										//checking breakfast 
										$bf_search_params = "Breakfast";
										$bf_input = preg_quote(@$bf_search_params, '~'); 
										$bf_result = preg_grep('~' . $bf_input . '~', $hd['HotelAmenities']);

										$p_search_params = "".strtolower('parking')."";
										$p_input = preg_quote(@$p_search_params, '~'); 
										$p_result = preg_grep('~' . $p_input . '~', $hd['HotelAmenities']);
										//car parking
										$cp_search_params = "".strtolower('park')."";
										$cp_input = preg_quote(@$cp_search_params, '~'); 
										$cp_result = preg_grep('~' . $cp_input . '~', $hd['HotelAmenities']);

										$s_search_params = "pool";
										$s_input = preg_quote(@$s_search_params, '~'); 
										$s_result = preg_grep('~' . $s_input . '~', $hd['HotelAmenities']);
										$swim = "Swim";
								
										$sw_input = preg_quote(@$swim, '~'); 
										$sw_result = preg_grep('~' . $sw_input . '~', $hd['HotelAmenities']);
				         			?>
				         				<?php if($internet_result||$innternet_result|| $wf_result):?>
				         					<li class="wf" data-toggle="tooltip" data-placement="top" title="Wifi"><span>Wifi</span></li>
				         					<input type="hidden" value="filter" id="wifi" class="wifi" data-wifi="1">
				         				<?php else:?>
				         					<input type="hidden" value="filter" id="wifi" class="wifi" data-wifi="0">
				         				<?php endif;?>
				         				<?php if($b_result||$bf_result):?>
				         					<li class="bf" data-toggle="tooltip" data-placement="top" title="Breakfast"><span>Breakfast</span></li>
				         					<input type="hidden" value="filter" id="breakfast" class="breakfast" data-breakfast="1">
				         				<?php else:?>
				         					<input type="hidden" value="filter" id="breakfast" class="breakfast" data-breakfast="0">
				         				<?php endif;?>
				         				<?php if($p_result || $cp_result):?>
				         						 <li class="pr" data-toggle="tooltip" data-placement="top" title="Parking"><span>Parking</span></li>
		         						 		<input type="hidden" value="filter" id="parking" data-parking ="1" class="parking">
		         						<?php else:?>
		         								<input type="hidden" value="filter" id="parking" class="parking" data-parking="0">
				         				<?php endif;?>
				         				<?php if($s_result||$sw_result):?>
				         						 <li class="sf" data-toggle="tooltip" data-placement="top" title="Swimming pool"><span>Swimming pool</span></li>
				         						 <input type="hidden" value="filter" id="pool" class="pool" data-pool="1">
				         				<?php else:?>
				         					 <input type="hidden" value="filter" id="pool" class="pool" data-pool="0">
				         				<?php endif;?>			         			 
				         			<?php else:?>
				         				<input type="hidden" value="filter" id="wifi" class="wifi" data-wifi="0">
						         		<input type="hidden" value="filter" id="breakfast" class="breakfast" data-breakfast="0">
						         		<input type="hidden" value="filter" id="parking" class="parking" data-parking="0">
						         		<input type="hidden" value="filter" id="pool" class="pool" data-pool="0">
				         		<?php endif;?>
				         	<?php else:?>
				         		<input type="hidden" value="filter" id="wifi" class="wifi" data-wifi="0">
				         		<input type="hidden" value="filter" id="breakfast" class="breakfast" data-breakfast="0">
				         		<input type="hidden" value="filter" id="parking" class="parking" data-parking="0">
				         		<input type="hidden" value="filter" id="pool" class="pool" data-pool="0">
				         	<?php endif;?>
                           
                           </ul>
						</div>

						<?php if(isset($hd['trip_adv_url'])&&empty($hd['trip_adv_url'])==false):?>
						  <div class="tripad">
						    <a href="#"><img src="<?=$hd['trip_adv_url']?>"></a>
						    <span>Rating <?=$hd['trip_rating']?></span>
						  </div>
						<?php endif;?>
						 </div>
						</div>
						<div class="maprew">
						  <div class="hoteloctnf">
						  <a href="<?php echo base_url().'index.php/hotel/map?lat='.$hd['Latitude'].'&lon='.$hd['Longitude'].'&hn='.urlencode($hd['HotelName']).'&sr='.intval($hd['StarRating']).'&c='.urlencode($hd['HotelLocation']).'&price='.$RoomPrice.'&img='.urlencode($hd['HotelPicture'])?>" class="location-map  fa fa-map-marker" target="map_box_frame" data-key="<?=$hd_key?>" data-hotel-code="<?=$hotel_code?>" data-star-rating="<?=$hd['StarRating']?>" data-hotel-name="<?=$hd['HotelName']?>" id="location_<?=$hotel_code?>_<?=$hd_key?>" data-toggle="tooltip" data-placement="top" data-original-title="View Map"></a>
						   </div>
						  
						</div>
					</div>
				</div>
				
				<div>
					<div class="sidepricewrp">
				
						<div class="priceflights">
							<h3>
								<small>Starting Price</small>
								<br>
								<strong> <?php echo $currency_obj->get_currency_symbol($currency_obj->to_currency); ?> 
							<span class="h-p"><?php echo roundoff_number($RoomPrice); ?></span></strong>
							</h3>
							<button class="btn btn-danger view_details_btn" type="button" data-toggle="collapse" data-target="#select-btn_<?=$hd['HotelCode']?>" aria-expanded="false" aria-controls="hotelbtn">Select Rooms</button>
							
							<!--<div class="prcstrtingt">Avg / Night</div>-->
						</div>
						<form action="<?php echo base_url().'index.php/hotel/hotel_details/'.($search_id)?>">
						
							<input type="hidden" id="mangrid_id_<?=$hd_key?>_<?=$hotel_code?>" value="<?=urlencode($hd['ResultToken'])?>" name="ResultIndex"  data-key="<?=$hd_key?>" data-hotel-code="<?=$hotel_code?>" class="result-index ResultIndex">


							<input type="hidden" id="booking_source_<?=$hd_key?>_<?=$hotel_code?>" value="<?=urlencode($booking_source)?>" name="booking_source"  data-key="<?=$hd_key?>" data-hotel-code="<?=$hotel_code?>" class="booking_source">
							
							<input type="hidden" value="get_details" name="op" class="operation op">

							<input type="hidden" name="hotel_code" value="<?=$hotel_code?>" class="operation hotel_code">
							<input type="hidden" name="result_index" value="<?=$hd_key?>" class="result_index">
							<input type="hidden" name="search_id" value="<?=$search_id?>" class="search_id">
							<!--<button class="confirmBTN b-btn bookallbtn splhotltoy" type="submit">Book</button>-->
						</form>
						<div class="viewhotlrmtgle hide">
							<button class="vwrums room-btn" type="button">View Rooms</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
		<!-- <form class="room-form hide">
			<input type="hidden" value="<?=urlencode($hd['ResultToken'])?>" name="ResultIndex" class="result-index">
			<input type="hidden" value="<?=urlencode($booking_source)?>" name="booking_source" class="booking_source">
			<input type="hidden" name="op" value="get_room_details">
			<input type="hidden" name="search_id" value="<?=$search_id?>">
		</form> -->
		
		<div class="clearfix"></div>
		<div class="room-list" style="display:none">
			<div class="room-summ romlistnh">
				<?=$mini_loading_image?>
			</div>
		</div>
		<?php
			//echo $hd['HotelPromotion'];
			if (isset($hd['HotelPromotion']) == true and empty($hd['HotelPromotion']) == false) {?>	
				<div class="gift-tag">
		          <span class="offdiv deal-status" data-deal="<?php echo ACTIVE?>"><?=$hd['HotelPromotion']?>% Off</span>
		        </div>
			<?php } else {?>
				<span class="deal-status hide" data-deal="<?php echo INACTIVE?>"></span>
				<?php
		}?>
	</div>

<!-- room list -->
 <div class="row mt-2 id-hotel-content-row1 collapse"   id="select-btn_<?=$hd['HotelCode']?>">
                    <div class="container-fluid id-content">
                        <div class="col-sm-3 padfive">
                            <div class="row">
                                <small>Filter by Room Type</small>
                                <!--<input type="text" name="" placeholder="Search Rooms" class="form-control">-->
								<select class="form-control selectpicker flt_by_room_type flt_by_rm_<?=$hd['HotelCode']?>" data-hotel_id="<?=$hd['HotelCode']?>" name="flt_by_room_type" data-live-search="true" id="flt_by_rm_<?=$hd_key?>">
									<?php foreach($hd['RoomDetails']['Type'] as $room_key => $room_val){ ?>
										<option value="<?=$room_val?>"><?=$room_val?></option>';
									<?php } ?>
                                    
                                </select>
                            </div>
                            <div class="row mt-1">
                                <small>Filter by Board Basis</small>
                                <select class="form-control id-select-content selectpicker" data-hotel_id="<?=$hd['HotelCode']?>" multiple data-live-search="true">
                                    <option>Room Only</option>
                                    <option>Breakfast</option>
                                    <option>Half Board</option>
                                    <option>Full Board</option>
                                    <option>All Inclusive</option>
                                </select>
                            </div>
                            <div class="row mt-1">
                                <div class="squaredThree3">
                                    
									<input type="checkbox" class="freecancellation" id="freecancellation<?=$hd['HotelCode']?>" data-hotel_id="<?=$hd['HotelCode']?>" name="freecancellation<?=$hd['HotelCode']?>" value="freecancel">
                                    <label for="freecancellation<?=$hd['HotelCode']?>">
                                        <span class="lbllbl-2">Free Cancellation</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-9 pr-0" id="hotelbtn_<?=$hd_key?>">
                            <?=$loading_image?>
                        </div>
                    </div>
                </div>

</div>
 <div class="modal fade myGallary" id="myGallary_<?=$hd['HotelCode']?>" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <!-- <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Modal Header</h4>
        </div> -->
        <div class="modal-body">
          <div id="owl-gallary_<?=$hd['HotelCode']?>" class="owl-carousel owl-theme img_gal">
              <div class="item"><img src="<?=$hd['HotelPicture']?>" alt="The Last of us">

              </div>
              <!--<div class="item"><img src="https://static.toiimg.com/thumb/msid-22509870,width-1070,height-580,resizemode-75,imgsize-22509870,pt-32,y_pad-40/22509870.jpg" alt="GTA V"></div>
              <div class="item"><img src="https://cdn.britannica.com/15/189715-050-4310222B/Dubai-United-Arab-Emirates-Burj-Khalifa-top.jpg" alt="Mirror Edge"></div>-->
          </div>
        </div>
        <!-- <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div> -->
      </div>
      
    </div>
  </div>
<?php

}
?>



  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();  
        
});
</script>
<script type="text/javascript">
    $(document).ready(function(){
    	  $('.selectpicker').selectpicker({
    style: 'btn-default'
  });
        // $('#select-btn1').click(function(){
        //      $(this).text( $(this).text() == 'Select Rooms' ? "Close Rooms" : "Select Rooms");
        //     $('.id-hotel-content-row1').slideToggle()(3000);
        // });
        // $('#select-btn').click(function(){
        //     alert('sasa');
        //     if (this.value=="Select Rooms") this.value = "Select Rooms";
        //     else this.value = "Close Rooms";
        // });

        $('.p-price-range').on('click', function(e){
            $(this).attr('div-status',1);
            $('.id-price-range-div').slideToggle()(3000);    
        });

        $(document).on('click', function (e) {
            if( $(e.target).closest(".p-price-range").length > 0 ) {
                return false;
            }else{
                let divDisplayAttr = $('#price-refine').css('display');
                let divStatus =  $('.p-price-range').attr('div-status');
                if(divStatus == 1){                              
                    if(divDisplayAttr = "block"){
                        $('.id-price-range-div').hide();    
                    }   
                }            
            }           
        });

        $(".img_gal").owlCarousel({
 
              navigation : true, // Show next and prev buttons
              slideSpeed : 300,
              paginationSpeed : 400,
              singleItem:true
         
              // "singleItem:true" is a shortcut for:
              // items : 1, 
              // itemsDesktop : false,
              // itemsDesktopSmall : false,
              // itemsTablet: false,
              // itemsMobile : false
          });
        $('.tog1').click(function(){
            $('.fa-fil-tog1').toggleClass('fa-caret-up','fa-caret-down');
        });
        $('.tog2').click(function(){
            $('.fa-fil-tog2').toggleClass('fa-caret-up','fa-caret-down');
        });
        $('.tog3').click(function(){
            $('.fa-fil-tog3').toggleClass('fa-caret-up','fa-caret-down');
        });
		$(document).on('click','.view_details_btn',function(e){
			var ResultIndex=$(this).parents('.sidepricewrp').find('.ResultIndex').val();
			var booking_source=$(this).parents('.sidepricewrp').find('.booking_source').val();
			var hotel_code=$(this).parents('.sidepricewrp').find('.hotel_code').val();
			var search_id=$(this).parents('.sidepricewrp').find('.search_id').val();
			var result_index=$(this).parents('.sidepricewrp').find('.result_index').val();
			var op=$(this).parents('.sidepricewrp').find('.op').val();
			
			
			$.post('<?=base_url();?>index.php/hotel/get_hotel_data',{'ResultIndex':ResultIndex,'booking_source':booking_source,'hotel_code':hotel_code,'search_id':search_id,'result_index':result_index,'op':op},function(data)
			{			        		
				$('#hotelbtn_'+result_index).html(data);
			});
		});
    });
</script>
<strong class="currency_symbol hide" > <?php echo $currency_obj->get_currency_symbol($currency_obj->to_currency); ?> </strong>
<script type="text/javascript">
	$(document).ready(function(){

		var default_loader = "<?=$GLOBALS['CI']->template->template_images('image_loader.gif')?>";
		//console.log("default_loader"+default_loader);
		//$(".load-image").attr('src',default_loader);

		setTimeout(function(){
			$(".load-image").removeClass('hide');
			$(".loader-image").addClass('hide');
			//loader-image
		},3000);
		$(document).on('click','.freecancellation',function(e){
			var sel_htl=$(this).data('hotel_id');
			var $this = $(this);
			filter_room($this,);
			
			
			
			
			
			$('.room-list_'+sel_htl).find('.romconoutdv').each(function() {
				var can_val=$(this).find('.freecanctext').val();
				if ($this.prop("checked")) {
					if(can_val==0){
						$(this).addClass('hide');
					}else{
						$(this).removeClass('hide');
					}
				}else{
					$(this).removeClass('hide');
				}
			});
		});
		
		$(document).on('change','.flt_by_room_type',function(e){
			var sel_htl=$(this).data('hotel_id');
			var sel_room_id=$(this).attr('id');
			var sel_room = $(this).val();
		    sel_room = sel_room.toLowerCase();
			console.log(sel_room);
			
			
		});
		function filter_room(var canc,var room_type,var board type, var hotel_code){
			
		}
	});
</script>

<style type="text/css">
	.shtlnamehotl {
    color: #222;
    display: table;
    font-size: 16px;
    overflow: hidden;
    position: relative;
    text-overflow: ellipsis;
    white-space: normal;
    padding: 0;
    float: left;
    width: 100%;
}
</style>
<style type="text/css">
    .mb-3{
        margin-bottom: 30px;
    }
    .id-hotel-main-row{
        border: 1px solid #ccc;
        background-color: #fff;
        margin-bottom: 15px;
        box-shadow: 0 2px 5px 0 rgba(0,0,0,0.0),0 2px 10px 0 rgba(0,0,0,0.1) !important;
    }
    .id-hotel-main-row:hover{
       border: 1px solid #0095ff;
       /*background-color: #ebf4ff; */
       background-color: #f5faff; 
    }
    p {
       margin: 0 0 5px!important;
   }
   .id-hotel-main-row:focus {     
        background-color:yellow;    
    }
    .id-hotel-content-row{
        padding: 10px;
    }
    .mb-1{
        margin-bottom: 10px;
    }
    .fa-meal-icons{
        font-size: 20px;
        color: #3a99d2;
        margin-right: 15px;
    }
    .id-filter-price{
        padding: 5px 20px;
        background-color: #bedeff;
        cursor: pointer;
    }
    .mt-3{
        margin-top: 30px;
    }
    .padfive {
         padding: 0px 2px !important; 
    }
    .id-filter-div{
        padding: 10px;
        background-color: #fff;
    }
    .id-filter-div .form-control {
        border: 1px solid #ddd;
        border-radius: 0;
    }
    .bg-white{
        background-color: #fff;
    }
    .shadow-1{
        box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12) !important;
    }
    .id-fa-search{
        position: absolute;
        top: 28px;
        left: 13px;
        color: #aaa;
    }
    .id-search-input{
        padding-left: 35px;
    }
    .id-hotels-found-p{
        font-size: 15px;
        color: #888;
    }
    .id-hotels-found-p strong{
        color: #4499f1;
    }
    .id-hotel-content-row h4{
        margin: 0;
    }
    .id-content{
        border: 1px solid #ccc;
        padding: 20px;
        border-radius: 8px;
    }
    .id-room-book{
        font-size: 12px;
        width: 110px;
    }
    .pr-0{
        padding-right: 0!important;
    }
    .id-content-row{
        padding: 6px 0 0;
        background: #fff;
    }
    .id-select-content{
        background: transparent!important;
        cursor: pointer;
    }
    .id-content-row .id-price-text{
        color: #e20000;
    }
    .bootstrap-select > .dropdown-toggle.bs-placeholder, .bootstrap-select > .dropdown-toggle.bs-placeholder:hover, .bootstrap-select > .dropdown-toggle.bs-placeholder:focus, .bootstrap-select > .dropdown-toggle.bs-placeholder:active {
        color: #999;
        background: transparent;
        border: 1px solid #ccc;
    }
    .id-content .form-control {
        background: transparent;
    }
    .bootstrap-select > .dropdown-toggle {
        background: transparent;
        border: 1px solid #ccc;
    }
    .p-cancellation {
        /*background-color: #e7f2ff;*/
        background-color: #f3f3f3;
        padding: 2px 6px;
        color: #666;
        font-size: 12px;
    }
    .datein {
        font-size: 13px;
    }
    .bootstrap-select > .dropdown-toggle.bs-placeholder, .bootstrap-select > .dropdown-toggle.bs-placeholder:hover, .bootstrap-select > .dropdown-toggle.bs-placeholder:focus, .bootstrap-select > .dropdown-toggle.bs-placeholder:active {
        border: none!important;
    }
    .id-filter-div .dropdown-menu{
        z-index: 111111;
    }
    .id-filter-div small{
        color: #a7a7a7;
    }
    .bootstrap-select > .dropdown-toggle {
        border: none;
    }
    .id-filter-div .id-price-range-div{
        position: absolute;
        background: #fff;
        padding: 10px;
        width: 265px;
        box-shadow: 0 6px 12px rgba(0,0,0,.175);
        z-index: 111111;
        border-radius: 4px;
        display: none;
    }
    .id-filter-div .p-price-range{
        padding: 7px;
        margin: 0!important;
        border: 1px solid #ccc;
        cursor: pointer;
    }
    .id-content .filter-option{
        border: 1px solid #ccc;
    }
    .id-map-style{
        padding: 10px;
        font-size: 18px;
        cursor: pointer;
        position: absolute;
        right: 0;
        color: #3a99d2;
        z-index: 1;
    }
    .id-img-div2 img{
        cursor: zoom-in;
        width: 100%;
        height: 150px;
    }
    .img_gal .item img{
        display: block;
        width: 100%;
        height: auto;
    }
    .img_gal .owl-buttons{
        display: none;
    }
    .myGallary .modal-dialog{
        margin-top: 100px;
    }
    hr.mt-1.mb-1 {
    margin-top: 10px;
}
    
</style>