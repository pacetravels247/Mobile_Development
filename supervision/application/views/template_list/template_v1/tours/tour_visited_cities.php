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
						data-toggle="tab"> Visited City List : [ <?php echo 'Package Name : '.string_replace_encode($tour_data['package_name']);?>]</a></li>
			        <li aria-controls="home"> &nbsp;&nbsp;
					<!-- <button class='btn btn-primary' onclick="$('.form').slideToggle();">Add City</button> -->
					<!-- <button class='btn btn-primary'><a href="<?php echo base_url(); ?>index.php/tours/tour_list" style="color:white;">Holiday List</a></button> -->
				    <button onclick="location.href='<?php echo base_url(); ?>index.php/tours/tour_list';" class="btn btn-primary"><a style="color:white;">Go Back to Holiday List</a></button>
				    </li>			
					
				</ul>
			</div>
		</div>
		<!-- PANEL HEAD START -->
		<div class="panel-body">
			<!-- PANEL BODY START -->
		<form
				action="<?php echo base_url(); ?>index.php/tours/tour_visited_cities_save"
				method="post" enctype="multipart/form-data" id="form form-horizontal validate-form"
				class='form form-horizontal validate-form' style="display:none;">
				<div class="tab-content">
					<!-- Add Package Starts -->
					<div role="tabpanel" class="tab-pane active" id="add_package">
						    <div class="col-md-12">

							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Package Duration
								</label>
								<div class='col-sm-4 controls'>
									<input type="hidden" name="duration" id="duration" value="<?=$tour_data['duration']?>">
									<input type="text" value="<?=($tour_data['duration']+1).' Days / '.($tour_data['duration']).' Night';?>" class='form-control' disabled>									
								</div>
							</div>

							<!-- <div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Visited City Name
								</label>
								<div class='col-sm-8 controls'>
									<input type="text" name="city" id="city" placeholder="Enter City" data-rule-required='true' class='form-control' required>									
								</div>
							</div> -->

							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>City
								</label>
								<div class='col-sm-4 controls'>
							    <?php
                                $tours_city = $tour_data['tours_city'];
                            //    debug($tours_city); exit();
                               $tours_city     = explode(',',$tours_city);
                             //   debug($tours_city);exit;
                                ?>
                                <select class='select2 form-control' name='city[]' id="city" multiple data-rule-required='true' required>                               
                                <?php
                                foreach($tours_city as $key => $value)
                                {
                                	echo '<option value="'.$value.'">'.$tours_city_name[$value].' </option>';
                                }
                                ?>                              
								</select>									
								</div>
							</div>
							
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>No Of Nights
								</label>
								<div class='col-sm-4 controls'>
								<select name='no_of_nights' id="no_of_nights" class='select2 form-control' data-rule-required='true' data-rule-required='true' required>
                                <option value="">Choose No Of Nights</option>
                                <?php
                                for($non=1;$non<=31;$non++)
                                {
                                	echo '<option value="'.$non.'">'.$non.(($non==1)? 'Night': 'Nights').'</option>';
                                }
                                ?>
								</select> &nbsp;	
											
								</div>
							</div>							
							
							<div id="itinerary_list"></div>	

							<hr>
                            
							<div class='' style='margin-bottom: 0'>
								<div class='row'>
									<div class='col-sm-9 col-sm-offset-3'>	
									    <input type="hidden" name="total_no_of_nights" id="total_no_of_nights" value="<?=$total_no_of_nights?>">
									    <input type="hidden" name="tour_id" value="<?=$tour_id?>">							
										<button class='btn btn-primary' type='submit'>Save</button>
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
			<div style="overflow-hidden; overflow-x:scroll;">
			<table class="table table-bordered">
			<thead>
				<tr>
					<th>SN</th>
					<th>City</th>
					<th>No Of Nights</th>
					<!-- <th>Action</th> -->				
				</tr>
			</thead>
			<tbody>
				<?php
        $sn = 1;
        //debug($tour_destinations);
        foreach ($tour_visited_cities as $key => $data) {    
        $city = $data['city'];
        $city = json_decode($city,1);
        foreach ($city as $k => $v) 
        {
        	if($k==0)
        	{ $city_names = $tours_city_name[$v]; } 
        	else { $city_names = $city_names.', '.$tours_city_name[$v]; }      
        } 
        echo '<tr>
              <td>'.$sn.'</td>
              <td>'.$city_names.'</td>
              <td>'.$data['no_of_nights'].(($data['no_of_nights']==1)? 'Night': 'Nights').' </td>';                  
            /*    <a class="" data-placement="top" href="'.base_url().'index.php/tours/edit_tour_visited_cities/'.$data['id'].'/'.$tour_id.'"
              data-original-title="Edit Tour Destination"> <i class="glyphicon glyphicon-pencil"></i> Edit
              </a>*/
              /*
        echo '<td class="center">
            
              <!--<a class="" data-placement="top" href="'.base_url().'index.php/tours/delete_tour_visited_cities/'.$data['id'].'/'.$tour_id.'"
              data-original-title="Delete Tour Destination"> <i class="glyphicon glyphicon-trash"></i></i> Delete
              </a>-->
              <a class="callDelete" id="'.$data['id'].'" tour-id="'.$tour_id.'"> 
              <i class="glyphicon glyphicon-trash"></i> Delete</a>
              </td>
              </tr>'; */           
        $sn++;
        }
        ?>
       
		</tbody>

		</table>
		 <p>*The cities data can not be modified once the itinerary has been entered during the creation of a holiday package.</p>
		</div>				
		</div>
		</div>
<script type="text/javascript">
     $(document).ready(function()
     {         
          $('#no_of_nights').on('change', function() { 
          $no_of_nights = parseInt($(this).val());  
          $total_no_of_nights = parseInt($('#total_no_of_nights').val());
          $duration = parseInt($('#duration').val());
          //alert($no_of_nights);alert($total_no_of_nights);alert($duration);

          if(($no_of_nights+$total_no_of_nights)>$duration)
          {
          	 $msg = $duration+' Nights / '+($duration+1)+' Days'
             alert('Sorry! This Holiday Package is designed for '+$msg+'. You are exceeding the limit.');
             $('#no_of_nights').val('');
             return false;
          }         
          }); 

          $(".callDelete").click(function() { 
            $id = $(this).attr('id'); //alert($id);
            $tour_id = $(this).attr('tour-id'); //alert($id);
		    $response = confirm("Are you sure to delete this record???");
		    if($response==true){ window.location='<?=base_url()?>index.php/tours/delete_tour_visited_cities/'+$id+'/'+$tour_id; } else{}
           });  
     });    
</script>
<script type="text/javascript" src="<?=get_domain()?>extras/system/template_list/template_v1/javascript/js/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" src="<?=get_domain()?>extras/system/template_list/template_v1/javascript/js/tiny_mce/tiny_mce_call.js"></script> 
<!--
<script type="text/javascript" src="/airliners/extras/system/template_list/template_v1/javascript/js/nicEdit-latest.js"></script> 
<script type="text/javascript">
bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
</script>-->
<link rel="stylesheet" type="text/css" href="http://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">
<script type="text/javascript" src="http://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
<script> $(function () { $('.table').DataTable(); }); </script> 