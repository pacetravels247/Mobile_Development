<?php
   $tab1 = 'active';
   $get_data = $this->input->get();
   // debug($get_data);exit;
 	if(isset($get_data) && valid_array($get_data)){
 		$action = base_url().'index.php/cms/add_tour_styles?&origin='.$get_data['origin'];
 	}
 	else{
 		$action = base_url().'index.php/cms/add_tour_styles';
 	}
 	// echo $action;exit;
?>
<!-- HTML BEGIN -->
<div class="bodyContent">
<div class="panel panel-default"><!-- PANEL WRAP START -->
<div class="panel-heading"><!-- PANEL HEAD START -->
<div class="panel-title">
<ul class="nav nav-tabs" role="tablist" id="myTab">
	<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE START-->
	<li role="presentation" class="<?=$tab1?>">
		<a id="fromListHead" href="#fromList" aria-controls="home" role="tab"	data-toggle="tab">Manage Tour Styles</a>
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
         <form class="form-horizontal" role="form" enctype="multipart/form-data" method="POST" action="<?= $action; ?>" autocomplete="off" name="home_page_heading">
            <fieldset form="promo_codes_form_edit">
               <legend class="form_legend">Add Tour Style Name</legend>
               <div class="form-group">
                  <label form="promo_codes_form_edit" for="destination" class="col-sm-3 control-label">Destination</label>
                  	<div class="col-sm-6">
                     <?php
                     if($destination_list['status'] == true){?>
                        <select name="destination" class="form-control">
                           <?php foreach($destination_list['data'] as $destination) {
                              // debug($destination);exit;
                              $select ='';
                              if(isset($destination_id)){
                                 // echo $airline_name;exit;
                                 if($destination['origin'] == $destination_id){
                                    $select = 'selected';
                                 }
                              }
                              
                           ?>
                           <option value="<?php echo $destination['destination_id'];?>" <?php echo $select; ?>><?php echo $destination['destination_name'];?></option>
                           <?php } ?>
                        </select>
                        <?php  } ?>
                     </div>
               </div>
                <div class="form-group">
                  <label form="promo_codes_form_edit" for="category" class="col-sm-3 control-label">Category</label>
                     <div class="col-sm-6">
                     <?php
                     if($category_list['status'] == true){?>
                        <select name="category" class="form-control">
                           <?php foreach($category_list['data'] as $category) {
                          
                              $select ='';
                              if(isset($category_id)){
                                 // echo $airline_name;exit;
                                 if($category['category_id'] == $category_id){
                                    $select = 'selected';
                                 }
                              }
                              
                           ?>
                           <option value="<?php echo $category['category_id'];?>" <?php echo $select; ?>><?php echo $category['category_name'];?></option>
                           <?php } ?>
                        </select>
                        <?php  } ?>
                     </div>
               </div>
               
               <?php if(isset($image)){?>
               <div class="form-group">
                  <label form="promo_codes_form_edit" for="module" class="col-sm-3 control-label">Current Image</label>
                  <div class="col-sm-6">
                    <img src="<?php echo $GLOBALS ['CI']->template->domain_tour_style_images ($image) ?>" height="100px" width="100px" class="img-thumbnail">
                   
                  </div>
               </div>
               <?php } ?>
              	<div class="form-group">
                  <label form="promo_codes_form_edit" for="image" class="col-sm-3 control-label">Image</label>
                  	<div class="col-sm-6">
                  		<input type="file"  id="image" class="form-control" name="image" value="<?php echo @$logo?>" <?php if(!isset($image)){?>required<?php } ?>>
                  	</div>
               </div>
              </fieldset>
            <div class="form-group">
            <?php if(isset($destination_id)){
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

