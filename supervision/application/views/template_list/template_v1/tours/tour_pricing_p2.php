<script src="<?php echo SYSTEM_RESOURCE_LIBRARY?>/ckeditor/ckeditor.js"></script>
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
						data-toggle="tab">Package Descriptions [ Package Name : <?=string_replace_encode($tour_data['package_name'])?> ]</a></li>
					
				</ul>
			</div>
		</div>
		<!-- PANEL HEAD START -->
		<div class="panel-body">
			<!-- PANEL BODY START -->
			<form
				action="<?php echo base_url(); ?>index.php/tours/tour_pricing_p2_save"
				method="post" enctype="multipart/form-data" id="form form-horizontal validate-form"
				class='form form-horizontal validate-form' onsubmit="return validation();">
				<div class="tab-content">
					<!-- Add Package Starts -->
					<div role="tabpanel" class="tab-pane active" id="add_package">
						   <div class="col-md-12">
						   	
						   	<input type="hidden" name="adult_twin_sharing" id="adult_twin_sharing" value="100"
										placeholder="Adult on twin sharing" data-rule-required='true'
										class='form-control add_pckg_elements' required>
										
										
									<input type="hidden" name="adult_tripple_sharing" id="adult_tripple_sharing" value="200"
										placeholder="Adult on tripple sharing"  class='form-control'>									
								
                           
						   <!--div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Per Person/Double Occupancy
								</label>
								<div class='col-sm-4 controls'>
									<input type="text" name="adult_twin_sharing" id="adult_twin_sharing" value="<?=$tour_data['adult_twin_sharing']?>"
										placeholder="Adult on twin sharing" data-rule-required='true'
										class='form-control add_pckg_elements' required>									
								</div>
							</div>
<!-- 					 		<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Adult on tripple sharing
								</label>
								<div class='col-sm-4 controls'>
									<input type="hidden" name="adult_tripple_sharing" id="adult_tripple_sharing" value="<?=$tour_data['adult_tripple_sharing']?>"
										placeholder="Adult on tripple sharing"  class='form-control'>									
								</div>
							</div>  -->
							<input type="hidden" name="adult_tripple_sharing" id="adult_tripple_sharing" value="0">
							<!--<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Child with bed
								</label>
								<div class='col-sm-4 controls'>
									<input type="text" name="child_with_bed" id="child_with_bed"  value="<?=$tour_data['child_with_bed']?>"
										placeholder="Child with bed" class='form-control'>									
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Child without bed
								</label>
								<div class='col-sm-4 controls'>
									<input type="text" name="child_without_bed" id="child_without_bed" value="<?=$tour_data['child_without_bed']?>"
										placeholder="Child without bed" class='form-control' >									
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Joining directly
								</label>
								<div class='col-sm-4 controls'>
									<input type="text" name="joining_directly" id="joining_directly" value="<?=$tour_data['joining_directly']?>"
										placeholder="Joining directly" class='form-control' >									
								</div>
							</div>

							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Single Suppliment
								</label>
								<div class='col-sm-4 controls'>
									<input type="text" name="single_suppliment" id="single_suppliment" value="<?=$tour_data['single_suppliment']?>"
										placeholder="Single Suppliment" class='form-control'>									
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Service Tax
								</label>
								<div class='col-sm-8 controls'>
									<input type="text" name="service_tax" id="service_tax" value="<?=string_replace_encode($tour_data['service_tax'])?>"
										placeholder="Service Tax" class='form-control'>									
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>TCS
								</label>
								<div class='col-sm-8 controls'>
									<input type="text" name="tcs" id="tcs" value="<?=string_replace_encode($tour_data['tcs'])?>"
										placeholder="TCS" class='form-control'>									
								</div>
							</div>-->
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Choose Country <span style = "color:red">*</span>
								</label>
								<div class='col-sm-6 col-md-6 controls'>
								<select class='select2 form-control' data-rule-required='true' name='tours_country[]' id="tours_country" multiple>
									<?php 
									//debug($tour_country);exit;
										foreach($tour_country as $t_key =>$t_val){
									?>
										<option value="<?=$t_key?>"><?=$t_val?></option>
									<?php
										}
									?>
                               
								</select>				
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3" for="validation_current">Highlights
								</label>
								<div class="col-sm-8 controls">
								<textarea name="highlights" class="form-control" id="highlights" cols="70" rows="5" placeholder="Tour Highlights"><?=string_replace_encode($tour_data['inclusions'])?></textarea>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3" for="validation_current">Inclusions <span class="text-danger">*</span>
								</label>
								<div class="col-sm-8 controls">
								<textarea name="inclusions" class="form-control" id="inclusions" cols="70" rows="5" placeholder="Tour Inclusions"><?=string_replace_encode($tour_data['inclusions'])?></textarea>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3" for="validation_current">Exclusions <!-- <span class="text-danger">*</span>-->
								</label>
								<div class="col-sm-8 controls">
								<textarea name="exclusions" class="form-control" id="exclusions" cols="70" rows="5" placeholder="Tour Exclusions"><?=string_replace_encode($tour_data['exclusions'])?></textarea>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3" for="validation_current">Terms & Conditions
								</label>
								<div class="col-sm-8 controls">
								<textarea name="terms" class="form-control" id="terms_conditions" cols="70" rows="5" placeholder="Terms & Conditions"><?=string_replace_encode($tour_data['terms'])?></textarea>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3" for="validation_current">Cancellation Policy
								</label>
								<div class="col-sm-8 controls">
								<textarea name="canc_policy" class="form-control" id="cancelation_policy" cols="70" rows="5" placeholder="Cancellation Policy"><?=string_replace_encode($tour_data['canc_policy'])?></textarea>
								</div>
							</div>	
							<div class="form-group">
								<label class="control-label col-sm-3" for="validation_current">Trip Notes
								</label>
								<div class="col-sm-8 controls">
								<textarea name="trip_notes" class="form-control" id="trip_note" cols="70" rows="5" placeholder="Trip Notes"><?=string_replace_encode($tour_data['trip_notes'])?></textarea>
								</div>
							</div>		
							<div class="form-group">
								<label class="control-label col-sm-3" for="validation_current">Visa Procedures
								</label>
								<div class="col-sm-8 controls">
								<textarea name="visa_procedures" class="form-control" id="visa_procedures" cols="70" rows="5" placeholder="Trip Notes"><?=string_replace_encode($tour_data['visa_procedures'])?></textarea>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3" for="validation_current">B2B Payment Policy
								</label>
								<div class="col-sm-8 controls">
								<textarea name="b2b_payment_policy" class="form-control" id="b2b_payment_policy" cols="70" rows="5" placeholder="B2B Payment Policy"><?=string_replace_encode($tour_data['b2b_payment_policy'])?></textarea>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3" for="validation_current">B2C Payment Policy
								</label>
								<div class="col-sm-8 controls">
								<textarea name="b2c_payment_policy" class="form-control" id="b2c_payment_policy" cols="70" rows="5" placeholder="B2C Payment Policy"><?=string_replace_encode($tour_data['b2c_payment_policy'])?></textarea>
								</div>
							</div>
						<!--	<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Banner Image
								</label>
								<div class='col-sm-4 controls'>
									<input type="file" name="banner_image" id="banner_image" class='form-control' data-rule-required='true' >					<?=img_size_msg(360,320)?>				
								</div>
							</div>
						    
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Upload Gallery
								</label>
								<div class='col-sm-4 controls'>
									<input type="file" name="gallery[]" id="gallery" multiple data-rule-required='true' class='form-control' >				<?=img_size_msg(610,370)?>						
								</div>
							</div>
							<div class='form-group'>
                              <label class='control-label col-sm-3' for='validation_current'>Image Descriptions
                                </label>
                               <div class='col-sm-9 controls'>
                               
                            <textarea  placeholder="Description" class="form-control" name="image_description"   id="image_description" ></textarea>
                            <strong>Note *:</strong>
                       		<span style="color:#999;">Please update each image description followed by "#"</span>
                            </div>
                            </div>		
						-->
						    <div class='' style='margin-bottom: 0'>
								<div class='row'>
									<div class='col-sm-9 col-sm-offset-3'>	
									    <input type="hidden" name="tour_id" value="<?=$tour_id?>">						
										<button class='btn btn-primary' type="submit">Save</button>
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
<script type="text/javascript">
     $(document).ready(function()
     {

		var tours_country = $('select[name="tours_country[]"]').bootstrapDualListbox({
			sortByInputOrder: 'true'
					 // nonSelectedListLabel: 'Non-selected',
					//  selectedListLabel: 'Selected',
					//  preserveSelectionOnMove: false,
					//  moveOnSelect: false
		});
		var prev_city='';
		$(document).on('mouseover',"select",function(){
		$(this).parent().find(".filter").blur();
		});
		$('#tours_country').on('change', function() { 
           
         	var tours_countries = $('#tours_country').val();
			//console.log(tours_countries);
         	if(tours_countries==null){
         		$('#tours_city').html('');
         	}
         	if(tours_countries.length > 0 ){
				var tours_country_list = tours_countries;
         	}else{
	         	var tours_country_list = Array.from(tours_countries);
	         }
			 
			 
			var i;
			var prev_len=prev_city.length;
			
			if(prev_city!=''){
				//alert(prev_city);
				if(prev_len>1){
					var prev_len_arr=Array.from(prev_city);
				}else{
					var prev_len_arr=prev_city;
				}
				var now_len = tours_country_list.length;
			}
			
			
			if(now_len < prev_len){
				
				//console.log(prev_city);
				for (i = 0; i< prev_len; i++) {
					
					if(jQuery.inArray(prev_len_arr[i], tours_countries) !== -1){
						//alert("there")
					}else{
						
						prev_len_arr.splice(i, 1);
						var tours_country_array=Array.from(prev_len_arr);
					}
				}
			
			}else{
				
				for (i = 0; i< tours_country_list.length; i++) {
					if(jQuery.inArray(tours_country_list[i], prev_city) !== -1){
						
					}else{
						var tours_country_array=Array.from(prev_city);
						
						tours_country_array.push(tours_country_list[i]);
						
					}
				}
			}
			console.log(tours_country_array);
			prev_city=tours_country_array;
				
				$.post('<?=base_url();?>tours/ajax_tours_highlights',{'tours_country':tours_country_array},function(data)
				{
					//$('#highlights').html(data);
					//$('#cke_highlights').find('.cke_editable').html(data);
					CKEDITOR.instances['highlights'].setData(data);
				});
				$.post('<?=base_url();?>tours/ajax_tours_inclusions',{'tours_country':tours_country_array},function(data)
				{
					//$('#inclusions').html(data);
					CKEDITOR.instances['inclusions'].setData(data);
				});
				$.post('<?=base_url();?>tours/ajax_tours_exclusions',{'tours_country':tours_country_array},function(data)
				{
					//$('#exclusions').html(data);
					CKEDITOR.instances['exclusions'].setData(data);
				});
				$.post('<?=base_url();?>tours/ajax_tours_terms_conditions',{'tours_country':tours_country_array},function(data)
				{
					//$('#terms_conditions').html(data);
					CKEDITOR.instances['terms_conditions'].setData(data);
				});
				$.post('<?=base_url();?>tours/ajax_tours_cancelation_policy',{'tours_country':tours_country_array},function(data)
				{
					//$('#cancelation_policy').html(data);
					CKEDITOR.instances['cancelation_policy'].setData(data);
				});
				$.post('<?=base_url();?>tours/ajax_tours_trip_note',{'tours_country':tours_country_array},function(data)
				{
					//$('#trip_note').html(data);
					CKEDITOR.instances['trip_note'].setData(data);
				});
				$.post('<?=base_url();?>tours/ajax_tours_visa_procedures',{'tours_country':tours_country_array},function(data)
				{
					//$('#visa_procedures').html(data);
					CKEDITOR.instances['visa_procedures'].setData(data);
				});
				$.post('<?=base_url();?>tours/ajax_tours_b2b_payment_policy',{'tours_country':tours_country_array},function(data)
				{
					//$('#visa_procedures').html(data);
					CKEDITOR.instances['b2b_payment_policy'].setData(data); 
				}); 
				$.post('<?=base_url();?>tours/ajax_tours_b2c_payment_policy',{'tours_country':tours_country_array},function(data)
				{
					//$('#visa_procedures').html(data);
					CKEDITOR.instances['b2c_payment_policy'].setData(data);
				});
					
			
		        /*$.post('<?=base_url();?>tours/ajax_tours_highlights',{'tours_country':tours_countries},function(data)
		        {
					//$('#highlights').html(data);
					//$('#cke_highlights').find('.cke_editable').html(data);
					CKEDITOR.instances['highlights'].setData(data);
		        });
				$.post('<?=base_url();?>tours/ajax_tours_inclusions',{'tours_country':tours_countries},function(data)
		        {
		        	//$('#inclusions').html(data);
					CKEDITOR.instances['inclusions'].setData(data);
		        });
				$.post('<?=base_url();?>tours/ajax_tours_exclusions',{'tours_country':tours_countries},function(data)
		        {
		        	//$('#exclusions').html(data);
					CKEDITOR.instances['exclusions'].setData(data);
		        });
				$.post('<?=base_url();?>tours/ajax_tours_terms_conditions',{'tours_country':tours_countries},function(data)
		        {
		        	//$('#terms_conditions').html(data);
					CKEDITOR.instances['terms_conditions'].setData(data);
		        });
				$.post('<?=base_url();?>tours/ajax_tours_cancelation_policy',{'tours_country':tours_countries},function(data)
		        {
		        	//$('#cancelation_policy').html(data);
					CKEDITOR.instances['cancelation_policy'].setData(data);
		        });
				$.post('<?=base_url();?>tours/ajax_tours_trip_note',{'tours_country':tours_countries},function(data)
		        {
		        	//$('#trip_note').html(data);
					CKEDITOR.instances['trip_note'].setData(data);
		        });
				$.post('<?=base_url();?>tours/ajax_tours_visa_procedures',{'tours_country':tours_countries},function(data)
		        {
		        	//$('#visa_procedures').html(data);
					CKEDITOR.instances['visa_procedures'].setData(data);
		        });
				$.post('<?=base_url();?>tours/ajax_tours_b2b_payment_policy',{'tours_country':tours_countries},function(data)
		        {
		        	//$('#visa_procedures').html(data);
					CKEDITOR.instances['b2b_payment_policy'].setData(data); 
		        }); 
				$.post('<?=base_url();?>tours/ajax_tours_b2c_payment_policy',{'tours_country':tours_countries},function(data)
		        {
		        	//$('#visa_procedures').html(data);
					CKEDITOR.instances['b2c_payment_policy'].setData(data);
		        });*/
	       // });
		   
		});
     	  $('#tour_addition_save').on('click', function() { //alert('tour_addition_save');
          $package_name = $('#package_name').val();
          $destination  = $('#destination').val(); 
          $duration     = $('#duration').val();
          $.post('add_tour_pre/'+$package_name+'/'+$destination+'/'+$duration,{'duration':$duration},function(data)
          {
          	  //alert(data);
              $('#tour_addition').html(data);
          });
          });

          $('#no_of_days').on('change', function() { //alert('no_of_days');
          $no_of_days = $(this).val(); 
          $.post('no_of_days/'+$no_of_days,{'no_of_days':$no_of_days},function(data)
          {
          	  //alert(data);
              $('#itinerary_contents').html(data);
          });
          });

          $('#no_of_hotels').on('change', function() { //alert('no_of_hotels');
          $no_of_hotels = $(this).val(); 
          $.post('no_of_hotels/'+$no_of_hotels,{'no_of_hotels':$no_of_hotels},function(data)
          {
          	  //alert(data);
              $('#hotel_contents').html(data);
          });
          });

          $('#no_of_weather').on('change', function() { //alert('no_of_weather');
          $no_of_weather = $(this).val(); 
          $.post('no_of_weather/'+$no_of_weather,{'no_of_weather':$no_of_weather},function(data)
          {
          	  //alert(data);
              $('#weather_contents').html(data);
          });
          });

          $('#tour_dep_date').on('change', function() { //alert('tour_dep_date');
          $tour_dep_date = $(this).val(); 
          $.post('tour_dep_date/'+$tour_dep_date,{'tour_dep_date':$tour_dep_date},function(data)
          {
          	  //alert(data);
              $('#tour_dep_date_list').html(data);
              $('#tour_dep_date').val(''); 
          });
          });   
     });
     
     function validation()
     {
          $adult_twin_sharing    = $('#adult_twin_sharing').val();
          $adult_tripple_sharing = $('#adult_tripple_sharing').val();
          /*$child_with_bed        = $('#child_with_bed').val();
          $child_without_bed     = $('#child_without_bed').val();
          $joining_directly      = $('#joining_directly').val();
          $single_suppliment     = $('#single_suppliment').val();*/

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
          } */
     }
</script>

<script type="text/javascript">
	function validation()
	{
	var inclusions = tinymce.get('inclusions').getContent();
	var exclusions = tinymce.get('exclusions').getContent(); 
	//var trip_notes = tinymce.get('trip_notes').getContent();  
   
    if (inclusions ==null || inclusions == '' )
      {
      alert("Inclusions is Required Field");
      return false;
      }
     /* if (exclusions ==null || exclusions == '' )
      {
      alert("Exclusions is Required Field");
      return false;
      }*/
     
	}
</script>
<script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
<script>
$( document ).ready(function (){
	CKEDITOR.replace( 'highlights' );
	CKEDITOR.replace( 'inclusions' );
	CKEDITOR.replace( 'exclusions' );
	CKEDITOR.replace( 'trip_notes' );
	CKEDITOR.replace( 'visa_procedures' );
	CKEDITOR.replace( 'terms_conditions' );
	CKEDITOR.replace( 'cancelation_policy' );
	CKEDITOR.replace( 'b2b_payment_policy' );
	CKEDITOR.replace( 'b2c_payment_policy' );
});
</script>

<script type="text/javascript" src="<?=get_domain()?>extras/system/template_list/template_v1/javascript/js/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" src="<?=get_domain()?>extras/system/template_list/template_v1/javascript/js/tiny_mce/tiny_mce_call.js"></script> 
<!--
<script type="text/javascript" src="/chariot/extras/system/template_list/template_v1/javascript/js/nicEdit-latest.js"></script> 
<script type="text/javascript">
bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
</script>-->
