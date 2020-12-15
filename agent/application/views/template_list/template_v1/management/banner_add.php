<?php
$_datepicker = array(array('expiry_date', FUTURE_DATE));
$this->current_page->set_datepicker($_datepicker);
$this->current_page->auto_adjust_datepicker(array(array('expiry_date')));
?>

<div class="bodyContent col-md-12">
	<div class="panel panel-primary clearfix">
		<!-- PANEL WRAP START -->
		<div class="panel-heading"><h4>Banner Advertisement</h4>
			<!-- PANEL HEAD START -->
		</div>
		<!-- PANEL HEAD START -->
		<div class="panel-body">
			<form method="POST" id="offline_booking" autocomplete="off"
				action="<?php echo base_url(). 'index.php/management/set_advertisement' ?>" enctype="multipart/form-data">
				
				<div class="clearfix form-group agent_details">
					<div class="col-md-12">
						<label>Advertisement Text</label> 
						<input type="text" class="form-control" name="title" placeholder="Advertisement Text" value="<?=@$title; ?>" required >
					</div>
				<div class="col-md-4">
					<label>Link (http://www.pacetravel.com)</label> 
					<input type="text" class="form-control" name="link" placeholder="Link" value="<?=@$link; ?>">
				</div>
				
				<div class="col-md-4">
					<label>Image</label> 
					<input type="file" name="offer_image" id="image" >
				</div>
					<!-- <div class="col-md-4">
						<label>Module Type</label>&nbsp;&nbsp; 
						<input type="radio" name="module_type" value="B2B">
						<label>B2B</label> 
						&nbsp;&nbsp;
						<input type="radio" name="module_type" value="B2C">
						<label>B2C</label> 
						&nbsp;&nbsp;
						<input type="radio" name="module_type" value="Both" checked>
						<label>Both</label> 
						&nbsp;&nbsp;
					</div> -->
			<div class="col-md-4">
				<label>Expiry Date</label> 
				<input type="text" readonly id="expiry_date" class="form-control" name="expiry_date" value="<?=@$expiry_date?>" placeholder="Request Date">
			</div>
	</div>
											<div class="clearfix form-group agent_details">					<div class="col-sm-1 pull-right">
						<button type="submit" name="submit" value="submit">Submit</button>
					</div>
				</div>
			</form>

			<div class="table-reponsive">
				<table border="1" class="table table-bordered">
					<tr>
						<td>S/No</td>
						<td>Advertisement Text</td>
						<td>Link</td>
						<td>Image</td>
						<td>Action</td>
					</tr>
					<?php 
						if(!empty($data)){
						$i=1;
						foreach($data as $key=>$value){
					?>
						<tr id="banner-row_<?=$value['id']?>">
							<td><?=$i?></td>
							<td><?=$value['title']?></td>
							<td><?=$value['link']?></td>
							<td><img src="<?=$this->template->domain_images().'offer_images/'.$value['image']?>" height="10%" class="image-responseive" style="width:100%; height: 120px; text-align:center" ></td>
							<td>
								<?php if($value['status'] == 1){ ?>
									<a style="color: #000000;">Active</a>
								&nbsp;&nbsp;<a  id="<?=$value['id']?>" style="cursor: pointer;" class="inactive">Inactive</a>
								<?php }else{ ?>
									<a id="<?=$value['id']?>" style="cursor: pointer;" class="active">Active</a>
								&nbsp;&nbsp;<a style="color: #000000;">Inactive</a>
								<!-- <a href="<?php echo base_url().'index.php/general/delete_add/'.$value['id'].'/'.$value['image'].'/banner'; ?>">Delete</a> -->
								<?php } ?>
								<a style="margin-left: 15px; cursor: pointer;" class="delete_ad btn btn-danger">Delete</a>
							</td>
						</tr>
					<?php $i++; } }else{ ?>
						<tr>
							<td colspan="5" align="center"> No, Data's Found. </td>
						</tr>
					<?php } ?>
				</table>
			</div>


		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$('.active, .inactive').click(function(){
			var $id = $(this).attr('id');
			var base_url = '<?php echo base_url(); ?>';
			var $status = $(this).attr('class');
			$.post(base_url+'index.php/management/add_status',{id: $id, status: $status}).done(function(data){
				if(data == '0'){
					alert('Please deactive all and then try again..');
				}else{
					location.reload();
				}
			});
		});
		$('.delete_ad').click(function(){
			if(!confirm("Are you sure, you want to delete?"))
				return false;
			
			var tr_id = $(this).closest("tr").attr("id");
			var $id = tr_id.split("_")[1];
			var base_url = '<?php echo base_url(); ?>';
			$.post(base_url+'index.php/management/delete_ad',{id: $id}).done(function(data){
				{
					$("#"+tr_id).remove();
				}
			});
		})
	});
</script>
