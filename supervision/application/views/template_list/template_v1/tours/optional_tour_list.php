<style>
	.ad_pkg .col-sm-1 { padding-right: 0px !important;}
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
						data-toggle="tab"> Optional Tour List </a></li>
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
				action="<?php echo base_url(); ?>index.php/tours/optional_tour_list_save"
				method="post" enctype="multipart/form-data" id="form form-horizontal validate-form"
				class='form form-horizontal validate-form' style="display:none;">
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
								   echo '<option value="'.$v['id'].'">'.$v['name'].' </option>';
								  }
								  ?>
								 </select>				
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3'>Choose City
								</label>
								<div class='col-sm-8 '>
									<select class='form-control js-example-basic-single2'  name='tour_city' id="tour_city" data-rule-required='true' required>
								  
									</select>									
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Tour Name
								</label>
								<div class='col-sm-8 controls'>
									<input type="text" name="tour_name" id="tour_name"
										placeholder="" data-rule-required='true'
										class='form-control' required>									
								</div>
							</div>
							<div class='form-group ad_pkg'>
								<label class='control-label col-sm-3' for='validation_current'>Adult Price
								</label>
								<div class='col-sm-2 controls'>
									<input type="number" name="adult_price" id="adult_price"
										placeholder="" min="0" data-rule-required='true'
										class='form-control' required>									
								</div>

								<label class='control-label col-sm-1' for='validation_current'>Child Price
								</label>
								<div class='col-sm-2 controls'>
									<input type="number" name="child_price" id="child_price"
										placeholder="" min="0" data-rule-required='true'
										class='form-control' required>	 								
								</div>
								<label class='control-label col-sm-1' for='validation_current'>Infant Price
								</label>
								<div class='col-sm-2 controls'>
									<input type="number" name="infant_price" id="infant_price"
										placeholder="" min="0" data-rule-required='true'
										class='form-control' required>									
								</div>

							</div>
							<!-- <div class='form-group'>
								
							</div>
							<div class='form-group'>
								
							</div>	 -->						
							<div class='' style='margin-bottom: 0'>
								<div class='row'>
									<div class='col-sm-9 col-sm-offset-3'>								
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
					<th>Tour Name </th>
					<th>Country</th>
					<th>City</th>
					<th>Status</th>	
		            <th>Status Change</th>	
		            <th>Action</th>				
				</tr>
		    </thead>
		    <tbody>		
				<?php
        $sn = 1;
        //debug($tour_destinations);
        foreach ($trip_note_list as $key => $data) {
        if($data['status']==1)
        {
           $status = '<span style="color:green;">Active</span>';
        }
        else
        {
           $status = '<span style="color:red;">In-Active</span>';
        }
        echo '<tr>
              <td>'.$sn.'</td>
              <td>'.$data['tour_name'].'</td>
			  <td>'.$data['name'].'</td>
			  <td>'.$data['cityname'].'</td>
			  ';
        //echo '<td>'.$galleryImages.'</td>';
        echo '<td>'.$status.'</td>';
        if($data['status']==1)
        {
        echo '<td>
              <a class="" data-placement="top" href="'.base_url().'index.php/tours/activation_optional_tour/'.$data['id'].'/0"
              data-original-title="Deactivate Tour Destination"> <i class="glyphicon glyphicon-th-large"></i></i> De-activate
              </a>
              </td>';
        }
        else
        {
        echo '<td>
              <a class="" data-placement="top" href="'.base_url().'index.php/tours/activation_optional_tour/'.$data['id'].'/1"
              data-original-title="Activate Tour Destination"> <i class="glyphicon glyphicon-th-large"></i></i> Activate
              </a>
              </td>';
        }      
        echo '<td class="center">
              <a class="" data-placement="top" href="'.base_url().'index.php/tours/edit_optional_tour_data/'.$data['id'].'"
              data-original-title="Edit Hotel"> <i class="glyphicon glyphicon-pencil"></i> Edit
              </a> 
              <a class="callDelete" id="'.$data['id'].'"> 
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
		<script type="text/javascript">  
		$(document).ready(function()
		{
           
          $(".callDelete").click(function() { 
            $id = $(this).attr('id'); //alert($id);
		        $response = confirm("Are you sure to delete this record???");
		        if($response==true){ window.location='<?=base_url()?>index.php/tours/delete_optional_tour/'+$id; } else{}
           });
			$("#tour_country").change(function() {
				var selections =$(this).val();
			
				$.ajax({
					url: '<?php echo base_url(); ?>index.php/tours/get_city_name/',
					type:'POST',
					data:{'tour_country':selections},
					success: function (data, textStatus, jqXHR) {                                   
					  
					 $('#tour_city').html('');
					  $('#tour_city').html(data);
					
					}
			   }); 
			if(selections!=''){
					$('.select2-selection').removeClass('invalid-ip');
				}
		});
		$(document).on('click','.form_subm',function(e){
			var country_name=$('#tour_country').val();
			var city_name=$('#tour_city').val();
			
			
			if($('input').hasClass('invalid-ip')){
				if(country_name==''){
					$('.select2-selection').addClass('invalid-ip');
				}
				alert("Please enter all the fields");
			}
			
			//alert(country_name);
			
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