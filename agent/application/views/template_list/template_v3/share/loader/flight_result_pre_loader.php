<?php
	$trip_type_loader = '';
	if($trip_details['trip_type'] == 'multicity') {
		$trip_details['depature'] = is_array($trip_details['depature']) ? $trip_details['depature'][0]: $trip_details['depature'];
		$trip_details['from'] = $trip_details['from'][0];
		$trip_details['to'] = end($trip_details['to']);
	}
	$trip_details['from_timestamp'] = strtotime($trip_details['depature']);
	if ($trip_details['trip_type'] == 'circle') {
		$trip_type_loader = 'round-loading';//Needed for loader only
		$trip_details['to_timestamp'] = strtotime($trip_details['return']);
	}
	//Preferred Class and Carrier Details -- Balu A
	$preferred_class = $trip_details['v_class'];
	$preferred_carrier = @$airline_list[$trip_details['carrier'][0]];
	?>
<div class="fulloading result-pre-loader-wrapper">
	<div class="loadmask"></div>
	<div class="centerload cityload">
	<div class="load_links" style="position: absolute;top: 0;right: 0;z-index: 9999;font-size:14px;font-weight:300">
			<a href=""><i class="fa fa-refresh"></i></a>
			<a href="<?php echo base_url(); ?>"><i class="fa fa-close"></i></a>			
		</div>
		<div class="loadcity hide"></div>
		<div class="clodnsun"></div>
		<div class="reltivefligtgo hide">
			<div class="flitfly"></div>
		</div>
		<div class="relativetop">
		   
		    <div class="">
      		<img src="https://pacetravels.net/extras/custom/TMX1512291534825461/images/flight_svg04.svg" width="100%">
      	</div>
        <div class="row text-center id-loader-div">
        	<p><span class="text-red"><i class="fa fa-spinner fa-spin" aria-hidden="true"></i> &nbsp; Please Wait..</span><br> <span class="p">We are searching flights</span></p>
        	<div class="id-modal-des">
	    		<p><i class="fa fa-plane" aria-hidden="true"></i>&nbsp;&nbsp; <?=$trip_details['from']?> <span>&nbsp;<i class="fa fa-chevron-right" aria-hidden="true"></i></span> &nbsp;<?=$trip_details['to']?></p>
	    	</div>
      </div>
			<div class="paraload hide">
				We are seeking the best results for your search. Please wait.<br/>
				This will take only few seconds......
			</div>
			<div class="clearfix"></div>
			<div class="sckintload hide <?=$trip_type_loader?>">
				<div class="ffty">
					<div class="borddo brdrit">
						<span class="lblbk"><?=$trip_details['from']?></span>
					</div>
				</div>
				<div class="ffty">
					<div class="borddo">
						<span class="lblbk"><?=$trip_details['to']?></span>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="tabledates hide">
					<div class="tablecelfty">
						<div class="borddo brdrit">
							<div class="fuldate">
								<span class="bigdate"><?=date('d', $trip_details['from_timestamp'])?></span>
								<div class="biginre">
									<?=date('M', $trip_details['from_timestamp'])?><br /><?=date('Y', $trip_details['from_timestamp'])?>
								</div>
							</div>
						</div>
					</div>
					<div class="tablecelfty">
						<div class="borddo">
							<?php
								if ($trip_details['trip_type'] == 'circle') {
								?>
							<div class="fuldate">
								<span class="bigdate"><?=date('d', $trip_details['to_timestamp'])?></span>
								<div class="biginre">
									<?=date('M', $trip_details['to_timestamp'])?><br /><?=date('Y', $trip_details['to_timestamp'])?>
								</div>
							</div>
							<?php
								}
								?>
						</div>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="nigthcunt">
					<?=$trip_details['trip_type_label']?> Trip
					<?php if(empty($preferred_carrier) == false) { ?>
					<div class="prefered_section">Airline: <?=$preferred_carrier?></div>
					<?php } ?>
					<?php if($preferred_class != 'All' && empty($preferred_class) == false) { ?>
					<div class="prefered_section">Class: <?=$preferred_class?></div>
					<?php } ?> 
				</div>
			</div>
		</div>
	</div>
</div>
<style type="text/css">
	.lblbk {
    color: #666;
    display: block;
    font-size: 14px;
    overflow: hidden;
    padding: 10px;
}
.id-loader-div .p{
		font-size: 15px;
		color: #aaa;
	}
	.id-loader-div h3{
		margin: 0;
	}
	
	.modal-dialog{
		margin-top: 12%;
		width: 400px;
	}
	.id-loader-div .p{
		color: #8c8b8b;
	}
	.id-loader-div{
		margin-top: 10px;
	}
	/*.id-modal-des p{
		font-size: 18px;
		font-weight: bold;
		text-transform: uppercase;
		color: #bb3f3f;
	}*/
	.id-modal-des p {
	    font-weight: bold;
	    text-transform: uppercase;
	    color: #fff;
	    /*background-color: #7299f0;*/
	    background: linear-gradient(0deg,#002042,#0a8ec1);
	    margin-bottom: 0!important;
	    padding: 10px;
	    font-size: 18px;
	}
	.id-modal-des{
		text-align: center!important;
		margin-top: 10px;
	}
	/*.id-modal-des i{
		font-size: 12px;
		color: #000;
		padding: 10px;
	}*/
	.id-loader-div p{
		margin: 0;
	}
</style>