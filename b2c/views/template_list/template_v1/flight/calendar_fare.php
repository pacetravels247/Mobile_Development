<?php
	//debug($flight_search_params);
	$data['trip_details'] = $flight_search_params;
	
	$template_images = $GLOBALS['CI']->template->template_images();
	$mini_loading_image = '<div class="text-center loader-image"><img src="'.$template_images.'loader_v3.gif" alt="Loading........"/></div>';
	$loading_image = '<div class="text-center loader-image"><img src="'.$template_images.'loader_v1.gif" alt="Loading........"/></div>';
	$flight_o_direction_icon = '<img src="'.$template_images.'icons/flight-search-result-up-icon.png" alt="Flight Search Result Up Icon">';
	echo $GLOBALS['CI']->template->isolated_view('flight/fare_panel_summary');
	?>
<!-- Page Scripts -->
<section class="search-result">
	<div class="container">
		<?php echo $GLOBALS['CI']->template->isolated_view('share/loader/flight_fare_pre_loader',$data);?>
		<div class="resultalls">
			<div class="colrit">
				<div class="insidebosc">
                    <!-- FLIGHT FARE SEARCH RESULT START -->
					<div  class="allresult" id="flight_search_result">
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
		/** PAGE FUNCTIONALITY STARTS HERE **/
		/**
		 *Load Data - Source Trigger
		 */
		function load_flight() {
			var _active_booking_source = <?=$active_source?>;
			$.each(_active_booking_source, function(k, booking_source_name) {
				core_flight_loader(booking_source_name);
			});
		}

		/**
		 *Load Flight Core Ajax
		 */
		function core_flight_loader(booking_source_id) {
			$('.loader-image').show();
			$.ajax({
				type: 'GET',
				url: app_base_url+'index.php/ajax/flight_list/'+booking_source_id+'?<?php echo http_build_query($flight_search_params) ?>',
				async: true,
				cache: false,
				dataType: 'json',
				success: function(result) {
					$('.loader-image').hide();
					if (result.hasOwnProperty('status') == true && result.status == true) {
						$('#flight_search_result').append(result.data);
						hide_result_pre_loader();
					}
				}
			});
		}

		//load flight from active sources
		show_result_pre_loader();
		load_flight();

		function loader(selector) {
			selector = selector || '#flight_search_result';
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
	});
</script>