<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title><?=domain_name()?></title>
      </head>
      <body>

<!-- Start of header -->

<?php

    extract($agent);
$project_base_url = '<a href="'.base_url().'" >www.'.$_SERVER['HTTP_HOST'].'</a>';

?>
<p>Dear <?=get_enum_list('title', $title).' '.$first_name.' '.$last_name?>, </p>

<p>Greetings from <?=$project_base_url?>, One of the fastest growing, energetic and vibrant travel companies of India.</p>

<p>Many thanks for your Interest and Submitting Online Agent Registration
using <?=$project_base_url?></p>

<p>Your login ID will be activated within 24 hours. Your Login Details as registered with us are as Follows:</p> 
<p>Login:  <?=$user_name?></p>

<p>Password:  <?=$password?></p>

<p>Please do not hesitate to contact us for all your Urgent Queries / Reservation or Requirements.</p>

<p>Regards</p>


<p>Sales Team</p>

<p><?=$project_base_url?></p>
<p>Call: +91-<?=$this->entity_domain_phone?></p>
<p>Email: <?=$this->entity_domain_mail?></p>

</body>
</html>