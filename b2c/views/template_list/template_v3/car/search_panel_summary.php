<style>
	.topssec::after{display:none;}
	
</style>

<?php //debug($car_search_params);exit;?>
<div class="modfictions for_hotel_modi">
	<div class="modinew">
		<div class="container">
		<div class="contentsdw">
		<div class="col-lg-6 col-sm-6 col-xs-6 hidden-xs nopad">
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 nopad">
				<div class="pad_ten">
					<div class="boxlabl">
						Pick-Up
					</div>
					<div class="from_to_place">
						<div class="namefromto set_fromloc"><span class="set_dots hide"></span><?php echo $car_search_params['car_from'];?></div>
					</div>
				</div>
			</div>

			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 nopad">
				<div class="pad_ten">
					<div class="boxlabl">
						Drop-Off
					</div>
					<div class="from_to_place">
						<div class="namefromto set_toloc"><span class="set_dots hide"></span><?php echo $car_search_params['car_to']; ?></div>
					</div>
				</div>
			</div>

			</div>

			<div class="col-lg-4 col-sm-4 col-xs-4 hidden-xs nopad">
				<div class="col-xs-6 nopad">
					<div class="pad_ten">
						<div class="from_to_place">
							<div class="boxlabl">Pick-Up Date</div>
							<div class="datein"> 
								<span class="calinn">
									<?=app_friendly_absolute_date($car_search_params['depature']); ?>
									<?=date('H:i',strtotime($car_search_params['depature_time'])); ?> 
								</span>
							</div>
						</div>
					</div>
				</div>
			
				<?php if(isset($car_search_params['return'])) { ?>
				 <div class="col-xs-6 nopad ">
					<div class="pad_ten">
						<div class="cal_i"></div>
						<div class="from_to_place">
							<div class="boxlabl">Return Date</div>
							<div class="datein"> 
								<span class="calinn">
									<?=app_friendly_absolute_date($car_search_params['return']); ?> 
									<?=date('H:i',strtotime($car_search_params['return_time'])); ?> 
								</span>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
				
			</div>
			
			<div class="col-md-2 col-sm-2 col-xs-4 nopad">
				<div class="pad_ten">
					<button class="modifysrch" data-toggle="collapse" data-target="#modify">Modify</button>
				</div>
			</div>
		</div>
	</div>
</div>

	<div class="modify_search_wrap splmodify">
		<div class="container" style="position: relative;">
		 <div class="">
			<div id="modify" class="collapse araeinner">
				<div class="insplarea">
					<?php echo $GLOBALS['CI']->template->isolated_view('share/car_search') ?>
				</div>
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