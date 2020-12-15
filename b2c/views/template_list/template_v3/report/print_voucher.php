<div class="fromtopmargin">
   <div class="container">
      <div class="col-md-12 col-xs-12 pnr-section">
         <div class="lblbluebold16px">
            <h4>Check PNR Status</h4>
         </div>
         <div class="lblfont12px clearfix">
           <form autocomplete="off" name="pnr_search" id="pnr_search" action="<?php echo base_url();?>index.php/report/print_voucher" method="POST" class="activeForm oneway_frm">
		   <div class="col-xs-12 padfive full_smal_tab mobile_width">
		      <div class="lablform">PNR Number</div>
		      <div class="col-xs-12 col-sm-4 nopad">           
		         <input type="text" id="pnr_number" class="normalinput form-control b-r-0" placeholder="Enter PNR Number" name="pnr_number" required="">          
		      </div>
		   </div>

	   <div class="col-xs-12 padfive full_smal_tab pnr_module">
	      <div class="form-group">
	         <div class="lablform" for="bus-date-1">Module</div>
	         <div class="input-group">
	         <span class="padfive">
	         <input type="radio" id="flight-pnr-module" class="lablform" name="module" value="<?php echo PROVAB_FLIGHT_BOOKING_SOURCE; ?>" checked=""> <label for="flight-pnr-module" class="lablform"> Flight</label>
	         </span>
	         <span class="padfive">
	         <input type="radio" id="bus-pnr-module" class="lablform" name="module" value="<?php echo PROVAB_BUS_BOOKING_SOURCE; ?>"> 
	         <label for="bus-pnr-module" class="lablform"> Bus</label>
	         </span>
	         <span class="padfive">
	         <input type="radio" id="hotel-pnr-module" class="lablform" name="module" value="<?php echo PROVAB_HOTEL_BOOKING_SOURCE; ?>"> 
	         <label for="hotel-pnr-module" class="lablform"> Hotel</label>
	         </span><span class="padfive">
	         <input type="radio" id="transfer-pnr-module" class="lablform" name="module" value="<?php echo PROVAB_TRANSFERV1_BOOKING_SOURCE; ?>">
	         <label for="transfer-pnr-module" class="lablform">Transfers</label>
	         </span>
	         <span class="padfive"><input type="radio" id="activities-pnr-module" class="lablform" name="module" value="<?php echo PROVAB_SIGHTSEEN_BOOKING_SOURCE; ?>">
	         <label for="activities-pnr-module" class="lablform">Activities</label>
	         </span> 
	         </div>
	      </div>
	   </div>
	   <div class="pull-left">
	     <div class="searchsbmtfot"><i class="fas fa-search"></i><input type="submit" name="search_flight" id="flight-form-submit" class="searchsbmt flight_search_btn" value="search"></div>
	   </div>
	</form>
         </div>
      </div>
   </div>
</div>