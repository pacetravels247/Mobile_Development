<?php
$posted_data = $pay_data;
$PAYU_BASE_URL = $pay_data['pay_target_url'];//PAY target url
$MERCHANT_KEY = $pay_data['key']; //PAYU key
$SALT = $pay_data['salt'];//PAYU salt
$action = '';
$posted = array();
if(!empty($posted_data)) {
  foreach($posted_data as $key => $value) {    
    $posted[$key] = $value; 
	
  }
}

$formError = 0;

if(empty($posted['txnid'])) {
  // Generate random transaction id
  $txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
} else {
  $txnid = $posted['txnid'];
}
$hash = '';
// Hash Sequence
$hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
if(empty($posted['hash']) && sizeof($posted) > 0) {
  if(
          empty($posted['key'])
          || empty($posted['txnid'])
          || empty($posted['amount'])
          || empty($posted['firstname'])
          || empty($posted['email'])
          || empty($posted['phone'])
          || empty($posted['productinfo'])
          || empty($posted['surl'])
          || empty($posted['furl'])
		  || empty($posted['service_provider'])
  ) {
    $formError = 1;
  } else {
    //$posted['productinfo'] = json_encode(json_decode('[{"name":"tutionfee","description":"","value":"500","isRequired":"false"},{"name":"developmentfee","description":"monthly tution fee","value":"1500","isRequired":"false"}]'));
	$hashVarsSeq = explode('|', $hashSequence);
    $hash_string = '';	
	foreach($hashVarsSeq as $hash_var) {
      $hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
      $hash_string .= '|';
    }

    $hash_string .= $SALT;

    $hash = strtolower(hash('sha512', $hash_string));
    //echo "<pre>";print_r($hash);exit;
    $action = $PAYU_BASE_URL . '/_payment';
  }
} elseif(!empty($posted['hash'])) {
  $hash = $posted['hash'];
  $action = $PAYU_BASE_URL . '/_payment';
}
?>
<html>
  <head>
  <script>
    var hash = '<?php echo $hash ?>';
    function submitPayuForm() {
      if(hash == '') {
        return;
      }
      var payuForm = document.forms.payuForm;
      payuForm.submit();
    }
  </script>
  </head>  
  <?php
  extract($pay_data);
  ?>
  <body onload="submitPayuForm()">
    <form action="<?php echo $action; ?>" method="post" name="payuForm">
      <input type="hidden" name="key" value="<?php echo $key ?>" />
      <input type="hidden" name="txnid" value="<?php echo $txnid ?>" />
      <input type="hidden" name="amount" value="<?php echo $amount?>" />
      <input type="hidden" name="productinfo" value="<?php echo $productinfo ?>" />
      <input type="hidden" name="firstname" value="<?php echo $firstname ?>" />
      <input type="hidden" name="email" value="<?php echo $email ?>" />
      <input type="hidden" name="phone" value="<?php echo $phone ?>" />
      <input type="hidden" name="surl" value="<?php echo $surl ?>" />
      <input type="hidden" name="furl" value="<?php echo $furl ?>" />
       <input type="hidden" name="hash" value="<?php echo $hash ?>"/>      
      <input type="hidden" name="service_provider" value="payu_paisa" size="64" />
      <input style="display:none;" type="submit" value="Submit" /></td>
    </form>
  </body>
</html>
