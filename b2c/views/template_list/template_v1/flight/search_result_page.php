<?php
$data['trip_details'] = $flight_search_params;
$template_images = $GLOBALS['CI']->template->template_images();
$mini_loading_image = '<div class="text-center loader-image"><img src="' . $template_images . 'loader_v3.gif" alt="Loading........"/></div>';
$loading_image = '<div class="text-center loader-image"><img src="' . $template_images . 'loader_v1.gif" alt="Loading........"/></div>';
$flight_o_direction_icon = '<img src="' . $template_images . 'icons/flight-search-result-up-icon.png" alt="Flight Search Result Up Icon">';
echo $GLOBALS['CI']->template->isolated_view('flight/search_panel_summary');
Js_Loader::$css[] = array('href' => $GLOBALS['CI']->template->template_css_dir('owl.carousel.min.css'), 'media' => 'screen');
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('owl.carousel.min.js'), 'defer' => 'defer');
?>
<section class="search-result">
    <div class="container">
        <?php echo $GLOBALS['CI']->template->isolated_view('share/loader/flight_result_pre_loader', $data); ?>
        <div class="row rowclear">
            <div class="col-lg-2 mobHide">
                <h2 class="h4">Refine your search</h2>
                <!-- Refine Search Filters Start -->
                <div class="panel panel-default b-r-0">
                    <div class="panel-body refine">
                        <div class="row">
                            <div class="col-lg-12 col-md-4 col-sm-6">
                                <h3 class="h4 m-t-0 refine-header trigger-collapse-view"
                                    data-toggle="collapse" data-target=".price-refine"><img
                                        src="<?= $template_images ?>icons/price-tag-icon.png"
                                        alt="Price Tag Icon"> Price</h3>
                                <div class="collapse in price-refine">
                                    <p id="amount"></p>
                                    <div id="slider-range" class="" aria-disabled="false"></div>
                                    <?= $mini_loading_image; ?>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-4 col-sm-6">
                                <hr class="hr-10 visible-lg-block">
                                <h3 class="h4 m-t-0 refine-header trigger-collapse-view"
                                    data-toggle="collapse" data-target=".airlines-refine"><img
                                        src="<?= $template_images ?>icons/flight-up-icon.png"
                                        alt="Flight Up Icon"> Airlines</h3>
                                <div class="collapse in airlines-refine" id="allairlines"><?= $mini_loading_image; ?></div>
                            </div>
                            <div class="clearfix visible-sm-block"></div>
                            <div class="col-lg-12 col-md-4 col-sm-6">
                                <hr class="visible-lg-block hr-10">
                                <h3 class="h4 m-t-0 refine-header trigger-collapse-view"
                                    data-toggle="collapse" data-target=".stop-refine"><img
                                        src="<?= $template_images ?>icons/flight-down-icon.png"
                                        alt="Flight Down Icon"> Stop</h3>
                                <div class="collapse in stop-refine" id="stopCount"><?= $mini_loading_image; ?></div>
                            </div>
                            <div class="clearfix visible-md-block"></div>
                            <div class="col-lg-12 col-md-4 col-sm-6">
                                <hr class="visible-lg-block hr-10">
                                <h3 class="h4 m-t-0 refine-header trigger-collapse-view"
                                    data-toggle="collapse" data-target=".duration-refine"><i class="fa fa-clock-o"></i> Departure</h3>
                                <div class="collapse in duration-refine">
                                    <p id="time"></p>
                                    <div id="slider-range1" class="" aria-disabled="false"></div>
                                    <?= $mini_loading_image; ?>
                                </div>
                            </div>
                            <div class="clearfix visible-sm-block"></div>
                            <div class="col-lg-12">
                                <button type="button" class="btn btn-t btn-block b-r-0 reset-page-loader">Reset Filters</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Refine Search Filters End -->
            <div class="col-lg-10 nopadMob">
                <div class="row row-no-gutter mobHide" id="clone-list-container">
                    <div class="col-lg-2">
                        <div class="panel panel-default b-r-0 m-0">
                            <div class="panel-body text-center p-tb-8">
                                <img
                                    src="<?= $template_images ?>icons/flight-small-up-icon.png"
                                    alt="Flight Small Up Icon">
                                <h6><a href="#">All Airlines</a></h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-10">
                        <?php echo $mini_loading_image; ?>
                        <div class="owl-carousel" id="owl-carousel-flight-search-result">
                        </div>
                    </div>
                </div>
                <div class="panel panel-info b-r-0 hide summary-container" id="multi-flight-summary-container">
                    <div class="panel-body p-tb-5">
                        <div class="row row-no-gutter text-center">
                            <div class="col-md-5 col-sm-6">
                                <img src="" alt="Departure Flight" class="departure-flight-icon">
                                <h6><b class="departure-flight-name">Please Select</b></h6>
                                <h6 class="outbound-details">Please Select</h6>
                                <p class="outbound-timing-details"><span>Dep : <span class="departure"></span></span> <i class="fa fa-clock-o"></i> <span>Arr : <span class="arrival"></span></span></p>
                            </div>
                            <div class="col-md-1 hidden-sm">
                                <br>
                                <i class="fa fa-plus-circle fa-lg fa-fw"></i>
                            </div>
                            <div class="col-md-5 col-sm-6">
                                <img src="" alt="Arrival Flight" class="arrival-flight-icon">
                                <h6><b class="arrival-flight-name">Please Select</b></h6>
                                <h6 class="inbound-details">Please Select</h6>
                                <p class="inbound-timing-details"><span>Dep : <span class="departure"></span></span> <i class="fa fa-clock-o"></i> <span>Arr : <span class="arrival"></span></span></p>
                            </div>
                            <div class="clearfix visible-sm-block"></div>
                        </div>
                        <div class="row row-no-gutter text-center bg-primary">
                            <div class="col-md-6 col-sm-6">
                                <br>
                                <h5 class="m-t-0"><b> <span class="currency"></span> <span class="h4 text-i f-p"></span></b></h5>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <h5>
                                    <input type="hidden" id="flight-from-price" value="0">
                                    <input type="hidden" id="flight-to-price" value="0">
                                    <form id="multi-flight-form" action="" method="POST" target="_blank">
                                        <div class="hide" id="trip-way-wrapper"></div>
                                        <button class="btn btn-default b-r-0" type="submit" id="multi-flight-booking-btn">Book</button>
                                        <?php
                                        //echo $this->facebook->share_button();
                                        //$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                                        //echo $actual_link;
                                        ?>
                                        <div class="fb-share-button" data-href="<?php //echo $actual_link ?>" data-layout="link"></div>
                                    </form>
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>
                <h2 class="h4 text-p"><?php echo $mini_loading_image ?>
                    <span id="total_records">Loading....</span> Flights
                </h2>
                <hr class="hr-10">
                <div class="row text-center sorting-wrapper mobTxtLft">
                    <div class="col-md-2 col-sm-6 sortMob">
                        <h6 class="m-0 name-l-2-h hand-cursor loader"><i
                                class="fa fa-sort-alpha-asc"></i> Airlines</h6>
                        <h6 class="m-0 name-h-2-l hide hand-cursor loader"><i
                                class="fa fa-sort-alpha-desc"></i> Airlines</h6>
                    </div>
                    <div class="clearfix visible-xs-block"></div>
                    <hr class="hr-10 visible-xs-block">
                    <div class="col-md-2 col-sm-6 sortMob">
                        <h6 class="m-0 departure-l-2-h hand-cursor loader"><i
                                class="fa fa-clock-o"></i> Departure</h6>
                        <h6 class="m-0 departure-h-2-l hide hand-cursor loader"><i
                                class="fa fa-clock-o"></i> Departure</h6>
                    </div>
                    <div class="clearfix visible-xs-block visible-sm-block"></div>
                    <hr class="hr-10 visible-xs-block visible-sm-block">
                    <div class="col-md-1"></div>
                    <div class="col-md-2 col-sm-6 sortMob">
                        <h6 class="m-0 arrival-l-2-h hand-cursor loader"><i
                                class="fa fa-clock-o"></i> Arrival</h6>
                        <h6 class="m-0 arrival-h-2-l hide hand-cursor loader"><i
                                class="fa fa-clock-o"></i> Arrival</h6>
                    </div>
                    <div class="clearfix visible-xs-block"></div>
                    <hr class="hr-10 visible-xs-block">
                    <div class="col-md-2 col-sm-6 sortMob">
                        <h6 class="m-0 duration-l-2-h hand-cursor loader"><i
                                class="fa fa-sort-numeric-asc"></i> Duration</h6>
                        <h6 class="m-0 duration-h-2-l hide hand-cursor loader"><i
                                class="fa fa-sort-numeric-desc"></i> Duration</h6>
                    </div>
                    <div class="clearfix visible-xs-block visible-sm-block"></div>
                    <hr class="hr-10 visible-xs-block visible-sm-block">
                    <div class="col-md-3 sortMob">
                        <h6 class="m-0 price-l-2-h hand-cursor loader"><i
                                class="fa fa-sort-amount-asc"></i> Price</h6>
                        <h6 class="m-0 price-h-2-l hide hand-cursor loader"><i
                                class="fa fa-sort-amount-desc"></i> Price</h6>
                    </div>
                </div>
                <hr class="hr-10">
                <?php echo $loading_image; ?>
                <div id="flight_search_result"
                     class="flight-search-result-panel b-r-0 m-b-10"></div>
                <div id="empty_flight_search_result" style="display: none;">No Data
                    Found
                </div>
                <hr class="hr-10">
                <div class="clearfix">
                    <div class="pull-right"><span class="h5">Showing: <?php echo $mini_loading_image ?><span
                                class="visible-row-record-count">...</span> of <span
                                class="total-row-record-count">...</span> Flights</span></div>
                </div>
                <hr class="hr-10 invisible">
            </div>
        </div>
    </div>
</section>
<?php
foreach ($active_booking_source as $t_k => $t_v) {
    $active_source[] = $t_v['source_id'];
}
$active_source = json_encode($active_source);
?>
<script	src="<?php echo JAVASCRIPT_LIBRARY_DIR ?>jquery.jsort.0.4.min.js" defer></script>
<script>
    $(document).ready(function () {
        var application_preference_currency = "<?php echo $this->currency->get_currency_symbol(get_application_currency_preference()); ?>";
        /**
         *Load Hotels - Source Trigger
         */
        function load_flight() {
            pre_load_audio();
            var _active_booking_source = <?= $active_source ?>;
            $.each(_active_booking_source, function (k, booking_source_name) {
                core_flight_loader(booking_source_name);
            });
        }

        /**
         *Load Hotels Core Ajax
         */
        function core_flight_loader(booking_source_id)
        {
            $('.loader-image').show();
            var params = {booking_source: booking_source_id, search_id:<?php echo $flight_search_params['search_id'] ?>, op: 'load'};
            $.ajax({
                type: 'GET',
                url: app_base_url + 'index.php/ajax/flight_list',
                async: true,
                cache: true,
                data: $.param(params),
                dataType: 'json',
                success: function (result) {
                    $('.loader-image').hide();
                    if (result.hasOwnProperty('status') == true && result.status == true) {
                        $('#flight_search_result').html(result.data);
                        post_load_audio();
                        //update total hotel count
                        //update_hotel_summary_counter(result.total_result_count);
                        updateFilters();
                        //filter_row_origin_marker();
                        update_total_count_summary();
                        default_col2x();
                        hide_result_pre_loader();
                    }
                }
            });
        }

        function toggle_more_details()
        {
            $(document).on('click', '.iti-btn', function (e) {
                e.preventDefault();
                toggle_active_class(this);
                $('.i-i-s-t', $(this).closest('.r-r-i')).toggle();
            });
        }

        var fare_details_cache = {};
        function toggle_fare_details()
        {
            //Fare Rules
            $(document).on('click', ".iti-fare-btn", function (e) {
                e.preventDefault();
                toggle_active_class(this);
                var _tmp_result_row_index = $(this).closest('.r-r-i');
                var _data_access_key = $('.data-access-key', _tmp_result_row_index).val();
                var _booking_source = $('.booking-source', _tmp_result_row_index).val();
                var _search_access_key = $('.search-access-key', _tmp_result_row_index).val();
                var _provab_auth_key = $('.provab-auth-key', _tmp_result_row_index).val();   //
                $('.loader-image', _tmp_result_row_index).show();
                $('.i-i-f-s-t', _tmp_result_row_index).toggle();
                if ((_data_access_key in fare_details_cache) == false) {
                    set_fare_details(_data_access_key, _booking_source, _search_access_key, _provab_auth_key);
                }
                $('.i-s-s-c', _tmp_result_row_index).html(fare_details_cache[_data_access_key]);
                $('.loader-image', _tmp_result_row_index).hide();
            });
        }

        //set fare details cache
        function set_fare_details(data_access_key, source, search_access_key, provab_auth_key)
        {
            var params = {data_access_key: data_access_key, booking_source: source, search_access_key: search_access_key, provab_auth_key: provab_auth_key};
            $.ajax({
                type: 'POST',
                url: app_base_url + 'index.php/ajax/get_fare_details',
                async: false,
                cache: false,
                data: $.param(params),
                dataType: 'json',
                success: function (result) {
                    if (result.hasOwnProperty('status') == true && result.status == true) {
                        fare_details_cache[data_access_key] = result.data;
                    } else {
                        fare_details_cache[data_access_key] = result.msg;
                    }
                }
            });
        }
        //load flight from active sources
        show_result_pre_loader();
        load_flight();
        toggle_more_details();
        toggle_fare_details();

        function updateFilters()
        {
            //update filters
            var airlineList = {};
            var stopCountList = {};
            var minPrice = 99999999;
            var maxPrice = 0;
            var minDepartureDatetime = 9999999999999999999;
            var maxDepartureDatetime = 0;
            var price = 0;
            var time = 0;
            var stopcount = 0;
            var cloneList = {};
            var airline = '';
            var cloneCriteriaList = {};
            $('.r-r-i').each(function (key, value) {
                price = $('.price:first', this).data('price');
                time = $('.dep_dt:first', this).data('datetime');
                stopcount = $('.stp:first', this).data('stp');
                airline = $('.a-n:first', this).data('code');
                if (airlineList.hasOwnProperty(airline) == false) {
                    airlineList[airline] = $('.a-n:first', this).text();
                    cloneList[airline + 'MINP'] = key;
                    cloneList[airline + 'MAXP'] = key;
                    cloneList[airline + 'MIXD'] = key;
                    cloneList[airline + 'MAXD'] = key;
                    cloneList[airline + 'MIXS'] = key;
                    cloneList[airline + 'MAXS'] = key;
                    cloneCriteriaList[airline + 'MINP'] = price;
                    cloneCriteriaList[airline + 'MAXP'] = price;
                    cloneCriteriaList[airline + 'MIXD'] = time;
                    cloneCriteriaList[airline + 'MAXD'] = time;
                    cloneCriteriaList[airline + 'MIXS'] = stopcount;
                    cloneCriteriaList[airline + 'MAXS'] = stopcount;
                } else {
                    if (price < cloneCriteriaList[airline + 'MINP']) {
                        cloneCriteriaList[airline + 'MINP'] = price;
                        cloneList[airline + 'MINP'] = key;
                    }
                    if (price > cloneCriteriaList[airline + 'MAXP']) {
                        cloneCriteriaList[airline + 'MAXP'] = price;
                        cloneList[airline + 'MAXP'] = key;
                    }
                    if (time < cloneCriteriaList[airline + 'MIXD']) {
                        cloneCriteriaList[airline + 'MIXD'] = time;
                        cloneList[airline + 'MIXD'] = key;
                    }
                    if (time > cloneCriteriaList[airline + 'MAXD']) {
                        cloneCriteriaList[airline + 'MAXD'] = time;
                        cloneList[airline + 'MAXD'] = key;
                    }
                    if (stopcount > cloneCriteriaList[airline + 'MIXS']) {
                        cloneCriteriaList[airline + 'MIXS'] = stopcount;
                        cloneList[airline + 'MIXS'] = key;
                    }
                    if (stopcount > cloneCriteriaList[airline + 'MAXS']) {
                        cloneCriteriaList[airline + 'MAXS'] = stopcount
                        cloneList[airline + 'MAXS'] = key;
                    }
                }
                if (stopCountList.hasOwnProperty(stopcount) == false) {
                    stopCountList[stopcount] = stopcount;
                }
                if (price < minPrice) {
                    minPrice = price;
                }

                if (price > maxPrice) {
                    maxPrice = price;
                }
                if (time < minDepartureDatetime) {
                    minDepartureDatetime = time;
                }
                if (time > maxDepartureDatetime) {
                    maxDepartureDatetime = time;
                }
            });
            airlineList = getSortedObject(airlineList);
            loadAirlineFilter(airlineList);
            loadStopFilter(stopCountList);
            loadPriceRangeSelector(minPrice, maxPrice);
            loadTimeRangeSelector(minDepartureDatetime, maxDepartureDatetime);
            cloneSlider(cloneList);
        }

        function getArray(objectWrap)
        {
            var objectWrapValueArr = [];
            $.each(objectWrap, function (key, value) {
                objectWrapValueArr.push(value);
            });
            return objectWrapValueArr;
        }

        function getSortedObject(obj)
        {
            var objValArray = getArray(obj);
            var sortObj = {};
            objValArray.sort();
            $.each(objValArray, function (obj_key, obj_val) {
                $.each(obj, function (i_k, i_v) {
                    if (i_v == obj_val) {
                        sortObj[i_k] = i_v;
                    }
                });
            });
            return sortObj;
        }
        function loadTimeRangeSelector(minTime, maxTime)
        {
           
            var slide_left = minTime;
            var slide_right = maxTime;
            $("#slider-range1").slider({
                range: true,
                min: minTime,
                max: maxTime,
                step: 96,
                animate: "slow",
                values: [minTime, maxTime],
                slide: function (event, ui) {
                    max = timeConverter(ui.values[ 1 ]);
                    min = timeConverter(ui.values[ 0 ]);
                    $("#time").text(min + " - " + max);
                },
                change: function (event, ui) {
                    if (parseFloat(ui.values[0]) == slide_left) {
                        if (parseFloat(ui.values[1]) > slide_right) {
                            visibility = ':hidden';
                        } else {
                            visibility = ':visible';
                        }
                    } else {
                        if (parseFloat(ui.values[0]) < slide_left) {
                            visibility = ':hidden';
                        } else {
                            visibility = ':visible';
                        }
                    }
                    slide_left = parseFloat(ui.values[0]);
                    slide_right = parseFloat(ui.values[1]);
                    filter_row_origin_marker(visibility);
                }
            });
            minTime = timeConverter(minTime);
            maxTime = timeConverter(maxTime);
            $("#time").text(minTime + ' - ' + maxTime);
        }

        var sliderCurrency = "<?php echo $this->currency->get_currency_symbol($to_currency); ?>";
        var minDefaultPrice = maxDefaultPrice = '';
        function loadPriceRangeSelector(minPrice, maxPrice)
        {
            var slide_left = minPrice;
            var slide_right = maxPrice;
            minDefaultPrice = minPrice;
            maxDefaultPrice = maxPrice;
            $("#slider-range").slider({
                range: true,
                min: minPrice,
                max: maxPrice,
                animate: "slow",
                values: [minPrice, maxPrice],
                slide: function (event, ui) {
                    $("#amount").text(sliderCurrency + ui.values[ 0 ] + " - " + sliderCurrency + ui.values[ 1 ]);
                },
                change: function (event, ui) {
                    if (parseFloat(ui.values[0]) == slide_left) {
                        if (parseFloat(ui.values[1]) > slide_right) {
                            visibility = ':hidden';
                        } else {
                            visibility = ':visible';
                        }
                    } else {
                        if (parseFloat(ui.values[0]) < slide_left) {
                            visibility = ':hidden';
                        } else {
                            visibility = ':visible';
                        }
                    }
                    slide_left = parseFloat(ui.values[0]);
                    slide_right = parseFloat(ui.values[1]);
                    filter_row_origin_marker(visibility);
                }
            });
            $("#amount").text(sliderCurrency + minPrice + " - " + sliderCurrency + maxPrice);
        }
        function timeConverter(UNIX_timestamp) {
            var a = new Date(UNIX_timestamp);
            var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            var month = months[a.getMonth()];
            var year = a.getFullYear();
            var date = a.getDate();
            var hour = a.getHours();
            var min = a.getMinutes();
            var time = date + ' ' + month + ' ' + hour + ':' + min;
            return time;
        }

        function loadStopFilter(stopCountList)
        {
            var stopList = '';
            if ($.isEmptyObject(stopCountList) == false) {
                $.each(stopCountList, function (key, value) {
                    if (value == 0) {
                        value = 'Non ';
                    }
                    stopList += '<div><label><input type="checkbox" class="stopcountcheckbox" value="' + key + '" checked="checked"><span>' + value + ' Stop</span></label></div><div class="clearfix"></div>';
                });
            } else {
                stopList += '<div> ----- </div>';
            }
            $('#stopCount').html('<div class="checkbox">' + stopList + '</div>');
        }

        function loadAirlineFilter(airlineList)
        {
            var imgPath = '<?php echo SYSTEM_IMAGE_DIR; ?>airline_logo/';
            var filterList = '';
            if ($('.a-n').length > 0) {
                $.each(airlineList, function (key, value) {
                    filterList += '<label><div><input type="checkbox" class="airlinecheckbox"	value="' + key + '" checked="checked"><img src="' + imgPath + key + '.gif" width="20px" height="15px"><span>' + value + '</span></div></label><div class="clearfix"></div>';
                });
            } else {
                filterList += '<div> ----- </div>';
            }
            $('#allairlines').html('<div class="checkbox">' + filterList + '</div>');
        }

        $(document).on('click', 'input.stopcountcheckbox, input.airlinecheckbox', function () {
            var visibility = '';
            if (this.checked == true) {
                visibility = ':hidden';
            } else {
                visibility = ':visible';
            }
            filter_row_origin_marker(visibility);
        });

        function cloneSlider(slides)￼Reset Filters
                All Airline
                ￼
                ￼
                ￼
        ￼
        Airline
        Depart
        Arrive
        Duration
                Price
        ￼
                Showing: ￼... of ... Flights
        {
            var cloneList = {};
            var list = '';
            $.each(slides, function (key, value) {
                cloneList[(value + 1)] = value + 1;
            });
            $.each(cloneList, function (k, v) {
                list += '<div class="item carousel-booking">';
                list += '<div class="panel panel-default b-r-0 m-0">';
                list += '<div class="panel-body">';
                list += '<div class="media">';
                list += '<div class="media-left">';
                list += '<a href="#">';
                list += '<img alt="Flight Image" src="' + $('#flight_search_result .r-r-i:nth-child(' + k + ') .airline-logo:first').attr('src') + '" height="15">';
                list += '</a>';
                list += '</div>';
                list += '<div class="media-body">';
                list += '<h4 class="media-heading text-t h6">' + $('#flight_search_result .r-r-i:nth-child(' + k + ') .a-n:first').text() + '</h4>';
                list += '<span class="h5"> ' + $('#flight_search_result .r-r-i:nth-child(' + k + ') .f-p:first').text() + '</span>';
                list += '</div>';
                list += '<div class="cloneForm" style="display:none;">';
                list += $('#flight_search_result .r-r-i:nth-child(' + k + ') .form-wrapper').html();
                list += '</div>';
                list += '</div>';
                list += '</div>';
                list += '</div>';
                list += '</div>';
            });

            $(".owl-carousel").html(list);
            carousel();
        }

        $(document).on('click', '.carousel-booking', function (e) {
            e.preventDefault();
            $('.book-form-wrapper', this).submit();
        });

        function carousel() {
            $(".owl-carousel").owlCarousel({
                items: 5,
                itemsDesktop: [1000, 1],
                itemsDesktopSmall: [900, 1],
                itemsTablet: [600, 1],
                itemsMobile: [479, 1],
                navigation: false,
                autoHeight: true,
                autoPlay: true,
                autoplayTimeout: 3000,
                autoplayHoverPause: true,
                dots: false
            });
        }

        function loader(selector)
        {
            selector = selector || '#flight_search_result';
            $(selector).animate({'opacity': '.1'});
            setTimeout(function () {
                $(selector).animate({'opacity': '1'}, 'slow');
            }, 1000);
        }

        function filter_row_origin_marker(visibility)
        {
            loader();
            visibility = visibility || '';
            //get all the search criteria
            var stopCountList = $('input.stopcountcheckbox:checked', '#stopCount').map(function () {
                return parseInt(this.value);
            }).get()
            var airlineList = $('input.airlinecheckbox:checked', '#allairlines').map(function () {
                return this.value;
            }).get();
            var min_price = parseFloat($("#slider-range").slider("values")[0]);
            var max_price = parseFloat($("#slider-range").slider("values")[1]);
            var min_duration = $("#slider-range1").slider("values")[0];
            var max_duration = $("#slider-range1").slider("values")[1];
            $('.r-r-i' + visibility).each(function (key, value) {
                if ($.inArray($('.a-n:first', this).data('code'), airlineList) != -1 &&
                        $.inArray(parseInt($('.stp:first', this).data('stp')), stopCountList) != -1 &&
                        ($('.price:first', this).data('price') >= min_price && $('.price:first', this).data('price') <= max_price) &&
                        ($('.dep_dt:first', this).data('datetime') >= min_duration && $('.dep_dt:first', this).data('datetime') <= max_duration)
                        ) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
            update_total_count_summary();
        }

        /**
         *Update Hotel Count Details
         */
        function update_total_count_summary()
        {
            $('#flight_search_result').show();
            var _visible_records = parseInt($('.r-r-i:visible').length);
            var _total_records = $('.r-r-i').length;
            if (isNaN(_visible_records) == true || _visible_records == 0) {
                _visible_records = 0;
                //display warning
                $('#flight_search_result').hide();
                $('#empty_flight_search_result').show();
            } else {
                $('#flight_search_result').show();
                $('#empty_flight_search_result').hide();
            }
            $('#total_records').text(_visible_records);
            $('.visible-row-record-count').text(_visible_records);
            $('.total-row-record-count').text(_total_records);
        }

        function unique_array_values(array_values)
        {
            var _unique_array_values = [];
            $.each(array_values, function (k, v) {
                if (_unique_array_values.indexOf(v) == -1) {
                    _unique_array_values.push(v);
                }
            });
            return _unique_array_values;
        }

        //airline price sort
        $(".price-l-2-h").click(function () {
            loader();
            $(this).addClass('hide');
            $('.price-h-2-l').removeClass('hide');
            $("#flight_search_result .r-w-g").jSort({sort_by: '.f-p:first', item: '.r-r-i', order: 'asc', is_num: true});
        });

        $(".price-h-2-l").click(function () {
            $(this).addClass('hide');
            $('.price-l-2-h').removeClass('hide');
            $("#flight_search_result .r-w-g").jSort({sort_by: '.f-p:first', item: '.r-r-i', order: 'desc', is_num: true});
        });

        //airline name sort
        $(".name-l-2-h").click(function () {
            $(this).addClass('hide');
            $('.name-h-2-l').removeClass('hide');
            $("#flight_search_result .r-w-g").jSort({sort_by: '.a-n:first', item: '.r-r-i', order: 'asc', is_num: false});
        });

        $(".name-h-2-l").click(function () {
            $(this).addClass('hide');
            $('.name-l-2-h').removeClass('hide');
            $("#flight_search_result .r-w-g").jSort({sort_by: '.a-n:first', item: '.r-r-i', order: 'desc', is_num: false});
        });


        //duration sort
        $(".duration-l-2-h").click(function () {
            $(this).addClass('hide');
            $('.duration-h-2-l').removeClass('hide');
            $("#flight_search_result .r-w-g").jSort({sort_by: '.f-d:first', item: '.r-r-i', order: 'asc', is_num: true});
        });

        $(".duration-h-2-l").click(function () {
            $(this).addClass('hide');
            $('.duration-l-2-h').removeClass('hide');
            $("#flight_search_result .r-w-g").jSort({sort_by: '.f-d:first', item: '.r-r-i', order: 'desc', is_num: true});
        });

        //departure name sort
        $(".departure-l-2-h").click(function () {
            $(this).addClass('hide');
            $('.departure-h-2-l').removeClass('hide');
            $("#flight_search_result .r-w-g").jSort({sort_by: '.f-d-t:first', item: '.r-r-i', order: 'asc', is_num: true});
        });

        $(".departure-h-2-l").click(function () {
            $(this).addClass('hide');
            $('.departure-l-2-h').removeClass('hide');
            $("#flight_search_result .r-w-g").jSort({sort_by: '.f-d-t:first', item: '.r-r-i', order: 'desc', is_num: true});
        });


        //arrival sort
        $(".arrival-l-2-h").click(function () {
            $(this).addClass('hide');
            $('.arrival-h-2-l').removeClass('hide');
            $("#flight_search_result .r-w-g").jSort({sort_by: '.f-a-t:first', item: '.r-r-i', order: 'asc', is_num: true});
        });

        $(".arrival-h-2-l").click(function () {
            $(this).addClass('hide');
            $('.arrival-l-2-h').removeClass('hide');
            $("#flight_search_result .r-w-g").jSort({sort_by: '.f-a-t:first', item: '.r-r-i', order: 'desc', is_num: true});
        });

        $('.loader').on('click', function (e) {
            e.preventDefault();
            loader();
        });
        $(document).on('click', '.reset-page-loader', function (e) {
            e.preventDefault();
            loader();
            location.reload();
        });

        //Handle col2x selector
        $(document).on('click', '.mfb-btn', function (e) {
            e.preventDefault();
            loader('#multi-flight-summary-container');
            loader();
            update_col2x_flight($(this).closest('.r-r-i'), $(this).closest('.r-w-g').attr('id'));
            $('#multi-flight-summary-container').effect('bounce', 'slow');
        });

        /**
         *Update Selected flight details and highlight selected flight
         */
        var default_currency = "<?php echo $this->currency->get_currency_symbol($to_currency); ?>";
        function update_col2x_flight(segment, trip_way_indicator)
        {
            $(segment).closest('.r-w-g').find('.r-r-i.active-selected').removeClass('active-selected bg-info');
            $(segment).addClass('active-selected bg-info');
            //update flight details
            var _flight_icon = $('.airline-logo:first', segment).attr('src');
            var _flight_name = $('.a-n:first', segment).text();
            var _flight_from_price = $('#flight-from-price').val();
            var _flight_to_price = $('#flight-to-price').val();
            var _location_details = $('.from-loc:first', segment).text() + ' <?php echo $flight_o_direction_icon ?> ' + $('.to-loc:first', segment).text();
            var _departure = $('.f-d-t:first', segment).text();
            var _arrival = $('.f-a-t:first', segment).text();
            if (trip_way_indicator == 't-w-i-1') {
                //t-w-i-1
                $('#multi-flight-summary-container .departure-flight-icon').attr('src', _flight_icon);
                $('#multi-flight-summary-container .departure-flight-name').text(_flight_name);
                $('#multi-flight-summary-container .outbound-details').html(_location_details);
                _flight_from_price = $('.f-p:first', segment).text();
                $('#multi-flight-summary-container .outbound-timing-details .departure').text(_departure);
                $('#multi-flight-summary-container .outbound-timing-details .arrival').text(_arrival);
            } else if (trip_way_indicator == 't-w-i-2') {
                //t-w-i-2
                $('#multi-flight-summary-container .arrival-flight-icon').attr('src', _flight_icon);
                $('#multi-flight-summary-container .arrival-flight-name').text(_flight_name);
                $('#multi-flight-summary-container .inbound-details').html(_location_details);
                $('#multi-flight-summary-container .inbound-timing-details .departure').text(_departure);
                $('#multi-flight-summary-container .inbound-timing-details .arrival').text(_arrival);
                _flight_to_price = $('.f-p:first', segment).text();
            }
            //update flight-price
            $('#flight-from-price').val(_flight_from_price);
            $('#flight-to-price').val(_flight_to_price);
            $('#multi-flight-summary-container .f-p').text((parseFloat(_flight_from_price) + parseFloat(_flight_to_price)).toFixed(2));
            $('#multi-flight-summary-container .currency').text(default_currency);
        }

        /**
         *Get Booking Form Contents
         */
        function get_booking_form_contents()
        {
            //run ajax and get update
            var _trip_way_1 = $('#t-w-i-1 .r-r-i.active-selected:first form.book-form-wrapper').serializeArray();
            var _trip_way_2 = $('#t-w-i-2 .r-r-i.active-selected:first form.book-form-wrapper').serializeArray();
            if (jQuery.isEmptyObject(_trip_way_1) == false && jQuery.isEmptyObject(_trip_way_2) == false) {
                return get_combined_booking_from(JSON.stringify(_trip_way_1), JSON.stringify(_trip_way_2));
            } else {
                location.reload();
            }
        }

        /**
         *Combined booking form to be loaded via Ajax
         */
        function get_combined_booking_from(trip_way_1, trip_way_2)
        {
            var _result = {};
            var params = {trip_way_1: trip_way_1, trip_way_2: trip_way_2, search_id:<?php echo $flight_search_params['search_id'] ?>};
            $.ajax({
                type: 'POST',
                url: app_base_url + 'index.php/ajax/get_combined_booking_from',
                async: false,
                cache: false,
                data: $.param(params),
                dataType: 'json',
                success: function (result) {
                    if (result.status == true) {
                        _result = result.data;
                    } else {
                        location.reload();
                    }
                }
            });
            return _result;
        }

        /**
         *update default first row as selected
         */
        function default_col2x()
        {
            var col_count = $('.r-w-g').length;
            if (parseInt(col_count) == 2) {
                $('#clone-list-container').addClass('hide');
                $('#multi-flight-summary-container').removeClass('hide');
                update_col2x_flight($('#t-w-i-1 .r-r-i:first'), 't-w-i-1');
                update_col2x_flight($('#t-w-i-2 .r-r-i:first'), 't-w-i-2');
            }
        }

        /**
         *Create Booking Form on click on book button
         */
        $(document).on('click', '#multi-flight-booking-btn', function (e) {
            e.preventDefault();
            //update booking form
            $(this).attr('disabled', true);
            loader('body');
            var _form_contents = get_booking_form_contents();
            $('#trip-way-wrapper').empty().html(_form_contents['form_content']);
            $('#multi-flight-form').attr('action', _form_contents['booking_url']);
            //submit form
            $('#multi-flight-form').submit();
            $(this).removeAttr('disabled');
        });
    });
</script>
<?php
$this->load->view('flight/flight_search');
?>
