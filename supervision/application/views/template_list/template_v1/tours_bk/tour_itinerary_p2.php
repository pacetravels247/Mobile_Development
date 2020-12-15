<!DOCTYPE html>
<html>
<head>
        <div id="Package" class="bodyContent col-md-12">
            <div class="panel panel-default">
                <!-- PANEL WRAP START -->
                <div class="panel-heading">
                    <!-- PANEL HEAD START -->
                    <div class="panel-title">
                        <ul class="nav nav-tabs nav-justified" role="tablist" id="myTab">
                            <!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE START-->
                            <li role="presentation" class="active" id="add_package_li">
                                <a href="#add_package" aria-controls="home" role="tab" data-toggle="tab"> Visited City List : [ <?php echo 'Package Name : ' . string_replace_encode($tour_data['package_name'])  ; ?> ] </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- PANEL HEAD START -->
                <div class="panel-body">
				
                    <!-- PANEL BODY START -->
                    <form action="<?php echo base_url(); ?>index.php/tours/tour_itinerary_p2_save" method="post" enctype="multipart/form-data" id="form form-horizontal validate-form" class='form form-horizontal validate-form'>
                        <div class="tab-content">
                            <!-- Add Package Starts -->
                            <div role="tabpanel" class="tab-pane active" id="add_package">
                                <div class="col-md-12">
                                    <div class='form-group'>
                                        <label class='control-label col-sm-3' for='validation_current'>Package Duration
                                        </label>
                                        <div class='col-sm-8 controls'>
                                            <input type="text" value="<?= $tour_data['duration']+1 . ' Days / ' . ($tour_data['duration'] ) . (($tour_data['duration']==1)?' Night': 'Nights'); ?>" class='form-control' disabled>
                                        </div>
                                    </div>
									<input type="hidden" value="<?php if(empty($tours_itinerary_dw)){echo '0';}else{echo '1';}?>" name="is_edit">
                                    <?php 
                                     //debug($tours_itinerary_dw);exit;
                                    foreach ($tour_visited_cities as $tvcKey => $tvcValue) { 
                                        $city_in_record = $tvcValue['city'];
                                        $city_in_record = json_decode($city_in_record,1);
										//debug($city_in_record);
                                       // foreach($city_in_record as $k => $v)
                                       // {
                                         $city_in_record_str = $tours_city_name[$city_in_record];                            
                                      //  }
                                        ?>
                                        <div class='form-group'>
                                            <label class='control-label col-sm-3' for='validation_current'>City Visited
                                            </label>
                                            <div class='col-sm-8 controls'>
                                                <input type="text" name="city" id="city" value="<?= $city_in_record_str . ' [ ' . $tvcValue['no_of_nights'] . (($tvcValue['no_of_nights']==1)?' Night': 'Nights').']'; ?>" placeholder="Enter City" class='form-control' disabled>
                                            </div>
                                        </div>
                                        <input type="hidden" name="id[]" value="<?= $tvcValue['id'] ?>">
                                        <?php } ?>
                                        <div id="itinerary_list">
                                            <?php
                                            $Dayno = 1;
                                             //debug($tour_visited_cities);exit;
                                            $totalvisint_tours=count($tour_visited_cities)-1;
                                            $Breakfast = '';
                                            $Lunch = '';
                                            $Dinner = '';
                                            $Beverages = '';
                                            $Snacks = '';
											
                                            foreach ($tour_visited_cities as $tvcKey => $tvcValue) {
                                                $itinerary = $tvcValue['itinerary'];
                                                // debug($itinerary);exit;
                                                $itinerary = json_decode($itinerary, true);
                                                // echo "dfsfdgfd";
                                                //debug($tours_itinerary_dw[$Dayno-1]['accomodation']);
                                                // debug($tvcValue);exit;
                                                if($tvcValue['no_of_nights'] == 0) $id = $tvcValue['id'];
                                                for ($i = 0; $i < $tvcValue['no_of_nights']; $i++) {
                                                    $id = $tvcValue['id'];
                                                    // debug($itinerary);exit;
													$accomodation = json_decode($tours_itinerary_dw[$Dayno-1]['accomodation'],1);
													//   debug($accomodation);   
                                                    if(valid_array($accomodation)){
                                                        // $itinerary = json_decode($itinerary, 1);

                                                       // $accomodation = $itinerary[$i]['accomodation'];
													   
                                                        if (in_array('Breakfast', $accomodation)) {
                                                            $Breakfast = 'checked';
                                                        } else {
                                                            $Breakfast = '';
                                                        }
                                                        if (in_array('Lunch', $accomodation)) {
                                                            $Lunch = 'checked';
                                                        } else {
                                                            $Lunch = '';
                                                        }
                                                        if (in_array('Dinner', $accomodation)) {
                                                            $Dinner = 'checked';
                                                        } else {
                                                            $Dinner = '';
                                                        }
                                                        if (in_array('Snacks', $accomodation)) {
                                                            $Snacks = 'checked';
                                                        } else {
                                                            $Snacks = '';
                                                        }
                                                        if (in_array('Beverages', $accomodation)) {
                                                            $Beverages = 'checked';
                                                        } else {
                                                            $Beverages = '';
                                                        }
                                                    }
                                                    $city_in_record = $tvcValue['city'];
                                                    $city_in_record = json_decode($city_in_record,1);
                                                   // foreach($city_in_record as $k => $v)
                                                    //{ 
                                                       // if($k==0){ 
                                                            $city_in_record_str = $tours_city_name[$city_in_record];
                                                       // } else
                                                       // { 
                                                       //     $city_in_record_str = $city_in_record_str.', '.$tours_city_name[$v];
                                                      //  }
                                                   // }
                                                    ?>
                                                    <hr>
                                                    <div class = "form-group">
                                                     <!--    <label class = "control-label col-sm-3 " for="validation_current">Day <?= $Dayno ?>  in <?= string_replace_encode($city_in_record_str) ?></label>-->
														<label class = "control-label col-sm-3 " for="validation_current">Day <?= $Dayno ?></label>
														<div class="col-sm-8 controls">
															<input type="text" name="visited_city_name[<?= $id ?>][]" placeholder="Enter Heading" data-rule-required="true" class="form-control" required value="<?= string_replace_encode($tours_itinerary_dw[$Dayno-1]['visited_city_name']) ?>">
														</div>
                                                    </div>
                                                 <!--   <div class="form-group">
                                                        <label class="control-label col-sm-3" for="validation_current">Day Program Title &nbsp; <span style = "color:red">*</span> </label>
                                                        <div class="col-sm-8 controls">
                                                            <input type="text" name="program_title[<?= $id ?>][]" placeholder="Enter Program Title" data-rule-required="true" class="form-control" required value="<?= string_replace_encode($itinerary[$i]['program_title']) ?>">
                                                        </div>
                                                    </div>-->
                                                   
													<div class="form-group">
                                                        <label class="control-label col-sm-3" for="validation_current">Itinerary
                                                        </label>
                                                        <div class="col-sm-8 controls">
                                                            <textarea name="itinerary_des[<?= $id?>][]" data-rule-required="true" class="form-control itinerary_des" data-rule-required="true" cols="70" rows="5" placeholder="Description"><?=string_replace_encode($tours_itinerary_dw[$Dayno-1]['itinerary_des']) ?></textarea>
                                                        </div>
                                                    </div>
													<div class="images_div">
														<div class="gallery_div col-sm-offset-3" id="gallery_div_<?=$Dayno?>">
															
															<?php 
															//debug($tours_itinerary_dw);
																$image=explode(',',$tours_itinerary_dw[$Dayno-1]['banner_image']);
																foreach($image as $im){
																	if($im!=''){
																		echo '<img src="'.$this->template->domain_images($im).'"style="width:50px;height:50px;margin:5px 0 10px 10px;"><a data-ite_id="'.$tours_itinerary_dw[$Dayno-1]['id'].'" data-ite_img_nam="'.$im.'" class="delete_image"><i class="fas fa-trash"></i></a>';
																	}
																	
																}
															
															?>
															<input type="hidden" value="<?=$tours_itinerary_dw[$Dayno-1]['banner_image']?>," name="old_images[<?= $Dayno?>][]">
														</div>
														<div class='form-group'>
															<label class='control-label col-sm-3' for='validation_current'>Upload Gallery
															</label>
															<div class='col-sm-4 controls'>
																<input type="file" name="gallery[<?= $Dayno?>][]" multiple data-rule-required='true' class='gallery form-control' >				<?=img_size_msg(610,370)?>						
															</div>
														</div>
													</div>
                                                    <!--<div class="form-group">
                                                        <label class="control-label col-sm-3" for="validation_current">Hotel Name </label>
                                                        <div class="col-sm-4 controls">
                                                            <input type="text" name="hotel_name[<?=$id ?>][]"  placeholder="Enter hotel name" class="form-control" value="<?=string_replace_encode($itinerary[$i]['hotel_name']) ?>">                  
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3" for="validation_current">Star Rating </label>
                                                        <div class="col-sm-4 controls">
                                                            <select name="rating[<?=$id ?>][]" class="form-control">
                                                                <option value="0">Select star rating</option>
                                                                <?php
                                                                for ($s = 1; $s <= 5; $s++) {
                                                                    $rating = $itinerary[$i]['rating'];
                                                                    if ($s == $rating) {
                                                                        $selected = 'selected';
                                                                    } else {
                                                                        $selected = '';
                                                                    }
                                                                    echo '<option value="' . $s . '" ' . $selected . '>' . $s . ' Star</option>';
                                                                }
                                                                ?>
                                                            </select>               
                                                        </div>
                                                    </div>-->
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3" for="validation_current">Meals </label>
                                                        <div class="col-sm-4 controls">
                                                            <input type="checkbox" name="accomodation[<?= $id ?>][<?= ($i) ?>][]" value="Breakfast" <?= $Breakfast ?>> Breakfast <br>                  
                                                            <input type="checkbox" name="accomodation[<?= $id ?>][<?= ($i) ?>][]" value="Lunch" <?= $Lunch ?>> Lunch <br>              
                                                            <input type="checkbox" name="accomodation[<?= $id ?>][<?= ($i) ?>][]" value="Dinner" <?= $Dinner ?>> Dinner <br>
                                                            <input type="checkbox" name="accomodation[<?= $id ?>][<?= ($i) ?>][]" value="Snacks" <?= $Snacks ?>> Snacks <br>
                                                            <input type="checkbox" name="accomodation[<?= $id ?>][<?= ($i) ?>][]" value="Beverages" <?= $Beverages ?>> Beverages <br>               
                                                        </div>
                                                    </div>
                                                    <?php
                                                    $Dayno++;
                                                }
												//debug($tours_itinerary_dw[$Dayno-1]);
                                                if($tvcKey==$totalvisint_tours) {
                                                   $accomodation = json_decode($tours_itinerary_dw[$Dayno-1]['accomodation'],1);
                                                    if(valid_array($accomodation)){
                                                         if (in_array('Breakfast', $accomodation)) {
                                                            $Breakfast = 'checked';
                                                        } else {
                                                            $Breakfast = '';
                                                        }
                                                        if (in_array('Lunch', $accomodation)) {
                                                            $Lunch = 'checked';
                                                        } else {
                                                            $Lunch = '';
                                                        }
                                                        if (in_array('Dinner', $accomodation)) {
                                                            $Dinner = 'checked';
                                                        } else {
                                                            $Dinner = '';
                                                        }
                                                        if (in_array('Snacks', $accomodation)) {
                                                            $Snacks = 'checked';
                                                        } else {
                                                            $Snacks = '';
                                                        }
                                                        if (in_array('Beverages', $accomodation)) {
                                                            $Beverages = 'checked';
                                                        } else {
                                                            $Beverages = '';
                                                        }
                                                    }
                                                   
                                                    ?>
                                                    <hr>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3" for="validation_current">Day <?= $Dayno ?> </label>
														<div class="col-sm-8 controls">
															<input type="text" name="visited_city_name[<?= $id ?>][]" placeholder="Enter Heading" data-rule-required="true" class="form-control" required value="<?= string_replace_encode($tours_itinerary_dw[$Dayno-1]['visited_city_name']) ?>">
														</div>
                                                    </div>
                                                   <!-- <div class="form-group">
                                                        <label class="control-label col-sm-3" for="validation_current">Day Program Title &nbsp; <span style = "color:red">*</span> </label>
                                                        <div class="col-sm-8 controls">
                                                            <input type="text" name="program_title[<?= $id?>][]" placeholder="Enter Program Title" data-rule-required="true" class="form-control" required value="<?= string_replace_encode($itinerary[$i]['program_title']) ?>">
                                                        </div>
                                                    </div>-->
                                                    
													<div class="form-group">
                                                        <label class="control-label col-sm-3" for="validation_current">Itinerary
                                                        </label>
                                                        <div class="col-sm-8 controls">
                                                            <textarea name="itinerary_des[<?= $id?>][]" data-rule-required="true" class="form-control itinerary_des" data-rule-required="true " cols="70" rows="5" placeholder="Description"><?=string_replace_encode($tours_itinerary_dw[$Dayno-1]['itinerary_des']) ?></textarea>
                                                        </div>
                                                    </div>
													<div class="images_div">
														
														<div class="gallery_div col-sm-offset-3" id="gallery_div_<?=$Dayno?>">
															<?php 
															//debug($tours_itinerary_dw[$Dayno-1]['banner_image']);
																$image=explode(',',$tours_itinerary_dw[$Dayno-1]['banner_image']);
																foreach($image as $im){
																	if($im!=''){
																		echo '<img src="'.$this->template->domain_images($im).'"style="width:50px;height:50px;margin:5px 0 10px 10px;"><a data-ite_id="'.$tours_itinerary_dw[$Dayno-1]['id'].'" data-ite_img_nam="'.$im.'" class="delete_image"><i class="fas fa-trash"></i></a>';
																	}
																	 
																}
															?>
															<input type="hidden" value="<?=$tours_itinerary_dw[$Dayno-1]['banner_image']?>" name="old_images[<?= $Dayno?>][]">
														</div>
														<div class='form-group'>
															<label class='control-label col-sm-3' for='validation_current'>Upload Gallery
															</label>
															<div class='col-sm-4 controls'>
																<input type="file" name="gallery[<?= $Dayno?>][]" id="gallery" multiple data-rule-required='true' class='form-control' >				<?=img_size_msg(610,370)?>						
															</div>
														</div>
													</div>
                                                   <!-- <div class="form-group">
                                                        <label class="control-label col-sm-3" for="validation_current">Hotel Name </label>
                                                        <div class="col-sm-4 controls">
                                                            <input type="text" name="hotel_name[<?= $id?>][]" placeholder="Enter hotel name" class="form-control" value="<?= string_replace_encode($itinerary[$i]['hotel_name']) ?>">
                                                        </div>
                                                    </div> 
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3" for="validation_current">Star Rating </label>
                                                        <div class="col-sm-4 controls">
                                                            <select name="rating[<?= $id ?>][]" class="form-control">
                                                                <option value="0">Select star rating</option>
                                                                <?php
                                                                for ($s = 1; $s <= 5; $s++) {
                                                                    $rating = $itinerary[$i]['rating'];
                                                                    if ($s == $rating) {
                                                                        $selected = 'selected';
                                                                    } else {
                                                                        $selected = '';
                                                                    }
                                                                    echo '<option value="' . $s . '" ' . $selected . '>' . $s . ' Star</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>-->
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-3" for="validation_current">Meals </label>
                                                        <div class="col-sm-4 controls">
                                                            <input type="checkbox" name="accomodation[<?= $id?>][<?= ($i)?>][]" value="Breakfast" <?= $Breakfast?>> Breakfast <br>
                                                            <input type="checkbox" name="accomodation[<?= $id?>][<?= ($i)?>][]" value="Lunch" <?= $Lunch?>> Lunch <br>
                                                            <input type="checkbox" name="accomodation[<?= $id?>][<?= ($i)?>][]" value="Dinner" <?= $Dinner?>> Dinner <br>
                                                            <input type="checkbox" name="accomodation[<?= $id ?>][<?= ($i) ?>][]" value="Snacks" <?= $Snacks ?>> Snacks <br>
                                                            <input type="checkbox" name="accomodation[<?= $id ?>][<?= ($i) ?>][]" value="Beverages" <?= $Beverages ?>> Beverages <br>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
												
                                            }
                                            ?>
                                            </div>
                                            <hr>
											<div class="form-group">
												<label class="control-label col-sm-3" for="validation_current">Program Description
												</label>
												<div class="col-sm-8 controls">
													<textarea name="program_des" data-rule-required="true" class="form-control program_des" data-rule-required="true " cols="70" rows="5" placeholder="Description"><?= string_replace_encode($tour_data['package_description']) ?></textarea>
												</div>
											</div>
                                            <div class='' style='margin-bottom: 0'>
                                                <div class='row'>
                                                    <div class='col-sm-9 col-sm-offset-3'>
                                                        <input type="hidden" name="tour_id" value="<?= $tour_id ?>">
                                                        <button class='btn btn-primary' type='submit'>Save
                                                        </button>
                                                        <?php 
                                                        if($this->session->userdata('edit_itinary')){
                                                            ?>
                                                            <a class="btn btn-primary" href="<?=base_url()?>tours/tour_list">Tour List </a>
                                                            <?php 
                                                        }
                                                        ?>
                                                        <button class='btn btn-primary'><a href="<?php echo base_url(); ?>index.php/tours/tour_pricing_p2/<?= $tour_id ?>" style="color:white;">Next</a></button>
                                                    </div>
                                                </div>
                                            </div>
                                </div>
								
                            </div>
                        </div>
                    </form>
                </div>
                <!-- PANEL BODY END -->
                <!-- PANEL WRAP END -->
            </div>
        </div>
            <!--script type="text/javascript" src="<?=get_domain()?>extras/system/template_list/template_v1/javascript/js/tiny_mce/tiny_mce.js"></script>
            <script type="text/javascript" src="<?=get_domain()?>extras/system/template_list/template_v1/javascript/js/tiny_mce/tiny_mce_call.js">
            </script>-->
            <!--
<script type="text/javascript" src="/chariot/extras/system/template_list/template_v1/javascript/js/nicEdit-latest.js"></script> 
<script type="text/javascript">
bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
</script>-->
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width">
            <title>ctrlq
            </title>
<script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
<script>
$( document ).ready(function (){
	CKEDITOR.replaceAll( 'program_des' );
	CKEDITOR.replaceAll( 'itinerary_des' );
	
});
$(function() {
    // Multiple images preview in browser
    var imagesPreview = function(input, placeToInsertImagePreview) {
		
        if (input.files) {
            var filesAmount = input.files.length;

            for (i = 0; i < filesAmount; i++) {
                var reader = new FileReader();

                reader.onload = function(event) {
                    $($.parseHTML('<img style="width:50px;height:50px;">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
                }

                reader.readAsDataURL(input.files[i]);
            }
        }

    };

    $('.gallery').on('change', function() {
		var image_div=$(this).parents('.images_div').find('.gallery_div').attr('id');
      //  alert(image_div);
	  $('#'+image_div).html('');
		imagesPreview(this,'#'+image_div); 
    });
	
	$(document).on('click','.delete_image',function(){
		var img=$(this).data('ite_img_nam');
		var id=$(this).data('ite_id');
		alert(img);
		$.ajax({
			url: '<?=base_url();?>tours/unlink_itinerary_image/' + id + '/' + img,
			success: function (data, textStatus, jqXHR) {                            
				//alert(data);   
				 // $("#img_" + id).remove();
					window.location.reload();
			}
		 });
	});
});
</script>	
</head>

<body>


</body>
</html>
