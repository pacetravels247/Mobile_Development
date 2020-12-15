<div class="content-wrapper" style="min-height: 1096px;">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>Invoice<small>#007612</small></h1>
        </section>

        <div class="pad margin no-print">
          <div style="margin-bottom: 0!important;" class="callout callout-info">
            <h4><i class="fa fa-info"></i> Note:</h4>
            This page has been enhanced for printing. Click the print button at the bottom of the invoice to test.
          </div>
        </div>

        <!-- Main content -->
        <section class="invoice">
          <!-- title row -->
          <div class="row">
            <div class="col-xs-12">
              <h2 class="page-header">
                <i class="fa fa-globe"></i> <?=domain_name()?>
                <small class="pull-right">Date:<?php echo date('d-m-Y');?></small>
              </h2>
            </div><!-- /.col -->
          </div>
          <!-- info row -->
          <div class="row invoice-info">
            <div class="col-sm-4 invoice-col">
              From
              <address>
                <strong><?=domain_name()?></strong><br>
                Phone: +91 123-4567-890<br>
                Email: info@<?=domain_name()?>
              </address>
            </div><!-- /.col -->
            <div class="col-sm-4 invoice-col">
              To
              <address>              
                <table>
                 <tr><td><?php echo "<strong>".$booking_pax_details[0]['first_name'].$booking_pax_details[0]['last_name']."</strong><br>Phone: +91".$booking_details['phone']."<br>Email:".$booking_details['email'];?></td></tr>
				<tr><td><?php ?></td></tr>
				
                </table>
              </address>
            </div><!-- /.col -->
            <div class="col-sm-4 invoice-col">
              <b>Invoice #<?php echo $booking_details['booking_source'];?></b><br>
              <b>Passport No:</b><?php echo $booking_pax_details[0]['passport_number'];?><br>
              <b>Payment Due:</b><?php echo date("d-m-Y",strtotime($booking_itinerary_details[0]['departure_datetime'])); ?><br>
              <b>Account:</b> 968-34567
            </div><!-- /.col -->
          </div><!-- /.row -->

          <!-- Table row -->
          <div class="row">
            <div class="col-xs-12 table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>From</th>
                    <th>To</th>
                    <th>Departure Time</th>
                    <th>Boarding Point</th>
                    <th>Booking Date</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><?php echo ucfirst($booking_itinerary_details[0]['from_airport_code'].' : '.$booking_itinerary_details[0]['from_airport_name'])?></td>
                    <td><?php echo ucfirst($booking_itinerary_details[0]['to_airport_code'].' : '.$booking_itinerary_details[0]['to_airport_name'])?></td>
                    <td><?php echo date("l, M jS Y",strtotime($booking_itinerary_details[0]['departure_datetime']))?></td>
                    <td><?php echo ucfirst($booking_itinerary_details[0]['from_airport_code'].' : '.$booking_itinerary_details[0]['from_airport_name'])?></td>
                    <td><?php echo $booking_details['currency'].' '.($booking_details['total_fare']+$booking_details['domain_markup']+$booking_details['level_one_markup'])?></td>
                  </tr>
                </tbody>
              </table>
            </div><!-- /.col -->
          </div><!-- /.row -->

          <div class="row">
            <!-- accepted payments column -->
            <div class="col-xs-6">
              <p class="lead">Payment Methods:</p>
              <?php
              $template_img_dir = $GLOBALS['CI']->template->template_images(); 
              ?>
              <img alt="Visa" src="<?=$template_img_dir?>payment/visa.png">
              <img alt="Mastercard" src="<?=$template_img_dir?>payment/mastercard.png">
              <img alt="American Express" src="<?=$template_img_dir?>payment/american-express.png">
              <img alt="Paypal" src="<?=$template_img_dir?>payment/paypal2.png">
              <p style="margin-top: 10px;" class="text-muted well well-sm no-shadow">
                Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles, weebly ning heekya handango imeem plugg dopplr jibjab, movity jajah plickers sifteo edmodo ifttt zimbra.
              </p>
            </div><!-- /.col -->
            <div class="col-xs-6">
              <p class="lead">Amount Due: <?php echo "<strong>".date("d-m-Y",strtotime($booking_itinerary_details[0]['departure_datetime'])); ?></p>
              <div class="table-responsive">
                <table class="table">
                  <tbody><tr>
                    <th style="width:50%">Total:</th>
                     <td><?php echo $booking_details['currency'].($booking_details['total_fare']+$booking_details['domain_markup']+$booking_details['level_one_markup'])?></td>
                  </tr>
                </tbody></table>
              </div>
            </div><!-- /.col -->
          </div><!-- /.row -->

          <!-- this row will not appear when printing -->
          <div class="row no-print">
            <div class="col-xs-12">
              <a class="btn btn-default" onclick="window.print()"><i class="fa fa-print"></i> Print</a>
              <button class="btn btn-success pull-right"><i class="fa fa-credit-card"></i> Submit Payment</button>
              <button style="margin-right: 5px;" class="btn btn-primary pull-right"><i class="fa fa-download"></i> Generate PDF</button>
            </div>
          </div>
        </section><!-- /.content -->
      </div>
