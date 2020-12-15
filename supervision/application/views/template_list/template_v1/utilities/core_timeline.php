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
			$day_summary_head = '';
		} else {
			$event_date_label = $event_date;
			$day_summary_head = '';
			//day summary header to be created
			$day_cond = array(array('DATE(TL.created_datetime)', '=', $this->db->escape(date('Y-m-d', strtotime($t_v['created_datetime'])))));
			$day_summary = $this->application_logger->day_summary($day_cond);
			if (valid_array($day_summary)) {
				$day_summary_head .= '<div class="timeline-item">';
				$day_summary_head .= '<div class="row">';
				foreach ($day_summary as $k => $v) {
					$day_summary_head .= '<div class="col-md-3 col-sm-6 col-xs-12">
              <div class="mini-info-box">
                <span class="mini-info-box-icon '.$v['event_icon'].'"></span>
                <div class="mini-info-box-content">
                  <span class="">'.$v['event_title'].'</span>
                  <span class="info-box-number">'.(empty($v['event_origin']) == true ? 0 : $v['total']).'</span>
                </div>
              </div>
            </div>';
				}
				$day_summary_head .= '</div>';
				$day_summary_head .= '</div>';
			}
		}
		$timeline .= '
			<li class="time-label" id="'.strtotime($current_event_date).'_event_day_header">
				<span class="bg-navy">
				'.$event_date_label.'
				</span>'.$day_summary_head.'
			</li>';
		//$timeline .= '<li>'..'</li>';
	}
	$t_head = '<strong class="text-blue">'.$t_v['event_title'].'</strong>';
	$t_body = $t_v['event_description'];
	$t_foot = false;
	$action_query_string = json_decode($t_v['action_query_string'], true);
	if(isset($action_query_string)){
		//debug($action_query_string);exit;
	$action_query_string = http_build_query($action_query_string['q_params']);
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
		case 'EID010' : //Account Status
			break;
		case 'EID011' : //Account Status
			$t_foot = '<a href="'.base_url().'index.php/management/b2b_balance_manager?'.$action_query_string.'">view details</a>';
			break;
	}
	
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