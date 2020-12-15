<div class="container-fluid">
	<h3>Timeline</h3>
	<ul class="timeline-wrapper timeline" id="timeline-list">
		
	</ul>
	<div id="event_bottom_chain">
		<div style="" class="data-utility-loader text-center">
			<span><span/>Please Wait <img class="img-responsive center-block" src="<?=$GLOBALS['CI']->template->template_images('loader_v3.gif')?>">
		</div>
	</div>
</div>
<script>
$(document).ready(function() {
	function load_timeline(_event_start, event_limit)
	{
		load_event_bottom_chain = false;
		$.get(app_base_url+'index.php/utilities/timeline_rack?oe_start='+_event_start+'&oe_limit='+event_limit, function(response) {
			if (response.status == false) {
				load_event_bottom_chain = false;
				$('#event_bottom_chain').hide();
				$('#timeline-list').append('<li><i class="fa fa-clock-o bg-gray"></i></li>');
			} else {
				$('#timeline-list').append(response.oe_list);
				load_event_bottom_chain = true;
				event_start = (event_start+event_limit);
				lazy_loader();
				adjust_time_label();
			}
		});
	}
	function adjust_time_label()
	{
		var time_head_list = {};
		var _cur_label_id = '';
		var _pre_label_id = '';
		var event_stamp = 'rt_list'+(new Date()).getTime();
		$('.time-label').each(function(k, v) {
			cur_ele = $(this);
			_cur_label_id = this.id;
			if (_cur_label_id == _pre_label_id) {
				$(this).fadeOut(3000).addClass(event_stamp);
			} else {
				_pre_label_id = _cur_label_id;
			}
		});
		setTimeout(function() {
			$('.'+event_stamp).remove();
			lazy_loader();
		}, 3000);
	}
	//Load timeline events
	$(window).scroll(function() {
		lazy_loader();
	});

	function lazy_loader()
	{
		if (isVisibleOnViewPort('#event_bottom_chain') == true && load_event_bottom_chain == true) {
			load_timeline(event_start, event_limit);
		}
	}
	function isVisibleOnViewPort(elem)
	{
		var $elem = $(elem);
		var $window = $(window);

		var docViewTop = $window.scrollTop();
		var docViewBottom = docViewTop + $window.height();

		var elemTop = $elem.offset().top;
		var elemBottom = elemTop + $elem.height();

		return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
	}

	//
	var event_start = 0;
	var event_limit = 20;
	var load_event_bottom_chain = true;
	lazy_loader(event_start, event_limit);

	var os_event_list = function() {
		var _latest_event_id = parseInt($('.event-origin:first').data('event-id'));
		//_latest_event_id = 50;
		if (_latest_event_id > 0) {
			$.ajax({
				url: app_base_url+'index.php/utilities/latest_timeline_events?last_event_id='+_latest_event_id,
				success: function(response) {
					if (response.status) {
						$('#timeline-list').prepend(response.oa_list);
						adjust_time_label();
					}
				},

				complete: function(){setTimeout(os_event_list, 500);}
			});
		}
	};
	var interval = setTimeout(os_event_list, 5000);
});
</script>