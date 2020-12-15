<style>
	.mode {
    border: 1px solid #ccc;
    float: left;
    width: 100%;
    padding: 0 0 20px;
}
.remove_hotel .fa-minus {
    font-size: 8px;
}
.add_hotel .fa-plus {
    font-size: 10px;
}
	.hotels_block {float: left;}
	.controls_new {width: 100%;float: left;}
	.mode_new {border: 1px solid #ccc;}
</style>
<?php error_reporting(0); ?>
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
						data-toggle="tab">Package Manager </a></li>
					
				</ul>
			</div>
		</div>
		<!-- PANEL HEAD START -->
		<div class="panel-body">
			<!-- PANEL BODY START -->
			<form
				action="<?php echo base_url(); ?>index.php/tours/add_tour_save"
				method="post" enctype="multipart/form-data" id="form form-horizontal validate-form"
				class='form form-horizontal validate-form'>
				<div class="tab-content">
					<!-- Add Package Starts -->
					<div role="tabpanel" class="tab-pane active" id="add_package">
						   <div class="col-md-12">
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Trip Type <span style = "color:red">*</span>
								</label>
								<div class='col-sm-3 col-md-8 controls'>
									<select class=' form-control'  name='trip_type' id="trip_type"  >
										<option value="">Select trip type</option>
										<option value="1">International</option>
										<option value="2">Domestic</option>
									</select>	
											
								</div>
							</div>
						<!--	<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Tour Code<span style = "color:red">*</span>
								</label>
								<div class='col-sm-8 controls'>
									<input type="text" name="tour_code" id="tour_code"
										placeholder="Enter Tour Code" data-rule-required='true'
										class='form-control add_pckg_elements' required>									
								</div>
							</div>-->
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Package Name <span style = "color:red">*</span>
								</label>
								<div class='col-sm-8 controls'>
									<input type="text" name="package_name" id="package_name"
										placeholder="Enter Package Name" data-rule-required='true'
										class='form-control add_pckg_elements' required>									
								</div>
							</div>
							
							
		

							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Choose  Category
								</label>
								<div class="col-md-8 nopad">
								<div class='col-sm-6 col-md-6 controls'>
								<select class='select2 form-control'  name='tour_type[]' id="tour_type" multiple >
                               
                                <?php
                               foreach($tour_type as $k => $v)
                                {
                                	echo '<option value="'.$v['id'].'">'.$v['tour_type_name'].' </option>';
                                }
                                ?>
								</select>	
											
								</div>
								<div class='col-sm-6 col-md-6 controls'>
								<select id="second_tour_type" class="form-control" name="tour_type_new[]" multiple>
								</select> 
								</div>
							</div>
							</div>
							<div class='form-group ini_banner_img hide'>
                                <label class='control-label col-sm-3' for='validation_current'>Banner Image
                                </label>
								<div class='col-sm-8 controls'>
									<img class="banner_imgs" src="/extras/custom/TMX1512291534825461/images/5eda114556.jpg" style="width:150px;height:100px;">
								</div>
                            </div>
							<div class='form-group'>
                                <label class='control-label col-sm-3' for='validation_current'>Change Banner Image
                                </label>
                                <div class='col-sm-8 controls'>
                                    <input type="file" name="banner_image" accept="image/x-jpg,image/jpeg" id="banner_image" class='form-control'>	
                                    <?=img_size_msg(200,300)?>								
                                </div>
                            </div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Package Type <span style = "color:red">*</span>
								</label>
								<div class='col-sm-8 col-md-8 controls'>
									<select class=' form-control'  name='package_type' id="package_type"  >
										<option value="">Select Package type</option>
										<option value="group">Grouped / Fixed</option>
										<option value="fit">FIT / Customised</option>
									</select>			
								</div>
							</div>

							<div class=' valid hidden multi_date_block'>
								<div class="form-group multi_frm_to_date">
									<label class='control-label col-sm-3'>Valid From <span style = "color:red">*</span></label>
									<div class='col-sm-3 controls'>
										<input type="date" placeholder="Enter Date" name="valid_frm[]" min="<?=date("Y-m-d")?>" class='form-control add_pckg_elements valid_frm' >
									</div>
									<label class='control-label col-sm-1'>Valid To <span style = "color:red">*</span></label>
									<div class='col-sm-3 controls'>
										<input type="date" placeholder="Enter Date" name="valid_to[]" min="<?=date("Y-m-d")?>" class='form-control add_pckg_elements valid_to' >
									</div>
									<div class='col-sm-2 controls'>
										<button type="button" class="btn btn-primary add_date">ADD</button>
									</div>
								</div>
								<div class="clearfix"></div>
								
							</div>
							<div class="form-group multi_date hidden">
								<label class='control-label col-sm-3'>Select Multi Dates<span style = "color:red">*</span></label>
								<div class='col-sm-6 controls multi_date_sections'>		
									<input id="multi_date" placeholder="Enter Date"  min="<?=date("Y-m-d")?>" name="multi_date" class="form-control add_pckg_elements" autocomplete="off" value="">
								</div>
							</div>





							

							<!--<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Choose Region <span style = "color:red">*</span>
								</label>
								<div class='col-sm-6 col-md-6 controls'>
								<select class='select2 form-control' data-rule-required='true' name='tours_continent' id="tours_continent" multiple data-rule-required='true' required>
                               <option value="NA">Select Region </option>
                                <?php
                                foreach($tours_continent as $tours_continent_key => $tours_continent_value)
                                {
                                	echo '<option value="'.$tours_continent_value['id'].'">'.$tours_continent_value['name'].' </option>';
                                }
                                ?>
								</select>				
								</div>
							</div>-->
							
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Choose Region <span style = "color:red">*</span>
								</label>
								<div class='col-sm-8 col-md-8 controls nopad'>
									<select class='select2 form-control'  name='tours_continent[]' id="tours_continent" multiple >
										<?php
											foreach($tours_continent as $tours_continent_key => $tours_continent_value)
											{
												echo '<option value="'.$tours_continent_value['id'].'">'.$tours_continent_value['name'].' </option>';
											}
										?>		
									</select>
								</div>
								<div class='col-sm-3 col-md-3 controls'>
								<!--<select id="third" class="form-control" name="tours_continent_new[]" multiple>
								</select> -->
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Choose Country <span style = "color:red">*</span>
								</label>
								<div class='col-sm-8 col-md-8 controls nopad'>
								<select class='select2 form-control' data-rule-required='true' name='tours_country[]' id="tours_country" multiple data-rule-required='true' required>
                              
                               
								</select>				
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Choose City <span style = "color:red">*</span>
								</label>
								<div class='col-sm-8 col-md-8 controls nopad'>
								
								<select class='select2 form-control' data-rule-required='true' name='tours_city[]' id="tours_city" multiple data-rule-required='true' required>
                                                            
								</select>
								</div>
									
							</div>		
							<!-- <div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Choose Country <span style = "color:red">*</span>
								</label>
								<div class='col-sm-6 col-md-6 controls'>
								<select class='select2 form-control' name='tours_country' id="tours_country" data-rule-required='true' required>
                                <option value="">Choose Country</option>                               
								</select>				
								</div>
							</div> -->
							<!-- <div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Choose City <span style = "color:red">*</span>
								</label>
								<div class='col-sm-6 col-md-6 controls'>
								<select class='select2 form-control' name='tours_city[]' id="tours_city" multiple data-rule-required='true' required>
                                <option value="">Choose City</option>                               
								</select>				
								</div>
							</div> -->
								
							<!--<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Choose Supplier <span style = "color:red">*</span>
								</label>
								<div class='col-sm-8 col-md-8 controls'>
									<select class='select2 form-control' data-rule-required='true' name='supplier' id="supplier" data-rule-required='true' required>          
									<option value="">Select Supplier</option> 
									</select>				
								</div>
							</div>-->
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Choose Supplier <span style = "color:red">*</span>
								</label>
								<div class='col-sm-8 col-md-8 controls nopad'>
								
								<select class='select2 form-control' data-rule-required='true' name='supplier[]' id="supplier" multiple data-rule-required='true' required>
                                                            
								</select>
								</div>
								
							</div>		
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Choose Concerned Person <span style = "color:red">*</span>
								</label>
								<div class='col-sm-8 col-md-8 controls nopad'>
								
								<select class='select2 form-control' data-rule-required='true' name='concerned_supplier[]' id="concerned_supplier" multiple data-rule-required='true' required>
                                                            
								</select>
								</div>
								
							</div>		
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Choose Duration <span style = "color:red">*</span>
								</label>
								<div class='col-sm-8 col-md-8 controls'>
									<select class='select2 form-control' data-rule-required='true' name='duration' id="duration" data-rule-required='true' required>          
									  <?php
									  for($dno=0;$dno<=30;$dno++)
									  {
									   if($dno==1) { 
										$DayNight = ($dno+1).' Days | '.($dno).' Night';
									   }else 
									   {
										$DayNight = ($dno+1).' Days | '.($dno).' Nights';
									   }
									   echo '<option value="'.$dno.'">'.$DayNight.'</option>';
									  }
									  ?>
								</select>				
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for=''>Hotels
								</label>
								<div class="hotels_block col-md-8 nopad">
									
								</div>
								<div class="clearfix"></div>
									
							</div>

							

							
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Inclusions
								</label>
								<?php 
								$inclusions_arr = json_decode($tour_data['inclusions_checks'],1);
								if(valid_array($inclusions_arr)){
									$inclusions_arr = $inclusions_arr;
								}
								else{
									$inclusions_arr = array();
								}

								?>
								<div class='col-sm-4 controls'>
									<input type="checkbox" name="inclusions[]" value="Hotel" <?=(in_array('Hotel',$inclusions_arr))? 'checked="checked"': '';?>>
									<!-- <i class="fa fa-hotel"> -->Hotel
									<br>
									<input type="checkbox" name="inclusions[]" value="Car" <?=(in_array('Car',$inclusions_arr))? 'checked="checked"': '';?>>
									<!-- <i class="fa fa-car"> -->Car
									<br>
									<input type="checkbox" name="inclusions[]" value="Meals" <?=(in_array('Meals',$inclusions_arr))? 'checked="checked"': '';?>>
									<!-- <i class="fa fa-spoon"> -->Meals
									<br>
									<input type="checkbox" name="inclusions[]" value="Sightseeing" <?=(in_array('Sightseeing',$inclusions_arr))? 'checked="checked"': '';?>>
									<!-- <i class="fa fa-binoculars"> -->Sightseeing
									<br>
									<input type="checkbox" name="inclusions[]" value="Transfers" <?=(in_array('Transfers',$inclusions_arr))? 'checked="checked"': '';?>>
									<!-- <i class="fa fa-binoculars"> -->Transfers
									<br>
									<input type="checkbox" name="inclusions[]" value="Flight" <?=(in_array('Flight',$inclusions_arr))? 'checked="checked"': '';?>>
									<!-- <i class="fa fa-binoculars"> -->Flight
									<br>
									<input type="checkbox" name="inclusions[]" value="Train" <?=(in_array('Train',$inclusions_arr))? 'checked="checked"': '';?>>
									<!-- <i class="fa fa-binoculars"> -->Train
									<br>
									<input type="checkbox" name="inclusions[]" value="Cruise" <?=(in_array('Cruise',$inclusions_arr))? 'checked="checked"': '';?>>
									<!-- <i class="fa fa-binoculars"> -->Cruise
									<br>
									<input type="checkbox" name="inclusions[]" value="Insurance" <?=(in_array('Insurance',$inclusions_arr))? 'checked="checked"': '';?>>
									<!-- <i class="fa fa-binoculars"> -->Travel Insurance
									<br>
								</div>
							</div> 
							<div class='form-group '>
								<label class='control-label col-sm-3' for='validation_current'>Choose Optional Tour 
								</label>
								<div class="col-md-8 optional_tour_block nopad">
								<!--<div class='col-sm-6 col-md-6 controls'>
								
								<select class='select2 form-control' data-rule-required='true' name='optional_tour[]' id="optional_tour" multiple data-rule-required='true' required>
                                                            
								</select>
								</div>-->	
							</div>	

							</div>		

							



							<div class='' style='margin-bottom: 0'>
								<div class='row'>
									<div class='col-sm-9 col-sm-offset-3'>								
										<button class='btn btn-primary' type="submit">Create Package</button> &nbsp;
										<a class='btn btn-primary' href="<?php echo base_url(); ?>index.php/tours/tour_list">Package List</a>
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


<script src="<?php echo base_url(); ?>assets/js/fileinput.js"></script>
<script type="text/javascript">
     $(document).ready(function()
     {
     	$('#tour_expire_date').datepicker({
     		minDate:0,
     		dateFormat:'yy-mm-dd'
     	});

    
     	var demo2 = $('select[name="theme[]"]').bootstrapDualListbox();
     	
		/*$('#theme').click(function() {
		    var options = $("#theme").find(':selected').clone();
		    $("#theme").find(':selected').remove();
		    
		    $('#second_theme').append(options);
            $("#theme option[value='"+options.val()+"']").attr('selected',true).attr("disabled",true).addClass('cstm_colr');
		        getSelectMultipleTheme(options);
		});

		$('#second_theme').click(function() {
			
			$('#theme').append($("#second_theme").find(':selected').clone());
			var options =  $("#second_theme").find(':selected').remove();
		    $("#theme option[value='"+options.val()+"']").removeAttr('disabled',false).removeClass('cstm_colr');
		    getSelectMultipleTheme(options);
		    $('#theme').html($('#theme option').sort(function(a){
				a = a.text;
				return a;
			}));
		}); */


		function getSelectMultipleTheme(options){
			$("#second_theme option[value='"+options.val()+"']").prop('selected', true);
		}

		$('#tour_type').click(function() {
		    var options = $("#tour_type").find(':selected').clone();
		    $("#tour_type").find(':selected').remove();
		    
		    $('#second_tour_type').append(options);
            $("#tour_type option[value='"+options.val()+"']").attr('selected',true).attr("disabled",true).addClass('cstm_colr');
		    // $("#theme option[value='"+options.val()+"']").remove();
		    getSelectMultipleTheme(options);
		});
		
		$('#tours_continent,#third').click(function() {
		    var options = $("#tours_continent").find(':selected').clone();
		    $("#tours_continent").find(':selected').remove();
		    
		    $('#third').append(options);
            $("#tours_continent option[value='"+options.val()+"']").attr('selected',true).attr("disabled",true).addClass('cstm_colr');
		    // $("#theme option[value='"+options.val()+"']").remove();
		    getSelectMultipleTheme(options);
		});
		$('#third').click(function() {
			
			$('#tour_type').append($("#third").find(':selected').clone());
			
			
			
		   var options =  $("#third").find(':selected').remove();
		   $("#tour_type option[value='"+options.val()+"']").removeAttr('disabled',false).removeClass('cstm_colr');
		   getSelectMultipleTheme(options);
		   $('#tour_type').html($('#tour_type option').sort(function(a){
				a = a.text;
				return a;
			}));
		});
		$('#second_tour_type').click(function() {
			
			$('#tour_type').append($("#second_tour_type").find(':selected').clone());
			
			
			
		   var options =  $("#second_tour_type").find(':selected').remove();
		   $("#tour_type option[value='"+options.val()+"']").removeAttr('disabled',false).removeClass('cstm_colr');
		   getSelectMultipleTheme(options);
		   $('#tour_type').html($('#tour_type option').sort(function(a){
				a = a.text;
				return a;
			}));
		});

		function getSelectMultipleTheme(options){
			$("#second_tour_type option[value='"+options.val()+"']").prop('selected', true);
		}






     	//var tours_country = $('select[name="tours_country[]"]').bootstrapDualListbox();
     	

     	//var demo1 = $('select[name="tour_type[]"]').bootstrapDualListbox({
					//  nonSelectedListLabel: 'Non-selected',
					 // selectedListLabel: 'Selected',
					//  preserveSelectionOnMove: false,
					 // moveOnSelect: false
				//	});

     	var tours_country = $('select[name="tours_country[]"]').bootstrapDualListbox({
			sortByInputOrder: 'true'
					 // nonSelectedListLabel: 'Non-selected',
					//  selectedListLabel: 'Selected',
					//  preserveSelectionOnMove: false,
					//  moveOnSelect: false
					});
		var tours_continent = $('select[name="tours_continent[]"]').bootstrapDualListbox({});
		var concerned_supplier = $('select[name="concerned_supplier[]"]').bootstrapDualListbox({});
		var supplier = $('select[name="supplier[]"]').bootstrapDualListbox({});
     	/*var tours_city = $('select[name="tours_city[]"]').bootstrapDualListbox({
					  nonSelectedListLabel: 'Non-selected',
					  selectedListLabel: 'Selected',
					  preserveSelectionOnMove: false,
					  moveOnSelect: false
					});*/
		var tours_city = $('select[name="tours_city[]"]').bootstrapDualListbox({
				sortByInputOrder: 'true'
			});
		var optional_tour = $('select[name="optional_tour[]"]').bootstrapDualListbox({});
     	$('#tours_city').click(function() {
		    var options = $("#tours_city").find(':selected').clone();
		    $('#second').append(options);
		    getSelectMultiple();
		});
		
		$('#second').click(function() {
		   $("#second").find(':selected').remove();
		   getSelectMultiple();
		});

		function getSelectMultiple(){
			$("#second option").prop('selected', true);
		}


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
	         	var tours_country_list = tours_countries.split(',');
	         }

         	$.each(tours_country_list, function(index, item) {

			    // do something with `item` (or `this` is also `item` if you like)

		        $.post('<?=base_url();?>tours/ajax_tours_country',{'tours_country':item},function(data)
		        {
		        	if(index>0){
			            $('#tours_city').append(data);
			            $('#tours_city').bootstrapDualListbox('refresh', true);
			        }else{			        	
			            $('#tours_city').html(data);
			            $('#tours_city').bootstrapDualListbox('refresh', true);
			        }
		        });
				$.post('<?=base_url();?>tours/ajax_tours_supplier',{'tours_country':item},function(data)
		        {
		        	if(index>0){
			            $('#supplier').append(data);
			            $('#supplier').bootstrapDualListbox('refresh', true);
			        }else{			        	
			            $('#supplier').html(data);
			            $('#supplier').bootstrapDualListbox('refresh', true);
			        }
		        });
				
	        });
		});
		var prev_city='';
		$(document).on('change','#tours_city', function() { 
           
         	var tours_city = $('#tours_city').val();
			
         	if(tours_city==null){
         		//$('#tours_city').html('');
				var tours_city =[];
         	}
			//console.log(tours_city.length);
			
         	if(tours_city.length > 0 ){
				var tours_country_list = tours_city;
         	}else{
	         	var tours_country_list = Array.from(tours_city);
	        }
			var duration=parseInt($('#duration').val());
			//alert(tours_country_list);
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
			
			if(duration==0){
				tot_nyt=-1;
			}
			
			var i;
			var prev_len=prev_city.length;
			
			if(prev_city!=''){
				//alert(prev_city);
				if(prev_len>1){
					var prev_len_arr=Array.from(prev_city);
				}
				var now_len = tours_country_list.length;
			}
			if(now_len < prev_len){
				for (i = 0; i< prev_len; i++) {
					
					if(jQuery.inArray(prev_len_arr[i], tours_city) !== -1){
						//alert("there")
					}else{
						//alert(prev_len_arr[i]);
						$(".city_ref_"+prev_len_arr[i]).remove();
					}
				}
			}else{
			
				for (i = 0; i< tours_country_list.length; i++) {
					if(jQuery.inArray(tours_country_list[i], prev_city) !== -1){
						//alert("there")
					}else{
						$.post('<?=base_url();?>tours/ajax_tours_hotels',{'tours_city':tours_country_list[i]},function(data)
						{
							if(tot_nyt<duration) 
							{
								if(i>0){
									$('.hotels_block').append(data);
									$('.hotels_block').bootstrapDualListbox('refresh', true);
								}else{			        	
									$('.hotels_block').html(data);
									$('.hotels_block').bootstrapDualListbox('refresh', true);
								}
							}else{
								 $msg = duration+' Nights / '+(duration+1)+' Days';
								 alert('Sorry! This Holiday Package is designed for '+$msg+'. You are exceeding the limit.');
								
							}
						});
						$.post('<?=base_url();?>tours/ajax_optional_tours',{'tours_city':tours_country_list[i]},function(data)
						{
							if(i>0){
								$('.optional_tour_block').append(data);
							   var optional_tour = $('select[name="optional_tour[]"]').bootstrapDualListbox({});
							}else{			        	
								$('.optional_tour_block').html(data);
								var optional_tour = $('select[name="optional_tour[]"]').bootstrapDualListbox({});
							}
						});
					}
				}
				
			}
			prev_city=tours_city;
			//alert(prev_city);
			
			
			
			
         	/*$.each(tours_country_list, function(index, item) {

			    // do something with `item` (or `this` is also `item` if you like)
				$.post('<?=base_url();?>tours/ajax_tours_hotels',{'tours_city':item},function(data)
		        {
					if(tot_nyt<duration)
					{
						if(index>0){
							$('.hotels_block').append(data);
							$('.hotels_block').bootstrapDualListbox('refresh', true);
						}else{			        	
							$('.hotels_block').html(data);
							$('.hotels_block').bootstrapDualListbox('refresh', true);
						}
					}else{
						 $msg = duration+' Nights / '+(duration+1)+' Days';
						 alert('Sorry! This Holiday Package is designed for '+$msg+'. You are exceeding the limit.');
						
					}
		        });
				//$.post('<?=base_url();?>tours/ajax_optional_toursss',{'tours_city':item},function(data)
		      //  {
		        	//if(index>0){
			        //    $('#optional_tour').append(data);
			        //    $('#optional_tour').bootstrapDualListbox('refresh', true);
			       // }else{			        	
			        //    $('#optional_tour').html(data);
			        //    $('#optional_tour').bootstrapDualListbox('refresh', true);
			       // }
		       // });
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
	        });*/
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
	 
	
	  
	
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 //sanchitha
	 
	
	$(document).on('change','.valid_frm',function(){
     	var start_date= $(this).val();
     	 //	alert(start_date);
    	$(this).parents('.multi_frm_to_date ').find('.valid_to').attr('min', start_date);
     	 	
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
	$(document).on('click','.add_hotel',function(){
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
		if(duration==0){
			tot_nyt=-1;
		}
		if(tot_nyt<duration)
        {
			
			var generate_text=$(this).parents('.controls').find('.this_city_hotel').html();	
			$(this).parents('.add_city_hotel_here').append('<div class="this_city_hotel"><a class="remove_hotel btn pull-right">Remove <i class="fa fa-minus" aria-hidden="true"></i></a><br/>'+generate_text+'</div>');	
		}else{
			$msg = duration+' Nights / '+(duration+1)+' Days';
             alert('Sorry! This Holiday Package is designed for '+$msg+'. You are exceeding the limit.');
             return false; 
		}
	
	
	});
	$(document).on('click','.remove_hotel',function(){
		$(this).parents('.this_city_hotel').remove();
	});
	
	$(document).on('click','.add_date',function(){
		var generate_date_text='<div class="multi_frm_to_date form-group"><label class="control-label col-sm-3">Valid From <span style = "color:red">*</span></label><div class="col-sm-3 controls"><input type="date" placeholder="Enter Date" name="valid_frm[]" min="<?=date("Y-m-d")?>" class="form-control add_pckg_elements valid_frm"></div><label class="control-label col-sm-1">Valid To <span style = "color:red">*</span></label><div class="col-sm-3 controls"><input type="date" placeholder="Enter Date" name="valid_to[]" min="<?=date("Y-m-d")?>" class="form-control add_pckg_elements valid_to"></div><div class="col-sm-2 controls"><button type="button" class="btn btn-primary remove_date">Remove</button></div></div>';
		
		$('.multi_date_block').append(generate_date_text);
		
	});
	$(document).on('click','.remove_date',function(){
		
		$(this).parents('.multi_frm_to_date').remove();
		
	});
	
	$(document).on('change','#duration',function(){
		var duration=$(this).val();
		//alert(duration);
		var tot_nyt=0;
		$.each($('.htl_no_of_nyt'), function() {
			var sel_nyt_val=$(this).val();
			if(sel_nyt_val!=''){
				tot_nyt+=sel_nyt_val;
			}
		});
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
	
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js"></script> 
<script src="<?php echo $GLOBALS['CI']->template->template_js_dir('multidate_init.js'); ?>"></script> 
 
<!--<script src="<?=JAVASCRIPT_LIBRARY_DIR?>common.js" type="text/javascript"></script>
<script	src="<?=SYSTEM_RESOURCE_LIBRARY?>/validate/jquery.validate.min.js" type="text/javascript"></script>
<script	src="<?=SYSTEM_RESOURCE_LIBRARY?>/validate/additional-methods.js" type="text/javascript"></script>
<script src="<?=SYSTEM_RESOURCE_LIBRARY?>/validate/custom.js"></script>-->