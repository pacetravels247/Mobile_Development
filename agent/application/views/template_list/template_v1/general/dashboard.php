<?php
$active_domain_modules = $this->active_domain_modules;
$tiny_loader = $GLOBALS['CI']->template->template_images('tiny_loader_v1.gif');
$tiny_loader_img = '<img src="'.$tiny_loader.'" class="loader-img" alt="Loading">';
$booking_summary = array();
?>
<div class="container-fluid">
	<div class="row">
			<?php if (is_active_airline_module()) { ?>
			<div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon flight-l-bg"><i class="fa fa-plane"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Flight Booking</span>
                  <span class="info-box-number <?=META_AIRLINE_COURSE?>"><?=$flight_booking_count?></span>
                  <a href="<?=base_url()?>index.php/report/flight" class="">more info
                  </a>
                </div><!-- /.info-box-content -->
              </div><!-- /.info-box -->
            </div>
            <?php } ?>
            <?php if (is_active_hotel_module()) { ?>
            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon hotel-l-bg"><i class="fa fa-bed"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Hotel Booking</span>
                  <span class="info-box-number <?=META_ACCOMODATION_COURSE?>"><?=$hotel_booking_count?></span>
                  <a href="<?=base_url()?>index.php/report/hotel" class="">more info
                  </a>
                </div><!-- /.info-box-content -->
              </div><!-- /.info-box -->
            </div>
            <?php } ?>
            <?php if (is_active_bus_module()) { ?>
            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bus-l-bg"><i class="fa fa-bus"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Bus Booking</span>
                  <span class="info-box-number <?=META_BUS_COURSE?>"><?=$bus_booking_count?></span>
                  <a href="<?=base_url()?>index.php/report/bus" class="">more info
                  </a>
                </div><!-- /.info-box-content -->
              </div><!-- /.info-box -->
            </div>
            <?php } ?>
            <?php if (is_active_package_module()) { ?>
            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="fa fa-suitcase"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Holiday Enquiry</span>
                  <a href="<?=base_url()?>index.php/report/package" class="">more info
                  </a>
                </div><!-- /.info-box-content -->
              </div><!-- /.info-box -->
            </div>
            <?php } ?><!--
            <div class="col-md-1 col-sm-6 col-xs-12">
	            <div class="info-box">
	                <span class="info-box-icon bg-primary"><i class="fa fa-user text-success"></i></span>
	                <div class="info-box-content">
	                  <span class="info-box-text">Online</span>
	                  <span class="info-box-number <?=META_BUS_COURSE?>"><?=intval(@$total_online_user->total)?></span>
	                </div> /.info-box-content 
	              </div> /.info-box 
			</div>
	--></div>
</div>
<div class="panel panel-default">
	<div class="panel-body">
		<div class="row">
			<div class="col-md-6">
				<div id='booking-calendar' class="">
				</div>
			</div>
			<div class="col-md-6">
				<div id='booking-timeline' class="">
				</div>
			</div>
		</div>
	</div>
</div>
<hr>
<div class="panel panel-default">
	<div class="panel-body">
		<div class="row">
			<div class="col-md-12">
				<div id='booking-summary' class="col-md-12">
				</div>
			</div>
		</div>
	</div>
</div>
<hr>
<div class="panel panel-default">
	<div class="panel-body">
		<div class="row">
<?php
$latest_trans_list = '';
$latest_trans_summary = '';
if (valid_array($latest_transaction)) {
	foreach ($latest_transaction as $k => $v) {
		$latest_trans_list .= '<li class="item">';
			$latest_trans_list .= '<div class="product-img image"><i class="'.get_arrangement_icon(module_name_to_id($v['transaction_type'])).'"></i></div>';
			$latest_trans_list .= '<div class="product-info">
									<a class="product-title" href="">
										'.$v['app_reference'].' -'.app_friendly_day($v['created_datetime']).' <span class="label label-primary pull-right"><i class="fa fa-inr"></i> '.($v['total_fare']+$v['profit']).'</span>
									</a>
									<span class="product-description">
										'.$v['remarks'].'
									</span>
								</div>';
		$latest_trans_list .= '</li>';
	}
}
?>
			<div class="col-md-12">
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">Recent Booking Transactions</h3>
					</div>
					<div class="box-body">
						<ul class="products-list product-list-in-box">
							<?=$latest_trans_list?>
						</ul>
					</div>
					<div class="box-footer text-center">
						<a class="uppercase" href="<?=base_url().'index.php/transaction/logs'?>">View All Transactions</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="<?php echo SYSTEM_RESOURCE_LIBRARY; ?>/Highcharts/js/highcharts.js"></script>
<script src="<?php echo SYSTEM_RESOURCE_LIBRARY; ?>/Highcharts/js/modules/exporting.js"></script>
<script>
$(function () {
	//LEAD REPORT -line graph
    $('#booking-timeline').highcharts({
    	credits: {
             enabled: false
        }, 
        chart: {
            type: 'spline'
        },      
        title: {
            text: 'Booking Details',
            x: -20 //center
        },
        subtitle: {
            text: '',
            x: -20
        },
        xAxis: {
            categories: <?php echo json_encode($time_line_interval); ?>,
            		tickPixelInterval: 0
        },
        yAxis: {
        	allowDecimals: false,
        	min: 0, 
        	max: <?php echo $max_count;?>,
            title: {
                text: '<?php echo 'No Of Booking'; ?>'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        tooltip: {
            valueSuffix: ''
        },
        legend: {
        	title: {
            text: 'No Of Booking'
            },
            subtitle: {
                text: 'count'
            },
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0,
            labelFormatter: function() {
                var total = 0;
                var total_face_value = this.userOptions.total_earned || 0;
                for(var i=this.yData.length; i--;) {
                    total += this.yData[i];
                };
                
                return this.name + '(' + total + ')';
             }            
           
        },
        series: <?php echo json_encode($time_line_report);?>,
        navigation: {
            buttonOptions: {
                align: 'right', 
                verticalAlign: 'top', 
                 x: 0, 
                 y: 0
            }
        }
       
    });

        $('#booking-summary').highcharts({
            title: {
                text: 'Monthly Recap Report'
            },
            xAxis: {
                categories: <?php echo json_encode($time_line_interval); ?>
            },
            yAxis: {
            	allowDecimals: false,
                title: {
                    text: 'Profit In <?php echo COURSE_LIST_DEFAULT_CURRENCY_VALUE?>'
                }
            },
            labels: {
                items: [{
                    html: 'Total Profit Earned in <?php echo COURSE_LIST_DEFAULT_CURRENCY_VALUE?>',
                    style: {
                        left: '50px',
                        top: '18px',
                        color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                    }
                }]
            },
            series: [<?php echo json_encode($group_time_line_report[0]);?>,
            	<?php echo json_encode($group_time_line_report[1]);?>,
            	<?php echo json_encode($group_time_line_report[2]);?>,
            	<?php echo json_encode($group_time_line_report[3]);?>,
               {
                type: 'pie',
                name: 'Total Earning',
                data: <?php echo json_encode($module_total_earning)?>,
                center: [100, 80],
                size: 100,
                showInLegend: false,
                dataLabels: {
                    enabled: false
                }
            }]
        });
});
</script>
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
			//focus_date(event_list.booking_event_list[0]['start']);
		}
	}
	
	//getting the value of arrangment details
	function set_booking_event_list()
	{
		$.ajax({
			url:app_base_url+"index.php/ajax/booking_events",
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
<style>
.fc-day-number {
    font-size: inherit;
    font-weight: inherit;
    padding-right: 10px;
}
</style>
<link href='<?php echo SYSTEM_RESOURCE_LIBRARY;?>/fullcalendar/fullcalendar.css' rel='stylesheet' />
<link href='<?php echo SYSTEM_RESOURCE_LIBRARY;?>/fullcalendar/fullcalendar.print.css' rel='stylesheet' media='print' />
<script src='<?php echo SYSTEM_RESOURCE_LIBRARY;?>/fullcalendar/lib/moment.min.js'></script>
<script src='<?php echo SYSTEM_RESOURCE_LIBRARY;?>/fullcalendar/fullcalendar.min.js'></script>