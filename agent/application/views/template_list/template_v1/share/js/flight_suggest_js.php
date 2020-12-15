<script>
$(document).ready(function(){
	$('#flight-form-submit').on('click', function(e) {
		var _adult = parseInt($('#OWT_adult').val());
		var _child = parseInt($('#OWT_child').val());
		var _infant = parseInt($('#OWT_infant').val());
		var _content = '';
		if (_infant > 0 && _adult < _infant) {
			e.preventDefault();
			_content = '1 Infant Per Adult Allowed';
		}
		show_alert_content(_content);
	});

	function show_alert_content(content, container)
	{
		if (container == '') {
			container = '.alert-danger';
		}
		$(container).text(content);
		if (content.length > 0) {
			$(container).removeClass('hide');
		} else {
			$(container).addClass('hide');
		}
	}

	$('[name="trip_type"]').on('change', function() {
		handle_active_trip_type(this.value);
	});

	/**
	* Handle Active Trip Type
	*/
	function handle_active_trip_type(_active_trip_type)
	{
		if (_active_trip_type == 'oneway') {
			$('#flight_datepicker2').attr('disabled', true).removeAttr('required').closest('.date-wrapper').animate({'opacity':'.3'});
			$('#flight_datepicker2').val('');
		} else if (_active_trip_type == 'circle') {
			$('#flight_datepicker2').removeAttr('disabled').attr('required', 'required').closest('.date-wrapper').animate({'opacity':'1'}).focus();
		} else if (_active_trip_type == 'multicity') {

		}
	}

	/**
	* Handle Active Trip Type
	*/
	handle_active_trip_type($('[name="trip_type"]:checked').val());

	//Cache flight autocomplete data
	var cache = {};
	$(".fromflight, .departflight").autocomplete({
	source:  function( request, response ) {
        var term = request.term;
        if ( term in cache ) {
          response( cache[ term ] );
          return;
        }
 
        $.getJSON( app_base_url+"index.php/ajax/get_airport_code_list", request, function( data, status, xhr ) {
          cache[ term ] = data;
          response( data );
        });
      },
    minLength: 0,//search after two characters
    autoFocus: true, // first item will automatically be focused
    select: function(event,ui){
    	auto_focus_input(this.id);
      }
    }).bind('focus', function(){ $(this).autocomplete("search"); } ).autocomplete( "instance" )._renderItem = function( ul, item ) {
		var auto_suggest_value = highlight_search_text(this.term.trim(), item.value, item.label);
	    return $("<li class='custom-auto-suggest'>")
	            .append('<a>' + auto_suggest_value + '</a>')
	            .appendTo(ul);
	};

    $('#flight_datepicker2, #OWT_adult, #OWT_child, #OWT_infant, #class, #carrier').change(function() {
    	auto_focus_input(this.id);
    });
});	
</script>