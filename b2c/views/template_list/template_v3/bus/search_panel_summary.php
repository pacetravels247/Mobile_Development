<?php //Images Url
$template_images = $GLOBALS['CI']->template->template_images();
?>
<style>
.topssec::after {
	display:none;
}
</style>
<div class="modfictions">
	<div class="modinew">
	<div class="container">
		<div class="contentsdw">
		<div class="smldescrptn">
			<div class="col-xs-10 nopad">
			<div class="col-xs-4 boxpad full_bus_sec">
				<h4 class="contryname">From</h4>
				<h3 class="placenameflt"><?=$bus_search_params['bus_station_from']?></h3>
			</div>
			<div class="col-xs-4 boxpad full_bus_sec">
				<h4 class="contryname">To</h4>
				<h3 class="placenameflt"><?=$bus_search_params['bus_station_to']?></h3>
			</div>
			<div class="col-xs-4 boxpad full_bus_none">
				<div class="boxlabl"><span class="faldate fa fa-calendar-o"></span>Journey Date</div>
				<div class="datein"><span class="calinn"><?=date('jS \ M Y',strtotime($bus_search_params['bus_date_1']))?></span></div>
			</div>
			</div>
			<div class="col-xs-2 boxpad"><a data-target="#modify" data-toggle="collapse" class="modifysrch"><span class="mdyfydsktp">Modify Search</span><i class="fa fa-angle-down mobresdv" aria-hidden="true"></i></a></div>
		</div>
		<div class="clearfix"></div>
		</div>
	</div>
	</div>
	<div class="splmodify">
	<div class="container">
		<div id="modify" class="collapse araeinner">
		<div class="insplarea">
			<?php echo $GLOBALS['CI']->template->isolated_view('share/bus_search') ?>
		</div>
		</div>
	</div>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$('.modifysrch').click(function(){
		$(this).stop( true, true ).toggleClass('up');
		$('.search-result').stop( true, true ).toggleClass('flightresltpage');
		$('.modfictions').stop( true, true ).toggleClass('fixd');
	});
});
</script>
