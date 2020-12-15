<?php
error_reporting(0);
foreach($tour_destinations as $td_key => $td_data)
{
 $TOUR_DESTINATIONS[$td_data['id']] =  $td_data['destination'];
}
//debug($tour_list);
foreach($tour_list as $tl_key => $tl_data)
{
 $TOUR_LIST[$tl_data['id']]     =  $tl_data['package_name'];
 $TOURS_COUNTRY[$tl_data['id']] =  $tours_country_name[$tl_data['tours_country']];
 $TOURS_TYPE[$tl_data['id']] =  $tl_data['package_type'];
 $TOURS_CODE[$tl_data['id']] =  $tl_data['tour_code'];
}
foreach($tours_itinerary as $ti_key => $ti_data)
{
 $TOUR_ITINERARY[$ti_data['id']] =  $ti_data['dep_date'];
}
?>
<style type="text/css">
  .crncy_det .row { margin: 0 -15px; }
</style>
<div id="Package" class="bodyContent col-md-12">
	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="panel-title">
				<ul class="nav nav-tabs nav-justified" role="tablist" id="myTab">
					<li role="presentation" class="active" id="add_package_li"><a
						href="#add_package" aria-controls="home" role="tab"
						data-toggle="tab">Tours Inquiry </a></li>
     </ul>
    </div>
   </div>
   <?php /* ?><div class="panel-body">
    <button class="btn btn-primary" type="button" data-toggle="collapse"
    data-target="#advanced_search" aria-expanded="false"
    aria-controls="advanced_search">Advanced Search</button>
    <hr>
    <div class="collapse in" id="advanced_search">
     <form method="GET" autocomplete="off"
     action="<?=base_url().'index.php/tours/tours_enquiry';?>">
     <div class="clearfix form-group">
      <div class="col-xs-4">
       <label> Package Name </label> <input type="text"
       class="form-control" name="package_name"
       value="<?=@$package_name?>" placeholder="Package Name">
      </div>
      <div class="col-xs-4">
       <label> Phone </label> <input type="text" class="form-control"
       name="phone" value="<?=@$phone?>" placeholder="Phone">
      </div> 
      <div class="col-xs-4">
       <label> Email </label> <input type="text" class="form-control"
       name="email" value="<?=@$email?>" placeholder="Email">
      </div>
     </div>
     <div class="col-sm-12 well well-sm">
      <button type="submit" class="btn btn-primary">Search</button>
      <button type="reset" class="btn btn-warning">Reset</button>
     </div>
    </form>
   </div>
  </div><?php */ ?>
  <div class="panel-body">
  </div>
  <div class="table-responsive scroll_main">
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>SN</th>
				<th>Reference No</th>
				<th>Agency Name with ID</th>
				<th>Contact No.</th>
				<th>Travel</th>
				<th>Destination</th>
				<th>Enquiry Date</th>
				<th>Date of Travel</th>
				<th>No of night</th>
				<th>No of pax</th>
				<th>Requests</th>
				<th>Action</th>
				<th>Amount</th>
				<!-- <th>Phone</th>
				<th>Email</th> -->
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
		<?php
			$current_record=1;
			if(!empty($table_data)){
				//debug($table_data);
				$sn=1;
				foreach($table_data as $parent_k => $parent_v) {
					extract($parent_v);
		?>
					<tr class="enq_tr">
						<input type="hidden" value="<?=$id?>" class="is_tour_id">
						<td><?php echo ($current_record++)?></td>
						<td><?php echo $agent_name;?></td>
						<td><?php echo $agent_details['agency_name'].' '.$agent_details['user_id'];?></td>
						<td><?php echo $agent_details['phone'];?></td>
						<!--<td><?php echo $country_name;?></td>-->
						<td><?php echo ucfirst($travel_type);?></td>
						<td><?php echo $country_name;?></td>
						<td><?php echo $created_date;?></td>
						
						<td><?php echo date('d/M',strtotime($fr_date)).' - '. date('d/M',strtotime($to_date));?></td>
						<td><?php echo $night; ?></td>
						<td><?php echo $adult.'|'.$child.'|'.$infant;?></td>
						<td><a class="btn btn_sm" data-toggle="modal" data-target="#note_<?=$id?>" >Notes</a></td>
						<td><a class="btn btn_sm" data-toggle="modal" data-target="#quot_<?=$id?>" >Send Quotation</a></td>
						<td><input type="text" class="quot_amount" data-ref="<?=$id?>" value="<?=$amount?>"></td>
						<td>
							<select class="enq_status">
								<option value="PENDING" <?php if($status=='PENDING'){ echo "selected";} ?> >PENDING</option>
								<option value="INPROGRESS" <?php if($status=='INPROGRESS'){ echo "selected";} ?>>INPROGRESS</option>
								<option value="QUOTED" <?php if($status=='QUOTED'){ echo "selected";} ?>>QUOTED</option>
								<option value="CONFIRMED" <?php if($status=='CONFIRMED'){ echo "selected";} ?>>CONFIRMED</option>
								<option value="CANCELLED" <?php if($status=='CANCELLED'){ echo "selected";} ?>>CANCELLED</option>
							</select>
						</td>
						
					</tr>
			<?php
			$sn++;
				}
				}
				else {
					echo '<tr><td>No Data Found</td></tr>';
				}
			?>
   </tbody>
  </table>
 </div>				
</div>
<?php
	$sn = 1;
	//debug($tours_enquiry);
	foreach ($table_data as $key => $data) { 
?>
	<div class="modal" id="note_<?=$data['id']?>" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Notes</h4>
				</div>
				<div class="modal-body bot">
		
					<div class="row">
<div class="qf_heading"> Agent Contact</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">Agent Id: </label>
	</div>
	<div class="col-sm-6">
	<span class="tab"><?=$data['agent_details']['user_id']?></span>
	</div>
</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">Agent Name: </label>
	</div>
	<div class="col-sm-6">
	<span class="tab"><?=$data['agent_details']['agency_name']?></span>
	</div>
</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">Agent Phone: </label>
	</div>
	<div class="col-sm-6">
	<span class="tab"><?=$data['agent_details']['phone']?></span>
	</div>
</div>
</div>
<div class="row">
<div class="qf_heading">Type of travel</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">Travel Type: </label>
	</div>
	<div class="col-sm-6">
	<span class="tab"><?=ucfirst($data['travel_type'])?></span>
	</div>
</div>
</div>
<div class="row">
<div class="qf_heading">WHERE YOU WANT TO TRAVEL</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">Destination: </label>
	</div>
	<div class="col-sm-6">
	<?php 
	$country=explode(',',$data['country_name']);
	foreach($country as $contr){
		echo '<span class="tab">'.$contr.'</span>';
	}
	?>
	</div>
</div>
</div>
<div class="row">
<div class="qf_heading">DEPARTURE CITY</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">Departure: </label>
	</div>
	<div class="col-sm-6">
	<span class="tab"><?=$data['city']?></span>
	</div>
</div>
</div>
<div class="row">
<div class="qf_heading">More Details</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">From Date:  </label>
	</div>
	<div class="col-sm-6">
	<span class="tab"><?=$data['fr_date']?></span>
	</div>
</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">To Date:  </label>
	</div>
	<div class="col-sm-6">
	<span class="tab"><?=$data['to_date']?></span>
	</div>
</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">No. of Night: </label>
	</div>
	<div class="col-sm-6">
	<span class="tab"><?=$data['night']?></span>
	</div>
</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">Adult: </label>
	</div>
	<div class="col-sm-6">
	<span class="tab"><?=$data['adult']?></span>
	</div>
</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">Child: </label>
	</div>
	<div class="col-sm-6">
	<span class="tab"><?=$data['child']?></span>
	</div>
</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">Infant: </label>
	</div>
	<div class="col-sm-6">
	<span class="tab"><?=$data['infant']?></span>
	</div>
</div>
</div>

<div class="row">
<div class="qf_heading">ANY SPECIAL REQUESTS</div>
<div class="col-sm-12">
	<div class="col-sm-4">
	<label class="control-label">Requests: </label>
	</div>
	<div class="col-sm-6">
	<span class="tab"><?=$data['remark']?></span>
	</div>
</div>
</div>					
					
					
					
					
					
					
					
				</div>

			</div>
		</div>
	</div>
	<div class="modal" id="quot_<?=$data['id']?>" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Upload Quotation</h4>
				</div>
				<div class="modal-body">
					<form action="<?php echo base_url();?>index.php/tours/upload_custom_enq_quot" enctype="multipart/form-data" method="post" id="upload_<?=$data['id']?>"> 
						<input type="hidden" name="app_ref" value="<?=$data['id']?>">
						<div>
							<div class="col-sm-12" style="margin: 20px 0px;">
								<div class="col-sm-12 images_div"  id="upload_parameters">
									<div class="gallery_div col-sm-12" id="hotel_voucher_div_<?=$data['id']?>">
							<?php 
							//debug($data['quotation']);
								
								if(!empty($data['quotation'])){
									if($data['quotation']!=' '){ 
										$doc_type=explode('.',$data['quotation']);
										if($doc_type[1]=='pdf'){
							?>
											<embed src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA?>/extras/custom/TMX1512291534825461/images/tour_uploads/<?=$data['quotation']?>" width="100px" height="80px" /><a data-ref="<?=$data['id']?>" data-ite_img_nam="<?=$data['quotation']?>" data-img_type="quotation" class="delete_image"><i class="fas fa-trash"></i></a>
									<?php }else{ ?>
												<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA?>/extras/custom/TMX1512291534825461/images/tour_uploads/<?=$data['quotation']?>" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;"><a data-ref="<?=$data['id']?>" data-ite_img_nam="<?=$data['quotation']?>" data-img_type="hotel_voucher" class="delete_imag"><i class="fas fa-trash"></i></a>
									<?php 	}
										} 
									
								}else { ?>
									<img src="<?=SYSTEM_IMAGE_FULL_PATH_TO_EXTRA.$this->template->domain_images('no_image.png');?>" alt="thumbnail" width="100px" height="80px" style="border: 1px solid #ccc;padding: 5px;display: block;">
								<?php } ?>
							
									</div>
								
									<input type="file" class="gallery form-control hotel_gallery" value="" name="cust_enq_quotation">
									<input type="hidden" value="<?=$hotel_voucher?>" name="old_enquiry_quotation">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12">
								<button type="submit" class="btn btn-danger id-enquiry-btn">Send Quotation</button>
								<strong id="mail_voucher_error_message" class="text-danger"></strong> 
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
<?php
$sn++;
   }
?>
</div>
<script type="text/javascript">
//function add_remark(id){

  //alert(id);
 // $.ajax({
 //      type: 'GET',
 //      url: app_base_url+'index.php/ajax/car_list/'+offset+'?booking_source=<?=$car_booking_source[0]['source_id']?>&search_id=<?=$car_search_params['search_id']?>&op=load'+url_filters,
 //      async: true,
 //      cache: true,
 //      //dataType: 'json',
 //      success: function(res) {
 //        loader(res);
 //        $(".car_filter_load").hide();
 //        $("#result_found_text").removeClass('hide');
 //      }
 //    });
//}
 $(document).ready(function()
 {
  
   $('.remark_submit').click(function(){
     var remark = this.id.split("_");
     var r_id = remark[1];
     var remark_text_id = "#agent_remark_"+r_id;
     var remark_text = $(remark_text_id).val();
     $.ajax({
            url:app_base_url+'tours/add_agent_remark',
            type:'POST',
            data:{'r_id':r_id,'agent_remark':remark_text},
            dataType: "json",
            success:function(ret){
            if(ret==true){
             
             setTimeout(function(){
             $('.remarks_msg').show();
             window.location.reload();
              }, 1000);
             } },
            error:function(){
            }
           }) ;
  });

  

  $('#child_count').change(function(){
    if($(this).val()>0){
      $('#child_price').removeAttr('readonly');
    }else{
      $('#child_price').attr('readonly','readonly');
    }
  });
  $('#infant_count').change(function(){
    if($(this).val()>0){
      $('#infant_price').removeAttr('readonly');
    }else{
      $('#infant_price').attr('readonly','readonly');
    }
  });
  $('#approve_form').submit(function() {
   if($('#total').val() == 'NaN' || $('#total').val() == 0){
    $('#total').addClass('invalid-ip');
    return false;
   }
  });
  $('#price_type1').click(function(){
    $(this).prop('checked', true);
    $('#price_type2').prop('checked', false);
    $('.price_type1').removeClass('hide');
    $('.price_type2').addClass('hide');
    $('#total').val(parseFloat($('#new_price').val()));
  });
  $('#price_type2').click(function(){
    $(this).prop('checked', true);    
    $('#price_type1').prop('checked', false);    
    $('.price_type2').removeClass('hide');
    $('.price_type1').addClass('hide');
    var total = 0;
    $.each($('.calculation'), function() {
     if($(this).val() == ''){
      var price = 0;
     }else{
      var price = $(this).val();     
     }
     total = total + parseFloat(price);
    });
    $('#total').val(total);
  });
  $('.approve_modal_btn').click(function(){
   $('#enquiry_reference_no').val($(this).data('enquiry'));
   $('#enquiry_tour_id').val($(this).data('tour_id'));
   var price = $(this).data('price');
   if(price == ''){
    price =0;
   }  
   $('#new_price').val(price);
   $('#total').val(price);
  });
  $('#new_price').on('keyup blur change', function(e) {
    $('#total').val($(this).val());
  });
  $('.calculation').on('keyup blur change', function(e) {
   var total = 0;
   $.each($('.calculation'), function() {
    if($(this).val() == ''){
     var price = 0;
    }else{
     var price = $(this).val();     
    }
     total = total + parseFloat(price);
   });
   $('#total').val(total.toFixed(2));
  });

  $(".callDelete").click(function() { 
   $id = $(this).attr('id'); 
   $response = confirm("Are you sure to delete this record?");
   if($response==true){ window.location='<?=base_url()?>index.php/tours/assign_delete_enquiry/'+$id; } else{}
  });
  
	$(document).on('change','.alloted_to',function(e){
		var sel_val = $(this).val();
		var sel_user =$(this).text();
		var enquiry_id =$(this).parents('.enq_tr').find('.is_tour_id').val();
		$response = confirm("Are you sure to assign this enquiry to "+sel_user+" ?");
		if($response==true){ 
			window.location='<?=base_url()?>index.php/tours/assign_enquiry/'+sel_val+'/'+enquiry_id; 
		} else{
			
		}
	});
	$(".quot_amount").blur(function(){
		var amount=$(this).val();
		var id=$(this).data('ref');
		$.ajax({
			url: '<?=base_url();?>tours/add_cust_enquiry_amount/' + id + '/' + amount,
			success: function (data, textStatus, jqXHR) {                            
				//window.location.reload();
			}
		});
	});
	$(document).on('click','.delete_image',function(){
		var img=$(this).data('ite_img_nam');
		var id=$(this).data('ref');
		var img_type=$(this).data('img_type');
		//alert(img);alert(id);alert(img_type); 
		$.ajax({
			url: '<?=base_url();?>tours/unlink_cust_enq_quot/' + id + '/' + img + '/' + img_type,
			success: function (data, textStatus, jqXHR) {                            
				//alert(data);   
				//$("#img_" + id).remove();
				window.location.reload();
			}
		});
	});
	$(document).on('change','.enq_status',function(e){
		var sel_val = $(this).val();
		var enquiry_id =$(this).parents('.enq_tr').find('.is_tour_id').val();
		//alert(sel_val);alert(enquiry_id);
		$response = confirm("Are you sure to change this enquiry status?");
		if($response==true){ 
			window.location='<?=base_url()?>index.php/tours/change_custom_enquiry_status/'+sel_val+'/'+enquiry_id; 
			//window.location.reload();
		} else{
			
		}
	});
 });
</script>
<link rel="stylesheet" type="text/css" href="http://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">
<script type="text/javascript" src="http://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
<script> $(function () { $('.table').DataTable(); }); </script> 
<?php 
function send_link_to_user($enquiry_reference_no,$tour_id)
{

 return '<a data-toggle="modal" data-target="#send_link_to_user" id="send_link_to_user_id" class="send_link_to_user_class fa fa-envelope-o" data-enquiry_reference_no="'.$enquiry_reference_no.'" data-tour_id="'.$tour_id.'"> Send Link</a>';
}
?>
<style>

span.tab {
    display: block;
    border: 1px solid #ccc;
    padding: 2px 10px;
}
.qf_heading {
    text-align: center;
    text-transform: uppercase;
    font-size: 16px;
    padding: 5px 0px;
    background: #f1f1f1;
    width: 100%;
    display: block;
    margin: 15px auto;
}
.modal-body.bot .col-sm-12 {
    margin-bottom: 10px;
}
</style>