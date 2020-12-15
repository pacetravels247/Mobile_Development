<?php error_reporting(0);?>
<script src="/chariot/extras/system/library/ckeditor/ckeditor.js"></script>
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
						data-toggle="tab">Package List </a></li>
			        <li aria-controls="home"> &nbsp;&nbsp;
					<button class='btn btn-primary'><a href="<?php echo base_url(); ?>index.php/tours/add_tour" style="color:white;">Add Package</a></button>
				    </li>			
					
				</ul>
			</div>
		</div>
		<!-- PANEL HEAD START -->
		<div class="panel-body">
			<!-- PANEL BODY START -->
			<form
				action="<?php echo base_url(); ?>index.php/tours/add_tour_destination_save"
				method="post" enctype="multipart/form-data" id="form form-horizontal validate-form"
				class='form form-horizontal validate-form' style="display:none;">
				<div class="tab-content">
					<!-- Add Package Starts -->
					<div role="tabpanel" class="tab-pane active" id="add_package">
						   <div class="col-md-12">

							<input type="hidden" name="a_wo_p" value="a_w"> <input type="hidden" name="deal" value="0">
				            <div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Package Type</label>
								<div class='col-sm-4 controls'>
									<input type="radio" name="pkg_type" id="pkg_typeD" value="Domestic" data-rule-required='true' class='form-control2 pkg_typeD' required checked> Domestic <br> 
									<input type="radio" name="pkg_type" id="pkg_typeI" value="International" data-rule-required='true' class='form-control2 pkg_typeD' required > International
								</div>
							</div>							
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Destination
								</label>
								<div class='col-sm-4 controls'>
									<input type="text" name="destination" id="destination"
										placeholder="Enter Destination" data-rule-required='true'
										class='form-control' required>									
								</div>
							</div>
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Description
								</label>
								<div class='col-sm-4 controls'>
									<textarea name="description" id="description" data-rule-required='true' class="form-control" data-rule-required="true" cols="70" rows="3" placeholder="Description" required></textarea>									
								</div>
							</div>	
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Highlights
								</label>
								<div class='col-sm-4 controls'>
									<textarea name="highlights" id="highlights" data-rule-required='true' class="form-control" data-rule-required="true" cols="70" rows="3" placeholder="Highlights" required></textarea>									
								</div>
							</div>
							<!--
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Upload Video
								</label>
								<div class='col-sm-4 controls'>
									<input type="file" name="video" id="video" class='form-control'>									
								</div>
							</div>
						    -->
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Upload Gallery
								</label>
								<div class='col-sm-4 controls'>
									<input type="file" name="gallery[]" id="gallery" multiple data-rule-required='true' class='form-control' required>									
								</div>
							</div>					
							<div class='' style='margin-bottom: 0'>
								<div class='row'>
									<div class='col-sm-9 col-sm-offset-3'>								
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
					<!--<th>Package ID</th>-->
					<th>Package Name</th>
					<th>Country</th>
					<th>City</th>
					<th>Duration</th>
					<!-- <th>Dep Dates : Publish</th> -->
		            <!--<th>Completion</th>
		            <th>Status Change</th>-->	
		            <th>Action</th>				
				</tr>
			</thead>
			<tbody>
				<?php
        $sn = 1;
        //debug($tour_destinations);
        foreach ($tour_list as $key => $data) {
        $duration = $data['duration'];
                                    if($duration==1)
                                	{ $duration = ($duration).' N | '.($duration+1).' D';}
                                    else{ $duration = ($duration).' N | '.($duration+1).' D';}	
        if($data['status']==1)
        {
           $status = '<span style="color:green;">Completed</span>';
        }
        else
        {
           $status = '<span style="color:red;">In-Completed</span>';
        }
        $dep_dates_list = '';       
        foreach($tour_dep_dates_list_all[$data['id']] as $ddl => $ddl_data)
        {
        	 $rand = rand(1,1000);
        	 if(in_array($ddl_data,$tour_dep_dates_list_published[$data['id']]))
        	 { $checked = 'checked';} else{$checked = '';}	
             $dep_dates_list .= changeDateFormatDMY($ddl_data).' : <input type="checkbox" class="published_status" id="published_status'.$ddl.$rand.'" value="1" tour_id="'.$data['id'].'" dep_date="'.$ddl_data.'" '.$checked.'><br>';
        }

                            $city_in_record = $data['tours_city'];
                            $city_in_record = explode(',',$city_in_record);
                            // debug($city_in_record); exit('');

                            foreach($city_in_record as $k => $v)
                            {
                              if($k==0){ 
                               $city_in_record_str = $tours_city_name[$v];
                              }else{ 
                               $city_in_record_str = $city_in_record_str.'<br>'.$tours_city_name[$v];
                              }                             
                            }
                            


        //echo '<td>'.$data['package_id'].'</td> ';
        echo '<tr>
              <td>'.$sn.'</td>                               
              <td>'.string_replace_encode($data['package_name']).'</td>
              <!--<td>'.string_replace_encode($tour_destinations[$data['destination']]).'</td>-->
              <td>'.$tours_country_name[$data['tours_country']].'</td>
              <td>'.$city_in_record_str.'</td>
              <td>'.$duration.'</td>
              <!--<td>'.$dep_dates_list.'</td>
              <td>'.$status.'</td>-->';   
        /*if($data['status']==1)
        {
        echo '<td>
              <a class="" data-placement="top" href="'.base_url().'tours/activation_tour_package/'.$data['id'].'/0"
              data-original-title="Deactivate Tour Destination"> <i class="glyphicon glyphicon-th-large"></i> De-activate
              </a>
              </td>';
        }
        else
        {
        echo '<td>
              <a class="" data-placement="top" href="'.base_url().'tours/activation_tour_package/'.$data['id'].'/1"
              data-original-title="Activate Tour Destination"> <i class="glyphicon glyphicon-th-large"></i> Activate
              </a>
              </td>';
        }*/ 

        if($data['status']==1)
        {
        echo '<td class="center">
         <a class="" data-placement="top" href="'.base_url().'index.php/tours/price_management_pending/'.$data['id'].'"
              data-original-title="Price Management"> <i class="glyphicon glyphicon-pencil" ></i> Price Management
              </a> &nbsp; <br>
              <a class="" data-placement="top" href="'.base_url().'index.php/tours/edit_tour_package/'.$data['id'].'"
              data-original-title="Edit Tour Destination"> <i class="glyphicon glyphicon-pencil" ></i> Edit
              </a> &nbsp; <br>
              <a class="" data-placement="top" href="'.base_url().'index.php/tours/tour_dep_dates/'.$data['id'].'"
              data-original-title="Edit Tour Destination"> <i class="glyphicon glyphicon-pencil"></i> Dep Dates
              </a> &nbsp; <br>
              <!-- <a class="" data-placement="top" href="'.base_url().'index.php/tours/tour_visited_cities/'.$data['id'].'"
              data-original-title="Edit Tour Destination"> <i class="glyphicon glyphicon-pencil"></i> Cities
              </a> &nbsp; <br>            
               <a class="" data-placement="top" href="'.base_url().'index.php/tours/itinerary/'.$data['id'].'"
              data-original-title="Show Itinerary"> <i class="glyphicon glyphicon-th-large"></i> Mapping
              </a> &nbsp; <br>-->
              <a class="" data-placement="top" href="'.base_url().'index.php/tours/approve_package/'.$data['id'].'"
              data-original-title="Show Itinerary"> <i class="glyphicon glyphicon-th-large"></i> Approve
              </a> &nbsp; <br>
              <a class="callDelete" id="'.$data['id'].'"> 
              <i class="glyphicon glyphicon-trash"></i> Delete</a>
              </td>
              </tr>';
              /*
              href="'.base_url().'tours/delete_tour_package/'.$data['id'].'"
              href="'.base_url().'tours/delete_tour_package/'.$data['id'].'"

              <a class="" data-placement="top" href="'.base_url().'tours/tour_pricing/'.$data['id'].'"
              data-original-title="Edit Tour Destination"> <i class="glyphicon glyphicon-pencil"></i> Tour Pricing
              </a>*/
        }   
        else
        {
        echo '<td class="center">
              <a class="" data-placement="top" href="'.base_url().'index.php/tours/tour_dep_dates_p2/'.$data['package_id'].'"
              data-original-title="Edit Tour Destination"> <i class="glyphicon glyphicon-pencil"></i> Dep Dates
              </a>
              <a class="callDelete" id="'.$data['id'].'"> 
              <i class="glyphicon glyphicon-trash"></i> Delete</a>
              </td>
              </tr>';              
        } 
        
        $sn++;
        }
        ?>
		</tbody>
		</table>
		</div>				
		</div>
		</div>
		<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>-->
		<script type="text/javascript">  
		$(document).ready(function()
		{
           $(".published_status").change(function()
           {
           	    $id = $(this).attr('id'); //alert($id);
           	    if($(this).is(":checked"))
           	    {
                   $publish_status = 1;
                   $publish_status_message = 'Thanks! This tour is successfully published now.';
           	    }
           	    else
           	    {
                   $publish_status = 0;
                   $publish_status_message = 'Thanks! This tour is successfully unpublished now.';
           	    } 
                $tour_id  = $(this).attr('tour_id');
                $dep_date = $(this).attr('dep_date');
                //alert($tour_id); alert($dep_date);
                $.post('<?php echo base_url();?>index.php/tours/ajax_tour_publish/',{'tour_id':$tour_id,'dep_date':$dep_date,'publish_status':$publish_status},function(data)
		        {
		            //alert(data);
		            if(data==1)
		            {
                       $message = 'Sorry! Holiday Package itinerary is not yet mapped for this departure date.';                     
                       $('#'+$id).prop("checked",false);
		            }
		            else if(data==2)
		            {
		               $message = $publish_status_message;
		            }
		            alert($message);		            
		        });
           });
           $(".callDelete").click(function() { 
            $id = $(this).attr('id'); //alert($id);
		    $response = confirm("Are you sure to delete this record???");
		    if($response==true){ window.location='<?=base_url()?>index.php/tours/delete_tour_package/'+$id; } else{}
           });
		});
        </script>
        <link rel="stylesheet" type="text/css" href="http://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">
<script type="text/javascript" src="http://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
<script> $(function () { $('.table').DataTable(); }); </script> 