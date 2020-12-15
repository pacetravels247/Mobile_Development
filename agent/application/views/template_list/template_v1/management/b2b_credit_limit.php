<script src="<?php echo SYSTEM_RESOURCE_LIBRARY?>/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo SYSTEM_RESOURCE_LIBRARY?>/datatables/dataTables.bootstrap.min.js"></script>
<!-- HTML BEGIN -->
<?php

$url =$this->uri->segment(3);
if($url != null ){
	$tab1 = " active ";
	$tab2 = "";
}else{

	$tab1 = " active ";
	$tab2 = "";
}
if (is_array($search_params)) {
	extract($search_params);
}
$_datepicker = array(array('created_datetime_from', PAST_DATE), array('created_datetime_to', PAST_DATE));
$this->current_page->set_datepicker($_datepicker);
$this->current_page->auto_adjust_datepicker(array(array('created_datetime_from', 'created_datetime_to')));
	if($_GET["transaction_type"]=="Instant_Recharge")
	{
		$tab_name1="New Instant Recharge";
		$tab_name2="Instant Recharge List";
	}
	else
	{
		$tab_name1="New Credit Limit Request";
		$tab_name2="Sent Credit Requests";
	}
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
							<?php echo $tab_name1; ?>
						</a>
					</li>
					<li role="presentation" class="<?php echo $tab2; ?>">
						<a href="#tableList" aria-controls="profile" role="tab" data-toggle="tab">
						<i class="fa fa-money"></i>
							<?php echo $tab_name2; ?>
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
               		<div class="clearfix"></div>
                 
					<div class="section_deposite">
					<?php
						/************************ GENERATE CURRENT PAGE FORM ************************/
						echo $balance_page_obj->generate_form('credit_request_form', $form_data);
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
					<input type="hidden" name="transaction_type" 
					value="<?php echo $_GET["transaction_type"]; ?>">
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
				<a href="<?php echo base_url(); ?>index.php/management/b2b_balance_manager" id="clear-filter" class="btn btn-primary">ClearFilter</a>
				</div>
			</form>
		</div>
            	<div class="col-md-12" style="margin-bottom: 15px; padding-left: 30px;"><strong>Current Balance</strong> : <strong><?php $balance = agent_current_application_balance(); echo agent_base_currency().' '.$balance['value']?>/-</strong></div>
				<?php //echo $GLOBALS['CI']->template->isolated_view('report/report_tabs'); ?>
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
function get_table($table_data='')
{
	$table = '
   <div class="table-responsive col-md-12">
   <table class="table table-hover table-striped table-bordered table-condensed" id="balance_request_table">';
      $table .= '<thead><tr>
   <th>Sno</th>
   <th>System Transaction</th>
   <th>Mode Of Payment</th>
   <th>Amount</th>
 	<th>Status</th>
   <th>Bank Deposit Slip</th>
   <th>Request Sent On</th>
   <th>Update Remarks</th>
   </tr></thead><tbody>';
	if (valid_array($table_data) == true) {
		foreach ($table_data as $k => $v) {
			$current_request_status = strtoupper($v['status']);
			$table .= '<tr>
			<td>'.($k+1).'</td>
			<td>'.$v['system_transaction_id'].'</td>
			<td>'.$v['transaction_type'].'</td>
			<td>'.$v['amount'].'</td>
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
		$table .= '<tr><td>---</td><td>---</td><td>---</td><td>---</td><td>---</td><td>---</td><td>---</td><td>---</td></tr>';
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
    $("#credit_request_form").submit(function(e){
		var paymentMethod = $("input[name='selected_pm']:checked").val();
		if(paymentMethod == 'PAYTM_DC'){
			if(parseInt($(".ins_amt").val())>2000)
			{
				e.preventDefault();
				alert("System allows within 2000/- amount for Instant recharge"); 
				return false; 
			}
		}		
    	if($("#bank_code").val() == "0")
    	{
    		e.preventDefault();
    		alert("Please select the bank.");
    		return false;
    	}
    });
    $("#amount").keyup(function(){
    	shoConFees();
    });
    $(".selected_pm").click(function(){
    	shoConFees();
    });
     $(document).on("change", "#bank_code", function(){
    	shoConFees();
    });
    function showBankList(selected_radio)
    {
    	$.ajax({
				url: app_base_url+"index.php/utilities/get_bank_list_options",
				type: "POST",
				dataType: "html",
				async: false,
				success: function(bank_list)
				{
					$(bank_list).insertAfter(selected_radio);
				}
			});
    }
	function shoConFees()
	{
		var amount = $("input[name='amount']").val();
		var selected_radio = $("input[name='selected_pm']:checked");
		var selected_pm = selected_radio.val();
		var bank_code = $("#bank_code").val();
		$(".con_fees_section").remove();
		if((amount.trim() > 0) && (selected_pm!=""))
		{
			if(selected_pm == "TECHP" && bank_code == undefined)
			{
				showBankList(selected_radio);
				return false;
			}
			if(selected_pm != "TECHP")
			{
				bank_code = 0;
				$("#bank_code").remove();
			}
			var pm_arr = selected_pm.split("_");
			var pm = pm_arr[0];
			var method = pm_arr[1];
			var data = "amount="+amount+"&selected_pm="+pm+"&method="+method+"&bank_code="+bank_code;
			$.ajax({
				url: app_base_url+"index.php/utilities/get_instant_recharge_convenience_fees/1",
				type: "POST",
				data: data,
				dataType: "JSON",
				async: false,
				success: function(data)
				{
					$("<p class='con_fees_section'>Convenience Fees: "+data["cf"]+", Total: "+data["total"]+"</p>").insertAfter("#amount");
					$("#convenience_fees").val(data["cf"]);
				}
			});
		}
	}
});
</script>
