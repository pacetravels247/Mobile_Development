<!DOCTYPE html>
<html>
<head>
    <title>Supplier Account Manager | China Dream Travel</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta content='text/html;charset=utf-8' http-equiv='content-type'>
    
    <link href='<?=base_url();?>assets/images/meta_icons/favicon.ico' rel='shortcut icon' type='image/x-icon'>
    <link href='<?=base_url();?>assets/images/meta_icons/apple-touch-icon.png' rel='apple-touch-icon-precomposed'>
    <link href='<?=base_url();?>assets/images/meta_icons/apple-touch-icon-57x57.png' rel='apple-touch-icon-precomposed'   sizes='57x57'>
    <link href='<?=base_url();?>assets/images/meta_icons/apple-touch-icon-72x72.png' rel='apple-touch-icon-precomposed'   sizes='72x72'>
    <link href='<?=base_url();?>assets/images/meta_icons/apple-touch-icon-114x114.png' rel='apple-touch-icon-precomposed' sizes='114x114'>
    <link href='<?=base_url();?>assets/images/meta_icons/apple-touch-icon-144x144.png' rel='apple-touch-icon-precomposed' sizes='144x144'>
    <!-- / START - page related stylesheets [optional] -->
    <link href="<?=base_url();?>assets/stylesheets/plugins/select2/select2.css" media="all" rel="stylesheet" type="text/css" />
    <!-- / END - page related stylesheets [optional] -->
    <!-- / bootstrap [required] -->
    <link href="<?=base_url();?>assets/stylesheets/bootstrap/bootstrap.css" media="all" rel="stylesheet" type="text/css" />
    <!-- / theme file [required] -->
    <link href="<?=base_url();?>assets/stylesheets/light-theme.css" media="all" id="color-settings-body-color" rel="stylesheet" type="text/css" />
    <!-- / coloring file [optional] (if you are going to use custom contrast color) -->
    <link href="<?=base_url();?>assets/stylesheets/theme-colors.css" media="all" rel="stylesheet" type="text/css" />
    <!-- / demo file [not required!] -->
    <link href="<?=base_url();?>assets/stylesheets/demo.css" media="all" rel="stylesheet" type="text/css" />
      <link href="<?=base_url();?>assets/stylesheets/jquery-ui.css" media="all" rel="stylesheet" type="text/css" />
      
 <link href="<?=base_url();?>assets/stylesheets/prettify.css" media="all" rel="stylesheet" type="text/css" />
    
    <link rel="stylesheet" href="<?php echo WEB_DIR; ?>css/style.css">
    <link href="<?=base_url();?>assets/stylesheets/bootstrap-wysihtml5.css" media="all" rel="stylesheet" type="text/css" />
 
 
<script src="http://www.codecomplete4u.com/wp-content/MyPlugins/jquery.ui.datepicker.js" type="text/javascript"></script>
<script src="<?=base_url();?>assets/javascripts/jquery/jquery-1.10.2.js" type="text/javascript"></script>
    
  </head>
  <body class='contrast-dark fixed-header'>
    <?php $this->load->view('suppliers/header');?>
    <div id='wrapper'>
      <div id='main-nav-bg'></div>
      <?php $this->load->view('supplier-sidemenu');?>
      <section id='content'>
        <div class='container'>
          <div class='row' id='content-wrapper'>
            <div class='col-xs-12'>
              <div class='row'>
                <div class='col-sm-12'>
                  <div class='page-header'>
                    <h1 class='pull-left'>
                      <i class='icon-building'></i>
                      <span>Edit Package</span>
                    </h1>
                   
                  </div>
                </div>
              </div>
              
              <div class='row'>
                <div class='col-sm-12'>
                  <div class='box bordered-box orange-border' style='margin-bottom:0;'>
                    <div class='box-header blue-background'>
                      <div class='title'>Edit Package</div>
                    
                    </div>
                        
                     <div class='box-content'>
  <form class='form form-horizontal validate-form' style='margin-bottom: 0;' action="<?php echo base_url(); ?>supplier/update_package/<?php echo $packdata->package_id;?>" method="post" enctype="multipart/form-data">
                       <!--  <form class='form form-horizontal validate-form' style='margin-bottom: 0;' action="<?php echo WEB_URL; ?>supplier/update_package/<?php echo $package_id->package_country; ?>" method="post"  enctype="multipart/form-data">  -->
                       <input type="hidden" name="w_wo_d" value="d">
                        <div class='form-group'>
                           <label class='control-label col-sm-3'  for='validation_current'>Package Name
 </label>
                             <div class='col-sm-4 controls'>
                        
                            <div class="controls">
                          <input type="text" name="name" id="name" value ="<?php echo $packdata->package_name;?>" data-rule-required='true' class='form-control' required>
                          </div>
                     
                          </div>
                          </div>
                        
                          <div class='form-group'>
                      <label class='control-label col-sm-3'  for='validation_current'>Duration
  </label>
      <div class="col-sm-4 controls"> 
      <input type="text" name="duration" class="form-control" id="duration" value ="<?php echo $packdata->duration;?>" onchange="show_duration_info(this.value)" size="40" disabled>                          
      
       
      </div>                   
                  </div>
                 
                        <div class='form-group'>
                          <label class='control-label col-sm-3' for='validation_country'>Country</label>
                          <div class='col-sm-4 controls'>
                            <select class='select2 form-control' data-rule-required='true' name='country' id="validation_country" value ="">
                            <!--  <input type="text" name="country" id="country" data-rule-required='true' class='form-control'>  -->
                              <?php foreach ($countries as $country) {?>
                                  <option  value='<?php echo $country->country_id;?>'<?php if($country->country_id == $packdata->package_country) { echo "selected=selected"; } ?>><?php echo $country->name;?></option>
                                 <?php }?>country
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
                        
                                 <input type="file" title='Image to add to add' class='form-control'   id='photo' name='photo'>
                                  <input type="hidden" name='hidephoto' value="<?php echo $packdata->image; ?>">
                                 <img src="<?php echo $packdata->image; ?>" width="100" name="photo">
                                 </div>
                          
                          
                          
                        </div>

                                 <div class='form-group'>
                            <label class='control-label col-sm-3' for='validation_name'>Description</label>
                          <div class='col-sm-3 controls'>
                               <textarea name="Description" class="form-control" data-rule-required="true"  cols="70" rows="3" placeholder="Description" value =""><?php echo $packdata->package_description;?></textarea>
                             <!--   <span id="dorigin_error" style="color:#F00;  display:none;"></span> -->
                           </div> 
                           </div>
                                 <div class='form-group'>
                           
                      <label class='control-label col-sm-3'  for='validation_current'>Number of Questions
  </label>
      <div class="col-sm-4 controls"> 
      <input type="text" name="questions" class="form-control" id="questions"  onchange="show_question_info(this.value)" size="40" value ="<?php echo $packdata->no_que;?>" disabled>                          
     
       
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
                      <div class='title'>Add Deals</div>

                    </div>
                    <div>
                     <div><h2></h2></div>
                    <div class='form-group'>
                           <label class='control-label col-sm-3'  for='validation_current'>Value
 </label>
                             <div class='col-sm-4 controls'>
                        
                            <div class="controls">
                          <input type="text" name="value" id="value" data-rule-number='true' data-rule-required='true' class='form-control' value ="<?php echo $deal->value;?>">
                          </div>
                     
                          </div>
                          </div>
                          <div class='form-group'>
                           <label class='control-label col-sm-3'  for='validation_current'>Discount
 </label>
                             <div class='col-sm-4 controls'>
                        
                            <div class="controls">
                          <input type="text" name="discount" id="discount" data-rule-number='true' data-rule-required='true' class='form-control' value ="<?php echo $deal->discount;?>">
                          </div>
                     
                          </div>
                          </div>
                          <div class='form-group'>
                           <label class='control-label col-sm-3'  for='validation_current'>You Save
 </label>
                             <div class='col-sm-4 controls'>
                        
                            <div class="controls">
                          <input type="text" name="save" id="save" data-rule-number='true' data-rule-required='true' class='form-control' value ="<?php echo $deal->you_save;?>">
                          </div>
                     
                          </div>
                          </div>
                          <div class='form-group'>
                           <label class='control-label col-sm-3'  for='validation_current'>Available Seats
 </label>
                             <div class='col-sm-4 controls'>
                        
                            <div class="controls">
                          <input type="text" name="seats" id="seats" data-rule-number='true' data-rule-required='true' class='form-control' value ="<?php echo $deal->seats;?>">
                          </div>
                     
                          </div>
                          </div>
                          <div class='form-group'>
                           <label class='control-label col-sm-3'  for='validation_current'>Time
 </label>
                             <div class='col-sm-4 controls'>
                        
                            <div class="controls">
                          <input type="text" name="time" id="datepicker1" data-rule-required='true' class='form-control' value ="<?php echo $deal->time;?>">
                          </div>
                     
                          </div>
                          </div>
                          <div class='box-header blue-background'>
                      <div class='title'>Pricing Policy</div>
                      
                      
                    </div>
                      <div><h2></h2></div>
                     
                          <div class='form-group'>
                           <label class='control-label col-sm-3'  for='validation_includes'>Price Includes
  </label>
                             <div class='col-sm-4 controls'>
                   
                      <!-- <input type="text" name="includes" id="includes" data-rule-required='true' class='form-control'> -->
                      <textarea name="includes" class="form-control" data-rule-required="true" value ="" cols="70" rows="3" placeholder="Price Includes"><?php echo $packdata->price_includes;?></textarea>

                        
                          </div>
                          </div>
                          <div class='form-group'>
                           <label class='control-label col-sm-3'  for='validation_excludes'>Price Excludes
  </label>
                             <div class='col-sm-4 controls'>
                   
                      <!-- <input type="text" name="excludes" id="excludes" data-rule-required='true' class='form-control'> -->
                       <textarea name="excludes" class="form-control" data-rule-required="true" value =""  cols="70" rows="3" placeholder="Price Excludes"><?php echo $packdata->price_excludes;?></textarea>
                        
                          </div>
                          </div>
                          <div class='box-header blue-background'>
                      <div class='title'>Cancellation & Refund Policy</div>
                     
                    </div>
                      <div><h1></h1></div>
                      <div class='form-group'>
                           <label class='control-label col-sm-3'  for='validation_advance'>Cancellation In Advance
  </label>
                             <div class='col-sm-4 controls'>
                   
                      <!-- <input type="text" name="excludes" id="excludes" data-rule-required='true' class='form-control'> -->
                       <textarea name="advance" class="form-control" data-rule-required="true"  cols="70" rows="3" placeholder="Cancellation In Advance"><?php echo $packdata->cancellation_advance;?></textarea>
                        
                          </div>
                          </div>
                          <div class='form-group'>
                           <label class='control-label col-sm-3'  for='validation_excludes'>Cancellation Penalty
  </label>
                             <div class='col-sm-4 controls'>
                   
                      <!-- <input type="text" name="excludes" id="excludes" data-rule-required='true' class='form-control'> -->
                       <textarea name="penality" class="form-control" data-rule-required="true"  cols="70" rows="3" placeholder="Cancellation Penalty"><?php echo $packdata->cancellation_penality;?></textarea>
                        
                          </div>
                          </div>
                         
                          </div>

                      
                    </div>
                           
                     </div>
                     

                     <div class='form-actions' style='margin-bottom:0'>
                          <div class='row'>
                            <div class='col-sm-9 col-sm-offset-3'>
                             <a href="<?php echo WEB_URL; ?>supplier/view_deals">
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
         <?php $this->load->view('footer');?>
        </div>
      </section>
    </div>
    <!-- / jquery [required] -->
   <script src="<?=base_url();?>assets/javascripts/jquery/jquery.form.js"></script>
 
    <script src="<?=base_url();?>assets/javascripts/jquery/jquery.min.js" type="text/javascript"></script>
    <!-- / jquery mobile (for touch events) -->
    <script src="<?=base_url();?>assets/javascripts/jquery/jquery.mobile.custom.min.js" type="text/javascript"></script>
    <!-- / jquery migrate (for compatibility with new jquery) [required] -->
    <script src="<?=base_url();?>assets/javascripts/jquery/jquery-migrate.min.js" type="text/javascript"></script>
    <!-- / jquery ui -->
    <script src="<?=base_url();?>assets/javascripts/jquery/jquery-ui.min.js" type="text/javascript"></script>
    <!-- / jQuery UI Touch Punch -->
    <script src="<?=base_url();?>assets/javascripts/plugins/jquery_ui_touch_punch/jquery.ui.touch-punch.min.js" type="text/javascript"></script>
    <!-- / bootstrap [required] -->
    <script src="<?=base_url();?>assets/javascripts/bootstrap/bootstrap.js" type="text/javascript"></script>
    <!-- / modernizr -->
    <script src="<?=base_url();?>assets/javascripts/plugins/modernizr/modernizr.min.js" type="text/javascript"></script>
    <!-- / retina -->
    <script src="<?=base_url();?>assets/javascripts/plugins/retina/retina.js" type="text/javascript"></script>
    <!-- / theme file [required] -->
    <script src="<?=base_url();?>assets/javascripts/theme.js" type="text/javascript"></script>
    <!-- / demo file [not required!] -->
    <script src="<?=base_url();?>assets/javascripts/demo.js" type="text/javascript"></script>
    <!-- / START - page related files and scripts [optional] -->
    <script src="<?=base_url();?>assets/javascripts/plugins/validate/jquery.validate.min.js" type="text/javascript"></script>
    <script src="<?=base_url();?>assets/javascripts/plugins/validate/additional-methods.js" type="text/javascript"></script>
     <script src="<?=base_url();?>assets/javascripts/plugins/fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
    <script src="<?=base_url();?>assets/javascripts/plugins/select2/select2.js" type="text/javascript"></script>
    <script src="<?=base_url();?>assets/js/common.js" type="text/javascript"></script>
    
    <script src="<?=base_url();?>assets/javascripts/plugins/validate/custom.js"></script>
    <!-- / END - page related files and scripts [optional] -->
    <script>
      $("#datepicker1").datepicker({
  //  showOn: "button",
       minDate: 0,
    onSelect: function(dateText, inst) {
     
        //dateText comes in as MM/DD/YY
        var datePieces = dateText.split('/');
        var month = datePieces[0];
        var day = datePieces[1];
       // alert(day);
        var year = datePieces[2];
        //define select option values for
        //corresponding element
        $('select#arrmonth').val(month);
        $('select#arrday').val(day);
        $('select#arryear').val(year);
       
    }
});
    </script>
  </body>


</html>
