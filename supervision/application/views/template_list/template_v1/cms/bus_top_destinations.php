<!-- HTML BEGIN -->
<div class="bodyContent">
	<div class="panel <?=PANEL_WRAPPER?>"><!-- PANEL WRAP START -->
		<div class="panel-heading"><!-- PANEL HEAD START -->
			<div class="panel-title">
				<i class="fa fa-edit"></i> Top Destinations In Bus
			</div>
		</div><!-- PANEL HEAD START -->
		<div class="panel-body"><!-- PANEL BODY START -->
			<fieldset><legend><i class="fa fa-bus"></i> City List</legend>
				<form action="<?=$_SERVER['PHP_SELF']?>" enctype="multipart/form-data" class="form-horizontal" method="POST" autocomplete="off">

					<div class="form-group">
						<label form="user" for="title" class="col-sm-3 control-label">From City_list<span class="text-danger">*</span></label>
						<div class="col-sm-5">
							<select name="city" class="form-control" required="">
								<option value="INVALIDIP">Please Select</option>
								<?=generate_options($bus_list)?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label form="user" for="title" class="col-sm-3 control-label">To City_list<span class="text-danger">*</span></label>
						<div class="col-sm-5">
							<select name="to_city" class="form-control" required="">
								<option value="INVALIDIP">Please Select</option>
								<?=generate_options($bus_list)?>
							</select>
						</div>
					</div>
					
					<div class="form-group">
						<label form="user" for="title" class="col-sm-3 control-label">Image<span class="text-danger">*</span></label>
						<div class="col-sm-6">
							<input type="file" class="" accept="image/*" required="required" name="top_destination">
						</div>
					</div>

					<div class="well well-sm">
						<div class="clearfix col-md-offset-1">
							<button class=" btn btn-sm btn-success " type="submit">Add</button>
						</div>
					</div>
				</form>
			</fieldset>
		</div><!-- PANEL BODY END -->
		<div class="panel-body">
			<table class="table table-condensed">
				<tr>
					<th>Sno</th>
					<th>From City</th>
					<th>To City</th>
					<th>Image</th>
					<th>Action</th>
				</tr>
				<?php
				//debug($data_list);exit;
				if (valid_array($data_list) == true) {
					foreach ($data_list as $k => $v) :
				?>
					<tr>
						<td><?=($k+1)?></td>
						<td><?=$v['from_city']?></td>
						<td><?=$v['to_city']?></td>
						<td><img src="<?php echo $GLOBALS ['CI']->template->domain_images ($v['image']) ?>" height="100px" width="100px" class="img-thumbnail"></td>
						<td><?php echo get_status_label($v['status']).get_status_toggle_button($v['status'], $v['origin']) ?>
							&nbsp;&nbsp;<a role="button" href="<?php echo base_url().'index.php/cms/delete_bus_top_destination/'.$v['origin']; ?>" class="text-danger">Delete</a>
						</td>
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
		return '<span class="label label-warning"><i class="fa fa-circle-o"></i> '.get_enum_list('status', INACTIVE).'</span>
	<a role="button" href="" class="hide">'.get_app_message('AL0021').'</a>';
	}
}

function get_status_toggle_button($status, $origin)
{
	if (intval($status) == ACTIVE) {
		return '&nbsp;&nbsp;<a role="button" href="'.base_url().'index.php/cms/deactivate_bus_top_destination/'.$origin.'/'.INACTIVE.'" class="text-info">Inactive</a>';
	} else {
		return '&nbsp;&nbsp;<a role="button" href="'.base_url().'index.php/cms/deactivate_bus_top_destination/'.$origin.'/'.ACTIVE.'" class="text-info">Active</a>';		
	}
}

?>
