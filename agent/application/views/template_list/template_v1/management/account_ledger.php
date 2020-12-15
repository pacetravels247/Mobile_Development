<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
<?php
if (is_array($search_params)) {
	extract($search_params);
}
$_datepicker = array(array('created_datetime_from', PAST_DATE), array('created_datetime_to', PAST_DATE));
$this->current_page->set_datepicker($_datepicker);
$this->current_page->auto_adjust_datepicker(array(array('created_datetime_from', 'created_datetime_to')));
?>
<!-- HTML BEGIN -->
<div class="bodyContent">
	<div class="panel panel-default clearfix"><!-- PANEL WRAP START -->
			<div class="panel-heading"><!-- PANEL HEAD START -->
				<div class="panel-title"><h3>Account Statement</h3>
				</div>
			</div>
			<!-- PANEL HEAD START -->	
			<div class="panel-body">
			<h4>Search Panel</h4>
			<hr>
			<form method="GET" autocomplete="off">
				<div class="clearfix form-group">
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
				</div>
				<div class="col-sm-12 well well-sm">
					<button type="submit" class="btn btn-primary">Search</button> 
					<button type="reset" class="btn btn-warning">Reset</button> 
					<a href="<?php echo base_url().'index.php/management/account_ledger'?>" id="clear-filter" class="btn btn-primary">ClearFilter</a>
				</div>
			</form>
			</div>

			<?php if($total_records > 0): ?>
			<a href="<?php echo base_url(); ?>index.php/management/export_account_ledger/excel<?= !empty($_SERVER["QUERY_STRING"])?'?'.$_SERVER["QUERY_STRING"]:''?>">
					<button class="btn btn-primary" type="button"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export to Excel</button>
			</a> 
			<a href="<?php echo base_url(); ?>index.php/management/export_account_ledger/pdf<?= !empty($_SERVER["QUERY_STRING"])?'?'.$_SERVER["QUERY_STRING"]:''?>" target="_blank">
					<button class="btn btn-primary" type="button"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF</button>
			</a>
			<?php endif; ?>
			<div class="panel-body"><!-- PANEL BODY START -->
				<div class="">
					<?php //echo $this->pagination->create_links();?>
					<div><?php echo 'Total <strong>'.$total_records.'</strong> transaction found.'?></div>
				</div>
			<div class="table-responsive">
				<div class="pull-right"><b>Opening balance</b> : <?=$table_data[0]['opening_balance']?></div>
			<table id="example" class="table table-condensed table-bordered table-striped">
			<thead>	
				<tr>
					<th>Sl. No.</th>
					<th>Date</th>
					<th>Reference Number</th>
					<th>Description</th>
					<th>Debit</th>
					<th>Credit</th>
					<th>Commision</th>
					<th>TDS</th>
					<?php if(false){ ?>
						<th>Credit Limit Updated</th>
						<th>Current Credit Limit</th>
						<th>Available balance</th>
						<th>Opening Balance</th>
					<?php } ?>
					<th>Closing Balance</th>
				</tr>
			</thead>	
			<tbody>
				<?php
				$segment_3 = $GLOBALS['CI']->uri->segment(3);
				$current_record = (empty($segment_3) ? 1 : $segment_3+1);
				$agent_commission = 0;
				$agent_tds = 0;
				$agent_seats = 0;
				$sale_amt = 0;
					if(valid_array($table_data) == true){ 
						$i=0;
						foreach($table_data as $k => $v){
						$agent_commission += $v['agent_commission'];
						$agent_tds += $v['agent_tds'];
						$sale_amt += $v['credit_amount'];
							$avail_balance = $v['closing_balance']+$v['current_credit_limit'];
							?>
							<tr>
								<td><?=($re = $current_record++)?></td>
								<td><?=app_friendly_datetime($v['transaction_date'])?></td>
								<td><?=$v['reference_number']?></td>
								<td><strong><?=$v['description']?></strong>
									<br>
									<small><?php //echo $v['transaction_details']; ?></small>
								</td>
								<td><?=(empty($v['debit_amount']) == false ? $v['debit_amount'] : '-')?></td>
								<td><?=(empty($v['credit_amount']) == false ? $v['credit_amount']: '-')?></td>
								<td><?=$v['agent_commission']?></td>
								<td><?=$v['agent_tds']?></td>
								<?php if(false){ ?> 	
									<td><?=$v['credit_limit']?></td>
									<td><?=$v['current_credit_limit']?></td>
									<td><?=$avail_balance?></td>
									<td><?=$v['opening_balance']?></td>
								<?php } ?>
								<td><?=$v['closing_balance']?></td>
							</tr>
					<?php $i++;
						 } ?>
				<?php }	else{ ?>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						
					</tr>
				<?php }?>
				</tbody>
			</table>
			</div>
			<div class="">
				<?php //echo $this->pagination->create_links();?> 
			</div>
			<table class="table table-condensed table-bordered table-striped" style="width: 35%;">
			<thead>	
				<tr>
					<th colspan="2">Summary</th>
					
				</tr>
			</thead>	
			<tbody>
				<tr>
					<td>Total Sale</td>
					<td><?php echo $sale_amt; ?></td>
				</tr>
				<tr>
					<td>Total Commision Earned</td>
					<td><?php echo $agent_commission; ?></td>
				</tr>
				<tr>
					<td>Total TDS Deducted</td>
					<td><?php echo $agent_tds; ?></td>
				</tr>
			</tbody>
		</table>

		</div><!-- PANEL BODY END -->
	</div><!-- PANEL END -->
</div>
<script type="text/javascript">
	$(document).ready(function() {
	    $('#example').DataTable();
	});
</script>
