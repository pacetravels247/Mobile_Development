

$(document).ready(function(){

	var modal_close = 1;
	//check_session_time_out();
	window.check_session_time_out = function (search_hash, start_time) { // search hash

		var obj = '';
		if(search_hash != '' && search_hash != null && search_hash != undefined){

			setInterval(function(){
				
				start_time--; 
				if(parseInt(search_session_alert_expiry) == start_time && modal_close == 1){
					modal_close = 0;
					$('#session-alert-header').text('Session Alert');
					$('#session-alert-body').html('<h4>You have only '+(search_session_alert_expiry/60)+' minutes to complete booking !!</h4>');
					
					$('#session-alert-modal').modal('show');
				}else if((start_time >= parseInt(search_session_expiry) && start_time > parseInt(search_session_alert_expiry)) || (start_time <= 0) && $('#session-alert-close-btn').attr('session-expired') == 0){
					$('#session-alert-close, #session-alert-close-btn').attr('session-expired', 1);
					$('#session-alert-header').text('Session Alert');
					$('#session-alert-body').html('<h4>Session is expired. Please search again</h4>');
					$('#session-alert-modal').modal('show');
				}

			}, 1000);
		}
	}

	// on close of modal if session is expired redirect to home page
	$('#session-alert-close, #session-alert-close-btn').on('click', function(){
		return false;//enable later
		$('#session-alert-modal').modal('hide');
		$('body').removeClass('modal-open');
		$('body').find('.modal-backdrop').remove();
		if($(this).attr('session-expired') == 1){
			window.location = app_base_url;
		}
	});

	

	$(document).click(function (e) {
	  if($(e.target).hasClass('modal') && $('#session-alert-modal').is(':visible')){ // modal close check session expired
		  	return false;//enable later
	    	$('#session-alert-modal').modal('hide');
			if($(' #session-alert-close-btn').attr('session-expired') == 1){
				window.location = app_base_url;
			}
	  }
	});

	if(session_time_out_function_call == 1){
		check_session_time_out(search_hash, start_time);
	}

});
