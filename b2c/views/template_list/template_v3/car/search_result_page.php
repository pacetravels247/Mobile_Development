<style type="text/css">
	
.second_sec{margin:20px 0;}
.second_sec a {text-decoration: none;}
.airinage img {width: 100%;height: 80px;}
.airinage {
    background: #F4F4F4 none repeat scroll 0 0;
    display: block;
    margin: 0 10px;
    min-height: 85px;
    overflow: hidden;
    padding: 5px;
    text-align: center;
    max-height: 180px;
    min-height: 180px;
}
.topmatrix_ailine {
    color: #333;
    margin-top: 5px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: clip;
    margin: 12px 0;
}
#owl-demo2 .owl-prev {
    position: absolute;
    left: -35px;
    top: 30%;
    background: url(../images/prev_icon.png);
    width: 35px;
    height: 60px;
    background-repeat: no-repeat;
}
#owl-demo2 .owl-next {
    position: absolute;
    right: -35px;
    top: 30%;
    background: url(../images/next_icon.png);
    width: 35px;
    height: 60px;
    background-repeat: no-repeat;
}
.features {
    display: block;
    width: 35%;
    overflow: hidden;
    margin: 12px auto 5px;
}
.features li {
    float: left;
    padding: 0px 3px;
    border-right: 1px solid #cbcbcc;
    list-style: none;
}
.features li strong {
    color: #525252;
    display: block;
    float: left;
    font-size: 15px;
    font-weight: normal;
    line-height: 25px;
}
.features li.person span {
    background-position: 0 0;
}
.features li .mn-icon {
    
    display: block;
    float: left;
    height: 24px;
    margin: 0 0px 0 5px;
    width: 24px;
}
.airinage{
    transition: all 600ms ease-in-out;
}
#owl-demo2 .airinage:hover{
    border: 1px solid red;
    transition: all 600ms ease-in-out;
}

.madgrid .col-xs-12.nopad {
    width: 100%;
    display: table;
}

.sidenamedesc {
    display: block;
    width: 75%;
    display: table-cell;
}

.celhtl.width60 {
    float: none;
    display: table-cell;
    vertical-align: middle;
}

.width20 {
    float: none !important;
    vertical-align: middle;
    display: table-cell !important;
}
</style>
<?php
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/car_search.js'), 'defer' => 'defer');
// Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/jquery-1.11.0.js'), 'defer' => 'defer');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('owl.carousel.min.js'), 'defer' => 'defer');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/pax_count.js'), 'defer' => 'defer');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/flight_session_expiry_script.js'), 'defer' => 'defer');
Js_Loader::$js[] = array('src' => JAVASCRIPT_LIBRARY_DIR.'jquery.jsort.0.4.min.js', 'defer' => 'defer');
Js_Loader::$css[] = array('href' => $GLOBALS['CI']->template->template_css_dir('page_resource/car_result.css'), 'media' => 'screen');
Js_Loader::$css[] = array('href' => $GLOBALS['CI']->template->template_css_dir('page_resource/owl.carousel.css'), 'media' => 'screen');
Js_Loader::$js[] = array('src' => JAVASCRIPT_LIBRARY_DIR.'jquery.nicescroll.js', 'defer' => 'defer');
echo $this->template->isolated_view('share/js/lazy_loader');
foreach ($active_booking_source as $t_k => $t_v) {
	$active_source[] = $t_v['source_id'];
}
$active_source = json_encode($active_source);
?>
<script>
var search_session_alert_expiry = "<?php echo $GLOBALS ['CI']->config->item ( 'flight_search_session_expiry_alert_period' ); ?>";
var search_session_expiry = "<?php echo $GLOBALS ['CI']->config->item ( 'flight_search_session_expiry_period' ); ?>";
var search_hash = '';
var session_time_out_function_call = 0;
var api_request_cnt = 0;
	var load_car = function(loader, offset, filters){		
		offset = offset || 0;
		var url_filters = '';
		$(".car_filter_load").show();
		if ($.isEmptyObject(filters) == false) {
			url_filters = '&'+($.param({'filters':filters}));
			
		}
		_lazy_content = $.ajax({
			type: 'GET',
			url: app_base_url+'index.php/ajax/car_list/'+offset+'?booking_source=<?=$active_booking_source[0]['source_id']?>&search_id=<?=$car_search_params['search_id']?>&op=load'+url_filters,
			async: true,
			cache: true,
			//dataType: 'json',
			success: function(res) {
				loader(res);
				$(".car_filter_load").hide();
				$("#result_found_text").removeClass('hide');
			}
		});		
	}
	var interval_load = function (res) {
		
		var dui;
		var r = res;
		dui = setInterval(function(){
			if (typeof(process_result_update) != "undefined" && $.isFunction(process_result_update) == true) {
				clearInterval(dui);
				process_result_update(r);
	     		
	     		ini_result_update(r);
	    	}
		}, 1);
	};
	load_car(interval_load);

</script>
<span class="hide">
	<input type="hidden" id="pri_search_id" value='<?=$car_search_params['search_id']?>'>
	<input type="hidden" id="pri_active_source" value='<?=$active_source?>'>
	<input type="hidden" id="pri_app_pref_currency" value='<?= $this->currency->get_currency_symbol(get_application_currency_preference()) ?>'>
	<?php // echo $this->currency->get_currency_symbol(get_application_display_currency_preference());?>
</span>
<?php
	//debug($car_search_params); exit;
	$data['result'] = $car_search_params;
	$mini_loading_image = '<div class="text-center loader-image"><img src="'.$GLOBALS['CI']->template->template_images('loader_v3.gif').'" alt="Loading........"/></div>';
	$loading_image = '<div class="text-center loader-image"><img src="'.$GLOBALS['CI']->template->template_images('loader_v1.gif').'" alt="Loading........"/></div>';
	$template_images = $GLOBALS['CI']->template->template_images();
	echo $GLOBALS['CI']->template->isolated_view('car/search_panel_summary');
	?>

	<div id="page-parent">
		
		<div class="allpagewrp top80">
		
		  <div class="clearfix"></div>
		  <div class="search-result car_search_results">
		    <div class="container">
		     <?php echo $GLOBALS['CI']->template->isolated_view('share/loader/car_result_pre_loader', $car_search_params); ?>
		      <div class="filtrsrch">
		        <div class="coleft">
		          <div class="flteboxwrp">
                  
		           
		             <div class="filtersho">                        
		             <div class="avlhtls"><strong id="filter_records">0</strong> Car found
                        </div>                       
                       <span class="close_fil_box"><i class="fa fa-close"></i></span>                    
                       </div>
		            
		            <div class="fltrboxin"> 
		             <a id="reset_filters" class="pull-right">Reset All</a>
		              <div class="bnwftr">
		                
		                <div class="rangebox">
							<button data-target="#collapse501" data-toggle="collapse"
									class="collapsebtn" type="button">Price</button>
									<strong><span id="total_result_count"><?php echo $mini_loading_image?></span></strong>
							<div id="collapse501" class="in collapse price_slider1">
								<div class="price_slider1">
									<div id="core_min_max_slider_values" class="hide">
										<input type="hidden" id="core_minimum_range_value" />
										<input type="hidden" id="core_maximum_range_value" />
									</div>
									<p id="car-price" class="level"></p>
									<div id="price-range" class="" aria-disabled="false"></div>
								</div>
							</div>
						</div>

                        <div class="rangebox">
							<button data-target="#collapse505" data-toggle="collapse" class="collapsebtn" type="button">Supplier</button>
							<div id="collapse505" class="collapse in">
								<div class="boxins">
									<ul class="locationul" id="car-vendor-list-wrapper">
									
									</ul>
								</div>
							</div>
		                </div>

		              
		                 <div class="rangebox">
							<button data-target="#collapse510" data-toggle="collapse" class="collapsebtn" type="button">Auto/Manual</button>
							<div id="collapse510" class="collapse in">
								<div class="boxins">
									<ul class="locationul" id="car-vehicle-manual-wrapper"></ul>
								</div>
							</div>
		                </div> 
		                
		                <!-- <div class="rangebox">
							<button data-target="#collapse506" data-toggle="collapse" class="collapsebtn" type="button">Included Insurance</button>
							<div id="collapse506" class="collapse in">
								<div class="boxins">
									<ul class="locationul" id="car-package-list-wrapper">
									</ul>
								</div>
							</div>
		                </div> -->
		               
		                <div class="rangebox">
							<button data-target="#collapse515" data-toggle="collapse" class="collapsebtn" type="button">AC/Non AC</button>
							<div id="collapse515" class="collapse in">
								<div class="boxins">
									<ul class="locationul" id="car-vehicle-ac-wrapper">
									</ul>
								</div>
							</div>
		                </div> 
		                <div class="septor"></div>
						<div class="rangebox">
							<button data-target="#collapse502" data-toggle="collapse"
									class="collapsebtn" type="button">Package</button>
							<div id="collapse502" class="collapse in">
								<div class="boxins">
									<ul class="locationul" id="car-vehicle-package-wrapper">
									</ul>
								</div>
							</div>
		                </div>
		                <div class="septor"></div>
		                <div class="rangebox">
							<button data-target="#collapse503" data-toggle="collapse" class="collapsebtn" type="button">Door Count</button>
							<div id="collapse503" class="collapse">
								<div class="boxins">
									<ul class="locationul" id="car-door-count-wrapper">
									</ul>
								</div>
							</div>
		                </div>
		                <div class="septor"></div>		                
		                <div class="rangebox">
							<button data-target="#collapse504" data-toggle="collapse"
									class="collapsebtn" type="button">Passenger Count</button>
							<div id="collapse504" class="collapse">
								<div class="boxins">
									<ul class="locationul" id="car-passenger-quantity-wrapper">
									</ul>
								</div>
							</div>
		                </div>		                
		                <div class="septor"></div>
						<div class="rangebox">
		                	<button data-target="#collapse507" data-toggle="collapse" class="collapsebtn" type="button">Car Category</button>
							<div id="collapse507" class="collapse">
								<div class="boxins">
									<ul class="locationul" id="car-vehicle-category-wrapper">
									</ul>
								</div>
							</div>
		                </div>
		                <div class="septor"></div>
		                <div class="rangebox">
							 <button data-target="#collapse508" data-toggle="collapse"
									class="collapsebtn" type="button">Car Size</button>
							<div id="collapse508" class="collapse">
								<div class="boxins">
									<ul class="locationul" id="car-vehicle-size-wrapper">
									</ul>
								</div>
							</div>
		                </div>
		                
		              </div>
		            </div>
		          </div>
		        </div>
		        <div class="colrit">
		          <div class="insidebosc">
                 

                  <div class="topmisty hote_reslts">              
                  <div class="col-xs-12 nopad fullshort">               
                   <button class="filter_show"><i class="fa fa-filter"></i> <span class="text_filt">Filter</span></button>                

	                   <div class="insidemyt">                  
		                   <div class="col-xs-12 nopad">                    
			                   <ul class="sortul">                    
			                   <li class="sortli" data-sort="hn">
			                   <a class="sorta asc name-l-2-h" data-order="asc"><span class="fa fa-sort-alpha-asc"></span> Car Name</a>
			                   <a class="sorta des name-h-2-l hide" data-order="desc"><span class="sirticon fa fa-sort-alpha-desc"></span> Car Name</a>
			                   </li>

			                   <li class="sortli" data-sort="s">
			                   <a class="sorta asc supplier-l-2-h" data-order="asc"><span class="sirticon fa fa-user"></span> Supplier</a>
			                   <a class="sorta des supplier-h-2-l hide" data-order="desc"><span class="sirticon fa fa-user"></span> Supplier</a>
			                   </li>

			                   <li class="sortli" data-sort="sr">
			                   <a class="sorta asc cartype-l-2-h" data-order="asc"><span class="sirticon fa fa-star-o"></span> Category</a>
			                   <a class="sorta des cartype-h-2-l hide" data-order="desc"><span class="sirticon fa fa-star-o"></span> Category</a>
			                   </li>                        

			                   <li class="sortli" data-sort="p">
			                   <a class="sorta asc price-l-2-h hide" data-order="asc"><span class="sirticon fa fa-tag"></span> Price</a>
			                   <a class="sorta des price-h-2-l active" data-order="desc"><span class="sirticon fa fa-tag"></span> Price</a>
			                   </li>
			                   </ul>                  
		                   </div>                
	                   </div>              
                   </div>            
                   </div>
		           
		            <!--All Available cars result comes here -->
					<div class="car_results" id="car_search_result">
						</div>
					<!-- End of result -->
				  <!--Map view indipendent hotel-->
		          </div>
		        </div>
		      </div>
		    </div>
		  </div>
		</div>
	</div>
	 
	<div id="empty-search-result" class="jumbotron container" style="display:none">
		<h1><i class="fa fa-taxi"></i> Oops!</h1>
		<p>No cars were found in this location today.</p>
		<p>
			Search results change daily based on availability.If you have an urgent requirement, please get in touch with our call center using the contact details mentioned on the home page. They will assist you to the best of their ability.
		</p>
	</div>
	<?php echo $GLOBALS['CI']->template->isolated_view('share/flight_session_expiry_popup');?>

<script>
/*	$('.filter_show').click(function(){
		$('.filtrsrch').addClass('open');
	});
	
	$('.close_filter').click(function(){
		$('.filtrsrch').removeClass('open');
	});*/
	
	/*  Mobile Filter  */
	$('.filter_show').click(function() {
		$('.filtrsrch').stop( true, true ).toggleClass('open');
		$('.col30').addClass('round_filt');
		// $('.col30').stop( true, true ).slideToggle(500);
		$(".col30.round_filt").show();
	});
	$(".close_fil_box").click(function(){
			$(".col30.round_filt").hide();
	});	
	
	</script>
	<!-- <script type="text/javascript" src="js/owl.carousel.min.js"></script> -->
<script>
  $(document).ready(function(){
    $("#owl-demo2").owlCarousel({
        items : 6, 
        itemsDesktop : [1000,6],
        itemsDesktopSmall : [900,4], 
        itemsTablet: [600,2], 
        itemsMobile : [479,1], 
        navigation : true,
        navigationText: [],
        pagination : false,
        autoPlay : 5000
    });


  });
</script>


 <script type="text/javascript">
   $(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
   });
  </script>