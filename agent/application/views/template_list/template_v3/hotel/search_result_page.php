<?php
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/hotel_search.js'), 'defer' => 'defer');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/pax_count.js'), 'defer' => 'defer');
Js_Loader::$js[] = array('src' => JAVASCRIPT_LIBRARY_DIR.'jquery.jsort.0.4.min.js', 'defer' => 'defer');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('jquery.nicescroll.js'), 'defer' => 'defer');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/marker_cluster.js'), 'defer' => 'defer');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/sweet_alert.min.js'), 'defer' => 'defer');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/hotel_search_opt.js'), 'defer' => 'defer');

echo $this->template->isolated_view('share/js/lazy_loader');
foreach ($active_booking_source as $t_k => $t_v) {
	$active_source[] = $t_v['source_id'];
}
$active_source = json_encode($active_source);
?>
<script>
var load_hotels = function(loader, offset, filters){
	offset = offset || 0;
	var url_filters = '';
	if ($.isEmptyObject(filters) == false) {
		url_filters = '&'+($.param({'filters':filters}));
	}
	_lazy_content = $.ajax({
		type: 'GET',
		url: app_base_url+'index.php/ajax/hotel_list/'+offset+'?booking_source=<?=$active_booking_source[0]['source_id']?>&search_id=<?=$hotel_search_params['search_id']?>&op=load'+url_filters,
		async: true,
		cache: true,
		dataType: 'json',
		success: function(res) {
			loader(res);
			$('#onwFltContainer').hide();
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
load_hotels(interval_load);
</script>
<span class="hide">
	<input type="hidden" id="pri_search_id" value='<?=$hotel_search_params['search_id']?>'>
	<input type="hidden" id="pri_active_source" value='<?=$active_source?>'>
	<input type="hidden" id="pri_app_pref_currency" value='<?=$this->currency->get_currency_symbol(get_application_currency_preference())?>'>
	<input type="hidden" id="api_base_url" value="<?=$GLOBALS['CI']->template->template_images()?>">
	<input type="hidden" id="api_booking_source" value="<?=$active_booking_source[0]['source_id']?>">
	<input type="hidden" id="default_loader" value="<?=$GLOBALS['CI']->template->template_images('image_loader.gif') ?>">
	<input type="hidden" id="pagination_loader" value="<?=$GLOBALS['CI']->template->template_images('loader_v1.gif')?>">
</span>
<?php
$data['result'] = $hotel_search_params;
$mini_loading_image = '<div class="text-center loader-image"><img src="'.$GLOBALS['CI']->template->template_images('loader_v3.gif').'" alt="Loading........"/></div>';
$loading_image = '<div class="text-center loader-image" style="display:none;"><img src="'.$GLOBALS['CI']->template->template_images('loader_v1.gif').'" alt="Loading........"/></div>';
$template_images = $GLOBALS['CI']->template->template_images();
echo $GLOBALS['CI']->template->isolated_view('hotel/search_panel_summary');
		
Js_Loader::$css[] = array('href' => $GLOBALS['CI']->template->template_css_dir('owl.carousel.min.css'), 'media' => 'screen');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('owl.carousel.min.js'), 'defer' => 'defer');

?>
<section class="search-result hotel_search_results">
	<div class="container-fluid"  id="page-parent">
	<?php echo $GLOBALS['CI']->template->isolated_view('share/loader/hotel_result_pre_loader',$data);?>
		<div class="resultalls">
		   
			<div class="coleft" id="coleftid">
				<div class="flteboxwrp">
				     
					<div class="filtersho">
						<div class="avlhtls"><strong id="filter_records"></strong> <span class="hide"> of <strong id="total_records"><?php echo $mini_loading_image?></strong> </span> Hotels found
						</div>
						<span class="close_fil_box"><i class="fas fa-times"></i></span>
					</div>
					<div class="fltrboxin">
					<form autocomplete="off">
						<div class="celsrch refine">
								<div class="row">
									<a class="pull-right" id="reset_filters">RESET ALL</a>
								</div>


<div class="bnwftr">
    <div class="panel-group" id="accordion">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne"><span class="glyphicon glyphicon-chevron-down"></span>Price</a>
                </h4>
            </div>
            <div id="collapseOne" class="panel-collapse collapse in">
                <div class="panel-body">
                    	<?php echo $mini_loading_image?>
									<div id="price-refine" class="in">
										<div class="price_slider1">
											<div id="core_min_max_slider_values" class="hide">
											<input type="hiden" id="core_minimum_range_value" value="">
											<input type="hiden" id="core_maximum_range_value" value="">
											</div>
											<p id="hotel-price" class="level"></p>
											<div id="price-range" class="" aria-disabled="false"></div>
										</div>
									</div>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo"><span class="glyphicon glyphicon-chevron-down"></span>Star Rating</a>
                </h4>
            </div>
            <div id="collapseTwo" class="panel-collapse collapse in">
                <div class="panel-body">
                    	<div id="collapse2" class="in">
									<div class="boxins marret" id="starCountWrapper">
										<a class="starone toglefil star-wrapper">
											<input class="hidecheck star-filter" type="checkbox" value="1">
											<div class="starin">
												<span class="rststrne">1</span> 
												<span class="starfa fas fa-star"></span>
												<span class="htlcount">-</span>
											</div>
										</a>
										<a class="starone toglefil star-wrapper">
											<input class="hidecheck star-filter" type="checkbox" value="2">
											<div class="starin">
												 <span class="rststrne">2</span>
												 <span class="starfa fas fa-star"></span>
												<span class="htlcount">-</span>
											</div>
										</a>
										<a class="starone toglefil star-wrapper">
											<input class="hidecheck star-filter" type="checkbox" value="3">
											<div class="starin">
												<span class="rststrne">3</span>
												<span class="starfa fas fa-star"></span>
												<span class="htlcount">-</span>
											</div>
										</a>
										<a class="starone toglefil star-wrapper">
											<input class="hidecheck star-filter" type="checkbox" value="4">
											<div class="starin">
												<span class="rststrne">4</span>
											    <span class="starfa fas fa-star"></span>
												<span class="htlcount">-</span>
											</div>
										</a>
										<a class="starone toglefil star-wrapper">
											<input class="hidecheck star-filter" type="checkbox" value="5">
											<div class="starin">
												<span class="rststrne">5</span>
											    <span class="starfa fas fa-star"></span>
												<span class="htlcount">-</span>
											</div>
										</a>
									</div>
								</div>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree"><span class="glyphicon glyphicon-chevron-down"></span>Hotel Name</a>
                </h4>
            </div>
            <div id="collapseThree" class="panel-collapse collapse in">
                <div class="panel-body">
                   	<div id="hotelsearch-refine" class="in">
									<div class="boxins">
										<div class="relinput">
											<input type="text" class="srchhtl" placeholder="Hotel name" id="hotel-name" />
											<input type="submit" class="srchsmall" id="hotel-name-search-btn" value="" />
										</div>
									</div>
								</div>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseSix"><span class="glyphicon glyphicon-chevron-down"></span>Amenities</a>
                </h4>
            </div>
            <div id="collapseSix" class="panel-collapse collapse in">
                <div class="panel-body">
                		<div id="collapse6" class="in">								
							<div class="boxins">
								<ul class="" id="hotel-amenitie-wrapper">
										<li>
											<div class="squaredThree">
													<input type="checkbox" id="wifi-hotels-view" value="filter" class="wifi-hotels-view" name="amenitie[]">
												<label for="wifi-hotels-view"></label>
											</div>
											<label class="lbllbl" for="wifi-hotels-view">Wi-Fi</label>
										</li>
										<li>
											<div class="squaredThree">
													<input type="checkbox" id="break-hotels-view" value="filter" class="break-hotels-view" name="amenitie[]">
												<label for="break-hotels-view"></label>
											</div>
											<label class="lbllbl" for="break-hotels-view">Breakfast</label>
										</li>
										<li>
											<div class="squaredThree">
													<input type="checkbox" id="parking-hotels-view" value="filter" class="parking-hotels-view" name="amenitie[]">
												<label for="parking-hotels-view"></label>
											</div>
											<label class="lbllbl" for="parking-hotels-view">Parking</label>
										</li>
										<li>
											<div class="squaredThree">
													<input type="checkbox" id="pool-hotels-view" value="filter" class="pool-hotels-view" name="amenitie[]">
												<label for="pool-hotels-view"></label>
											</div>
											<label class="lbllbl" for="pool-hotels-view">Swimming Pool</label>
										</li>
									</ul>
								</div>
						</div>
                </div>
            </div>
        </div>
		<div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseseleven"><span class="glyphicon glyphicon-chevron-down"></span>Board Type</a>
                </h4>
            </div>
            <div id="collapseseleven" class="panel-collapse collapse in">
                <div class="panel-body">
                		<div id="collapse6" class="in">								
							<div class="boxins">
								<ul class="" id="hotel-board-wrapper">
										<li>
											<div class="squaredThree">
													<input type="checkbox" id="ro-hotels-view" value="filter" class="ro-hotels-view" name="board[]">
												<label for="ro-hotels-view"></label>
											</div>
											<label class="lbllbl" for="ro-hotels-view">Room Only</label>
										</li>
										<li>
											<div class="squaredThree">
													<input type="checkbox" id="bb-hotels-view" value="filter" class="bb-hotels-view" name="board[]">
												<label for="bb-hotels-view"></label>
											</div>
											<label class="lbllbl" for="bb-hotels-view">Breakfast</label>
										</li>
										<li>
											<div class="squaredThree">
													<input type="checkbox" id="hb-hotels-view" value="filter" class="hb-hotels-view" name="board[]">
												<label for="hb-hotels-view"></label>
											</div>
											<label class="lbllbl" for="hb-hotels-view">Half Board</label>
										</li>
										<li>
											<div class="squaredThree">
													<input type="checkbox" id="fb-hotels-view" value="filter" class="fb-hotels-view" name="board[]">
												<label for="fb-hotels-view"></label>
											</div>
											<label class="lbllbl" for="fb-hotels-view">Full Board</label>
										</li>
									</ul>
								</div>
						</div>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseSix"><span class="glyphicon glyphicon-chevron-down"></span>Hotel Free Cancellation</a>
                </h4>
            </div>
            <div id="collapseFive" class="panel-collapse collapse in">
                <div class="panel-body">
                		<div id="collapse4" class="in">								
								<div class="squaredThree">
										<input type="checkbox" id="freecancel-hotels-view" value="filter" class="freecancel-hotels-view" name="free_cancel[]">
									<label for="freecancel-hotels-view"></label>
								</div>
									<label class="lbllbl" for="freecancel-hotels-view">Free Cancellation</label>

						</div>
                </div>
            </div>
        </div>
		<div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseSix"><span class="glyphicon glyphicon-chevron-down"></span>Rate Type</a>
                </h4>
            </div>
            <div id="collapseFive" class="panel-collapse collapse in">
                <div class="panel-body">
                		<div id="collapse4" class="in">								
								<div class="squaredThree">
										<input type="checkbox" id="refundable-hotels-view" value="filter" class="refundable-hotels-view" name="rate_type[]">
									<label for="refundable-hotels-view"></label>
								</div>
									<label class="lbllbl" for="refundable-hotels-view">Refundable</label>

						</div>
						<div id="collapse4" class="in">								
								<div class="squaredThree">
										<input type="checkbox" id="nonrefundable-hotels-view" value="filter" class="nonrefundable-hotels-view" name="rate_type[]">
									<label for="nonrefundable-hotels-view"></label>
								</div>
									<label class="lbllbl" for="nonrefundable-hotels-view">Non-refundable</label>

						</div>
                </div>
            </div>
        </div>
		<!--<div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseSix"><span class="glyphicon glyphicon-chevron-down"></span>Rate Type</a>
                </h4>
            </div>
            <div id="collapseFive" class="panel-collapse collapse in">
                <div class="panel-body">
                		<div id="collapse4" class="in">								
								<div class="squaredThree">
										<input type="checkbox" id="refundable-hotels-view" value="filter" class="refundable-hotels-view" name="rate_type[]">
									<label for="refundable-hotels-view"></label>
								</div>
									<label class="lbllbl" for="refundable-hotels-view">Refundable</label>
						</div>
						<div id="collapse4" class="in">								
								<div class="squaredThree">
										<input type="checkbox" id="nonrefundable-hotels-view" value="filter" class="nonrefundable-hotels-view" name="rate_type[]">
									<label for="nonrefundable-hotels-view"></label>
								</div>
									<label class="lbllbl" for="nonrefundable-hotels-view">Non-refundable</label>
						</div>
                </div>
            </div>
        </div>-->
         <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseFour"><span class="glyphicon glyphicon-chevron-down"></span>Hotel Location</a>
                </h4>
            </div>
            <div id="collapseFour" class="panel-collapse collapse in">
                <div class="panel-body">
                    		<div id="collapse2" class="in">
									<div class="boxins">
										<ul class="locationul" id="hotel-location-wrapper">
										</ul>
									</div>
						</div>
                </div>
            </div>
        </div> 
    </div>
</div>

								<!-- <div class="rangebox">
									<button data-target="#price-refine" data-toggle="collapse" class="collapsebtn refine-header" type="button">
								Price
								</button>
								<?php echo $mini_loading_image?>
									<div id="price-refine" class="in">
										<div class="price_slider1">
											<div id="core_min_max_slider_values" class="hide">
											<input type="hiden" id="core_minimum_range_value" value="">
											<input type="hiden" id="core_maximum_range_value" value="">
											</div>
											<p id="hotel-price" class="level"></p>
											<div id="price-range" class="" aria-disabled="false"></div>
										</div>
									</div>
								</div>
								<div class="septor"></div>
								<div class="rangebox">
								<button data-target="#collapse2" data-toggle="collapse" class="collapsebtn" type="button">
									Star Rating
								</button>
								<div id="collapse2" class="in">
									<div class="boxins marret" id="starCountWrapper">
										<a class="starone toglefil star-wrapper">
											<input class="hidecheck star-filter" type="checkbox" value="1">
											<div class="starin">
												1 <span class="starfa fas fa-star"></span>
												<span class="htlcount">-</span>
											</div>
										</a>
										<a class="starone toglefil star-wrapper">
											<input class="hidecheck star-filter" type="checkbox" value="2">
											<div class="starin">
												2 <span class="starfa fas fa-star"></span>
												<span class="htlcount">-</span>
											</div>
										</a>
										<a class="starone toglefil star-wrapper">
											<input class="hidecheck star-filter" type="checkbox" value="3">
											<div class="starin">
												3 <span class="starfa fas fa-star"></span>
												<span class="htlcount">-</span>
											</div>
										</a>
										<a class="starone toglefil star-wrapper">
											<input class="hidecheck star-filter" type="checkbox" value="4">
											<div class="starin">
												4 <span class="starfa fas fa-star"></span>
												<span class="htlcount">-</span>
											</div>
										</a>
										<a class="starone toglefil star-wrapper">
											<input class="hidecheck star-filter" type="checkbox" value="5">
											<div class="starin">
												5 <span class="starfa fas fa-star"></span>
												<span class="htlcount">-</span>
											</div>
										</a>
									</div>
								</div>
								</div>
								<div class="septor"></div>
								<div class="rangebox">
								<button data-target="#hotelsearch-refine" data-toggle="collapse" class="collapsebtn refine-header" type="button">
									Hotel Name
								</button>
								<div id="hotelsearch-refine" class="in">
									<div class="boxins">
										<div class="relinput">
											<input type="text" class="srchhtl" placeholder="Hotel name" id="hotel-name" />
											<input type="submit" class="srchsmall" id="hotel-name-search-btn" value="" />
										</div>
									</div>
								</div>
								</div>
								<div class="septor"></div>
								<div class="rangebox">
								<button data-target="#collapse2" data-toggle="collapse" class="collapsebtn" type="button">
									Hotel Location
								</button>
								<div id="collapse2" class="in">
									<div class="boxins">
										<ul class="locationul" id="hotel-location-wrapper">
										</ul>
									</div>
								</div>
								</div> -->


						</div>
						</form>
					</div>
				</div>
			</div>


			<div class="colrit">
			<div class="insidebosc">
			<div class="resultall">
			<div class="filt_map">
				<div class="filter_tab"><i class="fas fa-filter"></i></div>
			</div>
			<div class="filter_tab"><i class="fas fa-filter"></i></div>
				<div class="vluendsort">
					<!-- <div class="col-xs-5 nopad">
					<div class="nityvalue">
						 <label type="button" class="vlulike active filter-hotels-view" for="all-hotels-view">
							<input type="radio" id="all-hotels-view" value="all" class="hide deal-status-filter" name="deal_status[]" checked="checked">
							All Hotels
						</label>
					</div>
					</div> -->
					<div class="col-xs-9 mobile_width nopad">
						<div class="filterforallnty" id="top-sort-list-wrapper">
							<div class="topmistyhtl" id="top-sort-list-1">
								<div class="col-xs-12 nopad">
									<div class="insidemyt">
										<ul class="sortul">
										    <li class="sortli threonly">
										    	<label type="button" class="vlulike filter-hotels-view" for="deal-hotels-view">
													<input type="radio" id="deal-hotels-view" value="filter" class="hide deal-status-filter" name="deal_status[]">
													Deal
												</label>
										    </li>
											<li class="sortli threonly" data-sort="hn">
												<a class="sorta name-l-2-h asc" data-order="asc"><i class="fas fa-sort-alpha-down"></i> <strong>Name</strong></a>
												<a class="sorta name-h-2-l hide des" data-order="desc"><i class="fas fa-sort-alpha-down"></i> <strong>Name</strong></a>
											</li>
											<li class="sortli threonly" data-sort="sr">
												<a class="sorta star-l-2-h asc" data-order="asc"><i class="fas fa-star"></i> <strong>Star</strong></a>
												<a class="sorta star-h-2-l hide  des" data-order="desc"><i class="fas fa-star"></i> <strong>Star</strong></a>
											</li>
											<li class="sortli threonly" data-sort="p">
												<a class="sorta price-l-2-h asc" data-order="asc"><i class="fas fa-tag"></i> <strong>Price</strong></a>
												<a class="sorta price-h-2-l hide  des" data-order="desc"><i class="fas fa-tag"></i> <strong>Price</strong></a>
											</li>
										</ul>
									</div>
									<div class="map_tab"><a class="map_click"><i class="fas fa-map-marker-alt"></i></a></div>
										<div class="list_tab" style="display:  none;"><i class="fas fa-th-list"></i></div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-xs-3 mobile_none nopad">
						<div class="mapviw noviews">
                         <!-- <div class="mapviwhtl nopad noviews reswd">
	                        <div class="rit_view">
	                        	<a class="view_type grid_click"><span class="fa fa-th"></span></a> 
	                        </div>
	                      </div> -->
	                      <div class="mapviwlist nopad noviews reswd">
	                        <div class="rit_view">
	                       	 <a class="view_type list_click active" id="list_clickid" onclick="showhide()"><span class="fas fa-th-list"></span></a> 
	                        </div>
                          </div>
                          <div class="mapviwhtl nopad noviews reswd">
	                        <div class="rit_view">
	                        	<a class="view_type map_click" id="map_clickid" onclick="showhide()"><span class="fas fa-map-marker-alt"></span></a> 
	                        </div>
	                      </div>
            		    </div>
					</div>
				</div>
				
				</div>		
				<div class="allresult">
					<?php echo $loading_image;?>

					<div class="fl width100 fltRndTripWrap" id="onwFltContainer">
                            <!-- <div class="fl padTB5 width100">
                            </div> -->
                            <div class="fl width100">
                                <div class="card fl width100 marginTB10">

                                    <div class="card-block fl width100 padT20 marginT10 padB10 padLR20">
                                        <div class="col-md-2 col-sm-2 col-xs-2 padT10">
                                            <span class="db padB10 marginR20 marginB10 col-md-8 animated-background"></span>
                                            <span class="db padT10 animated-background col-md-8 marginR20"></span>
                                        </div>
                                        <div class="col-md-7 col-sm-7 col-xs-7 padT10 padLR0 brdRight">
                                            <div class="fl width100">
                                                <div class="col-md-3 col-sm-3 col-xs-3">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>

                                                <div class="col-md-5 col-sm-5 col-xs-5">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>

                                                <div class="col-md-3 col-sm-3 col-xs-3 padLR0">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-xs-3 col-sm-3 fltPrice">
                                            <div class="col-md-5 col-sm-8 col-xs-8 fr padT10">
                                                <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                <span class="animated-background db padB10 marginR20"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="fl width100 padTB10 animated-background"></div>
                                </div>
                            </div>

                            <div class="fl width100">
                                <div class="card fl width100 marginTB10">

                                    <div class="card-block fl width100 padT20 marginT10 padB10 padLR20">
                                        <div class="col-md-2 col-sm-2 col-xs-2 padT10">
                                            <span class="db padB10 marginR20 marginB10 col-md-8 animated-background"></span>
                                            <span class="db padT10 animated-background col-md-8 marginR20"></span>
                                        </div>
                                        <div class="col-md-7 col-sm-7 col-xs-7 padT10 padLR0 brdRight">
                                            <div class="fl width100">
                                                <div class="col-md-3 col-sm-3 col-xs-3">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>

                                                <div class="col-md-5 col-sm-5 col-xs-5">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>

                                                <div class="col-md-3 col-sm-3 col-xs-3 padLR0">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-xs-3 col-sm-3 fltPrice">
                                            <div class="col-md-5 col-sm-8 col-xs-8 fr padT10">
                                                <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                <span class="animated-background db padB10 marginR20"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="fl width100 padTB10 animated-background"></div>
                                </div>

                            </div>

                            <div class="fl width100">
                                <div class="card fl width100 marginTB10">

                                    <div class="card-block fl width100 padT20 marginT10 padB10 padLR20">
                                        <div class="col-md-2 col-sm-2 col-xs-2 padT10">
                                            <span class="db padB10 marginR20 marginB10 col-md-8 animated-background"></span>
                                            <span class="db padT10 animated-background col-md-8 marginR20"></span>
                                        </div>
                                        <div class="col-md-7 col-sm-7 col-xs-7 padT10 padLR0 brdRight">
                                            <div class="fl width100">
                                                <div class="col-md-3 col-sm-3 col-xs-3">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>

                                                <div class="col-md-5 col-sm-5 col-xs-5">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>

                                                <div class="col-md-3 col-sm-3 col-xs-3 padLR0">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-xs-3 col-sm-3 fltPrice">
                                            <div class="col-md-5 col-sm-8 col-xs-8 fr padT10">
                                                <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                <span class="animated-background db padB10 marginR20"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="fl width100 padTB10 animated-background"></div>
                                </div>

                            </div>

                            <div class="fl width100">
                                <div class="card fl width100 marginTB10">

                                    <div class="card-block fl width100 padT20 marginT10 padB10 padLR20">
                                        <div class="col-md-2 col-sm-2 col-xs-2 padT10">
                                            <span class="db padB10 marginR20 marginB10 col-md-8 animated-background"></span>
                                            <span class="db padT10 animated-background col-md-8 marginR20"></span>
                                        </div>
                                        <div class="col-md-7 col-sm-7 col-xs-7 padT10 padLR0 brdRight">
                                            <div class="fl width100">
                                                <div class="col-md-3 col-sm-3 col-xs-3">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>

                                                <div class="col-md-5 col-sm-5 col-xs-5">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>

                                                <div class="col-md-3 col-sm-3 col-xs-3 padLR0">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-xs-3 col-sm-3 fltPrice">
                                            <div class="col-md-5 col-sm-8 col-xs-8 fr padT10">
                                                <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                <span class="animated-background db padB10 marginR20"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="fl width100 padTB10 animated-background"></div>
                                </div>

                            </div>

                            <div class="fl width100">
                                <div class="card fl width100 marginTB10">

                                    <div class="card-block fl width100 padT20 marginT10 padB10 padLR20">
                                        <div class="col-md-2 col-sm-2 col-xs-2 padT10">
                                            <span class="db padB10 marginR20 marginB10 col-md-8 animated-background"></span>
                                            <span class="db padT10 animated-background col-md-8 marginR20"></span>
                                        </div>
                                        <div class="col-md-7 col-sm-7 col-xs-7 padT10 padLR0 brdRight">
                                            <div class="fl width100">
                                                <div class="col-md-3 col-sm-3 col-xs-3">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>

                                                <div class="col-md-5 col-sm-5 col-xs-5">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>

                                                <div class="col-md-3 col-sm-3 col-xs-3 padLR0">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-xs-3 col-sm-3 fltPrice">
                                            <div class="col-md-5 col-sm-8 col-xs-8 fr padT10">
                                                <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                <span class="animated-background db padB10 marginR20"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="fl width100 padTB10 animated-background"></div>
                                </div>

                            </div>

                            <div class="fl width100">
                                <div class="card fl width100 marginTB10">

                                    <div class="card-block fl width100 padT20 marginT10 padB10 padLR20">
                                        <div class="col-md-2 col-sm-2 col-xs-2 padT10">
                                            <span class="db padB10 marginR20 marginB10 col-md-8 animated-background"></span>
                                            <span class="db padT10 animated-background col-md-8 marginR20"></span>
                                        </div>
                                        <div class="col-md-7 col-sm-7 col-xs-7 padT10 padLR0 brdRight">
                                            <div class="fl width100">
                                                <div class="col-md-3 col-sm-3 col-xs-3">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>

                                                <div class="col-md-5 col-sm-5 col-xs-5">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>

                                                <div class="col-md-3 col-sm-3 col-xs-3 padLR0">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-xs-3 col-sm-3 fltPrice">
                                            <div class="col-md-5 col-sm-8 col-xs-8 fr padT10">
                                                <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                <span class="animated-background db padB10 marginR20"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="fl width100 padTB10 animated-background"></div>
                                </div>

                            </div>

                            <div class="fl width100">
                                <div class="card fl width100 marginTB10">

                                    <div class="card-block fl width100 padT20 marginT10 padB10 padLR20">
                                        <div class="col-md-2 col-sm-2 col-xs-2 padT10">
                                            <span class="db padB10 marginR20 marginB10 col-md-8 animated-background"></span>
                                            <span class="db padT10 animated-background col-md-8 marginR20"></span>
                                        </div>
                                        <div class="col-md-7 col-sm-7 col-xs-7 padT10 padLR0 brdRight">
                                            <div class="fl width100">
                                                <div class="col-md-3 col-sm-3 col-xs-3">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>

                                                <div class="col-md-5 col-sm-5 col-xs-5">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>

                                                <div class="col-md-3 col-sm-3 col-xs-3 padLR0">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-xs-3 col-sm-3 fltPrice">
                                            <div class="col-md-5 col-sm-8 col-xs-8 fr padT10">
                                                <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                <span class="animated-background db padB10 marginR20"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="fl width100 padTB10 animated-background"></div>
                                </div>

                            </div>

                            <div class="fl width100">
                                <div class="card fl width100 marginTB10">

                                    <div class="card-block fl width100 padT20 marginT10 padB10 padLR20">
                                        <div class="col-md-2 col-sm-2 col-xs-2 padT10">
                                            <span class="db padB10 marginR20 marginB10 col-md-8 animated-background"></span>
                                            <span class="db padT10 animated-background col-md-8 marginR20"></span>
                                        </div>
                                        <div class="col-md-7 col-sm-7 col-xs-7 padT10 padLR0 brdRight">
                                            <div class="fl width100">
                                                <div class="col-md-3 col-sm-3 col-xs-3">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>

                                                <div class="col-md-5 col-sm-5 col-xs-5">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>

                                                <div class="col-md-3 col-sm-3 col-xs-3 padLR0">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-xs-3 col-sm-3 fltPrice">
                                            <div class="col-md-5 col-sm-8 col-xs-8 fr padT10">
                                                <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                <span class="animated-background db padB10 marginR20"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="fl width100 padTB10 animated-background"></div>
                                </div>

                            </div>

                            <div class="fl width100">
                                <div class="card fl width100 marginTB10">

                                    <div class="card-block fl width100 padT20 marginT10 padB10 padLR20">
                                        <div class="col-md-2 col-sm-2 col-xs-2 padT10">
                                            <span class="db padB10 marginR20 marginB10 col-md-8 animated-background"></span>
                                            <span class="db padT10 animated-background col-md-8 marginR20"></span>
                                        </div>
                                        <div class="col-md-7 col-sm-7 col-xs-7 padT10 padLR0 brdRight">
                                            <div class="fl width100">
                                                <div class="col-md-3 col-sm-3 col-xs-3">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>

                                                <div class="col-md-5 col-sm-5 col-xs-5">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>

                                                <div class="col-md-3 col-sm-3 col-xs-3 padLR0">
                                                    <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                    <span class="animated-background db padB10 marginR20"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-xs-3 col-sm-3 fltPrice">
                                            <div class="col-md-5 col-sm-8 col-xs-8 fr padT10">
                                                <span class="animated-background db padT10 marginB5 marginR20"></span>
                                                <span class="animated-background db padB10 marginR20"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="fl width100 padTB10 animated-background"></div>
                                </div>

                            </div>
                        </div>

					<div id="hotel_search_result" class="hotel-search-result-panel result_srch_htl">
					</div>
					<div class="hotel_map">
						<div class="spinner hide" id="spinnerload">
		  					<div class="bounce1"></div>
		  					<div class="bounce2"></div>
		  					<div class="bounce3"></div>
		  					<div class="bounce4"></div>
		  					<div class="bounce5"></div>
						</div>
	                    <div class="map_hotel hide" id="map"></div>
	                </div>
					<div id="npl_img" class="text-center" loaded="true" style="display: none;">
						<?='<img src="'.$GLOBALS['CI']->template->template_images('loader_v1.gif').'" alt="Please Wait"/>'?>
					</div>
					<div id="empty_hotel_search_result"  style="display:none">
							<div class="noresultfnd">
								<div class="imagenofnd"><img src="<?=$template_images?>empty.jpg" alt="Empty" /></div>
								<div class="lablfnd">No Result Found!!!</div>
							</div>
					</div>
					<hr class="hr-10">
				</div>
			</div>
			</div>
			</div>
		</div>
	<div id="empty-search-result" class="jumbotron container" style="display:none">
		<h1><i class="fas fa-bed"></i> Oops!</h1>
		<p>No hotels were found in this location today.</p>
		<p>
		Search results change daily based on availability.If you have an urgent requirement, please get in touch with our call center using the contact details mentioned on the home page. They will assist you to the best of their ability.
		</p>
	</div>
	</div>
</section>

<div class="modal fade bs-example-modal-lg" id="hotel-img-gal-box-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h5 class="modal-title" id="myModalLabel">Hotel Images</h5>
				<div class="htlimgprz">
					<strong id="modal-price-symbol"></strong>&nbsp;
					<span class="h-p" id="modal-price"></span>
					<a href="" class="confirmBTN b-btn bookallbtn splhotltoy" id="modal-submit">Book</a>
					<!-- <button class="confirmBTN b-btn bookallbtn splhotltoy" type="submit">Book</button> -->
				</div>
				<ul class="htmimgstr">					
				</ul>
				<div class="imghtltrpadv hide">
				  <img src="" id="trip_adv_img">
				</div>
			</div>
			<div class="modal-body">
				<div class="spinner" id="spinnerload">
  					<div class="bounce1"></div>
  					<div class="bounce2"></div>
  					<div class="bounce3"></div>
  					<div class="bounce4"></div>
  					<div class="bounce5"></div>
				</div>
				<div id="hotel-images" class="hotel-images">

				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade bs-example-modal-lg" id="map-box-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
	<div class="modal-dialog modal-lg" style="width: 600px;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabelMap">Hotel Location Map</h4>
			</div>
			<div class="modal-body">			
				<iframe src="" id="map-box-frame" name="map_box_frame" style="height: 500px;width: 850px;">
				</iframe>
			</div>
		</div>
	</div>
</div>

<script>
    $(document).ready(function () {
        // Add minus icon for collapse element which is open by default
        $(".collapse.in").each(function () {
            $(this).siblings("#accordion .panel-heading").find(".glyphicon").addClass("glyphicon-chevron-up").removeClass("glyphicon-chevron-down");
        });

        // Toggle plus minus icon on show hide of collapse element
        $("#accordion .collapse").on('show.bs.collapse', function () {
            $(this).parent().find(".glyphicon").removeClass("glyphicon-chevron-down").addClass("glyphicon-chevron-up");
        }).on('hide.bs.collapse', function () {
            $(this).parent().find(".glyphicon").removeClass("glyphicon-chevron-up").addClass("glyphicon-chevron-down");
        });
    });
</script>

<?php
echo $this->template->isolated_view('share/media/hotel_search');
?>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyANXPM-4Tdxq9kMnI8OpL-M6kGsFFWreIY" type="text/javascript"></script>
<script type="text/javascript">
	$(document).ready(function(){
		if ($(window).width() < 550) {
			//alert("hiiiii");
			$(document).on("click",".madgrid",function(){
				var result_key = $(this).data('key');
				var hotel_code = $(this).data('hotel-code');				
				//var result_key = $(".result-index").data('key');
				
				//var hotel_code = $(".result-index").data('hotel-code');
				
				var result_token = $("#mangrid_id_"+result_key+'_'+hotel_code).val();
				var booking_source =$("#booking_source_"+result_key+'_'+hotel_code).val(); 
				var operation_details = $(".operation").val();
				window.location = '<?php echo base_url().'index.php/hotel/hotel_details/'.($hotel_search_params['search_id'])?>'+'?ResultIndex='+result_token+'&booking_source='+booking_source+'&op='+operation_details+'';
			});			
		}	
		$(".close_fil_box").click(function(){
			$(".coleft").hide();
			$(".resultalls").removeClass("open");
      });
		window.setInterval(function(){
	 		swal({
	 			text:'Your session has expired,Please search again!!!!',
	 			type:'info'

	 		}).then(function(){
	 			window.location.href="<?php echo base_url();?>";
	 		});
 			//alert('Session Expired!!!');
 			//window.location.href="<?php //echo base_url();?>";
 		},900000);
	});
</script>
<style type="text/css">
	.modinew {  /*double code*/
	    background: #002042 none repeat scroll 0 0;
	}
	.placenameflt {  /*double code*/
    	color: #fff;
    }
    .contryname {  /*double code*/
    	color: #8ba4ab;
    }
    .boxlabl {  /*double code*/
    	color: #8ba4ab;
    }
    .datein {  /*double code*/
    	color: #fff;
    }
    .countlbl{
    	color: #fff;
    }
    body{  /*double code*/
    	margin-top: 0;
    }
    .coleft {  /*double code*/
		width: 20%;
	}
	.resultalls .colrit {  /*double code*/
	    width: 80%;
	}
	.h-name {
		font-size: 20px;
	}
	.prcstrtingt {
		color: #aaa;
	}
	#reset_filters {  /*double code*/
	    padding: 8px;
	    font-size: 10px;
	}
	.splmodify {  /*double code*/
    	background: linear-gradient(96deg,#002042,#0a8ec1);
    	/*padding-bottom: 10px;*/
    }
    /*.priceflights strong {
    	font-weight: bold;
    	font-size: 18px;
    }*/
    .id-padding-10{
    	padding: 10px;
    	box-shadow: 0 8px 5px 0 #ccc;
    }
    .id-hotel-search-img{
    	box-shadow: 2px 2px 5px #ccc;
    	border: 5px solid #fff;
    }
    .rowresult .madgrid:hover{
        border: 1px solid #473823;
        /*box-shadow: 0 0 10px -5px #473823;*/
    }
    #hotel_search_result .rowresult.r-r-i .imagehtldis img {
    	height: 110px;
    }
    .celhtl.width30 {
    	padding: 35px 0;
    }
    .imagehtldis {
    	height: 110px;
    }
    .innd {
	    padding: 0px 0 0 15px;
	    min-height: 0;
	}
	.mwifdiv {
		padding: 8px 0 0;
	}
	.madgrid.forhtlpopover {
		border: 1px solid #ccc;
	}
	.adreshotle {
		font-size: 12px;
	}
</style>