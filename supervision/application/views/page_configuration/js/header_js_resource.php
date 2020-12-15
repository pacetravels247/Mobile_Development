<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
   <![endif]-->

<!-- Core Jquery -->
<!-- For IE 8 Support For JavaScript -->
<!--
   <script src="<?php echo JAVASCRIPT_LIBRARY_DIR; ?>jquery-1.11.1.min.js"></script>
    If You Don't Want IE 8 Browser Support Please Comment Out Major Version Of Jquery 1 and Uncomment Major Version Of Jquery 2 -->
<script src="<?php echo JAVASCRIPT_LIBRARY_DIR; ?>jquery-2.1.1.min.js"></script>
<!-- This Jquery for Older Plugins
   <script src="<?php echo JAVASCRIPT_LIBRARY_DIR; ?>jquery-migrate-1.2.1.min.js"></script>
   Core Bootstrap JavaScript -->
<script src="<?php echo BOOTSTRAP_JS_DIR; ?>bootstrap.min.js"></script>
<!-- Custom JavaScript You Can Edit -->
<script src="<?php echo $GLOBALS['CI']->template->template_js_dir('javascript.js'); ?>" async defer></script>
<script src="<?php echo $GLOBALS['CI']->template->template_js_dir('general.js'); ?>" ></script>
<?php if(is_logged_in_user() == true){ //If User is Logged in, then iclude the notification script?>
	<script src="<?php echo $GLOBALS['CI']->template->template_js_dir('page_resource/notification.js'); ?>" ></script>
<?php }?>
