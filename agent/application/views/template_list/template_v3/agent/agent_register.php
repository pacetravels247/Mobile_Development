<?php
if(empty(validation_errors()) == false) {
  $view_tab = '';
  $edit_tab = ' active ';
} else {
  $view_tab = ' active ';
  $edit_tab = '';
}
if(empty(validation_errors()) == false){
  $message = 'hide';
}
//$message = 'hide';//Remove it in Soorya Travel


?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/css/select2.min.css" rel="stylesheet" />
<style type="text/css">
  .pop-info{
    font-family: monospace;
    font-size: 15px;
    margin-left: 19%;
    opacity: 0.9;
    position: fixed;
    z-index: 1000;
  }
.select2-container .select2-selection--single { height: 45px;}
.select2-container--default .select2-selection--single .select2-selection__rendered {line-height: 45px;}
</style>
<div class="newaddtab"></div>
<div class="background_login">    
    <div class="loadcity"></div>    
    <div class="clodnsun"></div>        
        <div class="reltivefligtgo">        
          <div class="flitfly"></div>       
         </div>  
        <div class="clearfix"></div>   
       <div class="busrunning">       
            <div class="runbus"></div>           
             <div class="runbus2"></div>        
                 <div class="roadd"></div>       
         </div>   
  </div>
<div class="b2b_agent_profile agent_regpage agentmyn">
<div class="container">
  <?php if(!empty($this->session->flashdata('message'))) {?>
  <div class="alert alert-success"> <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> <strong><?php echo $this->session->flashdata('message'); ?></strong> </div>
  <?php } ?>
  <div class="tab-content sidewise_tab">
    <div data-role="tabpanel" class="tab-pane active clearfix" id="profile">
      <div class="agent_regtr">
        
        <div class="agentreg_heading"> AGENT REGISTER
        
        <a href="<?=base_url()?>" class="gobacklink">Back</a> 
        
        </div>
        
        <div class="clearfix"></div>
        <!-- Edit User Profile starts-->
        <div class="tab-content">
          <div data-role="tabpanel filldiv" class="tab-pane active" id="show_user_profile">
            <form action="<?=base_url().'index.php/user/agentRegister'; ?>" method="post" name="edit_user_form" id="register_user_form" enctype="multipart/form-data" autocomplete="off">
              <div class="each_sections">
                <div class="sec_heading"><strong>1</strong>Personal Info</div>
                <div class="inside_regwrp">
                  <div class="col-sm-12 nopad">
                    <div class="col-sm-6 nopad">
                      <div class="wrap_space">
                        <div class="label_form">First Name <span class="text-danger">*</span></div>
                        <div class="col-xs-3 nopad">
                          <div class="select_wrap">
                            <select name="title" class="select_form noborderit smaltext" required>
                              <?=
                                               
                                          generate_options(get_enum_list('title'), (array)@$title)?>
                            </select>
                            
                          </div>
                        </div>
                        <div class="col-xs-9 nopad">
                          <div class="div_wrap">
                            <input type="text" name="first_name"  placeholder="first name" value="<?php echo set_value('first_name'); ?>" class="input_form alpha_space _guest_validate_field" required />
                          </div>
                        </div>
                         <?php if(!empty(form_error('first_name'))) { ?>
                        <div class="agent_error"><?php echo form_error('first_name');?></div>
                        <?php } ?>
                      </div>
                    </div>
                    <!-- <span>This Field is mandatory</span> -->
                    <div class="col-sm-6 nopad">
                      <div class="wrap_space">
                        <div class="label_form">Last Name <span class="text-danger">*</span></div>
                        <div class="div_wrap">
                          <input type="text" name="last_name" placeholder="last name" value="<?php echo set_value('last_name'); ?>" class="input_form alpha_space _guest_validate_field" required="required"/>
                          
                        </div>
                        <?php if(!empty(form_error('last_name'))) { ?>
                        <div class="agent_error"><?php echo form_error('last_name');?></div>
                        <?php }?>
                      </div>
                    </div>
                    <div class="col-sm-6 nopad">
                      <div class="wrap_space">
                        <div class="label_form">DOB <span class="text-danger">*</span></div>
                        <div class="div_wrap">
                          <input type="text" name="date_of_birth" placeholder="Date Of Birth" value="<?php echo set_value('date_of_birth'); ?>" class="input_form alpha_space _guest_validate_field" id="date_of_birth" required="required"/>
                        </div>
                        <?php if(!empty(form_error('date_of_birth'))) { ?>
                        <div class="agent_error"><?php echo form_error('date_of_birth');?></div>
                        <?php }?>
                      </div>
                    </div>
                    <div class="col-sm-6 nopad">
                      <div class="wrap_space">
                        <div class="label_form">Referred By <span class="text-danger">*</span></div>
                        <div class="div_wrap">
                          <input type="text" name="referred_by" placeholder="Referred By" value="<?php echo set_value('referred_by'); ?>" class="input_form alpha_space _guest_validate_field" id="referred_by" required="required"/>
                        </div>
                        <?php if(!empty(form_error('date_of_birth'))) { ?>
                        <div class="agent_error"><?php echo form_error('date_of_birth');?></div>
                        <?php }?>
                      </div>
                    </div>
                    <div class="col-sm-12 nopad">
                      <div class="wrap_space">
                        <div class="label_form">Mobile Number <span class="text-danger">*</span></div>
                        <div class="col-xs-6 nopad">
                          <div class="select_wrap">
                            <select name="country_code" class="select_form noborderit smaltext country_code" required>
                              
                              <?=generate_options($phone_code_array, (array)@$country_code)?>
                         
                            </select>
                          </div>
                        </div>
                        <div class="col-xs-6 nopad">
                          <div class="div_wrap">
                            <input type="text" name="phone" placeholder="mobile number" value="<?php echo set_value('phone'); ?>" class="input_form numeric _guest_validate_field"  required="required" maxlength="10">
                            
                          </div>
                        </div>
                       
                          <?php if(!empty(form_error('phone'))) { ?>
                        <div class="agent_error"><?php echo form_error('phone');?></div>
                        <?php }?>
                      </div>
                    </div>
                    <div class="col-sm-12 nopad">
                      <div class="wrap_space">
                        <div class="label_form">Email <span class="text-danger">*</span></div>
                        <div class="div_wrap">
                          <input type="email" id="user_email" name="email" maxlength="80" placeholder="email" value="<?php echo set_value('email'); ?>" class="input_form email" required="required"/>
                          
                        </div>
                        
                          <?php if(!empty(form_error('email'))) { ?>
                          <div class="agent_error"><?php echo form_error('email');?></div>
                       <?php } ?>
                      </div>
                    </div>
                  </div>
                  
                  <!--<div class="col-sm-4 nopad">
                    <div class="tnlepasport_b2b upload_wrap wrap_space">
                      <div class="label_form">Profile Image</div>
                      <div class="uplod_image"  style="background-image:url(<?=$GLOBALS['CI']->template->template_images('agent_demo.png')?>)">
                        <input type="file" id="profile_img_id" name="image" accept="image/*" class="hideupload" />
                        
                      </div>
                      
                    </div>
                  </div>-->

                </div>
              </div>
              <div class="clearfix"></div>
              
              <div class="each_sections">
                <div class="sec_heading"><strong>2</strong>Company Details</div>
                <div class="inside_regwrp">
                  
                    <div class="col-sm-6 nopad">
                      <div class="wrap_space">
                        <div class="label_form">Company Name <span class="text-danger">*</span></div>
                        <div class="div_wrap">
                          <input type="text" name="company_name" placeholder="Company name" value="<?php echo set_value('company_name'); ?>" class="input_form _guest_validate_field alpha_space" maxlength="45" required="required" />
                          
                        </div>
                         <?php if(!empty(form_error('company_name'))) { ?>
                        <div class="agent_error"><?php echo form_error('company_name');?></div>
                        <?php } ?>
                      </div>
                    </div>

                    <?php //if($active_data['api_country_list_fk'] == 92) { ?>
                  
                    <?php //} ?>
                    
                    <div class="col-sm-6 nopad">
                      <div class="wrap_space">
                        <div class="label_form">Address <span class="text-danger">*</span></div>
                        <div class="div_wrap">
                          <textarea class="input_textarea _guest_validate_field" name="address" placeholder="Address" required><?php echo set_value('address'); ?></textarea>
                          
                        </div>
                         <?php if(!empty(form_error('address'))) { ?>
                        <div class="agent_error"><?php echo form_error('address');?></div>
                        <?php } ?>
                      </div>
                    </div>
                    
                    
                    <div class="col-sm-6 nopad">
                      <div class="wrap_space">
                        <div class="label_form">Country <span class="text-danger">*</span></div>
                        <div class="select_wrap">
                        <?php 
                          if(empty(set_value('country')) == false) {
                            $default_country = set_value('country');
                          } else {
                            $default_country = $active_data['api_country_list_fk'];
                          }
                          if(empty(set_value('city')) == false) {
                            $default_city = set_value('city');
                          } else {
                            $default_city = $active_data['api_city_list_fk'];
                          }
                          if(empty(set_value('state')) == false) {
                            $default_state = set_value('state');
                          }
                          else{
                             $default_state = 0;
                          }
                        ?>
                            <select name="country" id="country_id" class="select_form select2" required>
                              <option value="">Select Country</option>
                              <?=generate_options($country_list, array($default_country));?>
                            </select>
                          </div>
                         <?php if(!empty(form_error('country'))) { ?>
                        <div class="agent_error"><?php echo form_error('country');?></div>
                        <?php } ?>
                      </div>
                    </div>
                    <div class="col-sm-6 nopad">
                      <div class="wrap_space">
                        <div class="label_form">State <span class="text-danger">*</span></div>
                        <div class="select_wrap">
                            <select name="state"  id="state_id" class="select_form stateselect
                            _guest_validate_field" required>
                            </select>
                            
                          </div>
                          <?php if(!empty(form_error('state'))) { ?>
                        <div class="agent_error"><?php echo form_error('state');?></div>
                        <?php } ?>
                      </div>
                    </div>
                    <div class="col-sm-6 nopad">
                      <div class="wrap_space">
                        <div class="label_form">City <span class="text-danger">*</span></div>
                        <div class="select_wrap">
                            <select name="city"  id="city_id" class="select_form cityselect
                            _guest_validate_field" required>
                              <option></option>
                            </select>
                            
                          </div>
                          <?php if(!empty(form_error('city'))) { ?>
                        <div class="agent_error"><?php echo form_error('city');?></div>
                        <?php } ?>
                      </div>
                    </div>
                      <div id="pan_data">
                    <div class="col-sm-6 nopad">
                      <div class="wrap_space">
                        <div class="label_form">Pan No *</div>
                        <div class="div_wrap">
                          <input type="text" name="pan_number" placeholder="Pan No" value="<?php echo set_value('pan_number'); ?>" id="pan_number" class="input_form _guest_validate_field" maxlength="10" required="required"/>
                          
                        </div>
                         <?php if(!empty(form_error('pan_number'))) { ?>
                        <div class="agent_error"><?php echo form_error('pan_number');?></div>
                        <?php } ?>
                      </div>
                    </div>
                    
                    
                    <div class="col-sm-6 nopad">
                      <div class="wrap_space">
                        <div class="label_form">Pan Card Holder Name *</div>
                        <div class="div_wrap">
                          <input type="text" name="pan_holdername" placeholder="Pan Holder Name"  value="<?php echo set_value('pan_holdername'); ?>" class="input_form alpha_space _guest_validate_field" maxlength="45" required="required"/>
                          
                        </div>
                       <?php if(!empty(form_error('pan_holdername'))) { ?>
                        <div class="agent_error"><?php echo form_error('pan_holdername');?></div>
                        <?php } ?>
                      </div>
                    </div>
                    </div>
                    <div class="col-sm-6 nopad">
                      <div class="wrap_space">
                        <div class="label_form">Pin Code <span class="text-danger">*</span></div>
                        <div class="div_wrap">
                          <input type="text" name="pin_code" placeholder="Pin"  value="<?php echo set_value('pin_code'); ?>" class="input_form _guest_validate_field numeric" maxlength="10" required />
                          
                        </div>
                       <?php if(!empty(form_error('pin_code'))) { ?>
                        <div class="agent_error"><?php echo form_error('pin_code');?></div>
                        <?php } ?>
                      </div>
                    </div>
                    
                    <div class="col-sm-6 nopad">
                      <div class="wrap_space">
                        <div class="label_form">Alternate Contact No</div>
                        <div class="div_wrap">
                          <input type="text" name="office_phone" placeholder="Alternate Contact No" value="<?php echo set_value('office_phone'); ?>" class="input_form numeric"  maxlength="15">
                          
                        </div>
                        <?php if(!empty(form_error('office_phone'))) { ?>
                        <div class="agent_error"><?php echo form_error('office_phone');?></div>
                        <?php } ?>
                      </div>
                    </div>               
                </div>
              </div>

              <div class="clearfix"></div>
              <div class="each_sections">
                <div class="sec_heading"> <strong>3</strong>GST Details</div>
                <div class="inside_regwrp">
                    <div class="col-sm-6 nopad">
                      <div class="wrap_space">
                        <div class="label_form">GSTIN </div>
                        <div class="div_wrap">
                          <input type="text" name="gst_number" placeholder="GST Number" value="<?php echo set_value('gst_number'); ?>" class="input_form" maxlength="30">
                          
                        </div>
                        <?php if(!empty(form_error('gst_number'))) { ?>
                        <div class="agent_error"><?php echo form_error('gst_number');?></div>
                        <?php } ?>
                      </div>
                    </div>                    
                    <div class="col-sm-6 nopad">
                      <div class="wrap_space">
                        <div class="label_form">Legal Name </div>
                        <div class="div_wrap">
                          <input type="text" name="gst_name" placeholder="Legal Name" value="<?php echo set_value('gst_name'); ?>" class="input_form"/>
                        </div>
                        <?php if(!empty(form_error('gst_name'))) { ?>
                        <div class="agent_error"><?php echo form_error('legal_name');?></div>
                        <?php } ?>
                      </div>
                    </div>
                    
                    <div class="col-sm-6 nopad">
                      <div class="wrap_space">
                        <div class="label_form">GST Email </div>
                        <div class="div_wrap">
                          <input type="text" name="gst_email" placeholder="GST Email" value="<?php echo set_value('gst_email'); ?>" class="input_form"/>
                        </div>
                          <?php if(!empty(form_error('gst_email'))) { ?>
                        <div class="agent_error"><?php echo form_error('gst_email');?></div>
                        <?php } ?>
                      </div>
                    </div>
                    
                    <div class="col-sm-6 nopad">
                      <div class="wrap_space">
                        <div class="label_form">Contact Number </div>
                        <div class="div_wrap">
                          <input type="text" name="gst_phone" placeholder="Contact Number" value="<?php echo set_value('gst_phone'); ?>" class="input_form"/>
                        </div>
                          <?php if(!empty(form_error('gst_phone'))) { ?>
                        <div class="agent_error"><?php echo form_error('gst_phone');?></div>
                        <?php } ?>
                      </div>
                    </div>

                    <div class="col-sm-6 nopad">
                      <div class="wrap_space">
                        <div class="label_form">Address </div>
                        <div class="div_wrap">
                          <textarea class="input_textarea" name="gst_address" placeholder="Address"><?php echo set_value('gst_address'); ?></textarea>
                        </div>
                          <?php if(!empty(form_error('gst_address'))) { ?>
                        <div class="agent_error"><?php echo form_error('gst_address');?></div>
                        <?php } ?>
                      </div>
                    </div>

                    <div class="col-sm-6 nopad">
                      <div class="wrap_space">
                        <div class="label_form">GST Certificate </div>
                        <div class="div_wrap">
                          <input type="file" name="gst_certificate" placeholder="Upload GST Certificate" value="<?php echo set_value('gst_certificate'); ?>" class="input_form"/>
                        </div>
                          <?php if(!empty(form_error('gst_certificate'))) { ?>
                        <div class="agent_error"><?php echo form_error('gst_certificate');?>
                        </div>
                        <?php } ?>
                      </div>
                    </div>
                </div>
              </div>

              <div class="clearfix"></div>
              
              <div class="each_sections">
                <div class="sec_heading"> <strong>4</strong>Login Info</div>
                <div class="inside_regwrp">
                  <div class="col-sm-6 nopad">
                      <div class="wrap_space">
                        <div class="label_form">User Name <span class="text-danger">*</span></div>
                        <div class="div_wrap">
                          <input type="email" id="user_name" name="user_name" placeholder="email" maxlength="80" value="<?php echo set_value('user_name'); ?>" class="input_form email" required="required"/>
                        </div>
                        <?php if(!empty(form_error('user_name'))) { ?>
                        <div class="agent_error"><?php echo form_error('user_name');?></div>
                        <?php } ?>
                      </div>
                    </div>
                    
                    <div class="clearfix"></div>
                    <?php $password_reqs = "<ul>
                        <li>Minimum 8 Charectors.</li>
                        <li>One special Char: !, @, #, $, %, ^, &, *, (, )</li>
                        <li>One Capital letter: A-Z</li>
                        <li>One Capital Number: 1-9</li>
                      </ul>"; ?>
                    <div class="col-sm-6 nopad">
                      <div class="wrap_space">
                        <div class="label_form">Password <span class="text-danger">*</span></div>
                        <div class="div_wrap">
                          <input type="password" name="password" placeholder="password" value="<?php echo set_value('password'); ?>" class="input_form pass _guest_validate_field" required="required" data-container="body" data-toggle="popover" data-original-title="Must Include Below" data-placement="bottom" data-trigger="hover focus" data-content="<?=$password_reqs?>"/>
                        </div>
                        <?php if(!empty(form_error('password'))) { ?>
                        <div class="agent_error"><?php echo form_error('password');?></div>
                        <?php } ?>
                      </div>
                    </div>
                    
                    <div class="col-sm-6 nopad">
                      <div class="wrap_space">
                        <div class="label_form">Confirm Password <span class="text-danger">*</span></div>
                        <div class="div_wrap">
                          <input type="password" name="password_c" placeholder="retype password" value="<?php echo set_value('password_c'); ?>" class="input_form pass _guest_validate_field" required="required"/>
                        </div>
                          <?php if(!empty(form_error('password_c'))) { ?>
                        <div class="agent_error"><?php echo form_error('password_c');?></div>
                        <?php } ?>
                      </div>
                    </div>
                    
                </div>
              </div>
              
              
              
              <div class="clearfix"></div>
              
              
  <div class="submitsection">
  
    <div class="acceptrms">
      <div class="squaredThree">
          <input type="checkbox" value="<?php echo set_value('term_condition'); ?>" required="" name="term_condition" class="airlinecheckbox validate_user_register" 
          id="term_condition" 
        <?php echo (set_value('term_condition') ? 'checked' : ''); ?>>
          <label for="term_condition"></label>
        </div>
        <label for="term_condition" class="lbllbl">I accept the <a target="_balnk" href="<?=base_url()?>index.php/general/cms/terms&conditions/8">agency terms and conditons</a></label>
    </div>
    
    <div class="clearfix"></div>
    
    <button type="submit" class="btnreg_agent">Register</button>
    
  </div>
              
              
              

              
            </form>
          </div>
          
          
          
          <div data-role="tabpanel" class="tab-pane clearfix" id="edit_user_profile"> </div>
        </div>
        <!-- Edit User Profile Ends--> 
        
      </div>
    </div>
  </div>
</div>
<?php
$datepicker = array(array('date_of_birth', PAST_DATE));
$GLOBALS['CI']->current_page->set_datepicker($datepicker);
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>
<script type="text/javascript">
var default_country = '<?=$default_country;?>';
var default_city = '<?=$default_city;?>';
var default_state = '<?=$default_state;?>';


  $(document).ready(function(){
     $('[data-toggle="popover"]').popover({html: true});
    $('.select2').select2();
    $('.cityselect').select2({
      placeholder: "Select City",
    });
    $('.stateselect').select2({
      placeholder: "Select State",
    });
    $(".country_code").val(default_country);
    $('.country_code').select2();
    get_city_list();
    get_state_list();
    //get the state
    $('#country_id').on('change', function(){
      country_origion = $(this).val();
      if(country_origion == '92'){
        $("#pan_data").css("display", "block");
        $("#pan_data input").addClass('_guest_validate_field');
        $("#pan_data input").attr('required', 'required');
      }
      else{
        $("#pan_data").css("display", "none");
        $("#pan_data input").removeClass('_guest_validate_field');
        $("#pan_data input").removeAttr('required');
      }
      get_city_list();
      get_state_list();
    });
    function get_city_list(country_id)
    {
      var country_id = $('#country_id').val();
      if(country_id == ''){
          $("#city_id").empty().html('<option value = "" selected="">Select City</option>');
         return false;
         } 
      $.post(app_base_url+'index.php/ajax/get_city_lists',{  country_id : country_id},function( data ) {
         $("#city_id").empty().html(data);
         $('#city_id').val(default_city)
      });
    }
    function get_state_list(country_id)
    {
      var country_id = $('#country_id').val();
      if(country_id == ''){
          $("#state_id").empty().html('<option value = "" selected="">Select State</option>');
         return false;
         } 
      $.post(app_base_url+'index.php/ajax/get_state_lists',{  country_id : country_id},function( data ) {
        //alert(data.html);
         $("#state_id").empty().html(data);
         $('#state_id').val(default_state);
      });
    }
    //Auto populate the user email to the user name
    $('#user_email').on('blur', function(){
      var user_email = $(this).val().trim();
      if(user_email !='') {
        $('#user_name').val(user_email);
      }
    });

      $(document).on('click', '.btnreg_agent', function(){
                  
              var count = 0;
              $('._guest_validate_field').each( function () {
                      if(this.value.trim() == '') {
                             count++;
                  $(this).siblings("span").find(".formerror").hide();           
                           $(this).addClass('invalid-ip').parent().append( 
                 "<span id='name_error'><div class='formerror'style='color:red'>This Field is mandatory</div></span>");
                      }
                    

                     });
                if(count > 0){
                        return false;
                      }
      })


$('._guest_validate_field').focus( function () {
    $(this).removeClass('invalid-ip');
    $(this).parent().find(".formerror").hide();
  });


    $("#term_condition").on('click',function(){
      if($('#term_condition').is(':checked')){
      $('#term_condition').val('1');
          }else{
      $('#term_condition').val('0');
          } 

})

    
    
    });

//image preview
$(function() {
    $("#profile_img_id").on("change", function()
    {
        var files = !!this.files ? this.files : [];
        if (!files.length || !window.FileReader) return; // no file selected, or no FileReader support
 
        if (/^image/.test( files[0].type)){ // only image file
            var reader = new FileReader(); // instance of the FileReader
            reader.readAsDataURL(files[0]); // read the local file
 
            reader.onloadend = function(){ // set image data as background of div
                $(".uplod_image").css("background-image", "url("+this.result+")");
            }
        }else{
          $("#profile_img_id").val('');
        }
    });
});


</script>

