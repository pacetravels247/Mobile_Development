<div class="fulloading result-pre-loader-wrapper forhoteload">
	<div class="loadmask"></div>
	<div class="centerload cityload">
	<div class="load_links" style="position: absolute;top: 0;right: 0;z-index: 9999;font-size:14px;font-weight:300">
			<a href=""><i class="fa fa-refresh"></i></a>
			<a href="<?php echo base_url(); ?>"><i class="fa fa-close"></i></a>			
		</div>
		<div class="loadcity"></div>
		<div class="clodnsun"></div>
		<div class="reltivefligtgo">
			<div class="flitfly"></div>
		</div>
		<div class="relativetop">
			<div class="paraload">
				Searching for the best hotels
			</div>
			<div class="clearfix"></div>
			<div class="placenametohtl"><?php echo ucfirst($result['location']); ?></div>
			<div class="clearfix"></div>
			<div class="sckintload">
				<div class="ffty">
					<div class="borddo brdrit">
						<span class="lblbk">Check In</span>
					</div>
				</div>
				<div class="ffty">
					<div class="borddo">
						<span class="lblbk">Check Out</span>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="tabledates">
					<div class="tablecelfty">
						<div class="borddo brdrit">
							<div class="fuldate">
								<span class="bigdate"><?php echo date("d",strtotime($result['from_date']));?></span>
								<div class="biginre">
									<?php echo date("M",strtotime($result['from_date']));?><br />
									<?php echo date("Y",strtotime($result['from_date']));?>
								</div>
							</div>
						</div>
					</div>
					<div class="tablecelfty">
						<div class="borddo">
							<div class="fuldate">
								<span class="bigdate"><?php echo date("d",strtotime($result['to_date']));?></span>
								<div class="biginre">
									<?php echo date("M",strtotime($result['to_date']));?><br />
									<?php echo date("Y",strtotime($result['to_date']));?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="nigthcunt"><?php echo $hotel_search_params['no_of_nights'];?> <?=(intval($hotel_search_params['no_of_nights']) > 1 ? 'Nights' : 'Night')?></div>
			</div>
		</div>
		<div class="clearfix"></div>
		<div class="busrunning">
			<div class="runbus"></div>
			<div class="runbus2"></div>
			<div class="roadd"></div>
		</div>
	</div>
</div>