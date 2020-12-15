<?php //Images Url
$template_images = $GLOBALS['CI']->template->template_images();
if($flight_search_params['trip_type'] == 'multicity') {
	$flight_search_params['depature'] = $flight_search_params['depature'][0];
	$flight_search_params['from'] = $flight_search_params['from'][0];
	$flight_search_params['to'] = end($flight_search_params['to']);
}
?>
<style>
	.topssec::after{display:none;}
</style>
<div class="modfictions">
	<div class="modinew">
		<div class="container-fluid">
			<div class="contentsdw">
				<div class="smldescrptn">
					<div class="col-sm-7 col-xs-10 nopad">
						<div class="col-xs-6 boxpad none_boil_full">
							<h4 class="contryname"><?=ucfirst($flight_search_params['trip_type_label'])?> Trip</h4>
							<h3 class="placenameflt"><?php echo $flight_search_params['from'] ?> to <?php echo $flight_search_params['to'] ?></h3>
						</div>
						<?php 
						if($flight_search_params['trip_type']=='oneway') {
							$disable_return_date_label = ' style="opacity:0.4" ';
						} else {
							$disable_return_date_label = '';
						}
						?>
						<div class="col-xs-3 boxpad none_boil">
							<div class="boxlabl"><span class="faldate fa fa-calendar-o"></span>Departure</div>
							<div class="datein">
								<span class="calinn"><?=date('D, d M', strtotime($flight_search_params['depature']))?></span>
							</div>
						</div>
                        
						<div class="col-xs-3 boxpad none_boil" <?=$disable_return_date_label?>>
							<div class="boxlabl"><span class="faldate fa fa-calendar-o"></span>Return</div>
							<div class="datein">
								<span class="calinn">
								<?php
									if($flight_search_params['trip_type']=='circle' || $flight_search_params['trip_type']=='oneway') {
										if(isset($flight_search_params['return']) == true) {
											$temp_return_date = $flight_search_params['return'];
										} else {
											$temp_return_date = $flight_search_params['depature'];
										}
										echo date('D, d M', strtotime($temp_return_date));
									} else if ($flight_search_params['trip_type'] == 'multicity') {
										echo '---';
									}
								?>
								</span>
							</div>
						</div>
                        
					</div>
					<div class="col-sm-5 col-xs-2 nopad">
						<div class="col-xs-3 boxpad none_mody">
							<div class="boxlabl textcentr">Adult</div>
							<div class="countlbl"><?php echo $flight_search_params['adult_config']; ?></div>
						</div>
						<div class="col-xs-3 boxpad none_mody">
							<div class="boxlabl textcentr">Child</div>
							<div class="countlbl"><?php echo $flight_search_params['child_config']; ?></div>
						</div>
						<div class="col-xs-3 boxpad none_mody">
							<div class="boxlabl textcentr">Infant</div>
							<div class="countlbl"><?php echo $flight_search_params['infant_config']; ?></div>
						</div>
						<div class="col-xs-3 boxpad pull-right">
							<a class="modifysrch" data-toggle="collapse" data-target="#modify"><span class="mdyfydsktp">Modify Search</span>
							 <i class="fa fa-angle-down mobresdv" aria-hidden="true"></i>
							 </a>
						</div>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="splmodify">
		<div class="container-fluid">
			<div id="modify" class="collapse araeinner">
				<div class="insplarea">
					<?php echo $GLOBALS['CI']->template->isolated_view('share/flight_search') ?>
				</div>
			</div>
		</div>
	</div>
</div>

