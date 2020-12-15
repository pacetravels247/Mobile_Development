<?php
if (isset($exception) == true) {
	 ?>
<div class="err_mge">
  <div class="container">
     <div class="err_out">
       <div class="err">
        <h2>Error</h2>
        <h4>
        <?php 
            if($exception['op']=='booking_exception'){
               echo "Some Problem Occured. Please Try Again";
            }else{
              echo $exception['op'];
            }
        ?>
        </h4>             
       </div>
       <div class="ref_num">
        	<p>Ref: <?=$exception['exception_id']?></p>
       </div>
       <div class="confirm_btn">
       		<a href="<?php echo base_url()?>index.php/general/index/transfers/?default_view=<?php echo PROVAB_TRANSFERV1_BOOKING_SOURCE?>" class="btn" role="button">OK</a>
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

