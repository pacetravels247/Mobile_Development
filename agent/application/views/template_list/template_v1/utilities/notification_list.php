<?php
$notification_list = '';
$pagination = '<div>'. $GLOBALS['CI']->pagination->create_links().'</div>';
$notification_list .= $pagination;
$notification_list .= '<ul class="list-group">';
if(valid_array($list) == true) {
	$event_date = '';
	$event_loader_attr = '';
	$segment_3 = $GLOBALS['CI']->uri->segment(3);
	$current_record = (empty($segment_3) ? 0 : $segment_3);
	foreach ($list as $t_k => $t_v) {
		$current_event_date = date('dS M Y', strtotime($t_v['created_datetime']));
		$t_head = '<strong class="text-blue">'.$t_v['event_title'].'</strong>';
		$t_body = $t_v['event_description'];
		$t_foot = false;
		$action_query_string = json_decode($t_v['action_query_string'], true);
		$action_query_string = http_build_query($action_query_string['q_params']);
		$system_transaction_id = $action_query_string['q_params']['system_transaction_id'];
		$notification_action_url = '';
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
				$notification_action_url = base_url().'index.php/management/b2b_balance_manager/?'.$action_query_string;
				break;
			case 'EID012' : //Credit Limit
			$notification_action_url = base_url().'index.php/management/b2b_credit_limit/?'.$action_query_string;
			case 'EID015' : //package payment request
				
				if(substr($system_transaction_id,0,2)=='PT'){
					$notification_action_url = base_url().'index.php/tours/confirmed_tours_enquiry/?'.$action_query_string;
				}else if(substr($system_transaction_id,0,2)=='CP'){
					$notification_action_url = base_url().'index.php/tours/confirmed_custom_enquiry/?'.$action_query_string;
				}else{
					$notification_action_url = base_url().'report/package_enquiry_report/?'.$action_query_string;		
				}
			break;
		}
		$view_details_button = '<button class="btn btn-sm btn-primary pull-right fontsize14">View Details</button>';
		$event_time_label = timeline_day_count($t_v['created_datetime']);
		$notification_list .= '<li class="list-notices">
						<a href="'.$notification_action_url.'">
						<div class="col-sm-10">
						  <div class="colslno">'.(++$current_record).'. </div>
						  <i class="'.$t_v['event_icon'].' noticetext"></i>  <div class="noticewrp"><span class="noticemsgagent">'.$t_body.'</span>
						  <span class="timenotice">     <i class="fa fa-clock-o"></i> <span class="even-time-moments">'.$event_time_label.' ago</span></span>
						  </div>
						</div>
						<div class="col-sm-2">
						  '.$view_details_button.'
						</div>
					</li>';
	}
} else {
	$notification_list .= '<li class="list-group-item">No Notification Found !!</li>';
}
$notification_list .= '</ul>';
echo $notification_list;
?>