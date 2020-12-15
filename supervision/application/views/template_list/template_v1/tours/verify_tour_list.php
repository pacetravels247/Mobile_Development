<!-- <script src="/chariot/extras/system/library/ckeditor/ckeditor.js"></script> -->
<div id="Package" class="bodyContent col-md-12">
  <?=$GLOBALS['CI']->template->isolated_view('report/email_popup')?>
  <?php echo $this->session->flashdata("msg"); ?>
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
              <button onclick="location.href='<?php echo base_url(); ?>index.php/tours/add_tour';" class='btn btn-primary'><a style="color:white;">Add Package</a></button>
			  <button onclick="location.href='<?php echo base_url(); ?>index.php/tours/draft_list';" class='btn btn-primary'><a style="color:white;">Draft List</a></button>
            </li>     

          </ul>
        </div>
      </div>
      <!-- PANEL HEAD START -->
	  <div class="panel-head">
	  <div class="col-md-12">
		<div class="form-group col-sm-3">
			<label>Tour Code</label>
			<select class="form-control fil_tour_code">
				<option value="0">Select Tour Code</option>
				<?php foreach ($tour_list as $fil_tc_key => $fil_tc_data) { ?>
					<option value="<?=$fil_tc_data['tour_code']?>"><?=$fil_tc_data['tour_code']?></option>
				<?php } ?>
			</select>
		</div>
		<div class="form-group col-sm-3">
			<label>Package Name</label>
			<input type="text" class="form-control fil_package_name" value="">
		</div>
		<div class="form-group col-sm-3">
			<label>From Date</label>
			<input type="text" class="form-control fil_from_date" id="datepicker_from_date" value="">
		</div>
		<div class="form-group col-sm-3">
			<label>To Date</label>
			<input type="text" class="form-control fil_to_date" id="datepicker_to_date" value="">
		</div>
		<div class="form-group col-sm-3">
			<label>Country</label>
			<select class="form-control fil_tour_country">
				<option value="0">Select Country</option>
				<?php 
					foreach($tours_country_name as $t_key =>$t_val){
				?>
					<option value="<?=$t_key?>"><?=$t_val?></option>
				<?php
					}
				?>
			</select>
		</div>
		<div class="form-group col-sm-3">
			<label>City</label>
			<select class="form-control fil_tour_city">
				<option value="0" >Select City </option>
			</select>
		</div>
		<div class="form-group col-sm-3">
			<label>No Of Nights</label>
			<select class="form-control fil_duration">
				<option value="NA">Select Duration</option>
			<?php
				for($dno=0;$dno<=30;$dno++)
				{
				   if($dno==1) { 
					$DayNight = ($dno+1).' Days | '.($dno).' Night';
				   }else 
				   {
					$DayNight = ($dno+1).' Days | '.($dno).' Nights';
				   }
				   echo '<option value="'.$dno.'">'.$DayNight.'</option>';
				}
			?>
								
			</select>
		</div>
		<div class="form-group col-sm-3">
			<button type="button" class="btn btn-info filter_result">Search</button>
		</div>
	  </div>
	  </div>
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
		  <th>Tour Code</th>
          <th>Package Name</th>
          <!-- <th>Expiry Date</th> -->
          <th>Duration</th>
          <th>Country</th>
          <th>City</th>
         <!-- <th>Publish</th>
          <th>Top Deals</th>-->
                <!--<th>Completion</th>
                <th>Status Change</th>--> 
                <th>Action</th>       
              </tr>
            </thead>
            <tbody>
              <?php
              $sn = 1;
			 // debug()
              foreach ($tour_list as $key => $data) {
                //debug($data);exit;
                $tour_id = $data['id'];
                $duration = $data['duration'];
                $duration = ($duration+1).' D | '.($duration).' N';                
                if($data['status']==1)
                {
                 $status = '<span style="color:green;">Completed</span>';
               }
               else
               {
                 $status = '<span style="color:red;">In-Completed</span>';
               }
               $dep_dates_list = ''; 
               $top_deals_list = ''; 
               $ij = 0; 
               $email_btn = flight_voucher_email($app_reference, $tour_id,$email); 
               $tour_dep_dates_list_all[$data['id']] = '';  
               if(!empty($tour_dep_dates_list_all[$data['id']] ))
               {
                 foreach($tour_dep_dates_list_all[$data['id']] as $ddl => $ddl_data)
                 {
                   $rand = rand(1111,9999);
                   if(in_array($ddl_data,$tour_dep_dates_list_published[$data['id']]))
                   { 
                    $checked = 'checked';
                  }
                  else{
                    $checked = '';
                  }  
                  $dep_dates_list .= changeDateFormatDMY($ddl_data).' : <input type="checkbox" class="published_status" id="published_status'.$tour_id.'" value="1" data-tourid="'.$data['id'].'" data-depdate="'.$ddl_data.'" '.$checked.'><br>';
                  $ij++;
                }
              }
              else
              {
                foreach ($tour_dep_dates_list_published_wd[$data['id']] as $date_value) {
                  if($date_value['publish_status'] == 1)
                  {
                    $checked = 'checked';
                  }
                  else
                  {
                    $checked ='';
                  }
                  if($date_value['deals_status'] == 1)
                  {
                    $top_checked = 'checked';
                  }
                  else
                  {
                    $top_checked ='';
                  }
                }
              }
              if($ij==0){
                $ddl = '';
                $rand = '';
                $ddl_data = '';
                $dep_dates_list .= 'Publish: <input type="checkbox" class="published_status" id="published_status'.$tour_id.'" value="1" data-tourid="'.$data['id'].'" data-depdate="'.$ddl_data.'" '.$checked.'><br>';
                $top_deals_list .= 'Top deals: <input type="checkbox" class="deals_status" id="deals_status'.$tour_id.'" value="1" data-tourid="'.$data['id'].'" data-depdate="'.$ddl_data.'" '.$top_checked.'><br>';
              }
              $city_in_record = $data['tours_city'];
              $city_in_record = explode(',',$city_in_record);
			  $city_in_record = array_unique($city_in_record);
              foreach($city_in_record as $k => $v)
              {
                if($k==0){ 
                  $city_in_record_str = $tours_city_name[$v];
                } 
                else
                { 
                  $city_in_record_str = $city_in_record_str.'<br>'.$tours_city_name[$v];
                }                            
              }
              $tours_country = explode(',', $data['tours_country']);
              $output =  "<tr class='result_tr'>
			  <input type='hidden' class='res_country' value='".$data['tours_country']."' />
			   <input type='hidden' class='res_city' value='".$data['tours_city']."' />
			   <input type='hidden' class='res_no_of_nyt' value='".$data['duration']."' />
			   <input type='hidden' class='res_frm_to_date' value='".$data['from_to_data']."' />
			   <input type='hidden' class='res_multiple_date' value='".$data['multi_date']."' />
              <td>".$sn."</td>    
				<td class='res_tour_code'>".$data['tour_code']."</td> 			  
              <td class='res_pack_name'>".string_replace_encode($data['package_name'])."</td>
               <td>".$duration."</td>
              
             
              <td>";
              // <td>'.$data['expire_date'].'</td>
                foreach ($tours_country as $key => $value) {
                 $output .=  $tours_country_name[$value].'<br/>';
               }
               $output .='</td>
            <td>'.$city_in_record_str.'</td>
              '; 
			 // <td>'.$dep_dates_list.'</td>
              // <td>'.$top_deals_list.'</td>
               echo $output; 
               if($data['status']==7)
               {
                echo '<td class="center">
                <a data-toggle="modal" class="book_tourid" href="#book_modal" data-tourid="'.$data['id'].'"> <i class="fa fa-user" aria-hidden="true"></i>Book/Quote</a> &nbsp; <br>
                <a class="" data-placement="top" href="'.base_url().'index.php/tours/price_management/'.$data['id'].'"
                data-original-title="Price Management"><i class="fa fa-usd" aria-hidden="true"></i> Price Management
              </a> &nbsp; <br>
              <a class="" data-placement="top" href="'.base_url().'index.php/tours/edit_tour_package/'.$data['id'].'"
              data-original-title="Edit Tour Destination"> <i class="glyphicon glyphicon-pencil" ></i> Edit
            </a> &nbsp; <br>
            <a class="" data-placement="top" href="'.base_url().'index.php/tours/tour_itinerary_p2/'.$data['id'].'"
              data-original-title="Edit Tour Itinerary"> <i class="glyphicon glyphicon-pencil" ></i> Edit Itinerary
            </a> &nbsp; <br>
            <a class="" data-placement="top" href="'.base_url().'index.php/tours/tour_dep_dates/'.$data['id'].'"
            data-original-title="Edit Tour Destination"> <i class="fa fa-calendar"aria-hidden="true"></i> Dep Dates
          </a> &nbsp; <br>
          <a class="" data-placement="top" href="'.base_url().'index.php/tours/tour_visited_cities/'.$data['id'].'"
          data-original-title="Edit Tour Destination"> <i class="fa fa-building"aria-hidden="true"></i> Cities
        </a> &nbsp; <br>            

        <a class="callDelete" id="'.$data['id'].'"> 
          <i class="glyphicon glyphicon-trash"></i> Delete</a>
          &nbsp; <br>
          <a class="" data-placement="top" href="'.base_url().'index.php/tours/voucher/'.$data['id'].'"
          data-original-title="Show Itinerary"> <i class="fa fa-file-text" aria-hidden="true"></i>Brochure
        </a> &nbsp; 
        <a class="hide" data-placement="top" href="'.base_url().'index.php/tours/itinerary/'.$data['id'].'"
        data-original-title="Show Itinerary"> <i class="fa fa-map-marker" aria-hidden="true"></i> Mapping
      </a> &nbsp; <br>  
      <a class="hide" data-placement="top" href="'.base_url().'index.php/tours/update_tour_voucher/'.$data['id'].'"
      data-original-title="Show Itinerary"> <i class="fa fa-file-text" aria-hidden="true"></i>Update Brochure
    </a> &nbsp; 
    <a class="hide" data-placement="top" href="'.base_url().'index.php/tours/updated_voucher/'.$data['id'].'"
    data-original-title="Show Itinerary"> <i class="fa fa-file-text" aria-hidden="true"></i>Updated Brochure
  </a> &nbsp; '.$email_btn.'
</td>
</tr>';
}   
else
{
  echo '<td class="center">
<a class="" data-placement="top" href="'.base_url().'index.php/tours/edit_tour_package/'.$data['id'].'"
              data-original-title="Edit Tour Destination"> <i class="glyphicon glyphicon-pencil" ></i> Edit
            </a> &nbsp; <br>
			<a class="" data-placement="top" href="'.base_url().'index.php/tours/price_management/'.$data['id'].'/B2B/verify"
                data-original-title="Price Management"><i class="fa fa-usd" aria-hidden="true"></i>B2B Price Management
              </a> &nbsp; <br>
			<a class="" data-placement="top" href="'.base_url().'index.php/tours/price_management/'.$data['id'].'/B2C/verify"
                data-original-title="Price Management"><i class="fa fa-usd" aria-hidden="true"></i>B2C Price Management
              </a> &nbsp; <br>
   <a class="" data-placement="top" href="'.base_url().'index.php/tours/tour_itinerary_p2/'.$data['id'].'"
              data-original-title="Edit Tour Itinerary"> <i class="glyphicon glyphicon-pencil" ></i> Edit Itinerary
            </a> &nbsp; <br>
			<a class="" data-placement="top" href="'.base_url().'index.php/tours/tour_dep_dates/'.$data['id'].'/verify_tour_list"
            data-original-title="Edit Tour Destination"> <i class="fa fa-calendar"aria-hidden="true"></i> Dep Dates
          </a> &nbsp; <br>
		  
<a class="callDelete" id="'.$data['id'].'"> 
  <i class="glyphicon glyphicon-trash"></i> Delete</a> <br>
  <a class="btn btn-primary send_verify" data-tour_id="'.$data['id'].'" id="'.$data['id'].'"> 
  <i class="glyphicon glyphicon-trash"></i> Verified</a>
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

<div id="book_modal" class="modal fade" role="dialog">
  <div class="modal-dialog">
   <div class="modal-content">
     <form action="<?=base_url()?>index.php/tours/send_booking_link/" method="POST" role="form" id="approve_form">
       <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Book Package</h4>
      </div>
      <div class="modal-body">
       <input type="hidden" name="tour_id" id="tour_id">
       <div class="row">
        <div class="col-md-6 mb10">
         <div class="radio form-group inline">
          <label>
          <input type="radio" id="info_type1" checked="checked">
           User Info
          </label>
         </div>&nbsp; &nbsp; &nbsp; 
         <div class="radio form-group inline">
          <label>
          <input type="radio" id="info_type2" >
           Inquiry Reference No
          </label>
         </div>
        </div>
       </div>
       <div id="user_holder">
        <div class="form-group">
         <label for="">Title <span style="color:red">*</span></label>
         <select name="title" class="form-control user_info" required="required" >
          <?=generate_options(get_enum_list('title'))?>
         </select>
        </div>
        <div class="form-group">
         <label for="">First Name <span style="color:red">*</span></label>
         <input type="text" class="form-control user_info" required="required" name="name" id="name" placeholder="">
        </div>
        <div class="form-group">
         <label for="">Middle Name</label>
         <input type="text" class="form-control" name="mname" id="mname"  placeholder="" >
        </div>
        <div class="form-group">
         <label for="">Last Name <span style="color:red">*</span></label>
         <input type="text" class="form-control user_info" required="required" name="lname" id="lname" placeholder="" >
        </div>

        <div class="form-group">
         <label for="">Country Code <span style="color:red">*</span></label>
         <select name="pn_country_code" class="form-control user_info" required="required" >

        <?=generate_options($country_code_list, array(0 => 146))?>
         </select>
        </div>
        <div class="form-group">
         <label for="">Phone <span style="color:red">*</span></label>
         <input type="text" class="form-control numeric user_info" required="required" name="phone" id="phone" placeholder="" >
        </div>
        <div class="form-group">
         <label for="">Email <span style="color:red">*</span></label>
         <input type="text" class="form-control user_info" required="required" name="email" id="email" placeholder="" >
        </div>
        <div class="form-group">
         <label for="">Departure Date <span style="color:red">*</span></label>
         <input type="text" class="form-control user_info" required="required" id="departure_date" name="departure_date" placeholder="" readonly="readonly">
        </div>
        <div class="form-group">
         <label for="">Non-Online Booking <span style="color:red">*</span></label>
         <select name="booking_type" id="booking_type" class="form-control user_info" required="required" >
          <option value="">Select</option>
          <option value="Customer Walk-in">Customer Walk-in</option>
          <option value="Customer Dial-in">Customer Dial-in</option>
          <option value="Other">Other</option>
         </select>
        </div>
       </div>
       <div class="hide" id="enq_holder">
        <div class="form-group">
         <label for="">Inquiry Reference <span style="color:red">*</span></label>
         <input type="text" class="form-control" name="enquiry_reference_no" id="enquiry_reference_no" placeholder="" >
        </div>
       </div>
       <div class="row">
        <div class="col-md-6 mb10">
         <div class="radio form-group inline">
          <label>
           <input type="radio" id="price_type1" name="price_type" value="total" checked="checked">
           Total
          </label>
         </div>&nbsp; &nbsp; &nbsp; 
         <div class="radio form-group inline">
          <label>
           <input type="radio" id="price_type2" name="price_type" value="adult_wise" >
           Adult/Child/Infant wise
          </label>
         </div>
        </div>
       </div>
       <div class="row">
        <div class="col-md-6">
         <div class="form-group">
          <label for="">Adult Count</label>
          <select name="adult_count" id="adult_count" class="form-control" required="required">
           <?=generate_options(custom_numeric_dropdown(20,1,1));?>
          </select>
         </div>
         <div class="form-group">
          <label for="">Child Count</label>
          <select name="child_count" id="child_count" class="form-control" required="required">
           <?=generate_options(custom_numeric_dropdown(5,0,1));?>
          </select>
         </div>
         <div class="form-group">
          <label for="">Infant Count</label>
          <select name="infant_count" id="infant_count" class="form-control" required="required">
           <?=generate_options(custom_numeric_dropdown(5,0,1));?>
          </select>
         </div>
        </div>
        <div class="col-md-6">
         <div class="form-group price_type1">
          <label for="">Price <span style="color:red">*</span></label>
          <input type="text" class="form-control numeric" name="new_price" id="new_price" required="required" value="0">
         </div>
         <div class="form-group price_type2 hide">
          <label for="">Total Adult Price <span style="color:red">*</span></label>
          <input type="text" class="form-control numeric calculation" name="adult_price" id="adult_price" value="0" >
         </div>
         <div class="form-group price_type2 hide">
          <label for="">Total Child Price</label>
          <input type="text" class="form-control numeric calculation" name="child_price" id="child_price" value="0" readonly="readonly">
         </div>  
         <div class="form-group price_type2 hide">
          <label for="">Total Infant Price</label>
          <input type="text" class="form-control numeric calculation" name="infant_price" id="infant_price" value="0" readonly="readonly">
         </div>     
        </div>
        <div class="clearfix"></div>

        <div class="col-md-6">
         <div class="radio form-group inline">
          <label>
           <input type="radio" id="quote_type1" name="quote_type" value="request_quote" checked="checked">
           Request for Quote
          </label>
         </div> &nbsp; &nbsp;
         <div class="radio form-group inline">
          <label>
           <input type="radio" id="quote_type2" name="quote_type" value="final_quote" >
           Final Quote
          </label>
         </div>
        </div>

        <div class="col-md-6 col-xs-12 pull-right crncy_det">
         <div class="row">
          <div class="form-group col-md-6 col-xs-6">
           <label for="">Currency</label>
           <input type="text" id="currency" name="currency" class="form-control" readonly="readonly" value="<?=get_application_currency_preference()?>">
          </div>
          <div class="form-group col-md-6 col-xs-6">
           <label for="">Total</label>
           <input type="text" readonly="readonly" class="form-control" id="total" name="total" value="0.00">
          </div>
          <div class="form-group col-md-12 col-xs-12">
           <label for="">&nbsp;</label>
           <button type="submit" class="btn btn-primary pull-right" id="book_package_id">Send</button>                  
          </div>       
         </div>   
        </div>
       </div>
      </div>
     </form>
    </div>
   </div>
  </div>
  <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>-->
<script type="text/javascript">  
  $(document).ready(function()
  {
	$( "#datepicker_from_date" ).datepicker({
     		dateFormat:'yy-mm-dd'
     	});
	$( "#datepicker_to_date" ).datepicker({
     		dateFormat:'yy-mm-dd'
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

    

    /*$('#book_package_id').submit(function() {
      if($('#new_price').val() == 0){
        $('#new_price').addClass('invalid-ip');
        return false;
      }
    });*/
  });
  $('#approve_form').submit(function() {
   /*if($('#info_type2').is(':checked') && $('#enquiry_reference_no').val()=='') { 
    $('#enquiry_reference_no').addClass('invalid-ip');
    return false;
   }
   return false;*/   

   if($('#total').val() == 'NaN' || $('#total').val() == 0){
    $('#total').addClass('invalid-ip');
    return false;
   }
   /*if($('#new_price').val() == '0'){
    $('#new_price').addClass('invalid-ip');
    return false;
  }*/
  });
  $('#info_type1').click(function(){
   $(this).prop('checked', true);
   $('#info_type2').prop('checked', false);
   $('#user_holder').removeClass('hide');
   $('#enq_holder').addClass('hide');
   $('#enquiry_reference_no').removeAttr('required');
   $('#user_holder').find('.user_info').each(function() {
     $(this).attr('required', 'required');
   });
  });
  $('#info_type2').click(function(){
   $(this).prop('checked', true);
   $('#info_type1').prop('checked', false);
   $('#enq_holder').removeClass('hide');
   $('#user_holder').addClass('hide');
   $('#enquiry_reference_no').attr('required', 'required');
   $('#user_holder').find('.user_info').each(function() {
    $(this).removeAttr('required');
    });
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

    $('#new_price').on('keyup blur change', function(e) {
      $('#total').val(($(this).val()));
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
    $('.book_tourid').click(function() {
      $('#tour_id').val($(this).data('tourid'));
    });

        //send the email voucher
        $('.send_email_voucher').on('click', function(e) {
          $("#mail_voucher_modal").modal('show');
          $('#mail_voucher_error_message').empty();
          email = $(this).data('recipient_email');
          var tour_id = $(this).data('tour_id');

          $("#voucher_recipient_email").val(email);
          $("#tour__id").val(tour_id);
          app_reference = $(this).data('app-reference');
          book_reference = $(this).data('booking-source');
          app_status = $(this).data('app-status');
          $("#send_mail_btn").off('click').on('click',function(e){
            email = $("#voucher_recipient_email").val();
            tour__id = $("#tour__id").val();
            var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

            if(email != ''){
              if(!emailReg.test(email)){
                $('#mail_voucher_error_message').empty().text('Please Enter Correct Email Id');
                return false;    
              }

              var _opp_url = app_base_url+'index.php/tours/email_voucher/';
              _opp_url = _opp_url+email+'/'+tour_id;
              toastr.info('Please Wait!!!');
              $.get(_opp_url, function() {

                toastr.info('Email sent  Successfully!!!');
                $("#mail_voucher_modal").modal('hide');
              });
            }else{
              $('#mail_voucher_error_message').empty().text('Please Enter Email ID');
            }
          });  
        });
        $(".published_status").change(function()
        {
          var id_X = $(this).attr('id');
          if($(this).is(":checked"))
          {
           var publish_status = 1;
         }
         else
         {
           var publish_status = 0;
         } 
         var tour_id  = $(this).data('tourid');
         var dep_date = $(this).data('depdate');
         $.ajax({
          url: '<?php echo base_url();?>index.php/tours/ajax_tour_publish/',
          method: 'post',
          data: {'tour_id':tour_id,'dep_date':dep_date,'publish_status':publish_status},
          dataType: 'json',
          success:function(data){
            $(".alert").hide();                
            var html = '';
            if(publish_status ==1)
            {
             for (x in data['first']) {
              html += data['first'][x]+'\n';
            }
          }
          for (x in data['sec']) {
            html += data['sec'][x]+'\n';
          }
          alert(html);   
          if(data['first'].length){
            $("#"+id_X).prop("checked",false);
          }            
        }             
      });
       });
        $(".deals_status").change(function()
        {
          var id_X = $(this).attr('id');
          if($(this).is(":checked"))
          {
           var deals_status = 1;
         }
         else
         {
           var deals_status = 0;
         } 
         var tour_id  = $(this).data('tourid');
         $.ajax({
          url: '<?php echo base_url();?>index.php/tours/ajax_tour_topdeals/',
          method: 'post',
          data: {'tour_id':tour_id,'deals_status':deals_status},
          dataType: 'json',
          success:function(data){
            $(".alert").hide();                
            var html = '';
            if(deals_status ==1)
            {
             for (x in data['first']) {
              html += data['first'][x]+'\n';
            }
          }
          for (x in data['sec']) {
            html += data['sec'][x]+'\n';
          }
          alert(html);   
          if(data['first'].length){
            $("#"+id_X).prop("checked",false);
          }            
        }             
      });
       });
        $(".callDelete").click(function() { 
            $id = $(this).attr('id'); //alert($id);
            $response = confirm("Are you sure to delete this record???");
            if($response==true){ window.location='<?=base_url()?>index.php/tours/delete_tour_package/'+$id+'/verify_tour_list'; } else{}
          });
		  
		$(document).on('click','.send_verify',function()
        {
			var tour_id  = $(this).data('tour_id');
		//	alert(tour_id);
			$response = confirm("Are you sure you want to send the package to Publish List?");
			if($response==true){ 
				$.ajax({
					url: '<?php echo base_url();?>index.php/tours/ajax_tour_verified/',
					method: 'post',
					data: {'tour_id':tour_id},
					success:function(data){
						var obj = JSON.parse(data);
						console.log(obj);
						var html = '';
						if ( typeof obj['first'][0] !== "undefined") {
							html += obj['first'][0]+'\n';
						}
						if ( typeof obj['first'][1] !== "undefined") {
							html += obj['first'][1]+'\n';
						}
						
						alert(html);
						location.reload();
					}             
				});
			} else{
				
			}
       });
		  
		$(document).on('change','.fil_tour_country',function(){
			var tour_id  = $(this).val();
			//	alert(tour_id);
			$.post('<?=base_url();?>tours/ajax_tours_country',{'tours_country':tour_id},function(data){
				$('.fil_tour_city').html('<option value="0">Select City</option>'+data);
			})
		});
		$(document).on('click','.filter_result',function(){
			var tour_code=$('.fil_tour_code').val().toLowerCase();
			var package_name=$('.fil_package_name').val().toLowerCase();
			var tour_country=$('.fil_tour_country').val();
			var tour_city=$('.fil_tour_city').val();
			var tour_from_date=$('.fil_from_date').val();
			var tour_to_date=$('.fil_to_date').val();
			var tour_duration=$('.fil_duration').val();
			//console.log(tour_code+'|'+package_name+'|'+tour_country+'|'+tour_city+'|'+tour_from_date+'|'+tour_to_date+'|'+tour_duration);
			$.each($('.result_tr'), function() {
				
				var res_tour_code=$(this).find('.res_tour_code').text().toLowerCase();
				var res_pack_name=$(this).find('.res_pack_name').text().toLowerCase();
				var res_no_of_nyt=$(this).find('.res_no_of_nyt').val();
				var res_country=$(this).find('.res_country').val();
				res_country = res_country.split(",");
				var res_city=$(this).find('.res_city').val();
				res_city = res_city.split(",");
				var res_from_to_date=$(this).find('.res_frm_to_date').val();
				var res_multiple_date=$(this).find('.res_multiple_date').val();
				
					var date_flag=0;
					if(res_from_to_date !=='null'){
						console.log(res_from_to_date);
						var res_from_to_date = JSON.parse(res_from_to_date);
						$.each(res_from_to_date, function(index, item) {
							
							if(Date.parse(item['valid_from']) >= Date.parse(tour_from_date) && Date.parse(item['valid_to']) <= Date.parse(tour_to_date)){
							   date_flag=1;
							   //console.log("in");
							}else{
								//console.log("out");
							}
						});
					}else{
						//console.log(res_multiple_date);
						var res_multiple_date =  res_multiple_date.split(",");
						$.each(res_multiple_date, function(index, item) {
							if(Date.parse(item) >= Date.parse(tour_from_date) && Date.parse(item) <= Date.parse(tour_to_date)){
							  date_flag=1;
							  // console.log("mul - in");
							}else{
								//console.log("mul - out");
							}
						});
					}
				var date_flag_fin=0;
				if(date_flag==1){
					date_flag_fin=1;
				}else{
					date_flag_fin=0;
				}
				//console.log('final'+date_flag_fin);
				//console.log(tour_code+'|'+res_tour_code);
				var tour_code_flag=0;
				if(tour_code==res_tour_code){
					tour_code_flag=1;
				}else{
					tour_code_flag=0;
				}
				
				if(tour_code=='0'){
					tour_code_flag=1;
				}
				var pack_flag=0;
				var res = res_pack_name.includes(package_name);
				
				if (res) {
					pack_flag=1;
				}else{
					pack_flag=0;
				}
				if(res_pack_name==''){
					pack_flag=1;
				}
				
				var country_flag=0;
				if(jQuery.inArray(tour_country, res_country) !== -1){
				  // console.log("yes");
					country_flag=1;
				}else{
					country_flag=0
				}
				if(tour_country==0){
					country_flag=1;
				}
				
				var city_flag=0;
				if(jQuery.inArray(tour_city, res_city) !== -1){
				  // console.log("yes");
					city_flag=1;
				}else{
					city_flag=0
				}
				if(tour_city==0){
					city_flag=1;
				}
				
				var duration_flag=0;
				if(res_no_of_nyt==tour_duration){
					duration_flag=1;
				}else{
					duration_flag=0;
				}
				
				if(tour_duration=='NA'){
					duration_flag=1;
				}
				if(tour_from_date=='' || tour_to_date==''){
					date_flag_fin=1
				}
				if(tour_code_flag==1 && pack_flag==1 && country_flag==1 && city_flag==1 && duration_flag==1 && date_flag_fin==1){
					$(this).removeClass('hide');
				}else{
					$(this).addClass('hide');
				}
				//console.log(tour_code_flag+'|'+pack_flag+'|'+country_flag+'|'+duration_flag);
			});
		});  
		  
		  
    });
$( function() {
  $( "#departure_date" ).datepicker({
    minDate : 0,
  });
});
    </script>
    <link rel="stylesheet" type="text/css" href="http://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">
    <script type="text/javascript" src="http://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
    <script>
      $(document).ready(function() {
        $('.table').DataTable({
          "pageLength": 100
        });
      });
    </script>
    <?php 
    function flight_voucher_email($app_reference, $tour_id,$recipient_email)
    {

      return '<!--<a class="send_email_voucher fa fa-envelope-o" data-recipient_email="'.$recipient_email.'" data-tour_id="'.$tour_id.'"> Email Brochure</a>-->';
    }
    ?>