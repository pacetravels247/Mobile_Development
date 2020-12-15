<?php
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/hb_hotel_search.js'), 'defer' => 'defer');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/hb_pax_count.js'), 'defer' => 'defer');
Js_Loader::$js[] = array('src' => JAVASCRIPT_LIBRARY_DIR.'jquery.jsort.0.4.min.js', 'defer' => 'defer');
Js_Loader::$css[] = array('href' => $GLOBALS['CI']->template->template_css_dir('page_resource/hb_hotel_result.css'), 'media' => 'screen');

Js_Loader::$js[] = array('src' => JAVASCRIPT_LIBRARY_DIR.'jquery.nicescroll.js', 'defer' => 'defer');

echo $this->template->isolated_view('share/js/lazy_loader');
foreach ($active_booking_source as $t_k => $t_v) {
	$active_source[] = $t_v['source_id'];
}
$active_source = json_encode($active_source);
?>
<input type="hidden" id="api_request_count" value="<?=count($active_booking_source)?>">
<script>
	var request_count = $('#api_request_count').val();
	var load_hotels = function(loader, bookingSource, offset, filters){
		offset = offset || 0;
		var url_filters = '';
		if ($.isEmptyObject(filters) == false) {
			url_filters = '&'+($.param({'filters':filters}));
		}
		alert(app_base_url+'index.php/ajax/hotel_list/'+offset+'?booking_source='+bookingSource+'&search_id=<?=$hotel_search_params['search_id']?>&op=load'+url_filters);
		_lazy_content = $.ajax({
			type: 'GET',
			url: app_base_url+'index.php/ajax/hotel_list/'+offset+'?booking_source='+bookingSource+'&search_id=<?=$hotel_search_params['search_id']?>&op=load'+url_filters,
			async: true,
			cache: true,
			dataType: 'json',
			success: function(res) {
				// loader(res);
				if(res.status == 0) {
					request_count = request_count - 1;
					if(request_count <= 0) {
						loader(res);
					}
				} else {
					loader(res);
				}
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
	//load_hotels(interval_load);
</script>
<?php 
if(isset($active_booking_source) && valid_array($active_booking_source)) {
	foreach($active_booking_source as $act_k => $boking_src) {
//		if($boking_src['source_id'] != HB_HOTEL_BOOKING_SOURCE) {
			?><script>
				load_hotels(interval_load , '<?=@$boking_src['source_id']?>');
			</script><?php
//		}
	}
}
?>
<span class="hide">
<input type="hidden" id="pri_search_id" value='<?=$hotel_search_params['search_id']?>'>
<input type="hidden" id="pri_active_source" value='<?=$active_source?>'>
<input type="hidden" id="pri_app_pref_currency" value='<?=$this->currency->get_currency_symbol(get_application_display_currency_preference())?>'>
</span>
<?php
	$data['result'] = $hotel_search_params;
	$mini_loading_image = '<div class="text-center loader-image"><img src="'.$GLOBALS['CI']->template->template_images('loader_v3.gif').'" alt="Loading........"/></div>';
	$loading_image = '<div class="text-center loader-image"><img src="'.$GLOBALS['CI']->template->template_images('loader_v1.gif').'" alt="Loading........"/></div>';
	$template_images = $GLOBALS['CI']->template->template_images();
	//echo $GLOBALS['CI']->template->isolated_view('hotel/search_panel_summary');
?>
<section class="search-result hotel_search_results">
	<div  id="page-parent">
		<?php echo $GLOBALS['CI']->template->isolated_view('share/loader/hotel_result_pre_loader',$data);?>
		<div class="allpagewrp top80">
			<?php echo $GLOBALS['CI']->template->isolated_view('hotel/search_panel_summary');?>
			<div class="clearfix"></div>
			<div class="contentsec">
				<div class="container">
					<div class="filtrsrch">
						<div class="col30">
							<div class="flteboxwrp">
								<div class="filtersho">
									<div class="avlhtls"><strong id="filter_records"></strong> <span class="hide"> of <strong id="total_records"><?php echo $mini_loading_image?></strong> </span> Hotels found</div>
								</div>
								<div class="fltrboxin">
									<div class="celsrch">
		                            	<button class="close_filter"><span class="fa fa-close"></span></button>
										<div class="norfilterr">
											<div class="row">
												<a id="reset_filters" class="placenamefil pull-right"><span class="fa fa-repeat"></span> Reset</a>
											</div>
											<div class="outbnd">
												<div class="rangebox">
													<div class="ranghead">Hotel Name</div>
													<div id="" class="stoprow">
														<div class="boxins hotel_search_box">
															<input type="text" id="hotel-name" class="filter_input" placeholder="Type hotel name" />
															<input type="submit" value="" id="hotel-name-search-btn" class="srchsmall">
														</div>
													</div>
												</div>
												<div class="rangebox">
													<div class="ranghead">Price</div>
													<div id="price-refine" class="in price_slider1">
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
												<div class="rangebox">
													<div class="ranghead">Star Rating</div>
													<div id="starCountWrapper" class="stoprow">
														<div class="boxins marret padlow hotel_star_filter">
															<div class="relatboxsone">
															<?php
															$i = 0;
															for ($i=1; $i <= 5; $i++) {
															?>
																<a class="timone toglefil">
																	<label class="sprte star_image star_<?=$i?>"></label>
																</a>
															<?php
															}
															?>
															</div>
															<div class="clearfix"></div>
															<div class="relatboxs">
															<?php
															$i = 0;
															for ($i=1; $i <= 5; $i++) {
															?>
																
																<a class="timone toglefil star-wrapper ">
																<input type="checkbox" value="<?=$i?>" class="star-filter hide" id="star_<?=$i?>" />
																	<label class="rounds" for="star_<?=$i?>">
																	</label>
																</a>
															<?php
															}
															?>
															</div>
															<div class="clearfix"></div>
															<div class="relatboxsone" id="categorystar">
															<?php
															$i = 0;
															for ($i=1; $i <= 5; $i++) {
															?>
																<label class="timone toglefil">
																	<span class="starin"><span class="htlcount"><?=$i?> star</span></span>
																</label>
															<?php
															}
															?>
															</div>
														</div>
													</div>
												</div>
												<div class="rangebox">
													<div class="ranghead">Accomodation Type</div>
													<div id="airlines" class="stoprow">
														<div class="boxins">
															<ul class="locationul" id="hotel-acc-wrapper">
															</ul>
														</div>
													</div>
												</div>
												<div class="rangebox hide">
													<div class="ranghead">Facilities</div>
													<div id="airlines" class="stoprow">
														<div class="boxins">
															<ul class="locationul" id="hotel-facility-wrapper">
															</ul>
														</div>
													</div>
												</div>
												<div class="rangebox">
													<div class="ranghead">Location</div>
													<div id="airlines" class="stoprow">
														<div class="boxins">
															<ul class="locationul" id="hotel-location-wrapper">
																<!--<li><div class="squaredThree"><input type="checkbox" value="tigerair" name="" class="" id="squaredThree1"><label for="squaredThree1"></label></div><label for="squaredThree1" class="lbllbl">Bangalore</label></li>
																	-->
															</ul>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col70">
							<div class="in70">
								<div class="topmisty hote_reslts">
									<div class="col-xs-12 nopad">
										<button class="filter_show"><span class="fa fa-filter"></span></button>
										<div class="insidemyt">
											<div class="col-xs-8 nopad fullshort">
												<ul class="sortul">
													<li class="sortli" data-sort="hn">
														<a class="sorta asc name-l-2-h" data-order="asc"><span class="sirticon fa fa-sort-amount-asc"></span> Hotel Name</a>
														<a class="sorta des name-h-2-l hide" data-order="desc"><span class="sirticon fa fa-sort-amount-asc"></span> Hotel Name</a>
													</li>
													<li class="sortli" data-sort="sr">
														<a class="sorta asc star-l-2-h" data-order="asc"><span class="sirticon fa fa-star-o"></span> Star Rating</a>
														<a class="sorta des star-h-2-l hide" data-order="desc"><span class="sirticon fa fa-star-o"></span> Star Rating</a>
													</li>
													<li class="sortli" data-sort="p">
														<a class="sorta nobord asc price-l-2-h" data-order="asc"><span class="sirticon fa fa-tag"></span> Price</a>
														<a class="sorta nobord des price-h-2-l hide" data-order="desc"><span class="sirticon fa fa-tag"></span> Price</a>
													</li>
												</ul>
											</div>
											<div class="col-xs-4 nopad noviews">
												<div class="rit_view"> <a class="view_type list_click active"><span class="fa fa-list"></span>List</a> <a class="view_type map_click"><span class="fa fa-map"></span>Map</a> </div>
											</div>
										</div>
									</div>
								</div>
								<!--All Available flight result comes here -->
								<div class="allresult">
									<div class="hotel_map">
										<div class="map_hotel" id="map"></div>
									</div>
									<div class="hotels_results" id="hotel_search_result">
									</div>
									<div id="npl_img" class="text-center normal_load" loaded="true">
										<?php /*echo'<img src="'.$GLOBALS['CI']->template->template_images('loader_v1.gif').'" alt="Please Wait"/>' */?>
									</div>
									<div id="empty_hotel_search_result"  style="display:none">
										<div class="noresultfnd text-center">
											<div class="imagenofnd"><img src="<?=$template_images?>empty.jpg" alt="Empty" /></div>
											<div class="lablfnd">No Result Found!!!</div>
										</div>
									</div>
								</div>
								<!-- End of result --> 
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="empty-search-result" class="jumbotron container" style="display:none">
		<h1><i class="fa fa-bed"></i> Oops!</h1>
		<p>No hotels were found in this location today.</p>
		<p>
			Search results change daily based on availability.If you have an urgent requirement, please get in touch with our call center using the contact details mentioned on the home page. They will assist you to the best of their ability.
		</p>
	</div>
	<!--Map view indipendent hotel-->
	<div id="map_view_hotel" class="modal fade" data-role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Ramee Guestline Hotel</h4>
				</div>
				<div class="clearfix"></div>
				<div class="modal-body">
					<div class="map_hotel_pop" id="map1"></div>
				</div>
				<div class="clearfix"></div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
</section>
<script src="https://maps.googleapis.com/maps/api/js" defer></script>
<?php
echo $this->template->isolated_view('share/media/hotel_search');
?>
<script>
//filter toggle	
	
	
	$('.filter_show').click(function(){
		$('.filtrsrch').addClass('open');
	});
	
	$('.close_filter').click(function(){
		$('.filtrsrch').removeClass('open');
	});
</script>