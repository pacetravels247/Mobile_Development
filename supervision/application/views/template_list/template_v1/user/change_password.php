<div id="general_change_password" class="bodyContent col-md-12">
	<div class="panel panel-default clearfix"><!-- PANEL WRAP START -->
		<div class="panel-heading"><!-- PANEL HEAD START -->
			<div class="panel-title">
				<ul class="nav nav-tabs" role="tablist" id="myTab">
					<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE START-->
					<li role="presentation" class=""><a href="#fromList" aria-controls="home" role="tab" data-toggle="tab"><span class="glyphicon glyphicon-pencil"></span> &nbsp;<?php echo get_app_message('AL0018');?> </a></li>
					<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE END -->
				</ul>
			</div>
		</div><!-- PANEL HEAD START -->
		<div class="panel-body"><!-- PANEL BODY START -->
			<div class="tab-content">
				  <div role="tabpanel" class="clearfix tab-pane active" id="fromList">
						<div class="col-md-12">
							<div class="panel <?=PANEL_WRAPPER?> clearfix">
								<div class="panel-heading"><?php echo get_utility_message('UL009')?></div>
								 <?php									
									/** Generating Change Password Form**/	
									echo $this->current_page->generate_form('change_password');
									?>
							</div>
						</div>
				  </div>
			</div>
		</div><!-- PANEL BODY END -->
	</div><!-- PANEL WRAP END -->
</div>

