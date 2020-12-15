<?php
$refer = date('YmdHis');
$return = $pay_data['return_url'];
$account_id =$pay_data['account_id'];
$key    =$pay_data['key'];
$mode   =   $pay_data['mode'];


$hash = $key."|".$account_id."|".$pay_data['amount']."|".$refer."|".$return."|".$mode;
$secure_hash = md5($hash);

//$string = "$secretKey|$account_id|$amount|$refrence_no|$return_url|$mode";
//$secure_hash = md5($string);
?>
<html>
    <head>
        <script type="text/javascript">
            function submitform()
            {
                document.frmTransaction.submit();
            }
        </script>
     
    </head>
    <body onLoad="submitform()" >
        <div class="mask"></div>
        <form  method="post" action="https://secure.ebs.in/pg/ma/sale/pay" name="frmTransaction" id="frmTransaction" onSubmit="return validate()">
            <input name="account_id" type="hidden" value="<?=$account_id?>">
            <input name="return_url" type="hidden" size="60" value="<?= $return ?>" />
            <input name="mode" type="hidden" size="60" value="<?= $mode ?>" />
            <input name="reference_no" type="hidden" value="<?php echo date('YmdHis')?>" />
            <input name="amount" type="hidden" value="<?php echo $pay_data['amount']; ?>"/>
            <input name="description" type="hidden" value="<?php echo 'ebs payment' ?>" />
            <input name="name" type="hidden" maxlength="255" value="<?php echo $pay_data['name'] ?>" />
            <input name="address" type="hidden" maxlength="255" value="indian" />
            <input name="city" type="hidden" maxlength="255" value="indiancity" />
            <input name="state" type="hidden" maxlength="255" value="indianstate" />
            <input name="postal_code" type="hidden" maxlength="255" value="560100" />
            <input name="country" type="hidden" maxlength="255" value="IND" />
            <input name="phone" type="hidden" maxlength="255" value="<?php echo $pay_data['phone'] ?>" />
            <input name="email" type="hidden" size="60" value="<?php echo $pay_data['email'] ?>" />
            <input name="secure_hash" type="hidden" size="60" value="<?php echo $secure_hash; ?>" />
            <input name="submitted" value="Submit" type="hidden" />
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td align="center" valign="middle"><table width="650" border="0" cellpadding="0" cellspacing="0" class="tableborder">
                            <tr>
                                <td height="84" align="center" valign="middle">

                                    <div class="load_page">
                                        <div align="center">              <img src="images/logo.png" width="260" height="52"></div>
                                        <div  align="center" class="text1 style1" style="font-family:Verdana, Geneva, sans-serif; margin-top:10px; font-size:11px; color:#666; line-height:18px;">You are currently redirecting to the payment gateway page.</div>

                                        <div>&nbsp;</div>
                                        <img src="images/preloader.gif" width="160" height="20">
                                        <div align="center" valign="baseline" class="text1 style1" style="font-size:11px; color:#666; margin-top:10px; padding-left:50px; padding-right:50px;font-family:verdana;"><strong>Almost there !!</strong> your search results are being loaded, do not refresh the screen .</div>
                                      </div>
                                </td>
                            </tr>
                            <tr>

                            </tr>


                        </table>

                        </form>
                        </body>
                        </html>

