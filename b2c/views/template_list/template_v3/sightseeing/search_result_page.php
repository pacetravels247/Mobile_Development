<?php
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/sightseeing_search.js'), 'defer' => 'defer');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/pax_count.js'), 'defer' => 'defer');
Js_Loader::$js[] = array('src' => JAVASCRIPT_LIBRARY_DIR.'jquery.jsort.0.4.min.js', 'defer' => 'defer');

Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('jquery.nicescroll.js'), 'defer' => 'defer');
echo $this->template->isolated_view('share/js/lazy_loader');
foreach ($active_booking_source as $t_k => $t_v) {
	$active_source[] = $t_v['source_id'];
}
$active_source = json_encode($active_source);
?>
<script>
var load_products = function(loader, offset, filters){
	//alert(filters);
	offset = offset || 0;
	var url_filters = '';
	if ($.isEmptyObject(filters) == false) {
		url_filters = '&'+($.param({'filters':filters}));
	}
	_lazy_content = $.ajax({
		type: 'GET',
		url: app_base_url+'index.php/ajax/sightseeing_list/'+offset+'?booking_source=<?=$active_booking_source[0]['source_id']?>&search_id=<?=$sight_seen_search_params['search_id']?>&op=load'+url_filters,
		async: true,
		cache: true,
		dataType: 'json',
		success: function(res) {
			loader(res);
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
load_products(interval_load);
</script>

<style type="text/css">
	.starrtinghotl {
    display: block;
    max-width: 100%;
    position: absolute;
    right: 0; font-size: 13px;
    top: 8px;
    left: 0px;
    text-align: center;
}
.placenameflt{ padding: 14px 0px; }
.result_srch_htl .sidepricewrp .priceflights { margin:0; }
.celhtl.width30 {
    vertical-align: middle;
    padding: 35px 0; position: relative;
    overflow: hidden;
    display: block;
    background: #fff;
}
.imagehtldis { height: 184px; }
</style>

<span class="hide">
	<input type="hidden" id="pri_search_id" value='<?=$sight_seen_search_params['search_id']?>'>
	<input type="hidden" id="pri_active_source" value='<?=$active_source?>'>
	<input type="hidden" id="pri_app_pref_currency" value='<?=$this->currency->get_currency_symbol(get_application_currency_preference())?>'>
	
</span>
<?php
	$data['result'] = $sight_seen_search_params;
	$mini_loading_image = '<div class="text-center loader-image"><img src="'.$GLOBALS['CI']->template->template_images('loader_v3.gif').'" alt="Loading........"/></div>';
	$loading_image = '<div class="text-center loader-image"><img src="'.$GLOBALS['CI']->template->template_images('loader_v1.gif').'" alt="Loading........"/></div>';
	$template_images = $GLOBALS['CI']->template->template_images();
	function get_sorter_set()
	{
		return '<div class="filterforallnty" id="top-sort-list-wrapper">
	                        <div class="topmistyhtl" id="top-sort-list-1">
	                            <div class="col-xs-12 nopad">
	                                <div class="insidemyt">
										<ul class="sortul">
											
											<li class="sortli threonly"><a class="sorta name-l-2-h loader asc"><i class="fa fa-sort-alpha-asc"></i> <strong>Name</strong></a><a
												class="sorta name-h-2-l hide loader des"><i class="fa fa-sort-alpha-desc"></i> <strong>Name</strong></a></li>
											
											<li class="sortli threonly"><a class="sorta star-l-2-h loader asc"><i class="fa fa-star"></i> <strong>Rating</strong></a><a
												class="sorta star-h-2-l hide loader des"><i class="fa fa-star"></i> <strong>Rating</strong></a></li>
																					
											<li class="sortli threonly"><a class="sorta price-l-2-h loader asc"><i class="fa fa-tag"></i> <strong>Price</strong></a><a
												class="sorta price-h-2-l hide loader des"><i class="fa fa-tag"></i> <strong>Price</strong></a></li>
										</ul>
									</div>
	                            </div>
	                        </div>
	                    </div>';
	}
	echo $GLOBALS['CI']->template->isolated_view('sightseeing/search_panel_summary');
	?>
<section class="search-result tour_search_results sghtseen">
	<div class="container-fluid"  id="page-parent">
		<?php echo $GLOBALS['CI']->template->isolated_view('share/loader/sight_seen_pre_loader',$data);?>
		<div class="container">
		<div class="resultalls open">
			<div class="coleft">
				<div class="flteboxwrp">
					<div class="filtersho">
						<div class="avlhtls"><strong id="filter_records"></strong> <span class="hide"> of <strong id="total_records"><?php echo $mini_loading_image?></strong> </span> Activities found
						</div><span class="close_fil_box"><i class="fas fa-times"></i></span>
					</div>
					<div class="fltrboxin">
						<form autocomplete="off">
							<div class="celsrch refine">
								<div class="row_top_fltr">
									<a class="pull-right reset_filter" id="reset_filters" style="font-size: 12px;">RESET ALL</a>
								</div>
								<div class="rangebox">
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
									<button data-target="#hotelsearch-refine" data-toggle="collapse" class="collapsebtn refine-header" type="button">
									Search Activities
									</button>
									<div id="hotelsearch-refine" class="in">
										<div class="boxins">
											<div class="relinput">
												<input type="text" class="srchhtl form-control" placeholder="Activities Names" id="tour-name" />
												<input type="button" class="srchsmall" id="tour-search-btn" value="" />
												
											</div>
										</div>
									</div>
								</div>
								<div class="septor"></div>
<!-- 								<div class="rangebox">
	<div class="cate-list">
	<button data-target="#accordion" data-toggle="collapse" class="collapsebtn refine-header" type="button">
     Sightseeing Type
	</button>
	

		
		<div id="accordion" class="panel-group category-list">
				<div class="panel panel-default">
				
				<a class="btn cate-btn-click" data-toggle="collapse" data-parent="#accordion" href="#demo">Sightseeing Type</a>
				
				<div id="demo" class="panel-collapse collapse in">
				
				</div>
				</div>

		</div>
		</div>
	
</div> -->
								<div class="rangebox">
									<button data-target="#sight_seen_type" data-toggle="collapse" class="collapsebtn" type="button">
									Activities Type
									</button>
									<div id="sight_seen_type" class="in">
										<div class="boxins sight_seen_types">
											<ul class="" id="hotel-amenitie-wrapper">
											   <li>
											      <div class="squaredThree">
											      <input type="checkbox" id="wifi-hotels-view" value="filter" class="wifi-hotels-view" name="amenitie[]">
											      <label for="wifi-hotels-view"></label>
											      </div>
											      <label class="lbllbl" for="wifi-hotels-view">Activities Types</label></li>
											
											</ul>
										</div>
									</div>
								</div>
								<div class="rangebox hide">
									<button data-target="#collapse2" data-toggle="collapse" class="collapsebtn" type="button">
									Star Rating
									</button>
									<div id="collapse2" class="in">
										<div class="boxins marret" id="starCountWrapper">
											<a class="starone toglefil star-wrapper">
												<input class="hidecheck star-filter" type="checkbox" value="1">
												<div class="starin">
													1 <span class="starfa fa fa-star"></span>
													<span class="htlcount">-</span>
												</div>
											</a>
											<a class="starone toglefil star-wrapper">
												<input class="hidecheck star-filter" type="checkbox" value="2">
												<div class="starin">
													2 <span class="starfa fa fa-star"></span>
													<span class="htlcount">-</span>
												</div>
											</a>
											<a class="starone toglefil star-wrapper">
												<input class="hidecheck star-filter" type="checkbox" value="3">
												<div class="starin">
													3 <span class="starfa fa fa-star"></span>
													<span class="htlcount">-</span>
												</div>
											</a>
											<a class="starone toglefil star-wrapper">
												<input class="hidecheck star-filter" type="checkbox" value="4">
												<div class="starin">
													4 <span class="starfa fa fa-star"></span>
													<span class="htlcount">-</span>
												</div>
											</a>
											<a class="starone toglefil star-wrapper">
												<input class="hidecheck star-filter" type="checkbox" value="5">
												<div class="starin">
													5 <span class="starfa fa fa-star"></span>
													<span class="htlcount">-</span>
												</div>
											</a>
										</div>
									</div>
								</div>
								
							

								<div class="septor"></div>
								<div class="rangebox">
									<button data-target="#collapse22" data-toggle="collapse" class="collapsebtn" type="button">
									Promotion & discount Offers
									</button>
									<div id="collapse22" class="in">
										<div class="boxins">
											<ul class="locationul" id="discount-filter-wrapper">
											</ul>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			
				<!-- Prev|Next Searcrh Button Ends -->
				<div class="insideactivity">
					<div class="resultall">
						<div class="filter_tab fa fa-filter"></div>
						<div class="vluendsort">
							<div class="insidemyt col-xs-12 nopad">
								<!-- <div class="col-xs-3 nopad">
									<div class="nityvalue">
										<label type="button" class="vlulike active filter-hotels-view" for="all-hotels-view">
										<input type="radio" id="all-hotels-view" value="all" class="hide deal-status-filter" name="deal_status[]" checked="checked">
										All Activity
										</label>
									
									
									</div>
								</div> -->
								<div class="col-xs-10 mfulwdth nopad">
									<div class="filterforallnty" id="top-sort-list-wrapper">
										<div class="topmistyhtl" id="top-sort-list-1">
											<div class="col-xs-12 nopad">
												<div class="insidemyt">
													<ul class="sortul">
														<li class="sortli threonly" data-sort="hn">
															<a class="sorta name-l-2-h asc" data-order="asc"><i class="fa fa-sort-alpha-asc"></i> <strong>Name</strong></a>
															<a class="sorta name-h-2-l hide des" data-order="desc"><i class="fa fa-sort-alpha-desc"></i> <strong>Name</strong></a>
														</li>
														<li class="sortli threonly" data-sort="sr">
															<a class="sorta star-l-2-h asc" data-order="asc"><i class="fa fa-star"></i> <strong>Rating</strong></a>
															<a class="sorta star-h-2-l hide  des" data-order="desc"><i class="fa fa-star"></i> <strong>Rating</strong></a>
														</li>
														<li class="sortli threonly" data-sort="p">
															<a class="sorta price-l-2-h asc" data-order="asc"><i class="fa fa-tag"></i> <strong>Price</strong></a>
															<a class="sorta price-h-2-l hide  des" data-order="desc"><i class="fa fa-tag"></i> <strong>Price</strong></a>
														</li>
													<!-- 	<li class="sortli threonly" data-sort="p">
														<a class="sorta"><i class="fa fa-tag"></i><strong>Price</strong></a>
																<select class="col-xs-3 form-control seldiv" id="price-filter" name="price">
																	<option value="TOP_SELLERS" selected="selected">Top Seller</option>
																	<option value="PRICE_FROM_A">Price (low->high)</option>
																	<option value="PRICE_FROM_D">Price (high->low)</option>
																	
																</select>
														</li> -->
													</ul>
												</div>
											</div>
										</div>
									</div>
								</div>						
                            <div class="col-xs-2 mobile_none nopad">
                                <div class="mapviw noviews">
                                    <div class="mapviwhtl nopad noviews reswd">
                                           <div class="rit_view">
                                                   <a class="view_type grid_click active"><span class="fa fa-th"></span></a> 
                                           </div>
                                         </div>
                                    <div class="mapviwlist nopad noviews reswd">
                                        <div class="rit_view">
                                            <a class="view_type list_click" id="list_clickid"><span class="fa fa-list"></span></a> 
                                        </div>
                                    </div>
                                  
                                </div>
                            </div>
						</div>
						</div>

					
						<div class="allresult">
							<?php echo $loading_image;?>
						
							<div id="tour_search_result" class="hotel-search-result-panel result_srch_htl ">
							</div>
							<!-- <div class="hotel_map">
									                        <div class="map_hotel" id="location_map"></div>
									                    </div> -->

							<div id="npl_img" class="text-center hide" loaded="true">
								<?='<img src="'.$GLOBALS['CI']->template->template_images('loader_v1.gif').'" alt="Please Wait"/>'?>
							</div>
							<div id="empty_tour_search_result"  style="display:none">
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
		</div>
	</div>
	
	<div id="empty-search-result" class="jumbotron container" style="display:none">
		<h1><i class="fal fa-bed"></i> Oops!</h1>
		<p>No Sightseeing places were found in this location today.</p>
		<p>
			Search results change daily based on availability.If you have an urgent requirement, please get in touch with our call center using the contact details mentioned on the home page. They will assist you to the best of their ability.
		</p>
	</div>
</section>
 
<div class="modal fade bs-example-modal-lg" id="map-box-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Hotel Location Map</h4>
			</div>
			<div class="modal-body">
				<iframe src="" id="map-box-frame" name="map_box_frame" style="height: 500px;width: 850px;">
				</iframe>
			</div>
		</div>
	</div>
</div>
</div>
<input type="hidden" name="" id="selected_cate" value="">
<input type="hidden" name="" id="selected_sub_cate" value=""> 
<!-- <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyANXPM-4Tdxq9kMnI8OpL-M6kGsFFWreIY&callback=initialize" type="text/javascript"></script> -->
<?php
//echo $this->template->isolated_view('share/media/hotel_search');
?>
<script type="text/javascript">
	$(function(){
			$("#collapse3 .collapse ul li button.cate-btn-click").on("click",function(){
				//alert("hiiii");
			});
			 $(document).on("click", ".madgrid", function () {
               	
               	var search_id = "<?php echo $sight_seen_search_params['search_id']?>";
               	var booking_source = "<?=$active_booking_source[0]['source_id']?>";
               	var product_code =$(this).data('product-code');
               	var result_token = $(this).data('result-token');
                window.location ='<?php echo base_url()?>index.php/sightseeing/sightseeing_details?op=get_details&search_id='+search_id+'&booking_source='+booking_source+'&product_code='+product_code+'&result_token='+result_token;

              	//alert(url_text);


            });
	});
    $(document).ready(function () {	
        $(".close_fil_box").click(function () {
            $(".coleft").hide();
            $(".resultalls").removeClass("open");
        });
    });

</script>
