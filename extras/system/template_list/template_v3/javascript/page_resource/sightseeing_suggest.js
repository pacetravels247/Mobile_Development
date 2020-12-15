$(document).ready(function() {
	var cache = {};
	
	$(".activity_city").catcomplete({
		source: function(request, response) {
			var term = request.term;
			if(term!=' ' && term!='')//remove autocomplete
			{
				if (term in cache) {
					response(cache[term]);
					return
				}
				$.getJSON(app_base_url + "index.php/ajax/get_activity_destination_list", request, function(data, status, xhr) {
					cache[term] = data;
					response(data)
				})
			}
		},
		minLength: 0,
		autoFocus: true,
		select: function(event, ui) {
			var label = ui.item.label;
			var category = ui.item.category;
			$(this).siblings('.loc_id_holder').val(ui.item.id);
			$('#activity_from').focus()
		},
		change: function(ev, ui) {
			if (!ui.item) {
				$(this).val("")
			}
		}
	}).bind('focus', function() {
		$(this).catcomplete("search")
	}).catcomplete("instance")._renderItem = function(ul, item) {
		var auto_suggest_value = highlight_search_text(this.term.trim(), item.value, item.label);
		var hotel_count = '';
		var count = parseInt(item.count);
		if (count > 0) {
			var h_lab = '';
			if (count > 1) {
				h_lab = 'Activities'
			} else {
				h_lab = 'Activity'
			}
			hotel_count = '<span class="activity_cnt">(' + parseInt(item.count) + ' ' + h_lab + ')</span>'
		}
		return $("<li class='custom-auto-complete'>").append('<a> ' + auto_suggest_value + ' ' + hotel_count + '</a>').appendTo(ul)
	};
	
	/*$("#activity_from").change(function() {
		//manage date validation
		auto_set_dates($("#activity_from").datepicker('getDate'), "activity_from", 'minDate');
	});*/
	//if second date is already set then dont run
	if ($("#activity_to").val() == '' ) {
		auto_set_dates($("#activity_to").datepicker('getDate'), "activity_to", 'minDate');
	}
	
	
	/*show age drodown on click of plus*/
	$('.plusValAct').click(function() {
		var id = $(this).siblings("input").attr('id');

		if(id == "OWT_child") {
			var idval = '#child_ageId';
			var classVal = '.child_ageId';
		}

		if(id == "OWT_adult") {
			var idval = '#adult_ageId';
			var classVal = '.adult_ageId';
		}
		if(id == "OWT_infant") {
			var idval = '#infant_ageId';
			var classVal = '.infant_ageId';
		}
		var value = $(this).siblings("input").val();
		
		if(id == "OWT_child")
		{
			value = parseInt(value)+1;
		}
		
		
		if(value > 0 && id == "OWT_child") {
			$('.activity-child-age-label').removeClass('hide');
		}else {
			$('.activity-child-age-label').addClass('hide');
		}
		//alert(idval+value);
		$(idval+value).removeClass('hide');
		$(classVal+value).removeClass('hide');
		$(idval+value).prop("disabled", false);
		
	});
	
	/*hide age drodown on click of minus*/
	$('.minusValueAct').click(function() {
		var value = $(this).siblings("input").val();
		var id = $(this).siblings("input").attr('id');
		if(id == "OWT_child") {
			var idval = '#child_ageId';
			var classVal = '.child_ageId';
		}
		if(id == "OWT_adult") {
			var idval = '#adult_ageId';
			var classVal = '.adult_ageId';
		}
		if(id == "OWT_infant") {
			var idval = '#infant_ageId';
			var classVal = '.infant_ageId';
		}
		if(id == "OWT_child")
		{
		value = parseInt(value)-1;
		}
		/*&& id == "OWT_child"*/
		if(value > 0 && id == "OWT_child") {
			$('.activity-child-age-label').removeClass('hide');
		}else {
			$('.activity-child-age-label').addClass('hide');
		}
		var prevVal = parseInt(value) + 1;
		$(idval+prevVal).addClass('hide');
		$(classVal+prevVal).addClass('hide');
		$(idval+prevVal).prop("disabled", true);
		
	});
	
	
		
});