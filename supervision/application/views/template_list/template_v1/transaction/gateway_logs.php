<?php 
if (is_array($search_params)) {
	extract($search_params);
}
$_datepicker = array(array('created_datetime_from', PAST_DATE), array('created_datetime_to', PAST_DATE));
$this->current_page->set_datepicker($_datepicker);
$this->current_page->auto_adjust_datepicker_report(array(array('created_datetime_from', 'created_datetime_to')));
if(isset($_GET["refund_list"]) && $_GET["refund_list"] == 1)
{
	$refund_list = 1;
	$tab_name = "Refund";
}
else{
	$refund_list = 0;
	$tab_name = "Inward";
}
?>
<!-- HTML BEGIN -->
<div class="bodyContent">
	<div class="panel panel-default clearfix"><!-- PANEL WRAP START -->
		<div class="panel-heading"><!-- PANEL HEAD START -->
			<div class="panel-title"><i class="fa fa-shield"></i> Payment Gateway Transaction (<?php echo $tab_name; ?>)</div>
		</div>
		<!-- PANEL HEAD START -->
		<div class="panel-body">
			<h4>Search Panel
			</h4>
			<form method="GET" autocomplete="off" action="<?php echo base_url("transaction/gateway_logs"); ?>">
			<input type="hidden" name="refund_list" value="<?php echo $refund_list; ?>">
				<div class="clearfix form-group">
                    <div class="col-md-4 ">
                        <label>Supplier ID</label>
                        <select class="form-control" name="supplier_id">
                            <option value="">Select</option>            
                            <?= generate_options($supplier_list, (array) @$supplier_id) ?>                   
                        </select> 
                    </div>   
					<div class="col-xs-4">
						<label>
							Agent
						</label>
						<select class="form-control select2" name="agent_id">
							<option value="">All</option>
							<?php
								foreach ($agency_list as $key => $value) {
							?>
							<option value="<?=$value['user_id'];?>" <?php if(isset($_GET['agent_id']) && $value['user_id'] == @$agent_id) { echo 'selected';}?>><?=$value['agency_name']." - ".provab_decrypt($value['uuid']);?></option>
							<?php } ?>
						</select>
					</div>
					<div class="col-xs-4">
						<label>
						Transaction Type
						</label>
						<select class="form-control" name="transaction_type">
							<option value="">All</option>
							<?=generate_options(get_enum_list('transaction_type'), array(@$transaction_type))?>
						</select>
					</div>
					<div class="col-xs-4">
						<label>
						Payment Gateway
						</label>
						<select class="form-control" name="pg_name">
							<option value="">All</option>
							<?=generate_options(get_enum_list('pg_list'), array(@$pg_name))?>
						</select>
					</div>
					<div class="col-xs-4">
						<label>
						Reference Number
						</label>
						<input type="text" class="form-control" name="app_reference" value="<?=@$app_reference?>" placeholder="Reference Number">
					</div>
					<div class="col-xs-4">
						<label>
						From Date
						</label>
						<input type="text" readonly id="created_datetime_from" class="form-control" name="created_datetime_from" value="<?=@$created_datetime_from?>" placeholder="Request Date">
					</div>
					<div class="col-xs-4">
						<label>
						To Date
						</label>
						<input type="text" readonly id="created_datetime_to" class="form-control disable-date-auto-update" name="created_datetime_to" value="<?=@$created_datetime_to?>" placeholder="Request Date">
					</div>
				</div>
				<div class="col-sm-12 well well-sm">
					<button type="submit" class="btn btn-primary">Search</button> 
					<input type="submit" name="excel_export" class="btn btn-primary" value="Excel Export">
					<button type="reset" class="btn btn-warning">Reset</button>
					<a href="<?php echo base_url().'index.php/transaction/gateway_logs'?>" id="clear-filter" class="btn btn-primary">ClearFilter</a>
				</div>
			</form>
		</div>
		<div class="panel-body"><!-- PANEL BODY START -->
			<div class="">
				<?php echo $this->pagination->create_links();?> <span class="">Total <?php echo $total_rows ?> Records</span>
			</div>
			<div class="table-responsive">
		


	<div class="datatables-res">
		<table class="table datatables-td table-striped" >
		    <thead>
		        <tr>
		            <th>Sl. No.</th>
					<th>Agent</th>
					<th>Transaction Date</th>
					<th>Reference Number</th>
					<th>Amount</th>
					<th>Admin Charges</th>
					<th>Status</th>
					<th>PG Txn Id</th>
					<th>Refund Id</th>
					<th>Message</th>
					<th>Bank Txn Id</th>	
					<th>Bank Name</th>
					<th>PG Name</th>
					<th>Payment Mode</th>
					<td>Action</td>
		        </tr>
		    </thead>
		    <tbody>
		    	<?php
			if (valid_array($table_data)) {
				foreach ($table_data as $k => $v) {
					$flag = 0;
					if(!$refund_list){
						$rp = json_decode($v['response_params'], true);
					}
					
					if($refund_list)
						$rp = json_decode($v['refund_params'], true);
					$rqp = json_decode($v['request_params'], true);
					
					if(isset($v['app_reference']) && !empty($v['app_reference'])){
						$appReference = explode('-',$v['app_reference']);
						if (strpos($appReference[0], "BB") > -1){
							$flag = 1;
						}else if (strpos($appReference[0], "FB") > -1){
							$flag = 1;
						}else if (strpos($appReference[0], "HB") > -1){
							$flag = 1;
						}else if (strpos($appReference[0], "PB") > -1){
							$flag = 1;
						}else if (strpos($appReference[0], "CB") > -1){
							$flag = 1;
						}else if (strpos($appReference[0], "TECHP") > -1){
							$flag = 1;
						}
					}
					
				?>
		        <tr>
		            <td><?= ($k+1) ?></td>
					<td><?= $v['agency_name'] ?></td>
					<td><?= app_friendly_date($v['created_datetime']) ?></td>
					<td><?= $v['app_reference'] ?></td>
					<th><?= $v['amount'] ?></th>		
					<td><?= @$rqp["convenience_amount"] ?></td>		
					<td><?= @$rp["txn_status"] ?></td>
					<td><?= @$rp["pg_txn_id"] ?></td>
		            <td><?= @$rp["pg_refund_id"] ?></td>
					<td><?= @$rp["txn_msg"] ?></td>
		            <td><?= @$rp["bank_txn_id"] ?></td>
					<td><?= @$rp["bank_name"] ?></td>
					<td><?= @$v["pg_name"] ?></td>
					<td><?= @$v["payment_mode"] ?></td>
					<td>
						<?php if($flag == 0 && $rp["txn_status"] != 'TXN_SUCCESS'){ ?>
							<span class="label label-success check_status" data-orderId="<?php echo $v['app_reference']; ?>">Check Status</span>
							 <!-- <label for="check_status">Check Status</label> -->
							<!-- <span class="check_status label label_new" style="cursor: pointer;">Check Status</span> -->
						<?php } ?>
					</td>
		        </tr>
		        <?php
				}
			} else { ?>
				<td><?= ($k+1) ?></td>
					<td><?= $v['agency_name'] ?></td>
					<td><?= app_friendly_date($v['created_datetime']) ?></td>
					<td><?= $v['app_reference'] ?></td>
					<th><?= $v['amount'] ?></th>		
					<td><?= @$rqp["convenience_amount"] ?></td>		
					<td><?= @$rp["txn_status"] ?></td>
					<td><?= @$rp["pg_txn_id"] ?></td>
		            <td><?= @$rp["pg_refund_id"] ?></td>
					<td><?= @$rp["txn_msg"] ?></td>
		            <td><?= @$rp["bank_txn_id"] ?></td>
					<td><?= @$rp["bank_name"] ?></td>
					<td><?= @$v["pg_name"] ?></td>
					<td><?= @$v["payment_mode"] ?></td>
					<td><span class="check_status" style="cursor: pointer;">Check Status</span></td>
			<?php }
			?>
		    </tbody>
		</table>
	</div>


			</div>
		</div><!-- PANEL BODY END -->
	</div><!-- PANEL END -->
</div>
<input type="hidden" id="base_url" value="<?php echo base_url(); ?>">



<script type="text/javascript">
	$(document).ready( function () {
	    $('.datatables-td').DataTable();
		$(".check_status").click(function(){
		  let ORDERID = $(this).attr('data-orderId');
		  let baseurl = $('#base_url').val();
		  let from_date = $('#created_datetime_from').val();
		  let to_date = $('#created_datetime_to').val();
		  $.ajax({
		   	url: baseurl+'transaction/Check_payment_status',
		   	data: {ORDERID:ORDERID},
		   	type: 'post',
		   	success: function(data){
		   		if(data == 'TXN_SUCCESS')
		   		{
		            alert("Recharge Done Successfully....."+data);
		   			window.location = baseurl+'transaction/gateway_logs?refund_list=0&supplier_id=&agent_id=&transaction_type=&pg_name=&app_reference=&created_datetime_from='+from_date+'&created_datetime_to='+to_date+'';
		   		}else{
		   			alert("Not Added....."+data);
		   		}				
		   	}
		   });
		});
	});
</script>
<style type="text/css">
	.dataTables_wrapper .col-sm-12{
		min-height: .01%!important;
    	overflow-x: auto!important;
	}
</style>
