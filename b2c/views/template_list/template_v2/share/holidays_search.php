				<!-- holiday packages -->	  

<form action="<?php echo base_url();?>/tours/search" method="post" autocomplete="off" id="holiday_search">
	<div class="intabs">
		<h3 class="tabinhed">Tours And Packages</h3>
		<div class="clear"></div>
		<div class="multyflightwrap">
			<div class="full normal">
				<div class="row">
					<div class="col-lg-6 col-md-6 col-sm-6  marginbotom10 disover"> <span class="formlabel">Country Name</span>
						<div class="relativemask"> 
							<select class="mySelectBoxClass flyinputsnor" id="country" name="country">
								<option value="">All</option>
								<?php if(!empty($countries)){ ?>
								<?php foreach ($countries as $country) { ?>
								<option value="<?php echo $country->package_country; ?>" <?php if(isset($scountry)){ if($scountry == $country->package_country) echo "selected"; }?>><?php echo $country_name = $this->Package_Model->getCountryName($country->package_country)->name; ?></option>
								<?php } } ?>
							</select>
						</div>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-6  marginbotom10 disover"> <span class="formlabel">Packages List</span>
						<div class="relativemask"> 
							<select class="mySelectBoxClass flyinputsnor" id="package_type" name="package_type">
								<option value="">All Package Types</option>
								<?php if(!empty($package_types)){ ?>
								<?php foreach ($package_types as $package_type) { ?>
								<option value="<?php echo $package_type->package_types_id; ?>" <?php if(isset($spackage_type)){ if($spackage_type == $package_type->package_types_id) echo "selected"; } ?>><?php echo $package_type->package_types_name; ?></option>
								<?php } ?>
								<?php } ?>
							</select>
						</div>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="row marginbotom10 nopad">
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 mefullwd fiveh"> <span class="formlabel">Duration</span>
						<div class="relativemask"> 
							<select class="mySelectBoxClass flyinputsnor" id="duration" name="duration" >
								<option value="">All Durations</option>
								<option value="1-3" <?php if(isset($sduration)){ if($sduration == '1-3') echo "selected"; } ?>>1-3</option>
								<option value="4-7" <?php if(isset($sduration)){ if($sduration == '4-7') echo "selected"; } ?>>4-7</option>
								<option value="8-12" <?php if(isset($sduration)){ if($sduration == '8-12') echo "selected"; } ?>>8-12</option>
								<option value="12" <?php if(isset($sduration)){ if($sduration == '12') echo "selected"; } ?>>12</option>
							</select>
						</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 mefullwd fiveh"> <span class="formlabel">Budget</span>
						<div class="relativemask"> 
							<select class="mySelectBoxClass flyinputsnor" id="budget" name="budget">
								<option value="">All</option>
								<option value="100-500" <?php if(isset($sbudget)){ if($sbudget == '100-500') echo "selected"; } ?>>100-500</option>
								<option value="500-1000" <?php if(isset($sbudget)){ if($sbudget == '500-1000') echo "selected"; } ?>>500-1000</option>
								<option value="1000-5000" <?php if(isset($sbudget)){ if($sbudget == '1000-5000') echo "selected"; } ?>>1000-5000</option>
								<option value="5000" <?php if(isset($sbudget)){ if($sbudget == '5000') echo "selected"; } ?>>5000</option>
							</select>
						</div>
					</div>
				</div>
			</div>

			<!--    multycity     --> 

			<!--    multycity  end   --> 

		</div>
		<div class="clearfix"></div>
		<div class="formsubmit">
			<div class="relativefmsub">
				<input type="submit" class="btn btn-primary" style="position: relative;" value="Search" />
			</div>
		</div>
	</div>
</form>


