<!-- HTML BEGIN -->
<div class="bodyContent">
	<div class="row">
	<div class="col-md-2">
		<a class="btn btn-primary" href="<?php echo base_url("index.php/management/xml_logs/"); ?>">Direct Apis</a>
	</div>
	<div class="col-md-2">
		<a class="btn btn-primary" href="<?php echo base_url("index.php/management/xml_flight_logs/"); ?>">Service Apis</a>
	</div> 
	</div>
	<div class="panel panel-default clearfix"><!-- PANEL WRAP START -->
		<div class="panel-heading"><!-- PANEL HEAD START -->
			<div class="panel-title"><i class="fa fa-shield"></i> XML Logs</div>
			<div class="pull-right">
			<?php if($db=="normal") { ?>			
			<a href="<?php echo base_url("index.php/management/flush_xml_logs"); ?>" 
			class="btn btn-primary">
			<i class="fa fa-trash"></i> Flush logs older than 30 days</div>
			</a>
			<?php } ?>
			<?php if($db=="service") { ?>			
			<a href="<?php echo base_url("index.php/management/flush_xml_flight_logs"); ?>" 
			class="btn btn-primary">
			<i class="fa fa-trash"></i> Flush logs older than 30 days</div>
			</a>
			<?php } ?>
		</div>
		<!-- PANEL HEAD START -->
		<div class="panel-body"><!-- PANEL BODY START -->
			<div class="">
				<?php echo $this->pagination->create_links();?> <span class="">Total <?php echo $total_rows ?> Records</span>
			</div>
			<div class="table-responsive">
			<table class="table table-striped">
				<tr>
					<th>Sno</th>
					<th>Origin</th>
					<?php if($db=="normal") { ?>
					<th>Request / Response</th>
					<?php } ?>
					<?php if($db=="service") { ?>
					<th>Request</th>
					<th>Response</th>
					<?php } ?>
					<th>Module</th>
					<th>Time</th>
					<th>Actions</th>
				</tr>
			<?php
			if (valid_array($table_data)) {
				foreach ($table_data as $k => $v) {	
					if($db=="service")
						$v['module']="flight";
				?>
					<tr>
						<td><?=($k+1)?></td>
						<td><?=($v['origin'])?></td>
						<?php if($db=="service") { ?>
						<td><?=htmlentities(substr($v['request'], 0, 30));?></td>
						<?php } ?>
						<td><?=htmlentities(substr($v['test'], 0, 30));?></td>
						<td><?=$v['module']?></td>
						<td><?=$v['time']?></td>
						<?php if($db=="normal") { ?>
						<td><a href="<?php echo base_url("index.php/management/download_xml_log/".$v['origin']); ?>" class="btn btn-primary">Download</a></td>
						<?php } ?>
						<?php if($db=="service") { ?>
						<td><a href="<?php echo base_url("index.php/management/download_xml_flight_log/".$v['origin']); ?>" class="btn btn-primary">Download</a></td>
						<?php } ?>
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