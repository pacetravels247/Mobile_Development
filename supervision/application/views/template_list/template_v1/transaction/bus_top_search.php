<div class="row">
	<div class="col-md-12">
		<div id='bus-top-search' class="col-md-12">
		</div>
	</div>
</div>
<script>
$(function () {
    $('#bus-top-search').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'Top Bus Destination'
        },
        subtitle: {
            text: 'Location-Location(<?=$year_start.'-'.$year_end?>)'
        },
        xAxis: {
            type: 'category',
            labels: {
                rotation: -45,
                style: {
                    fontSize: '10px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Search Hits'
            }
        },
        legend: {
            enabled: false
        },
        tooltip: {
            pointFormat: 'Search Hits : <b>{point.y:1f}</b>'
        },
        series: [{
            name: 'Bus Top Search',
            data: <?=$bus_top_search?>,
            color: '#dd4b39',
            dataLabels: {
                enabled: true,
                rotation: -90,
                color: '#000000',
                align: 'right',
                format: '{point.y:1f} Hits',
                y: -40, // 40 pixels up from the top
                style: {
                    fontSize: '10px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        }]
    });
});
</script>
<hr>