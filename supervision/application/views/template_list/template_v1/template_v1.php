<?php
$GLOBALS['CI']->load->model('custom_db');
$custom_menu_search = $GLOBALS['CI']->custom_db->single_table_records('custom_menu_search');
if($_SERVER['HTTP_X_FORWARDED_FOR']=="157.50.108.123")
{
  // debug($GLOBALS['CI']->custom_menu_search);exit;
}
// exit("CMS1"); 

?>
<style>
.user-footer .btn-flat {
    padding: 5px 8px;
    margin: 0px 2px;
}
.navbar-nav > .user-menu > .dropdown-menu {
  width: 281px !important;
}
.select2-container { width: 100% !important; }
.company_name {
  font-size: 20px;
  font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
  padding: 8px;
  float: left;
}
</style>
<?php
$___favicon_ico = $GLOBALS['CI']->template->domain_images('favicon.ico');
?>
<?php
  $mini_loading_image  = '<div class="text-center loader-image"><img src="'.$GLOBALS['CI']->template->template_images('loader_v3.gif').'" alt="Loading........"/></div>';

  $controller_name = $GLOBALS["CI"]->uri->segment(1);
  echo "<script>var load_select2 = true;</script>";
  if($controller_name == "tours")
  {
    echo "<script>var load_select2 = false;</script>";
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta name="keywords" content="<?= META_KEYWORDS ?>">
    <meta name="description" content="<?= META_DESCRIPTION ?>">
    <link rel="shortcut icon" href="<?=$___favicon_ico?>" type="image/x-icon">
  <link rel="icon" href="<?=$___favicon_ico?>" type="image/x-icon">
    <title><?php echo get_app_message( 'AL001'). ' '.HEADER_TITLE_SUFFIX; ?></title>
    <?php //Loading Common CSS and JS
      $this->current_page->header_css_resource();
      $this->current_page->header_js_resource();
      echo $GLOBALS ['CI']->current_page->css ();
    ?>
    <link href="<?php echo $GLOBALS['CI']->template->template_css_dir('bootstrap-toastr/toastr.min.css');?>" rel="stylesheet" defer>
    <link href="<?php echo $GLOBALS['CI']->template->template_css_dir('bootstrap2-toggle.min.css');?>" rel="stylesheet" defer>
    <link href="<?php echo $GLOBALS['CI']->template->template_css_dir('bootstrap-duallistbox-master/src/bootstrap-duallistbox.css');?>" rel="stylesheet">
    <script src="<?php echo $GLOBALS['CI']->template->template_js_dir('bootstrap-toastr/toastr.min.js'); ?>"></script>
    <script src="<?php echo $GLOBALS['CI']->template->template_js_dir('bootstrap2-toggle.min.js'); ?>"></script>
     <script src="<?php echo $GLOBALS['CI']->template->template_js_dir('bootstrap-duallistbox-master/src/jquery.bootstrap-duallistbox.js'); ?>"></script>
<script defer src='<?php echo $GLOBALS['CI']->template->template_js_dir('datatables_js/jquery.dataTables.min.js');?>'></script>
    <script defer src='<?php echo $GLOBALS['CI']->template->template_js_dir('datatables_js/dataTables.bootstrap.min.js');?>'></script>

    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.4 -->
    <!-- Theme style -->
    <link href="<?php echo $this->template->template_css_dir('AdminLTE.min.css')?>" rel="stylesheet" type="text/css" />
    <!-- AdminLTE Skins. Choose a skin from the css/skins 
         folder instead of downloading all of them to reduce the load. -->
    <link href="<?php echo $this->template->template_css_dir('_all-skins.min.css')?>" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['CI']->template->template_css_dir('datatables_css/dataTables.bootstrap.min.css'); ?>">

   <link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['CI']->template->template_css_dir('page_resource/select2.min.css'); ?>">
    <script defer src='<?php echo $GLOBALS['CI']->template->template_js_dir('page_resource/select2.min.js');?>'></script>
  <script>
  var app_base_url = "<?=base_url()?>";
  var tmpl_img_url = '<?=$GLOBALS['CI']->template->template_images(); ?>';
  </script>
  </head>
  <body class="fixed skin-black-light sidebar-mini sidebar-collapse">
  <noscript><img src="<?php echo $GLOBALS['CI']->template->template_images('default_loading.gif'); ?>"
      class="img-responsive center-block"></img></noscript>
    <div class="wrapper">

  <!-- HEADER starts -->  
  <?php 
  //check if the user is loggedin and load respective data
  //START IF - PAGE After LOGIN
  if (is_logged_in_user()) {
  ?>
      <header class="main-header">
        <!-- Logo -->
        <a href="<?php echo base_url()?>" class="logo bg-white">
          <!-- mini logo for sidebar mini 50x50 pixels -->
          <!-- <span class="logo-mini"><img src="<?php echo $GLOBALS['CI']->template->domain_images('mobile_logo.png'); ?>" alt="logo" class="img-responsive center-block"></span> -->
           <span class="logo-mini"><img src="<?php echo $GLOBALS['CI']->template->domain_images($GLOBALS['CI']->template->get_domain_logo()); ?>" alt="logo" class="img-responsive center-block"></span>
          <!-- logo for regular state and mobile devices -->
          <span class="logo-lg"><img src="<?php echo $GLOBALS['CI']->template->domain_images($GLOBALS['CI']->template->get_domain_logo())?>" alt="logo" class="img-responsive center-block"></span>
        </a>

        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
        <span class="company_name">Pacetravels</span>
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
          </a>
          <!-- Navbar Right Menu -->
          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
              <!-- Messages: style can be found in dropdown.less-->
               <?php
                 if (is_domain_user()) { if(check_user_previlege('p114')):?>

                 <li class="">
                      <div class="text-center loader-image dash-bal-btn">
                          <select id="custom_search" class="form-control" name="status" onchange="menu_search(this.value);">
                            <option value="">Search menu</option>
                            <?php
                            //debug();
                                foreach ($GLOBALS['CI']->custom_menu_search['data'] as $key => $value) {
                                  $only_menu_link = explode("supervision", $value["menu_url"])[1];
                                  $menu_url = base_url($only_menu_link);
                                  //debug($only_menu_link); exit;
                            ?>
                            <option value="<?=$menu_url;?>"><?=$value['menu_name'];?></option>
                            <?php } ?>
                        </select>
                      </div>                        
                  </li> 
					<li class="">
						<div class="text-center loader-image dash-bal-btn">
							<button   data-toggle="collapse" class="btn btn-primary btn-sm  show-low-bal-btn"  data-target="#show_low_balance">Low Balance <i class="fal fa-angle-up" aria-hidden="true"></i>
							</button> 
							
						</div>                        
					</li>   
					<div class="modal fade" id="show_low_balance" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title" id="exampleModalLabel">Details</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									  <span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">
									<table class="table">
										<thead>
											<tr>
											  <th scope="col">Sl.no</th>
											  <th scope="col">Type</th>
											  <th scope="col">Balance</th>
											  <th scope="col">Status</th>
											</tr>
										</thead>
										<tbody id="low_bal_text">
											<tr>
												<td colspan="3">
													<img id="img-load" class="hidden" src="<?php echo $GLOBALS['CI']->template->template_images('loader_v3.gif') ?>" alt="Loading........"/>
												</td>
											</tr>
										</tbody>
									</table>
									<img id="img-load" class="hidden" src="<?php echo $GLOBALS['CI']->template->template_images('loader_v3.gif') ?>" alt="Loading........"/>
								</div>
								<div class="modal-footer">
									<button type="button" class="close btn btn-secondary" data-dismiss="modal">Close</button>
								</div>
							</div>
						</div>
					</div>
                  <!--<li class="">
                      <div class="text-center loader-image dash-bal-btn">
                          <button   data-toggle="collapse" class="btn btn-primary btn-sm  show-bal-btn"  data-target="#show-balance">API Balance <i class="fal fa-angle-up" aria-hidden="true"></i>
                          </button> 
                          <img id="img-load" class="hidden" src="<?php echo $GLOBALS['CI']->template->template_images('loader_v3.gif') ?>" alt="Loading........"/>
                      </div>                        
                  </li>           
                      <li id="show-balance" class="collapse">
                      </li>   -->  
              <?php
       endif; }
              ?>
              <li class="dropdown messages-menu hide">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <i class="fal fa-envelope"></i>
                  <span class="label label-success">4</span>
                </a>
                <ul class="dropdown-menu">
                  <li class="header">You have 4 messages</li>
                  <li>
                    <!-- inner menu: contains the actual data -->
                    <ul class="menu">
                      <li><!-- start message -->
                        <a href="#">
                          <div class="pull-left">
                            <img src="  " class="img-circle" alt="User Image"/>
                          </div>
                          <h4>
                            Support Team
                            <small><i class="fal fa-clock"></i> 5 mins</small>
                          </h4>
                          <p>Why not buy a new awesome theme?</p>
                        </a>
                      </li><!-- end message -->
                      <li>
                        <a href="#">
                          <div class="pull-left">
                            <img src="dist/img/user3-128x128.jpg" class="img-circle" alt="user image"/>
                          </div>
                          <h4>
                            AdminLTE Design Team
                            <small><i class="fal fa-clock"></i> 2 hours</small>
                          </h4>
                          <p>Why not buy a new awesome theme?</p>
                        </a>
                      </li>
                      <li>
                        <a href="#">
                          <div class="pull-left">
                            <img src="dist/img/user4-128x128.jpg" class="img-circle" alt="user image"/>
                          </div>
                          <h4>
                            Developers
                            <small><i class="fal fa-clock"></i> Today</small>
                          </h4>
                          <p>Why not buy a new awesome theme?</p>
                        </a>
                      </li>
                      <li>
                        <a href="#">
                          <div class="pull-left">
                            <img src="dist/img/user3-128x128.jpg" class="img-circle" alt="user image"/>
                          </div>
                          <h4>
                            Sales Department
                            <small><i class="fal fa-clock"></i> Yesterday</small>
                          </h4>
                          <p>Why not buy a new awesome theme?</p>
                        </a>
                      </li>
                      <li>
                        <a href="#">
                          <div class="pull-left">
                            <img src="dist/img/user4-128x128.jpg" class="img-circle" alt="user image"/>
                          </div>
                          <h4>
                            Reviewers
                            <small><i class="fal fa-clock"></i> 2 days</small>
                          </h4>
                          <p>Why not buy a new awesome theme?</p>
                        </a>
                      </li>
                    </ul>
                  </li>
                  <li class="footer"><a href="#">See All Messages</a></li>
                </ul>
              </li>
              <?php if(check_user_previlege('p115')): ?>
              <!-- Notifications: style can be found in dropdown.less -->
              <li class="dropdown notifications-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" id="get_event_notification">
                  <i class="fal fa-bell"></i>
                  <span class="label label-warning" id="active_notifications_count"></span>
                </a>
                <ul class="dropdown-menu">
                  <li>
                  <?php 
                  $notification_loading_image  = '<div class="text-center loader-image"><img src="'.$GLOBALS['CI']->template->template_images('loader_v3.gif').'" alt="Loading........"/></div>';
                  ?>
                    <!-- inner menu: contains the actual data -->
                    <ul class="menu" id="notification_dropdown"><?=$notification_loading_image?></ul>
                  </li>
                  <li class="footer hide" id="view_all_notification"><a href="<?=base_url()?>index.php/utilities/notification_list">View more</a></li>
                </ul>
              </li>
            <?php endif; ?>
              <!-- Tasks: style can be found in dropdown.less -->
              <li class="dropdown tasks-menu ">
                <a href="#" class="dropdown-toggle hide" data-toggle="dropdown">
                  <i class="fal fa-flag"></i>
                  <span class="label label-danger">9</span>
                </a>
                <ul class="dropdown-menu">
                  <li class="header">You have 9 tasks</li>
                  <li>
                    <!-- inner menu: contains the actual data -->
                    <ul class="menu">
                      <li><!-- Task item -->
                        <a href="#">
                          <h3>
                            Design some buttons
                            <small class="pull-right">20%</small>
                          </h3>
                          <div class="progress xs">
                            <div class="progress-bar progress-bar-aqua" style="width: 20%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                              <span class="sr-only">20% Complete</span>
                            </div>
                          </div>
                        </a>
                      </li><!-- end task item -->
                      <li><!-- Task item -->
                        <a href="#">
                          <h3>
                            Create a nice theme
                            <small class="pull-right">40%</small>
                          </h3>
                          <div class="progress xs">
                            <div class="progress-bar progress-bar-green" style="width: 40%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                              <span class="sr-only">40% Complete</span>
                            </div>
                          </div>
                        </a>
                      </li><!-- end task item -->
                      <li><!-- Task item -->
                        <a href="#">
                          <h3>
                            Some task I need to do
                            <small class="pull-right">60%</small>
                          </h3>
                          <div class="progress xs">
                            <div class="progress-bar progress-bar-red" style="width: 60%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                              <span class="sr-only">60% Complete</span>
                            </div>
                          </div>
                        </a>
                      </li><!-- end task item -->
                      <li><!-- Task item -->
                        <a href="#">
                          <h3>
                            Make beautiful transitions
                            <small class="pull-right">80%</small>
                          </h3>
                          <div class="progress xs">
                            <div class="progress-bar progress-bar-yellow" style="width: 80%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                              <span class="sr-only">80% Complete</span>
                            </div>
                          </div>
                        </a>
                      </li><!-- end task item -->
                    </ul>
                  </li>
                  <li class="footer">
                    <a href="#">View all tasks</a>
                  </li>
                </ul>
              </li>
              <!-- User Account: style can be found in dropdown.less -->
              <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <img src="<?=$GLOBALS['CI']->template->domain_images($GLOBALS['CI']->entity_image)?>" class="user-image" alt="User Image"/>
                  <span class="hidden-xs"><?php echo $GLOBALS['CI']->entity_name?></span>
                </a>
                <ul class="dropdown-menu">
                  <!-- User image -->
                  <li class="user-header">
                    <img src="<?=$GLOBALS['CI']->template->domain_images($GLOBALS['CI']->template->get_domain_logo())?>" class="img-circle" alt="User Image" />
                    <p>
                      <?=$GLOBALS['CI']->entity_name.' - '.app_friendly_absolute_date($GLOBALS['CI']->entity_date_of_birth)?>
                      <small>Active since <?=app_friendly_absolute_date($GLOBALS['CI']->entity_creation)?></small>
                    </p>
                  </li>
                  <!-- Menu Body -->
                  <li class="user-body hide">
                    <div class="col-xs-4 text-center">
                      <a href="#">Followers</a>
                    </div>
                    <div class="col-xs-4 text-center">
                      <a href="#">Sales</a>
                    </div>
                    <div class="col-xs-4 text-center">
                      <a href="#">Friends</a>
                    </div>
                  </li>
                  <!-- Menu Footer-->
                  <li class="user-footer">
                    <div class="pull-left">
                      <a href="<?php echo base_url().'index.php/user/account?uid='.intval($GLOBALS['CI']->entity_user_id); ?>" class="btn btn-default btn-flat">Profile</a>
                    </div>
                     <div class="pull-left">
                      <a href="<?php echo base_url().'user/change_password?uid='.intval($GLOBALS['CI']->entity_user_id);?>" class="btn btn-default btn-flat">Change Password</a>
                    </div>
                    <div class="pull-right">
                      <a href="<?php echo base_url().'index.php/general/initilize_logout'?>" class="btn btn-default btn-flat">Sign out</a>
                    </div>
                  </li>
                </ul>
              </li>
              <!-- Control Sidebar Toggle Button -->
              <li class="hide">
                <a href="#" data-toggle="control-sidebar"><i class="fal fa-gears"></i></a>
              </li>
            </ul>
          </div>

        </nav>
      </header>
      <!-- HEADER ends -->

      <!-- MENU starts -->
      <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
          <!-- Sidebar user panel -->
          <div class="user-panel">
            <div class="pull-left image">
            <?php //debug($GLOBALS['CI']);exit;?>
              <img src="<?php echo $GLOBALS['CI']->template->domain_images($GLOBALS['CI']->entity_image);?>" class="img-circle" alt="User Profile Image" />
            </div>
            <div class="pull-left info">
              <p><?php echo $GLOBALS['CI']->entity_name;?></p>
              <a href="#">Admin Console</a>
            </div>
          </div>
          <!-- sidebar menu: : style can be found in sidebar.less -->
          <?php include_once 'menu/magical_menu.php';?>
        </section>
        <!-- /.sidebar -->
      </aside>
      <!-- MENU ends -->
  
      <!-- BODY CONTENT starts -->
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <!--<section class="content-header">
          <h1>
            <a href="<?php echo base_url()?>">Dashboard - <?php echo date('Y')?></a>
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fal fa-dashboard"></i> Need Dynamic Links to be Activated</a></li>
            <li class="active">breadcrums</li>
          </ol>
        </section>
        --><!-- Main content -->
        <section class="content">
        <!-- UTILITY NAV -->
    <div class="container-fluid utility-nav clearfix">
      <!-- ROW -->
      <?php 
        if($this->session->flashdata('message')!="") {
          $message = $this->session->flashdata('message');
          $msg_type = $this->session->flashdata('type');
          $_override_app_msg = $this->session->flashdata('override_app_msg');
          if(empty($_override_app_msg) == false) {
            $override_app_msg = true;
          } else {
            $override_app_msg = false;
          }
        
          $toastr_msg = extract_message($message, $override_app_msg);
          $toastr = get_toastr_index($msg_type);
          ?>
          <script>
            toastr.<?=$toastr;?>("<?=$toastr_msg?>");
          </script>
        <?php
        }
      ?>
      <!-- /ROW -->
    </div>
          <!-- Info boxes -->
          <div class="">
            <?php echo $body ?>
          </div><!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->
      <!-- BODY CONTENT ends -->

      <!-- FOOTER starts -->
      <div class="clearfix"></div>
      <footer class="main-footer">
        <div class="pull-right hidden-xs">
          <b>Version</b> 2.0
        </div>
        <strong>Copyright &copy; <?php echo date('Y')?> <a target="_blank" href="<?= HEADER_DOMAIN_WEBSITE ?>"><?=HEADER_DOMAIN_NAME?> </strong></a> All rights reserved.
      </footer>
      <!-- FOOTER ends -->
      <?php include_once 'menu/support_privilege_helper.php';
      
    //END IF - PAGE After LOGIN
    } else {
      //Page without LOGIN
      echo $body;
    }
    ?>
    </div><!-- ./wrapper -->
  <?php
  Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/datepicker.js'), 'defer' => 'defer');
  Provab_Page_Loader::load_core_resource_files();
  echo $GLOBALS ['CI']->current_page->js ();
  ?>
  <script src='<?php echo SYSTEM_RESOURCE_LIBRARY; ?>/fastclick/fastclick.min.js'></script>
    <!-- Sparkline -->
    <script src="<?php echo SYSTEM_RESOURCE_LIBRARY?>/sparkline/jquery.sparkline.min.js" type="text/javascript"></script>
    <!-- SlimScroll 1.3.0 -->
    <script src="<?php echo SYSTEM_RESOURCE_LIBRARY;?>/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    
    <script>
      $(document).ready(function(){
			$(document).on('click','.close',function(){
				$("#show_low_balance").removeClass('in');
			});
          //Get Admin balance 
          /*$(".show-bal-btn").click(function(){          
           
              var bal_url = app_base_url + 'index.php/management/get_travelomatix_balance';           
              var div_length = $("#show-balance").children().length;
              //show loader image
              $("#show-balance").html("");
              if($(this).children("i").hasClass("fa-angle-up")){                
                  $(this).children("i").removeClass("fa-angle-up");
                  $(this).children("i").addClass("fa-angle-down");
                  $("#img-load").removeClass("hidden");
                  $("#img-load").addClass("visible");
                  $.get(bal_url, function(response) {  
                    //hide loader image
                    $("#img-load").removeClass("visible");
                    $("#img-load").addClass("hidden");
                    var html= '';
                  html +="<p><span>Bal</span> : <strong> <span>"+response.face_value+"</span></strong> \n\ <span>CL</span> : <strong> <span>"+response.credit_limit+"</span></strong> \n\ <span>Due</span>: <strong> <span>"+response.due_amount+"</span></strong></p>";                    
                    $("#show-balance").html(html); 
                },"json"); 

              }else if($(this).children("i").hasClass("fa-angle-down")){
                  $(this).children("i").removeClass("fa-angle-down");
                  $(this).children("i").addClass("fa-angle-up");
              }
            
          });*/
			$(".show-low-bal-btn").click(function(){  
				var bal_url = app_base_url + 'index.php/management/get_low_balance';           
				var div_length = $("#show-balance").children().length;
				//show loader image
				$("#show-balance").html("");
				if($(this).children("i").hasClass("fa-angle-up")){                
					$(this).children("i").removeClass("fa-angle-up");
					$(this).children("i").addClass("fa-angle-down");
					$("#img-load").removeClass("hidden");
					$("#img-load").addClass("visible");
					$.get(bal_url, function(response) {  
						//hide loader image
						console.log(response);
						$("#img-load").removeClass("visible");
						$("#img-load").addClass("hidden");
						var html= response;                    
						$("#low_bal_text").html(response); 
					}); 

				}else if($(this).children("i").hasClass("fa-angle-down")){
					$(this).children("i").removeClass("fa-angle-down");
					$(this).children("i").addClass("fa-angle-up");
				}
			});

        //highlight current menu
  		var loc = window.location.toString();
  		var menu_wrap = $('#magical-menu');
  		var menu_item = $("a[href='"+loc+"']", menu_wrap);
  		//console.log(menu_item);
  		if (menu_item.length > 0) {
  			menu_item.addClass('bg-green');
  			var menu_parent = $(menu_item.closest('li'), menu_wrap);
  			menu_parent.addClass('active text-success');
  			
  			var parent_ul = $(menu_parent.closest('ul'), menu_wrap);
  			parent_ul.trigger('click');
  			var traverse_tree = true;
  			while (traverse_tree) {
  				parent_li = $(parent_ul).closest('li');
  				parent_li.addClass('active');
  				//console.log(parent_li);
  				parent_ul = $(parent_li).closest('ul');
  				parent_ul.addClass('menu-open');
  				//console.log(parent_ul);
  				if (parent_li.length == 0 || parent_ul.length == 0) {
  					traverse_tree = false;
  					//parent_ul.trigger('click');
  				}
  			}
  		}
      });
        function menu_search(url){
          window.location.href = url;
        }
        $(document).ready(function() {
          if(load_select2)
            $("select").select2();
          $("#custom_search").select2();
        });
    </script>
    </body>
</html>

