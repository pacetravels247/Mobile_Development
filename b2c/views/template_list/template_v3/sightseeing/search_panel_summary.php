<style>
	.topssec::after{display:none;}
</style>
<?php


$destination = @$sight_seen_search_params['destination'];

?>
<div class="modfictions for_hotel_modi">
	<div class="modinew">
		<div class="container">
			<div class="contentsdw">
				<div class="smldescrptn">
					<div class="col-sm-8 col-xs-10 nopad">
						<div class="col-xs-6 boxpad none_boil_full">
							<h3 class="placenameflt"><?php echo @$destination?></h3>
						</div>
						<?php if(@$sight_seen_search_params['from_date']):?>
							<div class="col-xs-3 boxpad none_boil">
								<div class="boxlabl"><span class="faldate fa fa-calendar-o"></span>Travel Begin</div>
								<div class="datein">
									<span class="calinn"><?php echo app_friendly_absolute_date(@$sight_seen_search_params['from_date']); ?></span>
	                                
								</div>
							</div>
						<?php endif;?>
						<?php if(@$sight_seen_search_params['to_date']):?>
						<div class="col-xs-3 boxpad none_boil">
							<div class="boxlabl"><span class="faldate fa fa-calendar-o"></span>Travel End</div>
							<div class="datein">
								<span class="calinn">
									<?php echo app_friendly_absolute_date(@$sight_seen_search_params['to_date']); ?>
								</span>
							</div>
						</div>
						<?php endif;?>
					</div>
					<div class="col-sm-4 col-xs-2 nopad">
						
						<div class="col-xs-4 boxpad pull-right">
							<a class="modifysrch" data-toggle="collapse" data-target="#modify"><span class="mdyfydsktp">Modify Search</span><i class="fa fa-angle-down mobresdv" aria-hidden="true"></i></a>
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
					<?php echo $GLOBALS['CI']->template->isolated_view('share/sightseeing_search') ?>
				</div>
			</div>
		</div>
	</div>
</div>