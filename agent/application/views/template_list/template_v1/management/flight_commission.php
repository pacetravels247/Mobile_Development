<!-- HTML BEGIN -->
<div class="bodyContent">
	<div class="table_outer_wrper"><!-- PANEL WRAP START -->
		<div class="panel_custom_heading"><!-- PANEL HEAD START -->
			<div class="panel_title">
            	<?php include 'b2b_commission_header_tab.php';?>
			</div>
		</div><!-- PANEL HEAD START -->
		<div class="panel panel-body"><!-- PANEL BODY START -->
			<?php 
				$flight_commission_details = $commission_details['flight_commission_details'][0];
			?>
			<div class="col-md-12"><h4><i class="fa fa-plane"></i> Flight Commission: <strong><?=$flight_commission_details['api_value']?> %</strong></h4></div>
		</div><!-- PANEL BODY END -->
	</div><!-- PANEL WRAP END -->
</div>