<!-- HTML BEGIN -->
<div id="general_user" class="bodyContent">
	<div class="panel panel-default clearfix"><!-- PANEL WRAP START -->
		<div class="panel-heading"><!-- PANEL HEAD START -->
			<div class="panel-title">
				<ul class="nav nav-tabs" role="tablist" id="myTab">
					<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE START-->
					<li role="presentation" class="active">
						<a id="fromListHead" href="#fromList" aria-controls="home" role="tab" data-toggle="tab">
							<i class="fa fa-edit"></i>
							New Balance Request
						</a>
					</li>
					<li role="presentation">
						<a href="#tableList" aria-controls="profile" role="tab" data-toggle="tab">
						<i class="fa fa-money"></i>
						Sent Balance Request
						</a>
					</li>
					<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE END -->
				</ul>
			</div>
		</div><!-- PANEL HEAD START -->
		<div class=""><!-- PANEL BODY START -->
			<div class="tab-content">
				<div role="tabpanel" class="clearfix tab-pane active" id="fromList">
				<div class="panel <?=PANEL_WRAPPER?> clearfix">
					<div class="panel-heading">
						Request Type :
						<select id="balance_request_type" class="form-control" autocomplete="off">
							<?php echo generate_options($provab_balance_requests, array($balance_request_type));?>
						</select>
					</div>
					<div class="">
					<?php
						/************************ GENERATE CURRENT PAGE FORM ************************/
						echo $balance_page_obj->generate_form('request_form', $form_data);
						/************************ GENERATE UPDATE PAGE FORM ************************/
					?>
					</div>
               </div>
            </div>
            <div role="tabpanel" class="clearfix tab-pane" id="tableList">
            	<a href="<?php echo base_url()?>index.php/management/master_balance_manager"><strong>Current Balance</strong> : <strong><?php $balance = current_application_balance(); echo $balance['face_value']?></strong></a>
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
<script>
$(document).ready(function() {
	$('#balance_request_type').on('change', function() {
		//reload window with new parameter
		var _request_type = $(this).val();
		if (_request_type != '') {
			window.location.href = app_base_url+'index.php/management/master_balance_manager/'+_request_type;
		} else {
			location.reload();
		}
	});
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
   <th>System Transaction</th>
   <th>Mode Of Payment</th>
   <th>Cheque / DD No.</th>
   <th>Amount</th>
   <th>Status</th>
   <th>Request Sent On</th>
   <th>Update Remarks</th>
   </tr></thead><tbody>';
	if (valid_array($table_data) == true) {
		foreach ($table_data as $k => $v) {
			$current_request_status = strtoupper($v['status']);
			$table .= '<tr>
			<td>'.($k+1).'</td>
			<td>'.$v['system_transaction_id'].'</td>
			<td>'.get_enum_list('provab_balance_requests', strtoupper($v['transaction_type'])).'</td>
			<td>'.$v['trasaction_number'].'</td>
			<td>'.$v['amount'].'</td>
			<td><span class="label '.balance_status_label($current_request_status).'">'.$current_request_status.'</span></td>
			<td>'.app_friendly_absolute_date($v['created_datetime']).'</td>
			<td>'.$v['update_remarks'].'<br>'.app_friendly_absolute_date($v['updated_datetime']).'</td>
	</tr>';
		}
	} else {
		$table .= '<tr><td colspan="3">'.get_app_message('AL005').'</td></tr>';
	}
	$table .= '</tbody></table></div>';
	return $table;
}
?>