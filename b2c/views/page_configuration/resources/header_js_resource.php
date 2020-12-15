<script src="<?php echo JAVASCRIPT_LIBRARY_DIR; ?>jquery-2.1.1.min.js"></script>
<?php
//Js_Loader::$js[] = array('src' => JAVASCRIPT_LIBRARY_DIR.'jquery-migrate-1.2.1.min.js', 'defer' => 'defer');
Js_Loader::$js[] = array('src' => BOOTSTRAP_JS_DIR.'bootstrap.min.js', 'defer' => 'defer');
Js_Loader::$js[] = array('src' => BOOTSTRAP_JS_DIR.'bootstrap-select.min.js', 'defer' => 'defer');
Js_Loader::$js[] = array('src' => JAVASCRIPT_LIBRARY_DIR.'app.js', 'defer' => 'defer');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('javascript.js'), 'defer' => 'defer');
?>
