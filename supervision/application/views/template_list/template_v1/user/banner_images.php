<div class="bodyContent col-md-12">
	<div class="panel panel-default clearfix">
		<!-- PANEL WRAP START -->
		<div class="panel-heading">
			<!-- PANEL HEAD START -->
			<div class="panel-title">
				<ul class="nav nav-tabs" role="tablist" id="myTab">
					<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE START-->
					<li role="presentation" class=""><a href="#fromList"
						aria-controls="home" role="tab" data-toggle="tab"><?php echo get_app_message('AL00316');?> <span
							class="fa fa-image"></span></a></li>
					<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE END -->
				</ul>
			</div>
		</div>
		<!-- PANEL HEAD START -->
		<div class="panel-body">
			<!-- PANEL BODY START -->
			<div class="tab-content">
				<div role="tabpanel" class="clearfix tab-pane active" id="fromList">
					<div class="col-md-12">
						<div class="panel panel-info clearfix">
							<div class="col-md-12 col-md-offset-3 domain_logo_align">
							<?php echo get_banner_image($banner_image);?>
							</div>
							<div class="col-md-12">
								<form class="form-horizontal" role="form" id="banner_image"
									enctype="multipart/form-data" method="POST" action=""
									autocomplete="off" name="banner_image">
									<input type="hidden" value="<?php echo get_domain_auth_id();?>"
										required="" class=" origin hiddenIp" id="added_by"
										name="added_by">
									<div class="form-group"></div>
									<div class="form-group">
										<label form="domain_logo" for="domain_logo"
											class="col-sm-4 control-label">Change banner_image<span
											class="text-danger">*</span></label>
										<div class="col-sm-8">
											<input type="file" name="banner_image"
												class=" domain_logo" 
												accept="image/*">
										</div>
									</div>
									<div class="form-group">
										<div class="col-sm-8 col-sm-offset-4">
											<button class=" btn btn-success " id="domain_logo_submit"
												type="submit">Submit</button>
											<button class=" btn btn-warning " id="domain_logo_reset"
												type="reset">Reset</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- PANEL BODY END -->
	</div>
	<!-- PANEL WRAP END -->
</div>
<?php
function get_banner_image($banner_image) {
	if (empty ( $banner_image ) == false && file_exists ( $GLOBALS ['CI']->template->domain_image_full_path ( $banner_image ) )) {
		return '<img src="' . $GLOBALS ['CI']->template->domain_images ( $banner_image ) . '" height="350px" width="350px" class="img-thumbnail">';
	}
 }
?>
