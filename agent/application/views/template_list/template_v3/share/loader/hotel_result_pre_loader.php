<div class="fulloading result-pre-loader-wrapper forhoteload">
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
      		<img src="http://pacetravels.net/extras/custom/TMX1512291534825461/images/hotel_svg04.svg" width="100%">
      	</div>
       <div class="row text-center id-loader-div">
        	<p><span class="text-red"><i class="fa fa-spinner fa-spin" aria-hidden="true"></i> &nbsp; Please Wait..</span><br> <span class="p">We are searching best Hotels in</span></p>
        	<div class="id-modal-des">
	    		<p><i class="fa fa-bed" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo ucfirst($result['location']); ?></p>
	    	</div>
      </div>
			<div class="paraload hide">
				Searching for the best hotels
			</div>
			<div class="clearfix"></div>
			<div class="placenametohtl hide"><?php echo ucfirst($result['location']); ?></div>
			<div class="clearfix"></div>
			<div class="sckintload hide">
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