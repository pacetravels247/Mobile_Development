$(document).ready(function(e){
	//Cache Travellers autocomplete data
	var current_date = db_date();
	var _passport_min_exp = document.getElementById('pri_passport_min_exp').value;
	var cache = {};
	$(".user_traveller_details").catcomplete({
		source:  function( request, response ) {
			var term = request.term.trim();
			if ( term in cache ) {
			response( cache[ term ] );
			return;
			} else {
				$.getJSON( app_base_url+"index.php/ajax/user_traveller_details", request, function( data, status, xhr ) {
					cache[ term ] = data;
					response( cache[ term ] );
				});
			}
		},
		minLength: 0,//search after two characters
		autoFocus: true, // first item will automatically be focused
		select: function(event,ui){
			var traveller_last_name = ui.item.last_name;
			var traveller_date_of_birth = ui.item.date_of_birth;
			var traveller_id = ui.item.id; 
			auto_focus_input(this.id);
			//Last Name
			var parent_obj = $(this).closest('form');
			//Assigning the Last Name
			parent_obj.find("#passenger-last-name-"+$(this).data('row-id')).val(traveller_last_name);
			parent_obj.find("#adult-date-picker-"+$(this).data('row-id')).val(traveller_date_of_birth);
			//Passport Details
			var passport_expiry_day = zeroPad(ui.item.passport_expiry_day, 2);
			var core_passport_expiry_month = parseInt(ui.item.passport_expiry_month);
			var passport_expiry_month = (core_passport_expiry_month+1);//Month starting with zero
			passport_expiry_month = zeroPad(passport_expiry_month, 2);
			var passport_expiry_year = ui.item.passport_expiry_year;
			var passport_number = ui.item.passport_number;
			var passport_issuing_country = ui.item.passport_issuing_country; 
			var passport_date = (passport_expiry_year+'-'+passport_expiry_month+'-'+passport_expiry_day);
			var journey_date = _passport_min_exp;//Check out Date
			var expiry_days = get_day_difference(journey_date, passport_date);//Passport Expiry Day
			if(expiry_days >= 0 && passport_number !='' && passport_issuing_country !='') {//If Passport Details are valid 
				parent_obj.find("#passenger_passport_number_"+$(this).data('row-id')).val(passport_number);
				parent_obj.find("#passenger_passport_issuing_country_"+$(this).data('row-id')).val(passport_issuing_country);
				parent_obj.find("#passenger_passport_expiry_day_"+$(this).data('row-id')).val(passport_expiry_day);
				parent_obj.find("#passenger_passport_expiry_month_"+$(this).data('row-id')).val(core_passport_expiry_month);
				parent_obj.find("#passenger_passport_expiry_year_"+$(this).data('row-id')).val(passport_expiry_year);
			}
		}
		}).bind('focus', function(){ $(this).catcomplete("search"); } ).catcomplete( "instance" )._renderItem = function( ul, item ) {
			var auto_suggest_value = (this.term.trim(), item.value, item.label);
				return $("<li class='custom-auto-complete'>")
						.append('<a>' + auto_suggest_value + '</a>')
						.appendTo(ul);
		};
	//Validation Of Passport Details
	$('.passport_expiry_day, .passport_expiry_month, .passport_expiry_year').change(function(){
		var parent_obj = $(this).closest('form');
		var row_id = $(this).data('row-id');
		var exp_day = parent_obj.find("#passenger_passport_expiry_day_"+row_id).val();
		var exp_month = parent_obj.find("#passenger_passport_expiry_month_"+row_id).val();
		var exp_year = parent_obj.find("#passenger_passport_expiry_year_"+row_id).val();
		exp_day = zeroPad(exp_day, 2);
		exp_month = (parseInt(exp_month)+1);//Month starting with zero
		exp_month = zeroPad(exp_month, 2);
		if(isNaN(exp_day) == false && isNaN(exp_month) == false && isNaN(exp_year) == false) {
			var error_count = 0;
			var error_msg = '';
			var pasport_date = exp_year+'-'+exp_month+'-'+exp_day;
			if(validate_date(pasport_date) == true) {
				var expiry_days = get_day_difference(_passport_min_exp, pasport_date);//Check Passport Expiry Day with Travel Date
				if(expiry_days < 0) {//Passport is Expired
					error_msg = 'PassportExpiryDate is less than TravelDate';
					error_count++;
				}
			} else {
				error_msg = 'Invalid Date';
				error_count++;
			}
			if(error_count > 0) {
				if(parent_obj.find("#passport_error_msg_"+row_id).hasClass('hide') == true) {
					parent_obj.find("#passport_error_msg_"+row_id).removeClass('hide');
				}
				parent_obj.find("#passport_error_msg_"+row_id).empty().html(error_msg);
				//Resetting the Values
				parent_obj.find("#passenger_passport_expiry_day_"+row_id).val('INVALIDIP');
				parent_obj.find("#passenger_passport_expiry_month_"+row_id).val('INVALIDIP');
				parent_obj.find("#passenger_passport_expiry_year_"+row_id).val('INVALIDIP');
			} else {
				if(parent_obj.find("#passport_error_msg_"+row_id).hasClass('hide') == false) {
					parent_obj.find("#passport_error_msg_"+row_id).addClass('hide');
				}
				parent_obj.find("#passport_error_msg_"+row_id).empty();
			}
		}
	});	
});
