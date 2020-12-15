<?php
Js_Loader::$js[] = array('src' => BOOTSTRAP_JS_DIR.'bootstrap.min.js', 'defer' => 'defer');
Js_Loader::$js[] = array('src' => JAVASCRIPT_LIBRARY_DIR.'app.js', 'defer' => 'defer');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('javascript.js'), 'defer' => 'defer');
?>
<!-- JS For -->
<script src="<?php echo JAVASCRIPT_LIBRARY_DIR.'jquery-ui.min.js'; ?>" defer></script>
<script src="<?php echo $GLOBALS['CI']->template->template_js_dir('page_resource/login.js'); ?>" defer ></script>
<!-- Custom JavaScript You Can Edit -->
<script src="<?php echo $GLOBALS['CI']->template->template_js_dir('general.js'); ?>" defer ></script>
<script>
var project_cookie_path = "<?=PROJECT_COOKIE_PATH?>";
</script>