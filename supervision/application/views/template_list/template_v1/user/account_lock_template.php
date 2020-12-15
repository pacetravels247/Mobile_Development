<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Notification</title>
</head>
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
            <td><img src="<?php echo  $GLOBALS['CI']->template->domain_images($GLOBALS['CI']->template->get_domain_logo()); ?>" style="height:100px;" alt=" <?php echo domain_name();?>"></td>
         </tr>
         <tr>
            <td style="font-size: 15px; text-align:left;line-height:1.4;font-family:Arial,Helvetica,sans-serif;color:#656565;"><!--<h1 style="margin: 25px 0px;">Welcome to Book my Holidays</h1>-->
Dear <strong><?php echo $first_name.' '.$last_name; ?>,</strong>
<br/><br/>
Due to non payment of outstanding dues you will not be allowed to book. Please make payment of Rs. <?php echo $other_details["due_amount"]; ?>
<br/>
<br/><br/>
<table width="100%" style="border: 1px solid #DDDDDD; font-size: 13px; color: #656565; font-family: arial;" cellpadding="5">
   <tr>
      <td><strong>Login</strong></td>
      
   </tr>
   <tr>
      <td><strong>Username</strong></td>
      <td><?php echo $data["user_name"];?></td>
   </tr>

</table>

<br/>
<br/>
Regards, <br/>
Pace Travels,
Belagavi.<br/>
<img src="<?php echo  $GLOBALS['CI']->template->domain_images($GLOBALS['CI']->template->get_domain_logo()); ?>" style="height:40px;width:200px;" alt="<?php echo domain_name();?>">
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
