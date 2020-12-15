<?php
//TraceId
//ResultIndex
//HotelCode
$template_images = $GLOBALS['CI']->template->template_images();
$mini_loading_image = '<div class="text-center loader-image">Please Wait <img src="'.$template_images.'loader_v3.gif" alt="Loading........"/></div>';
$trace_id = $raw_hotel_list['HotelSearchResult']['TraceId'];
foreach ($raw_hotel_list['HotelSearchResult']['HotelResults'] as $hd) {
	$current_hotel_rate = ($hd['StarRating']);
?>
	<div class="panel-body p-0 r-r-i">
		<div class="row lg-flex">
			<div class="col-md-2 col-sm-6 p-tb-10">
				<figure>
					<img class="img-responsive center-block" alt="Hotel Image" src="<?php echo $hd['HotelPicture']?>">
				</figure>
			</div>
			<div class="col-md-5 col-sm-6 p-tb-10">
				<div class="property-type" data-property-type="hotel"></div>
				<h4><span class="h-name"><?php echo $hd['HotelName']?></span></h4>
				<h5><img alt="Hotel Location Icon" src="<?php echo $template_images?>icons/hotel-location-icon.png">
				 <span class="hotel-location"><?=$hd['HotelLocation']?></span></h5>				
				<h6>
					<span class="rating-no">
						<span class="h-sr hide"><?php echo $current_hotel_rate?></span>
						<?php echo print_star_rating($current_hotel_rate);?>
					</span>
				</h6>
				<p class="m-0 text-ellipsis"><?php echo $hd['HotelAddress']?></p>
			</div>
			<div class="col-md-2 col-sm-6 p-tb-10 text-center b-r">
				<?php
				if (isset($hd['HotelPromotion']) == true and empty($hd['HotelPromotion']) == false) {?>
					<h6 class="alert alert-success"><span class="deal-status fa fa-star" data-deal="<?php echo ACTIVE?>"></span> <span href="#"><?=$hd['HotelPromotion']?></span></h6>
				<?php
				} else {?>
					<span class="deal-status hide" data-deal="<?php echo INACTIVE?>"></span>
				<?php
				}?>
				<h6><a href="<?php echo base_url().'index.php/hotel/map?lat='.$hd['Latitude'].'&lon='.$hd['Longitude'].'&hn='.urlencode($hd['HotelName']).'&sr='.intval($hd['StarRating']).'&c='.urlencode($hd['HotelLocation']).'&img='.urlencode($hd['HotelPicture'])?>" class="location-map" target="map_box_frame"><img alt="Hotel Map Location Icon" src="<?php echo $template_images.'icons/hotel-map-location-icon.png'?>"> Show on Map</a></h6>
			</div>
			<?php
			/**
			 * HOTEL PRICE SECTION With Markup price will be returned
			 * 
			 */
			$search_id = intval($attr['search_id']);
			//$PublishedPrice				= $hd['Price']['PublishedPrice'];
			//$PublishedPriceRoundedOff	= $hd['Price']['PublishedPriceRoundedOff'];
			//$OfferedPrice				= $hd['Price']['OfferedPrice'];
			//$OfferedPriceRoundedOff		= $hd['Price']['OfferedPriceRoundedOff'];
			$RoomPrice					= $hd['Price']['RoomPrice'];
			?>
			<div class="col-md-3 col-sm-6">
				<div class="row row-no-gutter text-center">
					<div class="col-md-9 p-tb-10">
						<h6 class="text-uppercase">starting @ </h6>
						<h4><?php echo $currency_obj->get_currency_symbol($currency_obj->to_currency); ?> <span class="h3 text-i h-p"><?php echo roundoff_number($RoomPrice); ?></span></h4>
						<h5 class="h6"><del><?php echo $currency_obj->get_currency_symbol($currency_obj->to_currency); ?> <?php echo $RoomPrice;?></del></h5>
						<form method="GET" action="<?php echo base_url().'index.php/hotel/hotel_details/'.($search_id)?>" target="_blank">
							<div class="hide">
								<input type="hidden" value="<?=urlencode($hd['ResultIndex'])?>"	name="ResultIndex" class="result-index">
								<input type="hidden" value="<?=urlencode($hd['HotelCode'])?>"	name="HotelCode" class="hotel-code">
								<input type="hidden" value="<?=urlencode($trace_id)?>"						name="TraceId" class="trace-id">
								<input type="hidden" value="<?=urlencode($booking_source)?>"				name="booking_source" class="booking_source">
								<input type="hidden" value="get_details"									name="op" class="operation">
							</div>
							<button class="btn btn-p b-r-0 confirmBTN" type="submit">Book</button>
						</form>
					</div>
					<div class="col-md-3">
						<div role="group" class="btn-group btn-group-vertical">
							<button class="btn btn-default b-r-0" type="button">SMS</button>
							<button class="btn btn-default b-r-0" type="button"><i class="fa fa-usd"></i></button>
							<form class="room-form hide">
							<input type="hidden" value="<?=urlencode($hd['ResultIndex'])?>"	name="ResultIndex" class="result-index">
							<input type="hidden" value="<?=urlencode($hd['HotelCode'])?>"	name="HotelCode" class="hotel-code">
							<input type="hidden" value="<?=urlencode($trace_id)?>"						name="TraceId" class="trace-id">
							<input type="hidden" value="<?=urlencode($booking_source)?>"				name="booking_source" class="booking_source">
							<input type="hidden" name="op" value="get_room_details">
							<input type="hidden" name="search_id" value="<?=$search_id?>">
							</form>
							<button class="btn btn-default b-r-0 room-btn" type="button"><img alt="Double Arrow Down Icon" src="<?=$template_images?>icons/double-arrow-down-icon.png"></button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="clearfix room-list" style="display:none">
			<div class="row">
				<div class="col-md-12">
					<h5 class="text-info text-center">Room Details <i class="fa fa-bed"></i><i class="fa fa-arrow-circle-down pull-right"></i></h5>
				</div>
			</div>
			<?=$mini_loading_image?>
			<div class="room-summ romlistnh">
			</div>
		</div>
	</div>
<?php
}