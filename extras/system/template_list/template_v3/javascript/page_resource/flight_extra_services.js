$(document).ready(function(){
	var core_booking_amount = parseFloat($('#total_booking_amount').text());
        var booking_amount = parseFloat($('#flight_amount').val());
	var agent_payble_amount = parseFloat($('#agent_payable_amount').text());
        $('.add_extra_service').on('change', function(){
		update_total_booking_amount();
	});
        $('.add_insurance').click(function(){
            $('.add_insurance').val();
             update_total_booking_amount();
	});
        
        
        
	//Remove Baggage
	$('#remove_extra_baggage').on('click', function(){
		$('.choosen_baggage').val('');
		update_total_booking_amount();
	});
	//Remove Meal
	$('#remove_extra_meal').on('click', function(){
		$('.choosen_meal').val('');
		update_total_booking_amount();
	});
	//Remove Seat
	$('#remove_extra_seat').on('click', function(){
		$('.choosen_seat').val('');
		$('.choosen_seat').data('seat_price', '');
		$('.seat_pax_number').empty();
		$('.seat_pax_price').empty();
		
		//change seat image to available image
		$('img.seat_selected').attr('src', system_image_dir_url+'available.png');
		$('img.seat_selected').removeClass('seat_selected');
	
		update_total_booking_amount();
	});
        $('.remove_insurance').on('click', function(){
		$('.add_insurance').val(0);
		update_total_booking_amount();
	});
        
        
        
	/**
	 * Updates Total Booking Amount
	 */
	function update_total_booking_amount()
	{
		//Baggage
          //  var core_booking_amount = parseFloat($('#total_booking_amount').text());	    
		var total_baggage_price = 0;
		$('.choosen_baggage').each(function(){
			var baggage_obj = $(this).find('option:selected');
			var baggage_price = parseFloat(baggage_obj.data('choosen-baggage-price'));
			if(isNaN(baggage_price) == true){
				baggage_price = 0;
			}
			total_baggage_price +=baggage_price;
		});
		
		//Meals
		var total_meal_price = 0;
		$('.choosen_meal').each(function(){
			var meal_obj = $(this).find('option:selected');
			var meal_price = parseFloat(meal_obj.data('choosen-meal-price'));
			if(isNaN(meal_price) == true){
				meal_price = 0;
			}
			total_meal_price +=meal_price;
		});
		
		//Seat
		var total_seat_price = 0;
		$('.choosen_seat').each(function(){
			var seat_obj = $(this);
			var seat_price = parseFloat(seat_obj.data('seat_price'));
			if(isNaN(seat_price) == true){
				seat_price = 0;
			}
			total_seat_price +=seat_price;
		});
                
                // Insurance 
              var total_insurance_price = 0;
		$('.add_insurance').each(function(){
			var insurance_price=parseFloat($(this).val());
                        //var insurance_price=parseFloat(100);                      
			if(isNaN(insurance_price) == true){
				insurance_price = 0;
			}
                        
			total_insurance_price +=insurance_price;
                      // alert(total_insurance_price);
		});
		
		
		$('#extra_baggage_charge').empty().text(total_baggage_price);
		if(total_baggage_price > 0){
			$('#extra_baggage_charge_label').show();
		} else {
			$('#extra_baggage_charge_label').hide();
		}
		
		$('#extra_meal_charge').empty().text(total_meal_price);
		if(total_meal_price > 0){
			$('#extra_meal_charge_label').show();
		} else {
			$('#extra_meal_charge_label').hide();
		}
		
		$('#extra_seat_charge').empty().text(total_seat_price);
		if(total_seat_price > 0){
			$('#extra_seat_charge_label').show();
		} else {
			$('#extra_seat_charge_label').hide();
		}
                
                
                $('.insurance_amount').empty().text(total_insurance_price);
		if(total_insurance_price > 0){
			$('.insurance').show();
		} else {
			$('.insurance').hide();
		}
		var convenience_fees = parseFloat($('.convenience_fees_org').text());
		
		//alert(core_booking_amount);
		//Adding extraservice price to booking amount
		var extra_service_price = total_baggage_price+total_meal_price+total_seat_price+total_insurance_price+convenience_fees;
		
		var new_total_booking_amount = (core_booking_amount+extra_service_price);
		
		
		var promo_val = $("#promo_code_discount_val").val();
		if($.isNumeric(promo_val)){			
			new_total_booking_amount = new_total_booking_amount - promo_val;		
		}
		var org_convience_fee = $('.org_convience_fee').text();
		if(org_convience_fee > 0){
			new_total_booking_amount = booking_amount+extra_service_price;
			var convience_fee = (new_total_booking_amount*org_convience_fee)/100;

			new_total_booking_amount = new_total_booking_amount+convience_fee;
			$('#convenience_fees').empty().text(convience_fee);
			$('#grandtotal').empty().text(booking_amount+convience_fee);
		}
		$('#total_booking_amount').empty().text(new_total_booking_amount);
                $('.total_booking_amount').empty().text(new_total_booking_amount);
               
		
		//in Agent
	
		if($('.grand_total_amount').length){
			$('.grand_total_amount').empty().text(new_total_booking_amount);
		}
		
		if($('#agent_payable_amount').length){
			$('#agent_payable_amount').empty().text(agent_payble_amount+extra_service_price);
		}
	}
	
	$('.extract_pax_name_cls').on('input blur', function(){
		if($(this).val().trim()!= ''){
			var passenegr_index = $(".extract_pax_name_cls").index(this);
			var passenegr_name = '';
			var first_name_index = passenegr_index;
			if($(this).attr('name') == 'last_name[]'){
				first_name_index = (passenegr_index-1);
				passenegr_name += $(".extract_pax_name_cls").eq(passenegr_index-1).val().trim();
				passenegr_name += ' '+$(this).val().trim();
			} else {
				passenegr_name += $(this).val().trim();
				passenegr_name += ' '+$(".extract_pax_name_cls").eq(passenegr_index+1).val().trim();
			}
			first_name_index = (first_name_index/2);
			
			if(passenegr_name !=''){
				$('.bag_pax_name').eq(first_name_index).empty().text(passenegr_name);
				
				$('.meal_pax_name').eq(first_name_index).empty().text(passenegr_name);
				
				$('.meal_pref_pax_name').eq(first_name_index).empty().text(passenegr_name);
				
				$('.seat_pref_pax_name').eq(first_name_index).empty().text(passenegr_name);
				update_seat_pax_name(first_name_index, passenegr_name);
			}
			
		}
	});
	update_first_passenger_name();
	//update passenger name
	function update_first_passenger_name()
	{
		var first_passenger_name = $('.extract_pax_name_cls').eq(0).val().trim()+' '+$('.extract_pax_name_cls').eq(1).val().trim();
		
		if($('.extract_pax_name_cls').eq(0).val().trim() != ''){
			$('.bag_pax_name').eq(0).empty().text(first_passenger_name);
			$('.meal_pax_name').eq(0).empty().text(first_passenger_name);
			$('.meal_pref_pax_name').eq(0).empty().text(first_passenger_name);
			$('.seat_pref_pax_name').eq(0).empty().text(first_passenger_name);
			update_seat_pax_name(0, first_passenger_name)
		}
	}
	//Updates seat passenger name
	function update_seat_pax_name(first_name_index, passenegr_name)
	{
		$('.seat_pax_details').each(function(){
			$('.seat_pax_name', this).eq(first_name_index).empty().text(passenegr_name);
		});
	}
	//Remove later
	$('.extra_services_hide_show').click(function(){
		if($(this).hasClass('fa-angle-double-down')){
			$(this).removeClass('fa-angle-double-down').addClass('fa-angle-double-up');
		} else if($(this).hasClass('fa-angle-double-up')){
			$(this).removeClass('fa-angle-double-up').addClass('fa-angle-double-down');
		}
	});
	
	
	
	
	/*******Seat Map Starts ************/
	var seat_pax_index = 0;
	
	//By defalut activate first seat map
	activate_current_seat_segment_map(0);
	
	$('.seat_segment_map').click(function(event){
		
		var seat_segment_index = $(".seat_segment_map").index(this);
		activate_current_seat_segment_map(seat_segment_index);
	});
	$('.seat_segment_pax').click(function(event){
		var seat_segment_index = $(".seat_segment_pax").index(this);
		$('.seat_segment_map').eq(seat_segment_index).trigger('click');
		
	});
	$('.seat_segment_pax_tr').click(function(event){
		
		var seat_segment_index = $('.seat_segment_pax').index($(this).closest('.seat_segment_pax'));
		seat_pax_index = $(".seat_segment_pax_tr").index(this);
		
		$('.seat_segment_map').eq(seat_segment_index).trigger('click');
		if($('.seat_segment_map').eq(seat_segment_index).hasClass('active_seat_segment_map') == true){
			event.stopPropagation();//check this for more flights
		}
	});
	
	//Activate Current Seat Map
	function activate_current_seat_segment_map(seat_segment_index)
	{
		$('.seat_segment_map').removeClass('active_seat_segment_map');
		$('.seat_segment_pax').removeClass('active_seat_segment_pax');
		$('.seat_segment_pax_tr').removeClass('active_seat_segment_pax_tr');
		
		$('.seat_segment_map').eq(seat_segment_index).addClass('active_seat_segment_map');
		$('.seat_segment_pax').eq(seat_segment_index).addClass('active_seat_segment_pax');
		if(seat_pax_index > 0){
			$('.seat_segment_pax_tr').eq(seat_pax_index).addClass('active_seat_segment_pax_tr');
		} else {
			$('.seat_segment_pax_tr', $('.seat_segment_pax').eq(seat_segment_index)).eq(seat_pax_index).addClass('active_seat_segment_pax_tr');
		}
		
		seat_pax_index = 0;//Reset to zero
	}
	
	/**
	 * 1) Restrict with Pssenger: Max Seats
	 * 
	 * 
	 * 
	 */
	$('.choose_seat').click(function(){
		
		if(is_seat_free($(this))){
			var opeartion = '';
			if($(this).hasClass('seat_selected') == true){
				$(this).attr('src', system_image_dir_url+'available.png');
				$(this).removeClass('seat_selected');
				opeartion = 'remove';
			} else {
				$(this).attr('src', system_image_dir_url+'selected.png');
				$(this).addClass('seat_selected');
				opeartion = 'add';
			}
			update_pax_seat_details($(this), opeartion);
		}
		update_total_booking_amount();
	});
	/**
	 * Update Passenger Details
	 */
	function update_pax_seat_details(current_seat_object, opeartion)
	{
		//make old seat available
		make_seat_available();
		if(opeartion == 'add'){
			var seat_number = current_seat_object.data('seat_number');
			var seat_price = current_seat_object.data('seat_price');
			var seat_id = current_seat_object.data('seat_id');
		} else {
			var seat_number = '';
			var seat_price = '';
			var seat_id = '';
		}
		//Updating Seat Number
		$('td', '.active_seat_segment_pax_tr').eq(1).empty().text(seat_number);
		//Updating Seat Price
		$('td', '.active_seat_segment_pax_tr').eq(2).empty().text(seat_price);
		//Updating SeatID and Price
		$('input', '.active_seat_segment_pax_tr').eq(0).val(seat_id);
		$('input', '.active_seat_segment_pax_tr').eq(0).data('seat_price', seat_price);
		
	}
	/**
	 * Checks the seat is free to allocate
	 */
	function is_seat_free(current_seat_object)
	{
		var seat_id = current_seat_object.data('seat_id');
		var selected_seat_name = $("input[value='"+seat_id+"']", '.seat_segment_pax_tr').attr('name');
		
		if(typeof(selected_seat_name) == 'undefined'){
			return true;
		} else {
			update_pax_seat_details(current_seat_object, 'remove');
			return false;
		}
	}
	/**
	 * Free the seat
	 */
	function make_seat_available()
	{
		var old_seat_id = $('input', '.active_seat_segment_pax_tr').eq(0).val().trim();
		if(old_seat_id !=''){
			$("img[data-seat_id='"+old_seat_id +"']").attr('src', system_image_dir_url+'available.png');
			$("img[data-seat_id='"+old_seat_id +"']").removeClass('seat_selected');
		}
	}
	/*******Seat Map Ends ************/
	
	
});
