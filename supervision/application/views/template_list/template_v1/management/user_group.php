<?php
?>
<div class="bodyContent col-md-12">
	<div class="panel panel-default clearfix">
		<div class="panel-body">
			<?php echo validation_errors(); ?>
			<h4><?php echo ucfirst($action); ?> Group</h4>
			<hr>
			<form method="POST" id="group_user" name="group" autocomplete="off" action="<?php echo base_url(). 'index.php/management/user_group/'.$group_id.'/'.$action; ?>">
				<div class="clearfix form-group">
					<div class="col-xs-6">
					
						<label>Enter Group Name</label> <br />
						<span class="group_error error"></span>
						<input type="text" class="form-control"
							name="name" required="" value="<?=@$current_group['name']?>" maxlength="28"
							placeholder="Group Name">
					</div>
				</div>
				<div class="clearfix form-group">
					<div class="col-xs-6">
						<button type="button" id="submit" class="btn btn-primary">Save</button>
					</div>

				</div>
                               

			</form>

			<div class="clearfix">
				<!-- PANEL BODY START -->
				<table class="table table-condensed table-bordered">
					<tr>
						<th>Sno</th>
						<th>Group Name</th>
						<th>Action</th>
						<th>&nbsp;</th>
					</tr>
					<?php
					if (valid_array ( $table_data ) == true) {
						
						foreach ( $table_data as $k => $v ) {
							?>
								<tr>
						<td><?=(@$k+1)?></td>
						<td><?php echo @$v['name'];?></td>
						<td><?php get_edit_button(@$v['origin'], @$v['type']); ?><?php //get_delete_button(@$v['origin'], @$v['type']); ?></td>
						<td><a href="<?php echo base_url()?>index.php/management/group_commission"><button type="Button" class="btn btn-primary">View Commission</button></a> <?php  get_delete_button(@$v['origin'], @$v['type']); ?></td>
					</tr>
							<?php
						}
					} else {
						echo '<tr><td colspan="3">No Groups Found</td></tr>';
					}
					?>						
				</table>
			</div>
		</div>
	</div>
</div>
<?php
function get_edit_button($gid, $type) {
	if ($type == 'custom')
		echo '<a href="' . base_url () . 'index.php/management/user_group/' . $gid . '" class="btn btn-primary">Update</a>';
	else
		echo 'System';
}
function get_delete_button($gid, $type) {
	if ($type == 'custom')
		echo ' <a class="callDelete btn btn-default btn-sm btn-primary" id="'.$gid.'"> 
              <span class="glyphicon glyphicon-trash"></span> </a>';
		else
			echo 'System';
}

?>
<script type="text/javascript">  
$(document).ready(function()
{
   
  $(".callDelete").click(function() { 
    $id = $(this).attr('id'); 
        $response = confirm("Are you sure to delete this group ?\nNote: If you delete this group, all the agents of this group wiil be moved to 'Default Group'.");
      
        if($response==true){ window.location='<?=base_url()?>index.php/management/user_group/'+$id+"/"+"delete"; } else{}
   });

  $('#submit').click(function(e){
  	//e.preventDefault();
  	var url = '<?php echo base_url(); ?>';
  	var group_name = $('input[name="name"]').val();
  	var action = '<?php echo $action; ?>';
  	var group_id = '<?php echo @$group_id ?>';
  	$.post( url + 'index.php/management/check_user_group', { name: group_name, action : action, group_id : group_id })
	  .done(function( data ) {
	    if(data == "1"){
	    	location.reload();
	    }else{
	    	$('.group_error').html('Group Name Already Exists...');
	    }
	});
  });
});
</script>