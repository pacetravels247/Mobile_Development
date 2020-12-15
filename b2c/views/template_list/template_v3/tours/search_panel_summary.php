<style>
	.topssec::after{display:none;}
</style>
<?php
$adult_count = array_sum($hotel_search_params['adult_config']);
$child_count = array_sum($hotel_search_params['child_config']);
$room_count = $hotel_search_params['room_count'];
?>
<div class="modfictions for_hotel_modi">
	<div class="modinew">
		<div class="container">
			<div class="contentsdw">
				<div class="smldescrptn">
					<div class="col-sm-9 col-xs-10 nopad">
						<div class="col-xs-6 boxpad none_boil_full">
							<h3 class="placenameflt"><?php echo $hotel_search_params['city_name'].' - '.$hotel_search_params['country_name'];?></h3>
						</div>
						<div class="col-xs-3 boxpad none_boil">
							<div class="boxlabl"><span class="faldate fa fa-suitcase"></span>Package Type</div>
							<div class="datein">
								<span class="calinn">Holoday Package</span>
                                
							</div>
						</div>
						<div class="col-xs-3 boxpad none_boil">
							<div class="boxlabl"><span class="faldate fa fa-calendar-o"></span>Duration</div>
							<div class="datein">
								<span class="calinn">
									4 - 7 Days
								</span>
							</div>
						</div>
					</div>
					<div class="col-sm-3 col-xs-2 nopad">
						<div class="col-xs-9 boxpad none_mody">
							<div class="boxlabl">Budjet</div>
							<div class="calinn">1000 - 20000</div>
						</div>
						
						<div class="col-xs-3 boxpad pull-right">
							<a class="modifysrch" data-toggle="collapse" data-target="#modify"></a>
						</div>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="splmodify">
		<div class="container">
			<div id="modify" class="collapse araeinner">
				<div class="insplarea">
					<?php echo $GLOBALS['CI']->template->isolated_view('share/hotel_search') ?>
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