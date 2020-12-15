<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

<?php
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/hotel_search.js'), 'defer' => 'defer');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/pax_count.js'), 'defer' => 'defer');
Js_Loader::$js[] = array('src' => JAVASCRIPT_LIBRARY_DIR . 'jquery.jsort.0.4.min.js', 'defer' => 'defer');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('jquery.nicescroll.js'), 'defer' => 'defer');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/hotel_search_opt.js'), 'defer' => 'defer');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/marker_cluster.js'), 'defer' => 'defer');

Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/sweet_alert.min.js'), 'defer' => 'defer');

echo $this->template->isolated_view('share/js/lazy_loader');
foreach ($active_booking_source as $t_k => $t_v) {
    $active_source[] = $t_v['source_id'];
}
$active_source = json_encode($active_source);
?>
<script>
    var load_hotels = function (loader, offset, filters) {
        offset = offset || 0;
        var url_filters = '';
        if ($.isEmptyObject(filters) == false) {
            url_filters = '&' + ($.param({'filters': filters}));
        }
        _lazy_content = $.ajax({
            type: 'GET',
            url: app_base_url + 'index.php/ajax/hotel_list/' + offset + '?booking_source=<?= $active_booking_source[0]['source_id'] ?>&search_id=<?= $hotel_search_params['search_id'] ?>&op=load' + url_filters,
            async: true,
            cache: true,
            dataType: 'json',
            success: function (res) {
                loader(res);
                $('#onwFltContainer').hide();
            }
        });
    }

    var interval_load = function (res) {
        var dui;
        var r = res;
        dui = setInterval(function () {
            if (typeof (process_result_update) != "undefined" && $.isFunction(process_result_update) == true) {
                clearInterval(dui);
                process_result_update(r);
                ini_result_update(r);
            }
        }, 1);
    };
    load_hotels(interval_load);
</script>
<span class="hide">
    <input type="hidden" id="pri_search_id" value='<?= $hotel_search_params['search_id'] ?>'>
    <input type="hidden" id="pri_active_source" value='<?= $active_source ?>'>
    <input type="hidden" id="pri_app_pref_currency" value='<?= $this->currency->get_currency_symbol(get_application_currency_preference()) ?>'>
    <input type="hidden" id="api_base_url" value="<?= $GLOBALS['CI']->template->template_images() ?>">
    <input type="hidden" id="api_booking_source" value="<?= $active_booking_source[0]['source_id'] ?>">
    <input type="hidden" id="default_loader" value="<?= $GLOBALS['CI']->template->template_images('image_loader.gif') ?>">
</span>
<?php
$data['result'] = $hotel_search_params;
$mini_loading_image = '<div class="text-center loader-image"><img src="' . $GLOBALS['CI']->template->template_images('loader_v3.gif') . '" alt="Loading........"/></div>';
$loading_image = '<div class="text-center loader-image" style="display:none;"><img src="' . $GLOBALS['CI']->template->template_images('loader_v1.gif') . '" alt="Loading........"/></div>';
$template_images = $GLOBALS['CI']->template->template_images();
echo $GLOBALS['CI']->template->isolated_view('hotel/search_panel_summary');

Js_Loader::$css[] = array('href' => $GLOBALS['CI']->template->template_css_dir('owl.carousel.min.css'), 'media' => 'screen');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('owl.carousel.min.js'), 'defer' => 'defer');
?>
<input type="hidden" id="pagination_loader" value="<?= $GLOBALS['CI']->template->template_images('loader_v1.gif') ?>">
<div class="container-fluid bg-white shadow-1 mb-1">
    <div class="col-md-offset-1 col-md-10">
        <div class="row id-filter-div">
            <div class="col-sm-6 p-0">
                <div class="row">
                    <div class="col-sm-3 padfive">
                        <!-- <select class="form-control">
                            <option>Type</option>
                        </select> -->
                        <small>Property Type</small>
                        <select class="form-control id-select-content selectpicker" multiple data-live-search="true">
                            <option selected>All Hotells</option>
                            <option>Business Hotels</option>
                            <option>Resort</option>
                            <option>Home Stay</option>
                            <option>Airport Hotel</option>
                        </select>
                    </div>
                    <div class="col-sm-3 padfive">
                        <small>Price</small>
                        <p class="p-price-range" div-status="0">Price</p>

                        <div id="price-refine" class="in id-price-range-div">
                            <?php echo $mini_loading_image ?>
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
                    <div class="col-sm-3 padfive">
                        <!-- <select class="form-control">
                            <option>Stars</option>
                        </select> -->
                        <small>Star Ratings</small>
                        <select class="form-control id-select-content selectpicker" id="starCountWrapper" multiple data-live-search="true">
                            <option selected>All Stars</option>
                            <option value="1" class="star-filter">1 Star</option>
                            <option value="2" class="star-filter">2 Star</option>
                            <option value="3" class="star-filter">3 Star</option>
                            <option value="4" class="star-filter">4 Star</option>
                            <option value="5" class="star-filter">5 Star</option>
                        </select>
                    </div>
                    <div class="col-sm-3 padfive">
                        <!-- <select class="form-control">
                            <option>Amenities</option>
                        </select> -->
                        <small>Board Basis</small>
                        <select class="form-control id-select-content selectpicker" multiple data-live-search="true">
                            <option selected>All</option>
                            <option>Room Only</option>
                            <option>Breakfast</option>
                            <option>Half Board</option>
                            <option>Full Board</option>
                            <option>All Inclusive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 p-0">
                <div class="row">
                    <div class="col-sm-3 padfive">
                        <!-- <select class="form-control">
                            <option>Locations</option>
                        </select> -->
                        <small>Locations</small>
                        <select class="form-control id-select-content selectpicker" multiple data-live-search="true">
                            <option selected>All locations</option>
                            <option>Electronic City</option>
                            <option>Gandhi Nagar</option>
                            <option>Indranagar</option>
                            <option>Jayanagar</option>
                            <option>M.G Road</option>
                        </select>
                    </div>
                    <div class="col-sm-6 padfive">
                        <small>Search your Hotels</small>
                        <input type="text"  name="" class="form-control id-search-input" placeholder="Search Hotel Names">
                        <i class="fa fa-search id-fa-search"></i>
                    </div>
                    <div class="col-sm-3 padfive">
                        <small>Clear Filter</small>
                        <button class="btn btn-default form-control">Clear</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<section class="search-result hotel_search_results">
	<div class="container mb-3 col-md-offset-2 col-md-8">
        <div class="row">
            <div class="col-sm-3 p-0">
                <!--<p class="id-hotels-found-p"><strong>03</strong> Hotels Found</p>-->
				<div class="avlhtls id-hotels-found-p"><strong id="filter_records"></strong> <span class="hide"> of <strong id="total_records"><?php echo $mini_loading_image ?></strong> </span> Hotels found
                        </div>
            </div>
            <div class="col-sm-offset-3 col-sm-6 text-right mb-1 p-0">
               <!-- <span class="id-filter-price tog1"><i class="fa fa-caret-down fa-fil-tog1"></i>&nbsp; Name</span>-->
				<li class="sortli threonly" data-sort="hn">
					<span class="id-filter-price tog1"><a class="name-l-2-h asc" data-order="asc"><i class="fa fa-caret-down fa-fil-tog1"></i>&nbsp; Name</a></span>
					<span class="id-filter-price tog1"><a class="name-h-2-l hide des" data-order="desc"><i class="fa fa-caret-down fa-fil-tog1"></i>&nbsp; Name</a></span>
                </li>
				<li class="sortli threonly" data-sort="sr">
					<span class="id-filter-price tog2"><a class="star-l-2-h asc" data-order="asc"><i class="fa fa-caret-down fa-fil-tog2"></i>&nbsp; Rating</a></span>
					<span class="id-filter-price tog2"><a class="star-h-2-l hide  des" data-order="desc"><i class="fa fa-caret-down fa-fil-tog2"></i>&nbsp; Rating</a></span>
				</li>
				<li class="sortli threonly" data-sort="p">
					<span class="id-filter-price tog3"><a class="price-l-2-h asc" data-order="asc"><i class="fa fa-caret-down fa-fil-tog3"></i>&nbsp; Price</a></span>
					<span class="id-filter-price tog3"><a class="price-h-2-l hide  des" data-order="desc"><i class="fa fa-caret-down fa-fil-tog3"></i>&nbsp; Price</a></span>
					
				</li>
				
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 p-0 id-hotel-main-row">
                <div class="row">
                    <i class="fa fa-map-marker pull-right id-map-style" aria-hidden="true"></i>
                    <div class="col-sm-3 p-0">
                        <div class="id-img-div2">
                            <img data-toggle="modal" data-target="#myGallary" src="https://media-cdn.tripadvisor.com/media/photo-s/19/ab/18/57/paramount-hotel-dubai.jpg" width="100%" height="150px;">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <h3>Century residency</h3>
                        <p>104 Hospital Road,Avenue Road Cross,Parallel Road To</p>
                        <div class="starrtinghotl rating-no"><span class="h-sr hide">1</span><span class="star 1 "></span><span class="star 2"></span><span class="star 3 active"></span><span class="star 4 "></span><span class="star 5"></span></div><br>
                        <hr class="mt-1 mb-1">
                        <i class="fa fa-wifi fa-meal-icons" aria-hidden="true" title="Wi-Fi"></i>
                        <i class="fa fa-plane fa-meal-icons" aria-hidden="true" title="Flight"></i>
                        <i class="fa fa-cutlery fa-meal-icons" aria-hidden="true" title="Meal"></i>
                        <i class="fa fa-coffee fa-meal-icons" aria-hidden="true" title="Coffee Shop"></i>
                        <i class="fa fa-credit-card fa-meal-icons" aria-hidden="true" title="Credit Card Accepted"></i>
                        <i class="fa fa-taxi fa-meal-icons" aria-hidden="true" title="Cab Service"></i>
                    </div>
                    <div class="col-sm-3 text-center ">
                        <h3><small>Starting Price</small><br><strong> &#8377;  15,000</strong></h3>
                        <button class="btn btn-danger" id="select-btn">Select Rooms</button>
                    </div>
                </div>
                <div class="row mt-2 id-hotel-content-row" style="display: none;">
                    <div class="container-fluid id-content">
                        <div class="col-sm-3 padfive">
                            <div class="row">
                                <small>Filter by Board Basis</small>
                                <input type="text" name="" placeholder="Search Rooms" class="form-control">
                            </div>
                            <div class="row mt-1">
                                <small>Filter by Room Type</small>
                                <select class="form-control id-select-content selectpicker" multiple data-live-search="true">
                                    <option>Room Only</option>
                                    <option>Breakfast</option>
                                    <option>Half Board</option>
                                    <option>Full Board</option>
                                    <option>All Inclusive</option>
                                </select>
                            </div>
                            <div class="row mt-1">
                                <div class="squaredThree3">
                                    <input type="checkbox" name="" id="freecancellation">
                                    <label for="freecancellation">
                                        <span class="lbllbl-2">Free Cancellation</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-9 pr-0">
                            <div class="row shadow-1 id-content-row">
                                <div class="col-sm-6">
                                    <h4>Room Superior</h4>
                                    <small>Room only</small>
                                </div>
                                <div class="col-sm-3 text-center">
                                    <h4 class="id-price-text">3,469</h4>
                                    <small>Per room per night</small>
                                </div>
                                <div class="col-sm-3 text-right">
                                    <button class="btn btn-primary id-room-book">Book</button><br>
                                    <small>Cancellation Policy</small>
                                </div>
                                <div class="col-sm-12 p-0">
                                    <div class="p-cancellation">Free Cancellation</div>
                                </div>
                            </div>
                            <div class="row shadow-1 mt-1 id-content-row">
                                <div class="col-sm-6">
                                    <h4>Deluxe</h4>
                                    <small>Room only</small>
                                </div>
                                <div class="col-sm-3 text-center">
                                    <h4 class="id-price-text">4,500</h4>
                                    <small>Per room per night</small>
                                </div>
                                <div class="col-sm-3 text-right">
                                    <button class="btn btn-primary id-room-book">Book</button><br>
                                    <small>Cancellation Policy</small>
                                </div>
                                <div class="col-sm-12 p-0">
                                    <div class="p-cancellation">Free Cancellation</div>
                                </div>
                            </div>
                            <div class="row shadow-1 mt-1 id-content-row">
                                <div class="col-sm-6">
                                    <h4>Executive Double Room</h4>
                                    <small>Room only</small>
                                </div>
                                <div class="col-sm-3 text-center">
                                    <h4 class="id-price-text">5,169</h4>
                                    <small>Per room per night</small>
                                </div>
                                <div class="col-sm-3 text-right">
                                    <button class="btn btn-primary id-room-book">Book</button><br>
                                    <small>Cancellation Policy</small>
                                </div>
                                <div class="col-sm-12 p-0">
                                    <div class="p-cancellation">Free Cancellation</div>
                                </div>
                            </div>
                            <div class="row shadow-1 mt-1 id-content-row">
                                <div class="col-sm-6">
                                    <h4>Deluxe Air Conditioning</h4>
                                    <small>Room only</small>
                                </div>
                                <div class="col-sm-3 text-center">
                                    <h4 class="id-price-text">6,101</h4>
                                    <small>Per room per night</small>
                                </div>
                                <div class="col-sm-3 text-right">
                                    <button class="btn btn-primary id-room-book">Book</button><br>
                                    <small>Cancellation Policy</small>
                                </div>
                                <div class="col-sm-12 p-0">
                                    <div class="p-cancellation">Free Cancellation</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--<div class="row">
            <div class="col-md-12 p-0 id-hotel-main-row">
                <div class="row">
                    <i class="fa fa-map-marker pull-right id-map-style" aria-hidden="true"></i>
                    <div class="col-sm-3 p-0">
                        <div class="id-img-div2">
                            <img src="https://static.independent.co.uk/s3fs-public/thumbnails/image/2019/03/11/10/singapore.jpg" width="100%" height="150px;">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <h3>Hotel nandhini - minerva circle</h3>
                        <p>104 Hospital Road,Avenue Road Cross,Parallel Road To</p>
                        <div class="starrtinghotl rating-no"><span class="h-sr hide">1</span><span class="star 1 "></span><span class="star 2 "></span><span class="star 3 "></span><span class="star 4 active"></span><span class="star 5"></span></div><br>
                        <hr class="mt-1 mb-1">
                        <i class="fa fa-wifi fa-meal-icons" aria-hidden="true" title="Wi-Fi"></i>
                        <i class="fa fa-plane fa-meal-icons" aria-hidden="true" title="Flight"></i>
                        <i class="fa fa-cutlery fa-meal-icons" aria-hidden="true" title="Meal"></i>
                        <i class="fa fa-coffee fa-meal-icons" aria-hidden="true" title="Coffee Shop"></i>
                        <i class="fa fa-credit-card fa-meal-icons" aria-hidden="true" title="Credit Card Accepted"></i>
                        <i class="fa fa-taxi fa-meal-icons" aria-hidden="true" title="Cab Service"></i>
                    </div>
                    <div class="col-sm-3 text-center ">
                        <h3><small>Starting Price</small><br><strong> &#8377;  15,000</strong></h3>
                        <button class="btn btn-danger">Select Rooms</button>
                    </div>
                </div>
                <div class="row id-hotel-content-row2" style="display: none;">
                    sasasas
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 p-0 id-hotel-main-row">
                <div class="row">
                    <i class="fa fa-map-marker pull-right id-map-style" aria-hidden="true"></i>
                    <div class="col-sm-3 p-0">
                        <div class="id-img-div2">
                            <img src="https://i1.wp.com/www.traveloffpath.com/wp-content/uploads/2020/08/Singapore-Reopening-Borders-For-Tourism-Sept-1-From-Select-Countries-3.jpg?resize=759%2C500&ssl=1" width="100%" height="150px;">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <h3>Hotel t.a.p paradise</h3>
                        <p>104 Hospital Road,Avenue Road Cross,Parallel Road To</p>
                        <div class="starrtinghotl rating-no"><span class="h-sr hide">1</span><span class="star 1"></span><span class="star 2 active"></span><span class="star 3 "></span><span class="star 4 "></span><span class="star 5"></span></div><br>
                        <hr class="mt-1 mb-1">
                        <i class="fa fa-wifi fa-meal-icons" aria-hidden="true" title="Wi-Fi"></i>
                        <i class="fa fa-plane fa-meal-icons" aria-hidden="true" title="Flight"></i>
                        <i class="fa fa-cutlery fa-meal-icons" aria-hidden="true" title="Meal"></i>
                        <i class="fa fa-coffee fa-meal-icons" aria-hidden="true" title="Coffee Shop"></i>
                        <i class="fa fa-credit-card fa-meal-icons" aria-hidden="true" title="Credit Card Accepted"></i>
                        <i class="fa fa-taxi fa-meal-icons" aria-hidden="true" title="Cab Service"></i>
                    </div>
                    <div class="col-sm-3 text-center ">
                        <h3><small>Starting Price</small><br><strong> &#8377;  15,000</strong></h3>
                        <button class="btn btn-danger">Select Rooms</button>
                    </div>
                </div>
                <div class="row id-hotel-content-row3" style="display: none;">
                    sasasas
                </div>
            </div>
        </div>-->
    </div>
    <div class="container mb-3 col-md-offset-2 col-md-8"  id="page-parent">
      <!--   <?php echo $GLOBALS['CI']->template->isolated_view('share/loader/hotel_result_pre_loader', $data); ?> -->
        <div class="resultalls">

            <!--<div class="coleft" id="coleftid">
                <div class="flteboxwrp">

                    <div class="filtersho">
                        <div class="avlhtls"><strong id="filter_records"></strong> <span class="hide"> of <strong id="total_records"><?php echo $mini_loading_image ?></strong> </span> Hotels found
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
                                                    <a data-toggle="collapse" href="#collapseOne"><span class="glyphicon glyphicon-chevron-down"></span>Price</a>
                                                </h4>
                                            </div>
                                            <div id="collapseOne" class="panel-collapse collapse in">
                                                <div class="panel-body">
                                                    <?php echo $mini_loading_image ?>
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
                                                    <a data-toggle="collapse" href="#collapseTwo"><span class="glyphicon glyphicon-chevron-down"></span>Star Rating</a>
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
                                                    <a data-toggle="collapse" href="#collapseThree"><span class="glyphicon glyphicon-chevron-down"></span>Hotel Name</a>
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
                                                    <a data-toggle="collapse" href="#collapseSix"><span class="glyphicon glyphicon-chevron-down"></span>Amenities</a>
                                                </h4>
                                            </div>
                                            <div id="collapseSix" class="panel-collapse collapse in amenitie" >
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
                                                    <a data-toggle="collapse" href="#collapseSeven"><span class="glyphicon glyphicon-chevron-down"></span>Hotel Free Cancellation</a>
                                                </h4>
                                            </div>
                                            <div id="collapseSeven" class="panel-collapse collapse in">
                                                <div class="panel-body">
                                                    <div id="collapse4" class="in">	
                                                        <div class="boxins">							
                                                            <div class="squaredThree">
                                                                <input type="checkbox" id="freecancel-hotels-view" value="filter" class="freecancel-hotels-view" name="free_cancel[]">
                                                                <label for="freecancel-hotels-view"></label>
                                                            </div>
                                                            <label class="lbllbl" for="freecancel-hotels-view">Free Cancellation</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" href="#collapseFour"><span class="glyphicon glyphicon-chevron-down"></span>Hotel Location</a>
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
                                </div>-->

                                <!-- <div class="rangebox">
                                        <button data-target="#price-refine" data-toggle="collapse" class="collapsebtn refine-header" type="button">
                                Price
                                </button>
                                <?php echo $mini_loading_image ?>
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
                                </div> 


                            </div>
                        </form>
                    </div>
                </div>
            </div>-->


            <div class="colrit_hotel" style="width:100%">
                <div class="insidebosc">
                    <div class="resultall hide">
                        <div class="filt_map">
                            <div class="filter_tab"><i class="fal fa-filter"></i></div>
                        </div>
                        <div class="filter_tab"><i class="fal fa-filter"></i></div>
                        <div class="vluendsort">
                            <!-- <div class="col-xs-5 nopad">
                            <div class="nityvalue">
                                     <label type="button" class="vlulike active filter-hotels-view" for="all-hotels-view">
                                            <input type="radio" id="all-hotels-view" value="all" class="hide deal-status-filter" name="deal_status[]" checked="checked">
                                            All Hotels
                                    </label>
                            </div>
                            </div> -->
                            <div class="col-xs-10 mobile_width nopad">
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
                                                        <a class="sorta name-l-2-h asc" data-order="asc"><i class="fal fa-sort-alpha-down"></i> <strong>Name</strong></a>
                                                        <a class="sorta name-h-2-l hide des" data-order="desc"><i class="fal fa-sort-alpha-up"></i> <strong>Name</strong></a>
                                                    </li>
                                                    <li class="sortli threonly" data-sort="sr">
                                                        <a class="sorta star-l-2-h asc" data-order="asc"><i class="fal fa-star"></i> <strong>Star</strong></a>
                                                        <a class="sorta star-h-2-l hide  des" data-order="desc"><i class="fal fa-star"></i> <strong>Star</strong></a>
                                                    </li>
                                                    <li class="sortli threonly" data-sort="p">
                                                        <a class="sorta price-l-2-h asc" data-order="asc"><i class="fal fa-tag"></i> <strong>Price</strong></a>
                                                        <a class="sorta price-h-2-l hide  des" data-order="desc"><i class="fal fa-tag"></i> <strong>Price</strong></a>
                                                    </li>
                                                </ul>
                                            </div>

                                            <div class="map_tab"><a class="map_click"><i class="fa fa-map-marker"></i></a></div>
                                            <div class="list_tab" style="display:  none;"><i class="fa fa-th-list"></i></div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-2 mobile_none nopad">
                                <div class="mapviw noviews">
                                    <!-- <div class="mapviwhtl nopad noviews reswd">
                                           <div class="rit_view">
                                                   <a class="view_type grid_click"><span class="fa fa-th"></span></a> 
                                           </div>
                                         </div> -->
                                    <div class="mapviwlist nopad noviews reswd">
                                        <div class="rit_view">
                                            <a class="view_type list_click active" id="list_clickid" onclick="showhide()"><span class="fa fa-list"></span></a> 
                                        </div>
                                    </div>
                                    <div class="mapviwhtl nopad noviews reswd">
                                        <div class="rit_view">
                                            <a class="view_type map_click" id="map_clickid" onclick="showhide()"><span class="fa fa-map-marker"></span></a> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>		
                    <div class="allresult">
                        <?php echo $loading_image; ?>

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

                        <div id="hotel_search_result" class="hotel-search-result-panel result_srch_htl owl-carousel">

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
                        <div id="npl_img" class="text-center" loaded="true">
                            <?= '<img src="' . $GLOBALS['CI']->template->template_images('loader_v1.gif') . '" alt="Please Wait" />' ?>
                            <!-- <div class="spinner"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div><div class="bounce4"></div></div> -->
                        </div>
                        <div id="empty_hotel_search_result"  style="display:none">
                            <div class="noresultfnd">
                                <div class="imagenofnd"><img src="<?= $template_images ?>empty.jpg" alt="Empty" /></div>
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabelMap"></h4>
                <ul class="htmimgstr">					
                </ul>

            </div>
            <div class="modal-body">			
                <iframe src="" id="map-box-frame" name="map_box_frame" style="height: 500px;width: 850px;">
                </iframe>
            </div>
        </div>
    </div>
</div>
<!-- modal -->
  <div class="modal fade" id="myGallary" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <!-- <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Modal Header</h4>
        </div> -->
        <div class="modal-body">
          <div id="owl-gallary" class="owl-carousel owl-theme">
              <div class="item"><img src="https://cdn.cnn.com/cnnnext/dam/assets/200924183413-dubai-9-1.jpg" alt="The Last of us"></div>
              <div class="item"><img src="https://static.toiimg.com/thumb/msid-22509870,width-1070,height-580,resizemode-75,imgsize-22509870,pt-32,y_pad-40/22509870.jpg" alt="GTA V"></div>
              <div class="item"><img src="https://cdn.britannica.com/15/189715-050-4310222B/Dubai-United-Arab-Emirates-Burj-Khalifa-top.jpg" alt="Mirror Edge"></div>
          </div>
        </div>
        <!-- <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div> -->
      </div>
      
    </div>
  </div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
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
<!-- 
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyANXPM-4Tdxq9kMnI8OpL-M6kGsFFWreIY" type="text/javascript"></script> -->
<script type="text/javascript">
    $(document).ready(function () {
        if ($(window).width() < 550) {
            //alert("hiiiii");
            $(document).on("click", ".madgrid", function () {
                var result_key = $(this).data('key');
                var hotel_code = $(this).data('hotel-code');
                //var result_key = $(".result-index").data('key');

                //var hotel_code = $(".result-index").data('hotel-code');

                var result_token = $("#mangrid_id_" + result_key + '_' + hotel_code).val();
                var booking_source = $("#booking_source_" + result_key + '_' + hotel_code).val();
                var operation_details = $(".operation").val();
                window.location = '<?php echo base_url() . 'index.php/hotel/hotel_details/' . ($hotel_search_params['search_id']) ?>' + '?ResultIndex=' + result_token + '&booking_source=' + booking_source + '&op=' + operation_details + '';
            });


        }
        $(".close_fil_box").click(function () {
            $(".coleft").hide();
            $(".resultalls").removeClass("open");
        });



    });
    // functions that return icons.  Make or find your own markers.

</script>
<script type="text/javascript">
    $(document).ready(function () {

        window.setInterval(function () {
            swal({
                text: 'Your session has expired,Please search again!!!!',
                type: 'info'

            }).then(function () {
                window.location.href = "<?php echo base_url(); ?>";
            });
            //alert('Session Expired!!!');
            //window.location.href="<?php //echo base_url(); ?>";
        }, 900000);
    });

</script>

<style type="text/css">
    .mb-3{
        margin-bottom: 30px;
    }
    .id-hotel-main-row{
        border: 1px solid #ccc;
        background-color: #fff;
        margin-bottom: 15px;
        box-shadow: 0 2px 5px 0 rgba(0,0,0,0.0),0 2px 10px 0 rgba(0,0,0,0.1) !important;
    }
    .id-hotel-main-row:hover{
       border: 1px solid #0095ff;
       /*background-color: #ebf4ff; */
       background-color: #f5faff; 
    }
    p {
       margin: 0 0 5px!important;
   }
   .id-hotel-main-row:focus {     
        background-color:yellow;    
    }
    .id-hotel-content-row{
        padding: 10px;
    }
    .mb-1{
        margin-bottom: 10px;
    }
    .fa-meal-icons{
        font-size: 20px;
        color: #3a99d2;
        margin-right: 15px;
    }
    .id-filter-price{
        padding: 5px 20px;
        background-color: #bedeff;
        cursor: pointer;
    }
    .mt-3{
        margin-top: 30px;
    }
    .padfive {
         padding: 0px 2px !important; 
    }
    .id-filter-div{
        padding: 10px;
        background-color: #fff;
    }
    .id-filter-div .form-control {
        border: 1px solid #ddd;
        border-radius: 0;
    }
    .bg-white{
        background-color: #fff;
    }
    .shadow-1{
        box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12) !important;
    }
    .id-fa-search{
        position: absolute;
        top: 28px;
        left: 13px;
        color: #aaa;
    }
    .id-search-input{
        padding-left: 35px;
    }
    .id-hotels-found-p{
        font-size: 15px;
        color: #888;
    }
    .id-hotels-found-p strong{
        color: #4499f1;
    }
    .id-hotel-content-row h4{
        margin: 0;
    }
    .id-content{
        border: 1px solid #ccc;
        padding: 20px;
        border-radius: 8px;
    }
    .id-room-book{
        font-size: 12px;
        width: 110px;
    }
    .pr-0{
        padding-right: 0!important;
    }
    .id-content-row{
        padding: 6px 0 0;
        background: #fff;
    }
    .id-select-content{
        background: transparent!important;
        cursor: pointer;
    }
    .id-content-row .id-price-text{
        color: #e20000;
    }
    .bootstrap-select > .dropdown-toggle.bs-placeholder, .bootstrap-select > .dropdown-toggle.bs-placeholder:hover, .bootstrap-select > .dropdown-toggle.bs-placeholder:focus, .bootstrap-select > .dropdown-toggle.bs-placeholder:active {
        color: #999;
        background: transparent;
        border: 1px solid #ccc;
    }
    .id-content .form-control {
        background: transparent;
    }
    .bootstrap-select > .dropdown-toggle {
        background: transparent;
        border: 1px solid #ccc;
    }
    .p-cancellation {
        /*background-color: #e7f2ff;*/
        background-color: #f3f3f3;
        padding: 2px 6px;
        color: #666;
        font-size: 12px;
    }
    .datein {
        font-size: 13px;
    }
    .bootstrap-select > .dropdown-toggle.bs-placeholder, .bootstrap-select > .dropdown-toggle.bs-placeholder:hover, .bootstrap-select > .dropdown-toggle.bs-placeholder:focus, .bootstrap-select > .dropdown-toggle.bs-placeholder:active {
        border: none!important;
    }
    .id-filter-div .dropdown-menu{
        z-index: 111111;
    }
    .id-filter-div small{
        color: #a7a7a7;
    }
    .bootstrap-select > .dropdown-toggle {
        border: none;
    }
    .id-filter-div .id-price-range-div{
        position: absolute;
        background: #fff;
        padding: 10px;
        width: 265px;
        box-shadow: 0 6px 12px rgba(0,0,0,.175);
        z-index: 111111;
        border-radius: 4px;
        display: none;
    }
    .id-filter-div .p-price-range{
        padding: 7px;
        margin: 0!important;
        border: 1px solid #ccc;
        cursor: pointer;
    }
    .id-content .filter-option{
        border: 1px solid #ccc;
    }
    .id-map-style{
        padding: 10px;
        font-size: 18px;
        cursor: pointer;
        position: absolute;
        right: 0;
        color: #3a99d2;
        z-index: 1;
    }
    .id-img-div2 img{
        cursor: zoom-in;
    }
    #owl-gallary .item img{
        display: block;
        width: 100%;
        height: auto;
    }
    #owl-gallary .owl-buttons{
        display: none;
    }
    #myGallary .modal-dialog{
        margin-top: 100px;
    }
    hr.mt-1.mb-1 {
    margin-top: 10px;
}
    
</style>
<script type="text/javascript">
    $(document).ready(function(){
        $('#select-btn').click(function(){
             $(this).text( $(this).text() == 'Select Rooms' ? "Close Rooms" : "Select Rooms");
            $('.id-hotel-content-row').slideToggle()(3000);
        });
        // $('#select-btn').click(function(){
        //     alert('sasa');
        //     if (this.value=="Select Rooms") this.value = "Select Rooms";
        //     else this.value = "Close Rooms";
        // });

        $('.p-price-range').on('click', function(e){
            $(this).attr('div-status',1);
            $('.id-price-range-div').slideToggle()(3000);    
        });

        $(document).on('click', function (e) {
            if( $(e.target).closest(".p-price-range").length > 0 ) {
                return false;
            }else{
                let divDisplayAttr = $('#price-refine').css('display');
                let divStatus =  $('.p-price-range').attr('div-status');
                if(divStatus == 1){                              
                    if(divDisplayAttr = "block"){
                        $('.id-price-range-div').hide();    
                    }   
                }            
            }           
        });

        $("#owl-gallary").owlCarousel({
 
              navigation : true, // Show next and prev buttons
              slideSpeed : 300,
              paginationSpeed : 400,
              singleItem:true
         
              // "singleItem:true" is a shortcut for:
              // items : 1, 
              // itemsDesktop : false,
              // itemsDesktopSmall : false,
              // itemsTablet: false,
              // itemsMobile : false
          });
        $('.tog1').click(function(){
            $('.fa-fil-tog1').toggleClass('fa-caret-up','fa-caret-down');
        });
        $('.tog2').click(function(){
            $('.fa-fil-tog2').toggleClass('fa-caret-up','fa-caret-down');
        });
        $('.tog3').click(function(){
            $('.fa-fil-tog3').toggleClass('fa-caret-up','fa-caret-down');
        });
    });
</script>