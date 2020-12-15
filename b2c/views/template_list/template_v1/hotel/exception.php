<div class="jumbotron text-center">
<h1>OOOOPS!!!!!!!</h1>
<p class="text-danger">Hotel Booking Engine Could Not Process Your Request
!!!</p>
<p class="text-danger">Please Try Again To Process Your New Request</p>
<?php
if (isset($eid) == true and strlen($eid) > 0) {
	?>
	<p class="text-primary">You Can Use <strong><?=$eid?></strong> Reference Number To Talk To Our Customer Support</p>
	<?php
}
?>
<p><a class="btn btn-primary btn-lg" href="<?php echo base_url()?>index.php/general/index/hotel/?default_view=<?php echo META_ACCOMODATION_COURSE?>"
	role="button">Click Here To Start New Search</a></p>
</div>
<?php
if (isset($log_ip_info) and $log_ip_info == true and isset($eid) == true) {
?>
<script>
$(document).ready(function() {
	$.getJSON("http://ip-api.com/json", function(json) {
		$.post(app_base_url+"index.php/ajax/log_event_ip_info/<?=$eid?>", json);
	});
});
</script>
<?php
}
?>
