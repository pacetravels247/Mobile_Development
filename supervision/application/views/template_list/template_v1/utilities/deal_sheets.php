<script src="<?php echo SYSTEM_RESOURCE_LIBRARY?>/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo SYSTEM_RESOURCE_LIBRARY?>/datatables/dataTables.bootstrap.min.js"></script>
<!-- <script src="<?php echo SYSTEM_RESOURCE_LIBRARY?>/datatables/dataTables.bootstrap.css"></script>
<script src="<?php echo SYSTEM_RESOURCE_LIBRARY?>/datatables/jquery.dataTables.min.css"></script>
<script src="<?php echo SYSTEM_RESOURCE_LIBRARY?>/datatables/jquery.dataTables.css"></script>
 bootstrap.min.css-->
<div class="panel panel-primary clearfix">
	<div class="panel panel-heading">Bus Commission</div>
	<div class="panel-body">
		<div class="table-responsive">
			<form action="" method="POST" autocomplete="off">
				<table
					class="table table-hover table-striped table-bordered table-condensed">
					<thead>
						<tr>
							<th>Bus Commission</th>
							<?php if(@$bus_deals[0]['value_type'] == 'percentage'){
								$value = '%';
							}else{
								$value = 'INR';
							}?>
							<th><?=@$bus_deals[0]['value'].@$value?></th>
						</tr>
					</thead>
				</table>
			</form>
		</div>
	</div>
</div>
<div class="panel panel-primary clearfix">
	<div class="panel panel-heading">Airline Deal Sheet</div>
	<div class="panel-body">
		<div class="table-responsive">
			<form action="" method="POST" autocomplete="off">
				<table id="sort_table"
					class="table table-hover table-striped table-bordered table-condensed">
					<thead>
						<tr>
							<th>Sl No</th>
							<th>Airline</th>
							<th>Code</th>
							<th>Business</th>
							<th>Economy</th>
							<th>Import Fee</th>
						</tr>
					</thead>
					<tbody>
					<?php 
					//debug($flight_deals);exit;
					foreach ( $flight_deals as $key => $value ) {
					?>
					<tr>
						<td><?=$key+1?>
						<td><?=@$value['name']?></td>
						<td><?=@$value['code']?></td>
						<td><?=@$value['business']?></td>
						<td><?=@$value['economy']?></td>
						<td><?=@$value['import_fee']?></td>
						
					</tr>
					<?php } ?>
					</tbody>
				</table>
			</form>
		</div>
	</div>
</div>
<script>
$(document).ready(function() {
    $('#sort_table').DataTable();
} );
</script>