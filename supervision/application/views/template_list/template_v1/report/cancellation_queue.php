<?php
if (is_array($search_params)) {
    extract($search_params);
}
$_datepicker = array(array('created_datetime_from', PAST_DATE), array('created_datetime_to', PAST_DATE));
$this->current_page->set_datepicker($_datepicker);
$this->current_page->auto_adjust_datepicker(array(array('created_datetime_from', 'created_datetime_to')));
?>

<div class="bodyContent">
  <h3>Flight Cancellation List</h3>
    <div class="panel panel-default clearfix">
        <!-- PANEL WRAP START -->
        <!-- PANEL HEAD START -->
        <div class="panel-body">
           <div id="show-search">

                <form method="GET" autocomplete="off" action="<?= base_url() . 'report/cancellation_queue?' ?>">
                    <div class="clearfix form-group">  
                      <div class="col-xs-3">
                        <label>
                        Application Reference
                        </label>
                        <input type="text" class="form-control" name="app_reference" value="<?=@$app_reference?>" placeholder="Application Reference">
                      </div>
                      <div class="col-xs-3">
                        <label>
                        PNR
                        </label>
                        <input type="text" class="form-control" name="pnr" value="<?=@$pnr?>" placeholder="PNR">
                      </div>

                        <div class="col-xs-3">
                            <label>From Date : </label>
                            <input type="text" readonly id="created_datetime_from" class="form-control" name="created_datetime_from" value="<?= @$created_datetime_from ?>" placeholder="Request Date">
                        </div>
                        <div class="col-xs-3">
                            <label>To Date : </label>
                            <input type="text" readonly id="created_datetime_to" class="form-control disable-date-auto-update" name="created_datetime_to" value="<?= @$created_datetime_to ?>" placeholder="Request Date">
                        </div>
                    </div>

                    <div class="col-sm-offset-9 col-sm-3">
                        
                        <!-- <button type="reset" id="btn-reset" class="btn btn-warning">Reset</button> -->
                        <a href="<?= base_url() . 'report/cancellation_queue?' ?>" id="clear-filter" class="btn btn-primary btn-common">Reset</a>
                        <button type="submit" class="btn btn-danger btn-common">Search</button> 
                    </div>
                    
                </form>
            </div>
          
        </div>
        <hr>
        <div class="col-sm-12">
                     <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                           <li class="active"><a href="#activity" data-toggle="tab">Cancellation Queue List</a></li>
                           <li><a href="#settings" data-toggle="tab">Cancelled List</a></li>
                           <li><a href="#settings1" data-toggle="tab">Rejected List</a></li>
                        </ul>
                        <div class="tab-content">
                           <div class="active tab-pane" id="activity" >
                              <?php 
                                 echo $this->session->flashdata('msg');  
                                 ?>
                              <?php $attributes = ' id = "queue_form"' ;
                                 echo form_open('Account_entry/flight_cancellation_queue',$attributes);?>
                              <table id="#example1-tab2-dt" class="datatables-td datatables table table-striped table-bordered table-responsive" cellspacing="0" width="100%">
                                 <thead>
                                    <tr>
                                       <th>Sl NO</th>
                                       <th>App Reference</th>
                                        <th>Agency Id</th>
                                        <th>Supplier Name</th>
                                        <th>Airline Name</th>
                                        <!-- <th>Cancellation Type</th> -->
                                        <th>PNR</th>
                                        <th>Booking Date</th>
                                        <th>Can Requested Date</th>
                                        <th>Travel Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <?php $slno = 0; $total_amount=0; $journey_date=null; 
                                       ?>
                                    <?php foreach ($CancelQueue['processing_list'] as $row):  //echo "<pre>row==>>>"; print_r($row);die;
                                    //$customer_details = $data['booking_transaction_details'][0]['booking_customer_details'];
                                    $cancellation_type = 'Full Cancellation';
                                    // foreach($customer_details as $cus_k => $cus_val){
                                    //     if($cus_val['status'] == 'BOOKING_CONFIRMED'){
                                    //         $cancellation_type = 'Partial Cancellation';
                                    //     }
                                    // }
									$agent_details = $this->custom_db->single_table_records('user','*',array('user_id'=>$row['details']['created_by_id']))['data'][0];
									//debug($agent_details);
                                     $slno = $slno + 1; ?> 
                                    <tr>
                                       <td><?php echo $slno; ?></td>
                                       <td><?php echo $row['details']['app_reference']; ?></td>
                                       
                                       <td><?php echo $agent_details['agency_name'].'-'.provab_decrypt($agent_details['uuid']); ?></td>
                                       <td><?php 
                                       if($row['details']['booking_source'] == 'PTBSID0000000012'){ echo "Star Air"; }
                                       if($row['details']['booking_source'] == 'PTBSID0000000009'){ echo "ACH"; }
                                       if($row['details']['booking_source'] == 'PTBSID0000000010'){ echo "GDS"; }
                                       if($row['details']['booking_source'] == 'PTBSID0000000011'){ echo "SpiceJet"; }
                                       if($row['details']['booking_source'] == 'PTBSID0000000013'){ echo "Indigo"; }
                                       if($row['details']['booking_source'] == 'PTBSID0000000045'){ echo "TruJet"; }
                                       if($row['details']['booking_source'] == 'PTBSID0000000002'){ echo "TBO"; }
                                       ?></td>
                                       <td><?php echo $row['details']['airline_name']; ?></td>
                                       
                                       <td><?php echo $row['details']['book_id']; ?></td>
                                       <td>
                                          <?php  $booking_date= date('d-m-Y',strtotime($row['details']['created_datetime'])); echo  $booking_date; ?>
                                       </td>
                                       <td>
                                          <?php  $journey_date= date('d-m-Y',strtotime($row['cancel_data']['created_datetime'])); echo  $journey_date; ?>
                                       </td>
                                       <td>
                                          <?php  $cancel_req_date= date('d-m-Y',strtotime($row['details']['journey_start'])); echo  $cancel_req_date; ?>
                                       </td>
                                       <td><?php echo $row['cancel_data']['refund_status']; ?></td>
                                       

                                       <td><?= '<a href="'.base_url().'flight/process_cancellation_refund_details?app_reference='.$row['details']['app_reference'].'&booking_source='.$row['details']['booking_source'].'" class="btn btn-sm btn-warning "><i class="fa fa-info"></i> Process Cancellation</a>'; ?></td>
                                    </tr>
                                    <?php  endforeach ?>
                                 </tbody>
                              </table>
                           </div><!-- /.tab-pane -->
                           <div class="tab-pane" id="settings">
                              <table id="#example2-tab3-dt" class="datatables-td table table-striped table-bordered table-responsive" cellspacing="0" width="100%">
                                 <thead >
                                    <tr>
                                       <th>Sl NO</th>
                                       <th>App Reference</th>
                                        <th>Agency Id</th>
                                        <th>Supplier Name</th>
                                        <th>Airline Name</th>
                                        <th>Cancellation Type</th>
                                        <th>PNR</th>
                                        <th>Booking Date</th>
                                        <th>Can Requested Date</th>
                                        <th>Travel Date</th>
                                        <th>Status</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <?php $slno = 0; $total_amount=0; $journey_date=null; 
                                       ?>
                                    <?php foreach ($CancelQueue['cancelled_details'] as $row):  //echo "<pre>row==>>>"; print_r($row);die;
                                    //$customer_details = $data['booking_transaction_details'][0]['booking_customer_details'];
                                    $cancellation_type = 'Full Cancellation';
                                    // foreach($customer_details as $cus_k => $cus_val){
                                    //     if($cus_val['status'] == 'BOOKING_CONFIRMED'){
                                    //         $cancellation_type = 'Partial Cancellation';
                                    //     }
                                    // }

                                     $slno = $slno + 1; ?> 
                                    <tr>
                                       <td><?php echo $slno; ?></td>
                                       <td><?php echo $row['details']['app_reference']; ?></td>
                                       
                                       <td><?php echo $row['details']['created_by_id']; ?></td>
                                       <td><?php 
                                       if($row['details']['booking_source'] == 'PTBSID0000000012'){ echo "Star Air"; }
                                       if($row['details']['booking_source'] == 'PTBSID0000000009'){ echo "ACH"; }
                                       if($row['details']['booking_source'] == 'PTBSID0000000010'){ echo "GDS"; }
                                       if($row['details']['booking_source'] == 'PTBSID0000000011'){ echo "SpiceJet"; }
                                       if($row['details']['booking_source'] == 'PTBSID0000000013'){ echo "Indigo"; }
                                       if($row['details']['booking_source'] == 'PTBSID0000000045'){ echo "TruJet"; }
                                       ?></td>
                                       <td><?php echo $row['details']['airline_name']; ?></td>
                                       <td><?php echo $cancellation_type; ?></td>
                                       <td><?php echo $row['details']['book_id']; ?></td>
                                       <td>
                                          <?php  $booking_date= date('d-m-Y',strtotime($row['details']['created_datetime'])); echo  $booking_date; ?>
                                       </td>
                                       <td>
                                          <?php  $journey_date= date('d-m-Y',strtotime($row['cancel_data']['created_datetime'])); echo  $journey_date; ?>
                                       </td>
                                       <td>
                                          <?php  $cancel_req_date= date('d-m-Y',strtotime($row['details']['journey_start'])); echo  $cancel_req_date; ?>
                                       </td>
                                       <td title="<?php echo $row['cancel_data']['refund_status']; ?>">Successful</td>
                                    
                                    </tr>
                                    <?php  endforeach ?>
                                 </tbody>
                              </table>
                           </div><!-- /.tab-pane -->
                           <div class="tab-pane" id="settings1">
                              <table id="#example2-tab4-dt" class="datatables-td datatables table table-striped table-bordered table-responsive" cellspacing="0" width="100%">
                                 <thead >
                                    <tr>
                                       <th>Sl NO</th>
                                       <th>App Reference</th>
                                        <th>Agency Id</th>
                                        <th>Supplier Name</th>
                                        <th>Airline Name</th>
                                        <th>Cancellation Type</th>
                                        <th>PNR</th>
                                        <th>Booking Date</th>
                                        <th>Can Requested Date</th>
                                        <th>Travel Date</th>
                                        <th>Agent Comment</th>
                                        <th>Rejection Reason</th>
                                        <th>Status</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <?php $slno = 0; $total_amount=0; $journey_date=null; 
                                       ?>
                                    <?php foreach ($CancelQueue['rejected_details'] as $row):  //echo "<pre>row==>>>"; print_r($row);die;
                                    //$customer_details = $data['booking_transaction_details'][0]['booking_customer_details'];
                                    $cancellation_type = 'Full Cancellation';
                                    // foreach($customer_details as $cus_k => $cus_val){
                                    //     if($cus_val['status'] == 'BOOKING_CONFIRMED'){
                                    //         $cancellation_type = 'Partial Cancellation';
                                    //     }
                                    // }

                                     $slno = $slno + 1; ?> 
                                    <tr>
                                       <td><?php echo $slno; ?></td>
                                       <td><?php echo $row['details']['app_reference']; ?></td>
                                       
                                       <td><?php echo $row['details']['created_by_id']; ?></td>
                                       <td><?php 
                                       if($row['details']['booking_source'] == 'PTBSID0000000012'){ echo "Star Air"; }
                                       if($row['details']['booking_source'] == 'PTBSID0000000009'){ echo "ACH"; }
                                       if($row['details']['booking_source'] == 'PTBSID0000000010'){ echo "GDS"; }
                                       if($row['details']['booking_source'] == 'PTBSID0000000011'){ echo "SpiceJet"; }
                                       if($row['details']['booking_source'] == 'PTBSID0000000013'){ echo "Indigo"; }
                                       if($row['details']['booking_source'] == 'PTBSID0000000045'){ echo "TruJet"; }
                                       ?></td>
                                       <td><?php echo $row['details']['airline_name']; ?></td>
                                       <td><?php echo $cancellation_type; ?></td>
                                       <td><?php echo $row['details']['book_id']; ?></td>
                                       <td>
                                          <?php  $booking_date= date('d-m-Y',strtotime($row['details']['created_datetime'])); echo  $booking_date; ?>
                                       </td>
                                       <td>
                                          <?php  $journey_date= date('d-m-Y',strtotime($row['cancel_data']['created_datetime'])); echo  $journey_date; ?>
                                       </td>
                                       <td>
                                          <?php  $cancel_req_date= date('d-m-Y',strtotime($row['details']['journey_start'])); echo  $cancel_req_date; ?>
                                       </td>
                                       <td><?php echo $row['cancel_data']['agent_comments']; ?></td>
                                       <td><?php echo $row['cancel_data']['refund_comments']; ?></td>
                                       <td><?php echo $row['cancel_data']['refund_status']; ?></td>
                                       

                                    
                                    </tr>
                                    <?php  endforeach ?>
                                 </tbody>
                              </table>
                           </div><!-- /.tab-pane -->
                        </div><!-- /.tab-content -->
                     </div><!-- /.nav-tabs-custom -->
                  </div>
        <!-- PANEL BODY END -->
    </div>
    <!-- PANEL END -->
</div>
<style type="text/css">
  .content-wrapper{
    padding: 0!important;
  }
  .bodyContent .nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .bodyContent .nav-tabs>li.active>a:hover {
    color: #000 !important;
    font-weight: 600;
    cursor: default;
    background-color: #fff !important;
    border: 0 !important;
  }
  .nav-tabs-custom>.nav-tabs>li.active {
    border-top-color: red;
  }
  .btn-common{
    width: 47%;
    margin-left: 5px;
  }
  .panel{
    box-shadow: 0 5px 11px 0 rgba(0,0,0,0.18),0 4px 15px 0 rgba(0,0,0,0.15) !important;
  }
</style>

<script type="text/javascript">
    $(document).ready( function () {
        $('.datatables-td').DataTable();
    } );
</script>
<style type="text/css">
    .dataTables_wrapper .col-sm-12{
        min-height: .01%!important;
        overflow-x: auto!important;
    }
</style>