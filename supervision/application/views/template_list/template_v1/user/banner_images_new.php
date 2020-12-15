<!-- HTML BEGIN -->
<div class="bodyContent">
	<div class="row">
		<div class="pull-left" style="margin:5px 0">
			<a href="<?=base_url().'index.php/user/add_banner'?>">
				<button  class="btn btn-primary btn-sm pull-right amarg">Add Banner</button>
			</a>
		</div>
	</div>
	<div class="panel <?=PANEL_WRAPPER?>"><!-- PANEL WRAP START -->
		<div class="panel-heading"><!-- PANEL HEAD START -->
			<div class="panel-title">
				<i class="fa fa-edit"></i> Banners
			</div>
		</div><!-- PANEL HEAD START -->
		
		<div class="panel-body">
			<table class="table table-condensed">
				<tr>
					<th>Sl no</th>
					<th>Title</th>
					<th>Description</th>
					<th>Image</th>
					<th>Order</th>
					<th>Status</th>
					<th>Module</th>
					<th>Side</th>
					<th>Action</th>
				</tr>
				<?php
				// debug($data_list);exit;
				if (valid_array($data_list) == true) {
					foreach ($data_list as $k => $v) :
						$action = '<a role="button" href="'.base_url().'index.php/user/edit_banner/'.$v['origin'].'"><button class="btn btn-sm">Edit</button></a>';
						if (intval($v['status']) == 1) {
							$status_label = '<span class="label label-success">Active</span>';
						} else {
							$status_label = '<span class="label label-danger">Inactive</span>';
						} 
						$status_button = '<a role="button" href="'.base_url().'index.php/user/banner_delete/'.$v['origin'].'"><button class="label label-danger">Delete</button></a>'; 
				?>
					<tr>
						<td><?=($k+1)?></td>
						<td><?=$v['title']?></td>
						<td><?=$v['subtitle']?></td>
						<td><img src="<?php echo $GLOBALS ['CI']->template->domain_ban_images ($v['image']) ?>" height="100px" width="100px" class="img-thumbnail"></td>
						<td><?=$v['banner_order']?></td>
						<td><?=$status_label ?></td>
						<td><?=$v['module']?></td>
						<td><?=$v['side']?></td>
						<td><?=$action.' '.$status_button;?></td>
					</tr>
				<?php
					endforeach;
				} else {
					echo '<tr><td>No Data Found</td></tr>';
				}
				?>
			</table>
		</div>
	</div><!-- PANEL WRAP END -->
</div>
<?php 
function get_status_label($status)
{
	if (intval($status) == ACTIVE) {
		return '<span class="label label-success"><i class="fa fa-circle-o"></i> '.get_enum_list('status', ACTIVE).'</span>
	<a role="button" href="" class="hide">'.get_app_message('AL0021').'</a>';
	} else {
		return '';
	}
}

function get_status_toggle_button($status, $origin)
{
	if (intval($status) == ACTIVE) {
		return '<a role="button" href="'.base_url().'index.php/cms/deactivate_flight_top_destination/'.$origin.'" class="text-danger">Deactivate</a>';
	} else {
		return '';		
	}
}

?>
