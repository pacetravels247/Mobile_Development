<!-- HTML BEGIN -->
<div id="general_user" class="bodyContent">
	<div class="panel panel-default clearfix"><!-- PANEL WRAP START -->
		<div class="panel-heading"><!-- PANEL HEAD START -->
			<div class="panel-title">
				<ul class="nav nav-tabs" role="tablist" id="myTab">
					<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE START-->
					<li role="presentation" class="active">
						<a href="#tableList" aria-controls="profile" role="tab" data-toggle="tab">
						<i class="fa fa-money"></i>
						Received Balance Request
						</a>
					</li>
					<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE END -->
				</ul>
			</div>
		</div><!-- PANEL HEAD START -->
		<div class="panel-body"><!-- PANEL BODY START -->
			<div class="tab-content">
            <div role="tabpanel" class="clearfix tab-pane active" id="tableList">
				<?php
					/************************ GENERATE CURRENT PAGE TABLE ************************/
					echo get_table($table_data);
					/************************ GENERATE CURRENT PAGE TABLE ************************/
				?>
            </div>
			</div>
		</div><!-- PANEL BODY END -->
	</div><!-- PANEL WRAP END -->
</div>


<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		<form autocomplete="off" method="POST" action="">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel"><i class="fa fa-lock"></i> Please Verify Before You Process This Transaction Request</h4>
			</div>
			<div class="modal-body">
				<div class="alert alert-info text-bold">
					<span>Domain : <span id="request-domain-name"></span></span><br>
					<span>Amount : <span id="request-default-amount"></span></span> <?php echo COURSE_LIST_DEFAULT_CURRENCY_VALUE?><br>
					<span>Processed By : <?php echo $this->entity_name?></span>
				</div>
				
					<input type="hidden"	value=""	name="request_origin"		id="request-origin">
					<input type="hidden"	value=""	name="system_request_id"	id="system-request-id">
					<div class="form-group">
						<label for="update_remarks" class="col-sm-4 control-label">Status</label>
						<div class="col-sm-8">
							<select id="status-id" name="status_id" class="form-control" autocomplete="off">
							<?php echo generate_options($provab_balance_requests);?>
						</select>
						</div>
					</div>
					<div class="form-group">
						<label for="update-remarks" class="col-sm-4 control-label">Remarks</label>
						<div class="col-sm-8">
							<textarea class="update_remarks form-control" id="update-remarks" name="update_remarks" data-original-title="" title=""></textarea>
						</div>
					</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary">Save changes</button>
			</div>
		</form>
		</div>
	</div>
</div>
<script>
$(document).ready(function() {
	$(document).on('click', '.request-process-btn', function(e) {
		e.preventDefault();
		var _current_status			= $(this).data('status-id');
		var _request_origin			= $(this).data('request-origin');
		var _system_request_id		= $(this).data('system-request-id');
		var _request_domain_name	= $(this).data('request-domain-name');
		var _request_default_amount	= $(this).data('request-default-amount');
		update_process_request_details(_current_status, _request_origin, _system_request_id, _request_domain_name, _request_default_amount);
	});
	function update_process_request_details(status, request_origin, system_request_id, request_domain_name, request_default_amount)
	{
		$('#status-id').val(status);
		$('#request-origin').val(request_origin);
		$('#system-request-id').val(system_request_id);
		$('#request-domain-name').text(request_domain_name);
		$('#request-default-amount').text(request_default_amount);
	}
});
</script>

<!-- HTML END -->
<?php 
function get_table($table_data='')
{
	$table = '
   <div class="table-responsive col-md-12">
   <table class="table table-hover table-striped table-bordered table-condensed">';
      $table .= '<thead><tr>
   <th>Sno</th>
   <th>Domain</th>
   <th>System Transaction</th>
   <th>Mode Of Payment</th>
   <th>Amount</th>
   <th>Conversion to INR</th>
   <th>Status</th>
   <th>Request Sent On</th>
   <th>Request Contact</th>
   <th>Update Remarks</th>
   <th>Action</th>
   </tr></thead><tbody>';
	if (valid_array($table_data) == true) {
		foreach ($table_data as $k => $v) {
			$current_request_status = strtoupper($v['status']);
			$default_currency_value = ($v['amount']*$v['conversion_value']);
			$action = '';
			$action .= request_process_button($current_request_status, $v['origin'], $v['system_transaction_id'], $default_currency_value, $v['domain_name']);
			$table .= '<tr>
			<td>'.($k+1).'</td>
			<td>'.$v['domain_name'].'</td>
			<td>'.$v['system_transaction_id'].'</td>
			<td>'.get_enum_list('provab_balance_requests', strtoupper($v['transaction_type'])).'</td>
			<td>'.$v['amount'].' - '.$v['country'].'</td>
			<td>'.$v['conversion_value'].'</td>
			<td><span class="label '.balance_status_label($current_request_status).'">'.$current_request_status.'</span></td>
			<td>'.app_friendly_absolute_date($v['created_datetime']).'</td>
			<td>'.$v['first_name'].'<br><span><span class="fa fa-phone"></span> +'.$v['phone'].'</span><br><span><span class="fa fa-envelope-o"></span> '.$v['email'].'</span></td>
			<td>'.$v['update_remarks'].'<br>'.app_friendly_absolute_date($v['updated_datetime']).'</td>
			<td>'.$action.'</td>
	</tr>';
		}
	} else {
		$table .= '<tr><td colspan="3">'.get_app_message('AL005').'</td></tr>';
	}
	$table .= '</tbody></table></div>';
	return $table;
}

/**
 * 
 * @param unknown_type $status
 */
function request_process_button($status, $request_origin, $system_request_id, $default_currency_value, $request_domain_name)
{
	if ($status == 'PENDING') {
		$action = '<button data-toggle="modal" data-request-domain-name="'.$request_domain_name.'" data-request-default-amount="'.$default_currency_value.'" data-target="#myModal" class="request-process-btn btn btn-success btn-sm" data-system-request-id="'.$system_request_id.'" data-request-origin="'.$request_origin.'" data-status-id="'.$status.'">Process</button>';
	} else {
		//NOTE :: Other status can not be reverted
		//And Action buttons are not required
		$action = '';
	}
	return $action;
}