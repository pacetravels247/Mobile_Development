<?php
array_unshift(Js_Loader::$css, array('href' => JQUERY_UI_LIBRARY_DIR.'jquery-ui.structure.min.css'));
array_unshift(Js_Loader::$css, array('href' => DATEPICKER_LIBRARY_DIR.'jquery.datetimepicker.css'));
array_unshift(Js_Loader::$css, array('href' => JQUERY_UI_LIBRARY_DIR.'jquery-ui.theme.min.css'));
array_unshift(Js_Loader::$css, array('href' => $GLOBALS['CI']->template->template_css_dir('shared.css'), 'media' => 'screen'));
array_unshift(Js_Loader::$css, array('href' => $GLOBALS['CI']->template->template_css_dir('theme_style.css'), 'media' => 'screen'));
array_unshift(Js_Loader::$css, array('href' => BOOTSTRAP_CSS_DIR.'bootstrap.min.css', 'media' => 'screen'));
array_unshift(Js_Loader::$css, array('href' => BOOTSTRAP_CSS_DIR.'font-awesome.min.css', 'media' => 'screen'));
array_unshift(Js_Loader::$css, array('href' => BOOTSTRAP_CSS_DIR.'bootstrap-select.css', 'media' => 'screen'));
?>