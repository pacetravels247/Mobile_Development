<?php 
$_datepicker = array(array('date_from', PAST_DATE), array('date_to', PAST_DATE));
$this->current_page->set_datepicker($_datepicker);
if(!empty($search_params)){
	if (is_array($search_params)) {
	extract($search_params);
	}	
}
?>
<!--/************************ GENERATE Filter Form ************************/-->
<h4>Search Panel</h4>

<hr>
<form method="GET" autocomplete="off" id="search_filter_form">
	<input type="hidden" name="user_status" value="<?=@$user_status?>" >
	<div class="clearfix form-group">
		<div class="col-xs-4">
			<label>Agent List</label>
			<select class="form-contdol select2" name="uuid">
				<option value="">All</option>
				<?php
					foreach ($user_list as $key => $value) {
				?>
				<option value="<?=provab_decrypt($value['uuid']);?>" <?php if(isset($_GET['uuid']) && provab_decrypt($value['uuid']) == @$uuid) { echo 'selected';}?>><?=$value['agency_name']." - ".provab_decrypt($value['uuid']);?></option>
				<?php } ?>
			</select>
		</div>
		<div class="col-xs-4">
			<label>From Date</label>
			<input type="text" placeholder="Registdation Date" readonly value="<?=@$date_from?>" id="date_from" name="date_from" class="search_filter form-control">
		</div>
		<div class="col-xs-4">
			<label>To Date</label>
			<input type="text" placeholder="Registdation Date" readonly value="<?=@$date_to?>" id="date_to" name="date_to" class="search_filter form-control">
		</div>
	</div>
	<div class="col-sm-12 well well-sm">
		<button class="btn btn-primary" type="submit">Search</button>
		<button class="btn btn-primary" type="submit" value="export_excel" name="export_excel">Export</button> 
		<button class="btn btn-warning" type="reset">Reset</button>
		<a href="<?php echo base_url(); ?>index.php/report/tds_gst_report" id="clear-filter" class="btn btn-primary">ClearFilter</a>
	</div>
</form>
<div class="clearfix"></div>
<div class="panel-body">
<div class="convention">
	<ul class="list-inline">
		<li>F - Flight</li>
		<li>B - Bus</li>
		<li>S - Supplier</li>
		<li>C - Commission</li>
		<li>T - TDS</li>
		<li>A - Agent</li>
		<li>P - Pace</li>
	</ul>
</div>
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Agency Name</th>
				<th>Agency Id</th>
				<th>FSC</th>
				<th>FST</th>
				<th>AC</th>
				<th>AT</th>
				<th>PC</th>
				<th>PT</th>
				<th>GST</th>
				<th>BSC</th>
				<th>BST</th>
				<th>AC</th>
				<th>AT</th>
				<th>PC</th>
				<th>PT</th>
				<th>GST</th>
			</tr>
		</thead>
		<tbody>
		<?php 
			foreach($tds_gst AS $key=>$val) {
				extract($val);	
		?>
			<tr>
				<td><?php echo $agency_name; ?></td>
				<td><?php echo provab_decrypt($uuid); ?></td>
				<td><?=number_format($ftg["admin_commission"], 2)?></td>
				<td><?=number_format($ftg["admin_tds"], 2)?></td>
				<td><?=number_format($ftg["agent_commission"], 2)?></td>
				<td><?=number_format($ftg["agent_tds"], 2)?></td>
				<td><?=number_format($ftg["pace_commission"], 2)?></td>
				<td><?=number_format($ftg["pace_tds"], 2)?></td>
				<td><?=number_format($ftg["gst"], 2)?></td>
				<td><?=number_format($btg["admin_commission"], 2)?></td>
				<td><?=number_format($btg["admin_tds"], 2)?></td>
				<td><?=number_format($btg["agent_commission"], 2)?></td>
				<td><?=number_format($btg["agent_tds"], 2)?></td>
				<td><?=number_format($btg["pace_commission"], 2)?></td>
				<td><?=number_format($btg["pace_tds"], 2)?></td>
				<td><?=number_format($btg["gst"], 2)?></td>
			</tr>
		<?php } ?>	
		</tbody>
	</table>
</div>
