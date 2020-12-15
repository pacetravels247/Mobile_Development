<?php
$bus_datepicker = array(array('bus-date-1', FUTURE_DATE), array('bus-date-2', FUTURE_DATE));
$GLOBALS['CI']->current_page->set_datepicker($bus_datepicker);
$GLOBALS['CI']->current_page->auto_adjust_datepicker(array(array('bus-date-1', 'bus-date-2')));
?>
	<form autocomplete="off" name="bus" id="bus_form" action="<?php echo base_url();?>index.php/general/pre_bus_search" method="get" class="activeForm oneway_frm" style="">
		<div class="row">
			<div class="col-md-6">
				<h2 class="h3 txtwrapRow">Book Online Bus Ticket Across India</h2>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6">
				<div class="form-group">
					<label for="bus-station-from">Travelling From</label>
					<div class="input-group">
						<span class="input-group-addon b-r-0 p-0">
						<img src="<?php echo $GLOBALS['CI']->template->template_images('icons/bus-from-to-icon.png'); ?>" alt="Bus From Icon">
						</span>
						<input type="text" autocomplete="off" name="bus_station_from" class="bus-station auto-focus form-control b-r-0 valid_class bus-station-from" id="bus-station-from" placeholder="From" value="<?php echo @$bus_search_params['bus_station_from'] ?>" required>
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="form-group">
					<label for="bus-station-to">Travelling To</label>
					<div class="input-group">
						<span class="input-group-addon b-r-0 p-0">
						<img src="<?php echo $GLOBALS['CI']->template->template_images('icons/bus-from-to-icon.png'); ?>" alt="Bus To Icon">
						</span>
						<input type="text" autocomplete="off" name="bus_station_to" class="bus-station auto-focus form-control b-r-0 valid_class bus-station-to" id="bus-station-to" placeholder="To" value="<?php echo @$bus_search_params['bus_station_to'] ?>" required>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-3 date-wrapper">
				<div class="form-group">
					<label for="bus-date-1">Leaving On</label>
					<div class="input-group">
						<input type="text" readonly class="auto-focus hand-cursor form-control b-r-0" id="bus-date-1" placeholder="dd-mm-yy" value="<?php echo @$bus_search_params['bus_date_1'] ?>" name="bus_date_1" required>
						<span class="input-group-addon b-r-0 p-0">
							<label for="bus-date-1">
								<img src="<?php echo $GLOBALS['CI']->template->template_images('icons/calendar-main-search-icon.png'); ?>" alt="Calendar Icon">
							</label>
						</span>
					</div>
				</div>
			</div>
			<div class="col-sm-3 date-wrapper hide">
				<div class="form-group">
					<label for="bus-date-2">Returning On<small>(Optional)</small></label>
					<div class="input-group">
						<input type="text" readonly class="disable-date-auto-update hand-cursor form-control b-r-0" id="bus-date-2" name="bus_date_2" placeholder="dd-mm-yy" value="<?php echo @$bus_search_params['bus_date_2'] ?>" >
						<span class="input-group-addon b-r-0 p-0">
							<label for="bus-date-2">
								<img src="<?php echo $GLOBALS['CI']->template->template_images('icons/calendar-main-search-icon.png'); ?>" alt="Calendar Icon">
							</label>
						</span>
					</div>
				</div>
			</div>
		</div>
		<div class="clearfix">
			<div class="pull-left alert-wrapper hide">
				<div class="alert alert-danger" role="alert">
				  <strong>Note :</strong> <span class="alert-content"></span>
				</div>
			</div>
			<div class="pull-right srchBtnmob">
				<button type="submit" name="search_bus" id="bus-form-submit" class="bus_srchBtn btn btn-lg btn-i b-r-0 bus_search_btn">Search Buses</button>
			</div>
		</div>
	</form>

<script>
	$(document).ready(function(){
		var cache = {};
		$(".bus-station").autocomplete({
			source:  function( request, response ) {
				var term = request.term;
				if ( term in cache ) {
					response( cache[ term ] );
					return;
				}

				$.getJSON( app_base_url+"index.php/ajax/bus_stations", request, function( data, status, xhr ) {
					cache[ term ] = data;
					response( data );
				});
			},
			minLength: 2,//search after two characters
			autoFocus: true,
			select: function(event,ui) {
				auto_focus_input(this.id);
			}
		});

		$('#bus-station-from, #bus-station-to, #bus-date-1').change(function() {
	    	auto_focus_input(this.id);
	    });
	});
</script>