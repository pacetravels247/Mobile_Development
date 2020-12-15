<?php error_reporting(0);?>
<script src="/airliners/extras/system/library/ckeditor/ckeditor.js"></script>
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
						data-toggle="tab"> Departure Date List : [ <?php echo 'Package Name : '.string_replace_encode($tour_data['package_name']);?>]</a></li>				
				</ul>
			</div>
		</div>
		<!-- PANEL HEAD START -->
		<div class="panel-body">
			<!-- PANEL BODY START -->
		<form
				action="<?php echo base_url(); ?>index.php/tours/tour_dep_date_save"
				method="post" enctype="multipart/form-data" id="form form-horizontal validate-form"
				class='form form-horizontal validate-form'>
				<div class="tab-content">
					<!-- Add Package Starts -->
					<div role="tabpanel" class="tab-pane active" id="add_package">
						    <div class="col-md-12">
							<input type="hidden" value="<?=$tour_data['package_type']?>" name="package_type">
							<input type="hidden" value="<?=$list?>" name="list" class="list">
							<div class='form-group'> 
								
								<?php
								// debug($tour_data);
									if($tour_data['package_type']=='fit'){
								?>	
								<div class="multi_date_block">
									<div class="form-group multi_frm_to_date">
										<label class='control-label col-sm-3'>Valid From <span style = "color:red">*</span></label>
										<div class='col-sm-3 controls'>
											<input type="date" placeholder="Enter Date" name="valid_frm[]" class='form-control add_pckg_elements valid_frm' required>
										</div>
										<label class='control-label col-sm-1'>Valid To <span style = "color:red">*</span></label>
										<div class='col-sm-3 controls'>
											<input type="date" placeholder="Enter Date" name="valid_to[]" class='form-control add_pckg_elements valid_to' required>
										</div>
										<div class='col-sm-2 controls'>
											<button type="button" class="btn btn-primary add_date">ADD</button>
										</div>
									</div>
								</div>
								<?php 	}else{
								?>
								<div class="form-group multi_date ">
									<label class='control-label col-sm-3'>Select Multi Dates<span style = "color:red">*</span></label>
									<div class='col-sm-6 controls multi_date_sections'>		<input id="multi_date" required placeholder="Enter Date" name="multi_date" class="form-control add_pckg_elements" value="">
									</div>
								</div>
								<?php	}
								?>
							<!--	<div class='col-sm-4 controls'>
								<input type="text" name="tour_dep_date" id="tour_dep_date" class="form-control" value="" placeholder="Choose Date" data-rule-required='true' required readonly> 
								</div>-->
							</div>
															
							<div class='' style='margin-bottom: 0'>
								<div class='row'>
									<div class='col-sm-9 col-sm-offset-3'>	
									    <input type="hidden" name="tour_id" value="<?=$tour_id?>">							
										<button class='btn btn-primary' type='submit'>Save</button>
										<?php 
											if($list=='published_tour_list'){
												$button_text = "Go Back to publish List";
											}else if($list=='verify_tour_list'){
												$button_text = "Go Back to Verify List";
											}else if($list=='tour_list'){
												$button_text = "Go Back to Holiday List";
											}else{
												$button_text = "Go Back to Draft List";
											}
										
										?>
										<a href="<?php echo base_url(); ?>index.php/tours/<?=$list?>" style="color:white;" class="btn btn-primary"><?=$button_text?></a>
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
			
			<table class="table table-bordered">
			<thead>
				<tr>
					<th>SN</th>
					<?php if($tour_data['package_type']=='fit'){ ?>
					<th>From Date [dd-mm-yy]</th>
					<th>To Date [dd-mm-yy]</th>
					<?php }else{ ?>
					<th>Departure Date [dd-mm-yy]</th>
					<?php } ?>
					<th>Action</th>				
				</tr>
			</thead>
			<tbody>	
			<?php
        $sn = 1;
        //debug($tour_destinations);
        foreach ($tour_dep_dates as $key => $data) {       
        echo '<tr>
              <td>'.$sn.'</td>';                  
             
		if($tour_data['package_type']=='fit'){
			 echo '<td>'.changeDateFormatDMY($data['valid_from']).'</td>
					<td>'.changeDateFormatDMY($data['valid_to']).'</td>';
		}else{
             echo '<td>'.changeDateFormatDMY($data['dep_date']).'</td>';
		}
        echo '<td class="center">              
              <!--<a class="" data-placement="top" href="'.base_url().'index.php/tours/delete_tour_dep_date/'.$data['id'].'/'.$tour_id.'"
              data-original-title="Delete Tour Destination"> <i class="glyphicon glyphicon-trash"></i></i> Delete
              </a>-->
              <a class="callDelete" id="'.$data['id'].'" tour-type="'.$tour_data['package_type'].'" tour-id="'.$tour_id.'"> 
              <i class="glyphicon glyphicon-trash"></i> Delete</a>
              </td>
              </tr>';
              /*<a class="" data-placement="top" href="'.base_url().'tours/edit_tour_dep_date/'.$data['id'].'/'.$tour_id.'"
              data-original-title="Edit Tour Destination"> <i class="glyphicon glyphicon-pencil"></i> Edit
              </a>*/
        $sn++;
        }
        ?>
		</tbody>
		</table>				
		</div>
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
  
<script type="text/javascript">  
		$(document).ready(function()
		{
			$('#multi_date').datepicker({
			multidate: true,
			format: 'dd-mm-yyyy'
			});
			
            $(".callDelete").click(function() { 
            $id = $(this).attr('id'); //alert($id);
            $tour_id 	= $(this).attr('tour-id'); //alert($id);
			$tour_type 	= $(this).attr('tour-type'); //alert($id);
			$list 	= $('.list').val(); //alert($id);
		    $response 	= confirm("Are you sure to delete this record?");
		    if($response==true){ window.location='<?=base_url()?>index.php/tours/delete_tour_dep_date/'+$id+'/'+$tour_id+'/'+$tour_type+'/'+$list; } else{}
           });
		   
			$(document).on('click','.add_date',function(){
				var generate_date_text='<div class="multi_frm_to_date form-group"><label class="control-label col-sm-3">Valid From <span style = "color:red">*</span></label><div class="col-sm-3 controls"><input type="date" placeholder="Enter Date" name="valid_frm[]" required class="form-control add_pckg_elements valid_frm"></div><label class="control-label col-sm-1">Valid To <span style = "color:red">*</span></label><div class="col-sm-3 controls"><input type="date" placeholder="Enter Date" name="valid_to[]" required class="form-control add_pckg_elements valid_to"></div><div class="col-sm-2 controls"><button type="button" class="btn btn-primary remove_date">Remove</button></div></div>';
				
				$('.multi_date_block').append(generate_date_text);
				
			});
			$(document).on('click','.remove_date',function(){
				
				$(this).parents('.multi_frm_to_date').remove();
				
			});
			$(document).on('change','.valid_frm',function(){
				var start_date= $(this).val();
				 //	alert(start_date);
				$(this).parents('.multi_frm_to_date ').find('.valid_to').attr('min', start_date);
					
			});
		});
        </script>
<link rel="stylesheet" type="text/css" href="http://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">
<script type="text/javascript" src="http://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
<script> $(function () { $('.table').DataTable(); }); </script>                
