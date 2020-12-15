<div class="fulloading result-pre-loader-wrapper bus_preloader">
	<div class="loadmask"></div>
	<div class="centerload cityload">
		<div class="loadcity"></div>
		<div class="clodnsun"></div>
		<div class="relativetop">
		<div class="tmxloader hide"><img class="loadvgif" src="<?php echo $GLOBALS['CI']->template->domain_images('tm_bus_loader.gif'); ?>" alt="Logo" />
		    </div>
			<div class="paraload"> Searching for the best buses </div>
			<div class="clearfix"></div>
			<div class="sckintload ">
				<div class="ffty">
					<div class="borddo brdrit"> 
						<span class="lblbk"><?php echo ucfirst($result['bus_station_from']); ?></span> 
					</div>
				</div>
				<div class="ffty">
					<div class="borddo"> 
						<span class="lblbk"><?php echo ucfirst($result['bus_station_to']); ?></span> 
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="tabledates">
					<div class="tablecelfty">
						<div class="borddo brdrit">
							<div class="fuldate">
								<span class="bigdate"><?php echo  date("d",strtotime($result['bus_date_1']));?></span>
								<div class="biginre"> <?php echo  date("M",strtotime($result['bus_date_1']));?><br>
									<?php echo  date("Y",strtotime($result['bus_date_1']));?> 
								</div>
							</div>
						</div>
					</div>
				</div>
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