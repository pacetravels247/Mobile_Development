<?php
$lat = '24';
$lon = '45';
$name = '';
$description = '';
if (isset($data) == true and empty($data) == false and $data != '') {
	if ($data['latitude'] != '' and $data['longtitude'] != '') {
		$lat = $data['latitude'];
		$lon = $data['longtitude'];
		$ll_set = true;
	}
	if ($data['name']) {
		$name = $data['name'];
	}
}
$pop_up = '<div style="width:300px; padding:2px;">Event Logged From Here. IP Address : '.$data['ip'].'</div>';
?>
<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDY0kkJiTPVd2U7aTOAwhc9ySH6oHxOIYM&sensor=false"></script>



	<script>
	var myCenter=new google.maps.LatLng(<?php echo $lat; ?>, <?php echo $lon; ?>);
	function initialize()
	{
		var mapProp = {
		  center:myCenter,
		  zoom:4,
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
			<?=$name;?>
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