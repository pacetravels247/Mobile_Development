<script src="<?php echo SYSTEM_RESOURCE_LIBRARY?>/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo SYSTEM_RESOURCE_LIBRARY?>/datatables/dataTables.bootstrap.min.js"></script>
<!-- HTML BEGIN -->
<?php
if (is_array($search_params)) {
	extract($search_params);
}

$_datepicker = array(array('created_date_from', PAST_DATE), array('created_date_to', PAST_DATE));
$this->current_page->set_datepicker($_datepicker);
$this->current_page->auto_adjust_datepicker(array(array('created_date_from', 'created_date_to')));
?>
<div id="general_user" class="bodyContent">
<?php echo validation_errors(); ?>
	<div class="table_outer_wrper"><!-- PANEL WRAP START -->
        <div class="clearfix"></div>
		<div class="panel_bdy"><!-- PANEL BODY START -->
            <div class="panel-body">
			<h4>Search Panel</h4>
			<hr>
			<form method="GET" autocomplete="off">
				<div class="clearfix form-group">
					<div class="col-xs-4">
						<label>
						Bus PNR
						</label>
						<input type="text" class="form-control" name="bus_pnr" value="<?=@$bus_pnr?>" placeholder="Bus PNR">
					</div>
					<div class="col-xs-4">
						<label>
						Request From
						</label>
						<input type="text" readonly id="created_date_from" class="form-control" name="created_date_from" value="<?=@$created_date_from?>" placeholder="Request Date">
					</div>
					<div class="col-xs-4">
						<label>
						Request To
						</label>
						<input type="text" readonly id="created_date_to" class="form-control disable-date-auto-update" name="created_date_to" value="<?=@$created_date_to?>" placeholder="Request Date">
					</div>
				</div>
				<div class="col-sm-12 well well-sm">
				<button type="submit" class="btn btn-primary">Search</button> 
				<a href="<?php echo base_url(); ?>index.php/management/bus_operator_cancellation" id="clear-filter" class="btn btn-primary">ClearFilter</a>
				</div>
			</form>
		</div>
				<?php
					/************************ GENERATE CURRENT PAGE TABLE ************************/
					echo get_table($table_data);
					/************************ GENERATE CURRENT PAGE TABLE ************************/
				?>
			</div>
		</div><!-- PANEL BODY END -->
	</div><!-- PANEL WRAP END -->
<!-- HTML END -->
<?php 
function get_table($table_data='')
{
	$table = '
   <div class="table-responsive col-md-12">
   <table class="table table-hover table-striped table-bordered table-condensed" id="operator_cancel_req_table">';
      $table .= '<thead><tr>
   <th>Sno</th>
   <th>Bus PNR</th>
   <th>Agency Name</th>
   <th>Agent Name</th>
   <th>Agent Email</th>
   <th>Reason</th>
   <th>Requested Date</th>
   <th>Updated Reason</th>
   <th>Updated Status</th>
   </tr></thead><tbody>';
	if (valid_array($table_data) == true) {
		$boc_request_statuses = get_enum_list('boc_request_status');
		foreach ($table_data as $k => $v) {
			$table .= '<tr id="bop_'.$v['id'].'">
			<td>'.($k+1).'</td>
			<td>'.$v['bus_pnr'].'</td>
			<td>'.$v['agency_name'].' - '.provab_decrypt($v['uuid']).'</td>
			<td>'.$v['first_name'].' '.$v['last_name'].'</td>
			<td>'.provab_decrypt($v['email']).'</td>
			<td>'.$v['reason'].'</td>
			<td>'.$v['created_date'].'</td>
			<td>'.$v['request_reason'].'</td>
			<td>
			<form action="'.base_url("index.php/management/bus_operator_cancellation/".
				$v['id']).'" method="post">
			<select name="request_status" class="update_request_status">
				'.generate_options($boc_request_statuses, array($v['request_status'])).'
			</select>
			</form>
			</td>
		</tr>';
		}
	} else {
		$table .= '<tr>
					<td>---</td><td>---</td><td>---</td><td>---</td><td>---</td><td>---</td>
					<td>---</td><td>---</td><td>---</td>
					</tr>';
	}
	$table .= '</tbody></table></div>';
	return $table;
}
?>
<div id="bop_reason_update_modal" class="modal fade in" tabindex="-1" role="dialog" aria-hidden="false">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>        
            <h4 class="modal-title">Specify Reason</h4>
         </div>
         <div class="modal-body">
 <!-- Here the data will be loaded by jquery -->
<div id="message"></div><br>
 <form class="form-horizontal" role="form" id="bop_reason_update_form" method="post">
   <fieldset form="bop_reason_update_fields">
	  <div class="form-group">
         <label form="bop_status" for="bop_status" class="col-sm-3 control-label">
         Status</label>                                                
         <div class="col-sm-6">
			<select name="request_status" id="bop_status" class="bop_status">
				<?php $boc_request_statuses = get_enum_list('boc_request_status'); 
				echo generate_options($boc_request_statuses); ?>
			</select>
         </div>
      </div>	
      <div class="form-group">
         <label form="request_reason" for="request_reason" class="col-sm-3 control-label">
         Reason</label>                                                
         <div class="col-sm-6">
         <textarea id="request_reason" class="form-control" placeholder="Specify Reason here" 
         name="request_reason" required></textarea>                                             
         </div>
      </div>
   </fieldset>
   <div class="form-group">
      <div class="col-sm-8 col-sm-offset-4"> 
      <button class="btn btn-success" type="submit" id="save_quote_btn">Save</button> <button class=" btn btn-warning " id="reset_form" type="reset">Reset</button></div>
   </div>
 </form>
</div>
 <div class="modal-footer">
 <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
 </div>
  </div>
  <!-- /.modal-content -->  
</div>
<!-- /.modal-dialog -->
</div>
<script>
$(document).ready(function() {
	$(".update_request_status").change(function(){
		var req_status = $(this).val();
		$("#bop_status").val(req_status);
		$("#bop_status").select2("destroy").select2();
		var action_scene = $(this).parent("form").attr("action");
		$("#bop_reason_update_form").attr("action", action_scene);
		$("#bop_reason_update_modal").modal("show");
	});
    $('#operator_cancel_req_table').DataTable({
        // Disable initial sort 
        "aaSorting": []
    });

    $('#operator_cancel_req_table_filter').addClass('pull-right');	

});
</script>
