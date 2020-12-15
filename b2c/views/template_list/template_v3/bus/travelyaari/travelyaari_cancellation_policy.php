<div class="panel panel-danger cancellation-wrapper">
	<div class="panel-heading">
		<div class="panel-title">
			Cancellation Policy
		</div>
	</div>
	<div class="panel-body">
		<table class="table table-condensed table-bordered table-striped">
			<tr>
				<th>Cancellation Time</th>
				<th>Cancellation Charges</th>
			</tr>
		<?php
		// debug($CUR_CancellationCharges);exit;
		if (valid_array($CUR_CancellationCharges) == true) {
			
			foreach ($CUR_CancellationCharges as $__ck => $__cv) {
				$unit = "%";
				$hour = $__cv['Mins']/60;
				if($__ck !=0 && $__cv['Mins'] == $CUR_CancellationCharges[$__ck-1]['Mins']){
					$min_label = ' Departure Time > '.$hour;
				} else {
					$min_label = $hour.' Hours Before Departure Time';
				}
				if(trim($__cv['Pct'])=="No Cancellation")
					$unit = "";
					
			?>
				<tr>
					<td><?=$min_label?></td>
					<td><?=(empty($__cv['Amt']) == false ? $__cv['ChargeFixed'] : $__cv['Pct'].$unit)?></td>
				</tr>
			<?php
			}
		} else {
		?>
			<tr>
				<td colspan="2">Not Available</td>
			</tr>
		<?php
		}
		?>
		</table>
	</div>
</div>
