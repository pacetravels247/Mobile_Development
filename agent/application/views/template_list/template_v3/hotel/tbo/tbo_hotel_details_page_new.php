
<?php
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/hotel_details_slider.js'), 'defer' => 'defer');
$booking_url = $GLOBALS['CI']->hotel_lib->booking_url($hotel_search_params['search_id']);
//debug($params);
$mini_loading_image	 = '<div class="text-center loader-image"><img src="'.$GLOBALS['CI']->template->template_images('loader_v3.gif').'" alt="Loading........"/></div>';
$loading_image		 = '<div class="spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div><div class="bounce4"></div></div>';
$_HotelDetails		 = $hotel_details['HotelInfoResult']['HotelDetails'];
//debug($_HotelDetails);exit;
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
if($sanitized_data['Images']){
	$sanitized_data['Images'] = $sanitized_data['Images'];
}else{
	$sanitized_data['Images'] = $GLOBALS['CI']->template->template_images('default_hotel_img.jpg');
}
Js_Loader::$css[] = array('href' => $GLOBALS['CI']->template->template_css_dir('owl.carousel.min.css'), 'media' => 'screen');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('owl.carousel.min.js'), 'defer' => 'defer');
$base_url_image=base_url().'index.php/hotel/image_details_cdn';
?>

<?php
/**
 * Application VIEW
 */
// echo $GLOBALS['CI']->template->isolated_view('hotel/search_panel_summary');
?>


<div class="clearfix"></div>
<input type="hidden" id="latitude" value="<?=$sanitized_data['Latitude']?>">
<input type="hidden" id="longitude" value="<?=$sanitized_data['Longitude']?>">
<input type="hidden" id="api_base_url" value="<?=$GLOBALS['CI']->template->template_images('marker/green_hotel_map_marker.png')?>">
<input type="hidden" id="hotel_name" value="<?php echo $sanitized_data['HotelName']?>">







<div class="innertabs">
	<h3 class="mobile_view_header">Description</h3>    <!-- <div class="htldesdv">Hotel Description</div> -->
	<div id="hotel-additional-info" class="padinnerntb"><div class="lettrfty short-text"><?php echo $sanitized_data['Description']?></div>
		<div class="show-more">
			
			<a href="#" class="show_more" data-text="show_more">Show More +</a>
            <a href="#" class="show_less hide" data-text="show_less">Show Less -</a>
		</div>
	</div>
</div>
<div class="innertabs">
	<h3 class="mobile_view_header">Rooms</h3>
	<div class="">
		<div id="room-list_<?=$params['result_index']?>" class="room-list romlistnh short-text1">
			<?php echo $loading_image;?>
		</div>
	<!-- <div class="show-rooms">
		<a href="#" id="show-more-link" class="">Show More Rooms +</a>
	</div> -->
	</div>
</div>
<div class="tab-pane" id="facility">
	<div class="innertabs">
		<?php  if (valid_array($sanitized_data['HotelFacilities']) == true) {?>
			<h3 class="mobile_view_header">Facilities</h3>
		<?php }?>
		<div class="padinnerntb htlfac_lity lettrfty short-text2"><!-- <div class="hedinerflty">Hotel Facilities</div> -->
			<div class="show-more-fac">
			
				<a href="#" class="show_more_fac" data-text="show_more">Show More +</a>
				<a href="#" class="show_less_fac hide" data-text="show_less">Show Less -</a>
			</div>
			
			<?php
			if (valid_array($sanitized_data['HotelFacilities']) == true) {
				//:p Did this for random color generation
				//$color_code = string_color_code('Balu A');
				$color_code = '#00a0e0';
				?>
				
				<?php
				//-- List group -->
				foreach ($sanitized_data['HotelFacilities'] as $ak => $av) {?>
					<div class="col-xs-4 nopad">
					<div class="facltyid">
					<span class="glyphicon glyphicon-check" style="color:<?php echo $color_code?>"></span> <?php echo $av; ?></div></div>
				<?php
				}?>

				
			<?php
			}
			?>
			<?php
			if (valid_array($sanitized_data['Attractions']) == true) {
				//:p Did this for random color generation
				//$color_code = string_color_code('Balu A');
				$color_code = '#00a0e0';
				?>
				<div class="subfty">
				
				<?php
				//-- List group -->
				foreach ($sanitized_data['Attractions'] as $ak => $av) {?>
					<div class="col-xs-4 nopad"><div class="facltyid"><span class="glyphicon glyphicon-check" style="color:<?php echo $color_code?>"></span> <?php echo $av['Value']; ?></div></div>
				<?php
				}?>
				</div>
			<?php
			}
			?>
		
			
		</div>
	</div>
</div>
<?php
/**
 * This is used only for sending hotel room request - AJAX
 */
$hotel_room_params['ResultIndex']	= $params['ResultIndex'];
$hotel_room_params['booking_source']		= $params['booking_source'];
$hotel_room_params['search_id']		= $hotel_search_params['search_id'];
$hotel_room_params['op']			= 'get_room_details';
$hotel_room_params['result_index']			= $params['result_index'];

if($params['booking_source'] == REZLIVE_HOTEL){
	$hotel_room_params['room_details'] = json_encode($_HotelDetails['raw_room_data']);
	$hotel_room_params['hotel_code'] = $_HotelDetails['HotelCode'];
}
//debug($hotel_room_params);die('888888');
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
		var result_index = <?php echo $params['result_index']?>;
		if (booking_source) { _q_params.booking_source = booking_source; }
		if (ResultIndex) { _q_params.ResultIndex = ResultIndex; }
		$.post(app_base_url+"index.php/ajax/get_room_details", _q_params, function(response) {
			if (response.hasOwnProperty('status') == true && response.status == true) {
				$('#room-list_'+result_index).html(response.data);
				//alert(response.data);
				var _hotel_name = "<?php echo preg_replace('/^\s+|\n|\r|\s+$/m', '', $sanitized_data['HotelName']); ?>";
				var _hotel_star_rating = "<?php echo abs($sanitized_data['StarRating']);?>";
				var _hotel_image = "<?php echo $sanitized_data['Images'][0];?>";
				var _hotel_address = "<?php echo preg_replace('/^\s+|\n|\r|\s+$/m', '', $sanitized_data['Address']);?>";
				$('[name="HotelName"]').val(_hotel_name);
				$('[name="StarRating"]').val(_hotel_star_rating);
				$('[name="HotelImage"]').val(_hotel_image);//Balu A
				$('[name="HotelAddress"]').val(_hotel_address);//Balu A
			}
		});
	}
	load_hotel_room_details();
	$('.hotel_search_form').on('click', function(e) {
		e.preventDefault();
		$('#hotel_search_form').slideToggle(500);
	});
	
	
	$('.movetop').click(function(){
		$('html, body').animate({scrollTop: $('.fulldowny').offset().top - 60 }, 'slow');
	});
	 $(".show-more a").on("click", function() {
		var $link = $(this);
		var $links = $(this).attr('data-text');
		var $content = $link.parent().prev("div.lettrfty");
		var linkText = $links;
		$content.toggleClass("short-text, full-text");
		getShowLinkText(linkText);
		return false;
	  });
	  $(".show-more-fac a").on("click", function() {
		var $link = $(this);
		var $links = $(this).attr('data-text');
		var $content = $link.parent().prev("div.lettrfty");
		var linkText = $links;
		$content.toggleClass("short-text2, full-text");
		getShowLinkTextFac(linkText);
		return false;
	  });
	function getShowLinkText(currentText) {
		if(currentText == 'show_more'){
			$('.show_more').addClass('hide');
			$('.show_less').removeClass('hide');
		}
		else{
			$('.show_more').removeClass('hide');
			$('.show_less').addClass('hide');
		}
	}
	function getShowLinkTextFac(currentText) {
		if(currentText == 'show_more'){
			$('.show_more_fac').addClass('hide');
			$('.show_less_fac').removeClass('hide');
		}
		else{
			$('.show_more_fac').removeClass('hide');
			$('.show_less_fac').addClass('hide');
		}
	}
	
});

</script>

<?php

Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/pax_count.js'), 'defer' => 'defer');
?>