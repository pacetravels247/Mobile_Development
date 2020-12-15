<span class="hide">
<input type="hidden" id="pri_fb_app_id" value="<?=$app_id?>">
</span>
<script>
function statusChangeCallback(response){if(response.status==='connected'){updateCallback(true)}else{updateCallback(false)}}function checkLoginState(){FB.getLoginStatus(function(response){statusChangeCallback(response)})}window.fbAsyncInit=function(){FB.init({appId:document.getElementById('pri_fb_app_id').value,cookie:true,xfbml:true,version:'v2.0'});FB.getLoginStatus(function(response){statusChangeCallback(response)})};(function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(d.getElementById(id))return;js=d.createElement(s);js.id=id;js.src='//connect.facebook.net/en_US/sdk.js';fjs.parentNode.insertBefore(js,fjs)}(document,'script','facebook-jssdk'));function updateCallback(post){FB.api('/me?fields=id,name,email',function(response){var req='';if(post==true){req=response;$.post(app_base_url+'index.php/auth/social_network_login_auth/facebook',req,function(result){if(result&&result.status){login_into_app()}})}})}function login_into_app(){$('.my_account_dropdown').hide();location.reload()}function fb_login(){FB.login(function(response){if(response.authResponse){access_token=response.authResponse.accessToken;user_id=response.authResponse.userID;FB.api('/me?fields=id,name,email',function(response){$.post(app_base_url+'index.php/auth/social_network_login_auth/facebook',response,function(result){if(result&&result.status){login_into_app()}})})}},{scope:'publish_actions,email'})}
</script>
<a href="#" onclick="fb_login()" class="logspecify facecolor"
	id="fb_login">
<div class="mensionsoc">Login with Facebook</div>
</a>
