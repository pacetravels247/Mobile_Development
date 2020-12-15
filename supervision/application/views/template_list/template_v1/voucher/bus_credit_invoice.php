<?php
   $booking_details = $data['booking_details'][0];
   $itinerary_details = $booking_details['booking_itinerary_details'][0];
   $attributes = json_decode($booking_details['attributes'],true);
   $customer_details = $booking_details['booking_customer_details'];
   $base_fare = 0;
   $refund_amount = 0;
   $commission_reversed = 0;
   $cancel_charge = 0;
   foreach($cancellation_details["data"] AS $cancel_key=>$cancel_val)
   {
	   $cancel_charge += $cancel_val["cancel_charge"];
   }
?>
<div id="print_area">
<div class="pad margin no-print">
    <div style="margin-bottom: 0!important;" class="callout callout-info">
        <h4><i class="fa fa-info"></i> Note:</h4>
        This page has been enhanced for printing. Click the print button at the bottom of the invoice to test.
    </div>
</div>

<!-- Main content -->
<section class="invoice" style="position: relative;background: #fff;border: 1px solid #f4f4f4;padding: 20px;margin: 10px 25px;">
    <!-- title row -->
    <div class="row" width="100%" style="white-space: normal;">
        <div class="col-xs-12" width="100%">
            <h2 class="page-header"  style="padding-bottom: 9px;margin: 40px 0 20px;border-bottom: 1px solid #eee;">
                <i class="fa fa-globe"></i> Credit Note
                <small class="pull-right" style="color: #666;display: block;margin-top: 5px;float: right!important;">Date :<?php echo date('d-m-Y'); ?></small>
            </h2>
        </div><!-- /.col -->
    </div>
    <!-- info row -->
    <div class="row invoice-info" style="width: 100%;">
        <div class="col-sm-4 invoice-col" style="width: 33.33333333%;float: left;">
            <b style="font-weight: 700;">From</b>
            <address  style="margin-bottom: 20px;font-style: normal;line-height: 1.42857143;">
               <?=$admin_details['domainname']?><br>
                Phone : <?=$admin_details['phone']?><br>
               <!--  <b>Email :</b> info@sunango.com<br>  -->
                <?=$admin_details['address']?>,<br>
                <br>
                <b>GSTIN:</b> <?php echo domain_gst_number();?>
            </address>
        </div><!-- /.col -->
        <?php //debug($booking_customer_details[0]);exit;   ?>
        <div class="col-sm-4 invoice-col" style="width: 33.33333333%;float: left;">
            <b style="font-weight: 700;">To</b>
                         
                <address  style="margin-bottom: 20px;font-style: normal;line-height: 1.42857143;">
         <?php if($to_customer) { 
                $refund_extra_amt = $booking_details["admin_markup"]+$booking_details["agent_markup"];
            ?>
            <table width="100%">
               <tbody>
                  <tr><td><b>User name :</b> <?php echo $booking_details['lead_pax_name']; ?></td></tr>
                    <tr><td><b>Phone :</b> <?php echo $booking_details['lead_pax_phone_number']; ?></td></tr>
                    <tr><td><b>Email:</b><?php echo $booking_details['lead_pax_email']?></td></tr>
                    <tr><td><b>Address:</b><?php echo $booking_details['cutomer_address']?></td></tr>
               </tbody>
            </table>
            <?php } 
            else{ 
                $refund_extra_amt = $booking_details["admin_markup"];
                ?>
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
            <b>PNR :</b><?php echo $booking_details['pnr']; ?><br>
            <b>Date :</b> <?php echo @$booking_details['created_datetime']; ?>
        </div><!-- /.col -->
    </div><!-- /.row -->

    <!-- Table row -->
    <div class="row" style="white-space: normal;">
        <div class="col-xs-12 table-responsive" style="width: 100%">
            <table class="table table-striped" width="100%" >
                <thead>
                    <tr>
                        <th style="text-align: left;">S.No</th>
                        <th style="text-align: left;">Confirmation No</th>
                        <th style="text-align: left;">Pax Name</th>
                        <th style="text-align: left;">Gender</th>
                        <th style="text-align: left;">Seat No</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                  
                    if (isset($customer_details)) {
                        $si_no = 1; 
						$cust_count = 0;
						$cancel_pass_count = 0;
						$total_fare = 0;
						$api_tax = 0; 
						$gst = 0;
						$tds = 0;
						$commission_reversed = 0;
                        foreach ($customer_details as $key => $cus_details) {
							if($cus_details["status"] == "BOOKING_CANCELLED")
								$cancel_pass_count++;
                           if($cus_details["status"] == "BOOKING_CANCELLED" && $cus_details["origin"]==$_GET["passenger_origin"]){
							   $fare_det = json_decode($cus_details["attr"], true);
							   $total_fare = $fare_det["_AgentBuying"]+$fare_det["_Commission"]-$fare_det["_tdsCommission"];
							   $buying_price = $fare_det["_AgentBuying"];
							   if($to_customer){
								   $total_fare = $fare_det["_CustomerBuying"];
								   $buying_price = $fare_det["_CustomerBuying"];
							   }
							   $api_tax = $fare_det["_ServiceTax"];
							   $gst = $fare_det["_GST"];
							   $tds = $fare_det["_tdsCommission"];
							   $commission_reversed = $fare_det["_Commission"];
                                ?>
                                <tr>
                                    <td><?php echo $si_no; ?></td>
                                    <td><?php echo $booking_details['pnr']; ?></td>
                                    
                                    <td><?php echo $cus_details['name']; ?></td>
                                    <td><?php echo $cus_details['gender']; ?></td>
                                    <td><?php echo $cus_details['seat_no'];?></td>
                                </tr>
                                <?php
                                 $si_no++; 
                            }
							$cust_count++;
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div><!-- /.col -->
    </div><!-- /.row -->
    <div class="row invoice-info" style="white-space: normal;">
        <div class="col-sm-6 invoice-col"  style="width:50%;">
            <b>Refund Details</b>
			<?php 
				$single_cancel_charge = $cancel_charge/$cancel_pass_count;
				$refund_amount = $buying_price - $single_cancel_charge - $api_tax - $gst;
			?>
            <address>              
                <table>
                    <tr><td>Total Fare</td><td>&nbsp;</td><td><?=@roundoff_number($total_fare)?>/-</td></tr>
					<tr><td>Tax (-)</td><td>&nbsp;</td><td><?=@roundoff_number($api_tax)?>/-</td></tr>
                    <tr><td>Cancellation Charges (-)</td><td>&nbsp;</td><td><?=@roundoff_number($single_cancel_charge) ?>/-</td></tr>
                    <?php 
					if(!$to_customer) { 
					?>
                    <tr><td>Commission Reversed (-)</td><td>&nbsp;</td><td><?=@roundoff_number($commission_reversed)?>/-</td></tr>
					<tr><td>Tds (+)</td><td>&nbsp;</td><td><?=@roundoff_number($tds)?>/-</td></tr>
                    <?php } ?>
					<tr><td>GST (-)</td><td>&nbsp;</td><td><?=@$gst?>/-</td></tr>
                    <tr><td>Refund Amount</td><td>&nbsp;</td><td><?=@roundoff_number($refund_amount)?>/-</td></tr>
                </table>
            </address>
        </div>
    </div>
    <!-- info row -->
    <div class="row invoice-info" style="width: 100%;">
        <div class="col-sm-12 invoice-col" style="width:100%;">
            <p>Terms & Conditions</p>
            <address>              
                <?=$data['terms_conditions']?>
            </address>
        </div>
    </div>
    <div class="row no-print" style="width: 100%; display: none;">
        <div class="col-xs-12">
            <button class="btn btn-default" onclick="printIt('#print_area')"><i class="fa fa-print"></i> Print</button>
        </div>
    </div>
</section>
</div>
<script>
    function printIt(pa){
      var printContents = document.getElementById(pa).innerHTML;
      var originalContents = document.body.innerHTML;
      document.body.innerHTML = printContents;
      window.print();
      document.body.innerHTML = originalContents;
    }
  </script>