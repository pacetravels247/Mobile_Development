<?php
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/transfer_suggest_modify_search.js'), 'defer' => 'defer');

if ((isset($transfer_search_params) == false) || (isset($transfer_search_params) == true && valid_array($transfer_search_params) == false)) {
    $sparam = $this->input->cookie('sparam', TRUE);

    $sparam = unserialize($sparam);

    $sid = intval(@$sparam[META_TRANSFERS_COURSE]);
}


if (!isset($transfer_search_params['adult']) && empty($transfer_search_params['adult'])) {
    $transfer_search_params['adult'] = 1;
}

$transfer_datepicker = array(array('transfer_datepicker1', FUTURE_DATE_TIME), array('transfer_datepicker2', FUTURE_DATE_TIME));
$GLOBALS['CI']->current_page->set_datepicker($transfer_datepicker);
$GLOBALS['CI']->current_page->auto_adjust_datetimepicker(array(array('transfer_datepicker1', 'transfer_datepicker2')));
?>
<form id="trasfer" name="trasfer" autocomplete="off" action="<?= base_url() ?>index.php/general/pre_transfer_search">
    <div class="intabs">
        <div class="waywy" style="margin-top: 10px;">
            <div class="smalway">
                <label class="wament hand-cursor">
                    <input class="hide" type="radio" name="transfer_type" <?= (isset($transfer_search_params['trip_type']) == false ? 'checked' : ($transfer_search_params['trip_type']) == 'oneway' ? 'checked="checked"' : '') ?> id="onew-trip" value="oneway" /> One-Way
                </label>
                <label class="wament hand-cursor">
                    <input class="hide" type="radio" name="transfer_type" <?= (@$transfer_search_params['trip_type'] == 'circle' ? 'checked="checked"' : '') ?> id="rnd-trip" value="circle" /> Roundtrip
                </label>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="outsideserach">
            <div class="clearfix"></div>
            <div class="col-md-5 col-xs-12 nopad padfive marginbotom10 aftremarg">
                <div class="col-lg-6 col-md-6 col-sm-6 fiveh padfive">
                    <div class="marginbotom10">
                        <span class="formlabel">From</span>
                        <div class="relativemask plcemark"> 
                            <span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
                            <input type="text" value="<?php echo @$transfer_search_params['from'] ?>" placeholder="From, Airport, Hotel, City" name="transfer_from" id="transfer_from" class="normalinput b-r-0 fromtransfer ui-autocomplete-input" required="" aria-required="true" autocomplete="off">
                            <input class="hide loc_id_holder" name="from_loc_id" type="hidden" value="<?= @$transfer_search_params['from_code'] ?>" >
                            <input class="hide transfer_type" name="from_transfer_type" type="hidden" value="<?= @$transfer_search_params['from_transfer_type'] ?>" >

                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 fiveh padfive">
                    <div class="marginbotom10">
                        <span class="formlabel">To</span>
                        <div class="relativemask plcemark"> <span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
                            <input type="text" value="<?php echo @$transfer_search_params['to'] ?>" placeholder="To, Airport, Hotel, City" id="transfer_to" name="transfer_to" class="normalinput b-r-0 departtransfer ui-autocomplete-input" required="" aria-required="true" autocomplete="off">
                            <input class="hide loc_id_holder" name="to_loc_id" type="hidden" value="<?= @$transfer_search_params['to_code'] ?>" >
                            <input class="hide transfer_type" name="to_transfer_type" type="hidden" value="<?= @$transfer_search_params['to_transfer_type'] ?>"> 					
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-md-3 col-xs-12 padfive nopad">
                <div class="col-xs-6 padfive fiveh">

                    <span class="formlabel">Departure</span>
                    <div class="relativemask datemark"> 
                        <input type="text" placeholder="Depature Date" required="required"
                               value="<?php echo @$transfer_search_params['from_date'] ?>" 
                               class="forminput b-r-0 normalinput date_picker" id="transfer_datepicker1" 
                               name="depature" aria-required="true" autocomplete="off">
                    </div>

                </div>
                <div class="col-xs-6 padfive fiveh date-wrapper">

                    <span class="formlabel">Return</span>
                    <div class="relativemask datemark"> 
                        <input type="text"  readonly placeholder="Return Date" 
                               value="<?php echo @$transfer_search_params['to_date'] ?>" 
                               class="forminput normalinput b-r-0 date_picker" 
                               id="transfer_datepicker2" name="return"
                               <?= (@$transfer_search_params['transfer_type'] != 'circle' ? 'disabled="disabled"' : '') ?> autocomplete="off" aria-required="true" >
                               <!-- <input type="text" value="<?php echo @$transfer_search_params['to'] ?>" placeholder="To, Airport, Hotel, City" id="transfer_to" name="transfer_to" class="ft departtransfer ui-autocomplete-input" required="" aria-required="true" autocomplete="off">-->
                    </div>

                </div>
            </div>


            <div class="col-md-4 col-xs-12 padfive nopad">

                <div class="col-xs-7 padfive mobile_width">
                    <div class="formlabel">&nbsp;</div>
                    <div class="totlall">
                        <span class="remngwd"><span class="total_pax_count"></span> <span id="travel_text">Traveller</span></span>
                        <div class="roomcount pax_count_div">
                            <div class="mobile_adult_icon">Travelers<i class="fa fa-male"></i></div>

                            <div class="inallsn">
                                <div class="oneroom fltravlr">
                                    <div class="lablform2">Travelers</div>
                                    <div class="clearfix"></div>
                                    <div class="roomrow">

                                        <div class="celroe col-xs-7"><i class="fal fa-male"></i> Adults
                                            <span class="agemns">(12+)</span>
                                        </div>


                                        <div class="celroe col-xs-5">
                                            <div class="selectedwrapnum">
                                                <div class="onlynumwrap wrap1 pax-count-wrapper">
                                                    <div class="onlynum">

                                                        <button data-field="adult" data-type="minus"  class="btn btn-default btn-number btnpot minusValue adult" type="button"> 
                                                            <span class="glyphicon glyphicon-minus"></span> </button>
                                                        <input type="text" id="OWT_transfer_adult" name="adult" class="form-control input-number centertext valid_class pax_count_value" value="<?= (int) @$transfer_search_params['adult'] ?>" min="1" max="6" readonly>

                                                        <button data-field="adult" data-type="plus" class="btn btn-default btn-number btnpot btn_right plusValue" type="button"> 
                                                            <span class="glyphicon glyphicon-plus"></span> </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="col-xs-12 nopad padfive">
                                            <div class="formlabel11 padfive_child">Adult age at time of travel</div>
                                            <?php
                                            for ($d = 1; $d <= 6; $d++) {
                                                $classname = 'hide';
                                                $disable = 'disabled=""';
                                                if ($d == 1 || isset($transfer_search_params['adult_ages'][$d - 1])) {
                                                    $classname = '';
                                                    $disable = '';
                                                }
                                                ?>
                                                <div class="col-xs-6 fiveh padfive adult-ages transfer_adult_ageId<?= $d ?> <?= $classname ?>">

                                                    <div class="plcetogo selctmarksml">
                                                        <select class="normalsel padselctsmal marginbotom10 selectpicker <?= $classname ?>" <?= $disable ?> id="transfer_adult_ageId<?= $d ?>" name="adult_ages[]"><?php
                                                            for ($i = 12; $i <= 100; $i++) {
                                                                if (isset($transfer_search_params['adult_ages'][$d - 1]) && $transfer_search_params['adult_ages'][$d - 1] == $i) {
                                                                    $selected = "selected";
                                                                } else {
                                                                    $selected = '';
                                                                }
                                                                ?><option <?= $selected ?>><?= $i ?></option><?php
                                                            }
                                                            ?></select>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>

                                    </div>




                                    <div class="roomrow">
                                        <div class="celroe col-xs-7"><i class="fal fa-child"></i> Children
                                            <span class="agemns">(2-11)</span>
                                        </div>

                                        <div class="celroe col-xs-5">
                                            <div class="selectedwrapnum">
                                                <div class="onlynumwrap wrap1">
                                                    <div id="childs" class="onlynum pax-count-wrapper">
                                                        <button type="button" class="btn btn-default btn-number btnpot minusValue child" data-type="minus" data-field="child"> <span class="glyphicon glyphicon-minus"></span> </button>
                                                        <input type="text" id="OWT_transfer_child" name="child" class="form-control input-number centertext pax_count_value" value="<?= (int) @$transfer_search_params['child'] ?>" min="0" max="6" readonly>
                                                        <button type="button" class="btn btn-default btn-number btnpot btn_right plusValue" data-field="child" data-type="plus"> <span class="glyphicon glyphicon-plus"></span>  </button>
                                                    </div>
                                                </div>
                                            </div> 
                                        </div>

                                        <div class="col-xs-12 nopad pull-right padfive">
                                            <div class="formlabel11 padfive_child">Child age at time of travel</div>
                                            <?php
                                            for ($c = 1; $c <= 6; $c++) {
                                                if (isset($transfer_search_params['child_ages'][$c - 1])) {
                                                    $classname = '';
                                                    $disable = '';
                                                } else {
                                                    $disable = 'disabled=""';
                                                    $classname = "hide";
                                                }
                                                ?>
                                                <div class="col-xs-6 fiveh padfive child-ages transfer_child_ageId<?= $c ?> <?= $classname ?>">

                                                    <div class="plcetogo selctmarksml">
                                                        <select class="normalsel padselctsmal marginbotom10 selectpicker <?= $classname ?>" <?= $disable ?> id="transfer_child_ageId<?= $c ?>" name="child_ages[]"><?php
                                                            for ($j = 2; $j <= 11; $j++) {
                                                                if (isset($transfer_search_params['child_ages'][$c - 1]) && $transfer_search_params['child_ages'][$c - 1] == $j) {
                                                                    $selected = "selected";
                                                                } else {
                                                                    $selected = '';
                                                                }
                                                                ?><option <?= $selected ?>><?= $j ?></option><?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div> 

                                    </div>


                                    <a class="done1 comnbtn_room1"><span class="fa fa-check"></span> Done</a>
                                    <!-- Infant Error Message-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xs-5 fiveh padfive pull-right">
                    <span class="formlabel">&nbsp;</span>
                    <div class="searchsbmtfot marginbot10">
                        <button class="searchsbmt" type="submit">Search<span class="srcharow"></span></button>
                    </div>
                </div>

            </div>
            <div class="minVal_error_child_adult col-md-6" style="color:red;font-size:14px;font-weight:bold;"></div>		
            <div class="clearfix"></div>

        </div>
    </div>
</form>