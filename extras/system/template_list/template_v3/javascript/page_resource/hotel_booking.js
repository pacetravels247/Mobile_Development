$(document).ready(function()
{
	//Cache Travellers autocomplete data
	var pri_journey_date = document.getElementById('pri_journey_date').value;
	var current_date = db_date();
	var cache = {};
	$(".user_traveller_details").catcomplete({
		source:  function( request, response ) {
			var term = request.term;
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
			//Passport Details
			 var passport_expiry_day = zeroPad(ui.item.passport_expiry_day, 2);
			 var core_passport_expiry_month = parseInt(ui.item.passport_expiry_month);
			 var passport_expiry_month = (core_passport_expiry_month+1);//Month starting with zero
			 passport_expiry_month = (passport_expiry_month+1);//Month starting with zero
			 passport_expiry_month = zeroPad(passport_expiry_month, 2);
			 var passport_expiry_year = ui.item.passport_expiry_year;
			 var passport_number = ui.item.passport_number;
			 var passport_issuing_country = ui.item.passport_issuing_country; 
			 var passport_date = (passport_expiry_year+'-'+passport_expiry_month+'-'+passport_expiry_day);
			 var journey_date = pri_journey_date//Check In Date
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
});