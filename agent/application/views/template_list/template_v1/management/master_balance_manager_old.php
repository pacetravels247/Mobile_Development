<!--  <script src="<?php echo SYSTEM_RESOURCE_LIBRARY?>/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo SYSTEM_RESOURCE_LIBRARY?>/datatables/dataTables.bootstrap.min.js"></script> -->
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
	$tab1 = " active ";
	$tab2 = "";
}else{

	$tab2 = " active ";
	$tab1 = "";
}

if (is_array($search_params)) {
	extract($search_params);
}
$_datepicker = array(array('created_datetime_from', PAST_DATE), array('created_datetime_to', PAST_DATE));
$this->current_page->set_datepicker($_datepicker);
$this->current_page->auto_adjust_datepicker(array(array('created_datetime_from', 'created_datetime_to')));
?>
<div id="general_user" class="bodyContent">

	<div class="table_outer_wrper"><!-- PANEL WRAP START -->
		<div class="panel_custom_heading"><!-- PANEL HEAD START -->
			<div class="panel_title">
				<ul class="nav nav-tabs b2b_navul" role="tablist" id="myTab">
					<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE START-->
					<li role="presentation" class="<?php echo $tab1; ?>">
						<a id="fromListHead" href="#fromList" aria-controls="home" role="tab" data-toggle="tab">
							<i class="fa fa-edit"></i>
							New Balance Request
						</a>
					</li>
					<li role="presentation" class="<?php echo $tab2; ?>">
						<a href="#tableList" aria-controls="profile" role="tab" data-toggle="tab">
						<i class="fa fa-money"></i>
						Sent Balance Request
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
                
				<div class="panel-body">
					
                 <!-- <div class="col-xs-4">
                        <label class="bordinglbl">Request Type </label>
                        <div class="panel_selcts selctmark_dash">
                            <select id="balance_request_type" class="normalsel_dash" autocomplete="off">
                                <?php //echo generate_options($provab_balance_requests, array($balance_request_type));?>
                            </select>
                        </div>
                     </div>  -->   
                  
                  <div class="form-group">
                   <label class="col-sm-3 control-label text-right">Request Type </label>
                     <div class="col-sm-6">                        
                        <div class="panel_selcts selctmark_dash">
                            <select id="balance_request_type" class="normalsel_dash" autocomplete="off">
                                <?php echo generate_options($provab_balance_requests, array($balance_request_type));?>
                            </select>
                        </div>
                     </div>
                    </div>
                    
                   <div class="clearfix">
                     </div>
                
					<div class="section_deposite">
					
					<?php
				
						/************************ GENERATE CURRENT PAGE FORM ************************/
						echo $balance_page_obj->generate_form('request_form', $form_data);
						/************************ GENERATE UPDATE PAGE FORM ************************/
					?>
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
						Transaction Number
						</label>
						<input type="text" class="form-control" name="system_transaction_id" value="<?=@$system_transaction_id?>" placeholder="Transaction Number">
					</div>
					<div class="col-xs-4">
						<label>
						Status
						</label>
						<select class="form-control" name="status">
							<option>All</option>
							<?=generate_options($status_options, array(@$status))?>
						</select>
					</div>
					<div class="col-xs-4">
						<label>
						Request From
						</label>
						<input type="text" readonly id="created_datetime_from" class="form-control" name="created_datetime_from" value="<?=@$created_datetime_from?>" placeholder="Request Date">
					</div>
					<div class="col-xs-4">
						<label>
						Request To
						</label>
						<input type="text" readonly id="created_datetime_to" class="form-control disable-date-auto-update" name="created_datetime_to" value="<?=@$created_datetime_to?>" placeholder="Request Date">
					</div>
				</div>
				<div class="col-sm-12 well well-sm">
				<button type="submit" class="btn btn-primary">Search</button> 
				<button type="reset" class="btn btn-warning">Reset</button>
				</div>
			</form>
		</div>
            	<a href="<?php echo base_url()?>management/master_balance_manager"><strong>Current Balance</strong> : <strong><?php $balance = agent_current_application_balance(); echo agent_base_currency().' '.$balance['value']?></strong></a>
				<?//=$GLOBALS['CI']->template->isolated_view('report/report_tabs')?>
				<?php
					/************************ GENERATE CURRENT PAGE TABLE ************************/
					echo get_table($table_data, $total_rows);
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
			window.location.href = app_base_url+'index.php/management/b2b_balance_manager/'+_request_type;
		} else {
			location.reload();
		}
	});
	//Get Branch based on Bank -- Balu A
	$('#bank_id').change(function(){
		var bank_id = $(this).val().trim();
		if(bank_id!="" && isNaN(bank_id) == false && parseInt(bank_id) >= 1) {
			$('#bank').val($('#bank_id option:selected').text());
			$.get(app_base_url+'index.php/ajax/get_bank_branches/'+bank_id, function(response){
				var bank_branch = '';
				if(response.status == true) {
					bank_branch = response.branch;
					account_number = response.account_number;
				}
				$('#branch').val(bank_branch);
				$('#account_number').val(account_number);
				
			});
		} else {
			$('#branch').val('');
			$('#account_number').val('');
			$('#bank').val('');
		}
	});
	
});
</script>
<!-- HTML END -->
<?php 
function get_table($table_data='', $total_rows)
{
	$pagination = '<div class="pull-left">'.$GLOBALS['CI']->pagination->create_links().' <span class="">Total '.$total_rows.' Request found</span></div>';

	$table = '';
	$table .= '<div id="tableList" class="clearfix table-responsive">';
	$table .= $pagination;
	$table .='<div class="table-responsive col-md-12">
   <table class="table table-hover table-striped table-bordered table-condensed" id="balance_request_table">';
      $table .= '<thead><tr>
   <th>Sno</th>
   <th>System Transaction</th>
   <th>Mode Of Payment</th>
   <th>Amount</th>
   <th>Bank</th>
   <th>Branch</th>
   <th>Status</th>
   <th>Bank Deposit Slip</th>
   <th>Request Sent On</th>
   <th>Update Remarks</th>
   </tr></thead><tbody>';
	if (valid_array($table_data) == true) {
		$segment_3 = $GLOBALS['CI']->uri->segment(3);
		$current_record = 0;
		foreach ($table_data as $k => $v) {
			$current_request_status = strtoupper($v['status']);
			$table .= '<tr>
			<td>'.++$current_record.'</td>
			<td>'.$v['system_transaction_id'].'</td>
			<td>'.get_enum_list('provab_balance_requests', strtoupper($v['transaction_type'])).'</td>
			<td>'.$v['amount'].'</td>
			<td>'.$v['bank'].'</td>
			<td>'.$v['branch'].'</td>
			<td><span class="label '.balance_status_label($current_request_status).'">'.$current_request_status.'</span></td>';
			if($v['image'] != ''){
				$table .='<td><img src="'.$GLOBALS ['CI']->template->domain_images('deposit_slips/'.$v['image']).'" height="100px" width="120px" class="img-thumbnail"></td>';
				//<td><a href="'.$GLOBALS['CI']->template->file_full_path ('deposit_slips/'.$v['image']).'" target="_blank">Click to view</a></td>';
			}else{
				$table .='<td>Not Available</td>';
			}
			$table.='<td>'.app_friendly_absolute_date($v['created_datetime']).'</td>
			<td>'.$v['update_remarks'].'<br>'.app_friendly_absolute_date($v['updated_datetime']).'</td>
	</tr>';
		}
	} else {
		$table .= '<tr><td>---</td><td>---</td><td>---</td><td>---</td><td>---</td><td>---</td><td>---</td><td>---</td><td>---</td><td>---</td></tr>';
	}
	$table .= '</tbody></table></div>';
	return $table;
}
?>
<script>
$(document).ready(function() {
    $('#balance_request_table').DataTable({
        // Disable initial sort 
        "aaSorting": []
    });
});
</script>
