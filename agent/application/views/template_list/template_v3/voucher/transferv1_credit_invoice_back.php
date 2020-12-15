<?php
   $booking_details = $data['booking_details'][0]; 
   $itinerary_details = $booking_details['itinerary_details'][0];
   $attributes = json_decode($booking_details['attributes'],true);
   $customer_details = $booking_details['customer_details'];
?>
<!-- Main content -->
<section class="invoice">
    <!-- title row -->
    <div class="row">
        <div class="col-xs-12">
            <h2 class="page-header">
                <i class="fa fa-globe"></i> Credit Note
                <small class="pull-right">Date :<?php echo date('d-m-Y'); ?></small>
            </h2>
        </div><!-- /.col -->
    </div>
    <!-- info row -->
    <div class="row invoice-info">
        <div class="col-sm-4 invoice-col">
            <b>From</b>
            <address>
               <?=$admin_details['domainname']?><br>
                Phone : <?=$admin_details['phone']?><br>
               <!--  <b>Email :</b> info@sunango.com<br>  -->
                <?=$admin_details['address']?>,<br>
                <br>
                <b>GSTIN:</b> <?=domain_gst_number()?>
            </address>
        </div><!-- /.col -->
        <?php //debug($booking_customer_details[0]);exit;   ?>
        <div class="col-sm-4 invoice-col">
            <b>To</b>
            <address>              
            <?php if($to_customer) { ?>
            <table>
               <tbody>
                  <tr><td><b>User name :</b> <?php echo $booking_details['lead_pax_name']; ?></td></tr>
                    <tr><td><b>Phone :</b> <?php echo $booking_details['lead_pax_phone_number']; ?></td></tr>
                    <tr><td><b>Email:</b><?php echo $booking_details['lead_pax_email']?></td></tr>
                    <tr><td><b>Address:</b><?php echo $booking_details['cutomer_address']?></td></tr>
               </tbody>
            </table>
            <?php } 
            else{ ?>
                <table>
                    <tr><td><b>User name :</b> <?php echo $data['domainname']; ?></td></tr>
                    <tr><td><b>Address :</b> <?php echo $data['address']; ?></td></tr>

                    <?php if (isset($data['state'])) { ?>
                        <tr><td><b>State :</b> <?php echo $data['state']; ?></td></tr>
                    <?php } ?>
                     <?php if (isset($data['domaincountry'])) { ?>
                        <tr><td><b>Country :</b> <?php echo $data['domaincountry']; ?></td></tr>
                    <?php } ?>
                    <tr><td></td></tr>

                </table>
            <?php } ?>
            </address>
        </div><!-- /.col -->
       
        <div class="col-sm-4 invoice-col">
            <b>Pace Ref No :</b><?php echo $booking_details['app_reference']; ?><br>
            <b>Booking ID :</b><?php echo $booking_details['booking_id']; ?><br>
            <b>Date :</b> <?php echo @$booking_details['created_datetime']; ?>
        </div><!-- /.col -->
    </div><!-- /.row -->

    <!-- Table row -->
    <div class="row">
        <div class="col-xs-12 table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Confirmation No</th>
                        <th>Room Type</th>
                        <th>Pax Name</th>
                        <th>PAx Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                  
                    if (isset($customer_details)) {
                        foreach ($customer_details as $key => $cus_details) {
                           
                                ?>
                                <tr>
                                    <td><?php echo $key + 1; ?></td>
                                    <td><?php echo $booking_details['booking_id']; ?></td>
                                    <td><?php echo $booking_details['product_name']; ?></td>
                                    <td><?php echo $cus_details['title'] . ' ' . $cus_details['first_name'] . ' ' . $cus_details['last_name']; ?></td>
                                    <td><?php echo $cus_details['pax_type']; ?></td>
                                </tr>
                                <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div><!-- /.col -->
    </div><!-- /.row -->
    <div class="row invoice-info">

        <div class="col-sm-6 invoice-col" style="width:50%">
            <b>Refund Details</b>
            <address>              
                <table>
                    <tr><td>Total Fare</td></tr>
                    <tr><td>Other Taxes</td></tr>
                    <tr><td>Cancellation Charges</td></tr>
                    <tr><td>Refund Amount</td></tr>
                </table>
            </address>
        </div><!-- /.col -->
        <div class="col-sm-6 invoice-col" style="width:50%">
        <?php
          $ot = $booking_details['grand_total']-(@$refund_amount+@$cancel_charge);
       ?>
            <b>&nbsp;</b>
            <address>              
                <table>
                    <tr><td><?=@roundoff_number($booking_details['grand_total']) ?>/-</td></tr>
                    <tr><td><?=@roundoff_number($ot)?>/-</td></tr> 
                    <tr><td><?=@roundoff_number($cancel_charge) ?>/-</td></tr>
                    <tr><td><?=@roundoff_number($refund_amount) ?>/-</td></tr>
                </table>
            </address>
        </div><!-- /.col -->
    </div>
    <!-- info row -->
    <div class="row invoice-info">
        <div class="col-sm-12 invoice-col" style="width:100%;">
            <p>Terms & Conditions</p>
            <address>              
                <table>
                    <tr><td>* All Cases Disputes are subject to Bengaluru Jurisdiction.</td></tr>
                    <tr><td>* Refunds Cancellations are subject to Hotel's approval.
                        </td></tr>
                    <tr><td>* Kindly check all details carefully to avoid unnecessary complications.</td></tr>
                    <tr><td>* CHEQUE : Must be drawn in favour of "<?=$admin_details['domainname']?>".</td></tr>
                    <tr><td>* LATE PAYMENT : Interest @ 24% per annum will be charged on all outstanding bills after due date.</td></tr>
                    <tr><td>* Service charges as included above are to be collected from the customers on our behalf.
                            .</td></tr>
                    <tr><td>* Kindly check all details carefully to avoid un-necessary complications.</td></tr>
                    <tr><td>* Any Disputes or variations should be brought to our notice with in 15 days of the invoice.</td></tr>
                </table>
            </address>
        </div><!-- /.col -->


    </div><!-- /.row -->
    <!-- this row will not appear when printing -->
    <div class="row no-print" style="display:none;">
        <div class="col-xs-12">
            <a class="btn btn-default" onclick="window.print()"><i class="fa fa-print"></i> Print</a>
        </div>
    </div>
</section><!-- /.content -->