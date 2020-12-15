<!-- HTML BEGIN -->
<div id="general_user" class="bodyContent">
	<div class="table_outer_wrper"><!-- PANEL WRAP START -->
		<div class="panel_custom_heading"><!-- PANEL HEAD START -->
			<div class="panel_title">
				<ul class="nav nav-tabs b2b_navul" role="tablist" id="myTab">
					<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE START-->
					<li role="presentation" class="active">
						<a id="fromListHead" href="#fromList" aria-controls="home" role="tab" data-toggle="tab">
							<i class="fa fa-balert"></i>
							SET BALANCE ALERT
						</a>
					</li>
					<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE END -->
				</ul>
			</div>
		</div><!-- PANEL HEAD START -->
		
		<div class="panel_bdy"><!-- PANEL BODY START -->
			<?php
			//debug($form_data);exit;
				/************************ GENERATE CURRENT PAGE FORM ************************/
				echo $balance_alert_page_obj->generate_form('set_balance_alert_form', $form_data);
				/************************ GENERATE UPDATE PAGE FORM ************************/
			?>
		</div><!-- PANEL BODY END -->
		<div class="col-md-offset-2">
			<?php if(valid_array($balance_alert_details)  == true) {?>
			<span class="text-danger">
			NOTE: You would be alerted, when the credit balance falls below <strong><?=get_enum_list('threshold_amount_range', $balance_alert_details['threshold_amount']);?></strong>
			</span>
			<!-- <br />
			<span class="pull-right">Last Updated on: <strong><?//=app_friendly_date($balance_alert_details['created_datetime'])?> </strong></span>
			 -->
			<?php } ?>
		</div>
	</div><!-- PANEL WRAP END -->
</div>
<!-- HTML END -->
<script>
$('#set_balance_alert_form_submit').click(function() {
	
	var mobile_number = $('#mobile_number').val();
	var email_id = $('#email_id').val();
	
	if(mobile_number){
		if(mobile_number.length > 10){
			alert('Mobile Number not exceed 10');
			  return false;
		}
		else{
			var sms = $('#set_balance_alert_formenable_sms_notification1').val();
			//alert(sms);
			if($("#set_balance_alert_formenable_sms_notification1").prop('checked') == false){
	 		  alert('Please check the Send SMS');
	 		  return false;
			}
		}
		
	
	}
	else{
		if($("#set_balance_alert_formenable_sms_notification1").prop('checked') == true){
			if(mobile_number){
				return true;
			}
			else{
				alert('Please Enter Mobile Number');
				 return false;
			}
		}
	}
	if(email_id){
		var email = $('#set_balance_alert_formenable_email_notification1').val();
		if($("#set_balance_alert_formenable_email_notification1").prop('checked') == false){
 		  alert('Please check the Notify to E-mail');
 		  return false;
		}
		
	}
	else{
		if($("#set_balance_alert_formenable_email_notification1").prop('checked') == true){
			
			if(email_id){
				return true;
			}
			else{
				alert('Please Enter Email id');
				 return false;
			}
		}	
	}
	if(!email_id && !mobile_number){
		alert('Please Select any one');
		 return false;
	}
	
	
});
</script>