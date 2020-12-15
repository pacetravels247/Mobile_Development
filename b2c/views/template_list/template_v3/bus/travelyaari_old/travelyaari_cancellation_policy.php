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
		if (valid_array($CUR_CancellationCharges) == true) {
			foreach ($CUR_CancellationCharges as $__ck => $__cv) {
				if($__ck !=0 && $__cv['MinsBeforeDeparture'] == $CUR_CancellationCharges[$__ck-1]['MinsBeforeDeparture']){
					$min_label = ' Departure Time > '.$__cv['MinsBeforeDeparture'];
				} else {
					$min_label = $__cv['MinsBeforeDeparture'].' mins Before Departure Time';
				}
			?>
				<tr>
					<td><?=$min_label?></td>
					<td><?=(empty($__cv['ChargeFixed']) == false ? $__cv['ChargeFixed'] : $__cv['ChargePercentage'].'%')?></td>
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
