<div class="fulloading result-pre-loader-wrapper bus_preloader">
	<div class="loadmask"></div>
	<div class="centerload cityload">
		<div class="loadcity hide"></div>
		<div class="clodnsun hide"></div>
		<div class="relativetop">
			<div class="">
      		<img src="http://pacetravels.net/extras/custom/TMX1512291534825461/images/bus_svg01.svg" width="100%">
      	</div>
        <div class="row text-center id-loader-div">
        	<p><span class="text-red"><i class="fa fa-spinner fa-spin" aria-hidden="true"></i> &nbsp; Please Wait..</span><br> <span class="p">We are searching buses</span></p>
        	<div class="id-modal-des">
	    		<p><i class="fa fa-bus" aria-hidden="true"></i>&nbsp;&nbsp; <?php echo ucfirst($result['bus_station_from']); ?> <span>&nbsp;<i class="fa fa-chevron-right" aria-hidden="true"></i></span> &nbsp;<?php echo ucfirst($result['bus_station_to']); ?></p>
	    	</div>
      </div>
		<div class="tmxloader hide"><img class="loadvgif" src="<?php echo $GLOBALS['CI']->template->domain_images('tm_bus_loader.gif'); ?>" alt="Logo" />
		    </div>
			<div class="paraload hide"> Searching for the best buses </div>
			<div class="clearfix"></div>
			<div class="sckintload hide ">
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
		<div class="busrunning hide">
			<div class="runbus"></div>
			<div class="runbus2"></div>
			<div class="roadd"></div>
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