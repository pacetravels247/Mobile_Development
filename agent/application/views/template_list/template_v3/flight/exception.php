<?php
if (isset($exception) == true and strlen($exception) > 0) {
  $exception1 = json_decode(urldecode($exception)); 
   // debug($exception1);exit;
  
  if($exception1->op != 'booking_hold_exception'){
    $class = 'err_out';
  }
  else{
    $class = 'err_hold';
  }
  ?>
<div class="err_mge">
  <div class="container">
     <div class="<?php echo $class; ?>">
       <div class="err">
        <?php if($exception1->op != 'booking_hold_exception'){?>
        <h2>Error</h2>
      <?php } ?>
        <?php if(isset($exception1->message)){
          ?>
        <h4><?php echo $exception1->message?></h4>
        <?php } else if(isset($exception1->op)){?>
        <h4><?php echo $exception1->message?></h4>
        <?php } ?>
       </div>
        <?php if($exception1->op != 'booking_hold_exception'){?>
       <div class="ref_num">
          <p>Ref: <?=$exception1->exception_id?></p>
       </div>
     <?php } ?>
       <?php if($exception1->op != 'booking_hold_exception'){?>
       <div class="confirm_btn">
          <a href="<?php echo base_url()?>index.php/general/index/flight/?default_view=<?php echo META_AIRLINE_COURSE?>" class="btn">OK</a>
       </div>
     <?php } ?>
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
