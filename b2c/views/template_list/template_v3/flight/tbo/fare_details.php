<?php
$flight_segment_fare = force_multple_data_format($fare_rules);
$rules = '';
$rules .= '<div class="col-xs-12 nopad">';
	$rules .= '<div class="inboundiv splfares">';
	$rules .= '<h4 class="farehdng">Fare Rules</h4>';
foreach ($flight_segment_fare as $__fare_key => $__fare_rules) {
	$rules .= '<div class="flight-fare-rules rowfare">';
		$rules .= '<div class="lablfare">';
			$rules .= $__fare_rules['Origin'].' <span class="fa fa-long-arrow-right"></span> '.$__fare_rules['Destination'];
		$rules .= '</div>';
		$rules .= '<div class="feenotes">';
		$rules .= (isset($__fare_rules['FareRules']) == true ? $__fare_rules['FareRules'] : 'Not Available.');
		$rules .= '</div>';
	$rules .= '</div>';
}
	$rules .= '</div>';
$rules .= '</div>';
echo $rules;