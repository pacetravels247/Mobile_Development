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
	<div class="container">
<div class="col-md-9 id-filter text-center">

    <div class="row">
        <div class="col-sm-5 padfive">
            <div class="row">
                <div class="col-sm-2 padfive">
					<div class="custom-control">
						<input type="radio" class="custom-control-input tour_type_fil" id="customRadio1" value="0" name="customRadio1" checked>
						<label class="custom-control-label" for="customRadio1">All</label>
					</div>
                </div>
                <div class="col-sm-5 padfive">
					<div class="custom-control">
						<input type="radio" class="custom-control-input tour_type_fil" id="customRadio2"   value="2" name="customRadio1">
						<label class="custom-control-label" for="customRadio2">Domestic</label>
					</div>
                </div>
                <div class="col-sm-5 padfive">
					<div class="custom-control">
						<input type="radio" class="custom-control-input tour_type_fil" id="customRadio3"  value="1" name="customRadio1">
						<label class="custom-control-label" for="customRadio3">International</label>
					</div>
                </div>
            </div> 
        </div>
		<div class="col-sm-2 padfive">
		</div>
		<div class="col-sm-5 padfive">
		  <div class="row">
			<div class="col-sm-2 padfive">
			  <div class="custom-control">
				<input type="radio" class="custom-control-input trip_cat_fil" id="groupRadio1" value="0" name="groupRadio1" checked>
				<label class="custom-control-label" for="groupRadio1">All</label>
			  </div>
			</div>
			<div class="col-sm-5 padfive">
			  <div class="custom-control">
				<input type="radio" class="custom-control-input trip_cat_fil" id="groupRadio2" value="fit" name="groupRadio1">
				<label class="custom-control-label" for="groupRadio2">Customize Package</label>
			  </div>
			</div>
			<div class="col-sm-5 padfive">
			  <div class="custom-control">
				<input type="radio" class="custom-control-input trip_cat_fil" id="groupRadio3" value="group" name="groupRadio1">
				<label class="custom-control-label" for="groupRadio3">Group Package</label>
			  </div>
			</div>
		  </div> 
		</div>
    </div>
</div>
</div>
<div class="full witcontent  marintopcnt">
	<div class="container">
		<div class="container offset-0">
			<div class="cnclpoly">
				<div class="col-md-3 col-xs-12 nopad">
					<!-- <h1 id="contentTitle" class="h3">Tours And Packages</h1> -->
					<div class="clear"></div>
					<div class="tourfilter">
						<h1 id="contentTitle" class="h3">Tours and Packages</h1>
						<form action="<?php echo base_url().'index.php/tours/search'?>" autocomplete="off" id="holiday_search">
							<div class="tabspl forhotelonly">
								<div class="tabrow">
									<div class="col-md-12 col-sm-6 col-xs-6 mobile_width padfive">
										<div class="lbl_txt">Country</div>
										<select class="normalsel padselct arimo fil_tour_country" id="country" name="country">
											<option value="0">All</option>
											<?php 
												
												foreach($tours_country_name as $t_key =>$t_val){
													if($scountry==$t_key){
														$sel="selected";
													}else{
														$sel="";
													}
											?>
												<option value="<?=$t_key?>" <?=$sel?>><?=$t_val?></option>
											<?php
												}
											?>
										</select>
									</div>
									<div class="col-md-12 col-sm-6 col-xs-6 mobile_width padfive ">
										<div class="lbl_txt">City</div>
										<select class="normalsel padselct arimo fil_tour_city" name="city">
											<option value="0">ALL</option>
											<?php 
												if(isset($scity_list)){
													
													foreach($scity_list as $c_key =>$c_val){
														if($scity==$c_val['id']){
															$csel="selected";
														}else{
															$csel="";
														}
											
														echo '<option value="'.$c_val['id'].'" '.$csel.'>'.$c_val['CityName'].'</option>';
											
													}
												}
											?>
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
						<?php if(!empty($packages)){  ?>
						<?php foreach($packages as $pack){
							$pack['country_name'] = $this->Package_Model->tour_country($pack['tours_country']);
							$pack['city_name'] = $this->Package_Model->tour_city($pack['tours_city']);
							
							$countries=array();
							foreach($pack['country_name'] as $c_val){
							   $countries[]=$c_val['name'];
							} 
							
							$cities=array();
							foreach($pack['city_name'] as $c_val){
							   $cities[]=$c_val['CityName'];
							} 
							$page_data['city']= implode(', ',$cities);
							$page_data['country']= implode(', ',$countries);
							
							
						?>
         
							<li class="each_package col-md-12 col-xs-12 nopadMob nopad">
								<input type="hidden" class="tour_type" value="<?=$pack['trip_type']?>">
								<input type="hidden" class="tour_cat" value="<?=$pack['tour_pack_type']?>">
								<div class="inlitp">
									<div class="tpimage col-sm-3 col-xs-3 mobile_width nopad">
										<img
											src="<?php echo $GLOBALS['CI']->template->domain_images($pack['banner_image']); ?>"
											alt="<?php echo $pack['package_name']; ?>" />
									</div>
									<div class="tpcontent col-sm-7 col-xs-7 mobile_width">
										<h3 class="tptitle txtwrapRow"><?php echo $pack['package_name']; ?> </h3>
										 <p class="id-sub-p"><i class="fa fa-map-marker x2" aria-hidden="true"></i> &nbsp; City : <?=$page_data['city']?><br><i class="fa fa-globe x2" aria-hidden="true"></i> &nbsp; Country : <?=$page_data['country']?></p>
										<div class="htladrsxl"></div>
										<div class="clear"></div>
										
									</div>

									<div class="pkprice col-sm-2 col-xs-2 mobile_width nopad">
										<div class="pricebolk">	<strong> <?php echo $currency_obj->get_currency_symbol($currency_obj->to_currency); ?>  <?php echo number_format(get_converted_currency_value ( $currency_obj->force_currency_conversion ( $pack['netprice_price'] ) ),2);?></strong></div>
										<div class="durtio"><?php echo ($pack['duration']); ?> Nights / <?php echo $pack['duration']+1; ?> Days</div>
					
									<a class="relativefmsub trssxl"
										href="<?php echo base_url(); ?>index.php/tours/details/<?php echo $pack['pack_id']; ?>">
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
$(document).on('change','.fil_tour_country',function(){
	var tour_id  = $(this).val();
	//	alert(tour_id);
	$.post('<?=base_url();?>index.php/tours/ajax_tours_country',{'tours_country':tour_id},function(data){
		console.log(data);
		$('.fil_tour_city').html('<option value="0">All</option>'+data);
	})
});
$(document).on('change','.tour_type_fil',function(){
	//	var this_id=$(this).attr('id');
	//$('.tour_type_fil').attr('checked','');
	//$('#'+this_id).attr('checked','checked');
	var tour_type=$(this).val();
	//var tour_cat=$('.trip_cat_fil').val();
	var tour_cat=$("input[name='groupRadio1']:checked").val();
	
	package_filter(tour_type,tour_cat);
});
$(document).on('change','.trip_cat_fil',function(){
	//var tour_type=$('.tour_type_fil').val();
	var tour_type=$("input[name='customRadio1']:checked").val();
	var tour_cat=$(this).val();
	package_filter(tour_type,tour_cat);
});
function package_filter(tour_type,tour_cat){
	//alert(tour_type);alert(tour_cat);
	$('.each_package').each(function() {
		var pack_trip_type=$(this).find('.tour_type').val();
		var pack_tour_cat=$(this).find('.tour_cat').val();
		
		if(pack_trip_type==tour_type){
			//$(this).removeClass('hide');
			var type_flag=1;
		}else{
			if(tour_type==0){ 
				//$(this).addClass('hide');
				var type_flag=1;
			}else{
				var type_flag=0;
			}
		}
		if(pack_tour_cat==tour_cat){
			//$(this).removeClass('hide');
			var cat_flag=1;
		}else{
			if(tour_cat==0){ 
				//$(this).addClass('hide');
				var cat_flag=1;
			}else{
				var cat_flag=0;
			}
		}
		//console.log(type_flag,cat_flag);
		if(type_flag==1 && cat_flag==1){
			$(this).removeClass('hide');
		}else{
			$(this).addClass('hide');
		}
	});
}







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
<style type="text/css">
	#contentTitle{
		color: #fff;
	    background-color: #63c9f3;
	    padding: 10px;
	    font-size: 20px;
	    text-align: center;
	}
	.tabspl.forhotelonly {
	    padding: 20px;
	}
	.tourfilter {
	    border: 1px solid #63c9f3;
	    background: #ffffff;
	    padding: 0;
	}
	form#holiday_search .col-md-12.col-sm-6.col-xs-6.mobile_width.padfive .normalsel {
	    border: 1px solid #cccccc !important;
	    border-radius: 15px;
	    -moz-appearance: none;
	    background: transparent;
    	font-size: 12px;
	}
	.searchsbmtfot {
	    border-radius: 15px;
	}
	.tpimage {
	    border: 1px solid #eee;
	    border-radius: 15px;
	}
	.htladrsxl {
	    color: #adadad;
	}
	.durtio {
	    color: #a7a7a7;
	}
	.inlitp {
	    border: 1px solid #ccc;
	    padding: 10px;
	    box-shadow: 1px 2px 6px #ccc;
	}
	.inlitp:hover{
		border: 1px solid #000;
	}
	.cnclpoly {
	    padding-top: 45px;
	}
	.sfitlblx {
		text-transform: unset;
	}
	.col-md-9.id-filter.text-center {
    margin: 30px -17px -39px;
    background: #fff;
    border: 1px solid #f7f7f7;
    box-shadow: 2px 0px 3px #cccc;
    padding: 10px;
    border-radius: 10px 10px 0px 0px;
    float: right;
}
label.custom-control-label {
    top: -2px;
    position: relative;
    left: 5px;
}
</style>