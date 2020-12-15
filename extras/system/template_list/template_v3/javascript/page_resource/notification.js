$(document).ready(function(){
	$(".dropdown").hover(            
	function() {
		
		$('.dropdown-menu', this).stop( true, true ).fadeIn("fast");
		$(this).toggleClass('open');            
	},
	function() {
		$('.dropdown-menu', this).stop( true, true ).fadeOut("fast");
		$(this).toggleClass('open');              
	});
	
	get_events_notification_details();
	$('#get_event_notification').hover(function(){
		var deactive_notification = 1;
		get_events_notification_details(deactive_notification);
	});
	function get_events_notification_details(deactive_notification)
	{
		if (typeof deactive_notification == "undefined") {
			deactive_notification = 0;
	    }
		events_notification_count(deactive_notification);
		events_notification_list();
	}
	//Active Notification Count
	function events_notification_count(deactive_notification)
	{
		if (typeof deactive_notification == "undefined") {
			deactive_notification = 0;
	    }
		$.get(app_base_url+'utilities/active_notifications_count?deactive_notification='+deactive_notification, function(notification_count){
			if (typeof notification_count['data'] != "undefined") {
				var active_notifications_count = parseInt(notification_count['data']['active_notifications_count']);
				if(active_notifications_count<=0) {
					$('#active_notifications_count').text('');
				} else {
					$('#active_notifications_count').text(active_notifications_count);
				}
			}
		});
	}
	//Notification List
	function events_notification_list()
	{
		$.get(app_base_url+'utilities/events_notification', function(notification_list){
			var notification_list_data = '<li><a href="#"><small> <i class="fa fa-exclamation text-aqua"></i> No Activities</a></small></li>';
			if(notification_list['status'] == 1) {
				notification_list_data = notification_list['data']['notification_list'];
				if($('#view_all_notification').hasClass('hide') == true) {
					$('#view_all_notification').removeClass('hide');
				}
			} else {
				if($('#view_all_notification').hasClass('hide') == false) {
					$('#view_all_notification').addClass('hide');
				}
			}
			$('#notification_dropdown').empty().html(notification_list_data);
		});
	}
});