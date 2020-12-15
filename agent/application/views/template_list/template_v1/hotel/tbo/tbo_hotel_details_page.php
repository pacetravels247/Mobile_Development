<?php
$mini_loading_image	 = '<div class="text-center loader-image"><img src="'.$GLOBALS['CI']->template->template_images('loader_v3.gif').'" alt="Loading........"/></div>';
$loading_image		 = '<div class="text-center loader-image"><img src="'.$GLOBALS['CI']->template->template_images('loader_v1.gif').'" alt="Loading........"/></div>';
$TraceId			 = $hotel_details['HotelInfoResult']['TraceId'];
$_HotelDetails		 = $hotel_details['HotelInfoResult']['HotelDetails'];
$sanitized_data['HotelCode']			= $_HotelDetails['HotelCode'];
$sanitized_data['HotelName']			= $_HotelDetails['HotelName'];
$sanitized_data['StarRating']			= $_HotelDetails['StarRating'];
$sanitized_data['Description']			= $_HotelDetails['Description'];
$sanitized_data['Attractions']			= (isset($_HotelDetails['Attractions']) ? $_HotelDetails['Attractions'] : false);
$sanitized_data['HotelFacilities']		= (isset($_HotelDetails['HotelFacilities']) ? $_HotelDetails['HotelFacilities'] : false);
$sanitized_data['HotelPolicy']			= (isset($_HotelDetails['HotelPolicy']) ? $_HotelDetails['HotelPolicy'] : false);
$sanitized_data['SpecialInstructions']	= (isset($_HotelDetails['SpecialInstructions']) ? $_HotelDetails['SpecialInstructions'] : false);
$sanitized_data['Address']				= (isset($_HotelDetails['Address']) ? $_HotelDetails['Address'] : false);
$sanitized_data['PinCode']				= (isset($_HotelDetails['PinCode']) ? $_HotelDetails['PinCode'] : false);
$sanitized_data['HotelContactNo']		= (isset($_HotelDetails['HotelContactNo']) ? $_HotelDetails['HotelContactNo'] : false);
$sanitized_data['Latitude']				= (isset($_HotelDetails['Latitude']) ? $_HotelDetails['Latitude'] : 0);
$sanitized_data['Longitude']			= (isset($_HotelDetails['Longitude']) ? $_HotelDetails['Longitude'] : 0);
$sanitized_data['RoomFacilities']		= (isset($_HotelDetails['RoomFacilities']) ? $_HotelDetails['RoomFacilities'] : false);
$sanitized_data['Images']				= $_HotelDetails['Images'];
Js_Loader::$css[] = array('href' => $GLOBALS['CI']->template->template_css_dir('owl.carousel.min.css'), 'media' => 'screen');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('owl.carousel.min.js'), 'defer' => 'defer');
?>

<?php
/**
 * Application VIEW
 */
echo $GLOBALS['CI']->template->isolated_view('hotel/search_panel_summary');
?>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCJfvWH36KY3rrRfopWstNfduF5-OzoywY&sensor=false"></script>
<script type="text/javascript">
	/** Google Maps **/
	var myCenter=new google.maps.LatLng(<?=floatval($sanitized_data['Latitude'])?>,<?=floatval($sanitized_data['Longitude']); ?>);
	function initialize()
	{
		var mapProp = {
			center:myCenter,
			zoom:12,
			mapTypeId:google.maps.MapTypeId.ROADMAP
		};

		var map = new google.maps.Map(document.getElementById("Map"), mapProp);
	
		var marker = new google.maps.Marker({
			position:myCenter,
		});
	
		marker.setMap(map);
	
		var infowindow = new google.maps.InfoWindow({
			content:"Hotel Location"
		});
	
		google.maps.event.addListener(marker, "click", function() {
			infowindow.open(map, marker);
		});
	}
	google.maps.event.addDomListener(window, "load", initialize);
</script>
<div class="container">
<div class="panel panel-default htl_dtls_cont clearfix">
	<div class="panel-body">
		<p class="text-primary"><?php echo strtoupper($sanitized_data['HotelName']);?> <span class="rating-no"><span class="hide" id="h-sr"><?=$sanitized_data['StarRating']?></span><?php echo print_star_rating($sanitized_data['StarRating']);?></span></p> <?php echo '<span>-'.$sanitized_data['Address']?></span>
		<!-- Hotel Basic Info -->
	</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-md-4">
				<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
					<div class="carousel-inner" role="listbox">
					<?php
					//loop images
					if (valid_array($sanitized_data['Images']) == true) {
						$visible = 'active';
						foreach ($sanitized_data['Images'] as $i_k => $i_v) {?>
							<div class="item <?php echo $visible; $visible='';?> ">
								<img src=<?php echo $i_v?> alt="<?php echo $i_k?>" class="img-responsive" style="width:100%; height:200px">
								<div class="carousel-caption">
									<p><?php echo $sanitized_data['HotelName']?></p>
								</div>
							</div>
					<?php }
					}
					?>
					</div>
					<!-- Controls -->
					<a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
						<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
						<span class="sr-only">Previous</span>
					</a>
					<a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
						<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
						<span class="sr-only">Next</span>
					</a>
				</div>
			</div><!-- Images -->
			<div class="col-md-8">
				<div id="Map" class="col-md-12" style="height:200px; width:100%">Map</div>
			</div><!-- MAP -->
		</div>

		<!-- ROOM COMBINATION START -->
		<div id="room-list" class="room-list romlistnh">
			<?php echo $loading_image;?>
		</div>
		<!-- ROOM COMBINATION END -->

		<div class="panel panel-default clearfix">
		  <!-- Default panel contents -->
		  <div class="panel-heading"><?php echo $sanitized_data['HotelName'];?></div>
		  <div class="panel-body">
			  <div class="col-md-12">Hotel Information</div>
		  		<div id="hotel-additional-info" class="col-md-12"><?php echo $sanitized_data['Description']?></div>
			  </div>
		  
		  <?php
		    if (valid_array($sanitized_data['HotelFacilities']) == true) {
		    	//:p Did this for random color generation
		    	//$color_code = string_color_code('Balu A');
		    	$color_code = '#00a0e0';
		    	?>
		    	<div class="panel-body">
		    	<p class="list-group-item col-md-12" style="color:<?php echo $color_code?>">Hotel Facilities</p>
		    	<?php
		    	//-- List group -->
		    	foreach ($sanitized_data['HotelFacilities'] as $ak => $av) {?>
		    		<p class="list-group-item col-md-4"><span class="glyphicon glyphicon-check" style="color:<?php echo $color_code?>"></span> <?php echo $av; ?></p>
		    	<?php
		    	}?>
		    	</div>
				
		    <?php
		    }
		    ?>
		    
		    <?php
		    if (valid_array($sanitized_data['Attractions']) == true) {
		    	//:p Did this for random color generation
		    	//$color_code = string_color_code('Balu A');
		    	$color_code = '#00a0e0';
		    	?>
		    	<div class="panel-body">
		    	<p class="list-group-item col-md-12"><span class="fa fa-binoculars"  style="color:<?php echo $color_code?>"></span> Attractions</p>
		    	<?php
		    	//-- List group -->
		    	foreach ($sanitized_data['Attractions'] as $ak => $av) {?>
		    		<p class="list-group-item col-md-4"><span class="glyphicon glyphicon-check" style="color:<?php echo $color_code?>"></span> <?php echo $av['Value']; ?></p>
		    	<?php
		    	}?>
		    	</div>
				
		    <?php
		    }
		    ?>
		</div>
	</div>
	<div class="alert">
		<h4>Hotel Policy</h4>
		<p><?php echo (empty($sanitized_data['HotelPolicy']) == false ? $sanitized_data['HotelPolicy'] : '---');?></p>
	</div>
</div>
</div>
<?php
/**
 * This is used only for sending hotel room request - AJAX
 */
$hotel_room_params['HotelCode']		= $params['HotelCode'];
$hotel_room_params['ResultIndex']	= $params['ResultIndex'];
$hotel_room_params['booking_source']		= $params['booking_source'];
$hotel_room_params['TraceId']		= $params['TraceId'];
$hotel_room_params['search_id']		= $hotel_search_params['search_id'];
$hotel_room_params['op']			= 'get_room_details';
?>
<script>
$(document).ready(function() {
	//Load hotel Room Details
	var ResultIndex = '';
	var HotelCode = '';
	var TraceId = '';
	var booking_source = '';
	var op = 'get_room_details';
	function load_hotel_room_details()
	{
		var _q_params = <?php echo json_encode($hotel_room_params)?>;
		if (booking_source) { _q_params.booking_source = booking_source; }
		if (ResultIndex) { _q_params.ResultIndex = ResultIndex; }
		if (HotelCode) { _q_params.HotelCode = HotelCode; }
		if (TraceId) { _q_params.TraceId = TraceId; }
		$.post(app_base_url+"index.php/ajax/get_room_details", _q_params, function(response) {
			if (response.hasOwnProperty('status') == true && response.status == true) {
				$('#room-list').html(response.data);
				var _hotel_name = "<?php echo $sanitized_data['HotelName'];//Hotel Name comes from hotel info response ?>";
				var _hotel_star_rating = <?php echo abs($sanitized_data['StarRating'])?>;
				$('[name="HotelName"]').val(_hotel_name);
				$('[name="StarRating"]').val(_hotel_star_rating);
			}
		});
	}
	load_hotel_room_details();
	$('.hotel_search_form').on('click', function(e) {
		e.preventDefault();
		$('#hotel_search_form').slideToggle(500);
	});
});
</script>