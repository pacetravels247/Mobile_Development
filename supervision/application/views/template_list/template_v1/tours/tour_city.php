<div id="Package" class="bodyContent col-md-12">
	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="panel-title">
				<ul class="nav nav-tabs nav-justified" role="tablist" id="myTab">
					<li role="presentation" class="active" id="add_package_li"><a
						href="#add_package" aria-controls="home" role="tab"
						data-toggle="tab">Tour City </a></li>
      <li aria-controls="home"> &nbsp;&nbsp;
       <button class='btn btn-primary' onclick="$('.form').slideToggle();">Add</button>
      </li>		
     </ul>
    </div>
   </div> 
   <div class="panel-body">
    <form action="<?php echo base_url(); ?>index.php/tours/tour_city"method="post" enctype="multipart/form-data" id="form form-horizontal validate-form"class='form form-horizontal validate-form' style="display:none;"> 
    <div class="tab-content">
     <div role="tabpanel" class="tab-pane active" id="add_package">
      <div class="col-md-12">
       <div class='form-group'>
        <label class='control-label col-sm-3' for='validation_current'>Choose Country
        </label>
        <div class='col-sm-8 controls'>        
         <select class='select2 form-control' data-rule-required='true' name='country_id' id="country_id" data-rule-required='true' required>
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
	    <input type="hidden" class="is_unique" value="1">
       <div class='form-group'>
        <label class='control-label col-sm-3' for='validation_current'>Tour City Name
        </label>
        <div class='col-sm-8 controls'>
         <input type="text" name="CityName" id="CityName"
         placeholder="Enter Tour City Name" data-rule-required='true'
         class='form-control' required>					
         <small>Note:To add multiple city separete with coma(,).</small>				
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
      <th>Tour Country</th>
      <th>Tour City</th>
      <th>Action</th>				
     </tr>
    </thead>
    <tbody>		
     <?php
     $sn = 1;
     foreach ($tour_city as $key => $data) {
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
      <td>'.$data['CountryName'].'</td>
      <td>'.$data['CityName'].'</td>';    
  echo '<td class="center">
  <a class="" data-placement="top" href="'.base_url().'index.php/tours/edit_tour_city/'.$data['id'].'"
  data-original-title="Edit Tour Destination"> <i class="glyphicon glyphicon-pencil"></i> Edit
 </a>
	<a class="callDelete" id="'.$data['id'].'"> 
     <i class="glyphicon glyphicon-trash"></i> Delete</a> ';
 /*<a class="callDelete" id="'.$data['id'].'"> 
  <i class="glyphicon glyphicon-trash"></i> Delete</a>*/
echo '</td>
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
	
	$('.select2').select2();
	$(".callDelete").click(function() { 
		$id = $(this).attr('id'); //alert($id);
		$response = confirm("Are you sure to delete this record???");
		if($response==true){ window.location='<?=base_url()?>index.php/tours/delete_city/'+$id; } else{}
	});
				
});
</script>
<script type="text/javascript">  
 $(document).ready(function()
 {
  $(".callDelete").click(function() { 
    $id = $(this).attr('id'); 
    $response = confirm("Are you sure to delete this record???");
    if($response==true){ window.location='<?=base_url()?>index.php/tours/delete_tour_city/'+$id; } else{}
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
	$(document).on('keyup','#CityName',function(){
		var name=$('#CityName').val();
		$.post('<?=base_url();?>tours/check_unique_data',{'name':name,'table':'tours_city'},function(data)
		{
			//	alert(data);
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
	$(document).on('click','.form_subm',function(e){
		var country_name=$('#country_id').val();
		var des=$('#CityName').val();
		if($('#country_id').hasClass('invalid-ip')){
			$('.select2-selection').addClass('invalid-ip');
		}
		if(des=='' || country_name==''){
			alert("Please enter all the fields");
			
		}
		
	});
});
</script>

<link rel="stylesheet" type="text/css" href="http://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">
<script type="text/javascript" src="http://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
<script> $(function () { $('.table').DataTable(); }); </script>