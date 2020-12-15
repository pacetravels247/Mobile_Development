<?php
   $booking_details = $data['booking_details'][0]; 
   $itinerary_details = $booking_details['itinerary_details'][0];
   $attributes = json_decode($booking_details['attributes'],true);
   $customer_details = $booking_details['customer_details'];
   $agent_markup = $booking_details["agent_markup"];
?>
<!-- Main content -->
<section class="invoice"  style="position: relative;background: #fff;border: 1px solid #f4f4f4;padding: 20px;margin: 10px 25px;">
    <!-- title row -->
    <div class="row" style="white-space: normal;">
        <div class="col-xs-12" style="width: 100%;">
            <h2 class="page-header" style="padding-bottom: 9px;margin: 40px 0 20px;border-bottom: 1px solid #eee;">
                <i class="fa fa-globe"></i> Credit Note
                <small class="pull-right"  style="color: #666;display: block;margin-top: 5px;float: right!important;">Date :<?php echo date('d-m-Y'); ?></small>
            </h2>
        </div><!-- /.col -->
    </div>
    <!-- info row -->
    <div class="row invoice-info">
        <div class="col-sm-4 invoice-col"style="width: 33.33333333%;float: left;">
            <b style="font-weight: 700;">From</b>
            <address style="margin-bottom: 20px;font-style: normal;line-height: 1.42857143;">
               <?=$admin_details['domainname']?><br>
                Phone : <?=$admin_details['phone']?><br>
               <!--  <b>Email :</b> info@sunango.com<br>  -->
                <?=$admin_details['address']?>,<br>
                <br>
                <b>GSTIN:</b> <?=domain_gst_number()?>
            </address>
        </div><!-- /.col -->
        <?php //debug($booking_customer_details[0]);exit;   ?>
        <div class="col-sm-4 invoice-col"style="width: 33.33333333%;float: left;">
            <b style="font-weight: 700;">To</b>
            <address style="margin-bottom: 20px;font-style: normal;line-height: 1.42857143;">              
            <?php if($to_customer) { ?>
            <table width="100%">
               <tbody>
                  <tr><td><b>User name :</b> <?php echo $booking_details['lead_pax_name']; ?></td></tr>
                    <tr><td><b>Phone :</b> <?php echo $booking_details['lead_pax_phone_number']; ?></td></tr>
                    <tr><td><b>Email:</b><?php echo $booking_details['lead_pax_email']?></td></tr>
                    <tr><td><b>Address:</b><?php echo $booking_details['cutomer_address']?></td></tr>
               </tbody>
            </table>
            <?php } 
            else{ ?>
                <table width="100%">
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
       
        <div class="col-sm-4 invoice-col" style="width: 33.33333333%;float: left;">
            <b>Pace Ref No :</b><?php echo $booking_details['app_reference']; ?><br>
            <b>Booking ID :</b><?php echo $booking_details['booking_id']; ?><br>
            <b>Date :</b> <?php echo @$booking_details['created_datetime']; ?>
        </div><!-- /.col -->
    </div><!-- /.row -->

    <!-- Table row -->
    <div class="row" style="white-space: normal;">
        <div class="col-xs-12 table-responsive" style="width:100%;">
            <table class="table table-striped" width="100%">
                <thead>
                    <tr>
                        <th style="text-align: left;">S.No</th>
                        <th style="text-align: left;">Confirmation No</th>
                        <th style="text-align: left;">Room Type</th>
                        <th style="text-align: left;">Pax Name</th>
                        <th style="text-align: left;">PAx Type</th>
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
                                    <td><?php echo $itinerary_details['room_type_name']?></td>
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
    <div class="row invoice-info" style="white-space: normal;">

        <div class="col-sm-6 invoice-col" style="width:50%;float: left;">
            <b>Refund Details</b>
            <address>              
                <table>
                    <tr><td>Total Fare</td></tr>
                    <tr><td>Other Taxes</td></tr>
                    <tr><td>Cancellation Charges</td></tr>
					<tr><td>GST</td></tr>
                    <tr><td>Refund Amount</td></tr>
                </table>
            </address>
        </div><!-- /.col -->
        <div class="col-sm-6 invoice-col" style="width:50%;float: left;">
        <?php
			$gst_det = $GLOBALS["CI"]->custom_db->single_table_records("gst_master", "gst", array("module"=>"flight"));
			$gst_perc = $gst_det["data"][0]["gst"];
			$gst = 0;
			if(!$to_customer)
			{
				$booking_details['grand_total'] -= $agent_markup;
				$agent_markup = 0;
			}
			else
				$agent_markup = 0;
          $ot = $booking_details['grand_total']-(@$refund_amount+@$cancel_charge)-$agent_markup;
		  $refund_amount += $agent_markup;
       ?>
            <b>&nbsp;</b>
            <address>              
                <table>
                    <tr><td><?=@roundoff_number($booking_details['grand_total']) ?>/-</td></tr>
                    <tr><td><?=@roundoff_number($ot)?>/-</td></tr> 
                    <tr><td><?=@roundoff_number($cancel_charge) ?>/-</td></tr>
					<tr><td><?=@roundoff_number($gst) ?>/-</td></tr>
                    <tr><td><?=@roundoff_number($refund_amount) ?>/-</td></tr>
                </table>
            </address>
        </div><!-- /.col -->
    </div>
    <!-- info row -->
    <div class="row invoice-info" style="white-space: normal;">
        <div class="col-sm-12 invoice-col" style="width:100%;">
            <p>Terms & Conditions</p>
            <address>              
                <!-- <table>
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
                </table> -->
                <?=$data ['terms_conditions']?>
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