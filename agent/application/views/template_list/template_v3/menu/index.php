<?php
$active_domain_modules = $this->active_domain_modules;
$default_active_tab = $default_view;

// echo "<pre>"; print_r($holiday_data);exit;

/**
 * set default active tab
 *
 * @param string $module_name
 *          name of current module being output
 * @param string $default_active_tab
 *          default tab name if already its selected otherwise its empty
 */
function set_default_active_tab($module_name, &$default_active_tab) {
    if (empty ( $default_active_tab ) == true || $module_name == $default_active_tab) {
        if (empty ( $default_active_tab ) == true) {
            $default_active_tab = $module_name; // Set default module as current active module
        }
        return 'active';
    }
}
// add to js of loader
Js_Loader::$js [] = array(
    'src' => $GLOBALS ['CI']->template->template_js_dir('owl.carousel.min.js'),
    'defer' => 'defer'
);
// Js_Loader::$css [] = array(
//     'href' => $GLOBALS ['CI']->template->template_css_dir('owl.carousel.min.css'),
//     'media' => 'screen'
// );
?>


<div class="searcharea">
    <div class="srchinarea">
        <div class="allformst mrg_btm">
            <div class="col-xs-12 nopad">
            <div class="tab_border">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs tabstab">
                <?php if (is_active_airline_module()) { ?>
                <li
                        class="<?php echo set_default_active_tab(META_AIRLINE_COURSE, $default_active_tab)?>"><a
                        href="#flight" aria-controls="flight" role="tab" data-toggle="tab" id="fl"><span class="sprte iconcmn"><i class="fal fa-plane"></i></span><label>Flights</label></a></li>
                <?php } ?>
                <?php if (is_active_bus_module()) { ?>
                <li class="<?php echo set_default_active_tab(META_BUS_COURSE, $default_active_tab)?>"><a
                        href="#bus" aria-controls="bus" role="tab" data-toggle="tab" id="bs"><span class="sprte iconcmn"><i class="fal fa-bus"></i></span><label>Bus</label></a></li>
                <?php } ?>
                <?php if (is_active_hotel_module()) { ?>
                <li
                        class="<?php echo set_default_active_tab(META_ACCOMODATION_COURSE, $default_active_tab)?>"><a
                        href="#hotel" aria-controls="hotel" role="tab" data-toggle="tab" id="hl"><span class="sprte iconcmn"><i class="fal fa-building"></i></span><label>Hotels</label></a></li>
                <?php } ?>
                

                <?php if(is_active_transferv1_module()):?>
                    <li
                        class="<?php echo set_default_active_tab(META_TRANSFERV1_COURSE, $default_active_tab)?>"><a
                        href="#transfers" aria-controls="transfers" role="tab" data-toggle="tab" id="tf"><span class="sprte iconcmn"><i class="fal fa-taxi"></i></span><label>Transfers</label></a></li>

                <?php endif;?>
                    <?php if (is_active_sightseeing_module()) { ?>
                <li
                        class="<?php echo set_default_active_tab(META_SIGHTSEEING_COURSE, $default_active_tab)?>"><a
                        href="#sightseeing" aria-controls="sightseeing" role="tab" data-toggle="tab" id="ac"><span class="sprte iconcmn"><i class="fal fa-binoculars"></i></span><label>Activities</label></a></li>
                <?php } ?>
                <?php if (is_active_car_module()) { ?>
                <li
                        class="<?php echo set_default_active_tab(META_CAR_COURSE, $default_active_tab)?>"><a
                        href="#car" aria-controls="car" role="tab" data-toggle="tab"><span class="sprte iconcmn"><i class="fal fa-car"></i></span><label>Car</label></a></li>
                <?php } ?>

                <?php if (is_active_package_module()) { ?>
                <li
                        class="<?php echo set_default_active_tab(META_PACKAGE_COURSE, $default_active_tab)?>"><a
                        href="#holiday" aria-controls="holiday" role="tab"
                        data-toggle="tab"><span class="sprte iconcmn"><i class="fal fa-tree"></i></span><label>Package</label></a></li>
                <?php } ?>
                </ul>
              </div>
            
            <!-- Tab panes -->
            <div class="fixed_height mb15">
            <div class="col-md-12 nopad">
            <div class="secndblak">
                <div class="container-fluid">
                    <div class="tab-content custmtab">
                    <?php if (is_active_airline_module()) { ?>
                    <div
                            class="tab-pane <?php echo set_default_active_tab(META_AIRLINE_COURSE, $default_active_tab)?>"
                            id="flight">
                        <?php 
                        $comm_val = $fcd["flight_commission_details"][0]["api_value"];
                        $comm_val_unit = $fcd["flight_commission_details"][0]["value_type"];
                        if($comm_val != 0){
                            //echo "Commission - ".$comm_val." ".$comm_val_unit;
                        }   
                        echo $GLOBALS['CI']->template->isolated_view('share/flight_search');
                        ?>
                    </div>
                    <?php } ?>
                    <?php if (is_active_hotel_module()) { ?>
                    <div
                            class="tab-pane <?php echo set_default_active_tab(META_ACCOMODATION_COURSE, $default_active_tab)?>"
                            id="hotel">
                        <?php echo $GLOBALS['CI']->template->isolated_view('share/hotel_search')?>
                    </div>
                    <?php } ?>
                    <?php if (is_active_bus_module()) { ?>
                    <div class="tab-pane <?php echo set_default_active_tab(META_BUS_COURSE, $default_active_tab)?>" id="bus">
                        <?php 
                            $comm_val = $bcd["bus_commission_details"][0]["api_value"];
                            $comm_val_unit = $bcd["bus_commission_details"][0]["value_type"];
                            
                            if($comm_val != 0){
                                //echo "Commission - ".$comm_val." ".$comm_val_unit;
                            } 
                            echo $GLOBALS['CI']->template->isolated_view('share/bus_search');
                        ?>
                    </div>
                    <?php } ?>
                    <?php if(is_active_transferv1_module()):?>                      
                        <div
                            class="tab-pane <?php echo set_default_active_tab(META_TRANSFERV1_COURSE, $default_active_tab)?>"
                            id="transfers">
                        <?php 
                        $comm_val = $tcd["transfer_commission_details"][0]["api_value"];
                        $comm_val_unit = $tcd["transfer_commission_details"][0]["value_type"];
                        if($comm_val != 0){
                            //echo "Commission - ".$comm_val." ".$comm_val_unit;
                        }
                        echo $GLOBALS['CI']->template->isolated_view('share/transferv1_search');
                        ?>
                    </div>

                    <?php endif; ?>
                    <?php if (is_active_sightseeing_module()) { ?>
                    <div
                            class="tab-pane <?php echo set_default_active_tab(META_SIGHTSEEING_COURSE, $default_active_tab)?>"
                            id="sightseeing">
                        <?php
                        $comm_val = $scd["sightseeing_commission_details"][0]["api_value"];
                        $comm_val_unit = $scd["sightseeing_commission_details"][0]["value_type"];

                        if($comm_val != 0){
                            //echo "Commission - ".$comm_val." ".$comm_val_unit;
                        }
                        echo $GLOBALS['CI']->template->isolated_view('share/sightseeing_search')?>
                    </div>
                    <?php } ?>
                    <?php if (is_active_car_module()) { ?>
                    <div
                            class="tab-pane <?php echo set_default_active_tab(META_CAR_COURSE, $default_active_tab)?>"
                            id="car">
                        <?php echo $GLOBALS['CI']->template->isolated_view('share/car_search')?>
                    </div>
                    <?php } ?>

                    <?php if (is_active_package_module()) { ?>
                    <div
                            class="tab-pane <?php echo set_default_active_tab(META_PACKAGE_COURSE, $default_active_tab)?>"
                            id="holiday">
                        <?php echo $GLOBALS['CI']->template->isolated_view('share/holiday_search',$holiday_data)?>
                    </div>
                    <?php } ?>
                    </div>
                </div>
            </div>
            </div>
            </div>
            </div>
<!-- <div class="clearfix"></div> -->
<div class="all_report">
<div class="col-xs-12">
    <div class="row">
        <!-- <h3>Day Wise Booking</h3> -->
    </div>
</div>
</div>
<div class="clearfix"></div>
<?php 
if($_GET['default_view'] != META_PACKAGE_COURSE){
	?>
<div class="trans_logs">
<div class="col-md-12">
<?php if($_GET['default_view'] == META_ACCOMODATION_COURSE){ ?>
    <div class="row"><h3 class="id-h3 padd id-yellow">Day Wise Booking</h3></div>
<div class="row">
    <div class="col-sm-6 padfive">
            <div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            <i class="fa fa-search"></i> Recent Search Hotels
        </div>
    </div>
    <div class="panel-body">
        <div class="row">
                <table class="table table-striped">
                <tr>
                    <th>Sl. No.</th>
                    <th>Agent</th>
                    <th>Transaction Date</th>
                    <th>Reference Number</th>
                    <th>Transaction Type</th>
                    <th>Amount</th>
                    <th>Description</th>
                </tr>
            <!-- <?php
            if (valid_array($latest_transaction)) {

                foreach ($latest_transaction as $k => $v) {
                    if ($v['transaction_owner_id'] == 0) {
                        $user_info = 'Guest';
                    } else {
                        $user_info = $v['username'];
                    }
                ?>
                    <tr>
                        <td><?=($k+1)?></td>
                        <td><?=$v['agent_name']?></td>
                        <td><?=app_friendly_date($v['created_datetime'])?></td>
                        <td><?=$v['app_reference']?></td>
                        <td><?=ucfirst($v['transaction_type'])?></td>
                        <th><?=abs($v['fare']+$v['profit']).'-'.$v['currency']?></th>                   
                        <td><?=$v['remarks']?></td> 
                    </tr>
                <?php
                }
            } else {
                echo '<tr><td>No Data Found</td></tr>';
            }
            ?> -->
            </table>
           </div>
                    
        </div>
        <div class="box-footer text-center">
            <a class="uppercase" href="<?= base_url() . 'index.php/transaction/logs' ?>">View All Transactions</a>
        </div>
</div>
        </div>
        <!--  -->
        <div class="col-sm-6 padfive">
            <div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            <i class="fa fa-file"></i> Recent Booking Hotels
        </div>
    </div>
    <div class="panel-body">
        <div class="row">
                <table class="table table-striped">
                <tr>
                    <th>Sl. No.</th>
                    <th>Agent</th>
                    <th>Transaction Date</th>
                    <th>Reference Number</th>
                    <th>Transaction Type</th>
                    <th>Amount</th>
                    <th>Description</th>
                </tr>
           <!--  <?php
            if (valid_array($latest_transaction)) {

                foreach ($latest_transaction as $k => $v) {
                    if ($v['transaction_owner_id'] == 0) {
                        $user_info = 'Guest';
                    } else {
                        $user_info = $v['username'];
                    }
                ?>
                    <tr>
                        <td><?=($k+1)?></td>
                        <td><?=$v['agent_name']?></td>
                        <td><?=app_friendly_date($v['created_datetime'])?></td>
                        <td><?=$v['app_reference']?></td>
                        <td><?=ucfirst($v['transaction_type'])?></td>
                        <th><?=abs($v['fare']+$v['profit']).'-'.$v['currency']?></th>                   
                        <td><?=$v['remarks']?></td> 
                    </tr>
                <?php
                }
            } else {
                echo '<tr><td>No Data Found</td></tr>';
            }
            ?> -->
            </table>
           </div>
                    
        </div>
        <div class="box-footer text-center">
            <a class="uppercase" href="<?= base_url() . 'index.php/transaction/logs' ?>">View All Transactions</a>
        </div>
</div>
        </div>
</div>
<?php } ?>
    <div class="row">
        <!-- <h3>Day Wise Booking</h3> -->
    </div>
    <div class="row ">
        <div class="col-sm-4 padfive">
            <div class="row row_bookings_section">
        <h3 class="id-h3 padd id-yellow">Day Wise Booking</h3>
        <?php if (is_active_bus_module()) { ?>
            <div class="col-md-6 col-sm-6 col-xs-6 padd">
                <div class="info-box">
                    <span class="info-box-icon bus-l-bg"><i class="fa fa-bus"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Bus Booking</span>
                        <span class="text-red info-box-number <?= META_BUS_COURSE ?>"><?= $bus_booking_count ?></span>
                        <a href="<?= base_url() ?>index.php/report/bus" class="id-more-info">More info
                        </a>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div>

        <?php } ?>

        <?php if (is_active_airline_module()) { ?>
            <div class="col-md-6 col-sm-6 col-xs-6 padd">
                <div class="info-box">
                    <span class="info-box-icon flight-l-bg"><i class="fa fa-plane"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Flight Booking</span>
                        <span class="text-blue info-box-number <?= META_AIRLINE_COURSE ?>"><?= $flight_booking_count ?></span>
                        <a href="<?= base_url() ?>index.php/report/flight" class="id-more-info">More info
                        </a>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div>
        <?php } ?>
        <?php if (is_active_hotel_module()) { ?>
            <div class="col-md-6 col-sm-6 col-xs-6 padd">
                <div class="info-box">
                    <span class="info-box-icon hotel-l-bg"><i class="fa fa-bed"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Hotel Booking</span>
                        <span class="text-green info-box-number <?= META_ACCOMODATION_COURSE ?>"><?= $hotel_booking_count ?></span>
                        <a href="<?= base_url() ?>index.php/report/hotel" class="id-more-info">More info
                        </a>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div>
        <?php } ?>
        
        <?php if (is_active_transferv1_module()) { ?>
            <div class="col-md-6 col-sm-6 col-xs-6 padd">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-taxi"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Transfers</span>
                        <span class="text-yellow info-box-number <?= META_TRANSFERV1_COURSE ?>"><?= @$transfer_booking_count ?></span>

                        <a target="_blank"  href="<?= base_url() ?>index.php/report/transfers" class="id-more-info">More info
                        </a>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div>
        <?php } ?>

        <?php if (is_active_package_module()) { ?>
            <div class="col-md-6 col-sm-6 col-xs-6 padd">
                <div class="info-box">
                    <span class="info-box-icon bg-maroon"><i class="fa fa-suitcase"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Holiday Enquiry</span>
                        <a href="<?= base_url() ?>index.php/report/package_enquiries" class="id-more-info">More info
                        </a>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div>
        <?php } ?>

        <?php if (is_active_sightseeing_module()) { ?>
            <div class="col-md-6 col-sm-6 col-xs-6 padd">
                <div class="info-box">
                    <span class="info-box-icon bg-navy"><i class="fa fa-binoculars    "></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Activities</span>
                        <span class="text-navy info-box-number <?= META_SIGHTSEEING_COURSE ?>"><?= $sightseeing_booking_count ?></span>

                        <a target="_blank"  href="<?= base_url() ?>index.php/report/activities" class="id-more-info">More info
                        </a>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div>
        <?php } ?>

        

        <?php if (is_active_car_module()) { ?>
            <div class="col-md-6 col-sm-6 col-xs-6 padd">
                <div class="info-box">
                    <span class="info-box-icon bg-teal"><i class="fa fa-car"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Car Booking</span>
                        <span class="text-teal info-box-number <?= META_CAR_COURSE ?>"><?= $car_booking_count ?></span>
                        <a href="<?= base_url() ?>index.php/report/car" class="id-more-info">More info
                        </a>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div>
        <?php } ?>

    </div>

    <div class="row">
        <div class="">
            <div id='booking-timeline' class="">
            </div>
        </div>
    </div>






    </div>
        <div class="col-sm-8 padfive">
            <div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            <i class="fa fa-shield"></i> Transaction Logs
        </div>
    </div>
    <div class="panel-body">
        <div class="row">
                <table class="table table-striped">
                <tr>
                    <th>Sl. No.</th>
                    <!--<th>Agent</th> -->
                    <th>Transaction Date</th>
                    <th>Reference Number</th>
                    <th>Transaction Type</th>
                    <!--<th>Amount</th> -->
                    <th>Description</th>
                </tr>
            <?php
            if (valid_array($latest_transaction)) {

                foreach ($latest_transaction as $k => $v) {
                    if ($v['transaction_owner_id'] == 0) {
                        $user_info = 'Guest';
                    } else {
                        $user_info = $v['username'];
                    }
                ?>
                    <tr>
                        <td><?=($k+1)?></td>
                        <!--<td><?=$v['agent_name']?></td> -->
                        <td><?=app_friendly_date($v['created_datetime'])?></td>
                        <td><?=$v['app_reference']?></td>
                        <td class="text-center"><?=ucfirst($v['transaction_type'])?></td>
                        <!--<th><?=abs($v['fare']+$v['profit']).'-'.$v['currency']?></th> -->                   
                        <td><?=$v['remarks']?></td> 
                    </tr>
                <?php
                }
            } else {
                echo '<tr><td>No Data Found</td></tr>';
            }
            ?>
            </table>
           </div>
                    
        </div>
        <div class="box-footer text-center">
            <a class="uppercase" href="<?= base_url() . 'index.php/transaction/logs' ?>">View All Transactions</a>
        </div>
</div>
        </div>
    </div>
</div>
</div>
<?php } ?>
<div class="clearfix"></div>
            
   <?php
    if((isset($_GET['default_view']) && $_GET['default_view'] == META_AIRLINE_COURSE) || !isset($_GET['default_view'])) {
 //debug($top_flight_destinations); exit; ?>
<div class="perhldys">

    <div class="col-md-12">
        <div class="pagehdwrap">
            <h2 class="pagehding">Top Flight Destinations</h2>
        </div>
        <div class="row text-center">
            <?php 
            foreach($top_flight_destinations['data'] AS $value) { 
                $result = $GLOBALS['CI']->custom_db->single_table_records('flight_airport_list', 'origin', array('airport_code' => $value['from_airport_code']));
                $sorigin = $result['data'][0]['origin'];

                $result1 = $GLOBALS['CI']->custom_db->single_table_records('flight_airport_list', 'origin', array('airport_code' => $value['to_airport_code']));
                $dorigin = $result1['data'][0]['origin'];
            ?>
                <div class="col-sm-3 padfive listin topflight" style="cursor: pointer" data-trip_type="oneway" data-from="<?php echo $value['from_airport_name'].'('.$value['from_airport_code'].')'; ?>" data-to="<?php echo $value['to_airport_name'].'('.$value['to_airport_code'].')';?>" data-from_loc_id="<?php echo $sorigin; ?>"  data-to_loc_id="<?php echo $dorigin; ?>" data-departue="<?php echo date('d-m-Y'); ?>" data-v_class="Economy" data-carrier="" data-lcc_gds="0" data-conn_direct="0" data-adult="1" data-child="0" data-infant="0" data-infant="0" data-flight_search="search">
                    <div class="id-grid-image">
                      <div class="id-image-wrapper">
                        <img src="<?php echo $GLOBALS['CI']->template->domain_images($value["image"]); ?>" alt="grid img" class="img-responsive">
                      </div>
                      <div class="id-grid-text"><span><i class="fa fa-plane"></i></span>&nbsp;&nbsp; <?php echo $value["from_airport_name"];?> To <?php echo $value["to_airport_name"]; ?></div>
                    </div>
                </div>  
            <?php    
            }
            ?>
        </div>
    </div>
</div> <!-- perhldys div end -->
<?php } ?>

<div class="clearfix"></div>
<?php 
if(isset($_GET['default_view']) && $_GET['default_view'] == META_BUS_COURSE) {
//debug($top_bus_destinations); exit; ?>
<div class="perhldys" >

    <div class="col-md-12">
        <div class="pagehdwrap">
            <h2 class="pagehding">Top Bus Destinations</h2>
        </div>
        <div class="row text-center">
            <?php foreach($top_bus_destinations['data'] AS $k=>$tbd) { 
                    $from = $tbd["from_city"];
                    $to = $tbd["to_city"];
            ?>
                <div class="col-sm-3 padfive">
                    <div class="id-grid-image">
                      <div class="id-image-wrapper">
                        <img src="<?php echo $GLOBALS['CI']->template->domain_images($tbd["image"]); ?>" alt="grid img" class="img-responsive">
                      </div>
                      <div class="id-grid-text"><span><i class="fa fa-bus"></i></span>&nbsp;&nbsp; <a href="<?php echo base_url();?>bus/bus_search_test?from=<?=$from?>&to=<?=$to?>"><?php echo $from.'-'.$to;?></a></div>
                    </div>
                </div>
            <?php    
            } ?>        
            
        </div>
    </div>
</div> <!-- perhldys end -->

<?php } ?>     
<!-- Hotel sections -->

<?php 
if(isset($_GET['default_view']) && $_GET['default_view'] == META_ACCOMODATION_COURSE) {
//debug($top_bus_destinations); exit; ?>
<div class="perhldys" >
<div class="col-xs-12">
    <div class="pagehdwrap">
        <h2 class="pagehding">Top Hotel Destinations</h2>
        <!-- <span><i class="fal fa-star"></i></span> -->
    </div>
    <div class="retmnus">
    <?php 
   
    foreach($top_hotel_destinations['data'] AS $k=>$tbd) { 
        ?>
            <div class="col-xs-3 nopad htd-wrap">
                <div class="col-xs-12 nopad">
                    <div class="topone">
                        <div class="inspd2 effect-lexi">
                            <div class="imgeht2">
                            <div class="dealimg">
                                <img
                                    class="lazy lazy_loader"
                                    data-src="<?php echo $GLOBALS['CI']->template->domain_images($tbd["image"]); ?>"
                                    alt="<?php echo $tfd["from_city"]; ?>"
                                    src="<?php echo $GLOBALS['CI']->template->domain_images($tbd["image"]); ?>"
                                    />

                                     <div class="absint2 absintcol1 ">
                                <div class="absint2 absintcol2 ">
                                    <div class="absinn">
                                        <div class="smilebig2">
                                        <input type="hidden" class="top_des_id" value="<?php echo $tbd['origin']?>">
                                        <input type="hidden" class="rz_city_id_holder" value="<?php echo $tbd['rz_city_id']?>">
                                        <input type="hidden" class="rz_country_id_holder" value="<?php echo $tbd['country_code']?>">
                                        <input type="hidden"
                                 class="top-des-val hand-cursor"
                                 value="<?=hotel_suggestion_value($tbd['city_name'], $tbd['country_name'])?>">

                                        <?php
                                            $city = $tbd["city_name"];
                                            $country = $tbd["country_name"];
                                        ?>
                                        <h3> <i class="fa fa-bed"></i> <?php echo $city.'-'.$country ; ?></h3>
                                        <!-- <h4><a href="<?php echo base_url();?>bus/bus_search_test?from=<?=$from?>&to=<?=$to?>"><?php echo $from.'-'.$to;?></a></h4> -->
                                        </div>
                                        <div class="clearfix"></div>
                                        
                                    </div>
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

<?php } ?>    
               


<!-- Search Engine Start -->
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function($){
        //Top Destination Functionality
        $('.htd-wrap').on('click', function(e) {
            e.preventDefault();
            var curr_destination = $('.top-des-val', this).val();
            var rz_city_id_holder = $('.rz_city_id_holder', this).val();
            var rz_country_id_holder = $('.rz_country_id_holder', this).val();
            var check_in = "<?=add_days_to_date(7)?>";
            var check_out = "<?=add_days_to_date(10)?>";

            $('#hotel_destination_search_name').val(curr_destination);
            $('#hotel_checkin').val(check_in);
            $('#hotel_checkout').val(check_out);
            $('.rz_city_id_holder').val(rz_city_id_holder);
            $('.rz_country_id_holder').val(rz_country_id_holder);
            $('#hotel_search').submit();
        });
});
    //homepage slide show end
    $('#fl').click(function(){
        //alert('fl');
        window.location.href = "http://192.168.0.50/pace_travel/agent/menu/dashboard/flight?default_view=VHCID1420613784";
    })
    $('#bs').click(function(){
        window.location.href = "http://192.168.0.50/pace_travel/agent/menu/dashboard/bus?default_view=VHCID1433498307";
    })
    /*$('#hl').click(function(){
        window.location.href = "http://192.168.0.50/pace_travel/agent/menu/dashboard/hotel?default_view=VHCID1420613748";
    })
    $('#tl').click(function(){
        window.location.href = "http://192.168.0.50/pace_travel/agent/menu/dashboard/transfers?default_view=TMVIATID1527240212";
    })
    $('#ac').click(function(){
        window.location.href = "http://192.168.0.50/pace_travel/agent/menu/dashboard/activities?default_view=TMCID1524458882";
    })*/
    
</script>
<?php
Js_Loader::$js[] = array('src' => $GLOBALS['CI']->template->template_js_dir('page_resource/pax_count.js'), 'defer' => 'defer');
echo $this->template->isolated_view('share/js/lazy_loader');
?>

