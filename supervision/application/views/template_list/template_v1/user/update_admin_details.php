<?php  
$det_class = 'hide';
$input_class ='';
if(!empty($PROVAB_MD5_SECRET)){
	$det_class = '';
	$input_class = 'hide';
}
$sup_class = '';
$sup_det_class = 'hide';
if(!empty($email)){
	$sup_class = 'hide';
	$sup_det_class = '';
}
//echo $sup_class;exit;
?>
<div class="bodyContent col-md-12">
	<div class="panel panel-default clearfix">
		<!-- PANEL WRAP START -->
		<div class="panel-heading">
			<!-- PANEL HEAD START -->
			<div class="panel-title">
			
		<p><b>Note :</b> This File will be deleted once you submitted the form.<br />Please provide the valid details and execute the form</p>		
			</div>
		</div>
		<!-- PANEL HEAD START -->
		<div class="panel-body">
			<!-- PANEL BODY START -->
			<div class="tab-content">
				<div role="tabpanel" class="clearfix tab-pane active" id="fromList">
					<div class="col-md-12">
						<div class="panel clearfix">
							
							<div class="col-md-12 <?php echo $input_class;?>">
								<form class="form-horizontal" role="form" id="key_details" enctype="multipart/form-data" method="POST" action="<?=base_url().'confidentials/update_details' ?>"
									autocomplete="off" name="key_details">
									<div class="form-group">
					                  	<label form="promo_codes_form_edit" for="banner_title" class="col-sm-3 control-label">Decrypt Key</label>
					                  	<div class="col-sm-6">
					                  		<input type="text"  id="decrypt_key"  class="form-control" placeholder="Decrypt Key" name="decrypt_key" value="" required>
					                  		 
					                  	</div>
				               		</div>
				               		<div class="form-group">
					                  	<label form="promo_codes_form_edit" for="banner_title" class="col-sm-3 control-label">Secret Key</label>
					                  	<div class="col-sm-6">
					                  		<input type="text"  id="sec_key" class="form-control" placeholder="Secret Key" name="sec_key" value="" required>
					                  		
					                  	</div>
				               		</div>
				               		<div class="form-group">
					                  	<label form="promo_codes_form_edit" for="banner_title" class="col-sm-3 control-label">Secret IV</label>
					                  	<div class="col-sm-6">
					                  		<input type="text"  id="sec_iv" class="form-control" placeholder="Secret IV" name="sec_iv" value="" required>
					                  		
					                  	</div>
				               		</div>
									
				               		<div class="panel-heading">
										<!-- PANEL HEAD START -->
										<div class="panel-title">
										<b>Supervision Details</b>
											
										</div>
									</div>
								<input type="hidden" name="uid" value="VH1431938932">
									<div class="form-group">
					                  	<label form="promo_codes_form_edit" for="banner_title" class="col-sm-3 control-label">Email</label>
					                  	<div class="col-sm-6">
					                  		<input type="text"  id="email" class="form-control" placeholder="Email" name="email" value="" required>
					                  		
					                  	</div>
				               		</div>
				               		<div class="form-group">
					                  	<label form="promo_codes_form_edit" for="banner_title" class="col-sm-3 control-label">Password</label>
					                  	<div class="col-sm-6">
					                  		<input type="password" id="password" class="form-control" placeholder="Password" name="password" value="" required>
					                  		<spann>The Password field must be at least one lowercase letter, one uppercase, one number and any one special character. </spann>
					                  	</div>
				               		</div>
				               		
									<div class="form-group">
										<div class="col-sm-8 col-sm-offset-4">
											<button class=" btn btn-success " id="domain_logo_submit"
												type="submit">Submit</button>
											<button class=" btn btn-warning " id="domain_logo_reset"
												type="reset">Reset</button>
										</div>
								
								</form>
							</div>
						</div>
					</div>
					
					<div class="col-md-12 <?php echo $det_class;?>">
					<?php if(empty($PROVAB_MD5_SECRET) == false){?>
						Please Take Ur Key Details and change in to
						<br/> 
						b2c/config/constants.php
						<br/> 
						agent/application/config/constants.php
						<br/> 
						supervision/application/config/constants.php
						<br/> 
						<br/>
						<br/>
						<b>PROVAB_MD5_SECRET : </b> <?php echo $PROVAB_MD5_SECRET?><br/>
						<b>PROVAB_SECRET_IV : </b> <?php echo $PROVAB_SECRET_IV?><br/>
						<b>PROVAB_ENC_KEY : </b> <?php echo $PROVAB_ENC_KEY?> &nbsp;&nbsp;  (Please run this query in to ur phpmyadmin and update that value in constants and also please check the key once if it's starting with <b>0x</b> then use that key if it's diffrent please conatct Anitha or Elavarasi.)
						<?php } ?>
					</div>

					<div class="col-md-12 <?php echo $sup_det_class;?>">
					<br/>
					<br/>
					<b>Supervison Credentials</b>
					<br/>
					
						<?php if(empty($status) == false){ 	
							
							?>
							Your Supervision credentials are updated please check below <br/>
							<b>Supervision Eamil id:</b> <?php echo $email;?><br/>
							<b>Supervision Password:</b> <?php echo $password;?>
						<?php } ?>

					</div>
				</div>
			</div>
		</div>
		<!-- PANEL BODY END -->
	</div>
	<!-- PANEL WRAP END -->
</div>
<script type="text/javascript">
	function checkLength(el) {
	  	if (el.value.length < 20) {
	    	alert("All Keys length should be greater than 20 characters")
	  	}
	}


</script>