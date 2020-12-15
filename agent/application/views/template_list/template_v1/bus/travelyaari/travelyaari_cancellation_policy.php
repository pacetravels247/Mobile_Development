<div class="panel panel-danger">
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
			foreach ($CUR_CancellationCharges as $__ck => $__cv) { ?>
				<tr>
					<td><?=$__cv['MinsBeforeDeparture']?> mins Before Departure Time</td>
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