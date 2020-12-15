<!-- HTML BEGIN -->
<div id="sms_cc_users" class="bodyContent">
	<div class="panel panel-default">
		<div class="panel-body">
		<div class="panel-body"><!-- PANEL BODY START -->
			<div class="table-responsive">
			<table class="table table-striped">
			<tr>
			<th>SMS ID</th>
			<th>SMS Name</th>
			<th>Category</th>
			<th>From</th>
			<th>To</th>
			<th>Template</th>
		</tr>
			<?php
		if (valid_array($sms_data)) {
			$sms_id = $sms_data[0]["sms_id"];
			foreach($sms_data as $key => $value) { ?>
				<tr>
				<td><?php echo $value['sms_id']; ?></td>
				<td><?php echo $value['sms_name']; ?></td>
				<td><?php echo $value['category']; ?></td>
				<td><?php echo $value['from_type']; ?></td>
				<td><?php echo $value['to_type']; ?></td>
				<td><?php echo $value['template']; ?></td>
				</tr>
			<?php }
		} ?>
		</table>
		</div>
		<form id="sms_cc_users_form" method="post" 
		action="<?php echo base_url("utilities/add_sms_cc_previleges/".$sms_id); ?>">
			<table class="table table-stripped">
				<tr><th>User Type</th><th>User Name</th><th>Name</th><th>Mobile</th>
				<th>Do Send? (<a href="#" id="btn_select_all" data-check="">Check All</a>)</th></tr>
				<?php foreach($cc_users AS $user) { 
					 $is_checked = $GLOBALS["CI"]->chek_sms_user_map_exists($user["user_id"], $sms_id);
					 if($is_checked)
					 	$selected = "checked";
					 else
					 	$selected = "";
					?>
					<tr>
						<th><?php echo $user["ut_name"]; ?></th>
						<th><?php echo provab_decrypt($user["user_name"]); ?></th>
						<th><?php echo $user["first_name"]." ".$user["last_name"]; ?></th>
						<th><?php echo $user["phone"]; ?></th>
						<th><input type="checkbox" name="do_send[]" class="do_send" value="<?php echo $user["user_id"]; ?>" <?php echo $selected; ?>></th>
					</tr>
				<?php } ?>
			</table>
			<input type="submit" value="Save SMS CCs" name="sms_cc_user_submit" class="btn btn-primary">
		</form>
		</div>
	</div>
</div>			
<script>
$(function(){
	$('#btn_select_all').on('click', function(){
			var check = $(this).attr('data-check');
			if(check == ''){
				$('[name="do_send[]"]').prop('checked',true);
				$(this).attr('data-check', 'checked').text('Reset');
			} else if(check == 'checked') {
				$('.do_send').removeAttr("checked");
				$(this).attr('data-check', '').text('Check All');
			}
	});
});
</script>