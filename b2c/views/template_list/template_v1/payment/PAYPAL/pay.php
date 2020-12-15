<?php
//error_reporting(E_ALL);

 $posted_data = $pay_data;
 //debug($posted_data);die('5');
 //extract($posted_data);
 //die('456');
// $initiate_url = base_url().'index.php/payment_gateway/initiate';
 //$paypalURL = 'https://www.sandbox.paypal.com/cgi-bin/webscr'; //Test PayPal API URL
//$paypalID = 'Insert_PayPal_Email'; //Business Email
?>
<html>
  	<head>
		<script type="text/javascript">
		 var hash = '<?php echo $posted_data['tran_id']; ?>';
		    function submitPaypalForm() {
		      if(hash == '') {
		        return;
		      }
		      var paypalform = document.forms.payment_gws;
		      paypalform.submit();
		    }
		</script>
  	</head>
	<body onload="submitPaypalForm()">
		<form name="payment_gws" method="POST" action="<?= $posted_data['process_url'];?>">
			
			<input type="hidden" name="business" value="<?= $posted_data['paypal_username']; ?>">
			<input type="hidden" name="cmd" value="_xclick">
			<input type="hidden" name="item_number" value="<?= $posted_data['tran_id']; ?>">
			<input type="hidden" name="item_name" value="<?= $posted_data['productinfo'];?>">
			<input type="hidden" name="amount" value="<?= $posted_data['total_amount'];?>" />
			<input type='hidden' name='productinfo' value='<?= $posted_data['productinfo']; ?>'>
			<input type='hidden' name='no_shipping' value='1'>
			<input type="hidden" name="currency_code" value="<?= $posted_data['currency_code'];?>" />
			
			<input type="hidden" name="cancel_return" value="<?= $posted_data['curl'];?>" />
			<input type="hidden" name="return" value="<?= $posted_data['surl'];?>" />
			

			<input type="image" name="submit" border="0"
	        src="https://www.paypalobjects.com/en_US/i/btn/btn_buynow_LG.gif" alt="PayPal - The safer, easier way to pay online" style="display:none">
	        <img alt="" border="0" width="1" height="1" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" style="display:none">

       

			
			<input name = "upload" value = "1" type = "hidden">
			<input name = "no_note" value = "0" type = "hidden">
			<input name = "bn" value = "PP-BuyNowBF" type = "hidden">
			<input name = "tax" value = "0" type = "hidden">
			<input name = "rm" value = "2" type = "hidden">
			<input name = "handling_cart" value = "0" type = "hidden">
			<input name = "lc" value = "GB" type = "hidden">
			<input name = "cbt" value = "Return to <?php echo "www.cheapmytrip.com";?>" type = "hidden">
			<input name = "custom" value = "" type = "hidden">
			
	
			<!-- SUBMIT REQUEST  !-->
			<!--<input style="display:none;" type="submit"  value="Pay Now" />-->


		</form>
	</body>
</html>

	