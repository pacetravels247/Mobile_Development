<!-- HTML BEGIN -->
<div class="bodyContent">
<?php echo $this->session->flashdata("msg"); ?>
	<div class="panel panel-default clearfix"><!-- PANEL WRAP START -->
		<div class="panel-heading"><!-- PANEL HEAD START -->
			<div class="panel-title align-center"><i class="fa fa-shield"></i> Booking Details</div>
		</div>
		<!-- PANEL HEAD START -->
		<div class="panel-body"><!-- PANEL BODY START -->
			<div class="">
				<div class="panel panel-default">
			    <div class="panel-body">
			        <div class="row">
			            <div class="col-md-12">
			                <div id='booking-calendar' class="">

			                </div>
			            </div>
			        </div>
			    </div>
			</div>
			</div>
		</div><!-- PANEL BODY END -->
	</div><!-- PANEL END -->
</div>

<?php
Js_Loader::$css[] = array('href' => SYSTEM_RESOURCE_LIBRARY . '/fullcalendar/fullcalendar.css', 'media' => 'screen');
Js_Loader::$css[] = array('href' => SYSTEM_RESOURCE_LIBRARY . '/fullcalendar/fullcalendar.print.css', 'media' => 'print');
Js_Loader::$js[] = array('src' => SYSTEM_RESOURCE_LIBRARY . '/fullcalendar/lib/moment.min.js', 'defer' => 'defer');
Js_Loader::$js[] = array('src' => SYSTEM_RESOURCE_LIBRARY . '/fullcalendar/fullcalendar.min.js', 'defer' => 'defer');
?>

<script type="text/javascript">
	$(document).ready(function() {
		
    var event_list = {};
    function enable_default_calendar_view()
    {
    load_calendar('');
    get_event_list();
    set_event_list();
    $('[data-toggle="tooltip"]').tooltip();
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
    //focus_date(event_list.booking_event_list[0]['start']);
    }
    }

    //getting the value of arrangment details
    function set_booking_event_list()
    {
    $.ajax({
    url:app_base_url + "index.php/ajax/booking_events",
            async:false,
            success:function(response){
            //console.log(response)
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
            element.find('.fc-time').attr('class', "hide");
            element.attr('class', event.add_class + ' fc-day-grid-event fc-event fc-start fc-end');
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
    });


</script>
