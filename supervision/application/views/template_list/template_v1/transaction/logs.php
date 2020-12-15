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
			<div class="panel-title"><i class="fa fa-shield"></i> Transaction Logs</div>
		</div>
		<!-- PANEL HEAD START -->
		<div class="panel-body">
			<h4>Search Panel
			</h4>
			<form method="GET" autocomplete="off">
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
					<button type="reset" class="btn btn-warning">Reset</button>
					<a href="<?php echo base_url().'index.php/transaction/logs'?>" id="clear-filter" class="btn btn-primary">ClearFilter</a>
				</div>
			</form>
		</div>
		<div class="panel-body"><!-- PANEL BODY START -->
			<div class="">
				<?php echo $this->pagination->create_links();?> <span class="">Total <?php echo $total_rows ?> Records</span>
			</div>
			<div class="table-responsive">
			<table class="table table-striped">
				<tr>
					<th>Sl. No.</th>
					<th>Agent</th>
					<th>Transaction Date</th>
					<th>Reference Number</th>
					<th>Transaction Type</th>
					<th>Amount</th>
					<th>Description</th>
				</tr>
			<?php
			if (valid_array($table_data)) {
				foreach ($table_data as $k => $v) {

					if ($v['transaction_owner_id'] == 0) {
						$user_info = 'Guest';
					} else {
						$user_info = $v['username'];
					}

				?>
					<tr>
						<td><?=($k+1)?></td>
						<td><?=$v['agent_name'].' - '.provab_decrypt($v['uuid']);?></td>
						<td><?=app_friendly_date($v['created_datetime'])?></td>
						<td><?=$v['app_reference']?></td>
						<td><?=ucfirst($v['transaction_type'])?></td>
						<th><?=(abs($v['grand_total'])).'-'.$v['currency']?></th>				
						<td><?=$v['remarks']?></td>						
					</tr>
				<?php
				}
			} else {
				echo '<tr><td>No Data Found</td></tr>';
			}
			?>
			</table>
			</div>
		</div><!-- PANEL BODY END -->
	</div><!-- PANEL END -->
</div>
