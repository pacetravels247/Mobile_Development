<?php
$active_domain_modules = $this->active_domain_modules;
$tiny_loader = $GLOBALS['CI']->template->template_images('tiny_loader_v1.gif');
$tiny_loader_img = '<img src="' . $tiny_loader . '" class="loader-img" alt="Loading">';
$booking_summary = array();
?>

<!-- <div class="container-fluid">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-3">
                <div class="info-box">
                    <div class="info-box-content">
                        <span class="info-box-text text-bold">Flight</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="info-box">
                    <div class="info-box-content">
                        <span class="info-box-text text-bold">Flight</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="info-box">
                    <div class="info-box-content">
                        <span class="info-box-text text-bold">Flight</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="info-box">
                    <div class="info-box-content">
                        <span class="info-box-text text-bold">Flight</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> -->


<!-- <div class="clearfix"></div> -->
<div class="container-fluid">
    <div class="row id-dashboard-div">
        <?php //if (is_active_airline_module()) { ?>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box id-bcolor-blue">
                    <span class="info-box-icon"><i class="<?= get_arrangement_icon(META_AIRLINE_COURSE) ?> id-color-blue"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-bold">Flight</span>
                        <span><a target="_blank" href="<?= base_url() ?>index.php/report/b2b_flight_report">Booking : <?php echo $daily_data['flight_daily_rp'][0]['count_cnf_book'];?></a></span></br>
                        <span><a href="#">Cancellation : <?php echo $daily_data['flight_daily_rp'][0]['count_cnl_book'];?></a></span></br>
                        <span><a href="#">Seats : <?php echo $daily_data['flight_daily_rp'][0]['no_of_seats'];?></a></span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div>
        <?php //} ?>
        
        <?php //if (is_active_bus_module()) { ?>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box id-bcolor-red">
                    <span class="info-box-icon"><i class="<?= get_arrangement_icon(META_BUS_COURSE) ?> id-color-red"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-bold">Bus</span>
                        <span><a target="_blank" href="<?= base_url() ?>index.php/report/b2b_bus_report">Booking : <?php echo $daily_data['bus_daily_rp'][0]['count_cnf_book'];?></a></span></br>
                        <span><a href="#">Cancellation : <?php echo $daily_data['bus_daily_rp'][0]['count_cnl_book'];?></a></span></br>
                        <span><a href="#">Seats : <?php echo $daily_data['bus_daily_rp'][0]['no_of_seats'];?></a></span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div>
        <?php //} ?>

        <?php //if (is_active_hotel_module()) { ?>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box id-bcolor-green">
                    <span class="info-box-icon"><i class=" id-color-green <?= get_arrangement_icon(META_ACCOMODATION_COURSE) ?>"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-bold">Hotel</span>
                        <span><a target="_blank" href="<?= base_url() ?>index.php/report/b2b_hotel_report">Booking : <?php echo $daily_data['hotel_daily_rp'][0]['count_cnf_book'];?></a></span></br>
                        <span><a href="#">Cancellation : <?php echo $daily_data['hotel_daily_rp'][0]['count_cnl_book'];?></a></span></br>
                        <span><a href="#">Rooms : <?php echo $daily_data['hotel_daily_rp'][0]['no_of_rooms'];?></a></span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div>
        <?php //} ?>

        <!-- <?php if (is_active_transferv1_module()) { ?>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon transfers-l-bg"><i class="<?= get_arrangement_icon(META_TRANSFERV1_COURSE) ?>"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-bold">Transfer</span>
                        <span>Booking : <?php echo $daily_data['transfer_daily_rp'][0]['count_cnf_book'];?></span></br>
                        <span>Cancellation : <?php echo $daily_data['transfer_daily_rp'][0]['count_cnl_book'];?></span>
                    </div>
                </div>
            </div>
        <?php } ?> -->

        <!-- <?php if (is_active_sightseeing_module()) { ?>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon sightseeing-l-bg"><i class="<?= get_arrangement_icon(META_SIGHTSEEING_COURSE) ?>"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-bold">Activities</span>
                        <span>Booking : <?php echo $daily_data['activities_daily_rp'][0]['count_cnf_book'];?></span></br>
                        <span>Cancellation : <?php echo $daily_data['transfer_daily_rp'][0]['count_cnl_book'];?></span>
                    </div>
                </div>
            </div>
        <?php } ?> -->

        <!-- <?php if (is_active_car_module()) { ?>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon sightseeing-l-bg"><i class="<?= get_arrangement_icon(META_CAR_COURSE) ?>"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Car Booking</span>
                        <span class="info-box-number <?= META_CAR_COURSE ?>"><?= $car_booking_count ?></span>
                        <a href="<?= base_url() ?>index.php/report/b2c_car_report" class="">B2C Report 
                        </a></br>
                        <a href="<?= base_url() ?>index.php/report/b2b_car_report" class="">Agent Report</a>
                    </div>
                </div>
            </div>
        <?php } ?> -->

        <?php //if (is_active_package_module()) { ?>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box id-bcolor-yellow">
                    <span class="info-box-icon"><i class="fa fa-suitcase id-color-yellow"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Holiday Enquiry</span>
                        <a target="_blank" href="<?= base_url() ?>index.php/supplier/enquiries" class="">Enquiries
                        </a></br>
                        <!--<a href="<?= base_url() ?>index.php/report/b2b_package_report" class="">Agent Report</a>-->
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div>
        <?php //} ?>

        </div>
</div>


<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 pr-0">    
           <div class="panel panel-default" style="border-radius: 20px;">
                <div class="panel-head" style="padding: 2% 2%; margin-bottom: 20px;">
                    <div class="col-sm-3 padfive">
                        <!-- <label>Time</label> -->
                        <select class="form-control" name="time_period" id="time_period">
                            <option value="week">Week</option>
                            <option value="month">Month</option>
                        </select>
                    </div>
                    <div class="col-sm-3 padfive">
                        <input type ="text" name="start_date" class="form-control id-input" placeholder="Start Date" id="s_date">
                    </div>
                    <div class="col-sm-3 padfive">
                        <input type ="text" name="end_date" class="form-control id-input" placeholder="End Date" id="e_date">
                    </div>
                     <div class="col-sm-3 padfive">
                        <input type ="button" class="btn btn-primary form-control id-input" id="search" value="Generate">
                    </div>
                </div>
                <!-- <hr> -->
                <div class="panel-body">
                    <div id='booking-calendar' class="">
                        <div id="container"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">    
           <!-- <div class="panel panel-default" style="border-top: 3px solid #3c8dbc;height: 488px;"> -->
           <div class="panel panel-default" style="height: 485px; border-radius: 20px;">
                <div class="panel-head">
                    <h3 style="text-align: center;font-size: 18px; font-weight: 600;">Total Bookings</h3>
                </div>
                <!-- <hr> -->
                <?php //debug($month_compare);die(); ?>
                <div class="panel-body">
                    <div id='booking-calendar' class="">
                        <label>Flight</label><span class="text-primary pull-right" id="f_values"><?php echo $month_compare['current_month'][0]['curnt_mnth_f_b'] .'/'.$month_compare['previous_month'][0]['prev_mnth_f_b']; ?></span>
                        <div class="progress">
                          <div class="progress-bar progress-bar-default" role="progressbar" aria-valuenow="40"
                          aria-valuemin="0" aria-valuemax="100" style="width:<?php echo ($month_compare['current_month'][0]['curnt_mnth_f_b']/$month_compare['previous_month'][0]['prev_mnth_f_b'])*100?>%">
                          </div>
                        </div>

                        <label>Bus</label><span class="text-danger pull-right" id="b_values"><?php echo $month_compare['current_month'][0]['curnt_mnth_b_b'] .'/'.$month_compare['previous_month'][0]['prev_mnth_b_b']; ?></span></label>
                        <div class="progress">
                          <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="60"
                          aria-valuemin="0" aria-valuemax="100" style="width:<?php echo ($month_compare['current_month'][0]['curnt_mnth_b_b']/$month_compare['previous_month'][0]['prev_mnth_b_b'])*100?>%">
                          </div>
                        </div>

                        <label>Hotel</label><span class="text-success pull-right" id="h_values"><?php echo $month_compare['current_month'][0]['curnt_mnth_h_b'] .'/'.$month_compare['previous_month'][0]['prev_mnth_h_b']; ?></span>
                        <div class="progress">
                          <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="50"
                          aria-valuemin="0" aria-valuemax="100" style="width:<?php echo ($month_compare['current_month'][0]['curnt_mnth_h_b']/$month_compare['previous_month'][0]['prev_mnth_h_b'])*100?>%">
                          </div>
                        </div>

                        <label>Activities</label><span class="text-warning pull-right" id="a_values"><?php echo $month_compare['current_month'][0]['curnt_mnth_a_b'] .'/'.$month_compare['previous_month'][0]['prev_mnth_a_b']; ?></span></label>
                        <div class="progress">
                          <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="70"
                          aria-valuemin="0" aria-valuemax="100" style="width:<?php echo ($month_compare['current_month'][0]['curnt_mnth_a_b']/$month_compare['previous_month'][0]['prev_mnth_a_b'])*100?>%">
                          </div>
                        </div>

                        <label>Transfer</label><span class="text-info pull-right" id="t_values"><?php echo $month_compare['current_month'][0]['curnt_mnth_t_b'] .'/'.$month_compare['previous_month'][0]['prev_mnth_t_b']; ?></span></label>
                        <div class="progress">
                          <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="70"
                          aria-valuemin="0" aria-valuemax="100" style="width:<?php echo ($month_compare['current_month'][0]['curnt_mnth_t_b']/$month_compare['previous_month'][0]['prev_mnth_t_b'])*100?>%">
                          </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 id-profit-main">
            <div class="">
                 <!-- <div class="panel-head">
                    <h3 style="text-align: center;font-size: 18px;">Facts & Figures</h3>
                </div> -->
                <!-- <hr> -->
                <div class="panel-body" style="text-align: center;padding: 0;">
                    <div class="col-sm-3 pr-0 pl-0">
                        <div class="id-profit-div">
                            <p>Total Purchasing Cost</p>
                            <label>₹ <?php echo $total_purchase; ?></label>
                        </div>
                    </div>
                    <div class="col-sm-3 pr-0">
                        <div class="id-profit-div">
                            <p>Total Selling Cost</p>
                            <label>₹ <?php echo round($total_sales, 2); ?></label>
                        </div>
                    </div>
                    <div class="col-sm-3 pr-0">
                        <div class="id-profit-div">
                            <p>Total Refund</p>
                            <label>₹ <?php echo round($total_refund, 2); ?></label>
                        </div>
                    </div>
                    <div class="col-sm-3 pr-0">
                        <div class="id-profit-div">
                            <p>Total Profit</p>
                            <label>₹ <?php echo round($total_profit, 2); ?></label>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3 pr-0">
        <div class="row">
            <div class="info-box" style="border-top-right-radius: 20px;">
                <span class="info-box-icon id-bcolor-blue"><i class="<?= get_arrangement_icon(META_AIRLINE_COURSE) ?>"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-bold">Flight</span>
                    <span>Purchase Amount : <?php echo $flight_transc['admin_purchase'];?></span>
                    <br>
                    <span>Sales Amount : <?php echo $flight_transc['admin_sales'];?></span>
                    <br>
                    <span>Profit Amount : <?php echo $flight_transc['admin_profit'];?></span>
                </div><!-- /.info-box-content -->
            </div>
        </div>
        <?php //if (is_active_bus_module()) { ?>
        <div class="row">
            <div class="info-box">
                <span class="info-box-icon id-bcolor-red"><i class="<?= get_arrangement_icon(META_BUS_COURSE) ?>"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-bold">Bus</span>
                    <span>Purchase Amount : <?php echo $bus_transc['admin_purchase'];?></span>
                    <br>
                    <span>Sales Amount : <?php echo $bus_transc['admin_sales'];?></span>
                    <br>
                    <span>Profit Amount : <?php echo $bus_transc['admin_profit'];?></span>
                </div><!-- /.info-box-content -->
            </div>
        </div>
        <?php //} ?>

        <?php //if (is_active_hotel_module()) { ?>
        <div class="row">
            <div class="info-box">
                <span class="info-box-icon id-bcolor-green"><i class="<?= get_arrangement_icon(META_ACCOMODATION_COURSE) ?>"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-bold">Hotel</span>
                    <span>Purchase Amount : <?php echo $hotel_transc['admin_purchase'];?></span>
                    <br>
                    <span>Sales Amount : <?php echo $hotel_transc['admin_sales'];?></span>
                    <br>
                    <span>Profit Amount : <?php echo $hotel_transc['admin_profit'];?></span>
                </div><!-- /.info-box-content -->
            </div>
        </div>
        <?php //} ?>

        <?php //if (is_active_transferv1_module()) { ?>
        <div class="row">
            <div class="info-box">
                <span class="info-box-icon id-bcolor-purple"><i class="<?= get_arrangement_icon(META_TRANSFERV1_COURSE) ?>"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-bold">Transfer</span>
                    <span>Purchase Amount : <?php echo $transfer_transc['admin_purchase'];?></span>
                    <br>
                    <span>Sales Amount : <?php echo $transfer_transc['admin_sales'];?></span>
                    <br>
                    <span>Profit Amount : <?php echo $transfer_transc['admin_profit'];?></span>
                </div><!-- /.info-box-content -->
            </div>
        </div>
        <?php //} ?>

        <?php //if (is_active_sightseeing_module()) { ?>
        <div class="row">
            <div class="info-box" style="border-bottom-right-radius: 20px;">
                <span class="info-box-icon id-bcolor-brown"><i class="<?= get_arrangement_icon(META_SIGHTSEEING_COURSE) ?>"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-bold">Activities</span>
                    <span>Purchase Amount : <?php echo $activities_transc['admin_purchase'];?></span>
                    <br>
                    <span>Sales Amount : <?php echo $activities_transc['admin_sales'];?></span>
                    <br>
                    <span>Profit Amount : <?php echo $activities_transc['admin_profit'];?></span>
                </div><!-- /.info-box-content -->
            </div>
        </div>
        <?php //} ?>
    </div>
    <div class="col-md-9 id-deposit-div">
        <div class="row">
            <div class="panel panel-default" style="min-height: 430px; border-radius: 20px;margin-bottom: 10px;">
                <div class="panel-head box-header with-border id-bcolor-brown">
                <h3 style="font-size: 18px;margin: 0;">Payment Deposit Details</h3>
                </div>
            
                <div class="panel-body" style="padding: 0;">
                    <div id='payment_table' class="">
                        <table class="table">
                            <thead>
                              <tr>
                                <th>SL No.</th>
                                <th>Deposit Type</th>
                                <th>No. Of Deposit</th>
                                <th>Deposit Amount</th>
                              </tr>
                            </thead>
                            <tbody style="overflow-y: scroll;">
                                <?php
                                    $count = 1;
                                    foreach ($deposit_data as $key => $value) {
                                ?>
                                    <tr>
                                        <td><?php echo $count++; ?></td>
                                        <td><?php echo $value['transaction_type']; ?></td>
                                        <td><?php echo $value['no_of_dep']; ?></td>
                                        <td><label class="label label-success"><?php echo $value['amt']; ?></label></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                          </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row id-agent-div">
            <div class="panel panel-default" style="">   
               <!-- <div class="panel-head box-header with-border" style="text-align: center;background-color: #0073b7;color: #fff">
                <h3 style="font-size: 18px;margin: 0;">Agent Details</h3 >
                </div> -->
            
                <div class="panel-body">
                    
                    <div class="col-sm-3">
                        <p>Total Agents</p>
                        <a href="<?php echo base_url().'user/b2b_user'; ?>" target="_blank" style="color:#000;">
                        <label class="text-bold"><?php echo (count($agent_data['inactive_agent']) + count($agent_data['lock_agent']) + count($agent_data['active_agent']));?></label></a>
                    </div>
                    <div class="col-sm-2" data-toggle="modal" data-target="#inactive_agent_details">
                        <p>Inactive</p>
                        <label class="text-bold"><?php echo count($agent_data['inactive_agent']);?></label>
                    </div>
                    <div class="col-sm-2" data-toggle="modal" data-target="#new_agent_details">
                        <p>New</p>
                        <label class="text-bold"><?php echo count($agent_data['new_agent']);?></label>
                    </div>
                    <div class="col-sm-2" data-toggle="modal" data-target="#lock_agent_details">
                        <p>Locked</p>
                        <label class="text-bold"><?php echo count($agent_data['lock_agent']);?></label>
                    </div>
                     <div class="col-sm-3" data-toggle="modal" data-target="#active_agent_details">
                        <p>Unlocked</p>
                        <label class="text-bold"><?php echo count($agent_data['active_agent']);?></label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 pr-0">
        <?php echo $api_balances; ?>
    </div>
    <div class="col-md-4">
        <?php
            $latest_trans_list = '';
            $latest_trans_summary = '';
            if (valid_array($latest_transaction)) {
                //debug($latest_transaction);die();
                foreach ($latest_transaction as $k => $v) {
                    $latest_trans_list .= '<li class="item">';
                    $latest_trans_list .= '<div class="product-img image"><i class="' . get_arrangement_icon(module_name_to_id($v['transaction_type'])) . '"></i></div>';
                    $latest_trans_list .= '<div class="product-info">
                                    <a class="product-title" href="">
                                        ' . $v['app_reference'] . ' -' . app_friendly_day($v['created_datetime']) .'-'.$v['agent_name'].'-'.''.$v['supplier'].''.' <span class="label label-primary pull-right"><i class="fa fa-inr"></i> ' . ($v['grand_total']) . '</span>
                                    </a>
                                    <span class="product-description">
                                        ' . $v['remarks'] . '
                                    </span>
                                </div>';
                    $latest_trans_list .= '</li>';
                }
            }
            if(check_user_previlege('p118')): ?>
            <div class="col-md-12 id-recent-book">
                <div class="">
                    <div class="box-header with-border id-bcolor-brown">
                        <h3 class="box-title">Recent Booking Transactions</h3>
                        <p class="box-title"><a class="id-recent-book-agent-count" href="#" data-toggle="modal" data-target="#agent_count">Today's Active Agents:<?php echo $active_agent_count; ?></a> </p>
                    </div>
                    <?php //echo "<pre>recent_report==>>>>"; print_r($recent_report);die; ?>
                    <!-- <p style="font-size: 15px;margin-top: 10px;">Today's Active Agents: 0</p> <hr class="m-0"> -->
                    <div class="box-body pb-0 pt-0">
                        <div class="container-fluid nopad" >
                            <?php
                            foreach ($recent_report as $key => $value) { 
                            $date_a = new DateTime($value['datetime']);
                            $date_b = new DateTime(date('Y-m-d H:i:s'));
                            $interval = date_diff($date_a,$date_b);
                            $book_hour = $interval->format('%h');
                            $book_min = $interval->format('%i');
                            $book_day = $interval->format('%d');
                            $recent_time = "";
                            if($book_day > 0){
                              $recent_time = $book_day." D ".$book_hour." H ".$book_min." M ";
                            }else{
                              if($book_hour > 0){
                                $recent_time = $book_hour." h ".$book_min." min ";
                              }else{
                                $recent_time = $book_min." min ";
                              }
                            }
                                if($value['product'] == 'bus'){ ?>
                                    <div class="row mt-1 mb-1">
                                    <div class="col-sm-1 nopad">
                                        <i class="fa fa-bus"></i>
                                    </div>
                                    <div class="col-sm-6 pr-0">
                                        <p class="id-recent-book-name"><?php echo $value['operator'] ?></p><span title="<?php echo $value['Agency_name']; ?>"><?php echo $value['Agent_id']; ?></span>
                                    </div>
                                    <div class="col-sm-2">
                                        <p class="id-recent-book-b2b mb-0 mt-2">B2B</p>
                                    </div>
                                    <div class="col-sm-3 nopad">
                                        <p class="id-recent-book-price mb-0">&#8377; <?php echo $value['fare'] ?></p><span> <?php echo $recent_time; ?> ago</span>
                                    </div>
                                </div><hr class="m-0">

                               <?php }elseif($value['product'] == 'flight'){ ?>
                                    <div class="row mt-1 mb-1">
                                    <div class="col-sm-1 nopad">
                                        <i class="fa fa-plane"></i>
                                    </div>
                                    <div class="col-sm-6 pr-0">
                                        <p class="id-recent-book-name"><?php echo $value['airline_name'] ?></p><span title="<?php echo $value['Agency_name']; ?>"><?php echo $value['Agent_id']; ?></span>
                                    </div>
                                    <div class="col-sm-2">
                                        <p class="id-recent-book-b2b mb-0 mt-2">B2B</p>
                                    </div>
                                    <div class="col-sm-3 nopad">
                                        <p class="id-recent-book-price mb-0">&#8377; <?php echo $value['fare'] ?></p><span> <?php echo $recent_time; ?> ago</span>
                                    </div>
                                </div><hr class="m-0">
                               <?php }?>
                                
                             <?php } ?>
                        </div>
                        <!-- <ul class="products-list product-list-in-box">
                            <?= $latest_trans_list ?>
                        </ul> -->
                    </div>
                    <div class="box-footer text-center">
                        <a class="uppercase" href="<?= base_url() . 'index.php/transaction/logs' ?>">View All Transactions</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

    </div>

<!-- *************Agent count month wise******************************************************* -->
<div class="modal fade" id="agent_count" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
   <div class="modal-dialog" role="document" style="margin-top: 5%;">
      <div class="modal-content">
         <div class="modal-header text-center" style="background-color: #1c2331;color: #fff; ">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title font-weight-bold" id="myModalLabel">Monthly Agent Counts</h4>
         </div>
         <div class="modal-body">
            <div class="box-body ">
               <div class="" id="printBlock">
                  <div class="col-md-12">
                     <p class="font-weight-bold text-center" style="color: #b80303;">Details</p>
                     <div class="row">
                        <?php 
                           $monthly_report = htmlentities(json_encode($monthly_agent_count),ENT_QUOTES);
                            ?>
                        <input type="hidden" id="monthly-report" value="<?php echo $monthly_report; ?>">
                        <?php 
                           foreach ($monthly_agent_count as $key => $value) 
                             {
                              ?>
                        <div  class="col-sm-3 text-center monthly_wrapper" data-date="<?php echo $key; ?>" style="border:1px solid #cccccc;cursor: pointer;">
                           <span class="font-weight-bold"><?php echo date("d-m-Y",strtotime($key)); ?></span>
                           <hr style="margin-top: 1px; margin-bottom: 1px;">
                           <span class="fs-08"><?php 
                              $agent_count = count($value);
                              if($agent_count == '')
                              {echo 0;}
                              else
                              {echo $agent_count;} ?></span>
                        </div>
                        <?php }?>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<!--Model End -->
<!-- *************Agent count month wise******************************************************* -->
<div class="modal fade" id="agent_detail1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
   <div class="modal-dialog modal-sm" role="document" style="margin-top:10%; width:480px;">
      <div class="modal-content">
         <div class="modal-header text-center p-3" style="background-color: #b80303;color: #fff; ">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title font-weight-bold " id="myModalLabel">Agent Details</h5>
         </div>
         <div class="row" >
           <!--  <div class="col-sm-offset-4 col-sm-4">
               <a id="agentExcel" class="btn btn1 p-bcolor mb-2" selected-date=""><i class="fa fa-file-excel-o fa-fw"></i>&nbsp;Excel</a>
            </div> -->
         </div>
         <div class="modal-body" style="overflow-y:auto; height:400px;">
            <div class="row text-left">
               <div class="col-sm-12">
                  <table style="border: 2px solid #aaa" cellpadding="1" cellspacing="5" width="100%" class="table table-striped table-hover agency-table-wrapper">
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- *********************new agent******************************************** -->
<div class="modal fade" id="new_agent_details" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
   <div class="modal-dialog modal-sm" role="document" style="margin-top:10%; width:480px; height:200px;">
      <div class="modal-content">
         <div class="modal-header text-center p-3" style="background-color: #b80303;color: #fff; ">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title font-weight-bold">New Agent Details</h5>
         </div>
         <div class="modal-body" style="overflow-y:auto; height:400px;">
            <div class="row text-left">
               <div class="col-sm-12">
                  <table style="border: 2px solid #aaa" cellpadding="1" cellspacing="5" width="100%" class="table table-striped table-hover">
                  <tr>
                  <th>Agent Id</th>
                  <th>Agency Name</th>
                  <th>Address</th>
                  <th>Contact No</th>
                  </tr>
                  <?php
                  if(isset($agent_data['new_agent']) && !empty($agent_data['new_agent']))
                  {
                  foreach ($agent_data['new_agent'] as $k => $val) { ?>
                  <tr>
                  <td><?php echo provab_decrypt($val['uuid']); ?></td>
                  <td><?php echo $val['agency_name']; ?></td>
                  <td><?php echo $val['address']; ?></td>
                  <td><?php echo $val['phone']; ?></td>
                  </tr>
                  <?php } } ?>
                  
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<!--Model End -->
<!-- *********************inactive_agent_details******************************************** -->
<div class="modal fade" id="inactive_agent_details" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
   <div class="modal-dialog modal-sm" role="document" style="margin-top:10%; width:480px; height:200px;">
      <div class="modal-content">
         <div class="modal-header text-center p-3" style="background-color: #b80303;color: #fff; ">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title font-weight-bold">Inactive Agent Details</h5>
         </div>
         <div class="modal-body" style="overflow-y:auto; height:400px;">
            <div class="row text-left">
               <div class="col-sm-12">
                  <table style="border: 2px solid #aaa" cellpadding="1" cellspacing="5" width="100%" class="table table-striped table-hover">
                  <tr>
                  <th>Agent Id</th>
                  <th>Agency Name</th>
                  <th>Balance</th>
                  <th>Credit Limit</th>
                  <th>Contact No</th>
                  </tr>
                  <?php 
                  if(isset($agent_data['inactive_agent']) && !empty($agent_data['inactive_agent']))
                  {
                  foreach ($agent_data['inactive_agent'] as $k => $val) { ?>
                  <tr>
                  <td><?php echo provab_decrypt($val['uuid']); ?></td>
                  <td><?php echo $val['agency_name']; ?></td>
                  <td><?php echo $val['due_amount']; ?></td>
                  <td><?php echo $val['credit_limit']; ?></td>
                  <td><?php echo $val['phone']; ?></td>
                  </tr>
                  <?php } } ?>
                  
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<!-- *********************active_agent_details******************************************** -->
<div class="modal fade" id="active_agent_details" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
   <div class="modal-dialog modal-sm" role="document" style="margin-top:10%; width:680px; height:200px;">
      <div class="modal-content">
         <div class="modal-header text-center p-3" style="background-color: #b80303;color: #fff; ">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title font-weight-bold">Active Agent Details</h5>
         </div>
         <div class="modal-body" style="overflow-y:auto; height:400px;">
            <div class="row text-left">
               <div class="col-sm-12">
                  <table style="border: 2px solid #aaa" cellpadding="1" cellspacing="5" width="100%" class="table table-striped table-hover">
                  <tr>
                  <th>Agent Id</th>
                  <th>Agency Name</th>
                  <th>Balance</th>
                  <th>Credit Limit</th>
                  <th>Contact No</th>
                  </tr>
                  <?php 
                  if(isset($agent_data['active_agent']) && !empty($agent_data['active_agent']))
                  {
                  foreach ($agent_data['active_agent'] as $k => $val) { ?>
                  <tr>
                  <td><?php echo provab_decrypt($val['uuid']); ?></td>
                  <td><?php echo $val['agency_name']; ?></td>
                  <td><?php echo $val['due_amount']; ?></td>
                  <td><?php echo $val['credit_limit']; ?></td>
                  <td><?php echo $val['phone']; ?></td>
                  </tr>
                  <?php } } ?>
                  
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- *********************lock_agent******************************************** -->
<div class="modal fade" id="lock_agent_details" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
   <div class="modal-dialog modal-sm" role="document" style="margin-top:10%; width:680px; height:200px;">
      <div class="modal-content">
         <div class="modal-header text-center p-3" style="background-color: #b80303;color: #fff; ">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title font-weight-bold">Lock Agent Details</h5>
         </div>
         <div class="modal-body" style="overflow-y:auto; height:400px;">
            <div class="row text-left">
               <div class="col-sm-12">
                  <table style="border: 2px solid #aaa" cellpadding="1" cellspacing="5" width="100%" class="table table-striped table-hover">
                  <tr>
                  <th>Agent Id</th>
                  <th>Agency Name</th>
                  <th>Balance</th>
                  <th>Credit Limit</th>
                  <th>Status</th>
                  <th>Contact No</th>
                  </tr>
                  <?php 
                  if(isset($agent_data['lock_agent']) && !empty($agent_data['lock_agent']))
                  {
                  foreach ($agent_data['lock_agent'] as $k => $val) { ?>
                  <tr>
                  <td><?php echo provab_decrypt($val['uuid']); ?></td>
                  <td><?php echo $val['agency_name']; ?></td>
                  <td><?php echo $val['due_amount']; ?></td>
                  <td><?php echo $val['credit_limit']; ?></td>
                  <td><select class="toggle-user-status" data-user-id="<?php echo $val['user_id']; ?>" data-uuid="<?php echo $val['uuid']; ?>">
                    <?php if($val['status'] == 2){ ?>
                        <option value="2" selected>Lock</option>
                    <?php } ?><option value="0">Inactive</option><option value="1">Active</option></select></td>
                  <td><?php echo $val['phone']; ?></td>
                  </tr>
                  <?php } } ?>
                  
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<input type="hidden" name="base_url" id="base_url" value="<?php echo base_url(); ?>">

<script src="<?php echo SYSTEM_RESOURCE_LIBRARY; ?>/Highcharts/js/highcharts.js"></script>
<script src="<?php echo SYSTEM_RESOURCE_LIBRARY; ?>/Highcharts/js/modules/exporting.js"></script>

<?php

?>

<script>

$(document).ready(function(){
    $('#s_date,#e_date').datepicker({
         dateFormat: 'yy-mm-dd',
    });
    var query = '';
    draw_graph(query);

    //Active/Deactive Agent
    $('.toggle-user-status').on('change', function(e) {
        alert("toggle-user-status----")
        e.preventDefault();
        var _user_status = this.value;
        var app_base_url = $('#base_url').val();
        var _opp_url = app_base_url+'index.php/user/';
        if (parseInt(_user_status) == 1) {
            _opp_url = _opp_url+'activate_account/';
        } else if(parseInt(_user_status) == 0){
            _opp_url = _opp_url+'deactivate_account/';
        }else if(parseInt(_user_status) == 2){
            _opp_url = _opp_url+'lock_account/';
        }
        _opp_url = _opp_url+$(this).data('user-id')+'/'+$(this).data('uuid');
        toastr.info('Please Wait!!!');
        $.get(_opp_url, function() {
            toastr.info('Updated Successfully!!!');
        });
    });

    $(document).on('click', '.monthly_wrapper', function(e){
       var selectedDate = $(this).attr('data-date');
        $('#agentExcel').attr('selected-date',selectedDate);
       var allReportData = $('#monthly-report').val();
       var obj = JSON.parse(allReportData);
       $('.agency-table-wrapper').html('');
       $('.agency-table-wrapper').html('<tr><th class="text-center" style="padding: 0 10px 0 10px; border-right:1px solid #aaa;">Agent ID</th><th class="text-center" style="padding: 0 10px 0 10px;">Agency Name</th>/tr>');
       $.each(obj, function(index,element){
         if(index == selectedDate){
           if(element != ''){
            var bookingList = '';
            $.each(element, function(index1,element1){
               bookingList += '<tr class="text-left" ><td style="border-top: 1px solid #aaa; padding: 0 10px 0 10px; border-right:1px solid #aaa;">'+element1.agent_id+'</td><td style="border-top: 1px solid #aaa; padding: 0 10px 0 10px;">'+element1.agency_name+'</td></tr>';             
            });           
            $('.agency-table-wrapper').append(bookingList);
            $('#agent_detail1').modal('show');
           }
         }
       });
     });
});

function draw_graph(query){

    $.ajax({
        'url' : app_base_url+"index.php/menu/bi_reports_graph/"+query,
        'type' : 'get',
        'dataType' : 'json',
        success : function(response){
            $('#container').highcharts({
                chart: {
                    type: 'column'
                },
                title: {
                    text: 'Sales Analytics B2B'
                },
                subtitle: {
                    text: ''
                },
                xAxis: {
                    categories: response['days'],
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'No. Of Bookings'
                    }
                },
                tooltip: {
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                        '<td style="padding:0"><b>{point.y}</b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0
                    }
                },


                series : response['booking_data'],
            })
        }
    })
    
}

$('#search').click(function(){
    
    var time_period = $('#time_period').val();
    var s_date = $('#s_date').val();
    var e_date = $('#e_date').val();

    query = '?';
    if(time_period != ''){
        query += 'time_period='+time_period;
    }
    if(time_period != ''){
        query += '&s_date='+s_date;
    }
    if(time_period != ''){
        query += '&e_date='+e_date;
    }

    draw_graph(query);
    
})


</script>


<style type="text/css">
    .content-wrapper{
        padding-top: 0!important;
    }
    .id-dashboard-div .info-box{
        border-radius: 20px;
    }
    .id-dashboard-div .info-box-icon{
        border-radius: 20px;
    }
    .id-dashboard-div .info-box-icon{
        background-color: #fff!important;
    }
    .id-dashboard-div .id-color-blue{
        /*color: #0089ff!important;*/
        /*border: 4px solid #4b8fda;*/
        color: #3cc5db!important;
        border: 4px solid #3cc5db;
        border-radius: 20px;
    }
    .id-dashboard-div .id-color-green{
        /*color: #30a65a!important;*/
        /*border: 4px solid #4d9c70;*/
        color: #8ec31a!important;
        border: 4px solid #95c515;
        border-radius: 20px;

    }
    .id-dashboard-div .id-color-red{
        /*color: #ff1b00!important;*/
        /*border: 4px solid #d54f47;*/
        color: #ef5454!important;
        border: 4px solid #ef5454;
        border-radius: 20px;

    }
    .id-dashboard-div .id-color-yellow{
        /*color: #f39c12!important;*/
        /*border: 4px solid #d29c52;*/
        color: #fbcb00!important;
        border: 4px solid #fbca02;
        border-radius: 20px;

    }
     .id-bcolor-blue{
        /*background-image: linear-gradient(to right, #0089ff , #062d67);*/
        background-image: linear-gradient(to right, #0089ff , #00c6da);
        color: #fff;
    }
     .id-bcolor-green{
        /*background-image: linear-gradient(to right, #30a65a , #10270e);*/
        background-image: linear-gradient(to right, #30a65a , #98c50d);
        color: #fff;
    }
     .id-bcolor-red{
        /*background-image: linear-gradient(to right, #ff1b00 , #520e0e);*/
        background-image: linear-gradient(to right, #cc1600 , #f77644);
        color: #fff;
    }
     .id-bcolor-yellow{
        /*background-image: linear-gradient(to right, #f39c12 , #6b4200);*/
        background: linear-gradient(to right, #ff8d00 0%, #ffcd00 100%);
        color: #fff;
    }
     .id-bcolor-purple{
        /*background-image: linear-gradient(to right, #f39c12 , #6b4200);*/
        background: linear-gradient(to right, #4f1ab8 0%, #d995ef 100%);
    }
     .id-bcolor-brown{
        /*background-image: linear-gradient(to right, #f39c12 , #6b4200);*/
        background: linear-gradient(to right, #67280a 0%, #e1ac7f 100%);
    }
    .id-dashboard-div .info-box-text{
        font-size: 18px;
        font-weight: bold;
    }
    .id-dashboard-div .info-box-content a{
        color: #fff!important;
    }
    .id-dashboard-div .info-box-icon>i {
        line-height: 85px;
    }
    #booking-calendar span{
        font-weight: bold;
    }
    .pr-0{
        padding-right: 0!important;
    }
    .pl-0{
        padding-left: 0!important;
    }
    .p-0{
        padding: 0!important;
    }
    .m-0{
        margin: 0!important;
    }
    .mr-0{
        margin-right: 0!important;
    }
    .ml-0{
        margin-left: 0!important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow
    {
        height: 34px;
        right: 5px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 34px;
    }
    .select2-container .select2-selection--single{
        height: 34px;
    }
    .select2-container--default .select2-selection--single {
        border: 1px solid #d2d6dd;
    }
    .padfive {
        padding: 0 2px;
    }
    .id-input{
        border-radius: 4px!important;
    }
    .id-profit-main .id-profit-div{
        background-color: #f2f2f2;
        padding: 10px;
        border-radius: 20px;
        box-shadow: 3px 2px 7px rgba(0,0,0,0.1);
    }
    .id-profit-div label{
        font-size: 30px;
    }
    .id-profit-div p{
        margin: 0;
    }
    .id-profit-main {
        margin-bottom: 20px
    }
    .id-agent-div p{
        margin-bottom: 0;
    }
    .id-agent-div label{
        font-size: 20px;
        margin-bottom: 0;
    }
    .id-agent-div{
        text-align: center;
    }
    .id-agent-div .panel-body{
        padding: 10px;
    }
    .id-agent-div .panel{
        border-radius: 20px;
        background-color: #f2f2f2;
    }
    .id-deposit-div .panel-head{
        text-align: center;
        background-color: #0073b7;
        color: #fff;
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
    }
    .id-recent-book .box-header{
        color: #fff;
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
    }
    .id-recent-book{
        text-align: center;
        border-radius: 20px;
        border:1px solid #ccc;
        padding: 0;
    }
    .id-recent-book .box-footer {
        border-bottom-left-radius: 20px;
        border-bottom-right-radius: 20px;
    }
    .id-recent-book .panel-body{
        height: 200px;
        overflow-y: scroll;
    }
    .id-recent-book .panel-heading{
        border-top-right-radius: 20px;
        border-top-left-radius: 20px;
    }
    .id-recent-book .panel-default, .panel-heading, .panel{
        border-top-right-radius: 20px;
        border-top-left-radius: 20px;
    }
    .id-recent-book .id-recent-book-name{
        font-size: 18px;
        margin-bottom: 0;
        text-align: left;
        font-weight: bold;
        color: #3c8dbc;
    }
    .id-recent-book-agent-count{
        font-size: 18px;
        margin-bottom: 0;
        text-align: left;
        font-weight: bold;
        color: #b0dbf4;
    }
    .id-recent-book .col-sm-6{
        text-align: left;
    }
    .id-recent-book .id-recent-book-price{
        background-image: linear-gradient(to right, #30a65a , #98c50d);
        color: #fff;
        padding: 4px;
        border-radius: 20px;
        width: 100%;
        margin-bottom: 0;
    }
    .id-recent-book span{
        color: #666;
    }
    .id-recent-book i{
        font-size:25px;
        margin-top: 10px;
    }
    .id-recent-book .id-recent-book-b2b{
        margin-top: 10px;
        font-size: 15px;
    }
    .mt-1{
        margin-top: 10px!important;
    }
    .mb-1{
        margin-bottom: 10px!important;
    }
    .pb-0{
        padding-bottom: 0!important;
    }
    .pt-0{
        padding-top: 0!important;
    }
    .id-recent-book .box-footer{
        border-top: 0;
    }
    .id-recent-book .box-body{
        height: 1007px; 
        overflow-y: auto;
    }
</style>
