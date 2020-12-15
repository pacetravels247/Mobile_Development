<?php
$active_domain_modules = $this->active_domain_modules;
$tiny_loader = $GLOBALS['CI']->template->template_images('tiny_loader_v1.gif');
$tiny_loader_img = '<img src="'.$tiny_loader.'" class="loader-img" alt="Loading">';
$booking_summary = array();
?>
<div class="panel panel-default">
	<div class="panel-body">
		<div class="row">
			<div class="col-md-12">
				<div id='year-search-summary' class="col-md-12">
				//Year Search Summary - Module Wise
				</div>
			</div>
		</div>
	</div>
</div>
<script src="<?php echo SYSTEM_RESOURCE_LIBRARY; ?>/Highcharts/js/highcharts.js" defer></script>
<script src="<?php echo SYSTEM_RESOURCE_LIBRARY; ?>/Highcharts/js/modules/exporting.js" defer></script>
<script>
$(function () {
    $('#year-search-summary').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'Monthly Search Hits'
        },
        subtitle: {
            text: '<?=$year_start.'-'.$year_end?>'
        },
        xAxis: {
            categories: <?=$monthly_time_line_interval?>
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Search Hits'
            },
            stackLabels: {
                enabled: true,
                style: {
                    fontWeight: 'bold',
                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                }
            }
        },
        legend: {
            align: 'right',
            x: -30,
            verticalAlign: 'top',
            y: 25,
            floating: true,
            backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
            borderColor: '#CCC',
            borderWidth: 1,
            shadow: false
        },
        tooltip: {
            headerFormat: '<b>{point.x} Search hits</b><br/>',
            pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal} Search Hits'
        },
        plotOptions: {
            column: {
                stacking: 'normal',
                dataLabels: {
                    enabled: true,
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                    style: {
                        textShadow: '0 0 3px black'
                    }
                }
            }
        },
        series: <?=$monthly_series_data?>
    });
});
</script>