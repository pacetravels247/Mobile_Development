<?php

 

if (isset($login) == false || is_object($login) == false) {
	$login = new Provab_Page_Loader('login');
}
?>
<style>
label[for=opt_number]
{
   display:none;
}
</style>
<div class="login-box">
	<figure class="login-logo">
		<img src="<?php echo $GLOBALS['CI']->template->domain_images($GLOBALS['CI']->template->get_domain_logo())?>" alt="logo"	class="img-responsive center-block">
	</figure>
	<div class="login-box-body">
		<p class="login-box-msg"><i class="fa fa-power-off"></i> Sign in to continue</p>
		<?php $sparam = $this->input->cookie ( 'login_cookie', TRUE );  
//		debug($sparam ); exit; 
		if(!empty($sparam)){
			echo $login->generate_form('login', array('email' => '', 'password' => ''),array('opt_number'));
		}else{
			echo $login->generate_form('login', array('email' => '', 'password' => ''));
		 
		}?>
<!--<input type="button" name="send_otp" class="send_otp" value="SEND OTP">-->
	</div>
	<div class="panel-footer">
		<?php include_once 'forgot-password.php';?>
	</div>
</div>

<script type="text/javascript">
$("#login_custom").prop("type", "submit");
$("#login_custom").html("Login");
$(document).on('click', '.send_otps', function(){
      // $(".email, .password, label[for=email], label[for=password]").hide();
 	var email = $('#email').val(); 

        if(email==''){
    	  $( "#email" ).addClass( "invalid-ip" );
          return false;
  	}
        var password = $('#password').val(); 
        if(password==''){
    	  $( "#password" ).addClass( "invalid-ip" );
          return false;
  	}


  	toastr.info('Please Wait!!!');
	$.post(app_base_url + "index.php/general/send_otp", {email: email, password: password}, function(result){
        if(result)
        {
           if(result==true)
		{
$(".email, .password, label[for=email], label[for=password]").hide();
$('.opt_number').removeClass('hide');$("label[for=opt_number]").show();
$("#opt_number").prop('required',true);
$("#login_custom").prop("type", "submit");
$("#login_custom").html("Login");
$("#login_custom").removeClass("send_otp");
 toastr.info("OTP sent Successfully!!!");
  // window.location.reload();
		}
 if(result==false)
		{
 toastr.info("Invalid Login Details");
		}

          
        }
    });
  
});


</script>
