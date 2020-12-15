$(document).ready(function(){
	var cache = {};
	$(".user_traveller_details").catcomplete({
		source: function( request, response ) {
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
		minLength: 0,
		autoFocus: true,
		select: function(event,ui){
			var traveller_name = ui.item.first_name+' '+ui.item.last_name;
			var traveller_date_of_birth = ui.item.date_of_birth;
			var traveller_id = ui.item.id;
			ui.item.value = traveller_name;
			//Calculating Age Based On DOB
			var traveller_age = get_traveller_age(traveller_date_of_birth);
			auto_focus_input(this.id);
			$(this).closest('form').find("#age-"+$(this).data('row-id')).val(traveller_age);//Assigning the Age
		}
		}).bind('focus', function(){ $(this).catcomplete("search"); } ).catcomplete( "instance" )._renderItem = function( ul, item ) {
			var auto_suggest_value = (this.term.trim(), item.value, item.label);
			 	return $("<li class='custom-auto-complete'>")
						.append('<a>' + auto_suggest_value + '</a>')
						.appendTo(ul);
		};
		//Traveller Agre -- Balu A
		function get_traveller_age(dob)
		{
			var age = 0;
			if(typeof(dob)!='undefined' && dob !='') {
				dob=dob.split('-');
				var year=parseInt(dob[0]);
				var month=parseInt(dob[1])-1;
				var day=parseInt(dob[2]);
				var today=new Date();
				age=today.getFullYear()-year;
				if(today.getMonth()<month || (today.getMonth()==month && today.getDate()<day))
				{
					age--;
				}
				if(age == 0) {
					age=1;
				}
			}
			return age;
		}
});
