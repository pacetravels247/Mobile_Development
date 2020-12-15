<div class="bodyContent">
    <div class="panel panel-default clearfix">
        <!-- PANEL WRAP START -->
        <!-- PANEL HEAD START -->
        <div class="panel-body">
           
          
        </div>
        <div class="panel-body">

            <?php
            $table_header = '<tr>
							<th>S.No</th>
                            <th>App Reference</th>
							<th>PNR</th>
                                                        <th>Booking Date</th>
                                                        <th>Can Requested Date</th>
                                                        <th>Travel Date</th>
                                                        <th>Action</th>
						</tr>';
            ?>
            <div class="table-responsive">
                <table id="accounts_led" class="table table-condensed table-bordered table-striped">
                    <thead><?= $table_header ?></thead>
                    <tfoot><?= $table_header ?></tfoot>
                    <tbody>
                        <?php
                        if (valid_array($CancelQueue) == true) {
                            foreach ($CancelQueue as $CanKey => $data) {

                                extract($data);
                                 $cancellation_details = '';
                                $cancel_value = 0;
                                 foreach($data['booking_transaction_details'] as $val) {
               
                                    if(isset($val['booking_customer_details']))
                                    {
                                        foreach($val['booking_customer_details'] as $j=>$data1)
                                        {
                                            if(isset($data1['cancellation_details']) && $cancel_value == 0){
                                                $cancellation_details=$data1['cancellation_details'];
                                                $cancel_value = 1;
                                            }
                                            
                                        }  
                                    }
                
                                }
                            ?>
                                <tr>
                                    <td><?= ($CanKey + 1) ?></td>
                                    <td><?= $data['app_reference'] ?></td>
                                    <td><?= $data['pnr'] ?></td>
                                    <td><?= $data['created_datetime'] ?></td>

                                    <td>
                                        <?php
                                        echo $cancellation_details['cancellation_requested_on'];
                                      /*  foreach ($data['booking_transaction_details'][0]['booking_customer_details'] as $key => $can_data) {
                                            // echo  '<b>Pax'.($key+1).':</b> ',$can_data['cancellation_details']['cancellation_requested_on'].'<br />';
                                            echo $can_data['cancellation_details']['cancellation_requested_on'];
                                            break;
                                        }*/
                                        ?>

                                    </td>
                                    <td><?= $data['journey_start'] ?></td>

                                    <td><?= '<a target="_blank" href="' . base_url() . 'index.php/flight/ticket_cancellation_details?app_reference=' . $data['app_reference'] . '&booking_source=' . $data['booking_source'] . '&status=' . $data['status'] . '" class="col-md-12 btn btn-sm btn-info "><i class="fa fa-info"></i> <small>Cancellation Details</small></a>'; ?></td>
                                </tr>
                                <?php
                            }
                        } else {
                            if (empty($queue_error_message) == true) {
                                $queue_error_message = 'No Data Found !!';
                            }
                            ?>	<tr>
                                <td colspan="5"><?= $queue_error_message ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- PANEL BODY END -->
    </div>
    <!-- PANEL END -->
</div>
