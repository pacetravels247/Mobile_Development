<?php
$event_date = '';
$timeline = '';
$total_events = count($list);
$event_loader_attr = '';
foreach ($list as $t_k => $t_v) {
	$current_event_date = date('dS M Y', strtotime($t_v['created_datetime']));
	if ($current_event_date != $event_date) {
		$event_date = $current_event_date;
		if ($event_date == date('dS M Y')) {
			$event_date_label = 'Today('.date('dS M').')';
		} else {
			$event_date_label = $event_date;
		}
		$timeline .= '
			<li class="time-label" id="'.strtotime($current_event_date).'_event_day_header">
				<span class="bg-navy">
				'.$event_date_label.'
				</span>
			</li>';
		//$timeline .= '<li>'..'</li>';
	}
	$t_head = '<strong class="text-blue">'.$t_v['event_title'].'</strong>';
	$t_body = $t_v['event_description'];
	$t_foot = false;
	switch ($t_v['event_origin']) {
		case 'EID001' : //User Registration
			break;
		case 'EID002' : //Profile Update
			break;
		case 'EID003' : //Change Password
			break;
		case 'EID004' : //Email Subscription
			break;
		case 'EID005' : //Login - no body
			$t_head = $t_body;
			$t_body = false;
			break;
		case 'EID006' : //Logout - no body
			$t_head = $t_body;
			$t_body = false;
			break;
		case 'EID007' : //Balance Status
			break;
		case 'EID008' : //Transaction
			break;
		case 'EID009' : //Account Status
			break;
	}
	$event_time_label = timeline_day_count($t_v['created_datetime']);
	/*if ($total_events == ($t_k+1)) {
		//find out last event
		$event_loader_attr = '';
	}*/
	$timeline .= '
		<li '.$event_loader_attr.' data-event-id="'.$t_v['origin'].'" class="event-origin">
			<i class="'.$t_v['event_icon'].'"></i>
			<div class="timeline-item">
				<span class="time"><i class="fa fa-clock-o"></i> <span class="even-time-moments">'.$event_time_label.' ago</span></span>';
	if (empty($t_head) == false) {
		$timeline .= '<h3 class="timeline-header">'.$t_head.'</h3>';
	}

	if (empty($t_body) == false) {
		$timeline .= '<div class="timeline-body">'.$t_body.'</div>';
	}

	if (empty($t_foot) == false) {
		$timeline .= '<div class="timeline-footer">'.$t_foot.'</div>';
	}
	$timeline .= '
			</div>
		</li>';
}
echo $timeline;
?>