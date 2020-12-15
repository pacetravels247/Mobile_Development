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
$pop_up = '<div style="width:200px; padding:2px;"><div style="color:#3399FE;"><div style="color: #3399FE; font-size: 16px; font-weight:500;display: block;width: 100%;float: left;margin: 5px 0 0;text-overflow: ellipsis;white-space: nowrap;overflow: hidden;"> '.$hotel_name.', <small style="color:#333;display: block;width:100%;font-weight: 400;margin: 3px 0;"> '.$city.'</small></div> <div style="float:left;font-size: 13px;color: #333;width:100%;"> <div class="rating_empty" style="margin-left: 0px;"></div> </div> </div></div></div>';
?>
<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyAiR9CLZshY_vQpB7z5M7nIGCg16gfo2E8"></script>



	<script>
	var myCenter=new google.maps.LatLng(<?php echo $lat; ?>, <?php echo $lon; ?>);
	function initialize()
	{
		var mapProp = {
		  center:myCenter,
		  zoom:18,
		  mapTypeId:google.maps.MapTypeId.ROADMAP
		  };
		 var image = "<?php echo $GLOBALS['CI']->template->template_images('marker/green_hotel_map_marker.png')?>";
		 //console.log("image"+image);
		var map=new google.maps.Map(document.getElementById("googleMap"),mapProp);
			 <?php 
			 	if ($ll_set == true) { ?>
					var marker=new google.maps.Marker({
						  draggable: false,
						  animation: google.maps.Animation.DROP,
						  position:myCenter,
						  icon: image
					 });
					marker.setMap(map);
					var infowindow = new google.maps.InfoWindow({
						content:'<?php echo $pop_up ?>'
					});
					infowindow.open(map,marker);
					// google.maps.event.addListener(marker, "click", function() {
					google.maps.event.addListener(marker, "mouseover", function() {
						infowindow.open(map,marker);
						//toggleBounce();
						//console.log("here");
					});
					google.maps.event.addListener(marker, "mouseout", function() {
						infowindow.close();
						//toggleBounce();
						//console.log("here");
					});
					
			<?php
				}
			 ?>
		}	
		function toggleBounce() {
        if (marker.getAnimation() !== null) {
          marker.setAnimation(null);
        } else {
          marker.setAnimation(google.maps.Animation.BOUNCE);
        }
      }
	google.maps.event.addDomListener(window, "load", initialize);
	</script>
<div class="panel panel-default">
	<div class="panel-headding">
		<div class="panel-title">
			<?php //echo $hotel_image ?> <?php //echo $hotel_name.' <span class="rating-no">'.print_star_rating($star).' </span> Star ' ?> <small style="color:#000;"><?php //echo $city ?></small>
		</div>
	</div>
	<div class="panel-body ">
		<div id="googleMap" class="col-md-12 hotel-gmap"></div>
		<i class="fa fa-facebook" aria-hidden="true"></i>
	</div>
</div>
<style>
#googleMap {
	height: 480px;
	width:100%;
	background-color:#fff;
	overflow: hidden;
	position: relative;
}
.gm-style-iw {
	width: 210px !important;
	display: block;
	height: 80px;
	top: 10px !important;
	left: 22px !important;
	margin: 2px 0;
	text-align: center;
}
.projimg1 img {
	height: 150px;
	width: 300px;
	max-height: 150px;
}

</style>
<?php
$GLOBALS['CI']->current_page->header_css_resource();
?>