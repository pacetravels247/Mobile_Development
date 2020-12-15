<style>
	.each_contact_block .control-label{
		text-align: left;
		padding-left: 0;
	}
	.contact_block {
		margin: 0 -25px;
	}
	.each_contact_block:after{
		content: '';
		clear: both;
		display: table;
	}
	.contact_block-save{
		margin: 15px -10px 0;
	}
	/* Chrome, Safari, Edge, Opera */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Firefox */
input[type=number] {
  -moz-appearance: textfield;
}
</style>

<?php error_reporting(0);?>
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
						data-toggle="tab">Supplier List </a></li>
			        <li aria-controls="home"> &nbsp;&nbsp;
					<button class='btn btn-primary' onclick="$('.form').slideToggle();">Add</button>
				    </li>			
					
				</ul>
			</div>
		</div>
		<!-- PANEL HEAD START -->
		<div class="panel-body">
			<!-- PANEL BODY START -->
			<form
				action="<?php echo base_url(); ?>index.php/tours/supplier_data_save"
				method="post" enctype="multipart/form-data" id="form form-horizontal validate-form"
				class='form form-horizontal validate-form' style="display:none;">
				<div class="tab-content">
					<!-- Add Package Starts -->
					<div role="tabpanel" class="tab-pane active" id="add_package">
						   <div class="col-md-12">
							
							<div class='form-group'>
								<label class='control-label col-sm-3'>Choose Country
								</label>
								<div class='col-sm-5'>        
								 <select class='form-control js-example-basic-single'  name='tour_country' id="tour_country" data-rule-required='true' required>
								  <option value="">Choose Country</option>
								  <?php
								  foreach($tour_country as $k => $v)
								  {
								   echo '<option value="'.$v['id'].'">'.$v['name'].' </option>';
								  }
								  ?>
								 </select>				
								</div>
							</div>
							
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Supplier Name
								</label>
								<div class='col-sm-5 controls'>
									<input type="text" name="supplier_name" id="supplier_name"
										placeholder="" data-rule-required='true'
										class='form-control' maxlength="40" required>									
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Office Contact Number
								</label>
								<div class='col-sm-5 controls'>
									<input type="text" name="office_contact_number" id="office_contact_number"
										placeholder="" data-rule-required='true'
										class='form-control'  minlength="10" maxlength="20" required>									
								</div>
							</div>	
							<div class="form-group"><div class="col-sm-9 controls"><h2 class="form_legend">Contact Persons</h2>
                                                     </div>


<div class="col-sm-3 controls"><a class="add_pax btn btn-info btn-sm" style="
    margin-top: 20px;
    margin-bottom: 10px;
">Add</a></div></div>	
							<div class="contact_block">
								<div class="each_contact_block">
									<div class='col-sm-3 controls'>
										<label class='control-label col-sm-6' for='validation_current'>Contact Person</label>
										<input type="text" name="contact_person[]" id="contact_person"
											placeholder="" data-rule-required='true'
											class='form-control' maxlength="40" required>									
									</div>
									<div class='col-sm-3 controls'>
										<label class='control-label col-sm-6' for='validation_current'>Email</label>
										<input type="email" name="email[]" id="email"
											placeholder="" data-rule-required='true'
											class='form-control' required>									
									</div>
									<div class='col-sm-3 controls'>
										<label class='control-label col-sm-6' for='validation_current'>Phone</label>
										<input type="text" name="phone[]" id="phone"
											placeholder="" data-rule-required='true'
											class='form-control' minlength="10" maxlength="20" required>									
									</div>
									<div class='col-sm-3 controls'>
										<label class='control-label col-sm-12'>&nbsp;</label>
										<a class='btn btn-warning btn-sm remove_contact'>Remove</a>							
									</div>
								</div><div class="clearfix"></div>
							</div>
							<div class='contact_block-save'>
								<div class='row'>
									<div class='col-sm-9 nopad'>								
										<button class='btn btn-primary form_subm' type='submit'>Save</button>
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
	
			<div class="table-responsive scroll_main" style="overflow-hidden; overflow-x:scroll;">
			<table class="table table-bordered">
			<thead>
				<tr>
					<th>Sl.No</th>
					<th>Supplier Name </th>
					<th>Country</th>
					<th>Status </th>	
		            <th>Status Change</th>	
		            <th>Action</th>				
				</tr>
		    </thead>
		    <tbody>		
				<?php
        $sn = 1;
       // debug($supplier_list);
        foreach ($supplier_list as $key => $data) {
        if($data['s_status']==1)
        {
           $status = '<span style="color:green;">Active</span>';
        }
        else
        {
           $status = '<span style="color:red;">In-Active</span>';
        }
        echo '<tr>
              <td>'.$sn.'</td>
              <td>'.$data['supplier_name'].'</td>
			  <td>'.$data['name'].'</td>';
        //echo '<td>'.$galleryImages.'</td>';
        echo '<td>'.$status.'</td>';
        if($data['s_status']==1)
        {
        echo '<td>
              <a class="" data-placement="top" href="'.base_url().'index.php/tours/activation_supplier/'.$data['s_id'].'/0"
              data-original-title="Deactivate Tour Destination"> <i class="glyphicon glyphicon-th-large"></i></i> De-activate
              </a>
              </td>';
        }
        else
        {
        echo '<td>
              <a class="" data-placement="top" href="'.base_url().'index.php/tours/activation_supplier/'.$data['s_id'].'/1"
              data-original-title="Activate Tour Destination"> <i class="glyphicon glyphicon-th-large"></i></i> Activate
              </a>
              </td>';
        }      
        echo '<td class="center">
              <a class="" data-placement="top" href="'.base_url().'index.php/tours/edit_supplier_data/'.$data['s_id'].'"
              data-original-title="Edit Hotel"> <i class="glyphicon glyphicon-pencil"></i> Edit
              </a> 
              <a class="callDelete" id="'.$data['s_id'].'"> 
              <i class="glyphicon glyphicon-trash"></i> Delete</a>
              </td>
              </tr>';
        $sn++;
        }
        ?>
		</tbody>
		</table>
		</div>				
		</div>
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
           
          $(".callDelete").click(function() { 
            $id = $(this).attr('id'); //alert($id);
		        $response = confirm("Are you sure to delete this record???");
		        if($response==true){ window.location='<?=base_url()?>index.php/tours/delete_supplier/'+$id; } else{}
           });
			
			$(document).on('click','.add_pax',function(){
				var generate_text="<div class='each_contact_block'><div class='col-sm-3 controls'><label class='control-label col-sm-12' for='validation_current'>Contact Person</label><input type='text' name='contact_person[]'  maxlength='40' id='contact_person' placeholder='' data-rule-required='true' class='form-control' required></div><div class='col-sm-3 controls'><label class='control-label col-sm-3' for='validation_current'>Email</label><input type='email' name='email[]' id='email' placeholder='' data-rule-required='true' class='form-control' required></div><div class='col-sm-3 controls'><label class='control-label col-sm-3' for='validation_current'>Phone</label><input type='text' name='phone[]' id='phone' minlength='10' maxlength='20' placeholder='' data-rule-required='true' class='form-control' required></div><div class='col-sm-3 controls'><label class='control-label col-sm-12'>&nbsp;</label><a class='btn btn-warning btn-sm remove_contact'>Remove</a></div></div><div class='clearfix'></div>";
				
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
<!--
<script src="/chariot/extras/system/template_list/template_v1/javascript/js/nicEdit/nicEdit.js"></script>
<script type="text/javascript" src="/chariot/extras/system/template_list/template_v1/javascript/js/nicEdit/nicEdit_call.js"></script>

<link rel="stylesheet" href="/chariot/extras/system/template_list/template_v1/javascript/js/datatables/tables.css">
<script src="/chariot/extras/system/template_list/template_v1/javascript/js/datatables/jquery.dataTables.js"></script>
<script src="/chariot/extras/system/template_list/template_v1/javascript/js/datatables/jquery.dataTables.min.js"></script>
<script type="text/javascript">
            $(document).ready(function() {
                $('.table').dataTable();	
            });
</script>
-->

<!-- <script type="text/javascript" src="/chariot/extras/system/template_list/template_v1/javascript/js/nicEdit-latest.js"></script> 
<script type="text/javascript">
//<![CDATA[
bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
//]]>
</script> -->

<link rel="stylesheet" type="text/css" href="http://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">
<script type="text/javascript" src="http://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
<script> $(function () { $('.table').DataTable(); }); </script>