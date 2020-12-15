<?php
$___favicon_ico = $GLOBALS['CI']->template->domain_images('favicon.ico');

$active_domain_modules = $GLOBALS ['CI']->active_domain_modules;
$master_module_list = $GLOBALS ['CI']->config->item ( 'master_module_list' );
if (empty ( $default_view )) {
	$default_view = $GLOBALS ['CI']->uri->segment ( 1 );
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta
	content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no'
	name='viewport'>
	<link rel="shortcut icon" href="<?=$___favicon_ico?>" type="image/x-icon">
	<link rel="icon" href="<?=$___favicon_ico?>" type="image/x-icon">
	<title><?php echo get_app_message( 'AL001'). ' '.HEADER_TITLE_SUFFIX; ?></title>
		<?php 
		// Loading Common CSS and JS
		$GLOBALS ['CI']->current_page->header_css_resource ();
		$GLOBALS ['CI']->current_page->header_js_resource ();
		Js_Loader::$css[] = array('href' => $GLOBALS['CI']->template->template_css_dir('front_end.css'), 'media' => 'screen');
		echo $GLOBALS ['CI']->current_page->css ();
		?>
	<script>
	var app_base_url = "<?=base_url()?>";
	var tmpl_img_url = '<?=$GLOBALS['CI']->template->template_images(); ?>';
	</script>
  </head>
<body>
	<noscript>
		<img
			src="<?php echo $GLOBALS['CI']->template->template_images('default_loading.gif'); ?>"
			class="img-responsive center-block"></img>
	</noscript>
	<!-- header -->
	<header class="main-header">
		<nav class="navbar navbar-inverse m-0 b-r-0">
			<div class="container">
				<!-- Brand and toggle get grouped for better mobile display -->
				<div class="row">
					<div class="col-md-3">
						<div class="navbar-header">
							<button type="button" class="navbar-toggle collapsed"
								data-toggle="collapse" data-target=".navbar-collapse">
								<span class="sr-only">Toggle navigation</span> <span
									class="icon-bar"></span> <span class="icon-bar"></span> <span
									class="icon-bar"></span>
							</button>
							<a class="navbar-brand" href="<?php echo base_url()?>"><img
								src="<?php echo $GLOBALS['CI']->template->domain_images($GLOBALS['CI']->template->get_domain_logo()); ?>"
								alt="Air tickets - Holidays, Hotel Booking, Visa, Passport and Services"
								class="img-responsive"></a>
						</div>
					</div>
					<div class="col-md-9">
						<!-- Collect the nav links, forms, and other content for toggling -->
						<div class="collapse navbar-collapse">
							<div class="clearfix">
								<p class="navbar-text navbar-right h5">
									24 X 7 Care : <span class="text-i h4">+91 123-4567-890</span>
								</p>
							</div>
							<div class="clearfix">
								<div class="navbar-right">
									<ul class="nav navbar-nav">
										<li class=""><a href="<?php echo base_url()?>"><img
												src="<?php echo $GLOBALS['CI']->template->template_images('icons/home-nav-icon.png'); ?>"
												alt="Home Icon"> Home</a></li>
										<?php
										foreach ( $master_module_list as $k => $v ) {
											if (in_array ( $k, $active_domain_modules )) {
												?>
											<li
											class="<?=((@$default_view == $k || $default_view == $v) ? 'active' : '')?>"><a
											href="<?php echo base_url()?>index.php/general/index/<?php echo ($v)?>?default_view=<?php echo $k?>">
											<img src="<?php echo $GLOBALS['CI']->template->template_images('icons/'.($k).'-nav-icon.png'); ?>"
												alt="<?php echo strtoupper($v)?> Icon"> <?php echo ucfirst($v)?></a></li>
											<?php
											}
										}
										?>
									</ul>
									<!-- Single button -->
									<?php if (is_logged_in_user() == true) { ?>
									<div class="btn-group">
										<button type="button"
											class="btn btn-i b-r-0 navbar-btn dropdown-toggle"
											data-toggle="dropdown" aria-expanded="false">
											<?php echo $GLOBALS['CI']->entity_name?> <span class="caret"></span>
										</button>
										<ul class="dropdown-menu" role="menu">
											<li><a
												href="<?=base_url()?>index.php/user/profile/<?=@$GLOBALS['CI']->name?>">My
													Account</a></li>
											<li><a
												href="<?=base_url().'index.php/auth/change_password'?>">Change
													Password</a></li>
											<li class="divider"></li>
											<li><a
												href="<?=base_url().'index.php/auth/initilize_logout'?>">Logout</a></li>
										</ul>
									</div>
									<?php } else { ?>
										<button type="button" data-toggle="modal"
										data-target="#myModal_1" class="btn btn-t b-r-0 navbar-btn">Sign
										in / Sign up</button>
									<?php } ?>
								</div>
							</div>
						</div>
						<!-- /.navbar-collapse -->
					</div>
				</div>
			</div>
			<!-- /.container -->
		</nav>
	</header>

	<!-- UTILITY NAV -->
	<div class="container-fluid utility-nav clearfix">
		<!-- ROW --> <?php
		if ($this->session->flashdata ( 'message' ) != "") {
			$message = $this->session->flashdata ( 'message' );
			$msg_type = $this->session->flashdata ( 'type' );
			$show_btn = TRUE;
			if ($this->session->flashdata ( 'override_app_msg' ) != "") {
				$override_app_msg = $this->session->flashdata ( 'override_app_msg' );
			} else {
				$override_app_msg = FALSE;
			}
			
			echo get_message ( $message, $msg_type, $show_btn, $override_app_msg );
		}
		?> <!-- /ROW -->
	</div>
	<!-- /UTILITY NAV -->

	<!-- end header -->
	<div class="wrapper">
		<!-- Wrapper Starts -->
		<?php echo $body;?>
	</div>
	<!-- ./wrapper -->

	<footer class="main-footer">
	<?php if (is_logged_in_user() == false) {?>
		<div class="links">
			<div class="container">
				<div class="row">
					<div class="col-md-3">
						<h4>Let's socialize</h4>
												<figure class="logo">
							<a href="<?=base_url()?>"><img
								src="<?php echo $GLOBALS['CI']->template->domain_images($GLOBALS['CI']->template->get_domain_logo()); ?>"
								alt="Air tickets - Holidays, Hotel Booking, Visa, Passport and Services"
								class="img-responsive"></a>
						</figure>
						<ul class="list-inline social-icon">
						
						 <?php 
				$temp=$this->custom_db->single_table_records('social_links');
				if ($temp['data']['0']['status'] == ACTIVE) {?>
            	<li><a href="<?php echo $temp['data']['0']['url_link'];?>" class="facebook"><span class="fa-stack fa-lg"> <i
										class="fa fa-circle fa-stack-2x"></i> <i
										class="fa fa-facebook fa-fw fa-stack-1x fa-inverse"></i>
								</span></a></li>
            	<?php } ?>
            	<?php if ($temp['data']['1']['status'] == ACTIVE) {?>
            	<li><a href="<?php echo $temp['data']['1']['url_link'];?>" class="twitter"><span class="fa-stack fa-lg"> <i
										class="fa fa-circle fa-stack-2x"></i> <i
										class="fa fa-twitter fa-fw fa-stack-1x fa-inverse"></i>
								</span></a></li>
            	<?php } ?>
            	<?php if ($temp['data']['2']['status'] == ACTIVE) {?>
            	<li><a href="<?php echo $temp['data']['2']['url_link'];?>" class="google-plus"><span class="fa-stack fa-lg">
										<i class="fa fa-circle fa-stack-2x"></i> <i
										class="fa fa-google-plus fa-fw fa-stack-1x fa-inverse"></i>
								</span></a></li>
                <?php } ?>
                <?php if ($temp['data']['3']['status'] == ACTIVE) {?>
                <li><a href="<?php echo $temp['data']['3']['url_link'];?> " class="youtube"><span class="fa-stack fa-lg"> <i
										class="fa fa-circle fa-stack-2x"></i> <i
										class="fa fa-youtube fa-fw fa-stack-1x fa-inverse"></i>
								</span></a></li>
                <?php } ?>
                </ul>
					</div>
					
					<!-- FIX ME ****************************************************************************
					 Facebook Like Button
					 <div class="fb-like" data-share="true" data-width="450" data-show-faces="true"></div> 
					 ***************************************************************************************-->
					<div class="col-md-3 text-capitalize col-xs-6 col-sm-4">
						<h4>Product Offering</h4>
						<ul class="list-unstyled">
							<li><a href="#">Flight Search</a></li>
							<li><a href="#">Hotel Search</a></li>
							<li><a href="#">Cars Search</a></li>
							<li><a href="#">Vocation Search</a></li>
							<li><a href="#">Hot Deals Search</a></li>
							<li><a href="#">Apartment Search</a></li>
						</ul>
					</div>
					<div class="col-md-3 text-capitalize col-xs-6 col-sm-4">
						<h4>Travel Specialists</h4>
						<ul class="list-unstyled">
							<li><a href="#">New York</a></li>
							<li><a href="#">New Delhi</a></li>
							<li><a href="#">Mumbai</a></li>
							<li><a href="#">Singapore</a></li>
							<li><a href="#">London</a></li>
							<li><a href="#">Dubai</a></li>
						</ul>
					</div>
					<div class="clearfix visible-xs-block"></div>
					<div class="col-md-3 col-sm-4">
						<h4>Newsletter</h4>
						<form>
							<div class="form-group">
								<label class="sr-only" for="newsemail">Enter Email</label>
								<div class="input-group">
									<input type="email" class="form-control b-r-0" id="newsemail"
										placeholder="Enter Email" name="email">
									<div class="input-group-btn">
										<button type="button" class="btn btn-primary b-r-0"
											id="newsemailbtn">
											<i class="fa fa-arrow-right fa-fw"></i>
										</button>
									</div>
								</div>
							</div>
						</form>
						<h5>Customer Support</h5>
						<span class="text-t"><small><?php echo 'Contact Number'; ?></small></span>
						<br> <span><small><?php echo 'contact_email'; ?></small></span>
					</div>
				</div>
			</div>
		</div>
		<?php } ?>
		<div class="copyright">
			<div class="container">
				<div class="row">
					<div class="col-md-4 xs-text-center">
						<p><?php echo 'copyright' ?></p>
					</div>
					<div class="col-md-8">
						<ul class="list-inline text-right text-capitalize xs-text-center txtwrapRow">
							<li><a href="#">Home</a></li>
							<li><a href="#">About</a></li>
							<li><a href="#">Last Minute</a></li>
							<li><a href="#">Early Booking</a></li>
							<li><a href="#">Special Offers</a></li>
							<li><a href="#">Blog</a></li>
							<li><a href="#">Contact</a></li>
							<li><a href="#" id="j-scroll-up"><i
									class="fa fa-chevron-up fa-fw"></i></a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</footer>
	<!-- LOGIN MODAL OPEN -->
	<?=$GLOBALS['CI']->template->isolated_view('general/login')?>
	<!-- LOGIN MODAL CLOSE -->
	<?php
	Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/datepicker.js'), 'defer' => 'defer');
	Provab_Page_Loader::load_core_resource_files();
		// Loading Common CSS and JS
		$GLOBALS ['CI']->current_page->footer_js_resource ();
		echo $GLOBALS ['CI']->current_page->js ();
		?>
	<script>
		$(document).ready(function(){			
			$("#newsemailbtn").click(function(e){
				var email_regex = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
				var email = $("#newsemail").val();
				if (!email.match(email_regex) || email.length == 0) {
				$('#newsemail').val("Enter valid email ID"); // This Segment Displays The Validation Rule For Email
				//$("#newsemail").focus();
				return false;
				} else {
					$.ajax({
						url:app_base_url+"index.php/general/email_subscription",
						data: {email:email},
						success:function(data){							
							if(data == 'success'){
								$('#newsemail').val("successfully subscribed");
							}else if(data == 'already'){
								$('#newsemail').val("Already subscribed");
							}else{
								$('#newsemail').val("Subscribtion Failed");
							}
						}					
					});
				}			
		 });
		});
	</script>
</body>
</html>
