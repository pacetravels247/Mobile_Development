<?php
$lat = '24';
$lon = '45';
$ll_set = false;
$star = 0;
$city = '';
$hotel_name = '';
$description = '';
$phone_number = '';
$hotel_image = '';
if (isset($data) == true and empty($data) == false and $data != '') {
	if ($data['latitude'] != '' and $data['longtitude'] != '') {
		$lat = $data['latitude'];
		$lon = $data['longtitude'];
		$ll_set = true;
	}
	if (isset($data['star_rating']) == true) {
		$star = intval($data['star_rating']);
	}
	
	if ($data['city']) {
		$city = $data['city'];
	}
	if ($data['hotel_name']) {
		$hotel_name = $data['hotel_name'];
	}
	if (isset($data['phone'])) {
		$phone_number = $data['phone'];
	} else {
		$phone_number = '';
	}
	if (isset($data['hotel_image'])) {
		$style_pop_up = 'height="60" width="60"';
		$style_image = 'height="30" width="30"';
		$pop_up_hotel_image = '<img src="'.$data['hotel_image'].'" '.$style_pop_up.'>';
		$hotel_image = '<img src="'.$data['hotel_image'].'" '.$style_image.'>'; 
	} else {
		$hotel_image = $pop_up_hotel_image = '<img>';
	}
}
$pop_up = '<div style="width:300px; padding:2px;"><div style="color:#3399FE;"> <div class="projimg1" style="float:left;"> '.$pop_up_hotel_image.'</div> <div style=" color:color: #3399FE;; font-size: 14px; font-weight:bold;"> '.$hotel_name.', <small style="color:#000;"> '.$city.'</small></div> <div style="float:left"> <div class="rating_empty" style="margin-left: 0px;"> <span class="rating-no">'.print_star_rating($star).' </span> Star</div> </div> </div></div></div>';
?>
<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDY0kkJiTPVd2U7aTOAwhc9ySH6oHxOIYM&sensor=false"></script>



	<script>
	var myCenter=new google.maps.LatLng(<?php echo $lat; ?>, <?php echo $lon; ?>);
	function initialize()
	{
		var mapProp = {
		  center:myCenter,
		  zoom:13,
		  mapTypeId:google.maps.MapTypeId.ROADMAP
		  };
		
		var map=new google.maps.Map(document.getElementById("googleMap"),mapProp);
		 <?php 
		 	if ($ll_set == true) { ?>
				var marker=new google.maps.Marker({
					  position:myCenter,
				 });
				marker.setMap(map);
				var infowindow = new google.maps.InfoWindow({
					content:'<?php echo $pop_up ?>'
				});

				google.maps.event.addListener(marker, "click", function() {
					infowindow.open(map,marker);
				});
		<?php
			}
		 ?>
		}	
	google.maps.event.addDomListener(window, "load", initialize);
	</script>
<div class="panel panel-default">
	<div class="panel-headding">
		<div class="panel-title">
			<?php echo $hotel_image ?> <?php echo $hotel_name.' <span class="rating-no">'.print_star_rating($star).' </span> Star ' ?> <small style="color:#000;"><?php echo $city ?></small>
		</div>
	</div>
	<div class="panel-body">
		<div id="googleMap" class="col-md-12"></div>
	</div>
</div>
<style>
#googleMap {
    height: 410px;
    width: 790px;
    background-color: #E5E3DF;
    overflow: hidden;
    position: relative;
}
</style>
<?php
$GLOBALS['CI']->current_page->header_css_resource();
?>