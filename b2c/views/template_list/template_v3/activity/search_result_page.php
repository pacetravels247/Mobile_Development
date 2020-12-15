<?php
echo $GLOBALS['CI']->template->isolated_view('activity/search_panel_summary');
?>
<script>
    var load_transfer = function (loader, offset, filters) {
        offset = offset || 0;
        var url_filters = '';
        if ($.isEmptyObject(filters) == false) {
            url_filters = '&' + ($.param({'filters': filters}));
        }
        _lazy_content = $.ajax({
            type: 'GET',
            url: app_base_url + 'index.php/ajax/transfer_list/' + offset + '?booking_source=<?= $active_booking_source[0]['source_id'] ?>&search_id=<?= $hotel_search_params['search_id'] ?>&op=load' + url_filters,
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
    load_transfer(interval_load);
</script>
<div class="clearfix"></div>

<section class="search-result onlyfrflty">
    <div class="container"  id="page-parent">
        <div class="resultalls">
            <div class="coleft">
                <div class="flteboxwrp">
                    <div class="filtersho">
                        <div class="avlhtls"><strong id="total_records">15</strong> <span id="flights_text">Activities</span> found
                        </div>
                        <span class="close_fil_box"><i class="fa fa-close"></i></span>
                    </div>
                    <!-- Refine Search Filters Start -->
                    <div class="fltrboxin">
                        <div class="celsrch">
                            <div class="row">
                                <a class="pull-right" id="reset_filters">RESET ALL</a>
                            </div>
                            <div class="rangebox">
                                <button data-target="#collapse501" data-toggle="collapse" class="collapsebtn" type="button">
                                    Price
                                </button>
                                <div id="collapse501" class="in">
                                    <div class="price_slider1">
                                        <div id="core_min_max_slider_values" class="hide">
                                            <input type="hiden" id="core_minimum_range_value" value="">
                                            <input type="hiden" id="core_maximum_range_value" value="">
                                        </div>
                                        <p id="amount" class="level"></p>
                                        <div id="slider-range" class="" aria-disabled="false"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="septor"></div>

                            <div class="rangebox">
                                <button data-target="#collapse503" data-toggle="collapse" class="collapsebtn" type="button">
                                    Duration
                                </button>
                                <div id="collapse503" class="collapse in">
                                    <div class="boxins marret" id="departureTimeWrapper">
                                        <a class="timone toglefil time-wrapper">

                                            <div class="starin">
                                                <div class="tmxdv">
                                                    <input type="checkbox" class="time-category hidecheck" value="1">
                                                    <label class="ckboxdv">Half-day afternoon</label>
                                                </div>

                                            </div>

                                        </a>
                                        <a class="timone toglefil time-wrapper">

                                            <div class="starin">
                                                <div class="tmxdv">
                                                    <input type="checkbox" class="time-category hidecheck" value="2">
                                                    <label class="ckboxdv">Multi-day</label>
                                                </div>	

                                            </div>

                                        </a>
                                        <a class="timone toglefil time-wrapper">

                                            <div class="starin">
                                                <div class="tmxdv">
                                                    <input type="checkbox" class="time-category hidecheck" value="3">
                                                    <label class="ckboxdv">Full day</label>
                                                </div>

                                            </div>
                                        </a>
                                        <a class="timone toglefil time-wrapper">
                                            <div class="starin">
                                                <div class="tmxdv">
                                                    <input type="checkbox" class="hidecheck time-category" value="4">
                                                    <label class="ckboxdv">Half-day morning</label>
                                                </div>


                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="septor"></div>
                            <div class="rangebox">
                                <button data-target="#collapse504" data-toggle="collapse" class="collapsebtn" type="button">
                                    Activities
                                </button>
                                <div id="collapse504" class="collapse in">
                                    <div class="boxins marret" id="arrivalTimeWrapper">
                                        <a class="timone toglefil time-wrapper">
                                            <div class="tmxdv">
                                                <input type="checkbox" class="time-category hidecheck" value="1">
                                                <label class="ckboxdv">Theme and water parks</label>
                                            </div>

                                        </a>
                                        <a class="timone toglefil time-wrapper">
                                            <div class="tmxdv">
                                                <input type="checkbox" class="time-category hidecheck" value="2">
                                                <label class="ckboxdv">Cruises and water sports</label>
                                            </div>

                                        </a>

                                    </div>
                                </div>
                            </div>

                            <!-- <div class="septor"></div> -->
                        </div>
                    </div>
                </div>
                <!-- Refine Search Filters End -->
            </div>

            <div class="colrit">
                <div class="insidebosc">
                    
                    <?php for($i=1;$i<=8;$i++)  { ?>
                    
                    <div class="m-media">
                        <div class="m-media__container">
                            <div class="m-media__headline">
                                <div class="col-xs-3 nopad listimage full_mobile">
                                    <div class="m-media_headlineimage1">
                                        <img src="https://d2u09083uyrwez.cloudfront.net/api/file/PomT5cE3QYy1IDUq9iYS/convert?w=1500" class="m-media__image" alt="">
                                    </div>

                                </div>

                                <div class="col-xs-9 nopad listfull full_mobile">
                                    <div class="sidenamedesc">
                                        <div class="celhtl width70">
                                            <div class="m-media__headline-item">
                                                <span class="m-media__title">Thames River Champagne Sunset Cruise</span>
                                            </div>

                                            <div class="m-media__headline-item">
                                              
                                                    <div class="starrtinghotl rating-no">
                                                    <span class="h-sr hide">5</span>
                                                    <span class="star 1 active"></span>
                                                    <span class="star 2 "></span>
                                                    <span class="star 3 "></span>
                                                    <span class="star 4 "></span>
                                                    <span class="star 5 "></span>
                                                    </div>
                                               
                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="hotel_address elipsetool"> <span class="fal fa-calendar"></span> <span class="day">Full day</span></div>

                                            <div class="side_amnties1">
	                                            <ul class="hotel_fac1">
	                                               <li>Outdoor activities and adventure</li>                                
	                                            </ul>
                                            </div>

                                        </div>

                                        <div class="celhtl width30 transferprice">
                                            <div class="m-media__price">

                                                <div class="a-price">
                                                    <span class="currency a-price__price1" id="span_total_price_144227" rel="177.9932">177.99€</span>
                                                </div>

                                                <div class="a-button--primary">
                                                <a type="button" class="a-button__container1" title="Book Now">
                                                    <span class="a-button__text a-button__text--inherit">Details</span>
                                                </a>
                                               </div>
                                            
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php } ?>
                  



                </div>
            </div>
        </div>
    </div>
</section>			


