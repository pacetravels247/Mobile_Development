<?php
/*if(empty(validation_errors()) == false) {
	$view_tab = '';
	$edit_tab = ' active ';
} else {
	$view_tab = ' active ';
	$edit_tab = '';
}*/
// debug($form_data);exit;
extract($form_data);
$full_name = get_enum_list('title', $title).' '.$first_name.' '.$last_name;
?>
<div class="b2b_agent_profile">
      <div class="tab-content sidewise_tab">
       
        <div role="tabpanel" class="tab-pane active clearfix" id="profile">
          <div class="dashdiv">
            <div class="alldasbord">
              <div class="userfstep">
                <div class="step_head">
                  <h3 class="welcmnote">Hi,
                    <?=$full_name?>
                  </h3>
                  <a href="#edit_user_profile" data-aria-controls="home" data-role="tab" data-toggle="tab" class="editpro" id="edit_profile_btn">Edit profile</a> </div>
                <div class="clearfix"></div>
                <!-- Edit User Profile starts-->
                <div class="tab-content">
                  <div role="tabpanel filldiv" class="tab-pane active" id="show_user_profile">
                    <div class="colusrdash"> <img src="<?=(empty($image) == false ? $GLOBALS['CI']->template->domain_images($image) : $GLOBALS['CI']->template->template_images('face.png'))?>" alt="profile Image" /> </div>
                    <div class="useralldets">
                      <h4 class="dashuser">
                        <?=$full_name?>
                      </h4>
                      <div class="rowother"> <span class="far fa-user"></span> <span class="labrti">
                      	<span class="inlabl_name">Agency Name</span>
                        <?=(empty($agency_name) == true ? 'Agency Name' : $agency_name).' - '.$uuid?>
                        </span>
                       </div>
                      <div class="rowother"> <span class="far fa-envelope"></span> <span class="labrti">
                      	<span class="inlabl_name">Email</span>
                        <?=(empty($email) == true ? '---' : $email)?>
                        </span>
                       </div>
                      <?php if((empty($pan_number)) == false){ ?>
                      <div class="rowother"> <span class="far fa-credit-card"></span> <span class="labrti">
                        <span class="inlabl_name">PAN Number</span>
                        <?=(empty($pan_number) == true ? '---' : $pan_number)?>
                     
                        </span>
                        </div>
                       <?php } ?>
                       
                      <div class="rowother"> <span class="far fa-mobile"></span> <span class="labrti">
                      	<span class="inlabl_name">Phone Number</span>
                        <?=(($phone == 0 || $phone == '') ? '---':$mobile_code.' '.$phone)?>
                        </span>
                      </div>
                      <div class="rowother"> <span class="far fa-phone"></span> <span class="labrti">
                      	<span class="inlabl_name">Office Phone</span>
                        <?=(($office_phone == 0 || $office_phone == '') ? '---': $office_phone)?>
                        </span>
                      </div>
                      <div class="rowother"> <span class="far fa-map-marker"></span> <span class="labrti">
                      	<span class="inlabl_name">Address</span>
                        <?=(empty($address) == true ? '---' : $address)?>
                        </span> </div>
                    </div>
                  </div>
                  <div role="tabpanel" class="tab-pane clearfix" id="edit_user_profile">
                    <form action="<?=base_url().'index.php/user/account?'.$_SERVER['QUERY_STRING']?>" method="post" name="edit_user_form" id="edit_user_form" enctype="multipart/form-data" autocomplete="off">
                      <div class="col-md-12 text-danger">
                      <strong><?php echo validation_errors(); ?></strong>
                      </div>
                      <input type="hidden" name="user_id" value="<?=$user_id?>">
                      <input type="hidden" name="uuid" value="<?=$uuid?>">
                      <input type="hidden" name="email" value="<?=$email?>">
                      <div class="infowone">
                        <div class="clearfix"></div>
                        <div class="paspertorgn2 paspertedit">
                          <div class="col-xs-3 margpas">
                            <div class="tnlepasport_b2b">
                              <div class="paspolbl ">Title <span class="text-danger">*</span></div>
                              <div class="lablmain ">
                                <select name="title" class="clainput" required="required">
                                  <?=generate_options(get_enum_list('title'), (array)$title)?>
                                </select>
                              </div>
                            </div>
                          </div>
                          <div class="col-xs-4 margpas">
                            <div class="tnlepasport_b2b">
                              <div class="paspolbl ">FirstName <span class="text-danger">*</span></div>
                              <div class="lablmain ">
                                <input type="text" name="first_name" placeholder="first name" value="<?=$first_name?>" class="clainput alpha" maxlength="45" required />
                              </div>
                            </div>
                          </div>
                          <div class="col-xs-4 margpas">
                            <div class="tnlepasport_b2b">
                              <div class="paspolbl ">LastName <span class="text-danger">*</span></div>
                              <div class="lablmain ">
                                <input type="text" name="last_name" placeholder="last name" value="<?=$last_name?>" class="clainput alpha" maxlength="45" required="required"/>
                              </div>
                            </div>
                          </div>
                          <div class="col-xs-3 margpas">
                            <div class="tnlepasport_b2b">
                              <div class="paspolbl ">CountryCode<span class="text-danger">*</span></div>
                              <div class="lablmain ">
                                <select name="country_code" id="country_code" class="clainput" required="required">
                                <?php //debug($country_code_list);exit;?>
                                  <?=generate_options($phone_code_array, (array)$form_data['country_code'])?>
                                </select>
                              </div>
                            </div>
                          </div>
                          <div class="col-xs-4 margpas">
                            <div class="tnlepasport_b2b">
                              <div class="paspolbl ">MobileNumber <span class="text-danger">*</span></div>
                              <div class="lablmain ">
                                <input type="text" name="phone" placeholder="mobile number" value="<?=(($phone == 0 || $phone == '') ? '': $phone)?>" class="clainput numeric"  required="required" maxlength="10">
                              </div>
                            </div>
                          </div>
                          <div class="col-xs-4 margpas">
                            <div class="tnlepasport_b2b">
                              <div class="paspolbl ">DateofBirth <span class="text-danger">*</span></div>
                              <div class="lablmain ">
                                <input type="text" name="date_of_birth" id="date_of_birth" placeholder="dob" value="<?=((strtotime($date_of_birth) <= 0) ? '': $date_of_birth)?>" class="clainput" readonly required="required"/>
                              </div>
                            </div>
                          </div>
                         <?php if((empty($pan_number)) == true){
                          $style = 'style="display: none"';
                          }?>
                          <div id="pan_data" <?php echo $style; ?>>
                            <div class="col-xs-4 margpas">
                              <div class="tnlepasport_b2b">
                                <div class="paspolbl ">PAN Number <span class="text-danger">*</span></div>
                                <div class="lablmain ">
                                  <input type="text" name="pan_number" placeholder="PAN Number" value="<?=$pan_number?>" class="clainput"  required="required" maxlength="10">
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-xs-4 margpas">
                            <div class="tnlepasport_b2b">
                              <div class="paspolbl ">Office Phone <span class="text-danger">*</span></div>
                              <div class="lablmain ">
                                <input type="text" name="office_phone" placeholder="Office Number" value="<?=(($office_phone == 0 || $office_phone == '') ? '': $office_phone)?>" class="clainput"  required="required" maxlength="14">
                              </div>
                            </div>
                          </div>
                          <div class="col-xs-5 margpas">
                            <div class="tnlepasport_b2b">
                              <div class="paspolbl ">Address <span class="text-danger">*</span></div>
                              <div class="lablmain ">
                                <textarea name="address" placeholder="address" class="clainput" required="required"><?=$address?>
							</textarea>
                              </div>
                            </div>
                          </div>
                          <div class="col-xs-5 margpas">
                            <div class="tnlepasport_b2b">
                              <div class="paspolbl ">ProfileImage</div>
                              <div class="lablmain ">
                                <input type="file" name="image" accept="image/*" />
                              </div>
                            </div>
                          </div>
                          <div class="clearfix"></div>
                          <button type="submit" class="savepspot">Update</button>
                          <a href="#show_user_profile" data-aria-controls="home" data-role="tab" data-toggle="tab" class="cancelll">Cancel</a> </div>
                      </div>
                    </form>
                  </div>
                </div>
                <!-- Edit User Profile Ends--> 
                
              </div>
              <div class="clearfix"></div>
            </div>
          </div>
        </div>
      </div>
</div>
<script>
$(document).ready(function() {
	<?php if(empty(validation_errors()) == false) { ?>
		$('#edit_profile_btn').trigger('click');
	<?php } ?>
	
	$('.editpasport').click(function(){
		$(this).parent().parent('.infowone').addClass('editsave');
	});	
	$('.cancelll').click(function(){
		$(this).parent().parent('.infowone').removeClass('editsave');
	});	
   $('#country_code').on('change', function(){
      country_origion = $(this).val();
     
      if(country_origion == '92'){
        $("#pan_data").css("display", "block");
      }
      else{
        $("#pan_data").css("display", "none");
      }
      
    });
});
</script>
<?php
$datepicker = array(array('date_of_birth', PAST_DATE));
$GLOBALS['CI']->current_page->set_datepicker($datepicker);
?>