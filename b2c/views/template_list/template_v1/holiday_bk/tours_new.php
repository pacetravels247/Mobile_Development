<link  href="<?php echo $GLOBALS['CI']->template->template_css_dir('custom_tour.css') ?>" rel="stylesheet">
<link  href="<?php echo $GLOBALS['CI']->template->template_css_dir('custom_sky.css') ?>" rel="stylesheet">



<div class="full witcontent  marintopcnt">
  <div class="container">
    <div class="container offset-0">
      <div class="cnclpoly">
        <h1 id="contentTitle" class="h3 text_center">Tours And Packages</h1>
        <span class="cancelpara"><?php //echo $caption->caption; ?></span>
        <div class="clear"></div>
         <div class="tourfilter">
            <form action="<?php echo base_url(); ?>index.php/tours/search" method="get" >
            <div class="col-md-10 nopad">
                <div class="col-md-3">
                    <span class="formlabel">Country Name</span>
                    <div class="selectedwrap">
                      <select class="mySelectBoxClass flyinputsnor" id="country" name="country">
                         <option value="">All</option>
                         <?php if(!empty($countries)){ ?>
                         <?php foreach ($countries as $country) { ?>
                            <option value="<?php echo $country->package_country; ?>" <?php if(isset($scountry)){ if($scountry == $country->package_country) echo "selected"; }?>><?php echo $country_name = $this->Package_Model->getCountryName($country->package_country)->name; ?></option>
                         <?php } } ?>
                    </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <span class="formlabel">Packages List</span>
                    <div class="selectedwrap">
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
                <div class="col-md-3">
                    <span class="formlabel">Duration</span>
                    <div class="selectedwrap">
                    <select class="mySelectBoxClass flyinputsnor" id="duration" name="duration" >
                        <option value="">All Durations</option>
                        <option value="1-3" <?php if(isset($sduration)){ if($sduration == '1-3') echo "selected"; } ?>>1-3</option>
                        <option value="4-7" <?php if(isset($sduration)){ if($sduration == '4-7') echo "selected"; } ?>>4-7</option>
                        <option value="8-12" <?php if(isset($sduration)){ if($sduration == '8-12') echo "selected"; } ?>>8-12</option>
                        <option value="12" <?php if(isset($sduration)){ if($sduration == '12') echo "selected"; } ?>>12</option>
                    </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <span class="formlabel">Budget</span>
                    <div class="selectedwrap">
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
            <div class="col-md-2 nopad">
            	<button class="modify himargin">Search Holiday</button>
            </div>
            </form>
        </div>
        <div class="clear"></div>
        <div id="packgtr" class="packgtr">
          <ul id="container" class="row"> 
          <?php if(!empty($packages)){?>
          <?php foreach($packages as $pack){?>
          <?php $country_name = $this->Package_Model->getCountryName($pack->package_country); ?>
            <li class="col-md-4">
              <div class="inlitp">
                <div class="tpimage"> <img src="<?php echo $GLOBALS['CI']->template->domain_images(basename($pack->image)); ?>" alt="<?php echo $pack->package_name; ?>" /> </div>
                <div class="tpcontent">
          
                    <h3 class="tptitle"><?php echo $pack->package_name; ?> </h3>
                    <div class="htladrsxl"><?php echo $country_name->name; ?> | <?php echo $pack->package_city; ?>  </div>
          
                  
                  <div class="clear"></div>
                  <p> <?php echo substr($pack->package_description, 0,300); ?></p>
                  
                </div>
                  
                <div class="clear"></div>
          
                    <div class="pkprice">
                    <div class="pricebolk"><?php echo $pack->price;?></div>
                    <div class="durtio"><?php echo ($pack->duration-1); ?> Nights / <?php echo $pack->duration; ?> Days</div>
                  </div>
                <div class="clear"></div>
          
                <a class="relativefmsub trssxl" href="<?php echo base_url(); ?>index.php/tours/details/<?php echo $pack->package_id; ?>">
                    <span class="sfitlblx">View Detail</span>
                    <span class="srcharowx"></span>
                </a>
          
                
                </div>
            </li>
            <?php }?>
            <?php }else{?>
             <li class="tpli cenful">
              <div class="inlitp">
                <div class="tpimagexl"> <img src="" alt="No Packages Found" /> </div>
                <div class="tpcontent">
                  <h3 class="tptitle center">No Packages Found </h3>
                </div>
          
                
                <a class="relativefmsub trssxl" href="<?php echo base_url(); ?>index.php/home/tours">
                    <span class="sfitlblx">Reset Filters</span>
                    <span class="srcharowx"></span>
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

<script src="<?php echo ASSETS;?>/js/jquery.masonry.min.js" type="text/javascript"></script> 
<script type="text/javascript">
$(document).ready(function(){
	var $container = $('#packgtr');
	$container.imagesLoaded( function() {
		$container.masonry({itemSelector:        '.tpli'});
	});
});

$(window).resize(function(){
	var $container = $('#packgtr');
	$container.imagesLoaded( function() {
		$container.masonry({itemSelector:        '.tpli'});
	});
});
</script>
</body></html>
