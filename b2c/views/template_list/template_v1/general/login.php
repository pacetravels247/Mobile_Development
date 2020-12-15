<?php
if (isset($login) == false || is_object($login) == false) {
	$login = new Provab_Page_Loader('login');
}
?>
<div class="modal fade" id="myModal_1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Sign In To Continue <?=domain_name()?></h4>
      </div>
      <div class="modal-body">
        <?php echo $login->generate_form('login', array('email' => '@gmail.com', 'password' => '')); ?>
        <?php echo get_default_image_loader();//data-utility-loader?>
        <div id="login-status-wrapper" class="alert alert-danger" style="display:none"><p><i class="fa fa-warning"></i>
		</p></div>
      </div>
      <div class="modal-footer">
      <a class="hand-cursor pull-left" data-toggle="modal" data-target="#myModal_2" href="#" id="forgot-password">Forgot Password ? </a>
      <a class="hand-cursor" href="<?=base_url().'index.php/auth/register'?>">New User? Sign Up</a>
      </div>
      </div>
    </div>
  </div>

<!-- Modal -->
<div class="modal fade" id="myModal_2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><i class="fa fa-desktop"></i> Reset Password</h4>
      </div>
      <div class="modal-body">
        <div class="alert ">
        	<p><i class="fa fa-lock"></i> Please Provide Us Your Details To Reset Your Password</p>
        </div>
        <?php echo get_default_image_loader();//data-utility-loader?>
        <?php 
        echo $login->generate_form('forgot_password');
        ?>
        <div id="recover-title-wrapper" class="alert alert-success" style="display:none"><p><i class="fa fa-warning"></i> 
        <span id="recover-title"></span></p></div>
      </div>
      <div class="modal-footer ">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-success" id="reset-password-trigger">Reset Password Now</button>
      </div>     
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
	$('#login_submit').on('click', function(e) {
		e.preventDefault();
		var _username = $('#email').val();
		var _password = $('#password').val();
		if (_username == '' || _password == '') {
			$('#login-status-wrapper').text('Please Enter Username And Password To Continue!!!').show();
		} else {
			$('.data-utility-loader', $('#myModal_1')).show();
			$('#login-status-wrapper').text('Please Wait!!!').hide();
			$.post(app_base_url+"index.php/auth/login/", {username: _username, password: _password}, function(response) {
				if (response.status) {
					$('#myModal_1').hide();
					window.location.reload();
				} else {
					$('#login-status-wrapper').text(response.data).show();
				}
				$('.data-utility-loader', $('#myModal_1')).hide();
			});
		}
	});

	//Reset Password
	$('#reset-password-trigger').on('click', function(e) {
		e.preventDefault();
		$('#recover-title-wrapper').hide();
		$('.data-utility-loader', $('#myModal_2')).show();
		$.post(app_base_url+"index.php/auth/forgot_password/", {email: $('#recover_email').val(), 'phone':$('#recover_phone').val()}, function(response) {
			if (response.status) {
				$('#recover-title-wrapper').removeClass('alert-danger').addClass('alert-success');
			} else {
				$('#recover-title-wrapper').removeClass('alert-success').addClass('alert-danger');
			}
			$('#recover-title').text(response.data);
			$('#recover-title-wrapper').show();
			$('.data-utility-loader', $('#myModal_2')).hide();
		});
	});
});
</script>
