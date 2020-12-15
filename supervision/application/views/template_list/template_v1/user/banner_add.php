<?php
   $tab1 = 'active';

$active_domain_modules = $GLOBALS ['CI']->active_domain_modules;
$master_module_list = $GLOBALS ['CI']->config->item ( 'master_module_list' );
$module_options = "";
foreach ($master_module_list as $k => $v) {
   if (in_array ( $k, $active_domain_modules )) {
     $module_options .= "<option value='".$v."' ".$selected.">".$v."</option>";
   }
}

$sides = $GLOBALS ['CI']->config->item ('sides');
$side_options = "";
foreach ($sides as $k => $v) {
  $side_options .= "<option value='".$k."' ".$selected.">".$v."</option>";
}
?>
<!-- HTML BEGIN -->
<div class="bodyContent">
<div class="panel panel-default"><!-- PANEL WRAP START -->
<div class="panel-heading"><!-- PANEL HEAD START -->
<div class="panel-title">
<ul class="nav nav-tabs" role="tablist" id="myTab">
	<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE START-->
	<li role="presentation" class="<?php echo $tab1; ?>">
		<a id="fromListHead" href="#fromList" aria-controls="home" role="tab"	data-toggle="tab">
      Manage Banners</a>
	</li>
	<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE END -->
</ul>
</div>
</div>
<!-- PANEL HEAD START -->
<div class="panel-body"><!-- PANEL BODY START -->
<div class="tab-content">
<div role="tabpanel" class="clearfix tab-pane <?php echo $tab1; ?>" id="fromList">
<div class="panel-body">


<div class="tab-content">
   <div id="fromList" class="clearfix tab-pane  active " role="tabpanel">
      <div class="panel-body">
         <form class="form-horizontal" role="form" id="promo_codes_form_edit" enctype="multipart/form-data" method="POST" action="<?php echo base_url().'user/add_banner_action'; ?>" autocomplete="off" name="promo_codes_form_edit">
            <fieldset form="promo_codes_form_edit">
               <legend class="form_legend">Add Banners</legend>
               <div class="form-group">
                  <label form="promo_codes_form_edit" for="banner_title" class="col-sm-3 control-label">Title</label>
                  	<div class="col-sm-6">
                  		<input type="text"  id="banner_title" class="form-control" placeholder="Title" name="banner_title" value="">
                  	</div>
               </div>
               <div class="form-group">
                  <label form="promo_codes_form_edit" for="description" class="col-sm-3 control-label">Description</label>
                  <div class="col-sm-6">
                  	<textarea class=" description form-control" rows="3" id="banner_description" name="banner_description" dt=""  data-original-title="" title=""></textarea>
                  </div>
               </div>
               <div class="form-group">
                  <label form="promo_codes_form_edit" for="module" class="col-sm-3 control-label">Banner Image</label>
                  <div class="col-sm-6">
                    <input type="file" name="banner_image" required="required">
                  </div>
               </div>
               
               <div class="radio">
               	<label form="promo_codes_form_edit" for="status" class="col-sm-3 control-label">Status<span class="text-danger">*</span></label>
               		<label for="promo_codes_form_editstatus0" class="radio-inline">  
               			<input type="radio" value="0" id="promo_codes_form_editstatus0"  name="status" class=" status radioIp" dt="" required="">Inactive
               		</label>
               		<label for="promo_codes_form_editstatus1" class="radio-inline"> 
               		 <input type="radio" value="1" id="promo_codes_form_editstatus1" name="status" class=" status radioIp" checked="checked"  required="">Active
               		 </label>
               </div>
               <div class="form-group" style="margin-top:5px;">
                  <label form="promo_codes_form_edit" for="banner_order" class="col-sm-3 control-label">Order</label>
                  	<div class="col-sm-6">
                  		<input type="number" min="1" max="5"  id="banner_order" class="form-control" placeholder="Order" name="banner_order" value="0">
                  	</div>
               </div>
                <div class="form-group" style="margin-top:5px;">
                  <label form="promo_codes_form_edit" for="banner_module" class="col-sm-3 control-label">Module</label>
                     <div class="col-sm-6">
                        <select name="banner_module">
                           <?php echo $module_options; ?>
                           <option value="common" data-select2-id="6">Common</option>
                        </select>
                     </div>
               </div>
               <div class="form-group" style="margin-top:5px;">
                  <label form="promo_codes_form_edit" for="banner_order" class="col-sm-3 control-label">Side</label>
                     <div class="col-sm-6">
                        <select name="banner_side">
                           <?php echo $side_options; ?>
                        </select>
                     </div>
               </div>
            </fieldset>
            <div class="form-group">
               <div class="col-sm-8 col-sm-offset-4"> <button class=" btn btn-success " id="promo_codes_form_edit_submit" type="submit">Save</button> <button class=" btn btn-warning " id="promo_codes_form_edit_reset" type="reset">Reset</button></div>
            </div>
         </form>
      </div>
   </div>
</div>


</div>
</div>

</div>
</div>
<!-- PANEL BODY END --></div>
<!-- PANEL WRAP END --></div>
<!-- HTML END -->

