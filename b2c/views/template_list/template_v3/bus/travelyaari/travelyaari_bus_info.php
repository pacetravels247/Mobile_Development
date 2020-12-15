<?php
$fare_details = $currency_obj->get_currency($details['Fare']);
?>
<table class="table table-striped">
	<tr>
		<th>Bus Operator</th>
		<td><?=$details['CompanyName']?></td>
	</tr>
	<tr>
		<th>Bus</th>
		<td><?=$details['BusTypeName']?></td>
	</tr>
	<tr>
		<th>Mobile Ticket</th>
		<td><?=((empty($details['IsMTicketAllowed']) == true or $details['IsMTicketAllowed'] == 'false') ? 'No' : 'Yes')?></td>
	</tr>
	<tr>
		<th>Available Seats</th>
		<td><?=$details['AvailableSeats']?></td>
	</tr>
	<tr>
		<th>Bus Type</th>
		<td><?=$GLOBALS['CI']->bus_lib->get_bus_type($details['HasAC'], $details['HasNAC'], $details['HasSeater'], $details['HasSleeper'], $details['IsVolvo'])?></td>
	</tr>
	<tr>
		<th>Departure</th>
		<td><?=local_date($details['DepartureTime'])?></td>
	</tr>
	<tr>
		<th>Arrival</th>
		<td><?=local_date($details['ArrivalTime'])?></td>
	</tr>
	<tr>
		<th>Total Duration</th>
		<td><?=get_time_duration_label($details['DepartureTime'], $details['ArrivalTime'])?></td>
	</tr>
	<tr>
		<th>Fare</th>
		<td><?=$fare_details['default_currency'].' '.$fare_details['default_value']?></td>
	</tr>
	<tr>
		<th>Bus Number</th>
		<td><?=$details['BusNumber']?></td>
	</tr>
</table>