<div class="row">
	<div class="col-md-12">
		<div id='flight-top-search' class="col-md-12">
		</div>
	</div>
</div>
<script>
$(function () {
    $('#flight-top-search').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'Top Flight Destination'
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
            name: 'Flight Top Search',
            data: <?=$flight_top_search?>,
            color : '#0073b7',
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