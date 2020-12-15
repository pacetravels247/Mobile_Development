<?php
if (isset($login) == false || is_object($login) == false) {
	$login = new Provab_Page_Loader('login');
}
?>
<div class="login-box">
	<figure class="login-logo">
		<img src="<?php echo $GLOBALS['CI']->template->domain_images($GLOBALS['CI']->template->get_domain_logo())?>" alt="logo"	class="img-responsive center-block">
	</figure>
	<div class="login-box-body">
		<p class="login-box-msg"><i class="fa fa-power-off"></i> Sign in to continue</p>
		<?php echo $login->generate_form('login', array('email' => '@gmail.com', 'password' => '')); ?>
	</div>
	<div class="panel-footer">
		<?php echo $GLOBALS['CI']->template->isolated_view('general/forgot-password');?>
	</div>
</div>