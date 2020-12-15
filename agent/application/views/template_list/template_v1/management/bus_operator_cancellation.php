<script src="<?php echo SYSTEM_RESOURCE_LIBRARY?>/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo SYSTEM_RESOURCE_LIBRARY?>/datatables/dataTables.bootstrap.min.js"></script>
<!-- HTML BEGIN -->
<?php
/**if (form_visible_operation()) {
 $tab1 = " active ";
 $tab2 = "";

 } else {
 $tab2 = " active ";
 $tab1 = "";
 } **/
$url =$this->uri->segment(3);
if($url != null ){
	$tab1 = "";
	$tab2 = "active";
}else{

	$tab1 = "active";
	$tab2 = "";
}
if (is_array($search_params)) {
	extract($search_params);
}

$_datepicker = array(array('created_date_from', PAST_DATE), array('created_date_to', PAST_DATE));
$this->current_page->set_datepicker($_datepicker);
$this->current_page->auto_adjust_datepicker(array(array('created_date_from', 'created_date_to')));
?>
<div id="general_user" class="bodyContent">
<?php echo validation_errors(); ?>
<?php echo $this->session->flashdata("msg"); ?>
	<div class="table_outer_wrper"><!-- PANEL WRAP START -->
		<div class="panel_custom_heading"><!-- PANEL HEAD START -->
			<div class="panel_title">
				<ul class="nav nav-tabs b2b_navul" role="tablist" id="myTab">
					<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE START-->
					<li role="presentation" class="<?php echo $tab1; ?>">
						<a id="fromListHead" href="#fromList" aria-controls="home" role="tab" data-toggle="tab">
							<i class="fa fa-edit"></i>
							New Operator Cancel Request
						</a>
					</li>
					<li role="presentation" class="<?php echo $tab2; ?>">
						<a href="#tableList" aria-controls="profile" role="tab" data-toggle="tab">
						<i class="fa fa-money"></i>
						List of all requests sent
						</a>
					</li>
					<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE END -->
				</ul>
			</div>
		</div><!-- PANEL HEAD START -->
        <div class="clearfix"></div>
		<div class="panel_bdy"><!-- PANEL BODY START -->
			<div class="tab-content">
				<div role="tabpanel" class="tab-pane <?php echo $tab1; ?> clearfix" id="fromList">
				<div class="panel_inside">   
					<div class="section_deposite">
					<form class="form-horizontal" action="<?php echo base_url("index.php/management/bus_operator_cancellation"); ?>" method="post">
					<legend class="form_legend">Fill below details</legend>
						<div class="form-group">
					         <label class="col-sm-3 control-label" for="branch" form="request_form">Bus PNR<span class="text-danger">*</span></label>
					         <div class="col-sm-6">
					         	<input value="" name="bus_pnr" required="" type="text" 
					         	class="form-control invalid-ip">
					         </div>
					      </div>
					      <div class="form-group">
					         <label class="col-sm-3 control-label" for="branch" form="request_form">Reason<span class="text-danger">*</span></label>
					         <div class="col-sm-6">
					         	<input value="" name="reason" required="" type="text" 
					         	class="form-control invalid-ip">
					         </div>
					      </div>
					      <div class="col-sm-8 col-sm-offset-4">
						      <button type="submit" id="request_form_submit" class="btn btn-success ">Save</button>
						      <button type="reset" id="request_form_reset" class="btn btn-danger">Reset</button>
					      </div>
					  </form>    
					</div>
               </div>
            </div>
            <div role="tabpanel" class="tab-pane <?php echo $tab2; ?> clearfix" id="tableList">
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
			</div>
		</div><!-- PANEL BODY END -->
	</div><!-- PANEL WRAP END -->
</div>
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
		foreach ($table_data as $k => $v) {
			$table .= '<tr>
			<td>'.($k+1).'</td>
			<td>'.$v['bus_pnr'].'</td>
			<td>'.$v['agency_name'].'</td>
			<td>'.$v['first_name'].' '.$v['last_name'].'</td>
			<td>'.provab_decrypt($v['email']).'</td>
			<td>'.$v['reason'].'</td>
			<td>'.$v['created_date'].'</td>
			<td>'.$v['request_reason'].'</td>
			<td>'.$v['request_status'].'</td>
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
<script>
$(document).ready(function() {
    $('#operator_cancel_req_table').DataTable({
        // Disable initial sort 
        "aaSorting": []
    });
});
</script>