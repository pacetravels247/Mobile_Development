<?php
$active_domain_modules = $this->active_domain_modules;
$tiny_loader = $GLOBALS['CI']->template->template_images('tiny_loader_v1.gif');
$tiny_loader_img = '<img src="' . $tiny_loader . '" class="loader-img" alt="Loading">';
$booking_summary = array();
?>


<div class="clearfix"></div>
<div class="container-fluid">
    <div class="row">
        <?php if (is_active_airline_module()) { ?>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon flight-l-bg"><i class="<?= get_arrangement_icon(META_AIRLINE_COURSE) ?>"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-bold">Flight</span>
                        <span>Booking : <?php echo $daily_data['flight_daily_rp'][0]['count_cnf_book'];?></span></br>
                        <span>Cancellation : <?php echo $daily_data['flight_daily_rp'][0]['count_cnl_book'];?></span></br>
                        <span>Seats : <?php echo $daily_data['flight_daily_rp'][0]['no_of_seats'];?></span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div>
        <?php } ?>
        <?php if (is_active_hotel_module()) { ?>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon hotel-l-bg"><i class="<?= get_arrangement_icon(META_ACCOMODATION_COURSE) ?>"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-bold">Hotel</span>
                        <span>Booking : <?php echo $daily_data['hotel_daily_rp'][0]['count_cnf_book'];?></span></br>
                        <span>Cancellation : <?php echo $daily_data['hotel_daily_rp'][0]['count_cnl_book'];?></span></br>
                        <span>Rooms : <?php echo $daily_data['hotel_daily_rp'][0]['no_of_rooms'];?></span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div>
        <?php } ?>
        <?php if (is_active_bus_module()) { ?>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bus-l-bg"><i class="<?= get_arrangement_icon(META_BUS_COURSE) ?>"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-bold">Bus</span>
                        <span>Booking : <?php echo $daily_data['bus_daily_rp'][0]['count_cnf_book'];?></span></br>
                        <span>Cancellation : <?php echo $daily_data['bus_daily_rp'][0]['count_cnl_book'];?></span></br>
                        <span>Rooms : <?php echo $daily_data['bus_daily_rp'][0]['no_of_seats'];?></span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div>
        <?php } ?>

        <?php if (is_active_transferv1_module()) { ?>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon transfers-l-bg"><i class="<?= get_arrangement_icon(META_TRANSFERV1_COURSE) ?>"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-bold">Transfer</span>
                        <span>Booking : <?php echo $daily_data['transfer_daily_rp'][0]['count_cnf_book'];?></span></br>
                        <span>Cancellation : <?php echo $daily_data['transfer_daily_rp'][0]['count_cnl_book'];?></span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div>
        <?php } ?>

        <?php if (is_active_sightseeing_module()) { ?>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon sightseeing-l-bg"><i class="<?= get_arrangement_icon(META_SIGHTSEEING_COURSE) ?>"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text text-bold">Activities</span>
                        <span>Booking : <?php echo $daily_data['activities_daily_rp'][0]['count_cnf_book'];?></span></br>
                        <span>Cancellation : <?php echo $daily_data['transfer_daily_rp'][0]['count_cnl_book'];?></span>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div>
        <?php } ?>

        <?php if (is_active_car_module()) { ?>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon sightseeing-l-bg"><i class="<?= get_arrangement_icon(META_CAR_COURSE) ?>"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Car Booking</span>
                        <span class="info-box-number <?= META_CAR_COURSE ?>"><?= $car_booking_count ?></span>
                        <a href="<?= base_url() ?>index.php/report/b2c_car_report" class="">B2C Report 
                        </a></br>
                        <a href="<?= base_url() ?>index.php/report/b2b_car_report" class="">Agent Report</a>
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div>
        <?php } ?>

        <?php if (is_active_package_module()) { ?>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-suitcase"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Holiday Enquiry</span>
                        <a href="<?= base_url() ?>index.php/supplier/enquiries" class="">Enquiries
                        </a></br>
                        <!--<a href="<?= base_url() ?>index.php/report/b2b_package_report" class="">Agent Report</a>-->
                    </div><!-- /.info-box-content -->
                </div><!-- /.info-box -->
            </div>
        <?php } ?>

        </div>
</div>



<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">    
           <div class="panel panel-default">
                <div class="panel-head" style="padding-top: 2%">
                    <div class="col-sm-3">
                        <!-- <label>Time</label> -->
                        <select class="form-control" name="time_period" id="time_period">
                            <option value="week">week</option>
                            <option value="month">month</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <input type ="text" name="start_date" class="form-control" placeholder="start date" id="s_date">
                    </div>
                    <div class="col-sm-3">
                        <input type ="text" name="end_date" class="form-control" placeholder="end date" id="e_date">
                    </div>
                     <div class="col-sm-3">
                        <input type ="button" class="btn btn-success" id="search" value="Search">
                    </div>
                </div>
                <hr>
                <div class="panel-body">
                    <div id='booking-calendar' class="">
                        <div id="container"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">    
           <div class="panel panel-default">
                <div class="panel-head" style="padding-top: 2%">
                    <h4 style="text-align: center">Total Bookings</h4>
                </div>
                <hr>
                <?php //debug($month_compare);die(); ?>
                <div class="panel-body">
                    <div id='booking-calendar' class="">
                        <label>Flight</label><span class="label-default pull-right" id="f_values"><?php echo $month_compare['current_month'][0]['curnt_mnth_f_b'] .'/'.$month_compare['previous_month'][0]['prev_mnth_f_b']; ?></span>
                        <div class="progress">
                          <div class="progress-bar progress-bar-default" role="progressbar" aria-valuenow="40"
                          aria-valuemin="0" aria-valuemax="100" style="width:<?php echo ($month_compare['current_month'][0]['curnt_mnth_f_b']/$month_compare['previous_month'][0]['prev_mnth_f_b'])*100?>%">
                          </div>
                        </div>

                        <label>Hotel</label><span class="label-success pull-right" id="h_values"><?php echo $month_compare['current_month'][0]['curnt_mnth_h_b'] .'/'.$month_compare['previous_month'][0]['prev_mnth_h_b']; ?></span>
                        <div class="progress">
                          <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="50"
                          aria-valuemin="0" aria-valuemax="100" style="width:<?php echo ($month_compare['current_month'][0]['curnt_mnth_h_b']/$month_compare['previous_month'][0]['prev_mnth_h_b'])*100?>%">
                          </div>
                        </div>

                        <label>Bus</label><span class="label-danger pull-right" id="b_values"><?php echo $month_compare['current_month'][0]['curnt_mnth_b_b'] .'/'.$month_compare['previous_month'][0]['prev_mnth_b_b']; ?></span></label>
                        <div class="progress">
                          <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="60"
                          aria-valuemin="0" aria-valuemax="100" style="width:<?php echo ($month_compare['current_month'][0]['curnt_mnth_b_b']/$month_compare['previous_month'][0]['prev_mnth_b_b'])*100?>%">
                          </div>
                        </div>

                        <label>Activities</label><span class="label-warning pull-right" id="a_values"><?php echo $month_compare['current_month'][0]['curnt_mnth_a_b'] .'/'.$month_compare['previous_month'][0]['prev_mnth_a_b']; ?></span></label>
                        <div class="progress">
                          <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="70"
                          aria-valuemin="0" aria-valuemax="100" style="width:<?php echo ($month_compare['current_month'][0]['curnt_mnth_a_b']/$month_compare['previous_month'][0]['prev_mnth_a_b'])*100?>%">
                          </div>
                        </div>

                   
                        <label>Transfer</label><span class="label-info pull-right" id="t_values"><?php echo $month_compare['current_month'][0]['curnt_mnth_t_b'] .'/'.$month_compare['previous_month'][0]['prev_mnth_t_b']; ?></span></label>
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
        <div class="col-sm-12">
            <div class="panel panel-body" style="text-align: center">
                <div class="col-sm-3">
                    <label>TOTAL Purchasing Cost</label><br>
                    <label><?php echo $total_purchase; ?></label>
                </div>
                <div class="col-sm-3">
                    <label>TOTAL Selling Cost</label><br>
                    <label><?php echo $total_sales; ?></label>
                </div>
                <div class="col-sm-3">
                    <label>TOTAL Refund</label><br>
                    <label><?php echo $total_refund; ?></label>
                </div>
                <div class="col-sm-3">
                    <label>TOTAL PROFIT</label><br>
                    <label><?php echo $total_profit; ?></label>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4">
            <div class="col-sm-12 info-box">
                <span class="info-box-icon flight-l-bg"><i class="<?= get_arrangement_icon(META_AIRLINE_COURSE) ?>"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-bold">Flight</span>
                    <span>Purchase Amount : <?php echo $flight_transc['admin_purchase'];?></span></br>
                    <span>Sales Amount : <?php echo $flight_transc['admin_sales'];?></span></br>
                    <span>Profit Amount : <?php echo $flight_transc['admin_profit'];?></span>
                </div><!-- /.info-box-content -->
            </div><!-- /.info-box -->

            <?php if (is_active_hotel_module()) { ?>
                <div class="col-sm-12">
                    <div class="info-box">
                        <span class="info-box-icon hotel-l-bg"><i class="<?= get_arrangement_icon(META_ACCOMODATION_COURSE) ?>"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text text-bold">Hotel</span>
                            <span>Purchase Amount : <?php echo $hotel_transc['admin_purchase'];?></span></br>
                            <span>Sales Amount : <?php echo $hotel_transc['admin_sales'];?></span></br>
                            <span>Profit Amount : <?php echo $hotel_transc['admin_profit'];?></span>
                        </div><!-- /.info-box-content -->
                    </div><!-- /.info-box -->
                </div>
            <?php } ?>

             <?php if (is_active_bus_module()) { ?>
                <div class="col-sm-12">
                    <div class="info-box">
                        <span class="info-box-icon bus-l-bg"><i class="<?= get_arrangement_icon(META_BUS_COURSE) ?>"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text text-bold">Bus</span>
                            <span>Purchase Amount : <?php echo $bus_transc['admin_purchase'];?></span></br>
                            <span>Sales Amount : <?php echo $bus_transc['admin_sales'];?></span></br>
                            <span>Profit Amount : <?php echo $bus_transc['admin_profit'];?></span>
                        </div><!-- /.info-box-content -->
                    </div><!-- /.info-box -->
                </div>
            <?php } ?>

            <?php if (is_active_transferv1_module()) { ?>
                <div class="col-sm-12">
                    <div class="info-box">
                        <span class="info-box-icon transfers-l-bg"><i class="<?= get_arrangement_icon(META_TRANSFERV1_COURSE) ?>"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text text-bold">Transfer</span>
                            <span>Purchase Amount : <?php echo $transfer_transc['admin_purchase'];?></span></br>
                            <span>Sales Amount : <?php echo $transfer_transc['admin_sales'];?></span><br>
                            <span>Profit Amount : <?php echo $transfer_transc['admin_profit'];?></span>
                        </div><!-- /.info-box-content -->
                    </div><!-- /.info-box -->
                </div>
            <?php } ?>

            <?php if (is_active_sightseeing_module()) { ?>
                <div class="col-sm-12">
                    <div class="info-box">
                        <span class="info-box-icon sightseeing-l-bg"><i class="<?= get_arrangement_icon(META_SIGHTSEEING_COURSE) ?>"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text text-bold">Activities</span>
                            <span>Purchase Amount : <?php echo $activities_transc['admin_purchase'];?></span></br>
                            <span>Sales Amount : <?php echo $activities_transc['admin_sales'];?></span><br>
                            <span>Profit Amount : <?php echo $activities_transc['admin_profit'];?></span>
                        </div><!-- /.info-box-content -->
                    </div><!-- /.info-box -->
                </div>
            <?php } ?>
            
        </div>
        <div class="col-sm-8">
            <div class="panel panel-default">
                <div class="panel-head" style="text-align: center;">
                    <label><h4>Payment Deposit Details</h4></label>
                </div>
                <hr>
                <div class="panel-body">
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
                            <tbody>
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
            <div class="panel panel-default" style="text-align: center;">
                <h4>Agent Details</h4>
                <div class="panel-body">
                    <div class="col-sm-4">
                        <label>Total Agents</label><br>
                        <label class="text-bold"><?php echo $agent_data[0]['total_agents'];?></label>
                    </div>
                    <div class="col-sm-2">
                        <label>Inactive</label><br>
                        <label class="text-bold"><?php echo $agent_data[0]['inactive_agents'];?></label>
                    </div>
                    <div class="col-sm-2">
                        <label>New</label><br>
                        <label class="text-bold"><?php echo $agent_data[0]['new_agents'];?></label>
                    </div>
                    <div class="col-sm-2">
                        <label>Locked</label><br>
                        <label class="text-bold"><?php echo $agent_data[0]['locked_agent'];?></label>
                    </div>
                     <div class="col-sm-2">
                        <label>Unlocked</label><br>
                        <label class="text-bold"><?php echo $agent_data[0]['unlocked_agent'];?></label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="clearfix">
    <div class="row">
        <div class="col-sm-7">
        <h3 class="box-title">Supplier Balance Details</h3>
        <?php echo $api_balances; ?>
        </div>
        <div class="col-sm-5">
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
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Recent Booking Transactions</h3>
                    </div>
                    <div class="box-body">
                        <ul class="products-list product-list-in-box">
                            <?= $latest_trans_list ?>
                        </ul>
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
})

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



