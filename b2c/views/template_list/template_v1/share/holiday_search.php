<!-- holiday packages -->
<form action="<?php echo base_url().'index.php/tours/search'?>" autocomplete="off" id="holiday_search">
	<h2 class="h3">Tours &amp; Packages</h2>
	<div class="form-group">
		<div class="row">
			<div class="col-md-6 padfive">
            	<label></label>
				<select class="normalsel padselct ar" id="country" name="country">
					<option value="">All</option>
					<?php if(!empty($holiday_data['countries'])){ ?>
					<?php foreach ($holiday_data['countries'] as $country) { ?>
					<option value="<?php echo $country->package_country; ?>"
						<?php if(isset($scountry)){ if($scountry == $country->package_country) echo "selected"; }?>><?php echo $country->country_name; ?>
					</option>
					<?php } } ?>
				</select>
			</div>
			<div class="col-md-6 padfive">
            	<label></label>
				<select class="normalsel padselct ar" id="package_type" name="package_type">
					<option value="">All Package Types</option>
					<?php if(!empty($holiday_data['package_types'])){ ?>
					<?php foreach ($holiday_data['package_types'] as $package_type) { ?>
					<option value="<?php echo $package_type->package_types_id; ?>" <?php if(isset($spackage_type)){ if($spackage_type == $package_type->package_types_id) echo "selected"; } ?>><?php echo $package_type->package_types_name; ?></option>
					<?php } ?>
					<?php } ?>
				</select>
			</div>
			<div class="col-md-6 padfive">
            	<label></label>
				<select class="normalsel padselct ar" id="duration" name="duration" >
					<option value="">All Durations</option>
					<option value="1-3" <?php if(isset($sduration)){ if($sduration == '1-3') echo "selected"; } ?>>1-3</option>
					<option value="4-7" <?php if(isset($sduration)){ if($sduration == '4-7') echo "selected"; } ?>>4-7</option>
					<option value="8-12" <?php if(isset($sduration)){ if($sduration == '8-12') echo "selected"; } ?>>8-12</option>
					<option value="12" <?php if(isset($sduration)){ if($sduration == '12') echo "selected"; } ?>>12</option>
				</select>
			</div>
			<div class="col-md-6 padfive">
            	<label></label>
				<select class="normalsel padselct ar" id="budget" name="budget">
					<option value="">All</option>
					<option value="100-500" <?php if(isset($sbudget)){ if($sbudget == '100-500') echo "selected"; } ?>>100-500</option>
					<option value="500-1000" <?php if(isset($sbudget)){ if($sbudget == '500-1000') echo "selected"; } ?>>500-1000</option>
					<option value="1000-5000" <?php if(isset($sbudget)){ if($sbudget == '1000-5000') echo "selected"; } ?>>1000-5000</option>
					<option value="5000" <?php if(isset($sbudget)){ if($sbudget == '5000') echo "selected"; } ?>>5000</option>
				</select>
			</div>
		</div>
	</div>
	<div class="clearfix">
		<div class="pull-right srchBtnmob">
			<input type="submit" class="HolidaySrchBtn btn btn-lg btn-i b-r-0" value="Search Holiday Packages" />
		</div>
	</div>
</form>