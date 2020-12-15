<div class="price_mgnt">
<div class="pk_form col-xs-12" id="myModal">
<!--    <form id="enquiry_form" method="post" action="<?php echo base_url() ?>index.php/tours/save_price_management">
      <div class="modal-body">
         <h4 class="text-center hldy_tit">Price Management</h4>
         <div class="form-group modl">
            <label class="control-label col-md-5 col-xs-4" for="user_name">From Date  :<strong class="text-danger"></strong></label>
           <div class="col-md-7 col-xs-8 eml n_psngr"><i class="fa fa-calendar" aria-hidden="true"></i>  <input type="text" name="from_date" class="form-control mntxt" id="hl_depdat" placeholder="From Date" aria-required="true" required="required">    
            </div>
         </div>
         <div class="form-group modl">
            <label class="control-label col-md-5 col-xs-4" for="user_name">To Date  :<strong class="text-danger"></strong></label>
            <div class="col-md-7 col-xs-8 eml n_psngr"><i class="fa fa-calendar" aria-hidden="true"></i> <input type="text" name="to_date" class="form-control mntxt" id="hl_depdat1" placeholder="To Date" aria-required="true" required="required">    
            </div>
         </div>
         <!--  <div class="form-group modl">
            <label class="control-label col-md-5 col-xs-4" for="user_name">Depature Date  :<strong class="text-danger"></strong></label>
            <div class="col-md-7 col-xs-8 eml n_psngr"><i class="fa fa-calendar" aria-hidden="true"></i> <input type="text" name="depature_date" class="form-control mntxt" id="hl_depdat11" placeholder="To Date" aria-required="true" required="required">    
            </div>
         </div> -->
        <!--  <div class="form-group modl">
            <label class="control-label col-md-5 col-xs-4" for="user_email">Currency  :<strong class="text-danger"></strong></label>          
            <div class="col-md-7 col-xs-8 eml n_psngr"><i class="fa fa-inr" aria-hidden="true"></i><input type="text" class="form-control mntxt" placeholder="Currency" aria-required="true" required="required">    
            </div>
         </div> --
          <div class="form-group modl">
            <label class="control-label col-md-5 col-xs-4" for="user_email">Occupancy  :<strong class="text-danger"></strong></label>
            <div class="col-md-7 col-xs-8 eml"><i class="fa fa-user" aria-hidden="true"></i>
            <select class='select2 form-control' data-rule-required='true' name='occupancy' id="tours_continent" data-rule-required='true' required>
                                <option value="">Choose Occupancy</option>
                                <?php
                                foreach($occupancy_details as $occupancy_details_key => $occupancy_details_value)
                                {
                                  echo '<option value="'.$occupancy_details_value['id'].'">'.$occupancy_details_value['occupancy_name'].' </option>';
                                }
                                ?>
                </select> </div>
         </div>
         <div class="form-group modl">
            <label class="control-label col-md-5 col-xs-4" for="user_email">Price  :<strong class="text-danger"></strong></label>
            <div class="col-md-7 col-xs-8 eml"><i class="fa fa-inr" aria-hidden="true"></i><input type="text" class="form-control mntxt" name="sessional_price" id="sessional_price" placeholder="Price" aria-required="true" required="required"></div>
         </div>

          <!-- <div class="form-group modl">
            <label class="control-label col-md-5 col-xs-4" for="user_email">Price for sessional :<strong class="text-danger"></strong></label>
            <div class="col-md-7 col-xs-8 eml"><i class="fa fa-inr" aria-hidden="true"></i><input type="text" class="form-control mntxt" name="sessional_price" id="eemail" placeholder="Price" aria-required="true" required="required"></div>
         </div> --
          <div class="form-group modl">
            <label class="control-label col-md-5 col-xs-4" for="user_email">Markup  :<strong class="text-danger"></strong></label>
            <div class="col-md-7 col-xs-8 eml"><i class="fa fa-line-chart" aria-hidden="true"></i><input type="text" class="form-control mntxt" name="markup" id="eemail" placeholder="Markup" aria-required="true" required="required"></div>
         </div>
         <input type="hidden" name="tour_id" value="<?php echo $tour_id; ?>">
      </div>
      <div class="modal-footer"><input type="hidden" id="tour_id" value=""><input type="hidden" id="tours_itinerary_id" value=""><button type="submit" class="btn btn-default" id="send_enquiry_button">Save</button></div>
   </form> -->
</div>
</div>

<div class="col-xs-12 prc_tbl">
 <div class="table-responsive">          
  <table class="table">
    <thead>
      <tr>
        <th>S.Nu.</th>
        <th>From Date</th>
        <th>To Date</th>
        <th>Occupancy</th>
        <th>Supplier Price ( <?php echo @$price_details[0]['currency']; ?>)</th>
        <th>Airliners Price (CAD)</th>
         <th>Markup Value</th>
        <th>Markup Type</th>
        <th>Calculated Markup</th>
        <th>Final Price</th>
          <th>Edit</th>
      </tr>
    </thead>
    <tbody>
    <?php

     
//debug($price_details); exit();
      $i = 1;

     foreach ($price_details as  $price) {
    

     $currency_obj = new Currency(array('module_type' => 'holiday','from' => $price['currency'], 'to' => 'CAD')); 
   // debug($currency_obj); exit();
     $get_currency_symbol = ($this->session->userdata('currency') != '') ? $this->CI->session->userdata('currency') : 'CAD';
     $current_currency_symbol = $currency_obj->get_currency_symbol($get_currency_symbol);
     $converted_currency_rate = $currency_obj->getConversionRate(false);
     //echo $converted_currency_rate; exit();
     $final_price = $converted_currency_rate*$price['sessional_price'];

      if($price['value_type'] == 'percentage')
      {
         $markup = ($price['airliner_price'])/$price['markup'];
      }
      else
      {
        $markup = $price['markup'];
      }


    ?>
    <tr>
        <td><?= $i;?></td>
        <td><?= $price['from_date']; ?></td>
        <td><?= $price['to_date'];?></td>
        <td><?= $price['occupancy'];?></td>
        <td><?= $price['sessional_price']; ?></td>
        <td><?= $final_price; ?></td>
          <td><?= $markup;?></td>
       
        <td><?= $price['value_type'];?></td>
           <td><?= $price['markup'];?></td>
           <td><?= $price['final_airliner_price'];?></td>
      
       
        <td> <?php echo  '<a class="" data-placement="top" href="'.base_url().'index.php/tours/edit_price/'.$price['id'].'"
              data-original-title="Edit Tour Destination"> <i class="glyphicon glyphicon-pencil" ></i> Edit
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
    //  $( "#hl_depdat" ).datepicker();
        $( "#hl_depdat11" ).datepicker();
    });
});
</script> 
