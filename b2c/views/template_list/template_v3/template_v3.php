<?php
$___favicon_ico = $GLOBALS ['CI']->template->domain_images('favicon/favicon.ico');
//lib
//mod
//pg
$active_domain_modules = $GLOBALS ['CI']->active_domain_modules;
$master_module_list = $GLOBALS ['CI']->config->item('master_module_list');
if (empty($default_view)) {
    $default_view = $GLOBALS ['CI']->uri->segment(1);
}
//echo md5('admin@123');
//
// $booking_request = "{\"search_id\":\"424dts4x3ffdhsbwjqp7p76rci\",\"hotel_code\":\"H!0335063\",\"city_code\":\"C!000924\",\"group_code\":\"uwgal4tfxurcpu2x2giw2gvj5e\",\"checkin\":\"2018-07-14\",\"checkout\":\"2018-07-17\",\"booking_comments\":\"Testing\",\"payment_type\":\"AT_WEB\",\"booking_items\":[{\"room_code\":\"4dgffkrq4qrcplzuyy\",\"rate_key\":\"t6ytnqkatnnf7uc3yd6bkf64sgkp5ghuult3db3s2fcbf5xnnxnhldpq\",\"rooms\":[{\"paxes\":[{\"title\":\"Mr.\",\"name\":\"OMAR\",\"surname\":\"ALSAMMARRAIE\",\"type\":\"AD\"},{\"title\":\"Mr.\",\"name\":\"ABDULLAH\",\"surname\":\"MUSTAFA\",\"type\":\"AD\"}],\"room_reference\":\"qwusnpr4rm\"}]}],\"holder\":{\"title\":\"Mr.\",\"name\":\"OMAR\",\"surname\":\"ALSAMMARRAIE\",\"email\":\"banias.iq@gmail.com\",\"phone_number\":\"7811818379\",\"client_nationality\":\"TR\"}}";
// $arr = json_decode($booking_request,true);
// echo json_encode($arr);
// exit;

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- <meta name="viewport" content="width=device-width, initial-scale=1" /> -->
        <meta id="id" name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
        <meta name="keywords" content="<?= META_KEYWORDS ?>">
        <meta name="description" content="<?= META_DESCRIPTION ?>">
        <meta name="author" content="travelomatix">
        <link rel="shortcut icon" href="<?= $___favicon_ico ?>" type="image/x-icon">
        <link rel="icon" href="<?= $___favicon_ico ?>" type="image/x-icon">
        <title><?php echo HEADER_TITLE_SUFFIX; ?></title>
        <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,700&display=swap" rel="stylesheet">
        <?php
        // Loading Common CSS and JS
        $GLOBALS ['CI']->current_page->header_css_resource();
        Js_Loader::$css[] = array('href' => $GLOBALS['CI']->template->template_css_dir('front_end.css'), 'media' => 'screen');
        $GLOBALS ['CI']->current_page->header_js_resource();
        echo $GLOBALS ['CI']->current_page->css();
         Js_Loader::$css[] = array('href' => $GLOBALS['CI']->template->template_css_dir('bootstrap-select.css'), 'media' => 'screen');
        ?>

        

        <!-- Custom CSS -->
        <link href="<?php echo $GLOBALS['CI']->template->template_css_dir('media.css'); ?>" rel="stylesheet" />
        <script>
            var app_base_url = "<?= base_url() ?>";
            var tmpl_img_url = '<?= $GLOBALS['CI']->template->template_images(); ?>';
<?php if (!empty($slideImageJson)) { ?>
                var slideImageJson = '<?php echo base64_encode(json_encode($slideImageJson)); ?>';
                //alert(slideImageJson);
                var tmpl_imgs = JSON.parse(atob(slideImageJson));
<?php } ?>

            var _lazy_content;
        </script>
    </head>
    <body class="<?php echo (isset($body_class) == false ? 'index_page' : $body_class) ?>">

        <div id="show_log" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <!-- <div class="modal-header">
                    
                    </div> -->
                    <div class="modal-body">
                        <?= $GLOBALS['CI']->template->isolated_view('general/login') ?>
                    </div>
                </div>

            </div>
        </div>
        <!-- Timer -->
        <!--  <div class="cartsec1">
                 <span class="postime"><span id="display">00:00</span></span>
         </div> -->
        <!-- Timer -->
        <div class="allpagewrp">
            <!-- Header Start -->
            <header>
                <!-- <div class="topbar">
                    <div class="pull-left">
                        <div class="contact_num">
                            <p>Call Us:0123456789</p>
                        </div>
                    </div>
                     <div class="pull-right">
                        <div class="login_ico">
                            <ul class="list-inline">
                                <li><a href="#"><i class="fas fa-user-circle"></i>Login</a></li>
                                 <li class="dropdown">
                                    <a href="#" class="dropdown-toggle"  data-toggle="dropdown"><i class="far fa-usd-circle"></i>USD <i class="fas fa-caret-down"></i></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="#">Rupee</a></li>
                                        <li><a href="#">Yen</a></li>
                                    </ul>
                                </li>

                            </ul>
                        </div>
                    </div>
                </div> -->
                <div class="section_top sec_tp">
                    <div class="container">
                        <div class="topalstn">
                            <div class="socila hidesocial socilb">
                                <span>Call Us : 0123456789</span>
                                <!--  <?php
                                //echo $this->CI->session('phone');
                                $temp = $this->custom_db->single_table_records('social_links','*',array('status' => 1));
                                if($temp['status'] == ACTIVE){
                                    foreach($temp['data'] as $social_key => $social_links){ 
                                        if($social_links['social'] == 'facebook'){
                                            $icon = 'fa fa-facebook';
                                        }
                                        else if($social_links['social'] == 'google-plus'){
                                            $icon = 'fa fa-google-plus';
                                        }
                                        else if($social_links['social'] == 'twitter'){
                                            $icon = 'fa fa-twitter';
                                        }
                                        else if($social_links['social'] == 'youtube'){
                                            $icon = 'fa fa-youtube';
                                        }
                                    ?> 

                                    <a href="<?php echo $temp['data']['0']['url_link']; ?>"><i class="<?php echo $icon; ?>"></i></a>
                                   <?php  }
                                } ?> -->

                                
                            </div>
                            <div class="ritsude">
                               <!--   <ul class="list-inline">
                                                    <li><a href="#">holidays </a></li>
                                                     <li><a href="#">Deals & offers</a></li>
                                                      <li><a href="#">support</a></li>
                                                       <li><a href="#">contact</a></li>
                                               </ul>
 -->                                <div class="sidebtn">
                                    <?php if (is_logged_in_user() == false) { ?>
                                        <a class="topa logindown top_a" data-toggle="modal" data-target="#show_log">
                                            <div class="reglog">
                                             <!--    <ul class="list-inline">
                                                    <li><a href="#">holidays </a></li>
                                                     <li><a href="#">Deals & offers</a></li>
                                                      <li><a href="#">support</a></li>
                                                       <li><a href="#">contact</a></li>
                                               </ul> -->
                                                <div class="userimage usrimg">
                                                    <?php
                                                    if (is_logged_in_user() == true && empty($GLOBALS['CI']->entity_image) == false) {
                                                        $profile_image = $GLOBALS['CI']->template->domain_images($GLOBALS['CI']->entity_image);
                                                    } else {
                                                        $profile_image = $GLOBALS['CI']->template->template_images('user.png');
                                                    }
                                                    ?>
                                                    <img src="<?php echo $profile_image; ?>" alt="" />
                                                </div>

                                                <div class="userorlogin nwlogin">login</div>



                                            </div> 
                                         </a>
                                    <?php } else { ?>

                                        <a class="topa logindown dropdown-toggle" data-toggle="dropdown">
                                            <div class="reglog">
                                                <div class="userimage">
                                                    <?php
                                                    if (is_logged_in_user() == true && empty($GLOBALS['CI']->entity_image) == false) {
                                                        $profile_image = $GLOBALS['CI']->template->domain_images($GLOBALS['CI']->entity_image);
                                                    } else {
                                                        $profile_image = $GLOBALS['CI']->template->template_images('user.png');
                                                    }
                                                    ?>
                                                    <img src="<?php echo $profile_image; ?>" alt="" />
                                                </div>
                                                <?php if (is_logged_in_user() == false) { ?>
                                                    <div class="userorlogin">My Account</div>
                                                <?php } else { ?>
                                                    <div class="userorlogin"><?php echo $GLOBALS['CI']->entity_name ?><b class="caret cartdown"></b>  
                                                    </div>

                                                <?php } ?>

                                            </div>
                                        </a> 

                                     <div class="dropdown-menu mysign exploreul logdowndiv">
                                            <div class="signdiv">
                                                <div class="clearfix">
                                                    <ul>
                                                        <li><a
                                                                href="<?= base_url() ?>index.php/user/profile/<?= @$GLOBALS['CI']->name ?>">My
                                                                Account</a>
                                                        </li>
                                                        <li class="divider"></li>
                                                        <li><a href="<?= base_url() . 'index.php/auth/change_password' ?>">Change
                                                                Password</a>
                                                        </li>
                                                        <li class="divider"></li>
                                                        <li><a class="user_logout_button">Logout</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div> 
                               <?php } ?> 

                                 </div>

  <div class="sidebtn flagss">
       <a class="topa dropdown-toggle top_a" data-toggle="dropdown">
                                        <div class="reglognorml">
                                            <div class="flag_images">
                                                <?php
                                                $curr = get_application_currency_preference();

                                                echo '<span id="disply_curr" class="disply_curr curncy_img sprte ' . strtolower($curr) . '"></span>'
                                                ?>
                                            </div>
                                            <div class="flags nw_flags">
                                                <?php
                                                echo $curr;
                                                ?>
                                            </div>
                                            <b class="caret cartdown"></b>
                                        </div>
                                    </a>
                                    <ul class="dropdown-menu exploreul explorecntry logdowndiv">
                                        <?= $this->template->isolated_view('utilities/multi_currency') ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="toprit">

                                <!--  <div class="login_ico">
                            <ul class="list-inline">
                                <li><a href="#"><i class="fas fa-user-circle"></i>Login</a></li>
                                 <li class="dropdown">
                                    <a href="#" class="dropdown-toggle"  data-toggle="dropdown"><i class="far fa-usd-circle"></i>USD <i class="fas fa-caret-down"></i></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="#">Rupee</a></li>
                                        <li><a href="#">Yen</a></li>
                                    </ul>
                                </li>
  </ul>
                        </div> -->
                                <!-- <div class="sectns">
                                    <a class="phnumr" href="tel:<?= $this->entity_domain_phone ?>">
                                        <span class="sprte indnum samestl"></span>
                                        <span class="numhide"><?= $this->entity_domain_phone ?></span>
                                        <div class="fa cliktocl fa-phone"></div>
                                    </a>
                                </div>
                                <div class="sectns">
                                    <a class="mailadrs" href="mailto:<?= $this->entity_domain_mail ?>">
                                        <span class="fa fa-paper-plane"></span>
                                        <?= $this->entity_domain_mail ?>
                                    </a>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="topssec secnav">
                    <div class="container">
                        <div class="bars_menu fa fa-bars menu_brgr"></div>
                      
                        <div class="menuandall">
                            <div class="menu" >
                                <ul class="exploreall">

                                    <?php
                                        foreach ($master_module_list as $k => $v) { 
                                        if (in_array($k, $active_domain_modules)) {
                                    ?>
                                        <li
                                            class="<?= ((@$default_view == $k || $default_view == $v) ? 'active' : '') ?>"><a
                                                href="<?php echo base_url() ?>index.php/general/index/<?php echo ($v) ?>?default_view=<?php echo $k ?>">
                                                <span
                                                    class="sprte cmnexplor <?= module_spirit_img(strtolower($v)) ?>"></span>
                                                <strong><?php echo ucfirst($v); ?></strong>
                                            </a>
                                        </li>
                                    <?php }} ?>

                                    <!-- <li class="active"><a href="#">flights </a></li>
                                    <li><a href="#">hotels</a></li>
                                    <li><a href="#">bus</a></li>
                                    <li><a href="#">transfers</a></li>
                                    <li><a href="#">Activities </a></li>
                                    <li><a href="#">holidays </a></li>
                                    <li><a href="#">Deals & offers</a></li>
                                    <li><a href="#">support</a></li> 
                                    <li><a href="#">contact</a></li> -->
                                </ul>
                            </div>
                   
                            <div class="sepmenus leftnav">
                                <!-- <ul class="exploreall">
                                    <li class="active"><a href="#">flights12 </a></li>
                                    <li><a href="#">hotels</a></li>
                                    <li><a href="#">bus</a></li>
                                    <li><a href="#">transfers</a></li>
                                    <li><a href="#">Activities </a></li>       
                                </ul> -->
                                <ul class="exploreall">
                                    <?php
                                    //debug($master_module_list);exit;
                                    foreach ($master_module_list as $k => $v) {
                                        if (in_array($k, $active_domain_modules)) {
                                            ?>
                                            <li
                                                class="<?= ((@$default_view == $k || $default_view == $v) ? 'active' : '') ?>"><a
                                                    href="<?php echo base_url() ?>index.php/general/index/<?php echo ($v) ?>?default_view=<?php echo $k ?>">
                                                   
                                                    <?php echo ucfirst($v); ?>
                                                </a>
                                            </li>
                                            <?php
                                        }
                                    }
                                    ?>
                                </ul>
                            </div>
                            <a class="logo log_bg" href="<?= base_url() ?>">
                                <img class="tab_logo" src="<?php echo $GLOBALS['CI']->template->domain_images('mobile_logo.png'); ?>" alt="Logo" /> 
                                <img class="ful_logo logo_img" src="<?php echo $GLOBALS['CI']->template->domain_images($GLOBALS['CI']->template->get_domain_logo()); ?>" alt="" />
                            </a>
                            <div class="ritsude rgtnav">
                                <ul class="exploreall">
                                    <!-- <li><a href="#">holidays </a></li> -->
                                    <li><a href="#">Deals & offers</a></li>
                                    <li><a href="#">support</a></li> 
                                    <li><a href="">contact us</a></li>
                                    <li><a href="#">about us</a></li>
                                </ul>                      
                                <!--  <div class="sidebtn">                                                                            <a class="topa logindown top_a" data-toggle="modal" data-target="#show_log">                                            <div class="reglog">                                                <ul class="list-inline">                                                    <li><a href="#">holidays </a></li>                                                     <li><a href="#">Deals & offers</a></li>                                                      <li><a href="#">support</a></li>                                                       <li><a href="#">contact</a></li>                                                    
                                                </ul>                                                <div class="userimage usrimg">                                                                                                        <img src="/pace_travel/extras/system/template_list/template_v3/images/user.png" alt="">                                                </div>                                                <div class="userorlogin nwlogin">login</div>                                            </div> 
                                         </a>                                     

                                 </div>    -->
                            </div>
                        </div>
                    </div>
                </div>
             
            </header>
            <!-- Header End -->
            <div class="clearfix"></div>
            <!-- UTILITY NAV For Application MESSAGES START -->
            <div class="container-fluid utility-nav clearfix">
                <!-- ROW --> <?php
                if ($this->session->flashdata('message') != "") {
                    $message = $this->session->flashdata('message');
                    $msg_type = $this->session->flashdata('type');
                    $show_btn = TRUE;
                    if ($this->session->flashdata('override_app_msg') != "") {
                        $override_app_msg = $this->session->flashdata('override_app_msg');
                    } else {
                        $override_app_msg = FALSE;
                    }

                    echo get_message($message, $msg_type, $show_btn, $override_app_msg);
                }
                ?> <!-- /ROW -->
            </div>
            <!-- UTILITY NAV For Application MESSAGES END -->
            <!-- Body Printed Here -->
            <div class="fromtopmargin">
                <?= $body ?>
            </div>
            <div class="clearfix"></div>
            <!-- Footer Start -->
            <footer>
                <div class="container-fluid">
                 <div class="col-md-12 sign_bg">
                                    <div class="frtbest1 centertio">
                                         <div class="col-md-3 col-xs-12 soc_ico_div nopad">
                                            <div class="soc_ico">
                                         <i class="fab fa-facebook-f"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-xs-12 soc_ico_div nopad">
                                              <div class="soc_ico">
                                      <i class="fab fa-twitter"></i>
                                  </div>
                                        </div>
                                        <div class="col-md-3 col-xs-12 soc_ico_div nopad">
                                              <div class="soc_ico">
                                       <i class="fab fa-google-plus-g"></i>
                                   </div>
                                        </div>
                                          <div class="col-md-3 col-xs-12 soc_ico_div nopad">
                                              <div class="soc_ico">
                                       <i class="fab fa-instagram"></i>
                                   </div>
                                        </div>

                                       </div>

                                 
                                </div>
                                </div>
                <div class="fstfooter">
                 <div class="reftr">
                        <div class="container">
                      <div class="col-md-12 fulnine color_bg">
                        <div class="org_row">
                                <div class="col-sm-6 col-xs-12 nopad">
                                    <div class="frtbest">
                                        <ul id="accordionfot" class="accordionftr">
                                            <h4 class="ftrhd arimo ">Contact Us</h4>
                                            <?php  $cond = array(
                                                    'page_status' => ACTIVE
                                                );
                                                $cms_data = $this->custom_db->single_table_records('cms_pages', '', $cond);
                                                print_r($cms_data ['data'][0]);?>
                                           <!--  <p><?php echo @$domain_data['data'][0]['address']; ?></p>
                                            <br>
                                            <p><?php echo @$domain_data['data'][0]['email'] ; ?></p>
                                            <br>
                                            <p><?php echo @$domain_data['data'][0]['phone_code'].@$domain_data['data'][0]['phone']  ; ?></p>
                                            <p><?php echo @$domain_data['data'][0]['domain_webiste']  ; ?></p> -->
                                     </ul>
                                    </div>
                                </div>
                                <div class="col-sm-3 col-xs-12 nopad">
                                    <div class="frtbest">
                                        <ul id="accordionfot1" class="accordionftr">
                                            <h4 class="ftrhd arimo ">Explore Our World</h4>
                                            <ul class="submenuftr1">
                                              <?php
                                                $cond = array(
                                                    'page_status' => ACTIVE
                                                );
                                                $cms_data = $this->custom_db->single_table_records('cms_pages', '', $cond);
                                                //debug($cms_data);exit;
                                                foreach ($cms_data ['data'] as $keys => $values) {
                                                    if ($keys >= 4) {
                                                        break;
                                                    }
                                                    //echo '<li class="frteli"><a href="' . base_url () . 'index.php/general/cms/Bottom/' . $values ['page_id'] . '">' . $values ['page_title'] . ' <br> </a></li>';
                                                    echo '<li class="frteli"><a href="' . base_url() . $values ['page_label'] . '">' . $values ['page_title'] . ' <br> </a></li>';
                                                }
                                                ?>
                                                
                                            </ul>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-sm-3 col-xs-12 nopad">
                                    <div class="frtbest">
                                        <ul id="accordionfot2" class="accordionftr">
                                            <h4 class="ftrhd arimo ">Legal</h4>
                                            <ul class="submenuftr1">
                                              <?php
                                                foreach ($cms_data ['data'] as $keys => $values) {
                                                    if ($keys >= 4) {
                                                        //echo '<li class="frteli"><a href="' . base_url () . 'index.php/general/cms/Bottom/' . $values ['page_id'] . '">' . $values ['page_title'] . ' <br> </a></li>';
                                                        echo '<li class="frteli"><a href="' . base_url() . $values ['page_label'] . '">' . $values ['page_title'] . ' <br> </a></li>';
                                                    }
                                                }
                                                ?>
                                                    
                                            </ul>
                                        </ul>
                                    </div>
                                
                            </div>

                            </div>

                        <!--     <div class="footer-top__back-to-top">
                                <a class="footer-top__back-to-top-link  js-back-to-top" href="#">Back to top 
                                    <i class="fa fa-long-arrow-right" aria-hidden="true"></i>
                                </a>
                            </div> -->


                        </div>
                    </div>
                    </div>
                    <!-- <button type="button" class="btn btn-warning btn-xs pull-right col-md-3" data-toggle="modal" data-target="#myModal">Offline Payment</button> -->
                </div>
                <div class="clearfix"></div>


                <div id="myModal" class="modal fade" role="dialog">
                    <div class="modal-dialog">

                        <!-- Modal content-->
                        <form action="<?= base_url() ?>/index.php/general/offline_payment" method="post" name="offline" id="offline_form">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">Offline Payment</h4>
                                </div>
                                <center><span class="text-success offline-msg"></span></center>

                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="usr">Company Name:</label>
                                        <input type="text" class="form-control" id="company_name" name="company_name">
                                    </div>
                                    <div class="form-group">
                                        <label for="usr">Customer Name:</label>
                                        <input type="text" class="form-control" id="name" name="name">
                                    </div>
                                    <div class="form-group">
                                        <label for="usr">Customer Email:</label>
                                        <input type="text" class="form-control" id="customer_email" name="email">
                                    </div>
                                    <div class="form-group">
                                        <label for="usr">Customer Contact No:</label>
                                        <input type="text" class="form-control" id="phone" name="phone">
                                    </div>
                                    <div class="form-group">
                                        <label for="usr">Amount:</label>
                                        <input type="text" class="form-control" id="amount" name="amount">
                                    </div>
                                    <div class="form-group">
                                        <label for="usr">Remarks:</label>
                                        <textarea class="form-control" rows="5" id="remarks" name="remarks"></textarea>
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-success btn-offline-pay" >Submit</button>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </form> 
                    </div>
                </div>



                <div class="copyrit">
                           Copyright &copy; <?php echo date('Y') ?><a target="_balnk" href="#"> <?= HEADER_DOMAIN_NAME ?></a> All rights reserved.
                </div>
            </footer>
            <!-- Footer End -->
        </div>
        <?php
        // Dynamic Loading of all the files needed in the application
        Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/datepicker.js'), 'defer' => 'defer');
        Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('bootstrap-select.min.js'), 'defer' => 'defer');
        Provab_Page_Loader::load_core_resource_files();
        $GLOBALS ['CI']->current_page->footer_js_resource();
        echo $GLOBALS ['CI']->current_page->js();
        ?>
        <script src="<?php echo $GLOBALS['CI']->template->template_js_dir('modernizr.custom.js'); ?>" defer></script>

  <script type="text/javascript">
          $(document).ready(function(){
            $("#id").attr("content", "width=device-width, initial-scale=1")
        })
                                                var accessToken = "8484e898405d4becb83c0091285f68a2";
                                                var baseUrl = "https://api.api.ai/v1/";

                                                $(document).ready(function () {
                                                    $("#from_city").keypress(function (event) {
                                                        if (event.which == 13) {
                                                            event.preventDefault();
                                                            send();
                                                        }
                                                    });
                                                    $("#rec").click(function (event) {
                                                        switchRecognition();
                                                        // setInput();
                                                    });
                                                });

                                                var recognition;

                                                function startRecognition() {
                                                    recognition = new webkitSpeechRecognition();
                                                    recognition.onstart = function (event) {
                                                        updateRec();
                                                    };
                                                    recognition.onresult = function (event) {
                                                        var text = "";
                                                        for (var i = event.resultIndex; i < event.results.length; ++i) {
                                                            text += event.results[i][0].transcript;
                                                        }
                                                        setInput(text);
                                                        stopRecognition();
                                                    };
                                                    recognition.onend = function () {
                                                        stopRecognition();
                                                    };
                                                    recognition.lang = "en-US";
                                                    recognition.start();
                                                }

                                                function stopRecognition() {
                                                    if (recognition) {
                                                        recognition.stop();
                                                        recognition = null;
                                                    }
                                                    updateRec();
                                                }

                                                function switchRecognition() {
                                                    if (recognition) {
                                                        stopRecognition();
                                                    } else {
                                                        startRecognition();
                                                    }
                                                }

                                                function setInput(text) {
                                                    

                                                    $("#input_speech").val(text);
                                                    
                                                    
                                                    var from_city = text.split(" to");
                                                    if (typeof (from_city[1]) != 'undefined' && from_city[1].indexOf(' on') >= -1) {
                                                        var to_city = from_city[1].split(" on");
                                                    } else {
                                                        var to_city = [from_city[1], ''];
                                                    }
                                                    //alert(to_city);
                                                    if (typeof (to_city[1]) != 'undefined' && to_city[1].indexOf(' for') != -1) {
                                                        var ddate = to_city[1].split(" for");
                                                    } else {
                                                        //console.log(to_city);
                                                        if (typeof (to_city[1]) != 'undefined') {
                                                            var removed_space_date = to_city[1].trim();

                                                            var new_date = removed_space_date.split(" ");

                                                            var ddate = [new_date[1] + ' ' + new_date[0]];

                                                        } else {
                                                            var d = new Date();
                                                            var strDate = (d.getDate() + 1) + "-" + (d.getMonth() + 1);

                                                            var ddate = [strDate, ''];
                                                        }

                                                    }


                                                    if (typeof (ddate[1]) != 'undefined' && ddate[1].indexOf(' adult') != -1) {
                                                        var adult_value = ddate[1].split("adult");
                                                    } else {
                                                        var adult_value = ["1"];
                                                    }


                                                    if (typeof (adult_value[1]) != 'undefined' && adult_value[1].indexOf(' child') != -1) {
                                                        var child_value = adult_value[1].split(" child");
                                                    } else {
                                                        var child_value = ["0"];
                                                    }


                                                    if (typeof (child_value[1]) != 'undefined' && child_value[1].indexOf(' infant') != -1) {
                                                        var infant_value = child_value[1].split(" infant");
                                                    } else {
                                                        var infant_value = ["0"];
                                                    }
                                                    if ($.trim(to_city[0]) != '' && $.trim(from_city[0]) != '') {
                                                        var from_city_value = update_city($.trim(from_city[0]), 'from', 'from_loc_id_val');
                                                        var to_city_value = update_city($.trim(to_city[0]), 'to', 'to_loc_id_val');

                                                        $("#flight_datepicker1").val(ddate[0] + "-2018");
                                                        $("#OWT_adult").val(adult_value[0]);
                                                        $("#OWT_child").val(child_value[0]);
                                                        $("#OWT_infant").val(infant_value[0]);

                                                        
                                                        setTimeout(function () {
                                                            $("#flight-form-submit").click();
                                                        }, 5000);
                                                    } else {
                                                        alert("Please Try agin with proper input data");
                                                    }
                                                }

                                                function updateRec() {
                                                    $("#rec").html(recognition ? "<img style='width: 14px; padding-top: 2px;' src='<?php echo $GLOBALS['CI']->template->template_images('mike_red.png'); ?>'>" : "<img style='width: 14px; padding-top: 2px;' src='<?php echo $GLOBALS['CI']->template->template_images('mike.png'); ?>'>");
                                                }

                                                function update_city(input_data, id, val) {
                                                    var search_data = input_data.replace(" ", "_");

                                                    $.ajax({
                                                        type: "POST",
                                                        url: 'https://localhost/travelomatix/index.php/ajax/get_airport_code_list_for_voice_speach/' + search_data,
                                                        success: function (data) {
                                                            var data_arrange = data.split("|");

                                                            $("#" + id).val($.trim(data_arrange[0]));
                                                            $("#" + val).val($.trim(data_arrange[1]));
                                                        },
                                                    });
                                                }

                                                function send() {
                                                    var text = $("#input").val();
                                                    $.ajax({
                                                        type: "POST",
                                                        url: baseUrl + "query?v=20150910",
                                                        contentType: "application/json; charset=utf-8",
                                                        dataType: "json",
                                                        headers: {
                                                            "Authorization": "Bearer " + accessToken
                                                        },
                                                        data: JSON.stringify({query: text, lang: "en", sessionId: "somerandomthing"}),

                                                        success: function (data) {
                                                            setResponse(JSON.stringify(data, undefined, 2));
                                                        },
                                                        error: function () {
                                                            setResponse("Internal Server Error");
                                                        }
                                                    });
                                                    setResponse("Loading...");
                                                }

                                                function setResponse(val) {
                                                    $("#response").text(val);
                                                }

        </script>
    </body>
</html>


