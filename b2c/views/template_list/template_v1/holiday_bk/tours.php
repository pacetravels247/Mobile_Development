<link
	href="<?php echo $GLOBALS['CI']->template->template_css_dir('custom_tour.css') ?>"
	rel="stylesheet">
<link
	href="<?php echo $GLOBALS['CI']->template->template_css_dir('custom_sky.css') ?>"
	rel="stylesheet">

<style type="text/css">
	.padselct {padding-left: 15px;
    border: 1px solid #ddd;
    margin-bottom: 10px; }
</style>

<div class="full witcontent  marintopcnt">
	<div class="container">
		<div class="container offset-0">
			<div class="cnclpoly">
				<div class="col-md-3 col-xs-12 nopad">
				<h1 id="contentTitle" class="h3">Tours And Packages</h1>
				<div class="clear"></div>
				<div class="tourfilter">
					<form action="<?php echo base_url().'index.php/tours/search'?>"
						autocomplete="off" id="holiday_search">
						<div class="tabspl forhotelonly">
							<div class="tabrow">
								<div class="col-md-12 col-sm-6 col-xs-6 mobile_width padfive">
									<div class="lbl_txt">Country</div>
									<select class="normalsel padselct arimo" id="country"
										name="country">
										<option value="">All</option>
					<?php 
					if(isset($holiday_data['countries'])){
						$country_data = $holiday_data['countries'];
					}else{
						$country_data = $countries;
					}
					if(!empty($country_data)){ ?>
					<?php foreach ($country_data as $country) { ?>
					<option value="<?php echo $country->package_country; ?>"
											<?php if(isset($scountry)){ if($scountry == $country->package_country) echo "selected"; }?>><?php echo $country_name = $this->Package_Model->getCountryName($country->package_country)->name; ?></option>
					<?php } } ?>
				</select>
								</div>
								<div class="col-md-12 col-sm-6 col-xs-6 mobile_width padfive">
									<div class="lbl_txt">Package Type</div>
									<select class="normalsel padselct arimo" id="package_type"
										name="package_type">
										<option value="">All Package Types</option>
					<?php 
					if(isset($holiday_data['package_types'])){
						$holiday = $holiday_data['package_types'];
					}else{
						$holiday = $package_types;
					}
					if(!empty($holiday)){ ?>
					<?php foreach ($holiday as $package_type) { ?>
					<option value="<?php echo $package_type->package_types_id; ?>"
											<?php if(isset($spackage_type)){ if($spackage_type == $package_type->package_types_id) echo "selected"; } ?>><?php echo $package_type->package_types_name; ?></option>
					<?php } ?>
					<?php } ?>
				</select>
								</div>
								<div class="col-md-12 col-sm-6 col-xs-6 mobile_width padfive">
									<div class="lbl_txt">Duration</div>
									<select class="normalsel padselct arimo" id="duration"
										name="duration">
										<option value="">All Durations</option>
										<option value="1-3"
											<?php if(isset($sduration)){ if($sduration == '1-3') echo "selected"; } ?>>1-3</option>
										<option value="4-7"
											<?php if(isset($sduration)){ if($sduration == '4-7') echo "selected"; } ?>>4-7</option>
										<option value="8-12"
											<?php if(isset($sduration)){ if($sduration == '8-12') echo "selected"; } ?>>8-12</option>
										<option value="12"
											<?php if(isset($sduration)){ if($sduration == '12') echo "selected"; } ?>>12</option>
									</select>
								</div>
								<div class="col-md-12 col-sm-6 col-xs-6 mobile_width padfive">
									<div class="lbl_txt">Budget</div>
									<select class="normalsel padselct arimo" id="budget"
										name="budget">
										<option value="">All</option>
										<option value="100-500"
											<?php if(isset($sbudget)){ if($sbudget == '100-500') echo "selected"; } ?>>100-500</option>
										<option value="500-1000"
											<?php if(isset($sbudget)){ if($sbudget == '500-1000') echo "selected"; } ?>>500-1000</option>
										<option value="1000-5000"
											<?php if(isset($sbudget)){ if($sbudget == '1000-5000') echo "selected"; } ?>>1000-5000</option>
										<option value="5000"
											<?php if(isset($sbudget)){ if($sbudget == '5000') echo "selected"; } ?>>5000</option>
									</select>
								</div>
								<div class="col-md-12 col-xs-12 padfive">
									<div class="">&nbsp;</div>
									<div class="searchsbmtfot">
										<input type="submit" class="searchsbmt" value="search" />
									</div>
								</div>
							</div>
						</div>

					</form>
				</div>
				</div>

		<div class="col-md-9 col-xs-12 nopad">
				<div id="packgtr" class="packgtr">
					<ul id="container" class="row"> 
          <?php if(!empty($packages)){ ?>
          <?php foreach($packages as $pack){?>
          <?php $country_name = $this->Package_Model->getCountryName($pack->package_country); ?>
            <li class="col-md-12 col-xs-12 nopadMob nopad">
							<div class="inlitp">
								<div class="tpimage col-sm-3 col-xs-3 mobile_width nopad">
									<img
										src="<?php echo $GLOBALS['CI']->template->domain_upload_pckg_images(basename($pack->image)); ?>"
										alt="<?php echo $pack->package_name; ?>" />
								</div>
								<div class="tpcontent col-sm-7 col-xs-7 mobile_width">
									<h3 class="tptitle txtwrapRow"><?php echo $pack->package_name; ?> </h3>
									<div class="htladrsxl"><?php echo $country_name->name; ?> | <?php echo $pack->package_city; ?>  </div>
									<div class="clear"></div>
									<p> <?php echo substr($pack->package_description, 0,300); ?></p>
								</div>

								<div class="pkprice col-sm-2 col-xs-2 mobile_width nopad">
									<div class="pricebolk">	<strong> <?php echo $currency_obj->get_currency_symbol($currency_obj->to_currency); ?> </strong> <?php echo get_converted_currency_value ( $currency_obj->force_currency_conversion ( $pack->price ) );?></div>
									<div class="durtio"><?php echo ($pack->duration-1); ?> Nights / <?php echo $pack->duration; ?> Days</div>
				
								<a class="relativefmsub trssxl"
									href="<?php echo base_url(); ?>index.php/tours/details/<?php echo $pack->package_id; ?>">
									<span class="sfitlblx">View Detail</span> 
								</a>
								</div>
							</div>
						</li>
            <?php }?>
            <?php }else{?>
             <li class="tpli cenful">
							<div class="inlitp">
								<div class="tpimagexl">
									<img
										src="<?php echo $GLOBALS['CI']->template->template_images('no_result.png'); ?>"
										alt="No Packages Found" />
								</div>
								<div class="tpcontent">
									<h3 class="tptitle center">No Packages Found</h3>
								</div>
								<a class="relativefmsub trssxl"
									href="<?php echo base_url(); ?>index.php/tours/search"> <span
									class="sfitlblx">Reset Filters</span> <span class="srcharowx"></span>
								</a>

							</div>
						</li>
            <?php }?>
          </ul>
				</div>
			</div>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo ASSETS;?>/js/jquery.masonry.min.js"
	type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function(){
	var $container = $('#packgtr');
	$container.imagesLoaded( function() {
		$container.masonry({itemSelector:        '.tpli'});
	});
	updateFilters();
});

$(window).resize(function(){
	var $container = $('#packgtr');
	$container.imagesLoaded( function() {
		$container.masonry({itemSelector:        '.tpli'});
	});
});function updateFilters()
{
	var country_list = {};
	var temp_country = '';
	var temp_city = '';
	var temp_maxDuration = '';
	var temp_maxPrice = '';
	var minDuration = 1;
	var maxDuration = 30;
	var minPrice = 1;
	var maxPrice = 99999;

	$('.active_view').each(function(key, value) { 
		temp_country = $('.defaultCountryValue', this).text();
		temp_city = $('.defaultCityValue', this).text();
		temp_maxDuration = parseInt($('.defaultDurationValue', this).text());
		temp_maxPrice = parseFloat($('.defaultPriceValue', this).text())
		if(country_list.hasOwnProperty(temp_country) == false){country_list[temp_country] = temp_country}
		if(city_list.hasOwnProperty(temp_city) == false){city_list[temp_city] = temp_city}			
		if(maxDuration < temp_maxDuration){maxDuration = temp_maxDuration}
		if(maxPrice < temp_maxPrice){maxPrice = temp_maxPrice}
	});
	cityList = getSortedObject(city_list);
	countryList = getSortedObject(country_list);
	loadCityFilter(cityList);
	loadCountryFilter(countryList);
	loadDuration(minDuration,maxDuration);
	loadPrice(minPrice,maxPrice);
}
//sorting cities
function getSortedObject(obj)
{
	var objValArray = getArray(obj);
	var sortObj = {};
	objValArray.sort();
	$.each(objValArray, function(obj_key, obj_val) {
		$.each(obj, function(i_k, i_v) {
		    if (i_v == obj_val) {
		    	sortObj[i_k] = i_v;
			}
		});
	});
	return sortObj;
}
function getArray(objectWrap)
{
	var objectWrapValueArr = [];
	$.each(objectWrap, function(key, value) {
		objectWrapValueArr.push(value);
	});
	return objectWrapValueArr;
}
</script>
</body>
</html>
