<?php
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/hotel_search.js'), 'defer' => 'defer');
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
</span>
<?php
$data['result'] = $hotel_search_params;
$mini_loading_image = '<div class="text-center loader-image"><img src="'.$GLOBALS['CI']->template->template_images('loader_v3.gif').'" alt="Loading........"/></div>';
$loading_image = '<div class="text-center loader-image"><img src="'.$GLOBALS['CI']->template->template_images('loader_v1.gif').'" alt="Loading........"/></div>';
$template_images = $GLOBALS['CI']->template->template_images();
echo $GLOBALS['CI']->template->isolated_view('hotel/search_panel_summary');
		
Js_Loader::$css[] = array('href' => $GLOBALS['CI']->template->template_css_dir('owl.carousel.min.css'), 'media' => 'screen');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('owl.carousel.min.js'), 'defer' => 'defer');

?>
<section class="search-result hotel_search_results">
	<div class="container"  id="page-parent">
	<?php echo $GLOBALS['CI']->template->isolated_view('share/loader/hotel_result_pre_loader',$data);?>
		<div class="resultalls open">

		   
			<div class="coleft">
				<div class="flteboxwrp">
				     <div class="mapviw noviews">
                        <div class="mapviwlist nopad noviews reswd">
	                        <div class="rit_view">
	                       	 <a class="view_type list_click active"><span class="fa fa-list"></span>&nbsp;List</a> 
	                        </div>
                        </div>
                        <div class="mapviwhtl nopad noviews reswd">
	                        <div class="rit_view">
	                        	<a class="view_type map_click"><span class="fa fa-map-marker"></span>&nbsp;<span class="maphd">Show</span> Map</a> 
	                        </div>
	                    </div>
            		</div>
					<div class="filtersho">
						<div class="avlhtls"><strong id="filter_records"></strong> <span class="hide"> of <strong id="total_records"><?php echo $mini_loading_image?></strong> </span> Hotels found
						</div>
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
												<span class="starfa fa fa-star"></span>
												<span class="htlcount">-</span>
											</div>
										</a>
										<a class="starone toglefil star-wrapper">
											<input class="hidecheck star-filter" type="checkbox" value="2">
											<div class="starin">
												 <span class="rststrne">2</span>
												 <span class="starfa fa fa-star"></span>
												<span class="htlcount">-</span>
											</div>
										</a>
										<a class="starone toglefil star-wrapper">
											<input class="hidecheck star-filter" type="checkbox" value="3">
											<div class="starin">
												<span class="rststrne">3</span>
												<span class="starfa fa fa-star"></span>
												<span class="htlcount">-</span>
											</div>
										</a>
										<a class="starone toglefil star-wrapper">
											<input class="hidecheck star-filter" type="checkbox" value="4">
											<div class="starin">
												<span class="rststrne">4</span>
											    <span class="starfa fa fa-star"></span>
												<span class="htlcount">-</span>
											</div>
										</a>
										<a class="starone toglefil star-wrapper">
											<input class="hidecheck star-filter" type="checkbox" value="5">
											<div class="starin">
												<span class="rststrne">5</span>
											    <span class="starfa fa fa-star"></span>
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
				<div class="filter_tab fa fa-filter"></div>
				<div class="vluendsort">
					<div class="col-xs-5 nopad">
					<div class="nityvalue">
						<!-- <label type="button" class="vlulike active filter-hotels-view" for="all-hotels-view">
							<input type="radio" id="all-hotels-view" value="all" class="hide deal-status-filter" name="deal_status[]" checked="checked">
							All Hotels
						</label> -->
						<label type="button" class="vlulike filter-hotels-view" for="deal-hotels-view">
							<input type="radio" id="deal-hotels-view" value="filter" class="hide deal-status-filter" name="deal_status[]">
							Deal
						</label>
					</div>
					</div>
					<div class="col-xs-7 nopad">
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
												<a class="sorta star-l-2-h asc" data-order="asc"><i class="fa fa-star"></i> <strong>Star</strong></a>
												<a class="sorta star-h-2-l hide  des" data-order="desc"><i class="fa fa-star"></i> <strong>Star</strong></a>
											</li>
											<li class="sortli threonly" data-sort="p">
												<a class="sorta price-l-2-h asc" data-order="asc"><i class="fa fa-tag"></i> <strong>Price</strong></a>
												<a class="sorta price-h-2-l hide  des" data-order="desc"><i class="fa fa-tag"></i> <strong>Price</strong></a>
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				</div>		
				<div class="allresult">
					<?php echo $loading_image;?>
					<div id="hotel_search_result" class="hotel-search-result-panel result_srch_htl">
					</div>
					<div class="hotel_map">
	                    <div class="map_hotel" id="map"></div>
	                </div>
					<div id="npl_img" class="text-center" loaded="true">
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
	</div>
	<div id="empty-search-result" class="jumbotron container" style="display:none">
		<h1><i class="fa fa-bed"></i> Oops!</h1>
		<p>No hotels were found in this location today.</p>
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
				<h4 class="modal-title" id="myModalLabel">Hotel Images</h4>
			</div>
			<div class="modal-body">
				<div id="sync1" class="owl-carousel owl-theme">
				  <div class="item">
				   <div class="htlsldig">
				   		<img src="<?=$GLOBALS['CI']->template->template_images('hotel1.jpg')?>" alt="Hotel Image"/>
				   </div>
				   </div>
				  <div class="item">
				    <div class="htlsldig">
				   		<img src="<?=$GLOBALS['CI']->template->template_images('hotel2.jpg')?>" alt="Hotel Image"/>
				    </div>
				  </div>
				  <div class="item">
				   <div class="htlsldig">
				   		<img src="<?=$GLOBALS['CI']->template->template_images('hotel3.jpg')?>" alt="Hotel Image"/>
				    </div>
				    </div>
				  <div class="item">
				    <div class="htlsldig">
				   		<img src="<?=$GLOBALS['CI']->template->template_images('hotel4.jpg')?>" alt="Hotel Image"/>
				    </div>
				    </div>
				  <div class="item">
				    <div class="htlsldig">
				   		<img src="<?=$GLOBALS['CI']->template->template_images('hotel5.jpg')?>" alt="Hotel Image"/>
				    </div>
				    </div>
				  <div class="item">
				    <div class="htlsldig">
				   		<img src="<?=$GLOBALS['CI']->template->template_images('hotel6.jpg')?>" alt="Hotel Image"/>
				    </div>
				    </div>
				  <div class="item">
				   <div class="htlsldig">
				   		<img src="<?=$GLOBALS['CI']->template->template_images('hotel7.jpg')?>" alt="Hotel Image"/>
				    </div>
				    </div>
				  <div class="item">
				    <div class="htlsldig">
				   		<img src="<?=$GLOBALS['CI']->template->template_images('hotel8.jpg')?>" alt="Hotel Image"/>
				    </div>
				    </div>
				</div>

				<div id="sync2" class="owl-carousel owl-theme btmimg">
				  <div class="item">
				    <div class="htlsldigsml">
				   		<img src="<?=$GLOBALS['CI']->template->template_images('hotel1.jpg')?>" alt="Hotel Image"/>
				    </div>
				    </div>
				  <div class="item">
				   <div class="htlsldigsml">
				   		<img src="<?=$GLOBALS['CI']->template->template_images('hotel2.jpg')?>" alt="Hotel Image"/>
				    </div>
				    </div>
				  <div class="item">
				    <div class="htlsldigsml">
				   		<img src="<?=$GLOBALS['CI']->template->template_images('hotel3.jpg')?>" alt="Hotel Image"/>
				    </div>
				    </div>
				  <div class="item">
				    <div class="htlsldigsml">
				   		<img src="<?=$GLOBALS['CI']->template->template_images('hotel4.jpg')?>" alt="Hotel Image"/>
				    </div>
				    </div>
				  <div class="item">
				    <div class="htlsldigsml">
				   		<img src="<?=$GLOBALS['CI']->template->template_images('hotel5.jpg')?>" alt="Hotel Image"/>
				    </div>
				    </div>
				  <div class="item">
				   <div class="htlsldigsml">
				   		<img src="<?=$GLOBALS['CI']->template->template_images('hotel6.jpg')?>" alt="Hotel Image"/>
				    </div>
				    </div>
				  <div class="item">
				   <div class="htlsldigsml">
				   		<img src="<?=$GLOBALS['CI']->template->template_images('hotel7.jpg')?>" alt="Hotel Image"/>
				    </div>
				    </div>
				  <div class="item">
				   <div class="htlsldigsml">
				   		<img src="<?=$GLOBALS['CI']->template->template_images('hotel8.jpg')?>" alt="Hotel Image"/>
				    </div>
				    </div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
    $(document).ready(function(){
        // Add minus icon for collapse element which is open by default
        $(".collapse.in").each(function(){
        	$(this).siblings(".panel-heading").find(".glyphicon").addClass("glyphicon-chevron-up").removeClass("glyphicon-chevron-down");
        });
        
        // Toggle plus minus icon on show hide of collapse element
        $(".collapse").on('show.bs.collapse', function(){
        	$(this).parent().find(".glyphicon").removeClass("glyphicon-chevron-down").addClass("glyphicon-chevron-up");
        }).on('hide.bs.collapse', function(){
        	$(this).parent().find(".glyphicon").removeClass("glyphicon-chevron-up").addClass("glyphicon-chevron-down");
        });
    });
</script>

<?php
echo $this->template->isolated_view('share/media/hotel_search');
?>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyANXPM-4Tdxq9kMnI8OpL-M6kGsFFWreIY" type="text/javascript"></script>

 <script type="text/javascript">
  
  $(document).ready(function() {
	  var sync1 = $("#sync1");
      var sync2 = $("#sync2");

      sync1.owlCarousel({
        singleItem : true,
        slideSpeed : 1000,
        navigation: true,
        pagination:false,
        afterAction : syncPosition,
        responsiveRefreshRate : 200,
      });

      sync2.owlCarousel({
        items : 5,
        itemsDesktop      : [1199,4],
        itemsDesktopSmall     : [979,4],
        itemsTablet       : [768,4],
        itemsMobile       : [479,2],
        pagination:false,
        responsiveRefreshRate : 100,
        afterInit : function(el){
          el.find(".owl-item").eq(0).addClass("synced");
        }
      });

      function syncPosition(el){
        var current = this.currentItem;
        $("#sync2")
          .find(".owl-item")
          .removeClass("synced")
          .eq(current)
          .addClass("synced")
        if($("#sync2").data("owlCarousel") !== undefined){
          center(current)
        }

      }

      $("#sync2").on("click", ".owl-item", function(e){
        e.preventDefault();
        var number = $(this).data("owlItem");
        sync1.trigger("owl.goTo",number);
      });

      function center(number){
        var sync2visible = sync2.data("owlCarousel").owl.visibleItems;

        var num = number;
        var found = false;
        for(var i in sync2visible){
          if(num === sync2visible[i]){
            var found = true;
          }
        }

        if(found===false){
          if(num>sync2visible[sync2visible.length-1]){
            sync2.trigger("owl.goTo", num - sync2visible.length+2)
          }else{
            if(num - 1 === -1){
              num = 0;
            }
            sync2.trigger("owl.goTo", num);
          }
        } else if(num === sync2visible[sync2visible.length-1]){
          sync2.trigger("owl.goTo", sync2visible[1])
        } else if(num === sync2visible[0]){
          sync2.trigger("owl.goTo", num-1)
        }
      }
  });
</script>