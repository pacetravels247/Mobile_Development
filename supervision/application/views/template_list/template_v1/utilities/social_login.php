<!-- HTML BEGIN -->
<?php 
$status_options = get_enum_list('status');
//debug($social_links); exit;?>
<div class="bodyContent">
	<div class="panel panel-default clearfix">
		<!-- PANEL WRAP START -->
		<div class="panel-heading">
			<!-- PANEL HEAD START -->
			<div class="panel-title">
				<i class="fab fa-instagram"></i> Manage Social Logins
			</div>
		</div>
		<!-- PANEL HEAD START -->
		<div class="panel-body">
			<!-- PANEL BODY START -->
			<div class="table-responsive">
				<table class="table table-striped">
					<tr>
						<th>Social Network</th>
						<th>Status</th>
						<th>Config Id</th>
						<th colspan="2">Action</th>
					</tr>
			<?php
		if (valid_array($social_login)) {
			foreach($social_login as $key => $value) {
				$update = '<button class="updateButton btn btn-primary btn-sm">Update</button>';
				echo '<tr>
				<td>'.$value['social_login_name'].'</td>
				<td><select autocomplete="off" class="currency-status-toggle" id="'.$value['origin'].'">'.generate_options($status_options, array(intval($value['status']))).'</select></td>
				<td><input type="text" autocomplete="off" name="value" id="'.$value['origin'].'" class="form-control" value="'.$value['config'].'" /></td>
					
				
				<td>'.$update.'</td>
			</tr>';
			}
		} else {
			echo '<tr><td colspan=4>No Data Found</td></tr>';
		}
		?>
			</table>
			</div>
		</div>
		<!-- PANEL BODY END -->
	</div>
	<!-- PANEL END -->
</div>
<?php 
function get_status_label($status)
{
	if (intval($status) == ACTIVE) {
		return '<span class="label label-success"><i class="far fa-circle"></i> '.get_enum_list('status', ACTIVE).'</span>
	<a role="button" href="" class="hide">'.get_app_message('AL0021').'</a>';
	} else {
		return '<span class="label label-danger"><i class="far fa-circle"></i> '.get_enum_list('status', INACTIVE).'</span>
		<a role="button" href="" class="hide">'.get_app_message('AL0020').'</a>';
	}
}

function get_status_toggle_button($status, $origin)
{
	if (intval($status) == INACTIVE) {
		return '<a role="button" href="'.base_url().'index.php/utilities/activate_social_login/'.$origin.'" class="text-success">Activate</a>';
	} else {
		return '<a role="button" href="'.base_url().'index.php/utilities/deactivate_social_login/'.$origin.'" class="text-danger">Deactivate</a>';
	}
}

?>
<script>
$(document).ready(function() {
	$(document).on('click', '.location-map', function() {
		$('#map-box-modal').modal();
	});
	$('.currency-status-toggle').on('change', function () {
		var thisRef = this;
		$.get(app_base_url+'index.php/utilities/social_network_status_toggle/'+parseInt($(this).attr('id'))+'/'+$(this).val(), function (response) {
			toastr.success('Data Updated');
		});
	});
	$('.updateButton').on('click', function () {
		var thisRef = this;
		$.post(app_base_url+'index.php/utilities/edit_social_login1/'+parseFloat($(this).closest('td').siblings().children('[name="value"]').val())+'/'+$(this).closest('td').siblings().children('[name="value"]').attr('id'), function (response) {
			$(thisRef).removeClass('btn-warning');
			toastr.success('Data Updated');
		});
	});
});
</script>
<div class="modal fade bs-example-modal-lg" id="map-box-modal"
	tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="myModalLabel">Location Map</h4>
			</div>
			<div class="modal-body">
				<iframe src="" id="map-box-frame" name="map_box_frame"
					style="height: 500px; width: 850px;"> </iframe>
			</div>
		</div>
	</div>
</div>
</div>