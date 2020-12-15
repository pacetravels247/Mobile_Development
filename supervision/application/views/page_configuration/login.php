<?php

$cookie = $GLOBALS['CI']->input->cookie ( 'login_cookie', TRUE ); 
$otp = $GLOBALS['CI']->session->userdata('OTP');
// echo $cookie;exit;
$display=(string) NULL;
$display_1=(string) NULL;

if(isset($cookie) && empty($cookie)==false)
{
	$display="";
	$lang_line_code = '3';
	$fotter = array(array('type' => 'submit', 'lang_line_code' => 3));
}

if(empty($cookie)==true)
{
	$display="send_otp";
	$lang_line_code = '255';
	$fotter = array('type' => 'button');
}

$form_configuration['inputs'] = array(
	'email' => array('type' => 'email', 'label_line_code' => 1, 'DT' => 'PROVAB_SOLID_V80', 'class' => array('login-ip')),
	'password' => array('type' => 'password', 'label_line_code' => 2, 'DT' => 'PROVAB_SOLID_V45', 'class' => array('login-ip')),
	'remember_me' => array('type' => 'checkbox', 'label_line_code' => 9),
	'recover_phone' 		   => array('type' => 'number',   'label_line_code' => 189),

	'opt_number' 		   => array('type' => 'number', 'mandatory'=> false,  'label_line_code' => 255, 'class' => array('hide')),

	'recover_email' => array('type' => 'email',  'label_line_code' => 188)
);
$form_attributes = array('method' => 'POST', 'action' => '');
$form_configuration['form']['login'] = array(
	'form_header' => $form_attributes,
	'sections' => array(array('elements' => array('email', 'password', 'remember_me', 'opt_number'))),
	//'sections' => array(array('elements' => array('email', 'password', 'remember_me'))),
	'form_footer' => $fotter
      
);

$form_configuration['form']['forgot_password'] = array(
	'form_header' => $form_attributes,
	'sections' => array(array('elements' => array('recover_email', 'recover_phone'))),
	'form_footer' => array()
);
