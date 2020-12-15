<!-- HTML BEGIN -->
<div class="bodyContent">
	<div class="panel panel-default clearfix"><!-- PANEL WRAP START -->
		<div class="panel-heading"><!-- PANEL HEAD START -->
			<div class="panel-title"><i class="fa fa-shield"></i> Exception Event Logs</div>
		</div>
		<!-- PANEL HEAD START -->
		<div class="panel-body"><!-- PANEL BODY START -->
			<div class="">
				<?php echo $this->pagination->create_links();?> <span class="">Total <?php echo $total_rows ?> Records</span>
			</div>
			<div class="table-responsive">
			<table class="table table-striped">
				<tr>
					<th>Sno</th>
					<th>Reference ID</th>
					<th>Operation</th>
					<th>Message</th>
					<th>User Info</th>
					<th>Browser</th>
					<th>IP</th>
					<th>Occured On</th>
				</tr>
			<?php
			if (valid_array($table_data)) {
				foreach ($table_data as $k => $v) {
					$client_info = unserialize($v['client_info']);
					$user_info = '';
					$user_info .= '<ul class="list-group">';
					$user_info .= '<li class="list-group-item">IP:'.$client_info['query'].'</li>';
					$user_info .= '<li class="list-group-item">Country:'.$client_info['country'].'</li>';
					$user_info .= '<li class="list-group-item">Time-Zone:'.$client_info['timezone'].'</li>';
					if (empty($client_info['lat']) == false and empty($client_info['lon']) == false) {
						$user_info .= '<li class="list-group-item"><a target="map_box_frame" href="'.base_url().'index.php/general/event_location_map?latitude='.$client_info['lat'].'&longtitude='.$client_info['lon'].'&ip='.$client_info['query'].'" class="location-map"><i class="fa fa-globe"></i>Click to view Location</li>';
					}
					$user_info .= '</ul>';
				?>
					<tr>
						<td><?=($k+1)?></td>
						<td><?=$v['exception_id']?></td>
						<td><?=$v['op']?></td>
						<td><?=$v['notification']?></td>
						<td><?=$user_info?></td>
						<td><?=$v['user_agent']?></td>
						<td><?=$v['user_ip']?></td>
						<td><?=app_friendly_date($v['created_datetime'])?></td>
					</tr>
				<?php
				}
			} else {
				echo '<tr><td>No Data Found</td></tr>';
			}
			?>
			</table>
			</div>
		</div><!-- PANEL BODY END -->
	</div><!-- PANEL END -->
</div>
<script>
$(document).ready(function() {
	$(document).on('click', '.location-map', function() {
		$('#map-box-modal').modal();
	});
});
</script>
<div class="modal fade bs-example-modal-lg" id="map-box-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Location Map</h4>
			</div>
			<div class="modal-body">
				<iframe src="" id="map-box-frame" name="map_box_frame" style="height: 500px;width: 850px;">
				</iframe>
				</div>
			</div>
		</div>
	</div>
</div>