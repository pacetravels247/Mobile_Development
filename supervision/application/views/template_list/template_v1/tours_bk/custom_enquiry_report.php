<link href="<?php echo $GLOBALS['CI']->template->template_css_dir('bootstrap-toastr/toastr.min.css');?>" rel="stylesheet" defer>
<script src="<?php echo $GLOBALS['CI']->template->template_js_dir('bootstrap-toastr/toastr.min.js'); ?>"></script>

<div class="bodyContent">
	<div class="table_outer_wrper"><!-- PANEL WRAP START -->
		<div class="panel_custom_heading"><!-- PANEL HEAD START -->
			
			<div class="panel-title">
				<ul class="nav nav-tabs nav-justified" role="tablist" id="myTab">
					<li role="presentation" class="active" id="add_package_li"><a
						href="#add_package" aria-controls="home" role="tab"
						data-toggle="tab">Custom Enquiries</a></li>
     </ul>
    </div>
		</div><!-- PANEL HEAD START -->
		<div class="panel_bdy"><!-- PANEL BODY START -->
		
				<div class="clearfix"></div>
               

			<div class="tab-content">
				<div id="tableList" class="rigid_actions">
					<div class="pull-right">
					 <span class="">Total <?php echo $total_rows ?> Bookings</span>
					</div>
					<table class="table table-condensed table-bordered rigid_actions">
						<tr>
							<th>Sno</th>
							<th>Agent Name</th>
							<th>Agent ID</th>
							<th>Travel Type</th>
							<th>Destinations</th>
							<th>Departure City</th>
							<th>From Date</th>
							<th>To Date</th>
							<th>Night</th>
							<th>Adult</th>
							<th>Child</th>
							<th>Infant</th>
							<th>Remarks</th>
							
						</tr>
						<?php
						$current_record=1;
							if(!empty($table_data)){
							foreach($table_data as $parent_k => $parent_v) {
								extract($parent_v);
						?>
							<tr>
								<td><?php echo ($current_record++)?></td>
								<td><?php echo $agent_name;?></td>
								<td><?php echo $agent_id;?></td>
								<td><?php echo ucfirst($travel_type);?></td>
								<td><?php echo $country_name;?></td>
								<td><?php echo $city;?></td>
								<td><?php echo $fr_date;?></td>
								
								<td><?php echo $to_date;?></td>
								<td><?php echo $night; ?></td>
								<td><?php echo $adult;?></td>
								<td><?php echo $child;?></td>
								
								<td><?php echo $infant;?></td>
								<td><?php echo $remark;?></td>
								
							</tr>
						<?php
							}
							}
							else {
								echo '<tr><td>No Data Found</td></tr>';
							}
						?>
						
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
