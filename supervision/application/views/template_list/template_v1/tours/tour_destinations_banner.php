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
						data-toggle="tab">Holiday Destinations Banner </a></li>
			        <li aria-controls="home"> &nbsp;&nbsp;
					<button class='btn btn-primary'><a href="<?php echo base_url(); ?>index.php/tours/tour_list" style="color:white;">Tour List</a></button>
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
								<label class='control-label col-sm-3' for='validation_current'>Tour Description
								</label>
								<div class='col-sm-8 controls'>
									<textarea name="description" id="description" data-rule-required='true' class="form-control" data-rule-required="true" cols="70" rows="10" placeholder="Description" required></textarea>									
								</div>
							</div>	
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Tour Highlights
								</label>
								<div class='col-sm-8 controls'>
									<textarea name="highlights" id="highlights" data-rule-required='true' class="form-control" data-rule-required="true" cols="70" rows="10" placeholder="Highlights" required></textarea>									
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
	
			<div class="table-responsive scroll_main">
			<table class="table table-bordered">
			<thead>
				<tr>
					<!--<th>Sl.No</th>
					<th>Type</th>-->
					<th>Destination</th>
					<th>Gallery</th>
		            <th>Action</th>				
				</tr>
			</thead>
			<tbody>
				<?php
        $sn = 1;
        //debug($tour_destinations);
        foreach ($tour_destinations as $key => $data) {
        if($data['status']==1)
        {
           $status = '<span style="color:green;">Active</span>';
        }
        else
        {
           $status = '<span style="color:red;">In-Active</span>';
        }

        $gallery = $data['gallery'];
        $explode = explode(',',$gallery);
        $galleryImages = '';
        for($g=0;$explode[$g]!='';$g++)
        {   
        	if($g==0)
        	{
               $checked = 'checked';
        	}
        	else
        	{
        		$checked = '';
        	}
        	if($g%4==0)
        	{
                $galleryImages .= '<tr>';
        	}
        	//$path = $this->template->domain_image_upload_path().$explode[$g];
        	$galleryImages .= '<td>&nbsp;</td><td><img src="/chariot/extras/custom/keWD7SNXhVwQmNRymfGN/images/'.$explode[$g].'" style="width:150px"></td><td>&nbsp;</td><td><input type="radio" name="radio'.$data['id'].'" value="'.$explode[$g].'" '.$checked.'></td>';
        	if(($g+1)%4==0)
        	{
                $galleryImages .= '</tr>';
        	}
        }

        echo '<form action="'.base_url().'index.php/tours/tour_destinations_banner_save" method="post">
              <input type="hidden" name="id" value="'.$data['id'].'">
              <tr>
              <!--<td>'.$sn.'</td>
              <td>'.$data['type'].'</td>-->                  
              <td>'.$data['destination'].'</td>';
              echo '<td><table>'.$galleryImages.'</table></td>';
        
        echo '<td class="center">
              <button type="submit" class="btn btn-primary" data-placement="top" href="javascript:void(0);"
              data-original-title="Edit Tour Destination"> <i class="glyphicon glyphicon-saved"></i> Save
              </button> 
              </td>
              </tr>
              </form>';
        $sn++;
        }
        ?>
		</tbody>
		</table>
		</div>				
		</div>
		</div>
<!--
<script src="/chariot/extras/system/template_list/template_v1/javascript/js/nicEdit/nicEdit.js"></script>
<script type="text/javascript" src="/chariot/extras/system/template_list/template_v1/javascript/js/nicEdit/nicEdit_call.js"></script>

<script src="/chariot/extras/system/template_list/template_v1/javascript/js/datatables/jquery.dataTables.js"></script>
<script src="/chariot/extras/system/template_list/template_v1/javascript/js/datatables/jquery.dataTables.min.js"></script>
<script type="text/javascript">
            $(document).ready(function() {
                $('.table').dataTable();	
            });
</script>
-->
<link rel="stylesheet" type="text/css" href="http://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">
<script type="text/javascript" src="http://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
<script> $(function () { $('.table').DataTable(); }); </script> 