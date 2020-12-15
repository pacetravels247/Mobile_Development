<?php 
if(isset($MessageQueues) == true && valid_array($MessageQueues) == true){
	
	if($MessageQueues['a:Success'] == 'true' && valid_array($MessageQueues['a:MessageItems']['a:MessageItem']) == true){
		$q_status = true;
		$message_queues = $MessageQueues['a:MessageItems']['a:MessageItem'];
	} else {
		$q_status = false;
		$error_message  = $MessageQueues['a:Errors']['a:Error']['a:Message'];
		if(empty($error_message) == true){
			$error_message = 'No Queues';
		}
	}
}
?>
<div class="container" style="min-height: 350px; margin-top:30px;">
<h4><u>Message Queues</u></h4>
<table class="table table-bordered">
<tr>
<th>BookingID</th>
<th>Ticket Time Limit</th>
<th>Description</th>
</tr>

<?php if($q_status == true && isset($message_queues) == true && valid_array($message_queues) == true){
	foreach ($message_queues as $ms_k => $ms_v){ ?>
		<tr>
		<td><?=@$ms_v['a:UniqueID']?></td>
		<td><?=@$ms_v['a:TktTimeLimit']?></td>
		<td><?=@$ms_v['a:Messages']['b:string']?></td>
		</tr>
	<?php } ?>
<?php } else { ?>
	<tr><td><?=@$error_message?></td></tr>
<?php }?>


</table>
</div>