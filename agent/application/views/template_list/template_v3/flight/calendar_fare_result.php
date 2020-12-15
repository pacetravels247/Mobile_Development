<?php
	//debug($flight_search_params);
	$data['trip_details'] = $flight_search_params;
	$template_images = $GLOBALS['CI']->template->template_images();
	$mini_loading_image = '<div class="text-center loader-image"><img src="'.$template_images.'loader_v3.gif" alt="Loading........"/></div>';
	$loading_image = '<div class="text-center loader-image"><img src="'.$template_images.'loader_v1.gif" alt="Loading........"/></div>';
	$flight_o_direction_icon = '<img src="'.$template_images.'icons/flight-search-result-up-icon.png" alt="Flight Search Result Up Icon">';
	
	?>
<!-- Page Scripts -->
<link href='<?php echo SYSTEM_RESOURCE_LIBRARY;?>/fullcalendar/fullcalendar.css' rel='stylesheet' />
<link href='<?php echo SYSTEM_RESOURCE_LIBRARY;?>/fullcalendar/fullcalendar.print.css' rel='stylesheet' media='print' />
<script src='<?php echo SYSTEM_RESOURCE_LIBRARY;?>/fullcalendar/lib/moment.min.js'></script>
<script src='<?php echo SYSTEM_RESOURCE_LIBRARY;?>/fullcalendar/fullcalendar.min.js'></script>
<section class="search-result">
	<div class="container">
		<?php echo $GLOBALS['CI']->template->isolated_view('share/loader/flight_fare_pre_loader',$data);?>
		<div class="resultalls">
        
			<div class="coleft">
                <div class="flteboxwrp">
                	<div class="filtersho">
                        <div class="avlhtls">
                            <strong>Modify Search</strong>
                        </div>
                    </div>
					<?php echo $GLOBALS['CI']->template->isolated_view('flight/fare_panel_summary'); ?>
                </div>
            </div>
            
            <div class="colrit">
				<div class="insidebosc">
					<!-- FLIGHT FARE SEARCH RESULT START -->
                    <div class="margcalndr">
                        <div  class="allresult fare_cal" id="fare_search_result">
                            <div id="fare_calendar"></div>
                        </div>
                    </div>
					<!-- FLIGHT FARE SEARCH RESULT END -->
				</div>
			</div>
		</div>
	</div>
</section>
<?php
	foreach ($active_booking_source as $t_k => $t_v) {
	  $active_source[] = $t_v['source_id'];
	}
	$active_source = json_encode($active_source);
	?>
<script>
	$(document).ready(function() {
		//********************
		/**
		 *Load Flight - Source Trigger
		 */
		function load_flight_fare_calendar(date) {
			var _active_booking_source = <?=$active_source?>;
			$.each(_active_booking_source, function(k, booking_source_name) {
				core_flight_fare_loader(booking_source_name, date);
			});
		}
		/**
		*Update fare day wise 
		*/
		function load_flight_day_fare(current_date, session_id, thisRef) {
			var _active_booking_source = <?=$active_source?>;
			$.each(_active_booking_source, function(k, booking_source_name) {
				core_flight_day_fare_loader(booking_source_name, current_date, session_id, thisRef);
			});
		}

		function get_date_cache_key(date)
		{
			date = date.split('-');
			date = parseInt(date[0])+''+parseInt(date[1])+''+parseInt(date[2]);
			return date;
		}

		/**
		 *Load Flight Fare Core Ajax
		 */
		 var fare_cache = {};
		 var fare_session = {};
		function core_flight_fare_loader(booking_source_id, departure) {
			var search_params = <?php echo json_encode($flight_search_params)?>;
			search_params['depature'] = departure || search_params['depature'];
			departure = get_date_cache_key(search_params['depature']);
			if ((departure in fare_cache) == false) {
				$('.result-pre-loader').show();
				$.ajax({
					type: 'POST',
					url: app_base_url+'index.php/ajax/fare_list/'+booking_source_id+'?'+jQuery.param(search_params),
					async: true,
					cache: true,
					dataType: 'json',
					success: function(result) {
						$('.result-pre-loader').hide();
						if (result.hasOwnProperty('status') == true && result.status == true) {
							if ((departure in fare_cache) == false) {
								fare_cache[departure] = result.data.day_fare_list;
								fare_session[departure] = result.next_search;
								update_fare_calendar_events(departure);
							}
						} else {
							//No Result Found
							//alert(result.msg);
						}
						hide_result_pre_loader();
					}
				});
			} else {
				//update_fare_calendar_events(departure);
			}
		}
		function core_flight_day_fare_loader(booking_source_id, departure, session, thisRef) {
			if (session != '') {
				$('.result-pre-loader').show();
				//thisRef.css('opacity', '.3');
				var search_params = <?php echo json_encode($flight_search_params)?>;
				search_params['session_id'] = session;
				search_params['depature'] = departure || search_params['depature'];
				departure = search_params['depature'];
				$.ajax({
					type: 'POST',
					url: app_base_url+'index.php/ajax/day_fare_list/'+booking_source_id+'?'+jQuery.param(search_params),
					async: false,
					cache: true,
					dataType: 'json',
					success: function(result) {
						$('.result-pre-loader').hide();
						if (result.hasOwnProperty('status') == true && result.status == true) {
							fare_cache[result.data.departure] = result.data.day_fare_list;
							fare_session[result.data.departure] = result.next_search;
							update_fare_calendar_events(result.data.departure);
						} else {
							//No Result Found
							//alert(result.msg);
						}
						hide_result_pre_loader();
					}
				});
			}
		}

		

		function update_fare_calendar_events(date)
		{
			$("#fare_calendar").fullCalendar('addEventSource', fare_cache[date]);
		}

		function getSortedObject(obj) {
			var objValArray = getArray(obj);
			var sortObj = {};
			objValArray.sort();
			$.each(objValArray, function(obj_key, obj_val) {
				$.each(obj, function(i_k, i_v) {
					if (i_v == obj_val) {
						sortObj[i_k] = i_v;
					}
				});
			});
			return sortObj;
		}

		function loader(selector) {
			selector = selector || '#fare_calendar';
			$(selector).animate({
				'opacity': '.1'
			});
			setTimeout(function() {
				$(selector).animate({
					'opacity': '1'
				}, 'slow');
			}, 1000);
		}

		$('.loader').on('click', function(e) {
			e.preventDefault();
			loader();
		});
		//-------------------------************************************* Calendar Events
		//load default calendar with scheduled query
		function load_calendar(event_list)
		{
			$('#fare_calendar').fullCalendar({
				header: {
				 	left:   'prev',
				    center: 'title',
				    right:  'next'
				},
				eventClick: function (calEvent, jsEvent, view) {
					$('#calendar').fullCalendar('removeEvents', calEvent._id);
				},
				defaultDate: '<?=$flight_search_params['depature']?>',
				buttonText: {
					prev: 'previous',
					next: 'next'
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
					if (event.hasOwnProperty('event_date') == true) {
						element.attr('data-event-date', event.event_date);
					}
					if (event.hasOwnProperty('session_id') == true) {
						element.attr('data-session-id', event.session_id);
					}
				},
				viewRender : function (currentView) {
					var minDate = moment();
					currentView.title = 'Cheapest Flights - '+currentView.title;
					var current_month = currentView.start._d;
					var event_start_date = '';
					// Past
					if (minDate >= currentView.start && minDate <= currentView.end) {
						$(".fc-prev-button").prop('disabled', true); 
						$(".fc-prev-button").addClass('fc-state-disabled');
						var today = new Date();
						event_start_date = today.getFullYear()+'-0'+(today.getMonth()+1)+'-'+today.getDate();
						
					} else {
						$(".fc-prev-button").removeClass('fc-state-disabled'); 
						$(".fc-prev-button").prop('disabled', false);
						event_start_date = $('#fare_calendar').fullCalendar('getDate')._d;
						event_start_date = event_start_date.getFullYear()+'-0'+(event_start_date.getMonth()+1)+'-'+event_start_date.getDate();
					}
					
					load_flight_fare_calendar(event_start_date);
				}
			});
		}

		//Update Event Day wise
		$(document).on('click', '.update-day-fare', function(e) {
			e.preventDefault();
			var current_date = $(this).data('event-date');
			var session_id = $(this).data('session-id');
			$('.fc-content .fc-title', this).text('Please Wait!!!');
			$(this).remove();
			load_flight_day_fare(current_date, session_id);
		});
	
	//load flight from active sources
	load_calendar();
	show_result_pre_loader();
		//On Click, Make a search -- Balu A
		$(document).on('click', '.search-day-fare', function(e){
			e.preventDefault();
			var from = $('#from').val().trim();
			var to = $('#to').val().trim();
			var depature = $(this).data('event-date');
			window.location.href = app_base_url+'flight/pre_fare_search_result?from='+from+'&to='+to+'&depature='+depature;
		});
	});
</script>