<?php
if (isset($login) == false || is_object($login) == false) {
    $login = new Provab_Page_Loader('login');
}
$login_auth_loading_image  = '<div class="text-center loader-image"><img src="'.$GLOBALS['CI']->template->template_images('loader_v3.gif').'" alt="please wait"/></div>';
?>
<link href="<?php echo $GLOBALS['CI']->template->template_css_dir('agent_index.css');?>" rel="stylesheet" defer>
<link href="<?php echo $GLOBALS['CI']->template->template_css_dir('bootstrap-toastr/toastr.min.css');?>" rel="stylesheet" defer>
<script src="<?php echo $GLOBALS['CI']->template->template_js_dir('bootstrap-toastr/toastr.min.js'); ?>"></script>
 
 
 <link href="https://fonts.googleapis.com/css?family=Lato|Source+Sans+Pro" rel="stylesheet">
 <link href="<?php echo $GLOBALS['CI']->template->template_css_dir('owl.carousel.min.css');?>" rel="stylesheet" defer>
 <script src="<?php echo $GLOBALS['CI']->template->template_js_dir('owl.carousel.min.js'); ?>"></script>
 

 <div class="topform_main">
 <div class="topform">
 <div class="headagent">
    <div class="container">
        <div class="leftul"> 
            <?php 
            if(!empty($page_content['data'])) { 
                foreach ($page_content['data'] as $k => $v) {
                    if(strtolower(str_replace(' ', '', $v['page_title'])) == 'aboutus'){
            ?>
            <a class="myangr" href="<?php echo base_url () . 'index.php/general/cms/' .$v['page_seo_keyword'] ; ?>" ><?=@$v['page_title']?></a>
            <?php 
                break;
                        } else {
                            continue;
                        }
                    }
                } 
            ?> 
            
        </div>
        <div class="rightsin">
            <a class="myangr" href="<?=base_url().'index.php/user/agentRegister' ?>" >Haven't Registered Yet?</a>
        </div>
    </div>
</div>
 <div class="clearfix"></div>
  <div class="container">
   
    <div class="loginbox">
      <div class="col-sm-5 col-xs-5 nopad">
        <div class="innerfirst">
          <div class="logopart"> <!-- <img src="<?php echo $GLOBALS['CI']->template->domain_images($GLOBALS['CI']->template->get_domain_logo()); ?>" alt=""/> -->
            <img src="https://pacetravels.net/extras/custom/TMX1512291534825461/images/White_logo.png" alt="logo">
          </div>
          <div class="hmembr fr_mobl"> Online  <br/> Reservation <br/>System </div>
          <div class="lorentt fr_mobl">Award winning B2B platform for travel agents and start-up travel companies.</div>
        </div>
      </div>
      <div class="col-sm-7 col-xs-7 nopad">
      <?php 
      $class ='';
      $otp_class = 'hide';
      $OTP_status = $this->session->userdata('OTP_status');
      if(isset($OTP_status) && $OTP_status == 'not verified'){
        $class= 'hide';
        $otp_class = '';
      }
      //echo $this->session->userdata('OTP_status');exit;?>
        <div class="innersecing <?php echo $class; ?>">
          <div class="signhes"><i class="far fa-power-off"></i> Sign in to Continue </div>
          <?php $name = 'login' ?>
          <form name="<?=$name?>" autocomplete="off" action="<?php echo base_url(); ?>index.php/general/index" method="POST" enctype="multipart/form-data" id="login" role="form" class="form-horizontal">
          <?php $FID = $GLOBALS['CI']->encrypt->encode($name); ?>
          <input type="hidden" name="FID" value="<?=$FID?>">
          <div class="inputsing"> <span class="sprite userimg"></span>
            <!-- <input type="text" class="mylogbox" placeholder="Username" /> -->
            <input value="" name="email" dt="PROVAB_SOLID_V80" required="" type="email" placeholder="Username" class="mylogbox login-ip email _guest_validate_field" id="email" data-container="body" data-toggle="popover" data-original-title="" data-placement="bottom" data-trigger="hover focus" data-content="Username Ex: john@bookingsdaily.com">
          </div>
          <div class="inputsing"> <span class="sprite lockimg"></span>
            <!-- <input type="text" class="mylogbox" placeholder="Password" /> -->
            <input value="" name="password" dt="PROVAB_SOLID_V45" required="" type="password" placeholder="Password" class="login-ip password mylogbox _guest_validate_field" id="password" data-container="body" data-toggle="popover" data-original-title="" data-placement="bottom" data-trigger="hover focus" data-content="Password Ex: A3#FD*3377^*">
          </div>
         
          <!-- <button class="logbtn">Login</button> -->
           <button id="login_submit" class="logbtn">Login</button>
            <div id="login_auth_loading_image" style="display: none">
            <?=$login_auth_loading_image?>
          </div>
           <div id="login-status-wrapper" class="alert alert-danger" style="display: none"></div>
          </form>
          <div class="signhes"> Don’t have an account ? <a href="<?=base_url().'index.php/user/agentRegister' ?>">Sign up</a></div>
            <div class="signhes"><?php echo $GLOBALS['CI']->template->isolated_view('general/forgot-password');?></div>
          
        </div>
         <div class="innersecing <?php echo $otp_class; ?>" id="otp_div">
         <a href="#" class="gobacklink">Back</a> 
            <?php $name = 'otp' ?>
          <form name="<?=$name?>" autocomplete="off" action="" method="POST" enctype="multipart/form-data" id="login1" role="form" class="form-horizontal">
            <div class="inputsing">
            <!-- <input type="text" class="mylogbox" placeholder="Password" /> -->
            <input value="" name="opt" required="" type="text" placeholder="Enter OTP" class="login-ip mylogbox _guest_validate_field" id="otp">
          </div>
          <button id="opt_submit" class="logbtn">Login</button>
           <div id="login-otp-wrapper" class="alert alert-danger" style="display: none"></div>
          </form>

         </div>
      </div>
    </div>
  </div>
</div>
</div>

<style type="text/css">
  .invalid-ip {
    border: 1px solid #bf7070!important;
}
.alert-danger{
      background-color: #dd4b39!important;
}
 .logbtn {
    background: #d21819 !important;
    border: none;
  }
  .logopart::before {
    background: linear-gradient(96deg,#002042,#0a8ec1);
  }
  .topform::before {
    filter: blur(5px);
    -webkit-filter: blur(5px);
  }
  .loginbox {
      box-shadow: 0 0 20px rgba(33, 31, 31, 0.8);
      width: 60%;
    }
  .sprite {
    margin-top: 0;
    margin-right: 10px;
  }
  input:-webkit-autofill,
  input:-webkit-autofill:hover, 
  input:-webkit-autofill:focus, 
  input:-webkit-autofill:active  {
      -webkit-box-shadow: 0 0 0 30px white inset !important;
  }
</style>
<script>
$(document).ready(function() {
  $('#opt_submit').on('click', function(e) {
    
    e.preventDefault();
    var _otp = $('#otp').val();
    if (_otp == '') {
      $('#login-otp-wrapper').text('Please Enter Username And Password To Continue!!!').show();
    } else {
     
      $.post(app_base_url+"index.php/auth/check_otp/", {otp: _otp}, function(response) {
      
        if (response.status) {
          window.location.reload();
        } else {
          $('#login-otp-wrapper').text(response.data).show();
        }
       
      });
    }
  });
  $('.gobacklink').on('click', function(e) {
     $.post(app_base_url+"index.php/auth/back_button/", function(response) {
      
        if (response.status) {
          window.location.reload();
        } else {
          $('#login-otp-wrapper').text(response.data).show();
        }
       
      });
    
  });
});
</script>
