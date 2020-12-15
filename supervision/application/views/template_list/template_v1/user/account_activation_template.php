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
Welcome to  <?php echo domain_name();?>   Your login information is mentioned below. Please keep your username and password secure. You should change this password immediately and also consider changing your password every two weeks. 
<br/>The website is still in its infancy so you might occasionally run in to some technical errors. For any other further assistance related to travel portal account do get in touch with our support team, 
<a style="color:#656565;" target="_top" href="mailto:support@travelomatix.com">support@travelomatix.com</a>
<br/>
<br/><br/>
<table width="100%" style="border: 1px solid #DDDDDD; font-size: 13px; color: #656565; font-family: arial;" cellpadding="5">
   <tr>
      <td><strong>Login</strong></td>
      
   </tr>
   <tr>
      <td><strong>Username</strong></td>
      <td><?php echo $user_name;?></td>
   </tr>

</table>

<br/>
<br/>
Regards, <br/>
2nd Floor, Venkatadri IT Park, HP Avenue, Konnappana Agrahara, <br/>  Electronic City, Bengaluru, Karnataka 560100.<br/>
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
