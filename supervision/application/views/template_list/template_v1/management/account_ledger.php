
<?php
if (is_array($search_params)) {
	extract($search_params);
}
extract($agent_details);
$_datepicker = array(array('created_datetime_from', PAST_DATE), array('created_datetime_to', PAST_DATE));
$this->current_page->set_datepicker($_datepicker);
$this->current_page->auto_adjust_datepicker_report(array(array('created_datetime_from', 'created_datetime_to')));
?>
<!-- HTML BEGIN -->
<div class="bodyContent">
	<div class="panel panel-default clearfix"><!-- PANEL WRAP START -->
		
			<!-- PANEL HEAD START -->	
			<div class="panel-body">
			<h4>Search Panel</h4>
			<hr>
			<form method="GET" autocomplete="off">
				<div class="clearfix form-group well well-sm">
					<!-- <div class="col-xs-4">
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
                        </div> -->
					<div class="col-xs-4">
						<label>
						From Date
						</label>
						<input type="text" readonly id="created_datetime_from" class="form-control" name="created_datetime_from" value="<?=@$created_datetime_from?>" placeholder="From Date">
					</div>
					<div class="col-xs-4">
						<label>
						To Date
						</label>
						<input type="text" readonly id="created_datetime_to" class="form-control disable-date-auto-update" name="created_datetime_to" value="<?=@$created_datetime_to?>" placeholder="To Date">
					</div>
					<div class="col-xs-4" style="margin-top: 23px;">
						<button type="submit" class="btn btn-primary">Search</button> 
						<button type="reset" class="btn btn-warning">Reset</button>
						<a href="<?php echo base_url().'index.php/management/account_ledger'?>" id="clear-filter" class="btn btn-primary">ClearFilter</a>
					</div>
				</div>
			</form>			
			</div>
			
			<!-- <?php if($total_records > 0 ): ?>
			<a href="<?php echo base_url(); ?>index.php/management/export_account_ledger/excel<?= !empty($_SERVER["QUERY_STRING"])?'?'.$_SERVER["QUERY_STRING"]:''?>">
					<button class="btn btn-primary" type="button"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export to Excel</button>
			</a> 
			<a href="<?php echo base_url(); ?>index.php/management/export_account_ledger/pdf<?= !empty($_SERVER["QUERY_STRING"])?'?'.$_SERVER["QUERY_STRING"]:''?>" target="_blank">
					<button class="btn btn-primary" type="button"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF</button>
			</a>
			<?php endif; ?> -->
			<div class="panel-body"><!-- PANEL BODY START -->
				<!--  -->
			<div class="table-responsive">
			<table class="datatables-td table table-condensed table-bordered table-striped">
			<thead>	
				<tr>
					<th>Sl. No.</th>
					<th>Date</th>
					<th>Agent Id</th>
					<th>Transaction Type</th>
					<th>Reference Number</th>
					<th>Description</th>
					<th>Debit(-)</th>
					<th>Credit(+)</th>
					<th>Running Balance</th>
				</tr>
			</thead>	
			<tbody>
				<?php
						foreach($table_data as $k => $v){
							$debit_amount = '-';
							$credit_amount = '-';
							if(strpos($v['fare'],"-") > -1){
								$credit_amount = $v['fare'];
							}else{
								$debit_amount = $v['fare'];
							}
							$agency_data = '-';
							?>
							<tr>
								<td><?php echo $k+1; ?></td>
								<td><?php echo app_friendly_date($v['created_datetime']); ?></td>
								<?php foreach ($agency_list as $key1 => $value1) {
								if(trim($v['transaction_owner_id']) == trim($value1['user_id'])){
									$agency_data = provab_decrypt($value1['uuid']).'['.$value1['agency_name'].']';
									break;
								} ?>
								<?php } ?>
								<td><?php echo $agency_data; ?></td>
								<td><?php echo $v['transaction_type']; ?></td>
								<td><?php echo $v['app_reference']; ?></td>
								<td><?php echo $v['remarks']; ?></td>
								<td><?php echo $debit_amount; ?></td>
								<td><?php echo $credit_amount; ?></td>
								<td><?php echo $v['closing_balance']; ?>e</td>
							</tr>
						<?php } ?> 
							
				</tbody>
			</table>
			</div>
			<div class="">
			</div>
		</div><!-- PANEL BODY END -->
	</div><!-- PANEL END -->
</div>
<script type="text/javascript">
$(document).ready( function () {
    $('.datatables-td').DataTable();
    });
</script>
<style type="text/css">
	.dataTables_wrapper .col-sm-12{
		min-height: .01%!important;
    	overflow-x: auto!important;
	}
</style>
