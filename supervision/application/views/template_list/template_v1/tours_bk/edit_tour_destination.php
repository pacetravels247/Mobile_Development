<?php error_reporting(0); //debug($tour_destination_details); //exit; ?>
<div id="Package" class="bodyContent col-md-12">
    <div class="panel panel-default">
        <!-- PANEL WRAP START -->
        <div class="panel-heading">
            <!-- PANEL HEAD START -->
            <div class="panel-title">
                <ul class="nav nav-tabs nav-justified" role="tablist" id="myTab">
                    <!-- INCLUDE TAB FOR ALL THE DETAILS ON THE PAGE START-->
                    <li role="presentation" class="active" id="add_package_li"><a
                            href="#add_package" aria-controls="home" role="tab"
                            data-toggle="tab">Edit Tour Destinations </a></li>			
                </ul>
            </div>
        </div>
        <!-- PANEL HEAD START -->
        <div class="panel-body">
            <!-- PANEL BODY START -->
            <form
                action="<?php echo base_url(); ?>index.php/tours/edit_tour_destination_save"
                method="post" enctype="multipart/form-data" id="form form-horizontal validate-form"
                class='form form-horizontal validate-form'>
                <div class="tab-content">
                    <!-- Add Package Starts -->
                    <div role="tabpanel" class="tab-pane active" id="add_package">
                        <div class="col-md-12">

                            <input type="hidden" name="a_wo_p" value="a_w"> <input type="hidden" name="deal" value="0">
                            <div class='form-group'>
                                <label class='control-label col-sm-3' for='validation_current'>Package Type</label>
                                <div class='col-sm-4 controls'>
                                    <input type="radio" name="pkg_type" id="pkg_typeD" value="Domestic" data-rule-required='true' class='form-control2 pkg_typeD' required <?php
if ($tour_destination_details['type'] == 'Domestic') {
    echo 'checked';
}
?> > Domestic <br> 
                                    <input type="radio" name="pkg_type" id="pkg_typeI" value="International" data-rule-required='true' class='form-control2 pkg_typeD' required <?php
if ($tour_destination_details['type'] == 'International') {
    echo 'checked';
}
?> > International
                                </div>
                            </div>							
                            <div class='form-group'>
                                <label class='control-label col-sm-3' for='validation_current'>Destination
                                </label>
                                <div class='col-sm-8 controls'>
                                    <input type="text" name="destination" id="destination"
                                           placeholder="Enter Destination" data-rule-required='true'
                                           class='form-control add_pckg_elements' required value="<?php echo string_replace_encode($tour_destination_details['destination']);?>">

                                </div>
                            </div>
                            <div class='form-group'>
                                <label class='control-label col-sm-3' for='validation_current'>Tour Description
                                </label>
                                <div class='col-sm-8 controls'>
                                    <textarea name="description" id="description" data-rule-required='true' class="form-control" data-rule-required="true" cols="70" rows="5" placeholder="Description" required><?= string_replace_encode($tour_destination_details['description']) ?></textarea>									
                                </div>
                            </div>	
                            <div class='form-group'>
                                <label class='control-label col-sm-3' for='validation_current'>Tour Highlights
                                </label>
                                <div class='col-sm-8 controls'>
                                    <textarea name="highlights" id="highlights" data-rule-required='true' class="form-control" data-rule-required="true" cols="70" rows="5" placeholder="Highlights" required><?= string_replace_encode($tour_destination_details['highlights']) ?></textarea>									
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-sm-3' for='validation_current'>Banner Image
                                </label>
                                <div class='col-sm-4 controls'>
<?php
echo '<img class="banner_imgs" src="/airliners/extras/custom/keWD7SNXhVwQmNRymfGN/images/' . $tour_destination_details['banner_image'] . '" style="width:100%">';
?>										
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-sm-3' for='validation_current'>Change Banner Image
                                </label>
                                <div class='col-sm-4 controls'>
                                    <input type="file" name="banner_image" id="banner_image" class='form-control'>									
                                </div>
                            </div>

                            <div class='form-group'>
                                <label class='control-label col-sm-3' for='validation_current'>Gallery
                                </label>
                                <div class='col-sm-9 controls'>
                                    <?php
                                    $gallery = $tour_destination_details['gallery'];
                                    $explode = explode(',', $gallery);
                                    $galleryImages = '';
                                    for ($g = 0; $explode[$g] != ''; $g++) {
                                        if ($g % 6 == 0) {
                                            $galleryImages .= '<tr>';
                                        }
                                        //$path = $this->template->domain_image_upload_path().$explode[$g];
                                        $galleryImages .= '<td style="width:30px;"><input type="checkbox" name="gallery_previous[]" value="' . $explode[$g] . '" checked></td><td ><img style="width:100%;" class="gal_img" src="/airliners/extras/custom/keWD7SNXhVwQmNRymfGN/images/' . $explode[$g] . '"></td> ';
                                        //echo '<img src="'.$path.'" style="width:50%"> <input type="checkbox" name="gallery_previous[]" value="'.$explode[$g].'" checked><br><br>';
                                        if (($g + 1) % 6 == 0) {
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
                                </div>
                            </div>	

                            <div class='' style='margin-bottom: 0'>
                                <div class='row'>
                                    <div class='col-sm-9 col-sm-offset-3'>	
                                        <input type="hidden" name="id" value="<?= $tour_destination_details['id'] ?>">							
                                        <button class='btn btn-primary' type='submit'>Save</button>
                                        <a href="<?php echo base_url(); ?>index.php/tours/tour_destinations" class='btn btn-primary' style="color:white;">Main Destinations</a>

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </form>
        </div>
        <!-- PANEL BODY END -->
    </div>
    <!-- PANEL WRAP END -->
</div>

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
<!--
<script type="text/javascript" src="/chariot/extras/system/template_list/template_v1/javascript/js/nicEdit-latest.js"></script> 
<script type="text/javascript">
bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
</script>-->
