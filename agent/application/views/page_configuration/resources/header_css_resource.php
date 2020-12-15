<?php
array_unshift(Js_Loader::$css, array('href' => JQUERY_UI_LIBRARY_DIR.'jquery-ui.structure.min.css'));
array_unshift(Js_Loader::$css, array('href' => JQUERY_UI_LIBRARY_DIR.'jquery-ui.theme.min.css'));
array_unshift(Js_Loader::$css, array('href' => $GLOBALS['CI']->template->template_css_dir('media_agent.css'), 'media' => 'screen'));
array_unshift(Js_Loader::$css, array('href' => $GLOBALS['CI']->template->template_css_dir('agent.css')));
array_unshift(Js_Loader::$css, array('href' => $GLOBALS['CI']->template->template_css_dir('shared.css')));
array_unshift(Js_Loader::$css, array('href' => $GLOBALS['CI']->template->template_css_dir('theme_style_agent.css'), 'media' => 'screen'));
array_unshift(Js_Loader::$css, array('href' => BOOTSTRAP_CSS_DIR.'bootstrap.min.css', 'media' => 'screen'));
array_unshift(Js_Loader::$css, array('href' => BOOTSTRAP_CSS_DIR.'font-awesome.min.css', 'media' => 'screen'));
?>