<script>
$(document).ready(function() {
	$('#billing-country').on('change', function() {
		if (this.value in city_list_cache) {
			update_city_options(this.value);
		} else {
			fill_city_list(this.value);
		}
	});

	/**
	*Cache city list based on country
	*/
	var city_list_cache = {};
	function fill_city_list(country_name)
	{
		$.get(app_base_url+"index.php/ajax/get_city_list/"+country_name, function(city_list) {
			city_list_cache[country_name] = city_list;
			update_city_options(country_name)
		});
	}

	/**
	*update city list based on country from cache
	*/
	function update_city_options(country_name)
	{
		$('#billing-city').html(city_list_cache[country_name]);
	}
	//fill_city_list($('#billing-country').val());

	$('[type="submit"]').on('click', function(e) {
		var _status = true;
		var _focus = '';
		$('select:required').each(function() {
			if (this.value == 'INVALIDIP') {
				$(this).addClass('invalid-ip');
				if (_status == true) {
					_status = false;
					_focus = this;
				}
			} else if ($(this).hasClass('invalid-ip')) {
				$(this).removeClass('invalid-ip');
			}
		});
		if (_status == false) {
			$('.alert-content').text('Please Fill All The Data To Continue');
			$('.alert-wrapper').removeClass('hide');
			$(_focus).focus();
			e.preventDefault();
		} else {
			$('.alert-wrapper').addClass('hide');
			$('.alert-content').text('');
		}
	});
	//Tobin
	$('.review_tab_marker').click(function(){
		if($(this).hasClass('review_tab_marker')) {
			$('.rondsts').removeClass('active');
			$(this).parent('.rondsts').addClass('active');
			$('.bktab2, .bktab3').fadeOut(500,function(){$('.bktab1').fadeIn(500)});
		}
	});
	//Tobin
	$('.travellers_tab_marker').click(function(){
		$(this).parent('.rondsts').addClass('active');
		$('#stepbk1').parent('.rondsts').addClass('success');
		$('.bktab1, .bktab3').fadeOut(500,function(){$('.bktab2').fadeIn(500)});
	});
	//Tobin
	$('#alreadyacnt').click(function(){
		show_alert_content('');
		if($(this).prop("checked") == true){
			$('.cntgust').fadeOut(500,function(){$('.alrdyacnt').fadeIn(500)});
		}

		else if($(this).prop("checked") == false){
			$('.alrdyacnt').fadeOut(500,function(){$('.cntgust').fadeIn(500)});
		}
	});
	//User Login - Balu A
	$('#continue_as_user').click(function(){
		show_alert_content('');
		var username = $('#booking_user_name').val().trim();
		var password = $('#booking_user_password').val().trim();
		if(username !='' && password !='') {
			var login_data = {'username':username, 'password':password};
			$.post(app_base_url+'index.php/auth/login', login_data, function(response){
				if(response['status'] == true) {
					location.reload();
				} else {
					show_alert_content(response['data']);
				}
			});
		}
	});
	//Add Guest User Data - Balu A
	$('#continue_as_guest').click(function(){
		var username = $('#booking_user_name').val().trim();
		var mobile_number = $('#booking_user_mobile').val().trim();
		var count = 0;
		$('._guest_validate').each( function () {
			if(this.value.trim() == '') {
	           count++;
	           $(this).addClass('invalid-ip');
			}
		});
		if(username!='' && validate_email(username) == false) {
			$('#booking_user_name').val('').addClass('invalid-ip').attr('placeholder', 'Invalid Email ID');
			count++;
		}
		if(mobile_number!='' && (mobile_number.toString()).length != 10) {
			$('#booking_user_mobile').val('').addClass('invalid-ip').attr('placeholder', 'Invalid Mobile Number');
			count++;
		}
		if(count == 0) {
			var login_data = {'username':username, 'mobile_number':mobile_number};
			$.post(app_base_url+'index.php/auth/register_guest_user', login_data, function(response){
				if(response['status'] == true) {
					$('#billing-email').val(username);
					$('#passenger-contact').val(mobile_number);
					show_travellers_tab();
				}
			});
		}
	});
	//Guest User Data Validation
	//validation
	$('._guest_validate').focus( function () {
	    $(this).removeClass('invalid-ip');
	});
	$('._guest_validate').blur( function () {
		if(this.value.trim() == '')
	    $(this).addClass('invalid-ip');
	});
	$('.name_title').change(function(e){
		var name_title = $(this).val().trim();
		var gender = get_gender(name_title);
		$(this).closest('div._passenger_hiiden_inputs').find('.hidden_pax_details').find('.pax_gender').val(gender);
	});
	//Balu A
	//After Continue as a guest, hide review tab and show travellers tab
	function show_travellers_tab()
	{
		$('.core_travellers_tab').removeClass('inactive_travellers_tab_marker').addClass('travellers_tab_marker');
		$('.travellers_tab_marker').parent('.rondsts').addClass('active');
		$('#stepbk1').parent('.rondsts').addClass('success');
		$('.core_review_tab').parent('.rondsts').removeClass('active');
		$('.core_review_tab').removeClass('review_tab_marker').addClass('inactive_review_tab_marker');//Inactive Review Tab
		$('.bktab1, .bktab3').fadeOut(500,function(){$('.bktab2').fadeIn(500)});
	}
	$('._numeric_only').on('keydown focus blur keyup change cut copy paste', function (e) {
		isNumber(e, e.keyCode, e.ctrlKey, e.metaKey, e.shiftKey);
	});
	//Balu A
	//Shows an error Message for User Login
	function show_alert_content(content, container)
	{
		if (typeof(container) == 'undefined') {
			container = '.alert-danger';
		}
		$(container).text(content);
		if (content.length > 0) {
			$(container).removeClass('hide');
		} else {
			$(container).addClass('hide');
		}
	}
	//Balu A
	//Returns Gender Based on Pax Title
	function get_gender(name_title)
	{
		var gender = 1;
		if(name_title !='') {
			name_title = parseInt(name_title);
			var male_titles = [1];
			var female_titles = [2,3,5];
			if($.inArray(name_title, male_titles) != -1) {
				gender = 1;
			} else if($.inArray(name_title, female_titles) != -1) {
				gender = 2;
			}
		}
		return gender;
	}
});
</script>