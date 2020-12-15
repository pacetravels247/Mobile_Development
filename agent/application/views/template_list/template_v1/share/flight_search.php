<?php
$flight_datepicker = array(array('flight_datepicker1', FUTURE_DATE), array('flight_datepicker2', FUTURE_DATE));
$this->current_page->set_datepicker($flight_datepicker);
$this->current_page->auto_adjust_datepicker(array(array('flight_datepicker1', 'flight_datepicker2')));
$airline_list = $GLOBALS['CI']->db_cache_api->get_airline_code_list();
?>
<form autocomplete="off" name="flight" id="flight_form" action="<?php echo base_url();?>index.php/general/pre_flight_search" method="get" class="activeForm oneway_frm" style="">
	<div class="row">
		<div class="col-md-6">
			<h2 class="h3">Book Domestic &amp; International Flight Tickets</h2>
		</div>
		<div class="col-md-6 text-right">
			<div class="form-group">
				<label class="radio-inline">
					<input type="radio" name="trip_type" <?=(@$flight_search_params['trip_type'] == 'oneway' ? 'checked="checked"' : '')?> id="onew-trp" value="oneway" checked> One Way
				</label>
				<label class="radio-inline">
					<input type="radio" name="trip_type" <?=(@$flight_search_params['trip_type'] == 'circle' ? 'checked="checked"' : '')?> id="rnd-trp" value="circle"> Round Trip
				</label>
				<label class="radio-inline hide">
					<input type="radio" name="trip_type" <?=(@$flight_search_params['trip_type'] == 'multicity' ? 'checked="checked"' : '')?> id="flight-multicity" value="multicity"> Multicity
				</label>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-4">
			<div class="form-group">
				<label for="flight-from">From</label>
				<div class="input-group">
					<span class="input-group-addon b-r-0 p-0">
					<img src="<?php echo $GLOBALS['CI']->template->template_images('icons/flight-from-icon.png'); ?>" alt="Flight From Icon">
					</span>
					<input type="text" autocomplete="off" name="from" class="auto-focus form-control b-r-0 valid_class fromflight" id="from" placeholder="Departure From" value="<?php echo @$flight_search_params['from'] ?>" required>
				</div>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="form-group">
				<label for="flight-to">To</label>
				<div class="input-group">
					<span class="input-group-addon b-r-0 p-0">
					<img src="<?php echo $GLOBALS['CI']->template->template_images('icons/flight-to-icon.png'); ?>" alt="Flight From Icon">
					</span>
					<input type="text" autocomplete="off" name="to" class="auto-focus form-control b-r-0 valid_class departflight" id="to" placeholder="Arrival To" value="<?php echo @$flight_search_params['to'] ?>" required>
				</div>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="row">
			<div class="col-sm-6 date-wrapper">
			<div class="form-group">
				<label for="flight-departure-date">Departure</label>
				<div class="input-group">
					<input type="text" readonly class="auto-focus hand-cursor form-control b-r-0" id="flight_datepicker1" placeholder="dd-mm-yy" value="<?php echo @$flight_search_params['depature'] ?>" name="depature" required>
					<span class="input-group-addon b-r-0 p-0">
						<label for="flight_datepicker1">
							<img src="<?php echo $GLOBALS['CI']->template->template_images('icons/calendar-main-search-icon.png'); ?>" alt="Calendar Icon">
						</label>
					</span>
				</div>
			</div>
		</div>
		<div class="col-sm-6 date-wrapper">
			<div class="form-group">
				<label for="flight-return-date">Return</label>
				<div class="input-group">
					<input type="text" readonly class="auto-focus hand-cursor form-control b-r-0" id="flight_datepicker2" name="return" placeholder="dd-mm-yy" value="<?php echo @$flight_search_params['return'] ?>" <?=(@$flight_search_params['trip_type'] != 'circle' ? 'disabled="disabled"' : '')?> >
					<span class="input-group-addon b-r-0 p-0">
						<label for="flight_datepicker2">
							<img src="<?php echo $GLOBALS['CI']->template->template_images('icons/calendar-main-search-icon.png'); ?>" alt="Calendar Icon">
						</label>
					</span>
				</div>
			</div>
		</div></div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-2 col-sm-3">
			<div class="form-group">
				<label for="flight-adult">Adult</label>
				<select name="adult" id="OWT_adult" class="auto-focus form-control b-r-0 valid_class">
					<?php echo generate_options(numeric_dropdown(array('size' => 10)), array(@$flight_search_params['adult_config'])); ?>
				</select>
			</div>
		</div>
		<div class="col-lg-2 col-sm-3">
			<div class="form-group">
				<label for="flight-children">Child (< 12 yrs)</label>
				<select name="child" id="OWT_child" name="childs" class="auto-focus form-control b-r-0">
					<option value="0">0</option>
					<?php echo generate_options(numeric_dropdown(array('size' => 10)), array(@$flight_search_params['child_config'])); ?>
				</select>
			</div>
		</div>
		<div class="col-lg-2 col-sm-3">
			<div class="form-group">
				<label for="no_of_infants">Infant (< 2 yrs)</label>
				<select name="infant" id="OWT_infant" class="auto-focus form-control b-r-0">
					<option value="0">0</option>
					<?php echo generate_options(numeric_dropdown(array('size' => 10)), array(@$flight_search_params['infant_config'])); ?>
				</select>
			</div>
		</div>
		<div class="col-lg-2 col-sm-3">
			<div class="form-group">
				<label for="flight-class">Class</label>
				<select name="v_class" id="class" class="auto-focus form-control b-r-0">
					<?php
					$v_class = array('All' => 'Any', 'Economy' => 'Economy', 'Business' => 'Business', 'First' => 'First');
					echo generate_options($v_class, array(@$flight_search_params['v_class']));
					?>
				</select>
			</div>
		</div>
		<div class="col-lg-2 col-sm-3">
			<div class="form-group">
				<label for="flight-class">Airline</label>
				<select name="carrier[]" id="carrier" class="form-control b-r-0">
					<option value="">All</option>
					<?php
					echo generate_options($airline_list, @$flight_search_params['carrier']);
					?>
				</select>
			</div>
		</div>
	</div>
	<div class="clearfix">
		<div class="pull-left alert-wrapper hide">
			<div class="alert alert-danger" role="alert">
			  <strong>Note :</strong> <span class="alert-content"></span>
			</div>
		</div>
		<div class="pull-right">
			<button type="submit" name="search_flight" id="flight-form-submit" class="btn btn-lg btn-i b-r-0 flight_search_btn">Search Flights</button>
		</div>
	</div>
</form>
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
    minLength: 2,//search after two characters
    autoFocus: true, // first item will automatically be focused
    select: function(event,ui){
    	auto_focus_input(this.id);
      }
    });

    $('#flight_datepicker2, #OWT_adult, #OWT_child, #OWT_infant, #class, #carrier').change(function() {
    	auto_focus_input(this.id);
    });
});	
</script>