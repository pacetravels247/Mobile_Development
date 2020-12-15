<!DOCTYPE html>
<html>

<body>




<table width="100%">
   <tr>
      <td>
<table width="80%" border="0" style="border-collapse: collapse; border: 1px solid #dddddd; box-shadow:0px 0px 13px #dddddd;" cellspacing="0" cellpadding="0" align="center">
<tbody>
   <tr>
      <td style="padding: 15px;">
      <table width="100%">
           <tr>
            <td><img src="<?php echo  $GLOBALS['CI']->template->domain_images($GLOBALS['CI']->template->get_domain_logo()); ?>" style="height:100px;" alt="Travelomatix"></td>
         </tr>
         <tr>
            <td style="font-size: 15px; text-align:left;line-height:1.4;font-family:Arial,Helvetica,sans-serif;color:#656565;"><!--<h1 style="margin: 25px 0px;">Welcome to Book my Holidays</h1>-->
<br/>
Dear <strong><?php echo $master_transaction['request_user']; ?>,</strong>
<br/>
<?php 
$amount  = $master_transaction['amount'] * $master_transaction['currency_conversion_rate'];

?>
Your  balance request for a amount <?php echo $master_transaction['currency']; ?><?php echo $amount; ?>  has been processed on <?php echo app_friendly_absolute_date($master_transaction['updated_datetime']); ?>.

<br/>
<br/>
<br/>
<br/>
    </td>
         </tr>
         <tr><td></td></tr>
         <tr>
            <td>
              
            </td>
         </tr>
      </table>
      </td>
</tr>
</tbody>
</table>
</td>
   </tr>
</table>
</body>
</html>


