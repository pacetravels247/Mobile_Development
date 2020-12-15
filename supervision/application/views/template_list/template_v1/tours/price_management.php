<style>
  .price_mgnt .control-label.col-sm-3{text-align: right;}
  .price_mgnt .modal-footer {padding: 15px;text-align: left;float: left;width: 100%;}
  .price_mgnt .form-group {margin-bottom: 15px;float: left;width: 100%;}
  .price_mgnt .fa {position: absolute;line-height: 33px;padding: 0 10px;}
  .price_mgnt .form-control{padding: 6px 25px;}
</style>
<?php 
$hotel_datepicker = array(array('from_', FUTURE_DATE_DISABLED_MONTH), array('to_', FUTURE_DATE_DISABLED_MONTH));
$GLOBALS['CI']->current_page->set_datepicker($hotel_datepicker);
$GLOBALS['CI']->current_page->auto_adjust_datepicker(array(array('from_', 'to_')));
//error_reporting(E_ALL);exit;
?>
<div class="price_mgnt">
<div class="pk_form col-xs-12" id="myModal">
   <form id="enquiry_form" method="post" action="<?php echo base_url() ?>index.php/tours/save_price_management">
      <div class="modal-body">
         <h4 class="text-center hldy_tit">Price Management</h4>
          <div class="form-group modl">
            <label class="control-label col-sm-3" for="user_email">Package Type  :<strong class="text-danger"></strong></label>
            <div class="col-sm-3 col-md-8 controls eml"><i class="fa fa-user" aria-hidden="true"></i>
            <select class='select2 form-control form_parametere_needs' data-rule-required='true' name='package_type' id="package_type" data-rule-required='true' readonly required>
                <option value="">Choose Type</option>
                <option value="B2B" <?php if($module=='B2B') echo "selected"; ?>>B2B</option>
				<option value="B2C" <?php if($module=='B2C') echo "selected"; ?>>B2C</option>
            </select>
            </div>
         </div>
		<div class="form-group modl">
            <label class="control-label col-sm-3" for="user_email">Occupancy  :<strong class="text-danger"></strong></label>
            <div class="col-sm-3 col-md-8 controls eml"><i class="fa fa-user" aria-hidden="true"></i>
            <select class='select2 form-control form_parametere_needs' data-rule-required='true' name='occupancy' id="occupancy" data-rule-required='true' required>
                <option value="">Choose Occupancy</option>
                <?php
                foreach($occupancy_details as $occupancy_details_key => $occupancy_details_value)
                {
                  echo '<option value="'.$occupancy_details_value['id'].'">'.$occupancy_details_value['occupancy_name'].' </option>';
                }
                ?>
            </select>
            </div>
        </div>
		<div class="form-group modl">
            <label class="control-label col-sm-3" for="user_email">Currency  :<strong class="text-danger"></strong></label>
            <div class="col-sm-3 col-md-8 controls eml"><i class="fa fa-money" aria-hidden="true"></i>
            <select class='select2 form-control' data-rule-required='true' name='currency' id="currency" data-rule-required='true' required>
                                <option value="">Choose Currency</option>
								<option value="INR" selected>INR</option>
                             <?php
                                foreach($currency as $currency_key => $currency_value)
                                {
									
                                  echo '<option value="'.$currency_value['country'].'">'.$currency_value['country'].' </option>';
                                }
                                ?>
                </select> </div>
         </div>
		<div class="form-group modl">
            <label class="control-label col-sm-3" for="user_email">Purchase Price :<strong class="text-danger"></strong></label>
            <div class="col-sm-3 col-md-8 controls eml"><i class="fa fa-money" aria-hidden="true"></i><input type="text" class="form-control mntxt numeric" name="purchase_price" id="eemail" placeholder="Price" aria-required="true" required="required"></div>
        </div>
		
        <!-- <div class="form-group modl">
            <label class="control-label col-md-5 col-xs-4" for="user_name">From Date  :<strong class="text-danger"></strong></label>
            <div class="col-md-7 col-xs-8 eml n_psngr"><i class="fa fa-calendar" aria-hidden="true"></i>  <input type="text" name="from_date" class="form-control mntxt form_parametere_needs" id="from_" placeholder="From Date" aria-required="true" required="required"  readonly="readonly">    
            </div>
         </div>-->
         <!-- <span class="load_check hide">Please Wait we are checking availability</span> -->
         <!--<div class="form-group modl">
            <label class="control-label col-md-5 col-xs-4" for="user_name">To Date  :<strong class="text-danger"></strong></label>
            <div class="col-md-7 col-xs-8 eml n_psngr"><i class="fa fa-calendar" aria-hidden="true"></i> <input type="text" name="to_date" class="form-control mntxt form_parametere_needs" id="to_" placeholder="To Date" aria-required="true" required="required"  readonly="readonly">    
            </div>
         </div>-->
         <!--  <div class="form-group modl">
            <label class="control-label col-md-5 col-xs-4" for="user_name">Depature Date  :<strong class="text-danger"></strong></label>
            <div class="col-md-7 col-xs-8 eml n_psngr"><i class="fa fa-calendar" aria-hidden="true"></i> <input type="text" name="depature_date" class="form-control mntxt" id="hl_depdat11" placeholder="To Date" aria-required="true" required="required">    
            </div>
         </div> -->
        <!--  <div class="form-group modl">
            <label class="control-label col-md-5 col-xs-4" for="user_email">Currency  :<strong class="text-danger"></strong></label>          
            <div class="col-md-7 col-xs-8 eml n_psngr"><i class="fa fa-inr" aria-hidden="true"></i><input type="text" class="form-control mntxt" placeholder="Currency" aria-required="true" required="required">    
            </div>
         </div> -->
          
		 <div class="form-group modl">
            <label class="control-label col-sm-3" for="user_email">Market Price :<strong class="text-danger"></strong></label>
            <div class="col-sm-3 col-md-8 controls eml"><i class="fa fa-money" aria-hidden="true"></i><input type="text" class="form-control mntxt numeric" name="market_price" id="eemail" placeholder="Price" aria-required="true" required="required"></div>
         </div>
		 <div class="form-group modl">
            <label class="control-label col-sm-3" for="user_email"><span class="change_text">Net</span> Amount :<strong class="text-danger"></strong></label>
            <div class="col-sm-3 col-md-8 controls eml"><i class="fa fa-money" aria-hidden="true"></i><input type="text" class="form-control mntxt numeric" name="netprice_price" id="eemail" placeholder="Price" aria-required="true" required="required"></div>
         </div>
		 <div class="form-group modl">
            <label class="control-label col-sm-3" for="user_email">Advance Payment (%)<strong class="text-danger"></strong></label>
            <div class="col-sm-3 col-md-8 controls eml"><i class="fa fa-money" aria-hidden="true"></i><input type="text" class="form-control mntxt numeric" name="advance_pay" id="eemail" placeholder="Advance pay in percentage" aria-required="true"></div>
        </div>
         <!-- <div class="form-group modl">
            <label class="control-label col-md-5 col-xs-4" for="user_email">Value  :<strong class="text-danger"></strong></label>
            <div class="col-md-7 col-xs-8 eml "><i class="fa fa-usd" aria-hidden="true"></i><input type="text" class="form-control " name="airliner_price" id="airliner_price" placeholder="Price" aria-required="true" required="required"></div>
           
         </div> -->

         <!-- <div class="form-group modl">
            <label class="control-label col-md-5 col-xs-4" for="user_email">Price :<strong class="text-danger"></strong></label>
            <div class="col-md-7 col-xs-8 eml"><i class="fa fa-money" aria-hidden="true"></i><input type="text" class="form-control mntxt numeric" name="sessional_price" id="eemail" placeholder="Price" aria-required="true" required="required"></div>
         </div>
          <div class="form-group modl">
            <label class="control-label col-md-5 col-xs-4" for="user_email">Markup(NGN)  :<strong class="text-danger"></strong></label>
            <div class="col-md-4 col-xs-4 eml npadR"><i class="fa fa-line-chart" aria-hidden="true"></i><input type="text" class="form-control mntxt numeric" name="markup" id="eemail" placeholder="Markup" aria-required="true" required="required" ></div>
             <div class="col-md-3 col-xs-4 eml npadL prcnt">
            <select class="select2 form-control" data-rule-required="true" name="value_type" id="value_type" required="">
             <option value="plus">Plus</option>
             <option value="percentage">Percentage</option>
          </select></div>
         </div>-->
         
         <input type="hidden" name="tour_id" id="tour_id" value="<?php echo $tour_id; ?>">
		 <input type="hidden" name="list_type" value="<?php echo $list; ?>">
      </div>
      <div class="modal-footer">
        <label class="control-label col-sm-3" for="user_email"><strong class="text-danger"></strong></label>
        <div class="col-sm-3 col-md-8 controls eml">
      <button type="submit" class="btn btn-default send_enquiry_button" id="send_enquiry_button">Save</button>
		<?php 
			if($list=='draft'){
				echo '<a class="btn btn-default" href="'.base_url().$this->router->fetch_class().'/draft_list">Go Back to Draft List</a>';
			}else if($list=='holiday'){
				echo '<a class="btn btn-default" href="'.base_url().$this->router->fetch_class().'/tour_list">Go Back to Holiday List</a>';
			}else if($list=='verify'){
				echo '<a class="btn btn-default" href="'.base_url().$this->router->fetch_class().'/verify_tour_list">Go Back to Verify List</a>';
			}else if($list=='publish'){
				echo '<a class="btn btn-default" href="'.base_url().$this->router->fetch_class().'/published_tour_list">Go Back to Publish List</a>';
			}
	  
		?>
      
      </div>
    </div>

   </form>
</div>
</div>
<script type="text/javascript">
  $('.form_parametere_needs').change(function(){
    $('.load_check').html('Please wait');
    $('.load_check').removeClass('hide');
    // $('#load_check').removeClass('hide');
    $('.send_enquiry_button').attr('disabled', true);
    var date1 = $('#from_').val();
    var date2 = $('#to_').val();
   
    var occupencies = $("#occupancy").val();
    var tour_id = $('#tour_id').val();
  
    if(date1 != '' && date2 != '' && occupancy != ''){
      var myurl  = '<?=base_url()?>index.php/tours/check_price_avilability';
      $.ajax({
        url : myurl,
        type : "POST",
        dataType : "JSON",
        data : {from:date1, to:date2, occupency:occupencies,tour_id:tour_id},
        async : false,
        success : function(result){
          $('.load_check').addClass('hide');
          if(result.status){
            $('.send_enquiry_button').removeAttr('disabled');
            $(".error").hide();
          }else{
            $('.send_enquiry_button').attr('disabled', true);
             $(".modal-body").after("<span class = 'error'>The price is already added for same criteria. Please change your criteria</span>");
          }
        }
      });
    }

});
</script>

<div class="col-xs-12 prc_tbl">
 <div class="table-responsive">          
  <table class="table">
    <thead>
      <tr>
        <th>S.Nu.</th>
        <!--<th>From Date</th>
        <th>To Date</th> -->
		<th>Package Type</th>
		<th>Occupancy</th>
        <th>Currency</th>
		<th>Purchase Price</th>
        <th>Market Price</th>
        <th>Net Price</th>
        
      <!--   <th>Supplier Price ( <?php echo @$price_details[0]['currency']; ?>)</th> -->
        
       <!--  <th>Markup Value</th>
        <th>Markup Type</th>
        <th>Markup(NGN)</th>
        <th>Converted Price(NGN)<br>without Markup</th>
        <th>Final Price(NGN)</th>
         <th>Operation</th>-->
      </tr>
    </thead>
    <tbody>
    <?php
   $i = 1;
	//debug($price_details);exit;
     foreach ($price_details as  $price) {
      //debug($price);
   
      $currency_obj = new Currency(array('module_type' => 'holiday','from' => $price['currency'], 'to' => get_application_default_currency())); 
     $get_currency_symbol = ($this->session->userdata('currency') != '') ? $this->CI->session->userdata('currency') : 'CAD';
     $current_currency_symbol = $currency_obj->get_currency_symbol($get_currency_symbol);
     $converted_currency_rate = $currency_obj->getConversionRate(false);
    // $final_price = $converted_currency_rate*$price['sessional_price'];
     $final_price = $price['final_airliner_price'];
     if( $final_price==0){
      $final_price = $price['sessional_price'];
    //  echo  $final_price."----".$price['calculated_markup'];
     }
     $price_with_out_up = $converted_currency_rate*$price['sessional_price'];

      $query_x = "select * from occupancy_managment where id='".$price['occupancy']."'";
      $fetch_x = $this->db->query ( $query_x )->result_array ();   
      $fetch_x = $fetch_x[0];
      // debug($fetch_x);exit;   
      // $exe_x   = mysql_query($query_x);
      // $fetch_x = mysql_fetch_assoc($exe_x);
    ?>
    <tr>
        <td><?= $i;?></td>
        <td><?= $price['package_type']; ?></td>
        <td><?= $fetch_x['occupancy_name'];?></td>
        <td><?= $price['currency'];?></td>
		<td><?= $price['purchase_price'];?></td>
		<td><?= $price['market_price'];?></td>
        <td><?= $price['netprice_price'];?></td>
       <!--   <td><?= sprintf("%.2f", $price['sessional_price']);?></td>
       
       <td><?= $price['sessional_price']; ?></td> 
       
        <td><?= $price['markup'];?></td>
       
        <td><?= $price['value_type'];?></td>
           <td><?= sprintf("%.2f", $price['calculated_markup']);?></td>
           <td><?= sprintf("%.2f", $price_with_out_up);?></td> 
           <td><?= sprintf("%.2f", $final_price);?></td> 
           <?php  $final = $price['calculated_markup']+ $final_price; ?>
            <td><?= sprintf("%.2f", $final); ?></td>-->
        <td> <?php echo  '<a class="" data-placement="top" href="'.base_url().'index.php/tours/edit_price/'.$price['id'].'/'.$list.'"
              data-original-title="Edit Tour Destination"> <i class="glyphicon glyphicon-pencil" ></i> Edit
              </a> &nbsp; <br>'; ?> <?php echo  '<a class="" data-placement="top" href="'.base_url().'index.php/tours/delete_price/'.$price['id'].'/'.$tour_id.'/'.$price['package_type'].'/'.$list.'"
              data-original-title="Edit Tour Destination"> <i class="glyphicon glyphicon-trash" ></i> Delete
              </a> &nbsp; <br>'; ?></td>
  
      </tr>
    <?php
     $i++;
    }
    ?>
      
     
    </tbody>
  </table>
  </div>
</div>


 <script type="text/javascript">
 
$(document).ready(function(){
   $( function() {
      //$( "#hl_depdat" ).datepicker();
      //$( "#hl_depdat1" ).datepicker();
    });
	$(document).on('change','#package_type',function(){
		var sel_val=$(this).val();
	
		if(sel_val=='B2B')
		{
			$('.change_text').text('Net ');
		}else{
			$('.change_text').text('Discount ');
		}
	});
});
</script> 
<script type="text/javascript">
  $( function() {
    // futureDatepickerMonthDisabled("to_");
     // futureDatepickerMonthDisabled("from_");
     //$("#Pickup").change(function() {
      //manage date validation
     //  auto_set_dates($("#Pickup").datepicker('getDate'), "from_", 'minDate');
     //});
    //if second date is already set then dont run
    if ($("#from_").val() == '' ) {
       // auto_set_dates($("#Pickup").datepicker('getDate'), "from_", 'minDate');
    }
  } );

  $('#to__places').change(function() {
    var country = $(this).val();
    $.ajax({
      url:app_base_url + 'ajax/getcity_of_country',
      type:'post',
      data:{'country':country},
      success: function(response){
        $('#to__city').html(response);
      },error: function(){
        alert('City Not Available.');
      }
    });
    //alert(country);
  });
</script>
