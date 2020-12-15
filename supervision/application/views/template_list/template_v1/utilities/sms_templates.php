<!-- HTML BEGIN -->
<div class="bodyContent">
	<div class="panel panel-default clearfix"><!-- PANEL WRAP START -->
		<div class="panel-heading"><!-- PANEL HEAD START -->
			<div class="panel-title"><i class="fa fa-envelope"></i>  Manage SMS Templates <small></small></div>
		</div>
		<?php if(!$id) { ?>
		<!-- PANEL HEAD START -->
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
			<th>Action</th>
		</tr>
			<?php
		if (valid_array($sms_data)) {
			foreach($sms_data as $key => $value) {
				$update = '<button class="updateButton btn btn-primary btn-sm">Update</button>';
				echo '<tr>
				<td>'.$value['sms_id'].'</td>
				<td>'.$value['sms_name'].'</td>
				<td>'.$value['category'].'</td>
				<td>'.$value['from_type'].'</td>
				<td>'.$value['to_type'].'</td>
				<td>'.$value['template'].'</td>
				<td><a class="btn btn-primary" href="'.base_url("utilities/sms_templates/".$value['template_id']).'">Edit</a>
				<a class="btn btn-primary" href="'.base_url("utilities/cc_sms/".$value['sms_id']).'">CC SMS</a></td>
			</tr>';
			}
		} else {
			echo '<tr><td colspan=4>No Data Found</td></tr>';
		}
		?>
			</table>
			</div>
		</div><!-- PANEL BODY END -->
		<?php } 
		else {
		?>
		<div class="panel-body">
			<form name="sms_edit" class="form-horizontal" method="post">
				<div class="form-group">
					<label class="col-sm-3 control-label" for="first_name" form="sms_edit">
						SMS Name <span class="text-danger">*</span>
					</label>
					<div class="col-sm-6">
						<input name="sms_name" type="text" placeholder="SMS Name" class="form-control"
						value="<?php echo $sms_data[0]["sms_name"]; ?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label" for="category" form="sms_edit">
						Category <span class="text-danger">*</span>
					</label>
					<div class="col-sm-6">
						<input name="category" type="text" placeholder="Category" class="form-control"
						value="<?php echo $sms_data[0]["category"]; ?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label" for="template" form="sms_edit">
						Template <span class="text-danger">*</span>
					</label>
					<div class="col-sm-6">
						<textarea name="template" style="width: 570px;"><?php echo $sms_data[0]["template"]; ?></textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label">
					</label>
					<div class="col-sm-6">
						<input type="submit" name="submit_sms_template" class="btn btn-primary" value="submit">
					</div>
				</div>
			</form>
		</div>
		<?php } ?>
	</div><!-- PANEL END -->
</div>