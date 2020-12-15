<?php
   $tab1 = 'active';
   $get_data = $this->input->get();
   // debug($get_data);exit;
 	if(isset($get_data) && valid_array($get_data)){
 		$action = base_url().'index.php/cms/add_top_airlines?&origin='.$get_data['origin'];
 	}
 	else{
 		$action = base_url().'index.php/cms/add_top_airlines';
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
		<a id="fromListHead" href="#fromList" aria-controls="home" role="tab"	data-toggle="tab">Manage Top Airlines</a>
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
               <legend class="form_legend">Add Top Airline</legend>
               <div class="form-group">
                  <label form="promo_codes_form_edit" for="airline_name" class="col-sm-3 control-label">Airline</label>
                  	<div class="col-sm-6">
                     <?php
                    
                      if($airline_list['status'] == true){?>
                  		<select name="airline_name" class="form-control">
                           <?php foreach($airline_list['data'] as $airline) {
                              $select ='';
                              if(isset($airline_name)){
                                 // echo $airline_name;exit;
                                 if($airline['name'] == $airline_name){
                                    $select = 'selected';
                                 }
                              }
                              
                           ?>
                           <option value="<?php echo $airline['name'];?>" <?php echo $select; ?>><?php echo $airline['name'];?></option>
                           <?php } ?>
                        </select>
                        <?php  } ?>
                  	</div>
               </div>
               <?php if(isset($logo)){?>
               <div class="form-group">
                  <label form="promo_codes_form_edit" for="module" class="col-sm-3 control-label">Current Image</label>
                  <div class="col-sm-6">
                    <img src="<?php echo $GLOBALS ['CI']->template->domain_top_airline_images ($logo) ?>" height="100px" width="100px" class="img-thumbnail">
                   
                  </div>
               </div>
               <?php } ?>
              	<div class="form-group">
                  <label form="promo_codes_form_edit" for="airline_logo" class="col-sm-3 control-label">Logo</label>
                  	<div class="col-sm-6">
                  		<input type="file"  id="airline_logo" class="form-control" name="airline_logo" value="<?php echo @$logo?>" <?php if(!isset($logo)){?>required<?php } ?>>
                  	</div>
               </div>
              </fieldset>
            <div class="form-group">
            <?php if(isset($airline_name)){
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

