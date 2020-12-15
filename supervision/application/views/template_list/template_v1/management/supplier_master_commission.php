<form name="group_generic" action="<?php echo base_url().'index.php/management/master_commission'; ?>" method="post">
   <div class="org_row">
      <div class="col-md-6">
         <label for="new_airline_value" class="col-sm-3 control-label">Generic<span class="text-danger">*</span></label>                                
         <div class="col-md-9">
            <div class="form-group mb20">
               <div class="col-md-6">
                  <input class="form-control" type="text" name="group_generic_value" value="<?php echo $generic['value']; ?>" placeholder="Commission">
               </div>
               <div class="col-md-6">
                  <input class="form-control" type="text" name="group_generic_tds" value="<?php echo $generic['tds']; ?>" placeholder="TDS" >
               </div>
               <input type="hidden" name="origin[]" value="<?php echo $generic['origin']; ?>">
               <input type="hidden" name="method" value="generic">
               <input type="hidden" name="group_generic_value_type" value="<?php echo $generic['value_type']; ?>">
               <input type="hidden" name="group_fk" value="<?php echo $group_fk; ?>">
               <input type="hidden" name="group_generic_booking_source" value="<?php echo $generic['booking_source']; ?>">
               <input type="hidden" name="group_generic_course_id" value="<?php echo $generic['course_id']; ?>">
            </div>
         </div>
      </div>
   </div>
   <div class="clearfix"></div>
   <div class="well well-sm">
      <div class="clearfix col-md-offset-1">                            
         <button class=" btn btn-sm btn-success " id="add-airline-submit-btn" type="submit">Add</button>                            
         <button class=" btn btn-sm btn-warning" type="reset">Reset</button>                        
      </div>
   </div>
</form>
<?php if($generic['course_id'] != 'TMCID1524458882' && $generic['course_id'] != 'TMVIATID1527240212'){ ?>
<div class="clearfix"></div>
<form name="group_generic" action="<?php echo base_url().'index.php/management/master_commission'; ?>" method="post">
   <fieldset>
   <legend><i class="fa fa-list-ul" aria-hidden="true"></i> Supplier List</legend>
   <?php foreach($specific as $s_key => $s_val) { ?>
   <div class="col-md-4">
      <label for="new_airline_value" class="col-sm-5 control-label"><?php echo ucfirst($s_val['name']); ?><span class="text-danger">*</span></label>                                
      <div class="col-md-7 mb10">
         <div class="col-md-6">     
            <input class="form-control" type="text" name="specific_value[]" value="<?php echo $s_val['value']; ?>" placeholder="Commission" >
         </div>
         <div class="col-md-6">
            <input class="form-control" type="text" name="specific_tds[]" value="<?php echo $s_val['tds']; ?>" placeholder="TDS" >
         </div>
         <input type="hidden" name="group_fk" value="<?php echo $group_fk; ?>">
         <input type="hidden" name="specific_value_type[]" value="<?php echo $s_val['value_type']; ?>">
         <input type="hidden" name="origin[]" value="<?php echo $s_val['origin']; ?>">
         <input type="hidden" name="specific_booking_source[]" value="<?php echo $s_val['booking_source']; ?>">
         <input type="hidden" name="specific_course_id[]" value="<?php echo $s_val['course_id']; ?>">
      </div>
   </div>
   <?php } ?>
   <div class="clearfix"></div>
   <div class="well well-sm mt10">
      <div class="clearfix col-md-offset-1">  
         <input type="hidden" name="method" value="specific">
         <input class="btn btn-sm btn-success" type="submit" name="submit">                             
         <button class=" btn btn-sm btn-warning " type="reset">Reset</button>                        
      </div>
   </div>
   <!--<div class="col-xs-12 text-center">
      <input type="hidden" name="method" value="specific">
      <input class="btn btn-default btn-sbmt" type="submit" name="submit">
      </div> -->
</form>
<?php } ?>
</div>
<div class="clearfix"></div>
<?php if($generic['course_id'] == 'VHCID1433498307'){ ?>
<div class="org_row">
   <div class="col-xs-12 spl_lst nopad">
      <div class="panel-body">
         <fieldset>
            <legend><i class="fa fa-bus"></i> Bitla Operator Wise Commission </legend>
            <form action="<?php echo base_url().'index.php/management/add_master_operator_wise_commission' ?>"  method="POST" autocomplete="off">
               <?php foreach ($bitla_operators as $bitla_key => $bitla_value) { ?>
                  <div class="row mrk_lst">
                     <div class="col-md-4">
                        <label for="new_airline_value" class="col-sm-12 control-label"><?=$bitla_value['operator_details']['name']?></label>
                        <input type="hidden" name="booking_source[]" value="<?=$bitla_value['operator_details']['origin']?>">                               
                        <!-- <div class="col-md-7 mb10"> 
                           <input type="text" required="" readonly="" value="<?=$bitla_value['operator_details']['name']?>" name="operator[]" class="form-control"> 
                        </div> -->
                     </div>
                     <div class="col-md-4">
                        <div class="col-md-12 mb12"> 
                           <input type="text" placeholder="Commission value" name="commission_value[]" class="form-control" value="<?=$bitla_value['commission_details'][0]['value']?>"> 
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="col-md-12 mb12"> 
                           <input type="text" placeholder="TDS" name="tds[]" class="form-control" value="<?=$bitla_value['commission_details'][0]['tds']?>"> 
                        </div>
                     </div>
                  </div>   
               <?php } ?>
               <div class="hide">
                  <input type="hidden" name="group_fk" value="<?php echo $group_fk; ?>">
                  <input type="hidden" class="" name="add" value="add">
               </div>
               <!-- <div class="row mrk_lst">
                  <div class="col-md-6">
                     <label for="new_airline_value" class="col-sm-5 control-label">Enter Operator<span class="text-danger">*</span></label>                                
                     <div class="col-md-7 mb10"> 
                        <input type="text" required="" name="operator" class="form-control"> 
                     </div>
                  </div>
                  <div class="col-md-6">
                     <label for="new_airline_value" class="col-sm-5 control-label">Commission value<span class="text-danger">*</span></label>                                
                     <div class="col-md-7 mb10"> 
                        <input type="text" required="" name="commission_value" class="form-control" value=""> 
                     </div>
                  </div>
               </div> -->
               <div class="well well-sm mt10">
                  <div class="clearfix col-md-offset-1">                            
                     <button class=" btn btn-sm btn-success " id="general-markup-submit-btn" type="submit">Save</button>
                     <button class=" btn btn-sm btn-warning " type="reset">Reset</button>
                  </div>
               </div>
            </form>
         </fieldset>
         <?php if(false){ ?>
         <fieldset>
            <legend><i class="fa fa-bus"></i> Operator Commission List</legend>
            <form action="<?php echo base_url().'index.php/management/add_master_operator_wise_commission' ?>" method="POST" autocomplete="off">
               <input type="hidden" class="" name="update" value="update">
               <input type="hidden" name="group_fk" value="<?php echo $group_fk; ?>">
               <?php 
                  foreach($comm_operator_list as $key => $value){
                  ?>
               <input type="hidden" name="origin[]" value="<?=$value['origin']?>">
               <div class="row">
                  <div class="col-md-6">
                     <label for="new_airline_value" class="col-sm-5 control-label">Operator name<span class="text-danger">*</span></label>                                
                     <div class="col-md-7 mb10">
                        <div class="radio mar0">
                           <input type="text" class="form-control" name="operator_name[]" value="<?=$value['operator_name']?>">
                        </div>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <label for="new_airline_value" class="col-sm-5 control-label">Value<span class="text-danger">*</span></label>                                
                     <div class="col-md-7 mb10"> 
                        <input class="form-control" type="text" id="" name="value[]" class=" specific-value numeric" placeholder="Commission Value" value="<?=$value['value']?>">
                     </div>
                  </div>
               </div>
               <?php } ?>     
               <div class="well well-sm mt10">
                  <div class="clearfix col-md-offset-1">                                
                     <button class=" btn btn-sm btn-success " type="submit">Update</button>     
                     <button class=" btn btn-sm btn-warning " type="reset">Reset</button>
                  </div>
               </div>
            </form>
         </fieldset>
      <?php } ?>
      </div>
   </div>
</div>
<?php } ?>
<?php if($generic['course_id'] == 'VHCID1420613784'){ ?>
<fieldset>
   <legend><i class="fa fa-list-ul" aria-hidden="true"></i> Travelport Commission</legend>
   <div class="col-md-6">
      <label for="new_airline_value" class="col-sm-3 control-label">Supplier<span class="text-danger">*</span></label>                                
      <div class="col-md-9">
         <div class="form-group">
         <?php 
               if(@$supplier == TRAVELPORT_ACH_BOOKING_SOURCE){ 
                  $ach  = 'selected="selected"';
                  $gds = '';
               }else{
                  $ach = '';
                  $gds = 'selected="selected"';
               }
         ?>
            <select name="tp_supplier" id="tp_supplier" required="" class="tp_supplier form-control mb10 form_group">
               <option value=""> -- Select Travelport Supplier -- </option>
               <option value="<?php echo TRAVELPORT_GDS_BOOKING_SOURCE; ?>" <?=$gds?> >GDS</option>
               <option value="<?php echo TRAVELPORT_ACH_BOOKING_SOURCE; ?>" <?=$ach?> >ACH</option>
            </select>
         </div>
      </div>
   </div>
   <div class="col-md-5">
      <label for="new_airline_value" class="col-sm-3 control-label">Trip Type<span class="text-danger">*</span></label>                                
      <div class="col-md-9 mb10 nopad">
         <div class="radio mtb5">
            <?php 
               if(@$trip_type == 'international'){ 
                  $international  = 'checked="checked"';
                  $domestic = '';
               }else{
                  $international = '';
                  $domestic = 'checked="checked"';
               }
               ?>
            <label for="value_type" class="col-sm-4 control-label"></label>
            <label for="value_type_plus" class="radio-inline">
            <input <?=$domestic?> type="radio" value="domestic" id="trip_type_dom" name="trip_type" class="trip_type radioIp" required=""> Domestic
            </label>                                
            <label for="value_type_percent" class="radio-inline">
            <input type="radio" <?=$international?> value="international" id="trip_type_inter" name="trip_type" class="trip_type radioIp" required=""> International
            </label>                            
         </div>
      </div>
   </div>
</fieldset>
<div class="clearfix"></div>
<!-- PANEL BODY START /General Markup Starts-->            
<fieldset>
   <legend><i class="fa fa-plane"></i> Add Airline</legend>
   <form action="<?php echo base_url().'index.php/management/add_master_wise_commission' ?>" class="form-horizontal" method="POST" autocomplete="off">
      <div class="hide">
         <input type="hidden" class="selected_trip_type" name="selected_trip_type" value="<?php if(!empty(@$trip_type)){ echo $trip_type; }else{ echo 'domestic'; } ?>">
         <input type="hidden" class="selected_supplier" name="selected_supplier" value="<?php if(!empty(@$supplier)){ echo $supplier; }else{ echo TRAVELPORT_GDS_BOOKING_SOURCE; } ?>">
         <input type="hidden" name="group_fk" value="<?php echo $group_fk; ?>">
         <input type="hidden" class="" name="add" value="add">
      </div>
      <div class="row mrk_lst">
         <div class="col-md-2">
            <div class="form-group">
               <label for="sel1">Select Airline:</label>
               <select name="airline_origin" class="form-control airline_list" id="airline_list">
                  <option value=""> -- Select Airline -- </option>
                  <?php foreach($airline_list as $airline_key => $airline_value){ ?>
                  <option value="<?=$airline_value['origin']?>"><?=$airline_value['name'].' - '.$airline_value['code'] ?></option>
                  <?php } ?>                  
               </select>
            </div>
         </div>
         <div class="col-md-1">
            <div class="form-group">
               <label for="sel1">Class</label>
               <input type="text" name="class" class="form-control" value=""> 
            </div>
         </div>
         <div class="col-md-2">
            <div class="form-group">
               <label for="sel1">Basic Commission</label>
               <input type="text" name="basic" class="form-control" value=""> 
            </div>
         </div>
         <div class="col-md-2">
            <div class="form-group">
               <label for="sel1">PLB Commission</label>
               <input type="text" name="plb" class="form-control" value=""> 
            </div>
         </div>
         <div class="col-md-2">
            <div class="form-group">
               <label for="sel1">YQ Commission</label>
               <input type="text" name="yq" class="form-control" value=""> 
            </div>
         </div>
         <div class="col-md-1">
            <div class="form-group">
               <label for="sel1">IATA Comm</label>
               <input type="text" name="iata" class="form-control" readonly="" value="0"> 
            </div>
         </div>
         <div class="col-md-2">
            <div class="form-group">
               <label for="sel1">TDS</label>
               <input type="text" name="tds" class="form-control" value=""> 
            </div>
         </div>
         <!-- <div class="col-md-4">
            <div class="form-group">
            <label for="sel1">Markup value</label>
            <input type="text" name="markup_value" class="form-control" value=""> 
            </div>
            </div> -->
      </div>
      <div class="well well-sm">
         <div class="clearfix col-md-offset-1">                            
            <button class=" btn btn-sm btn-success " id="general-markup-submit-btn" type="submit">Save</button>
            <button class=" btn btn-sm btn-warning " type="reset">Reset</button>
         </div>
      </div>
   </form>
</fieldset>
<fieldset>
   <legend><i class="fa fa-plane"></i> Airline Commission List</legend>
   <form action="<?php echo base_url().'index.php/management/add_master_wise_commission' ?>" method="POST" autocomplete="off">
      <input type="hidden" class="" name="update" value="update">
      <input type="hidden" name="selected_trip_type" class="selected_trip_type_specific" value="<?php if(!empty(@$trip_type)){ echo $trip_type; }else{ echo 'domestic'; } ?>">
      <input type="hidden" name="selected_supplier" class="selected_supplier_specific" value="<?php if(!empty(@$supplier)){ echo $supplier; }else{ echo TRAVELPORT_GDS_BOOKING_SOURCE; } ?>">
      <input type="hidden" name="group_fk" value="<?php echo $group_fk; ?>">
      <div class="row mrk_lst">
         <div class="col-md-2">
            <div class="form-group">
               <label for="sel1">Select Airline:</label>
            </div>
         </div>
         <div class="col-md-1">
            <div class="form-group">
               <label for="sel1">Class</label>
            </div>
         </div>
         <div class="col-md-2">
            <div class="form-group">
               <label for="sel1">Basic Commission</label>
            </div>
         </div>
         <div class="col-md-2">
            <div class="form-group">
               <label for="sel1">PLB Commission</label>
            </div>
         </div>
         <div class="col-md-2">
            <div class="form-group">
               <label for="sel1">YQ Commission</label> 
            </div>
         </div>
         <div class="col-md-1">
            <div class="form-group">
               <label for="sel1">IATA Comm</label>
            </div>
         </div>
         <div class="col-md-2">
            <div class="form-group">
               <label for="sel1">TDS</label>
            </div>
         </div>
      </div>
      <?php 
         foreach($comm_airline_list as $key => $value){
         ?>
      <input type="hidden" name="origin[]" value="<?=$value['origin']?>">
      <div class="row">
         <div class="col-md-2">
            <div class="air_img"><img src="<?=SYSTEM_IMAGE_DIR?>airline_logo/<?=$value['code']?>.gif" alt="<?=$value['name']?>"> <?=$value['name']?></div>
         </div>
         <div class="col-md-1">
            <div class="radio mar0">
               <input class="form-control" type="text" name="airline_class[]" value="<?=$value['airline_class']?>">
            </div>
         </div>
         <div class="col-md-2">
            <div class="form-group">
               <input class="form-control" type="text" id="" name="value[]" class=" specific-value numeric" placeholder="Commission Value" value="<?=$value['value']?>">
            </div>
         </div>
         <div class="col-md-2">
            <div class="form-group">
               <input class="form-control" type="text" id="" name="plb[]" class=" specific-value numeric" placeholder="PLB Value" value="<?=$value['plb']?>">
            </div>
         </div>
         <div class="col-md-2">
            <div class="form-group">
               <input class="form-control" type="text" id="" name="yq[]" class=" specific-value numeric" placeholder="YQ Value" value="<?=$value['yq']?>">
            </div>
         </div>
         <div class="col-md-1">
            <div class="form-group">
               <input class="form-control" readonly="" type="text" id="" name="iata[]" class=" specific-value numeric" placeholder="IATA Value" value="<?=$value['iata']?>">
            </div>
         </div>
         <div class="col-md-2">
            <div class="form-group">
               <input class="form-control" type="text" id="" name="tds[]" class=" specific-value numeric" placeholder="TDS Value" value="<?=$value['tds']?>">
            </div>
         </div>
      </div>
      <?php } ?>     
      <div class="well well-sm">
         <div class="clearfix col-md-offset-1">                                
            <button class=" btn btn-sm btn-success " type="submit">Update</button>     
            <button class=" btn btn-sm btn-warning " type="reset">Reset</button>
         </div>
      </div>
   </form>
</fieldset>
</div>
</div>
</div>
<?php } ?>
<script type="text/javascript">
   $(document).ready(function(){
      $('#tp_supplier').select2();
      $('.airline_list').select2();
      $('.trip_type').click(function(){
         var tirp_type = $("input[name='trip_type']:checked").val();
         $('.selected_trip_type').val(tirp_type);
         $('.selected_trip_type_specific').val(tirp_type);
      });
      $('#tp_supplier').change(function(){
         var sup_val = $('#tp_supplier').val();
         $('.selected_supplier').val(sup_val);
         $('.selected_supplier_specific').val(sup_val);
      });
   })
</script>