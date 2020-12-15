<meta name="google-signin-client_id" content="<?=$client_id?>" />
<script src="https://apis.google.com/js/platform.js"  async defer></script>
<?php if(is_logged_in_user()) { ?>
<script src="https://apis.google.com/js/platform.js?onload=onLoad"  async defer></script>
<script>
function onLoad() {
	gapi.load('auth2', function() {
		gapi.auth2.init();
	});
}
</script>
<?php } ?>