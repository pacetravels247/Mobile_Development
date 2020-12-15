<div class="panel panel-default">
<?php //debug($info);exit;?>
<input type="hidden" class="master-user-info-id" value="<?=$info['user_id']?>">
	<!-- Default panel contents -->
	<div class="panel-heading">User Details</div>
	<div class="panel-body">
		<div class="table-responsive">
			<table class="table table-condensed">
				<tr>
					<th>User ID</th>
					<td><?=provab_decrypt($info['uuid'])?></td>
	
					<th>Name</th>
					<td><?php if(!empty($info['agency_name'])) 
								echo $info['agency_name'];
							else
								echo $info['first_name']." ". $info['last_name'];
					?></td>
					
				</tr>
				<tr>
					<th>Email</th>
					<td><?=provab_decrypt($info['email'])?></td>
					
					<th>Phone</th>
					<td><?=$info['phone']?></td>
				</tr>
			</table>
		</div>
	</div>
</div>