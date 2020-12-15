<div class="fulloading result-pre-loader-wrapper bus_preloader">
	<div class="loadmask"></div>
	<div class="centerload cityload">
		<div class="loadcity"></div>
		<div class="clodnsun"></div>
		<div class="relativetop">
		<div class="tmxloader hide"><img class="loadvgif" src="<?php echo $GLOBALS['CI']->template->domain_images('tm_bus_loader.gif'); ?>" alt="Logo" />
		    </div>
			<div class="paraload"> Searching for the best Cars </div>
			<div class="clearfix"></div>
			<div class="sckintload ">
				<div class="ffty">
					<div class="borddo brdrit"> 
						<span class="lblbk"><?php echo ucfirst($car_from); ?></span> 
					</div>
				</div>
				<div class="ffty">
					<div class="borddo"> 
						<span class="lblbk"><?php echo ucfirst($car_to); ?></span> 
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="tabledates">
					<div class="tablecelfty">
						<div class="borddo brdrit">
							<div class="fuldate">
								<span class="bigdate"><?php echo  date("d",strtotime($depature));?></span>
								<div class="biginre"> <?php echo  date("M",strtotime($depature));?><br>
									<?php echo  date("Y",strtotime($depature));?> 
								</div>
							</div>
							<div class="fuldate">
								<span class="bigdate"><?php echo  date("d",strtotime($return));?></span>
								<div class="biginre"> <?php echo  date("M",strtotime($return));?><br>
									<?php echo  date("Y",strtotime($return));?> 
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