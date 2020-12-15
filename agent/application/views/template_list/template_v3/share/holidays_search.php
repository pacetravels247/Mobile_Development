<!-- holiday packages -->
<!-- <form action="<?php echo base_url().'index.php/tours/search'?>"
	autocomplete="off" id="holiday_search">
	<div class="tabspl forhotelonly">
		<div class="tabrow">
			<div class="col-md-3 col-sm-3 col-xs-6 padfive full_smal_tab">
				<div class="lablform">Country</div>
				<div class="selectedwrap sidebord">
					<select class="normalsel holyday_selct" id="country" name="country">
						<option value="">All</option>
						<?php if(!empty($holiday_data['countries'])){?>
						<?php foreach ($holiday_data['countries'] as $country) { ?>
						<option value="<?php echo $country->country_id; ?>"
							<?php if(isset($scountry)){ if($scountry == $country->country_id) echo "selected"; }?>><?php echo $country->country_name; ?>
						</option>
						<?php } } ?>
					</select>
				</div>
			</div>
			<div class="col-md-3 col-sm-3 col-xs-6 padfive full_smal_tab">
				<div class="lablform">Package Type</div>
				<div class="selectedwrap sidebord">
					<select class="normalsel holyday_selct" id="package_type"
						name="package_type">
						<option value="">All Package Types</option>
						<?php if(!empty($holiday_data['package_types'])){ ?>
						<?php foreach ($holiday_data['package_types'] as $package_type) { ?>
						<option value="<?php echo $package_type->package_types_id; ?>"
							<?php if(isset($spackage_type)){ if($spackage_type == $package_type->package_types_id) echo "selected"; } ?>><?php echo $package_type->package_types_name; ?></option>
						<?php } ?>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="col-md-2 col-sm-2 col-xs-4 padfive full_smal_tab">
				<div class="lablform">Duration</div>
				<div class="selectedwrap sidebord">
					<select class="normalsel holyday_selct" id="duration"
						name="duration">
						<option value="">All Durations</option>
						<option value="1-3"
							<?php if(isset($sduration)){ if($sduration == '1-3') echo "selected"; } ?>>1-3</option>
						<option value="4-7"
							<?php if(isset($sduration)){ if($sduration == '4-7') echo "selected"; } ?>>4-7</option>
						<option value="8-12"
							<?php if(isset($sduration)){ if($sduration == '8-12') echo "selected"; } ?>>8-12</option>
						<option value="12"
							<?php if(isset($sduration)){ if($sduration == '12') echo "selected"; } ?>>12+</option>
					</select>
				</div>
			</div>
			<div class="col-md-2 col-sm-2 col-xs-4 padfive full_smal_tab">
				<div class="lablform">Budget</div>
				<div class="selectedwrap sidebord">
					<select class="normalsel holyday_selct" id="budget" name="budget">
						<option value="">All</option>
						<option value="100-500"
							<?php if(isset($sbudget)){ if($sbudget == '100-500') echo "selected"; } ?>><?php echo $currency_obj->get_currency_symbol($currency_obj->to_currency); ?> <?php echo get_converted_currency_value ( $currency_obj->force_currency_conversion ( '100' ) );?>-<?php echo get_converted_currency_value ( $currency_obj->force_currency_conversion ( '500' ) );?></option>
						<option value="500-1000"
							<?php if(isset($sbudget)){ if($sbudget == '500-1000') echo "selected"; } ?>><?php echo $currency_obj->get_currency_symbol($currency_obj->to_currency); ?> <?php echo get_converted_currency_value ( $currency_obj->force_currency_conversion ( '500' ) );?>-<?php echo get_converted_currency_value ( $currency_obj->force_currency_conversion ( '1000' ) );?></option>
						<option value="1000-5000"
							<?php if(isset($sbudget)){ if($sbudget == '1000-5000') echo "selected"; } ?>><?php echo $currency_obj->get_currency_symbol($currency_obj->to_currency); ?> <?php echo get_converted_currency_value ( $currency_obj->force_currency_conversion ( '1000' ) );?>-<?php echo get_converted_currency_value ( $currency_obj->force_currency_conversion ( '5000' ) );?></option>
						<option value="5000"
							<?php if(isset($sbudget)){ if($sbudget == '5000') echo "selected"; } ?>><?php echo $currency_obj->get_currency_symbol($currency_obj->to_currency); ?> <?php echo get_converted_currency_value ( $currency_obj->force_currency_conversion ( '5000' ) );?> <?php echo '> '?></option>
					</select>
				</div>
			</div>
			<div class="col-md-2 col-sm-2 col-xs-4 padfive full_smal_tab">
				<div class="lablform">&nbsp;</div>
				<div class="searchsbmtfot">
					<input type="submit" class="searchsbmt" value="search" />
				</div>
			</div>
		</div>
	</div>
</form> -->

<form action="" method="post" id="holiday_search">
	<div class="col-md-8 col-md-offset-2 holiday_srch_input">
		<div class="form-group">
			<input type="text" name="" class="form-control" placeholder="Search City,Country,Place">
		</div>
	</div>
		<div class="col-md-12">
		<div class="search_but">
					<input type="submit" class="srch_butt" value="search" />
		</div>
	</div>
</form>
<style type="text/css">
	.holiday_srch_input
	{
		position: relative;
	}
.search_but {
    position: absolute;
    top: -47px;
    right: 18%;
}
.srch_butt {
    background: #a100ff;
    color: #fff;
    padding: 4px 30px;
    font-size: 14px;
    border: 1px solid #a100ff;
    font-weight: 600;
    border-radius: 20px;
}
.holiday_srch_input input.form-control {
    border-radius: 20px !important;
    border: 1px solid #9d00f9;
}
</style>