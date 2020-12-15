<?php
	$social1 = is_active_social_login ( 'facebook' );
	$social2 = is_active_social_login ( 'twitter' );
	$social3 = is_active_social_login ( 'googleplus' );

	$addr = $this->custom_db->single_table_records('api_country_list', '*');
	$addr = $addr['data'];
	
	$login_auth_loading_image	 = '<div class="text-center loader-image"><img src="'.$GLOBALS['CI']->template->template_images('loader_v3.gif').'" alt="please wait"/></div>';
	
	if ($social1 == true) {
		$GLOBALS['CI']->load->library('social_network/facebook');
	}

	if ($social2 == true) {
		//Not Yet Active
	}

	if ($social3 == true) {
		$GLOBALS['CI']->load->library('social_network/google');
	}

	if (isset ( $login ) == false || is_object ( $login ) == false) {
		$login = new Provab_Page_Loader ( 'login' );
	}
	if (is_logged_in_user () == true) {
		if($social1 == true){
			echo '<div class="hide">'.$GLOBALS['CI']->facebook->login_button ().'</div>';
		}
	?>

<?php } else { ?>
<div class="my_account_dropdown mysign exploreul">
    <button type="button" class="close log_close" data-dismiss="modal">&times;</button>
	<div class="signdiv">
		<div class="insigndiv for_sign_in">
			<div class="leftpul">
				<?php
					if ($social1) {
						
						echo $GLOBALS['CI']->facebook->login_button ();
					}
					
					if ($social2) {
						?>
				<a class="logspecify tweetcolor">
					<span class="fa fa-twitter"></span>
					<div class="mensionsoc">Login with Twitter</div>
				</a>
				<?php
					}
					
					if ($social3) {
						?>
				<?php
					echo $GLOBALS['CI']->google->login_button ();?>
				<?php } ?>
			</div>
			<?php $no_social=no_social(); if($no_social != 0) {?>
			<div class="centerpul">
				<div class="orbar"> <strong>Or</strong> </div>
			</div>
			<?php }?>
			<div class="ritpul">
				<form role="form" id="login" action="" autocomplete="off"
					name="login">
					<div class="rowput"> <span class="fa fa-user"></span>
						<input type="email"
							data-content="Username Ex: john@gmail.com"
							data-trigger="hover focus" data-placement="bottom"
							data-original-title="Here To Help" data-toggle="popover"
							data-container="body" id="email"
							class="email form-control logpadding" placeholder="Username"
							required="required" name="email">
					</div>
					<div class="rowput"> <span class="fa fa-lock"></span>
						<input type="password"
							id="password" class="password form-control logpadding"
							placeholder="Password" required="required" name="password"
							value="" >
					</div>
					<div class="clearfix"></div>
					<div id="login-status-wrapper" class="alert alert-danger"
						style="display: none">
						<p> <i class="fa fa-warning"></i> </p>
					</div>
					<div class="clearfix"></div>
					<div id="login_auth_loading_image" style="display: none">
						<?=$login_auth_loading_image?>
					</div>
					<div class="clearfix"></div>
					
					<div class="misclog"> <a class="hand-cursor forgtpsw forgot_pasword" id="forgot-password">Forgot Password ? </a> </div>
					<div class="clearfix"></div>
					<button class="submitlogin" id="login_submit">Login</button>
					<div class="clear"></div>
					<div class="dntacnt"> New User? <a class="hand-cursor open_register">Sign Up</a> </div>
				</form>
			</div>
		</div>
		<div class="newacount_div for_sign_up">
			<div class="slpophd_new">Register with PaceTravel</div>
			<div class="othesend_regstr">
				<div class="ritpul">
					<form autocomplete="off" method="post" id="register_user_form">
						<div class="rowput has-feedback hide">
							<span class="fa fa-user"></span>
							<input type="text" class="validate_user_register form-control logpadding" value="Customer" placeholder="Name" name="first_name" required="" />
						</div>
						<div class="rowput has-feedback">
							<span class="fa fa-envelope"></span>
							<input type="email" class="validate_user_register form-control logpadding" placeholder="Email-Id" value="" name="email" required="" />
							<span class="err_msg"> Email Field is mandatory</span>
						</div>
						<div class="rowput has-feedback">
							<span class="fa fa-mobile"></span>							
							<select name="country_code" class="validate_user_register form-control logpadding" required="">
							  <option value = '' >select country code</option>
                              <?php
                              foreach ($addr as $key => $value) {
                              	echo "<option value = '".$value['country_code']."'>".$value['country_code'].' '.$value['name']."</option>";
                              } 
                              ?>
                            </select>
                            <span class="err_msg"> country code Field is mandatory</span>
						</div>    
						<div class="rowput has-feedback">
							<span class="fa fa-phone"></span>
							<input type="phone" class="validate_user_register numeric form-control logpadding" maxlength="10" placeholder="Mobile Number" value="" name="phone" required="" />
							<span class="err_msg"> phone Field is mandatory</span>
						</div>
						<div class="rowput has-feedback">
							<span class="fa fa-lock"></span>
							<input type="password" class="validate_user_register form-control logpadding" placeholder="New Password" value="" name="password" required="">
							<span class="err_msg"> password Field is mandatory</span>
						</div>
						<div class="rowput has-feedback">
							<span class="fa fa-lock"></span>
							<input type="password" class="validate_user_register form-control logpadding" placeholder="Retype Password" value="" name="confirm_password" required="" />
							<span class="err_msg">confirm password Field is mandatory</span>
						</div>
						
						<div class="clearfix"></div>
						<div class="row_submit">
							<div class="col-xs-12 nopad">
								<div class="agree_terms">
									<div class="squaredThree">
										<input type="checkbox" id="register_tc" class="airlinecheckbox validate_user_register" name="tc" required="">
										<label for="register_tc" class="register_tc"></label>
									</div>
									<label class="lbllbl" for="tc">By signing up you accept our <a target="_balnk" href="<?=base_url()?>index.php/general/cms/terms-conditions">terms of use and privacy policy</a></label>
								</div>
							</div>
							<div class="col-xs-12 nopad">
								<button type="submit" id="register_user_button" class="submitlogin">Register</button>
							</div>
						</div>
						<div class="loading hide" id="loading"><img src="<?php echo $GLOBALS['CI']->template->template_images('loader_v3.gif')?>"></div>
						<div class="rowput alert alert-success hide" id="register-status-wrapper"></div>
						<div class="rowput alert alert-danger hide" id="register-error-msg"></div>
						<div class="clearfix"></div>
						<!-- <div class="text_info">(You will receive an e-mail containing the account verification link.)</div> -->
					</form>
					<a class="open_sign_in">I already have an Account</a> 
				</div>
			</div>
		</div>
		<div class="actual_forgot for_forgot">
			<div class="slpophd_new">Forgot Password?</div>
			<div class="othesend_regstr">
				<div class="rowput">
					<span class="fa fa-envelope"></span>
					<input type="text" name="forgot_pwd_email" id="recover_email" class="logpadding form-control" placeholder="Enter Email-Id" />
					<span>This Field is mandatory</span>
				</div>
				<div class="rowput">
					<span class="fa fa-mobile"></span>
					<input type="text" name="forgot_pwd_phone" id="recover_phone"	class="logpadding form-control" placeholder="Registered Mobile Number " />
					<span>This Field is mandatory</span>
				</div> 
				<div class="clearfix"></div>
				<div id="recover-title-wrapper" class="alert alert-success"
					style="display: none">
					<p> <i class="fa fa-warning"></i> <span id="recover-title"></span> </p>
				</div>
				<div class="clearfix"></div>
				<button class="submitlogin" id="reset-password-trigger">Send EMail</button>
				<div class="clearfix"></div>
				<a class="open_sign_in">I am an Existing User</a>
			</div>
		</div>
	</div>
</div>
<!-- New Forgot Password Modal -->
<div id="forgotpaswrdpop" class="altpopup">
	<div class="comn_close_pop fa fa-times closepopup"></div>
	<div class="insideforgot">
		<div class="slpophd">Forgot Password?</div>
		<div class="othesend">
			<div class="rowput">
				<span class="fa fa-envelope"></span>
				<input type="text" name="forgot_pwd_email" id="recover_email_book" class="logpadding form-control" placeholder="Enter Email" required="required" />
				<span>This Field is mandatory</span>
			</div>
			<div class="rowput">
				<span class="fa fa-mobile"></span>
				<input type="text" name="forgot_pwd_phone" id="recover_phone_book"	class="logpadding form-control" placeholder="Enter Mobile Number" required="required" />
				<span>This Field is mandatory</span>
			</div>
			<div class="clearfix"></div>
			<div id="recover-title-wrapper-book" class="alert alert-success"
				style="display: none">
				<p> <i class="fa fa-warning"></i> <span id="recover-title-book"></span> </p>
			</div>
			<div class="centerdprcd">
				<button class="bookcont" id="reset-password-trigger-book">Send Mail</button>
			</div>
		</div>
	</div>
</div>
<?php }
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/login.js'), 'defer' => 'defer');
?>
<style>
	#register_user_form .err_msg.invalid-ip{
		color: #bf7070;
    	display: block;
    	border: 0 !important;
	}
	#register_user_form .err_msg{
		display: none;
	}
</style>