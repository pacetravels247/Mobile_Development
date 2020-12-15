<?php
echo $GLOBALS['CI']->template->isolated_view('transfer/search_panel_summary');
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
                        <div class="avlhtls"><strong id="total_records"> </strong> <span id="flights_text">Transfers</span> found
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
                                    Car Type
                                </button>
                                <div id="collapse503" class="collapse in">
                                    <div class="boxins marret" id="departureTimeWrapper">
                                        <a class="timone toglefil time-wrapper">

                                            <div class="starin">
                                                <div class="tmxdv">
                                                    <input type="checkbox" class="time-category hidecheck" value="1">
                                                    <label class="ckboxdv">Early Morning</label>
                                                </div>

                                            </div>

                                        </a>
                                        <a class="timone toglefil time-wrapper">

                                            <div class="starin">
                                                <div class="tmxdv">
                                                    <input type="checkbox" class="time-category hidecheck" value="2">
                                                    <label class="ckboxdv">Van</label>
                                                </div>	

                                            </div>

                                        </a>
                                        <a class="timone toglefil time-wrapper">

                                            <div class="starin">
                                                <div class="tmxdv">
                                                    <input type="checkbox" class="time-category hidecheck" value="3">
                                                    <label class="ckboxdv">Car</label>
                                                </div>

                                            </div>
                                        </a>
                                        <a class="timone toglefil time-wrapper">
                                            <div class="starin">
                                                <div class="tmxdv">
                                                    <input type="checkbox" class="hidecheck time-category" value="4">
                                                    <label class="ckboxdv">Minivan</label>
                                                </div>


                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="septor"></div>
                            <div class="rangebox">
                                <button data-target="#collapse504" data-toggle="collapse" class="collapsebtn" type="button">
                                    car Name
                                </button>
                                <div id="collapse504" class="collapse in">
                                    <div class="boxins marret" id="arrivalTimeWrapper">
                                        <a class="timone toglefil time-wrapper">
                                            <div class="tmxdv">
                                                <input type="checkbox" class="time-category hidecheck" value="1">
                                                <label class="ckboxdv">Private</label>
                                            </div>

                                        </a>
                                        <a class="timone toglefil time-wrapper">
                                            <div class="tmxdv">
                                                <input type="checkbox" class="time-category hidecheck" value="2">
                                                <label class="ckboxdv">BMW</label>
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

                    <?php for ($i = 1; $i <= 8; $i++) { ?>

                        <div class="m-media">
                            <div class="m-media__container">
                                <div class="m-media__headline">
                                    <div class="col-xs-3 nopad listimage full_mobile">
                                        <div class="m-media_headlineimage">
                                            <img src="//cdn-legacy.suntransfers.com/assets/images/vehicles/tx_thumb-d46b9419fe46851803f2498410d60ae3.png" class="m-media__image" alt="">
                                        </div>

                                    </div>

                                    <div class="col-xs-9 nopad listfull full_mobile">
                                        <div class="sidenamedesc">
                                            <div class="celhtl width70">
                                                <div class="m-media__headline-item">
                                                    <span class="m-media__title">Private Taxi</span>
                                                </div>

                                                <div class="m-media__headline-item">
                                                    <div class="m-media__list">
                                                        <div class="m-media__list-item">
                                                            <span class="a-text-highlight">
                                                                <span class="a-text_list"> <i class="fa fa-users"></i>3 passengers</span>
                                                            </span>
                                                        </div>

                                                        <div class="m-media__list-item">
                                                            <span class="a-text-highlight">
                                                                <span class="a-text_list"><i class="fa fa-suitcase"></i>3 medium suitcases</span>
                                                            </span>
                                                        </div>

                                                        <div class="m-media__list-item">
                                                            <span class="a-text-highlight">
                                                                <span class="a-text_list"><i class="fa fa-suitcase"></i> Door to Door</span>
                                                            </span>
                                                        </div>

                                                        <div class="m-media__list-item">
                                                            <span class="a-text-highlight">
                                                                <span class="a-text_list"><i class="fa fa-clock-o"></i>1 Hour 45 Mins</span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="celhtl width30 transferprice">
                                                <div class="m-media__price">

                                                    <div class="a-price">
                                                        <span class="currency a-price__price" id="span_total_price_144227" rel="177.9932">177.99€</span>
                                                        <span class="a-price__description">Return price</span>
                                                    </div>
                                                    <div class="a-textfree">
                                                        <span class="a-text-highlight__price">FREE cancellation </span>
                                                    </div>

                                                    <div class="a-text-highlight--clean">
                                                        <span class="a-text-highlight__text">Zero fees</span>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <div class="m-media__content">
                                    <div class="m-media__menu">
                                        <div class="m-media__highlights">
                                            <div class="m-info-display__item">
                                                <span class="m-info-display__text"><i class="fa fa-thumbs-up"></i> Meet and greet</span>
                                            </div>

                                            <div class="m-info-display__item">
                                                <span class="m-info-display__text"><i class="fa fa-thumbs-up"></i> Exclusive ride for you</span>
                                            </div>
                                        </div>

                                        <div class="m-media__cta">
                                            <a class="m-media__cta-item"  data-toggle="collapse" href="#transfer_details">
                                                <span class="m-media__text">more info <i class="fa fa-angle-down"></i></span>
                                                <span class="m-media__icon"></span></a>

                                            <div class="m-media__right pull-right">
                                                <div class="a-button--primary">
                                                    <a type="button" class="a-button__container" title="Book Now">
                                                        <span class="a-button__text a-button__text--inherit">Select this vehicle</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="transfer_details" class="collapse">
                                        <div class="col-xs-12 nopad">                                
                                            <div class="sidenamedesc">                                                                                         
                                                <div class="col-sm-12 col-xs-12 nopad">                                        
                                                    <div class="car_sideprice">                                                                               
                                                        <span class="inclusions">General Info List</span>                                            
                                                        <ul class="hondycar">                 
                                                            <li>Exclusive ride for you</li>                                                                                                                
                                                            <li>Door to door service</li>                                                                                                                
                                                            <li>Available 24/7</li>                                                                                                                
                                                            <li>Meet &amp; Greet service</li>                                                                                                                
                                                            <li>1 item of hand baggage allowed per person</li>                                                                                                                
                                                            <li>1 piece of baggage allowed per person ( max.dimensions 158cm) length+width+height=158cm</li>                                                                                                    
                                                        </ul>                                        
                                                    </div>                                    
                                                </div>                                
                                            </div>  

                                            <div class="clearfix"></div>    

                                            <div class="rentcondition">                                    
                                                <span class="conhead">Pickup Information</span>                                    
                                                <p>Once you have collected your luggage, a staff member will be waiting for you at the Arrivals Gate with a sign with your name on it. If you are unable to locate the driver/agent, please call LE PASSAGE INDIA on +91 9999846900 .Languages spoken at the call centre: English. Please do not leave the pick-up area without having contacted the agent/driver first.</p>                                    
                                                <span class="conhead">Guidelines List</span>                                                    
                                                <span class="hotel_address elipsetool">VOUCHER </span>                                            
                                                <p>Remember to bring this voucher and valid photo ID with you</p>                                                            
                                                <span class="hotel_address elipsetool">CHILDBOOSTER / BABY SEAT</span>                                            
                                                <p>Child car seats and boosters are not included unless specified in your booking and can carry an extra cost. Should you need to book them, please contact your point of sale prior to travelling.</p>                                                
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


