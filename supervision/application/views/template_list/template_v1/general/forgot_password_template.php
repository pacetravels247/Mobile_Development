<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Notification</title>
</head>

<body>
<div>
<p>Dear <?php echo $first_name.' '.$last_name; ?>,</p>
<hr>
<p>As Per Your Request Your Password Has Been Reset Successfully !!!</p>
<p>Please Use <strong><?php echo $password;?></strong> As Your New Password To Login to the system</p>

</div>
<hr>
<p>Thanks & Regards</p>
<p><?php echo PROJECT_NAME?></p>
</body>
</html>
