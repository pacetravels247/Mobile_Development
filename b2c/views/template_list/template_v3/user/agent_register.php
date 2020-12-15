<div class="register-page">
	<div class="row">
		<div class="col-md-offset-2 col-md-8">
			<div class="row register-box-body">
				<div class="col-md-6">
					<h4 class="login-box-msg">Register a new Account</h4>
					<form method="post" action="" autocomplete="off">
						<div class="form-group has-feedback">
							<input required name="first_name"
								value="<?=set_value('first_name')?>" type="text"
								placeholder="Name" class="form-control"> <span
								class="fa fa-user form-control-feedback"></span>
						<?=form_error('first_name', '<div class="text-danger">', '</div>');?>
					</div>
						<div class="form-group has-feedback">
							<input required name="phone" value="<?=set_value('phone')?>"
								type="phone" placeholder="Mobile" class="form-control"> <span
								class="fa fa-phone form-control-feedback"></span>
						<?=form_error('phone', '<div class="text-danger">', '</div>');?>
					</div>
						<div class="form-group has-feedback">
							<input required name="email" value="<?=set_value('email')?>"
								type="email" placeholder="Email" class="form-control"> <span
								class="fa fa-envelope form-control-feedback"></span>
						<?=form_error('email', '<div class="text-danger">', '</div>');?>
					</div>
						<div class="form-group has-feedback">
							<input required name="password" value="" type="password"
								placeholder="Password" class="form-control"> <span
								class="fa fa-lock form-control-feedback"></span>
						<?=form_error('password', '<div class="text-danger">', '</div>');?>
					</div>
						<div class="form-group has-feedback">
							<input required name="confirm_password" value="" type="password"
								placeholder="Retype password" class="form-control"> <span
								class="glyphicon glyphicon-log-in form-control-feedback"></span>
						<?=form_error('confirm_password', '<div class="text-danger">', '</div>');?>
					</div>
				
					</form>
				</div>

			</div>
		</div>
	</div>
</div>
</div>
<style>
.login-box, .register-box {
	margin: 0% auto;
	width: 360px;
}

.login-box-body, .register-box-body {
	background: #fff none repeat scroll 0 0;
	border-top: 0 none;
	color: #666;
	padding: 20px;
}

.login-box-msg, .register-box-msg {
	margin: 0;
	padding: 0 20px 20px;
	text-align: center;
}

.social-auth-links {
	margin: 10px 0;
}

.btn.btn-flat {
	border-radius: 0;
	border-width: 1px;
	box-shadow: none;
}

.login-page, .register-page {
	background: #d2d6de none repeat scroll 0 0;
}

.btn-facebook {
	background-color: #3b5998;
	border-color: rgba(0, 0, 0, 0.2);
	color: #fff;
}

.btn-social {
	overflow: hidden;
	padding-left: 44px;
	position: relative;
	text-align: left;
	text-overflow: ellipsis;
	white-space: nowrap;
}

.btn-google {
	background-color: #dd4b39;
	border-color: rgba(0, 0, 0, 0.2);
	color: #fff;
}
</style>