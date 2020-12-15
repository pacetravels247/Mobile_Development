<?php
//debug(urldecode($exception));exit;
if (isset($exception) == true and strlen($exception) > 0) {
	$exception1 = json_decode(urldecode($exception)); 
   //debug($exception1);exit;
  ?>
<div class="err_mge">
  <div class="container">
     <div class="err_out">
       <div class="err">
        <h2>Error</h2>
        <?php if(isset($exception1->message)){
          ?>
        <h4><?php echo $exception1->message?></h4>
        <?php } else if(isset($exception1->op)){?>
        <h4><?php echo $exception1->message?></h4>
        <?php } ?>
       </div>
       <div class="ref_num">
        	<p>Ref: <?=$exception1->exception_id?></p>
       </div>
       <div class="confirm_btn">
       		<a href="<?php echo base_url()?>index.php/general/index/flight/?default_view=<?php echo META_BUS_COURSE?>" class="btn">OK</a>
       </div>
     </div>
  </div>
</div>

<?php } 

if (isset($log_ip_info) and $log_ip_info == true and isset($exception) == true) {
	// echo 'herre I am';
	// debug($exception);exit;
?>
<script>
$(document).ready(function() {
	$.getJSON("http://ip-api.com/json", function(json) {
		$.post(app_base_url+"index.php/ajax/log_event_ip_info/<?=$exception1->xception_id?>", json);
	});
});
</script>
<?php
}
?>
