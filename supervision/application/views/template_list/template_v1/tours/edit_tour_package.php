<style type="text/css">
	img.gal_img {
    width: 100px !important;
    height: 80px;
    margin-bottom: 10px;
}
.remove_hotel .fa-minus {
    font-size: 8px;
}
.add_hotel .fa-plus {
    font-size: 10px;
}
	.mode {
    border: 1px solid #ccc;
    float: left;
    width: 100%;
    padding: 0 0 20px;
}
	.hotels_block {float: left;}
	.controls_new {width: 100%;float: left;}
	.mode_new {border: 1px solid #ccc;}
</style>
<?php
error_reporting(0);?>
<div id="Package" class="bodyContent col-md-12">
	<div class="panel panel-default">
	<?php echo $this->session->flashdata('msg'); ?>
		<!-- PANEL WRAP START -->
		<div class="panel-heading">
			<!-- PANEL HEAD START -->
			<div class="panel-title">
				<ul class="nav nav-tabs nav-justified" role="tablist" id="myTab">
					<!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE START-->
					<li role="presentation" class="active" id="add_package_li"><a
						href="#add_package" aria-controls="home" role="tab"
						data-toggle="tab">Update Holiday Package Manager </a></li>					
				</ul>
			</div>
		</div>
		<!-- PANEL HEAD START -->
		<div class="panel-body">
			<!-- PANEL BODY START -->
			<form
				action="<?php echo base_url(); ?>index.php/tours/edit_tour_package_save"
				method="post" enctype="multipart/form-data" id="form form-horizontal validate-form"
				class='form form-horizontal validate-form' onsubmit="return validation();">
				<div class="tab-content">
					<!-- Add Package Starts -->
					<div role="tabpanel" class="tab-pane active" id="add_package">
						   <div class="col-md-12">
                           <form
				action="<?php echo base_url(); ?>index.php/tours/add_tour_save"
				method="post" enctype="multipart/form-data" id="form form-horizontal validate-form"
				class='form form-horizontal validate-form'>
				
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Trip Type <span style = "color:red">*</span>
								</label>
								<div class='col-sm-3 col-md-8 controls'>
									<select class=' form-control'  name='trip_type' id="trip_type"  >
										<option value="">Select trip type</option>
										<option value="1" <?php if($tour_data['trip_type']==1) echo "selected"; ?>>International</option>
										<option value="2" <?php if($tour_data['trip_type']==2) echo "selected"; ?>>Domestic</option>
									</select>	
											
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Tour Code<span style = "color:red">*</span>
								</label>
								<div class='col-sm-8 controls'>
									<input type="text" name="tour_code" id="tour_code"
										placeholder="Enter Tour Code" data-rule-required='true' value="<?=$tour_data['tour_code']?>"
										class='form-control add_pckg_elements' required disabled>									
								</div>
							</div>
						    <!--<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Package ID
								</label>
								<div class='col-sm-4 controls'>
									<input type="text" value="<?=$tour_data['package_id']?>" class='form-control add_pckg_elements' disabled>									
								</div>
							</div>-->
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Package Name
								</label>
								<div class='col-sm-8 controls'>
									<input type="text" name="package_name" id="package_name" value="<?=string_replace_encode($tour_data['package_name'])?>"
										placeholder="Enter Package Name" data-rule-required='true'
										class='form-control add_pckg_elements' required>									
								</div>
							</div>
							<!--<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Package Description 
								</label>
								<div class='col-sm-8 controls'>
									<input type="text" name="package_description" id="package_description" value="<?=string_replace_encode($tour_data['package_description'])?>"
										placeholder="Enter Package Description" data-rule-required='true'
										class='form-control add_pckg_elements' >									
								</div>
							</div>
							
							<div class='form-group ' id="select_date">
								<label class='control-label col-sm-3' for='validation_current'>Expiry Date 
								</label>
								<div class='col-sm-4 controls'>
								<input type="text" name="tour_expire_date" id="tour_expire_date" class="form-control" value="<?=string_replace_encode($tour_data['expire_date'])?>" placeholder="Choose Date" data-rule-required='true'  readonly> 
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Supplier Name
								</label>
								<div class='col-sm-8 controls'>
									<input type="text" name="supplier_name" id="supplier_name" value="<?=string_replace_encode($tour_data['supplier_name'])?>"
										placeholder="Enter Supplier Name" 
										class='form-control add_pckg_elements' >									
								</div>
							</div>-->

						<!-- 	<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Choose Theme
								</label>
								<div class='col-sm-4 controls'>
								<select class='select2 form-control' data-rule-required='true' name='tour_type' id="tour_type" data-rule-required='true' required>
                                <option value="">Choose Theme</option>
                                <?php
                                foreach($tour_type as $k => $v)
                                {
                                	if($tour_data['tour_type']==$v['id']){$selected='selected';}
                                	else{$selected='';}
                                	echo '<option value="'.$v['id'].'" '.$selected.'>'.$v['tour_type_name'].' </option>';
                                }
                                ?>
								</select>				
								</div>
							</div>
 -->

 						<!-- 	<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Choose Theme <span style = "color:red">*</span>
								</label>
								<div class='col-sm-4 controls'>
								<select class='select2 form-control' data-rule-required='true' name='tour_type[]' id="tour_type" multiple data-rule-required='true' required>
                                <option value="">Choose Theme</option>
                                <?php
                               foreach($tour_type as $k => $v)
                                {
                                	echo '<option value="'.$v['id'].'">'.$v['tour_type_name'].' </option>';
                                }
                                ?>
								</select>				
								</div>
							</div> -->

								<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Choose Category
								</label>
								<div class='col-sm-8 controls nopad'>
								<select class='select2 form-control'  name='tour_type[]' id="tour_type" multiple >
                                <option value="">Choose Activity</option>
                                <?php
                                  $tour_data['tour_type'] = explode(',', $tour_data['tour_type']);
                               	  $tour_type_data = $tour_data['tour_type'];
                                foreach($tour_type as $k => $v)
                                {

                                	if(in_array($v['id'],$tour_type_data)){$selected='selected';}
                                	else{$selected='';}
                                	echo '<option value="'.$v['id'].'" '.$selected.'>'.$v['tour_type_name'].' </option>';
                                }
                                ?>
								</select>				
								</div>
							</div>
							<div class='form-group ini_banner_img'>
                                <label class='control-label col-sm-3' for='validation_current'>Banner Image
                                </label>
								<div class='col-sm-8 controls'>
									<?php
									if($tour_data['banner_image']){
									echo '<img class="banner_imgs" src="'.$this->template->domain_images( $tour_data['banner_image']) . '" style="width:200px;height:130px;">';
									}
									?>										
								</div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-sm-3' for='validation_current'>Change Banner Image
                                </label>
                                <div class='col-sm-8 controls'>
                                    <input type="file" name="banner_image" id="banner_image" class='form-control'>	
                                    <?=img_size_msg(200,300)?>								
                                </div>
                            </div>
							<!--<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Choose Theme
								</label>
								<div class='col-sm-4 controls'>
								<select class='select2 form-control' name='theme[]' id="theme" multiple>
                                <option value="">Choose Theme</option>
                                <?php
                                /*theme = $tour_data['theme'];
                                $theme = json_decode($theme,1); */
	                        	$tour_data['theme'] = explode(',', $tour_data['theme']);
	                            $theme = $tour_data['theme'];
                                foreach($tour_subtheme as $k => $v)
                                {
                                	if(in_array($v['id'],$theme)){$selected='selected';}
                                	else{$selected='';}
                                	echo '<option value="'.$v['id'].'" '.$selected.'>'.$v['tour_subtheme'].' </option>';
                                }
                                ?>
								</select>				
								</div>
							</div>


							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Choose Destination
								</label>
								<div class='col-sm-8 controls'>
								<select class='select2 form-control' data-rule-required='true' name='destination' id="destination" data-rule-required='true' required>
                                <option value="">Choose Destination</option>
                                <?php
                                foreach($tour_destinations as $tour_destinations_key => $tour_destinations_value)
                                {
                                	if($tour_data['destination']==$tour_destinations_value['id'])
                                    {$selected='selected';}else{$selected='';}
                                	echo '<option value="'.$tour_destinations_value['id'].'" '.$selected.'>'.string_replace_encode($tour_destinations_value['destination']).' [ '.$tour_destinations_value['type'].' ]</option>';
                                }
                                ?>
								</select>				
								</div>
							</div>-->
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Package Type <span style = "color:red">*</span>
								</label>
								<div class='col-sm-3 col-md-8 controls'>
									<select class=' form-control'  name='package_type' id="package_type"  >
										<option value="">Select Package type</option>
										<option value="group" <?php if($tour_data['package_type']=='group') echo "selected"; ?>>Grouped / Fixed</option>
										<option value="fit" <?php if($tour_data['package_type']=='fit') echo "selected"; ?>>FIT / Customised</option>
									</select>			
								</div>
							</div>
							<?php 
								if($tour_data['package_type']=='group'){
									$group_hide_class="";
									$fit_hide_class="hidden";
								}else{
									$group_hide_class="hidden";
									$fit_hide_class="";
								}
							?>
							<div class='form-group valid <?=$fit_hide_class?> multi_date_block' >
								<?php if(!empty($valid_frm_to_date)){
									foreach($valid_frm_to_date as $val_key =>$valid_val){ ?>
								<div class="form-group multi_frm_to_date">
									<label class='control-label col-sm-3'>Valid From <span style = "color:red">*</span></label>
									<div class='col-sm-3 controls'>
										<input type="date" placeholder="Enter Date" name="valid_frm[]" value="<?=$valid_val['valid_from']?>" class='form-control add_pckg_elements' id="valid_frm">
									</div>
									<label class='control-label col-sm-1'>Valid To <span style = "color:red">*</span></label>
									<div class='col-sm-3 controls'>
										<input type="date" placeholder="Enter Date" name="valid_to[]" value="<?=$valid_val['valid_to']?>" class='form-control add_pckg_elements' id="valid_to">
									</div>
									<?php if($val_key==0){ ?>
										<div class='col-sm-2 controls'>
											<button type="button" class="btn btn-primary add_date">ADD</button>
										</div>
									<?php }else{ ?>
										<div class='col-sm-2 controls'>
											<button type="button" class="btn btn-primary remove_date">Remove</button>
										</div>
									<?php } ?>
								</div>
								<div class="clearfix"></div>
								<?php }
								}else{?>
									<div class="form-group multi_frm_to_date">
									<label class='control-label col-sm-3'>Valid From <span style = "color:red">*</span></label>
									<div class='col-sm-3 controls'>
										<input type="date" placeholder="Enter Date" name="valid_frm[]" value="<?=$valid_val['valid_from']?>" class='form-control add_pckg_elements' id="valid_frm">
									</div>
									<label class='control-label col-sm-1'>Valid To <span style = "color:red">*</span></label>
									<div class='col-sm-3 controls'>
										<input type="date" placeholder="Enter Date" name="valid_to[]" value="<?=$valid_val['valid_to']?>" class='form-control add_pckg_elements' id="valid_to">
									</div>
									<?php if($val_key==0){ ?>
										<div class='col-sm-2 controls'>
											<button type="button" class="btn btn-primary add_date">ADD</button>
										</div>
									<?php }else{ ?>
										<div class='col-sm-2 controls'>
											<button type="button" class="btn btn-primary remove_date">Remove</button>
										</div>
									<?php } ?>
								</div>
								<div class="clearfix"></div>
							<?php	}?>
								
							</div>
							<div class="form-group multi_date <?=$group_hide_class?>">
								<label class='control-label col-sm-3'>Select Multi Dates<span style = "color:red">*</span></label>
								<div class='col-sm-8 controls multi_date_sections'>		<input id="multi_date" placeholder="Enter Date" name="multi_date" class="form-control add_pckg_elements" autocomplete="off" value="<?=$tour_data['multi_date']?>">
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Choose Region
								</label>
								<div class='col-sm-8 controls nopad'>
								<select class='select2 form-control' name='tours_continent[]' id="tours_continent" multiple>
                                <option value="">Choose Region</option>
                                <?php
								$tour_data['tours_continent'] = explode(',', $tour_data['tours_continent']);
                               	$tour_county_data = $tour_data['tours_continent'];
                              /* foreach($tours_continent as $tours_continent_key => $tours_continent_value)
                                {
                                	$tours_continent = $tour_data['tours_continent'];
                                	if($tours_continent_value['id']==$tours_continent){$selected = 'selected';}
                                	else{$selected = '';}

                                	echo '<option value="'.$tours_continent_value['id'].'" '.$selected.'>'.$tours_continent_value['name'].' </option>';
                                }*/
								foreach($tours_continent as $tours_continent_key => $tours_continent_value)
                                {
						
                                	if(in_array($tours_continent_value['id'],$tour_county_data)){$selected='selected';
                                	//echo $selected;exit;
                                }
                                	else{$selected='';}
                                	echo '<option value="'.$tours_continent_value['id'].'" '.$selected.'>'.$tours_continent_value['name'].' </option>';
                                }
                                ?>
								</select>				
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Choose Country
								</label>
								<div class='col-sm-8 controls nopad'>
								<select class='select2 form-control'  name='tours_country[]' id="tours_country" multiple >
                                <option value="">Choose Country</option>
                                <?php
                                  $tour_data['country'] = explode(',', $tour_data['tours_country']);
                               	  $tour_county_data = $tour_data['country'];
                               //	debug($tour_county_data);
								//debug($tours_continent_country);
                                foreach($tours_continent_country as $tours_country_key => $tours_country_value)
                                {

                                	if(in_array($tours_country_value['id'],$tour_county_data)){$selected='selected';
                                	//echo $selected;exit;
                                }
                                	else{$selected='';}
                                	echo '<option value="'.$tours_country_value['id'].'" '.$selected.'>'.$tours_country_value['name'].' </option>';
                                }
                                ?>
								</select>				
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Choose City
								</label>
								<div class='col-sm-8 controls nopad'>
								<select class='select2 form-control'  name='tours_city[]' id="tours_city" multiple >
                                <option value="">Choose City</option>
                                <?php
                                  $tour_data['city'] = explode(',', $tour_data['tours_city']);
                               	  $tour_city_data = $tour_data['city'];
								 // debug($tour_city_data);
                               	 //debug($tours_country_city);exit;
                                foreach($tours_country_city as $tours_city_key => $tours_city_value)
                                {

                                	if(in_array($tours_city_value['id'],$tour_city_data)){$selected='selected';
                                	//echo $selected;exit;
                                }
                                	else{$selected='';}
                                	echo '<option value="'.$tours_city_value['id'].'" '.$selected.'>'.$tours_city_value['CityName'].' </option>';
                                }
                                ?>
								</select>				
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Choose Supplier <span style = "color:red">*</span>
								</label>
								<div class='col-sm-6 col-md-8 controls'>
									<select class='select2 form-control' data-rule-required='true' name='supplier[]' id="supplier" data-rule-required='true' multiple required>          
										
										<?php
											//debug($tour_data['supplier_name']);
											//debug($tours_supplier);exit;
											$tour_data['supplier_name'] = explode(',', $tour_data['supplier_name']);
											$tour_supplier_name_data = $tour_data['supplier_name'];
											foreach($tours_supplier as $t_key =>$t_val){
												if(in_array($t_val['id'],$tour_supplier_name_data)){
													$selected='selected';
												}else{
													$selected='';
												}
												echo '<option value="'.$t_val['id'].'" '.$selected.'>'.$t_val['supplier_name'].' </option>';
											
										
											}
										?>									
									</select>				
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Choose Concerned Person <span style = "color:red">*</span>
								</label>
								<div class='col-sm-6 col-md-8 controls'>
									<select class='select2 form-control' data-rule-required='true' name='concerned_supplier[]' id="concerned_supplier" multiple data-rule-required='true' required>         
									
									<?php
									//debug($tour_data);exit;
										$tour_data['concerned_person'] = explode(',', $tour_data['concerned_person']);
										$tour_concerned_person_data = $tour_data['concerned_person'];
										foreach($tours_concerned_person as $t_key =>$t_val){
											if(in_array($t_val['id'],$tour_concerned_person_data)){
												$selected='selected';
											}else{
												$selected='';
											}
											echo '<option value="'.$t_val['id'].'" '.$selected.'>'.$t_val['contact_person'].' </option>';
										
									
										}
									?>
									</select>				
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Choose Duration
								</label>
								<div class='col-sm-8 controls'>
								<input type="hidden" name="duration"  value="<?=$tour_data['duration']?>">	
								<select class='select2 form-control' name="duration" id="duration">
									 <option value="">Choose Duration</option>
									 <?php
									 for($dno=0;$dno<=30;$dno++)
									 {
									  if($dno==1)
									  { 
									   $DayNight = ($dno+1).' Days | '.($dno).' Night';
									  }else
									  { 
									   $DayNight = ($dno+1).' Days | '.($dno).' Nights';
									  }  
									  if($tour_data['duration']==$dno)
									  {
									   $selected='selected';
									  }else
									  {
									   $selected='';
									  }
									  echo '<option value="'.$dno.'" '.$selected.'>'.$DayNight.'</option>';
									 }
									 ?>
								</select>				
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for=''>Hotels
								</label>
								<div class="hotels_block col-md-8 nopad">
									<?php 
									//debug($tours_sel_hotels);
									//debug($tours_hotels);
									
									foreach($tours_sel_hotels as $sel_h_key => $sel_h_val) {?>
									<div class="controls_new">
										<div class="controls" style="padding: 0px; margin: 13px;">
										<div class="mode add_city_hotel_here"><label class="control-label col-sm-12" style="text-align: left;margin-bottom: 6px;"><?=$sel_h_val[0]['city']?> Hotel <a class="btn add_hotel">Add hotel <i class="fa fa-plus" aria-hidden="true"></i></a></label>
										<?php 
											foreach($sel_h_val as $sel_key => $sel_val){
												if($sel_key>=1){
													$remove_text='<a class="remove_hotel btn pull-right">Remove <i class="fa fa-minus" aria-hidden="true"></i></a><br/>';
												}else{
													$remove_text="";
												}?>
										<div class="this_city_hotel"><?=$remove_text?><div class="col-sm-6 controls"><input type="hidden" name="hotel_city[]" placeholder="City Name" value="<?=$sel_val['city']?>" class="form-control" ><input type="hidden" name="hotel_city_id[]" placeholder="City Name" value="<?=$sel_val['city_id']?>" class="form-control" ></div><div class="col-sm-12 controls"><select class="select2 form-control" data-rule-required="true" name="hotel_name[]"  data-rule-required="true" >
									<?php	foreach($tours_hotels[$sel_val['city_id']] as $key => $value) {
												if($value['id']==$sel_val['hotel_id']){$selected='selected';}
												else{$selected='';}
									?>
										<option value="<?=$value['id']?>" <?=$selected?>><?=$value['hotel_name']?></option>
									<?php	} ?>
									</select></div><div class="clearfix form-group"></div><div class="col-sm-6 controls"><input type="text" name="star_rating[]" placeholder="Star rating" value="<?=$sel_val['star_rating']?>" class="form-control"></div><div class="col-sm-6 controls">
									<select class="select2 form-control htl_no_of_nyt" data-rule-required="true" name="no_night[]"  data-rule-required="true" required> 		<option value="">No of Nights</option>
											
											<?php
									for($dno=0;$dno<=30;$dno++)
									{
										if($dno==1)
										{ 
											$DayNight = ($dno+1).' Days | '.($dno).' Night';
										}else
									  { 
									   $DayNight = ($dno+1).' Days | '.($dno).' Nights';
									  }  
									  if($sel_val['no_of_night']==$dno)
									  {
									   $selected='selected';
									  }else
									  {
									   $selected='';
									  }
									  echo '<option value="'.$dno.'" '.$selected.'>'.$DayNight.'</option>';
									}
									 ?>
											</select>	</div><div class="clearfix form-group"></div></div>
											
											<?php } ?><div></div>
								</div>
								<div class="clearfix"></div>
								</div>	
							</div>
									<?php } ?>
									</div>
									</div>
							
							<?php
							$inclusions_checks = $tour_data['inclusions_checks'];
							// debug($inclusions_checks);exit;
							$inclusions_checks = json_decode($inclusions_checks,1);							
							?>

							<div class='form-group'>
                                <label class='control-label col-sm-3' for='validation_current'>Inclusions
                                </label>
                                <div class='col-sm-4 controls'>
                                    <input type="checkbox" name="inclusions_checks[]" value="Hotel" <?php if(in_array('Hotel',$inclusions_checks)){ echo 'checked';}?> > <!-- <i class="fa fa-hotel"> --> Hotel  <br>  
                                    <input type="checkbox" name="inclusions_checks[]" value="Car" <?php if(in_array('Car',$inclusions_checks)){ echo 'checked';}?> > <!-- <i class="fa fa-car"> --> Car  <br>
                                    <input type="checkbox" name="inclusions_checks[]" value="Meals" <?php if(in_array('Meals',$inclusions_checks)){ echo 'checked';}?> > <!-- <i class="fa fa-spoon"> --> Meals  <br>
                                    <input type="checkbox" name="inclusions_checks[]" value="Sightseeing" <?php if(in_array('Sightseeing',$inclusions_checks)){ echo 'checked';}?> > <!-- <i class="fa fa-binoculars"> --> Sightseeing  <br>     
                                	<input type="checkbox" name="inclusions_checks[]" value="Transfers" <?php if(in_array('Transfers',$inclusions_checks)){ echo 'checked';}?>> <!-- <i class="fa fa-binoculars"> --> Transfers  <br>   
									<input type="checkbox" name="inclusions_checks[]" value="Flight" <?=(in_array('Flight',$inclusions_checks))? 'checked="checked"': '';?>>
									<!-- <i class="fa fa-binoculars"> -->Flight <br>
									<input type="checkbox" name="inclusions_checks[]" value="Train" <?=(in_array('Train',$inclusions_checks))? 'checked="checked"': '';?>>
									<!-- <i class="fa fa-binoculars"> -->Train <br>
									<input type="checkbox" name="inclusions_checks[]" value="Cruise" <?=(in_array('Cruise',$inclusions_checks))? 'checked="checked"': '';?>>
									<!-- <i class="fa fa-binoculars"> -->Cruise <br>
									<input type="checkbox" name="inclusions_checks[]" value="Insurance" <?=(in_array('Insurance',$inclusions_checks))? 'checked="checked"': '';?>>
									<!-- <i class="fa fa-binoculars"> -->Travel Insurance <br>									
                                </div>
                            </div>	
							
							<!--<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Choose Optional Tour <span style = "color:red">*</span>
								</label>
								<div class='col-sm-8 col-md-8 controls nopad'>
								
								<select class='select2 form-control' data-rule-required='true' name='optional_tour[]' id="optional_tour" multiple data-rule-required='true' >
                                    <?php
									 
									  $tours_sel_opt_tours = $tours_sel_opt_tours;
									 
									foreach($optional_tours as $tours_country_key => $tours_country_value)
									{

										if(in_array($tours_country_value['id'],$tours_sel_opt_tours)){$selected='selected';
										//echo $selected;exit;
									}
										else{$selected='';}
										echo '<option value="'.$tours_country_value['id'].'" '.$selected.'>'.$tours_country_value['tour_name'].' </option>';
									}
									?>                        
								</select>
								</div>
									
							</div>	-->
							
							<div class='form-group '>
								<label class='control-label col-sm-3' for='validation_current'>Choose Optional Tour <span style = "color:red">*</span>
								</label>
								<div class="col-md-8 optional_tour_block nopad">
								<?php foreach($optional_tours as $sel_h_key => $sel_h_val){ ?>
									
									<div class='controls_new'>
										<div class='controls ' style='padding: 0px; margin: 13px;'>
											<div class='mode'>
												<label class='control-label col-sm-12' style='text-align: left;margin-bottom: 6px;'><?=$sel_h_val[0]['CityName']?> Optional Tours </label>
												<div class='col-sm-12 col-md-12 controls'>
													<select class='select2 form-control' data-rule-required='true' name='optional_tour[]' id='optional_tour' multiple data-rule-required='true' >
													<?php foreach($sel_h_val as $sel_op_key => $sel_op_val){ 
														if(in_array($sel_op_val['id'],$tours_sel_opt_tours)){$selected='selected';
															}
															else{$selected='';}
													
													?>
														<option value="<?=$sel_op_val['id']?>" <?=$selected?>><?=$sel_op_val['tour_name']?></option>
													<?php } ?>
													</select>
												</div>
											</div>
										</div>
									</div>
									<div class="clearfix"></div><br/>
								<?php } ?>
								</div>	

							</div>	
                            <!--<div class='form-group'>
                                <label class='control-label col-sm-3' for='validation_current'>Gallery
                                </label>
                                <div class='col-sm-9 controls'>
                                    <?php
                                    $gallery = $tour_data['gallery'];
                                    $explode = explode(',', $gallery);
                                    $galleryImages = '';
                                    for ($g = 0; $explode[$g] != ''; $g++) {
                                        if ($g % 5 == 0) {
                                            $galleryImages .= '<tr id="">';
                                        }
                                      //  debug($tour_data); exit();
                                        //$path = $this->template->domain_image_upload_path().$explode[$g];
                                        $galleryImages .= '<td id = "gal_img'.$g.'"> <a href = "javascript:void(0)" onclick="deleteimage('.$tour_data['id'].',\''.$explode[$g].'\',\'gal_img'.$g.'\')">X</a> &nbsp;&nbsp; <img style="width:90%;" class="gal_img" src="'.$this->template->domain_images( $explode[$g] )  . '"></td> ';
                                        //echo '<img src="'.$path.'" style="width:50%"> <input type="checkbox" name="gallery_previous[]" value="'.$explode[$g].'" checked><br><br>';
                                        if (($g + 1) % 5 == 0) {
                                            $galleryImages .= '</tr>';
                                        }
                                    }
                                    echo "<table style='width:100%;'>" . $galleryImages . "</table>";
                                    ?>										
                                </div>
                            </div>
                           

                            <div class='form-group'>
                                <label class='control-label col-sm-3' for='validation_current'>Upload Gallery
                                </label>
                                <div class='col-sm-4 controls'>
                                    <input type="file" name="gallery[]" id="gallery" multiple class='form-control'>	
                                    <?=img_size_msg(200,300)?>								
                                </div>
                            </div>	
                            <div class='form-group'>
                              <label class='control-label col-sm-3' for='validation_current'>Image Descriptions
                                </label>
                               <div class='col-sm-9 controls'>
                               <?php
                               $image_description  = $tour_data['image_description']; 
                               //echo $image_description;
                               $image_description = json_decode($image_description,1);
                               $image_description = implode("#", $image_description);
                               ?>
                            <textarea placeholder="Description" class="form-control" name="image_description" id="image_description" /><?php echo $image_description; ?></textarea> 
                            <strong>Note *:</strong>
                       		<span style="color:#999;">Please update each image description followed by "#"</span>
                            </div>
                            </div>
                            </div>
							-->

							<div class='' style='margin-bottom: 0'>
								<div class='row'>
									<div class='col-sm-9 col-sm-offset-3'>		
									    <input type="hidden" name="tour_id" value="<?=$tour_id?>">						
										<button class='btn btn-primary' type="submit">Update</button>
										<?php //debug($tour_data); 
										if($tour_data['package_status']=='CREATED'){
										?>
										<a class='btn btn-primary' href="<?php echo base_url(); ?>index.php/tours/draft_list">Go Back to Draft List</a>
										<?php
										}else if($tour_data['package_status']=='ITINERARY_ADDED'){
										?>
										<a class='btn btn-primary' href="<?php echo base_url(); ?>index.php/tours/tour_list">Go Back to Holiday List</a>
										<?php
										}else if($tour_data['package_status']=='VERIFICATION'){
										?>
										<a class='btn btn-primary' href="<?php echo base_url(); ?>index.php/tours/verify_tour_list">Go Back to verify holiday List</a>
										<?php
										}else if($tour_data['package_status']=='VERIFIED'){
										?>
										<a class='btn btn-primary' href="<?php echo base_url(); ?>index.php/tours/published_tour_list">Go Back to Published Holiday List</a>
										<?php
										}
										
										
										?>
									</div>
								</div>
							</div>						    						
						    <hr>
						    				
						</div>							
					</div>					
				</div>
			</form>
		</div>
		<!-- PANEL BODY END -->
	</div>
	<!-- PANEL WRAP END -->
</div>
<script src="<?php echo $GLOBALS['CI']->template->template_js_dir('multidate_init.js'); ?>"></script> 
<script type="text/javascript">
 function deleteimage(image_id,image_name,tag_id)
    {
      var image_id = image_id;
      var image_name = image_name;
  // alert(image_name);
  var answer = confirm ("Are you sure you want to delete from this post?");
  if (answer)
  {
    $.ajax({
      type: "POST",
      url: "<?php echo site_url('tours/tours_delete_image_id');?>",
      data: {"image_id" : image_id , "image_name" : image_name},
      success: function (response) {
        if (response == 1) {
                    // $(".imagelocation"+image_id).remove(".imagelocation"+image_id);
                    document.getElementById(tag_id).remove();
                    // console.log($('#'+image_name));
                  };
                  
                }
              });
  }
}


     $(document).ready(function()
     {


     	//var demo1 = $('select[name="tour_type[]"]').bootstrapDualListbox();
     	var demo2 = $('select[name="theme[]"]').bootstrapDualListbox();
     	
     	//var tours_country = $('select[name="tours_country[]"]').bootstrapDualListbox();
     	var demo1 = $('select[name="tour_type[]"]').bootstrapDualListbox({
					  nonSelectedListLabel: 'Non-selected',
					  selectedListLabel: 'Selected',
					  preserveSelectionOnMove: false,
					  moveOnSelect: true
					});
     	var tours_continent = $('select[name="tours_continent[]"]').bootstrapDualListbox({
					  nonSelectedListLabel: 'Non-selected',
					  selectedListLabel: 'Selected',
					  preserveSelectionOnMove: false,
					  moveOnSelect: true
					});
		var tours_country = $('select[name="tours_country[]"]').bootstrapDualListbox({
					  nonSelectedListLabel: 'Non-selected',
					  selectedListLabel: 'Selected',
					  preserveSelectionOnMove: false,
					  moveOnSelect: true
					});
     	var tours_city= $('select[name="tours_city[]"]').bootstrapDualListbox({
						nonSelectedListLabel: 'Non-selected',
						selectedListLabel: 'Selected',
						preserveSelectionOnMove: false,
						moveOnSelect: true,
						sortByInputOrder: 'true'
					});
		var optional_tour = $('select[name="optional_tour[]"]').bootstrapDualListbox({
					nonSelectedListLabel: 'Non-selected',
					  selectedListLabel: 'Selected',
					  preserveSelectionOnMove: false,
					  moveOnSelect: true
		});
		var concerned_supplier = $('select[name="concerned_supplier[]"]').bootstrapDualListbox({
					nonSelectedListLabel: 'Non-selected',
					  selectedListLabel: 'Selected',
					  preserveSelectionOnMove: false,
					  moveOnSelect: true
		});
		var supplier = $('select[name="supplier[]"]').bootstrapDualListbox({
						nonSelectedListLabel: 'Non-selected',
						selectedListLabel: 'Selected',
						preserveSelectionOnMove: false,
						moveOnSelect: true
		});
        $('#tours_continent').on('click', function() { 
        $tours_continent = $('#tours_continent').val();
        	$.post('<?=base_url();?>tours/ajax_tours_continent',{'tours_continent':$tours_continent},function(data){
          	  //alert(data);
              $('#tours_country').html(data);
              $('#tours_city').html('');
              tours_country.bootstrapDualListbox('refresh', true);
         	});
        });  

        $('#tours_country').on('change', function() { 
        	
        	var old_data = $('#tours_city').html();
         	var tours_countries = $('#tours_country').val();
         	//alert(tours_countries.length);
         	if(tours_countries.length > 0 ){
				var tours_country_list = tours_countries;
         	}else{
	         	var tours_country_list = tours_countries.split(',');
	        }

	        // please wait
	        $('#tours_city').html('<option>Please wait....</option>');
	        var res = '';
	        //alert(tours_country_list);
         	$.each(tours_country_list, function(index, item) {
         		
			    // do something with `item` (or `this` is also `item` if you like)
		        $.post('<?=base_url();?>tours/ajax_tours_country',{'tours_country':item},function(data)
		        {
		        	
		        	if(index>0){//alert(index);
			            $('#tours_city').append(data);
			            //$('#tours_city').bootstrapDualListbox('refresh', true);
			        }else{			      //alert(index);  	
			            $('#tours_city').html(data);
			            //$('#tours_city').bootstrapDualListbox('refresh', true);
			        }
			        if(tours_countries.length==parseInt(index+1)){
			        	setBoot('#tours_city');
			    	}
		        	
		        });
				$.post('<?=base_url();?>tours/ajax_tours_supplier',{'tours_country':item},function(data)
		        {
		        	if(index>0){
			            $('#supplier').append(data);
			           /// $('#supplier').bootstrapDualListbox('refresh', true);
			        }else{			        	
			            $('#supplier').html(data);
			           // $('#supplier').bootstrapDualListbox('refresh', true);
			        }
					$('#concerned_supplier').html('');
					if(tours_countries.length==parseInt(index+1)){
			        	setBoot(supplier);
			    	}
		        });
	        });

	      // alert(res);
       //  	if((res !== '') && (typeof(res) !== 'undefined')){
	      //       $('#tours_city').html(res);
	      //   }else{			        	
	      //       $('#tours_city').html(old_data);
	      //   }
		});
		$(document).on('mouseover',"select",function(){
		$(this).parent().find(".filter").blur();
		});
		$('#tours_city').on('change', function() { 
           
         	var tours_city = $('#tours_city').val();
         
         	if(tours_city==null){
         		//$('#tours_city').html('');
         	}
			//console.log(tours_city);
         	if(tours_city.length > 0 ){
				var tours_country_list = tours_city;
         	}else{
	         	var tours_country_list = tours_city.split(',');
	         }

         	$.each(tours_country_list, function(index, item) {

			    // do something with `item` (or `this` is also `item` if you like)
				$.post('<?=base_url();?>tours/ajax_tours_hotels',{'tours_city':item},function(data)
		        {
		        	if(index>0){
			            $('.hotels_block').append(data);
			            $('.hotels_block').bootstrapDualListbox('refresh', true);
			        }else{			        	
			            $('.hotels_block').html(data);
			            $('.hotels_block').bootstrapDualListbox('refresh', true);
			        }
		        });
				$.post('<?=base_url();?>tours/ajax_optional_tours',{'tours_city':item},function(data)
		        {
		        	if(index>0){
			            $('.optional_tour_block').append(data);
			           var optional_tour = $('select[name="optional_tour[]"]').bootstrapDualListbox({});
			        }else{			        	
			            $('.optional_tour_block').html(data);
			            var optional_tour = $('select[name="optional_tour[]"]').bootstrapDualListbox({});
			        }
		        });
	        });
		});
     });
     function setBoot(val){
     	$(val).bootstrapDualListbox('refresh', true);
     }   
</script>
<script type="text/javascript">
     $(document).ready(function()
     {          
          $('#tours_continent').on('click', function() { 
          $tours_continent = $('#tours_continent').val();
          $.post('<?=base_url();?>tours/ajax_tours_continent',{'tours_continent':$tours_continent},function(data)
          {       	  
              $('#tours_country').html(data);
              $('#tours_city').html('');
          });
          });  
               
         
		$('#second').click(function() {
		   $("#second").find(':selected').remove();
		   getSelectMultiple();
		}); 

		function getSelectMultiple(){
			$("#second option").prop('selected', true);
		}         
     });

     $(document).ready(function(){
		$('#valid_frm').change(function(){
			var start_date= $('#valid_frm').val();
				
			$('#valid_to').attr('min', start_date);
				
		});
		$('#package_type').change(function(){
			var type_id=$(this).val();
		
			if(type_id=='group'){
				
				$('.multi_date').removeClass('hidden');
				$('.valid').addClass('hidden');
				$('.valid_frm,.valid_to').prop('required',false);
				$('#multi_date').prop('required',true);
			}else{
				$('.valid').removeClass('hidden');
				$('.valid_frm,.valid_to').prop('required',true);
				$('#multi_date').prop('required',false);
				$('.multi_date').addClass('hidden');

			}
		});
		
		$('#tours_continent').on('change', function() { 
            
        tours_continent = $('#tours_continent').val();
		
		if(tours_continent==null){
         	$('#tours_country').html('');
        }
		
		if(tours_continent.length > 0 ){
			var tours_continent_list = tours_continent;
		}else{
			var tours_continent_list = tours_continent.split(',');
		}
		$.each(tours_continent_list, function(index, item) {

			// do something with `item` (or `this` is also `item` if you like)

			$.post('<?=base_url();?>tours/ajax_tours_continent',{'tours_continent':item},function(data)
			{	
				if(index>0){
					$('#tours_country').append(data);
					$('#tours_country').bootstrapDualListbox('refresh', true);
				}else{			        	
					$('#tours_country').html(data);
					$('#tours_country').bootstrapDualListbox('refresh', true);
				}
			});
		});
        
        });  
		$(document).on('click','.add_hotel',function(){
			var generate_text=$(this).parents('.controls').find('.this_city_hotel').html();	
			$(this).parents('.add_city_hotel_here').append('<div class="this_city_hotel"><a class="remove_hotel btn pull-right">Remove <i class="fa fa-minus" aria-hidden="true"></i></a><br/>'+generate_text+'</div>');	
		});
		$(document).on('click','.remove_hotel',function(){
			$(this).parents('.this_city_hotel').remove();
		});
		$(document).on('click','.add_date',function(){
			var generate_date_text='<div class="multi_frm_to_date form-group"><label class="control-label col-sm-3">Valid From <span style = "color:red">*</span></label><div class="col-sm-3 controls"><input type="date" placeholder="Enter Date" name="valid_frm[]" class="form-control add_pckg_elements valid_frm"></div><label class="control-label col-sm-1">Valid To <span style = "color:red">*</span></label><div class="col-sm-3 controls"><input type="date" placeholder="Enter Date" name="valid_to[]" class="form-control add_pckg_elements valid_to"></div><div class="col-sm-2 controls"><button type="button" class="btn btn-primary remove_date">Remove</button></div></div>';
			
			$('.multi_date_block').append(generate_date_text);
			
		});
		$(document).on('click','.remove_date',function(){
			
			$(this).parents('.multi_frm_to_date').remove();
			
		});
		$(document).on('change','.htl_no_of_nyt',function(){
			var duration=parseInt($('#duration').val());
			//alert(duration);
			var tot_nyt=0;
			$.each($('.htl_no_of_nyt'), function() {
				var sel_nyt_val=parseInt($(this).val());
				//console.log(sel_nyt_val);
				if(isNaN(sel_nyt_val)){
					//console.log(sel_nyt_val);
				}else{
					tot_nyt+=sel_nyt_val;
					
				}
			});
			if(tot_nyt>duration)
			{
				 $msg = duration+' Nights / '+(duration+1)+' Days';
				 alert('Sorry! This Holiday Package is designed for '+$msg+'. You are exceeding the limit.');
				 $('.htl_no_of_nyt').val('');
				 return false;
			}     
		});
		function readURL(input) {
			if (input.files && input.files[0]) {
				var reader = new FileReader();
			
				reader.onload = function(e) {
					$('.ini_banner_img').removeClass('hide');
					$('.banner_imgs').attr('src', e.target.result);
				}
			
				reader.readAsDataURL(input.files[0]); // convert to base64 string
			}
		}

		$("#banner_image").change(function() {
		  readURL(this);
		});
		$(document).on('change','#supplier', function() { 
			var supplier_id = $(this).val();
			var supplier_id = $('#supplier').val();
			console.log(supplier_id);
         	if(supplier_id==null){
         		$('#tours_city').html('');
         	}
         	if(supplier_id.length > 0 ){
				var supplier_id_list = supplier_id;
         	}else{
	         	var supplier_id_list = supplier_id.split(',');
	         }

         	$.each(supplier_id_list, function(index, item) {
				$.post('<?=base_url();?>tours/ajax_concerned_persons',{'supplier_id':item},function(data)
				{			        	
					
					
					if(index>0){
			            $('#concerned_supplier').append(data);
			            $('#concerned_supplier').bootstrapDualListbox('refresh', true);
			        }else{			        	
			            $('#concerned_supplier').html(data);
			            $('#concerned_supplier').bootstrapDualListbox('refresh', true);
			        }
				});
			});
		});
     });

     
</script>

