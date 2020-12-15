<!-- HTML BEGIN -->
<div class="bodyContent">
	<div class="row">
		<div class="pull-left" style="margin:5px 0">
			<a href="<?=base_url().'index.php/cms/add_why_choose_us	'?>">
				<button  class="btn btn-primary btn-sm pull-right amarg">Add Feature</button>
			</a>
		</div>
	</div>
	<div class="panel <?=PANEL_WRAPPER?>"><!-- PANEL WRAP START -->
		<div class="panel-heading"><!-- PANEL HEAD START -->
			<div class="panel-title">
				<i class="fa fa-edit"></i> Headings
			</div>
		</div><!-- PANEL HEAD START -->
		
		<div class="panel-body">
			<table class="table table-condensed">
				<tr>
					<th>Sl no</th>
					<th>Title</th>
					<th>Icon</th>
					<th>Status</th>
					<th>Action</th>
				</tr>
				<?php
				// debug($data_list);exit;
				if (valid_array($data_list) == true) {
					foreach ($data_list as $k => $v) :
					
				?>
					<tr>
						<td><?=($k+1)?></td>
						<td><?=$v['title']?></td>
						<td><?=$v['icon']?></td>
						<td><?=get_status_toggle_button($v['status'], $v['origin'])?></td>
						<td><?=get_edit_button($v['origin'])?> <?php echo " "?>
						<a href="<?php echo base_url(); ?>cms/delete_why_choose/<?php echo $v['origin']; ?>/<?php echo $v['origin']; ?>"  data-original-title="Delete"  onclick="return confirm('Do you want delete this record');" class="btn btn-danger btn-xs has-tooltip" data-original-title="Delete"> 
                                  <i class="icon-remove">Delete</i>
                                   </a>

						</td></tr>
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
	$status_options = get_enum_list('status');
	return '<select class="toggle-user-status" data-origin="'.$origin.'">'.generate_options($status_options, array($status)).'</select>';
}
function get_edit_button($origin)
{
	return '<a role="button" href="'.base_url().'index.php/cms/add_why_choose_us?'.$_SERVER['QUERY_STRING'].'&	origin='.$origin.'" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i>
		'.get_app_message('AL0022').'</a>
		';
	
}

?>
<script>
$(document).ready(function() {
	$('.toggle-user-status').on('change', function(e) {
		e.preventDefault();
		var _user_status = this.value;
		var _opp_url = app_base_url+'index.php/cms/';
		if (parseInt(_user_status) == 1) {
			_opp_url = _opp_url+'activate_why_choose/';
		} else {
			_opp_url = _opp_url+'deactivate_why_choose/';
		}
		_opp_url = _opp_url+$(this).data('origin');
		toastr.info('Please Wait!!!');
		$.get(_opp_url, function() {
			toastr.info('Updated Successfully!!!');
		});
	});
});
</script>
