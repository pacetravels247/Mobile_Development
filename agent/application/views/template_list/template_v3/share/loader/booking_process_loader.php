
<?php
$template_images = $GLOBALS['CI']->template->template_images();
?>
<div class="fulloading result-pre-loader-wrapper">
  <div class="loadmask"></div>
  <div class="centerload cityload">
  
    <div class="relativetop">
      <div class="paraload">
        <h3>Processing your booking...</h3>
        <img src="<?=$template_images?>default_loading.gif" alt="" />
        <div class="clearfix"></div>
        <small>please wait</small>
      </div>
    </div>
  </div>
</div>

<form id="form"  action="<?=$form_url?>" method="<?=$form_method?>"><?php
  foreach ( $form_params as $key => $val ) {
    ?><input type="hidden" name="<?=$key?>" value="<?=$val?>" /><?php
  }
  ?>
</form>

<script type="text/javascript">
$('.fulloading').show();
document.getElementById("form").submit();

</script>
