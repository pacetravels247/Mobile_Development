<?php
if (is_array($search_params)) {
	extract($search_params);
}
$param = $this->uri->segment (3);
$_datepicker = array(array('created_datetime_from', PAST_DATE), array('created_datetime_to', PAST_DATE));
$this->current_page->set_datepicker($_datepicker);
$this->current_page->auto_adjust_datepicker(array(array('created_datetime_from', 'created_datetime_to')));
?>
<!-- HTML BEGIN -->
<div class="bodyContent">
	<div class="panel panel-default clearfix"><!-- PANEL WRAP START -->
		<div class="panel-heading"><!-- PANEL HEAD START -->
			<!-- <div class="panel-title"><i class="fa fa-shield"></i> Suppliers Booking</div> -->
			<div class="panel_custom_heading"><!-- PANEL HEAD START -->
			<div class="panel_title">
				<ul id="myTab" role="tablist" class="nav nav-tabs b2b_navul">
					<li class="<?=($param == 'flight')?'active' : ''?>" role="presentation">
						<a  href="<?=base_url()?>index.php/report/bookings_list/flight"> Flight </a>
					</li>
					<li class="<?=($param == 'hotel')?'active' : ''?>" role="presentation">
						<a  href="<?=base_url()?>index.php/report/bookings_list/hotel"> Hotel </a>
					</li>
					<li class="<?=($param == 'bus')?'active' : ''?>" role="presentation">
						<a  href="<?=base_url()?>index.php/report/bookings_list/bus"> Bus </a>
					</li>
				</ul>
			</div>
		</div>
		</div>
		<!-- PANEL HEAD START -->
		<div class="panel-body">
			<h4>Search Panel
			</h4>
			<form method="GET" autocomplete="off">
				<div class="clearfix form-group">
					
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
					<div class="col-xs-4">
						<label>
						Suppliers
						</label>
						<select class="form-control" name="supplier">
							<option value="">All</option>
                       		<?php foreach($list_data as $v){ ?>
							<option value="<?=$v['source_id']?>" <?php if($_GET['supplier'] == $v['source_id'])echo 'selected'; ?> > <?=$v['name']?></option>
							<?php } ?>
						</select>
					</div>
					<div class="col-xs-4">
						<label>
						Booking Status
						</label>
						<select class="form-control" name="status">
							<option value="">All</option>
                       		<?=generate_options(get_enum_list('report_filter_status'),(array)@$_GET['status']);?>
						</select>
					</div>
				</div>
				<div class="col-sm-12 well well-sm">
				<input type="hidden" name="module" value="<?=$param?>" />
				<button type="submit" class="btn btn-primary">Search</button> 
				<button type="reset" class="btn btn-warning">Reset</button> 
				<a href="<?php echo base_url().'index.php/report/bookings_list/flight'?>" id="clear-filter" class="btn btn-primary">ClearFilter</a>
				</div>
			</form>
		</div>
		<div class="panel-body"><!-- PANEL BODY START -->
			<div class="">
				<!-- // <?php //echo $this->pagination->create_links();?> <span class=""> Total <?php //echo $total_rows ?> Records</span> -->
			</div>
			<div class="table-responsive">
			<table class="table table-striped">
				<tr>
					<th>Sl. No.</th>
					<th>Suppliers</th>
					<th>Seats</th>
					<th>Total Price</th>
					<th>Total Commission</th>
					<th>Pace Commission</th>
					<th>Agent Commission</th>
				</tr>
			<?php
			if (valid_array($table_data)) {
				foreach ($table_data as $k => $v) { ?>
					<tr>
						<td><?=$k+1;?></td>
						<td><?=$v['sup_name']?></td>
						<td><?=$v['total_bookings']?></td>
						<td><?=number_format($v['total_price'],2)?></td>
						<td><?= number_format($v['admin_comm'],2)?></td>
						<td><?= number_format(($v['admin_comm']-$v['agent_comm']),2)?></td>
						<td><?= number_format($v['agent_comm'],2)?></td>
					</tr>
				<?php
				}
			} else {
				echo '<tr><td colspan="3">No Data Found</td></tr>';
			}
			?>
			</table>
			</div>
		</div><!-- PANEL BODY END -->
	</div><!-- PANEL END -->
</div>