<div class="row">
            <div class="col-md-3">
              <div class="box box-solid">
                <div class="box-header with-border bg-purple">
                  <h4 class="box-title"><i class="fa fa-calendar"></i> Trip Calendar</h4>
                </div>
                <div class="box-body">
                  <!-- the events -->
                  <div id="external-events">
                    <div class="active-event external-event">Upcoming Trips</div>
                    <div class="inactive-event external-event">Past Trip</div>
                  </div>
                </div><!-- /.box-body -->
              </div><!-- /. box -->
            </div><!-- /.col -->
            <div class="col-md-9">
              <div class="box box-primary">
                <div class="box-body no-padding">
                  <!-- THE CALENDAR -->
                  <div id='booking-calendar'></div>
                </div><!-- /.box-body -->
              </div><!-- /. box -->
            </div><!-- /.col -->
          </div>
<script>
$(document).ready(function() {
	var event_list = {};
	function enable_default_calendar_view()
	{
		load_calendar('');
		get_event_list();
		set_event_list();
		$('[data-toggle="tooltip"]').tooltip();
	}
	function reset_calendar()
	{
		$("#booking-calendar").fullCalendar('removeEvents');
		get_event_list();
		set_event_list();
	}
	//Reload Events
	setInterval(function(){
		reset_calendar();
		$('[data-toggle="tooltip"]').tooltip();
	}, <?php echo SCHEDULER_RELOAD_TIME_LIMIT; ?>);
	
	enable_default_calendar_view();
	//sets all the events
	function get_event_list()
	{
		set_booking_event_list();
	}
	//loads all the loaded events
	function set_event_list()
	{
		$("#booking-calendar").fullCalendar('addEventSource', event_list.booking_event_list);
		if ("booking_event_list" in event_list && event_list.booking_event_list.hasOwnProperty(0)) {
			focus_date("<?=date('Y-m-d')?>");
		}
	}
	
	//getting the value of arrangment details
	function set_booking_event_list()
	{
		$.ajax({
			url:app_base_url+"index.php/ajax/trip_events",
			async:false,
			success:function(response){
				event_list.booking_event_list = response.data;
			}
		});
	}
	
	//load default calendar with scheduled query
	function load_calendar(event_list)
	{
		$('#booking-calendar').fullCalendar({
			header: {
				center: 'title'
			},
			//defaultDate: '2014-11-12', 
			editable: false,
			eventLimit: false, // allow "more" link when too many events
			events: event_list,
			eventRender: function(event, element) {
				element.attr('data-toggle', 'tooltip');
				element.attr('data-placement', 'bottom');
				element.attr('title', event.tip);
				element.attr('id', event.optid);
				element.find('.fc-time').attr('class',"hide"); 
				element.attr('class', event.add_class+' fc-day-grid-event fc-event fc-start fc-end');
				element.attr('href', event.href);
				element.attr('target', '_blank');
				element.css({'font-size':'10px', 'padding':'1px'});
				if (event.prepend_element) {
					element.prepend(event.prepend_element);
				}
			},
			eventDrop : function (event, delta) {
				event.end = event.end || event.start;
				if (event.start && event.end) {
					update_event_list(event.optid, event.start.format(), event.end.format());
					focus_date(event.start.format());
				} else {
					reset_calendar();
				}
			}
		});
	}
	function focus_date(date)
	{
		$('#booking-calendar').fullCalendar('gotoDate', date);
	}

	$(document).on('click', '.event-hand', function() {

	});
});
</script>
<link defer href='<?php echo SYSTEM_RESOURCE_LIBRARY;?>/fullcalendar/fullcalendar.css' rel='stylesheet' />
<link defer href='<?php echo SYSTEM_RESOURCE_LIBRARY;?>/fullcalendar/fullcalendar.print.css' rel='stylesheet' media='print' />
<script defer src='<?php echo SYSTEM_RESOURCE_LIBRARY;?>/fullcalendar/lib/moment.min.js'></script>
<script defer src='<?php echo SYSTEM_RESOURCE_LIBRARY;?>/fullcalendar/fullcalendar.min.js'></script>