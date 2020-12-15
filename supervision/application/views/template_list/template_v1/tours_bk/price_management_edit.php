<style>
  .price_mgnt .control-label.col-sm-3{text-align: right;}
  .price_mgnt .modal-footer {padding: 15px;text-align: left;float: left;width: 100%;}
  .price_mgnt .form-group {margin-bottom: 15px;float: left;width: 100%;}
  .price_mgnt .fa {position: absolute;line-height: 33px;padding: 0 10px;}
  .price_mgnt .form-control{padding: 6px 25px;}
</style>
<div class="price_mgnt">
<div class="pk_form col-xs-12" id="myModal">
   <form id="enquiry_form" method="post" action="<?php echo base_url() ?>index.php/tours/save_edit_price_management">
      <div class="modal-body">
         <h4 class="text-center hldy_tit">Price Management</h4>
			<div class="form-group modl">
				<label class="control-label col-sm-3" for="user_email">Package Type  :<strong class="text-danger"></strong></label>
				<div class="col-sm-3 col-md-8 controls eml"><i class="fa fa-user" aria-hidden="true"></i>
				<select class='select2 form-control form_parametere_needs' data-rule-required='true' name='package_type' id="package_type" data-rule-required='true' required readonly>
					<option value="">Choose Type</option>
					<option value="B2B" <?php if($price_details_single[0]['package_type']=='B2B') echo "selected"; ?>>B2B</option>
					<option value="B2C" <?php if($price_details_single[0]['package_type']=='B2C') echo "selected"; ?>>B2C</option>
				</select>
				</div>
			 </div>
          <div class="form-group modl">
            <label class="control-label col-sm-3" for="user_email">Occupancy  :<strong class="text-danger"></strong></label>
            <div class="col-sm-3 col-md-8 controls eml"><i class="fa fa-user" aria-hidden="true"></i>
            <select class='select2 form-control' data-rule-required='true' name='occupancy' id="tours_continent" data-rule-required='true' required disabled>
                                <option value="">Choose Occupancy</option>
								
                                <?php
                                foreach($occupancy_details as $occupancy_details_key => $occupancy_details_value)
                                {
                                    $class = '';
                                    if($occupancy_details_value['id'] == $price_details_single[0]['occupancy'])
                                   {
                                       $class = "selected";
                                   }
                                    
                                 echo '<option value="'.$occupancy_details_value['id'].'" '.$class.'>'.$occupancy_details_value['occupancy_name'].' </option>';
                                }
                                ?>
                </select> </div>
         </div>
		 <div class="form-group modl">
            <label class="control-label col-sm-3" for="user_email">Currency  :<strong class="text-danger"></strong></label>
            <div class="col-sm-3 col-md-8 controls eml"><i class="fa fa-money" aria-hidden="true"></i>
            <select class='select2 form-control' data-rule-required='true' name='currency' id="currency" data-rule-required='true' required >
                                <option value="">Choose Currency</option>
                                <?php

                                foreach($currency as $currency_key => $currency_value)
                                {

                                    if(strtolower($currency_value['country']) ==  @strtolower($price_details_single[0]['currency']))
                                    {
                                        echo '<option value="'.$currency_value['country'].'" selected>'.$currency_value['country'].' </option>';
                                    }else{
                                        echo '<option value="'.$currency_value['country'].'">'.$currency_value['country'].' </option>';
                                    }
                                  
                                }
                                ?>
                </select> 

         <!--  <input type="text" value="<?= @$price_details_single[0]['currency'];?>" class="form-control mntxt" name="currency" id="eemail" placeholder="Price" aria-required="true" required="required"  readonly="readonly"> --> </div> 
         </div>
		 <div class="form-group modl">
            <label class="control-label col-sm-3" for="user_email">Purchase Price :<strong class="text-danger"></strong></label>
            <div class="col-sm-3 col-md-8 controls eml"><i class="fa fa-money" aria-hidden="true"></i><input type="text" class="form-control mntxt numeric" name="purchase_price" id="eemail" placeholder="Price" value="<?=$price_details_single[0]['purchase_price']?>"  aria-required="true" required="required"></div>
        </div>
		<div class="form-group modl">
            <label class="control-label col-sm-3" for="user_email">Market Price :<strong class="text-danger"></strong></label>
            <div class="col-sm-3 col-md-8 controls eml"><i class="fa fa-money" aria-hidden="true"></i><input type="text" class="form-control mntxt numeric" name="market_price" id="eemail" placeholder="Price" value="<?=$price_details_single[0]['market_price']?>" aria-required="true" required="required"></div>
         </div>
		 <div class="form-group modl">
            <label class="control-label col-sm-3" for="user_email"><span class="change_text"><?php if($price_details_single[0]['package_type']=='B2B'){ echo "Net ";}else{ echo "Discount ";} ?></span> Amount :<strong class="text-danger"></strong></label>
            <div class="col-sm-3 col-md-8 controls eml"><i class="fa fa-money" aria-hidden="true"></i><input type="text" class="form-control mntxt numeric" name="netprice_price" id="eemail" placeholder="Price" value="<?=$price_details_single[0]['netprice_price']?>"  aria-required="true" required="required"></div>
         </div>
		 <?php 
			if($price_details_single[0]['package_type']=='B2B'){
				$adv_pay = $adv_pay['b2b_adv_pay'];
			}else{
				$adv_pay = $adv_pay['b2c_adv_pay'];
			}
		 ?>
		 <div class="form-group modl">
            <label class="control-label col-sm-3" for="user_email">Advance Payment (%)<strong class="text-danger"></strong></label>
            <div class="col-sm-3 col-md-8 controls eml"><i class="fa fa-money" aria-hidden="true"></i><input type="text" class="form-control mntxt numeric" name="advance_pay" id="advance_pay" placeholder="Advance pay in percentage" value="<?=$adv_pay?>" aria-required="true"></div>
		 </div>
         <!--<div class="form-group modl">
            <label class="control-label col-md-5 col-xs-4" for="user_name">From Date  :<strong class="text-danger"></strong></label>
           <div class="col-md-7 col-xs-8 eml n_psngr"><i class="fa fa-calendar" aria-hidden="true"></i>  <input type="text" value="<?php echo @$price_details_single[0]['from_date'];?>" name="from_date" class="form-control mntxt" id="hl_depdat" placeholder="From Date" aria-required="true" required="required" >    
            </div>
         </div>
         <div class="form-group modl">
            <label class="control-label col-md-5 col-xs-4" for="user_name">To Date  :<strong class="text-danger"></strong></label>
            <div class="col-md-7 col-xs-8 eml n_psngr"><i class="fa fa-calendar" aria-hidden="true"></i> <input type="text" value="<?= @$price_details_single[0]['to_date'];?>" name="to_date" class="form-control mntxt" id="hl_depdat1" placeholder="To Date" aria-required="true" required="required"  >    
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
         
           
        <!-- <div class="form-group modl">
            <label class="control-label col-md-5 col-xs-4" for="user_email">Price  :<strong class="text-danger"></strong></label>
            <div class="col-md-7 col-xs-8 eml"><i class="fa fa-money" aria-hidden="true"></i><input type="text" value="<?= @$price_details_single[0]['sessional_price'];?>" class="form-control mntxt" name="airliner_price" id="eemail" placeholder="Price" aria-required="true" required="required"></div>
         </div>-->

     <!--  <div class="form-group modl">
            <label class="control-label col-md-5 col-xs-4" for="user_email">Markup(CAD)  :<strong class="text-danger"></strong></label>
            <div class="col-md-4 col-xs-4 eml npadR"><i class="fa fa-line-chart" aria-hidden="true"></i><input type="text" class="form-control mntxt" name="markup" id="eemail" value="<?= @$price_details_single[0]['markup'];?>" placeholder="Markup" aria-required="true" required="required"></div>
             <div class="col-md-3 col-xs-4 eml npadL prcnt">
            <select class="select2 form-control" data-rule-required="true" name="value_type" id="value_type" required="">
             <option value="plus" <?= ($price_details_single[0]['value_type']=='plus')? 'selected="selected"': '';?>>Plus</option>
             <option value="percentage" <?= ($price_details_single[0]['value_type']=='percentage')? 'selected="selected"': '';?>>Percentage</option>
          </select></div>
         </div>
         <input type="hidden" name="sessional_price" value="<?= @$price_details_single[0]['sessional_price'];?>">
--> 
         <input type="hidden" name="tour_id" value="<?php echo $price_details_single[0]['tour_id']; ?>">
         <input type="hidden" name="id" value="<?php echo $price_details_single[0]['id']; ?>">

          <!-- <div class="form-group modl">
            <label class="control-label col-md-5 col-xs-4" for="user_email">Price for sessional :<strong class="text-danger"></strong></label>
            <div class="col-md-7 col-xs-8 eml"><i class="fa fa-inr" aria-hidden="true"></i><input type="text" class="form-control mntxt" name="sessional_price" id="eemail" placeholder="Price" aria-required="true" required="required"></div>
         </div> -->
        <!--   <div class="form-group modl">
            <label class="control-label col-md-5 col-xs-4" for="user_email">Markup  :<strong class="text-danger"></strong></label>
            <div class="col-md-7 col-xs-8 eml"><i class="fa fa-line-chart" aria-hidden="true"></i><input type="text" value="<?= @$price_details_single[0]['markup'];?>" class="form-control mntxt" name="markup" id="eemail" placeholder="Markup" aria-required="true" required="required"></div>
         </div> -->
      
      </div>
      <div class="modal-footer">
        <label class="control-label col-sm-3" for="user_email"><strong class="text-danger"></strong></label>
        <div class="col-sm-3 col-md-8 controls eml">
        <input type="hidden" id="tour_id" value=""><input type="hidden" id="tours_itinerary_id" value="">

      <button type="submit" class="btn btn-default" id="send_enquiry_button">Update</button>
      <a class='btn btn-primary' href="<?php echo base_url() ."index.php/tours/price_management/".$price_details_single[0]['tour_id']; ?>">Cancel</a>
  </div>
      </div>
   </form>
</div>
</div>
<script type="text/javascript">
 
$(document).ready(function(){
   
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