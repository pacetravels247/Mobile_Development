<div id="package_types" class="bodyContent col-md-12">
  <div class="panel panel-default">
    <!-- PANEL WRAP START -->
    <div class="panel-heading">
      <!-- PANEL HEAD START -->
      <div class="panel-title">
        <ul class="nav nav-tabs nav-justified" role="tablist" id="myTab">
          <!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE START-->
          <li role="presentation" class="active">
            <a href="#fromList"
              aria-controls="home" role="tab" data-toggle="tab">
              <h1>Edit Package
              </h1>
            </a>
          </li>
          <!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE END -->
        </ul>
      </div>
    </div>
    <!-- PANEL HEAD START -->
    <div class="panel-body">
      <!-- PANEL BODY START -->
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="fromList">
          <div class="col-md-12">
            <div class='row'>
              <div class='row'>
                <div class='col-sm-12'>
                  <div class='' style='margin-bottom:0;'>
                    <div class='box-content'>
  <form class='form form-horizontal validate-form' style='margin-bottom: 0;' action="<?php echo base_url(); ?>supplier/update_package/<?php echo $packdata->package_id;?>" method="post" enctype="multipart/form-data">
                       <input type="hidden" name="w_wo_d" value="w">
                      
                        <div class='form-group'>
                           <label class='control-label col-sm-3'  for='validation_current'>Package Name
 </label>
                             <div class='col-sm-4 controls'>
                        
                            <div class="controls">
                          <input type="text" name="name" id="name" value ="<?php echo $packdata->package_name;?>" data-rule-required='true' class='form-control'>
                          </div>
                     
                          </div>
                          </div>
                        
                          <div class='form-group'>
                           <!-- <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 fiveh">
                      <span class="form-group">Duration</span> -->
                      <label class='control-label col-sm-3'  for='validation_current'>Duration
  </label>
      <div class="col-sm-4 controls"> 
      <input type="text" name="duration" class="form-control" id="duration" value ="<?php echo $packdata->duration;?>" onchange="show_duration_info(this.value)" size="40" disabled>                          
      
       
      </div>                   
                  </div>
                 
                        <div class='form-group'>
                          <label class='control-label col-sm-3' for='validation_country'>Country</label>
                          <div class='col-sm-4 controls'>
                            <select class='select2 form-control' data-rule-required='true' name='country' id="validation_country" value ="<?php echo $packdata->package_country;?>">
                            <!--  <input type="text" name="country" id="country" data-rule-required='true' class='form-control'>  -->
                              <?php foreach ($countries as $country) {?><option  value='<?php echo $country->country_id;?>'<?php if($country->country_id == $packdata->package_country) { echo "selected=selected"; } ?>><?php echo $country->name;?></option>
                                 <?php }?>
                           </select>
                          </div>
                        </div>
                       

                        <div class='form-group'>
                           <label class='control-label col-sm-3'  for='validation_current'>City
 </label>
                             <div class='col-sm-4 controls'>

                            <div class="controls">
                          <input type="text" name="city" id="country" value ="<?php echo $packdata->package_city;?>" data-rule-required='true' class='form-control' disabled>
                          </div>
                     
                          </div>
                          </div>
                          <div class='form-group'>
                           <label class='control-label col-sm-3'  for='validation_current'>Location
  </label>
                             <div class='col-sm-4 controls'>
                   
                      <input type="text" name="location" id="location" data-rule-required='true' class='form-control' value ="<?php echo $packdata->package_location;?>">
                        
                          </div>
                          </div>
                          
                                 <div class='form-group'>
                          <label class='control-label col-sm-3' for='validation_company'>Package Main Image</label>
                          <div class='col-sm-3 controls'>
                         <!--  <input type="hidden" value="<?php echo $packdata->image; ?>" name="photo"> -->
                                 <input type="file" title='Image to add to add' class=''   id='photo' name='photo'>                                 
                                  <input type="hidden" name='hidephoto' value="<?php echo $packdata->image; ?>">
                                 <img src="<?php echo $GLOBALS['CI']->template->domain_upload_pckg_images($packdata->image); ?>" width="100" name="photo">
                                 </div>
                          
                           
                        </div>

                                 <div class='form-group'>
                            <label class='control-label col-sm-3' for='validation_name'>Description</label>
                          <div class='col-sm-8 controls'>
                            <textarea name="Description" data-rule-required='true' class="form-control ckeditor" id="editor" cols="1000" rows="10" placeholder="Description" required><?php echo $packdata->package_description;?></textarea>                             <!--   <span id="dorigin_error" style="color:#F00;  display:none;"></span> -->
                           </div> 
                           </div>
                      
                  <div class='form-group'>
                         
                      <label class='control-label col-sm-3'  for='validation_rating' >Rating  </label>
      <div class="col-sm-4 controls" >
      <select class='form-control' data-rule-required='true' name='rating' id="rating" value ="" >
     
                <option><?php echo $packdata->rating;?></option>
                                <option value="0">0</option>
                                <option value="1">1</option> 
                                <option value="2">2</option>
                                <option value="3">3</option>  
                                <option value="4">4</option>
                                <option value="5">5</option>
                                 
                                 </select>
                                    </div>  
                                     </div>
                 
                     <div class='box-header blue-background'>
                      <div class=''><h4>Price Info</h4></div>

                      
                    </div>

                          <div><h2></h2></div>
                  
                          
                     
                         <!--  </div> -->
                          <div class='form-group'>
                          <label class='control-label col-sm-3'  for='validation_number'>Price
 </label>
                             <div class='col-sm-3 controls'>
                        
                         
                          <input type="text"  name="p_price" id="p_price" data-rule-number='true' data-rule-required='true' class='form-control' value ="<?php echo $packdata->price;?>">
                         
                     
                          </div>
                          </div>
                           <div class='form-group'>
                                 <div class='form-group' id="addMultiCity"> </div> 
                              <div id="addCityButton" class="col-lg-2" style="display:none">
                                <input type="button" class="srchbutn comncolor" id="addCityInput" value="Add Month" style="padding:3px 10px;">
                                <input type="hidden" value="1" id="multiCityNo" name="no_of_days">
                           </div>
                           <div id="removeCityButton" class="col-lg-2" style="display:none;">
                                <input type="button" class="srchbutn comncolor" id="removeCityInput" value="Remove One Month" style="padding:3px 10px;">
                           </div>
                       </div>
                   
                         
                          
                         
                          
                          <div class='box-header blue-background'>
                      <div class=''><h4>Pricing Policy</h4></div>
                      
                      
                    </div>
                      <div><h2></h2></div>
                     
                          <div class='form-group'>
                           <label class='control-label col-sm-3'  for='validation_includes'>Price Includes
  </label>
                             <div class='col-sm-8 controls'>
                   
                      <!-- <input type="text" name="includes" id="includes" data-rule-required='true' class='form-control'> -->
                      <textarea name="includes" data-rule-required='true' class="form-control ckeditor" cols="80" rows="10" placeholder="Price Includes" required><?php echo $packdata->price_includes;?></textarea>                             
                      <!--   <span id="dorigin_error" style="color:#F00;  display:none;"></span> -->
                      </div>
                          </div>
                          <div class='form-group'>
                           <label class='control-label col-sm-3'  for='validation_excludes'>Price Excludes
  </label>
                             <div class='col-sm-8 controls'>
                   
                      <!-- <input type="text" name="excludes" id="excludes" data-rule-required='true' class='form-control'> -->
                      <textarea name="excludes" data-rule-required='true' class="form-control ckeditor" cols="80" rows="10" placeholder="Price Excludes" required><?php echo $packdata->price_excludes;?></textarea>                             
</div>
                          </div>
                          <div class='box-header blue-background'>
                      <div class=''><h4>Cancellation & Refund Policy</h4></div>
                     
                    </div>
                      <div><h1></h1></div>
                      <div class='form-group'>
                           <label class='control-label col-sm-3'  for='validation_advance'>Cancellation In Advance
  </label>
                             <div class='col-sm-8 controls'>
                   
                      <!-- <input type="text" name="excludes" id="excludes" data-rule-required='true' class='form-control'> -->
                       <textarea name="advance" class="form-control ckeditor" data-rule-required="true"  cols="70" rows="3" placeholder="Cancellation In Advance"><?php echo $packdata->cancellation_advance;?></textarea>
                        
                          </div>
                          </div>
                          <div class='form-group'>
                           <label class='control-label col-sm-3'  for='validation_excludes'>Cancellation Penalty
  </label>
                             <div class='col-sm-8 controls'>
                   
                      <!-- <input type="text" name="excludes" id="excludes" data-rule-required='true' class='form-control'> -->
                       <textarea name="penality" class="form-control ckeditor" data-rule-required="true"  cols="70" rows="3" placeholder="Cancellation Penalty"><?php echo $packdata->cancellation_penality;?></textarea>
                        
                          </div>
                          </div>
                        
                          </div>

                      
                    </div>
                           
                     </div>
                     

                     <div class='form-actions' style='margin-bottom:0'>
                          <div class='row'>
                            <div class='col-sm-9 col-sm-offset-3'>
                              <a href="<?php echo base_url(); ?>supplier/view_with_price">
                              <button class='btn btn-primary' type='button'>
                                <i class='icon-reply'></i>
                                Go Back
                              </button></a>
                              <button class='btn btn-primary' type='submit'>
                                <i class='icon-save'></i>
                                Update
                              </button>
                            </div>
                          </div>
                        </div>
                        
                  </div>
                </div>
                </form>
              
        </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- PANEL BODY END -->
</div>
<!-- PANEL WRAP END -->
</div>
<script src="<?php echo SYSTEM_RESOURCE_LIBRARY?>/ckeditor/ckeditor.js"></script>