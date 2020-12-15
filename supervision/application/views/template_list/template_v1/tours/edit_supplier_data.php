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
                            data-toggle="tab">Edit Supplier Details </a></li>			
                </ul>
            </div>
        </div>
        <!-- PANEL HEAD START -->
        <div class="panel-body">
            <!-- PANEL BODY START -->
            <form
                action="<?php echo base_url(); ?>index.php/tours/edit_supplier_save"
                method="post" enctype="multipart/form-data" id="form form-horizontal validate-form"
                class='form form-horizontal validate-form'>
                <div class="tab-content">
			
                    <!-- Add Package Starts -->
                    <div role="tabpanel" class="tab-pane active" id="add_package">
                        <div class="col-md-12">
							
							<div class='form-group'>
								<label class='control-label col-sm-3'>Choose Country
								</label>
								<div class='col-sm-8 '>        
								 <select class='form-control js-example-basic-single'  name='tour_country' id="tour_country" data-rule-required='true' required>
								  <option value="">Choose Country</option>
								  <?php
								  foreach($tour_country as $k => $v)
								  {
										if($supplier_details[0]['country']==$v['id']){
											$select="selected";
										}else{
											$select="";
										}
								   echo '<option value="'.$v['id'].'" '.$select.'>'.$v['name'].' </option>';
								  }
								  ?>
								 </select>				
								</div>
							</div>
							
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Supplier Name
								</label>
								<div class='col-sm-8 controls'>
									<input type="text" name="supplier_name" id="supplier_name"
										placeholder="" data-rule-required='true'
										class='form-control' value="<?=$supplier_details[0]['supplier_name']?>" required>									
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Office Contact Number
								</label>
								<div class='col-sm-8 controls'>
									<input type="number" name="office_contact_number" id="office_contact_number"
										placeholder="" data-rule-required='true'
										class='form-control' value="<?=$supplier_details[0]['office_contact_number']?>" required>									
								</div>
							</div>	
							<div class='form-group'>
								<h2 class="col-sm-6">Contact Persons</h2><a class="add_pax col-sm-6">Add</a>
							</div>	
							<div class="contact_block">
								<?php 
									foreach($supplier_details as $c_key =>$c_val){
								?>		
									<div class="each_contact_block">
										<div class='col-sm-3 controls'>
											<label class='control-label col-sm-4' for='validation_current'>Contact Person</label>
											<input type="text" name="contact_person[]" id="contact_person"
												placeholder="" data-rule-required='true'
												class='form-control' value="<?=$supplier_details[$c_key]['contact_person']?>" required>									
										</div>
										<div class='col-sm-3 controls'>
											<label class='control-label col-sm-3' for='validation_current'>Email</label>
											<input type="email" name="email[]" id="email"
												placeholder="" data-rule-required='true'
												class='form-control' value="<?=$supplier_details[$c_key]['email']?>" required>									
										</div>
										<div class='col-sm-3 controls'>
											<label class='control-label col-sm-3' for='validation_current'>Phone</label>
											<input type="phone" name="phone[]" id="phone"
												placeholder="" data-rule-required='true'
												class='form-control'  value="<?=$supplier_details[$c_key]['phone']?>" required>									
										</div>
										<div class='col-sm-3 controls'>
											<a class='btn remove_contact text-danger'><span class="fa fa-minus-circle"></span></a>							
										</div>
									</div><div class="clearfix"></div>
								<?php		
									}
								?>
								
							</div>	
                            <div class='' style='margin-bottom: 0'>
                                <div class='row'>
                                    <div class='col-sm-9 col-sm-offset-3'>	
                                       <input type="hidden" name="s_id" value="<?=$supplier_details[0]['s_id']?>">				
                                        <button class='btn btn-primary form_subm' type='submit'>Update</button>
                                        <a href="<?php echo base_url(); ?>index.php/tours/supplier_list" class='btn btn-primary' style="color:white;">Cancel</a>

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
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script>
$( document ).ready(function (){
	
	$('.js-example-basic-single').select2();
				//$('.js-example-basic-single2').select2();
});
</script>
<script type="text/javascript">  
		$(document).ready(function()
		{
			$('.add_pax').click(function(){
				var generate_text="<div class='each_contact_block'><div class='col-sm-3 controls'><label class='control-label col-sm-4' for='validation_current'>Contact Person</label><input type='text' name='contact_person[]' id='contact_person' placeholder='' data-rule-required='true' class='form-control' required></div><div class='col-sm-3 controls'><label class='control-label col-sm-3' for='validation_current'>Email</label><input type='email' name='email[]' id='email' placeholder='' data-rule-required='true' class='form-control' required></div><div class='col-sm-3 controls'><label class='control-label col-sm-3' for='validation_current'>Phone</label><input type='phone' name='phone[]' id='phone' placeholder='' data-rule-required='true' class='form-control' required></div><div class='col-sm-3 controls'><a class='btn remove_contact text-danger'><span class='fa fa-minus-circle'></span></a></div></div><div class='clearfix'></div>";
				
				$('.contact_block').append(generate_text);
			});
			$(document).on('click','.remove_contact',function(){
				$(this).parents('.each_contact_block').remove();
			});
			$("#tour_country").change(function() {
				var selections =$(this).val();
			
				if(selections!=''){
					$('.select2-selection').removeClass('invalid-ip');
				}
			});
			$(document).on('click','.form_subm',function(e){
				var country_name=$('#tour_country').val();
				if($('#tour_country').hasClass('invalid-ip')){
					$('.select2-selection').addClass('invalid-ip');
				}
				
				if($('input').hasClass('invalid-ip')){
					alert("Please enter all the fields");
				}
			});
		});
		
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
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script>
$( document ).ready(function (){
	
	$('.js-example-basic-single').select2();
				$('.js-example-basic-single2').select2();
});
</script>
<!--
<script type="text/javascript" src="/chariot/extras/system/template_list/template_v1/javascript/js/nicEdit-latest.js"></script> 
<script type="text/javascript">
bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
</script>-->
<style type="text/css">
	.each_contact_block .control-label {
    white-space: nowrap;
    padding-left: 0px;
}
.contact_block {
    margin-bottom: 20px;
}
a.add_pax.col-sm-6 {
    width: 100px;
    background: #367fa9;
    text-align: center;
    padding: 5px 10px;
    margin-top: 30px;
    color: #fff;
    border-radius: 5px;
    float: left;
    display: block;
}
a.btn.remove_contact.text-danger {
    margin: 25px 0px;
    font-size: 20px;
}
</style>