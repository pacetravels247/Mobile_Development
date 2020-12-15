<div id="general_change_password" class="bodyContent col-md-12">
<?php echo $this->session->flashdata("msg"); ?>
	<div class="panel panel-default clearfix"><!-- PANEL WRAP START -->
		<div class="panel-heading"><!-- PANEL HEAD START -->
			Change Password
		</div><!-- PANEL HEAD START -->
		<div class="panel-body"><!-- PANEL BODY START -->
			<?php
			/** Generating Change Password Form**/	
			echo $this->current_page->generate_form('change_password');
			?>
		</div><!-- PANEL BODY END -->
	</div><!-- PANEL WRAP END -->
</div>

