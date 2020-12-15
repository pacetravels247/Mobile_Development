<?php

error_reporting(0);

//debug($tours_itinerary); //exit;

//$itinerary = json_decode($tours_itinerary['itinerary'], 1); //debug($itinerary); exit;

$tour_visited_city_id = json_decode($tours_itinerary['tour_visited_city_id'], 1); //debug($tour_visited_city_id); //exit;

//$sightseeing = json_decode($tours_itinerary['sightseeing'], 1); //debug($sightseeing); exit;

$no_of_nights = json_decode($tours_itinerary['no_of_nights'], 1); //debug($no_of_nights); exit;

//$includes_city_tours = json_decode($tours_itinerary['includes_city_tours'], 1); //debug($includes_city_tours); //exit;



foreach ($tour_visited_city_id as $key => $record) {

    $tour_visited_cities_list[$key]['id'] = $tour_visited_city_id[$key];

    $tour_visited_cities_list[$key]['city'] = $tour_visited_city_id[$key];

    $tour_visited_cities_list[$key]['no_of_nights'] = $no_of_nights[$key];

    

}

//debug($tour_visited_cities_list); exit;

$pricing = json_decode($tours_itinerary['pricing'], 1); //debug($pricing); //exit;

?>

<form

    action="<?php echo base_url(); ?>index.php/tours/itinerary_save"

    method="post" enctype="multipart/form-data" id="form form-horizontal validate-form"

    class='form form-horizontal validate-form' onsubmit="return validation();">

    <div class="tab-content">

        <!-- Add Package Starts -->



        <div role="tabpanel" class="tab-pane active" id="add_package">

            <div class="col-md-12">						    

                <!-- <hr> -->

                <div class='form-group'>

                    <label class='control-label col-sm-3' for='validation_current'>Package Name

                    </label>

                    <div class='col-sm-8 controls'>

                        <input type="text" value="<?= string_replace_encode($tour_data['package_name']); ?>" data-rule-required='true' class='form-control' disabled>									

                    </div>

                </div> 

                <div class='form-group'>

                    <label class='control-label col-sm-3' for='validation_current'>Package Code

                    </label>

                    <div class='col-sm-4 controls'>

                        <input type="text" value="<?= $tours_itinerary['tour_code']; ?>" data-rule-required='true' class='form-control' disabled>									

                    </div>

                </div>

                <?php

                if($dep_date)

                {

                    ?>

                    <div class='form-group'>

                    <label class='control-label col-sm-3' for='validation_current'>Confirmed Departure Date

                    </label>

                    <div class='col-sm-4 controls'>

                        <input type="text" value="<?= changeDateFormat($dep_date) ?>" data-rule-required='true' class='form-control' disabled>                                  

                    </div>

                </div>

                    <?php

                }

                ?>

                

         <?php 

         //debug($tour_visited_cities_list);

         foreach ($tour_visited_cities_list as $index => $record) { 

         $city = $record['city'];

         $city = json_decode($city,1);

         //debug($city);//exit;

         foreach($city as $k => $v)

         {

            if($k==0){$visited_city = $tours_city_name[$v];}

            else{ $visited_city = $visited_city.', '.$tours_city_name[$v];} 

         }    

         ?>

                    <!-- <hr> -->

                    <input type="hidden" name="tour_visited_city_id[]" value="<?= $record['id'] ?>">

                    <!-- <div class='form-group'>

                        <label class='control-label col-sm-3' for='validation_current'>Visited City <?php echo ($index + 1); ?>

                        </label>

                        <div class='col-sm-8 controls'>

                            <input type="text" name="city" id="city" value="<?=$visited_city?> [ <?= $record['no_of_nights'] ?> Nights] " data-rule-required='true' class='form-control' disabled>									

                        </div>

                    </div>   -->                

                    <input type="hidden" name="no_of_nights[]" value="<?= $record['no_of_nights'] ?>">



                    

                              

                <?php

                }



                $day = 1;

                foreach ($tours_itinerary_dw as $index => $record) {



                    if ($index == 0) {

                        $reporting      = $tours_itinerary['reporting'];

                        $reporting_date = $tours_itinerary['reporting_date'];

                        $reporting_desc = $tours_itinerary['reporting_desc'];

                        ?>	

                        <hr>

                        <div class="form-group">

                            <label class="control-label col-sm-3" for="validation_current">Reporting </label>

                            <div class="col-sm-4 controls">

                    <input type="radio" name="reporting" value="Reporting Date" class="reporting" data-rule-required="true" required 

                    <?php if ($reporting == 'Reporting Date') { echo 'checked'; } ?> > Reporting Date <br>

                                <input type="radio" name="reporting" value="Departure Date" class="reporting" data-rule-required="true" required 

                                <?php if ($reporting == 'Departure Date') { echo 'checked'; } ?> > Departure Date									

                            </div>

                        </div>



                        <div class="form-group">

                            <label class="control-label col-sm-3" for="validation_current">Reporting Date </label>

                            <div class="col-sm-4 controls">

                                <input type="text" name="reporting_date" id="reporting_date" value="<?= $reporting_date ?>" data-rule-required="true" required class="form-control" readonly> 					

                            </div>

                        </div>



                        <div class="form-group">

                            <label class="control-label col-sm-3" for="validation_current">Reporting Description

                            </label>

                            <div class="col-sm-8 controls">

                                <textarea name="reporting_desc" id="reporting_desc" class="form-control" cols="70" rows="5" placeholder="Reporting Description"><?= string_replace_encode($reporting_desc) ?></textarea>

                            </div>

                        </div>

                    <?php

                    }

                    ?>

                    <input type="hidden" name="visited_city[]" value="<?=htmlentities($record['visited_city'], ENT_QUOTES)?>">

                    <div id="itinerary_list<?= $index ?>">

                        <?php

                        //debug($tours_itinerary_dw); //exit;

                        //foreach ($tours_itinerary_dw as $key => $value) {

                            //debug($value);

                            //if ($value['tour_visited_city_id'] == $record['id']) {



                                $accomodation = json_decode($record['accomodation'],1);

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



                                $plus_days = '+' . ($day - 1) . ' days';

                                $display_date = date('Y-m-d', strtotime($plus_days, strtotime($dep_date)));

                                $display_date = changeDateFormat($display_date);



                            $city_in_record = $record['visited_city'];

                            $city_in_record = json_decode($city_in_record,1);



                            foreach($city_in_record as $k => $v)

                            {

                              if($k==0){ $city_in_record_str = $tours_city_name[$v]; } 

                              else{ $city_in_record_str = $city_in_record_str.', '.$tours_city_name[$v]; }                             

                            }





                                echo '<hr>';

                                echo '<div class="form-group">

								<label class="control-label col-sm-3" for="validation_current">Day ' . $day . ' : ' . $display_date . ' : in ' . $city_in_record_str . '</label>

								</div>';

                                echo '<div class="form-group">

								<label class="control-label col-sm-3" for="validation_current">Day Program Title </label>

								<div class="col-sm-8 controls">

									<input type="text" name="program_title[]" value="' . string_replace_encode($record['program_title']) . '"

										placeholder="Enter Program Title" data-rule-required="true"

										class="form-control" required>									

								</div>

							</div>';

                                echo '<div class="form-group">

								<label class="control-label col-sm-3" for="validation_current">Program Description

								</label>

								<div class="col-sm-8 controls">

								<textarea name="program_des[]" class="form-control" cols="70" rows="3" placeholder="Description">' . string_replace_encode($record['program_des']) . '</textarea>

								</div>

							</div>';

                                echo '<div class="form-group">

								<label class="control-label col-sm-3" for="validation_current">Hotel Name </label>

								<div class="col-sm-8 controls">

									<input type="text" name="hotel_name[]" value="' . string_replace_encode($record['hotel_name']) . '" placeholder="Enter hotel name" class="form-control">									

								</div>

							</div>';

                                echo '<div class="form-group">

								<label class="control-label col-sm-3" for="validation_current">Star Rating </label>

								<div class="col-sm-4 controls">

								<select name="rating[]" class="form-control">';

                                echo '<option value="0" selected>Select star rating</option>';

                                for ($s = 1; $s <= 5; $s++) {

                                    if ($record['rating'] == $s) {

                                        $selected = '';

                                    } else {

                                        $selected = '';

                                    }

                                    echo '<option value="' . $s . '" ' . $selected . '>' . $s . ' Star</option>';

                                }

                                echo '</select>								

								</div>

							</div>';



                                echo '<div class="form-group">

								<label class="control-label col-sm-3" for="validation_current">Meals </label>

								<div class="col-sm-4 controls">

									<input type="checkbox" name="accomodation[' . ($day - 1) . '][]" value="Breakfast" ' . $Breakfast . '> Breakfast <br>									

									<input type="checkbox" name="accomodation[' . ($day - 1) . '][]" value="Lunch" ' . $Lunch . '> Lunch <br>									

									<input type="checkbox" name="accomodation[' . ($day - 1) . '][]" value="Dinner" ' . $Dinner . '> Dinner <br>									

								</div>

							</div>

                            <input type="hidden" name="tours_itinerary_dw_id[]" value="'.$record['id'].'">';

                                $day++;



                            }

                       // }

                        ?>						

                    </div>

                <?php //} ?>	

                <?php

                /*

                // one more day Starts

                $accomodation = $value['accomodation'];

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

                $plus_days = '+' . ($day - 1) . ' days';

                $display_date = date('Y-m-d', strtotime($plus_days, strtotime($dep_date)));

                $display_date = changeDateFormat($display_date);

                echo '<hr>';

                echo '<div class="form-group">

								<label class="control-label col-sm-3" for="validation_current">Day ' . $day . ' : ' . $display_date . ' : in ' . string_replace_encode($tour_visited_cities_all[$record['id']]) . '</label>

								</div>';

                echo '<div class="form-group">

								<label class="control-label col-sm-3" for="validation_current">Day Program Title </label>

								<div class="col-sm-8 controls">

									<input type="text" name="program_title[]" value="' . string_replace_encode($value['program_title']) . '"

										placeholder="Enter Program Title" data-rule-required="true"

										class="form-control" required>									

								</div>

							</div>';

                echo '<div class="form-group">

								<label class="control-label col-sm-3" for="validation_current">Program Description

								</label>

								<div class="col-sm-8 controls">

								<textarea name="program_des[]" class="form-control" cols="70" rows="3" placeholder="Description">' . string_replace_encode($value['program_des']) . '</textarea>

								</div>

							</div>';

                echo '<div class="form-group">

								<label class="control-label col-sm-3" for="validation_current">Hotel Name </label>

								<div class="col-sm-8 controls">

									<input type="text" name="hotel_name[]" value="' . string_replace_encode($value['hotel_name']) . '" placeholder="Enter hotel name" class="form-control">									

								</div>

							</div>';

                echo '<div class="form-group">

								<label class="control-label col-sm-3" for="validation_current">Star Rating </label>

								<div class="col-sm-4 controls">

								<select name="rating[]" class="form-control">';

                for ($s = 1; $s <= 5; $s++) {

                    if ($value['rating'] == $s) {

                        $selected = 'selected';

                    } else {

                        $selected = '';

                    }

                    echo '<option value="' . $s . '" ' . $selected . '>' . $s . ' Star</option>';

                }

                echo '</select>								

								</div>

							</div>';



                echo '<div class="form-group">

								<label class="control-label col-sm-3" for="validation_current">Meals </label>

								<div class="col-sm-4 controls">

									<input type="checkbox" name="accomodation[' . ($day - 1) . '][]" value="Breakfast" ' . $Breakfast . '> Breakfast <br>									

									<input type="checkbox" name="accomodation[' . ($day - 1) . '][]" value="Lunch" ' . $Lunch . '> Lunch <br>									

									<input type="checkbox" name="accomodation[' . ($day - 1) . '][]" value="Dinner" ' . $Dinner . '> Dinner <br>									

								</div>

							</div>';

                // one more day start 

                            */

                ?>				

                <?php

                $inclusions_checks = $tours_itinerary['inclusions_checks'];

                $inclusions_checks = json_decode($inclusions_checks,1);                         

                ?>

                <hr>

                <div class='form-group'>

                    <label class='control-label col-sm-3' for='validation_current'>Inclusions</label>

                    <div class='col-sm-4 controls'>

                        <input type="checkbox" name="inclusions_checks[]" value="Hotel" <?php if(in_array('Hotel',$inclusions_checks)){ echo 'checked';}?> > <!-- <i class="fa fa-hotel"> --> Hotel  <br>  

                        <input type="checkbox" name="inclusions_checks[]" value="Car" <?php if(in_array('Car',$inclusions_checks)){ echo 'checked';}?> > <!-- <i class="fa fa-car"> --> Car  <br>

                        <input type="checkbox" name="inclusions_checks[]" value="Meals" <?php if(in_array('Meals',$inclusions_checks)){ echo 'checked';}?> > <!-- <i class="fa fa-spoon"> --> Meals  <br>

                        <input type="checkbox" name="inclusions_checks[]" value="Sightseeing" <?php if(in_array('Sightseeing',$inclusions_checks)){ echo 'checked';}?> > <!-- <i class="fa fa-binoculars"> --> Sightseeing  <br> 

                         <input type="checkbox" name="inclusions_checks[]" value="Transfers" <?php if(in_array('Transfers',$inclusions_checks)){ echo 'checked';}?>> <!-- <i class="fa fa-binoculars"> --> Transfers  <br>     

                      <br>         

                    </div>

                </div>  

                <input type="hidden" name="adult_twin_sharing" id="adult_twin_sharing" value="<?= $tours_itinerary['adult_twin_sharing'] ?>"

                               placeholder="Adult on twin sharing" data-rule-required='true'

                               class='form-control add_pckg_elements' required> 



                                <input type="hidden" name="adult_tripple_sharing" id="adult_tripple_sharing" value="<?= $tours_itinerary['adult_tripple_sharing'] ?>"

                               placeholder="Adult on tripple sharing" class='form-control'> 

<!--                 <div class='form-group'>

                    <label class='control-label col-sm-3' for='validation_current'>Adult on twin sharing

                    </label>

                    <div class='col-sm-4 controls'>

                        <input type="text" name="adult_twin_sharing" id="adult_twin_sharing" value="<?= $tours_itinerary['adult_twin_sharing'] ?>"

                               placeholder="Adult on twin sharing" data-rule-required='true'

                               class='form-control add_pckg_elements' required>									

                    </div>

                </div>

                <div class='form-group'>

                    <label class='control-label col-sm-3' for='validation_current'>Adult on tripple sharing

                    </label>

                    <div class='col-sm-4 controls'>

                        <input type="text" name="adult_tripple_sharing" id="adult_tripple_sharing" value="<?= $tours_itinerary['adult_tripple_sharing'] ?>"

                               placeholder="Adult on tripple sharing" class='form-control'>									

                    </div>

                </div> -->

                <!--<div class='form-group'>

                    <label class='control-label col-sm-3' for='validation_current'>Child with bed

                    </label>

                    <div class='col-sm-4 controls'>

                        <input type="text" name="child_with_bed" id="child_with_bed"  value="<?= $pricing['child_with_bed'] ?>"

                               placeholder="Child with bed"  class='form-control'>									

                    </div>

                </div>

                <div class='form-group'>

                    <label class='control-label col-sm-3' for='validation_current'>Child without bed

                    </label>

                    <div class='col-sm-4 controls'>

                        <input type="text" name="child_without_bed" id="child_without_bed" value="<?= $pricing['child_without_bed'] ?>"

                               placeholder="Child without bed" class='form-control'>									

                    </div>

                </div>

                <div class='form-group'>

                    <label class='control-label col-sm-3' for='validation_current'>Joining directly

                    </label>

                    <div class='col-sm-4 controls'>

                        <input type="text" name="joining_directly" id="joining_directly" value="<?= $pricing['joining_directly'] ?>"

                               placeholder="Joining directly" class='form-control'>									

                    </div>

                </div>	

                <div class='form-group'>

                    <label class='control-label col-sm-3' for='validation_current'>Single Suppliment

                    </label>

                    <div class='col-sm-4 controls'>

                        <input type="text" name="single_suppliment" id="single_suppliment" value="<?= $pricing['single_suppliment'] ?>"

                               placeholder="Single Suppliment" class='form-control' >									

                    </div>

                </div>

                <div class='form-group'>

                    <label class='control-label col-sm-3' for='validation_current'>Serive Tax Desciption

                    </label>

                    <div class='col-sm-8 controls'>

                        <input type="text" name="service_tax" id="service_tax" value="<?= string_replace_encode($tours_itinerary['service_tax']) ?>"

                               placeholder="Serive Tax Desciption" class='form-control' >									

                    </div>

                </div>

                <div class='form-group'>

                    <label class='control-label col-sm-3' for='validation_current'>TCS Desciption

                    </label>

                    <div class='col-sm-8 controls'>

                        <input type="text" name="tcs" id="tcs" value="<?= string_replace_encode($tours_itinerary['tcs']) ?>"

                               placeholder="TCS Desciption" class='form-control' >									

                    </div>

                </div>-->

                <div class="form-group">

                    <label class="control-label col-sm-3" for="validation_current">Highlights

                    </label>

                    <div class="col-sm-8 controls">

                        <textarea name="highlights" class="form-control" cols="70" rows="5" placeholder="Tour Highlights"><?= string_replace_encode($tours_itinerary['highlights']) ?></textarea>

                    </div>

                </div>

                <div class="form-group">

                    <label class="control-label col-sm-3" for="validation_current">Tour Inclusions

                    </label>

                    <div class="col-sm-8 controls">

                        <textarea name="inclusions" class="form-control" cols="70" rows="5" placeholder="Tour Inclusions"><?= string_replace_encode($tours_itinerary['inclusions']) ?></textarea>

                    </div>

                </div>

                <div class="form-group">

                    <label class="control-label col-sm-3" for="validation_current">Tour Exclusions

                    </label>

                    <div class="col-sm-8 controls">

                        <textarea name="exclusions" class="form-control" cols="70" rows="5" placeholder="Tour Exclusions"><?= string_replace_encode($tours_itinerary['exclusions']) ?></textarea>

                    </div>

                </div>

                <div class="form-group">

                    <label class="control-label col-sm-3" for="validation_current">Terms & Conditions

                    </label>

                    <div class="col-sm-8 controls">

                        <textarea name="terms" class="form-control" cols="70" rows="5" placeholder="Tour Exclusions"><?= string_replace_encode($tours_itinerary['terms']) ?></textarea>

                    </div>

                </div>

                <div class="form-group">

                    <label class="control-label col-sm-3" for="validation_current">Cancellation Policy

                    </label>

                    <div class="col-sm-8 controls">

                        <textarea name="canc_policy" class="form-control" cols="70" rows="5" placeholder="Cancellation Policy"><?= string_replace_encode($tours_itinerary['canc_policy']) ?></textarea>

                    </div>

                </div>

               <!--  <div class='form-group'>

                    <label class='control-label col-sm-3' for='validation_current'>&nbsp;</label> 

                    <div class='col-sm-4 controls'>

                        <input type="checkbox" name="publish_status" id="publish_status" value="1" <?php if ($tours_itinerary['publish_status'] == 1) {

                    echo 'checked';

                } ?>> Publish

                    </div>

                </div>	 -->	    



                <div class='' style='margin-bottom: 0'>

                    <div class='row'>

                        <div class='col-sm-9 col-sm-offset-3'>	

                            <input type="hidden" name="reporting_date_fixed" id="reporting_date_fixed" value="<?php echo date('Y-m-d', strtotime('-1 day', strtotime($dep_date))); ?>">

                            <input type="hidden" name="dep_date" id="dep_date" value="<?= $dep_date ?>">

                            <input type="hidden" name="tour_id" value="<?= $tour_id ?>">

                            <input type="hidden" name="id" value="<?= $tours_itinerary['id'] ?>">							

                            <button class='btn btn-primary' type='submit'>Save</button>

                        </div>

                    </div>

                </div>

            </div>																	

        </div>					

    </div>

     

</form>	

<script type="text/javascript">

    $(document).ready(function()

    {         

        $('.reporting').on('click', function() { 

            $reporting            = $(this).val();  

            $reporting_date_fixed = $('#reporting_date_fixed').val();  

            $dep_date             = $('#dep_date').val(); 

            //alert($dep_date);alert($reporting_date_fixed);

            if($reporting=='Reporting Date')

            {

                $('#reporting_date').val($reporting_date_fixed);

            } 

            else if($reporting=='Departure Date')

            {

                $('#reporting_date').val($dep_date);

            }       

        }); 

    });

    function validation()

    {

        $adult_twin_sharing    = $('#adult_twin_sharing').val();

        $adult_tripple_sharing = $('#adult_tripple_sharing').val();

        $child_with_bed        = $('#child_with_bed').val();

        $child_without_bed     = $('#child_without_bed').val();

        $joining_directly      = $('#joining_directly').val();

        $single_suppliment     = $('#single_suppliment').val();



        if($adult_twin_sharing=="")

        {

            $("#adult_twin_sharing").attr("placeholder","Adult Twin Sharing Required.");

            $("#adult_twin_sharing").focus();

            return false;

        }

        else if(isNaN($adult_twin_sharing))

        {

            $("#adult_twin_sharing").val('');

            $("#adult_twin_sharing").attr("placeholder","Valid Amount Required.");

            $("#adult_twin_sharing").focus();

            return false;

        }

        if($adult_tripple_sharing!="" && isNaN($adult_tripple_sharing))

        {

            $("#adult_tripple_sharing").val('');

            $("#adult_tripple_sharing").attr("placeholder","Valid Amount Required.");

            $("#adult_tripple_sharing").focus();

            return false;

        }

        /*if($child_with_bed!="" && isNaN($child_with_bed))

        {

            $("#child_with_bed").val('');

            $("#child_with_bed").attr("placeholder","Valid Amount Required.");

            $("#child_with_bed").focus();

            return false;

        }

        if($child_without_bed!="" && isNaN($child_without_bed))

        {

            $("#child_without_bed").val('');

            $("#child_without_bed").attr("placeholder","Valid Amount Required.");

            $("#child_without_bed").focus();

            return false;

        }

        if($joining_directly!="" && isNaN($joining_directly))

        {

            $("#joining_directly").val('');

            $("#joining_directly").attr("placeholder","Valid Amount Required.");

            $("#joining_directly").focus();

            return false;

        }

        if($single_suppliment!="" && isNaN($single_suppliment))

        {

            $("#single_suppliment").val('');

            $("#single_suppliment").attr("placeholder","Valid Amount Required.");

            $("#single_suppliment").focus();

            return false;

        }*/

    }        

</script> 

<?php

       $HTTP_HOST = '192.168.0.63';

       if(($_SERVER['HTTP_HOST']==$HTTP_HOST) || ($_SERVER['HTTP_HOST']=='localhost'))

	   {

				$airliners_weburl = '/airliners/';	 

	   }

	   else

	   {

				$airliners_weburl = '/~development/airliners_v1/';

       } 

       /*<?=$airliners_weburl?>*/       

       ?>

<script type="text/javascript" src="<?=$airliners_weburl?>extras/system/template_list/template_v1/javascript/js/tiny_mce/tiny_mce.js"></script>

<script type="text/javascript" src="<?=$airliners_weburl?>extras/system/template_list/template_v1/javascript/js/tiny_mce/tiny_mce_call.js"></script>

<!-- <script type="text/javascript" src="/airliners/extras/system/template_list/template_v1/javascript/js/nicEdit-latest.js"></script> 

<script type="text/javascript">

//<![CDATA[

bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });

//]]>

</script> -->

