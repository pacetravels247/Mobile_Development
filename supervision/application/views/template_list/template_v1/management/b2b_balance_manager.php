<?php
if (is_array($search_params)) {
	extract($search_params);
}
$_datepicker = array(array('created_datetime_from', PAST_DATE), array('created_datetime_to', PAST_DATE));
$this->current_page->set_datepicker($_datepicker);
$this->current_page->auto_adjust_datepicker_report(array(array('created_datetime_from', 'created_datetime_to')));
?>
<!-- HTML BEGIN -->
<div id="general_user" class="bodyContent clearfix">
	<div class="panel panel-default clearfix"><!-- PANEL WRAP START -->
		<div class="panel-heading"><!-- PANEL HEAD START -->
			<h3>Agent Deposite Request List</h3>
		</div><!-- PANEL HEAD START -->



		 <div class="panel-body">
           <div id="show-search">

                <form method="GET" autocomplete="off">
                    <div class="clearfix form-group">  
                      <div class="col-xs-3">
                        <label>
                        Agent List
                        </label>
                        <select class="form-control select2" name="uuid">
							<option value="">All</option>
							<?php
								foreach ($agency_list as $key => $value) {
							?>
							<option value="<?=$value['uuid'];?>" <?php if(isset($_GET['uuid']) && $value['uuid'] == $_GET['uuid']) { echo 'selected';}?>><?=$value['agency_name']." - ".provab_decrypt($value['uuid']);?></option>
						<?php } ?>
					</select>
                      </div>
                      <div class="col-xs-3">
                        <label>
                        Transaction Number
                        </label>
                        <input type="text" class="form-control" name="system_transaction_id" value="<?=@$system_transaction_id?>" placeholder="Transaction Number">
                      </div>

                        <div class="col-xs-3">
                            <label>From Date : </label>
                            <input type="text" readonly id="created_datetime_from" class="form-control" name="created_datetime_from" value="<?=@$created_datetime_from?>" placeholder="Request Date">
                        </div>
                        <div class="col-xs-3">
                            <label>To Date : </label>
                            <input type="text" readonly id="created_datetime_to" class="form-control disable-date-auto-update" name="created_datetime_to" value="<?=@$created_datetime_to?>" placeholder="Request Date">
                        </div>
                    </div>

                    <div class="col-sm-offset-9 col-sm-3">
                        
                        <!-- <button type="reset" id="btn-reset" class="btn btn-warning">Reset</button> -->
                        <a href="<?php echo base_url(); ?>index.php/management/b2b_balance_manager" id="clear-filter" class="btn btn-primary btn-common">Reset</a>
                        <button type="submit" class="btn btn-danger btn-common">Search</button> 
                    </div>
                    
                </form>
            </div>
          
        </div>
        <hr>

		<?php 
			/************************ GENERATE CURRENT PAGE TABLE ************************/
			//echo get_table($table_data);
			/************************ GENERATE CURRENT PAGE TABLE ************************/
			?>

        <div class="col-sm-12">
                     <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                           <li class="active"><a href="#activity" data-toggle="tab">Pending List</a></li>
                           <li><a href="#settings" data-toggle="tab">Approved List</a></li>
                           <li><a href="#settings1" data-toggle="tab">Rejected List</a></li>
                        </ul>
                        <div class="tab-content">
                           <div class="active tab-pane" id="activity" >
                              <table id="#example1-tab2-dt" class="datatables-td datatables table table-striped table-bordered table-responsive" cellspacing="0" width="100%">
                                 <thead>
                                    <tr>
									   <th>Sl. no</th>
									   <th>System Transaction</th>
									   <th>Request From</th>
									   <th>Mode Of Payment</th>
									   <th>Amount</th>
									   <th>Status</th>
									   <th>Request Sent On</th>
									   <th>User Remarks</th>
									   <th>Update Remarks</th>
									   <th>Action</th>
									</tr></thead>
									<tbody>
										<?php
										$sl_no = 0;
										
										foreach ($table_data['pending_list'] as $key => $value) {  
											if($value['status'] == 'pending'){ 
												$sl_no = $sl_no+1; 
										if(trim($value['image'])!=""){
										$image_plus_sys_id = "<a href='".$GLOBALS["CI"]->template->domain_uploads().'deposit_slips/'.$value['image']."' target='_blank'>".$value['system_transaction_id']."</a>";
										}
										else
										{
											$image_plus_sys_id = $value['system_transaction_id'];
										}
										$current_request_status = strtoupper($value['status']);
										$default_currency_value = $value['amount'];
										$action = '';
										$action .= request_process_button($current_request_status, $value['origin'], $value['system_transaction_id'], $default_currency_value, $value['requested_from'],$value['email'],$value['request_user'],$value['phone']);
										
										$current_request_status = strtoupper($value['status']); ?>
										<tr>
											<?php $name = '';
											if(isset($value['requested_from']) && !empty($value['requested_from']) && $value['requested_from'] != ''){
												$name = $value['requested_from'];
											}else{
												foreach ($user_list as $a_key => $a_value) { 
											if(trim($value['created_by_id']) == trim($a_value['user_id'])){ 
												$name = $a_value['first_name'].' '.$a_value['last_name'];  } }
											}
										 ?>
										<td><?php echo $sl_no; ?></td>
										<td><?php echo $image_plus_sys_id; ?></td>
										<td><?php echo $name; ?></td>
										<td><?php echo strtoupper($value['transaction_type']); ?></td>
										<td><?php echo $value['amount']; ?></td>
										<td><span class="label <?php echo balance_status_label($current_request_status); ?>"><?php echo $current_request_status; ?></span></td>
										<td><?php echo app_friendly_absolute_date($value['created_datetime']); ?></td>
										<td><abbr title="<?php echo ($value['remarks'] == '' ? '--' : $value['remarks'])?>"><?php echo $value['request_user']; ?> Remarks</abbr></td>
										<td><?php echo $value['update_remarks']; ?><br><?php echo app_friendly_absolute_date($value['updated_datetime']) ?></td>
										<td><?php echo $action; ?></td>
								        </tr>
											
										<?php } } ?>
                                 </tbody>
                              </table>
                           </div><!-- /.tab-pane -->
                           <div class="tab-pane" id="settings">
                              <table id="#example2-tab3-dt" class="datatables-td datatables table table-striped table-bordered table-responsive" cellspacing="0" width="100%">
                                 <thead >
                                    <tr>
                                       <th>Sl. no</th>
									   <th>System Transaction</th>
									   <th>Request From</th>
									   <th>Mode Of Payment</th>
									   <th>Amount</th>
									   <th>Status</th>
									   <th>Request Sent On</th>
									   <th>Approved By</th>
									   <th>User Remarks</th>
									   <th>Update Remarks</th>
                                    </tr>
                                 </thead>
                                    <tbody>
										<?php
										$sl_no = 0;
										foreach ($table_data['list'] as $key => $value) {
											if($value['status'] == 'accepted'){
												$sl_no = $sl_no+1; 
										if(trim($value['image'])!=""){
										$image_plus_sys_id = "<a href='".$GLOBALS["CI"]->template->domain_uploads().'deposit_slips/'.$value['image']."' target='_blank'>".$value['system_transaction_id']."</a>";
										}
										else
										{
											$image_plus_sys_id = $value['system_transaction_id'];
										}
										$current_request_status = strtoupper($value['status']);
										$default_currency_value = $value['amount'];
										$action = '';
										$action .= request_process_button($current_request_status, $value['origin'], $value['system_transaction_id'], $default_currency_value, $value['requested_from'],$value['email'],$value['request_user'],$value['phone']);
										
										$current_request_status = strtoupper($value['status']); ?>
										<tr>
											<?php $name1 = '';
											if(isset($value['requested_from']) && !empty($value['requested_from']) && $value['requested_from'] != ''){
												$name1 = $value['requested_from'];
											}else{
												foreach ($user_list as $a_key => $a_value) { 
											if(trim($value['created_by_id']) == trim($a_value['user_id'])){ 
												$name1 = $a_value['first_name'].' '.$a_value['last_name'];  } }
											}
										 ?>
										<td><?php echo $sl_no; ?></td>
										<td><?php echo $image_plus_sys_id; ?></td>
										<td><?php echo $name1; ?></td>
										<td><?php echo strtoupper($value['transaction_type']); ?></td>
										<td><?php echo $value['amount']; ?></td>
										<td><span class="label <?php echo balance_status_label($current_request_status); ?>"><?php echo $current_request_status; ?></span></td>
										<td><?php echo app_friendly_absolute_date($value['created_datetime']); ?></td>
										<?php $name = ''; 
										foreach ($user_list as $a_key => $a_value) { 
											if(trim($value['updated_by_id']) == trim($a_value['user_id'])){ 
												$name = $a_value['first_name'].' '.$a_value['last_name'];  } } ?>
											<td><?php echo $name; ?></td>
										<td><abbr title="<?php echo ($value['remarks'] == '' ? '--' : $value['remarks'])?>"><?php echo $value['request_user']; ?> Remarks</abbr></td>
										<td><?php echo $value['update_remarks']; ?><br><?php echo app_friendly_absolute_date($value['updated_datetime']) ?></td>
								        </tr>
											
										<?php } } ?>
                                 </tbody>
                              </table>
                           </div><!-- /.tab-pane -->
                           <div class="tab-pane" id="settings1">
                              <table id="#example2-tab4-dt" class="datatables-td datatables table table-striped table-bordered table-responsive" cellspacing="0" width="100%">
                                 <thead >
                                    <tr>
                                       <th>Sl. no</th>
									   <th>System Transaction</th>
									   <th>Request From</th>
									   <th>Mode Of Payment</th>
									   <th>Amount</th>
									   <th>Status</th>
									   <th>Rejected By</th>
									   <th>Request Sent On</th>
									   <th>User Remarks</th>
									   <th>Update Remarks</th>
                                    </tr>
                                 </thead>
                                 <tbody>
										<?php
										$sl_no = 0;
										foreach ($table_data['list'] as $key => $value) { 
											if($value['status'] == 'rejected'){ 
												$sl_no = $sl_no+1; 
										if(trim($value['image'])!=""){
										$image_plus_sys_id = "<a href='".$GLOBALS["CI"]->template->domain_uploads().'deposit_slips/'.$value['image']."' target='_blank'>".$value['system_transaction_id']."</a>";
										}
										else
										{
											$image_plus_sys_id = $value['system_transaction_id'];
										}
										$current_request_status = strtoupper($value['status']);
										$default_currency_value = $value['amount'];
										$action = '';
										$action .= request_process_button($current_request_status, $value['origin'], $value['system_transaction_id'], $default_currency_value, $value['requested_from'],$value['email'],$value['request_user'],$value['phone']);
										
										$current_request_status = strtoupper($value['status']); ?>
										<tr>
											
											<?php $name = ''; $name1 = ''; 
										foreach ($user_list as $a_key => $a_value) { 
											if(trim($value['updated_by_id']) == trim($a_value['user_id'])){ 
												$name = $a_value['first_name'].' '.$a_value['last_name'];  }
												if(trim($value['created_by_id']) == trim($a_value['user_id'])){ 
												$name1 = $a_value['first_name'].' '.$a_value['last_name'];  } }
											 ?>
										<td><?php echo $sl_no; ?></td>
										<td><?php echo $image_plus_sys_id; ?></td>
										<td><?php echo isset($value['requested_from'])?$value['requested_from']:$name1; ?></td>
										<td><?php echo strtoupper($value['transaction_type']); ?></td>
										<td><?php echo $value['amount']; ?></td>
										<td><span class="label <?php echo balance_status_label($current_request_status); ?>"><?php echo $current_request_status; ?></span></td>

											<td><?php echo $name; ?></td>
										<td><?php echo app_friendly_absolute_date($value['created_datetime']); ?></td>
										<td><abbr title="<?php echo ($value['remarks'] == '' ? '--' : $value['remarks'])?>"><?php echo $value['request_user']; ?> Remarks</abbr></td>
										<td><?php echo $value['update_remarks']; ?><br><?php echo app_friendly_absolute_date($value['updated_datetime']) ?></td>
								        </tr>
											
										<?php } } ?>
                                 </tbody>
                              </table>
                           </div><!-- /.tab-pane -->
                        </div><!-- /.tab-content -->
                     </div><!-- /.nav-tabs-custom -->
                  </div>





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
			<div class="modal-body clearfix">
				<div class="alert alert-info text-bold">
					<span>Domain : <span id="request-domain-name"></span></span><br>
					<span>Amount : <span id="request-default-amount"></span></span> <?php echo COURSE_LIST_DEFAULT_CURRENCY_VALUE?><br>
					<span>Processed By : <?php echo $this->entity_name?></span>
				</div>
				
					<input type="hidden"	value=""	name="request_origin"		id="request-origin">
					<input type="hidden"	value=""	name="request_user_email"	id="request_user_email">
					<input type="hidden"	value=""	name="request_user_phone"	id="request_user_phone">
					<input type="hidden"	value=""	name="system_request_id"	id="system-request-id">
					<input type="hidden"	value=""	name="request_user"     	id="request_user">
					<div class="form-group">
						<label for="update_remarks" class="col-sm-4 control-label">Status</label>
						<div class="col-sm-8">
							<select id="status-id" name="status_id" class="form-control" autocomplete="off">
							<?php echo generate_options($provab_balance_status);?>
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
			<div class="modal-footer clearfix">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary">Save changes</button>
			</div>
		</form>
		</div>
	</div>
</div>


<script>
$(document).ready(function() {
	$(".select2").select2();
	$(document).on('click', '.request-process-btn', function(e) {
		e.preventDefault();
		var _current_status			= $(this).data('status-id');
		var _request_origin			= $(this).data('request-origin');
		var _system_request_id		= $(this).data('system-request-id');
		var _request_domain_name	= $(this).data('request-domain-name');
		var _request_default_amount	= $(this).data('request-default-amount');
		var _request_user_email	= $(this).data('request-user_email');
		var _request_user_phone	= $(this).data('request-user_phone');
		var _request_user	= $(this).data('request-user');
		
		update_process_request_details(_current_status, _request_origin, _system_request_id, _request_domain_name, _request_default_amount,_request_user_email,_request_user, _request_user_phone);
	});
	function update_process_request_details(status, request_origin, system_request_id, request_domain_name, request_default_amount,request_user_email,request_user, request_user_phone)
	{
		$('#status-id').select2("destroy");
		$('#status-id').val(status);
		$('#request-origin').val(request_origin);
		$('#system-request-id').val(system_request_id);
		$('#request-domain-name').text(request_domain_name);
		$('#request-default-amount').text(request_default_amount);
		$('#request_user_email').val(request_user_email);
		$('#request_user_phone').val(request_user_phone);
		$('#request_user').val(request_user);
	}
});
</script>

<!-- HTML END -->
<?php 
function request_process_button($status, $request_origin, $system_request_id, $default_currency_value, $agency_name,$email,$requested_user,$phone)
{
	if ($status == 'PENDING') {
		$action = '<button data-toggle="modal"   data-request-user="'.$requested_user.'" data-request-user_email="'.$email.'" data-request-user_phone="'.$phone.'" data-request-domain-name="'.$agency_name.' Travels" data-request-default-amount="'.$default_currency_value.'" data-target="#myModal" class="request-process-btn btn btn-success btn-sm" data-system-request-id="'.$system_request_id.'" data-request-origin="'.$request_origin.'" data-status-id="'.$status.'">Process</button>';
	} else {
		//NOTE :: Other status can not be reverted
		//And Action buttons are not required
		$action = '';
}
	return $action;
}
?>
<style type="text/css">
  .content-wrapper{
    padding: 0!important;
  }
  .bodyContent .nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .bodyContent .nav-tabs>li.active>a:hover {
    color: #000 !important;
    font-weight: 600;
    cursor: default;
    background-color: #fff !important;
    border: 0 !important;
  }
  .nav-tabs-custom>.nav-tabs>li.active {
    border-top-color: red;
  }
  .btn-common{
    width: 47%;
    margin-left: 5px;
  }
  .panel{
    box-shadow: 0 5px 11px 0 rgba(0,0,0,0.18),0 4px 15px 0 rgba(0,0,0,0.15) !important;
  }
</style>

<script type="text/javascript">
	$(document).ready( function () {
	    $('.datatables-td').DataTable();
	} );
</script>
<style type="text/css">
	.dataTables_wrapper .col-sm-12{
		min-height: .01%!important;
    	overflow-x: auto!important;
	}
</style>
