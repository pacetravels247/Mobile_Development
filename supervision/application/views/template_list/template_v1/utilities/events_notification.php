<?php
$event_date = '';
$timeline = '';
$total_events = count($list);
$event_loader_attr = '';
foreach ($list as $t_k => $t_v) {
	$current_event_date = date('dS M Y', strtotime($t_v['created_datetime']));
	$t_head = '<strong class="text-blue">'.$t_v['event_title'].'</strong>';
	$t_body = $t_v['event_description'];
	$t_foot = false;
	$action_query_params = json_decode($t_v['action_query_string'], true);
	$action_query_params = $action_query_params['q_params'];
	$action_query_string = http_build_query($action_query_params);
	$notification_action_url = '';
	switch ($t_v['event_origin']) {
		case 'EID001' : //User Registration
			$type = trim(@$action_query_params['user_type']);
			$action_function = '';
			switch($type){
				case B2C_USER;
					$action_function = '/user/b2c_user/';
					break;
				case B2B_USER;
					$action_function = '/user/b2b_user/';
					break;
			}
			$notification_action_url = base_url().'index.php'.$action_function.'?'.$action_query_string;
			break;
		case 'EID002' : //Profile Update
			$type = trim(@$action_query_params['user_type']);
			$action_function = '';
			switch($type){
				case B2C_USER;
					$action_function = '/user/b2c_user/';
					break;
				case B2B_USER;
					$action_function = '/user/b2b_user/';
					break;
			}
			$notification_action_url = base_url().'index.php'.$action_function.'?'.$action_query_string;
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
			$type = trim($action_query_params['type']);
			$module = @$action_query_params['module'];
			$action_function = '';
			switch($type){
				case 'flight';
					$action_function = '/report/'.$module.'_flight_report/';
					break;
				case 'hotel';
					$action_function = '/report/'.$module.'_hotel_report/';
					break;
				case 'bus';
					$action_function = '/report/'.$module.'_bus_report/';
					break;
			}
			$notification_action_url = base_url().'index.php'.$action_function.'?'.$action_query_string;
			break;
			break;
		case 'EID009' : //Account Status
			break;
		case 'EID010' : //API Status
			break;
		case 'EID011' : //Balance Deposit
			$notification_action_url = base_url().'index.php/management/b2b_balance_manager/?'.$action_query_string;
			break;
		case 'EID012' : //Credit Limit
			$notification_action_url = base_url().'index.php/management/b2b_credit_request/?'.$action_query_string;
			break;
		case 'EID013' : //Balance Debit
			$notification_action_url = base_url().'index.php/management/b2b_balance_manager/?'.$action_query_string;
			break;
	}
	$event_time_label = timeline_day_count($t_v['created_datetime']);
	$timeline .= '<li class="eventli">
					<a href="'.$notification_action_url.'">
					  <i class="'.$t_v['event_icon'].' noticetext"></i> <div class="noticewrp"><span class="noticemsg">'.$t_body.'</span>
					  <div class="clearfix"></div>
					  <span class="timenotice"><i class="fa fa-clock-o"></i> <span class="even-time-moments">'.$event_time_label.' ago</span></span></div>
					</a>
				</li>';
}
echo $timeline;
?>