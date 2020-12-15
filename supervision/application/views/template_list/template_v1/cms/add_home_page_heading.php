<?php
   $tab1 = 'active';
   $get_data = $this->input->get();
   // debug($get_data);exit;
   if(isset($get_data) && valid_array($get_data)){
      $action = base_url().'index.php/cms/add_home_page_heading?&origin='.$get_data['origin'];
   }
   else{
      $action = base_url().'index.php/cms/add_home_page_heading';
   }
?>
<!-- HTML BEGIN -->
<div class="bodyContent">
<div class="panel panel-default"><!-- PANEL WRAP START -->
<div class="panel-heading"><!-- PANEL HEAD START -->
<div class="panel-title">
<ul class="nav nav-tabs" role="tablist" id="myTab">
	<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE START-->
	<li role="presentation" class="<?=$tab1?>">
		<a id="fromListHead" href="#fromList" aria-controls="home" role="tab"	data-toggle="tab">Manage Home Page Headings</a>
	</li>
	<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE END -->
</ul>
</div>
</div>
<!-- PANEL HEAD START -->
<div class="panel-body"><!-- PANEL BODY START -->
<span class="error_msg"><?php $msg = $this->uri->segment(3); if(isset($msg)){ echo urldecode($msg); }?></span>
<div class="tab-content">
<div role="tabpanel" class="clearfix tab-pane <?=$tab1?>" id="fromList">
<div class="panel-body">


<div class="tab-content">
   <div id="fromList" class="clearfix tab-pane  active " role="tabpanel">
      <div class="panel-body">
         <form class="form-horizontal" role="form" enctype="multipart/form-data" method="POST" action="<?=$action?>" autocomplete="off" name="home_page_heading">
            <fieldset form="promo_codes_form_edit">
               <legend class="form_legend">Add Headings</legend>
               <div class="form-group">
                  <label form="promo_codes_form_edit" for="header_title" class="col-sm-3 control-label">Title</label>
                  	<div class="col-sm-6">
                  		<input type="text"  id="header_title" class="form-control" placeholder="Title" name="header_title" value="<?php echo @$title?>" required>
                  	</div>
               </div>
              
              </fieldset>
            <div class="form-group">
            <?php if(isset($title)){
                  $button = 'Update';
               }else{
                  $button = 'Save';
               }
               ?>
               <div class="col-sm-8 col-sm-offset-4"> <button class="btn btn-success" type="submit"><?php echo $button; ?></button> <button class=" btn btn-warning " id="promo_codes_form_edit_reset" type="reset">Reset</button></div>
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

