<?php
   $booking_details = $data['booking_details'][0]; 
   $itinerary_details = $booking_details['itinerary_details'][0];
   $attributes = json_decode($booking_details['attributes'],true);
   $customer_details = $booking_details['customer_details'];

?>

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
                <i class="fa fa-globe"></i> Sale Invocie
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
            </address>
        </div><!-- /.col -->
       
        <div class="col-sm-4 invoice-col">
            <b>TMX App Ref No :</b><?php echo $booking_details['app_reference']; ?><br>
            <b>Booking ID :</b><?php echo $booking_details['confirmation_reference']; ?><br>
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
                                    <td><?php echo $booking_details['confirmation_reference']; ?></td>
                                   
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

    <?php 

        #debug($domain_details['state']);exit;
    //debug($booking_transaction_details); exit;?>
    <!-- info row -->
    <div class="row invoice-info">
        <div class="col-sm-6 invoice-col" style="width:50%">
            <b>GST Details</b>
            <address>              
                <table>
                    <tr><td>Service Charge Collected </td></tr>
                    <?php if ($admin_details['state'] == "Karnataka") { ?>


                        <tr><td>Add : CGST @ 9% </td></tr>
                        <tr><td>Add : SGST @ 9% </td></tr>
                    <?php } else { ?>
                        <tr><td>Add : IGST @ 18% </td></tr>
                    <?php } ?>
                    <tr><td>Total GST </td></tr>

                </table>
            </address>
        </div><!-- /.col -->
        <?php// debug($booking_transaction_details);exit; ?>
        <div class="col-sm-6 invoice-col" style="width:50%">
            <b>&nbsp;</b>
           
            <?php if (($booking_details['admin_markup'] + $booking_details['agent_markup']) < 0) { ?>

                <address>              
                    <table>
                        <tr><td>0</td></tr>
                        <?php if ($admin_details['state'] == "Karnataka") { ?>
                            <tr><td>0</td></tr>
                            <tr><td>0</td></tr>
                        <?php } else { ?>
                            <tr><td>0</td></tr>
                        <?php } ?>
                        <tr><td>0</td></tr>

                    </table>
                </address>
            <?php } else { ?>
                <address>              
                    <table>


                        <?php

                        //debug($booking_transaction_details);
                        
                        $Markup_without_GST = 0;
                        if(round($booking_details['gst'])>0){
                            $Markup_without_GST = $booking_details['gst'];   
                        }
                        ?>

                        <tr><td><?php echo number_format(($Markup_without_GST), 2, '.', ''); ?></td></tr>
    <?php
    if ($admin_details['state'] == "Karnataka") {
        ?>
                            <tr><td><?php echo $cdst =number_format(($Markup_without_GST), 2, '.', ''); ?></td></tr>
                            <tr><td><?php echo $sgst = number_format(($Markup_without_GST), 2, '.', '');?></td></tr>
                        <?php } else { ?>
                            <tr><td><?php echo $igst = number_format(($Markup_without_GST), 2, '.', '');?></td></tr>
                        <?php } ?>
                        <tr><td><?php
                    if ($admin_details['state'] == "Karnataka") {
                        $TotalGST = $cdst + $sgst;
                    } else {
                        $TotalGST = $igst;
                    }

                    echo number_format(($TotalGST), 2, '.', '');
                        ?></td></tr>

                    </table>
                </address>
                            <?php } ?>
        </div><!-- /.col -->


    </div><!-- /.row -->


    <div class="row invoice-info">

        <div class="col-sm-6 invoice-col" style="width:50%">
            <b>Fare Details</b>
            <address>              
                <table>
                    <tr><td>Gross Total</td></tr>
                    <tr><td>Add : Service Charge Collected</td></tr>
                    <tr><td>Add : GST on service charge</td></tr>
                    <tr><td>Less : Commission Earned</td></tr>
                    <tr><td>Add : TDS Deducted</td></tr>
                    <tr><td>Round Off</td></tr>
                    <tr><td>Net Amount</td></tr>

                </table>
            </address>
        </div><!-- /.col -->
<?php

if($module=='b2b'){
        $TotalDisplayFare =$booking_details['grand_total'];

    }else{
        $TotalDisplayFare = ($booking_details['product_total_price']+$booking_details['convinence_amount']-$booking_details['discount']);

    }
    

$total = $TotalDisplayFare+$TotalGST;

$total = number_format($total, 2, '.', '');
$AgentCommission =$booking_details['agent_commission'];
$Agent_tds  = $booking_details['agent_tds'];
$negative_markup =0;

?>
        <div class="col-sm-6 invoice-col" style="width:50%">
            <b>&nbsp;</b>
            <address>              
                <table>
                    <tr><td><?php echo number_format(($TotalDisplayFare), 2, '.', ''); ?></td></tr>
                    <tr><td><?php
        if (($Markup_without_GST) < 0) {
            echo '0';
        } else {
            echo number_format(($Markup_without_GST), 2, '.', '');
        }
?></td></tr>
                    <tr><td><?php echo number_format($TotalGST, 2, '.', ''); ?></td></tr>
                    <tr><td><?php echo number_format($AgentCommission,2,'.','')?></td></tr> 
                 
                    <tr><td><?php echo number_format($Agent_tds,2,'.','')?></td></tr>
                    
                    <tr><td>0.00</td></tr>
                    <tr><td><?php echo number_format(($total), 2, '.', ''); ?></td></tr>

                </table>
            </address>
        </div><!-- /.col -->


    </div>

    <div class="row">
        <div class="col-sm-12 invoice-col" style=" width: 100%;"> 
            <div><b>Total Rupees in world: <?php echo getIndianCurrency($total); ?></b></div><br />
        </div>
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
    <div class="row no-print">
        <div class="col-xs-12">
            <a class="btn btn-default" onclick="window.print()"><i class="fa fa-print"></i> Print</a>
<?php // echo flight_inovice_pdf($booking_details['app_reference'], $booking_details['booking_source'], $booking_details['status']);   ?>
            <!-- <button style="margin-right: 5px;" class="btn btn-primary pull-right"><i class="fa fa-download"></i> Generate PDF</button> -->
        </div>
    </div>
</section><!-- /.content -->


    <?php


function getIndianCurrency($number) {
    $decimal = round($number - ($no = floor($number)), 2) * 100;
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
    $words = array(0 => '', 1 => 'One', 2 => 'Two',
        3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
        7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
        10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
        13 => 'Thirteen', 14 => 'fourteen', 15 => 'Fifteen',
        16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
        19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
        40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
        70 => 'Seventy', 80 => 'eighty', 90 => 'Ninety');
    $digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
    while ($i < $digits_length) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str [] = ($number < 21) ? $words[$number] . ' ' . $digits[$counter] . $plural . ' ' . $hundred : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
        } else
            $str[] = null;
    }
    $Rupees = implode('', array_reverse($str));
    $paise = ($decimal) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
    echo ($Rupees ? $Rupees . 'Rupees ' : '') . $paise;
}

function flight_inovice_pdf($app_reference, $booking_source = '', $status = '') {

    return '<a href="' . flight_invoice_url($app_reference, $booking_source, $status) . '/show_pdf" target="_blank" class="pull-right"><i class="fa fa-download"></i> Generate PDF</a>';
}
?>
