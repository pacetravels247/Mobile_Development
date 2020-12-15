<?php //debug($data); die; ?>
<div class="bodyContent col-md-12">
	<div class="panel panel-default clearfix"><!-- PANEL WRAP START -->
		<div class="panel-heading"><!-- PANEL HEAD START -->
			<div class="panel-title">
				<ul class="nav nav-tabs" role="tablist" id="myTab">
					<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE START-->
					<li role="presentation" class=""><a href="#fromList" aria-controls="home" role="tab" data-toggle="tab">Manage Domain <span class="fa fa-image"></span></a></li>
					<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE END -->
				</ul>
			</div>
		</div><!-- PANEL HEAD START -->
		<div class="panel-body"><!-- PANEL BODY START -->
			<div class="tab-content">
				  <div role="tabpanel" class="clearfix tab-pane active" id="fromList">
						<div class="col-md-12">
							<div class="panel panel-info clearfix">
							<div class="col-md-12 text-center domain_logo_align">
							<?php echo get_domain_logo($data['domain_logo']);?>
							</div>
							<div class="col-md-12">
								<form class="form-horizontal" role="form" id="domain_logo" enctype="multipart/form-data" method="POST" action="" autocomplete="off" name="domain_logo">        
									<input type="hidden" value="<?php echo get_domain_auth_id();?>" required="" class=" origin hiddenIp" id="origin" name="origin">
								        <div class="form-group"></div>
								        
								        <div class="form-group">
								            <label form="domain_logo" for="domain_name" class="col-sm-4 control-label">Domain Name<span class="text-danger">*</span></label>
								            <div class="col-sm-8">
								                <input type="text" id="domain_name" class="form-control alpha" placeholder="Enter your Domain Name" required=""  name="domain_name" value="<?php echo $data['domain_name'];?>">
								            </div>
								        </div>
								        <div class="form-group">
								            <label form="domain_logo" for="domain_name" class="col-sm-4 control-label">Domain Website<span class="text-danger">*</span></label>
								            <div class="col-sm-8">
								                <input type="text" id="domain_website" class="form-control alpha" placeholder="Enter your Domain Address" required=""  name="domain_website" value="<?php echo $data['domain_webiste'];?>">
								            </div>
								        </div>
								        <div class="form-group">
								            <label form="domain_logo" for="domain_logo" class="col-sm-4 control-label">Email Id</label>
								            <div class="col-sm-8">
								                <input type="email" id="email_id" class="form-control" placeholder="Enter your Email" name="email" value="<?php echo $data['email'];?>">
								            </div>
								        </div>
								        <div class="form-group">
								            <label form="domain_logo" for="domain_logo" class="col-sm-4 control-label">Supplier Voucher Receiving Email Id</label>
								            <div class="col-sm-8">
								                <input type="email" id="voucher_email" class="form-control" placeholder="Enter your Email" name="voucher_email" value="<?php echo $data['voucher_email'];?>">
								            </div>
								        </div>
								        <div class="form-group">
								            <label form="domain_logo" for="phone_number" class="col-sm-4 control-label">Phone Number</label>
								            <div class="col-sm-8">
								                <input type="text" id="phone_number" class="form-control" placeholder="Enter Your Phone Number"name="phone" maxlength="10" value="<?php echo $data['phone'];?>">
								            </div>
								        </div>
								         <div class="form-group">
								            <label form="domain_logo" for="address" class="col-sm-4 control-label">Address<span class="text-danger">*</span></label>
								            <div class="col-sm-8">
								                <input type="text" id="address" class="form-control" placeholder="Enter your Address" required=""  name="address" value="<?php echo $data['address'];?>">
								            </div>
								        </div>
								        
								        <div class="form-group">
								            <label form="domain_logo" for="country" class="col-sm-4 control-label">Country<span class="text-danger">*</span></label>
								            <div class="col-sm-8">
								                <select id="country_code" required="" onchange="get_city_list()" name="country">
								                <?php foreach($country_list as $country) { ?>
								                <option value="<?php echo $country['origin']; ?>" <?php if($data['api_country_list_fk']==$country['origin']){ echo "selected"; }?>><?php echo $country['name'];?></option>
								 
								                <?php } ?>
								                </select>
								            </div>
								        </div>
								        <div class="form-group">
								            <label form="domain_logo" for="city" class="col-sm-4 control-label">City<span class="text-danger">*</span></label>
								            <div class="col-sm-8">
								                <select name="city" id="city_list" required="">
								                <?php foreach($city_list as $city) { 
								                 if(!empty($data['api_city_list_fk'])) {?>
								                 	<option value="<?php echo $city['origin'];?>"<?php if($data['api_city_list_fk'] == $city['origin']) { echo "selected"; }?>><?php echo $city['destination']; ?> </option>
								               	<?php }} ?>
								                </select>
								            </div>
								        </div>
								        <div class="form-group">
								            <label form="domain_logo" for="domain_logo" class="col-sm-4 control-label">Change Logo<span class="text-danger">*</span></label>
								            <div class="col-sm-8">
								                <input type="file" id="domain_logo" class=" domain_logo domain_logo" placeholder=""  accept="image/*" name="domain_logo"  value="<?php if(!empty($data['domain_logo'])) {  echo $data['domain_logo']; } else{ ?>required <?php } ?> "/>
								            </div>
								        </div>
								        
								    <div class="form-group">
								        <div class="col-sm-8 col-sm-offset-4">
								            <button class=" btn btn-success " id="domain_logo_submit" type="submit">Submit</button>
								            <button class=" btn btn-warning " id="domain_logo_reset" type="reset">Reset</button>
								        </div>
								    </div>
								</form>
								</div>
							</div>
						</div>
				  </div>
			</div>
		</div><!-- PANEL BODY END -->
	</div><!-- PANEL WRAP END -->
</div>
<?php 
 function get_domain_logo($domain_logo) {
	if (empty($domain_logo) == false && file_exists($GLOBALS['CI']->template->domain_image_full_path($domain_logo))) {
		return '<img src="'.$GLOBALS['CI']->template->domain_images($domain_logo).'" height="350px" width="350px" class="img-thumbnail">';
	}
 }
?>
<script>
  function get_city_list() {
    country_code = $("#country_code").val();
 	country_code.trim();
   	$.ajax({
		type: 'POST',
		url: app_base_url+'user/get_city_list',
		//cache: true,
		 data: { country_id: country_code},
		dataType: 'html',
		success: function(result) {
			$('#city_list').html(result);
			 
			 
		}
	});
    } 
 </script>
