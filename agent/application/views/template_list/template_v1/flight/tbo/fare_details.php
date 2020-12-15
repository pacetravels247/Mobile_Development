<?php
$flight_segment_fare = force_multple_data_format($fare_rules);
$rules = '';
foreach ($flight_segment_fare as $__fare_key => $__fare_rules) {
	$rules .= '<div class="row">';
		$rules .= '<div class="col-md-12">';
			$rules .= '<div class="seg-sum">';
				$rules .= '<img src="'.SYSTEM_IMAGE_DIR.'airline_logo/'.$__fare_rules['Airline'].'.gif" alt="Flight Image" class="airline-logo" height="15"> ';
				$rules .= '<span>'.($__fare_key+1).')</span> <span class="from-location">'.$__fare_rules['Origin'].' ('.local_date($__fare_rules['DepartureDate']).')</span> <i class="fa fa-arrow-circle-right"></i> <span class="to-location">'.$__fare_rules['Destination'].' ('.local_date($__fare_rules['ReturnDate']).')</span> ';
			$rules .= '</div>';
			$rules .= '<div class="flight-fare-rules panel-body">';
				$rules .= (isset($__fare_rules['FareRuleDetail']) == true ? $__fare_rules['FareRuleDetail'] : 'Not Available.');
			$rules .= '</div>';
		$rules .= '</div>';
	$rules .= '</div>';
}
echo $rules;