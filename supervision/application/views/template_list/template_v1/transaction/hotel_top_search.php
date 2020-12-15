<div class="row">
	<div class="col-md-12">
		<div id='hotel-top-search' class="col-md-12">
		</div>
	</div>
</div>

<script>
$(function () {
    $('#hotel-top-search').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'Top Hotel Destination'
        },
        subtitle: {
            text: '<?=$year_start.'-'.$year_end?>'
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
            name: 'Hotel Top Search',
            data: <?=$hotel_top_search?>,
            color: '#00a65a',
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