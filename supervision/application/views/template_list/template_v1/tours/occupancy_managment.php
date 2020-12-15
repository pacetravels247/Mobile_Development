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
						data-toggle="tab">Occupancy Management</a></li>
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
				action="<?php echo base_url(); ?>index.php/tours/save_occupancy"
				method="post" enctype="multipart/form-data" id="form form-horizontal validate-form"
				class='form form-horizontal validate-form' style="display:none;">
				<div class="tab-content">
					<!-- Add Package Starts -->
					<div role="tabpanel" class="tab-pane active" id="add_package">
						   <div class="col-md-12">
						   		
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Occupancy Name
								</label>
								<div class='col-sm-8 controls'>
									<input type="text" name="occupancy_name" id="occupancy_name"
										placeholder="Occupancy Name" data-rule-required='true'
										class='form-control' required>									
								</div>
							</div>
									
											
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
					<th>Occupancy</th>
						
		            <th>Action</th>				
				</tr>
		    </thead>
		    <tbody>		
				<?php
        $sn = 1;
     //  debug($occupancy_details);exit;
        foreach ($occupancy_details as $key => $data) {
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
              
              <td>'.$data['occupancy_name'].'</td>';
			  
        //echo '<td>'.$galleryImages.'</td>';
        echo '<td>'.$status.'</td>';
      
        if($data['status']==1)
               {
               echo '<td>
                     <a class="" data-placement="top" href="'.base_url().'index.php/tours/activation_occ/'.$data['id'].'/0"
                     data-original-title="Deactivate Tour Destination"> <i class="glyphicon glyphicon-th-large"></i></i> De-activate
                     </a>
                     </td>';
               }
               else
               {
               echo '<td>
                     <a class="" data-placement="top" href="'.base_url().'index.php/tours/activation_occ/'.$data['id'].'/1"
                     data-original-title="Activate Tour Destination"> <i class="glyphicon glyphicon-th-large"></i></i> Activate
                     </a>
                     </td>';
               }  
           
        echo '<td class="center">
               <a class="" data-placement="top" href="'.base_url().'index.php/tours/edit_occ_management/'.$data['id'].'"
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
		        if($response==true){ window.location='<?=base_url()?>index.php/tours/delete_occupancy_managment/'+$id; } else{}
           });
		   $(document).on('click','.form_subm',function(e){
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
<!--script type="text/javascript" src="<?=$airliners_weburl?>extras/system/template_list/template_v1/javascript/js/tiny_mce/tiny_mce.js"></script-->
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