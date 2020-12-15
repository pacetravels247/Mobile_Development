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
						data-toggle="tab">Package Category </a></li>
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
				action="<?php echo base_url();?>index.php/tours/tour_type_save"
				method="post" enctype="multipart/form-data" id="form form-horizontal validate-form"
				class='form form-horizontal validate-form' style="display:none;">
				<div class="tab-content">
					<!-- Add Package Starts -->
					<div role="tabpanel" class="tab-pane active" id="add_package">
						   <div class="col-md-12">
							<input type="hidden" class="is_unique" value="1">
							<div class='form-group'>
								<label class='control-label col-sm-3' for='validation_current'>Package Category
								</label>
								<div class='col-sm-8 controls'>
									<input type="text" name="tour_type_name" id="tour_type_name"
										placeholder="" data-rule-required='true'
										class='form-control' required onblur="blurFunction()">									
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
                                <div class='col-sm-6 controls'>
                                    <input type="file" name="banner_image" required accept="image/x-jpg,image/jpeg" id="banner_image" class='form-control'>	
                                    <?=img_size_msg(200,300)?>								
                                </div>
                            </div>				
							<div class='' style='margin-bottom: 0'>
								<div class='row'>
									<div class='col-sm-9 col-sm-offset-3'>								
										<button class='btn btn-primary sub_form' type='submit'>Save</button>
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
					<th>Categories</th>
					<th>Current Status</th>
		            <th>Status Change</th>	
		            <th>Action</th>				
				</tr>
		    </thead>
		    <tbody>		
				<?php
        $sn = 1;
        //debug($tour_destinations);
        foreach ($tour_type as $key => $data) {
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
              <td>'.$data['tour_type_name'].'</td>';
        //echo '<td>'.$galleryImages.'</td>';
        echo '<td>'.$status.'</td>';
        if($data['status']==1)
        {
        echo '<td>
              <a class="" data-placement="top" href="'.base_url().'index.php/tours/activation_tour_type/'.$data['id'].'/0"
              data-original-title="Deactivate Tour Destination"> <i class="glyphicon glyphicon-th-large"></i></i> De-activate
              </a>
              </td>';
        }
        else
        {
        echo '<td>
              <a class="" data-placement="top" href="'.base_url().'index.php/tours/activation_tour_type/'.$data['id'].'/1"
              data-original-title="Activate Tour Destination"> <i class="glyphicon glyphicon-th-large"></i></i> Activate
              </a>
              </td>';
        }      
        echo '<td class="center">
              <a class="" data-placement="top" href="'.base_url().'index.php/tours/edit_tour_type/'.$data['id'].'"
              data-original-title="Edit Tour Destination"> <i class="glyphicon glyphicon-pencil"></i> Edit
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
		        if($response==true){ window.location='<?=base_url()?>index.php/tours/delete_tour_type/'+$id; } else{}
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
		   
		});
		$(document).on('keyup','#tour_type_name',function(){
			var name=$('#tour_type_name').val();
			$.post('<?=base_url();?>tours/check_unique_data',{'name':name,'table':'tour_type'},function(data)
			{
					//alert(data);
		        	if(data>0){
						$('.is_unique').val('0');
						//alert("Data already exist");
						//return false;
					}else{
						$('.is_unique').val('1');
					}
		    });
		});
		$("form").submit(function(e){
			var name=$('.is_unique').val();
			
			if(name==0){
		
				alert("Data already exist");
				e.preventDefault();
			}
		
			
		});
		$(document).on('click','.sub_form',function(e){
			
			if($('input').hasClass('invalid-ip')){
				alert("Please enter all the fields");
					$("#cke_description").css("border","1px solid red");
					
			}
				
		});
		/* function blurFunction() {
			  var name=$('#tour_type_name').val();
			  // alert(name);
			   $.post('<?=base_url();?>tours/check_unique_data',{'name':name,'table':'tour_type'},function(data)
		        {
					alert(data);
		        	if(data>0){
					
						alert("Data already exist");
						return false;
					}
		        });
			} */
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