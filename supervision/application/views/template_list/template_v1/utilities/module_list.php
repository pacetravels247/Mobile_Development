<!-- HTML BEGIN -->
<div class="bodyContent">
	<div class="panel panel-default clearfix"><!-- PANEL WRAP START -->
		<div class="panel-heading"><!-- PANEL HEAD START -->
			<div class="panel-title"><i class="fa fa-envelope"></i>  Manage Modules</div>
		</div>
		<!-- PANEL HEAD START -->
		<div class="panel-body"><!-- PANEL BODY START -->
			<div class="table-responsive">
			<table class="table table-striped">
				<tr>
			<th>Sl.No</th>
			<th>Module</th>
			<th>Action</th>
		</tr>
			<?php
		if (valid_array($domain_list)) {
			foreach($domain_list as $key => $value) {
				$update = '<button class="updateButton btn btn-primary btn-sm">Update</button>';
				echo '<tr>
				<td>'.$value['origin'].'</td>
				<td>'.$value['module'].'</td>
				<td>'.get_status_label($value['status']).get_status_toggle_button($value['status'], $value['origin']).'</td>
			</tr>';
			}
		} else {
			echo '<tr><td colspan=4>No Data Found</td></tr>';
		}
		?>
			</table>
			</div>
		</div><!-- PANEL BODY END -->
	</div><!-- PANEL END -->
</div>
<?php 
function get_status_label($status)
{
	if (intval($status) == ACTIVE) {
		return '<span class="label label-success"><i class="fa fa-circle-o"></i> '.get_enum_list('status', ACTIVE).'</span>
	<a role="button" href="" class="hide">'.get_app_message('AL0021').'</a>';
	} else {
		return '<span class="label label-danger"><i class="fa fa-circle-o"></i> '.get_enum_list('status', INACTIVE).'</span>
		<a role="button" href="" class="hide">'.get_app_message('AL0020').'</a>';
	}
}

function get_status_toggle_button($status, $condition)
{
	if (intval($status) == INACTIVE) {
		return '<a role="button" href="'.base_url().'index.php/utilities/activate_module/'.$condition.'" class="text-success">Activate</a>';
	} else {
		return '<a role="button" href="'.base_url().'index.php/utilities/deactivate_module/'.$condition.'" class="text-danger">Deactivate</a>';
	}
}

?>
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