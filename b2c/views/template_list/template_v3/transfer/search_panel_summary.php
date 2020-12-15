<style>
    .topssec::after{display:none;}
</style>
<div class="modfictions">
    <div class="modinew">
        <div class="container">
            <div class="contentsdw">
                <div class="smldescrptn">
                    <div class="col-sm-9 col-xs-10 nopad">
                        <div class="col-xs-6 boxpad none_boil_full">
                            <h4 class="contryname">Round Trip</h4>
                            <h3 class="placenameflt">Dubai International Airport</h3>
                        </div>

                        <div class="col-xs-3 boxpad none_boil">
                            <div class="boxlabl"><span class="faldate fa fa-calendar-o"></span>Departure</div>
                            <div class="datein">
                                <span class="calinn">14-03-2018</span>
                            </div>
                        </div>

                        <div class="col-xs-3 boxpad none_boil">

                            <div class="boxlabl"><span class="faldate fa fa-calendar-o"></span>Return</div>

                            <div class="datein">
                                <span class="calinn">
                                    14-03-2018
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3 col-xs-2 nopad">
                        <div class="col-xs-7 boxpad none_mody">
                            <div class="boxlabl">Passenger</div>
                            <div class="countlbl" style="text-align: left;">3</div>
                        </div>
                        <div class="col-xs-5 boxpad pull-right">
                            <a class="modifysrch" data-toggle="collapse" data-target="#modify"><span class="mdyfydsktp">Modify Search</span>
                                <i class="fa fa-angle-down mobresdv" aria-hidden="true"></i>
                            </a>

                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="splmodify">
        <div class="container">
            <div id="modify" class="collapse araeinner">
                <div class="insplarea">
                    <?php echo $GLOBALS['CI']->template->isolated_view('share/transfer_search') ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('.modifysrch').click(function () {
            $(this).stop(true, true).toggleClass('up');
            $('.search-result').stop(true, true).toggleClass('flightresltpage');
            $('.modfictions').stop(true, true).toggleClass('fixd');
        });
    });
</script>