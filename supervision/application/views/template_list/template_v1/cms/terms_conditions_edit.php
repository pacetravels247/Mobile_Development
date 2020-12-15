<?php 
$tab1 = " active ";
$page_data = $data_list[0];
$primary_id = $page_data['origin'];
$description =  $page_data['description'];
$credit_note =  $page_data['credit_note'];
$order =  $page_data['module'];
?>
<script src="<?php echo SYSTEM_RESOURCE_LIBRARY?>/ckeditor/ckeditor.js"></script>
<!-- HTML BEGIN -->
<div class="bodyContent">
<div class="panel panel-default"><!-- PANEL WRAP START -->
<div class="panel-heading"><!-- PANEL HEAD START -->
<div class="panel-title">
<ul class="nav nav-tabs" role="tablist" id="myTab">
	<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE START-->
	<li role="presentation" class="<?=$tab1?>">
		<a id="fromListHead" href="#fromList" aria-controls="home" role="tab"	data-toggle="tab">Manage Terms & Conditions</a>
	</li>
	<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE END -->
</ul>
</div>
</div>
<!-- PANEL HEAD START -->
<div class="panel-body"><!-- PANEL BODY START -->
<div class="tab-content">
<div role="tabpanel" class="clearfix tab-pane <?=$tab1?>" id="fromList">
<div class="panel-body">


<div class="tab-content">
   <div id="fromList" class="clearfix tab-pane  active " role="tabpanel">
      <div class="panel-body">
         <form class="form-horizontal" role="form" id="promo_codes_form_edit" enctype="multipart/form-data" method="POST" action="<?=base_url().'index.php/cms/update_terms_action/'.$primary_id?>" autocomplete="off" name="promo_codes_form_edit">
           
            <fieldset form="promo_codes_form_edit">
               <legend class="form_legend">Update Terms & Conditions</legend>
              <div class="form-group">
                  <label form="promo_codes_form_edit" for="description" class="col-sm-3 control-label">Voucher Terms & Conditions</label>
                  <div class="col-sm-8">
                  	<textarea class=" description form-control ckeditor" id="editor" rows="10" cols="80" id="description" name="description" dt=""  data-original-title="" title=""><?=$description?></textarea>
                  </div>
               </div>
               <div class="form-group">
                  <label form="promo_codes_form_edit" for="description" class="col-sm-3 control-label">Credit Note Terms & Conditions</label>
                  <div class="col-sm-8">
                     <textarea class=" description form-control ckeditor" id="editor" rows="10" cols="80" id="cn_description" name="credit_note" dt=""  data-original-title="" title=""><?=$credit_note?></textarea>
                  </div>
               </div>
               <div class="form-group" style="margin-top:5px;">
                  <label form="promo_codes_form_edit" for="banner_order" class="col-sm-3 control-label">Module</label>
                  	<div class="col-sm-6">
                  		<input type="text" id="banner_order" class="form-control" placeholder="Order" name="banner_order" value="<?=$order?>" readonly>
                  	</div>
               </div>
            </fieldset>
            <div class="form-group">
               <div class="col-sm-8 col-sm-offset-4"> <button class=" btn btn-success " id="promo_codes_form_edit_submit" type="submit">Update</button>
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

